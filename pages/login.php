<?php
require_once '../core/conexion.php';
require_once '../core/csrf.php';

$csrfToken = obtenerTokenCSRF();

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
?>
<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Iniciar Sesión</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        const loginColors = {
            primary: "<?php echo $login_primary; ?>",
            bg_light: "<?php echo $login_bg_light; ?>",
            bg_dark: "<?php echo $login_bg_dark; ?>"
        };
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: loginColors.primary,
                        "background-light": loginColors.bg_light,
                        "background-dark": loginColors.bg_dark
                    },
                    fontFamily: {
                        display: "Inter"
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        lg: "1rem",
                        xl: "1.5rem",
                        full: "9999px"
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-800 dark:text-slate-200 min-h-screen flex items-center justify-center p-4">
<div class="w-full flex items-center justify-center min-h-screen py-8">
<div class="w-full max-w-md">
<div class="bg-white dark:bg-slate-900 shadow-xl shadow-primary/5 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800">
<div class="p-8 pb-4 text-center">
<div class="inline-flex items-center justify-center w-12 h-12 bg-primary/10 rounded-xl mb-6">
</div>
<h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Iniciar Sesión</h2>
<p class="text-slate-500 dark:text-slate-400 text-sm">Bienvenido de nuevo, por favor ingresa tus datos.</p>
</div>
<div class="p-8 pt-2">
<form id="form-login-modal" class="space-y-5">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"/>
<div class="space-y-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300 ml-1" for="email">
                            Correo electrónico
                        </label>
<div class="relative group">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-xl">mail_outline</span>
<input class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-slate-400 text-sm" id="email" name="email" placeholder="ejemplo@correo.com" type="email"/>
</div>
<div id="error-email" class="text-red-500 text-sm hidden"></div>
</div>
<div class="space-y-2">
<div class="flex items-center justify-between px-1">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="password">
                                Contraseña
                            </label>
<a class="text-xs font-semibold text-primary hover:underline" href="#">¿Olvidaste tu contraseña?</a>
</div>
<div class="relative group">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-xl">lock_outline</span>
<input class="w-full pl-10 pr-12 py-3 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-slate-400 text-sm" id="password" name="password" placeholder="••••••••" type="password"/>
<button onclick="togglePasswordLogin()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" type="button">
<span id="icon-toggle-password" class="material-icons text-lg">visibility_off</span>
</button>
</div>
<div id="error-password" class="text-red-500 text-sm hidden"></div>
</div>
<div class="flex items-center space-x-2 px-1">
<input class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary" id="remember" name="remember" type="checkbox"/>
<label class="text-xs text-slate-600 dark:text-slate-400 font-medium" for="remember">Recordarme en este dispositivo</label>
</div>
<!-- General Error Message -->
<div id="mensaje-error" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg"></div>
<!-- Success Message -->
<div id="mensaje-exito" class="hidden bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg"></div>
<button class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-lg transition-all shadow-lg shadow-primary/25 active:scale-[0.98]" type="submit">
                        Iniciar Sesión
                    </button>
</form>
</div>
<div class="bg-slate-50 dark:bg-slate-800/50 p-6 text-center">
<p class="text-sm text-slate-600 dark:text-slate-400">
                    ¿No tienes una cuenta? 
                    <button onclick="loadRegistrarse()" class="text-primary font-bold hover:underline ml-1 bg-none border-none cursor-pointer">Regístrate gratis</button>
</p>
</div>
</div>
</div>
</div>
</body>

<script>
// Toggle mostrar/ocultar contraseña en login
function togglePasswordLogin() {
    const input = document.getElementById('password');
    const icon = document.getElementById('icon-toggle-password');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility_off';
    }
}

// Mostrar error en campo específico
function mostrarErrorLogin(campo, mensaje) {
    const errorEl = document.getElementById('error-' + campo);
    if (errorEl) {
        errorEl.textContent = mensaje;
        errorEl.classList.remove('hidden');
    }
}

// Manejo del formulario de login
function setupLoginForm() {
    const form = document.getElementById('form-login-modal');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Limpiar mensajes previos
        const mensajeError = document.getElementById('mensaje-error');
        const mensajeExito = document.getElementById('mensaje-exito');
        if (mensajeError) mensajeError.classList.add('hidden');
        if (mensajeExito) mensajeExito.classList.add('hidden');
        document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add('hidden'));
        
        // Validaciones frontend
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        let valido = true;

        if (!email) {
            mostrarErrorLogin('email', 'El correo es requerido');
            valido = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            mostrarErrorLogin('email', 'Ingresa un correo válido');
            valido = false;
        }

        if (!password) {
            mostrarErrorLogin('password', 'La contraseña es requerida');
            valido = false;
        }

        if (!valido) return;
        
        // Obtener datos del formulario
        const formData = new FormData(this);
        
        try {
            const response = await fetch('/PAGINA%20WED/api/validar_login.php', {
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
                
                // Redirigir después de 1.5 segundos
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                // Mostrar errores
                if (mensajeError) {
                    mensajeError.textContent = data.mensaje || 'Error desconocido';
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
    document.addEventListener('DOMContentLoaded', setupLoginForm);
} else {
    setupLoginForm();
}

function loadRegistrarse() {
    fetch('/PAGINA%20WED/pages/crear_cuenta.php')
        .then(response => response.text())
        .then(data => {
            // Crear un contenedor temporal para parsear el HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data;
            
            // Extraer solo el body content
            const bodyContent = tempDiv.querySelector('body')?.innerHTML || data;
            
            // Insertar el contenido en mainContent
            document.getElementById('mainContent').innerHTML = bodyContent;
            
            // Ejecutar scripts que puedan estar en el contenido cargado
            const scripts = tempDiv.querySelectorAll('script');
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');
                newScript.textContent = oldScript.textContent;
                document.body.appendChild(newScript);
            });
        });
}
</script>

</html>