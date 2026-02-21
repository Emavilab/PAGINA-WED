<!DOCTYPE html>
<html lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Ofertas y Promociones Especiales | Retail CMS</title>
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
                        "accent": "#ef4444",
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
        .promotion-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
    </style>
<main class="max-w-7xl mx-auto px-4 py-8">
<section class="promotion-gradient rounded-2xl overflow-hidden relative mb-12 shadow-2xl border border-slate-700">
<div class="absolute inset-0 opacity-20 pointer-events-none">
<div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-primary via-transparent to-transparent"></div>
</div>
<div class="relative z-10 p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
<div class="text-center md:text-left">
<span class="bg-accent text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4 inline-block">Flash Sale</span>
<h1 class="text-4xl md:text-6xl font-black text-white mb-4 leading-tight">
                    Venta Flash <br/><span class="text-primary">Hasta -70%</span>
</h1>
<p class="text-slate-300 text-lg mb-6 max-w-md">
                    Solo por tiempo limitado. Descuentos exclusivos en tecnología, moda y hogar. ¡No dejes que se escapen!
                </p>
<button class="bg-white text-slate-900 px-8 py-3 rounded-lg font-bold hover:bg-slate-100 transition-colors uppercase text-sm tracking-widest">
                    Comprar Ahora
                </button>
</div>
<div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 md:p-8 rounded-2xl text-white text-center">
<p class="text-xs uppercase font-semibold tracking-tighter mb-4 opacity-80">La oferta termina en:</p>
<div class="flex gap-4 items-start">
<div>
<div class="text-3xl md:text-4xl font-black tabular-nums">02</div>
<div class="text-[10px] uppercase opacity-70">Horas</div>
</div>
<div class="text-3xl md:text-4xl font-black opacity-50">:</div>
<div>
<div class="text-3xl md:text-4xl font-black tabular-nums">45</div>
<div class="text-[10px] uppercase opacity-70">Minutos</div>
</div>
<div class="text-3xl md:text-4xl font-black opacity-50">:</div>
<div>
<div class="text-3xl md:text-4xl font-black tabular-nums">12</div>
<div class="text-[10px] uppercase opacity-70">Segundos</div>
</div>
</div>
</div>
</div>
</section>
<div class="flex flex-col lg:flex-row gap-8">
<aside class="w-full lg:w-64 space-y-8">
<div>
<h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-xl">filter_list</span>
                    Filtros
                </h3>
<div class="space-y-4 mb-8">
<p class="text-sm font-bold text-slate-700 dark:text-slate-300">Categoría</p>
<div class="space-y-2">
<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">
<input class="rounded text-primary focus:ring-primary border-slate-300" type="checkbox"/>
                            Tecnología
                        </label>
<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">
<input checked="" class="rounded text-primary focus:ring-primary border-slate-300" type="checkbox"/>
                            Moda
                        </label>
<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">
<input class="rounded text-primary focus:ring-primary border-slate-300" type="checkbox"/>
                            Hogar
                        </label>
<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">
<input class="rounded text-primary focus:ring-primary border-slate-300" type="checkbox"/>
                            Deportes
                        </label>
</div>
</div>
<div class="space-y-4 mb-8">
<p class="text-sm font-bold text-slate-700 dark:text-slate-300">Rango de Precio</p>
<input class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-primary" type="range"/>
<div class="flex justify-between text-xs text-slate-500">
<span>$0</span>
<span>$5000+</span>
</div>
</div>
<div class="space-y-4">
<p class="text-sm font-bold text-slate-700 dark:text-slate-300">Descuento</p>
<div class="space-y-2">
<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">
<input class="text-primary focus:ring-primary border-slate-300" name="discount" type="radio"/>
                            -10% o más
                        </label>
<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">
<input class="text-primary focus:ring-primary border-slate-300" name="discount" type="radio"/>
                            -30% o más
                        </label>
<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">
<input class="text-primary focus:ring-primary border-slate-300" name="discount" type="radio"/>
                            -50% o más
                        </label>
</div>
</div>
</div>
<button class="w-full py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg text-sm font-medium hover:bg-slate-200 transition-colors">
                Limpiar Filtros
            </button>
</aside>
<div class="flex-1">
<div class="flex items-center justify-between mb-6">
<p class="text-sm text-slate-500">Mostrando 12 de 84 ofertas disponibles</p>
<select class="bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-sm rounded-lg focus:ring-primary">
<option>Ordenar por: Relevancia</option>
<option>Precio: Menor a Mayor</option>
<option>Precio: Mayor a Menor</option>
<option>Mayor Descuento</option>
</select>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
<div class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow relative">
<div class="relative aspect-square overflow-hidden bg-slate-100">
<img alt="Producto" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDf6xT8tP2mEZZMEDwRBd-GC4AjuAJc9JPYkqXljAbp7drcryxQLOR4BIoKnjRxoPZGL2XYB5tamWyfjX2Q_QhpnO_ZcFL37A0zCjdFnqbUXCErraCgYecULSvSWF5WL73mmCGZaQhYyEi8JgfuFvH_T55llU2TFABHZ4v-lz2XlyLFAkdMXAhquaRT23meqF7ASNRQ9NOPU57hl2qBBkX_PTLaBVriotujauVFxeHLf5QGydUk8_mTS957qY73U_Q54XyCl1Abqq4"/>
<span class="absolute top-3 left-3 bg-accent text-white px-2 py-1 rounded-lg text-xs font-bold">-50%</span>
<button class="absolute top-3 right-3 w-8 h-8 bg-white/80 dark:bg-slate-900/80 backdrop-blur rounded-full flex items-center justify-center text-slate-400 hover:text-accent transition-colors">
<span class="material-symbols-outlined text-xl">favorite</span>
</button>
</div>
<div class="p-4">
<p class="text-xs text-slate-500 uppercase tracking-widest mb-1">Tecnología</p>
<h4 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-2">Smartwatch Pro Gen 5 con Pantalla OLED</h4>
<div class="flex items-center gap-2 mb-4">
<span class="text-accent font-bold text-lg">$149.99</span>
<span class="text-slate-400 line-through text-sm">$299.99</span>
</div>
<button class="w-full bg-slate-900 dark:bg-primary text-white py-2 rounded-lg font-medium text-sm hover:opacity-90 transition-opacity">
                            Añadir al carrito
                        </button>
</div>
</div>
<div class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow relative">
<div class="relative aspect-square overflow-hidden bg-slate-100">
<img alt="Producto" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZydCEcVhw021asc3juQW6RtS1Ls5rPZXRjExwr2hkwexxmnZnDAknSt8IpAZ-nhOGGdOsnaUn8WJtFdH_krubmB_y6he6N-eTa8udolMKVnjf72EX-qZHacgR8HwmdzDIvrHK0v7VeObMwzM2wNVt0rZgwBS5e_xBwFEeFUUZv_nS3Zba_G47-wuJnomt57iNX0-bLQMgxlD1AODoB6LXwJDQF4Kf5TxDPsEL2DvO4vK2_8_JPhiZYxMTRQM_0lGfM4cee1Myxco"/>
<span class="absolute top-3 left-3 bg-accent text-white px-2 py-1 rounded-lg text-xs font-bold">-30%</span>
<button class="absolute top-3 right-3 w-8 h-8 bg-white/80 dark:bg-slate-900/80 backdrop-blur rounded-full flex items-center justify-center text-accent transition-colors">
<span class="material-symbols-outlined text-xl fill-1">favorite</span>
</button>
</div>
<div class="p-4">
<p class="text-xs text-slate-500 uppercase tracking-widest mb-1">Moda</p>
<h4 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-2">Chaqueta Impermeable Explorer Edition</h4>
<div class="flex items-center gap-2 mb-4">
<span class="text-accent font-bold text-lg">$62.30</span>
<span class="text-slate-400 line-through text-sm">$89.00</span>
</div>
<button class="w-full bg-slate-900 dark:bg-primary text-white py-2 rounded-lg font-medium text-sm hover:opacity-90 transition-opacity">
                            Añadir al carrito
                        </button>
</div>
</div>
<div class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow relative">
<div class="relative aspect-square overflow-hidden bg-slate-100">
<img alt="Producto" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC3C_aJqj7h2OmN1OvrHn4fQ44RklabANw7Cd3LU5neBh0nC6zLEt5VmO0UMgyp3TUovlM2dZm9_xBRfFW1p0XjcORHpwqeEVopbYBzUdNaS3Cc5xHUj_uO6mdEFR-Ale73--C8kl1wuelMAgbemZ9rX8Lj6EQce-ZrAtqK-7IIxq8_aUVMMrF1logYlnW6Mk74YI5EMdEg-LusMUSTmdoO4Ex1EES9KH8GcRnZAIndw9xuTmqAw8jDSc0ol0uPRXzq-rOFWczovJw"/>
<span class="absolute top-3 left-3 bg-accent text-white px-2 py-1 rounded-lg text-xs font-bold">-25%</span>
<button class="absolute top-3 right-3 w-8 h-8 bg-white/80 dark:bg-slate-900/80 backdrop-blur rounded-full flex items-center justify-center text-slate-400 hover:text-accent transition-colors">
<span class="material-symbols-outlined text-xl">favorite</span>
</button>
</div>
<div class="p-4">
<p class="text-xs text-slate-500 uppercase tracking-widest mb-1">Hogar</p>
<h4 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-2">Cafetera Express Automática 15 Bares</h4>
<div class="flex items-center gap-2 mb-4">
<span class="text-accent font-bold text-lg">$112.50</span>
<span class="text-slate-400 line-through text-sm">$150.00</span>
</div>
<button class="w-full bg-slate-900 dark:bg-primary text-white py-2 rounded-lg font-medium text-sm hover:opacity-90 transition-opacity">
                            Añadir al carrito
                        </button>
</div>
</div>
<div class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow relative">
<div class="relative aspect-square overflow-hidden bg-slate-100">
<img alt="Producto" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB87bHuRmQsG6gXKkKhX3YvU_gFLYrifHgQXRLULnA8eUbHjs-r5PMjispItZ74ciZTB2wUHLSrnlsmcjL8oOHbrlB3C-uxwSStmJzQFJ6xvy83Wpwqh73v587J5fT-kJJrQubtgWFG09jMU4hMgnkfzkwYKltQCwjIeYx70S2dwVcgav_LDGXd7sWOQsugqaJ2HiaOi4Uo4VFtAY3Y9IkTD7AAKsjDBZJay5sXIiZ16XDcfi1839D3Vsvb_ZnHWUJoYf3pZniZm_0"/>
<span class="absolute top-3 left-3 bg-accent text-white px-2 py-1 rounded-lg text-xs font-bold">-60%</span>
<button class="absolute top-3 right-3 w-8 h-8 bg-white/80 dark:bg-slate-900/80 backdrop-blur rounded-full flex items-center justify-center text-slate-400 hover:text-accent transition-colors">
<span class="material-symbols-outlined text-xl">favorite</span>
</button>
</div>
<div class="p-4">
<p class="text-xs text-slate-500 uppercase tracking-widest mb-1">Deportes</p>
<h4 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-2">Set de Pesas Ajustables 20kg</h4>
<div class="flex items-center gap-2 mb-4">
<span class="text-accent font-bold text-lg">$35.99</span>
<span class="text-slate-400 line-through text-sm">$89.99</span>
</div>
<button class="w-full bg-slate-900 dark:bg-primary text-white py-2 rounded-lg font-medium text-sm hover:opacity-90 transition-opacity">
                            Añadir al carrito
                        </button>
</div>
</div>
<div class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow relative">
<div class="relative aspect-square overflow-hidden bg-slate-100">
<img alt="Producto" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBsBBgFdPhU01XKCmSdR_AlrUQhN-S9NsrYcujWTUqD0rWLYzNgFFnBviL0qvqxVIjmVwNlc2vDqLB7UZ79Jd5KUt5stp154FGWbb6ZfEEtlaG4ijj4TtEIZxOyLfEhHY5XqDEZ7sg5OXBTudQSJxqnI_YrCK8VrIsNlOVPV0XdP-ZbVqQytURJf6xeKomKAqzQmt3qtlTddb6OSWNrm-ffv29U_c572_HL3hbHYp-j-1R8Rk2iQKG9HuBsYzROSgWfWKgdnxSGbRc"/>
<span class="absolute top-3 left-3 bg-accent text-white px-2 py-1 rounded-lg text-xs font-bold">-40%</span>
<button class="absolute top-3 right-3 w-8 h-8 bg-white/80 dark:bg-slate-900/80 backdrop-blur rounded-full flex items-center justify-center text-slate-400 hover:text-accent transition-colors">
<span class="material-symbols-outlined text-xl">favorite</span>
</button>
</div>
<div class="p-4">
<p class="text-xs text-slate-500 uppercase tracking-widest mb-1">Tecnología</p>
<h4 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-2">Audífonos Inalámbricos con Noise Cancelling</h4>
<div class="flex items-center gap-2 mb-4">
<span class="text-accent font-bold text-lg">$119.40</span>
<span class="text-slate-400 line-through text-sm">$199.00</span>
</div>
<button class="w-full bg-slate-900 dark:bg-primary text-white py-2 rounded-lg font-medium text-sm hover:opacity-90 transition-opacity">
                            Añadir al carrito
                        </button>
</div>
</div>
<div class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow relative">
<div class="relative aspect-square overflow-hidden bg-slate-100">
<img alt="Producto" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCJnKPn1vIQlwObFKM3dVCF2m_TF5ClgaHykXUAXK3CXMJT71B83mCS4QZ1zEnbWjzUo1FYib_j2UsT5U8dye4fduj0CJjNUzEeBDptl-ZoBgR0ywSGydp_qR5sSFkowpW0EXeHyesQlEl6G-bIWZ67bwVk5g1rjFTHXH54aLokZTfPLSDGuVikzJeFK6ofGsfpfZgdB7DZyD4vMr_K2NLgHqxEWGK3hZPpTFHujvKSV6LBFbKuyHF_Pf6Y0pj_9oM5MXqmR3cJJR4"/>
<span class="absolute top-3 left-3 bg-accent text-white px-2 py-1 rounded-lg text-xs font-bold">-15%</span>
<button class="absolute top-3 right-3 w-8 h-8 bg-white/80 dark:bg-slate-900/80 backdrop-blur rounded-full flex items-center justify-center text-slate-400 hover:text-accent transition-colors">
<span class="material-symbols-outlined text-xl">favorite</span>
</button>
</div>
<div class="p-4">
<p class="text-xs text-slate-500 uppercase tracking-widest mb-1">Moda</p>
<h4 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-2">Mochila Urbana de Cuero Sintético</h4>
<div class="flex items-center gap-2 mb-4">
<span class="text-accent font-bold text-lg">$41.65</span>
<span class="text-slate-400 line-through text-sm">$49.00</span>
</div>
<button class="w-full bg-slate-900 dark:bg-primary text-white py-2 rounded-lg font-medium text-sm hover:opacity-90 transition-opacity">
                            Añadir al carrito
                        </button>
</div>
</div>
</div>
<div class="mt-12 flex justify-center">
<nav class="flex items-center gap-2">
<button class="w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
<span class="material-symbols-outlined">chevron_left</span>
</button>
<button class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary text-white font-bold">1</button>
<button class="w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">2</button>
<button class="w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">3</button>
<span class="px-2 text-slate-400">...</span>
<button class="w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">8</button>
<button class="w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
<span class="material-symbols-outlined">chevron_right</span>
</button>
</nav>
</div>
</div>
</div>
</main>
</body></html>