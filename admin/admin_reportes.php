<?php

/*
MODULO DE REPORTES

Este módulo muestra estadísticas del inventario
como total de productos y productos con bajo stock.
*/

include("../core/conexion.php");

/* Total productos */
$totalProductos = $conexion->query("SELECT COUNT(*) as total FROM productos");
$row1 = $totalProductos->fetch_assoc();

/* Total inventario */
$totalStock = $conexion->query("SELECT SUM(stock) as total FROM productos");
$row2 = $totalStock->fetch_assoc();

/* Productos con bajo stock */
$bajoStock = $conexion->query("SELECT * FROM productos WHERE stock < 5");

?>

<h2>Reportes del Sistema</h2>

<p>Total de productos: <?php echo $row1['total']; ?></p>

<p>Total unidades en inventario: <?php echo $row2['total']; ?></p>

<h3>Productos con bajo stock</h3>

<table border="1">

<tr>
<th>Producto</th>
<th>Stock</th>
</tr>

<?php while($row = $bajoStock->fetch_assoc()){ ?>

<tr>
<td><?php echo $row['nombre']; ?></td>
<td><?php echo $row['stock']; ?></td>
</tr>

<?php } ?>

</table> 