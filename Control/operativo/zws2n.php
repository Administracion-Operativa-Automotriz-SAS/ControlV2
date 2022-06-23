<?php

/*
	Ultimas actualizaciones:  Octubre 4 2011 11:07  Verificación de varios siniestros con la misma placa.

*/
$MODO='Normal'; //  Normal / Seguimiento por email

include('inc/funciones_.php');
require('inc/webservice/nusoap.php'); // carga las rutinas para cliente nusoap de web service

// DEFINICION DE VARIABLES DE CONSUMO Y AUTENTICACION DEL WEBSERVICE
define('WS_USUARIO','WSSINI');
define('WS_PASSWORD','contrasena');
define('WS_COMPANIA','1');
define('WS_NEGOCIO','500');
define('WS_NAMESPACE','http://service.servicios.mapfre.com.co/');
define('WS_SERVER','http://contenido.mapfre.com.co/MapfreServices/ws?wsdl');
//define('WS_SERVER','http://10.192.20.14:8080/MapfreServices/ws?wsdl');
//define('WS_SERVER','http://10.192.16.22:8080/MapfreServices/ws?wsdl');
// define los ramos posibles 
$RAMO=array('101'=>'TREBOL ELITE','110'=>'TREBOL PARTICULAR','111'=>'TREBOL PUBLICO','112'=>'PARA LA MUJER','113'=>'PARA LA FAMILIA','115'=>'SUPER TREBOL',
'116'=>'SUPER TREBOL','117'=>'SUPER TREBOL','118'=>'SUPER TREBOL','120'=>'SERVICIO PUBLICO ESPECIAL','121'=>'VEHICULOS CERO KILOMETROS','122'=>'VEHICULOS KIA CERO KILOMETROS',
'123'=>'CODENSA AUTOS','125'=>'TREBOL MODULAR INDIVIDUAL','138'=>'MERCADEO MASIVO','140'=>'FINANCIERA','141'=>'SERVICIO PUBLICO',
'142'=>'SERVICIO PUBLICO ESPECIAL','150'=>'FINANCIERO INDIVIDUAL','155'=>'POLIZA COLECTIVA AUTOMOVILES','158'=>'GARANTIA EXTENDIDA','159'=>'POLIZA DE AUTOMOVILES ANDINA',
'161'=>'COLECTIVA PESADOS-SEMIPESADOS','162'=>'POLIZA COLECTIVA MOTOS','163'=>'POLIZA COLECTIVA TAXIS','164'=>'COLECTIVA GRUAS','165'=>'COLECTIVA VOLQUETAS',
'166'=>'COLECTIVA LICITACIONES','167'=>'RESPONSABILIDAD CIVIL');


$EMAIL="arturoquintero@aoacolombia.com,Arturo Quintero;gabrielsandoval@aoacolombia.com,Gabriel Sandoval";
$EMAIL1="arturoquintero@aoacolombia.com,Arturo Quintero";
$EMAIL2="arturoquintero@aoacolombia.com,Arturo Quintero";
$Correo_repetidos='';   // variable para acumular los siniestros de placas que ya tengan siniestros previos en la base de datos.  Objetivo: buscar pendientes para informar y hacer el respectivo ajuste.
//echo "<br />Creando el cliente <br />";
$client = new nusoap_client(WS_SERVER,false,false,false,false,false,600,600); // conexion con el webservice
$client->http_encoding = 'UTF-8';
$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = false;
$err = $client->getError();
if ($err) die('<h2>Constructor error</h2><pre>' . $err . '</pre><h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>');
// procdeso de autenticación
$params = array('arg0'=>WS_USUARIO, 'arg1'=>WS_PASSWORD,'arg2'=>WS_COMPANIA, 'arg3'=>WS_NEGOCIO); // configura el arreglo de parametros

if($Modo!='seguimiento')
{
	// proceso de conexión
	//echo "<br />Autenticando: ";
	$token = $client->call('autentica', $params, WS_NAMESPACE ); // invoca la autenticacion con la lista de parametros y retorna un token si la validacion es exitosa
	if ($client->fault) die("<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>$token</pre>");
		$err = $client->getError();if ($err) die("<h2>Error</h2><pre>$err </pre>");
		// fin proceso de autenticación
	if($token=='AUDS' || $token=='AUNV' || $token=='AUNE')  // verifica la respuesta del token
	{
		if($token=='AUDS') $Cadena='ACCESO USUARIO DESAUTORIZADO';
		if($token=='AUNV') $Cadena='ACCESO USUARIO NO VALIDO';
		if($token=='AUNE') $Cadena='ACCESO USUARIO NO EXISTENTE';
		// envia un correo cuando alguno de estos estados de token sucede
		$Envio1=enviar_gmail('sistemas@aoacolombia.com' /*de */,
		'Sistemas AOA Colombia' /*Nombre de */ ,
		"arturoquintero@aoacolombia.com,Arturo Quintero" /*para */,
		"" /*con copia*/,
		"Error Mapfre Webservice" /*Objeto */,
		"Error en el consumo webservice $Cadena.");
		die();
	}

	// USUARIO AUTENTICADO CORRECTAMENTE
	//	echo "token: $token";

	// proceso de lectura de siniestros
	$FECHA=date('Ymd');
	$DIA=array();
	//echo "<br>Buscando el dia: ";
	$Resultado=$client->call('leeSiniestrosConsultadosCSV',array('arg0'=>$token), WS_NAMESPACE); // invoca la rutina donde retorna los ultimos siniestros consultados en formato csv
	$err = $client->getError();
	if($err) die("Error: $err");
	$Cantidad=0;
	$Cantidad_ahora=0;
	if(!$LINK=mysql_connect('localhost','aoacol_arturo','AOA0l1lwpdaa')) die('Problemas con la conexion de la base de datos!'); // conecta con la base de datos
	if(!mysql_select_db('aoacol_aoacars',$LINK)) die('Problemas con la seleccion de la base de datos'); 
	$Cadena_email='';
	if(gettype($Resultado)=='string')
	{
		$Archivows=fopen('planos/ultimo_ws_mapfre.txt','w+');
		fwrite($Archivows,$Resultado);
		fclose($Archivows);
		$Siniestros=explode('|',$Resultado); // explota el resultado por un caracter
		$Cantidad=count($Siniestros);

		$Cadena_email.="<hr>Fecha: $FECHA $Cantidad Siniestros<br><table border cellspacing=0><tr><th>#</th><th>Siniestro</th><th>Fecha siniestro</th><th>Fecha declaración</th><th>Placa</th><th>Ciudad Origen</th><th>Ingreso a AOA</th><th>CodC</th></tr>";
		for($i=0;$i<count($Siniestros);$i++) // por cada siniestro crea las variables para la insercion
		{
			$S=explode(';',$Siniestros[$i]);

			$Numero_siniestro=$S[0];$Expediente=$S[1];$Ramo=$RAMO[$S[2]];$Poliza=$S[3];$IdAsegurado=$S[4];$NomAsegurado=$S[5];$Telefono_declarante=$S[6];$Celular_declarante=$S[7];
			$Fec_denuncia=substr($S[10],6,4).'-'.substr($S[10],3,2).'-'.substr($S[10],0,2);$Fec_siniestro=substr($S[9],6,4).'-'.substr($S[9],3,2).'-'.substr($S[9],0,2);

			$Placa=$S[11];	$Codigo_fasecolda=$S[12];$Codigo_chasis=$S[13];
			$Modelo=$S[14];$codCiudad=$S[15].'000';$CiuOrig=$codCiudad;$codCiudad=str_pad($codCiudad,8,'0',STR_PAD_LEFT);$Departamento=substr($codCiudad,0,2);
			if($Ofic=qo1m("select oficina from aoacol_aoacars.corresp_ofic where left(departamento,2)='$Departamento' ",$LINK)) $Ciudad=$Ofic;else $Ciudad=$codCiudad;
			$Linea=$S[16];
			$Telefono_conductor=$S[17];$Celular_conductor=$S[18];
			$Telefono_asegurado=$S[19];$Email_asegurado=$S[20];$Nombre_conductor=$S[21];
			$dias_servicio = 7;
			$Chevyseguro=($S[22]=='NO'?0:1);
			if($Chevyseguro==0)
			{
				//echo "<br>Buscando chevysegur para $Placa..";
				if(!$Especial=mysql_query("select id from placa_especial where placa like '%$Placa%' and aseguradora=4 ",$LINK)) die(mysql_error($LINK));
				$Chevyseguro=(mysql_num_rows($Especial)>0?1:0);echo $Chevyseguro;
			}
			if($Numero_siniestro) // inserta el siniestro
			{
				$futura_placa = qom("select * from aoacol_aoacars.futuras_placa_mapfre where placa= '".$Placa."'",$LINK);
				if(isset($futura_placa->placa))
				{
					$dias_servicio = 10;	
				}
				
				$Ahora=date('Y-m-d H:i:s');
				if(!mysql_query("insert ignore into aoacol_aoacars.siniestro (aseguradora,numero,ciudad,ciudad_original,fec_autorizacion,fec_siniestro,fec_declaracion,poliza,
				     sucursal_radicadora,intermediario,estado,placa,linea,modelo,asegurado_nombre,asegurado_id,expediente,
				     fasecolda,declarante_nombre,declarante_id,conductor_tel_resid,conductor_celular,conductor_nombre,declarante_email,
				     declarante_telefono,declarate_tel_otro,declarante_celular,ingreso,chevyseguro,dias_servicio)
				     values ('4','$Numero_siniestro','$Ciudad','$codCiudad','$Ahora','$Fec_siniestro','$Fec_denuncia','$Poliza',
				     '$Ramo','$Codigo_chasis','5','$Placa','$Linea','$Modelo','$NomAsegurado','$IdAsegurado','$Expediente',
				     '$Codigo_fasecolda','$NomAsegurado','$IdAsegurado','$Telefono_conductor','$Celular_conductor','$Nombre_conductor','$Email_asegurado',
				     '$Telefono_declarante','$Telefono_asegurado','$Celular_declarante','$Ahora','$Chevyseguro','$dias_servicio')",$LINK)) {echo mysql_error($LINK);mysql_close($LINK);die();}
				if($Varios=mysql_query("select t_aseguradora(aseguradora) as naseg,numero,fec_autorizacion,t_estado_siniestro(estado) as nest,asegurado_nombre,ingreso,t_ciudad(ciudad) as nciu
														from siniestro where placa='$Placa' order by ingreso",$LINK))
				{ // busca los siniestros previos donde coincida con la misma placa
					if(mysql_num_rows($Varios)>1)
					{
						$Correo_repetidos.="<tr ><td >Placa: $Placa</td></tr>";
						while($Var=mysql_fetch_object($Varios))
						{
							$Correo_repetidos.="<tr ><td >$Var->naseg</td><td >$Var->numero</td><td >$Var->fec_autorizacion</td><td >$Var->nest</td><td >$Var->asegurado_nombre</td><td >$Var->ingreso</td><td >$Var->nciu</td></tr>";
						}
					}
				}
				if(!$D=qom("select id,numero,ingreso,fec_siniestro,fec_autorizacion,placa,t_ciudad(ciudad_original) as nciudad,ingreso from siniestro where aseguradora=4 and numero='$Numero_siniestro' ",$LINK)) echo "<br />No encuentro siniestro $Numero_siniestro";
				if(!qo1m("select id from seguimiento where siniestro=$D->id and tipo=1",$LINK))
				{ // inserta el seguimiento de insercion del siniestro
					$H1=date('Y-m-d',strtotime($D->ingreso)); $H2=date('H:i:s',strtotime($D->ingreso));
					mysql_query("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo ) values ($D->id,'$H1','$H2','Webservice','Ingreso a AOA',1)",$LINK);
				}
				$Inicio_hora =date('Y-m-d H').':00:00';
				//echo "<br>$D->numero $D->ingreso $Inicio_hora ";
				if($D->ingreso >$Inicio_hora)
				{
					$Cadena_email.="<tr><td>$D->numero</td><td>$D->fec_siniestro</td><td>$D->fec_autorizacion</td><td>$D->placa</td>".
					"<td>$D->nciudad</td><td>$D->ingreso</td><td>$CiuOrig</td><td >$Chevyseguro</td></tr>";
					$Cantidad_ahora++;
				}
			}
		}
		$Cadena_email.="</table>";
	}
	mysql_close($LINK);
	echo "<br />Resultado $FECHA: $Cantidad_ahora";
}


$Cadena_seguimiento='';
if($Seguimientos=q("select si.numero,se.*,t_tipo_seguimiento(se.tipo) as ntipo
								From seguimiento se,aoacol_aoacars.siniestro si
								Where se.siniestro=si.id and se.enviado_ws=0 and si.aseguradora=4  order by id limit 400")) // obtiene los ultimos seguimientos para enviarlos en otra rutina webservice
{
	$Cantidad_devueltos=0;
	$id_enviados='0';
	while($Seg=mysql_fetch_object($Seguimientos))
	{
		if(is_numeric($Seg->numero))
		{
			$Cantidad_devueltos++; // crea una cadena para ser enviada al ws.mapfre
			//echo "<br />$Cantidad_devueltos: $Seg->numero ok. $Seg->id ";
			$Cadena_seguimiento.=$Seg->numero.';'.$Seg->fecha.';'.$Seg->hora.';'.$Seg->usuario.';'.str_replace(';',',',$Seg->descripcion).';'.$Seg->ntipo.'|';
			$id_enviados.=','.$Seg->id;
		}
		else
		{
			//echo "<br />$Seg->numero ignorado.";
			$id_enviados.=','.$Seg->id;
		}
	}
	//echo "<br />Cadena Seguimiento: <br /><br />$Cadena_seguimiento<br /><br />";
}
else
{
	//echo "<br>No hay mas seguimientos para reportar.";
}

$token = $client->call('autentica', $params, WS_NAMESPACE ); // autentica y obtiene el token
if ($client->fault) die("<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>$token</pre>");
$err = $client->getError();if ($err) die("<h2>Error</h2><pre>$err </pre>");
// fin proceso de autenticación
if($token=='AUDS' || $token=='AUNV' || $token=='AUNE')
{// si hay algun error lo informa en un correo electronico
	if($token=='AUDS') $Cadena='ACCESO USUARIO DESAUTORIZADO';
	if($token=='AUNV') $Cadena='ACCESO USUARIO NO VALIDO';
	if($token=='AUNE') $Cadena='ACCESO USUARIO NO EXISTENTE';
	$Envio1=enviar_gmail('sistemas@aoacolombia.com' /*de */,
	'Sistemas AOA Colombia' /*Nombre de */ ,
	"arturoquintero@aoacolombia.com,Arturo Quintero" /*para */,
	"" /*con copia*/,
	"Error Mapfre Webservice" /*Objeto */,
	"Error en el consumo webservice $Cadena.");
	die();
}
echo "<br />Token: $token <br />";
if($Cadena_seguimiento)
{ // invoca la rutina de envio de seguimientos a mapfre
	$Resultado=$client->call('informaEstadoSiniestros',array('arg0'=>$token,'arg1'=>$Cadena_seguimiento),WS_NAMESPACE);
	$err = $client->getError();
	if($err) die("Error: $err");
	else {q("update seguimiento set enviado_ws=1 where id in ($id_enviados)");	echo "<h4>Exito en el envio. $id_enviados</h4>";}
	$Cadena_emaild="Control de envio WS<br>Esta es la cadena de seguimiento que se entrego al WS mapfre hoy ".date('Y-m-d H:i:s');
	$Cadena_emaild.="<br>Numero de registros: $Cantidad_devueltos<br><br>".$Cadena_seguimiento;
	$Cadena_emaild.="<br><br>Token:  $token";


	if($MODO=='Seguimiento')
	{
		// envia un correo de seguimiento
		$Envio1=enviar_gmail('sistemas@aoacolombia.com' /*de */,'Sistemas AOA Colombia' /*Nombre de */ ,$EMAIL2 /*para */,"" /*con copia*/,"Mapfre Webservice" /*Objeto */,$Cadena_emaild);
	}
	else // inserta en la tabla de seguimientos de web service el resultado de la ejecucion
		q("insert into web_service (fecha,aseguradora,descripcion) values ('".date('Y-m-d H:i:s')."','4',\"$Cadena_emaild\")");
}


// cuando encuentra siniestros nuevos envía un correo informando de su insercion
$Cadena_email.='<br><br>Webservice de consumo:  '.WS_SERVER;
// envio del mail automatico
if($Cantidad_ahora)
{
	$Cadena_email="<hr>Fecha: $FECHA $Cantidad_ahora Siniestros nuevos<br><table border cellspacing=0><tr><th>Siniestro</th><th>Fecha siniestro</th><th>Fecha declaracion</th><th>Placa</th><th>Ciudad Origen</th><th>Ingreso a AOA</th><th>CodC</th><th>ChevySeguro</th></tr>".
		$Cadena_email;
	if($MODO=='Seguimiento')
		$Envio1=enviar_gmail('sistemas@aoacolombia.com' /*de */,'Sistemas AOA Colombia' /*Nombre de */ ,$EMAIL /*para */,"" /*con copia*/,"Mapfre Webservice" /*Objeto */,$Cadena_email);
	else // inserta en la tabla de seguimiento de web service la cadena de siniestros insertados
		q("insert into web_service (fecha,aseguradora,descripcion) values ('".date('Y-m-d H:i:s')."','4',\"$Cadena_email\")");
}
else
{
	$Cadena_email="<hr>Fecha: $FECHA 0 Siniestros nuevos<br><br>".$Cadena_email;
	if($MODO=='Seguimiento')
		$Envio1=enviar_gmail('sistemas@aoacolombia.com' /*de */,'Sistemas AOA Colombia' /*Nombre de */ ,$EMAIL1 /*para */,"" /*con copia*/,"Mapfre Webservice" /*Objeto */,$Cadena_email);
	else // inserta en la tabla de seguimiento de web service los siniestros insertados
		q("insert into web_service (fecha,aseguradora,descripcion) values ('".date('Y-m-d H:i:s')."','4',\"$Cadena_email\")");
}
if($Correo_repetidos)
{// envia una cadena de siniestros que coinciden con la misma placa
	$Cadena_emailr="<body>Correo de verificacion de siniestros con placas repetidas.<br>A continuacion se relacionan las placas repetidas:<br>".
		"<table border cellspacing='0'><tr ><th >Aseguradora</th><th >Numero</th><th >Fec.Autorizacion</th><th >Estado</th><th >Asegurado</th><th >Ingreso</th><th >Ciudad</th></tr>".
		$Correo_repetidos."</table><br><br>Cordialmente,<br><br>Sergio Castillo Castro.<br>Gestor de Procesos<br>AOA Colombia S.A.<br>sergiocastillo@aoacolombia.com</body></html>";
	$Envio=enviar_gmail('sistemas@aoacolombia.com' /*de */,
	'Sistemas AOA Colombia' /*Nombre de */ ,
	"sandraosorio@aoacolombia.com,Sandra Osorio;siniestros@aoacolombia.com,Gestor de Siniestros" /*para */,
	"sergiocastillo@aoacolombia.com,Sergio Castillo" /*con copia*/,
	"Siniestros con la misma placa." /*Objeto */,
	$Cadena_emailr);
}

if($Modo=='seguimiento')
{
	sleep(20);
	echo "<script language='javascript'>window.open('zws2n.php?Modo=seguimiento','_self');</script>";
}

?>