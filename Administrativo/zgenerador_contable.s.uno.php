<?php

/**
 *   EXPORTACIONES CONTABLES DE DOCUMENTOS HACIA SISTEMA UNO
 */
 
include('inc/funciones_.php');
sesion();
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');die();}

function exporta_caja_menor_uno()
{
	global $id;
	error_reporting(E_ALL);
	$BDA='aoacol_aoacars';
	$Centros_costos_placas=q("select v.placa,a.ccostos_uno from $BDA.vehiculo v,$BDA.aseguradora a where v.flota=a.id ");
	$ACCostos=array();
	while($V=mysql_fetch_object($Centros_costos_placas)) { $ACCostos["$V->placa"]="$V->ccostos_uno"; }
	include('Interfase.Sistemauno.class.php');
	$C=qo("select * from caja_menor where id=$id");
	if(!$C->td_contable_uno) { 
		q("update caja_menor set td_contable_uno='CM' where id='$id' ");
		$C=qo("select * from caja_menor where id=$id");
	}
	$Ofi=qo("select * from oficina where id=$C->oficina");
	$Pasivo=$C->flotas?$Ofi->ccombustible_uno:$Ofi->ccaja_menor_uno;
	html("EXPORTACION CONTABLE A SISTEMA UNO - CAJA MENOR - $Ofi->nombre");
	$tdc=$C->td_contable_uno;
	if($Detalle=q("select d.*,p.cuenta from caja_menord d,tipo_caja_menor t,puc p where d.caja=$id and d.tipo=t.id and t.puc_operativa=p.id "))
	{
		$idts='0';
		$ConsecutivoG=0;
		$Terceros=q("select distinct tercero from caja_menord where caja=$id ");
		$Fecha_doc=date('Ymd',strtotime($C->fecha));
		while($T=mysql_fetch_object($Terceros))
		{
			$idts.=','.$T->tercero;
		}

		$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
		if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
		$DD1=fopen($DESTINO_PLANO1,'w+');
		include('inc/link.php');
		while($D=mysql_fetch_object($Detalle))
		{
			$ConsecutivoG++;
			$Proyecto='          ';
			$Descripcion=$D->concepto;
			$Centro_de_Costos=0;
			$Cuenta_contable=$D->cuenta;
			if($D->ccostos_uno and !$D->placa)
			{
				$Centro_de_Costos=qo1m("select codigo from ccostos_uno where id=$D->ccostos_uno",$LINK);
				$Cuenta_contable=qo1m("select p.cuenta from puc p,tipo_caja_menor t where t.id=$D->tipo and t.cuenta=p.id",$LINK);
			}
			elseif($D->placa and !$D->ccostos_uno) { 
				$Proyecto='VO'.$D->placa;
				$Centro_de_Costos=$ACCostos["$D->placa"];
			}
			elseif($D->placa and $D->ccostos_uno) { 
				$Proyecto='VO'.$D->placa;
				$Centro_de_Costos=qo1m("select codigo from ccostos_uno where id=$D->ccostos_uno",$LINK);
			}
			
			//$Centro_de_Costos=($D->placa?$ACCostos["$D->placa"]:$Ofi->ccostos_uno);
			echo "<br>$D->id Centro de Costos: $Centro_de_Costos Proyecto: $Proyecto ";
				///
			$I = new Interfase_documento();
			$I->Consecutivo_grabacion=$ConsecutivoG;
			$I->Centro_operacion=$Ofi->centro_operacion;
			$I->Tipo_documento=$tdc;
			$I->Numero_documento=$C->consecutivo_contable;
			$I->Fecha_documento=$Fecha_doc;
			$I->Tercero=qo1m("select identificacion from proveedor where id=$D->tercero",$LINK);
			$I->Valor_documento=$C->reembolso;
			$I->Cuenta_contable=$Cuenta_contable;
			$I->Centro_operacion_mov=$I->Centro_operacion;
			$I->Tercero_mov=$I->Tercero;
			$I->Detalle1= l($Descripcion,40);
			$I->Detalle2= substr($Descripcion,40);
			$I->DC='D';
			$I->Valor=$D->subtotal;
			if($D->iva)
			{
				$I->Tasa_base_iva=($D->iva?16:0);
				$I->Base_iva_retencion=$D->subtotal;
				$I->Base_iva_ret_libro2=$D->subtotal;
				$I->Base_iva_ret_libro3=$D->subtotal;
			}	
			$I->Centro_costos=$Centro_de_Costos;
			$I->Detalle1_doc=l($Descripcion,60);
			$I->Indicador_contab_libro2='2';
			$I->Valor_transaccion_libro2=$D->subtotal;
			$I->Valor_transaccion_libro3=$D->subtotal;
			$I->Proyecto=$Proyecto;
			fwrite( $DD1,$I->genera());
				
			if($D->iva)
			{
				$ConsecutivoG++;
					///
				$I = new Interfase_documento();
				$I->Consecutivo_grabacion=$ConsecutivoG;
				$I->Centro_operacion=$Ofi->centro_operacion;
				$I->Tipo_documento=$tdc;
				$I->Numero_documento=$C->consecutivo_contable;
				$I->Fecha_documento=$Fecha_doc;
				$I->Tercero=qo1m("select identificacion from proveedor where id=$D->tercero",$LINK);
				$I->Valor_documento=$C->reembolso;
				$I->Cuenta_contable=$Ofi->civa_uno;
				$I->Centro_operacion_mov=$I->Centro_operacion;
				$I->Tercero_mov=$I->Tercero;
				$I->Detalle1= l($Descripcion,40);
				$I->Detalle2= substr($Descripcion,40);
				$I->DC='D';
				$I->Valor=$D->iva;
				if($D->iva)
				{
					$I->Tasa_base_iva=($D->iva?16:0);
					$I->Base_iva_retencion=$D->subtotal;
					$I->Base_iva_ret_libro2=$D->subtotal;
					$I->Base_iva_ret_libro3=$D->subtotal;
				}	
				$I->Centro_costos=$Centro_de_Costos;
				$I->Detalle1_doc=l($Descripcion,60);
				$I->Indicador_contab_libro2='2';
				$I->Valor_transaccion_libro2=$D->iva;
				$I->Valor_transaccion_libro3=$D->iva;
				$I->Proyecto=$Proyecto;
				fwrite( $DD1,$I->genera());
			}
		}
		mysql_close($LINK);
		
		$ConsecutivoG++;
		$Descripcion ='REEMBOLSO DE CAJA MENOR '.$C->elaborado_por ;
		$I = new Interfase_documento();
		$I->Consecutivo_grabacion=$ConsecutivoG;
		$I->Centro_operacion=$Ofi->centro_operacion;
		$I->Tipo_documento=$tdc;
		$I->Numero_documento=$C->consecutivo_contable;
		$I->Fecha_documento=$Fecha_doc;
		$I->Tercero=$C->cedula;
		$I->Valor_documento=$C->reembolso;
		$I->Cuenta_contable=$Pasivo;
		// $I->Centro_operacion_mov=$I->Centro_operacion; // Noviembre 24. se requiere que la cuenta del pasivo vaya al centro de operaciones 001
		$I->Centro_operacion_mov='001';
		$I->Tercero_mov=$I->Tercero;
		$I->Detalle1= l($Descripcion,40);
		$I->Detalle2= substr($Descripcion,40);
		$I->DC='C';
		$I->Valor=$C->reembolso;
		//$I->Centro_costos=$Centro_de_Costos;
		$I->Detalle1_doc=l($Descripcion,60);
		$I->Indicador_contab_libro2='2';
		$I->Valor_transaccion_libro2=$C->reembolso;
		$I->Valor_transaccion_libro3=$C->reembolso;
		//$I->Proyecto=$Proyecto;
		fwrite( $DD1,$I->genera());
		
		fclose( $DD1 );
		echo "<script language='javascript'>
				function pasar()
				{
					window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=FIBATCH.TXT','Oculto1');
					window.open('zgenerador_contable.s.uno.php?Acc=exporta_terceros_uno&id=$idts&Centro_de_operacion=$Ofi->centro_operacion','Int_ter');
				}
			</script>
			<body onload='pasar();' onclick='window.close();void(null);'>
			<iframe name='Int_ter' style='visibility:hidden' height='1' width='1'></iframe>
			<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
			</body>";
	}
}

function exporta_terceros_uno()
{
	global $id,$Centro_de_operacion;
	if($Clientes=q("select p.*,t.codigo_siigo as cti,rt.codigo_siigo as crt  from proveedor p, tipo_identificacion t, regimen_tributario rt where p.id  in ($id) and p.td=t.codigo and p.regimen=rt.id"))
	{
		include('Interfase.Sistemauno.class.php');
		$DESTINO_PLANO2 = 'planos/ter1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el tercero
		if(@is_file($DESTINO_PLANO2)) @unlink($DESTINO_PLANO2);
		$DD2=fopen($DESTINO_PLANO2,'w+');
		while($Cliente=mysql_fetch_object($Clientes))
		{
			$T=new interfase_tercero();
			$T->Codigo	=$Cliente->identificacion;
			$T->Nit	=$Cliente->identificacion;
			$T->Dv	=($Cliente->dv?$Cliente->dv:' '); 
			$T->Tipo_identificacion='1';  
			$T->Nombre=$T->prepara_apellido($Cliente->apellido).$T->prepara_cadena($Cliente->nombre,20);
			$T->Nombre_establecimiento=trim($Cliente->apellido.' '.$Cliente->nombre);
			$T->Indicador_cliente='1';
			$T->Pais='169';
			$T->Departamento=substr($Cliente->ciudad,0,2);
			$T->Ciudad=substr($Cliente->ciudad,2,3);
			$T->Direccion1=substr(trim($Cliente->direccion),0,40);
			$T->Direccion2=substr(trim($Cliente->direccion),40,40);
			$T->Direccion3=substr(trim($Cliente->direccion),80);
			$T->Telefono=trim($Cliente->celular);
			$T->Telefono2=trim($Cliente->telefono_casa);
			$T->Fax=trim($Cliente->telefono_oficina);
			$T->Email=trim($Cliente->email_e);
			$T->Barrio=trim($Cliente->barrio);
			$T->Codigo_clase_cliente='0201  ';
			$T->Centro_operacion=$Centro_de_operacion;
			$T->Zona='01    ';
			$T->Indicador_liquidacion_impuesto='1';
			$T->Codigo_rete_otro='01';
			$T->Codigo_condicion_pago='0 ';
			$T->Fecha_creacion=date('Ymd');
			fwrite($DD2,$T->genera());
		}
		fclose($DD2);
		html();
		echo "<body><script language='javascript'>	window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO2&Salida=TERCEROS.TXT','_self');</script>";
	}
	else
		echo "<body><script language='javascript'>alert('No se pudo obtener información: $id');window.close();void(null);</script>";
}
?>