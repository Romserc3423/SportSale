<?php
include 'conexion.php';

// Asegúrate de que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    exit;
}

// Obtener los datos enviados por JavaScript
$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';
$id = $data['id'] ?? 0;

if ($action === 'delete') {
    if ($id) {
        $sql = "DELETE FROM productos WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Producto eliminado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el producto.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID de producto no proporcionado.']);
    }
} elseif ($action === 'update') {
    $nombre = $data['nombre'] ?? '';
    $precio = $data['precio'] ?? 0;
    $stock = $data['stock'] ?? 0;
    $descripcion = $data['descripcion'] ?? '';
    // Debes obtener la categoría de alguna manera, por ahora la dejaremos sin modificar
    
    if ($id && $nombre && $precio >= 0 && $stock >= 0) {
        $sql = "UPDATE productos SET nombre = ?, precio = ?, stock = ?, descripcion = ? WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdisi", $nombre, $precio, $stock, $descripcion, $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Producto actualizado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el producto.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Datos de producto incompletos.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
}