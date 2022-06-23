<?php
require('inc/sess.php');
include_once('inc/funciones_.php');
include('inc/reportes_funciones.php');

if($_RT) require('html/spaw_control.class.php');
$Inicia_Rompimientos=true;
$Plano_csv='';



if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}
?>
