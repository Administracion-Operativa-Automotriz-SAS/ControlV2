<?php

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
			$dias_servicio_flag = false;
			
			$sql = "Select * from aoacol_aoacars.siniestro where numero = '".$data->NUMERO_SINIESTRO."' and
			aseguradora in (4,10) ";
			
			$sql_data = $this->connect->query($sql);
			
			$siniestro = mysql_fetch_object($sql_data);
			
			if($siniestro != null )
			{				
				
				$Usiniestro = array(
						"vigencia_desde" => $data->FECHA_INICIO_POLIZA,
						"vigencia_hasta" => $data->FECHA_VENCE_POLIZA,						
						"id" => $siniestro->id, 	
				);

					if(isset($data->COD_DOCUM_ASEG))
					{
						$Usiniestro["asegurado_id"] = $data->COD_DOCUM_ASEG;
					}		
					
					if(isset($data->TELEFONO))
					{
						$Usiniestro["declarante_tel_resid"] = $data->TELEFONO;
					}
					
					if(isset($data->MOVIL)) 
					{
						if($data->MOVIL !=  $siniestro->declarante_celular) 
						{
							$Usiniestro["declarante_tel_otro"] =  $siniestro->declarante_celular;
						}
						
						$Usiniestro["declarante_celular"] = $data->MOVIL;
					}				
				
				
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
						
				
				$date_to_analize = strtotime('01-09-2018');
				
				$date_from_excel = strtotime($data->FECHA_INICIO_POLIZA);
				
				if(($date_from_excel >= $date_to_analize) && strtotime($siniestro->fec_siniestro) >= $date_to_analize )
				{									
					$Usiniestro["dias_servicio"] = 10;							
					
					$dias_servicio_flag = true;
				}	

				$sql = $this->connect->update("aoacol_aoacars.siniestro", $Usiniestro);				
				
				//echo $sql;
				
				$update = $this->connect->query($sql);
				
				///print_r($update);
				
				if($update)
				{
					
					//Doing things with call center
					
					$NUSUARIO = "Cargue masivo mapfre";				
				
					if(isset($data->MOVIL))
					{
						$varTelefonoCel = $data->MOVIL;
					
						$date = explode(" ",date('Y-m-d H:i:s'));			
					
						$H1 = $date[0];
						
						$H2 = $date[1];
						
						$sql = "insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($siniestro->id,'$H1','$H2','$NUSUARIO','Actualización de datos de contacto: Telefono Anterior <h2>$varTelefonoCel</h2> ',8)";	
						
						//echo $sql;
						
						$this->connect->query($sql);					
						
						$sql = "delete from call2infoerronea where siniestro = $siniestro->id";
						
						//echo $sql;
						
						$this->connect->query($sql);	
					}					
					
					if($dias_servicio_flag)
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
					array_push($response,array("linea"=>$linea,"estado"=>"Hubo un error en la actualización"));
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
		
		foreach($this->request->dataset as $data)
		{
			
		print_r($data);
		}
		
		
		
	}
	
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
	
