<?php

/**
 * Programa para reconstruir las observaciones a partir del seguimiento
 */

include('inc/funciones_.php');

if($Seguimiento=q("select * from seguimiento where siniestro=$id order by fecha,hora"))
{
	$Observaciones='';
	while($S=mysql_fetch_object($Seguimiento))
	{
		$Observaciones.="\n$S->usuario [$S->fecha $S->hora]: $S->descripcion. ";
	}
	q("update siniestro set observaciones=concat(observaciones,\"$Observaciones\") where id=$id");
	echo "<script language='javascript'>
			function carga()
			{
				//alert('Reconstrucción de observaciones hecha satisfactoriamente');
				window.close();
				void(null);
			}
		</script>
		<body onload='carga()'></body>m";
}
else
{
	echo "Este siniestro no cuenta con seguimiento.";
}

?>