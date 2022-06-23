<?php

//soporte ext 2730 2732

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
//error_reporting(E_ERROR | E_PARSE | E_NOTICE);
error_reporting(E_ERROR);

include_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/DbConnect.php");

require($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/Requests-master/library/Requests.php");


	
	function registro_documento_obligatorio_test5()
	{
		
		Requests::register_autoloader();
		
		$data_to_sendToken = array("username"=>"s.ospina@valps.com",
		                   "password"=>"P4w5W0rT",
						   "grant_type"=>"password");
						   
						   
						   
		$requestToken = Requests::post('https://secfevalpruebas.ptesa.com.co:8443/api/fe/v1/security/oauth/token',
		array('content-type'=>'application/x-www-form-urlencoded'),$data_to_sendToken);
		
		
		
		$token =  json_decode($requestToken->body);
		
		var_dump($token);
		
		exit();
		
		$tokenDefinitivo = "Bearer ".$token->access_token;
		
		return 	$tokenDefinitivo;
	   
	}
	
	
	function representacion_grafica()
	{
		$token_id =  registro_documento_obligatorio_test();
		
		
		
	
		Requests::register_autoloader();
		
		$data_to_sendToken = array(
		"documentType"=>"1",
		"documentIdentification"=>"IS11346",
		"documentFileType"=>"1",
		"uuid"=>"059c51702fbb51c9ce65241b7c869c729faeaeae8d248607a7db73d4c161ffb459e0abdb15f29cc537fb6f699c1cddbe",
		"userType"=>"CUSTOMER",
	    "companyBranchId"=>"102579"
		
		
		);
		
        $tokenDefinitivo = $token_id;
		
		$headers = array(
		"content-type" => "application/json",
		"username" => $tokenDefinitivo
		);
		
					   
		return Requests::post('https://secfevalpruebas.ptesa.com.co:8443/api/fe/v1/emission/detail/documents/files',
		array('content-type'=>'application/json',"Authorization" => $tokenDefinitivo),$data_to_sendToken);
		
	
		
	   
	}
	
    
	
	
	 function registro_documento_obligatorio_test2()
	{
		
		Requests::register_autoloader();
		
		$data_to_sendToken = array("username"=>"sergiourbina@aoacolombia.com",
		                   "password"=>"P4w5W0rT",
						   "grant_type"=>"password");
						   
						   
						   
		$requestToken = Requests::post('https://facturaelectronicavp.ptesa.com.co/api/fe/v1/security/oauth/token',
		array('content-type'=>'application/x-www-form-urlencoded'),$data_to_sendToken);
		
		
		$token =  json_decode($requestToken->body);
		
		$tokenDefinitivo = "Bearer ".$token->access_token;
		
		
		$data_to_send = array(
		"documentType"=>"1",
		"documentIdentification"=>"IS11346",
		"documentFileType"=>"",
		"uuid"=>"059c51702fbb51c9ce65241b7c869c729faeaeae8d248607a7db73d4c161ffb459e0abdb15f29cc537fb6f699c1cddbe",
		"userType"=>"CUSTOMER",
	    "companyBranchId"=>"102579");
		
		
		
		
		
		$headers = array(
		"content-type" => "application/json",
		"username" => $tokenDefinitivo
		);
		
		
		$requestEmission = Requests::post('https://facturaelectronicavp.ptesa.com.co/api/fe/v1/integration/emission/documents',$headers,json_encode($data_to_send));
		
		sleep(10);
		
		
		return $requestEmission;
	}
		
	
	
	
	
	
 function documento__test()
	{
		
		Requests::register_autoloader();
		
		$data_to_sendToken = array("username"=>"sergiourbina@aoacolombia.com",
		                   "password"=>"P4w5W0rT",
						   "grant_type"=>"password");
						   
						   
						   
		$requestToken = Requests::post('https://facturaelectronicavp.ptesa.com.co/api/fe/v1/security/oauth/token',
		array('content-type'=>'application/x-www-form-urlencoded'),$data_to_sendToken);
		
		$token =  json_decode($requestToken->body);
		
		$tokenDefinitivo = "Bearer ".$token->access_token;
		
		
		$data_to_send = array(
            	"documentType"=>"1",
		"documentIdentification"=>"IS11346",
		"documentFileType"=>"1",
		"uuid"=>"059c51702fbb51c9ce65241b7c869c729faeaeae8d248607a7db73d4c161ffb459e0abdb15f29cc537fb6f699c1cddbe",
		"userType"=>"CUSTOMER",
	    "companyBranchId"=>"102579"
			);
		//var_dump($data_to_send);
		//exit();
		
		$options = array('timeout' => 80,
		                 );
		
		$headers = array(
		"content-type" => "application/json'",
		"username"=>"sergiourbina@aoacolombia.com",
		"Authorization" => $tokenDefinitivo
		);
		
		$requestEmission = Requests::post('https://secfevalpruebas.ptesa.com.co:8443/api/fe/v1/emission/detail/documents/files',$headers,json_encode($data_to_send),$options);
		//var_dump($requestEmission);
		
		sleep(6);
		

	   var_dump(json_decode($requestEmission->body));
	   
	}

	
	
	
	 function consultar_representacion_grafica()
	{
		Requests::register_autoloader();
		
	    $canal = "5";
		
		$token = "86efdc5a55d7a67950122da2346adc4bbcada56c036d505fabe8ea403618ec26"; 
		$origen = "prod.aoa";
		$usuario = "aoa";
		
		$codigoVerificacion = "e20952761855289dd691e587aa30862b"; 
		$usuarioFE = "AOA";
		
		
		$idDistribuidor = 1;
		
		
		
		$acceso = array(
			"canal"=>$canal,
			"token"=>$token,
			"origen"=>$origen,
			"usuario"=>$usuario
		);
		
		$data = array(
            	"documentType"=>"1",
		"documentIdentification"=>"IS11346",
		"documentFileType"=>"1",
		"uuid"=>"059c51702fbb51c9ce65241b7c869c729faeaeae8d248607a7db73d4c161ffb459e0abdb15f29cc537fb6f699c1cddbe",
		"userType"=>"CUSTOMER",
	    "companyBranchId"=>"102579"
			);
		
		$data_to_send = json_encode(array("rg"=>array("acceso"=>$acceso,"data"=>$data)));	

		//echo $data_to_send;	
		
		
		//End point pruebas
		
		//$request = Requests::post('http://serviciospruebas.ptesa.com:90/rg/consulta', array('content-type'=>'application/json'), $data_to_send);
		
	    //End point produccion
		
		$request = Requests::post('https://secfevalpruebas.ptesa.com.co:8443/api/fe/v1/emission/detail/documents/files', array('content-type'=>'application/json'), $data_to_send);
		
		$response = json_decode($request->body);

		print_r($response);
		
		//response trae  codigoRespuesta , mensajeRespuesta , representacion
	}
	
	
	
	
	
	
	
	
	
	
	registro_documento_obligatorio_test5();
	
	
	
?>