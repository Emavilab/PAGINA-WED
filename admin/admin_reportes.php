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
✔ Buscar productos (redirige al módulo productos)
✔ Mostrar tabla completa de productos
✔ Exportar datos a Excel

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
Si se usa el buscador filtra resultados
========================================================
*/
if(isset($_GET['buscar']) && $_GET['buscar'] != ""){
    $buscar = $conexion->real_escape_string($_GET['buscar']);
    $consultaProductos = "
    SELECT * FROM productos
    WHERE nombre LIKE '%$buscar%'
    ORDER BY nombre ASC
    ";
}else{
    $consultaProductos = "
    SELECT * FROM productos
    ORDER BY nombre ASC
    ";
}
$resultadoProductos = $conexion->query($consultaProductos);

?>

<style>
* { font-family: 'Inter', sans-serif; }

.contenedor-reportes {
    background: white;
    padding: 24px;
    border-radius: 12px;
    border-left: 6px solid #0f172a;
    box-shadow: 0px 5px 15px rgba(0,0,0,0.1);
    width: 100%;
    max-width: none;
}

.contenedor-reportes h2 {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 6px;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 12px;
}

.contenedor-reportes h2 .material-icons-round {
    font-size: 32px;
    color: #0f172a;
}

.contenedor-reportes p {
    color: #64748b;
    margin-bottom: 24px;
    font-size: 15px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}

.stat-card {
    flex: 1;
    min-width: 200px;
    padding: 24px;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
}

.stat-card.blue {
    background: #eff6ff;
    border-left: 5px solid #1e3a8a;
}

.stat-card.green {
    background: #ecfdf5;
    border-left: 5px solid #065f46;
}

.stat-card p {
    font-size: 14px;
    font-weight: 500;
    margin: 0 0 8px 0;
}

.stat-card h3 {
    font-size: 28px;
    font-weight: bold;
    margin: 0;
}

.stat-card.blue h3 { color: #1e3a8a; }
.stat-card.green h3 { color: #065f46; }

.contenedor-reportes h3 {
    margin: 28px 0 16px 0;
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 10px;
}

.contenedor-reportes h3 .material-icons-round {
    font-size: 24px;
}

.contenedor-reportes form {
    display: flex;
    gap: 12px;
    align-items: flex-end;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.contenedor-reportes form input[type="text"] {
    flex: 1;
    min-width: 200px;
    padding: 10px 14px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.contenedor-reportes form input:focus {
    outline: none;
    border-color: #0f172a;
}

.contenedor-reportes form a button {
    background: #065f46;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.contenedor-reportes form a button:hover {
    background: #047857;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(4, 120, 87, 0.3);
}

.contenedor-reportes table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.contenedor-reportes table th {
    background: #0f172a;
    color: white;
    padding: 14px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.5px;
}

.contenedor-reportes table td {
    padding: 14px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
}

.contenedor-reportes table tr:hover {
    background-color: #f8fafc;
}

.stock-bajo {
    background: #fee2e2;
    color: #dc2626;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.disponible {
    background: #dcfce7;
    color: #16a34a;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.material-icons-round {
    font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
}

@media (max-width: 768px) {
    .contenedor-reportes {
        padding: 16px;
    }
    
    .contenedor-reportes h2 {
        font-size: 22px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .contenedor-reportes form {
        flex-direction: column;
    }
    
    .contenedor-reportes form input[type="text"],
    .contenedor-reportes form a button {
        width: 100%;
    }
    
    .contenedor-reportes table {
        font-size: 12px;
    }
    
    .contenedor-reportes table th,
    .contenedor-reportes table td {
        padding: 10px;
    }
}
</style>

<div class="contenedor-reportes">

<!-- ===================================================== -->
<!-- TITULO DEL MODULO -->
<!-- ===================================================== -->
<h2>
  <span class="material-icons-round">bar_chart</span>
  Reportes del Sistema
</h2>

<p>Visualiza estadísticas y estado del inventario.</p>

<!-- ===================================================== -->
<!-- FORMULARIO BUSCADOR -->
<!-- ===================================================== -->
<form method="GET" action="productos.php">
  <input 
    type="text" 
    name="buscar"
    value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>"
    placeholder="Buscar producto por nombre, estado o stock...">

  <a href="exportar_excel.php">
    <button type="button">
      <span class="material-icons-round">file_download</span>
      Exportar Excel
    </button>
  </a>
</form>

<!-- ===================================================== -->
<!-- TARJETAS DE ESTADISTICAS -->
<!-- ===================================================== -->
<div class="stats-grid">
  <!-- TARJETA TOTAL PRODUCTOS -->
  <div class="stat-card blue">
    <p>Total de productos</p>
    <h3><?php echo $row1['total']; ?></h3>
  </div>

  <!-- TARJETA TOTAL INVENTARIO -->
  <div class="stat-card green">
    <p>Unidades en inventario</p>
    <h3><?php echo $row2['total']; ?></h3>
  </div>
</div>

<!-- ===================================================== -->
<!-- TABLA PRODUCTOS CON STOCK BAJO -->
<!-- ===================================================== -->
<h3>
  <span class="material-icons-round">warning</span>
  Productos con Stock Bajo
</h3>

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
      <td><span style="color:red;font-weight:bold;"><?php echo $row['stock']; ?></span></td>
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
<h3>
  <span class="material-icons-round">inventory_2</span>
  Tabla Completa de Productos
</h3>

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
