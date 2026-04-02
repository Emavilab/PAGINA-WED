<?php
/*
========================================================
API: CREAR PEDIDO DESDE EL CARRITO
========================================================

Este archivo procesa la creación de un pedido dentro del
sistema de tienda en línea.

FUNCIONALIDADES PRINCIPALES:
✔ Verificar autenticación del usuario
✔ Obtener datos del cliente desde la sesión
✔ Validar datos enviados por formulario (POST)
✔ Subir comprobante de pago (opcional)
✔ Obtener carrito activo del cliente
✔ Validar stock de productos
✔ Calcular subtotal, impuestos y costos de envío
✔ Insertar pedido en la base de datos
✔ Insertar detalle de productos del pedido
✔ Actualizar stock de productos
✔ Vaciar carrito después de la compra
✔ Manejar transacciones para evitar errores de datos

TABLAS UTILIZADAS:
- carritos
- carrito_detalle
- productos
- direcciones_cliente
- departamentos_envio
- metodos_envio
- pedidos
- detalle_pedido

RESPUESTA DE LA API:

Éxito:
{
  "exito": true,
  "id_pedido": 123
}

Error:
{
  "exito": false,
  "error": "Descripción del error"
}

AUTOR: Sistema de Tienda Online
========================================================
*/

require_once '../core/sesiones.php';
require_once '../core/conexion.php';
require_once '../core/csrf.php';

validarCSRFMiddleware();

/*
========================================================
CONFIGURAR RESPUESTA JSON
========================================================
*/
header('Content-Type: application/json; charset=utf-8');

/*
========================================================
VERIFICAR AUTENTICACIÓN DEL USUARIO
========================================================
Se valida que el usuario tenga sesión activa antes de
permitir realizar un pedido.
*/
if (!usuarioAutenticado()) {
    echo json_encode(["exito" => false, "error" => "No autorizado"]);
    exit;
}

// Obtener datos del usuario desde la sesión
$usuario = obtenerDatosUsuario();

$id_usuario = (int)($usuario['id'] ?? ($_SESSION['id_usuario'] ?? ($_SESSION['id'] ?? 0)));

if ($id_usuario <= 0) {
    echo json_encode(["exito" => false, "error" => "Sesión inválida"]);
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
} else {
    echo json_encode(["exito" => false, "error" => "Cliente no encontrado"]);
    exit;
}
$stmt_cliente->close();

/*
========================================================
VALIDAR DATOS RECIBIDOS DEL FORMULARIO
========================================================
Se verifica que los datos obligatorios existan.
*/
if (
    !isset($_POST['id_direccion']) ||
    !isset($_POST['id_metodo_pago'])
) {
    echo json_encode(["exito" => false, "error" => "Datos incompletos"]);
    exit;
}

// Convertir datos recibidos
$id_direccion = intval($_POST['id_direccion']);
$id_envio = !empty($_POST['id_envio']) ? intval($_POST['id_envio']) : null;
$id_metodo_pago = intval($_POST['id_metodo_pago']);

$nombreComprobante = null;

/*
================================
SUBIR COMPROBANTE DE PAGO
================================
Se permite subir una imagen como comprobante de pago.
Se valida:
✔ tamaño máximo
✔ tipo de archivo
✔ almacenamiento en servidor
*/

if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === 0) {

    $archivo = $_FILES['comprobante'];

    // Validar tamaño máximo de 3MB
    if ($archivo['size'] > 3 * 1024 * 1024) {
        echo json_encode(["exito" => false, "error" => "El comprobante supera los 3MB"]);
        exit;
    }

    // Tipos permitidos de imagen
    $tiposPermitidos = ['image/jpeg','image/png','image/webp'];

    // Obtener tipo real del archivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipoReal = finfo_file($finfo, $archivo['tmp_name']);
    finfo_close($finfo);

    // Validar formato
    if (!in_array($tipoReal, $tiposPermitidos)) {
        echo json_encode(["exito" => false, "error" => "Formato no permitido"]);
        exit;
    }

    // Generar nombre único para el archivo
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    
    // Validar extensión
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($extension, $extensionesPermitidas)) {
        echo json_encode(["exito" => false, "error" => "Extensión de archivo no permitida"]);
        exit;
    }
    
    $nombreComprobante = uniqid("comp_") . "_" . time() . "." . $extension;

    // Ruta donde se guardará el comprobante
    $rutaDestino = "../img/comprobantes/" . $nombreComprobante;

    // Crear directorio si no existe
    if (!is_dir("../img/comprobantes")) {
        mkdir("../img/comprobantes", 0755, true);
    }

    // Guardar archivo en el servidor
    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        echo json_encode(["exito" => false, "error" => "Error al guardar comprobante"]);
        exit;
    }
}

try {

    /*
    ================================================
    INICIAR TRANSACCIÓN
    ================================================
    Permite asegurar que todas las operaciones se
    ejecuten correctamente o se cancelen en caso de error.
    */
    $conexion->begin_transaction();

    /*
    ================================================
    OBTENER CARRITO ACTIVO DEL CLIENTE
    ================================================
    */
    $stmt = $conexion->prepare("
        SELECT id_carrito
        FROM carritos
        WHERE id_cliente = ? AND estado = 'activo'
        LIMIT 1
    ");
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $carrito = $resultado->fetch_assoc();

    if (!$carrito) {
        throw new Exception("Carrito vacío");
    }

    $id_carrito = $carrito['id_carrito'];

    /*
    ================================================
    OBTENER PRODUCTOS DEL CARRITO
    ================================================
    Se consultan los productos agregados al carrito
    junto con el stock disponible.
    */
    $stmt = $conexion->prepare("
        SELECT cd.*, p.stock
        FROM carrito_detalle cd
        JOIN productos p ON p.id_producto = cd.id_producto
        WHERE cd.id_carrito = ?
    ");
    $stmt->bind_param("i", $id_carrito);
    $stmt->execute();
    $detalles = $stmt->get_result();

    if ($detalles->num_rows == 0) {
        throw new Exception("Carrito sin productos");
    }

    // Variables para cálculos
    $subtotal = 0;
    $impuesto_total = 0;
    $items = [];

    /*
    ================================================
    VALIDAR STOCK Y CALCULAR TOTALES
    ================================================
    */
    while ($item = $detalles->fetch_assoc()) {

        if ($item['stock'] < $item['cantidad']) {
            throw new Exception("Stock insuficiente");
        }

        $subtotal += $item['subtotal'];

        // Impuesto del 15%
        $impuesto_total += ($item['subtotal'] * 0.15);

        $items[] = $item;
    }

    /*
================================
OBTENER COSTO DE ENVÍO POR DEPARTAMENTO
================================
*/

$stmtEnvio = $conexion->prepare("
    SELECT de.costo_envio
    FROM direcciones_cliente dc
    JOIN departamentos_envio de 
    ON dc.id_departamento = de.id_departamento
    WHERE dc.id_direccion = ?
");

$stmtEnvio->bind_param("i", $id_direccion);
$stmtEnvio->execute();
$resEnvio = $stmtEnvio->get_result();
$rowEnvio = $resEnvio->fetch_assoc();

$envio_departamento = $rowEnvio ? $rowEnvio['costo_envio'] : 0;

/*
================================
OBTENER COSTO DE ENVÍO POR MÉTODO
================================
*/

$envio_metodo = 0;
if ($id_envio) {

    $stmtMetodo = $conexion->prepare("
        SELECT costo
        FROM metodos_envio
        WHERE id_envio = ?
    ");

    $stmtMetodo->bind_param("i", $id_envio);
    $stmtMetodo->execute();
    $resMetodo = $stmtMetodo->get_result();
    $rowMetodo = $resMetodo->fetch_assoc();

    $envio_metodo = $rowMetodo ? $rowMetodo['costo'] : 0;
}

/*
================================
CALCULAR TOTAL DEL PEDIDO
================================
*/

$total = $subtotal + $impuesto_total + $envio_departamento + $envio_metodo;

    /*
    ================================================
    INSERTAR PEDIDO EN BASE DE DATOS
    ================================================
    */

    $stmt = $conexion->prepare("
        INSERT INTO pedidos 
        (subtotal, envio_departamento, impuesto_total, total, id_cliente, id_direccion, id_envio, id_metodo_pago, comprobante_pago, fecha_pedido)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?,?, NOW())
    ");

    $stmt->bind_param(
        "ddddiiiis",
        $subtotal,
        $envio_departamento,
        $impuesto_total,
        $total,
        $id_cliente,
        $id_direccion,
        $id_envio,
        $id_metodo_pago,
        $nombreComprobante
    );

    $stmt->execute();

    // Obtener ID del pedido recién creado
    $id_pedido = $conexion->insert_id;

    /*
    ================================================
    INSERTAR DETALLE DEL PEDIDO
    ================================================
    */

    foreach ($items as $item) {

        $monto_impuesto = $item['subtotal'] * 0.15;

        $stmt = $conexion->prepare("
            INSERT INTO detalle_pedido
            (cantidad, precio_unitario, subtotal, tasa_impuesto, monto_impuesto, id_pedido, id_producto)
            VALUES (?, ?, ?, 15, ?, ?, ?)
        ");

        $stmt->bind_param(
            "idddii",
            $item['cantidad'],
            $item['precio_unitario'],
            $item['subtotal'],
            $monto_impuesto,
            $id_pedido,
            $item['id_producto']
        );

        $stmt->execute();

        /*
        ============================================
        ACTUALIZAR STOCK DE PRODUCTOS
        ============================================
        */

        $stmtStock = $conexion->prepare("
            UPDATE productos
            SET stock = stock - ?
            WHERE id_producto = ?
        ");

        $stmtStock->bind_param("ii", $item['cantidad'], $item['id_producto']);
        $stmtStock->execute();
    }

    /*
    ================================================
    LIMPIAR CARRITO DEL CLIENTE
    ================================================
    */

    // Usar prepared statements para evitar SQL injection
    $stmtLimpiar = $conexion->prepare("DELETE FROM carrito_detalle WHERE id_carrito = ?");
    if ($stmtLimpiar) {
        $stmtLimpiar->bind_param("i", $id_carrito);
        $stmtLimpiar->execute();
        $stmtLimpiar->close();
    }

    // Actualizar estado del carrito
    $stmtCarrito = $conexion->prepare("UPDATE carritos SET estado = 'comprado' WHERE id_carrito = ?");
    if ($stmtCarrito) {
        $stmtCarrito->bind_param("i", $id_carrito);
        $stmtCarrito->execute();
        $stmtCarrito->close();
    }

    // Eliminar carrito de la sesión
    unset($_SESSION['carrito']);

    /*
    ================================================
    CONFIRMAR TRANSACCIÓN
    ================================================
    */

    $conexion->commit();

    echo json_encode(["exito" => true, "id_pedido" => $id_pedido]);
    exit;

} catch (Exception $e) {

    /*
    ================================================
    CANCELAR TRANSACCIÓN SI OCURRE ERROR
    ================================================
    */

    $conexion->rollback();

    echo json_encode([
        "exito" => false,
        "error" => $e->getMessage()
    ]);
    exit;
} 