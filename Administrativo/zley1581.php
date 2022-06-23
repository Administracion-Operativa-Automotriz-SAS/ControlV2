<?php

	// PROGRAMA PARA ENVIO AUTOMATICO DE CORREOS.
	include('inc/funciones_.php');
	if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}
	
	function envio_uno($email1='',$dd=null)
	{
		global $email;
		if($email1) $email=$email1;
		$arregla=false;
		if(strpos('.'.$email,' ')) { $email=str_replace(' ','',$email); $arregla=true;}
		if(strpos('.'.$email,',')) { $email=str_replace(',','.',$email); $arregla=true;}
		if(strpos('.'.$email,':')) { $email=str_replace(':','.',$email); $arregla=true;}
		if(strpos('.'.$email,'á')) { $email=str_replace('á','a',$email); $arregla=true;}
		if(strpos('.'.$email,'é')) { $email=str_replace('é','e',$email); $arregla=true;}
		if(strpos('.'.$email,'í')) { $email=str_replace('í','i',$email); $arregla=true;}
		if(strpos('.'.$email,'ó')) { $email=str_replace('ó','o',$email); $arregla=true;}
		if(strpos('.'.$email,'ú')) { $email=str_replace('ú','u',$email); $arregla=true;}
		if(strpos('.'.$email,'ñ')) { $email=str_replace('ñ','n',$email); $arregla=true;}
		
		if($dd && $arregla) q("update ".$dd['tabla']." set ".$dd['campo']."='$email' where id=".$dd['id']);
		
		
		if(!$Enviado=qo("select * from aoacol_administra.ley1581 where email=\"$email\" "))
		{
			html();
			if($email1) echo "<body>$email ";
			if($dd) 
			echo " Id=".$dd['id']."<br>
			<form action='zley1581.php' target='Corregir' method='Post' name='forma' id='forma'>
			Corregir <input type='text' name='correo' id='correo' value='$email' size='50' maxlength='100'>
			<input type='hidden' name='Acc' value='corregir_correo'>
			<input type='hidden' name='tabla' value='".$dd['tabla']."'>
			<input type='hidden' name='campo' value='".$dd['campo']."'>
			<input type='hidden' name='id' value='".$dd['id']."'>
			<input type='submit' name='continuar' id='continuar' value=' CORREGIR '>
			</form>
			<iframe name='Corregir' id='Corregir' style='visibility:visible' width='10' height='5'></iframe>
			";
	//		echo "Tabla: ".$dd['tabla']." Id=".$dd['id']."<br>";
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
	
	function corregir_correo()
	{
		global $id,$tabla,$campo,$correo;
		html();
		echo "<body>id=$id tabla=$tabla campo=$campo correo=$correo ";
		q("update $tabla set $campo='$correo' where id=$id");
		echo "<script language='javascript'>parent.parent.document.forma.submit();</script></body>";
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
		<iframe name='iframe1581' id='iframe1581' style='visibility:visible' width='100%' height='100'></iframe>
		";
	}
	
	function envio_varios_ok()
	{
		global $base,$tabla,$campo;
		if($Dato=qo("select $campo as email,id from $base.$tabla where $campo not in (select email from aoacol_administra.ley1581) 
						and $campo like '%@%' order by id limit 1"))
		{
			envio_uno($Dato->email,array("tabla"=>$base.'.'.$tabla,"id"=>$Dato->id,"campo"=>$campo));
			echo "<script language='javascript'>
				var Recargar=setTimeout(siguiente,4000);
				function siguiente()
				{window.open('zley1581.php?Acc=envio_varios_ok&base=$base&tabla=$tabla&campo=$campo','_self');}
			</script>";
		}
	}
?>