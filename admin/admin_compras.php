<?php
/*
========================================================
MODULO ADMINISTRADOR DE COMPRAS
========================================================

Este módulo permite gestionar las compras del sistema.

FUNCIONES:
✔ Mostrar lista de compras
✔ Registrar nueva compra
✔ Eliminar compra
✔ Interfaz sencilla para administrador

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

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Administrador de Compras</title>

<style>

/* ===============================
DISEÑO SIMPLE ADMIN
===============================*/

body{
font-family: Arial;
background:#f4f6f9;
margin:40px;
}

h1{
color:#333;
}

.contenedor{
background:white;
padding:20px;
border-radius:8px;
box-shadow:0px 0px 10px rgba(0,0,0,0.1);
}

input,button{
padding:8px;
margin:5px;
}

button{
background:#28a745;
color:white;
border:none;
cursor:pointer;
}

button:hover{
background:#218838;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

table th{
background:#343a40;
color:white;
padding:10px;
}

table td{
padding:10px;
border-bottom:1px solid #ddd;
}

.eliminar{
background:red;
padding:5px 10px;
color:white;
text-decoration:none;
}

</style>

</head>

<body>

<div class="contenedor">

<h1>📦 Módulo de Compras</h1>

<!-- =====================================================
FORMULARIO REGISTRAR COMPRA
===================================================== -->

<h3>Registrar Nueva Compra</h3>

<form method="POST">

<label>Fecha:</label><br>
<input type="datetime-local" name="fecha" required>

<br>

<label>Proveedor:</label><br>
<input type="text" name="proveedor" placeholder="Nombre proveedor" required>

<br><br>

<button type="submit" name="guardar">Guardar Compra</button>

</form>


<!-- =====================================================
TABLA DE COMPRAS
===================================================== -->

<h3>Lista de Compras</h3>

<table>

<tr>
<th>ID</th>
<th>Fecha</th>
<th>Proveedor</th>
<th>Acción</th>
</tr>

<?php while($row = mysqli_fetch_assoc($compras)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['fecha']; ?></td>

<td><?php echo $row['proveedor']; ?></td>

<td>

<a class="eliminar"
href="admin_compras.php?eliminar=<?php echo $row['id']; ?>"
onclick="return confirm('¿Eliminar compra?')">
Eliminar
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>