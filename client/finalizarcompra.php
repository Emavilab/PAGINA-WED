<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Finalizar Compra - Checkout</title>
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
<style type="text/tailwindcss">
        body {
            font-family: 'Inter', sans-serif;
        }
        .sticky-summary {
            position: sticky;
            top: 8rem;
        }
        #use-new-address:checked ~ #new-address-form {
            display: grid;
        }
        #new-address-form {
            display: none;
        }
    </style>
</head>

<main class="max-w-7xl mx-auto px-4 pb-20">
<div class="flex flex-col lg:flex-row gap-8">
<div class="flex-1 space-y-6">
<section class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
<div class="flex items-center gap-4 mb-6">
<span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">1</span>
<h2 class="text-xl font-bold">Información de Envío</h2>
</div>
<div class="mb-8">
<label class="block text-sm font-semibold mb-4 text-slate-600 dark:text-slate-400 uppercase tracking-wider">Seleccionar dirección guardada</label>
<div id="checkout-direcciones" class="grid grid-cols-1 md:grid-cols-2 gap-4">
</div>
</section>

<section class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
<div class="flex items-center gap-4 mb-6">
<span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">2</span>
<h2 class="text-xl font-bold">Método de Envío</h2>
</div>
<div id="checkout-metodos-envio" class="grid grid-cols-1 md:grid-cols-2 gap-4">
</div>
</section>

<section class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
<div class="flex items-center gap-4 mb-6">
<span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">3</span>
<h2 class="text-xl font-bold">Método de Pago</h2>
</div>
<div class="space-y-4">
<div id="checkout-metodos-pago" class="flex flex-wrap gap-2 mb-6"></div>
</section>
</div>

<aside class="lg:w-[400px]">
<div class="sticky-summary space-y-4">
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
<h2 class="text-lg font-bold mb-6">Resumen del Pedido</h2>
<div class="space-y-4 mb-6" id="orderSummary">

</div>
<hr class="border-slate-100 dark:border-slate-800 mb-6"/>
<div class="space-y-3 mb-6">
<div class="flex justify-between text-sm">
<span class="opacity-70">Subtotal</span>
<span id="subtotal">218,90 €</span>
</div>
<div class="flex justify-between text-sm">
<span class="opacity-70">Envío</span>
<span class="text-emerald-500 font-medium" id="shippingCost">Gratis</span>
</div>
<div class="flex justify-between text-sm">
<span class="opacity-70">Impuestos (IVA 21%)</span>
<span id="taxes">45,97 €</span>
</div>
<div class="flex justify-between text-lg font-bold border-t border-slate-100 dark:border-slate-800 pt-4 mt-4">
<span>Total Final</span>
<span class="text-primary text-2xl font-black" id="totalPrice">264,87 €</span>
</div>
</div>
<button class="w-full bg-primary hover:bg-primary/90 text-white py-4 rounded-xl font-bold text-lg shadow-lg shadow-primary/25 transition-all flex items-center justify-center gap-2" onclick="confirmarPago()">
<span class="material-icons">verified_user</span>
Confirmar y Pagar
</button>
<div class="mt-6 flex flex-col items-center gap-3">
<div class="flex gap-4 items-center opacity-50">
<span class="material-icons">local_shipping</span>
<span class="material-icons">history</span>
<span class="material-icons">security</span>
</div>
<p class="text-[11px] text-center opacity-60 leading-relaxed px-4">
Al confirmar el pedido, aceptas nuestras Políticas de Privacidad y Términos de Servicio. Tus datos están protegidos por encriptación de 256 bits.
</p>
</div>
</div>
<div class="bg-primary/5 border border-primary/20 p-4 rounded-xl flex items-start gap-3">
<span class="material-icons text-primary">redeem</span>
<div>
<h4 class="text-xs font-bold uppercase tracking-wider text-primary">Promoción Aplicada</h4>
<p class="text-xs opacity-80">Has obtenido envío gratuito por una compra superior a 150 €.</p>
</div>
</div>
</div>
</aside>
</div>
</main>
<script>

/* ===============================
   INICIALIZADOR PARA SPA
================================= */
function initCheckout() {
    console.log("Inicializando checkout...");
    cargarDireccionesCheckout();
    cargarMetodosEnvio(); 
    cargarResumenPedido();
    cargarMetodosPago();
}


/* ===============================
   CARGAR DIRECCIONES
================================= */
async function cargarDireccionesCheckout() {

    console.log("Cargando direcciones checkout...");

    const contenedor = document.getElementById("checkout-direcciones");
    if (!contenedor) return;

    try {

        const response = await fetch("api/api_obtener_direcciones.php", {
            credentials: "include"
        });

        const data = await response.json();

        if (!data.success || !data.direcciones.length) {
            contenedor.innerHTML = "<p class='text-sm text-slate-500'>No tienes direcciones guardadas.</p>";
            return;
        }

        contenedor.innerHTML = "";

        data.direcciones.forEach((dir, index) => {

            contenedor.innerHTML += `
                <label class="relative group">
                    <input 
                        class="peer hidden" 
                        name="saved_address" 
                        type="radio" 
                        value="${dir.id_direccion}"
                        ${index === 0 ? "checked" : ""}
                    />

                    <div class="h-full p-4 rounded-xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 cursor-pointer transition-all peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50">
                        
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">location_on</span>
                                <span class="font-bold text-slate-900 dark:text-white">
                                    Dirección
                                </span>
                            </div>

                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 dark:border-slate-700 flex items-center justify-center peer-checked:border-primary">
                                <div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform peer-checked:scale-100"></div>
                            </div>
                        </div>

                        <p class="text-sm opacity-70 leading-relaxed">
                            ${dir.direccion}<br/>
                            ${dir.ciudad}
                        </p>

                        <p class="text-xs mt-2 font-medium">
                            ${dir.telefono || "-"}
                        </p>

                    </div>
                </label>
            `;
        });

    } catch (error) {
        console.error("Error cargando direcciones checkout:", error);
    }
}


/* ===============================
   OBTENER DIRECCIÓN SELECCIONADA
================================= */
function obtenerDireccionSeleccionada() {
    const seleccionada = document.querySelector('input[name="saved_address"]:checked');
    return seleccionada ? seleccionada.value : null;
}


/* ===============================
   CONFIRMAR PAGO
================================= */
function confirmarPago() {

    const direccionId = obtenerDireccionSeleccionada();

    if (!direccionId) {
        alert("Selecciona una dirección antes de continuar.");
        return;
    }

    console.log("Dirección seleccionada:", direccionId);

    // 🔥 Aquí luego haremos el fetch para crear pedido
    // Ejemplo futuro:
    /*
    fetch("api/api_crear_pedido.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({
            id_direccion: direccionId
        })
    });
    */
}
let envioSeleccionado = 0;

async function cargarMetodosEnvio() {

    const contenedor = document.getElementById("checkout-metodos-envio");
    if (!contenedor) return;

    try {

        const response = await fetch("api/api_metodos_envio.php");
        const data = await response.json();

        if (!data.success || !data.metodos.length) {
            contenedor.innerHTML = "<p>No hay métodos disponibles</p>";
            return;
        }

        contenedor.innerHTML = "";

        data.metodos.forEach((metodo, index) => {

            const checked = index === 0 ? "checked" : "";
            const border = index === 0 ? 
                "border-2 border-primary bg-primary/5" : 
                "border border-slate-200 dark:border-slate-700";

            if(index === 0) {
                envioSeleccionado = parseFloat(metodo.costo);
            }

            contenedor.innerHTML += `
                <label class="relative flex flex-col p-4 ${border} rounded-xl cursor-pointer hover:border-primary/50 transition-colors">
                    
                    <input 
                        type="radio" 
                        name="shipping" 
                        value="${metodo.id_envio}" 
                        ${checked}
                        class="absolute top-4 right-4 text-primary focus:ring-primary"
                        onclick="seleccionarEnvio(${metodo.costo})"
                    />

                    <span class="font-bold text-slate-900 dark:text-white">
                        ${metodo.nombre}
                    </span>

                    <span class="text-sm opacity-70 mb-2">
                        ${metodo.tiempo_estimado || ""}
                    </span>

                    <span class="mt-auto font-bold ${metodo.costo == 0 ? 'text-emerald-500' : ''}">
                        ${metodo.costo == 0 ? 'Gratis' : metodo.costo + ' €'}
                    </span>

                </label>
            `;
        });

        cargarResumenPedido();

    } catch (error) {
        console.error("Error cargando métodos envío:", error);
    }
}
function seleccionarEnvio(costo) {
    envioSeleccionado = parseFloat(costo);
    cargarResumenPedido(); // recalcula todo correctamente
}
function actualizarTotalConEnvio(subtotal, impuestos) {

    const total = subtotal + impuestos + envioSeleccionado;

    document.getElementById("shippingCost").innerText =
        envioSeleccionado === 0 ? "Gratis" : envioSeleccionado.toFixed(2) + " €";

    document.getElementById("totalPrice").innerText =
        total.toFixed(2) + " €";
}

/* ===============================
   CARGAR RESUMEN DEL PEDIDO
================================= */
async function cargarResumenPedido() {

    const contenedor = document.getElementById("orderSummary");
    if (!contenedor) return;

    try {

        const response = await fetch("api/api_carrito.php?accion=listar", {
            credentials: "include"
        });

        const data = await response.json();

        if (!data.exito || !data.carrito.items.length) {
            contenedor.innerHTML = "<p class='text-sm text-slate-500'>Tu carrito está vacío.</p>";
            return;
        }

        const carrito = data.carrito;

        contenedor.innerHTML = "";

        carrito.items.forEach(item => {

            contenedor.innerHTML += `
                <div class="flex gap-4">
                    <div class="relative flex-shrink-0">
                        <img src="${item.imagen}" 
                             class="w-16 h-16 rounded-lg object-cover border border-slate-100 dark:border-slate-800">
                        <span class="absolute -top-2 -right-2 w-5 h-5 bg-slate-800 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                            ${item.cantidad}
                        </span>
                    </div>

                    <div class="flex-1">
                        <h4 class="text-sm font-semibold">${item.nombre}</h4>
                        <p class="text-sm font-bold mt-1">${item.precio_unitario.toFixed(2)} €</p>

                        <div class="flex items-center gap-2 mt-2 text-xs">
                            <button onclick="cambiarCantidad(${item.id_carrito_detalle}, ${item.cantidad - 1})"
                                class="px-2 py-1 bg-slate-200 dark:bg-slate-700 rounded hover:bg-primary hover:text-white">
                                −
                            </button>

                            <span class="px-2">${item.cantidad}</span>

                            <button onclick="cambiarCantidad(${item.id_carrito_detalle}, ${item.cantidad + 1})"
                                class="px-2 py-1 bg-slate-200 dark:bg-slate-700 rounded hover:bg-primary hover:text-white">
                                +
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        //  ACTUALIZAR TOTALES DESDE BACKEND
document.getElementById("subtotal").innerText =
    carrito.subtotal.toFixed(2) + " €";

document.getElementById("taxes").innerText =
    carrito.impuesto_total.toFixed(2) + " €";

// Recalcular total con envío seleccionado
actualizarTotalConEnvio(carrito.subtotal, carrito.impuesto_total);
        //  Actualizar totales reales desde backend
        document.getElementById("subtotal").innerText =
            carrito.subtotal.toFixed(2) + " €";

        document.getElementById("taxes").innerText =
            carrito.impuesto_total.toFixed(2) + " €";

        actualizarTotalConEnvio(carrito.subtotal, carrito.impuesto_total);

    } catch (error) {
        console.error("Error cargando resumen:", error);
    }
}
/* ===============================
   CAMBIAR CANTIDAD
================================= */
async function cambiarCantidad(idCarritoDetalle, nuevaCantidad) {

    const formData = new FormData();
    formData.append("id_carrito_detalle", idCarritoDetalle);
    formData.append("cantidad", nuevaCantidad);

    await fetch("api/api_carrito.php?accion=actualizar", {
        method: "POST",
        body: formData,
        credentials: "include"
    });

    cargarResumenPedido();
}
let metodoPagoSeleccionado = null;

async function cargarMetodosPago() {

    const contenedor = document.getElementById("checkout-metodos-pago");
    if (!contenedor) return;

    try {

        const response = await fetch("api/api_metodos_pago.php");
        const data = await response.json();

        if (!data.exito || !data.metodos.length) {
            contenedor.innerHTML = "<p>No hay métodos de pago disponibles</p>";
            return;
        }

        contenedor.innerHTML = "";

        data.metodos.forEach((metodo, index) => {

            const activo = index === 0;
            if (activo) metodoPagoSeleccionado = metodo.id_metodo_pago;

            contenedor.innerHTML += `
                <button 
                    onclick="seleccionarMetodoPago(${metodo.id_metodo_pago}, this)"
                    class="flex items-center gap-2 px-4 py-2 
                    ${activo 
                        ? 'bg-primary text-white' 
                        : 'border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'} 
                    rounded-full font-medium text-sm transition-all">

                    <span class="material-icons text-sm">payments</span>
                    ${metodo.nombre}
                </button>
            `;
        });

    } catch (error) {
        console.error("Error cargando métodos pago:", error);
    }
}
function seleccionarMetodoPago(idMetodo, boton) {

    metodoPagoSeleccionado = idMetodo;

    // Quitar estilos activos
    const botones = document.querySelectorAll("#checkout-metodos-pago button");
    botones.forEach(btn => {
        btn.classList.remove("bg-primary", "text-white");
        btn.classList.add("border", "border-slate-200", "dark:border-slate-700");
    });

    // Activar el seleccionado
    boton.classList.remove("border", "border-slate-200", "dark:border-slate-700");
    boton.classList.add("bg-primary", "text-white");
}
</script>