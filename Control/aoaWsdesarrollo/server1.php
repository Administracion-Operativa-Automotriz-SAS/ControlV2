<?php
// Incorporar la biblioteca nusoap al script, incluyendo nusoap.php (ver imagen de estructura de archivos)
require_once('includes/nusoap.php');
// incorporar la biblioteca de funciones generales de la aplicacion
include_once('inc/funciones_.php');
// Modificar la siguiente linea con la direccion en la cual se aloja este script
$miURL = 'http://app.aoacolombia.com/Control/aoaWsdesarrollo';
// Crea la instancia de un nuevo Web Service
$Servidor_ws = new soap_server();
$Servidor_ws->configureWSDL('WebServiceAOAdesarrollo', $miURL);
$Servidor_ws->wsdl->schemaTargetNamespace=$miURL;

// Registra la primera rutina ObtenerToken
$Servidor_ws->register('ObtenerToken',array('usuario'=>'xsd:string','clave' =>'xsd:string'),array('return'=>'xsd:string'),$miURL);

function ObtenerToken($usuario,$clave)
{
	$clave=e($clave); // encripta la clave que llega para compararla con el registro del usuario y validar si tiene acceso.
	// instrucciones para la creacion de un token.
	$codigo_inicial='1111111111';
	$codigo_final='9999999999';
	$Longitud=strlen($codigo_inicial);
	$Codigo=round(rand($codigo_inicial,$codigo_final),0);
	$Codigo=str_pad($Codigo,$Longitud,'0',STR_PAD_LEFT);
	$Token=$Codigo;
	
	if($id=qo1("select id from usuario_ws where usuario='$usuario' and clave='$clave' "))
	{
		// si valida el usuario entonces graba el token y lo retorna como respuesta del ws.
		q("update usuario_ws set token='$Token' where id=$id");
		return new soapval('return','xsd:string',$Token);
	}
	else
		return new soapval('return','xsd:string','SIN ACCESO para el usuario: '.$usuario); // de lo contrario retorna un error de usuario
}
// Registra la función InsertarSiniestro dentro del ws.
$Servidor_ws->register('InsertarSiniestro',
						array(
							'token'=>'xsd:string', // token de validacion
							'ciudad'=>'xsd:string', // ciudad en codificacion dane
							'fecha'=>'xsd:string',  // fecha en formato aaaa-mm-dd
							'numero'=>'xsd:string', // numero de siniestro alfanumerico
							'placa'=>'xsd:string', // placa en formato XXX999
							'identificacion'=>'xsd:string', // numero sin comas ni puntos
							'poliza'=>'xsd:string', // numero de poliza alfanumerico
							'nombre'=>'xsd:string', // nombre del asegurado
							'telefono1'=>'xsd:string',  // telefono 1 puede ser fijo o celular 
							'telefono2'=>'xsd:string', // telefono 2 puede ser fijo o celular
							'telefono3'=>'xsd:string', // telefono 3 puede ser fijo o celular
							'telefono4'=>'xsd:string', // telefono 4 puede ser fijo o celular
							'emailasegurado'=>'xsd:string', // email del asegurado
							'dias'=>'xsd:string', // dias de servicio por defecto 7 para perdidas parciales 21 para perdidas totales excepto Occidente pt=7
							'analista'=>'xsd:string', // nombre del analista de Royal que guardo la informacion
							'emailanalista'=>'xsd:string', // emali del analista de Royal que guardo la informacion
							'bancooccidente'=>'xsd:string' // indicador si es banco de occidente 1 o 0 para estandar
						)
						,array('return'=>'xsd:string'),$miURL);

 function InsertarSiniestro($token,$ciudad,$fecha,$numero,$placa,$identificacion,$poliza,$nombre,$telefono1,$telefono2,$telefono3,$telefono4,$emailasegurado,$dias,$analista,$emailanalista,$bancooccidente)
 {
	$Ahora=date('Y-m-d H:i:s');
	// Valida si el token es valido, de lo contrario retorna error de token
	if(qo1("select id from usuario_ws where token='$token' "))
	{
		$codCiudad=str_pad($ciudad,8,'0',STR_PAD_RIGHT);// Ajusta la ciudad.
		$CiuOrig=$codCiudad;  // Asigna la ciudad original 
		$codCiudad=str_pad($codCiudad,8,'0',STR_PAD_LEFT);
		$Departamento=substr($codCiudad,0,2); // Halla el departamento 
		if($Ofic=qo1("select oficina from corresp_ofic where left(departamento,2)='$Departamento' ")) $Ciudad=$Ofic;else $Ciudad=$codCiudad; // obtiene la ciudad de atención
		// validacion si el siniestro existe con anterioridad
		if($Existe=qo("select id from siniestro where aseguradora=2 and numero='$numero' "))
		{
			// 
			enviar_gmail('sistemas@aoacolombia.com','Control WS-Ike Version 2 (beta)',
			'arturoquintero@aoacolombia.com,ARTURO QUINTERO;mcmdelao@ikeasistencia.com,MARTIN MARTINEZ','',
			"Ingreso de registro $token",
			nl2br("
			Control de Ingreso de información vía Web Service para Ike.
			
			Información NO ingresada:
			
			Ciudad: $ciudad
			Fecha: $fecha
			Numero de siniestro: $numero
			Placa asegurado: $placa
			Identificacion: $identificacion
			Poliza: $poliza
			Nombre: $nombre
			Telefonos: [$telefono1] [$telefono2] [$telefono3] [$telefono4]
			Email asegurado: $emailasegurado
			Dias de servicio: $dias
			Nombre Analista: $analista
			Email Analista: $emailanalista
			Banco de Occidente: $bancooccidente
			Instante de registro: $Ahora
			
			ESTE CASO NO SE PUDO INGRESAR PORQUE EL NUMERO DE SINIESTRO YA EXISTE EN NUESTRA BASE DE DATOS. 
			
			Por favor deben verificar y corregir el número de siniestro y volver a enviar la información. 
			
			Fin mensaje de control.
			"));
			return new soapval('return','xsd:string',"ERROR: NUMERO DE SINIESTRO PREEXISTENTE O INVALIDO, NO SE PUDO INSERTAR");
		}
		else
		{
			// inserta el siniestro
			$Idn=q("insert ignore into siniestro (ciudad,ciudad_original,ciudad_siniestro,fec_autorizacion,fec_siniestro,fec_declaracion,numero,aseguradora,
			placa,asegurado_id,declarante_id,poliza,asegurado_nombre,declarante_nombre,declarante_telefono,declarante_celular,declarante_tel_resid,declarante_tel_ofic,
			declarante_email,dias_servicio,intermediario,email_analista,ingreso,estado,bco_occidente) values 
			('$Ciudad','$CiuOrig','$CiuOrig','$fecha','$fecha','$Ahora','$numero','2','$placa','$identificacion','$identificacion','$poliza','$nombre','$nombre',
			'$telefono1','$telefono2','$telefono3','$telefono4','$emailasegurado','$dias','$analista','$emailanalista','$Ahora','5','$bancooccidente')");
			$Fecha=date('Y-m-d'); $Hora=date('H:i:s');  // obtiene fecha y hora actual
			// inserta el seguimiento
			q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ('$Idn','$Fecha','$Hora','W.S. IKE','Ingreso AOA',1)");
			// ENVIO DE UN CORREO DE VERIFICACION
			enviar_gmail('sistemas@aoacolombia.com','Control WS-Ike Version 2 (beta)',
			'arturoquintero@aoacolombia.com,ARTURO QUINTERO;mcmdelao@ikeasistencia.com,MARTIN MARTINEZ','',
			"Ingreso de registro $token",
			nl2br("
			Control de Ingreso de información vía Web Service para Ike.
			
			Información ingresada:
			
			Ciudad: $ciudad
			Fecha: $fecha
			Numero de siniestro: $numero
			Placa asegurado: $placa
			Identificacion: $identificacion
			Poliza: $poliza
			Nombre: $nombre
			Telefonos: [$telefono1] [$telefono2] [$telefono3] [$telefono4]
			Email asegurado: $emailasegurado
			Dias de servicio: $dias
			Nombre Analista: $analista
			Email Analista: $emailanalista
			Banco de Occidente: $bancooccidente
			Instante de registro: $Ahora
			
			Fin mensaje de control.
			"));
			// retorna el valor de exito
			return new soapval('return','xsd:string',"RECEPCION DE INFORMACION EXITOSA");
		}
	}
	else
	{
		enviar_gmail('sistemas@aoacolombia.com','Control WS-Ike Version 2 (beta)',
		'arturoquintero@aoacolombia.com,ARTURO QUINTERO','Error de acceso al WS Ike',
		"
		Error de acceso al sistema Web Service de Ike token: $token
		
		Fin de la 
		");
		return new soapval('return','xsd:string',"Token Invalido");
	}
 }
if ( !isset( $HTTP_RAW_POST_DATA ) ) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
$Servidor_ws->service($HTTP_RAW_POST_DATA);
?>