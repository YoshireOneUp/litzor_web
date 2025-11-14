<?php
require_once 'verificar_sesion.php';
verificar_administrador();

require_once 'conexion_db.php';

// Obtener datos del administrador actual
$id_admin = $_SESSION['id_cl'];
$consulta = "SELECT * FROM clientes WHERE id_cl = ?";
$stmt = mysqli_prepare($conexion, $consulta);
mysqli_stmt_bind_param($stmt, "i", $id_admin);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($resultado);

// Estadísticas generales
$result_usuarios = mysqli_query($conexion, "SELECT COUNT(*) as total FROM clientes WHERE tipo_usuario = 1");
$total_usuarios = mysqli_fetch_assoc($result_usuarios)['total'];

$result_eventos = mysqli_query($conexion, "SELECT COUNT(*) as total FROM eventos");
$total_eventos = mysqli_fetch_assoc($result_eventos)['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Administrador - Litzor</title>
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
            color: #ef4444;
            margin-bottom: 1rem;
        }

        .profile-title {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .profile-subtitle {
            color: #ef4444;
            font-size: 1.1rem;
            font-weight: 600;
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
            color: #ef4444;
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

        .admin-badge {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            display: inline-block;
            margin-top: 1rem;
            font-weight: 600;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .stat-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1f2937;
        }

        .stat-label {
            color: #374151;
            font-size: 0.9rem;
            margin-top: 0.5rem;
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
                                        <a class="nav-link" href="panel_admin.php">
                                            <i class="bi bi-speedometer2"></i>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" href="perfil_admin.php">
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
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                    <h1 class="profile-title"><?php echo htmlspecialchars($admin['nombre_cl']); ?></h1>
                    <p class="profile-subtitle">
                        <i class="bi bi-star-fill"></i> ADMINISTRADOR
                    </p>
                    <div class="admin-badge">
                        <i class="bi bi-shield-lock-fill"></i> Acceso Total al Sistema
                    </div>
                </div>

                <?php if ($admin): ?>
                    
                    <div class="info-row">
                        <div class="info-icon">
                            <i class="bi bi-hash"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">ID de Administrador</div>
                            <div class="info-value">#<?php echo htmlspecialchars($admin['id_cl']); ?></div>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Nombre Completo</div>
                            <div class="info-value"><?php echo htmlspecialchars($admin['nombre_cl']); ?></div>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Correo Electrónico</div>
                            <div class="info-value"><?php echo htmlspecialchars($admin['correo_cl']); ?></div>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-icon">
                            <i class="bi bi-calendar-plus"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Administrador Desde</div>
                            <div class="info-value">
                                <?php echo date('d/m/Y', strtotime($admin['fecha_registro'])); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas del sistema -->
                    <div class="stats-row">
                        <div class="stat-box">
                            <div class="stat-number">
                                <i class="bi bi-people"></i> <?php echo $total_usuarios; ?>
                            </div>
                            <div class="stat-label">Usuarios Activos</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number">
                                <i class="bi bi-calendar-event"></i> <?php echo $total_eventos; ?>
                            </div>
                            <div class="stat-label">Eventos Totales</div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        No se pudo cargar la información del perfil.
                    </div>
                <?php endif; ?>

                <div class="text-center">
                    <a href="panel_admin.php" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Volver al Panel
                    </a>
                </div>
            </div>

        </div>
    </section>

    <script src="../assets/js/bootstrap.bundle.js"></script>

</body>
</html>
<?php mysqli_close($conexion); ?>