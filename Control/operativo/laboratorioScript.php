<?php
include('inc/funciones_.php');
//include('Zip.php');
sesion();
if(!empty($Acc) && function_exists($Acc)) { eval($Acc.'();'); die(); }
html('CARGA DE ARCHIVO');
echo "
<script language='javascript'>
	function validar_info()
	{
		document.forma.submit();
	}
</script>
<body onload='centrar(800,800)'>
<form action='laboratorioScript.php' method='post' enctype='multipart/form-data' target='_self' name='forma' id='forma'>
	<input name='archivo' type='file' id='archivo' size='35' />
	<input name='enviar' type='button' id='enviar' value='Cargar Archivo' onclick='validar_info()' />
	<input name='Acc' type='hidden' value='cargar' />
	<br /><br />
	Estimado Usuario: haga la consulta en la página de comparendos electrónicos, luego copie el resultado en una hoja electrónica y grabela con el formato <b><i>Libro de Excel 97-2003 (*.xls)</i></b> con cualquier nombre.
	Al copiar la información en la hoja electrónica asegúrese de que las placas queden en la columna <b><i>E</i></b>. Luego carguelo al sistema con esta aplicación. <br /><br />
	El resultado indicará si la placa es o no de AOA revisando una por una. <br /><br />
	Este es un ejemplo de como debe quedar la hoja electrónica antes de ser cargada y analizada: <br />
	<img src='img/ejemplocomparendos.png'>
</form>";

function cargar()
{
	global $archivo,$FECHA;
	if (is_uploaded_file($archivo))
	{
		$Destino="planos/comparendos.xls";
		if(file_exists($Destino)) unlink($Destino);
		copy($archivo, $Destino);
		//$zip = new Archive_Zip($Destino);
		//$Archivodestino=$zip->ListContent();
		//$A = $Archivodestino[0]['filename'];
		//echo "Verificando la existencia de $A<br>";
		//if( @file_exists( $A ) )
		//{
			// si existe lo borra.
		//	unlink( $A );
		//	PEAR::setErrorHandling(PEAR_ERROR_PRINT);
			//die();
		//}
		//if ( !$Lista = $zip->extract(array('add_path'=>dirname($Destino)) ))
		//{
		//		echo '<br />Error extracting ZIP archive:<br />';
		//	echo $zip->_error_code . ' : ' . $zip->_error_string . '<br />';
		//}
		//else
		//{
		//	$Archivofinal = $Lista[0]['filename'];
		//	$junk = exec("/usr/bin/sudo /bin/chmod 777 $Archivofinal");
			$junk = exec("/usr/bin/sudo /bin/chmod 777 $Destino");
			//echo "Extracción Exitosa del archivo <br>$Archivofinal";
		//	$Lista = $zip->ListContent();
		//}
		//unlink('planos/subido.zip');
	}
	else
	{
		echo "<H3 ALIGN='center'>ERROR EN LA LECTURA DEL ARCHIVO ORIGEN :  $userfile_name </H3> ";
		die();
	}
	html();
	echo "<body>";
	cargarArchivo($Destino);
	echo "</body>";
}

function comprobarArchivo( $archivo )
{
	$separar = explode('/',$archivo);
	$archivo = $separar[2];
	$separar = explode('.',$archivo);
	$status = "";
	$tamano = filesize( "planos/".$archivo );
	//Se comprueba que el formato del archivo.
	for( $i = 0; $i < sizeof( $separar ); $i++ )
	{
		if( $separar[$i] == "xls" )
		{			$status = "";	break;		}
		else
		{			$status = "El archivo no tiene una exencion valida";		}
	}
	if( $status != "" )
	{
		Enviar( "index.php","page=ctrlUploadBD&status=$status" );
		$unlink = "planos/$archivo";
		unlink( "planos/$archivo" );
	}
	else
	{
		//Se especifica el tamaño del archivo.
		if( $tamano < 102400000 )
		{			return "";					}
		else
		{
			$status = "El archivo es demaciado grande, lo maximo permitido es 10Mb";
			$unlink = "planos/$archivo";
			unlink("planos/$archivo");
			return $status;
		}
	}
}


/**
 * @param $destino: Ruta donde se almacenaran los archivos de Excel.
 * @return Crea sentencias sql insert y carga una tabla temporal en la Base de Datos.
 */
function cargarArchivo( $destino )
{
	include('inc/excel2/reader.php');
	// Se crea un objeto Spreadsheet_Excel_Reader de la clase reader.php.
	$data = new Spreadsheet_Excel_Reader();
	// Se establece la codificacion de salida.
	$data->setOutputEncoding('CP1251');
	//$destino = "./uploads/$destino";
	$data->read( "$destino" );
	$pPlaca=1;
	$poliza =2;
	echo "<br />
	<h3>Resultado del análisis de comparendos por placa</h3>
	Estimado Usuario: las placas que pertenecen a AOA se verán sombreadas en color amarillo intenso.<br />
	<br /><table border cellspacing='0'><tr >
				<th >#</th>
				<th >Placa</th>
				<th >Resultado</th>
				</tr>";
	include('inc/link.php');
	for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++)
	{
		$Placa=$data->sheets[0]['cells'][$i][$pPlaca];
		$pol=$data->sheets[0]['cells'][$i][$poliza];
		
		
		$sqlSeguros = "select * from seguros where n_poliza = '$pol'";
		$validaSeguros = qo($sqlSeguros);
		
		if($validaSeguros){
			echo "Si existe $Placa $pol<br>";	
		}else{
		   echo "No existe $Placa $pol<br>";	
		}
	}
	echo "</table>";
	mysql_close($LINK);
}


?>