 <?php
/**
 *  Juan David duque Aguirre 201|
 
 **/
 


include('inc/funciones_.php');



if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');	die();}


function valida_sision() // recibe la informacion del formulario de busqueda
{  
  $_SESSION = $_POST['token']; 
  
   	if ($_SESSION['User'] && $_SESSION['Id_alterno'] && $_SESSION['Nick'] && $_SESSION['Nombre'])

   {
	sesion();
   }	
}

function datos_session_proveedor() // recibe la informacion del formulario de busqueda
{  
  $tabla_usuario = $_POST['tabla_usuario']; 
 $usuario = $_POST['usuario']; 
  
  $Sins=q("Select * from aoacol_administra.$tabla_usuario where  id =  '$usuario' ");
  

  while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
		}
  	
}








function datos_session() // recibe la informacion del formulario de busqueda
{  
  $tabla_usuario = $_POST['tabla_usuario']; 
 $usuario = $_POST['usuario']; 
  
  $Sins=q("Select * from aoacol_aoacars.$tabla_usuario where  usuario =  '$usuario' ");
  

  while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
		}
  	
}

function datos_session_id() // recibe la informacion del formulario de busqueda
{  
  $tabla_usuario = $_POST['tabla_usuario']; 
 $usuario = $_POST['usuario']; 
  
  $Sins=q("Select * from aoacol_aoacars.$tabla_usuario where  usuario  = '$usuario' ");
  

 	while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("id"=>$row['id']);
	}
		echo json_encode($tabledata);
}


function subir_archivo_novedad() // recibe la informacion del formulario de busqueda
{  
  
		  $cadenatexto = $_POST["cadenatexto"];

		 
		echo "Escribi? en el campo de texto: " . $cadenatexto . "<br><br>";

		$dir_subida = '/var/www/html/public_html/conVue/archivo/';
        $fichero_subido = $dir_subida . basename($_FILES['userfile']['name']);
		
		$nombre_archivo = $_FILES['userfile']['name'];
		$tipo_archivo = $_FILES['userfile']['type'];
		$tamano_archivo = $_FILES['userfile']['size'];
			
			
		//compruebo si las caracter?sticas del archivo son las que deseo
		if (!((strpos($tipo_archivo, "gif") || strpos($tipo_archivo, "jpeg")) && ($tamano_archivo < 100000))) {
			echo "La extensi?n o el tama?o de los archivos no es correcta. <br><br><table><tr><td>
			<li>Se permiten archivos .gif o .jpg<br><li>se permiten archivos de 100 Kb m?ximo.</td></tr></table>";
			
		}else{
			
			$target_path = "/var/www/html/public_html/conVue/archivo/";
			
			echo $fileName; 
			

				$target_path = $target_path . basename( $_FILES['userfile']['name']); 
				if(move_uploaded_file($_FILES['userfile']['tmp_name'], $nombre_archivo)) {
					echo "The file ". $_FILES['userfile']['tmp_name']. basename( $_FILES['userfile']['name']). 
					" has been uploaded";
				} else{
					echo "hp!";
				}
		}
		  
  
  
  
}


function subir()
{
	
	   global $archivo,$id,$tipo_novedad;
	   $hoy2 = date("YmdHis");
	   $data='';
	   $nombre_archivo = "_NP_".$hoy2.rand(5,33).$tipo_novedad.$id;
	  
	
		$ruta = directorio_imagen('archivo',87);
		
		
		$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$archivo));
		
		
		
		$filepath = $ruta.$nombre_archivo;
		
		
		
		file_put_contents($filepath,$data);
		$ruta_media= "http://app.aoacolombia.com/Control/operativo/archivo/000/87/";
		
		 $ruta_final = $ruta_media.$nombre_archivo;
		
	   
		
		
		 guardar_archivo($nombre_archivo,$tipo_novedad,$ruta_final,$id);
}




function subir_foto()
{
	
	   global $archivo,$encargado,$tabla_usuario;
	   $hoy2 = date("YmdHis");
	   $data='';
	   $nombre_archivo = "_NP_".$hoy2.rand(5,33).$encargado.$tabla_usuario;
	  
	
		$ruta = directorio_imagen('archivo',87);
		
		
		$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$archivo));
		
		
		
		$filepath = $ruta.$nombre_archivo;
		
		
		
		file_put_contents($filepath,$data);
		$ruta_media= "http://app.aoacolombia.com/Control/operativo/archivo/000/87/";
		
		 $ruta_final = $ruta_media.$nombre_archivo;
		
	   
		
		
		 guardar_archivo_foto($nombre_archivo,$encargado,$ruta_final,$tabla_usuario);
}

function guardar_archivo_foto($nombre_archivo,$encargado,$ruta_final,$tabla_usuario) 
{ 
  

  
      $qry = q("UPDATE `aoacol_aoacars`.`$tabla_usuario` 
    set foto = ' $ruta_final' where id = '$encargado' ");
    echo "UPDATE `aoacol_aoacars`.`$tabla_usuario` 
    set foto = ' $ruta_final' where id = '$encargado'";
}



function guardar_archivo($nombre_archivo,$tipo_novedad,$ruta,$id_novedad) 
{ 
  

      $qry = q("INSERT INTO `aoa_modulo`.`archivo`
	  (nombre_archivo,tipo_novedad,id_novedad,ruta,estado) 
	  VALUES ('$nombre_archivo','$tipo_novedad', '$id_novedad','$ruta' ,0)");
      buscar_archivo();
}


function buscar_ubicacion() 
{ 
  
$ubicacion = $_POST['ubicacion']; 
     $ubn = qo("select vehiculo from ubicacion where id = $ubicacion");

	 
	 buscar_vehiculoData($ubn->vehiculo);
}


function buscar_vehiculoData($vehiculo) 
{ 
  
  

   $vehiculoData = qo("select v.tipo_caja,v.placa,s.n_poliza,s.id as id_aseguradora,s.aseguradora_nombre,s.linea_asistencia,l.nombre nlinea
							FROM vehiculo v
							inner join seguros s on v.n_poliza = s.id
							inner join linea_vehiculo l on v.linea = l.id  
							where v.id=$vehiculo ");
							echo json_encode($vehiculoData);

						

}






function buscar_archivo() 
{ 

		  $id=q("SELECT MAX(id_archivo) as id FROM `aoa_modulo`.`archivo` ;   ");
		
		while($row=mysql_fetch_object($id)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			$table_data =  array($row);
		};
		echo json_encode($table_data);
}



 function crearRemplazoSiniestro1(){
	           
		   
		   
		   
		   	include_once($_SERVER["DOCUMENT_ROOT"]."https://app.aoacolombia.com/Control/operativo/controllers/DbConnect.php");
	   
            $this->connect = new DbConnect();   

			try{
				
			$idRecepcion  = $_POST['idSi']; 
						 
			$query = q1("SELECT * FROM siniestro WHERE id = 2142185");
			
			echo $query;
		
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

	
 function insert($table,$array)
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


	
	function crearRemplazoSiniestro(){
		
		try{
				
			$idRecepcion = $_POST['idSi']; 
			
			
			$query = q("SELECT * FROM siniestro WHERE id = $idRecepcion");
			
			
			$queryCon = qo("SELECT * FROM siniestro WHERE id = $idRecepcion");
			
			
			while($row = mysql_fetch_array($query)){
	         	$arrayTable[]= $row;
	        }
		    
			
			
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
			
			
			$sql = insert("aoacol_aoacars.siniestro",$arrayTable[0]);
			
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
		
		
		
		
function buscar_siniestro() // recibe la informacion del formulario de busqueda
{  

$siniestro = $_POST['siniestro']; 

			$Sins=q("SELECT siniestro.id AS id , ciudad.id  AS id_ciudad ,  ciudad.nombre AS nombre ,  `numero`, `numero`,  ciudad.codigo as codigoci ,ciudad.codigo as ciudad,  `fec_autorizacion`,
  `pqr_asociado`,  `fec_siniestro`,  `fec_declaracion`,  `poliza`,
    `intermediario`,  `vigencia_desde`,  `vigencia_hasta`,  `placa`,  
	 `marca`,  `tipo`,  `linea`,  `modelo`,  `clase`,  `asegurado`,
	   `estado`, LEFT(`observaciones`, 256), 
		 `asegurado_nombre`,  `sucursal_radicadora`,  `asegurado_id`,  `asegurado_direccion`,  
		 `color`,  `servicio`,  `declarante_nombre`,  `declarante_id`,  `declarante_telefono`, 
		  `declarante_direccion`,  `declarante_ciudad`,  `declarante_tel_resid`,  `declarante_tel_ofic`, 
		   `declarante_celular`,  `declarate_tel_otro`,  `declarante_email`,  `conductor_nombre`,  `conductor_tel_resid`, 
			 `conductor_tel_ofic`,  `conductor_celular`,  `conductor_tel_otro`,  `conductor_email`,  `ubicacion`, 
			  `img_odo_salida_f`,  `img_odo_entrada_f`,  `img_inv_salida_f`,  `img_inv_entrada_f`,  `img_carta_autorizacion_f`,
			    `img_fotocopia_poliza_f`,  `img_camara_comercio_f`,  `img_orden_ingreso_taller_f`,  `img_cedula_f`, 
				  `img_pase_f`,  `img_contrato_f`, LEFT(`obsconclusion`, 256),  `img_encuesta_f`,  `fecha_inicial`,
				    `fecha_final`,  `encuesta_1`,  `encuesta_2`,  `encuesta_3`,  `encuesta_4`,  `encuesta_5`,  `causal`,
					   `aseguradora`,  `expediente`,  `fasecolda`,  `ciudad_original`,  `ingreso`,  `fotovh1_f`,  `fotovh2_f`,
						  `fotovh3_f`,  `fotovh4_f`,  `fotovh5_f`,  `fotovh6_f`,  `fotovh7_f`,  `fotovh8_f`,  `fotovh9_f`, 
						   `valida_recaudo`,  `info_erronea`,  `siniestro_propio`,  `email_analista`,  `contacto_exitoso`, 
							 `fotovh10_f`,  `control_calidad`, LEFT(`actualizacion_aseg`, 256),  `adicional1_f`,  `adicional2_f`, 
							  `eadicional1_f`,  `eadicional2_f`,  `encuesta_11`,  `encuesta_12`,  `encuesta_13`,  `encuesta_14`,
							    `encuesta_15`,  `encuesta_16`,  `adicional3_f`,  `adicional4_f`,  `subcausal`,  `chevyseguro`, 
								  `ciudad_siniestro`,  `retencion`,  `no_garantia`,  `dadicional3_f`,  `dadicional4_f`,  `dias_servicio`,
								    `bco_occidente`,  `congelamiento_f`,  `gastosf_f`,  `tipogarantia`,  `renta`,  `valoraseg`
								    

			from aoacol_aoacars.siniestro  
					INNER JOIN aoacol_aoacars.ciudad ON aoacol_aoacars.siniestro.ciudad_siniestro = aoacol_aoacars.ciudad.codigo

					

					where siniestro.id = '$siniestro '  or numero = '$siniestro ' "  );
	
		
		
		if($Sins == null){
			
			echo 0;
		}else{
		if(mysql_num_rows($Sins)>1){
			
			
			$Sins1=q("select siniestro.ubicacion,siniestro.id, siniestro.ciudad, siniestro.clase, siniestro.placa , siniestro.asegurado_nombre from aoacol_aoacars.siniestro  
					INNER JOIN aoacol_aoacars.ciudad ON aoacol_aoacars.siniestro.ciudad_siniestro = aoacol_aoacars.ciudad.codigo
					where siniestro.id = '$siniestro '  or numero = '$siniestro ' "  );
			
			     while($row = mysql_fetch_array($Sins1)){
					$tabledata[]= array("id"=>$row['id'],
					"asegurado_nombre"=>$row['asegurado_nombre'],
					"ciudad"=>$row['ciudad'],
					"ubicacion"=>$row['ubicacion'],
					"ciudad_original"=>$row['ciudad_original'],
					"clase"=>$row['clase'],
					"placa"=>$row['placa']);
					}
					
		echo json_encode($tabledata);
		
		
		}else{
			
		while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
		}
		
		
		};
	
		}	
}

function buscar_siniestro_eliminar() // recibe la informacion del formulario de busqueda
{  

$file = $_POST['file']; 
$id = $_POST['id'];

		if (file_exists(getcwd() . $file)) {
			  // Delete file.
			  unlink(getcwd() . $file);
			  echo "Eliminado";
	    }

	eliminar_archivo($id);
			
}


function eliminar_archivo($id) // recibe la informacion del formulario de busqueda
{ 

      $qry = qo("DELETE FROM `aoa_modulo`.`archivo`  WHERE id_archivo = ' $id' ");
	  
}





function guardar_img() // recibe la informacion del formulario de busqueda
{  
    $nombre = $_POST["nombre"];
    $encodedData=explode(',', $_POST["img"]);
    $data = base64_decode($encodedData[1]);
	
    $urlUploadImages = $_SERVER['DOCUMENT_ROOT'].$projName.'/img/';
    $nameImage = $nombre.".png";
    $img = imagecreatefromstring($data);
     if($img) {

        imagepng($img, $urlUploadImages.$nameImage, 0);
        imagedestroy($img); 
        echo 'https://app.aoacolombia.com/img/'.$nameImage;
    }
    else {
        
    }
		
}


function buscar_siniestro_ciudad_original() // recibe la informacion del formulario de busqueda
{  

$ciudad_original = $_POST['ciudad_original']; 

		$Ofic=qo("select * from oficina where ciudad=$ciudad_original");
			
			 $data= json_encode($Ofic);
			echo$data;
		
}

function buscar_archivo_id() // recibe la informacion del formulario de busqueda
{  

$id = $_POST['id']; 

		$Sins=q("select 
		            aoa_modulo.archivo.id_archivo,
					   aoa_modulo.archivo.tipo_novedad,
					   aoa_modulo.archivo.ruta,
					    aoa_modulo.archivo.estado,
						 aoa_modulo.archivo.nombre_archivo, 
					   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
					   aoa_modulo.tipoNovedad.color as color_tipoN,
					   aoa_modulo.tipoNovedad.idtipoNovedad as id_novedad_tipo
		from aoa_modulo.archivo 
    LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on aoa_modulo.archivo.tipo_novedad = tipoNovedad.idtipoNovedad 
		
where aoa_modulo.archivo.id_novedad = $id");
		
        while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("id_archivo"=>$row['id_archivo'],"estado"=>$row['estado'],"nombre_archivo"=>$row['nombre_archivo'],"ruta"=>$row['ruta'],"id_novedad"=>$row['id_novedad'],"id_novedad"=>$row['idtipoNovedad'],"nombre_tipoN"=>$row['nombre_tipoN'],"color_tipoN"=>$row['color_tipoN']
		);
	         
			 }

		
			 if($tabledata == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($tabledata);
		
		}
		
		
		
}


function envio_requisicion_convertida() // recibe la informacion del formulario de busqueda
{  


		$para = $_POST['para']; 
		$copia = $_POST['copia']; 
		$asunto = $_POST['asunto']; 
		$idnovedad = $_POST['idnovedad']; 
		$clave = $_POST['clave']; 
		$usuario_login = $_POST['usuario_login']; 
		$contenido = 104; 
		$proveedor_nombre = $_POST['proveedor_nombre']; 

		
		
	   $data_mail = array(
					"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
					"enviarEmail" => "true",
					"para" => '',
					"copia" =>  '',
					//"para" => $para,
					//"copia" => $copia,
					"idnovedad" => $idnovedad,
					"asunto" =>  $asunto,
					"clave" =>$clave,
					"usuario_login" => $usuario_login,
					"contenido" => $contenido,
					"proveedor_nombre" => $proveedor_nombre
					);
					
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/ServiEmail.php');
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
					curl_exec($ch);
					curl_close($ch);
					
			
		
}





function envio_proveedor() // recibe la informacion del formulario de busqueda
{  



		$para = $_POST['para']; 
		$copia = $_POST['copia']; 
		$asunto = $_POST['asunto']; 
		$idnovedad = $_POST['idnovedad']; 
		$clave = $_POST['clave']; 
		$usuario_login = $_POST['usuario_login']; 
		$contenido = 104; 
		$proveedor_nombre = $_POST['proveedor_nombre']; 

		
		
	   $data_mail = array(
					"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
					"enviarEmail" => "true",
				    "para" => '',
					"copia" =>  '',
					//"para" => $para,
					//"copia" => $copia,
					"idnovedad" => $idnovedad,
					"asunto" =>  $asunto,
					"clave" =>$clave,
					"usuario_login" => $usuario_login,
					"contenido" => $contenido,
					"proveedor_nombre" => $proveedor_nombre
					);
					
					
				    $ch = curl_init();
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/ServiEmail.php');
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
					curl_exec($ch);
					curl_close($ch);
		
}

function NovedadCall_envio() // recibe la informacion del formulario de busqueda
{  

		$para = $_POST['para']; 
		$copia = $_POST['copia']; 
		$asunto = $_POST['asunto'];
		$departamento = $_POST['departamento']; 
		$idnovedad = $_POST['idnovedad']; 
		$reportado = $_POST['reportado']; 
		$ciudorte = $_POST['ciudorte']; 
		$encanombre = $_POST['encanombre']; 
		$contenido = $_POST['contenido']; 
		$usuario_cierre = $_POST['usuario_cierre']; 
        
	   $data_mail = array(
					"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
					"enviarEmail" => "true",
					"id" => $id,
					"para" => '',
					"copia" =>  '',
					//"para" => $para,
					//"copia" => $copia,
					"idnovedad" => $idnovedad,
					"ciudorte" =>  $ciudorte,
					"usuario_cierre" => $usuario_cierre,
					"asunto" =>  $asunto,
					"departamento" =>  $departamento,
					"reportado" =>$reportado,
					"encanombre" => $encanombre,
					"contenido" => $contenido
					);

					
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/ServiEmail.php');
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
					curl_exec($ch);
					curl_close($ch);
		
}


function solicitar_requicion() // recibe la informacion del formulario de busqueda
{  

		$para = $_POST['para']; 
		$copia = $_POST['copia']; 
		$asunto = $_POST['asunto']; 
		$idnovedad = $_POST['idnovedad']; 
		$reportado = $_POST['reportado']; 
		$ciudorte = $_POST['ciudorte']; 
		$encanombre = $_POST['encanombre']; 
		$contenido = $_POST['contenido']; 
		$url = $_POST['url']; 

	   $data_mail = array(
					"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
					"enviarEmail" => "true",
					"id" => $id,
					 "para" => '',
					"copia" =>  '',
					//"para" => $para,
					//"copia" => $copia,
					"idnovedad" => $idnovedad,
					"ciudorte" =>  $ciudorte,
					"url" => $url,
					"asunto" =>  $asunto,
					"reportado" =>$reportado,
					"encanombre" => $encanombre,
					"contenido" => $contenido
					);

					
					
				$ch = curl_init();
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/ServiEmail.php');
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
					curl_exec($ch);
					curl_close($ch);
		
}



function NovedadCall_envio_encargado() // recibe la informacion del formulario de busqueda
{  

		$para = $_POST['para']; 
		$copia = $_POST['copia']; 
		$asunto = $_POST['asunto']; 
		$idnovedad = $_POST['idnovedad']; 
		$encanombre = $_POST['encanombre']; 
		$contenido = $_POST['contenido']; 
		$Usuario = $_POST['Usuario'];
		$encargado = $_POST['encargado'];
		$ciudorte   =  $_POST["ciudorte"]; 
		$reportado  =  $_POST["reportado"]; 	
		$usuario_cierre =  $_POST["usuario_cierre"]; 
		
		

	   $data_mail = array(
					"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
					"enviarEmail" => "true",
					"id" => $id,
					"encargado" => $encargado,
					"usuario_cierre" => $usuario_cierre,
					"idnovedad" => $idnovedad,
					 "para" => '',
					"copia" =>  '',
					//"para" => $para,
					//"copia" => $copia,
					"idnovedad" => $idnovedad,
					"ciudorte" =>  $ciudorte,
					"asunto" =>  $asunto,
					"reportado" =>$reportado,
					"encanombre" => $encanombre,
					"Usuario" => $Usuario,
					"contenido" => $contenido
					);

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/ServiEmail.php');
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
					curl_exec($ch);
					curl_close($ch);
}

function buscar_detalle_id_rq() 
{ 
$id = $_POST['id']; 

   $Sins=q("SELECT *  FROM  aoacol_administra.requisiciond where
 aoacol_administra.requisiciond.requisicion = $id ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("id"=>$row['id'],"tipo"=>$row['tipo'],"estado"=>$row['estado'],"clase"=>$row['clase'],"tipoItem"=>$row['tipoItem'],"nombre_tipoitens"=>$row['nombre_tipoitens']
		,"observaciones"=>$row['observaciones'],"cantidad"=>$row['cantidad'],"valor"=>$row['valor'],"valor_total"=>$row['valor_total']   );
	}
		echo json_encode($tabledata);
}

function buscar_requi_id_rq() 
{ 
$id = $_POST['id']; 

  $user = q(" select  *  from  aoacol_administra.requisicion 
  where id = $id  ");  	
	
			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id"=>$row['id'],
		"placa"=>$row['placa'],
		"proveedor"=>$row['proveedor'],
		"perfil"=>$row['perfil'],
		"estado"=>$row['estado'],
		"solicitado_por"=>$row['solicitado_por'],
		"ciudad"=>$row['ciudad'],);
		

		
	
	}
	
		echo json_encode($tabledata);
}





function buscar_detalle_id() 
{ 
$id = $_POST['id'];

   $Sins=q(" SELECT provee_produc_serv.id as id_tipoitens,concat(provee_produc_serv.nombre , ' = ' ,sistema.nombre, ' = ', unidad_de_medida.nombre)  as nombre_tipoitens,
aoa_modulo.itenes.id as id_id ,aoa_modulo.itenes.novedad_requisicion ,aoa_modulo.itenes.tipo ,aoa_modulo.itenes.tipo ,aoa_modulo.itenes.clase ,
aoa_modulo.itenes.tipoItem ,aoa_modulo.itenes.estado , aoa_modulo.itenes.valor ,aoa_modulo.itenes.observaciones ,aoa_modulo.itenes.factura_proveedor ,aoa_modulo.itenes.tipo_cobro ,
aoa_modulo.itenes.cantidad ,aoa_modulo.itenes.tipo1 ,aoa_modulo.itenes.consecutivo_suno ,aoa_modulo.itenes.consecutivo_provee ,aoa_modulo.itenes.valor_total ,
aoa_modulo.itenes.id_vehiculo ,aoa_modulo.itenes.centro_operacion ,
aoacol_administra.estado_requisicion.color_co AS color_req ,
aoacol_administra.estado_requisicion.nombre AS nombre_req ,
aoa_modulo.itenes.centro_costo ,aoacol_administra.estado_requisicion.id
	FROM aoacol_administra.provee_produc_serv 
	INNER JOIN aoacol_administra.sistema ON provee_produc_serv.sistema = sistema.id  
	INNER JOIN aoacol_administra.unidad_de_medida ON provee_produc_serv.unidad_de_medida = unidad_de_medida.id
    LEFT OUTER JOIN aoa_modulo.itenes on provee_produc_serv.id = itenes.tipoItem  
    LEFT  JOIN aoacol_administra.estado_requisicion ON  itenes.estado  = 
	 estado_requisicion.id 
	where provee_produc_serv.activacion = 1  and provee_produc_serv.uso in (2,3)   
	and   aoa_modulo.itenes.novedad_requisicion = $id ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("id"=>$row['id_id'],
		"tipo"=>$row['tipo'],
		"color_req"=>$row['color_req'],
		"nombre_req"=>$row['nombre_req'],
		"estado"=>$row['estado'],
		"clase"=>$row['clase'],
		"tipoItem"=>$row['tipoItem'],
		"nombre_tipoitens"=>$row['nombre_tipoitens']
		,"observaciones"=>$row['observaciones'],"cantidad"=>$row['cantidad'],"valor"=>$row['valor'],"valor_total"=>$row['valor_total']   );
	}
		
		echo json_encode($tabledata);
		
		
}



function buscar_cantidad_novedad_admin() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipo_cierre.nombre_cierre ,count( aoa_modulo.tipo_cierre.nombre_cierre ) as cantidad
from aoa_modulo.tipo_cierre  LEFT  JOIN aoa_modulo.novedad
	on tipo_cierre.id_tipo_cierre = novedad.tipo_cierre group by  aoa_modulo.tipo_cierre.nombre_cierre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("nombre_cierre"=>$row['nombre_cierre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}


function buscar_cantidad_novedad_direOfi() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipo_cierre.nombre_cierre ,count( aoa_modulo.tipo_cierre.nombre_cierre ) as cantidad
from aoa_modulo.tipo_cierre  LEFT  JOIN aoa_modulo.novedad
	on tipo_cierre.id_tipo_cierre = novedad.tipo_cierre where novedad.ciudad_reporte = '$id_usuario' group by  aoa_modulo.tipo_cierre.nombre_cierre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("nombre_cierre"=>$row['nombre_cierre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}


function buscar_cantidad_novedad() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipo_cierre.nombre_cierre ,count( aoa_modulo.tipo_cierre.nombre_cierre ) as cantidad
from aoa_modulo.tipo_cierre  LEFT  JOIN aoa_modulo.novedad
	on tipo_cierre.id_tipo_cierre = novedad.tipo_cierre where encargado = $id_usuario group by  aoa_modulo.tipo_cierre.nombre_cierre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("nombre_cierre"=>$row['nombre_cierre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}

function buscar_cantidad_novedad_admim() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipo_cierre.nombre_cierre ,count( aoa_modulo.tipo_cierre.nombre_cierre ) as cantidad
from aoa_modulo.tipo_cierre  LEFT  JOIN aoa_modulo.novedad
	on tipo_cierre.id_tipo_cierre = novedad.tipo_cierre  group by  aoa_modulo.tipo_cierre.nombre_cierre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("nombre_cierre"=>$row['nombre_cierre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}




function buscar_cantidad_novedad_proveedor() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipo_cierre.nombre_cierre ,count( aoa_modulo.tipo_cierre.nombre_cierre ) as cantidad
from aoa_modulo.tipo_cierre  LEFT  JOIN aoa_modulo.novedad
	on tipo_cierre.id_tipo_cierre = novedad.tipo_cierre 
	
	LEFT  JOIN aoa_modulo.novedad_requisicion
	on aoa_modulo.novedad.id_novedad = novedad_requisicion.novedad	
 where aoa_modulo.novedad_requisicion.proveedor = 1425
	 group by  aoa_modulo.tipo_cierre.nombre_cierre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("nombre_cierre"=>$row['nombre_cierre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}



function buscar_cantidad_tipo_placa() 
{ 
$id_usuario = $_POST['id_usuario']; 


   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where encargado = $id_usuario and
	id_siniestro = ''
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}
function buscar_cantidad_tipo_placa_direOfi() 
{ 
$id_usuario = $_POST['id_usuario']; 


   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where novedad.ciudad_reporte  = '$id_usuario' and
	id_siniestro = ''
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}

function buscar_cantidad_tipo_placa_admin() 
{ 
$id_usuario = $_POST['id_usuario']; 


   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where 
	id_siniestro = ''
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}


function buscar_cantidad_tipo_siniestro() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where encargado = $id_usuario
	and id_placa = ''
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}
function buscar_cantidad_tipo_siniestro_direOfi() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where novedad.ciudad_reporte  = '$id_usuario'
	and id_placa = ''
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}


function buscar_cantidad_tipo_siniestro_admin() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where  id_placa = ''
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}



function buscar_cantidad_tipo_requisicon() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where encargado = $id_usuario and  novedad.tipo_cierre = 7
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}

function buscar_cantidad_tipo_requisicon_direOfi() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where novedad.ciudad_reporte  = '$id_usuario' and  novedad.tipo_cierre = 7
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}


function buscar_cantidad_tipo_requisicon_admin() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where  novedad.tipo_cierre = 7
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}

function buscar_cantidad_tipo_cotizacion_direOfi() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where novedad.ciudad_reporte  = '$id_usuario' and  novedad.tipo_cierre = 4
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}


function buscar_cantidad_tipo_cotizacion() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where encargado = $id_usuario and  novedad.tipo_cierre = 4
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}

function buscar_cantidad_tipo_cotizacion_admin() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where  novedad.tipo_cierre = 4
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}


function buscar_cantidad_tipo_requisicion() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where encargado = $id_usuario and  novedad.tipo_cierre = 8
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}
function buscar_cantidad_tipo_cerrada() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where encargado = $id_usuario and  novedad.tipo_cierre = 7
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}



function buscar_cantidad_tipo_requisicion_admin() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where encargado = $id_usuario and  novedad.tipo_cierre = 8
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}

function buscar_cantidad_tipo_cerrada_admin() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where   novedad.tipo_cierre = 7
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}



function buscar_cantidad_tipo_adim() 
{ 
$id_usuario = $_POST['id_usuario']; 
$fecha_inicio = $_POST['fecha_inicio']; 
$fecha_final = $_POST['fecha_final']; 
   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where novedad.fecha_creacion  BETWEEN '$fecha_inicio' AND '$fecha_final'
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}

function buscar_cantidad_tipo_direOfi() 
{ 
$id_usuario = $_POST['id_usuario']; 
$fecha_inicio = $_POST['fecha_inicio']; 
$fecha_final = $_POST['fecha_final']; 
   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where novedad.ciudad_reporte  = '$id_usuario' and  novedad.fecha_creacion  BETWEEN '$fecha_inicio' AND '$fecha_final'
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}



function buscar_cantidad_tipo() 
{ 
$id_usuario = $_POST['id_usuario']; 
$fecha_inicio = $_POST['fecha_inicio']; 
$fecha_final = $_POST['fecha_final']; 
   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where  encargado = $id_usuario and
	novedad.fecha_creacion  BETWEEN '$fecha_inicio' AND '$fecha_final'
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}

function buscar_cantidad_tipo_proveedor() 
{ 
$proveedor = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.tipoNovedad.nombre , aoa_modulo.tipoNovedad.color,count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo
LEFT  JOIN aoa_modulo.novedad_requisicion
	on aoa_modulo.novedad.id_novedad = novedad_requisicion.novedad	
	 where  novedad_requisicion.proveedor	 = $proveedor  
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("color"=>$row['color'],"nombre"=>$row['nombre'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}

function buscar_cantidad_tipo_cierre() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select * from aoa_modulo.tipo_cierre");  
        	while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array($row['id_tipo_cierre']);
	}
	
	echo json_encode($tabledata);
}

function buscar_cantidad_tipo_cierre_direOfi() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select * from aoa_modulo.tipo_cierre");  
        	while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array($row['id_tipo_cierre']);
	}
	
	echo json_encode($tabledata);
}


function buscar_cantidad_tipo_cierre_admin() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select * from aoa_modulo.tipo_cierre");  
        	while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array($row['id_tipo_cierre']);
	}
	
	echo json_encode($tabledata);
}


function buscar_cantidad_tipo_array_nombre() 

{ 

   $Sins=q("select * from aoa_modulo.tipo_cierre ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[] = $row['nombre_cierre'];
	}
	 $tems = array();
	 if($tabledata === null){ 
	 echo json_encode($tems);
		
		}else{
			
		echo json_encode($tabledata);
		
		}	
		
			

}

function buscar_oficina() 

{ 
$oficina = $_POST['oficina']; 
   $Sins=qo("select ciudad.nombre, ciudad.departamento from aoacol_aoacars.oficina
			LEFT  JOIN aoacol_aoacars.ciudad 
				on aoacol_aoacars.oficina.ciudad = aoacol_aoacars.ciudad.codigo
			 where oficina.id  =  $oficina ");  
        	
	
	 if($Sins === null){ 
		
		}else{
			
		 echo json_encode($Sins->departamento.' - '.$Sins->nombre);
		
		}	
		
			

}


function buscar_tipo_cierre() 

{ 

$Sins=q("SELECT * FROM `aoa_modulo`.`tipo_cierre` ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$table_data[]= array("id"=>$row['id_tipo_cierre'],"nombre"=>$row['nombre_cierre'] ,"color"=>$row['color_cierre']);
	}
		echo json_encode($table_data);
		
			

}

function buscar_estado_requi() 

{ 

$Sins=q("SELECT  * FROM aoacol_administra.estado_requisicion ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$table_data[]= array("id"=>$row['id'],"nombre"=>$row['nombre'] ,"color"=>$row['color_co']);
	}
		echo json_encode($table_data);
		
			

}

function buscar_cantidad_tipo_array_dos_admin() 
{ 
$id_usuario = $_POST['id_usuario']; 
$id_tipo_cierre = $_POST['id_tipo_cierre']; 
$tipo_cierre = $_POST['tipo_cierre']; 
$fecha_inicio = $_POST['fecha_inicio']; 
$fecha_final = $_POST['fecha_final']; 
$id = $_POST['id']; 

   $Sins=q("select  count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad 
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where tipo_cierre = $id_tipo_cierre
	and novedad.fecha_creacion  BETWEEN '$fecha_inicio' AND '$fecha_final'
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
      
	 	if($Sins){ 
		while($row = mysql_fetch_array($Sins)){
		
			$tabledata[] = array("cantidad"=>$row['cantidad'],"nombre"=>$tipo_cierre,"id"=>$id);
		}
		}else{
	      $tabledata[] = array("cantidad"=>'',"nombre"=>$tipo_cierre,"id"=>$id);
	
	}
	
			
		echo json_encode($tabledata);
		

		
			

}


function buscar_cantidad_tipo_array_dos_direOfi() 
{ 
$id_usuario = $_POST['id_usuario']; 
$id_tipo_cierre = $_POST['id_tipo_cierre']; 
$tipo_cierre = $_POST['tipo_cierre']; 
$fecha_inicio = $_POST['fecha_inicio']; 
$fecha_final = $_POST['fecha_final']; 
$id = $_POST['id']; 

   $Sins=q("select  count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad 
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where novedad.ciudad_reporte = '$id_usuario' and tipo_cierre = $id_tipo_cierre
	and novedad.fecha_creacion  BETWEEN '$fecha_inicio' AND '$fecha_final'
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
      
	 	if($Sins){ 
		while($row = mysql_fetch_array($Sins)){
		
			$tabledata[] = array("cantidad"=>$row['cantidad'],"nombre"=>$tipo_cierre,"id"=>$id);
		}
		}else{
	      $tabledata[] = array("cantidad"=>'',"nombre"=>$tipo_cierre,"id"=>$id);
	
	}
	
			
		echo json_encode($tabledata);
		

		
			

}

function buscar_cantidad_tipo_array_dos() 
{ 


$id_usuario = $_POST['id_usuario']; 
$id_tipo_cierre = $_POST['id_tipo_cierre']; 
$tipo_cierre = $_POST['tipo_cierre']; 
$fecha_inicio = $_POST['fecha_inicio']; 
$fecha_final = $_POST['fecha_final']; 
$id = $_POST['id']; 

   $Sins=q("select  count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad 
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where encargado = $id_usuario and tipo_cierre = $id_tipo_cierre
	and novedad.fecha_creacion  BETWEEN '$fecha_inicio' AND '$fecha_final'
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
      
	 	if($Sins){ 
		while($row = mysql_fetch_array($Sins)){
		
			$tabledata[] = array("cantidad"=>$row['cantidad'],"nombre"=>$tipo_cierre,"id"=>$id);
		}
		}else{
	      $tabledata[] = array("cantidad"=>'',"nombre"=>$tipo_cierre,"id"=>$id);
	
	}
	
			
		echo json_encode($tabledata);
		

		
			

}


function buscar_cantidad_tipo_array_ciudad_direOfi() 
{ 
$id_usuario = $_POST['id_usuario']; 
$ciudad_reporte = $_POST['ciudad_reporte']; 
$ciudad_reporte_nombre = $_POST['ciudad_reporte_nombre']; 
$id = $_POST['id']; 
$fecha_inicio = $_POST['fecha_inicio']; 
$fecha_final = $_POST['fecha_final']; 
   $Sins=q("select  count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad 
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where novedad.ciudad_reporte = '$id_usuario' and novedad.ciudad_reporte = '$ciudad_reporte' and novedad.fecha_creacion  BETWEEN '$fecha_inicio' AND '$fecha_final'
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
      
	 	if($Sins){ 
		while($row = mysql_fetch_array($Sins)){
		
			$tabledata[] = array("cantidad"=>$row['cantidad'],"nombre"=>$ciudad_reporte_nombre,"id"=>$id);
		}
		}else{
	      $tabledata[] = array("cantidad"=>'',"nombre"=>$ciudad_reporte_nombre,"id"=>$id);
	
	}
	
			
		echo json_encode($tabledata);
		

		
			

}





function buscar_cantidad_tipo_array_ciudad_admin() 
{ 
$id_usuario = $_POST['id_usuario']; 
$ciudad_reporte = $_POST['ciudad_reporte']; 
$ciudad_reporte_nombre = $_POST['ciudad_reporte_nombre']; 
$id = $_POST['id']; 
$fecha_inicio = $_POST['fecha_inicio']; 
$fecha_final = $_POST['fecha_final']; 
   $Sins=q("select  count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad 
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where novedad.ciudad_reporte = '$ciudad_reporte' and novedad.fecha_creacion  BETWEEN '$fecha_inicio' AND '$fecha_final'
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
      
	 	if($Sins){ 
		while($row = mysql_fetch_array($Sins)){
		
			$tabledata[] = array("cantidad"=>$row['cantidad'],"nombre"=>$ciudad_reporte_nombre,"id"=>$id);
		}
		}else{
	      $tabledata[] = array("cantidad"=>'',"nombre"=>$ciudad_reporte_nombre,"id"=>$id);
	
	}
	
			
		echo json_encode($tabledata);
		

		
			

}





function buscar_cantidad_tipo_array_ciudad() 
{ 
$id_usuario = $_POST['id_usuario']; 
$ciudad_reporte = $_POST['ciudad_reporte']; 
$ciudad_reporte_nombre = $_POST['ciudad_reporte_nombre']; 
$id = $_POST['id']; 
$fecha_inicio = $_POST['fecha_inicio']; 
$fecha_final = $_POST['fecha_final']; 
   $Sins=q("select  
   
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad 
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo
	 	LEFT JOIN  aoa_modulo.novedad_requisicion
			on aoa_modulo.novedad_requisicion.novedad = novedad.id_novedad
			where novedad_requisicion.proveedor = 1425 and novedad.ciudad_reporte =
	  '$ciudad_reporte' and novedad.fecha_creacion
	    BETWEEN '$fecha_inicio' AND '$fecha_final'
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;  ");  
      
	 	if($Sins){ 
		while($row = mysql_fetch_array($Sins)){
		
			$tabledata[] = array("cantidad"=>$row['cantidad'],"nombre"=>$ciudad_reporte_nombre,"id"=>$id);
		}
		}else{
	      $tabledata[] = array("cantidad"=>'',"nombre"=>$ciudad_reporte_nombre,"id"=>$id);
	
	}
	
			
		echo json_encode($tabledata);
		

		
			

}



function buscar_cantidad_tipo_array($id_tipo_cierre) 
{ 
try{
	

$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select  count( aoa_modulo.tipoNovedad.nombre ) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad 
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where encargado = $id_usuario  and tipo_cierre = $id_tipo_cierre
	group by   aoa_modulo.tipoNovedad.nombre 
having count(*) > 1;");  
 $arrayDos = array();
	$iz = "";
   while($row = mysql_fetch_array($Sins)){
		$iz .= $row['cantidad'].",";
	}
	 $k = substr($iz, 0, -1);
	array_push($arrayDos,$k);
	
	echo json_encode($arrayDos);
	
}catch(Exception $e){
 echo json_encode(array("status"=>"NO"));  	
}
	
	
}


function buscar_cantidad_ciudad_proveedor() 
{ 
$id_usuario = $_POST['id_usuario']; 

   $Sins=q("select aoa_modulo.novedad.ciudad_reporte ,count( aoa_modulo.novedad.ciudad_reporte) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo 
		LEFT  JOIN aoa_modulo.novedad_requisicion
	on aoa_modulo.novedad.id_novedad = novedad_requisicion.novedad	
 where aoa_modulo.novedad_requisicion.proveedor = 1425
	 group by   aoa_modulo.novedad.ciudad_reporte
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("ciudad_reporte"=>$row['ciudad_reporte'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}


function buscar_cantidad_ciudad_direOfi() 
{ 
$id_usuario = $_POST['id_usuario']; 
$fecha_inicio = $_POST['fecha_inicio']; 
$fecha_final = $_POST['fecha_final']; 

  $Sins=q("select aoa_modulo.novedad.ciudad_reporte ,count( aoa_modulo.novedad.ciudad_reporte) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where novedad.ciudad_reporte  = '$id_usuario' and novedad.fecha_creacion  BETWEEN '$fecha_inicio' AND '$fecha_final'
	group by   aoa_modulo.novedad.ciudad_reporte
having count(*) > 1;");
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("ciudad_reporte"=>$row['ciudad_reporte'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}



function buscar_cantidad_ciudad_admin() 
{ 
$id_usuario = $_POST['id_usuario']; 
$fecha_inicio = $_POST['fecha_inicio']; 
$fecha_final = $_POST['fecha_final']; 

   $Sins=q("select aoa_modulo.novedad.ciudad_reporte ,count( aoa_modulo.novedad.ciudad_reporte) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where novedad.fecha_creacion  BETWEEN '$fecha_inicio' AND '$fecha_final'
	group by   aoa_modulo.novedad.ciudad_reporte
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("ciudad_reporte"=>$row['ciudad_reporte'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}


function buscar_cantidad_ciudad() 
{ 
$id_usuario = $_POST['id_usuario']; 
$fecha_inicio = $_POST['fecha_inicio']; 
$fecha_final = $_POST['fecha_final']; 

   $Sins=q("select aoa_modulo.novedad.ciudad_reporte ,count( aoa_modulo.novedad.ciudad_reporte) as cantidad
from aoa_modulo.tipoNovedad  LEFT  JOIN aoa_modulo.novedad
	on aoa_modulo.tipoNovedad.idtipoNovedad = novedad.id_tipo where encargado = $id_usuario and novedad.fecha_creacion  BETWEEN '$fecha_inicio' AND '$fecha_final'
	group by   aoa_modulo.novedad.ciudad_reporte
having count(*) > 1;");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("ciudad_reporte"=>$row['ciudad_reporte'],"cantidad"=>$row['cantidad']);
	}
		echo json_encode($tabledata);
}


function buscar_cantidad_novedad_tipo_direOfi() 
{ 

   $Sins=q("select aoa_modulo.tipo_cierre.nombre_cierre 
from aoa_modulo.tipo_cierre  LEFT  JOIN aoa_modulo.novedad
	on tipo_cierre.id_tipo_cierre = novedad.tipo_cierre where novedad.ciudad_reporte = '$id_usuario' group by  aoa_modulo.tipo_cierre.nombre_cierre 
having count(*) > 1; ");  
        	
		  while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= $S->id_tipo;
			echo$data;
		}
}


function buscar_cantidad_novedad_tipo() 
{ 

   $Sins=q("select aoa_modulo.tipo_cierre.nombre_cierre 
from aoa_modulo.tipo_cierre  LEFT  JOIN aoa_modulo.novedad
	on tipo_cierre.id_tipo_cierre = novedad.tipo_cierre where novedad.ciudad_reporte = '$id_usuario' group by  aoa_modulo.tipo_cierre.nombre_cierre 
having count(*) > 1; ");  
        	
		  while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= $S->id_tipo;
			echo$data;
		}
}

function buscar_cantidad_novedad_tipo_admin() 
{ 

   $Sins=q("select aoa_modulo.tipo_cierre.nombre_cierre 
from aoa_modulo.tipo_cierre  LEFT  JOIN aoa_modulo.novedad
	on tipo_cierre.id_tipo_cierre = novedad.tipo_cierre where  group by  aoa_modulo.tipo_cierre.nombre_cierre 
having count(*) > 1; ");  
        	
		  while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= $S->id_tipo;
			echo$data;
		}
}

function buscar_proveedor_id() 
{ 
$id = $_POST['id']; 
   $Sins=qo("SELECT proveedor.email , ciudad.nombre as nombre_ciudad,proveedor.identificacion,proveedor.ciudad as ciudad_codigo,
   proveedor.nombre  as nombre_prove
 FROM `aoacol_administra`.`proveedor`
   LEFT OUTER JOIN aoacol_aoacars.ciudad
	on proveedor.ciudad = ciudad.codigo 
 where proveedor.id = '$id' ");  
        	
		echo json_encode($Sins);
}



function login_proveedor() 
{ 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/x-www-form-urlencoded");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$usuario = $_POST['usuario']; 
$clave = $_POST['clave']; 
$Sins=q("SELECT *  FROM `aoacol_administra`.`proveedor` where ciudad  = '$usuario' and identificacion  = '$clave' ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$table_data[]= array("id"=>$row['id'],
		"identificacion"=>$row['identificacion'],
		"nombre"=>$row['nombre'],
		"ciudad"=>$row['ciudad_nombre'],
		"email"=>$row['email'],
		"celular"=>$row['celular'],
		"contacto"=>$row['contacto'],
		"telefono1"=>$row['telefono1']);
	}
     if($table_data == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($table_data);
		
		}		
}

function login_proveedor_val() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("SELECT *  FROM `aoacol_administra`.`proveedor` where identificacion  = '$usuario' ");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins);
		
		}		
}

function usuario_novedad_req() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where encargado = $usuario  and novedad.tipo_cierre = 7
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}

function usuario_novedad_cerr_direOfi() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where novedad.ciudad_reporte = '$usuario'  and novedad.tipo_cierre = 8
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}


function usuario_novedad_req_direOfi() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where novedad.ciudad_reporte = '$usuario' and novedad.tipo_cierre = 7
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}

function usuario_novedad_cerr() 
{ 
$encargado = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where novedad.tipo_cierre = 7
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}

function usuario_novedad_req_admin() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where novedad.tipo_cierre = 7
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}

function usuario_novedad_cerr_admin() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where novedad.tipo_cierre = 8
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}





function usuario_novedad_admin() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}


function usuario_novedad_placa() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where encargado = $usuario and id_siniestro = ''
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}


function usuario_novedad_placa_direOfi() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where novedad.ciudad_reporte = '$usuario' and id_siniestro = ''
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}



function usuario_novedad_cotizacion() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where encargado = $usuario  and novedad.tipo_cierre = 4
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}


function usuario_novedad_cotizacion_direOfi() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where novedad.ciudad_reporte = '$usuario'  and novedad.tipo_cierre = 4
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}



function usuario_novedad_siniestro() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where encargado = $usuario and id_placa =''
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}



function usuario_novedad_siniestro_direOfi() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where ciudad_reporte = '$usuario' and id_placa =''
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}



function usuario_novedad_placa_admin() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where  id_siniestro = ''
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}




function usuario_novedad_cotizacion_admin() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where  novedad.tipo_cierre = 4
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}




function usuario_novedad_siniestro_admin() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where id_placa =''
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}



function usuario_novedad() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where encargado = $usuario 
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}

function usuario_novedad_direOfi() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad  where novedad.ciudad_reporte = '$usuario' 
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}





function usuario_novedad_proveedor() 
{ 
$usuario = $_POST['usuario']; 
$Sins=qo("select  count( aoa_modulo.novedad.id_novedad ) as cantidad
from aoa_modulo.novedad 
LEFT  JOIN aoa_modulo.novedad_requisicion
	on aoa_modulo.novedad.id_novedad = novedad_requisicion.novedad	
 where aoa_modulo.novedad_requisicion.proveedor = $usuario 
having count(*) > 1;");  
        	
		
     if($Sins == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($Sins->cantidad);
		
		}		
}


function buscar_proveedor_id_tabla() 
{ 
$id = $_POST['id']; 
   $Sins=q("SELECT proveedor.id, proveedor.email,proveedor.nombre ,proveedor.celular ,proveedor.contacto, 
   proveedor.telefono1, ciudad.nombre as ciudad_nombre
 FROM `aoacol_administra`.`proveedor`
   LEFT OUTER JOIN aoacol_aoacars.ciudad
	on proveedor.ciudad = ciudad.codigo 
 where proveedor.id = '$id' ");  
        	
			while($row = mysql_fetch_array($Sins)){
		$table_data[]= array("idtipoNovedad"=>$row['id'],
		"nombre"=>$row['nombre'],
		"ciudad"=>$row['ciudad_nombre'],
		"email"=>$row['email'],
		"celular"=>$row['celular'],
		"contacto"=>$row['contacto'],
		"telefono1"=>$row['telefono1']);
	}
		echo json_encode($table_data);
}


function sede_prov_id() 
{ 
 $proveedor = $_POST['proveedor']; 
  $id = $_POST['id']; 
   $Sins=q("SELECT  prov_sede.id , ciudad.nombre AS ciudad , prov_sede.nombre  FROM `aoacol_administra`.`prov_sede` 
    LEFT OUTER JOIN aoacol_aoacars.ciudad
	on prov_sede.ciudad = ciudad.codigo 
   where prov_sede.id = $id  ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("id"=>$row['id'],"ciudad"=>$row['ciudad'],"nombre"=>$row['nombre']);
	}
		
		if($tabledata == null ){
			echo'0';
		}else{
			
		echo json_encode($tabledata);
		
		}
}


function sede_prov() 
{ 
 $proveedor = $_POST['proveedor']; 
   $Sins=q("SELECT  * FROM `aoacol_administra`.`prov_sede`   where proveedor  = ' $proveedor' ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("id"=>$row['id'],"ciudad"=>$row['ciudad'],"nombre"=>$row['nombre']);
	}
		
		if($tabledata == null ){
			
		}else{
			
		echo json_encode($tabledata);
		
		}
}

function buscar_proveedor() 
{ 

   $Sins=q("SELECT  * FROM `aoacol_administra`.`proveedor` ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("id"=>$row['id'],"nombre"=>$row['nombre']);
	}
		echo json_encode($tabledata);
}

function buscar_ciudad() 
{ 

   $Sins=q("SELECT * FROM `aoacol_administra`.`ciudad` LIMIT 1000; ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$table_data[]= array("id"=>$row['idtipoNovedad'],"nombre"=>$row['nombre']);
	}
		echo json_encode($table_data);
}

function buscar_tipo() 
{ 

   $Sins=q("SELECT * FROM `aoa_modulo`.`tipoNovedad` ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$table_data[]= array("id"=>$row['idtipoNovedad'],"nombre"=>$row['nombre'] ,"color"=>$row['color']);
	}
		echo json_encode($table_data);
}

function buscar_placa() // recibe la informacion del formulario de busqueda
{ 

$placa = $_POST['placa']; 

			$Sins=q("SELECT  * FROM `aoacol_aoacars`.`vehiculo` where placa ='$placa'");
			
		while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
			if($data === null){ 
				echo $data;
			}else{
				
			}
		}


}

function buscar_marca() // recibe la informacion del formulario de busqueda
{ 

$marca = $_POST['marca']; 

			$Sins=q("SELECT  * FROM `aoacol_aoacars`.`marca_vehiculo` where id ='$marca'");
			
		while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
			if($data === null){ 
				echo$data;
			}else{
				
			}
		}


}




function buscar_linea() // recibe la informacion del formulario de busqueda
{ 

$linea = $_POST['linea']; 

			$Sins=q("SELECT  * FROM `aoacol_aoacars`.`linea_vehiculo` where id ='$linea'");
			
		while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
			if($data === null){ 
				echo$data;
			}else{
				
			}
		}


}

function ingresaNovedad_siniestro() // recibe la informacion del formulario de busqueda
{ 
  $id_tipo=$_POST['id_tipo'];
  $id_siniestro=$_POST['id_siniestro'];
  $id_placa =$_POST['id_placa'];
  $novedad=$_POST['novedad'];
  $solicitante=$_POST['solicitante'];
  $ciudad=$_POST['ciudad'];
  $fecha_creacion=$_POST['fecha_creacion'];

      $qry = q("INSERT INTO `aoa_modulo`.`novedad`
	  (id_tipo, novedad,fecha_creacion,ciudad,id_siniestro,id_placa,solicitante) 
	  VALUES ('$id_tipo', '$novedad','$fecha_creacion','$ciudad', '$id_siniestro' ,'$id_placa', '$solicitante' )");
      buscar_novedad();
}

function ciudad_siniestro(){
	
$codigo = $_POST['codigo'];

$Sins=q("select * from aoacol_aoacars.ciudad where codigo ='$codigo' ");  
        	
	while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
		}
}

function usuario_Encargado_placa(){
	
$ciudad = $_POST['ciudad'];

$user = q("select * from aoacol_aoacars.operario");  	
		
        	
		while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id"=>$row['id'],"nombre"=>$row['nombre'],"apellido"=>$row['apellido']);
	}
		echo json_encode($tabledata);
		
		
		
}




function usuarioEncargado(){
	
$ciudad = $_POST['ciudad'];

$ofic=qo("select * from aoacol_aoacars.oficina where nombre='$ciudad' ");

$user = q("select * from aoacol_aoacars.operario where oficina = '$ofic->id' and fecha_retiro = '0000-00-00' ");  	
		
        	
		while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id"=>$row['id'],"nombre"=>$row['nombre'],"apellido"=>$row['apellido']);
	}
		echo json_encode($tabledata);
		
		
		
}


function buscar_vehiculos_remplazo(){
	
$ciudad = $_POST['ciudad'];
$fecha_actual = $_POST['fecha_actual'];

	$user = q("SELECT o.nombre as oficina_nombre, o.contacto as contacto_oficina, o.telefono as telefono_oficina, vehiculo.placa as placa , vehiculo.linea as linea_vehiculo , 
		vehiculo.servicio as servicio_vehiculo, vehiculo.tipo_caja, vehiculo.cilindraje, ubicacion.estado

		 from ubicacion  
		LEFT OUTER JOIN  vehiculo 
		   on   vehiculo.id  = ubicacion.vehiculo
		LEFT OUTER JOIN aoacol_aoacars.oficina o
		   on   vehiculo.ultima_ubicacion  = o.id 
			WHERE ubicacion.estado = 2 AND o.id = 2 AND ubicacion.fecha_final >= '$fecha_actual'");  	
		
        	
		while($row = mysql_fetch_array($user)){
		$tabledata[]= array("oficina_nombre"=>$row['oficina_nombre'],
		"contacto_oficina"=>$row['contacto_oficina'],
		"telefono_oficina"=>$row['telefono_oficina'] ,
		"placa"=>$row['placa'],
		"linea_vehiculo"=>$row['linea_vehiculo'],
		"servicio_vehiculo"=>$row['servicio_vehiculo'],
		"tipo_caja"=>$row['tipo_caja'],
		"cilindraje"=>$row['cilindraje'],
		"estado"=>$row['estado']
		);
	}
		echo json_encode($tabledata);
			
		
}

function buscar_encargado(){
	
$id = $_POST['id'];
$user = qo("select * from aoacol_aoacars.operario where id = '$id'  ");  	
		echo json_encode($user);
}


function buscar_novedad_operario_plata(){
	
$id = $_POST['id'];
$user = q("  select  aoa_modulo.novedad.id_novedad as id_novedad,

      vehiculo.modelo  as modelo_vehiculo ,
					vehiculo.placa  as placa ,
					linea_vehiculo.nombre  as linea_vehiculo,
					marca_vehiculo.nombre  as marca_vehiculo,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.depar_reporte_otro as depar_reporte_otro,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
    aoa_modulo.novedad.acti_caarga as acti_caarga,
     aoa_modulo.novedad.cierre_oper as cierre_oper,
      aoa_modulo.novedad.solicitante as solicitante,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
     aoa_modulo.novedad.encargado as encargado, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_asegurado,

   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad,
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
     aoa_modulo.novedad.ciudad_reporte_otro as ciudad_reporte, 
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.reportado_otro as reportado, 
   aoa_modulo.novedad.cierre as cierre, 
    aoa_modulo.tipo_cierre.nombre_cierre as estado_cierre, 
     aoa_modulo.tipo_cierre.color_cierre as estado_color, 

      aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
    aoa_modulo.tipoNovedad.color as color_tipoN, 
  aoa_modulo.tipoNovedad.idtipoNovedad as id_nombre_tipoN, 
   aoacol_aoacars.operario.nombre as nombre_opera,
      aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	 LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.siniestro on novedad.id_siniestro = siniestro.numero 
   	LEFT OUTER JOIN aoacol_aoacars.vehiculo
		 on aoacol_aoacars.siniestro.placa = vehiculo.placa 
	  
					LEFT OUTER JOIN aoacol_aoacars.linea_vehiculo ON  vehiculo.linea  = linea_vehiculo.id 
					LEFT OUTER JOIN aoacol_aoacars.marca_vehiculo ON  linea_vehiculo.marca  = marca_vehiculo.id 
		WHERE  aoa_modulo.novedad.id_novedad ='$id'  ");  	
	
			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
		"kmIngreso"=>$row['kmIngreso'],
		"fechaIngreso"=>$row['fechaIngreso'],
		"fechaSalida"=>$row['fechaSalida'],
			
		"estado_cierre"=>$row['estado_cierre'],
		"color_cierre"=>$row['color_cierre'],
		"color_tipoN"=>$row['color_tipoN'],
		"marca_vehiculo"=>$row['marca_vehiculo'],
		"linea_vehiculo"=>$row['linea_vehiculo'],
		"modelo_vehiculo"=>$row['modelo_vehiculo'],
		"estado_color"=>$row['estado_color'],
		
		"asegurado"=>$row['asegurado'],
		"email_asegurado"=>$row['email_asegurado'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		"acti_caarga"=>$row['acti_caarga'],
		"cierre_oper"=>$row['cierre_oper'],
		"placa"=>$row['placa'],
		"kmFinal"=>$row['kmFinal'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"fecha_creacion"=>$row['fecha_creacion'],
		   "encargado"=>$row['encargado'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"id_nombre_tipoN"=>$row['id_nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"depar_reporte_otro"=>$row['depar_reporte_otro'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}




function buscar_novedad_operario(){
	
$id = $_POST['id'];
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   MAX(aoacol_aoacars.ubicacion.fecha_inicial)as fechaIngreso,
	MAX(aoacol_aoacars.ubicacion.odometro_inicial) as kmIngreso,
	MAX(aoacol_aoacars.ubicacion.fecha_final) as fechaSalida,
	MAX(aoacol_aoacars.ubicacion.odometro_final) as kmFinal,
   aoacol_aoacars.vehiculo.placa,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.depar_reporte_otro as depar_reporte_otro,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
     aoa_modulo.novedad.encargado as encargado, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_asegurado,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad,
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
     aoa_modulo.novedad.ciudad_reporte_otro as ciudad_reporte, 
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.reportado_otro as reportado, 
   aoa_modulo.novedad.cierre as cierre, 
      aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
  aoa_modulo.tipoNovedad.idtipoNovedad as id_nombre_tipoN, 
   aoacol_aoacars.operario.nombre as nombre_opera,
      aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.siniestro on novedad.id_siniestro = siniestro.numero 
   	LEFT OUTER JOIN aoacol_aoacars.vehiculo
		 on aoacol_aoacars.siniestro.placa = vehiculo.placa 
		LEFT OUTER JOIN aoacol_aoacars.ubicacion
		 on vehiculo.id = ubicacion.vehiculo 	  
		WHERE  aoa_modulo.novedad.id_novedad ='$id'  ");  	
	
			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
		"kmIngreso"=>$row['kmIngreso'],
		"fechaIngreso"=>$row['fechaIngreso'],
		"fechaSalida"=>$row['fechaSalida'],
		
		"asegurado"=>$row['asegurado'],
		"email_asegurado"=>$row['email_asegurado'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		
		"placa"=>$row['placa'],
		"kmFinal"=>$row['kmFinal'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"fecha_creacion"=>$row['fecha_creacion'],
		   "encargado"=>$row['encargado'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"id_nombre_tipoN"=>$row['id_nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"depar_reporte_otro"=>$row['depar_reporte_otro'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}

function buscar_novedades(){

	
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.placa_aoa as placa_aoa,
   aoa_modulo.novedad.aseguradora_aoa as aseguradora_aoa, 
   saoa.aseguradora_nombre as aseguradora_aoa,
   scliente.aseguradora_nombre as aseguradora_cliente,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
   aoa_modulo.novedad.reportado_otro as reportado,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.id_placa as id_placa, 
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.tipo_cierre.nombre_cierre as nombre_cierre, 
   aoa_modulo.tipo_cierre.color_cierre as color_cierre,
   aoa_modulo.tipo_cierre.id_tipo_cierre as tipo_cierre,
   
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoa_modulo.tipoNovedad.color as color_tipoN,
   aoa_modulo.tipoNovedad.idtipoNovedad as id_novedad_tipo,
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	  LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.seguros saoa
   on  aoa_modulo.novedad.aseguradora_aoa = saoa.id 
    LEFT OUTER JOIN aoacol_aoacars.seguros scliente
   on  aoa_modulo.novedad.aseguradora_cliente  = scliente.id 
 ORDER by id_novedad DESC ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"asegurado"=>$row['asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		"placa_aoa"=>$row['placa_aoa'],
		"aseguradora_aoa"=>$row['aseguradora_aoa'],
		"color_cierre"=>$row['color_cierre'],
		"nombre_cierre"=>$row['nombre_cierre'],
		"aseguradora_cliente"=>$row['aseguradora_cliente'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"fecha_creacion"=>$row['fecha_creacion'],
		"id_placa"=>$row['id_placa'],
		"reportado"=>$row['reportado'],
		"color_tipoN"=>$row['color_tipoN'],
		"id_novedad_tipo"=>$row['id_novedad_tipo'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}

function buscar_novedades_id(){
$encargado = $_POST['encargado'];
	
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.placa_aoa as placa_aoa,
   aoa_modulo.novedad.aseguradora_aoa as aseguradora_aoa, 
   saoa.aseguradora_nombre as aseguradora_aoa,
   scliente.aseguradora_nombre as aseguradora_cliente,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
   aoa_modulo.novedad.reportado_otro as reportado,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.id_placa as id_placa, 
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.tipo_cierre.nombre_cierre as nombre_cierre, 
   aoa_modulo.tipo_cierre.color_cierre as color_cierre,
   aoa_modulo.tipo_cierre.id_tipo_cierre as tipo_cierre,
   
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoa_modulo.tipoNovedad.color as color_tipoN,
   aoa_modulo.tipoNovedad.idtipoNovedad as id_novedad_tipo,
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	  LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.seguros saoa
   on  aoa_modulo.novedad.aseguradora_aoa = saoa.id 
    LEFT OUTER JOIN aoacol_aoacars.seguros scliente
   on  aoa_modulo.novedad.aseguradora_cliente  = scliente.id
   where aoacol_aoacars.operario.id = $encargado
 ORDER by id_novedad DESC ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"asegurado"=>$row['asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		"placa_aoa"=>$row['placa_aoa'],
		"aseguradora_aoa"=>$row['aseguradora_aoa'],
		"color_cierre"=>$row['color_cierre'],
		"nombre_cierre"=>$row['nombre_cierre'],
		"aseguradora_cliente"=>$row['aseguradora_cliente'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"fecha_creacion"=>$row['fecha_creacion'],
		"id_placa"=>$row['id_placa'],
		"reportado"=>$row['reportado'],
		"color_tipoN"=>$row['color_tipoN'],
		"id_novedad_tipo"=>$row['id_novedad_tipo'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}





function buscar_novedades_id_cotizar(){
$encargado = $_POST['encargado'];
	
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.placa_aoa as placa_aoa,
   aoa_modulo.novedad.aseguradora_aoa as aseguradora_aoa, 
   saoa.aseguradora_nombre as aseguradora_aoa,
   scliente.aseguradora_nombre as aseguradora_cliente,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
   aoa_modulo.novedad.reportado_otro as reportado,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.id_placa as id_placa, 
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.tipo_cierre.nombre_cierre as nombre_cierre, 
   aoa_modulo.tipo_cierre.color_cierre as color_cierre,
   aoa_modulo.tipo_cierre.id_tipo_cierre as tipo_cierre,
   
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoa_modulo.tipoNovedad.color as color_tipoN,
   aoa_modulo.tipoNovedad.idtipoNovedad as id_novedad_tipo,
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	  LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.seguros saoa
   on  aoa_modulo.novedad.aseguradora_aoa = saoa.id 
    LEFT OUTER JOIN aoacol_aoacars.seguros scliente
   on  aoa_modulo.novedad.aseguradora_cliente  = scliente.id
   where aoacol_aoacars.operario.id = $encargado and  aoa_modulo.novedad.tipo_cierre = 4 
 ORDER by id_novedad DESC ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"asegurado"=>$row['asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		"placa_aoa"=>$row['placa_aoa'],
		"aseguradora_aoa"=>$row['aseguradora_aoa'],
		"color_cierre"=>$row['color_cierre'],
		"nombre_cierre"=>$row['nombre_cierre'],
		"aseguradora_cliente"=>$row['aseguradora_cliente'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"fecha_creacion"=>$row['fecha_creacion'],
		"id_placa"=>$row['id_placa'],
		"reportado"=>$row['reportado'],
		"color_tipoN"=>$row['color_tipoN'],
		"id_novedad_tipo"=>$row['id_novedad_tipo'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}


function buscar_novedades_id_cerrada(){
$encargado = $_POST['encargado'];
	
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.placa_aoa as placa_aoa,
   aoa_modulo.novedad.aseguradora_aoa as aseguradora_aoa, 
   saoa.aseguradora_nombre as aseguradora_aoa,
   scliente.aseguradora_nombre as aseguradora_cliente,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
   aoa_modulo.novedad.reportado_otro as reportado,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.id_placa as id_placa, 
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.tipo_cierre.nombre_cierre as nombre_cierre, 
   aoa_modulo.tipo_cierre.color_cierre as color_cierre,
   aoa_modulo.tipo_cierre.id_tipo_cierre as tipo_cierre,
   
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoa_modulo.tipoNovedad.color as color_tipoN,
   aoa_modulo.tipoNovedad.idtipoNovedad as id_novedad_tipo,
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	  LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.seguros saoa
   on  aoa_modulo.novedad.aseguradora_aoa = saoa.id 
    LEFT OUTER JOIN aoacol_aoacars.seguros scliente
   on  aoa_modulo.novedad.aseguradora_cliente  = scliente.id
   where aoacol_aoacars.operario.id = $encargado and  aoa_modulo.novedad.tipo_cierre = 8 
 ORDER by id_novedad DESC ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"asegurado"=>$row['asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		"placa_aoa"=>$row['placa_aoa'],
		"aseguradora_aoa"=>$row['aseguradora_aoa'],
		"color_cierre"=>$row['color_cierre'],
		"nombre_cierre"=>$row['nombre_cierre'],
		"aseguradora_cliente"=>$row['aseguradora_cliente'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"fecha_creacion"=>$row['fecha_creacion'],
		"id_placa"=>$row['id_placa'],
		"reportado"=>$row['reportado'],
		"color_tipoN"=>$row['color_tipoN'],
		"id_novedad_tipo"=>$row['id_novedad_tipo'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}



function buscar_novedades_id_requisicion(){
$encargado = $_POST['encargado'];
	
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.placa_aoa as placa_aoa,
   aoa_modulo.novedad.aseguradora_aoa as aseguradora_aoa, 
   saoa.aseguradora_nombre as aseguradora_aoa,
   scliente.aseguradora_nombre as aseguradora_cliente,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
   aoa_modulo.novedad.reportado_otro as reportado,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.id_placa as id_placa, 
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.tipo_cierre.nombre_cierre as nombre_cierre, 
   aoa_modulo.tipo_cierre.color_cierre as color_cierre,
   aoa_modulo.tipo_cierre.id_tipo_cierre as tipo_cierre,
   
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoa_modulo.tipoNovedad.color as color_tipoN,
   aoa_modulo.tipoNovedad.idtipoNovedad as id_novedad_tipo,
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	  LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.seguros saoa
   on  aoa_modulo.novedad.aseguradora_aoa = saoa.id 
    LEFT OUTER JOIN aoacol_aoacars.seguros scliente
   on  aoa_modulo.novedad.aseguradora_cliente  = scliente.id
   where aoacol_aoacars.operario.id = $encargado and  aoa_modulo.novedad.tipo_cierre = 7
 ORDER by id_novedad DESC ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"asegurado"=>$row['asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		"placa_aoa"=>$row['placa_aoa'],
		"aseguradora_aoa"=>$row['aseguradora_aoa'],
		"color_cierre"=>$row['color_cierre'],
		"nombre_cierre"=>$row['nombre_cierre'],
		"aseguradora_cliente"=>$row['aseguradora_cliente'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"fecha_creacion"=>$row['fecha_creacion'],
		"id_placa"=>$row['id_placa'],
		"reportado"=>$row['reportado'],
		"color_tipoN"=>$row['color_tipoN'],
		"id_novedad_tipo"=>$row['id_novedad_tipo'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}





function buscar_id_tipo_cierre(){
	$id_tipo= $_POST['id_tipo'];
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.placa_aoa as placa_aoa,
   aoa_modulo.novedad.aseguradora_aoa as aseguradora_aoa, 
   saoa.aseguradora_nombre as aseguradora_aoa,
   scliente.aseguradora_nombre as aseguradora_cliente,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
   aoa_modulo.novedad.reportado_otro as reportado,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.id_placa as id_placa, 
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.tipo_cierre.nombre_cierre as nombre_cierre, 
   aoa_modulo.tipo_cierre.color_cierre as color_cierre,
   aoa_modulo.tipo_cierre.id_tipo_cierre as tipo_cierre,
   
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoa_modulo.tipoNovedad.color as color_tipoN,
   aoa_modulo.tipoNovedad.idtipoNovedad as id_novedad_tipo,
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	  LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.seguros saoa
   on  aoa_modulo.novedad.aseguradora_aoa = saoa.id 
    LEFT OUTER JOIN aoacol_aoacars.seguros scliente
   on  aoa_modulo.novedad.aseguradora_cliente  = scliente.id 
 where aoa_modulo.novedad.tipo_cierre = $id_tipo
 ORDER by id_novedad DESC ");  	
	
    while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"asegurado"=>$row['asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		"placa_aoa"=>$row['placa_aoa'],
		"aseguradora_aoa"=>$row['aseguradora_aoa'],
		"color_cierre"=>$row['color_cierre'],
		"nombre_cierre"=>$row['nombre_cierre'],
		"aseguradora_cliente"=>$row['aseguradora_cliente'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"fecha_creacion"=>$row['fecha_creacion'],
		"id_placa"=>$row['id_placa'],
		"reportado"=>$row['reportado'],
		"color_tipoN"=>$row['color_tipoN'],
		"id_novedad_tipo"=>$row['id_novedad_tipo'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"apellido_opera"=>$row['apellido_opera']);
	}
		echo json_encode($tabledata);
}



function buscar_id_novedad_cierre(){
	$encargado = $_POST['encargado'];
	$id_tipo_novedad = $_POST['id_tipo_novedad'];
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.placa_aoa as placa_aoa,
   aoa_modulo.novedad.aseguradora_aoa as aseguradora_aoa, 
   saoa.aseguradora_nombre as aseguradora_aoa,
   scliente.aseguradora_nombre as aseguradora_cliente,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
   aoa_modulo.novedad.reportado_otro as reportado,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.id_placa as id_placa, 
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.tipo_cierre.nombre_cierre as nombre_cierre, 
   aoa_modulo.tipo_cierre.color_cierre as color_cierre,
   aoa_modulo.tipo_cierre.id_tipo_cierre as tipo_cierre,
   
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoa_modulo.tipoNovedad.color as color_tipoN,
   aoa_modulo.tipoNovedad.idtipoNovedad as id_novedad_tipo,
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	  LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.seguros saoa
   on  aoa_modulo.novedad.aseguradora_aoa = saoa.id 
    LEFT OUTER JOIN aoacol_aoacars.seguros scliente
   on  aoa_modulo.novedad.aseguradora_cliente  = scliente.id 
    where aoacol_aoacars.operario.id = $encargado and  aoa_modulo.novedad.tipo_cierre = 4 and aoa_modulo.novedad.id_tipo = $id_tipo_novedad
 ORDER by id_novedad DESC ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"asegurado"=>$row['asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		"placa_aoa"=>$row['placa_aoa'],
		"aseguradora_aoa"=>$row['aseguradora_aoa'],
		"color_cierre"=>$row['color_cierre'],
		"nombre_cierre"=>$row['nombre_cierre'],
		"aseguradora_cliente"=>$row['aseguradora_cliente'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"fecha_creacion"=>$row['fecha_creacion'],
		"id_placa"=>$row['id_placa'],
		"reportado"=>$row['reportado'],
		"color_tipoN"=>$row['color_tipoN'],
		"id_novedad_tipo"=>$row['id_novedad_tipo'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}

function buscar_id_novedad_cerrada(){
	$encargado = $_POST['encargado'];
	$id_tipo_novedad = $_POST['id_tipo_novedad'];
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.placa_aoa as placa_aoa,
   aoa_modulo.novedad.aseguradora_aoa as aseguradora_aoa, 
   saoa.aseguradora_nombre as aseguradora_aoa,
   scliente.aseguradora_nombre as aseguradora_cliente,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
   aoa_modulo.novedad.reportado_otro as reportado,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.id_placa as id_placa, 
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.tipo_cierre.nombre_cierre as nombre_cierre, 
   aoa_modulo.tipo_cierre.color_cierre as color_cierre,
   aoa_modulo.tipo_cierre.id_tipo_cierre as tipo_cierre,
   
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoa_modulo.tipoNovedad.color as color_tipoN,
   aoa_modulo.tipoNovedad.idtipoNovedad as id_novedad_tipo,
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	  LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.seguros saoa
   on  aoa_modulo.novedad.aseguradora_aoa = saoa.id 
    LEFT OUTER JOIN aoacol_aoacars.seguros scliente
   on  aoa_modulo.novedad.aseguradora_cliente  = scliente.id 
    where aoacol_aoacars.operario.id = $encargado and  aoa_modulo.novedad.tipo_cierre = 8 and aoa_modulo.novedad.id_tipo = $id_tipo_novedad
 ORDER by id_novedad DESC ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"asegurado"=>$row['asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		"placa_aoa"=>$row['placa_aoa'],
		"aseguradora_aoa"=>$row['aseguradora_aoa'],
		"color_cierre"=>$row['color_cierre'],
		"nombre_cierre"=>$row['nombre_cierre'],
		"aseguradora_cliente"=>$row['aseguradora_cliente'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"fecha_creacion"=>$row['fecha_creacion'],
		"id_placa"=>$row['id_placa'],
		"reportado"=>$row['reportado'],
		"color_tipoN"=>$row['color_tipoN'],
		"id_novedad_tipo"=>$row['id_novedad_tipo'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}

function buscar_id_novedad_requisiciom(){
	$encargado = $_POST['encargado'];
	$id_tipo_novedad = $_POST['id_tipo_novedad'];
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.placa_aoa as placa_aoa,
   aoa_modulo.novedad.aseguradora_aoa as aseguradora_aoa, 
   saoa.aseguradora_nombre as aseguradora_aoa,
   scliente.aseguradora_nombre as aseguradora_cliente,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
   aoa_modulo.novedad.reportado_otro as reportado,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.id_placa as id_placa, 
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.tipo_cierre.nombre_cierre as nombre_cierre, 
   aoa_modulo.tipo_cierre.color_cierre as color_cierre,
   aoa_modulo.tipo_cierre.id_tipo_cierre as tipo_cierre,
   
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoa_modulo.tipoNovedad.color as color_tipoN,
   aoa_modulo.tipoNovedad.idtipoNovedad as id_novedad_tipo,
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	  LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.seguros saoa
   on  aoa_modulo.novedad.aseguradora_aoa = saoa.id 
    LEFT OUTER JOIN aoacol_aoacars.seguros scliente
   on  aoa_modulo.novedad.aseguradora_cliente  = scliente.id 
    where aoacol_aoacars.operario.id = $encargado and  aoa_modulo.novedad.tipo_cierre = 7 and aoa_modulo.novedad.id_tipo = $id_tipo_novedad
 ORDER by id_novedad DESC ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"asegurado"=>$row['asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		"placa_aoa"=>$row['placa_aoa'],
		"aseguradora_aoa"=>$row['aseguradora_aoa'],
		"color_cierre"=>$row['color_cierre'],
		"nombre_cierre"=>$row['nombre_cierre'],
		"aseguradora_cliente"=>$row['aseguradora_cliente'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"fecha_creacion"=>$row['fecha_creacion'],
		"id_placa"=>$row['id_placa'],
		"reportado"=>$row['reportado'],
		"color_tipoN"=>$row['color_tipoN'],
		"id_novedad_tipo"=>$row['id_novedad_tipo'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}





function buscar_id_novedad(){
	$id_tipo_novedad = $_POST['id_tipo_novedad'];
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.placa_aoa as placa_aoa,
   aoa_modulo.novedad.aseguradora_aoa as aseguradora_aoa, 
   saoa.aseguradora_nombre as aseguradora_aoa,
   scliente.aseguradora_nombre as aseguradora_cliente,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
   aoa_modulo.novedad.reportado_otro as reportado,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.id_placa as id_placa, 
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.tipo_cierre.nombre_cierre as nombre_cierre, 
   aoa_modulo.tipo_cierre.color_cierre as color_cierre,
   aoa_modulo.tipo_cierre.id_tipo_cierre as tipo_cierre,
   
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoa_modulo.tipoNovedad.color as color_tipoN,
   aoa_modulo.tipoNovedad.idtipoNovedad as id_novedad_tipo,
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	  LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.seguros saoa
   on  aoa_modulo.novedad.aseguradora_aoa = saoa.id 
    LEFT OUTER JOIN aoacol_aoacars.seguros scliente
   on  aoa_modulo.novedad.aseguradora_cliente  = scliente.id  where aoa_modulo.novedad.id_tipo = $id_tipo_novedad
 ORDER by id_novedad DESC ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"asegurado"=>$row['asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		"placa_aoa"=>$row['placa_aoa'],
		"aseguradora_aoa"=>$row['aseguradora_aoa'],
		"color_cierre"=>$row['color_cierre'],
		"nombre_cierre"=>$row['nombre_cierre'],
		"aseguradora_cliente"=>$row['aseguradora_cliente'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"fecha_creacion"=>$row['fecha_creacion'],
		"id_placa"=>$row['id_placa'],
		"reportado"=>$row['reportado'],
		"color_tipoN"=>$row['color_tipoN'],
		"id_novedad_tipo"=>$row['id_novedad_tipo'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}




function buscar_encargado_mis_novedades(){

	
$encargado = $_POST['encargado'];
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.placa_aoa as placa_aoa,
   aoa_modulo.novedad.aseguradora_aoa as aseguradora_aoa, 
   saoa.aseguradora_nombre as aseguradora_aoa,
   scliente.aseguradora_nombre as aseguradora_cliente,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
   aoa_modulo.novedad.reportado_otro as reportado,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.id_placa as id_placa, 
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoa_modulo.tipoNovedad.color as color_tipoN,
   aoa_modulo.tipoNovedad.idtipoNovedad as id_novedad_tipo,
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.seguros saoa
   on  aoa_modulo.novedad.aseguradora_aoa = saoa.id 
    LEFT OUTER JOIN aoacol_aoacars.seguros scliente
   on  aoa_modulo.novedad.aseguradora_cliente  = scliente.id 
   WHERE  aoa_modulo.novedad.encargado  ='$encargado'  ORDER by id_novedad DESC "   ); 	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"asegurado"=>$row['asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],
		"placa_aoa"=>$row['placa_aoa'],
		"aseguradora_aoa"=>$row['aseguradora_aoa'],
		
		"aseguradora_cliente"=>$row['aseguradora_cliente'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"fecha_creacion"=>$row['fecha_creacion'],
		"id_placa"=>$row['id_placa'],
		"reportado"=>$row['reportado'],
		"color_tipoN"=>$row['color_tipoN'],
		"id_novedad_tipo"=>$row['id_novedad_tipo'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}



function buscar_encargado_mis_novedadesq(){
	

$encargado = $_POST['encargado'];
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.reportado as reportado,
   aoa_modulo.novedad.tele_reporte as tele_reporte,
   aoa_modulo.novedad.email_reporte as email_reporte,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoa_modulo.tipoNovedad.color as color_tipoN, 
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
	WHERE  aoa_modulo.novedad.encargado  ='$encargado'  ORDER by id_novedad DESC ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"color_tipoN"=>$row['color_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}

function centro_costo(){
	
$centro_costo = $_POST['centro_costo'];

$Sins=q("select * from aoacol_aoacars.aseguradora where id ='$centro_costo ' ");  
        	
	while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
		}
		
		
		
}


function ingresar_novedad_pendiente() // recibe la informacion del formulario de busqueda
{ 
  $descripcion=$_POST['descripcion'];
  $fecha_causa=$_POST['fecha_causa'];
  $causa=$_POST['causa'];
  $direccion=$_POST['direccion'];
  $departamento=$_POST['departamento'];
  $ciudad=$_POST['ciudad'];
  $novedad_id=$_POST['novedad_id'];
  
       $qry = q( "INSERT INTO `aoa_modulo`.`novedad_pendiente`
	  (descripcion_pad, 
	  fecha_causa,causa,direccion,departamento,ciudad,
	  novedad_id) 
	  VALUES ('$descripcion','$fecha_causa','$causa','$direccion'
	  ,'$departamento','$ciudad',
	  '$novedad_id')");
	
	
      buscar_novedad_pendiente();
}




function ingresar_detalle() // recibe la informacion del formulario de busqueda
{ 
  $novedad_requisicion=$_POST['novedad_requisicion'];
  $tipo=$_POST['tipo'];
  $clase=$_POST['clase'];
  $tipoItem=$_POST['tipoItem'];
  $observaciones=$_POST['observaciones'];
  $tipo_cobro=$_POST['tipo_cobro'];
  $cantidad = $_POST['cantidad'];
  $centro_costo = $_POST['centro_costo'];
  $centro_operacion=$_POST['centro_operacion'];
  $valor=$_POST['valor'];
  $valor_total=$_POST['valor_total'];
  $id_vehiculo=$_POST['id_vehiculo'];
  $factor=$_POST['factor'];
  
      $qry = q("INSERT INTO `aoa_modulo`.`itenes`
	  (novedad_requisicion,estado,
	  tipo,clase,valor,
	  tipoItem,observaciones,
	  tipo_cobro,cantidad,
	  centro_costo,centro_operacion,
	  valor_total,
	  id_vehiculo,factor) 
	  VALUES ('$novedad_requisicion','8',
	  '$tipo','$clase','$valor','$tipoItem','$observaciones', '$tipo_cobro' 
    ,'$cantidad ', '$centro_costo','$centro_operacion'
	, '$valor_total', '$id_vehiculo'   , '$factor'  )");	
  

	
	
      buscar_detalle();
}

function ingresar_detalle_requisicion() // recibe la informacion del formulario de busqueda
{ 
  $novedad_requisicion=$_POST['novedad_requisicion'];
  $tipo=$_POST['tipo'];
  $clase=$_POST['clase'];
  $tipoItem=$_POST['tipoItem'];
  $observaciones=$_POST['observaciones'];
  $tipo_cobro=$_POST['tipo_cobro'];
  $cantidad = $_POST['cantidad'];
  $centro_costo = $_POST['centro_costo'];
  $centro_operacion=$_POST['centro_operacion'];
  $valor=$_POST['valor'];
  $valor_total=$_POST['valor_total'];
  $id_vehiculo=$_POST['id_vehiculo'];
  $factor=$_POST['factor'];
  
      $qry = q("INSERT INTO `aoacol_administra`.`requisiciond`
	  (requisicion, 
	  tipo,clase,valor,
	  tipo1,observaciones,
	  tipo_cobro,cantidad,
	  centro_costo,centro_operacion,
	  valor_total,
	  id_vehiculo,factor) 
	  VALUES ('$novedad_requisicion',
	  '$tipo','$clase','$valor','$tipoItem','$observaciones', '$tipo_cobro' 
    ,'$cantidad ', '$centro_costo','$centro_operacion'
	, '$valor_total', '$id_vehiculo'   , '$factor'  )");	
  

	
	
     echo $qry;
	 
	 buscar_detalle_requisicion_id();
	 
}


 function buscar_detalle_requisicion_id() // recibe la informacion del formulario de busqueda
{ 

		  $id=q("SELECT MAX(id) as id FROM aoacol_administra.requisiciond ;   ");
		
		while($row=mysql_fetch_object($id)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			$table_data = $row;
			
			
		};
		
		
}
 
 

 




function ingresar_det() // recibe la informacion del formulario de busqueda
{ 
  $novedad_requisicion=$_POST['novedad_requisicion'];
  $tipo=$_POST['tipo'];
  $clase=$_POST['clase'];
  $tipoItem=$_POST['tipoItem'];
  $observaciones=$_POST['observaciones'];
  $tipo_cobro=$_POST['tipo_cobro'];
  $cantidad = $_POST['cantidad'];
  $centro_costo = $_POST['centro_costo'];
  $centro_operacion=$_POST['centro_operacion'];
  $valor=$_POST['valor'];
  $valor_total=$_POST['valor_total'];
   $estado=1;
  
      $qry = qo("INSERT INTO `aoa_modulo`.`itenes`
	  (novedad_requisicion, estado,
	  tipo,clase,valor,
	  tipoItem,observaciones,
	  tipo_cobro,cantidad,
	  centro_costo,centro_operacion,
	  valor_total) 
	  VALUES ('$novedad_requisicion','$estado',
	  '$tipo','$clase','$valor','$tipoItem', '$observaciones', '$tipo_cobro' 
    ,'$cantidad ', '$centro_costo','$centro_operacion'
	, '$valor_total' )");	

	
	
      buscar_detalle();
}
function editarNovedadRequisicion_estado() // recibe la informacion del formulario de busqueda
{ 
  $id_novedad=$_POST['id_novedad'];
  $estado=$_POST['estado'];
 $qry = q("UPDATE `aoa_modulo`.`novedad_requisicion`
 set estado_novedad_requisicion = ' 6 '  where id = '$id_novedad' ");
 
 echo  $qry;

 }
 
 function editarNovedadRequisicion_est() // recibe la informacion del formulario de busqueda
{ 
  $id_novedad=$_POST['id_novedad'];
  $estado=$_POST['estado'];


      $qry = q("UPDATE `aoa_modulo`.`novedad_requisicion`
 set estado_novedad_requisicion = ' $estado '  where id = '$id_novedad' ");
 
 echo  $qry;

 }
 
 
   function editar_cotizacion_req_det() // recibe la informacion del formulario de busqueda
{ 
  $estado=$_POST['estado'];
  $id_novedad=$_POST['id_novedad'];


      $qry = q("UPDATE `aoa_modulo`.`itenes`
 set estado = '$estado'  where id = '$id_novedad' ");
 
 echo $qry;
 
 }
 
 function editar_cotizacion_req_est() // recibe la informacion del formulario de busqueda
{ 
  $estado=$_POST['estado'];
  $id_novedad=$_POST['id_novedad'];


      $qry = q("UPDATE `aoa_modulo`.`novedad_requisicion`
 set estado_novedad_requisicion = ' $estado '  where id = '$id_novedad' ");
 
 echo $qry;
 
 }
  function editar_cotizacion_req() // recibe la informacion del formulario de busqueda
{ 
  $estado=$_POST['estado'];
  $id_novedad=$_POST['id_novedad'];


      $qry = q("UPDATE `aoa_modulo`.`novedad_requisicion`
 set estado_novedad_requisicion = '$estado'  where id = '$id_novedad' ");
 
 echo $qry;
 
 }
  function editar_novedad() // recibe la informacion del formulario de busqueda
{ 
  $estado=$_POST['estado'];
 $id_novedad=$_POST['id_novedad'];


      $qry = q("UPDATE `aoa_modulo`.`novedad`
 set tipo_cierre = '$estado'  where id_novedad = '$id_novedad' ");
 
 echo $qry;
 
 }
 
   function editar_novedad_req() // recibe la informacion del formulario de busqueda
{ 
  $estado=$_POST['estado'];
 $id_novedad=$_POST['id_novedad'];


      $qry = q("UPDATE `aoa_modulo`.`novedad`
 set tipo_cierre = '$estado'  where id_novedad = '$id_novedad' ");
 
 echo $qry;
 }
    function editar_novedad_req_estado() // recibe la informacion del formulario de busqueda
{ 
  $estado=$_POST['estado'];
  $requisicion=$_POST['requisicion'];
 $id_novedad=$_POST['id_novedad'];


       $qry = q("UPDATE `aoa_modulo`.`novedad_requisicion`
 set estado_novedad_requisicion = '$estado' ,requisicion = '$requisicion'    where id = '$id_novedad' ");
 
 echo $qry;
 }
 
 
 function editar_cotizacion() // recibe la informacion del formulario de busqueda
{ 
  $estado=$_POST['estado'];
$id_novedad=$_POST['id_novedad'];


      $qry = q("UPDATE `aoa_modulo`.`itenes`
 set estado = '$estado'  where id = '$id_novedad' ");
 
 echo  "UPDATE `aoa_modulo`.`itenes`
 set estado = '$estado'  where id = '$id_novedad' ";

 } 
  function editar_archivo() // recibe la informacion del formulario de busqueda
{ 
  $estado=$_POST['estado'];
$id_novedad = $_POST['id_novedad'];


      $qry = q("UPDATE `aoa_modulo`.`archivo`
 set estado = '$estado'  where id_archivo = '$id_novedad' ");
 
 echo  "UPDATE `aoa_modulo`.`archivo`
 set estado = '$estado'  where id_archivo = '$id_novedad' ";

 } 
 
 function editar_nov_re() // recibe la informacion del formulario de busqueda
{ 
  $estado=$_POST['estado'];
$id_novedad=$_POST['id_novedad'];


      $qry = q("UPDATE `aoa_modulo`.`novedad`
 set tipo_cierre = '$estado'  where id_novedad = '$id_novedad' ");
 
 echo  "UPDATE `aoa_modulo`.`itenes`
 set estado = '$estado'  where id_novedad = '$id_novedad' ";

 }
 
 
 function editar_detalle_estado() // recibe la informacion del formulario de busqueda
{ 
  $id_novedad=$_POST['id_novedad'];



      $qry = q("UPDATE `aoa_modulo`.`itenes`
 set estado = ' 2 '  where id = '$id_novedad' ");
 
 echo  $qry;

 }
 
 
  function crear_requisicion() // recibe la informacion del formulario de busqueda
{ 
  $id_novedad=$_POST['id_novedad'];
  
    $fecha  =$_POST['fecha']; 
    $solicitado_por  =$_POST['solicitado_por'];  
    $placa  =$_POST['placa']; 
    $ciudad  =$_POST['ciudad'];
    $estado  =$_POST['estado'];   
	$perfil  =$_POST['perfil'];   
	$proveedor  =$_POST['proveedor']; 
	
	$Nid=qo("INSERT INTO `aoacol_administra`.`requisicion` (fecha,solicitado_por,placa,ciudad,estado,perfil,proveedor) values ('$fecha','$solicitado_por','$placa','$ciudad','0','$perfil','$proveedor');");
 
   buscar_requisicion();


 }
 
 
 
 function buscar_requisicion() // recibe la informacion del formulario de busqueda
{ 

		  $id=q("SELECT MAX(id) as id FROM aoacol_administra.requisicion ;   ");
		
		while($row=mysql_fetch_object($id)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			$table_data = $row;
			
			
		};
		
		buscar_requisicion_id($table_data->id);
}
 
 
 
 
 function buscar_requisicion_id($id) // recibe la informacion del formulario de busqueda
{ 
         
		$idnovedad = $_POST['idnovedad']; 

		$user = q(" select  *  from  aoacol_administra.requisicion  where id = $id  ");  	
	
			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id"=>$row['id'],
		"placa"=>$row['placa'],
		"proveedor"=>$row['proveedor'],
		"perfil"=>$row['perfil'],
		"estado"=>$row['estado'],
		"solicitado_por"=>$row['solicitado_por'],
		"ciudad"=>$row['ciudad'],);
		

		
	
	}
	
		echo json_encode($tabledata);
}

  function editar_detalle_est() // recibe la informacion del formulario de busqueda
{ 

  $id_novedad = $_POST['id_novedad'];
  
  $estado  = $_POST['estado'];


      $qry = q("UPDATE `aoa_modulo`.`itenes`
 set estado = ' $estado '  where id = '$id_novedad' ");
 
 echo  $qry;

 }
 
 
 
  function editar_detalle_estado_requicision() // recibe la informacion del formulario de busqueda
{ 
  $id_novedad=$_POST['id_novedad'];



      $qry = q("UPDATE `aoa_modulo`.`itenes`
 set estado = ' 6 '  where id = '$id_novedad' ");
 
 echo  $qry;

 }
function editarNovedadR_estado_cotizar() // recibe la informacion del formulario de busqueda
{ 
  $id_novedad=$_POST['id_novedad'];
  $estado_novedad_requisicion=$_POST['estado_novedad_requisicion'];


      $qry = qo("UPDATE `aoa_modulo`.`novedad_requisicion`
 set estado_novedad_requisicion = ' 2 '  where id = '$id_novedad' ");
echo  $qry;
 }
 
  function editar_detalle_req() // recibe la informacion del formulario de busqueda
{ 
  $id_novedad=$_POST['id_novedad'];
$estado=$_POST['estado'];


      $qry = q("UPDATE `aoa_modulo`.`itenes`
 set estado = ' $estado'  where id = '$id_novedad' ");
 
 echo  $qry;

 }

 function editarNovedadDetalle() // recibe la informacion del formulario de busqueda
{ 
  $id=$_POST['id'];
  $tipo=$_POST['tipoBien'];
  $clase=$_POST['clase'];
  $tipoItem=$_POST['tipoItem'];
  $valor=$_POST['valor'];
  $cantidad=$_POST['cantidad'];
  $valor_total=$_POST['valor_total'];
  $observaciones=$_POST['observaciones'];

      $qry = qo("UPDATE `aoa_modulo`.`itenes` 
 set tipo = ' $tipo',
  clase = '$clase',
  tipoItem = '$tipoItem',
  valor = '$valor' ,
  cantidad = '$cantidad' ,
  observaciones = '$observaciones'  ,
  valor_total = '$valor_total' 
  
  where id = '$id' ");
  

}
 
 
 
 
 
function editarNovedadRequisicion() // recibe la informacion del formulario de busqueda
{ 
  $id=$_POST['id'];
  $novedad=$_POST['novedad'];
  $proveedor=$_POST['proveedor'];
  $descripcion=$_POST['descripcion'];
  $solicitante=$_POST['solicitante'];
  $actividad_solitante=$_POST['actividad_solitante'];
  $actividad_provedor=$_POST['actividad_provedor'];

      $qry = q("UPDATE `aoa_modulo`.`novedad_requisicion` 
 set proveedor = ' $proveedor',
  descripcion = '$descripcion',
  actividad_solitante = '$actividad_solitante',
  actividad_provedor = '$actividad_provedor'  where id = '$id' ");
}

function ingresaNovedadCall() // recibe la informacion del formulario de busqueda
{ 

  $id_tipo=$_POST['id_tipo'];
  $id_siniestro=$_POST['id_siniestro'];
    $id_placa=$_POST['id_placa'];
  $novedad=$_POST['novedad'];
  $solicitante=$_POST['solicitante'];
  $ciudad=$_POST['ciudad'];
  $fecha_creacion=$_POST['fecha_creacion'];
  $encargado=$_POST['encargado'];
  $tipo_cierre=$_POST['tipo_cierre'];
  $cierre=$_POST['cierre'];
  $reportado=$_POST['reportado'];
  $tele_reporte=$_POST['tele_reporte'];
  $ciudad_reporte=$_POST['ciudad_reporte'];
  $email_reporte=$_POST['email_reporte'];
  $reportado_otro=$_POST['reportado_otro'];
  $depar_reporte_otro=$_POST['depar_reporte_otro'];
  $tele_reporte_otro=$_POST['tele_reporte_otro'];
  $ciudad_reporte_otro=$_POST['ciudad_reporte_otro'];
  $email_reporte_otro=$_POST['email_reporte_otro'];
  
	$placa_aoa =$_POST['placa_aoa'];
  $aseguradora_aoa=$_POST['aseguradora_aoa'];
  $aseguradora_cliente=$_POST['aseguradora_cliente'];
  

  
  
        $qry = q("INSERT INTO `aoa_modulo`.`novedad`
	  (id_tipo, novedad,placa_aoa,aseguradora_cliente,aseguradora_aoa,
	  
	  fecha_creacion,ciudad,id_siniestro,id_placa,solicitante,encargado,tipo_cierre,
	  cierre,reportado,tele_reporte,ciudad_reporte,email_reporte,email_reporte_otro,ciudad_reporte_otro,depar_reporte_otro,tele_reporte_otro,reportado_otro) 
	  VALUES ('$id_tipo', '$novedad',   '$placa_aoa',  '$aseguradora_cliente',  '$aseguradora_aoa',           '$fecha_creacion','$ciudad', '$id_siniestro' ,'$id_placa',
	  '$solicitante' ,'$encargado'  ,'$tipo_cierre' ,'$cierre', ' $reportado' ,'$tele_reporte' ,'$ciudad_reporte' ,'$email_reporte' ,
       '$email_reporte_otro' ,'$ciudad_reporte_otro' ,'$depar_reporte_otro','$tele_reporte_otro' ,'$reportado_otro'	  )");
      buscar_novedad();
	 
}


function ingresar_evaluacion() // recibe la informacion del formulario de busqueda
{ 	
  $id_novedad=$_POST['id_novedad'];	
  $km_salida=$_POST['km_salida'];	
  $ventilacion_Aire = $_POST['ventilacion_Aire'];	
  $funcionamiento_limpiabrisas = $_POST['funcionamiento_limpiabrisas'];
  $km_ingreso=$_POST['km_ingreso'];
  $fecha_ingreso=$_POST['fecha_ingreso'];
  $luces_altas=$_POST['luces_altas'];
  $fecha_salida=$_POST['fecha_salida'];
  $funcionamiento_pito=$_POST['funcionamiento_pito'];
  $estado_alarma=$_POST['estado_alarma'];
  $direccionales=$_POST['direccionales'];
  $estado_stops=$_POST['estado_stops'];
  $estado_cinturon=$_POST['estado_cinturon'];
  $fugas_motor=$_POST['fugas_motor'];
  $fugas_frenos=$_POST['fugas_frenos'];
   $fugas_hidraulico=$_POST['fugas_hidraulico'];
  $estado_frenos=$_POST['estado_frenos'];
   $estado_llantas=$_POST['estado_llantas'];
  $firma=$_POST['firma'];
    $recomendaciones=$_POST['recomendaciones'];	

  $estado_vehiculo=$_POST['estado_vehiculo'];
        $qry = q("INSERT INTO `aoa_modulo`.`evaluacion`
	  (funcionamiento_limpiabrisas,recomendaciones,fugas_frenos,estado_llantas,ventilacion_Aire,luces_altas,km_salida,id_novedad, km_ingreso,fecha_ingreso,fecha_salida,funcionamiento_pito,estado_alarma,
	  direccionales,estado_stops,estado_cinturon,fugas_motor,estado_frenos,fugas_hidraulico,firma,estado_vehiculo) 
	  VALUES ('$funcionamiento_limpiabrisas','$recomendaciones','$fugas_frenos','$estado_llantas','$ventilacion_Aire','$luces_altas','$km_salida','$id_novedad', '$km_ingreso','$fecha_ingreso','$fecha_salida', 
	  '$funcionamiento_pito' ,'$estado_alarma' ,'$direccionales', 
	  ' $estado_stops' ,'$estado_cinturon' ,'$fugas_motor' ,
	  '$fugas_hidraulico','$estado_frenos','$firma','$estado_vehiculo'  )");
      buscar_evaluacion();
}


function buscar_tipo_evaluacion() // recibe la informacion del formulario de busqueda
{ 

		 $result = q("SELECT * FROM `aoa_modulo`.`tipo_evaluacion` LIMIT 1000;");
     	
		while($row = mysql_fetch_array($result)){
		$tabledata[]= array("id"=>$row['id_tipo_evaluacion'],"nombre"=>$row['nombre']);
	}
		echo json_encode($tabledata);

}



function buscar_novedad_pendienten_idn() // recibe la informacion del formulario de busqueda
{ 
$id_novedad = $_POST['id_novedad'];
$result = q("select * from aoa_modulo.novedad_pendiente where aoa_modulo.novedad_pendiente.novedad_id = $id_novedad ");     	
		while($row = mysql_fetch_array($result)){
		$tabledata[]= 
		array(
		"id_novedad_pendiente"=>$row['id_novedad_pendiente'],
		"descripcion_pad"=>$row['descripcion_pad'],
		"causa"=>$row['causa'],
		"departamento"=>$row['departamento'],
		"ciudad"=>$row['ciudad'],
		"direccion"=>$row['direccion'],
		"fecha_causa"=>$row['fecha_causa'],
		"archivo"=>$row['archivo']
		
		
		);
	
	}
	if($tabledata == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($tabledata);
		
		}
		

}

function editarNovedad_operador() // recibe la informacion del formulario de busqueda
{ 
  $id_novedad=$_POST['id_novedad'];
  $id_tipo=$_POST['id_tipo'];
  $id_siniestro=$_POST['id_siniestro'];
  $novedad=$_POST['novedad'];
  $solicitante=$_POST['solicitante'];
  $ciudad=$_POST['ciudad'];
  $fecha_creacion=$_POST['fecha_creacion'];
  $encargado=$_POST['encargado'];
  $tipo_cierre=$_POST['tipo_cierre'];
  $cierre=$_POST['cierre'];
  $reportado=$_POST['reportado'];
  $tele_reporte=$_POST['tele_reporte'];
  $ciudad_reporte=$_POST['ciudad_reporte'];
  $email_reporte=$_POST['email_reporte'];
  $acti_caarga=$_POST['acti_caarga'];
  $cierre_oper=$_POST['cierre_oper'];
  
  
  
   $qry = q("UPDATE `aoa_modulo`.`novedad` 
    set id_tipo = ' $id_tipo',
    novedad = '$novedad',
    fecha_creacion = '$fecha_creacion',
    ciudad = '$ciudad',
    id_siniestro = '$id_siniestro',
   id_placa = '$id_placa',
   encargado = '$encargado',
    tipo_cierre = '$tipo_cierre',
   cierre = '$cierre',
   reportado = '$reportado',
   tele_reporte = '$tele_reporte',
   ciudad_reporte = '$ciudad_reporte',
   email_reporte = '$email_reporte',
   acti_caarga = '$acti_caarga',
   cierre_oper = '$cierre_oper'  where id_novedad = '$id_novedad' ");
   
   echo  $qry;
  
      
}




function ingresaNovedad() // recibe la informacion del formulario de busqueda
{ 
  $id_tipo=$_POST['id_tipo'];
  $id_placa=$_POST['id_placa'];
  $novedad=$_POST['novedad'];
  $solicitante=$_POST['solicitante'];
  $encargado=$_POST['encargado'];
  $ciudad=$_POST['ciudad'];
   $reportado_otro=$_POST['reportado_otro'];
  $fecha_creacion=$_POST['fecha_creacion'];
    $descripcion = $_POST['descripcion'];
  
  
  

      $qry = q("INSERT INTO `aoa_modulo`.`novedad`
	  (id_tipo, novedad,fecha_creacion,ciudad,id_placa,solicitante,encargado,reportado_otro,cierre_oper) 
	  VALUES ('$id_tipo', '$novedad','$fecha_creacion','$ciudad', '$id_placa' , '$solicitante' , '$encargado' , '$reportado_otro', '$descripcion'  )");
      buscar_novedad();
}

function ingresaNovedadRequisicion() // recibe la informacion del formulario de busqueda
{ 

  $novedad=$_POST['novedad'];
  $proveedor=$_POST['proveedor'];
  $descripcion=$_POST['descripcion'];
  $solicitante=$_POST['solicitante'];
  $actividad_solitante=$_POST['actividad_solitante'];
  $actividad_provedor=$_POST['actividad_provedor'];

  
      $qry = q("INSERT INTO `aoa_modulo`.`novedad_requisicion`
	  (novedad, estado_novedad_requisicion, proveedor,descripcion,actividad_solitante,actividad_provedor) 
	  VALUES ('$novedad','8', '$proveedor','$descripcion','$actividad_solitante', '$actividad_provedor' )");
      buscar_NovedadRequisicion();
}



function eliminardetalle() // recibe la informacion del formulario de busqueda
{ 
  $id = $_POST['id'];

      $qry = qo("DELETE FROM `aoa_modulo`.`itenes`  WHERE id = ' $id' ");
	  
}


function eliminarNovedadRequisicion() // recibe la informacion del formulario de busqueda
{ 
  $id = $_POST['id'];

      $qry = q("DELETE FROM `aoa_modulo`.`novedad_requisicion`  WHERE id = ' $id' ");
	  
}

function buscar_cotizacion() // recibe la informacion del formulario de busqueda
{ 

		  $id=q("SELECT MAX(id) as id FROM `aoa_modulo`.`novedad_requisicion` ;   ");
		
		while($row=mysql_fetch_object($id)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			$table_a =  array($row);
		};
		echo json_encode($table_a);
} 

function buscar_detalle() // recibe la informacion del formulario de busqueda
{ 

		  $id=q("SELECT MAX(id) as id FROM `aoa_modulo`.`itenes` ;   ");
		
		while($row=mysql_fetch_object($id)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			$table_data =  array($row);
		};
		echo json_encode($table_data);
}

function buscar_evaluacion_id_novedad() // recibe la informacion del formulario de busqueda
{ 
          $id_novedad = $_POST['id_novedad'];
		  $id=q("SELECT * FROM aoa_modulo.evaluacion where id_novedad =   $id_novedad  ");
		
		while($row=mysql_fetch_object($id)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			$table_data =  array($row);
		};
		
		
		if($table_data == null ){
		 
		 echo json_encode(0);
		
		}else{
			
		echo json_encode($table_data);
		
		}
}


function buscar_evaluacion_id_evaluacion() // recibe la informacion del formulario de busqueda
{ 
          $id_evaluacion = $_POST['id_evaluacion'];
		  $id=q("SELECT * FROM aoa_modulo.evaluacion where id_evaluacion =   $id_evaluacion  ");
		
		while($row=mysql_fetch_object($id)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			$table_data =  array($row);
		};
		echo json_encode($table_data);
}

function buscar_novedad_pendiente() // recibe la informacion del formulario de busqueda
{ 


		  $id=q("SELECT MAX(id_novedad_pendiente) as buscar_novedad_pendiente FROM `aoa_modulo`.`novedad_pendiente` ;   ");
		
		while($row=mysql_fetch_object($id)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			$table_data =  $row;
		};
		echo json_encode($table_data);
}

function buscar_novedad() // recibe la informacion del formulario de busqueda
{ 

		  $id=q("SELECT MAX(id_novedad) as id FROM `aoa_modulo`.`novedad` ;   ");
		
		while($row=mysql_fetch_object($id)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			$table_data =  array($row);
		};
		echo json_encode($table_data);
}





function buscar_novedad_id_table() // recibe la informacion del formulario de busqueda
{ 
         $id_novedad = $_POST['id_novedad']; 
		  $id=q("select * from aoa_modulo.novedad  LEFT OUTER JOIN aoacol_aoacars.seguros scliente
   on  aoa_modulo.novedad.aseguradora_cliente  = scliente.id   where novedad.id_novedad  = $id_novedad ;   ");
		
		while($row=mysql_fetch_object($id)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			$table_data =  array($row);
		};
		echo json_encode($table_data);
}


function buscar_novedad_aoa_id_table() // recibe la informacion del formulario de busqueda
{ 


         $id_novedad = $_POST['id_novedad']; 
		 
		  $id=q("select v_m.nombre as nombre_marca, v.modelo,  v.tipo_caja,v.placa,s.n_poliza,s.id as id_aseguradora,s.aseguradora_nombre as s_nombre,s.linea_asistencia,l.nombre nlinea
							 fROM aoa_modulo.novedad n
                     inner join aoacol_aoacars.vehiculo v on v.placa = n.placa_aoa
							inner join aoacol_aoacars.seguros s on v.n_poliza = s.id
							inner join aoacol_aoacars.linea_vehiculo l on v.linea = l.id  
							inner join aoacol_aoacars.marca_vehiculo v_m on l.marca  = v_m.id
							where n.id_novedad  =  $id_novedad ;");
		
		while($row=mysql_fetch_object($id))  
			{	
			$table_data =  array($row);
		};
		$table_mm = json_encode($table_data);
		
		if(!$id){
			
			
			
		  $id=q("select v_m.nombre as nombre_marca,
   v.modelo,
  v.tipo_caja,
  v.placa,
  l.nombre nlinea
							 fROM aoa_modulo.novedad n
                     inner join aoacol_aoacars.vehiculo v on v.placa = n.placa_aoa
							
							inner join aoacol_aoacars.linea_vehiculo l on v.linea = l.id  
							inner join aoacol_aoacars.marca_vehiculo v_m ON l.marca  = v_m.id
							where n.id_novedad  =  $id_novedad ;");
		
		while($row=mysql_fetch_object($id))  
			{	
			$table_data =  array($row);
		};
		
		
		
		echo json_encode($table_data);
			
		}else{
		
		echo json_encode($table_data);
		
		}
}


function buscar_evaluacion() // recibe la informacion del formulario de busqueda
{ 

		  $id=q("SELECT MAX(id_evaluacion) as id FROM aoa_modulo.evaluacion ;   ");
		
		while($row=mysql_fetch_object($id)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			$table_data =  array($row);
		};
		echo json_encode($table_data);
}

function Buscar_factura() // recibe la informacion del formulario de busqueda
{ 
         
		$factura = $_POST['factura']; 

			$Sins=q("select * from aoacol_aoacars.factura 	WHERE  aoacol_aoacars.factura.consecutivo ='$factura' ");
		while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
		}

	
}
function novedad_tipo() // recibe la informacion del formulario de busqueda
{ 
         
		$tipo = $_POST['tipo']; 

			$Sins=q("select * from aoa_modulo.tipoNovedad  WHERE  aoa_modulo.tipoNovedad.idtipoNovedad ='$tipo' ");
		while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
		}

}

function buscar_idnovedad() // recibe la informacion del formulario de busqueda
{ 
         
		$idnovedad = $_POST['idnovedad']; 

		$user = q(" select  aoa_modulo.novedad.id_novedad as id_novedad,
		es_s.nombre as nombre_es_s,
		es_s.color_co as color_co_es_s,
c_s.nombre as ciudad_siniestro,c_s.departamento as departamento_siniestro,
      vehiculo.modelo  as modelo_vehiculo ,
					vehiculo.placa  as placa ,
					linea_vehiculo.nombre  as linea_vehiculo,
					marca_vehiculo.nombre  as marca_vehiculo,
   aoa_modulo.novedad.reportado as asegurado,
   s.n_poliza,s.id as id_aseguradora,s.aseguradora_nombre,s.linea_asistencia,
   aoa_modulo.novedad.depar_reporte_otro as depar_reporte_otro,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
    aoa_modulo.novedad.acti_caarga as acti_caarga,
     aoa_modulo.novedad.cierre_oper as cierre_oper,
      aoa_modulo.novedad.solicitante as solicitante,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
     aoa_modulo.novedad.encargado as encargado, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_asegurado,
   aoa_modulo.novedad.aseguradora_cliente as aseguradora_cliente, 
    aoa_modulo.novedad.id_placa as placa_cliente, 
     aoa_modulo.novedad.placa_aoa as placa_aoa, 
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad,
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
     aoa_modulo.novedad.ciudad_reporte_otro as ciudad_reporte, 
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.reportado_otro as reportado, 
   aoa_modulo.novedad.cierre as cierre, 
    aoa_modulo.tipo_cierre.nombre_cierre as estado_cierre, 
     aoa_modulo.tipo_cierre.color_cierre as estado_color, 
 aoacol_aoacars.siniestro.clase as clase_vehiculo,
 aoacol_aoacars.siniestro.marca as marca_vehiculo_cliente,
 
 aoacol_aoacars.siniestro.numero as servicio_numero,
 aoacol_aoacars.siniestro.servicio as servicio_cliente,

 aoacol_aoacars.siniestro.linea as linea_vehiculo_cliente,
 aoacol_aoacars.siniestro.tipo as tipo_vehiculo_cliente,
  aoacol_aoacars.siniestro.modelo as modelo_vehiculo_cliente,
 aoacol_aoacars.siniestro.id as id_siniestro,
   aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
    aoa_modulo.tipoNovedad.color as color_tipoN, 
  aoa_modulo.tipoNovedad.idtipoNovedad as id_nombre_tipoN, 
   aoacol_aoacars.operario.nombre as nombre_opera,
      aoacol_aoacars.operario.id as id_opera,
      a_c.nombre as nombre_aseguradora_cliente,
      
     a_a.nombre as nombre_aseguradora_aoa,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	 LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.siniestro on novedad.id_siniestro = siniestro.numero 
   
   	INNER JOIN aoacol_aoacars.ciudad c_s ON aoacol_aoacars.siniestro.ciudad_siniestro = c_s.codigo
	
INNER JOIN aoacol_aoacars.estado_siniestro  es_s ON aoacol_aoacars.siniestro.estado = es_s.id
	
   	LEFT OUTER JOIN aoacol_aoacars.vehiculo
		 on aoa_modulo.novedad.placa_aoa  = vehiculo.placa 
	  inner join aoacol_aoacars.seguros s on aoacol_aoacars.vehiculo.n_poliza = s.id
	    inner join aoacol_aoacars.aseguradora a_c on aoa_modulo.novedad.aseguradora_cliente = a_c.id
					LEFT OUTER JOIN aoacol_aoacars.linea_vehiculo ON  vehiculo.linea  = linea_vehiculo.id 
			inner join aoacol_aoacars.aseguradora a_a on aoacol_aoacars.vehiculo.flota = a_a.id 		
					LEFT OUTER JOIN aoacol_aoacars.marca_vehiculo ON  linea_vehiculo.marca  = marca_vehiculo.id 
		WHERE  aoa_modulo.novedad.id_novedad ='$idnovedad'  ");  	
	
			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"servicio_numero"=>$row['servicio_numero'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
		"kmIngreso"=>$row['kmIngreso'],
		"fechaIngreso"=>$row['fechaIngreso'],
		"clase_vehiculo"=>$row['clase_vehiculo'],
		"fechaSalida"=>$row['fechaSalida'],
		
		"nombre_aseguradora_cliente"=>$row['nombre_aseguradora_cliente'],
		"nombre_aseguradora_aoa"=>$row['nombre_aseguradora_aoa'],
		"acti_caarga"=>$row['acti_caarga'],
		"cierre_oper"=>$row['cierre_oper'],
		"linea_vehiculo_cliente"=>$row['linea_vehiculo_cliente'],
			
			
				"marca_vehiculo_cliente"=>$row['marca_vehiculo_cliente'],"modelo_vehiculo_cliente"=>$row['modelo_vehiculo_cliente'],"tipo_vehiculo_cliente"=>$row['tipo_vehiculo_cliente'],
		"ciudad_siniestro"=>$row['ciudad_siniestro'],
		"departamento_siniestro"=>$row['departamento_siniestro'],		
				
		"linea_vehiculo"=>$row['linea_vehiculo'],
		"marca_vehiculo"=>$row['marca_vehiculo'],
		"modelo_vehiculo"=>$row['modelo_vehiculo'],
		"color_tipoN"=>$row['color_tipoN'],
		"color_co_es_s"=>$row['color_co_es_s'],
		"nombre_es_s"=>$row['nombre_es_s'],
		"estado_cierre"=>$row['estado_cierre'],
		"estado_color"=>$row['estado_color'],
		"id_siniestro"=>$row['id_siniestro'],
		"asegurado"=>$row['asegurado'],
		"email_asegurado"=>$row['email_asegurado'],
		"ciudad_asegurado"=>$row['ciudad_asegurado'],
		"tele_asegurado"=>$row['tele_asegurado'],"servicio_cliente"=>$row['servicio_cliente'],
		"placa"=>$row['placa'],
		"kmFinal"=>$row['kmFinal'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_siniestro"=>$row['id_siniestro'],
		"fecha_creacion"=>$row['fecha_creacion'],
		"encargado"=>$row['encargado'],
		"nombre_tipoN"=>$row['nombre_tipoN'],
		"id_nombre_tipoN"=>$row['id_nombre_tipoN'],
		"cierre"=>$row['cierre'],
		"tele_reporte"=>$row['tele_reporte'],
        "tipo_cierre"=>$row['tipo_cierre'],
		"reportado"=>$row['reportado'],
		"nombre_opera"=>$row['nombre_opera'],
		"depar_reporte_otro"=>$row['depar_reporte_otro'],
		"apellido_opera"=>$row['apellido_opera']);
	}
	
		echo json_encode($tabledata);
}



function tipo_servicio(){
       
	    $type = $_POST['tipoServicio']; 
		$sql = "SELECT provee_produc_serv.id,concat(provee_produc_serv.nombre , ' = ' ,sistema.nombre, ' = ', unidad_de_medida.nombre)  as nombre
																	FROM aoacol_administra.provee_produc_serv 
																	INNER JOIN aoacol_administra.sistema ON provee_produc_serv.sistema = sistema.id  
																	INNER JOIN aoacol_administra.unidad_de_medida ON provee_produc_serv.unidad_de_medida = unidad_de_medida.id
																	where provee_produc_serv.activacion = 1  and provee_produc_serv.uso in (2,3) and tipo = $type  
																	order by provee_produc_serv.nombre";
		$result = q($sql);
	
	$rows = array();
	
	while($row = mysql_fetch_object($result))
	{
		$row->nombre = utf8_encode($row->nombre);
		array_push($rows, $row);
	}
	
	echo json_encode($rows);
}


function centros_operacion() // recibe la informacion del formulario de busqueda
{ 

		 $result = q("select id,nombre,centro_operacion from aoacol_aoacars.oficina");
		
        	
		while($row = mysql_fetch_array($result)){
		$tabledata[]= array("id"=>$row['id'],"nombre"=>$row['nombre']);
	}
		echo json_encode($tabledata);
			
	
}

function buscar_cotizacion_novedad() // recibe la informacion del formulario de busqueda
{ 
         
		$novedad = $_POST['novedad']; 

			$Sins=q("select 	aoa_modulo.novedad.id_novedad as id_n, aoa_modulo.novedad.reportado_otro,
			 aoa_modulo.novedad.solicitante, aoacol_aoacars.operario.nombre ,aoacol_aoacars.operario.apellido ,
			 aoa_modulo.novedad_requisicion.actividad_provedor,aoa_modulo.novedad_requisicion.proveedor,
			 aoa_modulo.novedad_requisicion.estado_novedad_requisicion,
			 aoa_modulo.novedad_requisicion.id
			  
				from   aoa_modulo.novedad_requisicion 
			 LEFT OUTER JOIN aoa_modulo.novedad
				on novedad_requisicion.novedad = novedad.id_novedad
			LEFT OUTER JOIN aoacol_aoacars.operario
			   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id  
			 where   aoa_modulo.novedad_requisicion.novedad  ='$novedad' ");
			while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("id"=>$row['id'],
		"id_n"=>$row['id_n'],
		"novedad"=>$row['novedad'],
		"requision"=>$row['requision'],
		"reportado"=>$row['reportado_otro'],
		"solicitante"=>$row['solicitante'],
		"proveedor"=>$row['proveedor'],
		"actividad_provedor"=>$row['actividad_provedor'],
		"encargado"=>$row['nombre'].$row['apellido']);
	}
	
	
	
		echo json_encode($tabledata);

	
}  
function buscar_cotizacion_nov() // recibe la informacion del formulario de busqueda
{ 
         
		$proveedor = $_POST['proveedor']; 

			$Sins=q("select novedad_requisicion.id as id_nov_cot ,
				 novedad_requisicion.actividad_provedor as 
				actividad_provedor  ,
                 novedad_requisicion.estado_novedad_requisicion as 
				estado_novedad_requisicion  ,
				novedad.novedad as novedad ,
				 novedad.fecha_creacion as novedad_fecha_creacion ,
				 novedad.km as km_vehiculo ,
				  novedad.ciudad  as ciudad_fecha_nov ,
				  tipoNovedad.nombre  as tipo_novedad,
				  tipoNovedad.color  as color_novedad,
					vehiculo.modelo  as modelo_vehiculo ,
					vehiculo.placa  as placa ,
					linea_vehiculo.nombre  as linea_vehiculo,
					marca_vehiculo.nombre  as marca_vehiculo
					
				from   aoa_modulo.novedad_requisicion 
					LEFT OUTER JOIN aoa_modulo.novedad ON novedad_requisicion.novedad = novedad.id_novedad  
					LEFT OUTER JOIN aoa_modulo.tipoNovedad on novedad.id_tipo = tipoNovedad.idtipoNovedad 
					LEFT OUTER JOIN aoacol_aoacars.vehiculo ON  novedad.placa_aoa  = vehiculo.placa 
					LEFT OUTER JOIN aoacol_aoacars.linea_vehiculo ON  vehiculo.linea  = linea_vehiculo.id 
					LEFT OUTER JOIN aoacol_aoacars.marca_vehiculo ON  linea_vehiculo.marca  = marca_vehiculo.id 
				 where proveedor   = $proveedor    ORDER by id_nov_cot DESC "    );
				 
				 
				 
					while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array(
		
		    "id_nov_cot"=>$row['id_nov_cot'],"novedad"=>$row['novedad'] ,"estado_novedad_requisicion"=>$row['estado_novedad_requisicion'] ,
			"actividad_provedor"=>$row['actividad_provedor'],"tipo_novedad"=>$row['tipo_novedad'] ,
			"ciudad_fecha_nov"=>$row['novedad_fecha_creacion'],"novedad_fecha_creacion"=>$row['ciudad_fecha_nov'],"modelo_vehiculo"=>$row['modelo_vehiculo'] ,
			"linea_vehiculo"=>$row['linea_vehiculo'],"marca_vehiculo"=>$row['marca_vehiculo'] ,
			"placa"=>$row['placa'] , "color_novedad"=>$row['color_novedad'] 
		
		);
	}
		echo json_encode($tabledata);

		
		
	
}  

function buscar_novedad_cotizacion_req() // recibe la informacion del formulario de busqueda
{ 

         
		$id_novedad = $_POST['id_novedad']; 

			$Sins=q("select novedad_requisicion.id as id_nov_cot ,novedad.id_novedad as id_novedad,
				 novedad_requisicion.actividad_provedor as 
				actividad_provedor  ,
				novedad_requisicion.proveedor as 
				proveedor  ,novedad.ciudad_reporte as 
				ciudad  ,
				
                 novedad_requisicion.estado_novedad_requisicion as 
				estado_novedad_requisicion  ,
				novedad.novedad as novedad ,
				 novedad.fecha_creacion as novedad_fecha_creacion ,
				 novedad.km as km_vehiculo ,
				  novedad.ciudad  as ciudad_fecha_nov ,
				   novedad_requisicion.requisicion  as requisicion ,
				  tipoNovedad.nombre  as tipo_novedad,
				  tipoNovedad.color  as color_novedad,
					vehiculo.modelo  as modelo_vehiculo ,
					vehiculo.placa  as placa ,
					linea_vehiculo.nombre  as linea_vehiculo,
					marca_vehiculo.nombre  as marca_vehiculo
					
				from   aoa_modulo.novedad_requisicion 
					LEFT OUTER JOIN aoa_modulo.novedad ON novedad_requisicion.novedad = novedad.id_novedad  
					LEFT OUTER JOIN aoa_modulo.tipoNovedad on novedad.id_tipo = tipoNovedad.idtipoNovedad 
					LEFT OUTER JOIN aoacol_aoacars.vehiculo ON  novedad.placa_aoa  = vehiculo.placa 
					LEFT OUTER JOIN aoacol_aoacars.linea_vehiculo ON  vehiculo.linea  = linea_vehiculo.id 
					LEFT OUTER JOIN aoacol_aoacars.marca_vehiculo ON  linea_vehiculo.marca  = marca_vehiculo.id 
				 where aoa_modulo.novedad.id_novedad   = $id_novedad 
				 and  aoa_modulo.novedad_requisicion.requisicion  >= 1  


				 ORDER by id_nov_cot DESC "    );
				 
				 
				 
					while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array(
		
		    "id_nov_cot"=>$row['id_nov_cot'], "id_novedad"=>$row['id_novedad'],"novedad"=>$row['novedad'] ,"ciudad"=>$row['ciudad'] ,
			"estado_novedad_requisicion"=>$row['estado_novedad_requisicion'] ,"requisicion"=>$row['requisicion'] ,
			"actividad_provedor"=>$row['actividad_provedor'],"tipo_novedad"=>$row['tipo_novedad'] ,
			"ciudad_fecha_nov"=>$row['novedad_fecha_creacion'],"novedad_fecha_creacion"=>$row['ciudad_fecha_nov'],"proveedor"=>$row['proveedor'] ,"modelo_vehiculo"=>$row['modelo_vehiculo'] ,
			"linea_vehiculo"=>$row['linea_vehiculo'],"marca_vehiculo"=>$row['marca_vehiculo'] ,
			"placa"=>$row['placa'] , "color_novedad"=>$row['color_novedad'] 
		
		);
	}
		echo json_encode($tabledata);

		
		
	
}  


function buscar_novedad_cotizacion() // recibe la informacion del formulario de busqueda
{ 

         
		$id_novedad = $_POST['id_novedad']; 

			$Sins=q("select novedad_requisicion.id as id_nov_cot ,novedad.id_novedad as id_novedad,
				 novedad_requisicion.actividad_provedor as 
				actividad_provedor  ,
				novedad_requisicion.proveedor as 
				proveedor  ,novedad.ciudad_reporte as 
				ciudad  ,
                 novedad_requisicion.estado_novedad_requisicion as 
				estado_novedad_requisicion  ,
				novedad.novedad as novedad ,
				novedad_requisicion.requisicion as requisicion ,
				 novedad.fecha_creacion as novedad_fecha_creacion ,
				 novedad.km as km_vehiculo ,
				  novedad.ciudad  as ciudad_fecha_nov ,
				  tipoNovedad.nombre  as tipo_novedad,
				  tipoNovedad.color  as color_novedad,
					vehiculo.modelo  as modelo_vehiculo ,
					vehiculo.placa  as placa ,
					linea_vehiculo.nombre  as linea_vehiculo,
					aoacol_administra.estado_requisicion.color_co as color_req,
					aoacol_administra.estado_requisicion.nombre as nombre_req,
					aoacol_administra.estado_requisicion.id as id_req,
					
					marca_vehiculo.nombre  as marca_vehiculo
					
				from   aoa_modulo.novedad_requisicion 
					LEFT OUTER JOIN aoa_modulo.novedad ON novedad_requisicion.novedad = novedad.id_novedad  
					LEFT OUTER JOIN aoa_modulo.tipoNovedad on novedad.id_tipo = tipoNovedad.idtipoNovedad 
					LEFT OUTER JOIN aoacol_aoacars.vehiculo ON  novedad.placa_aoa  = vehiculo.placa 
					LEFT OUTER JOIN aoacol_administra.estado_requisicion ON  novedad_requisicion.estado_novedad_requisicion  = aoacol_administra.estado_requisicion.id 
					LEFT OUTER JOIN aoacol_aoacars.linea_vehiculo ON  vehiculo.linea  = linea_vehiculo.id 
					LEFT OUTER JOIN aoacol_aoacars.marca_vehiculo ON  linea_vehiculo.marca  = marca_vehiculo.id 
				 where aoa_modulo.novedad.id_novedad   = $id_novedad  ORDER by id_nov_cot DESC "    );
				 
				 
				 
					while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array(
		
		    "id_nov_cot"=>$row['id_nov_cot'], "id_novedad"=>$row['id_novedad'],"novedad"=>$row['novedad'] ,"ciudad"=>$row['ciudad'] ,
			"estado_novedad_requisicion"=>$row['estado_novedad_requisicion'] ,
			"actividad_provedor"=>$row['actividad_provedor'],"tipo_novedad"=>$row['tipo_novedad'] ,
			"ciudad_fecha_nov"=>$row['novedad_fecha_creacion'],"novedad_fecha_creacion"=>$row['ciudad_fecha_nov'],"proveedor"=>$row['proveedor'] ,"modelo_vehiculo"=>$row['modelo_vehiculo'] ,
			"linea_vehiculo"=>$row['linea_vehiculo'],
			"color_req"=>$row['color_req'],
			"id_req"=>$row['id_req'],
			"nombre_req"=>$row['nombre_req'],
			
			"marca_vehiculo"=>$row['marca_vehiculo'] ,
			"placa"=>$row['placa'] , "color_novedad"=>$row['color_novedad'] 
		
		);
	}
		echo json_encode($tabledata);

		
		
	
}  


function tipo_servicio_sini() // recibe la informacion del formulario de busqueda
{ 
         
		$servicio = $_POST['servicio']; 

			$Sins=q("select * from aoacol_aoacars.tipo_servicio 	WHERE  aoacol_aoacars.tipo_servicio.id ='$servicio' ");
		while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
		}

	
}  


function centros_costo() // recibe la informacion del formulario de busqueda
{ 

		 $result = q("select id,nombre from aoacol_administra.ccostos_uno");
		
        	
		while($row = mysql_fetch_array($result)){
		$tabledata[]= array("id"=>$row['id'],"nombre"=>$row['nombre']);
	}
		echo json_encode($tabledata);
			
	
}


function buscar_NovedadRequisicion() // recibe la informacion del formulario de busqueda
{ 

		  $id=q("SELECT MAX(id) as id FROM `aoa_modulo`.`novedad_requisicion` ;   ");
		
		while($row=mysql_fetch_object($id)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			$table_data =  array($row);
		};
		echo json_encode($table_data);
} 



function buscar_novedad_pdf() // recibe la informacion del formulario de busqueda
{ 
         
		$id_novedad = $_POST['id_novedad']; 
		$cotizacion_id = $_POST['cotizacion_id']; 

			$Sins=q("select aoa_modulo.novedad.id_novedad as id_novedad,
						aoa_modulo.novedad.fecha_creacion as fecha_creacion,
						aoa_modulo.novedad.ciudad as ciudad_novedad,
						aoacol_aoacars.vehiculo.placa as placa,
						aoacol_aoacars.linea_vehiculo.nombre as linea,
						aoacol_aoacars.marca_vehiculo.nombre as marca,
						MAX(aoacol_aoacars.ubicacion.odometro_final)  as km,
						aoacol_administra.proveedor.nombre as proveedor,
						aoacol_administra.oficina.nombre as ubicacion,
						aoacol_administra.proveedor.nombre as proveedor_nombre,
						aoacol_administra.proveedor.celular as ubicacion_proveedor,
						aoacol_administra.proveedor.celular as celular,
						aoacol_administra.proveedor.email as correo,
						aoa_modulo.novedad.novedad as novedad,
						MAX(aoacol_aoacars.ubicacion.fecha_inicial)as fechaIngreso,
						MAX(aoacol_aoacars.ubicacion.odometro_inicial) as kmIngreso,
						MAX(aoacol_aoacars.ubicacion.fecha_final) as fechaSalida,
						MAX(aoacol_aoacars.ubicacion.odometro_final) as kmIngreso,
						aoa_modulo.novedad_requisicion.descripcion as descripcion,
						aoa_modulo.novedad_requisicion.actividad_solitante as actividad_solitante,
						aoa_modulo.novedad_requisicion.actividad_provedor as actividad_provedor
								from  aoa_modulo.novedad  
						LEFT OUTER JOIN aoa_modulo.tipoNovedad
					 on novedad.id_tipo = tipoNovedad.idtipoNovedad 
					 	LEFT OUTER JOIN aoa_modulo.novedad_requisicion
					 on novedad.id_novedad = novedad_requisicion.novedad 
					 	LEFT OUTER JOIN aoacol_administra.proveedor
					 on novedad_requisicion.proveedor = proveedor.id 
						LEFT OUTER JOIN aoacol_aoacars.vehiculo
					 on novedad.id_placa = vehiculo.placa 
					 	LEFT OUTER JOIN aoacol_aoacars.ubicacion
					 on vehiculo.id = ubicacion.vehiculo 
					  	LEFT OUTER JOIN aoacol_administra.oficina
					 on ubicacion.oficina = oficina.id 
					LEFT OUTER JOIN 
					aoacol_aoacars.linea_vehiculo on  vehiculo.linea = linea_vehiculo.id 
					LEFT OUTER JOIN 
					aoacol_aoacars.marca_vehiculo on  linea_vehiculo.marca = marca_vehiculo.id 
					WHERE  aoa_modulo.novedad.id_novedad  ='$id_novedad' 
					and aoa_modulo.novedad_requisicion.id  ='$cotizacion_id'
					");
		while($S=mysql_fetch_object($Sins))
			
			{	
			 $data= json_encode($S);
			echo$data;
		}

}


function buscar_novedad_placa() // recibe la informacion del formulario de busqueda
{ 
         
		$id_novedad = $_POST['id_novedad']; 

			$Sins=q("select aoa_modulo.novedad.id_novedad as id_novedad,
						aoa_modulo.novedad.fecha_creacion as fecha_creacion,
						aoa_modulo.novedad.ciudad as ciudad_novedad,
						aoacol_aoacars.vehiculo.placa as placa,
						aoacol_aoacars.linea_vehiculo.nombre as linea,
						aoacol_aoacars.marca_vehiculo.nombre as marca,
						MAX(aoacol_aoacars.ubicacion.odometro_final)  as km,
						aoacol_administra.proveedor.nombre as proveedor,
						aoacol_administra.oficina.nombre as ubicacion,
						aoacol_administra.proveedor.nombre as proveedor_nombre,
						aoacol_administra.proveedor.celular as ubicacion_proveedor,
						aoacol_administra.proveedor.celular as celular,
						aoacol_administra.proveedor.email as correo,
						aoa_modulo.novedad.novedad as novedad,
						MAX(aoacol_aoacars.ubicacion.fecha_inicial)as fechaIngreso,
						MAX(aoacol_aoacars.ubicacion.odometro_inicial) as kmIngreso,
						MAX(aoacol_aoacars.ubicacion.fecha_final) as fechaSalida,
						MAX(aoacol_aoacars.ubicacion.odometro_final) as kmIngreso,
						aoa_modulo.novedad_requisicion.descripcion as descripcion,
						aoa_modulo.novedad_requisicion.actividad_solitante as actividad_solitante,
						aoa_modulo.novedad_requisicion.actividad_provedor as actividad_provedor
								from  aoa_modulo.novedad  
						LEFT OUTER JOIN aoa_modulo.tipoNovedad
					 on novedad.id_tipo = tipoNovedad.idtipoNovedad 
					 	LEFT OUTER JOIN aoa_modulo.novedad_requisicion
					 on novedad.id_novedad = novedad_requisicion.novedad 
					 	LEFT OUTER JOIN aoacol_administra.proveedor
					 on novedad_requisicion.proveedor = proveedor.id 
						LEFT OUTER JOIN aoacol_aoacars.vehiculo
					 on novedad.id_placa = vehiculo.placa 
					 	LEFT OUTER JOIN aoacol_aoacars.ubicacion
					 on vehiculo.id = ubicacion.vehiculo 
					  	LEFT OUTER JOIN aoacol_administra.oficina
					 on ubicacion.oficina = oficina.id 
					LEFT OUTER JOIN 
					aoacol_aoacars.linea_vehiculo on  vehiculo.linea = linea_vehiculo.id 
					LEFT OUTER JOIN 
					aoacol_aoacars.marca_vehiculo on  linea_vehiculo.marca = marca_vehiculo.id 
					WHERE  aoa_modulo.novedad.id_novedad  ='$id_novedad' ");
		while($S=mysql_fetch_object($Sins))
			
			{	
			 $data= json_encode($S);
			echo$data;
		}

}



?>