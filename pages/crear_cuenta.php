<?php
require_once '../core/conexion.php';
require_once '../core/csrf.php';

$csrfToken = obtenerTokenCSRF();

// Cargar configuración general de colores
$res_cfg_reg = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");
$cfg_reg = ($res_cfg_reg && mysqli_num_rows($res_cfg_reg) > 0) ? mysqli_fetch_assoc($res_cfg_reg) : [];

function normalizar_color_registro($valor, $defecto) {
    if (!is_string($valor)) return $defecto;
    $valor = trim($valor);
    if ($valor === '') return $defecto;
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $valor)) return $defecto;
    return strtoupper($valor);
}

$reg_primary = normalizar_color_registro($cfg_reg['color_primary'] ?? '#135bec', '#135BEC');
$reg_bg_light = normalizar_color_registro($cfg_reg['color_background_light'] ?? '#f6f6f8', '#F6F6F8');
$reg_bg_dark = normalizar_color_registro($cfg_reg['color_background_dark'] ?? '#101622', '#101622');
?>
<!DOCTYPE html>

<html lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Crear Cuenta - Registro de Cliente</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              colors: {
                "primary": "<?php echo $reg_primary; ?>",
                "background-light": "<?php echo $reg_bg_light; ?>",
                "background-dark": "<?php echo $reg_bg_dark; ?>",
              },
              fontFamily: {
                "display": ["Manrope", "sans-serif"]
              },
              borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
            },
          },
        }
    </script>
<style>
        body {
            font-family: 'Manrope', sans-serif;
        }
        .bg-custom-mesh {
            background-color: #ffffff;
            background-image: radial-gradient(at 0% 0%, rgba(19, 91, 236, 0.03) 0, transparent 50%), 
                              radial-gradient(at 100% 100%, rgba(19, 91, 236, 0.03) 0, transparent 50%);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col font-display">

<main class="flex-grow flex items-center justify-center px-4 py-12 bg-custom-mesh dark:bg-background-dark">
<div class="w-full max-w-[480px]">
<!-- Registration Card -->
<div class="bg-white dark:bg-slate-900/50 dark:border dark:border-slate-800 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 lg:p-10">
<!-- Header Section -->
<div class="mb-10 text-center">
<h1 class="text-3xl font-extrabold text-slate-900 dark:text-white mb-3">Crear Cuenta</h1>
<p class="text-slate-500 dark:text-slate-400">Completa tus datos para empezar tu experiencia con nosotros.</p>
</div>
<form id="form-registro-modal" class="space-y-6">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"/>
<!-- Full Name -->
<div class="space-y-2">
<label class="block text-sm font-semibold text-slate-700 dark:text-slate-300" for="name">Nombre completo</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-icons-outlined text-slate-400 text-xl">person</span>
</span>
<input class="block w-full pl-10 pr-3 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200" id="name" name="name" placeholder="Ej. Juan Pérez" required="" type="text"/>
</div>
<div id="error-name" class="text-red-500 text-sm hidden"></div>
</div>
<!-- Email -->
<div class="space-y-2">
<label class="block text-sm font-semibold text-slate-700 dark:text-slate-300" for="email">Correo electrónico</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-icons-outlined text-slate-400 text-xl">alternate_email</span>
</span>
<input class="block w-full pl-10 pr-3 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200" id="email" name="email" placeholder="tu@ejemplo.com" required="" type="email"/>
</div>
<div id="error-email" class="text-red-500 text-sm hidden"></div>
</div>
<!-- Password -->
<div class="space-y-2">
<label class="block text-sm font-semibold text-slate-700 dark:text-slate-300" for="password">Contraseña</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-icons-outlined text-slate-400 text-xl">lock_open</span>
</span>
<input class="block w-full pl-10 pr-10 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200" id="password" name="password" placeholder="••••••••" required="" type="password"/>
<button onclick="togglePasswordRegistro('password','icon-pass')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" type="button">
<span id="icon-pass" class="material-icons-outlined text-xl">visibility</span>
</button>
</div>
<!-- Indicador de fortaleza -->
<div id="password-strength-container" class="hidden mt-2">
<div class="flex gap-1 mb-1">
<div id="str-bar-1" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
<div id="str-bar-2" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
<div id="str-bar-3" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
<div id="str-bar-4" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
</div>
<p id="password-strength-text" class="text-xs font-semibold text-slate-400"></p>
</div>
<p class="text-xs text-slate-400 mt-2">Mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial (!@#$%^&*)</p>
<div id="error-password" class="text-red-500 text-sm hidden"></div>
</div>
<!-- Confirm Password -->
<div class="space-y-2">
<label class="block text-sm font-semibold text-slate-700 dark:text-slate-300" for="confirm-password">Confirmar contraseña</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-icons-outlined text-slate-400 text-xl">lock</span>
</span>
<input class="block w-full pl-10 pr-10 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200" id="confirm-password" name="confirm-password" placeholder="••••••••" required="" type="password"/>
<button onclick="togglePasswordRegistro('confirm-password','icon-confirm')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" type="button">
<span id="icon-confirm" class="material-icons-outlined text-xl">visibility</span>
</button>
</div>
<div id="error-confirm-password" class="text-red-500 text-sm hidden"></div>
</div>
<!-- Terms checkbox -->
<div class="flex items-start">
<div class="flex items-center h-5">
<input class="h-4 w-4 text-primary border-slate-300 dark:border-slate-700 rounded focus:ring-primary" id="terms" name="terms" required="" type="checkbox"/>
</div>
<div class="ml-3 text-sm">
<label class="text-slate-500 dark:text-slate-400" for="terms">
                                Acepto los <a class="text-primary hover:underline font-medium" href="#">Términos de Servicio</a> y la <a class="text-primary hover:underline font-medium" href="#">Política de Privacidad</a>.
                            </label>
</div>
</div>
<div id="error-terms" class="text-red-500 text-sm hidden"></div>
<!-- General Error Message -->
<div id="mensaje-error" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg"></div>
<!-- Success Message -->
<div id="mensaje-exito" class="hidden bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg"></div>
<!-- Main CTA -->
<button class="w-full py-4 px-6 bg-primary hover:bg-primary/90 text-white font-bold rounded-lg shadow-lg shadow-primary/20 transform active:scale-[0.98] transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-primary/30" type="submit">
                        Registrarse
                    </button>
</form>
<!-- Footer Link -->
<div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
<p class="text-slate-500 dark:text-slate-400 text-sm">
                        ¿Ya tienes una cuenta? 
                        <button onclick="loadLogin()" class="text-primary hover:underline font-bold ml-1 transition-colors bg-none border-none cursor-pointer">Inicia sesión</button>
</p>
</div>
</div>
<!-- Trust Badges -->
<div class="mt-8 flex justify-center items-center gap-6 opacity-40 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-500">
<div class="flex items-center gap-1.5">
<span class="material-icons-outlined text-sm">verified_user</span>
<span class="text-xs font-bold uppercase tracking-widest">Seguro</span>
</div>
<div class="flex items-center gap-1.5">
<span class="material-icons-outlined text-sm">cloud_done</span>
<span class="text-xs font-bold uppercase tracking-widest">Cloud</span>
</div>
<div class="flex items-center gap-1.5">
<span class="material-icons-outlined text-sm">support_agent</span>
<span class="text-xs font-bold uppercase tracking-widest">Soporte 24/7</span>
</div>
</div>
</div>
</main>

</body>

<script>
// Toggle mostrar/ocultar contraseña
function togglePasswordRegistro(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}

// Evaluar fortaleza de contraseña
function evaluarFortaleza(password) {
    let score = 0;
    const checks = {
        length: password.length >= 8,
        upper: /[A-Z]/.test(password),
        lower: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?`~]/.test(password)
    };

    Object.values(checks).forEach(v => { if (v) score++; });

    // Bonus por longitud extra
    if (password.length >= 12) score++;

    return { score, checks };
}

function actualizarBarraFortaleza(password) {
    const container = document.getElementById('password-strength-container');
    const textEl = document.getElementById('password-strength-text');
    
    if (!password) {
        container.classList.add('hidden');
        return;
    }
    container.classList.remove('hidden');

    const { score } = evaluarFortaleza(password);

    const niveles = [
        { max: 2, label: 'Muy débil', color: 'bg-red-500', textColor: 'text-red-500' },
        { max: 3, label: 'Débil', color: 'bg-orange-500', textColor: 'text-orange-500' },
        { max: 4, label: 'Media', color: 'bg-yellow-500', textColor: 'text-yellow-500' },
        { max: 5, label: 'Fuerte', color: 'bg-green-500', textColor: 'text-green-500' },
        { max: 7, label: 'Muy fuerte', color: 'bg-emerald-500', textColor: 'text-emerald-500' }
    ];

    let nivel = niveles[0];
    let barrasActivas = 1;
    if (score >= 5) { nivel = niveles[4]; barrasActivas = 4; }
    else if (score >= 4) { nivel = niveles[3]; barrasActivas = 3; }
    else if (score >= 3) { nivel = niveles[2]; barrasActivas = 2; }
    else if (score >= 2) { nivel = niveles[1]; barrasActivas = 2; }

    for (let i = 1; i <= 4; i++) {
        const bar = document.getElementById('str-bar-' + i);
        bar.className = 'h-1.5 flex-1 rounded-full transition-all duration-300 ' + 
            (i <= barrasActivas ? nivel.color : 'bg-slate-200');
    }

    textEl.textContent = nivel.label;
    textEl.className = 'text-xs font-semibold ' + nivel.textColor;
}

// Mostrar error en campo específico
function mostrarErrorRegistro(campo, mensaje) {
    const errorEl = document.getElementById('error-' + campo);
    if (errorEl) {
        errorEl.textContent = mensaje;
        errorEl.classList.remove('hidden');
    }
}

// Manejo del formulario de registro
function setupRegistroForm() {
    const form = document.getElementById('form-registro-modal');
    if (!form) return;

    // Listener para evaluar fortaleza en tiempo real
    const passInput = document.getElementById('password');
    if (passInput) {
        passInput.addEventListener('input', function() {
            actualizarBarraFortaleza(this.value);
        });
    }
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Limpiar mensajes previos
        const mensajeError = document.getElementById('mensaje-error');
        const mensajeExito = document.getElementById('mensaje-exito');
        if (mensajeError) mensajeError.classList.add('hidden');
        if (mensajeExito) mensajeExito.classList.add('hidden');
        document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add('hidden'));
        
        // Validaciones frontend
        const nombre = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const terms = document.getElementById('terms').checked;
        let valido = true;

        // Validar nombre
        if (!nombre) {
            mostrarErrorRegistro('name', 'El nombre es requerido');
            valido = false;
        } else if (nombre.length < 3) {
            mostrarErrorRegistro('name', 'El nombre debe tener al menos 3 caracteres');
            valido = false;
        }

        // Validar email
        if (!email) {
            mostrarErrorRegistro('email', 'El correo es requerido');
            valido = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            mostrarErrorRegistro('email', 'Ingresa un correo válido');
            valido = false;
        }

        // Validar contraseña segura
        if (!password) {
            mostrarErrorRegistro('password', 'La contraseña es requerida');
            valido = false;
        } else {
            const { checks } = evaluarFortaleza(password);
            if (!checks.length) {
                mostrarErrorRegistro('password', 'Mínimo 8 caracteres');
                valido = false;
            } else if (!checks.upper) {
                mostrarErrorRegistro('password', 'Debe incluir al menos una mayúscula');
                valido = false;
            } else if (!checks.lower) {
                mostrarErrorRegistro('password', 'Debe incluir al menos una minúscula');
                valido = false;
            } else if (!checks.number) {
                mostrarErrorRegistro('password', 'Debe incluir al menos un número');
                valido = false;
            } else if (!checks.special) {
                mostrarErrorRegistro('password', 'Debe incluir al menos un carácter especial (!@#$%^&*)');
                valido = false;
            }
        }

        // Validar confirmación
        if (password !== confirmPassword) {
            mostrarErrorRegistro('confirm-password', 'Las contraseñas no coinciden');
            valido = false;
        }

        // Validar términos
        if (!terms) {
            mostrarErrorRegistro('terms', 'Debes aceptar los términos y condiciones');
            valido = false;
        }

        if (!valido) return;
        
        // Obtener datos del formulario
        const formData = new FormData(this);
        
        try {
            const response = await fetch('/PAGINA%20WED/api/registrar_usuario.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.exito) {
                // Mostrar mensaje de éxito
                if (mensajeExito) {
                    mensajeExito.textContent = data.mensaje;
                    mensajeExito.classList.remove('hidden');
                }
                
                // Limpiar formulario
                form.reset();
                actualizarBarraFortaleza('');
                
                // Redirigir después de 2 segundos
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 2000);
            } else {
                // Mostrar errores
                if (mensajeError) {
                    if (data.errores && Array.isArray(data.errores)) {
                        mensajeError.innerHTML = '<strong>Errores:</strong><ul class="mt-2">' + 
                            data.errores.map(e => '<li>• ' + e + '</li>').join('') + '</ul>';
                    } else {
                        mensajeError.textContent = data.mensaje || 'Error desconocido';
                    }
                    mensajeError.classList.remove('hidden');
                }
            }
        } catch (error) {
            if (mensajeError) {
                mensajeError.textContent = 'Error al conectar con el servidor: ' + error.message;
                mensajeError.classList.remove('hidden');
            }
        }
    });
}

// Ejecutar cuando el formulario esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupRegistroForm);
} else {
    setupRegistroForm();
}

function loadLogin() {
    // Cargar la página de login
    if (typeof fetch !== 'undefined') {
        fetch('/PAGINA%20WED/pages/login.php')
            .then(response => response.text())
            .then(data => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data;
                const bodyContent = tempDiv.querySelector('body')?.innerHTML || data;
                
                document.getElementById('mainContent').innerHTML = bodyContent;
                
                const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
                let scriptMatch;
                while ((scriptMatch = scriptRegex.exec(data)) !== null) {
                    const script = document.createElement('script');
                    script.textContent = scriptMatch[1];
                    document.body.appendChild(script);
                }
                
                window.scrollTo(0, 0);
            })
            .catch(error => console.error('Error al cargar login:', error));
    } else {
        window.location.href = 'pages/login.php';
    }
}
</script>

</html>