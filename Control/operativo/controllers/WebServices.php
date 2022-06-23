<?php  

/********************
 * Autor Original: Jesús Vega
 * 
 * Proyecto:  
 * Documentos relacionados: 
 * Descripción del script:
 * El script es un pool de endpoints que sirve como helper para solucionar problemas que hallan con cualquier tipo de servicio
 * Cambios:
 *Autor: Jesús Vega
 * 1. Se agregó la función de sesión de usuario 
 * Fecha:25/02/2019
 *********************/
	
	header('Access-Control-Allow-Origin: *');
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ERROR | E_PARSE);

	//header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/DbConnect.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/inc/funciones_.php");
	
	
	
	$request_body = file_get_contents('php://input');

	if($request_body)
	{
		   //print_r($request_body);
			$request = json_decode($request_body);
			//echo "make print";
			if(isset($request->acc))
			{
				$Wservices = new WebServices($request);			
				
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
	
	if($_REQUEST)
	{
		
		//print_r($_REQUEST);	
		
		if(isset($_REQUEST['acc']))
		{
			$request =  (Object)$_REQUEST;
			$ajax = new WebServices($request);
			$response = $ajax->$_REQUEST['acc']();
			
		}		

	}
		
	
	
	
	
	Class WebServices{
		
		
		
		function __construct($request){		
			$this->request = $request;
			$this->connect = new DbConnect();
		}
		
		public function verSegurosVehiculos(){
			
			//echo "Select * from aoacol_aoacars.seguros where id = ".$this->request->id;
			
			$sql = $this->connect->query("Select * from aoacol_aoacars.seguros where id = ".$this->request->id);
			$seguro = $this->connect->convert_object($sql);
			
			
			
			echo json_encode(array("status"=>"OK","seguros"=>$seguro));
		
		}
		
		public function validarPlaca(){
			
			
			
			$placa =  $this->request->placaValidar;
			
			$sql = "Select id from aoacol_aoacars.vehiculo WHERE placa = '$placa'";
			
			
			$query = $this->connect->query($sql);
			
			
			$ValidaPlaca = $this->connect->convert_object($query);
			
			if($ValidaPlaca){
				$var = true;
			}else{
				$var = false;
			}
			
			echo json_encode(array("status"=>"OK","validarPlaca"=>$var));
		}
		
		public function validarSeguros(){
			
			
			
			$poliza =  $this->request->seguroValidar;
			
			$sql = "Select id from aoacol_aoacars.seguros WHERE n_poliza = '$poliza'";
			
			
			
			$query = $this->connect->query($sql);
			
			
			$ValidaPoliza = $this->connect->convert_object($query);
			
			if($ValidaPoliza){
				$var = true;
			}else{
				$var = false;
			}
			
			echo json_encode(array("status"=>"OK","validarPoliza"=>$var));
		}
		
		
		
		public function registroTablaTemp(){
			
			$sql = $this->connect->query("Select * from aoacol_aoacars.seguros where id = ".$this->request->id);
			$seguro = $this->connect->convert_object($sql);
			$numPoliza = $seguro->n_poliza;
			$fecha = date('Y-m-d H:i:s');
			$placa = $this->request->placa;
			$sqlDos = $this->connect->query("select id from vehiculo where placa = '$placa'");
			$idVehiculo = $this->connect->convert_object($sqlDos);
			$idVe = $idVehiculo->id;
			
			$arrayTabla = array(
			"numero_poliza" => $numPoliza,
			"id_vehiculo" => $idVe,
			"fecha_cambio" => $fecha
			);
			
			
			
			$sql = $this->connect->insert("aoacol_aoacars.historico_poliza",$arrayTabla);
			
			
			$history = q($sql);
			
			
			$history2 = $this->connect->convert_object($history);
			
			
			
			
			
			echo json_encode(array("status"=>"OK","history"=>$history));
			
		}
		
		public function registroTablaTempImpuesto(){
			
			$placa = $this->request->placa;
			
			$sqlHelper = qo("select id from vehiculo where placa = '$placa'");
			
			$sql = "SELECT  id,valor_impuesto_cargo,avalulo_comercial,sanciones_vs,
					valor_a_pagar,valor_semaforizacion,descuento_pronto_pago,
					interes_de_mora,total_a_pagar,pago_voluntario,total_con_pago_voluntario,
					caratura_impuesto_f,primera_fecha_hasta,segunda_fecha_hasta,fecha_de_pago
					,numero_referencia_recaudo,secretaria_hacienda FROM vehiculo WHERE id = ".$sqlHelper->id;
			
			
			$consult = qo($sql);
			
			$arrayImpuesto = array("id_vehiculo" => $consult->id,"valor_impuesto_cargo" => $consult->valor_impuesto_cargo, 
			"avalulo_comercial" => $consult->avalulo_comercial, "sanciones_vs" => $consult->sanciones_vs
			, "valor_a_pagar" => $consult->valor_a_pagar, "valor_semaforizacion" => $consult->valor_semaforizacion
			, "descuento_pronto_pago" => $consult->descuento_pronto_pago, "interes_de_mora" => $consult->interes_de_mora
			, "total_a_pagar" => $consult->total_a_pagar, "pago_voluntario" => $consult->pago_voluntario
			, "total_con_pago_voluntario" => $consult->total_con_pago_voluntario
			, "caratura_impuesto_f" => $consult->caratura_impuesto_f, "primera_fecha_hasta" => $consult->primera_fecha_hasta
			, "segunda_fecha_hasta" => $consult->segunda_fecha_hasta, "fecha_de_pago" => $consult->fecha_de_pago
			, "numero_referencia_recaudo" => $consult->numero_referencia_recaudo
			, "secretaria_hacienda" => $consult->secretaria_hacienda);
			
			$sql = $this->connect->insert("aoacol_aoacars.historial_impuesto",$arrayImpuesto);
			
		
			 
			
			$historyImpuestos = q($sql);
			
			echo json_encode(array("status"=>"OK","historyImpuesto"=>$historyImpuestos));
			
		}
		
		public function registroTablaTempSoat(){
			
			try{
				
			$placa = $this->request->placa;
			
			$fechaInicio = $this->request->fechaIni;
			
			
			$sqlHelper = qo("select id from vehiculo where placa = '$placa'");
			sleep(1);
			
			
			$sql = "SELECT id,fecha_desde_soat,numero_de_poliza_soat,
					fecha_expedicion_soat,fecha_hasta_soat,nombre_entidad_expide_soat,
					estado_soat,caratula_soat_f from vehiculo where id = ".$sqlHelper->id;
			
			$consult = qo($sql);
			
			
			
			$arraySoat = array("id_vehiculo" => $consult->id,"numero_soat" => $consult->numero_de_poliza_soat,
			                   "fecha_expedicion" => $consult->fecha_expedicion_soat,"fecha_inicio_vijencia"
							   => $consult->fecha_desde_soat, "fecha_fin_vigencia" => $consult->fecha_hasta_soat,
							   "entida_expide" => $consult->nombre_entidad_expide_soat,"estado"=> $consult->estado_soat,
							   "caratula_soat_f" => $consult->caratula_soat_f
							   );
			var_dump($arraySoat);
			//Validacion previa al insert
			
			$sqlHistorial = "select * from historial_soat where id_vehiculo = $sqlHelper->id ORDER by id DESC LIMIT 1";
			$y =  qo($sqlHistorial);
			if($y){
			 if($y->fecha_inicio_vijencia == $fechaInicio){
				echo json_encode(array("status"=>"NO")); 
			 }else{
				 $sql = $this->connect->insert("aoacol_aoacars.historial_soat",$arraySoat);
			 } 	
			}else{
				$sql = $this->connect->insert("aoacol_aoacars.historial_soat",$arraySoat);
			}
			
			
			
			
			$historySoat = q($sql);
			
			echo json_encode(array("status"=>"OK","historySoat"=>$historySoat));
			
		   }catch(Exception $e){
				echo json_encode(array("status"=>"500")); 
			}
            
			
			
			
			
		}
		
		public function get_history_poliza(){
			$placa = $this->request->placaHistory;
			$sqlHelper = qo("select id from vehiculo where placa = '$placa'");
			
			$sqlConsulta = "SELECT  
							hi.numero_poliza,hi.fecha_cambio,se.aseguradora_nombre,
							se.vigencia_desde,se.vigencia_hasta
							from historico_poliza hi 
							INNER JOIN seguros se ON hi.numero_poliza = se.n_poliza
							where hi.id_vehiculo = ".$sqlHelper->id." ORDER BY hi.id desc";
			
			$rowsCon = q($sqlConsulta);
			
			$queryCon = $this->connect->convert_objects($rowsCon);
			 
			echo json_encode(array("status"=>"OK","historyGet"=>$queryCon));
		}
		
		public function get_history_soat(){
			$placaSoat = $this->request->placaHistorySoat;
			
			$sqlHelper = qo("select id from vehiculo where placa = '$placaSoat'");
			
			
			$sqlConsultaSoat = "SELECT  numero_soat,fecha_expedicion,
			fecha_inicio_vijencia,fecha_fin_vigencia,entida_expide,estado,caratula_soat_f
			FROM historial_soat WHERE id_vehiculo = ".$sqlHelper->id." ORDER BY fecha_inicio_vijencia desc";
			

			$filasRow = q($sqlConsultaSoat);
			
			$queryConSoat = $this->connect->convert_objects($filasRow);
			 
			echo json_encode(array("status"=>"OK","historyGetSoat"=>$queryConSoat));
		
		}
		
		public function get_history_impuesto(){
			
			$placaImpuesto = $this->request->placaHistoryImpuesto;
			
			$slqHelper = qo("select id from vehiculo where placa = '$placaImpuesto'");
			
			$sqlConsultaImpuesto = "SELECT * FROM  historial_impuesto where id_vehiculo = ".$slqHelper->id." ORDER BY fecha_de_pago DESC";
			 
			
			$rows  = q($sqlConsultaImpuesto);
			
			$queryConstSoat = $this->connect->convert_objects($rows);
			
			
			echo json_encode(array("status" => "OK" ,"historyGetImpuesto" => $queryConstSoat));
		}
		
		public function asignar_imagen(){
			try{
				
			   $idRecepcion = $this->request->idTablaRecepcion;
					
			   $ruta = directorio_imagen_tres('ingreso_recepcion',$idRecepcion);
			   
			   $rutaDos = str_replace("../../../Administrativo/", "", $ruta);
			   $hoy2 = date("YmdHis");
			   $filepath_dos =  $rutaDos."foto_f_arribo_$hoy2".".png";
			   
			   
			  $decoded = urldecode($this->request->fotoEnviar);
			  
			  $exp = explode(',', $decoded);
			  
			  $base64 = array_pop($exp);
			  
			  $data = base64_decode($base64);
			  $filepath = $ruta."foto_f_arribo_$hoy2".".png";
			  
			  
			  $newFunctin = new WebServices();
			  $queryValidar = qo("select foto_f from aoacol_administra.ingreso_recepcion where id=$idRecepcion");
			  if($queryValidar->foto_f != ''){
				  $newFunctin->deleteDir($ruta);
			      file_put_contents($filepath,$data);
			  }else{
				  file_put_contents($filepath,$data);
			  }
			   
			   /*Guardado a la tabla de ingreso a recepcion*/
			   q("update aoacol_administra.ingreso_recepcion set foto_f='$filepath_dos' where id=$idRecepcion");
			   
			   /*Guardado bitacora*/
			  q("insert into aoacol_administra.app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
				values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "',
				'" . date('s') . "','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','ingreso_recepcion','M','$Id','" . $_SERVER['REMOTE_ADDR'] . "','Modifica:foto_f ingresa imagen')");
			

            
		 echo json_encode(array("desc" => "Subida con exito!","estado" => 1,"rutaImagenFinal" => "https://app.aoacolombia.com/Administrativo/".$filepath_dos));
			
		}catch(Exception $e){
			
		  echo json_encode(array("estado"=>0,"desc"=>"Ocurrio un error inesperado")); 
		  
		}
			
			
					
		}
		
		public function crearRemplazoSiniestro(){
			try{
				
			$idRecepcion = $this->request->idSi;
						 
			$query = q1("SELECT * FROM siniestro WHERE id = $idRecepcion");
			
			$queryCon = $this->connect->convert_objects($query);
			$arrayTable = json_decode(json_encode($queryCon), true);
			
			$ralla =  substr($arrayTable[0]['numero'], -2);
			
			if($ralla[0] === "-"){
				 $arrayNumero =  substr($arrayTable[0]['numero'], 0, -2);
				 $r = $ralla[1]+1;
				 
			 }else{
				 $arrayNumero = $arrayTable[0]['numero'];
				 $r = 1;
				 
			 }			
			
			$arrayTable[0]['numero'] = $arrayNumero."-".$r;
		    
			$arrayTable[0]['estado'] = 5;
			
			$sql = "SELECT fec_devolucion FROM  cita_servicio  WHERE siniestro = $idRecepcion LIMIT 1";
			
			$feCita = qo($sql);
			
			$diaServicio = $arrayTable[0]['dias_servicio'];
			
			
			$hoy  = date("Y-m-d");
			
			$fechaHoy = new DateTime($hoy);
			$feDevo = new  DateTime($feCita->fec_devolucion);
			$diff = $fechaHoy->diff($feDevo);
			$vD = $diff->days;
			
			$arrayTable[0]['dias_servicio'] = $vD;
			
			$arrayTable[0]['img_odo_salida_f'] = ""; $arrayTable[0]['img_inv_salida_f'] = "";
			$arrayTable[0]['fotovh1_f'] = ""; $arrayTable[0]['fotovh2_f'] = "";
			$arrayTable[0]['fotovh3_f'] = ""; $arrayTable[0]['fotovh4_f'] = "";
			$arrayTable[0]['img_contrato_f'] = ""; $arrayTable[0]['eadicional1_f'] = "";
			$arrayTable[0]['eadicional2_f'] = ""; $arrayTable[0]['congelamiento_f'] = "";
			$arrayTable[0]['gastosf_f'] = ""; $arrayTable[0]['img_cedula_f'] = "";
			$arrayTable[0]['img_pase_f'] = ""; $arrayTable[0]['adicional1_f'] = "";
			$arrayTable[0]['adicional2_f'] = ""; $arrayTable[0]['adicional3_f'] = "";
			$arrayTable[0]['adicional4_f'] = ""; $arrayTable[0]['img_carta_autorizacion_f'] = "";
			$arrayTable[0]['img_fotocopia_poliza_f'] = ""; $arrayTable[0]['img_camara_comercio_f'] = "";
			$arrayTable[0]['dadicional3_f'] = ""; $arrayTable[0]['dadicional4_f'] = "";
			
			
			utf8_encode($arrayTable[0]['asegurado_nombre']);
			utf8_encode($arrayTable[0]['declarante_nombre']);
			
			
			$arrayTable[0]['ubicacion'] = "";
			
			
			unset($arrayTable[0]['id']);
			$sql = $this->connect->insert("aoacol_aoacars.siniestro",$arrayTable[0]);
			
			$idNewSinister = q1($sql);
			
			
			$sql = "UPDATE aoacol_aoacars.sin_autor SET siniestro = $idNewSinister WHERE  siniestro =  $idRecepcion";
			q1($sql);
			
			$sqlUpdate = "UPDATE siniestro SET vh_remplazo = 1, pasoApp = 0, pasoAppDevo = 0 WHERE id = $idRecepcion";
			q1($sqlUpdate);
			
			echo json_encode(array("desc" => "Siniestro de remplazo montado con exito","estado" => 1,"siniestro" => $arrayTable[0]['numero'],"style" => "disabled" ));
			
			}catch(Exception $e){
				echo json_encode(array("estado"=>0,"desc"=>"Ocurrio un error inesperado")); 	
			}
		}	
		
		public function get_fact_last_cons()
		{
			$sql = $this->connect->query("Select * from resolucion_factura order by fecha desc limit 1");
			$resolucion_factura = $this->connect->convert_object($sql);
			
			//print_r($resolucion_factura);		
			
			$sql = $this->connect->query("Select * from factura where consecutivo like  '%".$resolucion_factura->prefijo."%' order by id desc LIMIT 1 ");		
			
			$factura = $this->connect->convert_object($sql);
			
			//print_r($factura);
			
			if($factura)
			{
				 $g_cons = str_ireplace($resolucion_factura->prefijo,"",$factura->consecutivo);
				 $g_cons += 1;				 
				 $g_cons =  $resolucion_factura->prefijo."".$g_cons;		 
			}
			else
			{
				$g_cons = $resolucion_factura->prefijo."".$resolucion_factura->consecutivo_inicial;
			}
			
			echo json_encode(array("status"=>"OK","consecutive"=>$g_cons));
			
		}
		
		
		public function get_customer_by_name()
		{			
			$sql = $this->connect->query("select *  from cliente where concat(nombre,' ',apellido) LIKE '%".$this->request->name."%' order by id ");		
			

			$clientes = $this->connect->convert_objects($sql);
			
			echo json_encode(array("status"=>"OK","customers"=>$clientes));
		}
		
		/*Metodo de recursividad: Elimino primero todos los archivos dentro con unlink y luego con rmdir la carpeta*/
		public static function deleteDir($dirPath) {
			   if (! is_dir($dirPath)) {
				   throw new InvalidArgumentException("$dirPath must be a directory");
			   }
			   if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
				   $dirPath .= '/';
			   }
			   $files = glob($dirPath . '*', GLOB_MARK);
			   foreach ($files as $file) {
				   if (is_dir($file)) {
					   self::deleteDir($file);
				   } else {
					   unlink($file);
				   }
			   }
			   //rmdir($dirPath);
		}
		
		public function get_info_from_docs_M()
		{			
			
			$query = "select *  from aoacol_aoacars.docs_manuales_electronicos where consecutivo =  '".$this->request->consecutive."'";
						
			$sql = $this->connect->query($query);			

			$doc = $this->connect->convert_object($sql);
			
			echo json_encode(array("status"=>"OK","document"=>$doc));			
			
		}
		
		public function getSessions()
		{
			session_start();
			//echo "sesión";
			
			echo json_encode(array("status"=>"OK","Nick"=>utf8_decode($_SESSION["Nick"]),"Role"=>$_SESSION["User"]));			
			
		}
		
	}
?>