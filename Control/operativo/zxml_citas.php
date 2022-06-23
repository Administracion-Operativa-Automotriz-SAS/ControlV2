<?php

/**
 *  GENERADOR DE XML PARA LAS CITAS DEL DIA
 *
 * @version $Id$
 * @copyright 2011
 */
include('inc/funciones_.php');
$Hoy=date('Y-m-d');
Header("Content-type: text/xml");
Header('Cache-Control: no-cache');
Header('Pragma: no-cache');
$Filtro='';
if($C) $Filtro=" and c.oficina=$C ";

echo "<?xml version=\"1.0\"?>
";
echo "
	<citas>";
include('inc/link.php');
if($Citase=mysql_query("select c.*,s.id as idsin, s.numero
					FROM cita_servicio c,siniestro s,sin_autor au
					WHERE c.siniestro=s.id and c.fecha='$Hoy' and c.estado='P' and c.siniestro=au.siniestro and au.estado='A'
					$Filtro
					ORDER BY c.hora,c.placa",$LINK))
{
	$Contador=1;
	while($C=mysql_fetch_object($Citase))
	{
		$Ultimo_estado=qom("select u.estado,t_estado_vehiculo(u.estado) as ne from ubicacion u,vehiculo v where
					v.id=u.vehiculo and v.placa='$C->placa' and u.fecha_final='$Hoy' order by u.id desc limit 1",$LINK);

		$Fec_inicial = $C->fecha;
		$Fec_final = date('Y-m-d', strtotime(aumentadias($Fec_inicial,$C->dias_servicio)));
		$Fec_futuros1 = date('Y-m-d', strtotime(aumentadias($Fec_inicial, 1)));
		$Futuro = '';
		if ($Futuros = mysql_query("select t_estado_vehiculo(u.estado) as ne from ubicacion u ,vehiculo v where
												v.id=u.vehiculo and v.placa='$C->placa' and u.estado!=1 and u.estado!=96 and u.fecha_final>'$Hoy' and
												(		(u.fecha_inicial between '$Fec_futuros1' and '$Fec_final')    or  	( u.fecha_final between '$Fec_futuros1' and '$Fec_final') 		)   ",$LINK))
		{ while ($F = mysql_fetch_object($Futuros)) $Futuro .= ($Futuro?",":"").$F->ne; }
		if($Ultimo_estado)
		{if($Ultimo_estado->estado!=2 /* parqueadero */ && $Ultimo_estado->estado!=7 /*servicio concluido */  && $Ultimo_estado->estado!=1 && $Ultimo_estado->estado!=96 /*Domicilio*/)
			$Futuro.=($Futuro?",":"").$Ultimo_estado->ne;}
		$Sin_conc = qo1m("select u.id from ubicacion u,vehiculo v where v.id=u.vehiculo and v.placa='$C->placa' and u.fecha_final <= '$Fec_inicial' and u.estado=1",$LINK);
//		if($Autorizacion = qo1m("select id from sin_autor where siniestro='$C->siniestro' and estado='A' and num_autorizacion!='' ",$LINK))
			$Autorizacion=1;
//		else $Autorizacion=0;
		if($Autorizacion && !$Futuro && !$Sin_conc)
		{
			echo "
			<cita>
			<id_cita>$C->id</id_cita>
			<placa>$C->placa</placa>
			<id_siniestro>$C->idsin</id_siniestro>
			<estado>$C->estado</estado>
			<numero_siniestro>$C->numero [ ENTREGA ".l($C->hora,5)." ] ".($C->dir_domicilio?"-DOMICILIO-":"");
			echo "</numero_siniestro>
			<nombre_autorizado>".quitatildes($C->conductor)."</nombre_autorizado>
			</cita>";
			$Contador++;
		}

	}
}
echo "<cita>
<id_cita>0</id_cita>
<placa> </placa>
<id_siniestro>0</id_siniestro>
<estado>D</estado>
<numero_siniestro> </numero_siniestro>
<nombre_autorizado>------------- DEVOLUCIONES ------------</nombre_autorizado>
</cita>";



if($Citasd=mysql_query("select c.id,c.placa,s.id as idsin, s.numero,c.estado,c.conductor,c.hora
					FROM cita_servicio c,siniestro s
					WHERE c.siniestro=s.id and c.fec_devolucion='$Hoy' and c.estadod='P' $Filtro
					ORDER BY c.hora,c.placa",$LINK))
{
	$Contador=1;
	while($C=mysql_fetch_object($Citasd))
	{

		echo "
			<cita>
				<id_cita>$C->id</id_cita>
				<placa>$C->placa</placa>
				<id_siniestro>$C->idsin</id_siniestro>
				<estado>$C->estado</estado>
				<numero_siniestro>$C->numero [ DEVOLUCION ".l($C->hora,5)." ]</numero_siniestro>
				<nombre_autorizado>".quitatildes($C->conductor)."</nombre_autorizado>
			</cita>";
		$Contador++;
	}
}

echo "
	</citas>";
?>