<?php

/**
 * @author John Camacho
 * @copyright 2008
 */
 
 require ("fpdf.php");
 include ("lib_class.php");
 include ("libMYSQL.php");
 $id_contrato=$_GET['registro'];
 class PDF extends FPDF{
//Pie de página
function Footer(){
    //Posición: a 1,5 cm del final
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Número de página
    $this->Cell(0,10,'Hoja '.$this->PageNo().'/{nb}',0,0,'C');
}
}

//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
//$pdf->SetFont('Arial','',12);
$pdf->Image('contrato.PNG',0,0,215);
$MyQuery="SELECT s.siniestro,sg.nombre,CONCAT(c.apellido,' ',c.nombre),now(),c.documento,CONCAT(c1.apellido,' ',c1.nombre),c1.documento,
c.direccion,c.telefono_casa,c.celular,c.telefono_oficina,c.email,ct.n_tarjeta,ct.banco,ct.fecha_tarjeta,ct.n_seguridad,ct.cupo_tarjeta,
ct.n_activacion,ct.fecha_activacion,ct.fecha_entrega,YEAR(ct.fecha_entrega),MONTH(ct.fecha_entrega),DAY(ct.fecha_entrega),s.dias_seguro,
s.dias_adicional,ct.dias_seguro FROM siniestro s,seguro sg,cliente1 c1,contrato ct,cliente c WHERE sg.id=s.id_seguro AND c.id=s.id_cliente AND c1.id=s.id_cliente1 AND s.id=ct.id_siniestro AND ct.id=$id_contrato";
$loContrato=cargar_registro1($MyQuery);
$pdf->SetFont('Arial','',12);
//coordenada y
$pdf->Ln(17);
//coordenada x
$pdf->Cell(54);
//campo de texto general
$pdf->Cell(34,8,"$loContrato[0]",0,0,'C');
$pdf->SetFont('Arial','',10);
//coordenada y
$pdf->Ln(2);
//coordenada x
$pdf->Cell(128);
$pdf->Cell(36,4,"$loContrato[1]");
//coordenada y
$pdf->Ln(12);
//coordenada x
$pdf->Cell(23);
$pdf->Cell(85,4,"$loContrato[2]");
//Automatizacion tipo de documento
$trozos = explode ("-",$loContrato[4]);
if($trozos[1]==""){
$d=2;
$d1=24;
}
else{
$d=22;
$d1=4;	
}
$pdf->Cell($d);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(8,4,"X",0,0,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell($d1);
$pdf->Cell(47,4,"$loContrato[4]");
$pdf->SetFont('Arial','',8);
//coordenada y
$pdf->Ln(8);
//coordenada x
$pdf->Cell(89);
$pdf->Cell(64,4,"$loContrato[5]");
$pdf->Cell(6);
$pdf->Cell(30,4,"$loContrato[6]");
$pdf->SetFont('Arial','',6);
//coordenada y
$pdf->Ln(9);
//coordenada x
$pdf->Cell(28);
$pdf->Cell(54,4,"$loContrato[7]");
$pdf->SetFont('Arial','',8);
$pdf->Cell(24);
$pdf->Cell(32,4,"$loContrato[8]");
$pdf->Cell(10);
$pdf->Cell(32,4,"$loContrato[9]");
//coordenada y
$pdf->Ln(7);
//coordenada x
$pdf->Cell(150);
$pdf->Cell(32,4,"$loContrato[10]");
//coordenada y
$pdf->Ln(7);
//coordenada x
$pdf->Cell(18);
$pdf->Cell(75,4,"$loContrato[11]");
//coordenada y
$pdf->Ln(25);
//coordenada x
$pdf->Cell(57);
$pdf->Cell(49,4,"$loContrato[12]");
$pdf->Cell(17);
$pdf->Cell(68,4,"$loContrato[13]");
//coordenada y
$pdf->Ln(7);
//coordenada x
$pdf->Cell(120);
$pdf->Cell(49,4,"$loContrato[14]");
//coordenada y
$pdf->Ln(7);
//coordenada x
$pdf->Cell(55);
$pdf->Cell(38,4,"$loContrato[15]");
$pdf->Cell(45);
$pdf->Cell(50,4,"$loContrato[16]");
//coordenada y
$pdf->Ln(8);
//coordenada x
$pdf->Cell(56);
$pdf->Cell(43,4,"$loContrato[17]");
$pdf->Cell(50);
$pdf->Cell(40,4,"$loContrato[18]");
//coordenada y
$pdf->Ln(36);
//coordenada x
$pdf->Cell(49);
$pdf->Cell(43,4,"$loContrato[19]");
//calculo fecha devolucion
$total_d=$loContrato[23]+$loContrato[24]+$loContrato[25];
$f_dev=suma_fechas("$loContrato[22]-$loContrato[21]-$loContrato[20]",$total_d);
list($dia,$mes,$año)=split("-",$f_dev);
$pdf->Cell(55);
$pdf->Cell(43,4,"$año-$mes-$dia");
//coordenada y
$pdf->Ln(15);
//coordenada x
$pdf->Cell(83);
$pdf->Cell(8,4,"$loContrato[23]",0,0,'C');
$pdf->Cell(43);
$pdf->Cell(8,4,"$loContrato[24]",0,0,'C');
$pdf->Cell(32);
$pdf->Cell(8,4,"$loContrato[25]",0,0,'C');
//coordenada y
$pdf->Ln(7);
//coordenada x
$pdf->Cell(79);
$pdf->Cell(13,6,"$total_d",0,0,'C');
$pdf->SetFont('Arial','B',12);
//coordenada y
$pdf->Ln(8);
//coordenada x
$pdf->Cell(83);
if(!($loContrato[24]=="" or $loContrato[24]==0)){
$xA="X";
}
$pdf->Cell(4,4,"$xA",0,0,'C');
$pdf->Cell(53);
if(!($loContrato[25]=="" or $loContrato[25]==0)){
$xC="X";
}
$pdf->Cell(4,4,"$xC",0,0,'C');
$pdf->AddPage();
$pdf->Image('terminos1.PNG',0,0,215);
$pdf->AddPage();
$pdf->Image('terminos2.PNG',0,0,215);
$pdf->AddPage();
$pdf->Image('inventario.PNG',0,0,215);
$MyQuery="SELECT s.siniestro,DAY(ct.fecha_entrega),MONTH(ct.fecha_entrega),YEAR(ct.fecha_entrega),hora_entrega,CONCAT(c.apellido,' ',
c.nombre),c.documento,c.direccion,c.celular,v.placa,v.numero_motor,v.numero_chasis,ct.odometro FROM siniestro s,contrato ct,vehiculo v,cliente c  WHERE ct.id_vehiculo=v.id AND ct.id_siniestro=s.id AND s.id_cliente=c.id AND ct.id=$id_contrato";
$loInventario=cargar_registro1($MyQuery);
$pdf->SetFont('Arial','',12);
//coordenada y
$pdf->Ln(1);
//coordenada x
$pdf->Cell(61);
//campo de texto general
$pdf->Cell(30,8,"$loInventario[0]",0,0,'C');
$pdf->SetFont('Arial','',8);
//coordenada y
$pdf->Ln(12);
//coordenada x
$pdf->Cell(42);
//campo de texto general
$pdf->Cell(8,4,"$loInventario[1]");
$pdf->Cell(9);
$pdf->Cell(8,4,"$loInventario[2]");
$pdf->Cell(8);
$pdf->Cell(10,4,"$loInventario[3]");
$pdf->Cell(13);
$pdf->Cell(10,4,"$loInventario[4]");
//coordenada y
$pdf->Ln(9);
//coordenada x
$pdf->Cell(15);
//campo de texto general
$pdf->Cell(56,4,"$loInventario[5]");
$pdf->Cell(6);
$pdf->Cell(25,4,"$loInventario[6]");
//coordenada y
$pdf->Ln(4);
//coordenada x
$pdf->Cell(20);
//campo de texto general
$pdf->SetFont('Arial','',6);
$pdf->Cell(41,4,"$loInventario[7]");
$pdf->Cell(13);
$pdf->SetFont('Arial','',8);
$pdf->Cell(30,4,"$loInventario[8]");
//coordenada y
$pdf->Ln(4);
//coordenada x
$pdf->Cell(48);
//campo de texto general
$pdf->Cell(21,4,"$loInventario[9]");
//coordenada y
$pdf->Ln(4);
//coordenada x
$pdf->Cell(69);
//campo de texto general
$pdf->Cell(35,4,"$loInventario[10]");
//coordenada y
$pdf->Ln(4);
//coordenada x
$pdf->Cell(20);
//campo de texto general
$pdf->Cell(33,4,"$loInventario[11]");
$pdf->Cell(19);
$pdf->Cell(32,4,"$loInventario[12]");
$pdf->AddPage();
$pdf->Image('encuesta.PNG',0,0,215);
$MyQuery="SELECT CONCAT(c.apellido,' ',c.nombre),c.documento,ct.fecha_entrega,s.siniestro FROM cliente c,contrato ct,siniestro s WHERE 
c.id=s.id_cliente AND s.id=ct.id_siniestro AND ct.id=$id_contrato";
$loEncuesta=cargar_registro1($MyQuery);
$pdf->SetFont('Arial','',12);
//coordenada y
$pdf->Ln(30);
//coordenada x
$pdf->Cell(37);
//campo de texto general
$pdf->Cell(76,4,"$loEncuesta[0]");
$pdf->Cell(28);
$pdf->Cell(41,4,"$loEncuesta[1]");
//coordenada y
$pdf->Ln(5);
//coordenada x
$pdf->Cell(34);
//campo de texto general
$pdf->Cell(79,4,"$loEncuesta[2]");
$pdf->Cell(25);
$pdf->Cell(44,4,"$loEncuesta[3]");
$pdf->Output();
?>