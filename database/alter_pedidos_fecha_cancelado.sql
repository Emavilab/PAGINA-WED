-- Asegurar que fecha_pedido sea DATETIME con hora para el sistema de cancelación (3 horas).
-- Incluir estado 'cancelado' en el enum si no existe.
-- Ejecutar una sola vez en la base de datos.

ALTER TABLE pedidos
MODIFY COLUMN fecha_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- Si el enum de estado no incluye 'cancelado', descomentar y ejecutar (depende de tu versión MySQL/MariaDB):
-- ALTER TABLE pedidos MODIFY COLUMN estado ENUM('pendiente','confirmado','enviado','entregado','cancelado') DEFAULT 'pendiente';
