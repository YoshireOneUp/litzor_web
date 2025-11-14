<?php
require_once 'verificar_sesion.php';
verificar_administrador();

require_once 'conexion_db.php';

$id_usuario = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar que el usuario existe y es organizador (no se puede eliminar a otros admins)
$sql_verificar = "SELECT id_cl, tipo_usuario FROM clientes WHERE id_cl = ? AND tipo_usuario = 1";
$stmt = mysqli_prepare($conexion, $sql_verificar);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) === 0) {
    mysqli_close($conexion);
    header('Location: panel_admin.php?error=Usuario no encontrado o no se puede eliminar');
    exit;
}

// Eliminar usuario (eventos e invitados se eliminan automáticamente por CASCADE)
$sql_eliminar = "DELETE FROM clientes WHERE id_cl = ? AND tipo_usuario = 1";
$stmt_del = mysqli_prepare($conexion, $sql_eliminar);
mysqli_stmt_bind_param($stmt_del, "i", $id_usuario);

if (mysqli_stmt_execute($stmt_del)) {
    mysqli_close($conexion);
    header('Location: panel_admin.php?mensaje=Usuario eliminado exitosamente');
    exit;
} else {
    mysqli_close($conexion);
    header('Location: panel_admin.php?error=Error al eliminar el usuario');
    exit;
}
?>