<?php

/**
 *  PROGRAMA PARA DUPLICAR FACTURAS POR FRECUENCIA DE PAGOS
 *
 * @version $Id$
 * @copyright 2010
 */
include('inc/funciones_.php');
sesion();

if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}

pinta_facturas_inicial();

function pinta_facturas_inicial()
{
	global $FI,$FF;
	html('CONTROL DE FACTURAS POR FRECUENCIA DE GASTO');
	echo "<script language='javascript'>
		function carga()
		{
			centrar();
		}
		function recargar()
		{
			var Fi=document.forma.FI.value;
			var Ff=document.forma.FF.value;
			window.open('zcontrol_frecuencia.php?Acc=pinta_facturas_inicial&FI='+Fi+'&FF='+Ff,'_self');
		}
		function cambia_frecuencia(dato)
		{
			var nombre=dato.name;
			var fac=nombre.substr(3,10);
			var valor=dato.value;
			if(confirm('Desea ajustar la frecuencia de esta factura a '+valor+' dias?'))
			{
				window.open('zcontrol_frecuencia.php?Acc=ajusta_frecuencia&Fac='+fac+'&Valor='+valor,'Oculto_f');
			}
		}
		function chequeo(id,valor)
		{
			if(valor)
				document.getElementById('tr_'+id).style.backgroundColor='dddddd';
			else
				document.getElementById('tr_'+id).style.backgroundColor='ffffff';
		}
		function generar_facturas()
		{
			if(confirm('Desea duplicar las facturas seleccionadas?'))
			{
				document.forma1.Acc.value='duplicar_facturas';
				document.forma1.submit();
			}
		}
	</script>
	<body onload='carga()'>
	<h3>Control de Cuentas por Pagar por Frecuencias.</h3>
	<form action='zcotrol_frecuencia.php' method='post' target='_self' name='forma' id='forma'>
		Rango de fechas: ".pinta_FC('forma','FI',$FI)." - ".pinta_FC('forma','FF',$FF)." <input type='button' id='recarga' name='recarga' value='Aplicar' onclick='recargar()' ><br>
		<b>INSTRUCCIONES: Primero seleccione un rango de fechas para buscar las facturas que desea duplicar.
		El sistema toma como referencia la <u>fecha de vencimiento</u> de la factura para mostrarla dentro del rango que seleccione.
		 Luego en la columna <i>GENERAR</i>, seleccione las facturas que desea generar de forma automática.
		Verifique que la <u>fecha de emisión</u> y de <u>vencimiento</u> estén aproximadas a la realidad antes de duplicar las facturas.</b>
	</form>";
	if($FI && $FF)
	{
		if($Fac_base=q("select f.*,t_proveedor(f.proveedor) as nproveedor,t_tipo_documento(f.tipo_doc) as ntipo_doc,
					t_oficina(f.oficina) as noficina,t_concepto_factura(f.concepto) as nconcepto, case tipo_gasto when 'F' then 'FIJO' when 'V' then 'VARIABLE' end as ntipo_gasto
					from factura f
					where fecha_vence between '$FI' and '$FF' and aprob_solicitada=1 and con_aprobacion=1 and anulada=0
					order by ntipo_gasto,nproveedor "))
		{
			echo "<form action='zcontrol_frecuencia.php' method='post' target='_self' name='forma1' id='forma1'>
			<input type='hidden' name='Acc' id='Acc' value=''>
			<b>Cuando haya marcado las facturas que desea generar automáticamente haga click aquí:</b>
			<input type='button' value='GENERAR NUEVAS FACTURAS' onclick='generar_facturas()' style='width:200;height:20;font-weight:bold;'><br><br>
			Las facturas se duplicarán sin imágenes ni estados y el consecutivo aparecerá con el prefijo DUPL: para poder identificarlas y corregirlas tan pronto se tenga la información definitiva.<br><br>
			<table border cellspacing=0 style='empty-cells:show'><tr>
						<th>Tipo Gasto</th>
						<th>Proveedor</th>
						<th>Documento</th>
						<th>Oficina</th>
						<th>Fec. Emision</th>
						<th>Fec. Vencimiento</th>
						<th>Concepto - Descripción</th>
						<th>Valor</th>
						<th>Impuestos</th>
						<th>Total</th>
						<th>Frecuencia</th>
						<th>Generar</th>
						</tr>";
			while($F=mysql_fetch_object($Fac_base))
			{
				echo "<tr id='tr_$F->id'>
							<td align='center' ".($F->ntipo_gasto=='FIJO'?" BGCOLOR='ddffdd' ":" BGCOLOR='ffffdd' ").">$F->ntipo_gasto</td>
							<td>$F->nproveedor</td>
							<td>$F->ntipo_doc<br><b>$F->numero</b></td>
							<td>$F->noficina</td>
							<td align='center'>$F->fecha_emision</td>
							<td align='center'>$F->fecha_vence</td>
							<td>$F->nconcepto <br>$F->descripcion</td>
							<td align='right'>".coma_format($F->valor_factura)."</td>
							<td nowrap='yes'>".($F->iva?"Iva: <font color='green'>".coma_format($F->iva)."</font><br>":"").
							($F->rete_fuente?"Rt.Fuente:  $F->porc_retefuente% <font color='red'>".coma_format($F->rete_fuente)."</font><br>":"").
							($F->rete_ica?"Rt.Ica: $F->porc_reteica% <font color='red'>".coma_format($F->rete_ica)."</font><br>":"").
							($F->rete_iva?"Rt.Iva: <font color='red'>".coma_format($F->rete_iva)."</font>":"")."</td>
							<td align='right'><b>".coma_format($F->valor_a_pagar)."</b></td>
							<td align='center'>";
					if($F->frecuencia_pago==0)
					{
						echo "<select name='fp_$F->id' onchange='cambia_frecuencia(this)'><option value='0'></option>
										<option value='15'>Quincenal</option>
										<option value='30'>Mensual</option>
										<option value='60'>Bi-Mensual</option>
										<option value='180'>Semestral</option>
										<option value='360'>Anual</option></select></td><td>Debe seleccionar una frecuencia</td>
										";
					}
					else
					{
						echo "$F->frecuencia_pago</td><td nowrap='yes'><center><input type='checkbox' id='d_$F->id' name='d_$F->id' onchange='chequeo($F->id,this.checked)'></center>";
						switch($F->frecuencia_pago)
						{
							case 15: $N_fec_emision=aumentadias($F->fecha_emision,15);
												$N_fec_vence=aumentadias($F->fecha_vence,15);
												break;
							case 30: $N_fec_emision=aumentameses($F->fecha_emision,1);
												$N_fec_vence=aumentameses($F->fecha_vence,1);
												break;
							case 60: $N_fec_emision=aumentameses($F->fecha_emision,2);
												$N_fec_vence=aumentameses($F->fecha_vence,2);
												break;
							case 180: $N_fec_emision=aumentameses($F->fecha_emision,6);
												$N_fec_vence=aumentameses($F->fecha_vence,6);
												break;
							case 360: $N_fec_emision=aumentameses($F->fecha_emision,12);
												$N_fec_vence=aumentameses($F->fecha_vence,12);
												break;
						}
						$N_fec_emision=date('Y-m-d',strtotime($N_fec_emision));
						$N_fec_vence=date('Y-m-d',strtotime($N_fec_vence));
						echo "F.emision : ".pinta_FC('forma1',"fe_$F->id",$N_fec_emision)."<br>F.vencimiento:".pinta_FC('forma1',"fv_$F->id",$N_fec_vence)."</td>";
					}
					echo "</tr>";
			}
			echo "</table>
			</form>";
		}
	}
	echo "<iframe name='Oculto_f' id='Oculto_f' height=10 width=10 style='visibility:hidden'></iframe></body>";
}

function ajusta_frecuencia()
{
	global $Fac,$Valor;
	if($Fac && $Valor)
	{
		q("update factura set frecuencia_pago=$Valor where id=$Fac");
		echo "<script language='javascript'>
			function carga()
			{
				opener.recargar();
			}
		</script>
		<body onload='carga()'></body>";
	}
}


function duplicar_facturas()
{
	html("GENERACION AUTOMATICA DE FACTURAS");
	echo "<H3>GENERANDO AUTOMATICAMENTE LAS FACTURAS</H3>";
	require('inc/link.php');
	foreach($_POST as $campo => $valor)
	{
		if(substr($campo,0,2)=='d_')
		{
			$idFac=substr($campo,2);
			eval("\$Femision=\$_POST['fe_".$idFac."'];");
			eval("\$Fvencimiento=\$_POST['fv_".$idFac."'];");
			$O=qom("select * from factura where id=$idFac",$LINK);
			echo "<br><br><h3>Factura $idFac $Femision $Fvencimiento</h3>";
			if(!mysql_query("insert into factura (proveedor,tipo_doc,numero,oficina,fecha_emision,fecha_vence,concepto,placa,descripcion,
				concepto_trib,valor_factura,iva,valor_tot_fac,porc_retefuente,rete_fuente,porc_reteica,rete_ica,rete_iva,valor_a_pagar,tipo_gasto,frecuencia_pago) values
				('$O->proveedor','$O->tipo_doc','DUPL:$O->numero','$O->oficina','$Femision','$Fvencimiento','$O->concepto','$O->placa','$O->descripcion',
				'$O->concepto_trib','$O->valor_factura','$O->iva','$O->valor_tot_fac','$O->porc_retefuente','$O->rete_fuente','$O->porc_reteica','$O->rete_ica',
				'$O->rete_iva','$O->valor_a_pagar','$O->tipo_gasto','$O->frecuencia_pago') ",$LINK))
				echo "<li> ERROR ".mysql_error($LINK);
		}
	}
	mysql_close($LINK);
}
?>