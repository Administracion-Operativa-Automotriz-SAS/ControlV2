<?php
include('inc/funciones_.php');
//$Hoy=date('Y-m-d');
include('inc/link.php');
mysql_query("insert ignore into call2est_diaria (fecha,agente) select distinct '$Hoy',agente from call2proceso where date_format(fecha,'%Y-%m-%d') = '$Hoy' ",$LINK);
$Agentes=mysql_query("select agente from call2est_diaria where fecha='$Hoy' ",$LINK);
while($A=mysql_fetch_object($Agentes))
{
	$Ag=qom("select nombre,nivel from usuario_callcenter where id=$A->agente",$LINK);
	$Gestionados=mysql_query("select distinct siniestro from call2proceso where date_format(fecha,'%Y-%m-%d')='$Hoy' and agente=$A->agente",$LINK);
	$Sing='';while($Ges=mysql_fetch_object($Gestionados)) $Sing.=($Sing?',':'').$Ges->siniestro;
	$Cantidad_gestionados=mysql_num_rows($Gestionados);unset($Gestionados);
	if($Ag->nivel==1)
	{
		$Efectivos=mysql_query("select distinct siniestro from seguimiento where siniestro in ($Sing) and tipo = 17 and usuario='$Ag->nombre' ",$LINK);
		$Cantidad_efectivos=mysql_num_rows($Efectivos); unset($Efectivos);
	}
	elseif($Ag->nivel==2)
	{
		$Efectivos=mysql_query("select distinct siniestro from seguimiento where siniestro in ($Sing) and tipo = 5 and usuario='$Ag->nombre' ",$LINK);
		$Cantidad_efectivos=mysql_num_rows($Efectivos); unset($Efectivos);
	}
	else $Cantidad_efectivos=0;
	$Escalafon=qo1m("select sum puntaje from call2escalafon where agente=$A->agente and date_format(fecha,'%Y-%m-%d')='$Hoy' ",$LINK);
	mysql_query("update call2est_diaria set gestionados='$Cantidad_gestionados',efectivos='$Cantidad_efectivos',
		nivel='$Ag->nivel',escalafon='$Escalafon' where fecha='$Hoy' and agente='$A->agente' ",$LINK);
}
$Primer_dia_semana=primer_dia_de_semana($Hoy);

mysql_query("insert ignore into call2est_semanal (fecha,agente) select distinct '$Primer_dia_semana',agente from call2proceso where date_format(fecha,'%Y-%m-%d') between '$Primer_dia_semana' and '$Hoy' ",$LINK);
$Agentes=mysql_query("select agente from call2est_semanal where fecha='$Primer_dia_semana' ",$LINK);
while($A=mysql_fetch_object($Agentes))
{
	$Ag=qom("select nombre,nivel from usuario_callcenter where id=$A->agente",$LINK);
	$Gestionados=mysql_query("select distinct siniestro from call2proceso where date_format(fecha,'%Y-%m-%d') between '$Primer_dia_semana' and '$Hoy' and agente=$A->agente",$LINK);
	$Cantidad_gestionados=mysql_num_rows($Gestionados);
	$Sing='';while($Ges=mysql_fetch_object($Gestionados)) $Sing.=($Sing?',':'').$Ges->siniestro;
	unset($Gestionados);
	if($Ag->nivel==1)
	{
		$Efectivos=mysql_query("select distinct siniestro from seguimiento where siniestro in ($Sing) and tipo = 17 and usuario='$Ag->nombre' ",$LINK);
		$Cantidad_efectivos=mysql_num_rows($Efectivos); unset($Efectivos);
	}
	elseif($Ag->nivel==2)
	{
		$Efectivos=mysql_query("select distinct siniestro from seguimiento where siniestro in ($Sing) and tipo = 5 and usuario='$Ag->nombre' ",$LINK);
		$Cantidad_efectivos=mysql_num_rows($Efectivos); unset($Efectivos);
	}
	else $Cantidad_efectivos=0;
	$Escalafon=qo1m("select sum puntaje from call2escalafon where agente=$A->agente and date_format(fecha,'%Y-%m-%d') between '$Primer_dia_semana' and '$Hoy' ",$LINK);
	mysql_query("update call2est_semanal set gestionados='$Cantidad_gestionados',efectivos='$Cantidad_efectivos',
		nivel='$Ag->nivel',escalafon='$Escalafon' where fecha='$Primer_dia_semana' and agente='$A->agente' ",$LINK);
}
mysql_close($LINK);
?>