<?php

require_once __DIR__ . '/verificar_sesion.php'; 

// 2. Proteger el script asegurando que sea Organizador (tipo 1)
verificar_organizador();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    require_once __DIR__ . '../../config/conexion_db.php';
    
    $id_organizador = $_SESSION['id_cl'];
    $codigo_evento = trim($_POST['codigo_evento']);
    $nombre_evento = trim($_POST['nombre_evento']);
    $fecha_evento = $_POST['fecha_evento'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $ubicacion = trim($_POST['ubicacion']);
    $latitud = floatval($_POST['latitud']);
    $longitud = floatval($_POST['longitud']);
    $invitados_json = $_POST['invitados'];
    
    // Generar código QR usando API de QuickChart
    $codigo_qr_url = "https://quickchart.io/qr?text=" . urlencode($codigo_evento) . "&size=300";
    $codigo_qr_data = file_get_contents($codigo_qr_url);
    $codigo_qr_base64 = base64_encode($codigo_qr_data);
    
    // Insertar evento
    $sql = "INSERT INTO eventos (codigo_evento, codigo_qr, nombre_evento, fecha_evento, hora_inicio, hora_fin, ubicacion, latitud, longitud, id_organizador, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo')";
    
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssddi", 
        $codigo_evento, 
        $codigo_qr_base64, 
        $nombre_evento, 
        $fecha_evento, 
        $hora_inicio, 
        $hora_fin, 
        $ubicacion, 
        $latitud, 
        $longitud, 
        $id_organizador
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $id_evento = mysqli_insert_id($conexion);
        
        // Insertar invitados si hay
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
        
        // Redirigir a home con mensaje de éxito
        header('Location: ../public/organizador/home.php?mensaje=Evento creado exitosamente');
        exit;
        
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
        
        header('Location: ../public/organizador/crear_evento.php?error=Error al crear el evento');
        exit;
    }
    
} else {
    header('Location: ../public/organizador/home.php');
    exit;
}
?>