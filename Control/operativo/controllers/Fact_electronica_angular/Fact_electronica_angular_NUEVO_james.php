<?php 

/********************
 * Autor Original: Jesús Vega 
 * Proyecto: Factura electrónica
 * Documentos relacionados: 
 * Descripción del script:
 * Con este script se realizan todos los procesos de las interfaces angular, estos procesos son la creación de facturas 
 * de taller, notas credito de taller , nota debito de aoa , se hacen reprocesos de enviar notas creditos y facturas
 * así como consultar los XML.
 * Cambios:
 * Autor: Jesús Vega
 * 1. Se agrego la variable de sessión 
 * 2. Se agregó función verifyElecDocument. 
 * 3. Se agregó función verifyElecDocumentControl.
 * 4. Se cambio la forma en que se controlan las excepciones.
 * Fecha:27/02/2019
 *********************/

function errHandle($errNo, $errStr, $errFile, $errLine) {
    $msg = "$errStr in $errFile on line $errLine";
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        throw new ErrorException($msg, $errNo);
    } else {
		//echo "second option";
        //echo $msg;
		throw new ErrorException($msg, $errNo);
    }
}

set_error_handler('errHandle');

//include_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/DbConnect.php");
//require_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/PTESA_ws.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/factura_electronica_NUEVOBK.php");
//require_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/factura_electronica_NUEVO2.php");

if(!isset($_SESSION))
{	
	session_start();				
}

header('Content-Type: text/html; charset=utf-8'); 



if($_GET)
{
	//print_r($_GET);
	if(isset($_GET["XMLFACT"]) AND isset($_GET["XMLCONSE"]))
	{
		
		 $factura_electronica = new factura_electronica(null);
		$xml = $factura_electronica->generate_xml_with_taxes($_GET["XMLFACT"],$_GET["XMLCONSE"]);
		
		header('Content-disposition: attachment; filename="fact_electronica.xml"');
		header('Content-type: "text/xml"; charset="utf8"');
	
		echo $xml; 
	}
	if(isset($_GET["XMLNC"]) AND isset($_GET["XMLCONSE"]))
	{
		
		$factura_electronica = new factura_electronica(null);
		$factura_electronica->set_nota_credito(null);
		$xml = $factura_electronica->generate_xml_NC_with_taxes($_GET["XMLNC"],$_GET["XMLCONSE"]);
		header('Content-disposition: attachment; filename="notaCreditoElectronica.xml"');
		header('Content-type: "text/xml"; charset="utf8"');
		echo $xml["xml_g"];
	}
	 
	if(isset($_GET["XMLMANUAL_FACT"]))
	{
		$fact_angular = new Fact_electronica_angular(null);
		$sql = "select * from docs_manuales_electronicos where id = ".$_GET["XMLMANUAL_FACT"];			
		$query = $fact_angular->connect->query($sql);
		$doc = $fact_angular->connect->convert_object($query);
		$xml = $fact_angular->generate_fact_xml_for_m(json_decode($doc->json));
		//header("Content-type: text/xml");
		header('Content-disposition: attachment; filename="fact_electronica_manual.xml"');
		header('Content-type: "text/xml"; charset="utf8"');
		echo $xml;
	}
	if(isset($_GET["XMLMANUAL_ND"]))
	{
		$fact_angular = new Fact_electronica_angular(null);
		$sql = "select * from docs_manuales_electronicos where id = ".$_GET["XMLMANUAL_ND"];			
		$query = $fact_angular->connect->query($sql);
		$doc = $fact_angular->connect->convert_object($query);
		$xml_g = $fact_angular->generate_fact_xml_for_m_ND(json_decode($doc->json));
		$xml = $xml_g["xml_g"];
		//header("Content-type: text/xml");
		header('Content-disposition: attachment; filename="nd_electronica_manual.xml"');
		header('Content-type: "text/xml"; charset="utf8"');
		echo $xml;
	}
	if(isset($_GET["XMLMANUAL_DT"])){
		$fact_angular = new Fact_electronica_angular(null);
		$sql = "select * from docs_manuales_electronicos where id = ".$_GET["XMLMANUAL_DT"];
		$query = $fact_angular->connect->query($sql);
		$doc = $fact_angular->connect->convert_object($query);
		$xml = $fact_angular->generate_fact_xml_for_m_TANC(json_decode($doc->json));
		header("Content-Type: text/plain");
		header('Content-disposition: attachment; filename="dt_fact_electronica_manual.xml"');
		echo $xml;
	}
	
	exit;
	
}



//header('Content-Type: text/html; charset=utf-8');

$request_body = file_get_contents('php://input');

if($request_body)
{
	$request = json_decode($request_body);	
	$report = new Fact_electronica_angular($request);			
	$acc = $request->acc; 
	
	echo json_encode($report->$acc());
}
else
{
	header('HTTP/1.0 403 Forbidden');

	echo 'You are forbidden!';
}

class Fact_electronica_angular
{
	function __construct($request){
		$this->request = $request;
		$this->connect = new DbConnect();
		$this->ptesa_service = new PTESA_ws();
	}
	
	public function test()
	{
		return array("status"=>"OK");
	}
	
	
	public function get_fact_electronica()
	{
		$sql = "select * from fact_electronica_seguimiento where factura = ".$this->request->factura;			
		$query = $this->connect->query($sql);
		$facturas_electronicas = $this->connect->convert_objects($query);
		return array("status"=>"OK","fac_electronicas"=>$facturas_electronicas);
	}
	
	public function get_nc_electronica()
	{
		$sql = "select * from nota_credito_electronica_seguimiento where nota_credito = ".$this->request->nota_credito;			
		$query = $this->connect->query($sql);
		$facturas_electronicas = $this->connect->convert_objects($query);
		return array("status"=>"OK","nc_electronicas"=>$facturas_electronicas);
	}
	
	
	public function representacion_grafica()
	{
		$ptesa_ws = new PTESA_ws();
		$pdfbase64 = $ptesa_ws->consultar_representacion_grafica($this->request->cufe,$this->request->tipo_documento,true);
		return array("status"=>"OK","pdfbase64"=>$pdfbase64); 
	}
	
	public function enviar_factura()
	{
		$sql = "select * from factura where id = ".$this->request->factura;			
		$query = $this->connect->query($sql);
		$factura = $this->connect->convert_object($query);
		$factura_electronica = new factura_electronica($factura);
		$factura_electronica->generar_factura_electronica();
		return array("status"=>"OK","factura"=>$factura);
	}
	
	
	
	public function enviar_confirmacion_cufe()
	{
		
		$sql = "select * from factura where id = ".$this->request->factura;			
		$query = $this->connect->query($sql);
		$factura = $this->connect->convert_object($query);
		$factura_electronica = new factura_electronica($factura);
		$factura_electronica->generar_cufe_electronica();
		return array("status"=>"OK","factura"=>$factura);
	}
	public function enviar_nota_credito()
	{
		$sql = "select * from nota_credito where id = ".$this->request->nota_credito;			
		$query = $this->connect->query($sql);
		$nota_credito = $this->connect->convert_object($query);		
		$factura_electronica = new factura_electronica(null);
		$factura_electronica->set_nota_credito($nota_credito);
		$factura_electronica->generar_nota_credito();
		return array("status"=>"OK","factura"=>$nota_credito);
	}
	
	
	
	
	//Servicios relacionados a factura manual
	
	
	
	public function get_customers_service()
	{
		$sql = "select * from cliente where nombre  like '%".$this->request->name."%'";			
		$query = $this->connect->query($sql);
		$customers = $this->connect->convert_objects($query);
		return array("status"=>"OK","customers"=>$customers);
	}
	
	
	public function get_customer()
	{
		$sql = "select * from cliente where identificacion  = '".$this->request->iden."' LIMIT 1";			
		$query = $this->connect->query($sql);
		$customer = $this->connect->convert_object($query);
		
		$city = null;
		
		if($customer)
		{ 	
			$sql = "select * from ciudad where codigo  = '".$customer->ciudad."' LIMIT 1";			
			$query = $this->connect->query($sql);
			$city = $this->connect->convert_object($query);
		}		
		
		$customer->nombre = utf8_encode($customer->nombre);
		
		$customer->apellido = utf8_encode($customer->apellido);
		
		//print_r($customer);
		
		return array("status"=>"OK","customer"=>$customer,"city"=>$city);
	}
	
	
	private function myCount ($str) {
		$letters=$digits=$i=0;
		$retval=array();
		while ($i<strlen($str)) {
		if (preg_match("/[a-zA-Z]/",$str{$i}))
		$letters++;
		else if (preg_match("/[0-9]/",$str{$i}))
		$digits++;
		++$i;
		}

		$retval['letters']=$letters;
		$retval['digits']=$digits;

		return $retval;
	}
	
	
	
	public function validate_consecutive_bill($prot_cons)
	{
		
		$this->request->consecutive = trim($this->request->consecutive);		
		$ws = preg_match('/\s/',$this->request->consecutive);	
		
		$ns = $this->myCount($this->request->consecutive);
		
	
		if(substr($this->request->consecutive, 0, 2 ) === $prot_cons and !$ws and $ns["letters"]==2)
		{	
		
			$sql = "select * from docs_manuales_electronicos where consecutivo  = '".$this->request->consecutive."' and estado = 1 LIMIT 1";			
			$query = $this->connect->query($sql);
			$last_cons = $this->connect->convert_object($query);
			
			
			
			
			
			if($last_cons)
			{
				if($last_cons->consecutivo == $this->request->consecutive)
				{
					return array("status"=>"NO VALID","desc"=>"Consecutivo repetido");
				}
				else
				{
					return array("status"=>"OK","desc"=>"Consecutivo valido");
				}				
			}
			else
			{
				return array("status"=>"OK","desc"=>"Consecutivo valido");
			}
		
		}
		else
		{
			return array("status"=>"NO VALID","desc"=>"Consecutivo con formato no valido");
		}
	}
	
	//Verificar documento taller
	public function verifyElecDocument()
	{		
		try
		{
			$consecutive = $this->request->tipoDocumento."".$this->request->consecutivo;
			
			$sql = "select * from docs_manuales_electronicos where consecutivo  = '".$consecutive."'  and estado = 1 limit 1 ";			

			$query = $this->connect->query($sql);
			$last_cons = $this->connect->convert_object($query);	

			
			if(!$last_cons )
			{
				switch($this->request->tipoDocumento)
				{
					case "TA":
						$tipo_documento = 1;	
						break;
					case "ND":
						$tipo_documento = 3;
						break;
					case "DT":
						$tipo_documento = 2;
						break;
				}
					
					
					
				$response = $this->ptesa_service->consultar_representacion_grafica($this->request->cufe,$tipo_documento,true);
				//Deberia ser verificado con el segundo servicio
				/*$response = $this->ptesa_service->verifica_estado_servicio($this->request->cufe,$tipo_documento);
				
				print_r($response);
				exit;*/
				
				if(isset($response->status))
				{
					return array("status"=>"FAILED","desc"=>"El documento no se pudo verificar");
				}
				else
				{			
					$helperObject = json_decode('{"respuesta":{"codigoRespuesta":"00","mensajeRespuesta":"exitoso","contenidoCodigoQR":"","cufe":"","numeroDocumentoGenerado":"","ublToString":""}}');
					
					$helperObject->respuesta->cufe = $this->request->cufe;
					
					$helperObject->respuesta->numeroDocumentoGenerado = $consecutive;
					
					$register = array();
					
					$register["consecutivo"] = $consecutive;
					$register["json"] = "";
					$register["estado"] = 1;
					$register["usuario"] = $_SESSION["Nombre"];
					$register["descr"] = mysql_escape_string(json_encode($helperObject));		
					$register["tipo"] = $tipo_documento;
					
					$sql = $this->connect->insert("docs_manuales_electronicos",$register);								
					
					//echo $sql;
					
					$query = $this->connect->query($sql);
					
					return array("status"=>"OK","desc"=>"Documento verificado");
				}						
			}
			else
			{
				return array("status"=>"FAILED","desc"=>"El documento ya se encuentra verificado");
			}	
		}
		catch(Exception $ex){
			return array("status"=>"FAILED","desc"=>"Sucedio un error ".$ex->getMessage());
		}
	}
	
	//Verificar documento control
	public function verifyElecDocumentControl()
	{		
		try
		{
			$consecutive = $this->request->tipoDocumento."".$this->request->consecutivo;
			
			$register = array();
			
			switch($this->request->tipoDocumento)
			{
				case "FE":
					$validationOnLocal = "Select * from factura where consecutivo = '$consecutive' ";	
					$registerIndex = "factura";
					
					$sqlExistence = "select * from fact_electronica_seguimiento where ptesa_conse  = '".$consecutive."'  and estado = 1 limit 1 ";				
					$tipo_documento = 1;
					$bd = "fact_electronica_seguimiento";	
					break;
				case "NC":
					$validationOnLocal = "Select * from factura nota_credito consecutivo = '".$this->request->consecutivo."' ";
					$registerIndex = "nota_credito";
				
					$sqlExistence = "select * from nota_credito_electronica_seguimiento where ptesa_conse  = '".$consecutive."'  and estado = 1 limit 1 ";
					$tipo_documento = 2;
					$bd = "nota_credito_electronica_seguimiento";
					break;				
			}
			
			
			$query = $this->connect->query($validationOnLocal);
			$Doc = $this->connect->convert_object($query);
			
			if($Doc == null)
			{			
				return array("status"=>"FAILED","desc"=>"El consecutivo no existe en la base de datos");
			}
			else{
				$register[$registerIndex] = $Doc->id;  	
			}		
			
			//echo $sql;
			
			$query = $this->connect->query($sqlExistence);
			$lastCons = $this->connect->convert_object($query);
			
			
			if(!$lastCons)
			{
				$response = $this->ptesa_service->consultar_representacion_grafica($this->request->cufe,$tipo_documento,true);
				
				if(isset($response->status))
				{
					return array("status"=>"FAILED","desc"=>"El documento no se pudo verificar");
				}
				else
				{
					$helperObject = json_decode('{"respuesta":{"codigoRespuesta":"00","mensajeRespuesta":"exitoso","contenidoCodigoQR":"","cufe":"","numeroDocumentoGenerado":"","ublToString":""}}');
					
					$helperObject->respuesta->cufe = $this->request->cufe;
					
					$helperObject->respuesta->numeroDocumentoGenerado = $consecutive;
					
					$register["ptesa_conse"] = $consecutive;				
					$register["estado"] = 1;
					$register["usuario"] = $_SESSION["Nombre"];
					$register["descr"] = mysql_escape_string(json_encode($helperObject));			
					
					$sql = $this->connect->insert($bd,$register);
					
					//echo $sql;
					
					$query = $this->connect->query($sql);
					
					return array("status"=>"OK","desc"=>"Documento verificado");
				}
			}
			else
			{
				return array("status"=>"FAILED","desc"=>"El documento ya se encuentra verificado");
			}		
		}
		catch(Exception $ex){
			return array("status"=>"FAILED","desc"=>"Sucedio un error ".$ex->getMessage());
		}
	}
	
	
	public function make_elec_document()
	{
		
		if($this->request->doc == 1)
		{
			
			$cons_validation = $this->validate_consecutive_bill("TA");				

			
			if($cons_validation["status"] != "OK")
			{
				
				echo json_encode($cons_validation);
				exit;
			}
			
			
			//header('Content-Type: text/html; charset=utf-8');
			header('Content-Type: text/plain');
			
			$xml = $this->generate_fact_xml_for_m($this->request);
			
		    echo $xml;
			
			exit();
			
			
			$register = array();
			$register["consecutivo"] = $this->request->consecutive;
			$register["json"] = mysql_escape_string(json_encode($this->request));
			$register["tipo"] = 1; 			
			 
			 
			
			$response = $this->ptesa_service->registro_documento_obligatorio_test(base64_encode($xml),1,$register["consecutivo"]);
		
			$register['trackId'] = $response['trackId']->trackId;
			
			if(isset($response->status_code))
			{
				
				if($response->status_code == 200)
				{	
					$register["descr"] = mysql_escape_string($response->body);
					
					$response_ob = json_decode($response->body);
					
					if($response_ob->respuesta->codigoRespuesta == 0)
					{						
						$register["estado"] = 1;	
						
						//positivo
						$res = array("status"=>"OK","desc"=>"Factura creada");
						
					}
					else
					{
						if($response_ob->respuesta->codigoRespuesta == 12)
						{
							
						}
						
						$register["estado"] = 2;
						//error
						//print_r($response_ob);
						$res = array("status"=>"ERROR","desc"=>"Sucedio un error al mandar la factura ".$response_ob->respuesta->mensajeRespuesta);
						
						
					}
					
					//print_r($response_ob);
				}
				else
				{
					
					$register["descr"] = "Error ".$response->status_code;
					$register["estado"] = 3 ;
					//error
					
					$res = array("status"=>"ERROR","desc"=>"Error ".$response->status_code);
							
				}
				
				
			}
			else
			{
				
				$register["descr"] = "Not Server response";
				$register["estado"] = 3 ;
				//error
				
				$res =  array("status"=>"ERROR","desc"=>"Error de servidor");
						
			}		
			
			
			$prev_seg = $this->connect->query("select * from docs_manuales_electronicos where consecutivo = '".$register["consecutivo"]."' and
			 estado = ".$register["estado"]."  and descr = '".$register["descr"]."' and tipo = '".$register["tipo"]."' ");
			
			$prev_object = $this->connect->convert_object($prev_seg);
			
			//print_r($prev_object);	
				
			
			if($prev_object == null)
			{ 
				//echo "soy nulo";
				$register["usuario"] = $_SESSION["Nombre"];
				
				$sql = $this->connect->insert("docs_manuales_electronicos",$register);
				
				//echo $sql;
			
				$query = $this->connect->query($sql);
			}
			else{
				//echo "no soy nulo";
			}
			
			return $res;
			
			
		}
		
		if($this->request->doc == 2)
		{
			
			$cons_validation = $this->validate_consecutive_bill("DT");				

			
			if($cons_validation["status"] != "OK")
			{
				
				echo json_encode($cons_validation);
				exit;
			}
			
			
			//header('Content-Type: text/html; charset=utf-8');
			
			header('Content-Type: text/plain; ');
			
			$xml = $this->generate_fact_xml_for_m_TANC($this->request);
			
			//echo utf8_encode($xml); exit;
			
			
			$register = array();
			$register["consecutivo"] = $this->request->consecutive;
			$register["json"] = mysql_escape_string(json_encode($this->request));
			$register["tipo"] = 2;
			$register["doc_relacionado"] = $this->request->fact_cons;	
			 
			//$ptesa_conse = $this->request->fact_cons;
			
			//echo $ptesa_conse;
			
			$response = $this->ptesa_service->registro_documento_obligatorio(base64_encode($xml),2,$register["consecutivo"],$register["doc_relacionado"]);		
			//print_r($response);

			$success_message = "Nota credito taller creada";
			
			$error_message = "Sucedio un error al mandar la nota credito de taller";
			
		}
		
	

		if($this->request->doc == 3)
		{
			
			$cons_validation = $this->validate_consecutive_bill("ND");				

			
			if($cons_validation["status"] != "OK")
			{
				
				echo json_encode($cons_validation);
				exit;
			}
			
			header('Content-Type: text/plain; ');
			
			$xml_g = $this->generate_fact_xml_for_m_ND($this->request);
			
			
			$xml = $xml_g["xml_g"];
			$ptesa_conse = $xml_g["ptesa_conse"];
			
			
			$register = array();
			
			$register["consecutivo"] = $this->request->consecutive;
			$register["json"] = mysql_escape_string(json_encode($this->request));
			$register["tipo"] = 3;
			$register["doc_relacionado"] = $ptesa_conse;

			$response = $this->ptesa_service->registro_documento_obligatorio(base64_encode($xml),3,$register["consecutivo"],$ptesa_conse);		
			//print_r($response);

			$success_message = "Nota debito creada";
			
			$error_message = "Sucedio un error al mandar la nota debito";
			
		}			
			
			if(isset($response->status_code))
			{
				
				if($response->status_code == 200)
				{	
					$register["descr"] = mysql_escape_string($response->body);
					
					$response_ob = json_decode($response->body);
					
					if($response_ob->respuesta->codigoRespuesta == 0)
					{						
						$register["estado"] = 1;	
						
						//positivo
						$res = array("status"=>"OK","desc"=>$success_message);
						
					}
					else
					{
						if($response_ob->respuesta->codigoRespuesta == 12)
						{
							
						}
						
						$register["estado"] = 2;
						//error
						//print_r($response_ob);
						$res = array("status"=>"ERROR","desc"=>$error_message." ".$response_ob->respuesta->mensajeRespuesta);
						
						
					}
					
					//print_r($response_ob);
				}
				else
				{
					
					$register["descr"] = "Error ".$response->status_code;
					$register["estado"] = 3 ;
					//error
					
					$res = array("status"=>"ERROR","desc"=>"Error ".$response->status_code);
							
				}
				
				
			}
			else
			{
				
				$register["descr"] = "Not Server response";
				$register["estado"] = 3 ;
				//error
				
				$res =  array("status"=>"ERROR","desc"=>"Error de servidor");
						
			}		
			
			
			$prev_seg = $this->connect->query("select * from docs_manuales_electronicos where consecutivo = '".$register["consecutivo"]."' and
			 estado = ".$register["estado"]."  and descr = '".$register["descr"]."' and tipo = '".$register["tipo"]."' ");
			
			$prev_object = $this->connect->convert_object($prev_seg);
			
			//print_r($prev_object);	
				
			
			if($prev_object == null)
			{ 
				//echo "soy nulo";
				$register["usuario"] = $_SESSION["Nombre"];
				
				$sql = $this->connect->insert("docs_manuales_electronicos",$register);
				
				//echo $sql;
			
				$query = $this->connect->query($sql);
			}
			else{
				//echo "no soy nulo";
			}
			
			return $res;
		
	}
	
	//Generar xml para factura electronica
	public function generate_fact_xml_for_m($json_input)
	{
		/*Factura electronica manual*/
		$consecutive = $json_input->consecutive;		
		$segundos = date('h:i');
		$fecha_emicion = date("Y-m-d")."T15:".$segundos."-05:00";
		$fecha_venc = $json_input->bill_dates->fecha_elaboracion;
		
		
		$fact = new json_fact_electronica_taxes();
		
		$fact->consecutive($consecutive,$fecha_emicion,$fecha_venc);
		
		$fecha_emicion_Factura = date("Y-m-d");
		
		$fact->periodoFacturacion($fecha_emicion_Factura,date("Y-m-d",strtotime($fecha_emicion_Factura."+ 1 day")));
		
		
		
		$valor_letras = NumeroALetras::convertir($json_input->doc_details->total, 'pesos', 'centavos');
		
		//echo $valor_letras;
		
		$resolucion_numero = "18762012077960";
		
		$resolucion_fecha_ini = "2018/12/31";
		
		$resolucion_prefijo = "TA";
		
		$resolucion_consecutivo_inicial = 1;
		
		$resolucion_consecutivo_final = 1000;
		
		$resolucion = "Documento oficial de autorizacion de numeracion de facturacion No. ".$resolucion_numero." de                   ".$resolucion_fecha_ini." vigencia 24 meses ".$resolucion_prefijo."".$resolucion_consecutivo_inicial." ".$resolucion_prefijo."".$resolucion_consecutivo_final;
		
		$observaciones = "Observaciones ".$json_input->comments; 
		
		if(isset($json_input->orden))
		{
			$orden = $json_input->orden; 
		} 
		else 
		{
			$orden = "N.A";
		}
		
		$fact->resolucionFactura($resolucion_numero,$resolucion_fecha_ini,date("Y-m-d",strtotime($resolucion_fecha_ini."+ 2 year")),$resolucion_prefijo,$resolucion_consecutivo_inicial,$resolucion_consecutivo_final);
		
		$fact->proveedorOf($resolucion_prefijo);
		/*Query for city*/
		$sql = "select * from ciudad where codigo = ".$json_input->customer->ciudad;	
		$query = $this->connect->query($sql);
		$ciudadCliente = $this->connect->convert_objects($query);
		/*Query for cliente*/
		$sql = "select * from cliente WHERE identificacion = ".$json_input->customer->identificacion;
		$query = $this->connect->query($sql);
		$clienteTipo = $this->connect->convert_objects($query);
		
		$array_setInfartionCliente = array(array("nombre_cliente" => $json_input->customer->nombre,
		                                   "apellido_cliente" => $json_input->customer->apellido,
										   "ciudad_departamento" => $ciudadCliente[0]->departamento, 
										   "cliente_barrio" => $json_input->customer->barrio, 
										   "ciudad_nombre" => $ciudadCliente[0]->nombre, 
										   "cliente_direccion" => $json_input->customer->direccion,
										   "cliente_celular" => $json_input->customer->celular,
										   "telefono_casa" => $json_input->customer->telefono_casa,
										   "cliente_identificacion" => $json_input->customer->identificacion,
										   "registro_name" => utf8_decode("ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S."),
										   "linea" => "",
										   "correo" => $json_input->customer->email_e,
										   "tipo_persona" => $clienteTipo[0]->tipo_persona,
										   "codigo_municipio" => $json_input->city->codigo,
										   "dv" => $clienteTipo[0]->dv,
										   "tipo_id" => $clienteTipo[0]->tipo_id));//Aqui vamos OK.. Sergio
		
		$fact->set_InformacionClienteAdq($array_setInfartionCliente);
		
		$fact->medioDePago($IDMetodoPago= 1, $CodigoMedioPago=46,$FechaVencimiento='2019-10-05');
		
		
		
		
		$customer = $json_input->customer;
		
		$city = $json_input->city;
		
		//START EMAIL VALIDATION
		
		$customer->email_e = trim($customer->email_e);
		
		if (filter_var($customer->email_e, FILTER_VALIDATE_EMAIL)) {
			
		} else {
			
			echo json_encode(array("status"=>"ERROR","desc"=>"El correo del usuario es invalido"));
			exit;
			
		}
		
		$doc_details = $json_input->doc_details;		
		
		$bill_items = $json_input->bill_items;
		
		$items = array();	
		
		//START variables for taxes
		
			$base_tax = 0;

			$total_taxes_amount = 0;

			$array_of_taxes = array();	

			$tax_19 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"19.00");
			
			$tax_16 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"16.00");
			
			$tax_0 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"0.00");
		
		//END variables for taxes
		$valorTributos = 0;
		$valorTotalBruto = 0;
		foreach($bill_items as $bill_item)
		{
			$item = array();
			$item["cantidad"] = $bill_item->amount;
			$item["valor"] = $bill_item->value;
			
			//header('Content-Type: text/html; charset=utf-8');
		    
			$item["descripcion"] = $bill_item->desc." CANTIDAD ".$bill_item->amount." VALOR IVA ".$bill_item->tax;
			
			if($bill_item->tax != 0)
			{	
				$base_tax += ($bill_item->value)*($bill_item->amount);	
			}
			
			if($bill_item->tax == 19)
			{
				$tax_19["TaxBase"] +=  $bill_item->amount*$bill_item->value;
				$tax_19["TaxValue"] += ($bill_item->amount*$bill_item->value)*0.19;
				
				$item["valor_iva"] = ($item["cantidad"]*$item["valor"])*0.19;			
				$item["iva_perc"] = "19.00";
			}
			
			if($bill_item->tax == 16)
			{
				$tax_16["TaxBase"] +=  $bill_item->amount*$bill_item->value;
				$tax_16["TaxValue"] += ($bill_item->amount*$bill_item->value)*0.16;
			
				$item["valor_iva"] = ($item["cantidad"]*$item["valor"])*0.19;			
				$item["iva_perc"] = "19.00";
			}
			
			if($bill_item->tax == 0)
			{
				$item["valor_iva"] = 0; 			
				$item["iva_perc"] =	"0.00";				
			}
			
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
		
		$fact->subTotalTributo($items);
		
		$fact->valor_facturas($items);
		
		$aseguradora = (object)array("mandato" => 2);
		$fact->set_factItems($items,$aseguradora);

		//START especificación de impuestos
		
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
		
			
			
			
		
		
		//END especificación de impuestos
		if($json_input->orden !=''){
			$numberOrden = $json_input->orden;
		}else{
			$numberOrden = "N.A";
		}
		
		
		
		$arrayExtencions = array(array("VlrEnLetras" => NumeroALetras::convertir($valorTributos + $valorTotalBruto, 'pesos', 'centavos'),
		"resolucion_factura" => $resolucion, "OBL" =>"Actividades Economicas No. 7710 Y 7020\nGrandes Contribuyentes Dian Resolución 012635 14 Dic 2018\nGrandes Contribuyentes 
		Impuestos Distritales Bogota Resolucion DDI-032117 Shda 25 Oct 2019", "numero_orden" => $numberOrden));
		
		header('Content-type: "text/xml"; charset="utf8"');
		
		$fact->set_Extenciones($items,$arrayExtencions);
		
		$xml_g = utf8_decode(Json2xml::generate_xml($fact->json_object));
		
		return $xml_g;
		
		
	
	}
	
	//Generar factura manual de aoa para nota debito
	public function generate_fact_xml_for_m_ND($json_input)
	{
		/*Nota debito*/
		$consecutive = $json_input->consecutive;	

		$customer = $json_input->customer;		
		
		$nd = new json_nota_debito();
		
		$query = $this->connect->query("select * from factura where consecutivo = '".$json_input->fact_cons."'  LIMIT 1 ");
			
		$fact = $this->connect->convert_object($query);
		
		if($fact == null)
		{
			echo json_encode(array("status"=>"ERROR","desc"=>"No existe factura con ese consecutivo"));
			exit;
		}
		else{
			
			$query = $this->connect->query("select * from cliente where id = ".$fact->cliente."  LIMIT 1 ");
			
			$fact_cliente = $this->connect->convert_object($query);
		
			if($fact_cliente->identificacion != $customer->identificacion)
			{
				echo json_encode(array("status"=>"ERROR","desc"=>"El cliente de la factura es distinto a la nota debito"));
				exit;	
			}
			
		}
		
		
		$query = $this->connect->query("select * from fact_electronica_seguimiento where factura = ".$fact->id);
			
		$fact_seg = $this->connect->convert_object($query);
		
		if($fact_seg == null)
		{
			echo json_encode(array("status"=>"ERROR","desc"=>"La factura no ha sido enviada electrónicamente"));
			exit;
		}
		
		$nd->set_Consecutive($consecutive);
		
		$nd->set_Doc_anulacion($fact_seg->ptesa_conse);
		
		$fecha_venc = $json_input->bill_dates->fecha_elaboracion;			
		
		$valor_letras = NumeroALetras::convertir($json_input->doc_details->total, 'pesos', 'centavos');
		
		//echo $valor_letras;
		
		$nd->set_InformacionDeDocumento($fecha_venc,$valor_letras,"NA");	
		
		$city = $json_input->city;

		//START EMAIL VALIDATION
		
		$customer->email_e = trim($customer->email_e);
		
		if (filter_var($customer->email_e, FILTER_VALIDATE_EMAIL)) {
			
		} else {
			
			echo json_encode(array("status"=>"ERROR","desc"=>"El correo del usuario es invalido"));
			exit;
			
		}
		
		//END EMAIL VALIDATION
		
		
		$nd->set_InformacionDeAdquiriente($customer->email_e);
		
		$nd->set_Info_Cliente($customer->nombre,$customer->apellido,$city->departamento,$customer->barrio,$city->nombre,$customer->direccion,$customer->celular,$customer->identificacion,"");
		
		$doc_details = $json_input->doc_details; 
		
		//$doc_details->iva = str_replace(",","",$doc_details->iva);		
		
		//echo str_replace(",","",$doc_details->subtotal);
		
		$nd->set_TaxTotal(str_replace(",","",$doc_details->iva),str_replace(",","",$doc_details->subtotal));
		
		$nd->set_LegalMonetaryTotal(str_replace(",","",$doc_details->subtotal),str_replace(",","",$doc_details->iva),0);
		
		$bill_items = $json_input->bill_items;
		
		$items = array();	
			
		foreach($bill_items as $bill_item)
		{
			$item = array();
			$item["cantidad"] = $bill_item->amount;
			$item["valor"] = $bill_item->value;
			$item["descripcion"] = $bill_item->desc;
			array_push($items,$item);
		}
		
		$nd->set_ndItems($items);
		
		//header("Content-Type: text/plain");

		//print_r($nd->nd_object);
		
		//exit;

		$xml_g = utf8_decode(Json2xml::generate_xml($nd->nd_object));
		
		return array("xml_g"=>$xml_g,"ptesa_conse"=>$fact_seg->ptesa_conse);
		
		//return $xml_g;
			
	}
	
	//Generar xml manual para nota credito de taller
	public function generate_fact_xml_for_m_TANC($json_input)
	{
		$fecha_inicio = $json_input->bill_dates->fecha_elaboracion;
		
		$valor_letras = NumeroALetras::convertir($json_input->doc_details->total, 'pesos', 'centavos');
		
		//echo $valor_letras;
		
		$resolucion_prefijo = "TA";
		
		
		$consecutive = $json_input->consecutive;

		$fact_cons = $json_input->fact_cons;
		
		//START LOGICAL BILLING VALIDATION
		
			$query = $this->connect->query("select * from docs_manuales_electronicos where consecutivo = '".$fact_cons."' and estado = 1  LIMIT 1 ");
			
			$fact = $this->connect->convert_object($query);
			
			if($fact == null)
			{
				echo json_encode(array("status"=>"ERROR","desc"=>"Esta factura no esta disponible para hacer una nota credito"));
				exit;
			}
			else{
				
				/*$query = $this->connect->query("select * from docs_manuales_electronicos where doc_relacionado = '".$fact_cons."' and estado = 1 and  LIMIT 1 ");
			
				$fact = $this->connect->convert_object($query);*/
				
			}
		
		//END LOGICAL BILLING VALIDATION

		

		$customer = $json_input->customer;	
		
		$nc = new json_nota_credito_taxes($fact_cons);
		
		$nc->set_Doc_anulacion($fact_cons);
		
		$nc->set_Consecutive($consecutive);
		
		//INIT SETTING DOCUMENT INFORMATION
		
		if(isset($json_input->comments)){
		    $observaciones = "Observaciones ".$json_input->comments;      
		}else{
			$observaciones = "N.A";
		}
		
		if(isset($json_input->orden))
		{
			$orden = $json_input->orden;
		} 
		else 
		{
			$orden = "N.A";
		}
		
		$nc->set_InformacionDeDocumento($fecha_inicio,$valor_letras,$orden,'',$observaciones);
		
		//ENDING SETTING DOCUMENT INFORMATION
		
		
		//START EMAIL VALIDATION
		
		$customer->email_e = trim($customer->email_e);
		
		if (filter_var($customer->email_e, FILTER_VALIDATE_EMAIL)) {
			
		} else {
			
			echo json_encode(array("status"=>"ERROR","desc"=>"El correo del usuario es invalido"));
			exit;
			
		}
		
		//END EMAIL VALIDATION
		
		$nc->set_InformacionDeAdquiriente($customer->email_e);
		
		$city = $json_input->city;
		
		$nc->set_Info_Cliente($customer->nombre,$customer->apellido,$city->departamento,$customer->barrio,$city->nombre,$customer->direccion,$customer->celular,$customer->identificacion,"");
		
		$doc_details = $json_input->doc_details;		
		
		$nc->set_LegalMonetaryTotal($doc_details->subtotal,$doc_details->iva,0);
		
		$bill_items = $json_input->bill_items;
		
		$items = array();	
		
		//START variables for taxes
		
			$base_tax = 0;

			$total_taxes_amount = 0;

			$array_of_taxes = array();	

			$tax_19 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"19.00");
			
			$tax_16 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"16.00");
			
			$tax_0 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"0.00");
		
		//END variables for taxes
		
		foreach($bill_items as $bill_item)
		{
			$item = array();
			$item["cantidad"] = $bill_item->amount;
			$item["valor"] = $bill_item->value;
			
			header('Content-Type: text/html; charset=utf-8');
		    
			$item["descripcion"] = $bill_item->desc;
			
			if($bill_item->tax != 0)
			{	
				$base_tax += ($bill_item->value)*($bill_item->amount);	
			}
			
			if($bill_item->tax == 19)
			{
				$tax_19["TaxBase"] +=  $bill_item->amount*$bill_item->value;
				$tax_19["TaxValue"] += ($bill_item->amount*$bill_item->value)*0.19;
				
				$item["valor_iva"] = ($item["cantidad"]*$item["valor"])*0.19;			
				$item["iva_perc"] = "19.00";
			}
			
			if($bill_item->tax == 16)
			{
				$tax_16["TaxBase"] +=  $bill_item->amount*$bill_item->value;
				$tax_16["TaxValue"] += ($bill_item->amount*$bill_item->value)*0.16;
			
				$item["valor_iva"] = ($item["cantidad"]*$item["valor"])*0.19;			
				$item["iva_perc"] = "19.00";
			}
			
			if($bill_item->tax == 0)
			{
				$item["valor_iva"] = 0; 			
				$item["iva_perc"] =	"0.00";				
			}
			
			array_push($items,$item);
		}
		
		$nc->set_ncItems($items);

		//START especificación de impuestos
		
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
		
			$nc->set_TaxTotal($doc_details->iva,$base_tax);
			
			$nc->set_tax_subtotals($array_of_taxes);
		
		
		//END especificación de impuestos
		
		$nc->set_LegalMonetaryTotal($doc_details->subtotal,$doc_details->iva,0);
	
		//$nc->set_LegalMonetaryTotal("114","21",0);	
	
		$xml_g = utf8_decode(Json2xml::generate_xml($nc->nc_object));
		
		return $xml_g;
		
		
	}
	
	
}


?>	
	