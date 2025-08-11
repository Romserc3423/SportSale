<?php
// Incluye el archivo de conexión a la base de datos
include 'conexion.php';

// Habilita la visualización de errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicializa las variables para los datos
$productos = [];
$error_message = '';

// Consulta para obtener todos los productos, AHORA INCLUYENDO LA COLUMNA 'imagen'
$sql = "SELECT id_producto, nombre, precio, stock, descripcion, imagen FROM productos ORDER BY nombre ASC";
$result = $conn->query($sql);

if ($result === false) {
    // Si la consulta falla, muestra un mensaje de error
    $error_message = "Error en la consulta: " . $conn->error;
} else {
    // Si la consulta es exitosa, almacena los productos en un array
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }
}

// Cierra la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=3.0">
    <title>SPORTSALE - Generar Venta</title>
    <link rel="stylesheet" href="venta.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="sale-header">
        <div class="sale-header-content">
            <h1 class="sale-title">Realizar Venta</h1>
            <nav class="sale-nav">
                <a href="panel.php" class="btn back-btn"><i class="fas fa-arrow-left"></i> Volver al Panel de Gerente</a>
                <a href="index.html" class="btn back-btn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <main class="sale-main-content">
        <div class="sale-interface-grid">
            <section class="product-selection-area">
                <div class="sale-search-bar">
                    <input type="text" id="sale-product-search-input" placeholder="Buscar producto por nombre o ID..." autofocus>
                    <button id="sale-search-btn" class="btn search-btn"><i class="fas fa-search"></i></button>
                </div>

                <div class="product-display-grid" id="product-display-grid">
                    <?php if (!empty($error_message)): ?>
                        <p class="no-products-found"><?php echo $error_message; ?></p>
                    <?php elseif (empty($productos)): ?>
                        <p class="no-products-found">No se encontraron productos disponibles.</p>
                    <?php else: ?>
                        <?php foreach ($productos as $producto): ?>
                            <div class="product-card small-card" 
                                 data-id="<?php echo htmlspecialchars($producto['id_producto']); ?>" 
                                 data-name="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                                 data-price="<?php echo htmlspecialchars($producto['precio']); ?>" 
                                 data-stock="<?php echo htmlspecialchars($producto['stock']); ?>"
                                 data-image="<?php echo htmlspecialchars($producto['imagen']); ?>">
                                <div class="product-image-container">
                                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-image">
                                    

                                </div>
                                
                                <h4><?php echo htmlspecialchars($producto['nombre']); ?></h4>
                                <p>$<?php echo number_format($producto['precio'], 2); ?> | Stock: <?php echo htmlspecialchars($producto['stock']); ?></p>
                                <button class="add-to-cart-btn btn" data-id="<?php echo htmlspecialchars($producto['id_producto']); ?>">Añadir al carrito</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <section class="cart-summary-area">
                <h2>Carrito de Compra</h2>
                <ul id="sale-cart-list">
                </ul>
                <p id="empty-cart-message" class="empty-cart-message">El carrito está vacío.</p>

                <div class="cart-total-section">
                    <div class="total-line">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal">$0.00</span>
                    </div>
                    <div class="total-line">
                        <span>Descuento:</span>
                        <span id="cart-discount">$0.00</span>
                    </div>
                    <div class="total-line final-total">
                        <span>Total:</span>
                        <span id="cart-grand-total">$0.00</span>
                    </div>
                </div>

                <div class="payment-options">
                    <label for="payment-method">Método de Pago:</label>
                    <select id="payment-method">
                        <option value="Efectivo">Efectivo</option>
                        <option value="TarjetaCredito">Tarjeta de Crédito</option>
                        <option value="TarjetaDebito">Tarjeta de Débito</option>
                        <option value="Transferencia">Transferencia Bancaria</option>
                    </select>

                    <label for="amount-received">Monto Recibido:</label>
                    <input type="number" id="amount-received" step="0.01" min="0" placeholder="0.00">
                    <p class="change-display">Cambio: <span id="change-due">$0.00</span></p>
                </div>

                <div class="cart-action-buttons">
                    <button id="complete-sale-btn" class="btn primary-btn"><i class="fas fa-check-circle"></i> Completar Venta</button>
                    <button id="cancel-sale-btn" class="btn secondary-btn"><i class="fas fa-times-circle"></i> Cancelar Venta</button>
                </div>
            </section>
        </div>
    </main>

    <script src="venta.js"></script>
</body>
</html>