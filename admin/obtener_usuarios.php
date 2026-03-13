<?php
/**
 * Obtener Usuarios
 * Retorna lista de usuarios desde la base de datos en formato JSON
 */

require_once '../core/conexion.php';   // Conexión a la base de datos
require_once '../core/sesiones.php';   // Manejo de sesiones y autenticación

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir cabecera para respuesta en formato JSON
header('Content-Type: application/json; charset=utf-8');

// Validar autenticación - Solo admin (rol 1) y vendedor (rol 2) pueden ver usuarios
if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    http_response_code(403); // Código HTTP de acceso prohibido
    echo json_encode(['exito' => false, 'mensaje' => 'No tienes permisos para ver usuarios']);
    exit();
}

try {
    // ================================
    // Obtener parámetros de búsqueda y filtros
    // ================================
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
    $rol_filtro = isset($_GET['rol']) && $_GET['rol'] !== '' ? $_GET['rol'] : '';
    $estado_filtro = isset($_GET['estado']) && $_GET['estado'] !== '' ? $_GET['estado'] : '';
    $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
    $por_pagina = 10; // Número de registros por página
    $offset = ($pagina - 1) * $por_pagina;

    // ================================
    // Construir consulta base
    // ================================
    $query = "SELECT u.id_usuario, u.nombre, u.correo, u.id_rol, r.nombre as nombre_rol, 
                     u.estado, u.fecha_creacion 
              FROM usuarios u 
              LEFT JOIN roles r ON u.id_rol = r.id_rol 
              WHERE 1=1";

    $params = []; // Parámetros dinámicos para consulta
    $types = '';  // Tipos de datos para bind_param

    // Filtro de búsqueda (nombre o correo)
    if (!empty($busqueda)) {
        $query .= " AND (u.nombre LIKE ? OR u.correo LIKE ?)";
        $search_param = "%{$busqueda}%";
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'ss';
    }

    // Filtro de rol
    if (!empty($rol_filtro)) {
        $query .= " AND u.id_rol = ?";
        $params[] = intval($rol_filtro);
        $types .= 'i';
    }

    // Filtro de estado
    if (!empty($estado_filtro)) {
        $query .= " AND u.estado = ?";
        $params[] = $estado_filtro;
        $types .= 's';
    }

    // ================================
    // Contar total de registros
    // ================================
    $count_query = "SELECT COUNT(*) as total FROM usuarios u WHERE 1=1";
    if (!empty($busqueda)) {
        $count_query .= " AND (u.nombre LIKE ? OR u.correo LIKE ?)";
    }
    if (!empty($rol_filtro)) {
        $count_query .= " AND u.id_rol = ?";
    }
    if (!empty($estado_filtro)) {
        $count_query .= " AND u.estado = ?";
    }

    $stmt_count = $conexion->prepare($count_query);
    if (!empty($params)) {
        $stmt_count->bind_param($types, ...$params);
    }
    $stmt_count->execute();
    $total_result = $stmt_count->get_result()->fetch_assoc();
    $total = $total_result['total']; // Total de registros encontrados
    $stmt_count->close();

    // ================================
    // Agregar orden y paginación
    // ================================
    $query .= " ORDER BY u.fecha_creacion DESC LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $por_pagina;
    $types .= 'ii';

    // ================================
    // Ejecutar consulta principal
    // ================================
    $stmt = $conexion->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();

    $usuarios = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row; // Agregar cada usuario al arreglo
    }
    $stmt->close();

    // ================================
    // Respuesta en formato JSON
    // ================================
    echo json_encode([
        'exito' => true,
        'usuarios' => $usuarios,
        'total' => intval($total),
        'pagina' => $pagina,
        'por_pagina' => $por_pagina,
        'total_paginas' => ceil($total / $por_pagina)
    ]);

} catch (Exception $e) {
    // Manejo de errores
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al obtener usuarios: ' . $e->getMessage()
    ]);
}
