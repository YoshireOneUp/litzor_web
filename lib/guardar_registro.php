<?php


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/register.php");
    exit();
}

require_once __DIR__ . '../../config/conexion_db.php';

$nombre_cl = trim($_POST['nombre_cl']);
$correo_cl = trim($_POST['correo_cl']);
$contrasena_cl = trim($_POST['contrasena_cl']);

// Validaciones básicas
if (empty($nombre_cl) || empty($correo_cl) || empty($contrasena_cl)) {
    header("Location: ../public/register.php?error=Todos los campos son obligatorios");
    exit();
}

if (strlen($contrasena_cl) < 6) {
    header("Location: ../public/register.php?error=La contraseña debe tener al menos 6 caracteres");
    exit();
}

if (!filter_var($correo_cl, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../public/register.php?error=Correo electrónico no válido");
    exit();
}

// Verificar si el correo ya existe usando consulta preparada
$consulta_verificar = "SELECT id_cl FROM clientes WHERE correo_cl = ?";
$stmt_verificar = mysqli_prepare($conexion, $consulta_verificar);
mysqli_stmt_bind_param($stmt_verificar, "s", $correo_cl);
mysqli_stmt_execute($stmt_verificar);
$resultado_verificar = mysqli_stmt_get_result($stmt_verificar);

if (mysqli_num_rows($resultado_verificar) > 0) {
    mysqli_stmt_close($stmt_verificar);
    mysqli_close($conexion);
    header("Location: ../public/register.php?error=1"); // El correo ya existe
    exit();
}

mysqli_stmt_close($stmt_verificar);

// Insertar nuevo cliente (tipo_usuario = 1 para organizadores)
// NOTA: En producción deberías usar password_hash() para las contraseñas
$consulta_insertar = "INSERT INTO clientes (nombre_cl, correo_cl, contrasena_cl, tipo_usuario) 
                       VALUES (?, ?, ?, 1)";

$stmt_insertar = mysqli_prepare($conexion, $consulta_insertar);
mysqli_stmt_bind_param($stmt_insertar, "sss", $nombre_cl, $correo_cl, $contrasena_cl);

if (mysqli_stmt_execute($stmt_insertar)) {
    // Obtener el ID del usuario recién creado
    $id_nuevo_usuario = mysqli_insert_id($conexion);
    
    mysqli_stmt_close($stmt_insertar);
    mysqli_close($conexion);
    
    // Registro exitoso - iniciar sesión automáticamente
    session_start();
    $_SESSION['id_cl'] = $id_nuevo_usuario;
    $_SESSION['correo_cl'] = $correo_cl;
    $_SESSION['nombre_cl'] = $nombre_cl;
    $_SESSION['tipo_usuario'] = 1; // Organizador
    
    // Redirigir al home del organizador
    header("Location: ../public/organizador/home.php?mensaje=Cuenta creada exitosamente");
    exit();
} else {
    mysqli_stmt_close($stmt_insertar);
    mysqli_close($conexion);
    header("Location: ../public/register.php?error=2"); // Error al insertar
    exit();
}
?>