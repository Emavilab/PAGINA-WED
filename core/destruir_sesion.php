<?php
/**
 * =====================================================
 * ENDPOINT: Destruir Sesión por Expiración
 * =====================================================
 *
 * Este archivo se ejecuta cuando JavaScript detecta
 * que la sesión del usuario ha expirado.
 * 
 * FUNCIONALIDAD:
 * - Destruye completamente la sesión actual
 * - Elimina la cookie de sesión si existe
 * - Redirige al usuario a la página de inicio
 */

// =====================================================
// INCLUSIÓN DE ARCHIVOS NECESARIOS
// =====================================================

// Archivo que contiene funciones de sesiones y autenticación
require_once 'sesiones.php';

// =====================================================
// DESTRUIR SESIÓN COMPLETAMENTE
// =====================================================

// Limpiar todas las variables de sesión
$_SESSION = array();

// =====================================================
// ELIMINAR COOKIE DE SESIÓN (SI SE UTILIZA)
// =====================================================

if (ini_get("session.use_cookies")) {
    // Obtener los parámetros actuales de la cookie de sesión
    $params = session_get_cookie_params();

    // Sobrescribir la cookie de sesión con un tiempo de expiración pasado
    setcookie(
        session_name(),   // Nombre de la cookie de sesión
        '',               // Valor vacío
        time() - 42000,   // Tiempo de expiración pasado para borrar
        $params["path"],  // Ruta
        $params["domain"],// Dominio
        $params["secure"],// Solo HTTPS si aplica
        $params["httponly"]// Solo accesible vía HTTP, no JS
    );
}

// =====================================================
// DESTRUIR LA SESIÓN EN EL SERVIDOR
// =====================================================
session_destroy();

// =====================================================
// REDIRECCIÓN A PÁGINA DE INICIO
// =====================================================

// Enviar al usuario al index principal
header("Location: ../index.php");
exit();
?> 