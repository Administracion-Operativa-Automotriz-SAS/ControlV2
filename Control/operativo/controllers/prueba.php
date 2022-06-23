<?php

//soporte ext 2730 2732

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
//error_reporting(E_ERROR | E_PARSE | E_NOTICE);
error_reporting(E_ERROR);

include_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/DbConnect.php");

require($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/Requests-master/library/Requests.php");
verRespuestaElectronica();
function verRespuestaElectronica(){
		
		Requests::register_autoloader();
		$headers = array(
		"content-type" => "application/json",
		"Authorization" => "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE1OTAwOTQ3OTksInVzZXJfbmFtZSI6InNlcmdpb3VyYmluYUBhb2Fjb2xvbWJpYS5jb20iLCJhdXRob3JpdGllcyI6WyJTVVBQTElFUiJdLCJqdGkiOiJiMmFmYzE2My03YjlkLTQxYjctOGY5MC1lYjAwYWNmNTE3MTEiLCJjbGllbnRfaWQiOiJzZXJ2aWNlYXBwIiwic2NvcGUiOlsicmVhZCIsIndyaXRlIl19.sNZGSdgyrRjYRxYihSXRTFsvimgMYcGAGh1KlhTY2Io",
		"timeout" => 15
		);
		
		$options = array('timeout' => 15,
		                 );
		
		var_dump($headers);
		
		$data_send_documentos = array(
		"documentIdentificationType" => "31",
        "documentIdentificationNumber"=> "900174552",
        "companyBranchId" => 102579,
        "type" => "INVOICE",
        "number" => "",
        "uuid" => "",
        "issueDate" => array(
        "startDate" => "",
        "endDate" => ""
        ),
       "trackId" => 359369
	   );
	   
	   $requestDocuments_definitivo = Requests::post('https://facturaelectronicavp.ptesa.com.co/api/fe/v1/integration/emission/documents',$headers,json_encode($data_send_documentos),$options);
	   
	   var_dump($requestDocuments_definitivo);
	   
	   exit();
		
	}
	
	
	
?>