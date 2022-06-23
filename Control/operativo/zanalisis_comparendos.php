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
<form action='zanalisis_comparendos.php' method='post' enctype='multipart/form-data' target='_self' name='forma' id='forma'>
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
	$pPlaca=5;
	$culumComparendo = 2;
	$culumFecha = 3;
	$horaComparendo = 4;
	$contador=1;
	$valorComparendo = 9;
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
		$comparendo=$data->sheets[0]['cells'][$i][$culumComparendo];
		$fechaComparendo=$data->sheets[0]['cells'][$i][$culumFecha];
		$valueCom = $data->sheets[0]['cells'][$i][$valorComparendo];
		$horaComp = $data->sheets[0]['cells'][$i][$horaComparendo];
		
		
		if($Placa)
		{
			$sqlVerVh = "select id,t_oficina(ultima_ubicacion) as oficina from vehiculo where placa='$Placa'";
			$V=qo($sqlVerVh,$LINK);
			echo "<tr ".($V?"bgcolor='ffff00'":"")."><td align='right'>$contador</td><td >$Placa</td><td align='center'>".($V?"AOA $V->oficina":"Este vehiculo no existe")."</td></tr>";
			
			if($V){
				$sql = "SELECT numero_compraendo FROM comparendo WHERE numero_compraendo = '$comparendo'";
				$Com = qo($sql);
				
				if(!$Com){
					
				    $sqlIndagar = "SELECT *
									FROM ubicacion
									WHERE fecha_final >= '$fechaComparendo' AND vehiculo = $V->id
									ORDER BY id  asc
									LIMIT 1";
					
					
					$indagar = qo($sqlIndagar);
					
					$idUbicacionFinal = $indagar->id;
					
					
					/*Fecha incial*/
					$sqlUbicacionTodo = "SELECT fecha_inicial,id FROM ubicacion WHERE vehiculo = $V->id 
					                     and fecha_inicial = '$fechaComparendo'";
					$tUbicacionFecha = qo($sqlUbicacionTodo);
					
					if($tUbicacionFecha){
						$sqlSiniestro = "SELECT * FROM siniestro WHERE ubicacion = '$tUbicacionFecha->id'";
						$sini = qo($sqlSiniestro);
						$sqlServicio = "SELECT * FROM  cita_servicio WHERE siniestro = $sini->id";
						$servcio = qo($sqlServicio);
						
						if($horaComp >=  $servcio->hora_llegada){
							$idUbicacionFinal = $tUbicacionFecha->id;
							
							
						}
					}
					
					/*Fecha final*/
					
					$sqlUbicacionTodoFinal = "SELECT fecha_inicial,id FROM ubicacion WHERE vehiculo = $V->id 
					                     and fecha_final = '$fechaComparendo'";
					$tUbicacionFechaFinal = qo($sqlUbicacionTodoFinal);
					
					if($tUbicacionFechaFinal){
						$sqlSiniestro = "SELECT * FROM siniestro WHERE ubicacion = '$tUbicacionFechaFinal->id'";
						$sini = qo($sqlSiniestro);
						$sqlServicio = "SELECT * FROM  cita_servicio WHERE siniestro = $sini->id";
						$servcio = qo($sqlServicio);
						
						if($servcio->hora_llegada >= $horaComp){
							$idUbicacionFinal = $tUbicacionFecha->id;
							
						}
					}
					
					if($idUbicacionFinal){
						
					$sqlSiniestro = "select id,numero from siniestro where ubicacion = '$idUbicacionFinal'";
					
					$busquedaSiniestro = qo($sqlSiniestro);
					
					if($busquedaSiniestro->id){
						
						
						$sqlVeCompra = "SELECT id FROM vehiculo where id = $V->id AND id_propietario = 900174552";
						$v = qo($sqlVeCompra);
						
						if($v){
							$sql = "INSERT INTO comparendo
								(siniestro,vehiculo,fecha_comparendo,valor_comparendo,numero_compraendo,estado)
								VALUES ($busquedaSiniestro->id,$V->id,'$fechaComparendo',$valueCom,$comparendo,1)";
								echo "<br>Comparendo sucedió en un servicio placa: $Placa Fecha: $fechaComparendo Numero de siniestro: $busquedaSiniestro->numero #:$comparendo";
								
								
						}else{
							echo "<br>En SERVICIO, El vehiculo con placa: $Placa Fecha: $fechaComparendo Numero de siniestro: $busquedaSiniestro->numero #:$comparendo no es de AOA<br>";
						}
					}else{
						$sqlVeCompra = "SELECT id FROM vehiculo where id = $V->id AND id_propietario = 900174552";
						$v = qo($sqlVeCompra);
						if($v){
							$sql = "INSERT INTO comparendo
								(vehiculo,fecha_comparendo,valor_comparendo,numero_compraendo,estado)
								VALUES ($V->id,'$fechaComparendo',$valueCom,$comparendo,1)";
							echo "<br>El comparendo se realizo cuando el vehiculo no estaba en servicio placa: $Placa  fecha: $fechaComparendo #:$comparendo<br><br>";
						}else{
							echo "<br>Sin servicio ,El vehiculo con placa: $Placa Fecha: $fechaComparendo Numero de siniestro: $busquedaSiniestro->numero #:$comparendo no es de AOA<br>";
						}
					}
				}else{
					$sqlVeCompra = "SELECT id FROM vehiculo where id = $V->id AND id_propietario = 900174552";
					$v = qo($sqlVeCompra);
					if($v){
						$sql = "INSERT INTO comparendo
								(siniestro,vehiculo,fecha_comparendo,valor_comparendo,numero_compraendo,estado)
								VALUES ($V->id,'$fechaComparendo',$valueCom,$comparendo,1)";
					echo "No se encontro ubicacion placa: $Placa fecha: $fechaComparendo #:$comparendo<br><br>";
					}else{
						echo "<br>Sin ubicacion ,El vehiculo con placa: $Placa Fecha: $fechaComparendo Numero de siniestro: $busquedaSiniestro->numero #:$comparendo no es de AOA<br>";
					}	
				}
					
				}else{
					echo "Ya tenia el mismo numero de comparendo #:$comparendo  placa: $Placa<br>";
				}
			}
			$contador++;
		}

	}
	echo "</table>";
	mysql_close($LINK);
}


?>