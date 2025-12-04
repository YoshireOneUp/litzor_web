<?php
require_once __DIR__ . '../../../lib/verificar_sesion.php';
verificar_administrador(); 

require_once __DIR__ . '../../../config/conexion_db.php';

$id_organizador = $_SESSION['id_cl'];
$nombre_usuario = $_SESSION['nombre_cl'] ?? 'Usuario';

// Obtener eventos activos del organizador
$sql_activos = "SELECT * FROM eventos 
                WHERE id_organizador = ? AND estado = 'activo' 
                ORDER BY fecha_evento ASC, hora_inicio ASC";
$stmt = mysqli_prepare($conexion, $sql_activos);
mysqli_stmt_bind_param($stmt, "i", $id_organizador);
mysqli_stmt_execute($stmt);
$eventos_activos = mysqli_stmt_get_result($stmt);

// Obtener eventos finalizados (historial)
$sql_historial = "SELECT * FROM eventos 
                  WHERE id_organizador = ? AND estado = 'finalizado' 
                  ORDER BY fecha_evento DESC";
$stmt2 = mysqli_prepare($conexion, $sql_historial);
mysqli_stmt_bind_param($stmt2, "i", $id_organizador);
mysqli_stmt_execute($stmt2);
$eventos_historial = mysqli_stmt_get_result($stmt2);

// Actualizar automáticamente eventos pasados
$sql_update = "UPDATE eventos 
               SET estado = 'finalizado' 
               WHERE fecha_evento < CURDATE() AND estado = 'activo'";
mysqli_query($conexion, $sql_update);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Eventos - Litzor</title>
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

        .section-title {
            color: white;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 2rem;
        }

        .btn-crear-evento {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.2s;
            border: none;
        }

        .btn-crear-evento:hover {
            transform: translateY(-2px);
            color: white;
        }

        .evento-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            cursor: pointer;
            position: relative;
        }

        .evento-card:hover {
            transform: translateY(-5px);
        }

        .evento-titulo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .evento-fecha {
            color: #6b7280;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .evento-codigo {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
            font-weight: bold;
            font-family: monospace;
            font-size: 0.9rem;
        }

        .evento-opciones {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }

        .btn-opciones {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6b7280;
            cursor: pointer;
            padding: 0.5rem;
        }

        .dropdown-menu-custom {
            position: absolute;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            display: none;
            min-width: 150px;
            z-index: 100;
        }

        .dropdown-menu-custom.show {
            display: block;
        }

        .dropdown-item-custom {
            padding: 0.75rem 1rem;
            color: #374151;
            text-decoration: none;
            display: block;
            transition: background 0.2s;
        }

        .dropdown-item-custom:hover {
            background: #f3f4f6;
        }

        .dropdown-item-custom.danger:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .empty-state {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
        }

        .empty-icon {
            font-size: 4rem;
            color: #9ca3af;
        }

        .tab-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .tab-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .tab-btn.active {
            background: white;
            color: #746de3ff;
            font-weight: bold;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
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
                            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#movileNav">
                                <i class="bi bi-list text-white fs-1"></i>
                            </button>

                            <div class="navbar-collapse justify-content-end collapse" id="movileNav">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="../organizador/perfil_usuario.php">
                                            <i class="bi bi-person-circle"></i>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="../logout.php">
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
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="section-title mb-0">¡Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?>!</h1>
                <a href="../organizador/crear_evento.php" class="btn-crear-evento">
                    <i class="bi bi-plus-circle"></i>
                    Crear Evento
                </a>
            </div>

            <!-- Tabs -->
            <div class="tab-buttons">
                <button class="tab-btn active" onclick="cambiarTab('activos')">
                    <i class="bi bi-calendar-check"></i> Eventos Activos
                </button>
                <button class="tab-btn" onclick="cambiarTab('historial')">
                    <i class="bi bi-clock-history"></i> Historial
                </button>
            </div>

            <!-- Contenido Tab: Eventos Activos -->
            <div id="tab-activos" class="tab-content active">
                <?php if (mysqli_num_rows($eventos_activos) > 0): ?>
                    <?php while($evento = mysqli_fetch_assoc($eventos_activos)): ?>
                        <div class="evento-card" onclick="verEvento(<?php echo $evento['id_evento']; ?>)">
                            
                            <div class="evento-opciones">
                                <button class="btn-opciones" onclick="event.stopPropagation(); toggleDropdown(<?php echo $evento['id_evento']; ?>)">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <div id="dropdown-<?php echo $evento['id_evento']; ?>" class="dropdown-menu-custom">
                                    <a href="../organizador/editar_evento.php?id=<?php echo $evento['id_evento']; ?>" class="dropdown-item-custom">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <a href="../organizador/eliminar_evento.php?id=<?php echo $evento['id_evento']; ?>" 
                                       class="dropdown-item-custom danger"
                                       onclick="return confirm('¿Estás seguro de eliminar este evento?')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </a>
                                </div>
                            </div>

                            <h3 class="evento-titulo"><?php echo htmlspecialchars($evento['nombre_evento']); ?></h3>
                            <p class="evento-fecha">
                                <i class="bi bi-calendar3"></i> 
                                <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?> 
                                | 
                                <i class="bi bi-clock"></i> 
                                <?php echo date('H:i', strtotime($evento['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($evento['hora_fin'])); ?>
                            </p>
                            <?php if ($evento['ubicacion']): ?>
                                <p class="evento-fecha">
                                    <i class="bi bi-geo-alt"></i> 
                                    <?php echo htmlspecialchars($evento['ubicacion']); ?>
                                </p>
                            <?php endif; ?>
                            <span class="evento-codigo"><?php echo htmlspecialchars($evento['codigo_evento']); ?></span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <p class="mt-3 mb-0">No tienes eventos activos</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Contenido Tab: Historial -->
            <div id="tab-historial" class="tab-content">
                <?php if (mysqli_num_rows($eventos_historial) > 0): ?>
                    <?php while($evento = mysqli_fetch_assoc($eventos_historial)): ?>
                        <div class="evento-card" onclick="verEvento(<?php echo $evento['id_evento']; ?>)">
                            
                            <div class="evento-opciones">
                                <button class="btn-opciones" onclick="event.stopPropagation(); toggleDropdown(<?php echo $evento['id_evento']; ?>)">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <div id="dropdown-<?php echo $evento['id_evento']; ?>" class="dropdown-menu-custom">
                                    <a href="../organizador/eliminar_evento.php?id=<?php echo $evento['id_evento']; ?>" 
                                       class="dropdown-item-custom danger"
                                       onclick="return confirm('¿Estás seguro de eliminar este evento del historial?')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </a>
                                </div>
                            </div>

                            <h3 class="evento-titulo"><?php echo htmlspecialchars($evento['nombre_evento']); ?></h3>
                            <p class="evento-fecha">
                                <i class="bi bi-calendar3"></i> 
                                <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?> 
                                | 
                                <i class="bi bi-clock"></i> 
                                <?php echo date('H:i', strtotime($evento['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($evento['hora_fin'])); ?>
                            </p>
                            <span class="badge bg-secondary">Finalizado</span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-archive"></i>
                        </div>
                        <p class="mt-3 mb-0">No tienes eventos en el historial</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </section>

    <script src="../assets/js/bootstrap.bundle.js"></script>
    <script>
        function cambiarTab(tab) {
            // Ocultar todos los tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Mostrar tab seleccionado
            document.getElementById('tab-' + tab).classList.add('active');
            event.target.classList.add('active');
        }

        function toggleDropdown(id) {
            const dropdown = document.getElementById('dropdown-' + id);
            // Cerrar otros dropdowns
            document.querySelectorAll('.dropdown-menu-custom').forEach(d => {
                if (d.id !== 'dropdown-' + id) {
                    d.classList.remove('show');
                }
            });
            dropdown.classList.toggle('show');
        }

        function verEvento(id) {
            window.location.href = '../organizador/ver_evento.php?id=' + id;
        }

        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown-menu-custom').forEach(d => {
                d.classList.remove('show');
            });
        });
    </script>

</body>
</html>
<?php mysqli_close($conexion); ?>