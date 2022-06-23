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
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');die();}

function exportar_factura()
{
	global $id,$idf;
	include('Interfase.Siigo.class.php');
	$TDC=8;  // id del tipo de documento contable F006 FACTURA DE VENTA
	$GRUPO_CONTABLE=1; // UNICO GRUPO CONTABLE
	$F = qo( "select * from factura where id=$id" );
	$CFG=qo("select * from cfg_factura where activo=1");
	$Aseguradora=qo("select * from aseguradora where id=$F->aseguradora");
	$TDC = qo( "select * from tipo_doc_cont where id=$TDC" );
	$Cliente = qo( "select * from cliente where id=$F->cliente" );
	$Centro_costos = '0001';
	$Subcentro_costos = '000';
	// ###################### INICIO DE LA GENERACION DE LA FACTURA ########################
	$Detalle = q( "select *,t_concepto_fac(concepto) as nconcepto from facturad where factura=$id " );
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
	$Valor_retecree=0;
	if($F->anulada==1) $Descripcion="FACTURA $F->consecutivo ANULADA";
	else $Descripcion=l("FAC $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
	$Total_retenciones=0;
	///****   AUTO RETENCION DE CREE - QUE SE DEBE HACER PARA TODAS LAS FACTURAS
	$Valor_retecree=round($F->subtotal*$CFG->auto_rete_cree/1000,0);
	//  partida DEBITO
	$I = new Interfase_documento();
	$I->Tipo_comprobante = l( $TDC->codigo, 1 );
	$I->Codigo_comprobante = substr( $TDC->codigo, 1, 3 );
	$I->Numero_documento = $F->consecutivo;
	$I->Secuencia = $Secuencia;
	$I->Nit = $Cliente->identificacion;
	$I->Cuenta_contable = $CFG->arc_debito;
	$I->Fecha_documento = $Fecha_doc;
	$I->Centro_costos = $Centro_costos;
	$I->Subcentro_costos = $Subcentro_costos;
	$I->Descripcion = l("Auto Rete Cree $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
	$I->Afectacion = 'D';
	$I->Valor=($F->anulada?0:$Valor_retecree);
	fwrite( $DD1,$I->genera());
	$Secuencia++;
	//  partida CREDITO
	$I = new Interfase_documento();
	$I->Tipo_comprobante = l( $TDC->codigo, 1 );
	$I->Codigo_comprobante = substr( $TDC->codigo, 1, 3 );
	$I->Numero_documento = $F->consecutivo;
	$I->Secuencia = $Secuencia;
	$I->Nit = $Cliente->identificacion;
	$I->Cuenta_contable = $CFG->arc_credito;
	$I->Fecha_documento = $Fecha_doc;
	$I->Centro_costos = $Centro_costos;
	$I->Subcentro_costos = $Subcentro_costos;
	$I->Descripcion = l("Auto Rete Cree $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
	$I->Afectacion = 'D';
	$I->Valor=($F->anulada?0:$Valor_retecree);
	fwrite( $DD1,$I->genera());
	$Secuencia++;
		
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
	// PARTIDA DE LA CUENTA DEL ACTIVO 
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
	{// PARTIDA DEL IVA
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
	{// PARTIDAS DEL DETALLE
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
// ACTIVA LA DESCARGA DE ARCHIVOS PLANOS
	echo "<script language='javascript'>
		function pasar()
		{

		window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=f.txt','Oculto1');
		window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO2&Salida=t.txt','Oculto2');
		}
		</script>
		<body onload='pasar();'>
		<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		<iframe name='Oculto2' style='visibility:hidden' height='1' width='1'></iframe>
		</body>";
}

function exportar_factura_helisa() 
{
	global $id;
	if($id) $Consecutivo=qo1("select consecutivo from factura where id=$id"); else $Consecutivo=0;
	html('EXPORTACION DE DOCUMENTOS');
	echo "<body>
		<form action='zgenerador_contable.php' target='_self' method='POST' name='forma' id='forma'>
			Consecutivo inicial: ".menu1('consecutivoi',"select consecutivo,concat(consecutivo,' ',fecha_emision) from factura order by consecutivo",$Consecutivo,1)."<br><br>
			Consecutivo final: ".menu1('consecutivof',"select consecutivo,concat(consecutivo,' ',fecha_emision) from factura order by consecutivo",$Consecutivo,1)."<br><br>
			<input type='submit' name='continuar' id='continuar' value=' CONTINUAR ' >
			<input type='hidden' name='Acc' value='exportar_factura_helisa_ok'>
		</form>
	</body>";
}

function exportar_factura_helisa_ok()
{
	global $consecutivoi,$consecutivof;
	error_reporting(E_ALL);
	include('Interfase.Helisa.class.php');

	$TDC=8;  // id del tipo de documento contable F006 FACTURA DE VENTA
	$GRUPO_CONTABLE=1; // UNICO GRUPO CONTABLE
	$TDC = qo( "select * from tipo_doc_cont where id=$TDC" );
	$CFG=qo("select * from cfg_factura where activo=1");
	html('RESULTADO DE LA EXPORTACIÓN');
	echo "<body><h3>Resultado de la exportación</h3>
	Consecutivo inicial: $consecutivoi  Consecutivo final: $consecutivof ";
	if($Facturas = q( "select * from factura where consecutivo between $consecutivoi and $consecutivof order by consecutivo" ))
	{
		echo "Registros: ".mysql_num_rows($Facturas);
		
		$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
		if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
		$DD1=fopen($DESTINO_PLANO1,'w+');
		
		$DESTINO_PLANO2 = 'planos/int2a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para terceros
		if(@is_file($DESTINO_PLANO2)) @unlink($DESTINO_PLANO2);
		$DD2=fopen($DESTINO_PLANO2,'w+');
		
		$DESTINO_PLANO3 = 'planos/int3a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para terceros
		if(@is_file($DESTINO_PLANO3)) @unlink($DESTINO_PLANO3);
		$DD3=fopen($DESTINO_PLANO3,'w+');
		
		while($F=mysql_fetch_object($Facturas))
		{
			echo "<br>Procesando Consecutivo: $F->consecutivo";
			$Aseguradora=qo("select * from aseguradora where id=$F->aseguradora");
			$Cliente = qo( "select * from cliente where id=$F->cliente" );
			$Centro_costos = '0101';$Ofic=qo("select * from oficina where id=1");
			if($F->siniestro)
			{
				$Sin=qo("select ciudad from siniestro where id=$F->siniestro");
				$Ofic=qo("select * from oficina where ciudad='$Sin->ciudad' ");
			}
			
			//////////   INTERFASE DE TERCEROS para importar en el módulo de Terceros de Helisa.
			
			$T=new interfase_tercero();
			$T->Identidad	=$Cliente->identificacion;
			$T->Dv			=$Cliente->dv; 
			$T->Clase		=qo1("select codigo_helisa from tipo_identificacion where codigo='$Cliente->tipo_id' ");  
			$T->Nombres	=$Cliente->nombre.' '.$Cliente->apellido;
			$T->Direccion=$Cliente->direccion;
			$T->Telefono	=$Cliente->celular.' '.$Cliente->telefono_casa.' '.$Cliente->telefono_oficina;
			$T->Ciudad	=l($Cliente->ciudad,5);
			$T->Nombre_ciudad=qo1("select nombre from ciudad where codigo='$Cliente->ciudad' ");
			$T->Email		=$Cliente->email_e;
			$T->Apellido1	=$Cliente->apellido;
			$T->Nombre1	=$Cliente->nombre;
			fwrite($DD2,$T->genera());
			
			//////////   INTERFASE DE CLIENTES para importar en el módulo de Cartera de Helisa
			
			$T=new interfase_cliente();
			$T->Identidad	=$Cliente->identificacion;
			$T->Dv			=$Cliente->dv; 
			$T->Clase		=qo1("select codigo_helisa from tipo_identificacion where codigo='$Cliente->tipo_id' ");  
			$T->Nombres	=$Cliente->nombre.' '.$Cliente->apellido;
			$T->Direccion=$Cliente->direccion;
			$T->Telefono	=$Cliente->celular.' '.$Cliente->telefono_casa.' '.$Cliente->telefono_oficina;
			$T->Ciudad	=l($Cliente->ciudad,5);
			$T->Nombre_ciudad=qo1("select nombre from ciudad where codigo='$Cliente->ciudad' ");
			$T->Email		=$Cliente->email_e;
			$T->Apellido1	=$Cliente->apellido;
			$T->Nombre1	=$Cliente->nombre;
			fwrite($DD3,$T->genera());
			
			

			// ###################### INICIO DE LA GENERACION DE LA FACTURA ########################
			$Detalle = q( "select *,t_concepto_fac(concepto) as nconcepto from facturad where factura=$F->id " );
			$Fecha_doc=date('d/m/Y',strtotime($F->fecha_emision));
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
				$I->Tipo_comprobante = 'FV';
				$I->Numero_documento = $F->consecutivo;
				$I->Nit = $Cliente->identificacion;
				$I->Cuenta_contable = $Aseguradora->vrete_iva;
				$I->Fecha_documento = $Fecha_doc;
				$I->Centro_costos = $Ofic->ccostos;
				$I->Descripcion = l("Rete Iva $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
				$I->Afectacion = 'D';
				$I->Valor=($F->anulada?0:$Valor_retencion1);
				$I->Cheque=($F->anulada?0:$F->subtotal);
				fwrite( $DD1,$I->genera());
				///****   AUTO RETENCION DE LA FUENTE
				$Valor_retencion2=round($F->subtotal*$Aseguradora->pvrete_fuente/100,0);
				$I = new Interfase_documento();
				$I->Tipo_comprobante = 'FV';
				$I->Numero_documento = $F->consecutivo;
				$I->Nit = $Cliente->identificacion;
				$I->Cuenta_contable = $Aseguradora->vrete_fuente;
				$I->Fecha_documento = $Fecha_doc;
				$I->Centro_costos = $Ofic->ccostos;
				$I->Descripcion = l("Rete Fte $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
				$I->Afectacion = 'D';
				$I->Valor=($F->anulada?0:$Valor_retencion2);
				$I->Cheque=($F->anulada?0:$F->subtotal);
				fwrite( $DD1,$I->genera());
				///****   AUTO RETENCION DE ICA
				$Valor_retencion3=round($F->subtotal*$Aseguradora->pvrete_ica/1000,0);
				$I = new Interfase_documento();
				$I->Tipo_comprobante = 'FV';
				$I->Numero_documento = $F->consecutivo;
				$I->Nit = $Cliente->identificacion;
				$I->Cuenta_contable = $Aseguradora->vrete_ica;
				$I->Fecha_documento = $Fecha_doc;
				$I->Centro_costos = $Ofic->ccostos;
				$I->Descripcion = l("Rete Ica $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
				$I->Afectacion = 'D';
				$I->Valor=($F->anulada?0:$Valor_retencion3);
				$I->Cheque=($F->anulada?0:$F->subtotal);
				fwrite( $DD1,$I->genera());
				/// TOTALIZO LAS RETENCIONES PARA CALCULAR EL VALOR NETO DEL INGRESO
				$Total_retenciones=$Valor_retencion1+$Valor_retencion2+$Valor_retencion3;
			}

			///  CUENTA DE CARTERA.
			
			$I = new Interfase_documento();
			$I->Tipo_comprobante = 'FV';
			$I->Numero_documento = $F->consecutivo;
			$I->Nit = $Cliente->identificacion;
			$I->Cuenta_contable = '13050501';
			$I->Fecha_documento = $Fecha_doc;
			$I->Centro_costos = $Ofic->ccostos;
			$I->Descripcion =$Descripcion;
			$I->Afectacion = 'D';
			$I->Valor=($F->anulada?0:$F->total-$Total_retenciones);
			//$I->Cheque=($F->anulada?0:$F->subtotal);
			fwrite( $DD1,$I->genera());

			if($F->iva)
			{
				$I = new Interfase_documento();
				$I->Tipo_comprobante = 'FV';
				$I->Numero_documento = $F->consecutivo;
				$I->Nit = $Cliente->identificacion;
				$I->Cuenta_contable = '24080501';
				$I->Fecha_documento = $Fecha_doc;
				$I->Centro_costos = $Ofic->ccostos;
				$I->Descripcion = l("FAC $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
				$I->Afectacion = 'C';
				$I->Valor=($F->anulada?0:$F->iva);
				$I->Cheque=($F->anulada?0:$F->subtotal);
				fwrite( $DD1,$I->genera());
			}
			echo " Procesando detalle.. ";
			$DIFERIDO=false;
			while ( $D = mysql_fetch_object( $Detalle ) )
			{ // ASIENTOS PARA EL DETALLE
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
								if(l($Cuenta_contable,2)=='28') $DIFERIDO=true;
								$Descripcion=qo1("select nombre from concepto_fac where id=$D->concepto");
								echo "<br>$Cuenta_contable";
								if(strpos($Cuenta_contable,'X'))
								{
									echo "<br>Cambiando de cuenta";
									$Concepto_fac=qo("select * from concepto_fac where id=$D->concepto");
									$Cuenta_contable=qo1("select $Concepto_fac->campo_ingresos from oficina where id=$Ofic->id");
									echo "<br>$Cuenta_contable";
								}
							}
							else
							{
								$Cuenta_contable=qo1("select cuenta_ingreso from aseguradora where id=$F->aseguradora");
								$Descripcion=qo1("select nombre from concepto_fac where id=$D->concepto");
							}
						}
						$I = new Interfase_documento();
						$I->Tipo_comprobante = 'FV';
						$I->Numero_documento = $F->consecutivo;
						$I->Nit = $Cliente->identificacion;
						$I->Cuenta_contable = $Cuenta_contable;
						$I->Fecha_documento = $Fecha_doc;
						$I->Centro_costos = $Ofic->ccostos;
						$I->Descripcion = l(($F->anulada?'ANULADA: ':'').$Descripcion,50);
						$I->Afectacion = 'C';
						$I->Valor=($F->anulada?0:$D->cantidad*$D->unitario);
						fwrite( $DD1,$I->genera());
					}
				}
			}
			echo " Fin proceso de detalle. ";
			if(!$DIFERIDO)
			{
				///****   AUTO RETENCION CREE
				$Valor_retecree=round($F->subtotal*$CFG->auto_rete_cree/1000,0);
				// partida DEBITO
				$I = new Interfase_documento();
				$I->Tipo_comprobante = 'FV';
				$I->Numero_documento = $F->consecutivo;
				$I->Nit = $Cliente->identificacion;
				$I->Cuenta_contable = $CFG->arc_debito;
				$I->Fecha_documento = $Fecha_doc;
				$I->Centro_costos = $Ofic->ccostos;
				$I->Descripcion = l("Auto Rete Cree $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
				$I->Afectacion = 'D';
				$I->Valor=($F->anulada?0:$Valor_retecree);
				$I->Cheque=($F->anulada?0:$F->subtotal);
				fwrite( $DD1,$I->genera());
			
				// partida CREDITO
				$I = new Interfase_documento();
				$I->Tipo_comprobante = 'FV';
				$I->Numero_documento = $F->consecutivo;
				$I->Nit = $Cliente->identificacion;
				$I->Cuenta_contable = $CFG->arc_credito;
				$I->Fecha_documento = $Fecha_doc;
				$I->Centro_costos = $Ofic->ccostos;
				$I->Descripcion = l("Auto Rete Cree $F->consecutivo $Cliente->nombre $Cliente->apellido ",50);
				$I->Afectacion = 'C';
				$I->Valor=($F->anulada?0:$Valor_retecree);
				$I->Cheque=($F->anulada?0:$F->subtotal);
				fwrite( $DD1,$I->genera());
			}

		}
		fclose( $DD1 );fclose( $DD2 );fclose( $DD3 );
		// descarga de los archivos planos
		echo "<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		<iframe name='Oculto2' style='visibility:hidden' height='1' width='1'></iframe>
		<iframe name='Oculto3' style='visibility:hidden' height='1' width='1'></iframe>
		<script language='javascript'>
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=fac.txt','Oculto1');
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO2&Salida=ter.txt','Oculto2');
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO3&Salida=cli.txt','Oculto3');
		</script>
		</body>";
	}	
}

function exporta_rc_helisa()  // LA EXPORTACION SE INICIA DESDE EL PROGRAMA DE CARTERA E INVOCA ESTA RUTINA.
{
	global $recibos;
	html('EXPORTACION CONTABLE DE RECIBOS DE CAJA A HELISA');
	
	error_reporting(E_ALL);
	include('Interfase.Helisa.class.php');
	
	$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
	if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
	$DD1=fopen($DESTINO_PLANO1,'w+');
	
	$Recibos=explode(';',$recibos);
	include('inc/link.php');
	foreach($Recibos as $indice => $consec)
	{
		if($consec)
		{
			$Partes=explode(',',$consec);
			
			$consec_contable=$Partes[1];
			$id=$Partes[0];
			$Recibo=mysql_query("select * from recibo_caja where id=$id",$LINK);
			$RC=mysql_fetch_object($Recibo);
			$Oficina=qom("select * from oficina where id=$RC->oficina",$LINK);
			$td_contable=$Oficina->tipo_doc_rc;
			mysql_query("update recibo_caja set td_contable='$td_contable', consec_contable='$consec_contable' where id='$id' ",$LINK);
			echo "<br>$id: Tipo documento: $td_contable  Consecutivo: $consec_contable";
			$Recibo=mysql_query("select * from recibo_caja where id=$id",$LINK);
			$RC=mysql_fetch_object($Recibo);
			$Cliente=qom("select * from cliente where id=$RC->cliente",$LINK);
			$Banco=qom("select * from banco_aoa where id=$RC->banco_aoa",$LINK);
			$Factura=qom("select * from factura where id=$RC->factura",$LINK);
			$Concepto="PAGO FV $Factura->consecutivo - OF. $Oficina->sigla RC. $RC->consecutivo ";
			echo " preparando la clase ";
			// ASIENTO DE AFECTACION A LA CUENTA DE CARTERA
			$I = new Interfase_documento();
			echo " escribiendo el registro ";
			$I->Tipo_comprobante = $RC->td_contable;
			$I->Numero_documento = $RC->consec_contable;
			$I->Nit = $Cliente->identificacion;
			$I->Cuenta_contable = '13050501';
			$I->Fecha_documento = date('d/m/Y',strtotime($RC->fecha));
			$I->Centro_costos = $Oficina->ccostos;
			$I->Descripcion = l(($RC->anulado?'ANULADO: ':'').$Concepto,50);
			$I->Afectacion = 'C';
			$I->Valor=($RC->anulado?0:$RC->valor);
			
			fwrite( $DD1,$I->genera());
			// ASIENTO PARA LA CUENTA DEL BANCO
			$I = new Interfase_documento();
			$I->Tipo_comprobante = $RC->td_contable;
			$I->Numero_documento = $RC->consec_contable;
			$I->Nit = $Cliente->identificacion;
			$I->Cuenta_contable = $Banco->cuenta_contable;
			$I->Fecha_documento = date('d/m/Y',strtotime($RC->fecha));
			//$I->Centro_costos = $Oficina->ccostos;
			$I->Descripcion = l(($RC->anulado?'ANULADO: ':'').$Concepto,50);
			$I->Afectacion = 'D';
			$I->Valor=($RC->anulado?0:$RC->valor);
			$I->Cheque=$Banco->cuenta;
			fwrite( $DD1,$I->genera());
		}
	}
	mysql_close($LINK);
	echo "<br>";
	echo " Fin proceso de detalle. ";
	fclose( $DD1 );
	echo "<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		<script language='javascript'>
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=rc.txt','Oculto1');
		</script>
		</body>";
}

function exportacion_garantias()
{
	html();
	//  *******************     PRIMERA CONTABILIZACION   ASIENTO 1 Y 2  ********************************
	//  CAJA COMUN PARA GARANTIAS ES LA 11051010  (DEBITO)
	//  DEPOSITOS RECIBIDOS HAY UNO POR CADA CIUDAD
	//  28101501 CALI
	//  28101502 IBAGUE
	//  28101503 MEDELLIN
	//  28101504 PEREIRA
	//  28101505 BARRANQUILLA
	//  28101506 BOGOTA
	//  28101507 BUCARAMANGA
	
	// la cuenta 28 va con tercero AOA y con el centro de costos de la ciudad
	// la cuenta 11051010 va con tercero AOA sin centro de costos
	// AGOSTO 9, se solicita que el tercero sea el real y no AOA para la cuenta 28
	
	//  *******************     SEGUNDA CONTABILIZACION   ASIENTO 3 Y 4  ********************************
	// CAJA COMUN PARA GARANTIAS (CREDITO)
	// BANCO AL QUE SE CONSIGNA (DEBITO) 
	
	// En esta pantalla de control se tendra que seleccionar el banco al que se consigna
	
	echo "<script language='javascript'></script>
	<body><h3>EXPORTACION DE GARANTIAS DE SERVICIO y  GARANTIAS NO REEMBOLSABLES</h3>
	<form action='zgenerador_contable.php' target='Tablero_exportacion_garantias' method='POST' name='forma' id='forma'>
		Fecha Inicial: ".pinta_FC('forma','FI',date('Y-m-d'))." Fecha Final: ".pinta_FC('forma','FF',date('Y-m-d'))." 
		<input type='submit' name='continuar' id='continuar' value=' CONTINUAR ' >
		<input type='hidden' name='Acc' value='exportacion_garantias_tablero'>
	</form>
	<iframe name='Tablero_exportacion_garantias' id='Tablero_exportacion_garantias' style='visibility:visible' width='98%' height='80%'></iframe>
	</body>";
}

function exportacion_garantias_tablero()
{
	global $FI,$FF;
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	$CONCILIADOR=0;
	if($USUARIO==6 /* Facturacion */) $CONCILIADOR=qo1("select concilia_rc from usuario_facturacion where id=".$_SESSION['Id_alterno']);
	if($USUARIO==9 /* Contadores */) $CONCILIADOR=1;
	$NTRC=tu('recibo_caja','id');
	$NTAU=tu('sin_autor','id');
	$NTSN=tu('siniestro','id');
	$Bancos=q("select * from banco_aoa order by cuenta_contable");$ABancos=array();
	while($banco=mysql_fetch_object($Bancos)) {$ABancos[$banco->id]=$banco->nombre.' '.$banco->cuenta;} // CREA UN ARREGLO DE BANCOS
	html();
	echo "<script language='javascript'>
	
	function modificar_rc(id)
	{modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTRC&id='+id,0,0,700,900,'rc');}
	
	function asigna_banco(banco,id)
	{	if(confirm('Desea asignar el banco a este recibo de caja?'))
		{	window.open('zgenerador_contable.php?Acc=asignar_banco&id='+id+'&banco='+banco,'Oculto_exportacionrc');	} }
	
//	var ConsecutivoCC=0;
	var Aplano=new Array();
	
//	function inicializacc(valor,id)
//	{
//		if(confirm('Desea inicializar el consecutivo con el valor '+valor+' ?'))
//		ConsecutivoCC=Number(valor);
//		cambiacc(true,id);
//	}
	
	function genera_planorc()
	{
		with(document.genplano)
		{
			recibos.value='';
			for(var i in Aplano) 
			{ 
				if(Aplano[i]!=0)
				{
					recibos.value+=i+','+Aplano[i]+';';
				}
			}
			submit();
		}
	}
	function modificar_sin_autor(id)
	{modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTAU&id='+id,0,0,700,900,'au');}
	function modificar_siniestro(id)
	{modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTSN&id='+id,0,0,700,900,'au');}
	

	function cambiacc(valor,id,consecutivo)
	{ // nueva funcion cambiac, en la que asigna el mismo consecutivo original del recibo de caja como consecutivo contable, ya no es necesario usar la variable ConsecutivoCC
		var Campo=document.getElementById('cc'+id);
		if(valor) 
		{ if(!Number(Campo.value))
			{	Campo.value=consecutivo;	Aplano[id]=consecutivo;	}
			else Aplano[id]=Campo.value;
		}
		else {Campo.value='0';Aplano[id]=0;} // elimina el consecutivo del arreglo de transferencia
	}	

//	function cambiacc(valor,id){var Campo=document.getElementById('cc'+id);if(valor) {if(!Number(Campo.value)){ConsecutivoCC++;Campo.value=ConsecutivoCC;Aplano[id]=ConsecutivoCC;	}else	{Aplano[id]=Campo.value;}}else {	Campo.value='0';ConsecutivoCC--;Aplano[id]=0;}}

	</script>
	<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
	<h3>EXPORTACION DE GARANTIAS EFECTIVO Y TARJETAS DEBITO [ $FI - $FF ]</h3>
	<iframe name='Oculto_exportacionrc' id='Oculto_exportacionrc' height=1 width=1 style='visibility:hidden'></iframe>";
	// OBTIENE TODOS LOS REGISTROS DE RECIBO DE CAJA ENTRE DOS FECHAS
	if($Registros=q("select rc.id,rc.fecha,o.sigla,o.ccostos,o.nombre as nofic,rc.consecutivo,concat(cl.nombre,' ',cl.apellido) as ncliente,
			rc.valor,rc.consignacion_f as cons2,rc.consignacion2_f as cons3,rc.anulado,rc.motivo_anulacion,s.consignacion_f as cons1,
			rc.consignacion_numero,rc.consignacion_numero2,rc.td_contable,rc.consec_contable,rc.banco_aoa,s.fecha_consignacion,
			s.numero_consignacion,rc.autorizacion,rc.siniestro
			FROM recibo_caja rc, oficina o,cliente cl,sin_autor s
			WHERE rc.oficina=o.id and rc.garantia=1 and rc.cliente=cl.id and rc.autorizacion=s.id and rc.fecha between '$FI' and '$FF' 
			ORDER BY rc.oficina, rc.fecha"))
	{
		echo "<table border='0' cellspacing='1' width='100%'><tr>
			<th>Oficina</th>
			<th>Fecha</th>
			<th>Consecutivo</th>
			<th>Anulado</th>
			<th>Cliente</th>
			<th>Valor</th>
			<th>Consignacion</th>
			<th>Banco</th>
			<th>Opciones</th>
			</tr>";
		while($R =mysql_fetch_object($Registros )) // PINTA LOS RECIBOS DE CAJA
		{
			echo "<tr>
			<td>$R->nofic</td>
			<td>$R->fecha</td>
			<td>$R->sigla $R->consecutivo</td>
			<td align='center'>".($R->anulado?"<img src='gifs/standar/si.png' style='cursor:pointer' border='0' alt='$R->motivo_anulacion' title='$R->motivo_anulacion'>":"")."</td>
			<td>$R->ncliente</td>
			<td align='right'>".coma_format($R->valor)."</td>
			<td align='center'>".pinta_imag_consig($R)." $R->consignacion_numero $R->consignacion_numero2 
				<a class='info'>$R->numero_consignacion<span style='width:200px'>Fecha de Consignacion: $R->fecha_consignacion</span><a>
			".($USUARIO==1?"<a class='info' style='cursor:pointer' onclick='modificar_sin_autor($R->autorizacion);'><img src='gifs/standar/Pencil.png' border='0' height='10'><span>Modificar Autorización</span></a> 
				<a class='info' style='cursor:pointer' onclick='modificar_siniestro($R->siniestro);'><img src='gifs/standar/Pencil.png' border='0' height='10'><span>Modificar Siniestro</span></a> 
				<a class='info' style='cursor:pointer' onclick='modificar_rc($R->id);'><img src='gifs/standar/Pencil.png' border='0' height='10'><span>Modificar Recibo Caja</span></a> 
				":"")."
			</td>
			<td>
				<select name='banco_$R->id' id='banco_$R->id' style='width:90px;font-size:9px;' ";
							// DE ACUERDO AL PERFIL SE FACILITA EL ESCOGER EL BANCO CORRESPONDIENTE AL RECAUDO
							if(inlist($USUARIO,'6,9') && $CONCILIADOR)  {if($R->banco_aoa) echo "disabled";}
							elseif($USUARIO!=1) echo "disabled";
							echo " onchange='asigna_banco(this.value,$R->id);'><option value=''></option>";
							foreach($ABancos as $idb => $nbanco)
							{
								echo "<option value='$idb' ".($idb==$R->banco_aoa?"selected":"").">$nbanco</option>";
							}
							echo "</select>
			</td>
			<td>";
			IF($R->banco_aoa)
			echo "<input type='checkbox' onchange='cambiacc(this.checked,$R->id,$R->consecutivo);'>
					<input class='numero' type='text' name='cc$R->id' id='cc$R->id' size=3 style='background-color:".($R->consec_contable?"ddffdd":"ffffaa")
					."'  value='$R->consec_contable' readonly>";
			echo "</td>
			</tr>";
		}
		echo "</table><br>
				<form action='zgenerador_contable.php' target='Oculto_exportacionrc' method='POST' name='genplano' id='genplano'>
				<input type='hidden' name='Acc' value='exporta_rc_helisa_garantia'>
				<input type='hidden' name='recibos'>
			</form><br>";
		if(inlist($USUARIO,'1,9')) echo "<a style='cursor:pointer' onclick='genera_planorc();'>Exportar Recibos de Caja a Contabilidad</a>";
			echo "<br><br><br><br>";
	}
	else
	{
		echo "<b style='color:aa0000'>No hay Recibos de caja de garantias en el rango de fechas dado.</b>";
	}
	echo "</body>";
}

function asignar_banco() // ASIGNA UN BANCO AL RECIBO DE CAJA 
{
	global $id,$banco,$Nusuario;
	q("update recibo_caja set banco_aoa=$banco where id=$id");
	graba_bitacora('recibo_caja','M',$id,'Asigna banco');
	//echo "<body><script language='javascript'>parent.parent.recargar();</script></body>";
}

function pinta_imag_consig($R) // SI TIENE IMAGENES DE CONSIGNACION MUESTRA BOTONES PARA VISUALIZARLAS AMPLIADAS
{
	return ($R->cons1?"<a onclick=\"modal('$R->cons1',0,0,800,800,'vi');\" style='cursor:pointer'><img src='gifs/standar/Preview.png' border='0'></a>":"").
			($R->cons2?"<a onclick=\"modal('$R->cons2',0,0,800,800,'vi');\" style='cursor:pointer'><img src='gifs/standar/Preview.png' border='0'></a>":"").
			($R->cons3?"<a onclick=\"modal('$R->cons3',0,0,800,800,'vi');\" style='cursor:pointer'><img src='gifs/standar/Preview.png' border='0'></a>":"");
}

function exporta_rc_helisa_garantia() // LA EXPORTACION SE INICIA DESDE EL PROGRAMA DE CARTERA E INVOCA ESTA RUTINA.
{
	global $recibos;
	html('EXPORTACION CONTABLE DE RECIBOS DE CAJA DE GARANTIAS A HELISA');
	error_reporting(E_ALL);
	include('Interfase.Helisa.class.php');
	
	$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
	if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
	$DD1=fopen($DESTINO_PLANO1,'w+');
	
	$DESTINO_PLANO2 = 'planos/int2a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para terceros
	if(@is_file($DESTINO_PLANO2)) @unlink($DESTINO_PLANO2);
	$DD2=fopen($DESTINO_PLANO2,'w+');
		
	$DESTINO_PLANO3 = 'planos/int3a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para terceros
	if(@is_file($DESTINO_PLANO3)) @unlink($DESTINO_PLANO3);
	$DD3=fopen($DESTINO_PLANO3,'w+');
	
	$Recibos=explode(';',$recibos);
	include('inc/link.php');
	foreach($Recibos as $indice => $consec)
	{
		if($consec)
		{
			$Partes=explode(',',$consec);
			//$td_contable='RC';
			$consec_contable=$Partes[1];
			$id=$Partes[0];
			$Recibo=mysql_query("select * from recibo_caja where id=$id",$LINK);
			$RC=mysql_fetch_object($Recibo);
			$Oficina=qom("select * from oficina where id=$RC->oficina",$LINK);
			$td_contable=$Oficina->tipo_doc_rc;
			mysql_query("update recibo_caja set td_contable='$td_contable', consec_contable='$consec_contable' where id='$id' ",$LINK);
			echo "<br>$id: Tipo documento: $td_contable  Consecutivo: $consec_contable";
			$Recibo=mysql_query("select * from recibo_caja where id=$id",$LINK);
			$RC=mysql_fetch_object($Recibo);
			$Cliente=qom("select * from cliente where id=$RC->cliente",$LINK);
			$Oficina=qom("select * from oficina where id=$RC->oficina",$LINK);
			$Banco=qom("select * from banco_aoa where id=$RC->banco_aoa",$LINK);
			$Siniestro=qom("select numero from siniestro where id=$RC->siniestro",$LINK);
		//	$Factura=qom("select * from factura where id=$RC->factura",$LINK);
			$Concepto="GARANTIA SN.$Siniestro->numero OF.$Oficina->sigla RC.$RC->consecutivo";
			
			//////////   INTERFASE DE TERCEROS para importar en el módulo de Terceros de Helisa.
			$T=new interfase_tercero();
			$T->Identidad	=$Cliente->identificacion;
			$T->Dv			=$Cliente->dv; 
			$T->Clase		=qo1m("select codigo_helisa from tipo_identificacion where codigo='$Cliente->tipo_id' ",$LINK);  
			$T->Nombres	=$Cliente->nombre.' '.$Cliente->apellido;
			$T->Direccion=$Cliente->direccion;
			$T->Telefono	=$Cliente->celular.' '.$Cliente->telefono_casa.' '.$Cliente->telefono_oficina;
			$T->Ciudad	=l($Cliente->ciudad,5);
			$T->Nombre_ciudad=qo1m("select nombre from ciudad where codigo='$Cliente->ciudad' ",$LINK);
			$T->Email		=$Cliente->email_e;
			$T->Apellido1	=$Cliente->apellido;
			$T->Nombre1	=$Cliente->nombre;
			fwrite($DD2,$T->genera());
			
			//////////   INTERFASE DE CLIENTES para importar en el módulo de Cartera de Helisa
			
			$T=new interfase_cliente();
			$T->Identidad	=$Cliente->identificacion;
			$T->Dv			=$Cliente->dv; 
			$T->Clase		=qo1m("select codigo_helisa from tipo_identificacion where codigo='$Cliente->tipo_id' ",$LINK);  
			$T->Nombres	=$Cliente->nombre.' '.$Cliente->apellido;
			$T->Direccion=$Cliente->direccion;
			$T->Telefono	=$Cliente->celular.' '.$Cliente->telefono_casa.' '.$Cliente->telefono_oficina;
			$T->Ciudad	=l($Cliente->ciudad,5);
			$T->Nombre_ciudad=qo1m("select nombre from ciudad where codigo='$Cliente->ciudad' ",$LINK);
			$T->Email		=$Cliente->email_e;
			$T->Apellido1	=$Cliente->apellido;
			$T->Nombre1	=$Cliente->nombre;
			fwrite($DD3,$T->genera());
			
			// ###################### INICIO DE LA GENERACION DE LA FACTURA ########################
			
			$I = new Interfase_documento();
			$I->Tipo_comprobante = $RC->td_contable;
			$I->Numero_documento = $RC->consec_contable;
			// $I->Nit = '900174552'; // desde Agosto 9 se cambia por la identificación del cliente
			$I->Nit = $Cliente->identificacion;
			$I->Cuenta_contable = $Oficina->cuenta_dtg;
			$I->Fecha_documento = date('d/m/Y',strtotime($RC->fecha));
			$I->Centro_costos = $Oficina->ccostos;
			$I->Descripcion = l(($RC->anulado?'ANULADO: ':'').$Concepto,50);
			$I->Afectacion = 'C';
			$I->Valor=($RC->anulado?0:$RC->valor);
			fwrite( $DD1,$I->genera());
			// CUENTA DE BANCOS  AFECTACION DEBITO
			$I = new Interfase_documento();
			$I->Tipo_comprobante = $RC->td_contable;
			$I->Numero_documento = $RC->consec_contable;
			$I->Nit = '900174552';
			$I->Cuenta_contable = '11051010';
			$I->Fecha_documento = date('d/m/Y',strtotime($RC->fecha));
			$I->Descripcion = l(($RC->anulado?'ANULADO: ':'').$Concepto,50);
			$I->Afectacion = 'D';
			$I->Valor=($RC->anulado?0:$RC->valor);
			fwrite( $DD1,$I->genera());
			// CUENTA DE BANCOS AFECTACION CREDITO
			$I = new Interfase_documento();
			$I->Tipo_comprobante = $RC->td_contable;
			$I->Numero_documento = $RC->consec_contable;
			$I->Nit = '900174552';
			$I->Cuenta_contable = '11051010';
			$I->Fecha_documento = date('d/m/Y',strtotime($RC->fecha));
			$I->Descripcion = l(($RC->anulado?'ANULADO: ':'').$Concepto,50);
			$I->Afectacion = 'C';
			$I->Valor=($RC->anulado?0:$RC->valor);
			fwrite( $DD1,$I->genera());
			// ASIENTO DEL BANCO
			$I = new Interfase_documento();
			$I->Tipo_comprobante = $RC->td_contable;
			$I->Numero_documento = $RC->consec_contable;
			$I->Nit = '900174552';
			$I->Cuenta_contable = $Banco->cuenta_contable;
			$I->Fecha_documento = date('d/m/Y',strtotime($RC->fecha));
			$I->Descripcion = l(($RC->anulado?'ANULADO: ':'').$Concepto,50);
			$I->Afectacion = 'D';
			$I->Valor=($RC->anulado?0:$RC->valor);
			$I->Cheque=$Banco->cuenta;
			fwrite( $DD1,$I->genera());
			
		}
	}
	mysql_close($LINK);
	echo "<br>";
	echo " Fin proceso de detalle. ";
	fclose( $DD1 );fclose( $DD2 );fclose( $DD3 );
	// DESCARGA LOS ARCHIVOS PLANOS
	echo "<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		<iframe name='Oculto2' style='visibility:hidden' height='1' width='1'></iframe>
		<iframe name='Oculto3' style='visibility:hidden' height='1' width='1'></iframe>
		<script language='javascript'>
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=rc.txt','Oculto1');
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO2&Salida=ter.txt','Oculto2');
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO3&Salida=cli.txt','Oculto3');
		</script>
		</body>";
}

function exportacion_notas_contables()
{
	html();
	//  *******************     PRIMERA CONTABILIZACION   ASIENTO 1 Y 2  ********************************
	//  DINEROS RECIBIDOS DE TERCEROS  (DEBITO) 
	//  DEPOSITOS RECIBIDOS HAY UNO POR CADA CIUDAD con el tercero de AOA
	//  28101501 CALI
	//  28101502 IBAGUE
	//  28101503 MEDELLIN
	//  28101504 PEREIRA
	//  28101505 BARRANQUILLA
	//  28101506 BOGOTA
	//  28101507 BUCARAMANGA
	//  CONTRA CARTERA POR COBRAR 13050501 (CREDITO)  con el tercero de la factura
	echo "<script language='javascript'></script>
	<body><h3>EXPORTACION DE NOTAS CONTABLES</h3>
	<form action='zgenerador_contable.php' target='Tablero_exportacion_notas' method='POST' name='forma' id='forma'>
		Fecha Inicial: ".pinta_FC('forma','FI',date('Y-m-d'))." Fecha Final: ".pinta_FC('forma','FF',date('Y-m-d'))." 
		<input type='submit' name='continuar' id='continuar' value=' CONTINUAR ' >
		<input type='hidden' name='Acc' value='exportacion_notas_contables_tablero'>
	</form>
	<iframe name='Tablero_exportacion_notas' id='Tablero_exportacion_notas' style='visibility:visible' width='98%' height='80%'></iframe>
	</body>";
}

function exportacion_notas_contables_tablero()
{
	global $FI,$FF;
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	//$CONCILIADOR=0;
	//if($USUARIO==6 /* Facturacion */) $CONCILIADOR=qo1("select concilia_rc from usuario_facturacion where id=".$_SESSION['Id_alterno']);
	$NTNC=tu('nota_contable','id');
	$NTAU=tu('sin_autor','id');
	$NTSN=tu('siniestro','id');
	$NTFA=tu('factura','id');
	$NTRC=tu('recibo_caja','id');
	//$Bancos=q("select * from banco_aoa order by cuenta_contable");$ABancos=array();
	//while($banco=mysql_fetch_object($Bancos)) {$ABancos[$banco->id]=$banco->nombre.' '.$banco->cuenta;}
	html();
	echo "<script language='javascript'>
	
	function modificar_nc(id)
	{modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTNC&id='+id,0,0,700,900,'nc');}
	
	var ConsecutivoCC=0;
	var Aplano=new Array();
	
	function inicializacc(valor,id)
	{
		if(confirm('Desea inicializar el consecutivo con el valor '+valor+' ?'))
		ConsecutivoCC=Number(valor);
		cambiacc(true,id);
	}
	
	function genera_planonc()
	{
		with(document.genplano)
		{
			Notas.value='';
			for(var i in Aplano) 
			{ 
				if(Aplano[i]!=0)
				{
					Notas.value+=i+','+Aplano[i]+';';
				}
			}
			submit();
		}
	}
	
	function modificar_siniestro(id)
	{modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTSN&id='+id,0,0,700,900,'au');}
		
	function modificar_autorizacion(id)
	{modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTAU&id='+id,0,0,700,900,'au');}
		
	function modificar_factura(id)
	{modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTFA&id='+id,0,0,700,900,'au');}
	
	function imprimir_factura(id)
	{modal('zfacturacion.php?Acc=imprimir_factura&id='+id,0,0,700,900,'au');}
	
	function modificar_recibo_caja(id)
	{modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTRC&id='+id,0,0,700,900,'au');}
		
	function imprimir_recibo_caja(id)
	{modal('zcartera.php?Acc=imprimir_recibo&id='+id,0,0,700,900,'au');}
		
	function cambiacc(valor,id)
	{
		var Campo=document.getElementById('cc'+id);
		if(valor) 
		{
			if(!Number(Campo.value))
			{
				ConsecutivoCC++;
				Campo.value=ConsecutivoCC;
				Aplano[id]=ConsecutivoCC;
			}
			else
			{
				Aplano[id]=Campo.value;
			}
		}
		else 
		{
			Campo.value='0';
			ConsecutivoCC--;
			Aplano[id]=0;
		}
	}
	</script>
	<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
	<h3>EXPORTACION DE NOTAS CONTABLES [ $FI - $FF ]</h3>
	<iframe name='Oculto_exportacionnc' id='Oculto_exportacionnc' height=1 width=1 style='visibility:hidden'></iframe>";
	// obtiene todos los registros de notas contables entre dos fechas
	if($Registros=q("select nc.id,nc.fecha,nc.consecutivo as nc_consec,f.consecutivo as f_consec,t_cliente(f.cliente) as ncli,
			t_sin_autor(nc.autorizacion) as nautor,r.consecutivo as r_consec,nc.valor,r.id as idrc,f.id as idfac,nc.anulado,
			nc.cons_contable,o.sigla,r.siniestro,nc.autorizacion
			FROM nota_contable nc, factura f,recibo_caja r,oficina o
			WHERE nc.factura=f.id and nc.recibo_caja=r.id and r.oficina=o.id and nc.fecha between '$FI' and '$FF' 
			ORDER BY nc.fecha,nc.consecutivo"))
	{
		echo "<table border='0' cellspacing='1' width='100%'><tr>
			<th>Fecha</th>
			<th>Consec.</th>
			<th>Anul.</th>
			<th>Cliente</th>
			<th>Factura</th>
			<th>R.Caja</th>
			<th>Autorización</th>
			<th>Valor</th>
			<th>Opciones</th>
			</tr>";
		while($R =mysql_fetch_object($Registros )) // pinta registro por registro con la opcion de marcarlos para exportar
		{
			echo "<tr>
			<td>$R->fecha</td>
			<td>$R->nc_consec ".($USUARIO==1?"<a class='info' style='cursor:pointer' onclick='modificar_nc($R->id);'><img src='gifs/standar/Pencil.png' border='0' height='10'>N<span>Modificar Nota</span></a> 
				<a class='info' style='cursor:pointer' onclick='modificar_siniestro($R->siniestro);'><img src='gifs/standar/Pencil.png' border='0' height='10'>S<span>Modificar Siniestro</span></a> 
				<a class='info' style='cursor:pointer' onclick='modificar_autorizacion($R->autorizacion);'><img src='gifs/standar/Pencil.png' border='0' height='10'>A<span>Modificar Autorización</span></a> 
				<a class='info' style='cursor:pointer' onclick='modificar_factura($R->idfac);'><img src='gifs/standar/Pencil.png' border='0' height='10'>F<span>Modificar Factura</span></a> 
				<a class='info' style='cursor:pointer' onclick='modificar_recibo_caja($R->idrc);'><img src='gifs/standar/Pencil.png' border='0' height='10'>R<span>Modificar Recibo Caja</span></a> ":"")."</td>
			<td align='center'>".($R->anulado?"<img src='gifs/standar/si.png' style='cursor:pointer' border='0' alt='$R->observaciones' title='$R->observaciones'>":"")."</td>
			<td>$R->ncli</td>
			<td>$R->f_consec <a class='info' style='cursor:pointer' onclick='imprimir_factura($R->idfac);'><img src='gifs/standar/Preview.png' border='0'><span>Ver Factura</span></a></td>
			<td>$R->sigla $R->r_consec <a class='info' style='cursor:pointer' onclick='imprimir_recibo_caja($R->idrc);'><img src='gifs/standar/Preview.png' border='0'><span style='width:100px'>Ver Recibo de Caja</span></a></td>
			<td>$R->nautor</td>
			<td align='right'>".coma_format($R->valor)."</td>
			<td>
				<input type='checkbox' onchange='cambiacc(this.checked,$R->id);'>
					<input class='numero' type='text' name='cc$R->id' id='cc$R->id' size=3 style='background-color:".($R->cons_contable?"ddffdd":"ffffaa")
					."'  value='$R->cons_contable' onchange='inicializacc(this.value,$R->id);'>
			</td>
			</tr>";
		}
		echo "</table><br>
				<form action='zgenerador_contable.php' target='Oculto_exportacionnc' method='POST' name='genplano' id='genplano'>
				<input type='hidden' name='Acc' value='exporta_nota_contable_helisa'>
				<input type='hidden' name='Notas'>
			</form><br>";
		if(inlist($USUARIO,'1,9')) echo "<a style='cursor:pointer' onclick='genera_planonc();'>Exportar Notas Contables a Contabilidad</a>";
			echo "<br><br><br><br>";
	}
	else
	{
		echo "<b style='color:aa0000'>No hay Notas Contables en el rango de fechas dado.</b>";
	}
	echo "</body>";
}

function exporta_nota_contable_helisa() // exporta notas contables
{
	global $Notas;
	html('EXPORTACION DE NOTAS CONTABLES A HELISA');
	error_reporting(E_ALL);
	include('Interfase.Helisa.class.php');
	
	$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
	if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
	$DD1=fopen($DESTINO_PLANO1,'w+');
	echo "<body>$Notas";
	$notas=explode(';',$Notas);
	include('inc/link.php');
	foreach($notas as $indice => $consec)
	{// procesa todas las notas contables marcadas en el formulario anterior
		if($consec)
		{
			$Partes=explode(',',$consec);
			$td_contable='CC';
			$consec_contable=$Partes[1];
			$id=$Partes[0];
			mysql_query("update nota_contable set td_contable='$td_contable', cons_contable='$consec_contable' where id='$id' ",$LINK); // asigna el consecutivo contable a la nota
			echo "<br>$id: Tipo documento: $td_contable  Consecutivo: $consec_contable";
			$Nota=mysql_query("select * from nota_contable where id=$id",$LINK); // obtiene la infrormacion de la nota
			$NC=mysql_fetch_object($Nota);
			$Recibo=qom("select * from recibo_caja where id=$NC->recibo_caja",$LINK); // obtiene el recibo de caja
			$Cliente=qom("select * from cliente where id=$Recibo->cliente",$LINK); // obtiene el cliente
			$Oficina=qom("select * from oficina where id=$Recibo->oficina",$LINK); // obtiene la oficina
			$Siniestro=qom("select numero from siniestro where id=$Recibo->siniestro",$LINK); // obtiene el numero de siniestro
			$Factura=qom("select * from factura where id=$NC->factura",$LINK); // obtiene la factura
			$Concepto="CRUCE SN.$Siniestro->numero RC.OF.$Oficina->sigla $Recibo->consecutivo vs FV.$Factura->consecutivo ";
			// ASIENTO CONTABLE DE LA OFICINA
			$I = new Interfase_documento();
			$I->Tipo_comprobante = $NC->td_contable;
			$I->Numero_documento = $NC->cons_contable;
			$I->Nit = '900174552';
			$I->Cuenta_contable = $Oficina->cuenta_dtg;
			$I->Fecha_documento = date('d/m/Y',strtotime($NC->fecha));
			$I->Centro_costos = $Oficina->ccostos;
			$I->Descripcion = l(($NC->anulado?'ANULADO: ':'').$Concepto,50);
			$I->Afectacion = 'D';
			$I->Valor=($NC->anulado?0:$NC->valor);
			fwrite( $DD1,$I->genera());
			// ASIENTO CONTABLE DE CARTERA
			$I = new Interfase_documento();
			$I->Tipo_comprobante = $NC->td_contable;
			$I->Numero_documento = $NC->cons_contable;
			$I->Nit = $Cliente->identificacion;
			$I->Cuenta_contable = '13050501';
			$I->Fecha_documento = date('d/m/Y',strtotime($NC->fecha));
			$I->Descripcion = l(($NC->anulado?'ANULADO: ':'').$Concepto,50);
			$I->Afectacion = 'C';
			$I->Valor=($NC->anulado?0:$NC->valor);
			fwrite( $DD1,$I->genera());
		}
	}
	mysql_close($LINK);
	echo "<br>";
	echo " Fin proceso de detalle. ";
	fclose( $DD1 );
	echo "<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		<script language='javascript'>
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=nc.txt','Oculto1');
		</script>
		</body>";
}

function exportacion_transferencias_garantia() // formulario para exportacion de transferencias de garantias
{
	html();
	//  *******************     PRIMERA CONTABILIZACION   ASIENTO 1 Y 2  ********************************
	//  DINEROS RECIBIDOS DE TERCEROS  (DEBITO) 
	//  DEPOSITOS RECIBIDOS HAY UNO POR CADA CIUDAD con el tercero de AOA
	//  28101501 CALI
	//  28101502 IBAGUE
	//  28101503 MEDELLIN
	//  28101504 PEREIRA
	//  28101505 BARRANQUILLA
	//  28101506 BOGOTA
	//  28101507 BUCARAMANGA
	//  CONTRA BANCO (cuenta de garantías 03170515431 11100505)   (CREDITO)  con el tercero de AOA
	
	echo "<script language='javascript'></script>
	<body><h3>EXPORTACION DE TRANSFERENCIAS DE GARANTIAS</h3>
	<form action='zgenerador_contable.php' target='Tablero_exportacion_transferencias' method='POST' name='forma' id='forma'>
		Fecha Inicial: ".pinta_FC('forma','FI',date('Y-m-d'))." Fecha Final: ".pinta_FC('forma','FF',date('Y-m-d'))." 
		<input type='submit' name='continuar' id='continuar' value=' CONTINUAR ' >
		<input type='hidden' name='Acc' value='exportacion_transferencias_tablero'>
	</form>
	<iframe name='Tablero_exportacion_transferencias' id='Tablero_exportacion_transferencias' style='visibility:visible' width='98%' height='80%'></iframe>
	</body>";

}

function exportacion_transferencias_tablero()
{
	global $FI,$FF;
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	$NTAU=tu('sin_autor','id');
	$NTSN=tu('siniestro','id');
	html();
	echo "<script language='javascript'>
	
	var ConsecutivoCC=0;
	var Aplano=new Array();
	
	function inicializacc(valor,id)
	{
		if(confirm('Desea inicializar el consecutivo con el valor '+valor+' ?'))
		ConsecutivoCC=Number(valor);
		cambiacc(true,id);
	}
	
	function genera_planonc()
	{
		with(document.genplano)
		{
			Transferencias.value='';
			for(var i in Aplano) 
			{ 
				if(Aplano[i]!=0)
				{
					Transferencias.value+=i+','+Aplano[i]+';';
				}
			}
			submit();
		}
	}
	
	function modificar_siniestro(id)
	{modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTSN&id='+id,0,0,700,900,'au');}
		
	function modificar_autorizacion(id)
	{modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTAU&id='+id,0,0,700,900,'au');}
		
	function cambiacc(valor,id)
	{
		var Campo=document.getElementById('cc'+id);
		if(valor) 
		{
			if(!Number(Campo.value))
			{
				ConsecutivoCC++;
				Campo.value=ConsecutivoCC;
				Aplano[id]=ConsecutivoCC;
			}
			else
			{
				Aplano[id]=Campo.value;
			}
		}
		else 
		{
			Campo.value='0';
			ConsecutivoCC--;
			Aplano[id]=0;
		}
	}
	</script>
	<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
	<h3>EXPORTACION DE TRANSFERENCIAS DE GARANTIAS [ $FI - $FF ]</h3>
	<iframe name='Oculto_exportaciontg' id='Oculto_exportaciontg' height=1 width=1 style='visibility:hidden'></iframe>";
	// obtiene todas las transferencias de garantias entre dos fechas
	if($Registros=q("select a.id,a.fecha_devolucion,a.valor_devolucion,s.numero as numsin,a.devolucion_f, a.cons_contable,
		a.devol_ncuenta,a.obs_transferencia,a.devolucion2_f,a.siniestro,s.ciudad,consignacion_f
			FROM sin_autor a, siniestro s
			WHERE a.siniestro=s.id and a.fecha_devolucion between '$FI' and '$FF' and a.metodo_devol='TRANSFERENCIA'
			ORDER BY a.fecha_devolucion"))
	{
		echo "<table border='0' cellspacing='1' width='100%'><tr>
			<th>Fecha</th>
			<th>Siniestro</th>
			<th>Oficina</th>
			<th>Cliente</th>
			<th>Valor</th>
			<th>Imágenes</th>
			<th>Opciones</th>
			</tr>";
		include('inc/link.php');
		while($R =mysql_fetch_object($Registros )) // pinta las transferencias con la opcion de marcarlas para ser exportadas a la contabilidad
		{
			
			$Consecutivo_rc=qom("select * from recibo_caja where autorizacion=$R->id",$LINK);
			$Oficina=qom("select * from oficina where id='$Consecutivo_rc->oficina' ",$LINK);
			echo "<tr>
			<td>$R->fecha_devolucion</td>
			<td>$R->numsin ".($USUARIO==1?" <a class='info' style='cursor:pointer' onclick='modificar_siniestro($R->siniestro);'><img src='gifs/standar/Pencil.png' border='0' height='10'>S<span>Modificar Siniestro</span></a> 
							<a class='info' style='cursor:pointer' onclick='modificar_autorizacion($R->id);'><img src='gifs/standar/Pencil.png' border='0' height='10'>A<span>Modificar Autorizacion</span></a>":"")."</td>
			<td>".($R->consignacion_f?"<a class='info' style='cursor:pointer' onclick=\"modal('$R->consignacion_f',0,0,500,500,'devol');\"><img src='gifs/standar/Preview.png' border='0'><span>Ver Consignación</span></a>":"").
			" $Oficina->sigla $Consecutivo_rc->consecutivo</td>
			<td>$R->devol_ncuenta</td>
			<td align='right'>".coma_format($R->valor_devolucion)."</td>
			<td>".($R->devolucion_f?"<a class='info' style='cursor:pointer' onclick=\"modal('$R->devolucion_f',0,0,500,500,'devol');\"><img src='gifs/standar/Preview.png' border='0'><span>Ver Transferencia</span></a>":"").
				($R->devolucion2_f?"<a class='info' style='cursor:pointer' onclick=\"modal('$R->devolucion2_f',0,0,500,500,'devol');\"><img src='gifs/standar/Preview.png' border='0'><span>Ver Transferencia</span></a>":"").
			"</td><td>
				<input type='checkbox' onchange='cambiacc(this.checked,$R->id);'>
					<input class='numero' type='text' name='cc$R->id' id='cc$R->id' size=3 style='background-color:".($R->cons_contable?"ddffdd":"ffffaa")
					."'  value='$R->cons_contable' onchange='inicializacc(this.value,$R->id);'>
			</td>
			</tr>";
		}
		mysql_close($LINK);
		echo "</table><br>
				<form action='zgenerador_contable.php' target='Oculto_exportaciontg' method='POST' name='genplano' id='genplano'>
				<input type='hidden' name='Acc' value='exporta_transferencia_helisa'>
				<input type='hidden' name='Transferencias'>
			</form><br>";
		if(inlist($USUARIO,'1,9')) echo "<a style='cursor:pointer' onclick='genera_planonc();'>Exportar Transferencias de Garantias a Contabilidad</a>";
			echo "<br><br><br><br>";
	}
	else
	{
		echo "<b style='color:aa0000'>No hay Transferencias de Garantías en el rango de fechas dado.</b>";
	}
	echo "</body>";
}

function exporta_transferencia_helisa() // exporta las transferencias de pagos de garantias
{
	global $Transferencias;
	html('EXPORTACION DE TRANSFERENCIAS A HELISA');
	error_reporting(E_ALL);
	include('Interfase.Helisa.class.php');
	
	$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
	if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
	$DD1=fopen($DESTINO_PLANO1,'w+');
	echo "<body>$Transferencias";
	$Transf=explode(';',$Transferencias);
	include('inc/link.php');
	foreach($Transf as $indice => $consec)
	{
		if($consec)
		{
			$Partes=explode(',',$consec);
			$td_contable='TG';
			$consec_contable=$Partes[1];
			$id=$Partes[0];
			mysql_query("update sin_autor set td_contable='$td_contable', cons_contable='$consec_contable' where id='$id' ",$LINK);
			echo "<br>$id: Tipo documento: $td_contable  Consecutivo: $consec_contable";
			$Transfer=mysql_query("select * from sin_autor where id=$id",$LINK);
			$Tr=mysql_fetch_object($Transfer);
			$Siniestro=qom("select numero,ciudad from siniestro where id=$Tr->siniestro",$LINK);
			$Oficina=qom("select * from oficina where ciudad='$Siniestro->ciudad' ",$LINK);
			$Cons_rc=qom("select consecutivo,cliente from recibo_caja where autorizacion=$Tr->id",$LINK);
			$Concepto="TRANSF. GARANTIA SN.$Siniestro->numero OF.$Oficina->sigla $Cons_rc->consecutivo";
			$Cliente=qom("select identificacion from cliente where id=$Cons_rc->cliente",$LINK);
		// ASIENTO CONTABLE DE LA CUENTA DE TRANSFERENCIAS
			$I = new Interfase_documento();
			$I->Tipo_comprobante = $Tr->td_contable;
			$I->Numero_documento = $Tr->cons_contable;
			//$I->Nit = '900174552'; // desde Agosto 9 de 2013 se solicita cambiar el tercero ya no es AOA sino el cliente real
			$I->Nit = $Cliente->identificacion;
			$I->Cuenta_contable = $Oficina->cuenta_dtg;
			$I->Fecha_documento = date('d/m/Y',strtotime($Tr->fecha_devolucion));
			$I->Centro_costos = $Oficina->ccostos;
			$I->Descripcion = l($Concepto,50);
			$I->Afectacion = 'D';
			$I->Valor=$Tr->valor_devolucion;
			fwrite( $DD1,$I->genera());
			// ASIENTO CONTABLE DE LA CUENTA DEL BANCO
			$I = new Interfase_documento();
			$I->Tipo_comprobante = $Tr->td_contable;
			$I->Numero_documento = $Tr->cons_contable;
			$I->Nit = '900174552';
			$I->Cuenta_contable = '11100505';
			$I->Fecha_documento = date('d/m/Y',strtotime($Tr->fecha_devolucion));
			$I->Descripcion = l($Concepto,50);
			$I->Afectacion = 'C';
			$I->Valor=$Tr->valor_devolucion;
			$I->Cheque='03170515431';
			fwrite( $DD1,$I->genera());
		}
	}
	mysql_close($LINK);
	echo "<br>";
	echo " Fin proceso de detalle. ";
	fclose( $DD1 );
	// descarga los archivos planos
	echo "<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		<script language='javascript'>
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=tg.txt','Oculto1');
		</script>
		</body>";
}







?>