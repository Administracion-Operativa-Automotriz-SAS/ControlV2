<?php

/**
 *  Sistema de solicitud de Autorizaciones.
 *
 * @version $Id$
 * @copyright 2010
 */
include('inc/funciones_.php');
sesion();
include('zfunciones_autorizaciones.php');

$USUARIO=$_SESSION['User'];
$NUSUARIO=$_SESSION['Nombre'];
$IDUSUARIO=$_SESSION['Id_alterno'];
if(!empty($Acc) && function_exists($Acc)) { eval($Acc.'();');die();}

inicio_autorizacion();

function inicio_autorizacion()  // Pinta el formulario de solicitud de información. Solicita el número del siniestro y la placa del vehículo para validar si el funcionario está en el siniestro correcto.
{
	global $sini,$DesdeCall;
	html('SISTEMA DE SOLICITUD DE AUTORIZACION');
	echo "<script language='javascript'>
		function carga() { centrar(800,800); }
		function buscar()
		{
			if(alltrim(document.forma.sini.value) && alltrim(document.forma.placaaseg.value))
			{ document.forma.submit(); }
			else
			{ alert('Debe escribir algun valor en el número de siniestro para buscarlo y la placa del vehículo del Asegurado');
				document.forma.sini.style.backgroundColor='ffffdd';
				document.forma.sini.focus(); }}
		function activa_autoriza(id)
		{if(id) window.open('zautorizaciones.php?Acc=presenta_siniestro&id='+id,'Autorizacion');muestra('Autorizacion');}
		function oculta_autoriza(){oculta('Autorizacion');}
		function pide_siniestro(){activa_autoriza();window.open('zautorizaciones.php?Acc=pide_siniestro&sini='+document.forma.sini.value,'Autorizacion');}
		function volver_call(){opener.tachar_adjudicado();window.close();void(null);}
		function cerrar_ventana(){window.close();void(null);}
		function recargar(){buscar();}
	</script>
	<body onload='carga()' bgcolor='ffffff' style='color:000000;'><h3>Sistema de Solicitud de Autorizacion de Cupo  <img src='img/solicita_autorizacion.png' border='0' align='middle' height='50px'></h3>
	<form action='zautorizaciones.php' method='post' target='Oculto_autorizaciones' name='forma' id='forma'>
		Número de Siniestro: <input type='text' name='sini' value='$sini' onfocus='this.select()'>
		Placa del vehículo del Asegurado: <input type='text' name='placaaseg' id='placaaseg' value='' size='10' maxlength='10' style='font-size:14px' onkeyup='javascript:this.value=this.value.toUpperCase();'><br><br>
		<input type='button' value=' CONTINUAR ' style='font-weight:bold;font-size:16px' onclick='buscar()'>
		<input type='hidden' name='Acc' value='buscar_siniestro'>";
	if($DesdeCall)  // si el formulario es activado desde call center, le da la opcion al funcionario de volver al módulo de call center.
	{echo "<input type='button' value='Volver a CallCenter' onclick='volver_call();'>";}
	echo "</form>
		<iframe name='Autorizacion' id='Autorizacion' style='visibility:hidden' height=550 width='100%' frameborder='no'></iframe>
		<iframe name='Oculto_autorizaciones' style='visibility:hidden' height=1 width='100%'></iframe>
		</body>";
}

function buscar_siniestro() // Después de pedir el siniestro y la placa busca si hay una o varias coincidencias. si hay varias solicita al funcionario que escoja una
{
	global $sini,$placaaseg;
	
	if($Siniestros=q("select id from siniestro where numero like '%$sini%' and estado in (5,3,7,8) and placa='$placaaseg' "))
	{
		if(mysql_num_rows($Siniestros)==1)
		{
			$D=mysql_fetch_object($Siniestros);
			echo "<body><script language='javascript'>parent.activa_autoriza($D->id);</script></body>";
		}
		else
		{
			echo "<body><script language='javascript'>parent.pide_siniestro();</script></body>";
		}
	}
	else
		echo "<body><script language='javascript'>parent.oculta_autoriza();alert('No hay siniestros que cumplan con la información suministrada');</script></body>";
}

function presenta_siniestro() // muestra toda la información del siniestro y pide los datos del asegurado
{
	global $id,$USUARIO;
	$Fran=q("select * from franquisia_tarjeta where codigo!='' ");
	$Validacion_vencimiento=date('Ym');
	$D=qo("select *,t_ciudad(ciudad) as nciudad from siniestro where id=$id");
	
	//echo "select *,t_ciudad(ciudad) as nciudad from siniestro where id=$id";
	//print_r($D);
	
	$Ahora=date('Y-m-d');
	//////  VALIDACION SI LA CITA DEL SINIESTRO YA TIENE MARCADO EL ARRIBO o si está en estado pendiente.
	$Hoy=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-7)));
	if($Cita=qo("select * from cita_servicio where siniestro=$id and (fecha>='$Hoy' and estado='P') ")) {}
	elseif($Cita=qo("select * from cita_servicio where siniestro=$id and (fecha<='$Ahora' and estado='C') ")){}
	else {echo "<body><script language='javascript'>alert('Este siniestro no tiene cita de servicio para hoy $Hoy en estado Pendiente');window.close();void(null);</script></body>";die();}

	$Oficina=qo("Select * from oficina where ciudad='$D->ciudad'"); // obtiene la información de la oficina
	
	//echo "Select * from oficina where ciudad='$D->ciudad'";
	
	$Aseguradora=qo("select * from aseguradora where id=$D->aseguradora"); // obtiene la información de la aseguradora
	$Autorizacion=q("select a.*,f.tipo from sin_autor a, franquisia_tarjeta f where a.siniestro=$id and a.franquicia=f.id order by a.fecha_solicitud"); // obtiene la información de autorizaciones anteriores
	html();
	echo "<script language='javascript'>	var Validado=false;";
	if($Fran) // rutinas de validación en la captura de las tarjetas crédito
	{
		echo "function vfran(valor)
				{
					var Franquicia=document.forma.franquicia.value;
					";
		while($F=mysql_fetch_object($Fran))
		{
			$Longitud=strlen($F->codigo);
			echo "if(Franquicia==$F->id)
			{
				if(valor.substr(0,$Longitud)!='$F->codigo')
				{
					alert('Error en el número de la tarjeta, no corresponde de acuerdo a la franquicia');
					document.forma.n1.style.backgroundColor='ffffdd';
					document.forma.n1.value='';
					document.forma.n1.focus();
					return false;
				}
				document.forma.franquicia.disabled=true;
				document.forma.n1.disabled=true;
			}";
		}
		echo "}
				";
	}
	echo "
		function activa_n()
		{
			with(document.forma)
			{
				if(franquicia.value==6) // Garantia en Efectivo
				{
					n1.style.visibility='hidden';n2.style.visibility='hidden';n3.style.visibility='hidden';n4.style.visibility='hidden';
					banco.style.visibility='hidden';vencimiento_mes.style.visibility='hidden';vencimiento_ano.style.visibility='hidden';
					codigo_seguridad.style.visibility='hidden';voucher.style.visibility='hidden';valor.focus();return true;
				}
				if(franquicia.value==9 || franquicia.value==11) // Garantia en Pagare   |  Garantia No Reembolsable
				{
					n1.style.visibility='hidden';n2.style.visibility='hidden';n3.style.visibility='hidden';n4.style.visibility='hidden';
					banco.style.visibility='hidden';vencimiento_mes.style.visibility='hidden';vencimiento_ano.style.visibility='hidden';
					codigo_seguridad.style.visibility='hidden';voucher.style.visibility='hidden';valor.value='0';valor.focus();return true;
				}
				if(franquicia.value==10) // sin garantía
				{
					n1.style.visibility='hidden';n2.style.visibility='hidden';n3.style.visibility='hidden';n4.style.visibility='hidden';
					banco.style.visibility='hidden';vencimiento_mes.style.visibility='hidden';vencimiento_ano.style.visibility='hidden';
					codigo_seguridad.style.visibility='hidden';voucher.style.visibility='hidden';valor.value='0';valor.focus();return true;
				}
				n1.disabled=false;n2.disabled=false;n3.disabled=false;n4.disabled=false;n1.focus();
			}
			return true;
		}

		function valida_envio() // validación de todos los campos del formulario
		{
			with(document.forma)
			{
				if(!alltrim(nombre.value)) {alert('Debe escribir el nombre del tarjetahabiente o autorizado'); nombre.style.backgroundColor='ffff55';nombre.focus(); return false;}
				if(!alltrim(apellido.value)) {alert('Debe escribir el apellido del tarjetahabiente o autorizado'); apellido.style.backgroundColor='ffff55';apellido.focus(); return false;}
				if(!Number(identificacion.value)) {alert('Debe escribir correctamente el número de identificación, sin comas ni puntos');identificacion.style.backgroundColor='ffff55';identificacion.focus();return false;}
				if(!tipo_id.value) {alert('Debe seleccionar el tipo de identificación'); tipo_id.style.backgroundColor='ffff55';tipo_id.focus();return false;}
				if(!sexo.value) {alert('Debe seleccionar el sexo'); sexo.style.backgroundColor='ffff55';sexo.focus();return false;}
				if(!ciudad.value) { alert('Debe seleccionar una ciudad.'); ciudad.style.backgroundColor='ffff44'; ciudad.focus(); return false; }
				if(!alltrim(direccion.value)) { alert('Debe digitar la dirección de residencia.'); direccion.style.backgroundColor='ffff44'; direccion.focus(); return false; }
				if(!alltrim(telefono_oficina.value)) { alert('Debe digitar el telefono de oficina.'); telefono_oficina.style.backgroundColor='ffff44'; telefono_oficina.focus(); return false; }
				if(!alltrim(telefono_casa.value)) { alert('Debe digitar el telefono de la vivienda.'); telefono_casa.style.backgroundColor='ffff44'; telefono_casa.focus(); return false; }
				if(!alltrim(email_e.value)) { alert('Debe digitar el correo electronico.'); email_e.style.backgroundColor='ffff44'; email_e.focus(); return false; }
				if(!alltrim(franquicia.value)) {alert('Debe seleccionar la franquicia de la tarjeta de crédito'); franquicia.style.backgroundColor='ffff55';franquicia.focus(); return false;}
				if(franquicia.value!=6 && franquicia.value!=9 && franquicia.value!=10 && franquicia.value!=11)
				{
					if(!alltrim(n1.value)) {alert('Debe escribir el numero completo de la tarjeta de crédito'); n1.style.backgroundColor='ffff55';n1.focus(); return;}
					if(!alltrim(n2.value)) {alert('Debe escribir el numero completo de la tarjeta de crédito'); n2.style.backgroundColor='ffff55';n2.focus(); return;}
					if(!alltrim(n3.value)) {alert('Debe escribir el numero completo de la tarjeta de crédito'); n3.style.backgroundColor='ffff55';n3.focus(); return;}
					if(!alltrim(n4.value)) {alert('Debe escribir el numero completo de la tarjeta de crédito'); n4.style.backgroundColor='ffff55';n4.focus(); return;}
					if(!banco.value) {alert('Debe seleccionar el banco que expidió la tarjeta de crédito'); banco.style.backgroundColor='ffff55';banco.focus(); return;}
					if(!alltrim(vencimiento_mes.value)) {alert('Debe seleccionar el mes de vencimiento de la tarjeta de crédito'); vencimiento_mes.style.backgroundColor='ffff55';vencimiento_mes.focus(); return;}
					if(!alltrim(vencimiento_ano.value)) {alert('Debe seleccionar el año de vencimiento de la tarjeta de crédito');vencimiento_ano.style.backgroundColor='ffff55';vencimiento_ano.focus(); return;}
					if(!alltrim(codigo_seguridad.value)) {alert('Debe digitar el código de seguridad de la tarjeta de crédito'); codigo_seguridad.style.backgroundColor='ffff55';codigo_seguridad.focus(); return;}
					if(!alltrim(voucher.value)) {alert('Debe digitar el número del voucher'); voucher.style.backgroundColor='ffff55';voucher.focus(); return;}
				}
				else
				{if(!alltrim(observaciones.value)) {alert('Debe digitar en el campo de observaciones la razón por la que se solicita una garantía en efectivo');observaciones.style.backgroundColor='ffffdd';observaciones.focus();return false;}}
				if(franquicia.value==1 || franquicia.value==2 || franquicia.value==3 || franquicia.value==4)
				{
					if(!alltrim(devol_cuenta_banco.value)) {alert('Debe digitar la cuenta bancaria para devoluciones');devol_cuenta_banco.style.backgroundColor='ffff55';devol_cuenta_banco.focus();return false;}
					if(!Number(devol_cuenta_banco.value)) {alert('Numero de cuenta bancaria para devoluciones de garantía inválido'); devol_cuenta_banco.style.backgroundColor='ffff55';devol_cuenta_banco.focus();return false;}
					if(devol_cuenta_banco.value.length<9) {alert('Numero de cuenta bancaria para devoluciones de garantía inválido'); devol_cuenta_banco.style.backgroundColor='ffff55';devol_cuenta_banco.focus();return false;}
					if(devol_cuenta_banco.value.indexOf('0000000')>-1) {alert('Numero de cuenta bancaria para devoluciones de garantía inválido'); devol_cuenta_banco.style.backgroundColor='ffff55';devol_cuenta_banco.focus();return false;}
					if(!alltrim(devol_tipo_cuenta.value)) {alert('Debe seleccionar el tipo de cuenta bancaria para devoluciones');devol_tipo_cuenta.style.backgroundColor='ffff55';devol_tipo_cuenta.focus();return false;}
					if(!alltrim(devol_banco.value)) {alert('Debe seleccionar el banco para devoluciones');devol_banco.style.backgroundColor='ffff55';devol_banco.focus();return false;}
					if(!alltrim(devol_ncuenta.value)) {alert('Debe digitar a nombre de quien es la cuenta para devoluciones');devol_ncuenta.style.backgroundColor='ffff55';devol_ncuenta.focus();return false;}
					if(!alltrim(identificacion_devol.value)) {alert('Debe digitar  la identificación a la que pertenece la cuenta para devoluciones');identificacion_devol.style.backgroundColor='ffff55';identificacion_devol.focus();return false;}
				}
				franquicia.disabled=false;
				if(!Number(valor.value) && Number(valor.value)!=0) {alert('Debe escribir correctamente el valor de la garantía o consumo, sin comas ni puntos');valor.style.backgroundColor='ffff55';valor.focus();return;}
				if(confirm('Desea enviar la solicitud de Autorización?'))
				{n1.disabled=false;Acc.value='registrar_solicitud';document.forma.Enviar.style.visibility='hidden';document.forma.submit();}
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
				{alert('No se puede aceptar una tarjeta vencida. Verifique la información o solicite otra tarjeta.');document.forma.Enviar.disabled=true;return false;}
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
			if(!id) {alert('Debe digitar una identifcación valida');   document.forma.Enviar.style.visibility='hidden';document.forma.identificacion.focus();return false; }
			else {document.forma.Enviar.style.visibility='visible';}
			window.open('zautorizaciones.php?Acc=valida_identificacion&id='+id,'Oculto_autorizacion');
		}
		function reasignar(auanterior,idsiniestro)
		{if(confirm('Está seguro de querer REASIGNAR esta Autorización al nuevo servicio?')) window.open('zautorizaciones.php?Acc=reasignar&auanterior='+auanterior+'&idsiniestro='+idsiniestro,'Oculto_autorizacion');	}
		function recargar(){parent.recargar();}
	</script>
	<body style='color:00000;font-size:12' bgcolor='eeeeee'>
	<b>$D->numero $D->asegurado_nombre $D->nciudad</b>";
	if($Autorizacion) // presenta las autorizaciones anteriores o en espera si ya están autorizadas o no.
	{
		echo "<table width='100%' bgcolor='dddddd'><tr>
						<th style='font-size:14'>Fec. Solicitud</th>
						<th style='font-size:14'>Voucher</th>
						<th style='font-size:14'>Valor</th>
						<th style='font-size:14'>Estado</th>
						<th style='font-size:14'>Autorización</th></tr>";
		while($A=mysql_fetch_object($Autorizacion))
		{
			switch($A->estado)
			{
				case 'E': $Estado='EN ESPERA';$Aut='';break;
				case 'A': $Estado='AUTORIZADO';$Aut='******'.r($A->num_autorizacion,3);break;
				case 'R': $Estado='RECHAZADO';$Aut=$A->observaciones;break;
			}
			echo "<tr><td align='center' bgcolor='ffffff'>$A->fecha_solicitud</td>
						<td align='center' bgcolor='ffffff'>$A->numero_voucher</td>
						<td align='center' bgcolor='ffffff'>".coma_format($A->valor)."</td>
						<td align='center' bgcolor='ffffff'>$Estado</td>
						<td align='center' bgcolor='ffffff'>$Aut ";
			if(inlist($USUARIO,'1,5') && $A->tipo=='C' && $A->estado=='E') // si el usuario es de autorizaciones y el tipo de garantia es Tarjeta Crédito, puede procesar la autorización
				echo "<a style='cursor:pointer' onclick=\"parent.centrar(1000,800);window.open('zautorizaciones.php?Acc=estado','_self');\" class='info'>
							<img src='img/estado_autorizaciones.png' border='0' height='30'><span>Procesar Autorización</span></a>";
			if(inlist($USUARIO,'1,5,10') && $A->tipo=='P' && $A->estado=='E') // si el usuario es de autorizaciones y el tipo de garantía des Pagaré puede procesar la autorización
				echo "<a style='cursor:pointer' onclick=\"window.open('zautorizaciones.php?Acc=asignar_pagare&id=$A->id','_self');\" class='info'>
						<img src='img/pagare.png' border='0' height='30'><span>Asignar Pagaré</span></a>";
			echo "</td></tr>";
		}
		echo "</table>";
	}
	// presenta todo el formulario para llenar la solicitud de una nueva autorización.
	echo "<hr>
	<iframe id='Busqueda_Ciudad' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#eeffee;z-index:200;' height='400px' width='200px' ></iframe>
	<form action='zautorizaciones.php' method='post' target='Oculto_autorizacion' name='forma' id='forma'>
		<h3>Datos del Tarjetahabiente o Autorizado <span id='nuevo_cliente'></span>".
		($D->no_garantia?" <a class='info'><img src='img/nogarantia.png' border='0' height='30px' alt='Servicio Sin Garantia' title='Servicio Sin Garantia' align='middle'> <b style='color:ff3333'>Servicio Sin Garantia</b><span><img src='img/nogarantia.png'><h3>Servicio Sin Garantía</h3></span></a> ":"")
		."</h3>
		<table cellspacing=1 cellpadding=0>
		<tr ><td align='right'>Número de identificación:</td><td ><input type='text' name='identificacion' class='numero' size='15' maxlength='15' onblur='validar_identificacion(this.value);'></td>
		<td align='right'>Lugar de expedición:</td><td ><input type='text' name='lugar_expdoc' onblur='this.value=this.value.toUpperCase();' ></td></tr>
		<tr><td align='right'>Nombres:</td><td ><input type='text' name='nombre' size='30' maxlength='50' onblur='this.value=this.value.toUpperCase();'></td>
		<td align='right'>Apellidos:</td><td ><input type='text' name='apellido' size='30' maxlength='50' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr ><td align='right'>Tipo de identificación:</td><td >".menu1('tipo_id',"select codigo,nombre from tipo_identificacion",'',1)."</td>
		<td align='right'>Sexo:</td><td ><select name='sexo' id='sexo'><option value=''></option><option value='M'>Masculino</option><option value='F'>Femenino</option></select></td></tr>
		<tr ><td align='right'>Pais:</td><td >".menu1('pais',"select codigo,nombre from pais order by nombre",'CO',1)."</td><td align='right'>Ciudad:</td>
		<td ><input type='text' style='color:#000099;background-color:#FFFFFF;' name='_ciudad' id='_ciudad' size='30' onclick=\"busqueda_ciudad2('ciudad','05001000');\" readonly>
		<input type='hidden' name=ciudad id=ciudad value=''><span id='bc_ciudad'></span></td></tr>
		<tr ><td align='right'>Dirección Domicilio:</td><td ><input type='text' name='direccion' id='direccion' size='30' maxlength='50'></td>
		<td align='right'>Teléfono Oficina:</td><td ><input type='text' name='telefono_oficina' id='telefono_oficina' size='30' maxlength='50'></td></tr>
		<tr ><td align='right'>Teléfono Vivienda:</td><td ><input type='text' name='telefono_casa' id='telefono_casa' size='30' maxlength='50'></td>
		<td align='right'>Celular:</td><td ><input type='text' name='celular' id='celular' size='30' maxlength='50'></td></tr>
		<tr ><td align='right'>Email 1:</td><td colspan=2><input type='text' name='email_e' id='email_e' size='70' maxlength='70'></td></tr>
		</table>
		 <hr>Franquicia de la tarjeta: ";
	// pinta las opciones de franquicias autorizadas de acuerdo al perfil del usuario, la oficina en la que está y  la aseguradora
	if($D->no_garantia)
	{
		//echo "no garantia";	
		echo menu1("franquicia","select f.id,f.nombre from franquisia_tarjeta f,ciudad_franq c where c.franquicia=f.id and c.oficina=$Oficina->id
																and concat(',',c.perfil,',') like '%,".$_SESSION['User'].",%' and concat(',',c.aseguradora,',') like '%,$D->aseguradora,%' ",10,1,''," onchange='activa_n();' ");
	}
	else
	{	
		//echo "si garantia";
		//echo "select f.id,f.nombre from franquisia_tarjeta f,ciudad_franq c where c.franquicia=f.id and c.oficina=$Oficina->id
			//													and concat(',',c.perfil,',') like '%,".$_SESSION['User'].",%' and concat(',',c.aseguradora,',') like '%,$D->aseguradora,%' and f.id!=10";
		
		echo menu1("franquicia","select f.id,f.nombre from franquisia_tarjeta f,ciudad_franq c where c.franquicia=f.id and c.oficina=$Oficina->id
																and concat(',',c.perfil,',') like '%,".$_SESSION['User'].",%' and concat(',',c.aseguradora,',') like '%,$D->aseguradora,%' and f.id!=10",0,1,''," onchange='activa_n();' ");
	}
	echo "<br />Número de la tarjeta: <input type='text' name='n1' size=4 maxlength=4 class='numero' onblur=\"vfran(this.value);\" disabled> -
		<input type='text' name='n2' size=4 maxlength=4 class='numero' disabled> -
		<input type='text' name='n3' size=4 maxlength=4 class='numero' disabled> -
		<input type='text' name='n4' size=4 maxlength=4 class='numero' disabled><br />
		Banco que expidió la tarjeta: ".menu1("banco","select id,nombre from codigo_ach order by nombre",0,1)."<br />
		Fecha de vencimiento de la tarjeta: ".menu3("vencimiento_mes","01,01;02,02;03,03;04,04;05,05;06,06;07,07;08,08;09,09;10,10;11,11;12,12",0,1,''," onchange='valida_vencimiento()' ");
	echo " - <select name='vencimiento_ano' id='vencimiento_ano' onchange='valida_vencimiento()'><option value=''></option>";
	for($a=date('Y');$a<2100;$a++) { echo "<option value='$a'>$a</option>"; }
	echo "</select><br />
		Código de seguridad: <input type='text' id='codigo_seguridad' name='codigo_seguridad' class='numero' size='4' maxlength='4'>
		Número de Voucher: <input type='text' id='voucher' name='voucher' class='numero' size='7' maxlength='7'>
		<span style='background-color:88aa88;'><input type='checkbox' name='congelamiento' id='congelamiento'
		onchange=\"if(this.checked) {document.forma.voucher.value='000000';document.forma.voucher.readonly=true;} else {document.forma.voucher.value='';document.forma.voucher.readonly=false;}\"> Solo congelamiento</span><br />
		Valor de la garantía o consumo: $<input type='text' name='valor' id='valor' value='$Aseguradora->garantia' size='10' maxlength='10' class='numero' style='font-weight:bold'><br />
		Observaciones: <textarea name='observaciones' cols=80 rows=2></textarea><br /><br>

		<h3>Datos Financieros para devolución de garantías en Efectivo o  Consignación Voucher</h3>
		<table border cellspacing='0'><tr><td>
		<table>
		<tr><td>Numero de cuenta Bancaria: </td><td><input type='text' name='devol_cuenta_banco' id='cuenta' size=20></td></tr>
		<tr><td>Tipo de cuenta: </td><td><select name='devol_tipo_cuenta' id='tipo'><option value=''></option><option value='A'>Ahoros</option><option value='C'>Corriente</option></select></td></tr>
		<tr><td>Banco: </td><td>".menu1("devol_banco","select id,nombre from codigo_ach where codigo!='' order by nombre",0,1)."</td></tr>
		<tr><td>A nombre de (Nombres y apellidos): </td><td><input type='text' name='devol_ncuenta' id='devol_ncuenta' value='' size='50' maxlength='50' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td>Identificación: </td><td><input type='text' name='identificacion_devol' id='identificacion_devol' value='' size='20' maxlength=15></td></tr>
		</table></td></tr></table>
		<br>
		<b>Clave de confirmación</b>: <input type='password' name='Clave' id='Clave' onKeyPress='bloqueo_mayusculas(event)'> Digite la clave del usuario ".$_SESSION['Nombre']."
				<div id='bloqueomayusculas' style='visibility:hidden'><b style='color:red'>El bloqueo de mayúsculas está activado</b></div><br><br>
		<center><input type='button' id='Enviar' name='Enviar' value='Enviar' onclick='valida_envio()' style='font-size:16;width:200' ></center>
		<input type='hidden' name='id' id='id' value='$id'>
		<input type='hidden' name='Acc' id='Acc' value=''>
		<a href='zautorizaciones.php?Acc=presenta_siniestro&id=$id' target='_self'>Refrescar</a>
		</form>
		<script language='javascript'>activa_n();</script>";
	///   BUSQUEDA DE AUTORIZACIONES ANTERIORES
	if(inlist($USUARIO,'1,2,5'))
	{
		// busca autorizaciones donde coincida la cédula del asegurado o la placa para dar posibilidad de reasignar una garantía anterior al caso actual
		$Consulta="select si.id,si.numero,si.asegurado_nombre,si.asegurado_id,si.placa,au.fecha_solicitud,t_franquisia_tarjeta(au.franquicia) as nf,au.id as auid from siniestro si,sin_autor au where  si.id=au.siniestro and au.estado='A' and ";
		if($D->asegurado_id) $Consulta.=" (si.asegurado_id=$D->asegurado_id or placa='$D->placa') and si.id!=$D->id ";
		else $Consulta.=" si.placa='$D->placa' and si.id!=$D->id ";
		if($Anterior=q($Consulta))
		{
			echo "<hr>Autorizaciones anteriores: <br /><table ><tr >
						<th >Siniestro</th>
						<th >Asegurado</th>
						<th >Id Asegurado</th>
						<th >Placa</th>
						<th >Franquicia</th>
						<th >Reasignar</th>
						</tr>";
			while($Ant=mysql_fetch_object($Anterior)) // registro por registro pinta un link para la reasignación de la garantía anterior al registro actual
			{
				echo "<tr ><td >$Ant->numero</td><td >$Ant->asegurado_nombre</td>
						<td align='right'>".coma_format($Ant->asegurado_id)."</td><td >$Ant->placa</td><td >$Ant->nf</td>
						<td ><a onclick='reasignar($Ant->auid,$D->id);' style='cursor:pointer'><img src='gifs/standar/seguir.png' border='0'> Reasignar a este servicio</a></td></tr>";
			}
			echo "</table>";
		}
	}
	//////////////////////   fin busqueda de autorizaciones anteriores
	echo "<iframe name='Oculto_autorizacion' id='Oculto_autorizacion' style='visibility:hidden' height=1 width=1></iframe>
	</body>";
}

function registrar_solicitud() // graba todos los datos de una nueva solicitud en la tabla de autorizaciones
{
	global $id,$nombre,$identificacion,$n1,$n2,$n3,$n4,$franquicia,$banco,$vencimiento_mes,$vencimiento_ano,$codigo_seguridad,$voucher,$NUSUARIO,$valor,$observaciones;
	global $devol_ncuenta,$devol_cuenta_banco,$devol_tipo_cuenta,$devol_banco,$apellido,$lugar_expdoc,$tipo_id,$sexo,$pais,$ciudad,$direccion,
		$identificacion_devol,$telefono_oficina,$telefono_casa,$celular,$email_e,$Clave,$congelamiento;
	$congelamiento=sino($congelamiento);
	if(verificar_password($_SESSION['Nick'],$Clave)) // Verifica el password del usuario actual para evitar que un usuario no autorizado grabe la información y por trazabilidad de proceso
	{
		$nuevoc=q("insert ignore into cliente (identificacion) values ('$identificacion')"); // inserta en la tabla de clientes el nuevo cliente
		graba_bitacora('cliente','A',$nuevoc,'Adiciona Registro');
		q("update cliente set nombre='$nombre',apellido='$apellido',lugar_expdoc='$lugar_expdoc',tipo_id='$tipo_id',sexo='$sexo',pais='$pais',ciudad='$ciudad',
			direccion='$direccion',telefono_oficina='$telefono_oficina',telefono_casa='$telefono_casa',celular='$celular',email_e='$email_e' where identificacion='$identificacion'");
			// actualiza toda la información del cliente
		html();
		echo "<br /><br /><br /><h2 align='center'>Solicitud realizada por $NUSUARIO</h3>";
		$Hoy=date('Y-m-d H:i:s');
		// inserta en la tabla de autorizaciones los datos de la solicitud
		$Nid=q("insert into sin_autor (siniestro,nombre,identificacion,numero,franquicia,banco,vencimiento_mes,vencimiento_ano,codigo_seguridad,
		fecha_solicitud,solicitado_por,numero_voucher,estado,valor,observaciones,email,devol_cuenta_banco,devol_tipo_cuenta,devol_banco,
		devol_ncuenta,identificacion_devol,congelamiento)
		values ('$id','$nombre $apellido','$identificacion','$n1$n2$n3$n4','$franquicia','$banco','$vencimiento_mes','$vencimiento_ano','$codigo_seguridad',
		'$Hoy','$NUSUARIO','$voucher','E','$valor',\"$observaciones\",'$email_e','$devol_cuenta_banco','$devol_tipo_cuenta','$devol_banco',
		'$devol_ncuenta','$identificacion_devol','$congelamiento')");
		graba_bitacora('sin_autor','A',$Nid,'Adiciona Registro - Solicita Autorizacion');
		echo "<br /><br /><center><a href='zautorizaciones.php?Acc=presenta_siniestro&id=$id' target='_self'>Click aqui para verificar el estado de este siniestro</a>";
		sleep(1);
		// termina de guardar y recarga la pantalla para visualizar el estado actual de cada solicitud hecha
		echo "<body><script language='javascript'>parent.recargar();</script></body>";
	}
	else
	{echo "<body><script language='javascript'> alert('Clave erronea');parent.document.forma.Enviar.style.visibility='visible';</script></body>";}
}

function envio_solicitud_efectivo($id=0,$Nid=0)  // función antigua que envia un correo de solicitud de autorización a un directivo de AOA para permitir la recepción de garantías en efectivo esta rutina ya esta  fuera de uso
{
	global $idauto;
	$Nusuario=$_SESSION['Nombre'];
	$Hoy=date('Y-m-d H:i:s');
	if($idauto)
	{
		$Nid=$idauto;
		$id=qo1("select siniestro from sin_autor where id=$idauto");
	}
	$Sin=qo("select id,numero,asegurado_nombre,ciudad,ciudad_original,aseguradora,fec_autorizacion from siniestro where id=$id");
	$Auto=qo("select * from sin_autor where id=$Nid");
	$Ciudad=qo("select * from ciudad where codigo='$Sin->ciudad' ");
	if($Sin->ciudad_original) $Ciudado=qo("select * from ciudad where codigo='$Sin->ciudad_original' ");
	else $Ciudado=$Ciudad;
	$Aseguradora=qo1("select nombre from aseguradora where id=$Sin->aseguradora");
	//-  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -
	$Ruta_gabriel="utilidades/Operativo/operativo.php?Acc=autorizar_garantia_efectivo&idg=$Nid&Fecha=$Hoy&Usuario=GABRIEL SANDOVAL PAVAJEAU";
	$Ruta_henry="utilidades/Operativo/operativo.php?Acc=autorizar_garantia_efectivo&idg=$Nid&Fecha=$Hoy&Usuario=JOHN HENRY GONZALEZ ENCISO";
	$Ruta_arturo="utilidades/Operativo/operativo.php?Acc=autorizar_garantia_efectivo&idg=$Nid&Fecha=$Hoy&Usuario=ARTURO QUINTERO RODRIGUEZ";
	$Ruta_claudia="utilidades/Operativo/operativo.php?Acc=autorizar_garantia_efectivo&idg=$Nid&Fecha=$Hoy&Usuario=CLAUDIA ALEXANDRA CASTRO G.";
	//-  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -
	$Email_usuario=usuario('email');
	
	echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
		  <script>
            $.ajax(
                    {
                        url: 'https://sac.aoacolombia.com/enviar.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							copia:'$Email_usuario',
						     para:'gabrielsandoval@aoacolombia.com',
							contenido:'7',
							asunto:'SOLICITUD AUTORIZACION GARANTIA EN EFECTIVO $Sin->numero',
							asegurado_nombre:'$Sin->asegurado_nombre',
							nombre :  '$Ciudad->nombre ',
							departamento: '$Ciudad->departamento',
							sinumero:'$Sin->numero',
							Aseguradora:'$Aseguradora',
							fec_autorizacion:'$Sin->fec_autorizacion',
							Nusuario : '$Nusuario',
							Hoy:'$Hoy',
							observaciones:'$Auto->observaciones',
							$Ruta_gabriel : '$Ruta_gabriel',
							fechacontrol :'$Fecha_control'

							},
							
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
        </script>
		
				</body>";	

	

     	echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
		  <script>
            $.ajax(
                    {
                        url: 'https://sac.aoacolombia.com/enviar.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							copia:'$Email_usuario',
						     para:'gabrielsandoval@aoacolombia.com',
							contenido:'7',
							asunto:'SOLICITUD AUTORIZACION GARANTIA EN EFECTIVO $Sin->numero',
							asegurado_nombre:'$Sin->asegurado_nombre',
							nombre :  '$Ciudad->nombre ',
							departamento: '$Ciudad->departamento',
							sinumero:'$Sin->numero',
							Aseguradora:'$Aseguradora',
							fec_autorizacion:'$Sin->fec_autorizacion',
							Nusuario : '$Nusuario',
							Hoy:'$Hoy',
							observaciones:'$Auto->observaciones',
							$Ruta_gabriel : '$Ruta_gabriel',
							fechacontrol :'$Fecha_control'

							},
							
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
        </script>
		</body>";	
				
				
	$Envio2=enviar_gmail($Email_usuario /*de */,
	$NUSUARIO /*Nombre de */ ,
	"gabrielsandoval@aoacolombia.com,Gabriel Sandoval" /*para */,
	"$Email_usuario,$Nusuario" /*con copia*/,
	"SOLICITUD AUTORIZACION GARANTIA EN EFECTIVO $Sin->numero" /*Objeto */,
	$Mensaje);
	if($Envio1 && $Envio2 )
		echo "Envio exitoso a: gabrielsandoval@aoacolombia.com, claudiacastro@aoacolombia.com, $Email_usuario";
	else
		echo "Falla en el envío del mail.";
}

function pide_siniestro() // presenta de varios siniestros encontrados para que el funcionario seleccione el que necesita procesar.
{
	global $sini;
	html('SELECCION DE SINIESTRO');
	echo "<script language='javascript'>
		function pasar(valor){parent.activa_autoriza(valor);}
	</script><body>
	Seleccione el siniestro que está buscando<br />
	<form action='null' method='post' target='_self' name='forma' id='forma'>
	".menu1('forma',"select id,numero from siniestro where numero like '%$sini%' and estado in (5,3,7,8) ",0,1,''," onchange='pasar(this.value);' ")."
	</form></body>";
}

function estado() // presenta la lista de solicitudes del día, y las que falta por procesar.
{
	global $FEC,$FEC1,$USUARIO,$NUSUARIO;
	html('Estado de Autorizaciones');
	echo "<script language='javascript'>
		function carga()
		{
			centrar(500,500);
		}
		function actualiza_info(id)
		{
			modal('zautorizaciones.php?Acc=actualizar_info&idauto='+id,0,0,100,100,'auinfo');
		}
		function guardar_numero_voucher(id)
		{
			var Dato=document.getElementById('numero_voucher_'+id).value;
			window.open('zautorizaciones.php?Acc=guardar_numero_voucher&id='+id+'&d='+Dato,'Oculto_autorizacion');
		}
		function versin(id){modal('zsiniestro.php?Acc=buscar_siniestro&id='+id,0,0,600,1000,'eds');}
	</script>
	<body onload='carga()'><h3>ESTADO DE SOLICITUDES DE AUTORIZACIONES</H3>
	<iframe src='zautorizacion_nueva.php' height=1 width=1 style='visibility:hidden'></iframe>
	<iframe name='Oculto_autorizacion' id='Oculto_autorizacion' height=1 width=1 style='visibility:hidden'></iframe>
	";
	if(inlist($USUARIO,'1,2'))
	{
		echo "<form action='zautorizaciones.php' method='post' target='_self' name='forma' id='forma'>
			Fecha de consulta:  Desde: ".pinta_FC('forma','FEC',$FEC)." hasta: ".pinta_FC('forma','FEC1',$FEC1).
			" <input type='submit' id='enviar' name='enviar' value='Consultar' ><input type='hidden' name='Acc' id='Acc' value='estado'></form><br />
		";
	}
	$Oficina=false;
	if($USUARIO==5) // perfil de autorizaciones
	{
		$Oficina=qo1("select oficina from usuario_autorizacion where id=".$_SESSION['Id_alterno']); // busca si el usuario tiene filtro de ciudad para ver las autorizaciones de una sola oficina
	}
	$Hoy=date('Y-m-d');
	if($Oficina)  // construye la consulta filtrando por la oficina de atención basado en la tabla de citas.  Estado de citas: P:PROGRAMADA C:CUMPLIDA S:CUMPLIDA Y NO TOMA SERVICIO
	{
		// Muestra las autorizaciones que le faltan 24 horas para su cita
		// oculta las autorizaciones que han pasado 1 dia de su cita sin importar su estado
		//
		$Query_autorizaciones="select a.*,s.numero as nsin,fq.nombre as nfranq, fq.telefono as tfq,t_codigo_ach(banco) as nbanco,s.id as idsin,datediff(cs.fecha,'$Hoy') as fdias
										from sin_autor a,franquisia_tarjeta fq,siniestro s,cita_servicio cs where a.franquicia=fq.id and a.siniestro=s.id and a.estado='E'
										and cs.siniestro=a.siniestro and cs.estado in ('P','C','S') and cs.oficina=$Oficina and s.estado in (3,5)
										and if(a.formulario_web=1,(cs.estado in ('P','S') and datediff(cs.fecha,'$Hoy')<2),1) and datediff('$Hoy',cs.fecha) <1
										";
		// ESTADO DE SINIESTROS 3: ADJUDICADO 5:PENDIENTE
	}
	else // muestra las solicitudes de todas las citas.
	{
		$Query_autorizaciones="select a.*,s.numero as nsin,fq.nombre as nfranq, fq.telefono as tfq,t_codigo_ach(banco) as nbanco,s.id as idsin,datediff(cs.fecha,'$Hoy') as fdias
										from sin_autor a,franquisia_tarjeta fq,siniestro s,cita_servicio cs where a.franquicia=fq.id and a.siniestro=s.id and a.estado='E' 
										and cs.siniestro=a.siniestro and cs.estado in ('P','C','S') and s.estado in (3,5)
										and if(a.formulario_web=1,(cs.estado in ('P','S') and datediff(cs.fecha,'$Hoy')<2),1) and datediff('$Hoy',cs.fecha) <1
										";
		//
	}


	if($Autorizaciones=q($Query_autorizaciones)) // pinta las autorizaciones pendientes
	{
		echo "<table border cellspacing='0' width='100%' style='empty-cells:show;'><tr><th>Siniestro</th><th>Datos</th><th>Resultado</th></tr>";
		while($A=mysql_fetch_object($Autorizaciones))
		{
			$Sincars=qo("select *,t_estado_siniestro(estado) as nestado,t_ciudad(ciudad) as nciudad,asegurado_id from siniestro where id=$A->siniestro");
			if($Foto=qo1("select foto_f as foto from aoacol_administra.ingreso_recepcion where siniestro=$A->siniestro"))
			$Foto="<a onclick=\"modal('../../Administrativo/".$Foto."',0,0,400,400,'foto');\"><img src='../../Administrativo/".$Foto."' border='0' height='20'></a>";
			else $Foto='';
			$Cita=qo("select * from cita_servicio where siniestro=$A->siniestro and estado='P' ");
			switch($Sincars->estado)
			{
				case 1: $sty='background-color:ff0000;text-decoration:blink;';break; // no adjudicado
				case 3: $sty='background-color:ffffaa;';break; // adjudicado
				case 5: $sty='background-color:ffff00;text-decoration:blink;';break; // pendiente
				case 7: $sty='background-color:0000ff;color:ffff00;text-decoration:blink;';break; // servicio
				case 8: $sty='background-color:0000ff;color:ffffff;text-decoration:blink;';break; // concluido
			}
			echo "<tr >
					<td style='font-size:12' ".($A->formulario_web?" bgcolor='ffddff' ":" bgcolor='ffffff' ")."><b>$A->nsin <a style='cursor:pointer' onclick='versin($A->idsin);'><img src='imagenes/000/580/dicono_f_grua_lupa.png' height=30></a><br />Identificación: ".coma_format($Sincars->asegurado_id)."</b>
					<br><br />Solicitado por: <b style='color:000077'>$A->solicitado_por</b><br><br />Ciudad: <b>$Sincars->nciudad</b>";
			if($Cita)
			{
				$Ahora=date('Y-m-d H:i:s');
				$Falta=diferencia_tiempo($Ahora,date('Y-m-d H:i:s',strtotime("$Cita->fecha $Cita->hora")));
				$Hora_llamada=aumentaminutos($Cita->hora,-119);
				if($Cita->fecha.' '.$Hora_llamada<$Ahora) $Alerta="bgcolor='ff5555' ";
				else $Alerta=" bgcolor='ffffcc' ";
				echo "<br><table align='center'>
					<tr><td align='right' $Alerta><b>Cita:</b></td><td $Alerta><b>$Cita->fecha $Cita->hora</b> </td></tr>
					<tr><td align='right' $Alerta><b>Falta:</b></td><td $Alerta><b>$Falta $Ahora</b> ($A->fdias)</td></tr>
					<tr><td align='right' $Alerta><b>Fecha y Hora de Marcación:</b></td><td $Alerta><b>$Cita->fecha $Hora_llamada</b></td></tr>
					</table>";
			}
			echo "</td>
					<td  bgcolor='ffffff'>
							<table width='100%'>
							<tr><td>Nombre:</td><td style='font-size:14'><b>$A->nombre</b> $Foto</td></tr>
							<tr bgcolor='eeffee'><td>Identificación:</td><td style='font-size:14'><b>".coma_format($A->identificacion)."</b></td></tr>
							<tr><td>Numero de tarjeta:</td><td style='font-size:14'><b>".substr($A->numero,0,4)."-".substr($A->numero,4,4)."-".substr($A->numero,8,4)."-".substr($A->numero,12,4)."</b></td></tr>
							<tr bgcolor='eeffee'><td>Franquicia:</td><td style='font-size:14'><b>$A->nfranq</b></td></tr>
							<tr><td>Banco:</td><td style='font-size:14'><b>$A->nbanco</b></td></tr>
							<tr bgcolor='eeffee'><td>Vencimiento:</td><td style='font-size:14'><b>$A->vencimiento_mes / $A->vencimiento_ano</b></td></tr>
							<tr><td>Código Seguridad:</td><td style='font-size:14'><b>$A->codigo_seguridad</b></td></tr>
							<tr bgcolor='eeffee'><td>Número Voucher:</td><td style='font-size:14'>";
			if($A->numero_voucher) echo "<b>$A->numero_voucher</b>";
			else echo "<input type='text' name='numero_voucher_$A->id' id='numero_voucher_$A->id' class='numero' value='' size='10' maxlength='10'>
				<a style='cursor:pointer' onclick='guardar_numero_voucher($A->id);'><img src='gifs/standar/Next.png' border='0'></a>";
			echo "</td></tr>
							<tr><td>Fecha y hora de solicitud:</td><td style='font-size:14'><b>$A->fecha_solicitud</b></td></tr>
							</table>
							<b style='color:55bb55;background-color:000055;font-size:16px'>TELEFONO FRANQUICIA: $A->tfq</b>
					</td>
					<td bgcolor='ffffff'>
							<form action='zautorizaciones.php' method='post' target='_self' name='forma$A->id' id='forma$A->id'>
								<table>";
								// por cada solicitud de autorización muestra un formulario para el proceso de la autorización ante la garantía
			if($Sincars->estado!=8 && $Sincars->estado!=7)
				echo "<tr><td colspan=2><a onclick=\"modal('http://www.runt.com.co:7777/runt/ciudadanos/consultas/consulta_ciudadano_por_documento_final_public.jsf',100,100,600,800,'consulta');\"
                     style='cursor:pointer;color:blue;' class='info'>Consulta de estado de licencia de conducción RUNT<span style='width:200px'>Click para consultar el estado de la licencia de conducción en RUNT</span></a><br />
                     <a onclick=\"modal('http://web.mintransporte.gov.co/Consultas/transito/Consulta23122010.htm',100,100,600,800,'consulta');\"
                     style='cursor:pointer;color:blue;' class='info'>Consulta de estado de licencia de conducción MinTransporte<span style='width:200px'>Click para consultar el estado de la licencia de conducción en MINTRANSPORTE</span></a><br />
                     Marque si la licencia de conducción ha sido verificada correctamente : <input type='checkbox' name='estado_licencia'
                     onclick=\"if(!this.checked) {this.form.estado.value='R';this.form.estado.onchange();}\"></td></tr>";
			echo "<tr><td>Funcionario:</td><td><input type='text' name='funcionario' size='50' maxlength='50' onblur='this.value=this.value.toUpperCase();' ></td></tr>
									<tr><td>Valor:</td><td><input type='text' name='valor' size='15' maxlength='15' value='$A->valor' class='numero'></td></tr>
									<tr><td>Estado:</td><td><select name='estado' ";
			if($Sincars->estado!=8  && $Sincars->estado!=7)
				echo "onchange=\"	if(!this.form.estado_licencia.checked) {alert('El estado de licencia de este conductor no ha sido verificado');this.value='R';} ";
			if($Sincars->estado!=8  && $Sincars->estado!=7)
				echo "if(this.value=='A' && this.form.estado_licencia.checked)";
			else
				echo "onchange=\" if(this.value=='A') ";
			echo "
									{
										this.form.num_autorizacion.style.visibility='visible';
										document.getElementById('txt1_$A->id').innerHTML='Número de autorización:';
										document.getElementById('txt2_$A->id').innerHTML='';
										if(document.getElementById('txt3_$A->id'))
										{
											document.getElementById('txt3_$A->id').innerHTML='Factura para aplicar esta autorización:';
											this.form.Factura.style.visibility='visible';
										}
										this.form.nestado.style.visibility='hidden';
										this.form.ncausal.style.visibility='hidden';
										this.form.num_autorizacion.focus();
									}
									else
									{
										this.form.num_autorizacion.value='';
										this.form.num_autorizacion.style.visibility='hidden';
										this.form.observaciones.focus();";
			if($Sincars->estado!=8 && $Sincars->estado!=7)
				echo "
                         document.getElementById('txt1_$A->id').innerHTML='Cambiar estado del siniestro a:';
                         this.form.nestado.style.visibility='visible';
                         document.getElementById('txt2_$A->id').innerHTML='Si NO ADJUDICA seleccione la causal';
                         this.form.ncausal.style.visibility='visible';
                         if(document.getElementById('txt3_$A->id'))
                         {
				              document.getElementById('txt3_$A->id').innerHTML='';
				              this.form.Factura.style.visibility='hidden';
                         }
					  ";
			else
				echo "document.getElementById('txt1_$A->id').innerHTML='';
				          document.getElementById('txt2_$A->id').innerHTML='';";
			echo "	}
					\"><option value=''></option>";
			if($A->franquicia!=6  /*Efectivo: no debe mostrar ACEPTADA, solo se debe autorizar por correo electronico*/) // solo para AOA
				echo "<option value='A'>Aceptada</option>";

			echo "<option value='R'>Rechazada</option></select></td></tr>
					<tr><td>Observaciones:</td><td><input type='text' name='observaciones' size='50' onbulr='this.value=this.value.toUpperCase();'></td></tr>
					<tr><td><span id='txt1_$A->id' name='txt1_$A->id'></span></td><td>
								<input type='text' id='num_autorizacion' name='num_autorizacion' size='10' maxlength='10' class='numero' style='visibility:hidden'>
								".menu1("nestado","select id,nombre from estado_siniestro where id in(1,3,5) ",$Sincars->estado,0,"visibility:hidden")."
								</td></tr>
					<tr><td><span id='txt2_$A->id' name='txt2_$A->id'></span></td><td>";
			// para los rechazos muestra las causales para una no adjudicación del servicio
			echo menu1("ncausal","select id,nombre from causal",0,1,"visibility:hidden")."</td></tr>";
			// busca si hay facturas asociadas al siniestro y las muestra
			if($Fac=q("Select f.id,consecutivo,fecha_emision,total FROM factura f
						WHERE f.siniestro=$A->siniestro and f.anulada=0 and f.autorizadopor!='' and	f.id not in (select factura from recibo_caja where consignacion_numero!='')	"))
			{
				echo "<tr><td><span id='txt3_$A->id' name='txt2_$A->id'></span></td><td><select name='Factura' style='visibility:hidden'><option value=''></option>";
				while($Fa=mysql_fetch_object($Fac))
				{
					echo "<option value='$Fa->id'>$Fa->consecutivo [$Fa->fecha_emision] ".coma_format($Fa->total)."</option>";
				}
				echo "</td></tr>";
			}
			echo "</table>";
			$Us=$_SESSION['User'];
			$T=qo1("select id from usuario_tab where usuario='$USUARIO' and tabla='siniestro' "); // Verifica si el usuario puede modificar el registro de autorizaciones
			echo "<br /><a onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$T&id=$A->siniestro',0,0,500,800,'eds');\"
		          style='cursor:pointer'>Subir las imágenes al Siniestro <img src='gifs/standar/Pencil.png' border='0'></a>
		          <a onclick=\"modal('zautorizaciones.php?Acc=carga_imagenes&id=$A->siniestro',0,0,600,900,'eds');\" style='cursor:pointer'><img src='gifs/webcam.png' border='0' height='20'></a>";
			echo "<br /><center>
						<input type='button' value='Grabar información' style='font-size:12;font-weight:bold;width:200px' onclick=\"
						if(!alltrim(this.form.funcionario.value))
						{
							alert('Debe escribir el nombre del funcionario');
							this.form.funcionario.style.backgroundColor='ffff55';
							this.form.funcionario.focus();
							return;
						}
						if(!this.form.estado.value)
						{
							alert('Debe seleccionar el estado de la autorización');
							this.form.estado.style.backgroundColor='ffff55';
							this.form.estado.focus();
							return;
						}
						if(this.form.estado.value=='A' && !alltrim(this.form.num_autorizacion.value))
						{
							alert('Debe registar el número de autorización');
							this.form.num_autorizacion.style.backgroundColor='ffff55';
							this.form.num_autorizacion.focus();
							return;
						}
						if(this.form.estado.value=='R' && !alltrim(this.form.observaciones.value))
						{
							alert('Debe registrar el motivo por el cual fue rechazada la solicitud');
							this.form.observaciones.style.backgroundColor='ffff55';
							this.form.observaciones.focus();
							return;
						}";
			if($Sincars->estado!=8 && $Sincars->estado!=7)
				echo "
						if(this.form.estado.value=='R')
						{
							if(this.form.nestado.value=='1' && this.form.ncausal.value==0)
							{
								alert('Debe seleccionar la causal por la cual pasa el estado del siniestro a NO ADJUDICADO');
								this.form.ncausal.style.backgroundColor='ffff55';
								return;
							}
						}";

			echo "
						this.form.Acc.value='cambio_estado';
						this.form.submit();
						\" style='font-weight:bold'></center>
						<input type='hidden' name='id' id='id' value='$A->id'>
						<input type='hidden' name='Acc' id='Acc' value='cambio_estado'>
				</form>
				<br />Estado actual del siniestro: <b style='$sty'>&nbsp;$Sincars->nestado&nbsp;</b>
				<a class='info' style='cursor:pointer' onclick='actualiza_info($A->id);'><img src='img/actualizar.png' border='0' height='40'><span style='width:200px'>Actualizar información del asegurado</span></a>";

			echo "</td>
					</tr>";
		}
		echo "</table>";
	}
	else
	{
		echo "<br /><br /><font color='red' style='font-size:16'>No hay solicitudes pendientes.</font>";
	}
	if(!$FEC)
	{
		$Hoy=date('Y-m-d');$Hoy1=date('Y-m-d');
	} else
	{
		$Hoy=$FEC;$Hoy1=$FEC1;
	}

	if($Oficina)  // construye la consulta filtrando por la oficina de atención basado en la tabla de citas.
	{
		$Query_autorizaciones="select a.*,s.numero as nsin,fq.nombre as nfranq,t_codigo_ach(banco) as nbanco,s.id as idsin
									  from sin_autor a,siniestro s,franquisia_tarjeta fq, cita_servicio cs where a.siniestro=s.id and a.franquicia=fq.id and a.estado!='E'
									  and date_format(a.fecha_solicitud,'%Y-%m-%d') between '$Hoy' and '$Hoy1' and cs.siniestro=a.siniestro and cs.oficina=$Oficina
									  order by a.fecha_solicitud desc ";
	}
	else // construye la consulta sin filtro solo los del dia
	{
		$Query_autorizaciones="select a.*,s.numero as nsin,fq.nombre as nfranq,t_codigo_ach(banco) as nbanco,s.id as idsin
									  from sin_autor a,siniestro s,franquisia_tarjeta fq where a.siniestro=s.id and a.franquicia=fq.id and a.estado!='E'
									  and date_format(a.fecha_solicitud,'%Y-%m-%d') between '$Hoy' and '$Hoy1' order by a.fecha_solicitud desc ";
	}

	if($Autorizaciones=q($Query_autorizaciones))
	{
		echo "<hr><h3>AUTORIZACIONES ANTERIORES<table border cellspacing='0' width='100%' style='empty-cells:show;'><tr><th>#</th><th>Siniestro</th><th>Datos</th><th>Resultado</th></tr>";
		$Contador=1;
		$Us=$_SESSION['User'];
		$T=qo1("select id from usuario_tab where  usuario=$Us and tabla='siniestro'");
		while($A=mysql_fetch_object($Autorizaciones))
		{
			if($Rcaja=qo("select id from recibo_caja where autorizacion=$A->id and garantia=0")) {}
			else
			{
				if($Foto=qo1("select foto_f as foto from aoacol_administra.ingreso_recepcion where siniestro=$A->siniestro"))
				{	$Foto="<a onclick=\"modal('../../Administrativo/".$Foto."',0,0,400,400,'foto');\"><img src='../../Administrativo/".$Foto."' border='0' height='20'></a>";}
				else { $Foto='';}
				$Sincars=qo("select *,t_ciudad(ciudad) as nciudad,t_estado_siniestro(estado) as nestado from siniestro where id=$A->siniestro ");
				switch($A->estado)
				{
					case 'E': $Estado='EN ESPERA';$Aut='';break;
					case 'A': $Estado='AUTORIZADO';$Aut='******'.r($A->num_autorizacion,3);break;
					case 'R': $Estado='RECHAZADO';$Aut='';break;
				}
				echo "<tr><td valign='top'>$Contador</td>
						<td style='font-size:12' bgcolor='ffffff'><b>$A->nsin <a style='cursor:pointer' onclick='versin($A->idsin);'><img src='imagenes/000/580/dicono_f_grua_lupa.png' height=30></a></b><br><br>Solicitado por: <b>$A->solicitado_por</b><br><br />Ciudad: <font color='green'><b>$Sincars->nciudad</b></font></td>
						<td  bgcolor='ffffff' valign='top'>
								<table width='100%'>
								<tr><td width='140'>Nombre:</td><td style='font-size:14'><b>$A->nombre</b> $Foto</td></tr>
								<tr bgcolor='eeffee'><td>Franquicia:</td><td style='font-size:14'><b>$A->nfranq</b></td></tr>
								<tr bgcolor='eeffee'><td>Número Voucher:</td><td style='font-size:14'><b>$A->numero_voucher</b></td></tr>
								<tr><td>Fecha y hora de solicitud:</td><td style='font-size:14'><b>$A->fecha_solicitud</b></td></tr>
								</table>
						</td>
						<td bgcolor='ffffff'>
									<table>
										<tr><td>Valor:</td><td>".coma_format($A->valor)."</td></tr>
										<tr><td valign='top'>Estado:</td><td>";

				if($A->estado=='A')
				{
					echo "<b style='color:green'>$Estado</b> <br /><a class='info' onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$T&id=$A->siniestro',0,0,700,900,'eds');\"
									style='cursor:pointer'> Ver Siniestro <img src='gifs/standar/Pencil.png' border='0'><span style='width:100px'>Ver/Editar el siniestro</span></a>&nbsp;
									<a class='info' onclick=\"modal('zautorizaciones.php?Acc=carga_imagenes&id=$A->siniestro',0,0,600,900,'eds');\" style='cursor:pointer'><img src='gifs/webcam.png' border='0' height='20'>
									<span style='width:100px'>Cargar Imagenes</span></a>&nbsp;";
					if($RC=qo("select * from recibo_caja where autorizacion=$A->id"))
					{
						if($FAC=qo("select * from factura where id=$RC->factura"))
						{
							echo "<br /><a class='info' style='cursor:pointer' onclick=\"modal('zfacturacion.php?Acc=imprimir_factura&id=$FAC->id',0,0,700,900,'eds');\">Factura: $FAC->consecutivo <img src='gifs/standar/Preview.png' border='0'><span style='width:100px'>Ver Factura</span></a> ";
						}
						echo "<br /><a class='info' style='cursor:pointer' onclick=\"modal('zcartera.php?Acc=imprimir_recibo&id=$RC->id',0,0,700,900,'eds');\">Recibo de caja: $RC->consecutivo <img src='gifs/standar/Preview.png' border='0'><span style='width:100px'>Ver Recibo de Caja</span></a> ";
					}

				}
				else
				{
					echo "<b style='color:red'>$Estado</b>";
					if(inlist($USUARIO,'1,5'))
						echo "<br><a style='cursor:pointer;' onclick=\"modal('zautorizaciones.php?Acc=datos_autorizacion&id=$A->siniestro',0,0,500,500,'solicitud');\"><u>Solicitar Visualización</u></a>";
				}
				switch($Sincars->estado)
				{
					case 1: $sty='background-color:ff0000;color:ffffff';break; // no adjudicado
					case 3: $sty='background-color:ffffaa';break; // adjudicado
					case 5: $sty='background-color:ffff00';break; // pendiente
					case 7: $sty='background-color:0000ff;color:ffff00	';break; // servicio
					case 8: $sty='background-color:0000ff;color:ffffff';break; // concluido
				}
				echo "</td></tr><tr><td>Observaciones:</td><td>$A->observaciones</td></tr><tr><td>Numero Autorización:</td><td>$Aut</td></tr>";
				if($A->estado=='A')
					echo "<tr><td>Estado del siniestro:</td><td><b style='$sty'>&nbsp;$Sincars->nestado&nbsp;</b>
							<a class='info' style='cursor:pointer' onclick='actualiza_info($A->id);'><img src='img/actualizar.png' border='0' height='20'><span style='width:200px'>Actualizar información del asegurado</span></a>
							</td></tr>";

				echo "<tr><td>Fecha de proceso:</td><td>$A->fecha_proceso</td></tr>
									<tr><td>Tiempo de proceso:</td><td>".diferencia_tiempo($A->fecha_solicitud,$A->fecha_proceso)." $A->procesado_por</td></tr>
								</table>
					</td>
					</tr>";
				$Contador++;
			}
		}
		echo "</table>";
	}
	else
	{
		echo "<br /><br />No hay solicitudes pendientes.";
	}
	echo "</body>";
}

function guardar_numero_voucher()
{
	global $id,$d;
	q("update sin_autor set numero_voucher='$d' where id=$id");
	echo "<body><script language='javascript'>alert('Numero de voucher $d grabado satisfactoriamente');</script></body>";
}

function cambio_estado()
{
	global $id,$funcionario,$estado,$observaciones,$num_autorizacion,$NUSUARIO,$Factura,$valor,$seccion,
		$nestado,$ncausal;
	include('inc/link.php');
	echo "
	<script language='javascript'>
	function volver()
	{
		window.open('zautorizaciones.php?Acc=estado','_self');
	}
	</script>
	<body>
	<iframe name='Oculto_encripcion' style='visibility:hidden' width='1' height='1'></iframe>";
	$Hoy=date('Y-m-d H:i:s');$Ahora=date('Y-m-d');
	mysql_query("update sin_autor set funcionario='$funcionario',estado='$estado', observaciones='$observaciones', num_autorizacion='$num_autorizacion',
	fecha_proceso='$Hoy',procesado_por='$NUSUARIO',valor='$valor' where id='$id' ",$LINK);
	$D=qom("select * from sin_autor where id=$id",$LINK);
	$DS=qom("select * from siniestro where id=$D->siniestro",$LINK);
	if($DS->estado!=8 && $DS->estado!=7)
	{
	    if($nestado!=$DS->estado && $estado=='R')
	    {
		      if($nestado==1)
		      {
		        mysql_query("update siniestro set estado='$nestado',causal='$ncausal', subcausal=1, observaciones=concat(observaciones,'\n$NUSUARIO [$Hoy]: Cambio de estado en Autorizaciones. $observaciones')
		            where id=$D->siniestro",$LINK);
		      }
		      else
		      {
		        mysql_query("update siniestro set estado='$nestado',observaciones=concat(observaciones,'\n$NUSUARIO [$Hoy]: Cambio de estado en Autorizaciones. $observaciones')
		            where id=$D->siniestro",$LINK);
		      }
		      mysql_query("insert into aoacol_aoacars.app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
		              values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."','".date('s')."','".$_SESSION['Nick']."','".$NUSUARIO."','siniestro','M','$DS->id','".$_SERVER['REMOTE_ADDR']."','Cambio de estado en Autorizaciones.')",$LINK);
	    }
	    mysql_query("update siniestro set observaciones=concat(observaciones,'\n$NUSUARIO [$Hoy]: Cambio de estado en Autorizaciones. $observaciones') where id=$D->siniestro",$LINK);
	}
	if($estado=='A' && $Factura)  // generación automática de recibo de caja desde aqui
	{
		$Fac=qom("select * from factura where id=$Factura",$LINK);
		$Cli=qom("select * from cliente where id=$Fac->cliente",$LINK);
		$Concepto=qo2m("select concat(t_concepto_fac(concepto),' ',descripcion,' ') as nconcepto from facturad where factura=$Factura",'; ',$LINK);
		$idOfi=qo1m("select id from oficina where ciudad='$DS->ciudad'",$LINK);
		$Oficina=qom("select * from oficina where id=$idOfi",$LINK);
		$Consecutivo=qo1m("select max(consecutivo) from recibo_caja where oficina=$idOfi",$LINK)+1;
		$Fecha=date('Y-m-d');
		mysql_query("insert into recibo_caja (oficina,fecha,consecutivo,cliente,valor,concepto,siniestro,factura,efectivo,tarjeta_credito,autorizacion,capturado_por) values
		('$idOfi','$Fecha','$Consecutivo','$Fac->cliente','$Fac->total',\"$Concepto\",'$Fac->siniestro','$Factura','0','$Fac->total','$id','$NUSUARIO')",$LINK);
		if($IDN=mysql_insert_id($LINK))  graba_bitacora('recibo_caja','A',$IDN);
	}
	mysql_close($LINK);
	if($estado=='A' || $estado=='R')
	echo "<script language='javascript'>window.open('zencriptasin_autor.php?volviendo=1','Oculto_encripcion');</script>";
	else echo "<script language='javascript'>window.open('zautorizaciones.php?Acc=estado','_self');</script>";
	echo "</body>";
}

function ver_autorizaciones()
{
	global $id;  // numero de siniestro
	$Sin=qo("select * from siniestro where id=$id");
	$Aseguradora=qo("select * from aseguradora where id=$Sin->aseguradora");
	$Ciudad=qo("Select nombre from ciudad where codigo='$Sin->ciudad'");
	html('HISTORICO DE AUTORIZACIONES Siniestro número: '.$Sin->numero);
	echo "<html><body>
	<script language='javascript'>centrar(1000,700);</script>
	<h3>Siniestro número: $Sin->numero - $Aseguradora->nombre </h3>";
	if($Autorizaciones=q("select *, case estado when 'A' then 'Autorizado' when 'E' then 'En Espera' when 'R' then 'Rechazado' end as nestado,t_franquisia_tarjeta(franquicia) as nfranq,
										 t_codigo_ach(banco) as nbanco from sin_autor where siniestro=$id"))
	{
		echo "<table border cellspacing='0'><tr>
				<th>Tarjeta Habiente</th>
				<th>Número Tarjeta</th>
				<th>Banco</th>
				<th>Franquicia</th>
				<th>Vencimiento</th>
				<th>Voucher</th>
				<th>Valor</th>
				<th>Estado</th>
				<th>Funcionario que atendio</th>
				<th>Num Autorización</th>
				<th>Fecha Solicitud</th>
				<th>Solicitado por</th>
				<th>Fecha de Proceso</th>
				<th>Procesado por</th>
				<th>Observaciones</th>
				</tr>";
		while($Au=mysql_fetch_object($Autorizaciones))
		{
			echo "<tr>
						<td>$Au->nombre  Id:".coma_format($Au->identificacion)."</td>
						<td> ******".r($Au->numero,4)."</td>
						<td>$Au->nbanco</td>
						<td>$Au->nfranq</td>
						<td>$Au->vencimiento_mes - $Au->vencimiento_ano</td>
						<td>$Au->numero_voucher</td>
						<td align='right'>".coma_format($Au->valor)."</td>
						<td align='center'>$Au->nestado</td>
						<td >$Au->funcionario</td>
						<td align='center'>*****".r($Au->num_autorizacion,3)."</td>
						<td align='center'>$Au->fecha_solicitud</td>
						<td >$Au->solicitado_por</td>
						<td align='center' >$Au->fecha_proceso</td>
						<td >$Au->procesado_por</td>
						<td >$Au->observaciones</td>
						</tr>";
		}
		echo "</table>";
	}
	else
		echo "<br /><br /><center>No hay Autorizaciones para este siniestro.</center>";
}

function datos_autorizacion()
{
	global $id; // id del siniestro
	global $USUARIO,$NUSUARIO,$IDUSUARIO;
	$Visualizacion_completa=false;
	if($USUARIO==5) // AUTORIZACIONES
	{
		$Datos_usuario=qo("select * from usuario_autorizacion where id=".$IDUSUARIO);
		if($Datos_usuario->visualiza_completa) $Visualizacion_completa=true;
	}
	$Solicitado_por=$NUSUARIO;
	if($Solicitado=qo("select * from solicitud_dataautor where solicitado_por='$Solicitado_por' and autorizado_por!='' and visualizado_por='' and siniestro='$id' and
							date_format(fecha_solicitud,'%Y-%m-%d') ='".date('Y-m-d')."' "))
		$Por_visualizar=true;
	else $Por_visualizar=false;

	// validacion si el siniestro despues de concluido no tiene mas de 8 dias de antiguedad
	html('SOLICITUD DE INFORMACIÓN DE UNA AUTORIZACION');
	echo "<script language='javascript'>
			function carga()
			{
				centrar(700,500);
			}
			function valida_solicitud()
			{
				with(document.forma)
				{
					if(!alltrim(Clave.value)) {alert('Debe digitar su password del sistema'); Clave.style.backgroundColor='ffdddd';Clave.focus();return false;}
					if(!alltrim(cadena.value)) {alert('Debe digitar la cadena de seguridad');cadena.style.backgroundColor='ffdddd';cadena.focus();return false;}
					";
	if(!$Por_visualizar) echo "
					if(tipo.value=='2' && !alltrim(motivo.value))  {alert('Debe digitar el motivo cuando desea ver el detalle de la autorización');motivo.style.backgroundColor='ffdddd';motivo.focus();return false;}
					";
	echo "
					submit();
				}
			}
		</script>
		<body onload='carga()'><h3>Solicitud de Información de Autorizaciones</h3>";
	if($Visualizacion_completa) echo "VC ";
	$Sin=qo("select *,t_aseguradora(aseguradora) as naseg from siniestro where id=$id");
	echo "<b>Siniestro: $Sin->numero $Sin->naseg</b><br><br>";

	echo "<form action='zautorizaciones.php' method='post' target='_self' name='forma' id='forma'>
		<h3>Solicitud de Visualización de Garantía.</h3>
		Estimado(a) Usuario(a) ".$_SESSION['Nombre'].", Usted va a solicitar una autorización electrónica para poder visualizar una vez
		la información correspondiente a la garantía de este siniestro. Por favor digite su contraseña, llene la cadena de seguridad y de click en Enviar.<br><br>
		Clave del usuario actual: <input type='password' name='Clave' id='Clave' onKeyPress='bloqueo_mayusculas(event)'>
			<div id='bloqueomayusculas' style='visibility:hidden'><b style='color:red'>El bloqueo de mayúsculas está activado</b></div><br /><br />
		<table><tr><td><img src='inc/captcha/captcha.php?.png' id='chueco' alt='CAPTCHA' align='middle' border='2'></td><td>
		<a style='cursor:pointer' onclick=\"javascript:document.getElementById('chueco').src='inc/captcha/captcha.php?.png';\"><img src='gifs/standar/refrescar.png' border='0'>
		Cambiar la imagen </a><br><br>
		Escriba la cadena de seguridad: <input type='text' name='cadena' id='cadena' value='' size='10' maxlength='10' onblur='this.value=this.value.toUpperCase();'>
		<br><br>
		Tipo de Solicitud: <select name='tipo'><option value='1'>Información Básica</option>
		<option value='2'>Información Completa</option>
		</select><br><br>";
	if($Por_visualizar) echo "<b style='color:00aa00;font-size:18px;'>Visualización Completa Autorizada<b><br><br>";
	else echo "Motivo de la revisión: <input type='text' name='motivo' id='motivo' value='' size='70'><br><br>";
	echo "<input type='button' value=' CONTINUAR ' onclick='valida_solicitud();'>
		<input type='hidden' name='Acc' value='datos_autorizacion_valida_clave'>
		<input type='hidden' name='id' value='$id'>
		</form>";
	echo "</body>";
}

function datos_autorizacion_valida_clave()
{
	global $id,$Clave,$cadena,$tipo,$motivo;
	global $USUARIO,$NUSUARIO,$IDUSUARIO;
	$Visualizacion_completa=false;
	if($USUARIO==5) // AUTORIZACIONES
	{
		$Datos_usuario=qo("select * from usuario_autorizacion where id=".$IDUSUARIO);
		if($Datos_usuario->visualiza_completa) $Visualizacion_completa=true;
	}
	if(verificar_password($_SESSION['Nick'],$Clave))
	{
		if($cadena!=$_SESSION['CAPTCHAString'])
		{
			html('ERROR DE VALIDACION');
			graba_bitacora('siniestro','O',$id,'Solicitud de Información de Autorizaciones fallida por error en captcha.');
			echo "<script language='javascript'>
				function carga()
				{alert('Código de seguridad Incorrecto.');window.history.back();}
			</script>
			<body onload='carga()'></body>";
		}
		else
		{
			if($tipo==1)
				echo "<body>
					<form action='zautorizaciones.php' method='post' target='_self' name='forma' id='forma'>
						<input type='hidden' name='Acc' value='datos_autorizacion_ok'>
						<input type='hidden' name='tip' value='$tipo'>
						<input type='hidden' name='id' value='$id'>
					</form>
					<script language='javascript'>document.forma.submit();</script>
					</body>";
			elseif($Visualizacion_completa)
			{
				$Solicitado_por=$_SESSION['Nombre'];
				$Fecha=date('Y-m-d H:i:s');
				$idns=q("insert into solicitud_dataautor (siniestro,solicitado_por,fecha_solicitud,motivo,visualizado_por,fecha_visualizacion,autorizado_por,fecha_aprobacion) values ('$id','$Solicitado_por','$Fecha',\"$motivo\",'$Solicitado_por','$Fecha','AUTOMATICO','$Fecha')");
				graba_bitacora('siniestro','O',$id,'Visualización Completa de Garantias automática.');
				echo "<form action='zautorizaciones.php' method='post' target='_self' name='forma' id='forma'>
						<input type='hidden' name='Acc' value='datos_autorizacion_ok'>
						<input type='hidden' name='tip' value='$tipo'>
						<input type='hidden' name='id' value='$id'>
					</form>
					<script language='javascript'>document.forma.submit();</script>
					</body>";
			}
			else
			{

				$Solicitado_por=$_SESSION['Nombre'];
				$Fecha=date('Y-m-d H:i:s');

				if($Solicitado=qo("select * from solicitud_dataautor where solicitado_por='$Solicitado_por' and autorizado_por!='' and visualizado_por='' and siniestro='$id' "))
				{
					q("update solicitud_dataautor set visualizado_por='$Solicitado_por', fecha_visualizacion='$Fecha' where id=$Solicitado->id");
					echo "<form action='zautorizaciones.php' method='post' target='_self' name='forma' id='forma'>
						<input type='hidden' name='Acc' value='datos_autorizacion_ok'>
						<input type='hidden' name='tip' value='2'>
						<input type='hidden' name='id' value='$id'>
					</form>
					<script language='javascript'>document.forma.submit();</script>
					</body>";
				}
				
				elseif($Solicitado=qo("select * from solicitud_dataautor where solicitado_por='$Solicitado_por' and autorizado_por='' and siniestro='$id' "))
				{
					echo "<body><script language='javascript'>alert('Existe una solicitud de visualización pendiente de este Siniestro, pero no ha sido autorizada por el funcionario indicado');</script></body>";
				}
				else
				{
					$idn=q("insert into solicitud_dataautor (siniestro,solicitado_por,fecha_solicitud,motivo) values ('$id','$Solicitado_por','$Fecha',\"$motivo\")");
					$Siniestro=qo("select asegurado_nombre,fec_autorizacion,ciudad_original,t_aseguradora(aseguradora) as naseg,ciudad,numero,t_estado_siniestro(estado) as nestado from siniestro where id=$id");
					$Oficina=qo("select * from oficina where ciudad='$Siniestro->ciudad' ");
					$Email_usuario=usuario('email');
					$Ciudad=qo("select * from ciudad where codigo='$Siniestro->ciudad' ");
					if($Siniestro->ciudad_original) $Ciudado=qo("select * from ciudad where codigo='$Siniestro->ciudad_original' ");
					else $Ciudado=$Ciudad;
					$Ruta_arturo="utilidades/Operativo/operativo.php?Acc=autorizar_visualizacion_garantia&idn=$idn&Fecha=$Fecha&Usuario=Sergio Castillo Castro";
					$Ruta_claudia="utilidades/Operativo/operativo.php?Acc=autorizar_visualizacion_garantia&idn=$idn&Fecha=$Fecha&Usuario=Claudia Castro Garzon";
					$Mensaje="<body><b>SOLICITUD DE VISUALIZACION GARANTIA</B><BR><BR>Numero Siniestro: $Siniestro->numero - $Siniestro->naseg<br>".
					"Asegurado:$Siniestro->asegurado_nombre<br>Ciudad: $Ciudad->nombre ($Ciudad->departamento) <br>Ciudad Original: $Ciudado->nombre ($Ciudado->departamento)<br>".
					"Fecha de autorizacion: $Siniestro->fec_autorizacion<br>".
					"<br>Funcionario que solicita: $Solicitado_por Fecha de solicitud: $Fecha <br><br>".
					"<b>Motivo:</b> $motivo<br><br>".
					"Para AUTORIZAR la visualizacion de la garantia haga click aqui: <a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='$Ruta_claudia';\$Fecha_control='".date('Y-m-d')."';")."' target='_blank'>AUTORIZAR</a></body>";
					$Envio1=enviar_gmail($Email_usuario /*de3 */,
					$Solicitado_por /*Nombre de */ ,
					"$Oficina->email_autorizacion,$Oficina->nombre_autorizacion" /*para */,
					"" /*con copia*/,
					"SOLICITUD AUTORIZACION VISUALIZACION GARANTIA $Siniestro->numero" /*Objeto */,
					$Mensaje);
					if($Envio1)
					{echo "<body><script language='javascript'>alert('Mensaje enviado a $Oficina->email_autorizacion $Oficina->nombre_autorizacion');window.close();void(null);</script>";}
					else
					{echo "<body><script language='javascript'>alert('No se pudo enviar el mensaje a $Oficina->email_autorizacion $Oficina->nombre_autorizacion. Intentelo nuevamente.');</script>";}
				}
			}
		}
	}
	else
	{
		html('ERROR DE VALIDACION');
		graba_bitacora('siniestro','O',$id,'Solicitud de Información de Autorizaciones fallida por error en contraseña.');
		echo "<script language='javascript'>
				function carga()
				{
					alert('Clave Incorrecta.');
					window.close();void(null);
				}
			</script>
			<body onload='carga()'></body>";

	}
}

function datos_autorizacion_ok()
{
	global $id,$tip,$Audita;  // id del siniestro
	graba_bitacora('siniestro','O',$id,'Soliciutd de Información de Autorizaciones satisfactoria.');
	html("SOLICITUD DE INFORMACION DE AUTORIZACIONES");
	echo "<body><h3>RESULTADO DE LA SOLICITUD:</H3><script language='javascript'>centrar();</script>";
	$Resultado='';
	if($Autorizaciones=q("select *,t_codigo_ach(banco) as nbanco,t_codigo_ach(devol_banco) as ndevol_banco
		from sin_autor where siniestro=$id "))
	{
		while($A=mysql_fetch_object($Autorizaciones))
		{
			if($tip==1)
			{
				$Sin=qo("select numero from siniestro where id=$A->siniestro");
				$Fran=qo("select nombre from franquisia_tarjeta where id=$A->franquicia");
				$Resultado.="<br><b>SINIESTRO NUMERO $Sin->numero</b><br>
				<table border cellspacing='0'><tr><td>
				Estado : ".($A->estado=='A'?"<b style='background-color:ddffdd;font-size:16px'>ACEPTADO</b>":"<b style='background-color:ffdddd;font-size:16px'>RECHAZADO</b>")."<br>
				Nombre Tarjeta habiente: <b>$A->nombre </b><br>
				Franquicia: <b>$Fran->nombre</b><br>
				Numero Voucher: <b>$A->numero_voucher</b><br>
				Valor solicitado: <b>".coma_format($A->valor)."</b><br>
				Fecha Proceso: <b>$A->fecha_proceso</b><br>
				Solicitado por: <b>$A->solicitado_por</b><br>
				Procesado por: <b>$A->procesado_por</b><br>
				</td><td><b>INFORMACION PARA LA DEVOLUCION</b><br>
				Banco: <b>$A->ndevol_banco</b><br>
				Número de cuenta: <b>$A->devol_cuenta_banco</b><br>
				A nombre de: <b>$A->devol_ncuenta</b><br>
				Identificación: <b>$A->identificacion_devol</b></br>
				Tipo de cuenta: <b>".($A->devol_tipo_cuenta=='A'?'Ahorros':'Corriente')."</b><br>
				</td></tr></table>
				<br><br>";
			}
			else
			{
				if(!$A->numero && $A->data)
				{
					$Rd=desencripta_data($A->id);
					$A->identificacion=$Rd['identificacion'];
					$A->numero=$Rd['numero'];
					$A->nbanco=$Rd['banco'];
					$A->vencimiento_mes=$Rd['vencimiento_mes'];
					$A->vencimiento_ano=$Rd['vencimiento_ano'];
					$A->num_autorizacion=$Rd['num_autorizacion'];
					$A->funcionario=$Rd['funcionario'];
					$A->codigo_seguridad=$Rd['codigo_seguridad'];
				}
				$Sin=qo("select numero from siniestro where id=$A->siniestro");
				$Fran=qo("select nombre from franquisia_tarjeta where id=$A->franquicia");
				$Resultado.="<br><b>SINIESTRO NUMERO $Sin->numero</b><br>
				Estado : ".($A->estado=='A'?"<b style='background-color:ddffdd;font-size:16px'>ACEPTADO</b>":"<b style='background-color:ffdddd;font-size:16px'>RECHAZADO</b>")."<br>
				Nombre Tarjeta habiente: <b>$A->nombre </b><br>
				Identificación: <b>$A->identificacion</b><br>
				Numero Tarjeta: <b>$A->numero</b><br>
				Banco: <b>$A->nbanco</b><br>
				Franquicia: <b>$Fran->nombre</b><br>
				Vencimiento: <b>$A->vencimiento_mes - $A->vencimiento_ano</b><br>
				Numero Voucher: <b>$A->numero_voucher</b><br>
				Valor solicitado: <b>".coma_format($A->valor)."</b><br>
				Funcionario: <b>$A->funcionario</b><br>
				Observaciones: <b>$A->observaciones</b><br>
				Numero_autorizacion: <b>$A->num_autorizacion</b><br>
				Fecha Proceso: <b>$A->fecha_proceso</b><br>
				Código Seguridad: <b>$A->codigo_seguridad</b><br><br>";
			}
		}
	}
	else
	{
		$Resultado='No se encuentran autorizaciones aprobadas.';
	}
	echo "$Resultado";
	echo "<br /><CENTER><INPUT TYPE='BUTTON' VALUE='CERRAR ESTA VENTANA' onclick='window.close();void(null);' style='font-size:16;font-weight:bold; height=30px;'></center>
	<b>Estimado(a) ".$_SESSION['Nombre']." :<br><u>INCOCREDITO</u> es la entidad que vigila el correcto uso de la información de tarjetas de crédito y tarjeta habientes en los establecimientos comerciales.
	AOA no es la excepción y por lo tanto debemos cumplir los requisitos y sugerencias de seguridad que INCOCREDITO propone. <br>Una estrategia de seguridad es NO guardar, grabar o mantener en ningún
	medio fisico o electrónico cualquier información total o parcial de las tarjetas de crédito de los asegurados. Por lo tanto no copie ni guarde esta información que se está presentando en pantalla. Solamente
	utilicela solamente para el <b>Motivo</b> por el cual fue autorizada su visualización. En el caso de ocurrir un fraude, será investigado cada funcionario que tiene acceso a la información o que
	haya sido autorizado para verla. Tan pronto sea utilizada debidamente esta información, se debe cerrar la ventana para que nadie más pueda hacer un mal uso de la misma.<br>
	</b>
	A continuación se muestra un fragmento de la página www.incocredito.com.co en la  que se expresa claramente las políticas de seguridad que se deben mantener.<br>
	<img src='img/incocredito.jpg'></body>";
}

function actualizar_info()
{
	global $idcita,$idauto;
	$Us=$_SESSION['User'];
	if($idauto)
	{
		$Autorizacion=qo("select * from sin_autor where id=$idauto");
		$Sin=qo("select id,numero,t_aseguradora(aseguradora) as naseguradora,t_ciudad(ciudad) as noficina from siniestro where id=$Autorizacion->siniestro");
	}
	else
	{
		$Cita=qo("select * from cita_servicio where id=$idcita");
		$Sin=qo("select id,numero,t_aseguradora(aseguradora) as naseguradora,t_ciudad(ciudad) as noficina from siniestro where id=$Cita->siniestro");
	}
	html('ACTUALIZACION DE INFORMACION');
	echo "<script language='javascript'>
			var C=new Array();
			var Posicion=0;

			function copia_campo(Ncampo,id)
			{
				var Primerid=document.forma.primerid.value;
				if(Ncampo==1) Campo='email';
				if(Ncampo==2) Campo='nbanco';
				if(Ncampo==3) Campo='cuenta';
				if(Ncampo==4) Campo='beneficiario';
				document.getElementById(Campo+id).value=document.getElementById(Campo+Primerid).value;
			}

			function validar_formulario()
			{
				 pasar_info();
			}

			function pasar_info()
			{
				if(Posicion<C.length)
				{
					var id=C[Posicion];
					var Soloemail=document.getElementById('Soloemail'+id).value;
					var email=document.getElementById('email'+id).value;
					if(document.getElementById('nbanco'+id)) {var nbanco=document.getElementById('nbanco'+id).value;} else {var nbanco='';}
					if(document.getElementById('cuenta'+id)) {var cuenta=document.getElementById('cuenta'+id).value;} else {var cuenta='';}
					if(document.getElementById('tipo'+id)) {var tipo=document.getElementById('tipo'+id).value;} else {var tipo='';}
					if(document.getElementById('beneficiario'+id)) {var beneficiario=document.getElementById('beneficiario'+id).value;} else {var beneficiario='';}
					if(email)
					{
						window.open('zautorizaciones.php?Acc=actualizar_info_ok&Soloemail='+Soloemail+'&email='+email+'&nbanco='+nbanco+'&cuenta='+cuenta+'&tipo='+tipo+'&beneficiario='+beneficiario+'&id='+id,'Oculto_ainfo');
						document.getElementById('email'+id).style.backgroundColor='ffffdd';
						document.getElementById('nbanco'+id).style.backgroundColor='ffffdd';
						document.getElementById('cuenta'+id).style.backgroundColor='ffffdd';
						if(document.getElementById('tipo'+id)) document.getElementById('tipo'+id).style.backgroundColor='ffffdd';
						document.getElementById('beneficiario'+id).style.backgroundColor='ffffdd';
					}
					else
					{
						Posicion++;
						pasar_info();
					}
				}
				else
				{
					alert('La información fue actualizada satisfactoriamente');
					window.close();void(null);
				}
			}
		</script>
		<body ><script language='javascript'>centrar(1000,500);</script>";

	if($Autorizaciones=q("select *, t_franquisia_tarjeta(franquicia) as nfr from sin_autor where siniestro=$Sin->id  and estado in ('A','E') "))
	{

		echo "<form action='zautorizaciones.php' method='post' target='_self' name='forma' id='forma'>
			<input type='hidden' name='primerid' id='primerid' value=''>
			<h1 align='center'>INFORMACION PARA LA DEVOLUCION DE LA GARANTIA</H1>
			Aseguradora: <b>$Sin->naseguradora</b>  Ciudad: <b>$Sin->noficina</b>
			<table border cellspacing='0'><tr>
			<th></th>
			<th>Asegurado</th>
			<th>Identificación</th>
			<th>Tarjeta Habiente</th>
			<th>Franquicia</th>
			<th>Numero</th>
			<th>Fecha Solicitud</th>
			<th>Fecha Proceso</th>
			<th>Monto</th>
			<th>Estado</th>
			</tr>";
		$Suma=0;
		$Bg='ffffff';
		$Contador=1;
		include('inc/link.php');
		while($A=mysql_fetch_object($Autorizaciones))
		{
			if($A->estado=='E') $Estado='En espera';
			if($A->estado=='A') $Estado='Autorizado';
			if($A->estado=='R') $Estado='Rechazado';
			$Sin=qom("select asegurado_nombre,asegurado_id from siniestro where id=$A->siniestro ",$LINK);
			if($Suma==0)
			{
				echo "<script language='javascript'>document.forma.primerid.value='$A->id';</script>";
			}
			if($A->devol_banco && $A->devol_cuenta_banco && $A->devol_tipo_cuenta && $A->devol_ncuenta && $Us!=5 && $Us!=10) $Soloemail='SI'; else $Soloemail='NO';
			echo "<tr bgcolor='$Bg'><td rowspan=2>$Contador</td>
					<td>$Sin->asegurado_nombre</td><td align='right'>".coma_format($Sin->asegurado_id)."</td>
					<td><b>$A->nombre</b></td><td align='center'>$A->nfr</td><td align='right'>".r($A->numero,4)."</td><td>$A->fecha_solicitud</td><td>$A->fecha_proceso</td><td align='right' style='color:000088'>".coma_format($A->valor)."</td>
					<td align='center' style='color:880000'>$Estado</td></tr>";
			if($A->fecha_envio!='0000-00-00 00:00:00')
			{
				echo "<tr bgcolor='ddffdd'><td colspan=8>Esta garantía ya fue enviada por email a $A->email</td></tr>";
			}
			else
			{
				echo "
					<tr bgcolor='$Bg'><td colspan=8 nowrap='yes'>
					Email:<input type='text' name='email$A->id' id='email$A->id' value='$A->email' size=30 ondblclick='copia_campo(1,$A->id);'>
					Banco: <input type='text' name='nbanco$A->id' id='nbanco$A->id' ".($Soloemail=='SI'?" value='Información Protegida' disabled ":" value='$A->devol_banco' size=20 ondblclick='copia_campo(2,$A->id);' ").">
					Cuenta: <input type='text' name='cuenta$A->id' id='cuenta$A->id' ".($Soloemail=='SI'?" value='Información Protegida' disabled ":" value='$A->devol_cuenta_banco' size=20 ondblclick='copia_campo(3,$A->id);' ").">
					Tipo: ".($Soloemail=='SI'?" Info.Protegida":" <select name='tipo$A->id' id='tipo$A->id'><option value=''></option><option value='A' ".($A->devol_tipo_cuenta=='A'?"selected":"").">Ahoros</option><option value='C' ".($A->devol_tipo_cuenta=='C'?"selected":"").">Corriente</option></select> ")."
					A nombre de: <input type='text' name='beneficiario$A->id' id='beneficiario$A->id' ".($Soloemail=='SI'?" value='Información Protegida' disabled ":" value='$A->devol_ncuenta' size=30  ondblclick='copia_campo(4,$A->id);' ").">
					<input type='hidden' name='Soloemail$A->id' id='Soloemail$A->id' value='$Soloemail'>
					</td></tr>
					<tr><td colspan=9>&nbsp;</th></tr><script language='javascript'>C[C.length]=$A->id;</script>";
			}
			echo "";
			if($Bg=='ffffff') $Bg='dddddd'; else $Bg='ffffff';
			$Suma+=($A->estado=='A'?$A->valor:0);
			$Contador++;
		}
		mysql_close($LINK);
		echo "<tr><th colspan=8>Total</td><th align='right' style='font-size:14px'>".coma_format($Suma)."</td></tr></table>
		<center><input type='button' value=' Grabar Información ' style='font-size:18px;font-weight:bold' onclick='validar_formulario();'></center>

		</form>
		<iframe name='Oculto_ainfo' id='Oculto_ainfo' style='visibility:hidden' height='1' width='1'></iframe>";
	}
	else
	{
		echo "<b style='color:ff0000'>No hay Autorizaciones para el siniestro $Sin->numero</b> ";
	}
	echo "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></body>";
}

function actualizar_info_ok()
{
	global $email,$nbanco,$cuenta,$tipo,$beneficiario,$id,$Soloemail;
	if($Soloemail=='SI')
		q("update sin_autor set email='$email' where id=$id");
	else
		q("update sin_autor set email='$email',devol_banco='$nbanco',devol_cuenta_banco='$cuenta',devol_ncuenta='$beneficiario',devol_tipo_cuenta='$tipo' where id=$id");
	sleep(1);
	echo "<script language='javascript'>
			function carga()
			{
				parent.Posicion++;
				parent.pasar_info();
			}
		</script>
		<body onload='carga()'></body>";

}

function carga_imagenes()
{
	global $id;  // id del siniestro
	$Sin=qo("select * from siniestro where id=$id");

	if($Autorizaciones=q("select *,t_codigo_ach(banco) as nbanco,t_codigo_ach(devol_banco) as ndevol_banco
		from sin_autor where siniestro=$id and estado='A' "))
	{
		$Resultado='';
		while($A=mysql_fetch_object($Autorizaciones))
		{
			$Rd=desencripta_data($A->id);
			$A->identificacion=$Rd['identificacion'];
			$A->numero=$Rd['numero'];
			$A->nbanco=$Rd['banco'];
			$A->vencimiento_mes=$Rd['vencimiento_mes'];
			$A->vencimiento_ano=$Rd['vencimiento_ano'];
			$Fran=qo("select nombre from franquisia_tarjeta where id=$A->franquicia");

			$Resultado.="<table border cellspacing='0'><tr><td valign='top'>
				Nombre Tarjeta habiente: <b>$A->nombre </b><br>
				Franquicia: <b>$Fran->nombre</b><br>
				Numero: <b>***********".r($A->numero,10)."</b><br>
				Identificación: <b>***********".r($A->identificacion,5)."</b><br>
				Numero Voucher: ";
			if($A->numero_voucher) $Resultado.="<b>$A->numero_voucher</b>";
			else $Resultado.="<input type='text' name='numero_voucher_$A->id' id='numero_voucher_$A->id' class='numero' value='' size='10' maxlength='10'>
				<a style='cursor:pointer' onclick='guardar_numero_voucher($A->id);'><img src='gifs/standar/Next.png' border='0'></a>";
			$Resultado.="<br>
				Valor solicitado: <b>".coma_format($A->valor)."</b><br>
				Fecha Solicitud: <b>$A->fecha_solicitud</b><br>
				Solicitado por: <b>$A->solicitado_por</b><br>
				Procesado por: <b>$A->procesado_por</b><br>
				</td><td valign='top'><b>INFORMACION PARA LA DEVOLUCION</b><br>
				Banco: <b>$A->ndevol_banco</b><br>
				Número de cuenta: <b>$A->devol_cuenta_banco</b><br>
				A nombre de: <b>$A->devol_ncuenta</b><br>
				Identificación: <b>*******".r($A->identificacion_devol,5)."</b></br>
				Tipo de cuenta: <b>".($A->devol_tipo_cuenta=='A'?'Ahorros':'Corriente')."</b><br>
				</td></tr></table>
				<br><br>";
		}
	}

	html('CARGA DE IMAGENES');
	$Baseurl=strlen('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/');

	echo "<script language='javascript'>
		var CAMPO=0;
		function asignar_imagen(cmp,dato)
		{
			if(cmp==1) var Campo='img_cedula_f';
			if(cmp==2) var Campo='img_pase_f';
			if(cmp==3) var Campo='adicional1_f';
			if(cmp==4) var Campo='adicional2_f';
			if(cmp==5) var Campo='adicional3_f';
			if(cmp==6) var Campo='adicional4_f';
			window.open('zautorizaciones.php?Acc=asignar_imagen&id=$id&imagen='+dato+'&Campo='+Campo+'&Foto='+cmp,'Oculto_ci');
		}
		function guardar_numero_voucher(id)
		{
			var Dato=document.getElementById('numero_voucher_'+id).value;
			window.open('zautorizaciones.php?Acc=guardar_numero_voucher&id='+id+'&d='+Dato,'Oculto_ci');
		}
		</script>
		<body><script language='javascript'>centrar(950,650);</script><iframe name='Oculto_ci' id='Oculto_ci' style='visibility:hidden' width=1 height=1></iframe>
		<h3>Carga de Imágenes del siniestro número $Sin->numero $Sin->asegurado_nombre</h3>
		$Resultado
		<table align='center' bgcolor='dddddd'>
		<tr><td valign='top'>
			<table cellspacing='2' bgcolor='dddddd'>
				<tr><td><img id='foto1' src='".($Sin->img_cedula_f?$Sin->img_cedula_f:"gifs/standar/img_neutra.png")."' height='70' onclick='webcam.freeze();do_upload(1);'></td><td > Foto 1</td></tr>
				<tr><td><img id='foto2' src='".($Sin->img_pase_f?$Sin->img_pase_f:"gifs/standar/img_neutra.png")."' height='70' onclick='webcam.freeze();do_upload(2);'></td><td > Foto 2</td></tr>
				<tr><td><img id='foto3' src='".($Sin->adicional1_f?$Sin->adicional1_f:"gifs/standar/img_neutra.png")."' height='70' onclick='webcam.freeze();do_upload(3);'></td><td > Foto 3</td></tr>
				<tr><td><img id='foto4' src='".($Sin->adicional2_f?$Sin->adicional2_f:"gifs/standar/img_neutra.png")."' height='70' onclick='webcam.freeze();do_upload(4);'></td><td > Foto 4</td></tr>
				<tr><td><img id='foto5' src='".($Sin->adicional3_f?$Sin->adicional3_f:"gifs/standar/img_neutra.png")."' height='70' onclick='webcam.freeze();do_upload(5);'></td><td > Foto 5</td></tr>
				<tr><td><img id='foto6' src='".($Sin->adicional4_f?$Sin->adicional4_f:"gifs/standar/img_neutra.png")."' height='70' onclick='webcam.freeze();do_upload(6);'></td><td > Foto 6</td></tr>
			</table>
		</td>
		<td valign=top align='right'>
				<!-- inicio de las rutinas de la toma de imagen -->
				<script type='text/javascript' src='inc/js/webcam.js'></script>
				<script language='JavaScript'>
					webcam.set_api_url( 'zautorizaciones.php?Acc=carga_imagen' );
					webcam.set_quality( 100 ); // JPEG quality (1 - 100)
					webcam.set_shutter_sound( false ); // play shutter click sound
				</script>
				<script language='JavaScript'>
					document.write( webcam.get_html(800, 500) );
				</script>
				<form name='forma2' id='forma2'>
					<input type=button id='bt1' value='Configuración' onClick='webcam.configure()'>
				<!--	&nbsp;&nbsp;
				  <input type=button id='bt2' value='Disparo' onClick='webcam.freeze()' >
					&nbsp;&nbsp;
					<input type=button id='bt3' value='Guardar' onClick='do_upload()' >
					&nbsp;&nbsp;
					<input type=button id='bt4' value='Re-iniciar' onClick='webcam.reset()'>
					-->
				</form>
				<script language='JavaScript'>
					webcam.set_hook( 'onComplete', 'my_completion_handler' );
					function do_upload(campo)
					{
						CAMPO=campo;
						// upload to server
						webcam.upload();
					}
					function my_completion_handler(msg)
					{
						// extract URL out of PHP output
						if (msg.match(/(http\:\/\/\S+)/))
						{
							var image_url = RegExp.$1;
							direccion_imagen=image_url.substr($Baseurl);
							asignar_imagen(CAMPO,direccion_imagen);
							// reset camera for another shot
							webcam.reset();
						}
						else alert('PHP Error: ' + msg);
					}
				</script>
		</td></tr></table>
		</body>";

}

function carga_imagen()
{
	$filename = 'planos/'.date('YmdHis') . '.jpg';
	$result = file_put_contents( $filename, file_get_contents('php://input') );
	if (!$result)
	{
		print "ERROR: Failed to write data to $filename, check permissions";
		exit();
	}
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $filename;
	print "$url\n";
}

function asignar_imagen()
{
	global $id,$imagen,$Campo,$Foto;
	$Id=$id;
	$Camino='';
	$directorio='siniestro';
	$Tamano=600;
	$name=str_replace('planos/','',$imagen);
	$tmp_name=$imagen;
	//	global $Foto,$T,$Id,$C,$Tamano,$directorio;

	if(!is_dir($Camino.$directorio)) { mkdir($Camino.$directorio); 	chmod($Camino.$directorio, 0777); }
	$Subdirectorio=substr(str_pad($id,6,'0',STR_PAD_LEFT),0,3);
	if(!is_dir($Camino.$directorio.'/'.$Subdirectorio)) { mkdir($Camino.$directorio.'/'.$Subdirectorio); chmod($Camino.$directorio.'/'.$Subdirectorio, 0777);}
	if(!is_dir($Camino.$directorio.'/'.$Subdirectorio.'/'.$id)) { mkdir($Camino.$directorio.'/'.$Subdirectorio.'/'.$id); chmod($Camino.$directorio.'/'.$Subdirectorio.'/'.$id, 0777);}
	$Caracteristicas_imagen = getimagesize($imagen);
	if($Caracteristicas_imagen[1]>$Tamano)	picresize($tmp_name,$Tamano,'jpg');
	$File_destino=$Camino.$directorio.'/'.$Subdirectorio.'/'.$id.'/'.strtolower(str_replace(' ','_',$name));
	$i = 1;
	// pick unused file name
	while (file_exists($File_destino))
	{
		$name=ereg_replace('(.*)(\.[a-zA-Z]+)$', '\1_'.$i.'\2', $name);
		$File_destino = $Camino.$directorio.'/'.$Subdirectorio.'/'.$id.'/'.strtolower(str_replace(' ','_',$name));
		$i++;
	}
	if(is_file($File_destino)) @unlink($File_destino);
	if (!@copy($tmp_name, $File_destino)) { die('error en copy file'); }
	@unlink($tmp_name);
	// Guardamos todo en la base de datos
	require('inc/link.php');
	$Destino_final=str_replace($Camino,'',$File_destino);
	$Tabla='aoacol_aoacars.siniestro';
	//$Campo='img_cedula_f';
	mysql_query("update $Tabla set $Campo='$Destino_final' where id=$id ",$LINK);
	$Tabla='aoacol_aoacars.app_bitacora';
	mysql_query("insert into $Tabla (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
		values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "',
		'" . date('s') . "','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','siniestro','M','$id','" . $_SERVER['REMOTE_ADDR'] . "','Modifica:img_cedula_f ingresa imagen')",$LINK);
	mysql_close($LINK);
	echo "<body><script language='javascript'>parent.document.getElementById('foto$Foto').src='".DIM."$Destino_final';</script>";
}

function asignar_pagare()
{
	global $id;  // id de la autorizacion
	$Autorizacion=qo("select * from sin_autor where id=$id");
	$Siniestro=qo("select * from siniestro where id=$Autorizacion->siniestro");
	$Aseguradora=qo("select * from aseguradora where id=$Siniestro->aseguradora");
	$Cliente=qo("select * from cliente where identificacion=$Autorizacion->identificacion");
	$Oficina=qo("select * from oficina where ciudad=$Siniestro->ciudad");
	$Pre_existe=qo("select id,consecutivo from pagare where autorizacion=$id and anulado=0");
	html();
	echo "<script language='javascript'>
			function asigna_pagare1(dato)
			{
				var Dato=document.getElementById('consecutivo');
				if(Dato.value)
				{
					if(Number(Dato.value))
					{
						var Consecutivo=Dato.value;
						while(Consecutivo.length<10) Consecutivo='0'+Consecutivo;
						Consecutivo=dato+Consecutivo;
						window.open('zautorizaciones.php?Acc=asignar_numero_pagare&id=$id&Consecutivo='+Consecutivo,'_self');
						return true;
					}
					else
					{
						alert('Debe digitar un numero consecutivo correctamente');
					}
				}
				else
				{
					alert('Debe digitar un numero consecutivo correctamente');
				}
				return false;
			}
			function asigna_pagare(dato)
			{
				window.open('zautorizaciones.php?Acc=asignar_numero_pagare&id=$id&Consecutivo='+dato,'_self');
			}
			function imprimir_pagare(id)
			{
				modal('zautorizaciones.php?Acc=imprimir_pagare&id='+id,0,0,500,700,'pagare');
				document.getElementById('mod').style.visibility='visible';
			}
			function cambio_imagen_std(Campo,ruta,tamrecimg,id)
			{
				window.open('marcoindex.php?Acc=reg_sube_img&T=pagare&Id='+id+'&C='+Campo+'&tri='+tamrecimg+'&ruta='+ruta,'simg_'+Campo);
				document.getElementById('cerrar').style.visibility='visible';
				document.getElementById('lapiz_cambio').style.visibility='hidden';
			}
			function autorizar_servicio(id)
			{
				window.open('zautorizaciones.php?Acc=autorizar_pagare&autorizacion=$id&pagare='+id,'Oculto_pagare');
			}
			function asignar_foto_imagen_pagare(dato,pagare)
			{
				window.open('zautorizaciones.php?Acc=asignar_imagen_pagare&id='+pagare+'&imagen='+dato+'&Campo=imagen_f','Oculto_pagare');
			}
			function cerrar_ventana()
			{
				parent.cerrar_ventana();
			}
		</script>
		<body><script language='javascript'>parent.centrar(800,800);</script>

		<iframe name='Oculto_pagare' id='Oculto_pagare' style='visibility:hidden' height=1 width=1></iframe>
		<h3>ASIGNACION DE PAGARE</H3>
		<b>Oficina: $Oficina->nombre Cliente: $Cliente->nombre $Cliente->apellido Identificación: ".coma_format($Cliente->identificacion)."<br />
		Siniestro: $Siniestro->numero $Aseguradora->nombre</b>";
	if($Pre_existe)
	{
		echo "<hr><h3 style='color:blue'>PAGARE NUMERO $Pre_existe->consecutivo ASIGNADO</h3>
		<input type='button' value='IMPRIMIR PAGARE' onclick='imprimir_pagare($Pre_existe->id);'>";
		echo "<form action='zautorizaciones.php' method='post' target='_self' name='mod' id='mod' style='visibility:hidden'>
			<input type='hidden' name='Acc' value='subir_imagen_ok'>
			<table border=0 cellspacing=0 cellpadding=0><tr><td bgcolor='eeeeee'><h3 align='center'>Carga de Imagen Escaneada</h3>";
		$Ancho=280;	$Alto=250; $Info=''; $Sub_Contenido=substr($Info,strrpos($Info,'/')+1);$Sub_Tumb='tumb_'.$Sub_Contenido;$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$Info);
		if(!file_exists($Tumb) && file_exists($Info))
		{
			if(strpos(strtolower($Sub_Contenido),'.jpg')) picresize($Info,TUMB_SIZE,'jpg',$Tumb);
			if(strpos(strtolower($Sub_Contenido),'.gif')) picresize($Info,TUMB_SIZE,'gif',$Tumb);
			if(strpos(strtolower($Sub_Contenido),'.png')) picresize($Info,TUMB_SIZE,'png',$Tumb);
		}
		$Baseurl=strlen('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/');
		echo "<iframe id='simg_imagen_f' name='simg_imagen_f' src='$Tumb' height='$Alto' width='$Ancho' frameborder='0' ></iframe></td><td valign='top' bgcolor='efefef'>
					<a style='cursor:pointer;' class='info' onclick=\"cambio_imagen_std('imagen_f','pagare',1000,$Pre_existe->id);\" id='lapiz_cambio'><img src='gifs/standar/Pencil.png' border='0'>
					<span style='width:100px'>Cambiar la imagen</span></a><br><br></td>
					<td >
					<h3 align='center'>Toma de Imagen con cámara Web</h3>
					<!-- inicio de las rutinas de la toma de imagen -->
						<script type='text/javascript' src='inc/js/webcam.js'></script>
						<script language='JavaScript'>
							webcam.set_api_url( 'zautorizaciones.php?Acc=carga_imagen_pagare' );
							webcam.set_quality( 100 ); // JPEG quality (1 - 100)
							webcam.set_shutter_sound( false ); // play shutter click sound
						</script>
						<script language='JavaScript'>
							document.write( webcam.get_html(800, 500) );
						</script>
						<form name='forma2' id='forma2'>
							<input type=button id='bt1' value='Configuración' onClick='webcam.configure()'>
							&nbsp;&nbsp;
							 <input type=button id='bt2' value='Disparo' onClick='webcam.freeze();do_upload();' >
						<!--	&nbsp;&nbsp;
							<input type=button id='bt3' value='Guardar' onClick='do_upload()' >
							&nbsp;&nbsp;
							<input type=button id='bt4' value='Re-iniciar' onClick='webcam.reset()'>
							-->
						</form>
						<script language='JavaScript'>
							webcam.set_hook( 'onComplete', 'my_completion_handler' );
							function do_upload()
							{
								webcam.upload();
							}
							function my_completion_handler(msg)
							{
								// extract URL out of PHP output
								if (msg.match(/(http\:\/\/\S+)/))
								{
									var image_url = RegExp.$1;
									direccion_imagen=image_url.substr($Baseurl);
									asignar_foto_imagen_pagare(direccion_imagen,$Pre_existe->id);
									// reset camera for another shot
									webcam.reset();
								}
								else alert('PHP Error: ' + msg);
							}
						</script>
					</td></tr></table>";
		if($_SESSION['User']==1)	echo "<input type='text' name='imagen_f' id='imagen_f' value='$Info' size='20'>";
		else echo "<input type='hidden' name='imagen_f' id='imagen_f' value='$Info'>";
		echo "<br /><br /><input type='button' id='cerrar' name='cerrar' style='visibility:hidden;font-size:14px;' value=' CLICK AQUI PARA AUTORIZAR EL SERVICIO ' onclick='autorizar_servicio($Pre_existe->id);'>
				</form>";
		die();
	}
	if($Oficina->genera_pagare)
	{
		$Consecutivo=qo1("select max(substr(consecutivo,4,10)) from pagare where oficina=$Oficina->id ")+1;
		$Consec=$Oficina->sigla.str_pad($Consecutivo,10,'0',STR_PAD_LEFT);
		echo "<br /><br /><b>Nuevo consecutivo: <font style='color:880000;font-size:14'x'>$Consec</font><br /><br />
		<a onclick=\"modal('http://www.runt.com.co:7777/runt/ciudadanos/consultas/consulta_ciudadano_por_documento_final_public.jsf',100,100,600,800,'consulta');\"
                     style='cursor:pointer;color:blue;' class='info'>Consulta de estado de licencia de conducción RUNT<span style='width:200px'>Click para consultar el estado de la licencia de conducción en RUNT</span></a><br />
         <a onclick=\"modal('http://web.mintransporte.gov.co/Consultas/transito/Consulta23122010.htm',100,100,600,800,'consulta');\"
                     style='cursor:pointer;color:blue;' class='info'>Consulta de estado de licencia de conducción MinTransporte<span style='width:200px'>Click para consultar el estado de la licencia de conducción en MINTRANSPORTE</span></a><br />
                     <br />
         Marque si la licencia de conducción ha sido verificada correctamente : <input type='checkbox' name='estado_licencia'
                     onclick=\" document.getElementById('btn_asigna_pagare').style.visibility='visible';\"><br /><br />
		<input type='button' name='btn_asigna_pagare' id='btn_asigna_pagare' value='ASIGNAR PAGARE' onclick=\"asigna_pagare('$Consec');\" style='visibility:hidden;font-size:14px;'>";
	}
	else
	{
		echo "<br /><br />Por favor digite el consecutivo del pagaré: <input type='text' name='consecutivo' name='consecutivo' id='consecutivo' class='numero' size='10' maxlength='10'>
		<input type='button' value='Asignar Pagaré' onclick=\"asigna_pagare1('$Oficina->sigla');\">";
	}
}

function carga_imagen_pagare()
{
	$filename = 'planos/'.date('YmdHis') . '.jpg';
	$result = file_put_contents( $filename, file_get_contents('php://input') );
	if (!$result)
	{
		print "ERROR: Failed to write data to $filename, check permissions";
		exit();
	}
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $filename;
	print "$url\n";
}

function asignar_imagen_pagare()
{
	global $id,$imagen,$Campo;
	$Id=$id;
	$Camino='';
	$directorio='pagare';
	$Tamano=1000;
	$name=str_replace('planos/','',$imagen);
	$tmp_name=$imagen;
	if(!is_dir($Camino.$directorio)) { mkdir($Camino.$directorio); 	chmod($Camino.$directorio, 0777); }
	$Subdirectorio=substr(str_pad($id,6,'0',STR_PAD_LEFT),0,3);
	if(!is_dir($Camino.$directorio.'/'.$Subdirectorio)) { mkdir($Camino.$directorio.'/'.$Subdirectorio); chmod($Camino.$directorio.'/'.$Subdirectorio, 0777);}
	if(!is_dir($Camino.$directorio.'/'.$Subdirectorio.'/'.$id)) { mkdir($Camino.$directorio.'/'.$Subdirectorio.'/'.$id); chmod($Camino.$directorio.'/'.$Subdirectorio.'/'.$id, 0777);}
	$Caracteristicas_imagen = getimagesize($imagen);
	if($Caracteristicas_imagen[1]>$Tamano)	picresize($tmp_name,$Tamano,'jpg');
	$File_destino=$Camino.$directorio.'/'.$Subdirectorio.'/'.$id.'/'.strtolower(str_replace(' ','_',$name));
	$i = 1;
	// pick unused file name
	while (file_exists($File_destino))
	{
		$name=ereg_replace('(.*)(\.[a-zA-Z]+)$', '\1_'.$i.'\2', $name);
		$File_destino = $Camino.$directorio.'/'.$Subdirectorio.'/'.$id.'/'.strtolower(str_replace(' ','_',$name));
		$i++;
	}
	if(is_file($File_destino)) @unlink($File_destino);
	if (!@copy($tmp_name, $File_destino)) { die('error en copy file'); }
	@unlink($tmp_name);
	// Guardamos todo en la base de datos
	require('inc/link.php');
	$Destino_final=str_replace($Camino,'',$File_destino);
	$Tabla='aoacol_aoacars.pagare';
	//$Campo='img_cedula_f';
	mysql_query("update $Tabla set $Campo='$Destino_final' where id=$id ",$LINK);
	$Tabla='aoacol_aoacars.app_bitacora';
	mysql_query("insert into $Tabla (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
		values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "',
		'" . date('s') . "','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','pagare','M','$id','" . $_SERVER['REMOTE_ADDR'] . "','Modifica:imagen_f ingresa imagen')",$LINK);
	mysql_close($LINK);
	echo "<body><script language='javascript'>
		parent.document.getElementById('simg_imagen_f').src='".DIM."$Destino_final';parent.document.getElementById('cerrar').style.visibility='visible';</script>";
}

function asignar_numero_pagare()
{
	global $Consecutivo,$id;
	$Autorizacion=qo("select * from sin_autor where id=$id");
	$Siniestro=qo("select * from siniestro where id=$Autorizacion->siniestro");
	$Aseguradora=qo("select * from aseguradora where id=$Siniestro->aseguradora");
	$Cliente=qo("select * from cliente where identificacion=$Autorizacion->identificacion");
	$Oficina=qo("select * from oficina where ciudad=$Siniestro->ciudad");
	// VERIFICACION PREVIA SI EL CONSECUTIVO YA FUE ASIGNADO PREVIAMENTE
	$Hoy=date('Y-m-d');
	if($Previo=qo("select * from pagare where consecutivo='$Consecutivo'"))
	{
		if($Previo->siniestro && $Previo->anulado)
		{
			echo "<body><script language='javascript'>alert('El consecutivo $Consecutivo fue asignado a otra autorización y fue anulado');</script></body>";die();
		}
		if($Previo->siniestro)
		{
			echo "<body><script language='javascript'>alert('El consecutivo $Consecutivo ya está asignado a otra autorización');</script></body>";die();
		}
		q("update pagare set siniestro='$Siniestro->id', fecha='$Hoy',cliente='$Cliente->id',autorizacion='$id' where id=$Previo->id");
		graba_bitacora('pagare','M',$Previo->id,"Autoriza pagare");
		echo "<body><script language='javascript'>window.open('zautorizaciones.php?Acc=asignar_pagare&id=$id','_self');</script></body>";
		die();
	}
	html();
	$Codigo=round(rand(100000,999999),0);
	$Nid=q("insert into pagare (siniestro,consecutivo,oficina,fecha,cliente,autorizacion,codigo) values
		('$Siniestro->id','$Consecutivo','$Oficina->id','$Hoy','$Cliente->id','$id','$Codigo')");
	graba_bitacora('pagare','A',$Nid);
	echo "<body><script language='javascript'>window.open('zautorizaciones.php?Acc=asignar_pagare&id=$id','_self');</script></body>";
}

function imprimir_pagare()
{
	global $id;
	$Pagare=qo("select * from pagare where id=$id");
	$Cliente=qo("select * from cliente where id=$Pagare->cliente");
	$Siniestro=qo("select * from siniestro where id=$Pagare->siniestro");
	$Oficina=qo("select * from oficina where id=$Pagare->oficina");
	$Autorizacion=qo("select * from sin_autor where id=$Pagare->autorizacion");
	$Hoy=fecha_completa(date('Y-m-d'));
	include('inc/pdf/fpdf.php');
	$P=new pdf('P','mm','letter');
	$P->AddFont("c128a","","c128a.php");
	$P->AliasNbPages();
	$P->setTitle("PAGARE");
	$P->setAuthor("Arturo Quintero www.aoacolombia.com arturoquintero@aoacolombia.com");
	$P->Numeracion=false;
	$P->SetAutoPageBreak(false);
	$P->setFillColor(250,250,250);
	//	$P->Header_texto='';
	//	$P->Header_alineacion='L';
	//	$P->Header_alto='8';
	$P->SetTopMargin('5');
	//	$P->Header_colores=array(0,0,0,255,255,255,50,50,100); # rgb texto, rgb fondo, rgb borde
	//	$P->Header_imagen='img/cnota_entrada.jpg';
	///	$P->Header_posicion_imagen=array(20,5,80,14);
	$P->AddPage('P');
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$ny=10;
	$P->Image('../img/LOGO_AOA_200.jpg',10,$ny,25,12);
	$P->SetTextColor(0,0,0);$P->setxy(40,$ny);$P->setfont('Arial','B',14);$P->cell(47,10,'PAGARE NUMERO ');
	$P->settextcolor(150,0,0);$P->cell(10,10,$Pagare->consecutivo);
	$P->settextcolor(0,0,0);$P->setxy(132,$ny);
	$P->SetFont("c128a","",10);$P->cell(70,12, uccean128('FA'.str_pad($Pagare->consecutivo,14,'0',STR_PAD_LEFT).str_pad($Pagare->codigo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$ny+=16;
	$P->setxy(10,$ny);$P->setfont('Arial','',10);
	$P->multicell(190,5,"Yo _____________________________________________________, mayor de edad, identificado(a) como aparece al pie de mi firma, actuando en nombre propio, por medio del presente escrito ".
	"manifiesto, lo siguiente: PRIMERO: Que debo y pagaré, incondicional y solidariamente a la orden de ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A. Nit. 900.174.552-5 o a la persona natural o ".
	"jurídica a quien el mencionado acreedor ceda o endose sus derechos sobre este pagaré, la suma cierta de _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _".
	"PESOS MCTE. ($ _________________,oo), pesos moneda legal colombiana. SEGUNDO: Que el pago total de la mencionada obligación se efectuará en un sólo contado, el día _____ del mes de  _______________ ".
	"del año _______ en las dependencias de ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A. ubicada en la ciudad de $Oficina->nombre. TERCERO: Que en caso de mora pagaré a ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A. o a la persona natural ".
	"o jurídica a quien el mencionado acreedor ceda o endose sus derechos, intereses de mora a la más alta tasa permitida por la Ley, desde el día siguiente a la fecha de exigibilidad del presente pagaré, y hasta cuando ".
	"su pago total se efectúe. CUARTO: Expresamente declaro excusado el protesto del presente pagaré y los requerimientos judiciales o extrajudiciales para la constitución en mora. QUINTO: En caso de que haya lugar ".
	"al recaudo judicial o extrajudicial de la obligación contenida en el presente título valor será a mi cargo las costas judiciales y/o los honorarios que se causen por tal razón. SEXTO: El tenedor del presente pagaré ".
	"podrá declarar vencidos la totalidad de los plazos de esta obligación o de las cuotas que constituyan el saldo de lo debido y exigir su pago inmediato ya sea judicial o extrajudicialmente en los siguientes casos: ",0,'J',0);
	$ny=$P->y+5;
	$P->setxy(10,$ny);
	$P->multicell(190,5,"1. Cuando EL DEUDOR incumpla una cualquiera de las obligaciones derivadas del presente contrato, así sea de manera parcial.");
	$ny=$P->y+5;
	$P->setxy(10,$ny);
	$P->cell(190,5,"2. Por muerte del DEUDOR.");
	$ny+=10;
	$P->setxy(10,$ny);
	$P->multicell(190,5,"3. Cuando el DEUDOR se declare en procedo de liquidación obligatoria o convoque a concurso de acreedores.");
	$ny=$P->y+10;
	$P->setxy(10,$ny);
	$P->multicell(190,5,"En constancia de lo anterior, se suscribe en la ciudad de $Oficina->nombre, a los ".date('d')." días del mes de ".mes(date('m'))." del año ".date('Y').".");
	$ny=$P->y+10;
	$P->setxy(10,$ny);
	$P->cell(190,5,"EL DEUDOR,");
	$ny+=20;
	$P->setxy(10,$ny);
	$P->cell(190,5,"Firma:           ________________________");
	$ny+=8;
	$P->setxy(10,$ny);
	$P->cell(190,5,"Nombre:        ________________________");
	$P->rect(120,$ny-20,30,20);$P->setxy(130,$ny);$P->cell(10,5,'Huella');
	$ny+=8;
	$P->setxy(10,$ny);
	$P->cell(190,5,"C.C. No:        ________________________");
	$ny+=8;
	$P->setxy(10,$ny);
	$P->cell(190,5,"Domiciliado(a) en: Dirección: _________________________________   Ciudad: _______________");



	$P->AddPage('P');
	$ny=10;
	$P->Image('../img/LOGO_AOA_200.jpg',10,$ny,25,12);
	$P->SetTextColor(0,0,0);$P->setxy(40,$ny);$P->setfont('Arial','B',14);$P->cell(47,10,'PAGARE NUMERO ');
	$P->settextcolor(150,0,0);$P->cell(10,10,$Pagare->consecutivo);
	$P->settextcolor(0,0,0);$P->setxy(132,$ny);
	$P->SetFont("c128a","",10);$P->cell(70,12, uccean128('FA'.str_pad($Pagare->consecutivo,14,'0',STR_PAD_LEFT).str_pad($Pagare->codigo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$ny+=16;
	$P->setxy(10,$ny);$P->setfont('Arial','B',10);
	$P->cell(190,5,"Señores");
	$ny+=5;
	$P->setxy(10,$ny);
	$P->cell(190,5,"ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.");
	$ny+=5;
	$P->setxy(10,$ny);
	$P->cell(190,5,"La Ciudad.");
	$ny+=10;
	$P->setxy(10,$ny);
	$P->multicell(190,5,"REFERENCIA: AUTORIZACION PARA LLENAR ESPACIOS EN BLANCO DEL PAGARE NUMERO. $Pagare->consecutivo ");
	$ny+=16;
	$P->setxy(10,$ny);$P->setfont('Arial','',10);
	$P->multicell(190,5,"Yo __________________________________________________________ mayor de edad, identificado(a) como aparece al pie de mi firma, actuando en nombre propio, por medio del presente ".
	"escrito manifiesto que le faculto a usted, de manera permanente e irrevocable para que, en caso de incumplimiento en el pago oportuno de alguna de las obligaciones que hemos adquirido con usted, derivadas de ".
	"los negocios comerciales y contractuales bien sean verbales o escritos; sin previo aviso, proceda a llenar los espacios en blanco del pagaré No.$Pagare->consecutivo, que he suscrito en la fecha a su favor y que ".
	"se anexa, con el fin de convertir el pagaré, en un documento que presta mérito ejecutivo y que está sujeto a los parámetros legales del Artículo 622 del Código de Comercio. ",0,'J',0);
	$ny=$P->y+5;
	$P->setxy(10,$ny);
	$P->multicell(190,5,"1. El espacio correspondiente a “la suma cierta de” se llenará por una suma igual a la que resulte pendiente de pago de todas la obligaciones contraídas con el acreedor, por concepto de capital, ".
	"intereses, seguros, cobranza extrajudicial, según la contabilidad del acreedor a la fecha en que sea llenado el pagare.",0,'J',0);
	$ny=$P->y+5;
	$P->setxy(10,$ny);
	$P->multicell(190,5,"2. El espacio correspondiente a la fecha en que se debe hacer el pago, se llenará con la fecha correspondiente al día en que sea llenado el pagaré, fecha que se entiende que es la de su vencimiento.",0,'J',0);
	$ny=$P->y+5;
	$P->setxy(10,$ny);
	$P->multicell(190,5,"En constancia de lo anterior firmamos la presente autorización en la ciudad de $Oficina->nombre, a los ".date('d')." días del mes de ".mes(date('m'))." del año ".date('Y').".",0,'J',0);
	$ny=$P->y+10;
	$P->setxy(10,$ny);
	$P->cell(190,5,"EL DEUDOR,");
	$ny+=20;
	$P->setxy(10,$ny);
	$P->cell(190,5,"Firma:           ________________________");
	$ny+=8;
	$P->setxy(10,$ny);
	$P->cell(190,5,"Nombre:        ________________________");
	$P->rect(120,$ny-20,30,20);$P->setxy(130,$ny);$P->cell(10,5,'Huella');
	$ny+=8;
	$P->setxy(10,$ny);
	$P->cell(190,5,"C.C. No:        ________________________");
	$ny+=8;
	$P->setxy(10,$ny);
	$P->cell(190,5,"Domiciliado(a) en: Dirección: _________________________________   Ciudad: _______________");


	$P->Output($Archivo);
}

function imprimir_pagare_old()
{
	global $id;
	$Pagare=qo("select * from pagare where id=$id");
	$Cliente=qo("select * from cliente where id=$Pagare->cliente");
	$Siniestro=qo("select * from siniestro where id=$Pagare->siniestro");
	$Oficina=qo("select * from oficina where id=$Pagare->oficina");
	$Autorizacion=qo("select * from sin_autor where id=$Pagare->autorizacion");
	$Hoy=fecha_completa(date('Y-m-d'));
	include('inc/pdf/fpdf.php');
	$P=new pdf('P','mm','letter');
	$P->AddFont("c128a","","c128a.php");
	$P->AliasNbPages();
	$P->setTitle("PAGARE");
	$P->setAuthor("Arturo Quintero www.aoacolombia.com arturoquintero@aoacolombia.com");
	$P->Numeracion=false;
	$P->SetAutoPageBreak(false);
	$P->setFillColor(250,250,250);
	//	$P->Header_texto='';
	//	$P->Header_alineacion='L';
	//	$P->Header_alto='8';
	$P->SetTopMargin('5');
	//	$P->Header_colores=array(0,0,0,255,255,255,50,50,100); # rgb texto, rgb fondo, rgb borde
	//	$P->Header_imagen='img/cnota_entrada.jpg';
	///	$P->Header_posicion_imagen=array(20,5,80,14);
	$P->AddPage('P');
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$ny=5;
	$P->Image('../img/LOGO_AOA_200.jpg',5,$ny,30,12);
	$P->SetTextColor(0,0,0);$P->setxy(40,$ny);$P->setfont('Arial','B',14);$P->cell(47,10,'PAGARE NUMERO ');
	$P->settextcolor(150,0,0);$P->cell(10,10,$Pagare->consecutivo);
	$P->settextcolor(0,0,0);$P->setxy(132,$ny);
	$P->SetFont("c128a","",10);$P->cell(70,12, uccean128('FA'.str_pad($Pagare->consecutivo,14,'0',STR_PAD_LEFT).str_pad($Pagare->codigo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$ny+=16;
	$P->setxy(5,$ny);$P->setfont('Arial','B',10);$P->cell(80,5,"Lugar y fecha de suscripción: ".($Siniestro?"$Oficina->nombre - $Hoy ":"$Oficina->nombre  dia ___ del mes ___________________ del año: _______"));
	$ny+=5;
	$P->setxy(5,$ny);$P->cell(100,5,'Valor: _______________________________________________________________________________________________');
	$ny+=5;
	$P->setxy(5,$ny);$P->cell(84,5,'Persona a quien debe hacerse el pago (Acreedor): ');$P->setfont('Arial','',10);$P->cell(80,5,'ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A. Nit.900.174.552-5.');
	$ny+=5;$P->setfont('Arial','B',10);
	$P->setxy(5,$ny);$P->cell(80,5,'Ciudad y dirección donde se efectuará el pago: ');$P->setfont('Arial','',10);$P->cell(80,5,"$Oficina->nombre $Oficina->direccion");
	$ny+=5;
	$P->setxy(5,$ny);$P->cell(80,5,"Barrio: $Oficina->barrio - Teléfono: $Oficina->telefono ");
	$ny+=5;$P->setfont('Arial','B',10);
	$P->setxy(5,$ny);$P->cell(80,5,($Cliente?str_pad("Deudor: $Cliente->nombre $Cliente->apellido  ID: ".coma_format($Cliente->identificacion).".",120,'- ',STR_PAD_RIGHT):"Deudor: _____________________________________________________________________________________________"));
	$ny+=5;$P->setfont('Arial','',10);
	$P->setxy(5,$ny); $P->multicell(200,5,"DECLARO: PRIMERA.- OBJETO: Que por virtud del presente título valor pagaré incondicionalmente a la orden de ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A. ".
		"con NIT 900.174.552-5 o a quien represente sus derechos, en la ciudad y dirección indicados la suma de: _________________________________________________________________________ ".
		" ($ _________________), mas los intereses señalados en la Cláusula segunda de este documento según lo establecido en los términos y condiciones del servicio. ".
		"SEGUNDA.- INTERESES: Que sobre la suma debida se reconocerán Intereses Moratorios a la tasa máxima legal vigente. TERCERA.- PLAZO: Que pagaré el capital indicado en la cláusula primera de este ".
	    "pagaré el día _____________________________________________________________________.",0,'J',0);
	$ny=$P->y;
	$P->setxy(5,$ny); $P->multicell(200,5,"En constancia de lo anterior, se suscribe este documento en la ciudad de $Oficina->nombre el día ".($Siniestro?"$Hoy.":" _____ del mes ______________________ del año ______."),0,'J',0);
	$ny+=20; $P->setxy(5,$ny);$P->cell(80,5,"________________________________________________");
	$ny+=5;$P->setxy(5,$ny); $P->cell(80,5,"DEUDOR");
	$ny+=5; $P->setxy(5,$ny);$P->cell(80,5,"C.C.:");$P->setxy(130,$ny);$P->cell(10,5,'Huella');$P->rect(120,$ny-20,30,20);
	if($Oficina->fdatos_pagare) // si la oficina no tiene impresora, se imprime el formulario de datos para el pagare.
	{
		$ny+=40;$P->setxy(5,$ny);$P->setfont('Arial','B',10);$P->cell(200,5,"DATOS DEL ASEGURADO/AUTORIZADO");$P->setfont('Arial','',10);
		$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"SINIESTRO NUMERO : ".($Siniestro?$Siniestro->numero:"_________________________")."                   FECHA: $Hoy." );
		$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"IDENTIFICACION: ___________________________________________     TIPO: __CC   __CE  __NIT  __PSP  __TI ");
		$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"LUGAR DE EXPEDICION DE LA IDENTIFICACION: ___________________________________________________________");
		$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"NOMBRES: ___________________________________________________________________________________________");
		$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"APELLIDOS: __________________________________________________________________________________________");
		$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"SEXO : __M   __F          PAIS: ___________________________        CIUDAD: _____________________________________");
		$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"DIRECCION DOMICILIO: ________________________________________________________________________________");
		$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"TELEFONO OFICINA: ____________________________     TELEFONO CASA: ____________________________________");
		$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"CELULAR: ____________________________________________________________________________________________");
		$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"EMAIL: _______________________________________________________________________________________________");
	}
	$P->Output($Archivo);
}

function valida_identificacion()
{
	global $id;
	echo "<body><script language='javascript'>";
	if($Ingreso=qo("select foto_f,nombre,apellido from aoacol_administra.ingreso_recepcion where identificacion='$id' order by id desc limit 1"))
	{
		echo "parent.document.forma.nombre.value='$Ingreso->nombre';
					parent.document.forma.apellido.value='$Ingreso->apellido';";
	}

	if($C=qo("select * from cliente where identificacion='$id' "))
	{
		$Nciudad=qo1("select concat(departamento,' - ',nombre) from ciudad where codigo='$C->ciudad' ");
		echo "
				with(parent.document.forma)
				{
					tipo_id.value='$C->tipo_id';nombre.value='$C->nombre';apellido.value='$C->apellido';lugar_expdoc.value='$C->lugar_expdoc';
					pais.value='$C->pais';ciudad.value='$C->ciudad';_ciudad.value='$Nciudad';direccion.value='$C->direccion';
					telefono_oficina.value='$C->telefono_oficina';telefono_casa.value='$C->telefono_casa';celular.value='$C->celular';
					email_e.value='$C->email_e';observaciones.value='$C->observaciones';sexo.value='$C->sexo';
				}";
		if($B=qo("select * from sin_autor where identificacion_devol='$id' "))
		{
			echo "
				with(parent.document.forma)
				{
					devol_cuenta_banco.value='$B->devol_cuenta_banco';
					devol_tipo_cuenta.value='$B->devol_tipo_cuenta';
					devol_banco.value='$B->devol_banco';
					devol_ncuenta.value='$B->devol_ncuenta';
					identificacion_devol.value='$B->identificacion_devol';
				}
			";
		}
	}
	else
	{
		echo "with(parent.document.forma)
					{
						tipo_id.value='';nombre.value='';apellido.value='';lugar_expdoc.value='';
						pais.value='CO';ciudad.value='';_ciudad.value='';direccion.value='';
						telefono_oficina.value='';telefono_casa.value='';celular.value='';
						email_e.value='';observaciones.value='';
					}
					parent.document.getElementById('nuevo_cliente').innerHTML='<b style=color:ff4444 >CLIENTE NUEVO</B>';";
	}
	echo "</script></body>";
}

function autorizar_pagare()
{
	global $autorizacion,$pagare,$USUARIO,$NUSUARIO;
	$Pagare=qo("select * from pagare where id=$pagare");
	$Autorizacion=qo("select * from sin_autor where id=$autorizacion");
	if($Pagare->imagen_f)
	{
		$num_autorizacion=r($Pagare->consecutivo,10);
		$Hoy=date('Y-m-d H:i:s');
		q("update sin_autor set funcionario='AOA $NUSUARIO',estado='A',num_autorizacion='$num_autorizacion',fecha_proceso='$Hoy',procesado_por='$NUSUARIO'
			 where id=$autorizacion ");
		echo "<body><script language='javascript'>alert('El servicio ha quedado Autorizado satisfactoriamente');parent.cerrar_ventana();</script></body>";
	}
	else
	{
		echo "<body><script language='javascript'>alert('No se ha cargado la imagen del pagaré firmado. No se puede autorizar aun este servicio');</script>";
	}
}

function generar_pagare_blanco()
{
	html('GENERACION DE PAGARES EN BLANCO');
	echo "<body>
		<form action='zautorizaciones.php' method='post' target='_self' name='forma' id='forma'>
			Seleccione la ciudad: ".menu1("Oficina","select id,nombre from oficina")."<br />
			Consecutivo inicial: <input type='text' name='inicial'><br />
			Consecutivo final: <input type='text' name='final'><br />
			<input type='hidden' name='Acc' value='generar_pagare_blanco_ok'>
			<input type='submit' value='Continuar'>
		</form>
	</body>";
}

function generar_pagare_blanco_ok()
{
	global $Oficina,$inicial,$final;
	html('GENERACION DE PAGARES EN BLANCO');
	echo "<body>";
	if($Oficina && $inicial && $final)
	{
		if($inicial <=$final)
		{
			$O=qo("select sigla from oficina where id=$Oficina");
			include('inc/link.php');
			for($i=$inicial;$i<=$final;$i++)
			{
				$Codigo=round(rand(100000,999999),0);
				$Consec=$O->sigla.str_pad($i,10,'0',STR_PAD_LEFT);
				mysql_query("insert ignore into pagare (consecutivo,oficina,codigo) values ('$Consec','$Oficina','$Codigo')",$LINK);
				echo "<br />Pagare: $Consec incluido.";
			}
			mysql_close($LINK);
		}
		else echo "<body>El consecutivo inicial $inicial no es menor o igual al final $final ";
	}
	else
	echo "La información es invalida.";
}

function imprimir_varios_pagares()
{
	global $id;
	$Oficina=qo1("select oficina from pagare where id=$id");
	html('IMPRESION MASIVA DE PAGARES');
	echo "<body><form action='zautorizaciones.php' method='post' target='_self' name='forma' id='forma'>
		Consecutivo Inicial: ".menu1("inicial","select consecutivo,consecutivo from pagare where oficina=$Oficina")."
		Final: ".menu1("final","select consecutivo,consecutivo from pagare where oficina=$Oficina")."<br /><br />
		<input type='submit' value='Continuar'><input type='hidden' name='Ofic' value='$Oficina'>
		<input type='hidden' name='Acc' value='imprimir_varios_pagares_ok'>
		</form>";
}

function imprimir_varios_pagares_ok()
{
	global $inicial,$final,$Ofic;
	$Oficina=qo("select * from oficina where id=$Ofic");
	if($Pagares=q("select * from pagare where consecutivo between '$inicial' and '$final' "))
	{
		include('inc/pdf/fpdf.php');
		$P=new pdf('P','mm','letter');
		$P->AddFont("c128a","","c128a.php");
		$P->AliasNbPages();
		$P->setTitle("PAGARE");
		$P->setAuthor("Arturo Quintero www.aoacolombia.com arturoquintero@aoacolombia.com");
		$P->Numeracion=false;
		$P->SetAutoPageBreak(false);
		$P->setFillColor(250,250,250);
		//	$P->Header_texto='';
		//	$P->Header_alineacion='L';
		//	$P->Header_alto='8';
		$P->SetTopMargin('5');
		//	$P->Header_colores=array(0,0,0,255,255,255,50,50,100); # rgb texto, rgb fondo, rgb borde
		//	$P->Header_imagen='img/cnota_entrada.jpg';
		///	$P->Header_posicion_imagen=array(20,5,80,14);
		include('inc/link.php');
		while($Pagare=mysql_fetch_object($Pagares))
		{
			$Cliente=qom("select * from cliente where id=$Pagare->cliente",$LINK);
			$Siniestro=qom("select * from siniestro where id=$Pagare->siniestro",$LINK);
			$Autorizacion=qom("select * from sin_autor where id=$Pagare->autorizacion",$LINK);
			$P->AddPage('P');
			//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
			$ny=5;
			$P->Image('../img/LOGO_AOA_200.jpg',5,$ny,30,12);
			$P->SetTextColor(0,0,0);$P->setxy(40,$ny);$P->setfont('Arial','B',14);$P->cell(47,10,'PAGARE NUMERO ');
			$P->settextcolor(150,0,0);$P->cell(10,10,$Pagare->consecutivo);
			$P->settextcolor(0,0,0);$P->setxy(132,$ny);
			$P->SetFont("c128a","",10);$P->cell(70,12, uccean128('FA'.str_pad($Pagare->consecutivo,14,'0',STR_PAD_LEFT).str_pad($Pagare->codigo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
			$ny+=16;
			$P->setxy(5,$ny);$P->setfont('Arial','B',10);$P->cell(80,5,"Lugar y fecha de suscripción: ".($Siniestro?"$Oficina->nombre - $Hoy ":"$Oficina->nombre  dia ___ del mes ___________________ del año: _______"));
			$ny+=5;
			$P->setxy(5,$ny);$P->cell(100,5,'Valor: _______________________________________________________________________________________________');
			$ny+=5;
			$P->setxy(5,$ny);$P->cell(84,5,'Persona a quien debe hacerse el pago (Acreedor): ');$P->setfont('Arial','',10);$P->cell(80,5,'ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A. Nit.900.174.552-5.');
			$ny+=5;$P->setfont('Arial','B',10);
			$P->setxy(5,$ny);$P->cell(80,5,'Ciudad y dirección donde se efectuará el pago: ');$P->setfont('Arial','',10);$P->cell(80,5,"$Oficina->nombre $Oficina->direccion");
			$ny+=5;
			$P->setxy(5,$ny);$P->cell(80,5,"Barrio: $Oficina->barrio - Teléfono: $Oficina->telefono ");
			$ny+=5;$P->setfont('Arial','B',10);
			$P->setxy(5,$ny);$P->cell(80,5,($Cliente?str_pad("Deudor: $Cliente->nombre $Cliente->apellido",120,'- ',STR_PAD_RIGHT):"Deudor: _____________________________________________________________________________________________"));
			$ny+=5;$P->setfont('Arial','',10);
			$P->setxy(5,$ny); $P->multicell(200,5,"DECLARO: PRIMERA.- OBJETO: Que por virtud del presente título valor pagaré incondicionalmente a la orden de ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A. ".
				"con NIT 900.174.552-5 o a quien represente sus derechos, en la ciudad y dirección indicados la suma de: _________________________________________________________________________ ".
				" ($ _________________), mas los intereses señalados en la Cláusula segunda de este documento según lo establecido en los términos y condiciones del servicio.  ".
				"SEGUNDA.- INTERESES: Que sobre la suma debida se reconocerán Intereses Moratorios a la tasa máxima legal vigente. TERCERA.- PLAZO: Que pagaré el capital indicado en la cláusula primera de este ".
				"pagaré el día _____________________________________________________________________.",0,'J',0);
			$ny=$P->y;
			$P->setxy(5,$ny); $P->multicell(200,5,"En constancia de lo anterior, se suscribe este documento en la ciudad de $Oficina->nombre el día ".($Siniestro?"$Hoy.":" _____ del mes ______________________ del año ______."),0,'J',0);
			$ny+=20; $P->setxy(5,$ny);$P->cell(80,5,"________________________________________________");
			$ny+=5;$P->setxy(5,$ny); $P->cell(80,5,"DEUDOR");
			$ny+=5; $P->setxy(5,$ny);$P->cell(80,5,"C.C.:");
			if($Oficina->fdatos_pagare) // si la oficina no tiene impresora, se imprime el formulario de datos para el pagare.
			{
				$ny+=40;$P->setxy(5,$ny);$P->setfont('Arial','B',10);$P->cell(200,5,"DATOS DEL ASEGURADO/AUTORIZADO");$P->setfont('Arial','',10);
				$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"SINIESTRO NUMERO : ".($Siniestro?$Siniestro->numero:"_________________________")."                   FECHA: $Hoy." );
				$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"IDENTIFICACION: ___________________________________________     TIPO: __CC   __CE  __NIT  __PSP  __TI ");
				$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"LUGAR DE EXPEDICION DE LA IDENTIFICACION: ___________________________________________________________");
				$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"NOMBRES: ___________________________________________________________________________________________");
				$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"APELLIDOS: __________________________________________________________________________________________");
				$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"SEXO : __M   __F          PAIS: ___________________________        CIUDAD: _____________________________________");
				$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"DIRECCION DOMICILIO: ________________________________________________________________________________");
				$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"TELEFONO OFICINA: ____________________________     TELEFONO CASA: ____________________________________");
				$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"CELULAR: ____________________________________________________________________________________________");
				$ny+=10;$P->setxy(5,$ny);$P->cell(200,5,"EMAIL: _______________________________________________________________________________________________");
			}
		}
		mysql_close($LINK);
		$P->Output($Archivo);
	}
}

function reasignar()
{
	global $auanterior,$idsiniestro;
	$Ahora=date('Y-m-d H:i:s');
	$SinNue=qo1("select numero from siniestro where id=$idsiniestro");
	$SinAnt=qo1("select siniestro from sin_autor where id='$auanterior' ");
	$NSinAnt=qo1("select numero from siniestro where id=$SinAnt");
	q("update siniestro set observaciones=concat(observaciones,\"\nReasignacion de la autorización al siniestro $SinNue\") where id=$SinAnt");
	graba_bitacora('siniestro','M',$SinAnt,'Observaciones: Reasigna autorización');
	q("update sin_autor set siniestro='$idsiniestro',observaciones=concat(observaciones,\"\nReasignación del siniestro no. $NSinAnt al no. $SinNue Fecha original de solicitud:\",fecha_solicitud,\" Fecha original de proceso: \",fecha_proceso ),
	fecha_solicitud='$Ahora',fecha_proceso='$Ahora' where id=$auanterior ");
	graba_bitacora('sin_autor','M',$auanterior,"Reasigna autorización del $NSinAnt al $SinNue");
	echo "<body><script language='javascript'>alert('Asignación hecha satisfactoriamente.');</script>";
}









?>