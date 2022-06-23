<?php 
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ERROR | E_PARSE | E_NOTICE);
	
	
	require_once("controllers/PTESA_ws.php");
	
		//Crear factura con <cbc:ID>FE10</cbc:ID>

	$ptesa_service = new PTESA_ws();	
	
	
	header('Content-Type: text/plain');
	
	include("factura_xml/aoa_json_backup.php");
	
	include("factura_xml/Json2xml.php");
	
	$json_t = json_decode($xmlstr);	
	
	$fe_Invoice_key = "fe:Invoice";
	
	$cbc_ID_key = "cbc:ID";
	
	$json_t->$fe_Invoice_key->$cbc_ID_key = "FE29";	

	$xml_g = Json2xml::generate_xml($json_t);	
	
	
	//header('Content-disposition: attachment; filename="newfile.xml"');	
	//Header('Content-type: text/xml');
	//echo $xml_g;
	
	//echo "\n\n";
	
	$ptesa_service->registro_documento_obligatorio(base64_encode($xml_g),1,$json_t->$fe_Invoice_key->$cbc_ID_key);
	
	//$ptesa_service->verifica_estado_servicio("470ed2914e610b7e12fb0dfbc2f91548a5b31706",1);
	
	//$ptesa_service->consultar_representacion_grafica("470ed2914e610b7e12fb0dfbc2f91548a5b31706",1);
	
	//$ptesa_service->cambia_estado_servicio("470ed2914e610b7e12fb0dfbc2f91548a5b31706",2,8,1);
	
	//Flujo del xml
	
	//1. Capturar correo del adquiriente  y determinar su tiene rut o camara de comercio.
	//2. Pasar a la información del documento, y el valor letras y números.
	//3. Información de la persona a la que se factura.
	//4. Verificar si el cliente es empresa para poner razón social y volver a poner el nombre del cliente en otras etiquetas.
	//5. Poner los impuestos.
	//6. Poner los items de la factura.
	
	
?>