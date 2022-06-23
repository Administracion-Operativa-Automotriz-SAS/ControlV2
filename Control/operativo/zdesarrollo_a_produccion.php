<?php

/* PROGRAMA PARA PASAR INFORMACION DE LA BASE DE DATOS AOACARS DE PRODUCCION A LA DE DESARROLLO Y PRUEBAS */

include('inc/funciones_.php');

if (!empty($Acc) && function_exists($Acc)){eval($Acc . '();');	die();}

html('PROGRAMA PARA PASO DE INFORMACION');

echo "
<script language='javascript'>
var Operacion=1;
function siguiente()
{
	window.open('zdesarrollo_a_produccion.php?Acc=ejecutar_operacion&Operacion='+Operacion,'Oculto_pad');
	Operacion++;
}
function cerrar()
{
	alert('Se hizo '+Operacion+' operaciones');
	window.close();void(null);
}
</script>
<body>
<iframe name='Oculto_pad' id='Oculto_pad' style='visibility:hidden' width='1' height='1'></iframe>
<h3>Paso de Información de Producción a Desarrollo <input type='button' name='empezar' id='empezar' value=' EMPEZAR ' onclick='siguiente();'></h3>
<table><tr><th>Tabla</th><th>Tabla</th><th>Tabla</th></tr>
<tr>
	<td><input type='checkbox' id='ubicaciones'> Ubicaciones </td>
	<td><input type='checkbox' id='autorizaciones'> Autorizaciones </td>
	<td><input type='checkbox' id='siniestros'> Siniestros </td>
</tr>
<tr>
	<td><input type='checkbox' id='pagares'> Pagares </td>
	<td><input type='checkbox' id='visualizaciones'> Visualizaciones </td>
	<td><input type='checkbox' id='bancos'> Bancos</td>
</tr>
<tr>
	<td><input type='checkbox' id='franquicias'> Franquicias</td>
	<td><input type='checkbox' id='franquiciasciudades'> Franq.por ciudades </td>
	<td><input type='checkbox' id='alistamientos'> Alistamientos </td>
</tr>
<tr>
	<td><input type='checkbox' id='operariosflotas'> Operarios flotas </td>
	<td><input type='checkbox' id='compromisos'> Compromisos </td>
	<td><input type='checkbox' id='seguimientos'> Seguimientos </td>
</tr>
<tr>
	<td><input type='checkbox' id='clientes'> Clientes </td>
	<td><input type='checkbox' id='facturas'> Facturas </td>
	<td><input type='checkbox' id='detallefacturas'> Detalle facturas </td>
</tr>
<tr>
	<td><input type='checkbox' id='notascontables'> Notas contables </td>
	<td><input type='checkbox' id='notascredito'> Notas Credito </td>
	<td><input type='checkbox' id='recibocaja'> Recibo caja </td>
</tr>
<tr>
	<td><input type='checkbox' id='solicitudesfacturas'> Solicitudes factura </td>
	<td><input type='checkbox' id='conceptosfacturas'> Conceptos facturacion </td>
	<td><input type='checkbox' id='tarifas'> Tarifas </td>
</tr>
<tr>
	<td><input type='checkbox' id='activaciones'> Activaciones </td>
	<td><input type='checkbox' id='siniestrosanulados'> Siniestros anulados </td>
	<td><input type='checkbox' id='citas'> Citas de servicios</td>
</tr>
<tr>
	<td><input type='checkbox' id='controlcalidad'> Control calidad </td>
	<td><input type='checkbox' id='controloperaciones'> Control operaciones </td>
	<td><input type='checkbox' id='novedadesvehiculos'> Novedades H.V Vehiculos </td>
</tr>
<tr>
	<td><input type='checkbox' id='modificacionessiniestros'> Modificaciones siniestros </td>
	<td><input type='checkbox' id='solicitudesflotaaoa'> Solicitudes flota AOA </td>
	<td><input type='checkbox' id='respuestaspqrs'> Respuestas pqrs </td>
</tr>
<tr>
	<td><input type='checkbox' id='solicitudespqrs'> Solicitudes pqrs </td>
	<td><input type='checkbox' id='vehiculos'> Vehiculos </td>
	<td><input type='checkbox' id='usuarios'> Usuarios </td>
</tr>
<tr>
	<td><input type='checkbox' id='usuariosadministrativos'> Usuarios administrativos </td>
	<td><input type='checkbox' id='usuariosautorizaciones'> Usuarios autorizaciones </td>
	<td><input type='checkbox' id='usuarioscallcenter'> Usuarios callcenter </td>
</tr>
<tr>
	<td><input type='checkbox' id='usuarioscontadores'> Usuarios contadores </td>
	<td><input type='checkbox' id='usuarioscontroloperativo'> Usuarios control operativo </td>
	<td><input type='checkbox' id='usuarioscoordinacioncallcenter'> Usuarios coordinacion callcenter </td>
</tr>
<tr>
	<td> <input type='checkbox' id='usuariosdirectores'> Usuarios directores oficina</td>
	<td><input type='checkbox' id='usuariosfacturacion'> Usuarios facturacion </td>
	<td><input type='checkbox' id='usuariosjefeflotas'> Usuarios jefe flotas </td>
</tr>
<tr>
	<td><input type='checkbox' id='usuariosrecepcion'> Usuarios recepcion </td>
	<td><input type='checkbox' id='usuariosdesarrollo'> Usuarios desarrollo </td>
	<td><input type='checkbox' id='estado_agente_call'> Estados Agentes de Call </td>
</tr>
<tr>
	<td><input type='checkbox' id='call2cola1'> Call2 Cola1 </td>
	<td><input type='checkbox' id='call2cola2'> Call2 Cola2 </td>
	<td><input type='checkbox' id='call2infoerronea'> Call2 Info Erronea </td>
</tr>
<tr>
	<td><input type='checkbox' id='call2proceso'> Call2 Proceso </td>
	<td><input type='checkbox' id='call2tescalafon'> Call2 Tipo Escalafon </td>
	<td><input type='checkbox' id='call2evescalafon'> Call2 Evento de escalafon </td>
</tr>
<tr>
	<td><input type='checkbox' id='call2escalafon'> Call2 Escalafon Agentes </td>
	<td><input type='checkbox' id='causal'> Causales no Adj. </td>
	<td><input type='checkbox' id='subcausal'> Sub Causales no Adj. </td>
</tr>
<tr>
	<td><input type='checkbox' id='tipo_seguimiento'> Tipo Seguimiento </td>
	<td><input type='checkbox' id='tipifica_seguimiento'> Tipificacion Seguimiento </td>
	<td><input type='checkbox' id='tipo_compromiso'> Tipo Compromiso </td>
</tr>
</table>
</body></html>";

function ejecutar_operacion()
{
	global $Operacion;
	include('inc/link.php');
	switch($Operacion)
	{
		case 1: re_crea('ubicacion',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('ubicaciones').checked=true;parent.siguiente();</script></body>";
					break;
		case 2:re_crea('sin_autor',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('autorizaciones').checked=true;parent.siguiente();</script></body>";
					break;
		case 3:re_crea('siniestro',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('siniestros').checked=true;parent.siguiente();</script></body>";
					break;
		case 4:re_crea('pagare',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('pagares').checked=true;parent.siguiente();</script></body>";
					break;
		case 5:re_crea('solicitud_dataautor',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('visualizaciones').checked=true;parent.siguiente();</script></body>";
					break;
		case 6:re_crea('codigo_ach',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('bancos').checked=true;parent.siguiente();</script></body>";
					break;
		case 7:re_crea('franquisia_tarjeta',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('franquicias').checked=true;parent.siguiente();</script></body>";
					break;
		case 8:re_crea('ciudad_franq',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('franquiciasciudades').checked=true;parent.siguiente();</script></body>";
					break;
		case 9:re_crea('alistamiento',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('alistamientos').checked=true;parent.siguiente();</script></body>";
					break;
		case 10:re_crea('operario',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('operariosflotas').checked=true;parent.siguiente();</script></body>";
					break;
		case 11:re_crea('compromiso',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('compromisos').checked=true;parent.siguiente();</script></body>";
					break;
		case 12:re_crea('seguimiento',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('seguimientos').checked=true;parent.siguiente();</script></body>";
					break;
		case 13:re_crea('cliente',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('clientes').checked=true;parent.siguiente();</script></body>";
					break;
		case 14:re_crea('factura',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('facturas').checked=true;parent.siguiente();</script></body>";
					break;
		case 15:re_crea('facturad',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('detallefacturas').checked=true;parent.siguiente();</script></body>";
					break;
		case 16:re_crea('nota_contable',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('notascontables').checked=true;parent.siguiente();</script></body>";
					break;
		case 17:re_crea('nota_credito',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('notascredito').checked=true;parent.siguiente();</script></body>";
					break;
		case 18:re_crea('recibo_caja',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('recibocaja').checked=true;parent.siguiente();</script></body>";
					break;
		case 19:re_crea('solicitud_factura',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('solicitudesfacturas').checked=true;parent.siguiente();</script></body>";
					break;
		case 20:re_crea('concepto_fac',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('conceptosfacturas').checked=true;parent.siguiente();</script></body>";
					break;
		case 21:re_crea('tarifa',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('tarifas').checked=true;parent.siguiente();</script></body>";
					break;
		case 22:re_crea('activa_modsin',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('activaciones').checked=true;parent.siguiente();</script></body>";
					break;
		case 23:re_crea('siniestros_anulados',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('siniestrosanulados').checked=true;parent.siguiente();</script></body>";
					break;
		case 24:re_crea('cita_servicio',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('citas').checked=true;parent.siguiente();</script></body>";
					break;
		case 25:re_crea('calidad_servicio',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('controlcalidad').checked=true;parent.siguiente();</script></body>";
					break;
		case 26:re_crea('control_operacion',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('controloperaciones').checked=true;parent.siguiente();</script></body>";
					break;
		case 27:re_crea('hv_vehiculo',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('novedadesvehiculos').checked=true;parent.siguiente();</script></body>";
					break;
		case 28:re_crea('solicitud_modsin',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('modificacionessiniestros').checked=true;parent.siguiente();</script></body>";
					break;
		case 29:re_crea('solicitud_faoa',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('solicitudesflotaaoa').checked=true;parent.siguiente();</script></body>";
					break;
		case 30:re_crea('pqr_respuesta',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('respuestaspqrs').checked=true;parent.siguiente();</script></body>";
					break;
		case 31:re_crea('pqr_solicitud',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('solicitudespqrs').checked=true;parent.siguiente();</script></body>";
					break;
		case 32:re_crea('vehiculo',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('vehiculos').checked=true;parent.siguiente();</script></body>";
					break;
		case 33:re_crea('usuario',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('usuarios').checked=true;parent.siguiente();</script></body>";
					break;
		case 34:re_crea('usuario_admin',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('usuariosadministrativos').checked=true;parent.siguiente();</script></body>";
					break;
		case 35:re_crea('usuario_autorizacion',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('usuariosautorizaciones').checked=true;parent.siguiente();</script></body>";
					break;
		case 36:re_crea('usuario_callcenter',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('usuarioscallcenter').checked=true;parent.siguiente();</script></body>";
					break;
		case 37:re_crea('usuario_contador',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('usuarioscontadores').checked=true;parent.siguiente();</script></body>";
					break;
		case 38:re_crea('usuario_captura',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('usuarioscontroloperativo').checked=true;parent.siguiente();</script></body>";
					break;
		case 39:re_crea('usuario_coordcc',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('usuarioscoordinacioncallcenter').checked=true;parent.siguiente();</script></body>";
					break;
		case 40:re_crea('usuario_oficina',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('usuariosdirectores').checked=true;parent.siguiente();</script></body>";
					break;
		case 41:re_crea('usuario_facturacion',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('usuariosfacturacion').checked=true;parent.siguiente();</script></body>";
					break;
		case 42:re_crea('usuario_jefeflota',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('usuariosjefeflotas').checked=true;parent.siguiente();</script></body>";
					break;
		case 43:re_crea('usuario_recepcion',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('usuariosrecepcion').checked=true;parent.siguiente();</script></body>";
					break;
		case 44:re_crea('usuario_desarrollo',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('usuariosdesarrollo').checked=true;parent.siguiente();</script></body>";
					break;
		case 45:re_crea('estado_agente_call',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('estado_agente_call').checked=true;parent.siguiente();</script></body>";
					break;
		case 46:re_crea('call2cola1',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('call2cola1').checked=true;parent.siguiente();</script></body>";
					break;
		case 47:re_crea('call2cola2',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('call2cola2').checked=true;parent.siguiente();</script></body>";
					break;
		case 48:re_crea('call2infoerronea',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('call2infoerronea').checked=true;parent.siguiente();</script></body>";
					break;
		case 49:re_crea('call2proceso',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('call2proceso').checked=true;parent.siguiente();</script></body>";
					break;
		case 50:re_crea('call2tescalafon',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('call2tescalafon').checked=true;parent.siguiente();</script></body>";
					break;
		case 51:re_crea('call2evescalafon',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('call2evescalafon').checked=true;parent.siguiente();</script></body>";
					break;
		case 52:re_crea('call2escalafon',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('call2escalafon').checked=true;parent.siguiente();</script></body>";
					break;
		case 53:re_crea('causal',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('causal').checked=true;parent.siguiente();</script></body>";
					break;
		case 54:re_crea('subcausal',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('subcausal').checked=true;parent.siguiente();</script></body>";
					break;
		case 55:re_crea('tipo_seguimiento',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('tipo_seguimiento').checked=true;parent.siguiente();</script></body>";
					break;
		case 56:re_crea('tipifica_seguimiento',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('tipifica_seguimiento').checked=true;parent.siguiente();</script></body>";
					break;
		case 57:re_crea('tipo_compromiso',$LINK);mysql_close($LINK);
					echo "<body><script language='javascript'>parent.document.getElementById('tipo_compromiso').checked=true;parent.siguiente();</script></body>";
					break;
					
		case 58:
					echo "<body><script language='javascript'>parent.cerrar();</script></body>";
					break;
	}
}

function re_crea($tabla,$LINK	)
{
	//mysql_query("drop table if exists aoacol_aoacars.$tabla",$LINK);
	//mysql_query("create table aoacol_aoacars.$tabla like aoacol_carsmovil.$tabla",$LINK);
	//mysql_query("alter table aoacol_aoacars.$tabla default character set utf8 collate utf8_general_ci ",$LINK);
	//mysql_query("alter table aoacol_carsmovil.$tabla convert to character set utf8 collate utf8_general_ci ",$LINK);
	mysql_query("truncate aoacol_aoacars.$tabla",$LINK);
	mysql_query("insert into aoacol_aoacars.$tabla select * from aoacol_carsmovil.$tabla",$LINK);
	//mysql_query("drop table if exists aoacol_carsmovil.$tabla"."_t",$LINK);
	//mysql_query("create table aoacol_carsmovil.$tabla"."_t like aoacol_aoacars.$tabla"."_t",$LINK);
	//mysql_query("alter table aoacol_carsmovil.$tabla"."_t default character set utf8 collate utf8_general_ci ",$LINK);
	//mysql_query("alter table aoacol_carsmovil.$tabla"."_t convert to character set utf8 collate utf8_general_ci ",$LINK);
	//mysql_query("insert into aoacol_carsmovil.$tabla"."_t select * from aoacol_aoacars.$tabla"."_t",$LINK	);
}
