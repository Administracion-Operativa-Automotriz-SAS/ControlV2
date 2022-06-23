<?php
// INFORME DE ENTREGAS Y DEVOLUCIONES POR OFICINA ENTRE DOS FECHAS

include('inc/funciones_.php');

if(!$FI || !$FF) { pide_datos(); die(); }

procesa_informe();

function pide_datos()
{
	html('INFORME DE ENTREGAS Y DEVOLUCIONES POR OFICINA');
	echo "<body>
	<form action='zestadistica_citas.php' target='_self' method='POST' name='forma' id='forma'>
		Fecha inicial: ".pinta_FC('forma','FI',date('Y-m-d'))." Fecha final: ".pinta_FC('forma','FF',date('Y-m-d'))." Oficina: ".menu1('OFICINA',"select id,nombre from oficina",0,1)."
		<input type='submit' name='continuar' id='continuar' value=' CONSULTAR '>
	</form></body>";
}

function procesa_informe()
{
	global $FI,$FF,$OFICINA;
	html('INFORME DE ENTREGAS Y DEVOLUCIONES POR OFICINA');
	if($Citas=q("SELECT ofi.nombre as noficina,'ENTREGA' as tipo,a.nombre as naseg,s.numero, c.fecha,c.placa, c.hora, concat(o.nombre,' ',o.apellido) as noperario
		FROM cita_servicio c, siniestro s, operario o,oficina ofi,aseguradora a
		WHERE c.siniestro=s.id and o.id=c.operario_domicilio and c.fecha between '$FI' and '$FF' and c.estado='C' and ofi.id=c.oficina
			and a.id=c.flota ".
		($OFICINA?" and c.oficina=$OFICINA ":"")."
		UNION
		SELECT ofi.nombre as noficina,'DEVOLUCION' as tipo, a.nombre as naseg,s.numero,c.fec_devolucion as fecha,c.placa, 
						c.hora_devol as hora,  concat(o.nombre,' ',o.apellido) as noperario
		FROM cita_servicio c,siniestro s, operario o,oficina ofi,aseguradora a
		WHERE c.siniestro=s.id and o.id=operario_domiciliod and c.fec_devolucion between '$FI' and '$FF' and estadod='C' and ofi.id=c.oficina 
			and a.id=c.flota ".
		($OFICINA?" and c.oficina=$OFICINA ":"")."
		
		ORDER BY noficina,fecha,hora,tipo
	"))
	{
		echo "<table border cellspacing='0'><tr>
				<th>#</th>
				<th>OFICINA</th>
				<th>TIPO</th>
				<th>ASEGURADORA</th>
				<th>SINIESTRO</th>
				<th>FECHA</th>
				<th>HORA</th>
				<th>PLACA</th>
				<th>OPERARIO</th>
				</tr>";
		$Contador=0;
		while($C=mysql_fetch_object($Citas))
		{
			$Contador++;
			echo "<tr><td align='center'>$Contador</td><td>$C->noficina</td><td>$C->tipo</td>
				<td>$C->naseg</td><td>$C->numero</td><td>$C->fecha</td><td>$C->hora</td><td>$C->placa</td>
				<td>$C->noperario</td></tr>";
		}
		echo "</table>";
	}
}
?>