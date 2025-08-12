<?php
// reporte_api.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Mexico_City'); 
header('Content-Type: application/json');

require_once 'conexion.php'; 

try {
    // Leer los datos directamente de $_POST
    $request_data = $_POST;

    $comentario_form = $request_data['comentarios'] ?? '';

    $fecha_inicio = date('Y-m-d 00:00:00');
    $fecha_fin = date('Y-m-d 23:59:59');

    $reporte_data = [];

    $sql_ventas_resumen = "
        SELECT
            COALESCE(SUM(v.total), 0) AS total_ventas,
            COALESCE(COUNT(DISTINCT v.id_venta), 0) AS num_transacciones,
            COALESCE(SUM(dv.cantidad), 0) AS articulos_vendidos
        FROM venta v
        LEFT JOIN detalle_venta dv ON v.id_venta = dv.id_venta
        WHERE v.fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
    ";
    $stmt = $pdo->prepare($sql_ventas_resumen);
    $stmt->execute([':fecha_inicio' => $fecha_inicio, ':fecha_fin' => $fecha_fin]);
    $resumen = $stmt->fetch(PDO::FETCH_ASSOC);

    $reporte_data['total_ventas'] = (float) $resumen['total_ventas'];
    $reporte_data['num_transacciones'] = (int) $resumen['num_transacciones'];
    $reporte_data['articulos_vendidos'] = (int) $resumen['articulos_vendidos'];

    $sql_metodos_pago = "
        SELECT
            metodo_pago,
            COALESCE(SUM(total), 0) AS monto
        FROM venta
        WHERE fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
        GROUP BY metodo_pago
    ";
    $stmt = $pdo->prepare($sql_metodos_pago);
    $stmt->execute([':fecha_inicio' => $fecha_inicio, ':fecha_fin' => $fecha_fin]);
    $metodos_pago = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $reporte_data['metodos_pago'] = $metodos_pago;

    $sql_asesor = "
        SELECT
            e.nombre AS nombre_empleado,
            COALESCE(SUM(v.total), 0) AS monto_total
        FROM venta v
        JOIN empleado e ON v.id_empleado = e.id_empleado
        WHERE v.fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
        GROUP BY e.id_empleado
        ORDER BY monto_total DESC
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql_asesor);
    $stmt->execute([':fecha_inicio' => $fecha_inicio, ':fecha_fin' => $fecha_fin]);
    $asesor = $stmt->fetch(PDO::FETCH_ASSOC);

    $reporte_data['asesor_mas_ventas'] = $asesor['nombre_empleado'] ?? 'N/A';
    $reporte_data['monto_asesor_ventas'] = (float) ($asesor['monto_total'] ?? 0);

    $sql_productos_vendidos = "
        SELECT
            p.nombre AS nombre_producto
        FROM detalle_venta dv
        JOIN producto p ON dv.id_producto = p.id_producto
        JOIN venta v ON dv.id_venta = v.id_venta
        WHERE v.fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
        GROUP BY p.id_producto
        ORDER BY SUM(dv.cantidad) DESC
        LIMIT 3
    ";
    $stmt = $pdo->prepare($sql_productos_vendidos);
    $stmt->execute([':fecha_inicio' => $fecha_inicio, ':fecha_fin' => $fecha_fin]);
    $productos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $reporte_data['productos_mas_vendidos'] = $productos;

    $reporte_data['fecha'] = date('j \d\e F \d\e Y');
    $reporte_data['hora'] = date('H:i A T');
    $reporte_data['stock_bajo_advertencias'] = '5 ítems (Balón de Baloncesto T-7, Guantes de Ciclismo M, ... )';
    $reporte_data['devoluciones_cambios'] = $request_data['devoluciones'] ?? '0';
    $reporte_data['num_visitas'] = (int) ($request_data['visitas'] ?? 0);
    $reporte_data['comentarios'] = $comentario_form;

    echo json_encode($reporte_data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en la API.',
        'message' => $e->getMessage()
    ]);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error de base de datos.',
        'message' => $e->getMessage()
    ]);
    exit;
}
?>