<?php
require_once '../core/sesiones.php';

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    header("Location: ../index.php");
    exit();
}

/* ================================
   PAGINACIÓN
================================ */

$porPagina = 10;
$paginaActual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaActual - 1) * $porPagina;

/* ================================
   TOTAL DE PEDIDOS
================================ */

$sqlTotal = "SELECT COUNT(*) AS total FROM pedidos";
$resultTotal = $conexion->query($sqlTotal);
$totalPedidos = $resultTotal->fetch_assoc()['total'];
$totalPaginas = ceil($totalPedidos / $porPagina);

/* ================================
   LISTADO DE PEDIDOS
================================ */

$sql = "
SELECT 
    p.id_pedido,
    c.nombre AS cliente,
    p.fecha_pedido,
    p.subtotal,
    p.total,
    p.estado
FROM pedidos p
INNER JOIN clientes c ON p.id_cliente = c.id_cliente
ORDER BY p.fecha_pedido DESC
LIMIT ?, ?
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $offset, $porPagina);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Administración de Lista de Pedidos</title>

<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

<script>
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                primary: "#D9480F",
                "background-light": "#F8FAFC",
                "background-dark": "#0F172A",
            },
            fontFamily: {
                display: ["Inter", "sans-serif"],
            },
            borderRadius: {
                DEFAULT: "0.5rem",
            },
        },
    },
};
</script>

<style>
body { font-family: 'Inter', sans-serif; }
.table-container {
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1),
                0 2px 4px -2px rgb(0 0 0 / 0.1);
}
</style>
</head>

<body class="bg-slate-100 dark:bg-slate-900">

<main class="max-w-7xl mx-auto px-6 pb-12">

<header class="mb-8 flex justify-between items-end">
    <h2 class="text-3xl font-bold">Administración de Lista de Pedidos</h2>
</header>

<div class="bg-white dark:bg-slate-800 rounded-xl overflow-hidden table-container border border-slate-200 dark:border-slate-700">

<div class="bg-primary px-6 py-4">
    <h3 class="text-white font-bold text-lg">Lista de Pedidos</h3>
</div>

<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
<th class="px-6 py-4 text-xs font-bold uppercase">ID Pedido</th>
<th class="px-6 py-4 text-xs font-bold uppercase">Cliente</th>
<th class="px-6 py-4 text-xs font-bold uppercase">Fecha</th>
<th class="px-6 py-4 text-xs font-bold uppercase">Subtotal</th>
<th class="px-6 py-4 text-xs font-bold uppercase">Total</th>
<th class="px-6 py-4 text-xs font-bold uppercase">Estado</th>
<th class="px-6 py-4 text-xs font-bold uppercase text-center">Acciones</th>
</tr>
</thead>

<tbody class="divide-y divide-slate-200 dark:divide-slate-700">

<?php if ($resultado->num_rows > 0): ?>

<?php while ($pedido = $resultado->fetch_assoc()): ?>

<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">

<td class="px-6 py-5 font-semibold">
    #<?php echo $pedido['id_pedido']; ?>
</td>

<td class="px-6 py-5">
    <?php echo htmlspecialchars($pedido['cliente']); ?>
</td>

<td class="px-6 py-5">
    <?php echo date("d-m-Y", strtotime($pedido['fecha_pedido'])); ?>
</td>

<td class="px-6 py-5">
    L <?php echo number_format($pedido['subtotal'], 2); ?>
</td>

<td class="px-6 py-5 font-bold text-green-600">
    L <?php echo number_format($pedido['total'], 2); ?>
</td>

<td class="px-6 py-5">
<?php
$estado = $pedido['estado'];

$colores = [
    'pendiente' => 'bg-blue-100 text-blue-700',
    'confirmado' => 'bg-emerald-100 text-emerald-700',
    'enviado' => 'bg-purple-100 text-purple-700',
    'entregado' => 'bg-green-100 text-green-700',
    'cancelado' => 'bg-rose-100 text-rose-700'
];

$claseEstado = $colores[$estado] ?? 'bg-gray-100 text-gray-700';
?>

<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold <?php echo $claseEstado; ?>">
<?php echo ucfirst($estado); ?>
</span>
</td>

<td class="px-6 py-5">
<div class="flex justify-center gap-2">

<button class="btn-ver-detalle w-8 h-8 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center"
data-id="<?php echo $pedido['id_pedido']; ?>">
<span class="material-icons-outlined text-sm">visibility</span>
</button>

<button class="btn-cambiar-estado w-8 h-8 rounded-full bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center"
data-id="<?php echo $pedido['id_pedido']; ?>">
<span class="material-icons-outlined text-sm">swap_horiz</span>
</button>

<button class="btn-cancelar w-8 h-8 rounded-full bg-rose-500 hover:bg-rose-600 text-white flex items-center justify-center"
data-id="<?php echo $pedido['id_pedido']; ?>">
<span class="material-icons-outlined text-sm">close</span>
</button>

</div>
</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="7" class="text-center py-10 text-gray-500">
No hay pedidos registrados.
</td>
</tr>

<?php endif; ?>

</tbody>
</table>
</div>

<?php
$mostrando = min($porPagina, $totalPedidos - $offset);
?>

<div class="px-6 py-4 flex justify-between items-center bg-slate-50 dark:bg-slate-800/80">

<p class="text-sm text-slate-500">
Mostrando <?php echo $mostrando; ?> de <?php echo $totalPedidos; ?> pedidos
</p>

<div class="flex gap-2">

<?php if ($paginaActual > 1): ?>
<a href="?pagina=<?php echo $paginaActual - 1; ?>"
class="px-3 py-1 border rounded bg-white hover:bg-slate-50">Anterior</a>
<?php endif; ?>

<?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
<a href="?pagina=<?php echo $i; ?>"
class="px-3 py-1 rounded <?php echo $i == $paginaActual ? 'bg-primary text-white' : 'border bg-white'; ?>">
<?php echo $i; ?>
</a>
<?php endfor; ?>

<?php if ($paginaActual < $totalPaginas): ?>
<a href="?pagina=<?php echo $paginaActual + 1; ?>"
class="px-3 py-1 border rounded bg-white hover:bg-slate-50">Siguiente</a>
<?php endif; ?>

</div>
</div>

</div>

</main>
<!-- MODAL DETALLE PEDIDO -->
<div id="modalDetalle" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 w-full max-w-4xl rounded-xl p-6 relative max-h-[90vh] overflow-y-auto">

        <button onclick="cerrarModal()" 
        class="absolute top-3 right-3 text-gray-500 hover:text-black text-xl">
            ✕
        </button>

        <div id="contenidoDetalle">
            <!-- Aquí se carga el detalle -->
        </div>

    </div>
</div>
<script>
document.addEventListener("click", function(e){

    if(e.target.closest(".btn-ver-detalle")){
        const btn = e.target.closest(".btn-ver-detalle");
        const id = btn.dataset.id;

        fetch("admin_obtener_detalle.php?id=" + id)
        .then(res => res.text())
        .then(data => {
            document.getElementById("contenidoDetalle").innerHTML = data;
            document.getElementById("modalDetalle").classList.remove("hidden");
            document.getElementById("modalDetalle").classList.add("flex");
        });
    }

});

function cerrarModal(){
    document.getElementById("modalDetalle").classList.add("hidden");
    document.getElementById("modalDetalle").classList.remove("flex");
}
</script>
<script>
let pedidoActual = null;

document.addEventListener("click", function(e){

    if(e.target.closest(".btn-cambiar-estado")){
        const btn = e.target.closest(".btn-cambiar-estado");
        pedidoActual = btn.dataset.id;

        document.getElementById("modalEstado").classList.remove("hidden");
        document.getElementById("modalEstado").classList.add("flex");
    }

});

function cerrarModalEstado(){
    document.getElementById("modalEstado").classList.add("hidden");
    document.getElementById("modalEstado").classList.remove("flex");
}

function guardarCambioEstado(){

    const estado = document.getElementById("nuevoEstado").value;

    fetch("cambiar_estado.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "id=" + pedidoActual + "&estado=" + estado
    })
    .then(res => res.json())
    .then(data => {

        if(data.exito){

            const fila = document.querySelector(`button[data-id="${pedidoActual}"]`).closest("tr");
            const badge = fila.querySelector("span");

            const colores = {
                pendiente: "bg-blue-100 text-blue-700",
                confirmado: "bg-emerald-100 text-emerald-700",
                enviado: "bg-purple-100 text-purple-700",
                entregado: "bg-green-100 text-green-700",
            };

            badge.textContent = estado.charAt(0).toUpperCase() + estado.slice(1);
            badge.className = "inline-flex px-3 py-1 rounded-full text-xs font-bold " + colores[estado];

            cerrarModalEstado();

        } else {
            alert("Error: " + (data.error ?? "No se pudo actualizar"));
        }

    })
    .catch(err => {
        console.error(err);
        alert("Error de conexión");
    });
}
</script>
<!-- MODAL CAMBIAR ESTADO -->
<div id="modalEstado" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 w-full max-w-md relative">

        <button onclick="cerrarModalEstado()" 
        class="absolute top-3 right-3 text-gray-500 hover:text-black">
            ✕
        </button>

        <h3 class="text-lg font-bold mb-4">Cambiar Estado del Pedido</h3>

        <select id="nuevoEstado" class="w-full border rounded-lg p-2 mb-4">
            <option value="pendiente">Pendiente</option>
            <option value="confirmado">Confirmado</option>
            <option value="enviado">Enviado</option>
            <option value="entregado">Entregado</option>
        </select>

        <button onclick="guardarCambioEstado()" 
        class="w-full bg-primary text-white py-2 rounded-lg hover:bg-orange-700">
            Guardar Cambios
        </button>

    </div>
</div>
</body>
</html>