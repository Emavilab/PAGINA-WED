<?php
/**
 * Cerrar Sesión
 * Cierra la sesión del usuario autenticado
 */

require_once 'sesiones.php';

// Verificar si el usuario está autenticado
if (!usuarioAutenticado()) {
    header("Location: ../index.php");
    exit();
}

// Cerrar sesión del usuario
cerrarSesion();
