<?php
## calcula diario preparador para el reporte numero 1
include('inc/sess.php');
include('inc/funciones_.php');
html();
q("drop table if exists tmpi_diario");
#q("create table tmpi_diario
#	SELECT if(es.nombre='SERVICIO CONCLUIDO', if(ub.fecha_final='$FECHA','PARQUEADERO','SERVICIO'),es.nombre) as nombre,
#	vh.placa as vehiculo
#	FROM estado_vehiculo es, ubicacion ub ,vehiculo vh
#	WHERE es.id=ub.estado and vh.id=ub.vehiculo and '$FECHA' between ub.fecha_inicial and ub.fecha_final ".($OFICINA?" and ub.oficina='$OFICINA' ":"")."
#	GROUP BY vehiculo
#	ORDER BY vehiculo,ub.fecha_final desc,ub.fecha_inicial desc");

q("create table tmpi_diario select vh.placa as vehiculo,('                                                 ') as nombre
	FROM estado_vehiculo es, ubicacion ub ,vehiculo vh
	WHERE es.id=ub.estado and vh.id=ub.vehiculo and '$FECHA' between ub.fecha_inicial and ub.fecha_final ".($OFICINA?" and ub.oficina='$OFICINA' ":"")."
	GROUP BY vehiculo
	ORDER BY vehiculo");
$DD=q("select * from tmpi_diario");
require('inc/link.php');
while($D=mysql_fetch_object($DD))
{
	if($Estado=mysql_query("SELECT if(es.nombre='SERVICIO CONCLUIDO', if(ub.fecha_final='$FECHA','PARQUEADERO','SERVICIO'),es.nombre) as nombre
		FROM estado_vehiculo es, ubicacion ub ,vehiculo vh
		WHERE es.id=ub.estado and vh.id=ub.vehiculo  and vh.placa='$D->vehiculo' and '$FECHA' between ub.fecha_inicial and ub.fecha_final
		ORDER BY ub.fecha_final desc,ub.fecha_inicial desc limit 1",$LINK))
	{
		if($Estado=mysql_fetch_object($Estado))
		{
			mysql_query("update tmpi_diario set nombre='$Estado->nombre' where vehiculo='$D->vehiculo'",$LINK);
		}
	}
}
mysql_close($LINK);
echo "<body onload=\"window.open('reporte.php?ID=8&OFICINA=$OFICINA','_self');\"></body>";
?>
