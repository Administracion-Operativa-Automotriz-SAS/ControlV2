<?php

/**
 *   CALCULO DE DISTRICUCION DE NUMERO DE SINIESTROS POR DIA RESULTADO EN EXCEL
 *
 * 	 variables que recibe:
 * 		FI  Fecha inicial
 * 		FF  Fecha final
 * 		T   Tabla temporal de datos
 * @version $Id$
 * @copyright 2009
 */
include('inc/funciones_.php');
require('inc/sess.php');
$Tabla_temporal='tmpi_'.$_SESSION['Id_alterno'].'_'.$T;
set_include_path(get_include_path() . PATH_SEPARATOR . './inc/Excel/');
include('inc/Excel/PHPExcel.php');
include('inc/Excel/PHPExcel/IOFactory.php');
$Tit=Array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ',
'CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ',
'DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ',
'EA','EB','EC','ED','EE','EF','EG','EH','EI','EJ','EK','EL','EM','EN','EO','EP','EQ','ER','ES','ET','EU','EV','EW','EX','EY','EZ',
'FA','FB','FC','FD','FE','FF','FG','FH','FI','FJ','FK','FL','FM','FN','FO','FP','FQ','FR','FS','FT','FU','FV','FW','FX','FY','FZ',
'GA','GB','GC','GD','GE','GF','GG','GH','GI','GJ','GK','GL','GM','GN','GO','GP','GQ','GR','GS','GT','GU','GV','GW','GX','GY','GZ',
'HA','HB','HC','HD','HE','HF','HG','HH','HI','HJ','HK','HL','HM','HN','HO','HP','HQ','HR','HS','HT','HU','HV','HW','HX','HY','HZ',
'IA','IB','IC','ID','IE','IF','IG','IH','II','IJ','IK','IL','IM','IN','IO','IP','IQ','IR','IS','IT','IU','IV','IW','IX','IY','IZ',
'JA','JB','JC','JD','JE','JF','JG','JH','JI','JJ','JK','JL','JM','JN','JO','JP','JQ','JR','JS','JT','JU','JV','JW','JX','JY','JZ',
'KA','KB','KC','KD','KE','KF','KG','KH','KI','KJ','KK','KL','KM','KN','KO','KP','KQ','KR','KS','KT','KU','KV','KW','KX','KY','KZ',
'LA','LB','LC','LD','LE','LF','LG','LH','LI','LJ','LK','LL','LM','LN','LO','LP','LQ','LR','LS','LT','LU','LV','LW','LX','LY','LZ',
'MA','MB','MC','MD','ME','MF','MG','MH','MI','MJ','MK','ML','MM','MN','MO','MP','MQ','MR','MS','MT','MU','MV','MW','MX','MY','MZ',
'NA','NB','NC','ND','NE','NF','NG','NH','NI','NJ','NK','NL','NM','NN','NO','NP','NQ','NR','NS','NT','NU','NV','NW','NX','NY','NZ',
'OA','OB','OC','OD','NE','OF','OG','OH','OI','OJ','OK','OL','OM','EN','OO','OP','OQ','OR','OS','OT','OU','OV','OW','OX','OY','OZ',
'PA','PB','PC','PD','PE','PF','PG','PH','PI','PJ','PK','PL','PM','PN','PO','PP','PQ','PR','PS','PT','PU','PV','PW','PX','PY','PZ',
'QA','QB','QC','QD','QE','QF','QG','QH','QI','QJ','QK','QL','QM','QN','QO','QP','QQ','QR','QS','QT','QU','QV','QW','QX','QY','QZ',
'RA','RB','RC','RD','RE','RF','RG','RH','RI','RJ','RK','RL','RM','RN','RO','RP','RQ','RR','RS','RT','RU','RV','RW','RX','RY','RZ',
'SA','SB','SC','SD','SE','SF','SG','SH','SI','SJ','SK','SL','SM','SN','SO','SP','SQ','SR','SS','ST','SU','SV','SW','SX','SY','SZ',
'TA','TB','TC','TD','TE','TF','TG','TH','TI','TJ','TK','TL','TM','TN','TO','TP','TQ','TR','TS','TT','TU','TV','TW','TX','TY','TZ',
'UA','UB','UC','UD','UE','UF','UG','UH','UI','UJ','UK','UL','UM','UN','UO','UP','UQ','UR','US','UT','UU','UV','UW','UX','UY','UZ',
'VA','VB','VC','VD','VE','VF','VG','VH','VI','VJ','VK','VL','VM','VN','VO','VP','VQ','VR','VS','VT','VU','VV','VW','VX','VY','VZ',
'WA','WB','WC','WD','WE','WF','WG','WH','WI','WJ','WK','WL','WM','WN','WO','WP','WQ','WR','WS','WT','WU','WV','WW','WX','WY','WZ',
'XA','XB','XC','XD','XE','XF','XG','XH','XI','XJ','XK','XL','XM','XN','XO','XP','XQ','XR','XS','XT','XU','XV','XW','XX','XY','XZ',
'YA','YB','YC','YD','YE','YF','YG','YH','YI','YJ','YK','YL','YM','YN','YO','YP','YQ','YR','YS','YT','YU','YV','YW','YX','YY','YZ',
'ZA','ZB','ZC','ZD','ZE','ZF','ZG','ZH','ZI','ZJ','ZK','ZL','ZM','ZN','ZO','ZP','ZQ','ZR','ZS','ZT','ZU','ZV','ZW','ZX','ZY','ZZ');
$E = new PHPExcel();
// propiedades de la hoja electrónica
$E->getProperties()->setCreator("Arturo Quintero");
$E->getProperties()->setLastModifiedBy("Arturo Quintero");
$E->getProperties()->setTitle("Documento Office 2007 XLSX");
$E->getProperties()->setSubject("Documento Office 2007 XLSX");
$E->getProperties()->setDescription("Distribucion de numero de siniestros por ciudad entre dos fechas");
$E->getProperties()->setKeywords("office 2007 openxml php");
$E->getProperties()->setCategory("Reportes Control");
// fija la hoja por defecto al abrir el archivo de excel
$E->setActiveSheetIndex(0);
// inserción de información en la hoja activa
$E->getActiveSheet()->setCellValue('A1', 'AOA COLOMBIA S.A.');
$E->getActiveSheet()->setCellValue('A2', "DISTRIBUCION DE NUMERO DE SINIESTROS");
$E->getActiveSheet()->setCellValue('A3', "FECHA DESDE $FI HASTA $FF ");
$E->getActiveSheet()->setCellValue('A5', "CIUDAD");
$E->getActiveSheet()->setCellValue('L1',date('Y-m-d'));
// recorrido de la tabla temporal para cada uno de los dias
if($Temporal=q("select * from $Tabla_temporal"))
{
	$Cantidad_ciudades=mysql_num_rows($Temporal);
	// titulos de las fechas
	$Fecha=date('Ymd',strtotime($FI));
	$Contador_titulo=2;
	while(date('Ymd',strtotime($Fecha))<=date('Ymd',strtotime($FF)))
	{
		$Celda=$Tit[$Contador_titulo].'4';
		$Celda1=$Tit[$Contador_titulo+1].'4';
		$Celdat1=$Tit[$Contador_titulo].'5';
		$Celdat2=$Tit[$Contador_titulo+1].'5';
		$Celdat=$Tit[$Contador_titulo].($Cantidad_ciudades+6);
		$E->getActiveSheet()->setCellValue($Celda,"$Fecha");
		$E->getActiveSheet()->getStyle($Celda.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$E->getActiveSheet()->mergecells("$Celda:$Celda1");
		$E->getActiveSheet()->setCellValue($Celdat1,'Cantidad');
		$E->getActiveSheet()->setCellValue($Celdat2,'%');
		$E->getActiveSheet()->setCellValue($Celdat,"=sum(".$Tit[$Contador_titulo].'6:'.$Tit[$Contador_titulo].($Cantidad_ciudades+5).")");
		$E->getActiveSheet()->getStyle($Celdat)->getFont()->setBold(true);
		$Fecha=aumentadias($Fecha,1);
		$Contador_titulo+=2;
	}
	$E->getActiveSheet()->setCellValue($Tit[$Contador_titulo].'4',"TOTAL");
	$E->getActiveSheet()->getStyle($Tit[$Contador_titulo].'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$E->getActiveSheet()->mergecells($Tit[$Contador_titulo].'4:'.$Tit[$Contador_titulo+1].'4');
	$E->getActiveSheet()->setCellValue($Tit[$Contador_titulo].'5','Cantidad');
	$E->getActiveSheet()->setCellValue($Tit[$Contador_titulo+1].'5','%');
	$E->getActiveSheet()->setCellValue($Tit[$Contador_titulo].($Cantidad_ciudades+6),"=sum(".$Tit[$Contador_titulo].'6:'.$Tit[$Contador_titulo].($Cantidad_ciudades+5).")");

	$Contador_ciudad=6;
	require('inc/link.php');
	while($T =mysql_fetch_object($Temporal ))
	{
		$E->getActiveSheet()->setCellValue("A$Contador_ciudad","$T->ci_nombre");
		$Fecha=date('Ymd',strtotime($FI));
		$Contador_fecha=2;
		while(date('Ymd',strtotime($Fecha))<=date('Ymd',strtotime($FF)))
		{
			if(!$Cantidad=mysql_query("select count(id) as cantidad from siniestro si where si.ciudad='$T->ci_codigo' and
				si.fec_autorizacion='".date('Y-m-d',strtotime($Fecha))."' ",$LINK))
			die(mysql_error($LINK));
			if($cantidad=mysql_fetch_object($Cantidad));
			{
				$Celda=$Tit[$Contador_fecha].$Contador_ciudad;
				$Celda1=$Tit[$Contador_fecha+1].$Contador_ciudad;
				$Celdat=$Tit[$Contador_fecha].($Cantidad_ciudades+6);
				$E->getActiveSheet()->setCellValue($Celda,"$cantidad->cantidad");
				$E->getActiveSheet()->setCellValue($Celda1,"=if($Celdat>0,$Celda/$Celdat,0)");
				$E->getActiveSheet()->getStyle($Celda1)->getNumberFormat()->setFormatCode('0.00%');
			}
			$Fecha=aumentadias($Fecha,1);
			$Contador_fecha+=2;
		}
		$Celda=$Tit[$Contador_fecha].$Contador_ciudad;
		$Celda1=$Tit[$Contador_fecha+1].$Contador_ciudad;
		$Celdat=$Tit[$Contador_fecha].($Cantidad_ciudades+6);
		$E->getActiveSheet()->setCellValue($Celda,$T->si_id);
		$E->getActiveSheet()->setCellValue($Celda1,"=if($Celdat>0,$Celda/$Celdat,0)");
		$E->getActiveSheet()->getStyle($Celda1)->getNumberFormat()->setFormatCode('0.00%');
		$Contador_ciudad++;
	}
	$E->getActiveSheet()->setCellValue("A$Contador_ciudad","TOTALES");
	$E->getActiveSheet()->getStyle("A$Contador_ciudad")->getFont()->setBold(true);
	mysql_close($LINK);
}
$E->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
$E->getActiveSheet()->mergecells('A1:D1');
$E->getActiveSheet()->mergecells('A2:D2');
$E->getActiveSheet()->mergecells('A3:D3');
$E->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$E->getActiveSheet()->duplicateStyle($E->getActiveSheet()->getStyle('A5'), 'B4:'.$Tit[$Contador_titulo+2].'5');
$E->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$E->getActiveSheet()->freezePane('B6');
// GRABACION DE LA HOJA
$Grabacion = PHPExcel_IOFactory::createWriter($E, 'Excel2007');
$ArchivoGrabacion="planos/Distribucion".$FI.$FF.".xlsx";
$Grabacion->save($ArchivoGrabacion);
header("location:$ArchivoGrabacion");
?>