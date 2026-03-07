<?php
require_once '../core/sesiones.php';

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    echo json_encode(["status"=>"error","msg"=>"No autorizado"]);
    exit();
}

require_once '../core/conexion.php';

$accion = $_POST['accion'] ?? '';

/* ============================
   GUARDAR / EDITAR BANCO
============================ */

if($accion == "guardar_banco"){

$id_banco = intval($_POST['id_banco'] ?? 0);
$nombre = mysqli_real_escape_string($conexion,$_POST['nombre']);
$numero_cuenta = mysqli_real_escape_string($conexion,$_POST['numero_cuenta']);
$id_tipo_cuenta = intval($_POST['id_tipo_cuenta']);

$logo = "";

/* subir logo */
if(!empty($_FILES['logo']['name'])){

$nombre_archivo = time()."_".basename($_FILES["logo"]["name"]);
$ruta = "../img/bancos/".$nombre_archivo;

move_uploaded_file($_FILES["logo"]["tmp_name"],$ruta);

$logo = $nombre_archivo;

}

/* ============================
   NUEVO BANCO
============================ */

if($id_banco == 0){

$sql = "INSERT INTO bancos
(nombre,numero_cuenta,id_tipo_cuenta,logo)
VALUES
('$nombre','$numero_cuenta',$id_tipo_cuenta,'$logo')";

mysqli_query($conexion,$sql);

}

/* ============================
   EDITAR BANCO
============================ */

else{

if($logo != ""){

$sql = "UPDATE bancos SET
nombre='$nombre',
numero_cuenta='$numero_cuenta',
id_tipo_cuenta=$id_tipo_cuenta,
logo='$logo'
WHERE id_banco=$id_banco";

}else{

$sql = "UPDATE bancos SET
nombre='$nombre',
numero_cuenta='$numero_cuenta',
id_tipo_cuenta=$id_tipo_cuenta
WHERE id_banco=$id_banco";

}

mysqli_query($conexion,$sql);

}

echo json_encode(["status"=>"ok"]);
exit();

}


/* ============================
   ELIMINAR BANCO
============================ */

if($accion == "eliminar_banco"){

$id = intval($_POST['id']);

mysqli_query($conexion,"DELETE FROM bancos WHERE id_banco=$id");

echo json_encode(["status"=>"ok"]);
exit();

}