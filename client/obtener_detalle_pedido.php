<?php 
// =====================================================
// VERIFICACIÓN DE SESIÓN Y AUTENTICACIÓN
// =====================================================

// Incluir el archivo de sesiones que contiene las funciones de autenticación
require_once '../core/sesiones.php';

// Verificar si el usuario está autenticado
if (!usuarioAutenticado()) {
    exit("No autorizado"); // Salir si no está autenticado
}

// Verificar que se haya recibido un ID de pedido válido en la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("Pedido inválido"); // Salir si el ID no es numérico o no existe
}

// Obtener el ID del pedido y asegurarse que sea un entero
$id_pedido = intval($_GET['id']);

// Obtener los datos del usuario logueado
$usuario = obtenerDatosUsuario();

// Obtener el ID del cliente desde los datos del usuario
$id_cliente = $usuario['id_cliente'] ?? null;

// Validar que el usuario tenga un ID de cliente
if (!$id_cliente) {
    exit("Debes iniciar sesión para ver tus pedidos");
}

/* =====================================================
   OBTENER DETALLES DEL PEDIDO Y MÉTODO DE ENVÍO
===================================================== */

// Consulta SQL para obtener el pedido y su método de envío
$sqlPedido = "
    SELECT 
        p.id_pedido,
        p.fecha_pedido,
        p.estado,
        p.subtotal,
        p.impuesto_total,
        p.envio_departamento,
        p.total,
        m.nombre AS nombre_envio,
        m.costo AS costo_envio
    FROM pedidos p
    LEFT JOIN metodos_envio m ON p.id_envio = m.id_envio
    WHERE p.id_pedido = ? AND p.id_cliente = ?
";

// Preparar la consulta para prevenir inyecciones SQL
$stmt = $conexion->prepare($sqlPedido);
$stmt->bind_param("ii", $id_pedido, $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

// Validar que se haya encontrado el pedido
if ($result->num_rows === 0) {
    exit("No autorizado"); // Salir si el pedido no pertenece al cliente
}

// Guardar los datos del pedido en un arreglo asociativo
$pedido = $result->fetch_assoc();

// Determinar si el cliente puede cancelar el pedido
// Condiciones: estado "pendiente" y menos de 3 horas desde la creación
$puedeCancelar = (
    $pedido['estado'] === 'pendiente' && 
    (time() - strtotime($pedido['fecha_pedido']) <= 10800) // 3 horas en segundos
);

/* =====================================================
   OBTENER PRODUCTOS DEL PEDIDO
===================================================== */

// Consulta SQL para obtener los productos del pedido
$sqlDetalle = "
    SELECT 
        dp.cantidad, 
        dp.precio_unitario, 
        dp.subtotal, 
        p.nombre
    FROM detalle_pedido dp
    INNER JOIN productos p ON dp.id_producto = p.id_producto
    WHERE dp.id_pedido = ?
";

// Preparar la consulta de detalle de pedido
$stmt2 = $conexion->prepare($sqlDetalle);
$stmt2->bind_param("i", $id_pedido);
$stmt2->execute();
$resultDetalle = $stmt2->get_result();

/* =====================================================
   VALORES DEL PEDIDO
===================================================== */

$subtotal = $pedido['subtotal'];
$impuesto = $pedido['impuesto_total'];
$envio_departamento = $pedido['envio_departamento'] ?? 0;
$envio_metodo = $pedido['costo_envio'] ?? 0;
$total = $pedido['total'];

// Cargar moneda desde configuración del sistema
$res_cfg = mysqli_query($conexion, "SELECT moneda FROM configuracion WHERE id_config = 1");
$config = mysqli_fetch_assoc($res_cfg);
$moneda = $config['moneda'] ?? 'L';
?>

<!-- =====================================================
     MOSTRAR DETALLES DEL PEDIDO EN HTML
===================================================== -->

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

<!-- Tabla de productos del pedido -->
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
                <?php echo $moneda; ?> <?php echo number_format($item['precio_unitario'], 2); ?>
            </td>
            <td class="p-2 text-center">
                <?php echo $item['cantidad']; ?>
            </td>
            <td class="p-2 text-center font-semibold">
                <?php echo $moneda; ?> <?php echo number_format($item['subtotal'], 2); ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<hr class="my-4">

<!-- Resumen de costos -->
<div class="space-y-2 text-right">
    <div class="flex justify-between">
        <span>Subtotal:</span>
        <span><?php echo $moneda; ?> <?php echo number_format($subtotal, 2); ?></span>
    </div>

    <div class="flex justify-between">
        <span>Impuesto:</span>
        <span><?php echo $moneda; ?> <?php echo number_format($impuesto, 2); ?></span>
    </div>

    <div class="flex justify-between">
        <span>Envío (Departamento):</span>
        <span><?php echo $moneda; ?> <?php echo number_format($envio_departamento, 2); ?></span>
    </div>

    <div class="flex justify-between">
        <span>Envío (<?php echo $pedido['nombre_envio']; ?>):</span>
        <span><?php echo $moneda; ?> <?php echo number_format($envio_metodo, 2); ?></span>
    </div>

    <div class="flex justify-between font-bold text-lg border-t pt-2">
        <span>Total:</span>
        <span><?php echo $moneda; ?> <?php echo number_format($total, 2); ?></span>
    </div>
</div>

<!-- Botón para cancelar el pedido si cumple condiciones -->
<?php if ($puedeCancelar): ?>
<div class="mt-6 pt-4 border-t border-slate-200">
    <button type="button" class="btn-cancelar-pedido w-full py-3 px-4 rounded-lg font-bold text-white bg-red-500 hover:bg-red-600 transition-colors" data-id="<?php echo (int)$pedido['id_pedido']; ?>">
        Cancelar Pedido
    </button>
</div>
<?php endif; ?> 