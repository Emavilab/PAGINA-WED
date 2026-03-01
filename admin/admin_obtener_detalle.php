<?php
require_once '../core/sesiones.php';

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    exit("No autorizado");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("Pedido inválido");
}

$id_pedido = intval($_GET['id']);

/* ================================
   OBTENER PEDIDO
================================ */

$sqlPedido = "
SELECT 
    p.*,
    c.nombre AS cliente,
    m.nombre AS metodo_envio,
    m.costo AS costo_envio,
    dc.direccion,
    dc.ciudad,
    dc.codigo_postal,
    dc.telefono,
    dc.referencia
FROM pedidos p
INNER JOIN clientes c ON p.id_cliente = c.id_cliente
LEFT JOIN metodos_envio m ON p.id_envio = m.id_envio
LEFT JOIN direcciones_cliente dc ON p.id_direccion = dc.id_direccion
WHERE p.id_pedido = ?
";

$stmt = $conexion->prepare($sqlPedido);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit("Pedido no encontrado");
}

$pedido = $result->fetch_assoc();

/* ================================
   OBTENER PRODUCTOS
================================ */

$sqlDetalle = "
SELECT dp.*, pr.nombre
FROM detalle_pedido dp
INNER JOIN productos pr ON dp.id_producto = pr.id_producto
WHERE dp.id_pedido = ?
";

$stmt2 = $conexion->prepare($sqlDetalle);
$stmt2->bind_param("i", $id_pedido);
$stmt2->execute();
$resultDetalle = $stmt2->get_result();
?>

<h2 class="text-2xl font-bold mb-4">
Pedido #<?php echo $pedido['id_pedido']; ?>
</h2>

<div class="grid grid-cols-2 gap-6 mb-6 text-sm">

<div>
<p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['cliente']); ?></p>
<p><strong>Fecha:</strong> <?php echo date("d/m/Y", strtotime($pedido['fecha_pedido'])); ?></p>
<p><strong>Estado:</strong> <?php echo ucfirst($pedido['estado']); ?></p>
</div>

<div>
<p><strong>Método Envío:</strong> <?php echo htmlspecialchars($pedido['metodo_envio'] ?? 'No definido'); ?></p>
<p><strong>Costo Envío:</strong> L <?php echo number_format($pedido['costo_envio'] ?? 0,2); ?></p>
<p><strong>Impuesto:</strong> L <?php echo number_format($pedido['impuesto_total'],2); ?></p>
</div>

</div>

<!-- SECCIÓN DE DIRECCIÓN -->
<div class="bg-slate-100 dark:bg-slate-700 p-4 rounded-lg mb-6">
<h3 class="font-bold text-sm mb-3 uppercase">📍 Dirección de Envío</h3>
<div class="grid grid-cols-2 gap-4 text-sm">
    <div>
        <p class="text-slate-600 dark:text-slate-300">Referencia</p>
        <p class="font-semibold"><?php echo htmlspecialchars($pedido['referencia'] ?? 'No especificada'); ?></p>
    </div>
    <div>
        <p class="text-slate-600 dark:text-slate-300">Dirección</p>
        <p class="font-semibold"><?php echo htmlspecialchars($pedido['direccion'] ?? 'No especificada'); ?></p>
    </div>
    <div>
        <p class="text-slate-600 dark:text-slate-300">Ciudad</p>
        <p class="font-semibold"><?php echo htmlspecialchars($pedido['ciudad'] ?? 'No especificada'); ?></p>
    </div>
    <div>
        <p class="text-slate-600 dark:text-slate-300">Código Postal</p>
        <p class="font-semibold"><?php echo htmlspecialchars($pedido['codigo_postal'] ?? 'No especificado'); ?></p>
    </div>
    <div>
        <p class="text-slate-600 dark:text-slate-300">Teléfono</p>
        <p class="font-semibold"><?php echo htmlspecialchars($pedido['telefono'] ?? 'No especificado'); ?></p>
    </div>
</div>
</div>

<table class="w-full border text-sm">
<thead class="bg-gray-100">
<tr>
<th class="p-2 text-left">Producto</th>
<th class="p-2 text-center">Precio</th>
<th class="p-2 text-center">Cantidad</th>
<th class="p-2 text-center">Subtotal</th>
</tr>
</thead>
<tbody>

<?php while($item = $resultDetalle->fetch_assoc()): ?>

<tr class="border-t">
<td class="p-2"><?php echo htmlspecialchars($item['nombre']); ?></td>
<td class="p-2 text-center">
L <?php echo number_format($item['precio_unitario'],2); ?>
</td>
<td class="p-2 text-center">
<?php echo $item['cantidad']; ?>
</td>
<td class="p-2 text-center font-semibold">
L <?php echo number_format($item['precio_unitario'] * $item['cantidad'],2); ?>
</td>
</tr>

<?php endwhile; ?>

</tbody>
</table>

<div class="mt-6 text-right text-sm space-y-1">

<p>Subtotal: <strong>L <?php echo number_format($pedido['subtotal'],2); ?></strong></p>
<p>Impuesto: <strong>L <?php echo number_format($pedido['impuesto_total'],2); ?></strong></p>
<p>Envío: <strong>L <?php echo number_format($pedido['costo_envio'] ?? 0,2); ?></strong></p>

<hr class="my-2">

<p class="text-lg font-bold">
Total: L <?php echo number_format($pedido['total'],2); ?>
</p>

</div>

<?php
/* ================================
   COMPROBANTE DE TRANSFERENCIA
================================ */

$rutaComprobante = null;

if (!empty($pedido['comprobante_pago'])) {

    $rutaTemporal = "../img/comprobantes/" . $pedido['comprobante_pago'];

    if (file_exists($rutaTemporal)) {
        $rutaComprobante = $rutaTemporal;
    }
}

if ($rutaComprobante):
?>

<div class="mt-8 border-t pt-6">
<h3 class="text-lg font-bold mb-4">Comprobante de Transferencia</h3>

<div class="flex flex-col items-start gap-4">

<img 
src="<?php echo htmlspecialchars($rutaComprobante); ?>" 
class="w-full max-w-md rounded-lg shadow-md border cursor-pointer hover:scale-105 transition"
alt="Comprobante de pago">

<button 
onclick='abrirImagen(<?php echo json_encode($rutaComprobante); ?>)' 
class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-orange-700 mt-3">
    Ver en tamaño completo
</button>

</div>
</div>

<?php else: ?>

<div class="mt-8 border-t pt-6">
<p class="text-gray-500 italic">
Este pedido no tiene comprobante de transferencia.
</p>
</div>

<?php endif; ?>