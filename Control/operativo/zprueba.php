<?php

function enviar_gmail2($DE='sistemas@aoacolombia.com',
	$NOMBREDE='Arturo Quintero Rodriguez',
	$Destinatario="arturoquintero@aoacolombia.com,Arturo Quintero",
	$Concopia='arturoquintero@aoacolombia.com',
	$Subject='Objeto del mensaje',
	$Contenido='<BODY>Contenido del mensaje en <b>Html</b></body>',
	$Archivo='',   /*ruta,nombre;ruta2,nombre2..*/
	$Usuario='sistemas@aoacolombia.com',
	$Password='Sistemas2011' )
{
	require_once 'inc/smtp/MAIL.php';
	$CORREO = new MAIL;
	$CORREO->From($DE,$NOMBREDE);
	$Destinos=explode(';',$Destinatario);
	for($i=0;$i<count($Destinos);$i++)
	{
		$Destino=explode(',',$Destinos[$i]);
		echo "<br />$Destino[1]  $Destino[0] ";
		$CORREO->AddTo($Destino[0],$Destino[1]);
	}
	$Concopias=explode(';',$Concopia);
	for($i=0;$i<count($Concopias);$i++)
	{
		$Cc=explode(',',$Concopias[$i]);
		echo "<br />$Cc[1]  $Cc[0] ";
		$CORREO->AddCC($Cc[0],$Cc[1]);
	}
	$CORREO->Subject($Subject);
	$CORREO->Html($Contenido);
	$Archivos=explode(';',$Archivo);
	for($i=0;$i<count($Archivos);$i++)
	{
		$Adjunto=explode(',',$Archivos[$i]);
		if(@is_file($Adjunto) && strlen($Adjunto)>0)
			$CORREO->Attach(file_get_contents($Adjunto[0]),FUNC::mime_type($Adjunto[0]),$Adjunto[1], null, null, 'inline', MIME::unique());
	}
	$CONEXION = $CORREO->Connect('mail.aoacolombia.com',465, $Usuario, $Password, 'ssl', 10, 'localhost', null, 'plain') or die(print_r($CORREO->Result));
	$Resultado=$CORREO->Send($CONEXION);
	$CORREO->Disconnect();
	return $Resultado;
}

echo enviar_gmail2();

?>