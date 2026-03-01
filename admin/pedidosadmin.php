<?php
require_once '../core/sesiones.php';
require_once '../core/conexion.php';

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    header("Location: ../index.php");
    exit();
}

// Cargar configuración de moneda
$res_cfg = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");
$cfg = ($res_cfg && mysqli_num_rows($res_cfg) > 0) ? mysqli_fetch_assoc($res_cfg) : [];
$cfg_moneda_cod = $cfg['moneda'] ?? 'HNL';
$simbolos_moneda = ['USD' => '$', 'EUR' => '€', 'MXN' => '$', 'COP' => '$', 'ARS' => '$', 'GTQ' => 'Q', 'HNL' => 'L', 'CRC' => '₡'];
$cfg_moneda = $simbolos_moneda[$cfg_moneda_cod] ?? $cfg_moneda_cod;

/* ================================
   MANEJO DE REQUESTS AJAX
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero_pedido'])) {
    include 'pedidos_contenido.php';
    exit();
}
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
<script>
    window._cfgMoneda = '<?php echo addslashes($cfg_moneda); ?>';
</script>
</head>

<body class="bg-slate-100 dark:bg-slate-900">

<main class="max-w-7xl mx-auto px-6 pb-12">

<header class="mb-8 flex justify-between items-end">
    <h2 class="text-3xl font-bold">Administración de Lista de Pedidos</h2>
</header>

<!-- FILTROS Y BÚSQUEDA -->
<div class="bg-white dark:bg-slate-800 rounded-xl p-6 mb-8 border border-slate-200 dark:border-slate-700">
    <div class="flex flex-col md:flex-row gap-4">
        
        <div class="flex-1">
            <label class="block text-sm font-semibold mb-2">Buscar por Número de Pedido</label>
            <input 
                type="text" 
                id="inputBusqueda"
                placeholder="Ej: 123"
                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white focus:outline-none focus:border-primary"
            />
        </div>

        <div class="flex-1">
            <label class="block text-sm font-semibold mb-2">Filtrar por Estado</label>
            <select 
                id="selectEstado"
                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white focus:outline-none focus:border-primary"
            >
                <option value="">-- Todos los estados --</option>
                <option value="pendiente">Pendiente</option>
                <option value="confirmado">Confirmado</option>
                <option value="enviado">Enviado</option>
                <option value="entregado">Entregado</option>
                <option value="cancelado">Cancelado</option>
            </select>
        </div>

    </div>
</div>

<div class="bg-white dark:bg-slate-800 rounded-xl overflow-hidden table-container border border-slate-200 dark:border-slate-700">

<div class="bg-primary px-6 py-4">
    <h3 class="text-white font-bold text-lg">Lista de Pedidos</h3>
</div>

<div class="overflow-x-auto" id="tablasResultados">
    <p class="text-center py-10 text-gray-500">Cargando...</p>
</div>

</div>

<script>
(function() {
    // Ejecutar inicialización inmediatamente sin check de flag
    console.log('Script pedidos cargado');

    function inicializarPedidos() {
        const inputBusqueda = document.getElementById('inputBusqueda');
        const selectEstado = document.getElementById('selectEstado');
        const tablasResultados = document.getElementById('tablasResultados');

        if (!inputBusqueda || !selectEstado || !tablasResultados) {
            console.log('Esperando elementos...');
            setTimeout(inicializarPedidos, 100);
            return;
        }

        console.log('✓ Inicializando Pedidos');

        // Cargar datos inmediatamente
        cargarResultados();

        // Event listeners
        inputBusqueda.addEventListener('input', cargarResultados);
        selectEstado.addEventListener('change', cargarResultados);
    }

    window.cargarResultados = async function(pagina = 1) {
        const inputBusqueda = document.getElementById('inputBusqueda');
        const selectEstado = document.getElementById('selectEstado');
        const tablasResultados = document.getElementById('tablasResultados');

        if (!inputBusqueda || !selectEstado || !tablasResultados) return;

        const numero_pedido = inputBusqueda.value;
        const estado_filtro = selectEstado.value;

        const formData = new FormData();
        formData.append('numero_pedido', numero_pedido);
        formData.append('estado_filtro', estado_filtro);
        formData.append('pagina', pagina);

        try {
            tablasResultados.innerHTML = '<p class="text-center py-10 text-gray-500">Cargando...</p>';
            
            const response = await fetch('pedidos_contenido.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Error en la respuesta');
            }

            const html = await response.text();
            tablasResultados.innerHTML = html;

        } catch (error) {
            console.error('Error:', error);
            tablasResultados.innerHTML = '<p class="text-center py-10 text-red-500">Error al cargar los datos</p>';
        }
    };

    window.cargarPagina = function(pagina) {
        const inputBusqueda = document.getElementById('inputBusqueda');
        const selectEstado = document.getElementById('selectEstado');

        if (!inputBusqueda || !selectEstado) return;

        const numero_pedido = inputBusqueda.value;
        const estado_filtro = selectEstado.value;

        const formData = new FormData();
        formData.append('numero_pedido', numero_pedido);
        formData.append('estado_filtro', estado_filtro);
        formData.append('pagina', pagina);

        fetch('pedidos_contenido.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(html => {
            const tablasResultados = document.getElementById('tablasResultados');
            if (tablasResultados) {
                tablasResultados.innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const tablasResultados = document.getElementById('tablasResultados');
            if (tablasResultados) {
                tablasResultados.innerHTML = '<p class="text-center py-10 text-red-500">Error al cargar los datos</p>';
            }
        });
    };

    // Intentar inicializar ahora
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarPedidos);
    } else {
        setTimeout(inicializarPedidos, 50);
    }
})();
</script>

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

    if(e.target.closest(".btn-cancelar")){
        const btn = e.target.closest(".btn-cancelar");
        const id = btn.dataset.id;

        if(confirm("¿Estás seguro de que deseas cancelar este pedido?")){
            fetch("cambiar_estado.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id=" + id + "&estado=cancelado"
            })
            .then(res => res.json())
            .then(data => {
                if(data.exito){
                    const fila = document.querySelector('button[data-id="' + id + '"]').closest('tr');
                    const badge = fila.querySelector('span');
                    
                    badge.textContent = 'Cancelado';
                    badge.className = 'inline-flex px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700';
                    
                    alert('Pedido cancelado exitosamente');
                } else {
                    alert('Error: ' + (data.error ?? 'No se pudo cancelar'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error de conexión');
            });
        }
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

           const fila = document.querySelector('button[data-id="' + pedidoActual + '"]').closest("tr");
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

<!-- MODAL VER DETALLE -->
<div id="modalDetalle" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 w-full max-w-2xl relative max-h-96 overflow-y-auto">
        <button onclick="cerrarModal()" 
        class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl">
            ✕
        </button>
        <div id="contenidoDetalle"></div>
    </div>
</div>

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

<!-- MODAL IMAGEN GRANDE -->
<div id="modalImagen"
     style="display:none;"
     class="fixed inset-0 bg-black/80 items-center justify-center z-[9999]">

    <div class="relative max-w-4xl w-full p-4 flex justify-center">

        <!-- Botón cerrar -->
        <button onclick="cerrarModalImagen()"
                class="absolute top-2 right-2 text-white text-3xl font-bold hover:text-red-400">
            ✕
        </button>

        <!-- Imagen -->
        <img id="imagenGrande"
             src=""
             class="w-full max-h-[90vh] object-contain rounded-lg shadow-lg">

    </div>
</div>

<script>
function abrirImagen(ruta){
    var modal = document.getElementById("modalImagen");
    var img = document.getElementById("imagenGrande");

    img.src = ruta;
    modal.style.display = "flex";
}

function cerrarModalImagen(){
    var modal = document.getElementById("modalImagen");
    var img = document.getElementById("imagenGrande");

    img.src = "";
    modal.style.display = "none";
}
</script>

</body>
</html>