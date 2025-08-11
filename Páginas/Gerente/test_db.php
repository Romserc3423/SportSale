<?php
// Incluir el archivo de conexión
include 'db_connection.php';

// Si el script llega a este punto, significa que la conexión fue exitosa.
echo "<h1>Conexión a la base de datos 'proyecto_gerente' exitosa.</h1>";

// Opcional: Cerrar la conexión
$conn->close();
?>