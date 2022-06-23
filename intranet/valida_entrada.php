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
	echo "<body oncontextmenu='return false' >
	<H2 align=center>Usuario No definido en este Sistema. Consulte con el Administrador</h2>
	<a href='marcoindex.php?Acc=ingreso_sistema&SESION_PUBLICA=1' target='_self'>Volver a Intentarlo</a><br>";
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
		echo "<body oncontextmenu='return false' >$R->idnombre<H2 align=center>Clave Incorrecta</h2>
		<a href='marcoindex.php?Acc=ingreso_sistema&SESION_PUBLICA=1' target='_self'>Volver a Intentarlo</a><br>";
		die();
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
	IF (!$ENCONTRADO)
	{
		session_start();session_unset();session_destroy();html();
		echo "<body oncontextmenu='return false' ><H2 align=center>Usuario No definido en este Sistema. Consulte con el Administrador</h2>
				<a href='marcoindex.php?Acc=ingreso_sistema&SESION_PUBLICA=1' target='_self'>Volver a Intentarlo</a><br>";
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
			html();
			if(!is_dir('inc/cp')) {mkdir('inc/cp',0740);}
			$A=fopen('inc/cp/'.$IDuser.'.php',"w+");
			fwrite($A,"<?php
			/*Archvio inicial */
			echo \"<select name='_cs_' id='_cs_' style='width:100px' onchange=\\\"crea_perfil(this.value,'_top')\\\"><option value=''>Cambiar Perfil</option>\"; ");
			ECHO "<body>
			<h4><b>Por favor seleccione el perfil de seguridad: </b></h4>
			<form name='p' id='p'>
			<select name='per' id='per' style='width:200px' onchange=\"
			var Expira=new Date();
			Expira.setTime(Expira.getTime()+30*24*60*60*1000);
			crea_perfil(this.value,'_top');this.value='';\"><option value=''>Seleccione un perfil</option>";
			for($I=0;$I<count($PERFIL);$I++)
			{
				$Comando="_SESSION['Nick']='".$PERFIL[$I]['Nick']."';";
				$Comando.="_SESSION['User']='".$PERFIL[$I]['User']."';";
				$Comando.="_SESSION['Disenador']='".$PERFIL[$I]['Disenador']."';";
				$Comando.="_SESSION['Id_alterno']='".$PERFIL[$I]['Id_alterno']."';";
				$Comando.="_SESSION['Nombre']='".$PERFIL[$I]['Nombre']."';";
				$Comando.="_SESSION['Tabla_usuario']='".$PERFIL[$I]['Tabla_usuario']."';";
				$Comando.="_SESSION['Email']='".$PERFIL[$I]['Email']."';";
				$Comando.="_SESSION['Ngrupo']='".$PERFIL[$I]['Nombre_Perfil']."'";
				$Comando=base64_encode($Comando);$C=$IDuser;
				echo "<option value='$Comando' >".$PERFIL[$I]['Nombre_Perfil']."</option>";
				fwrite($A,"echo \"<option value='$Comando' >".$PERFIL[$I]['Nombre_Perfil']."</option>\";
				");
			}
			fwrite($A,"echo \"</select>\";
			?>");
			close($A,$C);
			echo "</select>
			</form><input type='button' value='Cerrar Sesion' onclick='mata_perfil();'></body>";
			die();
		}
		$ENCONTRADO=0;
	}
}
else
{
	echo "<body oncontextmenu='return false' onload=\"opener.location='index.php?Acc=r';window.close();void(null);\">";
	echo "No hay usuarios alternos<br>";
	die('');
}

?>