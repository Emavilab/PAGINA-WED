<?php
date_default_timezone_set('America/Tegucigalpa');
require_once '../core/sesiones.php';

if (!usuarioAutenticado()) {
    echo "<script>window.location='?modulo=login';</script>";
    exit();
}

$usuario = obtenerDatosUsuario();
$id_cliente = $usuario['id_cliente'] ?? null;

if (!$id_cliente) {
    echo "<script>window.location='?modulo=login';</script>";
    exit();
}

// --- PAGINACIÓN ---
$por_pagina = 10;
$pagina_actual = (isset($_GET['page']) && (int)$_GET['page'] > 0) ? (int)$_GET['page'] : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Obtener total de pedidos del cliente para paginación
$sql_total = "SELECT COUNT(*) AS total FROM pedidos WHERE id_cliente = ?";
$stmt_total = $conexion->prepare($sql_total);
$stmt_total->bind_param("i", $id_cliente);
$stmt_total->execute();
$res_total = $stmt_total->get_result();
$total_pedidos = 0;
if ($row_total = $res_total->fetch_assoc()) {
    $total_pedidos = (int)$row_total['total'];
}
$total_paginas = max(1, ceil($total_pedidos / $por_pagina));

// Obtener pedidos paginados del cliente
$sql = "SELECT id_pedido, fecha_pedido, estado, total 
        FROM pedidos 
        WHERE id_cliente = ? 
        ORDER BY fecha_pedido DESC
        LIMIT ? OFFSET ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("iii", $id_cliente, $por_pagina, $offset);
$stmt->execute();
$resultado = $stmt->get_result();

// Cargar configuración general de colores
$res_cfg_of = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");
$cfg_of = ($res_cfg_of && mysqli_num_rows($res_cfg_of) > 0) ? mysqli_fetch_assoc($res_cfg_of) : [];

function normalizar_color_ofertas($valor, $defecto) {
    if (!is_string($valor)) return $defecto;
    $valor = trim($valor);
    if ($valor === '') return $defecto;
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $valor)) return $defecto;
    return strtoupper($valor);
}

$of_primary = normalizar_color_ofertas($cfg_of['color_primary'] ?? '#137fec', '#137FEC');
$of_bg_light = normalizar_color_ofertas($cfg_of['color_background_light'] ?? '#f6f7f8', '#F6F7F8');
$of_bg_dark = normalizar_color_ofertas($cfg_of['color_background_dark'] ?? '#101922', '#101922');
?>


<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Mis Pedidos - RetailCMS</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": $of_primary,
                        "background-light": $of_bg_light,
                        "background-dark": $of_bg_dark,
                    },
                    fontFamily: {
                        "display": ["Inter"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
<style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 48;
        }
        .status-badge-entregado { background-color: #d1fae5; color: #047857; }
        .dark .status-badge-entregado { background-color: rgba(5, 150, 105, 0.3); color: #6ee7b7; }
        .status-badge-camino { background-color: #dbeafe; color: #1e40af; }
        .dark .status-badge-camino { background-color: rgba(30, 58, 138, 0.3); color: #60a5fa; }
        .status-badge-procesando { background-color: #fef3c7; color: #b45309; }
        .dark .status-badge-procesando { background-color: rgba(180, 83, 9, 0.3); color: #fbbf24; }
        .status-badge-cancelado { background-color: #fee2e2; color: #b91c1c; }
        .dark .status-badge-cancelado { background-color: rgba(185, 28, 28, 0.3); color: #fca5a5; }
    </style>
<main class="flex-grow max-w-7xl mx-auto px-4 py-8 md:py-12 w-full">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
<div>
<h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Historial de Mis Pedidos</h1>
<p class="text-slate-500 dark:text-slate-400 mt-1">Revisa el estado de tus compras y descarga tus facturas.</p>
</div>
<div class="flex items-center gap-2 text-sm text-slate-500 bg-white dark:bg-slate-800 px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700">
<span class="material-icons text-base">filter_list</span>
<span>Últimos 6 meses</span>
<span class="material-icons text-base">expand_more</span>
</div>
</div>
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
<th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">ID del Pedido</th>
<th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Fecha</th>
<th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Estado</th>
<th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Total</th>
<th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 text-right">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-slate-800">

<?php if ($resultado->num_rows > 0): ?>
    <?php while ($pedido = $resultado->fetch_assoc()): ?>

    <?php
        // Formatear fecha
        $fecha = date("d \\d\\e F, Y", strtotime($pedido['fecha_pedido']));

        // Puede cancelar solo si está pendiente y han pasado menos de 3 horas 
        $fechaPedido = new DateTime($pedido['fecha_pedido']);

        $fechaLimite = clone $fechaPedido;
        $fechaLimite->modify('+3 hours');

        $ahora = new DateTime();
        $deadlineTs = $fechaLimite->getTimestamp();

        $puedeCancelar = ($pedido['estado'] === 'pendiente' && $ahora <= $fechaLimite);
        $restanteInicial = max(0, $fechaLimite->getTimestamp() - $ahora->getTimestamp());
        $esPendiente = ($pedido['estado'] === 'pendiente');
        $restanteInicial = $esPendiente ? max(0, $deadlineTs - time()) : 0;

        // Clases según estado
        switch ($pedido['estado']) {
            case 'entregado':
                $claseEstado = 'status-badge-entregado';
                $textoEstado = 'Entregado';
                break;
            case 'enviado':
                $claseEstado = 'status-badge-camino';
                $textoEstado = 'En Camino';
                break;
            case 'confirmado':
            case 'pendiente':
                $claseEstado = 'status-badge-procesando';
                $textoEstado = ucfirst($pedido['estado']);
                break;
            case 'cancelado':
                $claseEstado = 'status-badge-cancelado';
                $textoEstado = 'Cancelado';
                break;
            default:
                $claseEstado = 'status-badge-procesando';
                $textoEstado = ucfirst($pedido['estado']);
        }
    ?>

    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors" <?php if ($esPendiente): ?>data-deadline="<?php echo $deadlineTs; ?>"<?php endif; ?> data-id="<?php echo $pedido['id_pedido']; ?>">

        <td class="px-6 py-5 whitespace-nowrap">
            <span class="font-bold text-slate-900 dark:text-white">
                #<?php echo $pedido['id_pedido']; ?>
            </span>
        </td>

        <td class="px-6 py-5 whitespace-nowrap text-slate-600 dark:text-slate-400 text-sm">
            <?php echo $fecha; ?>
        </td>

        <td class="px-6 py-5">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?php echo $claseEstado; ?> estado-label">
                <?php echo $textoEstado; ?>
            </span>
            <?php if ($esPendiente): ?>
            <div class="countdown-cell mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                <?php if ($puedeCancelar): ?>
                <span class="countdown-wrapper">Tiempo restante para cancelar: <strong class="countdown-display"><?php echo gmdate('H:i:s', $restanteInicial); ?></strong></span>
                <span class="expirado-message hidden">Tiempo de cancelación expirado</span>
                <?php else: ?>
                <span class="expirado-message">Tiempo de cancelación expirado</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </td>

        <td class="px-6 py-5 whitespace-nowrap font-semibold">
            L <?php echo number_format($pedido['total'], 2); ?>
        </td>

        <td class="px-6 py-5 whitespace-nowrap text-right">
            <div class="flex flex-wrap justify-end gap-2">
                <button 
                    class="btn-ver-detalle inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-primary hover:text-white dark:bg-slate-800 dark:hover:bg-primary text-slate-700 dark:text-slate-300 rounded-lg text-sm font-bold transition-all"
                    data-id="<?php echo $pedido['id_pedido']; ?>">
                    Ver Detalles
                    <span class="material-icons text-sm">visibility</span>
                </button>
                <?php if ($puedeCancelar): ?>
                <button
                    type="button"
                    class="btn-cancelar-pedido inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-500 dark:bg-red-900/30 dark:hover:bg-red-500 text-red-600 hover:text-white dark:text-red-400 dark:hover:text-white rounded-lg text-sm font-bold transition-all border border-red-200 dark:border-red-800"
                    data-id="<?php echo (int)$pedido['id_pedido']; ?>">
                    Cancelar Pedido
                    <span class="material-icons text-sm">close</span>
                    </button>
                <?php endif; ?>
            </div>
        </td>

    </tr>

    <?php endwhile; ?>
<?php else: ?>

    <tr>
        <td colspan="5" class="px-6 py-10 text-center text-slate-500">
            No tienes pedidos registrados aún.
        </td>
    </tr>

<?php endif; ?>

</tbody>
</table>
</div>
<div class="p-6 bg-slate-50 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
<p class="text-sm text-slate-500">
<?php
       $offset = ($pagina_actual - 1) * $por_pagina;

       /* calcular rango mostrado */
       $desde = 0;
       $hasta = 0;
       
       if ($total_pedidos > 0) {
           $desde = $offset + 1;
           $hasta = min($offset + $por_pagina, $total_pedidos);
       }
       echo "Mostrando {$desde}-{$hasta} de {$total_pedidos} pedidos";
        ?>
</p>
<div class="flex gap-2">
    <!-- Botón anterior -->
    <button
        class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-white dark:hover:bg-slate-700 disabled:opacity-50"
        <?php if ($pagina_actual <= 1): ?>disabled<?php endif; ?>
        onclick="if(<?php echo $pagina_actual; ?> > 1) window.location='?modulo=historialpedidoC&page=<?php echo ($pagina_actual-1); ?>';"
        aria-label="Anterior"
    >
        <span class="material-icons text-lg leading-none">chevron_left</span>
    </button>
    <!-- Botones de página dinámicos -->
    <?php
        // Mostrar un rango limitado si hay muchas páginas
        $max_buttons = 5;
        $mitad = floor($max_buttons/2);
        $inicio = max(1, $pagina_actual - $mitad);
        $fin = min($total_paginas, $inicio + $max_buttons - 1);
        if ($fin - $inicio < $max_buttons - 1) {
            $inicio = max(1, $fin - $max_buttons + 1);
        }
        for ($i = $inicio; $i <= $fin; $i++):
    ?>
        <button
            class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg <?php echo ($i==$pagina_actual) ? 'bg-primary text-white font-bold px-4 text-sm' : 'hover:bg-white dark:hover:bg-slate-700'; ?>"
            <?php if ($i == $pagina_actual): ?>disabled<?php endif; ?>
            onclick="if(<?php echo $i; ?> != <?php echo $pagina_actual; ?>) window.location='?modulo=historialpedidoC&page=<?php echo $i; ?>';"
        ><?php echo $i; ?></button>
    <?php endfor; ?>
    <!-- Botón siguiente -->
    <button
        class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-white dark:hover:bg-slate-700 disabled:opacity-50"
        <?php if ($pagina_actual >= $total_paginas): ?>disabled<?php endif; ?>
        onclick="if(<?php echo $pagina_actual; ?> < <?php echo $total_paginas; ?>) window.location='?modulo=historialpedidoC&page=<?php echo ($pagina_actual+1); ?>';"
        aria-label="Siguiente"
    >
        <span class="material-icons text-lg leading-none">chevron_right</span>
    </button>
</div>
</div>
</div>
</main>

<!-- Modal de confirmación de cancelación -->
<div id="modalConfirmacionCancelacion" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-slate-900 rounded-xl w-full max-w-md p-6 shadow-lg">
        <div class="mb-4">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Confirmación</h2>
        </div>
        <div class="mb-6">
            <p class="text-slate-600 dark:text-slate-300 text-base">
                ¿Estás seguro de que deseas cancelar este pedido? Esta acción no se puede deshacer.
            </p>
        </div>
        <div class="flex gap-3 justify-end">
            <button 
                id="btnCancelarCancelacion"
                type="button"
                class="px-6 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-white rounded-lg font-semibold transition-colors">
                No, mantener pedido
            </button>
            <button 
                id="btnConfirmarCancelacion"
                type="button"
                class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition-colors">
                Sí, cancelar pedido
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    function formatRestante(segundos) {
        if (segundos <= 0) return '00:00:00';
        var h = Math.floor(segundos / 3600);
        var m = Math.floor((segundos % 3600) / 60);
        var s = Math.floor(segundos % 60);
        return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }
    function actualizarCountdowns() {
        var ahora = Math.floor(Date.now() / 1000);
        document.querySelectorAll('tr[data-deadline]').forEach(function(tr) {
            var deadline = parseInt(tr.getAttribute('data-deadline'));
            var restante = Math.max(0, Math.floor(deadline - ahora));
            var cell = tr.querySelector('.countdown-cell');
            if (!cell) return;
            var wrapper = cell.querySelector('.countdown-wrapper');
            var display = cell.querySelector('.countdown-display');
            var expirado = cell.querySelector('.expirado-message');
            var btn = tr.querySelector('.btn-cancelar-pedido');
            if (restante <= 0) {
                if (wrapper) wrapper.classList.add('hidden');
                if (display) display.textContent = '00:00:00';
                if (expirado) { expirado.classList.remove('hidden'); expirado.textContent = 'Tiempo de cancelación expirado'; }
                if (btn) btn.classList.add('hidden');
            } else {
                if (wrapper) wrapper.classList.remove('hidden');
                if (display) display.textContent = formatRestante(restante);
                if (expirado) expirado.classList.add('hidden');
                if (btn) btn.classList.remove('hidden');
            }
        });
    }
    actualizarCountdowns();
    setInterval(actualizarCountdowns, 1000);
    
    console.log('Historial script completamente inicializado');
})();
</script>

<div id="modalPedido" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 relative">

    <button type="button"
class="cerrar-modal absolute top-3 right-3 text-gray-500 hover:text-black">
✕
</button>

        <div id="contenidoModal">
            <!-- Aquí se carga el detalle -->
        </div>

    </div>
</div>

<script>
function cerrarModal() {
    const modal = document.getElementById("modalPedido");
    const contenido = document.getElementById("contenidoModal");

    if (modal) {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    }

    if (contenido) {
        contenido.innerHTML = "";
    }
}

// Cerrar modal al hacer clic en la X
document.addEventListener("click", function(e) {
    if (e.target && e.target.classList.contains("cerrar-modal")) {
        cerrarModal();
    }
});

// Cerrar modal al hacer clic fuera (en el fondo oscuro)
document.addEventListener("click", function(e) {
    const modal = document.getElementById("modalPedido");
    if (modal && e.target === modal) {
        cerrarModal();
    }
});
</script>
</body></html>
<!-- Se requiere un archivo cancelar_pedido_ajax.php para manejar la actualización del estado vía AJAX:
<?php
require_once '../core/sesiones.php';
if (!usuarioAutenticado()) {
    echo json_encode(['success'=>false, 'msg'=>'No autenticado']); exit;
}
$id = isset($_POST['id_pedido']) ? (int)$_POST['id_pedido'] : 0;
if (!$id) { echo json_encode(['success'=>false]); exit; }
$usuario = obtenerDatosUsuario();
$id_cliente = $usuario['id_cliente'] ?? null;
require '../core/db.php'; // O el archivo que da acceso a $conexion
$stmt = $conexion->prepare("UPDATE pedidos SET estado='cancelado' WHERE id_pedido=? AND id_cliente=?");
$stmt->bind_param("ii", $id, $id_cliente);
$stmt->execute();
echo json_encode(['success'=>($stmt->affected_rows>0)]);
?>
-->