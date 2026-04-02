USE negocio_web;
START TRANSACTION;

DELIMITER $$

CREATE PROCEDURE apply_fk_migration(
    IN p_table_name VARCHAR(64),
    IN p_constraint_name VARCHAR(64),
    IN p_drop_sql LONGTEXT,
    IN p_add_sql LONGTEXT
)
BEGIN
    DECLARE constraint_count INT DEFAULT 0;

    SELECT COUNT(*)
      INTO constraint_count
      FROM information_schema.TABLE_CONSTRAINTS
     WHERE CONSTRAINT_SCHEMA = DATABASE()
       AND TABLE_NAME = p_table_name
       AND CONSTRAINT_NAME = p_constraint_name
       AND CONSTRAINT_TYPE = 'FOREIGN KEY';

    IF constraint_count > 0 THEN
        SET @stmt_sql = p_drop_sql;
        PREPARE stmt FROM @stmt_sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;

    SET @stmt_sql = p_add_sql;
    PREPARE stmt FROM @stmt_sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

CREATE PROCEDURE apply_index_migration(
    IN p_table_name VARCHAR(64),
    IN p_index_name VARCHAR(64),
    IN p_add_sql LONGTEXT
)
BEGIN
    DECLARE index_count INT DEFAULT 0;

    SELECT COUNT(*)
      INTO index_count
      FROM information_schema.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = p_table_name
       AND INDEX_NAME = p_index_name;

    IF index_count = 0 THEN
        SET @stmt_sql = p_add_sql;
        PREPARE stmt FROM @stmt_sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;

CALL apply_fk_migration(
    'carrito_detalle',
    'carrito_detalle_ibfk_1',
    'ALTER TABLE carrito_detalle DROP FOREIGN KEY carrito_detalle_ibfk_1',
    'ALTER TABLE carrito_detalle ADD CONSTRAINT carrito_detalle_ibfk_1 FOREIGN KEY (id_carrito) REFERENCES carritos(id_carrito) ON DELETE CASCADE'
);

CALL apply_fk_migration(
    'carritos',
    'carritos_ibfk_1',
    'ALTER TABLE carritos DROP FOREIGN KEY carritos_ibfk_1',
    'ALTER TABLE carritos ADD CONSTRAINT carritos_ibfk_1 FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE'
);

CALL apply_fk_migration(
    'detalle_pedido',
    'detalle_pedido_ibfk_1',
    'ALTER TABLE detalle_pedido DROP FOREIGN KEY detalle_pedido_ibfk_1',
    'ALTER TABLE detalle_pedido ADD CONSTRAINT detalle_pedido_ibfk_1 FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE'
);

CALL apply_fk_migration(
    'direcciones_cliente',
    'direcciones_cliente_ibfk_1',
    'ALTER TABLE direcciones_cliente DROP FOREIGN KEY direcciones_cliente_ibfk_1',
    'ALTER TABLE direcciones_cliente ADD CONSTRAINT direcciones_cliente_ibfk_1 FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE'
);

CALL apply_fk_migration(
    'historial_pedido',
    'historial_pedido_ibfk_1',
    'ALTER TABLE historial_pedido DROP FOREIGN KEY historial_pedido_ibfk_1',
    'ALTER TABLE historial_pedido ADD CONSTRAINT historial_pedido_ibfk_1 FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE'
);

CALL apply_fk_migration(
    'historial_pedido',
    'historial_pedido_ibfk_2',
    'ALTER TABLE historial_pedido DROP FOREIGN KEY historial_pedido_ibfk_2',
    'ALTER TABLE historial_pedido ADD CONSTRAINT historial_pedido_ibfk_2 FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE'
);

CALL apply_fk_migration(
    'pedidos',
    'pedidos_ibfk_1',
    'ALTER TABLE pedidos DROP FOREIGN KEY pedidos_ibfk_1',
    'ALTER TABLE pedidos ADD CONSTRAINT pedidos_ibfk_1 FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE'
);

CALL apply_index_migration(
    'lista_deseos',
    'fk_lista_deseos_producto',
    'ALTER TABLE lista_deseos ADD INDEX fk_lista_deseos_producto (id_producto)'
);

CALL apply_fk_migration(
    'lista_deseos',
    'fk_lista_deseos_cliente',
    'ALTER TABLE lista_deseos DROP FOREIGN KEY fk_lista_deseos_cliente',
    'ALTER TABLE lista_deseos ADD CONSTRAINT fk_lista_deseos_cliente FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE'
);

CALL apply_fk_migration(
    'lista_deseos',
    'fk_lista_deseos_producto',
    'ALTER TABLE lista_deseos DROP FOREIGN KEY fk_lista_deseos_producto',
    'ALTER TABLE lista_deseos ADD CONSTRAINT fk_lista_deseos_producto FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE'
);

DROP PROCEDURE apply_fk_migration;
DROP PROCEDURE apply_index_migration;

COMMIT;
