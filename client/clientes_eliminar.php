<?php

/*
=========================================================
API: ELIMINAR CUENTA DE USUARIO / CLIENTE
=========================================================

DESCRIPCIÓN:
Este script permite eliminar completamente una cuenta de usuario
del sistema junto con todos los datos relacionados en la base de datos.

El proceso elimina:
✔ Usuario
✔ Cliente asociado
✔ Carritos
✔ Detalles de carrito
✔ Pedidos
✔ Detalles de pedidos
✔ Direcciones del cliente
✔ Lista de deseos (si existe)
✔ Mensajes (si existe)
✔ Historial de pedidos (si existe)

La eliminación se realiza dentro de una TRANSACCIÓN para asegurar
la integridad de los datos.

SEGURIDAD:
✔ Solo un usuario autenticado puede ejecutar esta acción.
✔ Un usuario solo puede eliminar su propia cuenta.
✔ Un administrador puede eliminar cualquier cuenta.

RESPUESTA:
El script devuelve una respuesta en formato JSON.

Ejemplo de éxito:
{
  "success": true,
  "message": "Cliente eliminado correctamente"
}

Ejemplo de error:
{
  "success": false,
  "message": "Error al eliminar"
}

REQUISITOS:
- conexión a base de datos (conexion.php)
- manejo de sesiones (sesiones.php)

AUTOR: Sistema de Gestión
=========================================================
*/

require_once '../core/conexion.php';
require_once '../core/sesiones.php';

/*
---------------------------------------------------------
CONFIGURAR RESPUESTA COMO JSON
---------------------------------------------------------
*/
header('Content-Type: application/json');

/*
---------------------------------------------------------
VERIFICAR AUTENTICACIÓN DEL USUARIO
---------------------------------------------------------
Si el usuario no está autenticado se cancela la operación.
*/
if (!usuarioAutenticado()) {
    echo json_encode(["success" => false, "message" => "Usuario no autenticado"]);
    exit();
}

/*
---------------------------------------------------------
OBTENER DATOS DEL CUERPO DE LA PETICIÓN
---------------------------------------------------------
Se leen los datos enviados en formato JSON.
*/
$data = json_decode(file_get_contents("php://input"), true);

/*
---------------------------------------------------------
OBTENER ID DEL USUARIO A ELIMINAR
---------------------------------------------------------
*/
$id_usuario = intval($data['id'] ?? 0);

/*
---------------------------------------------------------
VALIDAR ID DEL USUARIO
---------------------------------------------------------
*/
if ($id_usuario <= 0) {
    echo json_encode(["success" => false, "message" => "ID inválido"]);
    exit();
}

/*
---------------------------------------------------------
VERIFICAR PERMISOS
---------------------------------------------------------
Un usuario solo puede eliminar su propia cuenta.
Un administrador puede eliminar cualquier cuenta.
*/
$es_admin = isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1;

if ($id_usuario !== $_SESSION['id_usuario'] && !$es_admin) {
    echo json_encode(["success" => false, "message" => "No tienes permisos para eliminar esta cuenta"]);
    exit();
}

try {

    /*
    -----------------------------------------------------
    INICIAR TRANSACCIÓN
    -----------------------------------------------------
    */
    $conexion->begin_transaction();
    
    /*
    -----------------------------------------------------
    DESACTIVAR FOREIGN KEY CHECKS
    -----------------------------------------------------
    Permite eliminar registros dependientes manualmente.
    */
    $conexion->query("SET FOREIGN_KEY_CHECKS=0");
    
    /*
    -----------------------------------------------------
    OBTENER ID DEL CLIENTE ASOCIADO AL USUARIO
    -----------------------------------------------------
    */
    $stmtCli = $conexion->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
    $stmtCli->bind_param("i", $id_usuario);
    $stmtCli->execute();
    $resCli = $stmtCli->get_result();
    
    $id_cliente = null;

    if ($filaCli = $resCli->fetch_assoc()) {
        $id_cliente = (int)$filaCli['id_cliente'];
    }

    $stmtCli->close();
    
    /*
    =====================================================
    ELIMINAR TODA LA INFORMACIÓN RELACIONADA AL CLIENTE
    =====================================================
    */
    if ($id_cliente) {

        /*
        -------------------------------------------------
        ELIMINAR DETALLES DE CARRITO
        -------------------------------------------------
        */
        $stmt = $conexion->prepare("DELETE FROM carrito_detalle WHERE id_carrito IN (SELECT id_carrito FROM carritos WHERE id_cliente = ?)");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();
        
        /*
        -------------------------------------------------
        ELIMINAR CARRITOS
        -------------------------------------------------
        */
        $stmt = $conexion->prepare("DELETE FROM carritos WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();
        
        /*
        -------------------------------------------------
        ELIMINAR DETALLES DE PEDIDOS
        -------------------------------------------------
        */
        $stmt = $conexion->prepare("DELETE FROM detalle_pedido WHERE id_pedido IN (SELECT id_pedido FROM pedidos WHERE id_cliente = ?)");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();
        
        /*
        -------------------------------------------------
        ELIMINAR PEDIDOS
        -------------------------------------------------
        */
        $stmt = $conexion->prepare("DELETE FROM pedidos WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();
        
        /*
        -------------------------------------------------
        ELIMINAR DIRECCIONES DEL CLIENTE
        -------------------------------------------------
        */
        $stmt = $conexion->prepare("DELETE FROM direcciones_cliente WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();
        
        /*
        -------------------------------------------------
        ELIMINAR LISTA DE DESEOS (SI EXISTE)
        -------------------------------------------------
        */
        $tableExists = $conexion->query("SHOW TABLES LIKE 'lista_deseos'");
        if ($tableExists && $tableExists->num_rows > 0) {
            $stmt = $conexion->prepare("DELETE FROM lista_deseos WHERE id_cliente = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id_cliente);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        /*
        -------------------------------------------------
        ELIMINAR MENSAJES (SI EXISTE)
        -------------------------------------------------
        */
        $tableExists = $conexion->query("SHOW TABLES LIKE 'mensajes'");
        if ($tableExists && $tableExists->num_rows > 0) {
            $stmt = $conexion->prepare("DELETE FROM mensajes WHERE id_usuario = ?");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $stmt->close();
        }
        
        /*
        -------------------------------------------------
        ELIMINAR HISTORIAL DE PEDIDOS (SI EXISTE)
        -------------------------------------------------
        */
        $tableExists = $conexion->query("SHOW TABLES LIKE 'historial_pedido'");
        if ($tableExists && $tableExists->num_rows > 0) {
            $stmt = $conexion->prepare("DELETE FROM historial_pedido WHERE id_usuario = ?");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $stmt->close();
        }
        
        /*
        -------------------------------------------------
        ELIMINAR REGISTRO DEL CLIENTE
        -------------------------------------------------
        */
        $stmt = $conexion->prepare("DELETE FROM clientes WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();
    }
    
    /*
    -----------------------------------------------------
    ELIMINAR USUARIO DEL SISTEMA
    -----------------------------------------------------
    */
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();
    
    /*
    -----------------------------------------------------
    REACTIVAR FOREIGN KEY CHECKS
    -----------------------------------------------------
    */
    $conexion->query("SET FOREIGN_KEY_CHECKS=1");
    
    /*
    -----------------------------------------------------
    CONFIRMAR TRANSACCIÓN
    -----------------------------------------------------
    */
    $conexion->commit();
    
    /*
    -----------------------------------------------------
    DESTRUIR SESIÓN SI EL USUARIO SE ELIMINA A SÍ MISMO
    -----------------------------------------------------
    */
    $destruir_sesion = ($id_usuario === $_SESSION['id_usuario']);
    if ($destruir_sesion) {
        session_destroy();
    }
    
    /*
    -----------------------------------------------------
    RESPUESTA EXITOSA
    -----------------------------------------------------
    */
    echo json_encode([
        "success" => true, 
        "message" => "Cliente eliminado correctamente", 
        "redirect" => $destruir_sesion ? "/index.php" : null
    ]);
    
} catch (Exception $e) {

    /*
    -----------------------------------------------------
    MANEJO DE ERRORES
    -----------------------------------------------------
    */
    
    // Reactivar foreign key checks en caso de error
    $conexion->query("SET FOREIGN_KEY_CHECKS=1");
    
    // Revertir cambios
    $conexion->rollback();
    
    echo json_encode([
        "success" => false,
        "message" => "Error al eliminar: " . $e->getMessage()
    ]);
}

?> 