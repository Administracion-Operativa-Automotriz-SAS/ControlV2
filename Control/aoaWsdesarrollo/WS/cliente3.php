<?php
set_time_limit(0);
header('Content-Type: text/plain');
// Incluimos la biblioteca de NuSOAP (la misma que hemos incluido en el servidor, ver la ruta que le especificamos)
require_once('includes/nusoap.php');
// Crear un cliente apuntando al script del servidor (Creado con WSDL) - 
// Las proximas 3 lineas son de configuracion, y debemos asignarlas a nuestros parametros
$serverURL = 'http://190.85.62.37/wsPrueba';
$serverScript = 'server3.php';
$metodoALlamar = 'getRespuestaTotalData';

// Crear un cliente de NuSOAP para el WebService
$cliente = new nusoap_client("$serverURL/$serverScript?wsdl", 'wsdl');

// Se pudo conectar?
$error = $cliente->getError();
if ($error) {
 echo '<pre style="color: red">' . $error  . '</pre>';
 echo '<p style="color:red;'>htmlspecialchars($cliente->getDebug(), ENT_QUOTES).'</p>';
 die();
}

// 1. Llamar a la funcion getRespuestaTotalData del servidor
$result = $cliente->call(	"getRespuestaTotalData", 											// Funcion a llamar
							 array(), 	// Parametros pasados a la funcion
							"uri:$serverURL/$serverScript", 							// namespace
						 	"uri:$serverURL/$serverScript/getRespuestaTotalData" 				// SOAPAction
						);

// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
if ($cliente->fault) {
 echo '<b>Error: ';
 print_r($result);
 echo '</b>';
} else {
 $error = $cliente->getError();
 if ($error) {
 echo '<b style="color: red">Error: ' . $error . '</b>';
 } else {
 echo 'Respuesta: '.$result;
 }
}
 echo "<br><br>";

// 2. Llamar a la funcion getRespuestaTotalData del servidor
$resultUno = $cliente->call(	"getRespuestaFilterPlateNumberDateTime", 						// Funcion a llamar
							 	array (	'plateAldiaLogistica' => 'XID177',
							 			'minorDateAldiaLogistica' => '2012-04-01 07:00:00',
										'majorDateAldiaLogistica' => '2012-04-01 07:15:00'
										), 														// Parametros pasados a la funcion
							"uri:$serverURL/$serverScript", 									// namespace
						 	"uri:$serverURL/$serverScript/getRespuestaFilterPlateNumberDateTime"// SOAPAction
						);

// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
if ($cliente->fault) {
 echo '<b>Error: ';
 print_r($resultUno);
 echo '</b>';
} else {
 $error = $cliente->getError();
 if ($error) {
 echo '<b style="color: red">Error: ' . $error . '</b>';
 } else {
 echo 'Respuesta Prueba:<br>'.$resultUno;
 }
}


 echo "<br><br>";

// 2. Llamar a la funcion getRespuestaTotalData del servidor
$resultDos = $cliente->call(	"getRespuestaFilterPlateNumberDate", 						// Funcion a llamar
							 	array (	'plateAldiaLogistica' => 'XID177',
							 			'dateAldiaLogistica' => '2012-04-01',
										), 														// Parametros pasados a la funcion
							"uri:$serverURL/$serverScript", 									// namespace
						 	"uri:$serverURL/$serverScript/getRespuestaFilterPlateNumberDate"// SOAPAction
						);

// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
if ($cliente->fault) {
 echo '<b>Error: ';
 print_r($resultDos);
 echo '</b>';
} else {
 $error = $cliente->getError();
 if ($error) {
 echo '<b style="color: red">Error: ' . $error . '</b>';
 } else {
 echo 'Respuesta Prueba 2:<br>'.$resultDos;
 }
}


echo '<br /> <br />';
/*
// 3. Llamar a la funcion getRespuestaTotalData del servidor
$resultDos = $cliente->call(	"getRespuestaFilterAllPlatesNumbersDate", 						// Funcion a llamar
							 	array (	'dateAldiaLogistica' => '2012-04-01' ), 				// Parametros pasados a la funcion
								"uri:$serverURL/$serverScript", 									// namespace
						 		"uri:$serverURL/$serverScript/getRespuestaFilterAllPlatesNumbersDate"// SOAPAction
						);

// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
if ($cliente->fault) {
 echo '<b>Error: ';
 print_r($resultDos);
 echo '</b>';
} else {
 $error = $cliente->getError();
 if ($error) {
 echo '<b style="color: red">Error: ' . $error . '</b>';
 } else {
 echo 'Respuesta Prueba 3:<br>'.$resultDos;
 }
}



echo '<br /><br />';
$Resultado2=$cliente->call('getLista',array(),"uri:$serverURL/$serverScript", "uri:$serverURL/$serverScript/$metodoALlamar");
 echo "<br><br>";
print_r($Resultado2);
 
$Resultado3=$cliente->call('obtener_placas',array(),"uri:$serverURL/$serverScript", "uri:$serverURL/$serverScript/$metodoALlamar");
echo "<br><br>";
print_r($Resultado3);echo "<br><br>";
//echo $Resultado3[0]['placa'];echo "<br><br>";
//echo $Resultado3[0]['datosplaca']['latitud'];
/**/
?>