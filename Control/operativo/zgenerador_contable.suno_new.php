<?php

error_reporting(E_ALL);

/**
 *   EXPORTACIONES CONTABLES DE DOCUMENTOS HACIA SIIGO
 *
 * Para las facturas que se anulan de un mes para otro, debe desarrollarse una funci�n nueva que exporte los asientos con sus valores pero en afectacion inversa
 *
 *
 *LO QUE SIGUE:  de acuerdo a la variable AUTO_RETENCION en clientes, generar los asientos en la exportacion.
 */
include('inc/funciones_.php');
sesion();
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');die();}



function exportar_factura_uno() 
{
	include('inc/gpos.php');
	if(isset($id)) {if($id) $Consecutivo=qo1("select consecutivo from factura where id=$id"); else $Consecutivo=0;}
	else $Consecutivo=0;
	html('EXPORTACION DE DOCUMENTOS');
	
	echo "<form action='zgenerador_contable.suno_new.php' target='_self' method='POST' name='forma' id='forma'>
	   Fecha inicial de consecutivo:
	   <input type='date' name='inicial_fecha_1'>
	   <input type='date' name='final_fecha_1'>
	   <input type='submit'>
	   <input type='hidden' name='Acc' value='exportar_factura_uno'>
	</form>
	";
	global $inicial_fecha_1,$final_fecha_1;
	
	if(isset($inicial_fecha_1))
			{
				$interval_fecha1 = "'".$inicial_fecha_1."' and '".$final_fecha_1."' ";
			}
			else
			{	
				$interval_fecha1 = " CURDATE() - INTERVAL 1 DAY and CURDATE() + INTERVAL 1 DAY";
			}
		
	
	//echo "select consecutivo,concat(consecutivo,' ',fecha_emision) from factura where fecha_emision between $interval_fecha1 order by id  desc limit 200"."<br>";
	//echo "select consecutivo,concat(consecutivo,' ',fecha_emision) from factura where fecha_emision between $interval_fecha1 order by id  desc limit 200"."<br>";
	echo "<body>
		<form action='zgenerador_contable.suno_new.php' target='_self' method='POST' name='forma' id='forma'>
		      
			Consecutivo inicial: ".menu1('consecutivoi',"select consecutivo,concat(consecutivo,' ',fecha_emision) from factura where fecha_emision between $interval_fecha1 order by id  desc limit 500",$Consecutivo,1)."<br><br>
			Consecutivo final: ".menu1('consecutivof',"select consecutivo,concat(consecutivo,' ',fecha_emision) from factura where fecha_emision between $interval_fecha1 order by id  asc limit 500",$Consecutivo,1)."<br><br>
			<input type='submit' name='continuar' id='continuar' value=' CONTINUAR ' >
			<input type='hidden' name='Acc' value='exportar_factura_uno_ok'>
		</form>
	</body>";
											  
}

function exportar_factura_uno_ok()
{
	global $consecutivoi,$consecutivof;
	error_reporting(E_ALL);
    include('Interfase.Sistemauno.class.php');

	$TDC=8;  // id del tipo de documento contable F006 FACTURA DE VENTA
	$GRUPO_CONTABLE=1; // UNICO GRUPO CONTABLE
	$TDC = qo( "select * from tipo_doc_cont where id=$TDC" );
	
	$CFG=qo("select * from cfg_factura where activo=1");
	html('RESULTADO DE LA EXPORTACION');
	echo "<body><h3>Resultado de la exportacion</h3>
	Consecutivo inicial: $consecutivoi  Consecutivo final: $consecutivof ";
	
	$FACT_INICIAL=qo("select * from factura where consecutivo = '$consecutivoi' ");
	$FACT_FINAL=qo("select * from factura where consecutivo = '$consecutivof' ");
	//print_r($FACT_INICIAL);
	
	$sql_fechas = "select * from factura where id between $FACT_INICIAL->id and $FACT_FINAL->id order by id";
	
	
	
	if($Facturas = q($sql_fechas))
	{
		echo "Registros: ".mysql_num_rows($Facturas);
		
		$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
		if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
		$DD1=fopen($DESTINO_PLANO1,'w+');
		
		$DESTINO_PLANO2 = 'planos/int2a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para terceros
		if(@is_file($DESTINO_PLANO2)) @unlink($DESTINO_PLANO2);
		$DD2=fopen($DESTINO_PLANO2,'w+');
		
		$ConsecutivoG=0;
		
		while($F=mysql_fetch_object($Facturas))
		{
			$DVehiculos=false;
			$Proyecto='          ';
			echo "<br>Procesando Consecutivo: $F->consecutivo";
			$Aseguradora=qo("select * from aseguradora where id=$F->aseguradora");
			$Centro_costos=$Aseguradora->ccostos_uno;
			echo " Centro de costos $Centro_costos ";
			$Cliente = qo( "select * from cliente where id=$F->cliente" );

			$Masivo = qo( "select * from factura_masiva where id_factura=$F->id" );
			if($Masivo){
				$siniestros = $Masivo->siniestro;
			}else{
				$siniestros = $F->siniestro;
			}

		    echo "Numero de siniestros: " .$siniestros;
			if($siniestros)
			{
				$Siniestro=qo("select * from siniestro where id=$siniestros");
				if($Siniestro->ubicacion)
				{
					$Ubicacion=qo("select * from ubicacion where id=$Siniestro->ubicacion");
					if($Ubicacion->vehiculo)
					{
						$Vehiculo=qo("select * from vehiculo where id=$Ubicacion->vehiculo");
						$Proyecto='VO'.$Vehiculo->placa;
					}
					if($Ubicacion->flota)
					{
						$Flota=qo("select * from aseguradora where id=$Ubicacion->flota");
						if($Flota->ccostos_uno) $Centro_costos=$Flota->ccostos_uno;
					}
					$Ofic=qo("select * from oficina where id=$Ubicacion->oficina");
				}
			}
			elseif($F->vehiculo)
			{
				$Vehiculo=qo("select * from vehiculo where id=$F->vehiculo");
				$Ofic=qo("select * from oficina where id=$Vehiculo->ultima_ubicacion");
				$Proyecto='VO'.$Vehiculo->placa;
			}
			elseif($F->aseguradora==6 ) // SI ES UNA FACTURA DE AOA y no tiene ni siniestro ni vehiculo asociado. se causa directamente.
			{
				$Ofic=qo("select * from oficina where id=$F->oficina");
				$Proyecto='        ';
			}
			elseif($F->movilidad==1)
			{
				$Ofic=qo("select * from oficina where id=$F->oficina");
				$Proyecto='        ';
				if($F->aseguradora==57)
					$ConsecutivoG=Genera_asientos_movilidad_aoato($F,$ConsecutivoG,$Cliente,$Aseguradora,$Centro_costos,$Ofic,$DD1,$DD2,$CFG,$TDC); // FACTURACION DE MOVILIDAD
				else
					$ConsecutivoG=Genera_asientos_movilidad($F,$ConsecutivoG,$Cliente,$Aseguradora,$Centro_costos,$Ofic,$Proyecto,$DD1,$DD2,$CFG,$TDC); // FACTURACION DE MOVILIDAD
				loop;
			}
			elseif($F->aseguradora==25) // si es AXA
			{
				$Ofic=qo("select * from oficina where id=$F->oficina");
				$Proyecto='        ';
			}
			else  // DISTRIBUCION DE VEHICULOS EN LA FLOTA
			{
				echo " Distribucion de flota ".$F->aseguradora;
				$Consulta='';
				if(inlist($F->aseguradora,'1,8,9')) $Consulta="select v.*,o.centro_operacion from vehiculo v,oficina o where o.id=v.ultima_ubicacion and v.flota in (1,8,9) and v.inactivo_desde='0000-00-00'  order by v.placa";
				if(inlist($F->aseguradora,'2,5')) $Consulta="select v.*,o.centro_operacion from vehiculo v,oficina o where o.id=v.ultima_ubicacion and v.flota in (2,5)  and v.inactivo_desde='0000-00-00'  order by v.placa";
				if(inlist($F->aseguradora,'4,10')) $Consulta="select v.*,o.centro_operacion from vehiculo v,oficina o where o.id=v.ultima_ubicacion and v.flota in (4,10)  and v.inactivo_desde='0000-00-00'  order by v.placa";
				if(inlist($F->aseguradora,'3,7')) $Consulta="select v.*,o.centro_operacion from vehiculo v,oficina o where o.id=v.ultima_ubicacion and v.flota in (3,7)  and v.inactivo_desde='0000-00-00'  order by v.placa";
				if(inlist($F->aseguradora,'55')) $Consulta="select v.*,o.centro_operacion from vehiculo v,oficina o where o.id=v.ultima_ubicacion and v.flota in (55)  and v.inactivo_desde='0000-00-00'  order by v.placa";
				if(inlist($F->aseguradora,'91')) $Consulta="select v.*,o.centro_operacion from vehiculo v,oficina o where o.id=v.ultima_ubicacion and v.flota in (91)  and v.inactivo_desde='0000-00-00'  order by v.placa";
				if(inlist($F->aseguradora,'93')) $Consulta="select v.*,o.centro_operacion from vehiculo v,oficina o where o.id=v.ultima_ubicacion and v.flota in (93)  and v.inactivo_desde='0000-00-00'  order by v.placa";
				if(inlist($F->aseguradora,'212')) $Consulta="select v.*,o.centro_operacion from vehiculo v,oficina o where o.id=v.ultima_ubicacion and v.flota in (212)  and v.inactivo_desde='0000-00-00'  order by v.placa";
				if(!$Consulta) $Consulta="select v.*,o.centro_operacion as co from vehiculo v, oficina o where o.id=v.ultima_ubicacion and v.flota=$F->aseguradora and v.inactivo_desde='0000-00-00' order by v.placa";
				$DVehiculos=q($Consulta);
				//echo " $Consulta cantidad: ".mysql_num_rows($DVehiculos);
				$Ofic=qo("select * from oficina where id=1");
			}
			
			if($F->movilidad==0)
			{
				//////////   INTERFASE DE TERCEROS para importar en el m�dulo de Terceros de Helisa.
				
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
				$T->Centro_operacion=$Ofic->centro_operacion;
				$T->Zona='01    ';
				$T->Indicador_liquidacion_impuesto='1';
				$T->Codigo_rete_otro='01';
				$T->Codigo_condicion_pago='0 ';
				$T->Fecha_creacion=date('Ymd');
				fwrite($DD2,$T->genera());
				
				// ###################### INICIO DE LA GENERACION DE LA FACTURA ########################
				
				$Detalle = q( "select *,t_concepto_fac(concepto) as nconcepto from facturad where factura=$F->id " );
				$Fecha_doc=date('Ymd',strtotime($F->fecha_emision));
				$Fecha_vencimiento=date('Ymd',strtotime($F->fecha_vencimiento));
				$Sumatoria=0;
				$Iva=0;
				$Total_retenciones=0;
				$Valor_documento=$F->total;
					
				if($Cliente->auto_retencion==1 && !$F->anulada)
				{
					
					
					///****   AUTO RETENCION DE IVA
					
					$ConsecutivoG++;
					$Valor_retencion1=round($F->subtotal*$Aseguradora->pvrete_iva/100,0);
					$Valor_retencion2=round($F->subtotal*$Aseguradora->pvrete_fuente/100,0);
					$Valor_retencion3=round($F->subtotal*$Aseguradora->pvrete_ica/1000,0);
					$Valor_documento=$F->total+$Valor_retencion1+$Valor_retencion2+$Valor_retencion3;
					
					$Descripcion="Retecion Iva $F->consecutivo $Cliente->nombre $Cliente->apellido";
					$Valor=$Valor_retencion1;
					///
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					
					if(strtotime($F->emision) > strtotime("2018-12-31"))
					{
						$I->Tipo_doc_cruce = $TDC->codigo_suno;					
					}
					else{
						$I->Tipo_doc_cruce = "FV"; 
					}
					
					
					
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$Aseguradora->vrete_iva;
					// $I->Centro_operacion_mov=$Ofic->centro_operacion; // Noviembre 24 se solicito pasar a 001 todos los centros de operaciones exceptuando la 41
					$I->Centro_operacion_mov='001';
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion,40);
					$I->Detalle2= substr($Descripcion,40);
					$I->DC='D';
					$I->Valor=$Valor;
					$I->Tasa_base_iva=($F->iva?16:0);
					$I->Base_iva_retencion=$F->subtotal;
					$I->Base_iva_ret_libro2=$F->subtotal;
					$I->Base_iva_ret_libro3=$F->subtotal;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Valor;
					$I->Valor_transaccion_libro3=$Valor;
					$I->Proyecto=$Proyecto;
					fwrite( $DD1,$I->genera());
					echo "<br>Tasa Base iva:|$I->Tasa_base_iva| l($Descripcion,40)|".$Proyecto;

					///****   AUTO RETENCION DE LA FUENTE
					$ConsecutivoG++;
					//Previamente estaba escrito retecion
					$Descripcion="Retencion en la Fuente $F->consecutivo $Cliente->nombre $Cliente->apellido";
					$Valor=$Valor_retencion2;
					///
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$Aseguradora->vrete_fuente;
					// $I->Centro_operacion_mov=$Ofic->centro_operacion; // Noviembre 24 se solicito pasar a 001 todos los centros de operaciones exceptuando la 41
					$I->Centro_operacion_mov='001'; 
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion,40);
					$I->Detalle2= substr($Descripcion,40);
					$I->DC='D';
					$I->Valor=$Valor;
					$I->Tasa_base_iva=($F->iva?16:0);
					$I->Base_iva_retencion=$F->subtotal;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Valor;
					$I->Valor_transaccion_libro3=$Valor;
					$I->Proyecto=$Proyecto;
					fwrite( $DD1,$I->genera());
					
					///****   AUTO RETENCION DE ICA
					$ConsecutivoG++;
					
					$Descripcion="Retecion Ica $F->consecutivo $Cliente->nombre $Cliente->apellido";
					$Valor=$Valor_retencion3;
					///
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$Aseguradora->vrete_ica;
					// $I->Centro_operacion_mov=$Ofic->centro_operacion;// Noviembre 24 se solicito pasar a 001 todos los centros de operaciones exceptuando la 41
					$I->Centro_operacion_mov='001';
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion,40);
					$I->Detalle2= substr($Descripcion,40);
					$I->DC='D';
					$I->Valor=$Valor;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Valor;
					$I->Valor_transaccion_libro3=$Valor;
					$I->Proyecto=$Proyecto;
					fwrite( $DD1,$I->genera());
					
					/// TOTALIZO LAS RETENCIONES PARA CALCULAR EL VALOR NETO DEL INGRESO
					$Total_retenciones=$Valor_retencion1+$Valor_retencion2+$Valor_retencion3;
				}
				
				///  CUENTA DE CARTERA.
				$ConsecutivoG++;
				$Valor=($F->anulada?1:$F->total-$Total_retenciones);
				if($F->anulada==1) $Descripcion_factura="FACTURA $F->consecutivo ANULADA";
				else $Descripcion_factura="FAX $F->consecutivo $Cliente->nombre $Cliente->apellido ";
				///
				echo '<br>Punto de partida----------------------------------------------------'.$Descripcion_factura.'</br>';
				$I = new Interfase_documento();
				$I->Consecutivo_grabacion=$ConsecutivoG;
				$I->Centro_operacion='001';
				$I->Tipo_documento=$TDC->codigo_suno;
				$I->Tipo_doc_cruce=$TDC->codigo_suno;
				$I->Numero_documento=$F->consecutivo;
				$I->Numero_doc_cruce=$F->consecutivo;
				$I->Fecha_documento=$Fecha_doc;
				$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
				$I->Tercero=$Cliente->identificacion;
				$I->Indicador_anulacion=($F->anulada==1?'X':' ');
				$I->Valor_documento=($F->anulada?1:$Valor_documento);
				$I->Cuenta_contable=$CFG->cuenta_activo_venta;
				// $I->Centro_operacion_mov=$Ofic->centro_operacion;// Noviembre 24 se solicito pasar a 001 todos los centros de operaciones exceptuando la 41
				$I->Centro_operacion_mov='001';
				$I->Tercero_mov=$Cliente->identificacion;
				$I->Detalle1= l($Descripcion_factura,40);
				$I->Detalle2= substr($Descripcion_factura,40);
				$I->DC='D';
				$I->Valor=$Valor;
				$I->Centro_costos=$Centro_costos;
				$I->Detalle1_doc=l($Descripcion_factura,60);
				$I->Indicador_contab_libro2='2';
				$I->Valor_transaccion_libro2=$Valor;
				$I->Valor_transaccion_libro3=$Valor;
				$I->Proyecto=$Proyecto;
				fwrite( $DD1,$I->genera());
				// se cambia lugar para desglosar factura detalle
				/* if($F->iva && !$F->anulada)
				{
					echo 'PASO 1';
					/// ASIENTO DEL PASIVO DEL IVA
					$ConsecutivoG++;
					$Valor=($F->anulada?1:$F->iva);
					///
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Indicador_anulacion=($F->anulada==1?'1':' ');
					$I->Valor_documento=($F->anulada?1:$Valor_documento);
					$I->Cuenta_contable=$CFG->cuenta_iva_venta;
					// $I->Centro_operacion_mov=$Ofic->centro_operacion;// Noviembre 24 se solicito pasar a 001 todos los centros de operaciones exceptuando la 41
					$I->Centro_operacion_mov='XXX';
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='C';
					$I->Valor=$Valor;//$Valor
					$I->Tasa_base_iva=($F->iva?19:0);       // se cambio del 16 al 19 ----james----
					$I->Base_iva_retencion=($F->anulada?1:$F->subtotal);
					$I->Base_iva_ret_libro2=($F->anulada?1:$F->subtotal);
					$I->Base_iva_ret_libro3=($F->anulada?1:$F->subtotal);
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Valor;
					$I->Valor_transaccion_libro3=$Valor;
					$I->Proyecto=$Proyecto;
					fwrite( $DD1,$I->genera());
					echo "<br>Tasa Base ivaxx:|$I->Tasa_base_iva| ";  // saca iva 
				} */
				echo " Procesando detalle.. ";
				$DIFERIDO=false;
				if($F->anulada)
				{
					$ConsecutivoG++;
					$Valor=1;
					$Descripcion_factura="FACTURA $F->consecutivo ANULADA";
					///
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Indicador_anulacion=($F->anulada==1?'X':' ');
					$I->Valor_documento=($F->anulada?1:$Valor_documento);
					$I->Cuenta_contable='41551501'; // Noviembre 24 se solicito pasar a 001 todos los centros de operaciones exceptuando la 41
					$I->Centro_operacion_mov=$Ofic->centro_operacion;
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='C';
					$I->Valor=$Valor;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Valor;
					$I->Valor_transaccion_libro3=$Valor;
					$I->Proyecto=$Proyecto;
					fwrite( $DD1,$I->genera());
				}
				elseif($Detalle)
				{
					while ( $D = mysql_fetch_object( $Detalle ) )
					{
						if($D->concepto==88 || $D->concepto==85){
							$docu = $Aseguradora->nit_mandato;
							$coiva = 0;
							$valorbase= 0;
							
						}else{
							$docu = $Cliente->identificacion;
							$coiva = 19;
							$valorbase = $D->base;
						}
						if($F->iva && !$F->anulada)
							{
								$CODIGO=qo("select * from concepto_fac where id=$D->concepto ");
								
								$Proyecto='        ';
								echo 'PASO XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
								/// ASIENTO DEL PASIVO DEL IVA
								$ConsecutivoG++;
								$Valor=($F->anulada?1:$F->iva);
								///
								$I = new Interfase_documento();
								$I->Consecutivo_grabacion=$ConsecutivoG;
								$I->Centro_operacion='001';
								$I->Tipo_documento=$TDC->codigo_suno;
								$I->Tipo_doc_cruce=$TDC->codigo_suno;
								$I->Numero_documento=$F->consecutivo;
								$I->Numero_doc_cruce=$F->consecutivo;
								$I->Fecha_documento=$Fecha_doc;
								$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
								$I->Tercero=$Cliente->identificacion; 
								$I->Indicador_anulacion=($F->anulada==1?'1':' ');
								$I->Valor_documento=($F->anulada?1:$Valor_documento);
								$I->Cuenta_contable=$CODIGO->cuenta_iva;   //   james cambio el numero de cuenta $CFG->cuenta_iva_venta
								// $I->Centro_operacion_mov=$Ofic->centro_operacion;// Noviembre 24 se solicito pasar a 001 todos los centros de operaciones exceptuando la 41
								$I->Centro_operacion_mov='001';
								$I->Tercero_mov=$docu;  // $Cliente->identificacion   james
								$I->Detalle1= l($Descripcion_factura,40);
								$I->Detalle2= substr($Descripcion_factura,40);
								$I->DC='C';
								$I->Valor=$D->iva;//$Valor
								$I->Tasa_base_iva=$coiva;       // se cambio del 16 al 19 ----james----  ($F->iva?19:0) se cambio esta linea
								$I->Base_iva_retencion=($F->anulada?1:$valorbase);   // se cambio $F->subtotal  james
								$I->Base_iva_ret_libro2=($F->anulada?1:$valorbase);  // se cambio $F->subtotal  james
								$I->Base_iva_ret_libro3=($F->anulada?1:$valorbase);   // se cambio $F->subtotal  james
								$I->Centro_costos=$Centro_costos;
								$I->Detalle1_doc=l($Descripcion_factura,60);
								$I->Indicador_contab_libro2='2';
								$I->Valor_transaccion_libro2=$D->iva;   // se cambio $Valor james
								$I->Valor_transaccion_libro3=$D->iva;
								$I->Proyecto=$Proyecto;
								fwrite( $DD1,$I->genera());
								echo "<br>Tasa Base ivaxx:|$I->Tasa_base_iva| ".$Proyecto;  // saca iva 
							}
						// ASIENTOS PARA EL DETALLE
						echo "<h3><b>Detalle de facturad ".$D->id."</b></h3>";
						$Valor_item=round($D->cantidad*$D->unitario,0);
						if ( $Causacion = q( "select * from inv_grupocont_cau where grupo_contable=$GRUPO_CONTABLE and td_contable=$TDC->id and tipo_afectacion='C' " ) )
						{
							while ( $Cau = mysql_fetch_object( $Causacion ) )
							{
								if($Cau->cuenta) // SI LA CUENTA ESTA DEFINIDA EN EL GRUPO CONTABLE
								{
									$Cuenta_contable=$Cau->cuenta;
									$Descripcion='VENTA';
								}
								else // LA CUENTA DEPENDE DEL CONCEPTO DE FACTURACION
								{
									$Concepto_facturacion=qo("Select * from concepto_fac where id=$D->concepto");
									$Cuenta_contable=$Concepto_facturacion->cuenta_ingresos_uno;
									if($Cuenta_contable)
									{
										if(l($Cuenta_contable,2)=='28') $DIFERIDO=true; // VERIFICACION SI LA CUENTA ES UN DIFERIDO O INGRESO RECIBIDO PARA TERCEROS
										$Descripcion=$Concepto_facturacion->nombre;
										echo "<br>$Cuenta_contable";
										if(strpos($Cuenta_contable,'X'))
										{
											echo "<br>Cambiando de cuenta";
											eval("\$Cuenta_contable=\$Ofic->$Concepto_facturacion->campo_ingresos;");
											echo "<br>$Cuenta_contable";
										}
									}
									else
									{
										$Cuenta_contable=$Aseguradora->cuenta_ingreso;
										$Descripcion=$Concepto_facturacion->nombre;
									}
								}
								if($DVehiculos && !$F->anulada) // DISTRIBUCION ENTRE LOS VEHICULOS DE LA FLOTA 
								{
									$Numero_vehiculos=mysql_num_rows($DVehiculos);
									echo "<br>Distribucion en flota $Aseguradora->razon_social $Numero_vehiculos Vehiculos</br> ";
									$Cuota=round($Valor_item/$Numero_vehiculos,0);
									$Acumulado=0;$Contador=0;
									while($Dv=mysql_fetch_object($DVehiculos))
									{
										$Contador++;
										$ConsecutivoG++;
										if($Contador==$Numero_vehiculos)
											$Valor=$Valor_item-$Acumulado;
										else
											$Valor=$Cuota;
										$Proyecto='VO'.$Dv->placa;
										echo "<li>Centro_operacion_mov: $Dv->co <br>";   // se quito $Dv->centro_operacion   james
										$Acumulado+=$Cuota;
										$Descripcion_factura="FAC Proyecto $Proyecto $F->consecutivo $Cliente->nombre $Cliente->apellido ";
										///

										$I = new Interfase_documento();
										$I->Consecutivo_grabacion=$ConsecutivoG;
										$I->Centro_operacion='001';
										$I->Tipo_documento=$TDC->codigo_suno;
										$I->Tipo_doc_cruce=$TDC->codigo_suno;
										$I->Numero_documento=$F->consecutivo;
										$I->Numero_doc_cruce=$F->consecutivo;
										$I->Fecha_documento=$Fecha_doc;
										$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
										$I->Tercero=$Cliente->identificacion;
										$I->Valor_documento=$Valor_documento;
										$I->Cuenta_contable=$Cuenta_contable;
										//$I->Centro_operacion_mov=$Ofic->centro_operacion;
										$I->Centro_operacion_mov=$Dv->co?$Dv->co:'***';  // $Dv->centro_operacion
										$I->Tercero_mov=$docu; // $Cliente->identificacion
										$I->Detalle1= l($Descripcion_factura,40);
										$I->Detalle2= substr($Descripcion_factura,40);
										$I->DC='C';
										$I->Valor=$Valor;
										$I->Centro_costos=$Centro_costos;
										$I->Detalle1_doc=l($Descripcion_factura,60);
										$I->Indicador_contab_libro2='2';
										$I->Valor_transaccion_libro2=$Valor;
										$I->Valor_transaccion_libro3=$Valor;
										$I->Proyecto=$Proyecto;
										
										fwrite( $DD1,$I->genera());
									}
									mysql_data_seek($DVehiculos,0);
								}
								else
								{
									echo "<br>Sin distribucion. ";
									$ConsecutivoG++;
									$Valor=($F->anulada?1:$Valor_item);
									if($F->anulada==1) $Descripcion_factura="FACTURA $F->consecutivo ANULADA";
									else $Descripcion_factura="FAC $F->consecutivo $Cliente->nombre $Cliente->apellido ";
									///
									$I = new Interfase_documento();
									$I->Consecutivo_grabacion=$ConsecutivoG;
									$I->Centro_operacion='001';
									$I->Tipo_documento=$TDC->codigo_suno;
									$I->Tipo_doc_cruce=$TDC->codigo_suno;
									$I->Numero_documento=$F->consecutivo;
									$I->Numero_doc_cruce=$F->consecutivo;
									$I->Fecha_documento=$Fecha_doc;
									$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
									$I->Tercero=$Cliente->identificacion;
									$I->Indicador_anulacion=($F->anulada==1?'1':' ');
									$I->Valor_documento=($F->anulada?1:$Valor_documento);
									$I->Cuenta_contable=$Cuenta_contable;
									$I->Centro_operacion_mov=($D->centro_operacion?$D->centro_operacion:$Ofic->centro_operacion);
									$I->Tercero_mov=$Cliente->identificacion;
									$I->Detalle1= l($Descripcion_factura,40);
									$I->Detalle2= substr($Descripcion_factura,40);
									$I->DC='C';
									$I->Valor=$Valor;
									$I->Centro_costos=$Centro_costos;
									$I->Detalle1_doc=l($Descripcion_factura,60);
									$I->Indicador_contab_libro2='2';
									$I->Valor_transaccion_libro2=$Valor;
									$I->Valor_transaccion_libro3=$Valor;
									$I->Proyecto=$Proyecto;
									
									fwrite( $DD1,$I->genera());
								}
							}
						}
					}
				}
				echo " Fin proceso de detalle. ";
				if(!$DIFERIDO && !$F->anulada)
				{
					///****   AUTO RETENCION CREE
					$Valor_retecree=round($F->subtotal*$CFG->auto_rete_cree/1000,0);
					// partida DEBITO
					$ConsecutivoG++;
					$Valor=($F->anulada?1:$Valor_retecree);
					if($F->anulada==1) $Descripcion_factura="FACTURA $F->consecutivo ANULADA";
					else $Descripcion_factura="Auto Rete Cree FAC $F->consecutivo $Cliente->nombre $Cliente->apellido ";
					///
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Indicador_anulacion=($F->anulada==1?'1':' ');
					$I->Valor_documento=($F->anulada?1:$F->total);
					$I->Cuenta_contable=$CFG->arc_debito;
					// $I->Centro_operacion_mov=$Ofic->centro_operacion;// Noviembre 24 se solicito pasar a 001 todos los centros de operaciones exceptuando la 41
					$I->Centro_operacion_mov='001';
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='D';
					$I->Valor=$Valor;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Valor;
					$I->Valor_transaccion_libro3=$Valor;
					$I->Proyecto=$Proyecto;
					fwrite( $DD1,$I->genera());
				
					// partida CREDITO
					$ConsecutivoG++;
					$Valor=($F->anulada?1:$Valor_retecree);
					if($F->anulada==1) $Descripcion_factura="FACTURA $F->consecutivo ANULADA";
					else $Descripcion_factura="Auto Rete Cree FAC $F->consecutivo $Cliente->nombre $Cliente->apellido ";
					///
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Indicador_anulacion=($F->anulada==1?'1':' ');
					$I->Valor_documento=($F->anulada?1:$F->total);
					$I->Cuenta_contable=$CFG->arc_credito;
					// $I->Centro_operacion_mov=$Ofic->centro_operacion;// Noviembre 24 se solicito pasar a 001 todos los centros de operaciones exceptuando la 41
					$I->Centro_operacion_mov='001';
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='C';
					$I->Valor=$Valor;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Valor;
					$I->Valor_transaccion_libro3=$Valor;
					$I->Proyecto=$Proyecto;
		              
					 fwrite( $DD1,$I->genera());
					
					
					
				}
			}
		}
		
		fclose( $DD1 );fclose( $DD2 );
		// descarga de los archivos planos
		
		
		echo "<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		<iframe name='Oculto2' style='visibility:hidden' height='1' width='1'></iframe>
		<script language='javascript'>
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=FIBATCH.TXT','Oculto1');
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO2&Salida=TERCEROS.TXT','Oculto2');
		</script>
		</body>";
	}	
}

function Genera_asientos_movilidad($F,$ConsecutivoG,$Cliente,$Aseguradora,$Centro_costos,$Ofic,$Proyecto,$DD1,$DD2,$CFG,$TDC)
{
	
	$Nit_AOA='900174552';
	$Fecha_doc=date('Ymd',strtotime($F->fecha_emision));
	$Fecha_vencimiento=date('Ymd',strtotime($F->fecha_vencimiento));
	
	
	
	/**
	28200501 C 45000 NETO  INGRESO AL CONDUCTOR ELEGIDO
	24080501 C  7200 IVA      IVA GENERADO CONDUCTOR ELEGIDO
	13050501 D 50400 TOTAL CUENTA POR COBRAR CONDUCTOR ELEGIDO
	13551503 D 4% DEL NETO O SEA 1800 
	
	*/
	if($F->anulada)
	{
		$ConsecutivoG++;
		$Descripcion_factura='ANULADA SRV. C. ELEGIDO Ingreso AOA';
		$I = new Interfase_documento();
		$I->Consecutivo_grabacion=$ConsecutivoG;
		$I->Centro_operacion='001';
		$I->Tipo_documento=$TDC->codigo_suno;
		$I->Tipo_doc_cruce=$TDC->codigo_suno;
		$I->Numero_documento=$F->consecutivo;
		$I->Numero_doc_cruce=$F->consecutivo;
		$I->Fecha_documento=$Fecha_doc;
		$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
		$I->Tercero=$Cliente->identificacion;
		$I->Indicador_anulacion='X';
		$I->Valor_documento=0;
		$I->Cuenta_contable=$CFG->mov_ingreso_aoa;
		$I->Centro_operacion_mov='001';
		$I->Tercero_mov=$Cliente->identificacion;
		$I->Detalle1= l($Descripcion_factura,40);
		$I->Detalle2= substr($Descripcion_factura,40);
		$I->DC='C';
		$I->Valor=0;
		$I->Centro_costos=$Centro_costos;
		$I->Detalle1_doc=l($Descripcion_factura,60);
		$I->Indicador_contab_libro2='2';
		$I->Valor_transaccion_libro2=0;
		$I->Valor_transaccion_libro3=0;
		fwrite( $DD1,$I->genera());
	}
	else
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
		$T->Centro_operacion=$Ofic->centro_operacion;
		$T->Zona='01    ';
		$T->Indicador_liquidacion_impuesto='1';
		$T->Codigo_rete_otro='01';
		$T->Codigo_condicion_pago='0 ';
		$T->Fecha_creacion=date('Ymd');
		fwrite($DD2,$T->genera());
				
		$Detalle=q("select * from facturad where factura=$F->id");
		while($DF=mysql_fetch_object($Detalle))
		{
			$Base=round($DF->cantidad*$DF->unitario,0);
			echo "<hr>Base: $Base ";
			if($DF->tercero_uno)  // si este campo viene lleno significa que el comprobante se reduce unicamente a 3 asientos:  neto, iva y total. porque es de TRANSORIENTE
			{
				
				$SUBTOTAL=$Base;
				$IVA=$DF->iva;
				$RETENCION=round($SUBTOTAL*4/100,0);
				$TOTAL=$DF->total-$RETENCION;
				$Cree_retenido=round($SUBTOTAL*$CFG->mov_porc_cree1/100,0);
				$Valor_documento=$F->total+$Cree_retenido;
				// PRIMER ASIENTO Ingreso al conductor al credito
				if($SUBTOTAL>0)
				{
					$ConsecutivoG++;
					$Descripcion_factura='SERVICIO C.ELEGIDO TRANSORIENTE - INGRESO '.$DF->descripcion;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_ing_cond_trans;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='C';
					$I->Valor=$SUBTOTAL;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$SUBTOTAL;
					$I->Valor_transaccion_libro3=$SUBTOTAL;
					fwrite( $DD1,$I->genera());
				}
				// SEGUNDO ASIENTO: IVA AOA 
				if($IVA>0)
				{
					$ConsecutivoG++;
					$Descripcion_factura='SERVICIO C.ELEGIDO TRANSORIENTE - IVA '.$DF->descripcion;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_iva_gen_aoa;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='C';
					$I->Valor=$IVA;
					$I->Tasa_base_iva=$CFG->mov_porc_iva_aoa;
					$I->Base_iva_retencion=$SUBTOTAL_COND;
					//$I->Base_iva_ret_libro2=$SUBTOTAL_COND;
					//$I->Base_iva_ret_libro3=$SUBTOTAL_COND;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$IVA;
					$I->Valor_transaccion_libro3=$IVA;
					fwrite( $DD1,$I->genera());
				}
				// TERCER ASIENTO: RETENCION DEL 4% DEL NETO 
				if($RETENCION)
				{
					$ConsecutivoG++;
					$Descripcion_factura='SERVICIO C.ELEGIDO TRANSORIENTE - RETENCION '.$DF->descripcion;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_ret_serv_aoa;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='D';
					$I->Valor=$RETENCION;
					$I->Tasa_base_iva=$CFG->mov_porc_ret_s_aoa;
					$I->Base_iva_retencion=$SUBTOTAL;
					//$I->Base_iva_ret_libro2=$SUBTOTAL;
					//$I->Base_iva_ret_libro3=$SUBTOTAL;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$RETENCION;
					$I->Valor_transaccion_libro3=$RETENCION;
					fwrite( $DD1,$I->genera());
				}
				// CUARTO ASIENTO CUENTA POR COBRAR 
				if($TOTAL>0)
				{
					$ConsecutivoG++;
					$Descripcion_factura='SERVICIO C.ELEGIDO TRANSORIENTE - CUENTA POR COBRAR '.$DF->descripcion ;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_ccobrar_aoa;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='D';
					$I->Valor=$TOTAL;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$TOTAL;
					$I->Valor_transaccion_libro3=$TOTAL;
					fwrite( $DD1,$I->genera());
				}
				// QUINTO ASIENTO: AUTO RETE CREE DEBITO 
				if($Cree_retenido>0)
				{
					$ConsecutivoG++;
					$Descripcion_factura='SRV. C. ELEGIDO Auto Rete CREE '.$DF->descripcion;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_auto_cree1;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='D';
					$I->Valor=$Cree_retenido;
					$I->Tasa_base_iva=$CFG->mov_porc_cree1;
					$I->Base_iva_retencion=$SUBTOTAL;
					//$I->Base_iva_ret_libro2=$SUBTOTAL;
					//$I->Base_iva_ret_libro3=$SUBTOTAL;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Cree_retenido;
					$I->Valor_transaccion_libro3=$Cree_retenido;
					fwrite( $DD1,$I->genera());
				}
				// SEXTO ASIENTO: AUTO RETE CREE CREDITO 
				if($Cree_retenido>0)
				{
					$ConsecutivoG++;
					$Descripcion_factura='SRV. C. ELEGIDO Auto Rete CREE '.$DF->descripcion;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_auto_cree2;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='C';
					$I->Valor=$Cree_retenido;
					$I->Tasa_base_iva=$CFG->mov_porc_cree1;
					$I->Base_iva_retencion=$SUBTOTAL;
					//$I->Base_iva_ret_libro2=$SUBTOTAL;
					//$I->Base_iva_ret_libro3=$SUBTOTAL;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Cree_retenido;
					$I->Valor_transaccion_libro3=$Cree_retenido;
					fwrite( $DD1,$I->genera());
				}	
			}
			else
			{
				$SUBTOTAL_AOA=$Base-$DF->costo;
				$SUBTOTAL_COND=$DF->costo;
				if($SUBTOTAL_AOA<0) // EN CASO DE QUE OCURRA PERDIDA PARA AOA
				{
					$SUBTOTAL_AOA=(-$SUBTOTAL_AOA);   // se convierte a un valor positivo el supuesto ingreso de AOA que en realidad es una perdida
					$Iva_generado_AOA=round($SUBTOTAL_AOA*$CFG->mov_porc_iva_aoa/100,0);
					$Iva_generado_COND=round($SUBTOTAL_COND*$CFG->mov_porc_iva_cond/100,0);
					echo "AOA = $SUBTOTAL_AOA Iva: $Iva_generado_AOA <br>Conductor: $SUBTOTAL_COND Iva: $Iva_generado_COND <br>";
					if($Cliente->p_rete_fuente>0.00)
					{
						$Retencion_servicios_AOA=round($SUBTOTAL_AOA*$Cliente->p_rete_fuente/100,0);
						$Retencion_servicios_COND=round($SUBTOTAL_COND*$Cliente->p_rete_fuente/100,0);
					}
					else $Retencion_servicios_AOA=$Retencion_servicios_COND=0;
					echo "Retencion Servicios AOA: $Retencion_servicios_AOA  Retencion Servicios Conductor: $Retencion_servicios_COND <br>";
					if($Cliente->p_rete_iva>0.00)
					{
						$Iva_retenido_AOA=round($SUBTOTAL_AOA*$Cliente->p_rete_iva/100,0);
						$Iva_retenido_COND=round($SUBTOTAL_COND*$Cliente->p_rete_iva/100,0);
					}
					else $Iva_retenido_AOA=$Iva_retenido_COND=0;
					echo "Rete Iva AOA: $Iva_retenido_AOA Rete Iva Conductor: $Iva_retenido_COND <br>";
					if($Cliente->p_rete_ica>0.00)
					{
						$Ica_retenido_AOA=round($SUBTOTAL_AOA*$Cliente->p_rete_ica/100,0);
						$Ica_retenido_COND=round($SUBTOTAL_COND*$Cliente->p_rete_ica/100,0);
					}
					else $Ica_retenido_AOA=$Ica_retenido_COND=0;
					echo "Rete Ica AOA: $Ica_retenido_AOA Rete Ica Conductor: $Ica_retenido_COND <br>";
					$Cree_retenido=round($SUBTOTAL_AOA*$CFG->mov_porc_cree1/100,0);
					$Valor_documento=$F->total+$Cree_retenido;
					echo "Valor Documento: $Valor_documento <br>";
					$Cuenta_x_cobrar_AOA=$SUBTOTAL_AOA+$Iva_generado_AOA-$Retencion_servicios_AOA-$Iva_retenido_AOA-$Ica_retenido_AOA;
					$Cuenta_x_cobrar_COND=$SUBTOTAL_COND+$Iva_generado_COND-$Retencion_servicios_COND-$Iva_retenido_COND-$Ica_retenido_COND;
					echo "Cuenta AOA $Cuenta_x_cobrar_AOA Cuenta Conductor: $Cuenta_x_cobrar_COND <br>";
					$Descripcion_factura='SRV. C. ELEGIDO';
					
					// PRIMER ASIENTO: Ingreso AOA   SE LLEVA AL DEBITO  DE LA 41 POR SER UNA PERDIDA 
					$ConsecutivoG++;
					$Descripcion_factura='SRV. C. ELEGIDO Ingr inverso AOA x perdida '.$DF->descripcion ;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_ingreso_aoa;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='D';
					$I->Valor=$SUBTOTAL_AOA;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$SUBTOTAL_AOA;
					$I->Valor_transaccion_libro3=$SUBTOTAL_AOA;
					fwrite( $DD1,$I->genera());
					
					// SEGUNDO ASIENTO: Ingreso CONDUCTOR
					$ConsecutivoG++;
					$Descripcion_factura='SRV. C. ELEGIDO Ingreso CONDUCTOR '.$DF->descripcion;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					//$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Tipo_doc_cruce='EX';
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$DF->idservicio;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$DF->conductor;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_ingreso_cond;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$DF->conductor;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='C';
					$I->Valor=$SUBTOTAL_COND;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$SUBTOTAL_COND;
					$I->Valor_transaccion_libro3=$SUBTOTAL_COND;
					fwrite( $DD1,$I->genera());
					
					// TERCER ASIENTO: IVA AOA   se lleva al DEBITO de la 24 por ser una perdida
					$ConsecutivoG++;
					$Descripcion_factura='SRV. C. ELEGIDO Iva inverso AOA x perdida '.$DF->descripcion;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_iva_gen_aoa;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='D';
					$I->Valor=$Iva_generado_AOA;
					$I->Tasa_base_iva=$CFG->mov_porc_iva_aoa;
					$I->Base_iva_retencion=$SUBTOTAL_AOA;
					$I->Base_iva_ret_libro2=$SUBTOTAL_AOA;
					$I->Base_iva_ret_libro3=$SUBTOTAL_AOA;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Iva_generado_AOA;
					$I->Valor_transaccion_libro3=$Iva_generado_AOA;
					fwrite( $DD1,$I->genera());
					
					// CUARTO ASIENTO: IVA CONDUCTOR
					$ConsecutivoG++;
					$Descripcion_factura='SRV. C. ELEGIDO Iva CONDUCTOR '.$DF->descripcion;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					//$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Tipo_doc_cruce='EX';
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$DF->idservicio;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$DF->conductor;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_iva_gen_cond;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$DF->conductor;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='C';
					$I->Valor=$Iva_generado_COND;
					$I->Tasa_base_iva=$CFG->mov_porc_iva_aoa;
					$I->Base_iva_retencion=$SUBTOTAL_COND;
					$I->Base_iva_ret_libro2=$SUBTOTAL_COND;
					$I->Base_iva_ret_libro3=$SUBTOTAL_COND;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Iva_generado_COND;
					$I->Valor_transaccion_libro3=$Iva_generado_COND;
					fwrite( $DD1,$I->genera());
					
					// QUINTO ASIENTO: RETENCION POR SERVICIOS AOA  se lleva al CREDITO de la 1355 por ser una perdida
					if($Cliente->p_rete_fuente>0.00)
					{
						$ConsecutivoG++;
						$Descripcion_factura='SRV. C. ELEGIDO Rete inversa Serv AOA x perdida '.$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						$I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$F->consecutivo;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$Cliente->identificacion;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_ret_serv_aoa;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$Cliente->identificacion;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='C';
						$I->Valor=$Retencion_servicios_AOA;
						$I->Tasa_base_iva=$CFG->mov_porc_ret_s_aoa;
						$I->Base_iva_retencion=$SUBTOTAL_AOA;
						$I->Base_iva_ret_libro2=$SUBTOTAL_AOA;
						$I->Base_iva_ret_libro3=$SUBTOTAL_AOA;
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=$Retencion_servicios_AOA;
						$I->Valor_transaccion_libro3=$Retencion_servicios_AOA;
						fwrite( $DD1,$I->genera());
							
						// SEXTO ASIENTO: RETENCION POR SERVICIOS CONDUCTOR
						$ConsecutivoG++;
						$Descripcion_factura='SRV. C. ELEGIDO Rete Servicios Conductor '.$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						//$I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Tipo_doc_cruce='EX';
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$DF->idservicio;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$DF->conductor;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_ret_serv_cond;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$DF->conductor;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='D';
						$I->Valor=$Retencion_servicios_COND;
						$I->Tasa_base_iva=$CFG->mov_por_ret_s_cond;
						$I->Base_iva_retencion=$SUBTOTAL_COND;
						$I->Base_iva_ret_libro2=$SUBTOTAL_COND;
						$I->Base_iva_ret_libro3=$SUBTOTAL_COND;
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=$Retencion_servicios_COND;
						$I->Valor_transaccion_libro3=$Retencion_servicios_COND;
						fwrite( $DD1,$I->genera());
					}
				
					// SEPTIMO ASIENTO: IVA RETENIDO AOA se lleva al CREDITO de la 1355 por ser una perdida
					if($Cliente->p_rete_iva>0.00)
					{
						$ConsecutivoG++;
						$Descripcion_factura='SRV. C. ELEGIDO Rete Iva inverso AOA x perdida '.$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						$I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$F->consecutivo;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$Cliente->identificacion;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_iva_reten_aoa;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$Cliente->identificacion;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='C';
						$I->Valor=$Iva_retenido_AOA;
						$I->Tasa_base_iva=$CFG->mov_porc_ivar_aoa;
						$I->Base_iva_retencion=$SUBTOTAL_AOA;
						$I->Base_iva_ret_libro2=$SUBTOTAL_AOA;
						$I->Base_iva_ret_libro3=$SUBTOTAL_AOA;
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=$Iva_retenido_AOA;
						$I->Valor_transaccion_libro3=$Iva_retenido_AOA;
						fwrite( $DD1,$I->genera());

						// OCTAVO ASIENTO: IVA RETENIDO CONDUCTOR
						$ConsecutivoG++;
						$Descripcion_factura='SRV. C. ELEGIDO Rete Iva Conductor '.$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						// $I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Tipo_doc_cruce='EX';
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$DF->idservicio;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$DF->conductor;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_iva_ret_cond;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$DF->conductor;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='D';
						$I->Valor=$Iva_retenido_COND;
						$I->Tasa_base_iva=$CFG->mov_por_ret_ivar_con;
						$I->Base_iva_retencion=$SUBTOTAL_COND;
						$I->Base_iva_ret_libro2=$SUBTOTAL_COND;
						$I->Base_iva_ret_libro3=$SUBTOTAL_COND;
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=$Iva_retenido_COND;
						$I->Valor_transaccion_libro3=$Iva_retenido_COND;
						fwrite( $DD1,$I->genera());
					}	
					// NOVENO ASIENTO: ICA RETENIDO AOA se lleva al CREDITO por ser una perdida
					if($Cliente->p_rete_ica>0.00)
					{
						$ConsecutivoG++;
						$Descripcion_factura='SRV. C. ELEGIDO Rete Ica inverso AOA x perdida '.$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						$I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$F->consecutivo;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$Cliente->identificacion;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_ica_ret_aoa;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$Cliente->identificacion;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='C';
						$I->Valor=$Ica_retenido_AOA;
						$I->Tasa_base_iva=$CFG->mov_porc_ica_ret_aoa;
						$I->Base_iva_retencion=$SUBTOTAL_AOA;
						$I->Base_iva_ret_libro2=$SUBTOTAL_AOA;
						$I->Base_iva_ret_libro3=$SUBTOTAL_AOA;
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=$Ica_retenido_AOA;
						$I->Valor_transaccion_libro3=$Ica_retenido_AOA;
						fwrite( $DD1,$I->genera());

						// DECIMO ASIENTO: ICA RETENIDO CONDUCTOR
						$ConsecutivoG++;
						$Descripcion_factura='SRV. C. ELEGIDO Rete Ica Conductor '.$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						// $I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Tipo_doc_cruce='EX';
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$DF->idservicio;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$DF->conductor;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_ica_ret_cond;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$DF->conductor;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='D';
						$I->Valor=$Ica_retenido_COND;
						$I->Tasa_base_iva=$CFG->mov_porc_ica_ret_con;
						$I->Base_iva_retencion=$SUBTOTAL_COND;
						$I->Base_iva_ret_libro2=$SUBTOTAL_COND;
						$I->Base_iva_ret_libro3=$SUBTOTAL_COND;
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=$Ica_retenido_COND;
						$I->Valor_transaccion_libro3=$Ica_retenido_COND;
						fwrite( $DD1,$I->genera());
					}	
					// DECIMO PRIMER ASIENTO: CUENTA POR COBRAR AOA+CONDUCTOR EL TERCERO ES AXA
					$ConsecutivoG++;
					$Descripcion_factura='SRV. C. ELEGIDO CxC Cond menos CxC AOA por perdida '.$DF->descripcion;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_ccobrar_aoa;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='D';
					$I->Valor=($Cuenta_x_cobrar_COND-$Cuenta_x_cobrar_AOA);
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=($Cuenta_x_cobrar_COND-$Cuenta_x_cobrar_AOA);
					$I->Valor_transaccion_libro3=($Cuenta_x_cobrar_COND-$Cuenta_x_cobrar_AOA);
					fwrite( $DD1,$I->genera());

				
					// DECIMO SEGUNDO ASIENTO: AUTO RETE CREE AOA DEBITO se lleva al Credito por ser una perdida
					$ConsecutivoG++;
					$Descripcion_factura='SRV. C. ELEGIDO Auto Rete inversa CREE  x perdida '.$DF->descripcion;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_auto_cree1;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='C';
					$I->Valor=$Cree_retenido;
					$I->Tasa_base_iva=$CFG->mov_porc_cree1;
					$I->Base_iva_retencion=$SUBTOTAL_AOA;
					$I->Base_iva_ret_libro2=$SUBTOTAL_AOA;
					$I->Base_iva_ret_libro3=$SUBTOTAL_AOA;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Cree_retenido;
					$I->Valor_transaccion_libro3=$Cree_retenido;
					fwrite( $DD1,$I->genera());
				
					// DECIMO TERCER ASIENTO: AUTO RETE CREE AOA CREDITO se lleva al debito por perdida
					$ConsecutivoG++;
					$Descripcion_factura='SRV. C. ELEGIDO Auto Rete inversa CREE  x perdida '.$DF->descripcion;
					$I = new Interfase_documento();
					$I->Consecutivo_grabacion=$ConsecutivoG;
					$I->Centro_operacion='001';
					$I->Tipo_documento=$TDC->codigo_suno;
					$I->Tipo_doc_cruce=$TDC->codigo_suno;
					$I->Numero_documento=$F->consecutivo;
					$I->Numero_doc_cruce=$F->consecutivo;
					$I->Fecha_documento=$Fecha_doc;
					$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
					$I->Tercero=$Cliente->identificacion;
					$I->Valor_documento=$Valor_documento;
					$I->Cuenta_contable=$CFG->mov_auto_cree2;
					$I->Centro_operacion_mov=$DF->centro_operacion;
					$I->Tercero_mov=$Cliente->identificacion;
					$I->Detalle1= l($Descripcion_factura,40);
					$I->Detalle2= substr($Descripcion_factura,40);
					$I->DC='D';
					$I->Valor=$Cree_retenido;
					$I->Tasa_base_iva=$CFG->mov_porc_cree1;
					$I->Base_iva_retencion=$SUBTOTAL_AOA;
					$I->Base_iva_ret_libro2=$SUBTOTAL_AOA;
					$I->Base_iva_ret_libro3=$SUBTOTAL_AOA;
					$I->Centro_costos=$Centro_costos;
					$I->Detalle1_doc=l($Descripcion_factura,60);
					$I->Indicador_contab_libro2='2';
					$I->Valor_transaccion_libro2=$Cree_retenido;
					$I->Valor_transaccion_libro3=$Cree_retenido;
					fwrite( $DD1,$I->genera());
					
				}
				else    /// CUANDO EL EJERCICIO DEL SERVICIO NO DA PERDIDA, SE COBRA MAS AL CLIENTE DE LO QUE SE LE PAGA AL CONDUCTOR
				{
					$Iva_generado_AOA=round($SUBTOTAL_AOA*$CFG->mov_porc_iva_aoa/100,0);
					$Iva_generado_COND=round($SUBTOTAL_COND*$CFG->mov_porc_iva_cond/100,0);
					echo "AOA = $SUBTOTAL_AOA Iva: $Iva_generado_AOA <br>Conductor: $SUBTOTAL_COND Iva: $Iva_generado_COND <br>";
					if($Cliente->p_rete_fuente>0.00)
					{
						$Retencion_servicios_AOA=round($SUBTOTAL_AOA*$Cliente->p_rete_fuente/100,0);
						$Retencion_servicios_COND=round($SUBTOTAL_COND*$Cliente->p_rete_fuente/100,0);
					}
					else $Retencion_servicios_AOA=$Retencion_servicios_COND=0;
					echo "Retencion Servicios AOA: $Retencion_servicios_AOA  Retencion Servicios Conductor: $Retencion_servicios_COND <br>";
					if($Cliente->p_rete_iva>0.00)
					{
						$Iva_retenido_AOA=round($SUBTOTAL_AOA*$Cliente->p_rete_iva/100,0);
						$Iva_retenido_COND=round($SUBTOTAL_COND*$Cliente->p_rete_iva/100,0);
					}
					else $Iva_retenido_AOA=$Iva_retenido_COND=0;
					echo "Rete Iva AOA: $Iva_retenido_AOA Rete Iva Conductor: $Iva_retenido_COND <br>";
					if($Cliente->p_rete_ica>0.00)
					{
						$Ica_retenido_AOA=round($SUBTOTAL_AOA*$Cliente->p_rete_ica/100,0);
						$Ica_retenido_COND=round($SUBTOTAL_COND*$Cliente->p_rete_ica/100,0);
					}
					else $Ica_retenido_AOA=$Ica_retenido_COND=0;
					echo "Rete Ica AOA: $Ica_retenido_AOA Rete Ica Conductor: $Ica_retenido_COND <br>";
					$Cree_retenido=round($SUBTOTAL_AOA*$CFG->mov_porc_cree1/100,0);
					$Valor_documento=$F->total+$Cree_retenido;
					echo "Valor Documento: $Valor_documento <br>";
					$Cuenta_x_cobrar_AOA=$SUBTOTAL_AOA+$Iva_generado_AOA-$Retencion_servicios_AOA-$Iva_retenido_AOA-$Ica_retenido_AOA;
					$Cuenta_x_cobrar_COND=$SUBTOTAL_COND+$Iva_generado_COND-$Retencion_servicios_COND-$Iva_retenido_COND-$Ica_retenido_COND;
					echo "Cuenta AOA $Cuenta_x_cobrar_AOA Cuenta Conductor: $Cuenta_x_cobrar_COND <br>";
					$Descripcion_factura='SRV. C. ELEGIDO';
					
					// PRIMER ASIENTO: Ingreso AOA
					if($SUBTOTAL_AOA>0)  // SOLO CREA EL ASIENTO SI EL INGRESO ES DISTINTO A CERO
					{
						$ConsecutivoG++;
						$Descripcion_factura='SRV. C. ELEGIDO Ingreso AOA '.$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						$I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$F->consecutivo;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$Cliente->identificacion;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_ingreso_aoa;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$Cliente->identificacion;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='C';
						$I->Valor=$SUBTOTAL_AOA;
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=$SUBTOTAL_AOA;
						$I->Valor_transaccion_libro3=$SUBTOTAL_AOA;
						fwrite( $DD1,$I->genera());
					}
					
					
					// SEGUNDO ASIENTO: Ingreso CONDUCTOR
					if($SUBTOTAL_COND>0)
					{
						$ConsecutivoG++;
						$Descripcion_factura='SRV. C. ELEGIDO Ingreso CONDUCTOR '.$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						// $I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Tipo_doc_cruce='EX';
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$DF->idservicio;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$DF->conductor;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_ingreso_cond;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$DF->conductor;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='C';
						$I->Valor=$SUBTOTAL_COND;
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=$SUBTOTAL_COND;
						$I->Valor_transaccion_libro3=$SUBTOTAL_COND;
						fwrite( $DD1,$I->genera());
					}
					// TERCER ASIENTO: IVA AOA
					if($Iva_generado_AOA>0)  // solo genera el asiento si el valor e distinto a cero
					{
						$ConsecutivoG++;
						$Descripcion_factura='SRV. C. ELEGIDO Iva AOA '.$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						$I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$F->consecutivo;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$Cliente->identificacion;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_iva_gen_aoa;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$Cliente->identificacion;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='C';
						$I->Valor=$Iva_generado_AOA;
						$I->Tasa_base_iva=$CFG->mov_porc_iva_aoa;
						$I->Base_iva_retencion=$SUBTOTAL_AOA;
						$I->Base_iva_ret_libro2=$SUBTOTAL_AOA;
						$I->Base_iva_ret_libro3=$SUBTOTAL_AOA;
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=$Iva_generado_AOA;
						$I->Valor_transaccion_libro3=$Iva_generado_AOA;
						fwrite( $DD1,$I->genera());
					}
					
					// CUARTO ASIENTO: IVA CONDUCTOR
					if($Iva_generado_COND>0)
					{
						$ConsecutivoG++;
						$Descripcion_factura='SRV. C. ELEGIDO Iva CONDUCTOR '.$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						// $I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Tipo_doc_cruce='EX';
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$DF->idservicio;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$DF->conductor;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_iva_gen_cond;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$DF->conductor;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='C';
						$I->Valor=$Iva_generado_COND;
						$I->Tasa_base_iva=$CFG->mov_porc_iva_cond;
						$I->Base_iva_retencion=$SUBTOTAL_COND;
						$I->Base_iva_ret_libro2=$SUBTOTAL_COND;
						$I->Base_iva_ret_libro3=$SUBTOTAL_COND;
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=$Iva_generado_COND;
						$I->Valor_transaccion_libro3=$Iva_generado_COND;
						fwrite( $DD1,$I->genera());
					}
					// QUINTO ASIENTO: RETENCION POR SERVICIOS AOA
					if($Cliente->p_rete_fuente>0.00 )
					{
						if($Retencion_servicios_AOA>0)
						{
							$ConsecutivoG++;
							$Descripcion_factura='SRV. C. ELEGIDO Rete Servicios AOA '.$DF->descripcion;
							$I = new Interfase_documento();
							$I->Consecutivo_grabacion=$ConsecutivoG;
							$I->Centro_operacion='001';
							$I->Tipo_documento=$TDC->codigo_suno;
							$I->Tipo_doc_cruce=$TDC->codigo_suno;
							$I->Numero_documento=$F->consecutivo;
							$I->Numero_doc_cruce=$F->consecutivo;
							$I->Fecha_documento=$Fecha_doc;
							$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
							$I->Tercero=$Cliente->identificacion;
							$I->Valor_documento=$Valor_documento;
							$I->Cuenta_contable=$CFG->mov_ret_serv_aoa;
							$I->Centro_operacion_mov=$DF->centro_operacion;
							$I->Tercero_mov=$Cliente->identificacion;
							$I->Detalle1= l($Descripcion_factura,40);
							$I->Detalle2= substr($Descripcion_factura,40);
							$I->DC='D';
							$I->Valor=$Retencion_servicios_AOA;
							$I->Tasa_base_iva=$CFG->mov_porc_ret_s_aoa;
							$I->Base_iva_retencion=$SUBTOTAL_AOA;
							$I->Base_iva_ret_libro2=$SUBTOTAL_AOA;
							$I->Base_iva_ret_libro3=$SUBTOTAL_AOA;
							$I->Centro_costos=$Centro_costos;
							$I->Detalle1_doc=l($Descripcion_factura,60);
							$I->Indicador_contab_libro2='2';
							$I->Valor_transaccion_libro2=$Retencion_servicios_AOA;
							$I->Valor_transaccion_libro3=$Retencion_servicios_AOA;
							fwrite( $DD1,$I->genera());
						}
							
						// SEXTO ASIENTO: RETENCION POR SERVICIOS CONDUCTOR
						if($Retencion_servicios_COND>0)
						{
							$ConsecutivoG++;
							$Descripcion_factura='SRV. C. ELEGIDO Rete Servicios Conductor '.$DF->descripcion;
							$I = new Interfase_documento();
							$I->Consecutivo_grabacion=$ConsecutivoG;
							$I->Centro_operacion='001';
							$I->Tipo_documento=$TDC->codigo_suno;
							// $I->Tipo_doc_cruce=$TDC->codigo_suno;
							$I->Tipo_doc_cruce='EX';
							$I->Numero_documento=$F->consecutivo;
							$I->Numero_doc_cruce=$DF->idservicio;
							$I->Fecha_documento=$Fecha_doc;
							$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
							$I->Tercero=$DF->conductor;
							$I->Valor_documento=$Valor_documento;
							$I->Cuenta_contable=$CFG->mov_ret_serv_cond;
							$I->Centro_operacion_mov=$DF->centro_operacion;
							$I->Tercero_mov=$DF->conductor;
							$I->Detalle1= l($Descripcion_factura,40);
							$I->Detalle2= substr($Descripcion_factura,40);
							$I->DC='D';
							$I->Valor=$Retencion_servicios_COND;
							$I->Tasa_base_iva=$CFG->mov_por_ret_s_cond;
							$I->Base_iva_retencion=$SUBTOTAL_COND;
							$I->Base_iva_ret_libro2=$SUBTOTAL_COND;
							$I->Base_iva_ret_libro3=$SUBTOTAL_COND;
							$I->Centro_costos=$Centro_costos;
							$I->Detalle1_doc=l($Descripcion_factura,60);
							$I->Indicador_contab_libro2='2';
							$I->Valor_transaccion_libro2=$Retencion_servicios_COND;
							$I->Valor_transaccion_libro3=$Retencion_servicios_COND;
							fwrite( $DD1,$I->genera());
						}
					}
				
					// SEPTIMO ASIENTO: IVA RETENIDO AOA
					if($Cliente->p_rete_iva>0.00)
					{
						if($Iva_retenido_AOA>0)
						{
							$ConsecutivoG++;
							$Descripcion_factura='SRV. C. ELEGIDO Rete Iva AOA '.$DF->descripcion;
							$I = new Interfase_documento();
							$I->Consecutivo_grabacion=$ConsecutivoG;
							$I->Centro_operacion='001';
							$I->Tipo_documento=$TDC->codigo_suno;
							$I->Tipo_doc_cruce=$TDC->codigo_suno;
							$I->Numero_documento=$F->consecutivo;
							$I->Numero_doc_cruce=$F->consecutivo;
							$I->Fecha_documento=$Fecha_doc;
							$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
							$I->Tercero=$Cliente->identificacion;
							$I->Valor_documento=$Valor_documento;
							$I->Cuenta_contable=$CFG->mov_iva_reten_aoa;
							$I->Centro_operacion_mov=$DF->centro_operacion;
							$I->Tercero_mov=$Cliente->identificacion;
							$I->Detalle1= l($Descripcion_factura,40);
							$I->Detalle2= substr($Descripcion_factura,40);
							$I->DC='D';
							$I->Valor=$Iva_retenido_AOA;
							$I->Tasa_base_iva=$CFG->mov_porc_ivar_aoa;
							$I->Base_iva_retencion=$SUBTOTAL_AOA;
							$I->Base_iva_ret_libro2=$SUBTOTAL_AOA;
							$I->Base_iva_ret_libro3=$SUBTOTAL_AOA;
							$I->Centro_costos=$Centro_costos;
							$I->Detalle1_doc=l($Descripcion_factura,60);
							$I->Indicador_contab_libro2='2';
							$I->Valor_transaccion_libro2=$Iva_retenido_AOA;
							$I->Valor_transaccion_libro3=$Iva_retenido_AOA;
							fwrite( $DD1,$I->genera());
						}
						
						// OCTAVO ASIENTO: IVA RETENIDO CONDUCTOR
						if($Iva_retenido_COND>0)
						{
							$ConsecutivoG++;
							$Descripcion_factura='SRV. C. ELEGIDO Rete Iva Conductor '.$DF->descripcion;
							$I = new Interfase_documento();
							$I->Consecutivo_grabacion=$ConsecutivoG;
							$I->Centro_operacion='001';
							$I->Tipo_documento=$TDC->codigo_suno;
							// $I->Tipo_doc_cruce=$TDC->codigo_suno;
							$I->Tipo_doc_cruce='EX';
							$I->Numero_documento=$F->consecutivo;
							$I->Numero_doc_cruce=$DF->idservicio;
							$I->Fecha_documento=$Fecha_doc;
							$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
							$I->Tercero=$DF->conductor;
							$I->Valor_documento=$Valor_documento;
							$I->Cuenta_contable=$CFG->mov_iva_ret_cond;
							$I->Centro_operacion_mov=$DF->centro_operacion;
							$I->Tercero_mov=$DF->conductor;
							$I->Detalle1= l($Descripcion_factura,40);
							$I->Detalle2= substr($Descripcion_factura,40);
							$I->DC='D';
							$I->Valor=$Iva_retenido_COND;
							$I->Tasa_base_iva=$CFG->mov_por_ret_ivar_con;
							$I->Base_iva_retencion=$SUBTOTAL_COND;
							$I->Base_iva_ret_libro2=$SUBTOTAL_COND;
							$I->Base_iva_ret_libro3=$SUBTOTAL_COND;
							$I->Centro_costos=$Centro_costos;
							$I->Detalle1_doc=l($Descripcion_factura,60);
							$I->Indicador_contab_libro2='2';
							$I->Valor_transaccion_libro2=$Iva_retenido_COND;
							$I->Valor_transaccion_libro3=$Iva_retenido_COND;
							fwrite( $DD1,$I->genera());
						}
					}	
					// NOVENO ASIENTO: ICA RETENIDO AOA
					if($Cliente->p_rete_ica>0.00)
					{
						if($Ica_retenido_AOA>0)
						{
							$ConsecutivoG++;
							$Descripcion_factura='SRV. C. ELEGIDO Rete Ica AOA '.$DF->descripcion;
							$I = new Interfase_documento();
							$I->Consecutivo_grabacion=$ConsecutivoG;
							$I->Centro_operacion='001';
							$I->Tipo_documento=$TDC->codigo_suno;
							$I->Tipo_doc_cruce=$TDC->codigo_suno;
							$I->Numero_documento=$F->consecutivo;
							$I->Numero_doc_cruce=$F->consecutivo;
							$I->Fecha_documento=$Fecha_doc;
							$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
							$I->Tercero=$Cliente->identificacion;
							$I->Valor_documento=$Valor_documento;
							$I->Cuenta_contable=$CFG->mov_ica_ret_aoa;
							$I->Centro_operacion_mov=$DF->centro_operacion;
							$I->Tercero_mov=$Cliente->identificacion;
							$I->Detalle1= l($Descripcion_factura,40);
							$I->Detalle2= substr($Descripcion_factura,40);
							$I->DC='D';
							$I->Valor=$Ica_retenido_AOA;
							$I->Tasa_base_iva=$CFG->mov_porc_ica_ret_aoa;
							$I->Base_iva_retencion=$SUBTOTAL_AOA;
							$I->Base_iva_ret_libro2=$SUBTOTAL_AOA;
							$I->Base_iva_ret_libro3=$SUBTOTAL_AOA;
							$I->Centro_costos=$Centro_costos;
							$I->Detalle1_doc=l($Descripcion_factura,60);
							$I->Indicador_contab_libro2='2';
							$I->Valor_transaccion_libro2=$Ica_retenido_AOA;
							$I->Valor_transaccion_libro3=$Ica_retenido_AOA;
							fwrite( $DD1,$I->genera());
						}
						
						// DECIMO ASIENTO: ICA RETENIDO CONDUCTOR
						if($Ica_retenido_COND>0)
						{
							$ConsecutivoG++;
							$Descripcion_factura='SRV C. ELEGIDO Rete Ica Cond '.$DF->descripcion;
							$I = new Interfase_documento();
							$I->Consecutivo_grabacion=$ConsecutivoG;
							$I->Centro_operacion='001';
							$I->Tipo_documento=$TDC->codigo_suno;
							// $I->Tipo_doc_cruce=$TDC->codigo_suno;
							$I->Tipo_doc_cruce='EX';
							$I->Numero_documento=$F->consecutivo;
							$I->Numero_doc_cruce=$DF->idservicio;
							$I->Fecha_documento=$Fecha_doc;
							$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
							$I->Tercero=$DF->conductor;
							$I->Valor_documento=$Valor_documento;
							$I->Cuenta_contable=$CFG->mov_ica_ret_cond;
							$I->Centro_operacion_mov=$DF->centro_operacion;
							$I->Tercero_mov=$DF->conductor;
							$I->Detalle1= l($Descripcion_factura,40);
							$I->Detalle2= substr($Descripcion_factura,40);
							$I->DC='D';
							$I->Valor=$Ica_retenido_COND;
							$I->Tasa_base_iva=$CFG->mov_porc_ica_ret_con;
							$I->Base_iva_retencion=$SUBTOTAL_COND;
							$I->Base_iva_ret_libro2=$SUBTOTAL_COND;
							$I->Base_iva_ret_libro3=$SUBTOTAL_COND;
							$I->Centro_costos=$Centro_costos;
							$I->Detalle1_doc=l($Descripcion_factura,60);
							$I->Indicador_contab_libro2='2';
							$I->Valor_transaccion_libro2=$Ica_retenido_COND;
							$I->Valor_transaccion_libro3=$Ica_retenido_COND;
							fwrite( $DD1,$I->genera());
						}
					}	
					// DECIMO PRIMER ASIENTO: CUENTA POR COBRAR AOA+CONDUCTOR EL TERCERO ES AXA
					if($Cuenta_x_cobrar_AOA+$Cuenta_x_cobrar_COND>0)
					{
						$ConsecutivoG++;
						$Descripcion_factura='SRV C. ELEGIDO CXC AOA y COND '.$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						$I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$F->consecutivo;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$Cliente->identificacion;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_ccobrar_aoa;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$Cliente->identificacion;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='D';
						$I->Valor=($Cuenta_x_cobrar_AOA+$Cuenta_x_cobrar_COND);
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=($Cuenta_x_cobrar_AOA+$Cuenta_x_cobrar_COND);
						$I->Valor_transaccion_libro3=($Cuenta_x_cobrar_AOA+$Cuenta_x_cobrar_COND);
						fwrite( $DD1,$I->genera());
					}
				
					// DECIMO SEGUNDO ASIENTO: AUTO RETE CREE AOA DEBITO
					if($Cree_retenido>0)
					{
						$ConsecutivoG++;
						$Descripcion_factura='SRV C. ELEGIDO Auto Rete CREE '.$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						$I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$F->consecutivo;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$Cliente->identificacion;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_auto_cree1;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$Cliente->identificacion;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='D';
						$I->Valor=$Cree_retenido;
						$I->Tasa_base_iva=$CFG->mov_porc_cree1;
						$I->Base_iva_retencion=$SUBTOTAL_AOA;
						$I->Base_iva_ret_libro2=$SUBTOTAL_AOA;
						$I->Base_iva_ret_libro3=$SUBTOTAL_AOA;
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=$Cree_retenido;
						$I->Valor_transaccion_libro3=$Cree_retenido;
						fwrite( $DD1,$I->genera());
					}
					
					// DECIMO TERCER ASIENTO: AUTO RETE CREE AOA CREDITO
					if($Cree_retenido>0)
					{
						$ConsecutivoG++;
						$Descripcion_factura='SRV C. ELEGIDO Auto Rete CREE ' .$DF->descripcion;
						$I = new Interfase_documento();
						$I->Consecutivo_grabacion=$ConsecutivoG;
						$I->Centro_operacion='001';
						$I->Tipo_documento=$TDC->codigo_suno;
						$I->Tipo_doc_cruce=$TDC->codigo_suno;
						$I->Numero_documento=$F->consecutivo;
						$I->Numero_doc_cruce=$F->consecutivo;
						$I->Fecha_documento=$Fecha_doc;
						$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
						$I->Tercero=$Cliente->identificacion;
						$I->Valor_documento=$Valor_documento;
						$I->Cuenta_contable=$CFG->mov_auto_cree2;
						$I->Centro_operacion_mov=$DF->centro_operacion;
						$I->Tercero_mov=$Cliente->identificacion;
						$I->Detalle1= l($Descripcion_factura,40);
						$I->Detalle2= substr($Descripcion_factura,40);
						$I->DC='C';
						$I->Valor=$Cree_retenido;
						$I->Tasa_base_iva=$CFG->mov_porc_cree1;
						$I->Base_iva_retencion=$SUBTOTAL_AOA;
						$I->Base_iva_ret_libro2=$SUBTOTAL_AOA;
						$I->Base_iva_ret_libro3=$SUBTOTAL_AOA;
						$I->Centro_costos=$Centro_costos;
						$I->Detalle1_doc=l($Descripcion_factura,60);
						$I->Indicador_contab_libro2='2';
						$I->Valor_transaccion_libro2=$Cree_retenido;
						$I->Valor_transaccion_libro3=$Cree_retenido;
						fwrite( $DD1,$I->genera());
					}
				}
			}
		}
	}
	return $ConsecutivoG;
}

function Genera_asientos_movilidad_aoato($F,$ConsecutivoG,$Cliente,$Aseguradora,$Centro_costos,$Ofic,$DD1,$DD2,$CFG,$TDC)
{
	
	$Nit_AOA='900174552';
	$Fecha_doc=date('Ymd',strtotime($F->fecha_emision));
	$Fecha_vencimiento=date('Ymd',strtotime($F->fecha_vencimiento));
	
	/**
	28200501 C 45000 NETO  INGRESO AL CONDUCTOR ELEGIDO
	13050501 D 50400 TOTAL CUENTA POR COBRAR CONDUCTOR ELEGIDO
	*/

	if($F->anulada)
	{
		$ConsecutivoG++;
		$Descripcion_factura='ANULADA SRV. C. ELEGIDO';
		$I = new Interfase_documento();
		$I->Consecutivo_grabacion=$ConsecutivoG;
		$I->Centro_operacion='001';
		$I->Tipo_documento=$TDC->codigo_suno;
		$I->Tipo_doc_cruce=$TDC->codigo_suno;
		$I->Numero_documento=$F->consecutivo;
		$I->Numero_doc_cruce=$F->consecutivo;
		$I->Fecha_documento=$Fecha_doc;
		$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
		$I->Tercero=$Cliente->identificacion;
		$I->Indicador_anulacion='X';
		$I->Valor_documento=0;
		$I->Cuenta_contable=$CFG->mov_ingreso_aoa;
		$I->Centro_operacion_mov='001';
		$I->Tercero_mov=$Cliente->identificacion;
		$I->Detalle1= l($Descripcion_factura,40);
		$I->Detalle2= substr($Descripcion_factura,40);
		$I->DC='C';
		$I->Valor=0;
		$I->Centro_costos=$Centro_costos;
		$I->Detalle1_doc=l($Descripcion_factura,60);
		$I->Indicador_contab_libro2='2';
		$I->Valor_transaccion_libro2=0;
		$I->Valor_transaccion_libro3=0;
		fwrite( $DD1,$I->genera());
	}
	else
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
		$T->Centro_operacion=$Ofic->centro_operacion;
		$T->Zona='01    ';
		$T->Indicador_liquidacion_impuesto='1';
		$T->Codigo_rete_otro='01';
		$T->Codigo_condicion_pago='0 ';
		$T->Fecha_creacion=date('Ymd');
		fwrite($DD2,$T->genera());
				
		$Detalle=q("select * from facturad where factura=$F->id");
		while($DF=mysql_fetch_object($Detalle))
		{
			$Base=round($DF->cantidad*$DF->unitario,0);
			echo "<hr>Base: $Base ";
			$SUBTOTAL_AOA=$Base;
			$Valor_documento=$F->total;
			echo "Valor Documento: $Valor_documento <br>";
			$Cuenta_x_cobrar_AOA=$SUBTOTAL_AOA;
			$Descripcion_factura='SRV. TRNSP.PASAJ.';
			// PRIMER ASIENTO: Ingreso AOA
			if($SUBTOTAL_AOA>0)  // SOLO CREA EL ASIENTO SI EL INGRESO ES DISTINTO A CERO
			{
				$ConsecutivoG++;
				$Descripcion_factura='SRV TRSP.PASAJ. '.$DF->descripcion;
				$I = new Interfase_documento();
				$I->Consecutivo_grabacion=$ConsecutivoG;
				$I->Centro_operacion='001';
				$I->Tipo_documento=$TDC->codigo_suno;
				$I->Tipo_doc_cruce=$TDC->codigo_suno;
				$I->Numero_documento=$F->consecutivo;
				$I->Numero_doc_cruce=$F->consecutivo;
				$I->Fecha_documento=$Fecha_doc;
				$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
				$I->Tercero='900645942';
				$I->Valor_documento=$Valor_documento;
				$I->Cuenta_contable=$CFG->aoato_cuenta_ingreso;
				$I->Centro_operacion_mov=$DF->centro_operacion;
				$I->Tercero_mov='900645942';
				$I->Detalle1= l($Descripcion_factura,40);
				$I->Detalle2= substr($Descripcion_factura,40);
				$I->DC='C';
				$I->Valor=$SUBTOTAL_AOA;
				$I->Centro_costos=$Centro_costos;
				$I->Detalle1_doc=l($Descripcion_factura,60);
				$I->Indicador_contab_libro2='2';
				$I->Valor_transaccion_libro2=$SUBTOTAL_AOA;
				$I->Valor_transaccion_libro3=$SUBTOTAL_AOA;
				fwrite( $DD1,$I->genera());
			}
			// DECIMO PRIMER ASIENTO: CUENTA POR COBRAR AOA+CONDUCTOR EL TERCERO ES AXA
			if($Cuenta_x_cobrar_AOA>0)
			{
				$ConsecutivoG++;
				$Descripcion_factura='SRV TRSP.PASAJ. '.$DF->descripcion;
				$I = new Interfase_documento();
				$I->Consecutivo_grabacion=$ConsecutivoG;
				$I->Centro_operacion='001';
				$I->Tipo_documento=$TDC->codigo_suno;
				$I->Tipo_doc_cruce=$TDC->codigo_suno;
				$I->Numero_documento=$F->consecutivo;
				$I->Numero_doc_cruce=$F->consecutivo;
				$I->Fecha_documento=$Fecha_doc;
				$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
				$I->Tercero=$Cliente->identificacion;
				$I->Valor_documento=$Valor_documento;
				$I->Cuenta_contable=$CFG->aoato_contrapartida;
				$I->Centro_operacion_mov=$DF->centro_operacion;
				$I->Tercero_mov=$Cliente->identificacion;
				$I->Detalle1= l($Descripcion_factura,40);
				$I->Detalle2= substr($Descripcion_factura,40);
				$I->DC='D';
				$I->Valor=($Cuenta_x_cobrar_AOA+$Cuenta_x_cobrar_COND);
				$I->Centro_costos=$Centro_costos;
				$I->Detalle1_doc=l($Descripcion_factura,60);
				$I->Indicador_contab_libro2='2';
				$I->Valor_transaccion_libro2=($Cuenta_x_cobrar_AOA+$Cuenta_x_cobrar_COND);
				$I->Valor_transaccion_libro3=($Cuenta_x_cobrar_AOA+$Cuenta_x_cobrar_COND);
				fwrite( $DD1,$I->genera());
			}
		}
	}
	return $ConsecutivoG;
}

function exporta_rc_uno()  // LA EXPORTACION SE INICIA DESDE EL PROGRAMA DE CARTERA E INVOCA ESTA RUTINA.
{
	global $recibos;
	html('EXPORTACION CONTABLE DE RECIBOS DE CAJA A SISTEMA UNO');
	
	include('Interfase.Sistemauno.class.php');
	$TDC=37;  // id del tipo de documento contable RECIBO DE CAJA
	$TDC = qo( "select * from tipo_doc_cont where id=$TDC" );
	$CFG=qo("select * from cfg_factura where activo=1");
	$TDCF=qo("select * from tipo_doc_cont where id=8"); // DOCUMENTO CONTABLE DE FACTURACION
	
	$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
	if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
	$DD1=fopen($DESTINO_PLANO1,'w+');
	
	$Recibos=explode(';',$recibos);
	include('inc/link.php');
	$ConsecutivoG=0;
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
			$td_contable=$Oficina->te_contable_uno;
			mysql_query("update recibo_caja set td_contable='$td_contable', consec_contable='$consec_contable' where id='$id' ",$LINK);
			echo "<br>$id: Tipo documento: $td_contable  Consecutivo: $consec_contable";
			$Recibo=mysql_query("select * from recibo_caja where id=$id",$LINK);
			$RC=mysql_fetch_object($Recibo);
			$Cliente=qom("select * from cliente where id=$RC->cliente",$LINK);
			$Banco=qom("select * from banco_aoa where id=$RC->banco_aoa",$LINK);
			$Factura=qom("select * from factura where id=$RC->factura",$LINK);
			echo " RC: $RC->consecutivo Factura: $Factura->consecutivo idSiniestro= $Factura->siniestro ";
			$Centro_costos=$Oficina->centro_costos_uno;
			$Tipo_doc_cruce=$TDCF->codigo_suno;
			$Numero_doc_cruce=$Factura->consecutivo;
			$Fecha_vencimiento=date('Ymd',strtotime($Factura->vecha_vencimiento));
			if($Factura->siniestro)
			{
				if($Siniestro=qom("select id,ubicacion,numero from siniestro where id=$Factura->siniestro",$LINK))
				{
					echo " Siniestro: $Siniestro->numero Ubicacion $Siniestro->ubicacion ";
					if($Siniestro->ubicacion)
					{
						$Ubicacion=qom("select * from ubicacion where id=$Siniestro->ubicacion",$LINK);
						// if($Ubicacion->vehiculo)
						// {
							// $Vehiculo=qom("select * from vehiculo where id=$Ubicacion->vehiculo",$LINK);
							// $Centro_costos=$Vehiculo->centro_costos;
						// }
						if($Ubicacion->flota)
						{
							$Flota=qom("select * from aseguradora where id=$Ubicacion->flota",$LINK);
							$Centro_costos=$Flota->ccostos_uno;
						}
					}
				}
			}
			$Concepto="PAGO FE $Factura->consecutivo - OF. $Oficina->sigla RC. $RC->consecutivo ";
			echo " preparando la clase ";
			// ASIENTO DE AFECTACION A LA CUENTA DE CARTERA
			
			$ConsecutivoG++;
			$Descripcion= ($RC->anulado?'ANULADO: ':'').$Concepto;
			$Valor=($RC->anulado?1:$RC->valor);
			$Fecha_doc=date('Ymd',strtotime($RC->fecha));
			///
			$I = new Interfase_documento();
			$I->Consecutivo_grabacion=$ConsecutivoG;
			$I->Centro_operacion=$Oficina->centro_operacion;
			$I->Tipo_documento=$td_contable;
			$I->Numero_documento=$RC->consecutivo;
			$I->Fecha_documento=$Fecha_doc;
			$I->Tercero=$Cliente->identificacion;
			$I->Indicador_anulacion=($RC->anulado?'X':' ');
			$I->Valor_documento=$Valor;
			$I->Cuenta_contable=$CFG->cuenta_activo_venta;
			// $I->Centro_operacion_mov=$Oficina->centro_operacion;
			$I->Centro_operacion_mov='001';
			$I->Tercero_mov=$Cliente->identificacion;
			$I->Detalle1= l($Descripcion,40);
			$I->Detalle2= substr($Descripcion,40);
			$I->DC='C';
			$I->Valor=$Valor;
			$I->Centro_costos=$Centro_costos;
			$I->Detalle1_doc=l($Descripcion,60);
			$I->Indicador_contab_libro2='2';
			$I->Valor_transaccion_libro2=$Valor;
			$I->Valor_transaccion_libro3=$Valor;
			$I->Tipo_doc_cruce=$Tipo_doc_cruce;
			$I->Numero_doc_cruce=$Numero_doc_cruce;
			$I->Fecha_vencimiento_cruce=$Fecha_vencimiento;
			fwrite( $DD1,$I->genera());
			// ASIENTO PARA LA CUENTA DEL BANCO
			$ConsecutivoG++;
			///
			$I = new Interfase_documento();
			$I->Consecutivo_grabacion=$ConsecutivoG;
			$I->Centro_operacion=$Oficina->centro_operacion;
			$I->Tipo_documento=$td_contable;
			$I->Numero_documento=$RC->consecutivo;
			$I->Fecha_documento=$Fecha_doc;
			$I->Tercero=$Cliente->identificacion;
			$I->Indicador_anulacion=($RC->anulado?'X':' ');
			$I->Valor_documento=$Valor;
			$I->Cuenta_contable=$Banco->cuenta_contable_uno;
			$I->Centro_operacion_mov='001';
			$I->Tercero_mov=$Cliente->identificacion;
			$I->Detalle1= l($Descripcion,40);
			$I->Detalle2= substr($Descripcion,40);
			$I->DC='D';
			$I->Valor=$Valor;
			$I->Centro_costos=$Centro_costos;
			$I->Detalle1_doc=l($Descripcion,60);
			$I->Indicador_contab_libro2='2';
			$I->Valor_transaccion_libro2=$Valor;
			$I->Valor_transaccion_libro3=$Valor;
			$I->Numero_cuenta=$Banco->cuenta;
			fwrite( $DD1,$I->genera());
		}
	}
	mysql_close($LINK);
	echo "<br>";
	echo " Fin proceso de detalle. ";
	fclose( $DD1 );
	echo "<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		<script language='javascript'>
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=FIBATCH.TXT','Oculto1');
		</script>
		</body>";
}

function exportacion_garantias_uno()
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
	<body><h3>EXPORTACION DE GARANTIAS DE SERVICIO y  GARANTIAS NO REEMBOLSABLES A SISTEMA UNO</h3>
	<form action='zgenerador_contable.suno.php' target='Tablero_exportacion_garantias' method='POST' name='forma' id='forma'>
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
	
	function modificar_rc(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTRC&id='+id,0,0,700,900,'rc');}
	
	function asigna_banco(banco,id) {	if(confirm('Desea asignar el banco a este recibo de caja?')) {	window.open('zgenerador_contable.suno.php?Acc=asignar_banco&id='+id+'&banco='+banco,'Oculto_exportacionrc');	} }
	
	var Aplano=new Array();

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
	function modificar_sin_autor(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTAU&id='+id,0,0,700,900,'au');}
	function modificar_siniestro(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTSN&id='+id,0,0,700,900,'au');}

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

	</script>
	<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>
	<h3>EXPORTACION DE GARANTIAS EFECTIVO Y TARJETAS DEBITO [ $FI - $FF ] A SISTEMA UNO</h3>
	<iframe name='Oculto_exportacionrc' id='Oculto_exportacionrc' height=1 width=1 style='visibility:hidden'></iframe>";
	// OBTIENE TODOS LOS REGISTROS DE RECIBO DE CAJA ENTRE DOS FECHAS
	if($Registros=q("select rc.id,rc.fecha,o.sigla,o.centro_costos_uno,o.nombre as nofic,rc.consecutivo,concat(cl.nombre,' ',cl.apellido) as ncliente,
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
			".($USUARIO==1?"<a class='info' style='cursor:pointer' onclick='modificar_sin_autor($R->autorizacion);'><img src='gifs/standar/Pencil.png' border='0' height='10'><span>Modificar Autorizaci�n</span></a> 
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
							foreach($ABancos as $idb => $nbanco) {echo "<option value='$idb' ".($idb==$R->banco_aoa?"selected":"").">$nbanco</option>";}
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
				<form action='zgenerador_contable.suno.php' target='Oculto_exportacionrc' method='POST' name='genplano' id='genplano'>
				<input type='hidden' name='Acc' value='exporta_rc_garantia_suno'>
				<input type='hidden' name='recibos'>
			</form><br>";
		if(inlist($USUARIO,'1,9')) echo "<a style='cursor:pointer' onclick='genera_planorc();'>Exportar Recibos de Caja a Contabilidad Sistema Uno</a>";
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

function exporta_rc_garantia_suno() // LA EXPORTACION SE INICIA DESDE EL PROGRAMA DE CARTERA E INVOCA ESTA RUTINA.
{
	global $recibos;
	html('EXPORTACION CONTABLE DE RECIBOS DE CAJA DE GARANTIAS A HELISA');
	error_reporting(E_ALL);
		
	include('Interfase.Sistemauno.class.php');
	$TDC=37;  // id del tipo de documento contable RECIBO DE CAJA
	$TDC = qo( "select * from tipo_doc_cont where id=$TDC" );
	$CFG=qo("select * from cfg_factura where activo=1");
	
	$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
	if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
	$DD1=fopen($DESTINO_PLANO1,'w+');
	
	$DESTINO_PLANO2 = 'planos/int2a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para terceros
	if(@is_file($DESTINO_PLANO2)) @unlink($DESTINO_PLANO2);
	$DD2=fopen($DESTINO_PLANO2,'w+');
		
	$Recibos=explode(';',$recibos);
	include('inc/link.php');
	$ConsecutivoG=0;
	foreach($Recibos as $indice => $consec)
	{
		if($consec)
		{
			$Partes=explode(',',$consec);
			//$td_contable='RC';
			$consec_contable=$Partes[1];
			$id=$Partes[0];
			$RC=qom("select * from recibo_caja where id=$id",$LINK);
			$Oficina=qom("select * from oficina where id=$RC->oficina",$LINK);
			$td_contable=$Oficina->te_contable_uno;
			mysql_query("update recibo_caja set td_contable='$td_contable', consec_contable='$consec_contable' where id='$id' ",$LINK);
			echo "<br>$id: Tipo documento: $td_contable  Consecutivo: $consec_contable";
			$RC=qom("select * from recibo_caja where id=$id",$LINK);
			$Cliente=qom("select * from cliente where id=$RC->cliente",$LINK);
			$Banco=qom("select * from banco_aoa where id=$RC->banco_aoa",$LINK);
			$Siniestro=qom("select numero from siniestro where id=$RC->siniestro",$LINK);
			$Concepto="GARANTIA SN.$Siniestro->numero OF.$Oficina->sigla RC.$RC->consecutivo";
			$Centro_costos=$Oficina->centro_costos_uno;
			
			//////////   INTERFASE DE TERCEROS para importar en el m�dulo de Terceros de Helisa.
			
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
			$T->Centro_operacion=$Oficina->centro_operacion;
			$T->Zona='01    ';
			$T->Indicador_liquidacion_impuesto='1';
			$T->Codigo_rete_otro='01';
			$T->Codigo_condicion_pago='0 ';
			$T->Fecha_creacion=date('Ymd');
			fwrite($DD2,$T->genera());
			
			// ###################### INICIO DE LA GENERACION DELOS ASIENTOS ########################
			
			// -------------------------------------- PRIMER ASIENTO: CREDITO A PASIVO VALORES RECIBIDOS PARA TERCEROS
			$ConsecutivoG++;
			$Descripcion= ($RC->anulado?'ANULADO: ':'').$Concepto;
			$Valor=($RC->anulado?1:$RC->valor);
			$Fecha_doc=date('Ymd',strtotime($RC->fecha));
			///
			$I = new Interfase_documento();
			$I->Consecutivo_grabacion=$ConsecutivoG;
			$I->Centro_operacion=$Oficina->centro_operacion;
			$I->Tipo_documento=$td_contable;
			$I->Numero_documento=$RC->consecutivo;
			$I->Fecha_documento=$Fecha_doc;
			$I->Tercero=$Cliente->identificacion;
			$I->Indicador_anulacion=($RC->anulado==1?'1':' ');
			$I->Valor_documento=($RC->anulado?0:$RC->valor);
			$I->Cuenta_contable=$Oficina->cta_pasivo_garantias;
			// $I->Centro_operacion_mov=$Oficina->centro_operacion;
			$I->Centro_operacion_mov='001';
			$I->Tercero_mov=$Cliente->identificacion;
			$I->Detalle1= l($Descripcion,40);
			$I->Detalle2= substr($Descripcion,40);
			$I->DC='C';
			$I->Valor=$Valor;
			$I->Centro_costos=$Centro_costos;
			$I->Detalle1_doc=l($Descripcion,60);
			$I->Indicador_contab_libro2='2';
			$I->Valor_transaccion_libro2=$Valor;
			$I->Valor_transaccion_libro3=$Valor;
			
			fwrite( $DD1,$I->genera());
			
			// --------------------------------------  SEGUNDO ASIENTO: DEBITO A CAJA
			$ConsecutivoG++;
			///
			$I = new Interfase_documento();
			$I->Consecutivo_grabacion=$ConsecutivoG;
			$I->Centro_operacion=$Oficina->centro_operacion;
			$I->Tipo_documento=$td_contable;
			$I->Numero_documento=$RC->consecutivo;
			$I->Fecha_documento=$Fecha_doc;
			$I->Tercero=$CFG->nit_empresa;
			$I->Indicador_anulacion=($RC->anulado==1?'1':' ');
			$I->Valor_documento=($RC->anulado?0:$RC->valor);
			$I->Cuenta_contable=$Oficina->cuenta_caja_garantia;
			$I->Concepto_flujo_caja='010103  ';
			// $I->Centro_operacion_mov=$Oficina->centro_operacion;
			$I->Centro_operacion_mov='001';
			$I->Tercero_mov=$CFG->nit_empresa;
			$I->Detalle1= l($Descripcion,40);
			$I->Detalle2= substr($Descripcion,40);
			$I->DC='D';
			$I->Valor=$Valor;
			//$I->Centro_costos=$Centro_costos;
			$I->Detalle1_doc=l($Descripcion,60);
			$I->Indicador_contab_libro2='2';
			$I->Valor_transaccion_libro2=$Valor;
			$I->Valor_transaccion_libro3=$Valor;
			/// DATOS OBLIGATORIOS PARA CUENTAS DE CAJA
			$I->Codigo_caja='011';
			$I->Indicador_modo='1';
			$I->Medio_pago='001';
			fwrite( $DD1,$I->genera());
			
			// -------------------------------------------  TERCER ASIENTO  CAJA AL CREDITO
			$ConsecutivoG++;
			///
			$I = new Interfase_documento();
			$I->Consecutivo_grabacion=$ConsecutivoG;
			$I->Centro_operacion=$Oficina->centro_operacion;
			$I->Tipo_documento=$td_contable;
			$I->Numero_documento=$RC->consecutivo;
			$I->Fecha_documento=$Fecha_doc;
			$I->Tercero=$CFG->nit_empresa;
			$I->Indicador_anulacion=($RC->anulado==1?'1':' ');
			$I->Valor_documento=($RC->anulado?0:$RC->valor);
			$I->Cuenta_contable=$Oficina->cuenta_caja_garantia;
			$I->Concepto_flujo_caja='010103  ';
			// $I->Centro_operacion_mov=$Oficina->centro_operacion;
			$I->Centro_operacion_mov='001';
			$I->Tercero_mov=$CFG->nit_empresa;
			$I->Detalle1= l($Descripcion,40);
			$I->Detalle2= substr($Descripcion,40);
			$I->DC='C';
			$I->Valor=$Valor;
			//$I->Centro_costos=$Centro_costos;
			$I->Detalle1_doc=l($Descripcion,60);
			$I->Indicador_contab_libro2='2';
			$I->Valor_transaccion_libro2=$Valor;
			$I->Valor_transaccion_libro3=$Valor;
			/// DATOS OBLIGATORIOS PARA CUENTAS DE CAJA
			$I->Codigo_caja='011';
			$I->Indicador_modo='1';
			$I->Medio_pago='001';
			fwrite( $DD1,$I->genera());
			
			// ----------------------------------------CUARTO ASIENTO DEL BANCO AL DEBITO
			$ConsecutivoG++;
			///
			$I = new Interfase_documento();
			$I->Consecutivo_grabacion=$ConsecutivoG;
			$I->Centro_operacion=$Oficina->centro_operacion;
			$I->Tipo_documento=$td_contable;
			$I->Numero_documento=$RC->consecutivo;
			$I->Fecha_documento=$Fecha_doc;
			$I->Tercero=$CFG->nit_empresa;
			$I->Indicador_anulacion=($RC->anulado==1?'1':' ');
			$I->Valor_documento=($RC->anulado?0:$RC->valor);
			$I->Cuenta_contable=$Banco->cuenta_contable_uno;
			$I->Centro_operacion_mov='001';
			$I->Tercero_mov=$CFG->nit_empresa;
			$I->Detalle1= l($Descripcion,40);
			$I->Detalle2= substr($Descripcion,40);
			$I->DC='D';
			$I->Valor=$Valor;
			//$I->Centro_costos=$Centro_costos;
			$I->Detalle1_doc=l($Descripcion,60);
			$I->Indicador_contab_libro2='2';
			$I->Valor_transaccion_libro2=$Valor;
			$I->Valor_transaccion_libro3=$Valor;
			$I->Numero_cuenta=$Banco->cuenta;
			fwrite( $DD1,$I->genera());
		}
	}
	mysql_close($LINK);
	echo "<br>";
	echo " Fin proceso de detalle. ";
	fclose( $DD1 );fclose( $DD2 );
	// DESCARGA LOS ARCHIVOS PLANOS
	echo "<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		<iframe name='Oculto2' style='visibility:hidden' height='1' width='1'></iframe>
		<script language='javascript'>
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=FIBATCH.TXT','Oculto1');
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO2&Salida=TERCEROS.TXT','Oculto2');
		</script>
		</body>";
}

function exportacion_notas_contables_uno()
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
	<body><h3>EXPORTACION DE NOTAS CONTABLES A SISTEMA UNO</h3>
	<form action='zgenerador_contable.suno.php' target='Tablero_exportacion_notas' method='POST' name='forma' id='forma'>
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
			<th>Autorizaci�n</th>
			<th>Valor</th>
			<th>Opciones</th>
			</tr>";
		while($R =mysql_fetch_object($Registros )) // pinta registro por registro con la opcion de marcarlos para exportar
		{
			echo "<tr>
			<td>$R->fecha</td>
			<td>$R->nc_consec ".($USUARIO==1?"<a class='info' style='cursor:pointer' onclick='modificar_nc($R->id);'><img src='gifs/standar/Pencil.png' border='0' height='10'>N<span>Modificar Nota</span></a> 
				<a class='info' style='cursor:pointer' onclick='modificar_siniestro($R->siniestro);'><img src='gifs/standar/Pencil.png' border='0' height='10'>S<span>Modificar Siniestro</span></a> 
				<a class='info' style='cursor:pointer' onclick='modificar_autorizacion($R->autorizacion);'><img src='gifs/standar/Pencil.png' border='0' height='10'>A<span>Modificar Autorizaci�n</span></a> 
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
				<form action='zgenerador_contable.suno.php' target='Oculto_exportacionnc' method='POST' name='genplano' id='genplano'>
				<input type='hidden' name='Acc' value='exporta_nota_contable_uno'>
				<input type='hidden' name='Notas'>
			</form><br>";
		if(inlist($USUARIO,'1,9')) echo "<a style='cursor:pointer' onclick='genera_planonc();'>Exportar Notas Contables a Contabilidad -> Sistema Uno</a>";
			echo "<br><br><br><br>";
	}
	else
	{
		echo "<b style='color:aa0000'>No hay Notas Contables en el rango de fechas dado.</b>";
	}
	echo "</body>";
}

function exporta_nota_contable_uno() // exporta notas contables
{
	global $Notas;
	html('EXPORTACION DE NOTAS CONTABLES A SISTEMA UNO');
	error_reporting(E_ALL);
	
	$TDC=19;  // id del tipo de documento contable NOTA CONTABLE
	$TDC = qo( "select * from tipo_doc_cont where id=$TDC" );
	$CFG=qo("select * from cfg_factura where activo=1");
	$TDCF=qo("select * from tipo_doc_cont where id=8" );
	$ConsecutivoG=0;
	include('Interfase.Sistemauno.class.php');
	
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
			
			$td_contable=$TDC->codigo_suno;
			$consec_contable=$Partes[1];
			$id=$Partes[0];
			mysql_query("update nota_contable set td_contable='$td_contable', cons_contable='$consec_contable' where id='$id' ",$LINK); // asigna el consecutivo contable a la nota
			echo "<br>$id: Tipo documento: $td_contable  Consecutivo: $consec_contable";
			$NC=qom("select * from nota_contable where id=$id",$LINK); // obtiene la infrormacion de la nota
			$Recibo=qom("select * from recibo_caja where id=$NC->recibo_caja",$LINK); // obtiene el recibo de caja
			$Cliente=qom("select * from cliente where id=$Recibo->cliente",$LINK); // obtiene el cliente
			$Oficina=qom("select * from oficina where id=$Recibo->oficina",$LINK); // obtiene la oficina
			$Centro_costos=$Oficina->centro_costos_uno;
			$Siniestro=qom("select numero from siniestro where id=$Recibo->siniestro",$LINK); // obtiene el numero de siniestro
			$Factura=qom("select * from factura where id=$NC->factura",$LINK); // obtiene la factura
			$Concepto="CRUCE SN.$Siniestro->numero RC.OF.$Oficina->sigla $Recibo->consecutivo vs FV.$Factura->consecutivo ";
			// ASIENTO CONTABLE DE LA OFICINA
			
			$ConsecutivoG++;
			$Descripcion= ($NC->anulado?'ANULADO: ':'').$Concepto;
			$Valor=($NC->anulado?0:$NC->valor);
			$Fecha_doc=date('Ymd',strtotime($NC->fecha));
			///
			$I = new Interfase_documento();
			$I->Consecutivo_grabacion=$ConsecutivoG;
			// $I->Centro_operacion=$Oficina->centro_operacion; Nov 5 2014 se solicito cambiar a 001 todas las notas contables
			$I->Centro_operacion='001';
			$I->Tipo_documento=$TDC->codigo_suno;
			$I->Numero_documento=$NC->cons_contable;
			$I->Fecha_documento=$Fecha_doc;
			$I->Tercero=$Cliente->identificacion;
			$I->Indicador_anulacion=($NC->anulado==1?'1':' ');
			$I->Valor_documento=$Valor;
			$I->Cuenta_contable=$Oficina->cta_pasivo_garantias;
			// $I->Centro_operacion_mov=$Oficina->centro_operacion;
			$I->Centro_operacion_mov='001';
			$I->Tercero_mov=$Cliente->identificacion;
			$I->Detalle1= l($Descripcion,40);
			$I->Detalle2= substr($Descripcion,40);
			$I->DC='D';
			$I->Valor=$Valor;
			$I->Centro_costos=$Centro_costos;
			$I->Detalle1_doc=l($Descripcion,60);
			$I->Indicador_contab_libro2='2';
			$I->Valor_transaccion_libro2=$Valor;
			$I->Valor_transaccion_libro3=$Valor;
			fwrite( $DD1,$I->genera());
			
			// ASIENTO CONTABLE DE CARTERA
			
			$ConsecutivoG++;
			///
			$I = new Interfase_documento();
			$I->Consecutivo_grabacion=$ConsecutivoG;
			// $I->Centro_operacion=$Oficina->centro_operacion; Nov 5 2014 se solicito cambiar a 001 todas las notas contables
			$I->Centro_operacion='001';
			$I->Tipo_documento=$TDC->codigo_suno;
			$I->Numero_documento=$NC->cons_contable;
			$I->Fecha_documento=$Fecha_doc;
			$I->Tercero=$Cliente->identificacion;
			$I->Indicador_anulacion=($NC->anulado==1?'1':' ');
			$I->Valor_documento=$Valor;
			$I->Cuenta_contable=$CFG->cuenta_activo_venta;
			// $I->Centro_operacion_mov=$Oficina->centro_operacion;
			$I->Centro_operacion_mov='001';
			$I->Tercero_mov=$Cliente->identificacion;
			$I->Detalle1= l($Descripcion,40);
			$I->Detalle2= substr($Descripcion,40);
			$I->DC='C';
			$I->Valor=$Valor;
			$I->Centro_costos=$Centro_costos;
			$I->Detalle1_doc=l($Descripcion,60);
			$I->Indicador_contab_libro2='2';
			$I->Valor_transaccion_libro2=$Valor;
			$I->Valor_transaccion_libro3=$Valor;
			$I->Tipo_doc_cruce=$TDCF->codigo_suno;
			$I->Numero_doc_cruce=$Factura->consecutivo;
			$I->Fecha_vencimiento_cruce=date('Ymd',strtotime($Factura->vecha_vencimiento));
			fwrite( $DD1,$I->genera());
		}
	}
	mysql_close($LINK);
	echo "<br>";
	echo " Fin proceso de detalle. ";
	fclose( $DD1 );
	echo "<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		<script language='javascript'>
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=FIBATCH.TXT','Oculto1');
		</script>
		</body>";
}

function exportacion_transferencias_garantia_uno() // formulario para exportacion de transferencias de garantias
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
	//  CONTRA BANCO (cuenta de garant�as 03170515431 11100505)   (CREDITO)  con el tercero de AOA
	
	echo "<script language='javascript'></script>
	<body><h3>EXPORTACION DE TRANSFERENCIAS DE GARANTIAS A SISTEMA UNO</h3>
	<form action='zgenerador_contable.suno.php' target='Tablero_exportacion_transferencias' method='POST' name='forma' id='forma'>
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
	<h3>EXPORTACION DE TRANSFERENCIAS DE GARANTIAS [ $FI - $FF ] A SISTEMA UNO</h3>
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
			<th>Im�genes</th>
			<th>Opciones</th>
			</tr>";
		include('inc/link.php');
		while($R =mysql_fetch_object($Registros )) // pinta las transferencias con la opcion de marcarlas para ser exportadas a la contabilidad
		{
			if($Consecutivo_rc=qom("select * from recibo_caja where autorizacion=$R->id",$LINK))
			{
				$Oficina=qom("select * from oficina where id='$Consecutivo_rc->oficina' ",$LINK);
				echo "<tr>
				<td>$R->fecha_devolucion</td>
				<td>$R->numsin ".($USUARIO==1?" <a class='info' style='cursor:pointer' onclick='modificar_siniestro($R->siniestro);'><img src='gifs/standar/Pencil.png' border='0' height='10'>S<span>Modificar Siniestro</span></a> 
								<a class='info' style='cursor:pointer' onclick='modificar_autorizacion($R->id);'><img src='gifs/standar/Pencil.png' border='0' height='10'>A<span>Modificar Autorizacion</span></a>":"")."</td>
				<td>".($R->consignacion_f?"<a class='info' style='cursor:pointer' onclick=\"modal('$R->consignacion_f',0,0,500,500,'devol');\"><img src='gifs/standar/Preview.png' border='0'><span>Ver Consignaci�n</span></a>":"").
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
			else
			{
				echo "<tr>
				<td>$R->fecha_devolucion</td>
				<td>$R->numsin ".($USUARIO==1?" <a class='info' style='cursor:pointer' onclick='modificar_siniestro($R->siniestro);'><img src='gifs/standar/Pencil.png' border='0' height='10'>S<span>Modificar Siniestro</span></a> 
								<a class='info' style='cursor:pointer' onclick='modificar_autorizacion($R->id);'><img src='gifs/standar/Pencil.png' border='0' height='10'>A<span>Modificar Autorizacion</span></a>":"")."</td>
				<td>".($R->consignacion_f?"<a class='info' style='cursor:pointer' onclick=\"modal('$R->consignacion_f',0,0,500,500,'devol');\"><img src='gifs/standar/Preview.png' border='0'><span>Ver Consignaci�n</span></a>":"").
				"SIN CONSECUTIVO RC</td>
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
		}
		mysql_close($LINK);
		echo "</table><br>
				<form action='zgenerador_contable.suno.php' target='Oculto_exportaciontg' method='POST' name='genplano' id='genplano'>
				<input type='hidden' name='Acc' value='exporta_transferencia_uno'>
				<input type='hidden' name='Transferencias'>
			</form><br>";
		if(inlist($USUARIO,'1,9')) echo "<a style='cursor:pointer' onclick='genera_planonc();'>Exportar Transferencias de Garantias a Contabilidad -> Sistema Uno</a>";
			echo "<br><br><br><br>";
	}
	else
	{
		echo "<b style='color:aa0000'>No hay Transferencias de Garant�as en el rango de fechas dado.</b>";
	}
	echo "</body>";
}

function exporta_transferencia_uno() // exporta las transferencias de pagos de garantias
{
	global $Transferencias;
	html('EXPORTACION DE TRANSFERENCIAS A HELISA');
	error_reporting(E_ALL);
	$TDC=38;  // id del tipo de documento contable NOTA CONTABLE
	$TDC = qo( "select * from tipo_doc_cont where id=$TDC" );
	$CFG=qo("select * from cfg_factura where activo=1");
	$ConsecutivoG=0;
	include('Interfase.Sistemauno.class.php');
	
	$DESTINO_PLANO1 = 'planos/int1a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
	if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
	$DD1=fopen($DESTINO_PLANO1,'w+');
	
	$DESTINO_PLANO2 = 'planos/int2a_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para terceros
	if(@is_file($DESTINO_PLANO2)) @unlink($DESTINO_PLANO2);
	$DD2=fopen($DESTINO_PLANO2,'w+');
		
	echo "<body>$Transferencias";
	$Transf=explode(';',$Transferencias);
	include('inc/link.php');
	foreach($Transf as $indice => $consec)
	{
		if($consec)
		{
			$Partes=explode(',',$consec);
			$td_contable=$TDC->codigo_suno;
			$consec_contable=$Partes[1];
			$id=$Partes[0];
			mysql_query("update sin_autor set td_contable='$td_contable', cons_contable='$consec_contable' where id='$id' ",$LINK);
			echo "<br>$id: Tipo documento: $td_contable  Consecutivo: $consec_contable";
			$TR=qom("select * from sin_autor where id=$id",$LINK);
			$Siniestro=qom("select numero,ciudad from siniestro where id=$TR->siniestro",$LINK);
			$Oficina=qom("select * from oficina where ciudad='$Siniestro->ciudad' ",$LINK);
			$Centro_costos=$Oficina->centro_costos_uno;
			$Cons_rc=qom("select consecutivo,cliente from recibo_caja where autorizacion=$TR->id",$LINK);
			$Descripcion="TRANSF. GARANTIA SN.$Siniestro->numero OF.$Oficina->sigla $Cons_rc->consecutivo";
			$Cliente=qom("select * from cliente where id=$Cons_rc->cliente",$LINK);
			
			//////////   INTERFASE DE TERCEROS para importar en el m�dulo de Terceros de Helisa.
			
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
			$T->Centro_operacion=$Oficina->centro_operacion;
			$T->Zona='01    ';
			$T->Indicador_liquidacion_impuesto='1';
			$T->Codigo_rete_otro='01';
			$T->Codigo_condicion_pago='0 ';
			$T->Fecha_creacion=date('Ymd');
			fwrite($DD2,$T->genera());
			
		// ASIENTO CONTABLE DE LA CUENTA DE TRANSFERENCIAS
			$ConsecutivoG++;
			$Fecha_doc=date('Ymd',strtotime($TR->fecha_devolucion));
			///
			$I = new Interfase_documento();
			$I->Consecutivo_grabacion=$ConsecutivoG;
			$I->Centro_operacion=$Oficina->centro_operacion;
			$I->Tipo_documento=$TDC->codigo_suno;
			$I->Numero_documento=$TR->cons_contable;
			$I->Fecha_documento=$Fecha_doc;
			$I->Tercero=$Cliente->identificacion;
			$I->Valor_documento=$TR->valor_devolucion;
			$I->Cuenta_contable=$Oficina->cta_pasivo_garantias;
			// $I->Centro_operacion_mov=$Oficina->centro_operacion;
			$I->Centro_operacion_mov='001';
			$I->Tercero_mov=$Cliente->identificacion;
			$I->Detalle1= l($Descripcion,40);
			$I->Detalle2= substr($Descripcion,40);
			$I->DC='D';
			$I->Valor=$TR->valor_devolucion;
			$I->Centro_costos=$Centro_costos;
			$I->Detalle1_doc=l($Descripcion,60);
			$I->Indicador_contab_libro2='2';
			$I->Valor_transaccion_libro2=$TR->valor_devolucion;
			$I->Valor_transaccion_libro3=$TR->valor_devolucion;
			fwrite( $DD1,$I->genera());
		
			// ASIENTO CONTABLE DE LA CUENTA DEL BANCO
			$ConsecutivoG++;
			$I = new Interfase_documento();
			$I->Consecutivo_grabacion=$ConsecutivoG;
			$I->Centro_operacion=$Oficina->centro_operacion;
			$I->Tipo_documento=$TDC->codigo_suno;
			$I->Numero_documento=$TR->cons_contable;
			$I->Fecha_documento=$Fecha_doc;
			$I->Tercero=$Cliente->identificacion;
			$I->Valor_documento=$TR->valor_devolucion;
			$I->Cuenta_contable=$CFG->cta_contable_bcotg;
			$I->Centro_operacion_mov='001';
			$I->Tercero_mov=$Cliente->identificacion;
			$I->Detalle1= l($Descripcion,40);
			$I->Detalle2= substr($Descripcion,40);
			$I->DC='C';
			$I->Valor=$TR->valor_devolucion;
			$I->Centro_costos=$Centro_costos;
			$I->Detalle1_doc=l($Descripcion,60);
			$I->Indicador_contab_libro2='2';
			$I->Valor_transaccion_libro2=$TR->valor_devolucion;
			$I->Valor_transaccion_libro3=$TR->valor_devolucion;
			$I->Numero_cuenta=$CFG->cuenta_banco_transfg;
			fwrite( $DD1,$I->genera());
		}
	}
	mysql_close($LINK);
	echo "<br>";
	echo " Fin proceso de detalle. ";
	fclose( $DD1 );fclose( $DD2 );
	// descarga los archivos planos
	echo "<iframe name='Oculto1' style='visibility:hidden' height='1' width='1'></iframe>
		<iframe name='Oculto2' style='visibility:hidden' height='1' width='1'></iframe>
		<script language='javascript'>
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=FIBATCH.TXT','Oculto1');
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO2&Salida=TERCEROS.TXT','Oculto2');
		</script>
		</body>";
}

function preparacion_menus()
{
	q("update usuario_tab set tipo='INTERFASES HELISA' where tipo='INTERFASES CONTABLES' ");
	q("insert into usuario_tab (usuario,tipo,descripcion,destino,tabla) values (1,'INTERFASES S.UNO','Exportaci�n de Facturas (Cuentas x Cobrar)','destino','zgenerador_contable.suno.php?Acc=exportar_factura_uno')");
	q("insert into usuario_tab (usuario,tipo,descripcion,destino,tabla) values (1,'INTERFASES S.UNO','Exportacion de Garantias de Servicio','destino','zgenerador_contable.suno.php?Acc=exportacion_garantias_uno')");
	q("insert into usuario_tab (usuario,tipo,descripcion,destino,tabla) values (1,'INTERFASES S.UNO','Exportacion de Notas Contables','destino','zgenerador_contable.suno.php?Acc=exportacion_notas_contables_uno')");
	q("insert into usuario_tab (usuario,tipo,descripcion,destino,tabla) values (1,'INTERFASES S.UNO','Exportacion de Transferencias Devol. Garantias','destino','zgenerador_contable.suno.php?Acc=exportacion_transferencias_garantia_uno')");
	
}





?>
