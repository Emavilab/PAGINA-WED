<?php
/**
 * API para obtener datos de usuario autenticado
 * Devuelve JSON con los datos del usuario si está autenticado
 */

require_once '../core/sesiones.php';

header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!usuarioAutenticado()) {
    echo json_encode([
        'autenticado' => false,
        'usuario' => null
    ]);
    exit();
}

// Obtener datos del usuario
$usuario = obtenerDatosUsuario();

// Responder con los datos del usuario
echo json_encode([
    'autenticado' => true,
    'usuario' => [
        'id_usuario' => $_SESSION['id_usuario'] ?? null,
        'nombre' => $_SESSION['nombre'] ?? null,
        'correo' => $_SESSION['correo'] ?? null,
        'id_rol' => $_SESSION['id_rol'] ?? null,
        'nombre_rol' => $usuario['nombre_rol'] ?? null
    ]
]);

?>
