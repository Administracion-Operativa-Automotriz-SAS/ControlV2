<?php

include('inc/funciones_.php');
$Ahora=date('Y-m-d');
$Seismeses=date('Y-m-d',strtotime(aumentadias($Ahora,-180)));
echo $Seismeses;

/*
	session_start();
	session_unset();session_destroy();
	header("location:http://app.aoacolombia.com/Control/desarrollo/m.aoacontrol.php");
// PROGRAMA QUE dispara la toma de imagenes de un servicio de prueba en el area de desarrollo de AOA
if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}

echo "
	<HTML><TITLE></TITLE>
	<head>
	 <meta charset='iso-8859-1'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'>
	<body>
	<form action='zp1.php' method='post' target='_self' name='forma' id='forma'>
	Id de la cita: <input type='number' name='id' id='id' value='' size='10' maxlength='10' placeholder='Id de la cita'>
	<input type='hidden' name='Acc' id='Acc' value='reenviar'>
	<input type='submit' value='Continuar' >
</form>";

function reenviar()
{
	global $id;
	$sitio=base64_encode("http://app.aoacolombia.com/Control/desarrollo/m.aoacontrol.util.php?Acc=toma_imagenes_entrega&id=$id");
	echo "<script language='javascript'>window.open('http://www.kas.com.co/Control001/util.php?Acc=reenviourl&sitio=$sitio','_self');setTimeout(regresar,10000);</script>";
}
*/

?>