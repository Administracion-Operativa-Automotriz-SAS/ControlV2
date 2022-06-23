<?php

#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#***************************************************FUNCIONES DE CORREO ELECTRONICO***************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


function enviar_gmail($DE='sistemas@aoacolombia.com',
							 $NOMBREDE='Arturo Quintero Rodriguez',
							 $Destinatario="administracion@intercolombia.net",
							$Concopia='',
							 $Subject='Objeto del mensaje',
							 $Contenido='<BODY>Contenido del mensaje en <b>Html</b></body>',
							 $Archivo='',   /*ruta,nombre;ruta2,nombre2..*/
							 $Usuario='sistemas@aoacolombia.com',
							 $Password='jl6316!' )
{
	require_once 'inc/smtp/MAIL.php';
	$CORREO = new MAIL;
	$CORREO->From($DE,$NOMBREDE);
	$Destinos=explode(';',$Destinatario);
	for($i=0;$i<count($Destinos);$i++)
	{
		$Destino=explode(',',$Destinos[$i]);
//		echo "$Destino[0]  $Destino[1] ";
		$CORREO->AddTo($Destino[0],$Destino[1]);
	}
	if($Concopia)
	{
		$Concopias=explode(';',$Concopia);
		for($i=0;$i<count($Concopias);$i++)
		{
			$Cc=explode(',',$Concopias[$i]);
			//		echo "<br />$Cc[1]  $Cc[0] ";
			$CORREO->AddCC($Cc[0],$Cc[1]);
		}
	}

	$CORREO->Subject($Subject);
	$CORREO->Html($Contenido);
	$Archivos=explode(';',$Archivo);
	for($i=0;$i<count($Archivos);$i++)
	{
		$Adjunto=explode(',',$Archivos[$i]);
		if(@is_file($Adjunto[0]) && strlen($Adjunto[0]))
			$CORREO->Attach(file_get_contents($Adjunto[0]),FUNC::mime_type($Adjunto[0]),$Adjunto[1], null, null, 'inline', MIME::unique());
	}
	$CONEXION = $CORREO->Connect('mail.aoacolombia.com', 465, $Usuario, $Password, 'tls', 10, 'localhost', null, 'plain') or die(print_r($CORREO->Result));
	$Resultado=$CORREO->Send($CONEXION);
	$CORREO->Disconnect();
	return $Resultado;
}


function enviar_gmail_old($DE='sistemas@aoacolombia.com',
	$NOMBREDE='Arturo Quintero Rodriguez',
	$Destinatario="administracion@intercolombia.net",
	$Concopia='',
	$Subject='Objeto del mensaje',
	$Contenido='<HTML><BODY>Contenido del mensaje en <b>Html</b></body></html>',
	$Archivo='',   /*ruta,nombre;ruta2,nombre2..*/
	$Usuario='aoa_smtp@intercolombia.net',
	$Password='0l1lwpdaa' )
{
	require_once 'inc/smtp/MAIL.php';
	$CORREO = new MAIL;
	$CORREO->From($DE,$NOMBREDE);
	$Destinos=explode(';',$Destinatario);
	for($i=0;$i<count($Destinos);$i++)
	{
		$Destino=explode(',',$Destinos[$i]);
		echo "$Destino[0]  $Destino[1] ";
		$CORREO->AddTo($Destino[0],$Destino[1]);
	}
	$CORREO->Subject($Subject);
	$CORREO->Html($Contenido);
	$Archivos=explode(';',$Archivo);
	for($i=0;$i<count($Archivos);$i++)
	{
		$Adjunto=explode(',',$Archivos[$i]);
		if(@is_file($Adjunto[0]) && strlen($Adjunto[0])>0)
			$CORREO->Attach(file_get_contents($Adjunto[0]),FUNC::mime_type($Adjunto[0]),$Adjunto[1], null, null, 'inline', MIME::unique());
	}
	$CONEXION = $CORREO->Connect('smtp.gmail.com', 587, $Usuario, $Password, 'tls', 10, 'localhost', null, 'plain') or die(print_r($CORREO->Result));
	$Resultado=$CORREO->Send($CONEXION);
	$CORREO->Disconnect();
	return $Resultado;
}

#	$mail->Host = SMTP_SERVIDOR;
#	$mail->SMTPAuth = SMTP_VALIDACION;
#	$mail->Username = SMTP_USUARIO;
#  $mail->Password = SMTP_PASSWORD;
#  $mail->Port=SMTP_PUERTO;

function enviar_mail($De = 'remitente@servidor',/* direccion email del remitente */
	$NombreDe = 'Remitente',/* nombre del remitente */
	$Destinatario = 'destinatario@servidor',/* direccion(es) mail del destinatario o destinatarios separados por coma */
	$Referencia = 'Asunto',/* Asunto del mail */
	$Txt_mail = '',/* texto del mail */
	$CC='', /* con copia */
	$BCC='' /* con copia oculta */
	)
{
	if($Smtp_validacion == 'false') $Valida = false;
	elseif($Smtp_validacion == 'true') $Valida = true;
	else $Valida = $Smtp_validacion;

	include_once 'html/smtp/smtp.php';
	$mail = new SMTP;
	$mail->Delivery('relay');
	$mail->Relay('mail.aoacolombia.com', 'sistemas@aoacolombia.com', 'Sistemas2010', 25 * 1,  'autodetect' /*autorizacion para el smtp puede ser autodetect | login| plain */,  false /*validacion puede ser false | true | ssl | tls */);
	$mail->TimeOut(10);
	$mail->Priority('Normal');
	$mail->From($De, $NombreDe);

	$Destinos = explode(',', $Destinatario); foreach($Destinos as $Destino)	$mail->AddTo($Destino, '');
	$ConCopias = explode(',', $CC); foreach($ConCopias as $CC) if($CC) 	$mail->Addcc($CC, '');
	$BConCopias = explode(',', $BCC); foreach($BConCopias as $BCC) if($BCC) 	$mail->Addbcc($BCC);
	$mail->Html($Txt_mail);
	$sent = $mail->Send($Referencia);
	IF($sent)
		return true;
	else
		return false;
}

function enviar_mail2($De='',$Dename='',$Destinatario='',$Subject='',$Contenido='',$CC='',$BCC='',$Archivo='',$Ruta='',$Archivo2='',$Ruta2='')  // funcion mejorada de envio de mail  requiere de las rutinas SMPT2
{
	include_once("html/smtp2/class.phpmailer.php");
	$mail = new phpmailer();
	$mail->PluginDir = "html/smtp2/";
	$mail->Mailer = "mail";
	$mail->Host = 'mail.aoacolombia.com';
	$mail->SMTPAuth = false;
	$mail->Username = 'sistemas@aoacolombia.com';
    $mail->Password = 'Sistemas2010';
	$mail->From = $De;
	$mail->FromName = $Dename;
	$mail->Subject = $Subject;
	$Destinos = explode(',', $Destinatario); foreach($Destinos as $Destino) 	$mail->AddAddress($Destino);
	$Concopia=explode(',',$CC);foreach($Concopia as $Conc) $mail->AddCC($Conc,'');
	$BConcopia=explode(',',$BCC);foreach($BConcopia as $BConc) $mail->AddbCC($BConc,'');
	$mail->IsHTML(true);
	$mail->Body = $Contenido;
	if($Archivo && $Ruta) $mail->AddAttachment($Ruta,$Archivo);
	if($Archivo2 && $Ruta2) $mail->AddAttachment($Ruta2,$Archivo2);
	$Exito= $mail->Send();
	if(!$Exito) echo $mail->ErrorInfo;
	return $Exito;
}

function enviar_mmail($De='',$Dename='',$Destinatario='',$Subject='',$Contenido='',$CC='',$BCC='',$Archivo='',$Ruta='',$Archivo2='',$Ruta2='')
{
	include_once('inc/Mail/MAIL.php');
	$Mail=new MAIL;
	$Mail->From($De,$Dename);
	$Destinos = explode(',', $Destinatario);	foreach($Destinos as $Destino) 	$Mail->addto($Destino,'');
	if($CC) $Concopias=explode(',',$CC);foreach($Concopias as $Concopia) $Mail->addcc($Concopia,'');
//	if($BCC) $BConcopias=explode(',',$BCC);foreach($BConcopias as $BConcopia) $Mail->addbcc($BConcopia);
	$Mail->Subject($Subject);
	$Mail->Html($Contenido);
	if($Archivo && $Ruta) $Mail->Attach(file_get_contents($Ruta), FUNC::mime_type($Ruta), $Archivo, null, null, 'attachment', MIME::unique());
	if($Archivo2 && $Ruta2) $Mail->Attach(file_get_contents($Ruta2), FUNC::mime_type($Ruta2), $Archivo2, null, null, 'attachment', MIME::unique());
	if($Mail->send()) return true; else return false;
}

function enviar_smail($De='',$Dename='',$Destinatario='',$Subject='',$Contenido='',$CC='',$BCC='')
{
	include_once('inc/Mail/SMTP.php');
	$c=SMTP::Connect('mail.aoacolombia.com',25,'sistemas@aoacolombia.com','Sistemas2010');
	$m='From: '.$De."\n".'To: '.$Destinatario."\n".($CC?'Cc: '.$CC."\n":'').($BCC?'Bcc: '.$BCC."\n":'').'Subject: '.$Subject."\n".'Content-Type: text/html'."\n\n".$Contenido;
	$s=SMTP::Send($c,array($Destinatario),$m,$De);
	SMTP::disconnect($c);
	if($s) return true; else return false;
}




























?>