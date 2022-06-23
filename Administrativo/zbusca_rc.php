<?php

/**
 * busca recibos de caja para pintar la cartera por cobrar o cobrada
 *
 * @version $Id$
 * @copyright 2010
 */
if($Re=qo("select * from recibo_caja where factura =$I->fa_id"))
{
	echo "<td>$Re->consecutivo</td><td>$Re->fecha</td>";
}

?>