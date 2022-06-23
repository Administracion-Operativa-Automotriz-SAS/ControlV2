<?php
include('inc/funciones_.php');
/*

COLSEGUROS

Control

CALCULO DE ESTADISTICA POR PERIODO PARA ENVIAR A RECAUDOS
Variable que recibe: $PERIODO del cual se extrae:
de la tabla de siniestros se cuenta la cantidad de registros usando como parametro la fecha de autorizacion
de la misma tabla se buscan los siniestros con estado 7: en servicio u 8: concluido para saber la utilizacion
los dos conteos se deben enviar al programa de recaudos correspondiente para ser guardados en la tabla ESTADISTICA
*/

if($PERIODO && $Retorno && $ASEGURADORA)
{
	$Conteo1=qo1("select count(*) from siniestro where date_format(fec_autorizacion,'%Y%m')='$PERIODO' and aseguradora='$ASEGURADORA' ");
	$Conteo1+=qo1("select count(*) from siniestro_hst where date_format(fec_autorizacion,'%Y%m')='$PERIODO' and aseguradora='$ASEGURADORA' ");
	$Conteo2=qo1("select count(*) from siniestro where date_format(fec_autorizacion,'%Y%m')='$PERIODO' and estado in (7,8) and aseguradora='$ASEGURADORA' ");
	$Conteo2+=qo1("select count(*) from siniestro_hst where date_format(fec_autorizacion,'%Y%m')='$PERIODO' and estado in (7,8) and aseguradora='$ASEGURADORA' ");
	$Conteo3=qo1("select count(*) from ubicacion,siniestro where siniestro.ubicacion=ubicacion.id and date_format(ubicacion.fecha_inicial,'%Y%m')='$PERIODO' and ubicacion.estado in (1,7) and siniestro.aseguradora='$ASEGURADORA' ");
	$Conteo3+=qo1("select count(*) from ubicacion,siniestro_hst where siniestro_hst.ubicacion=ubicacion.id and date_format(ubicacion.fecha_inicial,'%Y%m')='$PERIODO' and ubicacion.estado in (1,7) and siniestro_hst.aseguradora='$ASEGURADORA' ");
	if(inlist($ASEGURADORA,'2,1,3,4,7'))
		header("location:".base64_decode($Retorno)."/zactualiza_estadistica.php?Acc=actualiza&PERIODO=$PERIODO&c1=$Conteo1&c2=$Conteo2&c3=$Conteo3");
	else
    header("location:".base64_decode($Retorno)."/zactualiza_estadistica.php?Acc=actualizap&PERIODO=$PERIODO&c1=$Conteo1&c2=$Conteo2&c3=$Conteo3");
}
else
	echo "<script language='javascript'>
		function carga()
		{
			alert('No hay información sobre el periodo');
			window.close();void(null);
		}
	</script>
	<body onload='carga()'></body>";

?>
