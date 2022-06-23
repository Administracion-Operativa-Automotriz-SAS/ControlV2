<?php
/*  PROGRAMA PARA MONITOREAR POR CIUDAD LOS VEHICULOS, POR LOGO/SIN LOGO Y POR ESTADO Y POR ASEGURADORA
 *
 * 	Programa gerencial para identificar rápidamente por ciudad donde están y en que estado esta cada vehículo
 *
 */
//die('MODULO EN MANTENIMIENTO.');

include('inc/funciones_.php');
set_time_limit(0);
sesion();
$USUARIO=$_SESSION['User'];
$NUSUARIO=$_SESSION['Nombre'];
if(!empty($Acc) && function_exists($Acc)){ eval($Acc.'();'); die(); }
if($_Exel)
{ header("Content-type: application/vnd.ms-excel");	header("Content-Disposition: attachment; filename=monitor_operativo.xls");}
else
{html(TITULO_APLICACION." - MONITOR OPERATIVO - $NUSUARIO");}

$HOY=date('Y-m-d');
require('inc/link.php');
//VEHICULOS SIN LOGO
//if(!$Periodo) $Periodo=date('Ym');
if($_SESSION['User']==36 /* ASEGURADORA 3*/)
{
	$idAseg=qo1m("select aseguradora from usuario_aseguradora3 where id=".$_SESSION['Id_alterno'],$LINK);
	if(inlist($idAseg,'1,8,9')) // grupo allianz
		$Aseguradoras=mysql_query("select id,nombre from aseguradora where id in (1,8,9) order by orden_monitor",$LINK);
	elseif(inlist($idAseg,'2,5'))  // grupo royal
		$Aseguradoras=mysql_query("select id,nombre from aseguradora where id in (2,5) order by orden_monitor",$LINK);
	elseif(inlist($idAseg,'3,7')) // grupo liberty
		$Aseguradoras=mysql_query("select id,nombre from aseguradora where id in (3,7) order by orden_monitor",$LINK);
	elseif(inlist($idAseg,'4,10')) //  grupo mapfre
		$Aseguradoras=mysql_query("select id,nombre from aseguradora where id in (4,10) order by orden_monitor",$LINK);
}
else $Aseguradoras=mysql_query("select id,nombre from aseguradora where id not in (6) and activo=1 order by orden_monitor",$LINK);
$Opcion_Aseg='';
if($Aseguradoras)
while($Aseg=mysql_fetch_object($Aseguradoras))
{
	$Opcion_Aseg.="<option value='$Aseg->id' ";
	if(strpos(' '.$ASEG,$Aseg->id)) $Opcion_Aseg.='selected';
	$Opcion_Aseg.=">$Aseg->nombre</option>";
}

$Aseguradoras=mysql_query("select id,nombre from aseguradora where ".($ASEG?" id in ($ASEG)":" id not in (6)")." and activo=1 order by orden_monitor",$LINK);

$Flotames=mysql_query("select aseguradora,cantidad from flotames where periodo=$Periodo");
$Acumulados=array();
$Acumulados[1]=0;
$Acumulados[2]=1;
$Acumulados[3]=2;
$Acumulados[4]=3;
$Acumulados[5]=1;
$Acumulados[7]=2;
$Acumulados[8]=0;
$Acumulados[9]=0;
$TAcumulados=array();
$TAcumulados[0]['colspan']=9;$TAcumulados[0]['Nombre']='ALLIANZ';
$TAcumulados[1]['colspan']=5;$TAcumulados[1]['Nombre']='ROYAL';
$TAcumulados[2]['colspan']=5;$TAcumulados[2]['Nombre']='LIBERTY';
$TAcumulados[3]['colspan']=1;$TAcumulados[3]['Nombre']='MAPRE';
$ASeg=array();
$Contador=0;
if($Aseguradoras)
while($Aseg=mysql_fetch_object($Aseguradoras))
{
	$ASeg[$Contador]['id']=$Aseg->id;
	$ASeg[$Contador]['nombre']=$Aseg->nombre;
	$ASeg[$Contador]['solicitudes']=0;
	$ASeg[$Contador]['servicios']=0;
	$ASeg[$Contador]['dias']=0;
	$ASeg[$Contador]['promedio']=0;
	$ASeg[$Contador]['Xml']="<chart caption='$Aseg->nombre'  showValues='0' showLabels='0' yAxisMinValue='10'>";
	$Contador++;
}
if($Flotames)
while($Fm=mysql_fetch_object($Flotames))
{
	for($i=0;$i<count($ASeg);$i++)
	{
		if($ASeg[$i]['id']==$Fm->aseguradora)
		{
			$ASeg[$i]['cv']=$Fm->cantidad;
		}
	}
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
	//Recargar=setTimeout(recarga,180000);

	function actualiza_cv(id,cantidad,periodo)
	{
		modal('zmonitor_operativo.php?Acc=actualiza_cv&Aseguradora='+id+'&Periodo='+periodo+'&Cantidad='+cantidad,0,0,10,10,'acv');
	}
	function validar(Valor)
	{
		var Aseguradoras=getSelected(document.forma.ASEG);
		var Aseg='';
		for(var i=0;i<Aseguradoras.length;i++)
		{ if(i>0) Aseg+=',';
			Aseg+=Aseguradoras[i].value;
		}
		window.open('zmonitor_operativo.php?Periodo='+Valor+'&ASEG='+Aseg,'_self');
	}

	function validar2(Valor)
	{
		var Aseguradoras=getSelected(document.forma.ASEG);
		var Aseg='';
		for(var i=0;i<Aseguradoras.length;i++)
		{ if(i>0) Aseg+=',';
			Aseg+=Aseguradoras[i].value;
		}
		window.open('zmonitor_operativo.php?Periodo='+Valor+'&ASEG='+Aseg+'&_Exel=1','Oculto_monitor');
	}

	function consulta_servicios(aseguradora,dia)
	{
		modal('zmonitor_operativo.php?Acc=consulta_servicios&aseguradora='+aseguradora+'&dia='+dia+'&Periodo=$Periodo',0,0,600,600,'cs');
	}

</script>
<body onload='centrar()'>";
if(!$_Exel)
{
	echo "
	<table><tr><td nowrap='yes' valign='top'><form action='zmonitor_operativo.php' method='post' target='_self' name='forma' id='forma'>

	 Aseguradora: ";
		$Perini="200801";
	echo "
	<select name='ASEG' multiple size=3>$Opcion_Aseg</select></td><td valign='top'>Periodo:
	<select name='Periodo' onchange=\"validar(this.value);\">";
	while($Perini<=date('Ym'))
	{
		echo "<option value='$Perini' ".($Periodo?($Perini==$Periodo?"selected":""):($Perini==date('Ym')?"selected":"")).">$Perini</option>";
		$Perini=aumentaperiodo($Perini,1);
	}
	echo "</select><br>

	<input type='button' value='Continuar' onclick='validar(this.form.Periodo.value);'><br>
	<a style='cursor:pointer' onclick='validar2(document.forma.Periodo.value);'>Obtener en Excel</a>
	</form></td><td nowrap='yes'>[ Fecha Inicial: $Fecha_inicial <br />Fecha Final: $Fecha_final]<td>Puede seleccionar una o varias aseguradoras manteniendo presionada la tecla CTRL o SHIFT y usando el mouse para dar click sobre la que desea procesar</td></table>
	<iframe name='Oculto_monitor' style='visibility:hidden' height='1' width='1'></iframe>";
}

if(!$Periodo) { die("</body>"); }
//**********************************************************************************************************************************************************************
echo "  <hr>
			<table border cellspacing='0' style='empty-cells:show'>
			<tr>
			<th colspan=2>Dia</th>";
			for($i=0;$i<count($ASeg);$i++)
				echo "<th colspan=4>".$ASeg[$i]['nombre']."	".($_Exel?$ASeg[$i]['cv']:"<input type='text' class='numero' name='cv' id='cv' size=3 value='".$ASeg[$i]['cv']."' onblur='actualiza_cv(".$ASeg[$i]['id'].",this.value,$Periodo);' style='background-color:000000;color:ffffee' ".(inlist($USUARIO,'1,2')?"":"readonly").">")."</th>";
			echo "<th colspan=4>Total</th>
			</tr><tr><td>#</td><td>Nombre</td>";
			for($i=0;$i<count($ASeg);$i++) echo "<td>Solicitudes</td><td>Acum.</td><td>Servicios</td><td align='center'>%</td>";
			echo "<td>Solicitudes</td><td>Acum.</td><td>Servicios</td><td align='center'>%</td></tr>";

$dia=1;
$Aing=array();
if($Periodo<'201301')
{
	$Ingresos=mysql_query("select aseguradora,date_format(ingreso,'%d') as dia,count(*) as cantidad from siniestro where date_format(ingreso,'%Y%m')='$Periodo'
	union select aseguradora,date_format(ingreso,'%d') as dia,count(*) as cantidad from siniestro_hst where date_format(ingreso,'%Y%m')='$Periodo'
	group by aseguradora,dia order by aseguradora,dia",$LINK);
}
else
{
	$Ingresos=mysql_query("select aseguradora,date_format(ingreso,'%d') as dia,count(*) as cantidad from siniestro where date_format(ingreso,'%Y%m')='$Periodo'
		group by aseguradora,dia order by aseguradora,dia ",$LINK);
}
if($Ingresos) while($Ingreso=mysql_fetch_object($Ingresos)) $Aing[$Ingreso->aseguradora][$Ingreso->dia*1]=$Ingreso->cantidad;
$Aser=array();
if($Servicios=mysql_query("select flota as aseguradora,date_format(fecha_inicial,'%d') as dia,count(*) as cantidad from ubicacion where date_format(fecha_inicial,'%Y%m')='$Periodo' and estado in (1,7) group by aseguradora,dia order by aseguradora,dia",$LINK))
	while($Ser=mysql_fetch_object($Servicios)) $Aser[$Ser->aseguradora][$Ser->dia*1]=$Ser->cantidad;
$t_dias=0;
mysql_close($LINK);

// ******************************************************************************************************************************************************************************
$xml="<chart caption='EFECTIVIDAD DIARIA ACUMULADA' xAxisName='Dias' yAxisName='Efectividad' showValues='0' logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' showLabels='0' formatNumberScale='0'>";
$xml2="<chart caption='EFECTIVIDAD DIARIA GENERAL EN PORCENTAJE' xAxisName='Dias' yAxisName='Efectividad' showValues='0' showLabels='0' logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' yAxisMinValue='10'> ";
$Xml=array();
$categorias="<categories>";
$serie1="<dataset seriesName='Solicitudes'>";
$serie2="<dataset seriesName='Servicios' renderAs='Area'>";

while($dia<=$Dia_final)
{

	$Fecha=date('Ymd',strtotime($Periodo.str_pad($dia,2,'0',STR_PAD_LEFT)));
	if($Fecha<=date('Ymd')) $categorias.="<category label='$dia'/>";
	$Ndia=dia_semana(date('w',strtotime($Fecha)));
	echo "<tr ".(inlist($Ndia,'Sá,Do')?" bgcolor='ffeeee' ":"")."><td align='center'>$dia</td><td>$Ndia</td>";
	$t_solicitudes=$t_servicios=$d_solicitudes=0;
	$switch_color=false;
	for($i=0;$i<count($ASeg);$i++)
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

		if($Aing[$ASeg[$i]['id']][$dia]>0) $ASeg[$i]['dias']++;
		$ASeg[$i]['solicitudes']+=$Aing[$ASeg[$i]['id']][$dia];
		$ASeg[$i]['servicios']+=$Aser[$ASeg[$i]['id']][$dia];
		$TAcumulados[$Acumulados[$ASeg[$i]['id']]]['solicitudes']+=$Aing[$ASeg[$i]['id']][$dia];
		$TAcumulados[$Acumulados[$ASeg[$i]['id']]]['servicios']+=$Aser[$ASeg[$i]['id']][$dia];

		if($ASeg[$i]['solicitudes'])
		{
			$Porcentaje=round($ASeg[$i]['servicios']/$ASeg[$i]['solicitudes']*100,2);
		}
		else
		{
			$Porcentaje=0;
		}
		if($Fecha<=date('Ymd')) $ASeg[$i]['Xml'].="<set label='$dia' value='".round($Porcentaje,2)."'/>  ";
		echo "<td align='center' bgcolor='$bgcolor'>".($Fecha<=date('Ymd')?$Aing[$ASeg[$i]['id']][$dia]:'')."</td>";
		echo "<td align='center' bgcolor='$bgcolor'>".($Fecha<=date('Ymd')?$ASeg[$i]['solicitudes']:'')."</td>";
		echo "<td align='center' bgcolor='$bgcolor'><a onclick='consulta_servicios(".$ASeg[$i]['id'].",$dia);'>".($Fecha<=date('Ymd')?$ASeg[$i]['servicios']:'')."</td>";
		echo "<td align='center' bgcolor='$bgcolor'>".($Fecha<=date('Ymd')?coma_formatd($Porcentaje,2):'')."</td>";
		$t_solicitudes+=$ASeg[$i]['solicitudes'];
		$d_solicitudes+=$Aing[$ASeg[$i]['id']][$dia];
		$t_servicios+=$ASeg[$i]['servicios'];
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
for($i=0;$i<count($ASeg);$i++) $ASeg[$i]['Xml'].="</chart>";
$categorias.="</categories>";
$serie1.="</dataset>";
$serie2.="</dataset>";
$adicionales="<styles><definition><style name='CanvasAnim' type='animation' param='_xScale' start='0' duration='1' /></definition><application><apply toObject='Canvas' styles='CanvasAnim' /></application></styles>";
$xml.=$categorias.$serie1.$serie2.$adicionales."</chart>";
$xml2.="</chart>";
// ******************************************************************************************************************************************************************************

echo "<tr><td colspan=2>Promedio</td>";

for($i=0;$i<count($ASeg);$i++)
{
	$Solicitudes=$ASeg[$i]['solicitudes'];
	$ASeg[$i]['promedio']=($ASeg[$i]['dias']?round($Solicitudes/$ASeg[$i]['dias'],2):0);
	echo "<td align='center'>".$ASeg[$i]['promedio']."</td><td colspan=3></td>";
}

$Promedio=($t_dias?round($t_solicitudes/$t_dias,2):0);
echo "<td align='center'>$Promedio</td><td colspan=3></td></tr>";
echo "<tr><td colspan=2>Acumulado</td>";

for($i=0;$i<count($TAcumulados);$i++)
{
	if($TAcumulados[$i]['solicitudes'])
	{
		$Porcentaje=($TAcumulados[$i]['solicitudes']?round($TAcumulados[$i]['servicios']/$TAcumulados[$i]['solicitudes']*100,2):0);
		echo "<td colspan='".$TAcumulados[$i]['colspan']."' bgcolor='ffffdd' align='right'>".$TAcumulados[$i]['Nombre']."</td>
				<td align='right'>".coma_format($TAcumulados[$i]['solicitudes'])."</td><td align='right'>".coma_format($TAcumulados[$i]['servicios'])."</td>
				<td align='right' bgcolor='aaffaa'>".coma_formatd($Porcentaje,2)."</td>";
	}

}
echo "</tr>";

$Efec2=array();
$Efectividad=q("SELECT aseguradora,count(id) as total,sum(if(estado=7 or estado=8,1,0)) as servicio FROM siniestro WHERE date_format(fec_autorizacion,'%Y%m')='$Periodo'
							GROUP BY aseguradora ORDER BY aseguradora");
while($Efec=mysql_fetch_object($Efectividad))
{
	$Efec2[$Efec->aseguradora]['Solicitudes']=$Efec->total;
	$Efec2[$Efec->aseguradora]['Servicios']=$Efec->servicio;
	$Efec2[$Efec->aseguradora]['Efectividad']=($Efec->total?round($Efec->servicio/$Efec->total*100,2):0);
}
$Desf=array();
if($Desfase=q("SELECT aseguradora,sum(if(date_format(fec_autorizacion,'%Y%m')!='$Periodo' and date_format(ingreso,'%Y%m')='$Periodo',1,0)) as desfase_previo,
sum(if(date_format(fec_autorizacion,'%Y%m')='$Periodo' and date_format(ingreso,'%Y%m')!='$Periodo',1,0)) as desfase_posterior
FROM siniestro where date_format(fec_autorizacion,'%Y%m')='$Periodo' or date_format(ingreso,'%Y%m')='$Periodo'
GROUP BY aseguradora ORDER BY aseguradora"))
{
	while($Df=mysql_fetch_object($Desfase))
	{
		$Desf[$Df->aseguradora]['Previo']=$Df->desfase_previo;
		$Desf[$Df->aseguradora]['Posterior']=$Df->desfase_posterior;
	}
}
echo "<tr><td colspan=2>Efectividad 2</td>";
require('inc/link.php');
$Inges=array();

for($i=0;$i<count($ASeg);$i++)
{
	$Solicitudes=$ASeg[$i]['solicitudes'];
	$Solicitudes2=$Efec2[$ASeg[$i]['id']]['Solicitudes'];
	$Servicios=$ASeg[$i]['servicios'];
	$Aseguradora=$ASeg[$i]['id'];

	echo "<td>&nbsp;</td><td align='center'>$Solicitudes2</td><td align='center'>".$Efec2[$ASeg[$i]['id']]['Servicios']."</td>
				<td align='center'>".$Efec2[$ASeg[$i]['id']]['Efectividad']."</td>";
	mysql_query("update flotames set sin_mes='$Solicitudes',sin1_mes='$Solicitudes2',servicios='$Servicios',eficiencia=if(cantidad,round(servicios/cantidad,2),0),
	  porc_utilizacion=if(sin_mes>0,round(servicios/sin_mes*100,2),0) where aseguradora=$Aseguradora and periodo='$Periodo'  ",$LINK);
	$Inges[$i]['eficiencia']=qo1m("select eficiencia from flotames where aseguradora=$Aseguradora and periodo='$Periodo'   ",$LINK);
}
mysql_close($LINK);
echo "</tr>";
echo "<tr><td colspan=2>Desf.Previo</td>";
for($i=0;$i<count($ASeg);$i++)
{
	echo "<td>&nbsp;</td><td align='center'>+".$Desf[$ASeg[$i]['id']]['Previo']."</td><td colspan=2></td>";
}
echo "</tr>";
echo "<tr><td colspan=2>Desf.Post.</td>";
for($i=0;$i<count($ASeg);$i++)
{
	echo "<td>&nbsp;</td><td align='center' style='color:ff0000'>-".$Desf[$ASeg[$i]['id']]['Posterior']."</td><td colspan=2></td>";
}
echo "</tr><tr><td colspan=2>Efic. Flota</td>";
$switch_color=false;
for($i=0;$i<count($ASeg);$i++)
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

echo "</tr></table><br />";
if($_Exel || browser_movil()) die("<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />".(browser_movil()?"Dispositivo movil detectado":"")."<br /><br /></body>");
include('inc/chart/Includes/FusionCharts.php');
echo "<table cellspacing=4 align='center'><tr><td>".renderChart("inc/chart/Charts/MSArea.swf","",$xml,"efectividad",500,200,false,false)."</td>
			<td>".renderChart('inc/chart/Charts/Line.swf','',$xml2,"efectividad_general",500,200,false,false)."</td></tr></table>";

echo "<table cellspacing=2 align='center'><tr>";
if($ASeg[0]['solicitudes'] || $ASeg[0]['servicios'] ) echo "<td>".renderChart('inc/chart/Charts/Line.swf','',$ASeg[0]['Xml'],"efectividad_0",330,160,false,false)."</td>";
if($ASeg[1]['solicitudes'] || $ASeg[1]['servicios'] ) echo "<td>".renderChart('inc/chart/Charts/Line.swf','',$ASeg[1]['Xml'],"efectividad_1",330,160,false,false)."</td>";
if($ASeg[4]['solicitudes'] || $ASeg[4]['servicios'] ) echo "<td>".renderChart('inc/chart/Charts/Line.swf','',$ASeg[4]['Xml'],"efectividad_4",330,160,false,false)."</td>";
if($ASeg[5]['solicitudes'] || $ASeg[5]['servicios'] ) echo "<td>".renderChart('inc/chart/Charts/Line.swf','',$ASeg[5]['Xml'],"efectividad_5",330,160,false,false)."</td>";
echo "</tr></table>
		<table cellspacing=2 align='center'><tr>";
if($ASeg[3]['solicitudes'] || $ASeg[3]['servicios'] ) echo "<td>".renderChart('inc/chart/Charts/Line.swf','',$ASeg[3]['Xml'],"efectividad_3",330,160,false,false)."</td>";
if($ASeg[2]['solicitudes'] || $ASeg[2]['servicios'] ) echo "<td>".renderChart('inc/chart/Charts/Line.swf','',$ASeg[2]['Xml'],"efectividad_2",330,160,false,false)."</td>";
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

function consulta_servicios()
{
	global $aseguradora,$dia,$Periodo;
	$NT_siniestro=tu('siniestro','id');
	html('CONSULTA SERVICIOS');
	echo "<script language='javascript'>
			function vers(dato)
			{
				modal('marcoindex.php?Acc=mod_reg&id='+dato+'&Num_Tabla=$NT_siniestro',0,0,700,1000,'vs');
			}
		</script>
		<body><h3>Consulta Servicios Aseguradora: $aseguradora Fecha: $dia $Periodo</h3>";
	if($Servicios=q("select fecha_inicial,id from ubicacion where date_format(fecha_inicial,'%Y%m')='$Periodo' and date_format(fecha_inicial,'%d')<=$dia
							and estado in (1,7) and flota=$aseguradora order by fecha_inicial"))
	{
		$contador=1;$contador_op=0;
		echo "<table ><tr ><th >#</th><th >Fecha Inicial</th><th >Num.Siniestro</th><th >F.Ingreso</th><th >F.Autorizacion</th><th >Estado</th><th >Otros Periodos</th></tr>";
		include('inc/link.php');
		while($S=mysql_fetch_object($Servicios))
		{
			echo "<tr ><td >$contador</td><td >$S->fecha_inicial</td>";
			if($N=qom("select id,numero,ingreso,fec_autorizacion,t_estado_siniestro(estado) as nestado from siniestro where ubicacion=$S->id",$LINK))
			{
				if($NT_siniestro)
					echo "<td onclick='vers($N->id);'>$N->numero</td><td ";
				else
					echo "<td >$N->numero</td><td ";
				if(date('Ym',strtotime($N->ingreso))!=$Periodo)
				{
					$contador_op++;
					$Bgc="ddddff";
					$Salida="<td align='center'>$contador_op</td>";
				}
				else
				{
					$Bgc="ffffff";
					$Salida="<td >&nbsp;</td>";
				}
				echo "bgcolor='$Bgc'>$N->ingreso</td><td >$N->fec_autorizacion</td><td >$N->nestado</td>$Salida";
			}
			else
				echo "<td bgcolor='ffdddd' colspan=4>No tiene Siniestro ($S->id)</td>";
			echo "</tr>";
			$contador++;
		}
		mysql_close($LINK);
		echo "</table>";
	}
	echo "
		</body>";
}

?>