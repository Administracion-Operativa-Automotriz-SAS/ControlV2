<?php  
	header('Access-Control-Allow-Origin: *');
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ERROR | E_PARSE);

	include_once(dirname(__FILE__).'/../config/config.php');
	include_once(dirname(__FILE__).'/../config/resuelve.php');
	include 'log.php';
	//define('APIKEY','823//ac1*316f0*ed9ac872746$db6f9a3e2a$');
	//header('Content-Type: application/json');
	
	$apikeys = array('823//ac1*316f0*ed9ac872746$db6f9a3e2a$');
	
	
	$iparray = array('192.192.181.97','192.192.181.87','192.192.181.170','186.86.33.38','192.168.2.1','190.26.138.145');
	
	//$ipcount = 0;
	
	/*foreach($iparray as $ip)
	{
		if($_SERVER['REMOTE_ADDR'] == $ip)
		{
			$ipcount++;
		}
	}*/
	
	//echo $_SERVER['REMOTE_ADDR'];
	
	
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
	
	
	if(!in_array($_SERVER['REMOTE_ADDR'],$iparray))
	{
		echo json_encode("Acceso no permitido");
		wh_log($_SERVER['REMOTE_ADDR'].", Acceso no permitido  \n");
		exit;
	}

	if(isset($_REQUEST['APIKEY']))
	{

	if(!in_array($_REQUEST['APIKEY'],$apikeys))
		{
			wh_log($_SERVER['REMOTE_ADDR'].", Acceso restringido por pagina  \n");
			echo json_encode("Acceso restringido por pagina1");
			exit;
		}
	}else{
		wh_log($_SERVER['REMOTE_ADDR'].", Acceso restringido por pagina  \n");
		echo json_encode("Acceso restringido por pagina2");
		exit;
	}
	
	if(isset($_REQUEST))
	{
		wh_log($_SERVER['REMOTE_ADDR'].", ".json_encode($_REQUEST)."\n");
	}
	
	if(!isset($_REQUEST['ACC']))
	{
		wh_log($_SERVER['REMOTE_ADDR'].", No viene accion en el request  \n");
		echo json_encode("No viene accion en el request");
		exit;
	}

	if($_REQUEST['ACC']=="create_siniester")
	{
		$service = new GeneraliWebService($_REQUEST);
		$res = json_encode($service->create_siniester());
		wh_log($_SERVER['REMOTE_ADDR'].", $res  \n");
		echo $res;		
	}
	
	if($_REQUEST['ACC']=="test")
	{
		$service = new GeneraliWebService($_REQUEST);
		wh_log($_SERVER['REMOTE_ADDR']." test  \n");
		echo json_encode($service->test());		
	}
	
	Class GeneraliWebService{
		
		
		
		function __construct($request){		
			$this->request = $request;
			$this->lack_params = "";
    	}
		
		
		
		public function query($cadena, $Devolver_sql = 0,&$_Cantidad_registros_afectados=0) // corre un query invocado internamente
		{
			global $Nombre, $Id_alterno, $Num_Tabla,$LINK;

			if(!$LINK = mysql_connect(MYSQL_S, resuelve_usuario_mysql($cadena), MYSQL_P)) die('Problemas con la conexion de la base de datos!');
			mysql_query('SET collation_connection = utf8_general_ci',$LINK);
			if(!mysql_select_db(MYSQL_D, $LINK)) die('Problemas con la seleccion de la base de datos');
			if(strpos(' '.$cadena,'update ') || strpos(' '.$cadena,'alter table') || strpos(' '.$cadena,'insert '))
				mysql_query("set innodb_lock_wait_timeout=80",$LINK);
			else
				mysql_query("set innodb_lock_wait_timeout=20",$LINK);
			if($RQ = mysql_query($cadena, $LINK))
			{
				if($Devolver_sql)
				{
					mysql_close($LINK);
					return $RQ;
				}
				if(strpos(' ' . strtolower($cadena), 'insert '))
				{
					$IDR = mysql_insert_id($LINK);
					$_Cantidad_registros_afectados=mysql_affected_rows($LINK);
					mysql_close($LINK);
					return $IDR;
				}
				if(strpos(' ' . strtolower($cadena), 'update '))
				{
					$AFECTADAS = mysql_affected_rows($LINK);
					mysql_close($LINK);
					return $AFECTADAS;
				}
				if(strpos(' ' . strtolower($cadena), 'create'))
				{
					$_Cantidad_registros_afectados=mysql_affected_rows($LINK);
					mysql_close($LINK);
					return true;
				}
				if((strpos(' ' . strtolower($cadena), 'select ') || strpos(' ' . strtolower($cadena), 'show ') || strpos(' ' . strtolower($cadena), 'analyze ') || strpos(' ' . strtolower($cadena), 'check ') || strpos(' ' . strtolower($cadena), 'optimize ') || strpos(' ' . strtolower($cadena), 'repair ')
							) && (!strpos(' ' . strtolower($cadena), 'insert ') || !strpos(' ' . strtolower($cadena), 'update ')))
				{
					mysql_close($LINK);
					if($Devolver_sql) return $RQ;
					if(mysql_num_rows($RQ))
					{
						return $RQ;
					}
					else
					{
						return false;
					}
				}
			}
			else
			{
				$Error_de_mysql = mysql_error();
				mysql_close($LINK);
				if(strpos(' ' . $Error_de_mysql, 'Duplicate entry'))
				{
					$mensaje = "Entrada Duplicada, no se pudo ingresar el nuevo registro ";
					echo json_encode(array("estado"=>0,"desc"=>"Error de BD ".$mensaje));
					exit;	
									
				}
				elseif(strpos(' '.$Error_de_mysql,'Lock wait timeout exceeded') && strpos(' '.$cadena,'update') )
				{
					//q($cadena);
				}
				else
				{
					# debug_print_backtrace();
					echo json_encode(array("estado"=>0,"desc"=>"Error de BD ".$Error_de_mysql));
					exit;
					
				}
			}
		}
		
		public function create_siniester()
		{
			
			$array = array("NUMERO" => "numero del siniestro" ,"CIUDAD" => "ciudad de atencion" ,"PLACA" => "placa automovil",
			"ASEGURADO_NOMBRE" => "Nombre del asegurado","IDENTIFICACION" => "Identificación del asegurado","DECLARANTE_CELULAR" => "Celular del asegurado",
			"CIUDAD_ORIGINAL" => "Ciudad del cliente","CIUDAD_SINIESTRO" => "ciudad del siniestro","DIAS_SERVICIO" => "tiempo de servicio");

			if($this->check_request($array)>0)
			{
				return array("estado"=>2,"desc"=>"Faltan datos para procesar la solicitud ".$this->lack_params);
			}
			else{
				
				$numero = $this->request["NUMERO"];
				
				if($this->validate($numero) == 1)
				{
					return array("estado"=>3,"desc"=>"Ya existe este siniestro en la BD");
				}			
				
				$siniestro = new stdClass(); 
				
				$siniestro->ciudad = $this->request["CIUDAD"];
				$siniestro->fec_autorizacion = date('Y');
				$siniestro->fec_siniestro = date('Y');
				$siniestro->fec_declaracion = date('Y');
				$siniestro->placa = $this->request["PLACA"];
				$siniestro->estado = 5;
				$siniestro->declarante_celular = $this->request["DECLARANTE_CELULAR"];
				$siniestro->asegurado_nombre = $this->request["ASEGURADO_NOMBRE"];
				$siniestro->asegurado_id = $this->request["IDENTIFICACION"];
				$siniestro->declarante_id = $this->request["IDENTIFICACION"];
				$siniestro->declarante_nombre = $this->request["ASEGURADO_NOMBRE"]; 
				$siniestro->ciudad_original = $this->request["CIUDAD_ORIGINAL"];
				$siniestro->ciudad_siniestro = $this->request["CIUDAD_SINIESTRO"];
				$siniestro->dias_servicio = $this->request["DIAS_SERVICIO"];
				$siniestro->aseguradora = $this->aseg_apykey($this->request["APIKEY"]);
				$siniestro->ingreso =  date("Y-m-d h-i-s");
				$siniestro->numero = $numero;
				
				if($siniestro->aseguradora == null)
				{
					return array("estado"=>4,"desc"=>"No hay una aseguradora registrada al api key");
				}
				
				$sql = $this->insert("siniestro",$siniestro);
				
				
				/*$sql="INSERT INTO siniestro (numero,ciudad,fec_autorizacion,fec_siniestro,fec_declaracion,placa,estado,
				asegurado_nombre, asegurado_id, declarante_nombre, declarante_id, declarante_celular, aseguradora,
				ciudad_original,ingreso,ciudad_siniestro, dias_servicio)  VALUES ('$numero','$ciudad','$fec_autorizacion',
				'$fec_siniestro','$fec_declaracion','$placa','$estado','$asegurado_nombre','$identificacion','$asegurado_nombre',
				'$identificacion','$declarante_celular','$aseguradora','$ciudad_original',CURRENT_TIMESTAMP,'$ciudad_siniestro',
				'$dias_servicio') ";
				
				$this->query($sql);*/

				$this->query($sql);
				
				return array("estado"=>1,"desc"=>"Siniestro Creado","sql"=>$sql);
				
			}
			
			
		}
		
		
		public function aseg_apykey($key)
		{
			switch ($key) {
				case '823//ac1*316f0*ed9ac872746$db6f9a3e2a$':
					$aseguradora = 93;
					break;
				case '823//ac1*316f0*78945sd21a6$db6f9a3e2a$':
					$aseguradora = 1;
					break;
				case '823//ac1*316f0*85sdrtyh452v$db6f9a3e2a$':
					$aseguradora = 8;
					break;
				case '823//ac1*316f0*96sdf32332dv$db6f9a3e2a$':
					$aseguradora = 9;
					break;
				default:
					$aseguradora = null;
					break;
				
			}
			
			return $aseguradora;
		}
		
		public function insert($table,$array)
		{			
			//$table = $this->request['table'];
			
			if(is_object($array))
			{
				$array =  (array) $array;
			}
			
			$sql = "Insert into ".$table;
			$sql .= " (";
		
			
			foreach($array as $key => $value)
			{
				if($key != 'table' and $key != 'Acc')
				{
					$sql .= $key.",";
					
				}
		
				
			}
			$sql = substr_replace($sql, "", -1);
			$sql .= ") values ( ";
			
			foreach($array as $key => $value)
			{
				if($key != 'table' and $key != 'Acc')
				{
					$sql .= "'".$value."',";
					
				}
		
				
			}
			$sql = substr_replace($sql, "", -1);
			$sql .= ") ";		
			
			return $sql;
		}
		
		
		public function test()
		{
			$sql = "SELECT clase from siniestro order by id desc LIMIT 1 ";
			$query = $this->query($sql);
			$siniestro = mysql_fetch_object($query);
			//print_r($siniestro);
			return $siniestro;
		}
		
		public function validate($numero)
		{
			//echo "validacion";}
			$aseguradora = $this->aseg_apykey($this->request["APIKEY"]);
			$sql = "SELECT * from siniestro where numero = '$numero' and aseguradora = '$aseguradora' LIMIT 1";
			$query = $this->query($sql);
			$siniestro = mysql_fetch_object($query);
			//print_r($siniestro);
			if($siniestro != null)
			{
				return 1;
			}
			else{
				return 0;
			}
			
		}
		
		public function check_request($content)
		{			
			$counter = 0;
			foreach($content as $key => $var)
			{
				if($this->request[$key]==null)
				{
					  $this->lack_params .= " ".$key.", ";
					//echo "falta ".$var.",";
					$counter++;
				}
			}

			return $counter;

		}
		
		
	}
?>