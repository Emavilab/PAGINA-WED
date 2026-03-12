<?php
require_once '../core/sesiones.php';

/* ======================================================
   VERIFICAR AUTENTICACIÓN DEL USUARIO
   - Se asegura que el usuario esté autenticado
   - Solo roles 1 (admin) y 2 (gestor) pueden acceder
====================================================== */
if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    exit("No autorizado");
}

/* ======================================================
   VALIDAR ID DEL PEDIDO
   - Se recibe el ID por GET
   - Se valida que sea numérico
====================================================== */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("Pedido inválido");
}
$id_pedido = intval($_GET['id']);

/* ======================================================
   OBTENER INFORMACIÓN DEL PEDIDO
   - Se consulta el pedido con datos del cliente
   - Se incluyen método de envío y dirección
====================================================== */
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

/* ======================================================
   OBTENER PRODUCTOS DEL PEDIDO
   - Se consultan los productos asociados al pedido
   - Se unen con la tabla productos para obtener nombre
====================================================== */
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

<!-- ======================================================
     ENCABEZADO DEL PEDIDO
====================================================== -->
<h2 class="text-2xl font-bold mb-4">
    Pedido #<?php echo $pedido['id_pedido']; ?>
</h2>

<!-- ======================================================
     TABLA DE PRODUCTOS DEL PEDIDO
====================================================== -->
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
<?php
    /* ======================================================
       OBTENER DATOS DEL PRODUCTO DEL PEDIDO
       - Se obtiene el ID y cantidad
    ====================================================== */
    $producto_id = $item['id_producto'];
    $cantidad    = $item['cantidad'];

    /* ======================================================
       CONSULTAR PRECIO DEL PRODUCTO
       - Se busca el precio actual en la tabla productos
    ====================================================== */
    $sqlPrecio = "SELECT precio FROM productos WHERE id_producto = ?";
    $stmtPrecio = $conexion->prepare($sqlPrecio);
    $stmtPrecio->bind_param("i", $producto_id);
    $stmtPrecio->execute();
    $resultPrecio = $stmtPrecio->get_result();
    $producto = $resultPrecio->fetch_assoc();
    $precio   = $producto['precio'];

    /* ======================================================
       GUARDAR DETALLE DE COMPRA EN INVENTARIO
       - Se inserta en detalle_compra para registrar
         la compra en el inventario
    ====================================================== */
    $sqlInsert = "
    INSERT INTO detalle_compra (compra_id, producto_id, cantidad, precio)
    VALUES (?,?,?,?)
    ";
    $stmtInsert = $conexion->prepare($sqlInsert);
    $stmtInsert->bind_param("iiid", $id_pedido, $producto_id, $cantidad, $precio);
    $stmtInsert->execute();
?>
<tr class="border-t">
    <td class="p-2"><?php echo htmlspecialchars($item['nombre']); ?></td>
    <td class="p-2 text-center">L <?php echo number_format($precio,2); ?></td>
    <td class="p-2 text-center"><?php echo $cantidad; ?></td>
    <td class="p-2 text-center font-semibold">L <?php echo number_format($precio * $cantidad,2); ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

<!-- ======================================================
     TOTALES DEL PEDIDO
====================================================== -->
<div class="mt-6 text-right text-sm space-y-1">
    <p>Subtotal: <strong>L <?php echo number_format($pedido['subtotal'],2); ?></strong></p>
    <p>Impuesto: <strong>L <?php echo number_format($pedido['impuesto_total'],2); ?></strong></p>
    <p>Envío (Departamento): <strong>L <?php echo number_format($pedido['envio_departamento'] ?? 0,2); ?></strong></p>
    <p>Envío (<?php echo htmlspecialchars($pedido['metodo_envio'] ?? 'Método'); ?>): 
       <strong>L <?php echo number_format($pedido['costo_envio'] ?? 0,2); ?></strong></p>
    <hr class="my-2">
    <p class="text-lg font-bold">Total: L <?php echo number_format($pedido['total'],2); ?></p>
</div>
