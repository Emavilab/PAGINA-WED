<?php
require_once '../core/conexion.php';

// Cargar configuración general de colores
$res_cfg_login = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");
$cfg_login = ($res_cfg_login && mysqli_num_rows($res_cfg_login) > 0) ? mysqli_fetch_assoc($res_cfg_login) : [];

function normalizar_color_login($valor, $defecto) {
    if (!is_string($valor)) return $defecto;
    $valor = trim($valor);
    if ($valor === '') return $defecto;
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $valor)) return $defecto;
    return strtoupper($valor);
}

$login_primary = normalizar_color_login($cfg_login['color_primary'] ?? '#137fec', '#137FEC');
$login_bg_light = normalizar_color_login($cfg_login['color_background_light'] ?? '#f6f7f8', '#F6F7F8');
$login_bg_dark = normalizar_color_login($cfg_login['color_background_dark'] ?? '#15191d', '#15191D');

// Configuración de moneda
$cfg_moneda_cod = $cfg_login['moneda'] ?? 'HNL';
$simbolos_moneda = ['USD' => '$', 'EUR' => '€', 'MXN' => '$', 'COP' => '$', 'ARS' => '$', 'GTQ' => 'Q', 'HNL' => 'L', 'CRC' => '₡'];
$cfg_moneda = $simbolos_moneda[$cfg_moneda_cod] ?? $cfg_moneda_cod;
?>
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
                        primary: "<?php echo $login_primary; ?>",
                        "background-light": "<?php echo $login_bg_light; ?>",
                        "background-dark": "<?php echo $login_bg_dark; ?>"
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
        .new-address-form-wrapper {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            pointer-events: none;
            transition: max-height 0.35s ease-out, opacity 0.3s ease-out;
        }
        .new-address-form-wrapper.new-address-form-visible {
            max-height: 640px;
            opacity: 1;
            pointer-events: auto;
        }
    </style>
<script>
    window._cfgMoneda = '<?php echo addslashes($cfg_moneda); ?>';
</script>
</head>

<body class="bg-white dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display transition-colors duration-300 relative min-h-screen">
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

<div class="mt-6">
    <input type="checkbox" id="use-new-address" name="use-new-address" class="sr-only peer" aria-label="Agregar nueva dirección">
    <label for="use-new-address" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary cursor-pointer hover:opacity-80 transition-opacity duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary rounded">
        <span aria-hidden="true">+</span>
        Agregar nueva dirección
    </label>
</div>

<div id="new-address-form-wrapper" class="new-address-form-wrapper mt-6">
<form id="new-address-form" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 transition-colors duration-200">
    <div class="md:col-span-2">
        <label for="new-direccion" class="block text-sm font-semibold mb-1 text-slate-700 dark:text-slate-300">Dirección <span class="text-red-500">*</span></label>
        <input type="text" id="new-direccion" name="direccion" required maxlength="255" placeholder="Calle, número, colonia"
            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary">
        <p id="err-new-direccion" class="mt-1 text-xs text-red-500 hidden"></p>
    </div>
    <div>
        <label for="new-ciudad" class="block text-sm font-semibold mb-1 text-slate-700 dark:text-slate-300">Ciudad <span class="text-red-500">*</span></label>
        <input type="text" id="new-ciudad" name="ciudad" required maxlength="100" placeholder="Ciudad"
            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary">
        <p id="err-new-ciudad" class="mt-1 text-xs text-red-500 hidden"></p>
    </div>
    <div>
        <label for="new-id_departamento" class="block text-sm font-semibold mb-1 text-slate-700 dark:text-slate-300">Departamento <span class="text-red-500">*</span></label>
        <select name="id_departamento" id="new-id_departamento" required
            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary">
            <option value="">Seleccione departamento</option>
        </select>
        <p id="err-new-departamento" class="mt-1 text-xs text-red-500 hidden"></p>
    </div>
    <div>
        <label for="new-codigo_postal" class="block text-sm font-semibold mb-1 text-slate-700 dark:text-slate-300">Código postal</label>
        <input type="text" id="new-codigo_postal" name="codigo_postal" maxlength="20" placeholder="Opcional"
            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
    <div>
        <label for="new-telefono" class="block text-sm font-semibold mb-1 text-slate-700 dark:text-slate-300">Teléfono</label>
        <input type="text" id="new-telefono" name="telefono" maxlength="20" placeholder="Opcional"
            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary">
        <p id="err-new-telefono" class="mt-1 text-xs text-red-500 hidden"></p>
    </div>
    <div class="md:col-span-2">
        <label for="new-referencia" class="block text-sm font-semibold mb-1 text-slate-700 dark:text-slate-300">Referencia</label>
        <input type="text" id="new-referencia" name="referencia" maxlength="255" placeholder="Ej. Casa blanca, 2 cuadras al norte"
            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
    <div id="new-address-msg" class="md:col-span-2 hidden text-sm rounded-lg px-3 py-2"></div>
    <div class="md:col-span-2 flex justify-end">
        <button type="submit" id="btn-guardar-direccion" class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
            <span class="material-icons text-lg">save</span>
            Guardar dirección
        </button>
    </div>
</form>
</div>
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
<div id="contenedor-comprobante" class="mt-4 hidden">
    <label class="block text-sm font-medium mb-2">
        Subir comprobante de transferencia
    </label>
    <input 
        type="file" 
        id="input-comprobante"
        accept="image/png, image/jpeg, image/jpg, image/webp"
        class="w-full rounded-lg border border-slate-300 p-2"
    />
    <p class="text-xs text-slate-500 mt-1">
        Solo imágenes (JPG, PNG, WEBP). Máximo 3MB.
    </p>
</div>
</section>
</div>

<aside class="lg:w-[400px]">
<div class="sticky-summary space-y-4">
<!-- RESUMEN DE DIRECCIÓN SELECCIONADA -->
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
<h2 class="text-lg font-bold mb-4">📍 Dirección de Envío</h2>
<div id="direccionDisplay" class="text-sm space-y-2 p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
    <p class="text-slate-500">Selecciona una dirección arriba para ver los detalles</p>
</div>
</div>

<div class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
<h2 class="text-lg font-bold mb-6">Resumen del Pedido</h2>
<div class="space-y-4 mb-6" id="orderSummary">

</div>
<hr class="border-slate-100 dark:border-slate-800 mb-6"/>
<div class="space-y-3 mb-6">
<div class="flex justify-between text-sm">
<span class="opacity-70">Subtotal</span>
<span><span id="subtotal">218,90</span> <span id="monedaSubtotal" class="text-slate-600 dark:text-slate-400">L</span></span>
</div>
<div class="flex justify-between text-sm">
<span class="opacity-70" id="shippingDepartmentLabel">
Envío a tu departamento
</span>
<span id="shippingDepartment">L 0.00</span>
</div>

<div class="flex justify-between text-sm">
<span class="opacity-70">Envío adicional</span>
<span id="shippingExtra">L 0.00</span>
</div>
<div class="flex justify-between text-sm">
<span class="opacity-70">Impuestos</span>
<span><span id="taxes">45,97</span> <span id="monedaTaxes" class="text-slate-600 dark:text-slate-400">L</span></span>
</div>
<div class="flex justify-between text-lg font-bold border-t border-slate-100 dark:border-slate-800 pt-4 mt-4">
<span>Total Final</span>
<span class="text-primary text-2xl font-black"><span id="totalPrice">264,87</span> <span id="monedaTotal" class="text-primary">L</span></span>
</div>
</div>

<div class="mb-6 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 flex items-start gap-3">
    <span class="material-icons text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5">info</span>
    <p class="text-sm text-amber-800 dark:text-amber-200">
        Tienes hasta 3 horas después de realizar tu pedido para cancelarlo desde la sección <strong>Mis Pedidos</strong>.
    </p>
</div>

<button class="w-full bg-primary hover:bg-primary/90 text-white py-4 rounded-xl font-bold text-lg shadow-lg shadow-primary/25 transition-all flex items-center justify-center gap-2" onclick="confirmarPedido()">
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
    
    // Actualizar símbolos de moneda
    const moneda = window._cfgMoneda || 'L';
    const elementos = document.querySelectorAll('#monedaSubtotal, #monedaTaxes, #monedaTotal');
    elementos.forEach(el => el.textContent = moneda);
    
    cargarDepartamentosCheckout();
    cargarMetodosEnvio();
    cargarDireccionesCheckout();
    cargarResumenPedido();
    cargarMetodosPago();
    initNuevaDireccionToggle();
    bindNewAddressForm();
}


/* ===============================
   CARGAR DEPARTAMENTOS (SELECT)
================================= */
async function cargarDepartamentosCheckout() {
    const select = document.getElementById("new-id_departamento");
    if (!select) return;
    try {
        const response = await fetch("api/api_obtener_departamentos.php", { credentials: "include" });
        const data = await response.json();
        if (!data.success || !data.departamentos || !data.departamentos.length) return;
        select.innerHTML = '<option value="">Seleccione departamento</option>';
        data.departamentos.forEach(function (dep) {
            const opt = document.createElement("option");
            opt.value = dep.id_departamento;
            opt.textContent = dep.nombre_departamento;
            select.appendChild(opt);
        });
    } catch (err) {
        console.error("Error cargando departamentos:", err);
    }
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
            const costoEnvio = dir.costo_envio != null ? parseFloat(dir.costo_envio) : 0;
            const nombreDep = (dir.nombre_departamento || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
            const ref = (dir.referencia || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
            const direccionEsc = (dir.direccion || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
            const ciudadEsc = (dir.ciudad || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
            const cpEsc = (dir.codigo_postal || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
            const telEsc = (dir.telefono || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');

            contenedor.innerHTML += `
                <label class="relative group" onclick="mostrarDireccionSeleccionada('${direccionEsc}', '${ciudadEsc}', '${cpEsc}', '${telEsc}', '${ref}', '${nombreDep}', ${costoEnvio})">  
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
                            ${dir.ciudad}${dir.nombre_departamento ? ', ' + dir.nombre_departamento : ''}
                        </p>

                        <p class="text-xs mt-2 font-medium">
                            ${dir.telefono || "-"}
                        </p>

                    </div>
                </label>
            `;
        });

        // Mostrar la primera dirección por defecto
        if (data.direcciones.length > 0) {
            const primera = data.direcciones[0];
            const costoEnvio = primera.costo_envio != null ? parseFloat(primera.costo_envio) : 0;
            mostrarDireccionSeleccionada(
                primera.direccion, primera.ciudad, primera.codigo_postal || '', primera.telefono || '',
                primera.referencia || '', primera.nombre_departamento || '', costoEnvio
            );
        }

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
   NUEVA DIRECCIÓN: TOGGLE (MOSTRAR/OCULTAR)
================================= */
function showNewAddressForm() {
    const wrapper = document.getElementById('new-address-form-wrapper');
    const checkbox = document.getElementById('use-new-address');
    if (wrapper) wrapper.classList.add('new-address-form-visible');
    if (checkbox) checkbox.checked = true;
    const radios = document.querySelectorAll('input[name="saved_address"]');
    radios.forEach(r => { r.checked = false; });
}

function hideNewAddressForm() {
    const wrapper = document.getElementById('new-address-form-wrapper');
    const checkbox = document.getElementById('use-new-address');
    const msgEl = document.getElementById('new-address-msg');
    if (wrapper) wrapper.classList.remove('new-address-form-visible');
    if (checkbox) checkbox.checked = false;
    if (msgEl) { msgEl.classList.add('hidden'); msgEl.textContent = ''; }
}

function initNuevaDireccionToggle() {
    const checkbox = document.getElementById('use-new-address');
    const wrapper = document.getElementById('new-address-form-wrapper');
    if (!checkbox || !wrapper) return;

    // Estado inicial: formulario oculto
    wrapper.classList.remove('new-address-form-visible');

    checkbox.addEventListener('change', function () {
        if (this.checked) {
            wrapper.classList.add('new-address-form-visible');
            const radios = document.querySelectorAll('input[name="saved_address"]');
            radios.forEach(r => { r.checked = false; });
        } else {
            wrapper.classList.remove('new-address-form-visible');
        }
    });
}

/* ===============================
   NUEVA DIRECCIÓN: VALIDACIÓN Y GUARDAR
================================= */
function validarTelefono(tel) {
    if (!tel || !tel.trim()) return true;
    return /^[\d\s\-\+\(\)]{8,20}$/.test(tel.trim());
}

function validarFormNuevaDireccion() {
    const direccion = (document.getElementById('new-direccion')?.value || '').trim();
    const ciudad = (document.getElementById('new-ciudad')?.value || '').trim();
    const telefono = (document.getElementById('new-telefono')?.value || '').trim();
    const idDepartamento = (document.getElementById('new-id_departamento')?.value || '').trim();

    const errDir = document.getElementById('err-new-direccion');
    const errCiudad = document.getElementById('err-new-ciudad');
    const errTel = document.getElementById('err-new-telefono');
    const errDep = document.getElementById('err-new-departamento');

    errDir.classList.add('hidden');
    errDir.textContent = '';
    errCiudad.classList.add('hidden');
    errCiudad.textContent = '';
    errTel.classList.add('hidden');
    errTel.textContent = '';
    if (errDep) { errDep.classList.add('hidden'); errDep.textContent = ''; }

    let valido = true;

    if (!direccion) {
        errDir.textContent = 'La dirección es obligatoria.';
        errDir.classList.remove('hidden');
        valido = false;
    }
    if (!ciudad) {
        errCiudad.textContent = 'La ciudad es obligatoria.';
        errCiudad.classList.remove('hidden');
        valido = false;
    }
    if (!idDepartamento || parseInt(idDepartamento, 10) <= 0) {
        if (errDep) {
            errDep.textContent = 'El departamento es obligatorio.';
            errDep.classList.remove('hidden');
        }
        valido = false;
    }
    if (!validarTelefono(telefono)) {
        errTel.textContent = 'Teléfono no válido (8-20 dígitos o símbolos + - ( )).';
        errTel.classList.remove('hidden');
        valido = false;
    }

    return valido;
}

async function guardarNuevaDireccion(e) {
    e.preventDefault();

    const msgEl = document.getElementById('new-address-msg');
    const btn = document.getElementById('btn-guardar-direccion');
    msgEl.classList.add('hidden');
    msgEl.textContent = '';

    if (!validarFormNuevaDireccion()) return;

    const form = document.getElementById('new-address-form');
    const formData = new FormData(form);

    btn.disabled = true;
    btn.innerHTML = '<span class="material-icons text-lg animate-spin">hourglass_empty</span> Guardando...';

    try {
        const response = await fetch('api/api_crear_direccion.php', {
            method: 'POST',
            credentials: 'include',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            msgEl.textContent = data.message || 'Dirección guardada correctamente.';
            msgEl.className = 'md:col-span-2 text-sm rounded-lg px-3 py-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300';
            msgEl.classList.remove('hidden');

            form.reset();
            hideNewAddressForm();
            await cargarDireccionesCheckout();
        } else {
            msgEl.textContent = data.message || 'Error al guardar la dirección.';
            msgEl.className = 'md:col-span-2 text-sm rounded-lg px-3 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300';
            msgEl.classList.remove('hidden');
        }
    } catch (err) {
        console.error('Error guardar nueva dirección:', err);
        msgEl.textContent = 'Error de conexión. Intenta de nuevo.';
        msgEl.className = 'md:col-span-2 text-sm rounded-lg px-3 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300';
        msgEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-icons text-lg">save</span> Guardar dirección';
    }
}

function bindNewAddressForm() {
    const form = document.getElementById('new-address-form');
    if (form) form.addEventListener('submit', guardarNuevaDireccion);
}

/* ===============================
   MOSTRAR DIRECCIÓN EN RESUMEN
================================= */
function mostrarDireccionSeleccionada(direccion, ciudad, codigoPostal, telefono, referencia, nombreDepartamento, costoEnvio) {
    const display = document.getElementById('direccionDisplay');
    if (!display) return;

    if (typeof costoEnvio === 'number' && !isNaN(costoEnvio)) {
       envioDepartamento = costoEnvio;

        // actualizar etiqueta con nombre del departamento
        const label = document.getElementById("shippingDepartmentLabel");
        if(label){
            label.innerText = "Envío a tu departamento (" + nombreDepartamento + ")";
        }

        cargarResumenPedido();
    }

    display.innerHTML = `
        <div class="space-y-2">
            <div>
                <p class="text-xs text-slate-500">Referencia</p>
                <p class="font-semibold text-slate-900 dark:text-white">${referencia || 'No especificada'}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Dirección</p>
                <p class="font-semibold text-slate-900 dark:text-white">${direccion}</p>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <p class="text-xs text-slate-500">Ciudad</p>
                    <p class="font-semibold text-slate-900 dark:text-white">${ciudad}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Departamento</p>
                    <p class="font-semibold text-slate-900 dark:text-white">${nombreDepartamento || '-'}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Código Postal</p>
                    <p class="font-semibold text-slate-900 dark:text-white">${codigoPostal || '-'}</p>
                </div>
            </div>
            <div>
                <p class="text-xs text-slate-500">Teléfono</p>
                <p class="font-semibold text-slate-900 dark:text-white">${telefono || '-'}</p>
            </div>
        </div>
    `;
}

function obtenerEnvioSeleccionado() {
    const seleccionado = document.querySelector('input[name="shipping"]:checked');
    return seleccionado ? seleccionado.value : null;
}

/* ===============================
   CONFIRMAR PAGO
================================= */
async function confirmarPedido() {

    console.log('Iniciando validaciones de pedido...');

    // ✓ 1. Validar que haya carrito
    const resumenPedido = document.getElementById('orderSummary');
    if (!resumenPedido || resumenPedido.innerHTML.trim() === '' || resumenPedido.textContent.includes('vacío')) {
        alert("⚠️ Tu carrito está vacío. Agrega productos antes de continuar.");
        return;
    }

    // ✓ 2. Validar dirección
    const direccionId = obtenerDireccionSeleccionada();
    if (!direccionId) {
        alert("⚠️ Debes seleccionar una dirección de envío.");
        return;
    }

    const direccionDisplay = document.getElementById('direccionDisplay');
    if (!direccionDisplay || direccionDisplay.textContent.includes('Selecciona una dirección')) {
        alert("⚠️ Debes seleccionar una dirección válida.");
        return;
    }

    // ✓ 3. Validar método de envío
    const envioId = obtenerEnvioSeleccionado();
    if (!envioId) {
        alert("⚠️ Debes seleccionar un método de envío.");
        return;
    }

    // ✓ 4. Validar método de pago
    const metodoPagoId = metodoPagoSeleccionado;
    if (!metodoPagoId) {
        alert("⚠️ Debes seleccionar un método de pago.");
        return;
    }

    // ✓ 5. Validar totales sean positivos
    const subtotalEl = document.getElementById('subtotal');
    const totalEl = document.getElementById('totalPrice');
    
    if (!subtotalEl || !totalEl) {
        alert("⚠️ Error al calcular los totales. Recarga la página.");
        return;
    }

    const subtotal = parseFloat(subtotalEl.textContent.replace(/[^0-9.]/g, ''));
    const total = parseFloat(totalEl.textContent.replace(/[^0-9.]/g, ''));

    if (isNaN(subtotal) || isNaN(total) || subtotal <= 0 || total <= 0) {
        alert("⚠️ Los montos del pedido no son válidos. Recarga la página.");
        return;
    }

    // ✓ 6. Si es transferencia, validar comprobante
    const inputComprobante = document.getElementById("input-comprobante");
    if (metodoPagoId == 18) {

        if (!inputComprobante || inputComprobante.files.length === 0) {
            alert("⚠️ Debes subir el comprobante de transferencia.");
            return;
        }

        const file = inputComprobante.files[0];

        // Validar tamaño
        if (file.size > 3 * 1024 * 1024) {
            alert("⚠️ El comprobante no puede superar los 3MB. Archivo actual: " + (file.size / (1024 * 1024)).toFixed(2) + "MB");
            return;
        }

        // Validar tipo
        const tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];
        if (!tiposPermitidos.includes(file.type)) {
            alert("⚠️ Formato de imagen no permitido. Solo JPG, PNG o WEBP.");
            return;
        }
    }

    // ✓ Todas las validaciones pasaron - Crear pedido
    console.log('✓ Validaciones completadas. Creando pedido...');

    const formData = new FormData();
    formData.append("id_direccion", direccionId);
    formData.append("id_envio", envioId);
    formData.append("id_metodo_pago", metodoPagoId);

    if (inputComprobante && inputComprobante.files.length > 0) {
        formData.append("comprobante", inputComprobante.files[0]);
    }

    try {

        const response = await fetch("api/api_crear_pedido.php", {
            method: "POST",
            credentials: "include",
            body: formData
        });

        const data = await response.json();

        if (data.exito) {

            alert("✓ Pedido creado correctamente");

            setTimeout(() => {
                loadHistorialPedidos();
            }, 1500);

        } else {
            alert("❌ Error al crear pedido: " + (data.error || "Intenta de nuevo"));
            console.error('Error del servidor:', data);
        }

    } catch (error) {
        console.error(error);
        alert("❌ Error de conexión. Verifica tu conexión a internet.");
    }
}
let envioDepartamento = 0;
let envioMetodo = 0;

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
                 envioMetodo = parseFloat(metodo.costo);
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
                        ${metodo.costo == 0 ? 'Gratis' : window._cfgMoneda + ' ' + metodo.costo}
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
    envioMetodo = parseFloat(costo);
    cargarResumenPedido(); // recalcula todo correctamente
}
function actualizarTotalConEnvio(subtotal, impuestos) {

    const total = subtotal + impuestos + envioDepartamento + envioMetodo;

   document.getElementById("shippingDepartment").innerText =
    window._cfgMoneda + ' ' + envioDepartamento.toFixed(2);

document.getElementById("shippingExtra").innerText =
    envioMetodo === 0 ? "Gratis" : window._cfgMoneda + ' ' + envioMetodo.toFixed(2);

    document.getElementById("totalPrice").innerText =
        window._cfgMoneda + ' ' + total.toFixed(2);
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
                        <p class="text-sm font-bold mt-1">${window._cfgMoneda} ${item.precio_unitario.toFixed(2)}</p>

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
    window._cfgMoneda + ' ' + carrito.subtotal.toFixed(2);

document.getElementById("taxes").innerText =
    window._cfgMoneda + ' ' + carrito.impuesto_total.toFixed(2);

// Recalcular total con envío seleccionado
actualizarTotalConEnvio(carrito.subtotal, carrito.impuesto_total);
        //  Actualizar totales reales desde backend
        document.getElementById("subtotal").innerText =
            window._cfgMoneda + ' ' + carrito.subtotal.toFixed(2);

        document.getElementById("taxes").innerText =
            window._cfgMoneda + ' ' + carrito.impuesto_total.toFixed(2);

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

const contenedor = document.getElementById("contenedor-comprobante");
const input = document.getElementById("input-comprobante");

if (idMetodo == 20) {
    contenedor.classList.remove("hidden");
} else {
    contenedor.classList.add("hidden");

    // Limpia el comprobante si cambia de método
    if (input) {
        input.value = "";
    }
}
}
/* ===============================
   INICIALIZAR AL CARGAR
================================= */
(function() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCheckout);
    } else {
        setTimeout(initCheckout, 100);
    }
})();
</script>
</main>
</body>
</html>
</script>