<?php
/**
 * Editar Usuario desde Admin
 * Procesa la actualización de usuarios (admin, vendedor o cliente)
 */

require_once '../core/conexion.php';
require_once '../core/sesiones.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Asegurarse de que siempre retornamos JSON
header('Content-Type: application/json; charset=utf-8');

// Validar autenticación - Solo admin (rol 1) y vendedor (rol 2) pueden editar usuarios
if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    http_response_code(403);
    echo json_encode(['exito' => false, 'mensaje' => 'No tienes permisos para editar usuarios']);
    exit();
}

// Solo procesar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

// Obtener datos del formulario
$id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$contraseña = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';
$id_rol = isset($_POST['id_rol']) ? intval($_POST['id_rol']) : 0;
$estado = isset($_POST['estado']) ? trim($_POST['estado']) : 'activo';

// Validaciones
$errores = [];

if ($id_usuario <= 0) {
    $errores[] = 'ID de usuario inválido';
}

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

// Validar rol
if (!in_array($id_rol, [1, 2, 3])) {
    $errores[] = 'Rol inválido';
}

// Validar estado
if (!in_array($estado, ['activo', 'inactivo'])) {
    $errores[] = 'Estado inválido';
}

// Si hay errores, retornar respuesta JSON
if (!empty($errores)) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Por favor corrige los siguientes errores:',
        'errores' => $errores
    ]);
    exit();
}

// Obtener usuario actual para verificar que existe
$query_verify = "SELECT correo FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($query_verify);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    echo json_encode([
        'exito' => false,
        'mensaje' => 'El usuario no existe'
    ]);
    exit();
}

$usuario_actual = $result->fetch_assoc();
$stmt->close();

// Verificar si el correo cambió y si el nuevo correo ya existe
if ($correo !== $usuario_actual['correo']) {
    $query_check = "SELECT id_usuario FROM usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($query_check);
    $stmt->bind_param("s", $correo);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        echo json_encode([
            'exito' => false,
            'mensaje' => 'El correo ya está registrado por otro usuario'
        ]);
        exit();
    }
    $stmt->close();
}

// Actualizar usuario
$actualizar_contraseña = !empty($contraseña);

if ($actualizar_contraseña) {
    // Validar contraseña si se proporciona
    if (strlen($contraseña) < 6) {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'La contraseña debe tener al menos 6 caracteres'
        ]);
        exit();
    }

    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
    $query_update = "UPDATE usuarios SET nombre = ?, correo = ?, contraseña = ?, id_rol = ?, estado = ? 
                    WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query_update);
    $stmt->bind_param("sssisi", $nombre, $correo, $contraseña_hash, $id_rol, $estado, $id_usuario);
} else {
    // Sin actualizar contraseña
    $query_update = "UPDATE usuarios SET nombre = ?, correo = ?, id_rol = ?, estado = ? 
                    WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query_update);
    $stmt->bind_param("ssisi", $nombre, $correo, $id_rol, $estado, $id_usuario);
}

if ($stmt->execute()) {
    // Si es cliente, actualizar también en la tabla clientes
    if ($id_rol == 3) {
        $query_cliente = "UPDATE clientes SET nombre = ? WHERE id_usuario = ?";
        $stmt_cliente = $conexion->prepare($query_cliente);
        $stmt_cliente->bind_param("si", $nombre, $id_usuario);
        $stmt_cliente->execute();
        $stmt_cliente->close();
    }

    $stmt->close();

    echo json_encode([
        'exito' => true,
        'mensaje' => 'Usuario actualizado exitosamente'
    ]);
} else {
    $stmt->close();
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al actualizar el usuario: ' . $conexion->error
    ]);
}

