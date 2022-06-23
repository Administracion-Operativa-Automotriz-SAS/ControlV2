<?php
// Incorporar la biblioteca nusoap al script, incluyendo nusoap.php (ver imagen de estructura de archivos)
require_once('includes/nusoap.php');
// Modificar la siguiente linea con la direccion en la cual se aloja este script
$miURL = 'http://localhost/WS/';
$server = new soap_server();
$server->configureWSDL('webServiceAldiaLogistica', $miURL);
$server->wsdl->schemaTargetNamespace=$miURL;


/*
 * Ejemplo 1: getRespuesta es una funcion sencilla que recibe un parametro y retorna el mismo
 * con un string anexado
 */
$server->register('getRespuesta', // Nombre de la funcion
 array('parametro1' => 'xsd:string','parametro2' => 'xsd:string'), // Parametros de entrada
 array('return' => 'xsd:string'), // Parametros de salida
 $miURL);
 
 $server->register('getLista',array(),array('return' => 'tns:arreglo_registros'));
 
function getRespuesta($parametro1,$parametro2){
 return new soapval('return', 'xsd:string', 'soy servidor y devuelvo: '.$parametro1.' y '.$parametro2.' son parceros');
}

$server->wsdl->addComplexType('arreglo_campos','complexType','struct','all','',
	array('posicion'=>array('name'=>'posicion','type'=>'xsd:int'),
		'nombre'=>array('name'=>'nombre','type'=>'xsd:string')
	));
	
$server->wsdl->addComplexType('arreglo_registros', 'complexType', 'array', '', 
'SOAP-ENC:Array', array(), 
array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:arreglo_campos[]')), 
'tns:arreglo_campos'); 
	


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

$server->register('obtener_placas',array(),array('return' => 'tns:listado_placas'));

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