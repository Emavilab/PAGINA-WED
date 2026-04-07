# 🛒 ControlPlus — Sistema de Tienda en Línea

Sistema de comercio electrónico completo desarrollado en **PHP** con panel administrativo, tienda cliente, API REST interna y gestión integral de pedidos, productos, clientes, reportes y configuración del negocio.

---

## 📋 Tabla de Contenidos

- [Descripción General](#descripción-general)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Requisitos del Sistema](#requisitos-del-sistema)
- [Instalación y Configuración](#instalación-y-configuración)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Base de Datos](#base-de-datos)
- [Roles y Permisos](#roles-y-permisos)
- [Módulos del Sistema](#módulos-del-sistema)
  - [Panel Administrativo](#panel-administrativo)
  - [Tienda Cliente](#tienda-cliente)
  - [API Interna](#api-interna)
  - [Core / Sistema](#core--sistema)
- [Funcionalidades Clave](#funcionalidades-clave)
- [Sistema de Sesiones](#sistema-de-sesiones)
- [Correo Electrónico](#correo-electrónico)
- [Seguridad](#seguridad)
- [Configuración Dinámica del Negocio](#configuración-dinámica-del-negocio)

---

## Descripción General

**ControlPlus** es una plataforma web de e-commerce diseñada para negocios que necesiten vender en línea de forma completa. El sistema incluye:

- **Tienda pública** con catálogo de productos, buscador, ofertas, filtros por categoría y marca.
- **Carrito de compras** persistente por sesión de cliente.
- **Proceso de checkout** con selección de dirección de envío, método de envío, método de pago y carga de comprobante.
- **Panel administrativo** con Dashboard estadístico, gestión de productos, pedidos, clientes, compras y reportes.
- **Sistema de configuración centralizado** que controla colores, textos, redes sociales, menú, footer y más, todo desde la base de datos sin tocar código.
- **Sistema de sesiones con timeout** y modal de advertencia para el área administrativa.
- **Envío de correos** transaccionales mediante Gmail SMTP (PHPMailer).

---

## Tecnologías Utilizadas

| Categoría | Tecnología |
|---|---|
| Lenguaje Backend | PHP 8+ |
| Base de Datos | MySQL / MariaDB 10.4+ |
| Servidor Local | XAMPP (Apache) |
| Estilos CSS | Tailwind CSS (CDN) |
| Iconos | Material Icons, Material Symbols Outlined (Google Fonts) |
| Iconos adicionales | Font Awesome 6.4 |
| Tipografía | Inter (Google Fonts) |
| Email | PHPMailer 6 (Gmail SMTP) |
| Frontend | JavaScript vanilla (sin frameworks) |
| Exportación | PhpSpreadsheet / exportación a Excel |
| Charset | UTF-8 / utf8mb4 |

---

## Requisitos del Sistema

- **PHP** 8.0 o superior
- **MySQL** 5.7+ / **MariaDB** 10.4+
- **Apache** 2.4+ (XAMPP recomendado)
- Extensión **MySQLi** habilitada en PHP
- Extensión **mbstring** habilitada en PHP
- Conexión a Internet (para cargar Tailwind CSS y Google Fonts desde CDN)
- Cuenta Gmail con **Contraseña de Aplicación** para el módulo de correos

---

## Instalación y Configuración

### 1. Colocar el proyecto

```
Copiar la carpeta del proyecto a: C:\xampp\htdocs\PAGINA WED\
```

### 2. Importar la base de datos

1. Abrir **phpMyAdmin** en `http://localhost/phpmyadmin`
2. Crear una base de datos llamada `negocio_web`
3. Importar el archivo `database/negocio_web.sql`

### 3. Verificar la conexión

Configurar el archivo `.env` en la raíz del proyecto y verificar que `core/env_loader.php` esté cargando las variables correctamente:

```bash
DB_HOST=localhost
DB_USER=usuario_bd
DB_PASSWORD=contraseña_segura
DB_NAME=negocio_web
```

### 4. Configurar el correo SMTP

Configurar las variables SMTP en `.env`:

```bash
SMTP_USER=tu_correo@gmail.com
SMTP_PASSWORD=tu_contraseña_de_aplicacion
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
```

> **Nota:** Debes generar una "Contraseña de Aplicación" en tu cuenta de Google en: Seguridad → Verificación en dos pasos → Contraseñas de aplicaciones.

> **Importante:** El sistema ya usa carga centralizada de variables de entorno, así que no debes dejar credenciales reales hardcodeadas en `core/conexion.php` ni en `core/smtp_config.php`.

### 5. Acceder al sistema

- **Tienda pública:** `http://localhost/PAGINA%20WED/`
- **Panel Administrativo:** `http://localhost/PAGINA%20WED/admin/Dashboard.php`
- **Login:** `http://localhost/PAGINA%20WED/pages/login.php`

### 6. Ejecutar con Docker (Windows)

Este proyecto incluye configuración lista para Docker Compose manteniendo la ruta actual del sistema (`/PAGINA%20WED`) para no romper rutas existentes.

1. Asegúrate de tener Docker Desktop abierto.
2. En la raíz del proyecto, ajustar `.env` con estos valores de base de datos:

```bash
DB_HOST=db
DB_USER=controlplus
DB_PASSWORD=controlplus123
DB_NAME=negocio_web
```

3. Levantar contenedores:

```bash
docker compose up -d --build
```

4. Acceder:

- App: `http://localhost:8080/PAGINA%20WED/`
- Admin: `http://localhost:8080/PAGINA%20WED/admin/Dashboard.php`
- phpMyAdmin: `http://localhost:8081` (usuario `root`, clave `root`)

5. Detener servicios:

```bash
docker compose down
```

> Si quieres reiniciar también la base de datos desde cero, usar `docker compose down -v`.

---

## Estructura del Proyecto

```
PAGINA WED/
│
├── index.php                    ← Página principal de la tienda
│
├── admin/                       ← Panel administrativo
│   ├── Dashboard.php            ← Dashboard con estadísticas
│   ├── gestion_productos.php    ← CRUD de productos
│   ├── configuracion.php        ← Configuración del negocio
│   ├── pedidosadmin.php         ← Gestión de pedidos
│   ├── pedidos_contenido.php    ← Detalle de pedidos
│   ├── usuarios.php             ← Gestión de usuarios admin
│   ├── admin_reportes.php       ← Reportes e inventario
│   ├── admin_compras.php        ← Registro de compras/proveedores
│   ├── admin_bancos.php         ← Gestión de bancos
│   ├── admin_exportar exel.php  ← Exportar datos a Excel
│   ├── exportar_excel.php       ← Generación de archivo Excel
│   ├── cambiar_estado.php       ← Cambio de estado de pedidos
│   └── ...
│
├── api/                         ← Endpoints REST internos
│   ├── api_carrito.php          ← CRUD del carrito de compras
│   ├── api_crear_pedido.php     ← Crear nuevo pedido
│   ├── api_productos.php        ← Consulta de productos
│   ├── api_categorias.php       ← Listado de categorías
│   ├── api_lista_deseos.php     ← Lista de deseos
│   ├── api_mensajeria.php       ← Mensajes de contacto
│   ├── api_metodos_envio.php    ← Métodos de envío
│   ├── api_metodos_pago.php     ← Métodos de pago
│   ├── api_bancos.php           ← Datos bancarios para pago
│   ├── validar_login.php        ← Autenticación
│   ├── registrar_usuario.php    ← Registro de nuevos usuarios
│   └── ...
│
├── client/                      ← Páginas del cliente autenticado
│   ├── carrito.php              ← Vista del carrito
│   ├── finalizarcompra.php      ← Proceso de checkout
│   ├── historialpedidoC.php     ← Historial de pedidos
│   ├── listadedeseo.php         ← Lista de deseos
│   ├── perfil.php               ← Perfil del cliente
│   ├── mensajeria.php           ← Centro de mensajes
│   └── ...
│
├── core/                        ← Núcleo del sistema
│   ├── conexion.php             ← Conexión a la base de datos
│   ├── sesiones.php             ← Gestión de sesiones y autenticación
│   ├── smtp_config.php          ← Configuración de correo SMTP
│   ├── validador_inactividad.php← Validación de inactividad de sesión
│   ├── actualizar_actividad.php ← Endpoint AJAX para mantener sesión viva
│   ├── modal_advertencia_sesion.html ← Modal HTML de timeout
│   ├── destruir_sesion.php      ← Cerrar sesión y redirigir
│   ├── cerrar_sesion.php        ← Logout completo
│   ├── verificar_sesion.php     ← Verificación rápida de sesión
│   └── procesar_configuracion.php ← Guardar configuración del negocio
│
├── pages/                       ← Páginas públicas
│   ├── login.php                ← Inicio de sesión
│   ├── crear_cuenta.php         ← Registro de nuevos clientes
│   ├── contactanos.php          ← Formulario de contacto
│   ├── ofertas.php              ← Página de ofertas
│   └── guardar_mensaje.php      ← Procesar mensajes de contacto
│
├── js/                          ← Scripts JavaScript
│   ├── advertencia_sesion.js    ← Lógica del timeout con modal
│   └── mantener_sesion.js       ← AJAX para mantener sesión activa
│
├── img/                         ← Imágenes del sistema
│   ├── bancos/                  ← Logos de bancos
│   ├── banners/                 ← Imágenes de banners promocionales
│   ├── comprobantes/            ← Comprobantes de pago subidos
│   ├── marcas/                  ← Logos de marcas
│   ├── productos/               ← Fotografías de productos
│   └── slides/                  ← Imágenes del hero slideshow
│
├── vendor/                      ← Librerías de terceros
│   └── PHPMailer/src/           ← PHPMailer para envío de correos
│
└── database/
    └── negocio_web.sql          ← Script SQL de la base de datos
```

---

## Base de Datos

Nombre de la base de datos: **`negocio_web`**

Motor: **InnoDB** | Charset: **utf8mb4** | Collation: **utf8mb4_general_ci**

### Tablas del Sistema

| Tabla | Descripción |
|---|---|
| `usuarios` | Usuarios del sistema (admin, vendedor, cliente) |
| `roles` | Roles: Administrador, Vendedor, Cliente |
| `clientes` | Datos adicionales de los clientes (vinculado a `usuarios`) |
| `productos` | Catálogo de productos con precio, stock, oferta, etc. |
| `producto_imagenes` | Imágenes múltiples por producto con orden |
| `categorias` | Categorías jerárquicas (padre-hijo) con tasa de impuesto |
| `marcas` | Marcas de productos con logo |
| `carritos` | Carritos de compra activos por cliente |
| `carrito_detalle` | Ítems dentro de cada carrito |
| `pedidos` | Pedidos realizados con estado, totales y método de pago |
| `detalle_pedido` | Productos incluidos en cada pedido con impuestos |
| `direcciones_cliente` | Direcciones de entrega del cliente |
| `departamentos_envio` | Regiones/departamentos con costo de envío y días de entrega |
| `metodos_envio` | Servicios de envío disponibles |
| `metodos_pago` | Métodos de pago disponibles |
| `bancos` | Cuentas bancarias para recibir transferencias |
| `tipos_cuenta_banco` | Tipos de cuenta bancaria (Ahorro, Corriente, etc.) |
| `lista_deseos` | Productos guardados como favoritos por cliente |
| `mensajes_contacto` | Mensajes del formulario de contacto con estados |
| `banners` | Banners promocionales del sitio |
| `hero_slides` | Slides del hero / carrusel principal |
| `compras` | Registro de compras a proveedores |
| `detalle_compra` | Detalle de productos en cada compra de proveedor |
| `configuracion` | Configuración general del negocio (una sola fila) |

### Diagrama de Relaciones Principales

```
usuarios ──── clientes ──── carritos ──── carrito_detalle ──── productos
                │                                                    │
                ├── pedidos ──── detalle_pedido ────────────────────┘
                │       │
                │       ├── direcciones_cliente ── departamentos_envio
                │       ├── metodos_envio
                │       └── metodos_pago
                │
                └── lista_deseos ── productos

productos ── categorias (jerarquía padre-hijo)
productos ── marcas
productos ── producto_imagenes
```

### Estados de los Pedidos

```
pendiente → confirmado → enviado → entregado
                   └──────────────→ cancelado
```

### Monedas Soportadas

| Código | Símbolo | País |
|---|---|---|
| HNL | L | Honduras |
| USD | $ | Estados Unidos |
| GTQ | Q | Guatemala |
| CRC | ₡ | Costa Rica |
| MXN | $ | México |
| COP | $ | Colombia |
| ARS | $ | Argentina |
| EUR | € | Unión Europea |

---

## Roles y Permisos

| ID | Rol | Permisos |
|---|---|---|
| 1 | **Administrador** | Acceso total: configuración, usuarios, productos, reportes, compras, pedidos, mensajería |
| 2 | **Vendedor** | Acceso al Dashboard, productos, pedidos, clientes; sin acceso a configuración general de usuarios |
| 3 | **Cliente** | Tienda pública, carrito, pedidos propios, perfil, lista de deseos, mensajería |

---

## Módulos del Sistema

### Panel Administrativo

Acceso: `/admin/Dashboard.php` (requiere rol Administrador o Vendedor)

#### 📊 Dashboard
- Tarjetas de resumen: total de productos, clientes, pedidos del día e ingresos del día.
- Gráfico de distribución de estados de pedidos con porcentajes.
- Actividad reciente.
- Navegación lateral dinámica entre todos los módulos.

#### 📦 Gestión de Productos (`gestion_productos.php`)
- Crear, editar y eliminar productos.
- Código único de producto.
- Precio de venta, precio de costo, stock.
- Asignación de categoría y marca.
- Múltiples imágenes por producto con orden configurable.
- Sistema de **ofertas con precio de descuento y fechas de vigencia** (inicio / fin).
- Desactivar ofertas vencidas automáticamente al conectar.
- Estado: disponible / agotado.

#### 🗂️ Categorías
- Estructura jerárquica (categoría padre → subcategorías).
- Tasa de impuesto por categoría (se hereda al hijo si no está definida).
- Icono personalizable por categoría.

#### 🛍️ Pedidos (`pedidosadmin.php`)
- Listado de todos los pedidos con filtros por estado.
- Ver detalle completo de cada pedido (productos, cantidades, precios, impuestos).
- Ver comprobante de pago subido por el cliente.
- Cambiar estado del pedido (pendiente → confirmado → enviado → entregado / cancelado).

#### 👥 Clientes
- Listado de clientes registrados.
- Obtener información, editar y eliminar cuentas.

#### 👤 Usuarios Admin (`usuarios.php`)
- Crear nuevos usuarios con rol Administrador o Vendedor.
- Editar y eliminar usuarios del panel administrativo.

#### 📈 Reportes (`admin_reportes.php`)
- Total de productos registrados.
- Total de unidades en inventario.
- Listado de **productos con stock bajo** (alerta de reposición).
- Tabla completa de inventario.
- Exportación a **Excel**.

#### 🏭 Compras a Proveedores (`admin_compras.php`)
- Registrar compras de inventario a proveedores.
- Detalle de productos adquiridos con cantidad y precio de costo.
- Historial de compras.

#### 💬 Mensajería (`admin/pedidosadmin.php` + mensajes)
- Recibir y gestionar mensajes del formulario de contacto.
- Estados: nuevo, leído, respondido, cerrado.
- Responder directamente desde el panel (envío de correo al cliente).

#### 🏦 Bancos (`admin_bancos.php`)
- Gestionar cuentas bancarias para recibir transferencias.
- Logo, nombre, número de cuenta y tipo de cuenta.

#### ⚙️ Configuración (`configuracion.php`)
- **Datos del negocio:** nombre, logo, favicon, teléfono, correo, dirección, slogan.
- **Redes sociales:** Facebook, Instagram, Twitter, WhatsApp (JSON).
- **Tema de colores:** color primario, color primario oscuro, fondo claro, fondo oscuro.
- **Hero principal:** etiqueta, título, subtítulo, descripción, imagen, textos de botones.
- **Menú del header:** ítems personalizables con nombre y URL (JSON).
- **Columnas del footer:** contenido personalizable (JSON).
- **Banner superior:** texto del banner.
- **Pie de página:** texto de copyright.
- **Horario de atención.**
- **Moneda:** multi-moneda configurable.
- **Métodos de envío:** nombre, descripción, costo, reducción de días, estado.
- **Métodos de pago:** nombre, descripción, estado.
- **Departamentos de envío:** nombre, costo de envío, días de entrega.
- **Marcas:** nombre, logo, estado.

---

### Tienda Cliente

#### 🏠 Página Principal (`index.php`)
- Hero dinámico con slides o sección configurada desde el panel.
- Banner superior con texto configurable.
- Catálogo de productos destacados.
- Sección de marcas destacadas con logos.
- Menú de navegación completamente dinámico desde BD.
- Footer con columnas configurables y redes sociales.
- Modo oscuro / claro.

#### 🔍 Búsqueda (`api/api_buscar.php`)
- Búsqueda de productos en tiempo real.
- Filtro por nombre, categoría o marca.

#### 📂 Categorías (`client/categoria.php`)
- Listar productos por categoría y subcategoría.
- Filtros dinámicos.

#### 🛒 Carrito de Compras (`client/carrito.php`)
- Agregar, actualizar cantidad y eliminar productos.
- Carrito persistente en base de datos (no se pierde al cerrar el navegador).
- Cálculo de subtotales, impuestos por categoría y total.
- Requiere estar autenticado como cliente.

#### 💳 Checkout — Finalizar Compra (`client/finalizarcompra.php`)
- Seleccionar dirección de entrega (existente o crear nueva).
- Seleccionar método de envío con costo.
- Costo de envío por departamento/región.
- Seleccionar método de pago.
- Si el pago es por transferencia: mostrar bancos disponibles y subir comprobante.
- Resumen del pedido con subtotal, impuestos, envío y total.
- Creación del pedido en base de datos al confirmar.

#### ❤️ Lista de Deseos (`client/listadedeseo.php`)
- Guardar productos como favoritos.
- Ver y gestionar la lista desde el perfil.
- Agregar directamente al carrito desde la lista de deseos.

#### 📦 Historial de Pedidos (`client/historialpedidoC.php`)
- Ver todos los pedidos realizados.
- Detalle de cada pedido: productos, precios, estado.
- Cancelar pedidos en estado "pendiente".

#### 👤 Perfil del Cliente (`client/perfil.php`)
- Ver y editar datos personales.
- Cambiar contraseña.
- Gestionar direcciones de entrega (crear, editar, eliminar).
- Eliminar cuenta.

#### 💬 Mensajería (`client/mensajeria.php`)
- Enviar mensajes al equipo del negocio.
- Ver el estado de las respuestas.

#### 📄 Páginas Públicas
- **Contacto** (`pages/contactanos.php`): Formulario de contacto público.
- **Ofertas** (`pages/ofertas.php`): Productos en oferta activos.
- **Crear cuenta** (`pages/crear_cuenta.php`): Registro de nuevos clientes.
- **Login** (`pages/login.php`): Inicio de sesión.

---

### API Interna

Todos los endpoints devuelven **JSON** con el formato:
```json
{
  "exito": true/false,
  "mensaje": "...",
  "datos": { ... }
}
```

| Endpoint | Método | Descripción |
|---|---|---|
| `api/validar_login.php` | POST | Autenticar usuario |
| `api/registrar_usuario.php` | POST | Registrar nuevo cliente |
| `api/api_carrito.php` | GET/POST | CRUD del carrito de compras |
| `api/api_crear_pedido.php` | POST | Crear nuevo pedido |
| `api/api_cancelar_pedido.php` | POST | Cancelar pedido pendiente |
| `api/api_productos.php` | GET | Consultar productos con filtros |
| `api/obtener_productos.php` | GET | Obtener listado de productos |
| `api/api_categorias.php` | GET | Listado de categorías |
| `api/obtener_categorias.php` | GET | Categorías principales |
| `api/obtener_categorias_hijas.php` | GET | Subcategorías por padre |
| `api/obtener_subcategorias.php` | GET | Subcategorías |
| `api/api_lista_deseos.php` | GET/POST | Gestión de lista de deseos |
| `api/api_metodos_envio.php` | GET | Métodos de envío activos |
| `api/api_metodos_pago.php` | GET | Métodos de pago activos |
| `api/api_bancos.php` | GET | Bancos disponibles |
| `api/api_envio_departamento.php` | GET | Costo de envío por departamento |
| `api/api_obtener_departamentos.php` | GET | Lista de departamentos |
| `api/api_crear_direccion.php` | POST | Crear nueva dirección |
| `api/api_editar_direccion.php` | POST | Editar dirección existente |
| `api/api_eliminar_direccion.php` | POST | Eliminar dirección |
| `api/api_obtener_direcciones.php` | GET | Direcciones del cliente |
| `api/api_mensajeria.php` | GET/POST | Enviar y ver mensajes |
| `api/api_buscar.php` | GET | Buscar productos |
| `api/api_usuario.php` | GET | Datos del usuario autenticado |
| `api/api_actualizar_perfil.php` | POST | Actualizar perfil del cliente |
| `api/api_cambiar_contraseña.php` | POST | Cambiar contraseña |
| `api/api_eliminar_cuenta.php` | POST | Eliminar cuenta del cliente |
| `api/obtener_banners.php` | GET | Banners activos del sitio |
| `api/obtener_hero_slides.php` | GET | Slides del hero |
| `api/obtener_configuracion.php` | GET | Configuración pública del negocio |

---

### Core / Sistema

| Archivo | Función |
|---|---|
| `core/conexion.php` | Conexión MySQLi, charset UTF-8, desactiva ofertas vencidas automáticamente |
| `core/sesiones.php` | Gestión completa de sesiones: login, logout, validación de rol, obtener datos de usuario |
| `core/smtp_config.php` | Configuración SMTP y función `enviarCorreo()` usando PHPMailer + Gmail |
| `core/validador_inactividad.php` | Valida inactividad de sesión; cierra sesión si supera el tiempo límite |
| `core/actualizar_actividad.php` | Endpoint AJAX que actualiza el timestamp de actividad del usuario |
| `core/modal_advertencia_sesion.html` | HTML del modal de advertencia de cierre de sesión con animaciones |
| `core/destruir_sesion.php` | Destruye la sesión PHP y redirige al inicio |
| `core/cerrar_sesion.php` | Cierre de sesión con limpieza completa |
| `core/verificar_sesion.php` | Verificación rápida de sesión activa |
| `core/procesar_configuracion.php` | Procesa y guarda la configuración del negocio desde el formulario admin |

---

## Funcionalidades Clave

### Sistema de Ofertas
- Producto puede tener `precio_descuento` con `fecha_inicio_oferta` y `fecha_fin_oferta`.
- Al conectarse a la BD se ejecuta automáticamente:
  ```sql
  UPDATE productos SET en_oferta = 0, precio_descuento = NULL 
  WHERE en_oferta = 1 AND fecha_fin_oferta < CURDATE()
  ```
- El carrito y el checkout respetan el precio de oferta si está activo.

### Sistema de Impuestos
- Cada categoría puede tener una `tasa_impuesto` (%).
- Si la subcategoría no tiene tasa, hereda la del padre.
- El impuesto se calcula por ítem en el carrito y en el detalle del pedido.

### Carrito Persistente
- El carrito se guarda en base de datos (tablas `carritos` + `carrito_detalle`).
- Solo un carrito activo por cliente.
- El carrito no se pierde al cerrar navegador.

### Sistema de Envío Multi-Región
- Costos de envío configurables por departamento/región.
- Cada método de envío puede reducir los días de entrega estimados.

### Subida de Comprobantes de Pago
- Al pagar por transferencia, el cliente sube una imagen del comprobante.
- Los archivos se almacenan en `img/comprobantes/`.
- El administrador puede ver el comprobante desde el panel de pedidos.

### Configuración Visual Dinámica
- Los **4 colores** del tema (primario, primario oscuro, fondo claro, fondo oscuro) se almacenan en la BD y se inyectan en Tailwind CSS mediante JavaScript al cargar la página.
- El cambio de colores aplica **instantáneamente** sin desplegar código.

### Jerarquía de Categorías
- Las categorías pueden tener un `id_padre`, permitiendo una estructura de dos niveles: **Categoría → Subcategoría**.
- Los productos se asignan a la categoría más específica disponible.

---

## Sistema de Sesiones

El sistema implementa un mecanismo de **timeout de sesión por inactividad** exclusivo para el área administrativa:

### Flujo de Timeout
```
0:00  ── Sesión iniciada / actividad detectada
2:00  ── Sin actividad → aparece el modal de advertencia
2:30  ── Sin respuesta → sesión destruida, redirige a index.php
```

### Componentes

| Componente | Rol |
|---|---|
| `js/advertencia_sesion.js` | Detecta eventos del usuario (click, scroll, teclado, touch) y gestiona los timers |
| `core/actualizar_actividad.php` | Recibe peticiones AJAX para actualizar `$_SESSION['ultima_actividad']` |
| `core/modal_advertencia_sesion.html` | Modal visual con cuenta regresiva, animaciones y botón "Seguir activo" |
| `core/validador_inactividad.php` | PHP que valida en cada carga del Dashboard si la sesión expiró |
| `core/destruir_sesion.php` | Destruye la sesión y redirige al inicio |

### Constantes de Tiempo
```javascript
TIEMPO_SESION      = 90 segundos  // 2 minutos de inactividad
TIEMPO_ADVERTENCIA =  30 segundos  // 30 segundos de aviso antes de cerrar
```

---

## Correo Electrónico

El sistema usa **PHPMailer** para enviar correos a través de **Gmail SMTP (TLS, puerto 587)**.

La función principal disponible globalmente:

```php
enviarCorreo(
    $destinatario,   // correo del cliente o admin
    $asunto,         // asunto del correo
    $cuerpoHtml,     // HTML del correo
    $cuerpoTexto     // texto plano alternativo (opcional)
);
```

**Retorna:**
```php
['exito' => true/false, 'mensaje' => '...']
```

**Usos en el sistema:**
- Confirmación de registro de cuenta.
- Notificación de nuevo pedido.
- Respuestas a mensajes de contacto desde el panel administrativo.
- Notificaciones de cambio de estado de pedido.

---

## Seguridad

| Medida | Implementación |
|---|---|
| **Inyección SQL** | Uso de `prepared statements` con `bind_param()` en toda la API |
| **XSS** | `htmlspecialchars()` en todos los datos mostrados en HTML |
| **Control de acceso** | Verificación de rol en cada página protegida antes de cargar contenido |
| **Sesiones seguras** | `session_start()` en único punto central (`core/sesiones.php`) |
| **Contraseñas** | Almacenadas con hash (bcrypt/password_hash) |
| **Timeout de sesión** | Sistema de inactividad con cierre automático en área admin |
| **Validación de colores** | Regex `/^#[0-9A-Fa-f]{6}$/` para evitar inyección en CSS dinámico |
| **Errores en API** | En modo producción, los errores de BD devuelven JSON limpio sin exponer detalles internos |
| **Charset** | `utf8mb4` en BD + `charset=UTF-8` en todas las cabeceras para evitar problemas de encoding |

---

## Configuración Dinámica del Negocio

La tabla `configuracion` (una sola fila, `id_config = 1`) controla:

| Campo | Descripción |
|---|---|
| `nombre_negocio` | Nombre que aparece en el título y header |
| `logo` | Ruta al logo del negocio |
| `favicon` | Ruta al favicon del sitio |
| `slogan` | Slogan del negocio |
| `correo` | Correo de contacto mostrado en el sitio |
| `telefono` | Teléfono de contacto |
| `direccion` | Dirección física del negocio |
| `horario_atencion` | Horario mostrado en el footer / contacto |
| `moneda` | Código de moneda (HNL, USD, etc.) |
| `redes_sociales` | JSON con URLs de redes sociales |
| `header_menu` | JSON con ítems del menú de navegación |
| `footer_columns` | JSON con columnas del footer |
| `texto_banner_superior` | Texto del banner superior del sitio |
| `pie_pagina` | Texto de copyright del footer |
| `hero_etiqueta` | Etiqueta del hero principal |
| `hero_titulo` | Título del hero |
| `hero_subtitulo` | Subtítulo del hero |
| `hero_descripcion` | Descripción del hero |
| `hero_imagen` | Imagen de fondo del hero |
| `hero_btn_primario` | Texto del botón primario del hero |
| `hero_btn_secundario` | Texto del botón secundario del hero |
| `color_primary` | Color primario del tema (hex) |
| `color_primary_dark` | Color primario oscuro del tema (hex) |
| `color_background_light` | Color de fondo claro (hex) |
| `color_background_dark` | Color de fondo oscuro (hex) |

---

*Desarrollado con PHP + MySQL + TailwindCSS — Sistema ControlPlus*
