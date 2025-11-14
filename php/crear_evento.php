<?php
require_once 'verificar_sesion.php';
verificar_organizador();

// Generar código alfanumérico único
function generarCodigoEvento($conexion) {
    do {
        $codigo = '';
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < 8; $i++) {
            $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        
        // Verificar que sea único
        $stmt = mysqli_prepare($conexion, "SELECT id_evento FROM eventos WHERE codigo_evento = ?");
        mysqli_stmt_bind_param($stmt, "s", $codigo);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $existe = mysqli_num_rows($resultado) > 0;
        mysqli_stmt_close($stmt);
        
    } while ($existe);
    
    return $codigo;
}

require_once 'conexion_db.php';
$codigo_generado = generarCodigoEvento($conexion);
$nombre_usuario = $_SESSION['nombre_cl'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Evento - Litzor</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="shortcut icon" href="../assets/img/logo-wout-bg.png">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Leaflet Control Geocoder CSS (para búsqueda de direcciones) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.css" />
    
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

        .form-container {
            background: white;
            border-radius: 25px;
            padding: 2.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .form-title {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 2rem;
        }

        .codigo-badge {
            position: fixed;
            top: 100px;
            right: 20px;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 999;
        }

        .codigo-label {
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }

        .codigo-valor {
            font-family: monospace;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 2px;
        }

        #map {
            height: 400px;
            width: 100%;
            border-radius: 15px;
            margin-top: 1rem;
            z-index: 1;
        }

        .invitado-item {
            background: #f3f4f6;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-eliminar-invitado {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 0.3rem 0.6rem;
            cursor: pointer;
        }

        .btn-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            border: none;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 1.5rem;
        }

        .btn-cancelar {
            background: #6b7280;
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .search-info {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 0.75rem 1rem;
            border-radius: 5px;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #1e40af;
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

    <!-- Badge con código del evento -->
    <div class="codigo-badge">
        <div class="codigo-label">Código del Evento:</div>
        <div class="codigo-valor"><?php echo $codigo_generado; ?></div>
    </div>

    <section class="py-5">
        <div class="container">
            
            <div class="form-container">
                <h2 class="form-title">
                    <i class="bi bi-plus-circle text-success"></i>
                    Crear Nuevo Evento
                </h2>

                <form action="procesar_crear_evento.php" method="POST" id="formEvento">
                    
                    <input type="hidden" name="codigo_evento" value="<?php echo $codigo_generado; ?>">
                    
                    <!-- Nombre del Evento -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre del Evento *</label>
                        <input type="text" class="form-control form-control-lg" name="nombre_evento" required 
                               placeholder="Ej: Conferencia Anual 2025">
                    </div>

                    <!-- Fecha -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Fecha del Evento *</label>
                        <input type="date" class="form-control form-control-lg" name="fecha_evento" required
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <!-- Hora de Inicio y Fin -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hora de Inicio *</label>
                            <input type="time" class="form-control form-control-lg" name="hora_inicio" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hora de Fin *</label>
                            <input type="time" class="form-control form-control-lg" name="hora_fin" required>
                        </div>
                    </div>

                    <!-- Ubicación con Leaflet -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Ubicación *</label>
                        <input type="text" class="form-control form-control-lg" id="ubicacion" name="ubicacion" 
                               placeholder="Escribe la dirección y presiona Enter para buscar" required>
                        <div class="search-info">
                            <i class="bi bi-info-circle"></i> Escribe una dirección y presiona Enter, o haz clic directamente en el mapa
                        </div>
                        <input type="hidden" id="latitud" name="latitud">
                        <input type="hidden" id="longitud" name="longitud">
                        <div id="map"></div>
                    </div>

                    <!-- Lista de Invitados -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Lista de Invitados</label>
                        <div class="input-group mb-2">
                            <input type="email" class="form-control" id="correo_invitado" 
                                   placeholder="correo@ejemplo.com">
                            <button type="button" class="btn btn-primary" onclick="agregarInvitado()">
                                <i class="bi bi-plus"></i> Agregar
                            </button>
                        </div>
                        <div id="lista_invitados"></div>
                        <input type="hidden" name="invitados" id="invitados_json" value="[]">
                    </div>

                    <!-- Botones -->
                    <div class="row">
                        <div class="col-md-6">
                            <a href="home.php" class="btn-cancelar w-100">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn-submit">
                                <i class="bi bi-check-circle"></i> Crear Evento
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </section>

    <script src="../assets/js/bootstrap.bundle.js"></script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        let map;
        let marker;
        let invitados = [];

        // Inicializar Leaflet Map
        function initMap() {
            try {
                // Ubicación inicial (Querétaro, México)
                const ubicacionInicial = [20.5888, -100.3899];
                
                // Crear el mapa
                map = L.map('map').setView(ubicacionInicial, 13);
                
                console.log('Mapa Leaflet inicializado correctamente');
            
            // Agregar capa de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Crear marcador arrastrable
            marker = L.marker(ubicacionInicial, {
                draggable: true
            }).addTo(map);
            
            // Evento al arrastrar el marcador
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                document.getElementById('latitud').value = position.lat;
                document.getElementById('longitud').value = position.lng;
                
                // Obtener dirección usando geocoding inverso
                obtenerDireccion(position.lat, position.lng);
            });
            
            // Click en el mapa
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                document.getElementById('latitud').value = e.latlng.lat;
                document.getElementById('longitud').value = e.latlng.lng;
                
                obtenerDireccion(e.latlng.lat, e.latlng.lng);
            });
            
            // Inicializar valores
            document.getElementById('latitud').value = ubicacionInicial[0];
            document.getElementById('longitud').value = ubicacionInicial[1];
            
            } catch (error) {
                console.error('Error al inicializar el mapa:', error);
                document.getElementById('map').innerHTML = '<div style="padding: 2rem; text-align: center; color: #ef4444;">Error al cargar el mapa. Por favor recarga la página.</div>';
            }
        }

        // Buscar ubicación por texto
        function buscarUbicacion(direccion) {
            if (!direccion.trim()) return;
            
            // Usar Nominatim (servicio gratuito de OpenStreetMap)
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccion)}&limit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);
                        
                        // Mover mapa y marcador
                        map.setView([lat, lon], 15);
                        marker.setLatLng([lat, lon]);
                        
                        // Actualizar coordenadas
                        document.getElementById('latitud').value = lat;
                        document.getElementById('longitud').value = lon;
                        document.getElementById('ubicacion').value = data[0].display_name;
                    } else {
                        alert('No se encontró la ubicación. Intenta con otra dirección.');
                    }
                })
                .catch(error => {
                    console.error('Error al buscar ubicación:', error);
                    alert('Error al buscar la ubicación');
                });
        }

        // Obtener dirección desde coordenadas
        function obtenerDireccion(lat, lng) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById('ubicacion').value = data.display_name;
                    }
                })
                .catch(error => {
                    console.error('Error al obtener dirección:', error);
                });
        }

        // Enter para buscar
        document.getElementById('ubicacion').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarUbicacion(this.value);
            }
        });

        // Gestión de invitados
        function agregarInvitado() {
            const correo = document.getElementById('correo_invitado').value.trim();
            
            if (!correo) {
                alert('Por favor ingresa un correo');
                return;
            }
            
            if (!validarEmail(correo)) {
                alert('Por favor ingresa un correo válido');
                return;
            }
            
            if (invitados.includes(correo)) {
                alert('Este correo ya está en la lista');
                return;
            }
            
            invitados.push(correo);
            actualizarListaInvitados();
            document.getElementById('correo_invitado').value = '';
        }

        function eliminarInvitado(correo) {
            invitados = invitados.filter(i => i !== correo);
            actualizarListaInvitados();
        }

        function actualizarListaInvitados() {
            const lista = document.getElementById('lista_invitados');
            lista.innerHTML = '';
            
            invitados.forEach(correo => {
                const div = document.createElement('div');
                div.className = 'invitado-item';
                div.innerHTML = `
                    <span>${correo}</span>
                    <button type="button" class="btn-eliminar-invitado" onclick="eliminarInvitado('${correo}')">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
                lista.appendChild(div);
            });
            
            document.getElementById('invitados_json').value = JSON.stringify(invitados);
        }

        function validarEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        // Permitir agregar invitado con Enter
        document.getElementById('correo_invitado').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                agregarInvitado();
            }
        });

        // Inicializar mapa cuando cargue la página
        window.addEventListener('load', initMap);
    </script>

</body>
</html>
<?php mysqli_close($conexion); ?>