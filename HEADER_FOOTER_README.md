# CRUD Header y Footer - Guía de Uso

## 📋 Descripción General

Se ha implementado un sistema CRUD (Create, Read, Update, Delete) completo para gestionar el Header y Footer de tu sitio web. Ahora puedes editar estos elementos directamente desde el panel de administración sin necesidad de modificar archivos HTML.

## 🚀 Características

✅ **Interfaz Intuitiva**: Panel de configuración integrado en el Dashboard  
✅ **Edición Visual**: Editor HTML con vista previa en tiempo real  
✅ **Validación**: Campos no pueden estar vacíos  
✅ **Control de Estado**: Activa/Desactiva header y footer  
✅ **Base de Datos**: Almacenamiento persistente  
✅ **API RESTful**: Endpoint para el CRUD  
✅ **Caché**: Sistema de caché para mejor rendimiento  

## 📁 Archivos Creados/Modificados

```
├── database/
│   └── agregar_header_footer.sql       (Script SQL - crear tabla)
│
├── api/
│   └── api_header_footer.php           (API CRUD)
│
├── core/
│   └── header_footer_helper.php        (Funciones auxiliares)
│
├── admin/
│   └── configuracion.php               (Panel modificado - nuevo tab)
│
└── setup_header_footer.php             (Script de instalación)
```

## 📖 Pasos de Instalación

### 1. Crear la Tabla en la Base de Datos

**Opción A: Usando el Script Setup**
```
1. Abre tu navegador
2. Ve a: http://localhost/PAGINA-WED/setup_header_footer.php
3. Si ve un mensaje de éxito, la tabla se creó correctamente
```

**Opción B: Comando MySQL**
```bash
mysql -u root negocio_web < database/agregar_header_footer.sql
```

### 2. Verificar la instalación

Accede al panel de administración:
```
http://localhost/PAGINA-WED/admin/configuracion.php
```

Deberías ver un nuevo tab llamado "Header y Footer" 🎉

## 🎯 Cómo Usar el CRUD

### Acceso al Panel

1. Inicia sesión como Administrador
2. Ve a **Configuración** → **Header y Footer**

### Editar Header

1. En la sección **Header**, verás un editor de texto
2. Puedes editar:
   - **Título**: Nombre o identificador del header (opcional)
   - **Código HTML**: El HTML completo del header
   - **Estado**: Activo/Inactivo

3. **Ejemplo de HTML básico para el header:**
```html
<nav class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Mi Tienda</h1>
            <ul class="hidden md:flex gap-6">
                <li><a href="/" class="hover:text-cyan-600">Inicio</a></li>
                <li><a href="/categoria" class="hover:text-cyan-600">Productos</a></li>
                <li><a href="/contacto" class="hover:text-cyan-600">Contacto</a></li>
            </ul>
        </div>
    </div>
</nav>
```

### Editar Footer

Similar al header, tienes control completo sobre el footer.

**Ejemplo de HTML para el footer:**
```html
<footer class="bg-gray-800 text-white py-8 mt-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="font-bold mb-4">Sobre Nosotros</h3>
                <p class="text-gray-400">Somos tu mejor opción en línea.</p>
            </div>
            <div>
                <h3 class="font-bold mb-4">Enlaces Rápidos</h3>
                <ul class="text-gray-400">
                    <li><a href="/">Inicio</a></li>
                    <li><a href="/productos">Productos</a></li>
                    <li><a href="/contacto">Contacto</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold mb-4">Contacto</h3>
                <p class="text-gray-400">Email: info@mitienda.com</p>
                <p class="text-gray-400">Teléfono: +123 4567890</p>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2024 Mi Tienda. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>
```

### Botones de Acción

- **Guardar Header/Footer**: Almacena los cambios
- **Recargar**: Recarga los datos desde la base de datos (descarta cambios no guardados)

### Previsualización

En la sección **Previsualización** puedes ver cómo se vería el header y footer antes de guardar.

## 🔌 Integración en tus Páginas

Para mostrar el Header y Footer en tus páginas:

### Método 1: Usando las funciones auxiliares

**En la parte superior de tu archivo PHP:**
```php
<?php
require_once 'core/header_footer_helper.php';
?>
```

**Para mostrar el header en el inicio del body:**
```php
<?php mostrar_header(); ?>
```

**Para mostrar el footer al final del body:**
```php
<?php mostrar_footer(); ?>
```

### Método 2: Complete Example

```php
<?php
require_once 'core/header_footer_helper.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- Header dinámico -->
    <?php mostrar_header(); ?>

    <!-- Contenido de tu página -->
    <main class="container mx-auto px-4 py-8">
        <h1>Bienvenido</h1>
        <!-- Tu contenido aquí -->
    </main>

    <!-- Footer dinámico -->
    <?php mostrar_footer(); ?>
</body>
</html>
```

### Método 3: Usando caché (Recomendado para mejor rendimiento)

```php
<?php
require_once 'core/header_footer_helper.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Tienda</title>
</head>
<body>
    <?php mostrar_header_cache(); ?>
    
    <!-- Contenido -->
    
    <?php mostrar_footer_cache(); ?>
</body>
</html>
```

## 🌐 Ejemplos de Integración en Archivos Existentes

### Para `index.php`
```php
<?php
require_once 'core/sesiones.php';
require_once 'core/conexion.php';
require_once 'core/header_footer_helper.php';
// ... tu código ...
?>
<!DOCTYPE html>
<html>
<head>
    <!-- tus styles -->
</head>
<body>
    <?php mostrar_header_cache(); ?>
    
    <!-- Tu contenido actual del index -->
    
    <?php mostrar_footer_cache(); ?>
</body>
</html>
```

### Para `client/categoria.php`
```php
<?php
require_once '../core/sesiones.php';
require_once '../core/conexion.php';
require_once '../core/header_footer_helper.php';
?>
<!DOCTYPE html>
<html>
<head>
    <!-- tus styles -->
</head>
<body>
    <?php mostrar_header_cache(); ?>
    
    <!-- Tu contenido de categorías -->
    
    <?php mostrar_footer_cache(); ?>
</body>
</html>
```

## 📊 Estructura de la Base de Datos

```sql
CREATE TABLE `header_footer` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `tipo` enum('header','footer') UNIQUE,
  `titulo` varchar(200),
  `contenido` longtext NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp()
)
```

## 🔌 API Endpoints

### Obtener Header/Footer
```
GET /api/api_header_footer.php?accion=obtener&tipo=header
GET /api/api_header_footer.php?accion=obtener&tipo=footer

Response:
{
  "exito": true,
  "datos": {
    "id": 1,
    "tipo": "header",
    "titulo": "Mi Tienda",
    "contenido": "...",
    "estado": "activo",
    "fecha_creacion": "2024-01-01 12:00:00"
  }
}
```

### Guardar/Actualizar
```
POST /api/api_header_footer.php

Body (form-data):
- accion: guardar
- tipo: header|footer
- titulo: Mi Tienda (opcional)
- contenido: <html>...</html>
- estado: activo|inactivo
```

### Eliminar (Limpiar contenido)
```
GET /api/api_header_footer.php?accion=eliminar&tipo=header
```

## ✨ Características Avanzadas

### Usar Tailwind CSS en Header/Footer
```html
<nav class="flex justify-between items-center p-4 bg-gradient-to-r from-cyan-600 to-blue-600">
    <h1 class="text-white text-3xl font-bold">Mi Tienda</h1>
    <ul class="flex gap-6">
        <li><a href="/" class="text-white hover:underline">Inicio</a></li>
        <li><a href="/productos" class="text-white hover:underline">Productos</a></li>
    </ul>
</nav>
```

### Usar Variables PHP en Header/Footer

Lamentablemente NO se pueden usar variables PHP dinámicas directamente en el editor, pero puedes:

**Option 1: Usar un archivo PHP intermedio**
```php
<?php
$config = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1"));
?>
<?php require_once 'core/header_footer_helper.php'; ?>
<!-- Tu HTML con variables de PHP -->
```

**Option 2: Usar JavaScript para contenido dinámico**
```html
<nav id="header-dynamic">
    <script>
        fetch('/api/api_config.php').then(r => r.json()).then(data => {
            document.getElementById('header-dynamic').innerHTML = data.header_html;
        });
    </script>
</nav>
```

## 🐛 Troubleshooting

### El header/footer no aparece
1. Verifica que el estado sea "Activo"
2. Asegúrate de que el contenido no esté vacío
3. Recarga la página (Ctrl+F5 para borrar caché)

### Ver errores en la consola del navegador
1. Abre DevTools (F12)
2. Ve a la pestaña "Console"
3. Verifica si hay mensajes de error

### Verificar que la tabla existe
```sql
SELECT * FROM header_footer;
```

### Por si necesitas resetear todo
```sql
DELETE FROM header_footer;
INSERT INTO `header_footer` (`tipo`, `contenido`, `estado`) 
VALUES 
('header', '<nav>Tu header aquí</nav>', 'activo'),
('footer', '<footer>Tu footer aquí</footer>', 'activo');
```

## 📝 Notas Importantes

⚠️ **Validaciones Activas**:
- No se puede guardar un header/footer vacío
- Solo administradores pueden editar

⚠️ **Performance**:
- Se recomienda usar `mostrar_header_cache()` y `mostrar_footer_cache()` para mejor rendimiento
- Las consultas se cachean durante la misma solicitud

⚠️ **Seguridad**:
- El HTML se almacena tal cual (sin filtrado)
- Para más seguridad, considera agregar un filtro de HTML permitido

## 🎓 Ejemplo Completo

Mira el archivo `admin/configuracion.php` para ver cómo está implementada la interfaz del CRUD.

## 📞 Soporte

Si encuentras algún problema:
1. Revisa que la tabla se creó correctamente
2. Verifica que tienes permisos de administrador
3. Comprueba que el archivo `api/api_header_footer.php` existe
4. Revisa la consola del navegador para errores

---

**¡Listo!** Ya tienes un CRUD funcional para Header y Footer. 🎉
