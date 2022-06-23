<?php
/*código php... */
error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-Type: text/html; charset=UTF-8');
header("location=operativo/index.php");
include_once('../Control/operativo/inc/funcion.php');



if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}



function verificar_directorios()
{
	if(!is_dir('imagenes')) mkdir('imagenes',0777);
	if(!is_dir('imagenes/reportes')) mkdir('imagenes/reportes',0777);
	if(!is_dir('imagenes/datos')) mkdir('imagenes/datos',0777);
}


?>