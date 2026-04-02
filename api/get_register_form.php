<?php
/**
 * API para obtener el contenido del formulario de registro
 * Devuelve solo el HTML del formulario sin boilerplate
 */

require_once '../core/conexion.php';
require_once '../core/csrf.php';

$csrfToken = obtenerTokenCSRF();

header('Content-Type: text/html; charset=utf-8');

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
<!-- Cargar Material Icons Outlined para el formulario -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

<style>
    :root {
        --color-primary: <?php echo $reg_primary; ?>;
        --color-bg-light: <?php echo $reg_bg_light; ?>;
        --color-bg-dark: <?php echo $reg_bg_dark; ?>;
    }
    
    /* Asegurar que los iconos de Material Outlined se renderiven correctamente */
    .material-icons-outlined {
        font-family: 'Material Icons Outlined';
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
        vertical-align: middle;
    }
</style>

<main class="flex-grow flex items-center justify-center px-4 py-12 bg-slate-50 dark:bg-var(--color-bg-dark)">
    <div class="w-full max-w-[480px]">
        <!-- Registration Card -->
        <div class="bg-white dark:bg-slate-900/50 dark:border dark:border-slate-800 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 lg:p-10">
            <!-- Header Section -->
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white mb-3">Crear Cuenta</h1>
                <p class="text-slate-500 dark:text-slate-400">Completa tus datos para empezar tu experiencia con nosotros.</p>
            </div>
            
            <form id="form-register" class="space-y-6">
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
                <div id="mensaje-error-registro" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg"></div>
                
                <!-- Success Message -->
                <div id="mensaje-exito-registro" class="hidden bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg"></div>

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
