<?php

/*
=====================================================
GUARDAR COMPRA EN LA BASE DE DATOS
=====================================================

Este archivo recibe los datos del formulario
del modulo de compras y realiza dos acciones:

1) Guarda la compra en la tabla "compras"
2) Actualiza el stock del producto comprado

Autor: Sistema de Inventario
*/

// Conectar con la base de datos
require('../../database/conexion.php');


/*
=====================================================
RECIBIR LOS DATOS DEL FORMULARIO
=====================================================
*/

// ID del producto seleccionado
$producto_id = $_POST['producto_id'];

// Cantidad comprada
$cantidad = $_POST['cantidad'];


/*
=====================================================
INSERTAR LA COMPRA EN LA TABLA COMPRAS
=====================================================
*/

mysqli_query($conexion,"
INSERT INTO compras(producto_id,cantidad,fecha)
VALUES('$producto_id','$cantidad',NOW())
");


/*
=====================================================
ACTUALIZAR EL STOCK DEL PRODUCTO
=====================================================

Cada vez que se registra una compra,
el sistema aumenta el stock del producto.
*/

mysqli_query($conexion,"
UPDATE productos
SET stock = stock + $cantidad
WHERE id = $producto_id
");


/*
=====================================================
REDIRECCIONAR AL DASHBOARD
=====================================================

Después de guardar la compra el sistema
vuelve al panel principal.
*/

header("Location: Dashboard.php");

?>