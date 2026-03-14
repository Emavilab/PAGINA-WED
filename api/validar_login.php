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
 * INCLUIR ARCHIVO DE SESIONES
 * ---------------------------------------------------------------
 * Contiene las funciones necesarias para autenticación.
 */
require_once '../core/sesiones.php';

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
    registrarIntento($correo, 'Credenciales inválidas');
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
    registrarIntento($correo, 'Intento de login con usuario inactivo');
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
 * DETERMINAR REDIRECCIÓN SEGÚN EL ROL
 * ---------------------------------------------------------------
 */
$redirect = 'index.php'; // Página por defecto

if ($usuario['id_rol'] == 1) {
    $redirect = 'admin/Dashboard.php'; // Administrador
} elseif ($usuario['id_rol'] == 2) {
    $redirect = 'admin/Dashboard.php'; // Vendedor
} elseif ($usuario['id_rol'] == 3) {
    $redirect = 'index.php'; // Cliente
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
