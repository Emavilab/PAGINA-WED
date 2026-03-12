<?php
/*
=====================================================
MODULO DE COMPRAS
=====================================================
Registrar compras de productos, actualizar stock
y mostrar historial de compras realizadas.
=====================================================
*/

require_once '../core/conexion.php';

// Consultar productos disponibles
$query = $conexion->query("SELECT id_producto, nombre, stock, precio, precio_costo FROM productos ORDER BY nombre ASC");

// Consultar historial de compras
$historial = $conexion->query("
    SELECT c.id, c.proveedor, c.fecha, 
           dc.cantidad, dc.precio,
           p.nombre AS producto, p.stock AS stock_actual
    FROM compras c
    INNER JOIN detalle_compra dc ON dc.compra_id = c.id
    INNER JOIN productos p ON p.id_producto = dc.producto_id
    ORDER BY c.fecha DESC
    LIMIT 50
");
?>

<!-- CONTENEDOR PRINCIPAL -->
<div class="container" style="max-width:1100px; margin:auto; padding:20px;">

    <!-- FORMULARIO DE REGISTRO DE COMPRA -->
    <div style="border:1px solid #e2e8f0; padding:25px; border-radius:12px; background:#ffffff; margin-bottom:30px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="font-size:22px; font-weight:bold; margin-bottom:20px; color:#2563eb;">
            📦 Registrar Nueva Compra
        </h2>

        <form id="formCompra">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                <!-- Producto -->
                <div>
                    <label style="font-weight:600; display:block; margin-bottom:5px;">Producto</label>
                    <select name="producto_id" id="selectProducto" required
                            style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
                        <option value="">Seleccione un producto</option>
                        <?php while($row = $query->fetch_assoc()): ?>
                            <option value="<?= intval($row['id_producto']) ?>" 
                                    data-precio="<?= number_format((float)$row['precio_costo'], 2, '.', '') ?>">
                                <?= htmlspecialchars($row['nombre']) ?> (Stock: <?= intval($row['stock']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <!-- Proveedor -->
                <div>
                    <label style="font-weight:600; display:block; margin-bottom:5px;">Proveedor</label>
                    <input type="text" name="proveedor" required placeholder="Nombre del proveedor"
                           style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; box-sizing:border-box;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:20px;">
                <!-- Cantidad -->
                <div>
                    <label style="font-weight:600; display:block; margin-bottom:5px;">Cantidad</label>
                    <input type="number" name="cantidad" required min="1" placeholder="Ej: 10"
                           style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; box-sizing:border-box;">
                </div>
                <!-- Precio unitario -->
                <div>
                    <label style="font-weight:600; display:block; margin-bottom:5px;">Precio unitario (costo)</label>
                    <input type="number" name="precio" id="inputPrecio" required min="0" step="0.01" placeholder="0.00"
                           style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; box-sizing:border-box;">
                </div>
            </div>

            <button type="submit" id="btnGuardar"
                    style="background:#2563eb; color:white; padding:12px 30px; border:none; border-radius:8px; font-weight:bold; font-size:15px; cursor:pointer;">
                💾 Guardar Compra
            </button>
        </form>

        <!-- Mensaje de resultado -->
        <div id="resultado" style="margin-top:15px;"></div>
    </div>

    <!-- HISTORIAL DE COMPRAS -->
    <div style="border:1px solid #e2e8f0; padding:25px; border-radius:12px; background:#ffffff; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="font-size:22px; font-weight:bold; margin-bottom:20px; color:#1e293b;">
            📋 Historial de Compras
        </h2>

        <div style="overflow-x:auto;">
            <table id="tablaHistorial" style="width:100%; border-collapse:collapse; font-size:14px;">
                <thead>
                    <tr style="background:#f1f5f9; text-align:left;">
                        <th style="padding:12px; border-bottom:2px solid #e2e8f0;">#</th>
                        <th style="padding:12px; border-bottom:2px solid #e2e8f0;">Producto</th>
                        <th style="padding:12px; border-bottom:2px solid #e2e8f0;">Proveedor</th>
                        <th style="padding:12px; border-bottom:2px solid #e2e8f0;">Cantidad</th>
                        <th style="padding:12px; border-bottom:2px solid #e2e8f0;">Precio Unit.</th>
                        <th style="padding:12px; border-bottom:2px solid #e2e8f0;">Total</th>
                        <th style="padding:12px; border-bottom:2px solid #e2e8f0;">Fecha</th>
                        <th style="padding:12px; border-bottom:2px solid #e2e8f0;">Stock Actual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($historial && $historial->num_rows > 0): ?>
                        <?php while($c = $historial->fetch_assoc()): ?>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px;"><?= intval($c['id']) ?></td>
                            <td style="padding:10px;"><?= htmlspecialchars($c['producto']) ?></td>
                            <td style="padding:10px;"><?= htmlspecialchars($c['proveedor']) ?></td>
                            <td style="padding:10px; text-align:center;"><?= intval($c['cantidad']) ?></td>
                            <td style="padding:10px;">L. <?= number_format((float)$c['precio'], 2) ?></td>
                            <td style="padding:10px; font-weight:600;">L. <?= number_format((float)$c['precio'] * intval($c['cantidad']), 2) ?></td>
                            <td style="padding:10px;"><?= date('d/m/Y H:i', strtotime($c['fecha'])) ?></td>
                            <td style="padding:10px; text-align:center;">
                                <span style="background:#dcfce7; color:#166534; padding:3px 10px; border-radius:12px; font-weight:600;">
                                    <?= intval($c['stock_actual']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="padding:30px; text-align:center; color:#94a3b8;">
                                No hay compras registradas aún.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SCRIPT AJAX -->
<script>
// Auto-rellenar precio al seleccionar producto
document.getElementById('selectProducto').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const precio = option.getAttribute('data-precio');
    if (precio) {
        document.getElementById('inputPrecio').value = precio;
    }
});

// Enviar formulario por AJAX
document.getElementById('formCompra').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.textContent = '⏳ Guardando...';

    fetch('admin_guardar_compra.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(res => res.json())
    .then(data => {
        const div = document.getElementById('resultado');
        if (data.exito) {
            div.innerHTML = `
                <div style="background:#dcfce7; border:1px solid #86efac; color:#166534; padding:15px; border-radius:8px; margin-top:10px;">
                    ✅ <strong>${data.mensaje}</strong><br>
                    Producto: ${data.datos.producto} | Cantidad: ${data.datos.cantidad} | 
                    Proveedor: ${data.datos.proveedor} | Nuevo stock: <strong>${data.datos.nuevo_stock}</strong>
                </div>`;
            document.getElementById('formCompra').reset();
            // Recargar historial
            setTimeout(() => { loadPage('./admin_compras.php'); }, 1500);
        } else {
            div.innerHTML = `
                <div style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; padding:15px; border-radius:8px; margin-top:10px;">
                    ❌ ${data.mensaje}
                </div>`;
        }
    })
    .catch(() => {
        document.getElementById('resultado').innerHTML = `
            <div style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; padding:15px; border-radius:8px; margin-top:10px;">
                ❌ Error de conexión al servidor.
            </div>`;
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = '💾 Guardar Compra';
    });
});
</script>
