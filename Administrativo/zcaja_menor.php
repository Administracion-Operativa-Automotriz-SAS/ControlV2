<?php

/**
 *   LIBRERIA DE FUNCIONES PROPIAS DE LA CAJA MENOR
 *
 * @version $Id$
 * @copyright 2011
 */
include('inc/funciones_.php');
sesion();
if (!empty($Acc) && function_exists($Acc)){	eval($Acc . '();');	die();}

function revision_cm()
{
	global $id;
	html("REVISION DE CAJA MENOR");
	echo "<body><h3>Revisión de Caja Menor</h3>
				Por seguridad se solicita la contraseña del usuario actual para registrar esta operación.<br /><br />
				<form action='zcaja_menor.php' method='post' target='_self' name='forma' id='forma'>
					Clave de confirmación: <input type='password' name='Clave' id='Clave'><br /><br />
					<input type='submit' value='CONTINUAR'>
					<input type='hidden' name='Acc' value='revision_cm1'>
					<input type='hidden' name='id' value='$id'>
				</form>";
}

function revision_cm1()
{
	global $id,$Clave;
	if(verificar_password($_SESSION['Nick'],$Clave))
	{
		if($_SESSION['User']==3)
		{
			$Revisa_cm=qo1("select revisa_cm from usuario_admin where id=".$_SESSION['Id_alterno']);
			if(!$Revisa_cm) die("<body><script language='javascript'>alert('No tiene privilegios para revisar Caja Menor'); window.close();void(null);</script></body>");
		}
		if($_SESSION['User']==2)
		{
			$Revisa_cm=qo1("select revisa_cm from usuario_admin where id=".$_SESSION['Id_alterno']);
			if(!$Revisa_cm) die("<body><script language='javascript'>alert('No tiene privilegios para revisar Caja Menor'); window.close();void(null);</script></body>");
		}
		if($_SESSION['User']>3) die("<body><script language='javascript'>alert('No tiene privilegios para revisar Caja Menor'); window.close();void(null);</script></body>");
		echo "<body>
			<form action='zcaja_menor.php' method='post' target='_self' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='revision_cm2'>
				<input type='hidden' name='id' value='$id'>
			</form>
			<script language='javascript'>document.forma.submit();</script>
			</body>";
	}
	else
	{
		html('ERROR DE VALIDACION');
		graba_bitacora('caja_menor','O',$id,'Revisión de Caja Menor fallida por error en contraseña.');
		echo "<script language='javascript'>
				function carga()
				{
					alert('Clave Incorrecta. Esta inconsistencia de clave erronea quedó grabada en la bitacora del registro de caja menor.');
					window.close();void(null);
				}
			</script>
			<body onload='carga()'></body>";
	}
}

function revision_cm2()
{
	global $id;
	q("update caja_menor set revisado_por='".$_SESSION['Nombre']."' where id=$id");
	graba_bitacora('caja_menor','M',$id,"Revisa Caja Menor");
	echo "<body><script language='javascript'>alert('Revisión Satisfactoria registada a nombre de ".$_SESSION['Nombre']."');window.close();void(null);</script>";
}

function aprobacion_cm()
{
	global $id;
	html("APROBACION DE CAJA MENOR");
	echo "<body><h3>Aprobación de Caja Menor</h3>
				Por seguridad se solicita la contraseña del usuario actual para registrar esta operación.<br /><br />
				<form action='zcaja_menor.php' method='post' target='_self' name='forma' id='forma'>
					Clave de confirmación: <input type='password' name='Clave' id='Clave'><br /><br />
					<input type='submit' value='CONTINUAR'>
					<input type='hidden' name='Acc' value='aprobacion_cm1'>
					<input type='hidden' name='id' value='$id'>
				</form>";
}

function aprobacion_cm1()
{
	global $id,$Clave;
	if(verificar_password($_SESSION['Nick'],$Clave))
	{
		if($_SESSION['User']==3)
		{
			$Revisa_cm=qo1("select aprueba_cm from usuario_admin where id=".$_SESSION['Id_alterno']);
			if(!$Revisa_cm) die("<body><script language='javascript'>alert('No tiene privilegios para aprobar Caja Menor'); window.close();void(null);</script></body>");
		}
		if($_SESSION['User']==2)
		{
			$Revisa_cm=qo1("select aprueba_cm from usuario_admin where id=".$_SESSION['Id_alterno']);
			if(!$Revisa_cm) die("<body><script language='javascript'>alert('No tiene privilegios para aprobar Caja Menor'); window.close();void(null);</script></body>");
		}
		if($_SESSION['User']>3) die("<body><script language='javascript'>alert('No tiene privilegios para aprobar Caja Menor'); window.close();void(null);</script></body>");
		echo "<body>
			<form action='zcaja_menor.php' method='post' target='_self' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='aprobacion_cm2'>
				<input type='hidden' name='id' value='$id'>
			</form>
			<script language='javascript'>document.forma.submit();</script>
			</body>";
	}
	else
	{
		html('ERROR DE VALIDACION');
		graba_bitacora('caja_menor','O',$id,'Revisión de Caja Menor fallida por error en contraseña.');
		echo "<script language='javascript'>
				function carga()
				{
					alert('Clave Incorrecta. Esta inconsistencia de clave erronea quedó grabada en la bitacora del registro de caja menor.');
					window.close();void(null);
				}
			</script>
			<body onload='carga()'></body>";
	}
}

function aprobacion_cm2()
{
	global $id;
	q("update caja_menor set aprobado_por='".$_SESSION['Nombre']."' where id=$id");
	graba_bitacora('caja_menor','M',$id,"Aprueba Caja Menor");
	echo "<body><script language='javascript'>alert('Aprobación Satisfactoria registada a nombre de ".$_SESSION['Nombre']."');window.close();void(null);</script>";
}


?>