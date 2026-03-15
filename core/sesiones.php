<?php
/**
 * =====================================================
 * GESTIÓN DE SESIONES
 * =====================================================
 *
 * Este archivo maneja la autenticación, validación
 * y control de sesiones de usuarios.
 *
 * FUNCIONALIDADES PRINCIPALES:
 * - Iniciar sesión
 * - Validar si un usuario está autenticado
 * - Obtener datos de usuario
 * - Registrar y cerrar sesión
 * - Validar credenciales
 * - Crear nuevos usuarios
 * - Verificar roles (Admin / Cliente)
 * - Registrar intentos fallidos de login
 */

// =====================================================
// INICIAR SESIÓN
// =====================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si no está activa
}

// =====================================================
// INCLUIR CONEXIÓN A BASE DE DATOS
// =====================================================
require_once 'conexion.php'; // Archivo de conexión a la base de datos

// =====================================================
// FUNCIONES DE AUTENTICACIÓN Y SESIÓN
// =====================================================

/**
 * Validar si el usuario está autenticado
 * @return bool True si el usuario tiene sesión activa
 */
function usuarioAutenticado() {
    return isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario']);
}

/**
 * Obtener ID del usuario autenticado
 * @return int|null Retorna el ID de usuario o null si no está autenticado
 */
function obtenerIdUsuario() {
    return isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;
}

/**
 * Obtener todos los datos del usuario autenticado
 * @return array|null Arreglo con datos del usuario o null si no está autenticado
 */
function obtenerDatosUsuario() {
    global $conexion;

    if (!usuarioAutenticado()) {
        return null;
    }

    $id_usuario = $_SESSION['id_usuario'];

    // Consulta para obtener información del usuario, cliente y rol
    $query = "SELECT 
                u.*, 
                c.id_cliente,
                c.nombre as nombre_cliente, 
                r.nombre as nombre_rol
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
 * @param int $id_usuario ID del usuario
 * @param string $correo Correo electrónico
 * @param string $nombre Nombre del usuario
 * @param int $id_rol ID del rol
 * @return void
 */
function registrarSesion($id_usuario, $correo, $nombre, $id_rol) {
    $_SESSION['id_usuario'] = $id_usuario;
    $_SESSION['correo'] = $correo;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['id_rol'] = $id_rol;
    $_SESSION['fecha_login'] = time(); // Timestamp del login
}

/**
 * Cerrar sesión del usuario
 * @return void
 */
function cerrarSesion() {
    // Limpiar todas las variables de sesión
    $_SESSION = array();

    // Eliminar la cookie de sesión si existe
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Destruir la sesión en el servidor
    session_destroy();

    // Redirigir al index
    header("Location: ../index.php");
    exit();
}

/**
 * Validar credenciales de usuario
 * @param string $correo Correo del usuario
 * @param string $contraseña Contraseña en texto plano
 * @return array|null Datos del usuario si son válidos, null si falla
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
    

    // Verificar contraseña usando password_verify
    if (!password_verify($contraseña, $usuario['contraseña'])) {
        return null;
    }

    return $usuario;
}

/**
 * Crear nuevo usuario
 * @param string $nombre Nombre completo
 * @param string $correo Correo electrónico
 * @param string $contraseña Contraseña
 * @param int $id_rol Rol del usuario (default 3 = cliente)
 * @return array Resultado: ['exito' => bool, 'mensaje' => string, 'id_usuario' => int]
 */
function crearUsuario($nombre, $correo, $contraseña, $id_rol = 3) {
    global $conexion;

    // Validar correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        return ['exito' => false, 'mensaje' => 'El correo no es válido'];
    }

    // Validar longitud de contraseña
    if (strlen($contraseña) < 6) {
        return ['exito' => false, 'mensaje' => 'La contraseña debe tener al menos 6 caracteres'];
    }

    // Verificar si el correo ya existe
    $query_check = "SELECT id_usuario FROM usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($query_check);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        return ['exito' => false, 'mensaje' => 'El correo ya está registrado'];
    }
    $stmt->close();

    // Hashear la contraseña antes de guardar
    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

    // Insertar usuario en la tabla
    $query_insert = "INSERT INTO usuarios (nombre, correo, contraseña, id_rol, estado) 
                     VALUES (?, ?, ?, ?, 'activo')";

    $stmt = $conexion->prepare($query_insert);
    $stmt->bind_param("sssi", $nombre, $correo, $contraseña_hash, $id_rol);

    if ($stmt->execute()) {
        $id_usuario = $conexion->insert_id;

        // Crear registro en tabla clientes
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
 * Verificar si el usuario autenticado es administrador
 * @return bool True si es administrador
 */
function esAdmin() {
    return usuarioAutenticado() && isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1;
}

/**
 * Verificar si el usuario autenticado es cliente
 * @return bool True si es cliente
 */
function esCliente() {
    return usuarioAutenticado() && isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 3;
}

/**
 * Registrar intento fallido de login
 * @param string $correo Correo usado en el intento
 * @param string $razon Razón del fallo
 * @return void
 */
function registrarIntento($correo, $razon) {
    // Para auditoría o depuración
    error_log("Intento de login fallido - Email: {$correo} - Razón: {$razon}");
}
?> 