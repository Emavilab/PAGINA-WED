<?php
/*
========================================================
MODULO ADMINISTRADOR DE COMPRAS
========================================================

Este mÃ³dulo permite gestionar las compras del sistema.

FUNCIONES:
âœ” Mostrar lista de compras
âœ” Registrar nueva compra
âœ” Eliminar compra
âœ” Interfaz sencilla para administrador

TABLA UTILIZADA:
compras
- id
- fecha
- proveedor

AUTOR: Sistema Inventario
========================================================
*/

/* CONEXION A BASE DE DATOS */
include("../core/conexion.php");


/* =====================================================
REGISTRAR NUEVA COMPRA
=====================================================*/

if(isset($_POST['guardar']))
{

    $fecha = $_POST['fecha'];
    $proveedor = $_POST['proveedor'];

    $sql = "INSERT INTO compras (fecha, proveedor)
            VALUES ('$fecha','$proveedor')";

    mysqli_query($conexion,$sql);

}


/* =====================================================
ELIMINAR COMPRA
=====================================================*/

if(isset($_GET['eliminar']))
{

$id = $_GET['eliminar'];

$sql = "DELETE FROM compras WHERE id='$id'";

mysqli_query($conexion,$sql);

}


/* =====================================================
CONSULTAR COMPRAS
=====================================================*/

$compras = mysqli_query($conexion,"SELECT * FROM compras ORDER BY id DESC");

?>

<style>
* { font-family: 'Inter', sans-serif; }

.contenedor-compras {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    width: 100%;
    max-width: none;
}

.contenedor-compras h1 {
    color: #1e293b;
    font-size: 28px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.contenedor-compras h1 .material-icons-round {
    font-size: 32px;
    color: #28a745;
}

.contenedor-compras h3 {
    color: #475569;
    font-size: 18px;
    font-weight: 600;
    margin-top: 28px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.contenedor-compras h3 .material-icons-round {
    font-size: 24px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
}

.contenedor-compras label {
    font-weight: 600;
    color: #334155;
    font-size: 14px;
}

.contenedor-compras input[type="datetime-local"],
.contenedor-compras input[type="text"] {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.contenedor-compras input:focus {
    outline: none;
    border-color: #28a745;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.contenedor-compras button {
    background: #28a745;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.contenedor-compras button:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(33, 136, 56, 0.3);
}

.contenedor-compras table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 24px;
    overflow-x: auto;
}

.contenedor-compras table th {
    background: #1e293b;
    color: white;
    padding: 14px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.5px;
}

.contenedor-compras table td {
    padding: 14px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
}

.contenedor-compras table tr:hover {
    background-color: #f8fafc;
}

.contenedor-compras .eliminar {
    background: #ef4444;
    padding: 8px 14px;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s;
}

.contenedor-compras .eliminar:hover {
    background: #dc2626;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.material-icons-round {
    font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
}

@media (max-width: 768px) {
    .contenedor-compras {
        padding: 16px;
    }
    
    .contenedor-compras h1 {
        font-size: 22px;
    }
    
    .contenedor-compras table {
        font-size: 12px;
    }
    
    .contenedor-compras table th,
    .contenedor-compras table td {
        padding: 10px;
    }
}
</style>

<div class="contenedor-compras">

<h1>
  <span class="material-icons-round">shopping_bag</span>
  Modulo de Compras
</h1>

<!-- ===================================================== -->
<!-- FORMULARIO REGISTRAR COMPRA -->
<!-- ===================================================== -->

<h3>
  <span class="material-icons-round">add_circle</span>
  Registrar Nueva Compra
</h3>

<form method="POST">
  <div class="form-group">
    <label for="fecha">Fecha:</label>
    <input type="datetime-local" id="fecha" name="fecha" required>
  </div>

  <div class="form-group">
    <label for="proveedor">Proveedor:</label>
    <input type="text" id="proveedor" name="proveedor" placeholder="Nombre del proveedor" required>
  </div>

  <div class="form-actions">
    <button type="submit" name="guardar">
      <span class="material-icons-round">save</span>
      Guardar Compra
    </button>
  </div>
</form>


<!-- ===================================================== -->
<!-- TABLA DE COMPRAS -->
<!-- ===================================================== -->

<h3>
  <span class="material-icons-round">list</span>
  Lista de Compras
</h3>

<div style="overflow-x: auto;">
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Fecha</th>
      <th>Proveedor</th>
      <th>AcciÃ³n</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = mysqli_fetch_assoc($compras)){ ?>
    <tr>
      <td><?php echo $row['id']; ?></td>
      <td><?php echo $row['fecha']; ?></td>
      <td><?php echo $row['proveedor']; ?></td>
      <td>
        <a class="eliminar"
           href="admin_compras.php?eliminar=<?php echo $row['id']; ?>"
           onclick="return confirm('Â¿Eliminar esta compra?')">
          <span class="material-icons-round" style="font-size: 18px;">delete</span>
          Eliminar
        </a>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
</div>

</div>
