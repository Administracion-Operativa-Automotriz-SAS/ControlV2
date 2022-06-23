<?php

/**
 *   EXPORTACIONES CONTABLES DE DOCUMENTOS HACIA SIIGO
 *
 * Para las facturas que se anulan de un mes para otro, debe desarrollarse una función nueva que exporte los asientos con sus valores pero en afectacion inversa
 *
 *
 *LO QUE SIGUE:  de acuerdo a la variable AUTO_RETENCION en clientes, generar los asientos en la exportacion.
 */
include('inc/funciones_.php');
sesion();

if (!empty($Acc) && function_exists($Acc)){	eval($Acc . '();');	die();}

function exportar_factura()
{
	global $id;

	$TDC=8;  // id del tipo de documento contable F006 FACTURA DE VENTA
	$GRUPO_CONTABLE=1; // UNICO GRUPO CONTABLE
	$F = qo( "select * from factura where id=$id" );
	$Aseguradora=qo("select * from aseguradora where id=$F->aseguradora");
	$TDC = qo( "select * from tipo_doc_cont where id=$TDC" );
	$Cliente = qo( "select * from cliente where id=$F->cliente" );
	$Centro_costos = '0001';
	$Subcentro_costos = '000';

	// ###################### INICIO DE LA GENERACION DE LA FACTURA ########################
	$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
	if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
	$DD1=fopen($DESTINO_PLANO1,'w+');

	$DESTINO_PLANO2 = 'planos/ter1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el tercero
	if(@is_file($DESTINO_PLANO2)) @unlink($DESTINO_PLANO2);
	$DD2=fopen($DESTINO_PLANO2,'w+');

	$C=new interfase_tercero();
	$C->Nit=$Cliente->identificacion;
	$C->Nombre=($Cliente->apellido?$Cliente->apellido.' ':'').$Cliente->nombre;
	$C->Contacto=$C->Nombre;
	$C->Direccion=$Cliente->direccion;
	$C->Telefono1=$Cliente->telefono_casa;
	$C->Telefono2=$Cliente->celular;
	$C->Telefono3=$Cliente->telefono_oficina;
	$C->Email=$Cliente->email_e;
	$C->Sexo=$Cliente->sexo;
	$C->Tipo_identificacion=qo1("select codigo_siigo from tipo_identificacion where codigo='$Cliente->tipo_id' ");
	$C->Digito_verificacion=$Cliente->dv;
	$C->Tipo_persona=$Cliente->tipo_persona;
	fwrite( $DD2,$C->genera());



	$Fecha_doc=date('Ymd',strtotime($F->fecha_emision));
	$Secuencia = 1;
	$Sumatoria = 0;
	$Iva = 0;

	if($F->anulada==1) $Descripcion="FACTURA $F->consecutivo ANULADA";
	else $Descripcion=l("FAC $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);

	$Total_retenciones=0;
	if($Cliente->auto_retencion==1)
	{
		///****   AUTO RETENCION DE IVA
		$Valor_retencion1=round($F->subtotal*$Aseguradora->pvrete_iva/100,0);
		$I = new Interfase_documento();
		$I->Tipo_comprobante = l( $TDC->codigo, 1 );
		$I->Codigo_comprobante = substr( $TDC->codigo, 1, 3 );
		$I->Numero_documento = $F->consecutivo;
		$I->Secuencia = $Secuencia;
		$I->Nit = $Cliente->identificacion;
		$I->Cuenta_contable = $Aseguradora->vrete_iva;
		$I->Fecha_documento = $Fecha_doc;
		$I->Centro_costos = $Centro_costos;
		$I->Subcentro_costos = $Subcentro_costos;
		$I->Descripcion = l("Rete Iva $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
		$I->Afectacion = 'D';
		$I->Valor=($F->anulada?0:$Valor_retencion1);
		fwrite( $DD1,$I->genera());
		$Secuencia++;
		///****   AUTO RETENCION DE LA FUENTE
		$Valor_retencion2=round($F->subtotal*$Aseguradora->pvrete_fuente/100,0);
		$I = new Interfase_documento();
		$I->Tipo_comprobante = l( $TDC->codigo, 1 );
		$I->Codigo_comprobante = substr( $TDC->codigo, 1, 3 );
		$I->Numero_documento = $F->consecutivo;
		$I->Secuencia = $Secuencia;
		$I->Nit = $Cliente->identificacion;
		$I->Cuenta_contable = $Aseguradora->vrete_fuente;
		$I->Fecha_documento = $Fecha_doc;
		$I->Centro_costos = $Centro_costos;
		$I->Subcentro_costos = $Subcentro_costos;
		$I->Descripcion = l("Rete Fte $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
		$I->Afectacion = 'D';
		$I->Valor=($F->anulada?0:$Valor_retencion2);
		fwrite( $DD1,$I->genera());
		$Secuencia++;
		///****   AUTO RETENCION DE ICA
		$Valor_retencion3=round($F->subtotal*$Aseguradora->pvrete_ica/1000,0);
		$I = new Interfase_documento();
		$I->Tipo_comprobante = l( $TDC->codigo, 1 );
		$I->Codigo_comprobante = substr( $TDC->codigo, 1, 3 );
		$I->Numero_documento = $F->consecutivo;
		$I->Secuencia = $Secuencia;
		$I->Nit = $Cliente->identificacion;
		$I->Cuenta_contable = $Aseguradora->vrete_ica;
		$I->Fecha_documento = $Fecha_doc;
		$I->Centro_costos = $Centro_costos;
		$I->Subcentro_costos = $Subcentro_costos;
		$I->Descripcion = l("Rete Ica $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
		$I->Afectacion = 'D';
		$I->Valor=($F->anulada?0:$Valor_retencion3);
		fwrite( $DD1,$I->genera());
		$Secuencia++;
		/// TOTALIZO LAS RETENCIONES PARA CALCULAR EL VALOR NETO DEL INGRESO
		$Total_retenciones=$Valor_retencion1+$Valor_retencion2+$Valor_retencion3;
	}

	$I = new Interfase_documento();
	$I->Tipo_comprobante = l( $TDC->codigo, 1 );
	$I->Codigo_comprobante = substr( $TDC->codigo, 1, 3 );
	$I->Numero_documento = $F->consecutivo;
	$I->Secuencia = $Secuencia;
	$I->Nit = $Cliente->identificacion;
	$I->Cuenta_contable = '1305050100';
	$I->Fecha_documento = $Fecha_doc;
	$I->Centro_costos = $Centro_costos;
	$I->Subcentro_costos = $Subcentro_costos;
	$I->Descripcion =$Descripcion;
	$I->Afectacion = 'D';
	$I->Valor=($F->anulada?0:$F->total-$Total_retenciones);
	$I->Tipo_documento_cruce=l($TDC->codigo,1);
	$I->Codigo_comprobante_cruce=substr($TDC->codigo,1,3);
	$I->Numero_documento_cruce=$F->consecutivo;
	$I->Secuencia_documento_cruce=$Secuencia;
	$I->Fecha_vencimiento_documento_cruce=date( 'Ymd', strtotime($F->fecha_vencimiento));
	fwrite( $DD1,$I->genera());
	$Secuencia++;

	if($F->iva)
	{
		$I = new Interfase_documento();
		$I->Tipo_comprobante = l( $TDC->codigo, 1 );
		$I->Codigo_comprobante = substr( $TDC->codigo, 1, 3 );
		$I->Numero_documento = $F->consecutivo;
		$I->Secuencia = $Secuencia;
		$I->Nit = $Cliente->identificacion;
		$I->Cuenta_contable = '2408050100';
		$I->Fecha_documento = $Fecha_doc;
		$I->Centro_costos = $Centro_costos;
		$I->Subcentro_costos = $Subcentro_costos;
		$I->Descripcion = l("FAC $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
		$I->Afectacion = 'C';
		$I->Valor=($F->anulada?0:$F->iva);
		$I->Tipo_documento_cruce=l($TDC->codigo,1);
		$I->Codigo_comprobante_cruce=substr($TDC->codigo,1,3);
		fwrite( $DD1,$I->genera());
		$Secuencia++;
	}

	while ( $D = mysql_fetch_object( $Detalle ) )
	{

		$Valor_item=$D->cantidad*$D->unitario;


		if ( $Causacion = q( "select * from inv_grupocont_cau where grupo_contable=$GRUPO_CONTABLE and td_contable=$TDC->id and tipo_afectacion='C' " ) )
		{
			while ( $Cau = mysql_fetch_object( $Causacion ) )
			{
				if($Cau->cuenta)
				{
					$Cuenta_contable=$Cau->cuenta;
					$Descripcion='VENTA';
				}
				else
				{
					if($Cuenta_contable=qo1("select cuenta_ingreso from concepto_fac where id=$D->concepto "))
					{
						$Descripcion=qo1("select nombre from concepto_fac where id=$D->concepto");
					}
					else
					{
						$Cuenta_contable=qo1("select cuenta_ingreso from aseguradora where id=$F->aseguradora");
						$Descripcion=qo1("select nombre from concepto_fac where id=$D->concepto");
					}
				}
				$I = new Interfase_documento();
				$I->Tipo_comprobante = l( $TDC->codigo, 1 );
				$I->Codigo_comprobante = substr( $TDC->codigo, 1, 3 );
				$I->Numero_documento = $F->consecutivo;
				$I->Secuencia = $Secuencia;
				$I->Nit = $Cliente->identificacion;
				$I->Cuenta_contable = $Cuenta_contable;
				$I->Fecha_documento = $Fecha_doc;
				$I->Centro_costos = $Centro_costos;
				$I->Subcentro_costos = $Subcentro_costos;
				$I->Descripcion = l(($F->anulada?'ANULADA: ':'').$Descripcion,50);
				$I->Afectacion = $Cau->tipo_afectacion;
				$I->Valor=($F->anulada?0:$D->cantidad*$D->unitario);
				fwrite( $DD1,$I->genera());
				$Secuencia++;
			}
		}
	}

	fclose( $DD1 );
	fclose( $DD2 );

	echo "<script language='javascript'>
		function pasar()
		{

		window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=f.txt','Oculto1');
		window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO2&Salida=t.txt','Oculto2');
	// window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO3&Salida=b.txt','Oculto3');

		}

		</script>

		<body onload='pasar();'>
		<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		<iframe name='Oculto2' style='visibility:hidden' height='1' width='1'></iframe>
		<iframe name='Oculto3' style='visibility:hidden' height='1' width='1'></iframe>
		</body>";
}

function genera_causacion_cxp()
{
	global $id,$Reporte;  // id es el id de la factura por pagar
	$Fac=qo("select * from factura where id=$id");
	$Proveedor=qo("select * from proveedor where id=$Fac->proveedor");
	$NT=tu('comprobante','id');
	html('CAUSACION CONTABLE DE LA FACTURA POR PAGAR');
	echo "<script language='javascript'>
			function refrescar()
			{
				modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$NT&VINCULOT=$id&VINCULOC=factura',0,0,500,1000,'cb');
				opener.parent.location.reload();
				window.close();
				void(null);
			}
			function refrescar_reporte()
			{
				modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$NT&VINCULOT=$id&VINCULOC=factura',0,0,500,1000,'cb');
				opener.location.reload();
				window.close();
				void(null);
			}
		</script>
		<body><script language='javascript'>centrar(600,600);</script>
		<form action='zgenerador_contable.php' method='post' target='Oculto_gc' name='forma' id='forma'>
			Consecutivo contable: <input type='text' name='consecutivo_contable' size=13 maxlength=11 value='".($Fac->consecutivo_contable?$Fac->consecutivo_contable:$Fac->numero)."'><br />
			Detalle: <input type='text' name='detalle' size=70 maxlength=50 value='FACTURA $Fac->numero $Proveedor->nombre'><br /><br />
			<input type='checkbox' checked name='limpiar'> Limpiar la contabilizacion previa.<br /><br />
			<input type='submit' value='CONTINUAR'>
			<input type='hidden' name='Acc' value='genera_causacion_cxp_detalle'>
			<input type='hidden' name='id' value='$id'>
			<input type='hidden' name='Reporte' value='$Reporte'>

		</form>
		<iframe name='Oculto_gc' id='Oculto_gc' style='visibility:hidden' width=1 height=1></iframe>
		</body>";
}

function genera_causacion_cxp_detalle()
{
	global $id,$detalle,$consecutivo_contable,$limpiar,$Reporte;
	$limpiar=sino($limpiar);
	$Fac=qo("select * from factura where id=$id");
	if($limpiar)
	{
		q("delete from comprobante where factura=$id");
	}
	$consecutivo_contable=str_pad($consecutivo_contable,11,'0',STR_PAD_LEFT);
	q("update factura set consecutivo_contable='$consecutivo_contable' where id=$id");



	// primer asiento: el gasto
	q("insert into comprobante (factura,tercero,ccosto,detalle,ta,valor)
		values($id,$Fac->proveedor,1,'$detalle','D',$Fac->valor_factura)");
	// segundo asiento: el iva
	if($Fac->iva)
	{
		q("insert into comprobante (factura,tercero,ccosto,detalle,ta,valor)
			values($id,$Fac->proveedor,1,'$detalle','D',$Fac->iva)");
	}
	// tercer asiento: rete fuente 1
	if($Fac->rete_fuente)
	{
		q("insert into comprobante (factura,tercero,ccosto,detalle,ta,valor,base)
			values($id,$Fac->proveedor,1,'$detalle','C',$Fac->rete_fuente,$Fac->base1_ret)");
	}
	// cuarto asiento: rete fuente 2
	if($Fac->rete_fuente2)
	{
		q("insert into comprobante (factura,tercero,ccosto,detalle,ta,valor,base)
			values($id,$Fac->proveedor,1,'$detalle','C',$Fac->rete_fuente2,$Fac->base2_ret)");
	}
	// quinto asiento: rete ica
	if($Fac->rete_ica)
	{
		q("insert into comprobante (factura,tercero,ccosto,detalle,ta,valor,base)
			values($id,$Fac->proveedor,1,'$detalle','C',$Fac->rete_ica,$Fac->base1_reteica)");
	}
	// sexto y septimo asiento:  rete iva
	if($Fac->rete_iva)
	{
		q("insert into comprobante (factura,tercero,ccosto,detalle,ta,base,valor)
			values($id,$Fac->proveedor,1,'$detalle','C',$Fac->valor_factura,$Fac->rete_iva)");
		q("insert into comprobante (factura,tercero,ccosto,detalle,ta,base,valor)
			values($id,$Fac->proveedor,1,'$detalle','D',$Fac->valor_factura,$Fac->rete_iva)");
	}
	// octavo asiento: cuenta por pagar
	q("insert into comprobante (factura,tercero,cuenta,ccosto,detalle,ta,valor)
			values($id,$Fac->proveedor,657,1,'$detalle','C',$Fac->valor_a_pagar)");
	if($Reporte==1)
	{
		q("update tmpi_".$_SESSION['Id_alterno']."_21 set fac_consecutivo_contable='$consecutivo_contable' where fac_id='$id' ");
		echo "<body><script language='javascript'>parent.refrescar_reporte();</script></body>";
	}
	else
		echo "<body><script language='javascript'>parent.refrescar();</script></body>";
}

function exporta_cxp()
{
	global $id;  // id es el id de la factura por pagar
	include('Interfase.Siigo.class.php');
	$F=qo("select * from factura where id=$id");
	$tdc=qo1("select td_contable from tipo_documento where id=$F->tipo_doc");
	$Proveedor=qo("select * from proveedor where id=$F->proveedor");
	if($Detalle=q("select c.*,p.cuenta as cuentac,cc.codigo as codigocc,t.identificacion as idp
							FROM comprobante c,puc p, ccosto cc,proveedor t
							WHERE c.factura=$id  and c.cuenta=p.id and c.ccosto=cc.id and c.tercero=t.id
							ORDER BY c.ta desc "))
	{
		$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
		if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
		$DD1=fopen($DESTINO_PLANO1,'w+');
		$Secuencia = 1;
		while($D=mysql_fetch_object($Detalle))
		{
			$I = new Interfase_documento();
			$I->Tipo_comprobante = l( $tdc, 1 );
			$I->Codigo_comprobante = substr( $tdc, 1, 3 );
			$I->Numero_documento = $F->consecutivo_contable;
			$I->Secuencia = $Secuencia;
			$I->Nit = $D->idp;
			$I->Cuenta_contable = str_pad($D->cuentac,10,'0',STR_PAD_RIGHT);
			$I->Fecha_documento = date('Ymd',strtotime($F->fecha_emision));
			$I->Centro_costos = l($D->codigocc,4);
			$I->Subcentro_costos = r($D->codigocc,3);
			$I->Descripcion = $D->detalle;
			$I->Afectacion = $D->ta;
			$I->Valor=$D->valor;
			$I->Base_retencion=$D->base;
			fwrite( $DD1,$I->genera());
			$Secuencia++;
		}
		fclose( $DD1 );
		echo "<script language='javascript'>
			function pasar()
			{
				window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=p.txt','Oculto1');
			}
		</script>
		<body onload='pasar();'>
		<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		</body>";
	}
	else
	{
		echo "<script language='javascript'>alert('No ha generado la contabilidad de este documento');</script>";
	}
}

function genera_causacion_cxp2()
{
	global $id,$Reporte;  // id es el id de la factura por pagar
	$Fac=qo("select * from factura where id=$id");
	$Proveedor=qo("select * from proveedor where id=$Fac->proveedor");
	$NT=tu('comprobante','id');
	html('CAUSACION CONTABLE DE LA FACTURA POR PAGAR');
	echo "<script language='javascript'>
			function refrescar()
			{
				modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$NT&VINCULOT=$id&VINCULOC=factura',0,0,500,1000,'cb');
				opener.parent.location.reload();
				window.close();
				void(null);
			}
			function refrescar_reporte()
			{
				modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$NT&VINCULOT=$id&VINCULOC=factura',0,0,500,1000,'cb');
				opener.location.reload();
				window.close();
				void(null);
			}
		</script>
		<body><script language='javascript'>centrar(600,600);</script>
		<form action='zgenerador_contable.php' method='post' target='Oculto_gc' name='forma' id='forma'>
			Consecutivo contable: <input type='text' name='consecutivo_contable' size=13 maxlength=11 value='".($Fac->consecutivo_contable?$Fac->consecutivo_contable:$Fac->numero)."'><br />
			<input type='submit' value='CONTINUAR'>
			<input type='hidden' name='Acc' value='genera_causacion_cxp_detalle2'>
			<input type='hidden' name='id' value='$id'>
			<input type='hidden' name='Reporte' value='$Reporte'>
		</form>
		<iframe name='Oculto_gc' id='Oculto_gc' style='visibility:hidden' width=1 height=1></iframe>
		</body>";
}

function genera_causacion_cxp_detalle2()
{
	global $id,$consecutivo_contable;
	$Fac=qo("select * from factura where id=$id");
	$consecutivo_contable=str_pad($consecutivo_contable,11,'0',STR_PAD_LEFT);
	q("update factura set consecutivo_contable='$consecutivo_contable' where id=$id");
	echo "<body><script language='javascript'>window.open('zgenerador_contable.php?Acc=exporta_cxp2&id=$id','_self');</script></body>";
}

function exporta_cxp2()
{
	global $id;  // id es el id de la factura por pagar
	include('Interfase.Siigo.class.php');
	$F=qo("select * from factura where id=$id");
	$CC='0001000';
	$tdc=qo1("select td_contable from tipo_documento where id=$F->tipo_doc");
	if($Detalle=q("select d.*,t.identificacion as idp FROM fac_detalle d,proveedor t WHERE d.factura=$id  and d.tercero=t.id  order by debito desc,credito "))
	{
		$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
		if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
		$DD1=fopen($DESTINO_PLANO1,'w+');
		$Secuencia = 1;
		while($D=mysql_fetch_object($Detalle))
		{
			$I = new Interfase_documento();
			$I->Tipo_comprobante = l( $tdc, 1 );
			$I->Codigo_comprobante = substr( $tdc, 1, 3 );
			$I->Numero_documento = $F->consecutivo_contable;
			$I->Secuencia = $Secuencia;
			$I->Nit = $D->idp;
			$I->Cuenta_contable = str_pad($D->cuenta,10,'0',STR_PAD_RIGHT);
			$I->Fecha_documento = date('Ymd',strtotime($F->fecha_emision));
			$I->Centro_costos = l($CC,4);
			$I->Subcentro_costos = r($CC,3);
			$I->Descripcion = $D->concepto;
			$I->Afectacion = ($D->debito?'D':'C');
			$I->Valor=($D->debito?$D->debito:$D->credito);
			$I->Base_retencion=$D->base;
			fwrite( $DD1,$I->genera());
			$Secuencia++;
		}
		fclose( $DD1 );
		echo "<script language='javascript'>
			function pasar()
			{
				window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=p.txt','Oculto1');
			}
		</script>
		<body onload='pasar();'>
		<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		</body>";
	}
	else
	{
		echo "<script language='javascript'>alert('No ha generado la contabilidad de este documento');</script>";
	}
}

function exporta_terceros()
{
	global $id;
	if($Clientes=q("select p.*,t.codigo_siigo as cti,rt.codigo_siigo as crt  from proveedor p, tipo_identificacion t, regimen_tributario rt where p.id  in ($id) and p.td=t.codigo and p.regimen=rt.id"))
	{
		$DESTINO_PLANO2 = 'planos/ter1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el tercero
		if(@is_file($DESTINO_PLANO2)) @unlink($DESTINO_PLANO2);
		$DD2=fopen($DESTINO_PLANO2,'w+');
		while($Cliente=mysql_fetch_object($Clientes))
		{
			$C=new interfase_tercero();
			$C->Nit=$Cliente->identificacion;
			$C->Nombre=$Cliente->nombre;
			$C->Contacto=$C->Nombre;
			$C->Direccion=$Cliente->direccion;
			$C->Telefono1=$Cliente->telefono1;
			$C->Telefono2=$Cliente->telefono2;
			$C->Telefono3=$Cliente->celular;
			$C->Email=$Cliente->email;
			$C->Sexo=$Cliente->sexo;
			$C->Tipo_identificacion=$Cliente->cti;
			$C->Digito_verificacion=$Cliente->dv;
			$C->Tipo_persona=$Cliente->tipo_persona;
			$C->Codigo_clasificacion_tributaria=$Cliente->crt;
			fwrite( $DD2,$C->genera());
		}
		fclose($DD2);
		html();
		echo "<body><script language='javascript'>	window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO2&Salida=t.txt','_self');</script>";
	}
	else
		echo "<body><script language='javascript'>alert('No se pudo obtener información: $id');window.close();void(null);</script>";
}

function exporta_caja_menor()
{
	global $id;
	include('Interfase.Siigo.class.php');
	$C=qo("select * from caja_menor where id=$id");
	$Ofi=qo("select * from oficina where id=$C->oficina");
	$Pasivo=$C->flotas?$Ofi->ccaja_menor_flotas:$Ofi->ccaja_menor;
	html("EXPORTACION CONTABLE - CAJA MENOR - $Ofi->nombre");
	$tdc='L004';
	if($Detalle=q("select d.*,p.cuenta from caja_menord d,tipo_caja_menor t,puc p where d.caja=$id and d.tipo=t.id and t.cuenta=p.id "))
	{
		$idts='0';
		$Terceros=q("select distinct tercero from caja_menord where caja=$id ");
		while($T=mysql_fetch_object($Terceros))
		{
			$idts.=','.$T->tercero;
		}

		$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
		if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
		$DD1=fopen($DESTINO_PLANO1,'w+');
		$Secuencia = 1;
		include('inc/link.php');
		while($D=mysql_fetch_object($Detalle))
		{
			$I = new Interfase_documento();
			$I->Tipo_comprobante = l( $tdc, 1 );
			$I->Codigo_comprobante = substr( $tdc, 1, 3 );
			$I->Numero_documento = $C->consecutivo_contable;
			$I->Secuencia = $Secuencia;
			$I->Nit = qo1m("select identificacion from proveedor where id=$D->tercero",$LINK);
			$I->Cuenta_contable = str_pad($D->cuenta,10,'0',STR_PAD_RIGHT);
			$I->Fecha_documento = date('Ymd',strtotime($C->fecha));
			$I->Centro_costos = '0001';
			$I->Subcentro_costos = '000';
			$I->Descripcion = $D->concepto_alcofin;
			$I->Afectacion = 'D';
			$I->Valor=$D->valor;
			$I->Base_retencion=0;
			fwrite( $DD1,$I->genera());
			$Secuencia++;
		}
		mysql_close($LINK);
		$I = new Interfase_documento();
		$I->Tipo_comprobante = l( $tdc, 1 );
		$I->Codigo_comprobante = substr( $tdc, 1, 3 );
		$I->Numero_documento = $C->consecutivo_contable;
		$I->Secuencia = $Secuencia;
		$I->Cuenta_contable = str_pad($Pasivo,10,'0',STR_PAD_RIGHT);
		$I->Fecha_documento = date('Ymd',strtotime($C->fecha));
		$I->Centro_costos = '0001';
		$I->Subcentro_costos = '000';
		$I->Descripcion ='REEMB.CJ.MENOR '.$C->elaborado_por ;
		$I->Nit ='900174552';
		$I->Afectacion = 'C';
		$I->Valor=$C->reembolso;
		fwrite( $DD1,$I->genera());
		$Secuencia++;
		fclose( $DD1 );
		echo "<script language='javascript'>
				function pasar()
				{
					window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=p.txt','Oculto1');
					window.open('zgenerador_contable.php?Acc=exporta_terceros&id=$idts','Int_ter');
				}
			</script>
			<body onload='pasar();' onclick='window.close();void(null);'>
			<iframe name='Int_ter' style='visibility:hidden' height='1' width='1'></iframe>
			<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
			</body>";

	}

}

function genera_causacion_helisa()
{
	global $id,$Reporte;  // id es el id de la factura por pagar
	$Fac=qo("select * from factura where id=$id");
	$Proveedor=qo("select * from proveedor where id=$Fac->proveedor");
	$NT=tu('comprobante','id');
	html('CAUSACION CONTABLE DE LA FACTURA POR PAGAR');
	echo "<script language='javascript'>
			function refrescar()
			{
				modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$NT&VINCULOT=$id&VINCULOC=factura',0,0,500,1000,'cb');
				opener.parent.location.reload();
				window.close();
				void(null);
			}
			function refrescar_reporte()
			{
				modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$NT&VINCULOT=$id&VINCULOC=factura',0,0,500,1000,'cb');
				opener.location.reload();
				window.close();
				void(null);
			}
		</script>
		<body><script language='javascript'>centrar(600,600);</script>
		<form action='zgenerador_contable.php' method='post' target='Oculto_gc' name='forma' id='forma'>
			Consecutivo contable: <input type='text' name='consecutivo_contable' size=13 maxlength=11 value='".($Fac->consecutivo_contable?$Fac->consecutivo_contable:$Fac->numero)."'><br />
			<input type='submit' value='CONTINUAR'>
			<input type='hidden' name='Acc' value='genera_causacion_helisa_detalle'>
			<input type='hidden' name='id' value='$id'>
			<input type='hidden' name='Reporte' value='$Reporte'>
		</form>
		<iframe name='Oculto_gc' id='Oculto_gc' style='visibility:hidden' width=1 height=1></iframe>
		</body>";
}

function genera_causacion_helisa_detalle()
{
	global $id,$consecutivo_contable;
	$Fac=qo("select * from factura where id=$id");
	$consecutivo_contable=str_pad($consecutivo_contable,8,'0',STR_PAD_LEFT);
	q("update factura set consecutivo_contable='$consecutivo_contable' where id=$id");
	echo "<body><script language='javascript'>window.open('zgenerador_contable.php?Acc=exporta_helisa_cxp&id=$id','_self');</script></body>";
}

function exporta_helisa_cxp()
{
	global $id;  // id es el id de la factura por pagar
	include('Interfase.Helisa.class.php');
	$F=qo("select * from factura where id=$id");
	$CC='0101';
	$tdc=qo1("select td_contable from tipo_documento where id=$F->tipo_doc");
	if($Detalle=q("select d.*,t.identificacion as idp FROM fac_detalle d,proveedor t WHERE d.factura=$id  and d.tercero=t.id  order by debito desc,credito "))
	{
		$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
		if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
		$DD1=fopen($DESTINO_PLANO1,'w+');
		$Secuencia = 1;
		include('inc/link.php');
		while($D=mysql_fetch_object($Detalle))
		{
			if($D->centro_costo) $CC=qo1m("select codigo from ccosto where id=$D->centro_costo",$LINK); else $CC='0101';
			$I = new Interfase_documento();
			$I->Tipo_comprobante = 'FP';
			$I->Numero_documento = $F->consecutivo_contable;
			$I->Nit = $D->idp;
			$I->Cuenta_contable = $D->cuenta;
			$I->Fecha_documento = date('d/m/Y',strtotime($F->fecha_emision));
			$I->Centro_costos = $CC;
			$I->Descripcion = $D->concepto;
			$I->Afectacion = ($D->debito?'D':'C');
			$I->Valor=($D->debito?$D->debito:$D->credito);
			$I->Cheque=$F->numero;
			$I->Base=$D->base;
			fwrite( $DD1,$I->genera());
			$Secuencia++;
		}
		mysql_close($LINK);
		fclose( $DD1 );
		echo "<script language='javascript'>
			function pasar()
			{
				window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=p.txt','Oculto1');
			}
		</script>
		<body onload='pasar();'>
		<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		</body>";
	}
	else
	{
		echo "<script language='javascript'>alert('No ha generado la contabilidad de este documento');</script>";
	}
}

function exporta_caja_menor_helisa()
{
	global $id;
	include('Interfase.Helisa.class.php');
	$C=qo("select * from caja_menor where id=$id");
	$Ofi=qo("select * from oficina where id=$C->oficina");
	$Tc=tabla2arreglo('tipo_compra',$Campos=array('id','cuenta_contable'));
	$Pasivo=$C->flotas?$Ofi->ccaja_menor_flotas:$Ofi->ccaja_menor;
	html("EXPORTACION CONTABLE - CAJA MENOR - $Ofi->nombre");
	echo "$Tc[1] <br> $Tc[2]";
	$tdc=$C->td_contable;
	if($Detalle=q("select d.*,p.cuenta from caja_menord d,tipo_caja_menor t,puc p where d.caja=$id and d.tipo=t.id and t.cuenta=p.id "))
	{
		$idts='0';
		$Terceros=q("select distinct tercero from caja_menord where caja=$id ");
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
			$I = new Interfase_documento();
			$I->Tipo_comprobante = $tdc;
			$I->Numero_documento = str_pad($C->consecutivo_contable,8,'0',STR_PAD_LEFT);
			$I->Nit = qo1m("select identificacion from proveedor where id=$D->tercero",$LINK);
			$I->Cuenta_contable = $D->cuenta;
			$I->Fecha_documento = date('d/m/Y',strtotime($C->fecha));
			$I->Centro_costos = $Ofi->ccostos;
			$I->Descripcion = $D->concepto;
			$I->Afectacion = 'D';
			$I->Valor=$D->subtotal;
			fwrite( $DD1,$I->genera());
			if($D->iva)
			{
				$I = new Interfase_documento();
				$I->Tipo_comprobante = $tdc;
				$I->Numero_documento = str_pad($C->consecutivo_contable,8,'0',STR_PAD_LEFT);
				$I->Nit = qo1m("select identificacion from proveedor where id=$D->tercero",$LINK);
				$I->Cuenta_contable = $Tc[$D->tipo_compra];
				$I->Fecha_documento = date('d/m/Y',strtotime($C->fecha));
				$I->Centro_costos = $Ofi->ccostos;
				$I->Descripcion = $D->concepto;
				$I->Afectacion = 'D';
				$I->Valor=$D->iva;
				fwrite( $DD1,$I->genera());
			}
		}
		mysql_close($LINK);
		$I = new Interfase_documento();
		$I->Tipo_comprobante = $tdc;
		$I->Numero_documento = str_pad($C->consecutivo_contable,8,'0',STR_PAD_LEFT);
		$I->Cuenta_contable = $Pasivo;
		$I->Fecha_documento = date('d/m/Y',strtotime($C->fecha));
		$I->Centro_costos = $Ofi->ccostos;
		$I->Descripcion ='REEMB.CJ.MENOR '.$C->elaborado_por ;
		$I->Nit ='900174552';
		$I->Afectacion = 'C';
		$I->Valor=$C->reembolso;
		fwrite( $DD1,$I->genera());
		fclose( $DD1 );
		echo "<script language='javascript'>
				function pasar()
				{
					window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=p.txt','Oculto1');
				//	window.open('zgenerador_contable.php?Acc=exporta_terceros&id=$idts','Int_ter');
				}
			</script>
			<body onload='pasar();' onclick='window.close();void(null);'>
			<iframe name='Int_ter' style='visibility:hidden' height='1' width='1'></iframe>
			<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
			</body>";
	}
}


?>