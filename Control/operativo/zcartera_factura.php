<?php

/**
 *  Facturacion AOA
 *  Este modulo permite controlar la cartera de la empresa
 * @version $Id$
 * @copyright 2010
 */
include('inc/funciones_.php');
sesion();
$USUARIO = $_SESSION['User']; 
$Nusuario = $_SESSION['Nombre'];
$Nick = $_SESSION['Nick'];
$Hoyl = date('Y-m-d H:i:s');
$Hoy = date('Y-m-d');
$Hora = date('H:i:s');
$CONCILIADOR=0;


function imprimir_recibo() // imprime el recibo generando un documento en formato pdf
{
	global $id,$Hoyl;
	$sql = "select * from prefactura where id=$id";
	//echo $sql; 
	$R=qo($sql);
	$Cli=qo("select * from cliente where id=$R->cliente");
	if($R->siniestro!=0) $Sin=qo("select numero,fec_autorizacion from siniestro where id=$R->siniestro");
	if($R->autorizacion) $TC=qo("select *,t_codigo_ach(banco) as nbanco from sin_autor where id=$R->autorizacion");
	if($R->factura) $Fac=qo("select * from factura where id=$R->factura");
	include('inc/pdf/fpdf.php'); //incluye el objeto
	$P=new pdf('P','mm','letter');  // crea la instancia de la clase pdf
	$P->AddFont("c128a","","c128a.php"); // adicicona fuentes para codigo de barras
	$P->AliasNbPages();
	$P->setTitle("PRE-FACTURA");
	$P->setAuthor("Arturo Quintero www.aoacolombia.com arturoquintero@aoacolombia.com");
	$P->Numeracion=false;
	$P->SetAutoPageBreak(false);
	$P->setFillColor(250,250,250);
	//	$P->Header_texto='';
	//	$P->Header_alineacion='L';
	//	$P->Header_alto='8';
	$P->SetTopMargin('5');
	//	$P->Header_colores=array(0,0,0,255,255,255,50,50,100); # rgb texto, rgb fondo, rgb borde
	//	$P->Header_imagen='img/cnota_entrada.jpg';
	///	$P->Header_posicion_imagen=array(20,5,80,14);
	$P->AddPage('P'); // adiciona una pagina
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$ny=5;
	$P->Image('../img/LOGO_AOA_200.jpg',50,$ny,30,12);
	if($R->anulado) $P->image('gifs/ANULADO2.jpg',60,25,80,60);
	$P->SetXY(100,$ny);$P->SetTextColor(0,0,0);$P->setfont('Arial','B',10);$P->Cell(90,5,'ADMISTRACION OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
	$ny=$P->y+4;$P->setxy(100,$ny);$P->setfont('Arial','B',10);$P->Cell(90,5,'NIT.: 900.174.552-5',0,0,'C');
	$P->rect(110,$ny+5,70,14);
	$ny=$P->y+7;$P->setxy(120,$ny);$P->setfont('Arial','B',16);$P->Cell(80,5,'PRE-FACTURA',0,0,'L');
	$ny=$P->y+2;$P->setxy(20,$ny);$P->setfont('Arial','',10);$P->cell(90,4,'Carrera 69B 98A-10 Bogoto D.C.',0,0,'C');
	$ny=$P->y+3;$P->setxy(130,$ny);$P->setfont('Times','B',16);$P->Cell(10,5,'No.',0,0,'L');$P->setfont('Arial','B',16);$P->Cell(20,5,str_pad($R->id,6,'0',STR_PAD_LEFT),0,0,'L');
	$ny=$P->y+1;$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(90,4,'Pbx: (057) 1 7560510 Fax (057) 1 7560512',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->cell(90,4,'www.aoacolombia.com',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->Cell(22,4,'Ciudad:',1,0,'L');$P->Cell(108,4,'oficina virtual',1,0,'L');$P->Cell(20,8,'Fecha:',1,0,'C');$P->Cell(30,8,$R->fecha_emision,1,0,'C');
	$ny=$P->y+4;
	$P->setxy(20,$ny);
	if($R->siniestro!=0)
	{
		$P->Cell(22,4,'Siniestro:',1,0,'L');
		$P->cell(108,4,$Sin->numero.' F.Autorizacion: '.$Sin->fec_autorizacion,1,0,'L');
	}
	else
	{
		$P->Cell(22,4,'',1,0,'L');
		$P->cell(108,4,' ',1,0,'L');
	}
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Recibido de:',1,0,'L');$P->Cell(108,4,trim($Cli->nombre.' '.$Cli->apellido.' '.coma_format($Cli->identificacion)),1,0,'L');$P->Cell(8,4,'$',1,0,'C');$P->Cell(42,4,coma_format($R->valor),1,0,'R',1);
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Direccion:',1,0,'L');$P->Cell(158,4,$Cli->direccion,1,0,'L');
	$P->setxy(20,$P->y+4);$P->setfont('Arial','',6);$P->multicell(180,4,'En Letras: '.enletras(10000,1),1,'J',1);
	$P->setxy(20,$P->y);$P->setfont('Arial','',8);$P->multicell(180,4,'Concepto: '.str_replace("\r\n","",$R->concepto),1,'J');
	$P->setxy(20,$P->y+1);$P->setfont('Arial','B',8);$P->Cell(180,4,'FORMA DE PAGO',1,0,'C',1);$P->setfont('Arial','',8);
	$ny=$P->y+4;
	$P->setxy(20,$ny);
	
    $P->cell(40,4,'Cheque: $'.coma_format(2000),1,0,'L');$P->cell(64,4,'Banco: Banco de Bogota',1,0,'L');$P->cell(38,4,'Cuenta: 121212',1,0,'L');$P->cell(38,4,'No.Cheque: ',1,0,'L');
	
	$ny=$P->y+4;
	$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(110,4,'Elaborado por: '.$R->capturado_por ,1,1,'L');$P->setxy(130,$ny);$P->cell(70,15,' ',1);
	$ny=$P->y+5;$P->setxy(20,$ny);$P->SetFont("c128a","",12);$P->cell(110,14, uccean128('FA'.str_pad($Fac->id,10,'0',STR_PAD_LEFT).str_pad($R->id,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$ny=$P->y+10;$P->setxy(130,$ny);$P->setfont('Arial','',8);$P->cell(70,4,'Firma y sello',1,0,'L');
	$ny=$P->y+4;
	$P->setxy(100,$ny);$P->setfont('Arial','B',8);$P->cell(20,4,'ORIGINAL',0,0,'C');
	$P->setxy(170,$ny);$P->setfont('Arial','',6);$P->cell(30,4,$Hoyl,0,0,'R');

	
	$P->Output($Archivo); // presenta el archivo para ser visualizado en el browser o descargarlo al pc
}




?>