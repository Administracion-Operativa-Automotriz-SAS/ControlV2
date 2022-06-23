<?php
include('inc/funciones_.php');
include('Zip.php');
sesion();
if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}
html('CARGA DE ARCHIVO');
echo "
<script language='javascript'>
	function validar_info()
	{
		if(document.forma.FECHA.value=='')
		{
			alert('Debe seleccionar una fecha para el archivo antes de cargarlo');
		}
		else
		{
			document.forma.submit();
		}
	}
</script>
<body onload='centrar(700,400)'>
<form action='zcarga_xls.php' method='post' enctype='multipart/form-data' target='_self' name='forma' id='forma'>
	<input name='archivo' type='file' id='archivo' size='35' />
	Fecha del archivo: ".pinta_FC('forma','FECHA')."<br>
	<input name='enviar' type='button' id='enviar' value='Cargar Archivo' onclick='validar_info()' />
	<input name='Acc' type='hidden' value='cargar' /><br><br>
	Formato: xls<br>
	campos: poliza, placa, valor<br>
</form>";

function cargar()
{
	global $archivo,$FECHA;
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
		{
			// si existe lo borra.
			unlink( $A );
			PEAR::setErrorHandling(PEAR_ERROR_PRINT);
			//die();
		}
		if ( !$Lista = $zip->extract(array('add_path'=>dirname($Destino)) ))
		{
				echo '<br />Error extracting ZIP archive:<br />';
			echo $zip->_error_code . ' : ' . $zip->_error_string . '<br />';
		}
		else
		{
			$Archivofinal = $Lista[0]['filename'];
			$junk = exec("/usr/bin/sudo /bin/chmod 777 $Archivofinal");
			//echo "Extracción Exitosa del archivo <br>$Archivofinal";
			$Lista = $zip->ListContent();
		}
		unlink('planos/subido.zip');
	}
	else
	{
		echo "<H3 ALIGN='center'>ERROR EN LA LECTURA DEL ARCHIVO ORIGEN :  $userfile_name </H3> ";
		die();
	}
	$archivo=$Archivofinal;
	$status = comprobarArchivo( $archivo );
	if( $status == "" )
	{
		$status = "Archivo subido: <b>".$archivo."</b>";
		cargarArchivo($archivo,$FECHA);
	}
	echo "<body onload=\"window.open('zcarga_xls.php?Acc=calcula_historico_placa','_self');\"></body>";
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
function cargarArchivo( $destino ,$FECHA)
{
	include('inc/excel2/reader.php');
	// Se crea un objeto Spreadsheet_Excel_Reader de la clase reader.php.
	$data = new Spreadsheet_Excel_Reader();
	// Se establece la codificacion de salida.
	$data->setOutputEncoding('CP1251');
	//$destino = "./uploads/$destino";
	$data->read( "$destino" );
	//$data->read( "$destino" );
	$Temporal='tmpi_'.$_SESSION['Id_alterno'];
	q("drop table if exists $Temporal");
	q("create table $Temporal (id int not null auto_increment primary key,
	poliza varchar(50) not null,
	placa varchar(20) not null,
	valor varchar(100) not null)");

//   q( "TRUNCATE TABLE tempfile" );
    // Se buscan los nombres de las columnas para que solo con el nombre el programa
    // sea capaz de seleccionar los campos e ingresarlos a la bd en orden.
    /*for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
        if( $i == 1 ){
        	//Asigna el orden de las columnas por el nombre que se ecuentra en la fila uno.
        	$Sucursal = ( $data->sheets[0]['cells'][$i][$j] == "Sucursal" ) ? $j : $Sucursal;
        	$NumeroPoliza = ( $data->sheets[0]['cells'][$i][$j] == "Numero Poliza" ) ? $j : $NumeroPoliza;
        	$IdAsegurado =  ($data->sheets[0]['cells'][$i][$j] == "Id Asegurado" ) ? $j : $IdAsegurado;
        	$Asegurado =  ($data->sheets[0]['cells'][$i][$j] == "Asegurado" ) ? $j : $Asegurado;
        	$IdBeneficiario = ($data->sheets[0]['cells'][$i][$j] == "Id Beneficiario" ) ? $j : $IdBeneficiario;
        	$Beneficiario = ($data->sheets[0]['cells'][$i][$j] == "Beneficiario" ) ? $j : $Beneficiario;
        	$Placa = ($data->sheets[0]['cells'][$i][$j] == "Placa" ) ? $j : $Placa;
        	$TipoPoliza = ($data->sheets[0]['cells'][$i][$j] == "Tipo Poliza" ) ? $j : $TipoPoliza;
        	$FechaVigDe = ($data->sheets[0]['cells'][$i][$j] == "Fecha Vigencia Desde" ) ? $j : $FechaVigDe;
        	$FechaVigHa = ($data->sheets[0]['cells'][$i][$j] == "Fecha Vigencia Hasta" ) ? $j : $FechaVigHa;
        	$FechaInclusion = ($data->sheets[0]['cells'][$i][$j] == "Fecha Inclusion" ) ? $j : $FechaInclusion;
        	$Valor = ($data->sheets[0]['cells'][$i][$j] == "Valor" ) ? $j : $Valor;
        }
    }*/
    //Se prepara la sentencia Sql para ser enviada en paquetes de 500 registros.
	$pPoliza=1;
	$pPlaca=2;
	$pValor=3;

	$sql = "INSERT INTO $Temporal VALUES";

	$control = 500;
	include('inc/link.php');
	for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++)
	{
		$Poliza=$data->sheets[0]['cells'][$i][$pPoliza];
		$Placa=$data->sheets[0]['cells'][$i][$pPlaca];
		$Valor=$data->sheets[0]['cells'][$i][$pValor];
	//	echo "<br>$Poliza $Placa $Valor";
		mysql_query("insert into $Temporal (poliza,placa,valor) values ('$Poliza','$Placa','$Valor') ",$LINK);
	}
	mysql_close($LINK);
	q("insert into historico_pago (placa,poliza,fecha,valor) select placa,poliza,'$FECHA',valor from $Temporal");
	q("insert ignore into placa (placa,poliza) select distinct placa,poliza from $Temporal");
	q("insert ignore into poliza (numero) select distinct poliza from $Temporal");
}


function calcula_historico_placa()
{
	q("insert ignore into historico_placa (placa,periodo) select distinct placa,date_format(fecha,'%Y%m') as periodo from historico_pago");
	q("drop table if exists tmpi_tmp_pagos");
	q("create table tmpi_tmp_pagos select placa,date_format(fecha,'%Y%m') as periodo ,sum(valor) as valor
		from historico_pago group by placa,periodo order by placa,periodo");
	q("update historico_placa a,tmpi_tmp_pagos b set a.valor=b.valor where a.placa=b.placa and a.periodo=b.periodo");
	q("delete from historico_placa where valor=0");
	echo "<body onload=\"alert('Proceso finalizado');window.open('zrecalculo_periodo.php','_self');\"></body>";
}

?>