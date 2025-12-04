<?php
require_once './lib/verificar_sesion.php';
verificar_organizador();

require_once './config/conexion_db.php';

$id_evento = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_organizador = $_SESSION['id_cl'];

// Verificar que el evento pertenece al organizador
$sql_verificar = "SELECT id_evento FROM eventos WHERE id_evento = ? AND id_organizador = ?";
$stmt = mysqli_prepare($conexion, $sql_verificar);
mysqli_stmt_bind_param($stmt, "ii", $id_evento, $id_organizador);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) === 0) {
    mysqli_close($conexion);
    header('Location: ../public/organizador/home.php?error=Evento no encontrado');
    exit;
}

// Eliminar evento (los invitados se eliminan automáticamente por la clave foránea con ON DELETE CASCADE)
$sql_eliminar = "DELETE FROM eventos WHERE id_evento = ? AND id_organizador = ?";
$stmt_del = mysqli_prepare($conexion, $sql_eliminar);
mysqli_stmt_bind_param($stmt_del, "ii", $id_evento, $id_organizador);

if (mysqli_stmt_execute($stmt_del)) {
    mysqli_close($conexion);
    header('Location: ../public/organizador/home.php?mensaje=Evento eliminado exitosamente');
    exit;
} else {
    mysqli_close($conexion);
    header('Location: ../public/organizador/home.php?error=Error al eliminar el evento');
    exit;
}
?>