<?php
include 'conexion.php';

$mensaje = '';

$categorias = [];
$sql_categorias = "SELECT id_categoria, nombre FROM categorias ORDER BY nombre";
$res_categorias = $conn->query($sql_categorias);
if ($res_categorias) {
    while ($fila = $res_categorias->fetch_assoc()) {
        $categorias[] = $fila;
    }
} else {
    $mensaje = "Error al obtener las categorías: " . $conn->error;
}

$marcas = [];
$sql_marcas = "SELECT id_marca, nombre FROM marcas ORDER BY nombre";
$res_marcas = $conn->query($sql_marcas);
if ($res_marcas) {
    while ($fila = $res_marcas->fetch_assoc()) {
        $marcas[] = $fila;
    }
} else {
    $mensaje .= "Error al obtener las marcas: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $id_categoria = $_POST['id_categoria'] ?? '';
    $id_marca = $_POST['id_marca'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $descripcion = $_POST['descripcion'] ?? '';
    $color = $_POST['color'] ?? '';

    $ruta_imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $directorio_destino = 'uploads/';
        if (!is_dir($directorio_destino)) {
            mkdir($directorio_destino, 0755, true);
        }

        $nombre_archivo = basename($_FILES['imagen']['name']);
        $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
        $nombre_unico = uniqid() . '.' . $extension;
        $ruta_final = $directorio_destino . $nombre_unico;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_final)) {
            $ruta_imagen = $ruta_final;
        } else {
            $mensaje = "Error al subir la imagen.";
            goto end;
        }
    }

    if (empty($nombre) || empty($id_categoria) || empty($id_marca) || $precio <= 0 || $stock < 0) {
        $mensaje = "Error: Por favor, complete todos los campos obligatorios.";
    } else {
        $sql = "INSERT INTO productos (nombre, descripcion, stock, precio, id_marca, id_categoria, color, imagen) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdiisss", $nombre, $descripcion, $stock, $precio, $id_marca, $id_categoria, $color, $ruta_imagen);

        if ($stmt->execute()) {
            $mensaje = "¡Producto '$nombre' añadido con éxito!";
        } else {
            $mensaje = "Error al añadir el producto: " . $stmt->error;
        }

        $stmt->close();
    }
}
end:
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPORTSALE - Añadir Nuevo Producto</title>
    <link rel="stylesheet" href="Agp.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="../imagenes/iconosportsale-modified.png">
</head>
<body>
    <div class="form-container">
        <h2>Añadir Nuevo Producto</h2>

        <?php if (!empty($mensaje)): ?>
            <p style="text-align:center; color: #333; font-weight: bold;"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <form id="add-product-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="product-id"> 

            <label for="product-name">Nombre:</label>
            <input type="text" id="product-name" name="nombre" required>

            <label for="product-color">Color:</label>
            <input type="text" id="product-color" name="color">

            <label for="product-brand">Marca:</label>
            <select id="product-brand" name="id_marca" required>
                <option value="">Seleccionar Marca</option>
                <?php foreach ($marcas as $mar): ?>
                    <option value="<?php echo $mar['id_marca']; ?>"><?php echo htmlspecialchars($mar['nombre']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="product-category">Categoría:</label>
            <select id="product-category" name="id_categoria" required>
                <option value="">Seleccionar Categoría</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['id_categoria']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="product-price">Precio:</label>
            <input type="number" id="product-price" name="precio" step="0.01" min="0" required>

            <label for="product-stock">Stock:</label>
            <input type="number" id="product-stock" name="stock" min="0" required>

            <label for="product-image">Imagen del Producto:</label>
            <input type="file" id="product-image" name="imagen">

            <label for="product-description">Descripción:</label>
            <textarea id="product-description" name="descripcion"></textarea>

            <div class="form-actions">
                <button type="submit" class="btn primary-btn">Guardar Producto</button>
                <button type="button" id="cancel-add-btn" class="btn cancel-btn" onclick="window.location.href='Inventario.php'">Cancelar</button>
            </div>
        </form>
    </div>

    <?php if (strpos($mensaje, 'añadido con éxito') !== false): ?>
        <script>
            alert("✅ Producto añadido con éxito.");
            setTimeout(function() {
                window.location.href = "inventario.php";
            }, 2000);
        </script>
    <?php endif; ?>
</body>
</html>
