<?php
@set_time_limit(0);
@ini_set('default_socket_timeout', 2600);

//header('Content-Type: text/plain');
// Incluimos la biblioteca de NuSOAP (la misma que hemos incluido en el servidor, ver la ruta que le especificamos)
require_once('includes/nusoap.php');
// Crear un cliente apuntando al script del servidor (Creado con WSDL) - 
// Las proximas 3 lineas son de configuracion, y debemos asignarlas a nuestros parametros
$serverURL = 'http://190.85.62.37/logWs/ws';
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
$password = '12345678';
// 1. Llamar a la funcion getToken del servidor
$token = $cliente->call(	"getToken", 												// Funcion a llamar
							 array (													// Parametros pasados a la funcion
							 		"user" => "yor.lopez",								//	User
							 		"password" => "12345678",							//	Pass
									"mail" => "yor.lopez@aldialogistica.com"			//	mail
									), 													
							"uri:$serverURL/$serverScript", 							// Namespace
						 	"uri:$serverURL/$serverScript/getToken" 					// SOAPAction
						);

// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
if ($cliente->fault) {
	echo '<b>Error: ';
	print_r($token);
	echo '</b>';
	} else {
		$error = $cliente->getError();
		if ($error) {
			echo '<b style="color: red">Error: ' . $error . '</b>';
			} else {
				echo 'Respuesta Token: '.$token;
			}
	}
 echo "<br><br>";
/**/
#############################################################################################################
// 1. Llamar a la funcion getRespuestaTotalData del servidor
$resultTotalData = $cliente->call(	"getRespuestaTotalData", 									// Funcion a llamar
							 array (													// Parametros pasados a la funcion
							 		"token" => $token,									//	Token
									), 													
							"uri:$serverURL/$serverScript", 							// namespace
						 	"uri:$serverURL/$serverScript/getRespuestaTotalData" 		// SOAPAction
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
 				echo 'Respuesta Total Data (15 Min.): '.$resultTotalData;
 				}
	}
/*
#############################################################################################################
 echo "<br /><br />";
// 2. Llamar a la funcion getRespuestaTotalData del servidor
$resultPlateRestDatesTimeToken = $cliente->call("getRespuestaFilterPlateNumberDateTime", 						// Funcion a llamar
							 					array (
														"token" => $token,									//	Token
														'plateAldiaLogistica' => 'XID177',					//	Number Plate
														'minorDateAldiaLogistica' => '2012-04-01 07:00:00',	//	Minor DateTime
														'majorDateAldiaLogistica' => '2012-04-01 07:15:00'	//	Major DateTime
														), 	// Parametros pasados a la funcion
												"uri:$serverURL/$serverScript", 									// namespace
												"uri:$serverURL/$serverScript/getRespuestaFilterPlateNumberDateTime"// SOAPAction
											);

// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
if ($cliente->fault) {
	echo '<b>Error: ';
	print_r($resultPlateRestDatesTimeToken);
	echo '</b>';
	} else {
		$error = $cliente->getError();
		if ($error) {
			echo '<b style="color: red">Error: ' . $error . '</b>';
			} else {
				echo 'Respuesta Placa y Resta Fechas: '.$resultPlateRestDatesTimeToken;
		}
	}
#############################################################################################################
 echo "<br /><br />";


// 3. Llamar a la funcion getRespuestaFilterPlateNumberDateTime del servidor
$resultPlateRestDateToken = $cliente->call(	"getRespuestaFilterPlateNumberDate", 							// Funcion a llamar
											 array (	
													"token" => $token,											//	Token
													'plateAldiaLogistica' => 'XID177',
													'dateAldiaLogistica' => '2012-04-01',
													),						// Parametros pasados a la funcion
											"uri:$serverURL/$serverScript", 									// namespace
											"uri:$serverURL/$serverScript/getRespuestaFilterPlateNumberDate"// SOAPAction
										);

// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
if ($cliente->fault) {
	echo '<b>Error: ';
	print_r($resultPlateRestDateToken);
	echo '</b>';
	} else {
		$error = $cliente->getError();
		if ($error) {
			echo '<b style="color: red">Error: ' . $error . '</b>';
			} else {
				echo 'Respuesta Placa Fecha: '.$resultPlateRestDatesTimeToken;
		}
	}
#############################################################################################################
echo '<br /> <br />';

// 4. Llamar a la funcion getRespuestaFilterAllPlatesNumbersDate del servidor
$getRespuestaAllFilterPlateNumberDate = $cliente->call("getRespuestaAllFilterPlateNumberDate", 								// Funcion a llamar
							 			array (
							 					"token" => $token,											//	Token	
						 						'dateAldiaLogistica' => '2012-04-01' 
												), 				// Parametros pasados a la funcion
										"uri:$serverURL/$serverScript", 									// namespace
						 				"uri:$serverURL/$serverScript/getRespuestaAllFilterPlateNumberDate"	// SOAPAction
										);

// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
if ($cliente->fault) {
	echo '<b>Error: ';
	print_r($resultPlatesDateToken);
	echo '</b>';
		} else {
			$error = $cliente->getError();
			if ($error) {
				echo '<b style="color: red">Error: ' . $error . '</b>';
				} else {
					echo 'Respuesta Todas las Placas a una Fecha: '.$getRespuestaAllFilterPlateNumberDate;
			}
	}
/**/
?>