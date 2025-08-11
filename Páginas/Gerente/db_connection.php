<?php
// Configuración de la base de datos
$servername = "localhost"; // Generalmente es localhost si estás usando XAMPP, WAMPP, etc.
$username = "root";        // Por defecto en XAMPP/WAMPP
$password = "Romserc3423";            // Por defecto en XAMPP/WAMPP
$dbname = "phplogin"; // El nombre de la base de datos que acabas de crear

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    // Si la conexión falla, se detiene la ejecución y muestra un mensaje de error
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Opcional: Establecer el juego de caracteres a UTF-8 para evitar problemas con tildes y caracteres especiales
$conn->set_charset("utf8");

// La conexión ahora está lista para ser usada
// Por ejemplo, puedes incluir este archivo en otros scripts de PHP
// para acceder a la variable $conn.
?>