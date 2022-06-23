<?php
/* MODULO DE CALL CENTER VERSION 2
		FEBRERO 25 DE 2012
	*/

include('inc/funciones_.php');
require('festivosColombia.php');
if ($Acc != "watchHtmlEmail") {
	sesion();
} else {
	error_reporting(E_ALL);
	watchHtmlEmail();
	exit;
}


$USUARIO = $_SESSION['User'];
$tokenUser = $_SESSION['User'];
$tokenPeril = $_SESSION['Peril'];
$tokenNick = $_SESSION['Nick'];
$NUSUARIO = $_SESSION['Nombre'];
$IDUSUARIO = $_SESSION['Id_alterno'];
$NTsin = tu('siniestro', 'id');
$NTusu = tu('usuario_callcenter', 'id');
$NTap = tu('vehiculo_apartado', 'id');
$Fecha_de_arranque = '2013-04-09';
$A_accion_agentes = ',';

if (!empty($Acc) && function_exists($Acc)) {
	eval($Acc . '();');
	die();
}

iniciar_callcenter2();

function iniciar_callcenter2()
{
	global $USUARIO, $NUSUARIO, $NTusu, $NTsin, $NTap, $tokenNick, $IDUSUARIO;
	html('MODULO CALL CENTER 2');
	$Perfil = qo("select * from usuario where id=" . $USUARIO);
	echo "
	<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
	<script language='javascript'>
	var Tomando=false;
	var A_agentes='';
	function tomar_nuevo()
	{
		if(!Tomando)
		{
			document.getElementById('boton_rojo').src='img/boton_rojo_undido.png';
			Tomando=true;
			document.getElementById('mensaje').innerHTML='Buscando Nuevo Caso';
			window.open('zcallcenter2.php?Acc=tomar_nuevo_caso','Oculto_perfil_agente');
		}
		else {alert('En este momento el sistema est&aacute; buscando un caso nuevo para ser tomado');}
	}
	function cargar_caso(id)
	{
		
		window.open('zcallcenter2.php?Acc=presentar_caso&id='+id,'_self');
		
	}

	function buscar_caso()
	{
		with(document.forma)
		{
			if(placa.value || siniestro.value)
			{
				document.forma.submit();
			}
			else
			{
				alert('Debe escribir la placa o el siniestro para hacer la busqueda');
				placa.style.backgroundColor='ffff00';
				siniestro.style.backgroundColor='ffff00';
			}
		}
	}
	function mod_agente(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTusu&id='+id,0,0,600,800);}
	function mod_sin(id) { modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTsin&id='+id,0,0,600,800); }
	function mod_sin2(id) { modal('zsiniestro.php?Acc=buscar_siniestro&id='+id,0,0,600,800); }
	function ver_apartados() {modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$NTap',0,0,600,800,'vap');}
	function re_comenzar() {window.open('zcallcenter2.php','_self');}
	function cerrar_caso(idc) {if(confirm('Desea cerrar el caso abierto? '+idc)) window.open('zcallcenter2.php?Acc=cerrar_caso_abierto&idc='+idc,'Oculto_perfil_admin');}
	function recarga_accion() {window.open('zcallcenter2.php?Acc=ver_accion_agentes&ag='+A_agentes,'Oculto_perfil_admin_recarga');}
    
	function tomar_renta(placa) {document.forma.placa.value=placa;}
        
       function flota_autorizar(id) { modal('zcallcenter2.php?Acc=flota_autorizacion&id='+id,0,0,500,600); }
	   function solictud_reactivacion(id) { modal('zcallcenter2.php?Acc=solictud_reactivacion&id='+id,0,0,500,600); }
	   function solictud_reactivacion_cancelar(id) { modal('zcallcenter2.php?Acc=solictud_reactivacion_cancelar&id='+id,0,0,500,600); }
	   function correo_autorizar(id) { modal('zcallcenter2.php?Acc=correo_autorizar&id='+id,0,0,500,600); }
	   function modalcorreosautorizado(id,idcorreo){
		   console.log(id);
			if(id != 0){
				$.ajax({
				url:'https://pot.aoacolombia.com/api/actualizarvisto?id='+idcorreo+'&siniestroid='+id+'&agenteid=$IDUSUARIO',
				type: 'post',
				beforeSend: function () {
				},
				success: function (response) {
					window.location.href='zcallcenter2.php?Acc=presentar_caso&id='+id;
				}
			});
		}
		
	   }

	   function correo_autorizar_anulado(nada,id){
		modal('zcallcenter2.php?Acc=correo_autorizar_anulado&id='+id,0,0,500,600);
		$.ajax({
			url:'https://pot.aoacolombia.com/api/actualizarvisto?id='+id+'&siniestroid=0',
			type: 'post',
			beforeSend: function () {

			},
			success: function (response) {

			}
		});
	}
	</script>
	<style tyle='text/css'>
		td.agente {font-size:9px;}
	</style>
	<body bgcolor='ffffff'><h3>AOA COLOMBIA S.A.S  MODULO DE CALL CENTER 2 .:. Perfil: $Perfil->nombre Usuario: $NUSUARIO</h3>";
	if (inlist($Perfil->id, '1,2,26')) modulo_perfil_administrador();
	if (inlist($Perfil->id, '4')) modulo_perfil_agente();
	echo "</body>";
}

function get_nombre_dia($fecha)
{
	$fechats = strtotime($fecha); //pasamos a timestamp

	//el parametro w en la funcion date indica que queremos el dia de la semana
	//lo devuelve en numero 0 domingo, 1 lunes,....
	switch (date('w', $fechats)) {
		case 0:
			return "Domingo";
			break;
		case 1:
			return "Lunes";
			break;
		case 2:
			return "Martes";
			break;
		case 3:
			return "Miercoles";
			break;
		case 4:
			return "Jueves";
			break;
		case 5:
			return "Viernes";
			break;
		case 6:
			return "Sabado";
			break;
	}
}


function validationDays($deliveryDate, $Cita, $Oficina)
{

	$festivos = new Festivos();
	$arrayDate = array();

	for ($i = 0; $i < 5; $i++) {

		$fechaIncrementa = date("Y-m-d", strtotime($deliveryDate . "+ " . $i . " days")) . ' ' . $Cita->hora;

		array_push($arrayDate, $fechaIncrementa);
	}

	for ($y = 0; $y < count($arrayDate); $y++) {

		$dayName = get_nombre_dia($arrayDate[$y]);
		$DaNew = strtotime($arrayDate[$y]);
		$dayD =  date('Y-m-d', $DaNew);
		$dayCalculation = $festivos->esFestivo(date('d', $DaNew), date('m', $DaNew));
		$hora = date('h:i:s', strtotime($arrayDate[$y]));

		if ($dayCalculation) {
			if ($Oficina->hora_final_festivo) {
				if ($Oficina->hora_final_festivo >= $hora) {
					return $dateDay = array("date" => $dayD, "day" => $dayName, "hora" => $Oficina->hora_final_festivo);
					exit();
				}
			}
		}

		if (!$dayCalculation) {
			if ($dayName == "Sabado") {
				if ($Oficina->hora_final_sabado) {

					if ($Oficina->hora_final_sabado >= $hora) {
						return $dateDay = array("date" => $dayD, "day" => $dayName, "hora" => $Oficina->hora_inicial_sabado);
						exit();
					}
				}
			}
			if ($dayName == "Domingo") {
				if ($Oficina->hora_final_domingo) {
					if ($Oficina->hora_final_domingo >= $hora) {
						return $dateDay = array("date" => $dayD, "day" => $dayName, "hora" => $Oficina->hora_inicial_domingo);
						exit();
					}
				}
			}

			if ($dayName != "Domingo" or $dayName != "Sabado") {
				return $dateDay = array("date" => $dayD, "day" => $dayName, "hora" => $Oficina->hora_inicial);
				exit();
			}
		}
	}
}

function modulo_perfil_administrador()
{
	global $A_accion_agentes, $tokenNick, $USUARIO;
	echo "<center><b style='font-size:30px'>Control de Niveles</b></center>
	<!-- <a onclick='re_comenzar();' class='info'><img src='gifs/standar/Refresh.png' border='0'><span>Refrescar</span></a> -->

	<table align='center' bgcolor='dddddd'>
		<tr><th>Nivel 1</th><th>Nivel 2</th><th>Nivel 3</th><th>Nivel 4</th></tr>
		<tr><td valign='top' bgcolor='ffffff'>
			<iframe name='cola1' id='cola1' style='visibility:visible' width='100%' height='500px' src='zcallcenter2.php?Acc=pinta_cola1' frameborder='no'></iframe>
			<br>" . admin_nivel(1) . "</td>
			<td valign='top' bgcolor='ffffff'>
			<iframe name='cola2' id='cola2' style='visibility:visible' width='100%' height='500px' src='zcallcenter2.php?Acc=pinta_cola2' frameborder='no'></iframe>
			" . admin_nivel(3) . "
			<iframe name='cola2a' id='cola2a' style='visibility:visible' width='100%' height='300px' src='zcallcenter2.php?Acc=pinta_cola2a' frameborder='no'></iframe>
			</td><td valign='top' bgcolor='ffffff'>
			" . admin_nivel(2) . "
			<iframe name='cola3' id='cola3' style='visibility:visible' width='100%' height='250px' src='zcallcenter2.php?Acc=pinta_cola3' frameborder='no'></iframe>
			</td>
			<td valign='top' bgcolor='ffffff'>" . admin_nivel(4) . " <br><br> <div id='mostrar_solicitud'>" . solicitudes_flota() . "</div><br><br><a style='cursor:pointer;font-size:16px' onclick=\"modal('http://ctc.aoacolombia.com/auth1/$tokenNick/no/$USUARIO',0,900,900,900,'correodirecto');\"><img src='img/icono_correos_call.png' border='0' height='60'><br></a><div id='mostrar_correo'></div><br><div id='mostrar_reactivacion'></div><br></td>
	</table>
	<a style='cursor:pointer' onclick=\"modal('zcall2estadistica.php',0,0,100,100,'call2estadistica');\">Modulo de Estadisticas</a>
	<a onclick='recarga_accion();'>.</a>
	<iframe name='Oculto_perfil_admin' id='Oculto_perfil_admin' style='visibility:hidden' width='1' height='1'></iframe>
	<iframe name='Oculto_perfil_admin_recarga' id='Oculto_perfil_admin_recarga' style='visibility:hidden' width='1' height='1'></iframe>
	<script language='javascript'>A_agentes='$A_accion_agentes';recarga_accion();</script>";
}

function ver_accion_agentes()
{
	global $ag, $USUARIO;
	if ($Abiertos = q("select id,fecha,agente,siniestro from call2proceso where estado='A' order by agente")) {
		echo "<script language='javascript'>
		var Recargar=setTimeout(recarga,10000);
		function recarga() {parent.recarga_accion();}
		</script><body><script language='javascript'>";
		$Ahora = date('Y-m-d H:i:s');
		while ($A = mysql_fetch_object($Abiertos)) {
			$Tiempo = segundos2tiempo(segundos($A->fecha, $Ahora));
			$Resultado = "<a class='info' onclick='mod_sin2($A->siniestro);'><img src='gifs/standar/Next.png' height='12px' border='0'><span>Ver Siniestro</span></a> $Tiempo";
			if (inlist($USUARIO, '1,26')) $Resultado .= " <a class='info' onclick='cerrar_caso($A->id);'><img src='gifs/standar/Cancel.png' height='12px' border='0'><span>Cerrar Caso</span></a>";
			echo "if(parent.document.getElementById('td_ag_$A->agente'))
			parent.document.getElementById('td_ag_$A->agente').innerHTML=\"$Resultado\";
			else alert('No se encuentra el agente $A->agente');
			";

			$ag = str_replace(",$A->agente,", ",", $ag);
		}
		$Agentes = explode(',', $ag);
		for ($i = 0; $i < count($Agentes); $i++) {
			$ida = $Agentes[$i];
			if ($ida) echo "if(parent.document.getElementById('td_ag_$ida'))
			parent.document.getElementById('td_ag_$ida').innerHTML='&nbsp;';
			else alert('No se encuentra el agente $ida');
			";
		}

		$Resultado3 = '';
		if ($query = q("select * from cambio_flota_solicitud where estado=0")) {
			while ($Ag = mysql_fetch_object($query)) {
				$Resultado3 .= "<tr><td class='agente'>$Ag->solicitado_por </td><td class='agente'>$Ag->justificacion</td><td class='agente' align='left' nowrap='yes'><a style='cursor:pointer' onclick='flota_autorizar($Ag->id);'>[+]</a></td></tr>";
			}
		}
		$Resultado6 = '';
		if ($query = q("select * from solicitud_siniestro_reactivacion where estado=0")) {
			while ($Ag = mysql_fetch_object($query)) {
				$Resultado6 .= "<tr><td class='agente'>$Ag->solicitado_por </td><td class='agente'>$Ag->siniestro</td><td class='agente' align='left' nowrap='yes'><a style='cursor:pointer'  onclick='solictud_reactivacion($Ag->siniestro);'>[+]</a></td><td class='agente' align='left' nowrap='yes'><a style='cursor:pointer'  onclick='solictud_reactivacion_cancelar($Ag->siniestro);'>[---]</a></td></tr>";
			}
		}
		$Resultado4 = '';
		if ($query = q("select * from correo where siniestro=0 AND ISNULL(respuesta) AND pdf=0")) {
			while ($Ag = mysql_fetch_object($query)) {
				$Resultado4 .= "<tr><td class='agente'>$Ag->asunto </td><td class='agente'>" . strip_tags($Ag->cuerpo_mensaje) . "</td><td class='agente' align='left' nowrap='yes'><a style='cursor:pointer' onclick='correo_autorizar($Ag->id);'>[+]</a></td></tr>";
			}
		}
		$Resultado21 = "<table border cellspacing='0' width='100%'><tr><th>CORREOS PENDIENTES</th><th>JUSTIFICACION</th><th></th></tr>$Resultado4</table>";
		echo "parent.document.getElementById('mostrar_correo').innerHTML=\"$Resultado21\";</script>$ag</body>";
		$Resultado2 = "<table border cellspacing='0' width='100%'><tr><th>SOLICITADO POR</th><th>JUSTIFICACION</th><th></th></tr>$Resultado3</table>";
		echo "parent.document.getElementById('mostrar_solicitud').innerHTML=\"$Resultado2\";</script>$ag</body>";
		$Resultado31 = "<table border cellspacing='0' width='100%'><tr><th>REACTIVACION</th><th>SINIESTRO</th><th>ACEPTAR</th><th>ELIMINAR</th></tr>$Resultado6</table>";
		echo "<script>parent.document.getElementById('mostrar_reactivacion').innerHTML=\"$Resultado31\";</script>$ag</body>";
	}
	else{
		echo "<script language='javascript'>
		var Recargar=setTimeout(recarga,10000);
		function recarga() {parent.recarga_accion();}
		</script><body><script language='javascript'>";
		$Resultado3 = '';
		if ($query = q("select * from cambio_flota_solicitud where estado=0")) {
			while ($Ag = mysql_fetch_object($query)) {
				$Resultado3 .= "<tr><td class='agente'>$Ag->solicitado_por </td><td class='agente'>$Ag->justificacion</td><td class='agente' align='left' nowrap='yes'><a style='cursor:pointer' onclick='flota_autorizar($Ag->id);'>[+]</a></td></tr>";
			}
		}
		$Resultado6 = '';
		if ($query = q("select * from solicitud_siniestro_reactivacion where estado=0")) {
			while ($Ag = mysql_fetch_object($query)) {
				$Resultado6 .= "<tr><td class='agente'>$Ag->solicitado_por </td><td class='agente'>$Ag->siniestro</td><td class='agente' align='left' nowrap='yes'><a style='cursor:pointer'  onclick='solictud_reactivacion($Ag->siniestro);'>[+]</a></td><td class='agente' align='left' nowrap='yes'><a style='cursor:pointer'  onclick='solictud_reactivacion_cancelar($Ag->siniestro);'>[---]</a></td></tr>";
			}
		}
		$Resultado4 = '';
		if ($query = q("select * from correo where siniestro=0 AND ISNULL(respuesta) AND pdf=0")) {
			while ($Ag = mysql_fetch_object($query)) {
				$Resultado4 .= "<tr><td class='agente'>$Ag->asunto </td><td class='agente'>" . strip_tags($Ag->cuerpo_mensaje) . "</td><td class='agente' align='left' nowrap='yes'><a style='cursor:pointer' onclick='correo_autorizar($Ag->id);'>[+]</a></td></tr>";
			}
		}
		$Resultado21 = "<table border cellspacing='0' width='100%'><tr><th>CORREOS PENDIENTES</th><th>JUSTIFICACION</th><th></th></tr>$Resultado4</table>";
		echo "parent.document.getElementById('mostrar_correo').innerHTML=\"$Resultado21\";</script>$ag</body>";
		$Resultado2 = "<table border cellspacing='0' width='100%'><tr><th>SOLICITADO POR</th><th>JUSTIFICACION</th><th></th></tr>$Resultado3</table>";
		echo "parent.document.getElementById('mostrar_solicitud').innerHTML=\"$Resultado2\";</script>$ag</body>";
		$Resultado3 = "<table border cellspacing='0' width='100%'><tr><th>REACTIVACION</th><th>SINIESTRO</th><th>ACEPTAR</th><th>ELIMINAR</th></tr>$Resultado6</table>";
		echo "<script>parent.document.getElementById('mostrar_reactivacion').innerHTML=\"$Resultado3\";</script>$ag</body>";
	}
}

class caso_cola1
{
	var $Id = 0; // id del siniestro
	var $Aseguradora = 0; // id de la  aseguradora
	var $Fecha = ''; // fecha del siniestro

	function caso_cola1($S)
	{
		$this->Id = $S->id;
		$this->Aseguradora = $S->aseguradora;
		$this->Fecha = $S->fecha;
	}
}

function pinta_cola1()
{
	global $NTsin, $Fecha_de_arranque, $USUARIO, $IDUSUARIO;
	html();
	$AA = tabla2arreglo('aseguradora', array('id', 'emblema_f'));
	$AAo = tabla2arreglo('aseguradora', array('id', 'orden_monitor'));
	$AAd = tabla2arreglo('aseguradora', array('orden_monitor', 'emblema_f'));
	echo "<script language='javascript'>
	function mod_sin(id) { modal('zsiniestro.php?Acc=buscar_siniestro&id='+id,0,0,600,800); }
	function ver_seguimiento(id) {modal('zsiniestro.php?Acc=ver_seguimiento&id='+id,0,0,600,900,'vs');}
	function ver_observaciones(id) {modal('zsiniestro.php?Acc=ver_observaciones&id='+id,50,50,600,900,'vo');}
	function recargar() {document.getElementById('cargando').src='gifs/Loading.gif';window.open('zcallcenter2.php?Acc=pinta_cola1','_self');}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>";
	$Tmp = "tmpi_call2_cola1_" . $USUARIO . "_" . $IDUSUARIO;
	include('inc/link.php');
	mysql_query("drop table if exists $Tmp", $LINK);
	mysql_query("create table $Tmp select s.id,s.numero,s.ingreso,date_format(s.ingreso,'%Y%m%d') as fecha,s.aseguradora,s.info_erronea,s.placa
		FROM siniestro s where s.estado=5 and s.ingreso>='$Fecha_de_arranque'  ", $LINK);
	mysql_query("alter table $Tmp add primary key id (id)", $LINK);
	mysql_query("update $Tmp t,call2cola1 c set t.ingreso=c.fecha where t.id=c.siniestro ", $LINK);

	//mysql_query("delete t from $Tmp t,call2proceso c where t.id=c.siniestro and c.estado='A' ",$LINK);
	mysql_query("delete t from $Tmp t,call2cola2 c where t.id=c.siniestro", $LINK);
	mysql_query("delete t from $Tmp t,call2cola3 c where t.id=c.siniestro", $LINK);
	mysql_close($LINK);

	$sql = "select s.* from $Tmp s order by ingreso desc";
	//echo $sql;
	echo "<br>";
	if ($Cola1 = q($sql)) {
		$Ids_cola1 = '0';
		echo "<a style='cursor:pointer' onclick='recargar();'><img id='cargando' src='gifs/standar/Refresh.png' border='0' height='18px'></a>
		<b>Casos: " . mysql_num_rows($Cola1) . " Hoy: <span id='casoshoy' style='color:00aa00'></span> Erroneos: <span id='casosmal' style='color:aa5500'></span></b>
		Sin Gestion: <span id='singestion' style='color:bb0000'></span><br><center><span id='singestiont'></span></center><br>
		<table border cellspacing='0' width='100%'><tr><th>#</th><th>Aseguradora</th><th>Siniestro</th><th>Hora</th><th>Opc</th></tr>";
		$Fecha = '';
		$Bg = 'ffffff';
		$Bgr = 'ffbbbb';
		$Contador_fecha = 0;
		$Contador_fecha_hoy = 0;
		$Contador_infoerronea = 0;

		$Casos_cola1 = array();

		while ($C = mysql_fetch_object($Cola1)) {
			$Casos_cola1[$C->id] = new caso_cola1($C);
			$Ids_cola1 .= ',' . $C->id;
			if ($Fecha != date('Y-m-d', strtotime($C->ingreso))) {
				echo "<script language='javascript'>document.getElementById('$sFec').innerHTML='($Contador_fecha)';</script>";
				$Fecha = date('Y-m-d', strtotime($C->ingreso));
				$sFec = 'f' . date('Ymd', strtotime($C->ingreso));
				$aFec = 'a' . date('Ymd', strtotime($C->ingreso));
				$eFec = 'estadistica' . date('Ymd', strtotime($C->ingreso));

				$Bg = ($Bg == 'ffffff' ? 'ddddff' : 'ffffff');

				echo "<tr><td align='center' colspan='5' bgcolor='$Bg'><b>$Fecha <span id='$sFec'></span><span id='$eFec'></span></b><br><span id='$aFec'></span></td></tr>";
				$Contador_fecha = 0;
			}

			$Contador_fecha++;

			if ($C->info_erronea) {
				$Contador_infoerronea++;
			} elseif (date('Y-m-d') >= date('Y-m-d', strtotime($C->ingreso))) {
				$Contador_fecha_hoy++;
			}

			echo "<tr " . ($NTsin ? "ondblclick='mod_sin($C->id);'" : "") . ">
				<td bgcolor='" . ($C->info_erronea ? $Bgr : $Bg) . "'>$Contador_fecha</td>
				<td bgcolor='" . ($C->info_erronea ? $Bgr : $Bg) . "'><span id='im_co_" . $C->id . "'></span><span id='im_g_" . $C->id . "'></span><img src='" . $AA[$C->aseguradora] . "' border='0' height='16px'></td>
				<td bgcolor='" . ($C->info_erronea ? $Bgr : $Bg) . "'><a class='info'>$C->numero<span><b>$C->placa</b></span></a></td>
				<td bgcolor='" . ($C->info_erronea ? $Bgr : $Bg) . "'>" . date('H:i:s', strtotime($C->ingreso)) . "</td>
				<td bgcolor='" . ($C->info_erronea ? $Bgr : $Bg) . "' nowrap='yes'> <a style='cursor:pointer' onclick='ver_seguimiento($C->id);'>S</a> | <a style='cursor:pointer' onclick='ver_observaciones($C->id);'>O</a> </td>
			</tr>";
		}

		echo "</table>
		<script language='javascript'>
			document.getElementById('casoshoy').innerHTML='$Contador_fecha_hoy';
			document.getElementById('casosmal').innerHTML='$Contador_infoerronea';</script>";
		$Ids_singestion = $Ids_cola1;
		if ($Compromisos = q("select distinct siniestro from seguimiento where siniestro in ($Ids_cola1) and siniestro!=0 and tipo=16 ")) {
			echo "<script language='javascript'>";
			while ($Cmp = mysql_fetch_object($Compromisos)) {
				echo "document.getElementById('im_co_" . $Cmp->siniestro . "').innerHTML=\"<img src='gifs/jus.gif' border='0' alt='Con Compromiso' title='Con Compromiso'>\"; ";
				$Ids_singestion = str_replace(',' . $Cmp->siniestro, '', $Ids_singestion);
			}
			echo "</script>";
		}
		if ($Compromisos = q("select distinct siniestro from seguimiento where siniestro in ($Ids_singestion) and siniestro!=0 and tipo in (3,4,5,6,7,8,10,11,12,13,16,17,18,19)")) {
			echo "<script language='javascript'>";
			while ($Cmp = mysql_fetch_object($Compromisos)) {
				echo "	document.getElementById('im_g_" . $Cmp->siniestro . "').innerHTML=\"<img src='gifs/standar/noticias.png' border='0' alt='Gestionado' title='Gestionado'>\"; ";
				$Ids_singestion = str_replace(',' . $Cmp->siniestro, '', $Ids_singestion);
			}
			echo "</script>";
		}
		$A_singestion = explode(',', $Ids_singestion);
		$T_singestion = 0;
		echo "<script language='javascript'>";
		$Ac_fecha = array();
		$Ac_fechat = array();
		foreach ($A_singestion as $sing) {
			if ($sing) {
				$fecha = $Casos_cola1[$sing]->Fecha;
				$aseguradora = $AAo[$Casos_cola1[$sing]->Aseguradora];
				if (!$Ac_fecha[$fecha]) $Ac_fecha[$fecha] = array();
				if (!$Ac_fecha[$fecha][$aseguradora]) $Ac_fecha[$fecha][$aseguradora] = 0;
				$Ac_fecha[$fecha][$aseguradora]++;
				if (!$Ac_fechat[$aseguradora]) $Ac_fechat[$aseguradora] = 0;
				$Ac_fechat[$aseguradora]++;
				echo "document.getElementById('im_g_$sing').innerHTML=\"<img src='gifs/standar/Warning.png' border='0' alt='Sin Gestion' title='Sin Gestion'>\"; ";
				$T_singestion++;
			}
		}
		echo "document.getElementById('singestion').innerHTML='<b>$T_singestion</b>';";
		$Script = '';
		foreach ($Ac_fecha as $fec => $dato) {
			ksort($dato);
			$Script .= "
				var Objeto=document.getElementById('a$fec');Objeto.innerHTML=\"<b style='color:red'>SIN GESTION</b><br><table border cellspacing='0'>";
			$suma = 0;
			foreach ($dato as $aseguradora => $cantidad) {
				$img = "<img src='" . $AAd[$aseguradora] . "' border='0' height='16px'>";
				$Script .= "<tr><td>$img </td><td align='center'> $cantidad </td></tr>";
				$suma += $cantidad;
			}
			$Script .= "<tr><td>Total</td><td align='center'>$suma</td></tr></table>\";
			";
		}
		echo $Script;
		$Script = '';
		ksort($Ac_fechat);
		$suma = 0;
		if (count($Ac_fechat)) {
			$Script .= "
				var Objeto=document.getElementById('singestiont');Objeto.innerHTML=\"<b style='color:red'>SIN GESTION</b><br><table border cellspacing='0'>";
			foreach ($Ac_fechat as $aseguradora => $cantidad) {
				$img = "<img src='" . $AAd[$aseguradora] . "' border='0' height='16px'>";
				$Script .= "<tr><td>$img </td><td align='center'> $cantidad </td></tr>";
				$suma += $cantidad;
			}
			$Script .= "<tr><td>Total</td><td align='center'>$suma</td></tr></table>\";
			";
		}
		echo $Script;
		echo "</script>";
	}

	echo "<img src='gifs/Loading.gif' style='visibility:hidden'></body>";
}

function pinta_cola2()
{
	global $NTsin, $USUARIO, $IDUSUARIO;
	html();
	$AA = tabla2arreglo('aseguradora', array('id', 'emblema_f'));
	echo "<script language='javascript'>
	function mod_sin(id) {modal('zsiniestro.php?Acc=buscar_siniestro&id='+id,0,0,600,800);}
	function ver_seguimiento(id) {modal('zsiniestro.php?Acc=ver_seguimiento&id='+id,0,0,600,900,'vs');}
	function ver_observaciones(id) {modal('zsiniestro.php?Acc=ver_observaciones&id='+id,50,50,600,900,'vo');}
	function recargar() {document.getElementById('cargando').src='gifs/Loading.gif';window.open('zcallcenter2.php?Acc=pinta_cola2','_self');}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>";
	$Tmp = 'tmpi_call2_cola2_' . $USUARIO . '_' . $IDUSUARIO;
	include('inc/link.php');
	mysql_query("drop table if exists $Tmp", $LINK);
	/*Creacion tabla temporal pinta cola 2*/
	mysql_query("create table $Tmp select s.id,s.numero,c.fecha_aceptacion as ingreso,s.aseguradora,s.placa
		FROM siniestro s,call2cola2 c
		WHERE s.id=c.siniestro and c.aceptado=1 and s.estado=5", $LINK);
	mysql_query("alter table $Tmp add primary key id (id)", $LINK);
	mysql_query("delete t from $Tmp t, call2proceso c where t.id=c.siniestro and c.estado='A' ", $LINK);
	mysql_query("delete t from $Tmp t, call2cola3 c where t.id=c.siniestro ", $LINK);
	mysql_close($LINK);
	$sql = "select s.* from $Tmp s order by s.ingreso desc";

	if ($Cola2 = q($sql)) {
		$Ids_cola2 = '0';
		echo "<a style='cursor:pointer' onclick='recargar();'><img id='cargando' src='gifs/standar/Refresh.png' border='0' height='18px'></a>
		Numero de casos: " . mysql_num_rows($Cola2) . " Sin Gestion: <span id='singestion' style='color:bb0000'>0</span><br>
		<table border cellspacing='0' width='100%'><tr><th>#</th><th>Aseguradora</th><th>Siniestro</th><th>Hora</th><th>Opc</th></tr>";
		$Fecha = '';
		$Bg = 'ffffff';
		$Bgr = 'ffbbbb';
		$Contador = 0;
		while ($C = mysql_fetch_object($Cola2)) {

			$Ids_cola2 .= ',' . $C->id;
			if ($Fecha != date('Y-m-d', strtotime($C->ingreso))) {
				$Fecha = date('Y-m-d', strtotime($C->ingreso));
				$Bg = ($Bg == 'ffffff' ? 'ddddff' : 'ffffff');
				echo "<tr><td align='center' colspan='5' bgcolor='$Bg'><b>$Fecha</b></td></tr>";
			}
			$Contador++;
			echo "<tr " . ($NTsin ? "ondblclick='mod_sin($C->id);'" : "") . "><td align='center' bgcolor='$Bg'>$Contador</td>
			<td bgcolor='$Bg'><span id='im_co_" . $C->id . "'></span><span id='im_g_" . $C->id . "'></span><img src='" . $AA[$C->aseguradora] . "' border='0' height='16px'></td>
			<td bgcolor='$Bg'><a class='info'>$C->numero<span><b>$C->placa</b></span></a></td>
			<td bgcolor='$Bg'>" . date('H:i:s', strtotime($C->ingreso)) . "</td>
			<td bgcolor='$Bg' nowrap='yes'> <a style='cursor:pointer' onclick='ver_seguimiento($C->id);'>S</a> | <a style='cursor:pointer' onclick='ver_observaciones($C->id);'>O</a></td>
			</tr>";
		}
		echo "</table>";
		$Ids_singestion = $Ids_cola2;
		$Aceptaciones = q("select siniestro,concat(fecha,' ',hora) as fec from seguimiento where siniestro in ($Ids_singestion) and tipo=18 ");
		$A_aceptacion = array();
		while ($Ac = mysql_fetch_object($Aceptaciones)) {
			$A_aceptacion[$Ac->siniestro] = $Ac->fec;
		}

		if ($Compromisos = q("select distinct s.siniestro,concat(s.fecha,' ',s.hora) as fec from seguimiento s where s.siniestro in ($Ids_singestion) and s.tipo=16 order by fec,s.siniestro")) {
			echo "<script language='javascript'>";
			while ($Cmp = mysql_fetch_object($Compromisos)) {
				if ($Cmp->fec > $A_aceptacion[$Cmp->siniestro]) {
					$cadena = "Con Compromiso";
					echo "
						document.getElementById('im_co_" . $Cmp->siniestro . "').innerHTML=\"<img src='gifs/jus.gif' border='0' alt='$cadena' title='$cadena'>\"; ";
					$Ids_singestion = str_replace(',' . $Cmp->siniestro, '', $Ids_singestion);
				}
			}
			echo "</script>";
		}
		if ($Gestiones = q("select s.siniestro,concat(s.fecha,' ',s.hora) as fec from seguimiento s
		where s.siniestro in ($Ids_singestion)  and s.tipo in (3,4,5,6,7,8,10,11,12,13,16,17,18,19) order by fec,s.siniestro")) {
			echo "<script language='javascript'>";
			while ($G = mysql_fetch_object($Gestiones)) {
				if ($G->fec > $A_aceptacion[$G->siniestro]) {
					echo "	document.getElementById('im_g_" . $G->siniestro . "').innerHTML=\"<img src='gifs/standar/noticias.png' border='0' alt='Gestionado' title='Gestionado'>\"; ";
					$Ids_singestion = str_replace(',' . $G->siniestro, '', $Ids_singestion);
				}
			}
			echo "</script>";
		}
		//echo $Ids_singestion;
		$A_singestion = explode(',', $Ids_singestion);
		$T_singestion = 0;
		echo "<script language='javascript'>";
		foreach ($A_singestion as $sing) {
			if ($sing) {
				echo "document.getElementById('im_g_$sing').innerHTML=\"<img src='gifs/standar/Warning.png' border='0' alt='Sin Gestion' title='Sin Gestion'>\"; ";
				$T_singestion++;
			}
		}
		echo "document.getElementById('singestion').innerHTML='<b>$T_singestion</b>';</script>";
	}
	echo "</body>";
}

function pinta_cola2a()
{

	global $NTsin;
	html();
	$Fecha_cola2 = date('Y-m-d', strtotime(aumentadias(date('Y-m-d'), -2)));
	$AA = tabla2arreglo('aseguradora', array('id', 'emblema_f'));
	$A_cola3 = tabla2arreglo('call2cola3', array('siniestro', '1'));
	echo "<script language='javascript'>
	function mod_sin(id) {modal('zsiniestro.php?Acc=buscar_siniestro&id='+id,0,0,600,800);}
	function ver_seguimiento(id) {modal('zsiniestro.php?Acc=ver_seguimiento&id='+id,0,0,600,900,'vs');}
	function ver_observaciones(id) {modal('zsiniestro.php?Acc=ver_observaciones&id='+id,50,50,600,900,'vo');}
	function re_enviar_correo(id) {modal('zcallcenter2.php?Acc=caso_re_envia_correo_preadjudicacion&id='+id,0,0,600,900,'reenv');}
	function recargar() {document.getElementById('cargando').src='gifs/Loading.gif';window.open('zcallcenter2.php?Acc=pinta_cola2a','_self');}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
	<h4>Correos de Pre-Adjudicacion enviados</h4>";
	if ($Cola2 = q("select s.id,s.numero,c.fecha as envio,s.aseguradora,c.id as idc,s.placa
		FROM siniestro s,call2cola2 c where s.id=c.siniestro and c.aceptado=0 and s.estado=5
		order by c.fecha desc")) {
		echo "<a style='cursor:pointer' onclick='recargar();'><img id='cargando' src='gifs/standar/Refresh.png' border='0' height='18px'></a>
		Numero de casos: " . mysql_num_rows($Cola2) . " Mas de 2 D&iacute;as: <span id='cantig'></span><br>
		<table border cellspacing='0' width='100%'><tr><th>#</th><th>Aseguradora</th><th>Siniestro</th><th>Hora</th><th>Opc</th></tr>";
		$Fecha = '';
		$Bg = 'ffffff';
		$Bgr = 'ffbbbb';
		$Contador_c3 = 0;
		$Contador = 0;
		$Contador_fecha = 0;

		while ($C = mysql_fetch_object($Cola2)) {
			if ($Fecha != date('Y-m-d', strtotime($C->envio))) {

				echo "<script language='javascript'>document.getElementById('$sFec').innerHTML='($Contador_fecha)';</script>";

				$Fecha = date('Y-m-d', strtotime($C->envio));
				$Bg = ($Bg == 'ffffff' ? 'ddddff' : 'ffffff');
				$sFec = 'f' . date('Ymd', strtotime($C->envio));
				echo "<tr><td align='center' colspan='5' bgcolor='$Bg'><b>$Fecha <span id='$sFec'></span></b></td></tr>";
				$Contador_fecha = 0;
			}
			$Contador++;
			$Contador_fecha++;
			if (date('Y-m-d', strtotime($C->envio)) < $Fecha_cola2) {
				$Bg = 'E0AB77';
				$Contador_c3++;
			}
			echo "<tr " . ($NTsin ? "ondblclick='mod_sin($C->id);'" : "") . "><td align='center' bgcolor='$Bg'>$Contador</td>
			<td bgcolor='$Bg'>" . ($A_cola3[$C->id] ? "<img src='gifs/standar/noticias.png' border='0' alt='COLA 3' title='COLA 3'>" : "") . "<img src='" . $AA[$C->aseguradora] . "' border='0' height='16px'></td>
			<td bgcolor='$Bg'><a class='info'>$C->numero<span><b>$C->placa</b></span></a></td>
			<td bgcolor='$Bg'>" . date('H:i:s', strtotime($C->envio)) . "</td>
			<td bgcolor='$Bg' nowrap='yes'> <a style='cursor:pointer' onclick='ver_seguimiento($C->id);'>S</a> | <a style='cursor:pointer' onclick='ver_observaciones($C->id);'>O</a>  |
			<a style='cursor:pointer' onclick='re_enviar_correo($C->idc);'>E</a> </td>
			</tr>";
		}
		echo "</table>
		<script language='javascript'>document.getElementById('cantig').innerHTML=\"<b style='color:8E3400'>$Contador_c3</b>\";</script>";
	}
	echo "<iframe name='Oculto_cola2a' id='Oculto_cola2a' style='visibility:hidden' width='1' height='1'></iframe></body>";
}

function pinta_cola3()
{
	global $NTsin, $Fecha_de_arranque;
	html();
	$AA = tabla2arreglo('aseguradora', array('id', 'emblema_f'));
	echo "<script language='javascript'>
	function mod_sin(id) {modal('zsiniestro.php?Acc=buscar_siniestro&id='+id,0,0,600,800);}
	function ver_seguimiento(id) {modal('zsiniestro.php?Acc=ver_seguimiento&id='+id,0,0,600,900,'vs');}
	function ver_observaciones(id) {modal('zsiniestro.php?Acc=ver_observaciones&id='+id,50,50,600,900,'vo');}
	function recargar() {document.getElementById('cargando').src='gifs/Loading.gif';window.open('zcallcenter2.php?Acc=pinta_cola3','_self');}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>";
	if ($Cola3 = q("select s.id,s.numero,c.fecha,s.aseguradora,s.placa
		FROM siniestro s,call2cola3 c where s.id=c.siniestro and s.estado=5
		order by c.fecha desc")) {
		echo "<a style='cursor:pointer' onclick='recargar();'><img id='cargando' src='gifs/standar/Refresh.png' border='0' height='18px'></a>
		Numero de casos: " . mysql_num_rows($Cola3) . "<br>
		<table border cellspacing='0' width='100%'><tr><th>#</th><th>Aseguradora</th><th>Siniestro</th><th>Hora</th><th>Opc</th></tr>";
		$Fecha = '';
		$Bg = 'ffffff';
		$Contador = 0;
		while ($C = mysql_fetch_object($Cola3)) {
			if ($Fecha != date('Y-m-d', strtotime($C->fecha))) {
				$Fecha = date('Y-m-d', strtotime($C->fecha));
				$Bg = ($Bg == 'ffffff' ? 'ddddff' : 'ffffff');
				echo "<tr><td align='center' colspan='5' bgcolor='$Bg'><b>$Fecha</b></td></tr>";
			}
			$Contador++;
			echo "<tr " . ($NTsin ? "ondblclick='mod_sin($C->id);'" : "") . "><td align='center' bgcolor='$Bg'>$Contador</td>
			<td bgcolor='$Bg'><img src='" . $AA[$C->aseguradora] . "' border='0' height='16px'></td>
			<td bgcolor='$Bg'><a class='info'>$C->numero<span><b>$C->placa</b></span></a></td>
			<td bgcolor='$Bg'>" . date('H:i:s', strtotime($C->fecha)) . "</td>
			<td bgcolor='$Bg' nowrap='yes'> <a style='cursor:pointer' onclick='ver_seguimiento($C->id);'>S</a> | <a style='cursor:pointer' onclick='ver_observaciones($C->id);'>O</a> </td>
			</tr>";
		}
		echo "</table>";
	}
	echo "</body>";
}

function admin_nivel($Nivel)
{
	global $NTusu, $USUARIO, $A_accion_agentes;
	$Resultado = "";
	if ($Agentes = q("select u.*,e.nombre as nestado
							from usuario_callcenter u,estado_agente_call e
							where u.estado=e.id and u.nivel=$Nivel and e.id not in (6) order by u.estado,u.nombre")) {
		$Resultado .= "<table border cellspacing='0' width='100%'><tr><th>AGENTE $Nivel [Act:.a1.] [Inact:.a2.]</th><th>Estado</th><th>Abierto</th></tr>";
		include('inc/link.php');
		$Cantidad_activos = 0;
		$Cantidad_inactivos = 0;
		while ($Ag = mysql_fetch_object($Agentes)) {
			$A_accion_agentes .= $Ag->id . ',';
			if ($Ag->estado == 1) $Cantidad_activos++;
			else $Cantidad_inactivos++;
			$Bgc = ($Ag->estado == 1 ? "ddffdd" : "ffdddd");
			$Resultado .= "<tr><td bgcolor='$Bgc' class='agente'>$Ag->nombre " . ($Ag->prioridad_nuevos ? "<img src='gifs/standar/Warning.png' border='0' alt='Prioritario' title='Prioritario'>" : "") . "</td>
			<td bgcolor='$Bgc' class='agente'>" . ($NTusu ? "<a style='cursor:pointer' onclick='mod_agente($Ag->id);'>[+]</a> " : "") . "$Ag->nestado</td>
			<td bgcolor='ffffff' class='agente' align='left' nowrap='yes' id='td_ag_$Ag->id'>";
			$Resultado .= "</td></tr>";
		}
		$Resultado .= "</table>";
		mysql_close($LINK);
	}
	$Resultado = str_replace('.a1.', $Cantidad_activos, $Resultado);
	$Resultado = str_replace('.a2.', $Cantidad_inactivos, $Resultado);

	return $Resultado;
}
function solicitudes_flota()
{
	global $NTusu, $USUARIO, $A_accion_agentes;
	$Resultado = "";
	$Resultado .= "<table border cellspacing='0' width='100%'><tr><th>SOLICITADO POR</th><th>JUSTIFICACION</th><th></th></tr>";
	if ($Agentes = q("select * from cambio_flota_solicitud where estado=0")) {

		include('inc/link.php');
		$Cantidad_activos = 0;
		$Cantidad_inactivos = 0;
		while ($Ag = mysql_fetch_object($Agentes)) {

			$Resultado .= "<tr><td class='agente'>$Ag->solicitado_por </td>
			<td class='agente'>$Ag->justificacion</td>
			<td class='agente' align='left' nowrap='yes'><a style='cursor:pointer' onclick='flota_autorizar($Ag->id);'>[+]</a> ";
			$Resultado .= "</td></tr>";
		}
		$Resultado .= "</table>";
		mysql_close($LINK);
	}
	return  $Resultado;
}
function solicitudes_correos()
{
	global $NTusu, $USUARIO, $A_accion_agentes;
	$Resultado1 = "";

	$Resultado1 .= "<table border cellspacing='0' width='100%'><tr><th>CORREOS PENDIENTES</th><th>JUSTIFICACION</th><th></th></tr>";
	if ($Agentes = q("select * from correo where siniestro=0 AND ISNULL(respuesta) AND pdf=0")) {

		include('inc/link.php');
		$Cantidad_activos = 0;
		$Cantidad_inactivos = 0;
		while ($Ag = mysql_fetch_object($Agentes)) {

			$Resultado1 .= "<tr><td class='agente'>$Ag->asunto </td>
			<td class='agente'>" . strip_tags($Ag->cuerpo_mensaje) . "</td>
			<td class='agente' align='left' nowrap='yes'><a style='cursor:pointer' onclick='correo_autorizar($Ag->id);'>[+]</a> ";
			$Resultado1 .= "</td></tr>";
		}
		$Resultado1 .= "</table>";
		mysql_close($LINK);
	}
	return  $Resultado1;
}
function solicitudes_reactivacion_siniestro()
{
	global $NTusu, $USUARIO, $A_accion_agentes;
	$Resultado2 = "";

	$Resultado2 .= "<table border cellspacing='0' width='100%'><tr><th>CORREOS PENDIENTES</th><th>JUSTIFICACION</th><th></th></tr>";
	if ($Agentes = q("select * from correo where siniestro=0 AND ISNULL(respuesta) AND pdf=0")) {

		include('inc/link.php');
		$Cantidad_activos = 0;
		$Cantidad_inactivos = 0;
		while ($Ag = mysql_fetch_object($Agentes)) {

			$Resultado2 .= "<tr><td class='agente'>$Ag->asunto </td>
			<td class='agente'>" . strip_tags($Ag->cuerpo_mensaje) . "</td>
			<td class='agente' align='left' nowrap='yes'><a style='cursor:pointer' onclick='correo_autorizar($Ag->id);'>[+]</a> ";
			$Resultado2 .= "</td></tr>";
		}
		$Resultado2 .= "</table>";
		mysql_close($LINK);
	}
	return  $Resultado2;
}
/* ----------------------------------------------------------------------------------------------------------    MODULOS PERFIL AGENTE ------------------------------------------------------------------------------------------------*/
/* ----------------------------------------------------------------------------------------------------------    MODULOS PERFIL AGENTE ------------------------------------------------------------------------------------------------*/
/* ----------------------------------------------------------------------------------------------------------    MODULOS PERFIL AGENTE ------------------------------------------------------------------------------------------------*/
/* ----------------------------------------------------------------------------------------------------------    MODULOS PERFIL AGENTE ------------------------------------------------------------------------------------------------*/

function modulo_perfil_agente()
{
	global $IDUSUARIO, $tokenNick;

	if ($IDUSUARIO == 231) {
		//modulo_perfil_agente_developer();
		//exit;
	}


	$Dagente = qo("select * from usuario_callcenter where id=$IDUSUARIO");
	$Estado_Agente = $Dagente->estado;
	$Escalafon = q("select concat(e.nombre,' - ',t.nivel) as tipo,sum(c.puntaje) as total
						from call2escalafon c,call2tescalafon t,call2evescalafon e where c.codigo=t.id
						and t.evento=e.id and c.codigo=t.id and c.agente=$IDUSUARIO group by e.nombre,t.nivel order by e.nombre,t.nivel");
	$Escalafones = array();
	$Tescalafon = 0;
	while ($E = mysql_fetch_object($Escalafon)) {
		$Escalafones[$E->tipo] = $E->total;
		$Tescalafon += $E->total;
	}

	echo "<center><b style='font-size:16px;color:44aa44'>PERFIL AGENTE  NIVEL $Dagente->nivel " . ($Dagente->nivel2 ? " y Nivel $Dagente->nivel2" : "") . "</b></center>";

	$sql = "select seg.* from aoa_clientes.Auditoria_siniestros as sacap_siniestros inner join aoacol_aoacars.seguimiento as seg on sacap_siniestros.siniestro = seg.siniestro  where tipo = 16 and tipo_compromiso= 17 and cumplido = 0";
	$seg_allianz = array();

	$result = q($sql);
	while ($row = mysql_fetch_object($result)) {
		array_push($seg_allianz, $row);
	}

	echo "<h4>Casos pendientes de allianz por SACAP :  " . count($seg_allianz) . " </h4>";

	$extra_html =  "<select id='allianz_siniestros'><option>Selecciona</option>";

	foreach ($seg_allianz as $data) {
		//print_r($data);
		$extra_html .= "<option value=" . $data->siniestro . " >" . $data->siniestro . "</option>";
	}

	$extra_html .=  "</select> <button onclick='send_siniestro()'>Buscar caso</button>";

	echo $extra_html;

	echo "<br><br>";

	echo "<script>
		function send_siniestro()
		{
			e =  document.getElementById('allianz_siniestros');
			var siniestro = e.options[e.selectedIndex].value;
			window.location.href = 'zcallcenter2.php?Acc=presentar_caso&id='+siniestro;
		}
		function mostrar_anulados(){
			modal('http://ctc.aoacolombia.com/resultados?usuario=$tokenNick',0,0,500,600);
		}
	</script>";




	$Caso_abierto = qo("select * from call2proceso where agente='$IDUSUARIO' and estado='A'");
	if ($Caso_abierto && $Estado_Agente == 1) // estado activo
	{
		echo "Redireccionando al caso abierto numero $Caso_abierto->siniestro
			<iframe name='Oculto_caso_abierto' id='Oculto_caso_abierto' style='visibility:hidden' width='1' height='1'></iframe>
			<script language='javascript'>window.open('zcallcenter2.php?Acc=redireccionar_caso_abierto&id=$Caso_abierto->siniestro','Oculto_caso_abierto');</script></body>";
		die();
	}
	echo "<table align='center' border=0 cellspacing='1' bgcolor='000000' width='100%'><tr>
	<td bgcolor='ffffff' align='center'>";
	if ($Estado_Agente == 1) {
		echo "<a onclick='tomar_nuevo()'><img id='boton_rojo' src='img/boton_rojo.png' height='200px'></a><br>
			<span id='mensaje' style='color:blue;font-size:30px;font-weight:bold'>PRESIONE EL BOTON ROJO <br>PARA NUEVO CASO</span>";
		if ($Rentas = q("select id,numero,placa,ingreso,t_aseguradora(aseguradora) as naseg from siniestro where estado=5 and renta=1
			and id not in (select siniestro from call2proceso where estado='A') order by ingreso")) {
			echo "<table border cellspacing='0'><tr><th colspan=6>SERVICIOS DE RENTA</th></tr><tr><th>#</th><th>Aseguradora</th><th>Numero</th><th>Placa</th><th>Fecha Ingreso</th><th></th></tr>";
			$Contador_renta = 0;
			while ($Re = mysql_fetch_object($Rentas)) {
				$Contador_renta++;
				echo "<tr><td>$Contador_renta</td><td>$Re->naseg</td><td>$Re->numero</td><td>$Re->placa</td><td>$Re->ingreso</td>
						<td><a class='info' style='cursor:pointer' onclick=\"tomar_renta('$Re->placa');\"><img src='gifs/seguir.png' height=16><span>Tomar Caso</span></a></td></tr>";
			}
			echo "</table>";
		}
	} else
		echo "<b>ESTADO DE AGENTE: " . qo1("select nombre from estado_agente_call where id=$Estado_Agente") . "</b>";
	echo "</td>
	<td align='center' bgcolor='ffffff'><br><br><img src='$Dagente->foto_f' border='0' height='300'><br><br>
	<B style='color:000099'>ESCALAFON: $Tescalafon</b><br><table><tr><th>Evento</th><th>Cantidad</th></tr>";

	foreach ($Escalafones as $Tipo => $Cantidad)
		echo "<tr><td bgcolor='dddddd'>$Tipo</td><td align='right' bgcolor='dddddd'>$Cantidad</td></tr>";
	echo "</table></td><td align='center' bgcolor='ffffff'>
	<form action='zcallcenter2.php' target='Oculto_perfil_agente' method='POST' name='forma' id='forma'>
		Placa del Asegurado: <input type='text' name='placa' id='placa' value='' size='6' maxlength='10' onkeyup='javascript:this.value=this.value.toUpperCase();'><br><br>
		Siniestro: <input type='text' name='siniestro' id='siniestro' value='' size='20' maxlength='20'><br>
		<a style='cursor:pointer' onclick='buscar_caso();'><img src='img/grua_lupa.png' border='0'><br>
		<span id='mensaje2' style='color:blue;font-size:30px;font-weight:bold'>Buscar Caso</span></a>
		<input type='hidden' name='Acc' value='buscar_caso'>
	</form>
	</td>
	<td bgcolor='ffffff'></td></tr></table>";

	$PA_USUSE  = 'PA_USUSE';
	$tokenUser = $_SESSION['User'];
	$tokenPeril = $_SESSION['Peril'];
	$tokenNick = $_SESSION['Nick'];
	$NUSUARIO = $_SESSION['Nombre'];
	$USUARIO = $_SESSION['User'];
	$Perfil = qo("select * from usuario where id=" . $USUARIO);

	if ($Dagente->nivel > 1) echo "
	
          <script language='javascript'>
 
				 function ingresar_pa(dato) {if(confirm('Desea ingresar a Peque&ntilde;os accesor&iacute;os ?')) 
			    window.open('http://ctrl.aoacolombia.com/?#/inicio_call&PA_USUSE=$PA_USUSE&ciudad_siniestro=$Ofic->nombre&declarante_ciudad=$Ciuorig&declarante_email=$Sin->declarante_email&declarante_telefono=$Sin->declarante_telefono&declarante_nombre=$Sin->declarante_nombre&tokenNick=$tokenNick&siniestro=$Sin->numero&tokenPeril=$Perfil->idnombre&tokenUser=$NUSUARIO');
		
			}
		</script>	
		
	<table cellspacing=4><tr><td align='center' bgcolor='dddddd'><a onclick='ver_apartados()' style='cursor:pointer;font-size:16px'><img src='img/vehiculo.png' border='0' height='60' align='bottom'><br>Ver veh&iacute;culos apartados</a> </td>
	<td align='center'  bgcolor='dddddd'><a style='cursor:pointer;font-size:16px' onclick=\"modal('zcontrol_operativo3.php',0,0,600,600,'cop');\"><img src='img/control.png' border='0' height='60'><br>Tabla de Control</a></td>
	<td align='center' bgcolor='dddddd'><a style='cursor:pointer;font-size:16px' onclick=\"modal('zcitas.php',0,0,600,600,'citas');\"><img src='img/anotacion.png' border='0' height='60'><br>Citas del Dia</a></td>
	<td align='center' bgcolor='dddddd'><a style='cursor:pointer;font-size:16px' href='http://ctrl.aoacolombia.com/?#/inicio_call&PA_USUSE=$PA_USUSE&ciudad_siniestro=$Ofic->nombre&declarante_ciudad=$Ciuorig&declarante_email=$Sin->declarante_email&declarante_telefono=$Sin->declarante_telefono&declarante_nombre=$Sin->declarante_nombre&tokenNick=$tokenNick&siniestro=$Sin->numero&tokenPeril=$Perfil->idnombre&tokenUser=$NUSUARIO'  \"><img src='http://app.aoacolombia.com/img/Car-Repair-icon.png' border='0' height='60'><br>PEQUE&Ntilde;OS ACCESORIOS</a></td>
	<td align='center'  bgcolor='dddddd'><a style='cursor:pointer;font-size:16px' onclick=\"modal('http://ctc.aoacolombia.com/auth1/$tokenNick/no/$USUARIO',0,900,900,900,'correodirecto');\"><img src='img/icono_correos_call.png' border='0' height='60'><br>ENVIAR CORREOS</a></td>
	

	</tr></table>";
	// tabla
	echo "<h3>Solicitudes de correos</h3>";
	tablacorreo();
	echo "<br><button id='mostraryocultar' onclick='mostrar_anulados()'>MOSTRAR ANULADOS</button><br><br>";
	pinta_estadistica_diaria($IDUSUARIO);
	echo "<iframe name='Oculto_perfil_agente' id='Oculto_perfil_agente' style='visibility:hidden' width='1' height='1'></iframe>";
}

function tablacorreo()
{
	global $IDUSUARIO, $tokenNick;
	echo "
	<script>
	setInterval(function() {
		$('#mostrar_correo').load('zcallcenter2.php?Acc=tablacorreo');
	},10000);
	
	function ocultar_anulados(){
		document.getElementById('mostrar_correo2').style.display = 'none';
		document.getElementById('mostraryocultar').style.display = 'block';
		document.getElementById('ocultar').style.display = 'none';
	}
	
	</script>
	<div id='mostrar_correo'></div>
	
	
	
	";
	$Resultado4 = '';
	if ($query = q("select * from correo where visto_operario=0 and usuario='$tokenNick' and pdf=0")) {
		while ($Ag = mysql_fetch_object($query)) {
			$modal = "modalcorreosautorizado";
			if ($Ag->siniestro == 0) {

				$Ag->visto_operario = "ESPERANDO";
				$estilo = "style='background:yellow;'";
				if ($Ag->respuesta != null) {
					$estilo = "style='background:red;'";
					$Ag->visto_operario = "ANULADO";
					// $Ag->siniestro = 99999999;
					$modal = "correo_autorizar_anulado";
				}
			} else {
				$Ag->visto_operario = "RESPONDIDO";
				$Ag->siniestro = $Ag->siniestro;
				$estilo = "style='background:green;";
			}
			$Resultado4 .= "<tr><td class='agente'>$Ag->asunto </td><td class='agente'>" . strip_tags($Ag->cuerpo_mensaje) . "</td><td class='agente'>" . $Ag->respuesta . "</td><td $estilo class='agente' align='left' nowrap='yes'><a style='cursor:pointer; $estilo'  onclick='$modal($Ag->siniestro,$Ag->id)'; >$Ag->visto_operario</a></td></tr>";
		}
		$Resultado21 = "<table border cellspacing='0' width='60%'><tr><th>CORREOS PENDIENTES</th><th>JUSTIFICACION</th><th>RESPUESTA COORDINADOR</th><th></th></tr>$Resultado4</table>";
		echo "<script language='javascript'>document.getElementById('mostrar_correo').innerHTML=\"$Resultado21\";</script>$ag</body>";
	}
}

function modulo_perfil_agente_developer()
{

	global $IDUSUARIO;
	$Dagente = qo("select * from usuario_callcenter where id=$IDUSUARIO");
	$Estado_Agente = $Dagente->estado;
	$Escalafon = q("select concat(e.nombre,' - ',t.nivel) as tipo,sum(c.puntaje) as total
						from call2escalafon c,call2tescalafon t,call2evescalafon e where c.codigo=t.id
						and t.evento=e.id and c.codigo=t.id and c.agente=$IDUSUARIO group by e.nombre,t.nivel order by e.nombre,t.nivel");
	$Escalafones = array();
	$Tescalafon = 0;
	while ($E = mysql_fetch_object($Escalafon)) {
		$Escalafones[$E->tipo] = $E->total;
		$Tescalafon += $E->total;
	}

	echo "<center><b style='font-size:16px;color:44aa44'>PERFIL AGENTE  NIVEL $Dagente->nivel " . ($Dagente->nivel2 ? " y Nivel $Dagente->nivel2" : "") . "</b></center>";



	$sql = "select seg.* from aoa_clientes.Auditoria_siniestros as sacap_siniestros inner join aoacol_aoacars.seguimiento as seg on sacap_siniestros.siniestro = seg.siniestro  where tipo = 16 and tipo_compromiso= 17 and cumplido = 0";
	$seg_allianz = array();

	$result = q($sql);
	while ($row = mysql_fetch_object($result)) {
		array_push($seg_allianz, $row);
	}

	echo "<h4>Casos pendientes de allianz:  " . count($seg_allianz) . " </h4>";

	$extra_html =  "<select id='allianz_siniestros'><option>Selecciona</option>";

	foreach ($seg_allianz as $data) {
		//print_r($data);
		$extra_html .= "<option value=" . $data->siniestro . " >" . $data->siniestro . "</option>";
	}

	$extra_html .=  "</select> <button onclick='send_siniestro()'>Buscar caso</button>";

	echo $extra_html;

	echo "<br><br>";

	echo "<script>
		function send_siniestro()
		{
			e =  document.getElementById('allianz_siniestros');
			var siniestro = e.options[e.selectedIndex].value;
			window.location.href = 'zcallcenter2.php?Acc=presentar_caso&id='+siniestro;
		}
	</script>";

	$Caso_abierto = qo("select * from call2proceso where agente='$IDUSUARIO' and estado='A'");
	if ($Caso_abierto && $Estado_Agente == 1) // estado activo
	{
		echo "Redireccionando al caso abierto numero $Caso_abierto->siniestro
			<iframe name='Oculto_caso_abierto' id='Oculto_caso_abierto' style='visibility:hidden' width='1' height='1'></iframe>
			<script language='javascript'>window.open('zcallcenter2.php?Acc=redireccionar_caso_abierto&id=$Caso_abierto->siniestro','Oculto_caso_abierto');</script></body>";
		die();
	}
	echo "<table align='center' border=0 cellspacing='1' bgcolor='000000' width='100%'><tr>
	<td bgcolor='ffffff' align='center'>";
	if ($Estado_Agente == 1) {
		echo "<a onclick='tomar_nuevo()'><img id='boton_rojo' src='img/boton_rojo.png' height='200px'></a><br>
			<span id='mensaje' style='color:blue;font-size:30px;font-weight:bold'>PRESIONE EL BOTON ROJO <br>PARA NUEVO CASO</span>";
		if ($Rentas = q("select id,numero,placa,ingreso,t_aseguradora(aseguradora) as naseg from siniestro where estado=5 and renta=1
			and id not in (select siniestro from call2proceso where estado='A') order by ingreso")) {
			echo "<table border cellspacing='0'><tr><th colspan=6>SERVICIOS DE RENTA</th></tr><tr><th>#</th><th>Aseguradora</th><th>Numero</th><th>Placa</th><th>Fecha Ingreso</th><th></th></tr>";
			$Contador_renta = 0;
			while ($Re = mysql_fetch_object($Rentas)) {
				$Contador_renta++;
				echo "<tr><td>$Contador_renta</td><td>$Re->naseg</td><td>$Re->numero</td><td>$Re->placa</td><td>$Re->ingreso</td>
						<td><a class='info' style='cursor:pointer' onclick=\"tomar_renta('$Re->placa');\"><img src='gifs/seguir.png' height=16><span>Tomar Caso</span></a></td></tr>";
			}
			echo "</table>";
		}
	} else
		echo "<b>ESTADO DE AGENTE: " . qo1("select nombre from estado_agente_call where id=$Estado_Agente") . "</b>";
	echo "</td>
	<td align='center' bgcolor='ffffff'><br><br><img src='$Dagente->foto_f' border='0' height='300'><br><br>
	<B style='color:000099'>ESCALAFON: $Tescalafon</b><br><table><tr><th>Evento</th><th>Cantidad</th></tr>";
	foreach ($Escalafones as $Tipo => $Cantidad)
		echo "<tr><td bgcolor='dddddd'>$Tipo</td><td align='right' bgcolor='dddddd'>$Cantidad</td></tr>";
	echo "</table></td><td align='center' bgcolor='ffffff'>
	<form action='zcallcenter2.php' target='Oculto_perfil_agente' method='POST' name='forma' id='forma'>
		Placa del Asegurado: <input type='text' name='placa' id='placa' value='' size='6' maxlength='10' onkeyup='javascript:this.value=this.value.toUpperCase();'><br><br>
		Siniestro: <input type='text' name='siniestro' id='siniestro' value='' size='20' maxlength='20'><br>
		<a style='cursor:pointer' onclick='buscar_caso();'><img src='img/grua_lupa.png' border='0'><br>
		<span id='mensaje2' style='color:blue;font-size:30px;font-weight:bold'>Buscar Caso</span></a>
		<input type='hidden' name='Acc' value='buscar_caso'>
	</form>
	</td>
	<td bgcolor='ffffff'></td></tr></table>";
	if ($Dagente->nivel > 1) echo "
	<table cellspacing=4><tr><td align='center' bgcolor='dddddd'><a onclick='ver_apartados()' style='cursor:pointer;font-size:16px'><img src='img/vehiculo.png' border='0' height='60' align='bottom'><br>Ver veh&iacute;culos apartados</a> </td>
	<td align='center'  bgcolor='dddddd'><a style='cursor:pointer;font-size:16px' onclick=\"modal('zcontrol_operativo3.php',0,0,600,600,'cop');\"><img src='img/control.png' border='0' height='60'><br>Tabla de Control</a></td>
	<td align='center' bgcolor='dddddd'><a style='cursor:pointer;font-size:16px' onclick=\"modal('zcitas.php',0,0,600,600,'citas');\"><img src='img/anotacion.png' border='0' height='60'><br>Citas del Dia</a></td>
	</tr></table>";
	pinta_estadistica_diaria($IDUSUARIO);
	echo "<iframe name='Oculto_perfil_agente' id='Oculto_perfil_agente' style='visibility:hidden' width='1' height='1'></iframe>";
}

function pinta_estadistica_diaria($IDU)
{
	$Dagente = qo("select * from usuario_callcenter where id=$IDU");
	$Hoy = date('Y-m-d');
	echo "<br><table align='center'><tr><td align='center' colspan=2><h3>ESTADISTICA HOY $Hoy</h3></td></tr><tr><td>";
	if ($Pos_gestionados = q("select u.nombre, c.gestionados
	FROM call2est_diaria c, usuario_callcenter u
	WHERE c.agente=u.id and fecha='$Hoy' and c.nivel=$Dagente->nivel
	ORDER BY c.gestionados desc")) {
		echo "<table border cellspacing='0'><tr><th colspan=3>Siniestros Gestionados</th></tr><tr><th>Puesto</th><th>Agente</th><th>Cantidad</th></tr>";
		$Contador = 0;
		while ($G = mysql_fetch_object($Pos_gestionados)) {
			$Contador++;
			echo "<tr " . ($G->nombre == $Dagente->nombre ? "bgcolor='ffffaa'" : "") . "><td align='center'>$Contador</td><td>$G->nombre</td><td align='right'>$G->gestionados</td></tr>";
		}
		echo "</table>";
	}
	echo "</td><td>";
	if ($Pos_efectivos = q("select u.nombre, c.efectivos
	FROM call2est_diaria c, usuario_callcenter u
	WHERE c.agente=u.id and fecha='$Hoy' and c.nivel=$Dagente->nivel
	ORDER BY c.efectivos desc")) {
		echo "<table border cellspacing='0'><tr><th colspan=3>Siniestros Efectivos</th></tr><tr><th>Puesto</th><th>Agente</th><th>Cantidad</th></tr>";
		$Contador = 0;
		while ($G = mysql_fetch_object($Pos_efectivos)) {
			$Contador++;
			echo "<tr " . ($G->nombre == $Dagente->nombre ? "bgcolor='ffffaa'" : "") . "><td align='center'>$Contador</td><td>$G->nombre</td><td align='right'>$G->efectivos</td></tr>";
		}
		echo "</table>";
	}
	///////////-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$Primer_dia_semana = primer_dia_de_semana($Hoy);
	echo "</td></tr><tr><td align='center' colspan=2><h3>Estadistica Semanal $Primer_dia_semana - $Hoy </h3></td></tr><tr><td>";
	if ($Pos_gestionados = q("select u.nombre, c.gestionados
	FROM call2est_semanal c, usuario_callcenter u
	WHERE c.agente=u.id and fecha ='$Primer_dia_semana' and c.nivel=$Dagente->nivel

	ORDER BY c.gestionados desc")) {
		echo "<table border cellspacing='0'><tr><th colspan=3>Siniestros Gestionados</th></tr><tr><th>Puesto</th><th>Agente</th><th>Cantidad</th></tr>";
		$Contador = 0;
		while ($G = mysql_fetch_object($Pos_gestionados)) {
			$Contador++;
			echo "<tr " . ($G->nombre == $Dagente->nombre ? "bgcolor='ffffaa'" : "") . "><td align='center'>$Contador</td><td>$G->nombre</td><td align='right'>$G->gestionados</td></tr>";
		}
		echo "</table>";
	}
	echo "</td><td>";
	if ($Pos_efectivos = q("select u.nombre, c.efectivos
	FROM call2est_semanal c, usuario_callcenter u
	WHERE c.agente=u.id and fecha = '$Primer_dia_semana' and c.nivel=$Dagente->nivel
	ORDER BY c.efectivos desc")) {
		echo "<table border cellspacing='0'><tr><th colspan=3>Siniestros Efectivos</th></tr><tr><th>Puesto</th><th>Agente</th><th>Cantidad</th></tr>";
		$Contador = 0;
		while ($G = mysql_fetch_object($Pos_efectivos)) {
			$Contador++;
			echo "<tr " . ($G->nombre == $Dagente->nombre ? "bgcolor='ffffaa'" : "") . "><td align='center'>$Contador</td><td>$G->nombre</td><td align='right'>$G->efectivos</td></tr>";
		}
		echo "</table>";
	}
	echo "</td></tr></table>";
}

function tomar_nuevo_caso()
{
	global $USUARIO, $NUSUARIO, $IDUSUARIO, $Fecha_de_arranque;
	$Agente = qo("select * from usuario_callcenter where id=$IDUSUARIO");
	$Temp = "tmpi_call2_$IDUSUARIO";
	$Ahora = date('Y-m-d H:i:s');
	$Hoy = date('Y-m-d');
	$Seismeses = date('Y-m-d', strtotime(aumentadias($Hoy, -180)));
	include('inc/link.php');
	// ************************** LIMPIEZA DE REGISTROS ANTIGUOS EN LAS COLAS DE PROCESO 1, 2 Y 3 ******************************************
	mysql_query("insert ignore into call2proceso_hst select * from call2proceso where fecha_cierre<'$Seismeses' and fecha_cierre!='0000-00-00 00:00:00' ", $LINK);
	mysql_query("delete from call2proceso where fecha_cierre<'$Seismeses' and fecha_cierre!='0000-00-00 00:00:00' ", $LINK);
	mysql_query("insert ignore into call2cola2_hst select c.* from call2cola2 c,siniestro s where c.siniestro=s.id and s.estado!=5 and s.estado!=3 ", $LINK); // copia los casos ya procesados de la cola 2 a la cola historica
	mysql_query("insert ignore into call2cola2_hst select c.* from call2cola2 c,siniestro_hst s where c.siniestro=s.id and s.estado!=5 and s.estado!=3 ", $LINK); // copia los casos ya procesados de la cola 2 a la cola historica
	mysql_query("delete c from call2cola2 c,siniestro s where c.siniestro=s.id and s.estado!=5 and s.estado!=3", $LINK); // elimina los casos ya procesados de la cola 2 produccion
	mysql_query("delete c from call2cola2 c,siniestro_hst s where c.siniestro=s.id and s.estado!=5 and s.estado!=3", $LINK); // elimina los casos ya procesados de la cola 2 produccion
	mysql_query("insert ignore into call2cola3_hst select c.* from call2cola3 c,siniestro s where c.siniestro=s.id and s.estado!=5 and s.estado!=3 ", $LINK); // copia los casos ya procesados de la cola 3 a la cola historica
	mysql_query("delete c from call2cola3 c,siniestro s where c.siniestro=s.id and s.estado!=5 and s.estado!=3", $LINK); // elimina los casos ya procesados de la cola 3 produccion
	// ************************** FIN DE LIMPIEZA DE REGISTROS ANTIGUOS EN LAS COLAS DE PROCESO 1, 2 Y 3 ******************************************
	mysql_close($LINK);
	if ($Agente->nivel == 1) toma_caso_nivel1($Temp, $Agente, $Ahora);
	if ($Agente->nivel == 2) {
		$Ahora = date('Y-m-d H:i:s');
		if ($Agente->nivel2 == 1) {
			if ($Agente->frecuencia1 > $Agente->acumulado1) {
				if ($Agente->aseguradora1)  toma_caso_nivel2($Ahora, $Agente, 'acumulado1', $Agente->aseguradora1); // filtra la busqueda solo para esa aseguradora
				else  toma_caso_nivel2($Ahora, $Agente, 'acumulado1'); // toma caso cualquier aseguradora
			} elseif ($Agente->frecuencia2 > $Agente->acumulado2) {
				if ($Agente->aseguradora2)  toma_caso_nivel1($Temp, $Agente, $Ahora, 'acumulado2', $Agente->aseguradora2); // filtra la busqueda solo para esa aseguradora
				else  toma_caso_nivel1($Temp, $Agente, $Ahora, 'acumulado2'); // filtra la busqueda de todas las aseguradoras
			} else {
				q("update usuario_callcenter set acumulado1=0,acumulado2=0 where id=$IDUSUARIO"); // inicializa los acumuladores y vuelve a esta rutina
				echo "<body><script language='javascript'>window.open('zcallcenter2.php?Acc=tomar_nuevo_caso','_self');</script></body>";
			}
		} else
			toma_caso_nivel2($Ahora, $Agente); // toma caso cualquier aseguradora
	}
	if ($Agente->nivel == 3) {
		$Fecha_cola2 = date('Y-m-d', strtotime(aumentadias(date('Y-m-d'), -2)));
		if ($Caso = qo("select s.id,c.fecha from siniestro s, call2cola3 c where s.id=c.siniestro and
		s.id not in (select siniestro from call2proceso where estado='A')  and s.estado=5
		UNION
		select s.siniestro as id,s.fecha from call2cola2 s,siniestro si where si.id=s.siniestro and s.aceptado=0 and s.fecha<'$Fecha_cola2 00:00:00' and s.estado=0
		and si.estado=5 and s.siniestro not in (select siniestro from call2proceso where estado='A') and s.siniestro not in (select siniestro from call2cola3)
		order by fecha limit 1")) {
			$Hoy = date('Y-m-d H:i:s');
			$Fecha = date('Y-m-d');
			$Hora = date('H:i:s');
			if ($ID = q("insert into call2proceso (fecha,agente,siniestro,estado) values ('$Hoy','$IDUSUARIO','$Caso->id','A')")) {
				q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ('$Caso->id','$NUSUARIO','$Fecha','$Hora','Abre Caso - Adjudicacion',2)");
				q("delete from call2cola3 where siniestro=$Caso->id");
				echo "<body><script language='javascript'>parent.cargar_caso($Caso->id);</script></body>";
				die();
			}
		} else {
			echo "<body><script language='javascript'>alert('No hay casos pendientes de nivel 3');parent.Tomando=false; parent.document.getElementById('boton_rojo').src='img/boton_rojo.png';</script></body>";
			die();
		}
	}
	echo "<body><script language='javascript'>window.open('zcallcenter2.php?Acc=tomar_nuevo_caso','_self');</script></body>";
}

function toma_caso_nivel1($Temp, $Agente, $Ahora, $acumulado = '', $aseguradora = '')
{
	global $USUARIO, $NUSUARIO, $IDUSUARIO, $Fecha_de_arranque;

	//************************* INICIO DEL PROCESO DE TOMA DE CASO ************************************************************
	$Fin_de_linea = "\r\n";
	// $Debug='';
	$ConCompromiso = false; // posibles casos con compromiso
	// crea una tabla temporal para buscar los siniestros nuevos
	$Naseguradora = '';
	if ($aseguradora) {
		$Naseguradora = 'AOA';
		if ($aseguradora == 1 || $aseguradora == 8 || $aseguradora == 9) {
			$aseguradora = '1,8,9';
			$Naseguradora = 'ALLIANZ';
		} elseif ($aseguradora == 2 || $aseguradora == 5) {
			$aseguradora = '2,5';
			$Naseguradora = 'RSA';
		} elseif ($aseguradora == 3 || $aseguradora == 7) {
			$aseguradora = '3,7';
			$Naseguradora = 'LIBERTY';
		} elseif ($aseguradora == 4) {
			$aseguradora = '4';
			$Naseguradora = 'MAPFRE';
		}
	}
	include('inc/link.php');
	mysql_query("drop table if exists $Temp", $LINK);
	// $Debug.="Crea la tabla $Temp filtro de aseguradora: $aseguradora. ";
	if (!mysql_query("create table $Temp select s.id,s.ingreso,(0) as compromiso from siniestro s where s.estado=5
		and s.ingreso>='$Fecha_de_arranque' " . ($aseguradora ? " and s.aseguradora in ($aseguradora) and s.renta=0 " : ""), $LINK)) {
		mysql_close($LINK);
		die('Error: ' . mysql_error($LINK));
	}
	mysql_query("alter table $Temp add primary key id (id)", $LINK);
	mysql_query("delete t from $Temp t, call2cola2 c where t.id=c.siniestro", $LINK); // BORRA LOS CASOS DE LA COLA 2
	mysql_query("delete t from $Temp t, call2cola3 c where t.id=c.siniestro", $LINK); // BORRA LOS CASOS DE LA COLA 3
	mysql_query("delete t from $Temp t, call2infoerronea c where t.id=c.siniestro and c.fecha_proceso='0000-00-00 00:00:00' ", $LINK);	 // BORRA LOS CASOS DE INFORMACION ERRONEA
	mysql_query("alter table $Temp add index ingreso (ingreso)", $LINK); // crea indices para la tabla
	// $Debub.=" Actualiza los ingresos2. ";

	// $Debug.=" Prioridad nuevos: $Agente->prioridad_nuevos Tiempo de compromisos: $Agente->tiempo_compromiso ";
	$Ahora2 = date('Y-m-d H:i:s');
	$idcompromiso = 16;

	if ($Agente->prioridad_nuevos) // si el agente tiene prioridad para solo nuevos
	{
		$Casos = mysql_query("select id from $Temp order by id", $LINK);
		$casos = '0';
		while ($Ca = mysql_fetch_object($Casos)) $casos .= ',' . $Ca->id;
		mysql_query("delete from $Temp where id in (select distinct siniestro from seguimiento where siniestro in ($casos) and tipo>2)", $LINK); // borra del temporal todos los casos que hayan sido procesados con anterioridad
	} elseif ($Agente->tiempo_compromiso) {
		//$Casos=mysql_query("select id from $Temp order by id",$LINK);$casos='0';while($Ca=mysql_fetch_object($Casos))$casos.=','.$Ca->id; // obtiene todos los casos del temporal para crear un arreglo.

		$tiempo = qo1m("select minutos from call2tcomp where id=$Agente->tiempo_compromiso", $LINK);
		// $Debug.=" Tiempo de compromisos: $tiempo ";

		$Ahora1 = date('Y-m-d H:i:s', mktime(date('H'), date('i') - $tiempo, date('s'), date('n'), date('j'), date('Y')));
		// $Debug.=" Fechas limites: $Ahora1 - $Ahora2 ";
		echo "<body><script language='javascript'>parent.document.getElementById('mensaje').innerHTML='Buscando compromisos de $Ahora1 a $Ahora2';</script>";
		mysql_query("update $Temp t,seguimiento s set t.ingreso=s.fecha_compromiso,t.compromiso=1 where t.id=s.siniestro and s.tipo=16 and s.cumplido=0 and fecha_compromiso > '$Ahora1' ", $LINK); // ACTUALIZA EL INGRESO CON LAS FECHAS DE COMPROMISO
		$Qposibles = "select count(t.id) as cantidad from $Temp t where compromiso=1 and ingreso between '$Ahora1' and '$Ahora2' ";
		$Posibles = qo1m($Qposibles, $LINK);
		// $Debug.=" Consulta de posibles con compromiso $Qposibles ";
		if ($Posibles > 0) // si hay casos con compromiso borra los casos que no esten en el rango de tiempo y borra los casos que no sean de compromisos
		{
			$ConCompromiso = true;
			// $Debug.=" Compromisos encontrados: $Posibles borra los restantes. ";
			//mysql_query("delete from $Temp where id not in (select siniestro from call2cola1 c where c.compromiso=1 and c.fecha between '$Ahora1' and '$Ahora2' ) ",$LINK); // borra los casos que no sean exclusivamente compromisos
			mysql_query("delete from $Temp where compromiso=0 ", $LINK); // borra los casos que no sean exclusivamente compromisos
			//mysql_query("update $Temp set compromiso=1",$LINK); // Los casos que quedan deben ser solo de compromisos, entonces quedan marcados con 1 en el campo compromiso
		} else {
			// $Debug.=" No hay compromisos en el rango. ";
			echo "<script language='javascript'>parent.document.getElementById('mensaje').innerHTML='No hay compromisos en el rango de tiempo de $Ahora1 a $Ahora2, tomando un caso estandar..';</script>";
		}
	}

	mysql_close($LINK);
	// TOMA EL CASO NUEVO
	$Ahora = date('Y-m-d H:i:s');
	if ($Caso = qo("select id,compromiso from $Temp where ingreso<'$Ahora' and id not in (select siniestro from call2proceso where estado='A') order by ingreso limit 1")) {
		$Hoy = date('Y-m-d H:i:s');
		$Fecha = date('Y-m-d');
		$Hora = date('H:i:s');
		if ($ID = q("insert into call2proceso (fecha,agente,siniestro,estado) values ('$Hoy','$IDUSUARIO','$Caso->id','A')")) {
			$_SESSION['nivelcaso'] = 1;
			// $Debug.=" Marcando compromiso como cumplido siniestro: $Caso->id. ";
			q("update seguimiento set cumplido=1 where siniestro='$Caso->id' and tipo=16 and fecha_compromiso < '$Ahora2' and cumplido=0 ");
			// $Debug.=" Creando apertura en Seguimientos ";
			q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ('$Caso->id','$NUSUARIO','$Fecha','$Hora',\"Abre Caso \",2)");  // inserta un seguimiento
			q("delete from call2cola1 where siniestro=$Caso->id"); // borra de la cola 1 el siniestro
			if ($acumulado) q("update usuario_callcenter set $acumulado=$acumulado+1 where id=$IDUSUARIO"); // incrementa el acumulador
			// if($IDUSUARIO==2)
			// {
			// $a=fopen('planos/debugcallnivel1.txt','w+');
			// fwrite($a,$Debug.$Fin_de_linea);
			// fclose($a);
			// }
			echo "<body><script language='javascript'>parent.cargar_caso($Caso->id);</script></body>"; // carga el caso para el agente de call center
			die();
		}
	} else {
		if ($acumulado) q("update usuario_callcenter set $acumulado=$acumulado+1 where id=$IDUSUARIO"); // incrementa el acumulador
		echo "<body><script language='javascript'>if(confirm('No hay casos nuevos para tomar en nivel 1. Desea intentar nuevamente?')){window.open('zcallcenter2.php?Acc=tomar_nuevo_caso','_self');} else{parent.re_comenzar();}</script></body>";
		die();
	}
	//// FIN DE TOMA DE CASO NUEVO
}

function toma_caso_nivel2($Ahora, $Agente, $acumulado = '', $aseguradora = '')
{
	global $USUARIO, $NUSUARIO, $IDUSUARIO, $Fecha_de_arranque;
	$Naseguradora = '';
	if ($aseguradora) {
		$Naseguradora = 'AOA';
		if ($aseguradora == 1 || $aseguradora == 8 || $aseguradora == 9) {
			$aseguradora = '1,8,9';
			$Naseguradora = 'ALLIANZ';
		} elseif ($aseguradora == 2 || $aseguradora == 5) {
			$aseguradora = '2,5';
			$Naseguradora = 'RSA';
		} elseif ($aseguradora == 3 || $aseguradora == 7) {
			$aseguradora = '3,7';
			$Naseguradora = 'LIBERTY';
		} elseif ($aseguradora == 4) {
			$aseguradora = '4';
			$Naseguradora = 'MAPFRE';
		}
	}
	if ($Caso = qo("select s.id from siniestro s, call2cola2  c where s.id=c.siniestro and c.aceptado=1 and s.estado>1 and c.estado=0 and
		s.id not in (select siniestro from call2proceso where estado='A') and c.fecha_aceptacion<'$Ahora' " . ($aseguradora ? " and s.aseguradora in ($aseguradora) " : "") . "
		order by c.fecha_aceptacion limit 1")) {
		$Hoy = date('Y-m-d H:i:s');
		$Fecha = date('Y-m-d');
		$Hora = date('H:i:s');
		if ($ID = q("insert into call2proceso (fecha,agente,siniestro,estado) values ('$Hoy','$IDUSUARIO','$Caso->id','A')")) {
			$_SESSION['nivelcaso'] = 2;
			q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ('$Caso->id','$NUSUARIO','$Fecha','$Hora','Abre Caso - Adjudicacion',2)");
			q("update call2cola2 set estado='1' where siniestro=$Caso->id");
			if ($acumulado) q("update usuario_callcenter set $acumulado=$acumulado+1 where id=$IDUSUARIO"); // incrementa el acumulador
			graba_bitacora('call2cola2', 'M', $Caso->id, 'cambia estado a 1');
			echo "<body><script language='javascript'>parent.cargar_caso($Caso->id);</script></body>";
			die();
		}
	} else {
		if ($acumulado)  q("update usuario_callcenter set $acumulado=$acumulado+1 where id=$IDUSUARIO"); // incrementa el acumulador
		echo "<body><script language='javascript'>alert('No hay casos pendientes de nivel 2 $Naseguradora, intente nuevamente por favor.');parent.Tomando=false; parent.document.getElementById('boton_rojo').src='img/boton_rojo.png';</script></body>";
		die();
	}
}

function toma_caso_vozip()
{
	// Esta rutina debe ser la que llama el modulo de voz ip , debe recibir el id del siniestro ($idsiniestro), y la extesion marcada ($extension)
	// url automatica en VOZ IP: https://app.aoacolombia.com/Control/operativo/zcallcenter2.php?Acc=toma_caso_vozip

	/*


	espera te muestro
	http://localhost/encuesta/index.php?lead_id=94&vendor_id=&list_id=2000&gmt_offset_now=0.00&phone_code=57&
	phone_number=3133320089&title=Mr&first_name=Herbert&middle_initial=&last_name=Garcia&address1=&address2=1
	&address3=04&city=bog&state=&province=&postal_code=&country_code=CO&gender=U&date_of_birth=0000-00-00
	&alt_phone=&email=&security_phrase=&comments=Herbert&user=1000&pass=1234&orig_pass=1234&campaign=1002
	&phone_login=1000&original_phone_login=1000&phone_pass=1234&fronter=VDAD&closer=1000&group=1002&channel_group=1002
	&SQLdate=2015-07-03+122034&epoch=1435944036&uniqueid=1435944017.21&customer_zap_channel=SIP/teleone2-00000006
	&customer_server_ip=10.10.90.19&server_ip=10.10.90.19&SIPexten=1000&session_id=8600051&phone=3133320089&parked_by=94
	&dispo=&dialed_number=3133320089&dialed_label=MAIN&source_id=&rank=0&owner=&camp_script=&in_script=&script_width=774
	&script_height=474&fullname=Sebastian+Nocetti&agent_email=&recording_filename=&recording_id=&user_custom_one=
	&user_custom_two=&user_custom_three=&user_custom_four=&user_custom_five=&preset_number_a=&preset_number_b=
	&preset_number_c=&preset_number_d=&preset_number_e=&preset_dtmf_a=&preset_dtmf_b=&did_id=&did_extension=
	&did_pattern=&did_description=&closecallid=&xfercallid=&agent_log_id=126&entry_list_id=2000&call_id=V

		*/

	include('inc/gpos.php');
	$idsiniestro = $address2;
	$extension = $phone_login;
	$idcall = $lead_id;
	$numero_marcado = $phone_number;
	$nombre_agente = $fullname;

	if ($AgenteCall = qo("select * from usuario_callcenter where extension='$extension' limit 1")) {
		$idagente = $AgenteCall->id;
		$Hoy = date('Y-m-d H:i:s');
		$Fecha = date('Y-m-d');
		$Hora = date('H:i:s');

		if ($idsiniestro == null or $idsiniestro == 0) {
			//Agrege nueva condicion para no cambiar como funciona el programa sin embargo es necesario revisar por que crea el caso dos veces
			//echo "new condition";
			//select c.* from siniestro as s , call2proceso as c where s.declarante_celular = '$phone_number' and c.siniestro = s.id and c.agente = '$idagente' and c.estado = 'A' ;
			$sql = "select c.* from siniestro as s , call2proceso as c where s.declarante_celular = '$phone_number' and c.siniestro = s.id and c.agente = '$idagente' and c.estado = 'A' order by c.id desc limit 1";
			//echo $sql;
			$search_siniester = qo($sql);
			if ($search_siniester) {
				$idsiniestro = $search_siniester->siniestro;
			}
		}

		if ($idsiniestro != null and $idsiniestro != 0) {
			if ($ID = q("insert into call2proceso (fecha,agente,siniestro,estado) values ('$Hoy','$idagente','$idsiniestro','A')")) {
				$_SESSION['nivelcaso'] = 1;
				q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ('$idsiniestro','$idagente','$Fecha','$Hora',\"Abre Caso Id llamada:$idcall numero marcado: $phone Agente de call: $nombre_agente \",2)");  // inserta un seguimiento de apertura
				q("delete from call2cola1 where siniestro=$idsiniestro"); // borra de la cola 1 el siniestro
				echo "<body><script language='javascript'>window.open('zcallcenter2.php?Acc=presentar_caso&id='+$idsiniestro,'_self');</script></body>"; // carga el caso para el agente de call center
			} else {
				html();
				echo "<body><h3>FALLO EN REDIRECCION VOZ IP</h3>
					Error: no puede insertar el registro dentro de la cola de procesos.<br>
					Datos: <br>
					Id Siniestro: $idsiniestro  <br>
					Extension $extension <br>
					Agente Asignado: $AgenteCall->nombre  id: $idagente<br>

				</body>";
			}
		}
	} else {
		html();
		echo "<body><h3>FALLO EN REDIRECCION VOZ IP</h3>
			Error: No se encuentra el agente correspondiente a la extension. <br><br>
			Id Siniestro: $idsiniestro  <br>
			Extension $extension
		</body>";
	}
}

function buscar_caso()
{
	global $placa, $siniestro, $IDUSUARIO, $NUSUARIO;
	$Caso = false;
	$Encontrado = false;
	if ($placa) {
		if ($Caso = qo("select * from siniestro where placa = '$placa' order by ingreso desc"))	$Encontrado = true;
		else echo "<body><script language='javascript'>alert('No hay informacion');</script></body>";
	}
	if ($siniestro) {
		if ($Caso = qo("select * from siniestro where numero like '%$siniestro%'")) $Encontrado = true;
		else echo "<body><script language='javascript'>alert('No hay informacion');</script></body>";
	}

	if ($Caso && $Encontrado) {
		if ($Proceso = qo("select *,u.nombre as nagente from call2proceso c,usuario_callcenter u where c.siniestro=$Caso->id and c.estado='A' and c.agente=u.id ")) {
			echo "<body><script language='javascript'>alert('El caso esta siendo atendido por: $Proceso->nagente');parent.re_comenzar();</script></body>";
			die();
		}
		if ($Caso->estado == 5 /* Pendiente*/) {
			$Hoy = date('Y-m-d H:i:s');
			if ($ID = q("insert into call2proceso (fecha,agente,siniestro,estado) values ('$Hoy','$IDUSUARIO','$Caso->id','A')")) {
				$Fecha = date('Y-m-d');
				$Hora = date('H:i:s');
				q("update seguimiento set cumplido=1 where siniestro='$Caso->id' and tipo=16 and cumplido=0");
				q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ('$Caso->id','$NUSUARIO','$Fecha','$Hora','Abre Caso',2)");
				if ($Idcaso = qo1("select id from call2cola1 where siniestro=$Caso->id")) {
					$_SESSION['nivelcaso'] = 1;
					q("delete from call2cola1 where siniestro=$Caso->id");
				}
				if ($Idcaso = qo1("select id from call2cola2 where siniestro=$Caso->id")) {
					$_SESSION['nivelcaso'] = 2;
					q("delete from call2cola1 where siniestro=$Caso->id");
				}
			}
		}

		if ($Caso->estado == 7 /* Servicios*/) {
			$Hoy = date('Y-m-d H:i:s');
			if ($ID = q("insert into call2proceso (fecha,agente,siniestro,estado) values ('$Hoy','$IDUSUARIO','$Caso->id','A')")) {
				$Fecha = date('Y-m-d');
				$Hora = date('H:i:s');
				q("update seguimiento set cumplido=1 where siniestro='$Caso->id' and tipo=16 and cumplido=0");
				q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ('$Caso->id','$NUSUARIO','$Fecha','$Hora','Abre Caso',2)");
				if ($Idcaso = qo1("select id from call2cola1 where siniestro=$Caso->id")) {
					$_SESSION['nivelcaso'] = 1;
					q("delete from call2cola1 where siniestro=$Caso->id");
				}
				if ($Idcaso = qo1("select id from call2cola2 where siniestro=$Caso->id")) {
					$_SESSION['nivelcaso'] = 2;
					q("delete from call2cola1 where siniestro=$Caso->id");
				}
			}
		}


		/// carga el caso y lo presenta al usuario
		echo "<body><script language='javascript'>parent.cargar_caso($Caso->id);</script></body>";
	}
}

function presentar_caso()
{
	global $id, $USUARIO, $NUSUARIO, $IDUSUARIO, $tokenNick;
	$idrol = $_SESSION['User'];
	$Dagente = qo("select * from usuario_callcenter where id=$IDUSUARIO");
	html();
	echo "<script language='javascript'>
	function recargar() {window.open('zcallcenter2.php?Acc=presentar_caso&id=$id','_self');}
	function adicionar_observaciones()
	{ 
	
	var Obs=document.getElementById('nuevas_observaciones').value;
	var Date=document.getElementById('fecha_solicitud_usuario').value;
	if(alltrim(Obs)){
	var tipoCaja=document.getElementById('tipo_caja').value;
	if(alltrim(tipoCaja)=== 'Seleccione'){
		alert('Debe seleccionar un tipo de caja ');		
		}else{
			window.open('zcallcenter2.php?Acc=adicionar_observaciones&id=$id&d='+Obs+'&tipoCaja='+tipoCaja+'&date='+Date,'Oculto_presentar_caso');
	}
		}else{
			
		alert('Debe ingresar las Observaciones');
	}
	}
	
	function re_comenzar() {window.open('zcallcenter2.php','_self');}
	function solicitar_reactivacion() {window.open('zcallcenter2.php?Acc=solicita_reactivacion&id=$id','_self');}
	function cambia_oficina_caso(dato) {if(confirm('Desea cambiar de oficina?')) window.open('zcallcenter2.php?Acc=cambiar_ciudad_caso&id=$id&nueva_oficina='+dato,'Oculto_presentar_caso');}
	function asignar_transporte_vip() {window.open('zcallcenter2.php?Acc=asignar_transporte_vip&id=$id','_self');}
	function vercorreo(id) {modal('https://pot.aoacolombia.com/versinicorreo/'+id,'Oculto_ver_siniestro',400,400,600,900,'au');}
	function modalcorreos(){modal('http://ctc.aoacolombia.com/auth1/$tokenNick/$id/$idrol',0,900,900,900,'correo');}

	</script>";
	$Sin = qo("select * from siniestro where id=$id");
	$Aseg = qo("select * from aseguradora where id=$Sin->aseguradora");

	$Ofic = qo("select * from oficina where ciudad=$Sin->ciudad");
	$Ciuorig = qo1("select concat(departamento,' - ',nombre) from ciudad where codigo='$Sin->ciudad_original' ");

	$ubicacion = qo("select vehiculo from ubicacion where id = $Sin->ubicacion");
	$estadoSiniestro = qo("select e.nombre,e.color_co from siniestro s inner join estado_siniestro e on s.estado = e.id where s.id = $id");
	$va = base64_encode(1);

	$htmlBotonCliente = "<br>
	<style type='text/css'>
  .botonRegistro{
    text-decoration: none;
    padding: 10px;
    font-weight: 600;
    font-size: 15px;
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
	<a href='https://app.aoacolombia.com/Control/operativo/zcallcenter2.php?Acc=pide_datos_financieros&ids=$Sin->id&cerrando=1&sinG1=$va'>
	  <button type='button' class='botonRegistro'>Registra Cliente</button>
	</a><br><br>";
	$htmlTablaOffice = "
			<h2>Datos de la oficina:</h2>
			<table>
			<tr>
			<td bgcolor='ffffff'>Horarios y Pico y placa:</td><td bgcolor='ffffff'><textarea style='width: 725px;
    height: 108px;
' row='50' cols='50'>$Ofic->picoyplaca</textarea></td>
			<tr><td bgcolor='ffffff'>Ciudad:</td><td bgcolor='ffffff'>$Ofic->nombre</td></tr>
			<tr><td bgcolor='ffffff'>Direccion:</td><td bgcolor='ffffff'>$Ofic->direccion</td></tr>
			<tr><td bgcolor='ffffff'>Telefono:</td><td bgcolor='ffffff'>$Ofic->telefono</td></tr>
			</table>
			";

	if (isset($ubicacion->vehiculo)) {

		$vehiculoData = qo("select v.tipo_caja,v.placa,s.n_poliza,s.aseguradora_nombre,s.linea_asistencia,l.nombre nlinea
							FROM vehiculo v
							inner join seguros s on v.n_poliza = s.id
							inner join linea_vehiculo l on v.linea = l.id  
							where v.id=$ubicacion->vehiculo ");
		if ($Sin->estado == 7) {



			$linea_asistencia = qo("select s.linea_asistencia from  vehiculo v
								inner join seguros s on v.n_poliza = s.id
								where v.id = $ubicacion->vehiculo");

			$var = $linea_asistencia->linea_asistencia;




			if ($vehiculoData->tipo_caja == 1) {
				$cajaCambios = 'MANUAL';
			} else if ($vehuculo->tipo_caja == 2) {
				$cajaCambios = 'AUTOM&iacute;TICO';
			} else {
				$cajaCambios = 'NO DEFINIDA';
			}

			if ($linea_asistencia->linea_asistencia == "" or $linea_asistencia->linea_asistencia = null) {
				$lineaTel = 'NO DEFINIDA POR FALTA DE DATOS';
			} else {
				$lineaTel = $var;
			}

			//echo $lineaTel."Hola tes";
			//$ubicacion = qo("select vehiculo from ubicacion where id = $Sin->ubicacion");
			$htmlTabla .= "
			<h2>Datos del vehiculo:</h2>
			<table>
			<tr>
			<td bgcolor='ffffff'>Placa:</td><td bgcolor='ffffff'>$vehiculoData->placa</td>
			<tr><td bgcolor='ffffff'>Vehiculo:</td><td bgcolor='ffffff'>$vehiculoData->nlinea</td></tr>
			<td bgcolor='ffffff'>Caja:</td><td bgcolor='ffffff'>$cajaCambios</td>
			
			<tr><td bgcolor='ffffff'>Poliza:</td><td bgcolor='ffffff'>$vehiculoData->n_poliza</td>
			<tr><td bgcolor='ffffff'>Nombre Aseguradora:</td><td bgcolor='ffffff'>$vehiculoData->aseguradora_nombre</td>
			<td bgcolor='ffffff'>Linea de asistencia:</td><td bgcolor='ffffff'>$vehiculoData->linea_asistencia</td>";
			$htmlTabla .= "<tr><td bgcolor='ffffff'><a href='views/subviews/modal_arbol/arbol_protocolo_line.php' target='_blank' data-toggle='modal' data-target='#myModal'>LINK PROTOCOLO LINEAS DE ATENCION</a></td></tr>
			
			</tr>
			</tr>
			
			</table>";
		}
	}
	if ($Sin->pqr_asociado != 0) {
		$sql = "SELECT pqso.fecha,pqso.descripcion,est.nombre estado, est.color_co color FROM pqr_solicitud pqso 
				LEFT JOIN pqr_estado est ON  pqso.estado = est.id
				WHERE pqso.id = $Sin->pqr_asociado";


		$consultaPqr = qo($sql);
		$varPqr = "<style>
				 .rojo{
					 color:red;
				 }
				 </style>
				 <tr><td class='rojo'>Hay un PQR en el siniestro:</td> <td  width='300'><b>Numero: $Sin->pqr_asociado</b></td>
				             <tr><td class='rojo'>Fecha PQR:</td> <td  width='300'><b>$consultaPqr->fecha</b></td>
							 <tr><td class='rojo'>Descripcion PQR:</td> <td bgcolor='ffffff' width='300'><b>$consultaPqr->descripcion</b></td>
							 <tr><td bgcolor='ffffff'>Estado PQR:</td> <td style='background:$consultaPqr->color' width='300'><b>$consultaPqr->estado</b></td>";
	}
	echo "<body><h3>$NUSUARIO [Nivel $Dagente->nivel] .:. CASO ABIERTO: $id [Ingreso: $Sin->ingreso]</h3>
	<table cellspacing=4>
	<tr><td bgcolor='ffffff'> <img src='$Aseg->emblema_f' border='0' height='30px'> </td>
		<td bgcolor='ffffff'> <b style='font-size:18px'>$Aseg->nombre </b></td>
		<td bgcolor='ffffff' align='center'> <b style='font-size:16px;'>OFICINA:<br>" . menu1("Oficina_caso", "select id,nombre from oficina", $Ofic->id, 0, 'font-size:16px;font-weight:bold;', "onchange='cambia_oficina_caso(this.value);' ") . "</b> </td>
		<td bgcolor='ffffdd' rowspan='2' align='center'> <b>PLACA:</b><br><b style='font-size:20px'>$Sin->placa</b> </td>
		<td rowspan='2' bgcolor='000000'> <img src='$Dagente->foto_f' border='5' height='100px'> </td>
	</tr>
	<tr>
		<td bgcolor='ffffaa'> DIAS: <b style='font-size:20px'>$Sin->dias_servicio</b> </td>
		<td bgcolor='ffffff'> <b style='font-size:16px'>Numero Siniestro: $Sin->numero</b><br><br><center>ESTADO:
		<b style='background-color:$estadoSiniestro->color_co;font-size:14px;" . ($Sin->estado == 1 ? 'color:aa0000;' : '') . "'> $estadoSiniestro->nombre </b></center></td>
		<td bgcolor='ffffff'> Ciudad Original:<br>$Ciuorig</td>
		$varPqr
		</tr>";


	if ($Sin->chevyseguro || $Sin->no_garantia) {
		echo "<tr><td bgcolor='ffff55' colspan=5 align='center'>";
		if ($Sin->chevyseguro) echo " <img src='img/chevyseguro2.png' border='0' height='40px' alt='Chevyseguro' title='Chevyseguro' align='bottom'> <b style='font-size:20px'>ChevySeguro</b> ";
		if ($Sin->no_garantia) echo " <img src='img/nogarantia.png' border='0' height='40px' alt='Servicio Sin Garantia' title='Servicio Sin Garantia' align='bottom'> <b style='font-size:20px'>Servicio SIN Garantia</b> ";
		echo "</td></tr>";
	}
	if ($Sin->bco_occidente)
		echo "<tr><td bgcolor='ffff55' colspan=5 align='center'> <img src='img/banco_de_occidente.png' border='0' height='40px' alt='Banco de Occidente' title='Banco de Occidente' align='bottom'> <b style='font-size:20px'>Banco de Occidente</b> </td></tr>";
	if ($Especial = qo("select * from placa_especial where placa='$Sin->placa'")) {
		echo "<tr><td bgcolor='ffff55' colspan=5 align='center'><a class='info'><img src='img/especial.png' height='100px'>$Especial->descripcion<span style='width:400px'>$Especial->condicion</span></a></td></tr>";
	}
	if ($Ofic->envio_adjudicacion) {
		$varCheked = "checked";
	} else {
		$varCheked = "";
	}



	echo "</table>
	Datos de Contacto:
	<table><tr><td bgcolor='ffffff'>Asegurado:</td><td bgcolor='ffffff'><b style='font-size:14px'>$Sin->asegurado_nombre</b></td></tr>
	<tr><td bgcolor='ffffff'>Declarante:</td><td bgcolor='ffffff'><b style='font-size:14px'>$Sin->declarante_nombre</b></td><td bgcolor='ffffff'>Tels:<b style='font-size:14px'>$Sin->declarante_telefono | $Sin->declarante_tel_resid | $Sin->declarante_tel_ofic | $Sin->declarante_celular | $Sin->declarate_tel_otro | $Sin->declarante_email</b></td></tr>
	<tr><td bgcolor='ffffff'>Conductor:</td><td bgcolor='ffffff'>$Sin->conductor_nombre</td><td bgcolor='ffffff'><b style='font-size:14px'>$Sin->declarante_telefono | $Sin->conductor_tel_resid | $Sin->conductor_tel_ofic | $Sin->conductor_celular | $Sin->conductor_tel_otro</b></td></tr>
	
	
	<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css'>
	<script src='https://code.jquery.com/jquery-3.2.1.slim.min.js'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js'  ></script>
	<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' ></script>
	<tr><td bgcolor='ffffff'>Datos de ciudad:</td><td bgcolor='ffffff'><button type='button' class='btn btn-primary' data-toggle='modal' data-target='#exampleModal'>Ver datos</button></td></tr>
	
	<div class='modal fade' id='exampleModal' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
  <div class='modal-dialog' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='exampleModalLabel'>Datos ciudad siniestro</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <h4>Inicio</h4>
		<b>Contacto de ciudad:</b> $Ofic->contacto<br>
		<b>Tel&eacute;fono de contacto:</b> $Ofic->telefono<br>
		<hr>
		<b>Nombre de ciudad:</b> $Ofic->nombre<br>
		<b>Direccion:</b> $Ofic->direccion<br>
		<h4>Horario</h4>
		<b>Hora inicial:</b> $Ofic->hora_inicial<br>
		<b>Hora final:</b> $Ofic->hora_final<br>
		<b>Pico y placa:</b> $Ofic->picoyplaca<br>
		<b>Concurrecia de citas:</b> $Ofic->concurrencia<br>
		<b>Restriccion Almuerzo:</b> $Ofic->restriccion_almuerzo<br>
		<b>Concurrencia de citas a la hora del almuerzo:</b> $Ofic->concurrenciaa
		
		</div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary' data-dismiss='modal'>Cerrar</button>
        
      </div>
    </div>
  </div>
</div>
	
	";

	if ($Sin->actualizacion_aseg) echo "<tr><td bgcolor='ffffff'>Actualizaciones:</td><td bgcolor='ffffff' colspan='4'>" . nl2br($Sin->actualizacion_aseg) . "</td></tr>";
	echo "</table>";
	echo $htmlTablaOffice;
	echo $htmlBotonCliente;
	echo $htmlTabla;

	if ($Sin->tipo_caja != '') {
		if ($Sin->tipo_caja == 'AUTOMATICO') {
			$option = "<option selected value='AUTOMATICO'>AUTOMATICO</option>
					   <option value='MANUAL'>MANUAL</option>
					   <option value='INDIFERENTE '>INDIFERENTE </option>";
		} else {
			$option = "<option value='AUTOMATICO'>AUTOMATICO</option>
					   <option selected  value='MANUAL'>MANUAL</option>
					   <option value='INDIFERENTE '>INDIFERENTE </option>";
		}
	} else {
		$option = "
	<option value='AUTOMATICO'>AUTOMATICO</option>
	<option value='MANUAL'>MANUAL</option>
	<option value='INDIFERENTE '>INDIFERENTE </option>
	";
	}
	if ($Cs = q("(SELECT a.id,a.asunto,a.fecha_envio, b.usuario, a.siniestro,a.respuesta FROM correo a, usuario_callcenter b WHERE a.usuario=b.usuario AND a.siniestro=$id)  UNION
			(SELECT a.id,a.asunto,a.fecha_envio, b.usuario, a.siniestro,a.respuesta FROM correo a, usuario_desarrollo b WHERE a.usuario=b.usuario AND a.siniestro=$id) ORDER BY id DESC ")) {
		echo "<hr>
				<h5>Correos enviados</h5>
				
				<table border cellspacing='0'><tr>
					<th>Asunto</th>
					<th>Siniestro</th>
					<th>Usuario</th>
					<th>Fecha envio</th>
					<th>Respuesta Coordinador</th>
					<th>Ver</th>
					</tr>";
		while ($cs = mysql_fetch_object($Cs)) // pinta cada registro de la cola de proceso de call center
		{
			// (q("select * from usuario_callcenter where id=$cs->usuario")
			// q("select * from usuario_desarrollo where id=$cs->usuario")

			echo "<tr>
					<td>$cs->asunto</td>
					<td>$cs->siniestro</td>
					<td>$cs->usuario</td>
					<td>$cs->fecha_envio</td>
					<td>$cs->respuesta</td>
					<td><a style='cursor:pointer;color:blue;' onclick='vercorreo($cs->id);'>[+]</a></td>
					</tr>";
		}
		echo "</table>";
	}
	echo "<td bgcolor='ffffff' align='center'><a onclick='modalcorreos();' style='cursor:pointer'><img src='img/icono_correos_call.png' height='80'><br>Enviar un correo</a></td><hr>";

	echo "<br>Observaciones<br>
	<textarea cols=170 rows=10 readonly style='background-color:ffffff'>$Sin->observaciones</textarea><br>
	Adicionar Observaciones: <input id='nuevas_observaciones' type='text' size='120' style='font-size:12px'><br>
	Seleccione tipo de caja:
	<br>
	<select name='tipo_caja' id='tipo_caja'>
	<option value='Seleccione'>Seleccione</option>
   $option
	</select>
	<br>";

	if ($Sin->estado == '5') {
		if ($Sin->fecha_solicitud_usuario == '') {
			echo "Fecha solicitud usuario: <br> <input id='fecha_solicitud_usuario' type='date' size='120' value='$Sin->fecha_solicitud_usuario' style='font-size:12px'><br>";
		}
		else{
			echo "Fecha solicitud usuario: <br> <input id='fecha_solicitud_usuario' type='date' readonly disabled size='120' value='$Sin->fecha_solicitud_usuario' style='font-size:12px'><br>";
		}
	}
	else {
		echo "<input id='fecha_solicitud_usuario' type='date' hidden readonly disabled size='120' value='$Sin->fecha_solicitud_usuario' style='font-size:12px'>";
	}

	echo "
	<br>
	<br>
	
	<input type='button' value=' GUARDAR ' style='font-size:14px;font-weight:bold' onclick='adicionar_observaciones();'>
	<hr><iframe name='Control_caso' id='Control_caso' style='visibility:visible' width='800' height='180' frameborder='no' src='zcallcenter2.php?Acc=control_caso&id=$id&estado=$Sin->estado'></iframe>
	<iframe name='Oculto_presentar_caso' id='Oculto_presentar_caso' style='visibility:hidden' width='1' height='1'></iframe>
	</body>";
}

function control_caso()
{
	global $id, $USUARIO, $NUSUARIO, $IDUSUARIO, $estado, $tokenNick, $tokenPeril, $tokenUser;
	$Sin = qo("Select * from siniestro where id=$id");

	$Aseg = qo("select * from aseguradora where id=$Sin->aseguradora");
	$Ofic = qo("select * from oficina where ciudad=$Sin->ciudad");
	$Ciuorig = qo1("select concat(departamento,' - ',nombre) from ciudad where codigo='$Sin->ciudad_original' ");
	$ubicacion = qo("select vehiculo from ubicacion where id = $Sin->ubicacion");

	if (isset($ubicacion->vehiculo)) {
		$vehiculoData = qo("select v.tipo_caja,v.placa,s.n_poliza,s.aseguradora_nombre,s.linea_asistencia,l.nombre nlinea
							FROM vehiculo v
							inner join seguros s on v.n_poliza = s.id
							inner join linea_vehiculo l on v.linea = l.id  
							where v.id=$ubicacion->vehiculo ");
	}



	$url_normla = "http://ctrl.aoacolombia.com/?#/home&ciudad_siniestro=$Ofic->nombre&declarante_ciudad=$Ciuorig&declarante_email=$Sin->declarante_email&declarante_telefono=$Sin->declarante_telefono&declarante_nombre=$Sin->declarante_nombre&tokenNick=$tokenNick&siniestro=$Sin->numero&tokenPeril=$Perfil->idnombre&tokenUser=$NUSUARIO";
	$url_base = base64_encode($url_normla);



	html();

	$Perfil = qo("select * from usuario where id=" . $USUARIO);

	$Agente = qo("select * from usuario_callcenter where id=$IDUSUARIO");
	echo "<script language='javascript'> 
		 function ingresar_novedad(dato) {if(confirm('Desea ingresar una novedad del siniestro $id?')) 
			    window.open('http://ctrl.aoacolombia.com/?#/home&url=$url_base&ciudad_siniestro=$Ofic->nombre&declarante_ciudad=$Ciuorig&declarante_email=$Sin->declarante_email&declarante_telefono=$Sin->declarante_telefono&declarante_nombre=$Sin->declarante_nombre&tokenNick=$tokenNick&siniestro=$Sin->numero&tokenPeril=$Perfil->idnombre&tokenUser=$NUSUARIO');
		
			}</script>";
	if ($Agente->nivel == 1) {
		echo "<script language='javascript'> 
		
				function contacto_exitoso() { window.open('zcallcenter2.php?Acc=caso_contacto_exitoso&id=$id','_self'); }
				function contacto_tercera() { window.open('zcallcenter2.php?Acc=caso_contacto_tercera&id=$id','_self'); }
				function buzon() { window.open('zcallcenter2.php?Acc=caso_buzon_voz&id=$id','_self'); }
				function informacion_erronea() { window.open('zcallcenter2.php?Acc=caso_info_erronea&id=$id','_self'); }
				function reactivacion() { console.log('si entra');window.open('zcallcenter2.php?Acc=reactivar_caso&id=$id','_self');}
				function solicitar_reactivacion() { parent.solicitar_reactivacion(); }
				function re_comenzar() {parent.re_comenzar();}
				function actualizar_email() {modal('zcallcenter2.php?Acc=caso_actualizar_email&id=$id','actualizar');}
				function re_enviar_correo(id) {modal('zcallcenter2.php?Acc=caso_re_envia_correo_preadjudicacion&id='+id,0,0,400,800,'reenv');}
				function escalar(id) {window.open('zcallcenter2.php?Acc=caso_priorizar&id='+id,'Oculto_asignacion');}
				function cerrar_caso(id) {window.open('zcallcenter2.php?Acc=cerrar_caso&id='+id,'Oculto_asignacion');}
				function consulta_general(id) {window.open('zcallcenter2.php?Acc=consulta_general&id='+id,'Oculto_asignacion');}
				function no_adjudica() { window.open('zcallcenter2.php?Acc=caso_no_adjudica&id=$id','_self'); }
				function envio_email_inicial() {window.open('zcallcenter2.php?Acc=envio_email_inicial&id=$id','Oculto_asignacion')}
				function marcacion_numero_alterno(){window.open('zcallcenter2.php?Acc=marcacion_numero_alterno&id=$id','_self');}
			</script>
			<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
			<table align='center' cellspacing='10' bgcolor='ffffff'><tr>";
		if ($estado == 5 /* Pendiente */) {
			if ($Cola2 = qo("select * from call2cola2 where siniestro=$id and aceptado='0' and estado='0' "))   // SI ESTA EN PRE-ADJUDICACION Y NO HA ACEPTADO
				echo "<td colspan=3><h3>Este caso se encuentra en estado de espera de aceptacion de T&eacute;rminos y Condiciones.</h3></td></tr><tr>
					<td bgcolor='ffffff' align='center'><a onclick='actualizar_email();' style='cursor:pointer'><img src='img/actualizacion.png' height='80'><br>Actualizacion del correo electr&oacute;nico</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='re_enviar_correo($Cola2->id);' style='cursor:pointer'><img src='img/preadjudicacion.png' height='80'><br>Reenviar el correo de Pre-Adjudicacion</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='consulta_general($Cola2->id);' style='cursor:pointer'><img src='img/chat.png' height='80'><br>Consulta General</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='ingresar_novedad();' style='cursor:pointer'><img src='http://app.aoacolombia.com/img/Car-Repair-icon.png' height='80'><br>Ingresar novedad </a></td>";


			elseif ($Cola2 = qo("select * from call2cola2 where siniestro=$id and aceptado='1' and estado='0' "))  // SI ESTA EN PRE-ADJUDICACION PERO YA ACEPTO
				echo "<td colspan=2><h3>Este caso se encuentra en cola nivel 2 - Adjudicacion del Veh&iacute;culo.</h3></td></tr><tr>
					<td bgcolor='ffffff' align='center'><a onclick='escalar($Cola2->id);' style='cursor:pointer'><img src='img/escalar.png' height='80'><br>Priorizar llamada en Nivel 2</a></td>";
			elseif ($Cola3 = qo("select * from call2cola3 where siniestro=$id"))  // SI ESTA EN COLA3
				echo "<td colspan=2><h3>Este caso se encuentra en cola nivel 3 - Adjudicacion del Veh&iacute;culo.</h3></td></tr><tr>
					<td bgcolor='ffffff' align='center'><a onclick='cerrar_caso($Cola3->id);' style='cursor:pointer'><img src='img/escalar.png' height='80'><br>Cerrar este caso</a></td>";
			else  // SI SIGUE PENDIENTE EN COLA 1
			{
				echo "<td bgcolor='ffffff' align='center'><a onclick='contacto_exitoso();' style='cursor:pointer'><img src='img/contacto_call_asegurado.png' height='80'><br>Contacto Exitoso</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='contacto_tercera();' style='cursor:pointer'><img src='img/contacto_call_3_persona.png' height='80'><br>Contacto Tercera Persona</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='buzon();' style='cursor:pointer'><img src='img/buzon.png' height='80'><br>Buz&oacute;n de Voz</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='informacion_erronea();' style='cursor:pointer'><img src='img/informacion_erronea.png' height='80'><br>Informacion Err&oacute;nea</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='ingresar_novedad();' style='cursor:pointer'><img src='http://app.aoacolombia.com/img/Car-Repair-icon.png' height='80'><br>Ingresar novedad </a></td>";

				// BUSQUEDA DE ACUMULADO DE BUZONES DE VOZ Y DE TERCERA PERSONA PARA MOSTRAR EL BOTON DE NO ADJUDICACION
				$Cantidad_buzones = qo1("select count(id) from seguimiento where siniestro=$id and tipo in (12,13) ");
				$Contacto_exitoso = qo("select * from seguimiento where siniestro=$id and tipo=3");
				if (($Cantidad_buzones > 3 && !$Contacto_exitoso) || ($Cantidad_buzones > 6))
					echo "<td bgcolor='ffffff' align='center'><a onclick='no_adjudica();' style='cursor:pointer'><img src='img/no_adjudicacion.png' height='80'><br>No Adjudicacion<br>($Cantidad_buzones buzones)</a></td>";
				else
					echo "<td bgcolor='ffffff' align='center'><b>$Cantidad_buzones Buzones<br>" . ($Contacto_exitoso ? "Contacto Exitoso: $Contacto_exitoso->fecha $Contacto_exitoso->hora" : "") . "</b></td>";
				// NUEVO BOTON MARCAR NUMERO ALTERNO
				echo "<td bgcolor='ffffdd' align='center'><a onclick='marcacion_numero_alterno();' style='cursor:pointer'><img src='img/cambio_llamada.png' height='80'><br>Marcar N&uacute;mero Alterno</a></td>";
			}

			echo "</tr></table>
					<iframe name='Oculto_asignacion' id='Oculto_asignacion' style='visibility:hidden' width='1' height='1'></iframe>";
		}
		if ($estado == 1) {
			$Sin = qo("Select ingreso from siniestro where id=$id");
			$Dias = dias($Sin->ingreso, date('Y-m-d'));
			if ($Dias <= 30) echo "<td bgcolor='ffffff' align='center' style='font-size:14px'>
			NUMERO DE DIAS DEL CASO: <B>$Dias</B> <br>PERMITIDO REACTIVAR.</td><td bgcolor='ffffff' ALIGN='CENTER'>
			<a onclick='reactivacion();' style='cursor:pointer;font-size:14px;'><img src='img/reactivacion2.png' border='0' height='100'><BR>Re Activar Caso</a></td>";
			else echo "<td bgcolor='ffffff' align=' center'style='font-size:14px'>
			NUMERO DE DIAS DEL CASO: <B>$Dias</B>.</td><td bgcolor='ffffff' ALIGN='CENTER'>
			<a onclick=\"modal('zcallcenter2.php?Acc=solicita_reactivacion&id=$S->id',0,0,500,500,'sra');\" style='cursor:pointer;font-size:14px;'><img src='img/reactivacion3.png' border='0' height='80'>
			<br>Solicitar Re-Activacion de Caso</a></td>
			<td bgcolor='ffffff' align='center'><a onclick='ingresar_novedad();' style='cursor:pointer'><img src='http://app.aoacolombia.com/img/Car-Repair-icon.png' height='80'><br>Ingresar novedad </a></td>";
		}
		echo "</tr></table></body>";
	}
	if ($Agente->nivel == 2) {

		echo "<script language='javascript'>
				function asignar_vehiculo(){window.open('zcallcenter2.php?Acc=asignar_vehiculo&id=$id','Oculto_asignacion');}
				function sobreagendar(){window.open('zcallcenter2.php?Acc=sobreagendar&id=$id',0,0,400,800,'Oculto_asignacion');}
				function compromiso() {window.open('zcallcenter2.php?Acc=caso_compromiso" . ($_SESSION['nivelcaso'] == 2 ? "2" : "") . "&id=$id','_self');}
				function remarcacion() { window.open('zcallcenter2.php?Acc=caso_buzon2_voz&id=$id','_self');}
				function escalar(){ if(confirm('Desea escalar este caso?')) window.open('zcallcenter2.php?Acc=escalar_caso&id=$id','_self');}
				function re_comenzar() {parent.re_comenzar();}
				function actualizar_email() {modal('zcallcenter2.php?Acc=caso_actualizar_email&id=$id','actualizar');}
				function re_enviar_correo(id) {modal('zcallcenter2.php?Acc=caso_re_envia_correo_preadjudicacion&id='+id,0,0,400,800,'reenv');}
				function no_adjudica() { window.open('zcallcenter2.php?Acc=caso_no_adjudica&id=$id','_self'); }
				function cerrar_caso(id) {window.open('zcallcenter2.php?Acc=cerrar_caso&id='+id,'Oculto_asignacion');}

		        function cerrar_caso(idc) {
					

					
					if(confirm('Desea cerrar el caso abierto? '+idc)) 
					
				window.open('zcallcenter2.php?Acc=cerrar_caso_abierto_novedad&idc='+idc,'_self');
			
								window.history.go(-1);
                               window.history.back();
		      	}
				 
				 function ingresar_novedad(dato) {
					
					 
					 if(confirm('Desea  ingresar una novedad del siniestro $vehiculoData->placa $id?')) 
			    window.open('http://ctrl.aoacolombia.com/?#/home&url=$url_base&ciudad_siniestro=$Ofic->nombre&declarante_ciudad=$Ciuorig&declarante_email=$Sin->declarante_email&declarante_telefono=$Sin->declarante_telefono&declarante_nombre=$Sin->declarante_nombre&tokenNick=$tokenNick&siniestro=$Sin->numero&tokenPeril=$Perfil->idnombre&tokenUser=$NUSUARIO&placa=$vehiculoData->ubicacion&ubicacion=$ubicacion->vehiculo');

			
			
			}

		    	function actualizar_infofin() {modal('zcallcenter2.php?Acc=pide_datos_financieros&ids=$id&cerrando=1',0,0,800,800,'pdf');}
				function preadjudica() { window.open('zcallcenter2.php?Acc=caso_preadjudica&id=$id','_self'); }
				function actualizar_datos() {modal('zcallcenter2.php?Acc=caso_actualizar_datos&id=$id','actualizar');}
				function informacion_erronea() { window.open('zcallcenter2.php?Acc=caso_info_erronea&id=$id','_self'); }
				function marcacion_numero_alterno(){window.open('zcallcenter2.php?Acc=marcacion_numero_alterno&id=$id','_self');}
				function asignar_transporte_vip(){parent.asignar_transporte_vip();}
			</script>
			<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
			<table align='center' cellspacing='10' bgcolor='ffffff'><tr>";
		if ($Sin->estado == 5 /* Pendiente */) {
			if ($Cola2 = qo("select * from call2cola2 where siniestro=$id and aceptado='0' and estado='0' "))
				echo "<td colspan=2><h3>Este caso se encuentra en estado de espera de aceptacion de T&eacute;rminos y Condiciones.</h3></td></tr><tr>
					<td bgcolor='ffffff' align='center'><a onclick='actualizar_email();' style='cursor:pointer'><img src='img/actualizacion.png' height='80'><br>Actualizacion del correo electr&oacute;nico</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='re_enviar_correo($Cola2->id);' style='cursor:pointer'><img src='img/preadjudicacion.png' height='80'><br>Reenviar el correo de Pre-Adjudicacion</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='ingresar_novedad();' style='cursor:pointer'><img src='http://app.aoacolombia.com/img/Car-Repair-icon.png' height='80'><br>Ingresar novedad </a></td>";


			else {
				if ($Cola3 = qo("select * from call2cola3 where siniestro=$id")) echo "<tr><td colspan=4><h3>Este caso fue escalado al Nivel 3 en $Cola3->fecha</h3></td></tr><tr>";
				if ($Aseg->num_servicios_vip > 0)
					echo "<td bgcolor='ffffff' align='center'><a onclick='asignar_transporte_vip();' style='cursor:pointer'><img src='img/reno_placa_blanca.jpg' height='60'><br>Transporte VIP</a></td>";
				echo "<td bgcolor='ffffff' align='center'><a onclick='asignar_vehiculo();' style='cursor:pointer'><img src='img/adjudicacion1.png' height='60'><br>Adjudicar Vehiculo</a></td>
				<td bgcolor='ffffff' align='center'><a onclick='sobreagendar();' style='cursor:pointer'><img src='img/reasignar.png' height='80'><br>Sobre Agendar</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='compromiso();' style='cursor:pointer'><img src='img/compromiso.png' height='60'><br>Compromiso</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='remarcacion();' style='cursor:pointer'><img src='img/buzon.png' height='60'><br>Buz&oacute;n de Voz - Remarcacion</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='escalar();' style='cursor:pointer'><img src='img/escalar.png' height='60'><br>Escalar Caso</a></td>";

				if ($idc = qo1("select id from call2cola2 where siniestro=$id"))
					echo "<td bgcolor='ffffff' align='center'><a onclick='re_enviar_correo($idc);' style='cursor:pointer'><img src='img/preadjudicacion.png' height='60'><br>Reenviar el correo de Pre-Adjudicacion</a></td>";
				else
					echo "<td bgcolor='ffffff' align='center'><a onclick='preadjudica();' style='cursor:pointer'><img src='img/preadjudicacion.png' height='60'><br>Reenviar el correo de Pre-Adjudicacion</a></td>";
				//	if($_SESSION['nivelcaso']==1)
				echo "<td bgcolor='ffffff' align='center'><a onclick='actualizar_datos();' style='cursor:pointer'><img src='img/actualizacion.png' height='60'><br>Actualizacion de datos</a></td>
								<td bgcolor='ffffff' align='center'><a onclick='informacion_erronea();' style='cursor:pointer'><img src='img/informacion_erronea.png' height='60'><br>Informacion Err&oacute;nea</a></td>";
				// NUEVO BOTON MARCAR NUMERO ALTERNO
				echo "<td bgcolor='ffffdd' align='center'><a onclick='marcacion_numero_alterno();' style='cursor:pointer'><img src='img/cambio_llamada.png' height='80'><br>Marcar N&uacute;mero Alterno</a></td>";
			}
			echo "<td bgcolor='ffffff' align='center'><a onclick='no_adjudica();' style='cursor:pointer'><img src='img/no_adjudicacion.png' height='80'><br>No Adjudicacion</a></td>";
			echo "</tr></table>
					<iframe name='Oculto_asignacion' id='Oculto_asignacion' style='visibility:hidden' width='1' height='1'></iframe>";
		} elseif ($Sin->estado == 3 /* Adjudicado */) {

			echo "<td colspan=2><h3>Este caso se encuentra Adjudicado</h3></td></tr><tr>
					<td bgcolor='ffffff' align='center'><a onclick='actualizar_infofin();' style='cursor:pointer'><img src='img/actualizacion.png' height='80'><br>Actualizacion de la Informacion Financiera</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='ingresar_novedad();' style='cursor:pointer'><img src='http://app.aoacolombia.com/img/Car-Repair-icon.png' height='80'><br>Ingresar novedad </a></td>";
		} elseif ($Sin->estado == 7 /* SERVICIO */) {
			$Aq = qo("select * from usuario_callcenter where id =  $IDUSUARIO");

			$idangete = $Agente->id;

			$A = qo("select * from call2proceso where 
            siniestro='$id' and estado='A' and
			agente=$idangete");



			echo "<td bgcolor='ffffff' align='center'><a onclick='ingresar_novedad();'
			style='cursor:pointer'><img src='http://app.aoacolombia.com/img/Car-Repair-icon.png' height='80'><br>Ingresar novedad </a></td>
			
			<td bgcolor='ffffff' align='center'><a onclick='cerrar_caso($A->id);' style='cursor:pointer'><img src='http://app.aoacolombia.com/img/Close-icon.png' height='80'><br>Cerrar el caso</a></td>
			";
		} elseif ($Sin->estado == 1 /* Pendiente */) {
			$Dias = dias($Sin->ingreso, date('Y-m-d'));
			if ($Dias <= 30) echo "<td bgcolor='ffffff' align='center' style='font-size:14px'>
			NUMERO DE DIAS DEL CASO: <B>$Dias</B> <br>PERMITIDO REACTIVAR.</td><td bgcolor='ffffff' ALIGN='CENTER'>
			<a onclick=\"modal('zcallcenter2.php?Acc=solicita_reactivacion&id=$Sin->id',0,0,500,500,'sra');\" style='cursor:pointer;font-size:14px;'><img src='img/reactivacion2.png' border='0' height='100'><BR>Re Activar Caso</a></td>";
			else echo "<td bgcolor='ffffff' align=' center'style='font-size:14px'>
			NUMERO DE DIAS DEL CASO: <B>$Dias</B>.</td><td bgcolor='ffffff' ALIGN='CENTER'>
			<a onclick=\"modal('zcallcenter2.php?Acc=solicita_reactivacion&id=$Sin->id',0,0,500,500,'sra');\" style='cursor:pointer;font-size:14px;'><img src='img/reactivacion3.png' border='0' height='80'><br>Solicitar Re-Activacion de Caso</a></td>
				<td bgcolor='ffffff' align='center'><a onclick='ingresar_novedad();' style='cursor:pointer'><img src='http://app.aoacolombia.com/img/Car-Repair-icon.png' height='80'><br>Ingresar novedad </a></td>
			";
		} else
			echo "<b>EL ESTADO DEBERIA ESTAR COMO PENDIENTE, FAVOR SOLICITAR SOPORTE A DIRECCION DE CALL CENTER</b>";
	}
	if ($Agente->nivel >= 3) {
		$Sin = qo("Select * from siniestro where id=$id");
		$Aseg = qo("select * from aseguradora where id=$Sin->aseguradora");
		$Ofic = qo("select * from oficina where ciudad=$Sin->ciudad");
		echo "<script language='javascript'>
				function asignar_vehiculo(){window.open('zcallcenter2.php?Acc=asignar_vehiculo&id=$id','Oculto_asignacion');}
				function compromiso() {window.open('zcallcenter2.php?Acc=caso_compromiso3&id=$id','_self');}
				function remarcacion() { window.open('zcallcenter2.php?Acc=caso_buzon3_voz&id=$id','_self');}
				
                function ingresar_novedad(dato) {if(confirm('Desea ingresar una novedad del siniestro $id?')) 
			    window.open('http://ctrl.aoacolombia.com/?#/home?tokenNick=$tokenNick&tokenPeril=$Perfil->idnombre&tokenUser=$NUSUARIO');}

			
				function escalar(){ if(confirm('Desea escalar este caso?')) window.open('zcallcenter2.php?Acc=escalar_caso3&id=$id','_self');}
				function re_comenzar() {parent.re_comenzar();}
				function actualizar_email() {modal('zcallcenter2.php?Acc=caso_actualizar_email&id=$id','actualizar');}
				function re_enviar_correo(id) {modal('zcallcenter2.php?Acc=caso_re_envia_correo_preadjudicacion&id='+id,0,0,400,800,'reenv');}
				function no_adjudica() { window.open('zcallcenter2.php?Acc=caso_no_adjudica&id=$id','_self'); }
				function aceptacion_condiciones(dato) {if(confirm('Desea Aceptar Terminos y Condiciones?')) window.open('zcallcenter2.php?Acc=aceptacion_condiciones&id='+dato,'Oculto_asignacion');}
				function actualizar_infofin() {modal('zcallcenter2.php?Acc=pide_datos_financieros&ids=$id&cerrando=1',0,0,800,800,'pdf');}
				function marcacion_numero_alterno(){window.open('zcallcenter2.php?Acc=marcacion_numero_alterno&id=$id','_self');}
				function asignar_transporte_vip(){parent.asignar_transporte_vip();}
				
			</script>
			<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
			<table align='center' cellspacing='10' bgcolor='ffffff'><tr>";

		if ($Sin->estado == 5 /* Pendiente */) {
			if ($Cola2 = qo("select * from call2cola2 where siniestro=$id and aceptado='0' and estado='0' "))
				echo "<td colspan=6><h3>Este caso se encuentra en estado de espera de aceptacion de T&eacute;rminos y Condiciones.</h3></td></tr><tr>
					<td bgcolor='ffffff' align='center'><a onclick='actualizar_email();' style='cursor:pointer'><img src='img/actualizacion.png' height='80'><br>Actualizacion del correo electr&oacute;nico</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='compromiso();' style='cursor:pointer'><img src='img/compromiso.png' height='80'><br>Compromiso</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='remarcacion();' style='cursor:pointer'><img src='img/buzon.png' height='80'><br>Buz&oacute;n de Voz - Remarcacion</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='re_enviar_correo($Cola2->id);' style='cursor:pointer'><img src='img/preadjudicacion.png' height='80'><br>Reenviar el correo de Pre-Adjudicacion</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='aceptacion_condiciones($Cola2->id);' style='cursor:pointer'><img src='img/aceptacionterminos.png' height='80'><br>Aceptacion de T&eacute;rminos y Condiciones</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='ingresar_novedad();' style='cursor:pointer'><img src='http://app.aoacolombia.com/img/Car-Repair-icon.png' height='80'><br>Ingresar novedad </a></td>";
			else {
				if ($Cola3 = qo("select * from call2cola3 where siniestro=$id"))
					echo "<tr><td colspan=4><h3>Este caso fue escalado al Nivel 3 en $Cola3->fecha</h3></td></tr><tr>";
				if ($Aseg->num_servicios_vip > 0)
					echo "<td bgcolor='ffffff' align='center'><a onclick='asignar_transporte_vip();' style='cursor:pointer'><img src='img/reno_placa_blanca.jpg' height='60'><br>Transporte VIP</a></td>";
				echo "<td bgcolor='ffffff' align='center'><a onclick='asignar_vehiculo();' style='cursor:pointer'><img src='img/adjudicacion1.png' height='80'><br>Adjudicar Vehiculo</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='compromiso();' style='cursor:pointer'><img src='img/compromiso.png' height='80'><br>Compromiso</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='remarcacion();' style='cursor:pointer'><img src='img/buzon.png' height='80'><br>Buz&oacute;n de Voz - Remarcacion</a></td>
					<td bgcolor='ffffff' align='center'><a onclick='escalar();' style='cursor:pointer'><img src='img/escalar.png' height='80'><br>Escalar Caso</a></td>
						<td bgcolor='ffffff' align='center'><a onclick='ingresar_novedad();' style='cursor:pointer'><img src='http://app.aoacolombia.com/img/Car-Repair-icon.png' height='80'><br>Ingresar novedad </a></td>
					";
				// NUEVO BOTON MARCAR NUMERO ALTERNO
				echo "<td bgcolor='ffffdd' align='center'><a onclick='marcacion_numero_alterno();' style='cursor:pointer'><img src='img/cambio_llamada.png' height='80'><br>Marcar N&uacute;mero Alterno</a></td>";
			}
			echo "<td bgcolor='ffffff' align='center'><a onclick='no_adjudica();' style='cursor:pointer'><img src='img/no_adjudicacion.png' height='80'><br>No Adjudicacion</a></td>";
			echo "</tr></table>
					<iframe name='Oculto_asignacion' id='Oculto_asignacion' style='visibility:hidden' width='1' height='1'></iframe>";
		} elseif ($Sin->estado == 3 /* Adjudicado */) {
			echo "<td colspan=2><h3>Este caso se encuentra Adjudicado</h3></td></tr><tr>
					<td bgcolor='ffffff' align='center'><a onclick='actualizar_infofin();' style='cursor:pointer'><img src='img/actualizacion.png' height='80'><br>Actualizacion de la Informacion Financiera</a></td>";
		} else
			echo "<b>EL ESTADO DEBERIA ESTAR COMO PENDIENTE, FAVOR SOLICITAR SOPORTE A DIRECCION DE CALL CENTER</b>";
	}
}


function asignar_transporte_vip()
{
	global $id, $USUARIO, $NUSUARIO, $IDUSUARIO;
	$Dagente = qo("select * from usuario_callcenter where id=$IDUSUARIO");
	html();
	$Sin = qo("Select * from siniestro where id=$id");
	$Aseg = qo("select * from aseguradora where id=$Sin->aseguradora");
	$Ofic = qo("select * from oficina where ciudad=$Sin->ciudad");
	$Estado = qo("select * from estado_siniestro where id=$Sin->estado");
	echo "<body>
		<h3 align='center'>TRANSPORTE VIP</h3>
		<table cellspacing=4>
			<tr><td bgcolor='ffffff'> <img src='$Aseg->emblema_f' border='0' height='30px'> </td>
				<td bgcolor='ffffff'> <b style='font-size:18px'>$Aseg->nombre </b></td>
				<td bgcolor='ffffff' align='center'> <b style='font-size:16px;'>OFICINA:<br>" . menu1("Oficina_caso", "select id,nombre from oficina", $Ofic->id, 0, 'font-size:16px;font-weight:bold;', "onchange='cambia_oficina_caso(this.value);' ") . "</b> </td>
				<td bgcolor='ffffdd' rowspan='2' align='center'> <b>PLACA:</b><br><b style='font-size:20px'>$Sin->placa</b> </td>
				<td rowspan='2' bgcolor='000000'> <img src='$Dagente->foto_f' border='5' height='100px'> </td>
			</tr>
			<tr>
				<td bgcolor='ffffaa'> DIAS: <b style='font-size:20px'>$Sin->dias_servicio</b> </td>
				<td bgcolor='ffffff'> <b style='font-size:16px'>Numero Siniestro: $Sin->numero</b><br><br><center>ESTADO:
				<b style='background-color:ddddff;font-size:14px;" . ($Sin->estado == 1 ? 'color:aa0000;' : '') . "'> $Estado->nombre </b></center></td>
				<td bgcolor='ffffff'> Ciudad Original:<br>$Ciuorig</td></tr>";
	if ($Sin->chevyseguro || $Sin->no_garantia) {
		echo "<tr><td bgcolor='ffff55' colspan=5 align='center'>";
		if ($Sin->chevyseguro) echo " <img src='img/chevyseguro2.png' border='0' height='40px' alt='Chevyseguro' title='Chevyseguro' align='bottom'> <b style='font-size:20px'>ChevySeguro</b> ";
		if ($Sin->no_garantia) echo " <img src='img/nogarantia.png' border='0' height='40px' alt='Servicio Sin Garantia' title='Servicio Sin Garantia' align='bottom'> <b style='font-size:20px'>Servicio SIN Garantia</b> ";
		echo "</td></tr>";
	}
	if ($Sin->bco_occidente)
		echo "<tr><td bgcolor='ffff55' colspan=5 align='center'> <img src='img/banco_de_occidente.png' border='0' height='40px' alt='Banco de Occidente' title='Banco de Occidente' align='bottom'> <b style='font-size:20px'>Banco de Occidente</b> </td></tr>";
	if ($Especial = qo("select * from placa_especial where placa='$Sin->placa'")) {
		echo "<tr><td bgcolor='ffff55' colspan=5 align='center'><a class='info'><img src='img/especial.png' height='100px'>$Especial->descripcion<span style='width:400px'>$Especial->condicion</span></a></td></tr>";
	}
	echo "</table>
		<h4>PROGRAMACION DE SERVICIOS - TRANSPORTE VIP</h4>
		<iframe name='tablero_programacion' id='tablero_programacion' width='100%' height='60%' ></iframe>
		<form action='https://www.aoasemuevecontigo.com/zgenerador_servicios_vr.php' target='tablero_programacion' method='POST' name='forma' id='forma'>
			<input type='hidden' name='id_siniestro' value='$id'>
			<input type='hidden' name='nombre' value='$Sin->asegurado_nombre'>
			<input type='hidden' name='identificacion' value='$Sin->asegurado_id'>
			<input type='hidden' name='email' value='$Sin->declarante_email'>
			<input type='hidden' name='direccion' value='$Sin->declarante_direccion'>
			<input type='hidden' name='celular' value='$Sin->declarante_celular'>
			<input type='hidden' name='num_servicios_vip' value='$Aseg->num_servicios_vip'>
			<input type='hidden' name='dias_servicio' value='$Sin->dias_servicio'>
			<input type='hidden' name='placa' value='$Sin->placa'>
		</form>
		<script language='javascript'>document.forma.submit();</script>
	</body>";
}

function asignar_transporte_vip2()
{
	global $app, $IDUSUARIO, $NUSUARIO;
	include('inc/gpos.php');
	$Codigo_seguimiento = 29; // Adjudicacion de Servicio VIP
	// recibe id_siniestro y Servicios_Asignados desde la plataforma de movilidad
	q("UPDATE siniestro SET estado=3,observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: ADJUDICACION VIP Servicios: $Servicios_Asignados\") WHERE id=$id_siniestro");
	$Ahora = date('Y-m-d H:i:s');
	$Hora = date('H:i:s');
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id_siniestro,'$NUSUARIO','$Ahora','$Hora','SE ASIGNA SERVICIO VIP Servicios: $Servicios_Asignados',$Codigo_seguimiento)");
	q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$id_siniestro' and estado='A' ");
	if ($idcola2 = qo1("select id from call2cola2 where siniestro=$id_siniestro")) {
		q("update call2cola2 set estado='2' where idsiniestro=$id_siniestro");
		graba_bitacora('call2cola2', 'M', $idcola2, 'cambia estado a 2');
	}

	echo "<body><script language='javascript'>window.open('zcallcenter2.php','destino');</script></body>";
}

function cerrar_caso()
{
	global $id, $IDUSUARIO;
	$Ahora = date('Y-m-d H:i:s');
	$D = qo("select * from call2cola3 where id=$id");
	q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$D->siniestro' and estado='A' ");
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function consulta_general()
{
	global $id, $IDUSUARIO;
	$Ahora = date('Y-m-d H:i:s');
	$D = qo("select * from call2cola2 where id='$id' ");
	q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$D->siniestro' and estado='A' ");
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function caso_priorizar()
{
	global $id, $IDUSUARIO;
	$Cola = qo("select * from call2cola2 where id='$id'");
	$Luego = date('Y-m-d') . ' ' . aumentaminutos(date('H:i:s'), -30);
	$Ahora = date('Y-m-d H:i:s');
	q("update call2cola2 set fecha_aceptacion='$Luego',estado='0' where id=$id");
	graba_bitacora('call2cola2', 'M', $id, 'cambia fecha de aceptacion y estado a 0');
	q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$Cola->siniestro' and estado='A' ");
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function caso_actualizar_email()
{
	global $id;
	html('ACTUALIZACION DE CORREO');
	$Sin = qo("Select * from siniestro where id=$id");
	echo "<script language='javascript'>
	function validar_actualizacion()
	{
		with(document.forma)
		{
			if(!alltrim(correo.value)) { alert('Debe digitar el correo electr&oacute;nico del asegurado');correo.style.backgroundColor='ffff99';correo.focus();return false;}
			submit();
		}
	}
	function finalizar()
	{
		opener.re_comenzar();window.close();void(null);
	}
	</script><body><script language='javascript'>centrar(500,300);</script>
	<form action='' target='Oculto_actualizacion' method='POST' name='forma' id='forma'>
		Correo electr&oacute;nico: <input type='text' name='correo' id='correo' value='$Sin->declarante_email' size='80' maxlength='200'><br>
		<input type='button' name='continuar' id='continuar' value=' ACTUALIZAR INFORMACION ' onclick='validar_actualizacion()'>
		<input type='hidden' name='Acc' value='caso_actualizar_email_ok'><input type='hidden' name='id' value='$id'>
	</form>
	<iframe name='Oculto_actualizacion' id='Oculto_actualizacion' style='visibility:hidden' width='1' height='1'></iframe>
	</body>";
}

function caso_actualizar_email_ok()
{
	global $id, $correo;
	q("update siniestro set declarante_email='$correo' where id=$id");
	graba_bitacora('siniestro', 'M', $id, 'Actualiza correo declarante_email');
	echo "<body><script language='javascript'>parent.finalizar();</script></body>";
}

function asignar_vehiculo()
{
	global $id, $NUSUARIO, $IDUSUARIO;
	$Agente = qo("select * from usuario_callcenter where id=$IDUSUARIO");
	$Sin = qo("Select * from siniestro where id=$id");
	$Aseg = qo("select * from aseguradora where id=$Sin->aseguradora");
	$Ofic = qo("select * from oficina where ciudad=$Sin->ciudad");
	$_SESSION['Adjudicacion_SINIESTRO'] = $id;
	$_SESSION['Adjudicacion_OFICINA'] = $Ofic->id;
	$_SESSION['Adjudicacion_ASEGURADORA'] = $Aseg->id;
	$_SESSION['Adjudicacion_NIVEL'] = $Agente->nivel;
	html();
	echo "<script language='javascript'>function re_comenzar() {parent.re_comenzar();}</script>
	<body><script language='javascript'>modal('zcontrol_operativo3.php',0,0,700,700,'adjudicacion');</script></body>";
}
function sobreagendar()
{
	global $id,$NUSUARIO,$IDUSUARIO;
	$Sin = qo("Select * from siniestro where id=$id");
	$ciudad = qo("select * from ciudad where codigo='$Sin->ciudad'");
	$aseguradora = qo("select * from aseguradora where id='$Sin->aseguradora'");
	echo "
	<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css' integrity='sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn' crossorigin='anonymous'>
	<body><script language='javascript'>centrar(500,300);</script>
	<div class='container-fluid mt-4'>
	<form action='' target='Oculto_actualizacion' method='POST' name='forma' id='forma'>
		<h2 class='text-center'>SOBRE AGENDAMIENTO</h2>
		Siniestro: <input type='text' readonly class='form-control' name='siniestro' id='correo' value='$Sin->id' size='80' maxlength='200'><br>
		Oficina: <input type='text' readonly class='form-control' name='oficina' id='correo' value='$ciudad->nombre' size='80' maxlength='200'><br>
		Tipo: 
		<select class='form-control' name='tipo'>
			<option disabled selected>seleccione el tipo</option>
			<option>Programado</option>
			<option>Sobreagendado</option>
		</select>
		<br>
		Placa del asegurado: <input type='text' readonly class='form-control' name='placa' id='correo' value='$Sin->placa' size='80' maxlength='200'><br>
		Aseguradora: <input type='text' readonly class='form-control' name='aseguradora' id='correo' value='$aseguradora->nombre' size='80' maxlength='200'><br>
		Fecha: <input type='date' class='form-control' name='fecha' id='correo' size='80' maxlength='200'><br>
		Hora: <input type='time' class='form-control' name='hora' id='correo' size='80' maxlength='200'><br>
		Observaciones: <textarea class='form-control' name='obs' placeholder='Agregue una observacion'></textarea><br>
		<input type='submit' name='continuar' id='continuar' class='btn btn-primary' value=' SOBRE AGENDAR'>
		<input type='hidden' name='Acc' value='sobreagendar_ok'><input type='hidden' name='id' value='$id'>
	</form>
	</div>
	<iframe name='Oculto_actualizacion' id='Oculto_actualizacion' style='visibility:hidden' width='1' height='1'></iframe>
	</body>";
}

function sobreagendar_ok()
{
	global $id,$siniestro,$oficina,$tipo,$placa,$aseguradora,$fecha,$hora,$obs,$NUSUARIO;
	$Sin = qo("Select * from siniestro where id=$id");
	$sobreagendamiento = qo("insert into sobreagendamiento (siniestro,oficina,tipo,placa_asegurado,aseguradora,fecha_reserva,hora,observaciones,agente) values ('$siniestro','$oficina','$tipo','$placa','$aseguradora','$fecha','$hora','$obs','$NUSUARIO')");
	echo "<script>
	alert('$sobreagendamiento');
	</script>";
}

function reactivar_caso()
{
	global $id, $IDUSUARIO, $NUSUARIO;
	q("update siniestro set estado=5,observaciones=concat(observaciones,'\n" . $NUSUARIO . ' ' . date('Y-m-d H:i') . " Se reactiva el caso') where id=$id");
	$H1 = date('Y-m-d');
	$H2 = date('H:i:s');
	$Idn = q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'$H1','$H2','$NUSUARIO','Se reactiva el caso',10)");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona registro');
	$Hoy = date('Y-m-d H:i:s');
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	if ($ID = q("insert into call2proceso (fecha,agente,siniestro,estado) values ('$Hoy','$IDUSUARIO','$id','A')")) {
		q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ('$id','$NUSUARIO','$Fecha','$Hora','Abre Caso',2)");
		q("delete from call2cola1 where siniestro=$id");
		echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
	}
}

function caso_info_erronea()
{
	global $id, $USUARIO, $NUSUARIO, $IDUSUARIO;
	// BUSCA LOS EVENTOS DEL CASO
	$Cantidad_eventos = qo1("select count(id) from seguimiento where siniestro=$id and tipo=4");
	$Siniestro = qo("select * from siniestro where id=$id");
	$Aseguradora = qo("select email_soporte_e,email_copia from aseguradora where id=$Siniestro->aseguradora");
	$Numero_Aviso = $Cantidad_eventos + 1;
	$Email_usuario = usuario('email');
	$Oficina = qo1("select id from oficina where ciudad='$Siniestro->ciudad'");
	$Ciudad = qo1("select t_ciudad('$Siniestro->ciudad')");
	if ($Siniestro->ciudad_original) $Ciudado = qo1("select t_ciudad('$Siniestro->ciudad_original')");
	else $Ciudado = false;
	if ($Siniestro->email_analista) $destino = $Siniestro->email_analista;
	elseif ($Aseguradora->email_soporte_e) $destino = $Aseguradora->email_soporte_e;
	else $destino = false;
	$destino_copia = $Aseguradora->email_copia;
	html();
	echo "<script language='javascript'>
		function enviar_mensaje()
		{
			if(!document.forma.tipificacion.value)
			{
				alert('Debe seleccionar la tipificacion correcta para este caso'); document.forma.tipificacion.style.backgroundColor='ffff44';document.forma.tipificacion.focus();return false;
			}
			if(confirm('Seguro de marcar este caso como INFORMACION ERRONEA?')) document.forma.submit();
		}
		</script>
		<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0'><h3>INFORMACION ERRONEA ($Cantidad_eventos eventos)</h3>
		<iframe name='Oculto_info_erronea' id='Oculto_info_erronea' style='visibility:hidden' width='1' height='1'></iframe>
		<form action='zcallcenter2.php' method='post' target='Oculto_info_erronea' name='forma' id='forma'>
			La informacion erronea es: <input type='text' name='info_erronea1' value='El n&uacute;mero telef&oacute;nico o celular' size=80><br>
			Seleccione la tipificacion correcta para este caso: " . menu1("tipificacion", "select id,nombre from tipifica_seguimiento where tipo='INFORMACION ERRONEA' ", 0, 1) . "
			<input type='hidden' name='Acc' id='Acc' value='caso_info_erronea_ok'><br><br>";
	if ($Siniestro->declarante_email)	echo "<input type='checkbox' name='email_inicial'> <b style='color:blue;font-size:14px;'>Enviar automaticamente un Correo Inicial a la direccion $Siniestro->declarante_email </b><br><br>";
	echo "<input type='button' value=' MARCAR COMO INFORMACION ERRONEA ' style='font-size:16px;font-weight:bold;' onclick='enviar_mensaje()'>
			<input type='hidden' name='id' id='id' value='$id'>
		</form>";
}

function caso_info_erronea_ok()
{
	global $id, $info_erronea1, $NUSUARIO, $tipificacion, $IDUSUARIO, $email_inicial;
	$email_inicial = sino($email_inicial);
	q("update siniestro set info_erronea=1,observaciones=concat(observaciones,'\n" . $NUSUARIO . ' ' . date('Y-m-d H:i') . " Se marca como INFORMACION DE CONTACTO ERRONEA') where id='$id'");
	$H1 = date('Y-m-d');
	$H2 = date('H:i:s');
	$Idn = q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo,tipificacion) values ($id,'$H1','$H2','$NUSUARIO','Se marca como INFORMACION DE CONTACTO ERRONEA',4,'$tipificacion')");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona registro');
	$Ahora = date('Y-m-d H:i:s');
	q("insert into call2infoerronea (siniestro,fecha,marcado_por) values ('$id','$Ahora','$NUSUARIO')");
	q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$id' and estado='A' ");
	guarda_escalafon(9);
	if ($email_inicial) {
		$Sin = qo("select * from siniestro where id=$id");
		$Aseguradora = qo("select razon_social,numero_call,nombre_servicio from aseguradora where id=$Sin->aseguradora");
		$Ciuo = qo1("select nombre from ciudad where codigo='$Sin->ciudad_original' ");
		$Ciu = qo1("select nombre from ciudad where codigo='$Sin->ciudad' ");
		$Correo = "
		Respetado(a) Se&ntilde;or(a) $Sin->asegurado_nombre

		Reciba cordial saludo.

		Somos Administracion Operativa Automotriz S.A. la empresa que le proporciona el $Aseguradora->nombre_servicio de $Aseguradora->razon_social.

		Usted tuvo un siniestro con su veh&iacute;culo de placas $Sin->placa con el n&uacute;mero $Sin->numero.

		$Aseguradora->razon_social le brinda a Usted el beneficio de disponer de un $Aseguradora->nombre_servicio por un tiempo determinado y Administracion Operativa Automotriz S.A. necesita contactarlo para bindarle este servicio.

		En vista de que la informacion que tenemos para contactarlo no ha sido efectiva, le pedimos el favor de que nos proporcione un n&uacute;mero de celular o un n&uacute;mero fijo a donde nuestro Call Center pueda contactarlo y explicarle todo el proceso para acceder al servicio.

		La informacion que nos proporcion&eacute; la aseguradora es la siguiente:

		Nombre: $Sin->asegurado_nombre  " .
			($Sin->declarante_telefono ? " Tel&eacute;fono del declarante: $Sin->declarante_telefono" : "") .
			($Sin->declarante_celular ? " Celular del declarante: $Sin->declarante_celular" : "") .
			($Sin->declarante_tel_resid ? " Tel&eacute;fono de residencia del declarante: $Sin->declarante_tel_resid" : "") .
			($Sin->declarate_tel_otro ? " Tel&eacute;fono adicional del declarante: $Sin->declarate_tel_otro" : "") .
			($Sin->declarate_email ? " Email del declarante: $Sin->declarate_email" : "") .
			"
		Ciudad :" . ($Ciuo ? $Ciuo : $Ciu) . "
		Fecha de Autorizacion: $Sin->fec_autorizacion

		Por favor puede enviarnos sus datos actualizados al correo siniestros@aoacolombia.com o llamar a nuestro Pbx en Bogot&aacute; D.C. al n&uacute;mero $Aseguradora->numero_call .

		Agradecemos su atencion.

		Cordialmente,

		Direccion Call Center
		Administracion Operativa Automotriz S.A.
		Carrera 69B N&uacute;mero 98A-10 Barrio Morato Bogot&aacute; D.C.
		Pbx. 7560510 Fax 7560512
		www.aoacolombia.com

		";
		$Email_usuario = usuario('email');
		$Envio = enviar_gmail(
			"direccioncallcenter@aoacolombia.com" /*de */,
			"Direccion Call Center AOA" /*Nombre de */,
			"$Sin->declarante_email,$Sin->declarante_nombre" /*para */,
			"$Email_usuario,$NUSUARIO" /*con copia*/,
			"AOA COLOMBIA S.A.S $Aseguradora->nombre_servicio" /*Objeto */,
			nl2br($Correo)
		);
		if ($Envio)	echo "<body><script language='javascript'>alert('Envio exitoso a $Sin->declarante_email');</script></body>";
		else echo "<body><script language='javascript'>alert('Envio NO exitoso a $Sin->declarante_email');</script></body>";
	}
	echo "<body><script language='javascript'>parent.parent.re_comenzar();</script></body>";
}

function caso_contacto_exitoso()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$D = qo("select * from siniestro where id=$id");
	if ($D->contacto_exitoso == '0000-00-00 00:00:00') {
		q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'$Fecha','$Hora','$NUSUARIO','Contacto exitoso',3) ");
		q("update siniestro set contacto_exitoso='$Fecha $Hora' where id=$id");
	}
	html();
	echo "<script language='javascript'>
		function no_adjudica() { window.open('zcallcenter2.php?Acc=caso_no_adjudica&id=$id','_self'); }
		function preadjudica() { window.open('zcallcenter2.php?Acc=caso_preadjudica&id=$id','_self'); }
		function compromiso() { window.open('zcallcenter2.php?Acc=caso_compromiso&id=$id','_self'); }
		</script>
		<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
		<table align='center' cellspacing='10' bgcolor='ffffff'><tr>
		<td bgcolor='ffffff' align='center'><a onclick='preadjudica();' style='cursor:pointer'><img src='img/preadjudicacion.png' height='80'><br>Solicitud de Adjudicacion</a></td>
		<td bgcolor='ffffff' align='center'><a onclick='no_adjudica();' style='cursor:pointer'><img src='img/no_adjudicacion.png' height='80'><br>No Adjudicacion</a></td>
		<td bgcolor='ffffff' align='center'><a onclick='compromiso();' style='cursor:pointer'><img src='img/compromiso.png' height='80'><br>Compromiso</a></td>
		</tr></table>
	</body>";
}

function caso_preadjudica()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	$D = qo("select declarante_email from siniestro where id=$id");
	html();
	$Hoy = date('Y-m-d');
	echo "<script language='javascript'>
		function validar_envio_preadjudicacion()
		{ with(document.forma)
			{
				if(!alltrim(correo_asegurado.value)) {alert('Debe digitar el correo del asegurado.');correo_asegurado.style.backgroundColor='ffffaa';correo_asegurado.focus();return false;}
				if(confirm('Est&aacute; seguro de Solicitar la Adjudicacion?'))
				{	document.forma.continuar.style.visibility='hidden';document.getElementById('mensaje').innerHTML='CERRANDO CASO, ENVIANDO EMAIL AL ASEGURADO.';submit(); }
			}}
		function recargar()	{parent.re_comenzar();}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
		<form action='zcallcenter2.php' method='post' target='Oculto_preadj' name='forma' id='forma'>
			<H4>SOLICITUD DE ADJUDICACION DE VEHICULO</H4>
			Se enviar&aacute; un correo electr&oacute;nico con la informacion de requisitos y el anexo de t&eacute;rminos y condiciones.<br><br>
			Correo electr&oacute;nico del asegurado: <input type='text' name='correo_asegurado' id='correo_asegurado' size='80' value='$D->declarante_email'><br><br>
			<input type='checkbox' name='inmediato'> Marque esta casilla en caso de que el asegurado requiera la adjudicacion inmediata sin leer el correo de pre-adjudicacion.<br>
			<input type='hidden' name='Acc' value='caso_preadjudica_ok'><br><br>
			<input type='hidden' name='id' value='$id'>
			<input type='button' style='font-size:16px;font-weight:bold' name='continuar' id='continuar' value=' CONTINUAR ' onclick='validar_envio_preadjudicacion();'><br>
			<span id='mensaje' style='font-size:20x;color:000055;font-weight:bold;'></span>
		</form>
		<iframe name='Oculto_preadj' id='Oculto_preadj' style='visibility:hidden' width='1' height='1'></iframe>
	</body>";
}

function caso_preadjudica_ok()
{
	global $id, $IDUSUARIO, $NUSUARIO, $correo_asegurado, $inmediato;
	$inmediato = sino($inmediato);
	$Email_usuario = usuario('email');
	$codigo_inicial = '1111111111';
	$codigo_final = '9999999999';
	$Longitud = strlen($codigo_inicial);
	$Codigo = round(rand($codigo_inicial, $codigo_final), 0);
	$Codigo = str_pad($Codigo, $Longitud, '0', STR_PAD_LEFT);
	if (q("select id from call2cola2 where codigo='$Codigo' ")) {
		$Codigo = round(rand($codigo_inicial, $codigo_final), 0);
		$Codigo = str_pad($Codigo, $Longitud, '0', STR_PAD_LEFT);
	}
	if (q("select id from call2cola2 where codigo='$Codigo' ")) {
		$Codigo = round(rand($codigo_inicial, $codigo_final), 0);
		$Codigo = str_pad($Codigo, $Longitud, '0', STR_PAD_LEFT);
	}
	if (q("select id from call2cola2 where codigo='$Codigo' ")) {
		$Codigo = round(rand($codigo_inicial, $codigo_final), 0);
		$Codigo = str_pad($Codigo, $Longitud, '0', STR_PAD_LEFT);
	}
	if (q("select id from call2cola2 where codigo='$Codigo' ")) {
		$Codigo = round(rand($codigo_inicial, $codigo_final), 0);
		$Codigo = str_pad($Codigo, $Longitud, '0', STR_PAD_LEFT);
	}
	if (q("select id from call2cola2 where codigo='$Codigo' ")) {
		$Codigo = round(rand($codigo_inicial, $codigo_final), 0);
		$Codigo = str_pad($Codigo, $Longitud, '0', STR_PAD_LEFT);
	}
	if (q("select id from call2cola2 where codigo='$Codigo' ")) {
		$Codigo = round(rand($codigo_inicial, $codigo_final), 0);
		$Codigo = str_pad($Codigo, $Longitud, '0', STR_PAD_LEFT);
	}
	if (q("select id from call2cola2 where codigo='$Codigo' ")) {
		$Codigo = round(rand($codigo_inicial, $codigo_final), 0);
		$Codigo = str_pad($Codigo, $Longitud, '0', STR_PAD_LEFT);
	}
	if (q("select id from call2cola2 where codigo='$Codigo' ")) {
		$Codigo = round(rand($codigo_inicial, $codigo_final), 0);
		$Codigo = str_pad($Codigo, $Longitud, '0', STR_PAD_LEFT);
	}
	if (q("select id from call2cola2 where codigo='$Codigo' ")) {
		$Codigo = round(rand($codigo_inicial, $codigo_final), 0);
		$Codigo = str_pad($Codigo, $Longitud, '0', STR_PAD_LEFT);
	}
	if (q("select id from call2cola2 where codigo='$Codigo' ")) {
		$Codigo = round(rand($codigo_inicial, $codigo_final), 0);
		$Codigo = str_pad($Codigo, $Longitud, '0', STR_PAD_LEFT);
	}
	if (q("select id from call2cola2 where codigo='$Codigo' ")) {
		$Codigo = round(rand($codigo_inicial, $codigo_final), 0);
		$Codigo = str_pad($Codigo, $Longitud, '0', STR_PAD_LEFT);
	}
	$Ahora = date('Y-m-d H:i:s');
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo_seguimiento = 17; /*17: Correo pre-adjudicacion */
	$Sin = qo("select asegurado_nombre,placa,t_aseguradora(aseguradora) as naseg,numero,aseguradora from siniestro where id=$id");
	$Ase = qo("select nombre,razon_social,info_per,numero_call from aseguradora where id=$Sin->aseguradora");
	if ($Envio = enviar_gmail(
		$Email_usuario /*de */,
		$NUSUARIO /*nombre de */,
		"$correo_asegurado,$Sin->asegurado_nombre" /*para */,
		"direccioncallcenter@aoacolombia.com,DIRECCION CALL CENTER"   /*Con copia*/,
		"SOLICITUD VEHICULO DE REEMPLAZO"  /*OBJETO*/,
		nl2br("
		Senor(a) Asegurado(a) $Sin->asegurado_nombre,

		Reciba cordial saludo.

		Bienvenido(a)   al sistema de adjudicacion del Servicio de Vehiculo de Reemplazo de AOA Colombia.


		Este es un correo automatico del &Aacute;rea de Call Center cuyo objetivo es facilitar el acceso a nuestra plataforma para que
		usted revise y acepte los terminos y condiciones del servicio, respecto a la adjudicacion del vehiculo sustituto derivado de
		la reclamacion que presento ante $Ase->razon_social referente al vehiculo de placas $Sin->placa  y cuyo numero de
		siniestro es el $Sin->numero.

		En el siguiente link podra ingresar a la plataforma de asignacion del Vehiculo de Reemplazo al que tiene derecho.

		<a href='https://app.aoacolombia.com/servicio/?c=$Codigo'>https://app.aoacolombia.com/servicio/?c=$Codigo</a>  (si al dar click no funciona, por favor copie la direccion y peguela en la casilla de direccion de su navegador.)

		Su codigo unico de servicio es el $Codigo. Este codigo le servira para identificarse en nuestra base de datos y obtener toda la informacion correspondiente a los requisitos, terminos y condiciones del servicio.

		$Ase->info_per

		Nota respecto al uso de sus datos personales (Informacion no sensible)

		Es importante que tenga en cuenta que para acceder al servicio de vehiculo sustituto, usted puede haber
		suministrado y/o suministrara de manera voluntaria y libre informacion para ser utilizada exclusivamente durante
		las etapas de contactabilidad, agendamiento, prestacion, devolucion del vehiculo y cierre del
		servicio incluyendo la validacion del proceso de garantias; informacion que corresponde a datos personales
		como son nombres, apellidos, numero de identificacion, telefono (s), direccion(es), correo(s) electronico(s), ciudad,
		informacion de tarjeta(s) de credito y cuenta(s) bancaria(s) (cuando apliquen) entre otros.

		Dicha informacion tambien podra ser utilizada cuando usted realice una solicitud de asistencia tecnica y o PQR,
		para darle tratamiento y respuesta.

		Ante cualquier inquietud puede escribirnos al correo electronico sac@aoacolombia.com. Nuestro horario de atencion es de lunes a viernes de 8:00 a.m. a 5:00 p.m. y sabados de 9:00a.m. a 12:00 p.m.

		Cordialmente,


		Departamento de Call Center
		Direccion Nacional de Servicio al Cliente AOA Colombia
		www.aoacolombia.com
		$Email_usuario

		") /*mensaje */
	)) {
		$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','SE ENVIA CORREO DE PRE-ADJUDICACION $correo_asegurado " . ($inmediato ? " CON ACEPTACION INMEDIATA " : "") . "',$Codigo_seguimiento)");
		//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
		q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: SE ENVIA CORREO DE PRE-ADJUDICACION $correo_asegurado.\") where id=$id");
		if ($inmediato) {
			$Luego = date('Y-m-d') . ' ' . aumentaminutos(date('H:i:s'), -30);
			$Idn = q("insert into call2cola2 (siniestro,fecha,codigo,estado,aceptado,fecha_aceptacion) values ($id,'$Ahora','$Codigo','0',1,'$Luego')");
			graba_bitacora('call2cola2', 'A', $Idn, 'Adiciona Registro con aceptacion de t&eacute;rminos y condiciones');
		} else q("insert into call2cola2 (siniestro,fecha,codigo,estado) values ($id,'$Ahora','$Codigo','0')");
		q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$id' and estado='A' ");
		q("update siniestro set declarante_email='$correo_asegurado' where id=$id");
		guarda_escalafon(2);
		echo "<body><script language='javascript'>alert('Env&iacute;o del correo satisfactorio.');parent.recargar();</script></body>";
	} else {
		echo "<body><script language='javascript'>parent.getElementById('mensaje').innerHTML='NO SE PUDO ENVIAR EL CORREO, por favor verifique la informacion e intentelo nuevamente.';
		parent.forma.continuar.style.visibility='visible';</script></body>";
	}
}

function caso_re_envia_correo_preadjudicacion()
{
	global $id, $IDUSUARIO, $NUSUARIO;
	$Cola = qo("select * from call2cola2 where id='$id'");
	$Sin = qo("select declarante_email from siniestro where id=$Cola->siniestro");
	html('RE ENVIO DE PREADJUDICACION');
	echo "<body><script language='javascript'>centrar(600,400);</script>
	<form action='zcallcenter2.php' target='_self' method='POST' name='forma' id='forma'>
		Correo del asegurado: <input type='text' name='email' id='email' value='$Sin->declarante_email' size='50'>
		<input type='hidden' name='Acc' value='caso_re_envia_correo_preadjudicacion_ok'>
		<input type='hidden' name='id' value='$id'>
		<input type='submit' name='enviar' id='enviar' value=' ENVIAR ' >
	</form>
	</body>";
}

function caso_re_envia_correo_preadjudicacion_ok()
{
	global $id, $email, $IDUSUARIO, $NUSUARIO;
	$Email_usuario = usuario('email');
	$Cola = qo("select * from call2cola2 where id='$id'");
	$Ahora = date('Y-m-d H:i:s');
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo_seguimiento = 17; /*17: Correo pre-adjudicacion */
	q("update siniestro set declarante_email='$email' where id=$Cola->siniestro");
	$Sin = qo("select asegurado_nombre,placa,t_aseguradora(aseguradora) as naseg,numero,aseguradora from siniestro where id=$Cola->siniestro");
	$Ase = qo("select nombre,razon_social,info_per,numero_call from aseguradora where id=$Sin->aseguradora");
	if ($Envio = enviar_gmail(
		$Email_usuario /*de */,
		$NUSUARIO /*nombre de */,
		"$email,$Sin->asegurado_nombre" /*para */,
		"direccioncallcenter@aoacolombia.com,DIRECCION CALL CENTER"   /*Con copia*/,
		"SOLICITUD VEHICULO DE REEMPLAZO"  /*OBJETO*/,
		nl2br("
		Se&ntilde;or(a) Asegurado(a) $Sin->asegurado_nombre,

		Reciba cordial saludo.

		Bienvenido(a)   al sistema de adjudicacion del Servicio de Vehiculo de Reemplazo de AOA Colombia.


		Este es un correo automatico del &Aacute;rea de Call Center cuyo objetivo es facilitar el acceso a nuestra plataforma para que
		usted revise y acepte los terminos y condiciones del servicio, respecto a la adjudicacion del vehiculo sustituto derivado de
		la reclamacion que presento ante $Ase->razon_social referente al vehiculo de placas $Sin->placa  y cuyo numero de
		siniestro es el $Sin->numero.

		En el siguiente link podra ingresar a la plataforma de asignacion del Vehiculo de Reemplazo al que tiene derecho.

		<a href='https://app.aoacolombia.com/servicio/?c=$Codigo'>https://app.aoacolombia.com/servicio/?c=$Codigo</a>  (si al dar click no funciona, por favor copie la direccion y peguela en la casilla de direccion de su navegador.)

		Su codigo unico de servicio es el $Codigo. Este codigo le servira para identificarse en nuestra base de datos y obtener toda la informacion correspondiente a los requisitos, terminos y condiciones del servicio.

		$Ase->info_per

		Nota respecto al uso de sus datos personales (Informacion no sensible)

		Es importante que tenga en cuenta que para acceder al servicio de vehiculo sustituto, usted puede haber
		suministrado y/o suministrara de manera voluntaria y libre informacion para ser utilizada exclusivamente durante
		las etapas de contactabilidad, agendamiento, prestacion, devolucion del vehiculo y cierre del
		servicio incluyendo la validacion del proceso de garantias; informacion que corresponde a datos personales
		como son nombres, apellidos, numero de identificacion, telefono (s), direccion(es), correo(s) electronico(s), ciudad,
		informacion de tarjeta(s) de cr&eacute;dito y cuenta(s) bancaria(s) (cuando apliquen) entre otros.

		Dicha informacion tambien podra ser utilizada cuando usted realice una solicitud de asistencia tecnica y o PQR,
		para darle tratamiento y respuesta.

		Ante cualquier inquietud puede escribirnos al correo electronico sac@aoacolombia.com. Nuestro horario de atencion es de lunes a viernes de 8:00 a.m. a 5:00 p.m. y sabados de 9:00a.m. a 12:00 p.m.

		Cordialmente,


		Departamento de Call Center
		Direccion Nacional de Servicio al Cliente AOA Colombia
		www.aoacolombia.com
		$Email_usuario

		") /*mensaje */
	)) {
		$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($Cola->siniestro,'$NUSUARIO','$Fecha','$Hora','SE ENVIA CORREO DE PRE-ADJUDICACION $Sin->declarante_email ',$Codigo_seguimiento)");
		//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
		q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: SE ENVIA CORREO DE PRE-ADJUDICACION $email.\") where id=$Cola->siniestro");
		q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$Cola->siniestro' and estado='A' ");
		echo "<body><script language='javascript'>alert('Env&iacute;o del correo satisfactorio.');opener.re_comenzar();window.close();void(null);</script></body>";
	}
}
function caso_no_adjudica()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	html();
	echo "<script language='javascript'>
		function validar_no_adjudicacion()
		{
			if(confirm('Esta seguro de continuar con la NO  ADJUDICACION?'))
			{
				document.forma.continuar.style.visibility='hidden';
				document.forma.submit();
			}
		}
	</script>
	<body><body ><h3>Agente: $NUSUARIO - Proceso No Adjudicacion</h3>
	<form action='zcallcenter2.php' target='_self' method='POST' name='forma' id='forma'>
		Seleccione la causal: ";
	$Causales = q("select s.id,c.nombre as causal,s.nombre from causal c,subcausal s where s.causal in (1,2,18,21,22,23) and s.activo=1 and s.causal=c.id order by c.nombre,s.nombre");
	echo "<select name='subcausal'>";
	$Causal = '';
	while ($Cau = mysql_fetch_object($Causales)) {
		if ($Causal != $Cau->causal) {
			if ($Causal != '') echo "</optgroup>";
			$Causal = $Cau->causal;
			echo "<optgroup label='$Causal'>";
		}
		echo "<option value='$Cau->id'>$Cau->nombre</option>";
	}
	echo "</optgroup></select><br><br>
	<input type='button' name='continuar' id='continuar' value=' CONTINUAR CON LA NO ADJUDICACION ' style='font-size:16px;font-weight:bold;'onclick='validar_no_adjudicacion();'>
	<input type='hidden' name='Acc' value='caso_no_adjudicacion_ok'><input type='hidden' name='id' value='$id'>
	</form>
	</body>";
}

function caso_no_adjudicacion_ok()
{
	global $id, $NUSUARIO, $subcausal, $IDUSUARIO;
	$Ahora = date('Y-m-d H:i:s');
	$Subc = qo("select * from subcausal where id='$subcausal' ");
	$ncausal = qo1("select nombre from causal where id=$Subc->causal") . ' - ' . $Subc->nombre;
	$Sin = qo("select id,estado,retencion,causal,subcausal from siniestro where id=$id");
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo = 6; /*6: No Adjudicacion */
	q("update siniestro set estado=1,causal=$Subc->causal,subcausal='$subcausal',
		observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]: No Adjudica. Causal:$ncausal') where id=$id"); // cambia el estado a NO ADJUDICADO = 1
	graba_bitacora('siniestro', 'M', $id, 'Cambia estado a No Adjudicado.');
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','No Adjudica causal: $ncausal.',$Codigo)");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$id' and estado='A' ");
	q("delete from call2cola1 where siniestro='$id' ");
	q("update call2cola2 set estado=6 where siniestro='$id' ");
	$Idcc = qo1("select id from call2cola2 where siniestro=$id");
	graba_bitacora('call2cola2', 'M', $Idcc, 'Cambia estado a 6');
	q("delete from call2cola3 where siniestro='$id' ");
	q("delete from call2cola4 where siniestro='$id' ");
	guarda_escalafon(1);
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}
function caso_compromiso()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	// BUSCA LOS EVENTOS DEL CASO
	$Cantidad_eventos = qo1("select count(id) from seguimiento where siniestro=$id and tipo=16");
	html();
	echo "<script language='javascript'>
		function validar_envio_compromiso()
		{
			with(document.forma)
			{
				if(!subcausal.value) {alert('Debe seleccionar un tipo de compromiso');tipificacion.style.backgroundColor='ffffaa';tipificacion.focus();return false;}
				if(!alltrim(observaciones.value)) {alert('Debe digitar las observaciones');observaciones.style.backgroundColor='ffffaa';observaciones.focus();return false;}
				if(confirm('Esta seguro de crear el Compromiso?')) submit();
			}
		}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
		<form action='zcallcenter2.php' method='post' target='_self' name='forma' id='forma'>
			<h4>REGISTRO DE COMPROMISO DE LLAMADA A FUTURO ($Cantidad_eventos eventos)</H4>
			Seleccione la causal: " . $Causales = q("select s.id,c.nombre as causal,s.nombre from causal c,subcausal s where s.causal in (24,25,26,27) and s.activo=1 and s.causal=c.id order by c.nombre,s.nombre"); // 1,2,18,21,22,23
	echo "<select name='subcausal'>";
	$Causal = '';
	while ($Cau = mysql_fetch_object($Causales)) {
		if ($Causal != $Cau->causal) {
			if ($Causal != '') echo "</optgroup>";
			$Causal = $Cau->causal;
			echo "<optgroup label='$Causal'>";
		}
		echo "<option value='$Cau->id'>$Cau->nombre</option>";
	}
	echo "</optgroup></select><br>
			Fecha del compromiso: <select name='fechacompromiso'>";
	$Hoy = date('Y-m-d');
	for ($i = 1; $i < 20; $i++) {
		$nd = ndiasemana($Hoy);
		echo "<option value='$Hoy' " . ($nd == 'S�bado' || $nd == 'Domingo' ? "style='background-color:ffdddd'" : "") . ">$Hoy [$nd]</option>";
		$Hoy = date('Y-m-d', strtotime(aumentadias($Hoy, 1)));
	}
	echo "</select> Hora: " . pinta_hora('forma', 'horacompromiso', date('H:i:s')) . "<br>
			<br>Observaciones: <input type='text' name='observaciones' id='observaciones' size='80'><br>
			<input type='hidden' name='Acc' value='caso_compromiso_ok'><br>
			<input type='hidden' name='id' value='$id'>
			<input type='button' style='font-size:16px;font-weight:bold' value=' CONTINUAR ' onclick='validar_envio_compromiso();'>
		</form>
	</body>";
}

function caso_compromiso_ok()
{
	global $id, $NUSUARIO, $IDUSUARIO, $subcausal, $observaciones, $fechacompromiso, $horacompromiso;
	$Ahora = date('Y-m-d H:i:s');
	$Subc = qo("select * from subcausal where id='$subcausal' ");
	$ncausal = qo1("select nombre from causal where id=$Subc->causal") . ' - ' . $Subc->nombre;
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo = 16; /*16: Compromiso */
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo,tipo_compromiso,fecha_compromiso) values ($id,'$NUSUARIO','$Fecha','$Hora','COMPROMISO: $fechacompromiso $horacompromiso ',$Codigo,'$ncausal','$fechacompromiso $horacompromiso')");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	q("update siniestro set causal=$Subc->causal,subcausal='$subcausal',info_erronea = '',observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: COMPROMISO: $observaciones. ($ncausal)\") where id=$id");
	q("insert into call2cola1 (siniestro,fecha,compromiso) values ('$id','$fechacompromiso $horacompromiso',1)");
	$sql = "delete from call2infoerronea where siniestro = $id";
	q($sql);
	q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$id' and estado='A' ");
	guarda_escalafon(4);
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function caso_compromiso2()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	// BUSCA LOS EVENTOS DEL CASO
	$Cantidad_eventos = qo1("select count(id) from seguimiento where siniestro=$id and tipo=16");

	html();
	echo "<script language='javascript'>
		function validar_envio_compromiso()
		{
			with(document.forma)
			{
				if(!tipificacion.value) {alert('Debe seleccionar un tipo de compromiso');tipificacion.style.backgroundColor='ffffaa';tipificacion.focus();return false;}
				if(!alltrim(observaciones.value)) {alert('Debe digitar las observaciones');observaciones.style.backgroundColor='ffffaa';observaciones.focus();return false;}
				if(confirm('Est&aacute; seguro de crear el Compromiso?')) submit();
			}
		}
		function busca_conteo()
		{
			var Tipo=document.forma.tipificacion.value;
			window.open('zcallcenter2.php?Acc=busca_conteo_tipificacion&siniestro=$id&tipo='+Tipo,'Oculto_compromiso');
		}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
		<form action='zcallcenter2.php' method='post' target='_self' name='forma' id='forma'>
			<h4>REGISTRO DE COMPROMISO DE ASIGNACION DE VEHICULO A FUTURO ($Cantidad_eventos eventos)</H4>
			Seleccione la tipificacion seg&uacute;n el caso: " . menu1("tipificacion", "select id,nombre from tipo_compromiso where activo=1", 0, 1, '', " onchange='busca_conteo();' ") . " <span id='conteo'></span><br>
			Fecha del compromiso: <select name='fechacompromiso'>";
	$Hoy = date('Y-m-d');
	for ($i = 1; $i < 20; $i++) {
		$nd = ndiasemana($Hoy);
		echo "<option value='$Hoy' " . ($nd == 'S�bado' || $nd == 'Domingo' ? "style='background-color:ffdddd'" : "") . ">$Hoy [$nd]</option>";
		$Hoy = date('Y-m-d', strtotime(aumentadias($Hoy, 1)));
	}
	echo "</select> Hora: " . pinta_hora('forma', 'horacompromiso', date('H:i:s')) . "<br>
			<br>Observaciones: <input type='text' name='observaciones' id='observaciones' size='80'><br>
			<input type='hidden' name='Acc' value='caso_compromiso2_ok'><br>
			<input type='hidden' name='id' value='$id'>
			<input type='button' style='font-size:16px;font-weight:bold' value=' CONTINUAR ' onclick='validar_envio_compromiso();'>
		</form>
		<iframe name='Oculto_compromiso' id='Oculto_compromiso' style='visibility:hidden' width='1' height='1'></iframe>
	</body>";
}

function busca_conteo_tipificacion() // BUSCA CONTEO DE TIPIFICACION EN CASOS DE COMPROMISO NIVEL 2
{
	global $siniestro, $tipo;
	echo "select count(*) from seguimiento where siniestro='$siniestro' and tipo_compromiso='$tipo' " . "br";
	$Cantidad = qo1("select count(*) from seguimiento where siniestro='$siniestro' and tipo_compromiso='$tipo' ");
	echo "<body><script language='javascript'>parent.document.getElementById('conteo').innerHTML='$Cantidad veces';</script>
	$siniestro $tipo
	</body>";
}

function caso_compromiso2_ok()
{
	global $id, $NUSUARIO, $IDUSUARIO, $tipificacion, $observaciones, $fechacompromiso, $horacompromiso;
	$Ahora = date('Y-m-d H:i:s');
	$Ntipificacion = qo1("select nombre from tipo_compromiso where id='$tipificacion' ");
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo = 16; /*16: Compromiso */
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo,tipo_compromiso,fecha_compromiso) values ($id,'$NUSUARIO','$Fecha','$Hora','COMPROMISO 2: $fechacompromiso $horacompromiso ',$Codigo,'$tipificacion','$fechacompromiso $horacompromiso')");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: COMPROMISO 2: $observaciones. ($Ntipificacion)\") where id=$id");

	$Estado_siniestro = qo1("select estado from siniestro where id=$id");
	if ($Estado_siniestro == 5) {
		if ($idc1 = qo1("select id from call2cola1 where siniestro=$id")) {
			q("update call2cola1 set fecha='$fechacompromiso $horacompromiso' where id=$idc1");
		} else {
			q("insert into call2cola1(siniestro,fecha,compromiso) values ('$id','$fechacompromiso $horacompromiso',1)");
		}
		$Idcc = qo1("select id from call2cola1 where siniestro=$id");
		graba_bitacora('call2cola1', 'M', $Idcc, 'Graba Compromiso 2. Cola 1');
	} else {
		if ($idc2 = qo1("select id from call2cola2 where siniestro=$id")) {
			q("update call2cola2 set fecha_aceptacion='$fechacompromiso $horacompromiso',estado='0' where id=$idc2");
		} else {
			q("insert into call2cola2(siniestro,fecha,estado) values ('$id','$fechacompromiso $horacompromiso',0)");
		}
		$Idcc = qo1("select id from call2cola2 where siniestro=$id");
		graba_bitacora('call2cola2', 'M', $Idcc, 'Graba Compromiso 2. Cola 2');
	}
	q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$id' and estado='A' ");
	guarda_escalafon(4);
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function caso_compromiso3()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	html();
	echo "<script language='javascript'>
		function validar_envio_compromiso()
		{
			with(document.forma)
			{
				if(!tipificacion.value) {alert('Debe seleccionar un tipo de compromiso');tipificacion.style.backgroundColor='ffffaa';tipificacion.focus();return false;}
				if(!alltrim(observaciones.value)) {alert('Debe digitar las observaciones');observaciones.style.backgroundColor='ffffaa';observaciones.focus();return false;}
				if(confirm('Est&aacute; seguro de crear el Compromiso?')) submit();
			}
		}
		function busca_conteo()
		{
			var Tipo=document.forma.tipificacion.value;
			window.open('zcallcenter2.php?Acc=busca_conteo_tipificacion&siniestro=$id&tipo='+Tipo,'Oculto_compromiso');
		}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
		<form action='zcallcenter2.php' method='post' target='_self' name='forma' id='forma'>
			<h4>REGISTRO DE COMPROMISO DE ASIGNACION DE VEHICULO A FUTURO</H4>
			Seleccione la tipificacion seg&uacute;n el caso: " . menu1("tipificacion", "select id,nombre from tipo_compromiso where activo=1", 0, 1, '', " onchange='busca_conteo();' ") . " <span id='conteo'></span><br>
			Fecha del compromiso: <select name='fechacompromiso'>";
	$Hoy = date('Y-m-d');
	for ($i = 1; $i < 20; $i++) {
		$nd = ndiasemana($Hoy);
		echo "<option value='$Hoy' " . ($nd == 'S�bado' || $nd == 'Domingo' ? "style='background-color:ffdddd'" : "") . ">$Hoy [$nd]</option>";
		$Hoy = date('Y-m-d', strtotime(aumentadias($Hoy, 1)));
	}
	echo "</select> Hora: " . pinta_hora('forma', 'horacompromiso', date('H:i:s')) . "<br>
			<br>Observaciones: <input type='text' name='observaciones' id='observaciones' size='80'><br>
			<input type='hidden' name='Acc' value='caso_compromiso3_ok'><br>
			<input type='hidden' name='id' value='$id'>
			<input type='button' style='font-size:16px;font-weight:bold' value=' CONTINUAR ' onclick='validar_envio_compromiso();'>
		</form>
		<iframe name='Oculto_compromiso' id='Oculto_compromiso' style='visibility:hidden' width='1' height='1'></iframe>
	</body>";
}

function caso_compromiso3_ok()
{
	global $id, $NUSUARIO, $IDUSUARIO, $tipificacion, $observaciones, $fechacompromiso, $horacompromiso;
	$Ahora = date('Y-m-d H:i:s');
	$Ntipificacion = qo1("select nombre from tipo_compromiso where id='$tipificacion' ");
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo = 16; /*16: Compromiso */
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo,tipo_compromiso,fecha_compromiso) values ($id,'$NUSUARIO','$Fecha','$Hora','COMPROMISO 3: $fechacompromiso $horacompromiso ',$Codigo,'$tipificacion','$fechacompromiso $horacompromiso')");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: COMPROMISO 2: $observaciones. ($Ntipificacion)\") where id=$id");
	if ($id3 = qo("select * from call2cola3 where siniestro=$id"))
		q("update call2cola3 set fecha='$fechacompromiso $horacompromiso' where siniestro=$id");
	else
		q("insert into call2cola3 (siniestro,fecha) values ('$id','$fechacompromiso $horacompromiso')");
	q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$id' and estado='A' ");
	guarda_escalafon(4);
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function caso_contacto_tercera()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	html();
	echo "<script language='javascript'>
		function compromiso() { window.open('zcallcenter2.php?Acc=caso_compromiso&id=$id','_self'); }
		function buzon_tercera_persona() { window.open('zcallcenter2.php?Acc=caso_buzon_tercera_persona&id=$id','_self'); }
		function actualizar_datos() {modal('zcallcenter2.php?Acc=caso_actualizar_datos&id=$id','actualizar');}
		function re_comenzar() {parent.re_comenzar();}
		</script>
		<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
		<table align='center' cellspacing='10' bgcolor='ffffff'><tr>
		<td bgcolor='ffffff' align='center'><a onclick='buzon_tercera_persona();' style='cursor:pointer'><img src='img/buzon3persona.png' height='80'><br>Mensaje con Tercera Persona</a></td>
		<td bgcolor='ffffff' align='center'><a onclick='compromiso();' style='cursor:pointer'><img src='img/compromiso.png' height='80'><br>Compromiso</a></td>
		<td bgcolor='ffffff' align='center'><a onclick='actualizar_datos();' style='cursor:pointer'><img src='img/actualizacion.png' height='80'><br>Actualizacion de datos</a></td>
		</tr></table>
	</body>";
}

function caso_buzon_voz()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	// BUSCA LOS EVENTOS DE BUZON DEL CASO
	$Cantidad_eventos = qo1("select count(id) from seguimiento where siniestro=$id and tipo=12");
	html();
	echo "<script language='javascript'>
		function validar_envio_buzon()
		{
			with(document.forma)
			{
				if(!tipificacion.value) {alert('Debe seleccionar un tipo de buz&oacute;n');tipificacion.style.backgroundColor='ffffaa';tipificacion.focus();return false;}
				if(!alltrim(observaciones.value)) {alert('Debe digitar las observaciones');observaciones.style.backgroundColor='ffffaa';observaciones.focus();return false;}
				if(confirm('Est&aacute; seguro de crear el Compromiso?')) submit();
			}
		}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'><h4>Buz&oacute;n de Voz ($Cantidad_eventos eventos)</h4>
		<form action='zcallcenter2.php' method='post' target='_self' name='forma' id='forma'>
			Seleccione la tipificacion seg&uacute;n el caso:
			<select name='tipificacion'>";
	$Clases = q("select id,tipo,nombre from tipifica_seguimiento where tipo in ('TELEFONO FIJO','TELEFONO CELULAR') order by tipo,nombre");
	$Clase = '';
	while ($Cl = mysql_fetch_object($Clases)) {
		if ($Clase != $Cl->tipo) {
			if ($Clase != '') echo "</optgroup>";
			$Clase = $Cl->tipo;
			echo "<optgroup label='$Clase'>";
		}
		echo "<option value='$Cl->id'>$Cl->nombre</option>";
	}
	echo "</optgroup></select><br><br>
			<br>Observaciones: <input type='text' name='observaciones' id='observaciones' size='80'><br>
			<br>Cerrar el caso: <input type='checkbox' name='CERRAR_caso'>  Marque esta casilla si no hay mas n&uacute;meros para marcar.<br><br>
			<br><input type='hidden' name='Acc' value='caso_buzon_ok'>
			<input type='hidden' name='id' value='$id'>
			<input type='button' style='font-size:16px;font-weight:bold' value=' CONTINUAR ' onclick='validar_envio_buzon();'>
		</form>
	</body>";
}

function caso_buzon_ok()
{
	global $id, $NUSUARIO, $IDUSUARIO, $tipificacion, $observaciones, $CERRAR_caso;
	$CERRAR_caso = sino($CERRAR_caso);
	$minutos = qo1("select tiempo_remarcacion from aseguradora,siniestro where aseguradora.id=siniestro.aseguradora and siniestro.id=$id");
	$Ahora = date('Y-m-d H:i:s');
	$Luego = date('Y-m-d') . ' ' . aumentaminutos(date('H:i:s'), $minutos);
	$Ntipificacion = qo1("select concat(tipo,' - ',nombre) from tipifica_seguimiento where id='$tipificacion' ");
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo = 12; /*12: Mensaje en Buz&oacute;n de voz */
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo,tipificacion) values ($id,'$NUSUARIO','$Fecha','$Hora','Se deja mensaje en buz&oacute;n de voz.',$Codigo,'$tipificacion')");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: BUZON DE VOZ: $observaciones.  ($Ntipificacion)\") where id=$id");
	if ($CERRAR_caso) {
		q("insert into call2cola1 (siniestro,fecha) values ('$id','$Luego')");
		q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$id' and estado='A' ");
	}
	guarda_escalafon(3);
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function caso_buzon2_voz()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	// BUSCA LOS EVENTOS DE BUZON DEL CASO
	$Cantidad_eventos = qo1("select count(id) from seguimiento where siniestro=$id and tipo=12");
	html();
	echo "<script language='javascript'>
		function validar_envio_buzon()
		{
			with(document.forma)
			{
				if(!tipificacion.value) {alert('Debe seleccionar un tipo de buz&oacute;n');tipificacion.style.backgroundColor='ffffaa';tipificacion.focus();return false;}
				if(!alltrim(observaciones.value)) {alert('Debe digitar las observaciones');observaciones.style.backgroundColor='ffffaa';observaciones.focus();return false;}
				if(confirm('Est&aacute; seguro de crear el Compromiso?')) submit();
			}
		}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'><h4>Buz&oacute;n de Voz ($Cantidad_eventos eventos)</h4>
		<form action='zcallcenter2.php' method='post' target='_self' name='forma' id='forma'>
			Seleccione la tipificacion seg&uacute;n el caso:
			<select name='tipificacion'>";
	$Clases = q("select id,tipo,nombre from tipifica_seguimiento where tipo in ('TELEFONO FIJO','TELEFONO CELULAR') order by tipo,nombre");
	$Clase = '';
	while ($Cl = mysql_fetch_object($Clases)) {
		if ($Clase != $Cl->tipo) {
			if ($Clase != '') echo "</optgroup>";
			$Clase = $Cl->tipo;
			echo "<optgroup label='$Clase'>";
		}
		echo "<option value='$Cl->id'>$Cl->nombre</option>";
	}
	echo "</optgroup></select><br><br>
			<br>Observaciones: <input type='text' name='observaciones' id='observaciones' size='80'><br>
			<br>Cerrar el caso: <input type='checkbox' name='CERRAR_caso'>  Marque esta casilla si no hay mas n&uacute;meros para marcar.<br><br>
			<br><input type='hidden' name='Acc' value='caso_buzon2_ok'>
			<input type='hidden' name='id' value='$id'>
			<input type='button' style='font-size:16px;font-weight:bold' value=' CONTINUAR ' onclick='validar_envio_buzon();'>
		</form>
	</body>";
}

function caso_buzon2_ok()
{
	global $id, $NUSUARIO, $IDUSUARIO, $tipificacion, $observaciones, $CERRAR_caso;
	$CERRAR_caso = sino($CERRAR_caso);
	$minutos = qo1("select tiempo_remarcacion3 from aseguradora,siniestro where aseguradora.id=siniestro.aseguradora and siniestro.id=$id");
	$Ahora = date('Y-m-d H:i:s');
	$Luego = date('Y-m-d') . ' ' . aumentaminutos(date('H:i:s'), $minutos);
	$Ntipificacion = qo1("select concat(tipo,' - ',nombre) from tipifica_seguimiento where id='$tipificacion' ");
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo = 12; /*12: Mensaje en Buz&oacute;n de voz */
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo,tipificacion) values ($id,'$NUSUARIO','$Fecha','$Hora','Se deja mensaje en buz&oacute;n de voz.',$Codigo,'$tipificacion')");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: BUZON DE VOZ: $observaciones.  ($Ntipificacion)\") where id=$id");
	if ($CERRAR_caso) {
		q("update call2cola2 set fecha_aceptacion='$Luego',estado='0' where siniestro=$id");
		$Idcc = qo1("select id from call2cola2 where siniestro=$id");
		graba_bitacora('call2cola2', 'M', $Idcc, 'Ajusta la fecha de aceptacion y el estado a 0');
		q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$id' and estado='A' ");
	}
	guarda_escalafon(3);
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function caso_buzon3_voz()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	// BUSCA LOS EVENTOS DE BUZON DEL CASO
	$Cantidad_eventos = qo1("select count(id) from seguimiento where siniestro=$id and tipo=12");
	html();
	echo "<script language='javascript'>
		function validar_envio_buzon()
		{
			with(document.forma)
			{
				if(!tipificacion.value) {alert('Debe seleccionar un tipo de buz&oacute;n');tipificacion.style.backgroundColor='ffffaa';tipificacion.focus();return false;}
				if(!alltrim(observaciones.value)) {alert('Debe digitar las observaciones');observaciones.style.backgroundColor='ffffaa';observaciones.focus();return false;}
				if(confirm('Est&aacute; seguro de crear el Compromiso?')) submit();
			}
		}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'><h4>Buz&oacute;n de Voz ($Cantidad_eventos eventos)</h4>
		<form action='zcallcenter2.php' method='post' target='_self' name='forma' id='forma'>
			Seleccione la tipificacion seg&uacute;n el caso:
			<select name='tipificacion'>";
	$Clases = q("select id,tipo,nombre from tipifica_seguimiento where tipo in ('TELEFONO FIJO','TELEFONO CELULAR') order by tipo,nombre");
	$Clase = '';
	while ($Cl = mysql_fetch_object($Clases)) {
		if ($Clase != $Cl->tipo) {
			if ($Clase != '') echo "</optgroup>";
			$Clase = $Cl->tipo;
			echo "<optgroup label='$Clase'>";
		}
		echo "<option value='$Cl->id'>$Cl->nombre</option>";
	}
	echo "</optgroup></select><br><br>
			<br>Observaciones: <input type='text' name='observaciones' id='observaciones' size='80'><br>
			<br>Cerrar el caso: <input type='checkbox' name='CERRAR_caso'>  Marque esta casilla si no hay mas n&uacute;meros para marcar.<br><br>
			<br><input type='hidden' name='Acc' value='caso_buzon3_ok'>
			<input type='hidden' name='id' value='$id'>
			<input type='button' style='font-size:16px;font-weight:bold' value=' CONTINUAR ' onclick='validar_envio_buzon();'>
		</form>
	</body>";
}

function caso_buzon3_ok()
{
	global $id, $NUSUARIO, $IDUSUARIO, $tipificacion, $observaciones, $CERRAR_caso;
	$CERRAR_caso = sino($CERRAR_caso);
	$minutos = qo1("select tiempo_remarcacion3 from aseguradora,siniestro where aseguradora.id=siniestro.aseguradora and siniestro.id=$id");
	$Ahora = date('Y-m-d H:i:s');
	$Luego = date('Y-m-d') . ' ' . aumentaminutos(date('H:i:s'), $minutos);
	$Ntipificacion = qo1("select concat(tipo,' - ',nombre) from tipifica_seguimiento where id='$tipificacion' ");
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo = 12; /*12: Mensaje en Buz&oacute;n de voz */
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo,tipificacion) values ($id,'$NUSUARIO','$Fecha','$Hora','Se deja mensaje en buz&oacute;n de voz.',$Codigo,'$tipificacion')");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: BUZON DE VOZ: $observaciones.  ($Ntipificacion)\") where id=$id");
	if ($CERRAR_caso) {
		if ($id3 = qo("select id from call2cola3 where siniestro='$id' "))
			q("update call2cola3 set fecha='$Luego' where siniestro=$id");
		else
			q("insert into call2cola3 (siniestro,fecha) values ('$id','$Luego')");
		//q("update call2cola2 set fecha_aceptacion='$Luego',estado='0' where siniestro=$id");
		//$Idcc=qo1("select id from call2cola2 where siniestro=$id");
		//graba_bitacora('call2cola2','M',$Idcc,'Ajusta la fecha de aceptacion y el estado a 0');
		q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$id' and estado='A' ");
	}
	guarda_escalafon(3);
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function caso_buzon_tercera_persona()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	// BUSCA LOS EVENTOS DE BUZON DEL CASO
	$Cantidad_eventos = qo1("select count(id) from seguimiento where siniestro=$id and tipo=13");
	html();
	echo "<script language='javascript'>
		function validar_envio_buzon()
		{
			with(document.forma)
			{
				if(!alltrim(observaciones.value)) {alert('Debe digitar las observaciones');observaciones.style.backgroundColor='ffffaa';observaciones.focus();return false;}
				if(confirm('Est&aacute; seguro de guardar el Mensaje?')) submit();
			}
		}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'><h4>Mensaje con Tercera Persona ($Cantidad_eventos eventos)</h4>
		<form action='zcallcenter2.php' method='post' target='_self' name='forma' id='forma'>
			<br>Observaciones: <input type='text' name='observaciones' id='observaciones' size='80'><br>
			<br><input type='hidden' name='Acc' value='caso_buzon_tercera_persona_ok'>
			<input type='hidden' name='id' value='$id'>
			<input type='button' style='font-size:16px;font-weight:bold' value=' CONTINUAR ' onclick='validar_envio_buzon();'>
		</form>
	</body>";
}

function caso_buzon_tercera_persona_ok()
{
	global $id, $NUSUARIO, $IDUSUARIO, $observaciones;
	$minutos = qo1("select tiempo_remarcacion2 from aseguradora,siniestro where aseguradora.id=siniestro.aseguradora and siniestro.id=$id");
	$Ahora = date('Y-m-d H:i:s');
	$Luego = date('Y-m-d') . ' ' . aumentaminutos(date('H:i:s'), $minutos);
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo = 13; /*13: Mensaje con tercera persona */
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','Se deja mensaje con tercera persona.',$Codigo)");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: BUZON TERCERA PERSONA: $observaciones. \") where id=$id");
	//q("insert into call2cola1 (siniestro,fecha) values ('$id','$Luego')");
	//q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$id' and estado='A' ");
	guarda_escalafon(8);
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function caso_buzon_tercera_persona_ok_old()
{
	global $id, $NUSUARIO, $IDUSUARIO, $observaciones;
	$minutos = qo1("select tiempo_remarcacion2 from aseguradora,siniestro where aseguradora.id=siniestro.aseguradora and siniestro.id=$id");
	$Ahora = date('Y-m-d H:i:s');
	$Luego = date('Y-m-d') . ' ' . aumentaminutos(date('H:i:s'), $minutos);
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo = 13; /*13: Mensaje con tercera persona */
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','Se deja mensaje con tercera persona.',$Codigo)");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: BUZON TERCERA PERSONA: $observaciones. \") where id=$id");
	q("insert into call2cola1 (siniestro,fecha) values ('$id','$Luego')");
	q("update call2proceso set fecha_cierre='$Ahora',estado='C' where agente='$IDUSUARIO' and siniestro='$id' and estado='A' ");
	guarda_escalafon(8);
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}



function adicionar_observaciones()
{
	global $id, $USUARIO, $NUSUARIO, $d, $tipoCaja,$date;
	$H1 = date('Y-m-d');
	$H2 = date('H:i:s');
	$Ahora = $H1 . ' ' . $H2;
	$Idn1 = q("update siniestro set observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]:$d'),tipo_caja = '$tipoCaja'  where id=$id");
	// echo "<script>
	// alert('$date');
	// </script>";
	if (!empty($date)) {
		$Idn2 = q("update siniestro set fecha_solicitud_usuario='$date' where id=$id");
	}
	$Idn = q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'$H1','$H2','$NUSUARIO','$d',7)"); // 7: observacion general


	graba_bitacora('siniestro', 'M', $id, 'Observaciones');
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	echo "<body><script language='javascript'>

	parent.recargar();</script></body>";
}

function adicionar_observaciones2()
{
	echo "test";
}

function marcar_proceso_actualizacion()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	$Ahora = date('Y-m-d H:i:s');
	q("update call2infoerronea set fecha_proceso='$Ahora', procesado_por='$NUSUARIO' where id=$id");
	$D = qo("select * from call2infoerronea where id=$id");
	q("update siniestro set observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]: Actualizacion Informacion Erronea Procesada.') where id=$D->siniestro");
	$H1 = date('Y-m-d');
	$H2 = date('H:i:s');
	$Idn = q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($D->siniestro,'$H1','$H2','$NUSUARIO','Se recibe actualizacion por parte de la aseguradora.',8)"); // 8: ACTUALIZACION DE DATOS
	graba_bitacora('siniestro', 'M', $D->siniestro, 'Observaciones,Actualizacion aseguradora');
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona registro');
	echo "<body><script language='javascript'>opener.parent.location.reload();window.close();void(null);</script></body>";
}

function solicita_reactivacion()
{
	global $id, $NUSUARIO, $IDUSUARIO;
	$Sin = qo("select * from siniestro where id=$id");
	q("insert into solicitud_siniestro_reactivacion (siniestro,fecha,solicitado_por,justificacion) values ($Sin->id,'$Ahora','$NUSUARIO','$justificacion1')");
	echo "Ya se hizo la solicitud";
	// $Dagente=qo("select * from usuario_callcenter where id=$IDUSUARIO");
	// $Sin=qo("select * from siniestro where id=$id");
	// $Ciudad=qo("select * from ciudad where codigo='$Sin->ciudad' ");
	// if($Sin->ciudad_original) $Ciudado=qo("select * from ciudad where codigo='$Sin->ciudad_original' ");
	// else $Ciudado=$Ciudad;
	// $Fecha=date('Y-m-d H:i');
	// html('Solicitud de Modificacion');
	// $Ciudades=menu1("ciudad","select ciu.codigo,ciu.nombre as nciudad from ciudad ciu,oficina ofi where ofi.ciudad=ciu.codigo order by nciudad ",$Ciudad,1);
	// echo "<script language='javascript'>
	// 		function carga() {centrar(500,500);}
	// 		function activaruno() { if(document.forma.uno.checked) {document.getElementById('tduno').style.visibility='visible';document.forma.justificacion1.focus();} else document.getElementById('tduno').style.visibility='hidden'; }
	// 		function activardos() { if(document.forma.dos.checked) document.getElementById('tddos').style.visibility='visible'; else document.getElementById('tddos').style.visibility='hidden'; }
	// 		function enviar_solicitud()
	// 		{
	// 			with(document.forma)
	// 			{
	// 				if(uno.checked)
	// 				{
	// 					if(!alltrim(justificacion1.value))
	// 					{
	// 						alert('Debe justificar por que se desea pasar a Pendiente el estado de este siniestro');
	// 						justificacion1.style.backgroundColor='ffffdd';
	// 						return false;
	// 					}
	// 				}
	// 				if(dos.checked)
	// 				{
	// 					if(!alltrim(justificacion2.value))
	// 					{
	// 						alert('Debe justificar por que se desea cambiar de ciudad este siniestro');
	// 						justificacion2.style.backgroundColor='ffffdd';
	// 						return false;
	// 					}
	// 					if(!ciudad.value)
	// 					{
	// 						alert('Debe especificar una ciudad valida');
	// 						ciudad.style.backgroundColor='ffffdd';
	// 						return false;
	// 					}
	// 				}
	// 				if(!(uno.checked || dos.checked))
	// 				{
	// 					alert('La solicitud debe contener uno o los dos conceptos que son Reactivacion o Cambio de Ciudad');
	// 					return false;
	// 				}
	// 			}
	// 			document.forma.btn_enviar.style.visibility='hidden';
	// 			document.forma.submit();
	// 		}
	// 	</script>
	// 	<body onload='carga()'>";
	// 	$Aseg=qo("select * from aseguradora where id=$Sin->aseguradora");
	// 	$Ofic=qo("select * from oficina where ciudad=$Sin->ciudad");
	// 	$Ciuorig=qo1("select concat(departamento,' - ',nombre) from ciudad where codigo='$Sin->ciudad_original' ");
	// 	$Estado=qo("select * from estado_siniestro where id=$Sin->estado");
	// 	echo "<body><h3>$NUSUARIO .:. CASO ABIERTO: $id</h3>
	// 		<table cellspacing=4>
	// 		<tr><td bgcolor='ffffff'> <img src='$Aseg->emblema_f' border='0' height='30px'> </td>
	// 			<td bgcolor='ffffff'> <b style='font-size:18px'>$Aseg->nombre </b></td>
	// 			<td bgcolor='ffffff' align='center'> <b style='font-size:16px;'>OFICINA:<br>$Ofic->nombre</b> </td>
	// 			<td bgcolor='ffffdd' rowspan='2' align='center'> <b>PLACA:</b><br><b style='font-size:20px'>$Sin->placa</b> </td>
	// 			<td rowspan='2' bgcolor='000000'> <img src='$Dagente->foto_f' border='5' height='100px'> </td>
	// 		</tr>
	// 		<tr>
	// 			<td bgcolor='ffffaa'> DIAS: <b style='font-size:20px'>$Sin->dias_servicio</b> </td>
	// 			<td bgcolor='ffffff'> <b style='font-size:16px'>Numero Siniestro: $Sin->numero</b><br><br><center>ESTADO:
	// 			<b style='background-color:ddddff;font-size:14px;".($Sin->estado==1?'color:aa0000;':'')."'> $Estado->nombre </b></center></td>
	// 			<td bgcolor='ffffff'> Ciudad Original:<br>$Ciuorig</td></tr>
	// 		</table>

	// 	<form action='zcallcenter2.php' method='post' target='_self' name='forma' id='forma'>
	// 	<h3>Solicitud de Modificacion de Siniestro</h3>
	// 	Usuario: $NUSUARIO   Fecha: $Fecha <br />
	// 	<table border cellspacing='0' width='100%'>
	// 		<tr><td>Reactivacion <input type='checkbox' name='uno' onchange='activaruno();' ".($Sin->estado==1?'':"disabled")."> El estado actual es: <b>".qo1("select t_estado_siniestro($Sin->estado)")."</b></td></tr>
	// 		<tr><td  id='tduno' style='visibility:hidden;'>Esta opcion solicita pasar el estado a PENDIENTE por favor escriba la justificacion: <br /><textarea name='justificacion1' style='font-family:arial;font-size:12' rows='4' cols='80' valign='top'></textarea></td></tr>
	// 		<tr><td>Cambio de Ciudad <input type='checkbox' name='dos' onchange='activardos();'></td></tr>
	// 		<tr><td id='tddos' style='visibility:hidden;'>Ciudad: $Ciudades Justificacion: <br><textarea name='justificacion2' style='font-family:arial;font-size:12' rows='4' cols='80' valign='top'></textarea></td></tr>
	// 	</table>
	// 	<center><input type='button' id='btn_enviar' name='btn_enviar' value=' ENVIAR SOLICITUD DE REACTIVACION/MODIFICACION ' onclick='enviar_solicitud()'></center>
	// 	<input type='hidden' name='id' value='$id'><input type='hidden' name='Acc' value='solicita_reactivacion_ok'>
	// </form>";
}

function solicita_reactivacion_ok()
{

	global $id, $uno, $dos, $justificacion1, $justificacion2, $ciudad, $Nusuario, $Hoy, $NUSUARIO, $Ahora;

	$uno = sino($uno);
	$dos = sino($dos);
	$IDM = q("insert into solicitud_modsin (siniestro,cambio_estado,justificacion1,cambio_ciudad,ciudad,justificacion2,solicitado_por,fec_solicitud) values
		('$id','$uno',\"$justificacion1\",'$dos','$ciudad',\"$justificacion2\",'$Nusuario','$Hoy' )");
	$Nueva_ciudad = qo1("select t_ciudad('$ciudad') ");
	$Sin = qo("select id,numero,asegurado_nombre,ciudad,ciudad_original,aseguradora,fec_autorizacion from siniestro where id=$id");
	$Ciudad = qo("select * from ciudad where codigo='$Sin->ciudad' ");
	if ($Sin->ciudad_original) $Ciudado = qo("select * from ciudad where codigo='$Sin->ciudad_original' ");
	else $Ciudado = $Ciudad;
	$Aseguradora = qo1("select nombre from aseguradora where id=$Sin->aseguradora");
	if (dias($Sin->fec_autorizacion, date('Y-m-d')) > 180) {
		$Ruta1 = "utilidades/Operativo/operativo.php?Acc=modificar_siniestro&idm=$IDM&Fecha=$Hoy&Usuario=GABRIEL SANDOVAL";
		$Email_usuario = usuario('email');
		$Mensaje = '';
		$Mensaje .= "<body><b>SOLICITUD DE MODIFICACION DE SINIESTROS</B><BR><BR>Numero Siniestro: $Sin->numero - $Aseguradora<br>" .
			"Asegurado:$Sin->asegurado_nombre<br>Ciudad: $Ciudad->nombre ($Ciudad->departamento) <br>Ciudad Original: $Ciudado->nombre ($Ciudado->departamento)<br>" .
			"Fecha de autorizacion: $Sin->fec_autorizacion<br>";
		if ($uno) {
			$Mensaje .= "<br><b>Cambio de estado a PENDIENTE: </b>$justificacion1<br>";
		}
		if ($dos) {
			$Mensaje .= "<br><b>Cambio de ciudad a $Nueva_ciudad: </b>$justificacion2<br>";
		}
		$Mensaje .= "<br>Funcionario que solicita: $Nusuario Fecha de solicitud: $Hoy <br><br>";
		$Mensaje .= "Para activar haga click aqui: <a href='https://app.aoacolombia.com/i.php?i=" . base64_encode("\$Programa='$Ruta1';\$Fecha_control='" . date('Y-m-d') . "';") . "' target='_blank'>Aprobar la modificacion</a></body>";

		$data_mail = array(
			"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
			"enviarEmail" => "true",
			"id" => $id,
			"contenido" => "9",
			"para" =>  "danielsuarez@aoacolombia.com",
			"copia" => "sandraosorio@aoacolombia.com",
			"nameArchivo" => $nameArchivo,
			"asunto" => "Solicitud Modificacion Siniestro $Sin->numero",
			"Nusuario" => $Nusuario,
			"departamento" => $departamento,
			"justificacion2" => $justificacion2,
			"Nueva_ciudad" => $Nueva_ciudad,
			"justificacion1" => $justificacion1,
			"Sinfec_autorizacion" => $Sin->fec_autorizacion,
			"Ciudadodepartamento" => $Ciudado->departamento,
			"Ciudadonombre" => $Ciudado->nombre,
			"departamento" => $Ciudad->departamento,
			"IDM" => $IDM,
			"Hoy" => $Hoy,
			"ciudad" => $ciudad,
			"Nprov" => $Nprov,
			"Ciudadnombre" => $Ciudad->nombre,
			"Aseguradora" => $Aseguradora,
			"numero" => $Sin->numero
		);



		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/ServiEmail.php');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
		curl_exec($ch);
		curl_close($ch);





		echo "Envio exitoso a: sandraosorio@aoacolombia.com.";
	} else {
		if ($uno) {
			$Ahora = date('Y-m-d H:i:s');
			q("update solicitud_modsin set aprobado_por='$NUSUARIO',fec_aprobacion='$Ahora' where id=$IDM");
			q("update siniestro set estado=5,causal=0,subcausal=0 where id=$id ");
			q("update solicitud_siniestro_reactivacion set estado=1, fecha='$Ahora', autorizado_por='$NUSUARIO' where siniestro=$id ");
			// q("insert into solicitud_siniestro_reactivacion (siniestro,fecha,solicitado_por,justificacion) values ($id,'$Ahora','$NUSUARIO','$justificacion1')");
			graba_bitacora('siniestro', 'M', $id, "Reactiva el siniestro");
			echo "Reactivacion del Siniestro exitosa.";
		}
		if ($dos) {
			q("update solicitud_modsin set aprobado_por='$NUSUARIO',fec_aprobacion='$Ahora' where id=$IDM");
			q("update siniestro set ciudad='$ciudad' where id=$id");
			graba_bitacora('siniestro', 'M', $id, "Cambia de ciudad");
			echo "Cambio de ciudad del Siniestro exitosa.";
		}
	}
	echo "<br /><br /><CENTER><INPUT TYPE='BUTTON' VALUE='CERRAR ESTA VENTANA' onclick='window.close();void(null);' style='font-size:16;font-weight:bold; height=30px;'></center>";
}

function adjudicar_vehiculo()
{
	global $f, $v, $NUSUARIO, $tokenNick;
	html('ADJUDICACION DE VEHICULO ');

	if (!($_SESSION['Adjudicacion_SINIESTRO'] && $_SESSION['Adjudicacion_OFICINA'] && $_SESSION['Adjudicacion_ASEGURADORA'])) {
		echo "<body><script language='javascript'>centrar(400,200);alert('Adjudicacion caida, por favor empiece nuevamente desde el madulo de Call Center');window.close();void(null);</script></body>";
		die();
	}

	$Siniestro = $_SESSION['Adjudicacion_SINIESTRO'];
	$OFicina = $_SESSION['Adjudicacion_OFICINA'];
	$Aseguradora = $_SESSION['Adjudicacion_ASEGURADORA'];
	$Nivel = $_SESSION['Adjudicacion_NIVEL'];
	if (!$_SESSION['Adjudicacion_READJUDICAR']) {
		if ($Cita_pendiente = qo("Select * from cita_servicio where siniestro=$Siniestro and estado='P' ")) {
			echo "<body>";
			echo "<script language='javascript'>alert('Aun existe una cita pendiente del dia $Cita_pendiente->fecha $Cita_pendiente->hora con el veh&iacute;culo $Cita_pendiente->placa');window.close();void(null);</script>";
			echo "</body>";
			die();
		}
	}
	include('inc/link.php');
	$Sin = qom("Select * from siniestro where id=$Siniestro", $LINK);
	$V = qom("select *,t_linea_vehiculo(linea) as nlinea from vehiculo where id=$v", $LINK);
	if ($V->flota != $Sin->aseguradora) {       //   GAMA 2  ==8      GAMA 1 == 1          O   GAMA 9  Y  GAMA 8
		//		if( ($V->flota==8 && $Sin->aseguradora==1) || ($V->flota==9 && $Sin->aseguradora==8)  )
		//		{
		//			$Ahora=date('Y-m-d H:i:s');
		//			q("insert into cambio_flota (vehiculo,flota_original,flota_temporal,autorizado_por,fecha,solicitado_por,justificacion) values
		//			('$v','$V->flota','$Sin->aseguradora','AUTOMATICO','$Ahora','$NUSUARIO',\"Cambio automatico de gamas Allianz.\")");
		//		}
		//		else
		if ($Q = qo("select * from cambio_flota where date_format(fecha,'%Y-%m-%d')='" . date('Y-m-d') . "'  and vehiculo='$v' and flota_original='$V->flota' and flota_temporal='$Sin->aseguradora' ")) {
			$Cambio_autorizado = "Cambio de flota autorizado por $Q->autorizado_por ";
		} else {
			//if($tokenNick=='jemes.navarro'){
			$formulario = "<form action='zcallcenter2.php' target='Oculto_autorizacion_cambio_flotas' method='POST' name='formasol' id='formasol'>
                                       <fieldset>
                                          <legend>Solicitar Clave Para cambio de flota</legend>
					
					Justificacion: <input type='text' name='justificacion' id='justificacion' value='' size='80' maxlength='200'><br><br>
					
					<input type='button' name='autorizar' id='autorizar' value=' SOLICITAR CAMBIO DE FLOTA' style='font-size:16px' onclick='validar_cambio_flota_sol();'><br><br>
					<br>
                                        <i>Esta solicitud le aparecera en el dashboard del coordinador.</i>
                                         <input type='hidden' name='Acc' value=''>
					<input type='hidden' name='vehiculo' value='$v'>
					<input type='hidden' name='flota_original' value='$V->flota'>
					<input type='hidden' name='flota_temporal' value='$Sin->aseguradora'>
                                      <fieldset>
				</form>";
			//}else{
			$formulario2 = "<form action='zcallcenter2.php' target='Oculto_autorizacion_cambio_flota' method='POST' name='forma' id='forma'>
					Por Orden de Gerencia, se debe justificar todo cambio de flota.<br><br>
					Justificacion : <input type='text' name='justificacion' id='justificacion' value='' size='80' maxlength='200'><br><br>
					Clave de Supervisor para autorizar el cambio de flota: <input type='password' name='clavesupervisor' id='clavesupervisor' value='' size='10' ><br><br>
					<input type='button' name='autorizar' id='autorizar' value=' AUTORIZAR CAMBIO DE FLOTA ' style='font-size:16px' onclick='validar_cambio_flota();' >  <input type='button' name='autorizar' id='autorizar' value=' AUTORIZAR CAMBIO DE FLOTA ' style='font-size:16px' onclick='validar_cambio_flota();' ><br><br>
					<input type='hidden' name='Acc' value=''>
					<input type='hidden' name='vehiculo' value='$v'>
					<input type='hidden' name='flota_original' value='$V->flota'>
					<input type='hidden' name='flota_temporal' value='$Sin->aseguradora'>

				</form>";
			//}
			echo "
				<script language='javascript'>
					function validar_cambio_flota()
					{
						with(document.forma)
						{
							if(!alltrim(justificacion.value)) {alert('Debe justificar el cambio de flota.');justificacion.style.backgroundColor='ffffdd';justificacion.focus();return false;}
							if(confirm('Desea Autorizar el cambio de Flota o Gama?'))
							Acc.value='autorizar_cambio_gama';
							submit();
						}
					}
                                        function validar_cambio_flota_sol()
					{
						with(document.formasol)
						{
							if(!alltrim(justificacion.value)) {alert('Debe justificar el cambio de flota.');justificacion.style.backgroundColor='ffffdd';justificacion.focus();return false;}
							if(confirm('Desea Solicitar el cambio de Flota o Gama?'))
							Acc.value='solicitud_cambio_gama';
							submit();
						}
					}
					function recargar() { document.location.reload(); }

				</script>
				<body style='font-size:16px' bgcolor='ffffdd'><script language='javascript'>centrar(500,400);</script>
				 <br>
                                $formulario
                               
				<iframe name='Oculto_autorizacion_cambio_flota' id='Oculto_autorizacion_cambio_flota' style='visibility:hidden' width='1' height='1'></iframe>
				</body>";
			die();
		}
	}
	$Oficina = qom("select * from oficina where id=$V->ultima_ubicacion", $LINK);
	$Hora = $Oficina->hora_inicial;
	if ($Cita = qom("select c.id,c.fecha,c.hora from cita_servicio c , vehiculo v where c.placa=v.placa and v.id=$v and c.estado='P' limit 1", $LINK)) {
		if ($_SESSION['Adjudicacion_READJUDICAR']) {
			echo "<body><script language='javascript'>centrar(400,200);
				if(confirm('Aun tiene una cita pendiente el dia " . fecha_completa($Cita->fecha) . " a las $Cita->hora. Desea Cancelar la re-asignacion de veh&iacute;culo en proceso?'))
					window.open('zcallcenter2.php?Acc=mata_reasignacion','_self');
				else {window.close();void(null);}
				</script></body>";
			mysql_close($LINK);
			die();
		} else {
			echo "<body><script language='javascript'>centrar(400,200);
				alert('Aun tiene una cita pendiente el dia " . fecha_completa($Cita->fecha) . " a las $Cita->hora');
				window.close();void(null);</script></body>";
			mysql_close($LINK);
			die();
		}
	}
	$Ultimo = qom("select u.*,e.nombre as nestado from ubicacion u,estado_vehiculo e where u.vehiculo=$v and u.fecha_final<='$f' and u.estado=e.id
						order by u.fecha_final desc, u.id desc limit 1", $LINK);
	if ($Nivel > 2) $Pasa_estado = inlist($Ultimo->estado, '1,2,4,5,7,92,94,96,102');
	else $Pasa_estado = inlist($Ultimo->estado, '1,2,102');
	if ($Pasa_estado) {
		echo "<script language='javascript'>
			function asigna(hora)	{document.forma.Hora.value=hora;document.forma.submit();}
			function adjudicacion_finalizada() {opener.adjudicacion_finalizada();window.close();void(null);}
			function ver_accion_cita() {centrar();document.getElementById('Accion_cita').style.visibility='visible';}
			function cerrar_re_asignacion(){window.close();void(null);}
			</script>
		<body bgcolor='eeeeee' style='font-size:14px'><script language='javascript'>centrar(600,600);</script>
		<h4>$Cambio_autorizado</h4>
		<iframe name='Accion_cita' id='Accion_cita' style='visibility:hidden;position:absolute;' width='98%' height='90%' frameborder='no'></iframe>
			<form action='zcallcenter2.php' target='Accion_cita' method='POST' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='asigna_cita'>
				<input type='hidden' name='Oficina' value='$Oficina->id'>
				<input type='hidden' name='Siniestro' value='$Siniestro'>
				<input type='hidden' name='Vehiculo' value='$V->placa'>
				<input type='hidden' name='Dias_servicio' value='$Sin->dias_servicio'>
				<input type='hidden' name='Fecha' value='$f'>
				<input type='hidden' name='Hora' value=''>
			</form>";
		$Concur = array();
		if ($Concurrencias = mysql_query("select count(id) as cantidad, hora from cita_servicio where estado in ('P','C') and oficina=$Oficina->id and fecha='$f' group by hora order by hora", $LINK))
			while ($Co = mysql_fetch_object($Concurrencias)) {
				$Concur[$Co->hora] = $Co->cantidad;
			}


		if ($Ultimo->estado == 1) // estado en servicio
		{
			// debemos averiguar la hora de llegada
			if ($Cita = qom("select * from cita_servicio where placa='$V->placa' and fecha='$Ultimo->fecha_inicial' and estado='C' limit 1", $LINK)) {
				echo "<h3>ASIGNACION DE VEHICULO .:. OFICINA: $Oficina->nombre</h3>
				Placa: <b style='font-size:16px'>$V->placa</b> Linea: $V->nlinea <br>Fecha de Asignacion: <b>$f - " . ndiasemana($f) . "</b><br>
				<table bgcolor='ffffff'><tr><td valign='top'><table border cellspacing='0' bgcolor='ffffff'><tr><th>Hora</th><th>Concurrencia</th><th>Observaciones</th><th>Opcion</th></tr>";
				while ($Hora <= $Oficina->hora_final) {
					$fondo = '#ffffff';
					$Bloqueo = '';
					$Concurrencia = $Concur[$Hora];
					if (strpos(',' . $Oficina->restriccion_almuerzo, l($Hora, 5))) {
						$fondo = '#ffffcc';
						if ($Concurrencia >= $Oficina->concurrenciaa || $Oficina->concurrenciaa == 0) $Bloqueo = 'ffaaaa';
					} else {
						if ($Concurrencia >= $Oficina->concurrencia) $Bloqueo = 'ffaaaa';
					}
					// VERIFICACION DE SI EL VEHICULO TIENE FECHA DE DEVOLUCI&oacute;N EN UNA HORA DETERMINADA.. BLOQUEA LAS HORAS HASTA LA ENTREGA Y 1 HORA MAS PARA EL ALISTAMIENTO
					if ($Cita->fec_devolucion == $f) {
						if ($Hora <= $Cita->hora_devol) {
							$Bloqueo = 'aaffaa';
						} else if ($Hora <= aumentaminutos($Cita->hora_devol, 60)) {
							$Bloqueo = 'aaffdd';
						}
					}
					echo "<tr bgcolor='$fondo'><td>$Hora</td><td align='center'>$Concurrencia</td><td bgcolor='$Bloqueo'>";
					//if($Bloqueo=='ffffdd') echo " Almuerzo";
					if ($Bloqueo == 'ffdddd') echo " Concurrencia";
					if ($Bloqueo == 'aaffaa') echo " En Servicio";
					if ($Bloqueo == 'aaffdd') echo " Hora de Gracia";
					echo "</td><td>";
					if (!$Bloqueo) echo "<input type='button' name='asignar' id='asignar' value=' Asignar ' onclick=\"asigna('$Hora');\">";
					echo "</td></tr>";
					$Hora = aumentaminutos($Hora, 30);
					if ($Hora == '13:00:00') echo "</table></td><td valign='top'><table border cellspacing='0' bgcolor='ffffff'><tr><th>Hora</th><th>Concurrencia</th><th>Observaciones</th><th>Opcion</th></tr>";
				}
				echo "</table></td></tr></table><br>Pico y placa en $Oficina->nombre: $Oficina->picoyplaca<br><br>Rango de almuerzo: $Oficina->restriccion_almuerzo ";
				echo "</body>";
				mysql_close($LINK);
				die();
			}
		}
		echo "<h3>ASIGNACION DE VEHICULO .:. OFICINA: $Oficina->nombre</h3>
			Placa: <b style='font-size:16px'>$V->placa</b> Linea: $V->nlinea <br>Fecha de Asignacion: <b>$f - " . ndiasemana($f) . "</b><br>
			<table bgcolor='ffffff'><tr><td valign='top'><table border cellspacing='0' bgcolor='ffffff'><tr><th>Hora</th><th>Concurrencia</th><th>Observaciones</th><th>Opcion</th></tr>";
		while ($Hora <= $Oficina->hora_final) {
			$Bloqueo = '';
			$Concurrencia = $Concur[$Hora];
			if ($Concurrencia >= $Oficina->concurrencia) $Bloqueo = 'ffdddd';
			if (strpos(',' . $Oficina->restriccion_almuerzo, l($Hora, 5)))  $Bloqueo = 'ffffdd';
			echo "<tr><td>$Hora</td><td align='center'>$Concurrencia</td><td bgcolor='$Bloqueo'>";
			if ($Bloqueo == 'ffffdd') echo " Almuerzo";
			if ($Bloqueo == 'ffdddd') echo " Concurrencia";
			if ($Bloqueo == 'aaffaa') echo " En Servicio";
			if ($Bloqueo == 'aaffdd') echo " Hora de Gracia";
			echo "</td><td>";
			if (!$Bloqueo) echo "<input type='button' name='asignar' id='asignar' value=' Asignar ' onclick=\"asigna('$Hora');\">";
			echo "</td></tr>";
			$Hora = aumentaminutos($Hora, 30);
			if ($Hora == '13:00:00') echo "</table></td><td valign='top'><table border cellspacing='0' bgcolor='ffffff'><tr><th>Hora</th><th>Concurrencia</th><th>Observaciones</th><th>Opcion</th></tr>";
		}
		echo "</table></td></tr></table><br>Pico y placa en $Oficina->nombre: $Oficina->picoyplaca<br><br>";
		echo "</body>";
	} else echo "<body><script language='javascript'>centrar(400,200); alert('No se puede adjuciar en estado: $Ultimo->nestado');window.close();void(null);</script></body>";
	mysql_close($LINK);
}

function pre_activar()
{
	html();
	echo "<body><script language='javascript'>opener.activar();</script></body>";
}

function asigna_cita()
{
	global $Oficina, $Siniestro, $Vehiculo, $Dias_servicio, $Fecha, $Hora, $NUSUARIO;
	$Nhora = date('h:i A', strtotime($Fecha . ' ' . $Hora));
	$OF = qo("select * from oficina where id=$Oficina");
	$Veh = qo("select * from vehiculo where placa='$Vehiculo' ");

	$Sin = qo("select * from siniestro where id=$Siniestro");
	$flota = $_SESSION['Adjudicacion_ASEGURADORA'];
	html('ASIGNACION DE VEHICULO');
	echo "<script language='javascript'>
		function guardar_cita()
		{
			if(document.forma.tipogarantia.value)
			{
				if(confirm('Esta seguro de Agendar esta cita?'))
				{
					document.forma.Acc.value='asigna_cita_ok';
					document.forma.submit();
					document.forma.Enviar.style.visibility='hidden';
				}
			}
			else {alert('DEBE SELECCIONAR EL TIPO DE GARANTIA');document.forma.tipogarantia.style.backgroundColor='#ffffdd';return false;}
		}
		function adjudicacion_finalizada()
		{
			parent.adjudicacion_finalizada();
		}
		function adjudicacion_datos_financieros(ids)
		{
			window.open('zcallcenter2.php?Acc=pide_datos_financieros&ids='+ids,'_self');
		}
		function cerrar_re_asignacion()
		{
			alert('Re-Asignacion Satisfactoria');
			window.open('zcallcenter2.php?Acc=recargar_control','Oculto_control');
			parent.cerrar_re_asignacion();
		}
	</script>
	<body style='font-size:14px' bgcolor='ffffff'><script language='javascript'>centrar();parent.ver_accion_cita();</script>
	<h3>ASIGNACION DE VEHICULO Y CITA DE ENTREGA</h3>";
	if ($_SESSION['Adjudicacion_READJUDICAR']) {
		$Citaprevia = qo("select * from cita_servicio where siniestro='$Siniestro' and estado='P' ");
		$conductor = $Citaprevia->conductor;
		$observaciones = $Citaprevia->observaciones;
		$dir_domicilio = $Citaprevia->dir_domicilio;
		$tel_domicilio = $Citaprevia->tel_domicilio;
	} else {
		$conductor = $Sin->declarante_nombre;
		$tel_domicilio = $dir_domicilio = $observaciones = '';
	}
	echo "<form action='zcallcenter2.php' method='post' target='Oculto_agendar' name='forma' id='forma'>
	<br />Oficina: <b style='color:000000;font-size:14px;'>$OF->nombre ($OF->direccion)</b> <input type='hidden' name='oficina' value='$Oficina'>
	<input type='hidden' name='siniestro' id='siniestro' value='$Siniestro'>
	<input type='hidden' name='flota' id='flota' value='$flota'>
	<br /><br />Veh&iacute;culo asignado: <input type='text' name='placa' value='$Veh->placa' readonly style='font-size:12;font-weight:bold' size=7>
	Fecha y hora de la cita: <input type='hidden' name='fecha' value='$Fecha'>
	<b style='font-size:12;font-weight:bold;color:000000'>" . fecha_completa($Fecha) . "</b> HORA:
	<input type='hidden' name='hora' value='$Hora'> <b style='font-size:12;font-weight:bold;color:000000'>$Nhora</b>
	<br /><br /><b style='font-size:16;color:0000ff;text-decoration:blink;'>Persona quien va a recoger el veh&iacute;culo o conductor: </b>
	<input type='text' name='conductor' value='$conductor' style='font-size:12;font-weight:bold' size='50'><br />
	Este ser&aacute; el nombre que aparezca en el ACTA DE ENTREGA. Si quien recoge el veh&iacute;culo es un TERCERO, indique los requisitos adicionales.
	<br />Observaciones:
	<br /><textarea name='observaciones' id='observaciones' style='font-size:14' rows='5' cols='100'>$observaciones</textarea><br />
	SELECCIONE EL TIPO DE GARANTIA <select name='tipogarantia'><option value=''></option><option value='1'>SIN REEMBOLSO</option><option value='2'>REEMBOLSABLE</option><option value='3'>SIN GARANTIA</option></select><br>
	
	<table bgcolor='ddddff'><tr><td align='center' colspan=2 style='font-size:16'><B>DOMICILIO ENTREGA</B></td></tr>
	<tr><td align='right' style='font-size:14'>Direccion Domicilio:</td><td><input type='text' style='font-size:14' name='dir_domicilio' value='$dir_domicilio' size='100' maxlength='200' onblur='valida_domicilio();'></td></tr>
	<tr><td align='right' style='font-size:14'>Tel&eacute;fono Domicilio:</td><td><input type='text' style='font-size:14' name='tel_domicilio' value='$tel_domicilio' size='30' maxlength='50' onblur='valida_domicilio();'></td></tr>
	<tr ><td align='center' colspan='2'><input type='button' id='btn_envio_aviso'  name='btn_envio_aviso' value=' Enviar Aviso de Llamada a Autorizaciones ' style='visibility:hidden;font-weight:bold;font-size:14px;'	onclick='aviso_autorizacion();'></td></tr>
	</table>
	<br>
	<table bgcolor='ddddff'><tr><td align='center' colspan=2 style='font-size:16'><B>DOMICILIO DEVOLUCI&oacute;N</B></td></tr>
	<tr><td align='right' style='font-size:14'>Direccion domicilio devolucion:</td><td><input type='text' style='font-size:14' name='dir_domiciliod' value='$dir_domiciliod' size='100' maxlength='200' onblur='valida_domiciliod();'></td></tr>
	<tr><td align='right' style='font-size:14'>Tel&eacute;fono Domicilio devolucion:</td><td><input type='text' style='font-size:14' name='tel_domiciliod' value='$tel_domiciliod' size='30' maxlength='50' onblur='valida_domiciliod();'></td></tr>
	<tr ><td align='center' colspan='2'><input type='button' id='btn_envio_aviso'  name='btn_envio_aviso' value=' Enviar Aviso de Llamada a Autorizaciones ' style='visibility:hidden;font-weight:bold;font-size:14px;'	onclick='aviso_autorizacion();'></td></tr>
	</table>
	
	<br /><br />
	Se&ntilde;or usuario $NUSUARIO, el agendamiento de la cita quedar&aacute; registrada a nombre suyo con fecha y hora de registro.<br /><br />
	<input type='button' id='Enviar' name='Enviar' value=' AGENDAR CITA ' style='font-size:14;font-weight:bold;height:30px;width:300px' onclick='guardar_cita()'>
	<input type='hidden' name='Acc' id='' value=''>
	</form>
	<br><br>
	La informacion de esta cita ser&aacute; enviada al correo <b>$Sin->declarante_email </b>
	<iframe name='Oculto_agendar' id='Oculto_agendar' style='visibility:hidden' width=1 height=1></iframe></body>";
}

function enviar_mail_servicio_html()
{
	error_reporting(E_ALL);
	header('Content-Type: text/html; charset=utf-8');
	ob_start();
	include("CorreosHtml/citaProgramada.php");
	$buffer = ob_get_clean();

	//print_r($buffer);


	$envio = enviar_gmail(
		'sistemas@aoacolombia.com',
		'Sergio Castillo Castro',
		"ventas.javc@gmail.com",
		'',
		'Objeto del mensaje',
		utf8_decode($buffer),
		'',
		'sistemas@aoacolombia.com',
		'jl6316!'
	);

	print_r($envio);
	exit;
}


function asigna_cita_ok_testHtml()
{


	error_reporting(E_ALL);

	global $oficina, $siniestro, $flota, $placa, $fecha, $hora, $conductor, $observaciones, $NUSUARIO, $dir_domicilio, $dir_domiciliod, $tel_domicilio, $tel_domiciliod, $tipogarantia;
	global $IDUSUARIO, $NUSUARIO;
	// VALIDACION PARA EVITAR QUE UN VEHICULO NO SEA ASIGNADO A DOS SERVICIOS EN EL MISMO LAPSO DE TIEMPO
	// se busca en la tabla de citas alguna que est&aacute; en estado programada


	include('inc/link.php');

	$OF = qom("select * from oficina where id=$oficina", $LINK);

	//print_r($OF);

	//exit;

	$Nhora = date('h:i A', strtotime($fecha . ' ' . $hora));
	$Hoy = date('Y-m-d H:i');
	$Dia = ndiasemana(date('w', strtotime($fecha)));
	$Nciudad = qo1m("select t_ciudad(ciudad) from siniestro where id=$siniestro", $LINK);
	$Ndia = ndiasemana(date('w', strtotime($fecha))) . ' ' . date('d', strtotime($fecha)) . ' de ' . mes(date('m', strtotime($fecha))) . ' de ' . date('Y', strtotime($fecha));


	$S = qom("select * from siniestro where id=$siniestro", $LINK);

	$Aseguradora = qom("select * from aseguradora where id=$S->aseguradora", $LINK);

	//echo "after query";	

	if ($_SESSION['Adjudicacion_OFICINA']) {
		$_SESSION['Adjudicacion_OFICINA'] = false;
		$_SESSION['Adjudicacion_ASEGURADORA'] = false;
		$_SESSION['Adjudicacion_SINIESTRO'] = false;
		$_SESSION['Adjudicacion_NIVEL'] = false;
	}
	mysql_close($LINK);


	//print_r($S);

	$Email_usuario = usuario('email');

	error_reporting(E_ALL);
	header('Content-Type: text/html; charset=utf-8');


	//print_r($buffer);




	if ($S->declarante_email) {
		if ($S->renta) {
			if ($dir_domicilio) {
				$tipoCorreo = 1;
				$tipo_de_prueba = "dir Domicilio renta";
				/*$Correo="
					Respetado(a) Senor(a) $conductor

					Reciba cordial saludo.

					Bienvenido(a) al sistema de adjudicacion del Servicio de Renta de AOA Colombia.

					Este es un correo automatico del Area de Call Center cuyo objetivo es confirmar  la cita programada para el proximo ".fecha_completa($fecha)." a las $Nhora a fin de hacer entrega de un Veh&iacute;culo Rentado en la direccion: $dir_domicilio Telefono: $tel_domicilio,  solicitado por $conductor.

					Es importante que tenga en cuenta los requisitos previamente informados:

					- Documento de Identificacion original en el momento de la entrega de vehiculo.
					- Licencia de Conduccion original debidamente registrada.
					- Haber leido y aceptado los terminos y condiciones del servicio.
					 -$Aseguradora->info_per

					Importante: agradecemos tener en cuenta las siguientes recomendaciones:

					* Recuerde que despues de cumplido el tiempo del servicio, usted debe devolver el vehiculo en las mismas condiciones en que le fue entregado, incluyendo el combustible.
					* La hora pactada para la devolucion corresponde a la hora en la cual se suministro el vehiculo o en su defecto la establecida por la compa��a ajustandose a los horarios de pico y placa establecidos en la ciudad (en caso de aplicar). Una vez cumplido el tiempo de gracia (3 horas) previamente informado para la devolucion, se cobrara un dia adicional. Si desea prorrogar el servicio por mas dias deber� informarlo con 12 horas de anterioridad.
					*(Para cliente natural) La Garantia constituida mediante voucher sera	devuelta a traves de la direccion de correo electronico suministrada en un termino aproximado de 10 dias despues de finalizado el servicio con el fin verificar pagos pendientes. No obstante es importante tener en cuenta que en caso de no recibirlo por algun motivo concerniente a fallas tecnicas, errores de conectividad, entre otros, usted podra acercarse durante los primeros 60 dias de transcurrido el servicio a la oficina en la cual constituyo dicha garantia y reclamar el original. Pasado este tiempo, debera solicitarlo con antelacion para realizar el proceso de alistamiento y entrega del mismo desde nuestra central de archivo.

					Nota: respecto al suministro de sus datos personales (informacion no sensible)

					Es importante que tenga en cuenta que para acceder al servicio de renta corto plazo, usted puede haber suministrado y/o suministrar  de manera voluntaria y libre informacion para ser utilizada exclusivamente durante las etapas de contactabilidad, agendamiento, adjudicacion, prestacion, devolucion del vehiculo y cierre del servicio incluyendo la validacion del proceso de garantias; informacion que corresponde a datos personales como son nombres, apellidos, numero de identificacion,  telefono (s), direccion (es), correo(s) electronico(s), ciudad, pais, informacion de tarjeta(s) de credito y cuenta(s) bancaria(s) (cuando apliquen) entre otros.

					Ante cualquier inquietud puede escribirnos al correo electronico sac@aoacolombia.com. Nuestro horario de atencion es de lunes a viernes de 8:00 a.m. a 5:00 p.m. y sabados de 9:00a.m. a 12:00 p.m.


					Cordialmente

					Departamento de Call Center
					Direccion Nacional de Servicio al Cliente - AOA Colombia
					www.aoacolombia.com
					Nota: Este correo es de caracter informativo, por favor no responder a esta direccion de correo, ya que no se encuentra habilitada para recibir mensajes.
					";*/
			} else {
				$tipoCorreo = 2;
				$tipo_de_prueba = "renta sin domicilio";

				/*$Correo="
					Respetado(a) Se&ntilde;or(a) $conductor

					Reciba cordial saludo.

					Bienvenido(a) al sistema de adjudicacion del Servicio de Renta de AOA Colombia.

					Este es un correo automatico del Area de Call Center cuyo objetivo es confirmar  la cita programada para el proximo ".fecha_completa($fecha)." a las $Nhora en las instalaciones de AOA Colombia, Sucursal:  $OF->nombre, ubicada en la  $OF->direccion en $OF->barrio con el fin de realizar entrega de un Veh&iacute;culo Rentado,  solicitado por $conductor.

					Es importante que tenga en cuenta los requisitos previamente informados:

					- Documento de Identificacion original en el momento de la entrega de vehiculo.
					- Licencia de Conduccion original debidamente registrada.
					- Haber leido y aceptado los terminos y condiciones del servicio.
					 -$Aseguradora->info_per

					Importante: agradecemos tener en cuenta las siguientes recomendaciones:

					* Recuerde que despues de cumplido el tiempo del servicio, usted debe devolver el vehiculo en las mismas condiciones en que le fue entregado, incluyendo el combustible.
					* La hora pactada para la devolucion corresponde a la hora en la cual se suministro el vehiculo o en su defecto la establecida por la compa�ia ajustandose a los horarios de pico y placa establecidos en la ciudad (en caso de aplicar). Una vez cumplido el tiempo de gracia (3 horas) previamente informado para la devolucion, se cobrara un dia adicional. Si desea prorrogar el servicio por mas dias debera informarlo con 12 horas de anterioridad.
					*(Para cliente natural) La Garantia constituida mediante voucher sera	devuelta a traves de la direccion de correo electronico suministrada en un termino aproximado de 10 dias despues de finalizado el servicio con el fin verificar pagos pendientes. No obstante es importante tener en cuenta que en caso de no recibirlo por algun motivo concerniente a fallas tecnicas, errores de conectividad, entre otros, usted podra acercarse durante los primeros 60 dias de transcurrido el servicio a la oficina en la cual constituyo dicha garantia y reclamar el original. Pasado este tiempo, debera solicitarlo con antelacion para realizar el proceso de alistamiento y entrega del mismo desde nuestra central de archivo.

					Nota: respecto al suministro de sus datos personales (informacion no sensible)

					Es importante que tenga en cuenta que para acceder al servicio de renta corto plazo, usted puede haber suministrado y/o suministrar  de manera voluntaria y libre informacion para ser utilizada exclusivamente durante las etapas de contactabilidad, agendamiento, adjudicacion, prestacion, devolucion del vehiculo y cierre del servicio incluyendo la validacion del proceso de garantias; informacion que corresponde a datos personales como son nombres, apellidos, numero de identificacion,  telefono (s), direccion (es), correo(s) electronico(s), ciudad, pais, informacion de tarjeta(s) de credito y cuenta(s) bancaria(s) (cuando apliquen) entre otros.

					Ante cualquier inquietud puede escribirnos al correo electronico sac@aoacolombia.com. Nuestro horario de atencion es de lunes a viernes de 8:00 a.m. a 5:00 p.m. y s�bados de 9:00a.m. a 12:00 p.m.


					Cordialmente

					Departamento de Call Center
					Direccion Nacional de Servicio al Cliente - AOA Colombia
					www.aoacolombia.com
					Nota: Este correo es de caracter informativo, por favor no responder a esta direccion de correo, ya que no se encuentra habilitada para recibir mensajes.
					";*/
			}
		} else {
			if ($dir_domicilio) {
				$tipoCorreo = 3;
				$tipo_de_prueba = "dir Domicilio no renta";
				/*$Correo="
					Respetado(a) Senor(a) $conductor

					Reciba cordial saludo.

					Bienvenido(a) al sistema de adjudicacion del Servicio de vehiculo Sustituto de AOA Colombia.

					Este es un correo automatico del Area de Call Center cuyo objetivo es confirmar  la cita programada para el proximo ".fecha_completa($fecha)." a las $Nhora a fin de hacer entrega de un Vehiculo Sustituto en la direccion: $dir_domicilio Telefono: $tel_domicilio,  derivado de la reclamacion que presento ante  $Aseguradora->razon_social referente al vehiculo de placas $S->placa y cuyo numero de siniestro es $S->numero .

					Es importante que tenga en cuenta los requisitos previamente informados:

					- Documento de Identificacion original en el momento de la entrega de vehiculo.
					- Licencia de Conduccion original debidamente registrada (ante el Runt o Ministerio de Transporte).
					- Tarjeta de credito con el cupo disponible previamente informado por el consultor y el cual corresponde a una suma de:  $Aseguradora->garantia pesos para constituir la garantia, requisito indispensable para la prestacion del servicio. En caso de no poseer una tarjeta de credito propia o respaldada por una entidad bancaria cuya franquicia corresponda a las aceptadas y previamente informadas por el consultor que le atendio telefonicamente, usted puede solicitar a un tercero que le sea garante mediante tarjeta de credito para constituir la garantia. En este caso el Tarjetahabiente debe acompanarlo(a) a la cita de entrega del vehiculo para realizara congelamiento de cupo de la garantia, asi como los demas documentos que soportaran la cobertura mediante dicha tarjeta.
					- Numero de una cuenta bancaria, nombre de cuenta habiente, tipo de cuenta bancaria (Ahorros o Corriente) y el nombre de la entidad bancaria. Esta cuenta sera utilizada exclusivamente en caso de verse afectada la garantia constituida mediante congelamiento por un valor inferior a la misma, realizaremos la devolucion del excedente en el numero de cuenta por usted suministrado. (Este requisito no aplica para la garantia No Reembolsable). Existan cobros inferiores a la garant&iacute;a y se requiera devolver algun excedente.
					- Si usted es un tercero autorizado (a), debe presentar una carta diligenciada por el asegurado(a) indicando que usted est&aacute; autorizado(a) para proceder con la gestion del servicio (no requiere autenticacion) y adjunto a esta debe presentar una fotocopia de la Cedula de Ciudadan�a del (la) asegurado(a) o fotocopia de Camara y Comercio (no mayor a 30 dias) de la Entidad asegurada.
					- Haber leido y aceptado los terminos y condiciones del servicio.
					 -$Aseguradora->info_per

					Importante: agradecemos tener en cuenta las siguientes recomendaciones:

					*Asistir puntual a la hora de la cita pactada. Una vez cumplido el tiempo acordado sera  cancelada automaticamente por nuestra plataforma y se debera volver a reagendar una nueva cita, la cual sera asignada de acuerdo con la prioridad de entrega de vehiculos por usuario.
					*Recuerde que despues de cumplido el tiempo del servicio, usted debe devolver el vehiculo en las mismas condiciones en que le fue entregado, incluyendo el lavado y tanqueado.
					*La hora pactada para la devolucion corresponde a la hora en la cual se suministro el vehiculo o en su defecto la establecida por la compania ajustandose a los horarios de pico y placa establecidos en la ciudad (en caso de aplicar). Una vez cumplido el tiempo de gracia previamente informado para la devolucion, se incurrira en penalidad sin excepcion alguna. Recuerde que para su mayor comodidad y evitar incurrir en dicha penalidad, puede retornar el vehiculo a traves de un tercero por usted autorizado. Hora pactada devolucion: $Nhora.
					*La Garantia constituida mediante congelamiento sera devuelta a traves de la direccion de correo electronico suministrada en un termino aproximado de 10 dias despues de finalizado el servicio con el fin  verificar pagos pendientes. No obstante es importante tener en cuenta que en caso de no recibirlo por algun motivo concerniente a fallas tecnicas, errores de conectividad, entre otros, usted podra acercarse durante los primeros  60 dias de transcurrido el servicio a la oficina en la cual constituyo dicha garantia y reclamar el original. Pasado este tiempo, debera solicitarlo con antelacion para realizar el proceso de alistamiento y entrega del mismo desde nuestra central de archivo.

					Importante: La anulacion o devolucion de la garantia se realiza sin perjuicio a que ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A. acuda a otras vias legales (inclusive reporte a Centrales de Riesgo)  para el pago de saldos pendientes conforme a los Terminos y Condiciones del servicio de vehiculo sustituto.

					Nota: respecto al suministro de sus datos personales (informacion no sensible)

					Es importante que tenga en cuenta que para acceder al servicio de vehiculo sustituto, usted puede haber suministrado y/o suministrara de manera voluntaria y libre informacion para ser utilizada exclusivamente durante las etapas de contactabilidad, agendamiento, adjudicacion, prestacion, devolucion del vehiculo y cierre del servicio incluyendo la validacion del proceso de garantias; informacion que corresponde a datos personales como son nombres, apellidos, numero de identificacion, telefono (s), direccion(es), correo(s) electronico(s), ciudad, pais, informacion de tarjeta(s) de cr&eacute;dito y cuenta(s) bancaria(s) (cuando apliquen) entre otros.

					Dicha informacion tambien podra ser utilizada cuando usted realice una solicitud de asistencia tecnica y/o PQR,
					para darle tratamiento y respuesta.

					Ante cualquier inquietud puede escribirnos al correo electronico sac@aoacolombia.com. Nuestro horario de atencion es de lunes a viernes de 8:00 a.m. a 5:00 p.m. y sabados de 9:00a.m. a 12:00 p.m.


					Cordialmente

					Departamento de Call Center
					Direccion Nacional de Servicio al Cliente - AOA Colombia
					www.aoacolombia.com
					Nota: Este correo es de caracter informativo, por favor no responder a esta direccion de correo, ya que no se encuentra habilitada para recibir mensajes. Las Tildes fueron retiradas para garantizar compatibilidad de correo
					";*/
			} else {
				$tipoCorreo = 4;
				$tipo_de_prueba = "no renta";
				/*$Correo="
					Respetado(a) Senor(a) $conductor

					Reciba cordial saludo.

					Bienvenido(a) al sistema de adjudicacion del Servicio de vehiculo sustituto de AOA Colombia.

					Este es un correo automatico del Area de Call Center cuyo objetivo es confirmar  la cita programada para el proximo ".fecha_completa($fecha)." a las $Nhora  en las instalaciones de AOA Colombia, Sucursal:  $OF->nombre, ubicada en la  $OF->direccion en $OF->barrio con el fin de realizar entrega del  vehiculo sustituto derivado de la reclamacion que presento ante  $Aseguradora->razon_social referente al vehiculo de placas $S->placa y cuyo numero de siniestro es $S->numero .

					Es importante que tenga en cuenta los requisitos previamente informados:

					- Documento de Identificacion original en el momento de la entrega de vehiculo.
					- Licencia de Conduccion original debidamente registrada (ante el Runt o Ministerio de Transporte).
					- Tarjeta de credito con el cupo disponible previamente informado por el consultor y el cual corresponde a una suma de:  $Aseguradora->garantia pesos para constituir la garantia, requisito indispensable para la prestacion del servicio. En caso de no poseer una tarjeta de credito propia o respaldada por una entidad bancaria cuya franquicia corresponda a las aceptadas y previamente informadas por el consultor que le atendio telefonicamente, usted puede solicitar a un tercero que le sea garante mediante tarjeta de credito para constituir la garantia. En este caso el Tarjetahabiente debe acompanarlo(a) a la cita de entrega del vehiculo para el congelamiento de cupo de la garantia, asi como los demas documentos que soportaran la cobertura mediante dicha tarjeta.
					- Numero de una cuenta bancaria, nombre de cuenta habiente, tipo de cuenta bancaria (Ahorros o Corriente) y el nombre de la entidad bancaria. Esta cuenta sera utilizada exclusivamente en caso de verse afectada la garantia constituida mediante voucher por un valor inferior a la misma, realizaremos la devolucion del excedente en el numero de cuenta por usted suministrado. (Este requisito no aplica para la garantia No Reembolsable). Existan cobros inferiores a la garantia y se requiera devolver algun excedente.
					- Si usted es un tercero autorizado (a), debe presentar una carta diligenciada por el asegurado(a) indicando que usted esta autorizado(a) para proceder con la gestion del servicio (no requiere autenticacion) y adjunto a esta debe presentar una fotocopia de la Cedula de Ciudadania del (la) asegurado(a) o fotocopia de Camara y Comercio (no mayor a 30 dias) de la Entidad asegurada.
					- Haber leido y aceptado los terminos y condiciones del servicio.
					 -$Aseguradora->info_per

					Importante: agradecemos tener en cuenta las siguientes recomendaciones:

					*Asistir puntual a la hora de la cita pactada. Una vez cumplido el tiempo acordado sera  cancelada automaticamente por nuestra plataforma y se debera volver a reagendar una nueva cita, la cual sera asignada de acuerdo con la prioridad de entrega de vehiculos por usuario.
					*Recuerde que despues de cumplido el tiempo del servicio, usted debe devolver el vehiculo en las mismas condiciones en que le fue entregado, incluyendo el lavado y tanqueado.
					*La hora pactada para la devolucion corresponde a la hora en la cual se suministro el vehiculo o en su defecto la establecida por la compania ajustandose a los horarios de pico y placa establecidos en la ciudad (en caso de aplicar). Una vez cumplido el tiempo de gracia previamente informado para la devolucion, se incurrira en penalidad sin excepcion alguna. Recuerde que para su mayor comodidad y evitar incurrir en dicha penalidad, puede retornar el vehiculo a traves de un tercero por usted autorizado. Hora pactada devolucion: $Nhora.
					*La Garantia constituida mediante voucher sera devuelta a traves de la direccion de correo electronico suministrada en un termino aproximado de 10 dias despues de finalizado el servicio con el fin  verificar pagos pendientes. No obstante es importante tener en cuenta que en caso de no recibirlo por algun motivo concerniente a fallas tecnicas, errores de conectividad, entre otros, usted podra acercarse durante los primeros  60 dias de transcurrido el servicio a la oficina en la cual constituyo dicha garantia y reclamar el original. Pasado este tiempo, debera solicitarlo con antelacion para realizar el proceso de alistamiento y entrega del mismo desde nuestra central de archivo.

					Importante: La anulacion o devolucion de la garantia se realiza sin perjuicio a que ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A. acuda a otras vias legales (inclusive reporte a Centrales de Riesgo)  para el pago de saldos pendientes conforme a los Terminos y Condiciones del servicio de vehiculo sustituto.

					Nota: respecto al suministro de sus datos personales (informacion no sensible)

					Es importante que tenga en cuenta que para acceder al servicio de vehiculo sustituto, usted puede haber suministrado y/o suministrara de manera voluntaria y libre informacion para ser utilizada exclusivamente durante las etapas de contactabilidad, agendamiento, adjudicacion, prestacion, devolucion del vehiculo y cierre del servicio incluyendo la validacion del proceso de garantias; informacion que corresponde a datos personales como son nombres, apellidos, numero de identificacion, telefono (s), direccion(es), correo(s) electronico(s), ciudad, pais, informacion de tarjeta(s) de credito y cuenta(s) bancaria(s) (cuando apliquen) entre otros.

					Dicha informacion tambien podra ser utilizada cuando usted realice una solicitud de asistencia tecnica y/o PQR, para darle tratamiento y respuesta.

					Ante cualquier inquietud puede escribirnos al correo electronico sac@aoacolombia.com. Nuestro horario de atencion es de lunes a viernes de 8:00 a.m. a 5:00 p.m. y sabados de 9:00a.m. a 12:00 p.m.


					Cordialmente

					Departamento de Call Center
					Direccion Nacional de Servicio al Cliente - AOA Colombia
					www.aoacolombia.com
					Nota: Este correo es de caracter informativo, por favor no responder a esta direccion de correo, ya que no se encuentra habilitada para recibir mensajes. Las Tildes fueron retiradas para garantizar compatibilidad de correo
					";*/
			}
		}


		//include("CorreosHtml/citaProgramada.php");

		ob_start();
		include("CorreosHtml/citaProgramada.php");
		$buffer = ob_get_clean();



		echo $tipo_de_prueba;

		$envio = enviar_gmail(
			'sistemas@aoacolombia.com',
			'Sergio Castillo Castro',
			"sergiourbina765@gmail.com",
			'',
			'Objeto del mensaje',
			utf8_decode($buffer),
			'',
			'sistemas@aoacolombia.com',
			'jl6316!'
		);


		print_r($envio);
	} else {
		echo "in else";
	}
}


function watchHtmlEmail()
{

	echo "in watch email";

	$id = $_REQUEST["id"];

	$sql = "select * from siniestro where id=$id";

	//echo $sql;

	$S = qo($sql);

	//print_r($S);

	$sql = "select * from cita_servicio where siniestro='$S->id' order by id desc limit 1 ";

	//echo $sql;

	$Citaprevia = qo($sql);


	$conductor = $Citaprevia->conductor;
	$observaciones = $Citaprevia->observaciones;
	$dir_domicilio = $Citaprevia->dir_domicilio;
	$tel_domicilio = $Citaprevia->tel_domicilio;
	$fecha = $Citaprevia->fecha;
	$OF = qo("select * from oficina where id = " . $Citaprevia->oficina);


	//echo $dir_domicilio;

	if ($S->renta) {
		if ($dir_domicilio) {
			$tipoCorreo = 1;
		} else {
			$tipoCorreo = 2;
		}
	} else {
		if ($dir_domicilio) {
			$tipoCorreo = 3;
		} else {
			$tipoCorreo = 4;
		}
	}


	ob_start();

	include("CorreosHtml/citaProgramada.php");

	$buffer = ob_get_clean();

	echo utf8_decode($buffer);
}


function asigna_cita_ok()
{
	global $oficina, $siniestro, $flota, $placa, $fecha, $hora, $conductor, $observaciones, $NUSUARIO, $dir_domicilio, $dir_domiciliod, $tel_domicilio, $tel_domiciliod, $tipogarantia;
	global $IDUSUARIO, $NUSUARIO;
	// VALIDACION PARA EVITAR QUE UN VEHICULO NO SEA ASIGNADO A DOS SERVICIOS EN EL MISMO LAPSO DE TIEMPO
	// se busca en la tabla de citas alguna que est&aacute; en estado programada
	$sql = "select c.id,s.numero
				from cita_servicio c,siniestro s
				where c.siniestro=s.id and (c.placa='$placa' and c.estado='P' and '$fecha' between c.fecha and adddate(c.fecha,c.dias_servicio) ) or
								(c.placa='$paca'  and c.estadod='P' and '$fecha' <= adddate(c.fecha,c.dias_servicio))";

	if ($Cita_Previa = qo($sql)) {
		echo "<body><script language='javascript'>alert('Ya existe una cita que cruza su tiempo con la fecha propuesta de esta adjudicacion: $Cita_Previa->numero de fecha: $Cita_Previa->fecha');</script></body>";
		die();
	}
	include('inc/link.php');
	$OF = qom("select * from oficina where id=$oficina", $LINK);
	$Nhora = date('h:i A', strtotime($fecha . ' ' . $hora));
	$Hoy = date('Y-m-d H:i');
	$Dia = ndiasemana(date('w', strtotime($fecha)));
	$Nciudad = qo1m("select t_ciudad(ciudad) from siniestro where id=$siniestro", $LINK);
	$Ndia = ndiasemana(date('w', strtotime($fecha))) . ' ' . date('d', strtotime($fecha)) . ' de ' . mes(date('m', strtotime($fecha))) . ' de ' . date('Y', strtotime($fecha));
	mysql_query("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Hoy] Agenda cita para $Dia  $fecha a la(s) $hora en la ciudad de $Nciudad \"),estado=3,tipogarantia='$tipogarantia'
			  where id=$siniestro", $LINK);

	if ($tipogarantia == '1') $tg = 'No reembolsable';
	elseif ($tipogarantia == 2) $tg = 'Reembolsable';
	else $tg = 'No definida';
	if ($tipogarantia == 3) {
		$tg = 'SIN GARANTIA';
	}
	$H1 = date('Y-m-d');
	$H2 = date('H:i:s');
	mysql_query("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($siniestro,'$H1','$H2','$NUSUARIO','Agenda cita para $Dia $fecha a la(s) $hora con el veh&iacute;culo $placa en la ciudad de $Nciudad Tipo de Garantia: $tg " .
		($dir_domicilio ? " Domicilio: $dir_domicilio Tel: $tel_domicilio." : "") . "',5)", $LINK);
	//$Idn=mysql_insert_id($LINK);
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona registro',$LINK);
	$S = qom("select * from siniestro where id=$siniestro", $LINK);

	$Aseguradora = qom("select * from aseguradora where id=$S->aseguradora", $LINK);
	if ($_SESSION['Adjudicacion_READJUDICAR']) {
		if (!mysql_query("update cita_servicio set placa='$placa',hora='$hora',fecha='$fecha' ,oficina='$oficina',conductor='$conductor',
							observaciones=\"$observaciones\n$NUSUARIO [$Hoy]: Readjudica la cita.\" ,
							dir_domicilio='$dir_domicilio',tel_domicilio='$tel_domicilio',dir_domiciliod= '$dir_domiciliod',tel_domiciliod='$tel_domiciliod',conductor='$conductor'
							where siniestro='$siniestro' and estado='P' ", $LINK)) die(mysql_error($LINK));
	} else {

		mysql_query("insert into cita_servicio (oficina,siniestro,flota,placa,fecha,hora,conductor,observaciones,agendada_por,fecha_agenda,estado,dir_domicilio,tel_domicilio,dias_servicio,dir_domiciliod,tel_domiciliod)
			values ('$oficina','$siniestro','$flota','$placa','$fecha','$hora','$conductor','$observaciones','$NUSUARIO','" . date('Y-m-d H:i:s') . "','P','$dir_domicilio','$tel_domicilio','$S->dias_servicio','$dir_domiciliod','$tel_domiciliod')", $LINK);
		$Idn = mysql_insert_id($LINK);
		graba_bitacora('cita_servicio', 'A', $Idn, 'Adiciona registro.', $LINK);


		$Fec_entrega = date('Y-m-d', strtotime(aumentadias($fecha, $S->dias_servicio))) . ' ' . $hora; // calcula la fecha de devolucion
		$myDate = strtotime($Fec_entrega);

		$arrayValidation =  validationDays($Fec_entrega, $Idn, $OF);

		if ($arrayValidation['date'] == date('Y-m-d', $myDate)) {

			$dateDelivery = date('Y-m-d', $myDate);
			$hour = date('h:i:s', $myDate);
		} else {

			$dateDelivery = $arrayValidation['date'];
			$hour = $arrayValidation['hora'];
			$dateDelivery2 = date('Y-m-d', $myDate);

			$fecha1 = new DateTime($dateDelivery2);
			$fecha2 = new DateTime($dateDelivery);

			$diff = $fecha1->diff($fecha2);


			$calculationDays = ($S->dias_servicio + $diff->days);

			$observations = "Se a&ntilde;aden $diff->days dias por horarios de la oficina, fecha anterior de devolucion: $Fec_entrega, fecha nueva: $dateDelivery $hour!";

			$sqlInsert = "INSERT INTO temp_date_appointment 
		             (dias_servicio,observaciones,hora,aplazar_entrega,id_cita)
					  VALUES ($calculationDays,'$observations','$hour',1,$Idn)";


			$sqlDateAppointment = "select * from temp_date_appointment where id_cita = $Idn";

			$verificarTemp = qo($sqlDateAppointment);

			if (!$verificarTemp->observaciones) {
				q($sqlInsert);
				$newDateAppointment = "select * from temp_date_appointment where id_cita = $Idn";
				$requestDate =  qo($newDateAppointment);


				//Update a la cita
				$CitaNew =  qo("select * from cita_servicio where id = $Idn");
				$comentariosNuevos = $requestDate->observaciones . " " . $CitaNew->observaciones;

				q("UPDATE cita_servicio SET observaciones = '$comentariosNuevos', 
			aplazar_entrega = 1, dias_servicio = $requestDate->dias_servicio WHERE id = $Idn");
			}
		}





		///  BUSQUEDA SI YA TIENE UN ARRIBO DE ASEGURADO EL MISMO DIA
		if ($Arribo = qo1m("select arribo from cita_servicio where siniestro=$siniestro and date_format(arribo,'%Y-%m-%d')='$fecha' and estado='W' ", $LINK))
			mysql_query("update cita_servicio set arribo='$Arribo' where id=$Idn", $LINK);
		mysql_query("update call2proceso set fecha_cierre='$Hoy',estado='C' where agente='$IDUSUARIO' and siniestro='$siniestro' and estado='A' ");
		// Verificacon de existencia de la cola 2
		$Ahora = date('Y-m-d H:i:s');
		if ($idcola2 = qom("select * from call2cola2 where siniestro=$siniestro", $LINK)) {
			mysql_query("update call2cola2 set estado='2' where siniestro=$siniestro", $LINK);
			$Idcc = qo1m("select id from call2cola2 where siniestro=$siniestro", $LINK);
			graba_bitacora('call2cola2', 'M', $Idcc, 'cambia estado a 2', $LINK);
		} else {
			mysql_query("insert into call2cola2 (siniestro,fecha,codigo,aceptado,fecha_aceptacion,estado) values ('$siniestro','$Ahora','900174552','1','$Ahora','2')", $LINK);
			$Idcc = mysql_insert_id($LINK);
			graba_bitacora('call2cola2', 'A', $Idcc, 'Inserta con aceptacion de t&eacute;rminos y condiciones', $LINK);
		}
	}
	if ($_SESSION['Adjudicacion_OFICINA']) {
		$_SESSION['Adjudicacion_OFICINA'] = false;
		$_SESSION['Adjudicacion_ASEGURADORA'] = false;
		$_SESSION['Adjudicacion_SINIESTRO'] = false;
		$_SESSION['Adjudicacion_NIVEL'] = false;
	}
	mysql_close($LINK);
	if (!$_SESSION['Adjudicacion_READJUDICAR']) {
		$Email_usuario = usuario('email');
		if ($S->declarante_email) {
			/*if($S->asegurada == 59){
					ob_start();	
					include("CorreosHtml/CitaVentaProgramada.php");
				$buffer = ob_get_clean();
				}*/
			if ($S->renta) {
				if ($dir_domicilio) {
					if ($S->aseguradora == 59) {
						$tipoCorreo = 5;
						$Vehiculo = qo("select * from vehiculo where placa='$placa' "); // trae los datos del vehiculo
						$vehiculo2 = qo("Select linea.nombre as nom_linea, marca.nombre as nom_marca
						from  aoacol_aoacars.vehiculo as veh inner join aoacol_aoacars.linea_vehiculo as linea on veh.linea = linea.id
						inner join aoacol_aoacars.marca_vehiculo as marca on marca.id = linea.marca where placa = '$placa'  limit 1");
						$Linea = qo("select * from linea_vehiculo where id=$Vehiculo->linea");
						$claseVehiculo = qo("select concat( cl.nombre,'  ',cl.tipo) as clase from vehiculo v inner join  clase_vehiculo cl on v.clase = cl.id where v.placa = '$placa' limit 1");
					} else {
						$tipoCorreo = 1;
					}
				} else if ($S->aseguradora == 59) {
					$tipoCorreo = 6;
					$Vehiculo = qo("select * from vehiculo where placa='$placa'"); // trae los datos del vehiculo
					$vehiculo2 = qo("Select linea.nombre as nom_linea, marca.nombre as nom_marca
				    from  aoacol_aoacars.vehiculo as veh inner join aoacol_aoacars.linea_vehiculo as linea on veh.linea = linea.id
				    inner join aoacol_aoacars.marca_vehiculo as marca on marca.id = linea.marca where placa = '$placa'  limit 1");
					$Linea = qo("select * from linea_vehiculo where id=$Vehiculo->linea");
					$claseVehiculo = qo("select concat( cl.nombre,'  ',cl.tipo) as clase from vehiculo v inner join  clase_vehiculo cl on v.clase = cl.id where v.placa = '$placa' limit 1");
				} else {
					$tipoCorreo = 2;
				}

				ob_start();
				include("CorreosHtml/citaProgramada.php");
				$buffer = ob_get_clean();
			} else {
				if ($dir_domicilio) {
					if ($S->aseguradora == 59) {
						$tipoCorreo = 5;
						$Vehiculo = qo("select * from vehiculo where placa='$placa' "); // trae los datos del vehiculo
						$vehiculo2 = qo("Select linea.nombre as nom_linea, marca.nombre as nom_marca
						from  aoacol_aoacars.vehiculo as veh inner join aoacol_aoacars.linea_vehiculo as linea on veh.linea = linea.id
						inner join aoacol_aoacars.marca_vehiculo as marca on marca.id = linea.marca where placa = '$placa'  limit 1");
						$Linea = qo("select * from linea_vehiculo where id=$Vehiculo->linea");
						$claseVehiculo = qo("select concat( cl.nombre,'  ',cl.tipo) as clase from vehiculo v inner join  clase_vehiculo cl on v.clase = cl.id where v.placa = '$placa' limit 1");
					} else {
						$tipoCorreo = 3;
					}
				} else if ($S->aseguradora == 59) {
					$tipoCorreo = 6;
					$Vehiculo = qo("select * from vehiculo where placa='$placa'"); // trae los datos del vehiculo
					$vehiculo2 = qo("Select linea.nombre as nom_linea, marca.nombre as nom_marca
				    from  aoacol_aoacars.vehiculo as veh inner join aoacol_aoacars.linea_vehiculo as linea on veh.linea = linea.id
				    inner join aoacol_aoacars.marca_vehiculo as marca on marca.id = linea.marca where placa = '$placa'  limit 1");
					$Linea = qo("select * from linea_vehiculo where id=$Vehiculo->linea");
					$claseVehiculo = qo("select concat( cl.nombre,'  ',cl.tipo) as clase from vehiculo v inner join  clase_vehiculo cl on v.clase = cl.id where v.placa = '$placa' limit 1");
				} else {
					$tipoCorreo = 4;
				}

				ob_start();
				include("CorreosHtml/citaProgramada.php");
				$buffer = ob_get_clean();
			}

			if ($S->aseguradora == 59) {
				$copia1 = "tramites@aoacolombia.com";
			}

			echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
		  <script>
		  alert('Envio exitoso a: $Email_usuario');
             $.ajax({
                        url: 'https://sac.aoacolombia.com/ServiEmail.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							tipoCorreo:'$tipoCorreo',
							enviarEmail:'true ',
							APIKEYAOAAPP:'yNPlsmOGgZoGmH$129',
							id:'$S->id ',
							conductor:'$conductor',
							numero:'$S->numero',
							fecha:'$fecha',
							Nhora:'$Nhora',
							dir_domicilio:'$dir_domicilio',
							tel_domicilio:'$tel_domicilio',
							OFdireccion:'$OF->direccion',
							OFnombre:'$OF->nombre',
							Splaca :'$S->placa',
							copia1: '$copia1',
							vehiculo2nom_marca:'$vehiculo2->nom_marca',
							vehiculo2nom_linea :'$vehiculo2->nom_linea',
							Vehiculoplaca : '$Vehiculo->placa',
							Lineanombre:'$Linea->nombre',
							Vehiculocolor:' $Vehiculo->color ' ,
							Vehiculocilindraje:' $Vehiculo->cilindraje',
							claseVehiculoclase :'$claseVehiculo->clase',
							Aseguradoragarantia:'$Aseguradora->garantia',
							text:'$Aseguradora->info_pre',
						    para:'$S->declarante_email',
							contenido:'11',
							copia:'$Email_usuario',
							Aseguradorainfo_per:'$Aseguradorainfo_per',
							asunto:'AOA COLOMBIA S.A.S - Cita programada para  $Aseguradora->nombre_servicio'
							},	
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
        </script>
		
				</body> ";
		}
	}
	if ($_SESSION['Adjudicacion_READJUDICAR']) {
		$_SESSION['Adjudicacion_READJUDICAR'] = false;
		echo "parent.cerrar_re_asignacion();</script></body>";
	} else {
		guarda_escalafon(6);
		echo "<body><script>
		alert('Email enviado a $S->declarante_email');
		</script></body>";
	}
}

function mostrar_citaservicio()
{
	global $id;
	$idsin = qo1("select siniestro from cita_servicio where id=$id");
	html();
	echo "<body><script language='javascript'>centrar();window.open('zsiniestro.php?Acc=buscar_siniestro&id=$idsin','_self');</script></body>";
}

function reenviar_correo_adjudicacion()
{
	global $id;
	global $IDUSUARIO, $NUSUARIO;
	$Cita = qo("select * from cita_servicio where id=$id");
	include('inc/link.php');
	$OF = qom("select * from oficina where id=$Cita->oficina", $LINK);
	$Nhora = date('h:i A', strtotime($Cita->fecha . ' ' . $Cita->hora));
	$Hoy = date('Y-m-d H:i');
	$Dia = ndiasemana(date('w', strtotime($Cita->fecha)));
	$Nciudad = qo1m("select t_ciudad(ciudad) from siniestro where id=$Cita->siniestro", $LINK);
	$Ndia = ndiasemana(date('w', strtotime($Cita->fecha))) . ' ' . date('d', strtotime($Cita->fecha)) . ' de ' . mes(date('m', strtotime($Cita->fecha))) . ' de ' . date('Y', strtotime($Cita->fecha));
	$S = qom("select * from siniestro where id=$Cita->siniestro", $LINK);
	$Aseguradora = qom("select * from aseguradora where id=$S->aseguradora", $LINK);
	///  BUSQUEDA SI YA TIENE UN ARRIBO DE ASEGURADO EL MISMO DIA
	mysql_close($LINK);
	$Email_usuario = usuario('email');
	if ($S->declarante_email) {

		echo "
						<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
		  <script>
            $.ajax(
                    {
                        url: 'https://sac.aoacolombia.com/ServiEmail.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							enviarEmail:'true ',
							APIKEYAOAAPP:'yNPlsmOGgZoGmH$129',
						    para:'$S->declarante_email',
							contenido:'10',
							copia:'',
							asunto:'AOA COLOMBIA S.A.S - Cita programada para  $Aseguradora->nombre_servicio',
							Cita:'$Cita->dir_domicilio',
							fecha:'fecha_completa($fecha)',
							Nhora :'$Nhora',
							dir_domicilio :'$dir_domicilio',
							tel_domicilio:'$tel_domicilio',
							Aseguradora :'$Aseguradora->razon_social',
							placa:'$S->placa',
							numero:'$S->numero',
						    garantia:' $Aseguradora->garantia',
							conductor:'$conductor',
							 OFnombre ='$OF->nombre',
							 OFdireccion='$OF->direccion',
							 OFdireccion ='$OF->direccion',
							info_per:'$Aseguradora->info_per'
							},
							
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
        </script>";
	}

	echo " <body><script>alert('Email enviado a $S->declarante_email');
	</script></body>";
}

function escalar_caso()
{
	global $id, $IDUSUARIO, $NUSUARIO;
	html();
	echo "<script language='javascript'>
		function validar_escalar()
		{
			with(document.forma)
			{
				if(!alltrim(observaciones.value)) {alert('Debe digitar las observaciones');observaciones.style.backgroundColor='ffffaa';observaciones.focus();return false;}
				if(confirm('Est&aacute; seguro de escalar el caso?')) submit();
			}
		}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'><h4>ESCALAMIENTO DE CASO</h4>
		<form action='zcallcenter2.php' method='post' target='_self' name='forma' id='forma'>
			<br>Observaciones: <input type='text' name='observaciones' id='observaciones' size='80'><br>
			<br><input type='hidden' name='Acc' value='escalar_caso_ok'>
			<input type='hidden' name='id' value='$id'>
			<input type='button' style='font-size:16px;font-weight:bold' value=' CONTINUAR ' onclick='validar_escalar();'>
		</form>
	</body>";
}

function escalar_caso_ok()
{
	global $id, $IDUSUARIO, $NUSUARIO, $observaciones;
	$Ahora = date('Y-m-d H:i:s');
	$Luego = date('Y-m-d') . ' ' . aumentaminutos(date('H:i:s'), $minutos);
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo = 19; /*19: Escalamiento de caso a un nivel superior */
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','Se escala el caso a un nivel superior.',$Codigo)");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: ESCALAMIENTO DE CASO: $observaciones. \") where id=$id");
	q("insert into call2cola3 (siniestro,fecha) values ($id,'$Ahora')");
	guarda_escalafon(7);
	q("update call2proceso set estado='C',fecha_cierre='$Ahora' where agente='$IDUSUARIO' and siniestro=$id and estado='A' ");
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function escalar_caso3()
{
	global $id, $IDUSUARIO, $NUSUARIO;
	html();
	echo "<script language='javascript'>
		function validar_escalar()
		{
			with(document.forma)
			{
				if(!alltrim(observaciones.value)) {alert('Debe digitar las observaciones');observaciones.style.backgroundColor='ffffaa';observaciones.focus();return false;}
				if(confirm('Est&aacute; seguro de escalar el caso?')) submit();
			}
		}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'><h4>ESCALAMIENTO DE CASO</h4>
		<form action='zcallcenter2.php' method='post' target='_self' name='forma' id='forma'>
			<br>Observaciones: <input type='text' name='observaciones' id='observaciones' size='80'><br>
			<br><input type='hidden' name='Acc' value='escalar_caso3_ok'>
			<input type='hidden' name='id' value='$id'>
			<input type='button' style='font-size:16px;font-weight:bold' value=' CONTINUAR ' onclick='validar_escalar();'>
		</form>
	</body>";
}

function escalar_caso3_ok()
{
	global $id, $IDUSUARIO, $NUSUARIO, $observaciones;
	$Ahora = date('Y-m-d H:i:s');
	$Luego = date('Y-m-d') . ' ' . aumentaminutos(date('H:i:s'), $minutos);
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo = 19; /*19: Escalamiento de caso a un nivel superior */
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','Se escala el caso a un nivel superior.',$Codigo)");
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: ESCALAMIENTO DE CASO: $observaciones. \") where id=$id");
	q("insert into call2cola4 (siniestro,fecha) values ($id,'$Ahora')");
	guarda_escalafon(7);
	q("update call2proceso set estado='C',fecha_cierre='$Ahora' where agente='$IDUSUARIO' and siniestro=$id and estado='A' ");
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function caso_actualizar_datos()
{
	global $id, $declarante_celular;

	html('ACTUALIZACION DE DATOS');
	$Sin = qo("Select * from siniestro where id=$id");
	echo "<script language='javascript'>
	function validar_actualizacion()
	{
		with(document.forma)
		{
			//if(!alltrim(correo.value)) { alert('Debe digitar el correo electr&oacute;nico del asegurado');correo.style.backgroundColor='ffff99';correo.focus();return false;}
			if(!alltrim(actualizacion_aseg.value)) { alert('Debe digitar la informacion correspondiente a la actualizacion de datos de contacto');actualizacion_aseg.style.backgroundColor='ffff99';actualizacion_aseg.focus();return false;}
			submit();
		}
	}
	function finalizar(){	opener.re_comenzar();window.close();void(null);}
	function re_comenzar(){ finalizar();}
	</script><body><script language='javascript'>centrar(600,300);</script>
	<form action='zcallcenter2.php' target='Oculto_actualizacion' method='POST' name='forma' id='forma'>
		<h3>Actualizacion de datos de contacto:</h3>
		Datos de contacto: <br>
		Tel&eacute;fono Celular de contacto: <input type='text' name='telefono_contacto' id='telefono_contacto' size='10' maxlength='10'> A este n&uacute;mero se enviar&aacute; el mensaje de Texto (SMS) automatico para recordar las citas de entrega y devolucion.<br>

		<textarea name='actualizacion_aseg' id='actualizacion_aseg' cols=80 rows=5></textarea><br>
		Correo electr&oacute;nico: <input type='text' name='correo' id='correo' value='$Sin->declarante_email' size='60' maxlength='200'><br>
		<input type='button' name='continuar' id='continuar' value=' ACTUALIZAR INFORMACION ' onclick='validar_actualizacion()'>
		<input type='hidden' name='Acc' value='caso_actualizar_datos_ok'><input type='hidden' name='id' value='$id'>
	</form>
	<iframe name='Oculto_actualizacion' id='Oculto_actualizacion' style='visibility:hidden' width='1' height='1'></iframe>
	</body>";
}

function caso_actualizar_datos_ok()
{
	global $id, $correo, $actualizacion_aseg, $NUSUARIO, $telefono_contacto, $declarante_celular;


	$Ahora = date('Y-m-d H:i');
	q("update siniestro set declarante_email='$correo',info_erronea = '0',actualizacion_aseg=concat(actualizacion_aseg,\"\n$NUSUARIO [$Ahora]:  $actualizacion_aseg\") where id=$id");

	$S = qo("select * from siniestro where id=$id");
	$varTelefonoCel = $S->declarante_celular;
	$H1 = date('Y-m-d');
	$H2 = date('H:i:s');
	$Idn = q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'$H1','$H2','$NUSUARIO','Actualizacion de datos de contacto: Telefono Anterior <h2>$varTelefonoCel</h2> ',8)");
	$sql = "delete from call2infoerronea where siniestro = $id";
	q($sql);
	if ($S->declarante_celular) q("update siniestro set declarante_celular='$telefono_contacto' where id=$id ");
	elseif (!$S->declarante_tel_resid) q("update siniestro set declarante_tel_resid='$telefono_contacto' where id=$id ");
	elseif (!$S->declarante_tel_ofic) q("update siniestro set declarante_tel_ofic='$telefono_contacto' where id=$id ");
	elseif (!$S->declarante_telefono) q("update siniestro set declarante_telefono='$telefono_contacto' where id=$id ");
	elseif (!$S->declarate_tel_otro) q("update siniestro set declarate_tel_otro='$telefono_contacto' where id=$id ");
	graba_bitacora('siniestro', 'M', $id, 'Actualiza correo declarante_email y actualizacion de datos de contacto');

	//graba_bitacora('seguimiento','A',$Idn,'Adiciona registro');
	guarda_escalafon(5);
	echo "<body><script language='javascript'>parent.finalizar();</script></body>";
}

function guarda_escalafon($evento)
{
	global $IDUSUARIO;
	$Ahora = date('Y-m-d H:i:s');
	if ($Agente = qo("select nivel from usuario_callcenter where id=$IDUSUARIO")) {
		$Escalafon = qo("select * from call2tescalafon where nivel=$Agente->nivel and evento=$evento");
		if ($Escalafon && $Agente)
			q("insert into call2escalafon (agente,codigo,fecha,puntaje) values ($IDUSUARIO,$Escalafon->id,'$Ahora',$Escalafon->puntaje)");
	}
}

function pide_datos_financieros()
{
	global $ids, $USUARIO, $cerrando, $f, $v, $sinG1;

	$sinG = base64_decode($sinG1);

	html();

	$Sin = qo("select * from siniestro where id=$ids");
	$Oficina = qo("select * from oficina where ciudad='$Sin->ciudad' ");


	$Aseguradora = qo("select nombre from aseguradora where id = $Sin->aseguradora");
	$Validacion_vencimiento = date('Ym');



	$varBotones	= "<center><input type='button' id='Enviar' name='Enviar' value=' ENVIAR LA INFORMACION ' onclick='valida_envio()' style='font-size:16;width:300' ></center>
		<input type='hidden' name='id' id='id' value='$ids'>
		<input type='hidden' name='Acc' id='Acc' value=''>
		</form>
		<iframe name='Oculto_forma' id='Oculto_forma' style='visibility:hidden' width='1' height='1'></iframe>
		</body>";
	if ($sinG == 1) {
		$javaScritFra = "<script>
		var divP = document.getElementById('fra');
		var dinN = document.getElementById('franquicia');
		divP.removeChild(dinN);
		</script>";
		$titulo = "Formulario de registro cliente";
	} else {
		$titulo = " .:. AOA COLOMBIA S.A.S .:. SERVICIO DE VEHICULO DE REEMPLAZO .:. FORMULARIO DE GARANT͍A";
		$varFranquicia = "<tr><td align='right'>Franquicia</td><td>";
		$ScriptFranquicia = "if(franquicia.value) {alert('Debe seleccionar la franquicia de la tarjeta de cr&eacute;dito'); franquicia.style.backgroundColor='ffff55';franquicia.focus(); return false;}franquicia.disabled=false;";
	}

	echo $varScript = "
	<script language='javascript'>
		function valida_envio()
		{
			with(document.forma)
			{
				if(!Number(identificacion.value)) {alert('Debe escribir correctamente el n&uacute;mero de identificacion, sin comas ni puntos');identificacion.style.backgroundColor='ffff55';identificacion.focus();return false;}
				
				if(identificacion.value <= 10){
					alert('El numero de documento no puede ser menor de 10 digitos')
					identificacion.style.backgroundColor='ffff55';identificacion.focus()
					return false
				}
				
				var veriJuridica =  document.getElementById('seleCode').value;
				
				if(veriJuridica == 1){
					if(!codigoVerificacion.value) {alert('Debe seleccionar el tipo juridico'); codigoVerificacion.style.backgroundColor='ffff55';codigoVerificacion.focus();return false;}
				}
				
				if(!tipo_id.value) {alert('Debe seleccionar el tipo de identificacion'); tipo_id.style.backgroundColor='ffff55';tipo_id.focus();return false;}
				if(!alltrim(lugar_expdoc.value)) {alert('Debe escribir lugar de expedicion de la Identificacion'); lugar_expdoc.style.backgroundColor='ffff55';lugar_expdoc.focus(); return false;}
				if(!alltrim(nombre.value)) {alert('Debe escribir el nombre del tarjetahabiente o autorizado'); nombre.style.backgroundColor='ffff55';nombre.focus(); return false;}
				if(!alltrim(apellido.value)) {alert('Debe escribir el apellido del tarjetahabiente o autorizado'); apellido.style.backgroundColor='ffff55';apellido.focus(); return false;}
				if(!sexo.value) {alert('Debe seleccionar el sexo'); sexo.style.backgroundColor='ffff55';sexo.focus();return false;}
				if(!ciudad.value) { alert('Debe seleccionar una ciudad.'); ciudad.style.backgroundColor='ffff44'; ciudad.focus(); return false; }
				if(!alltrim(direccion.value)) { alert('Debe digitar la direccion de residencia.'); direccion.style.backgroundColor='ffff44'; direccion.focus(); return false; }
				if(!alltrim(telefono_oficina.value)) { alert('Debe digitar el telefono de oficina.'); telefono_oficina.style.backgroundColor='ffff44'; telefono_oficina.focus(); return false; }
				if(!alltrim(telefono_casa.value)) { alert('Debe digitar el telefono de la vivienda.'); telefono_casa.style.backgroundColor='ffff44'; telefono_casa.focus(); return false; }
				if(!alltrim(celular.value)) { alert('Debe digitar el telefono celular.'); celular.style.backgroundColor='ffff44'; celular.focus(); return false; }
				$ScriptFranquicia
				if(confirm('Confirma el env&iacute;o de esta informacion?'))
				{Enviar.style.visibility='hidden'; Acc.value='registrar_solicitud';document.forma.Enviar.style.visibility='hidden';document.forma.submit();}
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
			if(!id) {alert('Debe digitar una identifcacion valida');   document.forma.Enviar.style.visibility='hidden';document.forma.identificacion.focus();return false; }
			else {document.forma.Enviar.style.visibility='visible';}
			window.open('zcallcenter2.php?Acc=valida_identificacion&id='+id,'Oculto_forma');
		}
		function finalizar_envio()
		{
			" . ($cerrando ? "window.close();void(null); " : "parent.parent.adjudicacion_finalizada();") . "
		}
		function validar_id()
		{
			window.open('zcallcenter2.php?Acc=valida_identificacion&id='+document.forma.identificacion.value,'Oculto_forma');
		}
		function changeFunc(){
			
			var codigoVerificacionId = document.getElementById('codigoVerificacionId');
			var selector = document.getElementById('seleCode');
			var option = selector.options[selector.selectedIndex].value;
			var trPadre = document.getElementById('trPadre');
			var autoRetencionVen = document.getElementById('autoRetencionVen');
			var autoReteIva = document.getElementById('autoReteIva');
			var autoReteRetIca = document.getElementById('autoReteRetIca');
			var regimenSimple = document.getElementById('regimenSimple');
			var regiSimpleTribuNoIva = document.getElementById('regiSimpleTribuNoIva');
			var agenteRetencionNoIva = document.getElementById('agenteRetencionNoIva');
			
			
			if(option == 1){
			trPadre.style.display = 'contents';
			AutoRetencionVentas.style.display = 'contents';
			AutoRetencionIva.style.display = 'contents';
			AutoRetencionRET.style.display = 'contents';
			regimenSimpleTribu.style.display = 'contents';
			codigoVerificacionId.value = '';
			autoRetencionVen.checked = false;
			autoReteIva.checked = false;
			autoReteRetIca.checked = false;
			regimenSimple.checked = false;
			regiSimpleTribuNoIva.style.display = 'none';
			agenteRetencionNoIva.style.display = 'none';
			}else if(option == 2){
			
			trPadre.style.display = 'none';
			AutoRetencionVentas.style.display = 'none';
			AutoRetencionIva.style.display = 'none';
			AutoRetencionRET.style.display = 'none';
			regimenSimpleTribu.style.display = 'none';
			codigoVerificacionId.value = '';
			autoRetencionVen.checked = false;
			autoReteIva.checked = false;
			autoReteRetIca.checked = false;
			regimenSimple.checked = false;
			regiSimpleTribuNoIva.style.display = 'contents';
			agenteRetencionNoIva.style.display = 'contents';
			}
		}
		
	</script>";
	echo $varHtml = "
	<body style='font-size:16px' bgcolor='ffffff'><script language='javascript'>centrar();</script>
		<iframe id='Busqueda_Ciudad' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#eeffee;z-index:200;' height='400px' width='200px' ></iframe>
		<h3 align='center'>$titulo</h3>
		<center>
		<br><span id='nuevo_cliente' style='font-size:16px;font-weight:bold;'></span>
		<br><br>
		<form action='zcallcenter2.php' target='Oculto_forma' method='POST' name='forma' id='forma'>
			<table>
				<tr><td align='right'>N&uacute;mero de Identificacion</td><td><input type='text' class='numero' name='identificacion' id='identificacion' value='' size='15' maxlength='15'>
					<input type='button' name='validar' id='validar' value=' Validar Identificacion ' onclick='validar_id();'></td></tr>
				<tr><td align='right'>Tipo de Identificacion</td><td>" . menu1('tipo_id', "select codigo,nombre from tipo_identificacion", '', 1) . "</td></tr>
				<tr><td align='right'>Lugar de Expedicion de la Identificacion</td><td><input type='text' name='lugar_expdoc' id='lugar_expdoc' value='' size='50' maxlength='50' onkeyup='javascript:this.value=this.value.toUpperCase();'></td></tr>
				<tr><td align='right'>Selecciona si es juridica o natural</td><td><select id='seleCode' name='seleCode' onchange='changeFunc();'><option>Seleccione</option><option id='juridica' value='1'>Juridica</option><option value='2'>Natural</option></select></td></tr>
				<tr id='trPadre' style='display:none'><td align='right'>Digito de verificacion</td><td><input type='text' style='width: 2%;' name='codigoVerificacion' maxlength='1' id='codigoVerificacionId' ></td></tr>
				
				<tr><tr id='AutoRetencionVentas' style='display:none'><td align='right'>Auto Retencion de ventas</td><td><input type='checkbox' value='1' name='autoRetencionVen' id='autoRetencionVen' ></td></tr></tr>
				<tr><tr id='AutoRetencionIva' style='display:none'><td align='right'>Auto Retencion de iva</td><td><input type='checkbox' value='1' name='autoReteIva' id='autoReteIva' ></td></tr></tr>
				<tr><tr id='AutoRetencionRET' style='display:none'><td align='right'>Auto Retencion de RET ICA</td><td><input type='checkbox' value='1' name='autoReteRetIca' id='autoReteRetIca' ></td></tr></tr>
				<tr><tr id='regimenSimpleTribu' style='display:none'><td align='right'>Regimen simple tributario</td><td><input type='checkbox' value='1' name='regimenSimple' id='regimenSimple' ></td></tr></tr>
				
				
				<tr><tr id='regiSimpleTribuNoIva' style='display:none'><td align='right'>Regimen simple tributario NO IVA</td><td><input type='checkbox' value='1' name='regimenSimpleNoIva' id='regimenSimpleNoIva' ></td></tr></tr>
				<tr><tr id='agenteRetencionNoIva' style='display:none'><td align='right'>Agente retencion NO IVA</td><td><input type='checkbox' value='1' name='agenteReteNoIva' id='agenteReteNoIva' ></td></tr></tr>
				
				
				
				<tr><td align='right'>Nombres</td><td><input type='text' name='nombre' id='nombre' value='' size='50' maxlength='50' onkeyup='javascript:this.value=this.value.toUpperCase();'><input type='hidden' name='sinG' value='$sinG'></td></tr>
				<tr><td align='right'>Apellidos</td><td><input type='text' name='apellido' id='apellido' value='' size='50' maxlength='50' onkeyup='javascript:this.value=this.value.toUpperCase();'></td></tr>
				<tr><td align='right'>Sexo:</td><td ><select name='sexo' id='sexo'><option value=''></option><option value='M'>Masculino</option><option value='F'>Femenino</option></select></td></tr>
				<tr><td align='right'>Pais:</td><td >" . menu1('pais', "select codigo,nombre from pais order by nombre", 'CO', 1) . "</td></tr>
				<tr><td align='right'>Ciudad:</td><td ><input type='text' style='color:#000099;background-color:#FFFFFF;' name='_ciudad' id='_ciudad' size='30' onclick=\"busqueda_ciudad2('ciudad','05001000');\" value='Click aqui' readonly><input type='hidden' name=ciudad id=ciudad value=''><span id='bc_ciudad'></span> Utilice el mouse para seleccionar la ciudad, haga click la casilla.</td></tr>
				<tr><td align='right'>Direccion Domicilio:</td><td ><input type='text' name='direccion' id='direccion' size='30' maxlength='50' onkeyup='javascript:this.value=this.value.toUpperCase();'></td></tr>
				<tr><td align='right'>Tel&eacute;fono Oficina:</td><td ><input type='text' class='numero' name='telefono_oficina' id='telefono_oficina' size='30' maxlength='50'></td></tr>
				<tr><td align='right'>Tel&eacute;fono Vivienda:</td><td ><input type='text' class='numero' name='telefono_casa' id='telefono_casa' size='30' maxlength='50'></td></tr>
				<tr><td align='right'>Celular:</td><td ><input type='text' name='celular' class='numero' id='celular' size='30' maxlength='50'></td></tr>
				<tr><td align='right'>Correo electronico:</td><td ><input type='text' name='email_e' id='email_e' size='30' maxlength='50'></td></tr>$varFranquicia";

	//$Sin->no_garantia != 0	Validacion sin garantia		
	if ($Aseguradora->nombre == "USADOS" || $Sin->no_garantia != 0 || $sinG == 1) {
		echo "<div id='fra'>";
		echo menu1("franquicia", "select f.id,f.nombre from franquisia_tarjeta f,ciudad_franq c where c.franquicia=f.id and c.oficina=$Oficina->id
					and concat(',',c.perfil,',') like '%,$USUARIO,%' and concat(',',c.aseguradora,',') like '%,$Sin->aseguradora,%' ", 10, 1, '', " onchange='activa_n();' ") . "</td>";
		echo "</div>";
		echo  $javaScritFra;
		echo $varBotones;
	} else {
		echo menu1("franquicia", "select f.id,f.nombre from franquisia_tarjeta f,ciudad_franq c where c.franquicia=f.id and c.oficina=$Oficina->id
																and concat(',',c.perfil,',') like '%,$USUARIO,%' and concat(',',c.aseguradora,',') like '%,$Sin->aseguradora,%' ", 0, 1, '', " onchange='activa_n();' ");
		echo "</td></tr>
				<tr><td align='right'>Numero de Tarjeta</td><td><input type='text' name='numero_tarjeta' class='numero' id='numero_tarjeta' value='' size='20' maxlength='20'></td></tr>
				<tr><td align='right'>Banco que expidio la tarjeta</td><td>" . menu1("banco", "select id,nombre from codigo_ach order by nombre", 0, 1) . "</td></tr>
				<tr><td align='right'>Fecha de vencimiento de la tarjeta</td><td>" . menu3("vencimiento_mes", "01,01;02,02;03,03;04,04;05,05;06,06;07,07;08,08;09,09;10,10;11,11;12,12", 0, 1, '', " onchange='valida_vencimiento()' ");

		echo " - <select name='vencimiento_ano' id='vencimiento_ano' onchange='valida_vencimiento()'><option value=''></option>";
		for ($a = date('Y'); $a < 2100; $a++) {
			echo "<option value='$a'>$a</option>";
		}
		echo "</select></td></tr>
				<tr><td align='right'>C&oacute;digo de Seguridad<br>(Tres &uacute;ltimos digitos al respaldo de la tarjeta)</td><td><input type='password' name='codigo_seguridad' id='codigo_seguridad' value='' size='4' maxlength='4'></td></tr>
			</table><br><br>
			En caso de hacer cobros con la garant&iacute;a y si hay que devolver un dinero se hace mediante transferencia electr&oacute;nica. A continuacion por favor diligencie los datos
			del cuenta habiente para estos casos.<br><br>
			<table>
				<tr><td align='right'>N&uacute;mero de Cuenta Bancaria</td><td><input type='text' class='numero' name='devol_cuenta_banco' id='devol_cuenta_banco' value='' size='20' maxlength='20'></td></tr>
				<tr><td align='right'>Tipo de Cuenta</td><td><select name='devol_tipo_cuenta' id='tipo'><option value=''></option><option value='A'>Ahoros</option><option value='C'>Corriente</option></select></td></tr>
				<tr><td align='right'>Banco</td><td>" . menu1("devol_banco", "select id,nombre from codigo_ach where codigo!='' order by nombre", 0, 1) . "</td></tr>
				<tr><td align='right'>A nombre de (Nombres y apellidos)</td><td><input type='text' name='devol_ncuenta' id='devol_ncuenta' value='' size='50' maxlength='50' onkeyup='javascript:this.value=this.value.toUpperCase();'></td></tr>
				<tr><td align='right'>Documento de Identificacion</td><td><input type='text' class='numero' name='identificacion_devol' id='identificacion_devol' value='' size='20' maxlength=15></td></tr>
			</table>";
		echo $varScript = "<script language='javascript'>
		function valida_envio()
		{
			with(document.forma)
			{
				if(!alltrim(numero_tarjeta.value)) {alert('Debe escribir el numero completo de la tarjeta de cr&eacute;dito'); numero_tarjeta.style.backgroundColor='ffff55';numero_tarjeta.focus(); return false;}
				if(!Number(numero_tarjeta.value)) {alert('Debe escribir el numero completo de la tarjeta de cr&eacute;dito'); numero_tarjeta.style.backgroundColor='ffff55';numero_tarjeta.focus(); return false;}
				if(!banco.value) {alert('Debe seleccionar el banco que expidio la tarjeta de cr&eacute;dito'); banco.style.backgroundColor='ffff55';banco.focus(); return;}
				if(!alltrim(vencimiento_mes.value)) {alert('Debe seleccionar el mes de vencimiento de la tarjeta de cr&eacute;dito'); vencimiento_mes.style.backgroundColor='ffff55';vencimiento_mes.focus(); return;}
				if(!alltrim(vencimiento_ano.value)) {alert('Debe seleccionar el a&ntilde;o de vencimiento de la tarjeta de cr&eacute;dito');vencimiento_ano.style.backgroundColor='ffff55';vencimiento_ano.focus(); return;}
				if(!alltrim(codigo_seguridad.value)) {alert('Debe digitar el c&oacute;digo de seguridad de la tarjeta de cr&eacute;dito'); codigo_seguridad.style.backgroundColor='ffff55';codigo_seguridad.focus(); return;}
				if(codigo_seguridad.value!='000') if(!Number(codigo_seguridad.value)) {alert('Debe digitar el c&oacute;digo de seguridad de la tarjeta de cr&eacute;dito'); codigo_seguridad.style.backgroundColor='ffff55';codigo_seguridad.focus(); return;}
				if(!alltrim(devol_cuenta_banco.value)) {alert('Debe digitar la cuenta bancaria para devoluciones');devol_cuenta_banco.style.backgroundColor='ffff55';devol_cuenta_banco.focus();return false;}
				if(!Number(devol_cuenta_banco.value)) {alert('Numero de cuenta bancaria para devoluciones de garant&iacute;a inv&aacute;lido'); devol_cuenta_banco.style.backgroundColor='ffff55';devol_cuenta_banco.focus();return false;}
				if(devol_cuenta_banco.value.length<8) {alert('Numero de cuenta bancaria para devoluciones de garant&iacute;a inv&aacute;lido'); devol_cuenta_banco.style.backgroundColor='ffff55';devol_cuenta_banco.focus();return false;}
				if(devol_cuenta_banco.value.indexOf('0000000')>-1) {alert('Numero de cuenta bancaria para devoluciones de garant&iacute;a inv&aacute;lido'); devol_cuenta_banco.style.backgroundColor='ffff55';devol_cuenta_banco.focus();return false;}
				if(!alltrim(devol_tipo_cuenta.value)) {alert('Debe seleccionar el tipo de cuenta bancaria para devoluciones');devol_tipo_cuenta.style.backgroundColor='ffff55';devol_tipo_cuenta.focus();return false;}
				if(!alltrim(devol_banco.value)) {alert('Debe seleccionar el banco para devoluciones');devol_banco.style.backgroundColor='ffff55';devol_banco.focus();return false;}
				if(!alltrim(devol_ncuenta.value)) {alert('Debe digitar a nombre de quien es la cuenta para devoluciones');devol_ncuenta.style.backgroundColor='ffff55';devol_ncuenta.focus();return false;}
				if(!alltrim(identificacion_devol.value)) {alert('Debe digitar  la identificacion a la que pertenece la cuenta para devoluciones');identificacion_devol.style.backgroundColor='ffff55';identificacion_devol.focus();return false;}
				if(!Number(identificacion_devol.value)) {alert('Debe digitar  la identificacion a la que pertenece la cuenta para devoluciones');identificacion_devol.style.backgroundColor='ffff55';identificacion_devol.focus();return false;}
				
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
				{alert('No se puede aceptar una tarjeta vencida. Verifique la informacion o intente con otra tarjeta.');document.forma.Enviar.disabled=true;return false;}
				else	document.forma.Enviar.disabled=false;
			}
		}
		</script>";
		echo $varBotones;
	}
}

function valida_identificacion()
{
	global $id;
	echo "<body><script language='javascript'>";
	if ($Ingreso = qo("select foto_f,nombre,apellido from aoacol_administra.ingreso_recepcion where identificacion='$id' order by id desc limit 1")) {
		echo "parent.document.forma.nombre.value='$Ingreso->nombre';
					parent.document.forma.apellido.value='$Ingreso->apellido';";
	}
	if ($C = qo("select * from cliente where identificacion='$id' ")) {
		$Nciudad = qo1("select concat(departamento,' - ',nombre) from ciudad where codigo='$C->ciudad' ");
		echo "
				with(parent.document.forma)
				{
					tipo_id.value='$C->tipo_id';
					nombre.value='$C->nombre';
					apellido.value='$C->apellido';lugar_expdoc.value='$C->lugar_expdoc';
					pais.value='$C->pais';ciudad.value='$C->ciudad';_ciudad.value='$Nciudad';direccion.value='$C->direccion';
					telefono_oficina.value='$C->telefono_oficina';telefono_casa.value='$C->telefono_casa';celular.value='$C->celular';
					sexo.value='$C->sexo';email_e.value='$C->email_e'; codigoVerificacion.value='$C->dv';
					 
					
					if('$C->dv'){
						seleCode.value = 1;
						parent.document.getElementById('trPadre').style.display = 'contents';
						parent.document.getElementById('AutoRetencionVentas').style.display = 'contents';
						parent.document.getElementById('AutoRetencionIva').style.display = 'contents';
						parent.document.getElementById('AutoRetencionRET').style.display = 'contents';
						parent.document.getElementById('regimenSimpleTribu').style.display = 'contents';
						if('$C->auto_retenedor_renta' !=0){
							autoRetencionVen.checked = true;
						}else{
							autoRetencionVen.checked = false;
						}
						if('$C->auto_retenedor_iva' !=0){
						autoReteIva.checked  = true;
						}else{
						autoReteIva.checked  = false;	
						}
						if('$C->auto_retenedor_rete_ica' !=0){
							autoReteRetIca.checked = true;
						}else{
							autoReteRetIca.checked = false;
						}
						if('$C->regimen_simple_tributacion' !=0){
							regimenSimple.checked = true;
						}else{
							regimenSimple.checked = false;
						}
					}else{
						seleCode.value = 2;
						parent.document.getElementById('regiSimpleTribuNoIva').style.display = 'contents';
						parent.document.getElementById('agenteRetencionNoIva').style.display = 'contents';
						
						if($C->regimen_simple_tri_no_iva){
							regimenSimpleNoIva.checked = true;
						}else{
							regimenSimpleNoIva.checked = false;	
						}
						if($C->agente_retencion_iva_no_iva){
							agenteReteNoIva.checked = true;
						}else{
							agenteReteNoIva.checked = false;
						}
						
						
					}
				}
				parent.document.getElementById('nuevo_cliente').innerHTML='CLIENTE EXISTENTE';
				";
		if ($B = qo("select * from sin_autor where identificacion_devol='$id' ")) {
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
	} else {
		echo "with(parent.document.forma)
					{
						tipo_id.value='';nombre.value='';apellido.value='';lugar_expdoc.value='';
						pais.value='CO';ciudad.value='';_ciudad.value='';direccion.value='';
						telefono_oficina.value='';telefono_casa.value='';celular.value='';

					}
					parent.document.getElementById('nuevo_cliente').innerHTML='CLIENTE NUEVO';";
	}
	echo "</script></body>";
}

function registrar_solicitud()
{

	global $id, $nombre, $identificacion, $numero_tarjeta, $franquicia, $banco, $vencimiento_mes, $vencimiento_ano, $codigo_seguridad, $sinG, $email_e;
	global $devol_ncuenta, $devol_cuenta_banco, $devol_tipo_cuenta, $devol_banco, $apellido, $lugar_expdoc, $tipo_id, $sexo, $ciudad, $direccion,
		$identificacion_devol, $telefono_oficina, $telefono_casa, $celular, $pais, $NUSUARIO, $IDUSUARIO, $codigoVerificacion, $autoRetencionVen, $autoReteIva, $autoReteRetIca, $regimenSimple, $regimenSimpleNoIva, $agenteReteNoIva;
	// obtencion de la informacion de los registros, $id es el id de la cola 2 donde se guarda el siniestro
	$Sin = qo("select * from siniestro where id=$id");
	$Aseguradora = qo("select * from aseguradora where id=$Sin->aseguradora");

	if ($sinG == 1) {
		q("insert ignore into cliente (identificacion) values ('$identificacion')");
		echo "update cliente set nombre='$nombre',apellido='$apellido',lugar_expdoc='$lugar_expdoc',tipo_id='$tipo_id',sexo='$sexo',pais='$pais',ciudad='$ciudad',
			direccion='$direccion',telefono_oficina='$telefono_oficina',telefono_casa='$telefono_casa',celular='$celular',email_e='$email_e',dv='$codigoVerificacion',auto_retenedor_renta='$autoRetencionVen',auto_retenedor_iva = '$autoReteIva', auto_retenedor_rete_ica = '$autoReteRetIca', 
			regimen_simple_tributacion = '$regimenSimple', regimen_simple_tri_no_iva = '$regimenSimpleNoIva', agente_retencion_iva_no_iva = '$agenteReteNoIva'
			where identificacion='$identificacion'";
		q("update cliente set nombre='$nombre',apellido='$apellido',lugar_expdoc='$lugar_expdoc',tipo_id='$tipo_id',sexo='$sexo',pais='$pais',ciudad='$ciudad',
			direccion='$direccion',telefono_oficina='$telefono_oficina',telefono_casa='$telefono_casa',celular='$celular',email_e='$email_e',dv='$codigoVerificacion',auto_retenedor_renta='$autoRetencionVen',auto_retenedor_iva = '$autoReteIva', auto_retenedor_rete_ica = '$autoReteRetIca', 
			regimen_simple_tributacion = '$regimenSimple', regimen_simple_tri_no_iva = '$regimenSimpleNoIva', agente_retencion_iva_no_iva = '$agenteReteNoIva' where identificacion='$identificacion'");
		echo "<body>
		<script>
		alert('Registro exitoso de cliente');
	     var url = 'https://app.aoacolombia.com/Control/operativo/zcitas.php';
     window.onload = function() {
    setTimeout(function() {
       window.location.href = url; 
    },3000);
 }
		 </script>
		 </body>";

		exit();
	}
	// ingresa el cliente en la tabla de clientes teniendo cuidado de su preexistencia
	q("insert ignore into cliente (identificacion) values ('$identificacion')");

	// actualiza toda la informacion del cliente
	echo "update cliente set nombre='$nombre',apellido='$apellido',lugar_expdoc='$lugar_expdoc',tipo_id='$tipo_id',sexo='$sexo',pais='$pais',ciudad='$ciudad',
			direccion='$direccion',telefono_oficina='$telefono_oficina',telefono_casa='$telefono_casa',celular='$celular',email_e='$Sin->declarante_email',dv='$codigoVerificacion' where identificacion='$identificacion'";

	q("update cliente set nombre='$nombre',apellido='$apellido',lugar_expdoc='$lugar_expdoc',tipo_id='$tipo_id',sexo='$sexo',pais='$pais',ciudad='$ciudad',
			direccion='$direccion',telefono_oficina='$telefono_oficina',telefono_casa='$telefono_casa',celular='$celular',email_e='$Sin->declarante_email',dv='$codigoVerificacion' where identificacion='$identificacion'");
	// inserta la solicitud de autorizacion
	$Ahora = date('Y-m-d H:i:s');

	if ($Aseguradora->nombre == "USADOS") {
		$Nid = q("insert into sin_autor (siniestro,nombre,identificacion,numero,franquicia,banco,vencimiento_mes,vencimiento_ano,codigo_seguridad,
		fecha_solicitud,solicitado_por,estado,valor,observaciones,email,devol_cuenta_banco,devol_tipo_cuenta,devol_banco,
		devol_ncuenta,identificacion_devol)
		values ('$id','$nombre $apellido','$identificacion','$numero_tarjeta','$franquicia','$banco','$vencimiento_mes','$vencimiento_ano','$codigo_seguridad',
		'$Ahora','$NUSUARIO','A','$Aseguradora->garantia',\"SOLICITUD DILIGENCIADA POR EL AGENTE NIVEL 2 $NUSUARIO.\",'$Sin->declarante_email','$devol_cuenta_banco','$devol_tipo_cuenta','$devol_banco',
		'$devol_ncuenta','$identificacion_devol')");
	} else {
		$Nid = q("insert into sin_autor (siniestro,nombre,identificacion,numero,franquicia,banco,vencimiento_mes,vencimiento_ano,codigo_seguridad,
		fecha_solicitud,solicitado_por,estado,valor,observaciones,email,devol_cuenta_banco,devol_tipo_cuenta,devol_banco,
		devol_ncuenta,identificacion_devol)
		values ('$id','$nombre $apellido','$identificacion','$numero_tarjeta','$franquicia','$banco','$vencimiento_mes','$vencimiento_ano','$codigo_seguridad',
		'$Ahora','$NUSUARIO','E','$Aseguradora->garantia',\"SOLICITUD DILIGENCIADA POR EL AGENTE NIVEL 2 $NUSUARIO.\",'$Sin->declarante_email','$devol_cuenta_banco','$devol_tipo_cuenta','$devol_banco',
		'$devol_ncuenta','$identificacion_devol')");
	}



	graba_bitacora('sin_autor', 'A', $Nid, 'Adiciona Registro');
	echo "<body><script language='javascript'>alert('LA INFORMACION SE REGISTRO SATISFACTORIAMENTE');
	if(parent) parent.finalizar_envio(); </script></body>";
}

function autorizar_cambio_gama()
{
	global $clavesupervisor, $flota_original, $flota_temporal, $vehiculo, $IDUSUARIO, $NUSUARIO, $justificacion;
	$Ahora = date('Y-m-d H:i:s');
	$Eclave = e($clavesupervisor);
	if ($C = qo("select * from usuario_coordcc where clave='$Eclave' ")) {
		q("insert into cambio_flota (vehiculo,flota_original,flota_temporal,autorizado_por,fecha,solicitado_por,justificacion) values
			('$vehiculo','$flota_original','$flota_temporal','$C->nombre','$Ahora','$NUSUARIO',\"$justificacion\")");
		echo "<body><script language='javascript'>parent.recargar();</script></body>";
	} else {
		echo "<body><script language='javascript'>alert('CLAVE INVALIDA');</script></body>";
	}
}
function autorizar_cambio_gama_autorizacion()
{
	global $clavesupervisor, $IDUSUARIO, $NUSUARIO, $idcambio;

	$S = qo("select * from cambio_flota_solicitud where id='$idcambio' and estado=0 ");

	$Ahora = date('Y-m-d H:i:s');
	$Eclave = e($clavesupervisor);
	//if($clavesupervisor=='321')
	if ($C = qo("select * from usuario_coordcc where clave='$Eclave' ")) {
		q("insert into cambio_flota (vehiculo,flota_original,flota_temporal,autorizado_por,fecha,solicitado_por,justificacion) values
			('$S->vehiculo','$S->flota_original','$S->flota_temporal','$C->nombre','$Ahora','$S->solicitado_por',\"$S->justificacion\")");
		q("update cambio_flota_solicitud set estado=1 where id='$idcambio' ");

		echo "<body><script language='javascript'>alert('SE REALIZO EL PROCESO CON EXITO');window.opener.location.reload();window.close();</script></body>";
	} else {
		echo "<body><script language='javascript'>alert('CLAVE INVALIDA');window.close();</script></body>";
	}
}
function autorizar_siniestro_reactivar()
{
	global $id, $clavesupervisor, $IDUSUARIO, $NUSUARIO, $idcambio;

	$S = qo("select * from solicitud_siniestro_reactivacion where id='$idcambio' and estado=0 ");

	$Ahora = date('Y-m-d H:i:s');
	$Eclave = e($clavesupervisor);
	//if($clavesupervisor=='321')
	if ($C = qo("select * from usuario_coordcc where clave='$Eclave' ")) {
		// q("update solicitud_modsin set aprobado_por='$NUSUARIO',fec_aprobacion='$Ahora' where id=$id");
		q("update solicitud_siniestro_reactivacion set autorizado_por='$NUSUARIO',fecha='$Ahora',estado=1 where id=$idcambio");
		q("update siniestro set estado=5,causal=0,subcausal=0 where id=$idcambio");
		graba_bitacora('siniestro', 'M', $id, "Reactiva el siniestro");
		q("update solicitud_siniestro_reactivacion set estado=1 where id='$idcambio' ");
		echo "<body><script language='javascript'>alert('SE REALIZO EL PROCESO CON EXITO');window.opener.location.reload();window.close();</script></body>";
	} else {
		echo "<body><script language='javascript'>alert('CLAVE INVALIDA');window.close();</script></body>";
	}
}
function anular_cambio_gama_autorizacion()
{
	global $clavesupervisor, $IDUSUARIO, $NUSUARIO, $idcambio;

	$S = qo("select * from cambio_flota_solicitud where id='$idcambio' and estado=0 ");

	$Ahora = date('Y-m-d H:i:s');
	$Eclave = e($clavesupervisor);
	//if($clavesupervisor=='321')
	if ($C = qo("select * from usuario_coordcc where clave='$Eclave' ")) {

		q("update cambio_flota_solicitud set estado=1, autorizado_por='$C->nombre' where id='$idcambio' ");

		echo "<body><script language='javascript'>alert('SE REALIZO EL PROCESO CON EXITO');window.opener.location.reload();window.close();</script></body>";
	} else {
		echo "<body><script language='javascript'>alert('CLAVE INVALIDA');window.close();</script></body>";
	}
}
function anular_reactivacion_siniestro()
{
	global $clavesupervisor, $IDUSUARIO, $NUSUARIO, $idcambio;

	$S = qo("select * from solicitud_siniestro_reactivacion where id='$idcambio' and estado=0 ");

	$Ahora = date('Y-m-d H:i:s');
	$Eclave = e($clavesupervisor);
	//if($clavesupervisor=='321')
	if ($C = qo("select * from usuario_coordcc where clave='$Eclave' ")) {

		q("update solicitud_siniestro_reactivacion set estado=1, autorizado_por='$C->nombre' where id='$idcambio' ");

		echo "<body><script language='javascript'>alert('SE REALIZO EL PROCESO CON EXITO');window.opener.location.reload();window.close();</script></body>";
	} else {
		echo "<body><script language='javascript'>alert('CLAVE INVALIDA');window.close();</script></body>";
	}
}
function solictud_reactivacion_cancelar($id)
{
	global $id, $NUSUARIO, $IDUSUARIO;
	q("update solicitud_siniestro_reactivacion set estado=1 where siniestro=$id ");
	echo "se cancelo";
}
function solictud_reactivacion($id)
{
	global $id, $NUSUARIO, $IDUSUARIO;
	$Dagente = qo("select * from usuario_callcenter where id=$IDUSUARIO");
	$Sin = qo("select * from siniestro where id=$id");
	$Ciudad = qo("select * from ciudad where codigo='$Sin->ciudad' ");
	if ($Sin->ciudad_original) $Ciudado = qo("select * from ciudad where codigo='$Sin->ciudad_original' ");
	else $Ciudado = $Ciudad;
	$Fecha = date('Y-m-d H:i');
	html('Solicitud de Modificacion');
	$Ciudades = menu1("ciudad", "select ciu.codigo,ciu.nombre as nciudad from ciudad ciu,oficina ofi where ofi.ciudad=ciu.codigo order by nciudad ", $Ciudad, 1);
	echo "<script language='javascript'>
			function carga() {centrar(500,500);}
			function activaruno() { if(document.forma.uno.checked) {document.getElementById('tduno').style.visibility='visible';document.forma.justificacion1.focus();} else document.getElementById('tduno').style.visibility='hidden'; }
			function activardos() { if(document.forma.dos.checked) document.getElementById('tddos').style.visibility='visible'; else document.getElementById('tddos').style.visibility='hidden'; }
			function enviar_solicitud()
			{
				with(document.forma)
				{
					if(uno.checked)
					{
						if(!alltrim(justificacion1.value))
						{
							alert('Debe justificar por que se desea pasar a Pendiente el estado de este siniestro');
							justificacion1.style.backgroundColor='ffffdd';
							return false;
						}
					}
					if(dos.checked)
					{
						if(!alltrim(justificacion2.value))
						{
							alert('Debe justificar por que se desea cambiar de ciudad este siniestro');
							justificacion2.style.backgroundColor='ffffdd';
							return false;
						}
						if(!ciudad.value)
						{
							alert('Debe especificar una ciudad valida');
							ciudad.style.backgroundColor='ffffdd';
							return false;
						}
					}
					if(!(uno.checked || dos.checked))
					{
						alert('La solicitud debe contener uno o los dos conceptos que son Reactivacion o Cambio de Ciudad');
						return false;
					}
				}
				document.forma.btn_enviar.style.visibility='hidden';
				document.forma.submit();
			}
		</script>
		<body onload='carga()'>";
	$Aseg = qo("select * from aseguradora where id=$Sin->aseguradora");
	$Ofic = qo("select * from oficina where ciudad=$Sin->ciudad");
	$Ciuorig = qo1("select concat(departamento,' - ',nombre) from ciudad where codigo='$Sin->ciudad_original' ");
	$Estado = qo("select * from estado_siniestro where id=$Sin->estado");
	echo "<body><h3>$NUSUARIO .:. CASO ABIERTO: $id</h3>
			<table cellspacing=4>
			<tr><td bgcolor='ffffff'> <img src='$Aseg->emblema_f' border='0' height='30px'> </td>
				<td bgcolor='ffffff'> <b style='font-size:18px'>$Aseg->nombre </b></td>
				<td bgcolor='ffffff' align='center'> <b style='font-size:16px;'>OFICINA:<br>$Ofic->nombre</b> </td>
				<td bgcolor='ffffdd' rowspan='2' align='center'> <b>PLACA:</b><br><b style='font-size:20px'>$Sin->placa</b> </td>
				<td rowspan='2' bgcolor='000000'> <img src='$Dagente->foto_f' border='5' height='100px'> </td>
			</tr>
			<tr>
				<td bgcolor='ffffaa'> DIAS: <b style='font-size:20px'>$Sin->dias_servicio</b> </td>
				<td bgcolor='ffffff'> <b style='font-size:16px'>Numero Siniestro: $Sin->numero</b><br><br><center>ESTADO:
				<b style='background-color:ddddff;font-size:14px;" . ($Sin->estado == 1 ? 'color:aa0000;' : '') . "'> $Estado->nombre </b></center></td>
				<td bgcolor='ffffff'> Ciudad Original:<br>$Ciuorig</td></tr>
			</table>

		<form action='zcallcenter2.php' method='post' target='_self' name='forma' id='forma'>
		<h3>Solicitud de Modificacion de Siniestro</h3>
		Usuario: $NUSUARIO   Fecha: $Fecha <br />
		<table border cellspacing='0' width='100%'>
			<tr><td>Reactivacion <input type='checkbox' name='uno' onchange='activaruno();' " . ($Sin->estado == 1 ? '' : "disabled") . "> El estado actual es: <b>" . qo1("select t_estado_siniestro($Sin->estado)") . "</b></td></tr>
			<tr><td  id='tduno' style='visibility:hidden;'>Esta opcion solicita pasar el estado a PENDIENTE por favor escriba la justificacion: <br /><textarea name='justificacion1' style='font-family:arial;font-size:12' rows='4' cols='80' valign='top'></textarea></td></tr>
			<tr><td>Cambio de Ciudad <input type='checkbox' name='dos' onchange='activardos();'></td></tr>
			<tr><td id='tddos' style='visibility:hidden;'>Ciudad: $Ciudades Justificacion: <br><textarea name='justificacion2' style='font-family:arial;font-size:12' rows='4' cols='80' valign='top'></textarea></td></tr>
		</table>
		<center><input type='button' id='btn_enviar' name='btn_enviar' value=' ENVIAR SOLICITUD DE REACTIVACION/MODIFICACION ' onclick='enviar_solicitud()'></center>
		<input type='hidden' name='id' value='$id'><input type='hidden' name='Acc' value='solicita_reactivacion_ok'>
	</form>";
}
function flota_autorizacion($id)
{


	global $id;
	$C = qo("SELECT *,(SELECT nombre FROM aseguradora WHERE id=flota_original) AS fori,(SELECT nombre FROM aseguradora WHERE id=flota_temporal) AS ftemp FROM cambio_flota_solicitud a, vehiculo b, linea_vehiculo c where a.vehiculo=b.id AND b.linea=c.id AND a.id=$id and a.estado=0");
	echo "
				<script language='javascript'>
					function validar_cambio_flotax()
					{
						with(document.forma)
						{
							
							if(confirm('Desea Autorizar el cambio de Flota o Gama?'))
							Acc.value='autorizar_cambio_gama_autorizacion';
							submit();
						}
                                                recargar();
					}
                                        function anular_cambio_flota()
					{
						with(document.forma)
						{
							
							if(confirm('Desea Anular el cambio de Flota o Gama?'))
							Acc.value='anular_cambio_gama_autorizacion';
							submit();
						}
                                                recargar();
					}
					function recargar() { window.opener.location.reload(); }

				</script>
				<body style='font-size:16px' bgcolor='ffffdd'>
				<form action='zcallcenter2.php' target='' method='POST' name='forma' id='forma'>
					Por Orden de Gerencia, se debe justificar todo cambio de flota.<br><br>
					Justificacion: <input type='text' name='justificacion' id='justificacion' onclick='recargar()' value='$C->justificacion' size='80' maxlength='200' readonly><br>
                                            Placa: <input type='text' name='veh' id='veh' value='$C->placa' width='60px' readonly> Vehiculo: <input type='text' name='veh' id='veh' value='$C->nombre' width='60px' readonly><br>
                                                Flota Original: <input type='text' name='fori' id='fori' value='$C->fori' width='60px' readonly> a
                                                    Flota Temp: <input type='text' name='ftemp' id='ftemp' value='$C->ftemp' width='60px' readonly><br>
                                        Solicitado por: <input type='text' name='solicitado' id='solicitado' value='$C->solicitado_por' size='80' maxlength='200' readonly><br>
                                        Fecha Solicitud: <input type='text' name='fe' id='fe' value='$C->fecha' size='80' maxlength='200' readonly><br>
					Clave de Supervisor para autorizar el cambio de flota: <input type='password' name='clavesupervisor' id='clavesupervisor' value='' size='10' ><br><br>
					<input type='button' name='autorizar' id='autorizar' value=' AUTORIZAR CAMBIO DE FLOTA ' style='font-size:16px' onclick='validar_cambio_flotax();' >
                                        <input type='button' name='autorizar' id='autorizar' value=' ANULAR SOLICITUD ' style='font-size:16px' onclick='anular_cambio_flota();' ><br><br>
					<input type='hidden' name='Acc' value=''>
				
					<input type='hidden' name='idcambio' value='$id'>
					<br>
                                        <img src='$C->emblema_f' width='150px'> <img src='$C->vgenerica' width='150px'>

				</form>
                               </body>";
	die();
}
function correo_autorizar($id)
{
	global $id;
	$C = qo("select * from correo where siniestro=0 AND id=$id AND pdf=0");
	$usuariocallcenter = qo("SELECT * FROM usuario_callcenter WHERE usuario='$C->usuario'");
	echo "
	<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
				<script language='javascript'>
					function recargar() { window.opener.location.reload(); }
					function ver(id) { 
						console.log(id);
						var respuesta = $('#respuesta').val();
						if(id==0) {
							if(respuesta==''){
							alert('Rellene el formulario');
							}else{
								$.ajax({
									url:'https://pot.aoacolombia.com/api/siniestroactualizacion?siniestro='+id+'&id='+$id+'&respuesta='+respuesta,
									type: 'post',
									beforeSend: function () {
										
									},
									success: function (response) {   
										console.log('Se inserto');
										window.close();
									}
								});
							}
						}else{
							$.ajax({
								url:'https://pot.aoacolombia.com/api/siniestroactualizacion?siniestro='+id+'&id='+$id+'&respuesta='+respuesta,
								type: 'post',
								beforeSend: function () {
									
								},
								success: function (response) {   
									console.log('Se inserto');
									window.close();
								}
							});
						}
						
					}
					function validar_siniestro() { 
						var siniestro = document.getElementById('siniestro').value;
						$.ajax({
							
							url:'https://pot.aoacolombia.com/api/siniestroconsulta?placa='+siniestro,
							type: 'get',
							beforeSend: function () {
								
							},
							success: function (response) {
								
								if(response != ''){
									$('#autorizarno').hide();
									$('#nohay').hide();
								}else{
									$('#nohay').show();
								}   
								
								jQuery.each(response, function(index, item) {
									// console.log(item);
									const para = document.createElement('tr');
									para.innerHTML=
									'<td>'+item.placa+'</td>'+
									'<td>'+item.numero+'</td>'+
									'<td>'+item.estado+'</td>'+
									'<td>'+item.ingreso+'</td>'+
									'<td>'+item.nombre+'</td>'+
									'<td><button onclick=ver('+item.id+') value='+item.id+'>[+]</button></td>'
									;
									document.getElementById('tabla').appendChild(para);
									
								});
							}
						});
					 }
				</script>
				<style>
				table, th,tr, td {
					border: 1px solid black;
				  }
				</style>
				<body style='font-size:16px' bgcolor='ffffdd'>
				<form action='zcallcenter2.php' target='' method='POST' name='forma' id='forma'>
					VALIDAR LA INFORMACION<br><br>
					Enviado Por: <br>$usuariocallcenter->nombre<br><br>
					Asunto: <br><input type='text' name='fecha' id='justificacion' value='$C->asunto' size='40' maxlength='200' readonly><br>
					Fecha: <br><input type='text' name='fecha' id='justificacion' value='$C->fecha_envio' size='40' maxlength='200' readonly><br>
                    Justificacion :<br> <textarea name='justificacion' rows='10' cols='40' readonly>" . strip_tags($C->cuerpo_mensaje) . "</textarea>  <br>
					Respuesta : <br> <textarea name='justificacion' rows='10' cols='40' id='respuesta' placeholder='Apartado para responder la solicitud'></textarea>  <br>
					Placa: <br><input type='text' name='siniestro' id='siniestro' value='$C->siniestro' size='40' maxlength='200'>                  
					
					<input type='hidden' name='Acc' value=''>
					
					
					<input type='button' name='autorizar' id='autorizar' value='BUSCAR PLACA' style='font-size:16px' onclick='validar_siniestro();' ><br><br>
					<input type='button' name='autorizar' id='autorizarno' value='NO TIENE PLACA' style='font-size:16px' onclick='ver(0);' >
					<input type='hidden' name='idcambio' value='$id'>
					
				</form>
				<div id='mostrarsiniestro'>
					<table id='tabla'>
									<tr>
										<th>Placa</th>
										<th>Siniestro</th>
										<th>Estado</th>
										<th>Ingreso</th>
										<th>Aseguradora</th>
										<th>Elegir</th>
									</tr>
									</table>
					</div>
					<div id='nohay' style='display:none;'>No hay ninguna placa que coincida</div>
                               </body>";
	die();
}
function correo_autorizar_anulado($var, $id)
{
	global $id;
	$C = qo("select * from correo where siniestro=0 AND id=$id AND pdf=0");
	$usuariocallcenter = qo("SELECT * FROM usuario_callcenter WHERE usuario='$C->usuario'");
	echo "
	<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
				<script language='javascript'>
					function recargar() { window.opener.location.reload(); }
					function ver(id) { 
						console.log(id);
						var respuesta = $('#respuesta').val();
						if(id==0) {
							if(respuesta==''){
							alert('Rellene el formulario');
							}else{
								$.ajax({
									url:'https://pot.aoacolombia.com/api/siniestroactualizacion?siniestro='+id+'&id='+$id+'&respuesta='+respuesta,
									type: 'post',
									beforeSend: function () {
										
									},
									success: function (response) {   
										console.log('Se inserto');
										window.close();
									}
								});
							}
						}else{
							$.ajax({
								url:'https://pot.aoacolombia.com/api/siniestroactualizacion?siniestro='+id+'&id='+$id+'&respuesta='+respuesta,
								type: 'post',
								beforeSend: function () {
									
								},
								success: function (response) {   
									console.log('Se inserto');
									window.close();
								}
							});
						}
						
					}
			
				</script>
				<style>
				table, th,tr, td {
					border: 1px solid black;
				  }
				</style>
				<body style='font-size:16px' bgcolor='ffffdd'>
				<form action='zcallcenter2.php' target='' method='POST' name='forma' id='forma'>
					VALIDAR LA INFORMACION<br><br>
					Enviado Por: <br>$usuariocallcenter->nombre<br><br>
					Asunto: <br><input type='text' name='fecha' id='justificacion' value='$C->asunto' size='40' maxlength='200' readonly><br>
					Fecha: <br><input type='text' name='fecha' id='justificacion' value='$C->fecha_envio' size='40' maxlength='200' readonly><br>
                    Justificacion :<br> <textarea name='justificacion' rows='10' cols='40' readonly>" . strip_tags($C->cuerpo_mensaje) . "</textarea>  <br>
					Respuesta : <br> <textarea name='justificacion' rows='10' cols='40' id='respuesta' readonly>$C->respuesta</textarea>  <br>
					
					
				</form>
				
                               </body>";
	die();
}
function solicitud_cambio_gama()
{
	global $clavesupervisor, $flota_original, $flota_temporal, $vehiculo, $IDUSUARIO, $NUSUARIO, $justificacion;
	$Ahora = date('Y-m-d H:i:s');
	$Eclave = e($clavesupervisor);
	if ($C = qo("select * from cambio_flota_solicitud where vehiculo='$vehiculo' and estado=0 ")) {
		//echo "<script language='javascript'>alert('Ya se hizo la solicitud de cambio de gama');window.close();</script></body>";

		echo "<body><script language='javascript'>alert('Ya se hizo la solicitud de cambio de gama..');window.close();window.opener.close()</script></body>";
	} else {
		q("insert into cambio_flota_solicitud (vehiculo,flota_original,flota_temporal,estado,fecha,solicitado_por,justificacion) values
			('$vehiculo','$flota_original','$flota_temporal','0','$Ahora','$NUSUARIO',\"$justificacion\")");
		//echo "<script language='javascript'>alert('Se genero la solicitud con exito');window.close();</script>";
		echo "<body><script language='javascript'>alert('Se genero la solicitud con exito');window.close();window.opener.close()</script></body>";
	}
}
function cambiar_ciudad_caso()
{
	global $id, $nueva_oficina;
	$codigo_ciudad = qo1("select ciudad from oficina where id=$nueva_oficina");
	q("update siniestro set ciudad='$codigo_ciudad' where id=$id");
	echo "<body><script language='javascript'>alert('Ciudad cambiada');parent.re_comenzar();</script></body>";
}

function aceptacion_condiciones()
{
	global $id, $IDUSUARIO, $NUSUARIO;
	$D = qo("select * from call2cola2 where id='$id'");
	$Ahora = date('Y-m-d H:i:s');
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	q("update call2cola2 set aceptado=1,fecha_aceptacion='$Ahora' where id=$id");
	q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ('$D->siniestro','$NUSUARIO','$Fecha','$Hora','Acepta Terminos y Condiciones',18)");
	q("update siniestro set observaciones=concat(observaciones,'\n" . $NUSUARIO . ' ' . date('Y-m-d H:i') . " Aceptacion de t&eacute;rminos y condiciones por el Agente de Call Center.') where id=$id");
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}

function redireccionar_caso_abierto()
{
	global $id;
	sleep(1);
	echo "<body><script language='javascript'>parent.cargar_caso($id);</script></body>";
}

function adjudicacion_finalizada()
{
	echo "<body><script language='javascript'>opener.adjudicacion_finalizada();window.close();void(null);</script></body>";
}

function cerrar_caso_abierto()
{
	global $idc, $NUSUARIO;
	$Ahora = date('Y-m-d H:i:s');
	q("update call2proceso set estado='C',fecha_cierre='$Ahora' where id=$idc");
	graba_bitacora('call2proceso', 'M', $idc, 'Cierra desde Control Call Center');
	echo "<body><script language='javascript'>alert('Caso Cerrado Satisfactoriamente');parent.re_comenzar();</script></body>";
}


function cerrar_caso_abierto_novedad()
{
	global $idc, $NUSUARIO;
	$Ahora = date('Y-m-d H:i:s');
	q("update call2proceso set estado='C',fecha_cierre='$Ahora' where id=$idc");
	graba_bitacora('call2proceso', 'M', $idc, 'Cierra desde Control Call Center');
	echo "<body><script language='javascript'> 
	window.close();
	alert('Caso Cerrado Satisfactoriamente');
	parent.re_comenzar();
	
	</script></body>";
}

function re_agendar_cita()
{
	global $id;
	$Cita = qo("select * from cita_servicio where id=$id");
	$_SESSION['Adjudicacion_OFICINA'] = $Cita->oficina;
	$_SESSION['Adjudicacion_ASEGURADORA'] = $Cita->flota;
	$_SESSION['Adjudicacion_NIVEL'] = 4;
	$_SESSION['Adjudicacion_SINIESTRO'] = $Cita->siniestro;
	$_SESSION['Adjudicacion_READJUDICAR'] = true;
	echo "<body><script language='javascript'>
	window.open('zcallcenter2.php?Acc=recargar_control','Oculto_control');
	parent.re_agendar_cerrar();</script></body>";
}

function recargar_control()
{
	echo "<body><script language='javascript'>parent.recargar_datos();</script></body>";
}

function mata_reasignacion()
{
	$_SESSION['Adjudicacion_OFICINA'] = false;
	$_SESSION['Adjudicacion_ASEGURADORA'] = false;
	$_SESSION['Adjudicacion_NIVEL'] = false;
	$_SESSION['Adjudicacion_SINIESTRO'] = false;
	$_SESSION['Adjudicacion_READJUDICAR'] = false;
	echo "<body><script language='javascript'>window.open('zcallcenter2.php?Acc=recargar_control','Oculto_control');window.close();void(null);</script></body>";
}

function envio_email_inicial()
{
	global $id, $NUSUARIO, $USUARIO;
}

function marcacion_numero_alterno()
{
	global $id, $NUSUARIO, $USUARIO, $IDUSUARIO;
	// BUSCA LOS EVENTOS DE BUZON DEL CASO
	html();
	echo "<script language='javascript'>
		function validar_envio_marcacion()
		{
			with(document.forma)
			{
				if(!alltrim(observaciones.value)) {alert('Debe digitar las observaciones');observaciones.style.backgroundColor='ffffaa';observaciones.focus();return false;}
				if(confirm('Est&aacute; seguro de Marcar a un n&uacute;mero alterno?')) submit();
			}
		}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'><h4>Marcacion N&uacute;mero Alterno</h4>
		<form action='zcallcenter2.php' method='post' target='_self' name='forma' id='forma'>
			<br>
			<br>Observaciones: <input type='text' name='observaciones' id='observaciones' size='80'><br>
			<br><input type='hidden' name='Acc' value='marcacion_numero_alterno_ok'>
			<input type='hidden' name='id' value='$id'>
			<input type='button' style='font-size:16px;font-weight:bold' value=' CONTINUAR ' onclick='validar_envio_marcacion();'>
		</form>
	</body>";
}

function marcacion_numero_alterno_ok()
{
	global $id, $NUSUARIO, $IDUSUARIO, $observaciones;
	$Ahora = date('Y-m-d H:i:s');
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');
	$Codigo = 26; /*Marcacion a numero alterno */
	$Idn = q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','Marcacion a N&uacute;mero alterno.',$Codigo)");
	q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: Marcacion a Numero Alterno: $observaciones. \") where id=$id");
	q("update call2proceso set fecha_cierre='$Ahora',estado='C' where siniestro='$id' and estado='A' ");
	echo "<body><script language='javascript'>parent.re_comenzar();</script></body>";
}
