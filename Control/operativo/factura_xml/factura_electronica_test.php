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

//json factura
include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/aoa_json.php");

include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/aoa_json_with_taxes_test.php");


//json nota credito
include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/aoa_NC_json.php");

include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/aoa_NC_json_with_taxes.php");


//json nota debito
include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/aoa_ND_json.php");
	
	
include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/Json2xml_test.php");

include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/NumeroALetras.php");




//$fact_electronica = new factura_electronica(null);
//$fact_electronica->test();

//echo "here";

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
			
			//echo $xml_g;
			
			//exit;
			
			if($amount_to_plus == 1)
			{
				//echo $xml_g;	
			}
			
			//$consecutive = "FE704"; 
			
			$response = $this->ptesa_service->registro_documento_obligatorio(base64_encode($xml_g),1,$consecutive);

			//print_r($response);	
			
			$fact_electronica_seguimiento = array();
			
			$fact_electronica_seguimiento["ptesa_conse"] = $consecutive;
			
			$fact_electronica_seguimiento["factura"] = $factura->id;
			
			//$fact_electronica_seguimiento["xml"] = $xml_g;
			
			if(isset($response->status_code))
			{
				if($response->status_code == 200)
				{
					$fact_electronica_seguimiento["descr"] = mysql_escape_string($response->body);
					
					$response_ob = json_decode($response->body);
					
					if($response_ob->respuesta->codigoRespuesta == 0)
					{						
						$fact_electronica_seguimiento["estado"] = 1;	
						
						//echo "guardar confirmación";
						//print_r($response_ob);
						//positivo
					}
					else
					{
						if($response_ob->respuesta->codigoRespuesta == 12)
						{
							//echo "consecutivo ya tomado"; 
							//$amount_to_plus += 1;
							//echo "a sumar siguiente ".$amount_to_plus;
							//$this->generar_factura_electronica($amount_to_plus);
						}	
						
						$fact_electronica_seguimiento["estado"] = 2;
						//error
						//print_r($response_ob);
					}
					
					//print_r($response_ob);
				}
				if($response->status_code == 500)
				{
					$fact_electronica_seguimiento["descr"] = "Server error 500";
					$fact_electronica_seguimiento["estado"] = 3 ;
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
		
		//header('Content-Type: text/html; charset=utf-8');
		header('Content-Type: text/plain; ');
			
		//Header('Content-type: text/xml');
		
		//Get consecutive
		//$sql = "select * from  nota_credito_electronica_seguimiento where estado = 1 order by id desc limit 1";		
		//$query = $this->connect->query($sql);
		//$last_cons = $this->connect->convert_object($query);
		
		
		//print_r($last_cons);
		
		/*if($last_cons)
		{
			
			$next_number = str_replace("NC","",$last_cons->ptesa_conse);
			$next_number += $amount_to_plus;
			$consecutive = "NC".$next_number;
		}
		else
		{	
			if($amount_to_plus > 1)
			{
				
				$consecutive = "NC".$amount_to_plus;
			}
			else
			{	
				$consecutive = "NC1";
			}
		}*/	
		
		
		$consecutive = "NC".$nota_credito->consecutivo; 
		
		$xml_nc = $this->generate_xml_NC_with_taxes($nota_credito->id,$consecutive);		
		
		$xml = $xml_nc["xml_g"];
		
		
		$ptesa_conse = $xml_nc["ptesa_conse"];
		
		//echo $this->notac_obj->test_xml;		
		
		$response = $this->ptesa_service->registro_documento_obligatorio(base64_encode($xml),2,$consecutive,$ptesa_conse);		
		
		//Codigo 00 exitoso y 12 repetido
		
		//print_r($response);
		
		
		
		$nota_credito_seguimiento = array();
			
		$nota_credito_seguimiento["ptesa_conse"] = $consecutive;
		
		$nota_credito_seguimiento["nota_credito"] = $nota_credito->id;
		
		
		if(isset($response->status_code))
		{
			if($response->status_code == 200)
			{
				$nota_credito_seguimiento["descr"] = mysql_escape_string($response->body);
				
				$response_ob = json_decode($response->body);
				
				if($response_ob->respuesta->codigoRespuesta == 0)
				{						
					$nota_credito_seguimiento["estado"] = 1;	
					
					//echo "guardar confirmación";
					//print_r($response_ob);
					//positivo
				}
				else
				{
					if($response_ob->respuesta->codigoRespuesta == 12)
					{
						//echo "consecutivo ya tomado";
						//echo " ".$consecutive;	
						//$amount_to_plus += 1;
						//echo "a sumar siguiente ".$amount_to_plus;
						//$this->generar_nota_credito($amount_to_plus);
					}	
					
					$nota_credito_seguimiento["estado"] = 2;
					//error
					//print_r($response_ob);
				}
				
				//print_r($response_ob);
			}
			if($response->status_code == 500)
			{
				$fact_electronica_seguimiento["descr"] = "Server error 500";
				$fact_electronica_seguimiento["estado"] = 3 ;
			}
		}
		else
		{
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

	
	//xml para factura electrónica	
	
	public function generate_xml($idfac,$consecutive)
	{
		

		//XML PARA FÁCTURAS.
		$sql = "Select * from factura where id = ".$idfac;
		
		$query = $this->connect->query($sql);
		
		$factura = $this->connect->convert_object($query);		
		
			//Cliente
		
			$sql = "select * from cliente where id = ".$factura->cliente;		
			$query = $this->connect->query($sql);
			$cliente = $this->connect->convert_object($query);
			
			
			//Ciudad
			
			$sql = "select * from ciudad where codigo = ".$cliente->ciudad;		
			$query = $this->connect->query($sql); 
			$ciudad = $this->connect->convert_object($query);			
			
			
			//Detalles de factura
			
			$sql = "select f.*, c.nombre as concepto , c.porc_iva as ivaperc from facturad".$this->ext_table." as f inner join  concepto_fac as c on c.id = f.concepto where factura = ".$factura->id ;		
			
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
			
			
			//$consecutive = "FE704";
			
			$this->fact_obj->consecutive($consecutive); 
			
			
			$cliente->email_e = trim($cliente->email_e);
			
			
			$this->fact_obj->set_InformacionDeAdquiriente($cliente->email_e);
			//$this->fact_obj->set_InformacionDeAdquiriente("ventas.javc@gmail.com");
			//$this->fact_obj->set_InformacionDeAdquiriente("sergio.castillo@helpnow.com.co");
			
			
			//echo $cliente->email_e;
			
			if (filter_var($cliente->email_e, FILTER_VALIDATE_EMAIL)) {
				//echo "correo valido";
			
			} else {
				
				echo json_encode(array("status"=>"ERROR","desc"=>"El correo del usuario es invalido"));
				//exit;
				exit;
				
			}
			
			
			
			
			//Familyname no puede ir vacio o da error
			if(strlen($cliente->apellido) <= 1 )
			{				
				$cliente->apellido = $cliente->nombre;	
			}
			
			$this->fact_obj->set_InformacionCliente($cliente->nombre,$cliente->apellido,$ciudad->departamento,$cliente->barrio,$ciudad->nombre,$cliente->direccion,$cliente->celular,$cliente->identificacion,"");
			
			$orden_compra = "N.A";
			if(isset($siniestro->numero))
			{
				//No todas las facturas tienen siniestro
				$orden_compra = $siniestro->numero;
			}
			
			$sql = "select * from aoacol_aoacars.resolucion_factura order by fecha desc limit 1;";
			
			$query = $this->connect->query($sql);
			$resolucion_fact = $this->connect->convert_object($query);
			
			//$resolucion = "Numeracion autorizada por la DIAN segun resolucion No. ".$resolucion_fact->numero." de ".$resolucion_fact->fecha." ".$resolucion_fact->consecutivo_inicial."-".$resolucion_fact->consecutivo_final;	
			
			$resolucion = "Documento oficial de autorizacion de numeracion de facturacion No. ".$resolucion_fact->numero." de                   ".$resolucion_fact->fecha." vigencia 24 meses ".$resolucion_fact->prefijo."".$resolucion_fact->consecutivo_inicial." ".$resolucion_fact->prefijo."".$resolucion_fact->consecutivo_final;			
			/*funcion que no sabemos como va en la extenciones  set_InformacionDeDocumento*/
			$this->fact_obj->set_InformacionDeDocumento($factura->fecha_vencimiento,NumeroALetras::convertir($factura->total, 'pesos', 'centavos'),$orden_compra,$resolucion);
			
			$this->fact_obj->set_LegalMonetaryTotal($factura->subtotal,$factura->iva,0);
				
			$items = array();	
				
			$base_impuestos = 0;	
			
			foreach($detalles as $detalle)
			{
				$iva_perc = (int)$detalle->ivaperc;
				
				$item = array();
				$item["cantidad"] = $detalle->cantidad;
				$item["valor"] = $detalle->unitario;
				$item["descripcion"] = $detalle->concepto." ".$detalle->descripcion." IVA ".$detalle->ivaperc."%";
				
				if((int)$detalle->ivaperc != 0)
				{
					$base_impuestos += $item["cantidad"]*$item["valor"];
				}	
				
				array_push($items,$item);
			}
			
			$this->fact_obj->set_factItems($items);

			$this->fact_obj->set_TaxTotal($factura->iva,$base_impuestos);
			
			
			

			$this->fact_obj->set_iva_percent($iva_perc);			

			$xml_g = utf8_decode(Json2xml::generate_xml($this->fact_obj->json_object));
			
			return $xml_g;	
			
	}
	
	public function generate_xml_with_taxes($idfac,$consecutive)
	{
		$this->fact_obj = new json_fact_electronica_taxes();
		
		$sql = "Select * from factura where id = ".$idfac;

		$query = $this->connect->query($sql);

		$factura = $this->connect->convert_object($query);		

		//Cliente

		$sql = "select * from cliente where id = ".$factura->cliente;		
		$query = $this->connect->query($sql);
		$cliente = $this->connect->convert_object($query);


		//Ciudad

		$sql = "select * from ciudad where codigo = ".$cliente->ciudad;		
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
		
		
		$this->fact_obj->consecutive($consecutive); 


		$cliente->email_e = trim($cliente->email_e);


		$this->fact_obj->set_InformacionDeAdquiriente($cliente->email_e);
		//$this->fact_obj->set_InformacionDeAdquiriente("ventas.javc@gmail.com");
		//$this->fact_obj->set_InformacionDeAdquiriente("sergio.castillo@helpnow.com.co");


		//echo $cliente->email_e;

		if (filter_var($cliente->email_e, FILTER_VALIDATE_EMAIL)) {
			//echo "correo valido";

		} else {
			
			echo json_encode(array("status"=>"ERROR","desc"=>"El correo del usuario es invalido"));
			//exit;
			exit;
			
		}

		//Familyname no puede ir vacio o da error
		if(strlen($cliente->apellido) <= 1 )
		{				
			$cliente->apellido = $cliente->nombre;	
		}

		$this->fact_obj->set_InformacionCliente($cliente->nombre,$cliente->apellido,$ciudad->departamento,$cliente->barrio,$ciudad->nombre,$cliente->direccion,$cliente->celular,$cliente->identificacion,"");			

		$orden_compra = "N.A";
		if(isset($siniestro->numero))
		{
			//No todas las facturas tienen siniestro
			$orden_compra = $siniestro->numero;
		}

		$sql = "select * from aoacol_aoacars.resolucion_factura order by fecha desc limit 1;";

		$query = $this->connect->query($sql);
		$resolucion_fact = $this->connect->convert_object($query);

		//$resolucion = "Numeracion autorizada por la DIAN segun resolucion No. ".$resolucion_fact->numero." de ".$resolucion_fact->fecha." ".$resolucion_fact->consecutivo_inicial."-".$resolucion_fact->consecutivo_final;	

		$resolucion = "Documento oficial de autorizacion de numeracion de facturacion No. ".$resolucion_fact->numero." de                   ".$resolucion_fact->fecha." vigencia 24 meses ".$resolucion_fact->prefijo."".$resolucion_fact->consecutivo_inicial." ".$resolucion_fact->prefijo."".$resolucion_fact->consecutivo_final;			
		
		$observaciones = "Observaciones: ";
		
		if(strlen($factura->comentario_factura) > 0)
		{
			$observaciones .= $factura->comentario_factura;		
		}	
		if(isset($ciudad_siniestro))
		{	
			$observaciones .= " Ciudad ".$ciudad_siniestro->nombre;
		}
		
		$this->fact_obj->set_InformacionDeDocumento($factura->fecha_vencimiento,NumeroALetras::convertir($factura->total, 'pesos', 'centavos'),$orden_compra,$resolucion,$observaciones);		
		
		$this->fact_obj->set_LegalMonetaryTotal($factura->subtotal,$factura->iva,0);
			
		
		//START especificación de impuestos
		
		$items = array();	
			
		$total_taxes_amount = 0;

		$array_of_taxes = array();	

		$tax_19 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"19.00");
		
		$tax_16 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"16.00");
		
		$tax_0 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"0.00");
		
		
		
		foreach($detalles as $detalle)
		{
			$iva_perc = (int)$detalle->ivaperc;
			
			$item = array();
			$item["cantidad"] = $detalle->cantidad;
			$item["valor"] = $detalle->unitario;
			$item["descripcion"] = $detalle->concepto." ".$detalle->descripcion." IVA ".$detalle->ivaperc."%";
			
		
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
			
			array_push($items,$item);
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

		$this->fact_obj->set_factItems($items);

		$this->fact_obj->set_TaxTotal($total_taxes_amount);	
		
		$this->fact_obj->set_tax_subtotals($array_of_taxes);
		
		//END especificación de impuestos
		
		
		$xml_g = utf8_decode(Json2xml::generate_xml($this->fact_obj->json_object));
		
		return $xml_g;


	}
	
	//xml para nota credito
	public function generate_xml_NC($nota_credito_id,$consecutive)
	{
		
		
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
		
		$sql = "select * from ciudad where codigo = ".$cliente->ciudad;		
		$query = $this->connect->query($sql); 
		$ciudad = $this->connect->convert_object($query);
			
		
		//SEG FACTURA ELECTRÓNICA
		
		$sql = "select * from fact_electronica_seguimiento where factura = ".$factura->id." and estado = 1 order by id desc LIMIT 1 ";		
		$query = $this->connect->query($sql);
		$fact_seg = $this->connect->convert_object($query);
		
		if($fact_seg == null)
		{
			echo json_encode(array("status"=>"error","desc"=>"No se puede generar la nota credito electrónica ya que la factura asociada ".$nota_credito->factura." no ha sido enviada electrónicamente"));
			exit;
		}
		
		$cliente->email_e = trim($cliente->email_e);
		
		$this->notac_obj->set_InformacionDeAdquiriente($cliente->email_e);
		
		//$this->notac_obj->set_InformacionDeAdquiriente("sergio.castillo@helpnow.com.co");
		
		if (filter_var($cliente->email_e, FILTER_VALIDATE_EMAIL)) {
				//echo "correo valido";
			
		} else {
			
			echo json_encode(array("status"=>"ERROR","desc"=>"El correo del usuario es invalido"));
			//exit;
			exit;
			
		}
		
		
		
		
		$this->notac_obj->set_Info_Cliente($cliente->nombre,$cliente->apellido,$ciudad->departamento,$cliente->barrio,$ciudad->nombre,$cliente->direccion,$cliente->celular,$cliente->identificacion,"");	
		
		$this->notac_obj->set_Consecutive($consecutive);
		
		$orden_compra =  $factura->consecutivo;
		
		$sql = "select * from aoacol_aoacars.resolucion_factura order by fecha desc limit 1;";
			
		$query = $this->connect->query($sql);
		$resolucion_fact = $this->connect->convert_object($query);
		
		$resolucion = "Numeracion autorizada por la DIAN segun resolucion No. ".$resolucion_fact->numero." de ".$resolucion_fact->fecha;	
			
		
		$this->notac_obj->set_InformacionDeDocumento($factura->fecha_vencimiento,NumeroALetras::convertir($nota_credito->total, 'pesos', 'centavos'),$orden_compra,"");		

		
		$this->notac_obj->set_Doc_anulacion($fact_seg->ptesa_conse);		
		
		$this->notac_obj->set_TaxTotal($nota_credito->valor_iva,$nota_credito->base_iva);
		
		$this->notac_obj->set_LegalMonetaryTotal($nota_credito->valor_bruto,$nota_credito->valor_iva,0);		
		
		//echo $xml_g;
		
		
		$items = array();	
				
			/*foreach($detalles as $detalle)
			{
				$item = array();
				$item["cantidad"] = $detalle->cantidad;
				$item["valor"] = $detalle->unitario;
				$item["descripcion"] = $detalle->concepto." ".$detalle->descripcion;
				array_push($items,$item);
			}*/
			
			
		$item = array();
		$item["cantidad"] = 1;
		$item["valor"] = $nota_credito->valor_bruto;
		$item["descripcion"] = "Nota credito referente a la factura cons ".$fact_seg->ptesa_conse;
		array_push($items,$item);
				
		$this->notac_obj->set_ncItems($items);	
		
		//print_r($this->notac_obj->nc_object);		
		
		$xml_g = utf8_decode(Json2xml::generate_xml($this->notac_obj->nc_object));
		
		//echo $xml_g;		
			
		return array("xml_g"=>$xml_g,"ptesa_conse"=>$fact_seg->ptesa_conse);
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
		
		$sql = "select * from ciudad where codigo = ".$cliente->ciudad;		
		$query = $this->connect->query($sql); 
		$ciudad = $this->connect->convert_object($query);
			
		
		//SEG FACTURA ELECTRÓNICA
		
		$sql = "select * from fact_electronica_seguimiento where factura = ".$factura->id." and estado = 1 order by id desc LIMIT 1 ";		
		$query = $this->connect->query($sql);
		$fact_seg = $this->connect->convert_object($query);
		
		if($fact_seg == null)
		{
			echo json_encode(array("status"=>"error","desc"=>"No se puede generar la nota credito electrónica ya que la factura asociada ".$nota_credito->factura." no ha sido enviada electrónicamente"));
			exit;
		}
		
		$cliente->email_e = trim($cliente->email_e);
		
		$this->notac_obj->set_InformacionDeAdquiriente($cliente->email_e);
		
		//$this->notac_obj->set_InformacionDeAdquiriente("sergio.castillo@helpnow.com.co");
		
		if (filter_var($cliente->email_e, FILTER_VALIDATE_EMAIL)) {
				//echo "correo valido";
			
		} else {
			
			echo json_encode(array("status"=>"ERROR","desc"=>"El correo del usuario es invalido"));
			//exit;
			exit;
			
		}	
		
		
		$this->notac_obj->set_Info_Cliente($cliente->nombre,$cliente->apellido,$ciudad->departamento,$cliente->barrio,$ciudad->nombre,$cliente->direccion,$cliente->celular,$cliente->identificacion,"");	
		
		$this->notac_obj->set_Consecutive($consecutive);
		
		$orden_compra =  $factura->consecutivo;
		
		$sql = "select * from aoacol_aoacars.resolucion_factura order by fecha desc limit 1;";
			
		$query = $this->connect->query($sql);
		$resolucion_fact = $this->connect->convert_object($query);
		
		$resolucion = "Numeracion autorizada por la DIAN segun resolucion No. ".$resolucion_fact->numero." de ".$resolucion_fact->fecha;	
		
		$observaciones = "Observaciones nota credito ".$nota_credito->descripcion;
		
		$this->notac_obj->set_InformacionDeDocumento($factura->fecha_vencimiento,NumeroALetras::convertir($nota_credito->total, 'pesos', 'centavos'),$orden_compra,"",$observaciones);

		$this->notac_obj->set_Doc_anulacion($fact_seg->ptesa_conse);
		
		//Detalles de factura

		$sql = "select f.*, c.nombre as concepto , c.porc_iva as ivaperc from facturad as f inner join  concepto_fac as c on c.id = f.concepto INNER JOIN nota_creditod AS nc ON nc.facturad = f.id where factura = ".$factura->id ;		

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
		
		foreach($detalles as $detalle)
		{
			
			$query = $this->connect->query(" Select * from nota_creditod where facturad = ".$detalle->id);
		
			$n_detalle = $this->connect->convert_object($query);			
			
			if($n_detalle == null)
			{
				echo "Se tuvo errores al leer los detalles de la nota credito ";
				exit;
			}
			 
			$iva_perc = (int)$detalle->ivaperc;
			
			$item = array();
			$item["cantidad"] = $n_detalle->cantidad;
			$item["valor"] = $n_detalle->unitario;
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
			
			array_push($items,$item);
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

		$this->notac_obj->set_ncItems($items);

		$this->notac_obj->set_TaxTotal($total_taxes_amount);	
		
		$this->notac_obj->set_tax_subtotals($array_of_taxes);
		
		//END especificación de impuestos		
		
		//$this->notac_obj->set_LegalMonetaryTotal($nota_credito->valor_bruto,$nota_credito->valor_iva,0);
		
		$this->notac_obj->set_LegalMonetaryTotal(floor($total_result_amount),floor($total_taxes_amount));
		
		//echo floor($total_taxes_amount + $total_result_amount);		
		
		//exit;
		
		$xml_g = utf8_decode(Json2xml::generate_xml($this->notac_obj->nc_object));	
			
		return array("xml_g"=>$xml_g,"ptesa_conse"=>$fact_seg->ptesa_conse);
	}
	
	
	public function test()
	{
		echo "test";
		
		
	}

	function verificar_nota_credito_electronica()
	{
		sesion();
		//echo "Sub interfaz de factura electronica";
		header('Content-Type: text/html; charset=utf-8');
		include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/views/factura_electronica/interfaz.html");
	}
		
	
	
}







?>