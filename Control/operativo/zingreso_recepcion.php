<?php

/**
	 *  PANTALLA DE INGRESO A RECEPCION
 *
 * @version $Id$
 * @copyright 2011
 */
include('inc/funciones_.php');
//sesion();
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');die();}
html('AOA INGRESO EN RECEPCION');
$Baseurl=strlen('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/');
echo "
	<script language='javascript'>
		var IDN=0;
		var IMAGEN='';

		function validacion1()
		{

			with(document.forma)
			{
				if(!alltrim(nombre.value)) 	{alert('Debe escribir el nombre del visitante'); nombre.style.backgroundColor='ffff44'; return false;}
				if(!alltrim(apellido.value)) 	{alert('Debe escribir el apellido del visitante'); apellido.style.backgroundColor='ffff44'; return false;}
				if(!alltrim(identificacion.value)) 	{alert('Debe escribir el número de identificación del visitante'); identificacion.style.backgroundColor='ffff44'; return false;}
				if(!alltrim(descripcion.value)) {alert('Debe escribir el motivo de la visita'); descripcion.style.backgroundColor='ffff44'; return false;}   ";
	if(!$idcita) echo "if(!visitado.value) {alert('Debe seleccionar el funcionario a quien visita'); visitado.style.backgroundColor='ffff44'; return false;}  ";
	echo "var Nom=nombre.value;
				var Ape=apellido.value;
				var Ide=identificacion.value;
				var Desc=descripcion.value; ";
	if(!$idcita) echo "var Vis=visitado.value; "; else echo "var Vis=0;";
	echo "var Cit=idcita.value
				window.open('zingreso_recepcion.php?Acc=grabar_informacion_recepcion&nombre='+Nom+'&apellido='+Ape+'&identificacion='+Ide+'&descripcion='+Desc+'&visitado='+Vis+'&idcita='+Cit,'Oculto_recepcion');
			}
		}
		function asignar_imagen(dato)
		{
			window.open('zingreso_recepcion.php?Acc=asignar_imagen&id='+IDN+'&imagen='+dato,'Oculto_recepcion');
		}

		function recargar()
		{
			window.location.reload();
		}
		
	</script>
	<body onload='centrar(600,600);'>
		<form action='zingreso_recepcion.php' method='post' target='Oculto_recepcion' name='forma' id='forma'>
			<font style='font-size:16px'>
			<h3>INFORMACION BASICA DEL ".($idcita?"ASEGURADO / ACOMPAÑANTE":"VISITANTE")."</H3>
			Nombre(s): <input type='text' name='nombre' value='' size=60 maxlength=60 style='text-transform:uppercase' onblur='this.value=this.value.toUpperCase();'><br />
			Apellido(s): <input type='text' name='apellido' value='' size=60 maxlength=60 style='text-transform:uppercase' onblur='this.value=this.value.toUpperCase();'><br>
			Identificacion: <input type='text' class='numero' name='identificacion' id='identificacion' value='' size=15><br>
			Motivo de la visita: <textarea name='descripcion' style='font-size:12px;text-transform:uppercase' cols=50 rows=2 onblur='this.value=this.value.toUpperCase();'>$m</textarea><br>";
	if(!$idcita) echo "Visita A: ".menu1("visitado"," select id,concat(nombre1,' ',nombre2,' ',apellido1,' ',apellido2)
							as nombre from aoacol_administra.empleado order by nombre ",0,1)."<br />";
	echo "<input type='button' id='grabar' value='Guardar Información Básica' onclick='validacion1()' style='font-size:14px;fontweight:bold'>
			<input type='hidden' name='idcita' value='$idcita'>
			</font>
		</form>
	<iframe name='Oculto_recepcion' id='Oculto_recepcion' style='visibility:hidden' width=1 height=1></iframe>
	<style>
        .camara_digital{
			display: none;
		}	
	</style>
	<table align='center' id='table_foto' class='camara_digital'>
	<tr><td valign=top>
	<!-- inicio de las rutinas de la toma de imagen -->
	<head>
	<!--
		Tomar una fotografía y guardarla en un archivo v3
	    @date 2018-10-22
	    @author parzibyte
	    @web parzibyte.me/blog
	-->
	<meta charset='UTF-8'>
	<meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'>
	<title>Tomar foto con Javascript y PHP v3.0</title>
	<style>
		@media only screen and (max-width: 700px) {
			video {
				max-width: 100%;
			}
		}
	</style>
</head>
 
<body>
	<h1>Selecciona un dispositivo para toma de foto</h1>
	<div>
	<input type='hidden' id='idTablaRecepcion'>
		<select name='listaDeDispositivos' id='listaDeDispositivos'></select>
		<button id='boton'>Tomar foto</button>
		
		<p id='estado'></p>
	</div>
	<br>
	<video muted='muted' id='video'></video>
	<canvas id='canvas' style='display: none;'></canvas>
</body>
<script src='inc/js/webcam.js'></script>
	
	
	
	
	</td></tr></table>
	</body>";

function grabar_informacion_recepcion() // graba la información en la tabla de ingresos de recepcion en la base de datos administrativa
{
	global $apellido,$nombre,$descripcion,$visitado,$identificacion,$idcita;
	$NUSUARIO=$_SESSION['Nombre'];
	$Ahora=date('Y-m-d H:i:s');
	if($idcita)
	{
		$idsiniestro=qo1("select siniestro from cita_servicio where id=$idcita"); // trae informacion de la cita
	}
	$IDN=q("insert into aoacol_administra.ingreso_recepcion (apellido, nombre,descripcion,visitado,identificacion,fecha,registrado_por,cita,siniestro) values
	 ('$apellido','$nombre',\"$descripcion\",$visitado,'$identificacion','$Ahora','$NUSUARIO','$idcita','$idsiniestro') ");
	echo "<body>
	<script language='javascript'>parent.document.getElementById('bt2')
			//parent.document.getElementById('bt3')
			parent.document.getElementById('grabar')
			parent.document.getElementById('idTablaRecepcion').value = '$IDN'
			var table_foto = parent.document.getElementById('table_foto')  
			table_foto.style.display = 'block'
			parent.IDN='$IDN'
			
	</script>";
	
	
}


function carga_imagen() // carga la imagen de la foto en la recepcion
{
	
	$filename = 'planos/'.date('YmdHis') . '.jpg';
	$result = file_put_contents( $filename, file_get_contents('php://input') );
	if (!$result)
	{
		print "ERROR: Failed to write data to $filename, check permissions";
		exit();
	}
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $filename;
	
	print "$url\n";
}

function asignar_imagen() // asigna la imagen y la ubica en el directorio correspondiente a la tabla de recepcion
{
	global $id,$imagen;
	$Id=$id;
	$Camino='../../Administrativo/';
	$directorio='ingreso_recepcion';
	$Tamano=600;
	$name=str_replace('planos/','',$imagen);
	$tmp_name=$imagen;
//	global $Foto,$T,$Id,$C,$Tamano,$directorio;

	if(!is_dir($Camino.$directorio)) { mkdir($Camino.$directorio); 	chmod($Camino.$directorio, 0777); }
	$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
	if(!is_dir($Camino.$directorio.'/'.$Subdirectorio)) { mkdir($Camino.$directorio.'/'.$Subdirectorio); chmod($Camino.$directorio.'/'.$Subdirectorio, 0777);}
	if(!is_dir($Camino.$directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($Camino.$directorio.'/'.$Subdirectorio.'/'.$Id); chmod($Camino.$directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}
//	$Caracteristicas_imagen = getimagesize($imagen);
//	if($Caracteristicas_imagen[1]>$Tamano)	picresize($tmp_name,$Tamano,'jpg');
	$File_destino=$Camino.$directorio.'/'.$Subdirectorio.'/'.$Id.'/'.strtolower(str_replace(' ','_',$name));
	$i = 1;
	// pick unused file name
	while (file_exists($File_destino))
	{
		$name=ereg_replace('(.*)(\.[a-zA-Z]+)$', '\1_'.$i.'\2', $name);
		$File_destino = $Camino.$directorio.'/'.$Subdirectorio.'/'.$Id.'/'.strtolower(str_replace(' ','_',$name));
		$i++;
	}
	if(is_file($File_destino)) @unlink($File_destino);
	if (!@copy($tmp_name, $File_destino)) { die('error en copy file'); }
	@unlink($tmp_name);
	// Guardamos todo en la base de datos
	require('inc/link.php');
	$Destino_final=str_replace($Camino,'',$File_destino);
	// actualiza la tabla 
	mysql_query("update aoacol_administra.ingreso_recepcion set foto_f='$Destino_final' where id=$Id ",$LINK);
	// graba la bitacora
	mysql_query("insert into aoacol_administra.app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
		values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "',
		'" . date('s') . "','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','ingreso_recepcion','M','$Id','" . $_SERVER['REMOTE_ADDR'] . "','Modifica:foto_f ingresa imagen')",$LINK);
	mysql_close($LINK);
	echo "<body><script language='javascript'>alert('Imagen Grabada Satisfactoriamente');parent.recargar()</script>";
}

function consulta_ingreso() // muestra las fotos de recepcion de un siniestro en pantalla
{
	global $id;  // id= id del siniestro
	html('CONSULTA IMAGENES DE INGRESO RECEPCION');
	echo "<body>";
	if($S=qo("select *,t_aseguradora(aseguradora) as naseg,t_ciudad(ciudad) as nciu from siniestro where id=$id"))
	{
		echo "<h3>Visitantes del siniestro Numero $S->numero [$S->naseg] - $S->nciu</h3>";
		if($Imagenes=q("select * from aoacol_administra.ingreso_recepcion where siniestro=$id"))
		{
			echo "<table align='center'>";
			while($I=mysql_fetch_object($Imagenes))
			{
				echo "<tr ><td colspan=3><img src='../../Administrativo/$I->foto_f' border='3'></td></tr>
						<tr ><td>Fecha: $I->fecha</td><td >$I->nombre $I->apellido</td><td >Id: ".coma_format($I->identificacion)."</td></tr>";
			}
			echo "</table></body>";
		}
		else
		{
			echo "<h3>Este siniestro no tiene registro de visitantes</h3></body>";
		}
	}
	else
	{
		echo "<script language='javascript'>centrar(10,10);alert('No encuentro la información del siniestro $id');window.close();void(null();</script></body>";
	}
	echo "</html>";
}














?>