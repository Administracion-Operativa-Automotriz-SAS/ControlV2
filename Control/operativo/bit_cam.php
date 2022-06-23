<?php
include('inc/funciones_.php');
require('inc/sess.php');
## BITACORA DE AUDITORIA POR CAMPO.
q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."','".date('s')."','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','$Nombre_tabla','$accion','$id','".$_SERVER['REMOTE_ADDR']."')");
echo "<html><body onload='javascript:window.close();void(null);'>";
#ejemplo onchange=window.open('bit_cam.php?Nombre_tabla=alumno_grupo&id=$R->id&accion=e','_blank');
?>