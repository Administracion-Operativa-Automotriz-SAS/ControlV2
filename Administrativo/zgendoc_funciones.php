<?php
/***
PROGRAMA QUE GENERA LOS DOCUMENTOS 
se invoca desde gendoc.php

***/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);

if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}

function gendoc_memorando_ok()
{
	global $para,$de,$asunto,$alcance,$contenido,$firma,$cargo,$consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT).'-'.date('y');
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');$Fecha_completa=fecha_completa($Hoy);
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','$para','$para','','','$asunto')");
	$Tam_Fuente='12px';
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=COMUNICADO_INTERNO_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<table width='90%' border cellspacing='1' align='center'><tr><td width='20%' align='center' valign='middle'>
			<img src='http://app.aoacolombia.com/Administrativo/img/nlogo_aoa_200.jpg' border='0' height='50' width='110'>
		</td><td align='center' width='50%'><b style='font-size:16px'>COMUNICADO INTERNO</b></td>
		<td width='30%' style='font-size:12px'>$Tipo->sigla-$Nconsecutivo</td></tr>
		</table>
		<table  border cellspacing='1' width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;'>DE:</td><td style='font-size:$Tam_Fuente;'><b>$de</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>DIRIGIDOooooo A:</td><td style='font-size:$Tam_Fuente;'><b>$para</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>ALCANCE:</td><td style='font-size:$Tam_Fuente;'><b>$alcance</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>ASUNTO:</td><td style='font-size:$Tam_Fuente;'><b>$asunto</b></td></tr>
		</table>
		&nbsp;
		<p style='font-size:$Tam_Fuente'>Bogotá D.C. $Fecha_completa</p>&nbsp;
		&nbsp;";
	$Partes_contenido=explode(chr(13),$contenido);
	for($i=0;$i<count($Partes_contenido);$i++) 
		echo "<p align='justify' style='font-size:$Tam_Fuente;font-family:arial;'>".$Partes_contenido[$i]."</p>";
	echo "&nbsp;<p style='font-size:$Tam_Fuente;font-family:arial;'>Cordialmente</p>&nbsp;&nbsp;
			<p style='font-size:$Tam_Fuente;font-family:arial;'><b>$firma</b>&nbsp;<b>$cargo</b>";
	echo"</body></html>";
}

function gendoc_comunicacion_interna_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;

	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}	
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT).'-'.date('y');
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');$Fecha_completa=fecha_completa($Hoy);
	
	
	
	include 'views/gestion_documental/comunicacion_interna.html';	
}


function formato_novedad_vehiculo_reemplazo()
{
	global $consecutivo,$tipodoc,$Nusuario;

	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}	
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT).'-'.date('y');
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');$Fecha_completa=fecha_completa($Hoy);
	
	
	
	include  'views/gestion_documental/formato_novedad_vehiculo_reemplazo.html';
	
}



function gendoc_entrega_soat_ok()
{
	global $para,$de,$asunto,$contenido,$firma,$cargo,$consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','$para','$para','','','$asunto')");
	$Tam_Fuente='12px';
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=ENTREGA_SOAT_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_documento("ENTREGA SOAT").
		"<table width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' width='90'>Fecha</td><td style='font-size:$Tam_Fuente;' width='500'>$Ahora&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>PARA</td><td style='font-size:$Tam_Fuente;'><b>$para</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>DE</td><td style='font-size:$Tam_Fuente;'><b>$de</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>ASUNTO</td><td style='font-size:$Tam_Fuente;'><b>$asunto</b></td></tr>
		</table>
		<p align='right' style='font-size:$Tam_Fuente'><b>Consecutivo: $Tipo->sigla - $Nconsecutivo</b></p>
		&nbsp;";
	$Partes_contenido=explode(chr(13),$contenido);
	for($i=0;$i<count($Partes_contenido);$i++) 
		echo "<p align='justify' style='font-size:$Tam_Fuente;font-family:arial;'>".$Partes_contenido[$i]."</p>";
	echo "&nbsp;<p style='font-size:$Tam_Fuente;font-family:arial;'>Cordialmente</p>&nbsp;&nbsp;
			<p style='font-size:$Tam_Fuente;font-family:arial;'><b>$firma</b>&nbsp;<b>$cargo</b>&nbsp;C.C. Hoja de vida</p>";
	echo"</body></html>";
}

function gendoc_autorizacion_retiro_vehiculo_ok()
{
	global $para,$de,$asunto,$contenido,$firma,$cargo,$consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','$para','$para','','','$asunto')");
	$Tam_Fuente='12px';
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=AUTORIZACION_RETIRO_VEHICULO_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_documento("AUTORIZACION RETIRO VEHICULO").
		"<table width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' width='90'>Fecha</td><td style='font-size:$Tam_Fuente;' width='500'>$Ahora&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>PARA</td><td style='font-size:$Tam_Fuente;'><b>$para</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>DE</td><td style='font-size:$Tam_Fuente;'><b>$de</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>ASUNTO</td><td style='font-size:$Tam_Fuente;'><b>$asunto</b></td></tr>
		</table>
		<p align='right' style='font-size:$Tam_Fuente'><b>Consecutivo: $Tipo->sigla - $Nconsecutivo</b></p>
		&nbsp;";
	$Partes_contenido=explode(chr(13),$contenido);
	for($i=0;$i<count($Partes_contenido);$i++) 
		echo "<p align='justify' style='font-size:$Tam_Fuente;font-family:arial;'>".$Partes_contenido[$i]."</p>";
	echo "&nbsp;<p style='font-size:$Tam_Fuente;font-family:arial;'>Cordialmente</p>&nbsp;&nbsp;
			<p style='font-size:$Tam_Fuente;font-family:arial;'><b>$firma</b>&nbsp;<b>$cargo</b>&nbsp;AOA Colombia S.A.</p>";
	echo"</body></html>";
}

function gendoc_siniestros_envio_papeleria_ok()
{
	global $para,$oficina,$de,$asunto,$contenido,$firma,$cargo,$consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','$para','$para','','','$asunto')");
	$Tam_Fuente='12px';
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=ENVIO_PAPELERIA_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_documento("ENVIO PAPELERIA").
		"<table width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' width='90'>Fecha</td><td style='font-size:$Tam_Fuente;' width='500'>$Ahora&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>PARA</td><td style='font-size:$Tam_Fuente;'><b>$para</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>OFICINA</td><td style='font-size:$Tam_Fuente;'><b>$oficina</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>DE</td><td style='font-size:$Tam_Fuente;'><b>$de</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>ASUNTO</td><td style='font-size:$Tam_Fuente;'><b>$asunto</b></td></tr>
		</table>
		<p align='right' style='font-size:$Tam_Fuente'><b>Consecutivo: $Tipo->sigla - $Nconsecutivo</b></p>
		&nbsp;";
	$Partes_contenido=explode(chr(13),$contenido);
	for($i=0;$i<count($Partes_contenido);$i++) 
		echo "<p align='justify' style='font-size:$Tam_Fuente;font-family:arial;'>".$Partes_contenido[$i]."</p>";
	echo "&nbsp;<p style='font-size:$Tam_Fuente;font-family:arial;'>Cordialmente</p>&nbsp;&nbsp;
			<p style='font-size:$Tam_Fuente;font-family:arial;'><b>$firma</b>&nbsp;<b>$cargo</b></p>";
	echo"</body></html>";
}

function gendoc_circular_informativa_ok()
{
	global $de,$asunto,$firma,$cargo,$consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','$para','$para','','','$asunto')");
	$Tam_Fuente='12px';
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=CIRCULAR_INFORMATIVA_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>
		<table width='90%' align='center'><tr><td width='160'>
			<img src='http://app.aoacolombia.com/Administrativo/img/nlogo_aoa_200.jpg' border='0' height='90' width='190'>
		</td><td align='center'><b style='font-size:18px'>CIRCULAR INFORMATIVA</b></td><td width='160'>
		<p style='font-size:8px'>NOTA: Este formato debe ser diligenciado luego de que se ha enviado un correo electrónico de manejo Interno a los consultores y cuya información contenida se ha de conocimiento general o que altere la gestión que se suministra.</p>
		</td></tr></table>
		<table width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' width='90'>Fecha</td><td style='font-size:$Tam_Fuente;' width='500'>$Ahora&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>DE</td><td style='font-size:$Tam_Fuente;'><b>$de</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>ASUNTO</td><td style='font-size:$Tam_Fuente;'><b>$asunto</b></td></tr>
		</table>
		<p align='right' style='font-size:$Tam_Fuente'><b>Consecutivo: $Tipo->sigla - $Nconsecutivo</b></p>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<table border cellspacing='0' width='90%' align='center'>
			<tr>
				<td width='160' valign='bottom' align='center'><b style='font-size:8px'>&nbsp;&nbsp;&nbsp;&nbsp;$firma&nbsp;$cargo</b></td>
				<td width='160' valign='bottom' align='center' style='font-size:8px'>Firma del Coordinador Operativo</td>
				<td width='160' valign='bottom' align='center' style='font-size:8px'>Firma del Director de Call Center</td>
			</tr>
		</table>
		</body></html>";
}

///******************************************************************************************************
// CABECERA PARA TODOS LOS FORMATOS

function cabecera_formato($Tipo,$TITULO='',$Nc='')
{
	return "<table width='90%' class='table_image' border cellspacing='0' align='center'><tr><td width='20%' rowspan=3 align='center' valign='middle'>
			<img src='http://app.aoacolombia.com/Administrativo/img/nlogo_aoa_200.jpg' border='0' class='print_image' height='50' width='110'>
		</td><td align='center' rowspan=3 width='50%'><b style='font-size:16px'>$TITULO</b></td>
		<td width='30%' style='font-size:12px'>CODIGO: $Tipo->sigla</td></tr>
		<tr><td style='font-size:12px'>VERSION: $Tipo->version</td></tr><tr><td style='font-size:10px'>FECHA DE VIGENCIA: $Tipo->vigencia</td></tr>
		</td></tr></table>
		<table border='0' cellspacing='0' cellpadding='0' width='90%' align='center'><tr><td width='20%'></td><td align='center' style='font-size:8px;' width='50%'><span id='subtitle'>".utf8_encode("Consecutivo Interno Número")."</span>: $Nc</td><td width='30%'></td></tr></table>";
}


///******************************************************************************************************
function gendoc_formato_acta_ok()
{
	global $lugar,$nombre_reunion,$firma,$cargo,$consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','$nombre_reunion')");
	$Tam_Fuente='12px';
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=ACTA_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;ACTA",$Nconsecutivo)."&nbsp;
		$nombre_reunion
		<table width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' width='90'>FECHA:</td><td style='font-size:$Tam_Fuente;' width='500'>$Ahora</td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>HORA:</td><td style='font-size:$Tam_Fuente;'><b>(__:__ AM/PM A __:__ AM/PM)</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>LUGAR:</td><td style='font-size:$Tam_Fuente;'><b>$lugar</b></td></tr>
		</table>&nbsp;
		<table width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' width='90'>ASISTENTES:</td><td style='font-size:$Tam_Fuente;' width='500'>(Nombre, Cargo; Nombre, Cargo)</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='90'>INVITADOS:</td><td style='font-size:$Tam_Fuente;' width='500'>(Nombre, Cargo; Nombre, Cargo)</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='90'>AUSENTES:</td><td style='font-size:$Tam_Fuente;' width='500'>(Nombre, Cargo; Nombre, Cargo)</td></tr>
		</table>&nbsp;
		ORDEN DEL DÍA &nbsp;
		<ol>
		<li>Verificación de Quorum
		<li>Lectura, discusión y aprobación Acta anterior
		<li>(Tema a tratar en la reunión)
		<li>(Varios)
		<li>(Compromisos acordados - Responsable - Fecha)
		<li>Plan de seguimiento - Actividades programadas - Actividades ejecutadas
		</ol>
		DESARROLLO
		<ol>
		<li>(Escribir en tiempo pasado)
		</ol>
		COMPROMISOS
		<table border cellspacing='0' align='center' width='90%'>
		<tr><td align='center'>Acciones Claves</td><td align='center'>Responsables</td><td align='center'>Fecha de Programación</td></tr>
		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		</table>
		&nbsp;
		PLAN DE SEGUIMIENTO
		<table border cellspacing='0' align='center' width='90%'>
		<tr><td align='center'>Acciones Programadas</td><td align='center'>Actividades Ejecutadas</td></tr>
		<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
		</table>
		&nbsp;
		CONVOCATORIA&nbsp;
		(Aplica en los casos en que se programa una nueva reunión)&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<table border cellspacing='0' width='90%' align='center'>
			<tr>
				<td width='160' valign='bottom' align='center'><b style='font-size:8px'>&nbsp;&nbsp;&nbsp;&nbsp;$firma&nbsp;$cargo</b></td>
				<td width='160' valign='bottom' align='center' style='font-size:8px'>Nombres y Apellidos&nbsp;Cargo</td>
			</tr>
		</table>&nbsp;&nbsp;
		<p style='font-size:9px'>Proyectó: (nombre y apellidos, cargo, dependencia)</br>Revisó: (nombre y apellidos, cargo, dependencia)</p>
		
		</body></html>";
}

function gendoc_formato_acta_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Ahora=date('H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='12px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/formato_acta_reunion.html';
	
}

function gendoc_prestamo_documentos_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=PRESTAMO_DOCUMENTOS_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;PLANILLA PRESTAMO DE DOCUMENTOS",$Nconsecutivo)."&nbsp;

		<table border cellspacing='0' cellpadding='0' width='100%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' >DEPENDENCIA: _____________________________________</td></tr>
		</table>&nbsp;
		
		<table border cellspacing='0' cellpadding='0' width='100%' align='center'>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='7%' align='center' rowspan=2>Código&nbsp;Serie&nbsp;Subserie</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Descripción de la carpeta&nbsp;o expediente</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' colspan=4>Préstamo</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' colspan=3>Devolución</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Observaciones</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Código&nbsp;dependencia</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Fecha&nbsp;aa-mm-dd</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Nombre&nbsp;Funcionario</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Firma</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Fecha&nbsp;aa-mm-dd</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Funcionario que &nbsp;recibe</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Firma</td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		</table>&nbsp;
		</body></html>";
}

function gendoc_prestamo_documentos_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}	
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/prestamo_documentos.html';
	
}


function gendoc_formato_normograma_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=NORMOGRAMA_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;NORMOGRAMA",$Nconsecutivo)."
		&nbsp;

		<table border cellspacing='0' cellpadding='0' width='100%' align='center'>
			<td style='font-size:$Tam_Fuente;' >Fecha de Actualización:</td></tr>
		</table>&nbsp;
		
		<table border cellspacing='0' cellpadding='0' width='100%' align='center'>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='7%' align='center' colspan=4>Tipo</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Número</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Fecha de Vigencia</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' colspan=2>Implementada</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Acciones a tomar si no ha sido&nbsp;implementada</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Ley</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Decreto</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Resolución</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Otra</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Si</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>No</td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		</table>&nbsp;
		</body></html>";
}

function gendoc_formato_normograma_ok_2() 
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/formato_normograma.html';
}




function gendoc_acta_eliminacion_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/acta_eliminacion.html';

}


function gendoc_acta_eliminacion_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=ACTA_ELIMINACION_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;ACTA DE ELIMINACIÓN",$Nconsecutivo)."&nbsp;

<p style='font-size:$Tam_Fuente;text-align:justify;' >En __________________, a los ______ días del mes de ____________ de ______ siendo las ________ se reunieron en el Archivo Central
ubicado en AOA Colombia, funcionario(s): ___________________ ____________________ _____________________ del Proceso de Gestión Documental 
y ________________________ ___________________ de la dependencia, ___________________________________  con el fin de dar inicio al proceso
de destrucción de los documentos relacionados en esta Acta, aprobada mediante la Tabla de Retención Documental.</p>
<p style='font-size:$Tam_Fuente;text-align:justify;' > Por lo anterior se procede a la destrucción de los siguientes documentos:
		<table border cellspacing='0' cellpadding='0' width='100%' align='center'>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>No. de&nbsp;Orden</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Nombre de las Series y Subseries&nbsp;o Asuntos</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Nombre del Expediente</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' colspan=2>Fechas</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' colspan=3>Unidad de&nbsp;Conservación</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Folios</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' colspan=2>Digitalizado</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Inicial</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Final</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Carp.</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Tomo</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Otro</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Si</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>No</td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		</table>&nbsp;
		<p style='font-size:$Tam_Fuente1;text-align:right;'>Método de Eliminación:   PICADO _____  RASGADO: ______</p>
		<table width='100%'>
			<tr><td style='font-size:$Tam_Fuente1;'>Nombre:</td><td>__________________</td>
					<td style='font-size:$Tam_Fuente1;'>Nombre:</td><td>__________________</td>
					<td style='font-size:$Tam_Fuente1;'>Nombre:</td><td>__________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente1;'>Cargo:</td><td>__________________</td>
					<td style='font-size:$Tam_Fuente1;'>Cargo:</td><td>__________________</td>
					<td style='font-size:$Tam_Fuente1;'>Cargo:</td><td>__________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente1;'>Firma:</td><td>__________________</td>
					<td style='font-size:$Tam_Fuente1;'>Firma:</td><td>__________________</td>
					<td style='font-size:$Tam_Fuente1;'>Firma:</td><td>__________________</td></tr>
		</table>
		</body></html>";
}

function gendoc_tabla_retencion_documental_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=TABLA_RETENCION_DOCUMENTAL_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;TABLA DE RETENCION DOCUMENTAL",$Nconsecutivo)."
		&nbsp;
	<table width='100%' border cellspacing='0'>
		<tr><td style='font-size:$Tam_Fuente;'>ENTIDAD PRODUCTORA: ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.</td>
		<td align='right' style='font-size:$Tam_Fuente;'>HOJA: ______  DE: ______</td></tr>
		<tr><td style='font-size:$Tam_Fuente;'>DEPENDENCIA PRODUCTORA: </td>
		<td style='font-size:$Tam_Fuente;'>CODIGO:</td></tr>
	</table>
	<table border cellspacing='0' cellpadding='0' width='100%' align='center'>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' colspan=3 width='9%'>CÓDIGO</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2 colspan=2 width='10%'>SERIES, SUBSERIES Y TIPOS DOCUMENTALES</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' colspan=2 width='10%'>RETENCIÓN EN AÑOS</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' colspan=4 width='10%'>DISPOSICIÓN&nbsp;FINAL</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2 width='50%'>PROCEDIMIENTO</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' width='3%'>D</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' width='3%'>S</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' width='3%'>SB</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' width='10%'>ARCHIVO DE&nbsp;GESTION</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' width='10%'>ARCHIVO&nbsp;CENTRAL</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' width='2%'>CT</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' width='2%'>E</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' width='2%'>D</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' width='2%'>S</td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		</table>
		<table border cellspacing='0' cellpadding='0' width='100%'>
			<tr>
				<td style='font-size:$Tam_Fuente1;'>
					<table width='100%'>
						<tr>
							<td style='font-size:$Tam_Fuente1;'>CONVENCIONES:</td>
							<td style='font-size:$Tam_Fuente1;'>D = Dependencia</td>
							<td style='font-size:$Tam_Fuente1;'>S = Serie</td>
							<td style='font-size:$Tam_Fuente1;'>SB = Subserie</td>
							<td style='font-size:$Tam_Fuente1;'>* = TIPO DOCUMENTAL</td>
						</tr>
						<tr>
							<td></td>
							<td style='font-size:$Tam_Fuente1;'>CT = Conservación Total</td>
							<td style='font-size:$Tam_Fuente1;'>E = Eliminación</td>
							<td style='font-size:$Tam_Fuente1;'>D = Digitalización</td>
							<td style='font-size:$Tam_Fuente1;'>S = Selección</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente1;'>FIRMA RESPONSABLE&nbsp;&nbsp;&nbsp;NORMATIVIDAD CONSULTADA:                           Acuerdo 039 de 2002 del Archivo General de la Nación</td></tr>
		</table>
		</body></html>";
}

function gendoc_tabla_retencion_documental_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/tabla_retencion_documental.html';
	
}

function gendoc_inventario_entrega_documental_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where id= '".$_POST['docid']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';	
	include 'views/gestion_documental/inventario_entrega_documental.html';
	
}

function gendoc_inventario_entrega_documental_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=INVENTARIO_ENTREGA_DOCUMENTAL_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;INVENTARIO ENTREGA DOCUMENTAL",$Nconsecutivo)."
		&nbsp;
	<table width='100%' border cellspacing='0'>
		<tr>
			<td style='font-size:$Tam_Fuente1;' colspan=4>ENTIDAD PRODUCTORA: ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.</td>
		</tr>
		<tr>
			<td style='font-size:$Tam_Fuente1;'>DEPENDENCIA PRODUCTORA</td>
			<td style='font-size:$Tam_Fuente1;' colspan='3' align='center'>REGISTRO DE ENTRADA</td>
		</tr>
		<tr>
			<td style='font-size:$Tam_Fuente1;'>SECCIÓN:</td>
			<td style='font-size:$Tam_Fuente1;' align='center'>Año</td>
			<td style='font-size:$Tam_Fuente1;' align='center'>Mes</td>
			<td style='font-size:$Tam_Fuente1;' align='center'>Día</td>
		</tr>
		<tr>
			<td style='font-size:$Tam_Fuente1;'>SERIE:</td>
			<td style='font-size:$Tam_Fuente1;'></td>
			<td style='font-size:$Tam_Fuente1;'></td>
			<td style='font-size:$Tam_Fuente1;'></td>
		</tr>
		<tr>
			<td style='font-size:$Tam_Fuente1;' colspan=4>SUBSERIE:</td>
		</tr>
	</table>
	<table border cellspacing='0' cellpadding='0' width='100%' align='center'>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Relación</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Número&nbsp;Orden</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Número&nbsp;Topográfico</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Título Unidad Documental</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' colspan=2>Fechas Extremas</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' colspan=4>Unidad de Conservación</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Número de&nbsp;Folios</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Soporte</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center' rowspan=2>Notas</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Inicial</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Final</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>No. Caja</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>No. Carpeta</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>No. Tomo</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Otro</td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		</table>
		<table border cellspacing='0' cellpadding='0' width='100%'>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center'>Elaboró</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>Entregó</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>Recibió y cotejó</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;'>Nombres y Apellidos:</td>
				<td style='font-size:$Tam_Fuente1;'>Nombres y Apellidos:</td>
				<td style='font-size:$Tam_Fuente1;'>Nombres y Apellidos:</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;'>Cargo:</td>
				<td style='font-size:$Tam_Fuente1;'>Cargo:</td>
				<td style='font-size:$Tam_Fuente1;'>Cargo:</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;'>Firma:</td>
				<td style='font-size:$Tam_Fuente1;'>Firma:</td>
				<td style='font-size:$Tam_Fuente1;'>Firma:</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;'>Lugar:</td>
				<td style='font-size:$Tam_Fuente1;'>Lugar:</td>
				<td style='font-size:$Tam_Fuente1;'>Lugar:</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;'>Fecha:</td>
				<td style='font-size:$Tam_Fuente1;'>Fecha:</td>
				<td style='font-size:$Tam_Fuente1;'>Fecha:</td>
			</tr>
		</table>
		</body></html>";
}

function gendoc_rev_aprob_docum_y_formatos_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=REV_APROB_DOCYFORM_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;REVISIÓN Y APROBACIÓN DE&nbsp;DOCUMENTOS Y FORMATOS",$Nconsecutivo)."
		&nbsp;
	<table width='100%' border cellspacing='0'>
		<tr>
			<td style='font-size:$Tam_Fuente1;' WIDTH='10%'>PROCESO</td><td style='font-size:$Tam_Fuente1;'>  </td>
		</tr>
	</table>
	<table border cellspacing='0' cellpadding='0' width='100%' align='center'>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Código</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Nombre del Documento</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Fecha de Vigencia</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Versión</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Revisado por (firma y cargo)</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Aprobado por (firma y cargo)</td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>
		</table>
		</body></html>";
}

function gendoc_rev_aprob_docum_y_formatos_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}	
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/revision_aprobacion_documentos_formatos.html';
	
}

function gendoc_listado_maestro_docyform_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='10px';
	$Tam_Fuente1='12px';
	/*$resultado = q("select Distinct(placa) as dplaca from aoacol_aoacars.vehiculo");
	$placas = array();
   	while ($placa = mysql_fetch_object($resultado)) {
		array_push($placas, $placa);
	}*/
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/listadomaestro_documentos.html';
	
}

function gendoc_listado_maestro_docyform_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=MAESTRO_DOCYFORM_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;LISTADO MAESTRO DE&nbsp;DOCUMENTOS Y FORMATOS",$Nconsecutivo)."
		&nbsp;
	<table width='100%' border cellspacing='0'>
		<tr>
			<td style='font-size:$Tam_Fuente1;' WIDTH='8%'>PROCESO</td><td style='font-size:$Tam_Fuente1;' WIDTH='40%'>  </td>
			<td style='font-size:$Tam_Fuente1;' align='center' >FECHA ACTUALIZACION</TD>
			<td style='font-size:$Tam_Fuente1;'WIDTH='20%' >  </TD>
		</tr>
	</table>
	<table border cellspacing='0' cellpadding='0' width='100%' align='center'>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Código</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Nombre del Documento</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Fecha de Vigencia</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Versión</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Descripción de Cambios</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Fecha de&nbsp;Recepción de&nbsp;Solicitud</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Estado&nbsp;(Vigente /&nbsp;Obsoleto/&nbsp;Borrador)</td>
				<td style='font-size:$Tam_Fuente1;' width='' align='center'>Publicado en&nbsp;el servidor&nbsp;(Si / No)</td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		</table>
		</body></html>";
}

function gendoc_solicitud_manejo_documentos_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;

	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/solicitud_manejo_documentos.html';
}

function gendoc_solicitud_manejo_documentos_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=MANEJODOC_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;SOLICITUD DE CREACION&nbsp;ACTUALIZACION O ELIMINACION DE&nbsp;DOCUMENTOS",$Nconsecutivo)."
		&nbsp;
	<table width='100%' border cellspacing='0' cellpadding=3>
		<tr>
			<td style='font-size:$Tam_Fuente;' align='center'><b>Información General</b></td>
		</tr>
		<tr>
			<td style='font-size:$Tam_Fuente;' >Fecha de Solicitud  |_________|______|_____|  Código (si aplica) |________________|   </td>
		</tr>
			<td style='font-size:$Tam_Fuente;' >Documento |___|  Formato |___|  Nombre |______________________________________________| </td>
		</tr>
		<tr>
			<td style='font-size:$Tam_Fuente;' ><b>Solicitante y Responsable</b></td>
		</tr>
		<tr>
			<td style='font-size:$Tam_Fuente;' >Proceso  |____________________|    Nombre del líder del proceso |____________________________| </td>
		</tr>
		<tr>
			<td style='font-size:$Tam_Fuente;' align='center'>Creación |__|  Modificación |__|  Eliminación |__| Documento Externo |______| </td>
		</tr>
	</table>&nbsp;
	<table border cellspacing='0' width='100%'>
		<tr>
			<td style='font-size:$Tam_Fuente;' align='center'><b>Descripción de la Solicitud</b></td>
		</tr>
		<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
		<tr><td style='font-size:$Tam_Fuente;'>Aprueba Lider de proceso   Si |___|   No |___| </td></tr>
	</table>&nbsp;
	<table border cellspacing='0' width='100%'>
		<tr>
			<td style='font-size:$Tam_Fuente;' align='center'><b>Espacio para ser diligenciado por el responsable del Sistema de Gestión de Calidad</b></td>
		</tr>
		<tr>
			<td style='font-size:$Tam_Fuente;'>Fecha recepción de la solicitud  |_______|___|___| </td>
		</tr>
		<tr>
			<td style='font-size:$Tam_Fuente;'><b>La solicitud es aprobada?</b>   Si |___|   No |___| </td>
		</tr>
		<tr>
			<td style='font-size:$Tam_Fuente;'>Fecha de entrega del borrador  |_______|___|___| </td>
		</tr>
	</table>
	</body></html>";
}
    
function gendoc_induccion_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	global $nombre_empleado,$fecha_induccion;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','$nombre_empleado','','','','Inducción $fecha_induccion')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=INDUCCION_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;INDUCCIÓN",$Nconsecutivo)."
		
		<p width='110%' style='font-size:$Tam_Fuente;text-align:justify;'>Para la Empresa es importante conocer su apreciación acerca del proceso de inducción que se lleva a cabo. Marque con una X en SI o NO dependiendo si el tema en mención le fue explicado y/o entregado en la Inducción.</p>
		
		<table width='100%' border cellspacing='0'>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center' colspan=3><b>DATOS GENERALES DE LA INDUCCIÓN</b></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=3>Nombre del empleado  $nombre_empleado</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=3>Fecha de realización de la inducción  $fecha_induccion</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center' colspan=3><b>INDUCCIÓN GENERAL</b></td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Los siguientes temas fueron explicados por el Director de Gestión Humana S&SO</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>SI</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>NO</b></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;' >1. Misión</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >2. Visión</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >3. Organigrama</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >4. Mapa de procesos</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >5. Política de calidad</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >6. Mapa estratégico</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Los siguientes documentos le fueron entregados por el Director de Gestión Humana S&SO</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>SI</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>NO</b></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;' >Fotocopia de sus funciones</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Fotocopia de su contrato</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Fotocopia de su anexo de confidencialidad</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Folleto de Direccionamiento Estratégico</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Fue presentado a los Empleados de la Empresa</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center' colspan=3><b>INDUCCIÓN ESPECIFICA AL CARGO</b></td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Los siguientes temas o documentos fueron explicados por el Jefe Inmediato</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>SI</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>NO</b></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;' >Procedimientos y manuales que orientan las actividades a realizar por parte del empleado</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Tema o documento:</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Tema o documento:</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Tema o documento:</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Tema o documento:</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Tema o documento:</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Tema o documento:</td><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' ></td></tr>
		</table>&nbsp;&nbsp;&nbsp;&nbsp;
		<p style='text-align:center;font-size:$Tam_Fuente;'>__________________________________________________&nbsp;
		Empleado que recibe la inducción&nbsp;&nbsp;
		Nombre:_______________________________________&nbsp;&nbsp;
		Cédula:________________________________________</p>
	</body></html>";
}

function gendoc_induccion_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
				
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	

		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
	
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	

	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/formato_induccion.html';

}

function gendoc_programa_capacitacion_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	global $nombre_empleado,$fecha_induccion;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','$nombre_empleado','','','','Inducción $fecha_induccion')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='7px';
	$Tam_Fuente2='6px';
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=PROG_CAPACITACION_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;PROGRAMA DE CAPACITACION",$Nconsecutivo)."
		<table width='100%' border cellspacing='0' cellpadding='0'>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center' rowspan=3><b>Nombre de la Capacitación</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center' rowspan=3><b>Capacitador</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center' rowspan=3><b>Dirigido a</b></td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'><b>Enero</b></td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'><b>Febrero</b></td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'><b>Marzo</b></td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'><b>Abril</b></td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'><b>Mayo</b></td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'><b>Junio</b></td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'><b>Julio</b></td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'><b>Agosto</b></td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'><b>Septiembre</b></td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'><b>Octubre</b></td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'><b>Noviembre</b></td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'><b>Diciembre</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center' rowspan=3><b>Hora</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'>Semanas</td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'>Semanas</td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'>Semanas</td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'>Semanas</td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'>Semanas</td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'>Semanas</td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'>Semanas</td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'>Semanas</td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'>Semanas</td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'>Semanas</td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'>Semanas</td>
				<td style='font-size:$Tam_Fuente2;' align='center' colspan=4 width='4%'>Semanas</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>1</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>2</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>3</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>4</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>1</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>2</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>3</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>4</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>1</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>2</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>3</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>4</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>1</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>2</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>3</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>4</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>1</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>2</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>3</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>4</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>1</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>2</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>3</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>4</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>1</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>2</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>3</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>4</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>1</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>2</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>3</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>4</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>1</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>2</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>3</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>4</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>1</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>2</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>3</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>4</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>1</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>2</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>3</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>4</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>1</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>2</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>3</td>
				<td style='font-size:$Tam_Fuente2;' align='center' width='1%'>4</td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		</table>
	</body></html>";
}

function gendoc_programa_capacitacion_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/programa_capacitacion.html';
	
}


function gendoc_verificacion_referencias_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	global $nombre_empleado,$firma,$cargo;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','$nombre_empleado','','','','Inducción $fecha_induccion')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=VERIFICACION_REFERENCIAS_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;VERIFICACION REFERENCIAS LABORALES Y PERSONALES",$Nconsecutivo)."
		&nbsp;
		<table width='100%' cellspacing='0'>
			<tr><td style='font-size:$Tam_Fuente;' width='40%'>NOMBRE</td><td style='font-size:$Tam_Fuente;' >$nombre_empleado</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >CEDULA</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >ASPIRANTE AL CARGO DE:</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >EMPRESA DE CONTACTO:</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >PERSONA DE CONTACTO:</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >TELEFONO:</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >PARENTESCO:</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >FECHA INGRESO:</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >FECHA RETIRO:</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >MOTIVO RETIRO:</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >LO VOLVERÍAN A CONTRATAR:</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >CARGO QUE OCUPABA:</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >FUNCIONES:</td><td style='font-size:$Tam_Fuente;' >&nbsp;____________________________________________________________
																																													&nbsp;&nbsp;____________________________________________________________
																																													&nbsp;&nbsp;____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >COMO TRABAJADOR:</td><td style='font-size:$Tam_Fuente;' >&nbsp;____________________________________________________________
																																													&nbsp;&nbsp;____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >COMO PERSONA:</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________
																																													&nbsp;&nbsp;____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >FECHA VERIFICACIÓN:</td><td style='font-size:$Tam_Fuente;' >____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >FIRMA PERSONA RESPONSABLE:</td><td style='font-size:$Tam_Fuente;' >&nbsp;&nbsp;____________________________________________________________</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' >$firma</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' ></td><td style='font-size:$Tam_Fuente;' >$cargo</td></tr>
			
		</table>
	</body></html>";
}

function gendoc_verificacion_referencias_ok_2()
{ 
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	

	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/verificacion_referencias.html';
	
}

function gendoc_registro_capacitacion_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','$nombre_empleado','','','','Inducción $fecha_induccion')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=REGISTRO_CAPACITACION_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;REGISTRO DE CAPACITACION",$Nconsecutivo)."
		&nbsp;
		<table width='100%' cellspacing='0' border>
			<tr><td style='font-size:$Tam_Fuente;' width='15%'>DIA:</td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='15%'>HORA:</td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='15%'>LUGAR:</td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='15%'>TEMA A TRATAR:</td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='15%'>RESPONSABLE:</td><td style='font-size:$Tam_Fuente;' ></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='15%'>DIRIGIDO A:</td><td style='font-size:$Tam_Fuente;' ></td></tr>
		</table>&nbsp;
		<table width='100%' cellspacing='0' border>
			<tr>
				<td align='center' style='font-size:$Tam_Fuente;' ><b><i>Cédula</i></b></td>
				<td align='center' style='font-size:$Tam_Fuente;' ><b><i>Nombre</i></b></td>
				<td align='center' style='font-size:$Tam_Fuente;' ><b><i>Contratista</i></b></td>
				<td align='center' style='font-size:$Tam_Fuente;' ><b><i>Firma</i></b></td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
		</table>&nbsp;&nbsp;
		<p style='font-size:$Tam_Fuente'>Método de Evaluación: ______________________________</p>
	</body></html>";
}

function gendoc_registro_capacitacion_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}	
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/registro_capacitacion.html';
	
}

function gendoc_eval_periodo_prueba_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','$nombre_empleado','','','','Inducción $fecha_induccion')");
	$Tam_Fuente='14px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=EVAL_PERIODO_PRUEBA_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;EVALUACIÓN PERIODO DE PRUEBA",$Nconsecutivo)."
		&nbsp;
		<H3>I. CONSIDERACIONES AL EVALUADOR</H3>
		<p style='font-size:$Tam_Fuente;text-align:justify;' >La evaluación del período de prueba, es un análisisde los servicios prestados por el empleado en su nueva gestión. Su finalidad es calificar objetivamente eldesarrollo de la labor y la adaptación a la nuevasituación, con base en la observación, los hechos, los resultados y el comportamiento del evaluado en el cargo que este desempeñe.</p>
		<p style='font-size:$Tam_Fuente;text-align:justify;' >Esta evaluación debe realizarse con un alto grado de criterio y objetividad, pues constituye una de las más delicadas responsabilidades del evaluador, desde elpunto de vista de justicia y desarrollo humano, por lo cual es necesario que la calificación haga referencia a la línea de conducta general del empleado y no ha hechos aislados.</p>
		<p style='font-size:$Tam_Fuente;text-align:justify;' >El sistema de calificación empleado tiene tres alternativas, desde sobresaliente hasta no satisfactorio, así:</p>
		<table border cellspacing='0' width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;'> 1  <b> SOBRESALIENTE: </b> Se destaca y es considerado excepcional en este campo&nbsp;
2 <b> SATISFACTORIO: </b> Está de acuerdo con el nivel requerido&nbsp;
3. <b> NO SATISFACTORIO: </b> En  ocasiones alcanza el nivel requerido</td></tr>
		</table>&nbsp;
		<p style='font-size:$Tam_Fuente;text-align:justify;' >Es importante que usted sepa, que si el contrato del funcionario es a un año o superior, la duración del 
período de prueba es de dos meses. Para contratos inferiores a un año, la duración del período de prueba 
será el equivalente a la quinta parte del tiempo. Le recordamos leer detalladamente los factores de evaluación 
antes de responder, si eventualmente no tiene información al respecto, por favor no lo conteste.</p>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$Tam_Fuente='10px';		
	echo "<table width='100%' border cellspacing='0' align='center'><tr><td width='20%' rowspan=3 align='center' valign='middle'>
			<img src='http://app.aoacolombia.com/Administrativo/img/nlogo_aoa_200.jpg' border='0' height='50' width='110'>
		</td><td align='center' rowspan=3 width='50%'><b style='font-size:16px'>FORMATO&nbsp;EVALUACION PERIODO DE PRUEBA</b></td>
		<td width='30%' style='font-size:12px'>CODIGO: $Tipo->sigla</td></tr>
		<tr><td style='font-size:12px'>VERSION: $Tipo->version</td></tr><tr><td style='font-size:10px'>FECHA DE VIGENCIA: $Tipo->vigencia</td></tr>
		</td></tr></table>&nbsp;
		
		<H3>II. EVALUACION</H3>
		<h3>1. EVALUADO:</H3>
		<p style='font-size:$Tam_Fuente;'>Nombre: ___________________________________________  Cargo: ___________________________________________</p>
		<p style='font-size:$Tam_Fuente;'>Dependencia: ________________________________________________________________________________________</p>
		<table border cellspacing='0' width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;'>Fecha de ingreso:    DÍA ___________ / MES: __________________ / AÑO: __________</td></tr>
		</table>&nbsp;
		<h3>2. EVALUADOR:</H3>
		<p style='font-size:$Tam_Fuente;'>Nombre: ___________________________________________  Cargo Actual: _______________________________________</p>
		<table border cellspacing='0' width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;'>Fecha de Evaluación:    DÍA ___________ / MES: __________________ / AÑO: __________</td></tr>
		</table>&nbsp;
		
		<h3>I. FACTORES DE EVALUACIÓN (marque sobre la calificación)</H3>
		<table width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>ASISTENCIA Y PUNTUALIDAD: </b>Cumplimiento de la jornada, asistencia regular al trabajo </td><td> 1 |____|  2 |___|  3 |___| </td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>NORMAS DISCIPLINARIAS: </b>Cumplimiento de instrucciones y órdenes dadas por los superiores. Cumplimiento de normas y  reglamentos internos </td><td> 1 |____|  2 |___|  3 |___| </td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>RELACIONES CON SUS SUPERIORES: </b>Facilidad de interacción con sus superiores.</td><td> 1 |____|  2 |___|  3 |___| </td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>RELACIONES CON SUS COMPAÑEROS: </b>Facilidad de interacción con los nuevos compañeros de trabajo y colaboración conestos.</td><td> 1 |____|  2 |___|  3 |___| </td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>SERVICIO AL ASEGURADO: </b>Calidad y efectividad en la atención al público y los asegurados.</td><td> 1 |____|  2 |___|  3 |___| </td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>CONOCIMIENTO DEL TRABAJO: </b>Asimilación de la información necesaria para desempeñar el cargo, aprovechamientode la experiencia anterior.</td><td> 1 |____|  2 |___|  3 |___| </td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>RESPONSABILIDAD Y DEDICACIÓN AL TRABAJO: </b>Cumplimiento de sus deberes sin necesidad de excesivos controles. Atención oportuna a los asuntos que competen a su cargo.</td><td> 1 |____|  2 |___|  3 |___| </td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>INICIATIVA: </b>Capacidad de plantear, analizar y dar solución a dificultades relacionadas con su trabajo.</td><td> 1 |____|  2 |___|  3 |___| </td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>MOTIVACIÓN: </b>Interés por el trabajo y las actividades desarrolladas por la dependencia.</td><td> 1 |____|  2 |___|  3 |___| </td></tr>
		</table>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		
		
		<table width='100%' border cellspacing='0' align='center'><tr><td width='20%' rowspan=3 align='center' valign='middle'>
			<img src='http://app.aoacolombia.com/Administrativo/img/nlogo_aoa_200.jpg' border='0' height='50' width='110'>
		</td><td align='center' rowspan=3 width='50%'><b style='font-size:16px'>FORMATO&nbsp;EVALUACION PERIODO DE PRUEBA</b></td>
		<td width='30%' style='font-size:12px'>CODIGO: $Tipo->sigla</td></tr>
		<tr><td style='font-size:12px'>VERSION: $Tipo->version</td></tr><tr><td style='font-size:10px'>FECHA DE VIGENCIA: $Tipo->vigencia</td></tr>
		</td></tr></table>&nbsp;
		<table width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>ORGANIZACIÓN DEL TRABAJO: </b>Habilidad para planear, ordenar y distribuir el tiempo y los recursos disponibles, con el fin de realizar mejor sus labores en forma adecuada, en beneficio del trabajo y de los objetivos del área. </td><td> 1 |____|  2 |___|  3 |___| </td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>ADAPTACIÓN AL CARGO: </b>Ajuste a las nuevas situaciones de trabajo, métodos, procedimientos y clima organizacional en general.</td><td> 1 |____|  2 |___|  3 |___| </td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>EFICIENCIA EN EL TRABAJO: </b>Calidad del desempeño y rendimiento alcanzado.</td><td> 1 |____|  2 |___|  3 |___| </td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='70%'><b>GENERAL: </b>De modo general, la calificación que da usted al evaluado durante el periodo de prueba.</td><td> 1 |____|  2 |___|  3 |___| </td></tr>
			
		</table>
		<p style='font-size:$Tam_Fuente;'>Considera usted que el concepto sobre el desempeño del evaluado en el período de prueba es:</p>
		<p style='font-size:16px; text-align:center'><b>FAVORABLE |___|    DESFAVORABLE |___|</b></p>
		<p style='font-size:$Tam_Fuente;'><b>Observaciones:</b></p>
		________________________________________________________________________________&nbsp;
		________________________________________________________________________________&nbsp;
		________________________________________________________________________________&nbsp;
		________________________________________________________________________________&nbsp;&nbsp;
		<p style='font-size:$Tam_Fuente;'><b>Acuerdos y Compromisos:</b></p>
		________________________________________________________________________________&nbsp;
		________________________________________________________________________________&nbsp;
		________________________________________________________________________________&nbsp;
		________________________________________________________________________________&nbsp;&nbsp;&nbsp;&nbsp;
		<p style='font-size:$Tam_Fuente;'><b>Firma del Empleado:</b>_______________________________</p>&nbsp;
		<p style='font-size:$Tam_Fuente;'><b>Firma del Evaluador:</b>_______________________________</p>
	</body></html>";
}

function gendoc_eval_periodo_prueba_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/eval_periodo_prueba.html';
	
}

function gendoc_requisitos_ingreso_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=REQUISITOS_INGRESO_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;REQUISITOS DE INGRESO",$Nconsecutivo)."
		&nbsp;
		<center><i>Bienvenido</i>&nbsp;A NUESTRO EQUIPO DE TRABAJO</center>
		<table border cellspacing='0' width='90%' align='center'>
			<tr>
				<td style='font-size:$Tam_Fuente;' rowspan=2 align='center'><b>Requisitos de Ingreso:</b></td>
				<td style='font-size:$Tam_Fuente;' colspan=2 align='center'><b>Entregado</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente;'  align='center'><b>Si</b></td>
				<td style='font-size:$Tam_Fuente;'  align='center'><b>No</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente;'  align='center' colspan=3><b>PARA TODO EL PERSONAL</b></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;'>Hoja de Vida con Foto</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>4 Fotocopias <b>ampliadas al 150%</b> de la cédula</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>(Hombres) 1 Fotocopias de la Libreta Militar</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>1 Foto fondo azul</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>2 Ultimas referencias laborales</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>Fotocopias de estudios realizados autenticados</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>2 Referencias personales</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>2 Referencias familiares</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>1 Original de Antecedentes Disciplinarios</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>1 Fotocopia certificación E.P.S. o Carnet</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>1 Fotocopia certificación FONDO PENSION</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>Exámen Médico de Ingreso</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>1 Fotocopia del Pase</td><td> </td><td> </td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;'  align='center' colspan=3><b>PARA PERSONAS CASADAS O EN UNION LIBRE</b></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;'>1 Fotocopia Partida de Matrimonio y/o Certificado extrajuicio</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>1 Fotocopia Cédula del Conyugue</td><td> </td><td> </td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;'  align='center' colspan=3><b>PARA QUIENES TIENEN HIJOS</b></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;'>1 Fotocopia del Registro Civil de cada menor de 18 años o Tarjeta de Identidad</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>Original del Certificado de escolaridad de los hijos que se encuentren estudiando</td><td> </td><td> </td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;'  align='center' colspan=3><b>PARA CARGO DE CONDUCTOR</b></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;'>2 Fotocopias del Pase</td><td> </td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>!OJO! No tener partes pendientes</td><td> </td><td> </td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;'  align='center' colspan=3><b>ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.&nbsp;
				Carrera 69B No. 98A-10&nbsp;
				Conmutador 7560510 ext 111</b></td>
			</tr>
			
		</table>
	</body></html>";
}

function gendoc_requisitos_ingreso_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/requisito_ingreso_personal.html';
	
}



function gendoc_aprobacion_anticipo_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=APROBACION_ANTICIPO_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px; font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente; font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;RESPONSABLE DE APROBACIÓN Y MONTO DE ANTICIPOS",$Nconsecutivo)."
		&nbsp;
		&nbsp;
		<table border cellspacing='0' width='90%' align='center'>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Responsable de Aprobación</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Monto de Aprobación</b></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;'>&nbsp;&nbsp;</td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>&nbsp;&nbsp;</td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>&nbsp;&nbsp;</td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>&nbsp;&nbsp;</td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>&nbsp;&nbsp;</td><td> </td></tr>
		
		</table>
	</body></html>";
}

function gendoc_aprobacion_anticipo_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');	
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/aprobacion_anticipo.html';
}


function gendoc_solicitud_anticipo_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=SOLICITUD_ANTICIPO_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;SOLICITUD DE ANTICIPO",$Nconsecutivo)."
		&nbsp;
		&nbsp;
		<table border cellspacing='0' width='100%' align='center'>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center' width='28%' colspan=2>FECHA DEL ANTICIPO:</td><td width='20%'></td>
				<td style='font-size:$Tam_Fuente;' align='center'>CONSECUTIVO CONTABLE</td>
				<td style='font-size:$Tam_Fuente;' align='center'></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='5' align='center'><b>BENEFICIARIO DEL ANTICIPO</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=2>NOMBRE</td><td colspan=3></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=2>DOCUMENTO DE IDENTIDAD:</td><td> </td>
					<td style='font-size:$Tam_Fuente;'>CIUDAD:</td><td width='25%'> </td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='5' align='center'><b>SOLICITUD DE ANTICIPO PARA</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='5'>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='3'>VALOR SOLICITADO:</td><td> </td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='5' align='center'><b>ESPECIFICACIONES DEL ANTICIPO</b></td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'>ITEM</td>
				<td style='font-size:$Tam_Fuente;' align='center'>VALOR</td>
				<td style='font-size:$Tam_Fuente;'  align='center' colspan=3>DESCRIPCION</td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;'  align='center'>1.</td><td></td><td colspan=3></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'  align='center'>2.</td><td></td><td colspan=3></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'  align='center'>3.</td><td></td><td colspan=3></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'  align='center'>4.</td><td></td><td colspan=3></td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;'  align='center' colspan='5'>
					<i>En la Fecha y con el presente documento recibo de ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A A.OA, la suma indicada que será destinada exclusivamente para cubrir los gastos solicitados anteriormente.
					&nbsp;&nbsp;
					Cuando los gastos incurridos y soportados sean inferiores al anticipo recibido reintegrare de inmediato el saldo a favor de la empresa y adjuntare a la relación de gastos el recibo de caja.
					&nbsp;&nbsp;
					En el evento de no presentar oportunamente la relación de la compra, autorizo a ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A. para descontar de mi salario y/o prestaciones sociales el valor total del anticipo que me fue entregado.
					&nbsp;&nbsp;
					Igualmente me comprometo con la empresa a utilizar correctamente el anticipo recibido.
					&nbsp;
					</i>
				</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' colspan='2'>RESPONSABLE DE LA COMPRA:&nbsp;&nbsp;&nbsp;<i>Nombre y Cargo</i></td>
				<td></td>
				<td style='font-size:$Tam_Fuente;' colspan='2'>AUTORIZADO:&nbsp;&nbsp;&nbsp;<i>Nombre y Cargo</i></td>
			</tr>	
		</table>
	</body></html>";
}

function gendoc_solicitud_anticipo_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where id= '".$_POST['docid']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
	
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}			
		
	}	
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');	
	
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/solicitud_anticipo.html';
  }
  
  function formato_solicitud_adquisiciones_ok()
   {
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where id= '".$_POST['docid']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
	
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}			
		
	}	
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');	
	
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/formato_solicitud_adquisiciones.html';
  }

function gendoc_bys_calidad_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=BIENES_Y_SERVICIOS_CALIDAD_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 1px; margin-left: 1px; margin-right: 1px; margin-bottom: 1px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;BIENES Y SERVICIOS QUE AFECTAN LA CALIDAD",$Nconsecutivo)."
		<table border cellspacing='0' width='100%' align='center'>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center' width='4%'><b>Item</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center' width='15%'><b>Proceso</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center' width='8%'><b>Tipo</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center'><b>Descripcion</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center'><b>Frecuencia de Compra</b></td>
			</tr>
		";
	$Bys=q("select q.nombre as nproceso,p.* from provee_produc_serv p,q_proceso q where q.id=p.proceso  order by nproceso,tipo,nombre ");	
	$Contador=0;
	while($b=mysql_fetch_object($Bys))
	{
		$Contador++;
		$Ntipo=($b->tipo=='B'?'BIEN':'SERVICIO');
		echo "<tr>
				<td style='font-size:$Tam_Fuente1;' align='right'>$Contador</td>
				<td style='font-size:$Tam_Fuente1;'>$b->nproceso</td>
				<td style='font-size:$Tam_Fuente1;'>$Ntipo</td>
				<td style='font-size:$Tam_Fuente1;'>$b->nombre</td>
				<td style='font-size:$Tam_Fuente1;'>$b->fecuencia_compra</td>
				</tr>";
	}
		
	echo	"
		</table>
	</body></html>";
}

function gendoc_bys_calidad_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$Nusuario=$_SESSION['Nombre'];
		$Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		//print_r($Formato);
		$consecutivo=qo1("select max(consecutivo) from formatos_aoa where tipo_formato='".$Formato->id."'")+1;
		//echo "soy consecutivo ".$consecutivo;
		$Tipo = $Formato;		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	$insert = "insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')";
	//echo $insert;
	//exit;
	q($insert);
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/afectan_calidad.html';
	
}

function gendoc_legalizacion_anticipo_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=LEGALIZACION_ANTICIPO_$Nconsecutivo.doc");
	$html = "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 1px; margin-left: 1px; margin-right: 1px; margin-bottom: 1px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;LEGALIZACIÓN ANTICIPO",$Nconsecutivo)."
		<table border cellspacing='0' width='100%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' colspan='4'>FECHA DE ENTREGA DEL ANTICIPO</td><td colspan='5'></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='4'>FECHA DE LEGALIZACIÓN DEL ANTICIPO</td><td colspan='5'></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='4'>COMPROBANTE DE EGRESO O TRANSFERENCIA ELECTRONICA NUMERO</td><td colspan='5'></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='9' align='center'><b>RESPONSABLE DE LEGALIZACIÓN</b></td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' >NOMBRE:</td><td colspan=3></td>
				<td style='font-size:$Tam_Fuente;' >CARGO:</td><td colspan=4></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' colspan='2'>DOCUMENTO DE IDENTIDAD:</td><td colspan=2></td>
				<td style='font-size:$Tam_Fuente;' >CIUDAD:</td><td colspan=4></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='9' align='center'><b>DISCRIMINACIÓN DE LA LEGALIZACIÓN</b></td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' colspan='3'>VALOR A LEGALIZAR:</td><td colspan='6'></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='9' align='center'><b>FACTURAS A LEGALIZAR</b></td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center' width='10%'>Fecha</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>Cédula/Nit</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>Nombre del Proveedor</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>Observaciones</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>Placa</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>Centro de<br> Costos</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>Centro de<br> Operaciones</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>Factura de Reintegro</td>
				<td style='font-size:$Tam_Fuente1;' align='center' width='10%'>Valor</td>
			</tr>";
				
				for($i = 0; $i<7; $i++)
				{
					$html .="<tr>
						<td>&nbsp</td>
						<td>&nbsp</td>
						<td>&nbsp</td>
						<td>&nbsp</td>
						<td>&nbsp</td>
						<td>&nbsp</td>
						<td>&nbsp</td>
						<td>&nbsp</td>
						<td>&nbsp</td>
					</tr>";
					
				}

			
			$html .= "<tr><td style='font-size:$Tam_Fuente;' colspan=8>VALOR LEGALIZADO EN FACTURAS</td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=8>VALOR LEGALIZADO POR CONSIGNACION BANCARIA</td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=8>TOTAL LEGALIZADO</td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=9>OBSERVACIONES&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' colspan=4>ELABORADO POR:&nbsp;&nbsp;&nbsp;&nbsp;Nombre y Cargo</td>
				<td style='font-size:$Tam_Fuente;' colspan=5>APROBADO:&nbsp;&nbsp;&nbsp;&nbsp;Nombre y Cargo</td>
			</tr>
			
		</table>
	</body></html>";
	
	echo $html;
}

function gendoc_legalizacion_anticipo_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
	
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/legalizacion_anticipo.html';
	
}

function gendoc_lista_convenios_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=LISTADO_CONVENIOS_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 1px; margin-left: 1px; margin-right: 1px; margin-bottom: 1px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;LISTA DE CONVENIOS",$Nconsecutivo)."
		&nbsp;
		<table border cellspacing='0' width='80%' align='center'>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Aseguradora</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Convenio</b></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;'>RSA</td><td style='font-size:$Tam_Fuente;'>30 días</td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>Liberty</td><td style='font-size:$Tam_Fuente;'>10 días</td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>Allianz</td><td style='font-size:$Tam_Fuente;'>30 días</td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>Mapfre</td><td style='font-size:$Tam_Fuente;'>05-10 de cada mes</td></tr>
			
		</table>
	</body></html>";
}

function gendoc_lista_convenios_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$Nusuario=$_SESSION['Nombre'];
		$Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		//print_r($Formato);
		$consecutivo=qo1("select max(consecutivo) from formatos_aoa where tipo_formato='".$Formato->id."'")+1;
		//echo "soy consecutivo ".$consecutivo;
		$Tipo = $Formato;		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	$insert = "insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')";
	//echo $insert;
	//exit;
	q($insert);
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/lista_convenios.html';

}

function gendoc_definicion_bys_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=DEFINICION_BIENYSERVICIO_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 1px; margin-left: 1px; margin-right: 1px; margin-bottom: 1px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;DEFINICION DE BIENES Y SERVICIOS",$Nconsecutivo)."
		&nbsp;
		<table border cellspacing='0' width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' ><b>* ¿Para quién se está creando valor? Segmentación</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' ><b>* ¿Qué es el producto y/o servicio?</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' ><b>* ¿Qué NO es el producto y/o servicio?</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' ><b>* ¿Cuál es la oferta distintiva?</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' ><b>* ¿Qué acciones críticas se deben realizar para operar de manera exitosa?</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' ><b>* ¿Qué alianzas críticas se deben concretar para que el modelo sea exitoso?</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' ><b>* ¿Qué recursos se necesitan para generar la propuesta de valor, hacerla llegar al cliente, relacionarse con el cliente y generar ingresos?</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' ><b>* ¿Cuáles son los costos más relevantes del modelo?</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' ><b>* ¿Cuánto están dispuestos a pagar por la propuesta de valor?</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' ><b>* ¿Qué requisitos legales deben cumplirse para poder llevar a cabo el producto/servicio?</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			
		</table>
	</body></html>";
}

function gendoc_definicion_bys_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$Nusuario=$_SESSION['Nombre'];
		$Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		//print_r($Formato);
		$consecutivo=qo1("select max(consecutivo) from formatos_aoa where tipo_formato='".$Formato->id."'")+1;
		//echo "soy consecutivo ".$consecutivo;
		$Tipo = $Formato;		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	$insert = "insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')";
	//echo $insert;
	//exit;
	q($insert);
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/definicion_productos_servicios.html';
	
}


function gendoc_informe_auditoria_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=INFORME_AUDITORIA_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 1px; margin-left: 1px; margin-right: 1px; margin-bottom: 1px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;INFORME DE AUDITORIA",$Nconsecutivo)."
		&nbsp;
		<table border cellspacing='0' width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' align='right'>Fecha de la auditoría:</td><td colspan=3></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='right'>Lugar:</td><td colspan=3></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='4'><b>Datos Generales</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Objetivo</td><td colspan='3'></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Alcance</td><td colspan='3'></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Criterios</td><td colspan='3'></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='4'><b>Datos de Identificación</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Tipo de Auditoría</td><td colspan='3'></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Representante en la dirección</td><td colspan='3'></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Participantes del auditado</td><td colspan='3'></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Auditor</td><td colspan='3'></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='4'><b>Resultadosde la auditoría</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='4' align='center'><b>FORTALEZAS</b>&nbsp;(Factores críticos de éxito implementados asertivamente)</td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'>Aspecto evaluado</td>
				<td style='font-size:$Tam_Fuente;' align='center'>Aspecto encontrado</td>
				<td style='font-size:$Tam_Fuente;' align='center'>Contribución actual</td>
				<td style='font-size:$Tam_Fuente;' align='center'>Procesos implicados</td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center'>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center'>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center'>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center'>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='4' align='center'><b>OPORTUNIDADES DE MEJORA</b>&nbsp;(Riesgos que podrían materializarse)</td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'>Aspecto evaluado</td>
				<td style='font-size:$Tam_Fuente;' align='center'>Oportunidad o riesgo encontrado</td>
				<td style='font-size:$Tam_Fuente;' align='center'>Beneficio esperado al implementar la mejora o mitigar el riesgo</td>
				<td style='font-size:$Tam_Fuente;' align='center'>Procesos implicados</td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center'>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center'>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center'>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center'>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='4' align='center'><b>HALLAZGOS</b>&nbsp;(Incumplimientos a los criterios de auditoría)</td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'>Aspecto evaluado</td>
				<td style='font-size:$Tam_Fuente;' align='center' colspan=2>Descripción</td>
				<td style='font-size:$Tam_Fuente;' align='center'>Procesos implicados</td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td colspan='2'></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td colspan='2'></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td colspan='2'></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td colspan='2'></td><td></td></tr>
		</table>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		
		<table width='100%' border cellspacing='0' align='center'><tr><td width='20%' rowspan=3 align='center' valign='middle'>
			<img src='http://app.aoacolombia.com/Administrativo/img/nlogo_aoa_200.jpg' border='0' height='50' width='110'>
		</td><td align='center' rowspan=3 width='50%'><b style='font-size:16px'>FORMATO&nbsp;INFORME DE AUDITORIA</b></td>
		<td width='30%' style='font-size:12px'>CODIGO: $Tipo->sigla</td></tr>
		<tr><td style='font-size:12px'>VERSION: $Tipo->version</td></tr><tr><td style='font-size:10px'>FECHA DE VIGENCIA: $Tipo->vigencia</td></tr>
		</td></tr></table>
		&nbsp;
		<table border cellspacing='0' width='90%' align='center'>
			<tr><td style='font-size:$Tam_Fuente;' colspan='4' align='center'><b>RESULTADOS DE LA REVISIÓN FRENTE A ACCIÓN ANTERIORES</b>&nbsp;(Avance frente a auditorías previas)</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=4>&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=4>&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=4>&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=4>&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='4' align='center'><b>OBSERVACIONES</b>&nbsp;(Registrar temas adicionales)</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=4>&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=4>&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan='4' align='center'><b>CONCLUSIONES DE LA AUDITORIA</b>&nbsp;(Valoración frente a los objetivos y criterios de la auditoría)</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=4>&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' colspan=4 align='center'>&nbsp;&nbsp;Auditor   _____________________________&nbsp;&nbsp;</td></tr>
		</table>
	</body></html>";
}

function gendoc_informe_auditoria_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}	
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');	
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/informe_auditoria.html';
	
}

function gendoc_lista_chequeo_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=LISTA_DE_CHEQUEO_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 1px; margin-left: 1px; margin-right: 1px; margin-bottom: 1px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;LISTA DE CHEQUEO",$Nconsecutivo)."
		&nbsp;
		<table border cellspacing='0' width='40%' >
			<tr><td style='font-size:$Tam_Fuente;' width='50%'><b>Fecha</b></td><td></td></tr>
		</table>
		&nbsp;
		<table border cellspacing='0' width='100%'>
			<tr><td style='font-size:$Tam_Fuente;' width='20%'><b>Auditor Líder</b></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='20%'><b>Equipo Auditor</b></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='20%'><b>Proceso</b></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' width='20%'><b>Auditados</b></td><td></td></tr>
		</table>
		&nbsp;
		<table border cellspacing='0' width='100%'>
			<tr><td style='font-size:$Tam_Fuente;' align='center'><b>Criterios de Auditoría</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
		</table>
		&nbsp;
		<table border cellspacing='0' width='100%'>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center' width='5%'><b>Item</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Pregunta</b></td>
				<td style='font-size:$Tam_Fuente;' align='center' width='2%'><b>C</b></td>
				<td style='font-size:$Tam_Fuente;' align='center' width='2%'><b>NC</b></td>
				<td style='font-size:$Tam_Fuente;' align='center' width='2%'><b>OM</b></td>
				<td style='font-size:$Tam_Fuente;' align='center' width='2%'><b>F</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Observaciones</b></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		</table>
		<p style='font-size:$Tam_Fuente;' >C: Conforme   NC: No Conforme   OM: Oportunidad de Mejora  F: Fortaleza</p>
		&nbsp;&nbsp;&nbsp;<center><b style='font-size:$Tam_Fuente;'>_________________________________________&nbsp;Auditor Líder</b></center>
		
	</body></html>";
}

function gendoc_lista_chequeo_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/lista_chequeo.html';	
	
}


function gendoc_plan_auditoria_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=PLAN_DE_AUDITORIA_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 1px; margin-left: 1px; margin-right: 1px; margin-bottom: 1px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;PLAN DE AUDITORIA",$Nconsecutivo)."
		&nbsp;
		<table border cellspacing='0' width='40%' >
			<tr><td style='font-size:$Tam_Fuente;' width='50%'><b>Fecha Plan</b></td><td></td></tr>
		</table>
		&nbsp;
		<table border cellspacing='0' width='100%'>
			<tr><td style='font-size:$Tam_Fuente;' align='center' colspan='2'><b>Presentación del Proceso Auditado</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center' width='50%'><b>Nombre del Proceso</b></td><td style='font-size:$Tam_Fuente;' align='center'><b>Líder del Proceso</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td></tr>
		</table>
		&nbsp;
		<table border cellspacing='0' width='100%'>
			<tr><td style='font-size:$Tam_Fuente;' align='center' colspan='2'><b>Presentación del Equipo Auditor</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center' width='50%'><b>Nombre</b></td><td style='font-size:$Tam_Fuente;' align='center'><b>Líder o Auxiliar</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;</td><td></td></tr>
		</table>
		&nbsp;
		<table border cellspacing='0' width='100%'>
			<tr><td style='font-size:$Tam_Fuente;' align='center' colspan='2'><b>Objetivo, Alcance y Criterios de Auditoría</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center' width='20%'><b>Objetivo</b></td><td>&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center' width='20%'><b>Alcance</b></td><td>&nbsp;&nbsp;</td></tr>
			<tr><td style='font-size:$Tam_Fuente;' align='center' width='20%'><b>Criterios de Auditoría</b></td><td>&nbsp;&nbsp;</td></tr>
		</table>
		&nbsp;
		<table border cellspacing='0' width='100%'>
			<tr><td style='font-size:$Tam_Fuente;' align='center'><b>Recursos solicitados por el equipo auditor para llevar a cabo el trabajo</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
		</table>
		&nbsp;
		<table border cellspacing='0' width='100%'>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Auditado</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Fecha</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Hora</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Lugar</b></td>
			</tr>
			<tr><td style='font-size:$Tam_Fuente;'>&nbsp;</td><td></td><td></td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;'>&nbsp;</td><td></td><td></td><td></td></tr>
		</table>
		
		&nbsp;
		<table border cellspacing='0' width='100%'>
			<tr><td style='font-size:$Tam_Fuente;' align='center'><b>Observaciones</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
		</table>
		&nbsp;
		<table border cellspacing='0' width='100%'>
			<tr><td style='font-size:$Tam_Fuente;' align='center' width='50%'><b>Firma del Auditor Líder</b></td><td style='font-size:$Tam_Fuente;' align='center'><b>Firma del Responsable del Proceso</b></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >&nbsp;&nbsp;&nbsp;</td><td></td></tr>
			<tr><td style='font-size:$Tam_Fuente;' >Nombre:</td><td  style='font-size:$Tam_Fuente;' >Nombre:</td></tr>
		</table>
	</body></html>";
}

function gendoc_plan_auditoria_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
	
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");		
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}	
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/plan_auditoria.html';	
	
}

function gendoc_programacion_auditoria_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='8px';;
	$Tam_Fuente1='6px';
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=PROGRAMACION_DE_AUDITORIA_$Nconsecutivo.doc");
	
	$mes="<td></td><td></td><td></td><td></td>";
	$meses=$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes;
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 1px; margin-left: 1px; margin-right: 1px; margin-bottom: 1px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;PROGRAMACION DE AUDITORIA",$Nconsecutivo)."
		&nbsp;
		<table border cellspacing='0' width='100%' >
			<tr>
				<td style='font-size:$Tam_Fuente;' width='10%' rowspan=4 align='center'><b>Proceso a Auditar</b></td>
				<td style='font-size:$Tam_Fuente;' width='10%' rowspan=2 colspan=2 align='center'><b>Responsables</b></td>
				<td style='font-size:$Tam_Fuente;' width='80%' colspan=48 align='center'><b>Año: ________</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Enero</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Febrero</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Marzo</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Abril</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Mayo</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Junio</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Julio</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Agosto</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Septiembre</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Octubre</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Noviembre</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Diciembre</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' rowspan=2 align='center'><b>Auditor Líder</b></td>
				<td style='font-size:$Tam_Fuente1;' rowspan=2 align='center'><b>Auditor Acompañante</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
			<tr><td>&nbsp;</td><td></td><td></td>$meses</tr>
		</table>
	</body></html>";
}

function gendoc_programacion_auditoria_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");		
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	//$insert = "insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
	//	('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')";
	//echo $insert;
	//exit;
	//q($insert);
	$Tam_Fuente='12px';;
	$Tam_Fuente1='10px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/programacion_auditoria.html';	
}




function gendoc_acciones_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=ACCIONES_$Nconsecutivo.doc");
	$meses=$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes;
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 1px; margin-left: 1px; margin-right: 1px; margin-bottom: 1px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;ACCIONES",$Nconsecutivo)."
		&nbsp;
		<table border cellspacing='0' width='100%' >
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'><b>APERTURA</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>CIERRE</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' width='60%'>Fecha de apertura de la acción: |_______|___|___|   Acción No. |________|
				&nbsp;&nbsp;&nbsp;<center>RESPONSABLE</center>
				Proceso: |________________________| Líder de Proceso: |_______________________|&nbsp;&nbsp;
				<b>Tipo de Acción</b>&nbsp;
				<center> Correctiva |__|   Preventiva |__|  Mejora |__|</center>&nbsp;</br>
				<b>Fuente</b>&nbsp;
				Servicio |__| Cliente |__| Proceso |__| Auditoría |__| Riesgo |__|  Otro ¿Cuál? |___________|
				</td>
				<td style='font-size:$Tam_Fuente;'><center>
				Fecha Programada de cierre: |_____|___|___|&nbsp;
				Fecha real de cierre: . . . . . . . |_____|___|___|</center>&nbsp;
				<center><b>Acción Correctiva o Preventiva</b>&nbsp;Hallazgos que soportan la eficacia de la acción</center>
				_____________________________________________&nbsp;
				_____________________________________________&nbsp;
				_____________________________________________&nbsp;&nbsp;
				<center><b>Acción de mejora</b>&nbsp;¿Qué beneficios lograron con la implementación?</center>
				_____________________________________________&nbsp;
				_____________________________________________&nbsp;
				_____________________________________________&nbsp;
				_____________________________________________&nbsp;
				</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'><b>DESCRIPCIÓN DE LA NO CONFORMIDAD (Acción Correctivao Preventiva)&nbsp;
				DESCRIPCION DEL OBJETIVO DE LA MEJORA (Acción de Mejora)</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>AUTORIZAN EL CIERRE:</b></td>
			</tr>
			<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td style='font-size:$Tam_Fuente1;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<center>____________________________ _________________________&nbsp;
			             Nombre:_____________________ Nombre: __________________&nbsp;
						 Cargo: ______________________ Cargo: ____________________&nbsp;
						 Proceso: _____________________ Proceso: __________________
			</center></td>
			</tr>
		</table>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		".cabecera_formato($Tipo,"FORMATO&nbsp;ACCIONES",$Nconsecutivo)."		
		<table border cellspacing='0' width='100%' >
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'><b>APERTURA</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>CIERRE</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' width='60%'>Fecha de apertura de la acción: |_______|___|___|   Acción No. |________|</td>
				<td style='font-size:$Tam_Fuente;'><center>Fecha Programada de cierre: |_____|___|___|&nbsp;Fecha real de cierre: . . . . . . . |_____|___|___|</center>&nbsp;</td>
			</tr>
		</table>
		
		<table border cellspacing='0' width='100%' >
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center' colspan=2><b>ANALISIS DE CAUSAS O JUSTIFICACIÓN</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center' width='15%'><b>Elemento</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center'><b>Causas (si es Acción Correctiva o Preventiva) o justificación (si es Acción de Mejora)</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center'><b>Mano de Obra</b>&nbsp;(Causas o justificación&nbsp;derivada del talento&nbsp;humano)</td>
				<td style='font-size:$Tam_Fuente1;' >Por que?&nbsp;&nbsp;Por que?&nbsp;&nbsp;Por que?</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center'><b>Materia Prima</b>&nbsp;(Causas o justificación&nbsp;derivada de la&nbsp;información o insumos&nbsp;que se requieren)</td>
				<td style='font-size:$Tam_Fuente1;' >Por que?&nbsp;&nbsp;Por que?&nbsp;&nbsp;Por que?</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center'><b>Maquinaria</b>&nbsp;(Causas o justificación&nbsp;derivada de la&nbsp;infraestructura o&nbsp;tecnología)</td>
				<td style='font-size:$Tam_Fuente1;' >Por que?&nbsp;&nbsp;Por que?&nbsp;&nbsp;Por que?</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center'><b>Método</b>&nbsp;(Causas o justificación&nbsp;derivada de la forma&nbsp;de realizar las&nbsp;actividades)</td>
				<td style='font-size:$Tam_Fuente1;' >Por que?&nbsp;&nbsp;Por que?&nbsp;&nbsp;Por que?</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center'><b>Medición</b>&nbsp;(Causas o justificación&nbsp;derivada de los medios&nbsp;de control aplicados)</td>
				<td style='font-size:$Tam_Fuente1;' >Por que?&nbsp;&nbsp;Por que?&nbsp;&nbsp;Por que?</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center'><b>Medio Ambiente</b>&nbsp;(Causas o justificación&nbsp;derivadas del entorno)</td>
				<td style='font-size:$Tam_Fuente1;' >Por que?&nbsp;&nbsp;Por que?&nbsp;&nbsp;Por que?</td>
			</tr>
		</table>
		
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		".cabecera_formato($Tipo,"FOMRATO&nbsp;ACCIONES",$Nconsecutivo)."
		&nbsp;
		<table border cellspacing='0' width='100%' >
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'><b>APERTURA</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>CIERRE</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' width='60%'>Fecha de apertura de la acción: |_______|___|___|   Acción No. |________|</td>
				<td style='font-size:$Tam_Fuente;'><center>Fecha Programada de cierre: |_____|___|___|&nbsp;Fecha real de cierre: . . . . . . . |_____|___|___|</center>&nbsp;</td>
			</tr>
		</table>
		<table border cellspacing='0' width='100%' >
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center' colspan=5><b>PLAN DE ACCIÓN&nbsp;(Actividades a realizar para eliminar las causas delas no conformidades o implementar la mejora)</b></td>
				<td style='font-size:$Tam_Fuente;' align='center' colspan=3><b>SEGUIMIENTO&nbsp;(Diligenciar el avance que se tiene en la actividaddel plan de acción - pueden realizarse el número de seguimientos que se considere)</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' colspan=2 rowspan=2 align='center'><b>Actividad</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center' rowspan=2><b>Responsable</b>&nbsp;Funcionarios de planta: Cargo&nbsp;Copntratistas: Nombre y apellido</td>
				<td style='font-size:$Tam_Fuente1;' align='center' rowspan=2><b>Fecha de&nbsp;Inicio&nbsp;Planteada</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center' rowspan=2><b>Fecha de&nbsp;Fin&nbsp;Planteada</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=2><b>Descripción del avance del plan de acción</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center' rowspan=2><b>Evidencia/&nbsp;ubicación</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center' ><b>Fecha de&nbsp;seguimiento</b></td>
				<td style='font-size:$Tam_Fuente1;' align='center' ><b>Avance</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' width='1%' rowspan=3>1</td>
				<td width='20%' rowspan=3></td><td rowspan=3></td><td rowspan=3></td><td rowspan=3>
				</td><td>&nbsp;</td><td></td><td></td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>

			<tr>
				<td style='font-size:$Tam_Fuente1;' width='1%' rowspan=3>2</td>
				<td width='20%' rowspan=3></td><td rowspan=3></td><td rowspan=3></td><td rowspan=3>
				</td><td>&nbsp;</td><td></td><td></td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>

			<tr>
				<td style='font-size:$Tam_Fuente1;' width='1%' rowspan=3>3</td>
				<td width='20%' rowspan=3></td><td rowspan=3></td><td rowspan=3></td><td rowspan=3>
				</td><td>&nbsp;</td><td></td><td></td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>

			<tr>
				<td style='font-size:$Tam_Fuente1;' width='1%' rowspan=3>4</td>
				<td width='20%' rowspan=3></td><td rowspan=3></td><td rowspan=3></td><td rowspan=3>
				</td><td>&nbsp;</td><td></td><td></td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>

			<tr>
				<td style='font-size:$Tam_Fuente1;' width='1%' rowspan=3>5</td>
				<td width='20%' rowspan=3></td><td rowspan=3></td><td rowspan=3></td><td rowspan=3>
				</td><td>&nbsp;</td><td></td><td></td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>

			</table>&nbsp;
		<table border cellspacing='0' width='50%' >
			<tr><td style='font-size:$Tam_Fuente;' align='right'>Fecha último Seguimiento</td><td width='50%'></td>
		</table>
	</body></html>";
}

function gendoc_acciones_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
	
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");		
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='10px';;
	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/formato_acciones.html';

	
}

function gendoc_consolidado_acciones_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=CONSOLIDADO_DE_ACCIONES_$Nconsecutivo.doc");
	
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 1px; margin-left: 1px; margin-right: 1px; margin-bottom: 1px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;CONSOLIDADO DE ACCIONES",$Nconsecutivo)."
		&nbsp;
		<table border cellspacing='0' width='100%' >
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center' rowspan=2><b>Proceso</b></td>
				<td style='font-size:$Tam_Fuente;' align='center' rowspan=2><b>No de Acción</b></td>
				<td style='font-size:$Tam_Fuente;' align='center' rowspan=2><b>Abierta</b></td>
				<td style='font-size:$Tam_Fuente;' align='center' rowspan=2><b>Cerrada</b></td>
				<td style='font-size:$Tam_Fuente;' align='center' rowspan=2><b>Tema</b></td>
				<td style='font-size:$Tam_Fuente;' align='center' rowspan=2><b>Fuente</b></td>
				<td style='font-size:$Tam_Fuente;' align='center' colspan=2><b>Eficaz</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente;' align='center'><b>Si</b></td>
				<td style='font-size:$Tam_Fuente;' align='center'><b>No</b></td>
			</tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		</table>
	</body></html>";
}

function gendoc_consolidado_acciones_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
		//echo "consecutivo final ".$consecutivo;
		//exit;
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	//$insert = "insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
	//	('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')";
	//echo $insert;
	//exit;
	//q($insert);
	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/consolidado_acciones.html';
}



function gendoc_programa_mantenimiento_preventivo_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='8px';;
	$Tam_Fuente1='6px';
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=PROGRAMA_MANTENIMIENTO_PREVENTIVO_$Nconsecutivo.doc");
	
	$mes="<td></td><td></td><td></td><td></td>";
	$meses=$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes.$mes;
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 1px; margin-left: 1px; margin-right: 1px; margin-bottom: 1px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;PROGRAMA DE MANTENIMIENTO PREVENTIVO",$Nconsecutivo)."
		&nbsp;
		<table border cellspacing='0' width='100%' >
			<tr>
				<td style='font-size:$Tam_Fuente;' width='15%' rowspan=4 align='center'><b>Infraestructura</b></td>
				<td style='font-size:$Tam_Fuente;' colspan=48 align='center'><b>Año: ________</b></td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Enero</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Febrero</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Marzo</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Abril</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Mayo</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Junio</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Julio</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Agosto</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Septiembre</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Octubre</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Noviembre</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Diciembre</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
				<td style='font-size:$Tam_Fuente1;' align='center' colspan=4>Semanas</td>
			</tr>
			<tr>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
				<td style='font-size:$Tam_Fuente1;' align='center'>1</td><td style='font-size:$Tam_Fuente1;' align='center'>2</td><td style='font-size:$Tam_Fuente1;' align='center'>3</td><td style='font-size:$Tam_Fuente1;' align='center'>4</td>
			</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
			<tr><td>&nbsp;</td>$meses</tr>
		</table>
	</body></html>";
}


function gendoc_programa_mantenimiento_preventivo_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='8px';;
	$Tam_Fuente1='6px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/programa_mantenimiento_preventivo.html';	
}


function gendoc_comunicacion_externa_ok()
{
	global $para,$cargo_para,$empresa,$direccion,$de,$asunto,$contenido,$firma,$cargo,$consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT).'-'.date('y');
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');$Fecha_completa=fecha_completa($Hoy);
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','$para','$para','','','$asunto')");
	$Tam_Fuente='12px';
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=COMUNICACION_EXTERNA_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 30px; margin-right: 30px; margin-bottom: 30px;font-family:arial;font-size:$Tam_Fuente;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>
		<p align='left' style='font-size:$Tam_Fuente'>$Tipo->sigla-$Nconsecutivo</p>
		<br>
		<p style='font-size:$Tam_Fuente'>Bogotá D.C. $Fecha_completa</p>&nbsp;
		<br>
		
		Señor (a):&nbsp;
		<br><br>
		<b>$para&nbsp</b>
		<br>
		<b>$cargo_para</b>&nbsp;
		<br>
		$empresa&nbsp;
		<br>
		$direccion</b>&nbsp;&nbsp;
		<br>Ciudad&nbsp;&nbsp;
		<br>
		<br>
		<br>
		<br>
		<br>
		Asunto: $asunto&nbsp;&nbsp;&nbsp;&nbsp;<br><br>";
	$Partes_contenido=explode(chr(13),$contenido);
	for($i=0;$i<count($Partes_contenido);$i++) 
		echo "<p align='justify' style='font-size:$Tam_Fuente;font-family:arial;'>".$Partes_contenido[$i]."</p>";
	echo "&nbsp;<p style='font-size:$Tam_Fuente;font-family:arial;'>Cordialmente</p><br><br>&nbsp;&nbsp;
			<p style='font-size:$Tam_Fuente;font-family:arial;'><b>$firma</b>&nbsp;<br><b>$cargo</b>&nbsp;<br>&nbsp;
			Anexo: ";
	echo"</body></html>";
}

function gendoc_comunicacion_externa_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;

	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}	
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT).'-'.date('y');
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');$Fecha_completa=fecha_completa($Hoy);

	/*q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','$para','$para','','','$asunto')");*/
	//$Tam_Fuente='12px';
	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/comunicacion_externa2.html';	
}





function gendoc_actaentrega_equipos_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=ACTA DE ENTREGA DE EQUIPOS_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
		<body>".cabecera_formato($Tipo,"FORMATO&nbsp;ACTA DE ENTREGA DE EQUIPOS",$Nconsecutivo)."
		<table border cellspacing='0' width='100%' align='left'>
			<tr>
				<td style='font-size:$Tam_Fuente;'  align='left' colspan='6'>
					En la ciudad de _________________, siendo las __________ del día __________ (__) del mes de ___________, del año Dos Mil
					diecisiete (17), se reunieron en las instalaciones de A.O.A S.A.S, los Señores ______________________ y 
					______________________identificado como aparecen al pie de sus firmas, con el fin de hacer entrega de los 
					siguientes equipos de oficina:
				</td>
			</tr>
            <tr style='font-size:$Tam_Fuente;'><td colspan='6'><STRONG>Tipo de Equipo:</STRONG></td></tr>
			<tr style='font-size:$Tam_Fuente;'><td><STRONG>MARCA:</STRONG></td><td>______________</td><td><STRONG>MODELO:</STRONG></td><td>______________</td><td><STRONG>SERIAL:</STRONG></td><td>______________</td></tr>
			<tr style='font-size:$Tam_Fuente;'><td colspan='6'><STRONG>Detalle del Equipo:(especificar Marca, Modelo, Seriales y Accesorios)</STRONG></td></tr>
			<tr style='font-size:$Tam_Fuente;'><td colspan='6'>__________________________________________________________________________________________</td></tr>
			<tr style='font-size:$Tam_Fuente;'><td colspan='6'>__________________________________________________________________________________________</td></tr>
			<tr style='font-size:$Tam_Fuente;'><td colspan='6'>__________________________________________________________________________________________</td></tr>
			<tr style='font-size:$Tam_Fuente;'><td colspan='6'>__________________________________________________________________________________________</td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;'  align='left' colspan='6'>
                Así mismo, el (la) Señor(a),___________________________ identificado (a) con la cédula de ciudadanía 
				No.____________, expedida en la ciudad de ___________ en su calidad de ___________________ manifiesta: 
				<strong>PRIMERO: </strong>Que recibe el equipo para cumplir las funciones de ___________________, en la ciudad 
				de ___________ en perfecto estado y se compromete a utilizarlo en debida forma y a mantenerlo en excelentes condiciones. <strong>SEGUNDO:</strong> El equipo es de uso exclusivo de ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S, 
				motivo por el cual, sólo podrá utilizarse para desempeñar sus funciones. <strong>TERCERO:</strong> El trabajador se hace responsable por el deterioro que presente el equipo, salvo cuando el deterioro que presente se deba al uso normal del bien.
                <strong>CUARTO:</strong> En caso de deterioro (salvo el del uso normal del bien) o perdida ocasionados directa o indirectamente por el trabajador, hecho debidamente comprobado mediante la aplicación rigurosa del régimen disciplinario vigente en 
				ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S, el trabajador se compromete a pagar el importe total de los daños. 
				<strong>QUINTO:</strong> El equipo de recibido por el trabajador, podrá ser revisado en cualquier momento por la persona que designe
				ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S,<strong>SEXTO:</strong> El trabajador será plenamente responsable laboral, civil y penalmente
				por los daños que ocasione a ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S, o a terceros por el uso indebido del equipo anteriormente descrito. 
				<strong>SÉPTIMO:</strong> Así mismo desde ya autorizo a ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S, para descontar de los salarios y/o liquidación 
				final de prestaciones sociales, el valor del equipo en caso de pérdida o daño imputable al empleado, adicionalmente, autorizo a ADMINISTRACIÓN 
				OPERATIVA AUTOMOTRIZ S.A.S, descontar de los salarios y/o prestaciones el valor resultante del exceso en el cargo básico de consumo mensual.
				<strong>OCTAVO:</strong> Adicionalmente, el trabajador firma al pie de su nombre comprometiéndose a mantener en las condiciones relacionadas en 
				este documento y a responder como lo establece el numeral tercero y cuarto, los implementos que recibió el día ____________ (__) del mes de ____________ 
				del año Dos Mil (2017) y que constan en la presente Acta.
				&nbsp;<strong>Observaciones adicionales:</strong>
				&nbsp;Leído por los intervinientes, quienes están de acuerdo con lo descrito en la presente acta y para constancia y aprobación se firma un original, 
				y su respectiva copia los cuales serán archivados así:
				&nbsp;<strong>Original 1:</strong> 	Carpeta inventario de activos fijos – equipos de comunicación
				&nbsp;<strong>Original 2:</strong> 	Hoja de vida trabajador – Departamento de Gestión Humana - Bogotá
				</td>
			</tr>
			<td style='font-size:$Tam_Fuente;' colspan='2'>QUIEN ENTREGA:
			&nbsp;_________________________
			<br>
			&nbsp;Nombre:
			<br>
			&nbsp;Cargo:
			<br>
			&nbsp;C.C:</td>
			<td></td>
			<td style='font-size:$Tam_Fuente;' colspan='3'>QUIEN RECIBRE:
			&nbsp;_________________________
			<br>
			&nbsp;Nombre:
			<br>&nbsp;Cargo:
			<br>&nbsp;C.C:</td>
			</tr>	
		</table>
	</body></html>";
}

function gendoc_actaentrega_equipos_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/acta_entrega_equipo.html';
	
}

function gendoc_actaentrega_equipos_ok_3(){
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/acta_entrega_equipo_2.html';
	
}

function gendoc_solicitud_vacaciones_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=FORMATO SOLICITUD DE VACACIONES_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
			body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		</style>
<body>
".cabecera_formato($Tipo,"FORMATO SOLICITUD DE VACACIONES ",$Nconsecutivo)."
<table border cellspacing='0' width='98%' align='left'>
	<tr>
				<td style='font-size:$Tam_Fuente;'  align='left' colspan='5'><p><strong>Se&ntilde;ores</strong>&nbsp;
                      <strong>ADMINISTRACION OPERATIVA  AUTOMOTRIZ S.A.S</strong>&nbsp;
                      <strong>___________________________ </strong>(Jefe Inmediato y/o Direcci&oacute;n  de Gesti&oacute;n Humana).<strong></strong>&nbsp;
                      <strong>Ciudad</strong> </p></td>
  </tr>
            <tr style='font-size:$Tam_Fuente;'><td colspan='5'>&nbsp;</td>
            </tr>
			<tr style='font-size:$Tam_Fuente;'>
			  <td colspan='5'><p>Ref:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Solicitud vacaciones</strong></p></td>
  </tr>
			<tr style='font-size:$Tam_Fuente;'><td colspan='5'>&nbsp;</td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;'  align='left' colspan='5'><p>Por  medio de la presente me permito solicitar mi periodo de vacaciones comprendido entre  el ________________________ de 201__ al_______________________ de 201__, (&nbsp;&nbsp;&nbsp; ) d&iacute;as &nbsp;los cuales deseo disfrutar a parir del&nbsp;&nbsp; __________________ de&nbsp; 201___.</p>
				  <p>&nbsp;</p>
				  <p>Agradezco  su amable atenci&oacute;n.</p>
				  <p>&nbsp;</p>
				  <p>&nbsp;</p>
				  <p>Cordialmente,</p>
				  <p>&nbsp;</p>
				  <p>___________________________________&nbsp;
			    C.C. </p></td>
			</tr>
</table>
</body></html>";
}

function gendoc_solicitud_vacaciones_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/solicitud_vacaciones.html';
	
}


function lista_chequeo_venta_vehiculo_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=LISTA DE CHEQUEO VENTA VEHICULO_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 5px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
}
        .Estilo4 {font-size: 10px; font-weight: bold; }
        .Estilo7 {font-size: 12px; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
		.Estilo12 {font-size: 8}
        .Estilo13 {font-size: 10px; color: #666666; 
		
		</style>
<body>
".cabecera_formato($Tipo,"LISTA DE CHEQUEO VENTA VEHICULO",$Nconsecutivo)."
	<table border cellspacing='0' width='100%' align='left'>
		<tr>
			<td><span class='Estilo4' width='28%' >PLACA</span></td>
		</tr>
		<tr>
			<td width='28%' >&nbsp;</td>
		</tr>
		<tr>
			<th scope='col' width='28%'><span class='Estilo4'>DEPENDENCIA</span></th>
			<th scope='col' width='37%'><span class='Estilo4'>NOMBRE/COMENTARIOS</span></th>
			<th scope='col' width='35%'><span class='Estilo4'>FIRMA</span></th>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC' width='28%' ><span class='Estilo4'>GERENCIA GENERAL</span></td>
			<td bgcolor='#CCCCCC'>&nbsp;</td>
			<td bgcolor='#CCCCCC'>&nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>VISTO BUENA VENTA ACTIVO</td>
			<td class='Estilo1'>&nbsp;</td>
			<td class='Estilo12'>Fecha &nbsp;</td>
		</tr>
		<tr>
			<td  class='Estilo1' height='45'>OBSERVACIONES</td>
			<td  colspan='2' height='45'>&nbsp;</td>
			
		</tr>
		<tr>
			<td bgcolor='#CCCCCC'><span class='Estilo4'>DIRECCIÓN OPERACIONES</span></td>
			<td bgcolor='#CCCCCC'>&nbsp;</td>
			<td bgcolor='#CCCCCC'>&nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>VISTO BUENO DIR.OPERACIONES</td>
			<td class='Estilo1'>&nbsp;</td>
			<td class='Estilo1'>Fecha &nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1' height='45'>OBSERVACIONES</td>
			<td  colspan='2' height='45'>&nbsp;</td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC' width='28%' ><span class='Estilo4'>FACTURACION Y CARTERA</span></td>
			<td bgcolor='#CCCCCC'>&nbsp;</td>
			<td bgcolor='#CCCCCC'>&nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>No. FACTURA DE VENTA</td>
			<td class='Estilo1'>&nbsp;</td>
			<td class='Estilo12'>Fecha &nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>No. FACTURA DE TRÁMITES</td>
			<td class='Estilo1'>&nbsp;</td>
			<td class='Estilo12'>Fecha &nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>RECIBO DE CAJA No.</td>
			<td class='Estilo1'>&nbsp;</td>
			<td class='Estilo12'>Fecha &nbsp;</td>
		</tr>
		<tr>
			<td  class='Estilo1' height='45'>OBSERVACIONES</td>
			<td  colspan='2' height='45'>&nbsp;</td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC' width='28%' ><span class='Estilo4'>TESORERÍA</span></td>
			<td bgcolor='#CCCCCC'>&nbsp;</td>
			<td bgcolor='#CCCCCC'>&nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>VB. TESORERÍA</td>
			<td class='Estilo1'>&nbsp;</td>
			<td class='Estilo12'>Fecha &nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>BANCO</td>
			<td class='Estilo1'>&nbsp;</td>
			<td class='Estilo12'>Fecha ingreso &nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>VALOR</td>
			<td class='Estilo1' colspan ='2'>&nbsp;</td>			
		</tr>
		<tr>
			<td  class='Estilo1' height='45'>OBSERVACIONES</td>
			<td  colspan='2' height='45'>&nbsp;</td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC' width='28%' ><span class='Estilo4'>DIRECCIÓN ADMINISTRATIVA</span></td>
			<td bgcolor='#CCCCCC'>&nbsp;</td>
			<td bgcolor='#CCCCCC'>&nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>LEV. DE PRENDA</td>
			<td class='Estilo1'>&nbsp;</td>
			<td class='Estilo12'>Fecha &nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>PAGO SALDO LEASING</td>
			<td class='Estilo1'>&nbsp;</td>
			<td class='Estilo12'>Fecha &nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>VALIDACION SINIESTRO</td>
			<td class='Estilo1'>&nbsp;</td>
			<td class='Estilo12'>Fecha &nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>EXCLUSIÓN PÓLIZA COLECTIVA</td>
			<td class='Estilo12'>Aseguradora &nbsp;</td>
			<td class='Estilo12'>Fecha &nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>PLATAFORMA DE CONTROL</td>
			<td class='Estilo12'>Cambio a nueva ublicación 'venta flota' &nbsp;</td>
			<td class='Estilo12'>Fecha &nbsp;</td>
		</tr>
		<tr>
			<td class='Estilo1'>PLATAFORMA DE CONTROL</td>
			<td class='Estilo12'>Inactividad del vehículo &nbsp;</td>
			<td class='Estilo12'>Fecha &nbsp;</td>
		</tr>
		<tr>
			<td  class='Estilo1' height='65'>OBSERVACIONES</td>
			<td  colspan='2' height='65'>&nbsp;</td>
		</tr>
		<tr>
			<td  class='Estilo1' height='65'>CONDICIONES VENTA DE FLOTA</td>
			<td  colspan='2' height='65'>&nbsp;</td>
		</tr>
	</table>
 </body></html>";
}

function lista_chequeo_venta_vehiculo_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}	
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	//header("Content-type: application/vnd.ms-word");
	//header("Content-Disposition: attachment;Filename=LISTA DE CHEQUEO VENTA VEHICULO_$Nconsecutivo.doc");
	$resultado = q("select Distinct(placa) as dplaca from aoacol_aoacars.vehiculo");
	$placas = array();
   	while ($placa = mysql_fetch_object($resultado)) {
		array_push($placas, $placa);
	}
	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/lista_chequeo_venta_vehiculo.html';

}

function acta_entrega_venta_vehiculo_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;

	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	

		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}	
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$resultado = q("SELECT * FROM aoacol_administra.oficina");
	$oficinas = array();
   	while ($oficina = mysql_fetch_object($resultado)) {
		array_push($oficinas, $oficina);
	}
	$resultado = q("select Distinct(placa) as dplaca from aoacol_aoacars.vehiculo");
	$placas = array();
   	while ($placa = mysql_fetch_object($resultado)) {
		array_push($placas, $placa);
	}
	$resultado = q("select * from aoacol_aoacars.aseguradora");
	$aseguradoras = array();
   	while ($aseguradora = mysql_fetch_object($resultado)) {
		array_push($aseguradoras, $aseguradora);
	}
	$Tam_Fuente='10px';
	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/acta_entrega_venta_vehiculo.html';
}


function gendoc_formato_inscripciin_clientes()
{
	global $consecutivo,$tipodoc,$Nusuario;
   
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	

		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}	
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$resultado = q("SELECT * FROM aoacol_administra.oficina");
	$oficinas = array();
   	while ($oficina = mysql_fetch_object($resultado)) {
		array_push($oficinas, $oficina);
	}
	$resultado = q("select Distinct(placa) as dplaca from aoacol_aoacars.vehiculo");
	$placas = array();
   	while ($placa = mysql_fetch_object($resultado)) {
		array_push($placas, $placa);
	}
	$resultado = q("select * from aoacol_aoacars.aseguradora");
	$aseguradoras = array();
   	while ($aseguradora = mysql_fetch_object($resultado)) {
		array_push($aseguradoras, $aseguradora);
	}
	$Tam_Fuente='10px';
	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/formato_inscripciin_clientes.html';
}




function acta_entrega_venta_vehiculo_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=ACTA ENTREGA VENTA VEHICULO_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 5px; margin-left: 5px; margin-right: 5px; margin-bottom: 10px;font-family:arial;}
			td {margin-top: 2px; margin-left: 2px; margin-right: 2px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
}
        .Estilo4 {font-size: 8px; font-weight: bold; }
        .Estilo7 {font-size: 12px; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
		.Estilo12 {font-size: 8}
        .Estilo13 {font-size: 10 ; color: #666666;
		
		
		</style>
<body>
".cabecera_formato($Tipo,"ACTA ENTREGA VENTA VEHICULO",$Nconsecutivo)."
	<table border cellspacing='0' width='110%' align='left'>
		<tr>
			<td class='Estilo12' width='10%' >FECHA DE ENTREGA</td>
			<td bgcolor='#000000' width='5%'><span class='Estilo12' bgcolor='#FFFFFF' >DD</span></td>
			<td width='10%'></td>
			<td bgcolor='#000000' width='5%'><span class='Estilo12' bgcolor='#FFFFFF' >MM</span></td>
			<td width='10%'></td>
			<td bgcolor='#000000' width='5%'><span class='Estilo12' bgcolor='#FFFFFF' >AA</span></td>
			<td width='10%'></td>			
			<td class='Estilo12' width='10%'>CIUDAD</td>
			<td width='35%' colspan='2'></td>
		</tr>
		<tr>
			<td class='Estilo12' width='10%' >ENTREGADO A</td>
			<td class='Estilo12' width='10%' >COMPRADOR FINAL</td>
			<td width='5%'></td>
			<td class='Estilo12' width='10%' >CANAL DE VENTA</td>
			<td width='5%'></td>
			<td colspan='5'></td>
		</tr>
		<tr>
			<td class='Estilo12' width='10%' >CANAL DE VENTA</td>
			<td colspan='9'></td>
		</tr>
		<tr>
			<th bgcolor='#CCCCCC' colspan='10'><span class='Estilo4'>DATOS DEL COMPRADOR</span></th>
		</tr>
		<tr>
			<td class='Estilo12' width='10%' >PLACA</td>
			<td colspan='4'></td>
			<th class='Estilo12' width='10%'>CONVENCION</th>
			<th class='Estilo12' colspan='2'>RAYON( )</th>
			<th class='Estilo12' colspan='2'>GOLPE( )</th>
		</tr>
		<tr>
			<td class='Estilo12'>MARCA</td>
			<td ></td>
			<td class='Estilo12'>MODELO</td>
			<td colspan='2'></td>
			<td colspan='5' rowspan='11' align='center'>
				<img src='http://app.aoacolombia.com/Control/img/Automovilgaslla.jpg' border='0' height='180' width='305'>
			</td>
		</tr>
		<tr>
			<td class='Estilo12'>LINEA</td>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<td class='Estilo12'>CLASE</td>
			<td ></td>
			<td class='Estilo12'>COLOR</td>
			<td colspan='2'></td>			
		</tr>
		<tr>
			<td class='Estilo12' colspan='2'>KILOMETRAJE ENTREGA</td>
			<td ></td>
			<td class='Estilo12'>CILINDRAJE</td>
			<td ></td>			
		</tr>
		<tr>
			<td class='Estilo12' colspan='2'>TIPO CARROCERIA</td>
			<td colspan='3'></td>			
		</tr>
		<tr>
			<td class='Estilo12' colspan='2'>VALOR VENTA VEHICULO</td>
			<td colspan='3' class='Estilo12'>$</td>			
		</tr>
		<tr>
			<td class='Estilo12'>MODO PAGO</td>
			<td class='Estilo12'>EFECTIVO</td>
			<td ></td>
			<td class='Estilo12'>CON PRENDA</td>
			<td ></td>
		</tr>
		<tr>
			<td class='Estilo12'>ENTIDAD</td>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<td class='Estilo12' colspan='2'>VALOR GASTO DE TRAMITES</td>
			<td colspan='3' class='Estilo12'>$</td>			
		</tr>
		<tr>
			<td class='Estilo12' colspan='2'>VALOR ADICIONAL</td>
			<td colspan='3' class='Estilo12'>$</td>			
		</tr>
		<tr>
			<td class='Estilo12' colspan='2'>PROPIETARIO AUTOMOTOR</td>
			<td colspan='3' class='Estilo12'></td>			
		</tr>
		<tr>
			<td class='Estilo12' >POLIZA DE SEGUROS DE AUTOMOTORES</td>
			<td class='Estilo12'>SI</td>
			<td ></td>
			<td class='Estilo12'>NO</td>
			<td ></td>
			<th class='Estilo12' rowspan='5' colspan='5'>
				<img src='http://app.aoacolombia.com/Control/img/1.png' border='0' height='91' width='320'/>
			</th>			
		</tr>
		<tr>
			<td class='Estilo12'>ASEGURADORA</td>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<td class='Estilo12' colspan='2'>FECHA VENCIMIENTO SOAT</td>
			<td colspan='3' class='Estilo12'></td>			
		</tr>
		<tr>
			<td class='Estilo12'>PRENDA</td>
			<td class='Estilo12'>SI</td>
			<td ></td>
			<td class='Estilo12'>NO</td>
			<td ></td>
		</tr>
		<tr>
			<td class='Estilo12'>ENTIDAD</td>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th bgcolor='#CCCCCC' colspan='10'><span class='Estilo4'>DOCUMENTOS ENTREGADOS CON EL VEHICULO</span></th>
		</tr>
		<tr>
			<td class='Estilo12'>TARJETA DE PROPIEDAD</td>
			<td class='Estilo12'>ORIGINAL</td>
			<td ></td>
			<td class='Estilo12'>COPIA</td>
			<td ></td>
			<td class='Estilo12'>SEGURO OBLIGATORIO</td>
			<td class='Estilo12'>ORIGINAL</td>
			<td ></td>
			<td class='Estilo12'>COPIA</td>
			<td ></td>
		</tr>
		<tr>
			<td class='Estilo12'>MANUAL DE VEHICULO</td>
			<td class='Estilo12'>SI</td>
			<td ></td>
			<td class='Estilo12'>NO</td>
			<td ></td>
			<td class='Estilo12'>DUPLICADO DE LLAVE</td>
			<td class='Estilo12'>SI</td>
			<td ></td>
			<td class='Estilo12'>NO</td>
			<td ></td>
		</tr>
		<tr>
			<td class='Estilo4'>TOMA DE IMPRONTAS</td>
			<td class='Estilo12'>SI</td>
			<td ></td>
			<td class='Estilo12'>NO</td>
			<td ></td>
			<td class='Estilo12'>3 JUEGOS IMP.</td>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<td class='Estilo12' height='71'>COMENTARIOS</td>
			<td colspan='10' height='71'0></td>
		</tr>
		<tr>
			<th bgcolor='#CCCCCC' colspan='10'><span class='Estilo4'>DATOS DEL COMPRADOR</span></th>
		</tr>
		<tr>
			<td class='Estilo12' colspan='2'>FACTURA NOMBRE DE</td>
			<td colspan='8'></td>
		</tr>
		<tr>
			<td class='Estilo12'>NOMBRES</td>
			<td colspan='9'></td>
		</tr>
		<tr>
			<td class='Estilo12'>APELLIDOS</td>
			<td colspan='9'></td>
		</tr>
		<tr>
			<td class='Estilo12'>CC</td>
			<td></td>
			<td class='Estilo12'>CE</td>
			<td></td>
			<td class='Estilo12'>NIT</td>
			<td></td>
			<td class='Estilo12'>OTROS</td>
			<td></td>
			<td class='Estilo12' width='10%'>No. de identificacion</td>
			<td></td>
		</tr>
		<tr>
			<td class='Estilo12'>DIRECCIÓN</td>
			<td colspan='4'></td>
			<td class='Estilo12'>CIUDAD</td>
			<td colspan='4'></td>
		</tr>
		<tr>
			<td class='Estilo12'>TELEFONO FIJO</td>
			<td colspan='2'></td>
			<td class='Estilo12'>CELULAR</td>
			<td colspan='2'></td>
			<td class='Estilo12'>EMAIL</td>
			<td colspan='3'></td>
		</tr>
		<tr>
			<td class='Estilo12' height='71'>COMENTARIOS</td>
			<td colspan='5' height='71'></td>
			<td colspan='2' class='Estilo12' height='71'>
				Documentos para personal natural:
				&nbsp;
				<li>Cedula.</li>
			</td>
			<td colspan='2' class='Estilo12' height='71'>
				Documentos para persona juridica:
				&nbsp;
				<li>Camara de comercio no mayor a 30 días.</li>
				<li>Cedula representante legal.</li>
				<li>Rut.</li>
			</td>
		</tr>
		<tr>
			<th bgcolor='#CCCCCC' colspan='10'><span class='Estilo4'>DOCUMENTOS DE TRASPASO</span></th>
		</tr>
		<tr>
			<th class='Estilo12' colspan='2'>FLOTA AOA</th>
			<th class='Estilo12' colspan='2'>FLOTA AOA</th>
			<th class='Estilo12' colspan='2'>FLOTA LEASING</th>
			<th class='Estilo12' colspan='2'>FLOTA LEASING</th>
			<th class='Estilo12' colspan='2'>FLOTA DE PRENDA</th>
		</tr>
		<tr>
			<td colspan='2' class='Estilo12'>PERSONA NATUAL</td>
			<td colspan='2' class='Estilo12'>PERSONA JURIDICA</td>
			<td colspan='2' class='Estilo12'>PERSONA NATUAL</td>
			<td colspan='2' class='Estilo12'>PERSONA JURIDICA</td>
			<td colspan='2' class='Estilo12'>FORMATO DE SOLICITUD FUN</td>
		</tr>
		<tr>
			<td class='Estilo12'>Formato compraventa</td>
			<td></td>
			<td class='Estilo12'>Formato compraventa</td>
			<td></td>
			<td class='Estilo12'>Solicitar contrato leasing</td>
			<td></td>
			<td class='Estilo12'>Solicitar contrato leasing</td>
			<td></td>
			<td class='Estilo12'>Copia t.pa nombre nuevo dueño</td>
			<td></td>
		</tr>
		<tr>
			<td class='Estilo12'>Formulario solicitud fun</td>
			<td></td>
			<td class='Estilo12'>Formulario solicitud fun</td>
			<td></td>
			<td class='Estilo12'>Contrato de transferencia</td>
			<td></td>
			<td class='Estilo12'>Contrato de transferencia</td>
			<td></td>
			<td class='Estilo12'>Certificación lugar inscripción</td>
			<td></td>
		</tr>
			<td class='Estilo12'>Contrato de mandato</td>
			<td></td>
			<td class='Estilo12'>Contrato de mandato</td>
			<td></td>
			<td class='Estilo12'>Formulario solicitud fun</td>
			<td></td>
			<td class='Estilo12'>Formulario solicitud fun</td>
			<td></td>
			<td class='Estilo12'>Improntas</td>
			<td></td>
		<tr>
			<td class='Estilo12'>Copia cedula comprador</td>
			<td></td>
			<td class='Estilo12'>Camara de comercio</td>
			<td></td>
			<td class='Estilo12'>Contrato de mandato</td>
			<td></td>
			<td class='Estilo12'>Contrato de mandato</td>
			<td></td>
			<td class='Estilo12'>Contrato de mandato</td>
			<td></td>
		</tr>
		<tr>
			<td class='Estilo12'>Recibio sim</td>
			<td></td>
			<td class='Estilo12'>Entidad juridica no mayor a 30 días</td>
			<td></td>
			<td class='Estilo12'>Copia de cedula del comprador</td>
			<td></td>
			<td class='Estilo12'>Camara de comercio</td>
			<td></td>
			<td class='Estilo12'>Cedula del comprador</td>
			<td></td>
		</tr>
		<tr>
			<td class='Estilo12'>Levantamiento prenda según</td>
			<td></td>
			<td class='Estilo12'>Copia de la cedula representante legal</td>
			<td></td>
			<td class='Estilo12'>Recibio sim</td>
			<td></td>
			<td class='Estilo12'>La entidad juridica no mayor a 30 días</td>
			<td></td>
			<td class='Estilo12'>Cedula de autorizado</td>
			<td></td>
		</tr>
		<tr>
			<td class='Estilo12' colspan='2' rowspan='3'>Según sea el caso</td>
			<td class='Estilo12'>Recibio sim</td>
			<td></td>
			<td class='Estilo12'>Levantamiento prenda según sea el caso</td>
			<td></td>
			<td class='Estilo12'>Copia de la cedula del representante legal</td>
			<td></td>
			<td class='Estilo12'>Recibio sim</td>
			<td></td>
		</tr>
		<tr>
			<td class='Estilo12' rowspan='2'>Prenda si el caso</td>
			<td rowspan='2'></td>
			<td colspan='2' rowspan='2'></td>
			<td class='Estilo12'>Recibio sim</td>
			<td></td>
			<td colspan='2' rowspan='2'></td>			
		</tr>
		<tr>
			<td class='Estilo12'>Levantamiento prenda según sea el caso</td>
			<td></td>
		</tr>
		<tr>
			<th bgcolor='#CCCCCC' colspan='10'><span class='Estilo4'>TRAMITE FINALIZADO PRENDA VEHICULO</span></th>
		</tr>
		<tr>
			<td class='Estilo12' colspan='2'>TARJETA DE PROPIEDAD TRAMITE FINALIZADO</td>
			<td class='Estilo12'>ORIGINAL</td>
			<td></td>
			<td class='Estilo12'>COPIA</td>
			<td></td>
			<td class='Estilo12' colspan='2'>SEGURO OBLIGATORIO</td>
			<td class='Estilo12'>COPIA</td>
			<td></td>
		</tr>
		<tr>
			<td class='Estilo12'>CERTIFICADO DE GASES</td>
			<td class='Estilo12'>SI</td>
			<td></td>
			<td class='Estilo12'>NO</td>
			<td></td>
			<td class='Estilo12'>DUPLICADO DE LLAVE</td>
			<td class='Estilo12'>SI</td>
			<td></td>
			<td class='Estilo12'>NO</td>
			<td></td>
		</tr>
		<tr>
			<td class='Estilo12'>EMPADRONAMIENTO</td>
			<td class='Estilo12'>SI</td>
			<td></td>
			<td class='Estilo12'>NO</td>
			<td></td>
			<td class='Estilo12'>RECIBOS  PAGOS IMPUESTOS AÑOS</td>
			<td colspan='4'></td>
		</tr>
		<tr>
			<td class='Estilo12'>FACTURA COMPRA VEHICULO</td>
			<td class='Estilo12'>SI</td>
			<td></td>
			<td class='Estilo12'>NO</td>
			<td></td>
			<td colspan='5'></td>
		</tr>
		<tr>
			<td class='Estilo12' height='71'>COMENTARIOS</td>
			<td colspan='10' height='71'0></td>
		</tr>
	</table>
 </body></html>";
}


function gendoc_aprobacion_comite_compras_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=FORMATO APROBACIÓN COMITÉ DE COMPRAS_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
}
        .Estilo4 {font-size: 10px; font-weight: bold; }
        .Estilo7 {font-size: 12px; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
        .Estilo13 {font-size: 10px; color: #666666; }
		</style>
<body>
".cabecera_formato($Tipo,"FORMATO APROBACIÓN COMITÉ DE COMPRAS ",$Nconsecutivo)."
<table border cellspacing='0' width='51%' align='left'>
			<tr>
				<td width='508'  align='left' style='font-size:$Tam_Fuente;' ><strong>Fecha de Comité:</strong></td>
  <th width='1'></td>  
  </tr>
            <tr style='font-size:$Tam_Fuente;'><td colspan='6'><table width='0' border='0'>
              <tr>
                <td height='91'><table cellspacing='0' width='98%' align='left' border='1'>
                  <tr>
                    <th width='225' bgcolor='#CCCCCC' scope='col'><span class='Estilo1'>AREA QUE SOLICITA LA COMPRA </span></th>
                  </tr>
                  <tr>
                    <td height='45'>&nbsp;</td>
                  </tr>
                </table></td>
                <td><table width='98%' height='62' border='1' align='left' cellspacing='0'>
                  <tr>
                    <th width='296' bgcolor='#CCCCCC' scope='col'><span class='Estilo1'>TIPO DE COMPRA </span></th>
                  </tr>
                  <tr>
                    <td><table width='296' border='0'>
                        <tr>
                          <th width='64' scope='col'><span class='Estilo1'>BIENES</span></th>
                          <th width='63' scope='col'><table width='57%' border='1' align='left' cellspacing='0'>
                              <tr>
                                <td width='37'>&nbsp;</td>
                              </tr>
                          </table></th>
                          <th width='11' scope='col'>&nbsp;</th>
                          <th width='80' scope='col'><span class='Estilo1'>SERVICIOS</span></th>
                          <th width='56' scope='col'><table cellspacing='0' width='57%' align='left' border='1'>
                              <tr>
                                <td width='37'>&nbsp;</td>
                              </tr>
                          </table></th>
                        </tr>
                    </table></td>
                  </tr>
                </table></td>
              </tr>
            </table>
              </td>
            </tr>
			<tr style='font-size:$Tam_Fuente;'><td colspan='6'><table cellspacing='0' width='100%' align='left' border='1'>
              <tr>
                <th width='467' bgcolor='#CCCCCC' scope='col'><div align='left'><span class='Estilo1'>DESCRIPCION DE LA COMPRA O SERVICIO REQUERIDO </span></div></th>
              </tr>
              <tr>
                <td height='45'>&nbsp;</td>
              </tr>
            </table></td></tr>
			<tr>
				<td style='font-size:$Tam_Fuente;'  align='left' colspan='6'>
				<table cellspacing='0' width='100%' align='left' border='1'>
                  <tr>
                    <td width='78' bgcolor='#CCCCCC'><span class='Estilo10'>PROVEEDOR 1 </span></td>
                    <td width='87'><span class='Estilo4'>Raz&oacute;n Social y/o Nombre </span></td>
                    <td colspan='4'>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class='Estilo1'><strong>NIT/C.C</strong></span></td>
                    <td>&nbsp;</td>
                    <td width='79'><span class='Estilo1'><strong>Actividad CIIU</strong></span></td>
                    <td width='34'>&nbsp;</td>
                    <td width='82'><span class='Estilo1'><strong>Descripci&oacute;n</strong></span></td>
                    <td width='127'>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class='Estilo4'>Direcci&oacute;n</span></td>
                    <td colspan='2'>&nbsp;</td>
                    <td colspan='2'><div align='right'><span class='Estilo4'>Ciudad</span></div></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class='Estilo4'>Contacto</span></td>
                    <td colspan='2'>&nbsp;</td>
                    <td colspan='2'><div align='right'><span class='Estilo4'>Tel&eacute;fono</span></div></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class='Estilo4'>Email</span></td>
                    <td colspan='2'>&nbsp;</td>
                    <td colspan='2'><div align='right'><span class='Estilo4'>Celular</span></div></td>
                    <td>&nbsp;</td>
                  </tr>
                </table></td>
			</tr>
			<tr>
			  <td style='font-size:$Tam_Fuente;'  align='left' colspan='6'>
			  <table cellspacing='0' width='100%' align='left' border='1'>
                  <tr>
                    <td width='81' bgcolor='#CCCCCC'><span class='Estilo10'>PROVEEDOR 2 </span></td>
                    <td width='101'><span class='Estilo4'>Raz&oacute;n Social y/o Nombre </span></td>
                    <td colspan='4'>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class='Estilo1'><strong>NIT/C.C</strong></span></td>
                    <td>&nbsp;</td>
                    <td width='61'><span class='Estilo1'><strong>Actividad CIIU</strong></span></td>
                    <td width='66'>&nbsp;</td>
                    <td width='50'><span class='Estilo1'><strong>Descripci&oacute;n</strong></span></td>
                    <td width='128'>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class='Estilo4'>Direcci&oacute;n</span></td>
                    <td colspan='2'>&nbsp;</td>
                    <td colspan='2'><div align='right'><span class='Estilo4'>Ciudad</span></div></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class='Estilo4'>Contacto</span></td>
                    <td colspan='2'>&nbsp;</td>
                    <td colspan='2'><div align='right'><span class='Estilo4'>Tel&eacute;fono</span></div></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class='Estilo4'>Email</span></td>
                    <td colspan='2'>&nbsp;</td>
                    <td colspan='2'><div align='right'><span class='Estilo4'>Celular</span></div></td>
                    <td>&nbsp;</td>
                  </tr>
                </table>
  </td></tr>
  <tr>
	<td style='font-size:$Tam_Fuente;'  align='left' colspan='6'>
	<table cellspacing='0' width='100%' align='left' border='1'>
                  <tr>
                    <td width='93' bgcolor='#CCCCCC'><span class='Estilo10'>PROVEEDOR 3 </span></td>
                    <td width='97'><span class='Estilo4'>Raz&oacute;n Social y/o Nombre </span></td>
                    <td colspan='4'>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class='Estilo1'><strong>NIT/C.C</strong></span></td>
                    <td>&nbsp;</td>
                    <td width='96'><span class='Estilo1'><strong>Actividad CIIU</strong></span></td>
                    <td width='34'>&nbsp;</td>
                    <td width='101'><span class='Estilo1'><strong>Descripci&oacute;n</strong></span></td>
                    <td width='154'>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class='Estilo4'>Direcci&oacute;n</span></td>
                    <td colspan='2'>&nbsp;</td>
                    <td colspan='2'><div align='right'><span class='Estilo4'>Ciudad</span></div></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class='Estilo4'>Contacto</span></td>
                    <td colspan='2'>&nbsp;</td>
                    <td colspan='2'><div align='right'><span class='Estilo4'>Tel&eacute;fono</span></div></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td><span class='Estilo4'>Email</span></td>
                    <td colspan='2'>&nbsp;</td>
                    <td colspan='2'><div align='right'><span class='Estilo4'>Celular</span></div></td>
                    <td>&nbsp;</td>
                  </tr>
    </table></td>
  </tr>
  <tr>
	<td style='font-size:$Tam_Fuente;'  align='left' colspan='6'><p class='Estilo1'><br />
	  Nota: En caso de invitaci&oacute;n de m&aacute;s de tres proveedores, adicionar otro formato con los proveedores que se requieran
	  <table cellspacing='0' width='99%' align='left' border='1'>
                <tr>
                  <th width='257' bgcolor='#CCCCCC' scope='col'><span class='Estilo4'>DOCUMENTOS SOPORTE </span></th>
                  <th width='24' bgcolor='#CCCCCC' scope='col'><span class='Estilo4'>SI</span></th>
                  <th width='26' bgcolor='#CCCCCC' scope='col'><span class='Estilo4'>NO</span></th>
                  <th width='184' bgcolor='#CCCCCC' scope='col'><span class='Estilo4'>COMENTARIOS</span></th>
                </tr>
                <tr>
                  <td><span class='Estilo1'>CUADRO COMPARATIVO </span></td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td><span class='Estilo1'>MUESTRAS</span></td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td><span class='Estilo1'>FORMATO DE CREACION DE PROVEEDORES </span></td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
      </table></td><TR><TD><br />
	<table width='100%' height='404' border='1' align='left' cellspacing='0'>
                <tr>
                  <th colspan='10' bgcolor='#CCCCCC' scope='col'><div align='left'><span class='Estilo1'>APROBACION COMIT&Eacute; DE COMPRAS </span></div></th>
                </tr>
                <tr>
                  <td width='93'><span class='Estilo1'>PROVEEDOR SELECCIONADO </span></td>
                  <td colspan='9'>&nbsp;</td>
                </tr>
                <tr>
                  <td><span class='Estilo1'>VALOR COMPRA APROBADO </span></td>
                  <td colspan='9'>&nbsp;</td>
                </tr>
                <tr>
                  <td><span class='Estilo1'>FORMA DE PAGO </span></td>
                  <td colspan='9'><table width='300' border='0'>
                    <tr>
                      <td width='65'><div align='right'><span class='Estilo1'>CONTADO</span></div></td>
                      <td width='36'><table width='57%' border='1' align='left' cellspacing='0'><tr><td width='5' class='Estilo11'>&nbsp;</td></tr></table></td>
                      <td><span class='Estilo1'>30 D&Iacute;AS </span></td>
                      <td width='34'><table width='57%' border='1' align='left' cellspacing='0'>
                        <tr>
                          <td width='5' class='Estilo11'>&nbsp;</td>
                        </tr>
                      </table>                      </td>
                      <td><span class='Estilo1'>45 D&Iacute;AS </span></td>
                      <td width='35'><table width='57%' border='1' align='left' cellspacing='0'>
                        <tr>
                          <td width='57' class='Estilo11'>&nbsp;</td>
                        </tr>
                      </table>                      </td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td><span class='Estilo1'>FECHA DE APROBACION </span></td>
                  <td colspan='9'>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan='2'><span class='Estilo1'>REQUSICION U ORDEN DE COMPRA No. </span></td>
                  <td width='404' colspan='8'>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan='10'>&nbsp;</td>
                </tr>
                <tr>
                  <td><span class='Estilo1'>PROVEEDOR SELECCIONADO </span></td>
                  <td colspan='9'>&nbsp;</td>
                </tr>
                <tr>
                  <td><span class='Estilo1'>VALOR COMPRA APROBADO </span></td>
                  <td colspan='9'>&nbsp;</td>
                </tr>
                <tr>
                  <td><span class='Estilo1'>FORMA DE PAGO </span></td>
                  <td colspan='9'><table width='333' border='0'>
                      <tr>
                        <td width='70'><div align='right'><span class='Estilo1'>CONTADO</span></div></td>
                        <td width='44'><table width='57%' border='1' align='left' cellspacing='0'>
                          <tr>
                            <td width='5' class='Estilo11'>&nbsp;</td>
                          </tr>
                        </table></td>
                        <td width='46'><span class='Estilo1'>30 D&Iacute;AS </span></td>
                        <td width='55'><table width='42%' border='1' align='left' cellspacing='0'>
                            <tr>
                              <td width='5' class='Estilo11'>&nbsp;</td>
                            </tr>
                        </table></td>
                        <td width='60'><span class='Estilo1'>45 D&Iacute;AS </span></td>
                        <td width='32'><table width='57%' border='1' align='left' cellspacing='0'>
                            <tr>
                              <td width='20' class='Estilo11'>&nbsp;</td>
                            </tr>
                        </table></td>
                      </tr>
                  </table></td>
                </tr>
                <tr>
                  <td><span class='Estilo1'>FECHA DE APROBACION </span></td>
                  <td colspan='9'>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan='2'><span class='Estilo1'>REQUSICION U ORDEN DE COMPRA No. </span></td>
                  <td colspan='8'>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan='10'>&nbsp;</td>
                </tr>
      </table>
</td>
  </tr>
  <tr>
  <td style='font-size:$Tam_Fuente;'  align='left' colspan='6'><br/>
  <table cellspacing='0' width='100%' align='left' border='1'>
			  <tr>
				<td width='97'><span class='Estilo1'>GERENTE GENERAL </span></td>
				<td width='235'>&nbsp;</td>
				<td width='167'><span class='Estilo1'>FIRMA</span></td>
			  </tr>
			  <tr>
				<td rowspan='2'><span class='Estilo1'>DIRECCI&Oacute;N &oacute; AREA QUE SOLICITA LA COMPRA</span></td>
				<td><span class='Estilo1'>Indicar &aacute;rea </span></td>
				<td rowspan='2'><span class='Estilo1'>FIRMA</span></td>
			  </tr>
			  <tr>
				<td><span class='Estilo1'>Nombre</span></td>
		</tr>
			  <tr>
				<td><span class='Estilo1'>DIRECCI&Oacute;N DE COMPRAS </span></td>
				<td>&nbsp;</td>
				<td><span class='Estilo1'>FIRMA</span></td>
			  </tr>

	  </table>
  </td>
  </tr>
</table>
 </body></html>";
}

function gendoc_aprobacion_comite_compras_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	
	$resultado = q("SELECT nombre FROM aoacol_administra.proveedor where activo = 1");
	$proveedores = array();
   	while ($proveedor = mysql_fetch_object($resultado)) {
		array_push($proveedores, $proveedor);
	}
	
	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/aprobacion_comite_compras.html';
	
}



function gendoc_chequeo_antes_de_marcha_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=FORMATO CHEQUEO ANTES DE MARCHA_$Nconsecutivo.doc");
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 30px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
}
        .Estilo4 {font-size: 10px; font-weight: bold; }
        .Estilo7 {font-size: 12px; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
        .Estilo13 {font-size: 10px; color: #666666; }
		</style>
<body>
".cabecera_formato($Tipo,"FORMATO CHEQUEO ANTES DE MARCHA ",$Nconsecutivo)."
<table cellspacing='0' width='70%' align='left' border='1'>
  <col width='62' span='2' />
  <col width='46' span='3' />
  <col width='56' span='6' />
  <col width='54' span='2' />
  <col width='48' span='4' />
  <tr height='24'>
    <td width='82' height='24' class='Estilo4'>PLACA:</td>
    <td colspan='7' class='Estilo4'>&nbsp;</td>
    <td width='78' class='Estilo4'>Fecha:</td>
    <td colspan='5' class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='24'>
    <td height='24' colspan='4' class='Estilo4'>Responsable    inspecci&oacute;n:</td>
    <td colspan='10' class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='8'>
    <td height='8' colspan='14' class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='20'>
    <td height='38' colspan='5' rowspan='2' class='Estilo4'><div align='center'>TIPO</div></td>
    <td colspan='6' rowspan='2' class='Estilo4'><div align='center'>CRITERIO</div></td>
    <td colspan='2' class='Estilo4'><div align='center'>CONFORME</div></td>
    <td width='257' rowspan='2' class='Estilo4'><div align='center'>OBSERVACIONES</div></td>
  </tr>
  <tr height='18'>
    <td width='40' height='18' class='Estilo4'><div align='center'>SI</div></td>
    <td width='46' class='Estilo4'><div align='center'>NO</div></td>
  </tr>
  <tr height='20'>
    <td height='54' colspan='2' rowspan='3' class='Estilo4'>DOCUMENTOS</td>
    <td colspan='3' class='Estilo4'>Tarjeta propiedad</td>
    <td colspan='6' rowspan='3' class='Estilo4'>Verificar    su presencia y fecha de vigencia adecuada</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>SOAT</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Contrato</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='20'>
    <td height='37' colspan='2' rowspan='2' class='Estilo4'>CALCOMAN&Iacute;AS</td>
    <td colspan='3' class='Estilo4'>L&iacute;nea asistencia</td>
    <td colspan='6' rowspan='2' class='Estilo4'>Verificar su    presencia y fecha de vigencia adecuada</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Prohibido fumar</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='20'>
    <td height='37' colspan='2' rowspan='2' class='Estilo4'>DIRECCIONALES</td>
    <td colspan='3' class='Estilo4'>Delanteras</td>
    <td colspan='6' rowspan='2' class='Estilo4'>Funcionamiento    y respuesta inmediata</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Traseras</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='20'>
    <td height='88' colspan='2' rowspan='5' class='Estilo4'>LUCES</td>
    <td colspan='3' class='Estilo4'>Altas</td>
    <td colspan='6' rowspan='5' class='Estilo4'>Funcionamiento    de bombillos, cubiertas sin roturas, respuesta inmediata</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Bajas</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Stops</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Reversa</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Parqueo</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='68' colspan='2' rowspan='4' class='Estilo4'>LLAVES</td>
    <td colspan='3' class='Estilo4'>Carcasa</td>
    <td colspan='6' class='Estilo4'>Sin    roturas</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Llavero</td>
    <td colspan='6' class='Estilo4'>Coincide    la placa y est&aacute; en buen estado</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Bater&iacute;a</td>
    <td colspan='6' class='Estilo4'>Funcionando    y tapada</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Botones</td>
    <td colspan='6' class='Estilo4'>Funcionando</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='2' class='Estilo4'>PLUMILLAS</td>
    <td colspan='3' class='Estilo4'>Der / Izq / Atr&aacute;s</td>
    <td colspan='6' class='Estilo4'>Estado,    limpieza y funcionamiento</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='20'>
    <td height='37' colspan='2' rowspan='2' class='Estilo4'>FRENOS</td>
    <td colspan='3' class='Estilo4'>Principal</td>
    <td colspan='6' rowspan='2' class='Estilo4'>Verificar    antes de iniciar un servicio</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Emergencia</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='20'>
    <td height='37' colspan='2' rowspan='2' class='Estilo4'>LLANTAS</td>
    <td colspan='3' class='Estilo4'>Delanteras</td>
    <td colspan='6' rowspan='2' class='Estilo4'>Verificar    presi&oacute;n y profundidad de labrado</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Traseras</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='20'>
    <td height='37' colspan='2' rowspan='2' class='Estilo4'>ESPEJOS</td>
    <td colspan='3' class='Estilo4'>Laterales</td>
    <td colspan='6' rowspan='2' class='Estilo4'>Verificar    cubiertas sin roturas y limpieza</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Retrovisor</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='2' class='Estilo4'>PITO</td>
    <td colspan='9' class='Estilo4'>Verificar que responde adecuadamente</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='20'>
    <td height='54' colspan='2' rowspan='3' class='Estilo4'>FLUIDOS</td>
    <td colspan='3' class='Estilo4'>Frenos</td>
    <td colspan='6' rowspan='3' class='Estilo4'>Verificar    niveles y reportar fugas</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Aceite</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Refrigerante</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='20'>
    <td height='37' colspan='2' rowspan='2' class='Estilo4'>APOYA<br />
    CABEZAS</td>
    <td colspan='3' class='Estilo4'>Delanteros</td>
    <td colspan='6' rowspan='2' class='Estilo4'>Verificar su    presencia y estado</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Traseros</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='20'>
    <td height='37' colspan='2' rowspan='2' class='Estilo4'>CINTURONES DE SEGURIDAD</td>
    <td colspan='3' class='Estilo4'>Delanteros</td>
    <td colspan='6' rowspan='2' class='Estilo4'>Verificar su    presencia y estado</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Traseros</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='20'>
    <td height='295' colspan='2' rowspan='9' class='Estilo4'>HERRAMIENTA    Y KIT DE CARRETERA</td>
    <td colspan='9' class='Estilo4'>Alicate, destornilladores, llaves de expansi&oacute;n y    llaves fijas</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Extintor</td>
    <td colspan='6' class='Estilo4'>Fecha    vencimiento</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Cruceta</td>
    <td colspan='6' class='Estilo4'>Adecuado    para las tuercas</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Linterna</td>
    <td colspan='6' class='Estilo4'>Presente    y con bater&iacute;a</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Gato</td>
    <td colspan='6' class='Estilo4'>Que    soporte el peso</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Tacos</td>
    <td colspan='6' class='Estilo4'>2    tacos</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='33'>
    <td height='33' colspan='3' class='Estilo4'>Se&ntilde;ales</td>
    <td colspan='6' class='Estilo4'>2    se&ntilde;ales de forma triangular, reflectivas y con soporte</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='17'>
    <td height='17' colspan='3' class='Estilo4'>Chaleco</td>
    <td colspan='6' class='Estilo4'>Reflectivo</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
  <tr height='140'>
    <td height='140' colspan='3' class='Estilo4'>Botiqu&iacute;n</td>
    <td colspan='6' class='Estilo4'>Yodoplvidona    soluci&oacute;n antis&eacute;ptico bolsa (120ml), jab&oacute;n, gasas, curas, venda el&aacute;stica,    mlcropore rollo, algod&oacute;n paquete (25 gr), acetaminof&eacute;n tabletas}, mareol    tabletas, sales de rehldratacl&oacute;n oral, baja lenguas, suero fisiol&oacute;gico bolsa    (250 mi). guantes latex desechables, toallas higi&eacute;nicas, tijeras y term&oacute;metro    oral.</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
    <td class='Estilo4'>&nbsp;</td>
  </tr>
</table>
 </body></html>";
}

function formato_solicitud_gestion_humana_ok()
{

	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=Solicitud gestion humana_$Nconsecutivo.doc");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	
	$html = "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 5px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
}
        .Estilo4 {font-size: 10px; font-weight: bold; }
        .Estilo7 {font-size: 12px; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
		.Estilo12 {font-size: 8}
        .Estilo13 {font-size: 10px; color: #666666; 
		
		</style>
<body>
".cabecera_formato($Tipo,"FORMATO DE SOLICITUD GESTION HUMANA",$Nconsecutivo)."
	<table border cellspacing='0' width='100%' align='left'>
		
		<tr>
			<th scope='col' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>NOMBRE DEL SOLICITANTE</span></th>
			<td colspan='7'></td>
		</tr>
		<tr>
			<th scope='col' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>CARGO</span></th>
			<td colspan='7'></td>
		</tr>
		<tr>
			<th scope='col' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>FECHA</span></th>
			<td></td>
			<td width='10%'>DIA</td>
			<td></td>
			<td width='10%'>MES</td>
			<td></td>
			<td width='10%'>AÑO</td>
			<td></td>
		</tr>
		<tr>
			<th scope='col' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>TIPO DE SOLICITUD</span></th>
			<td colspan='7'></td>
		</tr>
		<tr>			
			<td height='30px' colspan='8'>&nbsp;</td>
		</tr>
		<tr>
			<td height='30px' colspan='8'>&nbsp;</td>
		</tr>
		<tr>
			<td height='30px' colspan='8'>&nbsp;</td>
		</tr>
		<tr>
			<th scope='col' colspan='8'  height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>COMENTARIOS A TENER EN CUENTA</span></th>
		</tr>
		<tr>
			<td height='30px' colspan='8'>&nbsp;</td>
		</tr>
		<tr>
			<td height='30px' colspan='8'>&nbsp;</td>
		</tr>
		<tr>
			<td height='30px' colspan='8'>&nbsp;</td>
		</tr>
		<tr>
			<td height='30px' colspan='8'>&nbsp;</td>
		</tr>
		<tr>
			<td height='30px' colspan='8'>&nbsp;</td>
		</tr>
		";
	
	
	$html = $html."</table>
 </body></html>";	
	echo $html;
	
}


function formato_solicitud_gestion_humana_ok_2()
{

	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/solicitud_gestion_humana.html';	
}


function control_permisos_ok()
{

	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=Solicitud permisos_$Nconsecutivo.doc");
	$html = "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 5px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
}
        .Estilo4 {font-size: 10px; font-weight: bold; }
        .Estilo7 {font-size: 12px; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
		.Estilo12 {font-size: 8}
        .Estilo13 {font-size: 10px; color: #666666; 
		
		</style>
<body>
".cabecera_formato($Tipo,"CONTROL DE PERMISOS",$Nconsecutivo)."
	<table border cellspacing='0' width='100%' align='left'>
		
		<tr>
			<th scope='col' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>FECHA</span></th>
			<td colspan='4'>&nbsp;</td>
		</tr>
		<tr>
			<th scope='col' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>NOMBRE TRABAJADOR</span></th>
			<td colspan='4'>&nbsp;</td>
		</tr>
		<tr>
			<th scope='col' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>CARGO</span></th>
			<td colspan='4'>&nbsp;</td>			
		</tr>
		<tr>
			<th scope='col' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>DEPENDENCIA</span></th>
			<td colspan='4'>&nbsp;</td>			
		</tr>
		<tr>
			<th scope='col' colspan='5' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>MOTIVOS DEL PERMISO</span></th>				
		</tr>
		<tr>
			<td scope='col' colspan='5'  height='30px' >&nbsp;</th>				
		</tr>
		<tr>
			<td scope='col' colspan='5'  height='30px' >&nbsp;</th>				
		</tr>
		<tr>
			<td scope='col' colspan='5'  height='30px' >&nbsp;</th>				
		</tr>
		<tr>
			<td scope='col' colspan='5'  height='30px' >&nbsp;</th>				
		</tr>
		<tr>
			<td scope='col' colspan='5'  height='30px' >&nbsp;</th>				
		</tr>
		<tr>
			<td scope='col' colspan='5'  height='30px' >&nbsp;</th>				
		</tr>
		<tr>
			<th scope='col' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>DESCONTABLE</span></th>
			<th scope='col' width='10%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>SI</span></th>
			<td></td>
			<th scope='col' width='10%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>NO</span></th>
			<td></td>			
		</tr>
		<tr>
			<th scope='col' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>FIRMA DEL TRABAJADOR</span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>VO BO JEFE INMEDIATO</span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col'  width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>VO BO RECURSOS HUMANOS</span></th>				
			<td colspan='4'></td>
		</tr>
		";
	
	
	$html = $html."</table>
 </body></html>";	
	echo $html;
	
}

function control_permisos_ok_2()
{

	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/control_permisos.html';
	
	
}

function requisicion_personal_ok()
{

	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=Requisicion personal_$Nconsecutivo.doc");
	$html = "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 5px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
}
        .Estilo4 {font-size: 10px; font-weight: bold; }
        .Estilo7 {font-size: 12px; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
		.Estilo12 {font-size: 8}
        .Estilo13 {font-size: 10px; color: #666666; 
		
		</style>
<body>
".cabecera_formato($Tipo,"REQUISICION DE PERSONAL",$Nconsecutivo)."
	<table border cellspacing='0' width='100%' align='left'>
		
		<tr>
			<th scope='col' colspan='5'  bgcolor='#CCCCCC' ><span class='Estilo4'>SOLICITUD GESTIÓN HUMANA</span></th>
		</tr>
		
		<tr>
			<th scope='col' width='40%' height='30' bgcolor='#CCCCCC' ><span class='Estilo4'>FECHA</span></th>
			<td colspan='4'></td>
		</tr>
		<tr>
			<th scope='col' colspan='5'  height='30' bgcolor='#CCCCCC' ><span class='Estilo4'>INFORME DE PUESTO VACANTE</span></th>
		</tr>
		<tr>
			<th scope='col' width='40%' height='30' bgcolor='#CCCCCC' ><span class='Estilo4'>NOMBRE DEL PUESTO</span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col' width='40%' height='30' bgcolor='#CCCCCC' ><span class='Estilo4'>NIVEL DE ESCOLARIDAD</span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col' width='40%' height='30' bgcolor='#CCCCCC' ><span class='Estilo4'>LUGAR DE RESIDENCIA</span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col' rowspan='5' width='40%' height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>EXPERIENCIA LABORAL</span></th>
						
		</tr>
		<tr>
			<th scope='col' colspan='2' width='30%'  height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>SEIS MESES</span></th>
			<td colspan='2'></td>
		</tr>
		<tr>
			<th scope='col' colspan='2' width='30%'  height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>UN AÑO</span></th>
			<td colspan='2'></td>
		</tr>
		<tr>
			<th scope='col' colspan='2' width='30%'  height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>MAS DE UN AÑO</span></th>
			<td colspan='2'>¿Cuanto?</td>
		</tr>
		<tr>
			<th scope='col' colspan='2' width='30%'  height='30px' bgcolor='#CCCCCC' ><span class='Estilo4'>NINGUNA</span></th>
			<td colspan='2'></td>
		</tr>
		<tr>
			<th scope='col' width='40%' height='50' bgcolor='#CCCCCC' ><span class='Estilo4'>PERFIL DEL CARGO</span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col' width='40%' height='50' bgcolor='#CCCCCC' ><span class='Estilo4'>NOMBRE DE LA PERSONA SOLICITANTE</span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col' width='40%' height='50' bgcolor='#CCCCCC' ><span class='Estilo4'>AREA SOLICITANTE</span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col' width='40%' height='30' bgcolor='#CCCCCC' ><span class='Estilo4'>ELABORADO POR</span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col' width='40%' height='30' bgcolor='#CCCCCC' ><span class='Estilo4'>NOMBRE</span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col' width='40%' height='30' bgcolor='#CCCCCC' ><span class='Estilo4'>CARGO</span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col' width='40%' height='30' bgcolor='#CCCCCC' ><span class='Estilo4'>FECHA DE CONTRATACIÓN E INICIO DE LABORES </span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col' colspan='5'  bgcolor='#CCCCCC' ><span class='Estilo4'>SOLICITUD AREA DE TECNOLOGÍA</span></th>
		</tr>
		<tr>
			<th scope='col' width='40%' height='30' bgcolor='#CCCCCC' ><span class='Estilo4'>No CASO MESA DE AYUDA</span></th>
			<td colspan='4'></td>			
		</tr>
		<tr>
			<th scope='col' colspan='5'  bgcolor='#CCCCCC' ><span class='Estilo4'>SOLICITUD AREA ADMINISTRATIVA</span></th>
		</tr>
		<tr>
			<td scope='col' colspan='5' height='70'></td>
		</tr>
		";
	
	
	$html = $html."</table>
 </body></html>";	
	echo $html;
	
}

function requisicion_personal_ok_2()
{

	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/requisicion_personal.html';
	
}

function acoso_laboral_ok()
{

	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=Acoso laboral_$Nconsecutivo.doc");
	$html = "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 5px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
	
}
        .Estilo4 {font-size: 10px; font-weight: bold; }
        .Estilo7 {font-size: 12px; font-weight: bold; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
		.Estilo12 {font-size: 8}
        .Estilo13 {font-size: 10px; color: #666666; 
		
		</style>
<body>
".cabecera_formato($Tipo,"FORMATO PARA INTERPONER PRESUNTAS QUEJAS DE ACOSO LABORAL",$Nconsecutivo)."";

	$html = $html."<table border cellspacing='0' width='100%' align='left'>
		<tr>
			<td class='Estilo11' bgcolor='#CCCCCC' height='30px'><span class='Estilo7'>Fecha de diligenciamiento:</span></td>
			<td class='Estilo11'>DIA:&nbsp</td>
			<td class='Estilo11'>MES:</td>
			<td class='Estilo11'>AÑO:</td>
		</tr>
		<tr>
			<td colspan='4'>
				<p class='Estilo11'>Respetado Funcionario, Le solicitamos que  antes de colocar su queja, tenga en cuenta la definición de Acoso Laboral: “Toda conducta PERSISTENTE y DEMOSTRABLE, ejercida sobre un empleado, trabajador por parte de empleador, un jefe o superior jerárquico inmediato o mediato, un compañero de trabajo o un subalterno, encaminada a infundir miedo, intimidación, terror y angustia a causar perjuicio laboral, generar desmotivación en el trabajo, o inducir la renuncia del mismo”.   
				(Ley 1010 de 2006 y Resolución Nacional 0652 de 2012).
				</p>
			</td>
		</tr>
		<tr>
			<td colspan='4'>
				<p class='Estilo11'><span style='font-weight: bold;'>MODALIDADES DE ACOSO LABORAL:</span> Lea bien las modalidades de acoso laboral establecidas en la Ley 1010 de 2006, verifique si se cumplen todos los requisitos señalados por la norma y marque con una X en la casilla el que considere se aplica a su caso:</p>
			</td>
		</tr>
		<tr>
			<td class='Estilo11' style='text-align:center;' width='10%'>1</td>
			<td colspan='2'>
				<p class='Estilo11'><span style='font-weight: bold;'>Maltrato laboral</span><br> 
					Todo acto de violencia contra la integridad física o moral, la libertad física o sexual y los bienes de quien se desempeñe como empleado o trabajador; toda expresión verbal injuriosa o ultrajante que lesione la integridad moral o los derechos a la intimidad y al buen nombre de quienes participen en una relación de trabajo de tipo laboral o todo comportamiento tendiente a menoscabar la autoestima y la dignidad de quien participe en una relación de trabajo de tipo laboral.
				</p>
			</td>
			<td width='15%'>&nbsp&nbsp&nbsp</td>
		</tr>
		<tr>
			<td class='Estilo11' style='text-align:center;'>2</td>
			<td colspan='2'>
				<p class='Estilo11'><span style='font-weight: bold;'>Persecución Laboral</span><br> 
					Toda conducta cuyas características de reiteración o evidente arbitrariedad permitan inferir el propósito de inducir la renuncia del empleado o trabajador, mediante la descalificación, la carga excesiva de trabajo y cambios permanentes de horario que puedan producir desmotivación laboral.
				</p>
			</td>
			<td>&nbsp&nbsp&nbsp</td>
		</tr>
		<tr>
			<td class='Estilo11' style='text-align:center;'>3</td>
			<td colspan='2'>
				<p class='Estilo11'><span style='font-weight: bold;'>Discriminación laboral</span><br> 
					Todo trato diferenciado por razones de raza, género, origen familiar o nacional, credo religioso, preferencia política o situación social o que carezca de toda razonabilidad desde el punto de vista laboral.
				</p>
			</td>
			<td>&nbsp&nbsp&nbsp</td>
		</tr>
		<tr>
			<td class='Estilo11' style='text-align:center;'>4</td>
			<td colspan='2'>
				<p class='Estilo11'><span style='font-weight: bold;'>Entorpecimiento laboral</span><br> 
					Toda acción tendiente a obstaculizar el cumplimiento de la labor o hacerla más gravosa o retardarla con perjuicio para el trabajador o empleado. Constituyen acciones de entorpecimiento laboral, entre otras, la privación, ocultación o inutilización de los insumos, documentos o instrumentos para la labor, la destrucción o pérdida de información, el ocultamiento de correspondencia o mensajes electrónicos.
				</p>
			</td>
			<td>&nbsp&nbsp&nbsp</td>
		</tr>
		<tr>
			<td class='Estilo11' style='text-align:center;'>5</td>
			<td colspan='2'>
				<p class='Estilo11'><span style='font-weight: bold;'>Inequidad laboral </span><br> 
					Asignación de funciones a menosprecio del trabajador.
				</p>
			</td>
			<td>&nbsp&nbsp&nbsp</td>
		</tr>
		<tr>
			<td class='Estilo11' style='text-align:center;'>6</td>
			<td colspan='2'>
				<p class='Estilo11'><span style='font-weight: bold;'>Desprotección laboral </span><br> 
					Toda conducta tendiente a poner en riesgo la integridad y la seguridad del trabajador mediante órdenes o asignación de funciones sin el cumplimiento de los requisitos mínimos de protección y seguridad para el trabajador.
				</p>
			</td>
			<td>&nbsp&nbsp&nbsp</td>
		</tr>
		<tr>
			<td colspan='4' bgcolor='#CCCCCC' style='text-align:center;' class='Estilo11'><span style='font-weight: bold;'>INFORMACIÓN DE LA PERSONA QUE PRESENTA LA QUEJA</span></td>
		</tr>
		<tr>
			<td colspan='2' height='80px' class='Estilo11'><span style='font-weight: bold;'>Nombre: ( no se aceptan anónimos)</span><br><br>&nbsp&nbsp&nbsp</td>
			<td colspan='2' height='80px' class='Estilo11'><span style='font-weight: bold;'>Documento:</span><br><br>&nbsp&nbsp&nbsp</td>			
		</tr>
		<tr>
			<td colspan='2' height='60px' class='Estilo11'><span style='font-weight: bold;'>Dependencia: </span><br><br>&nbsp&nbsp&nbsp</td>
			<td colspan='2' height='60px' class='Estilo11'><span style='font-weight: bold;'>Cargo:</span><br><br>&nbsp&nbsp&nbsp</td>			
		</tr>
		<tr>
			<td colspan='4' height='40px' class='Estilo11'><span style='font-weight: bold;'>Correo electronico</span><br><br>&nbsp&nbsp&nbsp</td>						
		</tr>
		<tr>
			<td colspan='4' bgcolor='#CCCCCC' style='text-align:center;' class='Estilo11'><span style='font-weight: bold;'>INFORMACIÓN DE LA PERSONA CONTRA LA  QUE SE PRESENTA LA QUEJA</span></td>
		</tr>
		<tr>
			<td colspan='4' height='80px' class='Estilo11'><span style='font-weight: bold;'>Nombre: ( no se aceptan anónimos)</span><br><br>&nbsp&nbsp&nbsp</td>						
		</tr>
		<tr>
			<td colspan='2' height='60px'><span style='font-weight: bold;' class='Estilo11'>Dependencia: </span><br><br>&nbsp&nbsp&nbsp</td>
			<td colspan='2' height='60px'><span style='font-weight: bold;' class='Estilo11'>Cargo:</span><br><br>&nbsp&nbsp&nbsp</td>			
		</tr>
		<tr>
			<td colspan='4' bgcolor='#CCCCCC' style='text-align:center;' class='Estilo11'><span style='font-weight: bold;'>RELACIONE LOS HECHOS CONSTITUTIVOS DE LA QUEJA:</span></td>
		</tr>
		<tr>
			<td colspan='4' height='50px'>&nbsp&nbsp&nbsp</td>
		</tr>
		<tr>
			<td colspan='4' bgcolor='#CCCCCC' class='Estilo11'><span style='font-weight: bold;'>De ser necesario, el Comité podrá solicitarle posteriormente la ampliación de la información ofrecida ¿Cuenta usted con alguna prueba o con el testimonio de alguna persona? ¿Cuál(es) y/o quién?</span></td>
		</tr>
		<tr>
			<td colspan='4' height='50px'>&nbsp&nbsp&nbsp</td>
		</tr>
		<tr>
			<td colspan='4' bgcolor='#CCCCCC' style='text-align:center;' class='Estilo11'><span style='font-weight: bold;'>Sugerencias:</span></td>
		</tr>
		<tr>
			<td colspan='4' height='50px'>&nbsp&nbsp&nbsp</td>
		</tr>
		<tr>
			<td colspan='4' bgcolor='#CCCCCC' style='text-align:center;' class='Estilo11'><span style='font-weight: bold;'>Firma y cedula de la persona de la queja:</span></td>
		</tr>
		<tr>
			<td colspan='4' height='50px'>&nbsp&nbsp&nbsp</td>
		</tr>
		";
	
	
	$html = $html."</table>
 </body></html>";	
	echo $html;
	
}

function acoso_laboral_ok_2()
{

	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	$insert = "insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')";
	//echo $insert;
	//exit;
	q($insert);
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/acoso_laboral.html';
	
}


function autoreporte_condiciones_inseguras_ok()
{

	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=AutoReporte_Condiciones_inseguras_$Nconsecutivo.doc");
	$html = "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 5px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
}
        .Estilo4 {font-size: 10px; font-weight: bold; }
        .Estilo7 {font-size: 12px; font-weight: bold; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
		.Estilo12 {font-size: 8}
        .Estilo13 {font-size: 10px; color: #666666; 
		
		</style>
<body>
".cabecera_formato($Tipo,"FORMATO AUTOREPORTE DE ACTOS Y CONDICIONES INSEGURAS",$Nconsecutivo)."";

	$html = $html."<table border cellspacing='0' width='100%' align='left'>
		<tr>
			<td class='Estilo11' bgcolor='#CCCCCC' height='30px' width='25%'><span style='font-weight:bold;'>1.Nombre:</span></td>
			<td class='Estilo11' width='25%'>&nbsp</td>
			<td class='Estilo11' bgcolor='#CCCCCC' height='30px' width='25%'><span style='font-weight:bold;'>2.Telefono:</span></td>
			<td width='25%'></td>			
		</tr>
		<tr>
			<td class='Estilo11' bgcolor='#CCCCCC' height='30px' width='25%'><span style='font-weight:bold;'>3.Correo:</span></td>
			<td width='25%'>&nbsp</td>
			<td class='Estilo11' bgcolor='#CCCCCC' height='30px' width='25%'><span style='font-weight:bold;'>4.Sede:</span></td>
			<td width='25%'></td>			
		</tr>
		<tr>
			<td class='Estilo11' bgcolor='#CCCCCC' height='30px' width='25%'><span style='font-weight:bold;'>5.Fecha de reporte:</span></td>
			<td width='25%'>&nbsp</td>
			<td class='Estilo11' bgcolor='#CCCCCC' height='30px' width='25%'><span style='font-weight:bold;'>6.Localización:</span></td>
			<td width='25%'></td>			
		</tr>
		<tr>
			<td class='Estilo11' colspan='4' bgcolor='#CCCCCC' style='text-align:center;'><span style='font-weight: bold;'>7.Descripción del acto o condición insegura</span></td>
		</tr>
		<tr>
			<td colspan='4' height='150px;' >¿Qué sucedió u observo?<br><br><br><br><br><br><br></td>
		</tr>
		<tr>
			<td class='Estilo11' colspan='4' bgcolor='#CCCCCC' style='text-align:center;'><span style='font-weight: bold;'>FIRMA</span></td>
		</tr>
		<tr>
			<td colspan='4' height='60px;'>&nbsp&nbsp</td>
		</tr>
		<tr>
			<td class='Estilo11' colspan='4' bgcolor='#CCCCCC' style='text-align:center;'><span style='font-weight: bold;'>8. Comentarios u observaciones</span></td>
		</tr>
		<tr>
			<td colspan='4' height='150px;'>&nbsp</td>
		</tr>
		<tr>
			<td class='Estilo11' colspan='4' ><i>*Envie la información reportada al analista de Salud Ocupacional o al COPASST para tomar acciones frente al acto y/o condición insegura reportada.*
				<br>¡GRACIAS!</i>
			</td>
		</tr>
		<tr>
			<td class='Estilo11' colspan='4' bgcolor='#CCCCCC' style='text-align:center;'><span style='font-weight: bold;'>Instrucciones de diligenciamiento</span></td>
		</tr>
		<tr>
			<td  class='Estilo11' bgcolor='#CCCCCC' style='text-align:center;'><span style='font-weight: bold;'>Item</span></td>
			<td class='Estilo11' colspan='3' bgcolor='#CCCCCC' style='text-align:center;'><span style='font-weight: bold;'>Descripción</span></td>
		</tr>
		<tr>
			<td  class='Estilo11' style='text-align:center;'>1</td>
			<td class='Estilo11' colspan='3'  style='text-align:center;'>Nombre completo de la persona que realiza la identificación y el reporte del acto inseguro o la condicion insegura relacionada con los riesgos laborales.</td>
		</tr>
		<tr>
			<td  class='Estilo11' style='text-align:center;'>2</td>
			<td class='Estilo11' colspan='3'  style='text-align:center;'>Numero de telefono de la persona que reporta el acto o la condición.</td>
		</tr>
		<tr>
			<td  class='Estilo11' style='text-align:center;'>3</td>
			<td class='Estilo11' colspan='3'  style='text-align:center;'>Correo electronico corpórativo de la persona que realiza el reporte del acto o la condición.</td>
		</tr>
		<tr>
			<td class='Estilo11' style='text-align:center;'>4</td>
			<td class='Estilo11' colspan='3'  style='text-align:center;'>Registre el nombre de la sede donde se a identificado la situación.</td>
		</tr>
		<tr>
			<td class='Estilo11' style='text-align:center;'>5</td>
			<td class='Estilo11' colspan='3'  style='text-align:center;'>Fecha del dliligenciamiento del reporte.</td>
		</tr>
		<tr>
			<td class='Estilo11' style='text-align:center;'>6</td>
			<td class='Estilo11' colspan='3'  style='text-align:center;'>Escriba el lugar especifico donde se ha identificado el acto o condicion insegura.</td>
		</tr>
		<tr>
			<td class='Estilo11' style='text-align:center;'>7</td>
			<td class='Estilo11' colspan='3'  style='text-align:center;'>Realice la descripcion del acto y/o condición identificada que pueda considerar puede desencadenar un accidente de trabajo o afección de la estructura, el proceso, el medio ambiente etc.</td>
		</tr>
		<tr>
			<td class='Estilo11' style='text-align:center;'>8</td>
			<td class='Estilo11' colspan='3'  style='text-align:center;'>Si tiene alguna obervación o comentarios, la cual aporte información al reporte del acto y/o condición insegura, por favor documentarla.</td>
		</tr>
		";
	
	
	$html = $html."</table>
 </body></html>";	
	echo $html;
	
}

function autoreporte_condiciones_inseguras_ok_2()
{

	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/condiciones_inseguras.html';	
	
}

function formato_acceso_datacenter_ok(){
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=FORMATO ACCESO DATACENTER_$Nconsecutivo.doc");
	$html = "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 5px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
}
        .Estilo4 {font-size: 10px; font-weight: bold; }
        .Estilo7 {font-size: 12px; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
		.Estilo12 {font-size: 8}
        .Estilo13 {font-size: 10px; color: #666666; 
		
		</style>
<body>
".cabecera_formato($Tipo,"FORMATO INGRESO AL DATACENTER",$Nconsecutivo)."
	<table border cellspacing='0' width='100%' align='left'>
		
		<tr>
			<th scope='col' width='20%' bgcolor='#CCCCCC' ><span class='Estilo4'>FECHA</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>NOMBRE VISITANTE</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>CEDULA VISITANTE</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>FIRMA VISITANTE</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>SUPERVISOR</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>FIRMA SUPERVISOR</span></th>
		</tr>";
	 for($i=0;$i<30;$i++)
	{
		$html = $html."<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>";
	}
	
	$html = $html."</table>
 </body></html>";	
	echo $html;
}

function formato_acceso_datacenter_ok_2(){
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/acceso_datacenter.html';

}


function auditoria_backup_ok()
{
	
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
		//echo "consecutivo final ".$consecutivo;
		//exit;
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/auditoria_backup.html';
		
}

function auditoria_mantenimiento_ok()
{
	
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
		//echo "consecutivo final ".$consecutivo;
		//exit;
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/auditoria_mantenimiento.html';
		
}

function formato_reintegro_gastos_ok(){
	global $consecutivo,$tipodoc,$Nusuario;
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');
	q("insert into formatos_aoa (creado_por,fecha,tipo_formato,consecutivo,destinatario,dirigido_a,cargo,ciudad,asunto) values 
		('$Nusuario','$Ahora','$tipodoc','$consecutivo','','','','','')");
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';;
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=FORMATO REINTEGRO DE GASTOS_$Nconsecutivo.doc");
	
	$html =  "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 5px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
}
        .Estilo4 {font-size: 10px; font-weight: bold; }
        .Estilo7 {font-size: 12px; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
		.Estilo12 {font-size: 8}
        .Estilo13 {font-size: 10px; color: #666666; 
		
		</style>
<body>
".cabecera_formato($Tipo,"FORMATO REINTEGRO DE GASTOS",$Nconsecutivo)."
	<table border cellspacing='0' width='100%' align='left'>
	
		<tr>			
			<td bgcolor='#CCCCCC' class='Estilo4' style='text-align:center;' colspan='7'> FECHA DE RADICACION REINTEGRO GASTOS</td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC' class='Estilo4' colspan='10' style='text-align:center;'> RESPONSABLE REINTEGRO GASTOS </td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC' class='Estilo4' colspan='5' style='text-align:center;'> NOMBRE DEL EMPLEADO </td>
			<td colspan='5'></td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC' class='Estilo4' colspan='2' style='text-align:center;'> DOCUMENTO DE IDENTIDAD </td>
			<td colspan='3'></td>
			<td bgcolor='#CCCCCC' class='Estilo4' colspan='2' style='text-align:center;'> CIUDAD </td>
			<td colspan='3'></td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC' class='Estilo4' colspan='10' style='text-align:center;'> DISCRIMINACIÓN GASTOS A REINTEGRAR </td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC' class='Estilo4' colspan='5' style='text-align:center;'> VALOR A REINTEGRAR </td>
			<td colspan='5'></td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC' class='Estilo4' colspan='10' style='text-align:center;'> FACTURAS A REINTEGRAR </td>
		</tr>
		<tr>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>No.</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>FECHA</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>NIT/CEDULA</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>NOMBRE DEL <br> PROVEEDOR</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>OBSERVACIONES</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>PLACA</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>CENTRO DE<BR> COSTOS</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>CIUDAD</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>FACTURA DE<br>REINTEGRO</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>VALOR <br> TOTAL</span></th>
		</tr>";
		
		for($i=0;$i<14;$i++)
		{ $html = $html."<tr>
			<td height='30'></td>
			<td height='30'></td>
			<td height='30'></td>
			<td height='30'></td>
			<td height='30'></td>			
			<td height='30'></td>
			<td height='30'></td>
			<td height='30'></td>
			<td height='30'></td>
			<td height='30'></td>
			
		</tr>"; }
		
		$html .=  "
			<tr>
				<td bgcolor='#CCCCCC' class='Estilo4' colspan='5' style='text-align:center;'> VALOR A REINTEGRAR POR GASTOS </td>
				<td colspan='5'></td>
			</tr>
			<tr>
				<td bgcolor='#CCCCCC' class='Estilo4' colspan='10'  style='text-align:center;'> OBSERVACIONES </td>
			</tr>
			<tr>
				<td height='50' colspan='10'> </td>
			</tr>
			<tr>
				<td bgcolor='#CCCCCC' class='Estilo4' colspan='2' style='text-align:center;'> ELABORADO POR: </td>
				<td bgcolor='#CCCCCC' class='Estilo4' colspan='2' style='text-align:center;'> REVISADO POR: </td>
				<td bgcolor='#CCCCCC' class='Estilo4' colspan='2' style='text-align:center;'> V.B. TESORERIA: </td>
				<td bgcolor='#CCCCCC' class='Estilo4' colspan='2' style='text-align:center;'> ESPACIO CONTABILIDAD </td>
				<td bgcolor='#CCCCCC' class='Estilo4' colspan='2' style='text-align:center;'> OTROS </td>
			</tr>
			<tr>
				<td  colspan='2' height='50'>  </td>
				<td  colspan='2' height='50'>  </td>
				<td  colspan='2' height='50'>  </td>
				<td  colspan='2' height='50'>  </td>
				<td  colspan='2' height='50'>  </td>
			</tr>			
		";
	
	
	$html = $html."</table> </body></html>";
	echo $html;
		
}

function formato_reintegro_gastos_ok_2(){
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
	
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/formato_reintegro_gastos.html';

		
}




function gendoc_chequeo_antes_de_marcha_ok_2()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}		
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	header('Content-Type: text/html; charset=utf-8');
	$resultado = q("select Distinct(placa) as dplaca from aoacol_aoacars.vehiculo");
	$placas = array();
   	while ($placa = mysql_fetch_object($resultado)) {
		array_push($placas, $placa);
	}
	
	include 'views/gestion_documental/chequeo_antes_marcha.html';
	
}

function formato_inscripcion_actualizacion_proveedores_ok()
{
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		//print_r($Formato);
		//exit;
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
		//echo "consecutivo final ".$consecutivo;
		//exit;
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';
	
	$resultado = q("SELECT * FROM aoacol_administra.oficina");
	$oficinas = array();
   	while ($oficina = mysql_fetch_object($resultado)) {
		array_push($oficinas, $oficina);
	}

	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/inscripcion_actualizacion_proveedores.html';
	
}

function lista_chequeo_pc_ok()
{
	
	global $consecutivo,$tipodoc,$Nusuario;
	//echo "here";
	//exit;
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		//print_r($_SESSION);
		//exit;
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		//echo "consecutivo ";
		//print_r($consecutivo);
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
		//echo "consecutivo final ".$consecutivo;
		//exit;
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;
	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/lista_chequeo_pc.html';
		
}

function pruebas_continuidad()
{
	
	global $consecutivo,$tipodoc,$Nusuario;
	
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;

	include 'views/gestion_documental/pruebas_continuidad.html';
		
}

function validar_alistamiento_ok()
{
	
	global $consecutivo,$tipodoc,$Nusuario;
	
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;

	include 'views/gestion_documental/validar_alistamiento.html';
		
}

function formato_retroalimentacion_ok()
{
	
	global $consecutivo,$tipodoc,$Nusuario;
	
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';;

	include 'views/gestion_documental/formato_retroalimentacion.html';
		
}

function formato_vehiculo_reemplazo_callcenter_ok()
{
	
	global $consecutivo,$tipodoc,$Nusuario;
	
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';
	
	$resultado = q("select * from aoacol_aoacars.aseguradora");
	$aseguradoras = array();
   	while ($aseguradora = mysql_fetch_object($resultado)) {
		array_push($aseguradoras, $aseguradora);
	}
	
	include 'views/gestion_documental/novedad_vehiculo_reemplazo_callcenter.html';
		
}

function solicitud_servicio_mensajeria_ok()
{
	
	global $consecutivo,$tipodoc,$Nusuario;
	
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';
	
	$resultado = q("select * from aoacol_aoacars.aseguradora");
	$aseguradoras = array();
   	while ($aseguradora = mysql_fetch_object($resultado)) {
		array_push($aseguradoras, $aseguradora);
	}
	
	include 'views/gestion_documental/solicitud_servicio_mensajeria.html';
		
}

function ruta_capacitacion_ok()
{
	
	global $consecutivo,$tipodoc,$Nusuario;
	
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';
	
	$resultado = q("select * from aoacol_aoacars.aseguradora");
	$aseguradoras = array();
   	while ($aseguradora = mysql_fetch_object($resultado)) {
		array_push($aseguradoras, $aseguradora);
	}
	
	include 'views/gestion_documental/ruta_capacitacion.html';
		
}

function paz_y_salvo_ok()
{
	
	global $consecutivo,$tipodoc,$Nusuario;
	
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';
	
	$resultado = q("select * from aoacol_aoacars.aseguradora");
	$aseguradoras = array();
   	while ($aseguradora = mysql_fetch_object($resultado)) {
		array_push($aseguradoras, $aseguradora);
	}
	
	include 'views/gestion_documental/formato_paz_salvo.html';
		
}

function entrevista_retiro_ok()
{
	
	global $consecutivo,$tipodoc,$Nusuario;
	
	
	if($tipodoc != null )
	{
		$Tipo=qo("select * from tipo_formatoaoa where id=$tipodoc");
		
	}
	else
	{
		include('inc/funciones_.php');
		sesion();	
		$docid=$_POST['docid'];	
		$Nusuario=$_SESSION['Nombre'];
		
		setcookie("iddocumento", $docid);
		setcookie("usuario", $Nusuario);
		
		$Formato=qo("select * from q_documento where codigo= '".$_POST['sigla']."' LIMIT 1");
		$Tipo = $Formato;		
		
		$consecutivo=qo("SELECT max(consecutivo) as max from documentos_guardados where documento = '$docid' ");
		
		if($consecutivo->max == null)
		{
			
			$old_Formato=qo("select * from tipo_formatoaoa where sigla= '".$_POST['sigla']."'");
		
			$old_consecutivo=qo("select max(consecutivo) as max from formatos_aoa where tipo_formato='".$old_Formato->id."'");			
			
			if($old_Formato->id==null or $old_consecutivo->max == null)
			{
				$consecutivo = 1;
			}
			
			else{
				$consecutivo = $old_consecutivo->max +1 ;
			}
		}
		else
		{
			$consecutivo = $consecutivo->max +1 ;
		}
	}
	
	$Nconsecutivo=str_pad($consecutivo,5,'0',STR_PAD_LEFT);
	
	$Ahora=date('Y-m-d H:i:s');$Hoy=date('Y-m-d');

	$Tam_Fuente='10px';
	$Tam_Fuente1='8px';
	
	$resultado = q("select * from aoacol_aoacars.aseguradora");
	$aseguradoras = array();
   	while ($aseguradora = mysql_fetch_object($resultado)) {
		array_push($aseguradoras, $aseguradora);
	}
	
	include 'views/gestion_documental/entrevista_retiro.html';
		
}

function test_document()
{
	//header('Content-Type: text/html; charset=utf-8');
	include 'views/gestion_documental/solicitud_anticipo.html';
}

?>