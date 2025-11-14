<?php
// Archivo: php/conexion_db.php
// Configuración de conexión a la base de datos

$host = "localhost";
$usuario = "root";
$password = ""; // Déjalo vacío si no tienes contraseña en tu MySQL
$base_datos = "db_litzor";
$puerto = 3309; // Ajusta según tu configuración

// Crear conexión con mysqli
$conexion = mysqli_connect($host, $usuario, $password, $base_datos, $puerto);

// Verificar conexión
if (!$conexion) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Establecer charset UTF-8 para evitar problemas con caracteres especiales
mysqli_set_charset($conexion, "utf8");

// Opcional: Descomentar en desarrollo para ver errores
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>