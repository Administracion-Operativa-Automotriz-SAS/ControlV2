<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
  require("inc/pdf/fpdf.php");

header('Content-Type: text/html; charset=utf-8');
include('inc/funciones_.php');

$id_novedad = $_GET["id_novedad"];


$nombre = $_GET["novedad"];


  $novedad=qo("select  aoa_modulo.novedad.id_novedad as id_novedad,
   MAX(aoacol_aoacars.ubicacion.fecha_inicial)as fechaIngreso,
	MAX(aoacol_aoacars.ubicacion.odometro_inicial) as kmIngreso,
	MAX(aoacol_aoacars.ubicacion.fecha_final) as fechaSalida,
	MAX(aoacol_aoacars.ubicacion.odometro_final) as kmFinal,
   aoacol_aoacars.vehiculo.placa,
   aoa_modulo.novedad.reportado as reportado,
   aoa_modulo.novedad.tele_reporte as tele_reporte,
   aoa_modulo.novedad.email_reporte as email_reporte,
   aoa_modulo.novedad.id_sinistro as id_sinistro,
   aoa_modulo.novedad.novedad as novedad, 
     aoa_modulo.novedad.encargado as encargado, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_reporte,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_sinistro as id_sinistro,
   aoa_modulo.novedad.novedad as novedad,
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
   aoa_modulo.novedad.cierre as cierre, 
      aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
  aoa_modulo.tipoNovedad.idtipoNovedad as id_nombre_tipoN, 
   aoacol_aoacars.operario.nombre as nombre_opera,
      aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.siniestro on novedad.id_sinistro = siniestro.numero 
   	LEFT OUTER JOIN aoacol_aoacars.vehiculo
		 on aoacol_aoacars.siniestro.placa = vehiculo.placa 
		LEFT OUTER JOIN aoacol_aoacars.ubicacion
		 on vehiculo.id = ubicacion.vehiculo 	  
		WHERE  aoa_modulo.novedad.id_novedad ='$id_novedad ' ");
					
					




	    $i = 0;

		$max = 7;

		$row_height = 10;
		$y_axis_initial = 170;
		
		$y_axis = 142;
        $y_axis = $y_axis + $row_height;

	$pdf = new FPDF();
    $pdf->AddPage();





	$pdf->setXY(10,15);
	
		$pdf->SetFont('Arial','B',10);

	   

        $pdf->Image('img/banner-vehiculo-sustituto-AOA.jpg',17,20,180); 
		$pdf->SetTextColor(0,0,0);
		$pdf->Text(42,55,utf8_decode("ADMINISTRACION OPERATIVA AUTOMOTRIZ SAS Y/O AOA COLOMBIA SAS"));
		$pdf->Text(68,60,utf8_decode("NIT.: 900.174.552-5 - I.V.A. RÉGIMEN COMÚN "));
		$pdf->SetFont('Arial','B',17);
		$pdf->Text(50,73,utf8_decode("NOVEDAD  $novedad->id_novedad DE LA PLACA $novedad->placa "));
		

    
	$pdf->setXY(10,65);
	
	$pdf->SetFont('Arial','B',8);
	$pdf->setXY(10,40);
	$pdf->SetFillColor(0, 188, 212);
		
	    $pdf->setXY(10,78);
		$pdf->Cell(190,7,utf8_decode('INFORMACIÓN DE NOVEDAD  '),1,1,'C',1);
		
	$pdf->SetFillColor(	215, 240, 127);	
	$pdf->setXY(10,85);
	    $pdf->SetFont('Arial','B',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(36,5,utf8_decode('CONSECUTIVO'),1,1,'C',1);
		
	$pdf->setXY(46,85);
		$pdf->SetFont('Arial','B',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(40,5,utf8_decode($novedad->id_novedad),1,1,'C');
			
		
		$pdf->setXY(86,85);
			$pdf->Cell(55,5,utf8_decode('FECHA DE CREACIÓN'),1,1,'C',1);
			
		$pdf->setXY(141,85);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($novedad->fecha_creacion),1,1,'C');
			$pdf->SetFont('Arial','B',7);


	$pdf->setXY(10,90);
		$pdf->Cell(36,5,utf8_decode('NOVEDAD '),1,1,'C',1);
		
		$pdf->setXY(46,90);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(88,5,utf8_decode($novedad->novedad),1,1,'C');
		
		$pdf->setXY(116,90);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(25,5,utf8_decode('TIPO NOVEDAD'),1,1,'C',1);
			
		$pdf->setXY(141,90);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($novedad->nombre_tipoN),1,1,'C');
			$pdf->SetFont('Arial','B',7);
		
		
		
		$pdf->setXY(10,105);
		$pdf->Cell(36,5,utf8_decode('SOLICITADO  '),1,1,'C',1);
		
		$pdf->setXY(46,90);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(88,5,utf8_decode($novedad->solicitante),1,1,'C');
		
		$pdf->setXY(116,90);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(25,5,utf8_decode('CIUDAD'),1,1,'C',1);
			
		$pdf->setXY(141,90);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($novedad->ciudad),1,1,'C');
			$pdf->SetFont('Arial','B',7);
		
			
			
			
			$pdf->setXY(10,113);
	
	$pdf->SetFont('Arial','B',8);
	$pdf->setXY(10,40);
	$pdf->SetFillColor(0, 188, 212);
		
	    $pdf->setXY(10,103);
		$pdf->Cell(190,7,utf8_decode('INFORMACIÓN DEL VEHICULO '),1,1,'C',1);
		
	$pdf->SetFillColor(	215, 240, 127);	
	$pdf->setXY(10,110);
	    $pdf->SetFont('Arial','B',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(30,5,utf8_decode('PLACA'),1,1,'C',1);
		
	$pdf->setXY(40,110);
		$pdf->SetFont('Arial','B',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(30,5,utf8_decode($novedad->placa),1,1,'C');
			
		
		$pdf->setXY(70,110);
			$pdf->Cell(30,5,utf8_decode('MODELO'),1,1,'C',1);
			
		$pdf->setXY(100,110);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(33,5,utf8_decode($novedad->modelo),1,1,'C');
			$pdf->SetFont('Arial','B',7);

            
        	$pdf->setXY(133,110);
			$pdf->Cell(28,5,utf8_decode('MARCA'),1,1,'C',1);
			
		$pdf->setXY(161,110);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(39,5,utf8_decode($novedad->marca),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
			

	$pdf->setXY(10,115);
		$pdf->Cell(50,5,utf8_decode('LINEA'),1,1,'C',1);
		
		$pdf->setXY(60,115);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(40,5,utf8_decode($novedad->linea),1,1,'C');
		
		$pdf->setXY(100,115);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(40,5,utf8_decode('FECHA INICIAL'),1,1,'C',1);
			
		$pdf->setXY(140,115);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(60,5,utf8_decode($novedad->fechaSalida),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
			
		$pdf->setXY(10,120);
		$pdf->Cell(50,5,utf8_decode('KILOMETRAJE INICIAL '),1,1,'C',1);
		
		$pdf->setXY(60,120);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(40,5,number_format($novedad->kmIngreso),1,1,'C');
		
		$pdf->setXY(100,120);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(40,5,utf8_decode('FECHA FINAL'),1,1,'C',1);
			
		$pdf->setXY(140,120);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(60,5,utf8_decode($novedad->fechaIngreso),1,1,'C');
			$pdf->SetFont('Arial','B',7);	
	
			
			
		
			
			
			
		$pdf->setXY(10,133);
		$pdf->SetFont('Arial','B',8);
	    $pdf->SetFillColor(0, 188, 212);
		$pdf->Cell(190,7,utf8_decode('INFORMACIÓN DEL PROVEDOR'),1,1,'C',1);
		
	    $pdf->SetFillColor(215, 240, 127);
	    $pdf->setXY(10,140);
		$pdf->Cell(45,5,utf8_decode('NOMBRE PROVEEDOR'),1,1,'C',1);
		
		$pdf->setXY(55,140);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad->proveedor_nombre),1,1,'C');
			
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,140);
		$pdf->Cell(41,5,utf8_decode('NIT PROVEEDOR'),1,1,'C',1);
		$pdf->setXY(141,140);
			$pdf->Cell(59,5,utf8_decode($novedad->proveedor_nit),1,1,'C');
		
        $pdf->setXY(10,145);
		$pdf->Cell(45,5,utf8_decode('CIUDAD PROVEEDOR'),1,1,'C',1);
		
		$pdf->setXY(55,145);
		
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad->ubicacion_proveedor),1,1,'C');
			
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,145);
		$pdf->Cell(41,5,utf8_decode('DIRECCIÓN PROVEEDOR'),1,1,'C',1);
		$pdf->setXY(141,145);
			$pdf->Cell(59,5,utf8_decode($novedad->proveedor_direccion),1,1,'C');
					
		$pdf->setXY(10,150);
		$pdf->Cell(45,5,utf8_decode('CONTACTO DEL PROVEEDOR'),1,1,'C',1);
		
		$pdf->setXY(55,150);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad->contacto_proveedor),1,1,'C');
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,150);
		$pdf->Cell(41,5,utf8_decode('EMAIL DEL PROVEEDOR'),1,1,'C',1);
		$pdf->setXY(141,150);
		$pdf->Cell(59,5,utf8_decode($novedad->correo),1,1,'C');
			
	
	$pdf->setXY(10,165);
		$pdf->SetFont('Arial','B',8);
	$pdf->SetFillColor(0, 188, 212);
		$pdf->Cell(189,5,utf8_decode('NOVEDAD'),1,1,'C',1);
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',9);
		 $pdf->SetFillColor(255, 255, 255);
		$pdf->setX(10,230);
		$pdf->MultiCell(189,5,utf8_decode($novedad->novedad),1,1,'C');
		$pdf->Ln(2);
		
		$pdf->setXY(10,180);
		$pdf->SetFont('Arial','B',8);
	$pdf->SetFillColor(0, 188, 212);
		$pdf->Cell(189,5,utf8_decode('DESCRIPCIÓN'),1,1,'C',1);
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',9);
		 $pdf->SetFillColor(255, 255, 255);
		$pdf->setX(10,230);
		$pdf->MultiCell(189,5,utf8_decode($novedad->descripcion),1,1,'C');
		$pdf->Ln(2);

		
				$pdf->setXY(10,195);
		$pdf->SetFont('Arial','B',8);
	$pdf->SetFillColor(0, 188, 212);
		$pdf->Cell(189,5,utf8_decode('ACTIVIDAD SOLICITANTE'),1,1,'C',1);
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',9);
		 $pdf->SetFillColor(255, 255, 255);
		$pdf->setX(10,230);
		$pdf->MultiCell(189,5,utf8_decode($novedad->actividad_solitante),1,1,'C');
		$pdf->Ln(2);
		
		
				$pdf->setXY(10,210);
		$pdf->SetFont('Arial','B',8);
	$pdf->SetFillColor(0, 188, 212);
		$pdf->Cell(189,5,utf8_decode('ACTIVIDAD PROVEEDOR'),1,1,'C',1);
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',9);
		 $pdf->SetFillColor(255, 255, 255);
		$pdf->setX(10,230);
		$pdf->MultiCell(189,5,utf8_decode($novedad->actividad_provedor),1,1,'C');
		$pdf->Ln(2);

			//If the current row is the last one, create new page and print column title
			if ($i == $max)
			{
				
				$max =14;
				$pdf->Cell(0,30,utf8_decode('Página').$pdf->PageNo().'/{nb}',0,0,'C');
				$pdf->AddPage();

		
			
				
				$y_axis = $y_axis + $row_height;
				
				$i = 0;
				
			}
					
	    
	   $pdf->Image('img/logo-footer-mail-AOA.jpg'); 
		
		
		$pdf->setXY(10,265);
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',8);
		$pdf->SetFillColor(140,208,12);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(0,10,utf8_decode('Página').$pdf->PageNo().'/{nb}',0,0,'C');
		$pdf->AliasNbPages();
      
	  $pdf->Output(''.$nombre.'.pdf', 'D');

      header("location: ".$filename);
     
?>

