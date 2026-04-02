-- FASE 3-3: MONITORING ALERTS - CREAR TABLA
-- Ejecutar esta query en phpMyAdmin para crear la tabla de alertas de monitoreo

-- Tabla para registrar alertas del sistema de monitoreo
CREATE TABLE IF NOT EXISTS `monitoring_alerts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tipo` VARCHAR(50) NOT NULL COMMENT 'Tipo de alerta (ACCESO_DENEGADO, DELETE_MASA, CAMBIO_ADMIN, etc)',
  `severidad` VARCHAR(20) NOT NULL COMMENT 'Nivel de severidad (CRÍTICA, ALTA, MEDIA, BAJA)',
  `titulo` VARCHAR(255) NOT NULL COMMENT 'Título descriptivo de la alerta',
  `mensaje` TEXT NOT NULL COMMENT 'Mensaje detallado de la alerta',
  `detalles` LONGTEXT NULL COMMENT 'Detalles técnicos en JSON',
  `tiempo` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Cuándo se generó la alerta',
  `leida` TINYINT(1) DEFAULT 0 COMMENT '0=no leída, 1=leída',
  
  -- ÍNDICES para búsquedas rápidas
  INDEX `idx_tipo` (`tipo`),
  INDEX `idx_severidad` (`severidad`),
  INDEX `idx_tiempo` (`tiempo`),
  INDEX `idx_leida` (`leida`),
  INDEX `idx_severidad_tiempo` (`severidad`, `tiempo`),
  INDEX `idx_leida_tiempo` (`leida`, `tiempo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Registro de alertas generadas por el sistema de monitoreo automático';

-- Índice para estadísticas
CREATE INDEX `idx_tipo_severidad` ON monitoring_alerts(tipo, severidad);

-- Política de limpieza (opcional, en cron diario):
-- DELETE FROM monitoring_alerts WHERE tiempo < DATE_SUB(NOW(), INTERVAL 30 DAY);
