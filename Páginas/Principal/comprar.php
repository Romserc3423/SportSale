<?php
session_start();


// Incluye la conexión
include '../conexion.php';

// Obtener productos
$productos = [];
$error_message = '';
$sql = "SELECT id_producto, nombre, precio, stock, descripcion, imagen FROM productos ORDER BY nombre ASC";
$result = $conexion->query($sql);
if ($result === false) {
    $error_message = "Error en la consulta: " . $conexion->error;
} else {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }
}
$conexion->close();

// Obtener id_cliente si está en sesión
$id_cliente = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
$nombreUsuario = htmlspecialchars($_SESSION['nombre_usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>SPORTSALE - Comprar</title>
    <link rel="stylesheet" href="comprar.css?v=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header (mantener estética) -->
    <header>
        <div class="container header-content">
            <div class="logo"><h1>SPORTSALE</h1></div>
            <div class="search-bar">
                <input type="text" id="search-input" placeholder="Buscar productos...">
                <button type="submit" id="search-button"><i class="fas fa-search"></i></button>
            </div>
            <nav class="user-nav">
                <ul>
                    <li><a href="#" class="login-btn"><i class="fas fa-user"></i> <?php echo $nombreUsuario; ?></a></li>
                    <li><a href="../logout.php" class="login-btn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <nav class="main-nav">
        <div class="container">
            <ul>
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="comprar.php" class="active">Comprar</a></li>
                <li><a href="#contacto">Contacto</a></li>
            </ul>
        </div>
    </nav>

    <main class="container comprar-main">
        <div class="row">
            <div class="col-md-8">
                <h2>Productos Disponibles</h2>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <div class="product-grid" id="product-grid">
                    <?php if (empty($productos)): ?>
                        <p>No hay productos disponibles.</p>
                    <?php else: ?>
                        <?php foreach ($productos as $p): ?>
                            <div class="product-card" 
                                 data-id="<?php echo intval($p['id_producto']); ?>"
                                 data-name="<?php echo htmlspecialchars($p['nombre']); ?>"
                                 data-price="<?php echo htmlspecialchars($p['precio']); ?>"
                                 data-stock="<?php echo intval($p['stock']); ?>"
                                 data-image="<?php echo htmlspecialchars($p['imagen']); ?>">
                                <div class="img-wrap">
                                    <img src="<?php echo htmlspecialchars($p['imagen']); ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>">
                                </div>
                                <h4><?php echo htmlspecialchars($p['nombre']); ?></h4>
                                <p class="desc"><?php echo htmlspecialchars($p['descripcion']); ?></p>
                                <p class="price">$<?php echo number_format($p['precio'], 2); ?> <span class="stock">| Stock: <?php echo intval($p['stock']); ?></span></p>
                                <div class="card-actions">
                                    <input type="number" class="qty-input" min="1" value="1" max="<?php echo intval($p['stock']); ?>">
                                    <button class="btn add-to-cart-btn"><i class="fas fa-cart-plus"></i> Añadir</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-4">
                <aside class="cart-panel">
                    <h3>Carrito</h3>
                    <ul id="cart-items-list" class="cart-items-list"></ul>
                    <p id="empty-cart">El carrito está vacío.</p>

                    <div class="totals">
                        <div class="line"><span>Subtotal</span><span id="subtotal">$0.00</span></div>
                        <div class="line"><span>Descuento</span><span id="discount">$0.00</span></div>
                        <div class="line total"><span>Total</span><span id="total">$0.00</span></div>
                    </div>

                    <div class="payment">
                        <label for="payment-method">Método de pago</label>
                        <select id="payment-method">
                            <option value="Efectivo">Efectivo</option>
                            <option value="TarjetaCredito">Tarjeta de Crédito</option>
                            <option value="TarjetaDebito">Tarjeta de Débito</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>

                        <label for="amount-received">Monto recibido</label>
                        <input type="number" id="amount-received" step="0.01" min="0" placeholder="0.00">
                        <p>Cambio: <span id="change">$0.00</span></p>
                    </div>

                    <div class="cart-actions">
                        <button id="checkout-btn" class="btn primary-btn" <?php /* disabled initially via JS */ ?>>Comprar ahora</button>
                        <button id="clear-cart-btn" class="btn secondary-btn">Vaciar</button>
                    </div>
                    <div id="checkout-msg" class="checkout-msg"></div>
                </aside>
            </div>
        </div>
    </main>

    <footer>
        <div class="container footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> SPORTSALE. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        // pasar id_cliente a JS
        const CLIENTE_ID = <?php echo ($id_cliente>0)? $id_cliente : 'null'; ?>;
    </script>
    <script src="comprar.js"></script>
</body>
</html>
