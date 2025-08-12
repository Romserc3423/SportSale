<?php
session_start();
header('Content-Type: application/json');

// 1) Verificar que el cliente esté autenticado
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['success'=>false, 'message'=>'Usuario no autenticado.']);
    exit;
}

// 2) Incluir conexión
include '../conexion.php'; // debe definir $conexion como mysqli

// 3) Leer JSON
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['success'=>false, 'message'=>'JSON inválido.']);
    exit;
}

// 4) Validar datos recibidos
$cartItems = $data['cartItems'] ?? null;
$total = isset($data['total']) ? floatval($data['total']) : null;
$metodo_pago = isset($data['metodo_pago']) ? strval($data['metodo_pago']) : 'Efectivo';
$id_cliente = intval($_SESSION['id_usuario']);

if (empty($cartItems) || !is_array($cartItems) || $total === null) {
    http_response_code(400);
    echo json_encode(['success'=>false, 'message'=>'Datos incompletos (cartItems o total).']);
    exit;
}

// Iniciar transacción
$conexion->begin_transaction();

try {
    // 5) Verificar stock
    $stmt_stock = $conexion->prepare("SELECT stock FROM productos WHERE id_producto = ? FOR UPDATE");
    foreach ($cartItems as $item) {
        $id_producto = intval($item['id']);
        $cantidad = intval($item['quantity']);
        if ($cantidad <= 0) throw new Exception("Cantidad inválida para el producto $id_producto.");

        $stmt_stock->bind_param("i", $id_producto);
        $stmt_stock->execute();
        $res = $stmt_stock->get_result();
        if ($res->num_rows === 0) {
            throw new Exception("Producto con ID $id_producto no existe.");
        }
        $row = $res->fetch_assoc();
        if (intval($row['stock']) < $cantidad) {
            throw new Exception("Stock insuficiente para el producto ID $id_producto. Disponible: {$row['stock']}.");
        }
    }
    $stmt_stock->close();

    // 6) Insertar venta (sin id_empleado)
    $sql_venta = "INSERT INTO venta (fecha_venta, total, metodo_pago, id_cliente) VALUES (NOW(), ?, ?, ?)";
    $stmt_venta = $conexion->prepare($sql_venta);
    if (!$stmt_venta) throw new Exception("Error prepare venta: " . $conexion->error);

    $stmt_venta->bind_param("dsi", $total, $metodo_pago, $id_cliente);
    if (!$stmt_venta->execute()) throw new Exception("Error al registrar venta: " . $stmt_venta->error);

    $id_venta = $conexion->insert_id;
    $stmt_venta->close();

    // 7) Insertar detalle y actualizar stock
    $stmt_detalle = $conexion->prepare("INSERT INTO detalle_venta (id_venta, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)");
    $stmt_update = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");

    foreach ($cartItems as $item) {
        $id_producto = intval($item['id']);
        $cantidad = intval($item['quantity']);
        $precio = floatval($item['price']);
        $subtotal_item = $cantidad * $precio;

        $stmt_detalle->bind_param("iiid", $id_venta, $id_producto, $cantidad, $subtotal_item);
        if (!$stmt_detalle->execute()) throw new Exception("Error insert detalle: " . $stmt_detalle->error);

        $stmt_update->bind_param("ii", $cantidad, $id_producto);
        if (!$stmt_update->execute()) throw new Exception("Error update stock: " . $stmt_update->error);
    }

    // 8) Confirmar transacción
    $conexion->commit();
    echo json_encode(['success'=>true, 'message'=>'Venta registrada', 'id_venta'=>$id_venta]);

} catch (Exception $e) {
    $conexion->rollback();
    http_response_code(500);
    echo json_encode([
        'success'=>false,
        'message'=>'Error procesando la venta',
        'error'=> $e->getMessage()
    ]);
}

$conexion->close();
