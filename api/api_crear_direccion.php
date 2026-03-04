<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../core/conexion.php';
require_once '../core/sesiones.php';

// Mostrar errores solo para desarrollo (quítalo en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {

    // Verificar autenticación
    if (!usuarioAutenticado()) {
        echo json_encode([
            'success' => false,
            'message' => 'Usuario no autenticado'
        ]);
        exit;
    }

    // Obtener datos del usuario
    $usuario = obtenerDatosUsuario();

    if (!$usuario || !isset($usuario['id_cliente'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Debes iniciar sesión para crear una dirección'
        ]);
        exit;
    }

    $id_cliente = $usuario['id_cliente'];

    // Obtener datos del POST
    $direccion       = trim($_POST['direccion'] ?? '');
    $ciudad          = trim($_POST['ciudad'] ?? '');
    $codigo_postal   = trim($_POST['codigo_postal'] ?? '');
    $telefono        = trim($_POST['telefono'] ?? '');
    $referencia      = trim($_POST['referencia'] ?? '');
    $id_departamento = isset($_POST['id_departamento']) ? (int) $_POST['id_departamento'] : 0;

    // Validación básica
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

    // Validar que id_departamento exista en departamentos_envio
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

    // Insertar en base de datos
    $query = "INSERT INTO direcciones_cliente 
              (id_cliente, direccion, ciudad, codigo_postal, telefono, referencia, id_departamento)
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($query);

    if (!$stmt) {
        throw new Exception("Error en prepare: " . $conexion->error);
    }

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

    if (!$stmt->execute()) {
        throw new Exception("Error en execute: " . $stmt->error);
    }

    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'Dirección guardada correctamente'
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor',
        'error'   => $e->getMessage()
    ]);
}