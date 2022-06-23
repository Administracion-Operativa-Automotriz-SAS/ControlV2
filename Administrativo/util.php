<?php

/**
 * Archivo de Utilidades adicionales para Aguila 5
 *
 * @version $Id$
 * @copyright 2009
 */

include('inc/funciones_.php');
if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}

function ver_imagen()  // funcion que muestra imagenes de las facturas de proveedores y las imágenes de legalización de anticipos y documentos similares
{
	global $id;
	html('IMAGENES DE FACTURA');
	if($D=qo("select *,t_tipo_documento(tipo_doc) as ntd from factura where id=$id"))
	{
		echo "Documento: <b>$D->ntd</b>  Número: <b>$D->numero</b>  Fecha: <b>$D->fecha_emision</b>  Vencimiento: <b>$D->fecha_vence</b><br> ";
		if($D->factura_f) echo "<br><img src='$D->factura_f'>";
		if($D->factura1_f) echo "<img src='$D->factura1_f'>";
		if($D->provisional1_f || $D->provisional2_f)
		{
			echo "<h3 align='center'>DOCUMENTOS DE LEGALIZACION</H3>";
			if($D->provisional1_f) echo "<br><img src='$D->provisional1_f'>";
			if($D->provisional2_f) echo "<img src='$D->provisional2_f'>";
		}
	}
	else
		echo "<b style='color:ffaaaa'>No hay información</b>";
}

function distribuir_asiento()
{
	global $id;
	html('DISTRIBUCION DE UN ASIENTO CONTABLE');
	if($D=qo("select *,t_factura(factura) as nfac,t_puc(cuenta) as ncuenta,t_proveedor(tercero) as ntercero from fac_detalle where id=$id"))
	{
		echo "Factura: <b>$D->nfac</b><br>Cuenta: <b>$D->ncuenta</b><br>Concepto: <b>$D->concepto</b><br>Tercero: <b>$D->ntercero</b><br>
				Placa: <b>$D->placa</b> Base: <b>".coma_format($D->base)."</b> Valor: <b>".coma_format($D->debito)."</b> ";
		
	}
	else
	echo "<b>No hay información.</b>";
}

?>