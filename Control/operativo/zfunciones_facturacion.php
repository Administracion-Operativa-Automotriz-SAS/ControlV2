<?php

// FUNCIONES FACTURACION
include_once('inc/funciones_.php');
if (!empty($Acc) && function_exists($Acc)) { eval($Acc . '();'); die(); }

function imprimir_factura()
{
	include('inc/gpos.php');
	//global $consecutivo,$id,$USUARIO,$app;
	//if($USUARIO!=1) die('MODULO EN MANTENIMIENTO');

	if($app )
	{
		$Archivo=directorio_imagen('factura_pdf',$id)."factura$id.pdf";
		if(is_file($Archivo))
		{
			if($vista) header("location:$Archivo");
			die();
		}
	}

	if($id)
	{		$D=qo("select * from factura where id=$id");}
	elseif($consecutivo) {$consecutivo = trim($consecutivo);$D=qo("select * from factura where consecutivo='$consecutivo'");}

	
	/*Al comentiariar este codigo inferior el sistema facturara de la manera Antigua*/
	if(!is_numeric($D->consecutivo)){
		
		header("Location: /Control/operativo/zfacturacion.php?Acc=verificar_factura_electronica&id=".$D->id);
		
		exit;
		
	}

	  
	/*Depende de resolucion de facturacion*/
	
	//echo "select * from resolucion_factura where '$D->consecutivo' between consecutivo_inicial and consecutivo_final";
	$Resol=qo("select * from resolucion_factura order by fecha desc limit 1");
	$Resolucion="-Resolución de Facturación DIAN No. $Resol->numero de ".mes(date('m',strtotime($Resol->fecha)))." ".date('d',strtotime($Resol->fecha))." de ".date('Y',strtotime($Resol->fecha))." del $Resol->consecutivo_inicial al $Resol->consecutivo_final Autoriza.";
	$FirmaFac=qo1("select firma_f from cfg_factura where activo=1");
	$Firma_recibido=directorio_imagen('factura_pdf',$id).'firma_recibido_factura.png';
	if($D)
	{

		$Sin=qo("select numero,ciudad from siniestro where id=$D->siniestro");
		$OrdenServicio=qo1("select left(nombre,2) from aseguradora where id=$D->aseguradora").'-'.$Sin->numero;
		$Ciudad_sin=qo1("select left(nombre,40) from ciudad where codigo='$Sin->ciudad' ");
		$Cliente=qo("select * from cliente where id=$D->cliente");
		if($D->impresion_resumida) $Qd="select (1) as cantidad, sum(cantidad*unitario) as unitario,(' ') as desripcion,($D->iva) as iva,t_concepto_fac($D->concepto_resumida) as nconcepto from facturad where factura=$D->id";
		else $Qd="select *,t_concepto_fac(concepto) as nconcepto from facturad where factura=$D->id";
		$Det=q($Qd);

		include('inc/pdf/fpdf.php');
		$P=new pdf('P','mm','letter');
		$P->AddFont("c128a","","c128a.php");
		$P->AliasNbPages();
		$P->setTitle("ORDEN DE SERVICIO");
		$P->setAuthor("Arturo Quintero www.aoacolombia.com arturoquintero@aoacolombia.com");
		$P->Numeracion=false;
		$P->SetAutoPageBreak(false);
		$P->setFillColor(240,240,240);
		//	$P->Header_texto='';
		//	$P->Header_alineacion='L';
		//	$P->Header_alto='8';
		$P->SetTopMargin('5');
		//	$P->Header_colores=array(0,0,0,255,255,255,50,50,100); # rgb texto, rgb fondo, rgb borde
		//	$P->Header_imagen='img/cnota_entrada.jpg';
		///	$P->Header_posicion_imagen=array(20,5,80,14);

		/////////////////////////////////////////     PRIMERA PAGINA: ORIGINAL //////////////////////////////////////////////////////////////////////

		$P->AddPage('P');
		$P->Image('../img/LOGO_AOA_200.jpg',20,5,60,24);
		if($FirmaFac) $P->image($FirmaFac,30,220,50,30);
		//
		$P->image( 'img/lateral_factura_aoa.jpg', 7.5, 130, 2, 40 );
		//$P->image( 'img/lateral_resolucion_factura_aoa.jpg', 210.5, 100, 2, 100 );
		$P->setfont('Arial','B',10);
		if($D->anulada)
		{
			$P->image('gifs/ANULADO2.jpg',40,60,120,150);
		}
		elseif(!$D->autorizadopor)
		{
			$P->image('gifs/SINAPROBACION.jpg',40,60,120,150);
		}
		$P->SetXY(100,5);
		$P->SetTextColor(0,0,0);
		$P->Cell(90,5,'ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S.',0,0,'C');
		$P->setxy(100,9);
		$P->Cell(90,5,'NIT.: 900.174.552-5 - I.V.A. RÉGIMEN COMÚN',0,0,'C');
		$P->setfont('Arial','',8);
		$P->setxy(100,14);
		$P->Cell(90,5,'ACTIVIDADES ECONÓMICAS No. 7710 Y 7020',0,0,'C');
		$P->setxy(100,17);
		$P->Cell(90,5,'GRANDES CONTRIBUYENTES-RESOLUCION 000076 1 DIC 2016',0,0,'C');
		$P->setxy(100,21);
		$P->Cell(90,5,'SOMOS GRANDES CONTRIBUYENTES IMPUESTOS DISTRITALES BOGOTA',0,0,'C');
		$P->SETXY(100,24);
		$P->CELL(90,5,'según Resolución No. DDI-010761',0,0,'C');
		$P->SETXY(100,27);
		$P->CELL(90,5,'SHD del 30 de Marzo de 2016 TALONARIO POR CONTINGENCIA FE',0,0,'C');
		$P->SETXY(100,30);
		$P->CELL(90,5,$Resolucion,0,0,'C');
		$P->SETFONT('Arial','B',8);
		$P->SETXY(10,37);	$P->CELL(20,4,'FECHA DE EMISION:');
		$P->SETXY(87,37);	$P->CELL(40,4,'FEC.VENCIMIENTO:',0,0,'R');
		$P->SETXY(10,45);	$P->CELL(20,4,'CLIENTE:');
		$P->SETXY(87,45);   $P->CELL(40,4,'NIT/CC:',0,0,'R');
		$P->SETXY(10,53);	$P->CELL(20,4,'DIRECCION:');
		$P->SETXY(87,53);	$P->CELL(40,4,'TELEFONO:',0,0,'R');
		$P->SETFONT('Arial','B',10);
		$P->SETXY(170,34);$P->CELL(40,6,'FACTURA DE VENTA',0,0,'C',1);
		$P->SETXY(170,45);$P->cell(40,7,'Orden de Servicio No.',0,0,'L',1);
		$P->SETFONT('Times','B',12);
		$P->settextColor(50,50,50);
		$P->setxy(170,39); $P->Cell(40,6,'No.   '.str_pad($D->consecutivo,6,' ',STR_PAD_LEFT),0,0,'C',1);
		$P->settextcolor(0,0,0);
		$P->setfont('Arial','',8);
		if($Ciudad_sin) {$P->setxy(170,52);$P->cell(40,7,$OrdenServicio,0,0,'C',1);}
		$P->setxy(40,37);$P->cell(40,4,fecha_completa($D->fecha_emision));
		$P->setxy(126,37);$P->cell(40,4,fecha_completa($D->fecha_vencimiento));
		$P->setxy(24,45);$P->cell(40,4,$Cliente->nombre.' '.$Cliente->apellido);
		$P->setxy(126,45);$P->cell(40,4,$Cliente->identificacion);
		$P->setxy(28,53);$P->cell(40,4,$Cliente->direccion);
		$P->setxy(126,53);$P->cell(40,4,$Cliente->celular.' '.$Cliente->telefono_casa);
		$P->line(170,34,170,59);
		$P->line(10,43,170,43);
		$P->line(10,51,170,51);
		$P->line(170,45,210,45);
		$P->rect(10,34,200,25);
		$P->settextcolor(255,255,255);
		$P->setfillcolor(100,100,100);
		$P->setfont('Arial','B',10);
		$P->setxy(10,61);$P->cell(149,5,'DETALLE',1,0,'C',1);
		$P->setxy(160,61);$P->cell(50,5,'VALOR',1,0,'C',1);
		$Y=70;
		$P->settextcolor(0,0,0);
		$P->setfillcolor(240,240,240);
		$P->setfont('Arial','',9);
		$Base_Iva=0;
		if($Det)
			while($I=mysql_fetch_object($Det))
			{
				if($I->iva) $Base_Iva+=$I->cantidad*$I->unitario;
				$P->setxy(170,$Y);$P->cell(30,4,'$ '.coma_formatd($I->cantidad*$I->unitario,2),0,0,'R');
				$P->setxy(15,$Y);
				$P->multicell(140,4,$I->nconcepto.' '.$I->descripcion);
				$Y=$P->y;
			}
		$P->setfont('Arial','',10);
		$Y+=5;
		if($Ciudad_sin)
		{
			$P->setxy(15,$Y);$P->cell(100,5,"$Ciudad_sin",0,0,'L');
		}
		$Y=190;
		if(is_file($Firma_recibido)) {$P->image($Firma_recibido,120,215,80,30);} else {$P->setxy(110,$Y+28);$P->cell(100,36,' ',0,0,'C',1);}
		$P->rect(10,67,200,$Y-41);
		$P->rect(10,$Y+28,200,36);
		$P->line(110,$Y+28,110,$Y+64);
		$P->line(114,$Y+50,206,$Y+50);
		$P->line(15,$Y+54,105,$Y+54);
		if($D->garantia)
		{
			$Autorizacion=qo("select * from sin_autor where id=$D->garantia");
			if($RCP=qo("select * from recibo_caja_prov where autorizacion=$D->garantia"))
			{
				$Sigla=qo1("select sigla from oficina where id=$RCP->oficina");
				$Nota="Esta factura es CANCELADA contra la Garantía correspondiente al Recibo de Caja Provisinal Número $Sigla-$RCP->consecutivo";
			}
			elseif($RC=qo("select * from recibo_caja where autorizacion=$D->garantia"))
			{
				$Noficina=qo1("select nombre from oficina where id=$RC->oficina");
				$Nota="Esta factura es CANCELADA contra la Garantía correspondiente al Recibo de Caja Número $RC->consecutivo de $Noficina";
			}
			$P->setxy(15,$Y-5);$P->multicell(120,5,$Nota);
		}
		if($D->comentario_factura) {$P->setxy(15,$Y-5); $P->multicell(120,5,$D->comentario_factura,0,'J');}
		$P->setxy(150,$Y-5);$P->cell(20,5,'SUBTOTAL');
		$P->setxy(170,$Y-5);$P->cell(30,5,'$ '.coma_formatd($D->subtotal,2),0,0,'R');
		$P->setxy(150,$Y);$P->cell(20,5,'BASE IVA');
		$P->setxy(170,$Y);$P->cell(30,5,'$ '.coma_formatd($Base_Iva,2),0,0,'R');
		$P->setxy(150,$Y+5);$P->cell(20,5,'IVA');
		$P->setxy(170,$Y+5);$P->cell(30,5,'$ '.coma_formatd($D->iva,2),0,0,'R');
		$P->setxy(150,$Y+10);$P->cell(20,5,'TOTAL');
		$P->setfont('Arial','B',10);
		$P->setxy(170,$Y+10);$P->cell(30,5,'$ '.coma_formatd($D->total,2),0,0,'R');
		$P->setfont('Arial','',10);
		$P->setxy(15,$Y+15);$P->multicell(185,5,'EN LETRAS: '.enletras($D->total,1),0,'J');
		$P->setfont('Arial','B',6);
		$P->setxy(13,$Y+28);$P->cell(30,4,'ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S.');
		$P->setxy(13,$Y+54);$P->cell(90,4,'FIRMA Y SELLO AUTORIZADOS',0,0,'C');
		$P->setxy(15,$Y+58);$P->cell(90,4,'Esta Factura de Venta es un Título Valor de conformidad con lo establecido en',0,0,'C');
		$P->setxy(15,$Y+60);$P->cell(90,4,'la Ley 1231 de julio 17 de 2008 y demás normas que lo complementan.',0,0,'C');
		$P->setxy(110,$Y+50);$P->cell(90,4,'FIRMA Y SELLO DEL COMPRADOR',0,0,'C');
		$P->setfont('Arial','',6);
		$P->setxy(110,$Y+54);$P->cell(90,4,'NOMBRE DE QUIEN RECIBE:');
		$P->setxy(110,$Y+57);$P->cell(90,4,'DOCUMENTO DE IDENTIDAD:');
		$P->setxy(110,$Y+60);$P->cell(90,4,'FECHA DE RECIBIDO:');
		$P->setfont('Arial','B',8);
		$P->setxy(100,$Y+65);$P->cell(20,4,'CARRERA 69B No. 98A-10 BARRIO MORATO PBX: (571) 756 05 10 - FAX (571) 756 05 12',0,0,'C');
		$P->setxy(100,$Y+70);$P->cell(20,4,'www.aoacolombia.com - Bogotá, D.C. - Colombia',0,0,'C');
		$P->setxy(100,$Y+75);$P->cell(20,4,'ORIGINAL',0,0,'C');

		/////////////////////////////////////////     SEGUNDA PAGINA: CLIENTE //////////////////////////////////////////////////////////////////////

		$P->AddPage('P');
		$P->Image('../img/LOGO_AOA_200.jpg',20,5,60,24);
		if($FirmaFac) $P->image($FirmaFac,30,220,50,30);
		//if(is_file($Firma_recibido)) {$P->image($Firma_recibido,120,215,80,30);} else {$P->setxy(110,$Y+28);$P->cell(100,36,' ',0,0,'C',1);}
		$P->image( 'img/lateral_factura_aoa.jpg', 7.5, 130, 2, 40 );
		//$P->image( 'img/lateral_resolucion_factura_aoa.jpg', 210.5, 100, 2, 100 );
		$P->setfont('Arial','B',10);
		if($D->anulada)
		{
			$P->image('gifs/ANULADO2.jpg',40,60,120,150);
		}
		elseif(!$D->autorizadopor)
		{
			$P->image('gifs/SINAPROBACION.jpg',40,60,120,150);
		}
		$P->SetXY(100,5);
		$P->SetTextColor(0,0,0);
		$P->Cell(90,5,'ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S.',0,0,'C');
		$P->setxy(100,9);
		$P->Cell(90,5,'NIT.: 900.174.552-5 - I.V.A. RÉGIMEN COMÚN',0,0,'C');
		$P->setfont('Arial','',8);
		$P->setxy(100,14);
		$P->Cell(90,5,'ACTIVIADES ECONÓMICAS No. 7414 Y 7111',0,0,'C');
		$P->setxy(100,17);
		$P->Cell(90,5,'GRANDES CONTRIBUYENTES-RESOLUCION 000076 1 DIC 2016',0,0,'C');
		$P->setxy(100,21);
		$P->Cell(90,5,'SOMOS GRANDES CONTRIBUYENTES IMPUESTOS DISTRITALES BOGOTA',0,0,'C');
		$P->SETXY(100,24);
		$P->CELL(90,5,'según Resolución No. DDI-010761',0,0,'C');
		$P->SETXY(100,27);
		$P->CELL(90,5,'SHD del 30 de Marzo de 2016 TALONARIO POR CONTINGENCIA FE',0,0,'C');
		$P->SETXY(100,30);
		$P->CELL(90,5,$Resolucion,0,0,'C');
		$P->SETFONT('Arial','B',8);
		$P->SETXY(10,37);	$P->CELL(20,4,'FECHA DE EMISION:');
		$P->SETXY(87,37);	$P->CELL(40,4,'FEC.VENCIMIENTO:',0,0,'R');
		$P->SETXY(10,45);	$P->CELL(20,4,'CLIENTE:');
		$P->SETXY(87,45);   $P->CELL(40,4,'NIT/CC:',0,0,'R');
		$P->SETXY(10,53);	$P->CELL(20,4,'DIRECCION:');
		$P->SETXY(87,53);	$P->CELL(40,4,'TELEFONO:',0,0,'R');
		$P->SETFONT('Arial','B',10);
		$P->SETXY(170,34);$P->CELL(40,6,'FACTURA DE VENTA',0,0,'C',1);
		$P->SETXY(170,45);$P->cell(40,7,'Orden de Servicio No.',0,0,'L',1);
		$P->SETFONT('Times','B',12);
		$P->settextColor(50,50,50);
		$P->setxy(170,39); $P->Cell(40,6,'No.   '.str_pad($D->consecutivo,6,' ',STR_PAD_LEFT),0,0,'C',1);
		$P->settextcolor(0,0,0);
		$P->setfont('Arial','',8);
		if($Ciudad_sin) {$P->setxy(170,52);$P->cell(40,7,$OrdenServicio,0,0,'C',1);}
		$P->setxy(40,37);$P->cell(40,4,fecha_completa($D->fecha_emision));
		$P->setxy(126,37);$P->cell(40,4,fecha_completa($D->fecha_vencimiento));
		$P->setxy(24,45);$P->cell(40,4,$Cliente->nombre.' '.$Cliente->apellido);
		$P->setxy(126,45);$P->cell(40,4,$Cliente->identificacion);
		$P->setxy(28,53);$P->cell(40,4,$Cliente->direccion);
		$P->setxy(126,53);$P->cell(40,4,$Cliente->celular.' '.$Cliente->telefono_casa);
		$P->line(170,34,170,59);
		$P->line(10,43,170,43);
		$P->line(10,51,170,51);
		$P->line(170,45,210,45);
		$P->rect(10,34,200,25);
		$P->settextcolor(255,255,255);
		$P->setfillcolor(100,100,100);
		$P->setfont('Arial','B',10);
		$P->setxy(10,61);$P->cell(149,5,'DETALLE',1,0,'C',1);
		$P->setxy(160,61);$P->cell(50,5,'VALOR',1,0,'C',1);
		$Y=70;
		$P->settextcolor(0,0,0);
		$P->setfillcolor(240,240,240);
		$P->setfont('Arial','',10);
		$Base_Iva=0;
		$P->setfont('Arial','',9);
		if($Det)
		{
			mysql_data_seek($Det, 0);
			while($I=mysql_fetch_object($Det))
			{
				if($I->iva) $Base_Iva+=$I->cantidad*$I->unitario;
				$P->setxy(170,$Y);$P->cell(30,5,'$ '.coma_formatd($I->cantidad*$I->unitario,2),0,0,'R');
				$P->setxy(15,$Y);
				$P->multicell(140,4,$I->nconcepto.' '.$I->descripcion);
				$Y=$P->y;
			}
		}
		$P->setfont('Arial','',10);
		$Y+=5;
		if($Ciudad_sin)
		{
			$P->setxy(15,$Y);$P->cell(100,5,"$Ciudad_sin",0,0,'L');
		}
		$Y=190;
		if(is_file($Firma_recibido)) {$P->image($Firma_recibido,120,215,80,30);} else {$P->setxy(110,$Y+28);$P->cell(100,36,' ',0,0,'C',1);}
		$P->rect(10,67,200,$Y-41);
		$P->rect(10,$Y+28,200,36);
		$P->line(110,$Y+28,110,$Y+64);
		$P->line(114,$Y+50,206,$Y+50);
		$P->line(15,$Y+54,105,$Y+54);
		if($D->garantia)
		{
			$Autorizacion=qo("select * from sin_autor where id=$D->garantia");
			if($RCP=qo("select * from recibo_caja_prov where autorizacion=$D->garantia"))
			{
				$Sigla=qo1("select sigla from oficina where id=$RCP->oficina");
				$Nota="Esta factura es CANCELADA contra la Garantía correspondiente al Recibo de Caja Provisinal Número $Sigla-$RCP->consecutivo";
			}
			elseif($RC=qo("select * from recibo_caja where autorizacion=$D->garantia"))
			{
				$Noficina=qo1("select nombre from oficina where id=$RC->oficina");
				$Nota="Esta factura es CANCELADA contra la Garantía correspondiente al Recibo de Caja Número $RC->consecutivo de $Noficina";
			}
			$P->setxy(15,$Y-5);$P->multicell(120,5,$Nota);
		}
		if($D->comentario_factura) {$P->setxy(15,$Y-5); $P->multicell(120,5,$D->comentario_factura,0,'J');}
		$P->setxy(150,$Y-5);$P->cell(20,5,'SUBTOTAL');
		$P->setxy(170,$Y-5);$P->cell(30,5,'$ '.coma_formatd($D->subtotal,2),0,0,'R');
		$P->setxy(150,$Y);$P->cell(20,5,'BASE IVA');
		$P->setxy(170,$Y);$P->cell(30,5,'$ '.coma_formatd($Base_Iva,2),0,0,'R');
		$P->setxy(150,$Y+5);$P->cell(20,5,'IVA');
		$P->setxy(170,$Y+5);$P->cell(30,5,'$ '.coma_formatd($D->iva,2),0,0,'R');
		$P->setxy(150,$Y+10);$P->cell(20,5,'TOTAL');
		$P->setfont('Arial','B',10);
		$P->setxy(170,$Y+10);$P->cell(30,5,'$ '.coma_formatd($D->total,2),0,0,'R');
		$P->setfont('Arial','',10);
		$P->setxy(15,$Y+15);$P->multicell(185,5,'EN LETRAS: '.enletras($D->total,1),0,'J');
		$P->setfont('Arial','B',6);
		$P->setxy(13,$Y+28);$P->cell(30,4,'ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S.');
		$P->setxy(13,$Y+54);$P->cell(90,4,'FIRMA Y SELLO AUTORIZADOS',0,0,'C');
		$P->setxy(15,$Y+58);$P->cell(90,4,'Esta Factura de Venta es un Título Valor de conformidad con lo establecido en',0,0,'C');
		$P->setxy(15,$Y+60);$P->cell(90,4,'la Ley 1231 de julio 17 de 2008 y demás normas que lo complementan.',0,0,'C');
		$P->setxy(110,$Y+50);$P->cell(90,4,'FIRMA Y SELLO DEL COMPRADOR',0,0,'C');
		$P->setfont('Arial','',6);
		$P->setxy(110,$Y+54);$P->cell(90,4,'NOMBRE DE QUIEN RECIBE:');
		$P->setxy(110,$Y+57);$P->cell(90,4,'DOCUMENTO DE IDENTIDAD:');
		$P->setxy(110,$Y+60);$P->cell(90,4,'FECHA DE RECIBIDO:');
		$P->setfont('Arial','B',8);
		$P->setxy(100,$Y+65);$P->cell(20,4,'CARRERA 69B No. 98A-10 BARRIO MORATO PBX: (571) 756 05 10 - FAX (571) 756 05 12',0,0,'C');
		$P->setxy(100,$Y+70);$P->cell(20,4,'www.aoacolombia.com - Bogotá, D.C. - Colombia',0,0,'C');
		$P->setxy(100,$Y+75);$P->cell(20,4,'CLIENTE',0,0,'C');
		if($Archivo) {
			if(is_file($Archivo)) unlink($Archivo);
		}
		$P->Output($Archivo);
		if($Archivo && $app)
		{
			if($vista) header("location:$Archivo");
			die();
		}
	}
	else
		echo "<script language='javascript'>
			function carga()
			{
				centrar(10,10);
				alert('No se encuentra información de la factura número $consecutivo');
				window.close();
				void(null);
			}
		</script>
		<body onload='carga()'></body>";
}

function imprimir_fake_factura()
{
	include('inc/gpos.php');
	//global $consecutivo,$id,$USUARIO,$app;
	//if($USUARIO!=1) die('MODULO EN MANTENIMIENTO');

	if($app )
	{
		$Archivo=directorio_imagen('factura_pdf',$id)."factura$id.pdf";
		if(is_file($Archivo))
		{
			if($vista) header("location:$Archivo");
			die();
		}
	}

	if($id) $D=qo("select * from factura_development where id=$id");
	elseif($consecutivo) $D=qo("select * from factura_development where consecutivo='$consecutivo'");
	$Resol=qo("select * from resolucion_factura where $D->consecutivo between consecutivo_inicial and consecutivo_final");
	$Resolucion="-Resolución de Facturación DIAN No. $Resol->numero de ".mes(date('m',strtotime($Resol->fecha)))." ".date('d',strtotime($Resol->fecha))." de ".date('Y',strtotime($Resol->fecha))." del $Resol->consecutivo_inicial al $Resol->consecutivo_final Autoriza.";
	$FirmaFac=qo1("select firma_f from cfg_factura where activo=1");
	$Firma_recibido=directorio_imagen('factura_pdf',$id).'firma_recibido_factura.png';
	if($D)
	{

		$Sin=qo("select numero,ciudad from siniestro where id=$D->siniestro");
		$OrdenServicio=qo1("select left(nombre,2) from aseguradora where id=$D->aseguradora").'-'.$Sin->numero;
		$Ciudad_sin=qo1("select left(nombre,40) from ciudad where codigo='$Sin->ciudad' ");
		$Cliente=qo("select * from cliente where id=$D->cliente");
		if($D->impresion_resumida) $Qd="select (1) as cantidad, sum(cantidad*unitario) as unitario,(' ') as desripcion,($D->iva) as iva,t_concepto_fac($D->concepto_resumida) as nconcepto from facturad_development where factura=$D->id";
		else $Qd="select *,t_concepto_fac(concepto) as nconcepto from facturad_development where factura=$D->id";
		$Det=q($Qd);

		include('inc/pdf/fpdf.php');
		$P=new pdf('P','mm','letter');
		$P->AddFont("c128a","","c128a.php");
		$P->AliasNbPages();
		$P->setTitle("ORDEN DE SERVICIO");
		$P->setAuthor("Arturo Quintero www.aoacolombia.com arturoquintero@aoacolombia.com");
		$P->Numeracion=false;
		$P->SetAutoPageBreak(false);
		$P->setFillColor(240,240,240);
		//	$P->Header_texto='';
		//	$P->Header_alineacion='L';
		//	$P->Header_alto='8';
		$P->SetTopMargin('5');
		//	$P->Header_colores=array(0,0,0,255,255,255,50,50,100); # rgb texto, rgb fondo, rgb borde
		//	$P->Header_imagen='img/cnota_entrada.jpg';
		///	$P->Header_posicion_imagen=array(20,5,80,14);

		/////////////////////////////////////////     PRIMERA PAGINA: ORIGINAL //////////////////////////////////////////////////////////////////////

		$P->AddPage('P');
		$P->Image('../img/LOGO_AOA_200.jpg',20,5,60,24);
		if($FirmaFac) $P->image($FirmaFac,30,220,50,30);
		//
		$P->image( 'img/lateral_factura_aoa.jpg', 7.5, 130, 2, 40 );
		//$P->image( 'img/lateral_resolucion_factura_aoa.jpg', 210.5, 100, 2, 100 );
		$P->setfont('Arial','B',10);
		if($D->anulada)
		{
			$P->image('gifs/ANULADO2.jpg',40,60,120,150);
		}
		elseif(!$D->autorizadopor)
		{
			$P->image('gifs/SINAPROBACION.jpg',40,60,120,150);
		}
		$P->SetXY(100,5);
		$P->SetTextColor(0,0,0);
		$P->Cell(90,5,'ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S.',0,0,'C');
		$P->setxy(100,9);
		$P->Cell(90,5,'NIT.: 900.174.552-5 - I.V.A. RÉGIMEN COMÚN',0,0,'C');
		$P->setfont('Arial','',8);
		$P->setxy(100,14);
		$P->Cell(90,5,'ACTIVIDADES ECONÓMICAS No. 7710 Y 7020',0,0,'C');
		$P->setxy(100,17);
		$P->Cell(90,5,'GRANDES CONTRIBUYENTES-RESOLUCION 000076 1 DIC 2016',0,0,'C');
		$P->setxy(100,21);
		$P->Cell(90,5,'SOMOS GRANDES CONTRIBUYENTES IMPUESTOS DISTRITALES BOGOTA',0,0,'C');
		$P->SETXY(100,24);
		$P->CELL(90,5,'según Resolución No. DDI-010761',0,0,'C');
		$P->SETXY(100,27);
		$P->CELL(90,5,'SHD del 30 de Marzo de 2016 TALONARIO POR CONTINGENCIA FE',0,0,'C');
		$P->SETXY(100,30);
		$P->CELL(90,5,$Resolucion,0,0,'C');
		$P->SETFONT('Arial','B',8);
		$P->SETXY(10,37);	$P->CELL(20,4,'FECHA DE EMISION:');
		$P->SETXY(87,37);	$P->CELL(40,4,'FEC.VENCIMIENTO:',0,0,'R');
		$P->SETXY(10,45);	$P->CELL(20,4,'CLIENTE:');
		$P->SETXY(87,45);   $P->CELL(40,4,'NIT/CC:',0,0,'R');
		$P->SETXY(10,53);	$P->CELL(20,4,'DIRECCION:');
		$P->SETXY(87,53);	$P->CELL(40,4,'TELEFONO:',0,0,'R');
		$P->SETFONT('Arial','B',10);
		$P->SETXY(170,34);$P->CELL(40,6,'FACTURA DE VENTA',0,0,'C',1);
		$P->SETXY(170,45);$P->cell(40,7,'Orden de Servicio No.',0,0,'L',1);
		$P->SETFONT('Times','B',12);
		$P->settextColor(50,50,50);
		$P->setxy(170,39); $P->Cell(40,6,'No.   '.str_pad($D->consecutivo,6,' ',STR_PAD_LEFT),0,0,'C',1);
		$P->settextcolor(0,0,0);
		$P->setfont('Arial','',8);
		if($Ciudad_sin) {$P->setxy(170,52);$P->cell(40,7,$OrdenServicio,0,0,'C',1);}
		$P->setxy(40,37);$P->cell(40,4,fecha_completa($D->fecha_emision));
		$P->setxy(126,37);$P->cell(40,4,fecha_completa($D->fecha_vencimiento));
		$P->setxy(24,45);$P->cell(40,4,$Cliente->nombre.' '.$Cliente->apellido);
		$P->setxy(126,45);$P->cell(40,4,$Cliente->identificacion);
		$P->setxy(28,53);$P->cell(40,4,$Cliente->direccion);
		$P->setxy(126,53);$P->cell(40,4,$Cliente->celular.' '.$Cliente->telefono_casa);
		$P->line(170,34,170,59);
		$P->line(10,43,170,43);
		$P->line(10,51,170,51);
		$P->line(170,45,210,45);
		$P->rect(10,34,200,25);
		$P->settextcolor(255,255,255);
		$P->setfillcolor(100,100,100);
		$P->setfont('Arial','B',10);
		$P->setxy(10,61);$P->cell(149,5,'DETALLE',1,0,'C',1);
		$P->setxy(160,61);$P->cell(50,5,'VALOR',1,0,'C',1);
		$Y=70;
		$P->settextcolor(0,0,0);
		$P->setfillcolor(240,240,240);
		$P->setfont('Arial','',9);
		$Base_Iva=0;
		if($Det)
			while($I=mysql_fetch_object($Det))
			{
				if($I->iva) $Base_Iva+=$I->cantidad*$I->unitario;
				$P->setxy(170,$Y);$P->cell(30,4,'$ '.coma_formatd($I->cantidad*$I->unitario,2),0,0,'R');
				$P->setxy(15,$Y);
				$P->multicell(140,4,$I->nconcepto.' '.$I->descripcion);
				$Y=$P->y;
			}
		$P->setfont('Arial','',10);
		$Y+=5;
		if($Ciudad_sin)
		{
			$P->setxy(15,$Y);$P->cell(100,5,"$Ciudad_sin",0,0,'L');
		}
		$Y=190;
		if(is_file($Firma_recibido)) {$P->image($Firma_recibido,120,215,80,30);} else {$P->setxy(110,$Y+28);$P->cell(100,36,' ',0,0,'C',1);}
		$P->rect(10,67,200,$Y-41);
		$P->rect(10,$Y+28,200,36);
		$P->line(110,$Y+28,110,$Y+64);
		$P->line(114,$Y+50,206,$Y+50);
		$P->line(15,$Y+54,105,$Y+54);
		if($D->garantia)
		{
			$Autorizacion=qo("select * from sin_autor where id=$D->garantia");
			if($RCP=qo("select * from recibo_caja_prov where autorizacion=$D->garantia"))
			{
				$Sigla=qo1("select sigla from oficina where id=$RCP->oficina");
				$Nota="Esta factura es CANCELADA contra la Garantía correspondiente al Recibo de Caja Provisinal Número $Sigla-$RCP->consecutivo";
			}
			elseif($RC=qo("select * from recibo_caja where autorizacion=$D->garantia"))
			{
				$Noficina=qo1("select nombre from oficina where id=$RC->oficina");
				$Nota="Esta factura es CANCELADA contra la Garantía correspondiente al Recibo de Caja Número $RC->consecutivo de $Noficina";
			}
			$P->setxy(15,$Y-5);$P->multicell(120,5,$Nota);
		}
		if($D->comentario_factura) {$P->setxy(15,$Y-5); $P->multicell(120,5,$D->comentario_factura,0,'J');}
		$P->setxy(150,$Y-5);$P->cell(20,5,'SUBTOTAL');
		$P->setxy(170,$Y-5);$P->cell(30,5,'$ '.coma_formatd($D->subtotal,2),0,0,'R');
		$P->setxy(150,$Y);$P->cell(20,5,'BASE IVA');
		$P->setxy(170,$Y);$P->cell(30,5,'$ '.coma_formatd($Base_Iva,2),0,0,'R');
		$P->setxy(150,$Y+5);$P->cell(20,5,'IVA');
		$P->setxy(170,$Y+5);$P->cell(30,5,'$ '.coma_formatd($D->iva,2),0,0,'R');
		$P->setxy(150,$Y+10);$P->cell(20,5,'TOTAL');
		$P->setfont('Arial','B',10);
		$P->setxy(170,$Y+10);$P->cell(30,5,'$ '.coma_formatd($D->total,2),0,0,'R');
		$P->setfont('Arial','',10);
		$P->setxy(15,$Y+15);$P->multicell(185,5,'EN LETRAS: '.enletras($D->total,1),0,'J');
		$P->setfont('Arial','B',6);
		$P->setxy(13,$Y+28);$P->cell(30,4,'ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S.');
		$P->setxy(13,$Y+54);$P->cell(90,4,'FIRMA Y SELLO AUTORIZADOS',0,0,'C');
		$P->setxy(15,$Y+58);$P->cell(90,4,'Esta Factura de Venta es un Título Valor de conformidad con lo establecido en',0,0,'C');
		$P->setxy(15,$Y+60);$P->cell(90,4,'la Ley 1231 de julio 17 de 2008 y demás normas que lo complementan.',0,0,'C');
		$P->setxy(110,$Y+50);$P->cell(90,4,'FIRMA Y SELLO DEL COMPRADOR',0,0,'C');
		$P->setfont('Arial','',6);
		$P->setxy(110,$Y+54);$P->cell(90,4,'NOMBRE DE QUIEN RECIBE:');
		$P->setxy(110,$Y+57);$P->cell(90,4,'DOCUMENTO DE IDENTIDAD:');
		$P->setxy(110,$Y+60);$P->cell(90,4,'FECHA DE RECIBIDO:');
		$P->setfont('Arial','B',8);
		$P->setxy(100,$Y+65);$P->cell(20,4,'CARRERA 69B No. 98A-10 BARRIO MORATO PBX: (571) 756 05 10 - FAX (571) 756 05 12',0,0,'C');
		$P->setxy(100,$Y+70);$P->cell(20,4,'www.aoacolombia.com - Bogotá, D.C. - Colombia',0,0,'C');
		$P->setxy(100,$Y+75);$P->cell(20,4,'ORIGINAL',0,0,'C');

		/////////////////////////////////////////     SEGUNDA PAGINA: CLIENTE //////////////////////////////////////////////////////////////////////

		$P->AddPage('P');
		$P->Image('../img/LOGO_AOA_200.jpg',20,5,60,24);
		if($FirmaFac) $P->image($FirmaFac,30,220,50,30);
		//if(is_file($Firma_recibido)) {$P->image($Firma_recibido,120,215,80,30);} else {$P->setxy(110,$Y+28);$P->cell(100,36,' ',0,0,'C',1);}
		$P->image( 'img/lateral_factura_aoa.jpg', 7.5, 130, 2, 40 );
		//$P->image( 'img/lateral_resolucion_factura_aoa.jpg', 210.5, 100, 2, 100 );
		$P->setfont('Arial','B',10);
		if($D->anulada)
		{
			$P->image('gifs/ANULADO2.jpg',40,60,120,150);
		}
		elseif(!$D->autorizadopor)
		{
			$P->image('gifs/SINAPROBACION.jpg',40,60,120,150);
		}
		$P->SetXY(100,5);
		$P->SetTextColor(0,0,0);
		$P->Cell(90,5,'ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S.',0,0,'C');
		$P->setxy(100,9);
		$P->Cell(90,5,'NIT.: 900.174.552-5 - I.V.A. RÉGIMEN COMÚN',0,0,'C');
		$P->setfont('Arial','',8);
		$P->setxy(100,14);
		$P->Cell(90,5,'ACTIVIADES ECONÓMICAS No. 7414 Y 7111',0,0,'C');
		$P->setxy(100,17);
		$P->Cell(90,5,'GRANDES CONTRIBUYENTES-RESOLUCION 000076 1 DIC 2016',0,0,'C');
		$P->setxy(100,21);
		$P->Cell(90,5,'SOMOS GRANDES CONTRIBUYENTES IMPUESTOS DISTRITALES BOGOTA',0,0,'C');
		$P->SETXY(100,24);
		$P->CELL(90,5,'según Resolución No. DDI-010761',0,0,'C');
		$P->SETXY(100,27);
		$P->CELL(90,5,'SHD del 30 de Marzo de 2016',0,0,'C');
		$P->SETXY(100,30);
		$P->CELL(90,5,$Resolucion,0,0,'C');
		$P->SETFONT('Arial','B',8);
		$P->SETXY(10,37);	$P->CELL(20,4,'FECHA DE EMISION:');
		$P->SETXY(87,37);	$P->CELL(40,4,'FEC.VENCIMIENTO:',0,0,'R');
		$P->SETXY(10,45);	$P->CELL(20,4,'CLIENTE:');
		$P->SETXY(87,45);   $P->CELL(40,4,'NIT/CC:',0,0,'R');
		$P->SETXY(10,53);	$P->CELL(20,4,'DIRECCION:');
		$P->SETXY(87,53);	$P->CELL(40,4,'TELEFONO:',0,0,'R');
		$P->SETFONT('Arial','B',10);
		$P->SETXY(170,34);$P->CELL(40,6,'FACTURA DE VENTA',0,0,'C',1);
		$P->SETXY(170,45);$P->cell(40,7,'Orden de Servicio No.',0,0,'L',1);
		$P->SETFONT('Times','B',12);
		$P->settextColor(50,50,50);
		$P->setxy(170,39); $P->Cell(40,6,'No.   '.str_pad($D->consecutivo,6,' ',STR_PAD_LEFT),0,0,'C',1);
		$P->settextcolor(0,0,0);
		$P->setfont('Arial','',8);
		if($Ciudad_sin) {$P->setxy(170,52);$P->cell(40,7,$OrdenServicio,0,0,'C',1);}
		$P->setxy(40,37);$P->cell(40,4,fecha_completa($D->fecha_emision));
		$P->setxy(126,37);$P->cell(40,4,fecha_completa($D->fecha_vencimiento));
		$P->setxy(24,45);$P->cell(40,4,$Cliente->nombre.' '.$Cliente->apellido);
		$P->setxy(126,45);$P->cell(40,4,$Cliente->identificacion);
		$P->setxy(28,53);$P->cell(40,4,$Cliente->direccion);
		$P->setxy(126,53);$P->cell(40,4,$Cliente->celular.' '.$Cliente->telefono_casa);
		$P->line(170,34,170,59);
		$P->line(10,43,170,43);
		$P->line(10,51,170,51);
		$P->line(170,45,210,45);
		$P->rect(10,34,200,25);
		$P->settextcolor(255,255,255);
		$P->setfillcolor(100,100,100);
		$P->setfont('Arial','B',10);
		$P->setxy(10,61);$P->cell(149,5,'DETALLE',1,0,'C',1);
		$P->setxy(160,61);$P->cell(50,5,'VALOR',1,0,'C',1);
		$Y=70;
		$P->settextcolor(0,0,0);
		$P->setfillcolor(240,240,240);
		$P->setfont('Arial','',10);
		$Base_Iva=0;
		$P->setfont('Arial','',9);
		if($Det)
		{
			mysql_data_seek($Det, 0);
			while($I=mysql_fetch_object($Det))
			{
				if($I->iva) $Base_Iva+=$I->cantidad*$I->unitario;
				$P->setxy(170,$Y);$P->cell(30,5,'$ '.coma_formatd($I->cantidad*$I->unitario,2),0,0,'R');
				$P->setxy(15,$Y);
				$P->multicell(140,4,$I->nconcepto.' '.$I->descripcion);
				$Y=$P->y;
			}
		}
		$P->setfont('Arial','',10);
		$Y+=5;
		if($Ciudad_sin)
		{
			$P->setxy(15,$Y);$P->cell(100,5,"$Ciudad_sin",0,0,'L');
		}
		$Y=190;
		if(is_file($Firma_recibido)) {$P->image($Firma_recibido,120,215,80,30);} else {$P->setxy(110,$Y+28);$P->cell(100,36,' ',0,0,'C',1);}
		$P->rect(10,67,200,$Y-41);
		$P->rect(10,$Y+28,200,36);
		$P->line(110,$Y+28,110,$Y+64);
		$P->line(114,$Y+50,206,$Y+50);
		$P->line(15,$Y+54,105,$Y+54);
		if($D->garantia)
		{
			$Autorizacion=qo("select * from sin_autor where id=$D->garantia");
			if($RCP=qo("select * from recibo_caja_prov where autorizacion=$D->garantia"))
			{
				$Sigla=qo1("select sigla from oficina where id=$RCP->oficina");
				$Nota="Esta factura es CANCELADA contra la Garantía correspondiente al Recibo de Caja Provisinal Número $Sigla-$RCP->consecutivo";
			}
			elseif($RC=qo("select * from recibo_caja where autorizacion=$D->garantia"))
			{
				$Noficina=qo1("select nombre from oficina where id=$RC->oficina");
				$Nota="Esta factura es CANCELADA contra la Garantía correspondiente al Recibo de Caja Número $RC->consecutivo de $Noficina";
			}
			$P->setxy(15,$Y-5);$P->multicell(120,5,$Nota);
		}
		if($D->comentario_factura) {$P->setxy(15,$Y-5); $P->multicell(120,5,$D->comentario_factura,0,'J');}
		$P->setxy(150,$Y-5);$P->cell(20,5,'SUBTOTAL');
		$P->setxy(170,$Y-5);$P->cell(30,5,'$ '.coma_formatd($D->subtotal,2),0,0,'R');
		$P->setxy(150,$Y);$P->cell(20,5,'BASE IVA');
		$P->setxy(170,$Y);$P->cell(30,5,'$ '.coma_formatd($Base_Iva,2),0,0,'R');
		$P->setxy(150,$Y+5);$P->cell(20,5,'IVA');
		$P->setxy(170,$Y+5);$P->cell(30,5,'$ '.coma_formatd($D->iva,2),0,0,'R');
		$P->setxy(150,$Y+10);$P->cell(20,5,'TOTAL');
		$P->setfont('Arial','B',10);
		$P->setxy(170,$Y+10);$P->cell(30,5,'$ '.coma_formatd($D->total,2),0,0,'R');
		$P->setfont('Arial','',10);
		$P->setxy(15,$Y+15);$P->multicell(185,5,'EN LETRAS: '.enletras($D->total,1),0,'J');
		$P->setfont('Arial','B',6);
		$P->setxy(13,$Y+28);$P->cell(30,4,'ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S.');
		$P->setxy(13,$Y+54);$P->cell(90,4,'FIRMA Y SELLO AUTORIZADOS',0,0,'C');
		$P->setxy(15,$Y+58);$P->cell(90,4,'Esta Factura de Venta es un Título Valor de conformidad con lo establecido en',0,0,'C');
		$P->setxy(15,$Y+60);$P->cell(90,4,'la Ley 1231 de julio 17 de 2008 y demás normas que lo complementan.',0,0,'C');
		$P->setxy(110,$Y+50);$P->cell(90,4,'FIRMA Y SELLO DEL COMPRADOR',0,0,'C');
		$P->setfont('Arial','',6);
		$P->setxy(110,$Y+54);$P->cell(90,4,'NOMBRE DE QUIEN RECIBE:');
		$P->setxy(110,$Y+57);$P->cell(90,4,'DOCUMENTO DE IDENTIDAD:');
		$P->setxy(110,$Y+60);$P->cell(90,4,'FECHA DE RECIBIDO:');
		$P->setfont('Arial','B',8);
		$P->setxy(100,$Y+65);$P->cell(20,4,'CARRERA 69B No. 98A-10 BARRIO MORATO PBX: (571) 756 05 10 - FAX (571) 756 05 12',0,0,'C');
		$P->setxy(100,$Y+70);$P->cell(20,4,'www.aoacolombia.com - Bogotá, D.C. - Colombia',0,0,'C');
		$P->setxy(100,$Y+75);$P->cell(20,4,'CLIENTE',0,0,'C');
		if($Archivo) {
			if(is_file($Archivo)) unlink($Archivo);
		}
		$P->Output($Archivo);
		if($Archivo && $app)
		{
			if($vista) header("location:$Archivo");
			die();
		}
	}
	else
		echo "<script language='javascript'>
			function carga()
			{
				centrar(10,10);
				alert('No se encuentra información de la factura número $consecutivo');
				window.close();
				void(null);
			}
		</script>
		<body onload='carga()'></body>";
}
?>