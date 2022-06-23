<?php
set_time_limit(0);
// Incorporar la biblioteca nusoap al script, incluyendo nusoap.php (ver imagen de estructura de archivos)
require_once('includes/nusoap.php');
// Modificar la siguiente linea con la direccion en la cual se aloja este script
$miURL = 'http://190.85.62.37/wsPrueba/'; //
$server = new soap_server();
$server->configureWSDL('webServiceAldiaLogistica', $miURL);
$server->wsdl->schemaTargetNamespace=$miURL;


/*
 * Ejemplo 1: getRespuestaTotalData es una funcion sencilla que recibe un parametro y retorna el mismo
 * con un string anexado
 */
$server->register (	'getRespuestaTotalData', 				// Nombre de la funcion
 					array (	'parametro1' => 'xsd:string',
							'parametro2' => 'xsd:string' 
							), 								// Parametros de entrada
 					array (	'return' => 'xsd:string' ), 	// Parametros de salida
 					$miURL
					);
 
$server->register (	'getLista',
					array(),
					array(	'return' => 'tns:arreglo_registros' )
					);
 
function getRespuestaTotalData($parametro1, $parametro2){
######################################################################
	function translateLatLong2Address ($cx, $cy){
		//$json_string2 = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$cx.','.$cy.'&sensor=false';
		$json_string2 = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$cx.','.$cy.'&sensor=false');
		$obj2 = json_decode($json_string2);
		$addr2 = $obj2->results[0]->formatted_address;
		$addr3 = $obj2->results[0]->address_components[2]->long_name;		
		$arrayDir = array ($addr2, $addr3);
		return 	$arrayDir;	
		
		}
	function dataRestMinuts($dateTimeActual, $valueRest) {
	
		$piecesDateTimeActual = @explode(" ", $dateTimeActual);
		$explodeDateActual = $piecesDateTimeActual[0];
		$dataDate = @explode("-", $explodeDateActual);
		$yearData = $dataDate[0];
		$monthData = $dataDate[1];
		$dayData = $dataDate[2];
		$explodeTimeActual = $piecesDateTimeActual[1];
		$dataTime = @explode(":", $explodeTimeActual);
		$hourData = $dataTime[0];
		$minutData = $dataTime[1];
		$secondData = $dataTime[2];
		
		$dataMinus15 = @date ( "Y-m-d H:i:s" , @mktime($hourData, ($minutData - $valueRest), $secondData, $monthData, $dayData, $yearData) );
		return $dataMinus15;
		
		}
######################################################################
/*
	$conexion = @mysql_connect('localhost', 'dpa', 'GzL78R8LJAGZWNyP');
	@mysql_select_db("dpa", $conexion);

	$sql = "SELECT b.imei

			FROM get_vehiculo a, get_gpc b
			WHERE 	a.id=b.vehiculo
			AND		a.flota='Aldia Logistica'
			";
	$querySql = @mysql_query($sql, $conexion);
	$nResultSql = @mysql_num_rows($querySql);

	if ( isset($nResultSql) and ($nResultSql > 0) ) {
		$stringImei = '';
		$arrayImei1 = array();
		for ($i=0; $i<$nResultSql; $i++) {
			$regSql = @mysql_fetch_array($querySql);
			$arrayImei[] = $regSql['imei'];
			$stringImei1 = $stringImei.''.$regSql['imei'].';';
			
			}
		}
	@mysql_free_result($querySql);

	$difDefaultTimeZone = (@date_default_timezone_set("America/Bogota"));
	$timeDefaultTimeZone = @time();
	$dateTimeActual = @date ( "Y-m-d" )." ". @date ( "H:i:s" , $timeDefaultTimeZone );
	$dateTimeRestActual15 = dataRestMinuts($dateTimeActual, 15);

	$stringImei = '';	

	for ($j=0; $j<count($arrayImei); $j++) {

		$sql = "SELECT 	DISTINCT
						c.cx, 
						c.cy, 
						c.imei,
						a.placa, 
						c.vel_dpa, 
						c.fecha_inser
	
				FROM get_vehiculo a, get_gpc b, get_ubicacion c 
				WHERE a.id=b.vehiculo 
				AND c.imei=b.imei 
				AND	a.flota='Aldia Logistica'
				AND	c.imei='".$arrayImei[$j]."'
				AND c.fecha_inser BETWEEN '".$dateTimeRestActual15."' AND '".$dateTimeActual."'

				";

		$querySql = @mysql_query($sql, $conexion);
		$nResultSql = @mysql_num_rows($querySql);
		set_time_limit(0);

		if ($nResultSql > 0) {
			
			for ($k=0; $k<$nResultSql; $k++) { 
				$regSql = @mysql_fetch_array($querySql);
				@set_time_limit(0);
				$imeiAldiaLogistica = $regSql['imei'];
				$placaAldiaLogistica = $regSql['placa'];
				$cxAldiaLogistica = $regSql['cx'];
				$cyAldiaLogistica = $regSql['cy'];
				$velDpaAldiaLogistica = $regSql['vel_dpa'];
				$fechaInserAldiaLogistica = $regSql['fecha_inser'];
				
				if ( $velDpaAldiaLogistica>=0 and  $velDpaAldiaLogistica<=5 ) {
					$estadoDpaAldiaLogistica = 'DETENIDO';
					
					}
				if ( $velDpaAldiaLogistica>=6 and  $velDpaAldiaLogistica<=16 ) {
					$estadoDpaAldiaLogistica = 'LENTO';
						
					}
				if ( $velDpaAldiaLogistica>=17 and  $velDpaAldiaLogistica<=70  ) {
					$estadoDpaAldiaLogistica = 'NORMAL';
					
					}
				if ( $velDpaAldiaLogistica>=71 ) {
					$estadoDpaAldiaLogistica = 'RAPIDO';
						
					}

				list($direccionUbicacion, $direccionBarrio) = translateLatLong2Address( $cyAldiaLogistica, $cxAldiaLogistica );
				if (empty($direccionBarrio)) {
					$direccionBarrio = 'N.A.';
					
					}
		
			$rowTotal = $placaAldiaLogistica.';'.$cyAldiaLogistica.';'.$cxAldiaLogistica.';'.$velDpaAldiaLogistica.';'.$estadoDpaAldiaLogistica.';'.$direccionUbicacion.';'.$direccionBarrio.';'.$fechaInserAldiaLogistica.';';

				$stringImei = $stringImei.''.$rowTotal.'|';

				}
			}	
		}
	@mysql_free_result($querySql);
/**/		
	return new soapval('return', 'xsd:string', 'XXX');
}
###########################################################################################################################
################ METODO No. 2.
$server->register (	'getRespuestaFilterPlateNumberDateTime',			// Nombre de la funcion
 					array (	'plateAldiaLogistica' => 'xsd:string',
							'minorDateAldiaLogistica' => 'xsd:dateTime', 	//minor and major
							'majorDateAldiaLogistica' => 'xsd:dateTime' 
							), 											// Parametros de entrada
 					array (	'return' => 'xsd:string' ), 				// Parametros de salida
 					$miURL
					);
/**/
function getRespuestaFilterPlateNumberDateTime($plateAldiaLogistica, $minorDateAldiaLogistica, $majorDateAldiaLogistica){ 
######################################################################
	function translateLatLong2Address ($cx, $cy){
		//$json_string2 = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$cx.','.$cy.'&sensor=false';
		$json_string2 = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$cx.','.$cy.'&sensor=false');
		$obj2 = json_decode($json_string2);
		$addr2 = $obj2->results[0]->formatted_address;
		$addr3 = $obj2->results[0]->address_components[2]->long_name;		
		$arrayDir = array ($addr2, $addr3);
		return 	$arrayDir;	
		
		}
######################################################################
/*
	$conexion = @mysql_connect('localhost', 'dpa', 'GzL78R8LJAGZWNyP');
	@mysql_select_db("dpa", $conexion);

	$difDefaultTimeZone = (@date_default_timezone_set("America/Bogota"));
	$timeDefaultTimeZone = @time();
	$dateTimeActual = @date ( "Y-m-d" )." ". @date ( "H:i:s" , $timeDefaultTimeZone );

	$stringImei = '';	


	$sql = "SELECT 	DISTINCT
					c.cx, 
					c.cy, 
					c.imei,
					a.placa, 
					c.vel_dpa, 
					c.fecha_inser
			FROM get_vehiculo a, get_gpc b, get_ubicacion c 
			WHERE a.id=b.vehiculo 
			AND c.imei=b.imei 
			AND	a.flota='Aldia Logistica'
			AND	a.placa='".$plateAldiaLogistica."'
			AND c.fecha_inser BETWEEN '".$minorDateAldiaLogistica."' AND '".$majorDateAldiaLogistica."'

			";

	$querySql = @mysql_query($sql, $conexion);
	$nResultSql = @mysql_num_rows($querySql);
	set_time_limit(0);

	if ($nResultSql > 0) {
			
		for ($k=0; $k<$nResultSql; $k++) { 
			$regSql = @mysql_fetch_array($querySql);
			@set_time_limit(0);
			$imeiAldiaLogistica = $regSql['imei'];
			$placaAldiaLogistica = $regSql['placa'];
			$cxAldiaLogistica = $regSql['cx'];
			$cyAldiaLogistica = $regSql['cy'];
			$velDpaAldiaLogistica = $regSql['vel_dpa'];
			$fechaInserAldiaLogistica = $regSql['fecha_inser'];
				
			if ( $velDpaAldiaLogistica>=0 and  $velDpaAldiaLogistica<=5 ) {
				$estadoDpaAldiaLogistica = 'DETENIDO';
				
				}
			if ( $velDpaAldiaLogistica>=6 and  $velDpaAldiaLogistica<=16 ) {
				$estadoDpaAldiaLogistica = 'LENTO';
					
				}
			if ( $velDpaAldiaLogistica>=17 and  $velDpaAldiaLogistica<=70  ) {
				$estadoDpaAldiaLogistica = 'NORMAL';
				
				}
			if ( $velDpaAldiaLogistica>=71 ) {
				$estadoDpaAldiaLogistica = 'RAPIDO';
					
				}

			list($direccionUbicacion, $direccionBarrio) = translateLatLong2Address( $cyAldiaLogistica, $cxAldiaLogistica );
			if (empty($direccionBarrio)) {
				$direccionBarrio = 'N.A.';
				
				}
			//$valuePrue = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$cyAldiaLogistica.','.$cxAldiaLogistica.'&sensor=false';
			//$rowTotal = $placaAldiaLogistica.';'.$cyAldiaLogistica.';'.$cxAldiaLogistica.';'.$velDpaAldiaLogistica.';'.$estadoDpaAldiaLogistica.';'.$direccionUbicacion.';'.$direccionBarrio.';'.$fechaInserAldiaLogistica.';'.$valuePrue;
			$rowTotal = $placaAldiaLogistica.';'.$cyAldiaLogistica.';'.$cxAldiaLogistica.';'.$velDpaAldiaLogistica.';'.$estadoDpaAldiaLogistica.';'.$direccionUbicacion.';'.$direccionBarrio.';'.$fechaInserAldiaLogistica;
				
			$stringImei = $stringImei.''.$rowTotal.'|';

			}
		}	
	@mysql_free_result($querySql);
/**/

list($direccionUbicacion, $direccionBarrio) = translateLatLong2Address ('7.996383333330', '-73.510683333300');
//echo $direccionUbicacion;

	return new soapval('return', 'xsd:string', $direccionUbicacion);
	}

$server->wsdl->addComplexType('arreglo_campos','complexType','struct','all','',
	array('posicion'=>array('name'=>'posicion','type'=>'xsd:int'),
		'nombre'=>array('name'=>'nombre','type'=>'xsd:string')
	));
	
$server->wsdl->addComplexType(	'arreglo_registros', 'complexType', 'array', '', 
								'SOAP-ENC:Array', 
								array(), 
								array (	
										array (	'ref' => 'SOAP-ENC:arrayType', 
												'wsdl:arrayType' => 'tns:arreglo_campos[]'
												)
										), 
								'tns:arreglo_campos'
							); 
	


function getLista()
{
	$Lista=array();
//	$Lista[0]=array('posicion'=>1,'nombre'=>'Arturo');
//	$Lista[1]=array('posicion'=>2,'nombre'=>'Paulo');
//	$Lista[2]=array('posicion'=>3,'nombre'=>'Gabriel');
	
	$Lista[0]['posicion']=1;$Lista[0]['nombre']='Arturo';
	$Lista[1]['posicion']=2;$Lista[1]['nombre']='Paulo';
	$Lista[2]['posicion']=3;$Lista[2]['nombre']=array('uno'=>'1','dos'=>'2','tres'=>'3');
	return $Lista;
}


// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$server->wsdl->addComplexType('datos_placa','complexType','struct','all','',
array('latitud'=>array('name'=>'latitud','type'=>'xsd:int')),
array('longitud'=>array('name'=>'longitud','type'=>'xsd:int')),
array('velocidad_inicial'=>array('name'=>'velocidad_inicial','type'=>'xsd:int')),
array('direccion'=>array('name'=>'direccion','type'=>'xsd:int'))
);

$server->wsdl->addComplexType('datos_placa_array',
'complexType',
'array','',
'SOAP-ENC:Array',
array(),
array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:datos_placa[]')),
'tns:datos_placa');

$server->wsdl->addComplexType('registro_placa','complexType','struct','all','',
array('placa'=>array('name'=>'placa','type'=>'xsd:string')),
array('datosplaca'=>array('name'=>'datosplaca',type=>'tns:datos_placa_array'))
);

$server->wsdl->addComplexType('listado_placas', 'complexType', 'array', '', 
'SOAP-ENC:Array', array(), 
array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:registro_placa[]')), 'tns:registro_placa'); 

$server->register (	'obtener_placas',
					array(),
					array(	'return' => 'tns:listado_placas' )
					);

function obtener_placas()
{
	$Lista=array();
	$Lista[0]=array();
	$Lista[0]['placa']='ABC123';
	$Lista[0]['datosplaca']=array('latitud'=>10,'longitud'=>10,'velocidad_inicial'=>10,'direccion'=>10);
	//$Lista[0]['datosplaca']['latitud']=10;
	//$Lista[0]['datosplaca']['longitud']=10;
	//$Lista[0]['datosplaca']['velocidad_inicial']=10;
	//$Lista[0]['datosplaca']['direccion']=10;
	
	$Lista[1]=array();
	$Lista[1]['placa']='ABC143';
	$Lista[1]['datosplaca']=array('latitud'=>10,'longitud'=>10,'velocidad_inicial'=>10,'direccion'=>10);
	
	
	//$Lista[0]=array('placa'=>'ABC123','datosplaca'=>array('latitud'=>10,'longitud'=>'11','velocidad_inicial'=>0,'direccion'=>1));
	return $Lista;
}

// Las siguientes 2 lineas las aporto Ariel Navarrete. Gracias Ariel
if ( !isset( $HTTP_RAW_POST_DATA ) )
    $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );

$server->service($HTTP_RAW_POST_DATA);
?>