<?php
/**
 * ================================================================
 * API DE VALIDACIÓN DE LOGIN
 * ================================================================
 *
 * DESCRIPCIÓN:
 * Este script procesa la autenticación de usuarios en el sistema.
 * Recibe las credenciales enviadas desde un formulario de inicio
 * de sesión y valida si el usuario existe y si la contraseña es
 * correcta.
 *
 * FUNCIONALIDADES:
 * ✔ Acepta únicamente solicitudes POST
 * ✔ Valida los campos del formulario
 * ✔ Verifica credenciales en la base de datos
 * ✔ Verifica que el usuario esté activo
 * ✔ Registra intentos fallidos de login
 * ✔ Crea la sesión del usuario al iniciar sesión
 * ✔ Redirige según el rol del usuario
 * ✔ Devuelve respuesta en formato JSON
 *
 * ARCHIVOS UTILIZADOS:
 * - ../core/sesiones.php
 *
 * FUNCIONES UTILIZADAS:
 * - validarCredenciales()
 * - registrarIntento()
 * - registrarSesion()
 *
 * ROLES DEL SISTEMA:
 * 1 = Administrador
 * 2 = Vendedor
 * 3 = Cliente
 *
 * RESPUESTA JSON EXITOSA:
 * {
 *   "exito": true,
 *   "mensaje": "Sesión iniciada correctamente",
 *   "redirect": "admin/Dashboard.php"
 * }
 *
 * RESPUESTA JSON CON ERROR:
 * {
 *   "exito": false,
 *   "mensaje": "Correo o contraseña incorrectos"
 * }
 *
 * ================================================================
 */

/**
 * ---------------------------------------------------------------
 * ENCABEZADOS DE RESPUESTA
 * ---------------------------------------------------------------
 * Se define que todas las respuestas serán en formato JSON.
 */
header('Content-Type: application/json; charset=utf-8');

/**
 * ---------------------------------------------------------------
 * MANEJO PERSONALIZADO DE ERRORES
 * ---------------------------------------------------------------
 * Si ocurre un error PHP, se devuelve una respuesta JSON
 * indicando que ocurrió un error en el servidor.
 */
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error en el servidor',
        'error' => "{$errstr} en {$errfile} línea {$errline}"
    ]);
    exit();
});

/**
 * ---------------------------------------------------------------
 * INCLUIR ARCHIVOS NECESARIOS
 * ---------------------------------------------------------------
 * Contiene las funciones necesarias para autenticación y rate limiting.
 */
require_once '../core/sesiones.php';
require_once '../core/rate_limiting.php';
require_once '../core/csrf.php';

validarCSRFMiddleware();

/**
 * ---------------------------------------------------------------
 * VALIDAR MÉTODO DE SOLICITUD
 * ---------------------------------------------------------------
 * Este endpoint solo acepta solicitudes POST.
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

/**
 * ---------------------------------------------------------------
 * OBTENER DATOS DEL FORMULARIO
 * ---------------------------------------------------------------
 */
$correo = isset($_POST['email']) ? trim($_POST['email']) : '';
$contraseña = isset($_POST['password']) ? $_POST['password'] : '';

/**
 * ---------------------------------------------------------------
 * VALIDACIONES BÁSICAS
 * ---------------------------------------------------------------
 */
$errores = [];

if (empty($correo)) {
    $errores[] = 'El correo es requerido';
}

if (empty($contraseña)) {
    $errores[] = 'La contraseña es requerida';
}

/**
 * ---------------------------------------------------------------
 * SI EXISTEN ERRORES DE VALIDACIÓN
 * ---------------------------------------------------------------
 * Se devuelve una respuesta JSON con los errores.
 */
if (!empty($errores)) {
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Por favor completa todos los campos',
        'errores' => $errores
    ]);
    exit();
}

/**
 * ---------------------------------------------------------------
 * VERIFICAR RATE LIMITING (PROTECCIÓN CONTRA BRUTE FORCE)
 * ---------------------------------------------------------------
 * Se valida que la IP/usuario no haya excedido los intentos fallidos.
 * Límites:
 * - 5 intentos fallidos por IP en 1 hora
 * - 10 intentos fallidos por usuario en 1 hora
 * - Bloqueo temporal de 15 minutos al superar límite
 */
$ip = obtenerIPReal();
$verificacion_rate = verificarRateLimiting($ip, $correo);

if (!$verificacion_rate['permitido']) {
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => false,
        'mensaje' => $verificacion_rate['mensaje'],
        'bloqueado' => true,
        'bloqueado_hasta' => $verificacion_rate['bloqueado_hasta']
    ]);
    exit();
}

/**
 * ---------------------------------------------------------------
 * VALIDAR CREDENCIALES DEL USUARIO
 * ---------------------------------------------------------------
 * Se verifica si el correo y la contraseña coinciden
 * con un usuario registrado en el sistema.
 */
$usuario = validarCredenciales($correo, $contraseña);

/**
 * ---------------------------------------------------------------
 * SI LAS CREDENCIALES SON INCORRECTAS
 * ---------------------------------------------------------------
 */
if (!$usuario) {
    registrarIntentoFallido($ip, $correo, 'credenciales_invalidas');
    
    // Verificar si acaba de ser bloqueado por rate limiting
    $nueva_verificacion = verificarRateLimiting($ip, $correo);
    if (!$nueva_verificacion['permitido']) {
        header('Content-Type: application/json');
        echo json_encode([
            'exito' => false,
            'mensaje' => $nueva_verificacion['mensaje'],
            'bloqueado' => true
        ]);
        exit();
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Correo o contraseña incorrectos'
    ]);
    exit();
}

/**
 * ---------------------------------------------------------------
 * VERIFICAR ESTADO DEL USUARIO
 * ---------------------------------------------------------------
 * Solo los usuarios activos pueden iniciar sesión.
 */
if ($usuario['estado'] !== 'activo') {
    registrarIntentoFallido($ip, $correo, 'usuario_inactivo');
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Tu cuenta ha sido desactivada. Por favor contacta al administrador.'
    ]);
    exit();
}

/**
 * ---------------------------------------------------------------
 * LOGIN EXITOSO
 * ---------------------------------------------------------------
 * Se registra la sesión del usuario en el sistema.
 */
registrarSesion(
    $usuario['id_usuario'],
    $usuario['correo'],
    $usuario['nombre'],
    $usuario['id_rol']
);

/**
 * ---------------------------------------------------------------
 * REGISTRAR INTENTO EXITOSO (para limpieza de rate limiting)
 * ---------------------------------------------------------------
 */
registrarIntentoExitoso($ip, $correo);

/**
 * ---------------------------------------------------------------
 * DETERMINAR REDIRECCIÓN SEGÚN EL ROL
 * ---------------------------------------------------------------
 */
$redirect = 'admin/Dashboard.php'; // Página por defecto para admins

// Admin (id_rol = 1)
if ($usuario['id_rol'] == 1) {
    $redirect = 'admin/Dashboard.php'; // Administrador
} 
// Vendedor (id_rol = 2)
elseif ($usuario['id_rol'] == 2) {
    $redirect = 'admin/Dashboard.php'; // Vendedor usa el dashboard
} 
// Cliente (id_rol = 3)
elseif ($usuario['id_rol'] == 3) {
    $redirect = 'index.php'; // Cliente vuelve a la página principal
}

/**
 * ---------------------------------------------------------------
 * LIMPIAR BUFFER DE SALIDA
 * ---------------------------------------------------------------
 * Se eliminan posibles salidas previas antes de enviar JSON.
 */
while (ob_get_level()) {
    ob_end_clean();
}

/**
 * ---------------------------------------------------------------
 * RESPUESTA FINAL JSON
 * ---------------------------------------------------------------
 */
http_response_code(200);
echo json_encode([
    'exito' => true,
    'mensaje' => 'Sesión iniciada correctamente',
    'redirect' => $redirect
]);

exit();
?> 
