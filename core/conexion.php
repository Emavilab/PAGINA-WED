<?php
/**
 * Conexión a la Base de Datos
 * Archivo de configuración para conectar a MySQL
 */

// Definir parámetros de conexión (usa variables de entorno en producción)
$servername = getenv('DB_HOST')   ?: "localhost";
$username   = getenv('DB_USER')   ?: "root";
$password   = getenv('DB_PASS')   ?: "";
$database   = getenv('DB_NAME')   ?: "negocio_web";
$db_port    = (int)(getenv('DB_PORT') ?: 3306);

// Crear conexión con MySQLi
$conexion = new mysqli($servername, $username, $password, $database, $db_port);

// Establecer charset UTF-8
$conexion->set_charset("utf8mb4");

// Verificar conexión
if ($conexion->connect_error) {
    // Si es una solicitud API, devolver JSON
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
        header('Content-Type: application/json; charset=utf-8', true);
        http_response_code(500);
        die(json_encode(['exito' => false, 'mensaje' => 'Error de conexión a la base de datos']));
    }
    die("Error de conexión: " . $conexion->connect_error);
}

// Modo de errores para debugging
$conexion->query("SET sql_mode='STRICT_TRANS_TABLES'");

// Variable para verificar que la conexión fue exitosa
$conexion_establecida = true;

// Desactivar ofertas vencidas automáticamente
$conexion->query("UPDATE productos SET en_oferta = 0, precio_descuento = NULL WHERE en_oferta = 1 AND fecha_fin_oferta < CURDATE()");