<?php

/**
 *  Calculo de límites de kilometrajes por vehiculos por mes entre dos fechas
 *
 * @version $Id$
 * @copyright 2010
 */

include('inc/funciones_.php');
html();
q("drop table if exists tmpi_minveh");
q("create table tmpi_minveh select v.id,v.placa,date_format(u.fecha_final,'%Y-%m') as periodo,min(u.odometro_inicial) as odoi
FROM vehiculo v,ubicacion u
WHERE v.id=u.vehiculo and u.fecha_final between '$FI' and  '$FF' and u.odometro_inicial>0
GROUP BY v.id,periodo
ORDER BY v.id,u.fecha_inicial,u.fecha_final");

q("drop table if exists tmpi_maxveh");
q("create table tmpi_maxveh select v.id,v.placa,date_format(u.fecha_final,'%Y-%m') as periodo,max(u.odometro_final) as odof
FROM vehiculo v,ubicacion u
WHERE v.id=u.vehiculo and u.fecha_final between '$FI' and '$FF' and u.odometro_final>0
GROUP BY v.id,periodo
ORDER BY v.id,u.fecha_inicial,u.fecha_final");


echo "<script language='javascript'>
	function carga()
	{
		parent.document.forma.Procesar.style.visibility='visible';
	}
</script>
<body onload='carga()'></body>"
?>