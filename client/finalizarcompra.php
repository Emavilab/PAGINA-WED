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
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<label class="relative flex flex-col p-4 border-2 border-primary bg-primary/5 rounded-xl cursor-pointer">
<input checked="" class="absolute top-4 right-4 text-primary focus:ring-primary" name="shipping" type="radio"/>
<span class="font-bold text-slate-900 dark:text-white">Envío Estándar</span>
<span class="text-sm opacity-70 mb-2">3 - 5 días hábiles</span>
<span class="mt-auto font-bold text-primary">Gratis</span>
</label>
<label class="relative flex flex-col p-4 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:border-primary/50 transition-colors">
<input class="absolute top-4 right-4 text-primary focus:ring-primary" name="shipping" type="radio"/>
<span class="font-bold text-slate-900 dark:text-white">Envío Express</span>
<span class="text-sm opacity-70 mb-2">24 - 48 horas</span>
<span class="mt-auto font-bold">9,90 €</span>
</label>
</div>
</section>

<section class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
<div class="flex items-center gap-4 mb-6">
<span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">3</span>
<h2 class="text-xl font-bold">Método de Pago</h2>
</div>
<div class="space-y-4">
<div class="flex flex-wrap gap-2 mb-6">
<button class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-full font-medium text-sm">
<span class="material-icons text-sm">credit_card</span> Tarjeta de Crédito
</button>
<button class="flex items-center gap-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-full font-medium text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
<span class="material-icons text-sm">account_balance_wallet</span> PayPal
</button>
<button class="flex items-center gap-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-full font-medium text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
<span class="material-icons text-sm">payments</span> Contra Entrega
</button>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div class="md:col-span-2">
<label class="block text-sm font-medium mb-1.5 opacity-80">Número de Tarjeta</label>
<div class="relative">
<input class="w-full rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:border-primary focus:ring-primary pr-12" placeholder="0000 0000 0000 0000" type="text"/>
<div class="absolute right-3 top-1/2 -translate-y-1/2 flex gap-1">
<div class="w-8 h-5 bg-slate-200 dark:bg-slate-700 rounded flex items-center justify-center text-[8px] font-bold">VISA</div>
<div class="w-8 h-5 bg-slate-200 dark:bg-slate-700 rounded flex items-center justify-center text-[8px] font-bold">MC</div>
</div>
</div>
</div>
<div>
<label class="block text-sm font-medium mb-1.5 opacity-80">Fecha de Expiración</label>
<input class="w-full rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:border-primary focus:ring-primary" placeholder="MM/YY" type="text"/>
</div>
<div>
<label class="block text-sm font-medium mb-1.5 opacity-80">CVC / CVV</label>
<input class="w-full rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:border-primary focus:ring-primary" placeholder="123" type="text"/>
</div>
<div class="md:col-span-2">
<label class="block text-sm font-medium mb-1.5 opacity-80">Nombre en la Tarjeta</label>
<input class="w-full rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-800 focus:border-primary focus:ring-primary" placeholder="Como aparece en la tarjeta" type="text"/>
</div>
</div>
</div>
</section>
</div>

<aside class="lg:w-[400px]">
<div class="sticky-summary space-y-4">
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
<h2 class="text-lg font-bold mb-6">Resumen del Pedido</h2>
<div class="space-y-4 mb-6" id="orderSummary">
<div class="flex gap-4">
<div class="relative flex-shrink-0">
<img alt="Zapatillas Deportivas" class="w-16 h-16 rounded-lg object-cover border border-slate-100 dark:border-slate-800" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC2QotxqjndSB5qkZ6hi7xoXmCQ1AhcKczkQ1ArMLAFi3o2Pc8HvOehknCvnLosMNLKXSdJuYpb1ydJBqe-M2_wRZv6aFJaYy5XPLnaiHvXXeXqiUM0Z7h9p3TPeV34oSz5WbolLunyugPcZjuuV-Mm1xvFBxEgM4m_FTV5Fj8iKI-1XOlxOYPNu0O9_3BU8Eyd5m8whfdz1Y-5QBX_tpEDETOjizK_4OPsehfx76gY1pyigoSZHW7EA5CpervyDDNVCgftkQJr0yU"/>
<span class="absolute -top-2 -right-2 w-5 h-5 bg-slate-800 text-white text-[10px] font-bold rounded-full flex items-center justify-center quantity1">1</span>
</div>
<div class="flex-1">
<h4 class="text-sm font-semibold">Zapatillas Ultra Boost v2</h4>
<p class="text-xs text-slate-500">Talla: 42 | Color: Rojo</p>
<p class="text-sm font-bold mt-1">129,90 €</p>
<div class="flex items-center gap-2 mt-2 text-xs">
<button onclick="decrementQuantity(1, this)" class="px-2 py-1 bg-slate-200 dark:bg-slate-700 rounded hover:bg-primary hover:text-white">−</button>
<span class="px-2">1</span>
<button onclick="incrementQuantity(1, this)" class="px-2 py-1 bg-slate-200 dark:bg-slate-700 rounded hover:bg-primary hover:text-white">+</button>
</div>
</div>
</div>
<div class="flex gap-4">
<div class="relative flex-shrink-0">
<img alt="Smartwatch" class="w-16 h-16 rounded-lg object-cover border border-slate-100 dark:border-slate-800" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDdVuwQnq77nE2Q8JQjl4_F5fUS8wcMNBp_ptSJ1huX_zxGEZ-yg2WWFT98LtNB8ay1vSrbFxKApz4QIwz0ysORY3METn3ll4YShVwHvh0RfIsjjamk7zuNbEjFnNOIUUPgksVp91DZQt5FiBESkJKOFUBaae9WqvZXPcPogEDIqQcr997C9nxEJVfSLwcQ_jyy3t-6xzNmU8SG5Gy0edGwS2MZRtDiPJMtTakNk9gG4_psPcniKhXA0tXXy9hWZzqqvnD8rYKVKjU"/>
<span class="absolute -top-2 -right-2 w-5 h-5 bg-slate-800 text-white text-[10px] font-bold rounded-full flex items-center justify-center quantity2">1</span>
</div>
<div class="flex-1">
<h4 class="text-sm font-semibold">Smartwatch Series 5</h4>
<p class="text-xs text-slate-500">Talla: Única | Color: Blanco</p>
<p class="text-sm font-bold mt-1">89,00 €</p>
<div class="flex items-center gap-2 mt-2 text-xs">
<button onclick="decrementQuantity(2, this)" class="px-2 py-1 bg-slate-200 dark:bg-slate-700 rounded hover:bg-primary hover:text-white">−</button>
<span class="px-2">1</span>
<button onclick="incrementQuantity(2, this)" class="px-2 py-1 bg-slate-200 dark:bg-slate-700 rounded hover:bg-primary hover:text-white">+</button>
</div>
</div>
</div>
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

</script>