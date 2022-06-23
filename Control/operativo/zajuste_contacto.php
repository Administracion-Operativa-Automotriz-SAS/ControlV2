<?php

/**
 * Programa para ajustar el primer contacto exitoso
 * $id es el id del siniestro que se desea ajustar
 * @version $Id$
 * @copyright 2010
 */

include('inc/funciones_.php');

if($Acc=='ajusta')
{
	q("update siniestro set contacto_exitoso='$ocasion' where id=$id");
	echo "<script language='javascript'>
			function carga()
			{
				window.close();void(null);
				//opener.parent.location.reload();
			}
		</script>
		<body onload='carga()'></body>";
	die();
}

html('Ajuste Contacto Exitoso');
$S=qo("select id,observaciones,contacto_exitoso,placa from siniestro where id=$id");
if($S->contacto_exitoso!='0000-00-00 00:00:00')
{
	echo "<script language='javascript'>
			function carga()
			{
				alert('Contacto exitoso ya marcado');
				window.close();void(null);
			}
		</script>
		<body onload='carga()'></body>";
	die();
}

$Arreglo=explode("\n",$S->observaciones);
echo "<script language='javascript'>centrar(800,200);</script>
<h3>$S->placa</h3>Seleccione el contacto exitoso:<br />
<form action='zajuste_contacto.php' method='post' target='_self' name='forma' id='forma'>
	<select name='ocasion'>";
for ($i=0;$i<count($Arreglo);$i++)
{
	if(strpos($Arreglo[$i],']') && !strpos($Arreglo[$i],'] Consultó.'))
	{
		$Linea=substr($Arreglo[$i],strpos($Arreglo[$i],'[')+1);
		$Partes=explode(']',$Linea);
		if(strlen($Partes[0])>20) $Partes[0]=r($Partes[0],19);
		echo "<option value='".$Partes[0]."'>".$Partes[0]." - ".$Partes[1]."</option>";
	}
}
echo "</select><br /><input type='hidden' name='Acc' value='ajusta'><input type='hidden' name='id' value='$id'>
<input type='submit'>
</form>";



?>