<?php
/*  PROGRAMA PARA MONITOREAR POR CIUDAD LOS VEHICULOS, POR LOGO/SIN LOGO Y POR ESTADO Y POR ASEGURADORA
 * 
 * 	Programa gerencial para identificar rápidamente por ciudad donde están y en que estado esta cada vehículo
 * 
 */


include('inc/sess.php');
$USUARIO=$_SESSION['User'];
include('inc/funciones_.php');

if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}
html(TITULO_APLICACION.' - MONITOR - '.$_SESSION['Nombre']);

echo "
<script language='javascript'>
	function recarga()
	{
		location.reload();
	}
	Recargar=setTimeout(recarga,20000);  
</script>
<body onload='centrar()'>";
$HOY=date('Y-m-d');
require('inc/link.php');
//VEHICULOS SIN LOGO
$D=array();
$M=array();
if($Carros=mysql_query("select distinct vehiculo,t_estado_vehiculo(estado) as estado,t_oficina(oficina) as oficina,marca_vehiculo.nombre as marca
				from ubicacion,vehiculo,linea_vehiculo,marca_vehiculo where ubicacion.vehiculo=vehiculo.id and '$HOY' between fecha_inicial and fecha_final and
				vehiculo.linea=linea_vehiculo.id and linea_vehiculo.marca=marca_vehiculo.id and 
				(vehiculo.inactivo_desde='0000-00-00' or vehiculo.inactivo_desde>'$HOY') order by oficina,vehiculo,fecha_final desc,fecha_inicial desc ",$LINK))
{
	$Ultc=0;
	while($C=mysql_fetch_object($Carros))
	{
		if($C->vehiculo!=$Ultc)
		{
				$D[$C->oficina][$C->estado]++;
				$M[$C->marca]++;
				$Ultc=$C->vehiculo;
		}
	}
}
if($Siniestros=mysql_query("select oficina.nombre as oficina, sum(if(estado=5,1,0)) as pendiente, sum(if(estado=3,1,0)) as adjudicado from siniestro , oficina
	where siniestro.estado in (3,5) and siniestro.ciudad=oficina.ciudad group by oficina.nombre order by oficina.nombre ",$LINK))
{
	while($S=mysql_fetch_object($Siniestros))
	{
		$D[$S->oficina]['Pendiente']=$S->pendiente;$D[$S->oficina]['Adjudicado']=$S->adjudicado;
	}
}

//VEHICULOS DE COLSEGUROS
$D1=array();
if($Carros=mysql_query("select distinct vehiculo,t_estado_vehiculo(estado) as estado,t_oficina(oficina) as oficina,marca_vehiculo.nombre as marca
				from aoacol_aoacolombia.ubicacion,aoacol_aoacolombia.vehiculo,aoacol_aoacolombia.linea_vehiculo,aoacol_aoacolombia.marca_vehiculo
				where ubicacion.vehiculo=vehiculo.id and '$HOY' between fecha_inicial and fecha_final and
				vehiculo.linea=linea_vehiculo.id and linea_vehiculo.marca=marca_vehiculo.id and 
				(vehiculo.inactivo_desde='0000-00-00' or vehiculo.inactivo_desde>'$HOY') and flota_aoa=0 order by oficina,vehiculo,fecha_final desc,fecha_inicial desc ",$LINK))
{
	$Ultc=0;
	while($C=mysql_fetch_object($Carros))
	{
		if($C->vehiculo!=$Ultc)
		{
				$D1[$C->oficina][$C->estado]++;
				$M[$C->marca]++;
				$Ultc=$C->vehiculo;
		}
	}
}

if($Siniestros=mysql_query("select oficina.nombre as oficina, sum(if(estado=5,1,0)) as pendiente, sum(if(estado=3,1,0)) as adjudicado,
	sum(if(estado=3,if(numero_voucher!='' or numero_voucher1!='' or numero_voucher2!='',1,0),0)) as voucher 
	from aoacol_aoacolombia.siniestro , aoacol_aoacolombia.oficina
	where siniestro.estado in (3,5) and siniestro.ciudad=oficina.ciudad group by oficina.nombre order by oficina.nombre ",$LINK))
{
	while($S=mysql_fetch_object($Siniestros))
	{
		$D1[$S->oficina]['Pendiente']=$S->pendiente;$D1[$S->oficina]['Adjudicado']=$S->adjudicado;$D1[$S->oficina]['Voucher']=$S->voucher;
	}
}

//VEHICULOS DE ROYAL BASICO
$D2=array();
if($Carros=mysql_query("select distinct vehiculo,t_estado_vehiculo(estado) as estado,t_oficina(oficina) as oficina,marca_vehiculo.nombre as marca
				from aoacol_aoacolombia2.ubicacion,aoacol_aoacolombia2.vehiculo ,aoacol_aoacolombia2.linea_vehiculo,aoacol_aoacolombia2.marca_vehiculo
				where ubicacion.vehiculo=vehiculo.id and '$HOY' between fecha_inicial and fecha_final and 
				vehiculo.linea=linea_vehiculo.id and linea_vehiculo.marca=marca_vehiculo.id and
				(vehiculo.inactivo_desde='0000-00-00' or vehiculo.inactivo_desde>'$HOY') and flota_aoa=0 order by oficina,vehiculo,fecha_final desc,fecha_inicial desc ",$LINK))
{
	$Ultc=0;
	while($C=mysql_fetch_object($Carros))
	{
		if($C->vehiculo!=$Ultc)
		{
				$D2[$C->oficina][$C->estado]++;
				$M[$C->marca]++;
				$Ultc=$C->vehiculo;
		}
	}
}

if($Siniestros=mysql_query("select oficina.nombre as oficina, sum(if(estado=5,1,0)) as pendiente, sum(if(estado=3,1,0)) as adjudicado,
	sum(if(estado=3,if(numero_voucher!='' or numero_voucher1!='' or numero_voucher2!='',1,0),0)) as voucher 
	from aoacol_aoacolombia2.siniestro , aoacol_aoacolombia2.oficina
	where siniestro.estado in (3,5) and siniestro.ciudad=oficina.ciudad group by oficina.nombre order by oficina.nombre ",$LINK))
{
	while($S=mysql_fetch_object($Siniestros))
	{
		$D2[$S->oficina]['Pendiente']=$S->pendiente;$D2[$S->oficina]['Adjudicado']=$S->adjudicado;$D2[$S->oficina]['Voucher']=$S->voucher;
	}
}

//VEHICULOS DE ROYAL BMW
$D3=array();
if($Carros=mysql_query("select distinct vehiculo,t_estado_vehiculo(estado) as estado,t_oficina(oficina) as oficina,marca_vehiculo.nombre as marca
				from aoacol_aoacolombia3.ubicacion,aoacol_aoacolombia3.vehiculo ,aoacol_aoacolombia3.linea_vehiculo,aoacol_aoacolombia3.marca_vehiculo
				where ubicacion.vehiculo=vehiculo.id and '$HOY' between fecha_inicial and fecha_final and 
				vehiculo.linea=linea_vehiculo.id and linea_vehiculo.marca=marca_vehiculo.id and
				(vehiculo.inactivo_desde='0000-00-00' or vehiculo.inactivo_desde>'$HOY') and flota_aoa=0 order by oficina,vehiculo,fecha_final desc,fecha_inicial desc ",$LINK))
{
	$Ultc=0;
	while($C=mysql_fetch_object($Carros))
	{
		if($C->vehiculo!=$Ultc)
		{
				$D3[$C->oficina][$C->estado]++;
				$M[$C->marca]++;
				$Ultc=$C->vehiculo;
		}
	}
}
if($Siniestros=mysql_query("select oficina.nombre as oficina, sum(if(estado=5,1,0)) as pendiente, sum(if(estado=3,1,0)) as adjudicado,
	sum(if(estado=3,if(numero_voucher!='' or numero_voucher1!='' or numero_voucher2!='',1,0),0)) as voucher 
	from aoacol_aoacolombia3.siniestro , aoacol_aoacolombia3.oficina
	where siniestro.estado in (3,5) and siniestro.ciudad=oficina.ciudad group by oficina.nombre order by oficina.nombre ",$LINK))
{
	while($S=mysql_fetch_object($Siniestros))
	{
		$D3[$S->oficina]['Pendiente']=$S->pendiente;$D3[$S->oficina]['Adjudicado']=$S->adjudicado;$D3[$S->oficina]['Voucher']=$S->voucher;
	}
}

//VEHICULOS DE LIBERTY
$D4=array();
if($Carros=mysql_query("select distinct vehiculo,t_estado_vehiculo(estado) as estado,t_oficina(oficina) as oficina,marca_vehiculo.nombre as marca
				from aoacol_libertyseguros.ubicacion,aoacol_libertyseguros.vehiculo,aoacol_libertyseguros.linea_vehiculo,aoacol_libertyseguros.marca_vehiculo
				where ubicacion.vehiculo=vehiculo.id and '$HOY' between fecha_inicial and fecha_final and 
				vehiculo.linea=linea_vehiculo.id and linea_vehiculo.marca=marca_vehiculo.id and
				(vehiculo.inactivo_desde='0000-00-00' or vehiculo.inactivo_desde>'$HOY') and flota_aoa=0 order by oficina,vehiculo,fecha_final desc,fecha_inicial desc ",$LINK))
{
	$Ultc=0;
	while($C=mysql_fetch_object($Carros))
	{
		if($C->vehiculo!=$Ultc)
		{
				$D4[$C->oficina][$C->estado]++;
				$M[$C->marca]++;
				$Ultc=$C->vehiculo;
		}
	}
}
if($Siniestros=mysql_query("select oficina.nombre as oficina, sum(if(estado=5,1,0)) as pendiente, sum(if(estado=3,1,0)) as adjudicado,
	sum(if(estado=3,if(numero_voucher!='' or numero_voucher1!='' or numero_voucher2!='',1,0),0)) as voucher 
	from aoacol_libertyseguros.siniestro , aoacol_libertyseguros.oficina
	where siniestro.estado in (3,5) and siniestro.ciudad=oficina.ciudad group by oficina.nombre order by oficina.nombre ",$LINK))
{
	while($S=mysql_fetch_object($Siniestros))
	{
		$D4[$S->oficina]['Pendiente']=$S->pendiente;$D4[$S->oficina]['Adjudicado']=$S->adjudicado;$D4[$S->oficina]['Voucher']=$S->voucher;
	}
}

//VEHICULOS DE MAPFRE
$D5=array();
if($Carros=mysql_query("select distinct vehiculo,t_estado_vehiculo(estado) as estado,t_oficina(oficina) as oficina,marca_vehiculo.nombre as marca
				from aoacol_mapfre.ubicacion,aoacol_mapfre.vehiculo,aoacol_mapfre.linea_vehiculo,aoacol_mapfre.marca_vehiculo
				where ubicacion.vehiculo=vehiculo.id and '$HOY' between fecha_inicial and fecha_final and 
				vehiculo.linea=linea_vehiculo.id and linea_vehiculo.marca=marca_vehiculo.id and
				(vehiculo.inactivo_desde='0000-00-00' or vehiculo.inactivo_desde>'$HOY') and flota_aoa=0 order by oficina,vehiculo,fecha_final desc,fecha_inicial desc ",$LINK))
{
	$Ultc=0;
	while($C=mysql_fetch_object($Carros))
	{
		if($C->vehiculo!=$Ultc)
		{
				$D5[$C->oficina][$C->estado]++;
				$M[$C->marca]++;
				$Ultc=$C->vehiculo;
		}
	}
}

if($Siniestros=mysql_query("select oficina.nombre as oficina, sum(if(estado=5,1,0)) as pendiente, sum(if(estado=3,1,0)) as adjudicado,
	sum(if(estado=3,if(numero_voucher!='' or numero_voucher1!='' or numero_voucher2!='',1,0),0)) as voucher 
	from aoacol_mapfre.siniestro , aoacol_mapfre.oficina
	where siniestro.estado in (3,5) and siniestro.ciudad=oficina.ciudad group by oficina.nombre order by oficina.nombre ",$LINK))
{
	while($S=mysql_fetch_object($Siniestros))
	{
		$D5[$S->oficina]['Pendiente']=$S->pendiente;$D5[$S->oficina]['Adjudicado']=$S->adjudicado;$D5[$S->oficina]['Voucher']=$S->voucher;
	}
}

mysql_close($LINK);

echo "<table border cellspacing=0 style='empty-cells:show'>
<h3 align='center'><b>SISTEMA DE MONITOREO DE OPERACIONES DE AOA COLOMBIA S.A.</b></h3>";
$TT_serv=$TT_parq=$TT_fuera=$TT_mant=$TT_total=$TT_pend=$TT_adj=0;

Pinta_estado($D,'FLOTA AOA - SIN LOGO');
echo "<br>";
Pinta_estado($D1,'COLSEGUROS');
Pinta_estado($D2,'ROYAL - BASICO');
Pinta_estado($D3,'ROYAL - BMW');
Pinta_estado($D4,'LIBERTY');
Pinta_estado($D5,'MAPFRE');

echo "<table border cellspacing=0 style='empty-cells:show'>";
echo "<tr bgcolor='eeeeff'><th width=150>TOTALES</th>
			<td align='center' width=50>".$TT_serv."</td>
			<td align='center' width=100>".$TT_parq."</td>
			<td align='center' width=100>".$TT_fuera."</td>
			<td align='center' width=100>".$TT_mant."</td>
			<td align='center' width=50>$TT_total</td>
			
			</tr></table>";
foreach($M as $marca => $cantidad)
echo "$marca : $cantidad &nbsp;&nbsp;";
echo "</body><html>";


function Pinta_estado($D,$Nombre)
{
	global $TT_serv,$TT_parq,$TT_fuera,$TT_mant,$TT_total,$TT_pend,$TT_adj;
	$T_servicio=$T_parqueadero=$T_fuera=$T_mantenimiento=$T_total=$T_pend=$T_adj=0;
	echo "<table border cellspacing=0 style='empty-cells:show' bgcolor='eeeeee'>
		<tr bgcolor='ddddee'><th width=150><b>$Nombre</b></th>
			<td align='center'><b>Servicio</b></td><td align='center'><b>Parqueadero</b></td><td align='center'><b>Fuera/Servicio</b></td><td align='center'><b>Mantenimiento</b></td>
			<td align='center'><b>Total</b></td><td align='center'><b>Pendientes</b></td><td align='center'><b>Adjudicados</b></td><td align='center'><b>Voucher</b></td>
			</tr>";
	foreach($D as $ciudad=>$estados)
	{
		$Servicio=$D[$ciudad]['SERVICIO'];
		$Parqueadero=$D[$ciudad]['PARQUEADERO']+$D[$ciudad]['SERVICIO CONCLUIDO'];
		$Fuera=$D[$ciudad]['FUERA DE SERVICIO'];
		$Mantenimiento=$D[$ciudad]['EN MANTENIMIENTO']+$D[$ciudad]['EN TRANSITO'];
		$Pendiente=$D[$ciudad]['Pendiente'];
		$Adjudicado=$D[$ciudad]['Adjudicado'];
		$Voucher=$D[$ciudad]['Voucher'];
		echo "<tr><td>$ciudad</td>
			<td align='center' width=50>".($Servicio?$Servicio:'')."</td>
			<td align='center' width=100>".($Parqueadero?$Parqueadero:'')."</td>
			<td align='center' width=100>".($Fuera?$Fuera:'')."</td>
			<td align='center' width=50>".($Mantenimiento?$Mantenimiento:'')."</td>";
		$Total=$Servicio+$Parqueadero+$Fuera+$Mantenimiento;
		echo "<td align='center'>$Total</td>
			<td align='center' width='80'>".($Pendiente?$Pendiente:'')."</td>
			<td align='center' width='80'>".($Adjudicado?$Adjudicado:'')."</td>
			<td align='center' width='80'>".($Voucher?$Voucher:'')."</td>
			";
		
		echo "</tr>";
		$T_servicio+=$Servicio;
		$T_parqueadero+=$Parqueadero;
		$T_fuera+=$Fuera;
		$T_mantenimiento+=$Mantenimiento;
		$T_total+=$Total;
		$T_pend+=$Pendiente;
		$T_adj+=$Adjudicado;
	}
	echo "<tr bgcolor='ddeedd'><td>Totales</td>
			<td align='center' width=50>".$T_servicio."</td>
			<td align='center' width=100>".$T_parqueadero."</td>
			<td align='center' width=100>".$T_fuera."</td>
			<td align='center' width=100>".$T_mantenimiento."</td>
			<td align='center' width=50>$T_total</td>
			<td align='center' width=80>$T_pend</td>
			<td align='center' width=80>$T_adj</td>
			</tr>
		</table>";
	$TT_serv+=$T_servicio;
	$TT_parq+=$T_parqueadero;
	$TT_fuera+=$T_fuera;
	$TT_mant+=$T_mantenimiento;
	$TT_total+=$T_total;
	$TT_pend+=$T_pend;
	$TT_adj+=$T_adj;
}

function consultar()
{
	global $Tipo,$Flota,$Ciudad,$Fecha;
	if($Tipo='Servicio')
	{
		
	}
}

function bflota($Flota)
{
	switch($Flota)
	{
		case 1: return 'aoacol_aoacars'; // flota sin logo
		case 2: return 'aoacol_aoacolombia'; // Colseguros
		case 3: return 'aoacol_aoacolombia2'; // Royal Basico
		case 4: return 'aoacol_aoacolombia3'; // Royal bmw
		case 5: return 'aoacol_libertyseguros'; // Liberty
		case 6: return 'aoacol_mapfre'; // Mapfre
	}
}

?>