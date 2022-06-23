<?php

/**
 *    SOFTWARE PARA CONTROL DE CUSTODIA, DEVOLUCION Y ESTADO DE GARANTIAS
 *
 * @version $Id$
 * @copyright 2011
 
 */
include('inc/funciones_.php');
//include("inc/smtp2/class.phpmailer.php");

sesion();
$AGarantia=array();$ACitas=array();
$USUARIO=$_SESSION['User'];
$NUSUARIO=$_SESSION['Nombre'];
if(!$FECHAI)  $FECHAI=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-10)));
if(!$FECHAF)  $FECHAF=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-10)));

if($USUARIO==10 /* operario de oficina  */)
{
	$OFIU=qo1("select oficina from usuario_oficina where id=".$_SESSION['Id_alterno']); // OBTIENE la oficina a la que pertenece el funcionario
	$OFIU=qo1("select ciudad from oficina where id=$OFIU"); // obtiene la ciudad 
}
if($USUARIO==32 /*  recepcion */)
{
	$OFIU=qo1("select oficina from usuario_recepcion where id=".$_SESSION['Id_alterno']); // obtiene la oficina a la que pertenece el funcionario
	$OFIU=qo1("select ciudad from oficina where id=$OFIU"); // obtiene la ciudad
}
if($USUARIO==5 || $USUARIO==1) $INSCRIBE_CUENTAS=qo1("select inscribe_garantias from usuario_autorizacion where id=".$_SESSION['Id_alterno']); else $INSCRIBE_CUENTAS=0;
if (!empty($Acc) && function_exists($Acc)){	eval($Acc . '();');	die();}

html('CONTROL DE CUSTODIA DE GARANTIAS');
$Propiets='';
if( $Propietarios=q("select distinct id_propietario from vehiculo where id_propietario!=0"))
{
	while($Prop=mysql_fetch_object($Propietarios))
	{
		$Propiets.=($Propiets?", ":"").$Prop->id_propietario;
	}
}
q("update sin_autor,recibo_caja set sin_autor.aut_fac=1 where sin_autor.id=recibo_caja.autorizacion and recibo_caja.garantia=0");
echo "<script language='javascript'>
		function ajustar_tablero()
		{
			document.getElementById('Tablero_garantia').style.height=document.body.clientHeight-100;
		}
		function recargar_tablero()
		{
			document.forma.submit();
		}

	</script>
	<body onload='ajustar_tablero();' onresize='ajustar_tablero();'>
	<h3>AOA - CONTROL DE CUSTODIA DE GARANTIAS  <span id='atransferir' style='color:005500'></span></H3>
	<a onclick=\"modal('zanalisis_comparendos.php',0,0,600,600,'acomp');\" style='cursor:pointer;'>Verificaci�n Comparendos </a> 
	Nits Propietarios: <b>900174552-5, 860059294-3 ,860002964-4, 890903938-8, 890300279-4</b> Se debe buscar en las bases de datos de comparendos con y sin d�gito de verificaci�n.
	<form action='zcontrol_custodia_garantia.php' method='post' target='Tablero_garantia' name='forma' id='forma'>
		Fecha inicial : ".pinta_FC('forma','FECHAI',$FECHAI)." Fecha Final: ".pinta_fc('forma','FECHAF',$FECHAF);
	if(!inlist($USUARIO,10,32))
		echo " Oficina: ".menu1("OFIU","select ciudad,nombre from oficina ",$OFIU,1);
	echo " Ver solo <select name='FILTRO'><option value=''>Todos</option><option value='E'>Efectivos  TD</option><option value='C'>Tarjetas Cr�dito</option><option value='P'>Pagares</option></select>
		Obtener resultado en excel: <input type='checkbox' name='Excel'>
		  <input type='button' id='consultar' value='CONTINUAR' onclick='recargar_tablero();'>
		<input type='hidden' name='Acc' value='consultar_garantias'>
	</form>
	<iframe name='Tablero_garantia' id='Tablero_garantia' height='400' width='100%' frameborder='no' scrolling='auto'></iframe>
	</body>";

function consultar_garantias()
{
	global $FECHAI,$FECHAF,$USUARIO,$NUSUARIO,$OFIU,$FILTRO,$INSCRIBE_CUENTAS,$Excel,$IDsiniestro;
	$Excel=sino($Excel);
	if($Excel)
	{
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=transferencias_$Hoy.xls");
	}
	else
		html("Control de Garantias");
	echo "<script language='javascript'>
		var Arr_transf= new Array();
		var Arr_inscritas= new Array();
		var Monto_transf=0;
			function incluye_transferencia(id,valor,dato)
			{
				if(dato==true)  // si esta maracdo para transferir
				{
					Arr_transf[Arr_transf.length]=id;Monto_transf+=valor;
					document.getElementById('btn_transferir').style.visibility='visible';
				}
				else
				{ // si desmarca, borra del arreglo el valor para no transferirlo
					for(var i=0;i<Arr_transf.length;i++)
					{
						if(Arr_transf[i]==id) { Arr_transf.splice(i,1);}
					}
					Monto_transf=Monto_transf-valor;
				}
				parent.document.getElementById('atransferir').innerHTML='Transferencias: '+Arr_transf.length+' Monto: '+Monto_transf;
			}
			function incluye_inscritas(id,dato)
			{
				if(dato==true)
				{
					Arr_inscritas[Arr_inscritas.length]=id;
					document.getElementById('btn_reporte_inscritas').style.visibility='visible';
				}
				else
				{
					for(var i=0;i<Arr_inscritas.length;i++)
					{
						if(Arr_inscritas[i]==id) { Arr_inscritas.splice(i,1);}
					}
				}
			}
			function genera_transferencia()
			{
				var Cadena='';
				for(var i=0;i<Arr_transf.length;i++) Cadena+=Arr_transf[i]+',';
				modal('zcontrol_custodia_garantia.php?Acc=lista_transferencia&d='+Cadena,0,0,500,800,'lt');
			}
			function genera_reporte_inscritas()
			{
				var Cadena='';
				for(var i=0;i<Arr_inscritas.length;i++)
				{
					Cadena+=Arr_inscritas[i]+',';
				}
				modal('zcontrol_custodia_garantia.php?Acc=lista_inscritas&d='+Cadena,0,0,500,800,'lt');
			}

			function subir_imagen(id) {modal('zcontrol_custodia_garantia.php?Acc=subir_imagen&id='+id,0,0,500,400,'si');}
			function ver_imagen(id) {modal('zcontrol_custodia_garantia.php?Acc=ver_imagen&id='+id,0,0,500,500,'si');}
			function subir_imagen2(id) {modal('zcontrol_custodia_garantia.php?Acc=subir_imagen2&id='+id,0,0,500,400,'si');}
			function ver_imagen2(id) {modal('zcontrol_custodia_garantia.php?Acc=ver_imagen2&id='+id,0,0,500,500,'si');}
			function actualiza_info(id) {modal('zautorizaciones.php?Acc=actualizar_info&idauto='+id,0,0,100,100,'auinfo');}
			function cambio_modo(id,dato) {if(confirm('Desea definir el m�todo de devoluci�n como '+dato+'?')){window.open('zcontrol_custodia_garantia.php?Acc=cambio_metodo_devolucion&id='+id+'&modo='+dato,'Oculto_garantia');	}}
			function enviar_mail_garantia(id) {window.open('zcontrol_custodia_garantia.php?Acc=enviar_email_garantia&id='+id,'Oculto_garantia');}
			function inserta_obs(id) {modal('zcontrol_custodia_garantia.php?Acc=inserta_obs&id='+id,0,0,100,100,'insobs');}
			function recepcion_garantia(id) {modal('zcontrol_custodia_garantia.php?Acc=recepcion_garantia&id='+id,0,0,200,400,'recepcion');}
			function registro_consignacion(id) {modal('zcontrol_custodia_garantia.php?Acc=registro_consignacion&id='+id,0,0,500,400,'recepcion');}
			function inscribir_cuenta(id) {modal('zcontrol_custodia_garantia.php?Acc=inscribir_cuenta&id='+id,0,0,300,450,'recepcion');}
			function ver_facturas(id) {modal('zcontrol_custodia_garantia.php?Acc=ver_facturas&id='+id,0,0,600,900,'facturas');}
			function marcar_comparendos(id) {modal('zcontrol_custodia_garantia.php?Acc=marcar_comparendos&id='+id,0,0,200,400,'recepcion');}
			function solicitar_factura(id) {modal('zcitas.php?Acc=solicitar_factura&cita='+id,0,0,400,500,'sfac');}
			function corregir_transferencia(id) {modal('zcontrol_custodia_garantia.php?Acc=corregir_transferencia&id='+id,0,0,400,600,'corregir');}
			function ver_recibo(id)	{modal('zcartera.php?Acc=imprimir_recibo&id='+id,100,100,700,800,'recibo')}
			function ver_foto(dato) {modal('../../Administrativo/'+dato,0,0,400,400,'foto');}
			function ver_siniestro(dato) { modal('zsiniestro.php?Acc=buscar_siniestro&id='+dato,0,0,800,800,'vs');}
			</script>
			<body >
			<h3 align='center'>CONTROL DE GARANTIAS - $FECHAI - $FECHAF
			<input type='button' name='btn_transferir' id='btn_transferir' onclick='genera_transferencia();' value=' TRANSFERIR ' style='visibility:hidden'>
			<input type='button' name='btn_reporte_inscritas' id='btn_reporte_inscritas' onclick='genera_reporte_inscritas();' value=' Reporte Inscripciones ' style='visibility:hidden'>
		</h3>";
	$Filtro_query='';
	if($FILTRO=='E') $Filtro_query=" and frq.tipo in ('E','D') ";
	if($FILTRO=='C') $Filtro_query=" and frq.tipo in ('C','G') ";
	if($FILTRO=='P') $Filtro_query=" and frq.tipo='P' ";

	$Consulta_garantias="select a.*,s.numero as nsin,s.placa,s.asegurado_nombre,s.asegurado_id,ase.nombre as naseguradora,ciu.nombre as nciudad,
								s.img_inv_salida_f as acta1,img_inv_entrada_f as acta2,frq.nombre as nfranq,s.fecha_final as fdevolvh,s.id as sid,a.id as aid,s.ubicacion
								FROM sin_autor a,siniestro s,franquisia_tarjeta frq,aseguradora ase,ciudad ciu
								WHERE a.siniestro=s.id and a.franquicia=frq.id  and s.fecha_final between '$FECHAI' and '$FECHAF' and a.estado='A'
								and a.aut_fac=0 and ase.id=s.aseguradora and s.ciudad=ciu.codigo ".($OFIU?" and s.ciudad=$OFIU ":"")." $Filtro_query 
								ORDER BY fecha_solicitud";
	// echo $Consulta_garantias;
	if($IDsiniestro) 
	$Consulta_garantias="select a.*,s.numero as nsin,s.placa,s.asegurado_nombre,s.asegurado_id,ase.nombre as naseguradora,ciu.nombre as nciudad,
								s.img_inv_salida_f as acta1,img_inv_entrada_f as acta2,frq.nombre as nfranq,s.fecha_final as fdevolvh,s.id as sid,a.id as aid,s.ubicacion
								FROM sin_autor a,siniestro s,franquisia_tarjeta frq,aseguradora ase,ciudad ciu
								WHERE a.siniestro=s.id and a.franquicia=frq.id  and a.estado='A' and s.id=$IDsiniestro and a.aut_fac=0 and ase.id=s.aseguradora and 
								s.ciudad=ciu.codigo ORDER BY fecha_solicitud";
    //  echo $Consulta_garantias;
	if($Garantias=q($Consulta_garantias))
	{
		$NT=tu('sin_autor','id');
		
		echo "<table align='center' border cellspacing='0' bgcolor='ffffff'><tr>
						<th colspan=15>DATOS DE LA GARANTIA</th><th colspan=3>CUSTODIA AOA</th><th colspan=4>AUDITORIA</th><th colspan=6>DEVOLUCION</th></tr>
						<tr><th>#</th><th>Aseguradora</th><th>Siniestro</th><th>Veh�culo</th><th>Acta</th><th>Asegurado</th><th>Identificaci�n</th><th>Tarjeta Habiente</th>
						<th>Identificaci�n</th><th>Ciudad</th><th>Fecha Solicitud</th><th>Devoluci�n.Vh.</th><th>Franquicia</th><th>Monto</th><th>Voucher</th>
						<th>Recepci�n</th><th>Consignaci�n</th><th>Inscripci�n</th>
						<th>Facturado</th><th>Pagado</th><th>Saldo<br />por cobrar</th><th>Comparendos</th>
						<th>A transferir</th><th>Actualizar</th><th>Im�gen</th><th>M�todo</th><th>Fec.Devoluci�n</th><th>Email</th></tr>";
		include('inc/link.php');
		$Contador=0;
		$IDSin=$IDAut='';
		while($G=mysql_fetch_object($Garantias))
		{
			//echo "select nombre from oficina,ubicacion where oficina.id=ubicacion.oficina and ubicacion.id=$G->ubicacion"."<br>";
				$Noficina=qo1m("select nombre from oficina,ubicacion where oficina.id=ubicacion.oficina and ubicacion.id=$G->ubicacion",$LINK);
				$S_AOA=qo1m("select id from vehiculo where placa='$G->placa' ",$LINK);
				$IDSin.=($IDSin?",":"").$G->sid;
				$IDAut.=($IDAut?",":"").$G->aid;
				$Contador++;
				$_id=$G->sid.'_'.$G->id;
				echo "<tr ".($USUARIO==1?" ondblclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NT&id=$G->id',0,0,500,500,'mod')\" ":"").">
						<td bgcolor='ffffff' align='center'>".coma_format($Contador)."</td>
						<td bgcolor='ffffff'>$G->naseguradora</td>
						<td bgcolor='ffffff' nowrap='yes' >
							$G->placa<br>$G->nsin <a class='info' style='cursor:pointer' onclick='ver_siniestro($G->sid);'>
							<img src='gifs/standar/Preview.png' border='0'><span>Ver Siniestro</span></a></td>
						<td bgcolor='ffffff' align='center' id='pl_$_id'></td>
						<td align='center' nowrap='yes' bgcolor='ffffff'>";
				if(!$Excel)
						echo "
							<a class='info' style='cursor:pointer' onclick=\"modal('$G->acta1',0,0,700,900,'acta');\"><img src='gifs/standar/Preview.png' border='0'><span>Ver el Acta de Entrega</span></a>
							<a class='info' style='cursor:pointer' onclick=\"modal('$G->acta2',0,0,700,900,'acta');\"><img src='gifs/standar/Preview.png' border='0'><span>Ver el Acta de Devoluci�n</span></a>";
				echo "
						</td>
						<td bgcolor='ffffff' ".($S_AOA?"style='background-image:url(img/LOGO_AOA_200_suave.png);background-size:contain;background-repeat: no-repeat' ":"").">$G->asegurado_nombre</td>
						<td align='right' bgcolor='ffffff'>".coma_format($G->asegurado_id)."</td>
						<td bgcolor='ffffff'>$G->nombre <span id='fo_$_id'></span></td>
						<td align='right' bgcolor='ffffff'>".coma_format($G->identificacion)."</td>
						<td bgcolor='ffffff'>$Noficina</td>
						<td bgcolor='ffffff' nowrap='yes'>".date('Y-m-d',strtotime($G->fecha_solicitud))."</td>
						<td bgcolor='ffffff' align='center' nowrap='yes'>$G->fdevolvh</td>
						<td nowrap='yes' bgcolor='ffffff'>$G->nfranq <br><span id='rcp_$_id'></span><span id='rc_$G->aid'></span>";	
				if($G->rc && $G->rc_id)
				{
						echo "<br />Recibo de Caja: <b>$G->rc ";
						if(!$Excel) echo "<a class='info' style='cursor:pointer' onclick='ver_recibo($G->rc_id);'><img src='gifs/standar/Preview.png' border='0'><span>Ver Recibo</span></a>";
						echo "</b>";
				}
				$Color_custodia='ddddff';
				$Color_auditoria='ffffcc';
				echo "</td><td align='right' bgcolor='ffffff'><b>".(!$Excel?coma_format($G->valor):$G->valor)."</b></td>
							<td>$G->numero_voucher</td><td align='center' bgcolor='$Color_custodia'>";
				//////////////////////   REGISTRO DE RECEPCION DE LA GARANTIA ////////////////////////////////////
				if($G->recibido_por)
				{
					if(!$Excel) echo "<a class='info'>$G->fecha_recepcion<span style='width:200px'>Recibido por: $G->recibido_por</span></a>";
					else echo "$G->fecha_recepcion";
				}
				else
				{
					if(!$Excel) echo "<a class='info' style='cursor:pointer' onclick='recepcion_garantia($G->id);'><img src='gifs/standar/calendario_siguiente.png' border='0'><span>Registrar Recepci�n de Garant�a</span></a>";
				}
				echo "</td><td align='center' bgcolor='$Color_custodia'>";
				//////////////////////   REGISTRO DE CONSIGNACION DE LA GARANTIA ////////////////////////////////////
				if($G->fecha_consignacion && $G->numero_consignacion)
				{
					echo "$G->fecha_consignacion<br />$G->numero_consignacion ";
					if(!$Excel)  echo "<a class='info' style='cursor:pointer' onclick=\"modal('$G->consignacion_f',0,0,700,900,'consignacion')\"><img src='gifs/standar/Preview.png' border='0'><span>Ver Consignaci�n</span></a>";
				}
				elseif($G->recibido_por)
				{
					IF(!$Excel) echo "<a class='info' style='cursor:pointer' onclick='registro_consignacion($G->id);'><img src='gifs/standar/nuevo_registro.png' border='0' height='10'><span>Adicionar Consignaci�n</span></a>";
				}
				echo "</td><td align='center' bgcolor='$Color_custodia' nowrap='yes'>";
				//////////////////////  REGISTRO DE LA INSCRIPCION DE LA CUENTA  //////////////////////////////////////
				if($G->inscripcion)
				{
					IF(!$Excel) echo "<a style='cursor:pointer'><img src='gifs/standar/si.png' border='0' height='10' alt='Inscrita' title='Inscrita'></a>"; else echo "Inscrita";

					if($INSCRIBE_CUENTAS)
						IF(!$Excel) echo "<br /><a class='info' onclick='corregir_transferencia($G->id);'><img src='gifs/standar/regresar_ovr.png' border='0' height='10'><span>Corregir Cuenta de Transferencia</span></a>";
				}
				elseif($G->numero_consignacion)
				{
					if($INSCRIBE_CUENTAS)
					{
						IF(!$Excel)
						{
							echo "<a class='info' style='cursor:pointer' onclick='inscribir_cuenta($G->id);'><img src='gifs/standar/derecha.png' border='0' height='10'><span>Inscribir Cuenta</span></a>";
							echo "<input type='checkbox' name='rinscritas$G->id' onchange='incluye_inscritas($G->id,this.checked);' alt='Marque para generar reporte de inscripciones' title='Marque para generar reporte de inscripciones'>";
						}
					}
				}
				IF(!$Excel) echo "<a class='info' style='cursor:pointer' onclick=\"modal('zcontrol_custodia_garantia.php?Acc=inserta_obs_transferencia&id=$G->id',0,0,500,500,'obs');\">
								<img src='gifs/mas.gif' border='0' height='10'>".($G->obs_transferencia?"...":"")."<span style='width:300px'><table><tr><td width='300px'>".
							($G->obs_transferencia?"<b>Observaciones:</b><hr>".nl2br($G->obs_transferencia)."<hr>":"")."Insertar observaciones de transferencia</td></tr></table></span></a>";

				if($G->obs_transferencia && $Excel) echo nl2br($G->obs_transferencia);
				echo "</td><td align='right' bgcolor='$Color_auditoria' id='fa_$_id'></td><td align='right' bgcolor='$Color_auditoria' id='fap_$_id'></td><td align='right' bgcolor='$Color_auditoria' id='saldo_$_id'></td>";
				if($G->comparendos)
				{if(!$Excel) echo "<td align='center'><img src='gifs/standar/si.png' border='0'></td>"; else echo "<td align='center'>Revisado</td>";}
				else
				{  echo "<td align='center' bgcolor='$Color_auditoria'>";
					IF(!$Excel) echo "<a class='info' style='cursor:pointer' onclick='marcar_comparendos($G->id);'><img src='gifs/standar/edita_registro.png' border='0' height='16'><span>Marcar Verificaci�n de comparendos</span></a><span id='sf_$G->id'></span></td>";}
				////////////////////  REGISTRO DE DATOS DE LA TRANSFERENCIA  ////////////////////////////////////////////////////////
				////////-----------monto a transferir --------------------
				if($G->inscripcion && $G->numero_consignacion) echo "<td align='right' 'ffffff' id='at_$G->id'></td>";
				else
					echo "<td></td>";
				
	//				mysql_query("update sin_autor set valor_devolucion='$A_transferir' where id=$G->id");
				////////------------------------------
				echo "<td align='center' nowrap='yes'>";
				if($G->email && !$Excel)	echo "<a class='info' ><img src='img/arroba.png' border='0' height='10'><span style='width:300px'>Correo: $G->email</span></a> ";
				IF(!$Excel) echo "<a class='info' style='cursor:pointer' onclick='actualiza_info($G->id);'><img src='img/actualizar.png' border='0' height='10'><span style='width:200px'>Actualizar informaci�n del asegurado</span></a>";
				if(inlist($USUARIO,'1,2,3'))
				{
					IF(!$Excel) echo "<a class='rinfo' style='cursor:pointer' href=\"javascript:modal('zcontrol_custodia_garantia.php?Acc=inserta_obs&id=$G->id',0,0,500,500,'obs');\">
								<img src='gifs/mas.gif' border='0' height='10'>".($G->obs_devolucion?"...":"")."<span>".($G->obs_devolucion?"<table width='300px'><tr><td>$G->obs_devolucion</td></tr></table>":"Insertar observaciones de devolucion")."</span></a>";
				}
				echo "</td><td align='center'>";
				if(!$G->metodo_devol)
				{
						if($INSCRIBE_CUENTAS)
						{
							if($G->comparendos && $G->inscripcion && $G->numero_consignacion)
							{
								IF(!$Excel) echo "<span id='it_$G->id' style='visibility:hidden'></span>";
							}
						}
				}
				
				////////////////////  IMAGENES DE DEVOLUCION  ////////////////////////////////////////////////////////
				
				IF(!$Excel)
				{
					if ($G->congelamiento) { echo "Congelado";} 
					elseif ($S_AOA) echo "AOA";
					else  // En caso de que sea solo congelamiento no aparece la captura de imagenes de devolucion
					{
						if($G->devolucion_f)
							echo "<a class='info' style='cursor:pointer' onclick='ver_imagen($G->id);'><img src='gifs/standar/si.png' border='0'><span>Ver imagen 1</span></a>";
						else
							echo "<a class='info' style='cursor:pointer' onclick='subir_imagen($G->id);'><img src='gifs/standar/Warning.png' border='0'><span>Subir Imagen 1</span></a>";
						if($G->devolucion2_f)
							echo "<a class='info' style='cursor:pointer' onclick='ver_imagen2($G->id);'><img src='gifs/standar/si.png' border='0'><span>Ver imagen 2</span></a>";
						else
							echo "<a class='info' style='cursor:pointer' onclick='subir_imagen2($G->id);'><img src='gifs/standar/Warning.png' border='0'><span>Subir Imagen 2</span></a>";
					}
				}
				echo "<td align='center' id='md_$G->id'>";
				if($G->obs_devolucion)
				{
					IF(!$Excel) echo "<a class='rinfo'><img src='img/alto.gif' border='0' height='30'><span><table width='200px'><tr><td>$G->obs_devolucion</td></tr></table></span></a>";
				}
				else
				{
					if($G->devolucion_f || $G->devolucion2_f || $INSCRIBE_CUENTAS || ($G->congelamiento && $G->comparendos) || $S_AOA)
					{
						if($G->metodo_devol && $Excel) echo "$G->metodo_devol ";
						else
							echo "<select onchange='cambio_modo($G->id,this.value);' ".($G->metodo_devol?"disabled":"")."><option value=''></option>
									<option value ='ANULADO' ".($G->metodo_devol=='ANULADO'?"selected":"").">Anulado</option>
									<option value ='CONSIGNADO' ".($G->metodo_devol=='CONSIGNADO'?"selected":"").">Consignado</option>
									<option value ='EN PERSONA' ".($G->metodo_devol=='EN PERSONA'?"selected":"").">En persona</option>
									<option value ='TRANSFERENCIA' ".($G->metodo_devol=='TRANSFERENCIA'?"selected":"").">Transferencia</option>
									<option value ='COBRO' ".($G->metodo_devol=='COBRO'?"selected":"").">Cobro</option>
									<option value ='CONGELAMIENTO' ".($G->metodo_devol=='CONGELAMIENTO'?"selected":"").">Solo Congelamiento</option>
									<option value ='VH. AOA' ".($G->metodo_devol=='VH. AOA'?"selected":"").">Vehiculo de AOA</option>
									</select>";
					}
					else
					{
						if($G->metodo_devol)
						{
							if($Excel) echo "$G->metodo_devol";
							else
								echo "<select onchange='cambio_modo($G->id,this.value);' disabled><option value=''></option>
									<option value ='ANULADO' ".($G->metodo_devol=='ANULADO'?"selected":"").">Anulado</option>
									<option value ='CONSIGNADO' ".($G->metodo_devol=='CONSIGNADO'?"selected":"").">Consignado</option>
									<option value ='EN PERSONA' ".($G->metodo_devol=='EN PERSONA'?"selected":"").">En persona</option>
									<option value ='TRANSFERENCIA' ".($G->metodo_devol=='TRANSFERENCIA'?"selected":"").">Transferencia</option>
									<option value ='COBRO' ".($G->metodo_devol=='COBRO'?"selected":"").">Cobro</option>
									<option value ='CONGELAMIENTO' ".($G->metodo_devol=='CONGELAMIENTO'?"selected":"").">Solo Congelamiento</option>
									<option value ='VH. AOA' ".($G->metodo_devol=='VH. AOA'?"selected":"").">Vehiculo de AOA</option>
									</select>";
						}
					}
				}
				echo "<td ";
				if($G->fecha_devolucion!='0000-00-00 00:00:00')
				{
					$P1=date('Ym',strtotime($G->fecha_consignacion));
					$P2=date('Ym',strtotime($G->fecha_devolucion));
					$Periodos=diferencia_periodo($P1,$P2);
					if($Periodos==1) echo "bgcolor='ffffdd' ";
					if($Periodos>1) echo "bgcolor='ffdddd' ";
					echo "alt ='$P1 $P2' title='$P1 $P2' ";
				}
				echo ">";
				if($G->fecha_devolucion!='0000-00-00 00:00:00') echo "$G->fecha_devolucion";
				echo "</td><td nowrap='yes'>";
				if($G->fecha_envio!='0000-00-00 00:00:00')
				{
					IF(!$Excel) echo "<a class='rinfo' >$G->fecha_envio<span style='width:200px'>Enviado por $G->enviado_por</span></a> ";
					else echo "$G->fecha_envio";
				}
				if($G->email && $G->metodo_devol && ($G->devolucion_f || $G->devolucion2_f) && !$G->obs_devolucion)
				{
					if(!$Excel) echo "<a class='info' style='cursor:pointer' onclick=' enviar_mail_garantia($G->id);'><img src='img/enviarmail.png' border='0' height='20' onclick=\"this.src='img/cargando.gif';\"><span>Enviar email</span></a>";
				}
				echo "</td>";
				echo "</tr>";
		}
		
		mysql_close($LINK);
		echo "</table><iframe name='Oculto_garantia2' id='Oculto_garantia2' style='visibility:hidden' width='10' height='2'></iframe>
		<form action='zcontrol_custodia_garantia.php' target='Oculto_garantia2' method='POST' name='forma2' id='forma2'>
			<input type='hidden' name='IDSin' value='$IDSin'>
			<input type='hidden' name='IDAut' value='$IDAut'>
			<input type='hidden' name='Acc' value='consultar_garantias2'>
		</form>";
		echo "<script language='javascript'>document.forma2.submit();</script>";
		mysql_free_result($Garantias);
	}
	echo "<iframe name='Oculto_garantia' id='Oculto_garantia' style='visibility:hidden' height='1' width='1'></iframe>
	<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></body>";
}

class fsiniestro
{
	var $siniestro=0; // ID DE SINIESTRO
	var $placa=''; // PLACA DEL ASEGURADO
	var $foto=''; // FOTO DEL ASEGURADO
	var $rcp=''; // RECIBO DE CAJA PROVISIONAL SI LO HAY
	var $facturado=0; // VALOR FACTURADO POR CADA SINIESTRO
	var $pagado=0; // VALOR PAGADO POR CADA SINIESTRO
	var  $descontado=0; // VALOR DESCONTADO POR CADA SINIESTRO
	var $saldo=0; // SALDO POR CADA SINIESTRO
	function fsiniestro($D) {$this->siniestro=$D->id;$this->placa=$D->placa;$this->foto=$D->foto;$this->rcp=$D->rcp;}
	function factura($valor)	{$this->facturado+=$valor; $this->saldo();}
	function pago($valor) {$this->pagado+=$valor; $this->saldo();}
	function nco($valor) {$this->descontado+=$valor; $this->saldo();}
	function saldo() {$this->saldo=$this->facturado-$this->pagado-$this->descontado;}
	function pinta1($ida) { echo "\npinta1($this->siniestro,$ida,'$this->placa','$this->foto','$this->rcp');";}
	function pinta3($ida) {if($this->facturado) echo "\npinta3($this->siniestro,$ida,'".coma_format($this->facturado)."');";}
	function pinta4($ida) {if($this->pagado) echo "\npinta4($this->siniestro,$ida,'".coma_format($this->pagado+$this->descontado)."');";}
	function pinta5($ida) {if($this->saldo) echo "\npinta5($this->siniestro,$ida,'".coma_format($this->saldo)."');";}
	function pinta_todo($ida) {$this->pinta1($ida);$this->pinta3($ida);$this->pinta4($ida);$this->pinta5($ida);}
}

class cgarantia
{
	var $id=0; // ID DE LA AUTORIZACION
	var $valor=0; // VALOR DE LA GARANTIA
	var $siniestro=0; // SINIESTRO AL QUE PERTENECE
	var $a_transferir=0; // MONTO A TRANSFERIR
	var $rc_id=0; // ID DEL RECIBO DE CAJA CORRESPONDIENTE
	var $rc=''; // NUMERO CONSECUTIVO DEL RECIBO DE CAJA
	var $inscribir_transferencia=0; // CONTROL DE INSCRIPCION DE CUENTA
	var $enBogota=0; // control para saber si la cuenta de transferencia esta radicada en Bogota para calcular los costos financieros
	var $Comentario_enBogota=''; // comentario para poner en el detalle de costos financieros
	var $enBancolombia=0; // control para saber si la cuenta de transferencia esta radicada en Bancolombia para calcular los costos financieros
	var $Comentario_enBancolombia=''; // comentario para poner en el detalle de costos financieros
	var $Comision=0; // comisi�n que cobra el banco de acuerdo a si es en bogota o no yt si es bancolombia u otra entidad
	var $Impuesto=0; // impuesto del 4 x 1000 por la transaccion
	var $Costo_financiero=0; // variable para acumular los costos financieros.
	var $Ciudad_cuenta=''; // Codigo de la ciudad cuenta de devoluci�n
	function cgarantia($D) {$this->id=$D->id; $this->rc_id=$D->rc_id;$this->rc=$D->rc;$this->siniestro=$D->siniestro;
		$this->valor=$D->valor;$this->Ciudad_cuenta=$D->ciudad_cuenta_devol;
		if($D->inscripcion && $D->numero_consignacion && !$D->metodo_devol) $this->inscribir_transferencia=1;
		if($D->devol_banco==5) {$this->enBancolombia=1; $this->Comentario_enBancolombia='Cuenta radicada en Bancolombia';}
		else {$this->enBancolombia=0;$this->Comentario_enBancolombia='Cuenta NO radicada en Bancolombia';}
		if($this->Ciudad_cuenta=='11001000') {$this->enBogota=1; $this->Comentario_enBogota='Cuenta radicada en la ciudad de Bogot�';}
		else {$this->enBogota=0; $this->Comentario_enBogota='Cuenta NO radicada en la ciudad de Bogot�';}
		}
	function pinta2($LINK) 
	{
		global $AGarantia,$ACitas;
		// CALCULO DEL MONTO A TRANSFERIR, DESCONTANDO COSTOS FINANCIEROS
		// COSTOS FINANCIEROS:
		$Costosf=qom("select * from costo_financiero where en_bogota=$this->enBogota and en_bancolombia=$this->enBancolombia ",$LINK);
		$this->a_transferir=$this->valor-$AGarantia[$this->siniestro]->saldo-$AGarantia[$this->siniestro]->descontado;
		$this->Comision=$Costosf->valor_comision;
		$this->Impuesto=round($this->a_transferir*$Costosf->impuesto,0);
		$this->Costo_financiero=$this->Comision+$this->Impuesto;
		if($this->rc_id) echo "\npinta2($this->id,'$this->rc_id','$this->rc');";
		$AGarantia[$this->siniestro]->pinta_todo($this->id);
		$Neto_a_transferir=$this->a_transferir-$this->Costo_financiero;
		if($Neto_a_transferir>0) 
		{	
			if($this->Ciudad_cuenta)
			{
				$Comentario_costos="Costos  Financieros de Transferencia:\nValor Comision: ".coma_format($this->Comision)." $this->Comentario_enBogota, $this->Comentario_enBancolombia.\nValor Impuesto: ".coma_format($this->Impuesto);
				$Comentario_costosjs="<table width=300px border cellspacing=0><tr><th colspan=3>Costos  Financieros de Transferencia:</th></tr><tr><td>Comision:</td><td align=right>".coma_format($this->Comision)."</td><td>$this->Comentario_enBogota, $this->Comentario_enBancolombia.</td></tr><tr><td>Impuesto:</td><td align=right> ".coma_format($this->Impuesto)."</td><td>Cuatro por mil</td></tr><tr><td align=center>TOTAL</td><td align=right>".coma_format($this->Costo_financiero)."</td></tr></table>";
				echo "\n pinta6($this->id,'".coma_format($Neto_a_transferir)."','$Comentario_costosjs'); ";	
				echo "\n pinta7($this->id,$Neto_a_transferir); ";
				mysql_query("update sin_autor set valor_devolucion='$this->a_transferir',costo_financiero='$this->Costo_financiero' , 
				detalle_costosf=\"$Comentario_costos\" where id=$this->id",$LINK); 
				if($ACitas[$this->siniestro]) echo "\npinta8($this->id,".$ACitas[$this->siniestro].");";
			}
			else
			{
				echo "\npinta6($this->id,'Falta Ciudad'); ";	
			}
		}
	}
}

function consultar_garantias2()
{
	global $BDA,$IDSin,$IDAut,$AGarantia,$ACitas;
	$tmpi='tmpi_gar'.$_SESSION['Id_alterno'].'_'.$_SESSION['User'];
	include('inc/link.php');
	$Datos1=mysql_query("select s.id,t_veh_ub(s.ubicacion) as placa,foto_recepcion(s.id) as foto,t_rcp(s.id) as rcp from siniestro s where s.id in ($IDSin) ",$LINK); // DATOS DE SINIESTROS
	$Datos2=mysql_query("Select a.id,t_rc_id(a.id) as rc_id,t_rc(a.id) as rc,a.siniestro,a.valor,a.comparendos,
												a.inscripcion, a.numero_consignacion,a.metodo_devol ,a.ciudad_cuenta_devol,a.devol_banco
												from sin_autor a where a.id in ($IDAut) ",$LINK); // DATOS DE AUTORIZACIONES
	$Datos3=mysql_query("Select id,siniestro from cita_servicio where siniestro in ($IDSin) and estado='C' ",$LINK);											
	$ACustodia=array();
	while($D=mysql_fetch_object($Datos2))  $ACustodia[$D->siniestro][$D->id]=new cgarantia($D); // ALIMENTA UN ARREGLO CON LOS DATOS DE LAS AUTORIZACIONES
	
	mysql_query("drop table if exists $tmpi",$LINK);
	mysql_query("create table $tmpi Select  siniestro,total as facturado,id as idf from factura where siniestro in ($IDSin) and anulada=0 
								union select siniestro,valor as facturado,(0000000) as idf from solicitud_factura where siniestro in ($IDSin) and (procesado_por='' or fecha_proceso='0000-00-00 00:00:00')",$LINK);
								
	while($D=mysql_fetch_object($Datos1))  { $AGarantia[$D->id]= new fsiniestro($D); } // ALIMENTA UN ARREGLO DE GARANTIAS CON LOS DATOS DE LOS SINIESTROS
	
	
	while($D=mysql_fetch_object($Datos3)) {$ACitas[$D->siniestro]=$D->id;}
	$DFacturas=mysql_query("select * from $tmpi ",$LINK);
	while($F=mysql_fetch_object($DFacturas))
	{ // PARA CADA SINIESTRO SI EXISTEN FACTURAS LAS VA ACUMULANDO EN SU CLASE Y TAMBIEN BUSCA PAGOS CON RC, NOTAS CREDITO Y NOTAS CONTABLES. 
		if(!$AGarantia[$F->siniestro]) $AGarantia[$F->siniestro]= new fsiniestro($F->siniestro);
		$AGarantia[$F->siniestro]->factura($F->facturado);
		if($F->idf)
		{
			$Pagado=qo1m("select sum(valor) from recibo_caja where factura=$F->idf and anulado=0 ",$LINK);
			$Notas=qo1m("select sum(valor) from nota_contable where factura=$F->idf and anulado=0 ",$LINK);
			$Notasc=qo1m("select sum(total) from nota_credito where factura=$F->idf and anulado=0",$LINK);
			$Tpagado=$Pagado+$Notasc;
			if($Tpagado) $AGarantia[$F->siniestro]->pago($Tpagado);
			if($Notas) $AGarantia[$F->siniestro]->nco($Notas);
		}
	}

	echo "<script language='javascript'>
	function pinta1(ids,ida,dato,dato1,dato2)
	{
		if(dato) parent.document.getElementById('pl_'+ids+'_'+ida).innerHTML=dato;
		if(dato1) parent.document.getElementById('fo_'+ids+'_'+ida).innerHTML=\"<a onclick=\\\"ver_foto('\"+dato1+\"');\\\"><img src='../../Administrativo/\"+dato1+\"' border='0' height='16'></a>\";		
		if(dato2) parent.document.getElementById('rcp_'+ids+'_'+ida).innerHTML='RCP <b>'+dato2+'</b>';		
	}
	function pinta2(ida,dato,dato1)
	{if(dato) parent.document.getElementById('rc_'+ida).innerHTML=\"Recibo Caja <b>\"+dato1+\"</b> <a class='info' style='cursor:pointer' onclick='ver_recibo(\"+dato+\");'><img src='gifs/standar/Preview.png' border='0'><span>Ver Recibo</span></a>\";}
	function pinta3(ids,ida,dato)
	{parent.document.getElementById('fa_'+ids+'_'+ida).innerHTML=dato+\"<a class='info' style='cursor:pointer' onclick='ver_facturas(\"+ids+\");'><img src='gifs/standar/Preview.png' border='0'><span>Ver detalle de facturas</span></a>\";}
	function pinta4(ids,ida,dato)
	{parent.document.getElementById('fap_'+ids+'_'+ida).innerHTML=dato;}
	function pinta5(ids,ida,dato)
	{parent.document.getElementById('saldo_'+ids+'_'+ida).innerHTML=\"<b style='color:aa0000'>\"+dato+\"</b>\";
	parent.document.getElementById('md_'+ida).innerHTML=\"<a class='rinfo'><img src='img/alto.gif' border='0' height='30'><span><table width='200px'><tr><td>Saldo en Cartera: \"+dato+\"</td></tr></table></span></a>\";}
	function pinta6(id,dato,comentario)
	{if(parent.document.getElementById('at_'+id)) parent.document.getElementById('at_'+id).innerHTML=\"<a class='rinfo'><b style='color:007700'>\"+dato+\"</b><span>\"+comentario+\"</span></a>\";}
	function pinta7(id,valor)
	{if(parent.document.getElementById('it_'+id)) {
	parent.document.getElementById('it_'+id).innerHTML=\" <input type='checkbox' name='transf\"+id+\"' onchange='incluye_transferencia(\"+id+\",\"+valor+\",this.checked);'>\";
	parent.document.getElementById('it_'+id).style.visibility='visible';}}
	function pinta8(id,dato)
	{if(parent.document.getElementById('sf_'+id))
		parent.document.getElementById('sf_'+id).innerHTML=\"<a style='cursor:pointer' onclick='solicitar_factura(\"+dato+\");' alt='Solicitar Factura' title='Solicitar Factura'><img src='gifs/standar/nuevo_ovr.png' border='0' height='16'></a>\";}
	</script><body><script language='javascript'>";
	

	foreach($ACustodia as $pid => $PDato) {foreach($PDato as $id => $Dato) $Dato->pinta2($LINK);} 
	mysql_close($LINK);
	
	echo "\nalert('Carga finalizada');</script>";
	
	echo "</body>";
}

function subir_imagen()
{
	global $id;
	html('SUBIR IMAGEN');
	echo "<script language='javascript'>
			function cambio_imagen_std(Campo,ruta,tamrecimg)
			{
				window.open('marcoindex.php?Acc=reg_sube_img&T=sin_autor&Id=$id&C='+Campo+'&tri='+tamrecimg+'&ruta='+ruta,'simg_'+Campo);
				document.getElementById('cerrar').style.visibility='visible';
				document.getElementById('lapiz_cambio').style.visibility='hidden';
			}
		</script>
		<body onload='carga()' onunload='opener.parent.recargar_tablero();'>
		<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='mod' id='mod'>
		<input type='hidden' name='Acc' value='subir_imagen_ok'>
		<table border=0 cellspacing=0 cellpadding=0><tr><td bgcolor='eeeeee'>";
	$Ancho=300;
	$Alto=250;
	$Info='';
	$Sub_Contenido=substr($Info,strrpos($Info,'/')+1);$Sub_Tumb='tumb_'.$Sub_Contenido;$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$Info);
	if(!file_exists($Tumb) && file_exists($Info))
	{
		if(strpos(strtolower($Sub_Contenido),'.jpg')) picresize($Info,TUMB_SIZE,'jpg',$Tumb);
		if(strpos(strtolower($Sub_Contenido),'.gif')) picresize($Info,TUMB_SIZE,'gif',$Tumb);
		if(strpos(strtolower($Sub_Contenido),'.png')) picresize($Info,TUMB_SIZE,'png',$Tumb);
	}
	echo "<iframe id='simg_devolucion_f' name='simg_devolucion_f' src='$Tumb' height='$Alto' width='$Ancho' frameborder='0' ></iframe>";
	echo "</td><td valign='top' bgcolor='efefef'>";
	echo "<a style='cursor:pointer;' class='info' onclick=\"cambio_imagen_std('devolucion_f','garantia',1000);\" id='lapiz_cambio'>
	 				<img src='gifs/standar/Pencil.png' border='0'>
		<span style='width:100px'>Cambiar la imagen</span></a><br><br>";
	echo "</td></tr></table>";
	if($_SESSION['User']==1)
		echo "<input type='text' name='devolucion_f' id='devolucion_f' value='$Info' size='20'>";
	else
		echo "<input type='hidden' name='devolucion_f' id='devolucion_f' value='$Info'>";

	echo "
		</form><center>
		<br /><b>NOTA: La carga de la im�gen tiene UNA sola oportunidad <br />y quedar� guardada en el registro tan pronto sea <br />subida al sistema. <br /><br />
		<input type='button' value='Cerrar esta ventana' style='visibility:hidden' id='cerrar' onclick='window.close();void(null);'></center>
		</body>";
}

function subir_imagen2()
{
	global $id;
	html('SUBIR IMAGEN');
	echo "<script language='javascript'>
			function cambio_imagen_std(Campo,ruta,tamrecimg)
			{
				window.open('marcoindex.php?Acc=reg_sube_img&T=sin_autor&Id=$id&C='+Campo+'&tri='+tamrecimg+'&ruta='+ruta,'simg_'+Campo);
				document.getElementById('cerrar').style.visibility='visible';
				document.getElementById('lapiz_cambio').style.visibility='hidden';
			}
		</script>
		<body onload='carga()' onunload='opener.parent.recargar_tablero();'>
		<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='mod' id='mod'>
		<input type='hidden' name='Acc' value='subir_imagen_ok'>
		<table border=0 cellspacing=0 cellpadding=0><tr><td bgcolor='eeeeee'>";
	$Ancho=300;
	$Alto=250;
	$Info='';
	$Sub_Contenido=substr($Info,strrpos($Info,'/')+1);$Sub_Tumb='tumb_'.$Sub_Contenido;$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$Info);
	if(!file_exists($Tumb) && file_exists($Info))
	{
		if(strpos(strtolower($Sub_Contenido),'.jpg')) picresize($Info,TUMB_SIZE,'jpg',$Tumb);
		if(strpos(strtolower($Sub_Contenido),'.gif')) picresize($Info,TUMB_SIZE,'gif',$Tumb);
		if(strpos(strtolower($Sub_Contenido),'.png')) picresize($Info,TUMB_SIZE,'png',$Tumb);
	}
	echo "<iframe id='simg_devolucion2_f' name='simg_devolucion2_f' src='$Tumb' height='$Alto' width='$Ancho' frameborder='0' ></iframe>";
	echo "</td><td valign='top' bgcolor='efefef'>";
	echo "<a style='cursor:pointer;' class='info' onclick=\"cambio_imagen_std('devolucion2_f','garantia',1000);\" id='lapiz_cambio'>
	 				<img src='gifs/standar/Pencil.png' border='0'>
		<span style='width:100px'>Cambiar la imagen</span></a><br><br>";
	echo "</td></tr></table>";
	if($_SESSION['User']==1)
		echo "<input type='text' name='devolucion2_f' id='devolucion2_f' value='$Info' size='20'>";
	else
		echo "<input type='hidden' name='devolucion2_f' id='devolucion2_f' value='$Info'>";

	echo "
		</form><center>
		<br /><b>NOTA: La carga de la im�gen tiene UNA sola oportunidad <br />y quedar� guardada en el registro tan pronto sea <br />subida al sistema. <br /><br />
		<input type='button' value='Cerrar esta ventana' style='visibility:hidden' id='cerrar' onclick='window.close();void(null);'></center>
		</body>";
}

function ver_imagen()
{
	global $id;
	$Img=qo1("select devolucion_f from sin_autor where id=$id");
	html('Ver Imagen de Garantia');
	echo "<body><script language='javascript'>centrar();</script><img src='$Img' border='0'></body>";
}

function ver_imagen2()
{
	global $id;
	$Img=qo1("select devolucion2_f from sin_autor where id=$id");
	html('Ver Imagen de Garantia');
	echo "<body><script language='javascript'>centrar();</script><img src='$Img' border='0'></body>";
}

function cambio_metodo_devolucion()
{
	global $id,$modo,$NUSUARIO;
	$Fecha=date('Y-m-d H:i:s');
	q("update sin_autor set metodo_devol='$modo',fecha_devolucion='$Fecha',devuelto_por='$NUSUARIO' where id=$id");
	graba_bitacora('sin_autor','M',$id,'metodo_devol, fecha_devolucion');
	echo "<body><script language='javascript'>parent.parent.recargar_tablero();</script>";
}

function enviar_email_garantia()
{
	global $id,$nofacturas,$sifacturas;
	$Hoy=date('Y-m-d');
	$NUSUARIO=$_SESSION['Nombre'];
	$G=qo("select * from sin_autor where id=$id");
	$Sin=qo("select id,numero,t_aseguradora(aseguradora) as naseguradora,ubicacion from siniestro where id=$G->siniestro"); // trae los datos del siniestro
	if($nofacturas) {}
	elseif($sifacturas)
	{
		$Consecutivos='';
		//print_r($_POST);
		foreach($_POST as $variable => $valor)
		{
			if(l($variable,5)=='_cfac')
			{
				$idfac=substr($variable,5);
				$Consecutivos.=($Consecutivos?", ":"").qo1("select consecutivo from factura where id=$idfac");
			}
		}
	}
	elseif($Facturas=q("select id from factura where siniestro=$Sin->id")) // busca si hay facturas para este siniestro
	{
		html("Facturas del siniestro $Sin->numero");
		echo "<body><script language='javascript'>modal('zcontrol_custodia_garantia.php?Acc=seleccion_facturas_email_garantia&siniestro=$Sin->id&idgarantia=$id',0,0,500,500,'emms');</script></body>";
		die();
	}

	$U=qo("select * from ubicacion where id=$Sin->ubicacion ");
	if($G->devol_ncuenta) $Nombrea=$G->devol_ncuenta;
	elseif($G->nombre) $Nombrea=$G->nombre;
	$Email_usuario=usuario('email');
	$Ruta_link="utilidades/Operativo/operativo.php?Acc=descargar_imagen_garantia&id=$id&Fecha=$Hoy";
	$Mensaje1=nl2br("<body>Estimado(a) Senor(a) 
	
Nos permitimos remitir voucher con sello de anulado, dejado como garantia por el servicio prestado entre los dias $U->fecha_inicial y $U->fecha_final correspondiente al siniestro numero $Sin->numero de la aseguradora $Sin->naseguradora. para acceder a la imagen debera digitar el numero de cedula con el que se constituyo la garantia, este enlace estara activo por 15 dias.

Para ver el documento por favor <a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='$Ruta_link';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> click aqui para descargar. </u></a>".
($Consecutivos?" 

Es de aclarar	 que al momento de la devolucion se afecto la garantia segun factura(s) numero(s) $Consecutivos con el fin de no afectar la totalidad del congelamiento dejado como garant�a del servicio.":"")."

Atentamente,

$NUSUARIO

AOA S.A.
Carrera 69B No. 98A-10 Morato
PBX +571 7560510
Bogota D.C., Colombia
www.aoacolombia.com

<img src='http://app.aoacolombia.com/img/AOAlogo.jpg' title='AOA COLOMBIA S.A. SE MUEVE CONTIGO'/>

<p style='font-size:9px'>Este mensaje es confidencial, esta amparado por secreto profesional y no puede ser usado ni divulgado por personas distintas de su(s) destinatario(s). Si no es el receptor autorizado, cualquier retencion, difusion, distribucion o copia de este mensaje es prohibida y sera sancionada por la ley. Si por error recibe este mensaje, favor reenviarlo al remitente y borrar el mensaje recibido.</p>
<p style='font-size:9px'>This messajge is confidential, subject to professional secret and may not be used or disclosed by any person other than its addressee(s). If you are not the addressee(s), any retention, dissemination, distribution or copying of this message is strictly prohibited and sanctioned by law. If you receive this message in error, please send it back and delete the message received.</p>

<b>Nota: Se eliminaron las tildes para compatibilidad con los administradores de correo electronico.</b>

</body>");
	
//	$Email_usuario=usuario('email2');
	$Exito=enviar_gmail($Email_usuario /*de */,$NUSUARIO /*Nombre de */ ,
						"$G->email,$Nombrea;sandraosorio@aoacolombia.com,Sandra Osorio" /*para */,
						"$Email_usuario,$NUSUARIO" /*con copia*/,
						"AOA Garantia de Servicio $Sin->numero" /*Objeto */,
						$Mensaje1);

	if($Exito)
	{
		html('Envio Exitoso');
		$Ahora=date('Y-m-d H:i:s');
		q("update sin_autor set fecha_envio='$Ahora',enviado_por='$NUSUARIO' where id=$id");
		if($nofacturas || $sifacturas)
			echo "<body><script language='javascript'>alert('Envio satisfactorio a $G->email con el archivo $G->devolucion_f desde el usuario $NUSUARIO');window.close();void(null);</script></body>";
		else
			echo "<body><script language='javascript'>centrar(10,10);alert('Email enviado a $G->email con el archivo: $G->devolucion_f desde el usuario $NUSUARIO ".PHP_VERSION."');parent.parent.recargar_tablero();</script></body>";
	}
	else
	{
		if($nofacturas || $sifacturas)
			echo "<body><script language='javascript'>alert('$mail->ErrorInfo');window.close();void(null);</script></body>";
		else
			echo "<body><script language='javascript'>alert('$mail->ErrorInfo');parent.parent.recargar_tablero();</script></body>";
	}
}

function seleccion_facturas_email_garantia()
{
	global $siniestro,$NUSUARIO,$idgarantia;
	$Sin=qo("select numero from siniestro where id=$siniestro");
	html("Seleccion de Facturas del siniestro n�mero $Sin->numero");
	echo "<script language='javascript'>function ver_fac(dato){modal('zfacturacion.php?Acc=imprimir_factura&id='+dato,0,0,700,900,'factura');}
	function vnofacturas() {document.forma.nofacturas.value='1';document.forma.sifacturas.value='';document.forma.submit();}
	function vsifacturas() {document.forma.sifacturas.value='1';document.forma.nofacturas.value='';document.forma.submit();}
	</script>
		<body><h3>Facturas del siniestro $Sin->numero</h4><br>
		<b>Estimado(a) usuario(a) $NUSUARIO, a continuaci�n ver� las facturas relacionadas con este siniestro. 
		Puede seleccionar una o varias facturas para incluir en el correo de anulaci�n del Voucher de la Garant�a indicandole al asegurado que fue 
		utilizada esa garantia para cancelar esas facturas.</b><br><br><br>
		<form action='zcontrol_custodia_garantia.php' target='_self' method='POST' name='forma' id='forma'>
		<table border cellspacing='0'><tr><th>Numero</th><th>Fecha</th><th>Valor</th><th>Pago(s)</th></tr>";
	$Facturas=q("select * from factura where siniestro=$siniestro order by consecutivo");
	while($F=mysql_fetch_object($Facturas))
	{
		echo "<tr><td><input type='checkbox' id='_cfac$F->id' name='_cfac$F->id'> 
			<a class='info' style='cursor:pointer' onclick='ver_fac($F->id)'><img src='gifs/standar/Preview.png'><span style='width:200px'>Ver factura</span></a>
		$F->consecutivo</td><td>$F->fecha_emision</td><td align='right'>".coma_format($F->total)."</td><td>";
		$Titulos=true;
		if($Rec_cajas=q("select rc.*,o.sigla from recibo_caja rc,oficina o where rc.factura=$F->id and o.id=rc.oficina"))
		{
			echo "<table border cellspacing='0'><tr><th>Documento</th><th>Valor</th></tr>";
			$Titulos=false;
			while($Rc=mysql_fetch_object($Rec_cajas))
			{
				echo "<tr><td>Recibo de Caja No. $Rc->sigla $Rc->consecutivo</td><td align='right'>".coma_format($Rc->valor)."</td></tr>";
			}
		}
		if($Notas_contables=q("select * from nota_contable where factura=$F->id"))
		{
			if($Titulos) echo "<table border cellspacing='0'><tr><th>Documento</th><th>Valor</th></tr>";
			$Titulos=false;
			while($Nc=mysql_fetch_object($Notas_contables))
			{
				echo "<tr><td bgcolor='ffffaa'>Nota Contable No. $Nc->consecutivo</td><td align='right'>".coma_format($Nc->valor)."</td></tr>";
			}
		}
		if($Notas_creditos=q("select * from nota_credito where factura=$F->id"))
		{
			if($Titulos) echo "<table border cellspacing='0'><tr><th>Documento</th><th>Valor</th></tr>";
			while($Nc=mysql_fetch_object($Notas_contables))
			{
				echo "<tr><td>Nota Credito No. $Nc->consecutivo</td><td align='right'>".coma_format($Nc->total)."</td></tr>";
			}
		}
		if(!$Titulos) echo "</table>";
		echo "</td></tr>";
	}
	echo "</table><br>
		<input type='button' value=' ENVIAR COMENTARIO DE FACTURAS EN EL CORREO ELECTRONICO' onclick='vsifacturas()' style='font-weight:bold;height:30px;'><br><br><br>
		<input type='button' value=' NO ENVIAR COMENTARIO DE FACTURAS EN EL CORREO ELECTRONICO ' onclick='vnofacturas()'>
		<input type='hidden' name='Acc' value='enviar_email_garantia'>
		<input type='hidden' name='nofacturas' value=''>
		<input type='hidden' name='sifacturas' value=''>
		<input type='hidden' name='id' value='$idgarantia'>
		</form>
	</body>";
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
		<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
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
	global $id, $observaciones, $NUSUARIO;
	$Ahora=date('Y-m-d H:i:s');
	q("update sin_autor set obs_devolucion=concat(obs_devolucion,\"\n[$NUSUARIO $Ahora] $observaciones\") where id=$id");
	echo "<body><script language='javascript'>opener.parent.recargar_tablero();window.close();void(null);</script>";
}

function recepcion_garantia()
{
	global $id;
	html('RECEPCION DE GARANTIA - ADMINISTRATIVO');
	echo "<body><h3>Marcaci�n de recepci�n de la garant�a</h3>
				Por seguridad se solicita la contrase�a del usuario actual para registrar esta recepci�n.<br /><br />
				<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
					Clave de confirmaci�n: <input type='password' name='Clave' id='Clave'><br /><br />
					<input type='submit' value='CONTINUAR'>
					<input type='hidden' name='Acc' value='recepcion_garantia_ok'>
					<input type='hidden' name='id' value='$id'>
				</form>";

}

function recepcion_garantia_ok()
{
	global $id,$Clave;
	if(verificar_password($_SESSION['Nick'],$Clave))
	{
		echo "<body>
			<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='recepcion_garantia_marca_ok'>
				<input type='hidden' name='id' value='$id'>
			</form>
			<script language='javascript'>document.forma.submit();</script>
			</body>";
	}
	else
	{
		html('ERROR DE VALIDACION');
		graba_bitacora('sin_autor','O',$id,'Solicitud de Recepcion de garantia fallida por error en contrase�a.');
		echo "<script language='javascript'>
				function carga()
				{
					alert('Clave Incorrecta. Esta inconsistencia de clave erronea qued� grabada en la bitacora del registro de garant�as.');
					window.close();void(null);
				}
			</script>
			<body onload='carga()'></body>";
	}
}

function recepcion_garantia_marca_ok()
{
	global $id,$NUSUARIO;
	$Ahora=date('Y-m-d H:i:s');
	q("update sin_autor set fecha_recepcion='$Ahora',recibido_por='$NUSUARIO' where id=$id");
	graba_bitacora('sin_autor','M',$id,'Marca recepci�n de garant�a en Efectivo / Tarjeta D�bito');
	echo "<body><script language='javascript'>alert('Marcacion de recibido exitosa');window.close();void(null);opener.parent.recargar_tablero();</script></body>";
}

function registro_consignacion()
{
	global $id;
	html('SUBIR IMAGEN');
	echo "<script language='javascript'>
			function cambio_imagen_std(Campo,ruta,tamrecimg)
			{
				window.open('marcoindex.php?Acc=reg_sube_img&T=sin_autor&Id=$id&C='+Campo+'&tri='+tamrecimg+'&ruta='+ruta,'simg_'+Campo);
				document.getElementById('cerrar').style.visibility='visible';
				document.getElementById('lapiz_cambio').style.visibility='hidden';
			}
			function validar_forma()
			{
				if(!alltrim(document.forma.numero_consignacion.value)) {alert('Debe digitar el n�mero de consignaci�n');document.forma.numero_consignacion.style.backgroundColor='ffffdd';document.forma.numero_consignacion.focus();return false;}
				if(document.forma.fecha_consignacion.value=='0000-00-00 00:00:00') {alert('Debe seleccionar la fehca de consignaci�n');document.forma.fecha_consignacion.style.backgroundColor='ffffdd';return false;}
				document.forma.submit();
			}
		</script>
		<body onunload='opener.parent.recargar_tablero();'>
		<form action='zcontrol_custodia_garantia.php' method='post' target='Oculto_consignacion' name='forma' id='forma'>
			N�mero de Consignaci�n: <input type='text' name='numero_consignacion'><br />
			Fecha de Consignaci�n: ".pinta_FC('forma','fecha_consignacion',date('Y-m-d'))."<br />
			<input type='button' name='continuar' id='continuar' value='Continuar' onclick='validar_forma()'><input type='hidden' name='Acc' value='registro_consignacion_datos'>
			<input type='hidden' name='id' value='$id'>
		</form>
		<iframe name='Oculto_consignacion' id='Oculto_consignacion' style='visibility:hidden' height=1 width=1></iframe>
		<div id='img_captura' style='visibility:hidden'>
		<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='mod' id='mod'>
		<input type='hidden' name='Acc' value='subir_imagen_ok'>
		<table border=0 cellspacing=0 cellpadding=0><tr><td bgcolor='eeeeee'>";
	$Ancho=300;
	$Alto=250;
	$Info='';
	$Sub_Contenido=substr($Info,strrpos($Info,'/')+1);$Sub_Tumb='tumb_'.$Sub_Contenido;$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$Info);
	if(!file_exists($Tumb) && file_exists($Info))
	{
		if(strpos(strtolower($Sub_Contenido),'.jpg')) picresize($Info,TUMB_SIZE,'jpg',$Tumb);
		if(strpos(strtolower($Sub_Contenido),'.gif')) picresize($Info,TUMB_SIZE,'gif',$Tumb);
		if(strpos(strtolower($Sub_Contenido),'.png')) picresize($Info,TUMB_SIZE,'png',$Tumb);
	}
	echo "<iframe id='simg_consignacion_f' name='simg_consignacion_f' src='$Tumb' height='$Alto' width='$Ancho' frameborder='0' ></iframe>";
	echo "</td><td valign='top' bgcolor='efefef'>";
	echo "<a style='cursor:pointer;' class='info' onclick=\"cambio_imagen_std('consignacion_f','garantia',1000);\" id='lapiz_cambio'><img src='gifs/standar/Pencil.png' border='0'><span style='width:100px'>Cambiar la imagen</span></a><br><br>";
	echo "</td></tr></table>";
	if($_SESSION['User']==1)
		echo "<input type='text' name='consignacion_f' id='consignacion_f' value='$Info' size='20'>";
	else
		echo "<input type='hidden' name='consignacion_f' id='consignacion_f' value='$Info'>";

	echo "
		</form><center>
		<br /><b>NOTA: La carga de la im�gen tiene UNA sola oportunidad <br />y quedar� guardada en el registro tan pronto sea <br />subida al sistema. <br /><br />
		<input type='button' value='Cerrar esta ventana' style='visibility:hidden' id='cerrar' onclick='window.close();void(null);'></center>
		</body>";
}

function registro_consignacion_datos()
{
	global $id,$numero_consignacion,$fecha_consignacion;
	q("update sin_autor set numero_consignacion='$numero_consignacion',fecha_consignacion='$fecha_consignacion' where id=$id");
	graba_bitacora('sin_autor','M',$id,'Numero Consignaci�n, Fecha consignaci�n');
	echo "<body><script language='javascript'>parent.document.getElementById('img_captura').style.visibility='visible';parent.document.forma.continuar.style.visibility='hidden';</script></body>";
}

function inscribir_cuenta()
{
	global $id;
	$D=qo("Select * from sin_autor where id=$id");
	if(!$D->devol_banco)
	{
		html();
		echo "<body><script language='javascript'>centrar(1,1);alert('No tiene datos para transferencia de garant�a');window.close();void(null);</script></body>";
		die();
	}
	$Banco=qo("select * from codigo_ach where id='$D->devol_banco' ");
	$Nciudad=qo1("select t_ciudad('$D->ciudad_cuenta_devol')");
	html('INSCRIPCION DE CUENTA');
	echo "<script language='javascript'>
		function inscribir_cuenta()
		{window.open('zcontrol_custodia_garantia.php?Acc=inscribir_cuenta_ok&id=$id','Oculto_inscribir');}
		function cerrar()
		{window.close();void(null);opener.parent.recargar_tablero();}
	</script>
	<body><h3>Inscripci�n de cuenta</h3>
	<table>
	<tr ><td >Nombre:</td><td ><b>$D->devol_ncuenta</b></td></tr>
	<tr ><td >Identificaci�n:</td><td ><b>$D->identificacion_devol</b></td></tr>
	<tr ><td >Banco:</td><td ><b>$Banco->nombre</b></td></tr>
	<tr ><td >N�mero de cuenta:</td><td ><b>$D->devol_cuenta_banco</b></td></tr>
	<tr ><td >Tipo de cuenta</td><td ><b>".($D->devol_tipo_cuenta=='A'?"Ahorros":"Corriente")."</b></td></tr>
	<tr ><td >Ciudad de Radicaci�n de la cuenta</td><td >".($D->ciudad_cuenta_devol?"<b>$Nciudad</b>":"<b style='color:aa0000'>NO SE HA REGISTRADO LA CIUDAD</b><br>Debe asignarle una ciudad a la cuenta de devoluci�n antes de continuar." )."</td></tr>
	</table><br />";
	if($D->inscripcion==1)
		echo "<h3>Esta cuenta aparece como inscrita</h3>";
	elseif($D->ciudad_cuenta_devol)
		echo "<input type='button' value='Registrar inscripci�n de cuenta' onclick='inscribir_cuenta()'>";
	else
		echo "No puede registrar la cuenta sin haberle asignado la ciudad. <a href='zcontrol_custodia_garantia.php?Acc=corregir_transferencia&id=$id' target='_self'>Click Aqu� para corregirla</a>";

	echo "$G->inscripcion <iframe name='Oculto_inscribir' id='Oculto_inscribir' style='visibility:hidden' width=1 height=1></iframe></body>";
}

function inscribir_cuenta_ok()
{
	global $id;
	q("update sin_autor set inscripcion=1 where id=$id");
	echo "<body><script language='javascript'>alert('Inscripci�n satisfactoria');parent.cerrar();</script>";
}

function ver_facturas()
{
	global $id,$USUARIO;
	$Siniestro=qo("select id,numero from siniestro where id=$id");
	html("VER FACTURAS Siniestro No. $Siniestro->numero");
	echo "<script language='javascript'>
			function ver_factura(id)
			{modal('zfacturacion.php?Acc=imprimir_factura&id='+id,0,0,700,900,'factura')}
			function ver_recibo(id)
			{modal('zcartera.php?Acc=imprimir_recibo&id='+id,100,100,700,800,'recibo')}
			function ver_consignacion(id)
			{modal('zcartera.php?Acc=imprimir_recibo&id='+id,100,100,700,800,'recibo')}
			function ver_notaco(id)
			{modal('zcartera.php?Acc=imprimir_ncontable&id='+id,100,100,700,800,'recibo')}
			function ver_notacr(id)
			{modal('zcartera.php?Acc=imprimir_ncredito&id='+id,100,100,700,800,'recibo')}
		</script>
		<body><h3>VER FACTURAS SINIESTRO: $Siniestro->numero</h3>";
	$Facturas=q("select *,t_cliente(cliente) as ncliente from factura where siniestro=$id and anulada=0");
	echo "<table border cellspacing='0'><tr ><th >Factura</th><th>Cliente</th><th>Fecha</th><th>Valor</th><th width='100px'>Recibo(s) de Caja</th>
				<th width='80px'>Fecha</th><th width='80px'>Valor</th><th width='80px'>Saldo</th></tr>";
	include('inc/link.php');
	$Total_Saldos=0;
	while($F=mysql_fetch_object($Facturas))
	{
		echo "<tr ><td >$F->consecutivo  <a class='info' style='cursor:pointer' onclick='ver_factura($F->id);'><img src='gifs/standar/Preview.png' border='0'><span>Ver Factura</span></a>
				</td><td >$F->ncliente</td><td >$F->fecha_emision</td><td align='right'>".coma_format($F->total)."</td><td colspan=3>";
		$Total1=0;$Total2=0;
		$Recibosdecaja=mysql_query("select * from recibo_caja where factura=$F->id and anulado=0",$LINK);
		if(mysql_num_rows($Recibosdecaja))
		{
			echo "<table cellspacing=1>";

			while($Rc=mysql_fetch_object($Recibosdecaja))
			{
				echo "<tr ><td width='100px'>RC: $Rc->consecutivo <a class='info' style='cursor:pointer' onclick='ver_recibo($Rc->id);'><img src='gifs/standar/Preview.png' border='0'><span>Ver Recibo</span></a>
						".($Rc->consignacion_f?"<a class='info' style='cursor:pointer' onclick=\"modal('$Rc->consignacion_f',0,0,800,800,'vc');\"><img src='gifs/standar/Preview.png' border='0'><span>Ver Consignacion</span></a>":"").
						($USUARIO=1?"<a onclick=\"modal('marcoindex.php?Acc=mod_reg&NTabla=recibo_caja&id=$Rc->id',0,0,800,800,'rc');\"><img src='gifs/standar/Pencil.png' border='0'></a>":"")."</td>
						<td width='80px'>$Rc->fecha</td><td align='right' width='80px'>".coma_format($Rc->valor)."</td></tr>";
				$Total1+=$Rc->valor;
			}
			echo "<tr ><td colspan=2 bgcolor='eeeeee'>Total Pagado:</td><td align='right' bgcolor='eeeeee'><b>".coma_format($Total1)."</b></td></tr>";
			echo "</table>";
		}
		$Notas_contables=mysql_query("select * from nota_contable where factura=$F->id and anulado=0",$LINK);
		if(mysql_num_rows($Notas_contables))
		{
			echo "<table cellspacing=1>";
			while($Nc=mysql_fetch_object($Notas_contables))
			{
				echo "<tr ><td width='100px'>NCO: $Nc->consecutivo <a class='info' style='cursor:pointer' onclick='ver_notaco($Nc->id);'><img src='gifs/standar/Preview.png' border='0'><span>Ver Nota Contable</span></a> </td>
						<td width='80px'>$Nc->fecha</td><td align='right' width='80px' style='color:880000;'>".coma_format($Nc->valor)."</td></tr>";
				$Total2+=$Nc->valor;
			}
			echo "<tr ><td colspan=2 bgcolor='eeeeee'>Total Pagado:</td><td align='right' bgcolor='eeeeee' style='color:880000;'><b>".coma_format($Total2)."</b></td></tr>";
			echo "</table>";
		}
		$Notas_credito=mysql_query("select * from nota_credito where factura=$F->id and anulado=0",$LINK);
		if(mysql_num_rows($Notas_credito))
		{
			echo "<table cellspacing=1>";
			while($Nc=mysql_fetch_object($Notas_credito))
			{
				echo "<tr ><td width='100px'>NCR: $Nc->consecutivo <a class='info' style='cursor:pointer' onclick='ver_notacr($Nc->id);'><img src='gifs/standar/Preview.png' border='0'><span>Ver Nota Cr�dito</span></a> </td>
						<td width='80px'>$Nc->fecha</td><td align='right' width='80px' style='color:880000;'>".coma_format($Nc->total)."</td></tr>";
				$Total2+=$Nc->total;
			}
			echo "<tr ><td colspan=2 bgcolor='eeeeee'>Total Pagado:</td><td align='right' bgcolor='eeeeee' style='color:880000;'><b>".coma_format($Total2)."</b></td></tr>";
			echo "</table>";
		}
		

		$Saldo=$F->total-$Total1-$Total2;
		$Total_Saldos+=$Saldo;
		echo "<td align='right' width='80px'>".coma_format($Saldo)."</td>";
		echo "</tr>";
	}
	echo "</table><br /><br /><h3>COBROS PENDIENTES POR FACTURAR</3>
		<table border cellspacing='0'><tr ><th>Solicitado por</th><th>Fecha de solicitud</th><th>Concepto</th><th>Descripci�n</th><th>Valor</th></tr>";
	$Por_facturar=mysql_query("select *,t_concepto_fac(concepto) as nconcepto from solicitud_factura where  siniestro=$id and (procesado_por='' or fecha_proceso='0000-00-00 00:00:00') ",$LINK);
	$Total_pf=0;
	while($Pf=mysql_fetch_object($Por_facturar))
	{
		echo "<tr ><td >$Pf->solicitado_por</td><td >$Pf->fecha_solicitud</td><td >$Pf->nconcepto</td><td >$Pf->descripcion</td><td align='right'>".coma_format($Pf->valor)."</td></tr>";
		$Total_pf+=$Pf->valor;
	}
	echo "<tr ><td colspan=4><b>Total</b></td><td align='right'><b>".coma_format($Total_pf)."</b></td></tr></table><br /><br />
	<h2>Saldo total Cartera: ".coma_format($Total_Saldos+$Total_pf)."</h2>";
	mysql_close($LINK);
	echo "</table>";
}

function marcar_comparendos()
{
	global $id;
	html('VERIFICACION DE COMPARENDOS');
	echo "<body><h3>Marcaci�n de Verificaci�n de Comparendos</h3>
				Por seguridad se solicita la contrase�a del usuario actual para registrar esta marcaci�n.<br /><br />
				<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
					Clave de confirmaci�n: <input type='password' name='Clave' id='Clave'><br /><br />
					<input type='submit' value='CONTINUAR'>
					<input type='hidden' name='Acc' value='marcacion_comparendos_ok'>
					<input type='hidden' name='id' value='$id'>
				</form>";

}

function marcacion_comparendos_ok()
{
	global $id,$Clave;
	if(verificar_password($_SESSION['Nick'],$Clave))
	{
		echo "<body>
			<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='comparendos_marca_ok'>
				<input type='hidden' name='id' value='$id'>
			</form>
			<script language='javascript'>document.forma.submit();</script>
			</body>";
	}
	else
	{
		html('ERROR DE VALIDACION');
		graba_bitacora('sin_autor','O',$id,'Marcaci�n de verificaci�n de comparendos de garantia fallida por error en contrase�a.');
		echo "<script language='javascript'>
				function carga()
				{
					alert('Clave Incorrecta. Esta inconsistencia de clave erronea qued� grabada en la bitacora del registro de garant�as.');
					window.close();void(null);
				}
			</script>
			<body onload='carga()'></body>";
	}
}

function comparendos_marca_ok()
{
	global $id,$NUSUARIO;
	$Ahora=date('Y-m-d H:i:s');
	q("update sin_autor set comparendos=1 where id=$id");
	graba_bitacora('sin_autor','M',$id,'Marca verificaci�n de comparendos de garant�a.');
	echo "<body><script language='javascript'>alert('Marcaci�n  exitosa');window.close();void(null);opener.parent.recargar_tablero();</script></body>";
}

function lista_transferencia() // GENERA LA LISTA DE TRANSFERENCIA DE GARANTIAS
{
	global $d;
	$Hoy=date('Y-m-d');
	html();
	echo "<script language='javascript'>
					function genera_xls(){window.open('zcontrol_custodia_garantia.php?Acc=genera_xls&d=$d','Oculto_trans');}
					function genera_plano(){window.open('zcontrol_custodia_garantia.php?Acc=genera_plano&d=$d','Oculto_trans');}
				</script>";
	if($Registros=q("select a.*,t_siniestro(a.siniestro) as nsin,t_codigo_ach(a.devol_banco)  as nbanco,t_ubicacion(s.ubicacion) as nubica
								from sin_autor a, siniestro s
								where a.id in ($d 0) and a.siniestro=s.id"))
	{
		echo "<body>
			<h3>Reporte de Pago de Garant�as - $Hoy</h3>
			<table border cellspacing='0'><tr>
					<th>Numero</th>
					<th>Siniestro</th>
					<th>Ubicaci�n</th>
					<th>Tarjeta Habiente</th>
					<th>Identificaci�n</th>
					<th>Banco</th>
					<th>Cuenta</th>
					<th>Valor</th>
					<th>C.Financieros</th>
					<th>Total</th>
					</tr>";
		$Contador=1;$Total=0;
		while($R=mysql_fetch_object($Registros))
		{
			$Neto_a_transferir=$R->valor_devolucion-$R->costo_financiero;
			echo "<tr ><td align='center'>$Contador</td><td >$R->nsin</td><td >$R->nubica</td><td >$R->devol_ncuenta</td><td align='right'>".coma_format($R->identificacion_devol)."</td><td >$R->nbanco</td>
						<td >$R->devol_cuenta_banco</td><td align='right'>".coma_format($R->valor_devolucion)."</td><td align='right'>".coma_format($R->costo_financiero)."</td>
						<td align='right'>".coma_format($Neto_a_transferir)."</td></tr>";
			$Contador++;$Total+=$Neto_a_transferir;
		}
		echo "<tr ><td colspan='9' align='center' bgcolor='eeeeff'><b>TOTAL TRANSFERENCIA</b></td><td align='right' bgcolor='eeeeff'><b>".coma_format($Total)."</b></td></tr></table>";
	}
	echo "<br /><center><input type='button' value='GENERAR EXCEL' onclick='genera_xls();'><input type='button' value='GENERAR ARCHIVO PLANO' onclick='genera_plano();'>
			</center>";
	echo "</body><iframe name='Oculto_trans' id='Oculto_trans' width='1' height='1' style='visibility:hidden'></iframe></html>";
}

function lista_inscritas()
{
	global $d;
	$Hoy=date('Y-m-d');
	html();
	echo "<script language='javascript'>
					function genera_xls()
					{	window.open('zcontrol_custodia_garantia.php?Acc=genera_xls_inscritas&d=$d','Oculto_inscritas'); }
					function genera_plano_inscripcion()
					{	window.open('zcontrol_custodia_garantia.php?Acc=genera_plano_inscripcion&d=$d','Oculto_inscritas'); }
				</script>";
	if($Registros=q("select a.*,t_siniestro(a.siniestro) as nsin,t_codigo_ach(devol_banco)  as nbanco,t_ubicacion(s.ubicacion) as nubica
						from sin_autor a, siniestro s
						where a.id in ($d 0) and a.siniestro=s.id"))
	{
		echo "<body>
			<h3>Reporte de Inscripci�n de Garant�as - $Hoy</h3>
			<table border cellspacing='0'><tr>
					<th>Numero</th>
					<th>Siniestro</th>
					<th>Ubicaci�n</th>
					<th>Tarjeta Habiente</th>
					<th>Identificaci�n</th>
					<th>Banco</th>
					<th>Cuenta</th>
					<th>Tipo Cuenta</th>
					<th>Valor Garant�a</th>
					</tr>";
		$Contador=1;$Total=0;
		while($R=mysql_fetch_object($Registros))
		{
			$TC=$R->devol_tipo_cuenta=='C'?'Corriente':'Ahoros';
			echo "<tr ><td align='center'>$Contador</td><td >$R->nsin</td><td >$R->nubica</td><td >$R->devol_ncuenta</td><td align='right'>".coma_format($R->identificacion_devol)."</td><td >$R->nbanco</td>
						<td >$R->devol_cuenta_banco</td><td >$TC</td><td align='right'>".coma_format($R->valor)."</td></tr>";
			$Contador++;$Total+=$R->valor;
		}
		echo "<tr ><td colspan='8' align='center' bgcolor='eeeeff'><b>TOTAL GARANTIAS INSCRITAS</b></td><td align='right' bgcolor='eeeeff'><b>".coma_format($Total)."</b></td></tr></table>";
	}
	echo "<br /><center><input type='button' value='GENERAR EXCEL' onclick='genera_xls();'> <input type='button' value='GENERAR PLANO' onclick='genera_plano_inscripcion();'></center>";
	echo "<iframe name='Oculto_inscritas' id='Oculto_inscritas' width='1' height='1' style='visibility:hidden'></iframe></body></html>";
}

function genera_xls()
{
	global $d;
	$Hoy=date('Y_m_d');

	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=transferencias_$Hoy.xls");
	echo "<body>";
	if($Registros=q("select a.*,t_siniestro(a.siniestro) as nsin,t_codigo_ach(a.devol_banco)  as nbanco, t_ubicacion(s.ubicacion) as nubica
							 from sin_autor a, siniestro s where a.id in ($d 0) and a.siniestro=s.id"))
	{
		echo "
			<h3>Reporte de Pago de Garant�as - $Hoy</h3>
			<table border cellspacing='0'><tr>
					<th>Numero</th>
					<th>Siniestro</th>
					<th>Ubicaci�n</th>
					<th>Tarjeta Habiente</th>
					<th>Identificaci�n</th>
					<th>Banco</th>
					<th>Cuenta</th>
					<th>Valor</th>
					<th>C.Financiero</th>
					<th>Total</th>
					</tr>";
		$Contador=1;$Total=0;
		while($R=mysql_fetch_object($Registros))
		{
			$Neto_a_transferir=$R->valor_devolucion-$R->costo_financiero;
			echo "<tr ><td align='center'>$Contador</td><td >$R->nsin</td><td >$R->nubica</td><td >$R->devol_ncuenta</td><td align='right'>$R->identificacion_devol</td><td >$R->nbanco</td>
						<td >$R->devol_cuenta_banco</td><td align='right'>$R->valor_devolucion</td><td align='right'>$R->costo_financiero</td><td align='right'>$Neto_a_transferir</td></tr>";
			$Contador++;$Total+=$Neto_a_transferir;
		}
		echo "<tr ><td colspan='9' align='center' bgcolor='eeeeff'><b>TOTAL TRANSFERENCIA</b></td><td align='right' bgcolor='eeeeff'><b>$Total</b></td></tr></table>";
	}
	echo "</body>";
}

function genera_xls_inscritas()
{
	global $d;
	$Hoy=date('Y_m_d');

	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=Inscripcion_Garantias_$Hoy.xls");
	echo "<body>";
	if($Registros=q("select a.*,t_siniestro(a.siniestro) as nsin,t_codigo_ach(a.devol_banco)  as nbanco,t_ubicacion(s.ubicacion) as nubica
								from sin_autor a,siniestro s where a.id in ($d 0) and a.siniestro=s.id"))
	{
		echo "
			<h3>Reporte de Inscripci�n de Garant�as - $Hoy</h3>
			<table border cellspacing='0'><tr>
					<th>Numero</th>
					<th>Siniestro</th>
					<th>Ubicaci�n</th>
					<th>Tarjeta Habiente</th>
					<th>Identificaci�n</th>
					<th>Banco</th>
					<th>Cuenta</th>
					<th>Tipo Cuenta</th>
					<th>Valor Garant�a</th>
					</tr>";
		$Contador=1;$Total=0;
		while($R=mysql_fetch_object($Registros))
		{
			$TC=$R->devol_tipo_cuenta=='C'?'Corriente':'Ahoros';
			echo "<tr ><td align='center'>$Contador</td><td >$R->nsin</td><td >$R->nubica</td><td >$R->devol_ncuenta</td><td align='right'>$R->identificacion_devol</td><td >$R->nbanco</td>
						<td >$R->devol_cuenta_banco</td><td >$TC</td><td align='right'>$R->valor</td></tr>";
			$Contador++;$Total+=$R->valor;
		}
		echo "<tr ><td colspan='8' align='center' bgcolor='eeeeff'><b>TOTAL GARANTIAS INSCRITAS</b></td><td align='right' bgcolor='eeeeff'><b>$Total</b></td></tr></table>";
	}
	echo "</body>";
}

function genera_plano_inscripcion()
{
	global $d;
	$Fecha=date('Y-m-d');
	$DESTINO_PLANO1 = 'planos/int3_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt';
	$DESTINO_PLANO2 = 'planos/int4_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt';
	html();
	echo "<script language='javascript'>
				function bajar_archivos(dato)
				{
					if(dato==1)
					{
						window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=IC_$Fecha.txt','Oculto_baja_archivos1');
					}
					if(dato==2)
					{
						window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=IC_$Fecha.txt','Oculto_baja_archivos1');
						window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO2&Salida=ERRORES_IC_$Fecha.txt','Oculto_baja_archivos2');
					}
					if(dato==3)
					{
						window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO2&Salida=ERRORES_IC_$Fecha.txt','Oculto_baja_archivos2');
					}
				}
			</script>
	<body>
	<iframe name='Oculto_baja_archivos1' style='visibility:hidden' height=1 width=1></iframe>
	<iframe name='Oculto_baja_archivos2' style='visibility:hidden' height=1 width=1></iframe>
	";
	if($Registros=q("select a.devol_cuenta_banco as cuenta,a.devol_tipo_cuenta as tipo,
								a.devol_ncuenta as nombre,b.codigo,a.identificacion_devol
								from sin_autor a,codigo_ach b
								where  a.devol_banco=b.id and a.id in ($d 0)"))
	{
		$Fin_de_linea="\r\n";
		$Lineas='';
		$Final=",1,Si,,,,0,0,1".$Fin_de_linea;
		$Errores='';
		while($R=mysql_fetch_object($Registros))
		{

			if($R->cuenta && strlen($R->cuenta)<18)
			{
				if($R->identificacion_devol)
				{
					$Cuenta=str_replace('-','' ,$R->cuenta);$Cuenta=str_replace('.','',$Cuenta);
					$TC=$R->tipo=='C'?'1':'7';  /// TIPO CUENTA 1 PARA CORRIENTE Y 7 PARA AHORROS
					$Nombre=l('GARANTIA '.$R->nombre,30);
					$Lineas.=$Cuenta.','.$TC.','.$Nombre.','.$R->codigo.','.$R->identificacion_devol.$Final;
				}
				else
				{
					$Errores.="Identificaci�n invalida. $R->cuenta, $R->tipo, $R->nombre, $R->codigo, $R->identificacion_devol.".$Fin_de_linea;
				}
			}
			else
			{
				if($R->cuenta) $Errores.="Cuenta excede 17 caracteres. $R->cuenta, $R->tipo, $R->nombre, $R->codigo, $R->identificacion_devol.".$Fin_de_linea;
				else $Errores.="Cuenta invalida. $R->cuenta, $R->tipo, $R->nombre, $R->codigo, $R->identificacion_devol.".$Fin_de_linea;
			}
		}
		$Script='';
		if($Lineas)
		{

			if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
			$DD1 = fopen($DESTINO_PLANO1, 'w+');
			fwrite($DD1, $Lineas);
			fclose($DD1);
			$Script="<script language='javascript'>bajar_archivos(1);</script>";
		}
		if($Errores)
		{

			if(@is_file($DESTINO_PLANO2)) @unlink($DESTINO_PLANO2);
			$DD2 = fopen($DESTINO_PLANO2, 'w+');
			fwrite($DD2, $Errores);
			fclose($DD2);
			$Script=($Script?"<script language='javascript'>bajar_archivos(2);</script>":"<script language='javascript'>bajar_archivos(3);</script>");
		}
		echo $Script;
	}
	else
	{
		echo "No hay informaci�n coincidente. ";
	}
	echo "</body>";
}

function genera_plano()
{
	global $d,$NUSUARIO;
	html();
	$Fin_de_linea="\r\n";
	$Cuenta_dispersora="03170515431";
	$Fecha=date('Ymd');
	if($Registros=q("select s.*,t_siniestro(s.siniestro) as nsin,a.codigo as nbanco
							from sin_autor s,codigo_ach a where s.id in ($d 0) and s.devol_banco=a.id "))
	{
		$DESTINO_PLANO1 = 'planos/int2_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt';
		$Total_lineas="";
		echo "<script language='javascript'>function bajar_archivos() {window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=DG_$Fecha.txt','_self');}</script><body>";
		$Contador_registro=0;
		$Sumatoria=0;
		while($R=mysql_fetch_object($Registros))
		{
			$Neto_a_transferir=$R->valor_devolucion-$R->costo_financiero;
			$Cuenta=str_replace('-','' ,$R->devol_cuenta_banco);$Cuenta=str_replace('.','',$Cuenta);
			$Nit=str_pad($R->identificacion_devol,15,"0",STR_PAD_LEFT);
			$Nombre=str_pad(substr($R->devol_ncuenta,0,30),30," ",STR_PAD_RIGHT);
			$Banco=str_pad($R->nbanco,9,'0',STR_PAD_LEFT);
			$Cuenta=str_pad($Cuenta,17,' ',STR_PAD_RIGHT);
			$ILP=' '; // indicador de lugar de pago
			$Tipo_transaccion=($R->devol_tipo_cuenta=='C'?'27':'37');
			$Valor=prepara_valor($Neto_a_transferir,15,2);
			$Referencia='000000000000000000000';
			$TDI='1';
			$Oficina='00000'; // oficina pagadora si es el caso
			$Fax='               '; // fax de la oficina pagadora
			$Email='                                                                                '; // email del beneficiario si hay convenio
			$IDA='               '; // Identificacion del autorizado
			$Filler='                           '; // relleno
			$Linea="6".$Nit.$Nombre.$Banco.$Cuenta.$ILP.$Tipo_transaccion.$Valor.$Fecha.$Referencia.$TDI.$Oficina.$Fax.$Email.$IDA.$Filler.$Fin_de_linea;
			$Total_lineas.=$Linea;
			$Contador_registro++;
			$Sumatoria+=$Neto_a_transferir;
		}
		if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
		$Cabeza="1".str_pad(900174552,15,'0',STR_PAD_LEFT); // nit de la empresa originadora
		$Cabeza.='I               '; // aplicacion I: inmediata
		$Cabeza.='220'; // Clase de transaccion 220: pago a proveedores
		$Cabeza.='D GARANTIA'; // descripcion del pago
		$Cabeza.=$Fecha; // fecha de transmision
		$Cabeza.='AA'; // Secuencia de transmision si es en el mismo dia, esto debe cambiar
		$Cabeza.=$Fecha; // fecha de aplicacion de la transaccion
		$Cabeza.=str_pad($Contador_registro,6,'0',STR_PAD_LEFT); // numero de registros
		$Cabeza.=prepara_valor(0,15,2); // sumatoria debitos ==0
		$Cabeza.=prepara_valor($Sumatoria,15,2); // sumatoria creditos
		$Cabeza.=$Cuenta_dispersora; // cuenta dispersora
		$Cabeza.='D'; // tipo de cuenta D: corriente
		$Cabeza.=str_repeat(' ',144); // relleno para futuros usos
		$Cabeza.=$Fin_de_linea;
		$DD1 = fopen($DESTINO_PLANO1, 'w+');
		fwrite($DD1, $Cabeza);
		fwrite($DD1, $Total_lineas);
		fclose($DD1);
		
		//// EMAIL AUTOMATICO 
		$Email_usuario=usuario('email');
		$Ahora=date('Y-m-d H:i:s');
		$Ip=$_SERVER['REMOTE_ADDR'];
		/*enviar_gmail($Email_usuario,$NUSUARIO,'shurtado@aoacolombia.co,SEBASTIAN HURTADO',
		'','TRANSFERENCIA BANCARIA',
		"Este mensaje es automatico y corresponde a una transferencia bancaria generada por $NUSUARIO el dia $Ahora desde la ip: $Ip ",
		"$DESTINO_PLANO1,DG_$Fecha.txt");*/
		
		
		echo "<script language='javascript'>bajar_archivos();</script>";
	}
	echo "</body>";
}

function prepara_valor($Valor=0,$Enteros=13,$Decimales=0)
{
	$Entero=intval($Valor);
	$Decimal=round($Valor-$Entero,$Decimales);
	$Cadena1=strval($Entero);
	$Resultado=str_pad($Cadena1,$Enteros,'0',STR_PAD_LEFT);
	if($Decimales)
	{
		$Cadena2=strval($Decimal);
		$Resultado.=str_pad(substr($Cadena2,strpos($Cadena2,'.')+1,$Decimales),$Decimales,'0',STR_PAD_RIGHT);
	}
	return $Resultado;
}

function corregir_transferencia()
{
	global $id;
	html("CORRECCION DE TRANSFERENCIA");
	echo "<body><h3>Correcci�n de Informaci�n de Transferencia Electr�nica</h3>
				Por seguridad se solicita la contrase�a del usuario actual para registrar esta operaci�n.<br /><br />
				<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
					Clave de confirmaci�n: <input type='password' name='Clave' id='Clave'><br /><br />
					<input type='submit' value='CONTINUAR'>
					<input type='hidden' name='Acc' value='corregir_transferencia1'>
					<input type='hidden' name='id' value='$id'>
				</form>";
}

function corregir_transferencia1()
{
	global $id,$Clave;
	if(verificar_password($_SESSION['Nick'],$Clave))
	{
		echo "<body>
			<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='corregir_transferencia2'>
				<input type='hidden' name='id' value='$id'>
			</form>
			<script language='javascript'>document.forma.submit();</script>
			</body>";
	}
	else
	{
		html('ERROR DE VALIDACION');
		graba_bitacora('sin_autor','O',$id,'Modifiaci�n de Transferencia Electr�nica de garant�a fallida por error en contrase�a.');
		echo "<script language='javascript'>
				function carga()
				{
					alert('Clave Incorrecta. Esta inconsistencia de clave erronea qued� grabada en la bitacora del registro de garant�as.');
					window.close();void(null);
				}
			</script>
			<body onload='carga()'></body>";
	}

}

function corregir_transferencia2()
{
	global $id;
	$D=qo("select * from sin_autor where id=$id");
	$Nciudad=($D->ciudad_cuenta_devol?qo1("select t_ciudad('$D->ciudad_cuenta_devol')"):" Click aqu� ");
	html("CORRECCION DE TANSFERENCIA GARANTIA");
	echo "<script language='javascript'>
			function valida()
			{
				with(document.forma)
				{
					if(!alltrim(obs_transferencia.value)) {alert('Debe escribir el motivo por el cual se hace correcci�n de la inscripci�n de la cuenta');obs_transferencia.style.backgroundColor='ffffdd';return false;}
				}
				document.forma.submit();
			}
			function busqueda_ciudad2(Campo,Contenido)
		{
			var Ventana_ciudad=document.getElementById('Busqueda_Ciudad');
			Ventana_ciudad.style.visibility='visible';Ventana_ciudad.style.left=mouseX;Ventana_ciudad.style.top=mouseY-10;Ventana_ciudad.src='inc/ciudades.html';
			Ciudad_campo=Campo;Ciudad_forma='forma';
		}
		function oculta_busca_ciudad()
		{document.getElementById('Busqueda_Ciudad').style.visibility='hidden';}
		</script>
		<body ><script language='javascript'>centrar(700,550);</script>
		<h3>Correcci�n de Informaci�n de Transferencia Electr�nica de Garant�a.</h3>
		<iframe id='Busqueda_Ciudad' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#eeffee;z-index:200;' height='400px' width='200px' ></iframe>
		<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
			N�mero de cuenta: <input type='text' name='devol_cuenta_banco' value='$D->devol_cuenta_banco'><br />
			Nombre del cuentahabiente: <input type='text' name='devol_ncuenta' value='$D->devol_ncuenta' size='50' maxlength='50'><br />
			Identificaci�n del cuentahabiente: <input type='text' name='identificacion' value='$D->identificacion_devol' size=15 maxlength='15' class='numero'></br>
			Banco: ".menu1("devol_banco","select id,nombre from codigo_ach where codigo!='' order by nombre",$D->devol_banco)."<br />
			Tipo de cuenta: <select name='devol_tipo_cuenta' id='devol_tipo_cuenta'><option value=''></option><option value='A' ".($D->devol_tipo_cuenta=='A'?'selected':'').">Ahoros</option>
			<option value='C' ".($D->devol_tipo_cuenta=='C'?'selected':'').">Corriente</option></select><br />
			Ciudad de radicaci�n de la Cuenta : <input type='text' style='color:#000099;background-color:#FFFFFF;' name='_ciudad' id='_ciudad' size='30' onclick=\"busqueda_ciudad2('ciudad','05001000');\" value='$Nciudad' readonly><input type='hidden' name='ciudad' id='ciudad' value='$D->ciudad_cuenta_devol'><span id='bc_ciudad'></span> Utilice el mouse para seleccionar la ciudad.<br>
			Motivo por el cual se hace correcci�n de la cuenta: <br />
			<textarea name='obs_transferencia' style='font-family:arial;font-size:11px;' cols=80 rows=4></textarea><br />
			<br /><input type='button' value='Continuar' onclick='valida();'>
			<input type='hidden' name='Acc' value='corregir_transferencia2_ok'>
			<input type='hidden' name='id' value='$id'>
		</form>
		</body>";
}

function corregir_transferencia2_ok()
{
	global $id,$devol_cuenta_banco,$devol_ncuenta,$devol_banco,$devol_tipo_cuenta,$obs_transferencia,$NUSUARIO,$identificacion,$ciudad;
	$Ahora=date('Y-m-d H:i:s');
	q("update sin_autor set devol_cuenta_banco='$devol_cuenta_banco',devol_ncuenta='$devol_ncuenta',devol_banco='$devol_banco',
			devol_tipo_cuenta='$devol_tipo_cuenta',identificacion_devol='$identificacion',metodo_devol='',inscripcion=0,ciudad_cuenta_devol='$ciudad',
			obs_transferencia=concat(obs_transferencia,\"\n$NUSUARIO [$Ahora]: $obs_transferencia\") where id=$id");
	graba_bitacora('sin_autor','M',$id,'devol_cuenta_banco, devol_ncuenta, devol_banco, devol_tipo_cuenta,metodo_devol. Corrige informaci�n de transferencia de garant�a');
	echo "<body><script language='javascript'>alert('Correci�n registrada sastisfactoriamente');window.close();void(null);</script></body>";
}

function inserta_obs_transferencia()
{
	global $id;
	$D=qo("select * from sin_autor where id=$id");
	html("OBSERVACIONES DE TANSFERENCIA GARANTIA");
	echo "<script language='javascript'>
		function valida()
		{
			with(document.forma)
			{
				if(!alltrim(obs_transferencia.value)) {alert('Debe escribir el motivo por el cual se hace correcci�n de la inscripci�n de la cuenta');obs_transferencia.style.backgroundColor='ffffdd';return false;}
			}
			document.forma.submit();
		}
	</script>
	<body >
	<h3>Observaciones de Transferencia Electr�nica de Garant�a.</h3>
	<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
		Observaciones: <br />
		<textarea name='obs_transferencia' style='font-family:arial;font-size:11px;' cols=80 rows=4></textarea><br />
		<br /><input type='button' value='Continuar' onclick='valida();'>
		<input type='hidden' name='Acc' value='inserta_obs_transferencia_ok'>
		<input type='hidden' name='id' value='$id'>
	</form>
	</body>";
}

function inserta_obs_transferencia_ok()
{
	global $id,$obs_transferencia,$NUSUARIO;
	$Ahora=date('Y-m-d H:i:s');
	$sql = "update sin_autor set obs_transferencia=concat(if(obs_transferencia is null,'',obs_transferencia),\"\n$NUSUARIO [$Ahora]: $obs_transferencia\") where id=$id";	
	q($sql);
	graba_bitacora('sin_autor','M',$id,'obs_transferencia');
	echo "<body><script language='javascript'>alert('Correci�n registrada sastisfactoriamente');window.close();void(null);</script></body>";
}


















?>