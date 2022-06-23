<?php
include('inc/funciones_.php');
// DEFINICION DE VARIABLES DE CONSUMO Y AUTENTICACION DEL WEBSERVICE
define('WS_USUARIO','WSSINI');
define('WS_PASSWORD','contrasena');
define('WS_COMPANIA','1');
define('WS_NEGOCIO','500');
define('WS_NAMESPACE','http://service.servicios.mapfre.com.co/');
//define('WS_SERVER','http://10.192.16.22:8400/MapfreServices/ws?wsdl');
define('WS_SERVER','http://www.mapfre.com.co/MapfreServices/ws?wsdl');
//define('WS_SERVER','http://10.192.20.14:8081/ServicioVentas/ws?wsdl');

$RAMO=array(
'101'=>'TREBOL ELITE',
'110'=>'TREBOL PARTICULAR',
'111'=>'TREBOL PUBLICO',
'112'=>'PARA LA MUJER',
'113'=>'PARA LA FAMILIA',
'115'=>'SUPER TREBOL',
'116'=>'SUPER TREBOL',
'117'=>'SUPER TREBOL',
'118'=>'SUPER TREBOL',
'120'=>'SERVICIO PUBLICO ESPECIAL',
'121'=>'VEHICULOS CERO KILOMETROS',
'122'=>'VEHICULOS KIA CERO KILOMETROS',
'123'=>'CODENSA AUTOS',
'125'=>'TREBOL MODULAR INDIVIDUAL',
'138'=>'MERCADEO MASIVO',
'140'=>'FINANCIERA',
'141'=>'SERVICIO PUBLICO',
'142'=>'SERVICIO PUBLICO ESPECIAL',
'150'=>'FINANCIERO INDIVIDUAL',
'155'=>'POLIZA COLECTIVA AUTOMOVILES',
'158'=>'GARANTIA EXTENDIDA',
'159'=>'POLIZA DE AUTOMOVILES ANDINA',
'161'=>'COLECTIVA PESADOS-SEMIPESADOS',
'162'=>'POLIZA COLECTIVA MOTOS',
'163'=>'POLIZA COLECTIVA TAXIS',
'164'=>'COLECTIVA GRUAS',
'165'=>'COLECTIVA VOLQUETAS',
'166'=>'COLECTIVA LICITACIONES',
'167'=>'RESPONSABILIDAD CIVIL'
);

if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}

menuws();
die();


function menuws()
{
  html(TITULO_APLICACION.' - '.$_SESSION['Nombre']);
  echo "
    <script language='javascript'>

    var REG='';

    function pinta()
    {
      var DD=document.getElementById('ws_trae').contentWindow.document;
      DD.open('text/html','replace');
      DD.write('<html><h3>Resultado:</h3>');
      DD.write(REG);
      DD.close();
      enviar_post();
    }
    function enviar_post()
    {
      if(REG.length)
      {
        window.open('ws.php?Acc=genera_post','ws_trae');
      }
    }

    </script>
    <body onload='centrar();'>
    <h3 align='center'>AOA COLOMBIA S.A. - CONSUMO DE WEBSERVICE DE MAPFRE S.A.</H3>
    <br>
    <a href='http://10.192.16.22:8400/MapfreServices/ws?wsdl' target='ws_trae'>MapfreServices</a>
    <a href='http://10.192.16.22:8400/MapfreServices/ws?xsd=1' target='ws_trae'>Esquema</a>
    <a href='http://10.192.20.14:8081/ServicioVentas/ws?wsdl' target='ws_trae'>ServicioVentas</a>
    <br>
    <a href='http://www.mapfre.com.co/MapfreServices/ws?wsdl' target='ws_trae'>MapfreServices</a>
    <a href='http://www.mapfre.com.co/MapfreServices/ws?xsd=1' target='ws_trae'>Esquema</a>

    <br><br>
    <form action='ws.php' method='post' target='ws_trae' name='forma' id='forma'>
    Traer Siniestros desde la feha: ".pinta_FC('forma','FECHA1',date('Y-m-d'))." hasta ".pinta_FC('forma','FECHA2',date('Y-m-d'))."<br>
    <br>
    Email para enviar resumen: <input type='text' name='EMAIL'
    value='arturoquintero@aoacolombia.com,anvalbu@mapfre.com.co,wfsilva@mapfre.com.co,germangonzalez@aoacolombia.com' size=200><br>
    <br>Marque esta casilla si quiere hacer la actualización en línea <input type='checkbox' name='enlinea' checked><br>
    <br>
    <input type='submit' value='CONTINUAR'>
    <input type='hidden' name='Acc' id='Acc' value='procesa_ws'>
    </form>
    <br><br>
    <iframe name='ws_trae' id='ws_trae' height='400' width='100%'></iframe>
    </body>
    </html>";
}

function genera_post()
{
  echo "<script language='javascript'>
      function carga()
      {
        var d=document.forma;
        d.Contenido.value=parent.REG;
        d.submit();
      }
    </script>
    <body onload='carga()'>
    <form action='http://app.aoacolombia.com/utilidades/Recepta/zrecibe_siniestro_post.php'
      method='post' target='_self' name='forma' id='forma'>
      <textarea name='Contenido' id='Contenido'></textarea>
    </form>
    </body></html>";
}


function procesa_ws()
{
  global $FECHA1,$FECHA2,$EMAIL,$CONEXION,$enlinea;
  $enlinea=sino($enlinea);
  echo "<body>";
  require_once('inc/webservice/nusoap.php');
  // proceso de conexión
  $client = new nusoap_client(WS_SERVER,FALSE);
  $err = $client->getError();
  if ($err) die('<h2>Constructor error</h2><pre>' . $err . '</pre><h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>');
  // procdeso de autenticación
  $params = array('arg0'=>WS_USUARIO, 'arg1'=>WS_PASSWORD,'arg2'=>WS_COMPANIA, 'arg3'=>WS_NEGOCIO);
  $token = $client->call('autentica', $params, WS_NAMESPACE );
  if ($client->fault) die("<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>$token</pre>");
  $err = $client->getError();if ($err) die("<h2>Error</h2><pre>$err </pre>");
  // fin proceso de autenticación
	echo '<h2>Procedimiento de autenticación iniciado correctamente</h2>Resultado de la autenticación: ';
	if($token=='AUDS' || $token=='AUNV' || $token=='AUNE')
	{
		if($token=='AUDS') $Cadena='ACCESO USUARIO DESAUTORIZADO';
		if($token=='AUNV') $Cadena='ACCESO USUARIO NO VALIDO';
		if($token=='AUNE') $Cadena='ACCESO USUARIO NO EXISTENTE';

		$Envio=enviar_mail(
      'sistemas@aoacolombia.com' /* de */,
      'SISTEMAS AOA' /*nombre de */,
      $EMAIL /*para*/,
      'Consumo WebService Mapfre' /*subject*/ ,
      "Error en el consumo Webservice: $Cadena " /*contenido*/);
      die();
	}
	echo "USUARIO AUTENTICADO CORRECTAMENTE";
	// proceso de lectura de siniestros
	ECHO "<H2>Procedimiento de lectura de siniestros iniciado<h2>";
	$FECHA=date('Ymd',strtotime($FECHA1));
	$DIA=array();
	$Contador=0;
	echo "<br>Buscando el dia: ";
	while(strtotime($FECHA)<=strtotime($FECHA2))
	{
		echo "$FECHA, ";
		$ResultadoDia=$client->call('leeSiniestros',array('arg0'=>date('Ymd',strtotime($FECHA)),'arg1'=>$token), WS_NAMESPACE);
		echo $ResultadoDia['COD_PLACA'];
		$DIA[$Contador]=$ResultadoDia;
		$err = $client->getError();
		if($err) $DIA[$Contador]="Error: $err";
		$Contador++;
		$FECHA=aumentadias($FECHA,1);
	}
  $Post='';
  $Cadena_email="<html><body>Estimados señores de MAPFRE:<br><br>".
    "A continuación se relaciona la obtención de información del webservice:<br><br>";
  $FECHA=date('Ymd',strtotime($FECHA1));
  $CSD=0;
  if($enlinea)
  {
    if(!$LINK=mysql_connect('localhost','aoacol_arturo','KXMd4v9GQup7')) die('Problemas con la conexion de la base de datos!');
  }
  for($i=0;$i<count($DIA);$i++)
  {
    //$CSD+=pinta_resultado($DIA[$i],$Cadena_email,$FECHA,$Post,$enlinea);
    $Resultado=$DIA[$i];
    echo "<br>$Resultado";
    if(gettype($Resultado)=='array')
    {
      if($Resultado['num_sini'])
  	{
  		$Resultadonuevo=$Resultado;
  		$Resultado=array();
  		$Resultado[0]=$Resultadonuevo;
  	}
    if($Cantidad=count($Resultado))
    {
      $CSD+=$Cantidad;
      echo "<hr>Fecha: $FECHA $Cantidad Siniestros.";
      echo "<br><table border cellspacing=0><tr>
      <th>Pos</th><th>Chasis</th><th>Cód Fasecolda</th><th>Línea</th><th>Modelo</th><th>PLACA</th><th>Cód Postal Radicado</th><th>Ramo</th>
      <th>ID. ASEGURADO</th><th>Compañía</th><th>FEC.DECL.SINIESTRO</TH><th>FECHA SINIESTRO</TH><th>NOMBRE ASEGURADO</TH><th>Num. Exp.</TH>
      <th>NUMERO POLIZA</TH><th>NUMERO SINIESTRO</TH><th>TELEFONO MOVIL</TH><th>TELEFONO FIJO</TH></TR>";

      $Cadena_email.="<hr>Fecha: $FECHA $Cantidad Siniestros<br>".
      "<table border cellspacing=0><tr><th>#</th><th>Siniestro</th><th>Fecha siniestro</th><th>Fecha declaración</th><th>Placa</th><th>Ciudad Origen</th><th>Ingreso a AOA</th></tr>";
      for($i=0;$i<count($Resultado);$i++)
      {
        echo "<TR>
          <td align='right'>".($i+1)."</td>
          <td align='center'>".$Resultado[$i]['COD_CHASIS']."</td>
          <td align='center'>".$Resultado[$i]['COD_FASECOLDA']."</td>
          <td align='center'>".$Resultado[$i]['COD_LINEA']."</td>
          <td align='center'>".$Resultado[$i]['COD_MODELO']."</td>
          <td align='center'>".$Resultado[$i]['COD_PLACA']."</td>
          <td align='center'>".$Resultado[$i]['COD_POSTAL_RADIC']."</td>
          <td align='center'>".$Resultado[$i]['cod_ramo']."</td>
          <td align='right' >".$Resultado[$i]['cod_docum_aseg']."</td>
          <td align='center'>".$Resultado[$i]['cod_cia']."</td>
          <td align='center'>".$Resultado[$i]['fec_denu_sini']."</td>
          <td align='center'>".$Resultado[$i]['fec_sini']."</td>
          <td align='center'>".$Resultado[$i]['nombre']."</td>
          <td align='center'>".$Resultado[$i]['num_exp']."</td>
          <td align='center'>".$Resultado[$i]['num_poliza']."</td>
          <td align='center'>".$Resultado[$i]['num_sini']."</td>
          <td align='center'>".$Resultado[$i]['tlf_movil']."</td>
          <td align='center'>".$Resultado[$i]['tlf_numero']."</td>
         ";
          if($enlinea)
          {
            $Numero_siniestro=$Resultado[$i]['num_sini'];
            $codCiudad=$Resultado[$i]['COD_POSTAL_RADIC'].'000';
            // ajuste automatico a algunas ciudades que vienen con el codigo incompleto
          	$codCiudad=str_pad($codCiudad,8,'0',STR_PAD_LEFT);
            $Departamento=substr($codCiudad,0,2);
            if($Ofic=qo1m("select oficina from aoacol_aoacars.corresp_ofic where left(departamento,2)='$Departamento' ",$LINK))
            	$Ciudad=$Ofic;
            else
            	$Ciudad=$codCiudad;
            //-------------------------------------------------------------------------------------------
            echo "<td>$Ciudad</td>";
            echo "</tr>";
            $Fec_denuncia=substr($Resultado[$i]['fec_denu_sini'],0,4).'-'.substr($Resultado[$i]['fec_denu_sini'],8,2).'-'.substr($Resultado[$i]['fec_denu_sini'],5,2);
            $Fec_siniestro=substr($Resultado[$i]['fec_sini'],0,4).'-'.substr($Resultado[$i]['fec_sini'],8,2).'-'.substr($Resultado[$i]['fec_sini'],5,2);
            $Poliza=$Resultado[$i]['num_poliza'];
            $Ramo=$RAMO[$Resultado[$i]['cod_ramo']];
            $Codigo_chasis=$Resultado[$i]['COD_CHASIS'];
            $Compania=$Resultado[$i]['cod_cia'];
            $Placa=$Resultado[$i]['COD_PLACA'];
            $Linea=$Resultado[$i]['COD_LINEA'];
            $Modelo=$Resultado[$i]['COD_MODELO'];
            $NomAsegurado=$Resultado[$i]['nombre'];
            $IdAsegurado=$Resultado[$i]['cod_docum_aseg'];
            $Expediente=$Resultado[$i]['num_exp'];
            $Codigo_fasecolda=$Resultado[$i]['COD_FASECOLDA'];
            $Telefono=$Resultado[$i]['tlf_numero'];
            $Celular=$Resultado[$i]['tlf_movil'];
            if($Numero_siniestro)
            {
            	$Ahora=date('Y-m-d H:i:s');
              if(!mysql_query("insert ignore into aoacol_aoacars.siniestro (aseguradora,numero,ciudad,ciudad_original,fec_autorizacion,fec_siniestro,fec_declaracion,poliza,
		               sucursal_radicadora,intermediario,estado,placa,linea,modelo,asegurado_nombre,asegurado_id,expediente,
		               fasecolda,declarante_nombre,declarante_id,declarante_telefono,declarante_celular,ingreso)
		               values ('4','$Numero_siniestro','$Ciudad','$codCiudad','$Fec_denuncia','$Fec_siniestro','$Fec_denuncia','$Poliza',
		               '$Ramo','$Codigo_chasis:$Compania','5','$Placa','$Linea','$Modelo','$NomAsegurado','$IdAsegurado','$Expediente',
		               '$Codigo_fasecolda','$NomAsegurado','$IdAsegurado','$Telefono','$Celular','$Ahora')",$LINK)) {echo mysql_error($LINK);mysql_close($LINK);die();}
            	$ID1=mysql_insert_id($LINK);
            }
          }
 //         else
 //         {
 //             $Post.=$Resultado[$i]['COD_CHASIS'].'|'.$Resultado[$i]['COD_FASECOLDA'].'|'.$Resultado[$i]['COD_LINEA'].'|'.$Resultado[$i]['COD_MODELO'].
 //               '|'.$Resultado[$i]['COD_PLACA'].'|'.$Resultado[$i]['COD_POSTAL_RADIC'].'|'.$Resultado[$i]['cod_ramo'].'|'.$Resultado[$i]['cod_docum_aseg'].
 //               '|'.$Resultado[$i]['cod_cia'].'|'.$Resultado[$i]['fec_denu_sini'].'|'.$Resultado[$i]['fec_sini'].'|'.$Resultado[$i]['nombre'].
 //               '|'.$Resultado[$i]['num_exp'].'|'.$Resultado[$i]['num_poliza'].'|'.$Resultado[$i]['num_sini'].'|'.$Resultado[$i]['tlf_movil'].
 //               '|'.$Resultado[$i]['tlf_numero'].'¬';
 //         }
          $D=qom("select * from aoacol_aoacars.siniestro where aseguradora=4 and numero='".$Resultado[$i]['num_sini']."' ",$LINK);
          $Co=qom("select * from aoacol_aoacars.ciudad where codigo='$D->ciudad_original' ",$LINK);
          $Cadena_email.="<tr><td align='right'>".($i+1)."</td><td>$D->numero</td><td>$D->fec_siniestro</td><td>$D->fec_autorizacion</td><td>$D->placa</td>".
            "<td>$Co->departamento $Co->nombre</td><td>$D->ingreso</td></tr>";
        }
        $Cadena_email.="</table>";
        echo "</table>";
      }
      else
      {
        $Cadena_email.="<hr>Fecha:$FECHA 0 siniestros.";
        echo "<hr>Fecha:$FECHA 0 siniestros.";
      }
    }
    else
    {
      $Cadena_email.="<br>Fecha:$FECHA 0 siniestros.";
      echo "<hr>Fecha:$FECHA 0 siniestros. $Resultado ";
    }
    $FECHA=aumentadias($FECHA,1);
  }
  mysql_close($LINK);
  $Cadena_email.='<br><br>Cordialmente, <br><br><B>GESTION DE PROCESOS<BR>AOA COLOMBIA S.A.</B>'.
    '<br><br>Webservice de consumo:  '.WS_SERVER.' <br><br>'.
    'Este mail es para control de calidad de la comunicación tecnológica entre AOA y Mapfre.<br> Cualquier inquietud por favor comunicarla a: '.
    'Arturo Quintero Rodriguez Teléfono 6293096 3176562730 <br>Gestor de Procesos AOA Colombia S.A. <br><br>'.$token.'</body></html>';
  // envio del mail automatico
	include_once('inc/Mail/SMTP.php');
	if($EMAIL && $CSD)
	{
		$c=SMTP::Connect('mail.aoacolombia.com',25,'sistemas@aoacolombia.com','Sistemas2010');
		$m="From: sistemas@aoacolombia.com\nTo: $EMAIL\nSubject: Mapfre Webservice\nContent-Type: text/html\n\n".$Cadena_email;
		$Email=split(',',$EMAIL);
		$s1=SMTP::Send($c,$Email,$m,'sistemas@aoacolombia.com');
		if($s1) echo "<h2>Envio exitoso del mail a $EMAIL </h2>";
		else echo '<h2>Ocurrio un problema con el envio del mail</h2>';
	}
	$c=SMTP::Connect('mail.aoacolombia.com',25,'sistemas@aoacolombia.com','Sistemas2010');
	$m="From: sistemas@aoacolombia.com\nTo: arturoquintero@aoacolombia.com\nSubject: Mapfre Webservice\nContent-Type: text/html\n\n".$Cadena_email;
	$s2=SMTP::Send($c,array('arturoquintero@aoacolombia.com'),$m,'sistemas@aoacolombia.com');
	if($s2) echo "<br />Enviado a: arturoquintero@aoacolombia.com";
}

/*
function pinta_resultado($Resultado,&$Cadena_email,$FECHA,&$P,$enlinea)
{
  global $RAMO;
  $Cantidad_siniestros_por_dia=0;
  echo "<br>$Resultado";
  if(gettype($Resultado)=='array')
  {
  	if($Resultado['num_sini'])
  	{
  		$Resultadonuevo=$Resultado;
  		$Resultado=array();
  		$Resultado[0]=$Resultadonuevo;
  	}
    if($Cantidad=count($Resultado))
    {
      $Cantidad_siniestros_por_dia=$Cantidad;
      echo "<hr>Fecha: $FECHA $Cantidad Siniestros.";
      echo "<br><table border cellspacing=0><tr>
      <th>Pos</th>
      <th>Chasis</th>
      <th>Cód Fasecolda</th>
      <th>Línea</th>
      <th>Modelo</th>
      <th>PLACA</th>
      <th>Cód Postal Radicado</th>
      <th>Ramo</th>
      <th>ID. ASEGURADO</th>
      <th>Compañía</th>
      <th>FEC.DECL.SINIESTRO</TH>
      <th>FECHA SINIESTRO</TH>
      <th>NOMBRE ASEGURADO</TH>
      <th>Num. Exp.</TH>
      <th>NUMERO POLIZA</TH>
      <th>NUMERO SINIESTRO</TH>
      <th>TELEFONO MOVIL</TH>
      <th>TELEFONO FIJO</TH>
      </TR>";
      if($enlinea)
      {
        if(!$LINK=mysql_connect('localhost','aoacol_arturo','KXMd4v9GQup7')) die('Problemas con la conexion de la base de datos!');
      }

      $Cadena_email.="<hr>Fecha: $FECHA $Cantidad Siniestros<br>".
      "<table border cellspacing=0><tr><th>#</th><th>Siniestro</th><th>Fecha siniestro</th><th>Fecha declaración</th><th>Placa</th><th>Ciudad Origen</th><th>Ingreso a AOA</th></tr>";
      for($i=0;$i<count($Resultado);$i++)
      {
        echo "<TR>
          <td align='right'>".($i+1)."</td>
          <td align='center'>".$Resultado[$i]['COD_CHASIS']."</td>
          <td align='center'>".$Resultado[$i]['COD_FASECOLDA']."</td>
          <td align='center'>".$Resultado[$i]['COD_LINEA']."</td>
          <td align='center'>".$Resultado[$i]['COD_MODELO']."</td>
          <td align='center'>".$Resultado[$i]['COD_PLACA']."</td>
          <td align='center'>".$Resultado[$i]['COD_POSTAL_RADIC']."</td>
          <td align='center'>".$Resultado[$i]['cod_ramo']."</td>
          <td align='right' >".$Resultado[$i]['cod_docum_aseg']."</td>
          <td align='center'>".$Resultado[$i]['cod_cia']."</td>
          <td align='center'>".$Resultado[$i]['fec_denu_sini']."</td>
          <td align='center'>".$Resultado[$i]['fec_sini']."</td>
          <td align='center'>".$Resultado[$i]['nombre']."</td>
          <td align='center'>".$Resultado[$i]['num_exp']."</td>
          <td align='center'>".$Resultado[$i]['num_poliza']."</td>
          <td align='center'>".$Resultado[$i]['num_sini']."</td>
          <td align='center'>".$Resultado[$i]['tlf_movil']."</td>
          <td align='center'>".$Resultado[$i]['tlf_numero']."</td>
         ";
          if($enlinea)
          {
            $Numero_siniestro=$Resultado[$i]['num_sini'];
            $codCiudad=$Resultado[$i]['COD_POSTAL_RADIC'].'000';
            // ajuste automatico a algunas ciudades que vienen con el codigo incompleto
          	$codCiudad=str_pad($codCiudad,8,'0',STR_PAD_LEFT);
            $Departamento=substr($codCiudad,0,2);
            if($Ofic=qo1m("select oficina from aoacol_aoacars.corresp_ofic where left(departamento,2)='$Departamento' ",$LINK))
            	$Ciudad=$Ofic;
            else
            	$Ciudad=$codCiudad;
            //-------------------------------------------------------------------------------------------
            echo "<td>$Ciudad</td>";
            echo "</tr>";
            $Fec_denuncia=substr($Resultado[$i]['fec_denu_sini'],0,4).'-'.substr($Resultado[$i]['fec_denu_sini'],8,2).'-'.substr($Resultado[$i]['fec_denu_sini'],5,2);
            $Fec_siniestro=substr($Resultado[$i]['fec_sini'],0,4).'-'.substr($Resultado[$i]['fec_sini'],8,2).'-'.substr($Resultado[$i]['fec_sini'],5,2);
            $Poliza=$Resultado[$i]['num_poliza'];
            $Ramo=$RAMO[$Resultado[$i]['cod_ramo']];
            $Codigo_chasis=$Resultado[$i]['COD_CHASIS'];
            $Compania=$Resultado[$i]['cod_cia'];
            $Placa=$Resultado[$i]['COD_PLACA'];
            $Linea=$Resultado[$i]['COD_LINEA'];
            $Modelo=$Resultado[$i]['COD_MODELO'];
            $NomAsegurado=$Resultado[$i]['nombre'];
            $IdAsegurado=$Resultado[$i]['cod_docum_aseg'];
            $Expediente=$Resultado[$i]['num_exp'];
            $Codigo_fasecolda=$Resultado[$i]['COD_FASECOLDA'];
            $Telefono=$Resultado[$i]['tlf_numero'];
            $Celular=$Resultado[$i]['tlf_movil'];
            if($Numero_siniestro)
            {
            	$Ahora=date('Y-m-d H:i:s');
		         if(!mysql_query("insert ignore into aoacol_aoacars.siniestro (aseguradora,numero,ciudad,ciudad_original,fec_autorizacion,fec_siniestro,fec_declaracion,poliza,
		               sucursal_radicadora,intermediario,estado,placa,linea,modelo,asegurado_nombre,asegurado_id,expediente,
		               fasecolda,declarante_nombre,declarante_id,declarante_telefono,declarante_celular,ingreso)
		               values ('4','$Numero_siniestro','$Ciudad','$codCiudad','$Fec_denuncia','$Fec_siniestro','$Fec_denuncia','$Poliza',
		               '$Ramo','$Codigo_chasis:$Compania','5','$Placa','$Linea','$Modelo','$NomAsegurado','$IdAsegurado','$Expediente',
		               '$Codigo_fasecolda','$NomAsegurado','$IdAsegurado','$Telefono','$Celular','$Ahora')",$LINK)) {echo mysql_error($LINK);mysql_close($LINK);die();}
            	$ID1=mysql_insert_id($LINK);
            }
          }
          else
          {
            $P.=$Resultado[$i]['COD_CHASIS'].'|'.$Resultado[$i]['COD_FASECOLDA'].'|'.$Resultado[$i]['COD_LINEA'].'|'.$Resultado[$i]['COD_MODELO'].
              '|'.$Resultado[$i]['COD_PLACA'].'|'.$Resultado[$i]['COD_POSTAL_RADIC'].'|'.$Resultado[$i]['cod_ramo'].'|'.$Resultado[$i]['cod_docum_aseg'].
              '|'.$Resultado[$i]['cod_cia'].'|'.$Resultado[$i]['fec_denu_sini'].'|'.$Resultado[$i]['fec_sini'].'|'.$Resultado[$i]['nombre'].
              '|'.$Resultado[$i]['num_exp'].'|'.$Resultado[$i]['num_poliza'].'|'.$Resultado[$i]['num_sini'].'|'.$Resultado[$i]['tlf_movil'].
              '|'.$Resultado[$i]['tlf_numero'].'¬';
          }
      	$D=qom("select * from aoacol_aoacars.siniestro where aseguradora=4 and numero='".$Resultado[$i]['num_sini']."' ",$LINK);
      	$Co=qom("select * from aoacol_aoacars.ciudad where codigo='$D->ciudad_original' ",$LINK);
          $Cadena_email.="<tr><td align='right'>".($i+1)."</td><td>$D->numero</td><td>$D->fec_siniestro</td><td>$D->fec_autorizacion</td><td>$D->placa</td>".
          "<td>$Co->departamento $Co->nombre</td><td>$D->ingreso</td></tr>";
      }
      $Cadena_email.="</table>";
      echo "</table>";
    }
    else
    {
      $Cadena_email.="<hr>Fecha:$FECHA 0 siniestros.";
      echo "<hr>Fecha:$FECHA 0 siniestros.";
    }
  }
  else
  {
    $Cadena_email.="<br>Fecha:$FECHA 0 siniestros.";
    echo "<hr>Fecha:$FECHA 0 siniestros. $Resultado ";
  }
  return $Cantidad_siniestros_por_dia;
}
*/
?>