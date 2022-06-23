<?php

/**
 * Programa para actualizar automáticamente la información de las aseguradoras de acuerdo al siniestro recien ingresado.
 *
 * @version $Id$
 * @copyright 2009
 */
$Mensaje='';
if($R->aseguradora && $R->numero)
{
	if($R->ingreso=='0000-00-00 00:00:00')
	{
		$Hoy=date('Y-m-d H:i:s');
		q("update aoacol_aoacars.siniestro set ingreso='$Hoy' where id=$R->id and ingreso='0000-00-00 00:00:00' "); // graba la fecha de ingreso si no la tiene
		$H1=date('Y-m-d'); $H2=date('H:i:s');
		$Usuario=$_SESSION['Nombre'];
		q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ('$R->id','$H1','$H2','$Usuario','Ingreso a AOA',1)"); // inserta un seguimiento para el siniestro
	}
}
else
{
	$Mensaje='Debe seleccionar la aseguradora y digitar el número del siniestro para poder guardar el registro.';
	echo "<script language='javascript'>
		alert('$Mensaje');
		</script>";
}





?>