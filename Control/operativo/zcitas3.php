<?php

/**
 * CONTROL OPERATIVO - ENTREGA Y DEVOLUCION VEHICULOS
 *
 * @version $Id$
 * @copyright 2010
 */

include('inc/funciones_.php');
include('inc/chart/Includes/FusionCharts.php'); // inserta rutinas para presentaci�n de graficos
require('festivosColombia.php');
sesion(); //verifica la sesion del usuario
if(!$Modo) $Modo=1; // solo pendientes
$USUARIO = $_SESSION['User'];
$Nusuario = $_SESSION['Nombre'];
$Nick = $_SESSION['Nick'];
$Hoyl = date('Y-m-d H:i:s');
$Hoy = date('Y-m-d');
$Hora = date('H:i:s');
$ESTADOS_CITA_DEVOLUCION = "P,PROGRAMADA;C,CUMPLIDA";
if($USUARIO==5) $PRE_AUTORIZA=qo1("select gen_pre_autoriza from usuario_autorizacion where id=".$_SESSION['Id_alterno']); else $PRE_AUTORIZA=0;
if($USUARIO==10) $PRE_AUTORIZA=qo1("select gen_pre_autoriza from usuario_oficina where id=".$_SESSION['Id_alterno']);
$Ultimos=array();$AFuturos=array();$ASinconcluir=array();$SMS_entregas=array();$SMS_devoluciones=array();

if (!empty($Acc) && function_exists($Acc)){	eval($Acc . '();');	die();}

citas_inicial();

function citas_inicial() // funcion principal de citas
{
	global $Hoy, $Dia,  $ESTADOS_CITA_DEVOLUCION, $USUARIO, $Hora,$OFI,$Modo,$ASEG,$PRE_AUTORIZA,$Ultimos,$AFuturos,$ASinconcluir,$SMS_entregas,$SMS_devoluciones;
	if(!$OFI && !$Dia && $USUARIO!=10 && $USUARIO!=33) $OFI=1;
	if (!$Dia) $Dia = $Hoy;
	$TUcita = tu('cita_servicio', 'id'); //trae permisos de modificacon de la tabla de citas para el usuario
	html('CONTROL OPERATIVO - CITAS DEL DIA'); // pinta cabeceras html
	echo "<script language='javascript'>

			var Parar_recarga=false;

			function cambio_fecha()
			{
				document.getElementById('btnconsultar').style.visibility='hidden';
				with(document.forma1)
				{var fec=Dia.value;var ofi=OFI.value;var modo=Modo.value;var aseg=ASEG.value;}
				if(!Parar_recarga)	window.open('zcitas.php?Acc=citas_inicial&Dia='+fec+'&OFI='+ofi+'&Modo='+modo+'&ASEG='+aseg,'_self');
			}
			";
		if(!inlist($USUARIO,'1,5,13'))
			echo "var Recargar=setTimeout(cambio_fecha,60000);";
	echo "

			function valida1(id,valor)  // cambio de estado para call center y jefe operativo
			{
				if(confirm('Desea cambiar el estado de esta cita?'))
				{
			         if(valor=='S' || valor=='X' || valor=='N')
			        {
			            if(confirm('Desea pasar el estado a PENDIENTE?')) window.open('zcitas.php?Acc=cambia_estado&id='+id+'&estado='+valor+'&Pendiente=1','Citas_oculto');
			            else window.open('zcitas.php?Acc=cambia_estado&id='+id+'&estado='+valor,'Citas_oculto');
			        }
					window.open('zcitas.php?Acc=cambia_estado&id='+id+'&estado='+valor,'Citas_oculto');
				}
			}

			function valida2(id,valor)  // CAMBIO DE ESTADO PARA OPERATIVO
			{
				if(valor=='R')
				{alert('No permite Reprogramar la cita, ese paso lo hace Call Center');return false;}
				if(confirm('Desea cambiar el estado de esta cita?')) window.open('zcitas.php?Acc=cambia_estado&id='+id+'&estado='+valor,'Citas_oculto');
			}

			function valida3(forma)
			{

				if(!Number(forma.kmf.value)) { alert('Debe escribir el kilometraje final v�lido'); forma.kmf.style.backgroundColor='ffff00';forma.kmf.value='';forma.kmf.focus(); return false;}
				if(!alltrim(forma.obs.value)) { alert('Debe escribir las observaciones en la conclusi�n del servicio'); forma.obs.style.backgroundColor='ffff00';forma.obs.value='';forma.obs.focus(); return false;}
				if(!forma.Nuevo_estado.value) {alert('Debe seleccionar el nuevo estado en el que va a quedar el veh�culo.'); forma.Nuevo_estado.style.backgroundColor='ffffdd';return false; }
				if(confirm('Desea cambiar el estado de esta devolucion?')) forma.submit();
			}
			function modifica_cita(id) { modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$TUcita&id='+id,0,0,700,1000,'mcita'); }
			function inicia_facturacion(idCita) { modal('zfacturacion.php?Acc=inserta_desde_cita&idCita='+idCita,0,0,700,1000,'factura'); }
			function imprimir_rcp(idr,idc) { modal('zcitas.php?Acc=imprimir_rcp&idr='+idr+'&idc='+idc,0,0,700,1000,'recibo_caja_provisional'); }

			function producir_informe()
			{
				with(document.forma1)
				{ var Oficina=OFI.value; var Aseguradora =ASEG.value; var Fecha=Dia.value; }
				modal('zcitas.php?Acc=informe_plano&Oficina='+Oficina+'&Aseguradora='+Aseguradora+'&Fecha='+Fecha,0,0,500,500,'informe');
			}

			function insertar_domicilio(id) {modal('zcitas.php?Acc=insertar_domicilio&id='+id,0,0,300,300,'domic');	}
			function arribo_asegurado(id) { if(confirm('Desea marcar esta cita con el indicador de Arribo de Asegurado?')) window.open('zcitas.php?Acc=arribo_asegurado&id='+id,'Citas_oculto'); }

			var Repinta_hora=false;

			var Contador_Segundos=60;
			function disminuye_contador_segundos()
			{ if(!Parar_recarga)
				{ document.getElementById('segundos').innerHTML=Contador_Segundos; Contador_Segundos--; Repinta_hora=setTimeout(disminuye_contador_segundos,1000); }
			}

			function para_recarga() { Parar_recarga=true; }

			function marcar_entrega(id)
			{ if(confirm('".$_SESSION['Nombre'].": Desea marcar el momento de la Entrega de este Veh�culo?')) window.open('zcitas.php?Acc=marcar_entregado&id='+id,'Citas_oculto'); }

			function actualiza_info(id) { modal('zautorizaciones.php?Acc=actualizar_info&idcita='+id,0,0,100,100,'auinfo'); }
			function nuevo_recibo_garantia(id) {modal('zcartera.php?Acc=nuevo_recibo_garantia&idcita='+id,0,0,600,600,'recg');	}
			function ver_visitantes(id) {modal('zingreso_recepcion.php?Acc=consulta_ingreso&id='+id,0,0,600,600,'iv');}
			function formulario_entrega(estado,idcita) { modal('zcitas.php?Acc=formulario_entrega&idcita='+idcita+'&estado='+estado,200,200,400,600,'fentrega');}
			function cambia_dias_servicio(id) {modal('zcitas.php?Acc=cambia_dias_servicio&id='+id,0,0,400,500,'cds');}
			function solicitar_factura(id) {modal('zcitas.php?Acc=solicitar_factura&cita='+id,0,0,400,500,'sfac');}

			function asigna_operario_entrega(operario,cita,objeto)
			{if(confirm('Asignar este operario a la entrega del veh�culo?'))
			{window.open('zcitas.php?Acc=asigna_operario_entrega&operario='+operario+'&cita='+cita,'Citas_oculto');objeto.disabled=true;}}

			function insertar_domiciliod(id) {modal('zcitas.php?Acc=insertar_domiciliod&id='+id,0,0,300,300,'domic');	}

			function asigna_operario_devolucion(operario,cita,objeto)
			{if(confirm('Asignar este operario a la devoluci�n del veh�culo?'))
			{window.open('zcitas.php?Acc=asigna_operario_devolucion&operario='+operario+'&cita='+cita,'Citas_oculto');objeto.disabled=true;}}

			function estado_operarios()
			{var Oficina=document.forma1.OFI.value;var Dia=document.forma1.Dia.value;
				window.open('zcitas.php?Acc=estado_operarios&Oficina='+Oficina+'&Dia='+Dia+'&Modo=diario','Estado_Operarios');
				document.getElementById('Estado_Operarios').style.visibility='visible'; }

			function ocultar_estado_operarios() { document.getElementById('Estado_Operarios').src='about:blank';
			document.getElementById('Estado_Operarios').style.visibility='hidden'; }
			function formulario_devolucion(idcita) { modal('zcitas.php?Acc=formulario_devolucion&idcita='+idcita,200,200,400,600,'fentrega');}

	</script>
	<body>
	<script language='javascript'>
		centrar();
	</script>
	<iframe id='Estado_Operarios' name='Estado_Operarios' height='600' width='800' style='visibility:hidden;position:fixed;top:10;left:10;border-style:solid;border-width:2px;z-index:119;opacity: 0.90' border='1' frameborder='yes'></iframe>
	<h3>CITAS DE ENTREGAS Y DEVOLUCIONES</h3>
	<iframe name='Citas_oculto' style='visibility:hidden' height=1 width=1></iframe>
	<form action='zcitas.php' method='post' target='_self' name='forma1' id='forma1'>
			<font style='font-size:14'>Fecha:</font> " . pinta_FC('forma1', 'Dia', $Dia, 'f', " style='font-size:14px' ") .
	"Oficina: ".menu1("OFI","Select id,nombre from oficina",$OFI,1).
	" Aseguradora: ".menu1('ASEG',"Select id,nombre from aseguradora where id not in (6) order by orden_monitor",$ASEG,1)."
		Ver: <select name='Modo'><option value='1' ".($Modo==1?"selected":"").">Solo Pendientes</option>
			<option value='2' ".($Modo==2?"selected":"").">Solo Canceladas</option><option value='3' ".($Modo==3?"selected":"").">Solo Cumplidas</option>
			<option value='4' ".($Modo==4?"selected":"").">Pendientes y cumplidas</option><option value='5' ".($Modo==5?"selected":"").">Todas</option></select>
		 <input type='button' id='btnconsultar' value='Consultar' onclick='Parar_recarga=false;cambio_fecha();'>
		 <a class='info' style='cursor:pointer' onclick='estado_operarios()'><img src='img/grafica.png' border='0' height='24px' valign='top'><span>Estado Operarios</span></a>
		 <font style='font-size:8px'>Versi�n Mayo 2 de 2013</font>";
	if(!inlist($USUARIO,'13,1,5')) // a ciertos perfiles les permite bloquear el contador en reversa que refresca la pantalla
		 echo "Tiempo para refrescar: <span id='segundos' style='font-size:14;' onclick='para_recarga();'></span><script language='javascript'>disminuye_contador_segundos();</script> ";
	// si el usuario es del perfil de oficinas, solo le muestra los datos de su oficina
	if ($USUARIO == 10){$OFICINA = qo1("select oficina from usuario_oficina where id='" . $_SESSION['Id_alterno'] . "'");}
	elseif($USUARIO==33)	{$OFICINA = qo1("select oficina from usuario_tesoreria where id='" . $_SESSION['Id_alterno'] . "'");}
	elseif($OFI)	$OFICINA=$OFI;
	else	$OFICINA = 0;
	echo "</form>";
	$Us = $_SESSION['User'];
	/////////////////////  *********************   ARREGLOS GLOBALES  **********************************************
	include('inc/link.php'); // conexion con la base de datos.
	// Calcula la cantidad de citas pendientes y cumplidas tanto en entrega como en devolucion
	$Citas_pendientes_e=qom("Select count(id) as cantidad,sum(if(hora<'$Hora',1,0)) as retrasado from cita_servicio where estado='P' and fecha='$Dia' ".($OFICINA?"and oficina=$OFICINA ":""),$LINK);
	$Citas_cumplidas_e=qom("Select count(id) as cantidad from cita_servicio where estado='C' and fecha='$Dia' ".($OFICINA?"and oficina=$OFICINA ":""),$LINK);
	$Citas_pendientes_d=qom("Select count(id) as cantidad,sum(if(hora_devol<'$Hora',1,0)) as retrasado  from cita_servicio where estadod='P' and estado='C' and fec_devolucion='$Dia' ".($OFICINA?"and oficina=$OFICINA ":""),$LINK);
	$Citas_cumplidas_d=qom("Select count(id) as cantidad from cita_servicio where estadod='C' and estado='C' and fec_devolucion='$Dia' ".($OFICINA?"and oficina=$OFICINA ":""),$LINK);

	$T = qo1m("select id from usuario_tab where usuario=$USUARIO and tabla='siniestro' ",$LINK); // trae el permiso de modificacion de la tabla siniestros para el usuario
	$Colores=mysql_query("select codigo,color_co from estado_citas ",$LINK); // carga un arreglo de colores de acuerdo a los estados de las citas
	$Colores_cita=array();
	while($Bgc=mysql_fetch_object($Colores)) { $Colores_cita[$Bgc->codigo]=$Bgc->color_co; }
	$Aseguradoras=mysql_query("select * from aseguradora ",$LINK); // carga un arreglo de nombres y logos de las aseguradoras
	$AAsegs=array();
	while($Ase=mysql_fetch_object($Aseguradoras)){$AAsegs[$Ase->id]=$Ase;}
	$Autorizaciones=array(); // carga un arreglo de autorizaciones para identificar cuales citas tienen ya autorizaciones
	
	//Autorizaciones
	
	$sql = "select a.siniestro,a.id from sin_autor a,siniestro s,cita_servicio c where a.siniestro=s.id and s.id=c.siniestro and a.siniestro=c.siniestro and c.estado='P' and a.estado='A'  ";
	
	//echo $sql;
	
	$QAutorizaciones=mysql_query($sql,$LINK);	
	while($Au=mysql_fetch_object($QAutorizaciones)) { $Autorizaciones[$Au->siniestro]=$Au->id; }
	
	
	$oficinas_asociadas = mysql_query("select * from oficina where id = '$OFICINA'  union select * from oficina where sucursal = '$OFICINA'  ");
	
	$asociadas = array();	
	
	if(mysql_num_rows($oficinas_asociadas))
	{
		while($oficina=mysql_fetch_object($oficinas_asociadas))
		{
			array_push($asociadas,$oficina->id);
		}
	}
	
	//print_r($asociadas);
	
	$f_asociadas = $OFICINA.",".implode(",",$asociadas);	
	
	
	// carga un arreglo con los ultimos estados de los vehiculos para identificar si estan disponibles para su entrega.
	if(!$QUltimos=mysql_query("select v.placa,u.vehiculo,u.estado,ev.nombre as ne,u.fecha_inicial,u.fecha_final
														from ubicacion u, vehiculo v,estado_vehiculo ev where
														u.vehiculo= v.id and u.estado=ev.id
														 ".($ASEG?" and u.flota=$ASEG":"").($OFICINA?" and v.ultima_ubicacion in ($f_asociadas) ":"")."
														and u.fecha_final='$Hoy' order by u.vehiculo,u.fecha_inicial,u.fecha_final,u.id ",$LINK)) die(mysql_error());
														
														
	while($Ul=mysql_fetch_object($QUltimos)) {if(!$Ultimos[$Ul->placa]) $Ultimos[$Ul->placa]=array();$Ultimos[$Ul->placa][count($Ultimos[$Ul->placa])]=$Ul;}
	
	
	$Fec_inicial = $Hoy;
	$Fec_final = date('Y-m-d', strtotime(aumentadias($Fec_inicial, 7)));
	$Fec_futuros1 = date('Y-m-d', strtotime(aumentadias($Fec_inicial, 1)));
	// carga un arreglo con eventos futuros por si hay programados mantenimientos, fuera de servicio o algun estado que impida prestar el vehiculo
	
	$sql = "Select v.placa,ev.nombre as ne,u.fecha_inicial,u.fecha_final
													from ubicacion u,vehiculo v,estado_vehiculo as ev
													where u.vehiculo=v.id and u.estado=ev.id and u.estado not in (1,96)
													 ".($ASEG?" and u.flota=$ASEG":"").($OFICINA?" and v.ultima_ubicacion in ($f_asociadas) ":"")."
													 and u.fecha_final>'$Hoy' and
													(		(u.fecha_inicial between '$Fec_futuros1' and '$Fec_final')    or  	( u.fecha_final between '$Fec_futuros1' and '$Fec_final') )
													order by v.placa,u.fecha_inicial,u.fecha_final,u.id ";
	
	/*echo "<br>";	
	echo "<br>";	
	echo $sql;*/												
	
	
	
	if(!$QFuturos=mysql_query($sql,$LINK)) die(mysql_error());
													
													
													
	while($Fu=mysql_fetch_object($QFuturos)) {if(!$AFuturos[$Fu->placa]) $AFuturos[$Fu->placa]=array();$AFuturos[$Fu->placa][count($AFuturos[$Fu->placa])]=$Fu;}
	// busca estados en servicio que esten sin concluir previos a la entrega del vehiculo
	if(!$QSinconcluir=mysql_query("select v.placa,u.id,u.fecha_inicial,u.fecha_final from ubicacion u,vehiculo v
														 where u.vehiculo=v.id and u.estado=1 and fecha_final<='$Hoy' ",$LINK)) die(mysql_error());
	while($Sc=mysql_fetch_object($QSinconcluir)) { $ASinconcluir[$Sc->placa]=$Sc; }
	unset($QAutorizaciones);unset($QUltimos);unset($Colores);unset($QFuturos);unset($QSinconcluir);unset($Aseguradoras);
	// calcula totales
	$Total_entregas=$Citas_pendientes_e->cantidad+$Citas_cumplidas_e->cantidad;
	$Total_devoluciones=$Citas_pendientes_d->cantidad+$Citas_cumplidas_d->cantidad;
	// OBTIENE LOS SMS ENVIADOS EN DEVOLUCIONES Y ENTREGAS

	$QSMS_entregas=mysql_query("select distinct s.siniestro from cita_servicio c, seguimiento s where c.siniestro=s.siniestro and c.fecha='$Dia' and s.tipo=23 ",$LINK);
	if(mysql_num_rows($QSMS_entregas))
	{
		while($Rsms=mysql_fetch_object($QSMS_entregas))
		{	$SMS_entregas[]=$Rsms->siniestro;}
	}

	$QSMS_devoluciones=mysql_query("select distinct s.siniestro from cita_servicio c, seguimiento s where c.siniestro=s.siniestro and c.fec_devolucion='$Dia' and s.tipo=24 ",$LINK);
	if(mysql_num_rows($QSMS_devoluciones))
	{
		while($Rsms=mysql_fetch_object($QSMS_devoluciones))
		{	$SMS_devoluciones[]=$Rsms->siniestro;}
	}
	// inicia pintando las entregas
	echo "<table align='center'><tr><td bgcolor='ffffee' style='font-size:14px'><b>ENTREGAS</b></td>
						<td bgcolor='ffffee' style='font-size:14px'>Pendientes: $Citas_pendientes_e->cantidad</td>
						<td bgcolor='ffffee' style='font-size:14px'>Retrasadas: $Citas_pendientes_e->retrasado</td>
						<td bgcolor='ffffee' style='font-size:14px'>Cumplidas: $Citas_cumplidas_e->cantidad</td>
						<td bgcolor='ffffee' style='font-size:14px'>Total: $Total_entregas</td></tr>
						<tr><td bgcolor='ffffee' style='font-size:14px'><b>DEVOLUCIONES</b></td>
						<td bgcolor='ffffee' style='font-size:14px'>Pendientes: $Citas_pendientes_d->cantidad</td>
						<td bgcolor='ffffee' style='font-size:14px'>Retrasadas: $Citas_pendientes_d->retrasado</td>
						<td bgcolor='ffffee' style='font-size:14px'>Cumplidas: $Citas_cumplidas_d->cantidad</td>
						<td bgcolor='ffffee' style='font-size:14px'>Total: $Total_devoluciones</td></tr></table>";

	// /////////////////////  *******************     ENTREGAS   *****************************************//////////////////////////////////////////////////////
	echo "<br /><h3 style='background-color:000055;color:44ff66;font-size:20px;' align='center'><a name='Entre'></a>ENTREGAS" . date('h:i A', strtotime($Hora)) . "  <a href='#Devol' style='color:ffffff'>Ir a Devoluciones</a> ";
	echo " <a onclick=\"window.open('zcita_excel.php?Acc=entregas','Citas_oculto');\" style='color:ffffff;cursor:pointer;'><img src='img/excel2013.png' style='height:20px'> Entregas y Devoluciones</a>"; 
	echo "</H3>";

	$Consulta=citas_inicial_modo_entregas($Modo,$OFICINA,$ASEG,$Dia); // construye la consulta de las citas de entregas
	
	if(!$Citas = mysql_query($Consulta,$LINK)) die(mysql_error()); // corre el query de citas de entrega
	if (mysql_num_rows($Citas))
	{
		echo "<table border cellspacing=0 style='empty-cells:show' width='100%'><tr>
						<th>Numero</th>
						<th>Siniestro</th>
						<th>Fecha</th>
						<th>Hora programada</th>
						<th>Conductor</th>
						<th>Observaciones</th>
						<th>Veh�culo</th>".($USUARIO!=32?"<th>Estado</th>":"")."

						</tr>";
		$c_of = '';
		/* controlador de cambio de oficina */
		$countN = 1;
		$con_of = 1;
		/* contador de citas por oficina */
		while ($C = mysql_fetch_object($Citas))
		{
			$Futuro = '';$Sin_conc = '';
			if ($c_of != $C->noficina) // pinta el rompimiento por oficina
			{
				echo "<tr><td colspan=10 style='font-size:16;font-weight:bold;background-color:000000;color:ffff00'>$C->noficina</td></tr>";
				$c_of = $C->noficina;
				$con_of = 1;
			}
			$Bc=$Colores_cita[$C->cestado];
			$Aseguradora=$AAsegs[$C->aseguradora];
			// pinta registro por registro de la cita
			echo "<tr bgcolor='$Bc' ";
			if(inlist($USUARIO, '1,2,7,4') && ($C->cestado != 'C' || $USUARIO==1))  // SI ES USUARIO GERENCIA, OPERATIVO, CALL CENTER
			{	if(browser_movil()) {echo " onclick='modifica_cita($C->id);' ";} else {echo "ondblclick='modifica_cita($C->cid);' ";}}
			if($C->cestado == 'C' ) echo " ondblclick=\"alert('Despues de cumplida la cita no se puede modificar');\" ";
			echo ">";
			citas_inicial_entrega_siniestro($con_of,$C,$Aseguradora,$T); // pinta las opciones de entrega del servicio
			citas_inicial_entrega_fecha($C); // pinta los datos de la fecha
			citas_inicial_entrega_hora($C,$Aseguradora); // pinta los datos de la hora con ayudas tooltip
			citas_inicial_entrega_conductor($C,$LINK); // pinta el formulario de asignacion de conductor
			citas_inicial_tiempos($C,$LINK); // pinta el estado de tiempos
			if($C->cestado=='C')	echo "<br /><a onclick='ver_visitantes($C->siniestro);' style='cursor:pointer'>Visitantes..</a>"; // permite ver los visitantes cargados en recepcion
			echo "</td><td>". nl2br($C->obscita) . ($C->obscita?" ":"") . "<a class='info' style='cursor:pointer' href=\"javascript:modal('zcitas.php?Acc=inserta_obs&id=$C->cid',0,0,500,500,'obs');\">
							<img src='gifs/mas.gif' border='0'><span>Insertar observaciones</span></a><br />"; // boton para insercion de observaciones
			echo "</td><td align='center' class='placa' style='background-image:url(img/placa.jpg);' width='80px'>$C->cplaca</td>";
			/// SI EL USUARIO ES SERVICIO AL CLIENTE O RECEPCION NO MUESTRA LOS ESTADOS DE LAS CITAS
			if($USUARIO!=32)
			{
				echo "<td align='center' width='20%'>";
				// /////////////   BUSQUEDA DE EVENTOS FUTUROS *************************************
				$Fec_inicial = $C->fecha;
				if($C->cestado!='P')
				{
					if($C->cestado=='C') citas_inicial_estado_cumplido($LINK,$C,$T); // pinta las opciones asociadas a una cita cumplida
					else echo menu4('estado', "Select codigo,nombre from estado_citas", $C->cestado, 0, "font-size:12", " disabled onchange=''; ",$LINK); // menu de cumplimiento de la cita
				}
				else  /// ESTADO = P  Programada
				{
					citas_inicial_busqueda_futuros($LINK,$C); // busca estados futuros y los pinta
					if ($Sin_conc == '' && $Futuro == '')/* si existen servicios sin concluir no permite cambiar ningun estado de la cita */
					{
						// //  si el usuario es call center, puede cancelar o reprogramar la cita
						// / si el usuario es operativo, puede cumplir la cita.
						if ($C->cestado == 'P')
						{ 
							// DEBE VERIFICAR SI YA TIENE AUTORIZACION
							if($Autorizaciones[$C->siniestro])
							{
								echo "<font color='green'><b>YA CUENTA CON AUTORIZACION</B></FONT>";
								if (inlist($_SESSION['User'], '1,2,5,10'))
								{ // icono para la captura de imagenes de la autorizacion
									echo "<a onclick=\"modal('zautorizaciones.php?Acc=carga_imagenes&id=$C->siniestro',0,0,600,900,'eds');\" style='cursor:pointer'><img src='gifs/webcam.png' border='0' height='20'></a>";
								}
								// boton para la impresi�n del acta de entrega/devolucion
								
							   
							   
							   $insurace=qo("select aseguradora from aoacol_aoacars.siniestro where id = $C->siniestro");
								
								if($insurace->aseguradora == 59){
								   echo "<br>"."<a onclick=\"modal('zcitas.php?Acc=imprimir_documentos_venta&idc=$C->cid',0,0,900,900,'impa');\" style='cursor:pointer;font-weight:bold;'><img src='gifs/print.png' border='0'> Imprimir Actas de venta</a><br />";
								    
								  }else{
									  echo "<br/><a class='verBoton' onclick=\"modal('zcitas.php?Acc=imprimir_acta&idc=$C->cid',0,0,900,900,'impa');\" style='cursor:pointer;font-weight:bold;'><img src='gifs/print.png' border='0'> Imprimir el Acta de Entrega</a><br />";
								  }
								
							   if($C->arribo!='0000-00-00 00:00:00' && $C->momento_entrega!='0000-00-00 00:00:00')
								{  ///  MUESTRA TODOS LOS ESTADOS INCLUYENDO * CUMPLIDA *
									$EST_CITA = "Select codigo,nombre from estado_citas where codigo in ('C','S','P')";
								}
								else  // SI NO HA SIDO ENTREGADO EL VEHICULO
								{  ///  MUESTRA TODOS LOS ESTADOS MENOS  * CUMPLIDA *
									echo "<font color='blue'>Vehiculo en proceso de entrega</font>";
									$EST_CITA = "Select codigo,nombre from estado_citas  where codigo in ('S','C','P','W') ";
								}
							}
							else  ///  SI NO TIENE AUTORIZACION
							{
								echo "<font color='brown'><b>AUN NO TIENE AUTORIZACION</B></FONT><BR>";
								if($C->dir_domicilio)
									echo "<BR> <a onclick=\"modal('zcitas.php?Acc=imprimir_acta&idc=$C->cid',0,0,900,900,'impa');\" style='cursor:pointer;font-weight:bold;'><img src='gifs/print.png' border='0'> Imprimir el Acta de Entrega</a>";
								$EST_CITA = "Select codigo,nombre from estado_citas  where codigo!='C' ";
							}

							/////////////////////////   FORMULARIO DE ENTREGA DE LA CITA  ////////////////////////////////////////////////////////////
							if (inlist($_SESSION['User'], '1,2,4,10,13,26'))
							{
								$Sin_observaciones = ($C->observaciones?0:1);
								//Nueva funcion menu4 por Sergio Urbina para poder ver los estados
								echo menu4('estado', $EST_CITA, $C->cestado, 0, "font-size:12; width:200px;"," onchange=\"formulario_entrega(this.value,$C->cid);this.value='P';\"",$LINK);
								
							}
							else
							{ echo menu4('estado', "Select codigo,nombre from estado_citas", $C->cestado, 0, "font-size:12;width:200px;", " disabled onchange=''; ",$LINK); }
						}
						else
						{
							echo menu4('estado', "Select codigo,nombre from estado_citas", $C->cestado, 0, "font-size:12;width:200px;", " disabled onchange=''; ",$LINK);
							echo "<br />Hora: <a onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$T&id=$C->siniestro',0,0,600,1000,'eds');\"
										style='cursor:pointer'>$C->hora_llegada <img src='gifs/standar/Pencil.png' border='0'></a>";
						}
					}
					else // CUANDO AUN HAY SERVICIOS SIN CONCLUIR O ESTADOS FUTUROS
					{
						// aun asi pinta el boton para imprimir el acta
						echo "<BR> <a onclick=\"modal('zcitas.php?Acc=imprimir_acta&idc=$C->cid',0,0,900,900,'impa');\" style='cursor:pointer;font-weight:bold;'><img src='gifs/print.png' border='0'> Imprimir el Acta de Entrega</a>";
						if(inlist($_SESSION['User'],'1,2,4,7,10,13,26'))
						{
							$Sin_observaciones = ($C->observaciones?0:1);
							echo menu4('estado', "Select codigo,nombre from estado_citas where codigo!='C'", $C->cestado, 0, "font-size:12;width:200px;"," onchange=\"formulario_entrega(this.value,$C->cid,0);this.value='P';\"",$LINK);
						}
						else // SI LOS USUARION NO SON LOS PERMITDOS, SIMPLEMENTE MUESTRA EL ESTADO PROTEJIDO
							echo menu4('estado', "select codigo,nombre from estado_citas", $C->cestado,0," font-size:12;width:200px;"," disabled ",$LINK);
					}
				}
				echo "</td>";
			}
			echo "</tr>";
			// ////////  CONTADOR DE OFICINA
			//var_dump($countN);
			$_SESSION['excel_citas_entregas'][$countN]=array(
						'numero'=>$countN,
						'placa'=>$C->cplaca,
						'ciudad'=>$C->noficina,
						'cliente'=>$Aseguradora->nombre,
						'siniestro'=>$C->nsiniestro,
						'fecha'=>$C->fecha,
						'estado'=>($C->estado=='P'?"PROGRAMADA":"CUMPLIDA"),
						'odometro' =>($C->img_odo_salida_f==''?"NO CARGADA":"CARGADA"),
						'acta_entrega' => ($C->img_inv_salida_f==''?"NO CARGADA":"CARGADA"),
						'frente_vehiculo' => ($C->fotovh1_f==''?"NO CARGADA":"CARGADA"),
						'izquierda_vehiculo' => ($C->fotovh2_f==''?"NO CARGADA":"CARGADA"),
						'derecha_vehiculo' => ($C->fotovh3_f==''?"NO CARGADA":"CARGADA"),
						'atras' => ($C->fotovh4_f==''?"NO CARGADA":"CARGADA"),
						'img_cedula_frontal' => ($C->img_cedula_f==''?"NO CARGADA":"CARGADA"),
						'img_cedula_reverso' => ($C->img_pase_f==''?"NO CARGADA":"CARGADA"),
						'img_licencia_frontal' => ($C->adicional1_f==''?"NO CARGADA":"CARGADA"),
						'img_licencia_reverso' => ($C->adicional2_f==''?"NO CARGADA":"CARGADA")
						);
			$con_of++;
			$countN++;
		}
		echo "</table>";
	}
	else
	{
		echo "<b style='color:ff0000'>No hay citas programadas para entrega</b>";
	}

	/*
	// ///////////////////////////////    ---------------------------------------     DEVOLUCIONES --------------------------------------------  /////////////////////////////////////////////////////////
	// ///////////////////////////////    ---------------------------------------     DEVOLUCIONES --------------------------------------------  /////////////////////////////////////////////////////////
	// ///////////////////////////////    ---------------------------------------     DEVOLUCIONES --------------------------------------------  /////////////////////////////////////////////////////////
	// ///////////////////////////////    ---------------------------------------     DEVOLUCIONES --------------------------------------------  /////////////////////////////////////////////////////////
	// ///////////////////////////////    ---------------------------------------     DEVOLUCIONES --------------------------------------------  /////////////////////////////////////////////////////////
*/
	echo "<br /><br /><br /><h3 style='background-color:000055;color:44ff66;font-size:20px;' align='center'><a name='Devol'></a>DEVOLUCIONES <a href='#Entre' style='color:ffffff'>Ir a Entregas</a> ";
	echo " <a onclick=\"window.open('zcita_excel.php?Acc=devoluciones','Citas_oculto');\" style='color:ffffff;cursor:pointer;'><img src='img/excel2013.png' style='height:20px'> Devoluciones</a>";
	echo "</H3>";
	$Consulta=citas_devolucion_modo($Modo, $OFICINA, $ASEG, $Dia); // construye el query de acuerdo al modo que quiere el usuario
	
    $Devoluciones = q($Consulta);
	
	if(mysql_num_rows($Devoluciones))
	{
		$_SESSION['excel_citas_devoluciones']=array();
		
		echo "<table border cellspacing=0 style='empty-cells:show' width='100%'>
			<tr>
				<th>Numero</th>
				<th>Siniestro</th>
				<th>Fecha</th>
				<th>Hora programada</th>
				<th>Conductor</th>
				<th>Observaciones</th>
				<th>Veh�culo</th>
				<th>Estado</th>
			</tr>";
		$c_of = '';
		/* controlador de cambio de oficina */ $con_of = 1;
		/* contador de citas por oficina */

		include('inc/link.php');
		$conuntNoDev = 1;
		while ($C = mysql_fetch_object($Devoluciones))
		{
			if ($c_of != $C->noficina) // rompimiento por oficina
			{
				echo "<tr><td colspan=10 style='font-size:16;font-weight:bold;background-color:000000;color:ffff00'>$C->noficina</td></tr>";
				$c_of = $C->noficina;
				$con_of = 1;
			}
			$Aseguradora=$AAsegs[$C->aseguradora];
			if ($C->estadod == 'C') $Bc = 'ccffcc';	else $Bc = 'ffffff';
			echo "<tr bgcolor='$Bc' " . (inlist($USUARIO, '1,2,4')?"ondblclick='modifica_cita($C->cid);' ":"") . " ><td align='center'>$con_of</td>";
			citas_devolucion_siniestro($C,$Aseguradora,$T); // presenta los datos del siniestro
			citas_devolucion_fechahora($C,$Aseguradora); // presenta los datos de la fecha y hora de devolucion con ayudas tooltip
			citas_devolucion_conductor($C,$LINK); // presenta los datos del conductor
			citas_devolucion_observaciones($C); // presenta y deja capturar observaciones
			
			echo "<td align='center' nowrap='yes'>";
			// //  si el usuario es call center, puede cancelar o reprogramar la cita
			// / si el usuario es operativo, puede cumplir la cita.
			if (inlist($_SESSION['User'], '1,2,4,10,13')) // call center y Autorizaciones
				echo menu3('estadod', $ESTADOS_CITA_DEVOLUCION, $C->estadod, 0, "font-size:12", ($C->estadod == 'C'?" disabled":"") . " onchange=\"formulario_devolucion($C->cid);this.value='P';\" ");
			else echo menu3('estadod', $ESTADOS_CITA_DEVOLUCION, $C->estadod, 0, "font-size:12", " disabled onchange=''; ");
			if ($C->estadod == 'C') citas_devolucion_cumplido($C,$T); // presenta los estados de devoluci�n cumplida
			echo "</td></tr>";
			$_SESSION['excel_citas_devoluciones'][$conuntNoDev]=array(
						'numero'=>$conuntNoDev,
						'placa'=>$C->cplaca,
						'ciudad'=>$C->noficina,
						'cliente'=>$Aseguradora->nombre,
						'siniestro'=>$C->nsiniestro,
						'fecha'=>$C->fec_devolucion,
						'estado'=>($C->estadod=='P'?"PROGRAMADA":"CUMPLIDA"),
						'odometro' =>($C->img_odo_entrada_f==''?"NO CARGADA":"CARGADA"),
						'acta_entrega' => ($C->img_inv_entrada_f==''?"NO CARGADA":"CARGADA"),
						'frente_vehiculo' => ($C->fotovh5_f==''?"NO CARGADA":"CARGADA"),
						'izquierda_vehiculo' => ($C->fotovh6_f==''?"NO CARGADA":"CARGADA"),
						'derecha_vehiculo' => ($C->fotovh7_f==''?"NO CARGADA":"CARGADA"),
						'atras' => ($C->fotovh8_f==''?"NO CARGADA":"CARGADA"),
						'encuesta' => ($C->img_encuesta_f==''?"NO CARGADA":"CARGADA")
						);
			$con_of++;
			$conuntNoDev++;
		}
		echo "</table>";
	}
	else echo "<b style='color:ff0000'>No hay devoluciones programadas</b>";
	mysql_close($LINK); //cierra la conexi�n con la base de datos.
	echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></body>";
}

//-------------------------------------------------------------------------------------------------------------------------------------------------------

function citas_inicial_modo_entregas($Modo,$OFICINA,$ASEG,$Dia) // construcci�n del query de citas de acuerdo al modo que quier ver el usuario
{

	
	$oficinas_asociadas=mysql_query("select * from oficina where sucursal = '$OFICINA' ");
	
	$asociadas = array();	
	
	if(mysql_num_rows($oficinas_asociadas))
	{
		while($oficina=mysql_fetch_object($oficinas_asociadas))
		{
			array_push($asociadas,$oficina->id);
		}
	}
	
	//print_r($asociadas);
	if(count($asociadas)>0)
	{	
		$f_asociadas = $OFICINA.",".implode(",",$asociadas);	
	}
	else
	{
		$f_asociadas = $OFICINA;	
	}
	//echo "<br>";
	
	//echo $f_asociadas;
	
	switch($Modo)
	{
		// solo pendientes
		case 1:
			
			$sql = "select c.*,o.nombre as noficina,s.numero as nsiniestro,a.nombre as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
			s.*,es.nombre as nestado,c.estado as cestado,s.estado as sestado
			from cita_servicio c,siniestro s,vehiculo v,oficina o,aseguradora a,estado_siniestro es
			where c.flota=a.id and c.oficina=o.id and c.siniestro=s.id and s.estado=es.id
			and v.placa=c.placa and c.fecha='$Dia' ".($OFICINA?" and c.oficina in ($f_asociadas) ":" ")."
			and c.estado='P' ".($ASEG?" and s.aseguradora=$ASEG":"")." order by noficina,c.hora";			
			
			//echo $sql;
			
			return $sql;
			
			break;
		// solo canceladas
		case 2: return "select c.*,o.nombre as noficina,s.numero as nsiniestro,a.nombre as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,es.nombre as nestado,c.estado as cestado,s.estado as sestado
									from cita_servicio c,siniestro s,vehiculo v,oficina o,aseguradora a,estado_siniestro es
									where c.flota=a.id and c.oficina=o.id and c.siniestro=s.id and s.estado=es.id
									and v.placa=c.placa and c.fecha='$Dia' ".($OFICINA?" and c.oficina in ($f_asociadas) ":" ")."
									and c.estado in  ('S','X','Y','N') ".($ASEG?" and s.aseguradora=$ASEG":"")." order by noficina,c.hora";
			break;
		// solo cumplidas
		case 3: 
				$sql = "select c.*,o.nombre as noficina,s.numero as nsiniestro,a.nombre as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,es.nombre as nestado,c.estado as cestado,s.estado as sestado
									from cita_servicio c,siniestro s,vehiculo v,oficina o,aseguradora a,estado_siniestro es
									where c.flota=a.id and c.oficina=o.id and c.siniestro=s.id and s.estado=es.id
									and v.placa=c.placa and c.fecha='$Dia' ".($OFICINA?" and c.oficina in ($f_asociadas) ":" ")."
									and c.estado='C' ".($ASEG?" and s.aseguradora=$ASEG":"")." order by noficina,c.hora";
				
				//echo $sql;
				
				return $sql;
			break;
		// pendientes y cumplidas
		case 4: return "select c.*,o.nombre as noficina,s.numero as nsiniestro,a.nombre as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,es.nombre as nestado,c.estado as cestado,s.estado as sestado
									from cita_servicio c,siniestro s,vehiculo v,oficina o,aseguradora a,estado_siniestro es
									where c.flota=a.id and c.oficina=o.id and c.siniestro=s.id and s.estado=es.id
									and v.placa=c.placa and c.fecha='$Dia' ".($OFICINA?" c.oficina in ($f_asociadas) ":" ")."
									and c.estado in  ('P','C') ".($ASEG?" and s.aseguradora=$ASEG":"")." order by noficina,c.hora";
			break;
		// todas las citas
		case 5: 
				$sql = "select c.*,o.nombre as noficina,s.numero as nsiniestro,a.nombre as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,es.nombre as nestado,c.estado as cestado,s.estado as sestado
									from cita_servicio c,siniestro s,vehiculo v,oficina o,aseguradora a,estado_siniestro es
									where c.flota=a.id and c.oficina=o.id and c.siniestro=s.id and s.estado=es.id
									and v.placa=c.placa and c.fecha='$Dia' ".($OFICINA?" and c.oficina in ($f_asociadas) ":" ")."
									".($ASEG?" and s.aseguradora=$ASEG":"")." order by noficina,c.hora";
				//echo $sql;
				return $sql;
			break;
	}
}

function citas_devolucion_modo($Modo,$OFICINA,$ASEG,$Dia)
{
	$oficinas_asociadas=mysql_query("select * from oficina where sucursal = '$OFICINA' ");
	
	$asociadas = array();	
	
	if(mysql_num_rows($oficinas_asociadas))
	{
		while($oficina=mysql_fetch_object($oficinas_asociadas))
		{
			array_push($asociadas,$oficina->id);
		}
	}
	
	//print_r($asociadas);
	
	if(count($asociadas)>0)
	{	
		$f_asociadas = $OFICINA.",".implode(",",$asociadas);	
	}
	else
	{
		$f_asociadas = $OFICINA;
	}
	
	
	switch($Modo)
	{
		// solo pendientes
		case 1: $sql = "select c.*,o.nombre as noficina,s.numero as nsiniestro,a.nombre as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,es.nombre as nestado,c.estado as cestado,s.estado as sestado
									from cita_servicio c,siniestro s,vehiculo v,oficina o,aseguradora a,estado_siniestro es
									where c.flota=a.id and c.oficina=o.id and c.siniestro=s.id and s.estado=es.id
									and v.placa=c.placa and  c.fec_devolucion='$Dia' and c.estado='C'  " . ($OFICINA?" and c.oficina in ($f_asociadas) ":" ") ."
									and c.estadod='P' ".($ASEG?"and s.aseguradora=$ASEG ":""). " order by noficina,hora_devol";
			//echo $sql;
			return $sql;						
			break;
		// solo canceladas
		case 2: return "select c.*,o.nombre as noficina,s.numero as nsiniestro,a.nombre as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,es.nombre as nestado,c.estado as cestado,s.estado as sestado
									from cita_servicio c,siniestro s,vehiculo v,oficina o,aseguradora a,estado_siniestro es
									where c.flota=a.id and c.oficina=o.id and c.siniestro=s.id and s.estado=es.id
									and v.placa=c.placa and  c.fec_devolucion='$Dia' and c.estado='C'  " . ($OFICINA?" and c.oficina in ($f_asociadas) ":" ") ." and
									c.estadod='X' ".($ASEG?"and s.aseguradora=$ASEG ":""). " order by noficina,hora_devol";
			break;
		// solo cumplidas
		case 3: $sql = "select c.*,o.nombre as noficina,s.numero as nsiniestro,a.nombre as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,es.nombre as nestado,c.estado as cestado,s.estado as sestado
									from cita_servicio c,siniestro s,vehiculo v,oficina o,aseguradora a,estado_siniestro es
									where c.flota=a.id and c.oficina=o.id and c.siniestro=s.id and s.estado=es.id
									and v.placa=c.placa and  c.fec_devolucion='$Dia' and c.estado='C'  " . ($OFICINA?" and c.oficina in ($f_asociadas) ":" ") ." and
									c.estadod='C' ".($ASEG?"and s.aseguradora=$ASEG ":""). " order by noficina,hora_devol";
			//echo $sql;
			return $sql;
			break;
		// solo pendientes y cumplidas
		case 4: return "select c.*,o.nombre as noficina,s.numero as nsiniestro,a.nombre as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,es.nombre as nestado,c.estado as cestado,s.estado as sestado
									from cita_servicio c,siniestro s,vehiculo v,oficina o,aseguradora a,estado_siniestro es
									where c.flota=a.id and c.oficina=o.id and c.siniestro=s.id and s.estado=es.id
									and v.placa=c.placa and  c.fec_devolucion='$Dia' and c.estado='C'  " . ($OFICINA?" and c.oficina in ($f_asociadas) ":" ") ." and
									c.estadod in ('P','C') ".($ASEG?"and s.aseguradora=$ASEG ":""). " order by noficina,hora_devol";
			break;
		// todas las citas
		case 5: return "select c.*,o.nombre as noficina,s.numero as nsiniestro,a.nombre as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,es.nombre as nestado,c.estado as cestado,s.estado as sestado
									from cita_servicio c,siniestro s,vehiculo v,oficina o,aseguradora a,estado_siniestro es
									where c.flota=a.id and c.oficina=o.id and c.siniestro=s.id and s.estado=es.id
									and v.placa=c.placa and  c.fec_devolucion='$Dia' and c.estado='C'  " . ($OFICINA?" and c.oficina in ($f_asociadas) ":" ") ."
									".($ASEG?"and s.aseguradora=$ASEG ":""). " order by noficina,hora_devol";
			break;
	}
}

function citas_inicial_busqueda_futuros($LINK,$C) // busca ultimos estados, futuros o servicios sin concluir para mostrar un alerta
{
	global $Hoy,$Ultimos,$AFuturos,$ASinconcluir;
	$Futuro='';
	if($Ultimos[$C->cplaca])  // busca el �ltimo estado hasta el dia de hoy, si es distinto a 
	//parqueadero, servicio concluido, servicio, o domicilio produce el alerta and u.estado not in (1,2,7,96)
	{
		$UUe=$Ultimos[$C->cplaca][count($Ultimos[$C->cplaca])-1];
		
		if(!inlist($UUe->estado,'1,2,7,96,102')) $Futuro.="<B>$UUe->ne</B>, <U>$UUe->fecha_inicial</U> - <U>$UUe->fecha_final</U>]";
	}
	if($AFuturos[$C->cplaca]) // busqueda de estados futuros para la placa
	{
		for($i=0;$i<count($AFuturos[$C->cplaca]);$i++)
		{ $FF=$AFuturos[$C->cplaca][$i]; $Futuro .= "[<B>$FF->ne</B> <U>$FF->fecha_inicial</U> - <U>$FF->fecha_final</U>] "; }
	}
	// si hay eventos futuros o �ltimos eventos distintos de parqueadero, presenta un alerta
	if($Futuro) echo "<font color='red'>EVENTOS FUTUROS: </font>$Futuro<br />Verifique con el Control Operativo para modificar los eventos futuros o reprogramar esta cita.";

	// /////////////   BUSQUEDA DE SERVICIOS SIN CONCLUIR *************************************
	$Sin_conc = '';
	if($ASinconcluir[$C->cplaca])
	{
		$SC=$ASinconcluir[$C->cplaca]; // presenta el alerta
		echo "<br><font color='red' style='text-decoration:blink;font-weight:bold;'>SERVICIO PREVIO SIN CONCLUIR: </font> [<U>$SC->fecha_inicial</U> - <U>$SC->fecha_final</U>]<br />
						Concluya los servicios previos a esta cita para poder continuar.<br />";
	}
}

function citas_inicial_estado_cumplido($LINK,$C,$T) // presenta las opciones de impresion y actualizaciones de una cita cumplida.
{
	echo menu4('estado', "Select codigo,nombre from estado_citas", $C->cestado, 0, "font-size:12","disabled onchange='';",$LINK);
	// pinta el boton para imprimir el acta de entrega
	echo "<br /><a onclick=\"modal('zcitas.php?Acc=imprimir_acta&idc=$C->cid',0,0,900,900,'impa');\" style='cursor:pointer;font-weight:bold;'><img src='gifs/print.png' border='0'> Imprimir el Acta de Entrega</a><br />";
	if($T)
	{// bot�n para modificaci�n del siniestro. para subir imagenes
		echo "<br />Hora: <a onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$T&id=$C->siniestro',0,0,600,1000,'eds');\"
					style='cursor:pointer'>$C->hora_llegada <img src='gifs/standar/Pencil.png' border='0'></a><br />";
	}
	else
	{
		echo "<br />Hora: $C->hora_llegada <br />"; // solo pinta la hora de llegada
	}

	$okimg1="<img src='gifs/standar/si.png' border='0' style='cursor:pointer' onclick=\"modal('";
	$okimg2="',0,0,500,500,'foto');\" border='0' ";
	// si est�n subidas las im�genes del siniestro, pinta un chulo azul, de lo contrario pinta un trianculo de advertencia.
	if($C->img_cedula_f) echo $okimg1.$C->img_cedula_f.$okimg2." alt='C�dula' title='C�dula'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='C�dula' title='C�dula'>";
	if($C->img_pase_f) echo $okimg1.$C->img_pase_f.$okimg2." alt='Reverso C�dula' title='Reverso C�dula'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Reverso C�dula' title='Reverso C�dula'>";
	if($C->adicional1_f) echo $okimg1.$C->acidional1_f.$okimg2." alt='Licencia' title='Licencia'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Licencia' title='Licencia'>";
	if($C->adicional2_f) echo $okimg1.$C->acidional2_f.$okimg2." alt='Reverso Licencia' title='Reverso Licencia'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Reverso Licencia' title='Reverso Licencia'>";
	if($C->adicional3_f) echo $okimg1.$C->acidional3_f.$okimg2." alt='Doc.Adicional 1' title='Doc.Adicional 1'>"; else echo "<img src='gifs/standar/marco.png' border='0' alt='Doc.Adicional 1' title='Doc.Adicional 1'>";
	if($C->adicional4_f) echo $okimg1.$C->acidional4_f.$okimg2." alt='Doc.Adicional 2' title='Doc.Adicional 2'>"; else echo "<img src='gifs/standar/marco.png' border='0' alt='Doc.Adicional 2' title='Doc.Adicional 2'>";
	echo "<br>";
	if($C->img_odo_salida_f) echo $okimg1.$C->img_odo_salida_f.$okimg2." alt='Odometro' title='Odometro'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Odometro' title='Odometro'>";
	if($C->img_inv_salida_f) echo $okimg1.$C->img_inv_salida_f.$okimg2." alt='Acta de Entrega' title='Acta de Entrega'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Acta de Entrega' title='Acta de Entrega'>";
	if($C->fotovh1_f) echo $okimg1.$C->fotovh1_f.$okimg2." alt='Frente' title='Frente'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Frente' title='Frente'>";
	if($C->fotovh2_f) echo $okimg1.$C->fotovh2_f.$okimg2." alt='Izquierda' title='Izquierda'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Izquierda' title='Izquierda'>";
	if($C->fotovh3_f) echo $okimg1.$C->fotovh3_f.$okimg2." alt='Derecha' title='Derecha'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Derecha' title='Derecha'>";
	if($C->fotovh4_f) echo $okimg1.$C->fotovh4_f.$okimg2." alt='Atras' title='Atras'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Atras' title='Atras'>";
	if($C->img_contrato_f) echo $okimg1.$C->img_contrato_f.$okimg2." alt='Contrato' title='Contrato'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Contrato' title='Contrato'>";
}

function citas_inicial_entrega_hora($C,$Aseguradora) // Presenta la hora de la cita con ayuda de tooltip
{
	global $Hora,$SMS_entregas;
	echo "<td align='center'><a class='info'>".
				(array_search($C->siniestro,$SMS_entregas)?"<img src='img/sms_enviado.png' height=30><br>":"")
				.($C->hora < $Hora && $C->cestado == 'P' && $C->arribo=='0000-00-00 00:00:00'?
				"<b style='color:ff0000;text-decoration:blink;'>" . date('h:i A', strtotime($C->hora)) . "</b>":
				date('h:i A', strtotime($C->hora))) . "<span style='width:700px'>
				<h3>Control de Seguimiento de Citas <i style='color:00000'>$Aseguradora->nombre  P�liza N�mero: $C->poliza Siniestro No. $C->numero </i></h3>
				<br />Fecha de ingreso del siniestro: <b>$C->ingreso</b> FECHA DE AUTORIZACION: <b style='color:ff0000'>$C->fec_autorizacion</b>
				<hr color='eeeeee'><b><u>ASEGURADO:</u></b> Nombre: <b>$C->asegurado_nombre</b> Identificaci�n: <b>$C->asegurado_id</b>
				<hr color='eeeeee'><b><u>Declarante:</u></b> Nombre: <b>$C->declarante_nombre</b> Identificaci�n: <b>$C->declarante_id</b>
				Telefonos: <b>$C->declarante_telefono / $C->declarante_tel_resid
				/ $C->declarante_tel_ofic / $C->declarante_celular</b>
				<hr color='eeeeee'><b><u>Conductor:</u></b> Nombre: <b>$C->conductor_nombre</b> Telefonos: <b>$C->declarante_telefono / $C->conductor_tel_resid
				/ $C->conductor_tel_ofic / $C->conductor_celular / $C->conductor_tel_otro</b><hr>
				Observaciones: $C->observaciones</span></a><br /><b>$C->nflota</b>
		</td>";
}

function citas_inicial_entrega_fecha($C) // presenta la fecha y el n�mero de dias de servicio
{
	global $USUARIO;
	echo "<td nowrap='yes'>$C->fecha<br>D�as de <br>Servicio: "; // si el usuario es coordinador de call center puede cambiar los dias de servicio
	if((($USUARIO==26 && $_SESSION['Id_alterno']==2) || $USUARIO==1) && $C->cestado=='P') echo "<a class='info' style='cursor:pointer' onclick='cambia_dias_servicio($C->cid)'>";
	echo "<b>$C->dias_servicio</b>";
	if((($USUARIO==26 && $_SESSION['Id_alterno']==2) || $USUARIO==1) && $C->cestado=='P') echo "</a>";
	echo "</td>";
}

function citas_inicial_entrega_siniestro($con_of,$C,$Aseguradora,$T) // presenta el numero de siniestro
{
	global $USUARIO,$PRE_AUTORIZA;
	echo "<td align='center'>$con_of</td><td>$C->nsiniestro <br>"; // presenta el contador de oficina y el numero de siniestro
	if($C->no_garantia) // si el caso no requiere garantia aparece una alerta
	echo " <a class='info'><img src='img/nogarantia.png' border='0' height='20px' alt='Servicio Sin Garantia' title='Servicio Sin Garantia' align='middle'>
				<span><img src='img/nogarantia.png'><h3>Servicio Sin Garant�a</h3></span></a> ";

	if($C->cestado == 'P' && inlist($USUARIO,'1,2,5,7,10')) // SI ES USUARIO GERENCIA, AUTORIZACIONES, OPERATIVO, OFICINA
		echo "<a class='info' href=\"javascript:modal('zautorizaciones.php?sini=$C->numero',0,0,600,600,'call');\" alt='Solicitar Autorizacion' title='Solicitar Autorizacion'>
							<img src='img/solicita_autorizacion.png' border='0' height='20' align='middle'><span style='width:100px'>Solicitar Autorizaci�n</span></a>&nbsp;";
	if(inlist($USUARIO,'1,6')) // SI EL USUARIO ES FACTURACION
	{ // presenta el �cono de inicio de proceso de facturaci�n
		echo "<a href=\"javascript:inicia_facturacion($C->cid);void(null);\" class='info'><img src='img/facturar.png' height='20' border='0' align='middle'><span style='width:200px'>Iniciar proceso de facturaci�n</span></a>&nbsp;";
	}
	if(inlist($USUARIO,'1,10,33'))  // SI EL USUARIO ES CAJERO
	{ // presenta el �cono de captura de nuevo recibo de garant�a
		echo "<a href=\"javascript:nuevo_recibo_garantia($C->cid);void(null);\" class='info'><img src='img/caja_registradora.png' height='20' border='0' align='middle'><span style='width:200px'>Recibo Caja x Garantia</span></a>&nbsp;";
	}
	if($PRE_AUTORIZA) // si es usuario de autorizaciones y puede preautorizar.. tambi�n lo pueden hacer los directores de oficina
	{ //  muestra el icono de elaboracion de recibos de caja por garantia
		echo "<a href=\"javascript:nuevo_recibo_garantia($C->cid);void(null);\" class='info'><img src='img/caja_registradora.png' height='20' border='0' align='middle'><span style='width:200px'>PRE-Autorizaci�n Efectivo o TD</span></a>&nbsp;";
	}
	echo "[<font color='blue'>$Aseguradora->nombre</font>]<br />Estado: $C->nestado<br />";
	// muestra el bot�n de consulta inteligente de siniestro
	echo "<a onclick=\"modal('zsiniestro.php?Acc=buscar_siniestro&id=$C->siniestro',0,0,600,1000,'eds');\"	style='cursor:pointer'><u>Ver Siniestro</u></a>";
	if(inlist($USUARIO,'1,2,5,6,13,33') && $C->cestado=='C') // muestra el bot�n de solicitud de visualizaci�n de la garant�a
		echo "	<a onclick=\"modal('zautorizaciones.php?Acc=datos_autorizacion&id=$C->siniestro',0,0,600,1000,'eds');\"	style='cursor:pointer'><u>Visualizaci�n de Garant�a</u></a>";
	echo "</td>";
}

function citas_inicial_tiempos($C,$LINK) // muestra el registro de tiempos de cada servicio desde el ingreso hasta la entrega del vehiculo
{
	global $USUARIO;
	if($C->cestado=='P') // CONTROL DE TIEMPOS
	{
		if($C->arribo!='0000-00-00 00:00:00')
		{
			if($Autorizacion = qom("select id,fecha_solicitud,fecha_proceso from sin_autor where siniestro='$C->siniestro' and estado!='R' ",$LINK)) // trae los datos de la autorizaci�n
			{
				// calcula el tiempo entre el arribo y la autorizaci�n
				if($C->arribo<$Autorizacion->fecha_solicitud) $Tiempo1_solicitud_autorizacion=diferencia_tiempo($C->arribo,$Autorizacion->fecha_solicitud); else $Tiempo1_solicitud_autorizacion='Anticipado';
				echo "<br>Tiempo entre Arribo y Solicitud Autorizaci�n: <b style='font-size:12px;color:4444ff'>$Tiempo1_solicitud_autorizacion</b>";
				if($Autorizacion->fecha_proceso!='0000-00-00 00:00:00')
				{
					// calcula el tiempo en tre la solicitud de autorizaci�n y el tiempo de solicitud
					$Tiempo2_proceso_autorizacon=diferencia_tiempo($Autorizacion->fecha_solicitud,$Autorizacion->fecha_proceso);
					echo "<br>Tiempo entre Solicitud y Proceso Autorizaci�n: <b style='font-size:12px;color:4444ff'>$Tiempo2_proceso_autorizacon</b>";
					if($C->momento_entrega!='0000-00-00 00:00:00')
					{
						//calcula el tiempo entre la aprobaci�n de la autorizaci�n y la entrega del vehiculo
						$Tiempo3_entrega=diferencia_tiempo($Autorizacion->fecha_proceso,$C->momento_entrega);
						echo "<br>Tiempo entre Proceso Autorizaci�n y entrega del Veh�culo: <b style='font-size:12px;color:4444ff'>$Tiempo3_entrega</b>";
					}
					else
					{
						if(inlist($USUARIO,'1,13,23,10'))
						{// para los operarios y auxiliares de informaci�n y directores de oficina: pinta el icono de entrega de vehiculo
							echo "<br><a class='info' style='cursor:pointer' onclick='marcar_entrega($C->cid);'><img src='img/vehiculo.png' border='0' height='30'> Marcar Entrega del Veh�culo <span  style='width:200px'>Marcar Entrega del Vehiculo</span></a>";
						}
					}
				}
				else
				{// en espera del proceso de autorizaci�n
					$Tiempo=diferencia_tiempo($Autorizacion->fecha_solicitud,date('Y-m-d H:i:s'));
					echo "<br><b style='font-size:14px;text-decoration:blink;color:cc0000;background-color:ffff55;'>Esperando proceso de Autorizaci�n: $Tiempo EN ESPERA</b>";
				}
			}
			else
			{// en espera en recepcion
				$Tiempo=diferencia_tiempo($C->arribo,date('Y-m-d H:i:s'));
				echo "<br><b style='font-size:14px;text-decoration:blink;color:cc0000;background-color:ffff55;'>EL CLIENTE LLEVA $Tiempo EN ESPERA</b>";
			}
			echo "<br /><a onclick='ver_visitantes($C->siniestro);' style='cursor:pointer'>Visitantes..</a>"; // bot�n para ver los visitantes del servicio
		}
		elseif(inlist($USUARIO,'1,2,5,32,10'))
		{// pinta el bot�n de marcacion de arribo y que dispara el proceso de toma de la foto en recepci�n
			echo "<br><a class='info' style='cursor:pointer' onclick='arribo_asegurado($C->cid);'><img src='img/arribo_asegurado.png' border='0' height='30'><span>Marcar Arribo de Asegurado</span></a>&nbsp; ";
		}
		if(!$C->dir_domicilio && $C->arribo=='0000-00-00 00:00:00')
		{
			if(inlist($USUARIO,'1,2,4,13'))
			{// para los usuarios de call center  presenta el bot�n para insertar domicilio en el servicio
				echo "<br><a class='info' style='cursor:pointer' onclick='insertar_domicilio($C->cid);'><img src='gifs/standar/seguir_amarillo.png' border='0'><span style='width:200px'>Crear Domicilio Entrega</span></a>&nbsp; ";
			}
		}
	}
	else
	{// cuando esta cumplida pinta todos los tiempos grabados.
		$Autorizacion = qom("select * from sin_autor where siniestro='$C->siniestro' and estado='A' and num_autorizacion!='' ",$LINK);
		if($C->arribo<$Autorizacion->fecha_solicitud) $Tiempo1_solicitud_autorizacion=diferencia_tiempo($C->arribo,$Autorizacion->fecha_solicitud); else $Tiempo1_solicitud_autorizacion='Anticipada';
		$Tiempo2_proceso_autorizacion=diferencia_tiempo($Autorizacion->fecha_solicitud,$Autorizacion->fecha_proceso);
		$Tiempo3_entrega=diferencia_tiempo($Autorizacion->fecha_proceso,$C->momento_entrega);
		$Tiempo4_registro=diferencia_tiempo($C->momento_entrega,$C->fecha.' '.$C->hora_llegada);
		echo "<br /><table cellspacing='1'><tr ><td bgcolor='dedede'>Arribo:</td><td colspan=4 bgcolor='dedede'>".date('H:i',strtotime($C->arribo))."</td></tr>
					<tr ><td bgcolor='dedede'>Autorizaci�n:</td><td bgcolor='dedede'>".date('H:i',strtotime($Autorizacion->fecha_solicitud))."</td><td bgcolor='dedede'>Dif:</td><td bgcolor='dedede'>$Tiempo1_solicitud_autorizacion</td></tr>
					 <tr ><td bgcolor='dedede'>Proceso Autorizaci�n:</td><td bgcolor='dedede'>".date('H:i',strtotime($Autorizacion->fecha_proceso))."</td><td bgcolor='dedede'>Dif:</td><td bgcolor='dedede'>$Tiempo2_proceso_autorizacion</td></tr>
				    <tr ><td bgcolor='dedede'>Entrega Veh�culo:</td><td bgcolor='dedede'>".date('H:i',strtotime($C->momento_entrega))."</td><td bgcolor='dedede'>Dif:</td><td bgcolor='dedede'>$Tiempo3_entrega</td></tr>
					<tr ><td bgcolor='dedede'>Registro final:</td><td bgcolor='dedede'>".date('H:i',strtotime($C->hora_llegada))."</td><td bgcolor='dedede'>Dif:</td><td bgcolor='dedede'>$Tiempo4_registro</td></tr></table> ";
	}
}

function citas_inicial_entrega_conductor($C,$LINK) // pinta si es domicilio y para seleccionar el operario que entrega el vehiculo
{
	global $USUARIO;
	
	$sql = "select * from oficina where id = '$C->oficina'  union select * from oficina where sucursal = '$C->oficina'  ";	
	
	//echo $sql;
	
	//echo "<br>";
	
	$oficinas_asociadas=q($sql);
	
	$asociadas = array();	
	
	if(mysql_num_rows($oficinas_asociadas))
	{
		while($oficina=mysql_fetch_object($oficinas_asociadas))
		{
			array_push($asociadas,$oficina->id);
		}
	}
	
	$f_asociadas = implode(",",$asociadas);	
	
	
	echo "<td width='200px'>$C->conductor  ";
	$va = base64_encode(1);
	
	if(inlist($USUARIO,'1,5'))
	{	
		echo "<br><br><style type='text/css'>
  .botonRegistro{
    text-decoration: none;
    padding: 2px;
    font-weight: 600;
    font-size: 10px;
    color: #ffffff;
    background-color: #1883ba;
    border-radius: 6px;
    border: 2px solid #0016b0;
  }
  .botonRegistro:hover{
    color: #1883ba;
    background-color: #ffffff;
  }
</style>
	<a href='https://app.aoacolombia.com/Control/operativo/zcallcenter2.php?Acc=pide_datos_financieros&ids=$C->siniestro&cerrando=1&sinG1=$va'>
	  <button type='button' class='botonRegistro'>Registra Cliente</button>
	</a><br><br>";
	}
	if($C->dir_domicilio)
		echo "<br /><span style='background-color:ffff33;font-size:12px;'><B>DOMICILIO ENTREGA</B>: $C->dir_domicilio <br /><B>TELEFONO</B>: $C->tel_domicilio</span><br>";
	if(inlist($USUARIO,'1,2,3,4,5,6,7,10,13,23,27'))
		echo "<br /><a class='info' style='cursor:pointer' onclick='solicitar_factura($C->cid);'><img src='gifs/standar/nuevo_ovr.png' border='0' height='18px'><span>Solicitar Factura</span></a>&nbsp;";
	if(inlist($USUARIO,'1,2,10,13,27'))
		echo menu4("operario_entrega","select id,concat(apellido,' ',nombre) from operario where inactivo=0 and oficina in ($f_asociadas) order by apellido,nombre",$C->operario_domicilio,1,'',($C->operario_domicilio?"disabled ":"onchange='asigna_operario_entrega(this.value,$C->cid,this);' "),$LINK);
	else
		echo "<br />Operario: ".qo1m("select concat(apellido,' ',nombre) from operario where id=$C->operario_domicilio",$LINK);
}

//--------------------------------------------------------------------------------------------------------------------

function citas_devolucion_conductor($C,$LINK) // pinta la zona de devoluciones donde puede pedir facturas y asignar domicilios en la devolucion y asignacion del oprerario
{
	global $USUARIO;
	
	$sql = "select * from oficina where id = '$C->oficina'  union select * from oficina where sucursal = '$C->oficina'  ";	
	
	//echo $sql;
	
	//echo "<br>";
	
	$oficinas_asociadas=mysql_query($sql);
	
	$asociadas = array();	
	
	if(mysql_num_rows($oficinas_asociadas))
	{
		while($oficina=mysql_fetch_object($oficinas_asociadas))
		{
			array_push($asociadas,$oficina->id);
		}
	}
	
	$f_asociadas = implode(",",$asociadas);

	//echo $f_asociadas;	
	
	echo "<td width='200px'>$C->conductor<br />";
	if(inlist($USUARIO,'1,2,3,4,5,6,7,10,13,23,27'))
	{	
		echo "<a class='info' style='cursor:pointer' onclick='solicitar_factura($C->cid);'><img src='gifs/standar/nuevo_ovr.png' border='0'><span>Solicitar Factura</span></a>&nbsp;";
	}
	if($C->dir_domiciliod)
	{	
		echo "<br /><span style='background-color:ffff33;font-size:12px;'><B>DOMICILIO DEVOLUCION</B>: $C->dir_domiciliod <br /><B>TELEFONO</B>: $C->tel_domiciliod</span><br />";
	}
	elseif(inlist($USUARIO,'1,2,4,13'))
	{
		echo "&nbsp;<a class='info' style='cursor:pointer' onclick='insertar_domiciliod($C->cid);'><img src='gifs/standar/seguir_amarillo.png' border='0'><span style='width:200px'>Crear Domicilio Devoluci�n</span></a>&nbsp; ";
	}
	if(inlist($USUARIO,'1,2,10,13,27'))
	{	
		$sql = "select id,concat(apellido,' ',nombre) from operario where inactivo=0 and oficina in ($f_asociadas) order by apellido,nombre";
		
		echo "<br />".menu1("operario_devolucion",$sql,$C->operario_domiciliod,1,'',($C->operario_domiciliod?"disabled ":"onchange='asigna_operario_devolucion(this.value,$C->cid,this);' "),$LINK);
	}
	else
	{	
			echo "<br />Operario: ".qo1m("select concat(apellido,' ',nombre) from operario where id=$C->operario_domiciliod",$LINK);
	}
	echo "</td>";
}

function citas_devolucion_siniestro($C,$Aseguradora,$T) // Pinta el siniestro en la devolucion
{
	global $USUARIO;
	echo "<td width='300px'>$C->nsiniestro ";
	echo "<a onclick=\"modal('zsiniestro.php?Acc=buscar_siniestro&id=$C->siniestro',0,0,600,1000,'eds');\"	style='cursor:pointer'><u>Ver Siniestro</u></a>";
	echo "<br>";
	if(inlist($USUARIO,'1,6')) // para el perfil de facturacion pinta el bot�n de inicio de proceso de facturacion
		echo "<a href=\"javascript:inicia_facturacion($C->cid);void(null);\" class='info'><img src='img/facturar.png' height='24' border='0' align='middle'><span style='width:200px'>Iniciar proceso de facturaci�n</span></a>&nbsp;";
	if(inlist($USUARIO,'1,6,10')) // para facturaci�n y oficinas, pinta solicitud de autorizacion para pago de facturas con tarjeta de credito.
		echo "&nbsp;<a class='info' href=\"javascript:modal('zautorizaciones.php?sini=$C->numero',0,0,600,600,'call');\" alt='Solicitar Autorizacion' title='Solicitar Autorizacion'><img src='img/solicita_autorizacion.png' border='0' height='30' align='middle'><span style='width:100px'>Solicitar Autorizaci�n</span></a> ";
	echo "[<font color='red'>$Aseguradora->nombre</font>]<br />	Estado: $C->nestado";
	echo "</td>";
}

function citas_devolucion_fechahora($C,$Aseguradora) // pinta la hora con ayuda tooltip
{
	global $Hora,$SMS_devoluciones;
	echo "<td nowrap='yes'>$C->fec_devolucion</td><td align='center'>".
		(array_search($C->siniestro,$SMS_devoluciones)?"<img src='img/sms_enviado.png' height=30><br>":"")
		."<a class='info'>" . ($C->hora_devol < $Hora && $C->estadod == 'P'?
		"<b style='color:ff0000;text-decoration:blink;'>" . date('h:i A', strtotime($C->hora_devol)) . "</b>":
		date('h:i A', strtotime($C->hora_devol))) . "<span style='width:700px'>
		<h3>Control de Seguimiento de Citas <i style='color:00000'>$Aseguradora->nombre  P�liza N�mero: $C->poliza Siniestro No. $C->numero </i></h3>
		<br />Fecha del siniestro: <b>$C->fec_siniestro</b> Fecha de declaraci�n del siniestro: <b>$C->fec_declaracion</b>
		FECHA DE AUTORIZACION: <b style='color:ff0000'>$C->fec_autorizacion</b>
		<br />Vigencia de la p�liza:  Desde <b>$C->vigencia_desde</b> hasta <b>$C->vigencia_hasta</b>
		<hr color='eeeeee'><b><u>ASEGURADO:</u></b> Nombre: <b>$C->asegurado_nombre</b> Identificaci�n: <b>$C->asegurado_id</b>
		<hr color='eeeeee'><b><u>Declarante:</u></b> Nombre: <b>$C->declarante_nombre</b> Identificaci�n: <b>$C->declarante_id</b>
		Telefonos: <b>$C->declarante_telefono / $C->declarante_tel_resid / $C->declarante_tel_ofic / $C->declarante_celular</b>
		<hr color='eeeeee'><b><u>Conductor:</u></b> Nombre: <b>$C->conductor_nombre</b> Telefonos: <b>$C->declarante_telefono / $C->conductor_tel_resid
		/ $C->conductor_tel_ofic / $C->conductor_celular / $C->conductor_tel_otro</b><hr>
		Observaciones: $C->observaciones</span></a><br /><b>$C->nflota</b></td>";
}

function citas_devolucion_observaciones($C) // pinta las observaciones en la devolucion y el vehiculo de AOA
{
	echo "<td>".nl2br($C->obs_devolucion)."<br><a class='info' style='cursor:pointer' href=\"javascript:modal('zcitas.php?Acc=inserta_obsd&id=$C->cid',0,0,500,500,'obs');\"><img src='gifs/mas.gif' border='0'><span>Insertar observaciones</span></a><br /></td>
	<td align='center' class='placa' style='background-image:url(img/placa.jpg);' width='80px'>$C->cplaca</td>";
}

function citas_devolucion_cumplido($C,$T) // pinta el formulario de cierre de devoluci�n
{
	if($T) // pinta el lapiz de modificaci�n del siniestro para cargar las imagenes.
		echo "<br />Hora: <a onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$T&id=$C->siniestro',0,0,600,1000,'eds');\" style='cursor:pointer'>$C->hora_devol_real <img src='gifs/standar/Pencil.png' border='0'></a><br />";
	else echo "<br />Hora: $C->hora_devol_real <br />"; // pinta la hora de devoluci�n real
	// pinta las imagenes que han sido cargadas o sino pinta una alerta de las que falta por cargar.
	$okimg1="<img src='gifs/standar/si.png' border='0' style='cursor:pointer' onclick=\"modal('";
	$okimg2="',0,0,500,500,'foto');\" border='0' ";
	if($C->img_odo_entrada_f) echo $okimg1.$C->img_odo_entrada_f.$okimg2." alt='Odometro' title='Odometro'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Odometro' title='Odometro'>";
	if($C->img_inv_entrada_f) echo $okimg1.$C->img_inv_entrada_f.$okimg2." alt='Acta de Entrega' title='Acta de Entrega'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Acta de Entrega' title='Acta de Entrega'>";
	if($C->fotovh5_f) echo $okimg1.$C->fotovh5_f.$okimg2." alt='Frente' title='Frente'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Frente' title='Frente'>";
	if($C->fotovh6_f) echo $okimg1.$C->fotovh6_f.$okimg2." alt='Izquierda' title='Izquierda'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Izquierda' title='Izquierda'>";
	if($C->fotovh7_f) echo $okimg1.$C->fotovh7_f.$okimg2." alt='Derecha' title='Derecha'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Derecha' title='Derecha'>";
	if($C->fotovh8_f) echo $okimg1.$C->fotovh8_f.$okimg2." alt='Atras' title='Atras'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Atras' title='Atras'>";
	if($C->img_encuesta_f) echo $okimg1.$C->img_encuesta_f.$okimg2." alt='Encuesta' title='Encuesta'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Encuesta' title='Encuesta'>";
}

//  **-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
//
//                                                    RUTINAS ESPECIFICAS A EVENTOS
//
//-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*

function inserta_obs() // formulario para insertar observaciones
{
	global $id;
	html('OBSERVACIONES');
	echo "<body onload='carga()'><script language='javascript'>centrar(500,500);</script>
		<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
		<input type='hidden' name='Acc' id='Acc' value='inserta_obs_ok'>
		Observaciones:<br />
		<textarea name='observaciones' cols=80 rows=10 style='font-size:12'></textarea><br />
		<br><input type='submit' value='GRABAR OBSERVACIONES'>
		<input type='hidden' name='id' id='id' value='$id'>
		</form>
		</body>";
}

function inserta_obs_ok() // funcion que graba las observaciones en la cita de servicio
{
	global $id, $observaciones, $Nusuario, $Hoyl;
	q("update cita_servicio set observaciones=concat(observaciones,\"\n[$Nusuario $Hoyl] $observaciones\") where id=$id");
	echo "<body><script language='javascript'>opener.location.reload();window.close();void(null);</script></body>";
}

function inserta_obsd() // formulario que permite grabar observaciones de devolucion a la cita
{
	global $id;
	html('OBSERVACIONES');
	echo "<body onload='carga()'><script language='javascript'>centrar(500,500);</script>
		<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
		<input type='hidden' name='Acc' id='Acc' value='inserta_obsd_ok'>
		Observaciones:<br />
		<textarea name='observaciones' cols=80 rows=10 style='font-size:12'></textarea><br />
		<br><input type='submit' value='GRABAR OBSERVACIONES'>
		<input type='hidden' name='id' id='id' value='$id'>
		</form>
		</body>";
}

function inserta_obsd_ok() // graba las observaciones de deovlucion en la cita
{
	global $id, $observaciones, $Nusuario, $Hoyl;
	q("update cita_servicio set obs_devolucion=concat(obs_devolucion,\"\n[$Nusuario $Hoyl] $observaciones\") where id=$id");
	echo "<body><script language='javascript'>opener.location.reload();window.close();void(null);</script></body>";
}

function completar_rcp() // funcion inactiva que peritia capturar recibos de caja provisional para garantias en efectivo. Esta funcion ya no se usa
{
	global $idr,$idc;
	die('Opcion deshabilitada!');
	$R=qo("select *,t_oficina(oficina) as noficina from recibo_caja_prov where id=$idr");
	$Oficina=qo("select * from oficina where id=$R->oficina");
	$Cita=qo("select * from cita_servicio where id=$idc");
	$Cli=qo("select * from sin_autor where id=$R->autorizacion");
	$Sin=qo("select numero,fec_autorizacion,aseguradora from siniestro where id=$R->siniestro");
	$Aseguradora=qo("select * from aseguradora where id=$Sin->aseguradora");
	$Nusuario=$_SESSION['Nombre'];
	html('RECIBO DE CAJA PROVISIONAL');
	echo "<script language='javascript'>
			function carga()
			{
				centrar(700,400);
			}
			function validar_datos()
			{
				with(document.forma)
				{
					if(alltrim(direccion.value)=='')
					{
						alert('Debe escribir una direcci�n v�lida del cliente');
						direccion.style.backgroundColor='ffffdd';
						direccion.focus();
						return false;
					}
					if(confirm('Desea grabar la informaci�n e imprimir el recibo de caja provisional?'))
					{
						submit();
					}
				}
			}
		</script>
		<body onload='carga()'>
		<h3>RECIBO DE CAJA PROVISIONAL NUMERO $Oficina->sigla ".str_pad($R->consecutivo, 6,'0',STR_PAD_LEFT)."</h3>
		<table>
		<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
			<tr><td align='right'>Fecha:</td><td><b>$R->fecha_recepcion</b></td><td align='right'>Siniestro No.</td><td><b>$Sin->numero</b></td></tr>
			<tr><td align='right'>Cliente:</td><td><b>$R->nombre</b></td><td align='right'>Identificaci�n:</td><td><b>".coma_format($R->identificacion)."</b></td></tr>
			<tr><td align='right'>Oficina:</td><td><b>$Oficina->nombre</b></td><td align='right'>Aseguradora:</td><td><b>$Aseguradora->nombre</b></td></tr>
			<tr><td align='right'>Fecha Autorizaci�n Siniestro:</td><td><b>$Sin->fec_autorizacion</b></td><td align='right'>Valor:</td><td><b>$ ".coma_format($R->valor)."</b></td></tr>
			<tr><td align='right'>Direcci�n del Cliente:</td><td colspan=3><input type='text' name='direccion' size='50' maxlength='100'></td></tr>
			<tr><td align='right'>Recibido por:</td><td colspan=3><input type='text' name='recibido_por' value='$Nusuario' size='50' readonly></td></tr>
			<tr><td align='center' colspan=4><input type='button' value='Grabar e imprimir' onclick='validar_datos()' style='height:25px;width:200px;font-weight:bold'></td></tr>
			</table>
			<input type='hidden' name='Acc' value='completar_rcp_ok'>
			<input type='hidden' name='idr' value='$idr'>
			<input type='hidden' name='idc' value='$idc'>
		</form>
		</body>";
}

function completar_rcp_ok() // funcion que guardaba un recibo de caja provisional para garantias en efectivo. Esta funcion ya no se usa
{
	global $idr,$idc,$direccion,$recibido_por;
	q("update recibo_caja_prov set direccion='$direccion', recibido_por='$recibido_por' where id=$idr");
	header("location:zcitas.php?Acc=imprimir_rcp&idr=$idr&idc=$idc");
}

function imprimir_rcp() // funcion que imprime recibo de caja provisional. Esta funcion ya no se usa
{
	global $idr,$idc;
	die('Opcion deshabilitada!');
	$R=qo("select *,t_oficina(oficina) as noficina from recibo_caja_prov where id=$idr");
	if($idc)
	{
		if($R->cita!=$idc) q("update recibo_caja_prov set cita=$idc where id=$idr");
	}
	else $idc=$R->cita;
	if(!$R->direccion || !$R->recibido_por)
		header("location:zcitas.php?Acc=completar_rcp&idr=$idr&idc=$idc");
	$Cita=qo("select * from cita_servicio where id=$idc");
	$Oficina=qo("select * from oficina where id=$R->oficina");
	$Cli=qo("select * from sin_autor where id=$R->autorizacion");
	$Sin=qo("select numero,fec_autorizacion from siniestro where id=$R->siniestro");

	include('inc/pdf/fpdf.php');
	$P=new pdf('P','mm','Letter');
	$P->AddFont("c128a","","c128a.php");
	$P->AliasNbPages();
	$P->setTitle("RECIBO DE CAJA PROVISIONAL");
	$P->setAuthor("Arturo Quintero Rodriguez www.aoacolombia.com arturoquintero@aoacolombia.com");
	$P->Numeracion=false;
	$P->SetAutoPageBreak(false);
	$P->setFillColor(230,230,230);
	$P->SetTopMargin('5');
	$P->AddPage('P');
	$P->Image('../img/LOGO_AOA_200.jpg',45,5,30,12);
	$P->setfont('Arial','B',10);
	$P->SetXY(100,5);
	$P->SetTextColor(0,0,0);
	$P->Cell(90,5,'ADMISTRACI�N OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
	$P->setxy(100,9);
	$P->Cell(90,5,'NIT.: 900.174.552-5',0,0,'C');
	$P->setfont('Arial','B',16);
	$P->setxy(100,16);
	$P->Cell(110,5,'RECIBO DE CAJA PROVISIONAL',0,0,'L');
	$P->SETFONT('Times','B',16);
	$P->setxy(130,22);
	$P->Cell(110,5,'No.',0,0,'L');
	$P->SETFONT('Times','B',16);
	$P->setxy(140,22);
	$P->settextcolor(200,0,0);
	$P->Cell(20,5,$Oficina->sigla.str_pad($R->consecutivo,6,'0',STR_PAD_LEFT),0,0,'L');
	$P->settextcolor(0,0,0);
	$P->setfont('Arial','',8);
	$P->setxy(20,17);
	$P->cell(80,4,"CARRERA 69B No. 98A-10 BARRIO MORATO",0,0,'C');
	$P->setxy(20,21);
	$P->cell(80,4,"PBX (571) 756 05 10  FAX (571) 756 05 12",0,0,'C');
	$P->setxy(20,25);
	$P->cell(80,4,"www.aoacolombia.com - Bogot�, D.C. - Colombia",0,0,'C');
	$P->rect(100,14,90,14);
	$P->setfont('Arial','',10);
	$P->setxy(20,30);
	$P->Cell(22,5,'Ciudad:',1,0,'L');
	$P->Cell(100,5,$R->noficina,1,0,'L');
	$P->Cell(20,10,'Fecha:',1,0,'C');
	$P->Cell(38,10,$R->fecha_recepcion,1,0,'C');
	$P->setxy(20,$P->y+5);
	$P->Cell(22,5,'Siniestro:',1,0,'L');
	$P->cell(100,5,$Sin->numero.' F.Autorizaci�n: '.$Sin->fec_autorizacion,1,0,'L');
	$P->setxy(20,$P->y+5);
	$P->Cell(22,5,'Recibido de:',1,0,'L');
	$P->Cell(108,5,trim($Cli->nombre),1,0,'L');
	$P->Cell(8,5,'Id:',1,0,'C');
	$P->Cell(42,5,coma_format($R->identificacion),1,0,'R',0);
	$P->setxy(20,$P->y+5);
	$P->Cell(22,5,'Direcci�n:',1,0,'L');
	$P->Cell(108,5,$R->direccion,1,0,'L');
	$P->Cell(8,5,'$',1,0,'C');
	$P->Cell(42,5,coma_format($R->valor),1,0,'R',1);
	$P->setxy(20,$P->y+5);
	$P->multicell(180,5,'En Letras: '.enletras($R->valor,1),1,'J',1);
	$P->setxy(20,$P->y);
	$P->multicell(180,5,'Por concepto de: Dep�sito en efectivo en garant�a para el servicio de veh�culo de reemplazo correspondiente al siniestro n�mero: '.
	$Sin->numero.'. Veh�culo que se entrega: '.$Cita->placa,1,'J');
	$P->setxy(20,$P->y+2);
	$P->setfont('Arial','',8);
	$ny=$P->y+5;
	$ny=$P->y+4;
	$P->setxy(20,80);$P->cell(110,5,'Recibido por:',1,0,'L');
	$P->setxy(20,85);$P->cell(110,5,$R->recibido_por,1,0,'L');
	$P->setxy(130,80);$P->cell(70,17,' ',1);
	$P->setxy(130,97);$P->cell(70,4,'Firma y sello',1,0,'L');
	$P->setxy(20,102);$P->Cell(180,4,'DEVOLUCION GARANTIA',1,0,'C',1);
	$P->setxy(20,106);$P->cell(110,5,'Entregado por:',1,0,'L');
	$P->setxy(20,111);$P->cell(110,18,' ',1,0,'L');
	$P->setxy(130,106);$P->cell(70,19,' ',1);
	$P->setxy(130,125);$P->cell(70,4,'Firma - Dinero Recibido',1,0,'L');
	$P->setxy(20,90);$P->SetFont("c128a","",12);	$P->cell(110,11, uccean128('FA'.str_pad($R->identificacion,12,'0',STR_PAD_LEFT).str_pad($R->consecutivo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$P->Image('../img/LOGO_AOA_200.jpg',45,135,30,12);
	$P->setfont('Arial','B',10);
	$P->SetXY(100,135);
	$P->SetTextColor(0,0,0);
	$P->Cell(90,5,'ADMISTRACI�N OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
	$P->setxy(100,139);
	$P->Cell(90,5,'NIT.: 900.174.552-5',0,0,'C');
	$P->setfont('Arial','B',16);
	$P->setxy(100,146);
	$P->Cell(110,5,'RECIBO DE CAJA PROVISIONAL',0,0,'L');
	$P->SETFONT('Times','B',16);
	$P->setxy(130,152);
	$P->Cell(110,5,'No.',0,0,'L');
	$P->SETFONT('Times','B',16);
	$P->setxy(140,152);
	$P->settextcolor(200,0,0);
	$P->Cell(20,5,$Oficina->sigla.str_pad($R->consecutivo,6,'0',STR_PAD_LEFT),0,0,'L');
	$P->settextcolor(0,0,0);
	$P->setfont('Arial','',8);
	$P->setxy(20,147);
	$P->cell(80,4,"CARRERA 69B No. 98A-10 BARRIO MORATO",0,0,'C');
	$P->setxy(20,151);
	$P->cell(80,4,"PBX (571) 756 05 10  FAX (571) 756 05 12",0,0,'C');
	$P->setxy(20,155);
	$P->cell(80,4,"www.aoacolombia.com - Bogot�, D.C. - Colombia",0,0,'C');
	$P->rect(100,144,90,14);
	$P->setfont('Arial','',10);
	$P->setxy(20,160);
	$P->Cell(22,5,'Ciudad:',1,0,'L');
	$P->Cell(100,5,$R->noficina,1,0,'L');
	$P->Cell(20,10,'Fecha:',1,0,'C');
	$P->Cell(38,10,$R->fecha_recepcion,1,0,'C');
	$P->setxy(20,$P->y+5);
	$P->Cell(22,5,'Siniestro:',1,0,'L');
	$P->cell(100,5,$Sin->numero.' F.Autorizaci�n: '.$Sin->fec_autorizacion,1,0,'L');
	$P->setxy(20,$P->y+5);
	$P->Cell(22,5,'Recibido de:',1,0,'L');
	$P->Cell(108,5,trim($Cli->nombre),1,0,'L');
	$P->Cell(8,5,'Id:',1,0,'C');
	$P->Cell(42,5,coma_format($R->identificacion),1,0,'R',0);
	$P->setxy(20,$P->y+5);
	$P->Cell(22,5,'Direcci�n:',1,0,'L');
	$P->Cell(108,5,$R->direccion,1,0,'L');
	$P->Cell(8,5,'$',1,0,'C');
	$P->Cell(42,5,coma_format($R->valor),1,0,'R',1);
	$P->setxy(20,$P->y+5);
	$P->multicell(180,5,'En Letras: '.enletras($R->valor,1),1,'J',1);
	$P->setxy(20,$P->y);
	$P->multicell(180,5,'Por concepto de: Dep�sito en efectivo en garant�a para el servicio de veh�culo de reemplazo correspondiente al siniestro n�mero: '.
	$Sin->numero.'. Veh�culo que se entrega: '.$Cita->placa,1,'J');
	$P->setxy(20,$P->y+2);
	$P->setfont('Arial','',8);
	$ny=$P->y+5;
	$ny=$P->y+4;
	$P->setxy(20,210);$P->cell(110,5,'Recibido por:',1,0,'L');
	$P->setxy(20,215);$P->cell(110,5,$R->recibido_por,1,0,'L');
	$P->setxy(130,210);$P->cell(70,17,' ',1);
	$P->setxy(130,227);$P->cell(70,4,'Firma y sello',1,0,'L');
	$P->setxy(20,232);$P->Cell(180,4,'DEVOLUCION GARANTIA',1,0,'C',1);
	$P->setxy(20,236);$P->cell(110,5,'Entregado por:',1,0,'L');
	$P->setxy(20,241);$P->cell(110,18,' ',1,0,'L');
	$P->setxy(130,236);$P->cell(70,19,' ',1);
	$P->setxy(130,255);$P->cell(70,4,'Firma - Dinero Recibido',1,0,'L');
	$P->setxy(20,220);$P->SetFont("c128a","",12);	$P->cell(110,11, uccean128('FA'.str_pad($R->identificacion,12,'0',STR_PAD_LEFT).str_pad($R->consecutivo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$P->Output($Archivo);
}

class d_tarjeta // clase que se usa para pintar datos del tarjeta habiente en la cabecera del acta de entrega/devolucion para garantias de tarjeta credito
{
	var $Nombre_tarjeta_habiente='';  // nombre del due�o de la tarjeta quien autoriza el congelamiento de cupo
	var $Identificacion=0; // identificaci�n del tarjeta habiente quien autoriza el congelamiento de cupo
	var $Franquicia=''; // nombre de la franquicia
	var $Numero_tarjeta=''; // numero de la tarjeta de credito
	var $Vencimiento_mes=''; // mes de vencimiento de la tarjeta de cr�dito
	var $Vencimiento_ano='';  // a�o de vencimiento de la tarjeta de cr�dito
	var $Valor_garantia=''; // monto de la garant�a a ser congelado

	function d_tarjeta($D) // funcion creadora de la instancia
	{
		$this->Nombre_tarjeta_habiente=$D->nombre;
		$this->Identificacion=$D->identificacion;
		$this->Franquicia=$D->nfranq;
		$this->Numero_tarjeta=$D->numero;
		$this->Vencimiento_mes=$D->vencimiento_mes;
		$this->Vencimiento_ano=$D->vencimiento_ano;
		$this->Valor_garantia=$D->valor;
	}
}

class d_efectivo // clase que se usa para pintar datos del tarjeta habiente en la cabecera del acta de entrega/devolucion para garantias de efectivo
{
	var $Nombre_tarjeta_habiente='';  // nombre del due�o de la tarjeta quien autoriza el congelamiento de cupo
	var $Identificacion=0; // identificaci�n del tarjeta habiente quien autoriza el congelamiento de cupo
	var $Numero_cuenta=''; // n�mero de cuenta
	var $Banco=''; // Banco
	var $Tipo_cuenta=''; // mes de vencimiento de la tarjeta de cr�dito
	var $Ciudad_cuenta='';  // a�o de vencimiento de la tarjeta de cr�dito
	var $Valor_garantia=''; // monto de la garant�a a ser congelado

	function d_tarjeta($D)
	{
		$this->Nombre_tarjeta_habiente=$D->devol_ncuenta;
		$this->Identificacion=$D->identificacion_devol;
		$this->Numero_cuenta=$D->devol_cuenta_banco;
		$this->Banco=$D->devol_banco;
		$this->Tipo_cuenta=$D->devol_tipo_cuenta;
		$this->Ciudad_cuenta=$D->ciudad_cuenta_devol;
		$this->Valor_garantia=$D->valor;
	}
}

function imprimir_documentos_venta(){
	global $idc,$Fdevol,$Hdevol;
	sesion(); //verifica la sesion del usuario
	
	$Cita=qo("select * from cita_servicio where id=$idc"); // trae los datos de la cita
	html('IMPRESION DOCUMENTOS DE VENTA');
	$fechaEntrega = pinta_fc('forma','Fdevol',date('Y-m-d',strtotime($Cita->fecha)));
	 
	 $hora = pinta_hora('forma','Hdevol',$Cita->hora);	
	//echo "try";
	//exit;
	$Fec_entrega=$Fdevol.' '.$Hdevol;
	$Siniestro=qo("select * from siniestro where id=$Cita->siniestro"); // trae los datos del siniestro
	$Aseguradora=qo("select * from aseguradora where id=$Siniestro->aseguradora"); // trae los datos de la aseguradora
	$Oficina=qo("select * from oficina where id=$Cita->oficina"); // trae los datos de la oficina
	$Vehiculo=qo("select * from vehiculo where placa='$Cita->placa' "); // trae los datos del vehiculo
	$vehiculo2 = qo("Select linea.nombre as nom_linea, marca.nombre as nom_marca
		from  aoacol_aoacars.vehiculo as veh inner join aoacol_aoacars.linea_vehiculo as linea on veh.linea = linea.id
		inner join aoacol_aoacars.marca_vehiculo as marca on marca.id = linea.marca where placa = '$Cita->placa'  limit 1");
	$claseVehiculo = qo("select concat( cl.nombre,'  ',cl.tipo) as clase from vehiculo v inner join  clase_vehiculo cl on v.clase = cl.id where v.placa = '$Cita->placa' limit 1");	
	$carroceria = qo("select ca.nombre as nombre from vehiculo ve inner join carroceria ca on ve.carroceria = ca.id where ve.placa = '$Cita->placa'");
	$modalidadDeTendecia = qo("select mt.nombre as modalidad from vehiculo ve left join modalidad_tenencia mt on ve.modalidad_de_compra = mt.id where ve.placa = '$Cita->placa'");
	$ubicaciones1=qo("select max(id) as id from ubicacion where vehiculo=$Vehiculo->id  "); // trae los datos ubicaciones1
	$ubicaciones2=qo("select odometro_final from ubicacion where id=$ubicaciones1->id "); // trae los datos ubicaciones2
	$kilome = $ubicaciones2->odometro_final;
	$Linea=qo("select * from linea_vehiculo where id=$Vehiculo->linea"); // trae los datos de la linea del vehiculo
	$servicio = qo("select s.nombre from aoacol_aoacars.vehiculo v inner join aoacol_aoacars.tipo_servicio s on v.servicio = s.id where v.placa = '$Cita->placa'");
	
	$Autorizado_nombre='';$Autorizado_id=0;$Autortizado_direccion='';$Autorizado_celular='';$Autorizado_email='';
	$TG='';$TGV='';
	include("views/actas_ventas/actas_ventas.php");
	documentos_venta($Siniestro,$fechaEntrega,$Oficina,$Vehiculo,$Linea,$vehiculo2,$kilome,$claseVehiculo,$carroceria,$modalidadDeTendecia,$servicio);
}
function get_nombre_dia($fecha){
   $fechats = strtotime($fecha); //pasamos a timestamp

//el parametro w en la funcion date indica que queremos el dia de la semana
//lo devuelve en numero 0 domingo, 1 lunes,....
switch (date('w', $fechats)){
    case 0: return "Domingo"; break;
    case 1: return "Lunes"; break;
    case 2: return "Martes"; break;
    case 3: return "Miercoles"; break;
    case 4: return "Jueves"; break;
    case 5: return "Viernes"; break;
    case 6: return "Sabado"; break;
}
}

function imprimir_acta() // imprime el acta de entrega/devolucion
{
	
	global $idc,$Fdevol,$Hdevol;
	sesion(); //verifica la sesion del usuario
	$A_tarjeta=array();
	$A_reembolsable=array();
	$Cita=qo("select * from cita_servicio where id=$idc"); // trae los datos de la cita
	$Fec_entrega = date('Y-m-d',strtotime(aumentadias($Cita->fecha, $Cita->dias_servicio))).' '.$Cita->hora; // calcula la fecha de devolucion
 	
    $Siniestro=qo("select * from siniestro where id=$Cita->siniestro");
	
	$Oficina=qo("select * from oficina where id=$Cita->oficina");
	

	
	if(!$Fdevol)
	{ // formulario en el que solicita la fecha de devoluci�n para tener encuenta si es domingo, festivo o pico y placa y correr la fecha y hora.
		html('IMPRESION ACTA DE ENTREGA Y DEVOLUCION');
		$varDia = get_nombre_dia($Fec_entrega);
		$festivos = new Festivos();
		
		$myDate = strtotime($Fec_entrega);
		

		
		if($varDia == "Domingo" || $varDia == "Sabado"){
			$style = "<style>.colorDia{
				color: red;
			}</style>";
			
		$ver ='false';
			
		}else{
			$style = "<style>.colorDia{
				color: blue;
			}</style>";
			
			$ver ='true';
		}
		
		
		
		if($festivos->esFestivo(date('d', $myDate), date('m',$myDate))){
			$diaD =  "$style <style>.colorDiaFestivo{
				color: red;
			}</style> <h2>El dia de devolucion  es el: <div disabled=".$ver."   class='colorDia'>".$varDia."</div> ".date("Y-m-d", $myDate)."<div class='colorDiaFestivo'> Es un dia festivo.</div><h2>";
		}else{
			$diaD = "$style <h2>El dia de devolucion es el: <div  disabled=".$ver."  class='colorDia'>".$varDia."</div> ".date("Y-m-d", $myDate)." No es un dia festivo</h2>";
		}
		
		echo '<style>   
		 .alert {
  padding: 20px;
  background-color:green; /* Red */
  color: white;
  margin-bottom: 15px;
}

/* The close button */
.closebtn {
  margin-left: 15px;
  color: white;
  font-weight: bold;
  float: right;
  font-size: 22px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.3s;
}

/* When moving the mouse over the close button */
.closebtn:hover {
  color: black;
}
	</style>	
		<div class="alert alert-success">
				  <strong>Pipo y placa '.$Oficina->nombre.' !</strong>  <h5> '.$Oficina->picoyplaca.'<h5>
				</div>';   


				
		    if( $varDia == "Sabado"){
			 
			 if(!$festivos->esFestivo(date('d', $Fec_entrega), date('m',$Fec_entrega))){
			
			

			if($Oficina->hora_inicial_festivo || $Oficina->hora_final_festivo){
				 
				  echo "<body><script language='javascript'>centrar(700,500);</script>
		<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
			
			 <h3  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial_festivo hasta $Oficina->hora_final_festivo <h3> 
			 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial_festivo)." $diaD 
			<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
			<input type='hidden' name='Acc' value='imprimir_acta'>
			<input type='hidden' name='idc' value='$idc'><br><br>
			<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
			vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
			contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
		</form></body>";
		
		
		
			 }else{
				 
				  $fecha_mas_dia  = date('Y-m-d',strtotime($Fec_entrega."+ 1 days")); 
				 
				   echo "<body><script language='javascript'>centrar(700,500);</script>
		<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
			Fecha de Devoluci�n: ".pinta_fc('forma','Fdevol',date('Y-m-d',strtotime($fecha_mas_dia)))."
			 <h3  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial_festivo hasta $Oficina->hora_final_festivo <h3> 
			 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial_festivo)." $diaD 
			<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
			<input type='hidden' name='Acc' value='imprimir_acta'>
			<input type='hidden' name='idc' value='$idc'><br><br>
			<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
			vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
			contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
		</form></body>";
			 }
				
				
			 }else{
			
         			
				
			if($Oficina->hora_inicial_sabado || $Oficina->hora_final_sabado ){
				 
				  echo "<body><script language='javascript'>centrar(700,500);</script>
		<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
			
			 <h3  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial_sabado hasta $Oficina->hora_final_sabado <h3> 
			 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial_sabado)." $diaD 
			<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
			<input type='hidden' name='Acc' value='imprimir_acta'>
			<input type='hidden' name='idc' value='$idc'><br><br>
			<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
			vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
			contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
		</form></body>";
				 
			 }

               
				 
			 }
			 
			
		 }
		 
		 
		 
		 
		  if( $varDia == "Lunes" || $varDia == "Martes" || $varDia == "Miercoles" || $varDia == "Jueves"  || $varDia == "Viernes" ){
			 
			 $fecha_mas_dia  = date('Y-m-d',strtotime($Fec_entrega."+ 1 days")); 
			 
			 
			 
			 
			  if($festivos->esFestivo(date('d', $Fec_entrega), date('m',$Fec_entrega))){
			//El dia es no fe es stivo
			
			   if($Oficina->hora_inicial || $Oficina->hora_final ){
				  echo "<body><script language='javascript'>centrar(700,500);</script>
		<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
			
			 <h3  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial hasta $Oficina->hora_final <h3> 
			 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial)." $diaD 
			<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
			<input type='hidden' name='Acc' value='imprimir_acta'>
			<input type='hidden' name='idc' value='$idc'><br><br>
			<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
			vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
			contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
		</form></body>";
		
			 } 

            }else{
				
				//El dia es festivo mas un dia 
				  
		   if(!$festivos->esFestivo(date('d', $fecha_mas_dia), date('m',$fecha_mas_dia))){
			
			   //El dia mas un dia no es festivo  
			 if($Oficina->hora_inicial || $Oficina->hora_final ){
				  echo "<body><script language='javascript'>centrar(700,500);</script>
				<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
					
					<h5  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial hasta $Oficina->hora_final <h5> 
					 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial)." $diaD 
					<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
					<input type='hidden' name='Acc' value='imprimir_acta'>
					<input type='hidden' name='idc' value='$idc'><br><br>
					<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
					vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
					contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
				</form></body>";
					 } 

			  
            }else{
				  
				 //El dia mas un dia  es festivo    
				 if($Oficina->hora_inicial || $Oficina->hora_final ){
				   
				   
				  echo "<body><script language='javascript'>centrar(700,500);</script>
		<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
			Fecha de Devoluci�n: ".pinta_fc('forma','Fdevol',date('Y-m-d',strtotime($fecha_mas_dia)))."
			 <h5  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial hasta $Oficina->hora_final <h5> 
			 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial)." $diaD 
			<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
			<input type='hidden' name='Acc' value='imprimir_acta'>
			<input type='hidden' name='idc' value='$idc'><br><br>
			<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
			vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
			contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
		</form></body>";
		
			 } 

			  };
					   
					   
					  

			 
			  };


			 
			 
			
			
		 }
		
		
		
		
		
		 if( $varDia == "Domingo"){
			  $fecha_mas_dia  = date('Y-m-d',strtotime($Fec_entrega."+ 1 days")); 
			  
			  if( $festivos->esFestivo(date('d', $fecha_mas_dia), date('m',$fecha_mas_dia))){
				
				 echo "<body><script language='javascript'>centrar(700,500);</script>
		<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
			Fecha de Devoluci�n: ".pinta_fc('forma','Fdevol',date('Y-m-d',strtotime($fecha_mas_dia)))."
			 <h5  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial hasta $Oficina->hora_final <h5> 
			 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial)." $diaD 
			<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
			<input type='hidden' name='Acc' value='imprimir_acta'>
			<input type='hidden' name='idc' value='$idc'><br><br>
			<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
			vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
			contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
		</form></body>";
		
		
		
		
		
				}else{
				
				 
				 echo "<body><script language='javascript'>centrar(700,500);</script>
		<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
			Fecha de Devoluci�n: ".pinta_fc('forma','Fdevol',date('Y-m-d',strtotime($fecha_mas_dia)))."
			 <h5  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial hasta $Oficina->hora_final <h5> 
			 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial)." $diaD 
			<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
			<input type='hidden' name='Acc' value='imprimir_acta'>
			<input type='hidden' name='idc' value='$idc'><br><br>
			<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
			vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
			contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
		</form></body>";
		
		
		
			  }
	  
			  }
			  
			 
			
		 
		 
		 
		  if(!$festivos->esFestivo(date('d', $myDate), date('m',$myDate))){
			  
			 
			  if(!$Oficina->hora_inicial_festivo || !$Oficina->hora_final_festivo  ){
				  
			  echo "<body><script language='javascript'>centrar(700,500);</script>
		<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
			
			 <h3  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial_festivo hasta $Oficina->hora_final_festivo <h3> 
			 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial_festivo)." $diaD 
			<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
			<input type='hidden' name='Acc' value='imprimir_acta'>
			<input type='hidden' name='idc' value='$idc'><br><br>
			<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
			vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
			contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
		</form></body>";
				  
				 
			 }else{
				 
				 
				  $fecha_mas_dia  = date('Y-m-d',strtotime($Fec_entrega."+ 1 days")); 
				
                   if(!$festivos->esFestivo(date('d', $fecha_mas_dia), date('m',$fecha_mas_dia))){
					   
					 if(!$Oficina->hora_inicial_festivo || !$Oficina->hora_final_festivo  ){   

						echo "<body><script language='javascript'>centrar(700,500);</script>
				<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
					Fecha de Devoluci�n: ".pinta_fc('forma','Fdevol',date('Y-m-d',strtotime($fecha_mas_dia)))."
					 <h5  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial hasta $Oficina->hora_final <h5> 
					 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial)." $diaD 
					<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
					<input type='hidden' name='Acc' value='imprimir_acta'>
					<input type='hidden' name='idc' value='$idc'><br><br>
					<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
					vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
					contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
				</form></body>";
				
				
			

					 }else{
						 
				 $fecha_mas_dia  = date('Y-m-d',strtotime($Fec_entrega."+ 1 days")); 
					
					$varDia = get_nombre_dia($fecha_mas_dia);
					
					
						 			
				    if( $varDia == "Domingo"){
						
				if($Oficina->hora_inicial_domingo|| $Oficina->hora_final_sabado ){
				 
				  echo "<body><script language='javascript'>centrar(700,500);</script>
		<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
			
			 <h3  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial_domingo hasta $Oficina->hora_final_sabado <h3> 
			 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_final_sabado)." $diaD 
			<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
			<input type='hidden' name='Acc' value='imprimir_acta'>
			<input type='hidden' name='idc' value='$idc'><br><br>
			<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
			vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
			contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
		</form></body>";
				 
			 }else{
				 
				 
				 $fecha_mas_dia_dos  = date('Y-m-d',strtotime($fecha_mas_dia."+ 1 days"));  
				 
				 
				 
				 
				 
				 
				 
			 }

	

						
					} 
				
					 if( $varDia == "Sadado"){
						
						if($Oficina->hora_inicial_sabado || $Oficina->hora_final_sabado ){
				 
				  echo "<body><script language='javascript'>centrar(700,500);</script>
		<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
			
			 <h3  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial_sabado hasta $Oficina->hora_final_sabado <h3> 
			 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial_sabado)." $diaD 
			<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
			<input type='hidden' name='Acc' value='imprimir_acta'>
			<input type='hidden' name='idc' value='$idc'><br><br>
			<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
			vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
			contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
		</form></body>";
				 
			 }

	
	
					}
                   if( $varDia == "Lunes" || $varDia == "Martes" || $varDia == "Miercoles" || $varDia == "Jueves"  || $varDia == "Viernes" ){

						 if($Oficina->hora_inicial || $Oficina->hora_final ){
						  echo "<body><script language='javascript'>centrar(700,500);</script>
		<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
			
			 <h3  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial hasta $Oficina->hora_final <h3> 
			 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial)." $diaD 
			<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
			<input type='hidden' name='Acc' value='imprimir_acta'>
			<input type='hidden' name='idc' value='$idc'><br><br>
			<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
			vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
			contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
		</form></body>";
						 }
						
					}					
						 
						 
						 
						 
					 }
					 
					 
					 
					 
				   }else{
					   
					   
					   	echo "<body><script language='javascript'>centrar(700,500);</script>
		<form action='zcitaszDiv.php' target='_self' method='POST' name='forma' id='forma'>
			Fecha de Devoluci�n: ".pinta_fc('forma','Fdevol',date('Y-m-d',strtotime($fecha_mas_dia)))."
			 <h5  style='color:#34b233 '> El horario de atenci�n es de $Oficina->hora_inicial hasta $Oficina->hora_final <h5> 
			 Hora de Devoluci�n : ".pinta_hora('forma','Hdevol',$Oficina->hora_inicial)." $diaD 
			<input type='submit' name='continuar' id='continuar' value=' IMPRIMIR '>
			<input type='hidden' name='Acc' value='imprimir_acta'>
			<input type='hidden' name='idc' value='$idc'><br><br>
			<b style='font-size:16px'>ESTIMADO (A) USUARIO (A) ".$_SESSION['Nombre']." Tenga en cuenta que si en el momento de la devoluci�n del automovil hay restricci�n
			vehicular, debe modificar la entrega a la siguiente hora h�bil la cual no tenga restricci�n seg�n las leyes y normas vigentes en su ciudad. De lo contrario
			contin�e con la impresi�n del acta sin hacer ninguna modificaci�n. <br><br>Gracias.</b>
		</form></body>";
		
		
		
					   
					   
				   }
				
				 
			 }
			
		 }
	
		
    die();
	}
	//echo "try";
	//exit;
	$Fec_entrega=$Fdevol.' '.$Hdevol;
	$Siniestro=qo("select * from siniestro where id=$Cita->siniestro"); // trae los datos del siniestro
	$Aseguradora=qo("select * from aseguradora where id=$Siniestro->aseguradora"); // trae los datos de la aseguradora
	$Oficina=qo("select * from oficina where id=$Cita->oficina"); // trae los datos de la oficina
	$Vehiculo=qo("select * from vehiculo where placa='$Cita->placa' "); // trae los datos del vehiculo
	$ubicaciones1=qo("select max(id) as id from ubicacion where vehiculo=$Vehiculo->id  "); // trae los datos ubicaciones1
	$ubicaciones2=qo("select odometro_final from ubicacion where id=$ubicaciones1->id "); // trae los datos ubicaciones2
	$kilome = $ubicaciones2->odometro_final;
	$Linea=qo("select * from linea_vehiculo where id=$Vehiculo->linea"); // trae los datos de la linea del vehiculo
	$Autorizado_nombre='';$Autorizado_id=0;$Autortizado_direccion='';$Autorizado_celular='';$Autorizado_email='';
	$TG='';$TGV='';
	if($Autorizacion=q("select a.*,f.nombre as nfranq,f.tipo as tf from sin_autor a,franquisia_tarjeta f  where a.siniestro=$Siniestro->id and a.estado='A' and a.franquicia=f.id ")) // trae los datos de la autorizacion
	{
		$Autorizaciones='';
		$Contador=1;
		$TH='Tarjeta Habiente(s): ';
		while($A=mysql_fetch_object($Autorizacion))
		{
			if($A->data)
			{
				$Rd=desencripta_data($A->id); // desencripta los datos para imprimirlos en el acta
				$A->identificacion=$Rd['identificacion'];
				$A->numero=$Rd['numero'];
				$A->nbanco=$Rd['banco'];
				$A->vencimiento_mes=$Rd['vencimiento_mes'];
				$A->vencimiento_ano=$Rd['vencimiento_ano'];
				$A->num_autorizacion=$Rd['num_autorizacion'];
				$A->funcionario=$Rd['funcionario'];
				$A->codigo_seguridad=$Rd['codigo_seguridad'];
			}
			if($A->tf=='C' /* tarjeta de credito */) $A_tarjeta[]=new d_tarjeta($A); // si hay varias autorizaciones, las acumula en un arreglo
			if($A->tf=='E' || $A->tf=='D') $A_reembolsable[]=new d_efectivo($A); // acumula las garantias reembolsables en efectivo
			$Autorizaciones.="Aut $Contador: $A->nombre id:$A->identificacion $A->nfranq ".r($A->numero,4)." # $A->num_autorizacion Vence: $A->vencimiento_mes-$A->vencimiento_ano. ";
			$TG.=($TG?", ":"").$A->nfranq;$TGV.=($TGV?", ":"").$A->numero_voucher;
			$TH.="$A->nombre /";
			if(!$Autorizado_nombre)
			{
				if($Cliente=qo("select * from cliente where identificacion='$A->identificacion'")) // trae los datos del cliente
				{
					$Autorizado_nombre=$Cliente->nombre.' '.$Cliente->apellido;$Autorizado_id=$A->identificacion;
					$Autorizado_direccion=$Cliente->direccion;$Autorizado_celular=$Cliente->celular;$Autorizado_email=$Cliente->email_e;
				}
			}
		}
	}

}

function insertar_domicilio() // formulario para insertar la informaci�n de un domicilio a la cita
{
	global $id;
	html('Crear Domicilio');
	echo "<script language='javascript'>
			function carga()
			{
				alert('Generaci�n de Solicitud hecha satisfactoriamente');
			}
		</script>
		<body><script language='javascript'>centrar(500,300);</script>
		<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
			<h3>Creaci�n de Domicilio</h3>
			<table align='center'>
			<tr><td align='ritght'>Direcci�n Domicilio</td><td><input type='text' name='dir_domicilio' size='70' maxlength='200'></td></tr>
			<tr><td align='ritght'>Tel�fono Domicilio</td><td><input type='text' name='tel_domicilio' size='50' maxlength='50'></td></tr>
			<tr><td colspan='2' align='center'><input type='submit' value='Continuar'></td></tr></table>
			<input type='hidden' name='Acc' value='insertar_domicilio_ok'>
			<input type='hidden' name='id' value='$id'>
		</form>
		</body>";
}

function insertar_domicilio_ok() // graba la informaci�n del domicilio en la cita
{
	global $id,$dir_domicilio,$tel_domicilio;
	q("update cita_servicio set dir_domicilio='$dir_domicilio',tel_domicilio='$tel_domicilio' where id=$id ");
	graba_bitacora('cita_servicio','M',$id,'Asigna Domicilio');
	echo "<body><script language='javascript'>window.close();void(null);opener.location.reload();</script></body>";
}

function arribo_asegurado() // funcion que marca el arribo del asegurado en la cita
{
	global $id;
	$Ahora=date('Y-m-d H:i:s');
	q("update cita_servicio set arribo='$Ahora' where id=$id"); // actualiza la tabla de citas
	graba_bitacora("cita_servicio","M",$id,"Marca arribo asegurado");  // graba la bitacora de citas
	$D=qo("select * from cita_servicio where id=$id"); // trae los datos de la cita
	html(); // pinta cabeceras html
	// dispara el proceso de toma de imaggen
	echo "<body><script language='javascript'>alert('Marcacion de Arribo del Asegurado Exitosa');
		modal('zingreso_recepcion.php?n=$D->conductor&m=VEHICULO DE REEMPLAZO&idcita=$id',0,0,600,600,'ingreso_recepcion');
		parent.Parar_recarga=false;parent.cambio_fecha();
		</script></body>";
}

function marcar_entregado() // hace la marcaci�n de vehiculo entregado al asegurado - para medir tiempos de respuesta
{
	global $id;
	$Operario=$_SESSION['Id_alterno'];
	$Ahora=date('Y-m-d H:i:s');
	q("update cita_servicio set momento_entrega='$Ahora',operario=$Operario where id=$id"); // actualiza la cita
	graba_bitacora("cita_servicio","M",$id,"Marca entrega vehiculo"); // graba la bitacora
	echo "<body><script language='javascript'>alert('Marcacion de Entrega de Vehiculo Exitosa');parent.Parar_recarga=false;parent.cambio_fecha();
		</script></body>";
}

function formulario_entrega() // Validaci�n de la entrega para ve si permite ditgitar los datos de la entrega o cancela o reagenda o cumple y no toma serivcio - Formulario
{
	global $idcita,$estado,$USUARIO;
	$C=qo("select * from cita_servicio where id=$idcita"); // trae los datos de la cita
	$Veh=qo("select * from vehiculo where placa='$C->placa' "); // trae los datos del vehiculo
	$ultimokm=qo1("select kilometraje($Veh->id)"); // obtiene el ultimo kilometraje del veh�culo
	if($estado=='C')
	{
		if(!$C->operario_domicilio) // si aun no tiene operario asignado no deja capturar los datos de la entrega
		{
			echo "<body><script language='javascript'>alert('No ha seleccionado el operario que entrega');window.close();void(null);</script></body>";
			die();
		}
	}
	$Oficina=qo("select * from oficina where id=$C->oficina"); // trae los datos de la oficina
	html("Cita de Entrega del vehiculo $C->placa"); // pinta las cabeceras html
	if($estado=='C') // CUMPLIDA
	{
		echo "<script language='javascript'>
				function validarkm()
				{
					with(document.forma)
					{
						var Dato=Number(kmi.value);
						 ";
		if($C->dir_domicilio) // cuando es domicilio, solicita un kilometraje adicional para determinar cuanto se gasto en el domicilio
			echo "
						if(Dato==0) {alert('Debe escribir un kilometraje inicial valido mayor que el kilometraje antes del desplazamiento del domicilio.'); kmi.style.backgroundColor='ffff00';kmi.focus();continuar.style.visibility='hidden';return false;}
						if(Dato<=$ultimokm) {alert('Debe escribir un kilometraje inicial v�lido mayor o igual que el registrado antes del desplazamiento del domicilio'); kmi.style.backgroundColor='ffff00';kmi.focus();continuar.style.visibility='hidden';return false;}
						var Desplazamiento_domicilio=Dato-Number(kmd.value);
						if(Desplazamiento_domicilio>$Oficina->km_domicilio)
						{
							document.getElementById('desp_domi').innerHTML=\"<b>Desplazamiento Domcilio: <font color='red'>\"+Desplazamiento_domicilio+\"</font></b>\";
							alert('Al grabar este cumplimiento de servicio, se enviar� un correo electr�nico alertando al Director de Operaciones sobre un desplazamiento excesivo para efectos de auditor�a y control');
						}
						else
							document.getElementById('desp_domi').innerHTML=\"<b>Desplazamiento Domcilio: \"+Desplazamiento_domicilio+\"</b>\";
						if(alltrim(observaciones.value) && Dato>$ultimokm) continuar.style.visibility='visible';";
		else
			echo "
						if(Dato==0) {alert('Debe escribir un kilometraje inicial valido'); kmi.style.backgroundColor='ffff00';kmi.focus();continuar.style.visibility='hidden';return false;}
						if(Dato<$ultimokm) {alert('Debe escribir un kilometraje inicial v�lido igual que el �ltimo registrado ".($USUARIO==1?$ultimokm:'')."'); kmi.style.backgroundColor='ffff00';kmi.focus();continuar.style.visibility='hidden';return false;}
						if(Dato>$ultimokm)
						{
							var Diferencia=Dato-$ultimokm;
							if(Diferencia>0 && Diferencia<3)
							{
								document.getElementById('dspq').style.visibility='visible';
								tpq.value=Diferencia;
							}
							else
							{
								alert('Debe escribir un kilometraje inicial v�lido igual que el �ltimo registrado.  No puede ser menor ni mayor que 2 kilometros mas del �ltimo registrado en la tabla de control.');
								kmi.style.backgroundColor='ffff00';kmi.focus();continuar.style.visibility='hidden';return false;
							}
						}
						if(alltrim(observaciones.value) && Dato==$ultimokm) continuar.style.visibility='visible';";

		echo "
						kmi.style.backgroundColor='eeffee';
						kmi.readonly=true;
						observaciones.focus();
					}
				}

				function validar_kmd()
				{
					with(document.forma)
					{
						var Dato=Number(kmd.value);
						if(Dato==0) {alert('Debe escribir un kilometraje de desplazamiento de domicilio valido'); kmd.style.backgroundColor='ffff00';kmd.focus();continuar.style.visibility='hidden';return false;}
						if(Dato<0) {alert('Debe escribir un kilometraje inicial v�lido igual o mayor que el ultimo registrado'); kmd.style.backgroundColor='ffff00';kmd.focus();continuar.style.visibility='hidden';return false;}
						if(Dato>$ultimokm)
						{
							var Diferencia=Dato-$ultimokm;
							if(Diferencia>0 && Diferencia<3)
							{
								document.getElementById('dspq').style.visibility='visible';
								tpq.value=Diferencia;
							}
							else
							{
								alert('Debe escribir un kilometraje inicial v�lido igual o mayor que el �ltimo registrado. No puede ser menor ni mayor que 2 kilometros mas del �ltimo registrado en la tabla de control.');
								kmd.style.backgroundColor='ffff00';kmd.focus();continuar.style.visibility='hidden';return false;
							}
						}
						if(Dato<$ultimokm) {alert('Debe escribir un kilometraje inicial v�lido mayor o igual que el �ltimo registrado. No puede ser menor al �ltimo estado en la tabla de control.');
										            	    kmd.style.backgroundColor='ffff00';kmd.focus();continuar.style.visibility='hidden';return false;}
						if(alltrim(observaciones.value) && Dato==$ultimokm)	{continuar.style.visibility='visible';}
						kmd.readonly=true;validarkmd.style.visibility='hidden';
						kmi.focus();
					}
				}
				function validarobs()
				{
					with(document.forma)
					{
						if(!alltrim(observaciones.value)) {alert('Debe digitar alguna observaci�n');observaciones.style.backgroundColor='ffff00';observaciones.focus();continuar.style.visibility='hidden';return false;}
						observaciones.style.backgroundColor='eeffee';
						continuar.style.visibility='visible';
					}
				}
				function validar()
				{
					with(document.forma)
					{
						if(confirm('Seguro de activar este servicio?')) submit();
					}
				}
			</script>
		<body><script language='javascript'>centrar(600,400);</script>
		<h3>VEHICULO <span style='background-color:dddd00'> $C->placa </span> ESTADO: CUMPLIDA</H3>
		<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>";
	///// variables para captura de entrega con domicilio
	if($C->dir_domicilio) // solicita un kilometraje adicional para determinar cuanto es el domicilio
		echo "Kilometraje previo al desplazamiento del domicilio: <input type='text' class='numero' id='kmd' name='kmd' size='10' maxlength='10' onblur='validar_kmd();'>
				<input type='button' name='validarkmd' id='validarkmd' Value='Validar' onclick='validar_kmd();'><br /><br />
				<span id='dspq' style='visibility:hidden'>
					Transito en parqueadero <input type='text' class='numero' id='tpq' name='tpq' size='5' maxlength='5' readonly value='0'>
				</span><br /><br />Kilometraje inicial del Servicio: <input type='text' class='numero' id='kmi' name='kmi' size='10' maxlength='10' onblur='validarkm();'> <span id='desp_domi'></span><br /><br />
				";
	else // si no hay domicilio solo pide el kilometraje inicial
		echo "Kilometraje inicial del Servicio: <input type='text' class='numero' id='kmi' name='kmi' size='10' maxlength='10' onblur='validarkm();'>
					<input type='button' name='validarkmd' id='validarkmd' Value='Validar' onclick='validarkm();'><br /><br />
					<span id='dspq' style='visibility:hidden'>
					Transito en parqueadero <input type='text' class='numero' id='tpq' name='tpq' size='5' maxlength='5' readonly value='0'>
				</span>
				<br /><br />";
				
    /*
	Observaciones: <input type='text' name='observaciones' id='observaciones' size='60' onblur='validarobs();' onkeyup='validarobs();'><br /><br />
	<input type='button' id='continuar' value='CONTINUAR' style='visibility:hidden;font-size:18px;font-weight:bold;' onclick='validar();'>
	*/	
	echo "
			Observaciones: <input type='text' name='observaciones' id='observaciones' size='60' onblur='validarobs();' onkeyup='validarobs();'><br /><br />
	<input type='button' id='continuar' value='CONTINUAR' style='visibility:hidden;font-size:18px;font-weight:bold;' onclick='validar();'>
			<input type='hidden' name='Acc' value='cambia_estado'>
			<input type='hidden' name='id' value='$idcita'>
			<input type='hidden' name='estado' value='$estado'>
		</form><script language='javascript'>
		".($C->dir_domicilio?"document.forma.kmd.focus();":"document.forma.kmi.focus();")."
		</script>
		</body>";
	}
	if($estado=='S') // cumplida y no toma el servicio  CANCELACION POST ADJUDICACION
	{
		if(!$estado_siniestro) $estado_siniestro=0;
		echo "<script language='javascript'>
				function valida_estado_sin()
				{
					with(document.forma)
					{
						if(Number(estado_siniestro.value)==5) {document.getElementById('continuar').style.visibility='visible';document.getElementById('cp_sc').style.visibility='hidden';}
						if(Number(estado_siniestro.value)==1) {document.getElementById('cp_sc').style.visibility='visible';document.getElementById('continuar').style.visibility='hidden';}
					}
				}
				function valida_subcausal()
				{
					with(document.forma)
					{
						if(Number(subcausal.value)!=0)
						{
							document.getElementById('continuar').style.visibility='visible';
						}
					}
				}
				function enviar_estado()
				{
					document.forma.submit();
				}
			</script>
			<body><script language='javascript'>centrar(600,400);</script>
			<H3>ESTADO: CUMPLIDA Y NO TOMA EL SERVICIO .:. CANCELACION POST-ADJUDICACION</H3>
			<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
				Seleccione el estado en el que debe quedar el siniestro: ".menu1('estado_siniestro', "select id,nombre from estado_siniestro where id in (1,5)",$estado_siniestro,1,''," onchange='valida_estado_sin();'");
		echo capa('cp_sc',1,'Relative','');  // cuando se le da cumplida y no toma el servicio, el programa pide seleccionar una sub causal para la estadistica de la aseguradora.
			echo "<br /><br />Sub Causal: ".menu1("subcausal","select id,nombre from subcausal where causal=10",0,1,''," onchange='valida_subcausal();'");
		echo fincapa();
		echo "<br /><br /><input type='submit' id='continuar' value='CONTINUAR' style='visibility:hidden' onclick='enviar_estado();'>
				<input type='hidden' name='Acc' value='cambia_estado'>
				<input type='hidden' name='id' value='$idcita'>
				<input type='hidden' name='estado' value='$estado'>
			</form>
			</body>";
	}
	if($estado=='X') // CANCELADA POR SOLICITUD DEL ASEGURADO  // SOLO LA CAUSAL SIN SUB-CAUSAL [Requerimiento de Oscar Gomez]
	{
		echo "<script language='javascript'>
				function valida_estado_sin()
				{
					with(document.forma)
					{
						if(Number(estado_siniestro.value)==1) {document.getElementById('continuar').style.visibility='visible';}
					}
				}
				function enviar_estado()
				{
					document.forma.submit();
				}
			</script>
			<body><script language='javascript'>centrar(600,400);</script>
			<H3>ESTADO: CANCELACION POR SOLICITUD DEL ASEGURADO .:. CANCELACION POST-ADJUDICACION</H3>
			<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
				Seleccione el estado en el que debe quedar el siniestro: ".menu1('estado_siniestro', "select id,nombre from estado_siniestro where id in (1)",$estado_siniestro,1,'',"onchange='valida_estado_sin();' ");
		echo "<br /><br /><input type='submit' id='continuar' value='CONTINUAR' style='visibility:hidden' onclick='enviar_estado();'>
				<input type='hidden' name='Acc' value='cambia_estado'>
				<input type='hidden' name='id' value='$idcita'>
				<input type='hidden' name='estado' value='$estado'>
			</form>
			</body>";
		/// como el estado es X la causal es 10. cancelaci�n post-adjudicaci�n
	}
	if($estado=='Y' || $estado=='W') // REAGENDADA   O // REASIGNADA
	{
		echo "<script language='javascript'>
				function valida_estado_sin()
				{
					with(document.forma)
					{
						if(Number(estado_siniestro.value)==5) {document.getElementById('continuar').style.visibility='visible';}
					}
				}
				function enviar_estado()
				{
					document.forma.submit();
				}
			</script>
			<body><script language='javascript'>centrar(600,400);</script>
			<H3>ESTADO: ".($estado=='Y'?"REAGENDADA":"REASIGNADA")." </H3>
			<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
				Seleccione el estado en el que debe quedar el siniestro: ".menu1('estado_siniestro', "select id,nombre from estado_siniestro where id in (5)",$estado_siniestro,1,'',"onchange='valida_estado_sin();'  ");
		echo "<br /><br /><input type='submit' id='continuar' value='CONTINUAR' style='visibility:hidden' onclick='enviar_estado();'>
				<input type='hidden' name='Acc' value='cambia_estado'>
				<input type='hidden' name='id' value='$idcita'>
				<input type='hidden' name='estado' value='$estado'>
			</form>
			</body>";
	}
	if($estado=='N') // NO CUMPLIDA
	{
		if(!$estado_siniestro) $estado_siniestro=0;
		echo "<script language='javascript'>
				function valida_estado_sin()
				{
					with(document.forma)
					{
						if(Number(estado_siniestro.value)>0) {document.getElementById('continuar').style.visibility='visible';}
					}
				}
				function enviar_estado()
				{
					document.forma.submit();
				}
			</script>
			<body><script language='javascript'>centrar(600,400);</script>
			<H3>ESTADO: NO CUMPLIDA</H3>
			<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
				Seleccione el estado en el que debe quedar el siniestro: ".menu1('estado_siniestro', "select id,nombre from estado_siniestro where id in (1,5)",$estado_siniestro,1,''," onchange='valida_estado_sin();'");
		echo "<br /><br /><input type='submit' id='continuar' value='CONTINUAR' style='visibility:hidden' onclick='enviar_estado();'>
				<input type='hidden' name='Acc' value='cambia_estado'>
				<input type='hidden' name='id' value='$idcita'>
				<input type='hidden' name='estado' value='$estado'>
			</form>
			</body>";
	}
}

function cambia_estado() // cambia estado  de acuerdo al formulario de entrega
{
	global $id, $estado, $kmi, $observaciones, $estado_siniestro,$subcausal,$kmd,$tpq,$Nusuario;
	html('CAMBIO DE ESTADO');
	include('inc/link.php');
	mysql_query("flush table ubicacion",$LINK); // limpia registros temporales y obliga a descargar el cache a la tabla de ubicaciones (porque tiene mucho movimiento)
	if ($D = qom("select * from cita_servicio where id=$id",$LINK)) // trae los datos de la cita
	{
		if($D->siniestro == "1792672")
		{
			$Sincars = qo("select * from siniestro where id=$D->siniestro");
			//print_r($Sincars);
			
			$clienteSiniestro = qo("Select * from cliente where identificacion = '".$Sincars->asegurado_id."'");
			
			$rand = rand(10,100);
			$rand2 = rand(10,100);
			$documento =   $clienteSiniestro->identificacion;
			
			require($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/Requests-master/library/Requests.php");
			Requests::register_autoloader();
			
			$data_to_send = json_encode(array(
				"recipient_type" => "email",
				"shop_id" => 130307,
				"password" => "c0b325ccddeb3ac7a57bc5b77",
				"first_name" => $clienteSiniestro->nombre,
				"last_name" => $clienteSiniestro->apellido,
				"email" => $clienteSiniestro->email_e,
				"transaction_id" => md5($documento).$rand,
				"transaction_time" => date("Y-m-d h:m:s"),
				"has_products"=>0,
				"products_info"=>"",
				"telephone"=>"+57".$clienteSiniestro->celular,
				"sender_email"=>"Allianz",
				"products_other"=>"",
				"project_name"=>"",
				"days_of_deletion"=>15,
				//"meta_data": "{ \"this\":\"Sergio\", \"something\":\"Urbina\", \"blub\":\"bla\" }",
				"meta_data"=> "{ \"numeroSiniestro\":\"$Sincars->numero\", \"placaAsegurado\":\"$Sincars->placa\", \"documentoAsegurado\":\"$clienteSiniestro->identificacion\" , \"asd\":\"Gracias por visitarnos\" , \"first_name\":\"$clienteSiniestro->nombre\" , \"last_name\":\"$clienteSiniestro->apellido\" }",
				"client_id"=> $clienteSiniestro->identificacion,
				"screen_name"=> $Sincars->asegurado_nombre,
				"is_afnor"=> "false"
			));
			
			print_r($data_to_send);
			
			$request = Requests::post('https://srr.ekomi.com/add-recipient', array('content-type'=>'application/json'),$data_to_send);
			
			print_r($request);
			
			$data_to_send2 = json_encode(array(
				"recipient_type" => "sms",
				"shop_id" => 130307,
				"password" => "c0b325ccddeb3ac7a57bc5b77",
				"first_name" => $clienteSiniestro->nombre,
				"last_name" => $clienteSiniestro->apellido,
				"email" => $clienteSiniestro->email_e,
				"transaction_id" => md5($documento).$rand2,
				//"transaction_id" => md5($clienteSiniestro->id).md5("Y-m-d h:m:s"),
				"transaction_time" => date("Y-m-d h:m:s"),
				"has_products"=>0,
				"products_info"=>"",
				"telephone"=>"+57".$clienteSiniestro->celular,
				"sender_email"=>"Allianz",
				"products_other"=>"",
				"project_name"=>"",
				"days_of_deletion"=>15,
				//"meta_data": "{ \"this\":\"Sergio\", \"something\":\"Urbina\", \"blub\":\"bla\" }",
				"meta_data"=> "{ \"numeroSiniestro\":\"$Sincars->numero\", \"placaAsegurado\":\"$Sincars->placa\", \"documentoAsegurado\":\"$clienteSiniestro->identificacion\" , \"asd\":\"Gracias por visitarnos\" , \"first_name\":\"$clienteSiniestro->nombre\" , \"last_name\":\"$clienteSiniestro->apellido\" }",
				"client_id"=> $clienteSiniestro->identificacion,
				"screen_name"=> $Sincars->asegurado_nombre,
				"is_afnor"=> "false"
			));
			
			
			print_r($data_to_send2);
			
			$request2 = Requests::post('https://srr.ekomi.com/add-recipient', array('content-type'=>'application/json'),$data_to_send2);
			
			
			print_r($request2);
			echo "Realizar servicio web";
			exit;
		}
		
		$Hora = date('H:i:s');
		$Email_usuario=usuario('email'); // obtiene el correo electronico del usuario
		$Fec_entrega = aumentadias($D->fecha, $D->dias_servicio); // calcula la fecha de devoluci�n del veh�culo
		if($estado=='C') $estadod='P'; else $estadod='';  // REPROGRAMA EL ESTADO DE DEVOLUCION
		// actualiza la cita
		if(!mysql_query("update cita_servicio set estado='$estado',estadod='$estadod',hora_llegada='$Hora',hora_devol=hora,fec_devolucion='$Fec_entrega' where id=$id",$LINK))
			die(mysql_error());
		$D = qom("select * from cita_servicio where id=$id",$LINK); //recarga los datos de la cita
		$Sincars = qom("select * from siniestro where id=$D->siniestro",$LINK); // trae los datos del siniestro
		$idv = qo1m("select id from vehiculo where placa='$D->placa' ",$LINK); // trae los datos del vehiculo
		$Hoy = date('Y-m-d H:i:s');
		$Ahora = date('Y-m-d');
		$Oficina=qom("select * from oficina where id=$D->oficina",$LINK); //trae los datos de la oficina
		if ($estado == 'C') // CUMPLIDO
		{
			// //////////////////                                   *******************                 CAMBIO DE ESTADO AUTOMATICO A SERVICIO                 ************************    ///////////////////////////
			// busca la ultima ubicacion para actualizar la fecha final con la fecha inicial del nuevo estado
			if ($Ultimo = qom("select * from ubicacion where vehiculo=$idv and fecha_final > '$D->fecha' and estado=2",$LINK)) // trae la ultima ubicaci�n del vehiculo
			{
				if ($Ultimo->fecha_inicial == $D->fecha) // si la fecha inicial y final coinciden dentro del mismo dia del cambio del nuevo estado, se elimina ese estado
					{if(!mysql_query("delete from ubicacion where id=$Ultimo->id",$LINK)) die(mysql_error());}
				else
					if(!mysql_query("update ubicacion set fecha_final='$D->fecha' where id=$Ultimo->id",$LINK)) die(mysql_error()); // sino actualiza la ubicaci�n actual
			}
			// Inserta la nueva ubicaci�n.

			if($tpq) // si hay recorido en el parqueadero
			{
				if($kmd) {$km1=$kmd-$tpq;$km2=$kmd;} else {$km1=$kmi-$tpq;$km2=$kmi;} // halla las distancias entre el ultimo kilometraje y el actual
				// inserta la ubicaci�n
				if(!mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) values
				('$D->oficina','$idv','$D->fecha','$D->fecha','94','$km1','$km2','$tpq',\"Domicilio de entrega\",'$Sincars->aseguradora')",$LINK)) die(mysql_error());
				$IDU1 =mysql_insert_id($LINK);
				// inserta la bitacora de la ubicacion
				graba_bitacora('ubicacion','A',$IDU1,'Adiciona autom�ticamente en cumplimiento de entrega.',$LINK);
			}
			if($kmd) // si hay recorrido de domicilio
			{
				$Diferencia=$kmi-$kmd; // calcula la distancia recorrida para el domicilio
				// inserta el registro en ubicacion
				if(!mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) values
				('$D->oficina','$idv','$D->fecha','$D->fecha','96','$kmd','$kmi','$Diferencia','Domicilio de entrega $D->dir_domicilio','$Sincars->aseguradora')",$LINK)) die(mysql_error());
				$IDU2 =mysql_insert_id($LINK);
				// inserta la bitacora de la ubicacion
				graba_bitacora('ubicacion','A',$IDU2,'Adiciona domicilio autom�ticamente',$LINK);
			}
			if(!$Sincars->ubicacion)
			{
				// inserta la ubicaci�n
				if(!mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,obs_mantenimiento,flota) values
					('$D->oficina','$idv','$D->fecha','$Fec_entrega','1','$kmi','$kmi',\"$observaciones\",'$Sincars->aseguradora')",$LINK)) die(mysql_error());
				$IDU3 =mysql_insert_id($LINK);
				// inserta la bitacora de la ubicacion
				graba_bitacora('ubicacion','A',$IDU3,'Inserta registro',$LINK);
				// Actualiza el siniestro asigna la ubicaci�n recien ingresada y la relaciona con el siniestro
				if(!mysql_query("update siniestro set observaciones=concat(observaciones,\"\n$Nusuario [$Hoy] Asigna Servicio\"), ubicacion=$IDU3,estado=7,fecha_inicial='$D->fecha',
									fecha_final='$Fec_entrega',causal=0,subcausal=0 where id=$D->siniestro ",$LINK)) die(mysql_error());
				// Inserta la bitacora del siniestro
				graba_bitacora('siniestro','M',$D->siniestro,"Asigna Servicio",$LINK);
			}
			else
			{
				echo "<script language='javascript'>alert('EL SINIESTRO TENIA UNA UBICACION DEBE DESLIGARLO PRIMERO');</script>";
			}
			// //////////////////                                   *******************                 CAMBIO DE ESTADO AUTOMATICO A SERVICIO                 ************************    ///////////////////////////
			if($Diferencia>$Oficina->km_domicilio)
			{
				// si la distancia de domicilio supera la maxima permitida envia un correo al director operativo informando del suceso
				$Operario=qo1m("select concat(apellido,' ',nombre) from operario where id='$D->operario_domicilio' ",$LINK);
				enviar_gmail($Email_usuario /*de */ ,$Nusuario /*nombre de */ ,
				"shurtado@aoacolombia.com,Sebastian Hurtado" /*para */ ,
				""   /*Con copia*/ ,
				"Exceso desplazamiento en domicilio $D->placa $Oficina->nombre"  /*OBJETO*/,
				"<body>Ocurrio un exceso en desplazamiento en domicilio<br><br> ".
				"Placa: $D->placa <br>Oficina: $Oficina->nombre<br>Fecha y hora de la cita: $D->fecha $D->hora<br>".
				"Numero Siniestro: $Sincars->numero $Sincars->asegurado_nombre <br>".
				"Auxiliar Operativo: $Operario.<br>Kilometraje en exceso: ".coma_format($Diferencia)."</body>" /*mensaje */);
			}
			///////////////////////////////////////////////////////////////////////////////////////////////////
		}
		elseif($estado!='P')   // programada
		{
			$Hoy = date('Y-m-d H:i:s');
			if($estado=='S') // cumplida y no toma el servicio
			{
				$Obs="Cumplida y no toma el servicio. ";
				$Causal=10;
				$Tipo_seguimiento=7;
			}
			if($estado=='X') // cancelada por solicitud del usuario
			{
				$Obs="Cancelada por solicitud del asegurado. ";
				$Causal=10;
				$Tipo_seguimiento=6;
			}
			if($estado=='Y') // Reagendada
			{
				$Obs="Ctia Reagendada. ";
				$Causal=0;
				$Tipo_seguimiento=14;
			}
			if($estado=='N') // No cumplida
			{
				$Obs="Cita no cumplida. ";
				$Causal=10;
				$Tipo_seguimiento=7;
			}
			if($estado=='W') // Reasignada
			{
				$Obs="Cita reasignada. ";
				$Causal=0;
				$Tipo_seguimiento=15;
			}
			// Actualiza el siniestro
			if(!mysql_query("update siniestro set observaciones=concat(observaciones,'\n$Nusuario [$Hoy]: $Obs $D->observaciones'),estado='$estado_siniestro',
					causal='$Causal',subcausal='$subcausal' where id=$D->siniestro ",$LINK)) die(mysql_error());
			if($estado_siniestro==5) mysql_query("update call2cola2 set estado='0' where siniestro=$D->siniestro",$LINK);
			// Inserta la bitacora del siniestro
			graba_bitacora('siniestro','M',$D->siniestro,"Cancelacion post-adjudicacion.",$LINK);
			// inserta en el seguimiento del caso la actualizaci�n de que se cancela por algun motivo
			if(!mysql_query("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo)  values ('$D->siniestro','$Ahora','$Hora','$Nusuario','$Obs','$Tipo_seguimiento')",$LINK)) die(mysql_error());
		}
		echo "<body onload='carga()'><script language='javascript'>alert('El estado fue cambiado'); opener.location.reload();
			window.close(); void(null);
			</script></body>";
	}
	mysql_close($LINK);
}

function cambia_dias_servicio() // permite cambiar los dias de servicio de la cita para casos especiales
{
	global $id;
	$Dias=qo1("select dias_servicio from cita_servicio where id=$id");
	html('CAMBIO DE DIAS DE SERVICIO');
	echo "<body><form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
	<h3>Cambio de cantidad de Dias de Servicio</h3>
	  Cambiar los dias de servicio a: <select name='dias_servicio'>
	  <option value='1' ".($Dias==1?"selected":"").">1</option>
	  <option value='2' ".($Dias==2?"selected":"").">2</option>
	  <option value='3' ".($Dias==3?"selected":"").">3</option>
	  <option value='4' ".($Dias==4?"selected":"").">4</option>
	  <option value='5' ".($Dias==5?"selected":"").">5</option>
	  <option value='6' ".($Dias==6?"selected":"").">6</option>
	  <option value='7' ".($Dias==7?"selected":"").">7</option>
	  <option value='8' ".($Dias==8?"selected":"").">8</option>
	  <option value='9' ".($Dias==9?"selected":"").">9</option>
	  <option value='10' ".($Dias==10?"selected":"").">10</option>
	  <option value='11' ".($Dias==11?"selected":"").">11</option>
	  <option value='12' ".($Dias==12?"selected":"").">12</option>
	  <option value='13' ".($Dias==13?"selected":"").">13</option>
	  <option value='14' ".($Dias==14?"selected":"").">14</option>
	  <option value='15' ".($Dias==15?"selected":"").">15</option>
	  <option value='16' ".($Dias==16?"selected":"").">16</option>
	  <option value='17' ".($Dias==17?"selected":"").">17</option>
	  <option value='18' ".($Dias==18?"selected":"").">18</option>
	  <option value='19' ".($Dias==19?"selected":"").">19</option>
	  <option value='20' ".($Dias==20?"selected":"").">20</option>
	  <option value='21' ".($Dias==21?"selected":"").">21</option>
	  </select>
	  <br><br><input type='submit' value='CONTINUAR'>
	  <input type='hidden' name='id' value='$id'>
	  <input type='hidden' name='Acc' value='cambia_dias_servicio_ok'>
	  </form></body>";
}

function cambia_dias_servicio_ok() // cambia los dias de servicio de una cita para casos especiales
{
	global $id,$dias_servicio;
	q("update cita_servicio set dias_servicio='$dias_servicio' where id=$id"); // actualiza la cita
	graba_bitacora('cita_servicio','M',$id,"Cambia dias de servicio a $dias_servicio"); // graba la bitacora de la cita
	echo "<body><script language='javascript'>window.close();void(null);opener.location.reload();</script></body>";
}

function solicitar_factura() // formulario de solicitud de facturacion
{
	global $cita;
	html('SOLICITUD DE FACTURA'); // pinta cabeceras html
	$Cita=qo("select * from cita_servicio where id=$cita"); // trae los datos de la cita
	$Sin=qo("select * from siniestro where id=$Cita->siniestro"); // trae los datos del siniestro
	echo "<script language='javascript'>
				function validar_formulario()
				{
					with(document.forma)
					{
						if(!alltrim(descripcion.value)) {alert('Debe especificar una descripci�n');descripcion.style.backgroundColor='ffffdd';descripcion.focus();return false;}
						if(!alltrim(forma_pago.value)) {alert('Debe seleccionar una forma de pago');forma_pago.style.backgroundColor='ffffdd';forma_pago.focus();return false;}
						Enviar.style.visibility='hidden';
					}
					document.forma.submit();
				}
		</script>
		<body><script language='javascript'>centrar(600,400);</script><h3>Solicitud de Facturaci�n</h3>
		<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
			Concepto a facturar: ".menu1("concepto","Select id,nombre from concepto_fac where activo_solicitud=1")."
			<br><br>Cantidad: <input type='text' name='cantidad' id='cantidad' size=5 maxlength=10 value='1' class='numero'>
			<br /><br />Escriba la descripci�n de lo que se necesita facturar:<br />
			<textarea name='descripcion' rows=4 cols=80 style='font-family:arial;font-size:12px'></textarea><br />
			<br />En caso de saberlo, escriba el valor que se debe factrurar (SIN IMPUESTOS):<br />
			Valor sin  Iva:<input type='text' class='numero' name='valor'><br /><br />
			Forma de pago: ".menu3("forma_pago","R,NUEVO PAGO EN EFECTIVO O T.DEBITO;T,NUEVO PAGO CON VOUCHER;G,PAGO CONTRA LA GARANTIA;A,ASEGURADORA ASUME EL PAGO;E,TRANSFERENCIA ELECETRONICA O CHEQUE",' ',1,'',' ')."<br /><br />
			<input type='button' name='Enviar' id='Enviar' value='CONTINUAR' onclick='validar_formulario();'>
			<input type='hidden' name='Acc' value='solicitar_factura_ok'>
			<input type='hidden' name='siniestro' value='$Cita->siniestro'>
			<input type='hidden' name='cita' value='$cita'>
		</form>
		</body>";
}

function solicitar_factura_ok() // guarda la solicitud de facturacion hecha desde la tabla de citas
{
	global $siniestro,$concepto,$cita,$descripcion,$valor,$Nusuario,$Hoyl,$forma_pago,$cantidad;
	// inserta el registro en la tabla de solicitudes
	q("insert into solicitud_factura (siniestro,cita,concepto,solicitado_por,fecha_solicitud,descripcion,valor,forma_pago,cantidad)
			values ('$siniestro','$cita','$concepto','$Nusuario','$Hoyl',\"$descripcion\",'$valor','$forma_pago','$cantidad')");
	echo "<body><script language='javascript'>alert('Solicitud grabada satisfactoriamente');window.close();void(null);</script>";
}

function generar_xml() // programa que genera un xml para graficos. no se est� usando en el momento
{
	global $Hoy;
	header("Content-type: text/xml"); // pinta cabeceras xml
	// trae las citas del dia
	$Citas=q("select c.id,c.placa,s.numero,e.nombre as nest from cita_servicio c,siniestro s,sin_autor a,
	estado_citas e where c.siniestro=s.id and c.estado=e.codigo and s.id=a.siniestro and a.estado='A' and c.fecha='$Hoy' ");
	echo "<citas>";
	// pinta la informaci�n de las citas con su estado
	while($C=mysql_fetch_object($Citas))
	{

		echo "
				<cita>
					<id_cita>$C->id</id_cita>
					<placa>$C->placa</placa>
					<id_siniestro>$C->numero</id_siniestro>
					<estado>$C->nest</estado>
					</cita>
					";
	}
	// funci�n incompleta.
}

function asigna_operario_entrega() // actualiza el registro de la cita con el operario que selecciona el funcionario
{
	global $operario,$cita,$Hoyl;
	q("update cita_servicio set operario_domicilio=$operario,arribo='$Hoyl' where id=$cita"); // actualiza la cita
	graba_bitacora('cita_servicio','M',$cita,'Asigna operario para entrega.'); // graba la bitacora
	echo "<body><script language='javascript'>alert('Operario Asignado Satisfactoriamente');</script>";
}

function insertar_domiciliod() // formulario para insertar un domicilio de devoluci�n
{
	global $id;
	html('Crear Domicilio'); // pinta las cabeceras html
	echo "<script language='javascript'>
		</script>
		<body><script language='javascript'>centrar(500,300);</script>
		<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
			<h3>Creaci�n de Domicilio de Devoluci�n</h3>
			<table align='center'>
			<tr><td align='ritght'>Direcci�n Domicilio</td><td><input type='text' name='dir_domiciliod' size='70' maxlength='200'></td></tr>
			<tr><td align='ritght'>Tel�fono Domicilio</td><td><input type='text' name='tel_domiciliod' size='50' maxlength='50'></td></tr>
			<tr><td colspan='2' align='center'><input type='submit' value='Continuar'></td></tr></table>
			<input type='hidden' name='Acc' value='insertar_domiciliod_ok'>
			<input type='hidden' name='id' value='$id'>
		</form>
		</body>";
}

function insertar_domiciliod_ok() // graba los datos de un domicilio de devoluci�n
{
	global $id,$dir_domiciliod,$tel_domiciliod;
	q("update cita_servicio set dir_domiciliod='$dir_domiciliod',tel_domiciliod='$tel_domiciliod' where id=$id "); // actualiza la cita
	graba_bitacora('cita_servicio','M',$id,'Asigna Domicilio Devoluci�n'); // graba la bitacora
	echo "<body><script language='javascript'>window.close();void(null);opener.location.reload();</script></body>";
}

function asigna_operario_devolucion() // asigna el operario de devolucion segun lo haya seleccionado el funcionario
{
	global $operario,$cita,$Hoyl;
	q("update cita_servicio set operario_domiciliod=$operario where id=$cita"); // actualiza la cita
	graba_bitacora('cita_servicio','M',$cita,'Asigna operario para devoluci�n.'); // graba la bitacora de la cita
	echo "<body><script language='javascript'>alert('Operario Asignado Satisfactoriamente');</script>";
}

function estado_operarios() // muestra diariamente, semanalmente y mensualmente la cantidad de entregas y solicitudes por oficina y por operario.
{
	global $Oficina,$Dia,$Modo,$Hoy;
	if($Oficina) $Ofi=qo("select * from oficina where id=$Oficina");  // trae los datos de la oficina
	else $Ofi->nombre='TODAS'; // o si no son todas las oficinas
	html();
	echo "<script language='javascript' src='inc/chart/JSClass/FusionCharts.js'></script>";
	echo "<script language='javascript'>
			function cerrar() {parent.ocultar_estado_operarios();}
			function cambia_modo(dato) {window.open('zcitas.php?Acc=estado_operarios&Oficina=$Oficina&Dia=$Dia&Modo='+dato,'_self');}
		</script>
		<body bgcolor='ddddff'>
		<h4>Oficina: $Ofi->nombre $Modo ";
	if($Modo=='diario')
	{ // muestra el dia que va a pintar y los botones de cambio de modo a semala y mensual
		echo " $Dia <a onclick=\"cambia_modo('semanal')\" style='cursor:pointer'>Semanal</a> <a onclick=\"cambia_modo('mensual')\" style='cursor:pointer'>Mensual</a>";
	}
	if($Modo=='semanal')
	{ // muestra los limites de la semana y los botones de cambio de modo a diario y mensual
		$Diai=primer_dia_de_semana($Dia);$Diaf=date('Y-m-d',strtotime(aumentadias($Diai,6)));
		echo " $Diai - $Diaf <a onclick=\"cambia_modo('diario')\" style='cursor:pointer'>Diario</a>  <a onclick=\"cambia_modo('mensual')\" style='cursor:pointer'>Mensual</a>";
	}
	if($Modo=='mensual')
	{  // muestra los limites del mes y los botones de cambio de modo a diario y mensual
		$Diai=primer_dia_de_mes($Dia);$Diaf=date('Y-m-',strtotime($Dia)).ultimo_dia_de_mes(date('Y',strtotime($Dia)),date('m',strtotime($Dia)));
		echo " $Diai - $Diaf <a onclick=\"cambia_modo('diario')\" style='cursor:pointer'>Diario</a> <a onclick=\"cambia_modo('semanal')\" style='cursor:pointer'>Semanal</a>";
	}
	echo "</h4>";
	// busca las entregas del rango de tiempo
	$Entregas=q("select concat(op.nombre,' ',op.apellido) as noperario, count(cs.id) as cantidad
							FROM operario op, cita_servicio cs
							WHERE op.id=cs.operario_domicilio and op.inactivo=0 ".($Oficina?" and op.oficina=$Oficina ":"")." and ".
							($Modo=='diario'?"cs.fecha='$Dia' ":"").($Modo=='semanal' || $Modo=='mensual'?"cs.fecha between '$Diai' and '$Diaf' ":"")."
							GROUP BY noperario ORDER BY noperario");
	// busca las devoluciones del rango de tiempos
	$Devoluciones=q("select concat(op.nombre,' ',op.apellido) as noperario, count(cs.id) as cantidad
							FROM operario op, cita_servicio cs
							WHERE op.id=cs.operario_domiciliod and op.inactivo=0 ".($Oficina?" and op.oficina=$Oficina ":"")." and ".
							($Modo=='diario'?"cs.fec_devolucion='$Dia' ":"").($Modo=='semanal' || $Modo=='mensual'?"cs.fec_devolucion between '$Diai' and '$Diaf' ":"")."
							GROUP BY noperario ORDER BY noperario");
	// inicia la creaci�n de los xml para mostrar las gr�ficas
	$xml1="<chart caption='ENTREGAS' xAxisName='Operarios' yAxisName='Operaciones' showValues='1' decimals='0' formatNumberScale='0' chartRightMargin='30'  logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' showLabels='1' >";
	$xml2="<chart caption='DEVOLUCIONES' xAxisName='Operarios' yAxisName='Operaciones' showValues='1' decimals='0' formatNumberScale='0' chartRightMargin='30'  logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' showLabels='1' >";
	$xml3="<chart caption='TOTAL' xAxisName='Operarios' yAxisName='Operaciones' showValues='1' decimals='0' formatNumberScale='0' chartRightMargin='30'  logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' showLabels='1' >";
	$Total=array();
	if($Entregas) // operario por operario va acumulando las entregas
	while($Op=mysql_fetch_object($Entregas))
	{
		$xml1.="<set label='$Op->noperario' value='$Op->cantidad'/>";$Total[$Op->noperario]=$Op->cantidad;
	}
	$xml1.="</chart>";
	if($Devoluciones) // operario por operario va acumulando las devoluciones
	while($Op=mysql_fetch_object($Devoluciones))
	{
		$xml2.="<set label='$Op->noperario' value='$Op->cantidad'/>";$Total[$Op->noperario]+=$Op->cantidad;
	}
	$xml2.="</chart>"; // el total de las operaciones se va acumulando en otro xml
	foreach($Total as $Operario => $Cantidad)
	{
		$xml3.="<set label='$Operario' value='$Cantidad'/>";
	}
	$xml3.="</chart>";
	// pinta las tres graficas
	echo "<table align='center'><tr ><td >".renderChart("inc/chart/Charts/Bar2D.swf","",$xml1,"entregas",350,200,false,false)."</td>
				<td >".renderChart("inc/chart/Charts/Bar2D.swf","",$xml2,"devoluciones",350,200,false,false)."</td></tr>
				<tr ><td colspan=2>".renderChart("inc/chart/Charts/Bar2D.swf","",$xml3,"total",704,200,false,false)."</td></tr></table>";
	echo "<br /><br />";
	// busca el estado actual de las citas de todas las ciudades y oficinas - Entregas
	$Citas_Ciudad=q("select distinct c.oficina,o.nombre as nofi from cita_servicio c, oficina o where c.fecha='$Dia' and c.oficina=o.id order by nofi ");
	echo "<table width='100%'><tr><th colspan=10>ESTADO NACIONAL DE CITAS - ENTREGAS</th></tr><tr>";
	while ($Cc=mysql_fetch_object($Citas_Ciudad))
	{
		// oficina por oficina calcula las citas por estados
		$ECitas=q("select  count(c.id) as cantidad,e.nombre as nestado,e.color_co  from estado_citas e,cita_servicio c where c.estado=e.codigo
					and c.fecha='$Dia'  and c.oficina=$Cc->oficina group by e.id order by e.id");
		echo "<td valign='top'><table border cellspacing='0'><tr>
			<th>$Cc->nofi</th>
			<th>Cantidad</th>
			</tr>";
		while($Ec =mysql_fetch_object($ECitas )) // pinta el resultado de los estados por citas.
		{
			echo "<tr bgcolor='$Ec->color_co'>
			<td>$Ec->nestado</td>
			<td align='center'>$Ec->cantidad</td>
			</tr>";
		}
		echo "</table></td>";
	}
	echo "</tr></table>";
	// busca el estado actual de las citas de todas las ciudades y oficinas - Devoluciones
	$Citas_Ciudad=q("select distinct c.oficina,o.nombre as nofi from cita_servicio c,oficina o where c.oficina=o.id and c.fec_devolucion='$Dia' and c.estado='C' order by nofi ");
	echo "<table width='100%'><tr><th colspan=10>ESTADO NACIONAL DE CITAS - DEVOLUCIONES</th></tr><tr>";
	while ($Cc=mysql_fetch_object($Citas_Ciudad))
	{
		$ECitas=q("select  count(id) as cantidad, case estadod when 'P' then 'PROGRAMADO' when 'C' then 'CUMPLIDO' end as nestado,
		case estadod when 'P' then 'ffffff' when 'C' then 'ddffdd' end as color_co  from cita_servicio where
					fec_devolucion='$Dia'  and oficina=$Cc->oficina and estado='C' group by estadod order by estadod");
		echo "<td valign='top'><table border cellspacing='0'><tr>
			<th>$Cc->nofi</th>
			<th>Cantidad</th>
			</tr>";
		// oficina por oficina muestra el acumulado por estado de las citas
		while($Ec =mysql_fetch_object($ECitas ))
		{
			echo "<tr bgcolor='$Ec->color_co'>
			<td>$Ec->nestado</td>
			<td align='center'>$Ec->cantidad</td>
			</tr>";
		}
		echo "</table></td>";
	}
	echo "</tr></table>";
	echo "<br /><br />
		<center><input type='button' value=' CERRAR ESTA VENTANA ' onclick='cerrar()' style='font-size:14;font-weight:bold'></center>
		</body>";
}

function formulario_devolucion() // formulario para capturar los datos de una devolucion
{
	global $idcita,$USUARIO;
	//echo "select * from cita_servicio where id=$idcita"."<br>";
	$C=qo("select * from cita_servicio where id=$idcita");  // se halla la informaci�n de la cita
	
	if(!$C->operario_domiciliod)
	{ // verifica que tenga asignado ya un operario en la devoluci�n del vehiculo
		echo "<body><script language='javascript'>alert('No ha seleccionado el operario que recibe');window.close();void(null);</script></body>";
		die();
	}
	$Oficina=qo("select * from oficina where id=$C->oficina"); // trae los datos de la oficina
	$Sin=qo("select aseguradora from siniestro where id=$C->siniestro"); // trae los datos del siniestro
	$Aseg=qo("select limite_kilometraje from aseguradora where id=$Sin->aseguradora"); // trae los datos de la aseguradora
	
	
	$UB=qo("select u.* from ubicacion u,siniestro s where u.id=s.ubicacion and s.id=$C->siniestro"); // se halla la ubicacion
	
	html('FORMULARIO DE DEVOLUCION'); // pinta las cabeceras html
	
	echo "<script language='javascript'>
			
			
			let currentProcess = false;
			
			function valida_km_final()
			{
				var dato=document.forma.kmf.value;
				if(!Number(dato)) {
					if(!currentProcess)
					{	
						currentProcess = true;
						alert('Debe escribir un valor numerico sin comas ni puntos');
						document.forma.kmf.style.backgroundColor='ffff55';
						document.forma.kmf.focus();
						return false;
					}
				}
			}

			function valida_km_final2()
			{
				
				var dato=document.forma.kmf.value;
				if(!Number(dato)) {
					if(!currentProcess)
					{	
						currentProcess = true;
						alert('Debe escribir un valor numerico sin comas ni puntos');
						document.forma.kmf.style.backgroundColor='ffff55';
						document.forma.kmf.focus();
						return false;
					}	
				}
				if(Number(dato)<=0)
				{ 
					if(!currentProcess)
					{	
						currentProcess = true;
						alert('Debe escribir un odometro valido');
						document.forma.kmf.style.backgroundColor='ffff55';
						document.forma.kmf.focus();
						return false;
					}
				}
				if(Number(dato)<=$UB->odometro_inicial)
				{ 	
					if(!currentProcess)
					{	currentProcess = true; 
						document.forma.kmf.style.backgroundColor='ffff55';
						document.forma.kmf.focus();
						console.log(document.forma.kmf);
						alert('Debe escribir un odometro mayor que el od�metro inicial');					
					    return false;
					}
					
				}
				var consumo=Number(dato)-$UB->odometro_inicial;
				var lkm = $Aseg->limite_kilometraje;
				if(consumo > lkm){
			       if(lkm !=0){
					   var limit = 1500;
					   if(lkm >= limit){
					document.getElementById('consumo').innerHTML=\"<b>Consumo: <font color='red'>\"+consumo+\"</font></b>\";
					alert('Se informar� via correo electr�nico al Director de Operaciones sobre este exceso en el kilometraje de este servicio');   
					   }
				   } 
			    }else
					document.getElementById('consumo').innerHTML='<b>Consumo: '+consumo+'</b>';
				document.forma.kmf.readonly=true;
				document.forma.kmf.style.backgroundColor='eeffee'; ";
				
	if($C->dir_domiciliod){
	  echo "document.forma.kmd.style.visibility='visible';document.forma.kmd.focus();";
	}
		
	else{
		echo "document.forma.obs.style.visibility='visible';document.forma.obs.focus();";
	}
		
	
	echo "
			}

			function valida_obs()
			{
				var dato=document.forma.obs.value;
				if(!alltrim(dato)) {
					if(!currentProcess)
					{	
						currentProcess = true; 
						alert('Debe escribir la observaci�n');
						document.forma.obs.style.backgroundColor='ffff55';
						document.forma.obs.focus();
						return false;
					}	
				}
				document.forma.obs.style.backgroundColor='eeffee';
				document.forma.Nuevo_estado.style.visibility='visible';
			}
			function valida_nuevo_estado()
			{
				var dato=document.forma.Nuevo_estado.value;
				if(dato==8)
				{
					document.forma.ords.readOnly=true;
					modal('zalistamiento.php?Acc=registrar_desde_citas&P=$C->placa&F=forma',0,0,600,600,'r_alistamiento');
				}
				if(dato==5)
				{
					document.forma.ords.readOnly=false;
					document.forma.Siniestro_propio.checked=true;
				}
				if(dato==92)
				{
					document.forma.ords.readOnly=false;
				}
				document.forma.ords.style.visibility='visible';
			}
			function prevalida_ords()
			{
				if(document.forma.Nuevo_estado.value==0) {
					if(!currentProcess)
					{	
						currentProcess = true;					
						alert('Debe primero seleccionar el nuevo estado en que quedar� el veh�culo');
						document.forma.Nuevo_estado.style.backgroundColor='ffffdd';
						document.forma.Nuevo_estado.focus();
						return false;
					}	
				}
			}
			function valida_ords()
			{
				var dato=document.forma.ords.value;
				if(!alltrim(dato)) {alert('Debe digitar la observaci�n para el nuevo estado');document.forma.ords.style.backgroundColor='ffffdd';document.forma.ords.focus();return false;}
				document.forma.Siniestro_propio.style.visibility='visible';
				document.forma.gr.style.visibility='visible';
			}
			function cerrar()
			{window.close();void(null);opener.location.reload();}

			function valida_km_final3()
			{
				with(document.forma)
				{
					if(!Number(kmd.value)) {alert('Debe escribir un kilometraje v�lido mayor o igual que el �ltimo registrado, sin comas ni puntos');kmd.style.backgroundColor='ffffdd';kmd.focus();return false;}
					if(Number(kmd.value)<Number(kmf.value)) {alert('Debe escribir un kilometraje v�lido mayor o igual que el �ltimo registrtado.');kmd.style.backgroundColor='ffffdd';kmd.focus();return false;}
					var Desplazamiento_domicilio=Number(kmd.value)-Number(kmf.value);
					if(Desplazamiento_domicilio>$Oficina->km_domicilio)
					{
						document.getElementById('consumod').innerHTML=\"<b>Desplazamiento Domicilio: <font color='red'>\"+Desplazamiento_domicilio+\"</font><b>\";
						alert('Al grabar este cumplimiento de devoluci�n, se enviar� un correo electr�nico alertando al Director de Operaciones sobre un desplazamiento excesivo para efectos de auditor�a y control');
					}
					else
						document.getElementById('consumod').innerHTML=\"<b>Desplazamiento Domcilio: \"+Desplazamiento_domicilio+\"</b>\";
					kmd.style.backgroundColor='eeffee';
					obs.style.visibility='visible';
					obs.focus();
				}
			}

			function enviar_frm()
			{
				document.forma.submit();
			}
		</script>
		<body >
			<form action='zcitas.php' method='post' target='Oculto_devolucion' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='cambia_estadod'>
				Kilometraje Final Servicio: 
		<input type='text' class='numero' id='kmf' name='kmf' size='10' maxlength='10' onkeyup='valida_km_final()' onblur='valida_km_final2()'> 
		<span id='consumo'></span>  <br /><br />";
		
		
	if($C->dir_domiciliod)
	{
		echo "Kilometraje al retorno del domicilio: <input type='text' class='numero' id='kmd' name='kmd' size='10' maxlength='10' onblur='valida_km_final3();'>
					<input type='button' onclick='validar_km_final3()' value=''><span id='consumod'></span><br /><br />";
	}
	echo "
				Observaciones:<br />
				<textarea name='obs' style='font-size:11px;font-family:arial;visibility:".($C->dir_domiciliod?"hidden":"visible")."' cols=60 rows=3 onkeyup='valida_obs();'></textarea><br />
				Estado en que quedar� el Veh�culo:".
				menu1('Nuevo_estado',"select id,nombre from estado_vehiculo where id in (5,8,92)",0,1,'visibility:hidden;'," onchange='valida_nuevo_estado();' ")."<br />
				Descripci�n:<br />
				<textarea name='ords' id='ords' cols=60 rows=3 style='font-size:11px;font-family:arial;visibility:hidden;' onfocus='prevalida_ords()' onblur='valida_ords()'></textarea><br />
				 <b>Siniestro Asegurado</b> <input type='checkbox' name='Siniestro_propio' style='visibility:hidden'><br />
				<input type='hidden' name='id' id='id' value='$idcita'>
				<input type='button' id='gr' name='gr' value='Grabar Devoluci�n' style='visibility:hidden;width:300px;height:60px;' onclick='enviar_frm();'>
			</form><script language='javascript'>document.forma.kmf.focus();</script>
			<iframe name='Oculto_devolucion' id='Oculto_devolucion' height='1' width='1' style='visibility:hidden'></iframe>
		</body>";
		
		
}

function cambia_estadod() // cambia el estado de devoluci�n de acuerdo a los datos capturados en el formulario de devoluci�n
{
	global $id, $estadod, $kmf, $obs, $ords,$Nuevo_estado,$Siniestro_propio,$kmd,$Nusuario;
	$Siniestro_propio=sino($Siniestro_propio);
	$Email_usuario=usuario('email'); // obtiene el email del usuario
	include('inc/link.php'); // conexion a la base de datos
	if ($D = qom("select * from cita_servicio where id=$id",$LINK)) // trae los datos de la cita
	{
		
		if($D->siniestro == "1792672")
		{
			$Sincars = qo("select * from siniestro where id=$D->siniestro");
			//print_r($Sincars);
			
			$clienteSiniestro = qo("Select * from cliente where identificacion = '".$Sincars->asegurado_id."'");
			
			$rand = rand(10,100);
			$rand2 = rand(10,100);
			$documento =   $clienteSiniestro->identificacion;
			
			require($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/Requests-master/library/Requests.php");
			Requests::register_autoloader();
			
			$data_to_send = json_encode(array(
				"recipient_type" => "email",
				"shop_id" => 130307,
				"password" => "c0b325ccddeb3ac7a57bc5b77",
				"first_name" => $clienteSiniestro->nombre,
				"last_name" => $clienteSiniestro->apellido,
				"email" => $clienteSiniestro->email_e,
				"transaction_id" => md5($documento).$rand,
				"transaction_time" => date("Y-m-d h:m:s"),
				"has_products"=>0,
				"products_info"=>"",
				"telephone"=>"+57".$clienteSiniestro->celular,
				"sender_email"=>"Allianz",
				"products_other"=>"",
				"project_name"=>"",
				"days_of_deletion"=>15,
				//"meta_data": "{ \"this\":\"Sergio\", \"something\":\"Urbina\", \"blub\":\"bla\" }",
				"meta_data"=> "{ \"numeroSiniestro\":\"$Sincars->numero\", \"placaAsegurado\":\"$Sincars->placa\", \"documentoAsegurado\":\"$clienteSiniestro->identificacion\" , \"asd\":\"Gracias por visitarnos\" , \"first_name\":\"$clienteSiniestro->nombre\" , \"last_name\":\"$clienteSiniestro->apellido\" }",
				"client_id"=> $clienteSiniestro->identificacion,
				"screen_name"=> $Sincars->asegurado_nombre,
				"is_afnor"=> "false"
			));
			
			print_r($data_to_send);
			
			$request = Requests::post('https://srr.ekomi.com/add-recipient', array('content-type'=>'application/json'),$data_to_send);
			
			print_r($request);
			
			$data_to_send2 = json_encode(array(
				"recipient_type" => "sms",
				"shop_id" => 130307,
				"password" => "c0b325ccddeb3ac7a57bc5b77",
				"first_name" => $clienteSiniestro->nombre,
				"last_name" => $clienteSiniestro->apellido,
				"email" => $clienteSiniestro->email_e,
				"transaction_id" => md5($documento).$rand2,
				//"transaction_id" => md5($clienteSiniestro->id).md5("Y-m-d h:m:s"),
				"transaction_time" => date("Y-m-d h:m:s"),
				"has_products"=>0,
				"products_info"=>"",
				"telephone"=>"+57".$clienteSiniestro->celular,
				"sender_email"=>"Allianz",
				"products_other"=>"",
				"project_name"=>"",
				"days_of_deletion"=>15,
				//"meta_data": "{ \"this\":\"Sergio\", \"something\":\"Urbina\", \"blub\":\"bla\" }",
				"meta_data"=> "{ \"numeroSiniestro\":\"$Sincars->numero\", \"placaAsegurado\":\"$Sincars->placa\", \"documentoAsegurado\":\"$clienteSiniestro->identificacion\" , \"asd\":\"Gracias por visitarnos\" , \"first_name\":\"$clienteSiniestro->nombre\" , \"last_name\":\"$clienteSiniestro->apellido\" }",
				"client_id"=> $clienteSiniestro->identificacion,
				"screen_name"=> $Sincars->asegurado_nombre,
				"is_afnor"=> "false"
			));
			
			
			print_r($data_to_send2);
			
			$request2 = Requests::post('https://srr.ekomi.com/add-recipient', array('content-type'=>'application/json'),$data_to_send2);
			
			
			print_r($request2);
			echo "Realizar servicio web";
			exit;
		}
		
		
		$Hora = date('H:i:s');
		$Fecha = date('Y-m-d');
		$Dias_servicio=dias($Fecha,$D->fecha); // recalcula los dias reales de servicio
		$Oficina=qom("select * from oficina where id=$D->oficina",$LINK); // trae los datos de la oficina
		// actualiza la cita del servicio en el estado,  fecha y hora de devoluci�n
		mysql_query("update cita_servicio set estadod='C',hora_devol_real='$Hora',fec_devolucion='$Fecha',obs_devolucion=concat(obs_devolucion,\"$obs - $ords\"),dias_servicio=$Dias_servicio where id=$id",$LINK);
		$Sincars = qom("select * from siniestro where id=$D->siniestro",$LINK); // trae los datos del siniestro		
		$Aseg=qom("select * from aseguradora where id=$Sincars->aseguradora",$LINK); // trae los datos de la aseguradora
		$idv = qo1m("select id from vehiculo where placa='$D->placa'",$LINK); // obtiene el id del vehiculo
		mysql_query("update siniestro set fecha_final='$Fecha', estado=8,obsconclusion=\"$obs\",siniestro_propio='$Siniestro_propio' where id=$D->siniestro",$LINK); // actualiza el siniestro en la fecha final y el estado
		graba_bitacora('siniestro','M',$D->siniestro,'Fecha final,estado,obsconclusion,siniestro_propio',$LINK); // graba la bitacora del siniestro
		$Ubicacion=qom("select * from ubicacion where id=$Sincars->ubicacion",$LINK); // trae tdos los datos de la ubicacion
		$Consumo=$kmf-$Ubicacion->odometro_inicial;
		mysql_query("update ubicacion set fecha_final='$Fecha', odometro_final='$kmf', odometro_diferencia=odometro_final-odometro_inicial,obs_mantenimiento=\"$obs\",
		observaciones=\"$ords\",estado=7 where id=$Sincars->ubicacion",$LINK); // actualiza la ubicaci�n
		graba_bitacora('ubicacion','M',$Ubicacion->id,'Concluye el servicio',$LINK); // graba la bitacora de la uticaci�n
		// si el consumo sobrepasa el limite de kilometraje de la aseguradora, envia un correo de advertencia
		$lkm = $Aseg->limite_kilometraje;
		if($Consumo > $lkm){
			if($lkm != 0){
				$limit = 1500;
				if($lkm >= $limit){
				enviar_gmail($Email_usuario /*de */ ,$Nusuario /*nombre de */ ,
			"shurtado@aoacolombia.com,Sebastian Hurtado;sergio.castillo@aoacolombia.com,Sergio Castillo" /*para */ ,
			""   /*Con copia*/ ,
			"Exceso consumo de kilometraje en servicio $D->placa $Oficina->nombre"  /*OBJETO*/,
			"<body>Ocurrio un exceso en consumo de kilometraje en el servicio<br><br> ".
			"Placa: $D->placa <br>Oficina: $Oficina->nombre<br>Fecha y hora de la cita: $D->fecha $D->hora<br>".
			"Numero Siniestro: $Sincars->numero $Sincars->asegurado_nombre <br>".
			"Kilometraje maximo permitido:  $Aseg->limite_kilometraje.  Kilometraje consumido: ".coma_format($Consumo)." </body>" /*mensaje */);	
				}
			}
			
		}
		/////  ACTUALIZA LAS UBICACIONES POSTERIORES A ESTE CIERRE
		mysql_query("update ubicacion set odometro_inicial='$kmf', odometro_final='$kmf', odometro_diferencia=0 where vehiculo='$idv' and fecha_inicial>='$Fecha' ",$LINK);
		if($kmd)   /// si hubo consumo de kilometraje por domicilio
		{
			$Estado_domicilio=96;
			$Diferencia=$kmd-$kmf;
			// inserta una ubiaci�n de domicilio despu�s de finalizacion del servicio
			mysql_query("insert into ubicacion (oficina,vehiculo,estado,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones,flota) values
				('$D->oficina','$idv','$Estado_domicilio','$Fecha','$Fecha','$kmf','$kmd','$Diferencia','Domicilio de devoluci�n $D->dir_domiciliod',$D->flota) ",$LINK);
			if($Diferencia>$Oficina->km_domicilio) // si sobrepasa el kilometraje limite de domicilio por oficina, envia un correo de alerta.
			{
				$Operario=qo1m("select concat(apellido,' ',nombre) from operario where id='$D->operario_domiciliod' ",$LINK);
				enviar_gmail($Email_usuario /*de */ ,$Nusuario /*nombre de */ ,
				"shurtado@aoacolombia.com,SEBASTIAN HURTADO;sergio.castillo@aoacolombia.com,Sergio Castillo" /*para */ ,
				""   /*Con copia*/ ,
				"Exceso desplazamiento en domicilio $D->placa $Oficina->nombre"  /*OBJETO*/,
				"<body>Ocurrio un exceso en desplazamiento en domicilio<br><br> ".
				"Placa: $D->placa <br>Oficina: $Oficina->nombre<br>Fecha y hora de la cita: $D->fecha $D->hora<br>".
				"Numero Siniestro: $Sincars->numero $Sincars->asegurado_nombre <br>".
				"Auxiliar Operativo: $Operario.<br>Kilometraje en exceso: ".coma_format($Diferencia)."</body>" /*mensaje */);
			}
		}
		if($ords) // //   si hay orden de servicio significa que el vehiculo pasa a fuera de servicio o mantenimiento programado o alistamiento  por arreglos en taller.
		{
			if($kmd) $kmf=$kmd;
			if($Nuevo_estado==5 /*fuera de servicio*/)
			{
				// inserta una ubicacion de fuera de servicio
				mysql_query("insert into ubicacion (oficina,vehiculo,estado,fecha_inicial,fecha_final,odometro_inicial,odometro_final,observaciones,flota,siniestro_propio) values
				('$D->oficina','$idv','$Nuevo_estado','$Fecha','$Fecha','$kmf','$kmf','$ords',$D->flota,'$Siniestro_propio')",$LINK);
				$UB1 =mysql_insert_id($LINK);
				if($Siniestro_propio) mysql_query("update siniestro set siniestro_propio=1 where id=$D->siniestro",$LINK); // si el siniestro es propio marca en la tabla de siniestros
			}
			else
			{ // inserta un nuevo estado ya sea de alistamiento o de mantenimiento preventivo
				$UB1 = q("insert into ubicacion (oficina,vehiculo,estado,fecha_inicial,fecha_final,odometro_inicial,odometro_final,observaciones,flota) values
				('$D->oficina','$idv','$Nuevo_estado','$Fecha','$Fecha','$kmf','$kmf','$ords',$D->flota)",$LINK);
				$UB1 =mysql_insert_id($LINK);
			}
			graba_bitacora('ubicacion','A',$UB1,'',$LINK); // graba la bitacora de la ubicaci�n
		}
		mysql_close($LINK); // cierra la conexi�n con la base de datos.
		echo "<body><script language='javascript'>alert('El estado fue cambiado');parent.cerrar();</script></body>";
	}
}


function desencripta_data($id) // desencripta datos de una garant�a para mostrar en el acta de entrega/devolucion.
{
	$D=qo("select * from sin_autor where id=$id");
	require_once('inc/Crypt.php');
	$C = new Crypt();
	$C->Mode = Crypt::MODE_HEX;
	$C->Key  = '!'.$D->id.'+';
	$Datos=$C->decrypt($D->data);
	$DR=explode('|',$Datos);
	$R['identificacion']=$DR[0];
	$R['numero']=$DR[1];
	$R['banco']=$DR[2];
	$R['vencimiento_mes']=$DR[3];
	$R['vencimiento_ano']=$DR[4];
	$R['num_autorizacion']=$DR[5];
	$R['funcionario']=$DR[6];
	$R['codigo_seguridad']=$DR[7];
	return $R;
}

?>