<?php
/**
 * Endpoint para Destruir Sesión por Expiración
 * Se ejecuta cuando JavaScript detecta que la sesión debe expirar
 */

require_once 'sesiones.php';

// Destruir sesión completamente
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

// Redirigir a página de inicio
header("Location: ../index.php");
exit();
?>
