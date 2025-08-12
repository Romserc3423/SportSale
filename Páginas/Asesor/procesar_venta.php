<?php
// Incluye el archivo de conexión a la base de datos
include 'conexion.php';

// Habilita la visualización de errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Establece el encabezado para que el navegador sepa que recibirá una respuesta JSON
header('Content-Type: application/json');

// Decodifica el JSON enviado por JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Validación inicial de los datos recibidos
if (empty($data['cartItems']) || !isset($data['total'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos de venta incompletos.']);
    exit();
}

// Inicia una transacción para asegurar la integridad de la base de datos
$conn->begin_transaction();

try {
    $total = $data['total'];
    $metodo_pago = $data['metodo_pago'] ?? 'Efectivo';
    $id_empleado = $data['id_empleado'] ?? 1;
    $id_cliente = $data['id_cliente'] ?? 1;

    // 1. Insertar en la tabla `venta`
    $sql_venta = "INSERT INTO venta (fecha_venta, total, metodo_pago, id_cliente, id_empleado) VALUES (NOW(), ?, ?, ?, ?)";
    $stmt_venta = $conn->prepare($sql_venta);
    $stmt_venta->bind_param("dsii", $total, $metodo_pago, $id_cliente, $id_empleado);
    $stmt_venta->execute();

    $id_venta = $conn->insert_id;

    // 2. Preparar las sentencias para `detalle_venta` y `productos`
    // NOTA: Se ha corregido la consulta para usar 'subtotal' en lugar de 'precio_unitario'
    $sql_detalle = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_detalle);

    $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
    $stmt_update = $conn->prepare($sql_update_stock);

    // 3. Iterar sobre los productos del carrito para insertar y actualizar
    foreach ($data['cartItems'] as $item) {
        $id_producto = $item['id'];
        $cantidad = $item['quantity'];
        $precio_unitario = $item['price'];
        
        // Calcular el subtotal para el ítem actual
        $subtotal_item = $cantidad * $precio_unitario;

        // Insertar en la tabla `detalle_venta`
        // NOTA: Se ha corregido el bind_param para usar el nuevo subtotal_item
        $stmt_detalle->bind_param("iiid", $id_venta, $id_producto, $cantidad, $subtotal_item);
        $stmt_detalle->execute();

        // Actualizar el stock del producto
        $stmt_update->bind_param("ii", $cantidad, $id_producto);
        $stmt_update->execute();
    }

    // Si todas las operaciones tienen éxito, confirma la transacción
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Venta completada.', 'id_venta' => $id_venta]);

} catch (Exception $e) {
    // Si ocurre un error, revierte la transacción
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al procesar la venta.', 'error' => $e->getMessage()]);
}

// Cierra la conexión a la base de datos
$conn->close();
?>