<?
include_once('inc/funciones_.php');
set_time_limit(0);
html();
echo "<body><h3>CAMBIO DE ESTADOS version 1.09 </h3><br><br>";
require('inc/link.php');
echo "<br>Limpiando placas";
if(!$O) mysql_query("update siniestro set placa=ltrim(placa)",$LINK);
$Ahora=date('Y-m-d H:i:s');

/// CAMBIO DE ESTADO A NO ADJUDICADO DE ACUERDO AL NUMERO DE DIAS POR ASEGURADORA
//  Tiene en cuenta si los siniestros tienen compromisos que no se hayan cumplido y que sean superiores a la fecha actual.
echo "<br>Actualizando siniestros por vencimiento en dias";
if(!$O) mysql_query("UPDATE siniestro s,aseguradora a SET s.estado=1,s.observaciones=concat(s.observaciones , ' [$Ahora] CAMBIO DE ESTADO AUTOMATICO 60 DIAS'),s.causal='15' 
							WHERE s.aseguradora=a.id and s.estado=5 and s.ingreso < adddate(curdate(),-a.dias_noadjudicacion)  
									and s.id not in (SELECT s.id FROM siniestro s,seguimiento c WHERE s.id=c.siniestro and c.tipo=16 and c.cumplido=0 and c.fecha_compromiso>curdate())",$LINK);

									/// CAMBIO DE ESTADO A NO ADJUDICADO 30 DIAS SOLO PARA LAS DEMAS ASEGURADORAS
// if(!$O) mysql_query("update siniestro set estado=1,observaciones=concat(observaciones , ' [$Ahora] CAMBIO DE ESTADO AUTOMATICO 60 DIAS'),causal='15' 	
							// WHERE estado=5 and ingreso < adddate(curdate(),-30) and aseguradora not in (4,10) 
								// and id not in (select  s.id from siniestro s,seguimiento c  where s.id=c.siniestro and c.tipo=16 and c.cumplido=0 and c.fecha_compromiso>curdate())",$LINK);
// asigna las ciudades originales cuando estas quedan en blanco

if(!$O) echo "<br>actualizando ciudades originales ";
if(!$O) mysql_query("update siniestro set ciudad_original=ciudad where ciudad_original=''",$LINK);
//$D180=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-180)));
//echo "<br>Pasando seguimientos de 6 meses";
//mysql_query("insert ignore into seguimiento_bk select * from seguimiento where fecha<'$D180' ",$LINK);
//echo "<br>Limpiando placas";
//mysql_query("delete from seguimiento where fecha<'$D180' ",$LINK);
//mysql_query("insert ignore into app_bitacora_bk select * from app_bitacora where concat(ano,'-',mes,'-',dia) < '$D180' ",$LINK);
///mysql_query("delete from app_bitacora where concat(ano,'-',mes,'-',dia) < '$D180' ",$LINK);

if(!$O) echo "<br>Optimizando siniestros, seguimientos y bitacora";
if(!$O) mysql_query("optimize table siniestro,seguimiento,app_bitacora",$LINK);
$Hoy=date('Y-m-d');
$Ayer=date('Y-m-d',strtotime(aumentadias($Hoy,-1)));
echo "<br>Creando Temporal de ubicaciones";
mysql_query("drop table if exists tmpi_ayer",$LINK);
mysql_query("create table tmpi_ayer select * from $base.ubicacion where  fecha_inicial<='$Hoy' and fecha_final >='$Ayer' ",$LINK);
echo "<br>Indexando Temporal de ubicaciones";
mysql_query("alter table tmpi_ayer add index llave (vehiculo,fecha_inicial,fecha_final)",$LINK);
echo "<br>Buscando Vehiculos";
if($Vehiculos=mysql_query("select * from vehiculo ",$LINK))
{
	while($V=mysql_fetch_object($Vehiculos))
	{
		echo "<br />$V->placa";
		if($Ultimo_estado=mysql_query("select * from tmpi_ayer where vehiculo=$V->id and fecha_inicial<='$Hoy' and fecha_final >='$Ayer' order by fecha_final desc,fecha_inicial desc, id desc limit 1",$LINK))
		{
			if($UE=mysql_fetch_object($Ultimo_estado))
			{
				if($UE->fecha_final<$Hoy)
				{
					if($UE->estado!=7) 
					{echo "<br>Ampliando fecha final";mysql_query("update ubicacion set fecha_final='$Hoy' where id=$UE->id",$LINK);}	
					else
					{
						$Flota=($V->flota_distinta==1?$UE->flota:6);
						$FechaI=aumentadias($UE->fecha_final,1);
						echo "<br>Insertando ubicación nueva";
						mysql_query("insert into ubicacion (oficina,flota,vehiculo,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones,estado) values
							($UE->oficina,$Flota,$V->id,'$UE->fecha_final','$Hoy',$UE->odometro_final,$UE->odometro_final,0,'Creación de estado automática',2)",$LINK);
					}
				}
			}
		}
	}
}
if(!$O)
{
	mysql_query("update siniestro set valida_recaudo=1 where valida_recaudo=0 and aseguradora=1",$LINK);
	echo "<br>Actualizando validaciones de Royal Basico";
	mysql_query("update siniestro s, aoacol_recaudoroyal.placa r set s.valida_recaudo=1 where s.valida_recaudo=0 and s.placa=r.placa and s.aseguradora=2",$LINK);
	echo "<br>Actualizando validaciones de Royal BMW";
	mysql_query("update siniestro s, aoacol_recaudoroyal.placa r set s.valida_recaudo=1 where s.valida_recaudo=0 and s.placa=r.placa and s.aseguradora=5",$LINK);
	echo "<br>Actualizando validaciones de Royal Liberty Gama Alta";
	mysql_query("update siniestro s, aoacol_recaudoliberty.placa r set s.valida_recaudo=1 where s.valida_recaudo=0 and s.placa=r.placa and s.aseguradora=3",$LINK);
	echo "<br>Actualizando validaciones de Royal Mapfre";
	mysql_query("update siniestro s, aoacol_recmapfre.historico_pago r set s.valida_recaudo=1 where s.valida_recaudo=0 and s.placa=r.poliza and s.aseguradora=4",$LINK);
	echo "<br>Actualizando validaciones de Royal Liberty Gama Media";
	mysql_query("update siniestro s, aoacol_relibertygm.historico_pago r set s.valida_recaudo=1 where s.valida_recaudo=0 and s.placa=r.poliza and s.aseguradora=7",$LINK);
}
mysql_close($LINK);


?>