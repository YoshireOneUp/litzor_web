<?php
// Archivo: php/validar.php - Sistema de autenticación con redirección automática

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['correo_cl'], $_POST['contrasena_cl'])) {
        
        session_start();
        require_once './config/conexion_db.php';

        $correo = trim($_POST['correo_cl']);
        $contrasena = trim($_POST['contrasena_cl']);

        // Consulta preparada para prevenir SQL injection
        $sql = "SELECT id_cl, nombre_cl, correo_cl, contrasena_cl, tipo_usuario 
                FROM clientes 
                WHERE correo_cl = ?";
        
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "s", $correo);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        if ($resultado && mysqli_num_rows($resultado) === 1) {
            
            $usuario = mysqli_fetch_assoc($resultado);

            // Validar contraseña (en producción deberías usar password_hash y password_verify)
            if ($usuario['contrasena_cl'] === $contrasena) {
                
                // Guardar datos en sesión
                $_SESSION['id_cl'] = $usuario['id_cl'];
                $_SESSION['nombre_cl'] = $usuario['nombre_cl'];
                $_SESSION['correo_cl'] = $usuario['correo_cl'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
                
                // Redirección según tipo de usuario
                if ($usuario['tipo_usuario'] == 1) {
                    // Organizador → home.php
                    header('Location: ../public/organizador/home.php');
                    exit;
                } 
                elseif ($usuario['tipo_usuario'] == 2) {
                    // Administrador → panel_admin.php
                    header('Location: panel_admin.php');
                    exit;
                } 
                else {
                    // Usuario no reconocido
                    header('Location: ../public/login.php?error=' . urlencode('Usuario no reconocido'));
                    exit;
                }
                
            } else {
                header('Location: ../public/login.php?error=' . urlencode('Credenciales incorrectas'));
                exit;
            }
            
        } else {
            header('Location: ../public/login.php?error=' . urlencode('Correo no registrado'));
            exit;
        }

        mysqli_close($conexion);
        
    } else {
        header('Location: ../public/login.php?error=' . urlencode('Completa el formulario'));
        exit;
    }
    
} else {
    header('Location: ../public/login.php?error=' . urlencode('Acceso no permitido'));
    exit;
}
?>