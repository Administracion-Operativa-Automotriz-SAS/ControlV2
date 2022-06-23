<?php 
include('inc/funciones_.php');
include('inc/chart/Includes/FusionCharts.php'); // inserta rutinas para presentación de graficos
include('inc/link.php');
include('libs/lib/WideImage.php');
define ('APIKEYAOAAPP','yNPlsmOGgZoGmH$8');
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 1);
header('Content-Type: application/json');
header('Content-Type: text/html; charset=utf-8');


mysql_connect("app.aoacolombia.com", "aoacol_arturo", "AOA0l1lwpdaa");
mysql_select_db("aoacol_aoacars");
 
if(!$_REQUEST){
	$request_body = file_get_contents('php://input');
	if($request_body && json_decode($request_body)){
		//echo "request body";
			$request_body = json_decode($request_body);
			//print_r($request_body);
			$_REQUEST = (array) $request_body;
			//print_r($_REQUEST);
	}
}

if(isset($_REQUEST['APIKEYAOAAPP'])){
	if($_REQUEST['APIKEYAOAAPP'] != 'yNPlsmOGgZoGmH$8'){
		echo json_encode("No tiene acceso");
		exit();
	}
}else{
	echo json_encode("No tiene acceso");
		exit;
}

if(isset($_REQUEST['create_event'])){
	$service = new WebServicesEventoApp($_REQUEST);
	echo json_encode($service->create_evento());
}
if(isset($_REQUEST['see_event'])){
	$service = new WebServicesEventoApp($_REQUEST);
	echo json_encode($service->see_event());
}
if(isset($_REQUEST['update_date_end'])){
	$service = new WebServicesEventoApp($_REQUEST);
	echo json_encode($service->update_event_date_end());
}

if(isset($_REQUEST['see_tipo_evento'])){
	$service = new WebServicesEventoApp($_REQUEST);
	echo json_encode($service->seet_tipo_evento_tabla());
}

if(isset($_REQUEST['see_evento_actividad'])){
	$service = new WebServicesEventoApp($_REQUEST);
	echo json_encode($service->seet_evento_actividad());
}
if(isset($_REQUEST['event_exercise'])){
	$service = new WebServicesEventoApp($_REQUEST);
	echo json_encode($service->actividades_aviertas());
}

if(isset($_REQUEST['filter_day'])){
	$service = new WebServicesEventoApp($_REQUEST);
	echo json_encode($service->filtrar_por_dia());
}

if(isset($_REQUEST['upload_img_departure'])){
	$service = new WebServicesEventoApp($_REQUEST);
	echo json_encode($service->subir_img_salida());
}

if(isset($_REQUEST['upload_img_return'])){
	$service = new WebServicesEventoApp($_REQUEST);
	echo json_encode($service->subir_img_devolucion());
}


if(isset($_REQUEST['upload_img_arribo'])){
	$service = new WebServicesEventoApp($_REQUEST);
	echo json_encode($service->validar_arribo());
}

if(isset($_REQUEST['acta_de_entrega'])){
	$service = new WebServicesEventoApp($_REQUEST);
	echo json_encode($service->acta_de_entrega_cita());
}


if(isset($_REQUEST['ultimo_km_vh'])){
	$service = new WebServicesEventoApp($_REQUEST);
	echo json_encode($service->ultimoKmVh());
}
class d_tarjeta // clase que se usa para pintar datos del tarjeta habiente en la cabecera del acta de entrega/devolucion para garantias de tarjeta credito
{
	var $Nombre_tarjeta_habiente='';  // nombre del dueño de la tarjeta quien autoriza el congelamiento de cupo
	var $Identificacion=0; // identificación del tarjeta habiente quien autoriza el congelamiento de cupo
	var $Franquicia=''; // nombre de la franquicia
	var $Numero_tarjeta=''; // numero de la tarjeta de credito
	var $Vencimiento_mes=''; // mes de vencimiento de la tarjeta de crédito
	var $Vencimiento_ano='';  // año de vencimiento de la tarjeta de crédito
	var $Valor_garantia=''; // monto de la garantía a ser congelado

	function d_tarjeta($D) // funcion creadora de la instancia
	{
		$this->Nombre_tarjeta_habiente=$D->nombre;
		$this->Identificacion=$D->identificacion;
		$this->Franquicia=$D->nfranq;
		$this->Numero_tarjeta=$D->numero;
		$this->Vencimiento_mes=$D->vencimiento_mes;
		$this->Vencimiento_ano=$D->vencimiento_ano;
		$this->Valor_garantia=$D->valor;
	}
}


Class WebServicesEventoApp{
	
	function __construct($request){
		$this->request = $request;
		$this->left_params = "";
	}
	
	public function create_evento(){
		
		
		$arrayValidarCampos = array(
		"ID_EVENTO_EMPLEADO" => "idEmpleado",
		"ID_TIPO_EVENTO" => "idTipoEvento",
		"FECHA_EVENTO" => "fecha_evento",
		"LOGITUD" => "longitud",
		"LATITUD" => "latitud",
		"DESCRIPCION" => "comentario");
		
		if($this->check_request($arrayValidarCampos)>0){
			return array("estado" => 2, "desc" => "Faltan datos para progresar la solicitud", "parametros"=>$this->left_params);
		}
		
		$idEmpleado = $this->request['ID_EVENTO_EMPLEADO'];
		
		$idTipoEvento = $this->request['ID_TIPO_EVENTO'];
		
		$fecha_transmision = date("Y-m-d H:i:s");
		
		$fecha_evento = $this->request['FECHA_EVENTO'];
		
		$longitud = $this->request['LOGITUD'];
		
		$latitud = $this->request['LATITUD'];
		
		$comentario = $this->request['COMENTARIO'];
		
		$descripcion = $this->request['DESCRIPCION'];
		
		
		switch($idTipoEvento){
			case 7:
			$arrayEventoActivity = array("EVENTO_ACTIVIDAD" => "evento_actividad");
			if($this->check_request($arrayEventoActivity)>0){
			return array("estado" => 2, "desc" => "Faltan datos para progresar la solicitud: PERMISO", "parametros"=>$this->left_params);
			}
			$evento_actividad = $this->request['EVENTO_ACTIVIDAD'];
			break;
			case 9:
			$arrayEventoActivity = array("EVENTO_ACTIVIDAD" => "evento_actividad");
			if($this->check_request($arrayEventoActivity)>0){
			return array("estado" => 2, "desc" => "Faltan datos para progresar la solicitud: CAPACITACION", "parametros"=>$this->left_params);}
			$evento_actividad = $this->request['EVENTO_ACTIVIDAD'];
			break;
		}
		
		$consulta = "SELECT * FROM  aoacol_aoatiempos.eventos WHERE evento_empleado = $idEmpleado and tipo_evento = $idTipoEvento and  fin_evento = '0000-00-00 00:00:00'";
		
		
		$existe =  $this->fetch_objects_test($consulta);
		
		if($existe){
			return array("estado" => 2, "" => "Hay una entrada en curso con este tipo de evento");
		}
		
		$query = "INSERT INTO aoacol_aoatiempos.eventos (evento_empleado,tipo_evento,tipo_evento_actividad,fecha_transmision,fecha_evento,longitud,latitud,comentario,descripcion) VALUES ($idEmpleado,$idTipoEvento,'$evento_actividad','$fecha_transmision','$fecha_evento','$longitud','$latitud','$comentario','$descripcion')";
		
		
		try{
			$Id_nuevo = q($query);
			
			return array("estado"=>1,"numero_evento"=>"Numero Evento ". $Id_nuevo ." fue grabada satisfactoriamente");
		}catch(Exception $e){
			
			return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
			
		}
		
		
	}
	
	/*Ver evento con su respectivo id*/
	public function see_event(){
		
		
		$arrayIevento = array("ID_EVENTO" => "idEvento");
		  
		  if($this->check_request($arrayIevento)>0){
			return array("estado" => 2, "desc" => "Faltan datos para progresar la solicitud", "parametros"=>$this->left_params);}
		   
		   $idEvento = $this->request['ID_EVENTO'];
		   
		   $query = "SELECT eve.id ID_EVENTO,op.nombre NOMBRE_OPERARIO,tie.nombre TIPO_EVENTO,
					se.nombre SECUENCIA
					,eve.fecha_transmision FECHA_TRANSMICION,eve.fecha_evento FECHA_EVENTO,eve.fin_evento FECHA_FIN,
					eve.longitud LONGITUD,eve.latitud LATITUD,eve.comentario COMENTARIO,eve.descripcion DESCRIPCION
					FROM aoacol_aoatiempos.eventos eve
					LEFT JOIN aoacol_aoacars.operario op ON eve.evento_empleado = op.id
					LEFT JOIN aoacol_aoatiempos.evento_tipo tie ON eve.tipo_evento = tie.id 
					LEFT JOIN aoacol_aoatiempos.evento_actividad se ON eve.tipo_evento_actividad = se.id
					WHERE eve.id = $idEvento ORDER BY ID_EVENTO DESC";
					
		try{
			
			$consultaQueryEstado =  $this->fetch_objects_test($query);
			
		 foreach($consultaQueryEstado as $ievento){
			 $ievento->TIPO_EVENTO = utf8_encode($ievento->TIPO_EVENTO);
		 }
			
			if(!$consultaQueryEstado){
				return array("estado" => 2, "desc" => "Aun no hay datos disponibles con el numero ".$idEvento);
			}
			
			$response = array("evento" => $consultaQueryEstado);
			
			return  $response;
		
		}catch(Exception $e){
			return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
		}	
					
	}
	public function seet_tipo_evento_tabla(){
		$query = "SELECT * FROM aoacol_aoatiempos.evento_tipo";
		
		try{
			$verTipoEventoTable = $this->fetch_objects_test($query);
			foreach($verTipoEventoTable as $iVerEvento){
				$iVerEvento->nombre = utf8_encode($iVerEvento->nombre);
			}
			
			$response = array("tabla_eventos" => $verTipoEventoTable);
			
			return $response;
			
		}catch(Exception $e){
			return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
		}
		
	}
	
	public function seet_evento_actividad(){
		$query = "SELECT id,nombre FROM aoacol_aoatiempos.evento_actividad";
		
		try{
			$verTipoEventoTable = $this->fetch_objects_test($query);
			foreach($verTipoEventoTable as $iVerEvento){
				$iVerEvento->nombre = utf8_encode($iVerEvento->nombre);
			}
			
			$response = array("tabla_eventos_actividad" => $verTipoEventoTable);
			
			return $response;
			
		}catch(Exception $e){
			return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
		}
	}
	
	
	
	/*Ver eventos de usuario*/
	public function update_event_date_end(){
		
		$arrayIevento = array("ID_EVENTO" => "idEvento","FECHA_FINAL_EVENTO" => "fecha_final");
		  
		  if($this->check_request($arrayIevento)>0){
			return array("estado" => 2, "desc" => "Faltan datos para progresar la solicitud", "parametros"=>$this->left_params);}
		   
		   $idEvento = $this->request['ID_EVENTO'];
		   
		   $fecha_final = $this->request['FECHA_FINAL_EVENTO'];
		   
		   $sql = "UPDATE aoacol_aoatiempos.eventos SET fin_evento = '$fecha_final' WHERE id = $idEvento";
		   
		   try{
			    q($sql);
			    return array("estado"=>1,"descripcion"=>"Numero de evneto actualizado $idEvento");
			   
			   
			}catch(Exception $e){
			return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");   
		   }
		
	}
	
	public function actividades_aviertas(){
		$arrayIevento = array("ID_OPERARIO" => "idOperario");
		
		$idOperario = $this->request['ID_OPERARIO'];
		
		if($this->check_request($arrayIevento)>0){
			return array("estado" => 2, "desc" => "Faltan datos para progresar la solicitud", "parametros"=>$this->left_params);}
		
		$sql = "SELECT eve.id ID_EVENTO,op.nombre NOMBRE_OPERARIO,tie.nombre TIPO_EVENTO,
					se.nombre SECUENCIA
					,eve.fecha_transmision FECHA_TRANSMICION,eve.fecha_evento FECHA_EVENTO,eve.fin_evento FECHA_FIN,
					eve.longitud LONGITUD,eve.latitud LATITUD,eve.comentario COMENTARIO,eve.descripcion DESCRIPCION
					FROM aoacol_aoatiempos.eventos eve
					LEFT JOIN aoacol_aoacars.operario op ON eve.evento_empleado = op.id
					LEFT JOIN aoacol_aoatiempos.evento_tipo tie ON eve.tipo_evento = tie.id 
					LEFT JOIN aoacol_aoatiempos.evento_actividad se ON eve.tipo_evento_actividad = se.id
					WHERE eve.evento_empleado = $idOperario AND eve.fin_evento ='0000-00-00 00:00:00' ORDER BY ID_EVENTO DESC";
					
       try{
			
			$consultaQueryActividadActivo =  $this->fetch_objects_test($sql);
			
		 foreach($consultaQueryActividadActivo as $ievento){
			 $ievento->TIPO_EVENTO = utf8_encode($ievento->TIPO_EVENTO);
		 }
			
			if(!$consultaQueryActividadActivo){
				return array("estado" => 2, "Aun no hay datos disponibles con el numero ".$idOperario);
			}
			
			$response = array("evento_activo" => $consultaQueryActividadActivo);
			
			return  $response;
		
		}catch(Exception $e){
			return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
		}
	}  
	public function filtrar_por_dia(){
		$arrayFilter = array("ID_OPERARIO" => "idOperario", "FECHA_DIA" => "fechaDia");
		
		$idOperario = $this->request['ID_OPERARIO'];
		
		$fechaDia = $this->request['FECHA_DIA'];
		
		if($this->check_request($arrayFilter)>0){
			return array("estado" => 2, "desc" => "Faltan datos para progresar la solicitud", "parametros"=>$this->left_params);}
		$sql = "SELECT eve.id ID_EVENTO,op.nombre NOMBRE_OPERARIO,tie.nombre TIPO_EVENTO,
					se.nombre SECUENCIA
					,eve.fecha_transmision FECHA_TRANSMICION,eve.fecha_evento FECHA_EVENTO,eve.fin_evento FECHA_FIN,
					eve.longitud LONGITUD,eve.latitud LATITUD,eve.comentario COMENTARIO,eve.descripcion DESCRIPCION
					FROM aoacol_aoatiempos.eventos eve
					LEFT JOIN aoacol_aoacars.operario op ON eve.evento_empleado = op.id
					LEFT JOIN aoacol_aoatiempos.evento_tipo tie ON eve.tipo_evento = tie.id 
					LEFT JOIN aoacol_aoatiempos.evento_actividad se ON eve.tipo_evento_actividad = se.id
					WHERE eve.evento_empleado = $idOperario AND eve.fin_evento = '$fechaDia' ORDER BY ID_EVENTO DESC";
					
			try{
			
			$consultaQueryFilterActivo =  $this->fetch_objects_test($sql);
			
		 foreach($consultaQueryFilterActivo as $ievento){
			 $ievento->TIPO_EVENTO = utf8_encode($ievento->TIPO_EVENTO);
		 }
			
			if(!$consultaQueryFilterActivo){
				return array("estado" => 2, "Aun no hay datos disponibles con el numero ".$idOperario);
			}
			
			$response = array("evento_activo" => $consultaQueryFilterActivo);
			
			return  $response;
		
		}catch(Exception $e){
			return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
		}		

	}
	/*
	function directorio_imagen($directorio='',$Id=0)
{
	if($directorio && $Id)
	{
		if(!is_dir($directorio)) { mkdir($directorio); chmod($directorio, 0777); }
		$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
		if(!is_dir($directorio.'/'.$Subdirectorio)) { mkdir($directorio.'/'.$Subdirectorio); chmod($directorio.'/'.$Subdirectorio, 0777);}
		if(!is_dir($directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($directorio.'/'.$Subdirectorio.'/'.$Id); chmod($directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}
		$ruta=$directorio.'/'.$Subdirectorio.'/'.$Id.'/';
	}
	else $ruta='';
	return $ruta;
}
*/
   public function subir_img_salida(){
	   
	   $arrayFilter = array("img_odo_salida_f" => "img_odo_salida_f", 
	   "fotovh1_f" => "fotovh1_f","fotovh2_f" => "fotovh2_f","fotovh3_f" => "fotovh3_f","fotovh4_f" => "fotovh4_f"
	   );
	   
	   if($this->check_request($arrayFilter)>0){
			return array("estado" => 2, "desc" => "Faltan datos para progresar la solicitud", "parametros"=>$this->left_params);
		}
	   $hoy2 = date("YmdHis");
	   
	   
	   /*Inicio Request*/
	   $idCita = $this->request['idCita'];
	   
	   $observacionesEntrega = $this->request['observacionesEntrega'];
	   
	   /*$tpq = $this->request['recorridoEnParqueadero'];*/
	   
	   $kmd = $this->request['kilometrajePrevioAldesplasamientoDomicilio'];
	   
	   
	   $kmi = $this->request['kilometrajeInicialAlservicio'];
	   
	   $observaciones = $this->request['observaciones'];
	   
	   $Nusuario = $this->request['Nusuario'];
	   
	   
	   $Email_usuario = $this->request['Email_usuario'];
	   
	   
	   $operario_domicilio = $this->request['operario_domicilio'];
	   
	    
	   /*Fin  Request*/
	   
	   
	   
	    ////Sacar ultimo kilometraje
	   
	   $Ahora = date('Y-m-d H:i:s'); $Hoy = date('Y-m-d');$Hora=date('H:i:s');
	   
	   $Cita=qo("select * from cita_servicio where id=$idCita");
	   
	   $Sin = qo("select * from siniestro where id=$Cita->siniestro"); // trae los datos del siniestro
	   
	   
	   
	   if($Sin->pasoApp != 1){
		   
	    
		if($kmi == 0){
		return array("estado"=>7,"desc"=>"Debe escribir un kilometraje inicial valido");exit();
	  }
	  
	  if($kmi < 0){return array("estado"=>3,"desc"=>"Debe escribir un kilometraje inicial válido igual que el último registrado"); exit();}
	
	  if($kmi < $ultimokm){ return array("estado"=>4,"desc"=>"Debe escribir un kilometraje inicial válido mayor o igual que el último registrado. 
	                                   No puede ser menor al último estado en la tabla de control.");exit(); }
		
	   
	   
	   if($Sin->ubicacion){
	   
	   return array("estado"=>5,"desc"=>"EL SINIESTRO TENIA UNA UBICACION DEBE DESLIGARLO PRIMERO");
	   exit();  
	   
	   }
	   
	   
	   $Veh=qo("select * from vehiculo where placa='$Cita->placa' "); // trae los datos del vehiculo
	   
	   $ultimokm=qo1("select kilometraje($Veh->id)");
	   
	   

	   $idv = qo("select id from vehiculo where placa='$Cita->placa'"); // obtiene el id del vehiculo
	   
	   $Oficina=qo("select * from oficina where id=$Cita->oficina"); //trae los datos de la oficina
	   
	   $Fec_entrega = aumentadias($Cita->fecha, $Cita->dias_servicio); // calcula la fecha de devolución del vehículo
	   
	    
	   ////////////////////                                   *******************                 CAMBIO DE ESTADO AUTOMATICO A SERVICIO                 ************************    ///////////////////////////
	// busca la ultima ubicacion para actualizar la fecha final con la fecha inicial del nuevo estado
	
	
	
	
	
	
	if($kmd){
		$diferencia = ($kmi - $kmd);
		$kmFinal = $kmd;
	}else{
		$diferencia = ($kmi - $ultimokm);
		$kmFinal = $kmi;
	}
	
	if($diferencia > 0  &&  $diferencia <=3){
		
		$observacionesEntregaExeso = "Exceso de kilolemtraje direncia de $diferencia desde App movil.";
		$sql1 = "insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,
		      odometro_final,odometro_diferencia,obs_mantenimiento,flota) 
		    values ('$Cita->oficina',$Veh->id,'$Hoy','$Hoy','94',$ultimokm,'$kmFinal','$diferencia',\"$observacionesEntregaExeso\",'$Sin->aseguradora')";
		
	}else{
		if($diferencia > 3){
			$observacionesEntregaExeso = "Exceso de kilolemtraje direncia de $diferencia desde App movil.";
		$sql2 =	"insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,
		      odometro_final,odometro_diferencia,obs_mantenimiento,flota) 
		    values ('$Cita->oficina',$Veh->id,'$Hoy','$Hoy','94',$ultimokm,'$kmFinal','$diferencia',\"$observacionesEntregaExeso\",'$Sin->aseguradora')";
			$excesoKm = q($sql2);
		graba_bitacora('ubicacion','A',$excesoKm,'Exceso de Kilometraje en el parqueadero');
		
		$mensaje = "<body><b>Notifcacion de exceso de kilometraje parqueadero</b>
		<br>Fecha $Hoy
		<br>Numero de siniestro: $Sin->numero
		<br>Numero de placa: $Cita->placa<br>
		Odometro inicial: $ultimokm<br>
		Odometro de entrega: $kmi<br>
		Diferencia: $diferencia<br>
		Este vehiculo excedio el kilometraje dentro del parqueadero 
		</body>"; 		
		
		   $data_mail = array(
			"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
			"enviarEmail" => "true",
			"para" => "sergiourbina@aoacolombia.com",
			"copia" => "sergiocastillo@aoacolombia.com",
			"asunto" => "Exceso de kilometraje en patio vehiculo: $Cita->placa'",
			"Hoy" => "$Hoy",
			"placa" => "$Cita->placa",
			"siniestro" => "$Sin->numero",
			"odometro_inicial"=> "$ultimokm",
			"odometro_entrega"=> "$kmi", 
			"diferencia" => "$diferencia",
			"mensaje" => "$mensaje"
			);
			
			$ch = curl_init();
		    
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/enviarWpApp.php');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
            curl_exec($ch);
			curl_close($ch);
			}
	}
	
	
	
	
	
	$Ultimo = qo("select * from ubicacion where vehiculo = $idv->id and fecha_final > '$Cita->fecha' and estado=2");
	   if ($Ultimo){// trae la ultima ubicación del vehiculo
		if ($Ultimo->fecha_inicial == $Cita->fecha){
			// si la fecha inicial y final coinciden dentro del mismo dia del cambio del nuevo estado, se elimina ese estado
			q("delete from ubicacion where id=$Ultimo->id");
		}else{
			q("update ubicacion set fecha_final='$Hoy' where id=$Ultimo->id"); // sino actualiza la ubicación actual
		} 
    }
	
	        
	   /*if($tpq) // si hay recorido en el parqueadero
	   {
		if($kmd) {$km1=$kmd-$tpq;$km2=$kmd;} else {$km1=$kmi-$tpq;$km2=$kmi;} // halla las distancias entre el ultimo kilometraje y el actual
		// inserta la ubicación
		$IDU1=q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) values 
		('$Cita->oficina','$idv->id','$Cita->fecha','$Cita->fecha','94','$km1','$km2','$tpq',\"Domicilio de entrega\",'$Sin->aseguradora')");
		graba_bitacora('ubicacion','A',$IDU1,'Adiciona automáticamente en cumplimiento de entrega.');
       }
	   */
	   
	    $operario_domicilioQuery = qo("SELECT operario_domicilio FROM cita_servicio WHERE id = $idCita");
		
		
		if(!$operario_domicilioQuery->operario_domicilio){
			q("UPDATE cita_servicio SET operario_domicilio = $operario_domicilio WHERE id = $idCita");
		}
	   
	   if($kmd) // si hay recorrido de domicilio
	   {
		$Diferencia=($kmi-$kmd); // calcula la distancia recorrida para el domicilio
		// inserta el registro en ubicacion
		$IDU2 =q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) 
		values ('$Cita->oficina','$idv->id','$Hoy','$Hoy','96','$kmd','$kmi','$Diferencia',\"Domicilio de entrega $Cita->dir_domicilio\",'$Sin->aseguradora')");
		graba_bitacora('ubicacion','A',$IDU2,'Adiciona domicilio automáticamente');
	   }
   
	   if(!$Sin->ubicacion){
		// inserta la ubicación
		if($Sin->aseguradora == 59 and $Sin->venta_directa == 1){
			$IDU3 =q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,obs_mantenimiento,flota) 
			values
			('$Cita->oficina','$idv->id','$Hoy','$Fec_entrega','103','$kmi','$kmi',\"$observaciones\",'$Sin->aseguradora')");
		}else if($Sin->aseguradora == 59 and $Sin->venta_directa !=1){
			$IDU3 =q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,obs_mantenimiento,flota) values
			('$Cita->oficina','$idv->id','$Hoy','$Fec_entrega','104','$kmi','$kmi',\"$observaciones\",'$Sin->aseguradora')");
		}else{
			$IDU3 = q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,obs_mantenimiento,flota) values
			('$Cita->oficina','$idv->id','$Hoy','$Fec_entrega','1','$kmi','$kmi',\"$observaciones\",'$Sin->aseguradora')");
			
		}
		graba_bitacora('ubicacion','A',$IDU3,'Inserta registro');
		
		// Actualiza el siniestro asigna la ubicación recien ingresada y la relaciona con el siniestro
		
		if($Sin->aseguradora == 59 and $Sin->venta_directa == 1){
		q("update siniestro set observaciones=concat(observaciones,\"\n$Nusuario [$Ahora] Asigna Servicio\"), 
		ubicacion=$IDU3,estado=8,fecha_inicial='$Hoy', fecha_final='$Fec_entrega',causal=0,subcausal=0 
		where id=$Cita->siniestro");
		}else{
		q("update siniestro set observaciones=concat(observaciones,\"\n$Nusuario [$Ahora] Asigna Servicio\"), 
		ubicacion=$IDU3,estado=7,fecha_inicial='$Hoy', fecha_final='$Fec_entrega',causal=0,subcausal=0 
		where id=$Cita->siniestro");
		}
		// Inserta la bitacora del siniestro
		graba_bitacora('siniestro','M',$Cita->siniestro,"Asigna Servicio");
		
		
	}
	
	// //////////////////                                   *******************                 CAMBIO DE ESTADO AUTOMATICO A SERVICIO                 ************************    ///////////////////////////
	if($Diferencia>$Oficina->km_domicilio)
	{
		// si la distancia de domicilio supera la maxima permitida envia un correo al director operativo informando del suceso
		$Operario=qo1("select concat(apellido,' ',nombre) from operario where id='$Cita->operario_domicilio' ");
		
		$mensaje = "<body>Ocurrio un exceso en desplazamiento en domicilio<br><br> ".
		"Placa: $Cita->placa <br>Oficina: $Oficina->nombre<br>Fecha y hora de la cita: $Cita->fecha $Cita->hora<br>".
		"Numero Siniestro: $Sin->numero $Sin->asegurado_nombre <br>".
		"Auxiliar Operativo: $Operario.<br>Kilometraje en exceso: ".coma_format($Diferencia)."</body>";
		
		$data_mail = array(
			"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
			"enviarEmail" => "true",
			"para" => "sergiourbina@aoacolombia.com",
			"copia" => "sergiourbina@aoacolombia.com",
			"asunto" => "Exceso desplazamiento en domicilio $Cita->placa $Oficina->nombre",
			"mensaje" => "$mensaje"
			);
			
			$ch = curl_init();
		    
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/enviarWpApp.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
            curl_exec($ch);
			curl_close($ch);
		
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	/// DEBE GENERARSE UN ESTADO INCONCLUSO PARA PODER TOMAR LAS FOTOGRAFIAS. EN EL ESTADO DE LA CITA, PODER MOSTRARLA Y CREAR LA CAPTURA DE LAS FOTOS.
	// lo mas rapido es usar un ckeck adicional que tambien sea usado en el filtro de citas que se muestran en el módulo Citas de esta aplicacion.

	// Cuando las fotos sean debidamente tomadas, debe pasarse a la firma del acta en pdf y guardarla. En ese momento se cierra el caso.
	 
	
	if($Sin->aseguradora == 59 and $Sin->venta_directa == 1){
	q("update cita_servicio set estado='C',estadod='C',fec_devolucion='$Fec_entrega',hora_devol='$Cita->hora',
	momento_entrega='$Ahora',hora_llegada='$Hora',entrega_fase1=1 where id=$Cita->id");	
	}else{
	q("update cita_servicio set estado='C',estadod='P',fec_devolucion='$Fec_entrega',hora_devol='$Cita->hora',
	momento_entrega='$Ahora',hora_llegada='$Hora',entrega_fase1=1 where id=$Cita->id");
	}
	
	graba_bitacora('cita_servicio','M',$idcita,'Efectua entrega servicio desde APP movil (Fase 1)');
	
	
	
    ///Sacar ultimo kilometraje
	
	   $idSiniestro = $Sin->id;
	   
	   $ruta = directorio_imagen('siniestro',$idSiniestro);
	   /*Odometro de al momento de la entrega*/
	   if($this->request['img_odo_salida_f']){
		   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_odo_salida_f']));
		   $filepath = $ruta."$idSiniestro"."_odometro_entrega_"."$hoy2".".png";
		   file_put_contents($filepath,$data);
		   q("update siniestro set img_odo_salida_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   
	   /*Carro frontal*/
	   if($this->request['fotovh1_f']){
		   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['fotovh1_f']));
		   $filepath = $ruta."$idSiniestro"."_frontal_ve_entrega_"."$hoy2".".png";
		   file_put_contents($filepath,$data);
		   q("update siniestro set fotovh1_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   
	   /*Carro lateral izquierdo*/
	   if($this->request['fotovh2_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['fotovh2_f']));
       $filepath = $ruta."$idSiniestro"."_lateral_izquierdo_entrega_"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set fotovh2_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   /*Carro lateral derecho*/
	   if($this->request['fotovh3_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['fotovh3_f']));
       $filepath = $ruta."$idSiniestro"."_lateral_derecho_entrega_"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set fotovh3_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   
	   /*Carro posterior*/
	   if($this->request['fotovh4_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['fotovh4_f']));
       $filepath = $ruta."$idSiniestro"."_posterior_ve_entregra_"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set fotovh4_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   
	   /*Imagen adicional*/
	   if($this->request['eadicional1_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['eadicional1_f']));
       $filepath = $ruta."$idSiniestro"."_adicional_1_"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set eadicional1_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   
	   /*Imagen adicional 2*/
	   if($this->request['eadicional2_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['eadicional2_f']));
       $filepath = $ruta."$idSiniestro"."_adicional_2_"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set eadicional2_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   /*Imagen de contrato*/
	   if($this->request['img_contrato_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_contrato_f']));
       $filepath = $ruta."$idSiniestro"."_img_contrato_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set img_contrato_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   /*Chequeo antes de marcha*/
	   if($this->request['congelamiento_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['congelamiento_f']));
       $filepath = $ruta."$idSiniestro"."_congelamiento_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set congelamiento_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   /*Autorizacion centrales de riesgo*/
	   if($this->request['gastosf_f']){
		 $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['gastosf_f']));
       $filepath = $ruta."$idSiniestro"."_congelamiento_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set gastosf_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   /*Cedula delantera*/
	   if($this->request['img_cedula_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_cedula_f']));
       $filepath = $ruta."$idSiniestro"."_img_cedula_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set img_cedula_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   /*Cedula reverso*/
	   if($this->request['img_pase_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_pase_f']));
       $filepath = $ruta."$idSiniestro"."_img_pase_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set img_pase_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   /*Licencia delantera*/
	   if($this->request['adicional1_f']){
		$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['adicional1_f']));
       $filepath = $ruta."$idSiniestro"."_adicional1_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set adicional1_f = '$filepath' where id=$idSiniestro");   
	   }
	   
	   
	   /*Licencia reverso*/
	   if($this->request['adicional2_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['adicional2_f']));
       $filepath = $ruta."$idSiniestro"."_adicional2_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set adicional2_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   /*Cedula frente garantia tercero*/
	   if($this->request['adicional3_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['adicional3_f']));
       $filepath = $ruta."$idSiniestro"."_adicional3_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set adicional3_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   /*Cedula reverso garantia tercero*/
	   if($this->request['adicional4_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['adicional4_f']));
       $filepath = $ruta."$idSiniestro"."_adicional4_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set adicional4_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   /*Carta de autorizacion*/
	   if($this->request['img_carta_autorizacion_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_carta_autorizacion_f']));
       $filepath = $ruta."$idSiniestro"."_img_carta_autorizacion_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set img_carta_autorizacion_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   /*Fotocopia de poliza*/
	   if($this->request['img_fotocopia_poliza_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_fotocopia_poliza_f']));
       $filepath = $ruta."$idSiniestro"."_img_fotocopia_poliza_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set img_fotocopia_poliza_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   
	   /*Imagen camara de comercio*/
	   if($this->request['img_camara_comercio_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_camara_comercio_f']));
       $filepath = $ruta."$idSiniestro"."_img_camara_comercio_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set img_camara_comercio_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   
	   /*Imagen Orden ingreso taller*/
	   if($this->request['img_orden_ingreso_taller_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_orden_ingreso_taller_f']));
       $filepath = $ruta."$idSiniestro"."_img_orden_ingreso_taller_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set img_orden_ingreso_taller_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   /*Inventario de momento de la entrega*/
	   if($this->request['img_inv_salida_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_inv_salida_f']));
       $filepath = $ruta."$idSiniestro"."_img_inv_salida_f"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set img_inv_salida_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   q("UPDATE siniestro SET pasoApp = 1 WHERE id = $Cita->siniestro");
	   
	   }else{
	   return array("estado"=>6,"desc"=>"Esta cita ya paso por App, No podemos enviarla otra ves!");
	   exit();  
	   }
	  try{
		  $mensaje = "Imagenes subidas con exito!";
		   $response = array("estado" => 1,"ruta" => $mensaje);
			
			return  $response;
	   }catch(Exception $e){
		   return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
	   }
	   
}

public function subir_img_devolucion(){
	
	  $arrayFilter = array("img_odo_entrada_f" => "img_odo_entrada_f", 
	   "fotovh5_f" => "fotovh5_f","fotovh6_f" => "fotovh6_f","fotovh7_f" => "fotovh7_f","fotovh8_f" => "fotovh8_f"
	   
	   );
	   
	   if($this->check_request($arrayFilter)>0){
			return array("estado" => 2, "desc" => "Faltan datos para progresar la solicitud", "parametros"=>$this->left_params);
		}
	  /*Request solicitados*/
	   
	  
	   $idCita = $this->request['idCita'];
	   
	   $observacionesd = $this->request['observacionesd'];
	   
	   $observacionesUltimoEstado  = $this->request['obsUltimoEstado'];
	   
	   $observacionesfs = $this->request['observacionesfs']; //Observaciones fuera de servicio
	   
	   //$Siniestro_propio = $this->request['Siniestro_propio']; //checkbox y es on si  hay dato y sino puede ir vacio
	   
	   $kmf = $this->request['kmdevolucion'];
	   
	   $Nuevo_estadod = $this->request['Nuevo_estadod'];
	   
	   
	   $kmd = $this->request['kilometrajeAlTerminarEldesplasamientoDomicilio'];
	   
	   $operario_domiciliod = $this->request['operario_domiciliod'];
	   
	   
	   /*Request solicitados*/
	   
	   
	   
	   $D=qo("select * from cita_servicio where id=$idCita");
	   
	   $idSiniestro = $D->siniestro;
	   
	   $SiniValidar = qo("SELECT  pasoAppDevo FROM siniestro WHERE id = $idSiniestro");
	   
	   
	   if($SiniValidar->pasoAppDevo != 1){
		   
	   
	   $Oficina=qo("select * from oficina where id=$D->oficina"); // trae los datos de la oficina
	   
	   $Fecha = date('Y-m-d');
	   $Dias_servicio=dias($Fecha,$D->fecha); // recalcula los dias reales de servicio
	   $Hora = date('H:i:s');
	  
	  /*Update  cita_servicio de devolucion*/
	  q("update cita_servicio set estadod='C',hora_devol_real='$Hora',fec_devolucion='$Fecha',obs_devolucion=concat(obs_devolucion,\"$observacionesd\"),dias_servicio=$Dias_servicio,devolucion_fase1=1 where id=$idCita");
	  graba_bitacora('cita_servicio','M',$idCita,'Efectua entrega servicio desde APP movil (Fase 1)'); 
	  /*Fin Update  cita_servicio de devolucion*/
	  
	  /*Actualiza siniestro*/
	  
	  //$Siniestro_propio=sino($Siniestro_propio);
	  q("update siniestro set fecha_final='$Fecha', estado=8,obsconclusion=\"$observacionesd $observacionesfs\" where id=$D->siniestro");
	  graba_bitacora('siniestro','M',$D->siniestro,'Fecha final,estado,obsconclusion');
	  
	  
	  /*Actualiza la ubicacion*/
      /*Consultas de listar selects las funciones qo*/
	  $Sincars = qo("select * from siniestro where id=$D->siniestro");
	  $Ubicacion =  qo("select * from ubicacion where id=$Sincars->ubicacion");
	  $Aseg=qo("select * from aseguradora where id=$Sincars->aseguradora"); // trae los datos de la aseguradora
	  $idv = qo("select id from vehiculo where placa='$D->placa'"); // obtiene el id del vehiculo
	  $aseguradora = qo("select id from aseguradora where id = $D->flota");
	  /*Consultas de listar selects*/
	  
	  
	  
	  
	  $ultimokm = $Ubicacion->odometro_inicial;
	  
	  /*Variables inicializadas*/
	  
	  $Consumo = ($kmf - $Ubicacion->odometro_inicial);
	  
	  $kmDiferencia = ($kmf - $Ubicacion->odometro_inicial);
	  
	  
	  
	  
	  
	  
	  /*Fin variables inicializadas*/
	  
	  
	  if($kmd){
		  $kmFinalServicio = $kmd;
	  }else{
		  $kmFinalServicio = $kmf;
	  }
	   
	   
	  q("update ubicacion set fecha_final='$Fecha', odometro_inicial='$Ubicacion->odometro_inicial', odometro_final='$kmFinalServicio', 
	     odometro_diferencia=$kmDiferencia,obs_mantenimiento=\"$observacionesd\",
	     observaciones=\"$observacionesfs\",estado=7 where id=$Ubicacion->id");
	  graba_bitacora('ubicacion','M',$Ubicacion->id,'Concluye el servicio');
	  /*Fin Actualiza la ubicacion*/
	  
	  $lkm = $Aseg->limite_kilometraje;
	  
	  
	  if($Consumo > $lkm){
		 if($lkm != 0){
		  $limit = 1500;
		  if($lkm >= $limit){
			  
		$mensaje = 
		"<body>Ocurrio un exceso en consumo de kilometraje en el servicio<br><br> ".
		"Placa: $D->placa <br>Oficina: $Oficina->nombre<br>Fecha y hora de la cita: $D->fecha $D->hora<br>".
		"Numero Siniestro: $Sincars->numero $Sincars->asegurado_nombre <br>".
		"Kilometraje maximo permitido:  $Aseg->limite_kilometraje.  Kilometraje consumido: ".coma_format($Consumo)." </body>";
		
			  $data_mail = array(
			"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
			"enviarEmail" => "true",
			"para" => "sergiourbina@aoacolombia.com",
			"copia" => "sergiocastillo@aoacolombia.com",
			"asunto" => "Exceso consumo de kilometraje en servicio $D->placa $Oficina->nombre",
			"mensaje" => "$mensaje"
			);
			
			$ch = curl_init();
		    
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/enviarWpApp.php');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
            curl_exec($ch);
			curl_close($ch);
			  
			  
			  
		  }
		  
	  } 
}

	$operario_domiciliodQuery = qo("SELECT operario_domiciliod FROM cita_servicio WHERE id = $idCita");
		
		
		if(!$operario_domiciliodQuery->operario_domiciliod){
			q("UPDATE cita_servicio SET operario_domiciliod = $operario_domiciliod WHERE id = $idCita");
		}
/////  ACTUALIZA LAS UBICACIONES POSTERIORES A ESTE CIERRE
	  
	  q("update ubicacion set odometro_inicial='$Ubicacion->odometro_inicial', odometro_final='$kmf', odometro_diferencia=0 where vehiculo='$idv->id' and fecha_inicial>='$Fecha'");
	  
	  
	  if($kmd) // si hay recorrido de domicilio
	   {
		
		$Diferencia=($kmf-$kmd); // calcula la distancia recorrida para el domicilio
		// inserta el registro en ubicacion
		$IDU2 =q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) 
		values ('$D->oficina','$idv->id','$Fecha','$Fecha','96','$kmd','$kmf','$Diferencia',\"Domicilio de entrega $D->dir_domicilio\",'$Sincars->aseguradora')");
		graba_bitacora('ubicacion','A',$IDU2,'Adiciona domicilio automáticamente');
	   }
	  
	   
	  
	  if($observacionesfs || $Nuevo_estadod==5 ) // /   si hay orden de servicio significa que el vehiculo pasa a fuera de servicio o mantenimiento programado o alistamiento  por arreglos en taller.
	 {
		if($Nuevo_estadod==5 /*fuera de servicio*/)
		{
			// inserta una ubicacion de fuera de servicio
			$UB1 = qo("insert into ubicacion (oficina,vehiculo,estado,fecha_inicial,fecha_final,odometro_inicial,odometro_final,observaciones,flota,siniestro_propio) values
				('$D->oficina','$idv->id','$Nuevo_estadod','$Fecha','$Fecha','$Ubicacion->odometro_inicial','$kmf',\"$observacionesfs\",$D->flota,'$Siniestro_propio')");
			graba_bitacora('ubicacion','A',$UB1,'Adiciona registro');
			/*if($Siniestro_propio) qo("update siniestro set siniestro_propio=1 where id=$D->siniestro"); // si el siniestro es propio marca en la tabla de siniestros*/
		}
		else
		{
			$UB1 = qo("insert into ubicacion (oficina,vehiculo,estado,fecha_inicial,fecha_final,odometro_inicial,odometro_final,observaciones,flota,siniestro_propio) values
				('$D->oficina','$idv->id','$Nuevo_estadod','$Fecha','$Fecha','$Ubicacion->odometro_inicial','$kmf',\"$observacionesfs\",$D->flota,'$Siniestro_propio')");
			graba_bitacora('ubicacion','A',$UB1,'Adiciona registro');
		}
		graba_bitacora('ubicacion','A',$UB1,'',$LINK); // graba la bitacora de la ubicación
	}else{
		
		$diferenciaKmEntrega = ($kmf - $kmf);
		// inserta un nuevo estado ya sea de alistamiento o de mantenimiento preventivo
		$UB1 = q("insert into ubicacion (oficina,vehiculo,estado,obs_mantenimiento,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,flota) values
				('$D->oficina','$idv->id','$Nuevo_estadod','$observacionesUltimoEstado','$Fecha','$Fecha','$kmf','$kmf','$diferenciaKmEntrega',$D->flota)",$LINK);
		graba_bitacora('ubicacion','A',$UB1,'Adiciona registro');
		
	}
	
	
	$Sin = qo("select * from siniestro where id=$D->siniestro"); // trae los datos del siniestro
	$extencion =  substr($Sin->numero, 0, 9);
	$extra =  substr($Sin->numero, 0, 5);
	if($Sin->vh_remplazo != 1 and  $extencion !='EXTENSION' and $extra != 'EXTRA'){
		
	if($aseguradora->id == 1 || $aseguradora->id == 8 || $aseguradora->id == 9){
		
			//$Sincars = qo("select * from siniestro where id= $D->siniestro");
			//print_r($Sincars);
			$clienteSiniestro = qo("Select * from cliente where identificacion = '".$Sin->asegurado_id."'");
			$rand = rand(10,100);
			$rand2 = rand(10,100);
			$documento =   $Sin->asegurado_id;
			$horaMail1 = date("s");
			$typeMail = "30";
			$validationMail = $horaMail1.$typeMail;
			date_default_timezone_set('Etc/GMT-5');
			$data_to_send = array(
				"recipient_type" => "email",
				"shop_id" => 130307,
				"password" => "c0b325ccddeb3ac7a57bc5b77",
				"first_name" => "$Sin->asegurado_nombre",
				"last_name" => " ",
				"email" => "$Sin->declarante_email",
				"transaction_id" => md5($documento).$validationMail.$rand,
				"transaction_time" => date("Y-m-d h:m:s"),
				"has_products"=>0,
				"products_info"=>"",
				"telephone"=>"+57".$Sin->declarante_celular,
				"sender_email"=>"Allianz",
				"products_other"=>"",
				"project_name"=>"",
				"days_of_deletion"=>15,
				//"meta_data": "{ \"this\":\"Sergio\", \"something\":\"Urbina\", \"blub\":\"bla\" }",
				"meta_data"=> "{ \"numeroSiniestro\":\"$Sin->numero\", \"placaAsegurado\":\"$Sin->placa\", 
				\"documentoAsegurado\":\"$Sin->asegurado_id\" , \"asd\":\"Gracias por visitarnos\" ,
				\"first_name\":\"$Sin->asegurado_nombre\" , \"last_name\":\" \" }",
				"client_id"=> $Sin->asegurado_id,
				"screen_name"=> "$Sin->asegurado_nombre",
				"is_afnor"=> "false"
			);
			
			$ch = curl_init();
		    
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_URL, 'https://srr.ekomi.com/add-recipient');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_to_send);
            $request = curl_exec($ch);
            
			
			
			$fechaMail = date("Y-m-d");
			$horaMail = date("H:i:s");
			$usuarioMail = $_SESSION[USER]->Nombre;
			$responseMail = json_encode($request).$Sin->declarante_email;
			$varTest1 = json_encode($data_to_send);
			$varImprimirDeatlle = $responseMail.$varTest1;
			
			
			
			
			$sqlMail = "INSERT INTO seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) VALUES ($D->siniestro,'$fechaMail','$horaMail','$usuarioMail','$varImprimirDeatlle',30)";
			
			qo($sqlMail);
			
			
			$horaSms1 = date("s");
			$typeSms = "31";
			$validationSms = $horaSms1.$typeSms;
			$data_to_send2 = array(
				"recipient_type" => "sms",
				"shop_id" => 130307,
				"password" => "c0b325ccddeb3ac7a57bc5b77",
				"first_name" => "$Sin->asegurado_nombre",
				"last_name" => " ",
				"email" => "$Sin->declarante_email",
				"transaction_id" => md5($documento).$validationSms.$rand2,
				//"transaction_id" => md5($clienteSiniestro->id).md5("Y-m-d h:m:s"),
				"transaction_time" => date("Y-m-d h:m:s"),
				"has_products"=>0,
				"products_info"=>"",
				"telephone"=>"+57"."$Sin->declarante_celular",
				"sender_email"=>"Allianz",
				"products_other"=>"",
				"project_name"=>"",
				"days_of_deletion"=>15,
				"meta_data"=> "{ \"numeroSiniestro\":\"$Sin->numero\", \"placaAsegurado\":\"$Sin->placa\", \"documentoAsegurado\":\"$Sin->asegurado_id\" , \"asd\":\"Gracias por visitarnos\" , \"first_name\":\"$Sin->asegurado_nombre\" , \"last_name\":\" \" }",
				"client_id"=> $Sin->asegurado_id,
				"screen_name"=> $Sin->asegurado_nombre,
				"is_afnor"=> "false"
			);
			
			
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_URL, 'https://srr.ekomi.com/add-recipient');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_to_send2);
            $request2 = curl_exec($ch);
            curl_close($ch);
			
			
			$fechaSms = date("Y-m-d");
			$horaSms = date("H:i:s");
			$usuarioSms = $_SESSION[USER]->Nombre;
			$responseSms = json_encode($request2).$Sin->declarante_celular;
			$varTest2 =  json_encode($data_to_send2);
			$varTestImprimir = $responseSms.$varTest2;
			
			
			$sqlSms = "INSERT INTO seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) VALUES ($D->siniestro,'$fechaSms','$horaSms','$usuarioSms','$varTestImprimir',31)";
			qo($sqlSms);
			
		}
		
}
		
		
		
		/*Inicio de cargue de imagenes*/
	   $ruta = directorio_imagen('siniestro',$idSiniestro);
	   $hoy = date("YmdHis");
	   
	   if($this->request['img_odo_entrada_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_odo_entrada_f']));
       $filepath = $ruta."$idSiniestro"."_odometro_devolucion_"."$hoy".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set img_odo_entrada_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   if($this->request['fotovh5_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['fotovh5_f']));
       $filepath = $ruta."$idSiniestro"."_frontal_entreg_"."$hoy".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set fotovh5_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   if($this->request['fotovh6_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['fotovh6_f']));
       $filepath = $ruta."$idSiniestro"."_lateral_izquierdo_devolucion_"."$hoy".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set fotovh6_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   if($this->request['fotovh7_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['fotovh7_f']));
       $filepath = $ruta."$idSiniestro"."_lateral_derecho_devolucion_"."$hoy".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set fotovh7_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   if($this->request['fotovh8_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['fotovh8_f']));
       $filepath = $ruta."$idSiniestro"."_posterior_devolucion_"."$hoy".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set fotovh8_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   if($this->request['fotovh9_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['fotovh9_f']));
       $filepath = directorio_imagen('siniestro',$idSiniestro)."$idSiniestro"."_adicional_devolucion_"."$hoy".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set fotovh9_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   if($this->request['dadicional3_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['dadicional3_f']));
       $filepath = $ruta."$idSiniestro"."_adicional_devolucion2_"."$hoy".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set dadicional3_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   if($this->request['dadicional4_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['dadicional4_f']));
       $filepath = $ruta."$idSiniestro"."_adicional_devolucion_3_"."$hoy".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set dadicional4_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   if($this->request['img_inv_entrada_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_inv_entrada_f']));
       $filepath = $ruta."$idSiniestro"."_img_inv_entrada_f_"."$hoy".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set img_inv_entrada_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   if($this->request['fotovh9_f']){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['fotovh9_f']));
       $filepath = $ruta."$idSiniestro"."_fotovh9_f_"."$hoy".".png";
	   file_put_contents($filepath,$data);
	   q("update siniestro set fotovh9_f = '$filepath' where id=$idSiniestro");  
	   }
	   
	   
	   }else{
		  return array("estado" => 6,"mensaje" => "No se puede enviar dos veses en la App!"); 
		  exit();
	   }
	   
	   /*Fin de cargue de imagenes*/
	   
	try{
		  return array("estado" => 1,"mensaje" => "Imagenes subidas con exito!");
			
		}catch(Exception $e){
		   return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
	   }
}
	
	
	public function validar_arribo(){
		
		$arrayFilter = array("idCita" => "idCita");
		
		if($this->check_request($arrayFilter)>0){
			return array("estado" => 2, "desc" => "Faltan datos para progresar la solicitud", "parametros"=>$this->left_params);
		}
		
		$Ahora=date('Y-m-d H:i:s');
		
		$idCita = $this->request['idCita'];
		
		$apellido = $this->request['apellido'];
		
		$nombre = $this->request['nombre'];
		
		$identificacion = $this->request['identificacion'];
		
		$NUSUARIO = $this->request['nusuario'];
		
		
		
		
		
		
		q("update cita_servicio set arribo='$Ahora' where id=$idCita");
		
		graba_bitacora("cita_servicio","M",$idCita,"Marca arribo asegurado");  // graba la bitacora de citas
		
		if($idCita){
		$idSiniestro = qo1("select siniestro from cita_servicio where id=$idCita"); // trae informacion de la cita
	    }
		
	   $ruta = directorio_imagen_dos('ingreso_recepcion',$idSiniestro);
	   $rutaDos = str_replace("../../Administrativo/", "", $ruta);
	   
	   $hoy2 = date("YmdHis");
	   $filepath_dos =  $rutaDos."$idSiniestro"."_arribo_recesion_"."$hoy2".".png";
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_arribo']));
       $filepath = $ruta."$idSiniestro"."_arribo_recesion_"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   
	   $descripcion = "Arribo desde App Movil..";
	   
	   q("insert into aoacol_administra.ingreso_recepcion (apellido, nombre,descripcion,identificacion,fecha,registrado_por,cita,siniestro,foto_f) values
		 ('$apellido','$nombre',\"$descripcion\",'$identificacion','$Ahora','$NUSUARIO','$idCita','$idSiniestro','$filepath_dos')");
		
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_arribo_tumb']));
       $filepath = $ruta."tumb_"."$idSiniestro"."_arribo_recesion_"."$hoy2".".png";
	   file_put_contents($filepath,$data);
	   
	   graba_bitacora("cita_servicio","M",$idCita,"Guarda imagen de arribo");
	
	try{
		  return array("estado" => 1,"mensaje" => "Arribo guardado con exito");
			
		}catch(Exception $e){
		   return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
	   }
	
	} 
	
	
	public function acta_de_entrega_cita(){
		
		$idc = $this->request['idCita'];
		
		$validarTablaPdf = $this->request['entregaODevolucion'];
		
		$fechaDevolucion = qo("select fec_devolucion, hora_devol from cita_servicio where id = $idc");
		$Fdevol = $fechaDevolucion->fec_devolucion;
		$Hdevol = $fechaDevolucion->hora_devol;
		$NUSUARIO = $this->request['nusuario'];
		
		
	$A_tarjeta=array();
	$A_reembolsable=array();
	$Cita=qo("select * from cita_servicio where id=$idc"); // trae los datos de la cita
	$Fec_entrega = date('Y-m-d',strtotime(aumentadias($Cita->fecha, $Cita->dias_servicio))).' '.$Cita->hora; // calcula la fecha de devolucion
	if(!$Fdevol){
      
	  return array("estado" => 1,"mensaje" => "ESTIMADO (A) USUARIO (A) ".$NUSUARIO." Tenga en cuenta que si en el momento de la devolución del automovil hay restricción vehicular, debe modificar la entrega a la siguiente hora hábil la cual no tenga restricción según las leyes y normas vigentes en su ciudad. De lo contrario continúe con la impresión del acta sin hacer ninguna modificación. Gracias.");
	  exit();
	}
	//echo "try";
	//exit;
	$Fec_entrega=$Fdevol.' '.$Hdevol;
	$Siniestro=qo("select * from siniestro where id=$Cita->siniestro"); // trae los datos del siniestro
	$Aseguradora=qo("select * from aseguradora where id=$Siniestro->aseguradora"); // trae los datos de la aseguradora
	$Oficina=qo("select * from oficina where id=$Cita->oficina"); // trae los datos de la oficina
	$Vehiculo=qo("select * from vehiculo where placa='$Cita->placa' "); // trae los datos del vehiculo
	$ubicaciones1=qo("select max(id) as id from ubicacion where vehiculo=$Vehiculo->id  "); // trae los datos ubicaciones1
	$ubicaciones2=qo("select odometro_final from ubicacion where id=$ubicaciones1->id "); // trae los datos ubicaciones2
	$kilome = $ubicaciones2->odometro_final;
	$Linea=qo("select * from linea_vehiculo where id=$Vehiculo->linea"); // trae los datos de la linea del vehiculo
	
	$Autorizado_nombre='';$Autorizado_id=0;$Autortizado_direccion='';$Autorizado_celular='';$Autorizado_email='';
	$TG='';$TGV='';
	
	$Sin_autor=qo("select * from sin_autor where siniestro=$Siniestro->id");
	$newFunctin = new WebServicesEventoApp;
	if($Autorizacion=q("select a.*,f.nombre as nfranq,f.tipo as tf from sin_autor a,franquisia_tarjeta f  where a.siniestro=$Siniestro->id and a.estado='A' and a.franquicia=f.id ")) // trae los datos de la autorizacion
	{
		$Autorizaciones='';
		$Contador=1;
		$TH='Tarjeta Habiente(s): ';
		while($A=mysql_fetch_object($Autorizacion))
		{
			if($A->data)
			{
				$Rd=$newFunctin->desencripta_data($A->id); // desencripta los datos para imprimirlos en el acta
				$A->identificacion=$Rd['identificacion'];
				$A->numero=$Rd['numero'];
				$A->nbanco=$Rd['banco'];
				$A->vencimiento_mes=$Rd['vencimiento_mes'];
				$A->vencimiento_ano=$Rd['vencimiento_ano'];
				$A->num_autorizacion=$Rd['num_autorizacion'];
				$A->funcionario=$Rd['funcionario'];
				$A->codigo_seguridad=$Rd['codigo_seguridad'];
			}
			
			
			if($A->tf=='C' /* tarjeta de credito */) $A_tarjeta[]=new d_tarjeta($A); // si hay varias autorizaciones, las acumula en un arreglo
			if($A->tf=='E' || $A->tf=='D') $A_reembolsable[]=new d_efectivo($A); // acumula las garantias reembolsables en efectivo
			$Autorizaciones.="Aut $Contador: $A->nombre id:$A->identificacion $A->nfranq ".r($A->numero,4)." # $A->num_autorizacion Vence: $A->vencimiento_mes-$A->vencimiento_ano. ";
			$TG.=($TG?", ":"").$A->nfranq;$TGV.=($TGV?", ":"").$A->numero_voucher;
			$TH.="$A->nombre /";
			if(!$Autorizado_nombre)
			{
				if($Cliente=qo("select * from cliente where identificacion=$A->identificacion")) // trae los datos del cliente
				{
					$Autorizado_nombre=$Cliente->nombre.' '.$Cliente->apellido;$Autorizado_id=$A->identificacion;
					$Autorizado_direccion=$Cliente->direccion;$Autorizado_celular=$Cliente->celular;$Autorizado_email=$Cliente->email_e;
				}
			}
		}
	}
	
	   $ruta = directorio_imagen('siniestro',$idSiniestro);
	   $hoy = date("YmdHis");
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->request['img_odo_entrada_f']));
       $filepath = $ruta."$idSiniestro"."_odometro_devolucion_"."$hoy".".png";
	   file_put_contents($filepath,$data);
	
	include('inc/pdf/fpdf.php'); // incluye la clase pdf
	
	include('pdfWebServicesApp/pdfApp.php'); // incluye la clase pdf
	
	if($validarTablaPdf ==  1){
		$VarNameFile = "Acta de entrega";
		$asuntoCorreo = "Acta de entrega";
		$mensaje = "Enviamos su acta de entrega por correo";
	}else{
		$VarNameFile = "Enviamos su acta de devolucion por correo";
		$mensaje = "Enviamos su acta de devolucion por correo";
		$asuntoCorreo = "Acta de devolucion";
	}
	
	
			
			$data_mail = array(
			"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
			"enviarEmail" => "true",
			"para" => "sergiourbina@aoacolombia.com",
			"copia" => "sergiocastillo@aoacolombia.com",
			"asunto" => $asuntoCorreo,
			"mensaje" => $mensaje,
			"archivo" => $ArchivoPdf,
			"nameArchivo" => $VarNameFile
			);
			
			$ch = curl_init();
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/enviarWpApp.php');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
            curl_exec($ch);
			curl_close($ch);
			
	$Subdirectorio=substr(str_pad($idc,6,'0',STR_PAD_LEFT),0,3);
	
	$dataDirec = 'lineavehiculo/vhActaEntrega/'.$Subdirectorio.'/'.$idc;
	
	
	$newFunctin = new WebServicesEventoApp;
	
	
	if($validarTablaPdf !== 1){
		$newFunctin->deleteDir($dataDirec);
	}
	
   try{
		  return array("estado" => 1,"mensaje" => "Envio exito de acta de entrega");
			
		}catch(Exception $e){
		   return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
	   }

	
		
	}
	
	
	public function ultimoKmVh(){
		try{
		$placa  = $this->request['placa'];	
		
		
		$sql = "select id from vehiculo where placa = '$placa'";
		
		$idVh =  qo($sql);
		
		$sql = "SELECT odometro_final FROM ubicacion WHERE vehiculo = $idVh->id ORDER BY id DESC LIMIT 1";
		
		
		$odmetroInicial = qo($sql);
		
		return array("estado" => 1,"odometro_final" => $odmetroInicial->odometro_final);
		
		
		}catch(Exception $e){
			return array("estado"=>0,"desc"=>"Ocurrio un error inesperado");
		}
		
		
		
	}
	/*Metodo de recursividad: Elimino primero todos los archivos dentro con unlink y luego con rmdir la carpeta*/
	public static function deleteDir($dirPath) {
       if (! is_dir($dirPath)) {
           throw new InvalidArgumentException("$dirPath must be a directory");
       }
       if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
           $dirPath .= '/';
       }
       $files = glob($dirPath . '*', GLOB_MARK);
       foreach ($files as $file) {
           if (is_dir($file)) {
               self::deleteDir($file);
           } else {
               unlink($file);
           }
       }
       rmdir($dirPath);
}
	
	public function desencripta_data($id) // desencripta datos de una garantía para mostrar en el acta de entrega/devolucion.
    {
	$D=qo("select * from sin_autor where id=$id");
	require_once('inc/Crypt.php');
	$C = new Crypt();
	$C->Mode = Crypt::MODE_HEX;
	$C->Key  = '!'.$D->id.'+';
	$Datos=$C->decrypt($D->data);
	$DR=explode('|',$Datos);
	$R['identificacion']=$DR[0];
	$R['numero']=$DR[1];
	$R['banco']=$DR[2];
	$R['vencimiento_mes']=$DR[3];
	$R['vencimiento_ano']=$DR[4];
	$R['num_autorizacion']=$DR[5];
	$R['funcionario']=$DR[6];
	$R['codigo_seguridad']=$DR[7];
	return $R;
   }
	
	
	
	public function check_request($content){
			$counter = 0;
			foreach($content as $key => $var)
			{
				if($this->request[$key]==null)
				{
					//echo "falta ".$var.",";
					$this->left_params .= $key.",";
					$counter++;
				}
			}

			return $counter;

		}
		
		private function fetch_objects_test($query)
		{
			$result = q($query);
		
			$rows = array();
			if($result != null)
			{
				while ($row = mysql_fetch_object($result))
				{
					array_push($rows, $row);
				}
				
				return $rows;
			}
			else
			{
				return null;	
			}			
		}
	
	
}

/*Gestor de funcionalidades*/



?>