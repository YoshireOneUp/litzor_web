<?php
require_once 'verificar_sesion.php';
verificar_organizador();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    require_once 'conexion_db.php';
    
    $id_organizador = $_SESSION['id_cl'];
    $id_evento = intval($_POST['id_evento']);
    $nombre_evento = trim($_POST['nombre_evento']);
    $fecha_evento = $_POST['fecha_evento'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $ubicacion = trim($_POST['ubicacion']);
    $latitud = floatval($_POST['latitud']);
    $longitud = floatval($_POST['longitud']);
    $invitados_json = $_POST['invitados'];
    
    // Verificar que el evento pertenece al organizador
    $sql_verificar = "SELECT id_evento FROM eventos WHERE id_evento = ? AND id_organizador = ?";
    $stmt_ver = mysqli_prepare($conexion, $sql_verificar);
    mysqli_stmt_bind_param($stmt_ver, "ii", $id_evento, $id_organizador);
    mysqli_stmt_execute($stmt_ver);
    $resultado_ver = mysqli_stmt_get_result($stmt_ver);
    
    if (mysqli_num_rows($resultado_ver) === 0) {
        mysqli_close($conexion);
        header('Location: home.php?error=Evento no encontrado');
        exit;
    }
    
    // Actualizar evento
    $sql_update = "UPDATE eventos 
                   SET nombre_evento = ?, fecha_evento = ?, hora_inicio = ?, hora_fin = ?, 
                       ubicacion = ?, latitud = ?, longitud = ?
                   WHERE id_evento = ? AND id_organizador = ?";
    
    $stmt = mysqli_prepare($conexion, $sql_update);
    mysqli_stmt_bind_param($stmt, "sssssddii", 
        $nombre_evento, 
        $fecha_evento, 
        $hora_inicio, 
        $hora_fin, 
        $ubicacion, 
        $latitud, 
        $longitud, 
        $id_evento,
        $id_organizador
    );
    
    if (mysqli_stmt_execute($stmt)) {
        
        // Actualizar invitados: eliminar los existentes y agregar los nuevos
        $sql_delete_inv = "DELETE FROM invitados WHERE id_evento = ?";
        $stmt_del = mysqli_prepare($conexion, $sql_delete_inv);
        mysqli_stmt_bind_param($stmt_del, "i", $id_evento);
        mysqli_stmt_execute($stmt_del);
        mysqli_stmt_close($stmt_del);
        
        // Insertar nuevos invitados
        $invitados = json_decode($invitados_json, true);
        if (!empty($invitados) && is_array($invitados)) {
            $sql_invitado = "INSERT INTO invitados (id_evento, correo_invitado) VALUES (?, ?)";
            $stmt_invitado = mysqli_prepare($conexion, $sql_invitado);
            
            foreach ($invitados as $correo) {
                mysqli_stmt_bind_param($stmt_invitado, "is", $id_evento, $correo);
                mysqli_stmt_execute($stmt_invitado);
            }
            
            mysqli_stmt_close($stmt_invitado);
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
        
        header('Location: ver_evento.php?id=' . $id_evento . '&mensaje=Evento actualizado exitosamente');
        exit;
        
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
        
        header('Location: editar_evento.php?id=' . $id_evento . '&error=Error al actualizar el evento');
        exit;
    }
    
} else {
    header('Location: home.php');
    exit;
}
?>