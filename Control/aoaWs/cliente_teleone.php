<?php
@set_time_limit(0);
@ini_set('default_socket_timeout', 2600);
require_once('includes/nusoap.php');
$serverURL = 'http://app.aoacolombia.com/Control/aoaWs';
$serverScript = 'server1.php';
// Crear un cliente de NuSOAP para el WebService
$cliente = new nusoap_client("$serverURL/$serverScript?wsdl", 'wsdl');

// Se pudo conectar?
$error = $cliente->getError();
if ($error) 
{
	echo '<pre style="color: red">' . $error  . '</pre>';
	echo '<p style="color:red;'>htmlspecialchars($cliente->getDebug(), ENT_QUOTES).'</p>';
	die();
}
echo "<br>Conexion exitosa.. <br>";
//  OBTENER UN TOKEN PARA TENER ACCESO 5 MINUTOS
$token = $cliente->call(	"ObtenerToken", 												// Funcion a llamar
							 array (	"usuario" => "teleonews","clave" => "Y1zpZJQHWcp7"), 													
							"uri:$serverURL/$serverScript", 							// Namespace
						 	"uri:$serverURL/$serverScript/ObtenerToken" 					// SOAPAction
							);

// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
if ($cliente->fault) {echo '<b>Error: ';print_r($token);echo '</b>';}
else {$error = $cliente->getError();if ($error)  echo '<b style="color: red">Error: ' . $error . '</b>'; else  echo 'Respuesta Token: '.$token;}
 echo "<br><br>";
/**/

#############################################################################################################
die();



// Insertar Siniestro
$Insercion = $cliente->call(  "InsertarSiniestro",                               
		array ( 
			"token" => $token,			//  Token
			"ciudad"=>'11001', // ciudad en codificacion dane
			"fecha"=>'2013-06-01',  // fecha en formato aaaa-mm-dd
			"numero"=>'19-2013-111-123456', // numero de siniestro alfanumerico
			"placa"=>'AAA123', // placa en formato XXX999
			"identificacion"=>'12345678', // numero sin comas ni puntos
			"poliza"=>'41111', // numero de poliza alfanumerico
			"nombre"=>'ARTURO QUINTERO RODRIGUEZ', // nombre del asegurado
			"telefono1"=>'7561510',  // telefono 1 puede ser fijo o celular 
			"telefono2"=>'3176562730', // telefono 2 puede ser fijo o celular
			"telefono3"=>'8647816', // telefono 3 puede ser fijo o celular
			"telefono4"=>'2020511', // telefono 4 puede ser fijo o celular
			"emailasegurado"=>'arturoquintero@aoacolombia.com', // email del asegurado
			"dias"=>'7', // dias de servicio por defecto 7 para perdidas parciales 21 para perdidas totales excepto Occidente pt=7
			"analista"=>'JUAN FERNANDEZ', // nombre del analista de Royal que guardo la informacion
			"emailanalista"=>'juan.fernandez@co.rsagroup.com' // em
			), 													
		"uri:$serverURL/$serverScript", 		//  namespace
		"uri:$serverURL/$serverScript/InsertarSiniestro"  //  SOAPAction
	);

	
	
// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
if ($cliente->fault) {echo '<b>Error: ';print_r($Insercion);echo '</b>';}
else {$error = $cliente->getError();if ($error)  echo '<b style="color: red">Error: ' . $error . '</b>'; else  echo 'Respuesta: '.$Insercion;}
?>