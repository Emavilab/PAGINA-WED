<?php
require_once '../core/sesiones.php';

// Solo clientes pueden acceder
if (!usuarioAutenticado() || $_SESSION['id_rol'] != 3) {
    header("Location: ../index1.php");
    exit();
}

// Obtener datos del cliente
require_once '../core/conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$stmt = $conexion->prepare("SELECT c.*, u.nombre, u.correo FROM clientes c JOIN usuarios u ON c.id_usuario = u.id_usuario WHERE u.id_usuario = ?");
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$clienteResult = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener cantidad de pedidos
$stmt = $conexion->prepare("SELECT COUNT(*) as total FROM pedidos WHERE id_cliente = ?");
$cliente_id = $clienteResult['id_cliente'] ?? 0;
$stmt->bind_param('i', $cliente_id);
$stmt->execute();
$pedidosCount = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Obtener últimos pedidos (3 más recientes)
$stmt = $conexion->prepare("SELECT id_pedido, fecha_pedido, total, estado FROM pedidos WHERE id_cliente = ? ORDER BY fecha_pedido DESC LIMIT 3");
$stmt->bind_param('i', $cliente_id);
$stmt->execute();
$pedidosResult = $stmt->get_result();
$ultimosPedidos = [];
while ($row = $pedidosResult->fetch_assoc()) {
    $ultimosPedidos[] = $row;
}
$stmt->close();

// Obtener cantidad de mensajes sin leer
$stmt = $conexion->prepare("SELECT COUNT(*) as total FROM mensajes WHERE id_cliente = ? AND leido = 0");
$stmt->bind_param('i', $cliente_id);
$stmt->execute();
$mensajesNoLeidos = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mi Panel - Cliente</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#137fec",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
            font-size: 24px;
        }
    </style>
</head>
<body class="bg-white dark:bg-background-dark text-slate-900 dark:text-slate-100">

    <!-- Navbar -->
    <nav class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-3xl">storefront</span>
                <h1 class="text-xl font-bold">Mi Panel</h1>
            </div>
            <div class="flex items-center gap-6">
                <a href="productos.php" class="text-sm font-medium hover:text-primary transition">
                    <span class="material-symbols-outlined inline text-lg align-middle mr-1">shopping_bag</span>
                    Tienda
                </a>
                <a href="perfil.php" class="text-sm font-medium hover:text-primary transition">
                    <span class="material-symbols-outlined inline text-lg align-middle mr-1">person</span>
                    Perfil
                </a>
                <a href="../core/cerrar_sesion.php" class="text-sm font-medium hover:text-red-500 transition">
                    <span class="material-symbols-outlined inline text-lg align-middle mr-1">logout</span>
                    Salir
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6">
        <!-- Bienvenida -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold mb-2">Bienvenido, <?= htmlspecialchars($clienteResult['nombre'] ?? $_SESSION['nombre']) ?></h2>
            <p class="text-slate-500 dark:text-slate-400">Aquí puedes ver tu información y administrar tus compras</p>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Mis pedidos -->
            <a href="historialpedidoC.php" class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 hover:shadow-lg transition cursor-pointer border border-slate-200 dark:border-slate-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Mis Pedidos</p>
                        <p class="text-3xl font-bold text-primary"><?= $pedidosCount ?></p>
                    </div>
                    <span class="material-symbols-outlined text-primary text-4xl opacity-20">receipt_long</span>
                </div>
            </a>

            <!-- Carrito -->
            <a href="carrito.php" class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 hover:shadow-lg transition cursor-pointer border border-slate-200 dark:border-slate-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Mi Carrito</p>
                        <p class="text-3xl font-bold text-primary">0</p>
                    </div>
                    <span class="material-symbols-outlined text-primary text-4xl opacity-20">shopping_cart</span>
                </div>
            </a>

            <!-- Mensajes -->
            <a href="mensajeria.php" class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 hover:shadow-lg transition cursor-pointer border border-slate-200 dark:border-slate-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Mensajes</p>
                        <p class="text-3xl font-bold text-primary"><?= $mensajesNoLeidos ?></p>
                    </div>
                    <span class="material-symbols-outlined text-primary text-4xl opacity-20">mail</span>
                </div>
            </a>
        </div>

        <!-- Últimos pedidos -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">history</span>
                    Últimos Pedidos
                </h3>
            </div>
            <?php if (empty($ultimosPedidos)): ?>
                <div class="p-6 text-center text-slate-500">
                    <span class="material-symbols-outlined text-3xl mb-2 opacity-50 block">shopping_bag</span>
                    <p>No tienes pedidos aún. <a href="productos.php" class="text-primary font-bold">¡Comienza a comprar!</a></p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Pedido</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Fecha</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Total</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Estado</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            <?php foreach ($ultimosPedidos as $pedido): ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                    <td class="px-6 py-4 text-sm font-semibold">#<?= $pedido['id_pedido'] ?></td>
                                    <td class="px-6 py-4 text-sm"><?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></td>
                                    <td class="px-6 py-4 text-sm font-bold text-primary">$<?= number_format($pedido['total'], 2) ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            <?php
                                            switch ($pedido['estado']) {
                                                case 'pendiente':
                                                    echo 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
                                                    break;
                                                case 'confirmado':
                                                    echo 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400';
                                                    break;
                                                case 'enviado':
                                                    echo 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400';
                                                    break;
                                                case 'entregado':
                                                    echo 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
                                                    break;
                                                default:
                                                    echo 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300';
                                            }
                                            ?>
                                        ">
                                            <?= ucfirst($pedido['estado']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="historialpedidoC.php?id=<?= $pedido['id_pedido'] ?>" class="text-primary hover:underline text-sm font-bold">
                                            Ver
                                            <span class="material-symbols-outlined inline text-sm">arrow_forward</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-6 border-t border-slate-200 dark:border-slate-700 text-center">
                    <a href="historialpedidoC.php" class="text-primary hover:underline font-bold">Ver todos mis pedidos</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Accesos rápidos -->
        <div class="mt-8">
            <h3 class="text-lg font-bold mb-4">Accesos Rápidos</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="productos.php" class="bg-gradient-to-br from-primary/10 to-primary/5 border border-primary/20 rounded-lg p-4 hover:shadow-lg transition flex flex-col items-center gap-2 text-center">
                    <span class="material-symbols-outlined text-primary text-3xl">shopping_bag</span>
                    <span class="text-sm font-bold">Comprar</span>
                </a>
                <a href="historialpedidoC.php" class="bg-gradient-to-br from-blue-500/10 to-blue-500/5 border border-blue-500/20 rounded-lg p-4 hover:shadow-lg transition flex flex-col items-center gap-2 text-center">
                    <span class="material-symbols-outlined text-blue-500 text-3xl">receipt_long</span>
                    <span class="text-sm font-bold">Mis Pedidos</span>
                </a>
                <a href="perfil.php" class="bg-gradient-to-br from-green-500/10 to-green-500/5 border border-green-500/20 rounded-lg p-4 hover:shadow-lg transition flex flex-col items-center gap-2 text-center">
                    <span class="material-symbols-outlined text-green-500 text-3xl">person</span>
                    <span class="text-sm font-bold">Mi Perfil</span>
                </a>
                <a href="mensajeria.php" class="bg-gradient-to-br from-purple-500/10 to-purple-500/5 border border-purple-500/20 rounded-lg p-4 hover:shadow-lg transition flex flex-col items-center gap-2 text-center">
                    <span class="material-symbols-outlined text-purple-500 text-3xl">mail</span>
                    <span class="text-sm font-bold">Mensajería</span>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 mt-12">
        <div class="max-w-7xl mx-auto p-6 text-center text-slate-500 dark:text-slate-400 text-sm">
            <p>&copy; 2026 PAGINA-WED. Todos los derechos reservados.</p>
        </div>
    </footer>

</body>
</html>
