 <?php
/**
 *  JEFE OPERATIVO AOA
 *
 *		activación de vehículos de flota AOA
 *
 * @version $Id$
 * @copyright 2010|
 
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

function datos_session() // recibe la informacion del formulario de busqueda
{  
  $tabla_usuario = $_POST['tabla_usuario']; 
 $usuario = $_POST['usuario']; 
  
  $Sins=q("Select * from aoacol_aoacars.$tabla_usuario where  nombre = '$usuario' ");
  

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



function buscar_siniestro() // recibe la informacion del formulario de busqueda
{  

$siniestro = $_POST['siniestro']; 

			$Sins=q("select * from aoacol_aoacars.siniestro  where id = '$siniestro '  or numero = '$siniestro ' "  );
	
		
		while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
		}

	
			
}

function buscar_siniestro_ciudad_original() // recibe la informacion del formulario de busqueda
{  

$ciudad_original = $_POST['ciudad_original']; 

		$Ofic=qo("select * from oficina where ciudad=$ciudad_original");
			
			 $data= json_encode($Ofic);
			echo$data;
		
}



function NovedadCall_envio() // recibe la informacion del formulario de busqueda
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
					"para" => $para,
					"copia" => $copia,
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
		$reportado = $_POST['reportado']; 
		$ciudorte = $_POST['ciudorte']; 
		$encanombre = $_POST['encanombre']; 
		$contenido = $_POST['contenido']; 
		$url = $_POST['url']; 

	   $data_mail = array(
					"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
					"enviarEmail" => "true",
					"id" => $id,
					"para" => $para,
					"copia" => $copia,
					"idnovedad" => $idnovedad,
					"ciudorte" =>  $ciudorte,
					"asunto" =>  $asunto,
					"reportado" =>$reportado,
					"encanombre" => $encanombre,
					"url" => $url,
					"contenido" => $contenido
					);

					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/ServiEmail.php');
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
					curl_exec($ch);
					curl_close($ch);
	              	echo $ch;
}



function buscar_detalle_id() 
{ 
$id = $_POST['id']; 
   $Sins=q("SELECT  * FROM `aoa_modulo`.`itenes` where novedad_requisicion = '$id' ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("tipo"=>$row['tipo'],"clase"=>$row['clase'],"tipoItem"=>$row['tipoItem']
		,"observaciones"=>$row['observaciones'],"cantidad"=>$row['cantidad'],"valor"=>$row['valor'],"valor_total"=>$row['valor_total']   );
	}
		echo json_encode($tabledata);
}


function buscar_proveedor_id() 
{ 
$id = $_POST['id']; 
   $Sins=q("SELECT  * FROM `aoacol_administra`.`proveedor` where id = '$id' ");  
        	
		while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("identificacion"=>$row['identificacion'],"nombre"=>$row['nombre'],"ciudad"=>$row['ciudad']
		,"telefono1"=>$row['telefono1'],"celular"=>$row['celular'],"email"=>$row['email']);
	}
	
		echo json_encode($tabledata);
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
		$table_data[]= array("id"=>$row['idtipoNovedad'],"nombre"=>$row['nombre']);
	}
		echo json_encode($table_data);
}

function buscar_placa() // recibe la informacion del formulario de busqueda
{ 

$placa = $_POST['placa']; 

			$Sins=q("SELECT  * FROM `aoa_pa_sinistro`.`vehiculo` where placa ='$placa'");
			
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
  $id_sinistro=$_POST['id_sinistro'];
  $id_placa =$_POST['id_placa'];
  $novedad=$_POST['novedad'];
  $solicitante=$_POST['solicitante'];
  $ciudad=$_POST['ciudad'];
  $fecha_creacion=$_POST['fecha_creacion'];

      $qry = q("INSERT INTO `aoa_modulo`.`novedad`
	  (id_tipo, novedad,fecha_creacion,ciudad,id_sinistro,id_placa,solicitante) 
	  VALUES ('$id_tipo', '$novedad','$fecha_creacion','$ciudad', '$id_sinistro' ,'$id_placa', '$solicitante' )");
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
function usuarioEncargado(){
	
$ciudad = $_POST['ciudad'];

$ofic=qo("select * from aoacol_aoacars.oficina where nombre='$ciudad' ");

$user = q("select * from aoacol_aoacars.operario where oficina = '$ofic->id' and fecha_retiro = '0000-00-00' ");  	
		
        	
		while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id"=>$row['id'],"nombre"=>$row['nombre'],"apellido"=>$row['apellido']);
	}
		echo json_encode($tabledata);
		
		
		
}

function buscar_encargado(){
	
$id = $_POST['id'];
$user = qo("select * from aoacol_aoacars.operario where id = '$id'  ");  	
		echo json_encode($user);
}


function buscar_novedad_operario(){
	
$id = $_POST['id'];
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.reportado as reportado,
   aoa_modulo.novedad.tele_reporte as tele_reporte,
   aoa_modulo.novedad.email_reporte as email_reporte,
   aoa_modulo.novedad.id_sinistro as id_sinistro,
   aoa_modulo.novedad.novedad as novedad, 
     aoa_modulo.novedad.encargado as encargado, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_sinistro as id_sinistro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
      aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoacol_aoacars.operario.nombre as nombre_opera,
      aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
	WHERE  aoa_modulo.novedad.id_novedad  ='$id'  ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_sinistro"=>$row['id_sinistro'],
		   "encargado"=>$row['encargado'],
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

function buscar_novedades(){
	
$user = q("select  aoa_modulo.novedad.id_novedad as id_novedad,
   aoa_modulo.novedad.reportado as reportado,
   aoa_modulo.novedad.tele_reporte as tele_reporte,
   aoa_modulo.novedad.email_reporte as email_reporte,
   aoa_modulo.novedad.id_sinistro as id_sinistro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_sinistro as id_sinistro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
 ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_sinistro"=>$row['id_sinistro'],
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
   aoa_modulo.novedad.reportado as reportado,
   aoa_modulo.novedad.tele_reporte as tele_reporte,
   aoa_modulo.novedad.email_reporte as email_reporte,
   aoa_modulo.novedad.id_sinistro as id_sinistro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_sinistro as id_sinistro,
   aoa_modulo.novedad.novedad as novedad, 
   aoa_modulo.novedad.cierre as cierre, 
   aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
   aoacol_aoacars.operario.nombre as nombre_opera,
   aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
	WHERE  aoa_modulo.novedad.encargado  ='$encargado'  ");  	
	

			while($row = mysql_fetch_array($user)){
		$tabledata[]= array("id_novedad"=>$row['id_novedad'],
		"reportado"=>$row['reportado'],
		"novedad"=>$row['novedad'],
		"email_reporte"=>$row['email_reporte'],
	     "id_opera"=>$row['id_opera'],
		"ciudad_reporte"=>$row['ciudad_reporte'],
		"solicitante"=>$row['solicitante'],
		"id_sinistro"=>$row['id_sinistro'],
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

function centro_costo(){
	
$centro_costo = $_POST['centro_costo'];

$Sins=q("select * from aoacol_aoacars.aseguradora where id ='$centro_costo ' ");  
        	
	while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
		}
		
		
		
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
	  (novedad_requisicion, 
	  tipo,clase,valor,
	  tipoItem,observaciones,
	  tipo_cobro,cantidad,
	  centro_costo,centro_operacion,
	  valor_total,
	  id_vehiculo,factor) 
	  VALUES ('$novedad_requisicion',
	  '$tipo','$clase','$valor','$tipoItem','$observaciones', '$tipo_cobro' 
    ,'$cantidad ', '$centro_costo','$centro_operacion'
	, '$valor_total', '$id_vehiculo'   , '$factor'  )");	

	
	
      buscar_detalle();
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
  $id_sinistro=$_POST['id_sinistro'];
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
        $qry = q("INSERT INTO `aoa_modulo`.`novedad`
	  (id_tipo, novedad,fecha_creacion,ciudad,id_sinistro,id_placa,solicitante,encargado,tipo_cierre,
	  cierre,reportado,tele_reporte,ciudad_reporte,email_reporte) 
	  VALUES ('$id_tipo', '$novedad','$fecha_creacion','$ciudad', '$id_sinistro' ,'$id_placa',
	  '$solicitante' ,'$encargado'  ,'$tipo_cierre' ,'$cierre', ' $reportado' ,'$tele_reporte' ,'$ciudad_reporte' ,'$email_reporte'  )");
      buscar_novedad();
}


function editarNovedad_operador() // recibe la informacion del formulario de busqueda
{ 
  $id_novedad=$_POST['id_novedad'];
  $id_tipo=$_POST['id_tipo'];
  $id_sinistro=$_POST['id_sinistro'];
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
    id_sinistro = '$id_sinistro',
   id_placa = '$id_placa',
   encargado = '$encargado',
    tipo_cierre = '$tipo_cierre',
   cierre = '$cierre',
   reportado = '$reportado',
   tele_reporte = '$tele_reporte',
   ciudad_reporte = '$ciudad_reporte',
   email_reporte = '$email_reporte',
   tipo_cierre_opera = '$acti_caarga',
   cierre_oper = '$cierre_oper'  where id_novedad = '$id_novedad' ");
  
      
}




function ingresaNovedad() // recibe la informacion del formulario de busqueda
{ 
  $id_tipo=$_POST['id_tipo'];
  $id_placa=$_POST['id_placa'];
  $novedad=$_POST['novedad'];
  $solicitante=$_POST['solicitante'];
  $ciudad=$_POST['ciudad'];
  $fecha_creacion=$_POST['fecha_creacion'];

      $qry = q("INSERT INTO `aoa_modulo`.`novedad`
	  (id_tipo, novedad,fecha_creacion,ciudad,id_placa,solicitante) 
	  VALUES ('$id_tipo', '$novedad','$fecha_creacion','$ciudad', '$id_placa' , '$solicitante' )");
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
	  (novedad, proveedor,descripcion,actividad_solitante,actividad_provedor) 
	  VALUES ('$novedad', '$proveedor','$descripcion','$actividad_solitante', '$actividad_provedor' )");
      buscar_NovedadRequisicion();
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


function buscar_novedad() // recibe la informacion del formulario de busqueda
{ 

		  $id=q("SELECT MAX(id_novedad) as id FROM `aoa_modulo`.`novedad` ;   ");
		
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
         
		$innovedad = $_POST['innovedad']; 

			$Sins=q("select 	*	from aoa_modulo.tipoNovedad
					LEFT OUTER JOIN aoa_modulo.novedad on tipoNovedad.idtipoNovedad = novedad.id_tipo
					LEFT OUTER JOIN aoacol_aoacars.siniestro on novedad.id_sinistro = siniestro.id 
					WHERE  aoa_modulo.novedad.id_novedad ='$innovedad' ");
		while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
		}

}


function buscar_cotización() // recibe la informacion del formulario de busqueda
{ 
         
		$innovedad = $_POST['innovedad']; 

			$Sins=q("SELECT  * FROM `aoa_modulo`.`novedad_requisicion` WHERE novedad = '$innovedad'   ");
		while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	
			 $data= json_encode($S);
			echo$data;
		}

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

			$Sins=q("select 	*	from   aoa_modulo.novedad_requisicion  where   aoa_modulo.novedad_requisicion.novedad  ='$novedad' ");
			while($row = mysql_fetch_array($Sins)){
		$tabledata[]= array("id"=>$row['id'],"novedad"=>$row['novedad'],"requision"=>$row['requision'],
		"proveedor"=>$row['proveedor'],
		"descripcion"=>$row['descripcion'],"actividad_solitante"=>$row['actividad_solitante'],"actividad_provedor"=>$row['actividad_provedor']);
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