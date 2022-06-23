<?php
include('inc/funciones_.php');
// VARIABLE GENERAL DE CONTROL DE ERRORES



$Errores='';
/*
Codigos de error:
0  todo bien
1. Usuario invalido
2. Cita no encontrada
3. Estado de cita invalido.   Pudo haber sido cancelada o ya no está en estado programada tanto la entrega como la devolucion

20. Fallo de carga de archivo
*/
//VALIDACION DEL USUARIO 
$User=$_POST['usuario'];
$Pwd=e($_POST['password']);
$Tipo_evento=$_POST['tipoServicio'];
$idCita=$_POST['idCita'];
$Placa=$_POST['placa'];

$fd = fopen("planos/log$idCita.txt", "w");
ob_start();
print "Fecha y Hora: ".date('Y-m-d H:i:s')." FILES:";
print_r($_FILES);
foreach ($_FILES as $id => $details)
	print "$id ".$details['name'];
print "POST:";
print_r($_POST);
foreach($_POST as $variable => $dato)
	print "$variable = $dato ";
print "GET:";
print_r($_GET);
foreach($_GET as $variable => $dato)
	print "$variable = $dato ";
fwrite($fd, ob_get_contents());
ob_end_clean();
fclose($fd);

if($Usuario=qo("select * from operario where usuario='$User' and clave='$Pwd' "))
{
	// SE BUSCA SI LA CITA ESTA EN EL ESTADO CORRECTO
	if($Cita=qo("select  * from cita_servicio where id=$idCita"))
	{
		// Busca si el estado de entrega coincide con el tipo de evento o si el estado de devolucion coincide con el tipo de evento si es devolucion tambien, de lo contrario no deja
		// continuar porque el estado de la cita no coincide
		$Estado_cita=false;
		if($Tipo_evento=='Entrega' && $Cita->estado=='P' /* Programada*/) $Estado_cita=true;
		elseif($Tipo_evento=='Devolucion' && $Cita->estadod=='P' /* Programada*/) $Estado_cita=true;
		if($Estado_cita)
		{
			///  VERIFICACION DE EXISTENCIA DEL SUBDIRECTORIO DONDE SE DEBEN GUARDAR LAS IMAGENES
			// preparacion de las variables
			$Id=$Cita->siniestro;
			$directorio='siniestro';
			// fin preparacion de las variables
			if(!is_dir($directorio)) { mkdir($directorio); chmod($directorio, 0777); }
			$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
			if(!is_dir($directorio.'/'.$Subdirectorio)) { mkdir($directorio.'/'.$Subdirectorio); chmod($directorio.'/'.$Subdirectorio, 0777);}
			if(!is_dir($directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($directorio.'/'.$Subdirectorio.'/'.$Id); chmod($directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}
			// fin verificacion del subdirectorio de carga.
			$DirectorioCarga = $directorio.'/'.$Subdirectorio.'/'.$Id.'/';
			$Cargado=true;
			$Actualizacion_siniestro=($Tipo_evento=='Entrega'?"estado=7,":"estado=8,");
			$Actualizacion_cita=($Tipo_evento=='Entrega'?"estado='C', ":"estadod='C', ");
			$DSiniestro=qo("Select * from siniestro where id=$Cita->siniestro");
			$AFecreal=explode('-',$_POST['fechaTramite']);
			$Fecreal=$AFecreal[0].'-'.str_pad(($AFecreal[1]+1),2,'0',STR_PAD_LEFT).'-'.str_pad($AFecreal[2],2,'0',STR_PAD_LEFT);
			foreach ($_FILES as $archivo => $DetalleArchivo)
			{
				$Identificador=basename($DetalleArchivo['name']);
				$ArchivoDestino = $DirectorioCarga . l($Tipo_evento,1).$idCita.'_'.$Identificador;
				$Momento=$Tipo_evento.' Tomada: '.$Fecreal.' '.$_POST['horaTramite'].
								' Cargada: '.date('Y-m-d h:i:s A',filemtime($_FILES[$archivo]['tmp_name'])).' Placa: '.$Placa.
								' Siniestro: '.$_POST['numSiniestro'];
				// busca si el archivo existe previamente, esto puede pasar al intentar subir por segunda oportunidad el mismo evento.
				if(is_file($ArchivoDestino)) { chmod($ArchivoDestino,0777); unlink($ArchivoDestino);}
				if (move_uploaded_file($DetalleArchivo['tmp_name'], $ArchivoDestino))
				{
					list($ancho, $alto, $tipo_imagen) = getimagesize($ArchivoDestino); 
					$im = imagecreatefromjpeg($ArchivoDestino);
					imagefilledrectangle (  $im,0, 0, $ancho, 30, imagecolorallocate($im, 255, 255, 255));
					imagettftext($im, 20, 0, 11, 21, imagecolorallocate($im, 0, 0, 0), 'inc/captcha/fonts/arial.ttf', $Momento);
					chmod($ArchivoDestino,0777);
					unlink($ArchivoDestino);
					imagejpeg($im,$ArchivoDestino,100);
					if($Tipo_evento=='Entrega')
					{
						if($Identificador=='Odometro.jpg' && !$DSiniestro->img_odo_salida_f) $Actualizacion_siniestro.=" img_odo_salida_f='$ArchivoDestino',";
						if($Identificador=='ActaEntrega.jpg' && !$DSiniestro->img_inv_salida_f) $Actualizacion_siniestro.= " img_inv_salida_f='$ArchivoDestino',";
						if($Identificador=='FrenteVehiculo.jpg' && !$DSiniestro->fotovh1_f) $Actualizacion_siniestro.= " fotovh1_f='$ArchivoDestino',";
						if($Identificador=='LateralIzquierdo.jpg' && !$DSiniestro->fotovh2_f) $Actualizacion_siniestro.= " fotovh2_f='$ArchivoDestino',";
						if($Identificador=='LateralDerecho.jpg' && !$DSiniestro->fotovh3_f) $Actualizacion_siniestro.= " fotovh3_f='$ArchivoDestino',";
						if($Identificador=='AtrasVehiculo.jpg' && !$DSiniestro->fotovh4_f) $Actualizacion_siniestro.= " fotovh4_f='$ArchivoDestino',";
						if($Identificador=='Contrato.jpg' && !$DSiniestro->img_contrato_f) $Actualizacion_siniestro.= " img_contrato_f='$ArchivoDestino',";
						if($Identificador=='CedulaFrente.jpg' && !$DSiniestro->img_cedula_f) $Actualizacion_siniestro.= " img_cedula_f='$ArchivoDestino',";
						if($Identificador=='CedulaRespaldo.jpg' && !$DSiniestro->img_pase_f) $Actualizacion_siniestro.= " img_pase_f='$ArchivoDestino',";
						if($Identificador=='LicenciaFrente.jpg' && !$DSiniestro->adicional1_f) $Actualizacion_siniestro.= " adicional1_f='$ArchivoDestino',";
						if($Identificador=='LicenciaRespaldo.jpg' && !$DSiniestro->adicional2_f) $Actualizacion_siniestro.= " adicional2_f='$ArchivoDestino',";
						if($Identificador=='DocumentoAdicional1.jpg' && !$DSiniestro->adicional3_f) $Actualizacion_siniestro.=" adicional3_f='$ArchivoDestino',";
						if($Identificador=='DocumentoAdicional2.jpg' && !$DSiniestro->adicional4_f) $Actualizacion_siniestro.=" adicional4_f='$ArchivoDestino',";
						if($Identificador=='Adicional1.jpg' && !$DSiniestro->eadicional1_f) $Actualizacion_siniestro.=" eadicional1_f='$ArchivoDestino',";
						if($Identificador=='Pagare.jpg' && !$DSiniestro->eadicional2_f) $Actualizacion_siniestro.=" eadicional2_f='$ArchivoDestino',";
						
					}
					elseif($Tipo_evento=='Devolucion')
					{
						if($Identificador=='Odometro.jpg' && !$DSiniestro->img_odo_entrada_f) $Actualizacion_siniestro.=" img_odo_entrada_f='$ArchivoDestino',";
						if($Identificador=='ActaDevolucion.jpg' && !$DSiniestro->img_inv_entrada_f) $Actualizacion_siniestro.=" img_inv_entrada_f='$ArchivoDestino',";
						if($Identificador=='FrenteVehiculo.jpg' && !$DSiniestro->fotovh5_f) $Actualizacion_siniestro.=" fotovh5_f='$ArchivoDestino',";
						if($Identificador=='LateralIzquierdo.jpg' && !$DSiniestro->fotovh6_f) $Actualizacion_siniestro.=" fotovh6_f='$ArchivoDestino',";
						if($Identificador=='LateralDerecho.jpg' && !$DSiniestro->fotovh7_f) $Actualizacion_siniestro.= " fotovh7_f='$ArchivoDestino',";
						if($Identificador=='AtrasVehiculo.jpg' && !$DSiniestro->fotovh8_f) $Actualizacion_siniestro.= " fotovh8_f='$ArchivoDestino',";
						if($Identificador=='Encuesta.jpg' && !$DSiniestro->img_encuesta_f) $Actualizacion_siniestro.= " img_encuesta_f='$ArchivoDestino',";
						if($Identificador=='ImagenAdicional3.jpg' && !$DSiniestro->dadicional3_f) $Actualizacion_siniestro.= " adicional3_f='$ArchivoDestino',";
						if($Identificador=='ImagenAdicional4.jpg' && !$DSiniestro->dadicional4_f) $Actualizacion_siniestro.= " adicional4_f='$ArchivoDestino',";
						if($Identificador=='ImagenAdicional1.jpg' && !$DSiniestro->fotovh9_f) $Actualizacion_siniestro.= " fotovh9_f='$ArchivoDestino',";
						if($Identificador=='ImagenAdicional2.jpg' && !$DSiniestro->fotovh10_f) $Actualizacion_siniestro.= " fotovh10_f='$ArchivoDestino',";
						
					}
				}
				else
				{
					$Errores.="20 Fallo al subir el archivo ".basename($DetalleArchivo['name'])."\n";
					$Cargado=false;
				}
			}
			if($Cargado) 
			{
				$Errores.="0 Información subida satisfactoriamente\n";
				$Observaciones=$_POST['observaciones'];
				$Ahora=date('Y-m-d H:i');
				$AHreal=explode(':',$_POST['horaTramite']);
				$Hreal=str_pad($AHreal[0],2,'0',STR_PAD_LEFT).':'.str_pad($AHreal[1],2,'0',STR_PAD_LEFT).':'.str_pad($AHreal[2],2,'0',STR_PAD_LEFT);
				$FHReal=$Fecreal.' '.$Hreal;
				$Actualizacion_siniestro=recorta($Actualizacion_siniestro);
				$Actualizacion_siniestro="update siniestro set $Actualizacion_siniestro ";
				if($Tipo_evento=='Devolucion') $Actualizacion_siniestro.=",obsconclusion=concat(obsconclusion,' \n[$User: $Ahora] ',\"$Observaciones\") ";
				$Actualizacion_siniestro.=" where id=$Cita->siniestro";
				include('inc/link.php');
				if(!mysql_query($Actualizacion_siniestro,$LINK)) $Errores.="Error en la actualizacion: $Actualizacion_siniestro".mysql_error($LINK);
				if($Tipo_evento=='Entrega')
				{
					$Fdevolucion=aumentadias($Cita->fecha,$Cita->dias_servicio);
					mysql_query("update cita_servicio set estado='C' , observaciones=concat(observaciones,' ','\n[$User: $Ahora] ',\"$Observaciones\"),
								momento_entrega='$FHReal',operario_domicilio=$Usuario->id, hora_llegada='$Ahora', fec_devolucion='$Fdevolucion',
								estadod='P',hora_devol='$Cita->hora' where id=$Cita->id",$LINK);
					// INSERCION DE LA ENTREGA
					// Si el kilometraje es mayor que el actual
					$Vehi=qom("select kilometraje(v.id) as kilometraje,v.id from vehiculo v where v.placa='$Cita->placa' ",$LINK);
					$kilometrajeEntrega=$_POST['kilometrajeEntrega'];
					if($kilometrajeEntrega>$Vehi->kilometraje) // GENERA UN ESTADO DE DOMICILIO PARA EL VEHICULO: estado 96  o de Traslado si es menor a 3 km la diferencia
					{
						$diferencia=$kilometrajeEntrega-$Vehi->kilometraje;
						if($diferencia<4)
							mysql_query("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones) values
								($Cita->oficina,$Vehi->id,94,$Cita->flota,'$Cita->fecha','$Cita->fecha',$Vehi->kilometraje,$kilometrajeEntrega,$diferencia,'Domicilio. Automatico desde Android') ",$LINK);
						else
							mysql_query("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones) values
								($Cita->oficina,$Vehi->id,96,$Cita->flota,'$Cita->fecha','$Cita->fecha',$Vehi->kilometraje,$kilometrajeEntrega,$diferencia,'Domicilio. Automatico desde Android') ",$LINK);
						
					}
					// inserta el estado de servicio
					$Insercion_ubicacion="insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones) values
						($Cita->oficina,$Vehi->id,1,$Cita->flota,'$Cita->fecha','$Fdevolucion',$kilometrajeEntrega,$kilometrajeEntrega,0,'Servicio. Automatico desde Android\n$observaciones')  ";
					if(mysql_query( $Insercion_ubicacion,$LINK))
					{
						$IDN=mysql_insert_id($LINK);
						mysql_query("update siniestro set ubicacion=$IDN where id=$Cita->siniestro",$LINK);
					}
					else $Errores.="5. error en la generacion de la nueva ubicacion\n $Insercion_ubicacion";
				}
				elseif($Tipo_evento=='Devolucion')
				{	
					$Hora = date('H:i:s');
					$Fecha = date('Y-m-d');
					$Dias_servicio=dias($Fecha,$Cita->fecha); // recalcula los dias reales de servicio
					mysql_query("update cita_servicio set estadod='C' , obs_devolucion=concat(obs_devolucion,' ','\n[$User: $Ahora] ',\"$Observaciones\"),
								fec_devolucion='$Fecreal',hora_devol_real='$Hreal',operario_domiciliod=$Usuario->id,dias_servicio=$Dias_servicio where id=$Cita->id",$LINK);
					$Vehi=qom("select kilometraje(v.id) as kilometraje,v.id from vehiculo v where v.placa='$Cita->placa' ",$LINK);
					$kilometrajeDevolucion=$_POST['kilometrajeEntrega'];
					$diferencia=$kilometrajeDevolucion-$Vehi->kilometraje;
					if($Ubicacion=qom("select * from ubicacion where vehiculo=$Vehi->id and estado=1 and fecha_inicial='$Cita->fecha' ",$LINK))
					{
						mysql_query("update siniestro set fecha_inicial='$Ubicacion->fecha_inicial',fecha_final='$Fecreal' where id=$Cita->siniestro",$LINK);
						mysql_query("update ubicacion set estado=7,fecha_final='$Fecreal',odometro_final=$kilometrajeDevolucion,odometro_diferencia=$diferencia,
							obs_mantenimiento=concat(obs_mantenimiento,' ','\n[$User: $Ahora] ',\"$observaciones\") where id=$Ubicacion->id",$LINK);
						$Nuevo_estado=$_POST['estadoDevolucion'];
						if($Nuevo_estado==8)  // ALISTAMIENTO
						{
							if(!mysql_query("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones) values
								($Cita->oficina,$Vehi->id,8,$Cita->flota,'$Fecreal','$Fecreal',$kilometrajeDevolucion,$kilometrajeDevolucion,0,'Alistamiento. Automatico desde Android') ",$LINK))
							$Errores.='6. No se pudo crear la ubicacion del alistamiento '.mysql_error($LINK);
							$Operario=qo1m("select id from operario where oficina=$Cita->oficina and por_defecto=1",$LINK);
						//	if(!mysql_query("insert into alistamiento (vehiculo,fecha,programado_por,descripcion,asignado_a) values ($Vehi->id,'$Fecreal','$Usuario->nombre $Usuario->apellido',
						//		\"$observaciones\",$Operario)",$LINK))
						//	$Errores.='5. No se pudo crear el pendiente de alistamiento '.mysql_error($LINK);
						}
						elseif($Nuevo_estado==92) // Mantenimiento Programado
						{
							mysql_query("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones) values
								($Cita->oficina,$Vehi->id,92,$Cita->flota,'$Fdevolucion','$Fdevolucion',$kilometrajeDevolucion,$kilometrajeDevolucion,0,'Mantenimiento Programado. Automatico desde Android') ",$LINK);
						}
						elseif($Nuevo_estado==5) // Fuera de servicio
						{
							$Oficina=qo1m("select nombre from oficina where id=$Cita->oficina",$LINK);
							$Naseguradora=qo1m("select nombre from aseguradora where id=$DSiniestro->aseguradora",$LINK);
							if(!mysql_query("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones) values
								($Cita->oficina,$Vehi->id,5,$Cita->flota,'$Fecreal','$Fecreal',$kilometrajeDevolucion,$kilometrajeDevolucion,0,concat('Fuera de Servicio. Automatico desde Android \n[$User: $Ahora] ',\"$observaciones\")) ",$LINK))
							$Errores.='7. No se pudo crear la ubicacion de fuera de servicio. '.mysql_error($LINK);
							$Envio1=enviar_gmail($Usuario->email /*de */,
										"$Usuario->nombre $Usuario->apellido" /*Nombre de */ ,
										"gabrielsandoval@aoacolombia.com,Gabriel Sandoval;juansuarez@aoacolombia.com,Juan Pablo Suarez Agudelo" /*para */,
										"$Usuario->email,$Usuario->nombre $Usuario->apellido" /*con copia*/,
										"Fuera de Servicio placa: $Vehi->placa" /*Objeto */,
										"<body><h3>Fuera de Servicio placa $Vehi->placa<h3>
										Registrado por <b>$Usuario->nombre $Usuario->apellido</b><br>
										Siniestro : <b>$DSiniestro->numero $DSiniestro->asegurado_nombre</b><br>
										Aseguradora: <b>$Naseguradora</b><br>
										Descripcion: <b>$observaciones</b><br><br>
										Favor verificar si el <b>Fuera de Servicio</b> es responsabilidad del asegurado. Si es asi, se debe marcar tanto en el siniestro como en la <b>Tabla de Control</b> 
										el indicador de <b><i>Siniestro Asegurado</i></b>.<br><br>
										Cordialmente,<br><br>
										<b>$Usuario->nombre $Usuario->apellido<br>
										<i>OPERARIO DE FLOTA $Oficina</i><br>
										AOA COLOMBIA S.A.<br>
										$Usuario->email<br>
										$Usuario->celular<br>
										</b>");
						}
					}
					else
					{
						$Errores.="4 No coincide la fecha de la cita con la inicial del servicio\n";
					}
				}
				mysql_close($LINK);
			}
		}
		else
		{
			$Errores.="3 Estado de Cita invalido\n";
		}
	}
	else
	{
		$Errores.="2 Cita no encontrada\n";
	}
}
else
{
	$Errores.="1 Usuario invalido\n";
}

header("Content-Type: plain/text"); 
header("Pragma: no-cache"); 
header("Expires: 0");
echo $Errores;




/*
POST:Array
[tipoServicio] => Entrega
[idCita] => 48193
[placa] => CZB765
[fechaTramite] => 2012-4-8
[horaTramite] => 23:58:13
[numSiniestro] => 6025252
[password] => Tabio2012+
[usuario] => arturo.quintero
[kilometrajeEntrega] => 61930
[observaciones] => vehiculo se entrega sin novedad

POST:Array
[tipoServicio] => Devolucion
[estadoDevolucion] => 8
[idCita] => 47901
[placa] => REN869
[horaTramite] => 0:0:5
[numSiniestro] => AW-2012-169-224
[password] => Tabio2012+
[fechaTramite] => 2012-4-12
[usuario] => arturo.quintero
[kilometrajeEntrega] => 16000
[observaciones] => Sin Novedad

*/

?>