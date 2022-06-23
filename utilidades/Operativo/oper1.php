<?php
/**
 *  JEFE OPERATIVO AOA
 *
 *		activación de vehículos de flota AOA
 *
 * @version $Id$
 * @copyright 2010
 **/
include('inc/funciones_.php');



if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}
session_start();session_unset();session_destroy();html();
$Hacer=base64_encode('operativo');
echo "<script language='javascript' src='inc/js/aqrenc.js'></script>
		<script type='text/javascript' language='JavaScript'>
		var futuro= new Date();futuro.setSeconds(130);var actualiza = 1000;function faltan()
		{var ahora = new Date();	var faltan = futuro - ahora;
			if (faltan > 0)
			{var segundos = Math.round(faltan/1000);
				document.formulario.reloj.value=  segundos + ' SEGUNDOS PARA INGRESAR' ;
				setTimeout('faltan()',actualiza);
			}	else{document.formulario.reloj.value= '0 segundos' ;
				window.open('operativo.php?Acc=bye','_self');
				return true;}}	</script></head>
		<body onload='faltan()' leftmargin='0' rightmargin='0' bgcolor='#ffffff' topmargin='0' bottommargin='0'>
		<form name='formulario' style='font-family: Corbel; padding: 0'>
		<p align='center'><font face='Corbel'><input type='text' name='reloj' value='' size='55' style='font-size:14px;border-style:solid; border-width:0; padding:0; text-align : center; font-family:Corbel; color:#000000; background-color:#ffffff'>
		</font></p></form> <FORM name='entrada' id='entrada'><TABLE BORDER='0' CELLSPACING=0 ALIGN='center'>
		<TR><TD ALIGN='right'>USUARIO:</TD><TD><INPUT TYPE='text' NAME='IDuser' MAXLENGTH='30' SIZE='20'></TD></TR>	<TR >	<TD ALIGN='right'>
		CONTRASEÑA:</TD><TD><INPUT TYPE='password' NAME='password' MAXLENGTH='20' SIZE='20'> </td> </tr> <tr>
		<td align='center' colspan=2> <INPUT TYPE='button' VALUE='Ingresar' onclick=\"var dato1=encripta(document.entrada.IDuser.value,AqrSoftware);
		var dato2=encripta(document.entrada.password.value,AqrSoftware); setCookie('IDU',dato1); setCookie('CLU',dato2); valida_entrada(0, 0);
		document.entrada.password.value='';\"> </td> </TR> </TABLE> 	</FORM>";

function bye()
{html();echo "<body bgcolor='blue' ><h3 style='color:ffffff'>AOA COLOMBIA S.A. SESION FINALIZADA</H3></BODY>";die();}

function operativo(){html();echo "HOLA";}

function activar_sinlogo()
{
	global $Placa,$Fecha,$Usuario;
	if($au=qo("select * from solicitud_faoa where placa='$Placa' and fecha='$Fecha' "))
	{$Mensaje=urlencode(base64_encode("<font color='blue'>El vehiculo de placas <b>$Placa</b> ya fue autorizado en la fecha <b>$Fecha</b> por <b>$au->autorizadopor</b> </font>"));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");}
	else
	{q("insert into solicitud_faoa (placa,fecha,autorizadopor) values ('$Placa','$Fecha','$Usuario')") ;
		$Mensaje=urlencode(base64_encode("<font color='green'>Autorización satisfactoria del vehiculo <b>$Placa</b> en la fecha <b>$Fecha</b> por <b>$Usuario</b></font>"));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");}
}

function mensaje_operativo(){global $Mensaje;html('AUTORIZACION');echo "<body>".base64_decode($Mensaje)."</body>";}

function modificar_siniestro()
{
	global $idm,$Usuario;
	if($Modificacion=qo("select * from solicitud_modsin where id=$idm"))
	{
		if($Modificacion->aprobado_por)
		{$Mensaje=urlencode(base64_encode("<font color='blue'>Esta Solicitud ya fue procesada por $Modificacion->aprobado_por en la fecha $Modificacion->fec_aprobacion</font>" ));
			header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();}
		$H1=date('Y-m-d');$H2=date('H:i:s');
		if($Modificacion->cambio_estado)
		{q("update siniestro set estado=5,causal=0,observaciones=concat(observaciones,\"\n$Usuario [".date('Y-m-d H:i:s')."] Cambia estado a Pendiente: $justificacion1\") where id=$Modificacion->siniestro ");
			q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($Modificacion->siniestro,'$H1','$H2','$Usuario',\"Cambia estado a Pendiente: $Modificacion->justificacion1\",10)");
			q("update solicitud_modsin set aprobado_por='$Usuario',fec_aprobacion='".date('Y-m-d H:i:s')."' where id=$idm");}
		if($Modificacion->cambio_ciudad)
		{$Nciudad_old=qo1("select t_ciudad(ciudad) from siniestro where id=$Modificacion->siniestro ");
			$Nciudad=qo1("select t_ciudad('$Modificacion->ciudad') ");
			q("update siniestro set ciudad='$Modificacion->ciudad',observaciones=concat(observaciones,\"\n$Usuario [".date('Y-m-d H:i:s')."] Cambia ciudad de $Nciudad_old a $Nciudad: $justificacion2\") where id=$Modificacion->siniestro ");
			q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($Modificacion->siniestro,'$H1','$H2','$Usuario',\"Cambia ciudad de $Nciudad_old a $Nciudad: $Modificacion->justificacion2\",9)");
			q("update solicitud_modsin set aprobado_por='$Usuario',fec_aprobacion='".date('Y-m-d H:i:s')."' where id=$idm");}
	}
	else
	{$Mensaje=urlencode(base64_encode("<font color='red'>ERROR: La modificación no se encuentra registrada.</font>" ));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");}
	$Mensaje=urlencode(base64_encode("<font color='green'>Modificación del siniestro número <b>".qo1("select numero from siniestro where id=$Modificacion->siniestro")."</b> satisfactoria.</font>" ));
	header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");
}

function autorizar_garantia_efectivo() 
{
	global $idg,$Usuario;
	$Hoy=date('Y-m-d H:i:s');$Ahora=date('Y-m-d');
	$D=qo("select * from sin_autor where id=$idg");
	$Observaciones=$D->observaciones;
	if($D->estado=='A')
	{$Mensaje=urlencode(base64_encode("<font color='red'>Esta solicitud ya fue aprobada por $D->funcionario el dia $D->fecha_proceso </font>" ));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();}
	else
	{$Mensaje=urlencode(base64_encode("<form action='operativo.php' method='post' target='_self' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='pre_autorizacion_garantia_efectivo'>
				<input type='hidden' name='idg' value='$idg'>
				<input type='hidden' name='Usuario' value='$Usuario'>
			</form>
			<script language='javascript'>document.forma.submit();</script>
			"));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");
		die();
	}
}

function pre_autorizacion_garantia_efectivo()
{
	global $idg,$Usuario;
	html('AUTORIZACION PARA RECIBIR GARANTIA EN EFECTIVO');
		echo "<script language='javascript'>
				function autorizar(){window.open('operativo.php?Acc=autorizar_garantia_efectivo_ok&idg=$idg&Usuario=$Usuario','_self');}
			</script>
				<body><h3>AUTORIZACION PARA RECIBIR GARANTIA EN EFECTIVO</h3><br /><br />
				Esta Solicitud aun no ha sido aprobada. <br /><br />Click en el siguiente link para aprobarla:<br /><br />
				<input type='button' value='AUTORIZAR' onclick=\"autorizar();\"><br /><br /></body>";
}

function autorizar_garantia_efectivo_ok()
{
	global $idg,$Usuario;
	$Hoy=date('Y-m-d H:i:s');$Ahora=date('Y-m-d');
	$D=qo("select * from sin_autor where id=$idg");
	if($D->estado=='A')
	{$Mensaje=urlencode(base64_encode("<font color='red'>Esta solicitud ya fue aprobada por $D->funcionario el dia $D->fecha_proceso </font>" ));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();}
	q("update sin_autor set funcionario='$Usuario',estado='A', observaciones='Autorizado para recibir garantía de servicio en efectivo.', num_autorizacion='Efectivo',
	fecha_proceso='$Hoy',procesado_por='$Usuario' where id='$idg' ");
	$Mensaje=urlencode(base64_encode("<font color='green'>Autorizacion satisfactoria. </font>" ));
	header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");
}

function autorizar_cambio_temporal()
{
	global $id,$Usuario,$solicitadopor,$flota;
	$Hoy=date('Y-m-d H:i:s');$Ahora=date('Y-m-d');
	$Actual=qo("select * from ubicacion where id=$id");
	if($Ya=qo1("select id from ubicacion where vehiculo=$Actual->vehiculo and flota=$flota and id>$id"))
	{$Mensaje=urlencode(base64_encode("<font color='red' style='font-size:14px'>Esta Autorización ya fue realizada con anterioridad</font>"));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();}
	$IDN=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento)
	values ('$Actual->oficina','$Actual->vehiculo','$Actual->estado','$flota','$Actual->fecha_final','$Actual->fecha_final','$Actual->odometro_final','$Actual->odometro_final',
	'0','Parqueadero') ");
	$Mensaje=urlencode(base64_encode("<font color='green' style='font-size:14px'>Autorización Satisfactoria</font>"));
	header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");
}

function descargar_imagen_garantia()
{	
	global $id;
	header('Content-Type: text/html; charset=utf-8');
	include("/var/www/html/public_html/Control/operativo/views/subviews/clientes/cliente_login.html");	
	
}


function descargar_imagen_garantia_ok()
{
	global $id,$identificacion;
	
	$sql = "select devolucion_f, devolucion2_f, fecha_devolucion  from sin_autor where id= '$id' and identificacion = '$identificacion' ";
	
	//echo $sql;
	
	$Imagen=qo($sql);
	
	//print_r($Imagen);
	
	
	
	if($Imagen == null)
	{
		header('Content-Type: text/html; charset=utf-8');
		$message = utf8_encode("<h4>No hay datos asociados,  Si necesita asistencia con su solicitud comuniquese con nosotros al teléfono 018000186262 o en Bogotá al teléfono 8837069</h4>");
		include("/var/www/html/public_html/Control/operativo/views/subviews/clientes/cliente_mensaje.html");	
		exit;
	}
	else{
		$now = time(); // or your date as well
		$_date = strtotime($Imagen->fecha_devolucion);
		$datediff = $now - $_date;
		$days = round($datediff / (60 * 60 * 24));	
		
		//echo $days;
			
		if($days>16 || ($Imagen->devolucion_f == null and $Imagen->devolucion2_f == null))
		{
			header('Content-Type: text/html; charset=utf-8');
			$message = utf8_encode("<h4>Lo sentimos, su información ya no se encuentra en el sistema. Si necesita asistencia con su solicitud comuniquese con nosotros al teléfono 018000186262 o en Bogotá al teléfono 8837069 </h4>");
			include("/var/www/html/public_html/Control/operativo/views/subviews/clientes/cliente_mensaje.html");	
			exit;
		}
	}
	
	if($Imagen->devolucion_f)
	{
		if(!strpos(strtoupper($Imagen->devolucion_f),"PDF")){
			header("Pragma: public");header("Expires: 0");header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=\"imagen.jpg\"");
			header("Content-Description: File Transfert");@readfile("../../Control/operativo/$Imagen->devolucion_f");		
		}else{
			header("Content-type:application/pdf");
			header("Content-Disposition:attachment;filename='downloaded.pdf'");
			readfile("../../Control/operativo/$Imagen->devolucion_f");
		}	
	}
	if($Imagen->devolucion2_f)
	{
		if(!strpos(strtoupper($Imagen->devolucion_f),"PDF")){
			header("Pragma: public");header("Expires: 0");header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=\"imagen.jpg\"");
			header("Content-Description: File Transfert");@readfile("../../Control/operativo/$Imagen->devolucion2_f");
		}else{
			header("Content-type:application/pdf");
			header("Content-Disposition:attachment;filename='downloaded.pdf'");
			readfile("../../Control/operativo/$Imagen->devolucion2_f");
		}
	}
}

function autorizar_visualizacion_garantia()
{
	global $idn,$Usuario;
	$Hoy=date('Y-m-d H:i:s');$Ahora=date('Y-m-d');
	$D=qo("select * from solicitud_dataautor where id=$idn");
	if($D->autorizado_por)
	{$Mensaje=urlencode(base64_encode("<font color='red'>Esta solicitud ya fue aprobada por $D->autorizado_por el dia $D->fecha_aprobacion </font>" ));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();}
	else
	{$Mensaje=urlencode(base64_encode("<form action='operativo.php' method='post' target='_self' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='autorizar_visualizacion_garantia_ok'>
				<input type='hidden' name='idn' value='$idn'><input type='hidden' name='Usuario' value='$Usuario'></form>
			<script language='javascript'>document.forma.submit();</script>"));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();}	
}

function autorizar_visualizacion_garantia_ok()
{global $idn,$Usuario;$Fecha=date('Y-m-d H:i:s');
	q("update solicitud_dataautor set autorizado_por='$Usuario',fecha_aprobacion='$Fecha' where id=$idn");
	$Mensaje=urlencode(base64_encode("<font color='green'>Autorizacion satisfactoria. </font>" ));
	header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");}

function aprobar_requisicion()
{
	global $id,$Fecha,$Usuario,$eUsuario,$Solicitado_por,$eSolicitado_por,$observaciones,$observa_aprobacion,$cotapr;
	$D=qo("select * from aoacol_administra.requisicion where id=$id");
	if($D->estado==2) {$Mensaje=urlencode(base64_encode("El estado de esta requisición ya fue procesado y es: Aprobado." ));
	header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	if($D->estado==3) {$Mensaje=urlencode(base64_encode("El estado de esta requisición ya fue procesado y es: Rechazado." ));
	header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	if($D->estado==4) {$Mensaje=urlencode(base64_encode("El estado de esta requisición ya fue procesado y es: Calificado." ));
	header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	
	//q("update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observaciones=\"$observaciones\",cotapr='$cotapr' where id=$id");
	//q("update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observa_aprobacion='$observa_aprobacion',cotapr='$cotapr' where id=$id");
	

	$verificar=qo("select ubicacion from aoacol_administra.requisicion where id = $id");
				
				if($verificar->ubicacion == 0){
					include('pdfrequisicionAdm.php');
				}else{
					include('pdfrequisicionOpe.php');
				}
				
			    

			 
				 $correo = "davidduque@aoacolombia.com";

                $attachment = $pdf->Output('certificadoIndividual.pdf', 'S');
				
				
				//require("inc/PHPMailer-master/PHPMailerAutoload.php");
				
				$mail = new PHPMailer;
				$mail->IsSMTP();                                    // tell the class to use SMTP
				$mail->SMTPAuth   = true;                           // enable SMTP authentication
				$mail->Port       = 25;                             // set the SMTP server port
				$mail->Host       = "mail.aoasoluciones.com";           // SMTP server
				$mail->Username   = "contacto@aoasoluciones.com";  // SMTP server username
				$mail->Password   = "CorreoAoa2019*.*";            // SMTP server password
				$mail->SMTPSecure = 'tls';
				
				
				

				//$mail->IsSendmail();  // tell the class to use Sendmail
				//$mail->AddReplyTo("aherrera@akiris.net","Anibal Herrera");

				$mail->setFrom('sistemas@aoacolombia.com','Sistema de Control Operativo');
				//$mail->From       = "no-responder@acinco.com.co";
				//$mail->FromName   = utf8_decode("Protección Móvil.");
				$mail->AddAddress($correo);

					$mail->addCC("davidduque@aoacolombia.com");
				$mail->addCC('sergiocastillo@aoacolombia.com');
				$mail->addCC("sergiourbina@aoacolombia.com");
				

				$mail->AddStringAttachment($attachment, 'certificadoIndividual.pdf');
				$mail->WordWrap   = 80; // set word wrap
				
				$mail->Subject = "CONFIRMACION DE APROBACION DE PEDIDO $id";

				
				
				
				$body  ="
				
				<html>
  <head>
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <meta name='x-apple-disable-message-reformatting' />
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <title></title>
    <style type='text/css' rel='stylesheet' media='all'>
    /* reset */

*
{
	border: 0;
	box-sizing: content-box;
	color: inherit;
	font-family: inherit;
	font-size: inherit;
	font-style: inherit;
	font-weight: inherit;
	line-height: inherit;
	list-style: none;
	margin: 0;
	padding: 0;
	text-decoration: none;
	vertical-align: top;
}

/* content editable */

*[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

*[contenteditable] { cursor: pointer; }

*[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 1em 0.5em #DEF; }

span[contenteditable] { display: inline-block; }

/* heading */

h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }

/* table */

table { font-size: 75%; table-layout: fixed; width: 100%; }
table { border-collapse: separate; border-spacing: 2px; }
th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
th, td { border-radius: 0.25em; border-style: solid; }
th { background: #EEE; border-color: #BBB; }
td { border-color: #DDD; }

/* page */

html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; padding: 0.5in; }
html { background: #fff; cursor: default; }

body { box-sizing: border-box; height:nome; margin: 0 auto; overflow: hidden; padding: 0.5in; width:nome; }
body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }

/* header */

header { margin: 0 0 3em; }
header:after { clear: both; content: ''; display: table; }

header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
header address p { margin: 0 0 0.25em; }
header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
header img { max-height: 100%; max-width: 100%; }
header input { cursor: pointer; -ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity=0)'; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

/* article */

article, article address, table.meta, table.inventory { margin: 0 0 3em; }
article:after { clear: both; content: ''; display: table; }
article h1 { clip: rect(0 0 0 0); position: absolute; }

article address { float: left; font-size: 125%; font-weight: bold; }

/* table meta & balance */

table.meta, table.balance { float: right; width: 36%; }
table.meta:after, table.balance:after { clear: both; content: ''; display: table; }

/* table meta */

table.meta th { border: 1px solid black; border-color: rgba(118,136,29,1);width: 40%; }
table.meta td {border: 1px solid black; border-color: rgba(118,136,29,1); width: 60%; }

/* table items */
.titulo{
font-size:15px;
font-weight:900;
color:rgba(118,136,29,1);
}
table.inventory { clear: both; width: 100%;border: 1px solid black; border-color: rgba(118,136,29,1); }
table.inventory th {  background-color:rgba(118,136,29,1); font-weight: bold; text-align: center; color:#fff; }

table.inventory td:nth-child(1) { width: 26%; }
table.inventory td:nth-child(2) { width: 38%; }
table.inventory td:nth-child(3) { text-align: right; width: 12%; }
table.inventory td:nth-child(4) { text-align: right; width: 12%; }
table.inventory td:nth-child(5) { text-align: right; width: 12%; }

/* table balance */

table.balance th, table.balance td { width: 50%; }
table.balance td { text-align: right; }

/* aside */

aside h1 { border: none; border-width: 0 0 1px; margin: 0 0 1em; }
aside h1 { border-color: #999; border-bottom-style: solid; }

/* javascript */

.add, .cut
{
	border-width: 1px;
	display: block;
	font-size: .8rem;
	padding: 0.25em 0.5em;	
	float: left;
	text-align: center;
	width: 0.6em;
}
    p.sub {
      font-size: 13px;
    }
    /* Utilities ------------------------------ */
    
    .align-right {
      text-align: right;
    }
    
    .align-left {
      text-align: left;
    }
    
    .align-center {
      text-align: center;
    }
    /* Buttons ------------------------------ */
    
    .button {
      background-color: #3869D4;
      border-top: 10px solid #3869D4;
      border-right: 18px solid #3869D4;
      border-bottom: 10px solid #3869D4;
      border-left: 18px solid #3869D4;
      display: inline-block;
      color: #FFF;
      text-decoration: none;
      border-radius: 3px;
      box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
      -webkit-text-size-adjust: none;
      box-sizing: border-box;
    }
    
    .button--green {
      background-color: #22BC66;
      border-top: 10px solid #22BC66;
      border-right: 18px solid #22BC66;
      border-bottom: 10px solid #22BC66;
      border-left: 18px solid #22BC66;
    }
    
    .button--red {
      background-color: #FF6136;
      border-top: 10px solid #FF6136;
      border-right: 18px solid #FF6136;
      border-bottom: 10px solid #FF6136;
      border-left: 18px solid #FF6136;
    }
    
    @media only screen and (max-width: 500px) {
      .button {
        width: 100% !important;
        text-align: center !important;
      }
    }
    /* Attribute list ------------------------------ */
    
    .attributes {
      margin: 0 0 21px;
    }
    
    .attributes_content {
      background-color: #F4F4F7;
      padding: 16px;
    }
    
    .attributes_item {
      padding: 0;
    }
    /* Related Items ------------------------------ */
    
    .related {
      width: 100%;
      margin: 0;
      padding: 25px 0 0 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
    }
    
    .related_item {
      padding: 10px 0;
      color: #CBCCCF;
      font-size: 15px;
      line-height: 18px;
    }
    
    .related_item-title {
      display: block;
      margin: .5em 0 0;
    }
    
    .related_item-thumb {
      display: block;
      padding-bottom: 10px;
    }
    
    .related_heading {
      border-top: 1px solid #CBCCCF;
      text-align: center;
      padding: 25px 0 10px;
    }
    /* Discount Code ------------------------------ */
    
    .discount {
      width: 100%;
      margin: 0;
      padding: 24px;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      background-color: #F4F4F7;
      border: 2px dashed #CBCCCF;
    }
    
    .discount_heading {
      text-align: center;
    }
    
    .discount_body {
      text-align: center;
      font-size: 15px;
    }
    /* Social Icons ------------------------------ */
    
    .social {
      width: auto;
    }
    
    .social td {
      padding: 0;
      width: auto;
    }
    
    .social_icon {
      height: 20px;
      margin: 0 8px 10px 8px;
      padding: 0;
    }
    /* Data table ------------------------------ */
    
    .purchase {
      width: 100%;
      margin: 0;
      padding: 35px 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
    }
    
    .purchase_content {
      width: 100%;
      margin: 0;
      padding: 25px 0 0 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
    }
    
    .purchase_item {
      padding: 10px 0;
      color: #51545E;
      font-size: 15px;
      line-height: 18px;
    }
    
    .purchase_heading {
      padding-bottom: 8px;
      border-bottom: 1px solid #EAEAEC;
    }

    .purchase_heading p {
      margin: 0;
      color: #85878E;
      font-size: 12px;
    }
    
    .purchase_footer {
      padding-top: 15px;
      border-top: 1px solid #EAEAEC;
    }
    
    .purchase_total {
      margin: 0;
      text-align: right;
      font-weight: bold;
      color: #333333;
    }
    
    .purchase_total--label {
      padding: 0 15px 0 0;
    }
    
    body {
      background-color: #F4F4F7;
      color: #51545E;
    }
    
    p {
      color: #51545E;
    }
    
    p.sub {
      color: #6B6E76;
    }
    
    .email-wrapper {
      width: 100%;
      margin: 0;
      padding: 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      background-color: #F4F4F7;
    }
    
    .email-content {
      width: 100%;
      margin: 0;
      padding: 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
    }
    /* Masthead ----------------------- */
    
    .email-masthead {
      padding: 25px 0;
      text-align: center;
    }
    
    .email-masthead_logo {
      width: 94px;
    }
    
    .email-masthead_name {
      font-size: 16px;
      font-weight: bold;
      color: #A8AAAF;
      text-decoration: none;
      text-shadow: 0 1px 0 white;
    }
    /* Body ------------------------------ */
    
    .email-body {
      width: 100%;
      margin: 0;
      padding: 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      background-color: #FFFFFF;
    }
    
    .email-body_inner {
      width: 570px;
      margin: 0 auto;
      padding: 0;
      -premailer-width: 570px;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      background-color: #FFFFFF;
    }
    
    .email-footer {
      width: nome;
      margin: 0 auto;
      padding: 0;
      -premailer-width: 570px;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      text-align: center;
    }
    
    .email-footer p {
      color: #6B6E76;
    }
    
    .body-action {
      width: 100%;
      margin: 30px auto;
      padding: 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      text-align: center;
    }
    
    .body-sub {
      margin-top: 25px;
      padding-top: 25px;
      border-top: 1px solid #EAEAEC;
    }
    
    .content-cell {
      padding: 35px;
    }
.add, .cut
{
	background: #9AF;
	box-shadow: 0 1px 2px rgba(0,0,0,0.2);
	background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
	background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
	border-radius: 0.5em;
	border-color: #0076A3;
	color: #FFF;
	cursor: pointer;
	font-weight: bold;
	text-shadow: 0 -1px 2px rgba(0,0,0,0.333);
}
input[type=text]:focus {
  border: 3px solid #555;
}
.add { margin: -2.5em 0 0; }

.add:hover { background: #00ADEE; }

.cut { opacity: 0; position: absolute; top: 0; left: -1.5em; }
.cut { -webkit-transition: opacity 100ms ease-in; }

tr:hover .cut { opacity: 1; }

@media print {
	* { -webkit-print-color-adjust: exact; }
	html { background: none; padding: 0; }
	body { box-shadow: none; margin: 0; }
	span:empty { display: none; }
	.add, .cut { display: none; }
}

@page { margin: 0; }
    </style>
    <!--[if mso]>
    <style type='text/css'>
      .f-fallback  {
        font-family: Arial, sans-serif;
      }
    </style>
  <![endif]-->
  </head>

		<header>
		adquisiciones@aoasoluciones.com; 
		   <table   style=' text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
                  <tr>
                    <td    style=' text-align: center;'  class='content-cell' align='center' >
		                   <img   style=' text-align: center;'  src='https://app.aoacolombia.com/Administrativo/img/banner-vehiculo-sustituto-AOA.jpg'>
                    </td>
                  </tr>

                </table>
				<br>
				  <table   style=' text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
                  <tr>
                    <td   style=' text-align: center;'  class='content-cell' align='center' >
                       <b  style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);' class='titulo'>Notificación de Aprobación de Requisición</b><br> </td>
                  </tr>

                </table>
			<address  style='margin-bottom: 1%; ' contenteditable>
			    <h3 class='titulo'  style='margin-bottom: 2%;  color: 1px solid rgba(118,136,29,1);'  >Número de Requisición:</td><td><b>$id</h3>
				
			</address>
			
		</header>
		<br>
		<hr>
		<hr>
		
		<address contenteditable style='margin-bottom: 1%; '>
			   <h3 class='titulo'  style='margin-bottom: 1%;  color: 1px solid rgba(118,136,29,1);'  >Número de Requisición: &nbsp; $id</h3>
				<p>Aprobado : &nbsp; $Usuario [$eUsuario]</p>
				<p>Solicitado : &nbsp; $Solicitado_por $eSolicitado_por] <p>
				<p>Observaciones : &nbsp;  $HT->odometro_inicial </p>
			
			</address>
		
		
			 
		
		 <article>	

               
		 <p>Cordialmente  &nbsp;  $Usuario </p>
			
		</article>
				

		<aside>
			   <table class='email-footer'    align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
                  <tr>
                    <td   style=' text-align: center;'  class='content-cell' align='center' >
                      <p  style=' text-align: center;'  class='f-fallback sub align-center'>
                        018000186262
                        <br>+(571) 8837069
                        <br>adquisiciones@aoasoluciones.com
						<br>http://www.aoacolombia.com
                      </p>
		            <img   style=' text-align: center;'  class='f-fallback email-masthead_name' src='https://app.aoacolombia.com/Administrativo/img/logo-footer-mail-AOA.jpg'>
                    </td>
                  </tr>

                </table>
		</aside>
	</body>
</html>";
				
									
				
			   $mail->MsgHTML($body);
				$mail->IsHTML(true);
				if($mail->send()){
					 echo "<body><script language='javascript'>alert('Email enviado satisfactoriamente a $Email_aprobador');</script></body>";
				}else{
					echo "<body><script language='javascript'>alert('No se pudo enviar a $Email_aprobador');</script></body>";
				}
				
				
// ENVIO DE CORREO AL PROVEEDOR CON EL DETALLE DE LA REQUISICION
	$Proveedor=qo("select * from aoacol_administra.proveedor where id=$D->proveedor");
	$EmailDestino='';
	if($Proveedor->contacto) $Nprov=$Proveedor->contacto;elseif($Proveedor->nombre) $Nprov=$Proveedor->nombre;else $Nprov='PROVEEDOR';
	// busqueda del correo electronico de acuerdo a la sede registrada en la requisición
	if($D->sede)
	{
		if($Sede=qo("select * from aoacol_administra.prov_sede where id=$D->sede"))
		{
			if($Sede->email) $EmailDestino=$Sede->email;
		}
	}
	$Mensaje=urlencode(base64_encode("Autorizacion satisfactoria Sede: " ));
	// si no hay sede registrada en la requisición, se toma el email del registro principal del proveedor
	if(!$EmailDestino)
	{
		if($Proveedor->email) $EmailDestino="$Proveedor->email,$Nprov";
	}
	
	// si hay correo electronico, se envia el mensaje
	if($EmailDestino)
	{
		$Det="<table border cellspacing='0'><tr><th>Tipo de Requisicion</th><th>Item</th><th>Unidad de medida</th><th>Descripcion</th><th>Cantidad</th><th>Valor unitario</th><th>Valor</th>";
	        $Detalle=q("select provee_produc_serv.nombre as item,tipo.nombre as tipo,unidad_de_medida.nombre as unidad_medida,requisiciond.observaciones,requisiciond.cantidad,
                    requisiciond.requisicion,requisiciond.valor_total,requisiciond.cantidad,requisiciond.valor as valor_unitario
					from aoacol_administra.requisiciond
					inner join aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					inner join aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					inner join aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
					where requisicion =$id");
	        while($Dt =mysql_fetch_object($Detalle ))
	        {
		    $Det.="<tr><td>$Dt->tipo</td><td>$Dt->item</td><td>$Dt->unidad_medida</td><td>$Dt->observaciones</td><td>$Dt->cantidad</td><td align='right'>$".coma_format($Dt->valor_unitario)."</td><td align='right'>$".coma_format($Dt->valor_total)."</td></tr>";
	        }
	        $Det.="</table>";
			
			
			$Res="<table border cellspacing='4'><tr><th>Resultado</th>";
        
		     $retorno=q("select requisiciond.requisicion,requisiciond.valor_total,
		            sum(requisiciond.valor_total) as resultado 
					from aoacol_administra.requisiciond
					where requisicion  =$id");
			while($Dt =mysql_fetch_object($retorno))
			{
			   $Res.="<tr><td>$".coma_format($Dt->resultado)."</td>";
			}
			$Res.="</table>";
			
			$Ciudades=qo("select requisicion.ciudad as campoCity ,ciudad.nombre as ciudad, 
                    ciudad.departamento
					from aoacol_administra.requisiciond
					inner join aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id
                    inner join aoacol_administra.ciudad on requisicion.ciudad = ciudad.codigo
                    where requisiciond.requisicion = $id limit 1");
			$ciudad = $Ciudades->ciudad;
			$departamento = $Ciudades->departamento;
			
			    $correo = "davidduque@aoacolombia.com";


				
				$attachment= $pdf->Output('certificadoIndividual.pdf', 'S');
				//require("inc/PHPMailer-master/PHPMailerAutoload.php");
				$mail = new PHPMailer;
				$mail->IsSMTP();                                    // tell the class to use SMTP
				$mail->SMTPAuth   = true;                           // enable SMTP authentication
				$mail->Port       = 25;                             // set the SMTP server port
				$mail->Host       = "mail.aoasoluciones.com";           // SMTP server
				$mail->Username   = "contacto@aoasoluciones.com";  // SMTP server username
				$mail->Password   = "CorreoAoa2019*.*";            // SMTP server password
				$mail->SMTPSecure = 'tls';
				
				
				

				//$mail->IsSendmail();  // tell the class to use Sendmail
				//$mail->AddReplyTo("aherrera@akiris.net","Anibal Herrera");

				$mail->setFrom('sistemas@aoacolombia.com','Sistema de Control Operativo');
				//$mail->From       = "no-responder@acinco.com.co";
				//$mail->FromName   = utf8_decode("Protección Móvil.");
				$mail->AddAddress($correo);

					$mail->addCC("davidduque@aoacolombia.com");
				$mail->addCC('sergiocastillo@aoacolombia.com');
				$mail->addCC("sergiourbina@aoacolombia.com");
				

				$mail->AddStringAttachment($attachment, 'certificadoIndividual.pdf');
				$mail->WordWrap   = 80; // set word wrap
				
				$mail->Subject = "CONFIRMACION DE APROBACION DE PEDIDO $id";

				
				
				
				$body  ="
				
				
				 
				
				<html>
			  <head>
				<meta name='viewport' content='width=device-width, initial-scale=1.0' />
				<meta name='x-apple-disable-message-reformatting' />
				<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
				<title></title>
				<style type='text/css' rel='stylesheet' media='all'>
				/* reset */

			*
			{
				border: 0;
				box-sizing: content-box;
				color: inherit;
				font-family: inherit;
				font-size: inherit;
				font-style: inherit;
				font-weight: inherit;
				line-height: inherit;
				list-style: none;
				margin: 0;
				padding: 0;
				text-decoration: none;
				vertical-align: top;
			}

			/* content editable */

			*[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

			*[contenteditable] { cursor: pointer; }

			*[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 1em 0.5em #DEF; }

			span[contenteditable] { display: inline-block; }

			/* heading */

			h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }

			/* table */

			table { font-size: 75%; table-layout: fixed; width: 100%; }
			table { border-collapse: separate; border-spacing: 2px; }
			th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
			th, td { border-radius: 0.25em; border-style: solid; }
			th { background: #EEE; border-color: #BBB; }
			td { border-color: #DDD; }

			/* page */

			html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; padding: 0.5in; }
			html { background: #fff; cursor: default; }

			body { box-sizing: border-box; height:nome; margin: 0 auto; overflow: hidden; padding: 0.5in; width:nome; }
			body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }

			/* header */

			header { margin: 0 0 3em; }
			header:after { clear: both; content: ''; display: table; }

			header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
			header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
			header address p { margin: 0 0 0.25em; }
			header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
			header img { max-height: 100%; max-width: 100%; }
			header input { cursor: pointer; -ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity=0)'; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

			/* article */

			article, article address, table.meta, table.inventory { margin: 0 0 3em; }
			article:after { clear: both; content: ''; display: table; }
			article h1 { clip: rect(0 0 0 0); position: absolute; }

			article address { float: left; font-size: 125%; font-weight: bold; }

			/* table meta & balance */

			table.meta, table.balance { float: right; width: 36%; }
			table.meta:after, table.balance:after { clear: both; content: ''; display: table; }

			/* table meta */

			table.meta th { border: 1px solid black; border-color: rgba(118,136,29,1);width: 40%; }
			table.meta td {border: 1px solid black; border-color: rgba(118,136,29,1); width: 60%; }

			/* table items */
			.titulo{
			font-size:15px;
			font-weight:900;
			color:rgba(118,136,29,1);
			}
			table.inventory { clear: both; width: 100%;border: 1px solid black; border-color: rgba(118,136,29,1); }
			table.inventory th {  background-color:rgba(118,136,29,1); font-weight: bold; text-align: center; color:#fff; }

			table.inventory td:nth-child(1) { width: 26%; }
			table.inventory td:nth-child(2) { width: 38%; }
			table.inventory td:nth-child(3) { text-align: right; width: 12%; }
			table.inventory td:nth-child(4) { text-align: right; width: 12%; }
			table.inventory td:nth-child(5) { text-align: right; width: 12%; }

			/* table balance */

			table.balance th, table.balance td { width: 50%; }
			table.balance td { text-align: right; }

			/* aside */

			aside h1 { border: none; border-width: 0 0 1px; margin: 0 0 1em; }
			aside h1 { border-color: #999; border-bottom-style: solid; }

			/* javascript */

			.add, .cut
			{
				border-width: 1px;
				display: block;
				font-size: .8rem;
				padding: 0.25em 0.5em;	
				float: left;
				text-align: center;
				width: 0.6em;
			}
				p.sub {
				  font-size: 13px;
				}
				/* Utilities ------------------------------ */
				
				.align-right {
				  text-align: right;
				}
				
				.align-left {
				  text-align: left;
				}
				
				.align-center {
				  text-align: center;
				}
				/* Buttons ------------------------------ */
				
				.button {
				  background-color: #3869D4;
				  border-top: 10px solid #3869D4;
				  border-right: 18px solid #3869D4;
				  border-bottom: 10px solid #3869D4;
				  border-left: 18px solid #3869D4;
				  display: inline-block;
				  color: #FFF;
				  text-decoration: none;
				  border-radius: 3px;
				  box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
				  -webkit-text-size-adjust: none;
				  box-sizing: border-box;
				}
				
				.button--green {
				  background-color: #22BC66;
				  border-top: 10px solid #22BC66;
				  border-right: 18px solid #22BC66;
				  border-bottom: 10px solid #22BC66;
				  border-left: 18px solid #22BC66;
				}
				
				.button--red {
				  background-color: #FF6136;
				  border-top: 10px solid #FF6136;
				  border-right: 18px solid #FF6136;
				  border-bottom: 10px solid #FF6136;
				  border-left: 18px solid #FF6136;
				}
				
				@media only screen and (max-width: 500px) {
				  .button {
					width: 100% !important;
					text-align: center !important;
				  }
				}
				/* Attribute list ------------------------------ */
				
				.attributes {
				  margin: 0 0 21px;
				}
				
				.attributes_content {
				  background-color: #F4F4F7;
				  padding: 16px;
				}
				
				.attributes_item {
				  padding: 0;
				}
				/* Related Items ------------------------------ */
				
				.related {
				  width: 100%;
				  margin: 0;
				  padding: 25px 0 0 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				}
				
				.related_item {
				  padding: 10px 0;
				  color: #CBCCCF;
				  font-size: 15px;
				  line-height: 18px;
				}
				
				.related_item-title {
				  display: block;
				  margin: .5em 0 0;
				}
				
				.related_item-thumb {
				  display: block;
				  padding-bottom: 10px;
				}
				
				.related_heading {
				  border-top: 1px solid #CBCCCF;
				  text-align: center;
				  padding: 25px 0 10px;
				}
				/* Discount Code ------------------------------ */
				
				.discount {
				  width: 100%;
				  margin: 0;
				  padding: 24px;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  background-color: #F4F4F7;
				  border: 2px dashed #CBCCCF;
				}
				
				.discount_heading {
				  text-align: center;
				}
				
				.discount_body {
				  text-align: center;
				  font-size: 15px;
				}
				/* Social Icons ------------------------------ */
				
				.social {
				  width: auto;
				}
				
				.social td {
				  padding: 0;
				  width: auto;
				}
				
				.social_icon {
				  height: 20px;
				  margin: 0 8px 10px 8px;
				  padding: 0;
				}
				/* Data table ------------------------------ */
				
				.purchase {
				  width: 100%;
				  margin: 0;
				  padding: 35px 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				}
				
				.purchase_content {
				  width: 100%;
				  margin: 0;
				  padding: 25px 0 0 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				}
				
				.purchase_item {
				  padding: 10px 0;
				  color: #51545E;
				  font-size: 15px;
				  line-height: 18px;
				}
				
				.purchase_heading {
				  padding-bottom: 8px;
				  border-bottom: 1px solid #EAEAEC;
				}

				.purchase_heading p {
				  margin: 0;
				  color: #85878E;
				  font-size: 12px;
				}
				
				.purchase_footer {
				  padding-top: 15px;
				  border-top: 1px solid #EAEAEC;
				}
				
				.purchase_total {
				  margin: 0;
				  text-align: right;
				  font-weight: bold;
				  color: #333333;
				}
				
				.purchase_total--label {
				  padding: 0 15px 0 0;
				}
				
				body {
				  background-color: #F4F4F7;
				  color: #51545E;
				}
				
				p {
				  color: #51545E;
				}
				
				p.sub {
				  color: #6B6E76;
				}
				
				.email-wrapper {
				  width: 100%;
				  margin: 0;
				  padding: 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  background-color: #F4F4F7;
				}
				
				.email-content {
				  width: 100%;
				  margin: 0;
				  padding: 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				}
				/* Masthead ----------------------- */
				
				.email-masthead {
				  padding: 25px 0;
				  text-align: center;
				}
				
				.email-masthead_logo {
				  width: 94px;
				}
				
				.email-masthead_name {
				  font-size: 16px;
				  font-weight: bold;
				  color: #A8AAAF;
				  text-decoration: none;
				  text-shadow: 0 1px 0 white;
				}
				/* Body ------------------------------ */
				
				.email-body {
				  width: 100%;
				  margin: 0;
				  padding: 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  background-color: #FFFFFF;
				}
				
				.email-body_inner {
				  width: 570px;
				  margin: 0 auto;
				  padding: 0;
				  -premailer-width: 570px;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  background-color: #FFFFFF;
				}
				
				.email-footer {
				  width: nome;
				  margin: 0 auto;
				  padding: 0;
				  -premailer-width: 570px;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  text-align: center;
				}
				
				.email-footer p {
				  color: #6B6E76;
				}
				
				.body-action {
				  width: 100%;
				  margin: 30px auto;
				  padding: 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  text-align: center;
				}
				
				.body-sub {
				  margin-top: 25px;
				  padding-top: 25px;
				  border-top: 1px solid #EAEAEC;
				}
				
				.content-cell {
				  padding: 35px;
				}
			.add, .cut
			{
				background: #9AF;
				box-shadow: 0 1px 2px rgba(0,0,0,0.2);
				background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
				background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
				border-radius: 0.5em;
				border-color: #0076A3;
				color: #FFF;
				cursor: pointer;
				font-weight: bold;
				text-shadow: 0 -1px 2px rgba(0,0,0,0.333);
			}
			input[type=text]:focus {
			  border: 3px solid #555;
			}
			.add { margin: -2.5em 0 0; }

			.add:hover { background: #00ADEE; }

			.cut { opacity: 0; position: absolute; top: 0; left: -1.5em; }
			.cut { -webkit-transition: opacity 100ms ease-in; }

			tr:hover .cut { opacity: 1; }

			@media print {
				* { -webkit-print-color-adjust: exact; }
				html { background: none; padding: 0; }
				body { box-shadow: none; margin: 0; }
				span:empty { display: none; }
				.add, .cut { display: none; }
			}

			@page { margin: 0; }
				</style>
				<!--[if mso]>
				<style type='text/css'>
				  .f-fallback  {
					font-family: Arial, sans-serif;
				  }
				</style>
			  <![endif]-->
			  </head>

					<header>
					   <table  style='margin-bottom: 2%;   text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
							  <tr>
								<td    style=' text-align: center;'  class='content-cell' align='center' >
									   <img   style=' text-align: center;'  src='https://app.aoacolombia.com/Administrativo/img/banner-vehiculo-sustituto-AOA.jpg'>
								</td>
							  </tr>

							</table>
							<br>
							  <table   style='margin-bottom: 6%;  text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
							  <tr>
								<td  style='margin-bottom: 3%; '  class='content-cell' align='center' >
								   <b  style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);' class='titulo'><b>Estimado Señor $Nprov,</b></b><br> </td>
							  </tr>

							</table>
						<address  style='margin-bottom: 1%; ' contenteditable>
							<h3 class='titulo'  style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);'  >Reciba cordial saludo.</h3>
						
			
								Por medio de este correo se le notifica formalmente sobre la 
								aprobación de <b><u>Requisición Interna Número $id</u>
								</b>de la ciudad de&nbsp;<b>$ciudad</b>&nbsp;con el departamento&nbsp;<b>$departamento</b>&nbsp; 
								en nuestra empresa para adquirir los siguientes bienes/servicios:
						</address>
						
					</header>
					<br>
					<hr>
					<hr>
					<article style='margin-bottom: 3%; '>
					
					  $Det
					</article>
					
					<p style='margin-top: 3%; color: 1px solid rgba(118,136,29,1);' class='titulo' >Valor total<br></p>
					<address  style='margin-bottom: 3%; ' contenteditable>
					  $Res
					</address>	
				
					<address  style='margin-bottom: 2%; ' contenteditable>
						Agradecemos de antemano su gentil atención.
					</address>	
					
					<address  style='margin-bottom: 1%; ' contenteditable>
						Cordialmente,
					</address>	
					 
					 <address  style='margin-bottom: 5%; ' contenteditable>
						$Usuario
					</address>
													

					<aside>
						   <table class='email-footer'    align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
							  <tr>
								<td   style=' text-align: center;'  class='content-cell' align='center' >
								  <p  style=' text-align: center;'  class='f-fallback sub align-center'>
									
									018000186262
									<br> AOA COLOMBIA S.A.
									<br>+(571) 8837069
									<br>$eUsuario
									<br>http://www.aoacolombia.com
									<br><i style='font-size:8px'>Mensaje automático del sistema de Requisiciones de AOA Colombia S.A. desarrollado por Tecnologia AOA. (it@aoacolombia.co)</i>
								  </p>
								<img   style=' text-align: center;'  class='f-fallback email-masthead_name' src='https://app.aoacolombia.com/Administrativo/img/logo-footer-mail-AOA.jpg'>
								</td>
							  </tr>

							</table>
					</aside>
				</body>
			</html>";
				
									
				
			   $mail->MsgHTML($body);
				$mail->IsHTML(true);
				if($mail->send()){
					 echo "<body><script language='javascript'>alert('Email enviado satisfactoriamente a $Email_aprobador');</script></body>";
				}else{
					echo "<body><script language='javascript'>alert('No se pudo enviar a $Email_aprobador');</script></body>";
				}
		header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");
	}
	else 
	echo "<b>El proveedor no tiene correo electrónico definido.</b>";
	
	//******************************************************************************************************
}

function daprobar_requisicion()
{
	global $id,$Fecha,$Usuario,$eUsuario,$Solicitado_por,$eSolicitado_por,$observaciones;
	$D=qo("select * from aoacol_administra.requisicion where id=$id");
	//return print_r($D);
	if($D->estado==2) {$Mensaje=urlencode(base64_encode("El estado de esta requisición ya fue procesado y es: Aprobado." ));
	header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	if($D->estado==3) {$Mensaje=urlencode(base64_encode("El estado de esta requisición ya fue procesado y es: Rechazado." ));
	header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	if($D->estado==4) {$Mensaje=urlencode(base64_encode("El estado de esta requisición ya fue procesado y es: Calificado." ));
	header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	q("update aoacol_administra.requisicion set estado=3,aprobado_por='$Usuario',observaciones=\"$observaciones\" where id=$id");
	$Mensaje=urlencode(base64_encode("Autorizacion negada satisfactoriamente." ));
	
	enviar_gmail($eUsuario,$Usuario,"$eSolicitado_por,$Solicitado_por","","NOTIFICACION DE APROBACION",
		"	 
	
	<html>
  <head>
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <meta name='x-apple-disable-message-reformatting' />
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <title></title>
    <style type='text/css' rel='stylesheet' media='all'>
    /* reset */

*
{
	border: 0;
	box-sizing: content-box;
	color: inherit;
	font-family: inherit;
	font-size: inherit;
	font-style: inherit;
	font-weight: inherit;
	line-height: inherit;
	list-style: none;
	margin: 0;
	padding: 0;
	text-decoration: none;
	vertical-align: top;
}

/* content editable */

*[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

*[contenteditable] { cursor: pointer; }

*[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 1em 0.5em #DEF; }

span[contenteditable] { display: inline-block; }

/* heading */

h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }

/* table */

table { font-size: 75%; table-layout: fixed; width: 100%; }
table { border-collapse: separate; border-spacing: 2px; }
th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
th, td { border-radius: 0.25em; border-style: solid; }
th { background: #EEE; border-color: #BBB; }
td { border-color: #DDD; }

/* page */

html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; padding: 0.5in; }
html { background: #fff; cursor: default; }

body { box-sizing: border-box; height:nome; margin: 0 auto; overflow: hidden; padding: 0.5in; width:nome; }
body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }

/* header */

header { margin: 0 0 3em; }
header:after { clear: both; content: ''; display: table; }

header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
header address p { margin: 0 0 0.25em; }
header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
header img { max-height: 100%; max-width: 100%; }
header input { cursor: pointer; -ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity=0)'; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

/* article */

article, article address, table.meta, table.inventory { margin: 0 0 3em; }
article:after { clear: both; content: ''; display: table; }
article h1 { clip: rect(0 0 0 0); position: absolute; }

article address { float: left; font-size: 125%; font-weight: bold; }

/* table meta & balance */

table.meta, table.balance { float: right; width: 36%; }
table.meta:after, table.balance:after { clear: both; content: ''; display: table; }

/* table meta */

table.meta th { border: 1px solid black; border-color: rgba(118,136,29,1);width: 40%; }
table.meta td {border: 1px solid black; border-color: rgba(118,136,29,1); width: 60%; }

/* table items */
.titulo{
font-size:15px;
font-weight:900;
color:rgba(118,136,29,1);
}
table.inventory { clear: both; width: 100%;border: 1px solid black; border-color: rgba(118,136,29,1); }
table.inventory th {  background-color:rgba(118,136,29,1); font-weight: bold; text-align: center; color:#fff; }

table.inventory td:nth-child(1) { width: 26%; }
table.inventory td:nth-child(2) { width: 38%; }
table.inventory td:nth-child(3) { text-align: right; width: 12%; }
table.inventory td:nth-child(4) { text-align: right; width: 12%; }
table.inventory td:nth-child(5) { text-align: right; width: 12%; }

/* table balance */

table.balance th, table.balance td { width: 50%; }
table.balance td { text-align: right; }

/* aside */

aside h1 { border: none; border-width: 0 0 1px; margin: 0 0 1em; }
aside h1 { border-color: #999; border-bottom-style: solid; }

/* javascript */

.add, .cut
{
	border-width: 1px;
	display: block;
	font-size: .8rem;
	padding: 0.25em 0.5em;	
	float: left;
	text-align: center;
	width: 0.6em;
}
    p.sub {
      font-size: 13px;
    }
    /* Utilities ------------------------------ */
    
    .align-right {
      text-align: right;
    }
    
    .align-left {
      text-align: left;
    }
    
    .align-center {
      text-align: center;
    }
    /* Buttons ------------------------------ */
    
    .button {
      background-color: #3869D4;
      border-top: 10px solid #3869D4;
      border-right: 18px solid #3869D4;
      border-bottom: 10px solid #3869D4;
      border-left: 18px solid #3869D4;
      display: inline-block;
      color: #FFF;
      text-decoration: none;
      border-radius: 3px;
      box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
      -webkit-text-size-adjust: none;
      box-sizing: border-box;
    }
    
    .button--green {
      background-color: #22BC66;
      border-top: 10px solid #22BC66;
      border-right: 18px solid #22BC66;
      border-bottom: 10px solid #22BC66;
      border-left: 18px solid #22BC66;
    }
    
    .button--red {
      background-color: #FF6136;
      border-top: 10px solid #FF6136;
      border-right: 18px solid #FF6136;
      border-bottom: 10px solid #FF6136;
      border-left: 18px solid #FF6136;
    }
    
    @media only screen and (max-width: 500px) {
      .button {
        width: 100% !important;
        text-align: center !important;
      }
    }
    /* Attribute list ------------------------------ */
    
    .attributes {
      margin: 0 0 21px;
    }
    
    .attributes_content {
      background-color: #F4F4F7;
      padding: 16px;
    }
    
    .attributes_item {
      padding: 0;
    }
    /* Related Items ------------------------------ */
    
    .related {
      width: 100%;
      margin: 0;
      padding: 25px 0 0 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
    }
    
    .related_item {
      padding: 10px 0;
      color: #CBCCCF;
      font-size: 15px;
      line-height: 18px;
    }
    
    .related_item-title {
      display: block;
      margin: .5em 0 0;
    }
    
    .related_item-thumb {
      display: block;
      padding-bottom: 10px;
    }
    
    .related_heading {
      border-top: 1px solid #CBCCCF;
      text-align: center;
      padding: 25px 0 10px;
    }
    /* Discount Code ------------------------------ */
    
    .discount {
      width: 100%;
      margin: 0;
      padding: 24px;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      background-color: #F4F4F7;
      border: 2px dashed #CBCCCF;
    }
    
    .discount_heading {
      text-align: center;
    }
    
    .discount_body {
      text-align: center;
      font-size: 15px;
    }
    /* Social Icons ------------------------------ */
    
    .social {
      width: auto;
    }
    
    .social td {
      padding: 0;
      width: auto;
    }
    
    .social_icon {
      height: 20px;
      margin: 0 8px 10px 8px;
      padding: 0;
    }
    /* Data table ------------------------------ */
    
    .purchase {
      width: 100%;
      margin: 0;
      padding: 35px 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
    }
    
    .purchase_content {
      width: 100%;
      margin: 0;
      padding: 25px 0 0 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
    }
    
    .purchase_item {
      padding: 10px 0;
      color: #51545E;
      font-size: 15px;
      line-height: 18px;
    }
    
    .purchase_heading {
      padding-bottom: 8px;
      border-bottom: 1px solid #EAEAEC;
    }

    .purchase_heading p {
      margin: 0;
      color: #85878E;
      font-size: 12px;
    }
    
    .purchase_footer {
      padding-top: 15px;
      border-top: 1px solid #EAEAEC;
    }
    
    .purchase_total {
      margin: 0;
      text-align: right;
      font-weight: bold;
      color: #333333;
    }
    
    .purchase_total--label {
      padding: 0 15px 0 0;
    }
    
    body {
      background-color: #F4F4F7;
      color: #51545E;
    }
    
    p {
      color: #51545E;
    }
    
    p.sub {
      color: #6B6E76;
    }
    
    .email-wrapper {
      width: 100%;
      margin: 0;
      padding: 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      background-color: #F4F4F7;
    }
    
    .email-content {
      width: 100%;
      margin: 0;
      padding: 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
    }
    /* Masthead ----------------------- */
    
    .email-masthead {
      padding: 25px 0;
      text-align: center;
    }
    
    .email-masthead_logo {
      width: 94px;
    }
    
    .email-masthead_name {
      font-size: 16px;
      font-weight: bold;
      color: #A8AAAF;
      text-decoration: none;
      text-shadow: 0 1px 0 white;
    }
    /* Body ------------------------------ */
    
    .email-body {
      width: 100%;
      margin: 0;
      padding: 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      background-color: #FFFFFF;
    }
    
    .email-body_inner {
      width: 570px;
      margin: 0 auto;
      padding: 0;
      -premailer-width: 570px;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      background-color: #FFFFFF;
    }
    
    .email-footer {
      width: nome;
      margin: 0 auto;
      padding: 0;
      -premailer-width: 570px;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      text-align: center;
    }
    
    .email-footer p {
      color: #6B6E76;
    }
    
    .body-action {
      width: 100%;
      margin: 30px auto;
      padding: 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      text-align: center;
    }
    
    .body-sub {
      margin-top: 25px;
      padding-top: 25px;
      border-top: 1px solid #EAEAEC;
    }
    
    .content-cell {
      padding: 35px;
    }
.add, .cut
{
	background: #9AF;
	box-shadow: 0 1px 2px rgba(0,0,0,0.2);
	background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
	background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
	border-radius: 0.5em;
	border-color: #0076A3;
	color: #FFF;
	cursor: pointer;
	font-weight: bold;
	text-shadow: 0 -1px 2px rgba(0,0,0,0.333);
}
input[type=text]:focus {
  border: 3px solid #555;
}
.add { margin: -2.5em 0 0; }

.add:hover { background: #00ADEE; }

.cut { opacity: 0; position: absolute; top: 0; left: -1.5em; }
.cut { -webkit-transition: opacity 100ms ease-in; }

tr:hover .cut { opacity: 1; }

@media print {
	* { -webkit-print-color-adjust: exact; }
	html { background: none; padding: 0; }
	body { box-shadow: none; margin: 0; }
	span:empty { display: none; }
	.add, .cut { display: none; }
}

@page { margin: 0; }
    </style>
    <!--[if mso]>
    <style type='text/css'>
      .f-fallback  {
        font-family: Arial, sans-serif;
      }
    </style>
  <![endif]-->
  </head>

		<header>
		adquisiciones@aoasoluciones.com; 
		   <table   style=' text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
                  <tr>
                    <td    style=' text-align: center;'  class='content-cell' align='center' >
		                   <img   style=' text-align: center;'  src='https://app.aoacolombia.com/Administrativo/img/banner-vehiculo-sustituto-AOA.jpg'>
                    </td>
                  </tr>

                </table>
				<br>
				  <table   style=' text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
                  <tr>
                    <td   style=' text-align: center;'  class='content-cell' align='center' >
                       <b  style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);' class='titulo'>Notificación de Aprobación de Requisición</b><br> </td>
                  </tr>

                </table>
			<address  style='margin-bottom: 1%; ' contenteditable>
			    <h3 class='titulo'  style='margin-bottom: 2%;  color: 1px solid rgba(118,136,29,1);'  >Número de Requisición:</td><td><b>$id</h3>
				
			</address>
			
		</header>
		<br>
		<hr>
		<hr>
		<article>
			
		<table><tr><td>Número de Requisición:</td><td><b>$id</b></td></tr>
		<tr><td>Aprobado por:</td><td><b>$Usuario [$eUsuario]</b></td></tr>
		<tr><td>Solicitado por:</td><td><b>$Solicitado_por $eSolicitado_por]</b></td></tr>
		<tr><td>Observaciones:</td><td>$observaciones</td></tr></table>
		</article>
         	
			 
		
		 <article>	
             
               
		 
			
		</article>
				

		<aside>
			   <table class='email-footer'    align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
                  <tr>
                    <td   style=' text-align: center;'  class='content-cell' align='center' >
                      <p  style=' text-align: center;'  class='f-fallback sub align-center'>
                        018000186262
                        <br>+(571) 8837069
                        <br>adquisiciones@aoasoluciones.com
						<br>http://www.aoacolombia.com
                      </p>
		            <img   style=' text-align: center;'  class='f-fallback email-masthead_name' src='https://app.aoacolombia.com/Administrativo/img/logo-footer-mail-AOA.jpg'>
                    </td>
                  </tr>

                </table>
		</aside>
	</body>
</html>");
		
	header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");
}

function descargar_imagen_requisicion()
{global $img;
	if($img)
	{
		if(!strpos(strtoupper($img),"PDF"))
		{
			header("Pragma: public");header("Expires: 0");header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=\"imagen.jpg\"");
			header("Content-Description: File Transfert");@readfile("../../Administrativo/$img");
		}
		else
		{
			header("Content-type:application/pdf");
			header("Content-Disposition:attachment;filename='downloaded.pdf'");
			readfile("../../Administrativo/$img");
		}
	}
	else
	{
		$Mensaje=urlencode(base64_encode("<font color='red'><b>No hay ninguna imágen.</b></font>" ));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();
	}
}

function mensaje_operativo_alerta(){global $Mensaje;html('AUTORIZACION');echo "<body>".base64_decode($Mensaje)." <script language='javascript'>alert('".base64_decode($Mensaje)."');</script></body>";}













?>