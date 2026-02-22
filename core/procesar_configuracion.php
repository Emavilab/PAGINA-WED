<?php
require_once 'conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? ''; 

    // --- LÓGICA PARA MARCAS ---
    if ($accion == 'guardar_marca') {
        $id = $_POST['id_marca'] ?? '';
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $estado = $_POST['estado'] ?? 'activo';

        if (empty($id)) {
            $sql = "INSERT INTO marcas (nombre, estado) VALUES ('$nombre', '$estado')";
        } else {
            $id = intval($id);
            $sql = "UPDATE marcas SET nombre = '$nombre', estado = '$estado' WHERE id_marca = $id";
        }

        if (mysqli_query($conexion, $sql)) {
            header("Location: ../admin/Dashboard.php"); 
            exit();
        } else {
            echo "Error en Marcas: " . mysqli_error($conexion);
        }
    }

    // --- LÓGICA PARA MÉTODOS DE ENVÍO ---
    if ($accion == 'guardar_envio') {
        $id = $_POST['id_envio'] ?? '';
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $costo = mysqli_real_escape_string($conexion, $_POST['costo']);
        $tiempo = mysqli_real_escape_string($conexion, $_POST['tiempo']);
        $estado = $_POST['estado'] ?? 'activo';
        $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);

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
            header("Location: ../admin/Dashboard.php?success=1"); 
            exit();
        } else {
            echo "Error en Envío: " . mysqli_error($conexion);
        }
    }

    // --- LÓGICA PARA MÉTODOS DE PAGO (Movido dentro del bloque POST) ---
    if ($accion == 'guardar_pago') {
        $id = $_POST['id_pago'] ?? '';
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
        $estado = $_POST['estado'] ?? 'activo';

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
            header("Location: ../admin/Dashboard.php?success=pago"); 
            exit();
        } else {
            echo "Error en Pago: " . mysqli_error($conexion);
        }
    }
}

// --- LÓGICA PARA ELIMINAR (GET) ---

// Eliminar Envío
if (isset($_GET['eliminar_envio'])) {
    $id = intval($_GET['eliminar_envio']);
    $sql = "DELETE FROM metodos_envio WHERE id_envio = $id";
    if (mysqli_query($conexion, $sql)) {
        header("Location: ../admin/Dashboard.php?deleted=1");
        exit();
    }
}

// Eliminar Pago
if (isset($_GET['eliminar_pago'])) {
    $id = intval($_GET['eliminar_pago']);
    $sql = "DELETE FROM metodos_pago WHERE id_metodo_pago = $id";
    if (mysqli_query($conexion, $sql)) {
        header("Location: ../admin/Dashboard.php?deleted=pago");
        exit();
    }
}
?>