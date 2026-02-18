<?php
/**
 * Validar Login
 * Procesa la autenticación de usuarios
 */

// Encabezados de respuesta JSON
header('Content-Type: application/json; charset=utf-8');

// Configurar manejo de errores
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error en el servidor',
        'error' => "{$errstr} en {$errfile} línea {$errline}"
    ]);
    exit();
});

require_once '../core/sesiones.php';

// Solo procesar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

// Obtener datos del formulario
$correo = isset($_POST['email']) ? trim($_POST['email']) : '';
$contraseña = isset($_POST['password']) ? $_POST['password'] : '';

// Validaciones básicas
$errores = [];

if (empty($correo)) {
    $errores[] = 'El correo es requerido';
}

if (empty($contraseña)) {
    $errores[] = 'La contraseña es requerida';
}

// Si hay errores, retornar respuesta JSON
if (!empty($errores)) {
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Por favor completa todos los campos',
        'errores' => $errores
    ]);
    exit();
}

// Validar credenciales
$usuario = validarCredenciales($correo, $contraseña);

if (!$usuario) {
    registrarIntento($correo, 'Credenciales inválidas');
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Correo o contraseña incorrectos'
    ]);
    exit();
}

// Verificar que el usuario esté activo
if ($usuario['estado'] !== 'activo') {
    registrarIntento($correo, 'Intento de login con usuario inactivo');
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Tu cuenta ha sido desactivada. Por favor contacta al administrador.'
    ]);
    exit();
}

// Login exitoso - Registrar sesión
registrarSesion(
    $usuario['id_usuario'],
    $usuario['correo'],
    $usuario['nombre'],
    $usuario['id_rol']
);

// Determinar página de redirección según rol
$redirect = 'index1.php'; // Default (desde raíz)

if ($usuario['id_rol'] == 1) {
    $redirect = 'admin/Dashboard.php'; // Administrador
} elseif ($usuario['id_rol'] == 2) {
    $redirect = 'admin/Dashboard.php'; // Vendedor
} elseif ($usuario['id_rol'] == 3) {
    $redirect = 'index1.php'; // Cliente
}

// Limpiar cualquier buffer de salida anterior
while (ob_get_level()) {
    ob_end_clean();
}

// Respuesta JSON final
http_response_code(200);
echo json_encode([
    'exito' => true,
    'mensaje' => 'Sesión iniciada correctamente',
    'redirect' => $redirect
]);
exit();
?>
