<?php

/******  PROGRAMA DE HELP DESK **********  */
include('inc/funciones_.php');
sesion();
$BDA='aoacol_administra';
$User=$_SESSION['User'];
$Nuser=$_SESSION['Nombre'];
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');	die();}

solicitud();

function solicitud()
{
	global $BDA,$User;
	$Nusuario=$_SESSION['Nombre'];
	$Email_usuario=usuario('email');
	$Hoy=date('Y-m-d');
	html();
	echo "<script language='javascript'>
	function valida_envio_tiket()
	{
		with(document.forma)
		{
			if(!empleado.value) {alert('Debe seleccionar un empleado'); empleado.style.backgroundColor='ffffaa';empleado.focus();return false;}
			if(!modulo.value) {alert('Debe seleccionar un módulo'); modulo.style.backgroundColor='ffffaa';modulo.focus();return false;}
			if(!alltrim(descripcion.value)) {alert('Debe digitar una descripcion'); descripcion.style.backgroundColor='ffffaa';descripcion.focus();return false;}
			if(!prioridad.value) {alert('Debe seleccionar una prioridad'); prioridad.style.backgroundColor='ffffaa';prioridad.focus();return false;}
			if(confirm('Seguro de insertar este tiket?')) submit();
		}
	}
	function recargar()
	{
		window.open('helpdesk.php','_self');
	}</script>
	<body><script language='javascript'>window.resizeTo(750,500);</script>
	<h3><img src='gifs/helpdesk.png' height='50'> HELP DESK - CREACION DE TIKET</h3>
	<form action='helpdesk.php' target='Oculto_hd' method='POST' name='forma' id='forma'>
		<table>
			<tr><td align='right'>Usuario:</td><td><input type='text' name='usuario' id='usuario' value='$Nusuario' size='50' maxlength='50' readonly></td></tr>
			<tr><td align='right'>Email Usuario:</td><td><input type='text' name='email_usuario' id='email_usuario' value='$Email_usuario' size='50' maxlength='200' readonly></td></tr>
			<tr><td align='right'>Empleado (usted):</td><td alt='Seleccione el empleado al cual pertenece el tiket' title='Seleccione el empleado al cual pertenece el tiket'>".
						menu1("empleado","select id,$BDA.t_empleado(empleado) as nemp from $BDA.gh_contrato where fecha_final='0000-00-00' or fecha_final>='$Hoy' order by nemp",0,1)."</td></tr>
			<tr><td align='right'>Módulo:</td><td alt='Seleccione el Módulo al cual pertenece el tiket' title='Seleccione el módulo al cual pertenece el tiket'><select name='modulo'><option></option>";
	$Grupos=q("select distinct $BDA.t_hd_modulo_tipo(tipo) as ntipo from $BDA.hd_modulo order by ntipo");		
	while($G=mysql_fetch_object($Grupos))
	{
		echo "<optgroup label='$G->ntipo'>";
		$Opcs=q("select m.id,m.nombre from $BDA.hd_modulo m ,$BDA.hd_modulo_tipo t where m.tipo=t.id and t.nombre = '$G->ntipo' order by m.nombre");
		while($O=mysql_fetch_object($Opcs))	echo "<option value='$O->id'>$O->nombre</option>";
		echo "</optgroup>";
	}
	echo "</select></td></tr>	
			<tr><td valign='top'  align='right'>Descripción:</td><td><textarea name='descripcion' cols=100 rows=4 style='font-family:arial;font-size:11px'></textarea></td></tr>
			<tr><td align='right'>Prioridad:</td><td><select name='prioridad'><option value=''></option>";
	$Prioridades=q("select * from $BDA.hd_prioridad_tiket order by id");
	while($Pri=mysql_fetch_object($Prioridades)) echo "<option value='$Pri->id' style='background-color:$Pri->color_co;'>$Pri->nombre</option>";
	echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;Estado:&nbsp;&nbsp;".menu1("estado","select id,nombre from $BDA.hd_estado_tiket where id=1",1,0)."</td></tr>
			<tr><td colspan='2' align='center'><input type='button' name='enviar' id='enviar' value=' CREAR TIKET ' onclick='valida_envio_tiket();' 
				style='font-weight:bold;font-size:16px'></td></tr>
		</table>
		<input type='hidden' name='Acc' value='insertar_tiket'>";
	if($User==9 /* helpdesk */)
	{
		echo "<br><hr><b>Zona HelpDesk</b> para captura de tikets de meses anteriores:<br>Fecha de Ingreso: ".pinta_fc('forma','FC')."  <input type='checkbox' name='Cerrado'> Cerrado ";
	}
	echo "</form>
	<iframe name='Oculto_hd' id='Oculto_hd' style='visibility:hidden' width='1' height='1'></iframe>
	<hr><h3>Estado de Tikets para $Nusuario</h3>";
	if($Tikets=q("select count(id) as cantidad, $BDA.t_hd_estado_tiket(estado) as nestado from $BDA.hd_tiket where usuario='$Nusuario' group by nestado order by nestado"))
	{
		echo "<table border=0 cellspacing='0'><tr>
			<th>Estado</th>
			<th>Cantidad</th>
			</tr>";
		while($T =mysql_fetch_object($Tikets ))
		{
			echo "<tr>
			<td>$T->nestado</td>
			<td align='right'>$T->cantidad</td>
			</tr>";
		}
		echo "</table>";
	}
	else
	echo "No tiene tikets registrados.";
	echo "</body>";
}

function insertar_tiket()
{
	global $usuario,$empleado,$modulo,$descripcion,$prioridad,$estado,$email_usuario,$BDA,$FC,$Cerrado;
	$Cerrado=sino($Cerrado);
	$Hoy=date('Y-m-d H:i:s');
	if($Cerrado)
	{
		$fingreso=$FC.' 12:00:00'; $frevisado=$FC.' 12:05:00'; $fproceso=$FC.' 12:10:00'; $fcierre=$FC.' 13:00:00';
		$idn=q("insert into $BDA.hd_tiket (usuario,empleado,modulo,descripcion,prioridad,estado,fecha_ingreso,fecha_revisado,fecha_proceso,fecha_cierre,cerrado_por) 
		values ('$usuario','$empleado','$modulo','$descripcion','$prioridad',4,'$fingreso','$frevisado','$fproceso','$fcierre',".$_SESSION['Id_alterno']);
		$fidn=str_pad($idn,5,'0',STR_PAD_LEFT);
		echo "<body><script language='javascript'>alert('Tiket creado Numero $fidn');parent.recargar();</script></body>";
	}
	else
	{
		$idn=q("insert into $BDA.hd_tiket (usuario,empleado,modulo,descripcion,prioridad,estado,fecha_ingreso) values 
		('$usuario','$empleado','$modulo',\"$descripcion\",'$prioridad','$estado','$Hoy')");
		$fidn=str_pad($idn,5,'0',STR_PAD_LEFT);
		$Funcionario=qo("select u.usuario,u.nombre,u.email from $BDA.hd_modulo m,$BDA.usuario_helpdesk u where m.id=$modulo and u.id=m.funcionario ");
		$NT=qo1("select id from $BDA.usuario_tab where usuario=9 and tabla='hd_tiket' ");
		$Envio1=enviar_gmail($email_usuario /*de */,
					$usuario /*Nombre de */ ,
					"$Funcionario->email,$Funcionario->nombre" /*para */,
					"" /*con copia*/,
					"INGRESO DE TIKET NUMERO $fidn" /*Objeto */,
					"<body><b>Ingreso de Tiket Número $fidn</b><br>
					Puede ver el tiket desde el siguiente link, pero si habia ingresado previamente con un perfil distinto de Help Desk, debe cambiarse de perfil o salir del sistema Administrativo.
					<br><a href='http://app.aoacolombia.com/Administrativo/marcoindex.php?Acc=mod_reg&Num_Tabla=$NT&id=$idn' target='_blank'>Click aqui para ver el Tiket</a>
					<br>
					</body>");
		echo "<body><script language='javascript'>alert('Tiket creado Numero: $fidn Email enviado a $Funcionario->email');parent.recargar();</script></body>";
	}
}

function control_helpdesk()
{
	global $Capav;
	if(!$Capav) $Capav=1;
	$Nusuario=$_SESSION['Nombre'];
	html('CONTROL DE HELP DESK');
	$Prioridades=q("select * from hd_prioridad_tiket");
	$APrioridades=array();
	while($P=mysql_fetch_object($Prioridades))	$APrioridades[$P->id]=$P->color_co;
	
	echo "<script language='javascript'>
	var Capav=$Capav;
	function cambia_estado(id,estado)
	{if(confirm('Desea cambiar el estado de este tiket?'))window.open('helpdesk.php?Acc=cambia_estado&id='+id+'&estado='+estado,'Oculto_hd');}
	function recargar(){window.open('helpdesk.php?Acc=control_helpdesk&Capav='+Capav,'_self');}
	Recargar=setTimeout(recargar,360000);
	function adiciona_observaciones(dato)
	{modal('helpdesk.php?Acc=adiciona_observaciones&id='+dato,0,0,500,700,'obs');}
	function ve_capa_solicitados()
	{	Capav=1;
		document.getElementById('cp_solicitados').style.visibility='visible';
		document.getElementById('cp_revisados').style.visibility='hidden';
		document.getElementById('cp_proceso').style.visibility='hidden';
		document.getElementById('cp_finalizados').style.visibility='hidden';
		document.getElementById('cp_estadistica').style.visibility='hidden';}
	function ve_capa_revisados()
	{	Capav=2;
		document.getElementById('cp_solicitados').style.visibility='hidden';
		document.getElementById('cp_revisados').style.visibility='visible';
		document.getElementById('cp_proceso').style.visibility='hidden';
		document.getElementById('cp_finalizados').style.visibility='hidden';
		document.getElementById('cp_estadistica').style.visibility='hidden';}
	function ve_capa_proceso()
	{	Capav=3;
		document.getElementById('cp_solicitados').style.visibility='hidden';
		document.getElementById('cp_revisados').style.visibility='hidden';
		document.getElementById('cp_proceso').style.visibility='visible';
		document.getElementById('cp_finalizados').style.visibility='hidden';
		document.getElementById('cp_estadistica').style.visibility='hidden';}
	function ve_capa_finalizados()
	{	Capav=4;
		document.getElementById('cp_solicitados').style.visibility='hidden';
		document.getElementById('cp_revisados').style.visibility='hidden';
		document.getElementById('cp_proceso').style.visibility='hidden';
		document.getElementById('cp_finalizados').style.visibility='visible';
		document.getElementById('cp_estadistica').style.visibility='hidden';}
	function ve_capa_estadistica()
	{	Capav=5;
		document.getElementById('cp_solicitados').style.visibility='hidden';
		document.getElementById('cp_revisados').style.visibility='hidden';
		document.getElementById('cp_proceso').style.visibility='hidden';
		document.getElementById('cp_finalizados').style.visibility='hidden';
		document.getElementById('cp_estadistica').style.visibility='visible';}
	</script><body><h3>CENTRO DE CONTROL - HELP DESK</h3>";
	echo "";
	echo "<table bgcolor='000000'><tr bgcolor='dddddd'><td style='cursor:pointer;font-size:20px' bgcolor='ffffff' onclick='ve_capa_solicitados()'>Solicitados</td>
	<td style='cursor:pointer;font-size:20px' bgcolor='ffffff' onclick='ve_capa_revisados()'>Revisados</td>
	<td style='cursor:pointer;font-size:20px' bgcolor='ffffff' onclick='ve_capa_proceso()'>En Proceso</td>
	<td style='cursor:pointer;font-size:20px' bgcolor='ffffff' onclick='ve_capa_finalizados()'>Finalizados</td>
	<td style='cursor:pointer;font-size:20px' bgcolor='ffffff' onclick='ve_capa_estadistica()'>Estadistica</td></tr></table>
	<br>
	<div id='cp_solicitados' style='visibility:hidden;position:absolute'>".chd_solicitados($APrioridades)."</div>
	<div id='cp_revisados' style='visibility:hidden;position:absolute'>".chd_revisados($APrioridades)."</div>
	<div id='cp_proceso' style='visibility:hidden;position:absolute'>".chd_enproceso($APrioridades)."</div>
	<div id='cp_finalizados' style='visibility:hidden;position:absolute'>".chd_cerrados($APrioridades)."</div>
	<iframe name='Oculto_hd' id='Oculto_hd' style='visibility:hidden' width='1' height='1'></iframe><br>
	<div id='cp_estadistica' style='visibility:hidden;position:absolute'><h3>RESUMEN DE HELPDESK POR MODULO</H3>";
	$Conteo1=q("select t_hd_modulo(modulo) as nmodulo,count(id) as cantidad from hd_tiket group by nmodulo order by nmodulo");
	$Cont=array();
	while($C=mysql_fetch_object($Conteo1)) $Cont["$C->nmodulo"]=$C->cantidad;
	
	$Total1=0;
	echo "<table cellspacing=1><tr><th colspan=2>MODULOS</th></tr>";
	foreach($Cont as $Llave => $cantidad) {echo "<tr><td bgcolor='ffffff'><b>$Llave</b></td><td bgcolor='ffffff' align='right'><b>".coma_format($cantidad)."</b></td></tr>";$Total1+=$cantidad;}
	echo "<tr><td bgcolor='ffffff'><b>TOTAL:</b></td><td bgcolor='ffffff' align='right'><b>".coma_format($Total1)."</b></td></tr></table>";
	
	$Conteo1=q("select t_hd_estado_tiket(estado) as nestado,count(id) as cantidad from hd_tiket group by estado order by estado");
	$Cont=array();
	while($C=mysql_fetch_object($Conteo1)) $Cont["$C->nestado"]=$C->cantidad;
	$Conteo2=q("select t_hd_prioridad_tiket(prioridad) as nprioridad,count(id) as cantidad from hd_tiket group by prioridad order by prioridad");
	$Prior=array();
	while($C=mysql_fetch_object($Conteo2)) $Prior["$C->nprioridad"]=$C->cantidad;
	$Conteo3=q("select t_gh_contrato(empleado) as nemp,count(id) as cantidad from hd_tiket group by nemp order by nemp");
	$Emp=array();
	while($C=mysql_fetch_object($Conteo3)) $Emp["$C->nemp"]=$C->cantidad;
	$Conteo4=q("select t_usuario_helpdesk(cerrado_por) as nfunc,count(id) as cantidad from hd_tiket group by nfunc order by nfunc");
	$Func=array();
	while($C=mysql_fetch_object($Conteo4)) $Func["$C->nfunc"]=$C->cantidad;
	
	
	echo "<table cellspacing='3'><tr><td valign='top'>";
	$Total1=0;
	echo "<table cellspacing=1><tr><th colspan=2>ESTADOS</th></tr>";
	foreach($Cont as $Llave => $cantidad) {echo "<tr><td bgcolor='ccccff'><b>$Llave</b></td><td bgcolor='ccccff' align='right'><b>".coma_format($cantidad)."</b></td></tr>";$Total1+=$cantidad;}
	echo "<tr><td bgcolor='ccccff'><b>TOTAL:</b></td><td bgcolor='ccccff' align='right'><b>".coma_format($Total1)."</b></td></tr></table>";
	
	echo "</td><td valign='top'>";
	$Total2=0;
	echo "<table cellspacing=1><tr><th colspan=2>PRIORIDADES:</th></tr>";
	foreach($Prior as $Llave => $cantidad) {echo "<tr><td bgcolor='ddffdd'><b>$Llave</b></td><td  bgcolor='ddffdd' align='right'><b>".coma_format($cantidad)."</b></td></tr>";$Total2+=$cantidad;}
	echo "<tr><td bgcolor='ddffdd'><b>TOTAL:</b></td><td bgcolor='ddffdd' align='right'><b>".coma_format($Total2)."</b></td></tr></table>";
	
	echo "</td><td valign='top'>";
	$Total3=0;
	echo "<table cellspacing='4'><tr><th colspan=2>EMPLEADOS:</th></tr>";
	foreach($Emp as $Llave => $cantidad) {echo "<tr><td bgcolor='ddffff'><b>$Llave</b></td><td bgcolor='ddffff' align='right'><b>".coma_format($cantidad)."</b></td></tr>";$Total3+=$cantidad;}
	echo "<tr><td bgcolor='ddffff'><b>TOTAL:</b></td><td bgcolor='ddffff' align='right'><b>".coma_format($Total3)."</b></td></tr></table>";
	
	echo "</td><td valign='top'>";
	$Total4=0;
	echo "<table cellspacing='4'><tr><th colspan=2>FUNCIONARIOS HELPDESK:</th></tr>";
	foreach($Func as $Llave => $cantidad) {echo "<tr><td  bgcolor='ddccdd' ><b>$Llave</b></td><td  bgcolor='ddccdd' align='right'><b>".coma_format($cantidad)."</b></td></tr>";$Total4+=$cantidad;}
	echo "<tr><td bgcolor='ddccdd'><b>TOTAL:</b></td><td bgcolor='ddccdd' align='right'><b>".coma_format($Total4)."</b></td></tr></table>";

	echo "</tr></table></div>
	<script language='javascript'>
	if(Capav==1) ve_capa_solicitados();
	if(Capav==2) ve_capa_revisados();
	if(Capav==3) ve_capa_proceso();
	if(Capav==4) ve_capa_finalizados();
	if(Capav==5) ve_capa_estadistica();
	</script>";
	
	echo "</body>";
}

function cambia_estado()
{
	global $id,$estado;
	$idfunc=$_SESSION['Id_alterno'];
	$Ahora=date('Y-m-d H:i:s');
	if($estado==2) $Campo_fecha="fecha_revisado";
	if($estado==3) $Campo_fecha="fecha_proceso";
	if($estado==4) $Campo_fecha="fecha_cierre";
	q("update hd_tiket set estado='$estado', $Campo_fecha = '$Ahora', cerrado_por='$idfunc' where id='$id' ");
	graba_bitacora('hd_tiket','M',$id,"Cambio estado a $estado");
	echo "<body><script language='javascript'>parent.recargar();window.open('helpdesk.php?Acc=envio_actualizacion_tiket&id=$id','_self');</script></body>";
}

function chd_solicitados($P)
{
	$Resultado='';
	if($Solicitados=q("select *,t_gh_contrato(empleado) as nemp,t_hd_modulo(modulo) as nmod,t_hd_prioridad_tiket(prioridad) as nprioridad
		from hd_tiket where estado=1 order by prioridad desc"))
	{
		$Resultado.="<h3>SOLICITADOS</H3><table width='100%'><tr><th>No.Tiket</th><th>Empleado</th><th>Modulo</th><th>Prioridad</th><th>Descripcion</th><th>Acción</th></tr>";
		while($S=mysql_fetch_object($Solicitados))
		{
			$Resultado.="<tr><td>$S->id<br>$S->fecha_ingreso</td><td>$S->nemp</td><td>$S->nmod</td><td bgcolor='".$P[$S->prioridad]."'>$S->nprioridad</td><td>$S->descripcion</td>
			<td><a class='srinfo' style='cursor:pointer' onclick='cambia_estado($S->id,2);'><img src='gifs/standar/derecha.png'><span>pasar a Revisado</span></a></td></tr>";
		}
		$Resultado.="</table>";
	}
	else $Resultado.="<b>No hay Solicitudes pendientes</b>";
	return $Resultado;
}

function chd_revisados($P)
{
	global $Nuser;
	$Resultado='';
	if($Solicitados=q("select *,t_gh_contrato(empleado) as nemp,t_hd_modulo(modulo) as nmod,t_hd_prioridad_tiket(prioridad) as nprioridad,t_usuario_helpdesk(cerrado_por) as nfuncio
		from hd_tiket where estado=2 order by id desc"))
	{
		$Resultado.="<h3>REVISADOS</H3><table width='100%'><tr><th>No.Tiket</th><th>Empleado</th><th>Modulo</th><th>Prioridad</th><th>Descripcion</th><th>Revisado Por</th><th>Acción</th></tr>";
		while($S=mysql_fetch_object($Solicitados))
		{
			$Resultado.="<tr><td>$S->id<br>$S->fecha_revisado</td><td>$S->nemp</td><td>$S->nmod</td><td bgcolor='".$P[$S->prioridad]."'>$S->nprioridad</td><td>$S->descripcion</td>
			<td ".($S->nfuncio==$Nuser?"style='background-color:ffeecc;color:000055;'":"").">$S->nfuncio</td>
			<td><a class='srinfo' style='cursor:pointer' onclick='cambia_estado($S->id,3);'><img src='gifs/standar/derecha.png'><span>pasar a En Proceso</span></a></td></tr>";
		}
		$Resultado.="</table>";
	}
	else $Resultado.="<b>No hay Solicitudes Revisadas</b>";
	return $Resultado;
}

function chd_enproceso($P)
{
	global $Nuser;
	$Resultado='';
	if($Solicitados=q("select *,t_gh_contrato(empleado) as nemp,t_hd_modulo(modulo) as nmod,t_hd_prioridad_tiket(prioridad) as nprioridad,t_usuario_helpdesk(cerrado_por) as nfuncio
		from hd_tiket where estado=3 order by id desc"))
	{
		$Resultado.="<h3>EN PROCESO</H3><table width='100%'><tr><th>No.Tiket</th><th>Empleado</th><th>Modulo</th><th>Prioridad</th><th>Descripcion</th><th>Observaciones</th>
							<th>Procesado Por</th><th>Acción</th></tr>";
		while($S=mysql_fetch_object($Solicitados))
		{
			$Resultado.="<tr><td>$S->id<br>$S->fecha_proceso</td><td>$S->nemp</td><td>$S->nmod</td><td bgcolor='".$P[$S->prioridad]."'>$S->nprioridad</td><td>$S->descripcion</td>
			<td>$S->observaciones</td><td ".($S->nfuncio==$Nuser?"style='background-color:ffeecc;color:000055;'":"").">$S->nfuncio</td>
			<td nowrap='yes'><a class='srinfo' style='cursor:pointer' onclick='adiciona_observaciones($S->id);'><img src='gifs/standar/nuevo_registro_blanco.png'><span>Observaciones</span></a>
			</td></tr>";
		}
		$Resultado.="</table>";
	}
	else $Resultado.="<b>No hay Solicitudes En Proceso</b>";
	return $Resultado;
}

function chd_cerrados($P)
{
	global $Nuser;
	$Resultado='';
	if($Solicitados=q("select *,t_gh_contrato(empleado) as nemp,t_hd_modulo(modulo) as nmod,t_hd_prioridad_tiket(prioridad) as nprioridad,
		t_usuario_helpdesk(cerrado_por) as nfuncio
		from hd_tiket where estado=4 order by fecha_cierre desc ,id desc limit 50"))
	{
		$Resultado.="<h3>FINALIZADOS Ultimos 50 cerrados </H3>
		<table width='100%'><tr><th>No.Tiket</th><th>Empleado</th><th>Modulo</th><th>Prioridad</th><th>Descripcion</th><th>Observaciones</th><th>Cerrado Por</th>
		<th>Fechas</th></tr>";
		$bgcolor='dddddd';
		while($S=mysql_fetch_object($Solicitados))
		{
			$bgcolor=($bgcolor=='dddddd'?'ffffff':'dddddd');
			$Resultado.="<tr bgcolor='$bgcolor'><td valign='top'>$S->id</td><td valign='top'>$S->nemp</td><td valign='top'>$S->nmod</td>
			<td valign='top' bgcolor='".$P[$S->prioridad]."'>$S->nprioridad</td><td valign='top'>$S->descripcion</td>
			<td valign='top'>$S->observaciones</td><td valign='top' ".($S->nfuncio==$Nuser?"style='background-color:ffeecc;color:000055;'":"").">$S->nfuncio</td>
			<td nowrap='yes' valign='top'>Ingreso: $S->fecha_ingreso<br>Revisión: $S->fecha_revisado<br>Proceso: $S->fecha_proceso<br>Cerrado: $S->fecha_cierre</td></tr>";
		}
		$Resultado.="</table>";
	}
	else $Resultado.="<b>No hay Solicitudes Cerradas</b>";
	return $Resultado;
}

function adiciona_observaciones()
{
	global $id;
	$D=qo("select * from hd_tiket where id=$id");
	html('ADICION DE OBSERVACIONES');
	echo "<script language='javascript'>
	function enviar_observaciones()
	{
		if(confirm('Desea guardar las observaciones?')) document.forma.submit();
	}
	</script><body><h3>ADICION DE OBSERVACIONES</h3>
	<b>Observaciones anteriores:</b><br>
	$D->observaciones<hr>
	<form action='helpdesk.php' target='_self' method='POST' name='forma' id='forma'>
		Observaciones:<br>
		<textarea name='observaciones' cols=100 rows=5 style='font-family:arial;font-size:11px'></textarea><br><br>
		<br><br><input type='checkbox' name='cerrar'> Cerrar el caso<br><br>
		<input type='button' name='grabar' id='grabar' value=' GUARDAR OBSERVACIONES ' style='font-size:14px;font-weight:bold;' onclick='enviar_observaciones()'>
		<input type='hidden' name='Acc' value='adiciona_observaciones_ok'><input type='hidden' name='id' value='$id'>
	</form></body>";
}

function adiciona_observaciones_ok()
{
	global $id,$observaciones,$cerrar;
	$cerrar=sino($cerrar);
	echo "<body>";
	$Ahora=date('Y-m-d H:i:s');
	if($observaciones)
	{
		q("update hd_tiket set observaciones=concat(observaciones,\"$observaciones\n\") where id=$id");
		graba_bitacora('hd_tiket','M',$id,"Adiciona observaciones");
		if($cerrar) q("update hd_tiket set estado=4,fecha_cierre='$Ahora' where id=$id");
		graba_bitacora('hd_tiket','M',$id,"Finaliza el caso del tiket");
		echo "<script language='javascript'>window.open('helpdesk.php?Acc=envio_actualizacion_tiket&id=$id','_self');</script>";
	}
	else echo "<script language='javascript'>window.close();void(null);</script></body>";
}

function envio_actualizacion_tiket()
{
	global $id;
	$fid=str_pad($id,5,'0',STR_PAD_LEFT);
	$D=qo("select *,t_hd_modulo(modulo) as nmod,t_hd_prioridad_tiket(prioridad) as nprioridad, t_hd_estado_tiket(estado) as nestado,
		t_usuario_helpdesk(cerrado_por) as uhd from hd_tiket where id=$id");
	if(!$Funcionario_hd=qo("select * from usuario_helpdesk where id=$D->cerrado_por")) die('Problemas obteniendo informacion del funcionario hd');
	$Empleado=qo("select e.correo_e as email,t_empleado(c.empleado) as nemp from gh_contrato c,empleado e where c.id=$D->empleado and c.empleado=e.id");
	$Envio1=enviar_gmail($Funcionario_hd->email /*de */,
				$Funcionario_hd->nombre /*Nombre de */ ,
				"$Empleado->email,$Empleado->nemp" /*para */,
				"$Funcionario_hd->email" /*con copia*/,
				"ACTUALIZACION DE TIKET NUMERO $fid" /*Objeto */,
				"<body><b>Actualización de Tiket Número $fid</b><br>
				Tiket insertado por: $D->usuario<br>
				Tiket asociado al empleado: $Empleado->nemp<br>
				Módulo: $D->nmod<br>
				Prioridad: $D->nprioridad<br>
				Descripción: $D->descripcion<br><br>
				Estado actual: $D->nestado<br><br>
				Observaciones:$D->observaciones<br><br>
				Ingreso: $D->fecha_ingreso Revisado: $D->fecha_revisado Proceso: $D->fecha_proceso Cierre: $D->fecha_cierre<br>
				Procesado por: $D->uhd<br><br><b>
				Cordialmente:<br><br>$Funcionario_hd->nombre<br><i>Departamento de Tecnología de la Información.</i></b>
				</body>");
	if($Envio1)
	echo "<body><script language='javascript'>alert('Envio satisfactorio a $Empleado->email de la actualización del tiket');window.close();void(null);opener.recargar();</script></body>";
	else echo "<body><script language='javascript'>alert('El envío del email de actualización falló al correo $Empleado->email');window.close();void(null);opener.recargar();</script></body>";
}









?>