<?php

/*
========================================================
MODULO: CREAR USUARIO DESDE PANEL ADMINISTRATIVO
========================================================

Este archivo se encarga de procesar la creación de
usuarios dentro del sistema administrativo.

TIPOS DE USUARIO QUE SE PUEDEN CREAR:
1 - Administrador
2 - Vendedor
3 - Cliente

FUNCIONES PRINCIPALES:
✔ Validar que el usuario esté autenticado
✔ Verificar permisos (solo admin y vendedor)
✔ Validar los datos del formulario
✔ Verificar que el correo no esté registrado
✔ Encriptar la contraseña del usuario
✔ Crear el usuario en la base de datos
✔ Crear registro en tabla clientes si el rol es cliente
✔ Retornar respuesta en formato JSON

RESPUESTAS:
El sistema devuelve respuestas JSON para ser usadas
por formularios AJAX del panel administrativo.

AUTOR: Sistema Web
========================================================
*/


/* ====================================================
   CONEXION A LA BASE DE DATOS
==================================================== */

require_once '../core/conexion.php';
require_once '../core/audit_logging.php';


/* ====================================================
   SISTEMA DE SESIONES
   Permite verificar si el usuario está logueado
==================================================== */

require_once '../core/sesiones.php';
require_once '../core/csrf.php';

validarCSRFMiddleware();

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* ====================================================
   CONFIGURACION DE RESPUESTA JSON
   Todas las respuestas del archivo serán en JSON
==================================================== */

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Usar ob_start() para capturar cualquier output accidental
ob_start();

// Registrar handler de error para endpoint JSON
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_end_clean(); // Limpiar output accidental
    header('Content-Type: application/json; charset=utf-8', true);
    http_response_code(500);
    die(json_encode(['exito' => false, 'mensaje' => 'Error interno: ' . $errstr]));
});

// Capturar excepciones no manejadas
set_exception_handler(function($e) {
    ob_end_clean(); // Limpiar output accidental
    header('Content-Type: application/json; charset=utf-8', true);
    http_response_code(500);
    die(json_encode(['exito' => false, 'mensaje' => 'Excepción: ' . $e->getMessage()]));
});



/* ====================================================
   VALIDAR AUTENTICACION Y PERMISOS
   Solo administradores (rol 1) y vendedores (rol 2)
   pueden crear nuevos usuarios
==================================================== */

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    http_response_code(403);
    echo json_encode(['exito' => false, 'mensaje' => 'No tienes permisos para crear usuarios']);
    exit();
}



/* ====================================================
   VALIDAR QUE LA PETICION SEA POST
   Evita accesos directos por URL
==================================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit();
}



/* ====================================================
   OBTENER DATOS DEL FORMULARIO
==================================================== */

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$contraseña = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';
$id_rol = isset($_POST['id_rol']) ? intval($_POST['id_rol']) : 0;



/* ====================================================
   VALIDACIONES DE DATOS
==================================================== */

$errores = [];



/* validar nombre */

if (empty($nombre)) {
    $errores[] = 'El nombre es requerido';
} elseif (strlen($nombre) < 3) {
    $errores[] = 'El nombre debe tener al menos 3 caracteres';
} elseif (strlen($nombre) > 100) {
    $errores[] = 'El nombre no puede exceder 100 caracteres';
}



/* validar correo */

if (empty($correo)) {
    $errores[] = 'El correo es requerido';
} elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El correo no es válido';
}



/* validar contraseña */

if (empty($contraseña)) {
    $errores[] = 'La contraseña es requerida';
} elseif (strlen($contraseña) < 6) {
    $errores[] = 'La contraseña debe tener al menos 6 caracteres';
} elseif (strlen($contraseña) > 50) {
    $errores[] = 'La contraseña es demasiado larga';
}



/* validar rol permitido */

if (!in_array($id_rol, [1, 2, 3])) {
    $errores[] = 'Rol inválido';
}



/* ====================================================
   SI EXISTEN ERRORES SE DEVUELVEN AL FORMULARIO
==================================================== */

if (!empty($errores)) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Por favor corrige los siguientes errores:',
        'errores' => $errores
    ]);
    exit();
}



/* ====================================================
   VERIFICAR SI EL CORREO YA ESTA REGISTRADO
==================================================== */

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



/* ====================================================
   ENCRIPTAR CONTRASEÑA DEL USUARIO
   Se utiliza password_hash para seguridad
==================================================== */

$contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);



/* ====================================================
   INSERTAR NUEVO USUARIO EN LA BASE DE DATOS
==================================================== */

$query_insert = "INSERT INTO usuarios (nombre, correo, contraseña, id_rol, estado) 
                VALUES (?, ?, ?, ?, 'activo')";

$stmt = $conexion->prepare($query_insert);

$stmt->bind_param("sssi", $nombre, $correo, $contraseña_hash, $id_rol);



/* ====================================================
   EJECUTAR REGISTRO DEL USUARIO
==================================================== */

if ($stmt->execute()) {

    $id_usuario = $conexion->insert_id;
    
    $stmt->close();
    
    // Si el rol es Cliente (3), insertar también en tabla clientes
    if ($id_rol == 3) {
        $query_cliente = "INSERT INTO clientes (id_usuario, nombre, estado) VALUES (?, ?, 'activo')";
        $stmt_cliente = $conexion->prepare($query_cliente);
        $stmt_cliente->bind_param("is", $id_usuario, $nombre);
        $stmt_cliente->execute();
        $stmt_cliente->close();
    }
    
    // REGISTRAR EN AUDIT LOG (SIN QUE CAUSE ERROR SI FALLA)
    try {
        if (function_exists('registrarAudit')) {
            registrarAudit(
                'CREATE',
                'usuarios',
                $id_usuario,
                [],
                [
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'rol' => $id_rol,
                    'estado' => 'activo'
                ],
                "Nuevo usuario creado: $nombre ($correo)"
            );
        }
    } catch (Throwable $auditError) {
        // No fallar si hay error en auditoría
        error_log("Error al registrar auditoría: " . $auditError->getMessage());
    }


    /* ====================================================
       OBTENER NOMBRE DEL ROL
    ==================================================== */

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
    


    /* ====================================================
       RESPUESTA EXITOSA
    ==================================================== */

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
