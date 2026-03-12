<?php
/*
=====================================================
MODULO DE COMPRAS
=====================================================

Este módulo permite registrar compras de productos
en el sistema de inventario y mostrar los datos
guardados en la misma página automáticamente.

FUNCIONES:
✔ Mostrar lista de productos disponibles
✔ Registrar la cantidad comprada
✔ Guardar la compra en la base de datos
✔ Actualizar el stock del producto
✔ Mostrar confirmación con los datos guardados

TABLAS RELACIONADAS:
- productos
- compras
- detalle_compra

Autor: Sistema de Inventario
*/

# =====================================================
# CONEXIÓN A BASE DE DATOS
# =====================================================
require_once '../core/conexion.php'; // ✅ Ajusta la ruta según tu proyecto

# =====================================================
# CONSULTAR PRODUCTOS
# =====================================================
$query = mysqli_query($conexion,"SELECT id_producto, nombre, stock FROM productos");
?>

<!-- =====================================================
CONTENEDOR PRINCIPAL DEL MODULO
===================================================== -->
<div class="container" style="border:1px solid #ccc; padding:20px; border-radius:10px; background:#f9f9f9;">

    <!-- TITULO DEL MODULO -->
    <h2 style="font-size:24px; font-weight:bold; margin-bottom:20px; color:#2563eb;">
        📦 Módulo de Compras
    </h2>

    <!-- DESCRIPCIÓN -->
    <p style="margin-bottom:10px;">
        Gestiona y registra compras de productos para actualizar el inventario.
    </p>

    <!-- FORMULARIO DE REGISTRO DE COMPRA -->
    <form id="formCompra" action="admin_guardar_compra.php" method="POST"
          style="border:1px solid #ddd; padding:20px; border-radius:10px; background:#ffffff;">

        <!-- SELECT PRODUCTOS -->
        <label style="font-weight:bold;">Producto</label>
        <select name="producto_id" required
                style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px; margin-bottom:15px;">
            <option value="">Seleccione un producto</option>
            <?php
            while($row = mysqli_fetch_array($query)){
                echo "<option value='".$row['id_producto']."'>";
                echo $row['nombre']." (Stock actual: ".$row['stock'].")";
                echo "</option>";
            }
            ?>
        </select>

        <!-- CANTIDAD COMPRADA -->
        <label style="font-weight:bold;">Cantidad comprada</label>
        <input type="number"
               name="cantidad"
               required
               min="1"
               style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px; margin-bottom:20px;">

        <!-- BOTÓN GUARDAR COMPRA -->
        <button type="submit"
                style="background:#2563eb; color:white; padding:10px 20px; border:none; border-radius:5px; font-weight:bold;">
            Guardar Compra
        </button>
    </form>

    <!-- CONTENEDOR PARA RESULTADO -->
    <div id="resultado"></div>
</div>

<!-- =====================================================
SCRIPT AJAX PARA PROCESAR COMPRA
===================================================== -->
<script>
document.getElementById('formCompra').onsubmit = function(e){
    e.preventDefault(); // Evita que se recargue la página
    var formData = new FormData(this);

    fetch('admin_guardar_compra.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById('resultado').innerHTML = data;
    });
};
</script>
