<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
  require("inc/pdf/fpdf.php");

header('Content-Type: text/html; charset=utf-8');
include('inc/funciones_.php');


$id_novedad = $_GET["id_novedad"];
$encargado = $_GET["encargado"];
$nombre = $_GET["novedad"];


if($encargado){

 
  $novedad=qo("select  aoa_modulo.novedad.id_novedad as id_novedad,
		es_s.nombre as nombre_es_s,
		es_s.color_co as color_co_es_s,
c_s.nombre as ciudad_siniestro,c_s.departamento as departamento_siniestro,
      vehiculo.modelo  as modelo_vehiculo ,
					vehiculo.placa  as placa ,
					linea_vehiculo.nombre  as linea_vehiculo,
					marca_vehiculo.nombre  as marca_vehiculo,
   aoa_modulo.novedad.reportado as asegurado,
   s.n_poliza,s.id as id_aseguradora,s.aseguradora_nombre,s.linea_asistencia,
   aoa_modulo.novedad.depar_reporte_otro as depar_reporte_otro,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
    aoa_modulo.novedad.acti_caarga as acti_caarga,
     aoa_modulo.novedad.cierre_oper as cierre_oper,
      aoa_modulo.novedad.solicitante as solicitante,
   aoa_modulo.novedad.id_sinistro as id_sinistro,
   aoa_modulo.novedad.novedad as novedad, 
     aoa_modulo.novedad.encargado as encargado, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_asegurado,
   aoa_modulo.novedad.aseguradora_cliente as aseguradora_cliente, 
    aoa_modulo.novedad.id_placa as placa_cliente, 
     aoa_modulo.novedad.placa_aoa as placa_aoa, 
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_sinistro as id_sinistro,
   aoa_modulo.novedad.novedad as novedad,
   aoa_modulo.novedad.fecha_creacion as fecha_creacion, 
     aoa_modulo.novedad.ciudad_reporte_otro as ciudad_reporte, 
   aoa_modulo.novedad.email_reporte_otro as email_reporte,
   aoa_modulo.novedad.tele_reporte_otro as tele_reporte,
   aoa_modulo.novedad.reportado_otro as reportado, 
   aoa_modulo.novedad.cierre as cierre, 
    aoa_modulo.tipo_cierre.nombre_cierre as estado_cierre, 
     aoa_modulo.tipo_cierre.color_cierre as estado_color, 
 aoacol_aoacars.siniestro.clase as clase_vehiculo,
 aoacol_aoacars.siniestro.marca as marca_vehiculo_cliente,
 
 aoacol_aoacars.siniestro.numero as servicio_numero,
 aoacol_aoacars.siniestro.servicio as servicio_cliente,

 aoacol_aoacars.siniestro.linea as linea_vehiculo_cliente,
 aoacol_aoacars.siniestro.tipo as tipo_vehiculo_cliente,
  aoacol_aoacars.siniestro.modelo as modelo_vehiculo_cliente,
 aoacol_aoacars.siniestro.id as id_siniestro,
   aoa_modulo.novedad.tipo_cierre as tipo_cierre, 
   aoa_modulo.tipoNovedad.nombre as nombre_tipoN, 
    aoa_modulo.tipoNovedad.color as color_tipoN, 
  aoa_modulo.tipoNovedad.idtipoNovedad as id_nombre_tipoN, 
   aoacol_aoacars.operario.nombre as nombre_opera,
      aoacol_aoacars.operario.id as id_opera,
      a_c.nombre as nombre_aseguradora_cliente,
      
     a_a.nombre as nombre_aseguradora_aoa,
   aoacol_aoacars.operario.apellido as apellido_opera
   from  aoa_modulo.novedad  
   LEFT OUTER JOIN aoa_modulo.tipoNovedad
	on novedad.id_tipo = tipoNovedad.idtipoNovedad 
	 LEFT OUTER JOIN aoa_modulo.tipo_cierre
	on novedad.tipo_cierre = tipo_cierre.id_tipo_cierre 
	LEFT OUTER JOIN aoacol_aoacars.operario
   on  aoa_modulo.novedad.encargado = aoacol_aoacars.operario.id 
   LEFT OUTER JOIN aoacol_aoacars.siniestro on novedad.id_sinistro = siniestro.numero 
   
   	INNER JOIN aoacol_aoacars.ciudad c_s ON aoacol_aoacars.siniestro.ciudad_siniestro = c_s.codigo
	
INNER JOIN aoacol_aoacars.estado_siniestro  es_s ON aoacol_aoacars.siniestro.estado = es_s.id
	
   	LEFT OUTER JOIN aoacol_aoacars.vehiculo
		 on aoa_modulo.novedad.placa_aoa  = vehiculo.placa 
	  inner join aoacol_aoacars.seguros s on aoacol_aoacars.vehiculo.n_poliza = s.id
	    inner join aoacol_aoacars.aseguradora a_c on aoa_modulo.novedad.aseguradora_cliente = a_c.id
					LEFT OUTER JOIN aoacol_aoacars.linea_vehiculo ON  vehiculo.linea  = linea_vehiculo.id 
			inner join aoacol_aoacars.aseguradora a_a on aoacol_aoacars.vehiculo.flota = a_a.id 		
					LEFT OUTER JOIN aoacol_aoacars.marca_vehiculo ON  linea_vehiculo.marca  = marca_vehiculo.id 
		WHERE  aoa_modulo.novedad.id_novedad   ='$id_novedad ' ");
					
					
     }else{


  $novedad=qo("select  aoa_modulo.novedad.id_novedad as id_novedad,

      vehiculo.modelo  as modelo_vehiculo ,
					vehiculo.placa  as placa ,
					linea_vehiculo.nombre  as linea_vehiculo,
					marca_vehiculo.nombre  as marca_vehiculo,
   aoa_modulo.novedad.reportado as asegurado,
   aoa_modulo.novedad.depar_reporte_otro as depar_reporte_otro,
   aoa_modulo.novedad.tele_reporte as tele_asegurado,
   aoa_modulo.novedad.email_reporte as email_asegurado,
    aoa_modulo.novedad.acti_caarga as acti_caarga,
     aoa_modulo.novedad.cierre_oper as cierre_oper,
      aoa_modulo.novedad.solicitante as solicitante,
   aoa_modulo.novedad.id_sinistro as id_sinistro,
   aoa_modulo.novedad.novedad as novedad, 
     aoa_modulo.novedad.encargado as encargado, 
   aoa_modulo.novedad.ciudad_reporte as ciudad_asegurado,
       aoacol_aoacars.operario.email as email_encargado,
   aoa_modulo.novedad.solicitante as solicitante, 
   aoa_modulo.novedad.id_sinistro as id_sinistro,
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
   LEFT OUTER JOIN aoacol_aoacars.siniestro on novedad.id_sinistro = siniestro.numero 
   	LEFT OUTER JOIN aoacol_aoacars.vehiculo
		 on aoacol_aoacars.siniestro.placa = vehiculo.placa 
	  
					LEFT OUTER JOIN aoacol_aoacars.linea_vehiculo ON  vehiculo.linea  = linea_vehiculo.id 
					LEFT OUTER JOIN aoacol_aoacars.marca_vehiculo ON  linea_vehiculo.marca  = marca_vehiculo.id 
		WHERE  aoa_modulo.novedad.id_novedad  ='$id_novedad ' ");
		
		
		
	 }
	 
	 
	 
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
			 
			 
			 if($Detalle == null){
				 
				 
				 
				 
				 
			 }

	    $i = 0;

		$max = 7;

		$row_height = 10;
		$y_axis_initial = 212;
		
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
		$pdf->Text(16,69,utf8_decode("ACTA DE NOVEDAD #$novedad->id_novedad DEL SINIESTRO #$novedad->id_sinistro "));
		

    
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
			$pdf->Cell(59,5,utf8_decode($novedad->fecha_creacion),1,1,'C');
			$pdf->SetFont('Arial','B',7);


	$pdf->setXY(10,90);
		$pdf->Cell(36,5,utf8_decode('NOVEDAD  '),1,1,'C',1);
		
		$pdf->setXY(46,90);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(55,5,utf8_decode($novedad->novedad),1,1,'C');
		
		$pdf->setXY(101,90);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(40,5,utf8_decode('Tipo de novedad'),1,1,'C',1);
			
		$pdf->setXY(141,90);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($novedad->nombre_tipoN),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
		


	$pdf->setXY(10,95);
		$pdf->Cell(36,5,utf8_decode('ASEGURADO  '),1,1,'C',1);
		
		$pdf->setXY(46,95);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(55,5,utf8_decode($novedad->asegurado),1,1,'C');
		
		$pdf->setXY(101,95);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(40,5,utf8_decode('TELÉFONO ASEGURADO'),1,1,'C',1);
			
		$pdf->setXY(141,95);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($novedad->tele_asegurado),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
			

			
	$pdf->setXY(10,100);
		$pdf->Cell(36,5,utf8_decode('CORREO ASEGURADO  '),1,1,'C',1);
		
		$pdf->setXY(46,100);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(55,5,utf8_decode($novedad->email_asegurado),1,1,'C');
		
		$pdf->setXY(101,100);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(40,5,utf8_decode('CIUDAD ASEGURADO'),1,1,'C',1);
			
		$pdf->setXY(141,100);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(59,5,utf8_decode($novedad->ciudad_asegurado),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
				
			
			
			$pdf->setXY(10,114);
	
	$pdf->SetFont('Arial','B',8);
	$pdf->setXY(10,40);
	$pdf->SetFillColor(187,224,67);
		
	    $pdf->setXY(10,114);
		$pdf->Cell(190,7,utf8_decode('REPORTADO POR'),1,1,'C',1);
		
	$pdf->SetFillColor(	215, 240, 127);	
	$pdf->setXY(10,121);
	    $pdf->SetFont('Arial','B',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(30,5,utf8_decode('REPORTADO'),1,1,'C',1);
		
	$pdf->setXY(40,121);
		$pdf->SetFont('Arial','B',7);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(30,5,utf8_decode($novedad->reportado),1,1,'C');
			
		
		$pdf->setXY(70,121);
			$pdf->Cell(30,5,utf8_decode('TELÉFONO REPORTADO'),1,1,'C',1);
			
		$pdf->setXY(100,121);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(33,5,utf8_decode($novedad->tele_reporte),1,1,'C');
			$pdf->SetFont('Arial','B',7);

            
        	$pdf->setXY(133,121);
			$pdf->Cell(28,5,utf8_decode('DEPARTAMENTO '),1,1,'C',1);
			
		$pdf->setXY(161,121);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(39,5,utf8_decode($novedad->depar_reporte_otro),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
			

	$pdf->setXY(10,126);
		$pdf->Cell(50,5,utf8_decode('CIUDAD'),1,1,'C',1);
		
		$pdf->setXY(60,126);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(40,5,utf8_decode($novedad->ciudad_reporte),1,1,'C');
		
		$pdf->setXY(100,126);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(40,5,utf8_decode('EMAIL REPORTADO'),1,1,'C',1);
			
		$pdf->setXY(140,126);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(60,5,utf8_decode($novedad->email_reporte),1,1,'C');
			$pdf->SetFont('Arial','B',7);
			
			
		$pdf->setXY(10,131);
		$pdf->Cell(50,5,utf8_decode('ENCARGADO '),1,1,'C',1);
		
		$pdf->setXY(60,131);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(40,5,number_format($novedad->encargado),1,1,'C');
		
		$pdf->setXY(100,131);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(40,5,utf8_decode('EMAIL ENCARGADO '),1,1,'C',1);
			
		$pdf->setXY(140,131);
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(60,5,utf8_decode($novedad->email_encargado),1,1,'C');
			$pdf->SetFont('Arial','B',7);	
	
			
			
		
			
			
			
		$pdf->setXY(10,146);
		$pdf->SetFont('Arial','B',8);
	    $pdf->SetFillColor(187,224,67);
		$pdf->Cell(190,7,utf8_decode('INFORMACIÓN DEL VEHÍCULO'),1,1,'C',1);
		
	    $pdf->SetFillColor(215, 240, 127);
	    $pdf->setXY(10,153);
		$pdf->Cell(45,5,utf8_decode('PLACA'),1,1,'C',1);
		
		$pdf->setXY(55,153);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad->placa),1,1,'C');
			
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,153);
		$pdf->Cell(41,5,utf8_decode('MARCA'),1,1,'C',1);
		$pdf->setXY(141,153);
			$pdf->Cell(59,5,utf8_decode($novedad->marca_vehiculo),1,1,'C');
		
        $pdf->setXY(10,158);
		$pdf->Cell(45,5,utf8_decode('MODELO'),1,1,'C',1);
		
		$pdf->setXY(55,158);
		
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad->modelo_vehiculo),1,1,'C');
			
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,158);
		$pdf->Cell(41,5,utf8_decode('LINEA'),1,1,'C',1);
		$pdf->setXY(141,158);
			$pdf->Cell(59,5,utf8_decode($novedad->linea_vehiculo),1,1,'C');
					
		$pdf->setXY(10,163);
		$pdf->Cell(45,5,utf8_decode('USUARIO SINIESTRO'),1,1,'C',1);
		
		$pdf->setXY(55,163);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad->asegurado),1,1,'C');
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,163);
		$pdf->Cell(41,5,utf8_decode('CIUDAD'),1,1,'C',1);
		$pdf->setXY(141,163);
		$pdf->Cell(59,5,utf8_decode($novedad->ciudad_asegurado),1,1,'C');
			
	

			$pdf->setXY(10,178);
		$pdf->SetFont('Arial','B',8);
	    $pdf->SetFillColor(187,224,67);
		$pdf->Cell(190,7,utf8_decode('INFORMACIÓN DEL VEHÍCULO'),1,1,'C',1);
		
	    $pdf->SetFillColor(215, 240, 127);
		
		$pdf->setXY(10,185);
		$pdf->Cell(45,5,utf8_decode('ESTADO DE CIERRE'),1,1,'C',1);
		
		$pdf->setXY(55,185);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad->estado_cierre),1,1,'C');
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,185);
		$pdf->Cell(41,5,utf8_decode('ACTIVIDAD ENCARGADO'),1,1,'C',1);
		$pdf->setXY(141,185);
		$pdf->Cell(59,5,utf8_decode($novedad->acti_caarga),1,1,'C');
		
		
		$pdf->setXY(10,190);
		$pdf->Cell(45,5,utf8_decode('CIERRE PROVEEDOR'),1,1,'C',1);
		
		$pdf->setXY(55,190);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(45,5,utf8_decode($novedad->cierre_oper),1,1,'C');
		$pdf->SetFont('Arial','B',7);
		$pdf->setXY(100,190);
		$pdf->Cell(41,5,utf8_decode('CUIDAD'),1,1,'C',1);
		$pdf->setXY(141,190);
		$pdf->Cell(59,5,utf8_decode($novedad->ciudad_asegurado),1,1,'C');
		
		
		
		
		
		
		
	$pdf->setXY(10,205);
		$pdf->SetFont('Arial','B',8);
	$pdf->SetFillColor(187,224,67);
		$pdf->Cell(189,7,utf8_decode('DETALLE DE LA REQUISICIÓN'),1,1,'C',1);


	
		//Add first page
       
    $pdf->SetFillColor(215, 240, 127);
		$pdf->SetFont('Arial','B',6);
		$pdf->SetY($y_axis_initial);
		$pdf->SetX(10);
		$pdf->Cell(10,8,utf8_decode('ID'),1,0,'C',1);
				$pdf->Cell(24,8,utf8_decode('ENCARGADO'),1,0,'C',1);
				$pdf->Cell(24,8,utf8_decode('REPORTADO'),1,0,'C',1);
				$pdf->Cell(24,8,utf8_decode('SOLICITANTE'),1,0,'C',1);
				$pdf->Cell(20,8,utf8_decode('DESCRIPCIÓN'),1,0,'C',1);
					$pdf->Cell(43.5,8,utf8_decode('PROVEEDOR'),1,0,'C',1);
				$pdf->Cell(43.5,8,utf8_decode('ACTIVIDAD PROVEEDOR'),1,0,'C',1);

		
		$pdf->Ln(8);

		
		
		 if(!$Detalle == null){
				 
				 
				 while($row = mysql_fetch_object($Detalle))
		{
			
			 
	
			//If the current row is the last one, create new page and print column title
			if ($i == $max)
			{
				
				$max =14;
				$pdf->Cell(0,30,utf8_decode('Página').$pdf->PageNo().'/{nb}',0,0,'C');
				$pdf->AddPage();

				//print column titles for the current page
				$pdf->SetFillColor(215, 240, 127);
				$pdf->SetFont('Arial','B',6);
				$pdf->SetY(20);
				$pdf->SetX(10);
				$pdf->Cell(10,8,utf8_decode('ID'),1,0,'C',1);
				$pdf->Cell(24,8,utf8_decode('ENCARGADO'),1,0,'C',1);
				$pdf->Cell(24,8,utf8_decode('REPORTADO'),1,0,'C',1);
				$pdf->Cell(24,8,utf8_decode('SOLICITANTE'),1,0,'C',1);
				$pdf->Cell(20,8,utf8_decode('DESCRIPCIÓN'),1,0,'C',1);
				$pdf->Cell(43.5,8,utf8_decode('PROVEEDOR'),1,0,'C',1);
				$pdf->Cell(43.5,8,utf8_decode('ACTIVIDAD PROVEEDOR'),1,0,'C',1);
                $pdf->Ln(8);
				$y_axis_initial = 10;
			
				
				$y_axis = $y_axis + $row_height;
				
				$i = 1;
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
			  
			  	$pdf->Cell(10,8,utf8_decode('ID'),1,0,'C',1);
				$pdf->Cell(24,8,utf8_decode('ENCARGADO'),1,0,'C',1);
				$pdf->Cell(24,8,utf8_decode('REPORTADO'),1,0,'C',1);
				$pdf->Cell(24,8,utf8_decode('SOLICITANTE'),1,0,'C',1);
				$pdf->Cell(20,8,utf8_decode('DESCRIPCIÓN'),1,0,'C',1);
					$pdf->Cell(43.5,8,utf8_decode('PROVEEDOR'),1,0,'C',1);
				$pdf->Cell(43.5,8,utf8_decode('ACTIVIDAD PROVEEDOR'),1,0,'C',1);
			  //get number of line
			  $line=count($textArray);
			 }
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(10,($line * $cellHeight),utf8_decode($id_cot),1,0,'l',1);
			
			$pdf->Cell(24,($line * $cellHeight),utf8_decode($encargado),1,0,'C',1);
			$pdf->Cell(24,($line * $cellHeight),utf8_decode($reportado),1,0,'C',1);
			$pdf->Cell(24,($line * $cellHeight),utf8_decode($solicitante),1,0,'C',1);
		    $pdf->Cell(20,($line * $cellHeight),utf8_decode($descripcion),1,0,'C',1);
			$pdf->MultiCell($cellWidth,$cellHeight,$proveedor, 1,  'C', true);
			$pdf->SetXY($x +26+24+13+ 21+18+ $cellWidth , $y);
		    $pdf->MultiCell($cellWidth , $cellHeight,$actividad_provedor, 1, 'C', true);
			//Go to next row
			$y_axis = $y_axis + $row_height;
			$i = $i + 1;
		}
		
	
				 
				 
			 }
		
		
      
		
		
	

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
		
		
		$pdf->setXY(10,275);
		
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Arial','',8);
		$pdf->SetFillColor(140,208,12);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(0,10,utf8_decode('Página').$pdf->PageNo().'/{nb}',0,0,'C');
		$pdf->AliasNbPages();
		
      
	  $pdf->Output(''.$nombre.'.pdf', 'D');

      header("location: ".$filename);
     
?>

