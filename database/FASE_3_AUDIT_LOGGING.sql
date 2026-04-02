-- FASE 3-2: AUDIT LOGGING - CREAR TABLA
-- Ejecutar esta query en phpMyAdmin para crear la tabla de auditoría

-- Tabla para registrar todas las acciones sensitivas
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NULL COMMENT 'ID del usuario que realizó la acción',
  `usuario_nombre` VARCHAR(255) NOT NULL COMMENT 'Nombre del usuario que actuó',
  `usuario_rol` INT NULL COMMENT 'Rol del usuario (1=Admin, 2=Vendedor, 3=Cliente)',
  `accion` VARCHAR(50) NOT NULL COMMENT 'Tipo de acción (CREATE, UPDATE, DELETE, LOGIN, etc)',
  `tabla` VARCHAR(100) NOT NULL COMMENT 'Tabla afectada',
  `registro_id` INT NOT NULL COMMENT 'ID del registro modificado',
  `valores_anteriores` LONGTEXT NULL COMMENT 'JSON con valores antes del cambio',
  `valores_nuevos` LONGTEXT NULL COMMENT 'JSON con valores después del cambio',
  `notas` VARCHAR(500) NULL COMMENT 'Notas adicionales',
  `ip` VARCHAR(45) NOT NULL COMMENT 'IP del cliente',
  `navegador` VARCHAR(255) NULL COMMENT 'Información del navegador/user agent',
  `tiempo` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Cuándo ocurrió',
  
  -- ÍNDICES para búsquedas rápidas
  INDEX `idx_usuario_id` (`usuario_id`),
  INDEX `idx_usuario_nombre` (`usuario_nombre`),
  INDEX `idx_accion` (`accion`),
  INDEX `idx_tabla` (`tabla`),
  INDEX `idx_registro_id` (`registro_id`),
  INDEX `idx_tiempo` (`tiempo`),
  INDEX `idx_accion_tabla` (`accion`, `tabla`),
  INDEX `idx_usuario_tiempo` (`usuario_id`, `tiempo`),
  INDEX `idx_tabla_registro` (`tabla`, `registro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Registro de auditoría de todas las acciones sensitivas del sistema';

-- Políticas de limpieza (opcional, en cron diario):
-- DELETE FROM audit_logs WHERE tiempo < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- ÍNDICE para búsquedas por rango de fechas (opcional pero recomendado)
-- CREATE INDEX idx_fecha_rango ON audit_logs(tiempo DESC);
