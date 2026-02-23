<?php
/**
 * Registrar Usuario
 * Procesa la creación de nuevas cuentas de usuario
 */

require_once '../core/sesiones.php';

// Solo procesar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

// Obtener datos del formulario
$nombre = isset($_POST['name']) ? trim($_POST['name']) : '';
$correo = isset($_POST['email']) ? trim($_POST['email']) : '';
$contraseña = isset($_POST['password']) ? $_POST['password'] : '';
$confirmar_contraseña = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';

// Validaciones
$errores = [];

// Validar nombre
if (empty($nombre)) {
    $errores[] = 'El nombre es requerido';
} elseif (strlen($nombre) < 3) {
    $errores[] = 'El nombre debe tener al menos 3 caracteres';
} elseif (strlen($nombre) > 100) {
    $errores[] = 'El nombre no puede exceder 100 caracteres';
}

// Validar correo
if (empty($correo)) {
    $errores[] = 'El correo es requerido';
} elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El correo no es válido';
}

// Validar contraseña segura
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

// Validar confirmación de contraseña
if ($contraseña !== $confirmar_contraseña) {
    $errores[] = 'Las contraseñas no coinciden';
}

// Si hay errores, retornar respuesta JSON
if (!empty($errores)) {
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Por favor corrige los siguientes errores:',
        'errores' => $errores
    ]);
    exit();
}

// Crear usuario
$resultado = crearUsuario($nombre, $correo, $contraseña);

if (!$resultado['exito']) {
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => false,
        'mensaje' => $resultado['mensaje']
    ]);
    exit();
}

// Usuario creado exitosamente
// Opcionalmente, autenticar automáticamente al usuario
registrarSesion($resultado['id_usuario'], $correo, $nombre, 3); // 3 = rol cliente

header('Content-Type: application/json');
echo json_encode([
    'exito' => true,
    'mensaje' => 'Cuenta creada exitosamente',
    'redirect' => 'index.php'
]);

?>
