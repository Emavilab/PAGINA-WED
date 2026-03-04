<?php
require_once '../core/sesiones.php';

if (!usuarioAutenticado()) {
    exit("No autorizado");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("Pedido inválido");
}

$id_pedido = intval($_GET['id']);
$usuario = obtenerDatosUsuario();
$id_cliente = $usuario['id_cliente'] ?? null;

if (!$id_cliente) {
    exit("Debes iniciar sesión para ver tus pedidos");
}

/* ================================
   OBTENER PEDIDO + MÉTODO ENVÍO
================================ */

$sqlPedido = "
SELECT 
    p.id_pedido,
    p.fecha_pedido,
    p.estado,
    p.subtotal,
    p.impuesto_total,
    p.total,
    m.nombre AS nombre_envio,
    m.costo AS costo_envio
FROM pedidos p
LEFT JOIN metodos_envio m ON p.id_envio = m.id_envio
WHERE p.id_pedido = ? AND p.id_cliente = ?
";

$stmt = $conexion->prepare($sqlPedido);
$stmt->bind_param("ii", $id_pedido, $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit("No autorizado");
}

$pedido = $result->fetch_assoc();

/* ================================
   OBTENER PRODUCTOS DEL PEDIDO
================================ */

$sqlDetalle = "
SELECT dp.cantidad, p.nombre, p.precio
FROM detalle_pedido dp
INNER JOIN productos p ON dp.id_producto = p.id_producto
WHERE dp.id_pedido = ?
";

$stmt2 = $conexion->prepare($sqlDetalle);
$stmt2->bind_param("i", $id_pedido);
$stmt2->execute();
$resultDetalle = $stmt2->get_result();

/* Valores */
$subtotal = $pedido['subtotal'];
$impuesto = $pedido['impuesto_total'];
$envio = $pedido['costo_envio'] ?? 0;
$total = $pedido['total'];
?>

<h2 class="text-2xl font-bold mb-4">
    Pedido #<?php echo $pedido['id_pedido']; ?>
</h2>

<p class="mb-1">
    <strong>Fecha:</strong> 
    <?php echo date("d/m/Y", strtotime($pedido['fecha_pedido'])); ?>
</p>

<p class="mb-4">
    <strong>Estado:</strong> 
    <?php echo strtoupper($pedido['estado']); ?>
</p>

<table class="w-full border rounded-lg overflow-hidden">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 text-left">Producto</th>
            <th class="p-2 text-center">Precio</th>
            <th class="p-2 text-center">Cantidad</th>
            <th class="p-2 text-center">Subtotal</th>
        </tr>
    </thead>
    <tbody>

    <?php while ($item = $resultDetalle->fetch_assoc()): ?>
        <tr class="border-t">
            <td class="p-2"><?php echo $item['nombre']; ?></td>
            <td class="p-2 text-center">
                L <?php echo number_format($item['precio'], 2); ?>
            </td>
            <td class="p-2 text-center">
                <?php echo $item['cantidad']; ?>
            </td>
            <td class="p-2 text-center font-semibold">
                L <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>
            </td>
        </tr>
    <?php endwhile; ?>

    </tbody>
</table>

<hr class="my-4">

<div class="space-y-2 text-right">

    <div class="flex justify-between">
        <span>Subtotal:</span>
        <span>L <?php echo number_format($subtotal, 2); ?></span>
    </div>

    <div class="flex justify-between">
        <span>Impuesto:</span>
        <span>L <?php echo number_format($impuesto, 2); ?></span>
    </div>

    <div class="flex justify-between">
        <span>Envío (<?php echo $pedido['nombre_envio']; ?>):</span>
        <span>L <?php echo number_format($envio, 2); ?></span>
    </div>

    <div class="flex justify-between font-bold text-lg border-t pt-2">
        <span>Total:</span>
        <span>L <?php echo number_format($total, 2); ?></span>
    </div>

</div>