<?php

#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#***************************************************FUNCIONES DE CORREO ELECTRONICO***************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

require 'PHPMailer-master/PHPMailerAutoload.php';

function intercept_mail_to_new_library($mail_object){
	
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = 'correo.aoacolombia.com';
	$mail->SMTPAuth = true;
	$mail->Username = 'sistemas@aoacolombia.com';
	$mail->Password = 'CorreoAoa2019*.*';
	$mail->SMTPSecure = 'tls';
	$mail->Port = 25;

	$mail->setFrom('sistemas@aoacolombia.com','Sistema de Control Operativo');
	
	$recipients =explode(';',$mail_object->destinatary);
	
	
	
	for($i=0;$i<count($recipients);$i++)
	{
		$recipient = explode(',',$recipients[$i]);
		
		if(isset($recipient[0]) and isset($recipient[1]))
		{ 
			
			$mail->addAddress($recipient[0],$recipient[1]);
		}
		else{
			
			$mail->addAddress($recipient[0]);
		}
	}
	
	if($mail_object->withCopyto)
	{
		$CCS=explode(';',$mail_object->withCopyto);
		for($i=0;$i<count($CCS);$i++)
		{
			$Cc=explode(',',$CCS[$i]);			
			if($Cc[1]){
				$mail->addCC($Cc[0],$Cc[1]);
			} 
			else
			{
				$mail->addCC($Cc[0]);
			}
		}
	}
	
	if($mail_object->files)
	{
		
		$Files = explode(';',$mail_object->files);
		
		for($i=0;$i<count($Files);$i++)
		{
			$attached = explode(',',$Files[$i]);
			
			if(@is_file($attached[0]) && strlen($attached[0])>0)
			{
				//echo "is file";
				if(isset($attached[1]))
				{
					$mail->addAttachment($attached[0], $attached[1]);	
				}
				else
				{
					$mail->addAttachment($attached[0]);
				}
			}
				
		}
	}
	
	
	
	//$mail->addCC('sistemas@aoacolombia.com');
	$mail->addCC('sergiocastillo@aoacolombia.com');
	//$mail->addCC('ventas.javc@gmail.com');	

	if($mail_object->from != "sistemas@aoacolombia.com" and  $mail_object->from != "")
	{ 
		$mail->addCC($mail_object->from);		
	}
	
	$mail->isHTML(true);
	$mail->Subject = $mail_object->subject;
	$mail->Body = $mail_object->content;
	$mail->XMailer = 'AOA Mailer';
	
	if(!$mail->send()) {
    //echo 'Message could not be sent.';
    echo '<br>Mailer Error: '.$mail->ErrorInfo.' <br>';
		return false;
	} else {
		return true;
		//echo 'Message has been sent';
	}
	
}

function enviar_gmail($DE='sistemas@aoacolombia.com',
							 $NOMBREDE='Sergio Castillo Castro',
							 $Destinatario="it@aoacolombia.co",
							$Concopia='',
							 $Subject='Objeto del mensaje',
							 $Contenido='<BODY>Contenido del mensaje en <b>Html</b></body>',
							 $Archivo='',   /*ruta,nombre;ruta2,nombre2..*/
							 $Usuario='sistemas@aoacolombia.com',
							 $Password='jl6316!' )
{
	
	$mail_object = new stdClass();
	$mail_object->from = $DE;
	$mail_object->namefrom = $NOMBREDE;
	$mail_object->destinatary = $Destinatario;
	$mail_object->withCopyto = $Concopia;
	$mail_object->subject = $Subject;
	$mail_object->content = $Contenido;
	$mail_object->files = $Archivo;
	$mail_object->user = $Usuario;
	$mail_object->password = $Password;
	
	return intercept_mail_to_new_library($mail_object);
	
	
	
	
	
	/*require_once 'inc/smtp/MAIL.php';
	$CORREO = new MAIL;
	$CORREO->From($DE,$NOMBREDE);
	$Destinos=explode(';',$Destinatario);
	for($i=0;$i<count($Destinos);$i++)
	{
		$Destino=explode(',',$Destinos[$i]);
//		echo "$Destino[0]  $Destino[1] ";
		if(isset($Destino[0]) and isset($Destino[1]))
		{ 
			$CORREO->AddTo($Destino[0],$Destino[1]);
		}
	}
	if($Concopia)
	{
		$Concopias=explode(';',$Concopia);
		for($i=0;$i<count($Concopias);$i++)
		{
			$Cc=explode(',',$Concopias[$i]);
			//		echo "<br />$Cc[1]  $Cc[0] ";
			if($Cc[1]) $CORREO->AddCC($Cc[0],$Cc[1]);
			else $CORREO->AddCC($Cc[0]);
		}
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
	//echo "Enviando..";
	$CONEXION = $CORREO->Connect('mail.aoacolombia.com',25, $Usuario, $Password,false, 10, 'localhost', null, false) or die(print_r($CORREO->Result));
	//$CONEXION = $CORREO->Connect('correo.aoacolombia.com',25, $Usuario, $Password) or die(print_r($CORREO->Result));
	$Resultado=$CORREO->Send($CONEXION); 
	$CORREO->Disconnect();
	return $Resultado;*/
}


function enviar_gmail_old($DE='sistemas@aoacolombia.com',
	$NOMBREDE='Sergio Castillo Castro',
	$Destinatario="it@aoacolombia.co",
	$Concopia='',
	$Subject='Objeto del mensaje',
	$Contenido='<HTML><BODY>Contenido del mensaje en <b>Html</b></body></html>',
	$Archivo='',   /*ruta,nombre;ruta2,nombre2..*/
	$Usuario='aoa_smtp@intercolombia.net',
	$Password='0l1lwpdaa' )
{
	/*require_once 'inc/smtp/MAIL.php';
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
	return $Resultado;*/
	return true;
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
	return true;
}

function enviar_mail2($De='',$Dename='',$Destinatario='',$Subject='',$Contenido='',$CC='',$BCC='',$Archivo='',$Ruta='',$Archivo2='',$Ruta2='')  // funcion mejorada de envio de mail  requiere de las rutinas SMPT2
{
	/*include_once("html/smtp2/class.phpmailer.php");
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
	return $Exito;*/
	return true;
}

function enviar_mmail($De='',$Dename='',$Destinatario='',$Subject='',$Contenido='',$CC='',$BCC='',$Archivo='',$Ruta='',$Archivo2='',$Ruta2='')
{
	/*include_once('inc/Mail/MAIL.php');
	$Mail=new MAIL;
	$Mail->From($De,$Dename);
	$Destinos = explode(',', $Destinatario);	foreach($Destinos as $Destino) 	$Mail->addto($Destino,'');
	if($CC) $Concopias=explode(',',$CC);foreach($Concopias as $Concopia) $Mail->addcc($Concopia,'');
//	if($BCC) $BConcopias=explode(',',$BCC);foreach($BConcopias as $BConcopia) $Mail->addbcc($BConcopia);
	$Mail->Subject($Subject);
	$Mail->Html($Contenido);
	if($Archivo && $Ruta) $Mail->Attach(file_get_contents($Ruta), FUNC::mime_type($Ruta), $Archivo, null, null, 'attachment', MIME::unique());
	if($Archivo2 && $Ruta2) $Mail->Attach(file_get_contents($Ruta2), FUNC::mime_type($Ruta2), $Archivo2, null, null, 'attachment', MIME::unique());
	if($Mail->send()) return true; else return false;*/
	return true;
}

function enviar_smail($De='',$Dename='',$Destinatario='',$Subject='',$Contenido='',$CC='',$BCC='')
{
	/*include_once('inc/Mail/SMTP.php');
	$c=SMTP::Connect('mail.aoacolombia.com',25,'sistemas@aoacolombia.com','Sistemas2010');
	$m='From: '.$De."\n".'To: '.$Destinatario."\n".($CC?'Cc: '.$CC."\n":'').($BCC?'Bcc: '.$BCC."\n":'').'Subject: '.$Subject."\n".'Content-Type: text/html'."\n\n".$Contenido;
	$s=SMTP::Send($c,array($Destinatario),$m,$De);
	SMTP::disconnect($c);
	if($s) return true; else return false;*/
	return true;
}




























?>