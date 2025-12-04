<?php
session_start();

// Si ya tiene sesión activa, redirigir
if (isset($_SESSION['id_cl']) && isset($_SESSION['tipo_usuario'])) {
    if ($_SESSION['tipo_usuario'] == 1) {
        header('Location: ../public/organizador/home.php');
        exit;
    } elseif ($_SESSION['tipo_usuario'] == 2) {
        header('Location: ../public/admin/panel_admin.php');
        exit;
    }
}

$mensaje_error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Litzor</title>
    <link href="../public/assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/assets/css/styles.css">
    <link rel="shortcut icon" href="../public/assets/img/logo-wout-bg.png">
    <style>
        body {
            background: linear-gradient(135deg, #746de3ff 0%, #5a52d5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-container {
            width: 100%;
            max-width: 500px;
        }

        .register-card {
            background: white;
            border-radius: 25px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .logo-register {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-register img {
            height: 80px;
        }

        .register-title {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .register-subtitle {
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

        .btn-register {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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

        .btn-register:hover {
            transform: translateY(-2px);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #6b7280;
        }

        .login-link a {
            color: #746de3ff;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
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

        .password-requirements {
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            
            <div class="logo-register">
                <img src="../public/assets/img/logo-wout-bg.png" alt="Litzor Logo">
            </div>

            <h1 class="register-title">Crear Cuenta</h1>
            <p class="register-subtitle">Regístrate como organizador</p>

            <?php if ($mensaje_error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?php 
                        if ($mensaje_error == '1') {
                            echo 'El correo ya está registrado';
                        } elseif ($mensaje_error == '2') {
                            echo 'Error al crear la cuenta. Intenta nuevamente';
                        } else {
                            echo htmlspecialchars($mensaje_error);
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form action="../lib/guardar_registro.php" method="post" id="formRegistro">
                
                <div class="mb-3">
                    <label for="nombre_cl" class="form-label">
                        <i class="bi bi-person"></i> Nombre completo
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="nombre_cl"
                        name="nombre_cl" 
                        placeholder="Juan Pérez García"
                        required
                        minlength="3"
                        autocomplete="name"
                    >
                </div>
                
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
                        minlength="6"
                        autocomplete="new-password"
                    >
                    <div class="password-requirements">
                        <i class="bi bi-info-circle"></i> Mínimo 6 caracteres
                    </div>
                </div>

                <div class="mb-3">
                    <label for="contrasena_confirm" class="form-label">
                        <i class="bi bi-lock-fill"></i> Confirmar contraseña
                    </label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="contrasena_confirm"
                        name="contrasena_confirm" 
                        placeholder="••••••••"
                        required
                        minlength="6"
                        autocomplete="new-password"
                    >
                </div>
                
                <button type="submit" class="btn-register">
                    <i class="bi bi-person-plus"></i> Registrarse
                </button>
                
                <div class="login-link">
                    ¿Ya tienes cuenta? <a href="../public/login.php">Inicia sesión aquí</a>
                </div>

                <div class="back-link">
                    <a href="../public/index.html">
                        <i class="bi bi-arrow-left"></i> Volver al inicio
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="../public/assets/js/bootstrap.bundle.js"></script>
    <script>
        // Validar que las contraseñas coincidan
        document.getElementById('formRegistro').addEventListener('submit', function(e) {
            const password = document.getElementById('contrasena_cl').value;
            const confirmPassword = document.getElementById('contrasena_confirm').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
        });
    </script>
</body>
</html>