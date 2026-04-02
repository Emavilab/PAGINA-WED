<?php

/*
========================================================
MODULO: GESTION DE BANCOS
========================================================

Este archivo se encarga de administrar los bancos
registrados dentro del sistema.

FUNCIONES PRINCIPALES:
✔ Crear un nuevo banco
✔ Editar información de un banco
✔ Subir logo del banco
✔ Eliminar un banco
✔ Validar que el usuario tenga permisos

PERMISOS:
Solo pueden acceder usuarios con rol:
- 1 (Administrador)
- 2 (Supervisor o encargado)

RESPUESTAS:
El sistema responde en formato JSON para ser utilizado
por peticiones AJAX del sistema administrativo.

AUTOR: Sistema Web
========================================================
*/


/* ====================================================
   CARGA DEL SISTEMA DE SESIONES
   Se utiliza para verificar que el usuario esté logueado
   y que tenga permisos para usar este módulo
==================================================== */

require_once '../core/sesiones.php';


/* ====================================================
   VALIDACION DE ACCESO
   Si el usuario no está autenticado o no tiene el rol
   adecuado, el sistema devuelve un error y se detiene
==================================================== */

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    echo json_encode(["status"=>"error","msg"=>"No autorizado"]);
    exit();
}

require_once '../core/csrf.php';
validarCSRFMiddleware();


/* ====================================================
   CONEXION A LA BASE DE DATOS
==================================================== */

require_once '../core/conexion.php';


/* ====================================================
   CAPTURA DE ACCION
   Se obtiene la acción enviada desde el formulario
   mediante POST para decidir qué operación ejecutar
==================================================== */

$accion = $_POST['accion'] ?? '';



/* ====================================================
   GUARDAR O EDITAR BANCO
==================================================== */

if($accion == "guardar_banco"){


/* ====================================================
   RECEPCION DE DATOS DEL FORMULARIO
==================================================== */

$id_banco = intval($_POST['id_banco'] ?? 0);

// Validación: nombre debe tener entre 2 y 100 caracteres
$nombre = trim($_POST['nombre'] ?? '');
if (strlen($nombre) < 2 || strlen($nombre) > 100) {
    echo json_encode(["status"=>"error","msg"=>"El nombre del banco debe tener entre 2 y 100 caracteres"]);
    exit();
}

// Validación: número de cuenta
$numero_cuenta = trim($_POST['numero_cuenta'] ?? '');
if (strlen($numero_cuenta) < 5 || strlen($numero_cuenta) > 50) {
    echo json_encode(["status"=>"error","msg"=>"El número de cuenta debe tener entre 5 y 50 caracteres"]);
    exit();
}

$id_tipo_cuenta = intval($_POST['id_tipo_cuenta']);
if ($id_tipo_cuenta <= 0) {
    echo json_encode(["status"=>"error","msg"=>"Tipo de cuenta inválido"]);
    exit();
}


/* variable donde se guardará el logo */

$logo = "";



/* ====================================================
   SUBIDA DEL LOGO DEL BANCO
   Solo permite imágenes con validación MIME
==================================================== */

if(!empty($_FILES['logo']['name'])){

// Validación de tamaño
$max_size = 2 * 1024 * 1024; // 2MB
if ($_FILES['logo']['size'] > $max_size) {
    echo json_encode(["status"=>"error","msg"=>"El archivo es demasiado grande (máximo 2MB)"]);
    exit();
}

// Validación de MIME type
$mime_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['logo']['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $mime_types)) {
    echo json_encode(["status"=>"error","msg"=>"Solo se permiten imágenes (JPEG, PNG, WebP, GIF)"]);
    exit();
}

// Validar extensión
$extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
$allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
if (!in_array($extension, $allowed_ext)) {
    echo json_encode(["status"=>"error","msg"=>"Extensión de archivo no permitida"]);
    exit();
}

// Generar nombre único
$nombre_archivo = uniqid('banco_') . '_' . time() . '.' . $extension;
$ruta = "../img/bancos/" . $nombre_archivo;

// Crear directorio si no existe
if (!is_dir("../img/bancos")) {
    mkdir("../img/bancos", 0755, true);
}

// Mover archivo al servidor
if(!move_uploaded_file($_FILES["logo"]["tmp_name"], $ruta)){
    echo json_encode(["status"=>"error","msg"=>"Error al subir el archivo"]);
    exit();
}

// Guardar nombre del archivo
$logo = $nombre_archivo;

}



/* ====================================================
   INSERTAR NUEVO BANCO - CON PREPARED STATEMENT
   Se ejecuta cuando id_banco = 0
==================================================== */

if($id_banco == 0){

$sql = "INSERT INTO bancos (nombre, numero_cuenta, id_tipo_cuenta, logo)
        VALUES (?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    error_log("Error preparando INSERT bancos: " . $conexion->error);
    echo json_encode(["status"=>"error","msg"=>"Error en el servidor"]);
    exit();
}

$stmt->bind_param("ssis", $nombre, $numero_cuenta, $id_tipo_cuenta, $logo);

if (!$stmt->execute()) {
    error_log("Error ejecutando INSERT bancos: " . $stmt->error);
    echo json_encode(["status"=>"error","msg"=>"Error al crear el banco"]);
    $stmt->close();
    exit();
}

$stmt->close();

}



/* ====================================================
   ACTUALIZAR BANCO EXISTENTE - CON PREPARED STATEMENT
==================================================== */

else{

// Validar que el banco exista
$check_sql = "SELECT id_banco FROM bancos WHERE id_banco = ?";
$check_stmt = $conexion->prepare($check_sql);
$check_stmt->bind_param("i", $id_banco);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(["status"=>"error","msg"=>"El banco no existe"]);
    $check_stmt->close();
    exit();
}
$check_stmt->close();

// Si se subió un nuevo logo se actualiza también
if($logo != ""){

$sql = "UPDATE bancos 
        SET nombre = ?, numero_cuenta = ?, id_tipo_cuenta = ?, logo = ?
        WHERE id_banco = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    error_log("Error preparando UPDATE bancos: " . $conexion->error);
    echo json_encode(["status"=>"error","msg"=>"Error en el servidor"]);
    exit();
}

$stmt->bind_param("ssiii", $nombre, $numero_cuenta, $id_tipo_cuenta, $logo, $id_banco);

}

/* si no hay nuevo logo solo se actualizan datos */

else{

$sql = "UPDATE bancos 
        SET nombre = ?, numero_cuenta = ?, id_tipo_cuenta = ?
        WHERE id_banco = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    error_log("Error preparando UPDATE bancos: " . $conexion->error);
    echo json_encode(["status"=>"error","msg"=>"Error en el servidor"]);
    exit();
}

$stmt->bind_param("ssii", $nombre, $numero_cuenta, $id_tipo_cuenta, $id_banco);

}

if (!$stmt->execute()) {
    error_log("Error ejecutando UPDATE bancos: " . $stmt->error);
    echo json_encode(["status"=>"error","msg"=>"Error al actualizar el banco"]);
    $stmt->close();
    exit();
}

$stmt->close();

}


/* ====================================================
   RESPUESTA DEL SISTEMA
==================================================== */

echo json_encode(["status"=>"ok"]);

exit();

}




/* ====================================================
   ELIMINAR BANCO - CON PREPARED STATEMENT
   Elimina un banco según su ID
==================================================== */

if($accion == "eliminar_banco"){

/* obtener ID enviado */
$id = intval($_POST['id']);

// Validar que el ID sea válido
if ($id <= 0) {
    echo json_encode(["status"=>"error","msg"=>"ID de banco inválido"]);
    exit();
}

// Verificar que el banco existe
$check_sql = "SELECT id_banco FROM bancos WHERE id_banco = ?";
$check_stmt = $conexion->prepare($check_sql);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(["status"=>"error","msg"=>"El banco no existe"]);
    $check_stmt->close();
    exit();
}
$check_stmt->close();

// Ejecutar eliminación con prepared statement
$delete_sql = "DELETE FROM bancos WHERE id_banco = ?";
$delete_stmt = $conexion->prepare($delete_sql);

if (!$delete_stmt) {
    error_log("Error preparando DELETE bancos: " . $conexion->error);
    echo json_encode(["status"=>"error","msg"=>"Error en el servidor"]);
    exit();
}

$delete_stmt->bind_param("i", $id);

if (!$delete_stmt->execute()) {
    error_log("Error ejecutando DELETE bancos: " . $delete_stmt->error);
    echo json_encode(["status"=>"error","msg"=>"Error al eliminar el banco"]);
    $delete_stmt->close();
    exit();
}

$delete_stmt->close();

/* respuesta del sistema */
echo json_encode(["status"=>"ok"]);
exit();

}