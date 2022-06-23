<?php

/**
 *    SOFTWARE PARA CONTROL DE CUSTODIA Y ESTADO DE GARANTIAS
 *
 * @version $Id$
 * @copyright 2011
 */
include('inc/funciones_.php');
include("inc/smtp2/class.phpmailer.php");

sesion();
$USUARIO=$_SESSION['User'];
$NUSUARIO=$_SESSION['Nombre'];
if(!$FECHAI)  $FECHAI=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-7)));
if(!$FECHAF)  $FECHAF=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-7)));

if($USUARIO==10 /* operario de oficina  */)
{
	$OFIU=qo1("select oficina from usuario_oficina where id=".$_SESSION['Id_alterno']);
	$OFIU=qo1("select ciudad from oficina where id=$OFIU");
}
if($USUARIO==32 /*  recepcion */)
{
	$OFIU=qo1("select oficina from usuario_recepcion where id=".$_SESSION['Id_alterno']);
	$OFIU=qo1("select ciudad from oficina where id=$OFIU");
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
	<a onclick=\"modal('zanalisis_comparendos.php',0,0,600,600,'acomp');\" style='cursor:pointer;'>Verificación Comparendos </a> Nits Propietarios: $Propiets
	<form action='zcontrol_custodia_garantia.php' method='post' target='Tablero_garantia' name='forma' id='forma'>
		Fecha inicial : ".pinta_FC('forma','FECHAI',$FECHAI)." Fecha Final: ".pinta_fc('forma','FECHAF',$FECHAF);
	if(!inlist($USUARIO,10,32))
		echo " Oficina: ".menu1("OFIU","select ciudad,nombre from oficina ",$OFIU,1);
	echo " Ver solo <select name='FILTRO'><option value=''>Todos</option><option value='E'>Efectivos  TD</option><option value='C'>Tarjetas Crédito</option><option value='P'>Pagares</option></select>
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
				if(dato==true)
				{
					Arr_transf[Arr_transf.length]=id;Monto_transf+=valor;
					document.getElementById('btn_transferir').style.visibility='visible';
				}
				else
				{
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
				for(var i=0;i<Arr_transf.length;i++)
				{
					Cadena+=Arr_transf[i]+',';
				}
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

			function subir_imagen(id)
			{modal('zcontrol_custodia_garantia.php?Acc=subir_imagen&id='+id,0,0,500,400,'si');}
			function ver_imagen(id)
			{modal('zcontrol_custodia_garantia.php?Acc=ver_imagen&id='+id,0,0,500,500,'si');}
			function subir_imagen2(id)
			{modal('zcontrol_custodia_garantia.php?Acc=subir_imagen2&id='+id,0,0,500,400,'si');}
			function ver_imagen2(id)
			{modal('zcontrol_custodia_garantia.php?Acc=ver_imagen2&id='+id,0,0,500,500,'si');}
			function actualiza_info(id)
			{modal('zautorizaciones.php?Acc=actualizar_info&idauto='+id,0,0,100,100,'auinfo');}
			function cambio_modo(id,dato)
			{if(confirm('Desea definir el método de devolución como '+dato+'?')){window.open('zcontrol_custodia_garantia.php?Acc=cambio_metodo_devolucion&id='+id+'&modo='+dato,'Oculto_garantia');	}}
			function enviar_mail_garantia(id)
			{window.open('zcontrol_custodia_garantia.php?Acc=enviar_email_garantia&id='+id,'Oculto_garantia');}
			function inserta_obs(id)
			{modal('zcontrol_custodia_garantia.php?Acc=inserta_obs&id='+id,0,0,100,100,'insobs');}
			function recepcion_garantia(id)
			{modal('zcontrol_custodia_garantia.php?Acc=recepcion_garantia&id='+id,0,0,200,400,'recepcion');}
			function registro_consignacion(id)
			{modal('zcontrol_custodia_garantia.php?Acc=registro_consignacion&id='+id,0,0,500,400,'recepcion');}
			function inscribir_cuenta(id)
			{modal('zcontrol_custodia_garantia.php?Acc=inscribir_cuenta&id='+id,0,0,200,400,'recepcion');}
			function ver_facturas(id)
			{modal('zcontrol_custodia_garantia.php?Acc=ver_facturas&id='+id,0,0,600,900,'facturas');}
			function marcar_comparendos(id)
			{modal('zcontrol_custodia_garantia.php?Acc=marcar_comparendos&id='+id,0,0,200,400,'recepcion');}
			function solicitar_factura(id)
			{modal('zcitas.php?Acc=solicitar_factura&cita='+id,0,0,400,500,'sfac');}
			function corregir_transferencia(id)
			{modal('zcontrol_custodia_garantia.php?Acc=corregir_transferencia&id='+id,0,0,400,600,'corregir');}
			function ver_recibo(id)
			{modal('zcartera.php?Acc=imprimir_recibo&id='+id,100,100,700,800,'recibo')}
			function ver_foto(dato)
			{modal('../../Administrativo/'+dato,0,0,400,400,'foto');}
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

	$Consulta_garantias="select a.*,s.numero as nsin,s.asegurado_nombre,s.asegurado_id,ase.nombre as naseguradora,ciu.nombre as nciudad,
								s.img_inv_salida_f as acta1,img_inv_entrada_f as acta2,frq.nombre as nfranq,s.fecha_final as fdevolvh,s.id as sid,a.id as aid
								FROM sin_autor a,siniestro s,franquisia_tarjeta frq,aseguradora ase,ciudad ciu
								WHERE a.siniestro=s.id and a.franquicia=frq.id  and s.fecha_final between '$FECHAI' and '$FECHAF' and a.estado='A'
								and a.aut_fac=0 and ase.id=s.aseguradora and s.ciudad=ciu.codigo ".($OFIU?" and s.ciudad=$OFIU ":"")." $Filtro_query 
								ORDER BY fecha_solicitud ";
	if($IDsiniestro) $Consulta_garantias="select a.*,s.numero as nsin,s.asegurado_nombre,s.asegurado_id,ase.nombre as naseguradora,ciu.nombre as nciudad,
								s.img_inv_salida_f as acta1,img_inv_entrada_f as acta2,frq.nombre as nfranq,s.fecha_final as fdevolvh,s.id as sid,a.id as aid
								FROM sin_autor a,siniestro s,franquisia_tarjeta frq,aseguradora ase,ciudad ciu
								WHERE a.siniestro=s.id and a.franquicia=frq.id  and a.estado='A' and s.id=$IDsiniestro and a.aut_fac=0 and ase.id=s.aseguradora and 
								s.ciudad=ciu.codigo ORDER BY fecha_solicitud ";

	if($Garantias=q($Consulta_garantias))
	{
		$NT=tu('sin_autor','id');
		
		echo "<table align='center' border cellspacing='0' bgcolor='ffffff'><tr>
						<th colspan=14>DATOS DE LA GARANTIA</th><th colspan=3>CUSTODIA AOA</th><th colspan=4>AUDITORIA</th><th colspan=6>DEVOLUCION</th></tr>
						<tr><th>#</th><th>Aseguradora</th><th>Siniestro</th><th>Vehículo</th><th>Acta</th><th>Asegurado</th><th>Identificación</th><th>Tarjeta Habiente</th>
						<th>Identificación</th><th>Ciudad</th><th>Fecha Solicitud</th><th>Devolución.Vh.</th><th>Franquicia</th><th>Monto</th>
						<th>Recepción</th><th>Consignación</th><th>Inscripción</th>
						<th>Facturado</th><th>Pagado</th><th>Saldo<br />por cobrar</th><th>Comparendos</th>
						<th>A transferir</th><th>Actualizar</th><th>Imágen</th><th>Método</th><th>Fec.Devolución</th><th>Email</th></tr>";
		include('inc/link.php');
		$Contador=0;
		$IDSin=$IDAut='';
		while($G=mysql_fetch_object($Garantias))
		{
				$IDSin.=($IDSin?",":"").$G->sid;
				$IDAut.=($IDAut?",":"").$G->aid;
				$Contador++;
				echo "<tr ".($USUARIO==1?" ondblclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NT&id=$G->id',0,0,500,500,'mod')\" ":"").">
						<td bgcolor='ffffff' align='center'>".coma_format($Contador)."</td>
						<td bgcolor='ffffff'>$G->naseguradora</td>
						<td bgcolor='ffffff'>$G->nsin </td>
						<td bgcolor='ffffff' align='center' id='pl_$G->sid'></td>
						<td align='center' nowrap='yes' bgcolor='ffffff'>";
				if(!$Excel)
						echo "
							<a class='info' style='cursor:pointer' onclick=\"modal('$G->acta1',0,0,700,900,'acta');\"><img src='gifs/standar/Preview.png' border='0'><span>Ver el Acta de Entrega</span></a>
							<a class='info' style='cursor:pointer' onclick=\"modal('$G->acta2',0,0,700,900,'acta');\"><img src='gifs/standar/Preview.png' border='0'><span>Ver el Acta de Devolución</span></a>";

				echo "
						</td>
						<td bgcolor='ffffff'>$G->asegurado_nombre</td>
						<td align='right' bgcolor='ffffff'>".coma_format($G->asegurado_id)."</td>
						<td bgcolor='ffffff'>$G->nombre <span id='fo_$G->sid'></span></td>
						<td align='right' bgcolor='ffffff'>".coma_format($G->identificacion)."</td>
						<td bgcolor='ffffff'>$G->nciudad</td>
						<td bgcolor='ffffff'>".date('Y-m-d',strtotime($G->fecha_solicitud))."</td>
						<td bgcolor='ffffff' align='center'>$G->fdevolvh</td>
						<td nowrap='yes' bgcolor='ffffff'>$G->nfranq <br><span id='rcp_$G->sid'></span><span id='rc_$G->aid'></span>";
					
				if($G->rc && $G->rc_id)
				{
						echo "<br />Recibo de Caja: <b>$G->rc ";
						if(!$Excel) echo "<a class='info' style='cursor:pointer' onclick='ver_recibo($G->rc_id);'><img src='gifs/standar/Preview.png' border='0'><span>Ver Recibo</span></a>";
						echo "</b>";
				}
				$Color_custodia='ddddff';
				$Color_auditoria='ffffcc';
				echo "</td><td align='right' bgcolor='ffffff'><b>".(!$Excel?coma_format($G->valor):$G->valor)."</b></td><td align='center' bgcolor='$Color_custodia'>";
				//////////////////////   REGISTRO DE RECEPCION DE LA GARANTIA ////////////////////////////////////
				if($G->recibido_por)
				{
					if(!$Excel) echo "<a class='info'>$G->fecha_recepcion<span style='width:200px'>Recibido por: $G->recibido_por</span></a>";
					else echo "$G->fecha_recepcion";
				}
				else
				{
					if(!$Excel) echo "<a class='info' style='cursor:pointer' onclick='recepcion_garantia($G->id);'><img src='gifs/standar/calendario_siguiente.png' border='0'><span>Registrar Recepción de Garantía</span></a>";
				}
				echo "</td><td align='center' bgcolor='$Color_custodia'>";
				//////////////////////   REGISTRO DE CONSIGNACION DE LA GARANTIA ////////////////////////////////////
				if($G->fecha_consignacion && $G->numero_consignacion)
				{
					echo "$G->fecha_consignacion<br />$G->numero_consignacion ";
					if(!$Excel)  echo "<a class='info' style='cursor:pointer' onclick=\"modal('$G->consignacion_f',0,0,700,900,'consignacion')\"><img src='gifs/standar/Preview.png' border='0'><span>Ver Consignación</span></a>";
				}
				elseif($G->recibido_por)
				{
					IF(!$Excel) echo "<a class='info' style='cursor:pointer' onclick='registro_consignacion($G->id);'><img src='gifs/standar/nuevo_registro.png' border='0'><span>Adicionar Consignación</span></a>";
				}
				echo "</td><td align='center' bgcolor='$Color_custodia' nowrap='yes'>";
				//////////////////////  REGISTRO DE LA INSCRIPCION DE LA CUENTA  //////////////////////////////////////
				if($G->inscripcion)
				{
					IF(!$Excel) echo "<a style='cursor:pointer'><img src='gifs/standar/si.png' border='0' alt='Inscrita' title='Inscrita'></a>"; else echo "Inscrita";

					if($INSCRIBE_CUENTAS)
						IF(!$Excel) echo "<br /><a class='info' onclick='corregir_transferencia($G->id);'><img src='gifs/standar/regresar_ovr.png' border='0'><span>Corregir Cuenta de Transferencia</span></a>";
				}
				elseif($G->numero_consignacion)
				{
					if($INSCRIBE_CUENTAS)
					{
						IF(!$Excel)
						{
							echo "<a class='info' style='cursor:pointer' onclick='inscribir_cuenta($G->id);'><img src='gifs/standar/derecha.png' border='0'><span>Inscribir Cuenta</span></a>";
							echo "<input type='checkbox' name='rinscritas$G->id' onchange='incluye_inscritas($G->id,this.checked);' alt='Marque para generar reporte de inscripciones' title='Marque para generar reporte de inscripciones'>";
						}
					}
				}
				IF(!$Excel) echo "<a class='info' style='cursor:pointer' onclick=\"modal('zcontrol_custodia_garantia.php?Acc=inserta_obs_transferencia&id=$G->id',0,0,500,500,'obs');\">
								<img src='gifs/mas.gif' border='0'>".($G->obs_transferencia?"...":"")."<span style='width:300px'><table><tr><td width='300px'>".
							($G->obs_transferencia?"<b>Observaciones:</b><hr>".nl2br($G->obs_transferencia)."<hr>":"")."Insertar observaciones de transferencia</td></tr></table></span></a>";

				if($G->obs_transferencia && $Excel) echo nl2br($G->obs_transferencia);
				echo "</td><td align='right' bgcolor='$Color_auditoria' id='fa_$G->sid'></td><td align='right' bgcolor='$Color_auditoria' id='fap_$G->sid'></td><td align='right' bgcolor='$Color_auditoria' id='saldo_$G->sid'></td>";
				//////////////////////  BUSQUEDA DE FACTURAS /////////////////////////////////
	/*			$Facturas=mysql_query("select * from factura where siniestro=$G->siniestro and anulada=0 ",$LINK);
				$Facturado=0;$Pagado=0;$Notas=0;$Saldo=0;$Rcp=0;
				while($F=mysql_fetch_object($Facturas))
				{
					$Facturado+=$F->total;
					$Pagado+=qo1m("select sum(valor) from recibo_caja where factura=$F->id and anulado=0 ",$LINK);
					$Notas+=qo1m("select sum(valor) from nota_contable where factura=$F->id and anulado=0 ",$LINK);
					$Rcp+=qo1m("select sum(valor) from recibo_caja where factura=$F->id and (recibo_provisional!=0 or garantia=1) and anulado=0",$LINK);
				}
				$Por_facturar=qo1m("select sum(valor) from solicitud_factura where siniestro=$G->siniestro and (procesado_por='' or fecha_proceso='0000-00-00 00:00:00') ",$LINK);
				if($Facturado || $Por_facturar)
				{
					$Saldo=$Facturado+$Por_facturar-$Pagado-$Notas;
					echo "".coma_format($Facturado+$Por_facturar)."<br />";
					IF(!$Excel)
						echo "<a class='info' style='cursor:pointer' onclick='ver_facturas($G->siniestro);'><img src='gifs/standar/Preview.png' border='0'><span>Ver detalle de facturas</span></a>";

					echo "</td><td align='right' bgcolor='$Color_auditoria'>".coma_format($Pagado+$Notas)."</td><td align='right' bgcolor='$Color_auditoria'>".coma_format($Saldo)."</td>";
					if($Saldo>0) $G->obs_devolucion.="\n".'[CONTROL AUTOMATICO '.date('Y-m-d h:i A').'] Saldo pendiente por cobrar: $'.coma_format($Saldo);
				}
				else
					echo "</td><td  bgcolor='$Color_auditoria'></td><td  bgcolor='$Color_auditoria'></td>";
*/				/////////////////////  REGISTRO DE VERIFICACION DE COMPARENDOS //////////////////////////////////
				if($G->comparendos)
				{
					if(!$Excel) echo "<td align='center'><img src='gifs/standar/si.png' border='0'></td>";
					else echo "<td align='center'>Revisado</td>";
				}
				else
				{
					echo "<td align='center' bgcolor='$Color_auditoria'>";
					IF(!$Excel) echo "<a class='info' style='cursor:pointer' onclick='marcar_comparendos($G->id);'><img src='gifs/standar/edita_registro.png' border='0'><span>Marcar Verificación de comparendos</span></a>";
					if($Cita=qo1m("select id from cita_servicio where siniestro=$G->siniestro and estado='C' ",$LINK))
					{
						IF(!$Excel) echo "<a class='info' style='cursor:pointer' onclick='solicitar_factura($Cita);'><img src='gifs/standar/nuevo_ovr.png' border='0'><span>Solicitar Factura</span></a> </td>";
					}
				}
				////////////////////  REGISTRO DE DATOS DE LA TRANSFERENCIA  ////////////////////////////////////////////////////////
				////////-----------monto a transferir --------------------
				if($G->inscripcion && $G->numero_consignacion)
					$A_transferir=$G->valor-$Saldo-$Notas-$Rcp;
				else
					$A_transferir=0;
				if($A_transferir<0)	echo "<td align='right' bgcolor='ffcccc'>".coma_format($A_transferir)."</td>";
				elseif($A_transferir>0 )
				{
					echo "<td align='right' bgcolor='ddffdd'>".coma_format($A_transferir)."</td>";
					mysql_query("update sin_autor set valor_devolucion='$A_transferir' where id=$G->id");
				}
				else echo "<td ></td>";
				////////------------------------------
				echo "<td align='center' nowrap='yes'>";
				if($G->email && !$Excel)	echo "<a class='info' ><img src='img/arroba.png' border='0' height='20'><span style='width:300px'>Correo: $G->email</span></a> ";
				IF(!$Excel) echo "<a class='info' style='cursor:pointer' onclick='actualiza_info($G->id);'><img src='img/actualizar.png' border='0' height='20'><span style='width:200px'>Actualizar información del asegurado</span></a>";
				if(inlist($USUARIO,'1,2,3'))
				{
					IF(!$Excel) echo "<a class='info' style='cursor:pointer' href=\"javascript:modal('zcontrol_custodia_garantia.php?Acc=inserta_obs&id=$G->id',0,0,500,500,'obs');\">
								<img src='gifs/mas.gif' border='0'><span>Insertar observaciones de devolucion</span></a>";
				}
				echo "</td><td align='center'>";
				if(!$G->metodo_devol)
				{
					if($A_transferir>0 )
					{
						if($INSCRIBE_CUENTAS)
						{
							if($G->comparendos && $G->inscripcion && $G->numero_consignacion)
							{
								IF(!$Excel) echo "<input type='checkbox' name='transf$G->id' onchange='incluye_transferencia($G->id,$A_transferir,this.checked);'>";
							}
						}
					}
				}
				IF(!$Excel)
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
				echo "<td align='center'>";
				if($G->obs_devolucion)
				{
					IF(!$Excel) echo "<a class='rinfo'><img src='img/alto.gif' border='0' height='30'><span><table width='200px'><tr><td>$G->obs_devolucion</td></tr></table></span></a>";
				}
				else
				{
					if($G->devolucion_f || $G->devolucion2_f || $INSCRIBE_CUENTAS)
					{
						if($G->metodo_devol && $Excel) echo "$G->metodo_devol ";
						else
							echo "<select onchange='cambio_modo($G->id,this.value);' ".($G->metodo_devol?"disabled":"")."><option value=''></option>
									<option value ='ANULADO' ".($G->metodo_devol=='ANULADO'?"selected":"").">Anulado</option>
									<option value ='CONSIGNADO' ".($G->metodo_devol=='CONSIGNADO'?"selected":"").">Consignado</option>
									<option value ='EN PERSONA' ".($G->metodo_devol=='EN PERSONA'?"selected":"").">En persona</option>
									<option value ='TRANSFERENCIA' ".($G->metodo_devol=='TRANSFERENCIA'?"selected":"").">Transferencia</option>
									<option value ='COBRO' ".($G->metodo_devol=='COBRO'?"selected":"").">Cobro</option>
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
		echo "</table><iframe name='Oculto_garantia2' id='Oculto_garantia2' style='visibility:visible' width='100%' height='200'></iframe>
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
	var $siniestro=0;
	var $facturado=0;
	var $pagado=0;
	var $saldo=0;
	function fsiniestro($dato) {$this->siniestro=$dato;}
	function factura($valor)	{$this->facturado+=$valor;}
	function pago($valor) {$this->pagado+=$valor;$this->saldo=$this->facturado-$this->pagado;}
}

function consultar_garantias2()
{
	global $BDA,$IDSin,$IDAut;
	$tmpi='tmpi_gar'.$_SESSION['Id_alterno'].'_'.$_SESSION['User'];
	include('inc/link.php');
	$Datos1=mysql_query("select s.id,t_veh_ub(s.ubicacion) as placa,foto_recepcion(s.id) as foto,t_rcp(s.id) as rcp from siniestro s where s.id in ($IDSin) ",$LINK);
	$Datos2=mysql_query("Select a.id,t_rc_id(a.id) as rc_id,t_rc(a.id) as rc from sin_autor a where a.id in ($IDAut) ",$LINK);
	mysql_query("drop table if exists $tmpi",$LINK);
	mysql_query("create table $tmpi Select  siniestro,total as facturado,id as idf from factura where siniestro in ($IDSin) and anulada=0 
								union select siniestro,valor as facturado,(0000000) as idf from solicitud_factura where siniestro in ($IDSin) and (procesado_por='' or fecha_proceso='0000-00-00 00:00:00')",$LINK);
	$APagado=array();
	$DFacturas=mysql_query("select * from $tmpi ",$LINK);
	while($F=mysql_fetch_object($DFacturas))
	{
		$APagado[$F->siniestro]= new fsiniestro($F->siniestro);
		$APagado[$F->siniestro]->factura($F->facturado);
		if($F->idf)
		{
			$Pagado=qo1m("select sum(valor) from recibo_caja where factura=$F->idf and anulado=0 ",$LINK);
			$Notas=qo1m("select sum(valor) from nota_contable where factura=$F->idf and anulado=0 ",$LINK);
			$Notasc=qo1m("select sum(total) from nota_credito where factura=$F->idf and anulado=0",$LINK);
			$Tpagado=$Pagado+$Notas+$Notasc;
			if($Tpagado) $APagado[$F->siniestro]->pago($Tpagado);
		}
	}
//	$Facturado=mysql_query("select siniestro,sum(facturado) as tfac from $tmpi group by siniestro order by siniestro",$LINK);
	mysql_close($LINK);
	echo "<script language='javascript'>
	function pinta1(id,dato,dato1,dato2)
	{
		if(dato) parent.document.getElementById('pl_'+id).innerHTML=dato;
		if(dato1) parent.document.getElementById('fo_'+id).innerHTML=\"<a onclick=\\\"ver_foto('\"+dato1+\"');\\\"><img src='../../Administrativo/\"+dato1+\"' border='0' height='16'></a>\";		
		if(dato2) parent.document.getElementById('rcp_'+id).innerHTML='RCP <b>'+dato2+'</b>';		
	}
	function pinta2(id,dato,dato1)
	{if(dato) parent.document.getElementById('rc_'+id).innerHTML=\"Recibo Caja <b>\"+dato1+\"</b> <a class='info' style='cursor:pointer' onclick='ver_recibo(\"+dato+\");'><img src='gifs/standar/Preview.png' border='0'><span>Ver Recibo</span></a>\";}
	function pinta3(id,dato)
	{parent.document.getElementById('fa_'+id).innerHTML=dato+\"<a class='info' style='cursor:pointer' onclick='ver_facturas(\"+id+\");'><img src='gifs/standar/Preview.png' border='0'><span>Ver detalle de facturas</span></a>\";}
	function pinta4(id,dato)
	{parent.document.getElementById('fap_'+id).innerHTML=dato;}
	function pinta5(id,dato)
	{parent.document.getElementById('saldo_'+id).innerHTML=dato;}
	</script><body><script language='javascript'>";
	while($D=mysql_fetch_object($Datos1)) echo "\npinta1($D->id,'$D->placa','$D->foto','$D->rcp');";
	while($D=mysql_fetch_object($Datos2)) if($D->rc_id) echo "\npinta2($D->id,'$D->rc_id','$D->rc');";
	foreach($APagado as $id => $Dato)
	{
		echo "\npinta3($Dato->siniestro,'".coma_format($Dato->facturado)."');";
		echo "\npinta4($Dato->siniestro,'".coma_format($Dato->pagado)."');";
		if($Dato->saldo) echo "\pinta5($Dato->siniestro,'".coma_format($Dato->saldo)."');";
	}
//	while($D=mysql_fetch_object($Facturado)) echo "\npinta3($D->siniestro,'".coma_format($D->tfac)."');";
//	foreach($APagado as $id => $Total) echo "\npinta4($id,'".coma_format($Total)."');";
	
	echo "\nalert('Carga finalizada');</script>";
	print_r($APagado);
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
		<br /><b>NOTA: La carga de la imágen tiene UNA sola oportunidad <br />y quedará guardada en el registro tan pronto sea <br />subida al sistema. <br /><br />
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
		<br /><b>NOTA: La carga de la imágen tiene UNA sola oportunidad <br />y quedará guardada en el registro tan pronto sea <br />subida al sistema. <br /><br />
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
	global $id;
//	if($_SESSION['User']!=1)
//	{
//		echo "<body><script language='javascript'>alert('Proceso en revisión, el mensaje no se enviará. Por favor intente en horas de la tarde.');</script></body>";
//		die();
//	}
	$Hoy=date('Y-m-d');
	$NUSUARIO=$_SESSION['Nombre'];
	$G=qo("select * from sin_autor where id=$id");
	$Sin=qo("select numero,t_aseguradora(aseguradora) as naseguradora,ubicacion from siniestro where id=$G->siniestro");
	$U=qo("select * from ubicacion where id=$Sin->ubicacion ");
	$Mensaje1="<body>Estimado(a) Senor(a) ";
	if($G->devol_ncuenta) $Nombrea=$G->devol_ncuenta;
	elseif($G->nombre) $Nombrea=$G->nombre;
	$Email_usuario=usuario('email');
	$Mensaje1.="$Nombrea:<br><br>Por medio de este email se le esta enviando documento de comprobacion de devolucion de Garantia por el servicio prestado entre ";
	$Mensaje1.="los dias  $U->fecha_inicial y $U->fecha_final correspondientea al siniestro numero $Sin->numero de la aseguradora $Sin->naseguradora ";
	$Ruta_link="utilidades/Operativo/operativo.php?Acc=descargar_imagen_garantia&id=$id&Fecha=$Hoy";
	$Mensaje1.="<br><br>Para ver el documento por favor <a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='$Ruta_link';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> click aqui para descargar. </u></a> <br>";
	$Mensaje1.="<br><br>Atentamente:<br><br>$NUSUARIO<br>Departamento de Autorizaciones<br>AOA Colombia S.A.<br>PBX 57(1) 7560510 FAX 57 (1) 7560512<br>Carrera 69B Nro. 98 A -10 B. Morato<br>Bogota D.C - Colombia<br>Email: $Email_usuario";
	$Mensaje1.="<br><br>Nota: se eliminaron las tildes por compatibilidad con los servicios de correo electronico.</body>";
//	$Email_usuario=usuario('email2');
	$Exito=enviar_gmail($Email_usuario /*de */,$NUSUARIO /*Nombre de */ ,
						"$G->email,$Nombrea;henrygonzalez@aoacolombia.com,Henry Gonzalez" /*para */,
						"$Email_usuario,$NUSUARIO" /*con copia*/,
						"AOA Garantia de Servicio $Sin->numero" /*Objeto */,
						$Mensaje1);

	if($Exito)
	{
		html('Envio Exitoso');
		$Ahora=date('Y-m-d H:i:s');
		q("update sin_autor set fecha_envio='$Ahora',enviado_por='$NUSUARIO' where id=$id");
		echo "<body><script language='javascript'>centrar(10,10);alert('Email enviado a $G->email con el archivo: $G->devolucion_f desde el usuario $NUSUARIO ".PHP_VERSION."');parent.parent.recargar_tablero();</script></body>";
	}
	else
	{
		echo "<body><script language='javascript'>centrar(10,10);alert('$mail->ErrorInfo');parent.parent.recargar_tablero();</script></body>";
	}
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
	echo "<body><h3>Marcación de recepción de la garantía</h3>
				Por seguridad se solicita la contraseña del usuario actual para registrar esta recepción.<br /><br />
				<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
					Clave de confirmación: <input type='password' name='Clave' id='Clave'><br /><br />
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
		graba_bitacora('sin_autor','O',$id,'Solicitud de Recepcion de garantia fallida por error en contraseña.');
		echo "<script language='javascript'>
				function carga()
				{
					alert('Clave Incorrecta. Esta inconsistencia de clave erronea quedó grabada en la bitacora del registro de garantías.');
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
	graba_bitacora('sin_autor','M',$id,'Marca recepción de garantía en Efectivo / Tarjeta Débito');
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
				if(!alltrim(document.forma.numero_consignacion.value)) {alert('Debe digitar el número de consignación');document.forma.numero_consignacion.style.backgroundColor='ffffdd';document.forma.numero_consignacion.focus();return false;}
				if(document.forma.fecha_consignacion.value=='0000-00-00 00:00:00') {alert('Debe seleccionar la fehca de consignación');document.forma.fecha_consignacion.style.backgroundColor='ffffdd';return false;}
				document.forma.submit();
			}
		</script>
		<body onunload='opener.parent.recargar_tablero();'>
		<form action='zcontrol_custodia_garantia.php' method='post' target='Oculto_consignacion' name='forma' id='forma'>
			Número de Consignación: <input type='text' name='numero_consignacion'><br />
			Fecha de Consignación: ".pinta_FC('forma','fecha_consignacion',date('Y-m-d'))."<br />
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
		<br /><b>NOTA: La carga de la imágen tiene UNA sola oportunidad <br />y quedará guardada en el registro tan pronto sea <br />subida al sistema. <br /><br />
		<input type='button' value='Cerrar esta ventana' style='visibility:hidden' id='cerrar' onclick='window.close();void(null);'></center>
		</body>";
}

function registro_consignacion_datos()
{
	global $id,$numero_consignacion,$fecha_consignacion;
	q("update sin_autor set numero_consignacion='$numero_consignacion',fecha_consignacion='$fecha_consignacion' where id=$id");
	graba_bitacora('sin_autor','M',$id,'Numero Consignación, Fecha consignación');
	echo "<body><script language='javascript'>parent.document.getElementById('img_captura').style.visibility='visible';parent.document.forma.continuar.style.visibility='hidden';</script></body>";
}

function inscribir_cuenta()
{
	global $id;
	$D=qo("Select * from sin_autor where id=$id");
	if(!$D->devol_banco)
	{
		html();
		echo "<body><script language='javascript'>centrar(1,1);alert('No tiene datos para transferencia de garantía');window.close();void(null);</script></body>";
		die();
	}
	$Banco=qo("select * from codigo_ach where id='$D->devol_banco' ");
	html('INSCRIPCION DE CUENTA');
	echo "<script language='javascript'>
		function inscribir_cuenta()
		{window.open('zcontrol_custodia_garantia.php?Acc=inscribir_cuenta_ok&id=$id','Oculto_inscribir');}
		function cerrar()
		{window.close();void(null);opener.parent.recargar_tablero();}
	</script>
	<body><h3>Inscripción de cuenta</h3>
	<table>
	<tr ><td >Nombre:</td><td ><b>$D->devol_ncuenta</b></td></tr>
	<tr ><td >Identificación:</td><td ><b>$D->identificacion</b></td></tr>
	<tr ><td >Banco:</td><td ><b>$Banco->nombre</b></td></tr>
	<tr ><td >Número de cuenta:</td><td ><b>$D->devol_cuenta_banco</b></td></tr>
	<tr ><td >Tipo de cuenta</td><td ><b>".($D->devol_tipo_cuenta=='A'?"Ahorros":"Corriente")."</b></td></tr>
	</table><br />";
	if($D->inscripcion==1)
		echo "<h3>Esta cuenta aparece como inscrita</h3>";
	else
		echo "<input type='button' value='Registrar inscripción de cuenta' onclick='inscribir_cuenta()'>";

	echo "$G->inscripcion <iframe name='Oculto_inscribir' id='Oculto_inscribir' style='visibility:hidden' width=1 height=1></iframe></body>";
}

function inscribir_cuenta_ok()
{
	global $id;
	q("update sin_autor set inscripcion=1 where id=$id");
	echo "<body><script language='javascript'>alert('Inscripción satisfactoria');parent.cerrar();</script>";
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
				echo "<tr ><td width='100px'>NCR: $Nc->consecutivo <a class='info' style='cursor:pointer' onclick='ver_notacr($Nc->id);'><img src='gifs/standar/Preview.png' border='0'><span>Ver Nota Crédito</span></a> </td>
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
		<table border cellspacing='0'><tr ><th>Solicitado por</th><th>Fecha de solicitud</th><th>Concepto</th><th>Descripción</th><th>Valor</th></tr>";
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
	echo "<body><h3>Marcación de Verificación de Comparendos</h3>
				Por seguridad se solicita la contraseña del usuario actual para registrar esta marcación.<br /><br />
				<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
					Clave de confirmación: <input type='password' name='Clave' id='Clave'><br /><br />
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
		graba_bitacora('sin_autor','O',$id,'Marcación de verificación de comparendos de garantia fallida por error en contraseña.');
		echo "<script language='javascript'>
				function carga()
				{
					alert('Clave Incorrecta. Esta inconsistencia de clave erronea quedó grabada en la bitacora del registro de garantías.');
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
	graba_bitacora('sin_autor','M',$id,'Marca verificación de comparendos de garantía.');
	echo "<body><script language='javascript'>alert('Marcación  exitosa');window.close();void(null);opener.parent.recargar_tablero();</script></body>";
}

function lista_transferencia()
{
	global $d;
	$Hoy=date('Y-m-d');
	html();
	echo "<script language='javascript'>
					function genera_xls()
					{
						window.open('zcontrol_custodia_garantia.php?Acc=genera_xls&d=$d','Oculto_trans');
					}
					function genera_plano()
					{
						window.open('zcontrol_custodia_garantia.php?Acc=genera_plano&d=$d','Oculto_trans');
					}

				</script>";
	if($Registros=q("select a.*,t_siniestro(a.siniestro) as nsin,t_codigo_ach(a.devol_banco)  as nbanco,t_ubicacion(s.ubicacion) as nubica
								from sin_autor a, siniestro s
								where a.id in ($d 0) and a.siniestro=s.id"))
	{
		echo "<body>
			<h3>Reporte de Pago de Garantías - $Hoy</h3>
			<table border cellspacing='0'><tr>
					<th>Numero</th>
					<th>Siniestro</th>
					<th>Ubicación</th>
					<th>Tarjeta Habiente</th>
					<th>Identificación</th>
					<th>Banco</th>
					<th>Cuenta</th>
					<th>Valor</th>
					</tr>";
		$Contador=1;$Total=0;
		while($R=mysql_fetch_object($Registros))
		{
			echo "<tr ><td align='center'>$Contador</td><td >$R->nsin</td><td >$R->nubica</td><td >$R->devol_ncuenta</td><td align='right'>".coma_format($R->identificacion)."</td><td >$R->nbanco</td>
						<td >$R->devol_cuenta_banco</td><td align='right'>".coma_format($R->valor_devolucion)."</td></tr>";
			$Contador++;$Total+=$R->valor_devolucion;
		}
		echo "<tr ><td colspan='7' align='center' bgcolor='eeeeff'><b>TOTAL TRANSFERENCIA</b></td><td align='right' bgcolor='eeeeff'><b>".coma_format($Total)."</b></td></tr></table>";
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
			<h3>Reporte de Inscripción de Garantías - $Hoy</h3>
			<table border cellspacing='0'><tr>
					<th>Numero</th>
					<th>Siniestro</th>
					<th>Ubicación</th>
					<th>Tarjeta Habiente</th>
					<th>Identificación</th>
					<th>Banco</th>
					<th>Cuenta</th>
					<th>Tipo Cuenta</th>
					<th>Valor Garantía</th>
					</tr>";
		$Contador=1;$Total=0;
		while($R=mysql_fetch_object($Registros))
		{
			$TC=$R->devol_tipo_cuenta=='C'?'Corriente':'Ahoros';
			echo "<tr ><td align='center'>$Contador</td><td >$R->nsin</td><td >$R->nubica</td><td >$R->devol_ncuenta</td><td align='right'>".coma_format($R->identificacion)."</td><td >$R->nbanco</td>
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
			<h3>Reporte de Pago de Garantías - $Hoy</h3>
			<table border cellspacing='0'><tr>
					<th>Numero</th>
					<th>Siniestro</th>
					<th>Ubicación</th>
					<th>Tarjeta Habiente</th>
					<th>Identificación</th>
					<th>Banco</th>
					<th>Cuenta</th>
					<th>Valor</th>
					</tr>";
		$Contador=1;$Total=0;
		while($R=mysql_fetch_object($Registros))
		{
			echo "<tr ><td align='center'>$Contador</td><td >$R->nsin</td><td >$R->nubica</td><td >$R->devol_ncuenta</td><td align='right'>$R->identificacion</td><td >$R->nbanco</td>
						<td >$R->devol_cuenta_banco</td><td align='right'>$R->valor_devolucion</td></tr>";
			$Contador++;$Total+=$R->valor_devolucion;
		}
		echo "<tr ><td colspan='7' align='center' bgcolor='eeeeff'><b>TOTAL TRANSFERENCIA</b></td><td align='right' bgcolor='eeeeff'><b>$Total</b></td></tr></table>";
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
			<h3>Reporte de Inscripción de Garantías - $Hoy</h3>
			<table border cellspacing='0'><tr>
					<th>Numero</th>
					<th>Siniestro</th>
					<th>Ubicación</th>
					<th>Tarjeta Habiente</th>
					<th>Identificación</th>
					<th>Banco</th>
					<th>Cuenta</th>
					<th>Tipo Cuenta</th>
					<th>Valor Garantía</th>
					</tr>";
		$Contador=1;$Total=0;
		while($R=mysql_fetch_object($Registros))
		{
			$TC=$R->devol_tipo_cuenta=='C'?'Corriente':'Ahoros';
			echo "<tr ><td align='center'>$Contador</td><td >$R->nsin</td><td >$R->nubica</td><td >$R->devol_ncuenta</td><td align='right'>$R->identificacion</td><td >$R->nbanco</td>
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
								a.devol_ncuenta as nombre,b.codigo,a.identificacion
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
				if($R->identificacion)
				{
					$Cuenta=str_replace('-','' ,$R->cuenta);$Cuenta=str_replace('.','',$Cuenta);
					$TC=$R->tipo=='C'?'1':'7';  /// TIPO CUENTA 1 PARA CORRIENTE Y 7 PARA AHORROS
					$Nombre=l('GARANTIA '.$R->nombre,30);
					$Lineas.=$Cuenta.','.$TC.','.$Nombre.','.$R->codigo.','.$R->identificacion.$Final;
				}
				else
				{
					$Errores.="Identificación invalida. $R->cuenta, $R->tipo, $R->nombre, $R->codigo, $R->identificacion.".$Fin_de_linea;
				}
			}
			else
			{
				if($R->cuenta) $Errores.="Cuenta excede 17 caracteres. $R->cuenta, $R->tipo, $R->nombre, $R->codigo, $R->identificacion.".$Fin_de_linea;
				else $Errores.="Cuenta invalida. $R->cuenta, $R->tipo, $R->nombre, $R->codigo, $R->identificacion.".$Fin_de_linea;
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
		echo "No hay información coincidente. ";
	}
	echo "</body>";
}

function genera_plano()
{
	global $d;

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
			$Cuenta=str_replace('-','' ,$R->devol_cuenta_banco);$Cuenta=str_replace('.','',$Cuenta);
			$Nit=str_pad($R->identificacion,15,"0",STR_PAD_LEFT);
			$Nombre=str_pad(substr($R->devol_ncuenta,0,30),30," ",STR_PAD_RIGHT);
			$Banco=str_pad($R->nbanco,9,'0',STR_PAD_LEFT);
			$Cuenta=str_pad($Cuenta,17,' ',STR_PAD_RIGHT);
			$ILP=' '; // indicador de lugar de pago
			$Tipo_transaccion=($R->devol_tipo_cuenta=='C'?'27':'37');
			$Valor=prepara_valor($R->valor_devolucion,15,2);
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
			$Sumatoria+=$R->valor_devolucion;
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
	echo "<body><h3>Corrección de Información de Transferencia Electrónica</h3>
				Por seguridad se solicita la contraseña del usuario actual para registrar esta operación.<br /><br />
				<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
					Clave de confirmación: <input type='password' name='Clave' id='Clave'><br /><br />
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
		graba_bitacora('sin_autor','O',$id,'Modifiación de Transferencia Electrónica de garantía fallida por error en contraseña.');
		echo "<script language='javascript'>
				function carga()
				{
					alert('Clave Incorrecta. Esta inconsistencia de clave erronea quedó grabada en la bitacora del registro de garantías.');
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
	html("CORRECCION DE TANSFERENCIA GARANTIA");
	echo "<script language='javascript'>
			function valida()
			{
				with(document.forma)
				{
					if(!alltrim(obs_transferencia.value)) {alert('Debe escribir el motivo por el cual se hace corrección de la inscripción de la cuenta');obs_transferencia.style.backgroundColor='ffffdd';return false;}
				}
				document.forma.submit();
			}
		</script>
		<body >
		<h3>Corrección de Información de Transferencia Electrónica de Garantía.</h3>
		<form action='zcontrol_custodia_garantia.php' method='post' target='_self' name='forma' id='forma'>
			Número de cuenta: <input type='text' name='devol_cuenta_banco' value='$D->devol_cuenta_banco'><br />
			Nombre del cuentahabiente: <input type='text' name='devol_ncuenta' value='$D->devol_ncuenta' size='50' maxlength='50'><br />
			Identificación del cuentahabiente: <input type='text' name='identificacion' value='$D->identificacion' size=15 maxlength='15' class='numero'></br>
			Banco: ".menu1("devol_banco","select id,nombre from codigo_ach where codigo!='' order by nombre",$D->devol_banco)."<br />
			Tipo de cuenta: <select name='devol_tipo_cuenta' id='devol_tipo_cuenta'><option value=''></option><option value='A' ".($D->devol_tipo_cuenta=='A'?'selected':'').">Ahoros</option>
			<option value='C' ".($D->devol_tipo_cuenta=='C'?'selected':'').">Corriente</option></select><br />
			Motivo por el cual se hace corrección de la cuenta: <br />
			<textarea name='obs_transferencia' style='font-family:arial;font-size:11px;' cols=80 rows=4></textarea><br />
			<br /><input type='button' value='Continuar' onclick='valida();'>
			<input type='hidden' name='Acc' value='corregir_transferencia2_ok'>
			<input type='hidden' name='id' value='$id'>
		</form>
		</body>";
}

function corregir_transferencia2_ok()
{
	global $id,$devol_cuenta_banco,$devol_ncuenta,$devol_banco,$devol_tipo_cuenta,$obs_transferencia,$NUSUARIO,$identificacion;
	$Ahora=date('Y-m-d H:i:s');
	q("update sin_autor set devol_cuenta_banco='$devol_cuenta_banco',devol_ncuenta='$devol_ncuenta',devol_banco='$devol_banco',
			devol_tipo_cuenta='$devol_tipo_cuenta',identificacion='$identificacion',metodo_devol='',inscripcion=0,
			obs_transferencia=concat(obs_transferencia,\"\n$NUSUARIO [$Ahora]: $obs_transferencia\") where id=$id");
	graba_bitacora('sin_autor','M',$id,'devol_cuenta_banco, devol_ncuenta, devol_banco, devol_tipo_cuenta,metodo_devol. Corrige información de transferencia de garantía');
	echo "<body><script language='javascript'>alert('Correción registrada sastisfactoriamente');window.close();void(null);</script></body>";
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
				if(!alltrim(obs_transferencia.value)) {alert('Debe escribir el motivo por el cual se hace corrección de la inscripción de la cuenta');obs_transferencia.style.backgroundColor='ffffdd';return false;}
			}
			document.forma.submit();
		}
	</script>
	<body >
	<h3>Observaciones de Transferencia Electrónica de Garantía.</h3>
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
	q("update sin_autor set obs_transferencia=concat(obs_transferencia,\"\n$NUSUARIO [$Ahora]: $obs_transferencia\") where id=$id");
	graba_bitacora('sin_autor','M',$id,'obs_transferencia');
	echo "<body><script language='javascript'>alert('Correción registrada sastisfactoriamente');window.close();void(null);</script></body>";
}



















?>