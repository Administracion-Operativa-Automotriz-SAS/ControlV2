<?php

	// PROGRAMA PARA ENVIO AUTOMATICO DE CORREOS.
	include('inc/funciones_.php');
	if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}
	
	function envio_uno($email1='',$dd=null)
	{
		global $email;
		if($email1) $email=$email1;
		if(!$Enviado=qo("select * from aoacol_administra.ley1581 where email=\"$email\" "))
		{
			html();
			if($email1) echo "<body>$email ";
			if($dd)
			echo "Tabla: ".$dd['tabla']." Id=".$dd['id']."<br>";
			enviar_gmail(
				'gerencia@aoacolombia.com' /*de */,
				'AOA COLOMBIA S.A.' /*Nombre de */ ,
				"$email" /*para */,
				"gerencia@aoacolombia.com" /*con copia*/,
				"LEY 1581 PROTECCION DE DATOS PERSONALES" /*Objeto */,
nl2br("
Bogotá D.C. ".date('d')." de ".mes(date('m'))." de ".date('Y').".

Reciba un cordial saludo de AOA.

Dando cumplimiento a la reglamentación de protección de datos personales consagrada en la Ley 1581 de 2012 y reglamentada a través del Decreto 1377 de 2013, con la finalidad de garantizar la confidencialidad de su información y uso adecuado de sus datos, nos gustaría contar con su autorización para que AOA pueda continuar con su información en la base de datos.

De acuerdo a lo anterior, si no está interesado en continuar en nuestra base de datos solicitamos de la manera más respetuosa que nos lo haga saber por este medio.

En caso tal de no recibir respuesta de este correo, dentro de un término de treinta (30) días hábiles, habilitará a AOA,  continuar con su información en nuestra base de datos conforme a lo establecido en el artículo 10 del Decreto 1377 de 2013. 

Gracias por su colaboración.

Cordialmente,

SEBASTIAN HURTADO
Representante Legal AOA Colombia S.A.
<img src='http://app.aoacolombia.com/img/AOAlogo.jpg' title='AOA COLOMBIA S.A. SE MUEVE CONTIGO'/>
")
			);
			$Ahora=date('Y-m-d H:i:s');
			q("insert into aoacol_administra.ley1581 (email,fecha) values (\"$email\",'$Ahora')");
			
			echo "<br>Correo enviado a $email ";
			if(!$email1) echo "</body>";
		}
		else
		{
			html();
			if(!$email1) echo "<body>";
			echo "<b style='color:red'>Ya habia sido enviado el correo $email el día $Enviado->fecha id: $Enviado->id </b>";
			if(!$email1) echo "</body>";
		}
	}
	
	function envio_varios()
	{
		html();
		echo "<body>
		<form action='zley1581.php' target='iframe1581' method='POST' name='forma' id='forma'>
			Base de datos: <input type='text' name='base' id='base' value='aoacol_aoacars' size='20' maxlength='50'><br>
			Tabla: <input type='text' name='tabla' id='tabla' value='cliente' size='20' maxlength='50'><br>
			Campo: <input type='text' name='campo' id='campo' value='email_e' size='20' maxlength='50'><br>
			<input type='button' name='procesar' id='procesar' value=' PROCESAR ' onclick='document.forma.submit();'>
			<input type='hidden' name='Acc' value='envio_varios_ok'>
		</form>
		<iframe name='iframe1581' id='iframe1581' style='visibility:visible' width='100%' height='200'></iframe>
		";
	}
	
	function envio_varios_ok()
	{
		global $base,$tabla,$campo;
		if($Dato=qo("select $campo as email,id from $base.$tabla where $campo not in (select email from aoacol_administra.ley1581) 
						and $campo like '%@%' order by id limit 1"))
		{
			envio_uno($Dato->email,array("tabla"=>$base.'.'.$tabla,"id"=>$Dato->id));
			echo "<script language='javascript'>
				var Recargar=setTimeout(siguiente,5000);
				function siguiente()
				{window.open('zley1581.php?Acc=envio_varios_ok&base=$base&tabla=$tabla&campo=$campo','_self');}
			</script>";
		}
	}
?>