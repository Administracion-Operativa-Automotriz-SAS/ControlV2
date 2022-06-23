<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include_once(dirname(__FILE__).'/../config/config.php');
	include_once(dirname(__FILE__).'/../config/resuelve.php');

	$Acc = $_GET['Acc'];
	if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}
	
	function query($cadena, $Devolver_sql = 0,&$_Cantidad_registros_afectados=0) // corre un query invocado internamente
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
	
	
	
	function data_vehiculo()
	{
		$placa = $_POST['placa'];
		$query = "Select veh.*,linea.nombre as nom_linea, marca.nombre as nom_marca, clase.nombre as nom_clase, carroc.nombre as nom_carroc
		from  aoacol_aoacars.vehiculo as veh inner join aoacol_aoacars.linea_vehiculo as linea on veh.linea = linea.id
		inner join aoacol_aoacars.marca_vehiculo as marca on marca.id = linea.marca inner join aoacol_aoacars.clase_vehiculo as clase on  clase.id = veh.clase	
		inner join aoacol_aoacars.carroceria as carroc on carroc.id = veh.carroceria where placa = '$placa'  limit 1";			
		$resultado = query($query);	
		if($resultado != null)
		{
			$vehiculo = mysql_fetch_object($resultado);
			echo json_encode($vehiculo);
		}
		else
		{
			echo "";
		}
	}
	
	
	function info_usuario()
	{
		$tipo_documento = $_POST['tipo_documento'];
		$documento = $_POST['documento'];		
		$query = "Select cl.*, ci.nombre as ciudad_nombre FROM aoacol_aoacars.cliente as cl inner join ciudad as ci on cl.ciudad = ci.codigo where tipo_id = '$tipo_documento' and identificacion = '$documento' limit 1";
		$resultado = query($query);
		$usuario = mysql_fetch_object($resultado);
		echo json_encode($usuario);
	}

	function comunicacion_externa()
	{		
		echo json_encode($_POST);
	}
	
	function data_proveedor()
	{		
		$query = "SELECT * from aoacol_administra.proveedor where nombre = '".$_POST['prov']."' AND activo != 0 ";
		//echo $query;
		$resultado = query($query);
		$proveedor = mysql_fetch_object($resultado);
		echo json_encode($proveedor);
	}
	
	function load_print_document()
	{	
		//header('X-XSS-Protection:0');
		$id = $_POST['id'];
		
		$query = "Select * from aoacol_administra.documentos_impresos where id = '$id' LIMIT 1 ";
		
		$resultado = query($query);	
		
		$load_document = mysql_fetch_object($resultado);
		
		$html = $load_document->html;
		
		if(mb_detect_encoding($html) == 'UTF-8'){
			header('Content-Type: text/html; charset=utf-8');
		}	
		
		$extra = "<script> var print_ver = 'activate'; </script>";
		$html = str_replace('\"', "", $html);
		$html = str_replace('true', "false", $html);
		$html = str_replace('make_visible', "nothing", $html);
		
		echo $extra;
		echo $html;

	}
	
	function load_save_document()
	{
		$id = $_POST['id'];
		
		setcookie("iddocumento_edit", $id);
		
		$query = "Select * from aoacol_administra.documentos_guardados where id = '$id' LIMIT 1 ";
		
		$resultado = query($query);	
		
		$load_document = mysql_fetch_object($resultado);
		
		$html = $load_document->html;
		
		//print_r($load_document);
		//echo mb_detect_encoding($html);
		if(mb_detect_encoding($html) == 'UTF-8'){
			//echo "utf8";
			header('Content-Type: text/html; charset=utf-8');
		}			
		
		$pos = strrpos($html, "<!--current_page_script-->");
	
		$pos2 = strrpos($html, "<!--/current_page_script-->");
		
		$extra = "<script> var edit = 'activate'; </script>";
	
		//$html = substr_replace ($html , $extra ,($pos+27) );
		
		$html = str_replace('\"', "",$html);
		echo $extra;
		echo $html;
	}
	
	function save_document()
	{
		header('X-XSS-Protection:0');
	    if(isset($_COOKIE["iddocumento"]) and isset($_COOKIE["usuario"]) and $_COOKIE["usuario"] !=null )
		{
			
			$usuario = $_COOKIE["usuario"];
			$html = $_POST['html'];
			$documento = $_COOKIE["iddocumento"];
			$consecutivo = $_POST['consecutivo'];
			
			
			
			$query = "Select max(id) as id from aoacol_administra.documentos_guardados where usuario = '$usuario' and documento = '$documento'
				and consecutivo = '$consecutivo' limit 1";	
					
			//echo $query;
				
			$resultado = query($query);
			
			//print_r($resultado); 
			
			$sdocumento = mysql_fetch_object($resultado);
			
			if($sdocumento->id == null)
			{
					$query = "Select max(consecutivo) as max from aoacol_administra.documentos_guardados where documento = '$documento' LIMIT 1 ";
			
					$resultado = query($query);
					
					$validacion = mysql_fetch_object($resultado);
					
					if($validacion->max == null)
					{
						$valcons = null; 
					}
					else{
						$valcons = $validacion->max+1; 
					}
					
					if($valcons !=null and $valcons != $consecutivo)
					{
						echo json_encode(array("estado"=>3));
						exit;
					}
					
				$query = "Insert into aoacol_administra.documentos_guardados  (usuario,html,documento,consecutivo) values ('$usuario','$html','$documento','$consecutivo');
				";
				//echo $query;
				$resultado = query($query);
			}
			else 
			{
				$uid = $sdocumento->id; 
				$query=" Update aoacol_administra.documentos_guardados set html = '$html' where id = '$uid' ";
				$resultado = query($query);
			}
			
			echo json_encode(array("estado"=>1));
		}
		else{
			echo json_encode(array("estado"=>2));
		}
	}
	
	function update_document()
	{
		if(isset($_COOKIE["iddocumento_edit"]) and $_COOKIE["iddocumento_edit"]!= null)
		{
			$uid = $_COOKIE["iddocumento_edit"];
			$html = $_POST['html'];
			$query = "Update aoacol_administra.documentos_guardados set html = '$html' where id = '$uid'";
			//echo $query;
			//exit;
			$resultado = query($query);
			echo json_encode(array("estado"=>1));
		}
		else{
			echo json_encode(array("estado"=>2));
		}
		
	}
	
	function save_print_document()
	{
		$html = $_POST['html'];
		$usuario = $_COOKIE["usuario"];
		
		if($_POST['modo'] == 1)
		{
			$guardadoid = $_COOKIE["iddocumento_edit"];
			$query = "Insert into aoacol_administra.documentos_impresos (usuario,guardado,html) values ('$usuario','$guardadoid','$html') ";
			$resultado = query($query);
			echo json_encode(array("estado"=>1));			
			
		}		
		if($_POST['modo'] == 2)
		{
			$documento = $_COOKIE["iddocumento"];
			$consecutivo = $_POST['consecutivo'];		
			
			$query = "SELECT id from aoacol_administra.documentos_guardados where documento = '$documento' and consecutivo = '$consecutivo' LIMIT 1";
			$resultado = query($query);				
			if($resultado != null)
			{
				$guardado = mysql_fetch_object($resultado);
				$guardadoid = $guardado->id;
				$query = "Insert into aoacol_administra.documentos_impresos (usuario,guardado,html) values ('$usuario','$guardadoid','$html') ";
				$resultado = query($query);
				echo json_encode(array("estado"=>1));	
			}
			else
			{
				$query = "Select max(id) as max from aoacol_administra.documentos_guardados";
				$resultado = query($query);			
				$guardado = mysql_fetch_object($resultado);
				$guardadoid = ($guardado->max)+1;
				$query = "Insert into aoacol_administra.documentos_guardados  (id,usuario,html,documento,consecutivo) values ('$guardadoid','$usuario','$html','$documento','$consecutivo');";
				$resultado = query($query);	
				$query = "Insert into aoacol_administra.documentos_impresos (usuario,guardado,html) values ('$usuario','$guardadoid','$html') ";
				$resultado = query($query);
				echo json_encode(array("estado"=>2));	
			}
		}
	}
	
	function look_prints_document()
	{
		$foid = $_POST['foid'];
		$query = "select * from aoacol_administra.documentos_impresos where guardado = '$foid' ";
		$resultado = query($query);
		$impresiones = array();
		if($resultado != null)
			while ($impresion = mysql_fetch_object($resultado)) {
				array_push($impresiones, $impresion);
		}
		include '../views/subviews/documentos_impresos.html';
	}
	
	function look_all_people_docs()
	{
		$query = "SELECT distinct(usuario)  from aoacol_administra.documentos_guardados";
		$resultado = query($query);
		$usuarios = array();
		if($resultado != null)
			while ($usuario = mysql_fetch_object($resultado)) {
				array_push($usuarios, $usuario);
		}
		include '../views/subviews/documentos_usuarios.html';
	}
	
	function changue_alias()
	{
		$alias=$_POST['name_alias'];
		$id=$_POST['id']; 
		$query = "UPDATE aoacol_administra.documentos_guardados SET alias = '$alias' where id = '$id' ";
		echo $query;
		$resultado = query($query);
	}

?>