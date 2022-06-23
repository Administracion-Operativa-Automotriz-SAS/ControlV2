<?php

/**
 * IMPORTADOR DE ARCHIVOS SQL
 *
 * @version $Id$
 * @copyright 2009
 */

function importar_sql()
{
	html('IMPORTADOR DE INSTRUCCIONES SQL EN ARHCIVOS ASCII');
	echo "
		<script language='javascript'>
		function procesar_archivo()
		{
			var Archivo=document.msubir.Archivo.value;
			window.open('util.php?Acc=procesar_archivo_importado_sql&A='+Archivo,'Oculto');
		}
		</script>
		<body onload='centrar(400,400)'>
		<FORM enctype='multipart/form-data' ACTION='util.php' METHOD='post' NAME='msubir' ID='msubir' target='Oculto'>
			<input type='hidden' name='MAX_FILE_SIZE' value='2000000'>
			<input type='hidden' name='Acc' value='cargar_archivo_sql'>
			Archivo que desea subir <input name='userfile' type='file'>
			<<input type='hidden' name='Archivo' id='Archivo' value=''>
			<input type='submit' value='Subir'>
		</form>
		<iframe name='Oculto' id='Oculto' src='' width=1 height=1 style='visibility:hidden'></iframe>
		<br />
		<center><span id='Resultado'></span></center>
		</body>";
}


function cargar_archivo_sql() // permite subir un archivo al servidor
{
	$directorio='planos/';
	if($up=getFilesVar('userfile'))
	{
		if(is_uploaded_file($up['tmp_name']))
		{
			$uplfile_name = $up['name'];
            $i = 1;
            // pick unused file name
            while (file_exists($directorio.$uplfile_name)) {
              $uplfile_name = ereg_replace('(.*)(\.[a-zA-Z]+)$', '\1_'.$i.'\2', $up['name']);
              $i++;
            }
			$File_destino=strtolower($uplfile_name);
			if (!@move_uploaded_file($up['tmp_name'], $directorio.$File_destino))
			{
				die('error en move_uploaded_file');
            }
			else
			{
				chmod($directorio.$File_destino,0777);
				echo "
					<script language='javascript'>
					function carga()
					{
						parent.document.msubir.Archivo.value='".$directorio.$File_destino."';
						parent.procesar_archivo();
					}
					</script><body onload='carga();'>";
			}
		}
		else die('fallo en is_uploaded_file');
	}
	else die('Fallo en getFilesVar');
}

function procesar_archivo_importado_sql()
{
	global $A;
	echo "<script language='javascript'>
	function carga()
	{
	 alert('$A');
	}
	</script>
	<body onload='carga()'>$A";
	chmod($A,0777);
	$gestor = fopen($A, "r");
	if(!$gestor) echo "No se pudo abrir $A"; else echo "Archivo abierto<br>";
	$Contenido = fread($gestor, filesize($A));
	fclose($gestor);
	echo "<br>Analizando";
	//analiza_ascii($Contenido);
	$Partes=explode(';'.chr(13).chr(10),$Contenido);
	require('inc/link.php');
	$Contador=1;
	$Error='';
	$Ejecutado='';
	for($i=0;$i<count($Partes);$i++)
	{
		if(!(strpos($Partes[$i],'saved_cs_client') || strpos($Partes[$i],'ET character_set_client =') ))
		{
			echo "<br /><h3>Parte Numero $i</h3>".$Partes[$i]."<hr>";
			$Primer_caracter=l($Partes[$i],1);
			
			//analiza_ascii($Partes[$i])."<br>";
			if($Primer_caracter!='#' && $Primer_caracter!='/')
			{
				if(mysql_query($Partes[$i],$LINK))
				{	$Ejecutado.="$Contador [$i] Ejecutada correctamente<br>";	}
			else
				{$Error.='<li>'.mysql_error($LINK);echo "<li>".mysql_error($LINK);	}
			}
			$Contador++;
		}
	}
	echo "<script language='javascript'>
		parent.document.getElementById('Resultado').innerHTML=\"$Contador instrucciones ejecutadas<br><br>$Ejecutado<b>Errores Encontrados:</b>$Error\";
		</script>";
	mysql_close($LINK);
	//echo $Contenido;

	echo "</body>";
}

function analiza_ascii($Cadena)
{
	for($i=0;$i<=strlen($Cadena) && $i<500;$i++)
	echo "<br />$i : ".$Cadena[$i].' : '.ord($Cadena[$i]);
}


?>