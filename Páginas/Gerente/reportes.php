<?php
// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Establece la zona horaria para evitar desfases de fecha
date_default_timezone_set('America/Mexico_City'); 

require_once 'config.php';

$reporte_data = [
    'total_ventas' => 0,
    'num_transacciones' => 0,
    'articulos_vendidos' => 0,
    'metodos_pago' => [],
];
$error_conexion = '';

try {
    // --- 1. DEFINIR EL RANGO DE FECHAS (SIEMPRE LA FECHA ACTUAL) ---
    $fecha_seleccionada = date('Y-m-d');
    $fecha_inicio = $fecha_seleccionada . ' 00:00:00';
    $fecha_fin = $fecha_seleccionada . ' 23:59:59';

    // --- 2. CONSULTAS SQL PARA OBTENER LOS DATOS DEL REPORTE ---

    // Resumen de ventas, transacciones y artículos
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

    // Desglose de ventas por método de pago
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
    $reporte_data['metodos_pago'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_conexion = 'Error al cargar los datos: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Reportes Diarios - SPORT_SALE</title>
    <link rel="stylesheet" href="reporte.css">
</head>
<body>
    <header>
        <a href="panel.php" class="btn-volver">Volver al Panel de Gerente</a>
        <h1>Panel de Reportes Diarios</h1>
        <p>Visión general de las ventas y actividades del día.</p>
    </header>

    <div class="container">
        <?php if ($error_conexion): ?>
            <div class="error-message"><?php echo $error_conexion; ?></div>
        <?php endif; ?>
        <form id="reporteForm">
            <div class="card-container">
                <div class="card">
                    <h2>Ventas del Día</h2>
                    <div class="form-group">
                        <label>Fecha:</label>
                        <input type="text" id="inputFecha" value="<?php echo date('d \d\e F \d\e Y'); ?>" readonly>
                        <input type="hidden" name="fecha_reporte" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Total de Ventas:</label>
                        <input type="text" id="inputTotalVentas" 
                            value="<?php echo '$' . number_format($reporte_data['total_ventas'], 2, ',', '.'); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Número de Transacciones:</label>
                        <input type="text" id="inputNumTransacciones" 
                            value="<?php echo $reporte_data['num_transacciones']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Artículos Vendidos:</label>
                        <input type="text" id="inputArticulosVendidos" 
                            value="<?php echo $reporte_data['articulos_vendidos']; ?>" readonly>
                    </div>
                    <h3>Métodos de Pago:</h3>
                    <?php if (!empty($reporte_data['metodos_pago'])): ?>
                        <?php foreach ($reporte_data['metodos_pago'] as $pago): ?>
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($pago['metodo_pago']); ?>:</label>
                                <input type="text" value="<?php echo '$' . number_format($pago['monto'], 2, ',', '.'); ?>" readonly>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="sin-datos">No hay datos de ventas para hoy.</p>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <h2>Reporte del Día</h2>
                    <div class="form-group">
                        <label>Fecha del Reporte:</label>
                        <input type="text" id="inputFechaReporte" value="<?php echo date('d \d\e F \d\e Y'); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Hora de Generación:</label>
                        <input type="text" id="inputHora" value="<?php echo date('H:i A T'); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Asesor con Más Ventas:</label>
                        <input type="text" id="inputAsesor" name="asesor" placeholder="Ej: Juan Pérez">
                    </div>
                    <div class="form-group">
                        <label>Devoluciones / Cambios:</label>
                        <input type="text" id="inputDevoluciones" name="devoluciones" placeholder="Ej: 2 (1 camiseta, 1 par de calcetines)">
                    </div>
                    <div class="form-group">
                        <label>Número de Visitas a Tienda:</label>
                        <input type="number" id="inputVisitas" name="visitas" placeholder="Ej: 120">
                    </div>
                    <div class="form-group">
                        <label>Comentarios / Incidencias:</label>
                        <textarea id="inputComentarios" name="comentarios" rows="3" placeholder="Ej: Cliente preguntó por..."></textarea>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-descargar-pdf">Descargar PDF</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 SPORTSALE Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="reporte.js"></script>
</body>
</html>