<?php

/**
 * Programa para calcular causales de no adjudicaciones
 *
 * @version $Id$
 * @copyright 2010
 */

include('inc/funciones_.php');
q("drop table if exists tmpi_causales");
q("create table tmpi_causales select date_format(si.fec_autorizacion,'%Y-%m') as mes, si.ciudad,si.causal,count(si.id) as cantidad from
siniestro si where si.estado=1 and si.fec_autorizacion between '$FI' and '$FF' group by mes,ciudad,causal order by mes,ciudad,causal");
q("alter table tmpi_causales add unique index llave (mes,ciudad,causal) ");
q("insert ignore into tmpi_causales select distinct t.mes,t.ciudad,c.id,0 from tmpi_causales t,causal c");
echo "<script language='javascript'>
	alert('Puede procesar el informe');
</script>"

?>