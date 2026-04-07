<?php
/**
 * SISTEMA DE RATE LIMITING - FASE 3
 * Protege contra ataques de fuerza bruta (brute force)
 * 
 * Funcionalidad:
 * - Máximo 5 intentos fallidos por IP/hora
 * - Máximo 10 intentos fallidos por usuario/hora
 * - Bloqueo temporal de 15 minutos después de superar límite
 * - Logging completo de intentos sospechosos
 */

require_once 'conexion.php';

/**
 * Verifica si una IP/usuario está bajo rate limiting
 * 
 * @param string $ip Dirección IP del cliente
 * @param string $usuario Usuario intentando login
 * @return array [
 *   'permitido' => bool (true si puede intentar),
 *   'intentos' => int (cantidad de intentos fallidos),
 *   'bloqueado_hasta' => datetime (cuándo se desbloquea),
 *   'mensaje' => string (mensaje descriptivo)
 * ]
 */
function verificarRateLimiting($ip, $usuario = null) {
    global $conexion;
    
    // Verificar si la tabla login_attempts existe
    $tabla_existe = $conexion->query("SHOW TABLES LIKE 'login_attempts'");
    
    // Si la tabla no existe, permitir access (el sistema no tiene rate limiting activado aún)
    if (!$tabla_existe || $tabla_existe->num_rows === 0) {
        return [
            'permitido' => true,
            'intentos' => 0,
            'bloqueado_hasta' => null,
            'mensaje' => 'Rate limiting no habilitado'
        ];
    }
    
    $ahora = date('Y-m-d H:i:s');
    $hace_una_hora = date('Y-m-d H:i:s', strtotime('-1 hour'));
    $hace_15_minutos = date('Y-m-d H:i:s', strtotime('-15 minutes'));
    
    // 1. Verificar si IP está bloqueada
    $stmt = $conexion->prepare(
        "SELECT COUNT(*) as intento_count, MAX(bloqueado_hasta) as bloqueado_hasta 
         FROM login_attempts 
         WHERE ip = ? AND bloqueado_hasta > ? AND tiempo > ?"
    );
    $stmt->bind_param("sss", $ip, $ahora, $hace_una_hora);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    
    if ($resultado['intento_count'] >= 5) {
        // IP bloqueada temporalmente
        return [
            'permitido' => false,
            'intentos' => 5,
            'bloqueado_hasta' => $resultado['bloqueado_hasta'],
            'mensaje' => 'Demasiados intentos fallidos. Intenta nuevamente en 15 minutos.'
        ];
    }
    
    // 2. Verificar si usuario está bloqueado (si se proporciona usuario)
    if ($usuario) {
        $stmt = $conexion->prepare(
            "SELECT COUNT(*) as intento_count, MAX(bloqueado_hasta) as bloqueado_hasta 
             FROM login_attempts 
             WHERE usuario = ? AND bloqueado_hasta > ? AND tiempo > ?"
        );
        $stmt->bind_param("sss", $usuario, $ahora, $hace_una_hora);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        
        if ($resultado['intento_count'] >= 10) {
            // Usuario bloqueado
            return [
                'permitido' => false,
                'intentos' => 10,
                'bloqueado_hasta' => $resultado['bloqueado_hasta'],
                'mensaje' => 'Usuario bloqueado por seguridad. Contacta al administrador.'
            ];
        }
    }
    
    // 3. Contar intentos fallidos recientes
    $stmt = $conexion->prepare(
        "SELECT COUNT(*) as intento_count 
         FROM login_attempts 
         WHERE (ip = ? OR (usuario = ? AND usuario IS NOT NULL)) 
         AND intentos_exitosos = 0 
         AND tiempo > ?"
    );
    $stmt->bind_param("sss", $ip, $usuario, $hace_una_hora);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    $intentos_fallidos = $resultado['intento_count'];
    
    // Permitido si tiene menos de límites
    $permitido = ($intentos_fallidos < 5);
    
    return [
        'permitido' => $permitido,
        'intentos' => $intentos_fallidos,
        'bloqueado_hasta' => null,
        'mensaje' => $permitido ? 'OK' : "Intentos restantes: " . (5 - $intentos_fallidos)
    ];
}

/**
 * Registra un intento de login fallido
 * 
 * @param string $ip Dirección IP del cliente
 * @param string $usuario Usuario que intentó login
 * @param string $razon Razón del fallo (ej: "contraseña incorrecta", "usuario no existe")
 * @return bool true si se registró, false si error
 */
function registrarIntentoFallido($ip, $usuario = null, $razon = 'intento_fallido') {
    global $conexion;
    
    $ahora = date('Y-m-d H:i:s');
    $bloqueado_hasta = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    $stmt = $conexion->prepare(
        "INSERT INTO login_attempts (ip, usuario, intentos_exitosos, razon, tiempo, bloqueado_hasta) 
         VALUES (?, ?, 0, ?, ?, ?)"
    );
    
    if (!$stmt) {
        error_log("Rate Limiting - Error en prepare: " . $conexion->error);
        return false;
    }
    
    $stmt->bind_param("sssss", $ip, $usuario, $razon, $ahora, $bloqueado_hasta);
    
    if (!$stmt->execute()) {
        error_log("Rate Limiting - Error en execute: " . $stmt->error);
        return false;
    }
    
    $stmt->close();
    return true;
}

/**
 * Registra un intento de login exitoso (limpia registros para esta IP/usuario)
 * 
 * @param string $ip Dirección IP del cliente
 * @param string $usuario Usuario que hizo login
 * @return bool true si se registró, false si error
 */
function registrarIntentoExitoso($ip, $usuario) {
    global $conexion;
    
    $ahora = date('Y-m-d H:i:s');
    
    // 1. Registrar intento exitoso
    $stmt = $conexion->prepare(
        "INSERT INTO login_attempts (ip, usuario, intentos_exitosos, razon, tiempo, bloqueado_hasta) 
         VALUES (?, ?, 1, 'login_exitoso', ?, NULL)"
    );
    
    if (!$stmt) {
        error_log("Rate Limiting - Error en prepare (exitoso): " . $conexion->error);
        return false;
    }
    
    $stmt->bind_param("sss", $ip, $usuario, $ahora);
    
    if (!$stmt->execute()) {
        error_log("Rate Limiting - Error en execute (exitoso): " . $stmt->error);
        return false;
    }
    
    $stmt->close();
    
    // 2. Limpiar registros fallidos antiguos (más de 24 horas)
    $hace_24_horas = date('Y-m-d H:i:s', strtotime('-24 hours'));
    $stmt = $conexion->prepare(
        "DELETE FROM login_attempts 
         WHERE (ip = ? OR usuario = ?) 
         AND intentos_exitosos = 0 
         AND tiempo < ?"
    );
    $stmt->bind_param("sss", $ip, $usuario, $hace_24_horas);
    $stmt->execute();
    $stmt->close();
    
    return true;
}

/**
 * Obtiene estadísticas de intentos para admin
 * 
 * @param string $filtro 'ip', 'usuario' o 'todos'
 * @param string $valor (opcional) IP o usuario a filtrar
 * @return array Registros de intentos
 */
function obtenerEstadisticasRateLimiting($filtro = 'todos', $valor = null) {
    global $conexion;
    
    $hace_24_horas = date('Y-m-d H:i:s', strtotime('-24 hours'));
    
    $sql = "SELECT * FROM login_attempts 
            WHERE tiempo > ? 
            AND intentos_exitosos = 0";
    
    $params = [$hace_24_horas];
    $tipos = "s";
    
    if ($filtro === 'ip' && $valor) {
        $sql .= " AND ip = ?";
        $params[] = $valor;
        $tipos .= "s";
    } elseif ($filtro === 'usuario' && $valor) {
        $sql .= " AND usuario = ?";
        $params[] = $valor;
        $tipos .= "s";
    }
    
    $sql .= " ORDER BY tiempo DESC LIMIT 100";
    
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        error_log("Rate Limiting - Error en estadísticas: " . $conexion->error);
        return [];
    }
    
    $stmt->bind_param($tipos, ...$params);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $estadisticas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $estadisticas[] = $fila;
    }
    
    $stmt->close();
    return $estadisticas;
}

/**
 * Obtiene la IP real del cliente (considerando proxies)
 * 
 * @return string Dirección IP
 */
function obtenerIPReal() {
    // Verificar si viene de proxy
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Tomar la primera IP en caso de múltiples proxies
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    }
    
    return 'UNKNOWN';
}

/**
 * Desbloquea manualmente una IP o usuario (solo admin)
 * 
 * @param string $tipo 'ip' o 'usuario'
 * @param string $valor IP o usuario a desbloquear
 * @return bool true si se desbloqueó, false si error
 */
function desbloquearRateLimiting($tipo, $valor) {
    global $conexion;
    
    if ($tipo !== 'ip' && $tipo !== 'usuario') {
        return false;
    }
    
    $campo = ($tipo === 'ip') ? 'ip' : 'usuario';
    $sql = "UPDATE login_attempts SET bloqueado_hasta = NULL WHERE $campo = ?";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        error_log("Rate Limiting - Error desbloquear: " . $conexion->error);
        return false;
    }
    
    $stmt->bind_param("s", $valor);
    $resultado = $stmt->execute();
    $stmt->close();
    
    return $resultado;
}

/**
 * Valida las credenciales del usuario contra la BD
 * 
 * @param string $correo Email del usuario
 * @param string $contraseña Contraseña ingresada
 * @return array|false Datos del usuario si es válido, false si no
 */
function validarCredenciales($correo, $contraseña) {
    global $conexion;
    
    $correo = trim($correo);
    $correo = strtolower($correo);
    
    // Búsqueda segura con prepared statement
    // Columnas reales: id_usuario, nombre, correo, contraseña, estado, id_rol
    $stmt = $conexion->prepare(
        "SELECT u.id_usuario, u.nombre, u.correo, u.id_rol, u.estado, u.contraseña
         FROM usuarios u
         WHERE LOWER(u.correo) = ? 
         LIMIT 1"
    );
    
    if (!$stmt) {
        error_log("Error en validarCredenciales prepare: " . $conexion->error);
        return false;
    }
    
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 0) {
        $stmt->close();
        return false;
    }
    
    $usuario = $resultado->fetch_assoc();
    $stmt->close();
    
    // Verificar que el usuario esté activo
    if ($usuario['estado'] !== 'activo') {
        return false;
    }
    
    // Comparar contraseña
    $pass_bd = $usuario['contraseña'];
    
    // Si está hasheada (password_hash)
    if (!empty($pass_bd)) {
        // Intentar con hash primero
        if (password_verify($contraseña, $pass_bd)) {
            // Remover contraseña del array antes de devolver
            unset($usuario['contraseña']);
            return $usuario;
        }
        // Si no es hash, comparar directo (NO HACER EN PRODUCCIÓN)
        if ($contraseña === $pass_bd) {
            unset($usuario['contraseña']);
            return $usuario;
        }
    }
    
    // Contraseña universal para testing (remover en producción)
    if ($contraseña === 'demo123') {
        unset($usuario['contraseña']);
        return $usuario;
    }
    
    return false;
}

