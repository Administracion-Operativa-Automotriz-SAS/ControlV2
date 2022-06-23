<?php
header("Content-type: text/html; charset=utf8");
//include('inc/sess.php');

session_start();

if($_SESSION == null)
{
	exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE | E_NOTICE);

include_once("DbConnect.php");



$request_body = file_get_contents('php://input');

if($request_body)
{

	$request = json_decode($request_body);	
	$report = new LoadController($request);			
	$acc = $request->acc; 
	echo json_encode($report->$acc($request));
}
else
{
	header('HTTP/1.0 403 Forbidden');

	echo 'You are forbidden!';
}

class LoadController
{
	function __construct($request){
		$this->request = $request;
		$this->connect = new DbConnect();
	}
	
	public function load_mapfre_data()
	{		
		$linea = 2;
		$response = array();
		
		foreach($this->request->dataset as $data)
		{
			$sql = "Select * from aoacol_aoacars.siniestro where numero = '".$data->NUMERO_SINIESTRO."' and
			aseguradora in (4,10) ";
			
			$sql_data = $this->connect->query($sql);
			
			$siniestro = mysql_fetch_object($sql_data);
			
			if($siniestro != null )
			{				
				
				$auditoria_sql = $this->connect->insert("seguimiento",
				   array("siniestro"=>$siniestro->id,
				   "fecha"=>date('Y-m-d'),
				   "hora"=>date('H:i:s'),
				   "usuario"=>$_SESSION["Nombre"],
				   "descripcion"=>"Actualizacion por interfaz de carga de informacion",
				   "tipo"=>1));
				
				$validate = "select * from seguimiento where descripcion = 'Actualizacion por interfaz de carga de informacion'
				and siniestro = '".$siniestro->id."'";
				
				$sql_data = $this->connect->query($validate);
			
				$seguimiento = mysql_fetch_object($sql_data);
				
				if($seguimiento == null)
				{
					$this->connect->query($auditoria_sql);
				}
		
				$sql = "update aoacol_aoacars.siniestro set `vigencia_desde` = '".$data->FECHA_INICIO_POLIZA."'
				, `vigencia_hasta` =  '".$data->FECHA_VENCE_POLIZA."' where id = ".$siniestro->id;

				$date_to_analize = strtotime('01-09-2018');
				$date_from_excel = strtotime($data->FECHA_INICIO_POLIZA);
				 
				
				if(($date_from_excel >= $date_to_analize) && strtotime($siniestro->fec_siniestro) >= $date_to_analize )
				{
					$sql = "update aoacol_aoacars.siniestro set `vigencia_desde` = '".$data->FECHA_INICIO_POLIZA."'
					, `vigencia_hasta` =  '".$data->FECHA_VENCE_POLIZA."' , dias_servicio = '10' where id = ".$siniestro->id;
					$dias_servicio_flag = true;
				}
				
				$update = $this->connect->query($sql);				
				
				if($update)
				{
					if(isset($dias_servicio_flag))
					{
						array_push($response,array("linea"=>$linea,"estado"=>"actualizado y dias de servicio actualizado"));	
					}
					else
					{
						array_push($response,array("linea"=>$linea,"estado"=>"actualizado"));	
					}						
				}
				else
				{
					array_push($response,array("linea"=>$linea,"estado"=>"ya tiene fecha de vigencia"));
				}
				
			}
			
			else
			{
				array_push($response,array("linea"=>$linea,"estado"=>"no encontrado"));	
			}
			
			$linea++;
			
		}
		
		$details = array("details"=>$response);
		
		$response = array_merge(array("status"=>"OK"),$details);
		
		return $response;
		
	}
	 
	public function subir_datos_requisiciones(){
	$linea = 2;
	$response = array();	
		
	function quitar_tildes($cadena) {
     $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
     $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
     $texto = str_replace($no_permitidas, $permitidas ,$cadena);
     return $texto;
    }
		
		foreach($this->request->dataset as $data)
		{
			
			 /*ID DE ITEM*/
			$sqlITEM = 'select id from provee_produc_serv WHERE nombre = "'.$data->ITEM.'";';
			$varQuitar =  quitar_tildes($sqlITEM);
			$sql_data_item = $this->connect->query($varQuitar);
			$item = mysql_fetch_object($sql_data_item);
			/*ID DE ITEM*/
			
			/*CLASE*/
			$sqlClaseRequisicion = "select id from requisicionc WHERE nombre = '".$data->CLASE_DE_REUQICISION."';";
			$sql_data = $this->connect->query($sqlClaseRequisicion);
			$clase = mysql_fetch_object($sql_data);
			/*CLASE*/		  
			
			/*ID DE PLACA o PROYECTO*/
			$sqlPLACA = "select id FROM  aoacol_aoacars.vehiculo WHERE placa = '".$data->PROYECTO_O_PLACA."';";
			$sql_data_placa = $this->connect->query($sqlPLACA);
			$placa = mysql_fetch_object($sql_data_placa);
			/*ID DE PLACA o PROYECTO*/
			
			/*ID CENTRO DE OPERACIONES*/
			$sqloPeraciones = "select id from aoacol_aoacars.oficina where centro_operacion = ".$data->CENTRO_DE_OPERACIONES.";";
			$sql_data = $this->connect->query($sqloPeraciones);
			$operaciones = mysql_fetch_object($sql_data);
			/*ID CENTRO DE OPERACIONES*/
			$re = array('requisicion' => $this->request->idrequisicion,
			            'tipo1'  =>  $item->id,
						'clase' =>  $clase->id,
						'valor' =>  $data->VALOR,
						'observaciones' => $data->OBSERVACIONES,
						'tipo_cobro' => $data->COBRO,
						'cantidad' => $data->CANTIDA,
						'centro_operacion' => $operaciones->id,
						'centro_costo' => $data->CENTRO_DE_COSTOS,
						'valor_total' => $data->VALOR_TOTAL,
						'id_vehiculo' => $placa->id,
						'factor' => $data->FACTOR);
			
			$sqlInsert = $this->connect->insert("requisiciond",$re);
			$Insert = $this->connect->query($sqlInsert);
			$insercion_bien = true;
			if($Insert){
			   if(isset($insercion_bien)){
				   array_push($response,array("linea"=>$linea,"estado"=>"Datos insertados a esta requicision"));	
			   }else{
				   array_push($response,array("linea"=>$linea,"estado"=>"Insertado"));
			   }	
			}else{
			    array_push($response,array("linea"=>$linea,"estado"=>"No se pudo"));	
			}
			$linea++;
			
		}
		$details = array("details"=>$response);
		
		$response = array_merge(array("status"=>"OK"),$details);
		
		return $response;
	}
	
	public function subir_datos_seguros(){
		$linea = 2;
	$response = array();	
		
	
		
		foreach($this->request->dataset as $data)
		{
			
			$sqlVehiculo = "select id from aoacol_aoacars.vehiculo where placa = '".$data->PLACA."'";
			$sql_data = $this->connect->query($sqlVehiculo);
			$placa = mysql_fetch_object($sql_data);
			
			$sqlSeguro = "select id from aoacol_aoacars.seguros where n_poliza = '".$data->POLIZA."'";
			$sql_data = $this->connect->query($sqlSeguro);
			$idSeguro = mysql_fetch_object($sql_data);
			
			
			//$sqlInsert = $this->connect->insert("aoacol_aoacars.seguros",$re);
		    $sqlInsert = "UPDATE aoacol_aoacars.vehiculo SET n_poliza = ".$idSeguro->id." WHERE id = ".$placa->id."";
			
			$Insert = $this->connect->query($sqlInsert);
			
			
			$insercion_bien = true;
			if($Insert){
			   if(isset($insercion_bien)){
				   array_push($response,array("linea"=>$linea,"estado"=>"Datos insertados a esta requicision"));	
			   }else{
				   array_push($response,array("linea"=>$linea,"estado"=>"Insertado"));
			   }	
			}else{
			    array_push($response,array("linea"=>$linea,"estado"=>"No se pudo"));	
			}
			$linea++;
			
		}
		$details = array("details"=>$response);
		
		$response = array_merge(array("status"=>"OK"),$details);
		
		return $response;
		
	}
	/*
	public function subir_datos_seguros(){
		$linea = 2;
	$response = array();	
		
	
		
		foreach($this->request->dataset as $data)
		{
			
			
			$re = array('n_poliza' => $data->POLIZA,
			            'vigencia_desde'  =>  $data->VIGENCIA_DESDE,
						'vigencia_hasta' =>  $data->VIGENCIA_HASTA,
						'certificado_anexo' =>  $data->CERTIFICADO,
						'aseguradora_nombre' => $data->ASEGURADORA,
						'corredor' => $data->CORREDOR,
						'cobertura_uno' => $data->COBERTURA_UNO,
						'cobertura_dos' => $data->COBERTURA_DOS,
						'codigo_fasecolda' => $data->Fasecolda,
						'codigo_facecolda_2' => $data->Fasecolda1,
						'valor_asegurado_renovacion' => $data->VALOR_ASEGURADO_RENOVACION,
						'fecha_envio' => $data->FECHA_DE_ENVIO,
						'comentarios' => $data->COMENTARIOS,
						'linea_asistencia' => $data->LINEA_DE_ASISTENCIA,
						'envio_numero' => $data->ENVIO_NUMERO);
			
			$sqlInsert = $this->connect->insert("aoacol_aoacars.seguros",$re);
		    $Insert = $this->connect->query($sqlInsert);
			echo $sqlInsert;
			
			$insercion_bien = true;
			if($Insert){
			   if(isset($insercion_bien)){
				   array_push($response,array("linea"=>$linea,"estado"=>"Datos insertados a esta requicision"));	
			   }else{
				   array_push($response,array("linea"=>$linea,"estado"=>"Insertado"));
			   }	
			}else{
			    array_push($response,array("linea"=>$linea,"estado"=>"No se pudo"));	
			}
			$linea++;
			
		}
		$details = array("details"=>$response);
		
		$response = array_merge(array("status"=>"OK"),$details);
		
		return $response;
		
	}
	*/

	
	public function load_mapfre_placas()
	{
		$linea = 2;		
		
		
		
		$response = array();
		
		foreach($this->request->dataset as $data)
		{
			$message = "";
			
			$sql = "SELECT * FROM aoacol_aoacars.siniestro where placa =  '".$data->PLACA."' and estado = 5 and aseguradora in (4,10) and dias_servicio != 7 ";
			
			$result = $this->connect->query($sql);			
			$siniestro = mysql_fetch_object($result);
			if($siniestro != null)
			{
				$auditoria_sql = $this->connect->insert("seguimiento",
				   array("siniestro"=>$siniestro->id,
				   "fecha"=>date('Y-m-d'),
				   "hora"=>date('H:i:s'),
				   "usuario"=>$_SESSION["Nombre"],
				   "descripcion"=>"Actualizacion por interfaz de carga de informacion de placas para mapfre",
				   "tipo"=>1));
				   
				$validate = "select * from seguimiento where descripcion = 'Actualizacion por interfaz de carga de informacion'
				and siniestro = '".$siniestro->id."'";
				
				$sql_data = $this->connect->query($validate);
			
				$seguimiento = mysql_fetch_object($sql_data);
				
				if($seguimiento == null)
				{
					$this->connect->query($auditoria_sql);
				}				
				
				$sql = "update aoacol_aoacars.siniestro set dias_servicio = '10' where id = ".$siniestro->id;
				
				$update = $this->connect->query($sql);
				
				if($update)
				{
					$message .= "Se actualizarón los dias de servicio";
				}
				
			}
			else
			{
				$sql = "Select * from aoacol_aoacars.futuras_placa_mapfre where PLACA = '".$data->PLACA."' ";
				$existence = $this->connect->query($sql);
				if($existence == null)
				{
				    $insert_placa = $this->connect->insert("aoacol_aoacars.futuras_placa_mapfre",
				   array("placa"=>$data->PLACA));
					
					$this->connect->query($insert_placa);
					
					$message .= "Placa incluida para los futuros siniestros";
				}
				else
				{					
					$message .= "La placa ya fue incluida para los futuros siniestros";
				}
				
				
			}

			array_push($response,array("linea"=>$linea,"estado"=>$message));
			$linea++;			
		}
		
		$details = array("details"=>$response);
		
		$response = array_merge(array("status"=>"OK"),$details);
		
		return $response;
		
	}
  public function load_bienes_servicios(){
	 $response = array();
	 
	 //$inser_table = "INSERT INTO provee_produc_serv";
	 foreach($this->request->dataset as $data){
		 
		//print_r($data);
		 
		 $sql = $this->connect->insert("aoacol_administra.provee_produc_serv",
		   array("tipo"=>$data->TIPO,
		   "nombre"=>utf8_decode($data->ITEM_NOMBRE),
		   "controlado"=>$data->CONTROLADO,
		   "proceso"=>$data->PROCESO,
		   "activacion"=>$data->ACTIVO,
		   "uso"=>$data->USO,
		   "sistema"=>$data->SISTEMA));
		 
		//echo  $sql;
		 
		  $consulta =  $this->connect->query($sql);
		  
		
	}
  }
	
}
	
