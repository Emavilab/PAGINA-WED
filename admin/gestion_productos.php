<?php
require_once '../core/sesiones.php';
require_once '../core/conexion.php';

// sólo administradores y vendedores pueden acceder
if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    header("Location: ../index1.php");
    exit();
}

// cargar categorías activas para el selector
$categories = [];
$catResult = $conexion->query("SELECT id_categoria, nombre FROM categorias WHERE estado='activo'");
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row;
}

// manejar eliminación
if (isset($_GET['eliminar'])) {
    $del_id = intval($_GET['eliminar']);
    $conexion->query("DELETE FROM productos WHERE id_producto = $del_id");
    header("Location: gestion_productos.php");
    exit();
}

$errors = [];
// manejar creación/actualización
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    $id = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;
    $nombre = trim($_POST['nombre_producto']);
    $sku = trim($_POST['sku_codigo_referencia']);
    $id_categoria = intval($_POST['id_categoria']);
    $descripcion = trim($_POST['descripcion']);
    $precio_venta = floatval($_POST['precio_venta']);
    $precio_costo = floatval($_POST['precio_costo']);
    $stock_inicial = intval($_POST['stock_inicial']);
    $alerta_stock = intval($_POST['alerta_stock_minimo']);
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : 'activo';

    // validaciones básicas
    if ($nombre === '') {
        $errors[] = 'El nombre es obligatorio';
    }
    if ($sku === '') {
        $errors[] = 'El SKU es obligatorio';
    }
    if ($id_categoria <= 0) {
        $errors[] = 'Debe seleccionar una categoría';
    }

    // imagen
    $imagen_producto = '';
    if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['imagen_producto']['tmp_name'];
        $name = basename($_FILES['imagen_producto']['name']);
        $target = __DIR__ . '/../img/productos/' . $name;
        if (move_uploaded_file($tmp, $target)) {
            $imagen_producto = $name;
        } else {
            $errors[] = 'Error al subir la imagen';
        }
    } else {
        // si hay imagen antigua en edición
        if ($id > 0 && !empty($_POST['imagen_actual'])) {
            $imagen_producto = $_POST['imagen_actual'];
        }
    }

    if (empty($errors)) {
        if ($id > 0) {
            $stmt = $conexion->prepare(
                "UPDATE productos SET nombre_producto=?, sku_codigo_referencia=?, id_categoria=?, descripcion=?, precio_venta=?, precio_costo=?, stock_inicial=?, alerta_stock_minimo=?, imagen_producto=?, estado=? WHERE id_producto=?"
            );
            $stmt->bind_param(
                "ssisdiiiiisi",
                $nombre,
                $sku,
                $id_categoria,
                $descripcion,
                $precio_venta,
                $precio_costo,
                $stock_inicial,
                $alerta_stock,
                $imagen_producto,
                $estado,
                $id
            );
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conexion->prepare(
                "INSERT INTO productos (nombre_producto, sku_codigo_referencia, id_categoria, descripcion, precio_venta, precio_costo, stock_inicial, alerta_stock_minimo, imagen_producto, estado, fecha_creacion) VALUES (?,?,?,?,?,?,?,?,?,?,NOW())"
            );
            $stmt->bind_param(
                "ssisdiiiiis",
                $nombre,
                $sku,
                $id_categoria,
                $descripcion,
                $precio_venta,
                $precio_costo,
                $stock_inicial,
                $alerta_stock,
                $imagen_producto,
                $estado
            );
            $stmt->execute();
            $stmt->close();
        }
        header("Location: gestion_productos.php");
        exit();
    }
}

// lista de productos
$products = [];
$res = $conexion->query(
    "SELECT p.*, c.nombre AS categoria_nombre FROM productos p LEFT JOIN categorias c ON p.id_categoria=c.id_categoria ORDER BY p.fecha_creacion DESC"
);
while ($row = $res->fetch_assoc()) {
    $products[] = $row;
}

// producto a editar (prefill)
$edit = null;
if (isset($_GET['editar'])) {
    $edit_id = intval($_GET['editar']);
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Gestión de Productos | Admin CMS</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
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
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen font-display">
<main class="p-8">
<header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
<div>
<h1 class="text-2xl font-bold mb-1">Lista de Productos</h1>
<p class="text-sm text-slate-500 dark:text-slate-400">Gestiona el catálogo de productos de tus 4 sedes de negocio.</p>
</div>
<div class="flex items-center gap-3">
<button onclick="openFormModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
<span class="material-icons text-sm">add</span>
<span>Agregar Nuevo Producto</span>
</button>
</div>
</header>
<?php if (!empty($errors)): ?>
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $err): ?>
                <li><?=htmlspecialchars($err)?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-xl">
<div class="text-sm text-slate-500 mb-1">Total Productos</div>
<div class="text-2xl font-bold"><?=count($products)?></div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
<div class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4">
<div class="relative w-full sm:w-96">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
<input class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none" placeholder="Buscar por nombre, categoría o marca..." type="text"/>
</div>
</div>
<div class="overflow-x-auto scrollbar-hide">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50/50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider font-semibold">
<th class="px-6 py-4">ID</th>
<th class="px-6 py-4">Nombre</th>
<th class="px-6 py-4">Categoría</th>
<th class="px-6 py-4">SKU</th>
<th class="px-6 py-4">Precio venta</th>
<th class="px-6 py-4">Stock</th>
<th class="px-6 py-4">Estado</th>
<th class="px-6 py-4 text-right">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-200 dark:divide-slate-800">
<?php foreach ($products as $p): ?>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
<td class="px-6 py-4 text-sm text-slate-500"><?= $p['id_producto'] ?></td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
<?php if (!empty($p['imagen_producto'])): ?>
<img class="w-full h-full object-cover" src="../img/productos/<?= htmlspecialchars($p['imagen_producto']) ?>"/>
<?php endif; ?>
</div>
<div class="font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($p['nombre_producto']) ?></div>
</div>
</td>
<td class="px-6 py-4">
<?= htmlspecialchars($p['categoria_nombre']) ?>
</td>
<td class="px-6 py-4"><?= htmlspecialchars($p['sku_codigo_referencia']) ?></td>
<td class="px-6 py-4"><?= number_format($p['precio_venta'],2) ?></td>
<td class="px-6 py-4"><?= $p['stock_inicial'] ?></td>
<td class="px-6 py-4"><?= htmlspecialchars($p['estado']) ?></td>
<td class="px-6 py-4 text-right">
<div class="flex items-center justify-end gap-2">
<a href="?editar=<?= $p['id_producto'] ?>" class="p-2 text-slate-400 hover:text-primary transition-colors" title="Editar">
<span class="material-symbols-outlined text-xl">edit</span>
</a>
<a href="?eliminar=<?= $p['id_producto'] ?>" onclick="return confirm('¿Eliminar este producto?');" class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Borrar">
<span class="material-symbols-outlined text-xl">delete</span>
</a>
</div>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

<!-- Modal Crear/Editar Producto -->
<div id="formModal" class="<?php echo $edit ? '' : 'hidden'; ?> fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden max-w-2xl w-full max-h-[90vh] overflow-y-auto">
<div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between sticky top-0 bg-white dark:bg-slate-800">
<div class="flex items-center gap-2">
<span class="material-icons text-primary">edit_note</span>
<h2 class="text-xl font-bold text-slate-800 dark:text-white"><?php echo $edit ? 'Editar Producto' : 'Crear Producto'; ?></h2>
</div>
<button onclick="closeFormModal()" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors">
<span class="material-icons">close</span>
</button>
</div>
<form action="" method="post" enctype="multipart/form-data" class="p-8 space-y-6">
<input type="hidden" name="id_producto" value="<?= $edit ? $edit['id_producto'] : '' ?>">
<input type="hidden" name="imagen_actual" value="<?= $edit ? htmlspecialchars($edit['imagen_producto']) : '' ?>">
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">label</span>
Nombre del Producto
</label>
<input name="nombre_producto" value="<?= $edit ? htmlspecialchars($edit['nombre_producto']) : '' ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all dark:placeholder-slate-500" placeholder="Ej: Laptop Dell XPS 13" type="text" required/>
</div>
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">description</span>
Descripción
</label>
<textarea name="descripcion" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all dark:placeholder-slate-500" placeholder="Descripción detallada del producto..." rows="4"><?= $edit ? htmlspecialchars($edit['descripcion']) : '' ?></textarea>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">category</span>
Categoría
</label>
<select name="id_categoria" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none appearance-none" required>
<option value="">Seleccionar categoría</option>
<?php foreach ($categories as $cat): ?>
    <option value="<?= $cat['id_categoria'] ?>" <?php if ($edit && $edit['id_categoria']==$cat['id_categoria']) echo 'selected'; ?>><?= htmlspecialchars($cat['nombre']) ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">branding_watermark</span>
SKU / Código de referencia
</label>
<input name="sku_codigo_referencia" value="<?= $edit ? htmlspecialchars($edit['sku_codigo_referencia']) : '' ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none" placeholder="Ej: SKU12345" type="text" required/>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">payments</span>
Precio de Venta
</label>
<input name="precio_venta" value="<?= $edit ? htmlspecialchars($edit['precio_venta']) : '' ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none" placeholder="0.00" step="0.01" type="number"/>
</div>
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">inventory_2</span>
Stock Inicial
</label>
<input name="stock_inicial" value="<?= $edit ? htmlspecialchars($edit['stock_inicial']) : '' ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none" placeholder="0" type="number"/>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">inventory_2</span>
Stock alerta mínimo
</label>
<input name="alerta_stock_minimo" value="<?= $edit ? htmlspecialchars($edit['alerta_stock_minimo']) : '' ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none" placeholder="0" type="number"/>
</div>
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">payments</span>
Precio Costo
</label>
<input name="precio_costo" value="<?= $edit ? htmlspecialchars($edit['precio_costo']) : '' ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none" placeholder="0.00" step="0.01" type="number"/>
</div>
</div>
<div class="space-y-2 max-w-md">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">check_circle</span>
Estado
</label>
<select name="estado" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none">
<option value="activo" <?php if ($edit && $edit['estado']=='activo') echo 'selected'; ?>>Activo</option>
<option value="inactivo" <?php if ($edit && $edit['estado']=='inactivo') echo 'selected'; ?>>Inactivo</option>
</select>
</div>
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">image</span>
Imagen del Producto
</label>
<?php if ($edit && !empty($edit['imagen_producto'])): ?>
    <div class="mb-2">
        <img src="../img/productos/<?=htmlspecialchars($edit['imagen_producto'])?>" class="w-20 h-20 object-cover" />
    </div>
<?php endif; ?>
<input type="file" name="imagen_producto" class="w-full" accept="image/*" />
</div>
<div class="flex flex-col sm:flex-row gap-4 pt-4">
<button class="flex-1 bg-primary hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-bold flex items-center justify-center gap-2 transition-all shadow-md" type="submit">
<span class="material-icons">save</span>
Guardar Producto
</button>
<button onclick="closeFormModal()" class="flex-1 bg-slate-400 hover:bg-slate-500 text-white py-3 px-6 rounded-lg font-bold flex items-center justify-center gap-2 transition-all shadow-md" type="button">
<span class="material-icons">close</span>
Cancelar
</button>
</div>
</form>
</div>
</div>

<script>
function openFormModal() {
    const modal = document.getElementById('formModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeFormModal() {
    const modal = document.getElementById('formModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// cerrar modal al hacer clic fuera o pulsar ESC
document.addEventListener('DOMContentLoaded', function() {
    const formModal = document.getElementById('formModal');
    if (formModal) {
        formModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeFormModal();
            }
        });
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFormModal();
        }
    });
});
</script>
</main>
</body></html>