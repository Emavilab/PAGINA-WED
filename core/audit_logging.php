<?php
/**
 * SISTEMA DE AUDIT LOGGING - FASE 3-2
 * Registra todas las acciones sensitivas en el sistema
 * 
 * Funcionalidad:
 * - Creación/edición/eliminación de usuarios
 * - Cambios en configuración
 * - Modificaciones en pedidos/pagos
 * - Cambios en productos, categorías
 * - Cambios en métodos de envío/pago
 * - Intentos de acceso no autorizado
 * - Cambio de contraseña
 * - Cambios en roles/permisos
 */

require_once 'conexion.php';

/**
 * Registra una acción en el audit log
 * 
 * @param string $accion Tipo de acción (CREATE, UPDATE, DELETE, LOGIN_FALLIDO, PERMISSION_DENIED, etc)
 * @param string $tabla Tabla afectada (usuarios, pedidos, productos, etc)
 * @param int $registro_id ID del registro modificado
 * @param array $valores_anteriores Array con valores antes del cambio
 * @param array $valores_nuevos Array con valores después del cambio
 * @param string $notas Notas adicionales opcionales
 * @return bool true si se registró, false si error
 */
function registrarAudit($accion, $tabla, $registro_id, $valores_anteriores = [], $valores_nuevos = [], $notas = '') {
    global $conexion;
    
    // Obtener usuario actual de sesión
    $usuario_id = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;
    $usuario_nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Sistema';
    $usuario_rol = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;
    
    // Obtener IP
    $ip = obtenerIPAudit();
    
    $ahora = date('Y-m-d H:i:s');

    // Usar query directa con prepared statement correcta
    $valores_ant = !empty($valores_anteriores) ? json_encode($valores_anteriores, JSON_UNESCAPED_UNICODE) : null;
    $valores_new = !empty($valores_nuevos) ? json_encode($valores_nuevos, JSON_UNESCAPED_UNICODE) : null;
    $navegador = obtenerNavegadorAudit();
    
    $stmt = $conexion->prepare(
        "INSERT INTO audit_logs 
        (usuario_id, usuario_nombre, usuario_rol, accion, tabla, registro_id, 
         valores_anteriores, valores_nuevos, notas, ip, navegador, tiempo) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    if (!$stmt) {
        error_log("Audit Logging - Error prepare: " . $conexion->error);
        return false;
    }

    $stmt->bind_param(
        "isisisssssss",
        $usuario_id,
        $usuario_nombre,
        $usuario_rol,
        $accion,
        $tabla,
        $registro_id,
        $valores_ant,
        $valores_new,
        $notas,
        $ip,
        $navegador,
        $ahora
    );
    
    $resultado = $stmt->execute();
    
    if (!$resultado) {
        error_log("Audit Logging - Error execute: " . $stmt->error);
    }
    
    $stmt->close();
    return $resultado;
}

/**
 * Registra acceso denegado
 * 
 * @param string $tabla Tabla a la que intentó acceder
 * @param string $motivo Motivo del rechazo
 * @return bool
 */
function registrarAccesoDenegado($tabla, $motivo = 'permiso_insuficiente') {
    return registrarAudit(
        'ACCESO_DENEGADO',
        $tabla,
        0,
        [],
        [],
        "Motivo: $motivo"
    );
}

/**
 * Registra cambio de contraseña
 * 
 * @param int $usuario_id ID del usuario que cambió contraseña
 * @param int $usuario_id_afectado ID del usuario cuya contraseña fue cambiada
 * @param string $notas Notas adicionales
 * @return bool
 */
function registrarCambioContrasena($usuario_id_afectado, $notas = '') {
    return registrarAudit(
        'CAMBIO_CONTRASEÑA',
        'usuarios',
        $usuario_id_afectado,
        ['contraseña' => '***HASH***'],
        ['contraseña' => '***HASH_NUEVO***'],
        $notas
    );
}

/**
 * Registra cambio de rol/permiso
 * 
 * @param int $usuario_id_afectado ID del usuario
 * @param int $rol_anterior Rol anterior
 * @param int $rol_nuevo Nuevo rol
 * @return bool
 */
function registrarCambioRol($usuario_id_afectado, $rol_anterior, $rol_nuevo) {
    $roles = ['Admin' => 1, 'Vendedor' => 2, 'Cliente' => 3];
    $rol_ant_nombre = array_search($rol_anterior, $roles) ?: 'Desconocido';
    $rol_nuevo_nombre = array_search($rol_nuevo, $roles) ?: 'Desconocido';
    
    return registrarAudit(
        'CAMBIO_ROL',
        'usuarios',
        $usuario_id_afectado,
        ['rol' => $rol_anterior, 'rol_nombre' => $rol_ant_nombre],
        ['rol' => $rol_nuevo, 'rol_nombre' => $rol_nuevo_nombre],
        "Cambio de rol: $rol_ant_nombre → $rol_nuevo_nombre"
    );
}

/**
 * Obtiene estadísticas de auditoría
 * 
 * @param string $filtro 'usuario', 'tabla', 'accion', 'fecha' o 'todos'
 * @param string $valor Valor a filtrar
 * @param int $limit Cantidad máxima de registros
 * @return array Registros de audit
 */
function obtenerAuditLog($filtro = 'todos', $valor = null, $limit = 100) {
    global $conexion;
    
    $hace_30_dias = date('Y-m-d H:i:s', strtotime('-30 days'));
    
    $sql = "SELECT * FROM audit_logs WHERE tiempo > ? ";
    $params = [$hace_30_dias];
    $tipos = "s";
    
    if ($filtro === 'usuario' && $valor) {
        $sql .= "AND usuario_nombre LIKE ? ";
        $params[] = "%$valor%";
        $tipos .= "s";
    } elseif ($filtro === 'tabla' && $valor) {
        $sql .= "AND tabla = ? ";
        $params[] = $valor;
        $tipos .= "s";
    } elseif ($filtro === 'accion' && $valor) {
        $sql .= "AND accion = ? ";
        $params[] = $valor;
        $tipos .= "s";
    } elseif ($filtro === 'fecha' && $valor) {
        $sql .= "AND DATE(tiempo) = ? ";
        $params[] = $valor;
        $tipos .= "s";
    }
    
    $sql .= "ORDER BY tiempo DESC LIMIT ?";
    $params[] = $limit;
    $tipos .= "i";
    
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        error_log("Audit - Error obtener: " . $conexion->error);
        return [];
    }
    
    $stmt->bind_param($tipos, ...$params);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $logs = [];
    while ($fila = $resultado->fetch_assoc()) {
        $logs[] = $fila;
    }
    
    $stmt->close();
    return $logs;
}

/**
 * Obtiene actividad por usuario
 * 
 * @param int $usuario_id ID del usuario
 * @param int $limit Registros máximos
 * @return array
 */
function obtenerActividadUsuario($usuario_id, $limit = 50) {
    global $conexion;
    
    $hace_30_dias = date('Y-m-d', strtotime('-30 days'));
    
    $stmt = $conexion->prepare(
        "SELECT * FROM audit_logs 
         WHERE usuario_id = ? 
         AND DATE(tiempo) >= ? 
         ORDER BY tiempo DESC 
         LIMIT ?"
    );
    
    $stmt->bind_param("isi", $usuario_id, $hace_30_dias, $limit);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $logs = [];
    while ($fila = $resultado->fetch_assoc()) {
        $logs[] = $fila;
    }
    
    $stmt->close();
    return $logs;
}

/**
 * Obtiene actividad sospechosa (múltiples accesos denegados, etc)
 * 
 * @return array
 */
function obtenerActividadSospechosa() {
    global $conexion;
    
    $hace_24_horas = date('Y-m-d H:i:s', strtotime('-24 hours'));
    
    $stmt = $conexion->prepare(
        "SELECT usuario_nombre, COUNT(*) as cantidad, accion, MAX(tiempo) as ultimo_intento
         FROM audit_logs
         WHERE accion IN ('ACCESO_DENEGADO', 'DELETE')
         AND tiempo > ?
         GROUP BY usuario_nombre, accion
         HAVING cantidad >= 3
         ORDER BY cantidad DESC"
    );
    
    $stmt->bind_param("s", $hace_24_horas);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $logs = [];
    while ($fila = $resultado->fetch_assoc()) {
        $logs[] = $fila;
    }
    
    $stmt->close();
    return $logs;
}

/**
 * Obtiene la IP del cliente
 * 
 * @return string
 */
function obtenerIPAudit() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    }
    return 'UNKNOWN';
}

/**
 * Obtiene información del navegador/user agent
 * 
 * @return string
 */
function obtenerNavegadorAudit() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Extraer navegador e OS
    if (strpos($user_agent, 'Chrome') !== false) {
        $navegador = 'Chrome';
    } elseif (strpos($user_agent, 'Firefox') !== false) {
        $navegador = 'Firefox';
    } elseif (strpos($user_agent, 'Safari') !== false) {
        $navegador = 'Safari';
    } elseif (strpos($user_agent, 'Edge') !== false) {
        $navegador = 'Edge';
    } else {
        $navegador = 'Otro';
    }
    
    // Limitar a 255 caracteres
    return substr("$navegador / $user_agent", 0, 255);
}

/**
 * Limpia logs antiguos (>30 días)
 * Recomendado ejecutar diariamente vía cron
 * 
 * @return bool
 */
function limpiarLogsAntiguos() {
    global $conexion;
    
    $hace_30_dias = date('Y-m-d', strtotime('-30 days'));
    
    $stmt = $conexion->prepare(
        "DELETE FROM audit_logs WHERE DATE(tiempo) < ?"
    );
    
    $stmt->bind_param("s", $hace_30_dias);
    $resultado = $stmt->execute();
    $stmt->close();
    
    return $resultado;
}

/**
 * Exporta logs a CSV
 * 
 * @param string $nombre_archivo Nombre del archivo (sin extensión)
 * @param string $filtro Filtro a aplicar
 * @param string $valor Valor del filtro
 */
function exportarAuditLogCSV($nombre_archivo = 'audit_log', $filtro = 'todos', $valor = null) {
    $logs = obtenerAuditLog($filtro, $valor, 1000);
    
    if (empty($logs)) {
        return false;
    }
    
    // Header para descargar
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $nombre_archivo . '_' . date('Y-m-d') . '.csv');
    
    // BOM para Excel
    echo "\xEF\xBB\xBF";
    
    // Encabezados
    $encabezados = array_keys($logs[0]);
    fputcsv(STDOUT, $encabezados, ';');
    
    // Datos
    foreach ($logs as $log) {
        fputcsv(STDOUT, $log, ';');
    }
    
    exit();
}

?>
