<?php
/**
 * GESION DE SESIONES
 * Funciones para autenticación y control de usuarios
 */

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Compatibilidad de sesión: sincronizar id e id_usuario
if (!isset($_SESSION['id_usuario']) && isset($_SESSION['id'])) {
    $_SESSION['id_usuario'] = $_SESSION['id'];
}
if (!isset($_SESSION['id']) && isset($_SESSION['id_usuario'])) {
    $_SESSION['id'] = $_SESSION['id_usuario'];
}

// Cargar variables de entorno
require_once __DIR__ . '/env_loader.php';

// Cargar conexión a base de datos
require_once __DIR__ . '/conexion.php';

// Cargar funciones de rate limiting
require_once __DIR__ . '/rate_limiting.php';

// Verifica si el usuario está autenticado
function usuarioAutenticado() {
    $id = $_SESSION['id'] ?? ($_SESSION['id_usuario'] ?? null);
    return !empty($id);
}

// Obtiene datos del usuario autenticado (incluyendo nombre del rol)
function obtenerDatosUsuario() {
    global $conexion;
    
    if (!usuarioAutenticado()) {
        return null;
    }
    
    try {
        // Obtener datos del usuario con el nombre del rol desde la tabla roles
            // Obtener datos del usuario con rol y cliente asociado
        $stmt = $conexion->prepare("
            SELECT 
                u.id_usuario,
                u.nombre,
                u.correo,
                u.id_rol,
                    r.nombre as nombre_rol,
                    c.id_cliente
            FROM usuarios u
            LEFT JOIN roles r ON u.id_rol = r.id_rol
                LEFT JOIN clientes c ON c.id_usuario = u.id_usuario
            WHERE u.id_usuario = ?
        ");
        
        $id = $_SESSION['id'] ?? ($_SESSION['id_usuario'] ?? null);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            $stmt->close();
            
            return [
                'id' => $usuario['id_usuario'] ?? null,
                    'id_usuario' => $usuario['id_usuario'] ?? null,
                    'id_cliente' => $usuario['id_cliente'] ?? null,
                'nombre' => $usuario['nombre'] ?? null,
                'correo' => $usuario['correo'] ?? null,
                'id_rol' => $usuario['id_rol'] ?? null,
                'nombre_rol' => $usuario['nombre_rol'] ?? 'Sin definir',
                'apellido' => $_SESSION['apellido'] ?? null,
                'telefono' => $_SESSION['telefono'] ?? null
            ];
        }
        
        $stmt->close();
        
        // Fallback a datos de sesión si no se encuentra en BD
        return [
            'id' => $_SESSION['id'] ?? null,
                'id_usuario' => $_SESSION['id'] ?? ($_SESSION['id_usuario'] ?? null),
                'id_cliente' => $_SESSION['id_cliente'] ?? null,
            'nombre' => $_SESSION['nombre'] ?? null,
            'correo' => $_SESSION['correo'] ?? null,
            'id_rol' => $_SESSION['id_rol'] ?? null,
            'nombre_rol' => 'Sin definir',
            'apellido' => $_SESSION['apellido'] ?? null,
            'telefono' => $_SESSION['telefono'] ?? null
        ];
        
    } catch (Exception $e) {
        // Si hay error en la consulta, retornar datos de sesión
        return [
            'id' => $_SESSION['id'] ?? null,
                'id_usuario' => $_SESSION['id'] ?? ($_SESSION['id_usuario'] ?? null),
                'id_cliente' => $_SESSION['id_cliente'] ?? null,
            'nombre' => $_SESSION['nombre'] ?? null,
            'correo' => $_SESSION['correo'] ?? null,
            'id_rol' => $_SESSION['id_rol'] ?? null,
            'nombre_rol' => 'Sin definir',
            'apellido' => $_SESSION['apellido'] ?? null,
            'telefono' => $_SESSION['telefono'] ?? null
        ];
    }
}

// Verifica si tiene un rol específico
function tieneRol($rol) {
    return usuarioAutenticado() && ($_SESSION['id_rol'] ?? null) == $rol;
}

// Verifica si es admin
function esAdmin() {
    return tieneRol(1);
}

// Verifica si es vendedor
function esVendedor() {
    return tieneRol(2);
}

// Verifica si es cliente
function esCliente() {
    return tieneRol(3);
}

// Cierra la sesión del usuario
function cerrarSesion() {
    // Destruir todas las variables de sesión
    $_SESSION = array();
    
    // Si hay una cookie de sesión, eliminarla
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
    
    // Destruir la sesión
    session_destroy();
}

/**
 * CREAR USUARIO
 * Registra un nuevo usuario en la base de datos
 * @param string $nombre Nombre del usuario
 * @param string $correo Correo del usuario
 * @param string $contraseña Contraseña sin encriptar
 * @return array Con estatus de la operación
 */
function crearUsuario($nombre, $correo, $contraseña) {
    global $conexion;
    
    // Verificar que el correo no esté registrado
    $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
    if (!$stmt) {
        return ['exito' => false, 'mensaje' => 'Error al preparar consulta'];
    }
    
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $stmt->close();
        return ['exito' => false, 'mensaje' => 'El correo ya está registrado'];
    }
    $stmt->close();
    
    // Hashear contraseña con bcrypt
    $contraseña_hash = password_hash($contraseña, PASSWORD_BCRYPT, ['cost' => 12]);
    
    $conexion->begin_transaction();

    // Insertar nuevo usuario
    // Campos: id_usuario (auto), nombre (s), correo (s), contraseña (s), id_rol (i), estado (s)
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contraseña, id_rol, estado) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        $conexion->rollback();
        return ['exito' => false, 'mensaje' => 'Error al preparar consulta de inserción'];
    }
    
    $id_rol = 3; // Cliente por defecto
    $estado = 'activo';
    
    // sssis = string nombre, string correo, string contraseña, int id_rol, string estado
    $stmt->bind_param("sssis", $nombre, $correo, $contraseña_hash, $id_rol, $estado);
    
    if (!$stmt->execute()) {
        $stmt->close();
        $conexion->rollback();
        return ['exito' => false, 'mensaje' => 'Error al crear la cuenta: ' . $conexion->error];
    }
    
    $id_usuario = $stmt->insert_id;
    $stmt->close();

    // Insertar cliente asociado al nuevo usuario
    $stmtCliente = $conexion->prepare("INSERT INTO clientes (id_usuario, nombre, estado) VALUES (?, ?, ?)");
    if (!$stmtCliente) {
        $conexion->rollback();
        return ['exito' => false, 'mensaje' => 'Error al preparar inserción de cliente'];
    }

    $stmtCliente->bind_param("iss", $id_usuario, $nombre, $estado);
    if (!$stmtCliente->execute()) {
        $stmtCliente->close();
        $conexion->rollback();
        return ['exito' => false, 'mensaje' => 'Error al crear registro de cliente: ' . $conexion->error];
    }
    $stmtCliente->close();

    $conexion->commit();
    
    return [
        'exito' => true,
        'id_usuario' => $id_usuario,
        'mensaje' => 'Usuario creado correctamente'
    ];
}

/**
 * REGISTRAR SESIÓN
 * Establece las variables de sesión para un usuario autenticado
 * @param int $id_usuario ID del usuario
 * @param string $correo Correo del usuario
 * @param string $nombre Nombre del usuario
 * @param int $id_rol ID del rol
 */
function registrarSesion($id_usuario, $correo, $nombre, $id_rol) {
    $_SESSION['id'] = $id_usuario;
    $_SESSION['id_usuario'] = $id_usuario;
    $_SESSION['correo'] = $correo;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['id_rol'] = $id_rol;
    $_SESSION['inicio_sesion'] = time();
}
