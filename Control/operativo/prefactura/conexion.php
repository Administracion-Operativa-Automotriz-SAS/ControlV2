<?php
$servidorbd = "localhost";
$usuarioBaseDatos = "aoacol_arturo";
$claveBaseDatos = "AOA0l1lwpdaa";
$baseDatos = "aoacol_aoacars";
$conexion = mysql_connect($servidorbd,$usuarioBaseDatos,$claveBaseDatos);

if (!$conexion)
die('No se Puede Conectar: ' . mysql_error());

mysql_select_db($baseDatos,$conexion);
date_default_timezone_set("America/Bogota" ) ; 
$hora = date('H:i:s',time() - 3600*date('I'));
$fecha_hoy = date("Y-m-d").' '.$hora;