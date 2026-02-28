<?php
require_once 'conexion.php';
header('Content-Type: application/json');

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
        $tiempo = mysqli_real_escape_string($conexion, $_POST['tiempo']);
        $estado = $_POST['estado'] ?? 'activo';
        $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion'] ?? '');

        if (empty(trim($nombre))) {
            responder(false, 'El nombre del método de envío es requerido');
        }

        if (empty($id)) {
            $sql = "INSERT INTO metodos_envio (nombre, costo, tiempo_estimado, estado, descripcion) 
                    VALUES ('$nombre', '$costo', '$tiempo', '$estado', '$descripcion')";
        } else {
            $id = intval($id);
            $sql = "UPDATE metodos_envio SET 
                    nombre = '$nombre', 
                    costo = '$costo', 
                    tiempo_estimado = '$tiempo', 
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

    // --- LÓGICA PARA CONFIGURACIÓN GENERAL ---
    if ($accion == 'guardar_config_general') {
        $nombre_negocio = mysqli_real_escape_string($conexion, $_POST['nombre_negocio'] ?? '');
        $slogan = mysqli_real_escape_string($conexion, $_POST['slogan'] ?? '');
        $correo = mysqli_real_escape_string($conexion, $_POST['correo'] ?? '');
        $telefono = mysqli_real_escape_string($conexion, $_POST['telefono'] ?? '');
        $direccion = mysqli_real_escape_string($conexion, $_POST['direccion'] ?? '');
        $moneda = mysqli_real_escape_string($conexion, $_POST['moneda'] ?? 'USD');
        $impuesto = floatval($_POST['impuesto'] ?? 0);
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
            $sql = "UPDATE configuracion SET 
                nombre_negocio = '$nombre_negocio',
                slogan = '$slogan',
                correo = '$correo',
                telefono = '$telefono',
                direccion = '$direccion',
                moneda = '$moneda',
                impuesto = $impuesto,
                horario_atencion = '$horario_atencion',
                texto_inicio = '$texto_inicio',
                pie_pagina = '$pie_pagina',
                redes_sociales = '$redes',
                texto_banner_superior = '$texto_banner_superior',
                header_menu = '$header_menu_json',
                footer_columns = '$footer_cols_json',
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
            $logo_col = !empty($logo_sql) ? ', logo' : '';
            $logo_val = !empty($logo_sql) ? ", '$logo_nombre'" : '';
            $fav_col = !empty($favicon_sql) ? ', favicon' : '';
            $fav_val = !empty($favicon_sql) ? ", '$favicon_nombre'" : '';
            $sql = "INSERT INTO configuracion (nombre_negocio, slogan, correo, telefono, direccion, moneda, impuesto, horario_atencion, texto_inicio, pie_pagina, redes_sociales, header_menu, footer_columns$logo_col$fav_col) 
                    VALUES ('$nombre_negocio', '$slogan', '$correo', '$telefono', '$direccion', '$moneda', $impuesto, '$horario_atencion', '$texto_inicio', '$pie_pagina', '$redes', '$header_menu_json', '$footer_cols_json'$logo_val$fav_val)";
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
}

// --- LÓGICA PARA ELIMINAR (GET) ---

// Eliminar Marca
if (isset($_GET['eliminar_marca'])) {
    $id = intval($_GET['eliminar_marca']);
    // Eliminar logo del disco si existe
    $res_logo = mysqli_query($conexion, "SELECT logo FROM marcas WHERE id_marca = $id");
    if ($row_logo = mysqli_fetch_assoc($res_logo)) {
        $carpeta = __DIR__ . '/../img/marcas/';
        if (!empty($row_logo['logo']) && file_exists($carpeta . $row_logo['logo'])) {
            unlink($carpeta . $row_logo['logo']);
        }
    }
    $sql = "DELETE FROM marcas WHERE id_marca = $id";
    if (mysqli_query($conexion, $sql)) {
        responder(true, 'Marca eliminada exitosamente');
    } else {
        responder(false, 'Error al eliminar marca: ' . mysqli_error($conexion));
    }
}

// Eliminar Envío
if (isset($_GET['eliminar_envio'])) {
    $id = intval($_GET['eliminar_envio']);
    $sql = "DELETE FROM metodos_envio WHERE id_envio = $id";
    if (mysqli_query($conexion, $sql)) {
        responder(true, 'Método de envío eliminado exitosamente');
    } else {
        responder(false, 'Error al eliminar método de envío: ' . mysqli_error($conexion));
    }
}

// Eliminar Pago
if (isset($_GET['eliminar_pago'])) {
    $id = intval($_GET['eliminar_pago']);
    $sql = "DELETE FROM metodos_pago WHERE id_metodo_pago = $id";
    if (mysqli_query($conexion, $sql)) {
        responder(true, 'Método de pago eliminado exitosamente');
    } else {
        responder(false, 'Error al eliminar método de pago: ' . mysqli_error($conexion));
    }
}

// Eliminar Banner
if (isset($_GET['eliminar_banner'])) {
    $id = intval($_GET['eliminar_banner']);
    // Eliminar imagen del disco
    $res_img = mysqli_query($conexion, "SELECT imagen FROM banners WHERE id_banner = $id");
    if ($row_img = mysqli_fetch_assoc($res_img)) {
        $carpeta = __DIR__ . '/../img/banners/';
        if (!empty($row_img['imagen']) && file_exists($carpeta . $row_img['imagen'])) {
            unlink($carpeta . $row_img['imagen']);
        }
    }
    $sql = "DELETE FROM banners WHERE id_banner = $id";
    if (mysqli_query($conexion, $sql)) {
        responder(true, 'Banner eliminado exitosamente');
    } else {
        responder(false, 'Error al eliminar banner: ' . mysqli_error($conexion));
    }
}

// Eliminar Hero Slide
if (isset($_GET['eliminar_hero_slide'])) {
    $id = intval($_GET['eliminar_hero_slide']);
    $res_img = mysqli_query($conexion, "SELECT imagen FROM hero_slides WHERE id_slide = $id");
    if ($row_img = mysqli_fetch_assoc($res_img)) {
        $carpeta = __DIR__ . '/../img/slides/';
        if (!empty($row_img['imagen']) && file_exists($carpeta . $row_img['imagen'])) {
            unlink($carpeta . $row_img['imagen']);
        }
    }
    $sql = "DELETE FROM hero_slides WHERE id_slide = $id";
    if (mysqli_query($conexion, $sql)) {
        responder(true, 'Slide eliminado exitosamente');
    } else {
        responder(false, 'Error al eliminar slide: ' . mysqli_error($conexion));
    }
}
?>