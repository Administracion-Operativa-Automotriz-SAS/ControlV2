<?php
/*
//// SERVICIO DE VEHICULO DE REEMPLAZO PARA EL ASEGURADO 

INGRESA CON UN CODICO UNICO QUE TIENE UNA VIGENCIA LIMITADA
*/

include('inc/funciones_.php');
if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}

menu_w_principal();

function menu_w_principal()
{
	global $c;
	html('AOACOLOMBIA.COM - VEH�CULO DE REEMPLAZO');
	echo "
	<script language='javascript'>
		function entrar()
		{
			var Placa=document.getElementById('placa').value;
			var Codigo=document.getElementById('codigo').value;
			if(!alltrim(Placa)) {alert('Debe digitar una placa v�lida');return false;}
			if(!alltrim(Codigo)) {alert('Debe digitar un codigo valido'); return false;}
			window.open('w_servicio.php?Acc=validar_ingreso&p='+Placa+'&c='+Codigo,'Oculto_servicio');
		}
		function entrar2(d)
		{
			window.open('w_servicio.php?Acc=presenta_caso&id='+d,'_self');
		}
		function validar_placa(Evento)
		{
			var Valido='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			var P=document.getElementById('placa');
			var keynum;
			if(window.event) keynum = Evento.keyCode;
			else if(Evento.which) keynum = Evento.which;
			//alert(keynum);
			if(keynum==107) keynum=187;if(keynum==109) keynum=189;
			if(keynum==106) keynum=187;if(keynum==111) keynum=189;
			if(keynum==96) keynum=48;
			if(keynum==8) return true;
			var Caracter=String.fromCharCode(keynum);
			if(Valido.indexOf(Caracter)==-1) 
			{
				alert('Error '+keynum+' No debe usar guiones, comas, puntos, espacios etc. solo digite la placa sin ningun separador');
				//P.value=P.value.replace(Caracter,'');
				P.value=P.value.slice(0,-1);
			}
			P.value=P.value.toUpperCase();
		}
	</script>
	<body style='font-size:20px'>
		<h3 align='center'> .:. AOA COLOMBIA S.A. .:. SERVICIO DE VEH�CULO DE REEMPLAZO .:. </h3>
		<center>
		<br>
		Se�or(a) Asegurado(a), por favor digite la placa de su veh�culo: <input type='text' style='font-size:20px' name='placa' id='placa' onkeyup='validar_placa(event);' size='5'><br>
		<br>Por favor digite el c�digo de acceso a su servicio enviado por correo electr�nico: <input type='text' style='font-size:20px' id='codigo' name='codigo' size='10' value='$c'>
		<br><BR>
		<a onclick='entrar();' style='cursor:pointer'><img src='img/fachada_carrera.jpg' height='300px' BORDER=5><br><b style='font-size:18px;color:000099;'>CLICK EN LA IMAGEN PARA ENTRAR</b></A>
		</center>
		<script language='javascript'>document.getElementById('placa').focus();</script>
		<iframe name='Oculto_servicio' id='Oculto_servicio' style='visibility:hidden' width='1' height='1'></iframe>
	</body>";
}

function validar_ingreso()
{
	global $p,$c;
	if($id=qo1("select c.id from siniestro s,call2cola2 c where c.siniestro=s.id and s.placa='$p' and c.codigo='$c'"))
		echo "<body><script language='javascript'>parent.entrar2($id);</script></body>";
	else
		echo "<body><script language='javascript'>alert('PLACA O CODIGO INCORRECTO');</script></body>";
}

function presenta_caso()
{
	global $id;
	html('AOA COLOMBIA S.A. - SERVICIO DE VEH�CULO DE REEMPLAZO');
	if($D=qo("select * from call2cola2 where id='$id'"))
	{
		$Sin=qo("select * from siniestro where id=$D->siniestro");
		$Ase=qo("select * from aseguradora where id=$Sin->aseguradora");
		if(inlist($Ase->id,'1,8,9'))
		{
			if($Sin->dias_servicio==7) $Ase->archivo_contrato='contrato_allianz7.pdf';
			elseif($Sin->dias_servicio>7) $Ase->archivo_contrato='contrato_allianz10.pdf';
		}
		echo "<script language='javascript'>
		function bajar_terminos() {window.open('w_servicio.php?Acc=bajar_archivo_contrato&Archivo=$Ase->archivo_contrato&Salida=contratovehiculoreemplazo.pdf&id=$D->id','Oculto_tyc');}
		function aceptacion_terminos() { if(confirm('SEGURO QUE DESEA ACEPTAR TERMINOS Y CONDICIONES DEL SERVICIO?')) window.open('w_servicio.php?Acc=acepta_terminos&id=$id','_self');}
		function noaceptacion_terminos() {window.open('w_servicio.php?Acc=noacepta_terminos&id=$id','_self');}
		
		</script><body style='font-size:16px'><h3>AOA COLOMBIA S.A. SERVICIO DE VEH�CULO DE REEMPLAZO</h3>
			Se�or(a) <b>$Sin->asegurado_nombre</b> Bienvenido(a) al sistema de adjudicaci�n del Servicio de Veh�culo de Reemplazo de 
			Administraci�n Operativa Automotriz S.A.<br><br>
			Usted posee una p�liza de seguros con la aseguradora: <b>$Ase->razon_social</b> y tuvo un siniestro con su veh�culo de placas: <b>$Sin->placa</b>.
			Ahora puede obtener el beneficio del Veh�culo de Reemplazo y Administraci�n Operativa Automotriz S.A. es quien le presta este servicio.<br><br>";
		if($D->aceptado)
		{
			echo "<b style='color:000066;'>Se�or(a) $Sin->asegurado_nombre: Usted ya acept� los t�rminos y condiciones del Servicio el d�a: $D->fecha_aceptacion y debi� 
			recibir un correo electr�nico a la direcci�n <i>$Sin->declarante_email</i> con toda la informaci�n y recomendaciones para la toma del Servicio.</b><br><br>
			Recuerde las recomendaciones que debe tener en cuenta: ";
		}
		else
		{
			echo "Para acceder al servicio del Veh�culo de Reemplazo Usted debe:";
		}
		echo "<br><br>
			<li> Poseer una tarjeta de cr�dito con cupo disponible de: <b>$".coma_format($Ase->garantia)." (".enletras($Ase->garantia)." PESOS MCTE.) </b> para constituir 
			la garant�a, requisito del servicio. Tambi�n puede solicitar a un tercero que le sea garante mediante tarjeta de cr�dito para constituir la garant�a. En este caso esa persona debe acompa�arlo(a) a la cita
			de entrega del veh�culo para firmar el voucher de la garant�a.<br><br>
			<li> Presentar su Licencia de Conducci�n original en el momento de la entrega del veh�culo.<br><br>
			<li> Presentar su Documento de Identificaci�n original en el momento de la entrega de veh�culo.<br><br>
			<li> Si usted no es el (la) asegurado(a), debe presentar una carta diligenciada por el (la) asegurado(a) indicando que usted est� autorizado(a) para proceder con la gesti�n del servicio. 
			Adicionalmente debe traer una fotocopia de la C�dula de Ciudadan�a del (la) asegurado(a) o fotocopia de C�mara y Comercio de la Entidad asegurada.<br><br>
			<li> Haber le�do, impreso y firmado los t�rminos y condiciones del servicio.
			<a onclick='bajar_terminos()' style='cursor:pointer;font-size:16px;color:000099'><i><u>Click aqu� para descargar los T�rminos y Condiciones</u></i></a>
			<br><br>
			<li> Suministrar la informaci�n correspondiente a: n�mero de cuenta bancaria, tipo de cuenta, entidad, nombre e identificaci�n del cuenta habiente. Esto en caso de que 
			existan cobros inferiores a la garant�a y se requiera devolver alg�n excedente.
			<br><br>
			<B>NOTA:</B> Las franquicias aceptadas son: Visa, Master Card, American Express y Diners Club. <b>No se aceptan tarjetas como</b>: Falabella, Exito, Carrefour, Easy, Codensa, La 14, entre otras.
			<br><br>";
		if($D->aceptado)
		{
			echo "  ";
		}
		else
		{
			echo "Se�or(a) Asegurado(a), por favor lea los t�rminos y condiciones y si est� de acuerdo con el contrato, impr�malo, f�rmelo 
			y de click en el  bot�n <b>ACEPTO TERMINOS Y CONDICIONES</b>. <br><br>
			<table align='center' width='50%'><tr>
				<td bgcolor='ffffff' align='center'><a style='cursor:pointer' onclick='aceptacion_terminos();'><img src='img/aceptacionterminos.png' border=5 height='120px'><br>
										ACEPTO TERMINOS Y CONDICIONES</a></td>
				<td bgcolor='ffffff' align='center'><a style='cursor:pointer' onclick='noaceptacion_terminos();'><img src='img/noaceptacionterminos.png' border=5 height='120px'><br>
									NO ACEPTO TERMINOS Y CONDICIONES</a></td>
			</tr></table>
			<iframe name='Oculto_tyc' id='Oculto_tyc' style='visibility:hidden' width='1' height='1'></iframe>";
		}
		echo "</body>";
	}
	else
	echo "<body><b style='font-size:20px;color:aa0000;'>ERROR EN LA OBTENCI�N DE LA INFORMACI�N</b></body>";
}

function acepta_terminos()
{
	global $id;
	html('AOA COLOMBIA S.A. - SERVICIO DE VEH�CULO DE REEMPLAZO');
	if($D=qo("select * from call2cola2 where id='$id'"))
	{
		$Sin=qo("select * from siniestro where id=$D->siniestro");
		$Fecha=date('Y-m-d');$Hora=date('H:i:s');$Ahora=$Fecha.' '.$Hora;$Codigo_seguimiento=18; /*18: Aceptaci�n de T�rminos y Condiciones */
		q("update call2cola2 set aceptado=1,fecha_aceptacion='$Ahora' where id=$id");
		graba_bitacora('call2cola2','M',$id,'Acepta Terminos y Condiciones');
		$Idn=q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($Sin->id,'$Sin->asegurado_nombre','$Fecha','$Hora','ACEPTACION DE TERMINOS Y CONDICIONES.',$Codigo_seguimiento)");
		//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
		q("update siniestro set observaciones=concat(observaciones,\"\n$Sin->asegurado_nombre [$Ahora]: ACEPTA TERMINOS Y CONDICIONES.\") where id=$Sin->id");
		$Ase=qo("select * from aseguradora where id=$Sin->aseguradora");
		$correo_asegurado=$Sin->declarante_email;
		html('AOA COLOMBIA S.A. SERVICIO DE VEH�CULO DE REEMPLAZO');
		echo "<body style='font-size:16px'><h3>AOA COLOMBIA S.A. SERVICIO DE VEH�CULO DE REEMPLAZO</h3>
		Se�or(a) Asegurado(a) $Sin->asegurado_nombre, usted ha aceptado los T�rminos y Condiciones del Servicio de Veh�culo de Reemplazo de Administraci�n Operativa Automotriz S.A.<br><br>
		En la pr�xima hora h�bil, lo contactar� el �rea de Adjudicaciones para informarle la fecha, hora y lugar de la cita donde se le entregar� el veh�culo asignado.<br><br>
		Adicionalmente le llegar� un correo electr�nico a la cuenta <i>$correo_asegurado</i> con informaci�n importante para el acceso al servicio.<br><br>
		En cualquier momento puede volver a consultar su servicio ingresando a http://www.aoacolombia.com/servicio y digitando su placa y <i>C�digo �nico de Servicio</i>.<br><br>
		Cualquier duda adicional con gusto ser� atendida en el tel�fono: $Ase->numero_call en Bogot� D.C. o en el correo direccioncallcenter@aoacolombia.com.
		
		</body>";
		
		enviar_gmail('direccioncallcenter@aoacolombia.com' /*de */ ,'DIRECCION CALL CENTER AOACOLOMBIA' /*nombre de */ ,	"$correo_asegurado,$Sin->asegurado_nombre" /*para */ ,
		"direccioncallcenter@aoacolombia.com,ARTURO QUINTERO"   /*Con copia*/ ,	"ACEPTACION TERMINOS Y CONDICIONES"  /*OBJETO*/,
		nl2br("
		Se�or(a) Asegurado(a) $Sin->asegurado_nombre,
		
		Reciba cordial saludo. 
		
		Usted ha ACEPTADO los T�rminos y Condiciones del Servicio de Veh�culo de Reemplazo de Administraci�n Operativa Automotriz S.A. 
		
		En la pr�xima hora h�bil, lo contactar� el �rea de Adjudicaciones para tomar sus datos e informarle la fecha, hora y lugar de la cita dondes se le entregar� el veh�culo asignado.
		
		Recuerde tener a mano la informaci�n de su Tarjeta de Cr�dito, cuenta bancaria y su documento de identificaci�n para realizar el proceso de registro interno.
		
		Tan pronto como el Agente de Call Center le asigne el la cita para la entrega del veh�culo, se enviar� de forma autom�tica la informaci�n de la misma a su correo electr�nico.
		
		En cualquier momento puede volver a consultar su servicio ingresando a http://www.aoacolombia.com/servicio y digitando su placa y C�digo �nico de Servicio.

		Cualquier inquietud con gusto ser� atendida en el tel�fono: $Ase->numero_call en Bogot� D.C. o en el correo electr�nico direccioncallcenter@aoacolombia.com.
		
		Si tiene inconvenientes, sugerencias, reclamos  o solicitudes puede hacerlo al correo pqr@aoacolombia.com.
		
		Cordialmente,
		
		DIRECCI�N CALL CENTER
		AOACOLOMBIA.COM
		www.aoacolombia.com
		
		") /*mensaje */);
	}
}

function noacepta_terminos()
{
	global $id;
	if($D=qo("select * from call2cola2 where id='$id'"))
	{
		$Sin=qo("select * from siniestro where id=$D->siniestro");
		html('AOA COLOMBIA S.A. SERVICIO DE VEH�CULO DE REEMPLAZO');
		echo "<script language='javascript'>
		function valida_no_adj()
		{
			with(document.forma)
			{
				if(!motivo_no_adjudicacion.value)
				{
					alert('Debe seleccionar un Motivo para registrar el Rechazo de Terminos y Condiciones'); return false;motivo_no_adjudicacion.style.backgroundColor='ffff00';
				}
				submit();
			}
		}
		</script>
		<body style='font-size:16px'><h3>AOA COLOMBIA S.A. SERVICIO DE VEH�CULO DE REEMPLAZO</h3>
		Se�or(a) Asegurado(a) $Sin->asegurado_nombre, usted desea <b>RECHAZAR</b> los T�rminos y Condiciones del Servicio de Veh�culo de Reemplazo de Administraci�n Operativa Automotriz S.A.<br><br>
		Por favor ind�quenos el motivo a continuaci�n y de click en CONTINUAR para finalizar su elecci�n:<br><br>
		<form action='' target='_self' method='POST' name='forma' id='forma'>
		MOTIVO: <select name='motivo_no_adjudicacion' >";
		$Opciones=q("select id,t_causal(causal) as ncausal, nombre from subcausal where cancelacion_aseg=1 order by ncausal,nombre");
		$Causal='';
		while($Cau=mysql_fetch_object($Opciones))
		{
			if($Causal!=$Cau->ncausal)
			{
				if($Causal!='') echo "</optgroup>";
				$Causal=$Cau->ncausal;
				echo "<optgroup label='$Causal'>";
			}
			echo "<option value='$Cau->id'>$Cau->nombre</option>";
		}
		echo "</optgroup></select><br><br>
		<input type='button' name='continuar' id='continuar' value=' CONTINUAR ' style='font-isze:18px' onclick='valida_no_adj();'>
		<input type='hidden' name='Acc' value='noacepta_terminos_ok'>
		<input type='hidden' name='id' value='$id'>
		<input type='hidden' name='naseg' value='$Sin->asegurado_nombre'>
		<input type='hidden' name='idsin' value='$Sin->id'>
		</form>
		";
	}
}

function noacepta_terminos_ok()
{
	global $id,$motivo_no_adjudicacion,$idsin,$naseg;
	$Ahora=date('Y-m-d H:i:s');
	$Subc=qo("select * from subcausal where id='$motivo_no_adjudicacion' ");
	$ncausal=qo1("select nombre from causal where id=$Subc->causal").' - '.$Subc->nombre;
	$Sin=qo("select * from siniestro where id=$idsin");
	$Ase=qo("select nombre,razon_social,numero_call from aseguradora where id=$Sin->aseguradora");
	$Fecha_vencimiento=aumentadias($Sin->ingreso,30);
	$Fecha=date('Y-m-d');$Hora=date('H:i:s');$Codigo=6; /*6: No Adjudicaci�n */
	q("update siniestro set estado=1,causal=$Subc->causal,subcausal='$motivo_no_adjudicacion',
		observaciones=concat(observaciones,'\n$naseg [$Ahora]: No Adjudica. Causal:$ncausal') where id=$idsin"); // cambia el estado a NO ADJUDICADO = 1
	graba_bitacora('siniestro','M',$idsin,'Rechaza Terminos y Condiciones del Servicio.');
	$Idn=q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($idsin,'$naseg','$Fecha','$Hora','Rechaza Terminos y Condiciones del Servicio causal: $ncausal. ',$Codigo)");
	q("update call2cola2 set estado=6 where siniestro=$idsin");
	q("delete from call2cola1 where siniestro=$idsin");
	q("delete from call2cola3 where siniestro=$idsin");
	q("delete from call2cola4 where siniestro=$idsin");
	
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	//q("update call2proceso set fecha_cierre='$Ahora',estado='C' where siniestro='$idsin' and estado='A' ");
	html();
	echo "<body style='font-size:16px'><h3>AOA COLOMBIA S.A. SERVICIO DE VEH�CULO DE REEMPLAZO</h3>
		Se�or(a) Asegurado(a) $Sin->asegurado_nombre,<br><br>
		Usted ha rechazado exitosamente los T�rminos y Condiciones del Servicio de Veh�culo de Reemplazo de Administraci�n Operativa Automotriz S.A. y el motivo fue el siguiente: <br><br>
		<b>$ncausal</b><br><br>
		Si en alg�n momento desea solicitar nuevamente el servicio puede comunicarse al n�mero <b>$Ase->numero_call</b> en Bogot� D.C. o al correo electr�nico <b>direccioncallcenter@aoacolombia.com</b>. 
		Este Servicio tiene vigencia desde <b>".fecha_completa($Sin->ingreso)."</b> hasta <b>".fecha_completa($Fecha_vencimiento)."</b> 
	</body>";
}

function formulario_garantia()
{
	html('AOACOLOMBIA.COM - VEH�CULO DE REEMPLAZO - FORMULARIO DE GARANT�A');
	echo "
	<script language='javascript'>
		function entrar()
		{
			var Placa=document.getElementById('placa').value;
			var Codigo=document.getElementById('codigo').value;
			if(!alltrim(Placa)) {alert('Debe digitar una placa v�lida');return false;}
			if(!alltrim(Codigo)) {alert('Debe digitar un codigo valido'); return false;}
			window.open('w_servicio.php?Acc=validacion_formulario_garantia&p='+Placa+'&c='+Codigo,'Oculto_servicio');
		}
		function entrar3(d)
		{
			window.open('w_servicio.php?Acc=formulario_garantia1&id='+d,'_self');
		}
	</script>
	<body style='font-size:16px'>
		<h3 align='center'> .:. AOA COLOMBIA S.A. .:. SERVICIO DE VEH�CULO DE REEMPLAZO .:. FORMULARIO DE GARANT�A</h3>
		<center>
		<br><br><br>
		Se�or(a) Asegurado(a), por favor digite la placa de su veh�culo: <input type='text' name='placa' id='placa' onkeyup='javascript:this.value=this.value.toUpperCase();' size='5'><br>
		<br>Por favor digite el c�digo de acceso a su servicio enviado por correo electr�nico: <input type='text' id='codigo' name='codigo' size='10'>
		<br><BR>
		<a onclick='entrar();' style='cursor:pointer'><img src='img/fachada_carrera.jpg' height='300px' BORDER=5><br><b style='font-size:18px;color:000099;'>CLICK EN LA IMAGEN PARA ENTRAR</b></A>
		</center>
		<iframe name='Oculto_servicio' id='Oculto_servicio' style='visibility:hidden' width='1' height='1'></iframe>
	</body>";
}

function validacion_formulario_garantia()
{
	global $p,$c;
	if($id=qo1("select c.id from siniestro s,call2cola2 c where c.siniestro=s.id and s.placa='$p' and c.codigo='$c'"))
		echo "<body><script language='javascript'>parent.entrar3($id);</script></body>";
	else
		echo "<body><script language='javascript'>alert('PLACA O CODIGO INCORRECTO');</script></body>";
}

function formulario_garantia1()
{
	global $id;
	html('AOACOLOMBIA.COM - VEH�CULO DE REEMPLAZO - FORMULARIO DE GARANTIA');
	$Validacion_vencimiento=date('Ym');
	echo "
	<script language='javascript'>
		function valida_envio()
		{
			with(document.forma)
			{
				if(!Number(identificacion.value)) {alert('Debe escribir correctamente el n�mero de identificaci�n, sin comas ni puntos');identificacion.style.backgroundColor='ffff55';identificacion.focus();return false;}
				if(!tipo_id.value) {alert('Debe seleccionar el tipo de identificaci�n'); tipo_id.style.backgroundColor='ffff55';tipo_id.focus();return false;}
				if(!alltrim(lugar_expdoc.value)) {alert('Debe escribir lugar de expedici�n de la Identificaci�n'); lugar_expdoc.style.backgroundColor='ffff55';lugar_expdoc.focus(); return false;}
				if(!alltrim(nombre.value)) {alert('Debe escribir el nombre del tarjetahabiente o autorizado'); nombre.style.backgroundColor='ffff55';nombre.focus(); return false;}
				if(!alltrim(apellido.value)) {alert('Debe escribir el apellido del tarjetahabiente o autorizado'); apellido.style.backgroundColor='ffff55';apellido.focus(); return false;}
				if(!sexo.value) {alert('Debe seleccionar el sexo'); sexo.style.backgroundColor='ffff55';sexo.focus();return false;}
				if(!ciudad.value) { alert('Debe seleccionar una ciudad.'); ciudad.style.backgroundColor='ffff44'; ciudad.focus(); return false; }
				if(!alltrim(direccion.value)) { alert('Debe digitar la direcci�n de residencia.'); direccion.style.backgroundColor='ffff44'; direccion.focus(); return false; }
				if(!alltrim(telefono_oficina.value)) { alert('Debe digitar el telefono de oficina.'); telefono_oficina.style.backgroundColor='ffff44'; telefono_oficina.focus(); return false; }
				if(!alltrim(telefono_casa.value)) { alert('Debe digitar el telefono de la vivienda.'); telefono_casa.style.backgroundColor='ffff44'; telefono_casa.focus(); return false; }
				if(!alltrim(celular.value)) { alert('Debe digitar el telefono celular.'); celular.style.backgroundColor='ffff44'; celular.focus(); return false; }
				if(!alltrim(franquicia.value)) {alert('Debe seleccionar la franquicia de la tarjeta de cr�dito'); franquicia.style.backgroundColor='ffff55';franquicia.focus(); return false;}
				if(!alltrim(numero_tarjeta.value)) {alert('Debe escribir el numero completo de la tarjeta de cr�dito'); numero_tarjeta.style.backgroundColor='ffff55';numero_tarjeta.focus(); return false;}
				if(!Number(numero_tarjeta.value)) {alert('Debe escribir el numero completo de la tarjeta de cr�dito'); numero_tarjeta.style.backgroundColor='ffff55';numero_tarjeta.focus(); return false;}
				if(!banco.value) {alert('Debe seleccionar el banco que expidi� la tarjeta de cr�dito'); banco.style.backgroundColor='ffff55';banco.focus(); return;}
				if(!alltrim(vencimiento_mes.value)) {alert('Debe seleccionar el mes de vencimiento de la tarjeta de cr�dito'); vencimiento_mes.style.backgroundColor='ffff55';vencimiento_mes.focus(); return;}
				if(!alltrim(vencimiento_ano.value)) {alert('Debe seleccionar el a�o de vencimiento de la tarjeta de cr�dito');vencimiento_ano.style.backgroundColor='ffff55';vencimiento_ano.focus(); return;}
				if(!alltrim(codigo_seguridad.value)) {alert('Debe digitar el c�digo de seguridad de la tarjeta de cr�dito'); codigo_seguridad.style.backgroundColor='ffff55';codigo_seguridad.focus(); return;}
				if(!Number(codigo_seguridad.value)) {alert('Debe digitar el c�digo de seguridad de la tarjeta de cr�dito'); codigo_seguridad.style.backgroundColor='ffff55';codigo_seguridad.focus(); return;}
				if(!alltrim(devol_cuenta_banco.value)) {alert('Debe digitar la cuenta bancaria para devoluciones');devol_cuenta_banco.style.backgroundColor='ffff55';devol_cuenta_banco.focus();return false;}
				if(!Number(devol_cuenta_banco.value)) {alert('Numero de cuenta bancaria para devoluciones de garant�a inv�lido'); devol_cuenta_banco.style.backgroundColor='ffff55';devol_cuenta_banco.focus();return false;}
				if(devol_cuenta_banco.value.length<9) {alert('Numero de cuenta bancaria para devoluciones de garant�a inv�lido'); devol_cuenta_banco.style.backgroundColor='ffff55';devol_cuenta_banco.focus();return false;}
				if(devol_cuenta_banco.value.indexOf('0000000')>-1) {alert('Numero de cuenta bancaria para devoluciones de garant�a inv�lido'); devol_cuenta_banco.style.backgroundColor='ffff55';devol_cuenta_banco.focus();return false;}
				if(!alltrim(devol_tipo_cuenta.value)) {alert('Debe seleccionar el tipo de cuenta bancaria para devoluciones');devol_tipo_cuenta.style.backgroundColor='ffff55';devol_tipo_cuenta.focus();return false;}
				if(!alltrim(devol_banco.value)) {alert('Debe seleccionar el banco para devoluciones');devol_banco.style.backgroundColor='ffff55';devol_banco.focus();return false;}
				if(!alltrim(devol_ncuenta.value)) {alert('Debe digitar a nombre de quien es la cuenta para devoluciones');devol_ncuenta.style.backgroundColor='ffff55';devol_ncuenta.focus();return false;}
				if(!alltrim(identificacion_devol.value)) {alert('Debe digitar  la identificaci�n a la que pertenece la cuenta para devoluciones');identificacion_devol.style.backgroundColor='ffff55';identificacion_devol.focus();return false;}
				if(!Number(identificacion_devol.value)) {alert('Debe digitar  la identificaci�n a la que pertenece la cuenta para devoluciones');identificacion_devol.style.backgroundColor='ffff55';identificacion_devol.focus();return false;}
				franquicia.disabled=false;
				if(confirm('Confirma el env�o de esta informaci�n?'))
				{Enviar.style.visibility='hidden'; Acc.value='registrar_solicitud';document.forma.Enviar.style.visibility='hidden';document.forma.submit();}
			}
		}
		function valida_vencimiento()
		{
			var Vm=document.forma.vencimiento_mes.value;
			var Va=document.forma.vencimiento_ano.value;
			if(Vm && Va)
			{
				var V=Va+Vm;
				if(V<'$Validacion_vencimiento')
				{alert('No se puede aceptar una tarjeta vencida. Verifique la informaci�n o intente con otra tarjeta.');document.forma.Enviar.disabled=true;return false;}
				else	document.forma.Enviar.disabled=false;
			}
		}
		function busqueda_ciudad2(Campo,Contenido)
		{
			var Ventana_ciudad=document.getElementById('Busqueda_Ciudad');
			Ventana_ciudad.style.visibility='visible';Ventana_ciudad.style.left=mouseX;Ventana_ciudad.style.top=mouseY-10;Ventana_ciudad.src='inc/ciudades.html';
			Ciudad_campo=Campo;Ciudad_forma='forma';
		}
		function oculta_busca_ciudad()
		{document.getElementById('Busqueda_Ciudad').style.visibility='hidden';}
		function validar_identificacion(id)
		{
			if(!id) {alert('Debe digitar una identifcaci�n valida');   document.forma.Enviar.style.visibility='hidden';document.forma.identificacion.focus();return false; }
			else {document.forma.Enviar.style.visibility='visible';}
			window.open('zautorizaciones.php?Acc=valida_identificacion&id='+id,'Oculto_autorizacion');
		}
		function finalizar_envio()
		{
			window.open('w_servicio.php','_self');
		}
	</script>
	<body style='font-size:16px'>
		<iframe id='Busqueda_Ciudad' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#eeffee;z-index:200;' height='400px' width='200px' ></iframe>
		<h3 align='center'> .:. AOA COLOMBIA S.A. .:. SERVICIO DE VEH�CULO DE REEMPLAZO .:. FORMULARIO DE GARANT�A</h3>
		<center>
		<br>Se�or(a) Asegurado(a), La informaci�n que se solicita a continuaci�n es de la persona que va a constituir la Garant�a para acceder al Servicio de Veh�culo de Reemplazo que le ofrece
		Administraci�n Operativa Automotriz S.A. Por favor diligencie el siguiente formulario:
		<br><br>
		<form action='w_servicio.php' target='Oculto_forma' method='POST' name='forma' id='forma'>
			<table>
				<tr><td align='right'>N�mero de Identificaci�n</td><td><input type='text' class='numero' name='identificacion' id='identificacion' value='' size='15' maxlength='15'></td></tr>
				<tr><td align='right'>Tipo de Identificaci�n</td><td>".menu1('tipo_id',"select codigo,nombre from tipo_identificacion",'',1)."</td></tr>
				<tr><td align='right'>Lugar de Expedici�n de la Identificaci�n</td><td><input type='text' name='lugar_expdoc' id='lugar_expdoc' value='' size='50' maxlength='50' onkeyup='javascript:this.value=this.value.toUpperCase();'></td></tr>
				<tr><td align='right'>Nombres</td><td><input type='text' name='nombre' id='nombre' value='' size='50' maxlength='50' onkeyup='javascript:this.value=this.value.toUpperCase();'></td></tr>
				<tr><td align='right'>Apellidos</td><td><input type='text' name='apellido' id='apellido' value='' size='50' maxlength='50' onkeyup='javascript:this.value=this.value.toUpperCase();'></td></tr>
				<tr><td align='right'>Sexo:</td><td ><select name='sexo' id='sexo'><option value=''></option><option value='M'>Masculino</option><option value='F'>Femenino</option></select></td></tr>
				<tr><td align='right'>Ciudad:</td><td ><input type='text' style='color:#000099;background-color:#FFFFFF;' name='_ciudad' id='_ciudad' size='30' onclick=\"busqueda_ciudad2('ciudad','05001000');\" value='Click aqui' readonly><input type='hidden' name=ciudad id=ciudad value=''><span id='bc_ciudad'></span> Utilice el mouse para seleccionar la ciudad, haga click la casilla.</td></tr>
				<tr><td align='right'>Direcci�n Domicilio:</td><td ><input type='text' name='direccion' id='direccion' size='30' maxlength='50' onkeyup='javascript:this.value=this.value.toUpperCase();'></td></tr>
				<tr><td align='right'>Tel�fono Oficina:</td><td ><input type='text' class='numero' name='telefono_oficina' id='telefono_oficina' size='30' maxlength='50'></td></tr>
				<tr><td align='right'>Tel�fono Vivienda:</td><td ><input type='text' class='numero' name='telefono_casa' id='telefono_casa' size='30' maxlength='50'></td></tr>
				<tr><td align='right'>Celular:</td><td ><input type='text' name='celular' class='numero' id='celular' size='30' maxlength='50'></td></tr>
				<tr><td align='right'>Franquicia</td><td>".menu1("franquicia","select f.id,f.nombre from franquisia_tarjeta f where f.id in (1,2,3,4)",'',1)."</td></tr>
				<tr><td align='right'>Numero de Tarjeta</td><td><input type='text' name='numero_tarjeta' class='numero' id='numero_tarjeta' value='' size='20' maxlength='20'></td></tr>
				<tr><td align='right'>Banco que expidio la tarjeta</td><td>".menu1("banco","select id,nombre from codigo_ach order by nombre",0,1)."</td></tr>
				<tr><td align='right'>Fecha de vencimiento de la tarjeta</td><td>".menu3("vencimiento_mes","01,01;02,02;03,03;04,04;05,05;06,06;07,07;08,08;09,09;10,10;11,11;12,12",0,1,''," onchange='valida_vencimiento()' ");
	echo " - <select name='vencimiento_ano' id='vencimiento_ano' onchange='valida_vencimiento()'><option value=''></option>";
	for($a=date('Y');$a<2100;$a++) { echo "<option value='$a'>$a</option>"; }
	echo "</select></td></tr>
				<tr><td align='right'>C�digo de Seguridad<br>(Tres �ltimos digitos al respaldo de la tarjeta)</td><td><input type='password' name='codigo_seguridad' id='codigo_seguridad' value='' size='4' maxlength='4'></td></tr>
			</table><br><br>
			En caso de hacer cobros con la garant�a y si hay que devolver un dinero se hace mediante transferencia electr�nica. A continuaci�n por favor diligencie los datos
			del cuenta habiente para estos casos.<br><br>
			<table>
				<tr><td align='right'>N�mero de Cuenta Bancaria</td><td><input type='text' class='numero' name='devol_cuenta_banco' id='devol_cuenta_banco' value='' size='20' maxlength='20'></td></tr>
				<tr><td align='right'>Tipo de Cuenta</td><td><select name='devol_tipo_cuenta' id='tipo'><option value=''></option><option value='A'>Ahoros</option><option value='C'>Corriente</option></select></td></tr>
				<tr><td align='right'>Banco</td><td>".menu1("devol_banco","select id,nombre from codigo_ach where codigo!='' order by nombre",0,1)."</td></tr>
				<tr><td align='right'>A nombre de (Nombres y apellidos)</td><td><input type='text' name='devol_ncuenta' id='devol_ncuenta' value='' size='50' maxlength='50' onkeyup='javascript:this.value=this.value.toUpperCase();'></td></tr>
				<tr><td align='right'>Documento de Identificaci�n</td><td><input type='text' class='numero' name='identificacion_devol' id='identificacion_devol' value='' size='20' maxlength=15></td></tr>
			</table>
			<center><input type='button' id='Enviar' name='Enviar' value=' ENVIAR LA INFORMACION ' onclick='valida_envio()' style='font-size:16;width:300' ></center>
		<input type='hidden' name='id' id='id' value='$id'>
		<input type='hidden' name='Acc' id='Acc' value=''>
		</form>
		<iframe name='Oculto_forma' id='Oculto_forma' style='visibility:hidden' width='1' height='1'></iframe>
		";
}

function registrar_solicitud()
{
	global $id,$nombre,$identificacion,$numero_tarjeta,$franquicia,$banco,$vencimiento_mes,$vencimiento_ano,$codigo_seguridad;
	global $devol_ncuenta,$devol_cuenta_banco,$devol_tipo_cuenta,$devol_banco,$apellido,$lugar_expdoc,$tipo_id,$sexo,$ciudad,$direccion,
		$identificacion_devol,$telefono_oficina,$telefono_casa,$celular;
	// obtencion de la informacion de los registros, $id es el id de la cola 2 donde se guarda el siniestro
	$Cola2=qo("select * from call2cola2 where id='$id'");
	$Sin=qo("select * from siniestro where id=$Cola2->siniestro");
	$Aseguradora=qo("select * from aseguradora where id=$Sin->aseguradora");
	// ingresa el cliente en la tabla de clientes teniendo cuidado de su preexistencia
	q("insert ignore into cliente (identificacion) values ('$identificacion')");
	// actualiza toda la informacion del cliente
	q("update cliente set nombre='$nombre',apellido='$apellido',lugar_expdoc='$lugar_expdoc',tipo_id='$tipo_id',sexo='$sexo',pais='CO',ciudad='$ciudad',
			direccion='$direccion',telefono_oficina='$telefono_oficina',telefono_casa='$telefono_casa',celular='$celular',email_e='$Sin->declarante_email' where identificacion='$identificacion'");
	// inserta la solicitud de autorizacion
	$Ahora=date('Y-m-d H:i:s');
	$Nid=q("insert into sin_autor (siniestro,nombre,identificacion,numero,franquicia,banco,vencimiento_mes,vencimiento_ano,codigo_seguridad,
		fecha_solicitud,solicitado_por,estado,valor,observaciones,email,devol_cuenta_banco,devol_tipo_cuenta,devol_banco,
		devol_ncuenta,identificacion_devol)
		values ('$Sin->id','$nombre $apellido','$identificacion','$numero_tarjeta','$franquicia','$banco','$vencimiento_mes','$vencimiento_ano','$codigo_seguridad',
		'$Ahora','$Sin->declarante_nombre','E','$Aseguradora->garantia',\"SOLICITUD DILIGENCIADA POR EL DECLARANTE DIRECTAMENTE.\",'$Sin->declarante_email','$devol_cuenta_banco','$devol_tipo_cuenta','$devol_banco',
		'$devol_ncuenta','$identificacion_devol')");
	q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
				values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."','".date('s')."','DECLARANTE','$Sin->declarante_nombre','sin_autor','A','$Nid','".$_SERVER['REMOTE_ADDR']."','Solicita Autorizaci�n')");
	echo "<body><script language='javascript'>alert('LA INFORMACION SE REGISTRO SATISFACTORIAMENTE');parent.finalizar_envio();</script></body>";
}

function bajar_archivo_contrato() // Bajar un archivo
{
	global $Archivo, $Salida,$id;
	$Ahora=date('Y-m-d H:i:s');
	q("update call2cola2 set descargado='$Ahora' where id=$id");
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"$Salida\"");
	header("Content-Description: File Transfert");
	@readfile('../../servicio/pdf/'.$Archivo);
}



?>
