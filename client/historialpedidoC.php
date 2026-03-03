<?php
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

// Obtener pedidos del cliente
$sql = "SELECT id_pedido, fecha_pedido, estado, total 
        FROM pedidos 
        WHERE id_cliente = ? 
        ORDER BY fecha_pedido DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$resultado = $stmt->get_result();
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
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
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
            default:
                $claseEstado = 'status-badge-procesando';
                $textoEstado = ucfirst($pedido['estado']);
        }
    ?>

    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">

        <td class="px-6 py-5 whitespace-nowrap">
            <span class="font-bold text-slate-900 dark:text-white">
                #<?php echo $pedido['id_pedido']; ?>
            </span>
        </td>

        <td class="px-6 py-5 whitespace-nowrap text-slate-600 dark:text-slate-400 text-sm">
            <?php echo $fecha; ?>
        </td>

        <td class="px-6 py-5 whitespace-nowrap">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?php echo $claseEstado; ?>">
                <?php echo $textoEstado; ?>
            </span>
        </td>

        <td class="px-6 py-5 whitespace-nowrap font-semibold">
            L <?php echo number_format($pedido['total'], 2); ?>
        </td>

        <td class="px-6 py-5 whitespace-nowrap text-right">
            <button 
                class="btn-ver-detalle inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-primary hover:text-white dark:bg-slate-800 dark:hover:bg-primary text-slate-700 dark:text-slate-300 rounded-lg text-sm font-bold transition-all"
                data-id="<?php echo $pedido['id_pedido']; ?>">
                Ver Detalles
                <span class="material-icons text-sm">visibility</span>
            </button>
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
<p class="text-sm text-slate-500">Mostrando 4 pedidos</p>
<div class="flex gap-2">
<button class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-white dark:hover:bg-slate-700 disabled:opacity-50" disabled="">
<span class="material-icons text-lg leading-none">chevron_left</span>
</button>
<button class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-primary text-white font-bold px-4 text-sm">1</button>
<button class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-white dark:hover:bg-slate-700">
<span class="material-icons text-lg leading-none">chevron_right</span>
</button>
</div>
</div>
</div>
</main>
<div id="modalPedido" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 relative">

        <button onclick="cerrarModal()" 
            class="absolute top-3 right-3 text-gray-500 hover:text-black">
            ✕
        </button>

        <div id="contenidoModal">
            <!-- Aquí se carga el detalle -->
        </div>

    </div>
</div>

</body></html>
