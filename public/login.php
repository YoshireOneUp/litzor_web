<?php
session_start();

// Si ya tiene sesión activa, redirigir según tipo de usuario
if (isset($_SESSION['id_cl']) && isset($_SESSION['tipo_usuario'])) {
    if ($_SESSION['tipo_usuario'] == 1) {
        header('Location: ../organizador/home.php');
        exit;
    } elseif ($_SESSION['tipo_usuario'] == 2) {
        header('Location: ../admin/panel_admin.php');
        exit;
    }
}

$mensaje = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje_exito = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Litzor</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="shortcut icon" href="../assets/img/logo-wout-bg.png">
    <style>
        body {
            background: linear-gradient(135deg, #746de3ff 0%, #5a52d5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
        }

        .login-card {
            background: white;
            border-radius: 25px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .logo-login {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-login img {
            height: 80px;
        }

        .login-title {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #746de3ff;
            box-shadow: 0 0 0 3px rgba(116, 109, 227, 0.1);
        }

        .btn-login {
            background: linear-gradient(135deg, #746de3ff 0%, #5a52d5 100%);
            color: white;
            padding: 0.75rem;
            border-radius: 10px;
            border: none;
            width: 100%;
            font-size: 1.1rem;
            font-weight: 600;
            transition: transform 0.2s;
            margin-top: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #6b7280;
        }

        .register-link a {
            color: #746de3ff;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .back-link {
            text-align: center;
            margin-top: 1rem;
        }

        .back-link a {
            color: #6b7280;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link a:hover {
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            
            <div class="logo-login">
                <img src="../assets/img/logo-wout-bg.png" alt="Litzor Logo">
            </div>

            <h1 class="login-title">¡Bienvenido!</h1>
            <p class="login-subtitle">Inicia sesión en tu cuenta</p>

            <?php if ($mensaje): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($mensaje_exito): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <?php echo htmlspecialchars($mensaje_exito); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form action="../lib/validar.php" method="post">
                
                <div class="mb-3">
                    <label for="correo_cl" class="form-label">
                        <i class="bi bi-envelope"></i> Correo electrónico
                    </label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="correo_cl"
                        name="correo_cl" 
                        placeholder="ejemplo@correo.com"
                        required
                        autocomplete="email"
                    >
                </div>
                
                <div class="mb-3">
                    <label for="contrasena_cl" class="form-label">
                        <i class="bi bi-lock"></i> Contraseña
                    </label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="contrasena_cl"
                        name="contrasena_cl" 
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Ingresar
                </button>
                
                <div class="register-link">
                    ¿No tienes cuenta? <a href="register.php">Créala aquí</a>
                </div>

                <div class="back-link">
                    <a href="../public/index.html">
                        <i class="bi bi-arrow-left"></i> Volver al inicio
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.js"></script>
</body>
</html>