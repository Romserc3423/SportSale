<?php
// Incluye el archivo de conexión a la base de datos
include 'db_connection.php';

// Directorio donde se guardarán las imágenes
$target_dir = "uploads/";

// Lógica para agregar un nuevo empleado con fotografía
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar_empleado'])) {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $puesto = $_POST['puesto'];
    $id_tienda = $_POST['id_tienda'];
    $foto_nombre = null; // Inicializa el nombre de la foto

    // Verifica si se subió un archivo
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $target_file = $target_dir . basename($_FILES["foto"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Genera un nombre de archivo único para evitar colisiones
        $unique_filename = uniqid() . "." . $imageFileType;
        $target_file = $target_dir . $unique_filename;

        // Mueve el archivo subido al directorio de destino
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $foto_nombre = $unique_filename;
        } else {
            echo "Error al subir la imagen.";
        }
    }

    // Lógica para insertar en la base de datos
    if ($foto_nombre) {
        $sql_insert = "INSERT INTO Empleados (nombre, telefono, puesto, id_tienda, foto) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql_insert)) {
            $stmt->bind_param("sssis", $nombre, $telefono, $puesto, $id_tienda, $foto_nombre);
            $stmt->execute();
            $stmt->close();
            header("Location: asesores.php"); // Redirige para evitar reenvío de formulario
            exit();
        } else {
            echo "Error al preparar la consulta de inserción.";
        }
    }
}

// Consulta SQL para seleccionar los empleados que son 'Vendedor' o 'Asesor'
$sql = "SELECT id_empleado, nombre, telefono, puesto, foto FROM Empleados WHERE puesto = 'Vendedor' OR puesto = 'Asesor'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestro Equipo - Asesores Expertos | SPORTSALE</title>
    <link rel="stylesheet" href="estilos_asesores.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> </head>
    <link rel="icon" type="image/png" href="../imagenes/iconosportsale-modified.png">
<body>
    <header>
        <a href="panel.php" class="btn back-btn"><i class="fas fa-arrow-left"></i> Volver al Panel de Gerente</a>
        <h1>ASESORES ACTIVOS</h1>
    </header>

    <main class="asesores-container">
        <button class="add-employee-btn" onclick="document.getElementById('form-agregar').style.display='block'">+ Agregar Empleado</button>
        <br><br>

        <div id="form-agregar" style="display:none;">
            <h3>Agregar Nuevo Empleado</h3>
            <form action="asesores.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="agregar_empleado" value="1">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required><br><br>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required><br><br>
                <label for="puesto">Puesto:</label>
                <input type="text" id="puesto" name="puesto" required><br><br>
                <label for="id_tienda">ID Tienda:</label>
                <input type="number" id="id_tienda" name="id_tienda" required><br><br>
                <label for="foto">Fotografía:</label>
                <input type="file" id="foto" name="foto"><br><br>
                <div class="form-buttons">
                    <button type="submit" class="add-btn">Guardar</button>
                    <button type="button" class="cancel-btn" onclick="document.getElementById('form-agregar').style.display='none'">Cancelar</button>
                </div>
            </form>
            <hr>
        </div>

        <?php
        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            // Recorrer los resultados fila por fila
            while($row = $result->fetch_assoc()) {
                // Lógica para mostrar la foto
                $imagen_url = $row['foto'] ? $target_dir . htmlspecialchars($row['foto']) : "default.jpg";
                ?>
                <section class="asesor-card">
                    <img src="<?php echo $imagen_url; ?>" alt="Foto de <?php echo htmlspecialchars($row['nombre']); ?>" class="asesor-foto">
                    <h2 class="asesor-nombre"><?php echo htmlspecialchars($row['nombre']); ?></h2>
                    <p class="asesor-cargo"><?php echo htmlspecialchars($row['puesto']); ?></p>
                    <p class="asesor-bio">Este es un empleado clave de nuestro equipo, listo para ayudarte con todas tus necesidades deportivas.</p>
                    <div class="card-actions">
                        <a href="modificar_empleado.php?id=<?php echo $row['id_empleado']; ?>" class="action-btn edit-btn">Modificar</a>
                        <a href="eliminar_empleado.php?id=<?php echo $row['id_empleado']; ?>" class="action-btn delete-btn">Eliminar</a>
                    </div>
                    <ul class="asesor-habilidades">
                        <li><i class="icon-running"></i> Habilidad 1</li>
                        <li><i class="icon-trail"></i> Habilidad 2</li>
                    </ul>
                </section>
                <?php
            }
        } else {
            echo "<p>No se encontraron asesores activos.</p>";
        }

        // Cerrar la conexión
        $conn->close();
        ?>
    </main>

    <footer>
        <p>&copy; 2025 SPORTSALE Todos los derechos reservados.</p>
    </footer>
</body>
</html>