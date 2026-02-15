<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Iniciar Sesión - Cliente</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#203650",
                        "background-light": "#f6f7f8",
                        "background-dark": "#15191d"
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
<div class="w-full max-w-md">
<div class="bg-white dark:bg-slate-900 shadow-xl shadow-primary/5 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800">
<div class="p-8 pb-4 text-center">
<div class="inline-flex items-center justify-center w-12 h-12 bg-primary/10 rounded-xl mb-6">
</div>

<p class="text-slate-500 dark:text-slate-400 text-sm">Bienvenido de nuevo, por favor ingresa tus datos.</p>
</div>
<div class="p-8 pt-2">
<form action="#" class="space-y-5">
<div class="space-y-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300 ml-1" for="email">
                            Correo electrónico
                        </label>
<div class="relative group">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-xl">mail_outline</span>
<input class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-slate-400 text-sm" id="email" placeholder="ejemplo@correo.com" type="email"/>
</div>
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
<input class="w-full pl-10 pr-12 py-3 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-slate-400 text-sm" id="password" placeholder="••••••••" type="password"/>
<button class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" type="button">
<span class="material-icons text-lg">visibility_off</span>
</button>
</div>
</div>
<div class="flex items-center space-x-2 px-1">
<input class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary" id="remember" type="checkbox"/>
<label class="text-xs text-slate-600 dark:text-slate-400 font-medium" for="remember">Recordarme en este dispositivo</label>
</div>
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
</body></html>