

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPORTSALE - Tu Tienda Deportiva</title>
    <link rel="stylesheet" href="style.css?v=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <!--MI MAYATE-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="../imagenes/iconosportsale-modified.png">
</head>
<body>
    <?php
session_start();
$nombreUsuario = isset($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : null;
?>
    <header>
        <div class="container header-content">
            <div class="logo">
                <h1>SPORTSALE</h1>
            </div>
            <div class="search-bar">
                <input type="text" id="search-input" placeholder="Buscar productos...">
                <button type="submit" id="search-button"><i class="fas fa-search"></i></button>
            </div>
            <nav class="user-nav">
                <ul>
                    <?php if ($nombreUsuario): ?>
    <li><a href="#" class="login-btn"><i class="fas fa-user"></i> <?php echo htmlspecialchars($nombreUsuario); ?></a></li>
<?php else: ?>
    <li><a href="../index.php" class="login-btn"><i class="fas fa-user"></i> Iniciar Sesión</a></li>
<?php endif; ?>
                    <li><a href="comprar.php" class="cart-btn"><i class="fas fa-shopping-cart"></i> Carrito (<span id="cart-count">0</span>)</a></li>
                    <li><a href="../logout.php" class="login-btn"><i class="fas fa-user"></i> Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <nav class="main-nav">
        <div class="container">
            <ul>
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#productos-destacados-section">Productos</a></li>
                <li><a href="#temporada-section">Temporada</a></li>
                <li><a href="#mas-vendidos-section">Más Vendidos</a></li>
                <li><a href="#contacto">Contacto</a></li>
                <li><a href="#sobrenosotros">¿Quiénes somos?</a></li>
            </ul>
        </div>
    </nav>
    </section>
    <section id="hero-banner">
  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">

      <!-- Slide 1 -->
      <div class="carousel-item slide1 active">
        <div class="banner-content">
          <h2>¡Hasta 50% de Descuento en Calzado Seleccionado!</h2>
          <p>No te pierdas nuestras ofertas exclusivas de verano. ¡Stock limitado!</p>
          <a href="#ofertas" class="btn btn-light">Ver Ofertas</a>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="carousel-item slide2">
        <div class="banner-content">
          <h2>¡Nueva Temporada 2025!</h2>
          <p>Descubre lo último en ropa y calzado deportivo.</p>
          <a href="#productos-destacados-section" class="btn btn-light">Ver Productos</a>
        </div>
      </div>

      <div class="carousel-item slide3">
        <div class="banner-content">
          <h2>¡Descubre las nuevas colaboraciones!</h2>
          <p>¡Lo último en tendencia!</p>
          <a href="#productos-destacados-section" class="btn btn-light">Ver Productos</a>
        </div>
      </div>

    </div>

    <!-- Controles manuales -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
  </div>
</section>

    <section id="productos-destacados-section" class="products-section">
        <div class="container">
            <h2>Productos Destacados</h2>
            <div class="product-grid">
                <div class="product-card" data-name="zapatillas running ultraboost pro calzado correr deporte">
                    <img src="../imagenes/Ultrabost.jpeg" alt="Zapatillas Running" class="imagenzoom">
                    <h3>Zapatillas Ultraboost Pro</h3>
                    <p>Comodidad y rendimiento para tus carreras diarias.</p>
                    <span class="price">$120.00</span>
                    <button class="add-to-cart-btn">Adquirir</button>
                </div>
                <div class="product-card" data-name="camiseta dry-fit ropa deportiva entrenamiento gym">
                    <img src="../imagenes/ITESxNIKE.png" alt="Tennis Edición ITES" class="imagenzoom">
                    <h3>Nike Air ITES</h3>
                    <p>Para los amantes del deporte y la programación.</p>
                    <span class="price">$100.00</span>
                    <button class="add-to-cart-btn">Adquirir</button>
                </div>
                <div class="product-card" data-name="short deportivo ligero gimnasio ropa correr">
                    <img src="../imagenes/birdmansuplemento_.jpg" alt="Short Gimnasio" class="imagenzoom">
                    <h3>Proteína Suplemento Birdman</h3>
                    <p>Lo mejor para el crecimiento muscular</p>
                    <span class="price">$25.00</span>
                    <button class="add-to-cart-btn">Adquirir</button>
                </div>
                <div class="product-card" data-name="mochila sportpack deportiva gym viaje">
                    <img src="../Gerente/uploads/68925ef1ddd55.jpeg" alt="Mochila Deportiva" class="imagenzoom">
                    <h3>Sudadera EX</h3>
                    <p>Cómoda y adecuada para ejercicios al aire libre</p>
                    <span class="price">$50.00</span>
                    <button class="add-to-cart-btn">Adquirir</button>
                </div>
            </div>
        </div>
    </section>

    <section id="temporada-section" class="products-section">
        <div class="container">
            <h2>Productos de Temporada</h2>
            <div class="product-grid">
                <div class="product-card" data-name="traje de baño profesional natacion piscina verano">
                    <img src="https://via.placeholder.com/300x200?text=Traje+de+Ba%C3%B1o" alt="Traje de Baño">
                    <h3>Traje de Baño Profesional</h3>
                    <p>Diseñado para velocidad y comodidad en el agua.</p>
                    <span class="price">$65.00</span>
                    <button class="add-to-cart-btn">Adquirir</button>
                </div>
                <div class="product-card" data-name="sandalias deportivas air playa piscina calzado verano">
                    <img src="https://via.placeholder.com/300x200?text=Sandalias+Deportivas" alt="Sandalias Deportivas">
                    <h3>Sandalias Deportivas Air</h3>
                    <p>Perfectas para después del entrenamiento o la playa.</p>
                    <span class="price">$30.00</span>
                    <button class="add-to-cart-btn">Adquirir</button>
                </div>
                <div class="product-card" data-name="gorra anti-uv running proteccion solar verano">
                    <img src="https://via.placeholder.com/300x200?text=Gorra+Running" alt="Gorra Running">
                    <h3>Gorra Anti-UV</h3>
                    <p>Protección solar y ventilación para tus salidas.</p>
                    <span class="price">$20.00</span>
                    <button class="add-to-cart-btn">Adquirir</button>
                </div>
            </div>
        </div>
    </section>

    <section id="mas-vendidos-section" class="products-section">
        <div class="container">
            <h2>Nuestros Más Vendidos</h2>
            <div class="product-grid">
                <div class="product-card" data-name="balon de futbol pro futball pelota">
                    <img src="https://via.placeholder.com/300x200?text=Balon+Futbol" alt="Balón de Fútbol">
                    <h3>Balón de Fútbol Pro</h3>
                    <p>El balón favorito de nuestros clientes, alta durabilidad.</p>
                    <span class="price">$45.00</span>
                    <button class="add-to-cart-btn">Adquirir</button>
                </div>
                <div class="product-card" data-name="guantes de entrenamiento gimnasio pesas gym">
                    <img src="https://via.placeholder.com/300x200?text=Guantes+Gimnasio" alt="Guantes de Gimnasio">
                    <h3>Guantes de Entrenamiento</h3>
                    <p>Agarre y protección para tus manos en cada levantamiento.</p>
                    <span class="price">$22.00</span>
                    <button class="add-to-cart-btn">Adquirir</button>
                </div>
                <div class="product-card" data-name="botella hidratacion eco agua gym correr">
                    <img src="https://via.placeholder.com/300x200?text=Botella+Agua" alt="Botella de Agua">
                    <h3>Botella Hidratación Eco</h3>
                    <p>Mantente hidratado con estilo y de forma sostenible.</p>
                    <span class="price">$15.00</span>
                    <button class="add-to-cart-btn">Adquirir</button>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-columns">
                <div class="footer-col">
                    <h3>SPORTSALE</h3>
                    <p>Tu destino para el mejor equipo deportivo. Calidad, rendimiento y estilo.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3>Enlaces Rápidos</h3>
                    <ul>
                        <li><a href="#inicio">Inicio</a></li>
                        <li><a href="#productos-destacados-section">Productos</a></li>
                        <li><a href="#">Sobre Nosotros</a></li>
                        <li><a href="#">Política de Privacidad</a></li>
                        <li><a href="#">Términos y Condiciones</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Atención al Cliente</h3>
                    <ul>
                        <li><a href="#">Preguntas Frecuentes</a></li>
                        <li><a href="#">Devoluciones y Envíos</a></li>
                        <li><a href="#">Contacto</a></li>
                        <li><p>Email: info@sportsale.com</p></li>
                        <li><p>Tel: +52 999 123 4567</p></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 SPORTSALE. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
        document.addEventListener('DOMContentLoaded', () => {
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            const cartCountSpan = document.getElementById('cart-count');
            let cartCount = 0;

            addToCartButtons.forEach(button => {
                button.addEventListener('click', () => {
                    cartCount++;
                    cartCountSpan.textContent = cartCount;
                    alert('Producto añadido al carrito (simulado)');
                });
            });

           

         

            const searchInput = document.getElementById('search-input');
            const searchButton = document.getElementById('search-button');
            const productCards = document.querySelectorAll('.product-card'); 
            const productSections = document.querySelectorAll('.products-section'); 

            const performSearch = () => {
                const searchTerm = searchInput.value.toLowerCase().trim(); 

                let anyProductFound = false; 

                
                productCards.forEach(card => {
                    
                    const productNameData = card.dataset.name;

                    
                    if (productNameData.includes(searchTerm) || searchTerm === '') {
                        card.style.display = 'block'; 
                        anyProductFound = true; 
                    } else {
                        card.style.display = 'none'; 
                    }
                });

               
                productSections.forEach(section => {
                    const cardsInASection = section.querySelectorAll('.product-card');
                    let sectionHasVisibleProducts = false;
                    cardsInASection.forEach(card => {
                        if (card.style.display !== 'none') {
                            sectionHasVisibleProducts = true;
                        }
                    });

                    if (sectionHasVisibleProducts || searchTerm === '') {
                        section.style.display = 'block'; 
                    } else {
                        section.style.display = 'none'; 
                    }
                });

                
                if (!anyProductFound && searchTerm !== '') {
                   
                    console.log("No se encontraron resultados para: " + searchTerm);
                 
                }
            };


            searchButton.addEventListener('click', performSearch);

            searchInput.addEventListener('keyup', (event) => {

                if (event.key === 'Enter' || searchInput.value.trim() === '') {
                    performSearch();
                } else if (event.key !== 'Shift' && event.key !== 'Control' && event.key !== 'Alt') { 
                    performSearch();
                }
            });
        });
        
    </script>
</body>
</html>