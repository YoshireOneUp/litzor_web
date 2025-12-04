<?php
require_once __DIR__ . '../../../lib/verificar_sesion.php';
verificar_administrador(); // Solo administradores

require_once __DIR__ . '../../../config/conexion_db.php';

// ===== ESTADÍSTICAS =====
// Total de usuarios (organizadores)
$result_usuarios = mysqli_query($conexion, "SELECT COUNT(*) as total FROM clientes WHERE tipo_usuario = 1");
$row_usuarios = mysqli_fetch_assoc($result_usuarios);
$total_usuarios = $row_usuarios['total'];

// Total de eventos
$result_eventos = mysqli_query($conexion, "SELECT COUNT(*) as total FROM eventos");
$row_eventos = mysqli_fetch_assoc($result_eventos);
$total_eventos = $row_eventos['total'];

// Total de asistentes
$result_asistentes = mysqli_query($conexion, "SELECT COUNT(*) as total FROM invitados");
$row_asistentes = mysqli_fetch_assoc($result_asistentes);
$total_asistentes = $row_asistentes['total'];

// ===== GRÁFICA CON DATOS REALES =====
// Eventos creados por día (últimos 30 días)
$query_grafica = "SELECT DATE(fecha_creacion) as fecha, COUNT(*) as total_eventos
                  FROM eventos
                  WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  GROUP BY DATE(fecha_creacion)
                  ORDER BY fecha";
$result_grafica = mysqli_query($conexion, $query_grafica);

// Asistentes agregados por día
$query_asistentes_dia = "SELECT DATE(fecha_agregado) as fecha, COUNT(*) as total_asistentes
                         FROM invitados
                         WHERE fecha_agregado >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                         GROUP BY DATE(fecha_agregado)
                         ORDER BY fecha";
$result_asistentes_dia = mysqli_query($conexion, $query_asistentes_dia);

// Construir arrays para la gráfica
$labels = [];
$eventosData = [];
$asistentesData = [];

// Crear array con todos los días de los últimos 30 días
$dias = [];
for ($i = 29; $i >= 0; $i--) {
    $fecha = date('Y-m-d', strtotime("-$i days"));
    $dias[$fecha] = [
        'label' => date('d/m', strtotime($fecha)),
        'eventos' => 0,
        'asistentes' => 0
    ];
}

// Llenar con datos reales de eventos
while ($row = mysqli_fetch_assoc($result_grafica)) {
    if (isset($dias[$row['fecha']])) {
        $dias[$row['fecha']]['eventos'] = $row['total_eventos'];
    }
}

// Llenar con datos reales de asistentes
while ($row = mysqli_fetch_assoc($result_asistentes_dia)) {
    if (isset($dias[$row['fecha']])) {
        $dias[$row['fecha']]['asistentes'] = $row['total_asistentes'];
    }
}

// Convertir a formato para Chart.js
foreach ($dias as $dia) {
    $labels[] = $dia['label'];
    $eventosData[] = $dia['eventos'];
    $asistentesData[] = $dia['asistentes'];
}

$labelsJSON = json_encode($labels);
$eventosJSON = json_encode($eventosData);
$asistentesJSON = json_encode($asistentesData);

// ===== LISTA DE USUARIOS =====
$sql_usuarios = "SELECT * FROM clientes WHERE tipo_usuario = 1 ORDER BY fecha_registro DESC";
$usuarios = mysqli_query($conexion, $sql_usuarios);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - Litzor</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
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

        .stat-card {
            background: linear-gradient(135deg, #a7f3d0 0%, #6ee7b7 100%);
            border-radius: 25px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: bold;
            color: #1f2937;
        }

        .stat-label {
            font-size: 1.2rem;
            color: #374151;
        }

        .chart-container, .table-container {
            background: white;
            border-radius: 25px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 2rem;
        }

        .tab-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .tab-btn {
            background: rgba(116, 109, 227, 0.2);
            color: #1f2937;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #746de3ff 0%, #5a52d5 100%);
            color: white;
            font-weight: bold;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .usuario-row {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .usuario-info {
            flex: 1;
        }

        .usuario-nombre {
            font-weight: bold;
            color: #1f2937;
            font-size: 1.1rem;
        }

        .usuario-correo {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .btn-accion {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            margin-left: 0.5rem;
            transition: transform 0.2s;
        }

        .btn-accion:hover {
            transform: scale(1.05);
        }

        .btn-editar {
            background: #3b82f6;
            color: white;
        }

        .btn-eliminar {
            background: #ef4444;
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
                                        <a class="nav-link" href="perfil_admin.php">
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
            
            <h1 class="text-white fw-bold mb-4">Panel de Administrador</h1>

            <!-- Tabs -->
            <div class="tab-buttons">
                <button class="tab-btn active" onclick="cambiarTab('estadisticas')">
                    <i class="bi bi-graph-up"></i> Estadísticas
                </button>
                <button class="tab-btn" onclick="cambiarTab('usuarios')">
                    <i class="bi bi-people"></i> Gestionar Usuarios
                </button>
            </div>

            <!-- TAB: ESTADÍSTICAS -->
            <div id="tab-estadisticas" class="tab-content active">
                
                <!-- Tarjetas de estadísticas -->
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="stat-label">Usuarios Registrados:</span>
                                    <div class="stat-number"><?php echo $total_usuarios; ?></div>
                                </div>
                                <i class="bi bi-people-fill" style="font-size: 4rem; color: #059669;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="stat-label">Eventos Creados:</span>
                                    <div class="stat-number"><?php echo $total_eventos; ?></div>
                                </div>
                                <i class="bi bi-calendar-event" style="font-size: 4rem; color: #059669;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="stat-label">Total Asistentes:</span>
                                    <div class="stat-number"><?php echo $total_asistentes; ?></div>
                                </div>
                                <i class="bi bi-person-check-fill" style="font-size: 4rem; color: #059669;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfica -->
                <div class="chart-container">
                    <h2 class="section-title">Actividad de los Últimos 30 Días</h2>
                    <canvas id="chartEventos"></canvas>
                </div>

            </div>

            <!-- TAB: GESTIONAR USUARIOS -->
            <div id="tab-usuarios" class="tab-content">
                
                <div class="table-container">
                    <h2 class="section-title">Lista de Usuarios (Organizadores)</h2>
                    
                    <?php if (mysqli_num_rows($usuarios) > 0): ?>
                        <?php while($usuario = mysqli_fetch_assoc($usuarios)): ?>
                            <div class="usuario-row">
                                <div class="usuario-info">
                                    <div class="usuario-nombre">
                                        <i class="bi bi-person-circle"></i>
                                        <?php echo htmlspecialchars($usuario['nombre_cl']); ?>
                                    </div>
                                    <div class="usuario-correo">
                                        <i class="bi bi-envelope"></i>
                                        <?php echo htmlspecialchars($usuario['correo_cl']); ?>
                                    </div>
                                    <small class="text-muted">
                                        Registrado: <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?>
                                    </small>
                                </div>
                                <div>
                                    <button class="btn-accion btn-editar" onclick="editarUsuario(<?php echo $usuario['id_cl']; ?>)">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>
                                    <button class="btn-accion btn-eliminar" 
                                            onclick="eliminarUsuario(<?php echo $usuario['id_cl']; ?>, '<?php echo htmlspecialchars($usuario['nombre_cl']); ?>')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">No hay usuarios registrados</p>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </section>

    <script src="../assets/js/bootstrap.bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Cambiar tabs
        function cambiarTab(tab) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            document.getElementById('tab-' + tab).classList.add('active');
            event.target.classList.add('active');
        }

        // Gráfica con datos reales
        const labels = <?php echo $labelsJSON; ?>;
        const eventosData = <?php echo $eventosJSON; ?>;
        const asistentesData = <?php echo $asistentesJSON; ?>;

        const ctx = document.getElementById('chartEventos').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Eventos Creados',
                    data: eventosData,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    yAxisID: 'y'
                }, {
                    label: 'Asistentes Agregados',
                    data: asistentesData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Eventos'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Asistentes'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

        // Funciones para gestionar usuarios
        function editarUsuario(id) {
            window.location.href = 'editar_usuario.php?id=' + id;
        }

        function eliminarUsuario(id, nombre) {
            if (confirm(`¿Estás seguro de eliminar al usuario "${nombre}"?\n\nEsta acción también eliminará todos sus eventos e invitados.`)) {
                window.location.href = '../public/admin/eliminar_usuario.php?id=' + id;
            }
        }
    </script>

</body>
</html>
<?php mysqli_close($conexion); ?>