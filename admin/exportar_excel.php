<?php

/*
====================================================
EXPORTAR REPORTES A EXCEL
====================================================
Este archivo genera un archivo Excel con los datos
del inventario.
*/

include("../core/conexion.php");

/* HEADERS PARA DESCARGAR EXCEL */

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=reporte_inventario.xls");

/* CONSULTA PRODUCTOS */

$query = $conexion->query("
SELECT nombre, stock
FROM productos
");

?>

<table border="1">

<tr>
<th>Producto</th>
<th>Stock</th>
</tr>

<?php while($row = $query->fetch_assoc()){ ?>

<tr>

<td><?php echo $row['nombre']; ?></td>

<td><?php echo $row['stock']; ?></td>

</tr>

<?php } ?>

</table>