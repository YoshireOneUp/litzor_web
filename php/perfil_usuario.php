<?php
require_once 'verificar_sesion.php';
verificar_organizador();

require_once 'conexion_db.php';

// Obtener datos del usuario actual
$id_usuario = $_SESSION['id_cl'];
$consulta = "SELECT * FROM clientes WHERE id_cl = ?";
$stmt = mysqli_prepare($conexion, $consulta);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);

// Contar eventos del usuario
$sql_eventos = "SELECT COUNT(*) as total FROM eventos WHERE id_organizador = ? AND estado = 'activo'";
$stmt_ev = mysqli_prepare($conexion, $sql_eventos);
mysqli_stmt_bind_param($stmt_ev, "i", $id_usuario);
mysqli_stmt_execute($stmt_ev);
$result_ev = mysqli_stmt_get_result($stmt_ev);
$eventos_count = mysqli_fetch_assoc($result_ev)['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Litzor</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="shortcut icon" href="../assets/img/logo-wout-bg.png">
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

        .nav-link i {
            font-size: 2rem;
            color: white;
            transition: color 0.3s;
        }

        .nav-link:hover i {
            color: #fbbf24;
        }

        .profile-card {
            background: white;
            border-radius: 25px;
            padding: 3rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 700px;
            margin: 0 auto;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .profile-icon {
            font-size: 6rem;
            color: #746de3ff;
            margin-bottom: 1rem;
        }

        .profile-title {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .profile-subtitle {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .info-row {
            padding: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-icon {
            font-size: 1.5rem;
            color: #746de3ff;
            margin-right: 1rem;
            min-width: 40px;
            text-align: center;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-weight: bold;
            color: #6b7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: #1f2937;
            font-size: 1.1rem;
        }

        .stats-container {
            background: linear-gradient(135deg, #a7f3d0 0%, #6ee7b7 100%);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1f2937;
        }

        .stat-label {
            color: #374151;
            font-size: 1rem;
        }

        .btn-back {
            background: linear-gradient(135deg, #746de3ff 0%, #5a52d5 100%);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            margin-top: 2rem;
            transition: transform 0.2s;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>

<body>

    <header id="navSection">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="logo">
                        <a href="../index.html" class="navbar-brand">
                            <img src="../assets/img/logo-wout-bg.png" alt="Litzor Logo">
                        </a>
                    </div>
                </div>
                <div class="col-6">
                    <nav class="navbar navbar-expand-lg">
                        <div class="container-fluid">
                            <div class="navbar-collapse justify-content-end">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="home.php">
                                            <i class="bi bi-house"></i>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" href="perfil_usuario.php">
                                            <i class="bi bi-person-circle"></i>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="logout.php">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            
            <div class="profile-card">
                
                <div class="profile-header">
                    <div class="profile-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h1 class="profile-title"><?php echo htmlspecialchars($usuario['nombre_cl']); ?></h1>
                    <p class="profile-subtitle">
                        <i class="bi bi-award"></i> Organizador de Eventos
                    </p>
                </div>

                <?php if ($usuario): ?>
                    
                    <div class="info-row">
                        <div class="info-icon">
                            <i class="bi bi-hash"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">ID de Usuario</div>
                            <div class="info-value">#<?php echo htmlspecialchars($usuario['id_cl']); ?></div>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Nombre Completo</div>
                            <div class="info-value"><?php echo htmlspecialchars($usuario['nombre_cl']); ?></div>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Correo Electrónico</div>
                            <div class="info-value"><?php echo htmlspecialchars($usuario['correo_cl']); ?></div>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-icon">
                            <i class="bi bi-calendar-plus"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Miembro Desde</div>
                            <div class="info-value">
                                <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="stats-container">
                        <div class="stat-number">
                            <i class="bi bi-calendar-event"></i> <?php echo $eventos_count; ?>
                        </div>
                        <div class="stat-label">Eventos Activos</div>
                    </div>

                <?php else: ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        No se pudo cargar la información del perfil.
                    </div>
                <?php endif; ?>

                <div class="text-center">
                    <a href="home.php" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Volver al Inicio
                    </a>
                </div>
            </div>

        </div>
    </section>

    <script src="../assets/js/bootstrap.bundle.js"></script>

</body>
</html>
<?php mysqli_close($conexion); ?>