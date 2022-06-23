<?php
include('inc/funciones_.php');
require('inc/webservice/nusoap.php');
include('inc/Mail/SMTP.php');

// DEFINICION DE VARIABLES DE CONSUMO Y AUTENTICACION DEL WEBSERVICE
define('WS_USUARIO','WSSINI');
define('WS_PASSWORD','contrasena');
define('WS_COMPANIA','1');
define('WS_NEGOCIO','500');
define('WS_NAMESPACE','http://service.servicios.mapfre.com.co/');
define('WS_SERVER','http://www.mapfre.com.co/MapfreServices/ws?wsdl');
//define('WS_SERVER','http://10.192.20.14:8080/MapfreServices/ws?wsdl');
//define('WS_SERVER','http://10.192.16.22:8080/MapfreServices/ws?wsdl');
$RAMO=array('101'=>'TREBOL ELITE','110'=>'TREBOL PARTICULAR','111'=>'TREBOL PUBLICO','112'=>'PARA LA MUJER','113'=>'PARA LA FAMILIA','115'=>'SUPER TREBOL',
'116'=>'SUPER TREBOL','117'=>'SUPER TREBOL','118'=>'SUPER TREBOL','120'=>'SERVICIO PUBLICO ESPECIAL','121'=>'VEHICULOS CERO KILOMETROS','122'=>'VEHICULOS KIA CERO KILOMETROS',
'123'=>'CODENSA AUTOS','125'=>'TREBOL MODULAR INDIVIDUAL','138'=>'MERCADEO MASIVO','140'=>'FINANCIERA','141'=>'SERVICIO PUBLICO',
'142'=>'SERVICIO PUBLICO ESPECIAL','150'=>'FINANCIERO INDIVIDUAL','155'=>'POLIZA COLECTIVA AUTOMOVILES','158'=>'GARANTIA EXTENDIDA','159'=>'POLIZA DE AUTOMOVILES ANDINA',
'161'=>'COLECTIVA PESADOS-SEMIPESADOS','162'=>'POLIZA COLECTIVA MOTOS','163'=>'POLIZA COLECTIVA TAXIS','164'=>'COLECTIVA GRUAS','165'=>'COLECTIVA VOLQUETAS',
'166'=>'COLECTIVA LICITACIONES','167'=>'RESPONSABILIDAD CIVIL');

$EMAIL="arturoquintero@aoacolombia.com,jforero@mapfre.com.co,wfsilva@mapfre.com.co,anvalbu@mapfre.com.co,germangonzalez@aoacolombia.com,gabrielsandoval@aoacolombia.com";
$EMAIL1="arturoquintero@aoacolombia.com,wfsilva@mapfre.com.co,anvalbu@mapfre.com.co";
$EMAIL2="arturoquintero@aoacolombia.com,anvalbu@mapfre.com.co";
// proceso de conexión
$client = new nusoap_client(WS_SERVER,FALSE,false,false,false,false,200,200);
$err = $client->getError();
if ($err) die('<h2>Constructor error</h2><pre>' . $err . '</pre><h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>');
// procdeso de autenticación
$params = array('arg0'=>WS_USUARIO, 'arg1'=>WS_PASSWORD,'arg2'=>WS_COMPANIA, 'arg3'=>WS_NEGOCIO);
$token = $client->call('autentica', $params, WS_NAMESPACE );
if ($client->fault) die("<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>$token</pre>");
$err = $client->getError();if ($err) die("<h2>Error</h2><pre>$err </pre>");
// fin proceso de autenticación
if($token=='AUDS' || $token=='AUNV' || $token=='AUNE')
{
	if($token=='AUDS') $Cadena='ACCESO USUARIO DESAUTORIZADO';
	if($token=='AUNV') $Cadena='ACCESO USUARIO NO VALIDO';
  if($token=='AUNE') $Cadena='ACCESO USUARIO NO EXISTENTE';
  $c=SMTP::Connect('mail.aoacolombia.com',25,'sistemas@aoacolombia.com','Sistemas2010');
  $m="From: sistemas@aoacolombia.com\nTo: arturoquintero@aoacolombia.com\nSubject: Mapfre Webservice\nContent-Type: text/html\n\nError en el consumo webservice $Cadena.";
  $s2=SMTP::Send($c,array('arturoquintero@aoacolombia.com'),$m,'sistemas@aoacolombia.com');
  if(!$s2) echo "Error enviado a: arturoquintero@aoacolombia.com";
  die();
}
// USUARIO AUTENTICADO CORRECTAMENTE
// proceso de lectura de siniestros
$Cadena='';
if($Seguimientos=q("select siniestro.numero,seguimiento.*,t_tipo_seguimiento(seguimiento.tipo) as ntipo from seguimiento,siniestro where seguimiento.siniestro=siniestro.id and seguimiento.enviado_ws=0 and siniestro.aseguradora=4"))
{
	$Cantidad=0;
	include('inc/link.php');
	while($Seg=mysql_fetch_object($Seguimientos))
	{
		if(is_numeric($Seg->numero))
		{
			echo "<br />$Seg->numero ok.";
			$Cadena.=$Seg->numero.';'.$Seg->fecha.';'.$Seg->hora.';'.$Seg->usuario.';'.str_replace(';',',',$Seg->descripcion).';'.$Seg->ntipo.'|';
			mysql_query("update seguimiento set enviado_ws=1 where id=$Seg->id ");
			$Cantidad++;
		}
		else
		{
			echo "<br />$Seg->numero ignorado.";
		}
	}
	mysql_close($LINK);
}

if($Cadena)
{
	$Resultado=$client->call('informaEstadoSiniestros',array('arg0'=>$token,'arg1'=>$Cadena),WS_NAMESPACE);
	$err = $client->getError();
	if($err) $Resultado="Error: $err";

	$Cadena_email="<html><body>Control de envio WS<br><br>Esta es la cadena de seguimiento que se entregó al WS mapfre hoy ".date('Y-m-d H:i:s');
	$Cadena_email.="<br>Número de registros: $Cantidad<br><br>".$Cadena;
	$Cadena_email.="<br><br>Token:  $token";
	$c=SMTP::Connect('mail.aoacolombia.com',25,'sistemas@aoacolombia.com','Sistemas2010');
	$m="From: sistemas@aoacolombia.com\nTo: $EMAIL2\nSubject: Mapfre Webservice\nContent-Type: text/html\n\n".$Cadena_email;
	$Email=split(',',$EMAIL2);
	$s1=SMTP::Send($c,$Email,$m,'sistemas@aoacolombia.com');
	if(!$s1) echo '<br>Ocurrio un problema con el envio del mail';
}

?>