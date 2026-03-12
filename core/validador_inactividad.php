<?php
/**
 * Validador de Inactividad de Sesión
 * Verifica si la sesión ha estado inactiva por más de 120 segundos (2 minutos)
 */

// Tiempo máximo de inactividad en segundos (120 = 2 minutos)
$TIEMPO_MAXIMO_INACTIVIDAD = 120;

// Obtener tiempo actual
$tiempo_actual = time();

// Si no existe la última actividad, registrarla
if (!isset($_SESSION['ultima_actividad'])) {
    $_SESSION['ultima_actividad'] = $tiempo_actual;
}

// Calcular tiempo de inactividad
$tiempo_inactividad = $tiempo_actual - $_SESSION['ultima_actividad'];

// Verificar si ha superado el tiempo máximo de inactividad
if ($tiempo_inactividad > $TIEMPO_MAXIMO_INACTIVIDAD) {
    // Limpiar todas las variables de sesión
    $_SESSION = array();
    
    // Eliminar la cookie de sesión
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
    
    // Destruir la sesión
    session_destroy();
    
    // Redirigir a página de inicio
    header("Location: ../index.php");
    exit();
}

// Actualizar la última actividad
$_SESSION['ultima_actividad'] = $tiempo_actual;
?>
