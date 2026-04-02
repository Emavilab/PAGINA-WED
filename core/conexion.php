<?php
/**
 * Conexión a la Base de Datos
 */

// Cargar variables de entorno
require_once __DIR__ . '/env_loader.php';

// Obtener parámetros de conexión
$servername = getEnv('DB_HOST', 'localhost');
$username = getEnv('DB_USER', 'root');
$password = getEnv('DB_PASSWORD', '');
$database = getEnv('DB_NAME', 'negocio_web');

// Crear conexión
$conexion = new mysqli($servername, $username, $password, $database);

// Establecer charset
if (!$conexion->set_charset("utf8mb4")) {
    error_log("Error al establecer charset: " . $conexion->error);
}

// Verificar conexión
if ($conexion->connect_error) {
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
        header('Content-Type: application/json; charset=utf-8', true);
        http_response_code(500);
        die(json_encode(['exito' => false, 'mensaje' => 'Error de conexión a BD']));
    }
    die("Conexión fallida: " . $conexion->connect_error);
}

// Flag de conexión exitosa
$conexion_establecida = true;

// Configuración segura
$conexion->query("SET SESSION sql_mode='STRICT_TRANS_TABLES'");
?>