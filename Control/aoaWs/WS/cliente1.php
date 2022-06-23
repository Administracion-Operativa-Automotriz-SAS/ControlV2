<?php
// Incluimos la biblioteca de NuSOAP (la misma que hemos incluido en el servidor, ver la ruta que le especificamos)
require_once('includes/nusoap.php');
// Crear un cliente apuntando al script del servidor (Creado con WSDL) - 
// Las proximas 3 lineas son de configuracion, y debemos asignarlas a nuestros parametros
$serverURL = 'http://www.intercolombia.net/ws/';
$serverScript = 'server1.php';
$metodoALlamar = 'getRespuesta';

// Crear un cliente de NuSOAP para el WebService
$cliente = new nusoap_client("$serverURL/$serverScript?wsdl", 'wsdl');
// Se pudo conectar?
$error = $cliente->getError();
if ($error) {
 echo '<pre style="color: red">' . $error  . '</pre>';
 echo '<p style="color:red;'>htmlspecialchars($cliente->getDebug(), ENT_QUOTES).'</p>';
 die();
}

// 1. Llamar a la funcion getRespuesta del servidor
$result = $cliente->call(
 "$metodoALlamar", // Funcion a llamar
 array('parametro1' => 'Arturo','parametro2' => 'Paulo'), // Parametros pasados a la funcion
 "uri:$serverURL/$serverScript", // namespace
 "uri:$serverURL/$serverScript/$metodoALlamar" // SOAPAction
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

$Resultado2=$cliente->call('getLista',array(),"uri:$serverURL/$serverScript", "uri:$serverURL/$serverScript/$metodoALlamar");
 echo "<br><br>";
print_r($Resultado2);
 
$Resultado3=$cliente->call('obtener_placas',array(),"uri:$serverURL/$serverScript", "uri:$serverURL/$serverScript/$metodoALlamar");
echo "<br><br>";
print_r($Resultado3);echo "<br><br>";
echo $Resultado3[0]['placa'];echo "<br><br>";
echo $Resultado3[0]['datosplaca']['latitud'];
?>