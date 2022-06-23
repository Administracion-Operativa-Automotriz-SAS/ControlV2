<?php 
/**********************
* Proyecto: Cambio de libreria mensaje de textos 
*
*
***********************/

header("Content-Type: text/plain");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/DbConnect.php");

require($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/Requests-master/library/Requests.php");


$tomorrow = date("Y-m-d", strtotime("+1 day"));

$sql = "select cita.fecha , cita.fec_devolucion, cita.hora, cita.dir_domicilio, cita.dir_domiciliod, sin.id as idsiniestro, sin.declarante_celular, sin.declarante_tel_resid, sin.declarante_tel_ofic, sin.declarante_telefono, aseg.nombre as nombreAseguradora, of.nombre as nofic, of.direccion from cita_servicio as cita inner join siniestro as sin on cita.siniestro = sin.id inner join aseguradora as aseg on sin.aseguradora = aseg.id inner join oficina as of on cita.oficina = of.id 
where (cita.fecha = '$tomorrow'  and cita.estado='P')  or (cita.fec_devolucion = '$tomorrow'  and cita.estadod='P') LIMIT 8";

$DbConnect = new DbConnect();

$citas = $DbConnect->convert_objects($DbConnect->query($sql));

$url = "http://107.20.199.106/sms/1/text/single";		
	
Requests::register_autoloader();


foreach($citas as $cita)
{
	
	$sql = "select * from seguimiento where siniestro = ".$cita->idsiniestro." and tipo in (23,24) and fecha = '".date('Y-m-d')."'";
	
	
	if(!$DbConnect->convert_object($DbConnect->query($sql)))
	{
		//print_r($cita);
	
		$fecha = $tomorrow; 
		$hora = $cita->hora;
		$direccion = $cita->direccion;
		
				
		$tipo = getTypesms($cita,$tomorrow); 
		$telefono = getPhonenumber($cita);
		
		switch($tipo)
		{
			case 1:
				$tipoSeg = 23;
				$mensaje_sms = "Recuerde su cita de entrega de vehiculo para el dia $fecha a las $hora en la $direccion. Contamos con su puntualidad. AOA se mueve contigo.";
				break;
			case 2:
				$tipoSeg = 24;
				$fecha = $cita->fec_devolucion;
				$mensaje_sms = "Recuerde su cita de devolucion de vehiculo el dia $fecha a las $hora en $direccion. Pregunte por nuestro servicio de renta. AOA se mueve contigo. Contamos con su puntualidad!";
				break;
			case 3:
				$tipoSeg = 23;
				$direccion = $cita->dir_domicilio;
				$mensaje_sms = "Recuerde su cita domiciliaria de entrega de vehiculo para el dia $fecha a las $hora en $direccion. AOA se mueve contigo. Contamos con su puntualidad!";
				break;
			case 4:
				$tipoSeg = 24;
				$fecha = $cita->fec_devolucion;
				$direccion = $cita->dir_domiciliod;
				$mensaje_sms = "Recuerde su cita domiciliaria de devolución de vehiculo el dia $fecha a las $hora en $direccion. Pregunte por nuestro servicio de renta. AOA se mueve contigo. Contamos con su puntualidad!";
				break;
			default:
				break;
		}
		
			
		
		//$telefono = "3043637333";
		$headers = array('Content-Type' => 'application/json' , "Authorization" =>'Basic c2VyZ2lvY2FzdGlsbG9AYW9hY29sb21iaWEuY29tOlNvcG9ydGVBb2EyMDE0Lg==');
		$options = array("from"=>"AOA","to"=>"57".$telefono,"text"=>$mensaje_sms);	
		 
		$request = Requests::post($url, $headers, json_encode($options));
		
		$seguimiento = array("siniestro" => $cita->idsiniestro, "fecha"=>date('Y-m-d'), "hora"=>date('H:i:s'),
			"usuario"=>"WS-VOIP", "descripcion"=>"Envio de sms","tipo"=>$tipoSeg);
		
		//$sql = $DbConnect->insert("seguimiento",$seguimiento);
		
		$DbConnect->query($sql);
			
	}	
	
	
}


function getPhonenumber($cita){
	
	$avaliableNumbers = array("declarante_celular","declarante_tel_resid","declarante_tel_ofic","declarante_telefono","declarate_tel_otro");
	foreach($avaliableNumbers as $avaliable)
	{
		$cita->$avaliable = trim($cita->$avaliable);
		
		if(strlen($cita->$avaliable) == 10 and substr($cita->$avaliable, 0, 1) === '3' and is_numeric($cita->$avaliable))
		{
			return $cita->$avaliable;
		}			
	}	
	
}

function getTypesms($cita,$tomorrow){
	
	if($cita->fecha === $tomorrow )
	{
		return 1;
	}
	
	if($cita->fec_devolucion === $tomorrow)
	{
		return 2;
	}

	if($cita->dir_domicilio)
	{
		return 3;
	}
	
	if($cita->dir_domiciliod)
	{
		return 4;
	}	
}