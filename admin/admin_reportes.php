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
Se eliminó completamente el buscador de productos.

AUTOR: Sistema Inventario
========================================================
*/

include("../core/conexion.php");


/*
========================================================
CONSULTAS DE ESTADISTICAS
========================================================
*/

/* TOTAL DE PRODUCTOS REGISTRADOS */
$totalProductos = $conexion->query("
SELECT COUNT(*) as total 
FROM productos
");

$row1 = $totalProductos->fetch_assoc();


/* TOTAL DE UNIDADES EN INVENTARIO */
$totalStock = $conexion->query("
SELECT SUM(stock) as total 
FROM productos
");

$row2 = $totalStock->fetch_assoc();


/*
========================================================
CONSULTA PRODUCTOS CON STOCK BAJO
========================================================
*/

$consultaStockBajo = "
SELECT * 
FROM productos
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
SELECT * 
FROM productos
ORDER BY nombre ASC
";

$resultadoProductos = $conexion->query($consultaProductos);

?>

<style>

*{ font-family: 'Inter', sans-serif; }

.contenedor-reportes{
background:white;
padding:24px;
border-radius:12px;
border-left:6px solid #0f172a;
box-shadow:0px 5px 15px rgba(0,0,0,0.1);
width:100%;
}

.contenedor-reportes h2{
font-size:28px;
font-weight:bold;
margin-bottom:6px;
color:#1e293b;
display:flex;
align-items:center;
gap:12px;
}

.contenedor-reportes p{
color:#64748b;
margin-bottom:24px;
font-size:15px;
}

.stats-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
gap:16px;
margin-bottom:28px;
}

.stat-card{
padding:24px;
border-radius:10px;
display:flex;
flex-direction:column;
}

.stat-card.blue{
background:#eff6ff;
border-left:5px solid #1e3a8a;
}

.stat-card.green{
background:#ecfdf5;
border-left:5px solid #065f46;
}

.stat-card p{
font-size:14px;
font-weight:500;
margin:0 0 8px 0;
}

.stat-card h3{
font-size:28px;
font-weight:bold;
margin:0;
}

.stat-card.blue h3{ color:#1e3a8a; }
.stat-card.green h3{ color:#065f46; }

.contenedor-reportes h3{
margin:28px 0 16px 0;
font-size:18px;
font-weight:600;
color:#1e293b;
display:flex;
align-items:center;
gap:10px;
}

.contenedor-reportes table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

.contenedor-reportes table th{
background:#0f172a;
color:white;
padding:14px;
text-align:left;
font-weight:600;
font-size:13px;
}

.contenedor-reportes table td{
padding:14px;
border-bottom:1px solid #e2e8f0;
font-size:14px;
}

.contenedor-reportes table tr:hover{
background-color:#f8fafc;
}

.stock-bajo{
background:#fee2e2;
color:#dc2626;
padding:6px 12px;
border-radius:6px;
font-size:12px;
font-weight:600;
display:inline-block;
}

.disponible{
background:#dcfce7;
color:#16a34a;
padding:6px 12px;
border-radius:6px;
font-size:12px;
font-weight:600;
display:inline-block;
}

.boton-exportar{
background:#065f46;
color:white;
padding:10px 20px;
border:none;
border-radius:8px;
cursor:pointer;
font-weight:600;
font-size:14px;
display:inline-flex;
align-items:center;
gap:8px;
transition:all 0.3s;
margin-bottom:20px;
}

.boton-exportar:hover{
background:#047857;
transform:translateY(-2px);
}

</style>


<div class="contenedor-reportes">

<!-- ===================================================== -->
<!-- TITULO DEL MODULO -->
<!-- ===================================================== -->

<h2>📊 Reportes del Sistema</h2>

<p>Visualiza estadísticas y estado del inventario.</p>


<!-- ===================================================== -->
<!-- BOTON EXPORTAR EXCEL -->
<!-- ===================================================== -->

<a href="exportar_excel.php">
<button class="boton-exportar">
📥 Exportar Excel
</button>
</a>


<!-- ===================================================== -->
<!-- TARJETAS DE ESTADISTICAS -->
<!-- ===================================================== -->

<div class="stats-grid">

<div class="stat-card blue">
<p>Total de productos</p>
<h3><?php echo $row1['total']; ?></h3>
</div>

<div class="stat-card green">
<p>Unidades en inventario</p>
<h3><?php echo $row2['total']; ?></h3>
</div>

</div>


<!-- ===================================================== -->
<!-- TABLA PRODUCTOS CON STOCK BAJO -->
<!-- ===================================================== -->

<h3>⚠ Productos con Stock Bajo</h3>

<table>

<thead>
<tr>
<th>Producto</th>
<th>Stock</th>
<th>Estado</th>
</tr>
</thead>

<tbody>

<?php while($row = $resultadoStockBajo->fetch_assoc()){ ?>

<tr>

<td><?php echo $row['nombre']; ?></td>

<td>
<span style="color:red;font-weight:bold;">
<?php echo $row['stock']; ?>
</span>
</td>

<td>
<span class="stock-bajo">Stock Bajo</span>
</td>

</tr>

<?php } ?>

</tbody>

</table>


<!-- ===================================================== -->
<!-- TABLA COMPLETA DE PRODUCTOS -->
<!-- ===================================================== -->

<h3>📦 Tabla Completa de Productos</h3>

<table>

<thead>
<tr>
<th>Producto</th>
<th>Stock</th>
<th>Estado</th>
</tr>
</thead>

<tbody>

<?php while($row = $resultadoProductos->fetch_assoc()){ ?>

<tr>

<td><?php echo $row['nombre']; ?></td>

<td><strong><?php echo $row['stock']; ?></strong></td>

<td>

<?php
if($row['stock'] < 5){
echo '<span class="stock-bajo">Stock Bajo</span>';
}else{
echo '<span class="disponible">Disponible</span>';
}
?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>
