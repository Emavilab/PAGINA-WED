<?php
/*
MODULO DE COMPRAS

Este archivo permite registrar compras de productos
para aumentar el stock del inventario.
*/

include("../core/conexion.php");

/*
Consulta para obtener todos los productos
y mostrarlos en el formulario
*/
$productos = $conexion->query("SELECT * FROM productos");
?>

<h2>Registrar Compra de Inventario</h2>

<form action="guardar_compra.php" method="POST">

<label>Seleccionar producto</label>

<select name="producto_id">

<?php while($row = $productos->fetch_assoc()){ ?>

<option value="<?php echo $row['id']; ?>">
<?php echo $row['nombre']; ?>
</option>

<?php } ?>

</select>

<br><br>

<label>Cantidad comprada</label>

<input type="number" name="cantidad" required>

<br><br>

<button type="submit">Registrar Compra</button>

</form>