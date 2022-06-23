<?php

/**  programa para consulta inteligente de siniestros */

include('inc/funciones_.php');
sesion(); // verifica la sesion del usuario
$Historico=0;
$USUARIO=$_SESSION['User'];

$NUSUARIO=$_SESSION['Nombre'];
if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}
inicial();

function inicial()
{
	global $USUARIO;
	html('CONSULTA INTELIGENTE SINIESTROS');  // pinta cabeceras de html y el formulario de busqueda
	echo "
	<script language='javascript'>
		function enviar_consulta()
		{
			document.forma.consultar.style.visibility='hidden';
			document.forma.submit();
		}
		function limpiardemas(){
			console.log('limpiando demas');
			document.getElementById('siniestro').value='';
			document.getElementById('placa').value='';
			document.getElementById('id').value='';
		}
	</script>
	<body style='font-size:16px' bgcolor='ddddff'><script language='javascript'>centrar();</script>
	<form action='zsiniestro.php' target='Tablero_busca_siniestro' method='POST' name='forma' id='forma'>
		Siniestro: <input type='text' style='font-size:16px;font-weight:bold;' name='siniestro' id='siniestro' > 
		Placa: <input type='text' style='font-size:16px;font-weight:bold;font-family:times new roman;' name='placa' id='placa' size=6 onfocus=\"document.forma.siniestro.value='';\"
		onkeyup='this.value=this.value.toUpperCase();'> 
		Id: <input type='number' style='font-size:16px;font-weight:bold;' class='numero' name='id' id='id' onfocus=\"document.forma.siniestro.value='';\"> ";
		if (inlist($USUARIO,'1,34')) {
			echo "N. Obligacion: <input type='text' style='font-size:16px;font-weight:bold;' name='num_obligacion' id='num_obligacion' onkeyup=\"limpiardemas()\"> ";

		}
		echo "<input type='button' style='font-size:16px;font-weight:bold;' name='consultar' id='consultar' value=' Consultar ' onclick='enviar_consulta();' >
		<input type='hidden' name='Acc' value='buscar_siniestro'>
	</form>
	<script language='javascript'>var Valor_busqueda=getCookie('consulta_SINIESTRO_');
	document.forma.siniestro.value=Valor_busqueda;</script>
	<iframe name='Tablero_busca_siniestro' id='Tablero_busca_siniestro' style='visibility:visible;border-width:4px' frameborder='yes'  border='4' width='98%' height='80%'></iframe></body>";
}

function buscar_siniestro() // recibe la informacion del formulario de busqueda
{  
	global $siniestro, $placa, $id, $Historico,$num_obligacion;
	sesion(); //verifica la sesion del usuario
	if($id)
	{
		if(!$Sins=q("select s.*,a.nombre as naseg ,es.nombre as nombreestado from siniestro s,aseguradora a ,estado_siniestro as es where es.id=s.estado and a.id=s.aseguradora and s.id=$id")) // busca por id en la tabla de siniestros
		{$Sins=q("select s.*,a.nombre as naseg ,es.nombre as nombreestado from siniestro_hst s,aseguradora a ,estado_siniestro as es where es.id=s.estado and a.id=s.aseguradora and s.id=$id");$Historico=1;} //  busca por id en la tabla de siniestros historicos
	}
	else 
	{
		if($siniestro)
		{// busca por numero de siniestro
			$Sins=q("select s.*,a.nombre as naseg,es.nombre as nombreestado from siniestro s,aseguradora a ,estado_siniestro as es where es.id=s.estado and s.aseguradora=a.id and s.numero like '%$siniestro%' order by s.ingreso desc");
			
			
			if(!$Sins) {$Sins=q("select s.*,a.nombre as naseg ,es.nombre as nombreestado from siniestro_hst s,aseguradora a ,estado_siniestro as es where es.id=s.estado and s.aseguradora=a.id and s.numero like '%$siniestro%' order by s.ingreso desc");$Historico=1;}
		}
		elseif ($num_obligacion) {
			$Sins=q("select aseguradora(c.aseguradora) AS naseg, c.numero, a.placa, c.asegurado_nombre, c.ingreso, c.id,c.* FROM cita_servicio a, vehiculo b, siniestro c WHERE a.placa=b.placa AND a.siniestro=c.id AND a.estado IN ('P','C') AND n_contrato='$num_obligacion' order by c.ingreso desc");
		}
		elseif($placa)
		{ // busca por placa
			$Sins=q("select s.*,a.nombre as naseg,es.nombre as nombreestado from siniestro s,aseguradora a,estado_siniestro as es where es.id=s.estado and s.aseguradora=a.id and s.placa like '%$placa%' order by s.ingreso desc");
			if(!$Sins) {$Sins=q("select s.*,a.nombre as naseg,es.nombre as nombreestado from siniestro_hst s,aseguradora a,estado_siniestro as es where es.id=s.estado and s.aseguradora=a.id and s.placa like '%$placa%' order by s.ingreso desc");$Historico=1;}
		}
		else $Sins=false;
	}
	if($Sins)
	{
		html(); // pinta cabeceras html
		if(mysql_num_rows($Sins)>1) // si son varios siniestros.
		{
			
			echo "<script language='javascript'>
			function ver_este(id)
			{window.open('zsiniestro.php?Acc=buscar_siniestro&id='+id,'_self');}
			</script><body style='font-size:14px;'>
			<h3>Siniestro: $siniestro</h3><b>Debe seleccionar uno de los siguientes registros:</b><br>
			<table border=0 cellspacing='5' ><tbody ><tr >
				<th style='font-size:14px;'>Aseguradora</th>
				<th style='font-size:14px;'>Siniestro</th><th style='font-size:14px;'>Placa</th>
				<th style='font-size:14px;'>Asegurado</th><th style='font-size:14px;'>Ingreso</th><th>Estado</th><th></th></tr>";
			while($S=mysql_fetch_object($Sins)) // pinta siniestro por siniestro para escoger uno de los que han sido encontrados
			{	echo "<tr><td style='font-size:14px;'>$S->naseg</td><td style='font-size:14px;'>$S->numero</td>
							<td style='font-size:14px;font-family:times new roman;font-weight:bold;'>$S->placa</td>
							<td style='font-size:14px;'>$S->asegurado_nombre</td><td style='font-size:14px;'>$S->ingreso</td><td style='font-size:14px;'>$S->nombreestado</td>
							<td align='center'><a style='font-size:14px;' onclick='ver_este($S->id);'><img src='gifs/standar/derecha.png' height='20px'></a></td></tr>";	}
			echo "</tbody></table>
				<script language='javascript'>parent.document.forma.consultar.style.visibility='visible';</script></body>";
		}
		else
		{
			// consigue todos los permisos de perfil para siniestro, recibo de caja, notas contables, notas credito, citas, solicitudes de extras, ubicaciones, solicitudes de factura
			// solicitud de datos de autorizaciones.
			$USUARIO = $_SESSION['User'];
			$NickUsuario=$_SESSION['Nick'];
			$Nusuario = $_SESSION['Nombre'];
			$NTAU=tu('sin_autor','id');
			if($Historico) $NTSN=tu('siniestro_hst','id'); else $NTSN=tu('siniestro','id');
			$NTSN_old=tu('siniestro_hst','id');
			$NTRC=tu('recibo_caja','id');
			$NTNCO=tu('nota_contable','id');
			$NTNCR=tu('nota_credito','id');
			$NTCS=tu('cita_servicio','id');
			$NTSE=tu('solicitud_extra','id');
			$NTUB=tu('ubicacion','id');
			$NTSF=tu('solicitud_factura','id');
			$NTSV=tu('solicitud_dataautor','id');
		
			$S=mysql_fetch_object($Sins); // extrae en objeto los datos del siniestro
			$Nestado=qo1("select t_estado_siniestro($S->estado)"); // trae el estado del siniestro
			$Ciudad=qo("select * from ciudad where codigo='$S->ciudad'"); // trae los datos de la ciudad
			$Aseguradora=qo("select * from aseguradora where id=$S->aseguradora"); // trae los datos de la aseguradora
			// $NickUsuarioEncrypt = openssl_encrypt($NickUsuario,"AES-128-ECB","a");
			// pinta todas las herramientas javascript
			echo "<script language='javascript'>
				var IDS_FACTURACION='0';
				var IDCITA_FACTURACION=0;
				function modificar_siniestro(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTSN&id='+id,0,0,700,900,'au');}
				function ver_seguimiento(id) {modal('zsiniestro.php?Acc=ver_seguimiento&id='+id+'&Historico=$Historico',0,0,600,900,'vs');}
				function ver_observaciones(id) {modal('zsiniestro.php?Acc=ver_observaciones&id='+id,50,50,600,900,'vo');}
				
				function insertarObservacion(id) {modal('zsiniestro.php?Acc=insertarObservacion&id='+id,10,20,200,700,'vo');}
				
				function mod_sin_autor(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTAU&id='+id,0,0,700,900,'au');}
				function mod_sin_autor_update(id) {modal('zsiniestro.php?Acc=updateSinautor&id='+id,0,0,700,900,'au');}
				function modificar_sinautor(id){modal('zsiniestro.php?Acc=modificarSinautor&id='+id,0,0,700,900,'au');}
				function mod_recibo_caja(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTRC&id='+id,0,0,700,900,'au');}
				function mod_nota_contable(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTNCO&id='+id,0,0,700,900,'au');}
				function arribo_asegurado(id) { if(confirm('Desea marcar esta cita con el indicador de Arribo de Asegurado?')) window.open('zcitas.php?Acc=arribo_asegurado&id='+id,'Citas_oculto'); }
				function mod_nota_credito(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTNCR&id='+id,0,0,700,900,'au');}
				function mod_cita(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTCS&id='+id,0,0,700,900,'au');}
				function mod_extra(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTSE&id='+id,0,0,700,900,'au');}
				function mod_ubicacion(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTUB&id='+id,0,0,700,900,'au');}
				function imprime_fac(id) {modal('zfacturacion.php?Acc=imprimir_factura&id='+id,0,0,600,900,'fac');}
				function imprime_rc(id) {modal('zcartera.php?Acc=imprimir_recibo&id='+id,0,0,600,900,'fac');}
				function imprime_nota_contable(id) {modal('zcartera.php?Acc=imprimir_ncontable&id='+id,0,0,600,900,'fac');}
				function solicitar_info_aut() {modal('zautorizaciones.php?Acc=datos_autorizacion&id=$S->id',0,0,500,500,'solicitud');}
				function verificar_cartera() {modal('zcartera.php?Acc=consulta_cartera&Siniestro=$S->id',0,0,600,s_ancho()-20,'solicitud');}
				function verificar_garantia() {modal('zcontrol_custodia_garantia.php?Acc=consultar_garantias&IDsiniestro=$S->id',0,0,600,s_ancho()-20,'solicitud');}
				function ver_contrato() {modal('../../servicio/pdf/$Aseguradora->archivo_contrato',0,0,600,s_ancho()-20,'solicitud');}
				function actualizar_datos() {modal('zcontrol_operativo.php?Acc=actualizar_info&ids=$S->id',0,0,400,600,'aifn');}
				function reenviar_correo(id){if(confirm('Desea Re-enviar el correo al asegurado?')) window.open('zcallcenter2.php?Acc=reenviar_correo_adjudicacion&id='+id,'Oculto_ver_siniestro');}
				function re_agendar_cita(id) {if(confirm('Desea Re-Asignar la cita?')) window.open('zcallcenter2.php?Acc=re_agendar_cita&id='+id,'Oculto_ver_siniestro');}
				function modalcorreo(id) {modal('https://pot.aoacolombia.com/versinicorreo/'+id,'Oculto_ver_siniestro',400,400,600,900,'au');}
				function correoenviar() {modal('http://ctc.aoacolombia.com/auth1/$NickUsuario/$S->id/$USUARIO','Oculto_ver_siniestro',400,800,1000,900,'au');}
				function correoenviarfactura(id) {
					var x = document.getElementById('consecutivfac'+id).value;
					console.log(x);
					modal('http://ctc.aoacolombia.com/auth1/$NickUsuario/'+x+'/$USUARIO','Oculto_ver_siniestro',400,800,1000,900,'ad');}
				function re_agendar_cerrar() {alert('A continuacion seleccione un veh&iacute;culo para reagendar la cita');window.close();void(null);}
				function retornar_siniestro(dato) {modal('util.php?Acc=devolver_siniestro&id='+dato,0,0,200,200,'dsin');}
				function mod_solicitud_factura(dato) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTSF&id='+dato,0,0,700,900,'msf');}
				function mod_solicitud_data(dato) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTSV&id='+dato,0,0,700,900,'msf');}
				function solicitar_factura(id) {modal('zcitas.php?Acc=solicitar_factura&cita='+id,0,0,400,500,'sfac');}
				function marcar_facturacion(dato,marca) 
				{
					if(marca) 
					{
						document.getElementById('div_facturar').style.display='block';
						Muestra_activar_citas_facturacion();
						IDS_FACTURACION+=','+dato;
					}
					else IDS_FACTURACION=IDS_FACTURACION.replace(','+dato,',0');
				}
				function activa_cita_facturar(dato,marca)
				{
					if(marca) IDCITA_FACTURACION=dato; else IDCITA_FACTURACION=0;
				}
				function facturar_conceptos()
				{
					IDS_FACTURACION=IDS_FACTURACION.replace(',0','');
					if(IDS_FACTURACION=='0') {alert('Debe tener marcado al menos una solicitud de facturacion');return false;}
					if(IDCITA_FACTURACION==0) {alert('Debe marcar una cita de entrega o devolucion para asociar la factura.'); return false;}
					if(confirm('Desea elaborar la factura de este(os) concepto(s)?')) 
					{
						window.open('zfacturacion.php?Acc=inserta_desde_cita&VS=1&idCita='+IDCITA_FACTURACION+'&IDSF='+IDS_FACTURACION,'capa_Facturacion');
						document.getElementById('capa_Facturacion').style.display='block'; 
					}
				}
				function cerrar_facturacion()
				{
					document.getElementById('capa_Facturacion').style.display='none';
				}
				</script>
				<style tyle='text/css'>
					a {cursor:pointer;color:000088;background-color:ddddff;}
					a:hover {color:ffffff;background-color:000088;}
				</style>
				<body>
				<script language='javascript'>
					var Expira=new Date();
					Expira.setTime(Expira.getTime()+30*24*60*60*1000);
					setCookie('consulta_SINIESTRO_','$S->numero',Expira);
				</script>
				<iframe name='Oculto_ver_siniestro' id='Oculto_ver_siniestro' style='visibility:hidden' width='1' height='1'></iframe>
				<iframe name='capa_Facturacion' id='capa_Facturacion' style='display:none;position:fixed;z-index:25;' width='95%' height='90%'></iframe>
				<h3>SINIESTRO: $S->numero ASEGURADORA: $Aseguradora->nombre <img src='$Aseguradora->emblema_f' border=2 height='20px'> 
				".($NTSN?"<a onclick='modificar_siniestro($S->id);'>[+]</a>":"")." ID: $S->id
				</h3>
				<table cellspacing=8><tr>";
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				echo "<th style='font-size:16px'>SINIESTRO</th><th style='font-size:16px'>SERVICIO</th></tr>";
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$isReplace = $S->vh_remplazo;
		$estado = $S->estado;
	    if($isReplace  || $estado == 8){$v = "checked disabled";}else{$v = "";}
		$htmlRemplazo = "<tr><td bgcolor='ffffff'>Remplazo:</td><td bgcolor='ffffff'><input type='checkbox' id='replacement' $v><input type='hidden' id='idSiniestro' value='$S->id'></td></tr><br><br>
							<script language='javascript' src='inc/js/helperJs.js'></script>";
		if(inlist($USUARIO,'1,10,26')){$t = $htmlRemplazo;}else{$t = "";}					
							
			echo "<tr><td bgcolor='ffffff' valign='top'>
						<table width='100%' bgcolor='dddddd'>
							<tr><td bgcolor='ffffff'>Asegurado:</td><td bgcolor='ffffff'><b>$S->asegurado_nombre</b></td></tr>
							<tr><td bgcolor='ffffff'>Declarante:</td><td bgcolor='ffffff'><b>$S->declarante_nombre $S->declarante_telefono <br>$S->declarante_celular $S->declarante_tel_ofic $S->declarate_tel_otro</b></td></tr>
							<tr><td bgcolor='ffffff'>Email Declarante:</td><td bgcolor='ffffff'><b>$S->declarante_email</b></td></tr>$t";
							
			if($S->actualizacion_aseg){ // verifica si han habido actualizaciones de la aseguradora
			echo "<tr><td bgcolor='ffffff'>Actualizaciones:</td><td bgcolor='ffffff' width='300'><b>$S->actualizacion_aseg</b></td></tr>";}
			/*Pqr asociado*/
			if($S->pqr_asociado != 0){
				 $sql = "SELECT pqso.fecha,pqso.descripcion,est.nombre estado, est.color_co color FROM pqr_solicitud pqso 
							LEFT JOIN pqr_estado est ON  pqso.estado = est.id
							WHERE pqso.id = $S->pqr_asociado";
				 $consultaPqr = qo($sql);
			 	 $htmlPqr = "<tr><td bgcolor='ffffff'>Hay un PQR en el siniestro:</td> <td bgcolor='ffffff' width='300'><b>Numero: $S->pqr_asociado</b></td>
				             <tr><td bgcolor='ffffff'>Fecha PQR:</td> <td bgcolor='ffffff' width='300'><b>$consultaPqr->fecha</b></td>
							 <tr><td bgcolor='ffffff'>Descripcion PQR:</td> <td bgcolor='ffffff' width='300'><b>$consultaPqr->descripcion</b></td>
							 <tr><td bgcolor='ffffff'>Estado PQR:</td> <td style='background:$consultaPqr->color' width='300'><b>$consultaPqr->estado</b></td>";
							 
							 echo $htmlPqr;
			}
			
			if(inlist($USUARIO,'1,2,3')){ // solo para super usuario, gerencia y control operativo aparece la opcion de actualizar datos
			echo "<tr><td bgcolor='ffffff' colspan='2'><a onclick='actualizar_datos()'><u><i>Actualizar Datos</i></u></a></td></tr>";
			}
			if(inlist($USUARIO,'32,1')){
				$citaSql = "select arribo,id from cita_servicio where siniestro = $S->id";$ciVali = qo($citaSql);
				$varRecepcion =  q("select * from aoacol_administra.ingreso_recepcion where siniestro=$S->id ");
				while($i = mysql_fetch_object($varRecepcion)){
					  $varFoto = $i->foto_f;
				}
				if($varFoto == ""){
						echo "<td><a class='info' style='cursor:pointer' onclick='arribo_asegurado($ciVali->id);'><img src='img/arribo_asegurado.png' border='0' height='30'><span>Marcar Arribo de Asegurado</span></a></td>&nbsp;";   
					  }else{
						echo "<td>Ya tiene foto</td>";  
					  }
			   }
			
			if($IR=q("select * from aoacol_administra.ingreso_recepcion where siniestro=$S->id ")) // busca los datos de la recepcion en el arribo.
			{
				while($Ir=mysql_fetch_object($IR)) // muestra el(los) visitante(s) 
			//echo "select * from aoacol_administra.ingreso_recepcion where siniestro=$S->id ";	
			echo "<tr><td bgcolor='ffffff'>Ingreso:</td><td bgcolor='ffffff'><b>$Ir->fecha".
						($Ir->foto_f?"<a onclick=\"modal('../../Administrativo/$Ir->foto_f',10,10,300,400,'img01');\"><img src='../../Administrativo/$Ir->foto_f' border='0' height='20px'></a>":"").
						"</b></td></tr>";
			}
			else
			{
				echo "<tr><td colspan='2' bgcolor='ffffff'>No hay registro de ingreso en Recepcion</td></tr>";
			}
			// pinta datos del siniestro
			echo "<tr><td align='right' bgcolor='ffffff'>Ciudad:</td><td bgcolor='ffffff'><b>$Ciudad->nombre</b></td></tr>
					<tr><td align='right' bgcolor='ffffff'>Placa:</td><td bgcolor='ffffff'><b>$S->placa</b></td></tr>
					<tr><td align='right' bgcolor='ffffff'>Ingreso:</td><td bgcolor='ffffff'><b>$S->ingreso</b></td></tr>
					<tr><td align='right' bgcolor='ffffff'>Estado</td><td bgcolor='ffffff'><b>$Nestado</b>";
			if($S->estado==1 && inlist($USUARIO,'1,2,26')) // para el coordinador de call center permite solicitar la reactivacion del siniestro
			{
				echo " <a onclick=\"modal('zcallcenter2.php?Acc=solicita_reactivacion&id=$S->id',0,0,500,500,'sra');\">Solicitar Reactivacion</a></td>";
			}
			echo "</tr>
					<tr><td align='right' bgcolor='ffffff'>D&iacute;as de Servicio</td><td bgcolor='ffffff'> <b>$S->dias_servicio</b></td></tr>
					</table>";
			// Permite solicitar informacion de las garantias
			echo "<a onclick='solicitar_info_aut();'><u>Solicitar Informacion de Autorizaciones</u></a> 
				";
			if(inlist($USUARIO,'1,2,3,5,10')) // autorizaciones y oficinas: verificacion de cartera y de garantias
			echo "<a onclick='verificar_cartera();'><u>Verificar Cartera</u></a> <a onclick='verificar_garantia();'><u>Verificar Garant&iacute;a</u></a>";
			// datos del contrato
			echo "<br><br>M&aacute;ximo kilometraje permitido: ".($Aseguradora->limite_kilometraje>10000?"ILIMITADO":coma_format($Aseguradora->limite_kilometraje))."<br>
				Valor de la garant&iacute;a: ".coma_format($Aseguradora->garantia)." <a onclick='ver_contrato()'><u>Ver Contrato</u></a> ";
			// Si el siniestro est&eacute; dentro de la tabla de historicos, permite retornarlo
			if($Historico) echo "<a onclick='retornar_siniestro($S->id)';><u>Retornar Siniestro</u></a>";
			echo "</td>";
			///////////////////////////////////////////////////////////////////////////////////////
			echo "<td  bgcolor='ffffff' valign='top'>
					<table width='100%'>
						<tr><td colspan='4'>
							<table width='100%'><tr>
								<tr>
								<td align='center' bgcolor='ddddee'><a onclick='ver_seguimiento($S->id);'><u>Ver Seguimiento</u></a></td>
								<td align='center' bgcolor='ddddee'><a onclick='ver_observaciones($S->id);'><u>Ver Observaciones</u></a></td>";
								if($USUARIO == 3){
									echo "<td align='center' bgcolor='ddddee'><a onclick='insertarObservacion($S->id);'><u>Adicionar Observaciones</u></a></td>";
								}
								 
							
							 echo "</tr></table>
						</td></tr>
						";
			// busta todas las citas que correspondan a este siniestro sin importar los estados que hayan tenido
			$Muestra_activar_citas_facturacion='';
			if($Citas=q("select c.*,o.nombre as noficina,ec.nombre as nestado,c.estadod,ec.color_co
							from cita_servicio c,estado_citas ec,oficina o
							where c.siniestro=$S->id and c.oficina=o.id and c.estado=ec.codigo
							order by c.fecha,c.hora,c.id"))
			{
				echo "<tr><td align='center' bgcolor='eeeeff'><b>CITA-CIUDAD</b></td>
						<td align='center'  bgcolor='eeeeff'><b>FECHA - HORA</b></td>
						<td align='center' bgcolor='eeeeff'><b>ESTADO</b></td>
						<td align='center' bgcolor='eeeeff'><b>Dias</b></td>
						</tr>";
				while($Cita=mysql_fetch_object($Citas)) // cita por cita pinta los detalles
				{
					$nestadod='';
					if($Cita->estadod=='P') $nestadod='Programada';
					if($Cita->estadod=='C') $nestadod='Cumplida';
					$fdevol=($Cita->fec_devolucion!='0000-00-00'?$Cita->fec_devolucion.' '.$Cita->hora_devol:'--');
					
					echo "<tr bgcolor='eeeeee'><td>Entrega $Cita->noficina</td><td align='center'>$Cita->fecha $Cita->hora</td><td bgcolor='$Cita->color_co'>$Cita->nestado 
					".($NTCS?"&nbsp;&nbsp;<a class='rinfo' onclick='mod_cita($Cita->id);'>[+]<span style='width:300px'>Ver la Cita</span></a>&nbsp;&nbsp;":"").
					($Cita->estado=='P' && inlist($USUARIO,'1,26')?"&nbsp;&nbsp;<a class='rinfo' onclick='re_agendar_cita($Cita->id);'>[R]<span style='width:300px'>Re Agendar Cita</span></a>&nbsp;&nbsp;":"").
					"<a class='rinfo' onclick='reenviar_correo($Cita->id);'>[E]<span style='width:300px'>Re-Enviar correo al asegurado</span></a>
					</td><td align='center'>$Cita->dias_servicio".($Cita->estado=='P'?"<br><a onclick='solicitar_factura($Cita->id);'>Solicitar Factura</a>":"")."
						<div id='div_cita$Cita->id' style='display:none;background-color:#E3FF93;'>
							Facturar con esta cita: <input type='checkbox' id='chk_Cita$Cita->id' name='chk_Cita$Cita->id' onclick='activa_cita_facturar($Cita->id,this.checked);'>
						</div>
					</td></tr>";
					if($Cita->estado=='C') echo "<tr><td>Devolucion $Cita->noficina</td><td align='center'>$fdevol</td><td>$nestadod</td><td><a onclick='solicitar_factura($Cita->id);'>Solicitar Factura</a>
						<div id='div_cita$Cita->id' style='display:none;background-color:#E3FF93;'>
							Facturar con esta cita: <input type='checkbox' id='chk_Cita$Cita->id' name='chk_Cita$Cita->id' onclick='activa_cita_facturar($Cita->id,this.checked);'>
						</div>
					</td></tr>";
					$Muestra_activar_citas_facturacion.="document.getElementById('div_cita$Cita->id').style.display='block'; ";
				}
			}
			// Busca la ubicacion, que es el estado de servicio en la tabla de control
			
			
			if($Ub=qo("select * from ubicacion where id=$S->ubicacion"))
			{
				// obtiene los datos del vehiculo
				
				$Vehiculo=qo("select v.placa,a.nombre as nflota,l.nombre as nlinea,modelo,cilindraje,nombre_propietario 
									from vehiculo v,aseguradora a ,linea_vehiculo l
									where v.id=$Ub->vehiculo and v.flota=a.id and v.linea=l.id	");
									
									
				$Oficina=qo("select * from oficina where id=$Ub->oficina"); // obtiene los datos de la oficina
				$Estadov=qo("select * from estado_vehiculo where id=$Ub->estado"); // obtiene el estado del vehculo
				// pinta los detalles del vehiculo y el estado de servicio
				echo "<tr><td align='right'>Veh&iacute;culo:</td><td colspan='2'>
									<table bgcolor='dddddd'>
										<tr><td align='right' bgcolor='ffffff'>Placa:</td><td bgcolor='ffffff'><b>$Vehiculo->placa</b> </td></tr>
										<tr><td align='right' bgcolor='ffffff'>Linea:</td><td bgcolor='ffffff'><b>$Vehiculo->nlinea</b></td></tr>
										<tr><td align='right' bgcolor='ffffff'>Flota:</td><td bgcolor='ffffff'><b>$Vehiculo->nflota</b></td></tr>
										<tr><td align='right' bgcolor='ffffff'>Modelo:</td><td bgcolor='ffffff'><b>$Vehiculo->modelo</b> Cilindraje: <b>$Vehiculo->cilindraje</b></td></tr>
										<tr><td align='right' bgcolor='ffffff'>Propietario:</td><td bgcolor='ffffff'><b>$Vehiculo->nombre_propietario</b></td></tr>
									</table>
							</td></tr>
							<tr><td align='right'>Oficina:</td><td colspan='2'><b>$Oficina->nombre</b> Estado actual: <b style='background-color:$Estadov->color_co'>$Estadov->nombre</b></td></tr>
							<tr><td align='right'>Fechas del servicio:</td><td><b>$Ub->fecha_inicial - $Ub->fecha_final</b> ".($NTUB?"<a onclick='mod_ubicacion($Ub->id);'>[+]</a>":"")."</td></tr>
							<tr><td align='right'>Kilometrajes:</td><td><b>$Ub->odometro_inicial - $Ub->odometro_final Total: $Ub->odometro_diferencia</b></td></tr>
							";
			}
			echo "</table>";
			
			/// BUSQUEDA DE SERVICIOS VIP ASIGNADOS EN LA PLATAFORMA DE MOVILIDAD
			
			echo "<div id='servicios_vip'>
			</div>
			<iframe name='Oculto_servicios_vip' id='Oculto_servicios_vip' style='display:none' width='1' height='1'></iframe>
			<form action='https://www.aoasemuevecontigo.com/zgenerador_servicios_vr.php' target='Oculto_servicios_vip' method='POST' name='forma_svip' id='forma_svip'>
				<input type='hidden' name='Acc' value='buscar_servicios_siniestro'>
				<input type='hidden' name='id_siniestro' value='$S->id'>
			</form>";
			
			
			echo "<script language='javascript'>
			function Muestra_activar_citas_facturacion()
			{
				$Muestra_activar_citas_facturacion
			}
			</script>";
			/////  BUSQUEDA DE SOLICITUDES DE EXTRAS
			if($Extras=q("select * from solicitud_extra where siniestro=$S->id or siniestro_asignado=$S->id "))
			{
				echo "<table border cellspacing='0'><tr><th colspan='8'>SOLICITUDES DE EXTRAS</th></tr><tr>
					<th>Fecha Solicitud</th>
					<th>Solicitado por</th>
					<th>Justificacion</th>
					</tr>";
				while($Ext =mysql_fetch_object($Extras )) // pinta cada detalle de la solicitud de extra
				{
					echo "<tr>
						<td valign='top' nowrap='yes'>$Ext->fecha".($NTSE?" <a onclick='mod_extra($Ext->id);'>[+]</a> ":"")."</td>
						<td valign='top'>$Ext->solicitado_por</td>
						<td>".nl2br($Ext->justificacion)."</td></tr><tr>
						<td>Tipo $Ext->tipo</td>
						<td>Dias $Ext->dias</td>
						<td>".($Ext->anulado?"ANULADO":"")."Procesado por: $Ext->procesado_por Fecha Proceso: $Ext->fecha_proceso</td>
						</tr>";
				}
				echo "</table>";
			}
			echo "</td>
				</tr>";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
echo "<tr><th style='font-size:16px'> G A R A N T I A </th><th style='font-size:16px'> A D I C I O N A L </th></tr>";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			echo "<tr><td  bgcolor='ffffff' valign='top'>";
			// busca todos los regisros de garantias o autorizaciones
			if($Garantias=q("select a.id,a.nombre,a.valor,a.estado,f.nombre as nfr,a.metodo_devol,a.observaciones,a.funcionario,
											a.identificacion,a.fecha_devolucion,a.aut_fac,fecha_proceso
											FROM sin_autor a,franquisia_tarjeta f 
											where a.franquicia=f.id and a.siniestro=$S->id"))
			{
				echo "<table bgcolor='dddddd'><tr>
					<td bgcolor='eeeeee' align='center'><b>Franquicia</b></td>
					<td bgcolor='eeeeee' align='center'><b>Responsable de la Garant&iacute;a</b></td>
					<td bgcolor='eeeeee' align='center'><b>Valor</b></td>
					<td bgcolor='eeeeee' align='center'><b>Estado</b></td>
					<td bgcolor='eeeeee' align='center'><b>Fec.Proceso</b></td>
					<td bgcolor='eeeeee' align='center'><b>Documento</b></td>
					<td bgcolor='eeeeee' align='center'><b>ID</b></td>
				</tr>";
				while($G =mysql_fetch_object($Garantias )) // pinta los detalles de las garantias
				{
					if($G->aut_fac) $bgcolor='ddddff'; else $bgcolor='ffffff';
					if($G->estado=='E'){$Color_Estado='ffffdd';$Label_Estado='En espera';}
					elseif($G->estado=='A'){$Color_Estado='ddffdd';$Label_Estado='Autorizado';}
					elseif($G->estado=='R'){$Color_Estado='ffdddd';$Label_Estado='Rechazado';}
					elseif($G->estado=='O'){$Color_Estado='6BBEDD';$Label_Estado='Abono';}
					echo "<tr>
					<td bgcolor='$bgcolor'><b>$G->nfr</b></td>
					<td bgcolor='$bgcolor'>".$G->nombre.(($G->estado=='A'&&($USUARIO==26 or $USUARIO==1))?" <a onclick='mod_sin_autor_update($G->id);'>[+]</a>":"")."</td>
					<td bgcolor='$bgcolor' align='right'><b>".coma_format($G->valor)."</b></td>
					<td bgcolor='$Color_Estado'>$Label_Estado".($USUARIO==1?" <a onclick='mod_sin_autor($G->id);'>[+]</a>":"").((($USUARIO==26 or $USUARIO==1)&&$G->estado=='E')?" <a onclick='modificar_sinautor($G->id);'>[*]</a>":"")."</td>
					<td bgcolor='$bgcolor' align='center'>$G->fecha_proceso</td>
					<td bgcolor='$bgcolor'>";
					// busca si hay un recibo de caja asociado a la garantia y lo muestra
					if($Rc=qo("select rc.id,rc.consecutivo,o.sigla,rc.anulado from recibo_caja rc,oficina o where o.id=rc.oficina and rc.autorizacion=$G->id"))
					{
						echo "RC: ".($Rc->anulado?"<strike style='color:aa0000'>":"")."$Rc->sigla $Rc->consecutivo".($Rc->anulado?"</strike>":"").
								" <a onclick='imprime_rc($Rc->id);'>[-]</a> ".
								($USUARIO==1?" <a onclick='mod_recibo_caja($Rc->id)'>[+]</a>":"") ;
					}
					echo "</td><td bgcolor='$bgcolor'>$G->id</td></tr>";
					// pinta el estado de devolucion
					if($G->estado=='A') echo "<tr><td bgcolor='$bgcolor' align='right'>Metodo de devoluci&oacute;n:</td><td bgcolor='$bgcolor'>".
							($G->metodo_devol?$G->metodo_devol:"<span style='color:992222'>No ha sido devuelto</span>")."</td>
							<td bgcolor='$bgcolor' colspan=2>Fecha y Hora:</td><td colspan='3' bgcolor='$bgcolor'>".
							($G->fecha_devolucion!='0000-00-00 00:00:00'?$G->fecha_devolucion:"<span style='color:992222'>No ha sido devuelto</span>")."</td></tr>";
					// pinta los datos de contacto del cliente por si hay que llamarlo
					if($Cliente=qo("select * from cliente where identificacion='$G->identificacion' "))
					{
						echo "<tr><td bgcolor='$bgcolor' colspan=7>
							<table cellspacing='1' bgcolor='dddddd' width='100%'>
								<tr><td align='right' bgcolor='$bgcolor'>Telefonos</td><td bgcolor='$bgcolor'><b>$Cliente->telefono_oficina $Cliente->telefono_casa $Cliente->celular</b></td></tr>
								<tr><td align='right' bgcolor='$bgcolor'>Correo</td><td bgcolor='$bgcolor'><b>$Cliente->email_e</b></td></tr>
								<tr><td align='right' bgcolor='$bgcolor'>Observacion</td><td bgcolor='$bgcolor'><b>$G->observaciones</b></td></tr>
								<tr><td align='right' bgcolor='$bgcolor'>Funcionario</td><td bgcolor='$bgcolor'><b>$G->funcionario</b></td></tr>
							</table>
						</td></tr>"; 
					}
				}
				echo "</table>";
			}
			echo "</td>
				<td  bgcolor='ffffff' valign='top'>";
			//// BUSQUEDA DE SOLICITUDES DE FACTURACION ////
				// verificacion si el usuario esta en el perfil de facturacion  -------------------------------------------------------------------------
				$Puede_facturar=false;
				if($idf=qo1("select id from usuario_facturacion where usuario='$NickUsuario' ")) $Puede_facturar=true;
				// ---------------------------------------------------------------------------------------------------------------------------------------------------------
				echo "<table border cellspacing='0'><tr><th colspan=8>SOLICITUDES DE FACTURACION</th></tr><tr>
				<th>ID</th>
				<th>SOLICITADO POR</th>
				<th>CONCEPTO</th>
				<th>FECHA SOLICITUD</th>
				<th>DESCRIPCION</th>
				<th>VALOR</th>
				<th>PROCESADO_POR</th>
				<th>FECHA PROCESO</th>
				</tr>";
				if($Solicitudes_fac=q("select s.id,s.solicitado_por,c.nombre,s.fecha_solicitud,s.descripcion,s.valor,s.procesado_por,s.fecha_proceso
										FROM solicitud_factura s,concepto_fac c
										WHERE s.concepto=c.id and s.siniestro=$S->id
										ORDER BY fecha_solicitud"))
			{
				
				while($SF =mysql_fetch_object($Solicitudes_fac )) // solicitud por solicitud pinta los detalles
				{
					echo "<tr>
					<td>$SF->id ".($NTSF?" <a onclick='mod_solicitud_factura($SF->id)'>[+]</a>":"")."</td>
					<td>$SF->solicitado_por</td>
					<td>$SF->nombre</td>
					<td>$SF->fecha_solicitud</td>
					<td>$SF->descripcion</td>
					<td align='right'>".coma_format($SF->valor)."</td>
					<td align='center'>";
					if($SF->procesado_por!='') {
                                            echo "$SF->procesado_por";
                                        }
					else 
					{
						if($Puede_facturar)
						{
							echo "<input type='checkbox' onchange='marcar_facturacion($SF->id,this.checked);'> ";
						}
					}
					echo "</td>
					<td>$SF->fecha_proceso</td>
					
					</tr>";
				}
			}
				
				echo "</table>";
				echo "<div id='div_facturar' style='display:none'>
					<br><center>
					<input type='button' value='  FACTURAR CONCEPTOS MARCADOS  ' onclick='facturar_conceptos()'>
					</center></br>
				</div>";
			
			////  BUSQUEDA DE FACTURAS CON SUS RESPECTIVOS PAGOS /////
			echo "<table bgcolor='dddddd' width='100%'><tr><td colspan='5' bgcolor='eeeeee' align='center'><b>FACTURAS</b></tr><tr>
					<td bgcolor='eeeeee' align='center'><b>Numero</b></td>
					<td bgcolor='eeeeee' align='center'><b>Identificacion</b></td>
					<td bgcolor='eeeeee' align='center'><b>Fecha</b></td>
					<td bgcolor='eeeeee' align='center'><b>Valor</b></td>
					<td bgcolor='eeeeee' align='center'><b>Pagos</b></td>
					<td bgcolor='eeeeee' align='center'><b>Aviso</b></td>
					</tr>";
			if($Facturas=q("SELECT factura.*,cliente.identificacion FROM factura INNER JOIN cliente ON factura.cliente=cliente.id WHERE siniestro=$S->id"))
			{
				
				while($Fa =mysql_fetch_object($Facturas )) // pinta los detalles de cada factura
				{
					echo "<tr>
					<td bgcolor='ffffff' valign='top' >".($Fa->anulada?"<strike style='color:aa0000'>":"")."$Fa->consecutivo".($Fa->anulada?"</strike>":"")." <a onclick='imprime_fac($Fa->id);'>[+]</a></td>
					<td bgcolor='ffffff' valign='top'>$Fa->identificacion</td>
					<td bgcolor='ffffff' valign='top'>$Fa->fecha_emision</td>
					<td bgcolor='ffffff' align='right' valign='top'>".coma_format($Fa->total)."</td>
					<td bgcolor='ffffff'>";
					// en caso de encontrar recibos de caja para cada factura pinta la informacion 
					if($Recibos=q("select rc.id,rc.consecutivo,rc.fecha,rc.valor,o.sigla,rc.consignacion_f from recibo_caja  rc,oficina o where o.id=rc.oficina and rc.factura=$Fa->id"))
					{
						echo "<table bgcolor='eeeeff' width='100%'><tr><td bgcolor='ddddff' colspan='4' align='center'><b>Recibos de Caja</b></td></tr>
						<tr><td bgcolor='ddddff' align='center'><b>Consecutivo</b></td><td bgcolor='ddddff' align='center'><b>Fecha</b></td>
						<td bgcolor='ddddff' align='center'><b>Valor</b></td><td bgcolor='ddddff' align='center'><b>Imagen</b></td></tr>";
						while($Rc =mysql_fetch_object($Recibos )) // pinta la informacion de cada recibo de caja
						{
							echo "<tr>
							<td bgcolor='ddddff'>$Rc->sigla $Rc->consecutivo ".
								($USUARIO==1?" <a onclick='mod_recibo_caja($Rc->id)'>[+]</a>":"")."</td>
							<td bgcolor='ddddff'>$Rc->fecha</td>
							<td bgcolor='ddddff' align='right'>".coma_format($Rc->valor)."</td>
							<td bgcolor='ddddff' align='center'>".($Rc->consignacion_f?"<a onclick=\"modal('$Rc->consignacion_f',10,10,600,600,'img01');\"><img src='$Rc->consignacion_f' border='0' height='16px'></a>":"")."</td>
							</tr>";
						}
						echo "</table>";
					}
					// busca si hay notas credito asociadas a la factura
					if($Notas_credito=q("select ncr.id,ncr.consecutivo,ncr.fecha,ncr.total from nota_credito ncr where ncr.factura=$Fa->id"))
					{
						echo "<table bgcolor='dddddd' width='100%'><tr><td bgcolor='eeeeee' colspan='3' align='center'><b>Notas Cr&eacute;dito</b></td></tr>
						<tr><td bgcolor='eeeeee' align='center'><b>Consecutivo</b></td><td bgcolor='eeeeee' align='center'><b>Fecha</b></td>
						<td bgcolor='eeeeee' align='center'><b>Valor</b></td></tr>";
						while($Ncr =mysql_fetch_object($Notas_credito )) // pinta la informacion de cada nota cr&eacute;dito
						{
							echo "<tr>
							<td bgcolor='ffffff'>".($Nco->anulado?"<strike style='color:aa0000'>":"")."$Ncr->consecutivo ".($Nco->anulado?"</strike>":"").
								($USUARIO==1?" <a onclick='mod_nota_credito($Ncr->id)'>[+]</a>":"")."</td>
							<td bgcolor='ffffff'>$Ncr->fecha</td>
							<td bgcolor='ffffff' align='right'>".coma_format($Ncr->total)."</td>
							</tr>";
						}
						echo "</table>";
					}
					// busca si hay notas contables asociadas a la factura
					if($Notas_contable=q("select nco.id,nco.consecutivo,nco.fecha,nco.valor,nco.anulado from nota_contable nco where nco.factura=$Fa->id"))
					{
						echo "<table bgcolor='dddddd' width='100%'><tr><td bgcolor='eeeeee' colspan='3' align='center'><b>Notas Contables</b></td></tr>
						<tr><td bgcolor='eeeeee' align='center'><b>Consecutivo</b></td><td bgcolor='eeeeee' align='center'><b>Fecha</b></td>
						<td bgcolor='eeeeee' align='center'><b>Valor</b></td></tr>";
						while($Nco =mysql_fetch_object($Notas_contable )) // pinta la informacion de cada nota contable
						{
							echo "<tr>
							<td bgcolor='ffffff'>".($Nco->anulado?"<strike style='color:aa0000'>":"")."$Nco->consecutivo ".($Nco->anulado?"</strike>":"").
								($USUARIO==1?" <a onclick='mod_nota_contable($Nco->id)'>[+]</a>":"")."
								<a onclick='imprime_nota_contable($Nco->id)'>[+]</a></td>
							<td bgcolor='ffffff'>$Nco->fecha</td>
							<td bgcolor='ffffff' align='right'>".coma_format($Nco->valor)."</td>
							</tr>";
						}
						echo "</table>";
					}
					echo "</td>
					<td  bgcolor='ffffff'><button onClick='correoenviarfactura($Fa->id)' id='consecutivfac$Fa->id' value='$Fa->consecutivo'>nuevocorreo</button></td>

					</tr>";
				}
				
			}			
			$resultado = q("SELECT * FROM factura_masiva a, factura b, cliente c WHERE a.id_factura = b.id and b.cliente=c.id and a.siniestro =$S->id ");
				while($row = mysql_fetch_object($resultado)){
					echo "<tr>
					<td bgcolor='E5A669' title='Factura Masiva'>".$row->factura." <a onclick='imprime_fac($row->id_factura);'>[+]</a></td>
					<td bgcolor='ffffff'>$row->identificacion</td>
					<td bgcolor='ffffff'>$row->fecha_emision</td>
					<td bgcolor='ffffff'>$row->total</td><td bgcolor='ffffff'>";
					// en caso de encontrar recibos de caja para cada factura pinta la informacion 
					if($Recibos=q("select rc.id,rc.consecutivo,rc.fecha,rc.valor,o.sigla,rc.consignacion_f from recibo_caja  rc,oficina o where o.id=rc.oficina and rc.factura=$row->id_factura"))
					{
						echo "<table bgcolor='eeeeff' width='100%'><tr><td bgcolor='ddddff' colspan='4' align='center'><b>Recibos de Caja</b></td></tr>
						<tr><td bgcolor='ddddff' align='center'><b>Consecutivo</b></td><td bgcolor='ddddff' align='center'><b>Fecha</b></td>
						<td bgcolor='ddddff' align='center'><b>Valor</b></td><td bgcolor='ddddff' align='center'><b>Imagen</b></td></tr>";
						while($Rc =mysql_fetch_object($Recibos )) // pinta la informacion de cada recibo de caja
						{
							echo "<tr>
							<td bgcolor='ddddff'>$Rc->sigla $Rc->consecutivo ".
								($USUARIO==1?" <a onclick='mod_recibo_caja($Rc->id)'>[+]</a>":"")."</td>
							<td bgcolor='ddddff'>$Rc->fecha</td>
							<td bgcolor='ddddff' align='right'>".coma_format($Rc->valor)."</td>
							<td bgcolor='ddddff' align='center'>".($Rc->consignacion_f?"<a onclick=\"modal('$Rc->consignacion_f',10,10,600,600,'img01');\"><img src='$Rc->consignacion_f' border='0' height='16px'></a>":"")."</td>
							</tr>";
						}
						echo "</table>";
					}

					echo "<td bgcolor='ffffff'><button onclick='correoenviarfactura($row->factura)' id='consecutivfac$row->factura' value='$row->factura'>nuevocorreo</button></td>";
				}
				echo "</table>";
			////////////////////////	   COLAS DE PROCESO          //////////////////////////////////////
			echo "<b>Colas de proceso</b><br>";
			if($C1=qo("select * from call2cola1 where siniestro=$S->id")) // cola 1  cuando el siniestro es nuevo dentro del call center y no ha sido procesado
				echo "Cola1: $C1->fecha<br>";
			$Estados_p=tabla2arreglo('estado_preadj',array('id','nombre'));
			if($C2q=q("select * from call2cola2 c where c.siniestro=$S->id")) // cola 2 cuando hay envios de pre-adjudicacion
			while($C2=mysql_fetch_object($C2q))
				echo "Cola2: $C2->fecha Codigo Unico: <b>-$C2->codigo-</b> ".($C2->descargado!='0000-00-00 00:00:00'?"Contrato descargado: <b>$C2->descargado</b> ":"").
				" Aceptacion:".($C2->aceptado?"<b>SI</b> Fecha aceptacion: $C2->fecha_aceptacion ".($C2->ip?" <a onclick=\"modal('https://en.utrace.de/?query=$C2->ip',0,0,700,1000,'ubicacion');\">IP $C2->ip</a> ":"").$Estados_p[$C2->estado]:"<b>NO</b>")." <br>";
			if($C3=qo("select * from call2cola3 where siniestro=$S->id")) // cola 3 casos especiales enviados a los coordinadores de call center
				echo "Cola3: $C3->fecha<br>";
			if($C4=qo("select * from call2cola4 where siniestro=$S->id")) // cola 4 casos especiales enviados a los coordinadores de call center
				echo "Cola3: $C4->fecha<br>";
			// busca en las colas de proceso para saber que agente de callcenter ha tenido contacto con el caso
			if($Cp=q("select c.*,u.nombre as nagente,timediff(c.fecha_cierre,c.fecha) as dif ,
				case c.estado when 'A' then 'Abierto' when 'C' then 'Cerrado' end as nestado
				from call2proceso c,usuario_callcenter u 
				where c.siniestro=$S->id and c.agente=u.id "))
			{
				echo "<table border cellspacing='0'><tr>
					<th>Agente</th>
					<th>Apertura</th>
					<th>Estado</th>
					<th>Cierre</th>
					<th>Tiempo</th>
					
					</tr>";
				while($cp =mysql_fetch_object($Cp )) // pinta cada registro de la cola de proceso de call center
				{
					echo "<tr>
					<td>$cp->nagente</td>
					<td>$cp->fecha</td>
					<td>$cp->nestado</td>
					<td>$cp->fecha_cierre</td>
					<td>$cp->dif</td>
					</tr>";
				}
				echo "</table>";
			}
			echo "<br><a onclick='correoenviar()'>Nuevo Correo</a><br>";
			if($Cs=q("SELECT a.id,a.asunto,a.fecha_envio,a.usuario, a.siniestro FROM correo a WHERE  a.siniestro=$S->id UNION
			SELECT a.id,a.asunto,a.fecha_envio,a.usuario, a.siniestro FROM correo a WHERE  a.siniestro=$S->id"))
			{
				echo "
				<h4>Correos enviados
				<table border cellspacing='0'><tr>
					<th>Asunto</th>
					<th>Siniestro</th>
					<th>Usuario</th>
					<th>Fecha envio</th>
					<th>Ver</th>
					</tr>";
				while($cs =mysql_fetch_object($Cs )) // pinta cada registro de la cola de proceso de call center
				{
					// (q("select * from usuario_callcenter where id=$cs->usuario")
					// q("select * from usuario_desarrollo where id=$cs->usuario")
					
					echo "<tr>
					<td>$cs->asunto</td>
					<td>$cs->siniestro</td>
					<td>$cs->usuario</td>
					<td>$cs->fecha_envio</td>
					<td><a onclick='modalcorreo($cs->id);'>[+]</a></td>
					</tr>";
					
					
				}
				echo "</table>";
			}
			if($Cs=q("SELECT * FROM seguimiento WHERE tipo=35 AND siniestro=$S->id"))
			{
				echo "
				<h4>Sms enviados factura proxima a vencer
				<table border cellspacing='0' ><tr>
					<th>fecha</th>
					<th>hora</th>
					<th>descripcion</th>
					</tr>";
				while($cs =mysql_fetch_object($Cs )) // pinta cada registro de la cola de proceso de call center
				{
					echo "<tr>
					<td>$cs->fecha</td>
					<td>$cs->hora</td>
					<td onclick=\"modal('zsiniestro.php?Acc=ver_seguimiento_sms&idsms=$cs->id',0,0,600,800);\">".substr($cs->descripcion,0,50)."</td>
					</tr>";
				}
				echo "</table>";
			}
			if($Ce=q("select * from call2infoerronea where siniestro =$S->id")) // busca registros de envio de informacion erronea a la aseguradora
			{
				echo "<br><b>Cola Informacion Erronea</b>
					<table border cellspacing='0'><tr>
						<th>Agente</th>
						<th>Fecha</th>
						<th>Envio</th>
						<th>Enviado por</th>
						<th>Fecha Proceso</th>
						<th>Procesado por</th>
						</tr>";
				while($ce =mysql_fetch_object($Ce )) // pinta cada registro de envio a la aseguradora
				{
					echo "<tr>
					<td>$ce->marcado_por</td>
					<td>$ce->fecha</td>
					<td>$ce->fecha_envio</td>
					<td>$ce->enviado_por</td>
					<td>$ce->fecha_proceso</td>
					<td>$ce->procesado_por</td>
					</tr>";
				}
				echo "</table>";
			}
			////////////////////   SOLICITUDES DE VISUALIZAICON DE GARANTIAS   /////////////////////////
			if($SV=q("select * from solicitud_dataautor where siniestro=$S->id"))
			{
				echo "<br><b>SOLICITUDES DE VISUALIZACION DE GARANTIAS</b>
					<table><tr>
					<th>Solicitado por</th>
					<th>Fec.Solicitud</th>
					<th>Motivo</th>
					<th>Autorizado por</th>
					<th>Fec.Aprobacion</th>
					<th>Fec.Visualizacion</th>
					</tr>";
				while($Sv=mysql_fetch_object($SV)) // pinta cada registro de solicitudes de visualizacion de la garant&iacute;a
				{
					echo "<tr>
						<td>$Sv->solicitado_por ".($USUARIO==1?" <a onclick='mod_solicitud_data($Sv->id)'>[+]</a>":"")."</td>
						<td>$Sv->fecha_solicitud</td>
						<td>$Sv->motivo</td>
						<td>$Sv->autorizado_por</td>
						<td>$Sv->fecha_aprobacion</td>
						<td>$Sv->fecha_visualizacion</td>
						</tr>";
				}
				echo "</table>";
			}
			echo "</td></tr>
				</table>
				<script language='javascript'>
				//document.forma_svip.submit();
				parent.document.forma.consultar.style.visibility='visible';</script>
				</body>";
		}
	}
	else
	{
		html();
		echo "<body style='font-size:16px'><h3>Siniestro: $siniestro</h3><b style='color:ff5555'>NO SE ENCUENTRA NINGUNA COINCIDENCIA</b> Vuelva a intentarlo.
		<script language='javascript'>parent.document.forma.consultar.style.visibility='visible';</script></body>";
	}
}
function updateSinautor($id)
{
	global $id;
	$Sin_autor=qo("select * from sin_autor where id =$id");
	
	echo"
	<style>
	input[type=text]#fname,input[type=text]#lname{
		width: 100%;
		padding: 12px 20px;
		margin: 8px 0;
		display: inline-block;
		border: 1px solid #ccc;
		border-radius: 4px;
		box-sizing: border-box;
	  }
	  
	  #button {
		width: 100%;
		background-color: #4CAF50;
		color: white;
		padding: 14px 20px;
		margin: 8px 0;
		border: none;
		border-radius: 4px;
		cursor: pointer;
	  }
	  
	  #button:hover {
		background-color: #45a049;
	  }
	  
	  #div {
		border-radius: 5px;
		background-color: #f2f2f2;
		padding: 20px;
	  }
	  #text-center-update{
		  text-align:center;
	  }
	</style>
	<div id='div'>
	<h2 id='text-center-update'>Actualizar Responsable de la Garantia</h2>
	
	<form method='post'>
	  <label for='fname'>Nombre</label>
	  <input type='text' id='fname' name='nombre' placeholder='Nombre' value='$Sin_autor->nombre'>
  
	  <label for='lname'>Identificacion</label>
	  <input type='text' id='lname' name='identificacion' placeholder='identificacion' value='$Sin_autor->identificacion'>	
	  <input type='submit' id='button' value='Actualizar'>
	</form>
	
  </div>";
  if(isset($_POST['identificacion'])){
	if($haycliente=q("select * from cliente where identificacion=".$_POST['identificacion'])){
		$update=q("update sin_autor set nombre='".$_POST['nombre']."', identificacion='".$_POST['identificacion']."' where id=$id");
		echo "<script>
		window.close();

	  </script>";
	}
	else {
		$insertcliente=q("insert into cliente (nombre,identificacion) values ('".$_POST['nombre']."',".$_POST['identificacion'].")");
		$update=q("update sin_autor set nombre='".$_POST['nombre']."', identificacion='".$_POST['identificacion']."' where id=$id");
		echo "<script>
		window.close();

	  </script>";
	}
  }
}
function modificarSinautor($id)
{
	global $id;
	$Sin_autor=qo("select *,franquisia_tarjeta.nombre AS franquicianombre from sin_autor LEFT JOIN franquisia_tarjeta ON franquisia_tarjeta.id= sin_autor.franquicia where sin_autor.id =$id");
	
	echo"
	<style>
	#estadosinautor,#lname{
		width: 100%;
		padding: 12px 20px;
		margin: 8px 0;
		display: inline-block;
		border: 1px solid #ccc;
		border-radius: 4px;
		box-sizing: border-box;
	  }
	  
	  #button {
		width: 100%;
		background-color: #4CAF50;
		color: white;
		padding: 14px 20px;
		margin: 8px 0;
		border: none;
		border-radius: 4px;
		cursor: pointer;
	  }
	  
	  #button:hover {
		background-color: #45a049;
	  }
	  
	  #div {
		border-radius: 5px;
		background-color: #f2f2f2;
		padding: 20px;
	  }
	  #text-center-update{
		  text-align:center;
	  }
	</style>
	<div id='div'>
	<h2 id='text-center-update'>Actualizar Responsable de la Garantia</h2>
	
	<form method='post'>
	  <label for='fname'>Siniestro</label>
	  <input id='lname' value='$Sin_autor->siniestro' readonly style='background-color: #F0F0F0'>
	  <br>

	  <label for='fname'>Nombre</label>
	  <input id='lname' value='$Sin_autor->nombre' readonly style='background-color: #F0F0F0'>
	  <br>

	  <label for='fname'>Identificacion</label>
	  <input id='lname' value='$Sin_autor->identificacion' readonly style='background-color: #F0F0F0'>
	  <br>

	  <label for='fname'>Franquicia</label>
	  <input id='lname' value='$Sin_autor->franquicianombre' readonly style='background-color: #F0F0F0'>
	  <br>

	  <label for='fname'>Funcionario</label>
	  <input id='lname' value='$Sin_autor->funcionario' readonly style='background-color: #F0F0F0'>
	  <br>

	  <label for='fname'>Fecha Solicitud</label>
	  <input id='lname' value='$Sin_autor->fecha_solicitud' readonly style='background-color: #F0F0F0'>
	  <br>

	  <label for='fname'>Fecha proceso</label>
	  <input id='lname' value='$Sin_autor->fecha_proceso' readonly style='background-color: #F0F0F0'>
	  <br>

	  <label for='fname'>Solicitado por</label>
	  <input id='lname' value='$Sin_autor->solicitado_por' readonly style='background-color: #F0F0F0'>
	  <br>
  
	  <label for='lname'>Estado</label>
	  <select name='estadosinautorcambio' id='estadosinautor'>";
		echo "<option value='E' ".($Sin_autor->estado=='E'?'selected':'')."> EN ESPERA </option>";
		echo "<option value='R' ".($Sin_autor->estado=='R'?'selected':'')."> RECHAZADO</option>";
		echo "<option value='A' ".($Sin_autor->estado=='A'?'selected':'')."> ACEPTADO </option>";
	  echo"</select>
	  <input type='submit' id='button' value='Actualizar'>
	</form>
	
  </div>";
  if(isset($_POST['estadosinautorcambio'])){
	  if ($_POST['estadosinautorcambio']==$Sin_autor->estado) {
		$seguimiento = q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($Sin_autor->siniestro,'".date('Y-m-d')."','".date('H:m:s')."','".$_SESSION['Nombre']."','Consulta garantia y no hace ningun cambio',2)");
		  echo "<script>
		window.close();
		";
	  }
	  else {
		  $update=q("update sin_autor set estado='".$_POST['estadosinautorcambio']."' where id=$id");
		$seguimiento = q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($Sin_autor->siniestro,'".date('Y-m-d')."','".date('H:m:s')."','".$_SESSION['Nombre']."','Cambio de estado de la garantia',41)");
		echo "<script>
		window.close();

	  </script>";
	  }
  }
}
function ver_seguimiento_sms()
{
	global $S;
	$S=qo("select * from seguimiento where id=".$_GET['idsms']."");
	html();
	echo "<body style='font-size:16px'><h3>Siniestro: ".$S->siniestro."</h3>
	<table border cellspacing='0'><tr>
		<th>fecha</th>
		<th>hora</th>
		<th>descripcion</th>
		</tr>";
		
			// (q("select * from usuario_callcenter where id=$cs->usuario")
			// q("select * from usuario_desarrollo where id=$cs->usuario")
			
			echo "<tr>
			<td>$S->fecha</td>
			<td>$S->hora</td>
			<td>$S->descripcion</td>
			</tr>";
			
			
		
		echo "</table>";
}
function ver_seguimiento() // funcion que permite ver el seguimiento en forma de matriz.
{
	global $id,$Historico;
	html('SEGUIMIENTO'); // pinta cabeceras html
	$NTS=tu('seguimiento','id');
	if($Historico) $D=qo("select * from siniestro_hst where id=$id"); else $D=qo("select * from siniestro where id=$id"); // trae los datos del siniestro
	echo "<script language='javascript'>
	function modifica_seg(id)	{modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTS&id='+id,0,0,600,800);}</script>
	<body><h3>Seguimiento de $D->numero</h3>";
	// busca los registros de seguimiento correspondientes al siniestro
	$sql = "select s.*,t.nombre as ntipo,t_tipifica_seguimiento(tipificacion) as ntipi 
		from seguimiento s,tipo_seguimiento t where s.siniestro=$id and s.tipo=t.id 
		union
		select s.*,t.nombre as ntipo,t_tipifica_seguimiento(tipificacion) as ntipi 
		from seguimiento_hst s,tipo_seguimiento t where s.siniestro=$id and s.tipo=t.id 
		order by fecha,hora";
	//echo $sql;
	$Seg=q($sql);
		
	if($Seg) // si encuentra registros de seguimiento los muestra
	{
		echo "<table width='100%'><tr>
		<th>Fecha - Hora</th>
		<th>Usuario</th>
		<th>Descripcion</th>
		<th>Tipo</th>
		<th>Tipificacion</th>
		<th>Fecha Compromiso</th>
		<th>Comp. Cumplido</th>
		</tr>";
		$bg='ffffff';
		while($S=mysql_fetch_object($Seg)) // pinta registro por registro todos los seguimientos
		{
			$bg=($bg=='ffffff'?'dddddd':'ffffff');
			echo "<tr bgcolor='$bg' ".($NTS?" ondblclick='modifica_seg($S->id);' ":"")."><td nowrap='yes' valign='top'>$S->fecha $S->hora</td>
			<td nowrap='yes' valign='top'>$S->usuario</td>
			<td>$S->descripcion</td>
			<td nowrap='yes' valign='top'>$S->ntipo</td>
			<td nowrap='yes' valign='top'>$S->ntipi</td>
			<td nowrap='yes' valign='top'>".(inlist($S->tipo,'16,21')?$S->fecha_compromiso:"")."</td>
			<td nowrap='yes' valign='top'>".($S->cumplido?"SI":"")."</td>
		</tr>";
		}
		echo "</table>";
	}
	else
	echo "<b>No se encuentra seguimiento del siniestro $D->numero</b>";
	echo "</body>";
}

function ver_observaciones() // muestra en una sola vista el campo de observaciones el cual es un resumen de los seguimientos
{
	global $id;
	html('OBSERVACIONES');
	$D=qo("select numero,observaciones,obsconclusion from siniestro where id=$id");
	echo "<body><h3>Seguimiento de $D->numero</h3><br><br>";
	echo nl2br($D->observaciones);
	echo "<hr>";
	echo nl2br($D->obsconclusion);
	
	echo "</body>";
}

function insertarObservacion(){
	global $id;
	echo "
	<script>
	function adicionar_observaciones()
					{ var Obs=document.getElementById('nuevas_observaciones').value;
					
						window.open('zsiniestro.php?Acc=adicionar_observaciones&id=$id&d='+Obs,'Oculto_presentar_caso');
						
						} 
	</script>	
					";
					
	echo "
	<style>
	
	.flex{
	display: flex;
    align-items: center;
	}
	.flex2{
		
	}
	</style>
	<div class='flex'>Adicionar Observaciones: <textarea class='estilotextarea3' cols='40' rows='4' id='nuevas_observaciones' ></textarea><br> </div>
	<div class='flex2'>
	<input type='button' value=' GUARDAR ' style='font-size:14px;font-weight:bold' onclick='adicionar_observaciones();'>
	</div>
	";
}
function adicionar_observaciones()
{
	global $id,$USUARIO,$NUSUARIO,$d;
	$H1=date('Y-m-d'); $H2=date('H:i:s');$Ahora=$H1.' '.$H2;
	q("update siniestro set observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]:$d') where id=$id");
	$Idn=q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'$H1','$H2','$NUSUARIO','$d',7)"); // 7: observacion general
	graba_bitacora('siniestro','M',$id,'Observaciones');
	//graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	echo "<body><script language='javascript'>close();</script></body>";
}


function recibir_servicios_vip()
{
	sesion(); // verifica la sesion del usuario
	include('inc/gpos.php');
	echo "<body><script language='javascript'>
			parent.document.getElementById('servicios_vip').innerHTML=\"$contenido\";
		</script></body>";
}
