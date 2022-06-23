<?php
header('Access-Control-Allow-Origin: *');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);
//echo "controlador de recepción";
//print_r($_FILES);
include_once(dirname(__FILE__).'/../config/config.php');
include_once(dirname(__FILE__).'/../config/resuelve.php');

if($_POST){	
	//echo "ENTRE A POST";
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	header('Content-type: text/html; charset=utf-8');
	$recepcion = new RecepcionController();
	//La funcion recive el id del siniestro y la imagen a procesar
	if(isset($_POST['otro_ingreso_recepcion']))
	{echo $recepcion->otro_ingreso_recepcion($_REQUEST);}
	if(isset($_POST['idsiniestro']))
	{echo $recepcion->register_image($_POST);}
	if(isset($_POST['privateip']))
	{echo $recepcion->register_image_from_desktop($_POST);}
	if(isset($_POST['check64']))
	{echo $recepcion->check_base64($_POST);}
	if(isset($_POST['img_session']))
	{echo $recepcion->check_images_session($_POST);}
	if(isset($_POST['img_move']))
	{echo $recepcion->recepcion_move_files($_POST);}
	if(isset($_POST['delete_all']))
	{echo $recepcion->delete_files($_POST);}
	if(isset($_POST['test']))
	{echo  $recepcion->test();}
	if(isset($_POST['garantias_service']))
	{echo  $recepcion->garantias_service();}
	if(isset($_POST['sid']))
	{echo  $recepcion->get_images();}
	
		
	
	//print_r($_POST);
}



class RecepcionController
{
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
				html();
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
			else
			{
				# debug_print_backtrace();
				echo "<br><br><b>Error en :<br>" . $cadena . "</b><br>Error: $Error_de_mysql<br>";
				enviar_gmail("sistemas@aoaoclombia.com",'Gestion de Procesos','sergiocastillo@aoacolombia.com,Sergio Castillo','',"Mysql Error",
				"<H3>Error MySQL </H3>Instruccion: $cadena<br>Error: $Error_de_mysql <br>Usuario: ".$_SESSION['User']."-".$_SESSION['Nick']);
				die();
			}
		}
	}
	
	public function otro_ingreso_recepcion($_REQUEST)
	{
		
		$img_process = $this->register_image($_REQUEST);
		echo json_encode($img_process);	
		
		$tharray = array("nombre"=>$_POST["otro_rec_nombre"],
		"apellido"=>$_POST["otro_rec_apellido"],
		"identificacion"=>$_POST["otro_rec_identificacion"],
		"fecha"=>date("Y-m-d H:i:s"),
		"visitado"=>1,
		"registrado_por"=>"Java_app",
		"descripcion"=>"Autoservicio recepcion",
		"foto_f"=>$img_process["upload_file"]);

		$sql = $this->insert("aoacol_administra.ingreso_recepcion",$tharray);	
		echo $sql;
		$this->query($sql);
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
	
	public function register_image($_REQUEST)
	{
		
		
		$sql = "select * from cita_servicio where siniestro = ".$_REQUEST['idsiniestro']." and estado = 'P' LIMIT 1 ";
		$query = $this->query($sql);

		$CITA = mysql_fetch_object($query);
		$id = $_REQUEST['idsiniestro'];
		$imagen = $_FILES['image'];
		$Camino='/var/www/html/public_html/Administrativo/ingreso_recepcion/Autoservicio';
		$Camino = $Camino."/".$CITA->id."/".$id;
		if(!is_dir($Camino))
		{
			mkdir($Camino, 0777, true);
		}
		else
		{
			chmod($Camino, 0777);
		}	
	
		$name = basename($_FILES['image']['name'])."_".$id.".png";
		$uploadfile = $Camino .'/'. $name;
	
		
			if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
			  $upload_status = "File is valid, and was successfully uploaded.\n";
			} else {
			  $upload_status = "Upload failed";
			}

		$array = array("upload_status"=>$upload_status,"upload_file"=>$uploadfile);
		
		return $array;
		
	}
	
	public function register_image_from_desktop($_REQUEST)
	{
		
		$Camino='/var/www/html/public_html/Administrativo/images_java';	
		
		$Camino = $Camino."/".$_POST['privateip'];
		
		if(!is_dir($Camino))
		{
			mkdir($Camino);
		}		
		
		echo $Camino;
		
		$imagen = $_FILES['file'];
		
		$uploadfile = $Camino .'/'. basename($_FILES['file']['name']);		
		
		echo "var in session ";	
		
		echo "<p>";
		
			if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
			  echo "File is valid, and was successfully uploaded.\n";
			} else {
			   echo "Upload failed";
			}

			/*echo "</p>";
			echo '<pre>';
			echo 'Here is some more debugging info:';
			print_r($_FILES);
			print "</pre>";*/
			
		$path = $Camino .'/'. basename($_FILES['file']['name']);
		$text64 = file_get_contents($path);
		//print_r($text64);
		file_put_contents($path, base64_decode($text64));
		
	}
	
	public function check_base64()
	{
		//funcion solo para probar el base64.
		$Camino='/var/www/html/public_html/Administrativo/images_java';
		$path = $Camino .'/Visual_Image.png';
		$text64 = file_get_contents($path);
		//print_r($text64);
		file_put_contents($path, base64_decode($text64));
	}
	
	public function check_images_session($_REQUEST)
	{
		$Camino='/var/www/html/public_html/Administrativo/images_java';		
		$Camino = $Camino."/".$_POST['priip'];
		$uploadfile = $Camino .'/'.$_POST['filename'];
		if(!file_exists ( $uploadfile ))
		{
			return "none";
		}
		else{		
			$imgData = base64_encode(file_get_contents($uploadfile));
			return $imgData;
		} 
	}
	
	public function delete_files($_REQUEST)
	{
		$imagena='/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Cedula_caraA.png";
		$imagenb='/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Cedula_caraB.png";
		$imagenc='/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Licencia_caraA.png";
		$imagend='/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Licencia_caraB.png";
		$imagene='/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Tarjeta_credito.png";
		$imagenf='/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Consignacion.png";
		$imageng='/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Tarh_frontal.png";
		$imagenh='/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Tarh_trasera.png";
		
		if(file_exists($imagena))
		{unlink($imagena);}
	
		if(file_exists($imagenb))
		{unlink($imagenb);}
	
		if(file_exists($imagenc))
		{unlink($imagenc);}
	
		if(file_exists($imagend))
		{unlink($imagend);}
	
		if(file_exists($imagene))
		{unlink($imagene);}
	
		if(file_exists($imagenf))
		{unlink($imagenf);}
	
		if(file_exists($imageng))
		{unlink($imageng);}
	
		if(file_exists($imagenh))
		{unlink($imagenh);}
		
		return "imagenes borradas";
		
	}
	
	public function recepcion_move_files($_REQUEST)
	{
		$Camino='/var/www/html/public_html/Control/operativo/siniestro';
		$Camino2='/var/www/html/public_html/Control/operativo/garantia';
		$Subdirectorio = substr(str_pad($_POST['siniestro'],6,'0',STR_PAD_LEFT),0,3);
		$folderpath = $Camino."/".$Subdirectorio."/".$_POST['siniestro'];
		$folderpath2 = $Camino2."/".$Subdirectorio."/".$_POST['siniestro'];
		$dbpath = 'siniestro'."/".$Subdirectorio."/".$_POST['siniestro'];
		$dbpath2 = 'garantia'."/".$Subdirectorio."/".$_POST['siniestro'];
		if(!is_dir($folderpath))
		{
			mkdir($folderpath);
		}
		
		if(!is_dir($folderpath2))
		{
			mkdir($folderpath2);
		}
		print_r($_POST);
		if($_POST['imagena']==1)
		{
			$tmp_file = '/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Cedula_caraA.png";	
			$finalpath = $folderpath."/"."Cedula_caraA.png";
			$finaldb = $dbpath."/"."Cedula_caraA.png";
			$finalpathtumb = $folderpath."/"."tumb_Cedula_caraA.png";
			
			if(file_exists($finalpath))
			{unlink($finalpath);}
			if(file_exists($finalpathtumb))
			{unlink($finalpathtumb);}
			
			
			echo $tmp_file;
			echo '<br>';
			echo $finalpath;
			rename($tmp_file, $finalpath);
			echo '<br>';
			echo "update siniestro set img_cedula_f = '".$finalpath."' where id = ".$_POST['siniestro'];
			$query = $this->query("update siniestro set img_cedula_f = '".$finaldb."' where id = ".$_POST['siniestro']);
		}
		if($_POST['imagenb']==1)
		{
			$tmp_file = '/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Cedula_caraB.png";
			$finalpath = $folderpath."/"."Cedula_caraB.png";
			$finalpathtumb = $folderpath."/"."tumb_Cedula_caraB.png";
			
			if(file_exists($finalpath))
			{unlink($finalpath);}
			if(file_exists($finalpathtumb))
			{unlink($finalpathtumb);}
			
			//return "stop";
		
			$finaldb = $dbpath."/"."Cedula_caraB.png";
			echo $tmp_file;
			echo '<br>';
			echo $finalpath;
			rename($tmp_file, $finalpath);
			echo "update siniestro set img_pase_f = '".$finalpath."' where id = ".$_POST['siniestro'];
			$query = $this->query("update siniestro set img_pase_f = '".$finaldb."' where id = ".$_POST['siniestro']);
		}
		if($_POST['imagenc']==1)
		{
			$tmp_file = '/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Licencia_caraA.png";
			$finalpath = $folderpath."/"."Licencia_caraA.png";
			$finaldb = $dbpath."/"."Licencia_caraA.png";
			$finalpathtumb = $folderpath."/"."tumb_Licencia_caraA.png";
			
			if(file_exists($finalpath))
			{unlink($finalpath);}
			if(file_exists($finalpathtumb))
			{unlink($finalpathtumb);}
			
			echo $tmp_file;
			echo '<br>';
			echo $finalpath;
			rename($tmp_file, $finalpath);
			echo "update siniestro set Adicional1_f = '".$finalpath."' where id = ".$_POST['siniestro'];
			$query = $this->query("update siniestro set Adicional1_f = '".$finaldb."' where id = ".$_POST['siniestro']);
		}
		if($_POST['imagend']==1)
		{
			$tmp_file = '/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Licencia_caraB.png";
			$finalpath = $folderpath."/"."Licencia_caraB.png";
			$finaldb = $dbpath."/"."Licencia_caraB.png";
			$finalpathtumb = $folderpath."/"."tumb_Licencia_caraB.png";
			
			if(file_exists($finalpath))
			{unlink($finalpath);}
			if(file_exists($finalpathtumb))
			{unlink($finalpathtumb);}
		
			echo $tmp_file;
			echo '<br>';
			echo $finalpath;
			rename($tmp_file, $finalpath);
			echo "update siniestro set Adicional2_f = '".$finalpath."' where id = ".$_POST['siniestro'];
			$query = $this->query("update siniestro set Adicional2_f = '".$finaldb."' where id = ".$_POST['siniestro']);
		}
		
		if($_POST['imagene']==1)
		{
			$tmp_file = '/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Tarjeta_credito.png";
			$text64 = file_get_contents($tmp_file);
			//print_r($text64);
			file_put_contents($tmp_file, base64_encode($text64));
			
			$finalpath = $folderpath2."/"."Tarjeta_credito.png";
			$finaldb = $dbpath2."/"."Tarjeta_credito.png";
			$finalpathtumb = $folderpath."/"."tumb_Tarjeta_credito.png";
			
			if(file_exists($finalpath))
			{unlink($finalpath);}
			if(file_exists($finalpathtumb))
			{unlink($finalpathtumb);}
			
			echo $tmp_file;
			echo '<br>';
			echo $finalpath;
			rename($tmp_file, $finalpath);
			
			//echo "update sin_autor set tarjeta_f = '".$finaldb."' where id = ".$_POST['Auid'];
			$query = $this->query("update sin_autor set tarjeta_f = '".$finaldb."' where id = ".$_POST['Auid']);
		}
		
		if($_POST['imagenf']==1)
		{
			$tmp_file = '/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Consignacion.png";
			$finalpath = $folderpath2."/"."Consignacion.png";
			$finaldb = $dbpath2."/"."Consignacion.png";
			$finalpathtumb = $folderpath."/"."tumb_Consignacion.png";
			
			if(file_exists($finalpath))
			{unlink($finalpath);}
			if(file_exists($finalpathtumb))
			{unlink($finalpathtumb);}
			
			echo $tmp_file;
			echo '<br>';
			echo $finalpath;
			rename($tmp_file, $finalpath);
			echo "update sin_autor set consignacion_f = '".$finaldb."' where id = ".$_POST['Auid'];
			$query = $this->query("update siniestro set Adicional2_f = '".$finaldb."' where id = ".$_POST['siniestro']);
		}
		
		if($_POST['imageng']==1)
		{
			$tmp_file = '/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Tarh_frontal.png";
			$finalpath = $folderpath2."/"."Tarh_frontal.png";
			$finaldb = $dbpath2."/"."Tarh_frontal.png";
			$finalpathtumb = $folderpath."/"."tumb_Tarh_frontal.png";
			
			if(file_exists($finalpath))
			{unlink($finalpath);}
			if(file_exists($finalpathtumb))
			{unlink($finalpathtumb);}
			
			echo $tmp_file;
			echo '<br>';
			echo $finalpath;
			rename($tmp_file, $finalpath);
			echo "update sin_autor set consignacion_f = '".$finaldb."' where id = ".$_POST['Auid'];
			$query = $this->query("update siniestro set eadicional3_f = '".$finaldb."' where id = ".$_POST['siniestro']);
		}
		
		if($_POST['imagenh']==1)
		{
			$tmp_file = '/var/www/html/public_html/Administrativo/images_java'."/".$_POST['priip']."/"."Consignacion.png";
			$finalpath = $folderpath2."/"."Tarh_trasera.png";
			$finaldb = $dbpath2."/"."Tarh_trasera.png";
			$finalpathtumb = $folderpath."/"."tumb_Tarh_trasera.png";
			
			if(file_exists($finalpath))
			{unlink($finalpath);}
			if(file_exists($finalpathtumb))
			{unlink($finalpathtumb);}
			
			echo $tmp_file;
			echo '<br>';
			echo $finalpath;
			rename($tmp_file, $finalpath);
			echo "update sin_autor set consignacion_f = '".$finaldb."' where id = ".$_POST['Auid'];
			$query = $this->query("update sin_autor set eadicional4_f = '".$finaldb."' where id = ".$_POST['Auid']);
		}
	}
	
	public function encoded_image($_REQUEST)
	{		
		
		$Camino='/var/www/html/public_html/Administrativo/images_java';	
		
		$Camino = $Camino."/".$_POST['privateip'];
		
		if(!is_dir($Camino))
		{
			mkdir($Camino);
		}		
		
		echo $Camino;
		
		$imagen = $_FILES['file'];
		
		$uploadfile = $Camino .'/'. basename($_FILES['file']['name']);		
		
		echo "var in session ";
		
	
		
		echo "<p>";
		
		if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
		  echo "File is valid, and was successfully uploaded.\n";
		} else {
		   echo "Upload failed";
		}

			
	}
	
	public function test()
	{
		print_r($_FILES);
		print_r($_POST);
	}
	
	public function garantias_service()
	{
		$this->register_image_from_web($_REQUEST);
		//print_r($_FILES);
	}
	
	public function register_image_from_web($_REQUEST)
	{
		//Este codigo no tiene funcionalidad aun
			$Camino='/var/www/html/public_html/Control/operativo/siniestro';
			$Subdirectorio = substr(str_pad($_POST['siniestro'],6,'0',STR_PAD_LEFT),0,3);
			$folderpath = $Camino."/".$Subdirectorio."/".$_POST['siniestro'];
			
			if(!is_dir($folderpath))
			{
				mkdir($folderpath);
			}		
			
			$imagen = $_FILES['file'];
			
		if($_POST["tipo_ima"]=="img_licencia_conduccion_frente")
		{						
			$name = "licenciaCaraA.png";
			
			$uploadfile = $folderpath .'/'.$name;		
			
			echo $uploadfile;
			
			if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
			  echo "File is valid, and was successfully uploaded.\n";
			} else {
			   echo "Upload failed";
			}
			$dbpath = 'siniestro'."/".$Subdirectorio."/".$_POST['siniestro']."/".$name;
			echo "update siniestro set Adicional1_f = '".$dbpath."' where id = ".$_POST['siniestro'];
			$query = $this->query("update siniestro set Adicional1_f = '".$dbpath."' where id = ".$_POST['siniestro']);
		}
		if($_POST["tipo_ima"]=="img_licencia_conduccion_reverso")
		{		
			$name = "licenciaCaraB.png";
			
			$uploadfile = $folderpath .'/'.$name;		
			
			echo $uploadfile;
			
			if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
			  echo "File is valid, and was successfully uploaded.\n";
			} else {
			   echo "Upload failed";
			}
			$dbpath = 'siniestro'."/".$Subdirectorio."/".$_POST['siniestro']."/".$name;
			echo "update siniestro set Adicional2_f = '".$dbpath."' where id = ".$_POST['siniestro'];
			$query = $this->query("update siniestro set Adicional2_f = '".$dbpath."' where id = ".$_POST['siniestro']);
		}
		if($_POST["tipo_ima"]=="img_cedula_ciudadania_frente")
		{
			$name = "CedulaCaraA.png";
			
			$uploadfile = $folderpath .'/'.$name;		
			
			echo $uploadfile;
			
			if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
			  echo "File is valid, and was successfully uploaded.\n";
			} else {
			   echo "Upload failed";
			}
			$dbpath = 'siniestro'."/".$Subdirectorio."/".$_POST['siniestro']."/".$name;
			echo "update siniestro set img_cedula_f = '".$dbpath."' where id = ".$_POST['siniestro'];
			$query = $this->query("update siniestro set img_cedula_f = '".$dbpath."' where id = ".$_POST['siniestro']);
		}
		if($_POST["tipo_ima"]=="img_cedula_ciudadania_reverso")
		{
			$name = "CedulaCaraB.png";
			
			$uploadfile = $folderpath .'/'.$name;		
			
			echo $uploadfile;
			
			if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
			  echo "File is valid, and was successfully uploaded.\n";
			} else {
			   echo "Upload failed";
			}
			$dbpath = 'siniestro'."/".$Subdirectorio."/".$_POST['siniestro']."/".$name;
			echo "update siniestro set img_pase_f = '".$dbpath."' where id = ".$_POST['siniestro'];
			$query = $this->query("update siniestro set img_pase_f = '".$dbpath."' where id = ".$_POST['siniestro']);
		}
		
	}
	
	public function get_images()
	{
			$query = $this->query("select Adicional1_f as licenciaA , Adicional2_f as licenciaB, img_cedula_f as cedulaA, img_pase_f as cedulaB, eadicional1_f as Tarh_frontal, eadicional2_f as Tarh_trasera  from siniestro  where id = ".$_POST['sid']);
			$object = mysql_fetch_object($query);
			echo json_encode($object);
	}
	
}


	

	
?>