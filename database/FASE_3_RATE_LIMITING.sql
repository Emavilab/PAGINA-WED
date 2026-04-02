-- FASE 3-1: RATE LIMITING - CREAR TABLA
-- Ejecutar esta query en phpMyAdmin para crear la tabla de login attempts

-- Tabla para registrar intentos de login
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ip` VARCHAR(45) NOT NULL COMMENT 'Dirección IP del cliente (IPv4 o IPv6)',
  `usuario` VARCHAR(100) NULL COMMENT 'Nombre de usuario intentado',
  `intentos_exitosos` TINYINT(1) DEFAULT 0 COMMENT '1=login exitoso, 0=fallido',
  `razon` VARCHAR(255) DEFAULT 'intento_fallido' COMMENT 'Razón del fallo',
  `tiempo` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Cuándo ocurrió el intento',
  `bloqueado_hasta` DATETIME NULL COMMENT 'Si está bloqueado, cuándo se desbloquea',
  
  -- ÍNDICES para mejor performance
  INDEX `idx_ip` (`ip`),
  INDEX `idx_usuario` (`usuario`),
  INDEX `idx_tiempo` (`tiempo`),
  INDEX `idx_bloqueado` (`bloqueado_hasta`),
  INDEX `idx_ip_tiempo` (`ip`, `tiempo`),
  INDEX `idx_usuario_tiempo` (`usuario`, `tiempo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Registro de intentos de login para rate limiting y seguridad';

-- Política de limpieza automática (opcional, en cron diario):
-- DELETE FROM login_attempts WHERE tiempo < DATE_SUB(NOW(), INTERVAL 30 DAY);
