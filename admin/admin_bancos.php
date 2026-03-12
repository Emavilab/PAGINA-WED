<?php

/*
========================================================
MODULO: GESTION DE BANCOS
========================================================

Este archivo se encarga de administrar los bancos
registrados dentro del sistema.

FUNCIONES PRINCIPALES:
✔ Crear un nuevo banco
✔ Editar información de un banco
✔ Subir logo del banco
✔ Eliminar un banco
✔ Validar que el usuario tenga permisos

PERMISOS:
Solo pueden acceder usuarios con rol:
- 1 (Administrador)
- 2 (Supervisor o encargado)

RESPUESTAS:
El sistema responde en formato JSON para ser utilizado
por peticiones AJAX del sistema administrativo.

AUTOR: Sistema Web
========================================================
*/


/* ====================================================
   CARGA DEL SISTEMA DE SESIONES
   Se utiliza para verificar que el usuario esté logueado
   y que tenga permisos para usar este módulo
==================================================== */

require_once '../core/sesiones.php';


/* ====================================================
   VALIDACION DE ACCESO
   Si el usuario no está autenticado o no tiene el rol
   adecuado, el sistema devuelve un error y se detiene
==================================================== */

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    echo json_encode(["status"=>"error","msg"=>"No autorizado"]);
    exit();
}


/* ====================================================
   CONEXION A LA BASE DE DATOS
==================================================== */

require_once '../core/conexion.php';


/* ====================================================
   CAPTURA DE ACCION
   Se obtiene la acción enviada desde el formulario
   mediante POST para decidir qué operación ejecutar
==================================================== */

$accion = $_POST['accion'] ?? '';



/* ====================================================
   GUARDAR O EDITAR BANCO
==================================================== */

if($accion == "guardar_banco"){


/* ====================================================
   RECEPCION DE DATOS DEL FORMULARIO
==================================================== */

$id_banco = intval($_POST['id_banco'] ?? 0);

$nombre = mysqli_real_escape_string($conexion,$_POST['nombre']);

$numero_cuenta = mysqli_real_escape_string($conexion,$_POST['numero_cuenta']);

$id_tipo_cuenta = intval($_POST['id_tipo_cuenta']);


/* variable donde se guardará el logo */

$logo = "";



/* ====================================================
   SUBIDA DEL LOGO DEL BANCO
   Si el usuario selecciona un archivo, se guarda
   en la carpeta /img/bancos/
==================================================== */

if(!empty($_FILES['logo']['name'])){

$nombre_archivo = time()."_".basename($_FILES["logo"]["name"]);

$ruta = "../img/bancos/".$nombre_archivo;


/* mover archivo al servidor */

move_uploaded_file($_FILES["logo"]["tmp_name"],$ruta);


/* guardar nombre del archivo */

$logo = $nombre_archivo;

}



/* ====================================================
   INSERTAR NUEVO BANCO
   Se ejecuta cuando id_banco = 0
==================================================== */

if($id_banco == 0){

$sql = "INSERT INTO bancos
(nombre,numero_cuenta,id_tipo_cuenta,logo)
VALUES
('$nombre','$numero_cuenta',$id_tipo_cuenta,'$logo')";

mysqli_query($conexion,$sql);

}



/* ====================================================
   ACTUALIZAR BANCO EXISTENTE
==================================================== */

else{


/* si se subió un nuevo logo se actualiza también */

if($logo != ""){

$sql = "UPDATE bancos SET
nombre='$nombre',
numero_cuenta='$numero_cuenta',
id_tipo_cuenta=$id_tipo_cuenta,
logo='$logo'
WHERE id_banco=$id_banco";

}


/* si no hay nuevo logo solo se actualizan datos */

else{

$sql = "UPDATE bancos SET
nombre='$nombre',
numero_centa='$numero_cuenta',
id_tipo_cuenta=$id_tipo_cuenta
WHERE id_banco=$id_banco";

}

mysqli_query($conexion,$sql);

}


/* ====================================================
   RESPUESTA DEL SISTEMA
==================================================== */

echo json_encode(["status"=>"ok"]);

exit();

}




/* ====================================================
   ELIMINAR BANCO
   Elimina un banco según su ID
==================================================== */

if($accion == "eliminar_banco"){


/* obtener ID enviado */

$id = intval($_POST['id']);


/* ejecutar eliminación */

mysqli_query($conexion,"DELETE FROM bancos WHERE id_banco=$id");


/* respuesta del sistema */

echo json_encode(["status"=>"ok"]);

exit();

}