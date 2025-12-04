<?php

session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

session_destroy();

header("Location: ./login.php?mensaje=Sesión cerrada correctamente");
exit();
?>