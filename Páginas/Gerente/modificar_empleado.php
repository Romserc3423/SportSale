<?php
// Incluye el archivo de conexión a la base de datos
include 'db_connection.php';

$id_empleado = null;
$nombre = "";
$telefono = "";
$puesto = "";
$id_tienda = "";
$foto_actual = "";
$target_dir = "uploads/";

// Lógica para procesar la actualización del empleado (cuando se envía el formulario)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modificar_empleado'])) {
    $id_empleado = $_POST['id_empleado'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $puesto = $_POST['puesto'];
    $id_tienda = $_POST['id_tienda'];
    $foto_actual = $_POST['foto_actual']; // Nombre de la foto actual

    $foto_nombre = $foto_actual; // Por defecto, mantiene la foto actual

    // Verifica si se subió una nueva foto
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $imageFileType = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
        $unique_filename = uniqid() . "." . $imageFileType;
        $target_file = $target_dir . $unique_filename;

        // Mueve el archivo subido
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            // Si la subida fue exitosa, actualiza el nombre de la foto
            $foto_nombre = $unique_filename;
        } else {
            echo "Error al subir la nueva imagen.";
        }
    }

    // Lógica para actualizar en la base de datos
    $sql_update = "UPDATE Empleados SET nombre = ?, telefono = ?, puesto = ?, id_tienda = ?, foto = ? WHERE id_empleado = ?";
    if ($stmt = $conn->prepare($sql_update)) {
        $stmt->bind_param("sssisi", $nombre, $telefono, $puesto, $id_tienda, $foto_nombre, $id_empleado);
        $stmt->execute();
        $stmt->close();
        header("Location: modificar_empleado.php?id=" . $id_empleado);
        exit();
    } else {
        echo "Error al preparar la consulta de actualización.";
    }
}

// Lógica para obtener los datos del empleado (cuando se carga la página por primera vez)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_empleado = $_GET['id'];
    $sql_select = "SELECT nombre, telefono, puesto, id_tienda, foto FROM Empleados WHERE id_empleado = ?";
    
    if ($stmt = $conn->prepare($sql_select)) {
        $stmt->bind_param("i", $id_empleado);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $nombre = $row['nombre'];
            $telefono = $row['telefono'];
            $puesto = $row['puesto'];
            $id_tienda = $row['id_tienda'];
            $foto_actual = $row['foto'];
        } else {
            echo "Empleado no encontrado.";
            exit();
        }

        $stmt->close();
    } else {
        echo "Error al preparar la consulta de selección.";
        exit();
    }
} else {
    echo "ID de empleado no especificado para modificar.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Empleado</title>
    <link rel="stylesheet" href="estilos_asesores.css">
</head>
<body>
    <header>
        <a href="asesores.php" class="btn back-btn"><i class="fas fa-arrow-left"></i> Volver a Asesores</a>
        <h1>MODIFICAR EMPLEADO</h1>
    </header>

    <main>
        <div id="form-modificar">
            <h3>Modificar Datos de <?php echo htmlspecialchars($nombre); ?></h3>
            <form action="modificar_empleado.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="modificar_empleado" value="1">
                <input type="hidden" name="id_empleado" value="<?php echo htmlspecialchars($id_empleado); ?>">
                <input type="hidden" name="foto_actual" value="<?php echo htmlspecialchars($foto_actual); ?>">

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required><br><br>

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required><br><br>

                <label for="puesto">Puesto:</label>
                <input type="text" id="puesto" name="puesto" value="<?php echo htmlspecialchars($puesto); ?>" required><br><br>

                <label for="id_tienda">ID Tienda:</label>
                <input type="number" id="id_tienda" name="id_tienda" value="<?php echo htmlspecialchars($id_tienda); ?>" required><br><br>
                
                <label>Foto actual:</label>
                <?php if ($foto_actual): ?>
                    <img src="<?php echo $target_dir . htmlspecialchars($foto_actual); ?>" alt="Foto actual" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;"><br><br>
                <?php endif; ?>

                <label for="foto">Nueva fotografía (opcional):</label>
                <input type="file" id="foto" name="foto"><br><br>

                <div class="form-buttons">
                    <button type="submit" class="edit-btn">Guardar Cambios</button>
                    <button type="button" class="cancel-btn" onclick="window.location.href='asesores.php'">Cancelar</button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 SPORTSALE Todos los derechos reservados.</p>
    </footer>
</body>
</html>