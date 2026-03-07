<?php

/*
Este archivo realiza dos funciones:

1. Guarda la compra en la tabla compras
2. Aumenta el stock del producto automáticamente
*/

include("../core/conexion.php");

/* Recibir datos del formulario */
$producto_id = $_POST['producto_id'];
$cantidad = $_POST['cantidad'];

/* Guardar registro de compra */
$sql = "INSERT INTO compras (producto_id, cantidad, fecha)
VALUES ('$producto_id','$cantidad', NOW())";

$conexion->query($sql);

/*
Actualizar el stock del producto
Sumando la cantidad comprada
*/

$update = "UPDATE productos 
SET stock = stock + $cantidad
WHERE id = $producto_id";

$conexion->query($update);

/* Redireccionar al módulo */
header("Location: compras.php");

?> 