<?php
/*  PROGRAMA PARA MONITOREAR POR CIUDAD LOS VEHICULOS, POR LOGO/SIN LOGO Y POR ESTADO Y POR ASEGURADORA
 *
 * 	Programa gerencial para identificar rápidamente por ciudad donde están y en que estado esta cada vehículo
 *
 */


include('inc/funciones_.php');
sesion();
$USUARIO=$_SESSION['User'];
$NUSUARIO=$_SESSION['Nombre'];
if($USUARIO==29)
{
	$Aseguradora=qo1("select aseguradora from usuario_aseguradora2 where id=".$_SESSION['Id_alterno']." ");
}
if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}

html(TITULO_APLICACION." - MONITOR OPERATIVO POR ASEGURADORA - $NUSUARIO");
$HOY=date('Y-m-d');
require('inc/link.php');

if($Aseguradora)
{
	$Naseguradora=qo1m("select nombre from aseguradora where id=$Aseguradora",$LINK);
}
$Oficinas=mysql_query("select id,nombre,ciudad from oficina where id in (1,2,3,5,7,8,10,11,14,15,16,17,18,19,22,23,25,26,41,27,28,42) order by nombre",$LINK);
$AOFicina=array();
$Contador=0;
while($Oficina=mysql_fetch_object($Oficinas))
{
	$AOFicina[$Contador]['nombre']=str_replace('- MORATO','',$Oficina->nombre);
	$AOFicina[$Contador]['codigo']=$Oficina->ciudad;
	$AOFicina[$Contador]['solicitudes']=0;
	$AOFicina[$Contador]['servicios']=0;
	$AOFicina[$Contador]['dias']=0;
	$AOFicina[$Contador]['promedio']=0;
	$AOFicina[$Contador]['Xml']="<chart caption='$Oficina->nombre'  showValues='0' showLabels='0' yAxisMinValue='10'>";
	$Contador++;
}


$Fecha_inicial=date('Y-m-d',strtotime($Periodo.'01'));
$Dia_final=ultimo_dia_de_mes(date('Y',$Fecha_inicial),date('m',$Fecha_inicial));
$Fecha_final=date('Y-m-d',strtotime($Periodo.$Dia_final));
echo "<script language='javascript' src='inc/chart/JSClass/FusionCharts.js'></script>";
echo "
<script language='javascript'>
	function recarga()
	{
		location.reload();
	}
	Recargar=setTimeout(recarga,60000);

	function validar()
	{
		window.open('zmonitor_operativo_aseguradora.php?Periodo='+document.forma.Periodo.value+'&Aseguradora='+document.forma.Aseguradora.value,'_self');
	}

</script>
<body onload='centrar()'>
<H3>Monitor Operativo por ciudad. Aseguradora: $Naseguradora Periodo: $Periodo</h3>
<form action='zmonitor_operativo_aseguradora.php' method='post' target='_self' name='forma' id='forma'><table><tr><td nowrap='yes'>";
if($USUARIO==29) $Perini='201101'; else $Perini="200801";
echo " Aseguradora: ";
$Asegs=mysql_query("select id,nombre from aseguradora where id not in (6) and activo=1 order by id",$LINK);
echo "<select name='Aseguradora'>";
while($As=mysql_fetch_object($Asegs))
{
	if($USUARIO==29)
	{
		if($As->id==$Aseguradora) echo "<option value='$As->id'>$As->nombre</option>";
	}
	else
	{
		echo "<option value='$As->id' ".($As->id==$Aseguradora?" selected ":"")." >$As->nombre</option>";
	}
}
echo "</select></td><td>";
echo " Periodo:
<select name='Periodo' onchange=\"validar();\">";
while($Perini<=date('Ym'))
{
	echo "<option value='$Perini' ".($Periodo?($Perini==$Periodo?"selected":""):($Perini==date('Ym')?"selected":"")).">$Perini</option>";
	$Perini=aumentaperiodo($Perini,1);
}
echo "</select><input type='button' value='Continuar' onclick='validar();'>
</form></td><td nowrap='yes'>[ Fecha Inicial: $Fecha_inicial Fecha Final: $Fecha_final]<td> </td></table>
";
if(!$Periodo )
{
	die("</body>");
}
echo "  <hr>
			<table border cellspacing='0' style='empty-cells:show'>
			<tr>
			<th colspan=2>Dia</th>";
			for($i=0;$i<count($AOFicina);$i++) echo "<th colspan=4>".$AOFicina[$i]['nombre']."</th>";
			echo "<th colspan=4>Total</th>
			</tr><tr><td>#</td><td>Nombre</td>";
			for($i=0;$i<count($AOFicina);$i++) echo "<td>Solicitudes</td><td>Acum.</td><td>Servicios</td><td align='center'>%</td>";
			echo "<td>Solicitudes</td><td>Acum.</td><td>Servicios</td><td align='center'>%</td></tr>";

$dia=1;
$Aing=array();
//if($Ingresos=mysql_query("select ciudad,date_format(ingreso,'%d') as dia,count(*) as cantidad from siniestro where date_format(ingreso,'%Y%m')='$Periodo'
//		and !(numero like '%EXTRA%' or numero like '%ESPECIAL%' ) and aseguradora=$Aseguradora group by ciudad,dia order by ciudad,dia ",$LINK))
if($Ingresos=mysql_query("select ciudad,date_format(ingreso,'%d') as dia,count(*) as cantidad from siniestro where date_format(ingreso,'%Y%m')='$Periodo'
		and aseguradora=$Aseguradora group by ciudad,dia order by ciudad,dia ",$LINK))
while($Ingreso=mysql_fetch_object($Ingresos)) $Aing[$Ingreso->ciudad][$Ingreso->dia*1]=$Ingreso->cantidad;
$Aser=array();
if($Servicios=mysql_query("select oficina.ciudad as ciudad,date_format(ubicacion.fecha_inicial,'%d') as dia,count(ubicacion.id) as cantidad
			from ubicacion,oficina where ubicacion.oficina=oficina.id and date_format(ubicacion.fecha_inicial,'%Y%m')='$Periodo' and
			ubicacion.estado in (1,7) and ubicacion.flota=$Aseguradora group by ciudad,dia order by ciudad,dia",$LINK))
	while($Ser=mysql_fetch_object($Servicios)) $Aser[$Ser->ciudad][$Ser->dia*1]=$Ser->cantidad;
$t_dias=0;
mysql_close($LINK);

// ******************************************************************************************************************************************************************************
$xml="<chart caption='EFECTIVIDAD DIARIA ACUMULADA' xAxisName='Dias' yAxisName='Efectividad' showValues='0' logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' showLabels='0' formatNumberScale='0'>";
$xml2="<chart caption='EFECTIVIDAD DIARIA GENERAL EN PORCENTAJE' xAxisName='Dias' yAxisName='Efectividad' showValues='0' showLabels='0' logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' yAxisMinValue='10'> ";
$Xml=array();
$categorias="<categories>";
$serie1="<dataset seriesName='Solicitudes'>";
$serie2="<dataset seriesName='Servicios' renderAs='Area'>";
include('inc/link.php');
while($dia<=$Dia_final)
{

	$Fecha=date('Ymd',strtotime($Periodo.str_pad($dia,2,'0',STR_PAD_LEFT)));
	if($Fecha<=date('Ymd')) $categorias.="<category label='$dia'/>";
	$Ndia=dia_semana(date('w',strtotime($Fecha)));
	echo "<tr ".(inlist($Ndia,'Sá,Do')?" bgcolor='ffeeee' ":"")."><td align='center'>$dia</td><td>$Ndia</td>";
	$t_solicitudes=$t_servicios=$d_solicitudes=0;
	$switch_color=false;
	for($i=0;$i<count($AOFicina);$i++)
	{
		if(!inlist($Ndia,'Sá,Do'))
		{
			if($switch_color)
			{
				$bgcolor='eeeeee';$switch_color=false;
			}
			else
			{
				$bgcolor='dddddd';$switch_color=true;
			}
		}
		else $bgcolor='ffeeee';

		if($Aing[$AOFicina[$i]['codigo']][$dia]>0) $AOFicina[$i]['dias']++;
		$AOFicina[$i]['solicitudes']+=$Aing[$AOFicina[$i]['codigo']][$dia];
		$AOFicina[$i]['servicios']+=$Aser[$AOFicina[$i]['codigo']][$dia];
		$TAcumulados[$Acumulados[$AOFicina[$i]['codigo']]]['solicitudes']+=$Aing[$AOFicina[$i]['codigo']][$dia];
		$TAcumulados[$Acumulados[$AOFicina[$i]['codigo']]]['servicios']+=$Aser[$AOFicina[$i]['codigo']][$dia];

		if($AOFicina[$i]['solicitudes'])
		{
			$Porcentaje=round($AOFicina[$i]['servicios']/$AOFicina[$i]['solicitudes']*100,2);
		}
		else
		{
			$Porcentaje=0;
		}
		if($Fecha<=date('Ymd')) $AOFicina[$i]['Xml'].="<set label='$dia' value='".round($Porcentaje,2)."'/>  ";
		echo "<td align='center' bgcolor='$bgcolor'>".($Fecha<=date('Ymd')?$Aing[$AOFicina[$i]['codigo']][$dia]:'')."</td>";
		echo "<td align='center' bgcolor='$bgcolor'>".($Fecha<=date('Ymd')?$AOFicina[$i]['solicitudes']:'')."</td>";
		echo "<td align='center' bgcolor='$bgcolor'>".($Fecha<=date('Ymd')?$AOFicina[$i]['servicios']:'')."</td>";
		echo "<td align='center' bgcolor='$bgcolor'>".($Fecha<=date('Ymd')?coma_formatd($Porcentaje,2):'')."</td>";
		$t_solicitudes+=$AOFicina[$i]['solicitudes'];
		$d_solicitudes+=$Aing[$AOFicina[$i]['codigo']][$dia];
		$t_servicios+=$AOFicina[$i]['servicios'];
		if($dia==$Dia_final)
		{
			$Codigo_ciudad=$AOFicina[$i]['codigo'];
			$Solicitudes_h=$AOFicina[$i]['solicitudes'];
			$Servicios_h=$AOFicina[$i]['servicios'];
			mysql_query("insert ignore into rsm_monitor2 (aseguradora,ciudad,periodo) values ($Aseguradora,'$Codigo_ciudad',$Periodo) ",$LINK);
			mysql_query("update rsm_monitor2 set solicitudes=$Solicitudes_h  , servicios=$Servicios_h   where aseguradora=$Aseguradora and ciudad='$Codigo_ciudad' and periodo=$Periodo",$LINK);
		}
	}
	if($switch_color)
	{
		$bgcolor='eeeeee';$switch_color=false;
	}
	else
	{
		$bgcolor='dddddd';$switch_color=true;
	}
	$Porcentaje=($t_solicitudes?round($t_servicios/$t_solicitudes*100,2):0);
	if($d_solicitudes) $t_dias++;
	echo "<td align='center' bgcolor='$bgcolor'>".($Fecha<=date('Ymd')?$d_solicitudes:'')."</td>";
	echo "<td align='center' bgcolor='$bgcolor'>".($Fecha<=date('Ymd')?$t_solicitudes:'')."</td>";
	if($Fecha<=date('Ymd')) $serie1.="<set value='$t_solicitudes'/>";
	echo "<td align='center' bgcolor='$bgcolor'>".($Fecha<=date('Ymd')?$t_servicios:'')."</td>";
	if($Fecha<=date('Ymd')) $serie2.="<set value='$t_servicios'/>";
	echo "<td align='center' bgcolor='$bgcolor'>".($Fecha<=date('Ymd')?coma_formatd($Porcentaje,2):'')."</td>";
	if($Fecha<=date('Ymd')) $xml2.="<set label='$dia' value='".round($Porcentaje,2)."'/>  ";
	echo "</tr>";

	$dia++;
}
mysql_close($LINK);
for($i=0;$i<count($AOFicina);$i++) $AOFicina[$i]['Xml'].="</chart>";
$categorias.="</categories>";
$serie1.="</dataset>";
$serie2.="</dataset>";
$adicionales="<styles><definition><style name='CanvasAnim' type='animation' param='_xScale' start='0' duration='1' /></definition><application><apply toObject='Canvas' styles='CanvasAnim' /></application></styles>";
$xml.=$categorias.$serie1.$serie2.$adicionales."</chart>";
$xml2.="</chart>";
// ******************************************************************************************************************************************************************************

echo "<tr><td colspan=2>Promedio</td>";

for($i=0;$i<count($AOFicina);$i++)
{
	$Solicitudes=$AOFicina[$i]['solicitudes'];
	$AOFicina[$i]['promedio']=($AOFicina[$i]['dias']?round($Solicitudes/$AOFicina[$i]['dias'],2):0);
	echo "<td align='center'>".$AOFicina[$i]['promedio']."</td><td colspan=3></td>";
}

$Promedio=($t_dias?round($t_solicitudes/$t_dias,2):0);
echo "<td align='center'>$Promedio</td><td colspan=3></td></tr>";


$Efec2=array();
$Efectividad=q("SELECT ciudad,count(*) as total,sum(if(estado=7 or estado=8,1,0)) as servicio
						FROM siniestro WHERE date_format(fec_autorizacion,'%Y%m')='$Periodo' and aseguradora=$Aseguradora
							GROUP BY ciudad ORDER BY ciudad");
while($Efec=mysql_fetch_object($Efectividad))
{
	$Efec2[$Efec->ciudad]['Solicitudes']=$Efec->total;
	$Efec2[$Efec->ciudad]['Servicios']=$Efec->servicio;
	$Efec2[$Efec->ciudad]['Efectividad']=($Efec->total?round($Efec->servicio/$Efec->total*100,2):0);
}
$Desf=array();
if($Desfase=q("SELECT ciudad,sum(if(date_format(fec_autorizacion,'%Y%m')!='$Periodo' and date_format(ingreso,'%Y%m')='$Periodo',1,0)) as desfase_previo,
sum(if(date_format(fec_autorizacion,'%Y%m')='$Periodo' and date_format(ingreso,'%Y%m')!='$Periodo',1,0)) as desfase_posterior
FROM siniestro where date_format(fec_autorizacion,'%Y%m')='$Periodo' or date_format(ingreso,'%Y%m')='$Periodo' and aseguradora=$Aseguradora
GROUP BY ciudad ORDER BY ciudad"))
{
	while($Df=mysql_fetch_object($Desfase))
	{
		$Desf[$Df->ciudad]['Previo']=$Df->desfase_previo;
		$Desf[$Df->ciudad]['Posterior']=$Df->desfase_posterior;
	}
}
echo "<tr><td colspan=2>Efectividad 2</td>";
require('inc/link.php');
$Inges=array();

for($i=0;$i<count($AOFicina);$i++)
{
	$Solicitudes=$AOFicina[$i]['solicitudes'];
	$Solicitudes2=$Efec2[$AOFicina[$i]['codigo']]['Solicitudes'];
	$Servicios=$AOFicina[$i]['servicios'];
	$Ciudad=$AOFicina[$i]['codigo'];

	echo "<td>&nbsp;</td><td align='center'>$Solicitudes2</td><td align='center'>".$Efec2[$AOFicina[$i]['codigo']]['Servicios']."</td>
				<td align='center'>".$Efec2[$AOFicina[$i]['codigo']]['Efectividad']."</td>";
//	mysql_query("update flotames set sin_mes='$Solicitudes',sin1_mes='$Solicitudes2',servicios='$Servicios',eficiencia=if(cantidad,round(servicios/cantidad,2),0),
//	  porc_utilizacion=if(sin_mes>0,round(servicios/sin_mes*100,2),0) where aseguradora=$Aseguradora and periodo='$Periodo'  ",$LINK);
//	$Inges[$i]['eficiencia']=qo1m("select eficiencia from flotames where aseguradora=$Aseguradora and periodo='$Periodo'   ",$LINK);
}
mysql_close($LINK);
echo "</tr>";
echo "<tr><td colspan=2>Desf.Previo</td>";
for($i=0;$i<count($AOFicina);$i++)
{
	echo "<td>&nbsp;</td><td align='center'>+".$Desf[$AOFicina[$i]['codigo']]['Previo']."</td><td colspan=2></td>";
}
echo "</tr>";
echo "<tr><td colspan=2>Desf.Post.</td>";
for($i=0;$i<count($AOFicina);$i++)
{
	echo "<td>&nbsp;</td><td align='center' style='color:ff0000'>-".$Desf[$AOFicina[$i]['codigo']]['Posterior']."</td><td colspan=2></td>";
}
echo "</tr>";
/*
echo "<tr><td colspan=2>Efic. Flota</td>";
$switch_color=false;

for($i=0;$i<count($AOFicina);$i++)
{
	if($switch_color)
	{
		$bgcolor='eeeeee';$switch_color=false;
	}
	else
	{
		$bgcolor='dddddd';$switch_color=true;
	}
	echo "<td colspan=4 align='right'  bgcolor='$bgcolor'>".coma_formatd($Inges[$i]['eficiencia'],2)." %</td>";
}

echo "</tr>";
*/

echo "</table><br />";
include('inc/chart/Includes/FusionCharts.php');
echo "<table cellspacing=4 align='center'><tr><td>".renderChart("inc/chart/Charts/MSArea.swf","",$xml,"efectividad",500,200,false,false)."</td>
			<td>".renderChart('inc/chart/Charts/Line.swf','',$xml2,"efectividad_general",500,200,false,false)."</td></tr></table>";
echo "<table cellspacing=2 align='center'><tr>";
$conteo=1;
for($i=0;$i<count($AOFicina);$i++)
{
	echo "<td>".renderChart('inc/chart/Charts/Line.swf','',$AOFicina[$i]['Xml'],"efectividad_$i",330,160,false,false)."</td>";
	$conteo++;
	if($conteo>3)
	{
		echo "</tr><tr>";
		$conteo=1;
	}
}
echo "</tr></table>";
echo "

<br />
<br />
<b>Nota:</b> <i><u>La Efectividad Diaria</u></i> se mide tomando las solicitudes que ingresan en el dia, sin importar si son del mismo periodo o no; los servicios que inician en el dia sin importar si pertenecen a
siniestros de otro periodo; se toma el acumulado de servicios sobre el acumulado de solicitudes y se halla el porcentaje de Efectividad Diario.<br /><br /><i><u>La Efectividad 2</u></i> se basa en los siniestros cuya
<font color='blue'>fecha de autorización</font>
corresponde al periodo visto; los servicios atendidos de los siniestros con <font color='blue'>fecha de autorización</font> del periodo visto;
se toma el numero de servicios dividido el número de solicitudes y se halla el
porcentaje de efectividad 2.<br /><br />Es normal que en el trascurso del mes la <i><u>Efectividad Diaria</u></i> sea mayor que la <i><u>Efectividad 2</u></i> pero después de finalizar el periodo y antes de cumplir 30 días
mas, es de esperar que la <i><u>Efectividad 2</u></i> supere a la <i><u>Efectividad diaria</u></i> debido a que existe la posibilidad de prestar servicios del periodo antes de su vencimiento por tiempo.<br /><br />
El <i><u>Desfase Previo</u></i> son siniestros que ingresaron dentro del periodo visto pero su <font color='green'>fecha de autorización corresponde a otro periodo</font>.<br /><br />
El <i><u>Desfase Posterior</u></i> son siniestros cuya fecha de autorización es de este periodo pero que <font color='brown'>ingresaron en otro mes, fuera del periodo visto.</font>";


function dia_semana($d)
{
	switch($d)
	{
		case 0: return 'Do';
		case 1: return 'Lu';
		case 2: return 'Ma';
		case 3: return 'Mi';
		case 4: return 'Ju';
		case 5: return 'Vi';
		case 6: return 'Sá';
	}
}

function actualiza_cv()
{
	global $Aseguradora,$Periodo,$Cantidad;
	q("insert ignore into flotames (aseguradora,periodo) values ('$Aseguradora','$Periodo') ");
	q("update flotames set cantidad='$Cantidad' where aseguradora='$Aseguradora' and periodo='$Periodo' ");
	echo "<script language='javascript'>
			function carga()
			{
				window.close();void(null);
				opener.location.reload();
			}
		</script>
		<body onload='carga()'></body>";
}


?>