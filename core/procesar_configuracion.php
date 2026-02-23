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
?>