<?php

/**
 *   PROGRAMA QUE BUSCA NUEVAS SOLICITUDES DE AUTORIZACIONES Y EMITE UN SONIDO.
 *
 * @version $Id$
 * @copyright 2010
 */
include('inc/funciones_.php');

echo "<script language='javascript'>
	function carga()
	{

	}
	function recarga()
	{
		location.reload();
	}
	Recargar=setTimeout(recarga,60000);
</script>
<body onload='carga()'>";
if($Autorizaciones=q("select id from aoacol_aoacars.sin_autor where estado='E'"))
{
	play_audio('img/timbre.mp3',1 /*autoplay=1*/);
}
?>













