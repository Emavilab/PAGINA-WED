<?php
/*
========================================================
API: ACTUALIZAR DIRECCIÓN DEL CLIENTE
========================================================

Este archivo permite que un cliente autenticado pueda
editar una dirección de envío previamente registrada
en el sistema.

FUNCIONALIDADES:
✔ Verificar autenticación del usuario
✔ Obtener el id_cliente asociado al usuario
✔ Validar datos enviados desde el formulario
✔ Verificar que el departamento exista
✔ Actualizar la dirección en la base de datos
✔ Retornar respuesta en formato JSON

TABLAS UTILIZADAS:
- clientes
- direcciones_cliente
- departamentos_envio

RESPUESTAS POSIBLES:

Éxito:
{
  "success": true,
  "message": "Dirección actualizada correctamente"
}

Error:
{
  "success": false,
  "message": "Descripción del error"
}

AUTOR: Sistema de Tienda Online
========================================================
*/

// Incluir archivos necesarios para sesión y conexión
require_once '../core/sesiones.php';
require_once '../core/conexion.php';
require_once '../core/csrf.php';

validarCSRFMiddleware();

// Definir que la respuesta será en formato JSON
header('Content-Type: application/json; charset=utf-8');

/*
========================================================
VERIFICAR AUTENTICACIÓN DEL USUARIO
========================================================
Se comprueba que el usuario tenga sesión activa.
*/
if (!usuarioAutenticado()) {
    echo json_encode([
        "success" => false,
        "message" => "Usuario no autenticado"
    ]);
    exit();
}

/*
========================================================
OBTENER ID DEL USUARIO AUTENTICADO
========================================================
Se obtiene el id del usuario desde la sesión.
*/
$id_usuario = (int)($_SESSION['id_usuario'] ?? ($_SESSION['id'] ?? 0));

if ($id_usuario <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Sesión inválida"
    ]);
    exit();
}

/*
========================================================
OBTENER ID DEL CLIENTE DESDE LA TABLA CLIENTES
========================================================
Buscar el id_cliente del usuario autenticado.
*/
$id_cliente = null;
$stmt_cliente = $conexion->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
$stmt_cliente->bind_param("i", $id_usuario);
$stmt_cliente->execute();
$resultado = $stmt_cliente->get_result();

if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    $id_cliente = $row['id_cliente'];
}
$stmt_cliente->close();

if (!$id_cliente) {
    echo json_encode([
        "success" => false,
        "message" => "Cliente no encontrado"
    ]);
    exit();
}

/*
========================================================
OBTENER DATOS DEL FORMULARIO
========================================================
Se reciben los datos enviados desde el formulario
para actualizar la dirección.
*/
$id_direccion = $_POST['id_direccion'] ?? null;
$direccion = trim($_POST['direccion'] ?? '');
$ciudad = trim($_POST['ciudad'] ?? '');
$codigo_postal = trim($_POST['codigo_postal'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$referencia = trim($_POST['referencia'] ?? '');
$id_departamento = isset($_POST['id_departamento']) ? (int) $_POST['id_departamento'] : 0;

/*
========================================================
VALIDACIÓN DE DATOS OBLIGATORIOS
========================================================
Se valida que los campos necesarios estén completos.
*/
if (!$id_direccion || !$direccion || !$ciudad) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos"
    ]);
    exit();
}

/*
========================================================
VALIDAR QUE EL DEPARTAMENTO SEA VÁLIDO
========================================================
El departamento debe existir en la tabla
departamentos_envio.
*/
if ($id_departamento <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "El departamento es obligatorio"
    ]);
    exit();
}

/*
========================================================
VERIFICAR EXISTENCIA DEL DEPARTAMENTO
========================================================
Se consulta la tabla departamentos_envio para asegurar
que el ID seleccionado existe.
*/
$stmtDep = $conexion->prepare("SELECT id_departamento FROM departamentos_envio WHERE id_departamento = ?");
$stmtDep->bind_param("i", $id_departamento);
$stmtDep->execute();
$resDep = $stmtDep->get_result();

if ($resDep->num_rows === 0) {
    $stmtDep->close();
    echo json_encode([
        "success" => false,
        "message" => "Departamento no válido"
    ]);
    exit();
}

$stmtDep->close();

/*
========================================================
ACTUALIZAR DIRECCIÓN EN BASE DE DATOS
========================================================
Se utiliza una consulta preparada para evitar
inyección SQL.
*/
try {

    $stmt = $conexion->prepare("
        UPDATE direcciones_cliente
        SET direccion = ?, ciudad = ?, codigo_postal = ?, telefono = ?, referencia = ?, id_departamento = ?
        WHERE id_direccion = ? AND id_cliente = ?
    ");

    /*
    ================================================
    ASIGNAR PARÁMETROS A LA CONSULTA
    ================================================
    */
    $stmt->bind_param(
        "sssssiii",
        $direccion,
        $ciudad,
        $codigo_postal,
        $telefono,
        $referencia,
        $id_departamento,
        $id_direccion,
        $id_cliente
    );

    // Ejecutar actualización
    $stmt->execute();

    /*
    ================================================
    VERIFICAR SI SE ACTUALIZÓ ALGUNA FILA
    ================================================
    */
    if ($stmt->affected_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "No se pudo actualizar la dirección"
        ]);
        exit();
    }

    /*
    ================================================
    RESPUESTA EXITOSA
    ================================================
    */
    echo json_encode([
        "success" => true,
        "message" => "Dirección actualizada correctamente"
    ]);

} catch (Exception $e) {

    /*
    ================================================
    MANEJO DE ERRORES
    ================================================
    */
    echo json_encode([
        "success" => false,
        "message" => "Error al actualizar"
    ]);
} 