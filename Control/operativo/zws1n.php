<?php

/*
   Ultimas actualizaciones:  Octubre 4 2011 11:07  Verificación de varios siniestros con la misma placa., Inclusión en el email de la variable de chevyseguro y elmiminación de tildes.

*/
$MODO='Normal'; //  Normal / Seguimiento por email

include('inc/funciones_.php');
include('inc/webservice/nusoap.php');
//include('inc/Mail/SMTP.php');

// DEFINICION DE VARIABLES DE CONSUMO Y AUTENTICACION DEL WEBSERVICE
define('WS_USUARIO','WSSINI');
define('WS_PASSWORD','contrasena');
define('WS_COMPANIA','1');
define('WS_NEGOCIO','500');
define('WS_NAMESPACE','http://service.servicios.mapfre.com.co/');
define('WS_SERVER','http://contenido.mapfre.com.co/MapfreServices/ws?wsdl');
//define('WS_SERVER','http://10.192.20.14:8081/ServicioVentas/ws?wsdl');
//define('WS_SERVER','http://10.192.16.22:8400/MapfreServices/ws?wsdl');
$RAMO=array('101'=>'TREBOL ELITE','110'=>'TREBOL PARTICULAR','111'=>'TREBOL PUBLICO','112'=>'PARA LA MUJER','113'=>'PARA LA FAMILIA','115'=>'SUPER TREBOL',
'116'=>'SUPER TREBOL','117'=>'SUPER TREBOL','118'=>'SUPER TREBOL','120'=>'SERVICIO PUBLICO ESPECIAL','121'=>'VEHICULOS CERO KILOMETROS','122'=>'VEHICULOS KIA CERO KILOMETROS',
'123'=>'CODENSA AUTOS','125'=>'TREBOL MODULAR INDIVIDUAL','138'=>'MERCADEO MASIVO','140'=>'FINANCIERA','141'=>'SERVICIO PUBLICO',
'142'=>'SERVICIO PUBLICO ESPECIAL','150'=>'FINANCIERO INDIVIDUAL','155'=>'POLIZA COLECTIVA AUTOMOVILES','158'=>'GARANTIA EXTENDIDA','159'=>'POLIZA DE AUTOMOVILES ANDINA',
'161'=>'COLECTIVA PESADOS-SEMIPESADOS','162'=>'POLIZA COLECTIVA MOTOS','163'=>'POLIZA COLECTIVA TAXIS','164'=>'COLECTIVA GRUAS','165'=>'COLECTIVA VOLQUETAS',
'166'=>'COLECTIVA LICITACIONES','167'=>'RESPONSABILIDAD CIVIL');

//$EMAIL="arturoquintero@aoacolombia.com,Arturo Quintero;gabrielsandoval@aoacolombia.com,Gabriel Sandoval";
$EMAIL="jesusvega@aoacolombia.com,Jesus Vega;sergiocastillo@aoacolombia.com,Sergio Castillo";
//$EMAIL1="arturoquintero@aoacolombia.com,Arturo Quintero";
$EMAIL1="jesusvega@aoacolombia.com,Jesus Vega";
// proceso de conexión
//echo "Conectando el cliente... ";
$client = new nusoap_client(WS_SERVER,false,false,false,false,false,600,600);
$client->http_encoding = 'UTF-8';
$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = false;
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
										"sergiocastillo@aoacolombia.com,Sergio castillo" /*para */,
										"" /*con copia*/,
										"Error Mapfre Webservice" /*Objeto */,
										"Error en el consumo webservice $Cadena.");
	  die();
}
// USUARIO AUTENTICADO CORRECTAMENTE
//echo "<br />Autenticado.";
// proceso de lectura de siniestros
$FECHA=date('Ymd');
$DIA=array();
//echo "<br />Buscando siniestros nuevos: ";
//echo "<br>Buscando el dia: ";
$Resultado=$client->call('leeSiniestros',array('arg0'=>date('Ymd',strtotime($FECHA)),'arg1'=>$token), WS_NAMESPACE);
//print_r($client);
//print_r($Resultado);
$err = $client->getError();
if($err) $Resultado="Error: $err";
//echo "<br />Conexion local..";
$Cadena_email="";
$Cantidad=0;
if(!$LINK=mysql_connect('localhost','aoacol_arturo','AOA0l1lwpdaa')) die('Problemas con la conexion de la base de datos!');
if(!mysql_select_db('aoacol_aoacars',$LINK)) die('Problemas con la seleccion de la base de datos');
//echo "Tipo de respuesta:".gettype($Resultado)."<br />";
$Correo_repetidos='';   // variable para acumular los siniestros de placas que ya tengan siniestros previos en la base de datos.  Objetivo: buscar pendientes para informar y hacer el respectivo ajuste.
if(gettype($Resultado)=='text')
{
	//print_r($Resultado);
	// Num_sini;Num_exp;Cod_ramo;Num_poliza;Cod_docum_aseg;Nombre;Tlf_numero;Tlf_numero2;Num_spto;Fec_sini;Fec_denu_sini;COD_PLACA;COD_FASECOLDA;COD_CHASIS;COD_MODELO;COD_POSTAL_RADIC;COD_LINEA
	$Siniestros=explode('|',$Resultado);
	for($i=0;$i<count($Siniestros);$i++);
	{
		$S=explode(';',$Siniestros[$i]);
		$Numero_siniestro=$S[0];
		$Expediente=$S[1];
		$Ramo=$S[2];
		$Poliza=$S[3];
		$IdAsegurado=$S[4];
		$NomAsegurado=$S[5];
		$Telefono_declarante=$S[6];
		$Celular_declarante=$S[7];
		//  Num_spto???
		$Fec_siniestro=$S[9];
		$Fec_denuncia=$S[10];
		$Placa=$S[11];
		$Codigo_fasecolda=$S[12];
		$Codigo_chasis=$S[13];
		$Modelo=$S[14];
		$codCiudad=$S[15];
		$Telefono_conductor=$S[17];
		$Celular_conductor=$S[18];
		$Telefono_asegurado=$S[19];
		$Email_asegurado=$S[20];
		$Nombre_conductor=$S[21];
		$Chevyseguro=($S[22]=='SI'?1:0);
		$Ciudad_siniestro=$S[23];
	}
}
elseif(gettype($Resultado)=='array')
{	
  // si es un solo arreglo, lo convierte en un arreglo dentro de otro arreglo para poderlo procesar
  if($Resultado['num_sini']) { $Resultadonuevo=$Resultado; $Resultado=array(); $Resultado[0]=$Resultadonuevo; }
  // -----------------------------------------------------------------------------------------------
  if($Cantidad=count($Resultado))
  {
	  	$Cadena_email.="<hr>Fecha: $FECHA $Cantidad Siniestros<br><table border cellspacing=0><tr><th>#</th><th>Siniestro</th><th>Fecha siniestro</th><th>Fecha declaracion</th><th>Placa</th><th>Ciudad Origen</th><th>Ingreso a AOA</th><th>Codigo C</th><th>ChevySeguro</th></tr>";
	    for($i=0;$i<count($Resultado);$i++)
	    {
			$Numero_siniestro=$Resultado[$i]['num_sini'];
			$codCiudad=$Resultado[$i]['COD_POSTAL_RADIC'].'000';
			$CiuOrig=$codCiudad;
			// ajuste automatico a algunas ciudades que vienen con el codigo incompleto
			$codCiudad=str_pad($codCiudad,8,'0',STR_PAD_LEFT);
			$Departamento=substr($codCiudad,0,2);
			if($Ofic=qo1m("select oficina from aoacol_aoacars.corresp_ofic where left(departamento,2)='$Departamento' ",$LINK)) $Ciudad=$Ofic;
			else $Ciudad=$codCiudad;
			$Ciudad_siniestro=$Resultado[$i]['zzCOD_POSTAL_SINI'].'000';
			//-------------------------------------------------------------------------------------------
			$Fec_denuncia=substr($Resultado[$i]['fec_denu_sini'],0,4).'-'.substr($Resultado[$i]['fec_denu_sini'],8,2).'-'.substr($Resultado[$i]['fec_denu_sini'],5,2);
			$Fec_siniestro=substr($Resultado[$i]['fec_sini'],0,4).'-'.substr($Resultado[$i]['fec_sini'],8,2).'-'.substr($Resultado[$i]['fec_sini'],5,2);
			$Poliza=$Resultado[$i]['num_poliza'];
			$Ramo=$RAMO[$Resultado[$i]['cod_ramo']];
			$total=$Resultado[$Resultado[$i]['NOM_EXP']];
			$Codigo_chasis=$Resultado[$i]['COD_CHASIS'];
			$Compania=$Resultado[$i]['cod_cia'];
			$Placa=$Resultado[$i]['COD_PLACA'];
			$Linea=$Resultado[$i]['COD_LINEA'];
			$Modelo=$Resultado[$i]['COD_MODELO'];
			$NomAsegurado=$Resultado[$i]['nombre'];
			$IdAsegurado=$Resultado[$i]['cod_docum_aseg'];
			$Expediente=$Resultado[$i]['num_exp'];
			$Codigo_fasecolda=$Resultado[$i]['COD_FASECOLDA'];
			$Telefono_conductor=$Resultado[$i]['TLF_NUMERO_CONDUCTOR'];
			$Celular_conductor=$Resultado[$i]['TLF_MOVIL_CONDUCTOR'];
			$Telefono_asegurado=$Resultado[$i]['CEL_SINI_ASEG'];
			$Email_asegurado=$Resultado[$i]['MAIL_SINI_ASEG'];
			$Nombre_Conductor=$Resultado[$i]['NOM_CONDUCTOR'];
			$Telefono_declarante=$Resultado[$i]['tlf_numero'];
			$Celular_declarante=$Resultado[$i]['tlf_movil'];
			$dias_servicio = 7;
			$ChevySeguro=($Resultado[$i]['ZZCHEVYSEGURO']=='SI'?1:0);
			if($ChevySeguro==0)
			{
				$Especial=mysql_query("select id from placa_especial where placa like '%$Placa%' ",$LINK);
				$ChevySeguro=(mysql_num_rows($Especial)>0?1:0);
			}
			
			if($Numero_siniestro)
			{
				$futura_placa = qom("select * from aoacol_aoacars.futuras_placa_mapfre where placa= '".$Placa."'",$LINK);
				if(isset($futura_placa->placa))
				{
					$dias_servicio = 10;	
				}
				
				echo "<br />Siniestro $Numero_siniestro";
		        $Ahora=date('Y-m-d H:i:s');
		        if(!mysql_query("insert ignore into aoacol_aoacars.siniestro (total, aseguradora,numero,ciudad,ciudad_original,fec_autorizacion,fec_siniestro,fec_declaracion,poliza,
		             sucursal_radicadora,intermediario,estado,placa,linea,modelo,asegurado_nombre,asegurado_id,expediente,
		             fasecolda,declarante_nombre,declarante_id,conductor_tel_resid,conductor_celular,conductor_nombre,
		             declarate_tel_otro,declarante_email,declarante_telefono,declarante_celular, ingreso,chevyseguro,ciudad_siniestro,dias_servicio)
		             values ( '$perdida_total','4','$Numero_siniestro','$Ciudad','$codCiudad','$Ahora','$Fec_siniestro','$Fec_denuncia','$Poliza',
		             '$Ramo','$Codigo_chasis:$Compania','5','$Placa','$Linea','$Modelo','$NomAsegurado','$IdAsegurado','$Expediente',
		             '$Codigo_fasecolda','$NomAsegurado','$IdAsegurado','$Telefono_conductor','$Celular_conductor','$Nombre_Conductor',
		             '$Telefono_asegurado','$Email_asegurado','$Telefono_declarante','$Celular_declarante', '$Ahora','$ChevySeguro','$Ciudad_siniestro','$dias_servicio')",$LINK)) {echo mysql_error($LINK);mysql_close($LINK);die();}
				if($Varios=mysql_query("select t_aseguradora(aseguradora) as naseg,numero,fec_autorizacion,t_estado_siniestro(estado) as nest,asegurado_nombre,ingreso,t_ciudad(ciudad) as nciu
														from siniestro where placa='$Placa' order by ingreso ",$LINK))
				{
					if(mysql_num_rows($Varios)>1)
					{
						$Correo_repetidos.="<tr ><td >Placa: $Placa</td></tr>";
						while($Var=mysql_fetch_object($Varios))
						{
							$Correo_repetidos.="<tr ><td >$Var->naseg</td><td >$Var->numero</td><td >$Var->fec_autorizacion</td><td >$Var->nest</td><td >$Var->asegurado_nombre</td><td >$Var->ingreso</td><td >$Var->nciu</td></tr>";
						}
					}
				}
				$Ciu=qo1m("select t_ciudad('$codCiudad')",$LINK);
			    $Cadena_email.="<tr><td align='right'>".($i+1)."</td><td>$Numero_siniestro</td><td>$Fec_siniestro</td><td>$Ahora</td><td>$Placa</td><td>$Ciu</td><td>$Ahora</td><td>$CiuOrig</td><td >$ChevySeguro</td></tr>";
			}
	    }
	    $Cadena_email.="</table>";
  }
  else
  {
    $Cadena_email.="<hr>Fecha:$FECHA 0 siniestros.";
  }
}
mysql_close($LINK);

if($MODO=='Seguimiento')
{
	$Envio=enviar_gmail('sistemas@aoacolombia.com' /*de */,'Sistemas AOA Colombia' /*Nombre de */ ,($Cantidad?$EMAIL:$EMAIL1) /*para */,"" /*con copia*/,"Mapfre Webservice" /*Objeto */,$Cadena_email);
}
else
{
	q("insert into web_service (fecha,aseguradora,descripcion) values ('".date('Y-m-d H:i:s')."','4',\"$Cadena_email\")");
}
if($Correo_repetidos)
{
	$Cadena_emailr="<body>Correo de verificacion de siniestros con placas repetidas.<br>A continuacion se relacionan las placas repetidas:<br>".
		"<table border cellspacing='0'><tr ><th >Aseguradora</th><th >Numero</th><th >Fec.Autorizacion</th><th >Estado</th><th >Asegurado</th><th >Ingreso</th><th >Ciudad</th></tr>".
		$Correo_repetidos."</table><br><br>Cordialmente,<br><br>Sergio Castillo Castro.<br>Director de Tecnologia y Planeacion<br>AOA Colombia S.A.<br>arturoquintero@aoacolombia.com</body></html>";
	echo "<br />Enviando el correo de repetidos.";
	$Envio=enviar_gmail('sistemas@aoacolombia.com' /*de */,
		'Sistemas AOA Colombia' /*Nombre de */ ,
		"sergiocastillo@aoacolombia.com,Sergio Castillo;siniestros@aoacolombia.com,Gestor de Siniestros" /*para */,
		"sandraosorio@aoacolombia.com,Sandra Osorio" /*con copia*/,
		"Siniestros con la misma placa." /*Objeto */,
		$Cadena_emailr);
}

///  CONTEO CADA HORA DE CASOS PROCESADOS POR CONSULTOR

//include('inc/funciones_.php');
$Hoy=date('Y-m-d');
include('inc/link.php');
mysql_query("insert ignore into call2est_diaria (fecha,agente) select distinct '$Hoy',agente from call2proceso where date_format(fecha,'%Y-%m-%d') = '$Hoy' ",$LINK);
$Agentes=mysql_query("select agente from call2est_diaria where fecha='$Hoy' ",$LINK);
while($A=mysql_fetch_object($Agentes))
{
	$Ag=qom("select nombre,nivel from usuario_callcenter where id=$A->agente",$LINK);
	$Gestionados=mysql_query("select distinct siniestro from call2proceso where date_format(fecha,'%Y-%m-%d')='$Hoy' and agente=$A->agente",$LINK);
	$Sing='';while($Ges=mysql_fetch_object($Gestionados)) $Sing.=($Sing?',':'').$Ges->siniestro;
	$Cantidad_gestionados=mysql_num_rows($Gestionados);unset($Gestionados);
	if($Ag->nivel==1)
	{
		$Efectivos=mysql_query("select distinct siniestro from seguimiento where siniestro in ($Sing) and tipo = 17 and usuario='$Ag->nombre' ",$LINK);
		$Cantidad_efectivos=mysql_num_rows($Efectivos); unset($Efectivos);
	}
	elseif($Ag->nivel==2)
	{
		$Efectivos=mysql_query("select distinct siniestro from seguimiento where siniestro in ($Sing) and tipo = 5 and usuario='$Ag->nombre' ",$LINK);
		$Cantidad_efectivos=mysql_num_rows($Efectivos); unset($Efectivos);
	}
	else $Cantidad_efectivos=0;
	$Escalafon=qo1m("select sum puntaje from call2escalafon where agente=$A->agente and date_format(fecha,'%Y-%m-%d')='$Hoy' ",$LINK);
	mysql_query("update call2est_diaria set gestionados='$Cantidad_gestionados',efectivos='$Cantidad_efectivos',
		nivel='$Ag->nivel',escalafon='$Escalafon' where fecha='$Hoy' and agente='$A->agente' ",$LINK);
}
$Primer_dia_semana=primer_dia_de_semana($Hoy);

mysql_query("insert ignore into call2est_semanal (fecha,agente) select distinct '$Primer_dia_semana',agente from call2proceso where date_format(fecha,'%Y-%m-%d') between '$Primer_dia_semana' and '$Hoy' ",$LINK);
$Agentes=mysql_query("select agente from call2est_semanal where fecha='$Primer_dia_semana' ",$LINK);
while($A=mysql_fetch_object($Agentes))
{
	$Ag=qom("select nombre,nivel from usuario_callcenter where id=$A->agente",$LINK);
	$Gestionados=mysql_query("select distinct siniestro from call2proceso where date_format(fecha,'%Y-%m-%d') between '$Primer_dia_semana' and '$Hoy' and agente=$A->agente",$LINK);
	$Cantidad_gestionados=mysql_num_rows($Gestionados);
	$Sing='';while($Ges=mysql_fetch_object($Gestionados)) $Sing.=($Sing?',':'').$Ges->siniestro;
	unset($Gestionados);
	if($Ag->nivel==1)
	{
		$Efectivos=mysql_query("select distinct siniestro from seguimiento where siniestro in ($Sing) and tipo = 17 and usuario='$Ag->nombre' ",$LINK);
		$Cantidad_efectivos=mysql_num_rows($Efectivos); unset($Efectivos);
	}
	elseif($Ag->nivel==2)
	{
		$Efectivos=mysql_query("select distinct siniestro from seguimiento where siniestro in ($Sing) and tipo = 5 and usuario='$Ag->nombre' ",$LINK);
		$Cantidad_efectivos=mysql_num_rows($Efectivos); unset($Efectivos);
	}
	else $Cantidad_efectivos=0;
	$Escalafon=qo1m("select sum puntaje from call2escalafon where agente=$A->agente and date_format(fecha,'%Y-%m-%d') between '$Primer_dia_semana' and '$Hoy' ",$LINK);
	mysql_query("update call2est_semanal set gestionados='$Cantidad_gestionados',efectivos='$Cantidad_efectivos',
		nivel='$Ag->nivel',escalafon='$Escalafon' where fecha='$Primer_dia_semana' and agente='$A->agente' ",$LINK);
}
mysql_close($LINK);
?>