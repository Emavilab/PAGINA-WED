<?php
/**
 * Crear Usuario desde Admin
 * Procesa la creación de usuarios (admin, vendedor o cliente)
 * (La autenticación está protegida en usuarios.php)
 */

require_once '../core/conexion.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Asegurarse de que siempre retornamos JSON
header('Content-Type: application/json; charset=utf-8');

// Solo procesar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

// Obtener datos del formulario
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$contraseña = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';
$id_rol = isset($_POST['id_rol']) ? intval($_POST['id_rol']) : 0;

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

// Validar contraseña
if (empty($contraseña)) {
    $errores[] = 'La contraseña es requerida';
} elseif (strlen($contraseña) < 6) {
    $errores[] = 'La contraseña debe tener al menos 6 caracteres';
} elseif (strlen($contraseña) > 50) {
    $errores[] = 'La contraseña es demasiado larga';
}

// Validar rol (solo 1, 2 o 3)
if (!in_array($id_rol, [1, 2, 3])) {
    $errores[] = 'Rol inválido';
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

// Verificar si el email ya existe
$query_check = "SELECT id_usuario FROM usuarios WHERE correo = ?";
$stmt = $conexion->prepare($query_check);
$stmt->bind_param("s", $correo);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    echo json_encode([
        'exito' => false,
        'mensaje' => 'El correo ya está registrado en el sistema'
    ]);
    exit();
}

$stmt->close();

// Hashear contraseña
$contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

// Insertar usuario
$query_insert = "INSERT INTO usuarios (nombre, correo, contraseña, id_rol, estado) 
                VALUES (?, ?, ?, ?, 'activo')";

$stmt = $conexion->prepare($query_insert);
$stmt->bind_param("sssi", $nombre, $correo, $contraseña_hash, $id_rol);

if ($stmt->execute()) {
    $id_usuario = $conexion->insert_id;
    
    // Si es cliente, crear también en tabla clientes
    if ($id_rol == 3) {
        $query_cliente = "INSERT INTO clientes (id_usuario, nombre, estado) 
                         VALUES (?, ?, 'activo')";
        
        $stmt_cliente = $GLOBALS['conexion']->prepare($query_cliente);
        $stmt_cliente->bind_param("is", $id_usuario, $nombre);
        $stmt_cliente->execute();
        $stmt_cliente->close();
    }
    
    $stmt->close();
    
    // Determinar nombre del rol
    $nombre_rol = '';
    switch($id_rol) {
        case 1:
            $nombre_rol = 'Administrador';
            break;
        case 2:
            $nombre_rol = 'Vendedor';
            break;
        case 3:
            $nombre_rol = 'Cliente';
            break;
    }
    
    echo json_encode([
        'exito' => true,
        'mensaje' => "Usuario {$nombre_rol} '{$nombre}' creado exitosamente",
        'id_usuario' => $id_usuario
    ]);
} else {
    $stmt->close();
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al crear el usuario: ' . $conexion->error
    ]);
}

