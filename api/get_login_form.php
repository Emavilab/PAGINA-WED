<?php
/**
 * API para obtener el contenido del formulario de login
 * Devuelve solo el HTML del formulario sin boilerplate
 */

require_once '../core/conexion.php';
require_once '../core/csrf.php';

$csrfToken = obtenerTokenCSRF();

header('Content-Type: text/html; charset=utf-8');

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
<!-- Cargar Material Icons para el formulario -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
    :root {
        --color-primary: <?php echo $login_primary; ?>;
        --color-bg-light: <?php echo $login_bg_light; ?>;
        --color-bg-dark: <?php echo $login_bg_dark; ?>;
    }
    
    /* Estilos CSS necesarios para los colores */
    .text-primary {
        color: var(--color-primary);
    }
    
    .bg-primary {
        background-color: var(--color-primary);
    }
    
    .focus\:ring-primary\/20:focus {
        --tw-ring-color: rgba(var(--color-primary), 0.2);
    }
    
    /* Asegurar que los iconos de Material se renderiven correctamente */
    .material-icons {
        font-family: 'Material Icons';
        font-weight: normal;
        font-style: normal;
        font-size: 24px;
        display: inline-flex;
        line-height: 1;
        text-transform: none;
        letter-spacing: normal;
        word-wrap: normal;
        white-space: nowrap;
        direction: ltr;
    }
</style>

<div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, var(--color-bg-light) 0%, var(--color-bg-dark) 100%);">
    <div class="w-full max-w-lg">
        <!-- Logo y Título -->
        <div class="text-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white mb-2">Iniciar Sesión</h1>
            <p class="text-slate-600 dark:text-slate-400">Bienvenido de nuevo, por favor ingresa tus datos.</p>
        </div>

        <!-- Formulario -->
        <form id="form-login-modal" class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-8 space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"/>
            <!-- Email -->
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="email">
                    Correo electrónico
                </label>
                <div class="relative group">
                    <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-xl">mail_outline</span>
                    <input class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-slate-400 text-sm" id="email" name="email" placeholder="ejemplo@correo.com" type="email"/>
                </div>
                <div id="error-email" class="text-red-500 text-sm hidden"></div>
            </div>

            <!-- Contraseña -->
            <div class="space-y-2">
                <div class="flex items-center justify-between px-1">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="password">
                        Contraseña
                    </label>
                    <a class="text-xs font-semibold text-primary hover:underline" href="#">¿Olvidaste tu contraseña?</a>
                </div>
                <div class="relative group">
                    <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-xl">lock_outline</span>
                    <input class="w-full pl-10 pr-12 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-slate-400 text-sm" id="password" name="password" placeholder="••••••••" type="password"/>
                    <button onclick="togglePasswordLogin()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" type="button">
                        <span id="icon-toggle-password" class="material-icons text-lg">visibility_off</span>
                    </button>
                </div>
                <div id="error-password" class="text-red-500 text-sm hidden"></div>
            </div>

            <!-- Recordarme -->
            <div class="flex items-center space-x-2 px-1">
                <input class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary" id="remember" name="remember" type="checkbox"/>
                <label class="text-xs text-slate-600 dark:text-slate-400 font-medium" for="remember">Recordarme en este dispositivo</label>
            </div>

            <!-- Mensajes de Error/Éxito -->
            <div id="mensaje-error" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg"></div>
            <div id="mensaje-exito" class="hidden bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg"></div>

            <!-- Botón Submit -->
            <button class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-lg transition-all shadow-lg shadow-primary/25 active:scale-[0.98]" type="submit">
                Iniciar Sesión
            </button>

            <!-- Enlace a Registro -->
            <p class="text-center text-sm text-slate-600 dark:text-slate-400">
                ¿No tienes cuenta? 
                <a class="text-primary font-semibold hover:underline cursor-pointer" onclick="loadRegistrarse()">Regístrate aquí</a>
            </p>
        </form>
    </div>
</div>
