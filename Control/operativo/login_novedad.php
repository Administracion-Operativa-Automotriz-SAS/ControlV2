<?php

if(isset($_POST['g-recaptcha-response']))
{
	if(!$_POST['g-recaptcha-response']) {echo "<body><script language='javascript'>alert('Debe indicar que NO ES UN ROBOT.');window.history.back();</script></body>";die();}
}
if($_POST['iDU'] && $_POST['cLU'])
{
	$IDuser=trim($_POST['iDU']);
	$_POST['iDU']='';
	$clave=trim($_POST['cLU']);
	$_POST['cLU']='';
}
if(!$IDuser)
{
	session_start();
	session_unset();
	session_destroy();
	include_once('inc/funciones_.php');
	html();
	echo "Hola";
	die('');
}

include_once('inc/funciones_.php');
session_cache_limiter('private');
$PASA_IMAP=0;
$PERFIL=array();

if(file_exists('config/imapsi.php'))
{
	REQUIRE('config/imapsi.php');
	if(!$Resultado_imap=imap_open(SERVIDOR_IMAP,"$IDuser","$clave"))
	{
		echo "<body oncontextmenu='return false' onload=\"opener.location='index.php?Acc=r';window.close();void(null);\">";
		echo "<H2 align=center>EL USUARIO O LA CLAVE SON INCORRECTOS</h2>
		<a href='marcoindex.php?Acc=ingreso_sistema&SESION_PUBLICA=1' target='_self'>Volver a Intentarlo</a>
		<BR>$Resultado_imap";
		die('');
	}
	$PASA_IMAP=1;
	imap_close($Resultado_imap);
	// inicio de la busqueda de los usuarios dentro del sistema con el mismo nombre de usuario $Usuario
}

if(file_exists('config/logingoogle.php') )
{echo "Autenticado con Google";require('config/logingoogle.php');$PASA_IMAP=1;}

$ENCONTRADO=0;


if($R=qo("select * from usuario where strcmp(idnombre,'$IDuser')=0"))
{
	if(strcmp($R->clave,e($clave))==0 || $PASA_IMAP || $CAMBIA_PERFIL)
	{
		$PERFIL[$ENCONTRADO]['Nick']=$IDuser;
		$PERFIL[$ENCONTRADO]['User']=$R->id;
		$PERFIL[$ENCONTRADO]['Id_alterno']=$R->id;
		$PERFIL[$ENCONTRADO]['Tabla_usuario']='usuario';
		$PERFIL[$ENCONTRADO]['Disenador']=$R->design;
		$PERFIL[$ENCONTRADO]['Nombre']=$R->nombre;
		$PERFIL[$ENCONTRADO]['Campo_clave']='clave';
		$PERFIL[$ENCONTRADO]['Nombre_Perfil']=$R->nombre;
		$PERFIL[$ENCONTRADO]['Email']=$R->email;
		$ENCONTRADO++;
	}
	else
	{
		session_start();session_unset();session_destroy();html();
		echo "
				
						
								
							Pailas manos 
	
	
	";
			
	}
}
 	




if($S1=q("select * from usuario where LENGTH(alt_tabla)>0 && LENGTH(alt_id)>0 && LENGTH(alt_pass)>0 && LENGTH(alt_nombre)>0"))
{
	while ($R1=mysql_fetch_object($S1))
	{
		if($S2=q("select id,$R1->alt_nombre as nombre, $R1->alt_pass as Clave,email from $R1->alt_tabla where strcmp($R1->alt_id,'$IDuser')=0"))
		{
			if ($R2=mysql_fetch_object($S2))
			{
				IF(strcmp($R2->Clave,e($clave))==0 || $PASA_IMAP || $CAMBIA_PERFIL)
				{
					$PERFIL[$ENCONTRADO]['Nick']=$IDuser;
					$PERFIL[$ENCONTRADO]['User']=$R1->id;
					$PERFIL[$ENCONTRADO]['Disenador']=$R1->design;
					$PERFIL[$ENCONTRADO]['Id_alterno']=$R2->id;
					$PERFIL[$ENCONTRADO]['Nombre']=$R2->nombre;
					$PERFIL[$ENCONTRADO]['Tabla_usuario']=$R1->alt_tabla;
					$PERFIL[$ENCONTRADO]['Nombre_Perfil']=$R1->nombre;
					$PERFIL[$ENCONTRADO]['Email']=$R1->email;
					$ENCONTRADO++;
				}
			}
		}
	}
	
		if($ENCONTRADO){
		$Alternas=q("select nombre,alt_tabla, alt_id,alt_pass from usuario where alt_tabla!='' and alt_id!='' and alt_pass!='' ");
			while($Alt=mysql_fetch_object($Alternas))
			{
				//qo("UPDATE $Alt->alt_tabla  SET auditoria_clave = '0'");
			   
			   if($idu=qo("select id,$Alt->alt_id,clave,auditoria_clave from $Alt->alt_tabla where $Alt->alt_id='$IDuser'"))
				{
					//$validarTablas=qo("select auditoria_clave from $Alt->alt_tabla where id = '$idu->id'");
					
					$validarTablas=qo("select auditoria_clave,validar_clave from $Alt->alt_tabla where id = '$idu->id'");
				
				if($validarTablas->validar_clave == 1){
					
					echo 5;
					
						 die();
					  }
				
					if($validarTablas->auditoria_clave == '0'){
						
						$fecha_actual = date("Y-m-d");
						$tiempo_adicional = date("Y-m-d",strtotime($fecha_actual."+ 90 days"));
						
	 qo("INSERT INTO auditoria_clave (usuario,clave,fecha_cambio,tiempo_adicional) VALUES ('$idu->usuario','$idu->clave','$fecha_actual','$tiempo_adicional')");
						
	$varLast=qo("Select * from auditoria_clave order by id DESC LIMIT 1");
						qo("UPDATE $Alt->alt_tabla  SET auditoria_clave = '$varLast->id' WHERE id = '$idu->id'");
						
					}else{
						/*
						$varAuditoria=qo("select * from auditoria_clave where id = '$idu->auditoria_clave'");
						 
						  $varTipoAdicional = $varAuditoria->tiempo_adicional;
						  $fecha_actual = date("Y-m-d");
						  
						if($fecha_actual > $varTipoAdicional){
							
							echo "<script language='javascript'>alert('El ultimo cambio de contraseña registrado supera 6 meses de tiempo. A continuación se le solicitará una nueva contraseña.');
					       window.open('marcoindex.php?Acc=cambio_pass','_self');</script>";
			              die();
						}else{
							echo "<script> console.log('El tiempo es menor');</script>";
						}
					*/
					}
				}
			}
	}
	
	
	
	IF (!$ENCONTRADO)
	{
		session_start();session_unset();session_destroy();
		echo 1;
				die('');
	}
	else
	{
		if(count($PERFIL)==1)
		{
			if(is_file('inc/cp/'.$IDuser.'.php')) unlink('inc/cp/'.$IDuser.'.php');
			html();
			$Comando="_SESSION['Nick']='".$PERFIL[0]['Nick']."';";
			$Comando.="_SESSION['User']='".$PERFIL[0]['User']."';";
			$Comando.="_SESSION['Disenador']='".$PERFIL[0]['Disenador']."';";
			$Comando.="_SESSION['Id_alterno']='".$PERFIL[0]['Id_alterno']."';";
			$Comando.="_SESSION['Nombre']='".$PERFIL[0]['Nombre']."';";
			$Comando.="_SESSION['Tabla_usuario']='".$PERFIL[0]['Tabla_usuario']."';";
			$Comando.="_SESSION['Email']='".$PERFIL[0]['Email']."';";
			$Comando.="_SESSION['Ngrupo']='".$PERFIL[0]['Nombre_Perfil']."'";
			$Comando=urlencode(base64_encode($Comando));
			ECHO "<Body onload=\"crea_perfil('$Comando','_top');\">
			Perfil: ".$PERFIL[0]['Nombre_Perfil'];
			echo "<br><input type='button' value='Cerrar Sesion' onclick='mata_perfil();'>
			<input type='button' value='Reingresar' onclick=\"crea_perfil('$Comando','_top');\"></body>";
			die();
		}
		if(count($PERFIL)>1)
		{
			ECHO json_encode($PERFIL);
			die();
		}
		$ENCONTRADO=8;
	}
}
else
{
	echo "<body oncontextmenu='return false' onload=\"opener.location='index.php?Acc=r';window.close();void(null);\">";
	echo "No hay usuarios alternos<br>";
	die('');
}

?>

