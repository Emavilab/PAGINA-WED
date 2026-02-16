<?php
/**
 * API para actualizar perfil del usuario
 * Actualiza nombre y correo en la base de datos
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
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';

// Validaciones
$errores = [];

if (empty($nombre)) {
    $errores[] = 'El nombre es requerido';
} elseif (strlen($nombre) < 3) {
    $errores[] = 'El nombre debe tener al menos 3 caracteres';
} elseif (strlen($nombre) > 100) {
    $errores[] = 'El nombre no puede exceder 100 caracteres';
}

if (empty($correo)) {
    $errores[] = 'El correo es requerido';
} elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El correo no es válido';
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

// Verificar si el correo ya existe (excepto para el usuario actual)
$query_check = "SELECT id_usuario FROM usuarios WHERE correo = ? AND id_usuario != ?";
$stmt = $conexion->prepare($query_check);
$stmt->bind_param("si", $correo, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'El correo ya está registrado con otra cuenta'
    ]);
    $stmt->close();
    exit();
}
$stmt->close();

// Actualizar datos del usuario
$query = "UPDATE usuarios SET nombre = ?, correo = ? WHERE id_usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("ssi", $nombre, $correo, $id_usuario);

if ($stmt->execute()) {
    // Actualizar también la tabla de clientes si el usuario es cliente
    $query_cliente = "UPDATE clientes SET nombre = ? WHERE id_usuario = ?";
    $stmt_cliente = $conexion->prepare($query_cliente);
    $stmt_cliente->bind_param("si", $nombre, $id_usuario);
    $stmt_cliente->execute();
    $stmt_cliente->close();
    
    // Actualizar la sesión
    $_SESSION['nombre'] = $nombre;
    $_SESSION['correo'] = $correo;
    
    echo json_encode([
        'exito' => true,
        'mensaje' => 'Datos actualizados correctamente'
    ]);
} else {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al actualizar los datos'
    ]);
}

$stmt->close();

?>
