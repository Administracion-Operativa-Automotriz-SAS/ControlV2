<?php
include('inc/funciones_.php');
sesion();
$USUARIO=$_SESSION['User'];
$NUSUARIO=$_SESSION['Nombre'];
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');	die();}

function envio_correo_infoerronea()
{
	html();
	$Aseguradoras=q("select id,nombre from aseguradora where id not in (6) order by orden_monitor");
	$Opcion_Aseg='';
	if($Aseguradoras)
	while($Aseg=mysql_fetch_object($Aseguradoras)){	$Opcion_Aseg.="<option value='$Aseg->id'>$Aseg->nombre</option>";}

	echo "<script language='javascript'>
	function validar2(Valor)
	{
		var Aseguradoras=getSelected(document.forma.ASEG);
		var Aseg='';
		for(var i=0;i<Aseguradoras.length;i++)
		{
			if(i>0) Aseg+=',';
			Aseg+=Aseguradoras[i].value;
		}
		document.forma.Aseguradora.value=Aseg;
		document.forma.submit();
	}
	function re_iniciar()
	{
		document.location.reload();
	}
	</script><body>
	<script language='javascript'>centrar();</script>
	<iframe name='Oculto_envio' id='Oculto_envio' style='visibility:hidden;position:absolute;' width='98%' height='95%'></iframe>
	<h3>ENVIO DE CORREOS A ASEGURADORAS .:. INFORMACION DE DATOS DE CONTACTO CON ERRORES</h3>
	<form action='zcontrol_operativo.php' target='tablero_correos' method='POST' name='forma' id='forma'>
		Seleccione la Aseguradora: <select name='ASEG' multiple size=7>$Opcion_Aseg</select>
		<input type='button' name='consultar' id='consultar' value=' CONSULTAR ' onclick='validar2();'>
		<input type='hidden' name='Aseguradora' value=''>
		<input type='hidden' name='Acc' value='envio_correo_infoerronea_consultar'>
	</form>
	<iframe name='tablero_correos' id='tablero_correos' style='visibility:visible' width='100%' height='70%'></iframe>
	</body>";
}

function envio_correo_infoerronea_consultar()
{
	// numero telefonico q tenemos, y copia de los correos a sofia lugo
	global $Aseguradora;
	$Ase=q("select * from aseguradora where id in ($Aseguradora)");
	html();
	echo "<script language='javascript'>
	var Incluidos='';
	function incluye(dato)
	{
		var Caja=document.getElementById('C'+dato);
		if(Caja.checked) Incluidos+=','+dato; else {alert('No puedo desmarcar, debe empezar de nuevo.');Caja.checked=true;}
	}
	function enviar_email()
	{
		document.forma.ids.value=Incluidos;
		parent.document.getElementById('Oculto_envio').style.visibility='visible';
		document.forma.submit();
	}
	function re_iniciar(){ parent.re_iniciar();}

	</script><body bgcolor='ffffff'>
	Aseguradoras: $Aseguradoras ";
	if($Errores=q("Select c.id,c.siniestro,s.numero,s.ingreso,concat(s.declarante_telefono,' ',s.declarante_celular,' ',s.declarante_tel_resid,' ',s.declarante_tel_ofic,
	' ',s.declarate_tel_otro,' ',conductor_tel_resid,' ',s.conductor_tel_ofic,' ',s.conductor_celular,' ',s.conductor_tel_otro) as dcontacto
	from call2infoerronea c,siniestro s where s.id=c.siniestro and s.aseguradora in ($Aseguradora)
							and fecha_envio='0000-00-00 00:00:00'  order by ingreso"))
	{
		echo " ".mysql_num_rows($Errores)." registros encontrados.
			<table border cellspacing='0'><tr>
			<th>Id Interno</th>
			<th>Numero</th>
			<th>Ingreso</th>
			<th>Seguimiento</th>
			<th>Datos de Contacto</th>
			<th>Enviar</th>
			</tr>";
		include('inc/link.php');
		while($E =mysql_fetch_object($Errores ))
		{
			echo "<tr>
				<td>$E->siniestro</td>
				<td>$E->numero</td>
				<td>$E->ingreso</td>
				<td>";
			if($Seguimientos=q("select *,t_tipifica_seguimiento(tipificacion) as ntip from seguimiento where siniestro=$E->siniestro and tipo=4"))
			{
				echo "<table width='100%'>";
				while($S=mysql_fetch_object($Seguimientos))
				{
					echo "<tr><td width='50%'>$S->fecha $S->hora $S->ntip</td></tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "No hay registros de seguimiento.";
			}
			echo "</td><td>$E->dcontacto</td>
			<td align='center'><input type='checkbox' name='C$E->siniestro' id='C$E->siniestro' onchange='incluye($E->siniestro);'></td></tr>";
		}
		echo "</table>
		<br><br><center><input type='button' name='enviar' id='enviar' value=' VISUALIZAR CORREO ' style='font-size:20px;' onclick='enviar_email();'></center>
		<form action='zcontrol_operativo.php' target='Oculto_envio' method='POST' name='forma' id='forma'>
			<input type='hidden' name='ids' value=''>
			<input type='hidden' name='Acc' value='enviar_email_infoerronea'>
		</form>

		";
	}
	else
	{
		echo "<b style='font-size:14px;color:aa0000'>NO HAY INFORMACION CORRESPONDIENTE A LA ASEGURADORA $Ase->nombre</b>";
	}
	echo "</body>";
}

function enviar_email_infoerronea()
{
	global $ids;
	$arregloIDS=explode(',',$ids);
	html();

	$Aseguradora=qo("select * from aseguradora,siniestro where siniestro.aseguradora=aseguradora.id and siniestro.id=".$arregloIDS[1]);
	if(strpos(' '.$Aseguradora->razon_social,'ALLIANZ'))
	{
		$Contenido="
		Señores:
		$Aseguradora->razon_social
		La Ciudad.
		Ref: Información de contacto errónea.

		Estimados señores:

		A continuación relaciono los encargos que el día de hoy presentaron datos incompletos o erroneos dentro de la plataforma Allia2net.com.co:

		";
		$Siniestros=q("select s.id,s.numero,s.ingreso,s.asegurado_nombre,s.placa,concat(s.declarante_telefono,' ',s.declarante_celular,' ',s.declarante_tel_resid,' ',s.declarante_tel_ofic,
		' ',s.declarate_tel_otro,' ',conductor_tel_resid,' ',s.conductor_tel_ofic,' ',s.conductor_celular,' ',s.conductor_tel_otro) as dcontacto
		from siniestro s where id in (0 $ids) order by s.ingreso");
		$Contenido.=" <table cellspacing=10><tr><td>SINIESTRO</td><td>ASEGURADO</td><td>INGRESO</td><td>PLACA</td><td>COMENTARIO</td><td>DATOS DE CONTACTO</td></tr>";
		include('inc/link.php');
		while($Si=mysql_fetch_object($Siniestros))
		{
			$Comentario=qo1m("select t_tipifica_seguimiento(tipificacion) as ntip from seguimiento where siniestro=$Si->id and tipo=4 order by id desc limit 1",$LINK);
			$Contenido.="<tr><td>$Si->numero</td><td>$Si->asegurado_nombre</td><td>".date('Y-m-d',strtotime($Si->ingreso))."</td><td>$Si->placa</td><td>$Comentario</td><td>$Si->dcontacto</td></tr>";
		}
		mysql_close($LINK);
		$Contenido.="</table>
		Quedo atenta a su pronta respuesta.

		Cordialmente,

		SANDRA MARCELA OSORIO VARGAS
		Auxiliar de Siniestros AOA Colombia S.A.
		PBX 57 (1) 7560510 Ext 115  Fax 57 (1) 7560512
		Carrera 69B Número 98A-10 Barrio Morato.
		Email: siniestros@aoacolombia.com
		Bogotá D.C. - Colombia.

		";
	}
	//elseif(strpos(' '.$Aseguradora->razon_social,'ROYAL'))
	else
	{
		$Contenido="
		Señores:
		$Aseguradora->razon_social
		La Ciudad.
		Ref: Información de contacto errónea.

		Estimados señores:

		Agradezco su colaboración para confirmar con los siguientes números de siniestro la información de contacto y placa del asegurado:

		";
		$Siniestros=q("select s.id,s.numero,s.ingreso,s.asegurado_nombre,s.placa,concat(s.declarante_telefono,' ',s.declarante_celular,' ',s.declarante_tel_resid,' ',s.declarante_tel_ofic,
		' ',s.declarate_tel_otro,' ',conductor_tel_resid,' ',s.conductor_tel_ofic,' ',s.conductor_celular,' ',s.conductor_tel_otro) as dcontacto
		from siniestro s where id in (0 $ids) order by s.ingreso");
		$Contenido.=" <table cellspacing=10><tr><td>SINIESTRO</td><td>ASEGURADO</td><td>INGRESO</td><td>PLACA</td><td>COMENTARIO</td><td>DATOS DE CONTACTO</td></tr>";
		include('inc/link.php');
		while($Si=mysql_fetch_object($Siniestros))
		{
			$Comentario=qo1m("select t_tipifica_seguimiento(tipificacion) as ntip from seguimiento where siniestro=$Si->id and tipo=4 order by id desc limit 1",$LINK);
			$Contenido.="<tr><td>$Si->numero</td><td>$Si->asegurado_nombre</td><td>".date('Y-m-d',strtotime($Si->ingreso))."</td><td>$Si->placa</td><td>$Comentario</td><td>$Si->dcontacto</td></tr>";
		}
		mysql_close($LINK);
		$Contenido.="</table>
		Quedo atenta a su pronta respuesta.

		Cordialmente,

		SANDRA MARCELA OSORIO VARGAS
		Auxiliar de Siniestros AOA Colombia S.A.
		PBX 57 (1) 7560510 Ext 115  Fax 57 (1) 7560512
		Carrera 69B Número 98A-10 Barrio Morato.
		Email: siniestros@aoacolombia.com
		Bogotá D.C. - Colombia.

		";
	}
	echo "
	<script language='javascript'>
	function enviar_correo()
	{
		document.getElementById('enviar').style.visibility='hidden';
		document.forma.submit();
	}
	</script><body bgcolor='eeffee'><script language='javascript'>
	</script>
	Cuerpo de la carta:
	<table border=5 cellpadding=10><tr><td style='font-size:14px'>".nl2br($Contenido)."</td></tr></table>

	<form action='zcontrol_operativo.php' target='_self' method='POST' name='forma' id='forma'>
		<input type='hidden' name='cuerpo' value=\"$Contenido\">
		<input type='hidden' name='Acc' value='enviar_email_infoerronea_ok'>
		<!--
		Destinatario: <input type='text' name='destino' id='destino' value='$Aseguradora->email_soporte_e' size=100><br>
		Con copia a: <input type='text' name='concopia' id='concopia' value='$Aseguradora->email_copia' size=100><br>
		-->
		Destinatario: <input type='text' name='destino' id='destino' value='' size=100><br>
		Con copia a: <input type='text' name='concopia' id='concopia' value='' size=100><br>		
		<input type='hidden' name='ids' value='$ids'>
		<input type='hidden' name='razon_social' value='$Aseguradora->razon_social'>
	</form><br><br>
	<center><input type='button' name='enviar' id='enviar' value=' ENVIAR CORREO ' style='font-size:22px' onclick='enviar_correo();'></center>
	<br><br><br><br>
	</body>";
}

function enviar_email_infoerronea_ok()
{
	global $cuerpo,$ids,$destino,$concopia,$razon_social,$NUSUARIO;	
	
	
//	if($Envio=enviar_gmail('siniestros@aoacolombia.com' /*de */ ,'SINIESTROS AOA COLOMBIA S.A.' /*nombre de */ ,	"$destino,$razon_social" /*para */ ,"$concopia"   /*Con copia*/ ,	"DATOS ERRADOS $razon_social"  /*OBJETO*/,nl2br($cuerpo) /*mensaje */))
	//html();
	//echo "<body>Destino: $destino<br>Con copia: $concopia<br>Razón Social: $razon_social<br><br><br>";
	$Envio=enviar_gmail('siniestros@aoacolombia.com' /*de */ ,'SINIESTROS AOA COLOMBIA S.A.' /*nombre de */ ,	$destino /*para */ ,$concopia  /*Con copia*/ ,	"DATOS ERRADOS $razon_social"  /*OBJETO*/,nl2br($cuerpo) /*mensaje */);
	
	if($Envio)
	{	
		echo "Correo mandado exitosamente <br>";
		
		$Fecha=date('Y-m-d'); $Hora=date('H:i:s');$Ahora=date('Y-m-d H:i:s');
		$aIDS=explode(',',$ids);

		//include('inc/link.php');
		foreach($aIDS as $siniestro)
		{
			if($siniestro != null)
			{ 
				echo "<br>$siniestro :";
				$sql = " update call2infoerronea set fecha_envio = '$Ahora',enviado_por= '$NUSUARIO' where siniestro='$siniestro' and fecha_envio='0000-00-00 00:00:00' ";
				//echo $sql;
				if(!q($sql))
				{
					//echo "no se pudo hacer consulta <br>";
					
					//die(mysql_error());
				}
				else
				{
					//echo "registro actualizado <br>";
					
				}
				
			}
		}		
		
		mysql_close($LINK);
		
		echo "<script language='javascript'>alert('EMAIL ENVIADO CORRECTAMENTE');parent.re_iniciar();</script>";
	}
	else{ echo "<script language='javascript'>alert('NO SE PUDO MANDAR EL CORREO ELECTRÓNICO');parent.re_iniciar();</script>";}
}

function actualizar_info()
{
	global $id,$ids;
	if($id) $D=qo("select * from call2infoerronea where id=$id");
	if($ids) $id=qo1("select id from call2infoerronea where siniestro=$ids");
	html("ACTUALIZACION DE INFORMACION");
	echo "<script language='javascript'>
	function validar_actualizacion()
	{
		with(document.forma)
		{
			if(!alltrim(actualizacion_aseg.value)) { alert('Debe digitar la información correspondiente a la actualización de datos de contacto');actualizacion_aseg.style.backgroundColor='ffff99';actualizacion_aseg.focus();return false;}
			submit();
		}
	}
	function finalizar(){window.close();void(null);}
	function re_comenzar(){ finalizar();}
	</script><body><script language='javascript'>centrar(600,300);</script>
	<form action='zcontrol_operativo.php' target='Oculto_actualizacion' method='POST' name='forma' id='forma'>
		<h3>Actualización de datos de contacto:</h3>
		Número de contacto: <input type='number' name='telefono_contacto' id='telefono_contacto' value='' size='20' maxlength='20' placeholder='celular'>
		Datos de contacto: <br>
		<textarea name='actualizacion_aseg' id='actualizacion_aseg' cols=80 rows=5></textarea><br>
		Correo electrónico: <input type='text' name='correo' id='correo' value='$Sin->declarante_email' size='60' maxlength='200'><br>
		<input type='button' name='continuar' id='continuar' value=' ACTUALIZAR INFORMACION ' onclick='validar_actualizacion()'>
		<input type='hidden' name='Acc' value='caso_actualizar_datos_ok'><input type='hidden' name='id' value='".($ids?$ids:$D->siniestro)."'>
		<input type='hidden' name='idcola' value='$id'>
	</form>
	<iframe name='Oculto_actualizacion' id='Oculto_actualizacion' style='visibility:hidden' width='1' height='1'></iframe>
	</body>";
}

function caso_actualizar_datos_ok()
{
	global $id,$correo,$actualizacion_aseg,$NUSUARIO,$idcola,$telefono_contacto;
	$Ahora=date('Y-m-d H:i');
	q("update siniestro set declarante_email='$correo',actualizacion_aseg=concat(actualizacion_aseg,\"\n$NUSUARIO [$Ahora]:  $actualizacion_aseg\") where id=$id");

	$S=qo("select * from siniestro where id=$id");
	$varTelefonoCel = $S->declarante_celular;
	
	$H1=date('Y-m-d'); $H2=date('H:i:s');
	$Idn=q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'$H1','$H2','$NUSUARIO','Actualización de datos de contacto: Telefono Anterior <h2>$varTelefonoCel</h2>',8)");
	
	if($S->declarante_celular) q("update siniestro set declarante_celular='$telefono_contacto' where id=$id ");
	elseif(!$S->declarante_tel_resid) q("update siniestro set declarante_tel_resid='$telefono_contacto' where id=$id ");
	elseif(!$S->declarante_tel_ofic) q("update siniestro set declarante_tel_ofic='$telefono_contacto' where id=$id ");
	elseif(!$S->declarante_telefono) q("update siniestro set declarante_telefono='$telefono_contacto' where id=$id ");
	elseif(!$S->declarate_tel_otro) q("update siniestro set declarate_tel_otro='$telefono_contacto' where id=$id ");

	graba_bitacora('siniestro','M',$id,'Actualiza correo declarante_email y actualizacion de datos de contacto');
	
	if($idcola) q("update call2infoerronea set fecha_proceso='$Ahora',procesado_por='$NUSUARIO' where id=$idcola");
	echo "<body><script language='javascript'>alert('Información actualizada');parent.finalizar();</script></body>";
}

?>