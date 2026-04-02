<?php
/*
========================================================
MODULO: ELIMINAR USUARIO DESDE PANEL ADMINISTRATIVO
========================================================

Este archivo procesa la eliminación de usuarios dentro
del sistema administrativo.

FUNCIONES PRINCIPALES:
✔ Validar que el usuario esté autenticado
✔ Verificar permisos (solo administrador puede eliminar)
✔ Validar el ID del usuario a eliminar
✔ Evitar que el usuario elimine su propia cuenta
✔ Verificar que el usuario exista en la base de datos
✔ Eliminar todos los datos relacionados con el usuario
✔ Utilizar transacciones para mantener integridad de datos
✔ Retornar respuesta en formato JSON para AJAX

PROCESO DE ELIMINACION:
1. Verificar usuario
2. Buscar cliente asociado
3. Eliminar dependencias:
   - Carritos
   - Detalles de carrito
   - Pedidos
   - Detalles de pedidos
   - Direcciones
   - Lista de deseos (si existe)
   - Mensajes (si existe)
   - Historial de pedidos (si existe)
4. Eliminar cliente
5. Eliminar usuario

SEGURIDAD:
✔ Uso de prepared statements
✔ Uso de transacciones
✔ Validación de permisos
✔ Control de foreign keys

AUTOR: Sistema Web
========================================================
*/


/* ====================================================
   CONEXION A LA BASE DE DATOS
==================================================== */
require_once '../core/conexion.php';
require_once '../core/audit_logging.php';
require_once '../core/csrf.php';
validarCSRFMiddleware();
require_once '../core/eliminacion_usuario.php';


/* ====================================================
   SISTEMA DE SESIONES
   Permite validar autenticación del usuario
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
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);


/* ====================================================
   VALIDAR AUTENTICACION Y PERMISOS
   Solo administrador (rol 1) puede eliminar usuarios
==================================================== */
if (!usuarioAutenticado() || $_SESSION['id_rol'] != 1) {
    http_response_code(403);
    echo json_encode(['exito' => false, 'mensaje' => 'No tienes permisos para eliminar usuarios']);
    exit();
}


/* ====================================================
   VALIDAR QUE LA PETICION SEA POST
==================================================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit();
}


/* ====================================================
   OBTENER ID DEL USUARIO A ELIMINAR
==================================================== */
$id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;

if ($id_usuario <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'ID de usuario inválido']);
    exit();
}


/* ====================================================
   EVITAR QUE EL USUARIO ELIMINE SU PROPIA CUENTA
==================================================== */
if (
   (isset($_SESSION['id']) && $id_usuario === (int)$_SESSION['id']) ||
   (isset($_SESSION['id_usuario']) && $id_usuario === (int)$_SESSION['id_usuario'])
) {
    echo json_encode(['exito' => false, 'mensaje' => 'No puedes eliminar tu propia cuenta']);
    exit();
}


/* ====================================================
   VERIFICAR QUE EL USUARIO EXISTA
==================================================== */
try {
    $resultadoEliminacion = eliminarUsuarioEnCascada($conexion, $id_usuario);

    $nombre_usuario = $resultadoEliminacion['nombre'] ?? '';
    $correo_usuario = $resultadoEliminacion['correo'] ?? '';

    // REGISTRAR EN AUDIT LOG (SIN QUE CAUSE ERROR SI FALLA)
    try {
        if (function_exists('registrarAudit')) {
            registrarAudit(
                'DELETE',
                'usuarios',
                $id_usuario,
                [
                    'nombre' => $nombre_usuario,
                    'correo' => $correo_usuario,
                    'estado' => 'eliminado'
                ],
                [],
                "Usuario eliminado completamente del sistema: $nombre_usuario"
            );
        }
   } catch (Throwable $auditError) {
        // No fallar si hay error en auditoría
        error_log("Error al registrar auditoría: " . $auditError->getMessage());
    }

    /* ====================================================
       RESPUESTA EXITOSA
    ==================================================== */
    echo json_encode([
        'exito' => true,
        'mensaje' => 'Usuario eliminado exitosamente'
    ]);

} catch (Throwable $e) {


    /* ====================================================
       SI OCURRE UN ERROR:
       - Revertir cambios
    ==================================================== */
    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage()
    ]);
}