<?php

/**
 * Menú para mostrar Conceptos y SubConceptos para captura de cuentas por pagar
 *
 * @version $Id$
 * @copyright 2010
 */
include('inc/funciones_.php');
html('CONCEPTOS Y SUBCONCEPTOS DE CUENTAS POR PAGAR');
echo "<script language='javascript'>
		function carga()
		{

		}
	</script>
	<body onload='carga()'>
	<script language='javascript'>centrar(600,400);</script>
	<ul>";
	$Conceptos=q("select * from concepto order by id");
	include('inc/link.php');
	while($C=mysql_fetch_object($Conceptos))
	{
		echo "<li  style='cursor:pointer;font-soze:14px;font-weight:bold;color:blue;' onclick=\"with(document.getElementById('m$C->id').style) {if(visibility=='hidden') {visibility='visible';position='relative';} else {visibility='hidden';position='absolute';} };\">$C->nombre
				<ul id='m$C->id' style='visibility:hidden;position:absolute;cursor:pointer;' >";
		$Subconceptos=mysql_query("select * from sub_concepto where concepto=$C->id order by nombre",$LINK);
		while($S=mysql_fetch_object($Subconceptos))
		{
			echo "<li style='cursor:pointer;font-weight:normal;color:black;' onclick=\"opener.document.mod.sub_concepto.value='$S->id';window.close();void(null);\">$S->nombre";
		}
		echo "</ul>";
	}
	mysql_close($LINK);
	echo "</ul>
	</body>";

?>