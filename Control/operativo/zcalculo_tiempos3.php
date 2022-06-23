<?php
/**
 *   Programa informe de Tiempos por Aseguradora.
 *
 * @version $Id$
 * @copyright 2010
 */
include('inc/funciones_.php');
sesion();
set_time_limit(0);

/**
 * Clase para la ciudad, debe acumular los casos reportados, los casos atendidos entre 0 y 12 horas y los de mayor a 12 horas,
 * OCTUBRE 29. Se excluira del calculo de tiempos para Liberty los casos que no tengan datos de contacto ni actualización de datos. 
		Se toma en cuenta cuando el filtro de estado EST está en ceros.
		
	FEBRERO 24 2015: Se debe excluir la opcion que aparece para Liberty de la exclusion de no contacto para que aparezca en el perfil de Allianz (Aseguradora 3)
 */
class c_ciudad
{
	var $Casos=0;
	var $Op1=0;
	var $Op2=0;

	function c_ciudad()
	{

	}

}


if(!empty($Acc) && function_exists($Acc)) { eval($Acc.'();'); die(); }
$Rango_colores=array();
$Rango_colores[0]['ini']=0;        $Rango_colores[0]['fin']=0;              $Rango_colores[0]['color']='ffffff';            $Rango_colores[0]['cantidadG']=0;$Rango_colores[0]['cantidadC']=0; $Rango_colores[0]['tag']='Pendiente';
$Rango_colores[1]['ini']=1;        $Rango_colores[1]['fin']=14400;      $Rango_colores[1]['color']='C6FFC2';    $Rango_colores[1]['cantidadG']=0; $Rango_colores[1]['cantidadC']=0;$Rango_colores[1]['tag']='de 0 a 4 horas';
$Rango_colores[2]['ini']=14401;$Rango_colores[2]['fin']=28800;      $Rango_colores[2]['color']='FFFFA4';     $Rango_colores[2]['cantidadG']=0; $Rango_colores[2]['cantidadC']=0;$Rango_colores[2]['tag']='de 4 a 8 horas';
$Rango_colores[3]['ini']=28801;$Rango_colores[3]['fin']=43200;       $Rango_colores[3]['color']='FFC1A3';    $Rango_colores[3]['cantidadG']=0; $Rango_colores[3]['cantidadC']=0;$Rango_colores[3]['tag']='de 8 a 12 horas';
$Rango_colores[4]['ini']=43201; $Rango_colores[4]['fin']=86400;       $Rango_colores[4]['color']='C8F9FF';   $Rango_colores[4]['cantidadG']=0;$Rango_colores[4]['cantidadC']=0; $Rango_colores[4]['tag']='de 12 a 24 horas';
$Rango_colores[5]['ini']=86401; $Rango_colores[5]['fin']=115200;     $Rango_colores[5]['color']='63BBFF';    $Rango_colores[5]['cantidadG']=0;$Rango_colores[5]['cantidadC']=0; $Rango_colores[5]['tag']='de 24 a 32 horas';
$Rango_colores[6]['ini']=115201;$Rango_colores[6]['fin']=99999999; $Rango_colores[6]['color']='C64FFF';     $Rango_colores[6]['cantidadG']=0; $Rango_colores[6]['cantidadC']=0;$Rango_colores[6]['tag']='Más de 32 horas';


if(!$ASEG)  {pide_datos(); die();}
if($ASEG=='3,7') $Aseg->nombre='LIBERTY GAMA ALTA Y GAMA MEDIA';
elseif($ASEG=='1,8,9') $Aseg->nombre='ALLIANZ';
elseif($ASEG=='1,2,3,4,5,7,8,9,10') $Aseg->nombre='Todas las Aseguradoras';
else $Aseg=qo("select * from aseguradora where id =$ASEG");

$_Exel=sino($_Exel);
$_FIEL=sino($_FIEL);
$Causal=array();
$Subcausal=array();
$A_op_ciudad=array();
$Citas=array();
$NTS=tu('siniestro','id');



if($_Exel) { header("Content-type: application/vnd.ms-excel"); header("Content-Disposition: attachment; filename=calculo_tiempos.xls"); }
else { html("CALCULO DE TIEMPOS PARA $Aseg->nombre"); }
echo "<script language='javascript'>
		function verseguimiento(id,contador) { modal('zcalculo_tiempos3.php?Acc=verseguimiento&id='+id+'&Contador='+contador,0,0,10,10,'seg'); }
		function reconstruye_seguimiento(id) { alert('función deshabilitada'); }
		function versiniestro(id) { modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTS&id='+id,0,0,700,900,'seg');}

		function fija_ancho(dato,descuento)
			{document.getElementById(dato+'_').width=document.getElementById(dato).clientWidth-descuento;}

		function valida_scroll()
			{ document.getElementById('_capa_titulo_superior_').style.left=-document.body.scrollLeft+8;
				var Altura=document.getElementById('_Tabla_').offsetTop;
				var Avance=document.body.scrollTop;
				if(Altura-Avance>0)	document.getElementById('_capa_titulo_superior_').style.top=Altura-Avance; else document.getElementById('_capa_titulo_superior_').style.top=0;
				if(document.getElementById('_capa_titulo_lateral_')) document.getElementById('_capa_titulo_lateral_').style.top=-document.body.scrollTop+Altura;}

	</script><body onscroll='valida_scroll()'>";
	
if($_Exel)
{
	echo "Convenciones de colores:<table><tr><td valign='top'><table border cellspacing='0'>";
	for($i=0;$i<count($Rango_colores);$i++)
	{
		echo "<tr><td colspan=2><b>Rango $i</b></td>
		<td bgcolor='".$Rango_colores[$i]['color']."'>-----</td>
		<td>".$Rango_colores[$i]['tag']."</td></tr>";
		if(($i+1) % 4 ==0) echo "</tr></table></td><td valign='top'><table border cellspacing='0'>";
	}
	echo "</table></td></tr></table>";
}
echo "
	<h3 align='center'>ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A. - Cálculo de Tiempos - Aseguradora: $Aseg->nombre</h3>";

if($EST) $F_Estados=" and estado=$EST "; else $F_Estados="";
if($CAU) $F_Causales=" and causal=$CAU "; else $F_Causales="";
if($SUBC) $F_Subcausales=" and subcausal=$SUBC"; else $F_Subcausales="";
if($MOS=='retencion') $F_retencion=" and retencion=1 ";
elseif($MOS=='sinretencion') $F_retencion=" and retencion=0 "; else $F_retencion="";

$FIEL='';
if($_FIEL)
{
	if(!$EST && strpos(' '.$Aseg->nombre,'LIBERTY'))
	{
		echo "LIBERTY FILTRADO ESPECIAL";
		$FIEL=" and if(s.estado in(3,7,8),1,if(s.declarante_tel_resid='' and s.declarante_tel_ofic='' and s.declarante_telefono='' and (s.declarante_celular like '%bound%' 
		or s.declarante_celular='') and
		s.declarate_tel_otro='' and s.conductor_tel_resid='' and s.conductor_tel_ofic='' and s.conductor_celular='' and s.conductor_tel_otro='' and s.actualizacion_aseg='',0,1))";
	}
}

include('inc/link.php');
mysql_query("drop table if exists tmpi_tiempos",$LINK);
if(!mysql_query("create table tmpi_tiempos select s.id,s.numero,a.sigla_gama,s.fec_autorizacion,s.estado,es.nombre as nestado,
		s.ingreso, s.ingreso as gestion,s.ingreso as contacto, t_causal(s.causal) as ncausal,
		s.observaciones,s.placa,c.nombre as nciudad, t_subcausal(s.subcausal) as nsubcausal,s.fecha_inicial,datediff(s.fecha_final,s.fecha_inicial) as dias, s.sucursal_radicadora as sucursal
		FROM siniestro s,estado_siniestro es,ciudad c,aseguradora a
		WHERE s.estado=es.id and s.ciudad=c.codigo and a.id=s.aseguradora and date_format(s.ingreso,'%Y-%m-%d')  between '$FI' and '$FF' and
			s.aseguradora in ($ASEG) $F_Estados $F_Causales $F_Subcausales $F_retencion $FIEL
		UNION
		select s.id,s.numero,a.sigla_gama,s.fec_autorizacion,s.estado,es.nombre as nestado,
		s.ingreso, s.ingreso as gestion,s.ingreso as contacto, t_causal(s.causal) as ncausal,
		s.observaciones,s.placa,c.nombre as nciudad, t_subcausal(s.subcausal) as nsubcausal,s.fecha_inicial,datediff(s.fecha_final,s.fecha_inicial) as dias, s.sucursal_radicadora as sucursal
		FROM siniestro_hst s,estado_siniestro es,ciudad c,aseguradora a
		WHERE s.estado=es.id and s.ciudad=c.codigo and a.id=s.aseguradora and date_format(s.ingreso,'%Y-%m-%d')  between '$FI' and '$FF' and
			s.aseguradora in ($ASEG) $F_Estados $F_Causales $F_Subcausales $F_retencion $FIEL
		ORDER by fec_autorizacion ",$LINK)) die(mysql_error($LINK));
mysql_query("alter table tmpi_tiempos add column remitido datetime default '0000-00-00 00:00:00'  ",$LINK);
mysql_query("alter table tmpi_tiempos add column actual datetime default '0000-00-00 00:00:00'  ",$LINK);
mysql_query("alter table tmpi_tiempos add unique index id (id) ",$LINK);
$Total_Siniestros_mes=qo1m("select count(id) from siniestro where  date_format(ingreso,'%Y-%m-%d') between '$FI' and '$FF' and aseguradora in ($ASEG) ",$LINK);


if($Siniestros=mysql_query("select * from tmpi_tiempos",$LINK))
{ while($S=mysql_fetch_object($Siniestros))
	{ if($Ges=qom("select concat(fecha,' ',hora) as momento from seguimiento where siniestro=$S->id and tipo>2 order by fecha,hora limit 1",$LINK))
		{mysql_query("update tmpi_tiempos set gestion='$Ges->momento' where id=$S->id",$LINK); }
		if($Cox=qom("select concat(fecha,' ',hora) as momento from seguimiento where siniestro=$S->id and tipo in (3,5,6,14,15,17) order by fecha,hora limit 1",$LINK))
		{mysql_query("update tmpi_tiempos set contacto='$Cox->momento' where id=$S->id",$LINK); }
		if($Rem=qom("select concat(fecha,' ',hora) as momento from seguimiento where siniestro=$S->id and tipo=4",$LINK))
		{ mysql_query("update tmpi_tiempos set remitido='$Rem->momento' where id=$S->id",$LINK); }
		if($Act=qom("select concat(fecha,' ',hora) as momento from seguimiento where siniestro=$S->id and tipo=8",$LINK))
		{ mysql_query("update tmpi_tiempos set actual='$Act->momento'  where id=$S->id",$LINK); }}}

mysql_query("drop table if exists tmpi_tiempos2",$LINK);
if(!mysql_query("create table tmpi_tiempos2 select s.id,s.numero,a.sigla_gama,s.fec_autorizacion,s.estado,es.nombre as nestado,
		s.ingreso, s.ingreso as gestion,s.ingreso as contacto, t_causal(s.causal) as ncausal,
		s.observaciones,placa,c.nombre as nciudad, t_subcausal(s.subcausal) as nsubcausal,fecha_inicial,datediff(s.fecha_final,s.fecha_inicial) as dias , s.sucursal_radicadora as sucursal
		FROM siniestro s, estado_siniestro es,ciudad c,aseguradora a
		WHERE s.estado=es.id and s.ciudad=c.codigo and a.id=s.aseguradora and !( date_format(s.ingreso,'%Y-%m-%d') between '$FI' and '$FF') and
			s.aseguradora in ($ASEG) $F_Estados and s.fecha_inicial between '$FI' and '$FF'
		UNION
		select s.id,s.numero,a.sigla_gama,s.fec_autorizacion,s.estado,es.nombre as nestado,
		s.ingreso, s.ingreso as gestion,s.ingreso as contacto, t_causal(s.causal) as ncausal,
		s.observaciones,placa,c.nombre as nciudad, t_subcausal(s.subcausal) as nsubcausal,fecha_inicial,datediff(s.fecha_final,s.fecha_inicial) as dias , s.sucursal_radicadora as sucursal
		FROM siniestro_hst s, estado_siniestro es,ciudad c,aseguradora a
		WHERE s.estado=es.id and s.ciudad=c.codigo and a.id=s.aseguradora and !( date_format(s.ingreso,'%Y-%m-%d') between '$FI' and '$FF') and
			s.aseguradora in ($ASEG) $F_Estados and s.fecha_inicial between '$FI' and '$FF'
		ORDER by fec_autorizacion ",$LINK)) { echo mysql_error($LINK); die();}
mysql_query("alter table tmpi_tiempos2 add column remitido datetime default '0000-00-00 00:00:00'  ",$LINK);
mysql_query("alter table tmpi_tiempos2 add column actual datetime default '0000-00-00 00:00:00'  ",$LINK);
mysql_query("alter table tmpi_tiempos2 add unique index id (id) ",$LINK);

if($Siniestros=mysql_query("select * from tmpi_tiempos2",$LINK))
{ while($S=mysql_fetch_object($Siniestros))
{ if($Ges=qom("select concat(fecha,' ',hora) as momento from seguimiento where siniestro=$S->id and tipo >2 order by fecha,hora limit 1",$LINK))
{mysql_query("update tmpi_tiempos2 set gestion='$Ges->momento' where id=$S->id",$LINK); }
	if($Cox=qom("select concat(fecha,' ',hora) as momento from seguimiento where siniestro=$S->id and tipo in (3,5,6,14,15,17) order by fecha,hora limit 1",$LINK))
	{mysql_query("update tmpi_tiempos2 set contacto='$Cox->momento' where id=$S->id",$LINK); }
	if($Rem=qom("select concat(fecha,' ',hora) as momento from seguimiento where siniestro=$S->id and tipo=4",$LINK))
	{ mysql_query("update tmpi_tiempos2 set remitido='$Rem->momento' where id=$S->id",$LINK); }
	if($Act=qom("select concat(fecha,' ',hora) as momento from seguimiento where siniestro=$S->id and tipo=8",$LINK))
	{ mysql_query("update tmpi_tiempos2 set actual='$Act->momento'  where id=$S->id",$LINK); }}}

if($Ciudades=mysql_query("select distinct nciudad from tmpi_tiempos order by nciudad",$LINK))
{ while($Ci=mysql_fetch_object($Ciudades))
	{ $A_op_ciudad[$Ci->nciudad]= new c_ciudad(); } }

if($Ciudades=mysql_query("select distinct nciudad from tmpi_tiempos2 where nciudad not in (select distinct nciudad from tmpi_tiempos) order by nciudad",$LINK))
{ while($Ci=mysql_fetch_object($Ciudades))
	{ $A_op_ciudad[$Ci->nciudad]= new c_ciudad();}}


if($QCitas=mysql_query("select siniestro from cita_servicio where fecha between '$FI' and '$FF' and estado='P' "))
{while($Cita=mysql_fetch_object($QCitas)){$Citas[$Cita->siniestro]=1;}}


if($Siniestros=mysql_query("select * from tmpi_tiempos ",$LINK))
{
	$A_titulos=array();
	//print_r($A_titulos);
	//echo 'entre aca';
	$A_titulos['contador']='#';$A_titulos['numero']='Numero'; $A_titulos['Gama']='Gama';$A_titulos['fau']='Fecha Autorización'; $A_titulos['estado']='Estado'; $A_titulos['ingreso']='Ingreso'; $A_titulos['gestion']='Gestión';
	$A_titulos['contacto']='Contacto'; $A_titulos['dgestion']='Dif. Gestión'; $A_titulos['dcontacto']='Dif. Contacto'; $A_titulos['causal']='Causal'; $A_titulos['subcausal']='Subcausal';
	$A_titulos['remitido']='Remitido'; $A_titulos['actualizacion']='Actualización'; $A_titulos['diferencia']='Diferencia'; $A_titulos['placa']='Placa'; $A_titulos['ciudad']='Ciudad';
	$A_titulos['finicial']='Fec.Ini.Serv.';$A_titulos['dias']='Dias.Serv.';$A_titulos['sucursal']='Sucursal.';
	//print_r($A_titulos);
	echo 'siniestros :';
	echo '<br>';
	//print_r($Siniestros);
	if($_Exel) echo "<table border cellspacing='0' style='empty-cells:show'><tr><th>#</th><th>Numero</th><th>Gama</th><th>Fecha Autorización</th><th>Estado</th>
				<th>Ingreso</th><th>Gestion</th><th>Contacto</th><th>Dif. Gestion</th><th>Dif. Contacto</th><th>Causal</th><th>Subcausal</th>
				<th>Remitido</th><th>Actualización</th><th>Diferencia</th><th>Placa</th><th>Ciudad</th><th>Observaciones</th><th>Rango Gestión</th><th>Rango Contacto</th>
				<th>Fec.Ini.Serv.</th><th>Dias.Serv.</th><th>Sucursal</th></tr>";
	if(!$_Exel) fija_titulo_superior($A_titulos,"border bgcolor='ffffff' style='empty-cells:show' cellspacing='0' width='100%' ",1);
	$Contador=0; $CantidadG=0; $CantidadC=0; $ContadorG=0; $ContadorC=0; $Erroneos=0;$Conteo=0;$Actualizados=0;
	$A_estados=array();$A_estados[1]=0; $A_estados[3]=0; $A_estados[5]=0; $A_estados[7]=0; $A_estados[8]=0;
	$A_servicios=array();$A_servicios['concluidosm']=0;$A_servicios['serviciom']=0;$A_servicios['concluidoso']=0;$A_servicios['servicioo']=0;
	$A_adjudicados=0;
	$A_sin_ciudades=array();
	if(!$_Exel) fija_titulo_superior($A_titulos,"border bgcolor='ffffff' style='empty-cells:show' cellspacing='0' width='100%' ",2);
	while($S=mysql_fetch_object($Siniestros))
	{
	    //print_r($S);		
		$Contador++; pinta_detalle($S,'ffffff',true);$A_sin_ciudades[$S->nciudad]++;
	}

	$Solicitudes_mes=$Contador;

	////////////////////////////////////////////////////////////////////   BUSQUEDA DE SINIESTROS ATENDIDOS DE MESES DISTINTOS EN ESTE PERIODO ////////////////////////////////////////////////////////////////

	if($Siniestros2=mysql_query("select * from tmpi_tiempos2"))
	{
		echo "<tr ><td bgcolor='ffffff' colspan='16'>&nbsp;</td></tr>
		<tr><td bgcolor='dddddd' align='center' colspan='20'><b style='font-size:16px'>SINIESTROS DE PERIODOS ANTERIORES CON SERVICIOS EN ESTE PERIODO</b></td></tr>";
			$Contador=0;$Sin_ma=0;
		while($S=mysql_fetch_object($Siniestros2))
		{ $Contador++; $Sin_ma++; pinta_detalle($S,'ffffff',false);
			//$A_sin_ciudades[$S->nciudad]++;
		}
	}

	////////////////////////////////////////////////////////////////////   BUSQUEDA DE SINIESTROS ATENDIDOS DE MESES DISTINTOS EN ESTE PERIODO ////////////////////////////////////////////////////////////////

	echo "</table>";
	fija_titulo_superior($A_titulos,"",3);

	if($Total_Siniestros_mes)
	{
	//	$Porcentaje_efectividad=round(($A_estados[3]+$A_estados[8]+$A_estados[7])/($Total_Siniestros_mes+$Sin_ma)*100,2);
	//	$Porcentaje_efectividad=round(($A_estados[3]+$A_estados[8]+$A_estados[7])/($Total_Siniestros_mes)*100,2);
		$Porcentaje_efectividad=round(($Conteo)/$Total_Siniestros_mes*100,2);

	//	$Porcentaje_pendientes=round($A_estados[5]/($Total_Siniestros_mes+$Sin_ma)*100,2);
		$Porcentaje_pendientes=round($A_estados[5]/($Total_Siniestros_mes)*100,2);

	//	$Porcentaje_noadj=round($A_estados[1]/($Total_Siniestros_mes+$Sin_ma)*100,2);
		$Porcentaje_noadj=round($A_estados[1]/($Total_Siniestros_mes)*100,2);

	}
	else
	{
		$Porcentaje_efectividad=0;$Porentaje_pendientes=0;$Porcentaje_noadj=0;
	}
	echo "<br /><br /><h3>RESUMEN ESTADISTICO</H3><br /><br /><table border cellspacing='0' width='100%'>
					<tr ><td><table width='100%' cellspacing='20'><tr ><td width='50%' valign='top'>";
	/* echo "<table border cellspacing='0' width='100%'><tr ><td>
					<table width='100%' cellspacing='0'><tr ><td bgcolor='505099' style='color:ffffff'>Número de siniestros reportados a AOA en el periodo</td>
					<td align='right' bgcolor='505099' style='color:ffffff'>".coma_format($Total_Siniestros_mes)."</td>
					</tr><tr ><td >Total servicios adjudicados</td><td align='right'>".coma_format($A_estados[3])."</td></tr>
					<tr ><td >Total servicios concluidos</td><td align='right'>".coma_format($A_servicios['concluidos'])."</td></tr>
					<tr ><td >Total siniestros en servicio</td><td align='right'>".coma_format($A_servicios['servicio'])."</td></tr>
					<tr ><td bgcolor='505099' style='color:ffffff'>% de efectividad</td><td align='right' bgcolor='505099' style='color:ffffff'>$Porcentaje_efectividad %</td></tr>
					</table></td></tr></table>
					<br /><table border cellspacing='0' width='100%'><tr ><td><table width='100%' cellspacing='0'>
					<tr ><td >Total de casos pendientes</td><td align='right'>".coma_format($A_estados[5])."</td></tr>
					<tr ><td bgcolor='505099' style='color:ffffff'>% de participación</td><td align='right' bgcolor='505099' style='color:ffffff'>$Porcentaje_pendientes %</td></tr>
					</table></td></tr></table><br />
					<table border cellspacing='0' width='100%'><tr ><td ><table width='100%' cellspacing='0'>
					<tr ><td >Total de casos no aceptados</td><td align='right'>".coma_format($A_estados[1])."</td></tr>
					<tr ><td bgcolor='505099' style='color:ffffff'>% de participación</td><td align='right' bgcolor='505099' style='color:ffffff'>$Porcentaje_noadj %</td></tr>
	 				</table></td></tr></table>";*/
	$Total=$A_estados[5]+$A_estados[1]+$A_estados[7]+$A_estados[3]+$A_estados[8];
	if($Total)
	{
		$P1=round($A_estados[5]/$Total*100,2);
		$P2=round($A_estados[1]/$Total*100,2);
		$P3=round($A_estados[7]/$Total*100,2);
		$P4=round($A_estados[3]/$Total*100,2);
		$P5=round($A_estados[8]/$Total*100,2);
		$TP=$P1+$P2+$P3+$P4+$P5;
	}
	else
		$P1=$P2=$P3=$P4=$P5=0;
	$Total_servicios_mes=$A_servicios['serviciom']+$A_servicios['concluidosm']+$A_servicios['servicioo']+$A_servicios['concluidoso'];
	if($Total)
		$Porcentaje_efectividad=round($Total_servicios_mes/$Total*100,2); else $Porcentaje_efectividad=0;
	echo "<table cellspacing='5' width='100%'>
			<tr>
			<td align='center' ><table border cellspacing=0><tr><td bgcolor='505099'  style='color:ffffff'><b>Total Solicitudes del Periodo</b></td><td><b>".coma_format($Total_Siniestros_mes)."</b></td></tr></table></td>
			<td><table border cellspacing='0'><tr><td colspan=3 bgcolor='505099'  style='color:ffffff'></b>Distribución<b></td></tr>
					<tr><td>Pendientes</td><td align='right'>".coma_format($A_estados[5])."</td><td align='right'>".coma_formatd($P1,2)." %</td></tr>
					<tr><td>No Adjudicados</td><td align='right'>".coma_format($A_estados[1])."</td><td align='right'>".coma_formatd($P2,2)." %</td></tr>
					<tr><td>En Servicio</td><td align='right'>".coma_format($A_estados[7])."</td><td align='right'>".coma_formatd($P3,2)." %</td></tr>
					<tr><td>Adjudicados</td><td align='right'>".coma_format($A_estados[3])."</td><td align='right'>".coma_formatd($P4,2)." %</td></tr>
					<tr><td>Prestados</td><td align='right'>".coma_format($A_estados[8])."</td><td align='right'>".coma_formatd($P5,2)." %</td></tr>
					<tr><td><b>TOTAL</td><td align='right'><b>".coma_format($Total)."</b></td><td align='right'>".coma_formatd($TP,2)." %</td></tr>
					</table></td>
			<td><table border cellspacing='0'>
				<tr><td  bgcolor='505099'  style='color:ffffff' colspan=3>Servicios del Periodo</td></tr>
				<tr><td >Servicios sin concluir de Siniestros del periodo</td><td align='right'>".coma_format($A_servicios['serviciom'])."</td></tr>
				<tr><td >Servicios prestados de Siniestros del periodo</td><td align='right'>".coma_format($A_servicios['concluidosm'])."</td></tr>
				<tr><td >Servicios sin concluir de Siniestros de otros meses</td><td align='right'>".coma_format($A_servicios['servicioo'])."</td></tr>
				<tr><td >Servicios prestados de Siniestros de otros meses</td><td align='right'>".coma_format($A_servicios['concluidoso'])."</td></tr>
				</table><br>
				<table border cellspacing='0'>
				<tr><td bgcolor='505099'  style='color:ffffff'>Total Servicios Prestados en el Periodo</td><td align='right'><b>".coma_format($Total_servicios_mes)."</b></td></tr>
				<tr><td>Total Solicitudes en el periodo</td><td align='right'><b>".coma_format($Total)."</b></td></tr>
				<tr><td bgcolor='505099'  style='color:ffffff'><b>PORCENTAJE DE UTILIZACION</b></td><td align='right'><b>".coma_formatd($Porcentaje_efectividad,2)." %</b></td></tr>
				</table></td>
			</tr></table>";				
					
	echo "<br /><table border cellspacing='0' width='100%'>
					<tr ><td bgcolor='505099' style='color:ffffff' align='center' colspan='3'>TIPIFICACION DE CASOS NO ACEPTADOS</td></tr>
					<tr ><td bgcolor='505099' style='color:ffffff' align='center'>CAUSA</td><td bgcolor='505099' style='color:ffffff' align='center'>#</td><td bgcolor='505099' style='color:ffffff' align='center'>%</td></tr>";
	ksort($Causal);
	$Total_cancelacion_post_adjudicacion=0;
	foreach($Causal as $llave => $cantidad)
	{
		if($A_estados[1]) $Porc_parcial=round($cantidad/$A_estados[1]*100,3); else $Porc_parcial=0;
		echo "<tr ><td >$llave</td><td align='center'>$cantidad</td><td align='right'>".coma_formatd($Porc_parcial,2)." %</td></tr>";
		if(l($llave,29)=='CANCELACION POST-ADJUDICACION') $Total_cancelacion_post_adjudicacion=$cantidad;
	}
	echo "<tr ><td ><b>TOTAL</b></td><td align='center'><b>".$A_estados[1]."</b></td><td align='right'><b>100%</b></td></tr></table></td><td width='50%' valign='top'>";
	ksort($A_sin_ciudades);
	echo "<table border width='100%' cellspacing='0'><tr><td bgcolor='505099' style='color:ffffff' align='center' colspan='3'>NO. SINIESTROS POR CIUDAD</td></tr>
				<tr ><td bgcolor='505099' style='color:ffffff' align='center'>CIUDAD</td><td bgcolor='505099' style='color:ffffff' align='center'>#</td><td bgcolor='505099' style='color:ffffff' align='center'>%</td></tr>";
	$Tot_ciudad=0;$Tot_porc=0;
	foreach($A_sin_ciudades as $llave => $cantidad)
	{ if($Total_Siniestros_mes) $Porc_parcial=round($cantidad/($Total_Siniestros_mes)*100,3); else $Porc_parcial=0;
		echo "<tr ><td >$llave</td><td align='center'>$cantidad</td><td align='right'>".coma_formatd($Porc_parcial,2)." %</td></tr>";
		$Tot_ciudad+=$cantidad;$Tot_porc+=$Porc_parcial;}
	echo "<tr ><td align='center'><b>TOTAL</b></td><td align='right'><b>".coma_format($Tot_ciudad)."</b></td><td align='right'><b>".coma_formatd($Tot_porc,2)." %</b></td></tr>
				</table><br /><table border width='100%' cellspacing='0'><tr><td bgcolor='505099' style='color:ffffff' align='center' colspan='3'>TIPIFICACIONES CANCELACION POST-ADJUDICACION</td></tr>
				<tr ><td bgcolor='505099' style='color:ffffff' align='center'>CAUSAL</td><td bgcolor='505099' style='color:ffffff' align='center'>#</td><td bgcolor='505099' style='color:ffffff' align='center'>%</td></tr>";
	ksort($Subcausal);
	$Tot_porc=0;$Tot_casos=0;
	foreach($Subcausal as $llave => $cantidad)
	{ if(strpos(' '.$llave,'CANCELACION POST-ADJUDICACION'))
		{ if($Total_cancelacion_post_adjudicacion) $Porc_parcial=round($cantidad/$Total_cancelacion_post_adjudicacion*100,3); else $Porc_parcial=0;
			echo "<tr ><td >$llave</td><td align='center'>$cantidad</td><td align='center'>".coma_formatd($Porc_parcial,2)." %</td></tr>";
			$Tot_porc+=$Porc_parcial;$Tot_casos+=$cantidad;}}
	echo "<tr ><td align='center'><b>TOTAL</b></td><td align='right'><b>".coma_format($Tot_casos)."</b></td><td align='right'><b>".coma_formatd($Tot_porc,2)." %</b></td></tr>
				</table></td></tr></table></td></tr><tr ><td >
				<table width='100%'><tr ><td bgcolor='505099' style='color:ffffff' align='center'><b>OPORTUNIDAD EN EL CONTACTO POR CIUDAD</b></td></tr></table>
				<table width='100%'><tr ><td ><table width='100%' cellspacing='20'><tr>";
	$Columna=0;$TCasos=0;$TOp1=0;$TOp2=0;
	foreach($A_op_ciudad as $llave => $contenido)
	{
		$Casos=$A_op_ciudad[$llave]->Casos;$Op1=$A_op_ciudad[$llave]->Op1;$Op2=$A_op_ciudad[$llave]->Op2;$Oportunidad=$Casos?round($Op1/$Casos*100,2):0;
		echo "<td align='center'><table border cellspacing='0'><tr ><td ><table width='200px'><tr ><td bgcolor='505099' colspan='2' align='center' style='color:ffffff'>$llave</td></tr>
					<tr ><td >CASOS</td><td align='right'>$Casos</td></tr>
					<tr ><td >0 - 12 HORAS</td><td align='right'>$Op1</td></tr>
					<tr ><td >MAYOR A 12 HORAS</td><td align='right'>$Op2</td></tr>
					<tr ><td >% OPORTUNIDAD</td><td align='right'><B>$Oportunidad %</B></td></tr></table>
					</td></tr></table></td>";
		$TCasos+=$Casos;$TOp1+=$Op1;$TOp2+=$Op2;
		$Columna++;
		if($Columna<2){	echo "";} else {echo "</tr><tr>"; $Columna=0;}
	}
	$Oportunidad=$TCasos?round($TOp1/$TCasos*100,2):0;
	echo "</table></td><td align='center'>
		<table border cellspacing='0'><tr ><td ><table width='200px'><tr ><td bgcolor='505099' colspan='2' align='center' style='color:ffffff'>NACIONAL</td></tr>
		<tr ><td >CASOS</td><td align='right'>$TCasos</td></tr>
		<tr ><td >0 - 12 HORAS</td><td align='right'>$TOp1</td></tr>
		<tr ><td >MAYOR A 12 HORAS</td><td align='right'>$TOp2</td></tr>
		<tr ><td >% OPORTUNIDAD</td><td align='right'><B>$Oportunidad %</B></td></tr></table>
		</td></tr></table>
	</td>
	</tr></table>";



	echo "</td></tr>
	</table>";

	if(inlist($_SESSION['User'],'11,36') /*Aseguradora nivel avanzado 1*/) { die("</body>"); }

	//////////////////////////////////////////////////////////////    E S T A D I S T I C A S //////////////////////////////////////////////////////////////////////////////////////////////
	echo "<br /><br /><br /><h2>ESTADISTICAS ADICIONALES</H2>
	<table cellpadding=5><tr><th colspan=6>Estadisticas [Total siniestros = $Total_Siniestros_mes]</th></tr>
	<tr><td valign='top'>";
	if(inlist($_SESSION['User'],'1,2,3,26'))
	{
		if($ContadorG) $PromedioG=round($CantidadG/$ContadorG,2); else $PromedioG=0;
		echo "<table><tr><th>Promedio entre Ingreso <br />y Gestion Call Center:</th></tr><tr><td>".segundos2horas($PromedioG)."</td>";
		if($ContadorC) $PromedioC=round($CantidadC/$ContadorC,2); else $PromedioC=0;
		echo "</tr><tr><th>Promedio entre Ingreso <br />y Contacto Exitoso:</th></tr><tr><td>".segundos2horas($PromedioC)."</td>";
		echo "</tr></table>";
	}

	echo "</td><td valign='top'><table border cellspacing='0'><tr><th colspan=4>Cantidad y porcentaje por rangos de Gestion Call Center</th></tr>
			<tr><th>Rango</th><th>Desde - Hasta</th><th>Cantidad</th><th>Porcentaje</th></tr>";
	$SporcG=0;
	for($i=0;$i<count($Rango_colores);$i++)
	{
		$CantidadG=$Rango_colores[$i]['cantidadG'];
		$PorcentajeG=($ContadorG?coma_formatd(round($CantidadG/$ContadorG*100,2),2):0);
		$Inicial=$Rango_colores[$i]['tag'];
		//	$Final=segundos2tiempo($Rango_colores[$i]['fin']);
		echo "<tr><td>Rango $i</td><td>$Inicial </td><td align='right'>$CantidadG</td><td align='right'>$PorcentajeG %</td>";
		$SporcG+=$PorcentajeG;
	}
	echo "</table></td>";

	echo "</td><td valign='top'><table border cellspacing='0'><tr><th colspan=4>Cantidad y porcentaje por rangos de Contactos Exitosos</th></tr>
			<tr><th>Rango</th><th>Desde - Hasta</th><th>Cantidad</th><th>Porcentaje</th></tr>";
	$SporcC=0;
	for($i=0;$i<count($Rango_colores);$i++)
	{
		$CantidadC=$Rango_colores[$i]['cantidadC'];
		$PorcentajeC=($ContadorC?coma_formatd(round($CantidadC/$ContadorC*100,2),2):0);
		$Inicial=$Rango_colores[$i]['tag'];
		//	$Final=segundos2tiempo($Rango_colores[$i]['fin']);
		echo "<tr><td>Rango $i</td><td>$Inicial </td><td align='right'>$CantidadC</td><td align='right'>$PorcentajeC %</td>";
		$SporcC+=$PorcentajeC;
	}
	echo "</table></td>";

	echo "<td align='center' valign='top'>";
	$No_adjudicados=0;
	if(count($Causal))
	{
		foreach($Causal as $Id => $Cantidad) $No_adjudicados+=$Cantidad;
		$Porcentaje=coma_formatd(round($No_adjudicados/$Solicitudes_mes*100,2),2);
		echo "<table border cellspacing='0'><th colspan=2>No adjudicados : $No_adjudicados de $Solicitudes_mes = $Porcentaje % </th><th>Cantidad</th><th>% No Adj</th><th>% Total</th></tr>";
		foreach($Causal as $Id => $Cantidad)
		{
			$Porcentaje=coma_formatd(round($Cantidad/$No_adjudicados*100,2),2);
			$Porcentaje2=coma_formatd(round($Cantidad/$Total_Siniestros_mes*100,2),2);
			echo "<tr><td colspan=2>$Id</td><td align='right'>$Cantidad</td><td align='right'>$Porcentaje %</td><td align='right'>$Porcentaje2 %</td></tr>";
		}
		echo "</table>";
	}
	else echo "No hay Causales.";
	echo "</td>";

	echo "<td align='center' valign='top'>";
	$No_adjudicados=0;
	if(count($Subcausal))
	{
		foreach($Subcausal as $Id => $Cantidad) $No_adjudicados+=$Cantidad;
		$Porcentaje=coma_formatd(round($No_adjudicados/$Solicitudes_mes*100,2),2);
		echo "<table border cellspacing='0'><th colspan=2>No adjudicados : $No_adjudicados de $Solicitudes_mes = $Porcentaje % </th><th>Cantidad</th><th>% No Adj</th><th>% Total</th></tr>";
		foreach($Subcausal as $Id => $Cantidad)
		{
			$Porcentaje=coma_formatd(round($Cantidad/$No_adjudicados*100,2),2);
			$Porcentaje2=coma_formatd(round($Cantidad/$Total_Siniestros_mes*100,2),2);
			echo "<tr><td colspan=2>$Id</td><td align='right'>$Cantidad</td><td align='right'>$Porcentaje %</td><td align='right'>$Porcentaje2 %</td></tr>";
		}
		echo "</table>";
	}
	else echo "No hay Causales.";
	echo "</td>";

	echo "<td align='center' valign='top'>";
	$Porcentaje=round($Erroneos/$Solicitudes_mes*100,2);
	$Porcentajea=round($Actualizados/$Solicitudes_mes*100,2);
	echo "<table><tr><th>Información erronea:</th></tr><tr><td> $Erroneos de $Solicitudes_mes = $Porcentaje % </td></tr>
			<tr><td>Actualizados: $Actualizados  de $Solicitudes_mes = $Porcentajea %</td></tr></table></td></table>";
}
else
{
	echo "No hay información que coincida con: Fecha inicial: $FI   Fecha final: $FF  Aseguradora: $Aseg->nombre ";
}
echo "</body>";

function pinta_detalle($S,$BGC='ffffff',$Acumula=true)
{
	global $Contador,$Rango_colores,$Rango1_excel,$Causal,$Subcausal,$CantidadG,$CantidadC,$ContadorG,$ContadorC,$Erroneos,$A_estados,$A_sin_ciudades,$A_op_ciudad,$_Exel,$Conteo,$FI,$FF,
		$A_servicios,$A_adjudicados,$Citas,$Actualizados;
	//if($S->estado==3){if($Citas[$S->id]) $A_estados[$S->estado]++; } else $A_estados[$S->estado]++;
	if($Acumula) $A_estados[$S->estado]++;
	if($Acumula) $A_op_ciudad[$S->nciudad]->Casos++;
	echo "<tr bgcolor='$BGC'><td align='center'>$Contador</td><td align='center' ".(inlist($_SESSION['User'],'1,2,3,26')?"onclick='verseguimiento($S->id,$Contador);' style='cursor:pointer' ":"")." nowrap='yes'>$S->numero</td><td align='center'>$S->sigla_gama</td><td align='center' ";
	if(inlist($_SESSION['User'],'1,2,3,26')) echo " style='cursor:pointer;' onclick='versiniestro($S->id);'";
	echo ">";
	if($_Exel) echo $S->fec_autorizacion;
	else { if(inlist($_SESSION['User'],'1,2,3,26')) echo "<a class='info' >$S->fec_autorizacion<span style='width:500px'>".nl2br($S->observaciones)."</span></a>"; else echo $S->fec_autorizacion; }
	echo "</td><td align='center' nowrap='yes'";
	echo ">$S->nestado</td><td align='center' nowrap='yes'>$S->ingreso</td><td align='center' nowrap='yes'>$S->gestion</td><td align='center' nowrap='yes'>".($S->contacto>$S->ingreso?$S->contacto:'')."</td>";
	////////// GESTION
	$SegundosG=segundos_habiles(($S->actual!='0000-00-00 00:00:00' && $S->actual<$S->gestion?$S->actual:$S->ingreso),$S->gestion,true);
	for($i=0;$i<count($Rango_colores);$i++)
	{ if($SegundosG >= $Rango_colores[$i]['ini'] && $SegundosG <= $Rango_colores[$i]['fin']) { $ColorG=$Rango_colores[$i]['color']; $Rango_colores[$i]['cantidadG']++; $Rango1_excel=$i; } }
	echo "<td align='center' bgcolor='$ColorG' nowrap='yes'>".($SegundosG?segundos2horas($SegundosG):'')."</td>";
	/////////// CONTACTO
	if($S->actual!='0000-00-00 00:00:00')
	{
		if($S->contacto>$S->actual)
			$SegundosC=segundos_habiles($S->actual,$S->contacto,true);
		else
			$SegundosC=segundos_habiles($S->ingreso,$S->contacto,true);
	}
	else 
		$SegundosC=segundos_habiles($S->ingreso,$S->contacto,true);
	for($i=0;$i<count($Rango_colores);$i++)
	{
		if($SegundosC >= $Rango_colores[$i]['ini'] && $SegundosC <= $Rango_colores[$i]['fin'])
		{
			$ColorC=$Rango_colores[$i]['color'];
			$Rango_colores[$i]['cantidadC']++;
			$Rango2_excel=$i;
			if($Acumula) { if($i<5) $A_op_ciudad[$S->nciudad]->Op1++; else $A_op_ciudad[$S->nciudad]->Op2++; }
			break;
		}
	}
	echo "<td align='center' bgcolor='$ColorC' nowrap='yes'>".($SegundosC?segundos2horas($SegundosC):'')."</td>";
	//////////////
	if($S->estado==1)
	{ echo "<td nowrap='yes'>$S->ncausal</td><td nowrap='yes'>".substr($S->nsubcausal,strpos($S->nsubcausal,' - '))."</td>";
		$Causal[$S->ncausal]++; $Subcausal[$S->ncausal.' - '.$S->nsubcausal]++; }
	else echo "<td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td nowrap='yes'>".($S->remitido!='0000-00-00 00:00:00'?$S->remitido:'')."</td><td nowrap='yes'>".($S->actual!='0000-00-00 00:00:00'?$S->actual:'')."</td>";
	
	if($S->remitido && $S->actual)
	{ $Segundosa=$S->remitido!='0000-00-00 00:00:00'?segundos_habiles($S->remitido,$S->actual,true):0;
		for($i=0;$i<count($Rango_colores);$i++) { if($Segundosa >= $Rango_colores[$i]['ini'] && $Segundosa<= $Rango_colores[$i]['fin']) { $Color=$Rango_colores[$i]['color']; } }
		echo "<td align='center' bgcolor='$Color' nowrap='yes'>".($Segundosa?segundos2horas($Segundosa):'')."</td>"; }
	else { echo "<td></td>"; }
	if($S->remitido!='0000-00-00 00:00:00') $Erroneos++;
	if($SegundosG>0) { $CantidadG+=$SegundosG;$ContadorG++; }
	if($SegundosC>0) { $CantidadC+=$SegundosC;$ContadorC++; }
	echo "<td>$S->placa</td><td nowrap='yes'>$S->nciudad</td>";
	if(inlist($S->estado,'7,8'))
	{
		if($S->fecha_inicial>=$FI && $S->fecha_inicial<=$FF && $Acumula)
		{
			$Conteo++;
			if($S->estado==8) $A_servicios['concluidosm']++; else $A_servicios['serviciom']++;
			//echo "<td>$Conteo</td>";
		}
		elseif($S->fecha_inicial>=$FI && $S->fecha_inicial<=$FF)
		{
			if($S->estado==8) $A_servicios['concluidoso']++; else $A_servicios['servicioo']++;
		}
	}
	if($S->fecha_inicial>=$FI && $S->fecha_inicial<=$FF)
	{
		if($S->actual) $Actualizados++;
	}
	if($_Exel) echo "<td>$S->observaciones</td><td>$Rango1_excel</td><td >$Rango2_excel</td>";
	echo "<td>$S->fecha_inicial</td><td align='right'>$S->dias</td>";
	echo "<td>$S->sucursal</td>";
	echo "</tr>";
}

function pide_datos()
{
	global $Rango_colores;
	if($_SESSION['User']==11) // ASEGURADORA NIVEL AVANZADO 1
	{
		$Aseg=qo1("select aseguradora from usuario_aseguradora1 where id=".$_SESSION['Id_alterno']);
		if($Aseg==3 || $Aseg==7) $Aseg='3,7';
		if($Aseg==1) $Aseg='1,8,9';
	}
	elseif($_SESSION['User']==29) // ASEGURADORA NIVEL AVANZADO 2
	{
		$Aseg=qo1("select aseguradora from usuario_aseguradora2 where id=".$_SESSION['Id_alterno']);
		if($Aseg==3 || $Aseg==7) $Aseg='3,7';
		if($Aseg==1) $Aseg='1,8,9';
	}
	elseif($_SESSION['User']==36) // ASEGURADORA NIVEL AVANZADO 3
	{
		$Aseg=qo1("select aseguradora from usuario_aseguradora3 where id=".$_SESSION['Id_alterno']);
		if($Aseg==3 || $Aseg==7) $Aseg='3,7';
		if($Aseg==1) $Aseg='1,8,9';
	}
	else
		$Aseg=0;

	html('CALCULO DE TIEMPOS POR ASEGURADORA');
	echo "<script language='javascript'>
			function carga()
			{
				centrar();
				document.getElementById('IResultado').style.height=document.body.clientHeight-185;
			}
			function validar()
			{
				carga();
				document.forma.submit();
			}
		</script>
		<body onload='carga()'>";
		
		/// VERIFICACION DE FECHAS INICIALES Y FINALES DE LOS CONCLUIDOS Y EN SERViCIO
$Fecha_verificacion=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-90)));
//echo $Fecha_verificacion;
q("update siniestro,ubicacion set siniestro.fecha_inicial=ubicacion.fecha_inicial, siniestro.fecha_final=ubicacion.fecha_final where
siniestro.ubicacion=ubicacion.id and ubicacion.fecha_inicial >= '$Fecha_verificacion' ");
		
		echo "<form action='zcalculo_tiempos3.php' method='post' target='IResultado' name='forma' id='forma'>
			Aseguradora: ";
	if($_SESSION['User']==11 /*Aseguradora nivel avanzado 1*/)
	{
		$Aseg=qo1("select aseguradora from usuario_aseguradora1 where id=".$_SESSION['Id_alterno']);
		if($Aseg==3 || $Aseg==7)
		{
			echo "<select name='ASEG'><option value='3,7'>LIBERTY GA Y GM</option></select>";
		}
		elseif($Aseg==1)
		{
			echo "<select name='ASEG'><option value='1,8,9'>ALLIANZ todas las gamas</option></select>";
		}
		else
			echo menu1("ASEG","Select id,nombre from aseguradora ".($Aseg?" where id in ($Aseg) ":""));
	}
	elseif($_SESSION['User']==36)
	{
		if($Aseg==1)
		{
			echo "<select name='ASEG'><option value='1,8,9'>ALLIANZ todas las gamas</option></select>";
		}
		else
			echo menu1("ASEG","Select id,nombre from aseguradora ".($Aseg?" where id in ($Aseg) ":""));
	}
	else
	{
		echo "<select name='ASEG'>";
		$Aseguradoras=q("select id,nombre from aseguradora where activo=1");
		while($As=mysql_fetch_object($Aseguradoras))
		{
			echo "<option value='$As->id'>$As->nombre</option>";
		}
		echo "<option value='3,7'>LIBERTY GA y GM</option>
				<option value='1,8,9'>ALLIANZ todas</option>
				<option value='1,2,3,4,5,7,8,9,10'>Todas</option></select>";
	}
	echo "Fecha inicial: ".pinta_FC('forma','FI',date('Y-m-d'))." Fecha Final : ".pinta_FC('forma','FF',date('Y-m-d'))."
			Estado: ".menu1("EST","select id,nombre from estado_siniestro order by id",0,1,"width:100px")."
			Causal: ".menu1("CAU","select id,if(id in (1,2,5,10),nombre,concat('  -- ',nombre,' ( opción antigua) ')) from causal where id not in (4) order by id ",0,1,"width:100px"," alt=' * Opciones anteriores ' title=' * Opciones anteriores ' ")."
			SubCausal: ".menu1("SUBC","select id,concat(t_causal(causal),' - ',nombre) from subcausal order by causal,nombre ",0,1,"width:100px")."
			Excel <input type='checkbox' name='_Exel'>";
	if($_SESSION['User']!=36 && $Aseg!=1) echo "Fitro de SIN CONTACTO para Liberty <input type='checkbox' name='_FIEL'>";
	echo "<input type='button' value='APLICAR' onclick='validar()' style='height:20px;font-weight:bold;'>
		</form>
		<table cellpadding=5><tr><th>Convenciones</th><th>Ayudas</th></tr>
		<tr><td>Convenciones de colores:<table><tr><td valign='top'><table border cellspacing='0'>";
	for($i=0;$i<count($Rango_colores);$i++)
	{
		echo "<tr><td><b>Rango $i</b></td>
		<td><span style='background-color:".$Rango_colores[$i]['color'].";'>-----</span></td>
		<td><td>".$Rango_colores[$i]['tag']."</td></tr>";
		if(($i+1) % 4 ==0) echo "</tr></table></td><td valign='top'><table border cellspacing='0'>";
	}
	echo "</table></td></tr></table></td><td valign='top'><ul><li>El reporte filtra tomando como criterio la fecha de autorización";
	if(inlist($_SESSION['User'],'1,2,3,26'))
		echo "<li>Puede ver el seguimiento de cada siniestro dando un click sobre el número del siniestro.
				 <li>Puede ver el siniestro dando un click sobre la fecha de autorización del siniestro.";
	echo " <li>Si no se selecciona un <i>Estado</i>, se muestran todos los registros.
		 <li>Si no se selecciona una <i>Causal</i>, se muestran todos los registros.
		 </ul></td></table>
		<iframe name='IResultado' id='IResultado' width='100%' border='0' frameborder='no'></iframe>

		</body>";
}

function verseguimiento()
{
	global $id,$Contador;
	$Numero=qo1("select numero from siniestro where id=$id");
	$NT=tu('seguimiento','id');
	html("POSICION: $Contador - Seguimiento  del siniestro: $Numero");
	echo "<script language='javascript'>
	var Marca1=0;
	var Marca2=0;
	function abre_seguimiento(id)
	{
		modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NT&id='+id,0,0,700,700,'seg1');
	}
	function convierte_a_exitoso(id)
	{
		modal('zcalculo_tiempos3.php?Acc=convierte_a_exitoso&id='+id,0,0,10,10,'seg2');
	}
	function marca_fecha(id)
	{
		if(Marca1==0)
		{
				Marca1=id;document.getElementById('marca1').innerHTML='Marca1 :'+id;
		}
		else
		{
			if(Marca2==0)
			{
				Marca2=id;
				document.getElementById('marca2').innerHTML='Marca2 :'+id;
				modal('zcalculo_tiempos3.php?Acc=cambia_fechas&id1='+Marca1+'&id2='+Marca2,0,0,10,10,'cf');
			}
		}
	}
	</script><body><script language='javascript'>centrar(800,500);</script><span id='marca1'></span><span id='marca2'></span>";
	if($Seguimiento=q("select *,t_tipo_seguimiento(tipo) as ntipo from seguimiento where siniestro=$id order by fecha, hora"))
	{
		echo "<table><tr><th>Fecha</th><th>Hora</th><th>Usuario</th><th>Descripcion</th><th>Tipo</th></tr>";
		while($S=mysql_fetch_object($Seguimiento))
		{
			echo "<tr><td ondblclick='marca_fecha($S->id);' nowrap='yes'>$S->fecha</td><td>$S->hora</td><td>$S->usuario</td><td ondblclick='convierte_a_exitoso($S->id);'>$S->descripcion</td><td onclick='abre_seguimiento($S->id);'>$S->ntipo</td></tr>";
		}
		echo "</table>";
	}
}

function convierte_a_exitoso()
{
	global $id;
	q("update seguimiento set tipo=3 where id=$id");
	echo "<script language='javascript'>
			function carga()
			{
			window.close();void(null);
			}
		</script>
		<body onload='carga()'></body>";
}

function reconstruye_seguimiento($LINK=0,$id1=0)
{
	global $id;
	if($id1) $id=$id1;
	if(!$LINK) include('inc/link.php');
	$Sin=qom("select * from siniestro where id=$id",$LINK);
	$Fechai=date('Y-m-d',strtotime($Sin->ingreso));
	$Horai=date('H:i:s',strtotime($Sin->ingreso));
	if(!qo1m("select id from seguimiento where siniestro=$Sin->id and tipo=1",$LINK)) 	mysql_query("insert into seguimiento (siniestro,fecha,hora,descripcion,tipo) values ($Sin->id,'$Fechai','$Horai','Ingresa a la base de datos',1)",$LINK);
	$Arreglo=explode("\n",$Sin->observaciones);
	$Usuario='';
	$Consulto=false;
	$Stop=false;
	$Contacto=qo1m("select id from seguimiento where siniestro=$Sin->id and tipo=3",$LINK);
//	include('inc/link.php');
	for($i=0;$i<count($Arreglo);$i++)
	{
		$Linea=$Arreglo[$i];
		if(strlen($Linea))
		{
			$Fecha=$Fechai;
			$Hora=$Horai;
			$Tipo=7;
			$Descripcion=$Linea;
			if(strpos($Linea,'[') && strpos($Linea,']'))
			{
				$Usuario=substr($Linea,0,strpos($Linea,'['));
				$Fecha=substr($Linea,strpos($Linea,'[')+1,10);
				$Hora=substr($Linea,strpos($Linea,'[')+12,8);
				$Descripcion=substr($Linea,strpos($Linea,']')+1);
				$Descripcion=trim(addslashes(addcslashes($Descripcion,"\24")));
				if(strpos(' '.strtolower($Descripcion),'agenda cita') ||  strpos(' '.strtolower($Descripcion),'asigna cita') )
				{
					if(!$Contacto)
					{
						$query="insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($Sin->id,'$Fecha','$Hora','$Usuario',\"Contacto exitoso\",3)";
						if(!mysql_query($query,$LINK))
						{
							echo "Error en: <br />$query<br /><br />".mysql_error($LINK);
							mysql_close($LINK);
							die();
						}
						$Contacto = true;
					}
					$Tipo=5;
				}
				elseif($Fecha.' '.$Hora==$Sin->contacto_exitoso)
				{
					$Contacto=true;
					$Tipo=3;
				}
				elseif(strpos(' '.strtolower($Descripcion),'remite')) $Tipo=4;
				elseif($Descripcion=='Consultó.') $Tipo=2;
				elseif(strpos(' '.strtolower($Descripcion),'se recibe correo electronico') || strpos(' '.strtolower($Descripcion),'se recibe actualizaci')
					) $Tipo=8;

			}
			if($Descripcion=='Consultó.' && $Consulto) continue;
			if(!qo1m("select id from seguimiento where siniestro=$Sin->id and fecha='$Fecha' and hora='$Hora' ",$LINK))
			{
				$query="insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($Sin->id,'$Fecha','$Hora','$Usuario',\"$Descripcion\",$Tipo)";
				if(!mysql_query($query,$LINK))
				{
					echo "Error en: <br />$query<br /><br />".mysql_error($LINK);
					mysql_close($LINK);
					die();
				}
			}
			if($Descripcion=='Consultó.') $Consulto=true;
		}
	}
//	mysql_close($LINK);
/*	echo "<script language='javascript'>
		function carga()
		{
		window.close();void(null);
		}
	</script>
	<body onload='carga()'></body>";
   */
}

function cambia_fechas()
{
	global $id1,$id2;
	$D2=qo("select fecha,hora from seguimiento where id=$id2");
	q("update seguimiento set fecha='$D2->fecha',hora='$D2->hora' where id=$id1");
	echo "<script language='javascript'>
		function carga()
		{
		window.close();void(null);opener.location.reload();
		}
	</script>
	<body onload='carga()'></body>";
}

function fija_titulo_superior($Titulos,$Caracteristicas_table,$Modo=1)
{
	if($Modo==1)
	{ echo "<div id='_capa_titulo_superior_' style='position:fixed;visibility:hidden'><table id='_Tabla__' $Caracteristicas_table><tr >";
		foreach($Titulos as $Campo=>$Etiqueta) {$Idc=$Campo.'_';echo "<th id='$Idc'>$Etiqueta</th>";}
		echo "</tr></table></div>"; }
	elseif($Modo==3)
	{ echo "<script language='javascript'>fija_ancho('_Tabla_',-1);";
		foreach($Titulos as $Campo=>$Etiqueta) {
			echo "fija_ancho('$Campo',1);";
		}
		echo "document.getElementById('_capa_titulo_superior_').style.visibility='visible';</script>";}
	else
	{ echo "<table id='_Tabla_' $Caracteristicas_table><tr >";
		foreach($Titulos as $Campo=>$Etiqueta) echo "<th id='$Campo'>$Etiqueta</th>";
		echo "</tr>"; }
	return true;
}

function fija_titulo_lateral($Modo=1,$Contenido='',$Caracteristicas='')
{
	if($Modo==1)
		echo "<div id='_capa_titulo_lateral_' style='position:fixed;'><table $Caracteristicas><tbody id='_detalle_capa_titulo_lateral'><tr >$Contenido</tr></tbody></table></div>";

	else
		echo "<script language='javascript'>document.getElementById('_detalle_capa_titulo_lateral').innerHTML+=\"$Contenido\";</script>";
}



?>