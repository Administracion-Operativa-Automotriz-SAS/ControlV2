<?php
// Incorporar la biblioteca nusoap al script, incluyendo nusoap.php (ver imagen de estructura de archivos)
require_once('includes/nusoap.php');
// incorporar la biblioteca de funciones generales de la aplicacion
include_once('inc/funciones_.php');
// Modificar la siguiente linea con la direccion en la cual se aloja este script
$miURL = 'http://app.aoacolombia.com/Control/aoaWs';
// Crea la instancia de un nuevo Web Service
$Servidor_ws = new soap_server();
$Servidor_ws->configureWSDL('WebServiceAOA', $miURL);
$Servidor_ws->wsdl->schemaTargetNamespace=$miURL;

// Registra la primera rutina ObtenerToken
$Servidor_ws->register('ObtenerToken',array('usuario'=>'xsd:string','clave' =>'xsd:string'),array('return'=>'xsd:string'),$miURL);

function ObtenerToken($usuario,$clave)
{
	$clave=e($clave); // encripta la clave que llega para compararla con el registro del usuario y validar si tiene acceso.
	// instrucciones para la creacion de un token. // Aen34g3Wcv
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
							'emailanalista'=>'xsd:string' // emali del analista de Royal que guardo la informacion
						)
						,array('return'=>'xsd:string'),$miURL);

 function InsertarSiniestro($token,$ciudad,$fecha,$numero,$placa,$identificacion,$poliza,$nombre,$telefono1,$telefono2,$telefono3,$telefono4,$emailasegurado,$dias,$analista,$emailanalista)
 {
	$Ahora=date('Y-m-d H:i:s');
	$a=fopen('r/ike.txt','w');fwrite($a,"token: $token");fclose($a);
	// Valida si el token es valido, de lo contrario retorna error de token
	if(qo1("select id from usuario_ws where token='$token' and id=1 "))
	{
		$codCiudad=str_pad($ciudad,8,'0',STR_PAD_RIGHT);// Ajusta la ciudad.
		$CiuOrig=$codCiudad;  // Asigna la ciudad original 
		$codCiudad=str_pad($codCiudad,8,'0',STR_PAD_LEFT);
		$Departamento=substr($codCiudad,0,2); // Halla el departamento 
		if($Ofic=qo1("select oficina from corresp_ofic where left(departamento,2)='$Departamento' ")) $Ciudad=$Ofic;else $Ciudad=$codCiudad; // obtiene la ciudad de atención
		// inserta el siniestro
		$Idn=q("insert ignore into siniestro (ciudad,ciudad_original,ciudad_siniestro,fec_autorizacion,fec_siniestro,fec_declaracion,numero,aseguradora,
		placa,asegurado_id,declarante_id,poliza,asegurado_nombre,declarante_nombre,declarante_telefono,declarante_celular,declarante_tel_resid,declarante_tel_ofic,
		declarante_email,dias_servicio,intermediario,email_analista,ingreso,estado,bco_occidente) values 
		('$Ciudad','$CiuOrig','$CiuOrig','$fecha','$fecha','$Ahora','$numero','2','$placa','$identificacion','$identificacion','$poliza','$nombre','$nombre',
		'$telefono1','$telefono2','$telefono3','$telefono4','$emailasegurado','$dias','$analista','$emailanalista','$Ahora','5','1')");
		$Fecha=date('Y-m-d'); $Hora=date('H:i:s');  // obtiene fecha y hora actual
		// inserta el seguimiento
		q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ('$Idn','$Fecha','$Hora','W.S. IKE','Ingreso AOA',1)");
		// ENVIO DE UN CORREO DE VERIFICACION
		enviar_gmail('sistemas@aoacolombia.com','Control WS-Ike',
		'arturoquintero@aoacolombia.com,ARTURO QUINTERO;viviana.bautista@co.rsagroup.com,VIVIANA BAUTISTA;ycampos@ikeasistencia.com,YAMILE CAMPOS','',
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
		Instante de registro: $Ahora
		
		Fin mensaje de control.
		"));
		// retorna el valor de exito
		return new soapval('return','xsd:string',"RECEPCION DE INFORMACION EXITOSA");
	}
	else
	{
		enviar_gmail('sistemas@aoacolombia.com','Control WS-Ike','arturoquintero@aoacolombia.com,ARTURO QUINTERO','Error de acceso al WS Ike',"Error de acceso al sistema Web Service de Ike token: $token");
		return new soapval('return','xsd:string',"Token Invalido");
	}
 }
 
// Registra la función InsertarSiniestro dentro del ws.
$Servidor_ws->register('InsertarSiniestroQbe',
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
							'emailanalista'=>'xsd:string' // emali del analista de Royal que guardo la informacion
						)
						,array('return'=>'xsd:string'),$miURL);
  
  function InsertarSiniestroQbe($token,$ciudad,$fecha,$numero,$placa,$identificacion,$poliza,$nombre,$telefono1,$telefono2,$telefono3,$telefono4,$emailasegurado,$dias,$analista,$emailanalista)
 {
	$Ahora=date('Y-m-d H:i:s');
	$a=fopen('r/qbe.txt','w');fwrite($a,"token: $token");fclose($a);
	// Valida si el token es valido, de lo contrario retorna error de token
	if(qo1("select id from usuario_ws where token='$token' and id=2 "))
	{
		$codCiudad=str_pad($ciudad,8,'0',STR_PAD_RIGHT);// Ajusta la ciudad.
		$CiuOrig=$codCiudad;  // Asigna la ciudad original 
		$codCiudad=str_pad($codCiudad,8,'0',STR_PAD_LEFT);
		$Departamento=substr($codCiudad,0,2); // Halla el departamento 
		if($Ofic=qo1("select oficina from corresp_ofic where left(departamento,2)='$Departamento' ")) $Ciudad=$Ofic;else $Ciudad=$codCiudad; // obtiene la ciudad de atención
		// inserta el siniestro
		$Idn=q("insert ignore into siniestro (ciudad,ciudad_original,ciudad_siniestro,fec_autorizacion,fec_siniestro,fec_declaracion,numero,aseguradora,
		placa,asegurado_id,declarante_id,poliza,asegurado_nombre,declarante_nombre,declarante_telefono,declarante_celular,declarante_tel_resid,declarante_tel_ofic,
		declarante_email,dias_servicio,intermediario,email_analista,ingreso,estado) values 
		('$Ciudad','$CiuOrig','$CiuOrig','$fecha','$fecha','$Ahora','$numero','15','$placa','$identificacion','$identificacion','$poliza','$nombre','$nombre',
		'$telefono1','$telefono2','$telefono3','$telefono4','$emailasegurado','$dias','$analista','$emailanalista','$Ahora','5')");
		$Fecha=date('Y-m-d'); $Hora=date('H:i:s');  // obtiene fecha y hora actual
		// inserta el seguimiento
		q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ('$Idn','$Fecha','$Hora','W.S. IKE','Ingreso AOA',1)");
		// ENVIO DE UN CORREO DE VERIFICACION
		enviar_gmail('sistemas@aoacolombia.com','Control WS-Ike',
		'arturoquintero@aoacolombia.com,ARTURO QUINTERO;ycampos@ikeasistencia.com,YAMILE CAMPOS','',
		"Ingreso de registro $token",
		nl2br("
		Control de Ingreso de información vía Web Service para Ike - QBE.
		
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
		Instante de registro: $Ahora
		
		Fin mensaje de control.
		"));
		// retorna el valor de exito
		return new soapval('return','xsd:string',"RECEPCION DE INFORMACION EXITOSA");
	}
	else
	{
		enviar_gmail('sistemas@aoacolombia.com','Control WS-QBE','sergiocastillo@aoacolombia,SERGIO CASTILLO','Error de acceso al WS QBE',"Error de acceso al sistema Web Service de QBE token: $token");
		return new soapval('return','xsd:string',"Token Invalido");
	}
 }
 
 $Servidor_ws->wsdl->addComplexType(
	'registro','complexType','struct','all','',
	array(
		'aseguradora' => array('name' => 'aseguradora', 'type' => 'xsd:int'),     // codigo de la aseguradora
		'numero_siniestro' => array('name' => 'numero_siniestro', 'type' => 'xsd:string'), // numero del siniestro (no es el id)
		'placa' => array('name' => 'placa', 'type' => 'xsd:string'), // placa del vehiculo siniestrado
		'telefono1' => array('name' => 'telefono1', 'type' => 'xsd:string'), // telefono
		'telefono2' => array('name' => 'telefono2', 'type' => 'xsd:string'), // telefono
		'telefono3' => array('name' => 'telefono3', 'type' => 'xsd:string'), // telefono
		'telefono4' => array('name' => 'telefono4', 'type' => 'xsd:string'), // telefono
		'telefono5' => array('name' => 'telefono5', 'type' => 'xsd:string'), // telefono
		'ingreso' => array('name' => 'ingreso', 'type' => 'xsd:string'), // fecha y hora de ingreso del siniestro a la base de datos de AOA
		'id' => array('name' => 'id', 'type' => 'xsd:int'), // id del siniestro
		'tipo' => array('name' => 'tipo', 'type' => 'xsd:int'), // tipo de caso 0=nuevo 1= compromiso
		'idcompromiso' => array('name' => 'idcompromiso', 'type' => 'xsd:int'), // id del seguimiento que corresponde al compromiso
		'ciudad' => array('name' => 'ciudad','type' => 'xsd:string'), // ciudad de atencion
		'nombre_asegurado' => array('name' => 'asegurado','type' => 'xsd:string'), // nombre del asegurado
		'nombre_aseguradora' => array('name' => 'nombre_aseguradora','type' => 'xsd:string') // nombre de la aseguradora
	)
 );
 
 $Servidor_ws->wsdl->addComplexType(
	'registros','complexType','array','','SOAP-ENC:Array',
	array(),
	array(
		array('ref' =>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:registro[]' )
	),
	'tns:registro'
 );
 
					
$Servidor_ws->register('ObtenerSiniestrosMarcacion',
						array(
							'token'=>'xsd:string', // token de validacion
							'aseg'=>'xsd:string' // id de la aseguradora que se desea obtener
						)
						,array('return'=>'tns:registros'),$miURL);
						
						
 function ObtenerSiniestrosMarcacion($token,$aseg)
 {
	// valida el token 
	$a=fopen('r/teleone_obtener.txt','w');fwrite($a,"token: $token aseguradora: $aseg");fclose($a);
	if(qo1("select id from usuario_ws where token='$token' and id=3 "))
	 {
		$Hoy=date('Y-m-d'); // obtengo la fecha de hoy
		$Hoy10=date('Y-m-d H:i:s',mktime(date('H'),date('i')+10,date('s'),date('n'),date('j'),date('Y')));
		 // Se buscan algunos datos de los siniestros en estado 5 (pendiente) de la fecha de ingreso actual
		if($aseg) $Condicion="and s.aseguradora in ($aseg) "; else $Condicion="";
		include('inc/link.php');
		mysql_query("drop table if exists tmpi_seg_marcacion",$LINK);
		mysql_query("create table tmpi_seg_marcacion SELECT distinct siniestro FROM seguimiento WHERE fecha>'2015-01-01' and tipo>1 ",$LINK);
		mysql_close($LINK);
		// obtiene todod los siniestros nuevos que no han tenido ningún tipo de seguimiento. y obtiene los que son de compromiso no cumplido durante el día y el momento de la llamada mas 10 minutos adelante para 
		// obtener los que se acercan. Genera una variable llamada TIPO que significa 0: nuevo 1: compromiso
		// el query es ordenado por fecha de ingreso/compromiso 
		
		if($Siniestros=q("select s.id,s.aseguradora,s.numero,s.placa,s.declarante_celular,s.declarante_tel_resid,s.declarante_tel_ofic,s.declarante_telefono,s.declarate_tel_otro,s.ingreso,
										(0) as tipo,(0000000000) as idcompromiso,c.nombre as nciudad,s.asegurado_nombre,a.nombre as naseg
			FROM siniestro s,ciudad c ,aseguradora a
				WHERE s.ciudad=c.codigo and s.aseguradora=a.id and s.estado=5 $Condicion  and s.id not in (select siniestro from tmpi_seg_marcacion) and s.id not in (select siniestro from call2proceso where estado='A')
			UNION select distinct s.id,s.aseguradora,s.numero,s.placa,s.declarante_celular,s.declarante_tel_resid,s.declarante_tel_ofic,s.declarante_telefono,s.declarate_tel_otro,g.fecha_compromiso as ingreso,
										(1) as tipo,g.id as idcompromiso,c.nombre as nciudad,s.asegurado_nombre,a.nombre as naseg
			FROM siniestro s,ciudad c,aseguradora a,seguimiento g 
				WHERE s.ciudad=c.codigo and s.aseguradora=a.id and g.siniestro=s.id and s.estado=5 $Condicion and g.cumplido=0 and g.tipo in (16) and g.fecha_compromiso between '$Hoy 00:00:00' and '$Hoy10' 
						and s.id not in (select siniestro from call2proceso where estado='A') and g.ws_vozip=0
			ORDER by ingreso
			LIMIT 200 "))
		{ 
			$respuesta=array();
			//$idcompromisos='0';
			while($S=mysql_fetch_object($Siniestros))
			{
				$respuesta[]=array(
												"aseguradora"=>$S->aseguradora,
												"numero_siniestro"=>$S->numero,
												"placa"=>$S->placa,
												"telefono1"=>$S->declarante_celular,
												"telefono2"=>$S->declarante_tel_resid,
												"telefono3"=>$S->declarante_tel_ofic,
												"telefono4"=>$S->declarante_telefono,
												"telefono5"=>$S->declarate_tel_otro,
												"ingreso"=>$S->ingreso,
												"id"=>$S->id,
												"tipo"=>$S->tipo,
												"idcompromiso"=>$S->idcompromiso,
												"ciudad"=>quitatildes($S->nciudad),
												"nombre_asegurado"=>quitatildes($S->asegurado_nombre),
												"nombre_aseguradora"=>quitatildes($S->naseg)
											);
				//if($S->idcompromiso) $idcompromisos.=','.$S->idcompromiso;
			}
			//if($idcompromisos!='0') q("update seguimiento set ws_vozip=1 where id in ($idcompromisos)");
			return new soapval('return','tns:registros',$respuesta);
		}
		else return new soapval('return','xsd:string',"No hay registros");
	}
	else return soapval('return','xsd:string',"Registro de Token Invalido");
 }
 
 $Servidor_ws->register('InsertarSeguimiento',
						array(
							'token'=>'xsd:string', // token de validacion
							'idsiniestro'=>'xsd:int', // id del siniestro 
							'tipo'=>'xsd:int', // tipo de seguimiento
							'descripcion'=>'xsd:string', // descripcion del seguimiento
							'idcompromiso'=>'xsd:string' // id del compromiso
						)
						,array('return'=>'xsd:string'),$miURL);
 
 function InsertarSeguimiento($token,$idsiniestro,$tipo,$descripcion,$idcompromiso)
 {
	if(qo1("select id from usuario_ws where token='$token' and id=3 "))
	{
		$Hoy=date('Y-m-d'); $hora=date('H:i:s');$usuario='WS-VOIP';
		if($tipo==21) 
		{
			$Aseguradora=qo1("select aseguradora from siniestro where id='$idsiniestro' ");
			$Tiempo_remarcacion=qo1("select remarca_voip from aseguradora where id='$Aseguradora' ");
			$tipo_compromiso=11;
			$Fecha_compromiso=date('Y-m-d H:i:s',mktime(date('H'),date('i'),date('s'),date('n'),date('j')+$Tiempo_remarcacion,date('Y')));
			q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo,tipo_compromiso,fecha_compromiso) values ('$idsiniestro','$Hoy','$hora','$usuario','$descripcion','$tipo','$tipo_compromiso','$Fecha_compromiso')");
		}
		else
		{
			q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ('$idsiniestro','$Hoy','$hora','$usuario','$descripcion','$tipo')");
		}
		if($tipo==22) {if($idcompromiso) q("update seguimiento set cumplido=1 where id=$idcompromiso");}
		if($tipo==27) {if($idcompromiso) q("update seguimiento set ws_vozip=1 where id=$idcompromiso");}
		return new soapval('return','xsd:string',"Seguimiento insertado $tipo");
	}
	else
	{
		enviar_gmail('sistemas@aoacolombia.com','Control WS-TELEONE','sergiocastillo@aoacolombia.com,SERGIO CASTILLO','Error de acceso al WS Teleone',"Error de acceso al sistema Web Service de TELEONE token: $token");
		return new soapval('return','xsd:string',"Token Invalido");
	}
 }
 
 $Servidor_ws->wsdl->addComplexType(
	'registrosms','complexType','struct','all','',
	array(
		'idsiniestro' => array('name' => 'idsiniestro', 'type' => 'xsd:int'),     // id del siniestro
		'aseguradora' => array('name' => 'aseguradora', 'type' => 'xsd:string'),     // nombre de la aseguradora
		'numero_siniestro' => array('name' => 'numero_siniestro', 'type' => 'xsd:string'), // numero del siniestro (no es el id)
		'placa' => array('name' => 'placa', 'type' => 'xsd:string'), // placa del vehiculo siniestrado
		'telefono1' => array('name' => 'telefono1', 'type' => 'xsd:string'), // telefono
		'telefono2' => array('name' => 'telefono2', 'type' => 'xsd:string'), // telefono
		'telefono3' => array('name' => 'telefono3', 'type' => 'xsd:string'), // telefono
		'telefono4' => array('name' => 'telefono4', 'type' => 'xsd:string'), // telefono
		'telefono5' => array('name' => 'telefono5', 'type' => 'xsd:string'), // telefono
		'tipo'=>array('name' => 'tipo', 'type' => 'xsd:string'), // tipo de cita 1= entrega  2= devolucion
		'fecha'=>array('name' => 'fecha', 'type' => 'xsd:string'), // fecha de la cita
		'hora'=>array('name' => 'hora', 'type' => 'xsd:string'), // hora de la cita
		'nombreoficina'=>array('name' => 'nombreoficina', 'type' => 'xsd:string'), // Nombre oficina
		'direccionoficina'=>array('name' => 'direccionoficina', 'type' => 'xsd:string') // Dirección de la oficina
	)
 );
 
 $Servidor_ws->wsdl->addComplexType(
	'registrossms','complexType','array','','SOAP-ENC:Array',
	array(),
	array(
		array('ref' =>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:registrosms[]' )
	),
	'tns:registrosms'
 );
 
 $Servidor_ws->register('ObtenerCitasSms',
						array(
							'token'=>'xsd:string', // token de validacion
						)
						,array('return'=>'tns:registrossms'),$miURL);
						
function ObtenerCitasSms($token)
{
	
	if(qo1("select id from usuario_ws where token='$token' and id=3 "))
	{
		$tempo1='tmpi_seg_sms1';
		$tempo2='tmpi_seg_sms2';
		$Manana=date('Y-m-d',mktime(date('H'),date('i'),date('s'),date('n'),date('j')+1,date('Y')));  // OBTIENE LA FECHA DE MAÑANA
		// CREA DOS TEMPORALES PARA EXCLUIR LAS CITAS QUE YA TUVIERON ENTREGA DE SMS
		q("drop table if exists $tempo1");
		// crea un primer temporal para saber cuales citas de entrega ya tuvieron envio de sms
		q("create table $tempo1 select distinct s.siniestro from cita_servicio c,seguimiento s where c.siniestro=s.siniestro and c.fecha='$Manana' and s.tipo=23");
		q("drop table if exists $tempo2");
		// crea un primer temporal para saber cuales citas de devolucion ya tuvieron envio de sms
		q("create table $tempo2 select distinct s.siniestro from cita_servicio c,seguimiento s where c.siniestro=s.siniestro and c.fec_devolucion='$Manana' and s.tipo=24");
		
		
		$respuesta=array();
		if($Citas=q("select a.nombre as naseg,c.hora,s.numero,s.placa,s.declarante_celular,s.declarante_tel_resid,s.declarante_tel_ofic,s.declarante_telefono,s.declarate_tel_otro,
			(1) as tipo,o.nombre as nofic,o.direccion,s.id as idsiniestro
			from cita_servicio c,siniestro s,aseguradora a,oficina o 
			where c.siniestro=s.id and s.aseguradora=a.id and c.oficina=o.id and c.fecha='$Manana' and c.estado='P' and c.siniestro not in (select siniestro from $tempo1) "))
		{
			while($S=mysql_fetch_object($Citas))
			{
				$respuesta[]=array(
												"idsiniestro"=>$S->idsiniestro,
												"aseguradora"=>$S->naseg,
												"numero_siniestro"=>$S->numero,
												"placa"=>$S->placa,
												"telefono1"=>$S->declarante_celular,
												"telefono2"=>$S->declarante_tel_resid,
												"telefono3"=>$S->declarante_tel_ofic,
												"telefono4"=>$S->declarante_telefono,
												"telefono5"=>$S->declarate_tel_otro,
												"tipo"=>$S->tipo,
												"fecha"=>$Manana,
												"hora"=>$S->hora,
												"nombreoficina"=>$S->nofic,
												"direccionoficina"=>$S->direccion
											);
			}
		}
		if($Devoluciones=q("select a.nombre as naseg,c.hora_devol,s.numero,s.placa,s.declarante_celular,s.declarante_tel_resid,s.declarante_tel_ofic,s.declarante_telefono,s.declarate_tel_otro,
			(2) as tipo,o.nombre as nofic,o.direccion,s.id as idsiniestro
			from cita_servicio c,siniestro s,aseguradora a , oficina o
			where c.siniestro=s.id and s.aseguradora=a.id and c.oficina=o.id and c.fec_devolucion='$Manana' and c.estadod='P' and c.siniestro not in (select siniestro from $tempo2) "))
		{
			while($S=mysql_fetch_object($Devoluciones))
			{
				$respuesta[]=array(
												"idsiniestro"=>$S->idsiniestro,
												"aseguradora"=>$S->naseg,
												"numero_siniestro"=>$S->numero,
												"placa"=>$S->placa,
												"telefono1"=>$S->declarante_celular,
												"telefono2"=>$S->declarante_tel_resid,
												"telefono3"=>$S->declarante_tel_ofic,
												"telefono4"=>$S->declarante_telefono,
												"telefono5"=>$S->declarate_tel_otro,
												"tipo"=>$S->tipo,
												"fecha"=>$Manana,
												"hora"=>$S->hora_devol,
												"nombreoficina"=>$S->nofic,
												"direccionoficina"=>$S->direccion
											); 
			}
		}
		return new soapval('return','tns:registrossms',$respuesta);
	}
	else
	{
		enviar_gmail('sistemas@aoacolombia.com','Control WS-TELEONE','sergiocastillo@aoacolombia.com,SERGIO CASTILLO','Error de acceso al WS Teleone',"Error de acceso al sistema Web Service de TELEONE token: $token");
		return new soapval('return','xsd:string',"Token Invalido");
	}
}
 
if ( !isset( $HTTP_RAW_POST_DATA ) ) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
$Servidor_ws->service($HTTP_RAW_POST_DATA);
?>