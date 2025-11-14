<?php
// Archivo: php/verificar_sesion.php
// Protege las páginas verificando que haya sesión activa

session_start();

// Función para verificar que el usuario esté logueado
function verificar_sesion_activa() {
    if (!isset($_SESSION['id_cl']) || !isset($_SESSION['tipo_usuario'])) {
        header('Location: login.php?error=' . urlencode('Debes iniciar sesión'));
        exit;
    }
}

// Función para verificar que sea organizador (tipo 1)
function verificar_organizador() {
    verificar_sesion_activa();
    if ($_SESSION['tipo_usuario'] != 1) {
        header('Location: login.php?error=' . urlencode('Acceso denegado: Solo organizadores'));
        exit;
    }
}

// Función para verificar que sea administrador (tipo 2)
function verificar_administrador() {
    verificar_sesion_activa();
    if ($_SESSION['tipo_usuario'] != 2) {
        header('Location: login.php?error=' . urlencode('Acceso denegado: Solo administradores'));
        exit;
    }
}
?>