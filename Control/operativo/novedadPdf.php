<?php
header('Content-Type: text/html; charset=utf-8');
  echo'5345';
  exit();
$id_novedad = $_POST['id_novedad'];

  require("inc/pdf/fpdf.php");

  $novedad=q("select aoa_modulo.novedad.id_novedad as id_novedad,
						aoa_modulo.novedad.fecha_creacion as fecha_creacion,
						aoa_modulo.novedad.fecha_creacion as fecha_creacion,
						aoa_modulo.novedad.ciudad as ciudad_novedad,
						aoacol_aoacars.vehiculo.placa as placa,
						aoacol_aoacars.linea_vehiculo.nombre as linea,
						aoacol_aoacars.marca_vehiculo.nombre as marca,
						MAX(aoacol_aoacars.ubicacion.odometro_final)  as km,
						aoacol_administra.proveedor.nombre as proveedor,
						aoacol_administra.oficina.nombre as ubicacion,
						aoacol_administra.proveedor.nombre as proveedor_nombre,
						aoacol_administra.proveedor.identificacion as proveedor_nit,
						aoacol_administra.proveedor.ciudad as ubicacion_proveedor,
						aoacol_administra.proveedor.contacto as contacto_proveedor
						aoacol_administra.proveedor.email as correo,
						aoacol_administra.proveedor.direccion as proveedor_direccion,
						aoa_modulo.novedad.novedad as novedad,
						MAX(aoacol_aoacars.ubicacion.fecha_inicial)as fechaIngreso,
						MAX(aoacol_aoacars.ubicacion.odometro_inicial) as kmIngreso,
						MAX(aoacol_aoacars.ubicacion.fecha_final) as fechaSalida,
						MAX(aoacol_aoacars.ubicacion.odometro_final) as kmIngreso,
						aoa_modulo.novedad_requisicion.descripcion as descripcion,
						aoa_modulo.novedad_requisicion.actividad_solitante as actividad_solitante,
						aoa_modulo.novedad_requisicion.actividad_provedor as actividad_provedor
								from  aoa_modulo.novedad  
						LEFT OUTER JOIN aoa_modulo.tipoNovedad
					 on novedad.id_tipo = tipoNovedad.idtipoNovedad 
					 	LEFT OUTER JOIN aoa_modulo.novedad_requisicion
					 on novedad.id_novedad = novedad_requisicion.novedad 
					 	LEFT OUTER JOIN aoacol_administra.proveedor
					 on novedad_requisicion.proveedor = proveedor.id 
						LEFT OUTER JOIN aoacol_aoacars.vehiculo
					 on novedad.id_placa = vehiculo.placa 
					 	LEFT OUTER JOIN aoacol_aoacars.ubicacion
					 on vehiculo.id = ubicacion.vehiculo 
					  	LEFT OUTER JOIN aoacol_administra.oficina
					 on ubicacion.oficina = oficina.id 
					LEFT OUTER JOIN 
					aoacol_aoacars.linea_vehiculo on  vehiculo.linea = linea_vehiculo.id 
					LEFT OUTER JOIN 
					aoacol_aoacars.marca_vehiculo on  linea_vehiculo.marca = marca_vehiculo.id 
					WHERE  aoa_modulo.novedad.id_novedad  ='137' ");
        
		 


	      
		      $texto = utf8_decode('  Tenga en cuenta que los anteriores valores no incluyen impuestos.
	   Las facturas y/o cuentas de cobro, deben ser radicadas mencionando el número de requisición o adjuntando el presente
	   documento. De lo contrario su cobro será rechazado.
	   Las facturas y/o cuentas de cobro, deben ser radicadas con el número de requisición o con el presente documento.
	   Para radicación de facturas electrónicas, deberán ser remitidas al siguiente correo electrónico feproveedores@aoacolombia.com 
	   En caso de realizar algún servicio a la flota de AOA, relacione el número de placa del vehiculo. ');

		
		
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
		$pdf->SetFont('Arial','B',10);
		$pdf->Text(60,65,utf8_decode("DIRECCIÓN: Cr 69 B No. 98 A 10 barrio Morato Bogotá D.C."));
		$pdf->Text(55,70,utf8_decode("TELEFONO: 7560510, EMAIL: feproveedores@aoacolombia.com  "));
		

    
	$pdf->setXY(10,65);
	
	$pdf->SetFont('Arial','B',8);
	$pdf->setXY(10,40);
	$pdf->SetFillColor(187,224,67);
		
	    $pdf->setXY(10,78);
		$pdf->Cell(190,7,utf8_decode('INFORMACIÓN DEL SOLICITANTE '),1,1,'C',1);
		
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
			$pdf->Cell(59,5,utf8_decode($novedad->fecha_creación),1,1,'C');
			$pdf->SetFont('Arial','B',7);


	$pdf->setXY(10,90);
		$pdf->Cell(36,5,utf8_decode('SOLICITADO  '),1,1,'C',1);
		
		$pdf->setXY(46,90);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(88,5,utf8_decode($novedad->solicitante),1,1,'C');
		
		$pdf->setXY(116,90);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(25,5,utf8_decode('CIUDAD'),1,1,'C',1);
			
		$pdf->setXY(141,90);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($novedad->ciudad_novedad),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
			
			
			
			$pdf->setXY(10,103);
	
	$pdf->SetFont('Arial','B',8);
	$pdf->setXY(10,40);
	$pdf->SetFillColor(187,224,67);
		
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
	    $pdf->SetFillColor(187,224,67);
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
		$pdf->Cell(45,5,utf8_decode($novedad->proveedor_ciudad),1,1,'C');
			
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
			
	

	$pdf->setXY(10,163);
		$pdf->SetFont('Arial','B',8);
	$pdf->SetFillColor(187,224,67);
		$pdf->Cell(189,7,utf8_decode('DETALLE DE LA NOVEDAD'),1,1,'C',1);


			//If the current row is the last one, create new page and print column title
			if ($i == $max)
			{
				
				$max =14;
				$pdf->Cell(0,30,utf8_decode('Página').$pdf->PageNo().'/{nb}',0,0,'C');
				$pdf->AddPage();

		
			
				
				$y_axis = $y_axis + $row_height;
				
				$i = 0;
				
			}
					
	    $pdf->Ln(8);
		$pdf->SetFillColor(13,0,143);
		$pdf->SetFont('Arial','B',8);
		$pdf->SetFillColor(187,224,67);
		$pdf->Cell(85,5,utf8_decode('NOTAS IMPORTANTES'),1,1,'C',1);
	
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',9);
		 $pdf->SetFillColor(255, 255, 255);
		$pdf->setX(10,230);
		$pdf->MultiCell(190,5,$texto,1,1,'C');
		$pdf->Ln(5);
	   $pdf->Image('img/logo-footer-mail-AOA.jpg'); 
		
		
		$pdf->setXY(10,265);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',8);
		$pdf->SetFillColor(140,208,12);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(0,10,utf8_decode('Página').$pdf->PageNo().'/{nb}',0,0,'C');
		$pdf->AliasNbPages();
		
		
	
	  $Res.="</table>";
      $Det.="</table>"; 
      $pdf->Output(''.$nombre.'.pdf', 'D');

      header("location: ".$filename);

?>