<?php
/**
 * Gestión de Sesiones
 * Maneja la autenticación, validación y control de sesiones de usuarios
 */

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión a la base de datos
require_once 'conexion.php';

/**
 * Validar si el usuario está autenticado
 * @return bool
 */
function usuarioAutenticado() {
    return isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario']);
}

/**
 * Obtener ID del usuario autenticado
 * @return int|null
 */
function obtenerIdUsuario() {
    return isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;
}

/**
 * Obtener datos del usuario autenticado
 * @return array|null
 */
function obtenerDatosUsuario() {
    global $conexion;
    
    if (!usuarioAutenticado()) {
        return null;
    }
    
    $id_usuario = $_SESSION['id_usuario'];
    $query = "SELECT u.*, c.nombre as nombre_cliente, r.nombre as nombre_rol
              FROM usuarios u 
              LEFT JOIN clientes c ON u.id_usuario = c.id_usuario
              LEFT JOIN roles r ON u.id_rol = r.id_rol
              WHERE u.id_usuario = ?";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $datos = $result->fetch_assoc();
    
    $stmt->close();
    
    return $datos;
}

/**
 * Registrar usuario en sesión
 * @param int $id_usuario
 * @param string $correo
 * @param string $nombre
 * @param int $id_rol
 * @return void
 */
function registrarSesion($id_usuario, $correo, $nombre, $id_rol) {
    $_SESSION['id_usuario'] = $id_usuario;
    $_SESSION['correo'] = $correo;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['id_rol'] = $id_rol;
    $_SESSION['fecha_login'] = time();
}

/**
 * Cerrar sesión del usuario
 * @return void
 */
function cerrarSesion() {
    session_destroy();
    header("Location: ../index1.php");
    exit();
}

/**
 * Validar credenciales del usuario
 * @param string $correo
 * @param string $contraseña
 * @return array|null Array con datos del usuario o null si fallan las credenciales
 */
function validarCredenciales($correo, $contraseña) {
    global $conexion;
    
    $query = "SELECT id_usuario, nombre, correo, contraseña, id_rol, estado 
              FROM usuarios 
              WHERE correo = ?";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    
    $stmt->close();
    
    // Si no existe el usuario
    if (!$usuario) {
        return null;
    }
    
    // Si el usuario está inactivo
    if ($usuario['estado'] === 'inactivo') {
        return null;
    }
    
    // Validar contraseña
    if (!password_verify($contraseña, $usuario['contraseña'])) {
        return null;
    }
    
    return $usuario;
}

/**
 * Crear nuevo usuario
 * @param string $nombre
 * @param string $correo
 * @param string $contraseña
 * @param int $id_rol (default: 3 = cliente)
 * @return array Array con resultado: ['exito' => bool, 'mensaje' => string, 'id_usuario' => int]
 */
function crearUsuario($nombre, $correo, $contraseña, $id_rol = 3) {
    global $conexion;
    
    // Validar email válido
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        return ['exito' => false, 'mensaje' => 'El correo no es válido'];
    }
    
    // Validar contraseña mínima
    if (strlen($contraseña) < 6) {
        return ['exito' => false, 'mensaje' => 'La contraseña debe tener al menos 6 caracteres'];
    }
    
    // Verificar si el email ya existe
    $query_check = "SELECT id_usuario FROM usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($query_check);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        return ['exito' => false, 'mensaje' => 'El correo ya está registrado'];
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
        
        // Crear registro en clientes
        $query_cliente = "INSERT INTO clientes (id_usuario, nombre, estado) 
                         VALUES (?, ?, 'activo')";
        
        $stmt_cliente = $conexion->prepare($query_cliente);
        $stmt_cliente->bind_param("is", $id_usuario, $nombre);
        $stmt_cliente->execute();
        $stmt_cliente->close();
        
        $stmt->close();
        return ['exito' => true, 'mensaje' => 'Usuario registrado exitosamente', 'id_usuario' => $id_usuario];
    } else {
        $stmt->close();
        return ['exito' => false, 'mensaje' => 'Error al registrar el usuario: ' . $conexion->error];
    }
}

/**
 * Verificar si el usuario es administrador
 * @return bool
 */
function esAdmin() {
    return usuarioAutenticado() && isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1;
}

/**
 * Verificar si el usuario es cliente
 * @return bool
 */
function esCliente() {
    return usuarioAutenticado() && isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 2;
}

/**
 * Hacer log de intentos fallidos de login
 * @param string $correo
 * @param string $razon
 * @return void
 */
function registrarIntento($correo, $razon) {
    // Opcional: guardar en una tabla de auditoría
    // Por ahora solo se usa para depuración
    error_log("Intento de login fallido - Email: {$correo} - Razón: {$razon}");
}

?>
