<?php



//error_reporting(-1);


// Motrar todos los errores de PHP
//error_reporting(E_ALL);

// Motrar todos los errores de PHP
ini_set('error_reporting', E_ALL);
header('Access-Control-Allow-Origin: *');
	
include("Requests-master/library/Requests.php");


include("DbConnect.php"); 


include("../inc/funciones_.php");
include('../inc/pdf/fpdf.php'); 
include("hubPdfPagos/elaborarPdf.php");
//sesion();

$request_body = file_get_contents('php://input');



if($request_body)
	{
		   //print_r($request_body);
			$request = json_decode($request_body);
			//echo "make print";
			
			if(isset($request->apikeyaoa)){
				if($request->apikeyaoa !='yNP3sfONgZoGmH$7'){
					echo json_encode("No tiene acceso");
				    exit;
				}else{
					
			if(isset($request->acc)){
				$Wservices = new FacturasHubClientes($request);			
				
				$acc = $request->acc; 
				
				if(method_exists($Wservices,$acc))
				{
					$Wservices->$acc($request);	
				}
				else{
					echo json_encode(array("desc"=>"Metodo no existe: ".$acc));
					
						}
				
					}
				}
			
			}else{
				echo json_encode("No tiene acceso");
				exit;
			}
			
	}
	

	
	
	
class FacturasHubClientes{
	
	function  __construct($request){
		$this->request = $request;
		$this->connect = new DbConnect();
		$this->pdf = new pdf_realizar();
		$this->Nusuario = "Sistema de pago online";
		
	}
	
  public function verDataFactura(){
	  try{
		  
	  
	  $doc =  $this->request->documento;
	  $fac = $this->request->factura;
	  
	  $sql = "SELECT fac.*,cli.* FROM  factura fac
			INNER JOIN cliente cli ON fac.cliente = cli.id
			WHERE fac.consecutivo = '$fac' AND cli.identificacion = '$doc'";
	  $queryFactura = qo($sql);
	  
      
		if($queryFactura){
			$sqlCiudad = "SELECT nombre,departamento FROM ciudad WHERE codigo = ".$queryFactura->ciudad;
               $traerId = "SELECT id FROM factura WHERE consecutivo = '$fac'"; 
			   $idF = qo($traerId);
              $sqlLineas = "SELECT con.nombre as concepto,facd.descripcion,
							facd.cantidad,facd.unitario
							,facd.iva,facd.total
							FROM facturad facd 
							INNER JOIN concepto_fac con ON facd.concepto = con.id
							WHERE factura = ".$idF->id; 
			  
			  $facLi = q($sqlLineas);
			  $queryLineas = $this->connect->convert_objects($facLi);
			  
			$ciudad = qo($sqlCiudad);
			$bill = 1;
		}else{
			$bill = 2;
		}
		
		$sqlReceiptBox = "SELECT reci.*
							FROM recibo_caja reci
							LEFT JOIN factura fac ON reci.factura = fac.id
							WHERE fac.consecutivo = '$fac' AND reci.anulado != 1";
        $receipt = q($sqlReceiptBox);
		
		if($receipt){
			
			$cicloReceipt = $this->connect->convert_objects($receipt);
			$arrayReceipt = array();
			   foreach($cicloReceipt as $row){
					 array_push($arrayReceipt,$row->valor);
				}
			 $valueReceipt = array_sum($arrayReceipt);
	 
		    if($valueReceipt == $queryFactura->total){
				$isPay = true;
			}else if($valueReceipt < $queryFactura->total){
				$isPay = false;$payIncomplete = true;
				$calculation = ($queryFactura->total - $valueReceipt);
				$total = $queryFactura->total;
				$queryFactura->total = $calculation;
				$queryFactura->subtotal = $calculation;
				$queryFactura->iva = 0;
			}
		}else{
			$isPay = false;
		}
		
		
							
		
		
	  
	  echo json_encode(array("status"=>"OK","viewFactura"=>"entraServicio",
	                         "is_bill" => $bill,"dataFact" => $queryFactura, 
							  "ciudad" => $ciudad,"lineasFac" => $queryLineas,"isPay" => $isPay,
							  "payIncomplete" => $payIncomplete,"abono" => $valueReceipt,"totalVoice" => $total));
	  
	  }catch(Exception $e){
		echo json_encode(array("status"=>"NO","viewFactura"=>"entraServicio"));  
	  }
  }
  
  
  public function sendBill(){
	
	try{
		  
	  $doc =  $this->request->documento;
	  $fac = $this->request->factura;
	  
	   
	  Requests::register_autoloader();
	  
	  $data_to_send = array(
	  "login" => "aoaPruebas",
	  "password" => "a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3",
	  "canal" => 3
	  ); 
	  
	  $headers = array(
		"content-type" => "application/json");
		
	  
	  $requestEmissionToken = Requests::post('https://serviciospruebas.ptesa.com.co:1093/eph/v1/seguridad/login',$headers,json_encode($data_to_send));
	  $decoRequest = json_decode($requestEmissionToken->body);
	  
	  $idPrograma =  $decoRequest->listaProgramas[0]->id;
	  
	  $tokenLoging = $decoRequest->token;
	 
	 
	   $headersTwo = array(
		"content-type" => "application/json",
		"Authorization" => "Bearer ".$tokenLoging);
	  
	  $sql = "SELECT id,numero_transaccion,fecha,codigo_respuesta,
	          mensaje_respuesta,total_pago,iva
	          FROM pago_online_seguimiento WHERE factura_consecutivo = '$fac'";
	  $q = q($sql);$queryPagos = $this->connect->convert_objects($q);
	  
	  $options = array('timeout' => 80,
		                 );
	 
	  $sql = "SELECT fac.*,cli.* FROM  factura fac
			INNER JOIN cliente cli ON fac.cliente = cli.id
			WHERE fac.consecutivo = '$fac' AND cli.identificacion = '$doc'";
	
	 $getDataClFac = qo($sql);
	 
	 $sqlCiudad = "SELECT ofi.id of FROM factura fac
								INNER JOIN siniestro sini ON fac.siniestro = sini.id
								INNER JOIN ciudad ciu ON sini.ciudad = ciu.codigo
								INNER JOIN oficina ofi ON ciu.codigo = ofi.ciudad 
								WHERE fac.consecutivo = '$fac'";
																
	$ciudadUno = qo($sqlCiudad);
	
	if(!$ciudadUno){
		$sqlCiudadFactura = "SELECT oficina FROM factura WHERE consecutivo = '$fac'";
		$ciudadFactura = qo($sqlCiudadFactura);
		$ciudadId = $ciudadFactura->oficina;
	}else{
		$ciudadId = $ciudadUno->of;
	}
	
	switch($getDataClFac->tipo_id){      
		case "CC":$tipoIntDen=1;break;
		case "CE":$tipoIntDen= 1;break;
		case "NIT":$tipoIntDen=8;break;
		case "PAS":$tipoIntDen=1;break;
		case "TI":$tipoIntDen=1; break;
	 }
	 
	 switch($getDataClFac->tipo_id){
		case "CC":$tipoPersona=0;break;
		case "CE":$tipoPersona= 0;break;
		case "NIT":$tipoPersona=8;break;
		case "PAS":$tipoPersona=0;break;
		case "TI":$tipoPersona=0; break;
	 }
	 
	 function clearChar($string){
		 $arryareplace = array("$","Ã",",",".","Â");
		 $va = str_replace($arryareplace,"",utf8_decode($string));
		 return substr($va,1);
	 }
	 
	if($this->request->isTruePay){
		   $getDataClFac->subtotal = clearChar($this->request->valueInvoice);
		   $getDataClFac->total = clearChar($this->request->valueInvoice);
		   $getDataClFac->iva = clearChar($this->request->ivaTotal);
	}
	  $idPrograma = 
	  $arrayInicialVenta = array("venta" => array(
	                            "canal" => 3,
								"idOrganizacion" => $decoRequest->idOrganizacion,
								"numeroAuditoria" => "DEC", 
								"tipoIdentificacion" => 1,
								"numeroIdentificacion"=> $getDataClFac->identificacion,
								"nombre" => $getDataClFac->nombre,
								"apellido" => $getDataClFac->apellido,
								"correoElectronico" => $getDataClFac->email_e,
								"numeroCelular" => $getDataClFac->celular,
								"valorVenta" => intval($getDataClFac->subtotal),
								"idPrograma" => $idPrograma, 
								"duracionLink" => 24,
								"enviarCorreoComercio" => "0",
								"enviarCorreoCliente" => "0",
								"tipoPersona" => 0,
								"impuestos" => array(
								  "iva" => intval($getDataClFac->iva)
								),
								"valorPagar" => intval($getDataClFac->total)
							));
    
	$requestEmissionVenta = Requests::post('https://serviciospruebas.ptesa.com.co:1093/eph/v1/venta/adicionarventa',$headersTwo,json_encode($arrayInicialVenta),$options);
	
	$respuestaLink = json_decode($requestEmissionVenta->body);
	
	
	$arrayEndpoint = array("codigoRespuesta" => $respuestaLink->codigoRespuesta,
						   "mensajeRespuesta" => $respuestaLink->mensajeRespuesta,
						   "numeroAuditoria" => $respuestaLink->numeroAuditoria,
						   "numeroTransaccion" => $respuestaLink->numeroTransaccion,
						   "link" => $respuestaLink->link);
	$date = date("Y-m-d H:i:s");
	
	$sql = "INSERT INTO pago_online_seguimiento
	(numero_transaccion,factura_consecutivo,numero_auditoria,mensaje_respuesta,fecha,link_pago,codigo_respuesta,total_pago,iva,ciudad)
	 VALUES ('".$arrayEndpoint['numeroTransaccion']."','$fac','".$arrayEndpoint['numeroAuditoria']."',
	 '".$arrayEndpoint['mensajeRespuesta']."','$date','".$arrayEndpoint['link']."'
	 ,'".$arrayEndpoint['codigoRespuesta']."',".intval($getDataClFac->total).",".intval($getDataClFac->iva).",'$ciudadId')";
	 
	q($sql);
	
	echo json_encode(array("status"=>"Ok","viewFactura"=>"entraServicio","dataEndpoint" => $arrayEndpoint));
	
	}catch(Exception $e){
	
	   echo json_encode(array("status"=>"NO","viewFactura"=>"Hubo un error"));
	
	}
  
  }
  
  public function confirmInvoice(){
	  
	 try{
		
		$number_transaction = $this->request->number_transaction;
		$state_response = $this->request->state_response;
		$message = $this->request->message;
		$reference = $this->request->reference;
		$type_id =  $this->request->type_id;
		$value_invoice = $this->request->value_invoice;
		$date_pay = $this->request->date_pay;
		$number_autorization = $this->request->number_autorization;
		$trazability_code = $this->request->trazability_code;
		$code_answer = $this->request->code_answer;
		$mesage_answer = $this->request->mesage_answer;
		
		$queryInser = "INSERT INTO seguimiento_answerptesa 
						(number_transaction,state_response,message,
						reference,type_id,value_invoice,date_pay,
						number_autorization,trazability_code,code_answer,
						mesage_answer)
						VALUES ('$number_transaction','$state_response','$message','$reference',
						'$type_id','$value_invoice','$date_pay','$number_autorization',
						'$trazability_code','$code_answer','$mesage_answer')";
		 q($queryInser);
		
		$sql_select = "SELECT id FROM seguimiento_answerptesa ORDER BY id DESC LIMIT 1";
		$last_id = qo($sql_select);
		
		
	  $sqlUpdate = "UPDATE pago_online_seguimiento SET id_answerptsa = '$last_id->id'  WHERE numero_transaccion = '$number_transaction'";
	  
	  q($sqlUpdate);
	  
	  $sqlSelectSegumiento = "SELECT * FROM pago_online_seguimiento WHERE id_answerptsa = '$last_id->id'";
	  
	  $query_seguimiento = qo($sqlSelectSegumiento);
	  
	  
	  $sqlSelectDataFactura = "SELECT ofi.id of,fac.cliente,fac.id id_factura,sini.id siniestro_id FROM factura fac
								INNER JOIN siniestro sini ON fac.siniestro = sini.id
								INNER JOIN ciudad ciu ON sini.ciudad = ciu.codigo
								INNER JOIN oficina ofi ON ciu.codigo = ofi.ciudad 
								WHERE fac.consecutivo = '$query_seguimiento->factura_consecutivo'
								ORDER BY ofi.id  ASC LIMIT 1";
	 $query_fac_eguimiento = qo($sqlSelectDataFactura);
	  
	  
	  $sql_concepto = "SELECT * FROM 
	    recibo_caja_new
		WHERE factura = '$query_fac_eguimiento->id_factura' AND anulado !=1
		ORDER BY id DESC
		LIMIT 1";
		
	   $concepto = qo($sql_concepto);
	  
	  if($concepto){
		  
	  }
	  
	  
	  $fecha_hoy = date("Y-m-d");
	  $conceptoUtf = utf8_encode($concepto->concepto);
	  
	  $consecutivoXCiudad = "SELECT max(consecutivo) as ult FROM recibo_caja_new WHERE oficina = $query_seguimiento->ciudad";
	  $conseCiudad =  qo($consecutivoXCiudad);	 
	  
	  $countDijito = ($conseCiudad->ult) + 1;
	  if($countDijito){
		  $consecutivoXCiudad = $countDijito;
	  }
	  $sql_insert = "INSERT INTO recibo_caja_new 
					(fecha,oficina,consecutivo,cliente,valor,concepto,siniestro,factura,
					capturado_por,base,iva,total,total_abonado) VALUES ('$fecha_hoy',
					'$query_seguimiento->ciudad','$countDijito',
					'$query_fac_eguimiento->cliente','$query_seguimiento->total_pago',
					'$conceptoUtf','$query_fac_eguimiento->siniestro_id',
					'$query_fac_eguimiento->id_factura','$this->Nusuario',
					'$query_seguimiento->total_pago','$query_seguimiento->iva','$query_seguimiento->total_pago',
					'$query_seguimiento->total_pago')";
	 $inserResivoCaja =   q($sql_insert);
	  
	  if($code_answer == 200){
	    
		if($state_response){
			
			$asuntoCorreo = "Pago exitoso de factura $query_seguimiento->factura_consecutivo";
			$mensaje = "Se adjunta recibo de caja #$inserResivoCaja";
			$reciboArchivo = $this->pdf->imprimir_recibo($inserResivoCaja);
			$data_mail_recibo = array(
			"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
			"enviarEmail" => "true",
			"para" => "jemesnavarro@aoacolombia.com",
			"copia" => "jemesnavarro@aoacolombia.com",
			"asunto" => $asuntoCorreo,
			"mensaje" => $mensaje,
			"archivo" => $reciboArchivo,
			"nameArchivo" => "Recibo de caja #$last_id->id"
			);
			// ServiEmail.php 
			$ch = curl_init();
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/enviarWpApp.php');
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail_recibo);
                        curl_exec($ch);
			curl_close($ch);	
		}
		
		
		
	  }else{
		 echo json_encode(array("status"=>"Ok","dataEndpoint" => "Algo esta mal, Cod:".$mesage_answer,"cambio de estado AOA" => false)); 
	  }
	  
	  
	  
	 
       echo json_encode(array("status"=>"Ok","dataEndpoint" => "Respuesta recibida con exito, ult:".$conseCiudad->ult." , ciudad:".$query_seguimiento->ciudad,"cambio de estado AOA" => true));
	   
	 }catch(Exception $e){
		echo json_encode(array("status"=>"NO","viewFactura"=>"Hubo un error")); 
	 
	 }
	  
}

	public function validarGarantias(){
		try{
		
		$idSiniestro = $this->request->idSiniestro;
		
		$sqlAseguradora = "SELECT aseguradora FROM siniestro WHERE id = $idSiniestro";
        
		$idSeguradora = qo($sqlAseguradora);
		
		$aseguradora = "SELECT garantia,garantia_consignada FROM aseguradora WHERE id = $idSeguradora->aseguradora";
		
		$verAseguradora = qo($aseguradora);
		
		if($verAseguradora->garantia != 0){
			$tarjeta_credito = true;
		}else{
			$tarjeta_credito = false;
		}
		
		if($verAseguradora->garantia_consignada != 0){
			$consignacion = true;
		}else{
			$consignacion = false;
		}
		
		$sqlProtecionTotal = "SELECT aseguradora FROM tarifa WHERE aseguradora = $idSeguradora->aseguradora AND concepto = 81";
		
		$protecion_total =  qo($sqlProtecionTotal);
		
		if($protecion_total->aseguradora != 0){
			$total_proetcion = true;
		}else{
			$total_proetcion = false;
			
		}
		
		$sqlNoRembolsable = "SELECT * FROM tarifa WHERE aseguradora = $idSeguradora->aseguradora AND concepto = 33";
		$valiNorembolsable = qo($sqlNoRembolsable);
		
		if($valiNorembolsable->id){
			$no_reembolsable = true;
		}else{
			$no_reembolsable = false;
		}
		
		echo json_encode(array("status"=>"Ok","tarjetaCredito"=>$tarjeta_credito,
		"consignacion"=>$consignacion,"protecion_total" => $total_proetcion,
		"no_reembolsable" =>$no_reembolsable));
		
		}catch(Exception $e){
			echo json_encode(array("status"=>"NO","mensaje"=>"Hubo un error"));
		}
		
		
	}
	
	public function infoGarantia(){
		try{
			
		function converNumber($number){
			return number_format($number);
		}	
		$idSiniestro = $this->request->idSiniestro;
		
		$tipoGarantia = $this->request->garantia;
		
		$sqlAseguradora = "SELECT aseguradora FROM siniestro WHERE id = $idSiniestro";
        
		$idSeguradora = qo($sqlAseguradora);
		
		$aseguradora = "SELECT garantia,garantia_consignada FROM aseguradora WHERE id = $idSeguradora->aseguradora";
		
		$verAseguradora = qo($aseguradora);
		
		if($verAseguradora->garantia != 0){
			
			$valor =  converNumber($verAseguradora->garantia);
			$mesaje_credito = "Garantia tarjeta de credito es de $ $valor COP";
			$valor_credito = $valor;
			
		}
		
		if($verAseguradora->garantia_consignada != 0){
			$valor = converNumber($verAseguradora->garantia_consignada);
			$mesaje_consiginacion = "Garantia en consignación es de $ $valor COP";
			$valor_consignacion = $valor;
		}
		
		
		$sqlProtecionTotal = "SELECT * FROM tarifa WHERE aseguradora = $idSeguradora->aseguradora AND concepto = 81";
		
		$protecion_total =  qo($sqlProtecionTotal);
		
		if($protecion_total->aseguradora != 0){
			
			$sqlIvaTarifa = "SELECT porc_iva FROM concepto_fac WHERE id = 81";
			$ivaTarifa =  qo($sqlIvaTarifa);
			$iva = "1.".str_replace($a,"",$ivaTarifa->porc_iva);
			
			$val = ($protecion_total->valor * $iva);
			
			
			$valor =  converNumber($val);
			$mesaje_protecion = "Garantia de proteción total es de $ $valor COP";
			$valor_protecion = $valor;
			
		}
		
		
		$sqlNoRembolsable = "SELECT id,valor FROM tarifa WHERE aseguradora = $idSeguradora->aseguradora AND concepto = 33";
		$valiNorembolsable = qo($sqlNoRembolsable);
		
		if($valiNorembolsable->id){
		
		$sqlIvaTarifa = "SELECT porc_iva FROM concepto_fac WHERE id = 33";
		$ivaTarifa =  qo($sqlIvaTarifa);
		
		$sqlCita = "SELECT dias_servicio FROM cita_servicio WHERE siniestro = $idSiniestro";
		$cita = qo($sqlCita);
		
		$a = array(".","0");
		$iva = "1.".str_replace($a,"",$ivaTarifa->porc_iva);
         
		

		
		$multiplicarDiasCitas = ($cita->dias_servicio * $valiNorembolsable->valor) * $iva;
		
		$valorTres = converNumber($multiplicarDiasCitas);
		$mensaje_noreembolsable = "Garantia no reembolsable es de $ $valorTres COP";
		
		$valor_norrembolsable = $valorTres;
		
		}
		
		
		
		
		if($tipoGarantia == "tarjeta_credito"){
			$mensajeInfo = $mesaje_credito;
			$v = $valor_credito;
		}else if($tipoGarantia == "protecion_total"){
			$mensajeInfo = $mesaje_protecion;
			$v = $valor_protecion;
		}else if($tipoGarantia == "consignacion"){
			$mensajeInfo = $mesaje_consiginacion;
			$v = $valor_consignacion;
		}else if($tipoGarantia == "no_reembolsable"){
			$mensajeInfo = $mensaje_noreembolsable;
			$v = $valor_norrembolsable;
		}
		
		
			
			
			echo json_encode(array("status"=>"Ok","mensaje"=>$mensajeInfo,"valor"=>$v));
		}catch(Exception $e){
			echo json_encode(array("status"=>"NO","viewFactura"=>"Hubo un error"));
		}
	}
	
	public function validarDepartamento($stringDepartamento){
	try{
        
		switch($stringDepartamento){
		case "ANTIOQUIA":$dep = "05"; break;case "ATLANTICO":$dep = "08";break;
		case "BOGOTA D.C.":$dep = "11";break;case "BOLIVAR":$dep = "13";break;
		case "BOYACA":$dep = "15";break;case "CALDAS":$dep = "17";break;case "CAQUETA":$dep = "18";break;
		case "CAUCA":$dep = "19";break;case "CESAR":$dep = "20";break;case "CORDOBA":$dep = "23";break;
		case "CUNDINAMARCA":$dep = "25";break;case "CHOCO":$dep = "27";break;case "HUILA":$dep = "41";break;
		case "LA GUAJIRA":$dep = "44";break;case "MAGDALENA":$dep = "47";break;
		case "META":$dep = "50";break;case "NARIÑO":$dep = "52";break;case "NORTE DE SANTANDER":$dep = "54";break;
		case "QUINDIO":$dep = "63";break;case "RISARALDA":$dep = "66";break;case "SANTANDER":$dep = "68";
		case "SUCRE":$dep = "70";break;case "TOLIMA":$dep = "73";break;case "VALLE DEL CAUCA":$dep = "76";break;
		case "ARAUCA":$dep = "81";break;case "CASANARE":$dep = "85";break;case "PUTUMAYO":$dep = "86";break;
		case "SAN ANDRES":$dep = "88";break;case "AMAZONAS":$dep = "91";break;case "GUAINIA":$dep = "94";break;case "GUAVIARE":$dep = "95";break;
		case "VAUPES":$dep = "97";break;case "VICHADA":$dep = "99";break;default: $dep = "11";
		
	   }
	   return $dep;
	}catch(Exception $e){
			echo json_encode(array("status"=>"NO","response"=>"500","mensaje"=>"Hubo un error")); 
		}
	}

	public function sendGarantia(){
		 try{
			 $idSiniestro = $this->request->idSiniestro;
			 $valorGarantia =  $this->request->valorGarantia;
			 $tipoGarantia = $this->request->garantia;
			 $numero_documento = $this->request->documento;
			 Requests::register_autoloader();
	  
	  $data_to_send = array(
	  "login" => "aoaPruebas",
	  "password" => "a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3",
	  "canal" => 3
	  ); 
	  
	  $headers = array(
		"content-type" => "application/json",
		"accept" => "application/json");
		
	  
	  $requestEmissionToken = Requests::post('https://serviciospruebas.ptesa.com.co:1093/eph/v1/seguridad/login',$headers,json_encode($data_to_send));
	  
	  $decoRequest = json_decode($requestEmissionToken->body);
	 
	  
	  $idPrograma =  $decoRequest->listaProgramas[0]->id;
	  
	  $tokenLoging = $decoRequest->token;
	  
	  $headersTwo = array(
				"content-type" => "application/json",
				"Authorization" => "Bearer ".$tokenLoging);
	
	$options = array('timeout' => 80,
		                 );
						 
	$sqlCiudad = "SELECT ciu.id FROM ciudad ciu
				INNER JOIN siniestro si ON ciu.codigo = si.ciudad
				WHERE si.id = $idSiniestro";
    
	$ciudadId = qo($sqlCiudad);		
		
	switch($tipoGarantia){
				 case "tarjeta_credito":
				 
				$getDataClFac =  qo("SELECT  ciu.departamento,ti.id tipo_documento,cli.* FROM cliente cli
									INNER JOIN tipo_identificacion ti ON cli.tipo_id = ti.codigo
									INNER JOIN ciudad ciu ON cli.ciudad = ciu.codigo
									WHERE cli.identificacion = '$numero_documento' ORDER BY cli.id DESC LIMIT 1");
				
				
				$sinCeroValue = str_replace(",","",$valorGarantia);
				
				if($getDataClFac->tipo_documento == 3){
					$getDataClFac->tipo_documento = 8;
				}
				
				
				$arrayConsultaCliente = array("consultarPagador" => array(
	                            "canal" => 3,
								"codigoVerificacion" => "5aeef9af9611f84ef06748611f60c3d7248d8470614e3675c4bd0e9209ed7df0",
								"numeroAuditoria" => "DEC", 
								"idOrganizacion" => 10000452,
								"origen"=> "aoaPruebas",
								"usuario" => "aoaPruebas",
								"tipoIdentificacion" => $getDataClFac->tipo_documento,
								"numeroIdentificacion" => $numero_documento,
								"numeroContrato" => "A32435"
							));
	
    
	$requestEmissionConsulta = Requests::post('https://serviciospruebas.ptesa.com.co:9443/services/pagador/consultar',$headers,json_encode($arrayConsultaCliente),$options);
	
	
	$respuestaLink = json_decode($requestEmissionConsulta->body);
	
	$met = new FacturasHubClientes($request);		
	
	$arrayEnvioRegistro = array("pagador" => array(
	                             "canal" => 3,
								 "codigoVerificacion" => "5aeef9af9611f84ef06748611f60c3d7248d8470614e3675c4bd0e9209ed7df0",
								 "numeroAuditoria" => "DEC",
								 "idOrganizacion" => 10000452,
								 "origen" => "aoaPruebas",
								 "usuario" => "aoaPruebas",
								 "tipoIdentificacion" => $getDataClFac->tipo_documento,
								 "numeroIdentificacion" => $numero_documento,
								 "idDepartamento" => $met->validarDepartamento($getDataClFac->departamento),
								 "fechaNacimiento" => "2000-02-02",
								 "nombre" => utf8_encode($getDataClFac->nombre),
								 "apellidos" => utf8_encode($getDataClFac->apellido),
								 "correoElectronico" => $getDataClFac->email_e,
								 "telefonoFijo" => $getDataClFac->telefono_casa,
								 "telefonoCelular" => $getDataClFac->celular,
								 "idCiudad" => substr($getDataClFac->ciudad, 2, -3),
								 "idEstadoCivil" => "",
								 "contratoComercio" => 1,
								 "contratoptesa" => 1,
								 "autorizaSms" => 1,
								 "autorizaMail" => 1,
								 "empresa" => utf8_encode($getDataClFac->nombre),
								 "cargo" => "",
								 "ocupacion" => "",
								 "idAgencia" => 0,
								 "idAgente" => 0,
								 "saludo" => "1"
								));
	
	
	$requestEmissionRegistrar = Requests::post('https://serviciospruebas.ptesa.com.co:9443/pagador/adicionar',$headers,json_encode($arrayEnvioRegistro),$options);
	
	
	
	
	
	$arrayEndpoint = array("codigoRespuesta" => $respuestaLink->codigoRespuesta,
						   "mensajeRespuesta" => $respuestaLink->mensajeRespuesta,
						   "numeroAuditoria" => $respuestaLink->numeroAuditoria,
						   "numeroTransaccion" => $respuestaLink->numeroTransaccion,
						   "link" => $respuestaLink->link);
	$date = date("Y-m-d H:i:s");
	
	$sql = "INSERT INTO pago_online_seguimiento
	(numero_transaccion,numero_auditoria,mensaje_respuesta,
	fecha,link_pago,codigo_respuesta,total_pago,iva,ciudad,tipo_envio)
	 VALUES ('".$arrayEndpoint['numeroTransaccion']."','".$arrayEndpoint['numeroAuditoria']."',
	 '".$arrayEndpoint['mensajeRespuesta']."','$date','".$arrayEndpoint['link']."'
	 ,'".$arrayEndpoint['codigoRespuesta']."',".intval($getDataClFac->total).",
	 ".intval($getDataClFac->iva).",'$ciudadId->id','2')";
    
	q($sql);
	
	echo json_encode(array("status"=>"Ok","viewGarantia"=>"entraServicio","dataEndpoint" => $arrayEndpoint));
				 
				 
				 
				 
				 break;
				 
				 case "protecion_total":
				 
				 break;
				 
				 case "consignacion":
				 
				 break;
				 
				 case "no_reembolsable":

				 break;				 
				 
			 }
			 
		}catch(Exception $e){
			echo json_encode(array("status"=>"NO","viewFactura"=>"Hubo un error")); 
		 }
	}
	
	
	
	
	public function receiveDataPaymentMethod(){
		
		try{
			
		$numberContract = $this->request->numberContract;
		$document = $this->request->document;
		$idPaymentMethod = $this->request->idPaymentMethod;
		
		
		$sqlInser = "INSERT INTO  medio_pago_garantia  (numberContract,document,idPaymentMethod) VALUES ($numberContract,$document,'$idPaymentMethod')";
		
		q($sqlInser);
		
		echo json_encode(array("status"=>"Ok","response"=>"200","mensaje" => "Registro con exito"));
		
		}catch(Exception $e){
			echo json_encode(array("status"=>"NO","response"=>"500","mensaje"=>"Hubo un error")); 
		}
		
		
	}
	
	
	
  
  

	
}
?>