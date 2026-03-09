<?php
/*
=====================================================
MODULO DE COMPRAS
=====================================================

Este módulo permite registrar compras de productos
en el sistema de inventario.

FUNCIONES:
✔ Mostrar lista de productos disponibles
✔ Registrar la cantidad comprada
✔ Enviar los datos al archivo que guarda la compra
✔ Posteriormente actualizar el stock del producto

TABLAS RELACIONADAS:
- productos
- compras
- detalle_compra

FLUJO DEL SISTEMA:
1️⃣ El usuario selecciona un producto
2️⃣ Ingresa la cantidad comprada
3️⃣ Se envía al archivo admin_guardar_compra.php
4️⃣ Se guarda la compra
5️⃣ Se guarda el detalle de la compra
6️⃣ Se actualiza el stock del producto

Autor: Sistema de Inventario
*/

# =====================================================
# CONEXIÓN A BASE DE DATOS
# =====================================================
require_once '../core/conexion.php';


# =====================================================
# CONSULTAR PRODUCTOS
# =====================================================
# Obtiene todos los productos disponibles en inventario

$query = mysqli_query($conexion,"SELECT id_producto, nombre, stock FROM productos");
?>

<!-- =====================================================
CONTENEDOR PRINCIPAL DEL MODULO
===================================================== -->

<div class="container" style="border:1px solid #ccc; padding:20px; border-radius:10px; background:#f9f9f9;">

    <!-- =====================================================
    TITULO DEL MODULO
    ===================================================== -->

    <h2 style="font-size:24px; font-weight:bold; margin-bottom:20px; color:#2563eb;">
        📦 Módulo de Compras
    </h2>

    <!-- DESCRIPCIÓN -->
    <p style="margin-bottom:10px;">
        Gestiona y registra compras de productos para actualizar el inventario.
    </p>


    <!-- =====================================================
    BUSCADOR (solo visual)
    ===================================================== -->

    <input type="text" 
           placeholder="Buscar por producto, fecha o cantidad..." 
           style="border:1px solid #2563eb; border-radius:5px; padding:8px; width:100%; margin-bottom:15px;">


    <!-- =====================================================
    BOTON ACTUALIZAR
    ===================================================== -->

    <button style="background:#2563eb; color:white; padding:8px 15px; border:none; border-radius:5px; margin-bottom:20px;">
        Actualizar
    </button>


    <!-- =====================================================
    FORMULARIO DE REGISTRO DE COMPRA
    ===================================================== -->

    <form action="admin_guardar_compra.php" method="POST"
          style="border:1px solid #ddd; padding:20px; border-radius:10px; background:#ffffff;">


        <!-- =====================================================
        SELECT PRODUCTOS
        ===================================================== -->

        <label style="font-weight:bold;">Producto</label>

        <select name="producto_id" required
                style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px; margin-bottom:15px;">

            <?php
            /*
            Recorre los productos de la base de datos
            y los muestra dentro del select
            */

            while($row = mysqli_fetch_array($query)){

                echo "<option value='".$row['id_producto']."'>";

                echo $row['nombre']." (Stock actual: ".$row['stock'].")";

                echo "</option>";
            }
            ?>

        </select>


        <!-- =====================================================
        CANTIDAD COMPRADA
        ===================================================== -->

        <label style="font-weight:bold;">Cantidad comprada</label>

        <input type="number"
               name="cantidad"
               required
               min="1"
               style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px; margin-bottom:20px;">


        <!-- =====================================================
        BOTÓN GUARDAR COMPRA
        ===================================================== -->

        <button type="submit"
                style="background:#2563eb; color:white; padding:10px 20px; border:none; border-radius:5px; font-weight:bold;">
            Guardar Compra
        </button>

    </form>

</div>