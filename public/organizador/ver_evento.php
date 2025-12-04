<?php
require_once './lib/verificar_sesion.php';
verificar_organizador();

require_once './config/conexion_db.php';

$id_evento = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_organizador = $_SESSION['id_cl'];

// Obtener datos del evento
$sql = "SELECT * FROM eventos WHERE id_evento = ? AND id_organizador = ?";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id_evento, $id_organizador);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) === 0) {
    header('Location: ../public/organizador/home.php?error=Evento no encontrado');
    exit;
}

$evento = mysqli_fetch_assoc($resultado);

// Obtener invitados
$sql_invitados = "SELECT * FROM invitados WHERE id_evento = ? ORDER BY fecha_agregado DESC";
$stmt_inv = mysqli_prepare($conexion, $sql_invitados);
mysqli_stmt_bind_param($stmt_inv, "i", $id_evento);
mysqli_stmt_execute($stmt_inv);
$invitados = mysqli_stmt_get_result($stmt_inv);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($evento['nombre_evento']); ?> - Litzor</title>
    <link rel="stylesheet" href="../public/assets/css/bootstrap.css">
    <link rel="stylesheet" href="../public/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/assets/css/styles.css">
    <link rel="shortcut icon" href="../public/assets/img/logo-wout-bg.png">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
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
        }

        .detalle-container {
            background: white;
            border-radius: 25px;
            padding: 2.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .evento-header {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .evento-titulo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .codigo-grande {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 15px;
            font-family: monospace;
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 3px;
            display: inline-block;
        }

        .info-section {
            margin-bottom: 2rem;
        }

        .info-label {
            font-weight: bold;
            color: #6b7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .info-valor {
            color: #1f2937;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .info-valor i {
            color: #746de3ff;
            margin-right: 0.5rem;
        }

        #mapDetalle {
            height: 400px;
            width: 100%;
            border-radius: 15px;
            margin-top: 1rem;
            z-index: 1;
        }

        .invitado-badge {
            background: #f3f4f6;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            margin: 0.3rem;
            display: inline-block;
        }

        .btn-acciones {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #e5e7eb;
        }

        .btn-custom {
            padding: 0.75rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
            font-weight: 500;
        }

        .btn-editar {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .btn-eliminar {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .btn-volver {
            background: #6b7280;
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
                        <a href="../public/index.html" class="navbar-brand">
                            <img src="../public/assets/img/logo-wout-bg.png" alt="Litzor Logo">
                        </a>
                    </div>
                </div>
                <div class="col-6">
                    <nav class="navbar navbar-expand-lg">
                        <div class="container-fluid">
                            <div class="navbar-collapse justify-content-end">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="../public/organizador/home.php">
                                            <i class="bi bi-arrow-left"></i>
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
            
            <div class="detalle-container">
                
                <div class="evento-header">
                    <h1 class="evento-titulo"><?php echo htmlspecialchars($evento['nombre_evento']); ?></h1>
                    <div class="codigo-grande"><?php echo htmlspecialchars($evento['codigo_evento']); ?></div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-section">
                            <div class="info-label">Fecha</div>
                            <div class="info-valor">
                                <i class="bi bi-calendar3"></i>
                                <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-section">
                            <div class="info-label">Horario</div>
                            <div class="info-valor">
                                <i class="bi bi-clock"></i>
                                <?php echo date('H:i', strtotime($evento['hora_inicio'])); ?> - 
                                <?php echo date('H:i', strtotime($evento['hora_fin'])); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-label">Ubicación</div>
                    <div class="info-valor">
                        <i class="bi bi-geo-alt-fill"></i>
                        <?php echo htmlspecialchars($evento['ubicacion']); ?>
                    </div>
                    <div id="mapDetalle"></div>
                </div>

                <div class="info-section">
                    <div class="info-label">Invitados (<?php echo mysqli_num_rows($invitados); ?>)</div>
                    <div class="mt-2">
                        <?php if (mysqli_num_rows($invitados) > 0): ?>
                            <?php while($invitado = mysqli_fetch_assoc($invitados)): ?>
                                <span class="invitado-badge">
                                    <i class="bi bi-person"></i>
                                    <?php echo htmlspecialchars($invitado['correo_invitado']); ?>
                                </span>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted">No hay invitados registrados</p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($evento['estado'] === 'activo'): ?>
                <div class="btn-acciones text-center">
                    <a href="../public/organizador/home.php" class="btn-custom btn-volver">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <a href="../public/organizador/editar_evento.php?id=<?php echo $evento['id_evento']; ?>" class="btn-custom btn-editar">
                        <i class="bi bi-pencil"></i> Editar Evento
                    </a>
                    <a href="../public/organizador/eliminar_evento.php?id=<?php echo $evento['id_evento']; ?>" 
                       class="btn-custom btn-eliminar"
                       onclick="return confirm('¿Estás seguro de eliminar este evento?')">
                        <i class="bi bi-trash"></i> Eliminar
                    </a>
                </div>
                <?php else: ?>
                <div class="btn-acciones text-center">
                    <a href="../public/organizador/home.php" class="btn-custom btn-volver">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <span class="badge bg-secondary p-3" style="font-size: 1.1rem;">
                        <i class="bi bi-archive"></i> Evento Finalizado
                    </span>
                </div>
                <?php endif; ?>

            </div>

        </div>
    </section>

    <script src="../public/assets/js/bootstrap.bundle.js"></script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Inicializar mapa de detalle (solo lectura)
        function initMapDetalle() {
            try {
                const lat = <?php echo $evento['latitud'] ?? 20.5888; ?>;
                const lng = <?php echo $evento['longitud'] ?? -100.3899; ?>;
                const ubicacion = [lat, lng];
                
                // Crear el mapa
                const map = L.map('mapDetalle').setView(ubicacion, 15);
                
                console.log('Mapa de detalle inicializado correctamente');
                
                // Agregar capa de OpenStreetMap
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);
                
                // Crear marcador (no arrastrable)
                L.marker(ubicacion, {
                    draggable: false
                }).addTo(map)
                .bindPopup("<?php echo htmlspecialchars($evento['nombre_evento']); ?>")
                .openPopup();
                
            } catch (error) {
                console.error('Error al inicializar el mapa:', error);
                document.getElementById('mapDetalle').innerHTML = '<div style="padding: 2rem; text-align: center; color: #ef4444;">Error al cargar el mapa.</div>';
            }
        }
        
        // Inicializar cuando cargue la página
        window.addEventListener('load', initMapDetalle);
    </script>

</body>
</html>
<?php mysqli_close($conexion); ?>