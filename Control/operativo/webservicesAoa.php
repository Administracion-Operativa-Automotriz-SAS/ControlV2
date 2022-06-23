<?php
include('inc/funciones_.php');

define ('APIKEYAOA','yNPlsmOGgZoGmH$7');
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 1);
header('Content-Type: application/json');

mysql_connect("app.aoacolombia.com", "aoacol_arturo", "AOA0l1lwpdaa");
mysql_select_db("aoacol_aoacars");

if(!$_REQUEST)
	{
		$request_body = file_get_contents('php://input');
		if($request_body && json_decode($request_body))
		{
			//echo "request body";
			$request_body = json_decode($request_body);
			//print_r($request_body);
			$_REQUEST = (array) $request_body;
			//print_r($_REQUEST);
		}
	}
	
	if(isset($_REQUEST['APIKEYAOA'])){
		if($_REQUEST['APIKEYAOA']!='yNPlsmOGgZoGmH$7'){
			echo json_encode("No tiene acceso");
			exit;
		}
	}else{
		echo json_encode("No tiene acceso");
		exit;
	}
	
	if(isset($_REQUEST['create_pqr'])){
		$service = new WebServiceAOA($_REQUEST);
		echo json_encode($service->create_pqr_class());
	}
	if(isset($_REQUEST['see_request_response'])){
		$service = new WebServiceAOA($_REQUEST);
		echo json_encode($service->see_request_response());
	}
	
	Class WebServiceAOA{
		
		function __construct($request){
			$this->request = $request;
			$this->left_params = "";
		}
		
		public function create_pqr_class(){
			try{
			$telefono = $this->request['TELEFONO_FIJO'];
			
			$celular = $this->request['TELEFONO_CELULAR'];
			
			if($celular !=""){
				$arrayTelefono = "";
				
			}else if($telefono != ""){
				$arrayTelefono = "";
			}else{
				return array("estado" => 3, "desc" => "Falta algun numero telefonico");
			}
			
			$arrayPqr = array(
			"NOMBRE_CLIENTE" => "nombre", 
			"CEDULA_CLIENTE" => "documento", 
			"DIRECCION_CLIENTE" => "direccion", 
			"EMAIL_CLIENTE" => "email", 
			"ASEGURADORA_PQR" => "aseguradora", 
			"TIPO_SOLICITUD_PQR" => "tipo_solicitud", 
			"COMENTARIOS_PQR" => "comentarios",
			"OFICINA" => "oficina"
			);
			
			if($this->check_request($arrayPqr)>0){
				
				return array("estado" => 2, "desc" => "Faltan datos para progresar la solicitud", "parametros"=>$this->left_params);
				exit();
			}
			
			$nombre = $this->request['NOMBRE_CLIENTE'];
			
			$documento = $this->request['CEDULA_CLIENTE'];
			
			$direccion = $this->request['DIRECCION_CLIENTE'];
			
			$email = $this->request['EMAIL_CLIENTE'];
			
			$placa_vehiculo = $this->request['PLACA_CLIENTE'];
			
			$aseguradora = $this->request['ASEGURADORA_PQR'];
			
			$tipo_solicitud = $this->request['TIPO_SOLICITUD_PQR'];
			
			$comentarios = $this->request['COMENTARIOS_PQR'];
			
			$oficina = $this->request['OFICINA'];
			
			
			$sqlDoplicado = "SELECT * FROM  pqr_solicitud WHERE numero_documento = '$documento' AND placa = '$placa_vehiculo' ORDER BY id DESC LIMIT 1";
			$v = qo($sqlDoplicado);
			
			if($v){
			   $sqlEstado = "SELECT t_pqr_estado_respuesta(pqr_estado_respuesta) AS nestado 
							FROM pqr_respuesta
							WHERE solicitud = ".$v->id; $t = qo($sqlEstado);
			   
			   if($t->nestado != 'RESUELTO'){
				 return array("estado1" => 1, "numero_pqr" => "Tiene una solicitud pendiente aun numero: $v->id");
			     exit();    
			   }
			
			}
				$hoy = date("Y-m-d H:i:s");
			
				$query = "INSERT INTO pqr_solicitud (cliente,numero_documento,direccion,email_e,placa,aseguradora,tipo_solicitud,tipo_solicitud_nombre,descripcion,fecha,fecha_recibido,telefono,celular,oficina)
				values ('$nombre','$documento','$direccion','$email','$placa_vehiculo','$aseguradora','$tipo_solicitud','$tipo_solicitud','$comentarios','$hoy','$hoy','$telefono','$celular','$oficina');";
			    
				$Id_nuevo = q($query);
			     return array("estado1"=>1,"numero_pqr"=>"Solicitud PQR ". $Id_nuevo ." fue grabada satisfactoriamente");
			
			
			}catch(Exception $e){
				
				return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
			}
			
			
			
		}
		
		public function see_request_response(){
			$arrayVer = array("DOCUMENTO_SOLICITANTE" => "documento_solicitante", "NUMERO_SOLICITUD" => "numero_solicitud");
			
			if($this->check_request($arrayVer)>0){
				return  array("estado" => 5, "desc" => "Faltan datos para progresar la solicitud", "parametros"=>$this->left_params);
			}
			
			$documento_solicitante = $_REQUEST['DOCUMENTO_SOLICITANTE'];
			
			$numero_solicitante = $_REQUEST['NUMERO_SOLICITUD'];
			
			
			$querySee = "SELECT r.id, t_pqr_estado_respuesta(pqr_estado_respuesta) AS nestado,r.descripcion,r.fecha
						FROM pqr_respuesta r 
						INNER JOIN pqr_solicitud s ON r.solicitud = s.id
						WHERE r.solicitud = $numero_solicitante AND s.numero_documento = $documento_solicitante
						AND t_pqr_estado_respuesta(pqr_estado_respuesta) != 'VALIDACION'
						GROUP BY nestado
						";
			
			
			$querySolicitud = "SELECT s.id AS numero_pqr,cliente nombre_solicitante,s.numero_documento,s.telefono telefono_o_celular,paseg.nombre nombre_aseguradora
								,s.email_e,aoacol_aoacars.t_oficina(oficina) AS oficina,s.descripcion,s.fecha_recibido
								,t_pqr_estado(estado) AS estado
								FROM pqr_solicitud AS s
								left JOIN pqr_estado AS e ON s.estado = e.id
								left JOIN aoacol_aoacars.aseguradora AS paseg ON s.aseguradora = paseg.id where s.id = '$numero_solicitante' AND s.numero_documento = '$documento_solicitante'";
			
			$querySolicitud2 = qo("SELECT s.telefono,s.celular,s.fecha_recibido 
			                       FROM pqr_solicitud  s 
								   WHERE s.id = '$numero_solicitante' AND s.numero_documento = '$documento_solicitante'");
			
			
			
			$consultaDetalles = q($querySolicitud);
			if(!$consultaDetalles){
				return array("estado" => 2, "Aun no hay datos disponibles con el numero ".$numero_solicitante);
			}
			try{
			
			
			
			$respuestaSolicitudes = $this->fetch_objects_test($querySee);
			
			$solicitudPQR = $this->fetch_objects_test($querySolicitud);
			
		    $Consulta="SELECT r.id,t_pqr_solicitud(r.solicitud) AS nsolicitud, t_pqr_tipo_accion(pqr_tipo_accion) AS ntipoaccion,r.descripcion,
					   t_pqr_estado_respuesta(pqr_estado_respuesta) AS nestado, r.fecha, r.procesado_por, 
					   t_resarcimiento_pqr(r.id) AS resarcimiento
				       FROM pqr_respuesta r 
				       INNER JOIN pqr_solicitud s ON r.solicitud = s.id
				       WHERE r.solicitud = $numero_solicitante AND s.numero_documento = $documento_solicitante
				       AND t_pqr_estado_respuesta(pqr_estado_respuesta) != 'VALIDACION'
					   GROUP BY nestado
					   ";
			
			if($querySolicitud2->celular != ''){
				$telefono_cliente = $querySolicitud2->celular;
			}else{
				$telefono_cliente = $querySolicitud2->telefono;
			}
			
			if($Datos=q($Consulta)){
				while($c = mysql_fetch_object($Datos)){
					$respuesta_estado = $c->nestado;
				}
			}
			    foreach($solicitudPQR as $ireporte){
					$ireporte->nombre_aseguradora = utf8_encode($ireporte->nombre_aseguradora);
					 if($respuesta_estado !=''){
					   $estado_respuesta = $respuesta_estado;
					   }else{
					   if($ireporte->estado !=""){
						 $estado_respuesta = $ireporte->estado;  
					   }else{
						 $estado_respuesta = 'RECIBIDO';  
					   }
					  }

					  
						$ireporte->estado = $estado_respuesta;
						$ireporte->telefono_o_celular = $telefono_cliente;
				}
				
				$other = array('0' => array('id' => $numero_solicitante,
				'nestado' => 'RECIBIDO',
				'descripcion' => 'Hemos recibido su solicitud y se dará atención en los términos previstos por la regulación vigente.',
				'fecha' => $querySolicitud2->fecha_recibido
				));
			 
			    $respuestaSolicitudes = array_merge($other,$respuestaSolicitudes);
				
				if($respuestaSolicitudes == null){
					$respuestaSolicitudes = $other;
				}
				
				foreach($respuestaSolicitudes as $rowRespuesta){
					switch($rowRespuesta->nestado){
						case "EN TRAMITE":
						$respuestaDescripcion = "Nos encontramos estudiando su solicitud, agradecemos estar atento a su dirección de correo electrónico donde será enviada la respuesta.";
						break;
						case "RESUELTO":
						$respuestaDescripcion = "Hemos atendido su petición y podrá consultar en el correo electrónico registrado.";
						break;
						case "VALIDACION":
						$respuestaDescripcion = "";
						break;
					}
					$rowRespuesta->descripcion = $respuestaDescripcion;
				}
				
				
				
			
			$response = array("solicitud" => $solicitudPQR, "respuesta_solicitud"=> $respuestaSolicitudes);
			return $response;
			
			}catch(Exception $e){
				
				return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
			
			}
		
		}
		
		
		public function check_request($content)
		{
			$counter = 0;
			foreach($content as $key => $var)
			{
				if($this->request[$key]==null)
				{
					//echo "falta ".$var.",";
					$this->left_params .= $key.",";
					$counter++;
				}
			}

			return $counter;

		}
		private function fetch_objects_test($query)
		{
			$result = q($query);
		
			$rows = array();
			if($result != null)
			{
				while ($row = mysql_fetch_object($result))
				{
					array_push($rows, $row);
				}
				
				return $rows;
			}
			else
			{
				return null;	
			}			
		}
	
	
	}
	

?>