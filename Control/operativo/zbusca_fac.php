<?php

/**
 * busca recibos de caja para pintar la cartera por cobrar o cobrada
 *
 * @version $Id$
 * @copyright 2010
 */
if($Facturas=q("select * from factura where siniestro=$I->ids and anulada=0 "))
{
	echo "<td align='right'>";
	$Total_facturas=0;
	$Ver_facturas='';
	while($Fac=mysql_fetch_object($Facturas))
	{
		$Total_facturas+=$Fac->total;
		$Ver_facturas.="<a class='info' style='cursor:pointer' onclick=\"modal('zfacturacion.php?Acc=imprimir_factura&id=$Fac->id',0,0,800,800,'fac');\"><img src='gifs/standar/Preview.png' border='0'><span>Factura $Fac->consecutivo</span></a>";
	}
	echo coma_format($Total_facturas)." ".$Ver_facturas ;
	echo "</td>";
	$Saldo=$I->re_valor-$Total_facturas;
	echo "<td align='right'>".coma_format($Saldo)."</td>";
}

?>