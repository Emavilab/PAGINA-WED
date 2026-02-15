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
                "primary": "#135bec",
                "background-light": "#f6f6f8",
                "background-dark": "#101622",
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
<form action="#" class="space-y-6" method="POST">
<!-- Full Name -->
<div class="space-y-2">
<label class="block text-sm font-semibold text-slate-700 dark:text-slate-300" for="name">Nombre completo</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-icons-outlined text-slate-400 text-xl">person</span>
</span>
<input class="block w-full pl-10 pr-3 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200" id="name" name="name" placeholder="Ej. Juan Pérez" required="" type="text"/>
</div>
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
</div>
<!-- Password -->
<div class="space-y-2">
<label class="block text-sm font-semibold text-slate-700 dark:text-slate-300" for="password">Contraseña</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-icons-outlined text-slate-400 text-xl">lock_open</span>
</span>
<input class="block w-full pl-10 pr-10 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200" id="password" name="password" placeholder="••••••••" required="" type="password"/>
<button class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" type="button">
<span class="material-icons-outlined text-xl">visibility</span>
</button>
</div>
</div>
<!-- Confirm Password -->
<div class="space-y-2">
<label class="block text-sm font-semibold text-slate-700 dark:text-slate-300" for="confirm-password">Confirmar contraseña</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-icons-outlined text-slate-400 text-xl">lock</span>
</span>
<input class="block w-full pl-10 pr-10 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200" id="confirm-password" name="confirm-password" placeholder="••••••••" required="" type="password"/>
</div>
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

</body></html>