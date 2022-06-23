<?php

/**
 * Archivo de Utilidades adicionales para Aguila 5
 *
 * @version $Id$
 * @copyright 2009
 */

include('inc/funciones_.php');
include('inc/importador_sql.php');
if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}


function verifica_sesion()
{
	$Momento=date('Y-m-d H:i:s');
	$Disponible=date('Y-m-d H:i:s',time()-60);
	$Ocupado=date('Y-m-d H:i:s',time()-400);
	require('inc/link.php');
	mysql_query("update chat_enlinea set estado=3 where momento<'$Ocupado' ",$LINK);
	mysql_query("update chat_enlinea set estado=2 where momento>='$Ocupado' ",$LINK);
	mysql_query("update chat_enlinea set estado=1 where momento>'$Disponible' ",$LINK);
	mysql_close($LINK);
}

function anular_siniestro()
{
	global $id;
	q("insert into siniestros_anulados select * from siniestro where id=$id");
	if(q("select  * from siniestros_anulados where id=$id"))
	{
		q("delete from siniestro where id=$id");
		echo "<script language='javascript'>
				function carga()
				{
					alert('Anulación de siniestro hecha satisfactoriamente');
				}
			</script>
			<body onload='carga()'></body>";
	}
	else
	{
		echo "Problemas con la anulación.";
	}
}

function enviar_email_contacto()
{
  global $txt_cnombre,$txt_capellido,$txt_cemail,$txt_cmensajes;
  $Envio=enviar_gmail($txt_cemail /*de */,
						$txt_cnombre.' '.$txt_capellido /*Nombre de */ ,
						"atencionalcliente@aoacolombia.com,ATENCION AL CLIENTE AOA" /*para */,
						"$txt_cemail,$txt_cnombre $txt_capellido" /*con copia*/,
						"Contacto pagian web AOA" /*Objeto */,
						$txt_cmensajes);
}

function crear_extra_ok()
{
	global $id,$motivo;
	sesion();
	html('Creación de Siniestro Extra a partir de otro');
	$ingreso=date('Y-m-d H:i:s');
	$D=qo("select * from siniestro where id=$id");
	if(q("insert into siniestro (aseguradora,numero,valida_recaudo,ciudad,ciudad_original,fec_autorizacion,fec_siniestro,fec_declaracion,poliza,sucursal_radicadora,expediente,fasecolda,intermediario,email_analista,vigencia_desde,vigencia_hasta,estado,placa,asegurado_nombre,asegurado_id,marca,tipo,linea,modelo,clase,color,servicio,asegurado_direccion,declarante_nombre,declarante_id,ingreso,observaciones) values
		('$D->aseguradora','EXTRA $D->numero','$D->valida_recaudo','$D->ciudad','$D->ciudad_original','$D->fec_autorizacion','$D->fec_siniestro','$D->fec_declaracion','$D->poliza','$D->sucursal_radicadora','$D->expediente','$D->fasecolda','$D->intermediario','$D->email_analista','$D->vigencia_desde','$D->vigencia_hasta','5','$D->placa','$D->asegurado_nombre','$D->asegurado_id','$D->marca','$D->tipo','$D->linea','$D->modelo','$D->clase','$D->color','$D->servicio','$D->asegurado_direccion','$D->declarante_nombre','$D->declarante_id','$ingreso',\"$motivo\")"))
	echo "<body><script language='javascript'>alert('Creación Satisfactoria');window.close();void(null);</script>";
}

function crear_extra()
{
	global $id;
	$D=qo1("select numero from siniestro where id=$id");
	html('CREACION DE UN EXTRA A PARTIR DE OTRO');
	echo "<script language='javascript'>
			function validar_motivo()
			{
				if(!alltrim(document.forma.motivo.value)) {alert('Debe escribir un motivo para la creación del extra');document.forma.motivo.style.backgroundColor='ffffdd';document.forma.motivo.focus();return false;}
				document.forma.submit();
			}</script><body><script language='javascript'>centrar(500,300);</script>
			<h3>Creación de un Extra a partir del Siniestro $D</h3>
	<form action='util.php' method='post' target='_self' name='forma' id='forma'>
		Motivo por el cual se crea el extra:<br />
		<textarea name='motivo' style='font-family:arial;font-size:12px' cols=80 rows=3></textarea><br />
		<br />
		<input type='button' value='CREAR EXTRA' onclick='validar_motivo()'>
		<input type='hidden' name='id' value='$id'>
		<input type='hidden' name='Acc' value='crear_extra_ok'>
	</form>";
}

function generar_extra()
{
	global $id;
	sesion();
	$D=qo("select *,t_siniestro(siniestro) as nsin from solicitud_extra where id=$id");
	if($D->procesado_por)
	{
		echo "<body><script language='javascript'>alert('Esta solicitud ya fue procesada por $D->procesado_por el día $D->fecha_proceso');window.close();void(null);</script></body>";
		die();
	}
	html("GENERACION EXTRA - $D->nsin");
	echo "<script language='javascript'>
	function procesar_solicitud()
	{
		if(document.forma.Clave.value)
			document.forma.submit();
		else
		{
			alert('Debe digitar la clave del usuario actual');
		}
	}
	function salir()
	{
		window.close();void(null);
	}
	</script><body><script language='javascript'>centrar(500,500);</script>
	<h3>GENERACION DE SERVICIO EXTRA - $D->nsin</h3>
	Siniestro Número: <b>$D->nsin</b> <br>
	Fecha de solicitud: <b>$D->fecha</b> Solicitado por: <b>$D->solicitado_por</b><br>
	Justificación: <b>$D->justificacion</b><br>
	Tipo: <b>$D->tipo</b> Número de días: <b>$D->dias</b>";
	if($D->anulado)
	{
		echo "<br><b>Esta solicitud está ANULADA no se puede procesar</b>";
	}	
	else
	{
		echo "<br><br>
		<form action='util.php' target='Oculto_genextra' method='POST' name='forma' id='forma'>
			Clave de Usuario: <input type='password' name='Clave' id='Clave' onKeyPress='bloqueo_mayusculas(event)'> Digite la clave del usuario ".$_SESSION['Nombre']."  
				<div id='bloqueomayusculas' style='visibility:hidden'><b style='color:red'>El bloqueo de mayúsculas está activado</b></div><br><br>
			<input type='button' name='procesar' id='procesar' value=' PROCESAR SOLICITUD ' onclick='procesar_solicitud();'>
			<input type='hidden' name='Acc' value='generar_extra_ok'><input type='hidden' name='id' value='$id'>
		</form>
		<iframe name='Oculto_genextra' id='Oculto_genextra' style='visibility:hidden' width='1' height='1'></iframe>";
	}
	echo "</body>";
}

function generar_extra_ok()
{
	global $id,$Clave;
	sesion();
	if(verificar_password($_SESSION['Nick'],$Clave))
	{
		$D=qo("select *,t_siniestro(siniestro) as nsin from solicitud_extra where id=$id");
		html();
		echo "<body><script language='javascript'>parent.document.forma.procesar.style.visibility='hidden';</script>";
		if($D->tipo=='EXTRA')
		{
			$Ahora=date('Y-m-d H:i:s');
			$Consecutivo=qo1("select max(consecutivo) as cons from solicitud_extra where tipo='$D->tipo' and anulado=0")+1;
			$Consec=str_pad($Consecutivo,4,'0',STR_PAD_LEFT);
			q("drop table if exists tmpi_crea_extra");
			q("create table tmpi_crea_extra select * from siniestro where id=$D->siniestro");
			q("alter table tmpi_crea_extra add primary key id (id)");
			$nuevoid=qo1("select max(id) from siniestro")+1;
			q("update tmpi_crea_extra set numero=concat('EXTRA $Consec ',numero),estado=5,ingreso='$Ahora',observaciones='',
			ubicacion=0,img_odo_salida_f='',img_odo_entrada_f='',img_inv_salida_f='',img_inv_entrada_f='',img_cedula_f='',
			img_pase_f='',img_contrato_f='',obsconclusion='',img_encuesta_f='',fecha_inicial='',fecha_final='',
			encuesta_1=0,encuesta_2=0,encuesta_3=0,encuesta_4=0,encuesta_5=0,causal=0,fotovh1_f='',fotovh2_f='',fotovh3_f='',fotovh4_f='',
			fotovh5_f='',fotovh6_f='',fotovh7_f='',fotovh8_f='',fotovh9_f='',contacto_exitoso='',fotovh10_f='',adicional1_f='',adicional2_f='',
			eadicional1_f='',eadicional2_f='',encuesta_11=0,encuesta_12=0,encuesta_13=0,encuesta_14=0,encuesta_15=0,encuesta_16=0,
			adicional3_f='',adicional4_f='',subcausal=0,dadicional3_f='',dadicional4_f='',id=$nuevoid,dias_servicio=$D->dias
			where id=$D->siniestro");
			$nid=q("insert into siniestro select * from tmpi_crea_extra");
			q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ('$nid','".date('Y-m-d')."','".date('H:i:s')."',
			'".$_SESSION['Nombre']."','CREACION DE EXTRA',1)");
			q("update solicitud_extra set consecutivo='$Consec',siniestro_asignado='$nid',
			procesado_por='".$_SESSION['Nombre']."', fecha_proceso='".date('Y-m-d H:i:s')."' where id=$id");
			echo "<script language='javascript'>alert('Se generó el servicio EXTRA $Consec Satisfactoriamente.');parent.salir();</script>";
		}
		elseif($D->tipo=='AOA')
		{
			$Ahora=date('Y-m-d H:i:s');
			$Consecutivo=qo1("select max(consecutivo) as cons from solicitud_extra where tipo='$D->tipo' and anulado=0")+1;
			$Consec=str_pad($Consecutivo,4,'0',STR_PAD_LEFT);
			q("drop table if exists tmpi_crea_extra");
			q("create table tmpi_crea_extra select * from siniestro where id=$D->siniestro");
			q("alter table tmpi_crea_extra add primary key id (id)");
			$nuevoid=qo1("select max(id) from siniestro")+1;
			q("update tmpi_crea_extra set numero=concat('AOA $Consec ',numero),estado=5,ingreso='$Ahora',observaciones='',
			ubicacion=0,img_odo_salida_f='',img_odo_entrada_f='',img_inv_salida_f='',img_inv_entrada_f='',img_cedula_f='',
			img_pase_f='',img_contrato_f='',obsconclusion='',img_encuesta_f='',fecha_inicial='',fecha_final='',
			encuesta_1=0,encuesta_2=0,encuesta_3=0,encuesta_4=0,encuesta_5=0,causal=0,fotovh1_f='',fotovh2_f='',fotovh3_f='',fotovh4_f='',
			fotovh5_f='',fotovh6_f='',fotovh7_f='',fotovh8_f='',fotovh9_f='',contacto_exitoso='',fotovh10_f='',adicional1_f='',adicional2_f='',
			eadicional1_f='',eadicional2_f='',encuesta_11=0,encuesta_12=0,encuesta_13=0,encuesta_14=0,encuesta_15=0,encuesta_16=0,
			adicional3_f='',adicional4_f='',subcausal=0,dadicional3_f='',dadicional4_f='',id=$nuevoid,dias_servicio=$D->dias 
			where id=$D->siniestro");
			$nid=q("insert into siniestro select * from tmpi_crea_extra");
			q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ('$nid','".date('Y-m-d')."','".date('H:i:s')."',
			'".$_SESSION['Nombre']."','CREACION DE AOA',1)");
			q("update solicitud_extra set consecutivo='$Consec',siniestro_asignado='$nid',
			procesado_por='".$_SESSION['Nombre']."', fecha_proceso='".date('Y-m-d H:i:s')."' where id=$id");
			echo "<script language='javascript'>alert('Se generó el servicio AOA $Consec Satisfactoriamente.');parent.salir();</script>";
		}
		elseif($D->tipo=='RENTAASEG')
		{
			$Ahora=date('Y-m-d H:i:s');
			$Consecutivo=qo1("select max(consecutivo) as cons from solicitud_extra where tipo='$D->tipo' and anulado=0")+1;
			$Consec=str_pad($Consecutivo,4,'0',STR_PAD_LEFT);
			q("drop table if exists tmpi_crea_extra");
			q("create table tmpi_crea_extra select * from siniestro where id=$D->siniestro");
			q("alter table tmpi_crea_extra add primary key id (id)");
			$nuevoid=qo1("select max(id) from siniestro")+1;
			q("update tmpi_crea_extra set numero=concat('RENTAASEG $Consec ',numero),estado=5,ingreso='$Ahora',observaciones='',
			ubicacion=0,img_odo_salida_f='',img_odo_entrada_f='',img_inv_salida_f='',img_inv_entrada_f='',img_cedula_f='',
			img_pase_f='',img_contrato_f='',obsconclusion='',img_encuesta_f='',fecha_inicial='',fecha_final='',
			encuesta_1=0,encuesta_2=0,encuesta_3=0,encuesta_4=0,encuesta_5=0,causal=0,fotovh1_f='',fotovh2_f='',fotovh3_f='',fotovh4_f='',
			fotovh5_f='',fotovh6_f='',fotovh7_f='',fotovh8_f='',fotovh9_f='',contacto_exitoso='',fotovh10_f='',adicional1_f='',adicional2_f='',
			eadicional1_f='',eadicional2_f='',encuesta_11=0,encuesta_12=0,encuesta_13=0,encuesta_14=0,encuesta_15=0,encuesta_16=0,
			adicional3_f='',adicional4_f='',subcausal=0,dadicional3_f='',dadicional4_f='',id=$nuevoid,dias_servicio=$D->dias, aseguradora=39 
			where id=$D->siniestro");
			$nid=q("insert into siniestro select * from tmpi_crea_extra");
			q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ('$nid','".date('Y-m-d')."','".date('H:i:s')."',
			'".$_SESSION['Nombre']."','CREACION DE RENTAASEG',1)");
			q("update solicitud_extra set consecutivo='$Consec',siniestro_asignado='$nid',
			procesado_por='".$_SESSION['Nombre']."', fecha_proceso='".date('Y-m-d H:i:s')."' where id=$id");
			echo "<script language='javascript'>alert('Se generó el servicio RENTAASEG $Consec Satisfactoriamente.');parent.salir();</script>";
		}
		elseif($D->tipo=='EXTENSION')
		{
			$Ahora=date('Y-m-d H:i:s');
			$S=qo("select * from siniestro where id=$D->siniestro");
			if($S->ubicacion)
			{
				if($Cita=qo("select * from cita_servicio where siniestro=$D->siniestro and estado='C' and estadod='P' "))
				{
					$U=qo("select * from ubicacion where id=$S->ubicacion");
					$Ffinal=aumentadias($S->fecha_inicial,$D->dias);
					q("update siniestro set estado=7, observaciones=concat(observaciones, '\nEXTENSION del servicio por $D->dias de acuerdo con la solicitud número $id'),
					fecha_final='$Ffinal',dias_servicio=$D->dias where id=$D->siniestro");
					q("update ubicacion set fecha_final='$Ffinal',estado=1,odometro_final=odometro_inicial,
					observaciones=concat(observaciones, '\nEXTENSION del servicio por $D->dias de acuerdo con la solicitud número $id')
					where id=$S->ubicacion");
					q("update cita_servicio set fec_devolucion='$Ffinal', estadod='P',dias_servicio=$D->dias, 
					observaciones=concat(observaciones, '\nEXTENSION del servicio por $D->dias de acuerdo con la solicitud número $id'),
					operario_domiciliod=0 where id=$Cita->id");
					q("update solicitud_extra set procesado_por='".$_SESSION['Nombre']."', fecha_proceso='".date('Y-m-d H:i:s')."' where id=$id");
					echo "<script language='javascript'>alert('Se hizo extensión del servicio de la fecha: $U->fecha_inicial hasta $Ffinal Satisfactoriamente.');parent.salir();</script>";
				}
				elseif($Cita=qo("select * from cita_servicio where siniestro=$D->siniestro and estado='C' and estadod='C' "))
				{
					$Ahora=date('Y-m-d H:i:s');
					$Dias=$D->dias-$Cita->dias_servicio;
					$Consecutivo=qo1("select max(consecutivo) as cons from solicitud_extra where tipo='$D->tipo' and anulado=0")+1;
					$Consec=str_pad($Consecutivo,4,'0',STR_PAD_LEFT);
					q("drop table if exists tmpi_crea_extra");
					q("create table tmpi_crea_extra select * from siniestro where id=$D->siniestro");
					q("alter table tmpi_crea_extra add primary key id (id)");
					$nuevoid=qo1("select max(id) from siniestro")+1;
					q("update tmpi_crea_extra set numero=concat('EXTENSION $Consec ',numero),estado=5,ingreso='$Ahora',observaciones='',
					ubicacion=0,img_odo_salida_f='',img_odo_entrada_f='',img_inv_salida_f='',img_inv_entrada_f='',img_cedula_f='',
					img_pase_f='',img_contrato_f='',obsconclusion='',img_encuesta_f='',fecha_inicial='',fecha_final='',
					encuesta_1=0,encuesta_2=0,encuesta_3=0,encuesta_4=0,encuesta_5=0,causal=0,fotovh1_f='',fotovh2_f='',fotovh3_f='',fotovh4_f='',
					fotovh5_f='',fotovh6_f='',fotovh7_f='',fotovh8_f='',fotovh9_f='',contacto_exitoso='',fotovh10_f='',adicional1_f='',adicional2_f='',
					eadicional1_f='',eadicional2_f='',encuesta_11=0,encuesta_12=0,encuesta_13=0,encuesta_14=0,encuesta_15=0,encuesta_16=0,
					adicional3_f='',adicional4_f='',subcausal=0,dadicional3_f='',dadicional4_f='',id=$nuevoid,dias_servicio=$Dias where id=$D->siniestro");
					$nid=q("insert into siniestro select * from tmpi_crea_extra");
					q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ('$nid','".date('Y-m-d')."','".date('H:i:s')."',
					'".$_SESSION['Nombre']."','CREACION DE EXTENSION',1)");
					q("update solicitud_extra set consecutivo='$Consec',siniestro_asignado='$nid',
					procesado_por='".$_SESSION['Nombre']."', fecha_proceso='".date('Y-m-d H:i:s')."' where id=$id");
					echo "<script language='javascript'>alert('Se generó el servicio EXTENSION $Consec Satisfactoriamente.');parent.salir();</script>";
				}
				else
				{
					echo "<script language='javascript'>alert('NO SE ENCUENTRA LA CITA DEL SINIESTRO $D->siniestro');</script></body>";
				}
			}
			else
			{
				q("update cita_servicio set dias_servicio ='$D->dias' where siniestro=$D->siniestro and estado='P' ");
				q("update siniestro set dias_servicio ='$D->dias' where id=$D->siniestro ");
				echo "<script language='javascript'>alert('EXTENSION DE CITA A $D->dias');</script>";
			}
		}
		echo "</body>";
	}
	else
	{
		echo "<body><script language='javascript'>alert('CLAVE INCORRECTA');window.close();void(null);</script></body>";
	}
}

function alerta_garantias()
{
	global $Excel;
	$Hoy=date('Y-m-d');
	$Antes=date('Y-m-d',strtotime(aumentadias($Hoy,-15)));
	if($Excel)
	{
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=Adjunto_Factura_$Consecutivo.xls");
	}
	else	html();
	echo "<script language='javascript'>
	function controlg(id)
	{
		modal('zcontrol_custodia_garantia.php?Acc=consultar_garantias&IDsiniestro='+id,0,0,500,500,'cg');
	}
	</script>
	<body>";
	q("drop table if exists tmpi_citas_servicios");
	q("create table tmpi_citas_servicios select distinct cs.siniestro,ofi.nombre
		from oficina ofi,cita_servicio cs,siniestro s
		where s.id=cs.siniestro and cs.oficina=ofi.id and s.fecha_final between '2013-01-01' and '$Antes' and cs.estado in ('C','S') ");
	
	q("alter table tmpi_citas_servicios add index llave (siniestro)");
	
	$Consulta_garantias="select a.*,s.numero as nsin,s.asegurado_nombre,s.asegurado_id,ase.nombre as naseguradora,ciu.nombre as nciudad,
								frq.nombre as nfranq,s.fecha_final as fdevolvh,s.id as sid,a.id as aid,s.ubicacion,tcs.nombre as nofic
								FROM sin_autor a,siniestro s,franquisia_tarjeta frq,aseguradora ase,ciudad ciu,tmpi_citas_servicios tcs
								WHERE a.siniestro=s.id and a.franquicia=frq.id and ase.id=s.aseguradora
								and s.ciudad=ciu.codigo and tcs.siniestro=s.id and
								s.fecha_final <='$Antes' and a.estado='A' and metodo_devol=''
								and a.aut_fac=0 and a.franquicia!=10 
								ORDER BY nofic,fecha_solicitud ";
	if($Garantias=q($Consulta_garantias))
	{	
		echo "<table align='center' border cellspacing='0' bgcolor='ffffff'><tr>
						<th colspan=14>DATOS DE LA GARANTIA</th></tr>
						<tr><th>#</th><th>Aseguradora</th><th>Siniestro</th><th>Asegurado</th><th>Identificación</th><th>Tarjeta Habiente</th>
						<th>Identificación</th><th>Fecha Solicitud</th><th>Devolución.Vh.</th><th>Franquicia</th><th>Monto</th></tr>";
		$Contador=0;
		include('inc/link.php');
		$Aciudad='';
		$Correo="<table border cellspacing='0'><tr><td>Siniestro</td><td>Fecha</td><td>Devol. Vh.</td><td>Franquicia</td><td>Valor</td></tr>";
		while($G=mysql_fetch_object($Garantias))
		{
		//	if(!$Noficina=qo1m("select nombre from oficina,ubicacion where oficina.id=ubicacion.oficina and ubicacion.id=$G->ubicacion",$LINK))
			$Noficina=$G->nofic;
			$Contador++;
			if($Aciudad!=$Noficina)
			{
				$Aciudad=$Noficina;
				echo "<tr><td colspan=14><h3>$Noficina</h3></td></tr>";
				$Correo.="<tr><td colspan=14><h3>$Noficina</h3></td></tr>";
				$Contador=1;
			}
			$Correo.="<tr><td>$G->nsin</td>";
			$Correo.="<td>".date('Y-m-d',strtotime($G->fecha_solicitud))."</td>";
			$Correo.="<td>$G->fdevolvh</td><td>$G->nfranq</td><td align='right'>$G->valor</td></tr>";
			echo "<tr ondblclick='controlg($G->sid);'>
						<td bgcolor='ffffff' align='center'>".coma_format($Contador)."</td>
						<td bgcolor='ffffff'>$G->naseguradora</td>
						<td bgcolor='ffffff'><a style='cursor:pointer' onclick=\"modal('zsiniestro.php?Acc=buscar_siniestro&siniestro=$G->nsin',0,0,600,600,'vs');\">$G->nsin</a></td>
						<td bgcolor='ffffff'>$G->asegurado_nombre</td>
						<td align='right' bgcolor='ffffff'>".coma_format($G->asegurado_id)."</td>
						<td bgcolor='ffffff'>$G->nombre <span id='fo_$_id'></span></td>
						<td align='right' bgcolor='ffffff'>".coma_format($G->identificacion)."</td>
						<td bgcolor='ffffff'>".date('Y-m-d',strtotime($G->fecha_solicitud))."</td>
						<td bgcolor='ffffff' align='center'>$G->fdevolvh</td>
						<td nowrap='yes' bgcolor='ffffff'>$G->nfranq <br><span id='rcp_$_id'></span><span id='rc_$G->aid'></span></td>
						<td align='right' bgcolor='ffffff'><b>".(!$Excel?coma_format($G->valor):$G->valor)."</b></td>
						</tr>";
			if($G->fecha_solicitud <='2012-01-01') mysql_query("update sin_autor set metodo_devol='ANULADO' where id=$G->id",$LINK);
		}
		$Correo.="</table>";
		mysql_close($LINK);
		echo "</table>";
		// "claudiacastro@aoacolombia.com,Claudia Castro;siniestros@aoacolombia.com,Siniestros" /*para */,
		enviar_gmail('sistemas@aoacolombia.com' /*de */,
                           'Sistema de Control Operativo' /*Nombre de */ ,
                            "claudiacastro@aoacolombia.com,Claudia Castro;siniestros@aoacolombia.com,Siniestros" /*para */,
                            "sergiocastillo@aoacolombia.com" /*con copia*/,
                            "Informe de Garantias sin cerrar $Hoy" /*Objeto */,
                            "<body><b>Informe de garantias sin cerrar. Fecha: $Hoy</b><br>$Correo
							<br><br>Nota: Se eliminaron las tildes del mensaje para compatibilidad con distintos administradores de correo.
							<br><br>Este es un correo automatico del Sistema de Control Operativo.<br><br>
							<img src='http://app.aoacolombia.com/img/AOAlogo.jpg' title='AOA COLOMBIA S.A. SE MUEVE CONTIGO'/><br>
							<p style='font-size:9px'>Este mensaje es confidencial, esta amparado por secreto profesional y no puede ser usado ni divulgado por personas distintas de su(s) destinatario(s). Si no es el receptor autorizado, cualquier retencion, difusion, distribucion o copia de este mensaje es prohibida y sero sancionada por la ley. Si por error recibe este mensaje, favor reenviarlo al remitente y borrar el mensaje recibido.</p>
							<p style='font-size:9px'>This messajge is confidential, subject to professional secret and may not be used or disclosed by any person other than its addressee(s). If you are not the addressee(s), any retention, dissemination, distribution or copying of this message is strictly prohibited and sanctioned by law. If you receive this message in error, please send it back and delete the message received.<br>
							</BODY>");
	}
	echo "<a href='util.php?Acc=alerta_garantias&Excel=1','_self'>Descargar en excel</a>";
	
}

function direc_mod6()
{
	$Seis=date('Y-m-d H:i:s',mktime(date('H')-6,date('i'),date('s'),date('n'),date('j'),date('Y')));
	html('LISTADO DE DIRECTORIOS ACTUALIZADOS');
	echo "<body><h3>Listado de directorios modificados las últimas 6 horas, o sea, desde: $Seis</h3>" ;
	if($query=q("select distinct tabla from app_bitacora where concat(ano,'-',mes,'-',dia,' ',hora,':',minuto,':',segundo)>'$Seis' and detalle like '%imagen%' order by tabla"))
	{
		$Directorio=array();
		while($T=mysql_fetch_object($query))
		{
			$nt=$T->tabla.'_t';
			$Directorios=q("select distinct campo,rutaimg from $nt where rutaimg!='' ");
			while($D=mysql_fetch_object($Directorios))
			{
				$Directorio[$T->tabla][$D->campo]=$D->rutaimg;
			}
		}
		$Query=q("select tabla,registro,detalle from app_bitacora where concat(ano,'-',mes,'-',dia,' ',hora,':',minuto,':',segundo)>'$Seis' and detalle like '%imagen%' order by tabla,registro");
		$Listado=array();
		$raiz='Control/operativo/';
		while($D =mysql_fetch_object($Query ))
		{
			$Campo=substr($D->detalle,strpos($D->detalle,'Modifica:')+9);
			$Campo=substr($Campo,0,strpos($Campo,' '));
			$dir=$raiz.$Directorio[$D->tabla][$Campo].'/'.substr(str_pad($D->registro,6,'0',STR_PAD_LEFT),0,3).'/'.$D->registro;
			$Listado[$dir]=1;
		}
		foreach($Listado as $dir => $contenido) echo "<br>$dir";
	}
	if($query=q("select distinct tabla from aoacol_administra.app_bitacora where concat(ano,'-',mes,'-',dia,' ',hora,':',minuto,':',segundo)>'$Seis' and detalle like '%imagen%' order by tabla"))
	{
		$Directorio2=array();
		while($T=mysql_fetch_object($query))
		{
			$nt=$T->tabla.'_t';
			$Directorios=q("select distinct campo,rutaimg from aoacol_administra.$nt where rutaimg!='' ");
			while($D=mysql_fetch_object($Directorios))
			{
				$Directorio2[$T->tabla][$D->campo]=$D->rutaimg;
			}
		}
		$Query=q("select tabla,registro,detalle from aoacol_administra.app_bitacora where concat(ano,'-',mes,'-',dia,' ',hora,':',minuto,':',segundo)>'$Seis' and detalle like '%imagen%' order by tabla,registro");
		$Listado=array();
		$raiz='Administrativo/';
		while($D =mysql_fetch_object($Query ))
		{
			$Campo=substr($D->detalle,strpos($D->detalle,'Modifica:')+9);
			$Campo=substr($Campo,0,strpos($Campo,' '));
			$dir=$raiz.$Directorio2[$D->tabla][$Campo].'/'.substr(str_pad($D->registro,6,'0',STR_PAD_LEFT),0,3).'/'.$D->registro;
			$Listado[$dir]=1;
		}
		foreach($Listado as $dir => $contenido) echo "<br>$dir";
	}
}

function direc_mod24()
{
	$Veinticuatro=date('Y-m-d H:i:s',mktime(date('H')-24,date('i'),date('s'),date('n'),date('j'),date('Y')));
	html('LISTADO DE DIRECTORIOS ACTUALIZADOS');
	echo "<body><h3>Listado de directorios modificados las últimas 24 horas, o sea, desde: $Veinticuatro</h3>" ;
	if($query=q("select distinct tabla from app_bitacora where concat(ano,'-',mes,'-',dia,' ',hora,':',minuto,':',segundo)>'$Veinticuatro' and detalle like '%imagen%' order by tabla"))
	{
		$Directorio=array();
		while($T=mysql_fetch_object($query))
		{
			$nt=$T->tabla.'_t';
			$Directorios=q("select distinct campo,rutaimg from $nt where rutaimg!='' ");
			while($D=mysql_fetch_object($Directorios))
			{
				$Directorio[$T->tabla][$D->campo]=$D->rutaimg;
			}
		}
		$Query=q("select tabla,registro,detalle from app_bitacora where concat(ano,'-',mes,'-',dia,' ',hora,':',minuto,':',segundo)>'$Veinticuatro' and detalle like '%imagen%' order by tabla,registro");
		$Listado=array();
		$raiz='Control/operativo/';
		while($D =mysql_fetch_object($Query ))
		{
			$Campo=substr($D->detalle,strpos($D->detalle,'Modifica:')+9);
			$Campo=substr($Campo,0,strpos($Campo,' '));
			$dir=$raiz.$Directorio[$D->tabla][$Campo].'/'.substr(str_pad($D->registro,6,'0',STR_PAD_LEFT),0,3).'/'.$D->registro;
			$Listado[$dir]=1;
		}
		foreach($Listado as $dir => $contenido) echo "<br>$dir";
	}
	if($query=q("select distinct tabla from aoacol_administra.app_bitacora where concat(ano,'-',mes,'-',dia,' ',hora,':',minuto,':',segundo)>'$Veinticuatro' and detalle like '%imagen%' order by tabla"))
	{
		$Directorio2=array();
		while($T=mysql_fetch_object($query))
		{
			$nt=$T->tabla.'_t';
			$Directorios=q("select distinct campo,rutaimg from aoacol_administra.$nt where rutaimg!='' ");
			while($D=mysql_fetch_object($Directorios))
			{
				$Directorio2[$T->tabla][$D->campo]=$D->rutaimg;
			}
		}
		$Query=q("select tabla,registro,detalle from aoacol_administra.app_bitacora where concat(ano,'-',mes,'-',dia,' ',hora,':',minuto,':',segundo)>'$Veinticuatro' and detalle like '%imagen%' order by tabla,registro");
		$Listado=array();
		$raiz='Administrativo/';
		while($D =mysql_fetch_object($Query ))
		{
			$Campo=substr($D->detalle,strpos($D->detalle,'Modifica:')+9);
			$Campo=substr($Campo,0,strpos($Campo,' '));
			$dir=$raiz.$Directorio2[$D->tabla][$Campo].'/'.substr(str_pad($D->registro,6,'0',STR_PAD_LEFT),0,3).'/'.$D->registro;
			$Listado[$dir]=1;
		}
		foreach($Listado as $dir => $contenido) echo "<br>$dir";
	}
}

function importar_tabla()
{
	html('IMPORTAR TABLA');
	echo "<body>
	<form action='util.php' target='_self' method='POST' name='forma' id='forma'>
			Tabla a importar: <input type='text' name='tabla' id='tabla' value='' size='30' maxlength='30'>
			<br><br><input type='submit' name='continuar' id='continuar' value=' CONTINUAR '>
			<input type='hidden' name='Acc' value='importar_tabla_ok'>
	</form>
	</body>";
}

function importar_tabla_ok()
{
	global $tabla;
	html();
	q("drop table if exists $tabla");
	q("create table $tabla like aoacol_aoacars.$tabla");
	q("alter table $tabla default character set utf8 collate utf8_general_ci ");
	q("alter table $tabla convert to character set utf8 collate utf8_general_ci ");
	q("insert into $tabla select * from aoacol_aoacars.$tabla");
	q("drop table if exists  $tabla"."_t");
	q("create table $tabla"."_t like aoacol_aoacars.$tabla"."_t");
	q("alter table $tabla"."_t default character set utf8 collate utf8_general_ci ");
	q("alter table $tabla"."_t convert to character set utf8 collate utf8_general_ci ");
	q("insert into $tabla"."_t select * from aoacol_aoacars.$tabla"."_t"	);
	echo "importacion finalizada";
}

function ver_variable_sesion()
{
	html();
	echo "<body>";
	session_cache_expire(900);
	echo session_cache_expire();
	echo "</body>";
}

function descarga_bd()
{
	global $id;
}

function lista_procesos()
{
	html('LISTA DE PROCESOS MYSQL');
	$Procesos=q("show processlist");
	echo "<script language='javascript'>
	function recargar() {window.open('util.php?Acc=lista_procesos','_self');}
	function matar_proceso(id){	window.open('util.php?Acc=eliminar_proceso_mysql&id='+id,'Oculto_lista_procesos');}
	function recargar(){window.open('util.php?Acc=lista_procesos','_self');}
	var Recarga=setTimeout(recargar,5000);
	</script><body>
	<h3>Lista de Procesos  <a style='cursor:pointer' onclick='recargar()'>Recargar<a></h3><table border cellspacing='0' width='100%'>
	<tr><th>Id</th><th>Usuario</th><th>Host</th><th>base</th><th>Comando</th><th>Opcion</th><th>Momento</th><th>Estado</th><th>Descripción</th></tr>";
	$Contador=0;
	while($P=mysql_fetch_object($Procesos))
	{
		echo "<tr><td>$P->Id</td><td>$P->User</td><td>$P->Host</td><td>$P->db</td>
					<td>$P->Command</td><td><a style='cursor:pointer' onclick='matar_proceso($P->Id);'>Matar</a></td><td>$P->Time</td><td>$P->State</td><td>$P->Info</td></tr>";
		$Contador++;
	}
	echo "</table>
	<br><br>
	<a href='util.php?Acc=infophp' target='_self'>PhP Info</a>
	<iframe name='Oculto_lista_procesos' id='Oculto_lista_procesos' style='visibility:hidden' width='1' height='1'></iframe>";
	if($Contador>20)
	enviar_gmail('sergiocastillo@aoacolombia.com' /*de */ ,'Sergio Castillo' /*nombre de */ ,
				"sergiocastillo@aoacolombia.com,Sergio Castillo" /*para */ ,
				""   /*Con copia*/ ,
				"Exceso en Procesos MySQL"  /*OBJETO*/,
				nl2br("Hay un exceso en el numero de procesos MySQL.
				Numero de procesos: $Contador
				Ingresar a la lista de procesos: 
				http://app.aoacolombia.com/Control/operativo/util.php?Acc=lista_procesos
				") /*mensaje */);
	echo "</body>";
}

function eliminar_proceso_mysql()
{
	global $id;
	q("kill $id");
	echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function devolver_siniestro()
{
	global $id;
	if(q("insert into siniestro select * from siniestro_hst where id=$id"))
	echo "<body><script language='javascript'>alert('Siniestro restaurado');window.close();void(null);</script></body>";
}

function infophp()
{
	html();
	echo "<body>";
	phpinfo();
	
}

function marcar_recongelado()
{
	global $id;
	sesion();
	$Ahora=date('Y-m-d H:i:s');
	q("update sin_autor set recongelamiento=1 where id=$id");
	q("update tmpi_".$_SESSION['Id_alterno']."_148 set au_recongelamiento=1 where au_id=$id");
	echo "<body><script language='javascript'>window.close();void(null);opener.location.reload();</script></body>";
}

function separar_factura_axa()
{
	include('inc/gpos.php');
	$IDCLIENTE=81073;
	$IDASEGURADORA=25;
	$IDCONCEPTO=145;
	$Hoy=date('Y-m-d');
	html('SEPARAR CONSECUTIVO DE FACTURA PARA AXA');
	echo "
		<style tyle='text/css'>
			<!--
				body {background-color:#ffffff;}
				.boton {background-color:#BFFFEF;color:#0040FF;height:30;padding:10;border-radius:5px;}
			-->
		</style>
		<script language='javascript'>
			function usar(dato,consecutivo)
			{
				if(confirm('Desea Asignar la factura '+consecutivo+' ?'))
						window.open('http://www.aoasemuevecontigo.com/zfacturacion_axa.php?Acc=recibir_factura&consecutivo='+consecutivo+'&idfactura='+dato,'_self');
			}
			function separar() {window.open('util.php?Acc=separar_factura_axa_ok','Oculto_separar');}
			function recargar() { window.open('util.php?Acc=separar_factura_axa','_self');}
		</script>
	<body>";
	if($Separadas=q("select * from factura where cliente=$IDCLIENTE and aseguradora=$IDASEGURADORA and anulada=0 and autorizadopor='' order by id"))
	{
		echo "Facturas previamente separadas sin aprobacion: 
			<table border cellspacing='0'><tr><th>#</th><th>Id</th><th>Consecutivo</th><th>Fecha Emisión</th><th>Valor Total</th><th></th></tr>";
		$Contador=0;
		while($S=mysql_fetch_object($Separadas))
		{
			$Contador++;
			echo "<tr><td>$Contador</td><td>$S->id</td><td>$S->consecutivo</td><td>$S->fecha_emision</td><td align='right'>".coma_format($S->total)."</td>
					<td>
						<input type='button' class='boton' name='usar' id='usar' value='USAR ESTA' onclick='usar($S->id,$S->consecutivo);'>
					</td></tr>";
		}
		echo "</table>";
	}
	echo "<br>
		<input type='button' class='boton' name='generar' id='generar' value='SEPARAR NUEVO CONSECUTIVO' onclick='separar();'>
		<iframe name='Oculto_separar' id='Oculto_separar' style='display:none' width='1' height='1'></iframe>
		</body>";
}

function consultar_factura_axa()
{
	include('inc/gpos.php');
	$IDCLIENTE=81073;
	$IDASEGURADORA=25;
	$IDCONCEPTO=145;
	$Hoy=date('Y-m-d');
	html('CONSULTAR CONSECUTIVO DE FACTURA PARA AXA');
	echo "
		<style tyle='text/css'>
			<!--
				body {background-color:#ffffff;}
				.boton {background-color:#BFFFEF;color:#0040FF;height:30;padding:10;border-radius:5px;}
			-->
		</style>
		<script language='javascript'>
			function usar(dato,consecutivo)
			{
				window.open('http://www.aoasemuevecontigo.com/zfacturacion_axa.php?Acc=recibir_factura&consecutivo='+consecutivo+'&idfactura='+dato,'_self');
			}
			function separar() {window.open('util.php?Acc=separar_factura_axa_ok','Oculto_separar');}
			function recargar(){window.open('util.php?Acc=separar_factura_axa','_self');}
		</script>
	<body>";
	if($Separadas=q("select * from factura where cliente=$IDCLIENTE and aseguradora=$IDASEGURADORA and anulada=0 and autorizadopor!='' order by id"))
	{
		echo "Facturas previamente generadas con aprobacion: 
			<table border cellspacing='0'><tr><th>#</th><th>Id</th><th>Consecutivo</th><th>Fecha Emisión</th><th>Valor Total</th><th>Aprobada por</th><th></th></tr>";
		$Contador=0;
		while($S=mysql_fetch_object($Separadas))
		{
			$Contador++;
			echo "<tr><td>$Contador</td><td>$S->id</td><td>$S->consecutivo</td><td>$S->fecha_emision</td><td align='right'>".coma_format($S->total)."</td>
					<td>$S->autorizadopor</td>
					<td>
						<input type='button' class='boton' name='usar' id='usar' value='CONSULTAR ESTA' onclick='usar($S->id,$S->consecutivo);'>
					</td></tr>";
		}
		echo "</table>";
	}
	echo "<br>
		<iframe name='Oculto_separar' id='Oculto_separar' style='display:none' width='1' height='1'></iframe>
		</body>";
}

function separar_factura_axa_ok()
{
	include('inc/gpos.php');
	$IDCLIENTE=81073;
	$IDASEGURADORA=25;
	$IDCONCEPTO=42;
	$Hoy=date('Y-m-d');
	$Consecutivo=qo1("select consecutivo_movil from cfg_factura where id=1");
	if($Consecutivo)
	{
		$Consecutivo++;
		q("update cfg_factura set consecutivo_movil=$Consecutivo where id=1");
	}
	else 
	{
		$Consecutivo=qo1("select consecutivo_aoa from cfg_factura where id=1")+1;
		q("update cfg_factura set consecutivo_aoa=$Consecutivo where id=1");
	}
	
	if($NF=q("insert into factura (cliente,aseguradora,consecutivo,fecha_emision,movilidad) values ('$IDCLIENTE','$IDASEGURADORA','$Consecutivo','$Hoy',1)"))
	{
		echo "<body><script language='javascript'>alert('FACTURA GENERADA');parent.recargar();</script></body>";
	}
	else echo "<body><script language='javascript'>alert('No se pudo crear la nueva factura.');</script></body>";
}

function generar_factura_axa()
{
	include('inc/gpos.php');
	$IDCONCEPTO=42;
	html();
	echo "
	<style type='text/css'>
	<!--
		body {background-color:#ffffff;color:#000000;}
	-->
	</style>
	<body><h3>GENERACION DE FACTURA AOA</h3>
		Valor de los servicios: $valor
		Id de Factura: $idfac";
	$iva=round($valor*0.16);
	$total=$iva+$valor;
//	$Fvence=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),30)));
	q("update factura set oficina=1, fecha_vencimiento=fecha_emision,subtotal='$valor', iva='$iva',total='$total' where id=$idfac");
	q("update facturad set unitario='0',iva='0',total='0' where factura=$idfac");
	$Det1=explode('|',$detalle);
	foreach($Det1 as $Det2)
	{
		$Det3=explode(':',$Det2);
		$Det4=explode('-',$Det3[0]);
		$idServicio=$Det4[0];$conductor=$Det4[1];$centro_operacion=$Det4[2];$valor=$Det3[1];
		echo "<br>IdServicio: $idServicio Centro de Operacion: $centro_operacion Conductor: $conductor Valor: $valor ";
		$iva=round($valor*0.16,0);
		$total=$valor+$iva;
		if($item=qo1("select id from facturad where factura='$idfac' and centro_operacion='$centro_operacion' and conductor='$conductor' and idservicio='$idServicio' "))
		{
			q("update facturad set concepto=$IDCONCEPTO,cantidad=1,unitario='$valor',iva='$iva',total='$total' where id='$item' ");
		}
		else
		{
			q("insert into facturad (factura,concepto,cantidad,unitario,iva,total,centro_operacion,conductor,idservicio) values ('$idfac','$IDCONCEPTO','1','$valor','$iva','$total','$centro_operacion','$conductor','$idServicio')");
		}
	}
	q("delete from facturad where factura='$idfac' and total=0");
	if($Sumas=qo("select sum(iva) as iva, sum(total) as total from facturad where factura=$idfac")) q("update factura set iva=$Sumas->iva,total=$Sumas->total where id=$idfac");
	echo "
	<h4>FIN GENERACION DE FACTURA</h4>
	</body>";
}

function obtener_costos_factura()
{
	include('inc/gpos.php');
	html();
	echo "<body><script language='javascript'>centrar(500,400);</script>
	<h3>Obtener Costos de Movilidad</h3>
	<form action='http://www.aoasemuevecontigo.com/zfacturacion_axa.php' target='_self' method='POST' name='forma' id='forma'>
		<input type='hidden' name='Acc' value='obtener_costo_factura'>
		Consecutivo inicial:  ".menu1("CI","select consecutivo,consecutivo from factura where movilidad=1 and anulada=0 order by consecutivo",$consecutivo,1)."<br>
		Consecutivo final: ".menu1("CF","select consecutivo,consecutivo from factura where movilidad=1 and anulada=0 order by consecutivo",$consecutivo,1)."<br><br>
		<input type='button' name='seguir' id='seguir' class='button blue' value='PROCESAR' onclick='this.form.submit();'>
	</form>
	</body>";
}

function set_costo_detalle_factura()
{
	include('inc/gpos.php');
	html();
	echo "<body>";
	//print_r($_POST);
//	$Idfactura=qo1("select id from factura where consecutivo=$Factura");
	$Detalles=explode('|',$Cadena);
	foreach($Detalles as $Detalle)
	{
		$Parte=explode(':',$Detalle);
		$Consecutivo=$Parte[0];$idServicio=$Parte[1];$Costo=$Parte[2];
		echo "<br>Consecutivo: $Consecutivo idServicio: $idServicio = $Costo";
		$Cantidad_detalle=qo1("select count(d.id) as cantidad from facturad d,factura f where f.id=d.factura and f.consecutivo='$Consecutivo' ");
		$Cantidad_ceroidservicio=qo1("select count(d.id) as cantidad from facturad d,factura f where f.id=d.factura and f.consecutivo='$Consecutivo' and d.idservicio=0");
		if($Cantidad_detalle==1 && $Cantidad_ceroidservicio==1) q("update facturad d,factura f set d.costo=$Costo,d.idservicio=$idServicio where d.factura=f.id and f.consecutivo=$Consecutivo");
		else q("update facturad d,factura f set d.costo=$Costo where d.factura=f.id and f.consecutivo=$Consecutivo and d.idservicio=$idServicio ");
	}
	echo "<script language='javascript'>alert('Actualizacion realizada con exito');parent.cerrar();</script></body>";
}

function obtener_ultima_factura()
{
	include('inc/gpos.php');
	$Ultimo=qo1("select max(consecutivo) from factura")+1;
	echo "<body><form action='http://www.aoasemuevecontigo.com/zfacturacion_individual.php' method='post' target='_self' name='forma' id='forma'>
		<input type='hidden' name='Acc' value='recibir_ultima_factura'>
		<input type='hidden' name='ultima' value='$Ultimo'>
	</form>
	<script language='javascript'>document.forma.submit()</script></body>";
}

function obtener_ultima_factura_aoato()
{
	include('inc/gpos.php');
	$Ultimo=qo1("select max(consecutivo) from factura")+1;
	echo "<body><form action='http://www.aoasemuevecontigo.com/zfacturacion_aoato.php' method='post' target='_self' name='forma' id='forma'>
		<input type='hidden' name='Acc' value='recibir_ultima_factura'>
		<input type='hidden' name='ultima' value='$Ultimo'>
	</form>
	<script language='javascript'>document.forma.submit()</script></body>";
}

function inserta_factura_individual()
{
	include('inc/gpos.php');
	html();
	$IDCONCEPTO=42;
	echo "<body>";
	$Registros=explode('|',$Data);
	$Data_retorno='';
	foreach($Registros as $Registro)
	{
		echo "<br>$Registro";
		$Parte=explode(';',$Registro);
		$Cliente=$Parte[0];
		$Aseguradora=$Parte[1];$Fecha_emision=$Parte[2];$Fecha_vencimiento=$Parte[3];$Aprobado_por=$Parte[4];$Neto=$Parte[5];$Numero_expediente=$Parte[6];$Centro_operacion=$Parte[7];
		$Idconductor=$Parte[8];$IdServicio=$Parte[9];$Descripcion=$Parte[10];
		// SI LA ASEGURADORA ES 38 significa que es un servicio particular y se debe verificar la pre-existencia del cliente
		if($Aseguradora==38)
		{
			$cliente_identificacion=$Parte[11];$cliente_tipo_id='CC';$cliente_nombre=$Parte[12];$cliente_apellido=$Parte[13];$cliente_lugar_expdoc='BOGOTA';
			$cliente_pais='CO';$cliente_ciudad=$Parte[18];$cliente_telefono_oficina=$Parte[14];$cliente_telefono_casa=$Parte[15];$cliente_celular=$Parte[14];
			$cliente_email_e=$Parte[17];$cliente_observaciones='Insertado desde Movilidad';$cliente_direccion=$Parte[16];$cliente_sexo='M';$cliente_tipo_persona='01';
			// VERIFICACION DE PRE-EXISTENCIA DEL CLIENTE
			if(!$Cliente=qo1("select id from cliente where identificacion='$cliente_identificacion' "))
			{
				$Cliente=q("insert into cliente (identificacion,tipo_id,nombre,apellido,lugar_expdoc,pais,ciudad,telefono_oficina,telefono_casa,celular,email_e,observaciones,direccion,sexo,tipo_persona) values
					('$cliente_identificacion','$cliente_tipo_id','$cliente_nombre','$cliente_apellido','$cliente_lugar_expdoc','$cliente_pais','$cliente_ciudad',
					'$cliente_telefono_oficina','$cliente_telefono_casa','$cliente_celular','$cliente_email_e','$cliente_observaciones','$cliente_direccion','$cliente_sexo',
					'$cliente_tipo_persona') ");
			}
			$Aseguradora=38;
		}
		$Iva=round($Neto*.16,0);
		$Total=$Neto+$Iva;
		
		$Consecutivo=qo1("select max(consecutivo) from factura")+1;
		$idfac=q("insert into factura (consecutivo,cliente,aseguradora,fecha_emision,movilidad,fecha_vencimiento,autorizadopor,subtotal,iva,total,observaciones) values
				('$Consecutivo','$Cliente','$Aseguradora','$Fecha_emision',1,'$Fecha_vencimiento','$Aprobado_por','$Neto','$Iva','$Total','Expediente número $Numero_expediente') ");
		q("update cfg_factura set consecutivo_aoa='$Consecutivo' where activo=1");
		$Data_retorno.=($Data_retorno?'|':'')."$Numero_expediente;$Consecutivo;$IdServicio";
		q("insert into facturad (factura,concepto,cantidad,unitario,iva,total,centro_operacion,conductor,descripcion,idservicio) values ('$idfac','$IDCONCEPTO','1','$Neto','$Iva','$Total','$Centro_operacion','$Idconductor',\"$Descripcion\",'$IdServicio')");
	}
	echo "
	<form action='http://www.aoasemuevecontigo.com/zfacturacion_individual.php' target='_self' method='POST' name='forma' id='forma'>
		<input type='hidden' name='Acc' value='confirmar_factura_individual'>
		<input type='hidden' name='Data_retorno' value='$Data_retorno'>
	</form>
	<script language='javascript'>document.forma.submit();</script>
	</body>";
}

function inserta_factura_masiva()
{
	include('inc/gpos.php');
	html();
	$IDCONCEPTO=42;
	echo "<body> Servicios x factura: $Cxf <br>Informacion recibida: <br><br>$Data";
	$Registros=explode('|',$Data);
	$Data_retorno='';
	$Contador_servicios=0;
	foreach($Registros as $Registro)
	{
		echo "<br>$Registro";
		
		$Parte=explode(';',$Registro);
		$Cliente=$Parte[0];
		$Aseguradora=$Parte[1];
		$Fecha_emision=$Parte[2];
		$Fecha_vencimiento=$Parte[3];
		$Aprobado_por=$Parte[4];
		$Neto=$Parte[5];
		$Numero_expediente=$Parte[6];
		$Centro_operacion=$Parte[7];
		$Idconductor=$Parte[8];
		$IdServicio=$Parte[9];
		$idTercero=$Parte[10];
		$Iva=round($Neto*.16,0);
		$Total=$Neto+$Iva;
		if($Contador_servicios==0)
		{
			if($Data_retorno) 
			{
				$Totales=qo("select sum(unitario) as neto,sum(iva) as iva, sum(total) as total from facturad where factura='$idfac' ");
				q("update factura set subtotal='$Totales->neto',iva='$Totales->iva',total='$Totales->total' where id='$idfac' ");
			}
			$Consecutivo=qo1("select max(consecutivo) from factura")+1;
			$idfac=q("insert into factura (consecutivo,cliente,aseguradora,fecha_emision,movilidad,fecha_vencimiento,autorizadopor) values
				('$Consecutivo','$Cliente','$Aseguradora','$Fecha_emision',1,'$Fecha_vencimiento','$Aprobado_por')");
			q("update cfg_factura set consecutivo_aoa='$Consecutivo' where activo=1");
		}
		$Contador_servicios++;
		$Data_retorno.=($Data_retorno?'|':'')."$Numero_expediente;$Consecutivo;$IdServicio";
		q("insert into facturad (factura,concepto,cantidad,unitario,iva,total,centro_operacion,conductor,idservicio,tercero_uno,descripcion) 
		values ('$idfac','$IDCONCEPTO','1','$Neto','$Iva','$Total','$Centro_operacion','$Idconductor','$IdServicio','$idTercero','Expediente $Numero_expediente')");
		if($Contador_servicios>=$Cxf) $Contador_servicios=0;
	}
	echo "
	<form action='http://www.aoasemuevecontigo.com/zfacturacion_masiva.php' target='_self' method='POST' name='forma' id='forma'>
		<input type='hidden' name='Acc' value='confirmar_factura_masiva'>
		<input type='hidden' name='Data_retorno' value='$Data_retorno'>
	</form>
	<script language='javascript'>
		alert('Presione cualquier tecla para continuar con el proceso.');
		 document.forma.submit();
	</script>
	</body>";
}

function inserta_factura_masiva_aoato()
{
	include('inc/gpos.php');
	html();
	$IDCONCEPTO=46;
	echo "<body> Servicios x factura: $Cxf <br>Informacion recibida: <br><br>$Data";
	$Registros=explode('|',$Data);
	
	//print_r($Registros);
	//exit;
	
	
	$Data_retorno='';
	$Contador_servicios=0;
	foreach($Registros as $Registro)
	{
		//echo "<br>$Registro";
		$Parte=explode(';',$Registro);
		$cliente_identificacion=$Parte[11];
		$cliente_tipo_id='CC';
		$cliente_nombre=$Parte[12];
		$cliente_apellido=$Parte[13];
		$cliente_lugar_expdoc='BOGOTA';
		$cliente_pais='CO';
		$cliente_ciudad=$Parte[18];
		$cliente_telefono_oficina=$Parte[14];
		$cliente_telefono_casa=$Parte[15];
		$cliente_celular=$Parte[14];
		$cliente_email_e=$Parte[17];
		$cliente_observaciones='Insertado desde Movilidad';
		$cliente_direccion=$Parte[16];
		$cliente_sexo='M';
		$cliente_tipo_persona='01';
		// VERIFICACION DE PRE-EXISTENCIA DEL CLIENTE
		if(!$Cliente=qo1("select id from cliente where identificacion='$cliente_identificacion' "))
		{
			$Cliente=q("insert into cliente (identificacion,tipo_id,nombre,apellido,lugar_expdoc,pais,ciudad,telefono_oficina,telefono_casa,celular,email_e,observaciones,direccion,sexo,tipo_persona) values
					('$cliente_identificacion','$cliente_tipo_id','$cliente_nombre','$cliente_apellido','$cliente_lugar_expdoc','$cliente_pais','$cliente_ciudad',
					'$cliente_telefono_oficina','$cliente_telefono_casa','$cliente_celular','$cliente_email_e','$cliente_observaciones','$cliente_direccion','$cliente_sexo',
					'$cliente_tipo_persona') ");
		}
		
		if(!$Oficina_operaciones=qo1("select id from oficina where ciudad='$cliente_ciudad' ")) $Oficina_operaciones=1;
		
		$Aseguradora=$Parte[1];
		$Fecha_emision=$Parte[2];
		$Fecha_vencimiento=$Parte[3];
		$Aprobado_por=$Parte[4];
		$Neto=$Parte[5];
		$Numero_expediente=$Parte[6];
		$Centro_operacion=$Parte[7];
		$Idconductor=$Parte[8];
		$IdServicio=$Parte[9]; 
		$Descripcion=$Parte[10];
		
		//$Iva=round($Neto*.16,0);
		$Iva=0;
		$Total=$Neto+$Iva;
		
		//$Consecutivo=qo1("select max(consecutivo) from factura")+1;
		
		$Consecutivo = generar_consecutivo_prefijo();		
		
		$sql = "insert into factura (consecutivo,cliente,aseguradora,fecha_emision,movilidad,fecha_vencimiento,subtotal,iva,total,observaciones,oficina) values
				('$Consecutivo','$Cliente','$Aseguradora','$Fecha_emision',1,'$Fecha_vencimiento','$Neto','$Iva','$Total','Expediente número $Numero_expediente','$Oficina_operaciones') ";	
		
		
		$idfac=q($sql);
		
		
		//q("update cfg_factura set consecutivo_aoa='$Consecutivo' where activo=1");
		
		
		$Data_retorno.=($Data_retorno?'|':'')."$Numero_expediente;$Consecutivo;$IdServicio";
		
		//echo "data retorno ".$Data_retorno;
		
		//exit;
		
		q("insert into facturad (factura,concepto,cantidad,unitario,iva,total,centro_operacion,conductor,descripcion,idservicio) values ('$idfac','$IDCONCEPTO','1','$Neto','$Iva','$Total','$Centro_operacion','$Idconductor',\"$Descripcion\",'$IdServicio')");
	}
	echo "
	<form action='http://www.aoasemuevecontigo.com/zfacturacion_aoato.php' target='_self' method='POST' name='forma' id='forma'>
		<input type='hidden' name='Acc' value='confirmar_factura_masiva'>
		<input type='hidden' name='Data_retorno' value='$Data_retorno'>
	</form>
	<script language='javascript'>
		alert('Presione cualquier tecla para continuar con el proceso.');
		document.forma.submit();
	</script>
	</body>";
}

function generar_consecutivo_prefijo()
{
	$resolucion_factura = qo("Select * from resolucion_factura order by fecha desc limit 1");
	$factura = qo("Select * from factura where consecutivo like  '%".$resolucion_factura->prefijo."%' order by id desc LIMIT 1 ");
	if($factura)
	{
		 $g_cons = str_ireplace($resolucion_factura->prefijo,"",$factura->consecutivo);
		 $g_cons += 1;		 
		 
		 
		 $g_cons =  $resolucion_factura->prefijo."".$g_cons; 
		 
		 
	}
	else
	{
		$g_cons = $resolucion_factura->prefijo."".$resolucion_factura->consecutivo_inicial;
	}
	return $g_cons;
}

function reinserta_factura_masiva()
{
	include('inc/gpos.php');
	$idfac=qo1("select id from factura where consecutivo=$FAC");
	html();
	$IDCONCEPTO=42;
	
	echo "<body> Retransmisión masiva de la factura: $FAC id: $idfac";
	q("delete from facturad where factura=$idfac");
	$Registros=explode('|',$Data);
	$Contador=0;
	foreach($Registros as $Registro)
	{
		$Contador++;
		echo "<br>$Contador : $Registro";
		$Parte=explode(';',$Registro);
	//	print_r($Parte);
		$Neto=$Parte[0];
		$Numero_expediente=$Parte[1];
		$Centro_operacion=$Parte[2];
		$Idconductor=$Parte[3];
		$IdServicio=$Parte[4];
		$idTercero=$Parte[5];
		$Iva=round($Neto*.16,0);
		$Total=$Neto+$Iva;
		q("insert into facturad (factura,concepto,cantidad,unitario,iva,total,centro_operacion,conductor,idservicio,tercero_uno,descripcion) 
		values ('$idfac','$IDCONCEPTO','1','$Neto','$Iva','$Total','$Centro_operacion','$Idconductor','$IdServicio','$idTercero','Expediente $Numero_expediente')");
	}
	echo "</body>";
}


function reliquidar_factura()
{
	global $id;
	$D=qo("select sum(cantidad*unitario) as subtotal,sum(iva) as iva, sum(total) as total from facturad where factura='$id' ");
	q("update factura set subtotal=$D->subtotal,iva=$D->iva,total=$D->total where id='$id' ");
	echo "<body><script language='javascript'>window.close();void(null);</script></body>";
}

function actualiza_siniestro_vip()
{
	include('inc/gpos.php');
	Q("update siniestro set estado=$nuevo_estado where id=$id_siniestro");
	q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."',
			'".date('s')."','$nick','$nombre','$Nombre_tabla','$Accion','$Registro','$ip','Cambia a nuevo estado desde Movilidad')");
	echo "<body><script language='javascript'>window.open('http://www.aoasemuevecontigo.com/util.php?Acc=regreso_actualizacion_vip','_self');</script></body>";
}




















?>
