<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE | E_NOTICE);
//error_reporting(E_ERROR);

if(!isset($_SESSION))
{
	session_start();
	if(!isset($_SESSION["Nombre"]))
	{
		echo "No hay sesión";
		exit;
	}		
}

	



include_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/DbConnect.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/PTESA_ws_test.php");

//json factura   se cambio  jSon_with_takes_NUEVO_james.php
include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/aoa_json.php");

include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/jSon_with_takes_NUEVO.php");


//json nota credito   aoa_NC_json_with_taxes_james.php
include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/aoa_NC_json.php");

include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/aoa_NC_json_with_taxes.php");


//json nota debito
include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/aoa_ND_json.php");
	
	
include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/Json2xml_test.php");

include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/NumeroALetras.php");




/* $fact_electronica = new factura_electronica(null);
$fact_electronica->test();

echo "here"; */

class factura_electronica
{
	
	
	function __construct($factura,$ext_table="")
	{
		$this->connect = new DbConnect();
		$this->factura = $factura;
		$this->ext_table = $ext_table;
		$this->fact_obj = new json_fact_electronica_taxes();
		$this->ptesa_service = new PTESA_ws();
		//Hay que mandar la factura como un objeto
		set_time_limit (30);
	}
	
	function set_nota_credito($nota_credito)
	{
		$this->nota_credito = $nota_credito;
		$this->notac_obj = new json_nota_credito();		
		
	}
	
	
	public function generar_factura_electronica($amount_to_plus=1)
	{
		
		
		/*if($amount_to_plus > 1)
		{
			sleep(7);
		}*/
		
		$factura = $this->factura;
		
		$sql = "select * from fact_electronica_seguimiento where factura = '".$factura->id."' and estado = 1 ";
		
		//echo $sql;
		
		$already_exist = $this->connect->query($sql);
		
		$object_already_exist = $this->connect->convert_object($already_exist);
		
		//print_r($object_already_exist);
		
		if($object_already_exist)
		{
			echo json_encode(array("status"=>"OK","desc"=>"Ya tiene una factura eléctronica aceptada"));
			//exit;
			return false;
		}		
		
		
		header('Content-Type: text/html; charset=utf-8');
		header('Content-Type: text/plain');
			
			//Header('Content-type: text/xml');
			
			//Time to send fact to dian		
			
			
			//Get consecutive
			$sql = "select * from  fact_electronica_seguimiento where estado = 1 order by id desc limit 1";
			$query = $this->connect->query($sql);
			$last_cons = $this->connect->convert_object($query);
			
			
			//print_r($last_cons);
			
			/*if($last_cons)
			{
				
				$next_number = str_replace("FE","",$last_cons->ptesa_conse);
				$next_number += $amount_to_plus;
				$consecutive = "FE".$next_number;
			}
			else
			{
				if($amount_to_plus > 1)
				{
					
					$consecutive = "FE".$amount_to_plus;
				}
				$consecutive = "FE1";
			}*/

			$consecutive = $factura->consecutivo;	
           
			
			//$xml_g = $this->generate_xml_with_taxes($this->factura->id,$consecutive);
		    
			$xml_g = $this->generate_xml_with_taxes($this->factura->id,$consecutive);	
			
			
			//$xml_g  = utf8_decode($xml_g);
			
			
			
			if($amount_to_plus == 1)
			{
				//echo $xml_g;	
			}
			
			//$consecutive = "FE704"; 
			
			$response = $this->ptesa_service->registro_documento_obligatorio_test(base64_encode($xml_g),1,$consecutive);
			
			$fact_electronica_seguimiento = array();
			
			$fact_electronica_seguimiento["ptesa_conse"] = $consecutive;
			
			$fact_electronica_seguimiento["factura"] = $factura->id;
			
			$fact_electronica_seguimiento["trackId"] = $response['trackId']->trackId;
			
			//$fact_electronica_seguimiento["xml"] = $xml_g;
			
			if(isset($response['response']->status_code))
			{
				if($response['response']->status_code == 200)
				{
					if($response['response']->body == "[]"){
						$fact_electronica_seguimiento["estado"] = 4;
						$fact_electronica_seguimiento["descr"] = "No es posible enviar la factura";
						
					}else{
						
					$arrayRemplace = array('[',']');
					
					$varInicio = '{"respuesta":';
					
					$varFinal = '}';
					
					$fact_electronica_seguimiento["descr"] = mysql_escape_string($varInicio.str_replace($arrayRemplace,"",$response['response']->body).$varFinal);
					
					$response_ob2 = json_decode($response['response']->body);
					
					  //echo $response_ob2;
					  
					  if($response['response']->body == ''){
						  $varError = "";
					  }
                    
					
					if($response_ob2["0"]->dianStatus->name == 'ACCEPTED' || $response_ob2["0"]->dianStatus->name == 'ACCEPTED_WITH_NOTIFICATIONS' || $response_ob2["0"]->dianStatus->name == 'PROCESSING')
					{						
						$fact_electronica_seguimiento["estado"] = 1;	
						
						//echo "guardar confirmación";
						//print_r($response_ob);
						//positivo
					}
					else
					{
						$fact_electronica_seguimiento["estado"] = 2;
						//error
						//print_r($response_ob);
					}
					
					//print_r($response_ob);
					
				  }
				}
				if($response['response']->status_code == 500)
				{
					$fact_electronica_seguimiento["descr"] = "Server error 500";
					$fact_electronica_seguimiento["estado"] = 3;
				}
			}
			
			
			if(!$fact_electronica_seguimiento["estado"])
			{
				echo json_encode(array("status"=>"ERROR","desc"=>"Hay un problema para comunicarse con el servidor"));
				//exit;
				return false;
			}
			
			
			
			
			$prev_seg = $this->connect->query("select * from fact_electronica_seguimiento where factura = '".$fact_electronica_seguimiento["factura"]."' and
			 estado = ".$fact_electronica_seguimiento["estado"]." and ptesa_conse = '".$fact_electronica_seguimiento["ptesa_conse"]."' and descr = '".$fact_electronica_seguimiento["descr"]."'");
			
			$prev_object = $this->connect->convert_object($prev_seg);
			
			//print_r($prev_object);
			    
			
			if($prev_object == null)
			{ 
				//echo "soy nulo";
				$fact_electronica_seguimiento["usuario"] = $_SESSION["Nombre"];
				$sql = $this->connect->insert("fact_electronica_seguimiento",$fact_electronica_seguimiento);
			
				$query = $this->connect->query($sql);
			}
			else{
				//echo "no soy nulo";
			}
			
			
	}
	
	
	
	public function generarrr_factura_electronica($amount_to_plus=1)
	{
		
		
		/*if($amount_to_plus > 1)
		{
			sleep(7);
		}*/
		
		$factura = $this->factura;
		
		$sql = "select * from fact_electronica_seguimiento where factura = '".$factura->id."' and estado = 1 ";
		
		//echo $sql;
		
		$already_exist = $this->connect->query($sql);
		
		$object_already_exist = $this->connect->convert_object($already_exist);
		
		//print_r($object_already_exist);
		
		if($object_already_exist)
		{
			echo json_encode(array("status"=>"OK","desc"=>"Ya tiene una factura eléctronica aceptada"));
			//exit;
			return false;
		}		
		
		
		header('Content-Type: text/html; charset=utf-8');
		header('Content-Type: text/plain');
			
			//Header('Content-type: text/xml');
			
			//Time to send fact to dian		
			
			
			//Get consecutive
			$sql = "select * from  fact_electronica_seguimiento where estado = 1 order by id desc limit 1";
			$query = $this->connect->query($sql);
			$last_cons = $this->connect->convert_object($query);
			
			
			//print_r($last_cons);
			
			/*if($last_cons)
			{
				
				$next_number = str_replace("FE","",$last_cons->ptesa_conse);
				$next_number += $amount_to_plus;
				$consecutive = "FE".$next_number;
			}
			else
			{
				if($amount_to_plus > 1)
				{
					
					$consecutive = "FE".$amount_to_plus;
				}
				$consecutive = "FE1";
			}*/

			$consecutive = $factura->consecutivo;	
			
			//$xml_g = $this->generate_xml_with_taxes($this->factura->id,$consecutive);
		    
			$xml_g = $this->generate_xml_with_taxes($this->factura->id,$consecutive);			
			
			//$xml_g  = utf8_decode($xml_g);
			
			
			
			if($amount_to_plus == 1)
			{
				//echo $xml_g;	
			}
			
			//$consecutive = "FE704"; 
			
		    $response = $this->ptesa_service->registro_documento_obligatorio_test(base64_encode($xml_g),1,$consecutive);
			
			
			$fact_electronica_seguimiento = array();
			
			$fact_electronica_seguimiento["ptesa_conse"] = $consecutive;
			
			$fact_electronica_seguimiento["factura"] = $factura->id;
			
			$fact_electronica_seguimiento["trackId"] = $response;
			
			$fact_electronica_seguimiento["descr"] = "Debes confirmar el cufe!!!";
			
			$fact_electronica_seguimiento["estado"] = 1;
			
			if($response){
				
					$prev_seg = $this->connect->query("select * from fact_electronica_seguimiento where factura = '".
					
					$fact_electronica_seguimiento["factura"]."' ");
			
			
			
			$prev_object = $this->connect->convert_object($prev_seg);
			
			if($prev_object == null)
			{ 
				//echo "soy nulo";
				$fact_electronica_seguimiento["usuario"] = $_SESSION["Nombre"];
				$sql = $this->connect->insert("fact_electronica_seguimiento",$fact_electronica_seguimiento);
			
				$query = $this->connect->query($sql);
				
			}
			else{
				//echo "no soy nulo";
			}
			}
			
			
	}
	
	
	public function generar_nota_credito($amount_to_plus=1)
	{	
		/*if($amount_to_plus > 1)
		{
			sleep(5);
		}*/
		
		$nota_credito = $this->nota_credito;
		
		$sql = "select * from nota_credito_electronica_seguimiento where nota_credito = '".$nota_credito->id."' and estado = 1 ";
		
		$already_exist = $this->connect->query($sql);
		
		$object_already_exist = $this->connect->convert_object($already_exist);	
		
		if($object_already_exist)
		{
			return array("status"=>"EXIST","desc"=>"Ya tiene una nota eléctronica aceptada");
			//exit;
			
		}
		header('Content-Type: text/html; charset=utf-8');
		header('Content-Type: text/plain');		
		
		$consecutive = "NC".$nota_credito->consecutivo; 
		
		
		$xml_nc = $this->generate_xml_NC_with_taxes($nota_credito->id,$consecutive);		
		
		
		
		$xml = $xml_nc["xml_g"];
		
		$ptesa_conse = $xml_nc["ptesa_conse"];
		
		$response = $this->ptesa_service->registro_documento_obligatorio_test(base64_encode($xml),2,$consecutive,$ptesa_conse);		
		
		$nota_credito_seguimiento = array();
			
		$nota_credito_seguimiento["ptesa_conse"] = $consecutive;
		
		$nota_credito_seguimiento["nota_credito"] = $nota_credito->id;
		
		$nota_credito_seguimiento["trackId"] = $response['trackId']->trackId;
		
		
		if(isset($response['response']->status_code))
		{
			if($response['response']->status_code == 200){
				
				if($response['response']->success == true){
						$nota_credito_seguimiento["estado"] = 1;
						$nota_credito_seguimiento["descr"] = '{"respuesta":{"codigoRespuesta":"00","mensajeRespuesta":"exitoso","contenidoCodigoQR":"","numeroDocumentoGenerado":"'.$consecutive.'","ublToString":""}}';
						
					}else{
					$arrayRemplace = array('[',']');
					
					$varInicio = '{"respuesta":';
					
					$varFinal = '}';
					
					$nota_credito_seguimiento["descr"] = mysql_escape_string($varInicio.str_replace($arrayRemplace,"",$response['response']->body).$varFinal);	
					
					$response_ob2 = json_decode($response['response']->body);
					if($response['response']->body == ''){
						  $varError = "";
					  }
				
				
				if($response_ob2["0"]->dianStatus->name == 'ACCEPTED' || $response_ob2["0"]->dianStatus->name == 'ACCEPTED_WITH_NOTIFICATIONS' || $response_ob2["0"]->dianStatus->name == 'PROCESSING'){
					$nota_credito_seguimiento["estado"] = 1;
				}else{
					$nota_credito_seguimiento["estado"] = 2;
				}
				
				//print_r($response_ob);
			  }
			}
			if($response['response']->status_code == 500)
			{
				$fact_electronica_seguimiento["descr"] = "Server error 500";
				$fact_electronica_seguimiento["estado"] = 3 ;
			}
		}else{
			$nota_credito_seguimiento["descr"] = "Not Server response";
			$nota_credito_seguimiento["estado"] = 3 ;
			//error
		}
		
		if(!$nota_credito_seguimiento["estado"])
		{
			echo json_encode(array("status"=>"ERROR","desc"=>"Hay un problema para comunicarse con el servidor"));
			//exit;
			return false;
		}
		
		
		$prev_seg = $this->connect->query("select * from nota_credito_electronica_seguimiento where nota_credito = '".$nota_credito_seguimiento["nota_credito"]."' and
		 estado = ".$nota_credito_seguimiento["estado"]." and ptesa_conse = '".$nota_credito_seguimiento["ptesa_conse"]."' and descr = '".$nota_credito_seguimiento["descr"]."'");
		
		$prev_object = $this->connect->convert_object($prev_seg);
		
		//print_r($prev_object);	
			
		
		if($prev_object == null)
		{ 
			//echo "soy nulo";
			$nota_credito_seguimiento["usuario"] = $_SESSION["Nombre"];
			$sql = $this->connect->insert("nota_credito_electronica_seguimiento",$nota_credito_seguimiento);
		
			$query = $this->connect->query($sql);
		}
		else{
			//echo "no soy nulo";
		}
		
		//return array("status"=>"OK","desc"=>"Nota electrónica generada");
	
	}	

	public function generar_cufe_electronica($amount_to_plus=1)
	{
		
		
		/*if($amount_to_plus > 1)
		{
			sleep(7);
		}*/
		
		$factura = $this->factura;
		
		$sql = "select trackId from fact_electronica_seguimiento where factura = '".$factura->id."' and estado = 6 ";
		
		//echo $sql;
		
		$already_exist = $this->connect->query($sql);
		
		$object_already_exist = $this->connect->convert_objects($already_exist);
		
		
		foreach($object_already_exist as $detalle)
		{
        $trackId_id = $detalle->trackId;
		};
		
	
		
		
		if(!$object_already_exist)
		{
			echo json_encode(array("status"=>"OK","desc"=>"Ya tiene una factura eléctronica aceptada"));
			//exit;
			return false;
		}		
		
		
		header('Content-Type: text/html; charset=utf-8');
		header('Content-Type: text/plain');
			
			//Header('Content-type: text/xml');
			
			//Time to send fact to dian		
			
			
			//Get consecutive
			$sql = "select * from  fact_electronica_seguimiento where estado = 1 order by id desc limit 1";
			$query = $this->connect->query($sql);
			$last_cons = $this->connect->convert_object($query);
			
			
			//print_r($last_cons);
			
			/*if($last_cons)
			{
				
				$next_number = str_replace("FE","",$last_cons->ptesa_conse);
				$next_number += $amount_to_plus;
				$consecutive = "FE".$next_number;
			}
			else
			{
				if($amount_to_plus > 1)
				{
					
					$consecutive = "FE".$amount_to_plus;
				}
				$consecutive = "FE1";
			}*/

			$consecutive = $factura->consecutivo;	
           
			
			//$xml_g = $this->generate_xml_with_taxes($this->factura->id,$consecutive);
		    
			$xml_g = $this->generate_xml_with_taxes($this->factura->id,$consecutive);	
			
			
			//$xml_g  = utf8_decode($xml_g);
			
			
			
			if($amount_to_plus == 1)
			{
				//echo $xml_g;	
			}
			
			//$consecutive = "FE704"; 
				
			$response = $this->ptesa_service->registro_cufe_documento_obligatorio_test($trackId_id,base64_encode($xml_g),1,$consecutive);
			
			$fact_electronica_seguimiento = array();
			
			$fact_electronica_seguimiento["ptesa_conse"] = $consecutive;
			
			$fact_electronica_seguimiento["factura"] = $factura->id;
			
			$fact_electronica_seguimiento["trackId"] = $response['trackId']->trackId;
			
			//$fact_electronica_seguimiento["xml"] = $xml_g;
			
			if(isset($response['response']->status_code))
			{
				if($response['response']->status_code == 200)
				{
					if($response['response']->body == "[]"){
						$fact_electronica_seguimiento["estado"] = 4;
						$fact_electronica_seguimiento["descr"] = "No es posible enviar la factura";
						
					}else{
						
					$arrayRemplace = array('[',']');
					
					$varInicio = '{"respuesta":';
					
					$varFinal = '}';
					
					$fact_electronica_seguimiento["descr"] = mysql_escape_string($varInicio.str_replace($arrayRemplace,"",$response['response']->body).$varFinal);
					
					$response_ob2 = json_decode($response['response']->body);
					
					  //echo $response_ob2;
					  
					  if($response['response']->body == ''){
						  $varError = "";
					  }
                    
					
					if($response_ob2["0"]->dianStatus->name == 'ACCEPTED' || $response_ob2["0"]->dianStatus->name == 'ACCEPTED_WITH_NOTIFICATIONS' || $response_ob2["0"]->dianStatus->name == 'PROCESSING')
					{						
						$fact_electronica_seguimiento["estado"] = 1;	
						
						//echo "guardar confirmación";
						//print_r($response_ob);
						//positivo
					}
					else
					{
						$fact_electronica_seguimiento["estado"] = 2;
						//error
						//print_r($response_ob);
					}
					
					//print_r($response_ob);
					
				  }
				}
				if($response['response']->status_code == 500)
				{
					$fact_electronica_seguimiento["descr"] = "Server error 500";
					$fact_electronica_seguimiento["estado"] = 3;
				}
			}
			
			
			if(!$fact_electronica_seguimiento["estado"])
			{
				echo json_encode(array("status"=>"ERROR","desc"=>"Hay un problema para comunicarse con el servidor"));
				//exit;
				return false;
			}
			
			
			
			
			$prev_seg = $this->connect->query("select * from fact_electronica_seguimiento where factura = '".$fact_electronica_seguimiento["factura"]."' and
			 estado = ".$fact_electronica_seguimiento["estado"]." and ptesa_conse = '".$fact_electronica_seguimiento["ptesa_conse"]."' and descr = '".$fact_electronica_seguimiento["descr"]."'");
			
			$prev_object = $this->connect->convert_object($prev_seg);
			
			//print_r($prev_object);
			    
			
			if($prev_object == null)
			{ 
				//echo "soy nulo";
				$fact_electronica_seguimiento["usuario"] = $_SESSION["Nombre"];
				$sql = $this->connect->insert("fact_electronica_seguimiento",$fact_electronica_seguimiento);
			
				$query = $this->connect->query($sql);
			}
			else{
				//echo "no soy nulo";
			}
			
			
	}
	
	//xml para factura electrónica	
	public function generate_xml_with_taxes($idfac,$consecutive)
	{
		$this->fact_obj = new json_fact_electronica_taxes();
		
		$sql = "Select * from factura where id = ".$idfac;
		
		$query = $this->connect->query($sql);

		$factura = $this->connect->convert_object($query);		

		
		//aseguradora
		
		$sql = "SELECT ase.txt_mandato,ase.mandato,ase.nombre,ase.nit,ase.dv_cliente_juridico,ase.tipo_mandato, ase.clase_servicio FROM factura fa
               INNER JOIN aseguradora ase ON fa.aseguradora = ase.id 
               WHERE fa.id = ".$factura->id;
		$query = $this->connect->query($sql);
		
		$aseguradora = $this->connect->convert_object($query);
		
		
		//Cliente

		$sql = "select * from cliente where id = ".$factura->cliente;		
		$query = $this->connect->query($sql);
		$cliente = $this->connect->convert_object($query);


		//Ciudad  factura
		
		$sql = "SELECT ciu.codigo FROM factura fac
                LEFT JOIN ciudad ciu ON fac.ciudad_factura = ciu.id 
				WHERE fac.id =".$factura->id;
				
		$query = $this->connect->query($sql); 
		$ciudadFactura = $this->connect->convert_object($query);
		
		if($cliente->ciudad == ''){
			$buscarCodigo = $ciudadFactura->codigo;
		}else{
			$buscarCodigo = $cliente->ciudad;
		}



		$sql = "select * from ciudad where codigo = ".$buscarCodigo;		
		$query = $this->connect->query($sql); 
		$ciudad = $this->connect->convert_object($query);			


		//Detalles de factura

		$sql = "select f.*, c.nombre as concepto , c.porc_iva as ivaperc from facturad as f inner join  concepto_fac as c on c.id = f.concepto where factura = ".$factura->id ;		
        
		$query = $this->connect->query($sql);
		$detalles = $this->connect->convert_objects($query);


		if(isset($detalles[0]->id_siniestro))
		{
			$id_siniestro = $detalles[0]->id_siniestro;
		}
		else
		{
			$id_siniestro = $factura->siniestro;
		}

		//Siniestro  Esto toca modificarlo para que funcione con facturación por lote

		$sql = "select * from siniestro where id = ".$id_siniestro;		
		$query = $this->connect->query($sql);
		$siniestro = $this->connect->convert_object($query);		
		
		if(isset($siniestro->ciudad))
		{
			$sql = "select * from ciudad where id = ".$siniestro->ciudad;		
			$query = $this->connect->query($sql);		
			$ciudad_siniestro =  $this->connect->convert_object($query);		
		}		
		$segundos = date('h:i');
		$fecha_emicion = date("Y-m-d")."T15:".$segundos."-05:00";
		
		$fecha_emicion_Factura = date("Y-m-d");
		$this->fact_obj->consecutive($consecutive,$fecha_emicion,$factura->fecha_vencimiento);
		
		$this->fact_obj->periodoFacturacion($fecha_emicion_Factura,date("Y-m-d",strtotime($fecha_emicion_Factura."+ 1 day")));
		
		$this->fact_obj->tipoOperacion($aseguradora);
		
		$cliente->email_e = trim($cliente->email_e);


		//$this->fact_obj->set_InformacionDeAdquiriente($cliente->email_e);
		
		
		//$this->fact_obj->set_InformacionDeAdquiriente("ventas.javc@gmail.com");
		//$this->fact_obj->set_InformacionDeAdquiriente("sergio.castillo@helpnow.com.co");


		//echo $cliente->email_e;

		

		//Familyname no puede ir vacio o da error
		if(strlen($cliente->apellido) <= 1 )
		{				
			$cliente->apellido = $cliente->nombre;	
		}
		
		$array_setInfartionCliente = array(array("nombre_cliente" => $cliente->nombre, "apellido_cliente" => $cliente->apellido,
		"ciudad_departamento" => utf8_encode($ciudad->departamento), "cliente_barrio" => $cliente->barrio, "ciudad_nombre" =>$ciudad->nombre ,"cliente_direccion" => utf8_encode($cliente->direccion),
		"cliente_celular" => $cliente->celular,"telefono_casa" => $cliente->telefono_casa, "cliente_identificacion" =>$cliente->identificacion,"registro_name" => "ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S.","linea" => "", 
		"correo" => $cliente->email_e, "tipo_persona" => $cliente->tipo_persona,"codigo_municipio" => $buscarCodigo, "dv" => $cliente->dv,"tipo_id" => $cliente->tipo_id, 
		"auto_retenedor_renta" => $cliente->auto_retenedor_renta,
		"auto_retenedor_iva" => $cliente->auto_retenedor_iva, 
		"auto_retenedor_rete_ica" => $cliente->auto_retenedor_rete_ica, 
		"regimen_simple_tributacion" => $cliente->regimen_simple_tributacion,
		"regimen_simple_tri_no_iva" => $cliente->regimen_simple_tri_no_iva, 
		"agente_retencion_iva_no_iva" => $cliente->agente_retencion_iva_no_iva));
		
		

	    $orden_compra = "N.A";
		if(isset($siniestro->numero))
		{
			//No todas las facturas tienen siniestro
			$orden_compra = $siniestro->numero;
		}

		$sql = "select * from aoacol_aoacars.resolucion_factura order by fecha desc limit 1;";

		$query = $this->connect->query($sql);
		$resolucion_fact = $this->connect->convert_object($query);

		$resolucion = "Documento oficial de autorizacion de numeracion de facturacion No. 
		".$resolucion_fact->numero." de ".$resolucion_fact->fecha." vigencia 24 meses 
		".$resolucion_fact->prefijo."".$resolucion_fact->consecutivo_inicial." ".$resolucion_fact->prefijo."".$resolucion_fact->consecutivo_final;
		
		$observaciones = "";
		
		if(strlen($factura->comentario_factura) > 0)
		{
			$observaciones .= $factura->comentario_factura;		
		}	
		
		$this->fact_obj->resolucionFactura($resolucion_fact->numero,$resolucion_fact->fecha,date("Y-m-d",strtotime($resolucion_fact->fecha."+ 2 year")),$resolucion_fact->prefijo,$resolucion_fact->consecutivo_inicial,$resolucion_fact->consecutivo_final);
		
		$this->fact_obj->proveedorOf($resolucion_fact->prefijo);
		
		
		
		/*Se envian por este llamamiento de funcion el array de extenciones*/
		
		//START especificación de impuestos
		
		$items = array();	
			
		$total_taxes_amount = 0;

		$array_of_taxes = array();	

		$tax_19 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"19.00");
		
		$tax_16 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"16.00");
		
		$tax_0 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"0.00");
		
		$valoriva = 0;
		$valorTributos = 0;
		$valorTotalBruto = 0;
		foreach($detalles as $detalle)
		{
			$iva_perc = (int)$detalle->ivaperc;
			
			$item = array();
			$item["cantidad"] = $detalle->cantidad;
			$item["valor"] = $detalle->unitario;
			$item["seguro"] = $detalle->seguro;
			$item["canon"] = $detalle->canon;
			if($cliente->id == 259799){
				$ivaView = "16.00";
			}else{
				$ivaView = $detalle->ivaperc;
			}
			$item["descripcion"] = $detalle->concepto." ".$detalle->descripcion." IVA ".$ivaView."%";
			
			
			
		
		
		
			if((int)$detalle->ivaperc != 0)
			{
				$total_taxes_amount += $item["cantidad"]*$item["valor"]*((int)$detalle->ivaperc/100);
			}
			if((int)$detalle->ivaperc == 19)
			{
				$tax_19["TaxBase"] +=  $item["cantidad"]*$item["valor"];
				$tax_19["TaxValue"] += ($item["cantidad"]*$item["valor"])*0.19;
			
				$item["valor_iva"] = ($item["cantidad"]*$item["valor"])*0.19;			
				$item["iva_perc"] = "19.00";
			}
			if((int)$detalle->ivaperc == 16)
			{
				$tax_16["TaxBase"] +=  $item["cantidad"]*$item["valor"];
				$tax_16["TaxValue"] += ($item["cantidad"]*$item["valor"])*0.16;
			
				$item["valor_iva"] = ($item["cantidad"]*$item["valor"])*0.16;			
				$item["iva_perc"] = "16.00";
			}
			if((int)$detalle->ivaperc == 0)
			{
				$item["valor_iva"] = 0; 			
				$item["iva_perc"] =	"0.00";				
			}
			if($cliente->id == 259799){
				$tax_16["TaxBase"] +=  $item["cantidad"]*$item["valor"];
				$tax_16["TaxValue"] += ($item["cantidad"]*$item["valor"])*0.16;
			
				$item["valor_iva"] = ($item["cantidad"]*$item["valor"])*0.16;			
				$item["iva_perc"] = "16.00";
			} 
			if($item["canon"]!=0){
				$item["valor_iva"] = $detalle->iva;
			}
			$valoriva += $item["valor_iva"];
			array_push($items,$item);
			
			$valorTotalBruto += ($item["cantidad"]*$item["valor"]); 			
			
			
			
			
			if($item["iva_perc"] == '19.00'){
				
				
				$valorTributos += ($item["cantidad"]*$item["valor"])*0.19;
			}else if($item["iva_perc"] == '16.00'){
				
				
				$valorTributos += ($item["cantidad"]*$item["valor"])*0.16;
				
			}else{
				$valorTributo = 0.00;
				
			}
		}

		
		if($tax_19["TaxValue"]>0)
		{
			array_push($array_of_taxes,$tax_19);
		}
	
		if($tax_16["TaxValue"]>0)
		{
			array_push($array_of_taxes,$tax_16);
		}
		
		if($tax_19["TaxValue"] == 0 and $tax_16["TaxValue"] == 0)
		{
			array_push($array_of_taxes,$tax_0);			
		}

	/* Remplazo por la nueva funcionalidad*///$this->fact_obj->set_factItems($items);

		//$this->fact_obj->set_TaxTotal($total_taxes_amount);	
		
		//$this->fact_obj->set_tax_subtotals($array_of_taxes);
		
		$this->fact_obj->set_InformacionClienteAdq($array_setInfartionCliente);
		 
		 $this->fact_obj->medioDePago($IDMetodoPago= 1, $CodigoMedioPago=46,$FechaVencimiento='2019-10-05'); 
		 
		if (filter_var($cliente->email_e, FILTER_VALIDATE_EMAIL)) {
			//echo "correo valido";

		} else {
			echo json_encode(array("status"=>"ERROR","desc"=>"El correo del usuario es invalido"));
			//exit;
			exit;
			
		}
		
		if($cliente->identificacion == "" or $cliente->identificacion == null or $cliente->identificacion == 0){
			echo json_encode(array("status"=>"ERROR","desc"=>"El numero de documento no es valido"));
			exit();
		}
		
		
		$arrayExtencions = array(array("VlrEnLetras" => NumeroALetras::convertir($valoriva + $valorTotalBruto, 'pesos', 'centavos'),
		"resolucion_factura" => $resolucion, "OBL" 
		=>"Actividades Economicas No. 7710 Y 7020\nGrandes Contribuyentes Impuestos Distritales Bogota Resolucion\nDDI-032117 Shda 25 Oct 2019", "numero_orden" => $orden_compra,
		"observaciones" => $observaciones,"observacionesMandato"=> $aseguradora->txt_mandato));
		
		//$this->fact_obj->totalImpuestos($total_taxes_amount,$array_of_taxes);
		
		$this->fact_obj->subTotalTributo($items);
		
		$this->fact_obj->valor_facturas($items);/*Se envia a la nueva funcionalidad*/
		
		$this->fact_obj->set_factItems($items,$aseguradora); /*Ya estamos apuntado a la nueva funcionalidad*/
		
		$this->fact_obj->set_Extenciones($items,$arrayExtencions,$aseguradora);
		
		//END especificación de impuestos
		
		//header('Content-type: "text/xml"; charset="utf8"');
		
		//print_r($this->fact_obj);
		
		//exit;
		
		$xml_g = utf8_decode(Json2xml::generate_xml($this->fact_obj->json_object));
		
		
		
		
		
		return $xml_g;


	}
	
	
	
	
	//xml para nota credito
	public function generate_xml_NC_with_taxes($nota_credito_id,$consecutive)
	{
		$this->notac_obj = new json_nota_credito_taxes();
		
		//Info de la nota credito
		
		$sql = "select * from nota_credito where id = ".$nota_credito_id;		
		$query = $this->connect->query($sql);
		$nota_credito = $this->connect->convert_object($query);		
		
		//Info de la factura
	
		$sql = "select * from factura where id = ".$nota_credito->factura;		
		$query = $this->connect->query($sql);
		$factura = $this->connect->convert_object($query);		
		
		//Info del cliente
			
		$sql = "select * from cliente where id = ".$factura->cliente;		
		$query = $this->connect->query($sql);
		$cliente = $this->connect->convert_object($query);
		
		//Ciudad
		
		$sql = "SELECT ciu.codigo FROM factura fac
                LEFT JOIN ciudad ciu ON fac.ciudad_factura = ciu.id 
				WHERE fac.id =".$factura->id;
				
		$query = $this->connect->query($sql); 
		$ciudadFactura = $this->connect->convert_object($query);
		
		if($cliente->ciudad == ''){
			$buscarCodigo = $ciudadFactura->codigo;
		}else{
			$buscarCodigo = $cliente->ciudad;
		}

        $sql = "select * from ciudad where codigo = ".$buscarCodigo;		
		$query = $this->connect->query($sql); 
		$ciudad = $this->connect->convert_object($query);
		
		//Aseguradora
		$sql = "SELECT ase.mandato,ase.nombre,ase.nit,ase.dv_cliente_juridico,ase.tipo_mandato, ase.clase_servicio FROM factura fa
               INNER JOIN aseguradora ase ON fa.aseguradora = ase.id 
               WHERE fa.id = ".$factura->id;
		$query = $this->connect->query($sql);
		
		$aseguradora = $this->connect->convert_object($query);
			
		// resolucion
		
		$sql = "select * from aoacol_aoacars.resolucion_factura order by fecha desc limit 1;";
        $query = $this->connect->query($sql);
		$resolucion_fact = $this->connect->convert_object($query);

		$resolucion = "Documento oficial de autorizacion de numeracion de facturacion No. 
		".$resolucion_fact->numero." de ".$resolucion_fact->fecha." vigencia 24 meses 
		".$resolucion_fact->prefijo."".$resolucion_fact->consecutivo_inicial." ".$resolucion_fact->prefijo."".$resolucion_fact->consecutivo_final;
		
		
		//SEG FACTURA ELECTRÓNICA
		
		$sql = "select * from fact_electronica_seguimiento where factura = ".$factura->id." and estado = 1 order by id desc LIMIT 1 ";		
		$query = $this->connect->query($sql);
		$fact_seg = $this->connect->convert_object($query);
		
		if($fact_seg == null)
		{
			
			
			if($factura->cufe){
			$consecutivoFactura = $factura->consecutivo;
			$cufeFactura =  $factura->cufe;
			}else{
				echo json_encode(array("status"=>"error","desc"=>"No se puede generar la nota credito electrónica ya que la factura asociada ".$nota_credito->factura." no ha sido enviada electrónicamente"));
			exit;
			}
			
		}else{
			$consecutivoFactura = $fact_seg->ptesa_conse;
		$iCife = json_decode($fact_seg->descr);
		
		foreach($iCife as $value){
			 $cufeFactura = $value->uuid;
		 }
		}
		
		
		$cliente->email_e = trim($cliente->email_e);
		
		if (filter_var($cliente->email_e, FILTER_VALIDATE_EMAIL)) {
				//echo "correo valido";
			
		} else {
			
			echo json_encode(array("status"=>"ERROR","desc"=>"El correo del usuario es invalido"));
			//exit;
			exit;
			
		}	
		
		$array_setInfartionCliente = array(array("nombre_cliente" => $cliente->nombre, "apellido_cliente" => $cliente->apellido,
		"ciudad_departamento" => utf8_encode($ciudad->departamento), "cliente_barrio" => $cliente->barrio, "ciudad_nombre" =>$ciudad->nombre ,"cliente_direccion" => utf8_encode($cliente->direccion),
		"cliente_celular" => $cliente->celular,"telefono_casa" => $cliente->telefono_casa, "cliente_identificacion" =>$cliente->identificacion,"registro_name" => utf8_encode("ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S."),"linea" => "", 
		"correo" => $cliente->email_e, "tipo_persona" => $cliente->tipo_persona,"codigo_municipio" => $buscarCodigo, "dv" => $cliente->dv,"tipo_id" => $cliente->tipo_id));
		
		$orden_compra = "N.A";
		if(isset($siniestro->numero))
		{
			//No todas las facturas tienen siniestro
			$orden_compra = $siniestro->numero;
		}
		
		//$observaciones = "Observaciones nota credito ".$nota_credito->descripcion;
		
		//Detalles de factura

		//$sql = "select f.*, c.nombre as concepto , c.porc_iva as ivaperc from facturad as f inner join  concepto_fac as c on c.id = f.concepto INNER JOIN nota_creditod AS nc ON nc.facturad = f.id where factura = ".$factura->id ;		
        $sql = "select a.* , c.nombre AS concepto , c.porc_iva AS ivaperc, b.id AS idnota FROM facturad a, nota_creditod b, concepto_fac c WHERE a.id=b.facturad AND a.concepto=c.id AND b.nota_credito=".$nota_credito->consecutivo;
		$query = $this->connect->query($sql);
		
		$detalles = $this->connect->convert_objects($query);
		
		

		//START especificación de impuestos
		
		$items = array();	
			
		$total_taxes_amount = 0;

		$array_of_taxes = array();	

		$tax_19 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"19.00");
		
		$tax_16 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"16.00");
		
		$tax_0 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"0.00");

		$total_result_amount = 0;	
		$valorTributos = 0;
		$valorTotalBruto = 0;
		$valoriva = 0;
		$c = 0;
		foreach($detalles as $detalle)
		{
			$c++;
			$query = $this->connect->query(" Select * from nota_creditod where id = ".$detalle->idnota);
		
			$n_detalle = $this->connect->convert_object($query);			
			
			if($n_detalle == null)
			{
				echo "Se tuvo errores al leer los detalles de la nota credito ";
				exit;
			}
			//echo $detalle->id;
			//exit;
			$iva_perc = (int)$detalle->ivaperc;
			
			$item = array();
			$item["cantidad"] = $n_detalle->cantidad;
			$item["valor"] = $n_detalle->unitario;
			$item["seguro"] = $detalle->seguro;
		    $item["canon"] = $detalle->canon;

			$ivanc = $n_detalle->total - ($n_detalle->unitario* $n_detalle->cantidad);
			
			if($cliente->id == 259799){
				$ivaView = "16.00";
			}else{
				$ivaView = $detalle->ivaperc;
			}
			$item["descripcion"] = $detalle->concepto." ".$detalle->descripcion." IVA ".$ivaView."%";
			
			$item["descripcion"] = $detalle->concepto." ".$detalle->descripcion." IVA ".$detalle->ivaperc."%";
			
			
			$total_result_amount += $item["cantidad"]*$item["valor"];
			
			if((int)$detalle->ivaperc != 0)
			{				
				$total_taxes_amount += $item["cantidad"]*$item["valor"]*((int)$detalle->ivaperc/100);
			}
			if((int)$detalle->ivaperc == 19)
			{
				$tax_19["TaxBase"] +=  $item["cantidad"]*$item["valor"];
				$tax_19["TaxValue"] += ($item["cantidad"]*$item["valor"])*0.19;
			
				$item["valor_iva"] = ($item["cantidad"]*$item["valor"])*0.19;			
				$item["iva_perc"] = "19.00";
			}
			if((int)$detalle->ivaperc == 16)
			{
				$tax_16["TaxBase"] +=  $item["cantidad"]*$item["valor"];
				$tax_16["TaxValue"] += ($item["cantidad"]*$item["valor"])*0.16;
			
				$item["valor_iva"] = ($item["cantidad"]*$item["valor"])*0.16;			
				$item["iva_perc"] = "16.00";
			}
			if((int)$detalle->ivaperc == 0)
			{
				$item["valor_iva"] = 0; 			
				$item["iva_perc"] =	"0.00";				
			}
			if($cliente->id == 259799){
				$tax_16["TaxBase"] +=  $item["cantidad"]*$item["valor"];
				$tax_16["TaxValue"] += ($item["cantidad"]*$item["valor"])*0.16;
			
				$item["valor_iva"] = ($item["cantidad"]*$item["valor"])*0.16;			
				$item["iva_perc"] = "16.00";
			} 
			if($item["canon"]!=0){
				//se saca el iva de la diferencia entre el total y valor unidad de la nota credito
				$item["valor_iva"] = $ivanc;
			}
			
			$valoriva += $item["valor_iva"];

			array_push($items,$item);
			
			

			$valorTotalBruto += ($item["cantidad"]*$item["valor"]);
			
			if($item["iva_perc"] == '19.00'){
				
				if($item["canon"]!=0){
					$valorTributos += $detalle->iva;
				}else{
					$valorTributos += ($item["cantidad"]*$item["valor"])*0.19;
				}
				
			}else if($item["iva_perc"] == '16.00'){
				
				
				if($item["canon"]!=0){
					$valorTributos += $detalle->iva;
				}else{
					$valorTributos += ($item["cantidad"]*$item["valor"])*0.16;
				}
				
			}else{
				$valorTributo = 0.00;
				
			}
			
		}

		

		if($tax_19["TaxValue"]>0)
		{
			array_push($array_of_taxes,$tax_19);
		}
	
		if($tax_16["TaxValue"]>0)
		{
			array_push($array_of_taxes,$tax_16);
		}
		
		if($tax_19["TaxValue"] == 0 and $tax_16["TaxValue"] == 0)
		{
			array_push($array_of_taxes,$tax_0);			
		}
		
		$arrayExtencions = array(array("VlrEnLetras" => NumeroALetras::convertir($valoriva + $valorTotalBruto, 'pesos', 'centavos'),
		"resolucion_factura" => $resolucion, "OBL" 
		=>"Actividades Economicas No. 7710 Y 7020\nGrandes Contribuyentes Dian Resolución 012635 14 Dic 2018\nGrandes Contribuyentes 
		Impuestos Distritales Bogota Resolucion DDI-032117 Shda 25 Oct 2019", "consecutivo_factura" => $factura->consecutivo));

		$segundos = date('h:i');
		$fecha_emicion = date("Y-m-d")."T15:".$segundos."-05:00";
		
		$fecha_emicion_Factura = date("Y-m-d");
		$diaDespues = date("Y-m-d",strtotime($fecha_emicion_Factura."+ 1 day"));
		
		$this->notac_obj->periodoFacturacion($fecha_emicion_Factura,$diaDespues);
		
		
		$this->notac_obj->datosNotaCredito(10,"NC".$nota_credito->consecutivo,1,$fecha_emicion,91);
		
		$this->notac_obj->proveedorOf($resolucion_fact->prefijo);
		
		$this->notac_obj->set_InformacionClienteAdq($array_setInfartionCliente);
		
		$this->notac_obj->explicacionesNota(6,"Nota Aplicada a la Factura.: ".$factura->consecutivo);
		
		$this->notac_obj->referenciasFacturas($factura->consecutivo,$cufeFactura,date("Y-m-d"));
		
		$this->notac_obj->medioDePago($IDMetodoPago= 1, $CodigoMedioPago=46,$FechaVencimiento='2019-10-05'); 
		
		$this->notac_obj->subTotalTributo($items);
		
		$this->notac_obj->valor_facturas($items);
		
		$this->notac_obj->set_factItems($items,$aseguradora,$validarItem = 0);
		
		$this->notac_obj->set_Extenciones($items,$arrayExtencions);

		
		$xml_g = utf8_decode(Json2xml::generate_xml($this->notac_obj->json_object));
		
		return array("xml_g"=>$xml_g,"ptesa_conse"=>$consecutivoFactura);
		
	}
	
	
	
		
	
	
}







?>