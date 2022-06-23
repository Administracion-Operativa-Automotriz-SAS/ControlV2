<?php
########## PERFILES DE SEGURIDAD #############
# 2: ADMINISTRADOR
# 3: CAPTURA - CESAR
# 4: CALL CENTER - ALEXANDRA
# 5: AUTORIZACIONES - DIANA
# 6: ADJUDICACIONES  - PENDIENTE

include('inc/funciones_.php');
sesion();
if(inlist($_SESSION['User'],'8,11,14' /*aseguradora y aseguradora 1*/))
{
	if($_SESSION['User']==8) $Aseguradora=qo1("select aseguradora from usuario_aseguradora where id=".$_SESSION['Id_alterno']);
	elseif($_SESSION['User']==14) $Aseguradora=qo1("select aseguradora from usuario_callallianz where id=".$_SESSION['Id_alterno']);
	else $Aseguradora=qo1("select aseguradora from usuario_aseguradora1 where id=".$_SESSION['Id_alterno']);
	if($Aseguradora==1) $Aseguradora='1,8,9';
	elseif($Aseguradora==2) $Aseguradora='2,5';
	elseif($Aseguradora==3) $Aseguradora='3,7';
}
else $Aseguradora=0;
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');die();}
pide_datos();

function pide_datos()
{
	html();
	echo "<body onload='centrar();'>".titulo_modulo("<b>Consulta siniestros</b>");
	echo "<form action='zconsulta_siniestro1_1.php' method='post' target='_self' name='forma' id='forma'>
		Señor Usuario: Digite la placa para hacer la búsqueda:<br>
		<br>
		<table>
		<tr><td>Placa del vehículo</td><td><input type='text' name='placa' id='placa'></td></tr>
		<tr><td colspan=2 align='center'><input type='button' value='CONSULTAR' style='width:200;height:60;font-weight:bold'
		onclick=\"if(alltrim(document.forma.placa.value)) this.form.submit(); else alert('No ha digitado ningún criterio de busqueda');\"></td></tr></table>
		<input type='hidden' name='Acc' value='buscar'>
		</form></body>";
}

function buscar()
{
	global $placa,$Aseguradora;
	if($placa) 
	{
		$Cond="placa like '%$placa%' "; 
		$Consulta=" Placa = $placa ";
		graba_bitacora('siniestro','C',0,"Consulta placa $placa");
	}
	html();
	if($Siniestros=q("select * from siniestro where $Cond ".($Aseguradora?"and aseguradora in ($Aseguradora) ":"")." order by ingreso desc"))
	{
		$Numero_resultados=mysql_num_rows($Siniestros);
		echo "<body>".titulo_modulo("<b>Consulta Siniestros</b>").
			"Criterio de búsqueda: $Consulta  Número de resultados: $Numero_resultados <br><br>
			Seleccione uno de los siguientes resultados para ver en detalle todo su contenido:<br><br>
			<table border cellspacing=0><tr>
			<th>Selección</th>
			<th>Numero de siniestro</th>
			<th>Numero de Póliza</th>
			<th>Placa</th>
			<th>Asegurado</th>
			<th>Identifiación Asegurado</th>
			<th>Declarante</th>
			<th>Identifiación Declarante</th>
			</tr>";
		while($S=mysql_fetch_object($Siniestros))
		{
			echo "<tr>
					<td align='center'><input type='radio' name='xx' id='xx' onclick=\"muestra('c_".$S->id."');\">";
			capa('c_'.$S->id,1); echo "<table bgcolor='#dddddd' border><tr><td>".pinta_detalle($S)."</td></tr></table>"; fincapa();
			echo "</td>
					<td>$S->numero</td>
					<td>$S->poliza</td>
					<td>$S->placa</td>
					<td>$S->asegurado_nombre</td>
					<td align='right'>".coma_format($S->asegurado_id)."</td>
					<td>$S->declarante_nombre</td>
					<td align='right'>".coma_format($S->declarante_id)."</td>
					</tr>";
		}
		echo "</table>";

	}
	else
	{
		echo "<body>".titulo_modulo("<b>Consulta Siniestros</b>")."El criterio de búsqueda $Consulta no ha obtenido ningún resultado.";
	}
	pide_datos();
}



function pinta_detalle($R)
{
	return "<TABLE class='tableedit' align='center' border cellspacing=0>
		<TD class='tdedit' ALIGN='right' >Numero de Siniestro</TD>
		<TD class='tdedit'  >$R->numero</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Ciudad</TD>
		<TD class='tdedit'  colspan='5' >".qo1("select concat(departamento,' - ',nombre) from ciudad where codigo='$R->ciudad'")."</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Fecha Autorización</TD>
		<TD class='tdedit'  >$R->fec_autorizacion</td>
		<TD class='tdedit' ALIGN='right' >Fecha Siniestro</TD>
		<TD class='tdedit'  >$R->fec_siniestro</td>
		<TD class='tdedit' ALIGN='right' >Fecha Declaración</TD>
		<TD class='tdedit'  >$R->fec_declaracion</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Número de póliza</TD>
		<TD class='tdedit'  >$R->poliza</td>
		<TD class='tdedit' ALIGN='right' >Sucursal Radicadora</TD>
		<TD class='tdedit'  colspan='3' >$R->sucursal_radicadora</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Intermediario</TD>
		<TD class='tdedit'  colspan='5' >$R->intermediario</td></tr><tr>
		<TD class='tdedit' ALIGN='right' colspan='2'>Vigencia póliza (desde)</TD>
		<TD class='tdedit'  >$R->vigencia_desde</td>
		<TD class='tdedit' ALIGN='right' colspan='2' >Vigencia póliza (hasta)</TD>
		<TD class='tdedit'  >$R->vigencia_hasta</td></tr><tr></tr></TABLE><TABLE class='tableedit' align='center'  border cellspacing=0>
		<TD class='tdedit' ALIGN='right' >Estado</TD>
		<TD class='tdedit'  >".qo1("select nombre from estado_siniestro where id='$R->estado'")."</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Observaciones generales</TD>
		<TD class='tdedit'  >$R->observaciones</td></tr>
		<tr><TD class='tdedit' ALIGN='right' ></TD>
		<TD class='tdedit'  ></td></tr>
		<tr></tr></TABLE><div style='text-align: center;'><span style='font-weight: bold;'>
		<br />DATOS DEL VEHICULO</span>
		<br /></div><hr /><TABLE class='tableedit' align='center'  border cellspacing=0>
		<TD class='tdedit' ALIGN='right' >Placa Vehiculo</TD>
		<TD class='tdedit'  >$R->placa</td>
		<TD class='tdedit' ALIGN='right' >Marca</TD>
		<TD class='tdedit'  >$R->marca</td>
		<TD class='tdedit' ALIGN='right' >Tipo</TD>
		<TD class='tdedit'  >$R->tipo</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Línea</TD>
		<TD class='tdedit'  >$R->linea</td>
		<TD class='tdedit' ALIGN='right' >Modelo</TD>
		<TD class='tdedit'  >$R->modelo</td>
		<TD class='tdedit' ALIGN='right' >Clase</TD>
		<TD class='tdedit'  >$R->clase</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Color</TD>
		<TD class='tdedit'  >$R->color</td>
		<TD class='tdedit' ALIGN='right' >Servicio</TD>
		<TD class='tdedit'  colspan='3' >$R->servicio</td></tr><tr></tr></TABLE>
		<br /><div style='text-align: center;'><span style='font-weight: bold;'>DATOS PERSONALES - ASEGURADO
		<br /></span><hr />
		<br /></div><TABLE class='tableedit' align='center' border cellspacing=0>
		<TD class='tdedit' ALIGN='right' >Nombre Asegurado</TD>
		<TD class='tdedit'  >$R->asegurado_nombre</td>
		<TD class='tdedit' ALIGN='right' >Identificación Asegurado</TD>
		<TD class='tdedit'  >$R->asegurado_id</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Dirección Asegurado</TD>
		<TD class='tdedit'  colspan='3' >$R->asegurado_direccion</td></tr><tr></tr></TABLE>
		<br /><div style='text-align: center;'><span style='font-weight: bold;'>DATOS PERSONALES - DECLARANTE</span><hr />
		<br /></div><TABLE class='tableedit' align='center' border cellspacing=0>
		<TD class='tdedit' ALIGN='right' >Nombre Declarante</TD>
		<TD class='tdedit'  colspan='2' >$R->declarante_nombre</td>
		<TD class='tdedit' ALIGN='right' >Identifiación Declarante</TD>
		<TD class='tdedit'  >$R->declarante_id</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Teléfono Declarante</TD>
		<TD class='tdedit'  >$R->declarante_telefono</td>
		<TD class='tdedit' ALIGN='right' >Dirección Declarante</TD>
		<TD class='tdedit'  colspan='2' >$R->declarante_direccion</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Ciudad Declarante</TD>
		<TD class='tdedit'  colspan='2' >$R->declarante_ciudad</td>
		<TD class='tdedit' ALIGN='right' >Telefono Residencia</TD>
		<TD class='tdedit'  >$R->declarante_tel_resid</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Telefono Oficina</TD>
		<TD class='tdedit'  >$R->declarante_tel_ofic</td>
		<TD class='tdedit' ALIGN='right' >Teléfono celular</TD>
		<TD class='tdedit'  >$R->declarante_celular</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Otro teléfono</TD>
		<TD class='tdedit'  >$R->declarate_tel_otro</td>
		<TD class='tdedit' ALIGN='right' >Correo electrónico</TD>
		<TD class='tdedit'  colspan='2' >$R->declarante_email</td></tr><tr></tr></TABLE>
		<br /><div style='text-align: center;'><span style='font-weight: bold;'>DATOS PERSONALES - CONDUCTOR</span><hr />
		<br /></div><TABLE class='tableedit' align='center' border cellspacing=0>
		<TD class='tdedit' ALIGN='right' >Nombre Conductor</TD>
		<TD class='tdedit'  colspan='3' >$R->conductor_nombre</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Telefono Residencia</TD>
		<TD class='tdedit'  >$R->conductor_tel_resid</td>
		<TD class='tdedit' ALIGN='right' >Teléfono Oficina</TD>
		<TD class='tdedit'  >$R->conductor_tel_ofic</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Teléfono Celular</TD>
		<TD class='tdedit'  >$R->conductor_celular</td>
		<TD class='tdedit' ALIGN='right' >Otro teléfono</TD>
		<TD class='tdedit'  >$R->conductor_tel_otro</td></tr><tr>
		<TD class='tdedit' ALIGN='right' >Correo electrónico</TD>
		<TD class='tdedit'  colspan='3' >$R->conductor_email</td></tr>".
		($R->img_encuesta_f?"<tr><td colspan=5 align='center'><a onclick=\"modal('$R->img_encuesta_f',0,0,700,700,'encuesta');\" style='cursor:pointer' class='info'><img src='gifs/actualizar_datos.gif' border=0 alt='Ver Encuesta'><span>Ver imagen de la encuesta</span></a></td></tr>":"")."</TABLE>";
}























?>