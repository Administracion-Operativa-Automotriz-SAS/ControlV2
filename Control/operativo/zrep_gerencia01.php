<?php

/* PROGRAMA PARA CARGAR ESTADISTICA EN EL REPORTE GENERAL DE GERENCIA NUMERO 01
*/

include('inc/funciones_.php');
if (!empty($Acc) && function_exists($Acc)) { eval($Acc . '();'); die(); }

function importa_datos()
{
	global $id;
	html();
	echo "<body><script language='javascript'>centrar(400,300);</script>";
	include('inc/link.php');
	$D=qom("select  * from rep_gerencia01 where id=$id",$LINK);
	$Fac=qom("select * from factura where id=$D->numero_factura",$LINK);
	if($D->numero_factura2)
	{
		$Fac2=qom("select * from factura where id=$D->numero_factura2",$LINK);
		$Fac->subtotal+=$Fac2->subtotal;
		$Fac->iva+=$Fac2->iva;
		$Fac->total+=$Fac2->total;
	}
	if($D->numero_factura3)
	{
		$Fac3=qom("select * from factura where id=$D->numero_factura3",$LINK);
		$Fac->subtotal+=$Fac3->subtotal;
		$Fac->iva+=$Fac3->iva;
		$Fac->total+=$Fac3->total;
	}

	$Aseg=qom("select * from aseguradora where id=$D->aseguradora",$LINK);
	if($D->aseguradora==3)
	{
		echo "importando liberty";
		$Aseg2=qom("select * from aseguradora where id=7",$LINK);
		$Est=qom("select * from ".$Aseg->baserec.".estadistica where periodo=$D->periodo",$LINK);
		$Est2=qom("select * from ".$Aseg2->baserec.".estadistica where periodo=$D->periodo",$LINK);
		$Frecuencia=$Est2->frecuencia;
		$Cobertura=$Est->cobertura+$Est2->cobertura;
		$Siniestros=$Est->siniestros+$Est2->siniestros;
		$Servicios=$Est->servicios+$Est2->servicios;
		$Positivos1=qom("select sum(valor) as valor,count(id) as cantidad from ".$Aseg->baserec.".historico_pago where date_format(fecha,'%Y%m') = '$D->periodo' and valor>=0",$LINK);
		$Negativos1=qom("select sum(valor) as valor,count(id) as cantidad from ".$Aseg->baserec.".historico_pago where date_format(fecha,'%Y%m') = '$D->periodo' and valor<0",$LINK);
		$Positivos2=qom("select sum(valor) as valor,count(id) as cantidad from ".$Aseg2->baserec.".historico_pago where date_format(fecha,'%Y%m') = '$D->periodo' and valor>=0",$LINK);
		$Negativos2=qom("select sum(valor) as valor,count(id) as cantidad from ".$Aseg2->baserec.".historico_pago where date_format(fecha,'%Y%m') = '$D->periodo' and valor<0",$LINK);
		echo " $Positivos1->valor $Positivos1->cantidad  ";
		echo " $Positivos2->valor $Positivos2->cantidad  ";
		$Positivos->valor=$Positivos1->valor+$Positivos2->valor;
		$Positivos->cantidad=$Positivos1->cantidad+$Positivos2->cantidad;
		$Negativos->valor=$Negativos1->valor+$Negativos2->valor;
		$Negativos->cantidad=$Negativos1->cantidad+$Negativos2->cantidad;
	}
	else
	{
		$Est=qom("select * from ".$Aseg->baserec.".estadistica where periodo=$D->periodo",$LINK);
		$Positivos=qom("select sum(valor) as valor,count(id) as cantidad from ".$Aseg->baserec.".historico_pago where date_format(fecha,'%Y%m') = '$D->periodo' and valor>=0",$LINK);
		$Negativos=qom("select sum(valor) as valor,count(id) as cantidad from ".$Aseg->baserec.".historico_pago where date_format(fecha,'%Y%m') = '$D->periodo' and valor<0",$LINK);
		if($D->aseguradora==2) 
		{$Frecuencia=$Est->frecuencia; $Cobertura=$Est->cobertura+$Est->coberturap;$Siniestros=$Est->siniestros+$Est->siniestrosp;$Servicios=$Est->servicios+$Est->serviciosp;}
		else
		{$Frecuencia=$Est->frecuencia; $Cobertura=$Est->cobertura;$Siniestros=$Est->siniestros;$Servicios=$Est->servicios;}
	}
	
	if(!mysql_query("update rep_gerencia01 set pvigente=$Cobertura,solicitud=$Siniestros,servicio=$Servicios,frecuencia=$Frecuencia,
		vpositivos=$Positivos->valor,cpositivos=$Positivos->cantidad ,vnegativos=$Negativos->valor,cnegativos=$Negativos->cantidad,
		facturado_neto=$Fac->subtotal,iva=$Fac->iva,facturado_total=$Fac->total
		where id=$id",$LINK)) echo mysql_error($LINK);
	mysql_close($LINK);
	echo "<script language='javascript'>alert('Información importada satisfactoriamente.');window.close();void(null);opener.parent.location.reload();</script></body>";
}
?>