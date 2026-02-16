<?php
/**
 * Conexión a la Base de Datos
 * Archivo de configuración para conectar a MySQL
 */

// Definir parámetros de conexión
$servername = "localhost";
$username = "root";
$password = "";
$database = "negocio_web";

// Crear conexión con MySQLi
$conexion = new mysqli($servername, $username, $password, $database);

// Establecer charset UTF-8
$conexion->set_charset("utf8mb4");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Modo de errores para debugging
$conexion->query("SET sql_mode='STRICT_TRANS_TABLES'");

// Variable para verificar que la conexión fue exitosa
$conexion_establecida = true;

?>
