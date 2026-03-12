<?php

/*
========================================================
MODULO DE REPORTES DEL SISTEMA
========================================================

Este módulo permite visualizar estadísticas del inventario
y generar reportes exportables.

FUNCIONES PRINCIPALES:
✔ Mostrar total de productos
✔ Mostrar total de unidades en inventario
✔ Mostrar productos con bajo stock
✔ Mostrar tabla completa de productos
✔ Exportar datos a Excel

NOTA:
Se eliminó el buscador de productos según requerimiento.

AUTOR: Sistema Inventario
*/

include("../core/conexion.php");


/*
========================================================
CONSULTAS DE ESTADISTICAS
========================================================
*/

/* TOTAL DE PRODUCTOS REGISTRADOS */
$totalProductos = $conexion->query("SELECT COUNT(*) as total FROM productos");
$row1 = $totalProductos->fetch_assoc();

/* TOTAL DE UNIDADES EN INVENTARIO */
$totalStock = $conexion->query("SELECT SUM(stock) as total FROM productos");
$row2 = $totalStock->fetch_assoc();


/*
========================================================
CONSULTA PRODUCTOS CON STOCK BAJO
========================================================
*/

$consultaStockBajo = "
SELECT * FROM productos
WHERE stock < 5
ORDER BY nombre ASC
";

$resultadoStockBajo = $conexion->query($consultaStockBajo);


/*
========================================================
CONSULTA TABLA COMPLETA DE PRODUCTOS
========================================================
*/

$consultaProductos = "
SELECT * FROM productos
ORDER BY nombre ASC
";

$resultadoProductos = $conexion->query($consultaProductos);

?>

<div style="
background:white;
padding:30px;
border-radius:10px;
border-left:6px solid #0f172a;
box-shadow:0px 5px 15px rgba(0,0,0,0.1);
">

<!-- ===================================================== -->
<!-- TITULO DEL MODULO -->
<!-- ===================================================== -->

<h2 style="font-size:26px;font-weight:bold;margin-bottom:5px;">
📊 Reportes del Sistema
</h2>

<p style="color:#555;margin-bottom:20px;">
Visualiza estadísticas y estado del inventario.
</p>


<!-- ===================================================== -->
<!-- BOTON EXPORTAR EXCEL -->
<!-- ===================================================== -->

<a href="exportar_excel.php">
<button 
type="button"
style="
background:#065f46;
color:white;
padding:10px 15px;
border:none;
border-radius:5px;
cursor:pointer;
">
Exportar Excel
</button>
</a>

<br><br>


<!-- ===================================================== -->
<!-- TARJETAS DE ESTADISTICAS -->
<!-- ===================================================== -->

<div style="display:flex;gap:20px;flex-wrap:wrap;">

<!-- TARJETA TOTAL PRODUCTOS -->

<div style="
flex:1;
min-width:200px;
background:#eff6ff;
border-left:5px solid #1e3a8a;
padding:20px;
border-radius:8px;
">
<p>Total de productos</p>

<h3 style="font-size:28px;font-weight:bold;color:#1e3a8a;">
<?php echo $row1['total']; ?>
</h3>

</div>


<!-- TARJETA TOTAL INVENTARIO -->

<div style="
flex:1;
min-width:200px;
background:#ecfdf5;
border-left:5px solid #065f46;
padding:20px;
border-radius:8px;
">

<p>Unidades en inventario</p>

<h3 style="font-size:28px;font-weight:bold;color:#065f46;">
<?php echo $row2['total']; ?>
</h3>

</div>

</div>

<br>


<!-- ===================================================== -->
<!-- TABLA PRODUCTOS CON STOCK BAJO -->
<!-- ===================================================== -->

<h3 style="margin-bottom:10px;">
⚠ Productos con Stock Bajo
</h3>

<table style="width:100%;border-collapse:collapse;border:1px solid #ccc;">

<thead style="background:#7f1d1d;color:white;">

<tr>
<th style="padding:10px;">Producto</th>
<th style="padding:10px;">Stock</th>
<th style="padding:10px;">Estado</th>
</tr>

</thead>

<tbody>

<?php while($row = $resultadoStockBajo->fetch_assoc()){ ?>

<tr style="border-bottom:1px solid #ddd;">

<td style="padding:10px;">
<?php echo $row['nombre']; ?>
</td>

<td style="padding:10px;color:red;font-weight:bold;">
<?php echo $row['stock']; ?>
</td>

<td style="padding:10px;">

<span style="
background:#fee2e2;
color:#b91c1c;
padding:5px 10px;
border-radius:20px;
font-size:12px;
">

Stock Bajo

</span>

</td>

</tr>

<?php } ?>

</tbody>
</table>

<br><br>


<!-- ===================================================== -->
<!-- TABLA COMPLETA DE PRODUCTOS
===================================================== -->

<h3 style="margin-bottom:10px;">
📦 Tabla Completa de Productos
</h3>

<table style="width:100%;border-collapse:collapse;border:1px solid #ccc;">

<thead style="background:#0f172a;color:white;">

<tr>

<th style="padding:10px;">Producto</th>
<th style="padding:10px;">Stock</th>
<th style="padding:10px;">Estado</th>

</tr>

</thead>

<tbody>

<?php while($row = $resultadoProductos->fetch_assoc()){ ?>

<tr style="border-bottom:1px solid #ddd;">

<td style="padding:10px;">
<?php echo $row['nombre']; ?>
</td>

<td style="padding:10px;font-weight:bold;">
<?php echo $row['stock']; ?>
</td>

<td style="padding:10px;">

<?php

/*
========================================================
VERIFICAR ESTADO DEL INVENTARIO
========================================================
*/

if($row['stock'] < 5){

echo '

<span style="
background:#fee2e2;
color:#b91c1c;
padding:5px 10px;
border-radius:20px;
font-size:12px;
">

Stock Bajo

</span>

';

}else{

echo '

<span style="
background:#dcfce7;
color:#166534;
padding:5px 10px;
border-radius:20px;
font-size:12px;
">

Disponible

</span>

';

}

?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>
