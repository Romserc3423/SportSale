<?php
date_default_timezone_set('America/Mexico_City'); // O la zona horaria de tu negocio
include 'conexion.php';

// Obtiene la fecha actual en formato 'YYYY-MM-DD'
// Esto es crucial para filtrar las ventas del día
$hoy = date('Y-m-d');

// Consulta 1: Suma total de las ventas del día
$sqlVentas = "SELECT SUM(total) AS ventas FROM venta WHERE DATE(fecha_venta) = ?";
$stmtVentas = $conn->prepare($sqlVentas);
$stmtVentas->bind_param("s", $hoy);
$stmtVentas->execute();
$resVentas = $stmtVentas->get_result()->fetch_assoc();
$ventas = $resVentas['ventas'] ?? 0;

// Consulta 2: Cantidad total de productos vendidos en el día
// Se utiliza un JOIN para vincular las tablas `detalle_venta` y `venta`
$sqlProductos = "
    SELECT SUM(dv.cantidad) AS productos
    FROM detalle_venta dv
    INNER JOIN venta v ON dv.id_venta = v.id_venta
    WHERE DATE(v.fecha_venta) = ?";
$stmtProductos = $conn->prepare($sqlProductos);
$stmtProductos->bind_param("s", $hoy);
$stmtProductos->execute();
$resProductos = $stmtProductos->get_result()->fetch_assoc();
$productos = $resProductos['productos'] ?? 0;

// Consulta 3: Número de transacciones (ventas) realizadas en el día
$sqlTransacciones = "SELECT COUNT(*) AS transacciones FROM venta WHERE DATE(fecha_venta) = ?";
$stmtTransacciones = $conn->prepare($sqlTransacciones);
$stmtTransacciones->bind_param("s", $hoy);
$stmtTransacciones->execute();
$resTransacciones = $stmtTransacciones->get_result()->fetch_assoc();
$transacciones = $resTransacciones['transacciones'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=3.0">
    <title>SPORTSALE - Panel de Gerente</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="manager-container">
        <aside class="sidebar">
            <div class="logo">
                <div class="letras">
                    <h2>SPORTSALE</h2>
                </div>
                <h3>Gerente</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#" id="nav-dashboard" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="Inventario.php" id="nav-inventory"><i class="fas fa-boxes"></i> Inventario</a></li>
                    <li><a href="Venta.php" id="nav-sell"><i class="fas fa-cash-register"></i> Realizar Venta</a></li>
                    <li><a href="asesores.php"><i class="fas fa-users"></i> Asesores</a></li>
                    <li><a href="reportes.php"><i class="fas fa-chart-line"></i> Reportes</a></li>
                    <li><a href="../logout2.php" id="nav-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content-area">
            <header class="content-header">
                <h1>Bienvenido, Gerente!</h1>
            </header>

            <section id="dashboard-view" class="content-section active">
                <h2>Resumen del Día</h2>
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <h3>Ventas del Día</h3>
                        <p id="daily-sales-value">$ <?php echo number_format($ventas, 2); ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Productos Vendidos</h3>
                        <p id="products-sold-value"><?php echo (int)$productos; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Transacciones</h3>
                        <p id="transactions-value"><?php echo (int)$transacciones; ?></p>
                    </div>
                </div>
            </section>

            <section id="inventory-view" class="content-section">
                <h2>Gestión de Inventario</h2>
                <button id="add-product-btn" class="btn"><i class="fas fa-plus"></i> Añadir Nuevo Producto</button>
                <div class="inventory-table-container">
                    <table id="inventory-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div id="product-modal" class="modal">
                    <div class="modal-content">
                        <span class="close-button">&times;</span>
                        <h2><span id="modal-title">Añadir Nuevo Producto</span></h2>
                        <form id="product-form">
                            <input type="hidden" id="product-id">
                            <label for="product-name">Nombre:</label>
                            <input type="text" id="product-name" required>
                            <label for="product-price">Precio:</label>
                            <input type="number" id="product-price" step="0.01" min="0" required>
                            <label for="product-stock">Stock:</label>
                            <input type="number" id="product-stock" min="0" required>
                            <label for="product-description">Descripción:</label>
                            <textarea id="product-description"></textarea>
                            <button type="submit" class="btn">Guardar Producto</button>
                        </form>
                    </div>
                </div>
            </section>

            <section id="sell-view" class="content-section">
                <h2>Realizar Nueva Venta</h2>
                <div class="sale-interface">
                    <div class="product-search-sale">
                        <input type="text" id="sale-search-input" placeholder="Buscar producto para vender...">
                        <button id="add-to-sale-btn" class="btn"><i class="fas fa-cart-plus"></i> Añadir</button>
                    </div>
                    <div class="cart-summary">
                        <h3>Productos en el Carrito:</h3>
                        <ul id="sale-cart-list"></ul>
                        <div class="cart-total">
                            Total: <span id="sale-total">$0.00</span>
                        </div>
                        <button id="complete-sale-btn" class="btn">Completar Venta</button>
                    </div>
                </div>
                <div class="available-products-for-sale">
                    <h3>Productos Disponibles</h3>
                    <div class="product-grid" id="sale-product-display">
                        <div class="product-card small-card" data-id="1" data-name="Zapatillas Ultraboost Pro" data-price="120.00" data-stock="15">
                            <img src="https://via.placeholder.com/150x100?text=Zapatillas" alt="Zapatillas">
                            <h4>Zapatillas Ultraboost Pro</h4>
                            <p>$120.00 | Stock: 15</p>
                            <button class="add-to-sale-quick-btn" data-id="1">Añadir</button>
                        </div>
                        <div class="product-card small-card" data-id="2" data-name="Camiseta Dry-Fit" data-price="35.00" data-stock="50">
                            <img src="https://via.placeholder.com/150x100?text=Camiseta" alt="Camiseta">
                            <h4>Camiseta Dry-Fit</h4>
                            <p>$35.00 | Stock: 50</p>
                            <button class="add-to-sale-quick-btn" data-id="2">Añadir</button>
                        </div>
                    </div>
                </div>
            </section>

            <section id="advisors-view" class="content-section">
                <h2>Información de Asesores</h2>
                <div class="advisors-table-container">
                    <table id="advisors-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Usuario</th>
                                <th>Ventas Realizadas</th>
                                <th>Último Acceso</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </section>

            <section id="reports-view" class="content-section">
                <h2>Generar Reportes</h2>
                <div class="report-options">
                    <div class="report-card">
                        <h3>Reporte de Ventas del Día</h3>
                        <p>Descarga un resumen de todas las ventas realizadas hoy.</p>
                        <button id="download-daily-report-btn" class="btn"><i class="fas fa-download"></i> Descargar</button>
                    </div>
                    <div class="report-card">
                        <h3>Reporte por Asesor</h3>
                        <p>Selecciona un asesor para ver sus ventas.</p>
                        <select id="advisor-select">
                            <option value="">Seleccionar Asesor</option>
                        </select>
                        <button id="download-advisor-report-btn" class="btn" disabled><i class="fas fa-download"></i> Descargar</button>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>