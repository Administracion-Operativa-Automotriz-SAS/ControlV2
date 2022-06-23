<?php

/**
 *   PROGRAMA PARA ACTUALIZACION DE CUENTAS DE SIIGO EN LA TABLA PUC
 *
 * @version $Id$
 * @copyright 2011
 */
include('inc/funciones_.php');
include('Zip.php');
if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}
html('Carga de tabla de cuentas');
echo "<body ><script language='javascript'>centrar(400,300);</script>
	<h3>Carga de archivo en formato CSV con separador de punto y coma</h3>
	<form action='zactualizacion_puc.php' method='post' enctype='multipart/form-data' target='_self' name='forma' id='forma'>
			Se debe subir el archivo comprimido en un ZIP.<br />
			Las columnas son:<br />
			Cuenta, Nombre, Clase, Ccosto, Tercero, Retención
			<br />Archivo: <input name='archivo' type='file' id='archivo' size='35' />
	<input name='enviar' type='submit' id='enviar' value='Cargar Archivo'>
	<input name='Acc' type='hidden' value='cargar' />
	</form>
	</body>";

function cargar()
{
	global $archivo;
	if (is_uploaded_file($archivo))
	{
		$Destino="planos/subido.zip";
		if(file_exists($Destino)) unlink($Destino);
		copy($archivo, $Destino);
		$zip = new Archive_Zip($Destino);
		$Archivodestino=$zip->ListContent();
		$A = $Archivodestino[0]['filename'];
		//echo "Verificando la existencia de $A<br>";
		if( @file_exists( $A ) )
		{unlink( $A );PEAR::setErrorHandling(PEAR_ERROR_PRINT);}
		if ( !$Lista = $zip->extract(array('add_path'=>dirname($Destino)) ))
		{echo '<br />Error extracting ZIP archive:<br />'.$zip->_error_code . ' : ' . $zip->_error_string . '<br />';}
		else {$Archivofinal = $Lista[0]['filename'];$junk = exec("/usr/bin/sudo /bin/chmod 777 $Archivofinal");$Lista = $zip->ListContent();}
		unlink('planos/subido.zip');
	}
	else
	{echo "<H3 ALIGN='center'>ERROR EN LA LECTURA DEL ARCHIVO ORIGEN :  $userfile_name </H3> ";die();}
	$archivo=$Archivofinal;
	html('Carga de actualización del PUC');
	echo "Archivo final: $archivo";
	Q("truncate table puc");
	q("load data local infile '$archivo' into table puc fields terminated by ';' (cuenta,nombre,clase,ccosto,tercero,retencion)");

}
?>