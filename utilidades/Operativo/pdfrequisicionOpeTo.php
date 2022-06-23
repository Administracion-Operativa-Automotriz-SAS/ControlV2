<?php
				  
				  
     $USER=$_SESSION['User'];
		$NUSUARIO=$_SESSION['Nombre'];
		$BDA="aoacol_administra";

$ER=qo("select requisicion.placa, 
				 concat( oficina.centro_operacion,' ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto,aseguradora.nombre as ASEGURADORA,ubicacion.flota,requisiciond.centro_operacion
				 from aoacol_administra.requisiciond 
				 LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
				 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 inner JOIN aoacol_aoacars.oficina on ubicacion.oficina = oficina.id
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion.id = $id");
	$D=qo("select * from aoacol_administra.requisicion where id=$id"); // trae la informaci�n de la requisicion
	$Ciu=qo1("select t_ciudad('$D->ciudad')"); // trae la informaci�n de la ciudad
	$Prov=qo("select * from aoacol_administra.proveedor where id=$D->proveedor");
	$Pr=qo("select * from aoacol_administra.perfil_requisicion where id=$D->perfil"); // trae la informaci�n del perfil que aprueba la requisici�n
	
	$Hoy=date('Y-m-d H:i:s');
	
	
	$DV=qo("select * from aoacol_aoacars.vehiculo where placa='$D->placa'");
	$HU=qo("select ubicacion from $BDA.requisicion where id = $id");
	$HT=qo("select * from aoacol_aoacars.ubicacion where id='$HU->ubicacion'");
    $li=qo("select * from aoacol_aoacars.linea_vehiculo where id='$DV->linea'");
	$Mr=qo("select * from aoacol_aoacars.marca_vehiculo where id = '$li->marca'");
	
	
	$Deta=qo("select requisiciond.requisicion,provee_produc_serv.nombre as item,tipo.nombre as tipo, unidad_de_medida.nombre as unidad_medida,
				 requisiciond.observaciones,requisiciond.cantidad,requisiciond.valor as valor_unitario, requisiciond.valor_total,requisicion.placa, 
				 concat( oficina.centro_operacion,' ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto, requisicion.fecha 
				 from aoacol_administra.requisiciond 
				 LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
				 LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id 
				 LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id 
				 LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id 
				 LEFT OUTER JOIN aoacol_administra.requisicionc on requisiciond.clase = requisicionc.id 
				 LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
				 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 inner JOIN aoacol_aoacars.oficina on ubicacion.oficina = oficina.id
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion =$id");
	
	
		$reto=q("select requisiciond.requisicion,requisiciond.valor_total,
		            sum(requisiciond.valor_total) as resultado 
					from aoacol_administra.requisiciond
					where requisicion  =$id");

						
	
	require("pdf/fpdf.php");
	      
			      $texto = utf8_decode('  Tenga en cuenta que los anteriores valores no incluyen impuestos.
	   Las facturas y/o cuentas de cobro, deben ser radicadas mencionando el n�mero de requisici�n o adjuntando el presente
	   documento. De lo contrario su cobro ser� rechazado.
	   Las facturas y/o cuentas de cobro, deben ser radicadas con el n�mero de requisici�n o con el presente documento.
	   Para radicaci�n de facturas electr�nicas, deber�n ser remitidas al siguiente correo electr�nico feproveedores@aoacolombia.com 
	   En caso de realizar alg�n servicio a la flota de AOA, relacione el n�mero de placa del vehiculo. ');

		
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
		

 $pdf->SetFont('Arial','B',10);
		$pdf->Image('https://pbs.twimg.com/profile_images/1305916683178123273/1BtxwW1A_400x400.jpg',67,1,80); 
		
		$pdf->SetTextColor(0,0,0);
		$pdf->Text(62,55,"TRANSPORTES ESPECIALES DEL ORIENTE S.A.S.");
		$pdf->Text(73,60,"NIT.: 900645942-6 - Persona jur�dica ");
		$pdf->SetFont('Arial','B',10);
		$pdf->Text(60,65,"DIRECCI�N: Cr 69 B No. 98 A 10 barrio Morato Bogot� D.C.");
		$pdf->Text(35,70,"CELULAR : 3202303743, TELEFONO: 6337733 EMAIL: gabriel.sandoval@transorientesas.com");

    
	$pdf->setXY(10,65);
	
	$pdf->SetFont('Arial','B',8);
	$pdf->setXY(10,40);
	$pdf->SetFillColor(187,224,67);
		
	    $pdf->setXY(10,78);
		$pdf->Cell(190,7,utf8_decode('INFORMACI�N DEL SOLICITANTE '),1,1,'C',1);
		
	$pdf->SetFillColor(	50, 214, 79);	
	$pdf->setXY(10,85);
	    $pdf->SetFont('Arial','B',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(36,5,utf8_decode('FECHA DE REQUISICI�N'),1,1,'C',1);
		
	$pdf->setXY(46,85);
		$pdf->SetFont('Arial','B',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(40,5,utf8_decode($D->fecha),1,1,'C');
			
		
		$pdf->setXY(86,85);
			$pdf->Cell(55,5,utf8_decode('REQUISICI�N N�MERO'),1,1,'C',1);
			
		$pdf->setXY(141,85);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($id),1,1,'C');
			$pdf->SetFont('Arial','B',7);


	$pdf->setXY(10,90);
		$pdf->Cell(36,5,utf8_decode('SOLICITADO POR '),1,1,'C',1);
		
		$pdf->setXY(46,90);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(88,5,$D->solicitado_por,1,1,'C');
		
		$pdf->setXY(116,90);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(25,5,utf8_decode('CIUDAD'),1,1,'C',1);
			
		$pdf->setXY(141,90);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($Ciu),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
			
			
			
			$pdf->setXY(10,103);
	
	$pdf->SetFont('Arial','B',8);
	$pdf->setXY(10,40);
	$pdf->SetFillColor(187,224,67);
		
	    $pdf->setXY(10,103);
		$pdf->Cell(190,7,utf8_decode('INFORMACI�N DEL VEHICULO '),1,1,'C',1);
		
	$pdf->SetFillColor(	50, 214, 79);	
	$pdf->setXY(10,110);
	    $pdf->SetFont('Arial','B',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(30,5,utf8_decode('PLACA'),1,1,'C',1);
		
	$pdf->setXY(40,110);
		$pdf->SetFont('Arial','B',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(30,5,utf8_decode($D->placa),1,1,'C');
			
		
		$pdf->setXY(70,110);
			$pdf->Cell(30,5,utf8_decode('MODELO'),1,1,'C',1);
			
		$pdf->setXY(100,110);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(33,5,utf8_decode($DV->modelo),1,1,'C');
			$pdf->SetFont('Arial','B',7);

            
        	$pdf->setXY(133,110);
			$pdf->Cell(28,5,utf8_decode('MARCA'),1,1,'C',1);
			
		$pdf->setXY(161,110);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(39,5,utf8_decode($Mr->nombre),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
			

	$pdf->setXY(10,115);
		$pdf->Cell(50,5,utf8_decode('LINEA'),1,1,'C',1);
		
		$pdf->setXY(60,115);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(40,5,utf8_decode($li->nombre),1,1,'C');
		
		$pdf->setXY(100,115);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(40,5,utf8_decode('FECHA INICIAL'),1,1,'C',1);
			
		$pdf->setXY(140,115);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(60,5,utf8_decode($HT->fecha_inicial),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
			
		$pdf->setXY(10,120);
		$pdf->Cell(50,5,utf8_decode('KILOMETRAJE INICIAL '),1,1,'C',1);
		
		$pdf->setXY(60,120);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(40,5,number_format($HT->odometro_inicial),1,1,'C');
		
		$pdf->setXY(100,120);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(40,5,utf8_decode('FECHA FINAL'),1,1,'C',1);
			
		$pdf->setXY(140,120);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(60,5,utf8_decode($HT->fecha_final),1,1,'C');
			$pdf->SetFont('Arial','B',7);	
	
			
			
		
			
			
			
		$pdf->setXY(10,133);
		$pdf->SetFont('Arial','B',8);
	    $pdf->SetFillColor(187,224,67);
		$pdf->Cell(190,7,utf8_decode('INFORMACI�N DEL PROVEDOR'),1,1,'C',1);
		
	    $pdf->SetFillColor(50, 214, 79);
	    $pdf->setXY(10,140);
		$pdf->Cell(45,5,utf8_decode('NOMBRE PROVEEDOR'),1,1,'C',1);
		
		$pdf->setXY(55,140);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($Prov->nombre),1,1,'C');
			
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,140);
		$pdf->Cell(41,5,utf8_decode('NIT PROVEEDOR'),1,1,'C',1);
		$pdf->setXY(141,140);
			$pdf->Cell(59,5,utf8_decode($Prov->identificacion),1,1,'C');
		
        $pdf->setXY(10,145);
		$pdf->Cell(45,5,utf8_decode('CIUDAD PROVEEDOR'),1,1,'C',1);
		
		$pdf->setXY(55,145);
		
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($Prov->ciudad),1,1,'C');
			
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,145);
		$pdf->Cell(41,5,utf8_decode('DIRECCI�N PROVEEDOR'),1,1,'C',1);
		$pdf->setXY(141,145);
			$pdf->Cell(59,5,utf8_decode($Prov->direccion),1,1,'C');
					
		$pdf->setXY(10,150);
		$pdf->Cell(45,5,utf8_decode('CONTACTO DEL PROVEEDOR'),1,1,'C',1);
		
		$pdf->setXY(55,150);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($Prov->contacto),1,1,'C');
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,150);
		$pdf->Cell(41,5,utf8_decode('EMAIL DEL PROVEEDOR'),1,1,'C',1);
		$pdf->setXY(141,150);
		$pdf->Cell(59,5,utf8_decode($Prov->email),1,1,'C');
			
	

	$pdf->setXY(10,163);
		$pdf->SetFont('Arial','B',8);
	$pdf->SetFillColor(187,224,67);
		$pdf->Cell(189,7,utf8_decode('DETALLE DE LA REQUISICI�N'),1,1,'C',1);


	
		//Add first page
       
    $pdf->SetFillColor(50, 214, 79);
		$pdf->SetFont('Arial','B',6);
		$pdf->SetY($y_axis_initial);
		$pdf->SetX(10);
		$pdf->Cell(26,8,utf8_decode('TIPO DE REQUISICI�N'),1,0,'C',1);
		$pdf->Cell(35,8,utf8_decode('ITEM'),1,0,'C',1);
		$pdf->Cell(24,8,utf8_decode('UNIDAD DE MEDIDA'),1,0,'C',1);
		$pdf->Cell(13,8,utf8_decode('CANTIDAD'),1,0,'C',1);
		$pdf->Cell(21,8,utf8_decode('VALOR UNITARIO'),1,0,'C',1);
		$pdf->Cell(18,8,utf8_decode('VALOR TOTAL '),1,0,'C',1);
		$pdf->Cell(52,8,utf8_decode('DESCRIPCI�N'),1,0,'C',1);

		
		$pdf->Ln(8);

		while($row = mysql_fetch_object($Deta))
		{
		$Dt = $row;  	
	
			//If the current row is the last one, create new page and print column title
			if ($i == $max)
			{
				$max =14;
				$pdf->Cell(0,30,utf8_decode('P�gina').$pdf->PageNo().'/{nb}',0,0,'C');
				$pdf->AddPage();
				//print column titles for the current page
				$pdf->SetFillColor(50, 214, 79);
				$pdf->SetFont('Arial','B',6);
				$pdf->SetY(20);
				$pdf->SetX(10);
				$pdf->Cell(26,8,utf8_decode('TIPO DE REQUISICI�N'),1,0,'C',1);
				$pdf->Cell(35,8,utf8_decode('ITEM'),1,0,'C',1);
				$pdf->Cell(24,8,utf8_decode('UNIDAD DE MEDIDA'),1,0,'C',1);
				$pdf->Cell(13,8,utf8_decode('CANTIDAD'),1,0,'C',1);
				$pdf->Cell(21,8,utf8_decode('VALOR UNITARIO'),1,0,'C',1);
				$pdf->Cell(18,8,utf8_decode('VALOR'),1,0,'C',1);
				$pdf->Cell(52,8,utf8_decode('DESCRIPCI�N'),1,0,'C',1);
                $pdf->Ln(8);
				$y_axis_initial = 10;
			
				
				$y_axis = $y_axis + $row_height;
				
				$i = 0;
				
			}
			
			$pdf->SetFont('Arial','',7);
			$fontSize=9;

			$tempFontSize=$fontSize;

			$tipo = $row->tipo;
			$item = $row->item;
			$unidad_medida = $row->unidad_medida;
			$observacions = $row->observaciones;
			$centrodeoperacion = $row->centrodeoperacion;
			$cantidad = $row->cantidad;
			$valor_unitario = $row->valor_unitario;
			$valor_total = $row->valor_total;
			$cellWidth=52;//wrapped cell width
			$cellHeight=5;//normal one-line cell height
			 //check whether the text is overflowing
			 if($pdf->GetStringWidth($observacions) < $cellWidth){
			  //if not, then do nothing
			  $line=1;
			 }else{
			  //if it is, then calculate the height needed for wrapped cell
			  //by splitting the text to fit the cell width
			  //then count how many lines are needed for the text to fit the cell
			  
			  $textLength=strlen($observacions); //total text length
			  $errMargin=10;  //cell width error margin, just in case
			  $startChar=0;  //character start position for each line
			  $maxChar=0;   //maximum character in a line, to be incremented later
			  $textArray=array(); //to hold the strings for each line
			  $tmpString="";  //to hold the string for a line (temporary)
			  
			  while($startChar < $textLength){ //loop until end of text
			   //loop until maximum character reached
			   while( 
			   $pdf->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) &&
			   ($startChar+$maxChar) < $textLength ) {
				$maxChar++;
				$tmpString=substr($observacions,$startChar,$maxChar);
			   }
			   //move startChar to next line
			   $startChar=$startChar+$maxChar;
			   //then add it into the array so we know how many line are needed
			   array_push($textArray,$tmpString);
			   //reset maxChar and tmpString
			   $maxChar=0;
			   $tmpString='';
			   
			  }
			  //get number of line
			  $line=count($textArray);
			 }
			
			$pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(26,($line * $cellHeight),utf8_decode($tipo),1,0,'l',1);
			$pdf->Cell(35,($line * $cellHeight),utf8_decode($item),1,0,'C',1);
			$pdf->Cell(24,($line * $cellHeight),utf8_decode($unidad_medida),1,0,'C',1);
			$pdf->Cell(13,($line * $cellHeight),utf8_decode($cantidad),1,0,'C',1);
			$pdf->Cell(21,($line * $cellHeight),utf8_decode($valor_unitario),1,0,'C',1);
		    $pdf->Cell(18,($line * $cellHeight),utf8_decode($valor_total),1,0,'C',1);
			$xPos=$pdf->GetX();
		    $yPos=$pdf->GetY();
		    $pdf->MultiCell($cellWidth,$cellHeight,$observacions,1);
			
			 

			//Go to next row
			$y_axis = $y_axis + $row_height;
			$i = $i + 1;
		}
		
		 $Total = mysql_fetch_object($reto);
        
        
       $pdf->setX(10);
	   	$pdf->SetFillColor(	187,224,67);
	   $pdf->SetFont('Arial','B',10);
	   $pdf->Cell(47,5,utf8_decode("TOTAL:"),1,0,'C',1);
	  $pdf->SetFillColor(255, 255, 255);
		$pdf->Cell(90,5,"$".number_format($Total->resultado),1,1,'C',1);
		
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
	  
		
		
		$pdf->setXY(10,265);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',8);
		$pdf->SetFillColor(140,208,12);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(0,10,utf8_decode('P�gina').$pdf->PageNo().'/{nb}',0,0,'C');
		$pdf->AliasNbPages();
		

	  
?>