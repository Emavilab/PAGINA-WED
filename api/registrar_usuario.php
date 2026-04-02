<?php
/**
 * ================================================================
 * API DE REGISTRO DE USUARIO
 * ================================================================
 *
 * DESCRIPCIÓN:
 * Este script procesa el registro de nuevos usuarios en el sistema.
 * Recibe los datos enviados desde un formulario HTML mediante método POST
 * y realiza validaciones antes de crear la cuenta en la base de datos.
 *
 * FUNCIONALIDADES:
 * ✔ Verifica que la petición sea de tipo POST
 * ✔ Obtiene los datos enviados desde el formulario
 * ✔ Valida nombre, correo y contraseña
 * ✔ Verifica que las contraseñas coincidan
 * ✔ Crea el usuario en la base de datos
 * ✔ Inicia sesión automáticamente al registrarse
 * ✔ Devuelve una respuesta en formato JSON
 *
 * ARCHIVOS UTILIZADOS:
 * - ../core/sesiones.php
 *
 * FUNCIONES UTILIZADAS:
 * - crearUsuario()
 * - registrarSesion()
 *
 * RESPUESTA JSON POSITIVA:
 * {
 *   "exito": true,
 *   "mensaje": "Cuenta creada exitosamente",
 *   "redirect": "index.php"
 * }
 *
 * RESPUESTA JSON CON ERRORES:
 * {
 *   "exito": false,
 *   "mensaje": "Por favor corrige los siguientes errores:",
 *   "errores": [...]
 * }
 *
 * ================================================================
 */

// Incluir sistema de sesiones y funciones de autenticación
require_once '../core/sesiones.php';
require_once '../core/csrf.php';

validarCSRFMiddleware();

/**
 * ---------------------------------------------------------------
 * VERIFICAR MÉTODO DE SOLICITUD
 * ---------------------------------------------------------------
 * Este endpoint solo acepta solicitudes POST por seguridad.
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
 * Se recuperan los valores enviados desde el formulario HTML.
 */
$nombre = isset($_POST['name']) ? trim($_POST['name']) : '';
$correo = isset($_POST['email']) ? trim($_POST['email']) : '';
$contraseña = isset($_POST['password']) ? $_POST['password'] : '';
$confirmar_contraseña = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';

/**
 * ---------------------------------------------------------------
 * VALIDACIONES DE DATOS
 * ---------------------------------------------------------------
 * Se verifican los campos del formulario antes de registrar
 * el usuario en la base de datos.
 */
$errores = [];

/**
 * ---------------------------------------------------------------
 * VALIDAR NOMBRE
 * ---------------------------------------------------------------
 */
if (empty($nombre)) {
    $errores[] = 'El nombre es requerido';
} elseif (strlen($nombre) < 3) {
    $errores[] = 'El nombre debe tener al menos 3 caracteres';
} elseif (strlen($nombre) > 100) {
    $errores[] = 'El nombre no puede exceder 100 caracteres';
}

/**
 * ---------------------------------------------------------------
 * VALIDAR CORREO ELECTRÓNICO
 * ---------------------------------------------------------------
 */
if (empty($correo)) {
    $errores[] = 'El correo es requerido';
} elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El correo no es válido';
}

/**
 * ---------------------------------------------------------------
 * VALIDAR CONTRASEÑA SEGURA
 * ---------------------------------------------------------------
 * Reglas:
 * - mínimo 8 caracteres
 * - máximo 50 caracteres
 * - al menos una mayúscula
 * - al menos una minúscula
 * - al menos un número
 * - al menos un carácter especial
 */
if (empty($contraseña)) {
    $errores[] = 'La contraseña es requerida';
} else {
    if (strlen($contraseña) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres';
    }
    if (strlen($contraseña) > 50) {
        $errores[] = 'La contraseña es demasiado larga';
    }
    if (!preg_match('/[A-Z]/', $contraseña)) {
        $errores[] = 'La contraseña debe incluir al menos una letra mayúscula';
    }
    if (!preg_match('/[a-z]/', $contraseña)) {
        $errores[] = 'La contraseña debe incluir al menos una letra minúscula';
    }
    if (!preg_match('/[0-9]/', $contraseña)) {
        $errores[] = 'La contraseña debe incluir al menos un número';
    }
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?`~]/', $contraseña)) {
        $errores[] = 'La contraseña debe incluir al menos un carácter especial (!@#$%^&*)';
    }
}

/**
 * ---------------------------------------------------------------
 * VALIDAR CONFIRMACIÓN DE CONTRASEÑA
 * ---------------------------------------------------------------
 */
if ($contraseña !== $confirmar_contraseña) {
    $errores[] = 'Las contraseñas no coinciden';
}

/**
 * ---------------------------------------------------------------
 * SI EXISTEN ERRORES SE DEVUELVE RESPUESTA JSON
 * ---------------------------------------------------------------
 */
if (!empty($errores)) {
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Por favor corrige los siguientes errores:',
        'errores' => $errores
    ]);
    exit();
}

/**
 * ---------------------------------------------------------------
 * CREAR USUARIO EN EL SISTEMA
 * ---------------------------------------------------------------
 * Se llama a la función crearUsuario() definida en sesiones.php
 */
$resultado = crearUsuario($nombre, $correo, $contraseña);

/**
 * ---------------------------------------------------------------
 * SI OCURRE UN ERROR AL CREAR EL USUARIO
 * ---------------------------------------------------------------
 */
if (!$resultado['exito']) {
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => false,
        'mensaje' => $resultado['mensaje']
    ]);
    exit();
}

/**
 * ---------------------------------------------------------------
 * REGISTRO EXITOSO
 * ---------------------------------------------------------------
 * Se inicia sesión automáticamente para el usuario.
 * Rol 3 = Cliente
 */
registrarSesion($resultado['id_usuario'], $correo, $nombre, 3);

/**
 * ---------------------------------------------------------------
 * RESPUESTA FINAL JSON
 * ---------------------------------------------------------------
 */
header('Content-Type: application/json');
echo json_encode([
    'exito' => true,
    'mensaje' => 'Cuenta creada exitosamente',
    'redirect' => 'index.php'
]);

?> 