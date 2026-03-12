<?php
/*
========================================================
MODULO: EDITAR USUARIO DESDE PANEL ADMINISTRATIVO
========================================================

Este archivo procesa la actualización de usuarios dentro
del sistema administrativo.

TIPOS DE USUARIOS:
1 - Administrador
2 - Vendedor
3 - Cliente

FUNCIONES PRINCIPALES:
✔ Validar que el usuario esté autenticado
✔ Verificar permisos (solo admin y vendedor)
✔ Validar los datos recibidos del formulario
✔ Verificar que el usuario exista
✔ Verificar que el correo no esté duplicado
✔ Actualizar los datos del usuario
✔ Actualizar la contraseña si se proporciona
✔ Actualizar el nombre del cliente si el usuario es cliente
✔ Retornar respuesta en formato JSON para AJAX

RESPUESTAS:
El sistema devuelve respuestas JSON para ser usadas
por formularios o solicitudes AJAX del panel administrativo.

AUTOR: Sistema Web
========================================================
*/


/* ====================================================
   CONEXION A LA BASE DE DATOS
==================================================== */

require_once '../core/conexion.php';


/* ====================================================
   SISTEMA DE SESIONES
   Permite verificar autenticación del usuario
==================================================== */

require_once '../core/sesiones.php';


// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* ====================================================
   CONFIGURAR RESPUESTA EN FORMATO JSON
   Todas las respuestas serán JSON
==================================================== */

header('Content-Type: application/json; charset=utf-8');


/* ====================================================
   VALIDAR AUTENTICACION Y PERMISOS
   Solo Administrador (rol 1) y Vendedor (rol 2)
   pueden editar usuarios
==================================================== */

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    http_response_code(403);
    echo json_encode(['exito' => false, 'mensaje' => 'No tienes permisos para editar usuarios']);
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

$id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$contraseña = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';
$id_rol = isset($_POST['id_rol']) ? intval($_POST['id_rol']) : 0;
$estado = isset($_POST['estado']) ? trim($_POST['estado']) : 'activo';


/* ====================================================
   VALIDACIONES DE DATOS DEL FORMULARIO
==================================================== */

$errores = [];


// Validar ID del usuario
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


// Validar rol permitido
if (!in_array($id_rol, [1, 2, 3])) {
    $errores[] = 'Rol inválido';
}


// Validar estado permitido
if (!in_array($estado, ['activo', 'inactivo'])) {
    $errores[] = 'Estado inválido';
}


// Si existen errores se devuelven al formulario
if (!empty($errores)) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Por favor corrige los siguientes errores:',
        'errores' => $errores
    ]);
    exit();
}



/* ====================================================
   VERIFICAR QUE EL USUARIO EXISTA
==================================================== */

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



/* ====================================================
   VERIFICAR SI EL CORREO CAMBIO
   Y SI YA ESTA REGISTRADO POR OTRO USUARIO
==================================================== */

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



/* ====================================================
   ACTUALIZAR DATOS DEL USUARIO
   Se verifica si se actualizará la contraseña
==================================================== */

$actualizar_contraseña = !empty($contraseña);


if ($actualizar_contraseña) {

    // Validar longitud de contraseña
    if (strlen($contraseña) < 6) {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'La contraseña debe tener al menos 6 caracteres'
        ]);
        exit();
    }

    // Encriptar contraseña
    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

    $query_update = "UPDATE usuarios SET nombre = ?, correo = ?, contraseña = ?, id_rol = ?, estado = ? 
                    WHERE id_usuario = ?";

    $stmt = $conexion->prepare($query_update);

    $stmt->bind_param("sssisi", $nombre, $correo, $contraseña_hash, $id_rol, $estado, $id_usuario);

} else {

    // Actualizar datos sin modificar contraseña
    $query_update = "UPDATE usuarios SET nombre = ?, correo = ?, id_rol = ?, estado = ? 
                    WHERE id_usuario = ?";

    $stmt = $conexion->prepare($query_update);

    $stmt->bind_param("ssisi", $nombre, $correo, $id_rol, $estado, $id_usuario);
}



/* ====================================================
   EJECUTAR ACTUALIZACION
==================================================== */

if ($stmt->execute()) {

    /* ===============================================
       SI EL USUARIO ES CLIENTE
       SE ACTUALIZA TAMBIEN EN TABLA CLIENTES
    =============================================== */

    if ($id_rol == 3) {

        $query_cliente = "UPDATE clientes SET nombre = ? WHERE id_usuario = ?";

        $stmt_cliente = $conexion->prepare($query_cliente);

        $stmt_cliente->bind_param("si", $nombre, $id_usuario);

        $stmt_cliente->execute();

        $stmt_cliente->close();
    }

    $stmt->close();


    /* ===============================================
       RESPUESTA EXITOSA
    =============================================== */

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