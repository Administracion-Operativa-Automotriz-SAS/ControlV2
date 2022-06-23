<?php
/*
	PROGRAMA PARA OBTENER DATOS DE CITAS EN EXCEL.
*/

include('inc/funciones_.php');
include('inc/Excel/PHPExcel.php');
include('inc/Excel/PHPExcel/IOFactory.php');
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');die();}

function devoluciones()
{
	error_reporting(E_ALL);
	// include('inc/Excel/PHPExcel.php');
	// include('inc/Excel/PHPExcel/IOFactory.php');
	sesion();
	//print_r($_SESSION['excel_citas_devoluciones']);
	$estilo=array(
		'font'=>array('bold'=>true),
		'alignment'=>array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	);
		
	$firma=array(
		'borders'=>array(
			'top'=>array(
				'style'=>PHPExcel_Style_Border::BORDER_THICK,
				'color'=>array('argb'=>'00000000'),
				'alignment'=>array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
			)
		)
	);	
	
	$bordes_todo=array(
		'borders'=>array(
			'allborders'=>array(
				'style'=>PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);
		
	$H=new PHPExcel();
	$H->getActiveSheet()->setTitle('Devol Programadas');
	$H->getActiveSheet()->setCellValue('A1','DEVOLUCIONES PROGRAMADAS');
	$H->getActiveSheet()->mergeCells('A1:J1');
	$H->getActiveSheet()->setCellValue('A2','#');
	$H->getActiveSheet()->setCellValue('B2','PLACA');
	$H->getActiveSheet()->setCellValue('C2','CIUDAD');
	$H->getActiveSheet()->setCellValue('D2','CLIENTE');
	$H->getActiveSheet()->setCellValue('E2','SINIESTRO');
	$H->getActiveSheet()->setCellValue('F2','PLACA SINIESTRO');
	$H->getActiveSheet()->setCellValue('G2','FECHA DEV');
	$H->getActiveSheet()->setCellValue('H2','TIPO');
	$H->getActiveSheet()->setCellValue('I2','ESTADO');
	$H->getActiveSheet()->setCellValue('J2','OBSERVACIONES');
	$H->getActiveSheet()->setCellValue('K2','OK');
	$H->getActiveSheet()->getStyle('A1:H2')->applyFromArray($estilo);
	$H->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$Contador=2;
	foreach($_SESSION['excel_citas_devoluciones'] as $Linea)
	{
		$Contador++;
		$H->getActiveSheet()->setCellValue('A'.$Contador,$Linea['numero']);
		$H->getActiveSheet()->setCellValue('B'.$Contador,$Linea['placa']);
		$H->getActiveSheet()->setCellValue('C'.$Contador,$Linea['ciudad']);
		$H->getActiveSheet()->setCellValue('D'.$Contador,utf8_encode($Linea['cliente']));
		$H->getActiveSheet()->setCellValue('E'.$Contador,$Linea['siniestro']);
		$H->getActiveSheet()->setCellValue('F'.$Contador,$Linea['placa_siniestro']);
		$H->getActiveSheet()->setCellValue('G'.$Contador,$Linea['fecha']);
		$H->getActiveSheet()->setCellValue('H'.$Contador,'DEVOLUCION');
		$H->getActiveSheet()->setCellValue('I'.$Contador,$Linea['estado']);
		
	}
	$Contador+=10;
	$H->getActiveSheet()->getStyle('A2:K'.$Contador)->applyFromArray($bordes_todo);
	$Contador+=5;
	$H->getActiveSheet()->setCellValue('A'.$Contador,'ENTREGA');
	$H->getActiveSheet()->mergeCells('A'.$Contador.':C'.$Contador);
	$H->getActiveSheet()->getStyle('A'.$Contador)->applyFromArray($firma);

	$H->getActiveSheet()->setCellValue('E'.$Contador,'RECIBE');
	$H->getActiveSheet()->mergeCells('E'.$Contador.':G'.$Contador);
	$H->getActiveSheet()->getStyle('E'.$Contador)->applyFromArray($firma);
	$W=new PHPExcel_Writer_Excel5($H);
	$W->save('planos/devoluciones.xls');
	header('location:planos/devoluciones.xls');

}

function entregas()
{
	
	error_reporting(E_ALL);
	sesion();
	
	$estilo=array(
		'font'=>array('bold'=>true),
		'alignment'=>array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
	);
		
	$firma=array(
		'borders'=>array(
			'top'=>array(
				'style'=>PHPExcel_Style_Border::BORDER_THICK,
				'color'=>array('argb'=>'00000000'),
				'alignment'=>array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
			)
		)
	);	
		
	$bordes_todo=array(
		'borders'=>array(
			'allborders'=>array(
				'style'=>PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);
	
	$H=new PHPExcel();
	$H->getActiveSheet()->setTitle('Entr y Devol Cumplidas');
	$H->getActiveSheet()->setCellValue('A1','ENTREGAS Y DEVOLUCIONES CUMPLIDAS');
	$H->getActiveSheet()->mergeCells('A1:J1');
	$H->getActiveSheet()->setCellValue('A2','#');
	$H->getActiveSheet()->setCellValue('B2','PLACA');
	$H->getActiveSheet()->setCellValue('C2','CIUDAD');
	$H->getActiveSheet()->setCellValue('D2','CLIENTE');
	$H->getActiveSheet()->setCellValue('E2','SINIESTRO');
	$H->getActiveSheet()->setCellValue('F2','PLACA SINIESTRO');
	$H->getActiveSheet()->setCellValue('G2','FECHA');
	$H->getActiveSheet()->setCellValue('H2','TIPO');
	$H->getActiveSheet()->setCellValue('I2','ESTADO');
	$H->getActiveSheet()->setCellValue('J2','OBSERVACIONES');
	$H->getActiveSheet()->setCellValue('K2','OK');
	$H->getActiveSheet()->setCellValue('L2','IMG ODOMETRO');
	$H->getActiveSheet()->setCellValue('M2','IMG ACTA DE ENTREGA');
	$H->getActiveSheet()->setCellValue('N2','IMG FRONTAL');
	$H->getActiveSheet()->setCellValue('O2','IMG LATERAL IZQUIERDO');
	$H->getActiveSheet()->setCellValue('P2','IMG LATERAL DERECHO');
	$H->getActiveSheet()->setCellValue('Q2','IMG POSTERIO');
	
	$H->getActiveSheet()->setCellValue('R2','IMG CEDULA FRONTAL');
	$H->getActiveSheet()->setCellValue('S2','IMG CEDULA REVERSO');
	$H->getActiveSheet()->setCellValue('T2','IMG LICENCIA FRONTAL');
	$H->getActiveSheet()->setCellValue('U2','IMG LICENCIA REVERSO');
	$H->getActiveSheet()->setCellValue('V2','IMG ENCUESTA');
	
	$H->getActiveSheet()->getStyle('A1:H2')->applyFromArray($estilo);
	$H->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
	$H->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
	
	$Contador=2;
	foreach($_SESSION['excel_citas_entregas'] as $Linea)
	{
		$Contador++;
		$H->getActiveSheet()->setCellValue('A'.$Contador,$Linea['numero']);
		$H->getActiveSheet()->setCellValue('B'.$Contador,$Linea['placa']);
		$H->getActiveSheet()->setCellValue('C'.$Contador,$Linea['ciudad']);
		$H->getActiveSheet()->setCellValue('D'.$Contador,utf8_encode($Linea['cliente']));
		$H->getActiveSheet()->setCellValue('E'.$Contador,$Linea['siniestro']);
		$H->getActiveSheet()->setCellValue('F'.$Contador,$Linea['placa_siniestro']);
		$H->getActiveSheet()->setCellValue('G'.$Contador,$Linea['fecha']);
		$H->getActiveSheet()->setCellValue('H'.$Contador,'ENTREGA');
		$H->getActiveSheet()->setCellValue('K'.$Contador,$Linea['estado']);
		$H->getActiveSheet()->setCellValue('L'.$Contador,$Linea['odometro']);
		$H->getActiveSheet()->setCellValue('M'.$Contador,$Linea['acta_entrega']);
		$H->getActiveSheet()->setCellValue('N'.$Contador,$Linea['frente_vehiculo']);
		$H->getActiveSheet()->setCellValue('O'.$Contador,$Linea['izquierda_vehiculo']);
		$H->getActiveSheet()->setCellValue('P'.$Contador,$Linea['derecha_vehiculo']);
		$H->getActiveSheet()->setCellValue('Q'.$Contador,$Linea['atras']);
		$H->getActiveSheet()->setCellValue('R'.$Contador,$Linea['img_cedula_frontal']);
		$H->getActiveSheet()->setCellValue('S'.$Contador,$Linea['img_cedula_reverso']);
		$H->getActiveSheet()->setCellValue('T'.$Contador,$Linea['img_licencia_frontal']);
		$H->getActiveSheet()->setCellValue('U'.$Contador,$Linea['img_licencia_reverso']);
		
	}
	$Contador+=2;
	foreach($_SESSION['excel_citas_devoluciones'] as $Linea)
	{
		$Contador++;
		$H->getActiveSheet()->setCellValue('A'.$Contador,$Linea['numero']);
		$H->getActiveSheet()->setCellValue('B'.$Contador,$Linea['placa']);
		$H->getActiveSheet()->setCellValue('C'.$Contador,$Linea['ciudad']);
		$H->getActiveSheet()->setCellValue('D'.$Contador,utf8_encode($Linea['cliente']));
		$H->getActiveSheet()->setCellValue('E'.$Contador,$Linea['siniestro']);
		$H->getActiveSheet()->setCellValue('F'.$Contador,$Linea['placa_siniestro']);
		$H->getActiveSheet()->setCellValue('G'.$Contador,$Linea['fecha']);
		$H->getActiveSheet()->setCellValue('H'.$Contador,'DEVOLUCION');
		$H->getActiveSheet()->setCellValue('K'.$Contador,$Linea['estado']);
		$H->getActiveSheet()->setCellValue('L'.$Contador,$Linea['odometro']);
		$H->getActiveSheet()->setCellValue('M'.$Contador,$Linea['acta_entrega']);
		$H->getActiveSheet()->setCellValue('N'.$Contador,$Linea['frente_vehiculo']);
		$H->getActiveSheet()->setCellValue('O'.$Contador,$Linea['izquierda_vehiculo']);
		$H->getActiveSheet()->setCellValue('P'.$Contador,$Linea['derecha_vehiculo']);
		$H->getActiveSheet()->setCellValue('U'.$Contador,$Linea['atras']);
		$H->getActiveSheet()->setCellValue('V'.$Contador,$Linea['encuesta']);
		
		
	}
	$Contador+=10;
	$H->getActiveSheet()->getStyle('A2:J'.$Contador)->applyFromArray($bordes_todo);
	$Contador+=5;
	$H->getActiveSheet()->setCellValue('A'.$Contador,'ENTREGA');
	$H->getActiveSheet()->mergeCells('A'.$Contador.':C'.$Contador);
	$H->getActiveSheet()->getStyle('A'.$Contador)->applyFromArray($firma);

	$H->getActiveSheet()->setCellValue('E'.$Contador,'RECIBE');
	$H->getActiveSheet()->mergeCells('E'.$Contador.':G'.$Contador);
	$H->getActiveSheet()->getStyle('E'.$Contador)->applyFromArray($firma);
	$W=new PHPExcel_Writer_Excel5($H);
	$W->save('planos/entregasydevoluciones.xls');
	header('location:planos/entregasydevoluciones.xls');
}
?>