<?php

/*
=====================================================
GUARDAR COMPRA EN LA BASE DE DATOS
=====================================================

Este archivo recibe los datos enviados desde
el formulario del módulo de compras y realiza
las siguientes acciones:

1️⃣ Crea un registro en la tabla "compras"
2️⃣ Obtiene el ID de la compra creada
3️⃣ Inserta el detalle en la tabla "detalle_compra"
4️⃣ Actualiza el stock del producto comprado
5️⃣ Redirige al módulo de compras

TABLAS UTILIZADAS
-----------------------------------------------------
productos
compras
detalle_compra

FLUJO DEL SISTEMA
-----------------------------------------------------
Formulario Compras
        │
        ▼
admin_guardar_compra.php
        │
        ▼
1 Insertar compra
2 Insertar detalle compra
3 Actualizar stock
4 Redirigir

Autor: Sistema de Inventario
*/

# =====================================================
# CONEXIÓN A LA BASE DE DATOS
# =====================================================

require('../../database/conexion.php');


# =====================================================
# RECIBIR DATOS DEL FORMULARIO
# =====================================================

/*
Datos enviados desde admin_compras.php
*/

$producto_id = $_POST['producto_id'];   // ID del producto seleccionado
$cantidad    = $_POST['cantidad'];      // Cantidad comprada


# =====================================================
# 1 CREAR REGISTRO EN TABLA COMPRAS
# =====================================================

/*
Se crea una nueva compra.
Aquí solo se guarda la fecha.
Los productos se guardan en detalle_compra.
*/

mysqli_query($conexion,"
INSERT INTO compras(fecha)
VALUES(NOW())
");


# =====================================================
# 2 OBTENER ID DE LA COMPRA
# =====================================================

/*
mysqli_insert_id obtiene el ID generado
por la última consulta INSERT.
*/

$compra_id = mysqli_insert_id($conexion);


# =====================================================
# 3 INSERTAR DETALLE DE LA COMPRA
# =====================================================

/*
Se guarda la relación entre:

compra
producto
cantidad
*/

mysqli_query($conexion,"
INSERT INTO detalle_compra
(compra_id, producto_id, cantidad)

VALUES
('$compra_id', '$producto_id', '$cantidad')
");


# =====================================================
# 4 ACTUALIZAR STOCK DEL PRODUCTO
# =====================================================

/*
Cada compra aumenta el stock del inventario
del producto seleccionado.
*/

mysqli_query($conexion,"
UPDATE productos
SET stock = stock + $cantidad
WHERE id = $producto_id
");


# =====================================================
# 5 REDIRECCIONAR AL MODULO DE COMPRAS
# =====================================================

/*
Después de guardar la compra
el sistema vuelve al módulo de compras.
*/

header("Location: admin_compras.php");

?>