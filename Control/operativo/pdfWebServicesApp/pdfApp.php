<?php
		/*Imagenes de carro rayado*/
		$imgAutoRayado = $this->request['imgAutoRayado'];
		$FirmaArrendatario = $this->request['FirmaArrendatario'];
		$firmaTarjetaHabiente = $this->request['firmaTarjetaHabiente'];
		$firmaPersonaJuridica  = $this->request['firmaPersonaJuridica'];
		$firmaDevolucion = $this->request['firmaDevolucion'];
		/*Exterior vehiculo*/
		$emblemasPoll = $this->request['emblemasPoll'];$emblemasPollDes = $this->request['emblemasPollDes'];
		$copasPoll = $this->request['copasPoll'];$copasPollDes = $this->request['copasPollDes'];
		$antenaRadio = $this->request['antenaRadio'];$antenaRadioDes = $this->request['antenaRadioDes'];
		$limpiaParabrisas = $this->request['limpiaParabrisas'];$limpiaParabrisasDes = $this->request['limpiaParabrisasDes'];
		$niveles = $this->request['niveles'];$nivelesDes = $this->request['nivelesDes'];
		$lucesAltasYbajas = $this->request['lucesAltasYbajas'];$lucesAltasYbajasDes = $this->request['lucesAltasYbajasDes'];
		$direcionales = $this->request['direcionales'];$direcionalesDes = $this->request['direcionalesDes'];
		$luzReversaFrenoPlaca = $this->request['luzReversaFrenoPlaca'];  $luzReversaFrenoPlacaDes = $this->request['luzReversaFrenoPlacaDes'];
		$tapaCombustible = $this->request['tapaCombustible'];$tapaCombustibleDes = $this->request['tapaCombustibleDes'];
		/*Baul vehiculo*/
		
		$gatoYGanchoArrastre = $this->request['gatoYGanchoArrastre'];$gatoYGanchoArrastreDes = $this->request['gatoYGanchoArrastreDes'];
		$cruceta = $this->request['cruceta'];$crucetaDes = $this->request['crucetaDes'];
		$repusto = $this->request['repusto'];$repustoDes = $this->request['repustoDes'];
		$kitCarretera = $this->request['kitCarretera'];$kitCarreteraDes = $this->request['kitCarreteraDes'];
		/*Interior*/
		$tapetes = $this->request['tapetes'];$tapetesDes = $this->request['tapetesDes'];
		$cinturonSeguridad = $this->request['cinturonSeguridad'];$cinturonSeguridadDes = $this->request['cinturonSeguridadDes'];
		$espejosLateralesRetrovisor = $this->request['espejosLateralesRetrovisor'];$espejosLateralesRetrovisorDes = $this->request['espejosLateralesRetrovisorDes'];
		$luzCortesia = $this->request['luzCortesia'];$luzCortesiaDes = $this->request['luzCortesiaDes'];
		$radio  = $this->request['radio'];$radioDes  = $this->request['radioDes'];
		$pito  = $this->request['pito'];$pitoDes  = $this->request['pitoDes'];
		$bloqueoCentral  = $this->request['bloqueoCentral'];$bloqueoCentralDes  = $this->request['bloqueoCentralDes'];
		$elevaVidriosDelanteros  = $this->request['elevaVidriosDelanteros'];$elevaVidriosDelanterosDes  = $this->request['elevaVidriosDelanterosDes'];
		$calefaccionYaireAcondicionado = $this->request['calefaccionYaireAcondicionado'];$calefaccionYaireAcondicionadoDes = $this->request['calefaccionYaireAcondicionadoDes'];
		$encenderYCenicero = $this->request['encenderYCenicero'];$encenderYCeniceroDes = $this->request['encenderYCeniceroDes'];
		/*Documentacion*/
		$tarjetaPropiedad = $this->request['tarjetaPropiedad'];$tarjetaPropiedadDes = $this->request['tarjetaPropiedadDes'];
		$soatVijente = $this->request['soatVijente'];$soatVijenteDes = $this->request['soatVijenteDes'];
		$lineaAsistencia = $this->request['lineaAsistencia'];$lineaAsistenciaDes = $this->request['lineaAsistenciaDes'];
		$manualesGarantia = $this->request['manualesGarantia'];$manualesGarantiaDes = $this->request['manualesGarantiaDes'];
		$contrato = $this->request['contrato'];$contratoDes = $this->request['contratoDes'];
		
		/*Medidor de gasolina*/
		
		$medidorGasolina = $this->request['medidorGasolina'];
		
		
		if($validarTablaPdf == 1){
			$tabla = "encuesta_acta_entrega";
		}else{
			$tabla = "encuesta_acta_devolucion";
		}


/*Query insert into*/
$sql = "INSERT INTO $tabla (idCita,emblemasPoll,emblemasPollDes,copasPoll,copasPollDes,
	  antenaRadio,antenaRadioDes,
	  limpiaParabrisas,limpiaParabrisasDes,niveles,nivelesDes,lucesAltasYbajas,lucesAltasYbajasDes,
	  direcionales,
	  direcionalesDes,
	  luzReversaFrenoPlaca,luzReversaFrenoPlacaDes,tapaCombustible,tapaCombustibleDes,gatoYGanchoArrastre,
	  gatoYGanchoArrastreDes,
	  cruceta,crucetaDes,repusto,repustoDes,kitCarretera,kitCarreteraDes,
	  tapetes,tapetesDes,cinturonSeguridad,cinturonSeguridadDes,espejosLateralesRetrovisor,
	  espejosLateralesRetrovisorDes,
	  luzCortesia,luzCortesiaDes,pito,pitoDes,radio,radioDes,bloqueoCentral,bloqueoCentralDes,
	  elevaVidriosDelanteros,
	  elevaVidriosDelanterosDes,encenderYCenicero,encenderYCeniceroDes,tarjetaPropiedad,
	  tarjetaPropiedadDes,soatVijente,soatVijenteDes,lineaAsistencia,lineaAsistenciaDes,
	  manualesGarantia,manualesGarantiaDes,contrato,contratoDes,
	  calefaccionYaireAcondicionado,
	  calefaccionYaireAcondicionadoDes)
	  
	  values ($idc,'$emblemasPoll','$emblemasPollDes','$copasPoll','$copasPollDes','$antenaRadio',
	  '$antenaRadioDes','$limpiaParabrisas',
	  '$limpiaParabrisasDes','$niveles','$nivelesDes','$lucesAltasYbajas','$lucesAltasYbajasDes',
	  '$direcionales','$direcionalesDes',
	  '$luzReversaFrenoPlaca','$luzReversaFrenoPlacaDes','$tapaCombustible','$tapaCombustibleDes',
	  '$gatoYGanchoArrastre',
	  '$gatoYGanchoArrastreDes','$cruceta','$crucetaDes','$repusto','$repustoDes','$kitCarretera',
	  '$kitCarreteraDes',
	  '$tapetes','$tapetesDes','$cinturonSeguridad','$cinturonSeguridadDes','$espejosLateralesRetrovisor',
	  '$espejosLateralesRetrovisorDes',
	  '$luzCortesia','$luzCortesiaDes',
	  '$pito',
	  '$pitoDes',
	  '$radio','$radioDes',
	  '$bloqueoCentral',
	  '$bloqueoCentralDes','$elevaVidriosDelanteros',
	  '$elevaVidriosDelanterosDes',
	  '$encenderYCenicero','$encenderYCeniceroDes','$tarjetaPropiedad','$tarjetaPropiedadDes',
	  '$soatVijente','$soatVijenteDes','$lineaAsistencia','$lineaAsistenciaDes','$manualesGarantia',
	  '$manualesGarantiaDes','$contrato','$contratoDes','$calefaccionYaireAcondicionado',
	  '$calefaccionYaireAcondicionadoDes')";
	  
 q($sql);

$sqlEncuesta = "SELECT en.* FROM cita_servicio ci
				 INNER JOIN encuesta_acta_entrega en 
				 ON ci.id = en.idCita
				 WHERE en.idCita = $idc";
$consultaEncuesta = qo($sqlEncuesta);

$sqlEncuestaDe = "SELECT en.* FROM cita_servicio ci
				 INNER JOIN encuesta_acta_devolucion en 
				 ON ci.id = en.idCita
				 WHERE en.idCita = $idc";
$consultaEncuestaDe = qo($sqlEncuestaDe);


       $hoy2 = date("YmdHis");
	   $ruta = directorio_imagen('lineavehiculo/vhActaEntrega',$idc);
	   
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgAutoRayado));
       $filepath = $ruta."$idc"."_convencion_"."$hoy2".".jpg";
	   file_put_contents($filepath,$data);
	   q("update $tabla set imgAutoRayado = '$filepath' where idCita=$idc");
	   
	   if($validarTablaPdf == 1){
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $FirmaArrendatario));
       $filepath2 = $ruta."$idc"."_firma_arrendatario_"."$hoy2".".jpg";
	   file_put_contents($filepath2,$data);
	   q("update encuesta_acta_entrega set firmaArrendatario = '$filepath2' where idCita=$idc");
	   
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $firmaTarjetaHabiente));
       $filepath3 = $ruta."$idc"."_firma_tarjeta_habiente_"."$hoy2".".jpg";
	   file_put_contents($filepath3,$data);
	   q("update encuesta_acta_entrega set firmaTarjetaHabiente = '$filepath3' where idCita=$idc");
	   
	   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $firmaPersonaJuridica));
       $filepath4 = $ruta."$idc"."_firma_persona_juridica"."$hoy2".".jpg";
	   file_put_contents($filepath4,$data);
	   q("update encuesta_acta_entrega set firmaPersonaJuridica = '$filepath4' where idCita=$idc");  
	   }else{
		   $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $firmaDevolucion));
		   $filepath5 = $ruta."$idc"."_firma_devolucion"."$hoy2".".jpg";
		   file_put_contents($filepath5,$data);
		   q("update encuesta_acta_devolucion set firmaDevolucion = '$filepath5' where idCita=$idc");  
	   }
	   
	   
	   
	   
	   
	   
	   
	   /*Update para el medidor de gasolina*/
	   
	   
	   switch($medidorGasolina){case 0:$nun = 0;break;case 1:$nun = 1;break;
								case 2:$nun = 2;break;case 3:$nun = 3;break;
								case 4:$nun = 4;break;case 5:$nun = 5;break;
								case 6:$nun = 6;break;case 7:$nun = 7;break;
								case 8:$nun = 8;break;case 9:$nun = 9;break;}
	   
	   
	   if($validarTablaPdf == 1){
		   $urlImg = "lineavehiculo/medidorGasolinaEntrega/MedidorGasolina$nun.jpg";  
	   }else{
		   $urlImg = "lineavehiculo/medidorGasolinaDevolucion/MedidorGasolina$nun.jpg";
		   $ImgConsulta = qo("select firmaArrendatario,firmaTarjetaHabiente,firmaPersonaJuridica 
		        FROM encuesta_acta_entrega where idCita= $idc");
	   }
	   
	   q("update $tabla set medidorGasolina = '$urlImg' where idCita=$idc");
	  
	  
	  
		 $gaSolinaMedirEn = qo("select medidorGasolina from encuesta_acta_entrega where idCita=$idc"); 
	  
		  $gaSolinaMedirDe = qo("select medidorGasolina from encuesta_acta_devolucion where  idCita= $idc");
		  $firmaDevolucion = qo("select firmaDevolucion from encuesta_acta_devolucion where  idCita= $idc");
	  
	  
	  
	  

    /*Genera PDF*/
    $P=new pdf('P','mm','Letter'); // crea la instancia en tamaño carta
	$P->AddFont("c128a","","c128a.php"); // incluye fuentes para codigo de barras
	$P->AliasNbPages();
	$P->setTitle("ACTA DE ENTREGA/DEVOLUCION");
	$P->setAuthor("Tecnologia AOA it@aoacolombia.co");
	$P->Numeracion=false;
	$P->SetAutoPageBreak(false);
	$P->SetTopMargin('5');
	$P->AddPage('P');
	$P->Image('img/logo_AOA_nuevo_BN.JPG',128,8,80,7);
	$P->Image('img/itemsdechequeo.jpg',10,50,40,10);
	$P->Image('img/convencion.jpg',95,50,53,5);
	$P->Image('img/exterior.jpg',10,60,8,45);
	$P->Image('img/baul.jpg',10,105,8,20);
	$P->Image('img/interior.jpg',10,125,8,50);
	$P->Image('img/documentos.jpg',10,175,8,25);
	$P->Image('img/entrega.jpg',95,148,7,29);
	$P->Image('img/devolucion.jpg',95,180,7,29);
	$P->Image('img/encuesta.jpg',95,211,7,61);
	$P->Image('img/comentarios.jpg',10,202,7,37);
	//$P->rect(10,130,8,20);
	//$P->rect(10,150,8,50);
	//$P->rect(10,200,8,25);
	$P->setfont('Arial','B',13);
	$P->setxy(10,8);
	$P->Cell(110,5,'ACTA DE ENTREGA Y DEVOLUCION',0,0,'L');
	$P->setfont('Arial','',8);
	$P->setxy(10,20);$P->multicell(198,4,$TH,1,'L');
	$P->setfont('Arial','',9);
	$P->setxy(10,24);
	$P->cell(30,6,'PLACA ',1,0,'L');
	
	
	$vehiculo = qo("Select linea.nombre as nom_linea, marca.nombre as nom_marca
		from  aoacol_aoacars.vehiculo as veh inner join aoacol_aoacars.linea_vehiculo as linea on veh.linea = linea.id
		inner join aoacol_aoacars.marca_vehiculo as marca on marca.id = linea.marca where placa = '$Cita->placa'  limit 1");
	
	//print_r($vehiculo);
	
	
	$P->cell(70,6,$Cita->placa." ".$vehiculo->nom_marca." ".$vehiculo->nom_linea." ",1);
	
	if($Siniestro->renta)
	{
		$P->cell(40,6,'NUMERO SERVICIO ',1,0,'L');
	}		
	else
	{	$P->cell(40,6,'NUMERO SINIESTRO ',1,0,'L');}	

	$P->cell(58,6,$Siniestro->numero.' '.$Siniestro->asegurado_nombre,1,0,'L');
	
	$P->setxy(10,30);$P->cell(30,6,'AUTORIZADO',1,0,'L');
	//$P->cell(120,6,$Cita->conductor,1,0,'L');
	$P->cell(120,6,$Autorizado_nombre,1,0,'L');
	$P->cell(10,6,'C.C.',1,0,'L');$P->cell(38,6,$Autorizado_id,1,0,'L');
	$P->setxy(10,36);$P->cell(30,6,'DIRECCION',1,0,'L');$P->cell(120,6,$Autorizado_direccion,1,0,'L');
	$P->cell(10,6,'TEL',1,0,'L');$P->cell(38,6,$Autorizado_celular,1,0,'L');
	$P->setxy(10,42);$P->cell(30,6,'EMAIL',1,0,'L');$P->cell(168,6,$Autorizado_email,1,0,'L');

	$P->setfont('Arial','B',8);
	$P->setxy(50,50);
	//$P->cell(40,10,'',1,0,'C');
	$P->cell(20,5,'SALIDA',1,0,'C');$P->cell(20,5,'RETORNO',1,0,'C');
	$P->setfont('Arial','B',8);
	$P->setxy(50,55);$P->setfont('Arial','',8);$P->cell(10,5,'SI',1,0,'C');$P->cell(10,5,'NO',1,0,'C');$P->cell(10,5,'SI',1,0,'C');$P->cell(10,5,'NO',1,0,'C');
	$P->setfont('Arial','B',7);
	$P->setxy(128,16);$P->cell(57,4,"Oficina: $Oficina->nombre",0,0,'L');
	$P->setxy(10,16);
	$P->SetFillColor(255,255,255);
	$P->cell(10,4,$Aseguradora->sigla,1,0,'C');
	$P->setfont('Arial','',7);
	$P->setxy(20,16);$P->cell(76,4,"FECHA Y HORA DE DEVOLUCION: $Fec_entrega",1,0,'L');
	
	if($consultaEncuesta->emblemasPoll){
	  $emblemas = $consultaEncuesta->emblemasPoll;
	  $emblemasNo = "";
	}else{
		$emblemas = "";
		$emblemasNo = "X";
	}
	if($consultaEncuesta->copasPoll){
	  $copasPoll = $consultaEncuesta->copasPoll;
	  $copasPollNo = "";
	}else{
		$copasPoll = "";
		$copasPollNo = "X";
	}
	
	if($consultaEncuesta->antenaRadio){
	  $antenaRadio = $consultaEncuesta->antenaRadio;
	  $antenaRadioNo = "";
	}else{
		$antenaRadio = "";
		$antenaRadioNo = "X";
	}
	
	if($consultaEncuesta->limpiaParabrisas){
	  $limpiaParabrisas = $consultaEncuesta->limpiaParabrisas;
	  $limpiaParabrisasNo = "";
	}else{
		$limpiaParabrisas = "";
		$limpiaParabrisasNo = "X";
	}
	
	if($consultaEncuesta->niveles){
	  $niveles = $consultaEncuesta->niveles;
	  $nivelesNo = "";
	}else{
		$limpiaParabrisas = "";
		$nivelesNo = "X";
	}
	
	if($consultaEncuesta->niveles){
	  $niveles = $consultaEncuesta->niveles;
	  $nivelesNo = "";
	}else{
		$limpiaParabrisas = "";
		$nivelesNo = "X";
	}
	
	if($consultaEncuesta->niveles){
	  $niveles = $consultaEncuesta->niveles;
	  $nivelesNo = "";
	}else{
		$limpiaParabrisas = "";
		$nivelesNo = "X";
	}
	
	if($consultaEncuesta->lucesAltasYbajas){
	  $lucesAltasYbajas = $consultaEncuesta->lucesAltasYbajas;
	  $lucesAltasYbajasNo = "";
	}else{
		$lucesAltasYbajas = "";
		$lucesAltasYbajasNo = "X";
	}
	
	if($consultaEncuesta->direcionales){
	  $direcionales = $consultaEncuesta->direcionales;
	  $direcionalesNo = "";
	}else{
		$direcionales = "";
		$direcionalesNo = "X";
	}
	
	if($consultaEncuesta->luzReversaFrenoPlaca){
	  $luzReversaFrenoPlaca = $consultaEncuesta->luzReversaFrenoPlaca;
	  $direcionalesNo = "";
	}else{
		$luzReversaFrenoPlaca = "";
		$luzReversaFrenoPlacaNo = "X";
	}
	
	if($consultaEncuesta->tapaCombustible){
	  $tapaCombustible = $consultaEncuesta->tapaCombustible;
	  $tapaCombustibleNo = "";
	}else{
		$tapaCombustible = "";
		$tapaCombustibleNo = "X";
	}
	
	if($consultaEncuesta->gatoYGanchoArrastre){
	  $gatoYGanchoArrastre = $consultaEncuesta->gatoYGanchoArrastre;
	  $gatoYGanchoArrastreNo = "";
	}else{
		$gatoYGanchoArrastre = "";
		$gatoYGanchoArrastreNo = "X";
	}
	
	/*********************************Validaciones para devolucion*******************/
	
	if($consultaEncuestaDe->emblemasPoll){
	  $emblemasDe = $consultaEncuestaDe->emblemasPoll;
	  $emblemasNoDe = "";
	}else{
		$emblemasDe = "";
		$emblemasNoDe = "X";
	}
	if($consultaEncuestaDe->copasPoll){
	  $copasPollDe = $consultaEncuestaDe->copasPoll;
	  $copasPollNoDe = "";
	}else{
		$copasPollDe = "";
		$copasPollNoDe = "X";
	}
	
	if($consultaEncuestaDe->antenaRadio){
	  $antenaRadioDe = $consultaEncuestaDe->antenaRadio;
	  $antenaRadioNoDe = "";
	}else{
		$antenaRadioDe = "";
		$antenaRadioNoDe = "X";
	}
	
	if($consultaEncuestaDe->limpiaParabrisas){
	  $limpiaParabrisasDe = $consultaEncuestaDe->limpiaParabrisas;
	  $limpiaParabrisasNoDe = "";
	}else{
		$limpiaParabrisasDe = "";
		$limpiaParabrisasNoDe = "X";
	}
	
	if($consultaEncuestaDe->niveles){
	  $nivelesDe = $consultaEncuestaDe->niveles;
	  $nivelesNoDe = "";
	}else{
		$limpiaParabrisasDe = "";
		$nivelesNoDe = "X";
	}
	
	if($consultaEncuestaDe->niveles){
	  $nivelesDe = $consultaEncuestaDe->niveles;
	  $nivelesNoDe = "";
	}else{
		$limpiaParabrisasDe = "";
		$nivelesNoDe = "X";
	}
	
	if($consultaEncuestaDe->niveles){
	  $nivelesDe = $consultaEncuestaDe->niveles;
	  $nivelesNoDe = "";
	}else{
		$limpiaParabrisasDe = "";
		$nivelesNoDe = "X";
	}
	
	if($consultaEncuestaDe->lucesAltasYbajas){
	  $lucesAltasYbajasDe = $consultaEncuestaDe->lucesAltasYbajas;
	  $lucesAltasYbajasNoDe = "";
	}else{
		$lucesAltasYbajasDe = "";
		$lucesAltasYbajasNoDe = "X";
	}
	
	if($consultaEncuestaDe->direcionales){
	  $direcionalesDe = $consultaEncuestaDe->direcionales;
	  $direcionalesNoDe = "";
	}else{
		$direcionalesDe = "";
		$direcionalesNoDe = "X";
	}
	
	if($consultaEncuestaDe->luzReversaFrenoPlaca){
	  $luzReversaFrenoPlacaDe = $consultaEncuestaDe->luzReversaFrenoPlaca;
	  $direcionalesNoDe = "";
	}else{
		$luzReversaFrenoPlacaDe = "";
		$luzReversaFrenoPlacaNoDe = "X";
	}
	
	if($consultaEncuestaDe->tapaCombustibleDe){
	  $tapaCombustibleDe = $consultaEncuestaDe->tapaCombustible;
	  $tapaCombustibleNoDe = "";
	}else{
		$tapaCombustibleDe = "";
		$tapaCombustibleNoDe = "X";
	}
	
	if($consultaEncuestaDe->gatoYGanchoArrastre){
	  $gatoYGanchoArrastreDe = $consultaEncuestaDe->gatoYGanchoArrastre;
	  $gatoYGanchoArrastreNoDe = "";
	}else{
		$gatoYGanchoArrastreDe = "";
		$gatoYGanchoArrastreNoDe = "X";
	}
	
	
	
	$P->setxy(18,60);$P->cell(32,5,'Emblemas (4)',1,0,'L');$P->cell(10,5,$emblemas,1,0);$P->cell(10,5,$emblemasNo,1,0);$P->cell(10,5,$emblemasDe,1,0);$P->cell(10,5,$emblemasNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Copas (4)',1,0,'L');$P->cell(10,5,$copasPoll,1,0);$P->cell(10,5,$copasPollNo,1,0);$P->cell(10,5,$copasPoll,1,0);$P->cell(10,5,$copasPollNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Antena de radio',1,0,'L');$P->cell(10,5,$antenaRadio,1,0);$P->cell(10,5,$antenaRadioNo,1,0);$P->cell(10,5,$antenaRadioDe,1,0);$P->cell(10,5,$antenaRadioNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Limpia-parabrisas',1,0,'L');$P->cell(10,5,$limpiaParabrisas,1,0);$P->cell(10,5,$limpiaParabrisasNo,1,0);$P->cell(10,5,$limpiaParabrisasDe,1,0);$P->cell(10,5,$limpiaParabrisasNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Niveles',1,0,'L');$P->cell(10,5,$niveles,1,0);$P->cell(10,5,$nivelesNo,1,0);$P->cell(10,5,$nivelesDe,1,0);$P->cell(10,5,$nivelesNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Luces altas y bajas',1,0,'L');$P->cell(10,5,$lucesAltasYbajas,1,0);$P->cell(10,5,$lucesAltasYbajasNo,1,0);$P->cell(10,5,$lucesAltasYbajasDe,1,0);$P->cell(10,5,$lucesAltasYbajasNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Direccionales',1,0,'L');$P->cell(10,5,$direcionales,1,0);$P->cell(10,5,$direcionalesNo,1,0);$P->cell(10,5,$direcionalesDe,1,0);$P->cell(10,5,$direcionalesNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Luz de reversa,freno y placa',1,0,'L');$P->cell(10,5,$luzReversaFrenoPlaca,1,0);$P->cell(10,5,$luzReversaFrenoPlacaNo,1,0);$P->cell(10,5,$luzReversaFrenoPlacaDe,1,0);$P->cell(10,5,$luzReversaFrenoPlacaNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Tapa de combustible',1,0,'L');$P->cell(10,5,$tapaCombustible,1,0);$P->cell(10,5,$tapaCombustibleNo,1,0);$P->cell(10,5,$tapaCombustibleDe,1,0);$P->cell(10,5,$tapaCombustibleNoDe,1,0);
	$P->setfont('Arial','',7);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Gato y gancho arrastre',1,0,'L');$P->cell(10,5,$gatoYGanchoArrastre,1,0);$P->cell(10,5,$gatoYGanchoArrastreNo,1,0);$P->cell(10,5,$gatoYGanchoArrastreDe,1,0);$P->cell(10,5,$gatoYGanchoArrastreNoDe,1,0);
	
	if($consultaEncuesta->cruceta){
	  $cruceta = $consultaEncuesta->cruceta;
	  $crucetaNo = "";
	}else{
		$cruceta = "";
		$crucetaNo = "X";
	}
	
	if($consultaEncuesta->repusto){
	  $repusto = $consultaEncuesta->repusto;
	  $repustoNo = "";
	}else{
		$repusto = "";
		$repustoNo = "X";
	}
	
	if($consultaEncuesta->kitCarretera){
	  $kitCarretera = $consultaEncuesta->kitCarretera;
	  $kitCarreteraNo = "";
	}else{
		$kitCarretera = "";
		$kitCarreteraNo = "X";
	}
	
	if($consultaEncuesta->tapetes){
	  $tapetes = $consultaEncuesta->tapetes;
	  $tapetesNo = "";
	}else{
		$tapetes = "";
		$tapetesNo = "X";
	}
	
	if($consultaEncuesta->tapetes){
	  $tapetes = $consultaEncuesta->tapetes;
	  $tapetesNo = "";
	}else{
		$tapetes = "";
		$tapetesNo = "X";
	}
	
	if($consultaEncuesta->cinturonSeguridad){
	  $cinturonSeguridad = $consultaEncuesta->cinturonSeguridad;
	  $cinturonSeguridadNo = "";
	}else{
		$cinturonSeguridad = "";
		$cinturonSeguridadNo = "X";
	}
	
	if($consultaEncuesta->espejosLateralesRetrovisor){
	  $espejosLateralesRetrovisor = $consultaEncuesta->espejosLateralesRetrovisor;
	  $espejosLateralesRetrovisorNo = "";
	}else{
		$espejosLateralesRetrovisor = "";
		$espejosLateralesRetrovisorNo = "X";
	}
	
	if($consultaEncuesta->luzCortesia){
	  $luzCortesia = $consultaEncuesta->luzCortesia;
	  $luzCortesiaNo = "";
	}else{
		$luzCortesia = "";
		$luzCortesiaNo = "X";
	}
	
	if($consultaEncuesta->radio){
	  $radio = $consultaEncuesta->radio;
	  $radioNo = "";
	}else{
		$radio = "";
		$radioNo = "X";
	}
	
	if($consultaEncuesta->pito){
	  $pito = $consultaEncuesta->pito;
	  $pitoNo = "";
	}else{
		$pito = "";
		$pitoNo = "X";
	}
	
	if($consultaEncuesta->bloqueoCentral){
	  $bloqueoCentral = $consultaEncuesta->bloqueoCentral;
	  $bloqueoCentralNo = "";
	}else{
		$bloqueoCentral = "";
		$bloqueoCentralNo = "X";
	}
	
	if($consultaEncuesta->bloqueoCentral){
	  $bloqueoCentral = $consultaEncuesta->bloqueoCentral;
	  $bloqueoCentralNo = "";
	}else{
		$bloqueoCentral = "";
		$bloqueoCentralNo = "X";
	}
	
	if($consultaEncuesta->elevaVidriosDelanteros){
	  $elevaVidriosDelanteros = $consultaEncuesta->elevaVidriosDelanteros;
	  $elevaVidriosDelanterosNo = "";
	}else{
		$elevaVidriosDelanteros = "";
		$elevaVidriosDelanterosNo = "X";
	}
	
	if($consultaEncuesta->calefaccionYaireAcondicionado){
	  $calefaccionYaireAcondicionado = $consultaEncuesta->calefaccionYaireAcondicionado;
	  $calefaccionYaireAcondicionadoNo = "";
	}else{
		$calefaccionYaireAcondicionado = "";
		$calefaccionYaireAcondicionadoNo = "X";
	}
	
	if($consultaEncuesta->tarjetaPropiedad){
	  $tarjetaPropiedad = $consultaEncuesta->tarjetaPropiedad;
	  $tarjetaPropiedadNo = "";
	}else{
		$tarjetaPropiedad = "";
		$tarjetaPropiedadNo = "X";
	}
	
	if($consultaEncuesta->encenderYCenicero){
	  $encenderYCenicero = $consultaEncuesta->encenderYCenicero;
	  $encenderYCeniceroNo = "";
	}else{
		$encenderYCenicero = "";
		$encenderYCeniceroNo = "X";
	}
	
	if($consultaEncuesta->soatVijente){
	  $soatVijente = $consultaEncuesta->soatVijente;
	  $soatVijenteNo = "";
	}else{
		$soatVijente = "";
		$soatVijenteNo = "X";
	}
	
	if($consultaEncuesta->lineaAsistencia){
	  $lineaAsistencia = $consultaEncuesta->lineaAsistencia;
	  $lineaAsistenciaNo = "";
	}else{
		$lineaAsistencia = "";
		$lineaAsistenciaNo = "X";
	}
	
	if($consultaEncuesta->manualesGarantia){
	  $manualesGarantia = $consultaEncuesta->manualesGarantia;
	  $manualesGarantiaNo = "";
	}else{
		$manualesGarantia = "";
		$manualesGarantiaNo = "X";
	}
	
	if($consultaEncuesta->contrato){
	  $contrato = $consultaEncuesta->contrato;
	  $contratoNo = "";
	}else{
		$contrato = "";
		$contratoNo = "X";
	}
	/**********************************Validaciones devolucion******************************/
	
	if($consultaEncuestaDe->cruceta){
	  $crucetaDe = $consultaEncuestaDe->cruceta;
	  $crucetaNoDe = "";
	}else{
		$crucetaDe = "";
		$crucetaNoDe = "X";
	}
	
	if($consultaEncuestaDe->repusto){
	  $repustoDe = $consultaEncuestaDe->repusto;
	  $repustoNoDe = "";
	}else{
		$repustoDe = "";
		$repustoNoDe = "X";
	}
	
	if($consultaEncuestaDe->kitCarretera){
	  $kitCarreteraDe = $consultaEncuestaDe->kitCarretera;
	  $kitCarreteraNoDe = "";
	}else{
		$kitCarreteraDe = "";
		$kitCarreteraNoDe = "X";
	}
	
	if($consultaEncuestaDe->tapetes){
	  $tapetesDe = $consultaEncuestaDe->tapetes;
	  $tapetesNoDe = "";
	}else{
		$tapetesDe = "";
		$tapetesNoDe = "X";
	}
	
	if($consultaEncuestaDe->tapetes){
	  $tapetesDe = $consultaEncuestaDe->tapetes;
	  $tapetesNoDe = "";
	}else{
		$tapetesDe = "";
		$tapetesNoDe = "X";
	}
	
	if($consultaEncuestaDe->cinturonSeguridad){
	  $cinturonSeguridadDe = $consultaEncuestaDe->cinturonSeguridad;
	  $cinturonSeguridadNoDe = "";
	}else{
		$cinturonSeguridadDe = "";
		$cinturonSeguridadNoDe = "X";
	}
	
	if($consultaEncuestaDe->espejosLateralesRetrovisor){
	  $espejosLateralesRetrovisorDe = $consultaEncuestaDe->espejosLateralesRetrovisor;
	  $espejosLateralesRetrovisorNoDe = "";
	}else{
		$espejosLateralesRetrovisorDe = "";
		$espejosLateralesRetrovisorNoDe = "X";
	}
	
	if($consultaEncuestaDe->luzCortesia){
	  $luzCortesiaDe = $consultaEncuestaDe->luzCortesia;
	  $luzCortesiaNoDe = "";
	}else{
		$luzCortesiaDe = "";
		$luzCortesiaNoDe = "X";
	}
	
	if($consultaEncuestaDe->radio){
	  $radioDe = $consultaEncuestaDe->radio;
	  $radioNoDe = "";
	}else{
		$radioDe = "";
		$radioNoDe = "X";
	}
	
	if($consultaEncuestaDe->pito){
	  $pitoDe = $consultaEncuestaDe->pito;
	  $pitoNoDe = "";
	}else{
		$pitoDe = "";
		$pitoNoDe = "X";
	}
	
	if($consultaEncuestaDe->bloqueoCentral){
	  $bloqueoCentralDe = $consultaEncuestaDe->bloqueoCentral;
	  $bloqueoCentralNoDe = "";
	}else{
		$bloqueoCentralDe = "";
		$bloqueoCentralNoDe = "X";
	}
	
	if($consultaEncuestaDe->bloqueoCentral){
	  $bloqueoCentralDe = $consultaEncuestaDe->bloqueoCentral;
	  $bloqueoCentralNoDe = "";
	}else{
		$bloqueoCentralDe = "";
		$bloqueoCentralNoDe = "X";
	}
	
	if($consultaEncuestaDe->elevaVidriosDelanteros){
	  $elevaVidriosDelanterosDe = $consultaEncuestaDe->elevaVidriosDelanteros;
	  $elevaVidriosDelanterosNoDe = "";
	}else{
		$elevaVidriosDelanterosDe = "";
		$elevaVidriosDelanterosNoDe = "X";
	}
	
	if($consultaEncuestaDe->calefaccionYaireAcondicionado){
	  $calefaccionYaireAcondicionadoDe = $consultaEncuestaDe->calefaccionYaireAcondicionado;
	  $calefaccionYaireAcondicionadoNoDe = "";
	}else{
		$calefaccionYaireAcondicionadoDe = "";
		$calefaccionYaireAcondicionadoNoDe = "X";
	}
	
	if($consultaEncuestaDe->tarjetaPropiedad){
	  $tarjetaPropiedadDe = $consultaEncuestaDe->tarjetaPropiedad;
	  $tarjetaPropiedadNoDe = "";
	}else{
		$tarjetaPropiedadDe = "";
		$tarjetaPropiedadNoDe = "X";
	}
	
	if($consultaEncuestaDe->encenderYCenicero){
	  $encenderYCeniceroDe = $consultaEncuestaDe->encenderYCenicero;
	  $encenderYCeniceroNoDe = "";
	}else{
		$encenderYCeniceroDe = "";
		$encenderYCeniceroNoDe = "X";
	}
	
	if($consultaEncuestaDe->soatVijente){
	  $soatVijenteDe = $consultaEncuestaDe->soatVijente;
	  $soatVijenteNoDe = "";
	}else{
		$soatVijenteDe = "";
		$soatVijenteNoDe = "X";
	}
	
	if($consultaEncuestaDe->lineaAsistencia){
	  $lineaAsistenciaDe = $consultaEncuesta->lineaAsistencia;
	  $lineaAsistenciaNoDe = "";
	}else{
		$lineaAsistenciaDe = "";
		$lineaAsistenciaNoDe = "X";
	}
	
	if($consultaEncuestaDe->manualesGarantia){
	  $manualesGarantiaDe = $consultaEncuestaDe->manualesGarantia;
	  $manualesGarantiaNoDe = "";
	}else{
		$manualesGarantiaDe = "";
		$manualesGarantiaNoDe = "X";
	}
	
	if($consultaEncuestaDe->contrato){
	  $contratoDe = $consultaEncuestaDe->contrato;
	  $contratoNoDe = "";
	}else{
		$contratoDe = "";
		$contratoNoDe = "X";
	}
	
	
	
	$P->setfont('Arial','',7);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Cruceta',1,0,'L');$P->cell(10,5,$cruceta,1,0);$P->cell(10,5,$crucetaNo,1,0);$P->cell(10,5,$crucetaDe,1,0);$P->cell(10,5,$crucetaNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Repuesto',1,0,'L');$P->cell(10,5,$repusto,1,0);$P->cell(10,5,$repustoNo,1,0);$P->cell(10,5,$repustoDe,1,0);$P->cell(10,5,$repustoNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Kit de carretera',1,0,'L');$P->cell(10,5,$kitCarretera,1,0);$P->cell(10,5,$kitCarreteraNo,1,0);$P->cell(10,5,$kitCarreteraDe,1,0);$P->cell(10,5,$kitCarreteraNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Tapetes',1,0,'L');$P->cell(10,5,$tapetes,1,0);$P->cell(10,5,$tapetesNo,1,0);$P->cell(10,5,$tapetesDe,1,0);$P->cell(10,5,$tapetesNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Cinturon. Seguridad (5)',1,0,'L');$P->cell(10,5,$cinturonSeguridad,1,0);$P->cell(10,5,$cinturonSeguridadNo,1,0);$P->cell(10,5,$cinturonSeguridadDe,1,0);$P->cell(10,5,$cinturonSeguridadNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Espejos laterales, retrov.',1,0,'L');$P->cell(10,5,$espejosLateralesRetrovisor,1,0);$P->cell(10,5,$espejosLateralesRetrovisorNo,1,0);$P->cell(10,5,$espejosLateralesRetrovisorDe,1,0);$P->cell(10,5,$espejosLateralesRetrovisorNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,utf8_decode('Luz de cortesía'),1,0,'L');$P->cell(10,5,$luzCortesia,1,0);$P->cell(10,5,$luzCortesiaNo,1,0);$P->cell(10,5,$luzCortesiaDe,1,0);$P->cell(10,5,$luzCortesiaNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Radio',1,0,'L');$P->cell(10,5,$radio,1,0);$P->cell(10,5,$radioNo,1,0);$P->cell(10,5,$radioDe,1,0);$P->cell(10,5,$radioNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Pito',1,0,'L');$P->cell(10,5,$pito,1,0);$P->cell(10,5,$pitoNo,1,0);$P->cell(10,5,$pitoDe,1,0);$P->cell(10,5,$pitoNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Bloqueo Central',1,0,'L');$P->cell(10,5,$bloqueoCentral,1,0);$P->cell(10,5,$bloqueoCentralNo,1,0);$P->cell(10,5,$bloqueoCentralDe,1,0);$P->cell(10,5,$bloqueoCentralNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Elevavidrios delanteros',1,0,'L');$P->cell(10,5,$elevaVidriosDelanteros,1,0);$P->cell(10,5,$elevaVidriosDelanterosNo,1,0);$P->cell(10,5,$elevaVidriosDelanterosDe,1,0);$P->cell(10,5,$elevaVidriosDelanterosNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,utf8_decode('Calefacción y A/A'),1,0,'L');$P->cell(10,5,$calefaccionYaireAcondicionado,1,0);$P->cell(10,5,$calefaccionYaireAcondicionadoNo,1,0);$P->cell(10,5,$calefaccionYaireAcondicionadoDe,1,0);$P->cell(10,5,$calefaccionYaireAcondicionadoNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Encencedor y cenicero',1,0,'L');$P->cell(10,5,$encenderYCenicero,1,0);$P->cell(10,5,$encenderYCeniceroNo,1,0);$P->cell(10,5,$encenderYCeniceroDe,1,0);$P->cell(10,5,$encenderYCeniceroNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Tarjeta de propiedad',1,0,'L');$P->cell(10,5,$tarjetaPropiedad,1,0);$P->cell(10,5,$tarjetaPropiedadNo,1,0);$P->cell(10,5,$tarjetaPropiedadDe,1,0);$P->cell(10,5,$tarjetaPropiedadNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'SOAT vigente',1,0,'L');$P->cell(10,5,$soatVijente,1,0);$P->cell(10,5,$soatVijenteNo,1,0);$P->cell(10,5,$soatVijenteDe,1,0);$P->cell(10,5,$soatVijenteNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Linea Asistencia',1,0,'L');$P->cell(10,5,$lineaAsistencia,1,0);$P->cell(10,5,$lineaAsistenciaNo,1,0);$P->cell(10,5,$lineaAsistenciaDe,1,0);$P->cell(10,5,$lineaAsistenciaNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,utf8_decode('Manuales y Garantía'),1,0,'L');$P->cell(10,5,$manualesGarantia,1,0);$P->cell(10,5,$manualesGarantiaNo,1,0);$P->cell(10,5,$manualesGarantiaDe,1,0);$P->cell(10,5,$manualesGarantiaNoDe,1,0);
	$P->setxy(18,$P->y+5);$P->cell(32,5,'Contrato',1,0,'L');$P->cell(10,5,$contrato,1,0);$P->cell(10,5,$contratoNo,1,0);$P->cell(10,5,$contratoDe,1,0);$P->cell(10,5,$contratoNoDe,1,0);
	$texto=utf8_decode("Me fueron explicados los mecanismos de encendido, bloqueo central y apertura de puertas del vehículo");

	$P->setxy(95,130);
	$P->setfont('Arial','',10);
	$P->MultiCell(113,5,$texto,1,1,'C');

	$P->setfont('Arial','B',8);
	//$P->setxy(95,115);$P->cell(118,5,'ENTREGA',1,0,'C');
	$P->setfont('Arial','',6);
	$P->setxy(95,148);$P->cell(113,29,' ',1,0,'L');
	$P->setxy(102,148);$P->cell(40,5,'Fecha(A/M/D): '.$Cita->fecha,1,0,'L');$P->cell(35,5,'Hora: '.$Cita->hora,1,0,'L');$P->cell(31,5,'Kilometros: '.number_format($kilome),1,0,'L');
	$P->setxy(102,154);
	$P->multicell(78,3,"Observaciones: ".($Cita->dir_domicilio?"DOMICILIO: $Cita->dir_domicilio TEL: $Cita->tel_domicilio | $Autorizaciones":""),0,'L');
	//$P->SetTextColor(200,200,200);
	$P->setfont('Arial','B',8);
	if($ImgConsulta->firmaTarjetaHabiente){
		$P->Image($ImgConsulta->firmaTarjetaHabiente,105,160,40,7);
	}else{
		$P->Image($filepath3,105,160,40,7);
	}
	
	$P->setxy(105,166);$P->cell(50,4,"_____________________");
	$P->setxy(115,170);$P->cell(50,4,"CLIENTE");
	$P->setxy(150,160);$P->setfont('Arial','B',10);$P->cell(38,4,$NUSUARIO);/*Entrega*/
	$P->setxy(145,167);$P->cell(50,4,"_____________________");
	$P->setxy(158,170);$P->cell(50,4,"AOA");
	$P->setxy(150,173);$P->setfont('Arial','B',5);$P->cell(38,4,"NOMBRE FUNCIONARIO");
	
	$P->setfont('Arial','',6);
	$P->setxy(95,180);$P->cell(113,29,' ',1,0,'L');
	$P->setxy(102,180);$P->cell(40,5,'Fecha(A/M/D): ',1,0,'L');$P->cell(35,5,'Hora: ',1,0,'L');$P->cell(31,5,'Kilometros: ',1,0,'L');
	$P->setxy(102,188);
	$P->multicell(78,3,"Observaciones: ".($Cita->dir_domiciliod?"DOMICILIO: $Cita->dir_domiciliod TEL: $Cita->tel_domiciliod | $Autorizaciones":""),0,'L');
	$P->setfont('Arial','B',8);
	if($firmaDevolucion->firmaDevolucion){
	$P->Image($filepath5,105,190,40,7);	
	}
	$P->setxy(105,195);$P->cell(50,4,"_____________________");
	$P->setxy(115,198);$P->cell(50,4,"CLIENTE");
	if($firmaDevolucion->firmaDevolucion){
		$P->setxy(150,190);$P->setfont('Arial','B',10);$P->cell(38,4,$NUSUARIO);/*Devolucion*/
	}
	$P->setxy(145,195);$P->cell(50,4,"_____________________");
	$P->setxy(158,198);$P->cell(50,4,"AOA");
	$P->setxy(150,200);$P->setfont('Arial','B',5);$P->cell(38,4,"NOMBRE FUNCIONARIO");
	/*$P->SetTextColor(0,0,0);
	$P->setfont('Arial','B',8);
	$P->setxy(90,170);$P->cell(118,5,'DEVOLUCION',1,0,'C');
	$P->setfont('Arial','',7);
	$P->setxy(90,175);$P->cell(118,5,'Observaciones de Retorno:',0,0,'L');$P->setxy(90,175);$P->cell(118,20,' ',1,0,'L');
	$P->SetTextColor(200,200,200);
	$P->setfont('Arial','B',14);
	$P->setxy(90,195);$P->cell(60,10,'CLIENTE ',1,0,'C');$P->cell(58,10,'AOA ',1,0,'C');
	$P->setxy(160,202);$P->setfont('Arial','B',8);$P->cell(38,4,'NOMBRE FUNCIONARIO ',0,0,'C');*/
	
	$P->setfont('Arial','',7);
	$P->setxy(95,211);$P->cell(113,61,' ',1,0,'L');
	
	/*$P->setfont('Arial','B',7);
	$P->SetTextColor(0,0,0);
	$P->SETXY(90,205);$P->cell(118,3,"E N C U E S T A",1,0,'C');
	$P->setfont('Arial','',7);*/
	if(inlist($Siniestro->aseguradora,'3,7'))
	{
		//$P->SetFillColor(220,220,220);
		$P->setxy(102,212);$P->cell(100,3,utf8_decode("1. De 0 a 10 donde 0 es nada probable y 10 es muy probable , recomendaría a Liberty Seguros"),0,0,'L',1);
		$P->setxy(102,215);$P->cell(100,3,utf8_decode("¿a amigos o familiares? _____",0,0,'L'),1);
		$P->setxy(102,221);$P->cell(100,3,utf8_decode("2. En escala de 0 a 10 donde 0 es totalmente insatisfecho y 10 es totalmente satisfecho, ¿que"));
		$P->setxy(102,224);$P->cell(100,3,utf8_decode("tan satisfecho se encuentra con el servicio de vehículo sustituto de Liberty Seguros? _____"));
		$P->setxy(102,229);$P->cell(100,3,utf8_decode("En una escala de 0 a 10 (donde 0 = muy insatisfecho, 10 = muy satisfecho) en base a su"),0,0,'L',1);
		$P->setxy(102,231);$P->cell(100,3,utf8_decode("experiencia de los servicios más recientes de vehículo sustituto por favor califique los"),0,0,'L',1);
		$P->setxy(102,234);$P->cell(100,3,utf8_decode("siguientes factores:"),0,0,'L',1);
		$P->setxy(102,239);$P->cell(100,3,utf8_decode("3. Facilidad de contacto con AOA: _____  4. Amabilidad del funcionario: _____"),0,0,'L');
		$P->setxy(102,242);$P->cell(100,3,utf8_decode("5. Claridad en la información recibida para la asignación de Vehículo Sustituto:_____"),0,0,'L');
		$P->setxy(102,247);$P->cell(100,3,utf8_decode("6. Por favor indíquenos el número de veces que tuvo que llamar para recibir información del"),0,0,'L',1);
		$P->setxy(102,250);$P->cell(100,3,utf8_decode("servicio y coordinar la entrega del vehículo: _____"),0,0,'L',1);
		$P->setxy(102,253);$P->cell(100,3,utf8_decode("7. Tiempo para pa asignación del vehículo: _____"),0,0,'L');
		$P->setxy(102,259);$P->cell(100,3,utf8_decode("8. Facilidad para la entrega del vehículo: _____   9. Calidad del Vehículo asignado:_____"),0,0,'L');
		$P->setxy(102,261);$P->cell(100,3,utf8_decode("10. La facilidad para la devolución del Vehículo: _____"),0,0,'L',1);
		//$P->SetFillColor(255,255,255);
	}
	else
	{
		//$P->SetFillColor(220,220,220);0
		
		if($Siniestro->aseguradora == 55) 
		{			
			$P->setxy(102,212);$P->cell(100,3,utf8_decode("1. ¿Cómo califica el servicio prestado por el agente que atendió su llamada?"),0,0,'L',1);			
			$currentY = 218;
		}
		else{
			$P->setxy(102,212);$P->cell(100,3,utf8_decode("1. Califique de 1 a 5 la información y orientación recibida en el primer contacto por el personal"),0,0,'L',1);
			$P->setxy(102,216);$P->cell(100,3," de nuestro call center:",0,0,'L',1);
			$currentY = 218;
		}		
		
		$P->setfont('Arial','B',7);
		$P->rect(104,$currentY+4,2,2);$P->setxy(107,$currentY+4);$P->cell(13,2,"5 Excelente.",0,0,'L',1);
		$P->rect(124,$currentY+4,2,2);$P->setxy(127,$currentY+4);$P->cell(10,2,"4 Buena.",0,0,'L',1);
		$P->rect(140,$currentY+4,2,2);$P->setxy(143,$currentY+4);$P->cell(13,2,"3 Regular.",0,0,'L',1);
		$P->rect(158,$currentY+4,2,2);$P->setxy(161,$currentY+4);$P->cell(10,2,"2 Mala.",0,0,'L',1);
		$P->rect(173,$currentY+4,2,2);$P->setxy(176,$currentY+4);$P->cell(13,2,"1 Pesima.",0,0,'L',1);		
		$P->setfont('Arial','',7);
		$P->setxy(102,$currentY+7);$P->cell(100,3,utf8_decode("2. Califique de 1 a 5 la gestión y agilidad de nuestras  auxiliares de servicio al  cliente en el"));
		$P->setxy(102,$currentY+10);$P->cell(100,3,utf8_decode(" momento de su ingreso a las instalaciones de AOA SAS:"));
		$P->setfont('Arial','B',7);
		$P->rect(104,$currentY+14,2,2);$P->setxy(107,$currentY+14);$P->cell(13,2,"5 Excelente.",0,0,'L',1);
		$P->rect(124,$currentY+14,2,2);$P->setxy(127,$currentY+14);$P->cell(10,2,"4 Buena.",0,0,'L',1);
		$P->rect(140,$currentY+14,2,2);$P->setxy(143,$currentY+14);$P->cell(13,2,"3 Regular.",0,0,'L',1);
		$P->rect(158,$currentY+14,2,2);$P->setxy(161,$currentY+14);$P->cell(10,2,"2 Mala.",0,0,'L',1);
		$P->rect(173,$currentY+14,2,2);$P->setxy(176,$currentY+14);$P->cell(13,2,"1 Pesima.",0,0,'L',1);	
		$P->setfont('Arial','',7);
		
		
		
		
		
		if($Siniestro->aseguradora == 55) 
		{			
			$P->setxy(102,235);$P->cell(100,3,utf8_decode("3. ¿Cómo califica la calidad del servicio prestado ? "),0,0,'L',1);			
			$currentY = 239;
		}
		else{
			$P->setxy(102,235);$P->cell(100,3,utf8_decode("3. Califique de 1 a 5 la gestión y agilidad de nuestros auxiliares operativos en el momento de",0,0,'L',1));
			$P->setxy(102,238);$P->cell(100,3,utf8_decode("la entrega del vehículo:",0,0,'L',1));
			$currentY = 239;	
		}
		
		
		
		
		$P->setfont('Arial','B',7);
		$P->rect(104,$currentY+4,2,2);$P->setxy(107,$currentY+4);$P->cell(13,2,"5 Excelente.",0,0,'L',1);
		$P->rect(124,$currentY+4,2,2);$P->setxy(127,$currentY+4);$P->cell(10,2,"4 Buena.",0,0,'L',1);
		$P->rect(140,$currentY+4,2,2);$P->setxy(143,$currentY+4);$P->cell(13,2,"3 Regular.",0,0,'L',1);
		$P->rect(158,$currentY+4,2,2);$P->setxy(161,$currentY+4);$P->cell(10,2,"2 Mala.",0,0,'L',1);
		$P->rect(173,$currentY+4,2,2);$P->setxy(176,$currentY+4);$P->cell(13,2,"1 Pesima.",0,0,'L',1);

		
		if($Siniestro->aseguradora != 55 and $Siniestro->aseguradora != 93)
		{
			$P->setfont('Arial','',7);
			$P->setxy(102,247);$P->cell(100,3,utf8_decode("4. De ser necesario utilizaría nuevamente nuestros servicios?"));
			$P->setfont('Arial','B',7);
			$P->rect(104,250,2,2);$P->setxy(107,250);$P->cell(13,2,"Definitivamente si.",0,0,'L',1);
			$P->rect(132,250,2,2);$P->setxy(135,250);$P->cell(10,2,"Probablemente si.",0,0,'L',1);
			$P->rect(160,250,2,2);$P->setxy(163,250);$P->cell(13,2,utf8_decode("No lo utilizaría."),0,0,'L',1);			
		}
		else
		{
			$P->setfont('Arial','',7);
			if($Siniestro->aseguradora == 55)
			{
				//$P->setxy(102,248);$P->cell(100,3,"4.En una escala de 0 a 10 siendo 0 muy improbable y 10 muy probable ¿Recomendaría a");
				$P->setxy(102,251);$P->cell(100,3,utf8_decode("Previsora Seguros?: _________"));		
			}
			
			if($Siniestro->aseguradora == 93)
			{
				$P->setxy(102,248);$P->cell(100,3,utf8_decode("4.En una escala de 0 a 10 siendo 0 muy improbable y 10 muy probable"));
				$P->setxy(102,251);$P->cell(100,3,utf8_decode("¿Recomendaría HDI Seguros a su familia o amigos ?: _________"));		
			}
		}
		$P->setfont('Arial','',7);
		
		
		if($Siniestro->aseguradora == 55) 
		{			
			$P->setxy(102,254);$P->cell(100,3,utf8_decode("5. De acuerdo con la experiencia, nos recomendaria con familiares o amigos. ¿Cual es la"),0,0,'L',1);
			$P->setxy(102,257);$P->cell(100,3,utf8_decode("pricipal razón por la que nos dio este puntaje? *Calificaciones menores a 8."),0,0,'L',1);
			$P->setxy(102,247);$P->cell(100,3,utf8_decode("mejora  mas importante que debemos realizar para tener un resultado cercano a 10?"),0,0,'L',1);			
			$P->setfont('Arial','B',7);	      
			$P->setxy(102,260);$P->cell(100,3,utf8_decode("Calificacion: _________"));
			//$P->setxy(130,250);$P->cell(100,utf8_decode(3,"¿Por que ?:");			
			$P->setfont('Arial','',7);        
		
		}else{                                 
			$P->setxy(102,254);$P->cell(100,3,utf8_decode("5. Recomendaría usted los servicios prestados por AOA S.A.S. a sus familiares o conocidos"),0,0,'L',1);
			$P->setxy(102,257);$P->cell(100,3,utf8_decode("en caso de  requerirlos?"),0,0,'L',1);
			$P->setfont('Arial','B',7);
			$P->rect(104,260,2,2);$P->setxy(107,260);$P->cell(5,2,"Si.",0,0,'L',1);
			$P->rect(114,260,2,2);$P->setxy(118,260);$P->cell(5,2,"No.",0,0,'L',1);
			$P->setfont('Arial','',7);			
		}

		if($Siniestro->aseguradora == 55) 
		{			
			$P->setxy(102,264);$P->cell(100,3,utf8_decode("6. ¿Cuanto esfuerzo personal tuvo que invertir en la prestación del servicio?"));
			//$P->setxy(110,264);$P->cell(100,3,"donde 1 es alto esfuerzo y 10 es poco esfuerzo");
			$P->setfont('Arial','B',7);	
			$P->setxy(102,268);$P->cell(100,3,"Calificacion: _________");	
		}
		else{
			$P->setxy(102,264);$P->cell(100,3,utf8_decode("6. Califique de 1 a 5 en términos generales los servicios prestados por AOA S.A.S."));
			$P->setfont('Arial','B',7);
			$P->rect(104,268,2,2);$P->setxy(107,268);$P->cell(13,2,"5 Excelente.",0,0,'L',1);
			$P->rect(124,268,2,2);$P->setxy(127,268);$P->cell(10,2,"4 Buena.",0,0,'L',1);
			$P->rect(140,268,2,2);$P->setxy(143,268);$P->cell(13,2,"3 Regular.",0,0,'L',1);
			$P->rect(158,268,2,2);$P->setxy(161,268);$P->cell(10,2,"2 Mala.",0,0,'L',1);
			$P->rect(173,268,2,2);$P->setxy(176,268);$P->cell(13,2,"1 Pesima.",0,0,'L',1);				
		}	
		
		$P->setfont('Arial','',7);
		//$P->SetFillColor(255,255,255);
	}

	$P->setfont('Arial','',7);
	$P->setxy(10,202);$P->cell(80,37,' ',1,0,'L'); //celda comentarios
	
	$P->setfont('Arial','',6);
	//$P->setxy(10,242);
	//$P->cell(80,17,' ');
	$P->setxy(10,240);
	$P->cell(13,1,utf8_decode("SI AL MOMENTO DE LA DEVOLUCIÓN DEL VEHÍCULO SE PRESENTA"),0,'L');
	$P->setxy(10,243);
	$P->cell(13,1,utf8_decode("UN DAÑO CON RESPECTO A COMO FUE ENTREGADO INICIALMENTE,"),0,'L');
	$P->setxy(10,246);
	$P->cell(13,1,utf8_decode("EL COSTO DE LA REPARACIÓN PARA LLEVARLO A SU ESTADO INICIAL, "),0,'L');
	$P->setxy(10,249);
	$P->cell(13,1,utf8_decode("CORRERÁ A CARGO DEL CLIENTE O USUARIO FIRMANTE DE PRESENTE"),0,'L');
	$P->setxy(10,252);
	$P->cell(13,1,utf8_decode("ACTA,SI NO SE CONOCE EL VALOR LOS DAÑOS SE SOLICITARÁ UNA"),0,'L');
	$P->setxy(10,255);
	$P->cell(13,1,utf8_decode("COTIZACIÓN AL PROVEEDOR AUTORIZADO DE LA COMPAÑÍA Y UNA VEZ"),0,'L');
	$P->setxy(10,258);
	$P->cell(13,1,utf8_decode("CUENTE CON LA COTIZACIÓN SE DEBERÁ CANCELAR EL VALOR DE"),0,'L');
	$P->setxy(10,261);
	$P->cell(13,1,utf8_decode("MANERA INMEDIATA Y/O REALIZAR EL COBRO CONTRA LA GARANTÍA"),0,'L');
	$P->setxy(10,264);
	$P->cell(13,1,utf8_decode("QUE HAYA SIDO DEJADA A AOA. PARA LOS DEMÁS CASOS USTED DEBE"),0,'L');
	$P->setxy(10,267);
	$P->cell(13,1,utf8_decode("EXIGIR LA COPIA DE SU FACTURA Y/O RECIBO DE CAJA , DE LO CONTRARIO"),0,'L');
	$P->setxy(10,270);
	$P->cell(13,1,utf8_decode("NO DEBERÁ CANCELAR NINGÚN VALOR."),0,'L');
	$P->setxy(1,270);
	$P->cell(13,2,". ",0,'L');
	//$P->setxy(10,250);$P->cell(198,3,"Comentarios:");
	//$P->setxy(10,250);$P->cell(198,10," ",1);
	$P->setxy(95,50);
	$P->setfont('Arial','B',8);$P->cell(53,5,"",1,0,'L');$P->cell(30,5,"RAYON      (*)",1,0,'L');$P->cell(30,5,"GOLPE      (O)",1,0,'L');
	$P->setxy(95,55);
	$P->cell(113,65,"",1,0,'L');
	$P->setfont('Arial','B',8);
	$P->setxy(10,274);$P->multicell(199,4,"____________________________________________________________________________________________________________________________",0,'C');
	$P->setfont('Arial','',8);
	$P->setxy(10,274);$P->multicell(199,4,utf8_decode("Carrera 69 B No. 98 A - 10  PBX: +(571) 756 0510 • Fax: 756 0510 Ext. 112  Bogotá D.C., Colombia  www.aoacolombia.com"),0,'C');
	if($Linea->vgenerica) $P->Image($filepath,102,57,102,62);
	//if($Linea->izquierda_f) $P->Image($Linea->izquierda_f,97,59,70,26);
	//if($Linea->delante_f) $P->Image($Linea->delante_f,172,59,32,26);
	//if($Linea->derecha_f) $P->Image($Linea->derecha_f,97,90,70,26);
	//if($Linea->atras_f) $P->Image($Linea->atras_f,172,90,32,26);
	
	if($gaSolinaMedirEn->medidorGasolina){
		$P->Image($gaSolinaMedirEn->medidorGasolina,187,154,10,20);
	}else{
		$P->Image('lineavehiculo/medidorGasolinaEntrega/MedidorGasolina0.jpg',187,154,10,20);
	}
    
	if($gaSolinaMedirDe->medidorGasolina){
		$P->Image($gaSolinaMedirDe->medidorGasolina,187,186,10,20);
	}else{
		$P->Image('lineavehiculo/medidorGasolinaDevolucion/MedidorGasolina0.jpg',187,186,10,20);
	}
	
	 
	
		$Incremento=3.5;$Y=17;$Fuente=12;
		$P->AddPage('P');
		//$P->Image('../img/LOGO_AOA_200.jpg',20,10,38,16);
		$P->Image('img/logo_AOA_nuevo_BN.JPG',128,8,80,7);
		$P->setfont('Arial','B',12);
		//$P->setxy(60,12);$P->multiCell(134,7,'ANEXO CONTRATO DE ARRENDAMIENTO DE VEHICULOS',1,'C');
		$P->setxy(20,7);
		$P->Cell(110,5,'ANEXO CONTRATO DE',0,0,'L');
		$P->setxy(20,10);
		$P->Cell(10,7,utf8_decode('ARRENDAMIENTO DE VEHÍCULOS'),0,0,'L');
		$P->setxy(19,12);$P->multicell(191,4,"_______________________________________________________________________________",0,'C');
		$P->setfont('Arial','B',8);
		
		$P->setxy(22,$Y);
		$P->cell(40,$Incremento,"SERVICIO PRESTADO",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setxy(70,($Y-4))+1;
		//$P->setfont('Arial','',10);
		$Clase_servicios=q("select * from clase_servicio");
		while($cs=mysql_fetch_object($Clase_servicios))
		{
			if($Aseguradora->clase_servicio==$cs->id)
			{
				$compose = "X";	
			}
			else
			{
				$compose = "_____";
			}
			//$compose = $Incremento,(?" X ":"_____";
			if($cs->nombre == "OTRO")
			{
				$P->Cell(strlen($cs->nombre)*5,$Incremento,$cs->nombre."  ".$compose,1,0,'L');
			}
			else
			{
				$P->Cell(strlen($cs->nombre)*3.2,$Incremento,$cs->nombre."  ".$compose,1,0,'L');	
			}
			
			//$P->cell(10,));
		}
		//$P->setfont('Arial','B',10);
		$Y=$P->y+$Incremento;
		$P->setxy(22,$Y);$P->cell(181.6,$Incremento,"DATOS DEL USUARIO / ARRENDATARIO:",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		$P->setxy(22,$Y);
		$P->cell(50,$Incremento,"CEDULA: ".coma_format($Autorizado_id),1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setxy(20,$Y);
		$P->cell(131.6,$Incremento,"NOMBRES Y APELLIDOS: $Autorizado_nombre",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','B',10);
		$P->setxy(22,$Y);
		$P->cell(50,$Incremento,"ASEGURADORA:",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		$P->cell(131.6,$Incremento,"$Aseguradora->razon_social",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','B',10);
		$P->setxy(22,$Y);
		$P->cell(50,$Incremento,"DIAS DE SERVICIO:",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		$P->cell(31.6,$Incremento,"$Siniestro->dias_servicio",1,0,'L');
		
		
		
		//Añadir que entre en la validacion cuando este en servicio 
		if($Siniestro->estado == 3 || $Siniestro->estado == 7)
		{
			$P->cell(20,$Incremento,"GARANTÍA:",1,0,'L');
				
			if($Sin_autor)
			{
				$sumatoria = qo("select SUM(valor) as sumatoria from sin_autor where siniestro = '".$Siniestro->id."' and estado = 'A'  ");
				
				if(strlen($Sin_autor->vencimiento_mes) > 0 )
				{
					$P->cell(80,$Incremento,"CONGELAMIENTO $".$sumatoria->sumatoria,1,0,'L');		
				}	
				/*if($Sin_autor->aut_fac == 1 and strlen($Sin_autor->vencimiento_mes) == 0 )
				{
					$P->cell(80,$Incremento,"NO REEMBOLSABLE $".$sumatoria->sumatoria,1,0,'L');		
				}*/
				if(strlen( $Sin_autor->numero_consignacion) > 0 and $Sin_autor->aut_fac == 0 and strlen($Sin_autor->vencimiento_mes) == 0)
				{
					$P->cell(80,$Incremento,"REEMBOLSABLE $".$sumatoria->sumatoria,1,0,'L');
				}
				//strlen($Sin_autor->numero_consignacion) > 0 and  validacion quitada para el if de abajo
				if($Sin_autor->aut_fac == 1 )
				{
					$P->cell(80,$Incremento,"PROTECCIÓN TOTAL $".$sumatoria->sumatoria,1,0,'L');
				}				 
		
			}
			else{
				$P->cell(80,$Incremento,"",1,0,'L');
			}			
						
		}
		else{
			$P->cell(100,$Incremento,"",1,0,'L');
		}
		
		$P->setxy(22,$Y);
		$P->cell(50,$Incremento,"OFICINA AOA QUE ATIENDE:",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		$P->cell(40,$Incremento,"$Oficina->nombre",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','B',10);
		$P->cell(43,$Incremento,"SINIESTRO O SERVICIO:",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		$P->cell(48.6,$Incremento," $Siniestro->numero",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','B',10);
		$P->setxy(22,$Y);
		$P->cell(50,$Incremento,"TIPO DE GARANTIA:",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		$P->cell(131.6,$Incremento,"$TG",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','B',10);
		$P->setxy(22,$Y);
		$P->cell(50,$Incremento,"VOUCHER No.: ",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		$P->cell(40,$Incremento,"$TGV",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','B',10);
		$P->cell(91.6,$Incremento,"NUMERO DE CUOTAS:_______________________________",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		//$P->setfont('Arial','B',10);
		$P->setxy(22,$Y);
		$P->cell(50,$Incremento,"FECHA DE ENTREGA: ",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		;$P->cell(131.6,$Incremento,"".fecha_completa($Cita->fecha)." ".$Cita->hora,1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','B',10);
		$P->setxy(22,$Y);
		$P->cell(50,$Incremento,"FECHA DE DEVOLUCION: ",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		$P->cell(50.6,$Incremento,"".fecha_completa($Fdevol)." ".$Hdevol,1,0,'L');
		//$Y=$P->y+$Incremento;
		/*$P->setfont('Arial','B',10);
		$P->setxy(20,$Y+1);$P->cell(90,$Incremento,"PICO Y PLACA: ");$Y=$P->y+$Incremento;
		$P->setfont('Arial','',10);*/
		//$P->setxy(22,$Y);
		//$P->setfont('Arial','B',10);
		
		//personalizado a mapfre
		if($Aseguradora->id == 4 && $Siniestro->dias_servicio >= 10)
		{
			$Aseguradora->limite_kilometraje = 0;	
		}
		
	
		
		if($Aseguradora->limite_kilometraje == 0){$kilometrj="ILIMITADO";}else{$kilometrj=coma_format($Aseguradora->limite_kilometraje);}
		$P->cell(81,$Incremento,"LIMITE DE KILOMETRAJE:     ".$kilometrj,1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		//$P->setfont('Arial','B',10);
		$P->setxy(22,$Y);
		$P->cell(50,$Incremento,"DOMICILIO ENTREGA:",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		$P->cell(50.6,$Incremento,"SI __     NO__",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','B',10);
		//$P->setxy(22,$Y);
		$P->cell(50,$Incremento,"DOMICILIO DEVOLUCION:",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		$P->cell(31,$Incremento,"SI __     NO__",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','B',10);
		$P->setxy(22,$Y);
		$P->cell(50,$Incremento,"COBERTURA ADICIONAL:",1,0,'L');
		$Y=$P->y+$Incremento;
		//$P->setfont('Arial','',10);
		$P->cell(131.6,$Incremento,"SI __     NO__",1,0,'L');
		$Y=$P->y+$Incremento;
		$P->setfont('Arial','B',9);
		$P->setxy(20,$Y+2);$P->cell(50,$Incremento,"CLAUSULAS:");$Y=$P->y+$Incremento;
		$P->setfont('Arial','',8);
		
		$P->setxy(20,$Y+1);
		$P->multicell(180,3,utf8_decode("Cláusula 1: El  ARRENDATARIO Y/O USUARIO  de manera voluntaria y dando certeza de que todo lo aquí consignado  es cierto, por medio del presente documento ".
			"declara que lo recursos entregados provienen de la ocupación, profesión u oficio que desarrolla, los cuales no se enmarcan dentro de ninguna actividad ilícita de las contempladas en ".
			"el Código Penal Colombiano o en cualquier norma que lo modifique, adicione o complemente."));
		
		$Y=$P->y+2;$P->setxy(20,$Y);
		$P->multicell(180,3,utf8_decode("Cláusula 2:  El  ARRENDATARIO Y/O USUARIO  declara y reconoce que recibe el vehículo especificado en el estado de conservación y funcionamiento de acuerdo ".
			"con el Acta de Entrega."));
		
		$Y=$P->y+2;$P->setxy(20,$Y);
		$P->multicell(180,3,utf8_decode("Cláusula 3: El  ARRENDATARIO Y/O USUARIO  deberá hacer entrega del VEHÍCULO en las mismas condiciones mecánicas, de limpieza y de conservación en las ".
			"que fue entregado, así como el tanque de combustible lleno. El  ARRENDATARIO Y/O USUARIO  deberá sufragar el costo de cualquier gasto adicional en que  incurra, como chofer, ".
			"combustibles a la tarifa establecida por el Proveedor, entrega a domicilio y en general, cualquier otro gasto en el que se incurra por el uso del automóvil dado en préstamo."));
		
		$Y=$P->y+2;$P->setxy(20,$Y);
		$P->multicell(180,3,utf8_decode("Cláusula 4: Cuando se trate de un contrato de arrendamiento, bajo la modalidad de un vehículo en sustitución de las aseguradoras y el vehículo no fuera devuelto ".
			"dentro del plazo establecido en el presente contrato, el usuario se compromete a asumir la tarifa de 11 SMDLV + IVA por cada día hora o fracción que se genere por todo el tiempo ".
			"adicional en que el vehículo se encuentre en su poder."));
		
		$Y=$P->y+2;$P->setxy(20,$Y);
		$P->multicell(180,3,utf8_decode("Cláusula 5: El  ARRENDATARIO Y/O USUARIO  debe cumplir con las obligaciones establecidas por las normas vigentes de tránsito y toda la normatividad que se ".
			"relacione con el uso de vehículos. En caso de incumplimiento deberá responder ante El Proveedor y las respectivas autoridades por las infracciones de tránsito que se produzcan ".
			"durante la vigencia del servicio de VEHICULO  y que sean causadas directamente por El  ARRENDATARIO Y/O USUARIO  o el conductor. Si El  ARRENDATARIO Y/O USUARIO  ".
			"pretende acogerse a los derechos dispuestos en el artículo 136 de la ley 769 de 2002, tendrá dos (2) días a partir de la comunicación que reciba del Proveedor para manifestar si se ".
			"acoge a los mismos so pena de considerar la renuncia expresa a tales derechos."));
		
		$Y=$P->y+2;$P->setxy(20,$Y);
		$P->multicell(180,3,utf8_decode("Cláusula 6: Suscripción Irrevocable. El  ARRENDATARIO Y/O USUARIO  autoriza de manera expresa e irrevocable a la ARRENDADORA el congelamiento de cupo ".
			"y/o venta no presencial con la tarjeta de crédito que relaciono, conforme los términos y condiciones del presente contrato, . Esto sin el perjuicio que LA ARRENDADORA pueda acudir ".
			"a otras vías legales para el pago de cualquier importe conforme el presente contrato.  PARAGRAFO: Por su seguridad se solicita el código de seguridad de la tarjeta de crédito, con el ".
			"fin de poder realizar la transacción autorizada mediante esta cláusula.".
			"Parágrafo Primero: Garantía: Para efectos de la anulación de la garantía AOA Colombia tendrá 10 días hábiles a partir de la devolución ".
			"del vehículo sustituto, para la verificación de comparendos electrónicos y posterior anulación de la garantía (voucher o efectivo), en caso de no encontrar ninguna otra novedad por el uso del automóvil dado ".
			"en préstamo.Parágrafo segundo: Ante cualquier suma adeudada a la Arrendadora, el Arrendatario autoriza con la firma del presente documento, al cobro de intereses moratorios a la tasa máxima autorizada por la ".
			"Superintendencia Financiera de Colombia Parágrafo tercero: Ante cualquier inconveniente que se presente, por temas derivados del congelamiento de cupo, con la entidad financiera a la cual está vinculada la tarjeta 
			de crédito presentada como garantía, será el ARRENDATARIO Y/O USUARIO el encargado de realizar los trámites necesarios ante la entidad financiera para superar el inconveniente; ya que esta última es la responsable de 
			efectuar dicho levantamiento."));
		
		$Y=$P->y+2;$P->setxy(20,$Y);
		$P->multicell(180,3,utf8_decode("Cláusula 7: Autorizo de manera expresa e irrevocable a LA ARRENDADORA , en nombre propio y/o en representación tal y como aparece al pie de mi firma, a  ".
			"SOLICITAR, CONSULTAR, PROCESAR Y/O REPORTAR A DATACRÉDITO, y/o Registros Públicos como entidades que maneja y administra bases de datos, toda la información y ".
			"referencias relativas a mi nombre y/o a la empresa que represento. Mis derechos y obligaciones así como la permanencia de mi información en las bases de datos ".
			"corresponderán a lo estipulado por la ley 1266 de 2008 (Habeas Data)."));		
		
		$Y=$P->y+2;$P->setxy(20,$Y);
		$P->multicell(180,3,utf8_decode("Cláusula 8: Aplican restricciones y condiciones para cada tarifa según su vigencia."));
		
		
		$Y=$P->y+2;$P->setxy(20,$Y);
		$P->multicell(180,3,utf8_decode("Cláusula 9: La mayoría de los vehículos cuentan con un dispositivo de rastreo satelital, para salvaguardar los intereses de la compañía, cualquier alteración o daño de este dispositivo, será responsabilidad del Arrendatario y/o Usuario, que tenga el vehículo en custodia. "));
		
		$Y=$P->y+2;$P->setxy(20,$Y);
		$P->multicell(180,3,utf8_decode("Cláusula 10: Con la firma del presente anexo el ARRENDATARIO Y/O USUARIO acepta los términos establecidos en las condiciones CONTRATO DE ARRENDAMIENTO DE VEHÍCULOS CONDICIONES GENERALES, el cual ha sido puesto a su disposición para su lectura y conocimiento."));
		
		
		$Y=$P->y+2;$P->setxy(20,($Y-1));
		
		$P->multicell(180,3,utf8_decode("AUTORIZACIÓN TRATAMIENTO DE DATOS PERSONALES: En mi calidad de titular de la información AUTORIZO de manera previa, expresa y voluntaria, a ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S. (en adelante AOA) para que realice el tratamiento de mis datos personales, dentro del marco del objeto del presente CONTRATO. Asimismo, declaro que: (i) me han sido informadas las finalidades del tratamiento de mis datos (ii) conozco el 'Manual de Políticas y Procedimientos para la Protección de Datos Personales' de la compañía, la cual puede ser consultada en nuestra página web www.aoacolombia.com (iii) tengo claridad en relación con la existencia de los canales de atención dispuestos por AOA para efectos de ejercer los derechos que ostento como titular de la información. "));
		$P->setfont('Arial','',8);
		$Y=$P->y+1;
		$P->setxy(20,$Y);
		$P->multicell(180,3,utf8_decode("Cláusula 11: Por medio de la presente firma AUTORIZO de manera general a AOA S.A.S., a NOTIFICARME en la dirección de correo electrónico informada, todos los Actos, comunicaciones, decisiones y tramites en los cuales tengo interés, de carácter particular y concreto en relación con el contrato comercial suscrito."));		
		
		$P->setfont('Arial','',7);
		$Y=$P->y+$Incremento+9;
		if($ImgConsulta->firmaArrendatario){
			$P->Image($ImgConsulta->firmaArrendatario,15,248,60,7);
		}else{
			$P->Image($filepath2,15,248,60,7);
		}
		$P->setxy(20,$Y-1);$P->cell(40,1,"________________________________");
		$P->setxy(20,$Y);$P->cell(50,4,"Nombre Usuario / Arrendatario");
		
		if($ImgConsulta->firmaTarjetaHabiente){
			$P->Image($ImgConsulta->firmaTarjetaHabiente,75,248,60,7);
		}else{
			$P->Image($filepath3,75,248,60,7);
		}
		
		
		$P->setxy(85,$Y-1);$P->cell(40,1,"________________________________");
		$P->setxy(85,$Y);$P->cell(50,4,"Nombre Tarjeta Habiente");
		if($ImgConsulta->firmaPersonaJuridica){
			$P->Image($ImgConsulta->firmaPersonaJuridica,140,248,60,7);
		}else{
			$P->Image($filepath4,140,248,60,7);
		}
		
		$P->setxy(150,$Y-1);$P->cell(40,1,"________________________________");
		$P->setxy(150,$Y);$P->cell(50,4,utf8_decode("Nombre Persona Jurídica"));
		//$P->setfont('Arial','',7);
		$P->setxy(20,$Y+3);$P->cell(50,4,"c.c");
		$P->setxy(20,$Y+6);$P->cell(50,4,"Firma");
		$P->setxy(20,$Y+9);$P->cell(50,4,"Correo");
		$P->setxy(85,$Y+3);$P->cell(50,4,"c.c");
		$P->setxy(85,$Y+6);$P->cell(50,4,"Firma");
		$P->setxy(85,$Y+9);$P->cell(50,4,"Correo");
		$P->setxy(150,$Y+3);$P->cell(50,4,"Nit.");
		$P->setxy(150,$Y+6);$P->cell(50,4,"Firma");
		$P->setxy(150,$Y+9);$P->cell(50,4,"Correo");
		//$P->setxy(150,$Y+9);$P->cell(50,4,"Nombre Representante Legal:");
		$Y=$P->y+8;
		//$P->setfont('Arial','B',7);
		//$P->setfont('Arial','',7);
		//$P->setxy(20,$Y+3);$P->cell(50,4,"Nit:");
        $P->setfont('Arial','B',8);
	    $P->setxy(10,268);$P->multicell(198,4,"____________________________________________________________________________________________________________________________",0,'C');
	    $P->setfont('Arial','',8);
	    //$P->setxy(10,267);$P->multicell(198,4,"Carrera 69 B No. 98 A - 10 • PBX: +(571) 756 0510 • Fax: 756 0510 Ext. 112 • Bogotá D.C., Colombia • www.aoacolombia.com",0,'C');		
	
	$ArchivoPdf = $P->Output('acta.pdf','S');
	$siniestro = $Cita->siniestro;
	$ruta = directorio_imagen('siniestro',$siniestro);
	$nombreArchivo = "img_inv_salida_f_".$siniestro.".pdf";
	$rutaTotalArchivoPDF = $ruta.$nombreArchivo;
	$attachment = chunk_split(base64_encode($ArchivoPdf));
	$data = base64_decode($attachment,true);
	file_put_contents($rutaTotalArchivoPDF,$data);
	q("update siniestro set img_inv_salida_f = '$rutaTotalArchivoPDF' where id = $siniestro");

?>