<?php
	
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include_once(dirname(__FILE__).'/../config/config.php');
	include_once(dirname(__FILE__).'/../config/resuelve.php');
	

if($_REQUEST)
{
	
	if(isset($_REQUEST['Acc']))
	{
		$ajax = new WsController($_REQUEST);
		$response = $ajax->$_REQUEST['Acc']();
		echo json_encode($response);
	}
	else
	{ 
		//echo "here";
		$request_body = file_get_contents('php://input');
		if($request_body)
		{
			$request = json_decode($request_body);	
			$ajax = new WsController($request);
			
			switch ($_SERVER['REQUEST_METHOD']) {
				case "POST":
					$response = $ajax->create($request->table);
					break;
				case "PUT":
					
					$response = $ajax->persist($request->table);
					break;
				case "DELETE":
					$response = $ajax->delete($request->table);
					break;
				case "GET":
					
					$response = $ajax->get($request->table);
					break;
			}		
			
			echo json_encode($response);
		}
	}
}

class WsController
{

	function __construct($request=null){		
		$this->request = $request;		
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
				//html();
				echo "<h3>Entrada Duplicada, no se pudo ingresar el nuevo registro</h3><script language='javascript'>alert('ENTRADA DUPLICADA, el registro no se pudo modificar o guardar.');</script>Debe ";
				if($Num_Tabla)
				{
					echo "<a href='javascript:oculta_edicion($Num_Tabla,false);'>cerrar esta ventana</a> e intentarlo nuevamente.";
				}
				else
					echo "<a href='javascript:window.close();void(null);'>cerrar esta ventana</a> e intentarlo nuevamente.";
				die();
			}
			elseif(strpos(' '.$Error_de_mysql,'Lock wait timeout exceeded') && strpos(' '.$cadena,'update') )
			{
				q($cadena);
			}
			
		}
	}
	
	
	public function  fetch_object($query)
	{
		if($query != null)
		{
			$rows = array();
			while($row = mysql_fetch_object($query)){
				
				array_push($rows, $row);
			}
			if(count($rows)==0)
			{
				return null;
			}
			
			elseif(count($rows)==1)
			{
				return $rows[0];
			}
			
			else
			{
				return $rows;
			}
		}
		return null;
	}

	public function get_from_table()
	{
		$SQL  = $_POST['query'];	
		$query = $this->query($SQL);
		$results = $this->fetch_object($query);
		echo json_encode($results);		
	}
	
	public function getAll()
	{		
		$table = $_REQUEST['table'];
		$sql = "Select * from ".$table;		
		$query = $this->query($sql) or die(mysql_error());		
		$rows = $this->fetch_object($query);
		
		return array("status"=>1,"rows"=>$rows);
	}


	public function persist($table)
	{	
		if(isset($this->request->update_validation_subscriber))
		{
			$validation_process = $this->process_validation($this->request->update_validation_subscriber);
			if($validation_process["status"]!=1)
			{
				return $validation_process;
			}				
		}	
		$sql = $this->update($table,$this->request->data);		
		$this->query($sql);	
		return array("status"=>1);	

	}

	public function delete($table)
	{	
		if(isset($this->request->delete_validation_subscriber))
		{
			$validation_process = $this->process_validation($this->request->delete_validation_subscriber);
			if($validation_process["status"]!=1)
			{
				return $validation_process;
			}				
		}
		
		if($this->request->safe_table == true)
		{
			$sql = $this->safe_delete($table,$this->request->data);	
		}
		else
		{
			$sql = $this->delete_query($table,$this->request->data);	
		}		
		
		$this->query($sql);
		return array("status"=>1);
	}

	public function create($table)
	{
		if(isset($this->request->create_validation_subscriber))
		{
			$validation_process = $this->process_validation($this->request->create_validation_subscriber);
			if($validation_process["status"]!=1)
			{
				return $validation_process;
			}				
		}
		
		$sql = $this->insert($table,$this->request->data);
		
		$this->query($sql);
		return array("status"=>1,"message"=>"Nuevo elemento insertado");
	}

	public function delete_query($table,$array)
	{	
		$sql = "DELETE from ".$table." where id  = ".$array->id;
		return $sql;
	}

	public function safe_delete($table,$array)
	{	
		$sql = "Update ".$table." set estado = 0 where id  = ".$array->id;
		return $sql;
	}

	public function insert($table,$array)
	{
		
		
		//$table = $this->request['table'];
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
	
	
	public function update($table,$array)
	{

		$sql = "update ".$table;
		$sql .= " SET ";
	
		
		foreach($array as $key => $value)
		{
			
			if($key == 'id')
			{
				$id = $value;
			}
			else
			{
				if($key != 'table' and $key != 'Acc')
				{
					$sql .= $key."  = '".$value."',";
					
				}
			}
			
		}	

		$sql = substr_replace($sql, "", -1);	
		
		$sql .= " where id = ".$id;			
		
		return $sql;
 	}
	
	public function process_validation($validation)
	{			
		switch($validation->type)
		{
			case "FOREIGN_VALUES":
				$sql = "SELECT * from  $validation->with where $validation->foreign_key = ".$this->request->data->id." ";				
				$result = $this->query($sql);
				if($result)
				{
					$n = mysql_num_rows($result);	
				}
				else{
					$n = 0;
				}				
				
				if($n > 0)
				{
					return array("status"=>2,"message"=>"Existen valores relacionados a este tabla que impiden su eliminación");
				}				
				break;
			default:
				break;
		}
		
		return array("status"=>1);
	}
	



}









?>