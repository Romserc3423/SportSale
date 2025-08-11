<?php
include 'conexion.php';

$hoy = date('Y-m-d');

// Ventas del día (suma total de todas las ventas de hoy)
$sqlVentas = "SELECT SUM(total) AS ventas FROM venta WHERE DATE(fecha_venta) = ?";
$stmtVentas = $conn->prepare($sqlVentas);
$stmtVentas->bind_param("s", $hoy);
$stmtVentas->execute();
$resVentas = $stmtVentas->get_result()->fetch_assoc();
$ventas = $resVentas['ventas'] ?? 0;

// Productos vendidos (suma de cantidades desde detalle_venta JOIN con venta)
$sqlProductos = "
    SELECT SUM(dv.cantidad) AS productos
    FROM detalle_venta dv
    INNER JOIN venta v ON dv.id_venta = v.id_venta
    WHERE DATE(v.fecha_venta) = ?";
$stmtProductos = $conn->prepare($sqlProductos);
$stmtProductos->bind_param("s", $hoy);
$stmtProductos->execute();
$resProductos = $stmtProductos->get_result()->fetch_assoc();
$productos = $resProductos['productos'] ?? 0;

// Transacciones del día (número de ventas realizadas hoy)
$sqlTransacciones = "SELECT COUNT(*) AS transacciones FROM venta WHERE DATE(fecha_venta) = ?";
$stmtTransacciones = $conn->prepare($sqlTransacciones);
$stmtTransacciones->bind_param("s", $hoy);
$stmtTransacciones->execute();
$resTransacciones = $stmtTransacciones->get_result()->fetch_assoc();
$transacciones = $resTransacciones['transacciones'] ?? 0;

echo json_encode([
    "ventas" => number_format($ventas, 2),
    "productos" => (int)$productos,
    "transacciones" => (int)$transacciones
]);
?>
