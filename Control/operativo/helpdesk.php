<?php

/******  PROGRAMA DE HELP DESK **********  */
include('inc/funciones_.php');
sesion();
$BDA='aoacol_administra';
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');	die();}

solicitud();

function solicitud()
{
	global $BDA;
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
		<input type='hidden' name='Acc' value='insertar_tiket'>
	</form>
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
	global $usuario,$empleado,$modulo,$descripcion,$prioridad,$estado,$email_usuario,$BDA;
	$Hoy=date('Y-m-d H:i:s');
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



?>