<?php
session_start();
$_SESSION['id_rol'] = 1;
$_SESSION['id_usuario'] = 1;
$_GET['pagina'] = 1;

ob_start();
require_once 'obtener_usuarios.php';
$output = ob_get_clean();

echo $output;
?>