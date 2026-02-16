<!DOCTYPE html>

<html lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Contáctanos | Retail CMS</title>
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
<style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>

<main class="max-w-7xl mx-auto px-4 py-12">
<!-- Hero Section -->
<div class="text-center mb-16">
<nav class="flex justify-center mb-4 text-xs font-medium uppercase tracking-widest text-slate-500">
<span class="mx-2">/</span>
</nav>
<h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-4">Estamos para ayudarte</h1>
<p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto text-lg leading-relaxed">
                ¿Tienes alguna duda sobre nuestros productos o necesitas asistencia técnica? Completa el formulario y nuestro equipo se pondrá en contacto contigo en breve.
            </p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
<!-- Contact Form Section -->
<div class="lg:col-span-2">
<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
<h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
<span class="material-icons text-primary">email</span>
                        Envíanos un mensaje
                    </h2>
<form id="formContacto" class="space-y-6">

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

<div class="space-y-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="nombre">Nombre completo</label>
<input required
class="w-full px-4 py-3 rounded border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
id="nombre"
name="nombre"
type="text"
placeholder="Ej. Juan Pérez">
</div>

<div class="space-y-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="correo">Correo electrónico</label>
<input required
class="w-full px-4 py-3 rounded border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
id="correo"
name="correo"
type="email"
placeholder="juan@ejemplo.com">
</div>

</div>

<div class="space-y-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="telefono">Teléfono</label>
<input
class="w-full px-4 py-3 rounded border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
id="telefono"
name="telefono"
type="text"
placeholder="Ej. 9999-9999">
</div>

<div class="space-y-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="asunto">Asunto</label>
<input required
class="w-full px-4 py-3 rounded border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
id="asunto"
name="asunto"
type="text"
placeholder="¿En qué podemos ayudarte?">
</div>

<div class="space-y-2">
<label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="mensaje">Mensaje</label>
<textarea required
class="w-full px-4 py-3 rounded border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-primary focus:border-primary transition-all resize-none"
id="mensaje"
name="mensaje"
rows="5"
placeholder="Escribe aquí los detalles de tu consulta..."></textarea>
</div>

<div class="flex items-center gap-3">
<input required
class="rounded text-primary focus:ring-primary border-slate-300 dark:border-slate-700 dark:bg-slate-800"
id="privacidad"
type="checkbox">
<label class="text-sm text-slate-600 dark:text-slate-400" for="privacidad">
He leído y acepto la política de privacidad.
</label>
</div>

<button
class="w-full md:w-auto bg-primary hover:bg-primary/90 text-white font-bold py-4 px-8 rounded-lg shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2"
type="submit">
<span>Enviar Mensaje</span>
<span class="material-icons text-sm">send</span>
</button>

</form>

</div>
</div>
<!-- Contact Info Section -->
<div class="space-y-6">
<!-- Info Card 1: Offices -->
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm group hover:border-primary transition-colors">
<div class="flex items-start gap-4">
<div class="bg-primary/10 dark:bg-primary/20 p-3 rounded-lg text-primary">
<span class="material-icons">location_on</span>
</div>
<div>
<h3 class="font-bold text-slate-900 dark:text-white mb-1">Nuestras Oficinas</h3>
<p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                Av. de la Reforma 222, Piso 15<br/>
                                Col. Juárez, Ciudad de México<br/>
                                CP 06600, México
                            </p>
</div>
</div>
</div>
<!-- Info Card 2: Phone -->
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm group hover:border-primary transition-colors">
<div class="flex items-start gap-4">
<div class="bg-primary/10 dark:bg-primary/20 p-3 rounded-lg text-primary">
<span class="material-icons">phone</span>
</div>
<div>
<h3 class="font-bold text-slate-900 dark:text-white mb-1">Teléfonos de Atención</h3>
<p class="text-sm text-slate-600 dark:text-slate-400">
                                Ventas: <span class="font-semibold">+52 (55) 1234-5678</span><br/>
                                Soporte: <span class="font-semibold">+52 (55) 8765-4321</span>
</p>
</div>
</div>
</div>
<!-- Info Card 3: Email -->
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm group hover:border-primary transition-colors">
<div class="flex items-start gap-4">
<div class="bg-primary/10 dark:bg-primary/20 p-3 rounded-lg text-primary">
<span class="material-icons">alternate_email</span>
</div>
<div>
<h3 class="font-bold text-slate-900 dark:text-white mb-1">Correo Electrónico</h3>
<p class="text-sm text-slate-600 dark:text-slate-400">
<a class="hover:text-primary" href="mailto:soporte@retailcms.com">soporte@retailcms.com</a><br/>
<a class="hover:text-primary" href="mailto:ventas@retailcms.com">ventas@retailcms.com</a>
</p>
</div>
</div>
</div>
<!-- Info Card 4: Social Media -->
<div class="bg-primary p-6 rounded-xl shadow-lg shadow-primary/20">
<h3 class="font-bold text-white mb-4">Síguenos en redes</h3>
<div class="flex gap-4">
<a class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors text-white" href="#">
<svg class="w-5 h-5 fill-current" viewbox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path></svg>
</a>
<a class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors text-white" href="#">
<svg class="w-5 h-5 fill-current" viewbox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"></path></svg>
</a>
<a class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors text-white" href="#">
<svg class="w-5 h-5 fill-current" viewbox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path></svg>
</a>
<a class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors text-white" href="#">
<svg class="w-5 h-5 fill-current" viewbox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path></svg>
</a>
</div>
</div>
</div>
</div>
<!-- Map Section -->
<div class="space-y-6">
<h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
<span class="material-icons text-primary">map</span>
                Nuestra Ubicación Principal
            </h2>
<div class="relative w-full h-96 rounded-xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800 bg-slate-200 dark:bg-slate-800">
<img alt="Mapa de ubicación" class="w-full h-full object-cover opacity-80" data-alt="Google Maps style view of a city location in Mexico City" data-location="Mexico City" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD9lQ25R0RN5NimGXY5vgFARNNSoG-m3SniA3Hkrs__kqCm1DHrK15Rw1LMOUc6uNDeUHiQN89camsoAYlxda7V2sRvrBKO91SFAbao_iWvIqSERLT9Ik25SZgFshSjon3Us9J2hTEArDNQinhfA3IAui8YJDdTlJ0AVtx45DLwG46h50r0kujgtd5Q__VxizbBgtr5kldB1JTiakz32pQ-V27dRk1gdQn7OB4vmrbq-v0LOwiMLNEiPaaoSNrKdmNvxjpIbC6SkEk"/>
<!-- Floating Map Controls Overlay (Visual Mockup) -->
<div class="absolute top-4 left-4 bg-white dark:bg-slate-900 p-4 rounded shadow-lg max-w-xs border border-slate-200 dark:border-slate-800 hidden md:block">
<p class="font-bold text-sm text-slate-900 dark:text-white">Sede Central Retail CMS</p>
<p class="text-xs text-slate-500 mb-2">Av. de la Reforma 222, CDMX</p>
<button class="text-primary text-xs font-bold hover:underline">Ver en Google Maps</button>
</div>
<!-- Custom Marker Point -->
<div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
<div class="relative flex items-center justify-center">
<div class="absolute w-12 h-12 bg-primary/30 rounded-full animate-ping"></div>
<div class="relative bg-primary p-2 rounded-full border-2 border-white shadow-xl">
<span class="material-icons text-white">storefront</span>
</div>
</div>
</div>
</div>
</div>
</main>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){

    $("#formContacto").submit(function(e){
        e.preventDefault();

        $.ajax({
            url: "guardar_mensaje.php",
            type: "POST",
            data: $(this).serialize(),
            success: function(respuesta){

                if(respuesta.trim() === "ok"){
                    alert("Mensaje enviado correctamente ✅");
                    $("#formContacto")[0].reset();
                } else {
                    alert("Error al guardar en la base de datos ❌");
                }
            },
            error: function(){
                alert("Error de conexión con el servidor ❌");
            }
        });

    });

});
</script>
</body>
</html>