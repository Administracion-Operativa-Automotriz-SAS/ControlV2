<?php

/**
 * busca recibos de caja para pintar la cartera por cobrar o cobrada
 *
 * @version $Id$
 * @copyright 2010
 */
if($Re=qo("select *,left(t_oficina(oficina),3) as noficina from recibo_caja where factura =$I->fa_id and anulado=0 "))
{
	echo "<td align='right'><a class='info' style='cursor:pointer' onclick=\"modal('zcartera.php?Acc=imprimir_recibo&id=$Re->id',0,0,700,1000,'rc');\">$Re->noficina-".str_pad($Re->consecutivo,6,'0',STR_PAD_LEFT).($Re->anulado?" <b style='background-color:ffff00'>Anulado</b> ":"").
		"<span>Ver Recibo</span></a></td><td>$Re->fecha</td>";
	if($Re->consignacion_numero)
	{
		if($Re->consignacion_f)
			echo "<td><a class='info' style='cursor:pointer' onclick=\"modal('$Re->consignacion_f',0,0,700,1000,'cs');\">$Re->consignacion_numero<span>Ver Consignacion</span></a></td><td>$Re->consignacion_fecha</td>";
		else
			echo "<td>$Re->consignacion_numero</td><td>$Re->consignacion_fecha</td>";
	}
	if($Re->consignacion_numero2)
	{
		if($Re->consignacion2_f)
			echo "<td><a class='info' style='cursor:pointer' onclick=\"modal('$Re->consignacion2_f',0,0,700,1000,'cs');\">$Re->consignacion_numero2<span>Ver Consignacion</span></a></td><td>$Re->consignacion_fecha2</td>";
		else
			echo "<td>$Re->consignacion_numero2</td><td>$Re->consignacion_fecha2</td>";
	}

}

?>