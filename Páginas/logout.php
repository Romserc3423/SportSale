<?php
session_start();
session_unset();  // elimina todas las variables de sesión
session_destroy(); // destruye la sesión
header("Location: Principal/index.php"); // redirige al login
exit;
?>
