<?php

// PARTICION del informe 114 para mostrar estadistica por placas.
$Numero=count($_SESSION['Placas_ciudad']);
if($Numero)
{
	echo "<tr bgcolor='DAF5FF'><td></td><td colspan=7 ><h5>Numero de placas por Ciudad: ".$IA->ci_nombre.": $Numero</h5>";
	echo "<table border cellspacing='0' cellpadding='5'><tr><td><table border cellspacing='0' cellpadding='5'><tr>
		<th>Placa</th>
		<th>Cantidad</th>
		<th>kilometraje</th>
		<th>Promedio</th>
		</tr>";
	$tcantidad=$tkilometraje=0;
	foreach($_SESSION['Placas_ciudad'] as $placa => $dato)
	{
		$Promedio=round($dato['kilometraje']/$dato['cantidad'],2);
		echo "<tr>
		<td>$placa</td>
		<td align='right'>".$dato['cantidad']."</td>
		<td align='right'>".$dato['kilometraje']."</td>
		<td align='right'>".coma_formatd($Promedio,2)."</td>
		</tr>";
		$tcantidad+=$dato['cantidad'];
		$tkilometraje+=$dato['kilometraje'];
	}
	$Promedio_ponderado=round($tkilometraje/$tcantidad,2);
	echo "</table></td><td valign='top'>
	<table border cellspacing='0' cellpadding='5'><tr><th colspan='2'>PROMEDIO PONDERADO</th></tr>
		<tr><td><b>Ciudad:</b></td><td><b>$IA->ci_nombre</b></td></tr>
		<tr><td><b>Cantidad de servicios:</b></td><td align='right'><b>".coma_format($tcantidad)."</b></td></tr>
		<tr><td><b>Cantidad de kilometros:</b></td><td align='right'><b>".coma_format($tkilometraje)."</b></td></tr>
		<tr><td><b>Promedio Ponderado:</b></td><td align='right'><b>".coma_formatd($Promedio_ponderado,2)."</b></td></tr>
	</table>
	<br>";
	$Eficiencia_total=round($tcantidad/$Numero,2);
	$Numero_meses=count($_SESSION['Meses']);
	$Eficiencia_mensual=$Eficiencia_total/$Numero_meses;
	echo "<table><tr><th colspan=2>Eficiencia</th></tr>
				<tr><td>Total</td><td align='center'>".coma_formatd($Eficiencia_total,2)."</td></tr>
				<tr><td>Numero de meses</td><td align='center'>$Numero_meses</td></tr>
				<tr><td>Mensual</td><td align='center'>".coma_formatd($Eficiencia_mensual,2)."</td></tr>
			</table>
	</td></tr></table>";
	echo "</td></tr>";
}
?>