<?php

//soporte ext 2730 2732

session_start();

if($_SESSION == null)
{
	exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE | E_NOTICE);

include_once("DbConnect.php");

require('Requests-master/library/Requests.php');

class PTESA_ws
{
	function __construct(){

		$this->connect = new DbConnect();
		
		
		//constants
		
		
		$this->canal = "13";
		$this->token = "aoa"; 
		$this->Origen = "aoa.test";
		$this->Usuario = "b8624a016c65de0344e69d95882b8b6853d28dbbaaba65efcddc944cf5623c79";
		
		
		
		//registrar_factura
		//entregado por ptesa
		$this->claveTecnica  = "";
		$this->extensionArchvo = "";
		$this->formatoArchivo = "";
		$this->idDistribuidor = "";
		$this->idSoftwareFacturacion = "";
		
		//Información camara y comercio, debe entregarse en el formato de la camara de comercio
		$this->barrio  = "";
		$this->ciudad  = "";
		$this->codigoPais  = "CO";
			//entregado por ptesa
		$this->codigoSucursal="";
		$this->departamento="";
		$this->direccion="";
		$this->esEstablecimiento = false;
		$this->nombreEstablecimiento  = "";
		$this->numeroMatriculaMercantil = "";
		$this->pais  = "Colombia";
		$this->tipoEstablecimiento  = "E-02";
		$this->zonaPostal = "";
		
		$this->InfoCamara = array(
			"barrio"=>$this->barrio,
			"ciudad"=>$this->ciudad,
			"codigoPais"=>$this->codigoPais,
			"codigoSucursal"=>$this->codigoSucursal,
			"departamento"=>$this->departamento,
			"direccion"=>$this->direccion,
			"esEstablecimiento"=>$this->esEstablecimiento,
			"nombreEstablecimiento"=>$this->esEstablecimiento,
			"numeroMatriculaMercantil"=>$this->numeroMatriculaMercantil,
			"pais"=>$this->pais,
			"tipoEstablecimiento"=>$this->tipoEstablecimiento,
			"zonaPostal"=>$this->zonaPostal
		);
		
		
		//Informaciòn del rut
		//InformacionRut
		
		$this->rutbarrio ="";
		$this->rutciudad="";
		$this->rutcorreoElectronico="";
		$this->rutDepartamento="";
		$this->rutdigitoVerificacion="";
		$this->rutdireccion="";
		$this->rutnit="";
		$this->rutnombreComercial="";
		$this->rutpais="";
		$this->rutprimerApellido="";
		$this->rutprimerNombre = "";
		$this->rutraszonSocial="";
		$this->rutresponsabilidades="";
		$this->rutsegundoApellido="";
		$this->rutsegundoNombre="";
		$this->ruttipoContribuyente=2;
		$this->ruttipoRegimen=0;
		$this->usuarioAduanero="";
		
		
		$this->InformacionRut = array(
			"barrio"=>$this->rutbarrio,
			"ciudad"=>$this->rutciudad,
			
		);
		
		$this->acceso = array(
			"canal"=>$this->canal,
			"token"=>$this->token,
			"origen"=>$this->origen,
			"usuario"=>$this->usuario
		);
		
	}
	
	public function registro_obligado_factura($adjuntarPdf=false,$correoDistribucion="",
	$telefonoCelular ="",$usaSucursales=false,$tipoIdentificacion=12,$numeroIdentificacion="",$tipoDocumento=1)
	{
		Requests::register_autoloader();
		
		$data = array(
			"adjuntarPdf"=>$adjuntarPdf,
			"correoDistribucion"=>$correoDistribucion,
			"telefonoCelular"=>$telefonoCelular,
			"usaSucursales"=>$usaSucursales,
			"tipoIdentificacion"=>$tipoIdentificacion,
			"numeroIdentificacion"=>$numeroIdentificacion,
			"claveTecnica"=>$this->claveTecnica,
			"extensionArchvo"=>$this->extensionArchvo,
			"formatoArchivo"=>$this->formatoArchivo,
			"idDistribuidor"=>$this->idDistribuidor,
			"idSoftwareFacturacion"=>$this->idSoftwareFacturacion,
			"infoCamara"=>$this->InfoCamara,
		);
		
		if(in_array($tipoDocumento,array(2,3)))
		{ 	
			//Con tipo $tipoDocumento se sabe si es factura nota debito o credito si es nota credito o debito existen
			$data = array_merge($data,array("NumeracionInicial"=>$NumeracionInicial,
					"numeracionFinal"=>$numeracionFinal,
					"prefijo"=>$numeracionFinal
				));	
		}
		
		// Now let's make a request!
		$request = Requests::post('http://serviciospruebas.ptesa.com/registroobligado/', array(), $data);
		$response = $request->body;
		//response trae  codigoRespuesta , mensajeRespuesta , codigoVerificacion , usuarioFE
		echo $response;
	}
	
	public function cambia_estado_servicio($codigoVerificacion="",
		$usuarioFE="",$cufe="",$estado="",$idMotivoRechazo="",$tipoDocumento=1
	)
	{
		Requests::register_autoloader();
		
		$data = array(
			"codigoVerificacion"=>$codigoVerificacion,
			"usuarioFE"=>$usuarioFE,
			"cufe"=>$cufe,
			"estado"=>$estado,
			"idMotivoRechazo"=>$idMotivoRechazo,
			"tipoDocumento"=>$tipoDocumento
		);
		
		$request = Requests::post('http://serviciospruebas.ptesa.com:90/cambiar', array(), $data);
		$response = $request->body;
		//response trae  codigoRespuesta , mensajeRespuesta 
		echo $response;
	}
	
	public function verifica_estado_servicio($codigoVerificacion="",
		$usuarioFE="",$cufe="",$estado="",$idMotivoRechazo="",$tipoDocumento=1
	)
	{
		Requests::register_autoloader();
		
		$data = array(
			"codigoVerificacion"=>$codigoVerificacion,
			"usuarioFE"=>$usuarioFE,
			"cufe"=>$cufe,
			"estado"=>$estado,
			"idMotivoRechazo"=>$idMotivoRechazo,
			"tipoDocumento"=>$tipoDocumento
		);
		
		$request = Requests::post('http://serviciospruebas.ptesa.com:90/estado', array(), $data);
		$response = $request->body;
		//response trae  codigoRespuesta , mensajeRespuesta , estado , idMotivoRechazo , numeroDocumento
		echo $response;
	}
	
	
	public function registro_documento_obligatorio($codigoVerificacion="",
		$usuarioFE = "",
		$cufe = "",
		$tipoDocumento = "",
		$numeroDocumento = ""
	)
	{
		//mensajebase64 archivo 
		Requests::register_autoloader();
		
		$data = array(
			"codigoVerificacion"=>$codigoVerificacion,
			"usuarioFE"=>$usuarioFE,
			"mensajeBase64"=>$cufe,
			"tipoDocumento"=>$tipoDocumento,
			"numeroDocumento"=>$numeroDocumento			
		);
		
		if(in_array($tipoDocumento,array(2,3)))
		{
			$data = array_merge($data,array("numeroDocumentoAsociado" => $numeroDocumentoAsociado));
		}
		
		$request = Requests::post('http://serviciospruebas.ptesa.com:90/registrodocumento', array(), $data);
		$response = $request->body;
		//response trae  codigoRespuesta , mensajeRespuesta , contenidoCodigoQR , cufe , numeroDocumentoGenreado, ublToString
		echo $response;
	}
	
	
	
	
	public function consultar_representacion_grafica($codigoVerificacion = "",
		$usuarioFE = "",
		$cufe = "",
		$idDistribuidor = "",
		$tipoDocumento = ""
	)
	{
		Requests::register_autoloader();
		
		$data = array(
			"codigoVerificacion"=>$codigoVerificacion,
			"usuarioFE"=>$usuarioFE,
			"cufe"=>$cufe,
			"idDistribuidor"=>$idDistribuidor,		
			"tipoDocumento"=>$tipoDocumento
		);
		$request = Requests::post('http://serviciospruebas.ptesa.com:90/rg', array(), $data);
		$response = $request->body;
		echo $response;
		//response trae  codigoRespuesta , mensajeRespuesta , representacion
	}
	
	
	public function test()
	{
		Requests::register_autoloader();		
		$request = Requests::get('http://httpbin.org/get', array('Accept' => 'application/json'));		
		$response = $request->body;
		echo $response;
	}
	
}	
















 ?>