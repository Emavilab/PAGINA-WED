<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Catálogo de Productos</title>

    <!-- Tailwind with project colors -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#137fec",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
            font-size: 24px;
        }
    </style>
</head>
<body class="bg-white dark:bg-background-dark text-slate-900 dark:text-slate-100">
    <main class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-4">Nuestros Productos</h1>
        <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
    </main>

    <script>
        async function loadProducts() {
            try {
                const res = await fetch('../api/obtener_productos.php');
                const data = await res.json();
                const grid = document.getElementById('product-grid');
                if (!Array.isArray(data) || data.length === 0) {
                    grid.innerHTML = '<p class="text-center col-span-full text-gray-500">No hay productos disponibles.</p>';
                    return;
                }
                data.forEach(p => {
                    const card = document.createElement('div');
                    card.className = 'bg-white dark:bg-slate-800 rounded-lg shadow hover:shadow-lg transition overflow-hidden flex flex-col';
                    const imgUrl = p.imagen_principal ? `../${p.imagen_principal}` : 'https://via.placeholder.com/300x200?text=Sin+imagen';
                    const precioHtml = (p.precio_descuento && parseFloat(p.precio_descuento) > 0)
                        ? `<p class="text-gray-400 line-through text-sm">$${parseFloat(p.precio).toFixed(2)}</p><p class="text-primary font-bold text-xl">$${parseFloat(p.precio_descuento).toFixed(2)}</p>`
                        : `<p class="text-primary font-bold text-xl">$${parseFloat(p.precio).toFixed(2)}</p>`;
                    card.innerHTML = `
                        <img src="${imgUrl}" alt="${p.nombre}" class="w-full h-48 object-cover" />
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <h2 class="font-semibold text-lg mb-1">${p.nombre}</h2>
                                <p class="text-xs text-gray-500 mb-2">${p.categoria_nombre || ''}</p>
                                ${precioHtml}
                            </div>
                            <button class="mt-4 inline-flex items-center justify-center gap-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                                <span class="material-symbols-outlined">add_shopping_cart</span> Agregar
                            </button>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            } catch (err) {
                console.error(err);
                document.getElementById('product-grid').innerHTML = '<p class="col-span-full text-center text-red-500">Error cargando productos.</p>';
            }
        }
        loadProducts();
    </script>
</body>
</html>