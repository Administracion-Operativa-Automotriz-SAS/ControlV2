<?php 
header("Content-Type: text/plain");
//set_time_limit(30000);
//ini_set('memory_limit','128999M');

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */


require_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/jSon_with_takes_NUEVO.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/Json2xml_test.php");



$jSon_with_takes = new json_fact_electronica_taxes();

$jSon_with_takes->totalImpuestos();

$jSon_with_takes->valor_facturas();

$jSon_with_takes->consecutive();

$jSon_with_takes->agregar_notas_facturas();

$jSon_with_takes->set_Extenciones();

$jSon_with_takes->set_InformacionClienteAdq();

//$jSon_with_takes->set_InformacionDeAdquiriente();

$jSon_with_takes->set_factItems();

$jSon_with_takes->medioDePago();



//echo json_encode($jSon_with_takes->json_object);

//$xml = Json2XML::generate_xml( json_decode($jSon_with_takes));

$xml_g = Json2xml::generate_xml($jSon_with_takes->json_object);



print_r($xml_g);


//print_r($xml);


