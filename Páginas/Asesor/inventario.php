<?php
// Incluye el archivo de conexión a la base de datos
include 'conexion.php';

// Consulta para obtener todos los productos del inventario
$sql = "SELECT p.id_producto, p.nombre, c.nombre AS categoria, p.precio, p.stock, p.descripcion
        FROM productos p
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria
        ORDER BY p.id_producto DESC";
$resultado = $conn->query($sql);

$productos = [];
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $productos[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPORTSALE - Gestión de Inventario</title>
    <link rel="stylesheet" href="style3.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="../imagenes/iconosportsale-modified.png">
</head>
<body>
    <header class="inventory-header">
        <div class="inventory-header-content">
            <h1 class="inventory-title">Gestión de Inventario</h1>
            <nav class="inventory-nav">
                <a href="panel.php" class="btn back-btn"><i class="fas fa-arrow-left"></i> Volver al Panel de Gerente</a>
                <a href="AgregarProd.php" class="btn"><i class="fas fa-plus"></i> Añadir Nuevo Producto</a>
                <a href="../logout2.php" class="btn back-btn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <main class="inventory-main-content">
        <div class="category-filter-container">
            <div class="category-tabs" id="category-tabs">
                <button class="category-tab active" data-category="ALL">Todas</button>
                <button class="category-tab" data-category="ROPA">Ropa</button>
                <button class="category-tab" data-category="CALZADO">Calzado</button>
                <button class="category-tab" data-category="EQUIPOS">Equipos</button>
                <button class="category-tab" data-category="ACCESORIOS">Accesorios</button>
                <button class="category-tab" data-category="SUPLEMENTOS">Suplementos</button>
                <button class="category-tab" data-category="TECNOLOGIA">Tecnología</button>
                <button class="category-tab" data-category="UNISEX">Unisex</button>
                <button class="category-tab" data-category="DEPORTES">Deportes</button>
            </div>
            <div class="stock-filter-tabs" id="stock-filter-tabs">
                <button class="stock-tab active" data-stock-status="ALL">Todos los Estados</button>
                <button class="stock-tab" data-stock-status="AVAILABLE">Disponibles</button>
                <button class="stock-tab" data-stock-status="LOW_STOCK">Bajo Stock</button>
                <button class="stock-tab" data-stock-status="OUT_OF_STOCK">Sin Stock</button>
            </div>
        </div>

        <div class="inventory-search-bar">
            <input type="text" id="inventory-search-input" placeholder="Buscar producto por nombre o descripción...">
            <button id="inventory-search-button" class="btn"><i class="fas fa-search"></i> Buscar</button>
        </div>

        <div class="inventory-table-container">
            <table id="inventory-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($productos) > 0): ?>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($producto['id_producto']); ?></td>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($producto['categoria']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($producto['precio'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                                <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                                <td>
                                    <?php
                                    if ($producto['stock'] <= 5) {
                                        echo '<span class="status-badge low-stock">Bajo Stock</span>';
                                    } elseif ($producto['stock'] == 0) {
                                        echo '<span class="status-badge out-of-stock">Sin Stock</span>';
                                    } else {
                                        echo '<span class="status-badge available">Disponible</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-edit" data-id="<?php echo $producto['id_producto']; ?>"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-delete" data-id="<?php echo $producto['id_producto']; ?>"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No se encontraron productos en el inventario.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="product-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2><span id="modal-title">Añadir Nuevo Producto</span></h2>
            <form id="product-form">
                <input type="hidden" id="product-id">
                <label for="product-name">Nombre:</label>
                <input type="text" id="product-name" required>
                <label for="product-category">Categoría:</label>
                <select id="product-category" required>
                    <option value="">Seleccionar Categoría</option>
                    <option value="ROPA">Ropa</option>
                    <option value="CALZADO">Calzado</option>
                    <option value="EQUIPOS">Equipos</option>
                    <option value="ACCESORIOS">Accesorios</option>
                    <option value="SUPLEMENTOS">Suplementos</option>
                    <option value="TECNOLOGIA">Tecnología</option>
                    <option value="UNISEX">Unisex</option>
                    <option value="DEPORTES">Deportes</option>
                </select>
                <label for="product-price">Precio:</label>
                <input type="number" id="product-price" step="0.01" min="0" required>
                <label for="product-stock">Stock:</label>
                <input type="number" id="product-stock" min="0" required>
                <label for="product-description">Descripción:</label>
                <textarea id="product-description"></textarea>
             
           
            <form id="product-form" enctype="multipart/form-data">
    <input type="hidden" id="product-id">
    <div class="form-group">
        <label for="product-image">Imagen del Producto:</label>
        <input type="file" id="product-image" name="imagen">
    </div>

    <div class="form-group" id="current-image-container" style="display: none;">
        <label>Imagen Actual:</label>
        <img id="current-image" src="" alt="Imagen del Producto" style="max-width: 150px; display: block; margin-top: 10px;">
    </div>

    <button type="submit" class="btn">Guardar Producto</button>
</form>
 </form>
        </div>
    </div>
    <script src="inventario.js"></script> 
</body>
</html>