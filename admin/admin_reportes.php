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
✔ Buscar productos
✔ Filtrar reportes
✔ Exportar datos a Excel

AUTOR: Sistema Inventario
*/

include("../core/conexion.php");

/*
========================================================
CONSULTAS A BASE DE DATOS
========================================================
*/

/* TOTAL PRODUCTOS */
$totalProductos = $conexion->query("
SELECT COUNT(*) as total FROM productos
");
$row1 = $totalProductos->fetch_assoc();

/* TOTAL UNIDADES INVENTARIO */
$totalStock = $conexion->query("
SELECT SUM(stock) as total FROM productos
");
$row2 = $totalStock->fetch_assoc();

/* PRODUCTOS CON STOCK BAJO */
$bajoStock = $conexion->query("
SELECT * FROM productos WHERE stock < 5
");

?>

<div style="
background:white;
padding:30px;
border-radius:10px;
border-left:6px solid #0f172a;
box-shadow:0px 5px 15px rgba(0,0,0,0.1);
">

<!-- ===================================================== -->
<!-- TITULO -->
<!-- ===================================================== -->

<h2 style="font-size:26px;font-weight:bold;margin-bottom:5px;">
📊 Reportes del Sistema
</h2>

<p style="color:#555;margin-bottom:20px;">
Visualiza estadísticas y estado del inventario.
</p>


<!-- ===================================================== -->
<!-- BUSCADOR -->
<!-- ===================================================== -->

<form method="GET">

<input 
type="text" 
name="buscar"
placeholder="Buscar producto..."
style="
padding:10px;
width:300px;
border:1px solid #ccc;
border-radius:5px;
">

<button 
type="submit"
style="
background:#1e3a8a;
color:white;
padding:10px 15px;
border:none;
border-radius:5px;
cursor:pointer;
">

Buscar

</button>


<a href="admin_reportes.php">

<button 
type="button"
style="
background:#0f172a;
color:white;
padding:10px 15px;
border:none;
border-radius:5px;
cursor:pointer;
">

Actualizar

</button>

</a>

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

</form>


<br>


<!-- ===================================================== -->
<!-- TARJETAS DE ESTADISTICAS -->
<!-- ===================================================== -->

<div style="display:flex;gap:20px;flex-wrap:wrap;">

<!-- TOTAL PRODUCTOS -->

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


<!-- TOTAL INVENTARIO -->

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
<!-- TABLA PRODUCTOS BAJO STOCK -->
<!-- ===================================================== -->

<h3 style="margin-bottom:10px;">
⚠ Productos con Bajo Stock
</h3>

<table style="
width:100%;
border-collapse:collapse;
border:1px solid #ccc;
">

<thead style="background:#0f172a;color:white;">

<tr>

<th style="padding:10px;">Producto</th>
<th style="padding:10px;">Stock</th>
<th style="padding:10px;">Estado</th>

</tr>

</thead>

<tbody>

<?php

/* BUSQUEDA */

if(isset($_GET['buscar'])){

$buscar = $_GET['buscar'];

$bajoStock = $conexion->query("
SELECT * FROM productos
WHERE stock < 5
AND nombre LIKE '%$buscar%'
");

}

/* MOSTRAR RESULTADOS */

while($row = $bajoStock->fetch_assoc()){

?>

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

</div> 