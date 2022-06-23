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
if($P) $Filtro.=" and c.placa like '%$P%' ";
if($F) $Hoy=$F;
if($C) $Filtro=" and c.oficina=$C ";

echo "<?xml version=\"1.0\"?>
";
echo "
	<citas>";
include('inc/link.php');
if($T=='E' || !$T)
{
	if($Citase=mysql_query("select c.*,s.id as idsin, s.numero,kilometraje(v.id) as kilom
					FROM cita_servicio c,siniestro s,sin_autor au,vehiculo v
					WHERE c.siniestro=s.id and c.fecha='$Hoy' and c.estado='P' and c.siniestro=au.siniestro and au.estado='A' and v.placa=c.placa 
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
			if(!$Futuro && !$Sin_conc)
			{
				echo "
				<cita>
				<tipo_cita>Entrega</tipo_cita>
				<id_cita>$C->id</id_cita>
				<placa>$C->placa</placa>
				<kilometraje>$C->kilom</kilometraje>
				<fecha>$C->fecha</fecha>
				<hora>$C->hora</hora>
				<id_siniestro>$C->idsin</id_siniestro>
				<estado>$C->estado</estado>
				<numero_siniestro>$C->numero</numero_siniestro>
				<domicilio>".quitatildes($C->dir_domicilio)."</domicilio>
				<nombre_autorizado>".ucwords(strtolower(quitatildes($C->conductor))).($C->dir_domicilio?' .:. DOMICILIO .:. ':'')."</nombre_autorizado>
				</cita>";
				$Contador++;
			}
		}
	}
}

if($T=='D' || !$T)
{
	if($Citasd=mysql_query("select c.*,s.id as idsin, s.numero,kilometraje(v.id) as kilom
						FROM cita_servicio c,siniestro s,vehiculo v
						WHERE c.siniestro=s.id and c.fec_devolucion='$Hoy' and v.placa=c.placa and c.estadod='P' $Filtro
						ORDER BY c.hora,c.placa",$LINK))
	{
		$Contador=1;
		while($C=mysql_fetch_object($Citasd))
		{

			echo "
				<cita>
				<tipo_cita>Devolucion</tipo_cita>
				<id_cita>$C->id</id_cita>
				<placa>$C->placa</placa>
				<kilometraje>$C->kilom</kilometraje>
				<fecha>$C->fecha</fecha>
				<hora>$C->hora</hora>
				<id_siniestro>$C->idsin</id_siniestro>
				<estado>$C->estadod</estado>
				<numero_siniestro>$C->numero</numero_siniestro>
				<domicilio>".quitatildes($C->dir_domiciliod)."</domicilio>
				<nombre_autorizado>".ucwords(strtolower(quitatildes($C->conductor))).($C->dir_domicilio?' .:. DOMICILIO .:. ':'')."</nombre_autorizado>
				</cita>";
			$Contador++;
		}
	}
}
echo "
	</citas>";
?>