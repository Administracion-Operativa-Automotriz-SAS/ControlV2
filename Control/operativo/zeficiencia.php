<?php
/* INFORME DE EFICIENCIA DE SERVICIOS POR CIUDAD POR PERIODO
 * 
 *  Este informe debe organizar por oficina por placas, en forma matricial el acumulado de serviicos por cada vehiculo en cada periodo
 *  Y obtener los promedios de los vehiculos que tuvieron servicio, escluyendo del este cálculo los que no tuvieron servicio.
 */

include('inc/funciones_.php');
sesion();
if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}
html();
echo "
<script language='javascript'>
	function carga()
	{
		centrar();
	}
</script>
<body onload='carga()'>
			<center>
			<FORM ACTION='zeficiencia.php' TARGET='_self' METHOD='post' name='forma' id='forma'>
			<table><tr><td>
				<INPUT TYPE='hidden' NAME='Acc' VALUE='ejecutar'>
				<h3 align='center'><img src='img/LogoAOA.jpg' border=0 height='100'><br>
				EFICIENCIA DE SERVICIOS POR CIUDAD POR VEHICULO</H3>Fecha inicial: ".
				pinta_FC('forma','FI')." Fecha Final: ".pinta_FC('forma','FF')."</td></tr><tr><td nowrap='yes'>

			<input type='submit' value='Procesar' name='Procesar' id='Procesar' style='width:200;height:30' onclick='this.form.submit();'>
			</FORM></center></body></html>	";
function ejecutar()
{
	global $FI,$FF;
	html();
	echo "Fecha inicial: $FI Fecha final: $FF ";
	$Periodo_inicial=date('Ym',strtotime($FI));
	$Periodo_final=date('Ym',strtotime($FF));
	$Carros=q("select * from vehiculo order by flota_aoa,placa");
	
}