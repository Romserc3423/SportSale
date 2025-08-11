<?php
$host = "localhost";
$usuario = "root";
$clave = "Romserc3423";
$base = "phplogin";

$conn = new mysqli($host, $usuario, $clave, $base);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
