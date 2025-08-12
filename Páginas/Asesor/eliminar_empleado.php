<?php
// Incluye el archivo de conexión a la base de datos
include 'db_connection.php';

// Verifica si se ha pasado un ID de empleado para eliminar
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_empleado = $_GET['id'];

    // Consulta SQL para eliminar el empleado
    $sql_delete = "DELETE FROM Empleados WHERE id_empleado = ?";

    // Prepara la consulta para evitar inyección SQL
    if ($stmt = $conn->prepare($sql_delete)) {
        $stmt->bind_param("i", $id_empleado);

        // Ejecuta la consulta
        if ($stmt->execute()) {
            // Eliminación exitosa
            // Redirige de vuelta a la página de asesores
            header("Location: asesores.php?status=deleted");
            exit();
        } else {
            // Error en la ejecución
            echo "Error al eliminar el empleado: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Error en la preparación de la consulta
        echo "Error al preparar la consulta: " . $conn->error;
    }
} else {
    // No se proporcionó un ID de empleado válido
    echo "ID de empleado no especificado.";
}

$conn->close();
?>