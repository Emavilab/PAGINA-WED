<?php
/**
 * SISTEMA DE MONITOREO Y ALERTAS - FASE 3-3
 * Detecta actividades sospechosas y genera alertas automáticas
 * 
 * Funcionalidad:
 * - Detecta múltiples intentos de acceso denegado
 * - Detecta múltiples DELETEs en poco tiempo
 * - Detecta cambios en usuarios admin
 * - Detecta patrones de ataque
 * - Envía alertas por email
 * - Registra alertas en BD
 * - Dashboard de monitoreo
 */

require_once 'conexion.php';
require_once 'audit_logging.php';
require_once 'smtp_config.php';

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Ejecuta todas las reglas de monitoreo
 * Debe llamarse cada hora vía cron
 * 
 * @return array Resumen de alertas generadas
 */
function ejecutarMonitoreo() {
    global $conexion;
    
    $alertas_generadas = [];
    
    // 1. Detectar accesos denegados sospechosos
    $accesos_denegados = detectarAccesosDenegados();
    if (!empty($accesos_denegados)) {
        $alertas_generadas[] = $accesos_denegados;
    }
    
    // 2. Detectar DELETEs en masa
    $deletes_masa = detectarDeletesMasa();
    if (!empty($deletes_masa)) {
        $alertas_generadas[] = $deletes_masa;
    }
    
    // 3. Detectar cambios en usuarios admin
    $cambios_admin = detectarCambiosAdmin();
    if (!empty($cambios_admin)) {
        $alertas_generadas[] = $cambios_admin;
    }
    
    // 4. Detectar cambios en configuración crítica
    $cambios_criticos = detectarCambiosCriticos();
    if (!empty($cambios_criticos)) {
        $alertas_generadas[] = $cambios_criticos;
    }
    
    // 5. Detectar patrones de ataque (múltiples IPs fallidas)
    $patrones_ataque = detectarPatronesAtaque();
    if (!empty($patrones_ataque)) {
        $alertas_generadas[] = $patrones_ataque;
    }
    
    return $alertas_generadas;
}

/**
 * Detecta múltiples accesos denegados desde una IP/usuarioo
 * 
 * @return array|null Alerta si se detecta, null si no
 */
function detectarAccesosDenegados() {
    global $conexion;
    
    $hace_1hora = date('Y-m-d H:i:s', strtotime('-1 hour'));
    
    $stmt = $conexion->prepare(
        "SELECT usuario_nombre, ip, COUNT(*) as cantidad, MAX(tiempo) as ultimo
         FROM audit_logs
         WHERE accion = 'ACCESO_DENEGADO'
         AND tiempo > ?
         GROUP BY usuario_nombre, ip
         HAVING cantidad >= 5"
    );
    
    $stmt->bind_param("s", $hace_1hora);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows == 0) {
        $stmt->close();
        return null;
    }
    
    $alertas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $alertas[] = $fila;
    }
    $stmt->close();
    
    if (!empty($alertas)) {
        $alert = [
            'tipo' => 'ACCESO_DENEGADO',
            'severidad' => 'ALTA',
            'titulo' => 'Múltiples accesos denegados detectados',
            'detalles' => $alertas,
            'cantidad' => count($alertas),
            'mensaje' => 'Se detectaron ' . count($alertas) . ' intento(s) de acceso no autorizado',
            'recomendacion' => 'Revisar intentos de acceso no autorizados. Verificar IPs sospechosas.'
        ];
        
        registrarAlerta($alert);
        enviarAlertaEmail($alert);
        
        return $alert;
    }
    
    return null;
}

/**
 * Detecta múltiples eliminaciones (DELETEs) en poco tiempo
 * 
 * @return array|null Alerta si se detecta
 */
function detectarDeletesMasa() {
    global $conexion;
    
    $hace_1hora = date('Y-m-d H:i:s', strtotime('-1 hour'));
    
    $stmt = $conexion->prepare(
        "SELECT usuario_nombre, tabla, COUNT(*) as cantidad, MAX(tiempo) as ultimo
         FROM audit_logs
         WHERE accion = 'DELETE'
         AND tiempo > ?
         GROUP BY usuario_nombre, tabla
         HAVING cantidad >= 10"
    );
    
    $stmt->bind_param("s", $hace_1hora);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows == 0) {
        $stmt->close();
        return null;
    }
    
    $alertas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $alertas[] = $fila;
    }
    $stmt->close();
    
    if (!empty($alertas)) {
        $alert = [
            'tipo' => 'DELETE_MASA',
            'severidad' => 'CRÍTICA',
            'titulo' => '⚠️ Múltiples eliminaciones detectadas',
            'detalles' => $alertas,
            'cantidad' => array_sum(array_column($alertas, 'cantidad')),
            'mensaje' => 'Se detectaron ' . array_sum(array_column($alertas, 'cantidad')) . ' eliminaciones en la última hora',
            'recomendacion' => 'Verificar inmediatamente. Posible eliminación de datos en masa. Considerar restaurar desde backup.'
        ];
        
        registrarAlerta($alert);
        enviarAlertaEmail($alert, true); // Alerta crítica
        
        return $alert;
    }
    
    return null;
}

/**
 * Detecta cambios en usuarios con rol ADMIN
 * 
 * @return array|null Alerta si se detecta
 */
function detectarCambiosAdmin() {
    global $conexion;
    
    $hace_1hora = date('Y-m-d H:i:s', strtotime('-1 hour'));
    
    $stmt = $conexion->prepare(
        "SELECT usuario_nombre, accion, COUNT(*) as cantidad, MAX(tiempo) as ultimo
         FROM audit_logs
         WHERE tabla = 'usuarios'
         AND usuario_rol = 1
         AND tiempo > ?
         GROUP BY usuario_nombre, accion"
    );
    
    $stmt->bind_param("s", $hace_1hora);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $cambios = [];
    while ($fila = $resultado->fetch_assoc()) {
        $cambios[] = $fila;
    }
    $stmt->close();
    
    if (!empty($cambios)) {
        $alert = [
            'tipo' => 'CAMBIO_ADMIN',
            'severidad' => 'ALTA',
            'titulo' => 'Cambios en usuarios administrativos detectados',
            'detalles' => $cambios,
            'cantidad' => count($cambios),
            'mensaje' => 'Se detectaron ' . count($cambios) . ' cambio(s) en cuenta(s) de administrador',
            'recomendacion' => 'Verificar que los cambios fueron autorizados. Revisar identidad del ejecutor.'
        ];
        
        registrarAlerta($alert);
        enviarAlertaEmail($alert);
        
        return $alert;
    }
    
    return null;
}

/**
 * Detecta cambios en configuración crítica
 * 
 * @return array|null Alerta si se detecta
 */
function detectarCambiosCriticos() {
    global $conexion;
    
    $hace_24horas = date('Y-m-d H:i:s', strtotime('-24 hours'));
    
    $stmt = $conexion->prepare(
        "SELECT usuario_nombre, COUNT(*) as cantidad, MAX(tiempo) as ultimo
         FROM audit_logs
            WHERE tabla IN ('configuracion', 'metodos_pago', 'metodos_envio')
         AND accion IN ('UPDATE', 'DELETE')
         AND tiempo > ?
         GROUP BY usuario_nombre"
    );
    
    $stmt->bind_param("s", $hace_24horas);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $cambios = [];
    while ($fila = $resultado->fetch_assoc()) {
        $cambios[] = $fila;
    }
    $stmt->close();
    
    if (!empty($cambios)) {
        $alert = [
            'tipo' => 'CAMBIO_CRITICO',
            'severidad' => 'MEDIA',
            'titulo' => 'Cambios en configuración crítica',
            'detalles' => $cambios,
            'cantidad' => count($cambios),
            'mensaje' => 'Se ha modificado configuración del sistema',
            'recomendacion' => 'Revisar cambios realizados. Verificar que sean intencionales.'
        ];
        
        registrarAlerta($alert);
        enviarAlertaEmail($alert);
        
        return $alert;
    }
    
    return null;
}

/**
 * Detecta patrones de ataque (múltiples IPs intentando múltiples cosas)
 * 
 * @return array|null Alerta si se detecta
 */
function detectarPatronesAtaque() {
    global $conexion;
    
    $hace_4horas = date('Y-m-d H:i:s', strtotime('-4 hours'));
    
    $stmt = $conexion->prepare(
        "SELECT ip, COUNT(*) as total_eventos, 
                COUNT(DISTINCT accion) as tipos_acciones,
                GROUP_CONCAT(DISTINCT accion) as acciones
         FROM audit_logs
         WHERE tiempo > ?
         AND accion IN ('ACCESO_DENEGADO', 'DELETE', 'CAMBIO_CONTRASEÑA')
         GROUP BY ip
         HAVING total_eventos >= 15 AND tipos_acciones >= 2"
    );
    
    $stmt->bind_param("s", $hace_4horas);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $patrones = [];
    while ($fila = $resultado->fetch_assoc()) {
        $patrones[] = $fila;
    }
    $stmt->close();
    
    if (!empty($patrones)) {
        $alert = [
            'tipo' => 'PATRÓN_ATAQUE',
            'severidad' => 'CRÍTICA',
            'titulo' => '🚨 Posible ataque detectado',
            'detalles' => $patrones,
            'cantidad' => count($patrones),
            'mensaje' => 'Se detectó actividad sospechosa desde ' . count($patrones) . ' IP(s)',
            'recomendacion' => 'ACCIÓN INMEDIATA: Verificar actividad sospechosa. Considerar bloquear IP(s). Revisar logs completos.'
        ];
        
        registrarAlerta($alert);
        enviarAlertaEmail($alert, true); // Alerta crítica
        
        return $alert;
    }
    
    return null;
}

/**
 * Registra una alerta en la base de datos
 * 
 * @param array $alerta Datos de la alerta
 * @return bool
 */
function registrarAlerta($alerta) {
    global $conexion;
    
    $tipo = $alerta['tipo'];
    $severidad = $alerta['severidad'];
    $titulo = $alerta['titulo'];
    $mensaje = $alerta['mensaje'];
    $detalles = json_encode($alerta['detalles'], JSON_UNESCAPED_UNICODE);
    $ahora = date('Y-m-d H:i:s');
    
    $stmt = $conexion->prepare(
        "INSERT INTO monitoring_alerts (tipo, severidad, titulo, mensaje, detalles, tiempo, leida)
         VALUES (?, ?, ?, ?, ?, ?, 0)"
    );
    
    if (!$stmt) {
        error_log("Monitoring - Error al registrar alerta: " . $conexion->error);
        return false;
    }
    
    $stmt->bind_param("ssssss", $tipo, $severidad, $titulo, $mensaje, $detalles, $ahora);
    $resultado = $stmt->execute();
    $stmt->close();
    
    return $resultado;
}

/**
 * Envía alerta por email
 * 
 * @param array $alerta Datos de la alerta
 * @param bool $critica Si es crítica, envía a múltiples destinatarios
 * @return bool
 */
function enviarAlertaEmail($alerta, $critica = false) {
    try {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = getEnv('SMTP_HOST', 'smtp.gmail.com');
        $mail->SMTPAuth = true;
        $mail->Username = getEnv('SMTP_USER', '');
        $mail->Password = getEnv('SMTP_PASSWORD', '');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = getEnv('SMTP_PORT', 587);
        
        $mail->setFrom(getEnv('SMTP_USER', ''), 'Sistema de Seguridad');
        
        // Destinatarios
        $mail->addAddress(getEnv('ADMIN_EMAIL', 'admin@example.com'));
        if ($critica) {
            // Agregar otros admins si es crítica
            $admin_emails = explode(',', getEnv('CRITICAL_ALERT_EMAILS', ''));
            foreach ($admin_emails as $email) {
                if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress(trim($email));
                }
            }
        }
        
        $mail->isHTML(true);
        $mail->Subject = ($critica ? '🚨 CRÍTICA: ' : '⚠️ ') . $alerta['titulo'];
        
        // HTML del email
        $html = "
        <h2>{$alerta['titulo']}</h2>
        <p><strong>Severidad:</strong> <span style='color: " . obtenerColorSeveridad($alerta['severidad']) . ";'>{$alerta['severidad']}</span></p>
        <p><strong>Tipo:</strong> {$alerta['tipo']}</p>
        <p><strong>Mensaje:</strong> {$alerta['mensaje']}</p>
        <p><strong>Recomendación:</strong> <em>{$alerta['recomendacion']}</em></p>
        <p><strong>Detectado:</strong> " . date('d/m/Y H:i:s') . "</p>
        <hr>
        <h3>Detalles Técnicos:</h3>
        <pre>" . json_encode($alerta['detalles'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>
        <hr>
        <p><a href='http://" . $_SERVER['HTTP_HOST'] . "/admin/monitoring_dashboard.php'>Ver Dashboard de Monitoreo</a></p>
        ";
        
        $mail->Body = $html;
        $mail->AltBody = $alerta['mensaje'];
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("Monitoring - Error al enviar email: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene color según severidad (para email HTML)
 * 
 * @param string $severidad
 * @return string Color hex
 */
function obtenerColorSeveridad($severidad) {
    $colores = [
        'CRÍTICA' => '#d32f2f',
        'ALTA' => '#f57c00',
        'MEDIA' => '#fbc02d',
        'BAJA' => '#388e3c'
    ];
    return $colores[$severidad] ?? '#666666';
}

/**
 * Obtiene todas las alertas sin leer
 * 
 * @return array
 */
function obtenerAlertasSinLeer() {
    global $conexion;
    
    $stmt = $conexion->prepare(
        "SELECT * FROM monitoring_alerts
         WHERE leida = 0
         ORDER BY tiempo DESC
         LIMIT 50"
    );
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $alertas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $alertas[] = $fila;
    }
    
    $stmt->close();
    return $alertas;
}

/**
 * Obtiene todas las alertas (últimas 100)
 * 
 * @return array
 */
function obtenerTodasLasAlertas() {
    global $conexion;
    
    $stmt = $conexion->prepare(
        "SELECT * FROM monitoring_alerts
         ORDER BY tiempo DESC
         LIMIT 100"
    );
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $alertas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $alertas[] = $fila;
    }
    
    $stmt->close();
    return $alertas;
}

/**
 * Marca alerta como leída
 * 
 * @param int $alerta_id
 * @return bool
 */
function marcarAlertaLeida($alerta_id) {
    global $conexion;
    
    $stmt = $conexion->prepare(
        "UPDATE monitoring_alerts SET leida = 1 WHERE id = ?"
    );
    
    $stmt->bind_param("i", $alerta_id);
    $resultado = $stmt->execute();
    $stmt->close();
    
    return $resultado;
}

/**
 * Obtiene estadísticas de alertas
 * 
 * @return array
 */
function obtenerEstadisticasAlertas() {
    global $conexion;
    
    $hace_24horas = date('Y-m-d H:i:s', strtotime('-24 hours'));
    
    $stmt = $conexion->prepare(
        "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN leida = 0 THEN 1 ELSE 0 END) as sin_leer,
            SUM(CASE WHEN severidad = 'CRÍTICA' THEN 1 ELSE 0 END) as criticas,
            SUM(CASE WHEN severidad = 'ALTA' THEN 1 ELSE 0 END) as altas,
            SUM(CASE WHEN tiempo > ? THEN 1 ELSE 0 END) as ultimas_24h
         FROM monitoring_alerts"
    );
    
    $stmt->bind_param("s", $hace_24horas);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $stats = $resultado->fetch_assoc();
    $stmt->close();
    
    return $stats;
}

/**
 * Limpia alertas antiguas (>30 días)
 * 
 * @return bool
 */
function limpiarAlertasAntiguas() {
    global $conexion;
    
    $hace_30_dias = date('Y-m-d', strtotime('-30 days'));
    
    $stmt = $conexion->prepare(
        "DELETE FROM monitoring_alerts WHERE DATE(tiempo) < ?"
    );
    
    $stmt->bind_param("s", $hace_30_dias);
    $resultado = $stmt->execute();
    $stmt->close();
    
    return $resultado;
}

?>
