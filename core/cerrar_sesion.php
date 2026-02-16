<?php
/**
 * Cerrar Sesión
 * Cierra la sesión del usuario autenticado
 */

require_once 'sesiones.php';

// Verificar si el usuario está autenticado
if (!usuarioAutenticado()) {
    header("Location: ../index1.php");
    exit();
}

// Obtener datos antes de destruir sesión (opcional, para logs)
$usuario_id = obtenerIdUsuario();
$correo = isset($_SESSION['correo']) ? $_SESSION['correo'] : 'desconocido';

// Cerrar sesión
cerrarSesion();

?>
