<?php

//soporte ext 2730 2732

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
//error_reporting(E_ERROR | E_PARSE | E_NOTICE);
error_reporting(E_ERROR);

include_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/DbConnect.php");

require($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/Requests-master/library/Requests.php");

class PTESA_ws
{
	function __construct(){

		$this->connect = new DbConnect();
		
		//cufe --> Codigo unico de facturación electronica.
		
		//constants
		
		
		
		//Pruebas
		
		/*
		$this->canal = "13";
		$this->token = "b8624a016c65de0344e69d95882b8b6853d28dbbaaba65efcddc944cf5623c79"; 
		$this->origen = "aoa";
		$this->usuario = "aoa.test";
		
		$this->codigoVerificacion = "e20952761855289dd691e587aa30862b";
		$this->usuarioFE = "AOA";
		*/
		
		//Producción
		
		$this->canal = "5";
		
		$this->token = "86efdc5a55d7a67950122da2346adc4bbcada56c036d505fabe8ea403618ec26"; 
		$this->origen = "prod.aoa";
		$this->usuario = "aoa";
		
		$this->codigoVerificacion = "e20952761855289dd691e587aa30862b"; 
		$this->usuarioFE = "AOA";
		
		
		$this->idDistribuidor = 1;
		
		
		
		$this->acceso = array(
			"canal"=>$this->canal,
			"token"=>$this->token,
			"origen"=>$this->origen,
			"usuario"=>$this->usuario
		);
		
	}
	
	public function registro_documento_obligatorio($mensajeBase64,$tipoDocumento,$numeroDocumento,$numeroDocumentoAsociado="")
	{
		
		/*Tipo documento 1 factura 2 nota credito 3 nota debito*/
		//mensajebase64 archivo 
		
		Requests::register_autoloader();
		
		$data = array(
			"codigoVerificacion"=>$this->codigoVerificacion,
			"usuarioFE"=>$this->usuarioFE,
			"mensajeBase64"=>$mensajeBase64,
			"tipoDocumento"=>$tipoDocumento,
			"numeroDocumento"=>$numeroDocumento			
		);
		
		//numeroDocumento es el consecutivo de la factura
		
		if(in_array($tipoDocumento,array(2,3)))
		{
			//numerodocumentoasociado se llena cuando es una nota credito
			$data = array_merge($data,array("numeroDocumentoAsociado" => $numeroDocumentoAsociado));
		}
		
		$data_to_send = json_encode(array("documento"=>array("acceso"=>$this->acceso,"data"=>$data)));
		
		//print_r($data_to_send);
		
		//exit;
		
		//End point pruebas
		
		//return $request = Requests::post('http://serviciospruebas.ptesa.com:90/registrodocumento/documentofirma/', array('content-type'=>'application/json'),$data_to_send);
		
		
		//End point producción
		
		return $request = Requests::post('https://facturaelectronica.ptesa.com:8443/services/registrodocumento/documentofirma/', array('content-type'=>'application/json'),$data_to_send);
		

		 
		//$response = $request->body;
		
		//response trae  codigoRespuesta , mensajeRespuesta , contenidoCodigoQR , cufe , numeroDocumentoGenreado, ublToString
		//echo $response;
		
		//{"respuesta":{"codigoRespuesta":"01","mensajeRespuesta":"error.procesando.tx"}} Cuando hay un error
		//{"respuesta":{"codigoRespuesta":12,"mensajeRespuesta":"error.documento.ya.enviado"}}
		//{"respuesta":{"codigoRespuesta":14,"mensajeRespuesta":"error.estructura"}}
		//{"respuesta":{"codigoRespuesta":15,"mensajeRespuesta":"error.mensaje.datos"}}
		//{"respuesta":{"codigoRespuesta":01,"mensajeRespuesta":"java.lang.RuntimeException:row1.payload can't be empty"}}	
			
		//string de respuesta	
		//{"respuesta":{"codigoRespuesta":"00","mensajeRespuesta":"exitoso","contenidoCodigoQR":"NumFac:FE28\nFecFac:20180801170505\nNitFac:900174552\nDocAdq:00000000\nValFac:30000.00\nValIva:4300.00\nValOtroIm:2400.00\nValFacIm:36700.00\nCUFE:f508c26b697d4e002e14e30e4fa1d376b0483476","cufe":"f508c26b697d4e002e14e30e4fa1d376b0483476","numeroDocumentoGenerado":"FE28","ublToString":""}}
	
	}
	
	public function registro_documento_obligatorio_test($mensajeBase64,$tipoDocumento,$numeroDocumento,$numeroDocumentoAsociado="")
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
            "content"=>$mensajeBase64,
			"supplierId"=>100064,
			"format"=>"xml");
		//var_dump($data_to_send);
		//exit();
		
		$headers = array(
		"content-type" => "application/json",
		"Authorization" => $tokenDefinitivo
		);
		
		$requestEmission = Requests::post('https://facturaelectronicavp.ptesa.com.co/api/fe/v1/integration/emission',$headers,json_encode($data_to_send));
		//var_dump($requestEmission);
		
		sleep(60);
		
		$trackId =  json_decode($requestEmission->body);
		
		$data_send_trackIds = array("trackId"=>$trackId->trackId);
		
		$requestTrackId = Requests::post('https://facturaelectronicavp.ptesa.com.co/api/fe/v1/integration/emission/trackId',$headers,json_encode($data_send_trackIds));
		//var_dump($requestTrackId);
		
		$trackIdDisponible = json_decode($requestTrackId->body);
		
		
		$options = array('timeout' => 150,
		                 );
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
       "trackId" => $trackIdDisponible->trackId
	   );
	   
	   $requestDocuments_definitivo = Requests::post('https://facturaelectronicavp.ptesa.com.co/api/fe/v1/integration/emission/documents',$headers,json_encode($data_send_documentos),$options);
	   
	   return array("response" => $requestDocuments_definitivo, "trackId" => $trackIdDisponible); 
	   
	}
	
	
	public function registro_documento_obligatorio_test_dos($mensajeBase64,$tipoDocumento,$numeroDocumento,$numeroDocumentoAsociado="")
	{
		
		Requests::register_autoloader();
		
		$data_to_sendToken = array("username"=>"sergiourbina@aoacolombia.com",
		                   "password"=>"P4w5W0rT",
						   "grant_type"=>"password");
						   
						   
						   
		$requestToken = Requests::post('https://secfevalpruebas.ptesa.com.co:8443/api/fe/v1/security/oauth/token',
		array('content-type'=>'application/x-www-form-urlencoded'),$data_to_sendToken);
		
		$token =  json_decode($requestToken->body);
		
		$tokenDefinitivo = "Bearer ".$token->access_token;
		
		
		$data_to_send = array(
            "content"=>$mensajeBase64,
			"supplierId"=>100064,
			"format"=>"xml");
		
		
		$headers = array(
		"content-type" => "application/json",
		"Authorization" => $tokenDefinitivo
		);
		
		//var_dump($headers);
		
		$requestEmission = Requests::post('https://secfevalpruebas.ptesa.com.co:8443/api/fe/v1/integration/emission',$headers,json_encode($data_to_send));
		//var_dump($requestEmission);
		
		sleep(10);
		
		$trackId =  json_decode($requestEmission->body);
		
		$data_send_trackIds = array("trackId"=>$trackId->trackId);
		
		$requestTrackId = Requests::post('https://secfevalpruebas.ptesa.com.co:8443/api/fe/v1/integration/emission/trackId',$headers,json_encode($data_send_trackIds));
		//var_dump($requestTrackId);
		sleep(10);
		
		$trackIdDisponible = json_decode($requestTrackId->body);
		
		
		
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
       "trackId" => $trackIdDisponible->trackId
	   );
	   
	   
	   //var_dump($data_send_documentos);
	   $requestDocuments_definitivo = Requests::post('https://secfevalpruebas.ptesa.com.co:8443/api/fe/v1/integration/emission/documents',$headers,json_encode($data_send_documentos));
	   
	   //var_dump($requestDocuments_definitivo);
	   //var_dump($requestDocuments);
	   //exit();
	   
	   sleep(10);
	   
	   //$requestDocuments_definitivo = Requests::post('https://facturaelectronicavp.ptesa.com.co/api/fe/v1/integration/emission/documents',$headers,json_encode($data_send_documentos));
	   
	   return array("response" => $requestDocuments_definitivo, "trackId" => $trackIdDisponible); 
	   
	}
		
	public function cambia_estado_servicio($cufe,$estado,$idMotivoRechazo,$tipoDocumento)
	{
		/* tipoDocumento 1 factura 2 nota credito 3 nota debito*/
		
		/*0: estado
			1: entregado
			2: aceptado
			3: rechazado
			5: visualizado
			*/
			
		$sql = "select * from aoacol_administra.conceptos_rechazo_facturacion_electronica where id = ".$idMotivoRechazo;	
		$result = $this->connect->query($sql);
		$concepto_rechazo = mysql_fetch_object($result);
		
		
		
		Requests::register_autoloader();
		
		$data = array(			
			"codigoVerificacion"=>$this->codigoVerificacion,
			"usuarioFE"=>$this->usuarioFE,
			"cufe"=>$cufe,
			"estado"=>$estado,
			"idMotivoRechazo"=>$idMotivoRechazo,
			"motivoRechazo"=>$concepto_rechazo->concepto,
			"tipoDocumento"=>$tipoDocumento
		);
		
		$data_to_send = json_encode(array("estado"=>array("acceso"=>$this->acceso,"data"=>$data)));
		
		//echo $data_to_send;
		//echo "\n\n";
		
		//End point prueba
		
		 return $request = Requests::post('http://serviciospruebas.ptesa.com:90/estado/cambiar',array('content-type'=>'application/json'), $data_to_send);
		
		
		//$request = Requests::post('https://serviciospruebas.ptesa.com.co/estado/cambiar',array('content-type'=>'application/json'), $data_to_send);
		
		//print_r($request);
		
		//{"respuesta":{"codigoRespuesta":"08","mensajeRespuesta":"NOK"}} es el mismo estado
		//{"respuesta":{"codigoRespuesta":"00","mensajeRespuesta":"OK"}} ok
		
		//$response = $request->body;
		//response trae  codigoRespuesta , mensajeRespuesta 
		//echo $response; 
	}
	
	public function verifica_estado_servicio($cufe,$tipoDocumento)
	{
		Requests::register_autoloader();
		
		$data = array(
			"codigoVerificacion"=>$this->codigoVerificacion, 
			"usuarioFE"=>$this->usuarioFE,
			"cufe"=>$cufe,			
			"tipoDocumento"=>$tipoDocumento
		);
		
		//$data_to_send = array("acceso"=>$this->acceso,"data"=>$data);
		$data_to_send = json_encode(array("consultar"=>array("acceso"=>$this->acceso,"data"=>$data)));
		
		//End point prueba
		
		//$request = Requests::post('http://serviciospruebas.ptesa.com:90/estado/consultar', array('content-type'=>'application/json'), $data_to_send);
		
		//End point producción
		
		//print_r($data_to_send);
		
		//return $request = Requests::post('http://facturaelectronica.ptesa.com/RegistroEstadoService', array('content-type'=>'application/json'), $data_to_send);
		
		return $request = Requests::post('https://facturaelectronica.ptesa.com:8443/estado/consultar', array('content-type'=>'application/json'), $data_to_send);
		
		//print_r($request);
		
		
		//$response = $request->body;
		//response trae  codigoRespuesta , mensajeRespuesta , estado , idMotivoRechazo , numeroDocumento
		//echo $response;
		
		//{"respuesta":{"codigoRespuesta":"00","mensajeRespuesta":"OK","estado":1,"motivoRechazo":"","numeroDocumento":"FE25"}}
	}

	
	
	public function consultar_representacion_grafica($cufe,$tipoDocumento,$ajax=false)
	{
		Requests::register_autoloader();
		
		$this->idDistribuidor = 2;
		
		$data = array(
			"codigoVerificacion"=>$this->codigoVerificacion,
			"usuarioFE"=>$this->usuarioFE,
			"cufe"=>$cufe,
			"idDistribuidor"=>$this->idDistribuidor,		
			"tipoDocumento"=>$tipoDocumento
		);
		
		$data_to_send = json_encode(array("rg"=>array("acceso"=>$this->acceso,"data"=>$data)));	

		//echo $data_to_send;	
		
		
		//End point pruebas
		
		//$request = Requests::post('http://serviciospruebas.ptesa.com:90/rg/consulta', array('content-type'=>'application/json'), $data_to_send);
		
	    //End point produccion
		
		$request = Requests::post('https://facturaelectronica.ptesa.com:8443/services/rg/consulta', array('content-type'=>'application/json'), $data_to_send);
		
		
		$response = json_decode($request->body);

		//print_r($response);
		
		if($response->respuesta->codigoRespuesta != 0)
		{
			echo json_encode(array("status"=>"ERROR","desc"=>"No se puede consultar la representación gráfica"));
			exit;
		}
		
		if($ajax)
		{
			return $response->respuesta->representacion;			
		}
		else
		{
			header('Content-Type: application/pdf');
			echo base64_decode($response->respuesta->representacion);
		}
		//response trae  codigoRespuesta , mensajeRespuesta , representacion
	}
	
	
	
	public function test_new_xml($base64xml,$consecutive)
	{
		$this->canal = "13";
		$this->token = "b8624a016c65de0344e69d95882b8b6853d28dbbaaba65efcddc944cf5623c79"; 
		$this->origen = "aoa";
		$this->usuario = "aoa.test";
		
		$this->codigoVerificacion = "e20952761855289dd691e587aa30862b";
		$this->usuarioFE = "AOA";
		
		
		$this->acceso = array(
			"canal"=>$this->canal,
			"token"=>$this->token,
			"origen"=>$this->origen,
			"usuario"=>$this->usuario
		);
		
		$response = $this->registro_documento_obligatorio_test($base64xml,1,$consecutive);
		
		return $response;
	}
	
	public function registro_documento_obligatorio_test_2($mensajeBase64,$tipoDocumento,$numeroDocumento,$numeroDocumentoAsociado="")
	{
		$this->canal = "13";
		$this->token = "b8624a016c65de0344e69d95882b8b6853d28dbbaaba65efcddc944cf5623c79"; 
		$this->origen = "aoa";
		$this->usuario = "aoa.test";
		
		$this->codigoVerificacion = "e20952761855289dd691e587aa30862b";
		$this->usuarioFE = "AOA";
		
		
		$this->acceso = array(
			"canal"=>$this->canal,
			"token"=>$this->token,
			"origen"=>$this->origen,
			"usuario"=>$this->usuario
		);
		
		
		
		
		/*Tipo documento 1 factura 2 nota credito 3 nota debito*/
		//mensajebase64 archivo 
		
		Requests::register_autoloader();
		
		$data = array(
			"codigoVerificacion"=>$this->codigoVerificacion,
			"usuarioFE"=>$this->usuarioFE,
			"mensajeBase64"=>$mensajeBase64,
			"tipoDocumento"=>$tipoDocumento,
			"numeroDocumento"=>$numeroDocumento			
		);
		
		//numeroDocumento es el consecutivo de la factura
		
		if(in_array($tipoDocumento,array(2,3)))
		{
			//numerodocumentoasociado se llena cuando es una nota credito
			$data = array_merge($data,array("numeroDocumentoAsociado" => $numeroDocumentoAsociado));
		}
		
		$data_to_send = json_encode(array("documento"=>array("acceso"=>$this->acceso,"data"=>$data)));
		
		//print_r($data_to_send);
		
		//exit;
		
		//End point pruebas
		
		
		return $request = Requests::post('http://serviciospruebas.ptesa.com:90/registrodocumento/documentofirma/', array('content-type'=>'application/json'),$data_to_send);
		
		
		//End point producción
		
		//return $request = Requests::post('https://facturaelectronica.ptesa.com:8443/services/registrodocumento/documentofirma/', array('content-type'=>'application/json'),$data_to_send);
		

		 
		//$response = $request->body;
		
		//response trae  codigoRespuesta , mensajeRespuesta , contenidoCodigoQR , cufe , numeroDocumentoGenreado, ublToString
		//echo $response;
		
		//{"respuesta":{"codigoRespuesta":"01","mensajeRespuesta":"error.procesando.tx"}} Cuando hay un error
		//{"respuesta":{"codigoRespuesta":12,"mensajeRespuesta":"error.documento.ya.enviado"}}
		//{"respuesta":{"codigoRespuesta":14,"mensajeRespuesta":"error.estructura"}}
		//{"respuesta":{"codigoRespuesta":15,"mensajeRespuesta":"error.mensaje.datos"}}
		//{"respuesta":{"codigoRespuesta":01,"mensajeRespuesta":"java.lang.RuntimeException:row1.payload can't be empty"}}	
			
		//string de respuesta	
		//{"respuesta":{"codigoRespuesta":"00","mensajeRespuesta":"exitoso","contenidoCodigoQR":"NumFac:FE28\nFecFac:20180801170505\nNitFac:900174552\nDocAdq:00000000\nValFac:30000.00\nValIva:4300.00\nValOtroIm:2400.00\nValFacIm:36700.00\nCUFE:f508c26b697d4e002e14e30e4fa1d376b0483476","cufe":"f508c26b697d4e002e14e30e4fa1d376b0483476","numeroDocumentoGenerado":"FE28","ublToString":""}}
	
	}
	
	
	
}	
?>