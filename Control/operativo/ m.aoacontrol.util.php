<?php
error_reporting(E_ALL);
define('ESTILO_MOVIL','inc/css/estilomovilcontrol.css'); // ESTILO MOVILES
$app="m.aoacontrol.util.php";
include('inc/funciones_.php');
include('inc/funciones_movil.php');
if (!empty($Acc) && function_exists($Acc)){eval($Acc.'();');die();}

function toma_imagenes_entrega()
{
	global $app;
	include('inc/gpos.php');
	$Cita=qo("select * from cita_servicio where id=$id");
	$Vehiculo=qo("select * from vehiculo where placa='$Cita->placa' ");
	$Linea=qo("select * from linea_vehiculo where id='$Vehiculo->linea' ");
	$Ahora=date('Y-m-d H:i:s');
	cabecera_movil(TITULO_APLICACION);
	echo "
		<style type='text/css'>
		<!--
			.boton2 {color:red;border-radius:10px;width:90%;border-style:solid;margin:1px;height:80px;}
		-->
		</style>
		<script language='javascript'>
			var actual;
			var cronometro;
			var elcrono;
			var tiempocarga='';

			function cerrar(){window.close();void(null);}
			function guardar_fotos()
			{
				with(document.forma)
				{
					if(frente.value && atras.value && izquierda.value && derecha.value && odometro.value )
					{
						if(confirm('Desea guardar las fotos en este momento?'))
						{
							document.forma.botonguardar.value='GUARDANDO FOTOS';
							document.getElementById('b1').disabled=true;
							document.getElementById('b2').disabled=true;
							document.getElementById('b3').disabled=true;
							document.getElementById('b4').disabled=true;
							document.getElementById('b5').disabled=true;
							document.getElementById('b6').disabled=true;
							document.getElementById('b7').disabled=true;
							document.getElementById('b8').disabled=true;
							document.getElementById('b9').disabled=true;
							document.getElementById('b10').disabled=true;
							document.getElementById('b11').disabled=true;
							document.getElementById('b12').disabled=true;
							document.getElementById('b13').disabled=true;
							cronometro=new Date();
							elcrono=setInterval(actualizar_cronometro,10);
							document.forma.submit();
						}
					}
					else
					{
						var mensaje='';
						if(!frente.value) mensaje+='Falta la foto del frente del vehículo. ';
						if(!atras.value) mensaje+='Falta la foto de atras del vehículo. ';
						if(!izquierda.value) mensaje+='Falta la foto de la izquierda del vehículo. ';
						if(!derecha.value) mensaje+='Falta la foto de la derecha del vehículo. ';
						if(!odometro.value) mensaje+='Falta la foto del odómetro del vehículo. ';
						alert(mensaje);
					}
				}
			}
			function actualizar_cronometro()
			{
				actual=new Date();
				var diferencia=actual-cronometro;
				var cr=new Date();
				cr.setTime(diferencia);
				var cs=cr.getMilliseconds(); //milisegundos
				var cs=cs/10; //paso a centésimas de segundo.
				var cs=Math.round(cs); //redondear las centésimas
				var sg=cr.getSeconds(); //segundos
				var mn=cr.getMinutes(); //minutos
				//poner siempre 2 cifras en los números
				if (cs<10) {cs='0'+cs;}
				if (sg<10) {sg='0'+sg;}
				if (mn<10) {mn='0'+mn;}
				//llevar resultado al visor.
				tiempocarga='00:'+mn+':'+sg;
				document.getElementById('vcrono').innerHTML='Tiempo transcurrido: '+tiempocarga+':'+cs;
				if(tiempocarga>'00:04:00')
				{
					document.getElementById('botonguardar').value='REINTENTAR ENVIO';
					document.getElementById('botonguardar').disabled=false;
					clearInterval(elcrono);
				}
			}

			function parar_cronometro()	{clearInterval(elcrono);guardar_tiempo_entrega();}

			function guardar_tiempo_entrega()
			{
				window.open('$app?Acc=guardar_tiempo_entrega&id=$id&t='+tiempocarga,'Oculto_fotos');
			}
		</script>
		<body>
			<center><img src='img/logo_movil.png'></center>
			<h3 align='center'>TOMA DE IMAGENES DE ENTREGA $Cita->placa</h3>
			<form action='$app' method='post' target='Oculto_fotos' enctype='multipart/form-data' name='forma' id='forma'>
				<div style='display:none'>
				<input type='file' name='frente' id='frente' accept='image/*' capture='camera' onchange=\"document.getElementById('b2').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='atras' id='atras' accept='image/*' capture='camera' onchange=\"document.getElementById('b4').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='izquierda' id='izquierda' accept='image/*' capture='camera' onchange=\"document.getElementById('b3').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='derecha' id='derecha' accept='image/*' capture='camera' onchange=\"document.getElementById('b5').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='odometro' id='odometro' accept='image/*' capture='camera' onchange=\"document.getElementById('b1').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='acta' id='acta' accept='image/*' capture='camera' onchange=\"document.getElementById('b6').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='contrato' id='contrato' accept='image/*' capture='camera' onchange=\"document.getElementById('b7').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='cedulafrente' id='cedulafrente' accept='image/*' capture='camera' onchange=\"document.getElementById('b8').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='cedulareves' id='cedulareves' accept='image/*' capture='camera' onchange=\"document.getElementById('b9').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='licenciafrente' id='licenciafrente' accept='image/*' capture='camera' onchange=\"document.getElementById('b10').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='licenciareves' id='licenciareves' accept='image/*' capture='camera' onchange=\"document.getElementById('b11').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='adicional1' id='adicional1' accept='image/*' capture='camera' onchange=\"document.getElementById('b12').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='adicional2' id='adicional2' accept='image/*' capture='camera' onchange=\"document.getElementById('b13').style.backgroundImage='url(img/fotook.png)';\">
				</div>
				<input type='hidden' name='MAX_FILE_SIZE' value='40000000'>
				<input type='hidden' name='id' id='id' value='$id'>
				<center>
				<input type='button' id='b1' value='ODOMETRO' 	onclick='document.forma.odometro.click();' style='height:100px;color:#DB6C00;font-size:40px;background-image:url(img/odometro.png);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b2' value='FRENTE' 		onclick='document.forma.frente.click();' style='height:200px;color:#DB6C00;font-size:40px;background-image:url($Linea->delante_f);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b3' value='IZQUIERDA' 	onclick='document.forma.izquierda.click();' style='height:100px;color:#DB6C00;font-size:40px;background-image:url($Linea->izquierda_f);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b4' value='ATRAS' 			onclick='document.forma.atras.click();' style='height:200px;color:#DB6C00;font-size:40px;background-image:url($Linea->atras_f);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b5' value='DERECHA' 	onclick='document.forma.derecha.click();' style='height:100px;color:#DB6C00;font-size:40px;background-image:url($Linea->derecha_f);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b6' value='ACTA\nENTREGA' 	onclick='document.forma.acta.click();' style='height:300px;color:#DB6C00;font-size:40px;background-image:url(img/acta_mini.jpg);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b7' value='CONTRATO' 	onclick='document.forma.contrato.click();' style='height:300px;color:#DB6C00;font-size:40px;background-image:url(img/contrato_mini.png);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b8' value='CEDULA - FRENTE' 	onclick='document.forma.cedulafrente.click();' style='height:300px;color:#DB6C00;font-size:36px;background-image:url(img/cedula.png);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b9' value='CEDULA - REVERSO' 	onclick='document.forma.cedulareves.click();' style='height:300px;color:#DB6C00;font-size:36px;background-image:url(img/cedula.png);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b10' value='LICENCIA - FRENTE' 	onclick='document.forma.licenciafrente.click();' style='height:300px;color:#DB6C00;font-size:36px;background-image:url(img/licencia.png);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b11' value='LICENCIA - REVERSO' 	onclick='document.forma.licenciareves.click();' style='height:300px;color:#DB6C00;font-size:36px;background-image:url(img/licencia.png);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b12' value='ADICIONAL 1' 	onclick='document.forma.adicional1.click();' style='height:100px;color:#DB6C00;font-size:50px;background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b13' value='ADICIONAL 2' 	onclick='document.forma.adicional2.click();' style='height:100px;color:#DB6C00;font-size:50px;background-size:contain;background-repeat:no-repeat;background-position:center;'><br>

				<input type='button' name='botonguardar' id='botonguardar' class='button' value='GUARDAR FOTOS' onclick='guardar_fotos();'>
				<span id='vcrono' style='font-size:20px;color:blue;'></span>
				</center>

				<br />
				<input type='hidden' name='Acc' id='Acc' value='toma_imagenes_entrega_ok'>
				<input type='hidden' name='idcita' value='$id'><input type='hidden' name='Fecreal' id='Fecreal' value='$Ahora'>
			</form>
			<input type='button' class='button' value='REGRESAR' onclick=\"window.close();void(null);\">
			<iframe name='Oculto_fotos' id='Oculto_fotos' style='display:block' width='100%' height='300'></iframe>
		  </body>";
}

function toma_imagenes_entrega_ok()
{
	global $app;
	include('inc/gpos.php');
	$Cita=qo("select id,siniestro,placa from cita_servicio where id=$id");
	$idSiniestro=$Cita->siniestro;
	$numsiniestro=qo1("select numero from siniestro where id=$idSiniestro");
	echo "<body>Siniestro $idSiniestro";

	$Ruta=directorio_imagen('siniestro',$idSiniestro);
	// frente
	$name = $_FILES["frente"]["name"]; $tmp_name = $_FILES["frente"]["tmp_name"];echo "<br>$name";
	if($name)
	{
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['frente']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set fotovh1_f='$File_destino' where id=$idSiniestro");
		}
	}

	// atras
	$name = $_FILES["atras"]["name"]; $tmp_name = $_FILES["atras"]["tmp_name"];echo "<br>$name";
	if($name)
	{
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['atras']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set fotovh4_f='$File_destino' where id=$idSiniestro");
		}
	}

	// izquierda
	$name = $_FILES["izquierda"]["name"]; $tmp_name = $_FILES["izquierda"]["tmp_name"];echo "<br>$name";
	if($name)
	{
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['izquierda']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set fotovh2_f='$File_destino' where id=$idSiniestro");
		}
	}

	// derecha
	$name = $_FILES["derecha"]["name"]; $tmp_name = $_FILES["derecha"]["tmp_name"];echo "<br>$name";
	if($name)
	{
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['derecha']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set fotovh3_f='$File_destino' where id=$idSiniestro");
		}
	}

	// odometro
	$name = $_FILES["odometro"]["name"]; $tmp_name = $_FILES["odometro"]["tmp_name"];echo "<br>$name";
	if($name)
	{
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['odometro']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set img_odo_salida_f='$File_destino' where id=$idSiniestro");
		}
	}

	// Acta de entrega
	$name = $_FILES["acta"]["name"]; $tmp_name = $_FILES["acta"]["tmp_name"];
	if($name)
	{
		echo "<br>$name";
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['acta']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set img_inv_salida_f='$File_destino' where id=$idSiniestro");
		}
	}

	// Contrato
	$name = $_FILES["contrato"]["name"]; $tmp_name = $_FILES["contrato"]["tmp_name"];echo "<br>$name";
	if($name)
	{
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['contrato']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set img_contrato_f='$File_destino' where id=$idSiniestro");
		}
	}

	// Cedula frente
	$name = $_FILES["cedulafrente"]["name"]; $tmp_name = $_FILES["cedulafrente"]["tmp_name"];
	if($name)
	{
		echo "<br>$name";
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['cedulafrente']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set img_cedula_f='$File_destino' where id=$idSiniestro");
		}
	}

	// Cedula reverso
	$name = $_FILES["cedulareves"]["name"]; $tmp_name = $_FILES["cedulareves"]["tmp_name"];
	if($name)
	{
		echo "<br>$name";
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['cedulareves']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set img_pase_f='$File_destino' where id=$idSiniestro");
		}
	}

	// Licencia frente
	$name = $_FILES["licenciafrente"]["name"]; $tmp_name = $_FILES["licenciafrente"]["tmp_name"];
	if($name)
	{
		echo "<br>$name";
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['licenciafrente']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set adicional1_f='$File_destino' where id=$idSiniestro");
		}
	}

	// Licencia reverso
	$name = $_FILES["licenciareves"]["name"]; $tmp_name = $_FILES["licenciareves"]["tmp_name"];
	if($name)
	{
		echo "<br>$name";
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['licenciareves']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set adicional2_f='$File_destino' where id=$idSiniestro");
		}
	}

	// Imagen Adicional 1
	$name = $_FILES["adicional1"]["name"]; $tmp_name = $_FILES["adicional1"]["tmp_name"];
	if($name)
	{
		echo "<br>$name";
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['adicional1']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set eadicional1_f='$File_destino' where id=$idSiniestro");
		}
	}

	// Imagen Adicional 2
	$name = $_FILES["adicional2"]["name"]; $tmp_name = $_FILES["adicional2"]["tmp_name"];
	if($name)
	{
		echo "<br>$name";
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='ENTREGA Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['adicional2']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set eadicional2_f='$File_destino' where id=$idSiniestro");
		}
	}

	echo "<body ><script language='javascript'>parent.parar_cronometro();</script></body>";
}

function guardar_tiempo_entrega()
{
	include('inc/gpos.php');
	q("update cita_servicio set entrega_fase2=1,tiempo_carga_entrega='$t' where id=$id");
	echo "<body><script language='javascript'>alert('Fotos cargadas Satisfactoriamente');parent.cerrar();</script></body>";
}

function toma_imagenes_devolucion()
{
	global $app;
	include('inc/gpos.php');
	$Cita=qo("select * from cita_servicio where id=$id");
	$Vehiculo=qo("select * from vehiculo where placa='$Cita->placa' ");
	$Linea=qo("select * from linea_vehiculo where id='$Vehiculo->linea' ");
	$Ahora=date('Y-m-d H:i:s');
	cabecera_movil(TITULO_APLICACION);
	echo "
		<style type='text/css'>
		<!--
			.boton2 {color:red;border-radius:10px;width:90%;border-style:solid;margin:1px;height:80px;}
		-->
		</style>
		<script language='javascript'>
			function cerrar(){window.close();void(null);}
			function guardar_fotos()
			{
				with(document.forma)
				{
					if(frente.value && atras.value && izquierda.value && derecha.value && odometro.value)
					{
						if(confirm('Desea guardar las fotos en este momento?'))
						{
							document.forma.botonguardar.value='GUARDANDO FOTOS';
							//document.forma.botonguardar.disabled=true;
							document.getElementById('b1').disabled=true;
							document.getElementById('b2').disabled=true;
							document.getElementById('b3').disabled=true;
							document.getElementById('b4').disabled=true;
							document.getElementById('b5').disabled=true;
							document.getElementById('b6').disabled=true;
							document.getElementById('b7').disabled=true;
							document.getElementById('b8').disabled=true;
							document.getElementById('b9').disabled=true;
							document.getElementById('b10').disabled=true;
							cronometro=new Date();
							elcrono=setInterval(actualizar_cronometro,10);
							document.forma.submit();
						}
					}
					else
					{
						var mensaje='';
						if(!frente.value) mensaje+='Falta la foto del frente del vehículo. ';
						if(!atras.value) mensaje+='Falta la foto de atras del vehículo. ';
						if(!izquierda.value) mensaje+='Falta la foto de la izquierda del vehículo. ';
						if(!derecha.value) mensaje+='Falta la foto de la derecha del vehículo. ';
						if(!odometro.value) mensaje+='Falta la foto del odómetro del vehículo. ';
						alert(mensaje);
					}
				}
			}
			function actualizar_cronometro()
			{
				actual=new Date();
				var diferencia=actual-cronometro;
				var cr=new Date();
				cr.setTime(diferencia);
				var cs=cr.getMilliseconds(); //milisegundos
				var cs=cs/10; //paso a centésimas de segundo.
				var cs=Math.round(cs); //redondear las centésimas
				var sg=cr.getSeconds(); //segundos
				var mn=cr.getMinutes(); //minutos
				//poner siempre 2 cifras en los números
				if (cs<10) {cs='0'+cs;}
				if (sg<10) {sg='0'+sg;}
				if (mn<10) {mn='0'+mn;}
				//llevar resultado al visor.
				tiempocarga='00:'+mn+':'+sg;
				document.getElementById('vcrono').innerHTML='Tiempo transcurrido: '+tiempocarga+':'+cs;
				if(tiempocarga>'00:04:00')
				{
					document.getElementById('botonguardar').value='REINTENTAR ENVIO';
					document.getElementById('botonguardar').disabled=false;
				}
			}

			function parar_cronometro()	{clearInterval(elcrono);guardar_tiempo_devolucion();}

			function guardar_tiempo_devolucion()
			{
				window.open('$app?Acc=guardar_tiempo_devolucion&id=$id&t='+tiempocarga,'Oculto_fotos');
			}
		</script>
		<body >
			<center><img src='img/logo_movil.png'></center>
			<h3 align='center'>TOMA DE IMAGENES DE DEVOLUCION $Cita->placa</h3>
			<form action='$app' method='post' target='Oculto_fotos' enctype='multipart/form-data' name='forma' id='forma'>
				<div style='display:none'>
				<input type='file' name='frente' id='frente' accept='image/*' capture='camera' onchange=\"document.getElementById('b2').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='atras' id='atras' accept='image/*' capture='camera' onchange=\"document.getElementById('b4').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='izquierda' id='izquierda' accept='image/*' capture='camera' onchange=\"document.getElementById('b3').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='derecha' id='derecha' accept='image/*' capture='camera' onchange=\"document.getElementById('b5').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='odometro' id='odometro' accept='image/*' capture='camera' onchange=\"document.getElementById('b1').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='acta' id='acta' accept='image/*' capture='camera' onchange=\"document.getElementById('b6').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='documentoadicional1' id='documentoadicional1' accept='image/*' capture='camera' onchange=\"document.getElementById('b7').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='documentoadicional2' id='documentoadicional2' accept='image/*' capture='camera' onchange=\"document.getElementById('b8').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='vehiculoadicional1' id='vehiculoadicional1' accept='image/*' capture='camera' onchange=\"document.getElementById('b9').style.backgroundImage='url(img/fotook.png)';\">
				<input type='file' name='vehiculoadicional2' id='vehiculoadicional2' accept='image/*' capture='camera' onchange=\"document.getElementById('b10').style.backgroundImage='url(img/fotook.png)';\">
				</div>
				<input type='hidden' name='MAX_FILE_SIZE' value='40000000'>
				<input type='hidden' name='id' id='id' value='$id'>
				<center>
				<input type='button' id='b1' value='ODOMETRO' 	onclick='document.forma.odometro.click();' style='height:100px;color:#DB6C00;font-size:40px;background-image:url(img/odometro.png);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b2' value='FRENTE' 		onclick='document.forma.frente.click();' style='height:200px;color:#DB6C00;font-size:40px;background-image:url($Linea->delante_f);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b3' value='IZQUIERDA' 	onclick='document.forma.izquierda.click();' style='height:100px;color:#DB6C00;font-size:40px;background-image:url($Linea->izquierda_f);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b4' value='ATRAS' 			onclick='document.forma.atras.click();' style='height:200px;color:#DB6C00;font-size:40px;background-image:url($Linea->atras_f);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b5' value='DERECHA' 	onclick='document.forma.derecha.click();' style='height:100px;color:#DB6C00;font-size:40px;background-image:url($Linea->derecha_f);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b6' value='ACTA\nDEVOLUCION' 	onclick='document.forma.acta.click();' style='height:300px;color:#DB6C00;font-size:40px;background-image:url(img/acta_mini.jpg);background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b7' value='DOCUMENTO\nADICIONAL 1' 	onclick='document.forma.documentoadicional1.click();' style='height:100px;color:#DB6C00;font-size:36px;background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b8' value='DOCUMENTO\nADICIONAL 2' 	onclick='document.forma.documentoadicional2.click();' style='height:100px;color:#DB6C00;font-size:36px;background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b9' value='IMAGEN DE VEHICULO\nADICIONAL 1' 	onclick='document.forma.vehiculoadicional1.click();' style='height:100px;color:#DB6C00;font-size:30px;background-size:contain;background-repeat:no-repeat;background-position:center;'><br>
				<input type='button' id='b10' value='IMAGEN DE VEHICULO\nADICIONAL 2' 	onclick='document.forma.vehiculoadicional2.click();' style='height:100px;color:#DB6C00;font-size:30px;background-size:contain;background-repeat:no-repeat;background-position:center;'><br>

				<input type='button' name='botonguardar' id='botonguardar' class='button' value='GUARDAR FOTOS' onclick='guardar_fotos();'>
				<span id='vcrono' style='font-size:20px;color:blue;'></span>
				</center>

				<br />
				<input type='hidden' name='Acc' id='Acc' value='toma_imagenes_devolucion_ok'>
				<input type='hidden' name='idcita' value='$id'><input type='hidden' name='Fecreal' id='Fecreal' value='$Ahora'>
			</form>
			<input type='button' class='button' value='REGRESAR' onclick=\"window.close();void(null);\">
			<iframe name='Oculto_fotos' id='Oculto_fotos' style='display:none' width='100%' height='300'></iframe>
		  </body>";
}

function toma_imagenes_devolucion_ok()
{
	global $app;
	include('inc/gpos.php');
	$Cita=qo("select id,siniestro,placa from cita_servicio where id=$id");
	$idSiniestro=$Cita->siniestro;
	$numsiniestro=qo1("select numero from siniestro where id=$idSiniestro");

	echo "<body>Siniestro $idSiniestro";
	$Ruta=directorio_imagen('siniestro',$idSiniestro);
	// frente
	$name = $_FILES["frente"]["name"]; $tmp_name = $_FILES["frente"]["tmp_name"];echo "<br>$name";
	$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
	if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
	else
	{
		$Momento='DEVOLUCION Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['frente']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
		list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
		$im = imagecreatefromjpeg($File_destino);
		imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
		imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
		chmod($File_destino,0777);
		unlink($File_destino);
		imagejpeg($im,$File_destino,100);
		q("update siniestro set fotovh5_f='$File_destino' where id=$idSiniestro");
	}

	// atras
	$name = $_FILES["atras"]["name"]; $tmp_name = $_FILES["atras"]["tmp_name"];echo "<br>$name";
	$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
	if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
	else
	{
		$Momento='DEVOLUCION Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['atras']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
		list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
		$im = imagecreatefromjpeg($File_destino);
		imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
		imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
		chmod($File_destino,0777);
		unlink($File_destino);
		imagejpeg($im,$File_destino,100);
		q("update siniestro set fotovh8_f='$File_destino' where id=$idSiniestro");
	}

	// izquierda
	$name = $_FILES["izquierda"]["name"]; $tmp_name = $_FILES["izquierda"]["tmp_name"];echo "<br>$name";
	$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
	if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
	else
	{
		$Momento='DEVOLUCION Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['izquierda']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
		list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
		$im = imagecreatefromjpeg($File_destino);
		imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
		imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
		chmod($File_destino,0777);
		unlink($File_destino);
		imagejpeg($im,$File_destino,100);
		q("update siniestro set fotovh6_f='$File_destino' where id=$idSiniestro");
	}

	// derecha
	$name = $_FILES["derecha"]["name"]; $tmp_name = $_FILES["derecha"]["tmp_name"];echo "<br>$name";
	$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
	if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
	else
	{
		$Momento='DEVOLUCION Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['derecha']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
		list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
		$im = imagecreatefromjpeg($File_destino);
		imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
		imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
		chmod($File_destino,0777);
		unlink($File_destino);
		imagejpeg($im,$File_destino,100);
		q("update siniestro set fotovh7_f='$File_destino' where id=$idSiniestro");
	}

	// odometro
	$name = $_FILES["odometro"]["name"]; $tmp_name = $_FILES["odometro"]["tmp_name"];echo "<br>$name";
	$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
	if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
	else
	{
		$Momento='DEVOLUCION Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['odometro']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
		list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
		$im = imagecreatefromjpeg($File_destino);
		imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
		imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
		chmod($File_destino,0777);
		unlink($File_destino);
		imagejpeg($im,$File_destino,100);
		q("update siniestro set img_odo_entrada_f='$File_destino' where id=$idSiniestro");
	}

	// Acta de devolucion
	$name = $_FILES["acta"]["name"]; $tmp_name = $_FILES["acta"]["tmp_name"];
	if($name)
	{
		echo "<br>$name";
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='DEVOLUCION Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['acta']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set img_inv_entrada_f='$File_destino' where id=$idSiniestro");
		}
	}

	// Documento Adicional 1
	$name = $_FILES["documentoadicional1"]["name"]; $tmp_name = $_FILES["documentoadicional1"]["tmp_name"];
	if($name)
	{
		echo "<br>$name";
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='DEVOLUCION Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['documentoadicional1']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set fotovh9_f='$File_destino' where id=$idSiniestro");
		}
	}

	// Documento Adicional 2
	$name = $_FILES["documentoadicional2"]["name"]; $tmp_name = $_FILES["documentoadicional2"]["tmp_name"];
	if($name)
	{
		echo "<br>$name";
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='DEVOLUCION Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['documentoadicional2']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set fotovh10_f='$File_destino' where id=$idSiniestro");
		}
	}

	// Imagen Vehiculo Adicional 1
	$name = $_FILES["vehiculoadicional1"]["name"]; $tmp_name = $_FILES["vehiculoadicional1"]["tmp_name"];
	if($name)
	{
		echo "<br>$name";
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='DEVOLUCION Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['vehiculoadicional1']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set dadicional3_f='$File_destino' where id=$idSiniestro");
		}
	}

	// Imagen Vehiculo Adicional 2
	$name = $_FILES["vehiculoadicional2"]["name"]; $tmp_name = $_FILES["vehiculoadicional2"]["tmp_name"];
	if($name)
	{
		echo "<br>$name";
		$File_destino=$Ruta.strtolower(str_replace(' ','_',$name));if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die("Error copiando el archivo del directorio temporal al directorio destino $File_destino"); }
		else
		{
			$Momento='DEVOLUCION Tomada: '.$Fecreal.' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES['vehiculoadicional2']['tmp_name'])).' Placa: '.$Cita->placa.' Siniestro: '.$numsiniestro;
			list($ancho, $alto, $tipo_imagen) = getimagesize($File_destino);
			$im = imagecreatefromjpeg($File_destino);
			imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
			imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
			chmod($File_destino,0777);
			unlink($File_destino);
			imagejpeg($im,$File_destino,100);
			q("update siniestro set dadicional4_f='$File_destino' where id=$idSiniestro");
		}
	}

	echo "<body ><script language='javascript'>parent.parar_cronometro();</script></body>";
}

function guardar_tiempo_devolucion()
{
	include('inc/gpos.php');
	q("update cita_servicio set devolucion_fase3=1,tiempo_carga_devol='$t' where id=$id");
	echo "<body><script language='javascript'>alert('Fotos cargadas Satisfactoriamente');parent.cerrar();</script></body>";
}
?>