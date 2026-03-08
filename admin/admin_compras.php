<?php
/*
=====================================================
MODULO DE COMPRAS
=====================================================

Este archivo muestra un formulario para registrar
compras de productos en el sistema.

Función principal:
- Seleccionar un producto existente
- Ingresar la cantidad comprada
- Enviar los datos al archivo que guardará la compra
  en la base de datos.

Autor: Sistema de Inventario
*/

// Conexión a la base de datos
require('../../database/conexion.php');
?>

<!-- CONTENEDOR PRINCIPAL DEL MÓDULO -->
<div class="container" style="border:1px solid #ccc; padding:20px; border-radius:10px; background:#f9f9f9;">

    <!-- TÍTULO DEL MÓDULO -->
    <h2 style="font-size:24px; font-weight:bold; margin-bottom:20px; color:#2563eb;">
        Módulo de Compras
    </h2>

    <!-- SUBTÍTULO Y BARRA DE BÚSQUEDA -->
    <p style="margin-bottom:10px;">Gestiona y revisa las compras realizadas</p>
    <input type="text" placeholder="Buscar por producto, fecha o cantidad..." 
           class="form-control mb-3" 
           style="border:1px solid #2563eb; border-radius:5px; padding:8px;">

    <!-- BOTÓN DE ACTUALIZAR -->
    <button class="btn btn-primary" 
            style="background:#2563eb; color:white; padding:8px 15px; border:none; border-radius:5px; margin-bottom:15px;">
        Actualizar
    </button>

    <!-- FILTROS DE COMPRAS -->
    <div class="filters mt-3" style="margin-bottom:20px;">
        <button class="btn btn-secondary" style="background:#6b7280; color:white; margin-right:5px;">Todos</button>
        <button class="btn btn-secondary" style="background:#10b981; color:white; margin-right:5px;">Nuevos</button>
        <button class="btn btn-secondary" style="background:#f59e0b; color:white; margin-right:5px;">Procesados</button>
        <button class="btn btn-secondary" style="background:#ef4444; color:white;">Cancelados</button>
    </div>

    <p class="mt-3">0 compras registradas</p>

    <!-- FORMULARIO PARA REGISTRAR UNA COMPRA -->
    <form action="admin_guardar_compra.php" method="POST" 
          style="border:1px solid #ddd; padding:20px; border-radius:10px; background:#ffffff;">

        <!-- SELECT PARA MOSTRAR LOS PRODUCTOS -->
        <label style="font-weight:bold;">Producto</label><br>
        <select name="producto_id" required 
                style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px; margin-bottom:15px;">
            <?php
            /*
            Consulta SQL para obtener todos los productos
            registrados en la tabla productos
            */
            $query = mysqli_query($conexion,"SELECT * FROM productos");

            /*
            Recorre cada producto encontrado
            y lo muestra dentro del select
            */
            while($row = mysqli_fetch_array($query)){
                echo "<option value='".$row['id']."'>".$row['nombre']."</option>";
            }
            ?>
        </select>

        <!-- CAMPO PARA INGRESAR LA CANTIDAD COMPRADA -->
        <label style="font-weight:bold;">Cantidad</label><br>
        <input type="number" name="cantidad" required 
               style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px; margin-bottom:15px;">

        <!-- BOTÓN PARA GUARDAR LA COMPRA -->
        <button type="submit" 
                style="background:#2563eb; color:white; padding:10px 20px; border:none; border-radius:5px; font-weight:bold;">
            Guardar Compra
        </button>
    </form>
</div>
