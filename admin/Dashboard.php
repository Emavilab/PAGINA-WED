<?php
/**
 * Dashboard Administrativo
 * Solo accesible para Administrador (rol 1) y Vendedor (rol 2)
 */

require_once '../core/sesiones.php';

// Verificar autenticación
if (!usuarioAutenticado()) {
    header("Location: login.php");
    exit();
}

// Verificar permisos: solo rol 1 (admin) y rol 2 (vendedor)
if ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2) {
    // Usuario sin permisos, redirigir a index
    header("Location: ../index1.php");
    exit();
}

// Obtener datos del usuario autenticado
$usuario = obtenerDatosUsuario();
?>
<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Dashboard Administrativo Profesional</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#3b82f6",
              "background-light": "#f8fafc",
              "background-dark": "#0f172a",
              "sidebar-dark": "#1e293b",
            },
            fontFamily: {
              display: ["Inter", "sans-serif"],
            },
            borderRadius: {
              DEFAULT: "0.75rem",
            },
          },
        },
      };
      function toggleDarkMode() {
        document.documentElement.classList.toggle('dark');
      }
    </script>
<style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-active {
            background-color: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
            color: #3b82f6;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 transition-colors duration-200">
<div class="flex h-screen overflow-hidden">
<aside class="w-64 bg-sidebar-dark text-white flex-shrink-0 flex flex-col hidden lg:flex">
<div class="p-6 flex items-center gap-3">
<div class="bg-primary p-2 rounded-lg">
<span class="material-icons-round text-white">store</span>
</div>
<div>
<h1 class="font-bold text-lg leading-tight">Mi Negocio</h1>
<p class="text-xs text-slate-400">Admin Panel</p>
</div>
</div>
<nav class="flex-1 mt-4 px-3 space-y-1">
<a class="flex items-center gap-3 px-4 py-3 sidebar-active rounded-r-none rounded-lg transition-all nav-link" href="#" onclick="loadPage('Dashboard.php', event)">
<span class="material-icons-round">dashboard</span>
<span class="font-medium">Dashboard</span>
</a>
<?php if ($_SESSION['id_rol'] == 1): // Solo para administrador ?>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('../client/productos.php', event)">
<span class="material-icons-round">inventory_2</span>
<span class="font-medium">Productos</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('../client/categoria.php', event)">
<span class="material-icons-round">category</span>
<span class="font-medium">Categorías</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('../client/clientes.php', event)">
<span class="material-icons-round">people</span>
<span class="font-medium">Clientes</span>
</a>
<?php endif; ?>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('./pedidosadmin.php', event)">
<span class="material-icons-round">shopping_cart</span>
<span class="font-medium">Pedidos</span>
</a>
<?php if ($_SESSION['id_rol'] == 1): // Solo para administrador ?>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('./usuarios.php', event)">
<span class="material-icons-round">manage_accounts</span>
<span class="font-medium">Usuarios</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('../client/mensajeria.php', event)">
<span class="material-icons-round">mail</span>
<span class="font-medium">Mensajería</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('../core/configuracion.php', event)">
<span class="material-icons-round">settings</span>
<span class="font-medium">Configuraciones</span>
</a>
<?php endif; ?>
</nav>
<div class="p-4 mt-auto">
<button class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-red-400 w-full transition-all" onclick="cerrarSesion();">
<span class="material-icons-round">logout</span>
<span class="font-medium">Cerrar Sesión</span>
</button>
</div>
</aside>
<div class="flex-1 flex flex-col overflow-y-auto">
<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-8 flex items-center justify-between sticky top-0 z-10">
<h2 id="page-title" class="text-xl font-bold dark:text-white">Dashboard</h2>
<div class="flex items-center gap-6">
<button class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors" onclick="toggleDarkMode()">
<span class="material-icons-round dark:hidden">dark_mode</span>
<span class="material-icons-round hidden dark:block text-yellow-400">light_mode</span>
</button>
<button class="relative p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
<span class="material-icons-round">notifications</span>
<span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
</button>
<div class="flex items-center gap-3 pl-4 border-l border-slate-200 dark:border-slate-800">
<div class="text-right hidden sm:block">
<p class="text-sm font-semibold dark:text-white"><?php echo htmlspecialchars($usuario['nombre'] ?? 'Usuario'); ?></p>
<p class="text-xs text-slate-500"><?php echo htmlspecialchars($usuario['nombre_rol'] ?? 'Sin rol'); ?></p>
</div>
<img alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-primary/20" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDjLIRD3VjeXzAexA8RNlnwfNuxo9_3LYfXncJ7KNLB2OF5dsOzX1SN7Zzpzlo_wtsHyINXyxAjAZmxkms2AFqLczyXft-Hnlc08GhRdmd8XTPhvyfNz3B8r6mk1iL_snNH5pI3nM5ZR6cLOLJhkTijYUDTB4f758oIfwnZ7h2KsElVYJ9PspnA99elI_-NFEedvH4HJ6EyKyzESoCOE6CK2Afsa-xQLsKnrMu7yCghMeb__nwsxlrOxKL0be4Q3wzePnWYI89B-24"/>
</div>
</div>
</header>
<main id="main-content" class="p-8">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border-l-4 border-blue-500 shadow-sm hover:shadow-md transition-shadow">
<div class="flex justify-between items-start">
<div>
<p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Total Productos</p>
<h3 class="text-2xl font-bold mt-1">2,543</h3>
<p class="text-xs text-green-500 mt-2 flex items-center font-medium">
<span class="material-icons-round text-xs mr-1">trending_up</span> 12% este mes
                                </p>
</div>
<div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-500 rounded-xl">
<span class="material-icons-round">inventory_2</span>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border-l-4 border-indigo-500 shadow-sm hover:shadow-md transition-shadow">
<div class="flex justify-between items-start">
<div>
<p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Total Clientes</p>
<h3 class="text-2xl font-bold mt-1">1,234</h3>
<p class="text-xs text-green-500 mt-2 flex items-center font-medium">
<span class="material-icons-round text-xs mr-1">trending_up</span> 8% este mes
                                </p>
</div>
<div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-500 rounded-xl">
<span class="material-icons-round">people</span>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border-l-4 border-orange-500 shadow-sm hover:shadow-md transition-shadow">
<div class="flex justify-between items-start">
<div>
<p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Pedidos Hoy</p>
<h3 class="text-2xl font-bold mt-1">45</h3>
<p class="text-xs text-green-500 mt-2 flex items-center font-medium">
<span class="material-icons-round text-xs mr-1">trending_up</span> 23% vs ayer
                                </p>
</div>
<div class="p-3 bg-orange-50 dark:bg-orange-900/30 text-orange-500 rounded-xl">
<span class="material-icons-round">shopping_cart</span>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border-l-4 border-green-500 shadow-sm hover:shadow-md transition-shadow">
<div class="flex justify-between items-start">
<div>
<p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Ingresos Hoy</p>
<h3 class="text-2xl font-bold mt-1">$4,532.50</h3>
<p class="text-xs text-green-500 mt-2 flex items-center font-medium">
<span class="material-icons-round text-xs mr-1">trending_up</span> 15% vs ayer
                                </p>
</div>
<div class="p-3 bg-green-50 dark:bg-green-900/30 text-green-500 rounded-xl">
<span class="material-icons-round">attach_money</span>
</div>
</div>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
<div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm">
<div class="flex items-center justify-between mb-6">
<h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
<span class="material-icons-round text-primary">show_chart</span>
                                Ventas Este Mes
                            </h4>
<select class="text-sm bg-slate-50 dark:bg-slate-800 border-none rounded-lg focus:ring-primary">
<option>Últimos 30 días</option>
<option>Últimos 7 días</option>
</select>
</div>
<div class="h-64 bg-slate-50 dark:bg-slate-800/50 rounded-xl flex items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-700">
<div class="text-center">
<span class="material-icons-round text-4xl text-slate-300 dark:text-slate-600">bar_chart</span>
<p class="text-slate-400 dark:text-slate-500 mt-2 text-sm font-medium">Gráfico de ventas</p>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm">
<h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-6">
<span class="material-icons-round text-primary">list_alt</span>
                            Estados de Pedidos
                        </h4>
<div class="space-y-6">
<div>
<div class="flex justify-between mb-2">
<span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Pendientes</span>
<span class="text-sm font-bold">12</span>
</div>
<div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
<div class="bg-blue-500 h-2 rounded-full" style="width: 35%"></div>
</div>
</div>
<div>
<div class="flex justify-between mb-2">
<span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Confirmados</span>
<span class="text-sm font-bold">18</span>
</div>
<div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
<div class="bg-green-500 h-2 rounded-full" style="width: 60%"></div>
</div>
</div>
<div>
<div class="flex justify-between mb-2">
<span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Enviados</span>
<span class="text-sm font-bold">10</span>
</div>
<div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
<div class="bg-orange-500 h-2 rounded-full" style="width: 45%"></div>
</div>
</div>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm overflow-hidden">
<div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
<h4 class="font-bold text-slate-800 dark:text-white">Pedidos Recientes</h4>
<button class="text-primary hover:text-blue-600 text-sm font-semibold flex items-center gap-1 group">
                            Ver todos los pedidos
                            <span class="material-icons-round text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
</button>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 uppercase text-xs font-bold tracking-wider">
<tr>
<th class="px-6 py-4">ID Pedido</th>
<th class="px-6 py-4">Cliente</th>
<th class="px-6 py-4">Monto</th>
<th class="px-6 py-4">Estado</th>
<th class="px-6 py-4">Fecha</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-slate-800">
<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
<td class="px-6 py-4 font-medium text-slate-600 dark:text-slate-300">#1005</td>
<td class="px-6 py-4">Carlos López</td>
<td class="px-6 py-4 font-semibold text-green-600">$1,250.00</td>
<td class="px-6 py-4">
<span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold rounded-full">Entregado</span>
</td>
<td class="px-6 py-4 text-slate-500 text-sm">Hoy</td>
</tr>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
<td class="px-6 py-4 font-medium text-slate-600 dark:text-slate-300">#1004</td>
<td class="px-6 py-4">Ana Martínez</td>
<td class="px-6 py-4 font-semibold text-green-600">$890.50</td>
<td class="px-6 py-4">
<span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 text-xs font-bold rounded-full">Enviado</span>
</td>
<td class="px-6 py-4 text-slate-500 text-sm">Ayer</td>
</tr>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
<td class="px-6 py-4 font-medium text-slate-600 dark:text-slate-300">#1003</td>
<td class="px-6 py-4">Roberto García</td>
<td class="px-6 py-4 font-semibold text-green-600">$2,100.00</td>
<td class="px-6 py-4">
<span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold rounded-full">Confirmado</span>
</td>
<td class="px-6 py-4 text-slate-500 text-sm">Hace 2 días</td>
</tr>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
<td class="px-6 py-4 font-medium text-slate-600 dark:text-slate-300">#1002</td>
<td class="px-6 py-4">María Pérez</td>
<td class="px-6 py-4 font-semibold text-green-600">$567.30</td>
<td class="px-6 py-4">
<span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-xs font-bold rounded-full">Pendiente</span>
</td>
<td class="px-6 py-4 text-slate-500 text-sm">Hace 3 días</td>
</tr>
</tbody>
</table>
</div>
</div>
</main>
</div>
</div>

<script>
function loadPage(page, event) {
    if (event) {
        event.preventDefault();
    }
    
    const mainContent = document.getElementById('main-content');
    const pageTitle = document.getElementById('page-title');
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Actualizar el título según la página (verificar solo el nombre del archivo)
    if (page.includes('Dashboard.php')) {
        pageTitle.textContent = 'Dashboard';
    } else if (page.includes('productos.php')) {
        pageTitle.textContent = 'Productos';
    } else if (page.includes('categoria.php')) {
        pageTitle.textContent = 'Categorías';
    } else if (page.includes('clientes.php')) {
        pageTitle.textContent = 'Clientes';
    } else if (page.includes('pedidosadmin.php')) {
        pageTitle.textContent = 'Pedidos';
    } else if (page.includes('usuarios.php')) {
        pageTitle.textContent = 'Usuarios';
    } else if (page.includes('mensajeria.php')) {
        pageTitle.textContent = 'Mensajería';
    } else if (page.includes('configuracion.php')) {
        pageTitle.textContent = 'Configuraciones';
    }
    
    // Actualizar el estado activo del sidebar (extraer solo el nombre del archivo)
    const fileName = page.split('/').pop(); // Obtener solo el nombre del archivo
    navLinks.forEach(link => {
        link.classList.remove('sidebar-active');
        link.classList.add('text-slate-400', 'hover:text-white', 'hover:bg-slate-800');
        
        const onclick = link.getAttribute('onclick');
        if (onclick && (onclick.includes(fileName) || onclick.includes(page))) {
            link.classList.remove('text-slate-400', 'hover:text-white', 'hover:bg-slate-800');
            link.classList.add('sidebar-active');
        }
    });
    
    // Cargar el contenido dinámicamente
    if (page.includes('Dashboard.php')) {
        // Mostrar el dashboard por defecto
        location.reload();
    } else {
        fetch(page)
            .then(response => response.text())
            .then(data => {
                // Crear un contenedor temporal para parsear el HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data;
                
                // Extraer estilos
                const styles = tempDiv.querySelectorAll('style');
                styles.forEach(style => {
                    document.head.appendChild(style.cloneNode(true));
                });
                
                // Obtener solo el contenido HTML (sin estilos)
                let htmlContent = data;
                htmlContent = htmlContent.replace(/<style[\s\S]*?<\/style>/g, '');
                
                // Extraer scripts para ejecutarlos después
                const scripts = [];
                const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
                let match;
                while ((match = scriptRegex.exec(data)) !== null) {
                    scripts.push(match[1]);
                }
                
                // Remover scripts del HTML
                htmlContent = htmlContent.replace(/<script[\s\S]*?<\/script>/g, '');
                
                mainContent.innerHTML = htmlContent;
                
                // Ejecutar scripts después de insertar el HTML
                scripts.forEach(scriptContent => {
                    const script = document.createElement('script');
                    script.textContent = scriptContent;
                    document.body.appendChild(script);
                });
            })
            .catch(error => {
                mainContent.innerHTML = '<div class="text-center text-red-500"><p>Error al cargar la página</p></div>';
                console.error('Error:', error);
            });
    }
}

// Funciones para el modal de productos
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

// Función para toggle de formularios en categorías y otras páginas
function toggleFormulario(type) {
    const formulario = document.getElementById('formulario-' + type);
    if (formulario) {
        formulario.classList.toggle('hidden');
    }
}

// Cargar el dashboard por defecto al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.getElementById('main-content');
    // El dashboard ya tiene contenido por defecto
});

// Listener global para cerrar modal con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFormModal();
    }
});

// Función para cerrar sesión
function cerrarSesion() {
    CustomModal.show('confirm', 'Cerrar Sesión', '¿Estás seguro que deseas cerrar sesión?', (confirmed) => {
        if (confirmed) {
            window.location.href = '../core/cerrar_sesion.php';
        }
    });
}
</script>

</body></html>