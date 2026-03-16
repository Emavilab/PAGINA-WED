<?php
require_once 'sesiones.php';
require_once 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

// Verificar autenticación y permisos
if (!usuarioAutenticado()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado: debe iniciar sesión']);
    exit();
}

// Solo admin (rol 1) y vendedor (rol 2) pueden acceder
if ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permisos insuficientes']);
    exit();
}

function responder($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';

    // --- LÓGICA PARA MARCAS ---
    if ($accion == 'guardar_marca') {
        $id = $_POST['id_marca'] ?? '';
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $estado = $_POST['estado'] ?? 'activo';

        if (empty(trim($nombre))) {
            responder(false, 'El nombre de la marca es requerido');
        }

        // Manejar subida de logo
        $logo_nombre = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            $extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $extensiones_permitidas)) {
                responder(false, 'Formato de imagen no permitido. Use: jpg, png, gif, webp o svg');
            }

            // Tamaño máximo 5MB
            if ($_FILES['logo']['size'] > 5 * 1024 * 1024) {
                responder(false, 'La imagen no debe superar los 5MB');
            }

            $carpeta_destino = __DIR__ . '/../img/marcas/';
            if (!is_dir($carpeta_destino)) {
                mkdir($carpeta_destino, 0755, true);
            }

            $logo_nombre = 'marca_' . time() . '_' . uniqid() . '.' . $extension;
            $ruta_completa = $carpeta_destino . $logo_nombre;

            if (!move_uploaded_file($_FILES['logo']['tmp_name'], $ruta_completa)) {
                responder(false, 'Error al subir la imagen del logo');
            }

            // Si estamos editando, eliminar el logo anterior
            if (!empty($id)) {
                $res_logo = mysqli_query($conexion, "SELECT logo FROM marcas WHERE id_marca = " . intval($id));
                if ($row_logo = mysqli_fetch_assoc($res_logo)) {
                    if (!empty($row_logo['logo']) && file_exists($carpeta_destino . $row_logo['logo'])) {
                        unlink($carpeta_destino . $row_logo['logo']);
                    }
                }
            }
        }

        if (empty($id)) {
            $logo_sql = $logo_nombre ? ", logo" : "";
            $logo_val = $logo_nombre ? ", '$logo_nombre'" : "";
            $sql = "INSERT INTO marcas (nombre, estado$logo_sql) VALUES ('$nombre', '$estado'$logo_val)";
        } else {
            $id = intval($id);
            $logo_update = $logo_nombre ? ", logo = '$logo_nombre'" : "";
            $sql = "UPDATE marcas SET nombre = '$nombre', estado = '$estado'$logo_update WHERE id_marca = $id";
        }

        if (mysqli_query($conexion, $sql)) {
            responder(true, empty($_POST['id_marca']) ? 'Marca creada exitosamente' : 'Marca actualizada exitosamente');
        } else {
            responder(false, 'Error en Marcas: ' . mysqli_error($conexion));
        }
    }

    // --- LÓGICA PARA MÉTODOS DE ENVÍO ---
    if ($accion == 'guardar_envio') {
        $id = $_POST['id_envio'] ?? '';
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $costo = mysqli_real_escape_string($conexion, $_POST['costo']);
        $reduccion_dias = intval($_POST['reduccion_dias'] ?? 0);
        $estado = $_POST['estado'] ?? 'activo';
        $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion'] ?? '');

        if (empty(trim($nombre))) {
            responder(false, 'El nombre del método de envío es requerido');
        }

        if (empty($id)) {
            $sql = "INSERT INTO metodos_envio (nombre, costo, reduccion_dias, estado, descripcion) 
                    VALUES ('$nombre', '$costo', $reduccion_dias, '$estado', '$descripcion')";
        } else {
            $id = intval($id);
            $sql = "UPDATE metodos_envio SET 
                    nombre = '$nombre', 
                    costo = '$costo', 
                    reduccion_dias = $reduccion_dias, 
                    estado = '$estado', 
                    descripcion = '$descripcion' 
                    WHERE id_envio = $id";
        }

        if (mysqli_query($conexion, $sql)) {
            responder(true, empty($_POST['id_envio']) ? 'Método de envío creado exitosamente' : 'Método de envío actualizado exitosamente');
        } else {
            responder(false, 'Error en Envío: ' . mysqli_error($conexion));
        }
    }

    // --- LÓGICA PARA MÉTODOS DE PAGO ---
    if ($accion == 'guardar_pago') {
        $id = $_POST['id_pago'] ?? '';
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion'] ?? '');
        $estado = $_POST['estado'] ?? 'activo';

        if (empty(trim($nombre))) {
            responder(false, 'El nombre del método de pago es requerido');
        }

        if (empty($id)) {
            $sql = "INSERT INTO metodos_pago (nombre, descripcion, estado) 
                    VALUES ('$nombre', '$descripcion', '$estado')";
        } else {
            $id = intval($id);
            $sql = "UPDATE metodos_pago SET 
                    nombre = '$nombre', 
                    descripcion = '$descripcion', 
                    estado = '$estado' 
                    WHERE id_metodo_pago = $id";
        }

        if (mysqli_query($conexion, $sql)) {
            responder(true, empty($_POST['id_pago']) ? 'Método de pago creado exitosamente' : 'Método de pago actualizado exitosamente');
        } else {
            responder(false, 'Error en Pago: ' . mysqli_error($conexion));
        }
    }


// ==================== GUARDAR DEPARTAMENTO ENVIO ====================
if ($_POST['accion'] === 'guardar_departamento_envio') {

$id = $_POST['id_departamento'] ?? '';
$nombre = $_POST['nombre_departamento'] ?? '';
$costo = $_POST['costo_envio'] ?? 0;
$dias = $_POST['dias_entrega'] ?? 1;

if($id){

$stmt = $conexion->prepare("
UPDATE departamentos_envio 
SET nombre_departamento=?, costo_envio=?, dias_entrega=? 
WHERE id_departamento=?
");

$stmt->bind_param("sdii",$nombre,$costo,$dias,$id);

}else{

$stmt = $conexion->prepare("
INSERT INTO departamentos_envio 
(nombre_departamento,costo_envio,dias_entrega) 
VALUES (?,?,?)
");

$stmt->bind_param("sdi",$nombre,$costo,$dias);

}

if($stmt->execute()){
responder(true,"Departamento guardado correctamente");
}else{
responder(false,"Error al guardar departamento");
}

}


    // --- LÓGICA PARA CONFIGURACIÓN GENERAL ---
    if ($accion == 'guardar_config_general') {
        $nombre_negocio = mysqli_real_escape_string($conexion, $_POST['nombre_negocio'] ?? '');
        $slogan = mysqli_real_escape_string($conexion, $_POST['slogan'] ?? '');
        $correo = mysqli_real_escape_string($conexion, $_POST['correo'] ?? '');
        $telefono = mysqli_real_escape_string($conexion, $_POST['telefono'] ?? '');
        $direccion = mysqli_real_escape_string($conexion, $_POST['direccion'] ?? '');
        $moneda = mysqli_real_escape_string($conexion, $_POST['moneda'] ?? 'USD');
        $horario_atencion = mysqli_real_escape_string($conexion, $_POST['horario_atencion'] ?? '');
        $texto_inicio = mysqli_real_escape_string($conexion, $_POST['texto_inicio'] ?? '');
        $pie_pagina = mysqli_real_escape_string($conexion, $_POST['pie_pagina'] ?? '');
        $texto_banner_superior = mysqli_real_escape_string($conexion, $_POST['texto_banner_superior'] ?? '');
        $hero_etiqueta = mysqli_real_escape_string($conexion, $_POST['hero_etiqueta'] ?? '');
        $hero_titulo = mysqli_real_escape_string($conexion, $_POST['hero_titulo'] ?? '');
        $hero_subtitulo = mysqli_real_escape_string($conexion, $_POST['hero_subtitulo'] ?? '');
        $hero_descripcion = mysqli_real_escape_string($conexion, $_POST['hero_descripcion'] ?? '');
        $hero_btn_primario = mysqli_real_escape_string($conexion, $_POST['hero_btn_primario'] ?? '');
        $hero_btn_secundario = mysqli_real_escape_string($conexion, $_POST['hero_btn_secundario'] ?? '');

        // Colores del tema (se validan más abajo)
        $color_primary_raw = $_POST['color_primary'] ?? '';
        $color_primary_dark_raw = $_POST['color_primary_dark'] ?? '';
        $color_bg_light_raw = $_POST['color_background_light'] ?? '';
        $color_bg_dark_raw = $_POST['color_background_dark'] ?? '';

        // Menú de navegación del header (JSON) y columnas del footer (JSON)
        $header_menu_raw = $_POST['header_menu_json'] ?? '[]';
        $footer_cols_raw = $_POST['footer_columns_json'] ?? '[]';

        $header_menu_arr = json_decode($header_menu_raw, true);
        if (!is_array($header_menu_arr)) {
            $header_menu_arr = [];
        }
        $footer_cols_arr = json_decode($footer_cols_raw, true);
        if (!is_array($footer_cols_arr)) {
            $footer_cols_arr = [];
        }

        $header_menu_json = mysqli_real_escape_string(
            $conexion,
            json_encode($header_menu_arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
        $footer_cols_json = mysqli_real_escape_string(
            $conexion,
            json_encode($footer_cols_arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        // Redes sociales como JSON
        $redes = json_encode([
            'facebook' => $_POST['red_facebook'] ?? '',
            'instagram' => $_POST['red_instagram'] ?? '',
            'whatsapp' => $_POST['red_whatsapp'] ?? '',
            'tiktok' => $_POST['red_tiktok'] ?? '',
            'twitter' => $_POST['red_twitter'] ?? '',
            'youtube' => $_POST['red_youtube'] ?? ''
        ], JSON_UNESCAPED_SLASHES);
        $redes = mysqli_real_escape_string($conexion, $redes);

        // Manejar subida de logo
        $logo_sql = '';
        $logo_nombre = '';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg','jpeg','png','gif','webp','svg','ico'];
            if (in_array($ext, $permitidas)) {
                $carpeta = __DIR__ . '/../img/';
                $logo_nombre = 'logo_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $carpeta . $logo_nombre)) {
                    // Eliminar logo anterior
                    $res_old = mysqli_query($conexion, "SELECT logo FROM configuracion WHERE id_config = 1");
                    if ($row_old = mysqli_fetch_assoc($res_old)) {
                        if (!empty($row_old['logo']) && file_exists($carpeta . $row_old['logo'])) {
                            unlink($carpeta . $row_old['logo']);
                        }
                    }
                    $logo_sql = ", logo = '$logo_nombre'";
                }
            }
        }

        // Manejar subida de favicon
        $favicon_sql = '';
        $favicon_nombre = '';
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg','jpeg','png','gif','webp','svg','ico'];
            if (in_array($ext, $permitidas)) {
                $carpeta = __DIR__ . '/../img/';
                $favicon_nombre = 'favicon_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['favicon']['tmp_name'], $carpeta . $favicon_nombre)) {
                    // Eliminar favicon anterior
                    $res_old = mysqli_query($conexion, "SELECT favicon FROM configuracion WHERE id_config = 1");
                    if ($row_old = mysqli_fetch_assoc($res_old)) {
                        if (!empty($row_old['favicon']) && file_exists($carpeta . $row_old['favicon'])) {
                            unlink($carpeta . $row_old['favicon']);
                        }
                    }
                    $favicon_sql = ", favicon = '$favicon_nombre'";
                }
            }
        }

        // Manejar subida de hero_imagen
        $hero_img_sql = '';
        $hero_img_nombre = '';
        if (isset($_FILES['hero_imagen']) && $_FILES['hero_imagen']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['hero_imagen']['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg','jpeg','png','gif','webp','svg'];
            if (in_array($ext, $permitidas)) {
                $carpeta = __DIR__ . '/../img/';
                $hero_img_nombre = 'hero_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['hero_imagen']['tmp_name'], $carpeta . $hero_img_nombre)) {
                    $res_old = mysqli_query($conexion, "SELECT hero_imagen FROM configuracion WHERE id_config = 1");
                    if ($row_old = mysqli_fetch_assoc($res_old)) {
                        if (!empty($row_old['hero_imagen']) && file_exists($carpeta . $row_old['hero_imagen'])) {
                            unlink($carpeta . $row_old['hero_imagen']);
                        }
                    }
                    $hero_img_sql = ", hero_imagen = '$hero_img_nombre'";
                }
            }
        }

        // Verificar si existe configuración
        $check = mysqli_query($conexion, "SELECT id_config FROM configuracion WHERE id_config = 1");
        if (mysqli_num_rows($check) > 0) {
            // Normalizar colores (asegurar formato #RRGGBB o usar defecto)
            $normalizeColor = function($value, $default) {
                $value = trim((string)$value);
                if ($value === '') return $default;
                if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) return $default;
                return strtoupper($value);
            };
            $color_primary = $normalizeColor($color_primary_raw, '#137FEC');
            $color_primary_dark = $normalizeColor($color_primary_dark_raw, '#0D66C2');
            $color_bg_light = $normalizeColor($color_bg_light_raw, '#F6F7F8');
            $color_bg_dark = $normalizeColor($color_bg_dark_raw, '#101922');

            $sql = "UPDATE configuracion SET 
                nombre_negocio = '$nombre_negocio',
                slogan = '$slogan',
                correo = '$correo',
                telefono = '$telefono',
                direccion = '$direccion',
                moneda = '$moneda',
                horario_atencion = '$horario_atencion',
                texto_inicio = '$texto_inicio',
                pie_pagina = '$pie_pagina',
                redes_sociales = '$redes',
                texto_banner_superior = '$texto_banner_superior',
                header_menu = '$header_menu_json',
                footer_columns = '$footer_cols_json',
                color_primary = '$color_primary',
                color_primary_dark = '$color_primary_dark',
                color_background_light = '$color_bg_light',
                color_background_dark = '$color_bg_dark',
                hero_etiqueta = '$hero_etiqueta',
                hero_titulo = '$hero_titulo',
                hero_subtitulo = '$hero_subtitulo',
                hero_descripcion = '$hero_descripcion',
                hero_btn_primario = '$hero_btn_primario',
                hero_btn_secundario = '$hero_btn_secundario'
                $logo_sql
                $favicon_sql
                $hero_img_sql
                WHERE id_config = 1";
        } else {
            // Normalizar colores para nuevo registro
            $normalizeColor = function($value, $default) {
                $value = trim((string)$value);
                if ($value === '') return $default;
                if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) return $default;
                return strtoupper($value);
            };
            $color_primary = $normalizeColor($color_primary_raw, '#137FEC');
            $color_primary_dark = $normalizeColor($color_primary_dark_raw, '#0D66C2');
            $color_bg_light = $normalizeColor($color_bg_light_raw, '#F6F7F8');
            $color_bg_dark = $normalizeColor($color_bg_dark_raw, '#101922');

            $logo_col = !empty($logo_sql) ? ', logo' : '';
            $logo_val = !empty($logo_sql) ? ", '$logo_nombre'" : '';
            $fav_col = !empty($favicon_sql) ? ', favicon' : '';
            $fav_val = !empty($favicon_sql) ? ", '$favicon_nombre'" : '';
            $sql = "INSERT INTO configuracion (nombre_negocio, slogan, correo, telefono, direccion, moneda, horario_atencion, texto_inicio, pie_pagina, redes_sociales, header_menu, footer_columns, color_primary, color_primary_dark, color_background_light, color_background_dark$logo_col$fav_col) 
                        VALUES ('$nombre_negocio', '$slogan', '$correo', '$telefono', '$direccion', '$moneda', '$horario_atencion', '$texto_inicio', '$pie_pagina', '$redes', '$header_menu_json', '$footer_cols_json', '$color_primary', '$color_primary_dark', '$color_bg_light', '$color_bg_dark'$logo_val$fav_val)";
        }

        if (mysqli_query($conexion, $sql)) {
            responder(true, 'Configuración general guardada exitosamente');
        } else {
            responder(false, 'Error al guardar configuración: ' . mysqli_error($conexion));
        }
    }

    // --- LÓGICA PARA BANNERS ---
    if ($accion == 'guardar_banner') {
        $id_banner = intval($_POST['id_banner'] ?? 0);
        $titulo = mysqli_real_escape_string($conexion, $_POST['titulo'] ?? '');
        $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion'] ?? '');
        $texto_boton = mysqli_real_escape_string($conexion, $_POST['texto_boton'] ?? '');
        $enlace = mysqli_real_escape_string($conexion, $_POST['enlace'] ?? '');
        $orden = intval($_POST['orden'] ?? 0);
        $estado_raw = $_POST['estado'] ?? 'activo';
        $estado = ($estado_raw === 'activo' || $estado_raw === '1' || $estado_raw === 1) ? 'activo' : 'inactivo';
        $estado = mysqli_real_escape_string($conexion, $estado);

        if (empty(trim($titulo))) {
            responder(false, 'El título del banner es requerido');
        }

        // Manejar subida de imagen
        $img_nombre = '';
        $img_sql = '';
        if (isset($_FILES['imagen_banner']) && $_FILES['imagen_banner']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['imagen_banner']['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg','jpeg','png','gif','webp','svg'];
            if (!in_array($ext, $permitidas)) {
                responder(false, 'Formato de imagen no permitido');
            }
            if ($_FILES['imagen_banner']['size'] > 5 * 1024 * 1024) {
                responder(false, 'La imagen no debe superar los 5MB');
            }
            $carpeta = __DIR__ . '/../img/banners/';
            if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);
            $img_nombre = 'banner_' . time() . '_' . uniqid() . '.' . $ext;
            if (!move_uploaded_file($_FILES['imagen_banner']['tmp_name'], $carpeta . $img_nombre)) {
                responder(false, 'Error al subir la imagen del banner');
            }
            // Si editamos, eliminar imagen anterior
            if ($id_banner > 0) {
                $res_old = mysqli_query($conexion, "SELECT imagen FROM banners WHERE id_banner = $id_banner");
                if ($row_old = mysqli_fetch_assoc($res_old)) {
                    if (!empty($row_old['imagen']) && file_exists($carpeta . $row_old['imagen'])) {
                        unlink($carpeta . $row_old['imagen']);
                    }
                }
            }
            $img_sql = ", imagen = '$img_nombre'";
        }

        if ($id_banner > 0) {
            // Actualizar
            $sql = "UPDATE banners SET 
                titulo = '$titulo',
                descripcion = '$descripcion',
                texto_boton = '$texto_boton',
                enlace = '$enlace',
                orden = $orden,
                estado = '$estado'
                $img_sql
                WHERE id_banner = $id_banner";
        } else {
            // Crear nuevo - imagen requerida solo si es nuevo
            if (empty($img_nombre)) {
                responder(false, 'La imagen del banner es requerida');
            }
            $sql = "INSERT INTO banners (titulo, descripcion, imagen, texto_boton, enlace, orden, estado) 
                    VALUES ('$titulo', '$descripcion', '$img_nombre', '$texto_boton', '$enlace', $orden, '$estado')";
        }

        if (mysqli_query($conexion, $sql)) {
            responder(true, $id_banner > 0 ? 'Banner actualizado exitosamente' : 'Banner creado exitosamente');
        } else {
            responder(false, 'Error al guardar banner: ' . mysqli_error($conexion));
        }
    }

    // --- LÓGICA PARA HERO SLIDES ---
    if ($accion == 'guardar_hero_slide') {
        $id_slide = intval($_POST['id_slide'] ?? 0);
        $titulo = mysqli_real_escape_string($conexion, $_POST['titulo'] ?? '');
        $subtitulo = mysqli_real_escape_string($conexion, $_POST['subtitulo'] ?? '');
        $texto_boton = mysqli_real_escape_string($conexion, $_POST['texto_boton'] ?? '');
        $enlace = mysqli_real_escape_string($conexion, $_POST['enlace'] ?? '');
        $orden = intval($_POST['orden'] ?? 0);
        $estado_raw = $_POST['estado'] ?? 'activo';
        $estado = ($estado_raw === 'activo') ? 'activo' : 'inactivo';

        if (empty(trim($titulo))) {
            responder(false, 'El título del slide es requerido');
        }

        $img_nombre = '';
        $img_sql = '';
        if (isset($_FILES['imagen_slide']) && $_FILES['imagen_slide']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['imagen_slide']['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg','jpeg','png','gif','webp','svg'];
            if (!in_array($ext, $permitidas)) {
                responder(false, 'Formato de imagen no permitido');
            }
            if ($_FILES['imagen_slide']['size'] > 5 * 1024 * 1024) {
                responder(false, 'La imagen no debe superar los 5MB');
            }
            $carpeta = __DIR__ . '/../img/slides/';
            if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);
            $img_nombre = 'slide_' . time() . '_' . uniqid() . '.' . $ext;
            if (!move_uploaded_file($_FILES['imagen_slide']['tmp_name'], $carpeta . $img_nombre)) {
                responder(false, 'Error al subir la imagen del slide');
            }
            if ($id_slide > 0) {
                $res_old = mysqli_query($conexion, "SELECT imagen FROM hero_slides WHERE id_slide = $id_slide");
                if ($row_old = mysqli_fetch_assoc($res_old)) {
                    if (!empty($row_old['imagen']) && file_exists($carpeta . $row_old['imagen'])) {
                        unlink($carpeta . $row_old['imagen']);
                    }
                }
            }
            $img_sql = ", imagen = '$img_nombre'";
        }

        if ($id_slide > 0) {
            $sql = "UPDATE hero_slides SET 
                titulo = '$titulo',
                subtitulo = '$subtitulo',
                texto_boton = '$texto_boton',
                enlace = '$enlace',
                orden = $orden,
                estado = '$estado'
                $img_sql
                WHERE id_slide = $id_slide";
        } else {
            if (empty($img_nombre)) {
                responder(false, 'La imagen del slide es requerida');
            }
            $sql = "INSERT INTO hero_slides (titulo, subtitulo, imagen, texto_boton, enlace, orden, estado) 
                    VALUES ('$titulo', '$subtitulo', '$img_nombre', '$texto_boton', '$enlace', $orden, '$estado')";
        }

        if (mysqli_query($conexion, $sql)) {
            responder(true, $id_slide > 0 ? 'Slide actualizado exitosamente' : 'Slide creado exitosamente');
        } else {
            responder(false, 'Error al guardar slide: ' . mysqli_error($conexion));
        }
    }

    // --- LÓGICA PARA RESTAURAR VALORES PREDETERMINADOS ---
    if ($accion == 'restaurar_valores_predeterminados') {
        // Definir valores predeterminados
        $defaults = [
            'nombre_negocio' => 'ControlPlus',
            'slogan' => 'Tu tienda en línea',
            'correo' => 'admin@controlplus.com',
            'telefono' => '+1 (555) 123-4567',
            'direccion' => 'Dirección de la empresa',
            'moneda' => 'USD',
            'horario_atencion' => 'Lunes a Viernes 9AM - 6PM',
            'texto_inicio' => 'Bienvenido a ControlPlus',
            'pie_pagina' => '© 2024 ControlPlus. Todos los derechos reservados.',
            'texto_banner_superior' => 'Ofertas especiales disponibles',
            'hero_etiqueta' => 'Bienvenido',
            'hero_titulo' => 'Tu tienda de confianza',
            'hero_subtitulo' => 'Descubre nuestros productos',
            'hero_descripcion' => 'Compra los mejores productos',
            'hero_btn_primario' => 'Comprar Ahora',
            'hero_btn_secundario' => 'Conocer Más',
            'color_primary' => '#137FEC',
            'color_primary_dark' => '#0D66C2',
            'color_background_light' => '#F6F7F8',
            'color_background_dark' => '#101922',
            'redes_sociales' => json_encode([
                'facebook' => '',
                'instagram' => '',
                'whatsapp' => '',
                'tiktok' => '',
                'twitter' => '',
                'youtube' => ''
            ], JSON_UNESCAPED_SLASHES),
            'header_menu' => json_encode([], JSON_UNESCAPED_SLASHES),
            'footer_columns' => json_encode([], JSON_UNESCAPED_SLASHES)
        ];

        // Escapar valores
        foreach ($defaults as $key => $value) {
            $defaults[$key] = mysqli_real_escape_string($conexion, $value);
        }

        // Comenzar transacción para atomicidad
        mysqli_query($conexion, "START TRANSACTION");

        try {
            // 1. Eliminar logo y favicon
            $carpeta_img = __DIR__ . '/../img/';
            $res_img = mysqli_query($conexion, "SELECT logo, favicon FROM configuracion WHERE id_config = 1");
            if ($row_img = mysqli_fetch_assoc($res_img)) {
                // Eliminar logo
                if (!empty($row_img['logo']) && file_exists($carpeta_img . $row_img['logo'])) {
                    @unlink($carpeta_img . $row_img['logo']);
                }
                // Eliminar favicon
                if (!empty($row_img['favicon']) && file_exists($carpeta_img . $row_img['favicon'])) {
                    @unlink($carpeta_img . $row_img['favicon']);
                }
            }

            // 2. Eliminar todos los banners y sus imágenes
            $carpeta_banners = __DIR__ . '/../img/banners/';
            $res_banners = mysqli_query($conexion, "SELECT imagen FROM banners");
            while ($row = mysqli_fetch_assoc($res_banners)) {
                if (!empty($row['imagen']) && file_exists($carpeta_banners . $row['imagen'])) {
                    @unlink($carpeta_banners . $row['imagen']);
                }
            }
            mysqli_query($conexion, "DELETE FROM banners");

            // 3. Eliminar todos los hero slides y sus imágenes
            $carpeta_slides = __DIR__ . '/../img/slides/';
            $res_slides = mysqli_query($conexion, "SELECT imagen FROM hero_slides");
            while ($row = mysqli_fetch_assoc($res_slides)) {
                if (!empty($row['imagen']) && file_exists($carpeta_slides . $row['imagen'])) {
                    @unlink($carpeta_slides . $row['imagen']);
                }
            }
            mysqli_query($conexion, "DELETE FROM hero_slides");

            // 4. Verificar si existe configuración
            $check = mysqli_query($conexion, "SELECT id_config FROM configuracion WHERE id_config = 1");
            
            if (mysqli_num_rows($check) > 0) {
                // Actualizar valores existentes (sin logo ni favicon)
                $sql = "UPDATE configuracion SET 
                    nombre_negocio = '{$defaults['nombre_negocio']}',
                    slogan = '{$defaults['slogan']}',
                    correo = '{$defaults['correo']}',
                    telefono = '{$defaults['telefono']}',
                    direccion = '{$defaults['direccion']}',
                    moneda = '{$defaults['moneda']}',
                    horario_atencion = '{$defaults['horario_atencion']}',
                    texto_inicio = '{$defaults['texto_inicio']}',
                    pie_pagina = '{$defaults['pie_pagina']}',
                    texto_banner_superior = '{$defaults['texto_banner_superior']}',
                    hero_etiqueta = '{$defaults['hero_etiqueta']}',
                    hero_titulo = '{$defaults['hero_titulo']}',
                    hero_subtitulo = '{$defaults['hero_subtitulo']}',
                    hero_descripcion = '{$defaults['hero_descripcion']}',
                    hero_btn_primario = '{$defaults['hero_btn_primario']}',
                    hero_btn_secundario = '{$defaults['hero_btn_secundario']}',
                    color_primary = '{$defaults['color_primary']}',
                    color_primary_dark = '{$defaults['color_primary_dark']}',
                    color_background_light = '{$defaults['color_background_light']}',
                    color_background_dark = '{$defaults['color_background_dark']}',
                    redes_sociales = '{$defaults['redes_sociales']}',
                    header_menu = '{$defaults['header_menu']}',
                    footer_columns = '{$defaults['footer_columns']}',
                    logo = NULL,
                    favicon = NULL,
                    hero_imagen = NULL
                    WHERE id_config = 1";

                if (!mysqli_query($conexion, $sql)) {
                    throw new Exception('Error al restaurar valores: ' . mysqli_error($conexion));
                }
            } else {
                throw new Exception('No hay configuración para restaurar');
            }

            // Confirmar transacción
            mysqli_query($conexion, "COMMIT");
            responder(true, 'Configuración restaurada a valores predeterminados. Logo, favicon, banners y slides han sido eliminados.');

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            mysqli_query($conexion, "ROLLBACK");
            responder(false, $e->getMessage());
        }
    }
}

// --- LÓGICA PARA ELIMINAR (GET) ---

// Eliminar Marca
if (isset($_GET['eliminar_marca'])) {
    $id = intval($_GET['eliminar_marca']);
    if ($id <= 0) {
        responder(false, 'ID de marca inválido');
    }
    
    try {
        // Verificar si hay productos asociados a esta marca
        $stmtCheck = $conexion->prepare("SELECT COUNT(*) as count FROM productos WHERE id_marca = ?");
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        $rowCheck = $resCheck->fetch_assoc();
        
        if ($rowCheck['count'] > 0) {
            responder(false, 'No se puede eliminar esta marca porque tiene ' . $rowCheck['count'] . ' producto(s) asociado(s). Primero debes eliminar o reasignar los productos.');
        }
        
        // Desactivar foreign key checks temporalmente
        mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS=0");
        
        // Obtener logo para eliminarlo del disco
        $stmt = $conexion->prepare("SELECT logo FROM marcas WHERE id_marca = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res_logo = $stmt->get_result();
        
        // Eliminar logo del disco si existe
        if ($row_logo = $res_logo->fetch_assoc()) {
            $carpeta = __DIR__ . '/../img/marcas/';
            if (!empty($row_logo['logo']) && file_exists($carpeta . $row_logo['logo'])) {
                @unlink($carpeta . $row_logo['logo']);
            }
        }
        
        // Eliminar marca
        $stmtDel = $conexion->prepare("DELETE FROM marcas WHERE id_marca = ?");
        $stmtDel->bind_param("i", $id);
        
        if ($stmtDel->execute()) {
            // Reactivar foreign key checks
            mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS=1");
            responder(true, 'Marca eliminada exitosamente');
        } else {
            // Reactivar foreign key checks
            mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS=1");
            responder(false, 'Error al eliminar marca: ' . $stmtDel->error);
        }
    } catch (Exception $e) {
        // Reactivar foreign key checks en caso de error
        mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS=1");
        responder(false, 'Error: ' . $e->getMessage());
    }
}

/* =========================
   ELIMINAR BANCO
========================= */

if(isset($_GET['eliminar_banco'])){

$id = intval($_GET['eliminar_banco']);

$res = mysqli_query($conexion,"SELECT logo FROM bancos WHERE id_banco=$id");
$banco = mysqli_fetch_assoc($res);

if(!empty($banco['logo'])){
$ruta = "../img/bancos/".$banco['logo'];
if(file_exists($ruta)){
unlink($ruta);
}
}

mysqli_query($conexion,"DELETE FROM bancos WHERE id_banco=$id");

echo json_encode([
"success"=>true,
"message"=>"Banco eliminado correctamente"
]);

exit();

}
// Eliminar Envío
if (isset($_GET['eliminar_envio'])) {
    $id = intval($_GET['eliminar_envio']);
    if ($id <= 0) {
        responder(false, 'ID de método de envío inválido');
    }
    
    try {
        // Desvincular pedidos asociados (poner id_envio en NULL)
        $stmtUpdate = $conexion->prepare("UPDATE pedidos SET id_envio = NULL WHERE id_envio = ?");
        $stmtUpdate->bind_param("i", $id);
        $stmtUpdate->execute();
        
        // Eliminar método de envío
        $stmt = $conexion->prepare("DELETE FROM metodos_envio WHERE id_envio = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            responder(true, 'Método de envío eliminado exitosamente');
        } else {
            responder(false, 'Error al eliminar método de envío: ' . $stmt->error);
        }
    } catch (Exception $e) {
        responder(false, 'Error: ' . $e->getMessage());
    }
}

// Eliminar Pago
if (isset($_GET['eliminar_pago'])) {
    $id = intval($_GET['eliminar_pago']);
    if ($id <= 0) {
        responder(false, 'ID de método de pago inválido');
    }
    
    try {
        // Verificar si hay pedidos asociados a este método de pago
        $stmtCheck = $conexion->prepare("SELECT COUNT(*) as count FROM pedidos WHERE id_metodo_pago = ?");
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        $rowCheck = $resCheck->fetch_assoc();
        
        if ($rowCheck['count'] > 0) {
            responder(false, 'No se puede eliminar este método de pago porque tiene ' . $rowCheck['count'] . ' pedido(s) asociado(s). Primero debes cambiar el método de pago de esos pedidos.');
        }
        
        // Desactivar foreign key checks temporalmente
        mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS=0");
        
        // Eliminar método de pago
        $stmt = $conexion->prepare("DELETE FROM metodos_pago WHERE id_metodo_pago = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Reactivar foreign key checks
            mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS=1");
            responder(true, 'Método de pago eliminado exitosamente');
        } else {
            // Reactivar foreign key checks
            mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS=1");
            responder(false, 'Error al eliminar método de pago: ' . $stmt->error);
        }
    } catch (Exception $e) {
        // Reactivar foreign key checks en caso de error
        mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS=1");
        responder(false, 'Error: ' . $e->getMessage());
    }
}

// Eliminar Departamento Envío
if (isset($_GET['eliminar_departamento_envio'])) {

$id = intval($_GET['eliminar_departamento_envio']);

$stmt = $conexion->prepare("DELETE FROM departamentos_envio WHERE id_departamento = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    responder(true, "Departamento eliminado correctamente");
} else {
    responder(false, "Error al eliminar departamento");
}

exit;

}

// Eliminar Banner
if (isset($_GET['eliminar_banner'])) {
    $id = intval($_GET['eliminar_banner']);
    if ($id <= 0) {
        responder(false, 'ID de banner inválido');
    }
    
    // Eliminar imagen del disco
    $stmt = $conexion->prepare("SELECT imagen FROM banners WHERE id_banner = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res_img = $stmt->get_result();
    
    if ($row_img = $res_img->fetch_assoc()) {
        $carpeta = __DIR__ . '/../img/banners/';
        if (!empty($row_img['imagen']) && file_exists($carpeta . $row_img['imagen'])) {
            @unlink($carpeta . $row_img['imagen']);
        }
    }
    
    $stmtDel = $conexion->prepare("DELETE FROM banners WHERE id_banner = ?");
    $stmtDel->bind_param("i", $id);
    
    if ($stmtDel->execute()) {
        responder(true, 'Banner eliminado exitosamente');
    } else {
        responder(false, 'Error al eliminar banner: ' . $stmtDel->error);
    }
}

// Eliminar Hero Slide
if (isset($_GET['eliminar_hero_slide'])) {
    $id = intval($_GET['eliminar_hero_slide']);
    if ($id <= 0) {
        responder(false, 'ID de slide inválido');
    }
    
    // Obtener imagen del slide
    $stmt = $conexion->prepare("SELECT imagen FROM hero_slides WHERE id_slide = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res_img = $stmt->get_result();
    
    if ($row_img = $res_img->fetch_assoc()) {
        $carpeta = __DIR__ . '/../img/slides/';
        if (!empty($row_img['imagen']) && file_exists($carpeta . $row_img['imagen'])) {
            @unlink($carpeta . $row_img['imagen']);
        }
    }
    
    // Eliminar slide
    $stmtDel = $conexion->prepare("DELETE FROM hero_slides WHERE id_slide = ?");
    $stmtDel->bind_param("i", $id);
    
    if ($stmtDel->execute()) {
        responder(true, 'Slide eliminado exitosamente');
    } else {
        responder(false, 'Error al eliminar slide: ' . $stmtDel->error);
    }
}
?>