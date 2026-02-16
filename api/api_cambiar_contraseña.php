<?php
/**
 * API para cambiar contraseña del usuario
 */

require_once '../core/sesiones.php';

header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!usuarioAutenticado()) {
    http_response_code(401);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'No autorizado'
    ]);
    exit();
}

// Solo procesar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Método no permitido'
    ]);
    exit();
}

global $conexion;

$id_usuario = $_SESSION['id_usuario'];
$contraseña_actual = isset($_POST['contraseña_actual']) ? $_POST['contraseña_actual'] : '';
$contraseña_nueva = isset($_POST['contraseña_nueva']) ? $_POST['contraseña_nueva'] : '';
$confirmar_contraseña = isset($_POST['confirmar_contraseña']) ? $_POST['confirmar_contraseña'] : '';

// Validaciones
$errores = [];

if (empty($contraseña_actual)) {
    $errores[] = 'La contraseña actual es requerida';
}

if (empty($contraseña_nueva)) {
    $errores[] = 'La nueva contraseña es requerida';
} elseif (strlen($contraseña_nueva) < 6) {
    $errores[] = 'La nueva contraseña debe tener al menos 6 caracteres';
} elseif (strlen($contraseña_nueva) > 50) {
    $errores[] = 'La contraseña es demasiado larga';
}

if ($contraseña_nueva !== $confirmar_contraseña) {
    $errores[] = 'Las contraseñas no coinciden';
}

// Si hay errores
if (!empty($errores)) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Por favor corrige los siguientes errores:',
        'errores' => $errores
    ]);
    exit();
}

// Obtener contraseña actual del usuario
$query = "SELECT contraseña FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

// Verificar que la contraseña actual sea correcta
if (!password_verify($contraseña_actual, $usuario['contraseña'])) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'La contraseña actual es incorrecta'
    ]);
    exit();
}

// Encriptar la nueva contraseña
$contraseña_encriptada = password_hash($contraseña_nueva, PASSWORD_BCRYPT);

// Actualizar la contraseña
$query = "UPDATE usuarios SET contraseña = ? WHERE id_usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("si", $contraseña_encriptada, $id_usuario);

if ($stmt->execute()) {
    echo json_encode([
        'exito' => true,
        'mensaje' => 'Contraseña actualizada correctamente'
    ]);
} else {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al actualizar la contraseña'
    ]);
}

$stmt->close();

?>
