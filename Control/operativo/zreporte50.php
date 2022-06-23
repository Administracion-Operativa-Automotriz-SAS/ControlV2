<?php

/**
 *   ANALIZADOR DE OBSERVACIONES, REPORTE 50
 *
 * @version $Id$
 * @copyright 2010
 */
if($I->idub)
{
	if($U=qo1("select concat(vehiculo.placa,' (',ubicacion.fecha_inicial,' - ',ubicacion.fecha_final,') [',estado_vehiculo.nombre,']') from estado_vehiculo,ubicacion,vehiculo where ubicacion.estado=estado_vehiculo.id and ubicacion.vehiculo=vehiculo.id and ubicacion.id=".$I->idub))
	{
		echo "<td>".$U."</td>";
	}
	else echo "<td></td>";
}
else echo "<td></td>";
$Fechaa='<font color=red>SIN ACTUALIZACION</FONT>';
if($p=strpos(strtolower($I->observaciones),'se recibe correo'))
{
	$Fechaa=substr($I->observaciones,$p-30,30);
	$Fechaa=substr($Fechaa,strpos($Fechaa,'[')+1);
	$Fechaa=substr($Fechaa,0,strpos($Fechaa,']'));
}
elseif($p=strpos(strtolower($I->observaciones),'se recibe actualizaci'))
{
	$Fechaa=substr($I->observaciones,$p-30,30);
	$Fechaa=substr($Fechaa,strpos($Fechaa,'[')+1);
	$Fechaa=substr($Fechaa,0,strpos($Fechaa,']'));
}
elseif($p=strpos(strtolower($I->observaciones),'se comunican de el call center de mapfre'))
{
	$Fechaa=substr($I->observaciones,$p-30,30);
	$Fechaa=substr($Fechaa,strpos($Fechaa,'[')+1);
	$Fechaa=substr($Fechaa,0,strpos($Fechaa,']'));
}
elseif($p=strpos(strtolower($I->observaciones),'se recibe comunicaion de felipe moyano actualizando') )
{
	$Fechaa=substr($I->observaciones,$p-30,30);
	$Fechaa=substr($Fechaa,strpos($Fechaa,'[')+1);
	$Fechaa=substr($Fechaa,0,strpos($Fechaa,']'));
}
elseif($p=strpos(strtolower($I->observaciones),'se recibe comunicacion de felipe moyano actualizando ') )
{
	$Fechaa=substr($I->observaciones,$p-30,30);
	$Fechaa=substr($Fechaa,strpos($Fechaa,'[')+1);
	$Fechaa=substr($Fechaa,0,strpos($Fechaa,']'));
}
elseif($p=strpos(strtolower($I->observaciones),'agenda cita para'))
{
	$Fechaa=substr($I->observaciones,$p-30,30);
	$Fechaa=substr($Fechaa,strpos($Fechaa,'[')+1);
	$Fechaa=substr($Fechaa,0,strpos($Fechaa,']'));
}
echo "<td align='center'>$Fechaa</td>";
$Dias1=dias($I->si_fec_autorizacion,$I->si_ingreso);
$Dias2=dias($I->si_ingreso,$Fechaa);
echo "<td align='center'>$Dias1</td><td align='center'>$Dias2</td>";
if($I->estado=='CONCLUIDO' || $I->estado=='SERVICIO') $Con_servicio++; else $Sin_servicio++;

?>