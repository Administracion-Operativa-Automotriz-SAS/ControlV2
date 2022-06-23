<?
require_once('includes/nusoap.php');
$soapclient = new soapclient('http://www.intercolombia.net/ws/server.php?wsdl');
echo $soapclient->call( 'hola' , array('name' => 'Mundo') );
?>