<?php

include_once(dirname(__FILE__).'/../config/config.php');
include_once(dirname(__FILE__).'/../config/resuelve.php');



class DbConnect
{
	
	function DbConnect(){
		$this->id_column = "id";
	}

	public function query($cadena, $Devolver_sql = 0,&$_Cantidad_registros_afectados=0) // corre un query invocado internamente
	{
		global $Nombre, $Id_alterno, $Num_Tabla,$LINK;
		
			

		if(!$LINK = mysql_connect(MYSQL_S, resuelve_usuario_mysql($cadena), MYSQL_P)) die('Problemas con la conexion de la base de datos!');
		mysql_query('SET collation_connection = utf8_general_ci',$LINK);
		if(!mysql_select_db(MYSQL_D, $LINK)) die('Problemas con la seleccion de la base de datos');
		if(strpos(' '.$cadena,'update ') || strpos(' '.$cadena,'alter table') || strpos(' '.$cadena,'insert '))
		{	
			mysql_query("set innodb_lock_wait_timeout=80",$LINK);
		}
		else
		{
			mysql_query("set innodb_lock_wait_timeout=20",$LINK);
		}
		
		/*
		Verificar errores mysql
		
			$RQ = mysql_query($cadena, $LINK);
		
			echo mysql_error();
		
		exit;*/
		
		//print_r($RQ = mysql_query($cadena, $LINK));
		
		//echo mysql_error();
		
		//exit;
		
		
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
				return true;
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
					//echo "here";
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
				return true;
			}
			else
			{
				
				echo mysql_error();
				
				return false;
				# debug_print_backtrace();
				/*echo "<br><br><b>Error en :<br>" . $cadena . "</b><br>Error: $Error_de_mysql<br>";
				enviar_gmail("sistemas@aoaoclombia.com",'Gestion de Procesos','sergiocastillo@aoacolombia.com,Sergio Castillo','',"Mysql Error",
				"<H3>Error MySQL </H3>Instruccion: $cadena<br>Error: $Error_de_mysql <br>Usuario: ".$_SESSION['User']."-".$_SESSION['Nick']);
				die();*/
			}
		}
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
		$sql .= "); ";		
		
		return $sql;
	}
	
	public function update($table,$array)
	{
		$sql = "update ".$table;
		$sql .= " SET ";

		$columns = "";
		$values = "";	
		
		foreach($array as $key => $value)
		{
			
			if($key == $this->id_column)
			{
				$id = $value;
			}
			else
			{
				if($key != 'table' and $key != 'Acc' and $key != "password")
				{
					$sql .= $key."  = '".$value."',";
					$columns  .= $key.",";
					$values .= $value.",";
				}
			}
			
		}	

		$sql = substr_replace($sql, "", -1);	
		
		$sql .= " where ".$this->id_column." = ".$id;

		$values = substr($values, 0, -1);
		$columns = substr($columns, 0, -1);
		
		return $sql;
 	}
	
	public function convert_objects($query)
	{
		if($query != null)
		{
			$rows = array();
			while($row = mysql_fetch_object($query)){
				
				array_push($rows, $row);
			}
			return $rows;
		}
		return null;
	}
	
	public function convert_object($query)
	{
		if($query != null)
		{
			$row = mysql_fetch_object($query);
			return $row;
		}
		return null;
	}
}	