<?php
// Archivo: php/logout.php
// Cierra la sesión y redirige al login

session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, también se debe borrar la cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destruir la sesión
session_destroy();

// Redirigir al login con mensaje
header("Location: ../public/login.php?mensaje=Sesión cerrada correctamente");
exit();
?>