<?php
require_once '../core/sesiones.php';
require_once '../core/conexion.php';

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    http_response_code(401);
    exit();
}

// Cargar configuración de moneda
$res_cfg = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");
$cfg = ($res_cfg && mysqli_num_rows($res_cfg) > 0) ? mysqli_fetch_assoc($res_cfg) : [];
$cfg_moneda_cod = $cfg['moneda'] ?? 'HNL';
$simbolos_moneda = ['USD' => '$', 'EUR' => '€', 'MXN' => '$', 'COP' => '$', 'ARS' => '$', 'GTQ' => 'Q', 'HNL' => 'L', 'CRC' => '₡'];
$cfg_moneda = $simbolos_moneda[$cfg_moneda_cod] ?? $cfg_moneda_cod;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero_pedido'])) {
    
    $numeroPedido = isset($_POST['numero_pedido']) ? trim($_POST['numero_pedido']) : '';
    $estadoFiltro = isset($_POST['estado_filtro']) && $_POST['estado_filtro'] !== '' ? $_POST['estado_filtro'] : null;
    $pagina = isset($_POST['pagina']) && is_numeric($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
    $metodoPagoFiltro = isset($_POST['metodo_pago_filtro']) && $_POST['metodo_pago_filtro'] !== '' ? $_POST['metodo_pago_filtro'] : null;
    $metodoEnvioFiltro = isset($_POST['metodo_envio_filtro']) && $_POST['metodo_envio_filtro'] !== '' ? $_POST['metodo_envio_filtro'] : null;

    $porPagina = 10;
    $offset = ($pagina - 1) * $porPagina;

    $whereClauses = [];
    $params = [];

    if (!empty($numeroPedido)) {
        $whereClauses[] = "p.id_pedido LIKE ?";
        $params[] = "%".$numeroPedido."%";
    }

    if ($estadoFiltro !== null) {
        $whereClauses[] = "p.estado = ?";
        $params[] = $estadoFiltro;
    }
    
    if ($metodoPagoFiltro !== null) {
    $whereClauses[] = "p.id_metodo_pago = ?";
    $params[] = $metodoPagoFiltro;
    }

    if ($metodoEnvioFiltro !== null) {
        if ($metodoEnvioFiltro === 'con_envio') {
            $whereClauses[] = "p.id_envio IS NOT NULL";
        } elseif ($metodoEnvioFiltro === 'sin_envio') {
            $whereClauses[] = "p.id_envio IS NULL";
        } else {
            $whereClauses[] = "p.id_envio = ?";
            $params[] = $metodoEnvioFiltro;
        }
    }

    $whereSQL = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

    $sqlTotal = "SELECT COUNT(*) AS total FROM pedidos p " . $whereSQL;
    $stmtTotal = $conexion->prepare($sqlTotal);

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmtTotal->bind_param($types, ...$params);
    }

    $stmtTotal->execute();
    $totalPedidos = $stmtTotal->get_result()->fetch_assoc()['total'];
    $totalPaginas = ceil($totalPedidos / $porPagina) ?: 1;

    $orderBy = "ORDER BY ";
    if ($metodoEnvioFiltro === 'con_envio') {
        $orderBy .= "me.nombre ASC, p.fecha_pedido DESC";
    } else {
        $orderBy .= "p.fecha_pedido DESC";
    }

    $sql = "
  SELECT 
    p.id_pedido,
    c.nombre AS cliente,
    p.fecha_pedido,
    p.subtotal,
    p.total,
    p.estado,
    mp.nombre AS metodo_pago,
    me.nombre AS metodo_envio
FROM pedidos p
INNER JOIN clientes c ON p.id_cliente = c.id_cliente
LEFT JOIN metodos_pago mp ON p.id_metodo_pago = mp.id_metodo_pago
LEFT JOIN metodos_envio me ON p.id_envio = me.id_envio
    " . $whereSQL . "
    " . $orderBy . "
    LIMIT ?, ?
    ";

    $stmt = $conexion->prepare($sql);

    if (!empty($params)) {
        $paramsConLimits = array_merge($params, [$offset, $porPagina]);
        $types = str_repeat('s', count($params)) . 'ii';
        $stmt->bind_param($types, ...$paramsConLimits);
    } else {
        $stmt->bind_param("ii", $offset, $porPagina);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();
    ?>

<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
<th class="px-6 py-4 text-xs font-bold uppercase">ID Pedido</th>
<th class="px-6 py-4 text-xs font-bold uppercase">Cliente</th>
<th class="px-6 py-4 text-xs font-bold uppercase">Fecha</th>
<th class="px-6 py-4 text-xs font-bold uppercase">Subtotal</th>
<th class="px-6 py-4 text-xs font-bold uppercase">Total</th>
<th class="px-6 py-4 text-xs font-bold uppercase">Método Pago</th>
<th class="px-6 py-4 text-xs font-bold uppercase">Método Envío</th>
<th class="px-6 py-4 text-xs font-bold uppercase">Estado</th>
<th class="px-6 py-4 text-xs font-bold uppercase text-center">Acciones</th>
</tr>
</thead>

<tbody class="divide-y divide-slate-200 dark:divide-slate-700">

<?php if ($resultado->num_rows > 0): ?>

<?php while ($pedido = $resultado->fetch_assoc()): ?>

<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">

<td class="px-6 py-5 font-semibold">
    <?php echo $pedido['id_pedido']; ?>
</td>

<td class="px-6 py-5">
    <?php echo htmlspecialchars($pedido['cliente']); ?>
</td>

<td class="px-6 py-5">
    <?php echo date('d/m/Y', strtotime($pedido['fecha_pedido'])); ?>
</td>

<td class="px-6 py-5">
    <?php echo $cfg_moneda; ?> <?php echo number_format($pedido['subtotal'], 2); ?>
</td>

<td class="px-6 py-5 font-semibold">
    <?php echo $cfg_moneda; ?> <?php echo number_format($pedido['total'], 2); ?>
</td>

<td class="px-6 py-5">
    <?php echo htmlspecialchars($pedido['metodo_pago'] ?? 'No definido'); ?>
</td>

<td class="px-6 py-5">
    <?php if (!empty($pedido['metodo_envio'])): ?>
    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-cyan-100 text-cyan-700">
        <i class="fas fa-truck mr-1"></i> <?php echo htmlspecialchars($pedido['metodo_envio']); ?>
    </span>
    <?php else: ?>
    <span class="text-slate-400 text-sm">Estándar</span>
    <?php endif; ?>
</td>

<td class="px-6 py-5">
    <?php 
        $colores = [
            'pendiente' => 'bg-blue-100 text-blue-700',
            'confirmado' => 'bg-emerald-100 text-emerald-700',
            'enviado' => 'bg-purple-100 text-purple-700',
            'entregado' => 'bg-green-100 text-green-700',
            'cancelado' => 'bg-red-100 text-red-700'
        ];
        $color = $colores[$pedido['estado']] ?? 'bg-gray-100 text-gray-700';
    ?>
    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold <?php echo $color; ?>">
        <?php echo ucfirst($pedido['estado']); ?>
    </span>
</td>

<td class="px-6 py-5 text-center">
<div class="flex justify-center gap-2">

<button class="btn-ver-detalle w-8 h-8 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center"
data-id="<?php echo $pedido['id_pedido']; ?>">
<span class="material-icons-outlined text-sm">visibility</span>
</button>

<?php if($pedido['estado'] != 'cancelado'): ?>

<button class="btn-cambiar-estado w-8 h-8 rounded-full bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center"
data-id="<?php echo $pedido['id_pedido']; ?>">
<span class="material-icons-outlined text-sm">swap_horiz</span>
</button>

<?php endif; ?>

</div>
</td>
</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="9" class="px-6 py-20 text-center text-gray-500">
    No se encontraron pedidos
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

<div class="flex items-center justify-between mt-6">

<p class="text-sm text-gray-600">Mostrando página <strong><?php echo $pagina; ?></strong> de <strong><?php echo $totalPaginas; ?></strong></p>

<div class="flex gap-2">
    <?php if ($pagina > 1): ?>
    <button onclick="cargarPagina(<?php echo $pagina - 1; ?>)" class="px-3 py-1 border rounded bg-white hover:bg-slate-50">Anterior</button>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
    <button onclick="cargarPagina(<?php echo $i; ?>)" class="px-3 py-1 rounded <?php echo $i == $pagina ? 'bg-primary text-white' : 'border bg-white'; ?>">
        <?php echo $i; ?>
    </button>
    <?php endfor; ?>

    <?php if ($pagina < $totalPaginas): ?>
    <button onclick="cargarPagina(<?php echo $pagina + 1; ?>)" class="px-3 py-1 border rounded bg-white hover:bg-slate-50">Siguiente</button>
    <?php endif; ?>
</div>

</div>

<?php
    exit();
}
?>
