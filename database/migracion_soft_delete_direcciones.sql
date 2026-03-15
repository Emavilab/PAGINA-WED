-- ============================================================
-- Migración: Soft Delete para direcciones_cliente
-- ============================================================
-- Agrega campos para marcar direcciones como inactivas en lugar
-- de eliminarlas físicamente. Así se conserva el historial para
-- pedidos ya realizados.
--
-- Ejecutar una sola vez en la base de datos.
-- ============================================================

ALTER TABLE direcciones_cliente
ADD COLUMN activo TINYINT(1) DEFAULT 1,
ADD COLUMN fecha_eliminacion DATETIME NULL;

-- activo = 1 → dirección activa (visible para el cliente)
-- activo = 0 → dirección eliminada (soft delete, no se muestra)
