<?php

/**
 * Programa previo al recibo de caja
 *
 * @version $Id$
 * @copyright 2010
 */
global $VINCULOT,$VINCULOC;
if($R->factura && !$VINCULOT) $VINCULOT=$R->factura;
if($R->factura)
{
  $Fac=qo("select * from factura where id='$R->factura'");
  if($Fac->siniestro)
  {
    $Sin=qo("select * from siniestro where id='$Fac->siniestro'");
    $R->oficina=qo1("select id from oficina where ciudad='$Sin->ciudad' ");
  }
  else
  {
    $R->oficina=qo1("select id from oficina where nombre like '%Bogota%' ");
  }
}
elseif($R->siniestro)
{
  $Sin=qo("select * from siniestro where id='$R->siniestro'");
  $R->oficina=qo1("select id from oficina where ciudad='$Sin->ciudad' ");
}
else
{
	$R->oficina=qo1("select id from oficina where nombre like '%Bogota%' ");
}


if(!$R->cliente) $R->cliente=$Fac->cliente;
if(!$R->fecha) $R->fecha=date('Y-m-d');
if(!$R->consecutivo) $R->consecutivo=qo1("select max(consecutivo) from recibo_caja where oficina='$R->oficina'")+1;
if(!$R->capturado_por) $R->capturado_por=$_SESSION['Nombre'];
if(!$R->valor) $R->valor=$Fac->total;
if(!$R->base)
{
	$R->base=$R->valor;
	$R->iva=$Fac->iva;
	$R->total=$R->valor;
	if($R->autorizacion)
	{
		$Au=qo("select * from sin_autor where id='$R->autorizacion'");
		$Fr=qo("select * from franquisia_tarjeta where id='$Au->franquicia'");
		$R->prete_ica=$Fr->prete_ica;
		$R->prete_fuente=$Fr->prete_fuente;
		$R->pcomision=$Fr->pcomision;
		$R->prete_iva=$Fr->prete_iva;
		$R->rete_ica=round($R->base*$R->prete_ica/1000,2);
		$R->rete_fuente=round($R->base*$R->prete_fuente/100,2);
		$R->comision=round($R->base*$R->pcomision/100,2);
		$R->rete_iva=round($R->iva*$R->prete_iva/100,2);

	}
	elseif(!$R->siniestro)
	{
		if($Aseguradora=qo("select * from aseguradora where id='$Fac->aseguradora'"))
		{
			$R->prete_ica=$Aseguradora->rete_ica;
			$R->prete_fuente=$Aseguradora->rete_fuente;
			$R->pcomision=0;
			$R->prete_iva=$Aseguradora->rete_iva;
			$R->rete_ica=round($R->base*$R->prete_ica/1000,2);
			$R->rete_fuente=round($R->base*$R->prete_fuente/100,2);
			$R->comision=round($R->base*$R->pcomision/100,2);
			$R->rete_iva=round($R->iva*$R->prete_iva/100,2);
		}
		else
		{
			echo "<script language='javascript'>alert('Debe seleccionar una aseguradora para continuar');</script>";
		}
	}
	else
		$R->prete_ica=$R->prete_fuente=$R->pcomision=$R->prete_iva=$R->rete_ica=$R->rete_fuente=$R->comision=$R->rete_iva=0;
	$R->total_abonado=$R->total-($R->rete_ica+$R->rete_fuente+$R->comision+$R->rete_iva);
	$R->diferencia=$Fac->total-$R->total_abonado;
}


if(!$R->concepto) $R->concepto=qo2("select concat(t_concepto_fac(concepto),' ',descripcion) as nconcepto from facturad where factura='$Fac->id'",'; ');

?>