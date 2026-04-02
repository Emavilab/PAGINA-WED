<?php
/*
========================================================
MODULO: CREAR DIRECCIÓN DEL CLIENTE
========================================================
Este archivo funciona como una API que permite a un
usuario autenticado registrar una nueva dirección
de envío en el sistema.

FUNCIONALIDADES:
✔ Verificar que el usuario esté autenticado
✔ Obtener los datos del cliente desde la sesión
✔ Validar los datos enviados desde el formulario
✔ Verificar que el departamento exista en la base de datos
✔ Insertar la nueva dirección en la tabla direcciones_cliente
✔ Retornar respuesta en formato JSON

TABLAS UTILIZADAS:
- direcciones_cliente
- departamentos_envio

RESPUESTA DEL SERVIDOR:

Éxito:
{
  "success": true,
  "message": "Dirección guardada correctamente"
}

Error:
{
  "success": false,
  "message": "Descripción del error"
}

AUTOR: Sistema de Tienda Online
========================================================
*/

// Definir tipo de respuesta JSON y codificación
header('Content-Type: application/json; charset=utf-8');

// Incluir conexión a base de datos y sistema de sesiones
require_once '../core/conexion.php';
require_once '../core/sesiones.php';
require_once '../core/csrf.php';

validarCSRFMiddleware();

/*
========================================================
CONFIGURACIÓN DE ERRORES (SOLO DESARROLLO)
========================================================
Se muestran errores de PHP para facilitar depuración.
En producción se recomienda desactivarlo.
*/
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

try {

    /*
    ====================================================
    VERIFICAR AUTENTICACIÓN DEL USUARIO
    ====================================================
    Se valida que el usuario tenga sesión activa.
    */
    if (!usuarioAutenticado()) {
        echo json_encode([
            'success' => false,
            'message' => 'Usuario no autenticado'
        ]);
        exit;
    }

    /*
    ====================================================
    OBTENER DATOS DEL USUARIO
    ====================================================
    Se obtienen los datos del usuario desde la sesión
    para identificar el cliente.
    */
    $usuario = obtenerDatosUsuario();

    if (!$usuario) {
        echo json_encode([
            'success' => false,
            'message' => 'Debes iniciar sesión para crear una dirección'
        ]);
        exit;
    }

    // Obtener ID de usuario (compatibilidad de sesión)
    $id_usuario = (int)($usuario['id'] ?? ($_SESSION['id_usuario'] ?? ($_SESSION['id'] ?? 0)));

    if ($id_usuario <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Sesión inválida de usuario'
        ]);
        exit;
    }

    // Obtener ID del cliente desde la tabla clientes
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
        // Si no existe cliente asociado, crearlo automáticamente
        $nombre_cliente = trim((string)($usuario['nombre'] ?? ($_SESSION['nombre'] ?? 'Cliente')));
        if ($nombre_cliente === '') {
            $nombre_cliente = 'Cliente';
        }

        $stmt_crear_cliente = $conexion->prepare("INSERT INTO clientes (id_usuario, nombre, estado) VALUES (?, ?, 'activo')");
        if (!$stmt_crear_cliente) {
            throw new Exception("Error en prepare crear cliente: " . $conexion->error);
        }

        $stmt_crear_cliente->bind_param("is", $id_usuario, $nombre_cliente);
        if (!$stmt_crear_cliente->execute()) {
            throw new Exception("Error al crear cliente asociado: " . $stmt_crear_cliente->error);
        }

        $id_cliente = (int)$stmt_crear_cliente->insert_id;
        $stmt_crear_cliente->close();
    }

    /*
    ====================================================
    OBTENER DATOS DEL FORMULARIO (POST)
    ====================================================
    Se reciben los datos enviados desde el formulario
    de registro de dirección.
    */
    $direccion       = trim($_POST['direccion'] ?? '');
    $ciudad          = trim($_POST['ciudad'] ?? '');
    $codigo_postal   = trim($_POST['codigo_postal'] ?? '');
    $telefono        = trim($_POST['telefono'] ?? '');
    $referencia      = trim($_POST['referencia'] ?? '');
    $id_departamento = isset($_POST['id_departamento']) ? (int) $_POST['id_departamento'] : 0;

    /*
    ====================================================
    VALIDACIÓN DE DATOS
    ====================================================
    Se valida que los campos obligatorios estén
    completos antes de continuar.
    */
    if (empty($direccion) || empty($ciudad)) {
        echo json_encode([
            'success' => false,
            'message' => 'Dirección y ciudad son obligatorias'
        ]);
        exit;
    }

    if ($id_departamento <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'El departamento es obligatorio'
        ]);
        exit;
    }

    /*
    ====================================================
    VALIDAR EXISTENCIA DEL DEPARTAMENTO
    ====================================================
    Se verifica que el departamento seleccionado exista
    dentro de la tabla departamentos_envio.
    */
    $stmtDep = $conexion->prepare("SELECT id_departamento FROM departamentos_envio WHERE id_departamento = ?");

    $stmtDep->bind_param("i", $id_departamento);

    $stmtDep->execute();

    $resDep = $stmtDep->get_result();

    if ($resDep->num_rows === 0) {

        $stmtDep->close();

        echo json_encode([
            'success' => false,
            'message' => 'Departamento no válido'
        ]);

        exit;
    }

    $stmtDep->close();


    /*
    ====================================================
    INSERTAR DIRECCIÓN EN BASE DE DATOS
    ====================================================
    Se guarda la nueva dirección asociada al cliente.
    */
    $query = "INSERT INTO direcciones_cliente 
              (id_cliente, direccion, ciudad, codigo_postal, telefono, referencia, id_departamento, activo)
              VALUES (?, ?, ?, ?, ?, ?, ?, 1)";

    $stmt = $conexion->prepare($query);

    // Verificar que la consulta preparada sea válida
    if (!$stmt) {
        throw new Exception("Error en prepare: " . $conexion->error);
    }

    /*
    ====================================================
    ASIGNAR PARÁMETROS A LA CONSULTA
    ====================================================
    Se vinculan los datos del formulario a la consulta
    preparada para evitar inyección SQL. activo = 1 para
    que la nueva dirección sea visible (soft delete).
    */
    $stmt->bind_param(
        "isssssi",
        $id_cliente,
        $direccion,
        $ciudad,
        $codigo_postal,
        $telefono,
        $referencia,
        $id_departamento
    );

    // Ejecutar inserción
    if (!$stmt->execute()) {
        throw new Exception("Error en execute: " . $stmt->error);
    }

    $stmt->close();


    /*
    ====================================================
    RESPUESTA EXITOSA
    ====================================================
    Se devuelve un mensaje indicando que la dirección
    fue registrada correctamente.
    */
    echo json_encode([
        'success' => true,
        'message' => 'Dirección guardada correctamente'
    ]);

} catch (Exception $e) {

    /*
    ====================================================
    MANEJO DE ERRORES DEL SERVIDOR
    ====================================================
    Captura cualquier excepción y devuelve un mensaje
    de error en formato JSON.
    */
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor',
        'error'   => $e->getMessage()
    ]);
} 
