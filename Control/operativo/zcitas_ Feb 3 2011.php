<?php

/**
 * CONTROL OPERATIVO - ENTREGA Y DEVOLUCION VEHICULOS
 *
 * @version $Id$
 * @copyright 2010
 */

include('inc/funciones_.php');
include('inc/chart/Includes/FusionCharts.php');
sesion();
if(!$Modo) $Modo=1;
$USUARIO = $_SESSION['User'];
$Nusuario = $_SESSION['Nombre'];
$Nick = $_SESSION['Nick'];
$Hoyl = date('Y-m-d H:i:s');
$Hoy = date('Y-m-d');
$Hora = date('H:i:s');
$ESTADOS_CITA_DEVOLUCION = "P,PROGRAMADA;C,CUMPLIDA";
if($USUARIO==5) $PRE_AUTORIZA=qo1("select gen_pre_autoriza from usuario_autorizacion where id=".$_SESSION['Id_alterno']); else $PRE_AUTORIZA=0;
if($USUARIO==10) $PRE_AUTORIZA=qo1("select gen_pre_autoriza from usuario_oficina where id=".$_SESSION['Id_alterno']);

if (!empty($Acc) && function_exists($Acc)){	eval($Acc . '();');	die();}

citas_inicial();

function citas_inicial()
{
	global $Hoy, $Dia,  $ESTADOS_CITA_DEVOLUCION, $USUARIO, $Hora,$OFI,$Modo,$ASEG,$PRE_AUTORIZA;
	if(!$OFI && !$Dia && $USUARIO!=10 && $USUARIO!=33) $OFI=1;
	if (!$Dia) $Dia = $Hoy;
	$TUcita = tu('cita_servicio', 'id');
	html('CONTROL OPERATIVO - CITAS DEL DIA');
	echo "<script language='javascript'>

			var Parar_recarga=false;

			function cambio_fecha()
			{
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

				if(!Number(forma.kmf.value)) { alert('Debe escribir el kilometraje final válido'); forma.kmf.style.backgroundColor='ffff00';forma.kmf.value='';forma.kmf.focus(); return false;}
				if(!alltrim(forma.obs.value)) { alert('Debe escribir las observaciones en la conclusión del servicio'); forma.obs.style.backgroundColor='ffff00';forma.obs.value='';forma.obs.focus(); return false;}
				if(!forma.Nuevo_estado.value) {alert('Debe seleccionar el nuevo estado en el que va a quedar el vehículo.'); forma.Nuevo_estado.style.backgroundColor='ffffdd';return false; }
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
			{ if(confirm('".$_SESSION['Nombre'].": Desea marcar el momento de la Entrega de este Vehículo?')) window.open('zcitas.php?Acc=marcar_entregado&id='+id,'Citas_oculto'); }

			function actualiza_info(id) { modal('zautorizaciones.php?Acc=actualizar_info&idcita='+id,0,0,100,100,'auinfo'); }
			function nuevo_recibo_garantia(id) {modal('zcartera.php?Acc=nuevo_recibo_garantia&idcita='+id,0,0,600,600,'recg');	}
			function ver_visitantes(id) {modal('zingreso_recepcion.php?Acc=consulta_ingreso&id='+id,0,0,600,600,'iv');}
			function formulario_entrega(estado,idcita,ultimokm) { modal('zcitas.php?Acc=formulario_entrega&idcita='+idcita+'&estado='+estado+'&ultimokm='+ultimokm,200,200,400,600,'fentrega');}
			function cambia_dias_servicio(id) {modal('zcitas.php?Acc=cambia_dias_servicio&id='+id,0,0,400,500,'cds');}
			function solicitar_factura(id) {modal('zcitas.php?Acc=solicitar_factura&cita='+id,0,0,400,500,'sfac');}

			function asigna_operario_entrega(operario,cita,objeto)
			{if(confirm('Asignar este operario a la entrega del vehículo?'))
			{window.open('zcitas.php?Acc=asigna_operario_entrega&operario='+operario+'&cita='+cita,'Citas_oculto');objeto.disabled=true;}}

			function insertar_domiciliod(id) {modal('zcitas.php?Acc=insertar_domiciliod&id='+id,0,0,300,300,'domic');	}

			function asigna_operario_devolucion(operario,cita,objeto)
			{if(confirm('Asignar este operario a la devolución del vehículo?'))
			{window.open('zcitas.php?Acc=asigna_operario_devolucion&operario='+operario+'&cita='+cita,'Citas_oculto');objeto.disabled=true;}}

			function estado_operarios()
			{var Oficina=document.forma1.OFI.value;var Dia=document.forma1.Dia.value;
				window.open('zcitas.php?Acc=estado_operarios&Oficina='+Oficina+'&Dia='+Dia+'&Modo=diario','Estado_Operarios');
				document.getElementById('Estado_Operarios').style.visibility='visible'; }

			function ocultar_estado_operarios() { document.getElementById('Estado_Operarios').style.visibility='hidden'; }
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
		 <input type='button' value='Consultar' onclick='Parar_recarga=false;cambio_fecha();'>
		 <a class='info' style='cursor:pointer' onclick='producir_informe()'><img src='gifs/print.png' height='20px' border='0' valign='top'><span>Producir un informe</span></a>
		 <a class='info' style='cursor:pointer' onclick='estado_operarios()'><img src='img/grafica.png' border='0' height='24px' valign='top'><span>Estado Operarios</span></a>
		 <font style='font-size:8px'>Versión Febrero 3 de 2012</font>";
	if(!inlist($USUARIO,'13,1,5'))
		 echo "Tiempo para refrescar: <span id='segundos' style='font-size:14;' onclick='para_recarga();'></span><script language='javascript'>disminuye_contador_segundos();</script> ";

	echo "<br /><h3 style='background-color:000055;color:44ff66;font-size:20px;' align='center'><a name='Entre'></a>ENTREGAS" . date('h:i A', strtotime($Hora)) . "  <a href='#Devol' style='color:ffffff'>Ir a Devoluciones</a></H3>";
	if ($USUARIO == 10)
	{
		$OFICINA = qo1("select oficina from usuario_oficina where id='" . $_SESSION['Id_alterno'] . "'");
	}
	elseif($USUARIO==33)
	{
		$OFICINA = qo1("select oficina from usuario_tesoreria where id='" . $_SESSION['Id_alterno'] . "'");
	}
	elseif($OFI)
		$OFICINA=$OFI;
	else
		$OFICINA = 0;
	echo "</form>";
	$Us = $_SESSION['User'];
	/////////////////////  *********************   ARREGLOS GLOBALES  **********************************************
	include('inc/link.php');
	$T = qo1m("select id from usuario_tab where usuario=$USUARIO and tabla='siniestro' ",$LINK);
	$Colores=mysql_query("select codigo,color_co from estado_citas ",$LINK);
	$Colores_cita=array();
	while($Bgc=mysql_fetch_object($Colores)) { $Colores_cita[$Bgc->codigo]=$Bgc->color_co; }
	$Aseguradoras=mysql_query("select * from aseguradora ",$LINK);
	$AAsegs=array();
	while($Ase=mysql_fetch_object($Aseguradoras)){$AAsegs[$Ase->id]=$Ase;}
	$Autorizaciones=array();
	$QAutorizaciones=mysql_query("select a.siniestro,a.id from sin_autor a,siniestro s,cita_servicio c where a.siniestro=s.id and s.id=c.siniestro and a.siniestro=c.siniestro and c.estado='P' and a.estado='A' and a.num_autorizacion!='' ",$LINK);
	while($Au=mysql_fetch_object($QAutorizaciones)) { $Autorizaciones[$Au->siniestro]=$Au->id; }
//	if(!$QUltimos=mysql_query("select v.placa,u.vehiculo,u.estado,t_estado_vehiculo(u.estado) as ne,u.fecha_inicial,u.fecha_final  from ubicacion u, vehiculo v where
//														v.id=u.vehiculo ".($OFICINA?" and v.ultima_ubicacion=$OFICINA":"").($ASEG?" and u.flota=$ASEG":"")." and u.estado not in (1,2,7,96)
//														and u.fecha_final='$Hoy' order by u.vehiculo,u.fecha_inicial,u.fecha_final,u.id ")) die(mysql_error());
//	$Ultimos=array();
//	while($Ul=mysql_fetch_object($QUltimos))
//	{
//		if(!$Ultimos[$Ul->placa]) $Ultimos[$Ul->placa]=array();
//		$Ultimos[$Ul->placa][count($Ultimos[$Ul->placa])]=$Ul;
//	}


	// /////////////////////  *******************     ENTREGAS   *****************************************//////////////////////////////////////////////////////

	$Consulta=citas_inicial_modo_entregas($Modo,$OFICINA,$ASEG,$Dia);


	if(!$Citas = mysql_query($Consulta,$LINK)) die(mysql_error());
	if (mysql_num_rows($Citas))
	{
		echo "<table border cellspacing=0 style='empty-cells:show' width='100%'><tr>
						<th>Numero</th>
						<th>Siniestro</th>
						<th>Fecha</th>
						<th>Hora programada</th>
						<th>Conductor</th>
						<th>Observaciones</th>
						<th>Vehículo</th>
						<th>Estado</th>
						</tr>";
		$c_of = '';

		/* controlador de cambio de oficina */
		$con_of = 1;
		/* contador de citas por oficina */

		while ($C = mysql_fetch_object($Citas))
		{
			$Futuro = '';$Sin_conc = '';
			if ($c_of != $C->noficina)
			{
				echo "<tr><td colspan=10 style='font-size:16;font-weight:bold;background-color:000000;color:ffff00'>$C->noficina</td></tr>";
				$c_of = $C->noficina;



				$con_of = 1;
			}
			$Bc=$Colores_cita[$C->cestado];
			$Aseguradora=$AAsegs[$C->aseguradora];
			echo "<tr bgcolor='$Bc' ";
			if(inlist($USUARIO, '1,2,7,4') && ($C->cestado != 'C' || $USUARIO==1))  // SI ES USUARIO GERENCIA, OPERATIVO, CALL CENTER
				echo "ondblclick='modifica_cita($C->cid);' ";
			if($C->cestado == 'C' ) echo " ondblclick=\"alert('Despues de cumplida la cita no se puede modificar');\" ";
			echo ">";
			citas_inicial_entrega_siniestro($con_of,$C,$Aseguradora,$T);
			citas_inicial_entrega_fecha($C);
			citas_inicial_entrega_hora($C,$Aseguradora);
			citas_inicial_entrega_conductor($C,$LINK);
			citas_inicial_tiempos($C,$LINK);
			if($C->cestado=='C')	echo "<br /><a onclick='ver_visitantes($C->siniestro);' style='cursor:pointer'>Visitantes..</a>";
			echo "</td><td>". nl2br($C->obscita) . ($C->obscita?" ":"") . "<a class='info' style='cursor:pointer' href=\"javascript:modal('zcitas.php?Acc=inserta_obs&id=$C->cid',0,0,500,500,'obs');\">
							<img src='gifs/mas.gif' border='0'><span>Insertar observaciones</span></a><br />";

			echo "</td><td align='center' class='placa' style='background-image:url(img/placa.jpg);' width='80px'>$C->cplaca</td><td align='center' width='20%'>";
			// /////////////   BUSQUEDA DE EVENTOS FUTUROS *************************************


			$Fec_inicial = $C->fecha;
			if($C->cestado!='P')
			{
				if($C->cestado=='C') citas_inicial_estado_cumplido($LINK,$C,$T);
				else echo menu1('estado', "Select codigo,nombre from estado_citas", $C->cestado, 0, "font-size:12", " disabled onchange=''; ",$LINK);
			}
			else  /// ESTADO = P  Programada
			{
				citas_inicial_busqueda_futuros($LINK,$C);
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
							echo "<br /><a onclick=\"modal('zcitas.php?Acc=imprimir_acta&idc=$C->cid',0,0,900,900,'impa');\" style='cursor:pointer;font-weight:bold;'><img src='gifs/print.png' border='0'> Imprimir el Acta de Entrega</a><br />";
							if($C->arribo!='0000-00-00 00:00:00' && $C->momento_entrega!='0000-00-00 00:00:00')
							{  ///  MUESTRA TODOS LOS ESTADOS INCLUYENDO * CUMPLIDA *
									$EST_CITA = "Select codigo,nombre from estado_citas where codigo in ('C','S','P') ";
							}
							else  // SI NO HA SIDO ENTREGADO EL VEHICULO
							{  ///  MUESTRA TODOS LOS ESTADOS MENOS  * CUMPLIDA *
								echo "<font color='blue'>Vehiculo en proceso de entrega</font>";
								$EST_CITA = "Select codigo,nombre from estado_citas  where codigo in ('S','P','W') ";
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
							echo menu1('estado', $EST_CITA, $C->cestado, 0, "font-size:12; width:200px;"," onchange=\"formulario_entrega(this.value,$C->cid,$C->uodo);this.value='P';\"",$LINK);
						}
						else
						{ echo menu1('estado', "Select codigo,nombre from estado_citas", $C->cestado, 0, "font-size:12;width:200px;", " disabled onchange=''; ",$LINK); }
					}
					else
					{
						echo menu1('estado', "Select codigo,nombre from estado_citas", $C->cestado, 0, "font-size:12;width:200px;", " disabled onchange=''; ",$LINK);
						echo "<br />Hora: <a onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$T&id=$C->siniestro',0,0,600,1000,'eds');\"
									style='cursor:pointer'>$C->hora_llegada <img src='gifs/standar/Pencil.png' border='0'></a>";
					}
				}
				else // CUANDO AUN HAY SERVICIOS SIN CONCLUIR O ESTADOS FUTUROS
				{
			        echo "<BR> <a onclick=\"modal('zcitas.php?Acc=imprimir_acta&idc=$C->cid',0,0,900,900,'impa');\" style='cursor:pointer;font-weight:bold;'><img src='gifs/print.png' border='0'> Imprimir el Acta de Entrega</a>";
					if(inlist($_SESSION['User'],'1,2,4,7,10,13,26'))
					{
						$Sin_observaciones = ($C->observaciones?0:1);
						echo menu1('estado', "Select codigo,nombre from estado_citas where codigo!='C'", $C->cestado, 0, "font-size:12;width:200px;"," onchange=\"formulario_entrega(this.value,$C->cid,0);this.value='P';\"",$LINK);
					}
					else // SI LOS USUARION NO SON LOS PERMITDOS, SIMPLEMENTE MUESTRA EL ESTADO PROTEJIDO
						echo menu1('estado', "select codigo,nombre from estado_citas", $C->cestado,0," font-size:12;width:200px;"," disabled ",$LINK);
				}
			}
			echo "</td></tr>";
			// ////////  CONTADOR DE OFICINA
			$con_of++;
		}

		echo "</table>";
	}
	else
	{
		echo "<b style='color:ff0000'>No hay citas programadas para entrega</b>";
	}
	// ///////////////////////////////    ---------------------------------------     DEVOLUCIONES --------------------------------------------  /////////////////////////////////////////////////////////
	// ///////////////////////////////    ---------------------------------------     DEVOLUCIONES --------------------------------------------  /////////////////////////////////////////////////////////
	// ///////////////////////////////    ---------------------------------------     DEVOLUCIONES --------------------------------------------  /////////////////////////////////////////////////////////
	// ///////////////////////////////    ---------------------------------------     DEVOLUCIONES --------------------------------------------  /////////////////////////////////////////////////////////
	// ///////////////////////////////    ---------------------------------------     DEVOLUCIONES --------------------------------------------  /////////////////////////////////////////////////////////

	echo "<br /><br /><br /><h3 style='background-color:000055;color:44ff66;font-size:20px;' align='center'><a name='Devol'></a>DEVOLUCIONES <a href='#Entre' style='color:ffffff'>Ir a Entregas</a></H3>";
	$Consulta=citas_devolucion_modo($Modo, $OFICINA, $ASEG, $Dia);

	if(!$Devoluciones = mysql_query($Consulta,$LINK)) die(mysql_error());
	if(mysql_num_rows($Devoluciones))
	{
		echo "<table border cellspacing=0 style='empty-cells:show' width='100%'><tr>
						<th>Numero</th>
						<th>Siniestro</th>
						<th>Fecha</th>
						<th>Hora programada</th>
						<th>Conductor</th>
						<th>Observaciones</th>
						<th>Vehículo</th>
						<th>Estado</th>
						</tr>";
		$c_of = '';
		/* controlador de cambio de oficina */ $con_of = 1;
		/* contador de citas por oficina */

		include('inc/link.php');
		while ($C = mysql_fetch_object($Devoluciones))
		{
			if ($c_of != $C->noficina)
			{
				echo "<tr><td colspan=10 style='font-size:16;font-weight:bold;background-color:000000;color:ffff00'>$C->noficina</td></tr>";
				$c_of = $C->noficina;
				$con_of = 1;
			}
			$Aseguradora=$AAsegs[$C->aseguradora];
			if ($C->estadod == 'C') $Bc = 'ccffcc';	else $Bc = 'ffffff';
			echo "<tr bgcolor='$Bc' " . (inlist($USUARIO, '1,2,4')?"ondblclick='modifica_cita($C->cid);' ":"") . " ><td align='center'>$con_of</td>";
			citas_devolucion_siniestro($C,$Aseguradora,$T);
			citas_devolucion_fechahora($C,$Aseguradora);
			citas_devolucion_conductor($C,$LINK);
			citas_devolucion_observaciones($C);
			echo "<td align='center' nowrap='yes'>";
			// //  si el usuario es call center, puede cancelar o reprogramar la cita
			// / si el usuario es operativo, puede cumplir la cita.
			if (inlist($_SESSION['User'], '1,2,4,10,13')) // call center y Autorizaciones
				echo menu3('estadod', $ESTADOS_CITA_DEVOLUCION, $C->estadod, 0, "font-size:12", ($C->estadod == 'C'?" disabled":"") . " onchange=\"formulario_devolucion($C->cid);this.value='P';\" ");
			else echo menu3('estadod', $ESTADOS_CITA_DEVOLUCION, $C->estadod, 0, "font-size:12", " disabled onchange=''; ");
			if ($C->estadod == 'C') citas_devolucion_cumplido($C,$T);
			echo "</td></tr>";
			$con_of++;
		}
		echo "</table>";
	}
	else echo "<b style='color:ff0000'>No hay devoluciones programadas</b>";
	mysql_close($LINK);
	echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></body>";
}

//-------------------------------------------------------------------------------------------------------------------------------------------------------

function citas_inicial_modo_entregas($Modo,$OFICINA,$ASEG,$Dia)
{
	switch($Modo)
	{
		case 1:  return "select c.*,t_oficina(c.oficina) as noficina,t_siniestro(c.siniestro) as nsiniestro,t_aseguradora(c.flota) as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,t_ciudad(s.ciudad) as nciudad,t_ciudad(s.ciudad_original) as nciudado,t_estado_siniestro(s.estado) as nestado,c.estado as cestado,s.estado as sestado,
									v.nombre_propietario,v.id_propietario,kilometraje(v.id) as uodo
									from cita_servicio c,siniestro s,vehiculo v
									where c.siniestro=s.id and v.placa=c.placa and c.fecha='$Dia' " .	($OFICINA?" and c.oficina='$OFICINA' ":" ")." and c.estado='P' ".($ASEG?" and s.aseguradora=$ASEG":"")." order by noficina,c.hora";
			break;
		case 2: return "select c.*,t_oficina(c.oficina) as noficina,t_siniestro(c.siniestro) as nsiniestro,t_aseguradora(c.flota) as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,t_ciudad(s.ciudad) as nciudad,t_ciudad(s.ciudad_original) as nciudado,t_estado_siniestro(s.estado) as nestado,c.estado as cestado,s.estado as sestado,
									v.nombre_propietario,v.id_propietario,kilometraje(v.id) as uodo
									from cita_servicio c,siniestro s,vehiculo v
									where c.siniestro=s.id and v.placa=c.placa and c.fecha='$Dia' " .	($OFICINA?" and c.oficina='$OFICINA' ":" ")." and c.estado in  ('S','X','Y','N') ".($ASEG?" and s.aseguradora=$ASEG":"")." order by noficina,c.hora";
			break;
		case 3: return "select c.*,t_oficina(c.oficina) as noficina,t_siniestro(c.siniestro) as nsiniestro,t_aseguradora(c.flota) as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,t_ciudad(s.ciudad) as nciudad,t_ciudad(s.ciudad_original) as nciudado,t_estado_siniestro(s.estado) as nestado,c.estado as cestado,s.estado as sestado,
									v.nombre_propietario,v.id_propietario,kilometraje(v.id) as uodo
									from cita_servicio c,siniestro s,vehiculo v
									where c.siniestro=s.id and v.placa=c.placa and c.fecha='$Dia' " .	($OFICINA?" and c.oficina='$OFICINA' ":" ")." and c.estado='C' ".($ASEG?" and s.aseguradora=$ASEG":"")." order by noficina,c.hora";
			break;
		case 4: return "select c.*,t_oficina(c.oficina) as noficina,t_siniestro(c.siniestro) as nsiniestro,t_aseguradora(c.flota) as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,t_ciudad(s.ciudad) as nciudad,t_ciudad(s.ciudad_original) as nciudado,t_estado_siniestro(s.estado) as nestado,c.estado as cestado,s.estado as sestado,
									v.nombre_propietario,v.id_propietario,kilometraje(v.id) as uodo
									from cita_servicio c,siniestro s,vehiculo v
									where c.siniestro=s.id and v.placa=c.placa and c.fecha='$Dia' " .	($OFICINA?" and c.oficina='$OFICINA' ":" ")." and c.estado in  ('P','C') ".($ASEG?" and s.aseguradora=$ASEG":"")." order by noficina,c.hora";
			break;
		case 5: return "select c.*,t_oficina(c.oficina) as noficina,t_siniestro(c.siniestro) as nsiniestro,t_aseguradora(c.flota) as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,t_ciudad(s.ciudad) as nciudad,t_ciudad(s.ciudad_original) as nciudado,t_estado_siniestro(s.estado) as nestado,c.estado as cestado,s.estado as sestado,
									v.nombre_propietario,v.id_propietario,kilometraje(v.id) as uodo
									from cita_servicio c,siniestro s,vehiculo v
									where c.siniestro=s.id and v.placa=c.placa and c.fecha='$Dia' " .	($OFICINA?" and c.oficina='$OFICINA' ":" ")." ".($ASEG?" and s.aseguradora=$ASEG":"")." order by noficina,c.hora";
			break;
	}
}

function citas_devolucion_modo($Modo,$OFICINA,$ASEG,$Dia)
{
	switch($Modo)
	{
		case 1: return "select c.*,t_oficina(c.oficina) as noficina,t_siniestro(c.siniestro) as nsiniestro,t_aseguradora(c.flota) as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,t_ciudad(s.ciudad) as nciudad,t_ciudad(s.ciudad_original) as nciudado,t_estado_siniestro(s.estado) as nestado,c.estado as cestado,s.estado as sestado,
									v.nombre_propietario,v.id_propietario,kilometraje(v.id) as uodo
									from cita_servicio c,siniestro s,vehiculo v
									where c.siniestro=s.id and v.placa=c.placa and  c.fec_devolucion='$Dia' and c.estado='C'  " . ($OFICINA?" and c.oficina='$OFICINA' ":" ") ." and c.estadod='P' ".($ASEG?"and s.aseguradora=$ASEG ":""). " order by noficina,hora_devol";
			break;
		case 2: return "select c.*,t_oficina(c.oficina) as noficina,t_siniestro(c.siniestro) as nsiniestro,t_aseguradora(c.flota) as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,t_ciudad(s.ciudad) as nciudad,t_ciudad(s.ciudad_original) as nciudado,t_estado_siniestro(s.estado) as nestado,c.estado as cestado,s.estado as sestado,
									v.nombre_propietario,v.id_propietario,kilometraje(v.id) as uodo
									from cita_servicio c,siniestro s,vehiculo v
									where c.siniestro=s.id and v.placa=c.placa and  c.fec_devolucion='$Dia' and c.estado='C'  " . ($OFICINA?" and c.oficina='$OFICINA' ":" ") ." and c.estadod='X' ".($ASEG?"and s.aseguradora=$ASEG ":""). " order by noficina,hora_devol";
			break;
		case 3: return "select c.*,t_oficina(c.oficina) as noficina,t_siniestro(c.siniestro) as nsiniestro,t_aseguradora(c.flota) as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,t_ciudad(s.ciudad) as nciudad,t_ciudad(s.ciudad_original) as nciudado,t_estado_siniestro(s.estado) as nestado,c.estado as cestado,s.estado as sestado,
									v.nombre_propietario,v.id_propietario,kilometraje(v.id) as uodo
									from cita_servicio c,siniestro s,vehiculo v
									where c.siniestro=s.id and v.placa=c.placa and  c.fec_devolucion='$Dia' and c.estado='C'  " . ($OFICINA?" and c.oficina='$OFICINA' ":" ") ." and c.estadod='C' ".($ASEG?"and s.aseguradora=$ASEG ":""). " order by noficina,hora_devol";
			break;
		case 4: return "select c.*,t_oficina(c.oficina) as noficina,t_siniestro(c.siniestro) as nsiniestro,t_aseguradora(c.flota) as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,t_ciudad(s.ciudad) as nciudad,t_ciudad(s.ciudad_original) as nciudado,t_estado_siniestro(s.estado) as nestado,c.estado as cestado,s.estado as sestado,
									v.nombre_propietario,v.id_propietario,kilometraje(v.id) as uodo
									from cita_servicio c,siniestro s,vehiculo v
									where c.siniestro=s.id and v.placa=c.placa and  c.fec_devolucion='$Dia' and c.estado='C'  " . ($OFICINA?" and c.oficina='$OFICINA' ":" ") ." and c.estadod in ('P','C') ".($ASEG?"and s.aseguradora=$ASEG ":""). " order by noficina,hora_devol";
			break;
		case 5: return "select c.*,t_oficina(c.oficina) as noficina,t_siniestro(c.siniestro) as nsiniestro,t_aseguradora(c.flota) as nflota,c.observaciones as obscita,c.id as cid,s.id as sid,c.placa as cplaca,
									s.*,t_ciudad(s.ciudad) as nciudad,t_ciudad(s.ciudad_original) as nciudado,t_estado_siniestro(s.estado) as nestado,c.estado as cestado,s.estado as sestado,
									v.nombre_propietario,v.id_propietario,kilometraje(v.id) as uodo
									from cita_servicio c,siniestro s,vehiculo v
									where c.siniestro=s.id and v.placa=c.placa and  c.fec_devolucion='$Dia' and c.estado='C'  " . ($OFICINA?" and c.oficina='$OFICINA' ":" ") ."  ".($ASEG?"and s.aseguradora=$ASEG ":""). " order by noficina,hora_devol";
			break;
	}

}

function citas_inicial_busqueda_futuros($LINK,$C)
{
	global $Hoy,$Ultimos;
	$Fec_inicial = $C->fecha;
	$Fec_final = date('Y-m-d', strtotime(aumentadias($Fec_inicial, 7)));
	$Fec_futuros1 = date('Y-m-d', strtotime(aumentadias($Fec_inicial, 1)));
	if($Ultimos[$C->cplaca])
	{
		echo "<b style='color:0000ff'>".count($Ultimos[$C->cplaca])."</b>";
	}
	$Ultimo_estado=qom("select u.estado,t_estado_vehiculo(u.estado) as ne,u.fecha_inicial,u.fecha_final from ubicacion u,vehiculo v where
					v.id=u.vehiculo and v.placa='$C->cplaca' and u.fecha_final='$Hoy' order by u.id desc limit 1",$LINK);

	if ($Futuros = mysql_query("select t_estado_vehiculo(u.estado) as ne,u.fecha_inicial,u.fecha_final from ubicacion u ,vehiculo v where
												v.id=u.vehiculo and v.placa='$C->cplaca' and u.estado!=1 and u.estado!=96 and u.fecha_final>'$Hoy' and
												(		(u.fecha_inicial between '$Fec_futuros1' and '$Fec_final')    or  	( u.fecha_final between '$Fec_futuros1' and '$Fec_final') 		)   ",$LINK))
	{
		$Futuro = '';
		while ($F = mysql_fetch_object($Futuros))
			$Futuro .= "[<B>$F->ne</B> <U>$F->fecha_inicial</U> - <U>$F->fecha_final</U>] ";
	}
	if($Ultimo_estado)
	{if($Ultimo_estado->estado!=2 /* parqueadero */ && $Ultimo_estado->estado!=7 /*servicio concluido */  && $Ultimo_estado->estado!=1 && $Ultimo_estado->estado!=96 /*Domicilio*/)
			$Futuro.="<B>$Ultimo_estado->ne</B>, <U>$Ultimo_estado->fecha_inicial</U> - <U>$Ultimo_estado->fecha_final</U>]";}

	if($Futuro)
		echo "<font color='red'>EVENTOS FUTUROS: </font>$Futuro<br />Verifique con el Control Operativo para modificar los eventos futuros o reprogramar esta cita.";

	// /////////////   BUSQUEDA DE SERVICIOS SIN CONCLUIR *************************************
	$Sin_conc = '';
	if ($Sinconcluir = mysql_query("select u.id,u.fecha_inicial,u.fecha_final from ubicacion u,vehiculo v where
														v.id=u.vehiculo and v.placa='$C->cplaca' and u.fecha_final <= '$Fec_inicial' and u.estado=1",$LINK))
	{
		while ($SC = mysql_fetch_object($Sinconcluir))
		{
			$Num_siniestro = qo1m("select numero from siniestro where ubicacion=$SC->id",$LINK);
			$Sin_conc .= "[$Num_siniestro <U>$SC->fecha_inicial</U> - <U>$SC->fecha_final</U>] ";
		}
		if($Sin_conc)
		{
			echo "<br><font color='red' style='text-decoration:blink;font-weight:bold;'>SERVICIO PREVIO SIN CONCLUIR: </font>$Sin_conc <br />
						Concluya los servicios previos a esta cita para poder continuar.<br />";
		}
	}
}

function citas_inicial_estado_cumplido($LINK,$C,$T)
{
	echo menu1('estado', "Select codigo,nombre from estado_citas", $C->cestado, 0, "font-size:12", " disabled onchange=''; ",$LINK);
	echo "<br /><a onclick=\"modal('zcitas.php?Acc=imprimir_acta&idc=$C->cid',0,0,900,900,'impa');\" style='cursor:pointer;font-weight:bold;'><img src='gifs/print.png' border='0'> Imprimir el Acta de Entrega</a><br />";
	if($T)
	{
		echo "<br />Hora: <a onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$T&id=$C->siniestro',0,0,600,1000,'eds');\"
					style='cursor:pointer'>$C->hora_llegada <img src='gifs/standar/Pencil.png' border='0'></a><br />";
	}
	else
	{
		echo "<br />Hora: $C->hora_llegada <br />";
	}

	$okimg1="<img src='gifs/standar/si.png' border='0' style='cursor:pointer' onclick=\"modal('";
	$okimg2="',0,0,500,500,'foto');\" border='0' ";
	if($C->img_cedula_f) echo $okimg1.$C->img_cedula_f.$okimg2." alt='Cédula' title='Cédula'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Cédula' title='Cédula'>";
	if($C->img_pase_f) echo $okimg1.$C->img_pase_f.$okimg2." alt='Reverso Cédula' title='Reverso Cédula'>"; else echo "<img src='gifs/standar/Warning.png' border='0' alt='Reverso Cédula' title='Reverso Cédula'>";
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

function citas_inicial_entrega_hora($C,$Aseguradora)
{
	global $Hora;
	echo "<td align='center'><a class='info'>" . ($C->hora < $Hora && $C->cestado == 'P' && $C->arribo=='0000-00-00 00:00:00'?
				"<b style='color:ff0000;text-decoration:blink;'>" . date('h:i A', strtotime($C->hora)) . "</b>":
				date('h:i A', strtotime($C->hora))) . "<span style='width:700px'>
								<h3>Control de Seguimiento de Citas <i style='color:00000'>$Aseguradora->nombre  Póliza Número: $C->poliza Siniestro No. $C->numero </i></h3>
								Ciudad: <b>$C->nciudad</b> " . ($C->nciudado?"Ciudad original: <b>$C->nciudado</b>":"") .
				                "<br />Fecha del siniestro: <b>$C->fec_siniestro</b> Fecha de declaración del siniestro: <b>$C->fec_declaracion</b>
								FECHA DE AUTORIZACION: <b style='color:ff0000'>$C->fec_autorizacion</b>
								<br />Vigencia de la póliza:  Desde <b>$C->vigencia_desde</b> hasta <b>$C->vigencia_hasta</b>
								<br />Placa: <b>$C->placa</b> Marca: <b>$C->marca</b> Tipo: <b>$C->tipo</b> Línea: <b>$C->linea</b>
								Modelo: <b>$C->modelo</b> Clase: <b>$C->clase</b>
								<hr color='eeeeee'><b><u>ASEGURADO:</u></b> Nombre: <b>$C->asegurado_nombre</b> Identificación: <b>$C->asegurado_id</b>
								<hr color='eeeeee'><b><u>Declarante:</u></b> Nombre: <b>$C->declarante_nombre</b> Identificación: <b>$C->declarante_id</b>
								Telefonos: <b>$C->declarante_telefono / $C->declarante_tel_resid
								/ $C->declarante_tel_ofic / $C->declarante_celular</b>
								<hr color='eeeeee'><b><u>Conductor:</u></b> Nombre: <b>$C->conductor_nombre</b> Telefonos: <b>$C->declarante_telefono / $C->conductor_tel_resid
								/ $C->conductor_tel_ofic / $C->conductor_celular / $C->conductor_tel_otro</b><hr>
								Observaciones: $C->observaciones</span></a><br />FLOTA: $C->nflota
								<br>Propietario: $C->nombre_propietario Nit: ".coma_format($C->id_propietario)."
						</td>";

}

function citas_inicial_entrega_fecha($C)
{
	global $USUARIO;
	echo "<td nowrap='yes'>$C->fecha<br>Días de <br>Servicio: ";
	if((($USUARIO==26 && $_SESSION['Id_alterno']==5) || $USUARIO==1) && $C->cestado=='P') echo "<a class='info' style='cursor:pointer' onclick='cambia_dias_servicio($C->cid)'>";
	echo "<b>$C->dias_servicio</b>";
	if((($USUARIO==26 && $_SESSION['Id_alterno']==5) || $USUARIO==1) && $C->cestado=='P') echo "</a>";
	echo "</td>";
}

function citas_inicial_entrega_siniestro($con_of,$C,$Aseguradora,$T)
{
	global $USUARIO,$PRE_AUTORIZA;
	echo "<td align='center'>$con_of</td><td>$C->nsiniestro <br>";
	if($C->cestado != 'C' && inlist($USUARIO,'1,2,7,4')) // SI ES USUARIO GERENCIA, OPERATIVO, CALL CENTER
		echo "<a class='info' href=\"javascript:modal('zcallcenter.php?Acc=inicio_proceso_call&id_siniestro=$C->siniestro',0,0,600,900,'ca');\" alt='Call Center' title='Call Center'>
					<img src='img/callphone.png' border='0' height='20' align='middle'><span style='width:100px'>Call Center</span></a>&nbsp;";
	if($C->no_garantia)
	echo " <a class='info'><img src='img/nogarantia.png' border='0' height='20px' alt='Servicio Sin Garantia' title='Servicio Sin Garantia' align='middle'>
				<span><img src='img/nogarantia.png'><h3>Servicio Sin Garantía</h3></span></a> ";

	if($C->cestado == 'P' && inlist($USUARIO,'1,2,5,7,10')) // SI ES USUARIO GERENCIA, AUTORIZACIONES, OPERATIVO, OFICINA
		echo "<a class='info' href=\"javascript:modal('zautorizaciones.php?sini=$C->numero',0,0,600,600,'call');\" alt='Solicitar Autorizacion' title='Solicitar Autorizacion'>
							<img src='img/solicita_autorizacion.png' border='0' height='20' align='middle'><span style='width:100px'>Solicitar Autorización</span></a>&nbsp;";
	if(inlist($USUARIO,'1,6')) // SI EL USUARIO ES FACTURACION
	{
		echo "<a href=\"javascript:inicia_facturacion($C->cid);void(null);\" class='info'><img src='img/facturar.png' height='20' border='0' align='middle'><span style='width:200px'>Iniciar proceso de facturación</span></a>&nbsp;";
	}
	if(inlist($Us,'1,10,33'))  // SI EL USUARIO ES CAJERO
	{
		echo "<a href=\"javascript:nuevo_recibo_garantia($C->cid);void(null);\" class='info'><img src='img/caja_registradora.png' height='20' border='0' align='middle'><span style='width:200px'>Recibo Caja x Garantia</span></a>&nbsp;";
	}
	if($PRE_AUTORIZA)
	{
		echo "<a href=\"javascript:nuevo_recibo_garantia($C->cid);void(null);\" class='info'><img src='img/caja_registradora.png' height='20' border='0' align='middle'><span style='width:200px'>PRE-Autorización Efectivo o TD</span></a>&nbsp;";
	}
	echo "[<font color='blue'>$Aseguradora->nombre</font>]<br />Estado: $C->nestado<br />";
	if($T)
	{
		echo "<a onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$T&id=$C->siniestro',0,0,600,1000,'eds');\"	style='cursor:pointer'><u>Ver Siniestro</u></a>";
	}
	if(inlist($USUARIO,'1,2,5,6,13,33') && $C->cestado=='C')
		echo "	<a onclick=\"modal('zautorizaciones.php?Acc=ver_autorizaciones&id=$C->siniestro',0,0,600,1000,'eds');\"	style='cursor:pointer'><u>Ver Autorizaciones</u></a>
					<a onclick=\"modal('zautorizaciones.php?Acc=datos_autorizacion&id=$C->siniestro',0,0,600,1000,'eds');\"	style='cursor:pointer'><u>Solicitar Información</u></a>
					";
	echo "</td>";
}

function citas_inicial_tiempos($C,$LINK)
{
	global $USUARIO;
	if($C->cestado=='P') // CONTROL DE TIEMPOS
	{
		if($C->arribo!='0000-00-00 00:00:00')
		{
			if($Autorizacion = qom("select id,fecha_solicitud,fecha_proceso from sin_autor where siniestro='$C->siniestro' and estado!='R' ",$LINK))
			{
				$Tiempo1_solicitud_autorizacion=diferencia_tiempo($C->arribo,$Autorizacion->fecha_solicitud);
				echo "<br>Tiempo entre Arribo y Solicitud Autorización: <b style='font-size:12px;color:4444ff'>$Tiempo1_solicitud_autorizacion</b>";
				if($Autorizacion->fecha_proceso!='0000-00-00 00:00:00')
				{
					$Tiempo2_proceso_autorizacon=diferencia_tiempo($Autorizacion->fecha_solicitud,$Autorizacion->fecha_proceso);
					echo "<br>Tiempo entre Solicitud y Proceso Autorización: <b style='font-size:12px;color:4444ff'>$Tiempo2_proceso_autorizacon</b>";
					if($C->momento_entrega!='0000-00-00 00:00:00')
					{
						$Tiempo3_entrega=diferencia_tiempo($Autorizacion->fecha_proceso,$C->momento_entrega);
						echo "<br>Tiempo entre Proceso Autorización y entrega del Vehículo: <b style='font-size:12px;color:4444ff'>$Tiempo3_entrega</b>";
					}
					else
					{
						if(inlist($USUARIO,'1,13,23,10'))
						{
							echo "<br><a class='info' style='cursor:pointer' onclick='marcar_entrega($C->cid);'><img src='img/vehiculo.png' border='0' height='30'> Marcar Entrega del Vehículo <span  style='width:200px'>Marcar Entrega del Vehiculo</span></a>";
						}
					}
				}
				else
				{
					$Tiempo=diferencia_tiempo($Autorizacion->fecha_solicitud,date('Y-m-d H:i:s'));
					echo "<br><b style='font-size:14px;text-decoration:blink;color:cc0000;background-color:ffff55;'>Esperando proceso de Autorización: $Tiempo EN ESPERA</b>";
				}
			}
			else
			{
				$Tiempo=diferencia_tiempo($C->arribo,date('Y-m-d H:i:s'));
				echo "<br><b style='font-size:14px;text-decoration:blink;color:cc0000;background-color:ffff55;'>EL CLIENTE LLEVA $Tiempo EN ESPERA</b>";
			}
			echo "<br /><a onclick='ver_visitantes($C->siniestro);' style='cursor:pointer'>Visitantes..</a>";

		}
		elseif(inlist($USUARIO,'1,2,5,32,10'))
		{
			echo "<br><a class='info' style='cursor:pointer' onclick='arribo_asegurado($C->cid);'><img src='img/arribo_asegurado.png' border='0' height='30'><span>Marcar Arribo de Asegurado</span></a>&nbsp; ";
		}
		if(!$C->dir_domicilio && $C->arribo=='0000-00-00 00:00:00')
		{
			if(inlist($USUARIO,'1,2,4,13'))
			{
				echo "<br><a class='info' style='cursor:pointer' onclick='insertar_domicilio($C->cid);'><img src='gifs/standar/seguir_amarillo.png' border='0'><span style='width:200px'>Crear Domicilio Entrega</span></a>&nbsp; ";
			}
		}
	}
	else
	{
		$Autorizacion = qom("select * from sin_autor where siniestro='$C->siniestro' and estado='A' and num_autorizacion!='' ",$LINK);
		$Tiempo1_solicitud_autorizacion=diferencia_tiempo($C->arribo,$Autorizacion->fecha_solicitud);
		$Tiempo2_proceso_autorizacion=diferencia_tiempo($Autorizacion->fecha_solicitud,$Autorizacion->fecha_proceso);
		$Tiempo3_entrega=diferencia_tiempo($Autorizacion->fecha_proceso,$C->momento_entrega);
		$Tiempo4_registro=diferencia_tiempo($C->momento_entrega,$C->fecha.' '.$C->hora_llegada);
		echo "<br /><table cellspacing='1'><tr ><td bgcolor='dedede'>Arribo:</td><td colspan=4 bgcolor='dedede'>".date('H:i',strtotime($C->arribo))."</td></tr>
					<tr ><td bgcolor='dedede'>Autorización:</td><td bgcolor='dedede'>".date('H:i',strtotime($Autorizacion->fecha_solicitud))."</td><td bgcolor='dedede'>Dif:</td><td bgcolor='dedede'>$Tiempo1_solicitud_autorizacion</td></tr>
					 <tr ><td bgcolor='dedede'>Proceso Autorización:</td><td bgcolor='dedede'>".date('H:i',strtotime($Autorizacion->fecha_proceso))."</td><td bgcolor='dedede'>Dif:</td><td bgcolor='dedede'>$Tiempo2_proceso_autorizacion</td></tr>
				    <tr ><td bgcolor='dedede'>Entrega Vehículo:</td><td bgcolor='dedede'>".date('H:i',strtotime($C->momento_entrega))."</td><td bgcolor='dedede'>Dif:</td><td bgcolor='dedede'>$Tiempo3_entrega</td></tr>
					<tr ><td bgcolor='dedede'>Registro final:</td><td bgcolor='dedede'>".date('H:i',strtotime($C->hora_llegada))."</td><td bgcolor='dedede'>Dif:</td><td bgcolor='dedede'>$Tiempo4_registro</td></tr></table> ";
	}
}

function citas_inicial_entrega_conductor($C,$LINK)
{
	global $USUARIO;
	echo "<td width='200px'>$C->conductor  ";
	if($C->dir_domicilio)
		echo "<br /><span style='background-color:ffff33;font-size:12px;'><B>DOMICILIO ENTREGA</B>: $C->dir_domicilio <br /><B>TELEFONO</B>: $C->tel_domicilio</span><br>";
	if(inlist($USUARIO,'1,2,3,5,6,7,10,13,23,27'))
		echo "<br /><a class='info' style='cursor:pointer' onclick='solicitar_factura($C->cid);'><img src='gifs/standar/nuevo_ovr.png' border='0' height='18px'><span>Solicitar Factura</span></a>&nbsp;";
	if(inlist($USUARIO,'1,2,10,13,27'))
		echo menu1("operario_entrega","select id,concat(apellido,' ',nombre) from operario where inactivo=0 and oficina=$C->oficina order by apellido,nombre",$C->operario_domicilio,1,'',($C->operario_domicilio?"disabled ":"onchange='asigna_operario_entrega(this.value,$C->cid,this);' "),$LINK);
	else
		echo "<br />Operario: ".qo1m("select concat(apellido,' ',nombre) from operario where id=$C->operario_domicilio",$LINK);
}

//--------------------------------------------------------------------------------------------------------------------

function citas_devolucion_conductor($C,$LINK)
{
	global $USUARIO;
	echo "<td width='200px'>$C->conductor<br />";
	if(inlist($USUARIO,'1,2,3,5,6,7,10,13,23,27'))
		echo "<a class='info' style='cursor:pointer' onclick='solicitar_factura($C->cid);'><img src='gifs/standar/nuevo_ovr.png' border='0'><span>Solicitar Factura</span></a>&nbsp;";
	if($C->dir_domiciliod)
		echo "<br /><span style='background-color:ffff33;font-size:12px;'><B>DOMICILIO DEVOLUCION</B>: $C->dir_domiciliod <br /><B>TELEFONO</B>: $C->tel_domiciliod</span><br />";
	elseif(inlist($USUARIO,'1,2,4,13'))
		echo "&nbsp;<a class='info' style='cursor:pointer' onclick='insertar_domiciliod($C->cid);'><img src='gifs/standar/seguir_amarillo.png' border='0'><span style='width:200px'>Crear Domicilio Devolución</span></a>&nbsp; ";
	if(inlist($USUARIO,'1,2,10,13,27'))
		echo "<br />".menu1("operario_devolucion","select id,concat(apellido,' ',nombre) from operario where inactivo=0 and oficina=$C->oficina order by apellido,nombre",$C->operario_domiciliod,1,'',($C->operario_domiciliod?"disabled ":"onchange='asigna_operario_devolucion(this.value,$C->cid,this);' "),$LINK);
	else
			echo "<br />Operario: ".qo1m("select concat(apellido,' ',nombre) from operario where id=$C->operario_domiciliod",$LINK);
	echo "</td>";
}

function citas_devolucion_siniestro($C,$Aseguradora,$T)
{
	global $USUARIO;
	echo "<td width='300px'>$C->nsiniestro<br>";
	if(inlist($USUARIO,'1,6'))
		echo "<a href=\"javascript:inicia_facturacion($C->cid);void(null);\" class='info'><img src='img/facturar.png' height='24' border='0' align='middle'><span style='width:200px'>Iniciar proceso de facturación</span></a>&nbsp;";
	if(inlist($USUARIO,'1,6,10'))
		echo "&nbsp;<a class='info' href=\"javascript:modal('zautorizaciones.php?sini=$C->numero',0,0,600,600,'call');\" alt='Solicitar Autorizacion' title='Solicitar Autorizacion'><img src='img/solicita_autorizacion.png' border='0' height='30' align='middle'><span style='width:100px'>Solicitar Autorización</span></a> ";
	echo "[<font color='red'>$Aseguradora->nombre</font>]<br />	Estado: $C->nestado";
	if(inlist($USUARIO,'1,2') )
		echo "<br /><a onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$T&id=$C->siniestro',0,0,600,1000,'eds');\"	style='cursor:pointer'><u>Ver Siniestro</u></a>&nbsp;<a onclick=\"modal('zautorizaciones.php?Acc=ver_autorizaciones&id=$C->siniestro',0,0,600,1000,'eds');\"	style='cursor:pointer'><u>Ver Autorizaciones</u></a>";
	echo "</td>";
}

function citas_devolucion_fechahora($C,$Aseguradora)
{
	global $Hora;
	echo "<td nowrap='yes'>$C->fec_devolucion</td><td align='center'><a class='info'>" . ($C->hora_devol < $Hora && $C->estadod == 'P'?
				"<b style='color:ff0000;text-decoration:blink;'>" . date('h:i A', strtotime($C->hora_devol)) . "</b>":
				date('h:i A', strtotime($C->hora_devol))) . "<span style='width:700px'>
								<h3>Control de Seguimiento de Citas <i style='color:00000'>$Aseguradora->nombre  Póliza Número: $C->poliza Siniestro No. $C->numero </i></h3>
								Ciudad: <b>$C->nciudad</b> " . ($C->nciudado?"Ciudad original: <b>$C->nciudado</b>":"") .
                "<br />Fecha del siniestro: <b>$C->fec_siniestro</b> Fecha de declaración del siniestro: <b>$C->fec_declaracion</b>
								FECHA DE AUTORIZACION: <b style='color:ff0000'>$C->fec_autorizacion</b>
								<br />Vigencia de la póliza:  Desde <b>$C->vigencia_desde</b> hasta <b>$C->vigencia_hasta</b>
								<br />Placa: <b>$C->placa</b> Marca: <b>$C->marca</b> Tipo: <b>$C->tipo</b> Línea: <b>$C->linea</b>
								Modelo: <b>$C->modelo</b> Clase: <b>$C->clase</b>
								<hr color='eeeeee'><b><u>ASEGURADO:</u></b> Nombre: <b>$C->asegurado_nombre</b> Identificación: <b>$C->asegurado_id</b>
								<hr color='eeeeee'><b><u>Declarante:</u></b> Nombre: <b>$C->declarante_nombre</b> Identificación: <b>$C->declarante_id</b>
								Telefonos: <b>$C->declarante_telefono / $C->declarante_tel_resid
								/ $C->declarante_tel_ofic / $C->declarante_celular</b>
								<hr color='eeeeee'><b><u>Conductor:</u></b> Nombre: <b>$C->conductor_nombre</b> Telefonos: <b>$C->declarante_telefono / $C->conductor_tel_resid
								/ $C->conductor_tel_ofic / $C->conductor_celular / $C->conductor_tel_otro</b><hr>
								Observaciones: $C->observaciones</span></a><br />FLOTA: $C->nflota
								<br>Propietario: $C->nombre_propietario Nit: ".coma_format($C->id_propietario)."</td>";
}

function citas_devolucion_observaciones($C)
{
	echo "<td>".nl2br($C->obs_devolucion)."<br><a class='info' style='cursor:pointer' href=\"javascript:modal('zcitas.php?Acc=inserta_obsd&id=$C->cid',0,0,500,500,'obs');\"><img src='gifs/mas.gif' border='0'><span>Insertar observaciones</span></a><br /></td><td align='center' class='placa' style='background-image:url(img/placa.jpg);' width='80px'>$C->cplaca</td>";
}

function citas_devolucion_cumplido($C,$T)
{
	if($T)
		echo "<br />Hora: <a onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$T&id=$C->siniestro',0,0,600,1000,'eds');\" style='cursor:pointer'>$C->hora_devol_real <img src='gifs/standar/Pencil.png' border='0'></a><br />";
	else
		echo "<br />Hora: $C->hora_devol_real <br />";
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

function informe_plano()
{
	global $Fecha,$Oficina,$Aseguradora;
	html('INFORME DE CITAS '.$Fecha);
	echo "<body><script language='javascript'>centrar();</script>
		<h3><img src='img/LOGO_AOA_200.png' border='0' height='30'> INFORME DE CITAS FECHA: $Fecha</h3>";
	if ($Citas = q("select c.*,t_oficina(c.oficina) as noficina,t_siniestro(c.siniestro) as nsiniestro,t_aseguradora(flota) as nflota,
			t_aseguradora(s.aseguradora) as naseguradora
		    from cita_servicio c,siniestro s where
			c.siniestro=s.id and c.fecha='$Fecha'  and c.estado='P'  " .
		($Oficina?" and c.oficina='$Oficina' ":" ").($Aseguradora?" and s.aseguradora=$Aseguradora":"")." order by noficina,c.hora"))
	{
		echo "<table border cellspacing=0 style='empty-cells:show' width='100%'><tr>
					<th>Numero</th>
					<th>Siniestro</th>
					<th>Aseguradora</th>
					<th>Hora programada</th>
					<th>Conductor</th>
					<th>Observaciones</th>
					<th>Vehículo</th>
					</tr>";
		$c_of = '';
		while ($C = mysql_fetch_object($Citas))
		{
			if ($c_of != $C->noficina)
			{
				echo "<tr><td colspan=10 style='font-size:16;font-weight:bold;background-color:000000;color:ffff00'>$C->noficina</td></tr>";
				$c_of = $C->noficina;
				$con_of = 1;
			}
			echo "<tr><td align='center'>$con_of</td><td>$C->nsiniestro</td><td>$C->naseguradora</td><td align='center'>".date('h:i A', strtotime($C->hora))."</td>
						<td>$C->conductor ".($C->dir_domicilio?"<br /><span style='background-color:ffff33;font-size:12px;'><B>DOMICILIO</B>: $C->dir_domicilio <br /><B>TELEFONO</B>: $C->tel_domicilio</span>":"")."</td>
						<td>".nl2br($C->observaciones)."</td><td align='center'>$C->placa</a></tr>";
		}
		echo "</table>";
	}
	echo "Oficina: $Oficina Aseguradora: $Aseguradora ";
}

function inserta_obs()
{
	global $id;
	html('OBSERVACIONES');
	echo "<script language='javascript'>
function carga()
{
	centrar(500,500);
}
</script>
<body onload='carga()'>
<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
<input type='hidden' name='Acc' id='Acc' value='inserta_obs_ok'>
Observaciones:<br />
<textarea name='observaciones' cols=80 rows=10 style='font-size:12'></textarea><br />
<br><input type='submit' value='GRABAR OBSERVACIONES'>
<input type='hidden' name='id' id='id' value='$id'>
</form>
</body>";
}

function inserta_obs_ok()
{
	global $id, $observaciones, $Nusuario, $Hoyl;

	q("update cita_servicio set observaciones=concat(observaciones,\"\n[$Nusuario $Hoyl] $observaciones\") where id=$id");
	echo "<script language='javascript'>
function carga()
{
	opener.location.reload();
	window.close();
	void(null);
}
</script>
<body onload='carga()'></body>";
}

function inserta_obsd()
{
	global $id;
	html('OBSERVACIONES');
	echo "<script language='javascript'>
function carga()
{
	centrar(500,500);
}
</script>
<body onload='carga()'>
<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
<input type='hidden' name='Acc' id='Acc' value='inserta_obsd_ok'>
Observaciones:<br />
<textarea name='observaciones' cols=80 rows=10 style='font-size:12'></textarea><br />
<br><input type='submit' value='GRABAR OBSERVACIONES'>
<input type='hidden' name='id' id='id' value='$id'>
</form>
</body>";
}

function inserta_obsd_ok()
{
	global $id, $observaciones, $Nusuario, $Hoyl;

	q("update cita_servicio set obs_devolucion=concat(obs_devolucion,\"\n[$Nusuario $Hoyl] $observaciones\") where id=$id");
	echo "<script language='javascript'>
function carga()
{
	opener.location.reload();
	window.close();
	void(null);
}
</script>
<body onload='carga()'></body>";
}

function completar_rcp()
{
	global $idr,$idc;
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
						alert('Debe escribir una dirección válida del cliente');
						direccion.style.backgroundColor='ffffdd';
						direccion.focus();
						return false;
					}
					if(confirm('Desea grabar la información e imprimir el recibo de caja provisional?'))
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
			<tr><td align='right'>Cliente:</td><td><b>$R->nombre</b></td><td align='right'>Identificación:</td><td><b>".coma_format($R->identificacion)."</b></td></tr>
			<tr><td align='right'>Oficina:</td><td><b>$Oficina->nombre</b></td><td align='right'>Aseguradora:</td><td><b>$Aseguradora->nombre</b></td></tr>
			<tr><td align='right'>Fecha Autorización Siniestro:</td><td><b>$Sin->fec_autorizacion</b></td><td align='right'>Valor:</td><td><b>$ ".coma_format($R->valor)."</b></td></tr>
			<tr><td align='right'>Dirección del Cliente:</td><td colspan=3><input type='text' name='direccion' size='50' maxlength='100'></td></tr>
			<tr><td align='right'>Recibido por:</td><td colspan=3><input type='text' name='recibido_por' value='$Nusuario' size='50' readonly></td></tr>
			<tr><td align='center' colspan=4><input type='button' value='Grabar e imprimir' onclick='validar_datos()' style='height:25px;width:200px;font-weight:bold'></td></tr>
			</table>
			<input type='hidden' name='Acc' value='completar_rcp_ok'>
			<input type='hidden' name='idr' value='$idr'>
			<input type='hidden' name='idc' value='$idc'>
		</form>

		</body>";
}

function completar_rcp_ok()
{
	global $idr,$idc,$direccion,$recibido_por;
	q("update recibo_caja_prov set direccion='$direccion', recibido_por='$recibido_por' where id=$idr");
	header("location:zcitas.php?Acc=imprimir_rcp&idr=$idr&idc=$idc");
}

function imprimir_rcp()
{
	global $idr,$idc;
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
	//	$P->Header_texto='';
	//	$P->Header_alineacion='L';
	//	$P->Header_alto='8';
	$P->SetTopMargin('5');
	//	$P->Header_colores=array(0,0,0,255,255,255,50,50,100); # rgb texto, rgb fondo, rgb borde
	//	$P->Header_imagen='img/cnota_entrada.jpg';
	///	$P->Header_posicion_imagen=array(20,5,80,14);
	$P->AddPage('P');
	$P->Image('../img/LOGO_AOA_200.jpg',45,5,30,12);
	$P->setfont('Arial','B',10);
	$P->SetXY(100,5);
	$P->SetTextColor(0,0,0);
	$P->Cell(90,5,'ADMISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
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
	$P->cell(80,4,"www.aoacolombia.com - Bogotá, D.C. - Colombia",0,0,'C');
	$P->rect(100,14,90,14);
	$P->setfont('Arial','',10);
	$P->setxy(20,30);
	$P->Cell(22,5,'Ciudad:',1,0,'L');
	$P->Cell(100,5,$R->noficina,1,0,'L');
	$P->Cell(20,10,'Fecha:',1,0,'C');
	$P->Cell(38,10,$R->fecha_recepcion,1,0,'C');
	$P->setxy(20,$P->y+5);
	$P->Cell(22,5,'Siniestro:',1,0,'L');
	$P->cell(100,5,$Sin->numero.' F.Autorización: '.$Sin->fec_autorizacion,1,0,'L');
	$P->setxy(20,$P->y+5);
	$P->Cell(22,5,'Recibido de:',1,0,'L');
	$P->Cell(108,5,trim($Cli->nombre),1,0,'L');
	$P->Cell(8,5,'Id:',1,0,'C');
	$P->Cell(42,5,coma_format($R->identificacion),1,0,'R',0);
	$P->setxy(20,$P->y+5);
	$P->Cell(22,5,'Dirección:',1,0,'L');
	$P->Cell(108,5,$R->direccion,1,0,'L');
	$P->Cell(8,5,'$',1,0,'C');
	$P->Cell(42,5,coma_format($R->valor),1,0,'R',1);
	$P->setxy(20,$P->y+5);
	$P->multicell(180,5,'En Letras: '.enletras($R->valor,1),1,'J',1);
	$P->setxy(20,$P->y);
	$P->multicell(180,5,'Por concepto de: Depósito en efectivo en garantía para el servicio de vehículo de reemplazo correspondiente al siniestro número: '.
	$Sin->numero.'. Vehículo que se entrega: '.$Cita->placa,1,'J');
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
	$P->Cell(90,5,'ADMISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
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
	$P->cell(80,4,"www.aoacolombia.com - Bogotá, D.C. - Colombia",0,0,'C');
	$P->rect(100,144,90,14);
	$P->setfont('Arial','',10);
	$P->setxy(20,160);
	$P->Cell(22,5,'Ciudad:',1,0,'L');
	$P->Cell(100,5,$R->noficina,1,0,'L');
	$P->Cell(20,10,'Fecha:',1,0,'C');
	$P->Cell(38,10,$R->fecha_recepcion,1,0,'C');
	$P->setxy(20,$P->y+5);
	$P->Cell(22,5,'Siniestro:',1,0,'L');
	$P->cell(100,5,$Sin->numero.' F.Autorización: '.$Sin->fec_autorizacion,1,0,'L');
	$P->setxy(20,$P->y+5);
	$P->Cell(22,5,'Recibido de:',1,0,'L');
	$P->Cell(108,5,trim($Cli->nombre),1,0,'L');
	$P->Cell(8,5,'Id:',1,0,'C');
	$P->Cell(42,5,coma_format($R->identificacion),1,0,'R',0);
	$P->setxy(20,$P->y+5);
	$P->Cell(22,5,'Dirección:',1,0,'L');
	$P->Cell(108,5,$R->direccion,1,0,'L');
	$P->Cell(8,5,'$',1,0,'C');
	$P->Cell(42,5,coma_format($R->valor),1,0,'R',1);
	$P->setxy(20,$P->y+5);
	$P->multicell(180,5,'En Letras: '.enletras($R->valor,1),1,'J',1);
	$P->setxy(20,$P->y);
	$P->multicell(180,5,'Por concepto de: Depósito en efectivo en garantía para el servicio de vehículo de reemplazo correspondiente al siniestro número: '.
		$Sin->numero.'. Vehículo que se entrega: '.$Cita->placa,1,'J');
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

function imprimir_acta()
{
	global $idc;
	$Cita=qo("select * from cita_servicio where id=$idc");
	$Fec_entrega = date('Y-m-d',strtotime(aumentadias($Cita->fecha, $Cita->dias_servicio)));
	$Siniestro=qo("select * from siniestro where id=$Cita->siniestro");
	$Vehiculo=qo("select * from vehiculo where placa='$Cita->placa' ");
	$Linea=qo("select * from linea_vehiculo where id=$Vehiculo->linea");
	$Autorizado_nombre='';$Autorizado_id=0;$Autortizado_direccion='';$Autorizado_celular='';$Autorizado_email='';
	if($Autorizacion=q("select *,t_franquisia_tarjeta(franquicia) as nfranq from sin_autor  where siniestro=$Siniestro->id and estado='A' "))
	{
		$Autorizaciones='';
		$Contador=1;
		$TH='Tarjeta Habiente(s): ';
		while($A=mysql_fetch_object($Autorizacion))
		{
			$Autorizaciones.="Aut $Contador: $A->nombre $A->identificacion $A->nfranq ".r($A->numero,4)." Vence: $A->vencimiento_mes - $A->vencimiento_ano. ";
			$TH.="$A->nombre /";
			if(!$Autorizado_nombre)
			{
				if($Cliente=qo("select * from cliente where identificacion=$A->identificacion"))
				{
					$Autorizado_nombre=$Cliente->nombre.' '.$Cliente->apellido;$Autorizado_id=$A->identificacion;
					$Autorizado_direccion=$Cliente->direccion;$Autorizado_celular=$Cliente->celular;$Autorizado_email=$Cliente->email_e;
				}
			}
		}
	}
	include('inc/pdf/fpdf.php');
	$P=new pdf('P','mm','Letter');
	$P->AddFont("c128a","","c128a.php");
	$P->AliasNbPages();
	$P->setTitle("ACTA DE ENTREGA/DEVOLUCION");
	$P->setAuthor("Arturo Quintero Rodriguez www.aoacolombia.com arturoquintero@aoacolombia.com");
	$P->Numeracion=false;
	$P->SetAutoPageBreak(false);
	$P->SetTopMargin('5');
	$P->AddPage('P');
	$P->Image('../img/LOGO_AOA_200.jpg',10,5,36,14);
	$P->setfont('Arial','B',20);
	$P->setxy(80,10);
	$P->Cell(110,5,'ACTA DE ENTREGA',0,0,'L');
	$P->setfont('Arial','',8);
	$P->setxy(10,20);$P->multicell(198,4,$TH,1,'L');
	$P->setfont('Arial','B',10);
	$P->setxy(10,24);$P->cell(30,6,'PLACA: ',1,0,'R');$P->cell(20,6,$Cita->placa,1);
	$P->cell(40,6,'NUMERO SINIESTRO: ',1,0,'R');$P->cell(108,6,$Siniestro->numero.' '.$Siniestro->asegurado_nombre,1,0,'L');
	$P->setxy(10,30);$P->cell(30,6,'AUTORIZADO:',1,0,'R');
	//$P->cell(120,6,$Cita->conductor,1,0,'L');
	$P->cell(120,6,$Autorizado_nombre,1,0,'L');
	$P->cell(10,6,'CC:',1,0,'R');$P->cell(38,6,$Autorizado_id,1,0,'L');
	$P->setxy(10,36);$P->cell(30,6,'DIRECCION:',1,0,'R');$P->cell(120,6,$Autorizado_direccion,1,0,'L');
	$P->cell(10,6,'TEL:',1,0,'R');$P->cell(38,6,$Autorizado_celular,1,0,'L');
	$P->setxy(10,42);$P->cell(30,6,'EMAIL:',1,0,'R');$P->cell(100,6,$Autorizado_email,1,0,'L');
	$P->setxy(10,50);$P->cell(40,6,' ',1,0,'L');$P->cell(40,6,'FECHA D/M/A',1,0,'C');$P->cell(20,6,'HORA',1,0,'C');$P->cell(30,6,'KM',1,0,'C');
	$P->setxy(10,56);$P->cell(40,6,'ENTREGA',1,0,'C');$P->cell(40,6,$Cita->fecha,1,0,'C');$P->cell(20,6,$Cita->hora,1,0,'C');$P->cell(30,6,' ',1,0,'C');
	//$P->celdas(array('V',' ',' ',' ',' ','M',' ',' ',' ',' ','F'),array(6,5,6,5,5,6,5,5,6,5,6),1,'C',0,6);
	$P->setxy(10,62);$P->cell(40,6,'DEVOLUCION',1,0,'C');$P->cell(40,6,' ',1,0,'C');$P->cell(20,6,' ',1,0,'C');$P->cell(30,6,' ',1,0,'C');
	//$P->celdas(array('V',' ',' ',' ',' ','M',' ',' ',' ',' ','F'),array(6,5,6,5,5,6,5,5,6,5,6),1,'C',0,6);
	$P->setxy(10,68);$P->cell(40,6,'TOTALES',1,0,'C');$P->cell(60,6,'KILOMETROS DEL SERVICIO:',1,0,'C');$P->cell(30,6,' ',1,0,'C');
	//$P->cell(17,6,'LLANTAS',1,0,'C');$P->celdas(array('25','50','75','100'),array(11,11,11,10),1,'C',0,6);
	$P->setxy(10,74);$P->cell(40,6,'ITEMS DE CHEQUEO',1,0,'C');$P->cell(20,6,'SALIDA',1,0,'C');$P->cell(20,6,'RETORNO',1,0,'C');
	$P->setfont('Arial','B',8);
	$P->setxy(10,80);$P->cell(40,5,'Exterior',1,0,'C');$P->cell(10,5,'SI',1,0,'C');$P->cell(10,5,'NO',1,0,'C');$P->cell(10,5,'SI',1,0,'C');$P->cell(10,5,'NO',1,0,'C');
	$P->setfont('Arial','',8);
	$P->setxy(190,16);$P->cell(20,4,"Dev: $Fec_entrega",0,0,'R');
	$P->setxy(10,85);$P->cell(40,5,'Emblemas (4)',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Copas (4)',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Antena de radio',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Limpia-parabrisas',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Niveles',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Luces altas y bajas',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Direccionales',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Luz de reversa,freno y placa',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Tapa de combustible',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setfont('Arial','B',8);
	$P->setxy(10,$P->y+5);$P->cell(80,5,'BAUL',1,0,'C');$P->cell(118,5,"X:falta   *:rayón   D:deterioro   O:golpe   R:roto   N:no funciona ",1,0,'C');
	$P->setfont('Arial','',8);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Gato y gancho de arrastre',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Cruceta',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(18,5,'Repuesto',1,0,'L');$P->cell(5,5,'25',1,0);$P->cell(5,5,'50',1,0);$P->cell(5,5,'75',1,0);$P->cell(7,5,'100',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Kit de carretera',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Paraguas',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Pernos',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);

	$P->setfont('Arial','B',8);
	$P->setxy(10,$P->y+5);$P->cell(80,5,'INTERIOR',1,0,'C');
	$P->setfont('Arial','',8);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Tapetes',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Cinturones de seguridad (5)',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Espejos laterales, retrovisor',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Luz de cortesía',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Radio',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Pito',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Bloqueo Central',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Elevavidrios delanteros',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Calefacción y A/A',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Encencedor y cenicero',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setfont('Arial','B',8);
	$P->setxy(10,$P->y+5);$P->cell(80,5,'DOCUMENTOS',1,0,'C');
	$P->setfont('Arial','',8);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Tarjeta de propiedad',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'SOAT vigente',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Tarjeta de Seguro',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Manuales y Garantía',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setxy(10,$P->y+5);$P->cell(40,5,'Certificado de Gases',1,0,'L');$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);$P->cell(10,5,' ',1,0);
	$P->setfont('Arial','B',8);
	$P->setxy(90,135);$P->cell(118,5,'ENTREGA',1,0,'C');
	$P->setfont('Arial','',7);
	$P->setxy(90,140);$P->multicell(118,3,"Observaciones de Entrega: ".($Cita->dir_domicilio?"DOMICILIO: $Cita->dir_domicilio TEL: $Cita->tel_domicilio | $Autorizaciones":""),0,'L');
	$P->setxy(90,140);$P->cell(118,35,' ',1,0,'L');
	$P->SetTextColor(200,200,200);
	$P->setfont('Arial','B',14);
	$P->setxy(90,175);$P->cell(60,17,'CLIENTE ',1,0,'C');$P->cell(58,17,'AOA ',1,0,'C');
	$P->SetTextColor(0,0,0);
	$P->setfont('Arial','B',8);
	$P->setxy(90,195);$P->cell(118,5,'DEVOLUCION',1,0,'C');
	$P->setfont('Arial','',7);
	$P->setxy(90,200);$P->cell(118,5,'Observaciones de Retorno:',0,0,'L');$P->setxy(90,200);$P->cell(118,32,' ',1,0,'L');
	$P->SetTextColor(200,200,200);
	$P->setfont('Arial','B',14);
	$P->setxy(90,232);$P->cell(60,18,'CLIENTE ',1,0,'C');$P->cell(58,18,'AOA ',1,0,'C');
	$P->SetTextColor(0,0,0);
	$P->setfont('Arial','B',8);
	$P->setxy(10,253);$P->multicell(198,4,"SI AL MOMENTO DE LA DEVOLUCIÓN DEL VEHÍCULO SE PRESENTA ALGÚN COBRO, USTED DEBE EXIGIR LA COPIA DE SU FACTURA Y/O RECIBO DE CAJA, DE LO CONTRARIO NO DEBE CANCELAR NINGÚN VALOR.",0,'C');
//	if($Cita->dir_domicilio)
//	{
//		$P->setfont('Arial','',7);
//		$P->setxy(10,261);$P->multicell(198,4,"DOMICILIO: $Cita->dir_domicilio TEL: $Cita->tel_domicilio | $Autorizaciones",0,'L');
//	}
	if($Linea->izquierda_f) $P->Image($Linea->izquierda_f,100,76,70,26);
	if($Linea->derecha_f) $P->Image($Linea->derecha_f,100,103,70,26);
	if($Linea->delante_f) $P->Image($Linea->delante_f,172,76,32,26);
	if($Linea->atras_f) $P->Image($Linea->atras_f,172,103,32,26);
	if($Linea->indicador_f) $P->Image($Linea->indicador_f,150,44,60,30);
	$P->Output($Archivo);
}

function insertar_domicilio()
{
	global $id;
	html('Crear Domicilio');
	echo "<script language='javascript'>
			function carga()
			{
				alert('Generación de Solicitud hecha satisfactoriamente');
			}
		</script>
		<body><script language='javascript'>centrar(500,300);</script>
		<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
			<h3>Creación de Domicilio</h3>
			<table align='center'>
			<tr><td align='ritght'>Dirección Domicilio</td><td><input type='text' name='dir_domicilio' size='70' maxlength='200'></td></tr>
			<tr><td align='ritght'>Teléfono Domicilio</td><td><input type='text' name='tel_domicilio' size='50' maxlength='50'></td></tr>
			<tr><td colspan='2' align='center'><input type='submit' value='Continuar'></td></tr></table>
			<input type='hidden' name='Acc' value='insertar_domicilio_ok'>
			<input type='hidden' name='id' value='$id'>
		</form>
		</body>";
}

function insertar_domicilio_ok()
{
	global $id,$dir_domicilio,$tel_domicilio;
	q("update cita_servicio set dir_domicilio='$dir_domicilio',tel_domicilio='$tel_domicilio' where id=$id ");
	graba_bitacora('cita_servicio','M',$id,'Asigna Domicilio');
	echo "<body><script language='javascript'>window.close();void(null);opener.location.reload();</script></body>";
}

function imprimir_contrato()
{
	global $idc;

}

function arribo_asegurado()
{
	global $id;
	$Ahora=date('Y-m-d H:i:s');
	q("update cita_servicio set arribo='$Ahora' where id=$id");
	graba_bitacora("cita_servicio","M",$id,"Marca arribo asegurado");
	$D=qo("select * from cita_servicio where id=$id");
	html();
	echo "<body><script language='javascript'>alert('Marcacion de Arribo del Asegurado Exitosa');
		modal('zingreso_recepcion.php?n=$D->conductor&m=VEHICULO DE REEMPLAZO&idcita=$id',0,0,600,600,'ingreso_recepcion');
		parent.Parar_recarga=false;parent.cambio_fecha();
		</script></body>";
}

function marcar_entregado()
{
	global $id;
	$Operario=$_SESSION['Id_alterno'];
	$Ahora=date('Y-m-d H:i:s');
	q("update cita_servicio set momento_entrega='$Ahora',operario=$Operario where id=$id");
	graba_bitacora("cita_servicio","M",$id,"Marca entrega vehiculo");
	echo "<body><script language='javascript'>alert('Marcacion de Entrega de Vehiculo Exitosa');parent.Parar_recarga=false;parent.cambio_fecha();
		</script></body>";
}

function formulario_entrega()
{
	global $idcita,$estado,$ultimokm;
	$C=qo("select * from cita_servicio where id=$idcita");
	if($estado=='C')
	{
		if(!$C->operario_domicilio)
		{
			echo "<body><script language='javascript'>alert('No ha seleccionado el operario que entrega');window.close();void(null);</script></body>";
			die();
		}
	}
	$Oficina=qo("select * from oficina where id=$C->oficina");
	html("Cita de Entrega del vehiculo $C->placa");
	if($estado=='C') // CUMPLIDA
	{
		echo "<script language='javascript'>
				function validarkm()
				{
					with(document.forma)
					{
						var Dato=Number(kmi.value);
						 ";
		if($C->dir_domicilio)
			echo "
						if(Dato==0) {alert('Debe escribir un kilometraje inicial valido mayor que el kilometraje antes del desplazamiento del domicilio.'); kmi.style.backgroundColor='ffff00';kmi.focus();continuar.style.visibility='hidden';return false;}
						if(Dato<=$ultimokm) {alert('Debe escribir un kilometraje inicial válido mayor o igual que el registrado antes del desplazamiento del domicilio'); kmi.style.backgroundColor='ffff00';kmi.focus();continuar.style.visibility='hidden';return false;}
						var Desplazamiento_domicilio=Dato-Number(kmd.value);
						if(Desplazamiento_domicilio>$Oficina->km_domicilio)
						{
							document.getElementById('desp_domi').innerHTML=\"<b>Desplazamiento Domcilio: <font color='red'>\"+Desplazamiento_domicilio+\"</font></b>\";
							alert('Al grabar este cumplimiento de servicio, se enviará un correo electrónico alertando al Director de Operaciones sobre un desplazamiento excesivo para efectos de auditoría y control');
						}
						else
							document.getElementById('desp_domi').innerHTML=\"<b>Desplazamiento Domcilio: \"+Desplazamiento_domicilio+\"</b>\";
						if(alltrim(observaciones.value) && Dato>$ultimokm) continuar.style.visibility='visible';";
		else
			echo "
						if(Dato==0) {alert('Debe escribir un kilometraje inicial valido'); kmi.style.backgroundColor='ffff00';kmi.focus();continuar.style.visibility='hidden';return false;}
						if(Dato<$ultimokm) {alert('Debe escribir un kilometraje inicial válido igual que el último registrado'); kmi.style.backgroundColor='ffff00';kmi.focus();continuar.style.visibility='hidden';return false;}
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
								alert('Debe escribir un kilometraje inicial válido igual que el último registrado.  No puede ser menor ni mayor que 2 kilometros mas del último registrado en la tabla de control.');
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
						if(Dato<0) {alert('Debe escribir un kilometraje inicial válido igual o mayor que el ultimo registrado'); kmd.style.backgroundColor='ffff00';kmd.focus();continuar.style.visibility='hidden';return false;}
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
								alert('Debe escribir un kilometraje inicial válido igual o mayor que el último registrado. No puede ser menor ni mayor que 2 kilometros mas del último registrado en la tabla de control.');
								kmd.style.backgroundColor='ffff00';kmd.focus();continuar.style.visibility='hidden';return false;
							}
						}
						if(Dato<$ultimokm) {alert('Debe escribir un kilometraje inicial válido mayor o igual que el último registrado. No puede ser menor al último estado en la tabla de control.');
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
						if(!alltrim(observaciones.value)) {alert('Debe digitar alguna observación');observaciones.style.backgroundColor='ffff00';observaciones.focus();continuar.style.visibility='hidden';return false;}
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
	if($C->dir_domicilio)
		echo "Kilometraje previo al desplazamiento del domicilio: <input type='text' class='numero' id='kmd' name='kmd' size='10' maxlength='10' onblur='validar_kmd();'>
				<input type='button' name='validarkmd' id='validarkmd' Value='Validar' onclick='validar_kmd();'><br /><br />
				<span id='dspq' style='visibility:hidden'>
					Transito en parqueadero <input type='text' class='numero' id='tpq' name='tpq' size='5' maxlength='5' readonly value='0'>
				</span><br /><br />Kilometraje inicial del Servicio: <input type='text' class='numero' id='kmi' name='kmi' size='10' maxlength='10' onblur='validarkm();'> <span id='desp_domi'></span><br /><br />
				";
	else
		echo "Kilometraje inicial del Servicio: <input type='text' class='numero' id='kmi' name='kmi' size='10' maxlength='10' onblur='validarkm();'>
					<input type='button' name='validarkmd' id='validarkmd' Value='Validar' onclick='validarkm();'><br /><br />
					<span id='dspq' style='visibility:hidden'>
					Transito en parqueadero <input type='text' class='numero' id='tpq' name='tpq' size='5' maxlength='5' readonly value='0'>
				</span>
				<br /><br />";
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
		echo capa('cp_sc',1,'Relative','');
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
		/// como el estado es X la causal es 10. cancelación post-adjudicación
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

function cambia_estado()
{
	global $id, $estado, $kmi, $observaciones, $estado_siniestro,$subcausal,$kmd,$tpq,$Nusuario;
	html('CAMBIO DE ESTADO');
	include('inc/link.php');
	if ($D = qom("select * from cita_servicio where id=$id",$LINK))
	{
		$Hora = date('H:i:s');
		$Email_usuario=usuario('email');
		$Fec_entrega = aumentadias($D->fecha, $D->dias_servicio);
		if($estado=='C') $estadod='P'; else $estadod='';  // REPROGRAMA EL ESTADO DE DEVOLUCION
		mysql_query("update cita_servicio set estado='$estado',estadod='$estadod',hora_llegada='$Hora',hora_devol=hora,fec_devolucion='$Fec_entrega' where id=$id",$LINK);
		$D = qom("select * from cita_servicio where id=$id",$LINK);
		$Sincars = qom("select * from siniestro where id=$D->siniestro",$LINK);
		$idv = qo1m("select id from vehiculo where placa='$D->placa'",$LINK);
		$Hoy = date('Y-m-d H:i:s');
		$Ahora = date('Y-m-d');
		$Oficina=qom("select * from oficina where id=$D->oficina",$LINK);

		if ($estado == 'C') // CUMPLIDO
		{
			// //////////////////                                   *******************                 CAMBIO DE ESTADO AUTOMATICO A SERVICIO                 ************************    ///////////////////////////
			// busca la ultima ubicacion para actualizar la fecha final con la fecha inicial del nuevo estado

			if ($Ultimo = qom("select * from ubicacion where vehiculo=$idv and fecha_final > '$D->fecha' and estado=2",$LINK))
			{
				if ($Ultimo->fecha_inicial == $D->fecha) // si la fecha inicial y final coinciden dentro del mismo dia del cambio del nuevo estado, se elimina ese estado
					mysql_query("delete from ubicacion where id=$Ultimo->id",$LINK);
				else
					mysql_query("update ubicacion set fecha_final='$D->fecha' where id=$Ultimo->id",$LINK);
			}
			// Inserta la nueva ubicación.

			if($tpq) // si hay recorido en el parqueadero
			{
				if($kmd) {$km1=$kmd-$tpq;$km2=$kmd;} else {$km1=$kmi-$tpq;$km2=$kmi;}
				mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) values
				('$D->oficina','$idv','$D->fecha','$D->fecha','94','$km1','$km2','$tpq',\"Domicilio de entrega\",'$Sincars->aseguradora')",$LINK);
				$IDU1 =mysql_insert_id($LINK);
				// inserta la bitacora de la ubicacion
				graba_bitacora('ubicacion','A',$IDU1,'Adiciona automáticamente en cumplimiento de entrega.',$LINK);
			}
			if($kmd) // si hay recorrido de domicilio
			{
				$Diferencia=$kmi-$kmd;
				mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) values
				('$D->oficina','$idv','$D->fecha','$D->fecha','96','$kmd','$kmi','$Diferencia','Domicilio de entrega $D->dir_domicilio','$Sincars->aseguradora')",$LINK);
				$IDU2 =mysql_insert_id($LINK);
				// inserta la bitacora de la ubicacion
				graba_bitacora('ubicacion','A',$IDU2,'Adiciona domicilio automáticamente',$LINK);
			}

			if(!$Sincars->ubicacion)
			{
				mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,obs_mantenimiento,flota) values
					('$D->oficina','$idv','$D->fecha','$Fec_entrega','1','$kmi','$kmi',\"$observaciones\",'$Sincars->aseguradora')",$LINK);
				$IDU3 =mysql_insert_id($LINK);
				// inserta la bitacora de la ubicacion
				graba_bitacora('ubicacion','A',$IDU3,'Inserta registro',$LINK);
				// Actualiza el siniestro
				if(!mysql_query("update siniestro set observaciones=concat(observaciones,\"\n$Nusuario [$Hoy] Asigna Servicio\"), ubicacion=$IDU3,estado=7,fecha_inicial='$D->fecha',
									fecha_final='$Fec_entrega',causal=0,subcausal=0 where id=$D->siniestro ",$LINK)) die(mysql_error());
				// Inserta la bitacora del siniestro
				graba_bitacora('siniestro','M',$D->siniestro,"Asigna Servicio",$LINK);

			}
			// //////////////////                                   *******************                 CAMBIO DE ESTADO AUTOMATICO A SERVICIO                 ************************    ///////////////////////////
			if($Diferencia>$Oficina->km_domicilio)
			{

				$Operario=qo1m("select concat(apellido,' ',nombre) from operario where id='$D->operario_domicilio' ",$LINK);
				enviar_gmail($Email_usuario /*de */ ,$Nusuario /*nombre de */ ,
				"arturoquintero@aoacolombia.com,ARTURO QUINTERO;gabrielsandoval@aoacolombia.com,GABRIEL SANDOVAL" /*para */ ,
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
			mysql_query("update siniestro set observaciones=concat(observaciones,'\n$Nusuario [$Hoy]: $Obs $D->observaciones'),estado='$estado_siniestro',
					causal='$Causal',subcausal='$subcausal' where id=$D->siniestro ",$LINK);
			// Inserta la bitacora del siniestro
			mysql_query("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
		        values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "','" . date('s') . "','" . $_SESSION['Nick'] . "','" . $_SESSION['Nombre'] . "','siniestro','M','$D->siniestro','" . $_SERVER['REMOTE_ADDR'] . "','Cancelacion post-adjudicacion.')",$LINK);
			mysql_query("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo)  values ('$D->siniestro','$Ahora','$Hora','$Nusuario','$Obs','$Tipo_seguimiento')",$LINK);
		}
		echo "<body onload='carga()'><script language='javascript'>alert('El estado fue cambiado'); opener.location.reload(); window.close(); void(null);</script></body>";
	}
	mysql_close($LINK);
}

function cambia_dias_servicio()
{
	global $id;
	$Dias=qo1("select dias_servicio from cita_servicio where id=$id");
	html('CAMBIO DE DIAS DE SERVICIO');
	echo "<body><form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
	  Cambiar los dias de servicio a: <select name='dias_servicio'>
	  <option value='1' ".($Dias==1?"selected":"").">1</option>
	  <option value='2' ".($Dias==2?"selected":"").">2</option>
	  <option value='3' ".($Dias==3?"selected":"").">3</option>
	  <option value='4' ".($Dias==4?"selected":"").">4</option>
	  <option value='5' ".($Dias==5?"selected":"").">5</option>
	  <option value='6' ".($Dias==6?"selected":"").">6</option>
	  <option value='7' ".($Dias==7?"selected":"").">7</option>
	  <option value='8' ".($Dias==8?"selected":"").">8</option>
	  <option value='9' ".($Dias==9?"selected":"").">9</option></select>
	  <br><br><input type='submit' value='CONTINUAR'>
	  <input type='hidden' name='id' value='$id'>
	  <input type='hidden' name='Acc' value='cambia_dias_servicio_ok'>
	  </form></body>";
}

function cambia_dias_servicio_ok()
{
	global $id,$dias_servicio;
	q("update cita_servicio set dias_servicio='$dias_servicio' where id=$id");
	graba_bitacora('cita_servicio','M',$id,"Cambia dias de servicio a $dias_servicio");
	echo "<body><script language='javascript'>window.close();void(null);opener.location.reload();</script></body>";
}

function solicitar_factura()
{
	global $cita;
	html('SOLICITUD DE FACTURA');
	$Cita=qo("select * from cita_servicio where id=$cita");
	$Sin=qo("select * from siniestro where id=$Cita->siniestro");
	echo "<script language='javascript'>
				function validar_formulario()
				{
					with(document.forma)
					{
						if(!alltrim(descripcion.value)) {alert('Debe especificar una descripción');descripcion.style.backgroundColor='ffffdd';descripcion.focus();return false;}
						if(!alltrim(forma_pago.value)) {alert('Debe seleccionar una forma de pago');forma_pago.style.backgroundColor='ffffdd';forma_pago.focus();return false;}
						Enviar.style.visibility='hidden';
					}
					document.forma.submit();
				}
		</script>
		<body><script language='javascript'>centrar(600,400);</script><h3>Solicitud de Facturación</h3>
		<form action='' method='post' target='_self' name='forma' id='forma'>
			Concepto a facturar: ".menu1("concepto","Select id,nombre from concepto_fac where activo_solicitud=1")."
			<br /><br />Escriba la descripción de lo que se necesita facturar:<br />
			<textarea name='descripcion' rows=4 cols=80 style='font-family:arial;font-size:12px'></textarea><br />
			<br />En caso de saberlo, escriba el valor que se debe factrurar:<br />
			<input type='text' class='numero' name='valor'><br /><br />
			Forma de pago: ".menu3("forma_pago","R,NUEVO PAGO EN EFECTIVO O T.DEBITO;T,NUEVO PAGO CON VOUCHER;G,PAGO CONTRA LA GARANTIA;A,ASEGURADORA ASUME EL PAGO",' ',1)."<br /><br />
			<input type='button' name='Enviar' id='Enviar' value='CONTINUAR' onclick='validar_formulario();'>
			<input type='hidden' name='Acc' value='solicitar_factura_ok'>
			<input type='hidden' name='siniestro' value='$Cita->siniestro'>
			<input type='hidden' name='cita' value='$cita'>
		</form>
		</body>";
}

function solicitar_factura_ok()
{
	global $siniestro,$concepto,$cita,$descripcion,$valor,$Nusuario,$Hoyl,$forma_pago;
	q("insert into solicitud_factura (siniestro,cita,concepto,solicitado_por,fecha_solicitud,descripcion,valor,forma_pago)
			values ('$siniestro','$cita','$concepto','$Nusuario','$Hoyl',\"$descripcion\",'$valor','$forma_pago')");
	echo "<body><script language='javascript'>alert('Solicitud grabada satisfactoriamente');window.close();void(null);</script>";
}

function generar_xml()
{
	global $Hoy;
	header("Content-type: text/xml");
	$Citas=q("select c.id,c.placa,s.numero,e.nombre as nest from cita_servicio c,siniestro s,sin_autor a,estado_citas e where c.siniestro=s.id and c.estado=e.codigo and s.id=a.siniestro and a.estado='A' and c.fecha='$Hoy' ");
	echo "<citas>";
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
}

function asigna_operario_entrega()
{
	global $operario,$cita,$Hoyl;
	q("update cita_servicio set operario_domicilio=$operario,arribo='$Hoyl' where id=$cita");
	graba_bitacora('cita_servicio','M',$cita,'Asigna operario para entrega.');
	echo "<body><script language='javascript'>alert('Operario Asignado Satisfactoriamente');</script>";
}

function insertar_domiciliod()
{
	global $id;
	html('Crear Domicilio');
	echo "<script language='javascript'>
		</script>
		<body><script language='javascript'>centrar(500,300);</script>
		<form action='zcitas.php' method='post' target='_self' name='forma' id='forma'>
			<h3>Creación de Domicilio de Devolución</h3>
			<table align='center'>
			<tr><td align='ritght'>Dirección Domicilio</td><td><input type='text' name='dir_domiciliod' size='70' maxlength='200'></td></tr>
			<tr><td align='ritght'>Teléfono Domicilio</td><td><input type='text' name='tel_domiciliod' size='50' maxlength='50'></td></tr>
			<tr><td colspan='2' align='center'><input type='submit' value='Continuar'></td></tr></table>
			<input type='hidden' name='Acc' value='insertar_domiciliod_ok'>
			<input type='hidden' name='id' value='$id'>
		</form>
		</body>";
}

function insertar_domiciliod_ok()
{
	global $id,$dir_domiciliod,$tel_domiciliod;
	q("update cita_servicio set dir_domiciliod='$dir_domiciliod',tel_domiciliod='$tel_domiciliod' where id=$id ");
	graba_bitacora('cita_servicio','M',$id,'Asigna Domicilio Devolución');
	echo "<body><script language='javascript'>window.close();void(null);opener.location.reload();</script></body>";
}

function asigna_operario_devolucion()
{
	global $operario,$cita,$Hoyl;
	q("update cita_servicio set operario_domiciliod=$operario where id=$cita");
	graba_bitacora('cita_servicio','M',$cita,'Asigna operario para devolución.');
	echo "<body><script language='javascript'>alert('Operario Asignado Satisfactoriamente');</script>";
}

function estado_operarios()
{
	global $Oficina,$Dia,$Modo;
	if($Oficina) $Ofi=qo("select * from oficina where id=$Oficina");
	else $Ofi->nombre='TODAS';
	html();
	echo "<script language='javascript' src='inc/chart/JSClass/FusionCharts.js'></script>";
	echo "<script language='javascript'>
			function cerrar() {parent.ocultar_estado_operarios();}
			function cambia_modo(dato) {window.open('zcitas.php?Acc=estado_operarios&Oficina=$Oficina&Dia=$Dia&Modo='+dato,'_self');}
		</script>
		<body bgcolor='ddddff'>
		<h4>Oficina: $Ofi->nombre $Modo ";
	if($Modo=='diario')
	{
		echo " $Dia <a onclick=\"cambia_modo('semanal')\" style='cursor:pointer'>Semanal</a> <a onclick=\"cambia_modo('mensual')\" style='cursor:pointer'>Mensual</a>";
	}
	if($Modo=='semanal')
	{
		$Diai=primer_dia_de_semana($Dia);$Diaf=date('Y-m-d',strtotime(aumentadias($Diai,6)));
		echo " $Diai - $Diaf <a onclick=\"cambia_modo('diario')\" style='cursor:pointer'>Diario</a>  <a onclick=\"cambia_modo('mensual')\" style='cursor:pointer'>Mensual</a>";
	}
	if($Modo=='mensual')
	{
		$Diai=primer_dia_de_mes($Dia);$Diaf=date('Y-m-',strtotime($Dia)).ultimo_dia_de_mes(date('Y',strtotime($Dia)),date('m',strtotime($Dia)));
		echo " $Diai - $Diaf <a onclick=\"cambia_modo('diario')\" style='cursor:pointer'>Diario</a> <a onclick=\"cambia_modo('semanal')\" style='cursor:pointer'>Semanal</a>";
	}


	echo "</h4>";
	$Entregas=q("select concat(op.nombre,' ',op.apellido) as noperario, count(cs.id) as cantidad
							FROM operario op, cita_servicio cs
							WHERE op.id=cs.operario_domicilio and op.inactivo=0 ".($Oficina?" and op.oficina=$Oficina ":"")." and ".
							($Modo=='diario'?"cs.fecha='$Dia' ":"").($Modo=='semanal' || $Modo=='mensual'?"cs.fecha between '$Diai' and '$Diaf' ":"")."
							GROUP BY noperario ORDER BY noperario");
	$Devoluciones=q("select concat(op.nombre,' ',op.apellido) as noperario, count(cs.id) as cantidad
							FROM operario op, cita_servicio cs
							WHERE op.id=cs.operario_domiciliod and op.inactivo=0 ".($Oficina?" and op.oficina=$Oficina ":"")." and ".
							($Modo=='diario'?"cs.fec_devolucion='$Dia' ":"").($Modo=='semanal' || $Modo=='mensual'?"cs.fec_devolucion between '$Diai' and '$Diaf' ":"")."
							GROUP BY noperario ORDER BY noperario");
	$xml1="<chart caption='ENTREGAS' xAxisName='Operarios' yAxisName='Operaciones' showValues='1' decimals='0' formatNumberScale='0' chartRightMargin='30'  logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' showLabels='1' >";
	$xml2="<chart caption='DEVOLUCIONES' xAxisName='Operarios' yAxisName='Operaciones' showValues='1' decimals='0' formatNumberScale='0' chartRightMargin='30'  logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' showLabels='1' >";
	$xml3="<chart caption='TOTAL' xAxisName='Operarios' yAxisName='Operaciones' showValues='1' decimals='0' formatNumberScale='0' chartRightMargin='30'  logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' showLabels='1' >";
	$Total=array();
	if($Entregas)
	while($Op=mysql_fetch_object($Entregas))
	{
		$xml1.="<set label='$Op->noperario' value='$Op->cantidad'/>";$Total[$Op->noperario]=$Op->cantidad;
	}
	$xml1.="</chart>";
	if($Devoluciones)
	while($Op=mysql_fetch_object($Devoluciones))
	{
		$xml2.="<set label='$Op->noperario' value='$Op->cantidad'/>";$Total[$Op->noperario]+=$Op->cantidad;
	}
	$xml2.="</chart>";
	foreach($Total as $Operario => $Cantidad)
	{
		$xml3.="<set label='$Operario' value='$Cantidad'/>";
	}
	$xml3.="</chart>";
	echo "<table align='center'><tr ><td >".renderChart("inc/chart/Charts/Bar2D.swf","",$xml1,"entregas",350,200,false,false)."</td>
				<td >".renderChart("inc/chart/Charts/Bar2D.swf","",$xml2,"devoluciones",350,200,false,false)."</td></tr>
				<tr ><td colspan=2>".renderChart("inc/chart/Charts/Bar2D.swf","",$xml3,"total",704,200,false,false)."</td></tr></table>";
	echo "<br /><br />
		<center><input type='button' value=' CERRAR ESTA VENTANA ' onclick='cerrar()' style='font-size:14;font-weight:bold'></center>
		</body>";
}

function formulario_devolucion()
{
	global $idcita,$USUARIO;
	$C=qo("select * from cita_servicio where id=$idcita");  // se halla la información de la cita
	if(!$C->operario_domiciliod)
	{
		echo "<body><script language='javascript'>alert('No ha seleccionado el operario que recibe');window.close();void(null);</script></body>";
		die();
	}
	$Oficina=qo("select * from oficina where id=$C->oficina");
	$Sin=qo("select aseguradora from siniestro where id=$C->siniestro");
	$Aseg=qo("select limite_kilometraje from aseguradora where id=$Sin->aseguradora");
	$UB=qo("select u.* from ubicacion u,siniestro s where u.id=s.ubicacion and s.id=$C->siniestro"); // se halla la ubicacion
	html('FORMULARIO DE DEVOLUCION');
	echo "<script language='javascript'>
			function valida_km_final()
			{
				var dato=document.forma.kmf.value;
				if(!Number(dato)) {alert('Debe escribir un valor numerico sin comas ni puntos');document.forma.kmf.style.backgroundColor='ffff55';document.forma.kmf.focus();return false;}
			}

			function valida_km_final2()
			{
				var dato=document.forma.kmf.value;
				if(!Number(dato)) {alert('Debe escribir un valor numerico sin comas ni puntos');document.forma.kmf.style.backgroundColor='ffff55';document.forma.kmf.focus();return false;}
				if(Number(dato)<=0)
				{ alert('Debe escribir un odometro valido'); document.forma.kmf.style.backgroundColor='ffff55';document.forma.kmf.focus(); return false;}
				if(Number(dato)<=$UB->odometro_inicial)
				{ alert('Debe escribir un odometro mayor que el odómetro inicial');document.forma.kmf.style.backgroundColor='ffff55';document.forma.kmf.focus();return false;}
				var consumo=Number(dato)-$UB->odometro_inicial;
				if(consumo>$Aseg->limite_kilometraje)
				{	document.getElementById('consumo').innerHTML=\"<b>Consumo: <font color='red'>\"+consumo+\"</font></b>\";
					alert('Se informará via correo electrónico al Director de Operaciones sobre este exceso en el kilometraje de este servicio');
				}
				else
					document.getElementById('consumo').innerHTML='<b>Consumo: '+consumo+'</b>';
				document.forma.kmf.readonly=true;
				document.forma.kmf.style.backgroundColor='eeffee'; ";
	if($C->dir_domiciliod)
		echo "document.forma.kmd.style.visibility='visible';document.forma.kmd.focus();";
	else
		echo "document.forma.obs.style.visibility='visible';document.forma.obs.focus();";
	echo "
			}

			function valida_obs()
			{
				var dato=document.forma.obs.value;
				if(!alltrim(dato)) {alert('Debe escribir la observación');document.forma.obs.style.backgroundColor='ffff55';document.forma.obs.focus();return false;}
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
				if(document.forma.Nuevo_estado.value==0) {alert('Debe primero seleccionar el nuevo estado en que quedará el vehículo');  document.forma.Nuevo_estado.style.backgroundColor='ffffdd';document.forma.Nuevo_estado.focus();return false;}
			}
			function valida_ords()
			{
				var dato=document.forma.ords.value;
				if(!alltrim(dato)) {alert('Debe digitar la observación para el nuevo estado');document.forma.ords.style.backgroundColor='ffffdd';document.forma.ords.focus();return false;}
				document.forma.Siniestro_propio.style.visibility='visible';
				document.forma.gr.style.visibility='visible';
			}
			function cerrar()
			{window.close();void(null);opener.location.reload();}

			function valida_km_final3()
			{
				with(document.forma)
				{
					if(!Number(kmd.value)) {alert('Debe escribir un kilometraje válido mayor o igual que el último registrado, sin comas ni puntos');kmd.style.backgroundColor='ffffdd';kmd.focus();return false;}
					if(Number(kmd.value)<Number(kmf.value)) {alert('Debe escribir un kilometraje válido mayor o igual que el último registrtado.');kmd.style.backgroundColor='ffffdd';kmd.focus();return false;}
					var Desplazamiento_domicilio=Number(kmd.value)-Number(kmf.value);
					if(Desplazamiento_domicilio>$Oficina->km_domicilio)
					{
						document.getElementById('consumod').innerHTML=\"<b>Desplazamiento Domicilio: <font color='red'>\"+Desplazamiento_domicilio+\"</font><b>\";
						alert('Al grabar este cumplimiento de devolución, se enviará un correo electrónico alertando al Director de Operaciones sobre un desplazamiento excesivo para efectos de auditoría y control');
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
				Kilometraje Final Servicio: <input type='text' class='numero' id='kmf' name='kmf' size='10' maxlength='10' onkeyup='valida_km_final();' onblur='valida_km_final2();'> <span id='consumo'></span>  <br /><br />";
	if($C->dir_domiciliod)
	{
		echo "Kilometraje al retorno del domicilio: <input type='text' class='numero' id='kmd' name='kmd' size='10' maxlength='10' onblur='valida_km_final3();'>
					<input type='button' onclick='validar_km_final3()' value=''><span id='consumod'></span><br /><br />";
	}
	echo "
				Observaciones:<br />
				<textarea name='obs' style='font-size:11px;font-family:arial;visibility:".($C->dir_domiciliod?"hidden":"visible")."' cols=60 rows=3 onkeyup='valida_obs();'></textarea><br />
				Estado en que quedará el Vehículo:".
				menu1('Nuevo_estado',"select id,nombre from estado_vehiculo where id in (5,8,92)",0,1,'visibility:hidden;'," onchange='valida_nuevo_estado();' ")."<br />
				Descripción:<br />
				<textarea name='ords' id='ords' cols=60 rows=3 style='font-size:11px;font-family:arial;visibility:hidden;' onfocus='prevalida_ords()' onblur='valida_ords()'></textarea><br />
				 <b>Siniestro Asegurado</b> <input type='checkbox' name='Siniestro_propio' style='visibility:hidden'><br />
				<input type='hidden' name='id' id='id' value='$idcita'>
				<input type='button' id='gr' name='gr' value='Grabar Devolución' style='visibility:hidden;width:300px;height:60px;' onclick='enviar_frm();'>
			</form><script language='javascript'>document.forma.kmf.focus();</script>
			<iframe name='Oculto_devolucion' id='Oculto_devolucion' height='1' width='1' style='visibility:hidden'></iframe>
		</body>";
}

function cambia_estadod()
{
	global $id, $estadod, $kmf, $obs, $ords,$Nuevo_estado,$Siniestro_propio,$kmd,$Nusuario;
	$Siniestro_propio=sino($Siniestro_propio);
	$Email_usuario=usuario('email');
	include('inc/link.php');
	if ($D = qom("select * from cita_servicio where id=$id",$LINK))
	{
		$Hora = date('H:i:s');
		$Fecha = date('Y-m-d');
		$Oficina=qom("select * from oficina where id=$D->oficina",$LINK);
		mysql_query("update cita_servicio set estadod='C',hora_devol_real='$Hora',fec_devolucion='$Fecha',obs_devolucion=concat(obs_devolucion,\"$obs - $ords\") where id=$id",$LINK);
		$Sincars = qom("select * from siniestro where id=$D->siniestro",$LINK);
		$Aseg=qom("select * from aseguradora where id=$Sincars->aseguradora",$LINK);
		$idv = qo1m("select id from vehiculo where placa='$D->placa'",$LINK);
		mysql_query("update siniestro set fecha_final='$Fecha', estado=8,obsconclusion=\"$obs\",siniestro_propio='$Siniestro_propio' where id=$D->siniestro",$LINK);
		graba_bitacora('siniestro','M',$D->siniestro,'Fecha final,estado,obsconclusion,siniestro_propio',$LINK);
		$Ubicacion=qom("select * from ubicacion where id=$Sincars->ubicacion",$LINK);
		$Consumo=$kmf-$Ubicacion->odometro_inicial;
		mysql_query("update ubicacion set fecha_final='$Fecha', odometro_final='$kmf', odometro_diferencia=odometro_final-odometro_inicial,obs_mantenimiento=\"$obs\",
		observaciones=\"$ords\",estado=7 where id=$Sincars->ubicacion",$LINK);
		graba_bitacora('ubicacion','M',$Ubicacion->id,'Concluye el servicio',$LINK);
		if($Consumo>$Aseg->limite_kilometraje)
		{
			enviar_gmail($Email_usuario /*de */ ,$Nusuario /*nombre de */ ,
			"arturoquintero@aoacolombia.com,ARTURO QUINTERO;gabrielsandoval@aoacolombia.com,GABRIEL SANDOVAL" /*para */ ,
			""   /*Con copia*/ ,
			"Exceso consumo de kilometraje en servicio $D->placa $Oficina->nombre"  /*OBJETO*/,
			"<body>Ocurrio un exceso en consumo de kilometraje en el servicio<br><br> ".
			"Placa: $D->placa <br>Oficina: $Oficina->nombre<br>Fecha y hora de la cita: $D->fecha $D->hora<br>".
			"Numero Siniestro: $Sincars->numero $Sincars->asegurado_nombre <br>".
			"Kilometraje maximo permitido:  $Aseg->limite_kilometraje.  Kilometraje consumido: ".coma_format($Consumo)." </body>" /*mensaje */);
		}
		/////  ACTUALIZA LAS UBICACIONES POSTERIORES A ESTE CIERRE
		mysql_query("update ubicacion set odometro_inicial='$kmf', odometro_final='$kmf', odometro_diferencia=0 where vehiculo='$idv' and fecha_inicial>='$Fecha' ",$LINK);
		if($kmd)   /// si hubo consumo de kilometraje por domicilio
		{
			$Estado_domicilio=96;
			$Diferencia=$kmd-$kmf;
			mysql_query("insert into ubicacion (oficina,vehiculo,estado,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones,flota) values
				('$D->oficina','$idv','$Estado_domicilio','$Fecha','$Fecha','$kmf','$kmd','$Diferencia','Domicilio de devolución $D->dir_domiciliod',$D->flota) ",$LINK);
			if($Diferencia>$Oficina->km_domicilio)
			{

				$Operario=qo1m("select concat(apellido,' ',nombre) from operario where id='$D->operario_domiciliod' ",$LINK);
				enviar_gmail($Email_usuario /*de */ ,$Nusuario /*nombre de */ ,
				"arturoquintero@aoacolombia.com,ARTURO QUINTERO;gabrielsandoval@aoacolombia.com,GABRIEL SANDOVAL" /*para */ ,
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
			if($Nuevo_estado==5 /*fuera de servicio*/)
			{
				mysql_query("insert into ubicacion (oficina,vehiculo,estado,fecha_inicial,fecha_final,odometro_inicial,odometro_final,observaciones,flota,siniestro_propio) values
				('$D->oficina','$idv','$Nuevo_estado','$Fecha','$Fecha','$kmf','$kmf','$ords',$D->flota,'$Siniestro_propio')",$LINK);
				$UB1 =mysql_insert_id($LINK);
				if($Siniestro_propio) mysql_query("update siniestro set siniestro_propio=1 where id=$D->siniestro",$LINK);
			}
			else
			{
				$UB1 = q("insert into ubicacion (oficina,vehiculo,estado,fecha_inicial,fecha_final,odometro_inicial,odometro_final,observaciones,flota) values
				('$D->oficina','$idv','$Nuevo_estado','$Fecha','$Fecha','$kmf','$kmf','$ords',$D->flota)",$LINK);
				$UB1 =mysql_insert_id($LINK);
			}
			graba_bitacora('ubicacion','A',$UB1,'',$LINK);
		}
		mysql_close($LINK);
		echo "<body><script language='javascript'>alert('El estado fue cambiado');parent.cerrar();</script></body>";
	}
}


?>