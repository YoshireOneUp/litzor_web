<?php

session_start();

function verificar_sesion_activa()
{
    if (!isset($_SESSION['id_cl']) || !isset($_SESSION['tipo_usuario'])) {
        // RUTA CORREGIDA
        header('Location: ../login.php?error=' . urlencode('Debes iniciar sesión'));
        exit;
    }
}

function verificar_organizador()
{
    verificar_sesion_activa();
    if ($_SESSION['tipo_usuario'] != 1) {
        // RUTA CORREGIDA
        header('Location: ../login.php?error=' . urlencode('Acceso denegado: Solo organizadores'));
        exit;
    }
}

function verificar_administrador()
{
    verificar_sesion_activa();
    if ($_SESSION['tipo_usuario'] != 2) {
        // RUTA CORREGIDA
        header('Location: ../login.php?error=' . urlencode('Acceso denegado: Solo administradores'));
        exit;
    }
}
