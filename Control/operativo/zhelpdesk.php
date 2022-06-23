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
	echo "<body><h3>HELP DESK - CREACION DE TIKET</h3>
	<form action='' target='_self' method='POST' name='forma' id='forma'>
		<table>
			<tr><td>Usuario:</td><td><input type='text' name='usuario' id='usuario' value='$Nusuario' size='50' maxlength='50' readonly></td></tr>
			<tr><td>Email Usuario:</td><td><input type='text' name='email_usuario' id='email_usuario' value='$Email_usuario' size='50' maxlength='200' readonly></td></tr>
			<tr><td>Empleado:</td><td>".menu1("empleado","select id,$BDA.t_empleado(empleado) as nemp from $BDA.gh_contrato where fecha_final='0000-00-00' or fecha_final>='$Hoy' order by nemp",0,1)."</td></tr>
			<tr><td>Módulo</td><td><select name='modulo'><option></option>";
	$Grupos=q("select distinct $BDA.t_hd_modulo_tipo(tipo) as ntipo from $BDA.hd_modulo order by ntipo");		
	while($G=mysql_fetch_object($Grupos))
	{
		echo "<optgroup label='$G->ntipo'>";
		$Opcs=q("select m.id,m.nombre from $BDA.hd_modulo m ,$BDA.hd_modulo_tipo t where m.tipo=t.id and t.nombre = '$G->ntipo' order by m.nombre");
		while($O=mysql_fetch_object($Opcs))
		{
			echo "<option value='$O->id'>$O->nombre</option>";
		}
		echo "</optgroup>";
	}
//			.menu1("modulo","select id,concat(t_hd_modulo_tipo(tipo),' - ',nombre) as ntipo,nombre from hd_modulo order by ntipo,nombre",0,1).
	echo "</select></td></tr>	
			<tr><td valign='top'>Descripción</td><td><textarea name='descripcion' cols=100 rows=4 style='font-family:arial;font-size:11px'></textarea></td></tr>
			<tr><td>Prioridad:</td><td><select name='prioridad'><option value=''></option>";
	$Prioridades=q("select * from $BDA.hd_prioridad_tiket order by id");
	while($Pri=mysql_fetch_object($Prioridades))
	{
		echo "<option value='$Pri->id' style='background-color:$Pri->color_co;'>$Pri->nombre</option>";
	}
	echo "</select></td></tr>
			<tr><td>Estado:</td><td>".menu1("estado","select id,nombre from $BDA.hd_estado_tiket where id=1",1,0)."</td></tr>
			<tr><td colspan='2' align='center'><input type='button' name='enviar' id='enviar' value=' CREAR TIKET ' onclick='valida_envio_tiket();'></td></tr>
		</table>
		
	</form></body>";
}



?>