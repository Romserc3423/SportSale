<?php
// conexion.php

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos
// Por favor, verifica que estos datos sean correctos para tu entorno
define('DB_HOST', 'localhost'); // O '127.0.0.1' si 'localhost' no funciona
define('DB_NAME', 'phplogin');
define('DB_USER', 'root');
define('DB_PASS', 'Romserc3423');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si la conexión falla, muestra un mensaje de error y detiene la ejecución
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>