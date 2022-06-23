<?php  
	header('Access-Control-Allow-Origin: *');
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ERROR | E_PARSE);

	include_once(dirname(__FILE__).'/../config/config.php');
	include_once(dirname(__FILE__).'/../config/resuelve.php');
	include 'log.php';
	define('APIKEY','823//ac1*316f0*ed9ac872746$db6f9a3e2a$');
	header('Content-Type: application/json');

	$iparray = array('192.192.181.97');
	
	$ipcount = 0;
	
	foreach($iparray as $ip)
	{
		if($_SERVER['REMOTE_ADDR'] == $ip)
		{
			$ipcount++;
		}
	}
	
	if($ipcount<1)
	{
		echo json_encode("Acceso no permitido");
		wh_log($_SERVER['REMOTE_ADDR'].", Acceso no permitido  \n");
		exit;
	}

	if(isset($_REQUEST['APIKEY']))
	{
		if($_REQUEST['APIKEY']!='823//ac1*316f0*ed9ac872746$db6f9a3e2a$')
		{
			wh_log($_SERVER['REMOTE_ADDR'].", Acceso restringido por pagina  \n");
			echo json_encode("Acceso restringido por pagina");
			exit;
		}
	}
	else{
		wh_log($_SERVER['REMOTE_ADDR'].", Acceso restringido por pagina  \n");
		echo json_encode("Acceso restringido por pagina");
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
			"ASEGURADO_NOMBRE" => "Nombre del asegurado","ASEGURADO_ID" => "Identificación del asegurado","DECLARANTE_CELULAR" => "Celular del asegurado",
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
				
				$ciudad = $this->request["CIUDAD"];
				$fec_autorizacion = date('Y');
				$fec_siniestro = date('Y');
				$fec_declaracion = date('Y');
				$placa = $this->request["PLACA"];
				$estado = 5;
				$declarante_celular = $this->request["DECLARANTE_CELULAR"];
				$asegurado_nombre = $this->request["ASEGURADO_NOMBRE"];
				$identificacion = $this->request["IDENTIFICACION"];
				$ciudad_original = $this->request["CIUDAD_ORIGINAL"];
				$ciudad_siniestro = $this->request["CIUDAD_SINIESTRO"];
				$dias_servicio = $this->request["DIAS_SERVICIO"];
				$aseguradora = 93;
				$sql="INSERT INTO siniestro (numero,ciudad,fec_autorizacion,fec_siniestro,fec_declaracion,placa,estado,
				asegurado_nombre, asegurado_id, declarante_nombre, declarante_id, declarante_celular, aseguradora,
				ciudad_original,ingreso,ciudad_siniestro, dias_servicio)  VALUES ('$numero','$ciudad','$fec_autorizacion',
				'$fec_siniestro','$fec_declaracion','$placa','$estado','$asegurado_nombre','$identificacion','$asegurado_nombre',
				'$identificacion','$declarante_celular','$aseguradora','$ciudad_original',CURRENT_TIMESTAMP,'$ciudad_siniestro',
				'$dias_servicio') ";
				
				$this->query($sql);				
				return array("estado"=>1,"desc"=>"Siniestro Creado");
				
			}
			
			
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
			//echo "validacion";
			$sql = "SELECT * from siniestro where numero = '$numero' and aseguradora = 93 LIMIT 1";
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