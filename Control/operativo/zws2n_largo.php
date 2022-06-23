<?php
include('inc/funciones_.php');
require('inc/webservice/nusoap.php');
//include('inc/Mail/SMTP.php');

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

$EMAIL="arturoquintero@aoacolombia.com,Arturo Quintero;anvalbu@mapfre.com.co,Anderson Valbuena;gabrielsandoval@aoacolombia.com,Gabriel Sandoval";
$EMAIL1="anvalbu@mapfre.com.co,Anderson Valbuena";
$EMAIL2="arturoquintero@aoacolombia.com,Arturo Quintero;anvalbu@mapfre.com.co,Anderson Valbuena";
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
	$Envio1=enviar_gmail('sistemas@aoacolombia.com' /*de */,
										'Sistemas AOA Colombia' /*Nombre de */ ,
										"arturoquintero@aoacolombia.com,Arturo Quintero" /*para */,
										"" /*con copia*/,
										"Error Mapfre Webservice" /*Objeto */,
										"Error en el consumo webservice $Cadena.");

  die();
}
// USUARIO AUTENTICADO CORRECTAMENTE
// proceso de lectura de siniestros
echo "Token: $token <br /><br />";

$FECHA=date('Ymd');
$DIA=array();
//echo "<br>Buscando el dia: ";
$Resultado=$client->call('leeSiniestrosConsultadosCSV',array('arg0'=>$token), WS_NAMESPACE);
$err = $client->getError();
if($err) die("Error: $err");
//$Cadena_email="<html><body>Estimados señores de MAPFRE:<br><br>A continuación se relaciona la obtención de información del webservice:<br><br>";
$Cantidad=0;
$Cantidad_ahora=0;
if(!$LINK=mysql_connect('localhost','aoacol_arturo','KXMd4v9GQup7')) die('Problemas con la conexion de la base de datos!');
if(!mysql_select_db('aoacol_aoacars',$LINK)) die('Problemas con la seleccion de la base de datos');
if(gettype($Resultado)=='string')
{
	// Num_sini;Num_exp;Cod_ramo;Num_poliza;Cod_docum_aseg;Nombre;Tlf_numero;Tlf_numero2;Num_spto;Fec_sini;Fec_denu_sini;COD_PLACA;COD_FASECOLDA;COD_CHASIS;COD_MODELO;COD_POSTAL_RADIC;COD_LINEA, telf no conductor, tel. movil conductor, tel sini asegurado, mail aseg, nom conductor
//	echo "Resultado: $Resultado<br /><br />";
	$Siniestros=explode('|',$Resultado);
	$Cantidad=count($Siniestros);
//	echo "<br />Cantidad: $Cantidad<br /><br />";
	//$Cadena_email.="<hr>Fecha: $FECHA $Cantidad Siniestros<br><table border cellspacing=0><tr><th>#</th><th>Siniestro</th><th>Fecha siniestro</th><th>Fecha declaración</th><th>Placa</th><th>Ciudad Origen</th><th>Ingreso a AOA</th><th>CodC</th></tr>";
	for($i=0;$i<count($Siniestros);$i++)
	{
		$S=explode(';',$Siniestros[$i]);

		$Numero_siniestro=$S[0];$Expediente=$S[1];$Ramo=$RAMO[$S[2]];$Poliza=$S[3];$IdAsegurado=$S[4];$NomAsegurado=$S[5];$Telefono_declarante=$S[6];$Celular_declarante=$S[7];
		//  Num_spto???
	//	$Fec_denuncia=substr($S[10],0,4).'-'.substr($S[10],8,2).'-'.substr($S[10],5,2);$Fec_siniestro=substr($S[9],0,4).'-'.substr($S[9],8,2).'-'.substr($S[9],5,2);

		$Fec_denuncia=substr($S[10],6,4).'-'.substr($S[10],3,2).'-'.substr($S[10],0,2);$Fec_siniestro=substr($S[9],6,4).'-'.substr($S[9],3,2).'-'.substr($S[9],0,2);

		$Placa=$S[11];	$Codigo_fasecolda=$S[12];$Codigo_chasis=$S[13];
		$Modelo=$S[14];$codCiudad=$S[15].'000';$CiuOrig=$codCiudad;$codCiudad=str_pad($codCiudad,8,'0',STR_PAD_LEFT);$Departamento=substr($codCiudad,0,2);
		if($Ofic=qo1m("select oficina from aoacol_aoacars.corresp_ofic where left(departamento,2)='$Departamento' ",$LINK)) $Ciudad=$Ofic;else $Ciudad=$codCiudad;
		$Linea=$S[16];
		$Telefono_conductor=$S[17];$Celular_conductor=$S[18];
		$Telefono_asegurado=$S[19];$Email_asegurado=$S[20];$Nombre_conductor=$S[21];
		if($Numero_siniestro)
		{
			$Ahora=date('Y-m-d H:i:s');
			if(!mysql_query("insert ignore into aoacol_aoacars.siniestro (aseguradora,numero,ciudad,ciudad_original,fec_autorizacion,fec_siniestro,fec_declaracion,poliza,
			     sucursal_radicadora,intermediario,estado,placa,linea,modelo,asegurado_nombre,asegurado_id,expediente,
			     fasecolda,declarante_nombre,declarante_id,conductor_tel_resid,conductor_celular,conductor_nombre,declarante_email,
			     declarante_telefono,declarate_tel_otro,declarante_celular,ingreso)
			     values ('4','$Numero_siniestro','$Ciudad','$codCiudad','$Fec_denuncia','$Fec_siniestro','$Fec_denuncia','$Poliza',
			     '$Ramo','$Codigo_chasis','5','$Placa','$Linea','$Modelo','$NomAsegurado','$IdAsegurado','$Expediente',
			     '$Codigo_fasecolda','$NomAsegurado','$IdAsegurado','$Telefono_conductor','$Celular_conductor','$Nombre_conductor','$Email_asegurado',
			     '$Telefono_declarante','$Telefono_asegurado','$Celular_declarante','$Ahora')",$LINK)) {echo mysql_error($LINK);mysql_close($LINK);die();}
		}
		if(!$D=qom("select * from aoacol_aoacars.siniestro where aseguradora=4 and numero='$Numero_siniestro' ",$LINK)) echo "<br />No encuentro siniestro $Numero_siniestro";
		$Co=qom("select * from aoacol_aoacars.ciudad where codigo='$D->ciudad_original' ",$LINK);
		if(!qo1m("select id from aoacol_aoacars.seguimiento where siniestro=$D->id and tipo=1",$LINK))
		{
			$H1=date('Y-m-d',strtotime($D->ingreso)); $H2=date('H:i:s',strtotime($D->ingreso));
			mysql_query("insert into aoacol_aoacars.seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo ) values ($D->id,'$H1','$H2','Webservice','Ingreso a AOA',1)",$LINK);
		}
		$Inicio_hora =date('Y-m-d H').':00:00';
		echo "<br>$D->numero $D->ingreso $Inicio_hora ";
		if($D->ingreso >$Inicio_hora)
		{
			$Cadena_email.="<tr><td>$D->numero</td><td>$D->fec_siniestro</td><td>$D->fec_autorizacion</td><td>$D->placa</td>".
			"<td>$Co->departamento $Co->nombre</td><td>$D->ingreso</td><td>$CiuOrig</td></tr>";
			$Cantidad_ahora++;
		}
	}
	$Cadena_email.="</table>";
}
mysql_close($LINK);
$Cadena_seguimiento='';
if(!$LINK=mysql_connect('localhost','aoacol_arturo','KXMd4v9GQup7')) die('Problemas con la conexion de la base de datos!');
if(!mysql_select_db('aoacol_aoacars',$LINK)) die('Problemas con la seleccion de la base de datos');

if($Seguimientos=mysql_query("select si.numero,se.*,t_tipo_seguimiento(se.tipo) as ntipo
								From aoacol_aoacars.seguimiento se,aoacol_aoacars.siniestro si
								Where se.siniestro=si.id and se.enviado_ws=0 and si.aseguradora=4",$LINK))
{
	$Cantidad_devueltos=0;
	while($Seg=mysql_fetch_object($Seguimientos))
	{
		if(is_numeric($Seg->numero))
		{
			echo "<br />$Seg->numero ok.";
			$Cadena_seguimiento.=$Seg->numero.';'.$Seg->fecha.';'.$Seg->hora.';'.$Seg->usuario.';'.str_replace(';',',',$Seg->descripcion).';'.$Seg->ntipo.'|';
			mysql_query("update aoacol_aoacars.seguimiento set enviado_ws=1 where id=$Seg->id ",$LINK);
			$Cantidad_devueltos++;
		}
		else
		{
			echo "<br />$Seg->numero ignorado.";
			mysql_query("update aoacol_aoacars.seguimiento set enviado_ws=1 where id=$Seg->id ",$LINK);
		}
	}
	mysql_close($LINK);
}

if($Cadena_seguimiento)
{
	echo "<br />Cadena para enviar: <br /><br />$Cadena_seguimiento<br /><br />";
	$Resultado=$client->call('informaEstadoSiniestros',array('arg0'=>$token,'arg1'=>$Cadena),WS_NAMESPACE);
	$err = $client->getError();
	if($err) die("Error: $err");

	$Cadena_emaild="<html><body>Control de envio WS<br><br>Esta es la cadena de seguimiento que se entregó al WS mapfre hoy ".date('Y-m-d H:i:s');
	$Cadena_emaild.="<br>Número de registros: $Cantidad_devueltos<br><br>".$Cadena_seguimiento;
	$Cadena_emaild.="<br><br>Token:  $token";


	$Envio1=enviar_gmail('sistemas@aoacolombia.com' /*de */,
										'Sistemas AOA Colombia' /*Nombre de */ ,
										$EMAIL2 /*para */,
										"" /*con copia*/,
										"Mapfre Webservice" /*Objeto */,
										$Cadena_emaild);
}



$Cadena_email.='<br><br>Cordialmente, <br><br><B>GESTION DE PROCESOS<BR>AOA COLOMBIA S.A.</B>'.
    '<br><br>Webservice de consumo:  '.WS_SERVER.' <br><br>'.
    'Este mail es para control de calidad de la comunicación tecnológica entre AOA y Mapfre.<br> Cualquier inquietud por favor comunicarla a: '.
    'Arturo Quintero Rodriguez Teléfono 6293096 3176562730 <br>Gestor de Procesos AOA Colombia S.A. <br><br>'.$token.'</body></html>';
// envio del mail automatico
if($Cantidad_ahora)
{
	$Cadena_email="<html><body>Estimados señores de MAPFRE:<br><br>A continuación se relaciona la obtención de información del webservice:<br><br>".
		"<hr>Fecha: $FECHA $Cantidad_ahora Siniestros nuevos<br><table border cellspacing=0><tr><th>Siniestro</th><th>Fecha siniestro</th><th>Fecha declaración</th><th>Placa</th><th>Ciudad Origen</th><th>Ingreso a AOA</th><th>CodC</th></tr>".
		$Cadena_email;
	$Envio1=enviar_gmail('sistemas@aoacolombia.com' /*de */,
										'Sistemas AOA Colombia' /*Nombre de */ ,
										$EMAIL /*para */,
										"" /*con copia*/,
										"Mapfre Webservice" /*Objeto */,
										$Cadena_email);
}
else
{
	$Cadena_email="<html><body>Estimados señores de MAPFRE:<br><br>A continuación se relaciona la obtención de información del webservice:<br><br>".
	"<hr>Fecha: $FECHA 0 Siniestros nuevos<br><br>".$Cadena_email;
	$Envio1=enviar_gmail('sistemas@aoacolombia.com' /*de */,
											'Sistemas AOA Colombia' /*Nombre de */ ,
											$EMAIL1 /*para */,
											"" /*con copia*/,
											"Mapfre Webservice" /*Objeto */,
											$Cadena_email);
}


echo "<br />Resultado $FECHA: $Cantidad_ahora";
?>