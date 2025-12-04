<?php
require_once './lib/verificar_sesion.php';
verificar_administrador();

require_once './config/conexion_db.php';

$id_usuario = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos del usuario
$sql = "SELECT * FROM clientes WHERE id_cl = ? AND tipo_usuario = 1";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) === 0) {
    header('Location: ../public/admin/panel_admin.php?error=Usuario no encontrado');
    exit;
}

$usuario = mysqli_fetch_assoc($resultado);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_cl']);
    $correo = trim($_POST['correo_cl']);
    $contrasena = trim($_POST['contrasena_cl']);
    
    if (!empty($contrasena)) {
        // Si se proporcionó nueva contraseña
        $sql_update = "UPDATE clientes SET nombre_cl = ?, correo_cl = ?, contrasena_cl = ? WHERE id_cl = ?";
        $stmt_up = mysqli_prepare($conexion, $sql_update);
        mysqli_stmt_bind_param($stmt_up, "sssi", $nombre, $correo, $contrasena, $id_usuario);
    } else {
        // Si no se cambió la contraseña
        $sql_update = "UPDATE clientes SET nombre_cl = ?, correo_cl = ? WHERE id_cl = ?";
        $stmt_up = mysqli_prepare($conexion, $sql_update);
        mysqli_stmt_bind_param($stmt_up, "ssi", $nombre, $correo, $id_usuario);
    }
    
    if (mysqli_stmt_execute($stmt_up)) {
        mysqli_close($conexion);
        header('Location: ../public/admin/panel_admin.php?mensaje=Usuario actualizado exitosamente');
        exit;
    } else {
        $error_msg = "Error al actualizar el usuario";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - Litzor</title>
    <link rel="stylesheet" href="../public/assets/css/bootstrap.css">
    <link rel="stylesheet" href="../public/assets/css/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #746de3ff 0%, #5a52d5 100%);
            min-height: 100vh;
            padding-top: 80px;
        }

        #navSection {
            background-color: #1e293b;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .logo img {
            height: 50px;
            margin-left: 2rem;
        }

        .form-container {
            background: white;
            border-radius: 25px;
            padding: 2.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-title {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 2rem;
        }

        .btn-submit {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            border: none;
            width: 100%;
        }

        .btn-cancelar {
            background: #6b7280;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            width: 100%;
        }
    </style>
</head>

<body>

    <header id="navSection">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="logo">
                        <a href="../public/index.html" class="navbar-brand">
                            <img src="../public/assets/img/logo-wout-bg.png" alt="Litzor Logo">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            
            <div class="form-container">
                <h2 class="form-title">
                    <i class="bi bi-pencil text-primary"></i>
                    Editar Usuario
                </h2>

                <?php if (isset($error_msg)): ?>
                    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                <?php endif; ?>

                <form method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre Completo *</label>
                        <input type="text" class="form-control form-control-lg" name="nombre_cl" 
                               value="<?php echo htmlspecialchars($usuario['nombre_cl']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Correo Electrónico *</label>
                        <input type="email" class="form-control form-control-lg" name="correo_cl" 
                               value="<?php echo htmlspecialchars($usuario['correo_cl']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Nueva Contraseña (dejar vacío para no cambiar)</label>
                        <input type="password" class="form-control form-control-lg" name="contrasena_cl" 
                               placeholder="Nueva contraseña">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="panel_admin.php" class="btn-cancelar">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn-submit">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </section>

    <script src="../public/assets/js/bootstrap.bundle.js"></script>

</body>
</html>
<?php mysqli_close($conexion); ?>