<?php

$id_novedad = $_GET["id_novedad"];
if( !isset($_POST['submit']) ) {
			


?>
<hr>
    <form method='post' style='  margin-bottom:1%; text-align : center;  ' action=''>
       <img src='https://www.aoacolombia.com/wp-content/uploads/2019/09/cropped-logotipo-aoa-colombia-1.png'></img>
	  	<br>	<br>	<br>	<br>
	Nombre  <input type="text" name="name"  value="Novedad<?php echo$id_novedad; ?>"/>
        <input type="submit" value="Descargar  " name="submit" />
		<img src='img/pdf.jpg'  width='20' height='20' ></img>
		<br>
    </form>
<?php
}
else {
error_reporting(E_ALL);
ini_set('display_errors', '1');
  require("inc/pdf/fpdf.php");

include('inc/funciones_.php');

 $imagen_size = 20;
$id_novedad = $_GET["id_novedad"];
$encargado = $_GET["encargado"];
$nombre = $_GET["novedad"];

 

 
  $novedad=qo("select  aoa_modulo.novedad.id_novedad as id_novedad,

      vehiculo.modelo  as modelo_vehiculo ,vehiculo.tipo_caja,
					vehiculo.placa  as placa ,
					linea_vehiculo.nombre  as linea_vehiculo,
					marca_vehiculo.nombre  as marca_vehiculo,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.id_placa,
   aoa_modulo.novedad.depar_reporte_otro as depar_reporte_otro,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
    aoa_modulo.novedad.acti_caarga as acti_caarga,
     aoa_modulo.novedad.cierre_oper as cierre_oper,
      aoa_modulo.novedad.solicitante as solicitante,
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad, 
     aoa_modulo.novedad.encargado as encargado, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_asegurado,
       aoacol_aoacars.operario.email as email_encargado,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_siniestro as id_siniestro,
   aoa_modulo.novedad.novedad as novedad,
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
     aoa_modulo.novedad.ciudad_reporte_otro as ciudad_reporte, 
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.reportado_otro as reportado, 
   aoa_modulo.novedad.cierre as cierre, 
    aoa_modulo.tipo_cierre.nombre_cierre as estado_cierre, 
     aoa_modulo.tipo_cierre.color_cierre as estado_color, 

      aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
    aoa_modulo.tipoNovedad.color as color_tipoN, 
  aoa_modulo.tipoNovedad.idtipoNovedad as id_nombre_tipoN, 
   aoacol_aoacars.operario.nombre as nombre_opera,
      aoacol_aoacars.operario.id as id_opera,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	 LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.siniestro on novedad.id_siniestro = siniestro.numero 
   	LEFT OUTER JOIN aoacol_aoacars.vehiculo
		 on aoa_modulo.novedad.id_placa = vehiculo.placa 
	  
					LEFT OUTER JOIN aoacol_aoacars.linea_vehiculo ON  vehiculo.linea  = linea_vehiculo.id 
					LEFT OUTER JOIN aoacol_aoacars.marca_vehiculo ON  linea_vehiculo.marca  = marca_vehiculo.id 
		WHERE  aoa_modulo.novedad.id_novedad  ='$id_novedad ' ");
		
		
		

	 
	 
	 
	 $Detalle=q("select 	aoa_modulo.novedad.id_novedad as id_n, aoa_modulo.novedad.reportado_otro,
			 aoa_modulo.novedad.solicitante, aoacol_aoacars.operario.nombre ,aoacol_aoacars.operario.apellido ,
			 aoa_modulo.novedad_requisicion.descripcion,
			 aoa_modulo.novedad_requisicion.actividad_provedor,aoa_modulo.novedad_requisicion.proveedor,
			 aoa_modulo.novedad_requisicion.estado_novedad_requisicion,
			 aoa_modulo.novedad_requisicion.id,
			 aoacol_administra.proveedor.nombre as nombre_prov
			  
				from   aoa_modulo.novedad_requisicion 
			 LEFT OUTER JOIN aoa_modulo.novedad
				on novedad_requisicion.novedad = novedad.id_novedad
				 LEFT OUTER JOIN aoacol_administra.proveedor
				 on novedad_requisicion.proveedor = proveedor.id
			LEFT OUTER JOIN aoacol_aoacars.operario
			   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
			 where   aoa_modulo.novedad_requisicion.novedad  ='$id_novedad' ");
			 
			 
			 
			 
			 
			 
			
			 
			 
			  $novedad_pendiente = qo("select * from aoa_modulo.novedad_pendiente where aoa_modulo.novedad_pendiente.novedad_id = $id_novedad "); 
			 
		

	    $i = 0;

		$max = 7;

		$row_height = 10;
		$y_axis_initial = 17;
		
		$y_axis = 122;
        $y_axis = $y_axis + $row_height;
		
		
		if($novedad->tipo_caja == 1 ){
			
			$tipo_caja = 'Manual';
			
		}else{
			
			$tipo_caja = 'Automatica';
		};
 
	$pdf = new FPDF();
    $pdf->AddPage();





	$pdf->setXY(10,15);
	
		$pdf->SetFont('Arial','B',10);

	   

        $pdf->Image('img/banner-vehiculo-sustituto-AOA.jpg',17,20,180); 
		$pdf->SetTextColor(0,0,0);
		$pdf->Text(42,55,utf8_decode("ADMINISTRACION OPERATIVA AUTOMOTRIZ SAS Y/O AOA COLOMBIA SAS"));
		$pdf->Text(68,60,utf8_decode("NIT.: 900.174.552-5 - I.V.A. R??GIMEN COM??N "));
		$pdf->SetFont('Arial','B',17);
		$pdf->Text(30,69,utf8_decode("ACTA DE NOVEDAD #$novedad->id_novedad DEL VEH??CULO  #$novedad->id_placa "));
		

    
	$pdf->setXY(10,65);
	
	$pdf->SetFont('Arial','B',8);
	$pdf->setXY(10,40);
	$pdf->SetFillColor(187,224,67);
		
	    $pdf->setXY(10,78);
		$pdf->Cell(190,7,utf8_decode('INFORMACI??N DE NOVEDAD '),1,1,'C',1);
		
	$pdf->SetFillColor(	215, 240, 127);	
	$pdf->setXY(10,85);
	    $pdf->SetFont('Arial','B',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(36,5,utf8_decode('id'),1,1,'C',1);
		
	$pdf->setXY(46,85);
		$pdf->SetFont('Arial','B',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(40,5,utf8_decode($novedad->id_novedad),1,1,'C');
			
		
		$pdf->setXY(86,85);
			$pdf->Cell(55,5,utf8_decode('Novedad'),1,1,'C',1);
			
		$pdf->setXY(141,85);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($novedad->novedad),1,1,'C');
			$pdf->SetFont('Arial','B',7);


	$pdf->setXY(10,90);
		$pdf->Cell(36,5,utf8_decode('Tipo novedad'),1,1,'C',1);
		
		$pdf->setXY(46,90);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(55,5,utf8_decode($novedad->nombre_tipoN),1,1,'C');
		
		$pdf->setXY(101,90);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(40,5,utf8_decode('Fecha creacion'),1,1,'C',1);
			
		$pdf->setXY(141,90);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($novedad->fecha_creacion),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
		


	$pdf->setXY(10,95);
		$pdf->Cell(36,5,utf8_decode('Placa reportada '),1,1,'C',1);
		
		$pdf->setXY(46,95);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(55,5,utf8_decode($novedad->placa),1,1,'C');
		
		$pdf->setXY(101,95);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(40,5,utf8_decode('Reportado'),1,1,'C',1);
			
		$pdf->setXY(141,95);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($novedad->reportado),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
			

			
	$pdf->setXY(10,100);
		$pdf->Cell(36,5,utf8_decode('Atendido'),1,1,'C',1);
		
		$pdf->setXY(46,100);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(55,5,utf8_decode($novedad->solicitante),1,1,'C');
		
		$pdf->setXY(101,100);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(40,5,utf8_decode('Encargado'),1,1,'C',1);
			
		$pdf->setXY(141,100);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($novedad->nombre_opera.' '.$novedad->apellido_opera ),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
						
	
		
			$pdf->setXY(10,115);
		$pdf->SetFont('Arial','B',8);
	    $pdf->SetFillColor(187,224,67);
		$pdf->Cell(190,7,utf8_decode('INFORMACI??N DEL VEH??CULO'),1,1,'C',1);
		
	    $pdf->SetFillColor(215, 240, 127);
		
		$pdf->setXY(10,122);
		$pdf->Cell(45,5,utf8_decode('Placa'),1,1,'C',1);
		
		$pdf->setXY(55,122);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad->placa),1,1,'C');
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,122);
		$pdf->Cell(41,5,utf8_decode('Marca'),1,1,'C',1);
		$pdf->setXY(141,122);
		$pdf->Cell(59,5,utf8_decode($novedad->marca_vehiculo),1,1,'C');
		
		
		$pdf->setXY(10,127);
		$pdf->Cell(45,5,utf8_decode('Modelo'),1,1,'C',1);
		
		$pdf->setXY(55,127);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad->modelo_vehiculo),1,1,'C');
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,127);
		$pdf->Cell(41,5,utf8_decode('Tipo caja'),1,1,'C',1);
		$pdf->setXY(141,127);
		$pdf->Cell(59,5,utf8_decode($tipo_caja),1,1,'C');
		
		
		
             if(!$novedad_pendiente == null){
	$imagen_size = 290;
	  
		$pdf->setXY(10,142);
		$pdf->SetFont('Arial','B',8);
	    $pdf->SetFillColor(187,224,67);
		$pdf->Cell(190,7,utf8_decode('INFORMACI??N DE LA NOVEDAD PENDIENTE '),1,1,'C',1);
		
	    $pdf->SetFillColor(215, 240, 127);
		
		$pdf->setXY(10,149);
		$pdf->Cell(45,5,utf8_decode('Id'),1,1,'C',1);
		
		$pdf->setXY(55,149);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad_pendiente->id_novedad_pendiente),1,1,'C');
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,149);
		$pdf->Cell(41,5,'Descripci??n',1,1,'C',1);
		$pdf->setXY(141,149);
		$pdf->Cell(59,5,utf8_decode($novedad_pendiente->descripcion_pad),1,1,'C');
		
		
		
		$pdf->setXY(10,154);
		$pdf->Cell(45,5,utf8_decode('Fecha causa '),1,1,'C',1);
		
		$pdf->setXY(55,154);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad_pendiente->fecha_causa),1,1,'C');
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,154);
		$pdf->Cell(41,5,utf8_decode('Causa de la novedad'),1,1,'C',1);
		$pdf->setXY(141,154);
		$pdf->Cell(59,5,utf8_decode($novedad_pendiente->causa),1,1,'C');
		
		
		
		
		$pdf->setXY(10,159);
		$pdf->Cell(45,5,utf8_decode('Departamento de la novedad'),1,1,'C',1);
		
		$pdf->setXY(55,159);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad_pendiente->departamento),1,1,'C');
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,159);
		$pdf->Cell(41,5,utf8_decode('Ciudad de la novedad'),1,1,'C',1);
		$pdf->setXY(141,159);
		$pdf->Cell(59,5,utf8_decode($novedad_pendiente->ciudad),1,1,'C');
		  

		}
		
		
			 if(!$Detalle == null){
		

		$imagen_size = 230;
		
	$pdf->setXY(10,174);
		$pdf->SetFont('Arial','B',8);
	$pdf->SetFillColor(187,224,67);
		$pdf->Cell(189,7,utf8_decode('DETALLE DE LA REQUISICI??N'),1,1,'C',1);


	
		//Add first page
       
    $pdf->SetFillColor(215, 240, 127);
		$pdf->SetFont('Arial','B',6);
		$pdf->setXY(10,181);
		
		$pdf->Cell(16,5,utf8_decode('ID'),1,0,'C',1);
				
				$pdf->Cell(30,5,utf8_decode('REPORTADO'),1,0,'C',1);
				$pdf->Cell(30,5,utf8_decode('SOLICITANTE'),1,0,'C',1);
				$pdf->Cell(26,5,utf8_decode('DESCRIPCI??N'),1,0,'C',1);
				$pdf->Cell(43.5,5,utf8_decode('PROVEEDOR'),1,0,'C',1);
				$pdf->Cell(43.5,5,utf8_decode('ACTIVIDAD PROVEEDOR'),1,0,'C',1);

		
		$pdf->Ln(5);

		
		
	
				 
				 
				 while($row = mysql_fetch_object($Detalle))
		{
			
			 
	
			//If the current row is the last one, create new page and print column title
			if ($i == $max)
			{
				
				

				//print column titles for the current page
				$pdf->SetFillColor(215, 240, 127);
				$pdf->SetFont('Arial','B',6);
				$pdf->setXY(10,186);
				$pdf->Cell(16,8,utf8_decode('ID'),1,0,'C',1);
				$pdf->Cell(30,8,utf8_decode('REPORTADO'),1,0,'C',1);
				$pdf->Cell(30,8,utf8_decode('SOLICITANTE'),1,0,'C',1);
				$pdf->Cell(26,8,utf8_decode('DESCRIPCI??N'),1,0,'C',1);
				$pdf->Cell(43.5,8,utf8_decode('PROVEEDOR'),1,0,'C',1);
				$pdf->Cell(43.5,8,utf8_decode('ACTIVIDAD PROVEEDOR'),1,0,'C',1);
                $pdf->Ln(8);
				$y_axis_initial = 10;
			
				
				
			}	
			
			
	     $pdf->SetFont('Arial','',7);
			$fontSize=9;

			$tempFontSize=$fontSize;
             $descripcion = $row->descripcion;
			$novedad = $row->id_n;
			$id_cot = $row->id;
			$reportado = $row->reportado_otro;
			$solicitante = $row->solicitante;
			$proveedor = $row->nombre_prov;
			$actividad_provedor = $row->actividad_provedor;
			$encargado = $row->nombre.' '.$row->apellido;
			$cellWidth=43;//wrapped cell width
			$cellHeight=5;//normal one-line cell height
			 //check whether the text is overflowing
			 if($pdf->GetStringWidth($actividad_provedor) < $cellWidth){
			  //if not, then do nothing
			  $line=1;
			 }else{
			  //if it is, then calculate the height needed for wrapped cell
			  //by splitting the text to fit the cell width
			  //then count how many lines are needed for the text to fit the cell
			   $line=2;
			  $textLength=strlen($actividad_provedor); //total text length
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
				$tmpString=substr($actividad_provedor,$startChar,$maxChar);
			   }
			
			//move startChar to next line
			   $startChar=$startChar+$maxChar;
			
			//then add it into the array so we know how many line are needed
			   array_push($textArray,$tmpString);
			 
			 //reset maxChar and tmpString
			   $maxChar=0;
			   $tmpString='';
			   
			  }
			  $descripcion = $row->descripcion;
			  	$novedad = $row->id_n;
			$id_cot = $row->id;
			$reportado = $row->reportado_otro;
			$solicitante = $row->solicitante;
			$proveedor = $row->nombre_prov;
			$actividad_provedor = $row->actividad_provedor;
			$encargado = $row->nombre.' '.$row->apellido;
			  
			  	$pdf->Cell(16,8,utf8_decode('ID'),1,0,'C',1);
				$pdf->Cell(30,8,utf8_decode('REPORTADO'),1,0,'C',1);
				$pdf->Cell(30,8,utf8_decode('SOLICITANTE'),1,0,'C',1);
				$pdf->Cell(26,8,utf8_decode('DESCRIPCI??N'),1,0,'C',1);
					$pdf->Cell(43.6,8,utf8_decode('PROVEEDOR'),1,0,'C',1);
				$pdf->Cell(43.5,8,utf8_decode('ACTIVIDAD PROVEEDOR'),1,0,'C',1);
			  //get number of line
			  $line=count($textArray);
			 }
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(16,($line * $cellHeight),utf8_decode($id_cot),1,0,'l',1);
			
			
			$pdf->Cell(30,($line * $cellHeight),utf8_decode($reportado),1,0,'C',1);
			$pdf->Cell(30,($line * $cellHeight),utf8_decode($solicitante),1,0,'C',1);
		    $pdf->Cell(26,($line * $cellHeight),utf8_decode($descripcion),1,0,'C',1);
			$pdf->MultiCell($cellWidth,$cellHeight,$proveedor, 1,  'C', true);
			$pdf->SetXY($x +26+24+13+ 21+18+ $cellWidth , $y);
		    $pdf->MultiCell($cellWidth , $cellHeight,$actividad_provedor, 1, 'C', true);
			//Go to next row
			$y_axis = $y_axis + $row_height;
			$i = $i + 1;
		}
		
	
				 
				 
			
		
		
      
		
		
	

			//If the current row is the last one, create new page and print column title
			if ($i == $max)
			{
				
				$max =14;
				$pdf->Cell(0,30,utf8_decode('P??gina').$pdf->PageNo().'/{nb}',0,0,'C');
				$pdf->AddPage();

		
			
				
				$y_axis = $y_axis + $row_height;
				
				$i = 0;
				
			}
					
	     }
	   

	   $pdf->setXY(10,$imagen_size);
	   
	   $pdf->Image('img/logo-footer-mail-AOA.jpg'); 
		
		
		
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',8);
		$pdf->SetFillColor(140,208,12);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(0,8,utf8_decode('P??gina').$pdf->PageNo().'/{nb}',0,0,'C');
		$pdf->AliasNbPages();
		
      
	       $pdf->Output(''.$nombre.'.pdf', 'D');

      header("location: ".$filename);

}
?>

