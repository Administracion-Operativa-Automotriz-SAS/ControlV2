<?php

/**
	 *   Programa para carga de imagenes y datos de entrega a traves de ANDROID
 *
 * @version $Id$
 * @copyright 2011
*/
include('inc/funciones_.php');
$Fin_de_linea="\r\n";
$uploaded = 0;
$message = array();
$base_path  = "./siniestro/";
$Camino='';
$directorio='siniestro';
if(!is_dir($Camino.$directorio)) { mkdir($Camino.$directorio); 	chmod($Camino.$directorio, 0777); }
$id_siniestro = $_POST['id_siniestro'];
$Subdirectorio=substr(str_pad($id_siniestro,6,'0',STR_PAD_LEFT),0,3);
if(!is_dir($Camino.$directorio.'/'.$Subdirectorio)) { mkdir($Camino.$directorio.'/'.$Subdirectorio); chmod($Camino.$directorio.'/'.$Subdirectorio, 0777);}
if(!is_dir($Camino.$directorio.'/'.$Subdirectorio.'/'.$id_siniestro)) { mkdir($Camino.$directorio.'/'.$Subdirectorio.'/'.$id_siniestro); chmod($Camino.$directorio.'/'.$Subdirectorio.'/'.$id_siniestro, 0777);}
$Ruta_final=$Camino.$directorio.'/'.$Subdirectorio.'/'.$id_siniestro.'/';

//$usuario= $_POST['usuario'];
//$usuario=21;
$DU=qo("select * from operario where android!=0 limit 1");
$_SESSION['Nombre']=$DU->nombre.' '.$DU->apellido;
$_SESSION['Nick']=$DU->usuario;
$_SESSION['Id_alterno']=$DU->id;
$_SESSION['Tabla_usuario']='operario';
$_SESSION['Ngrupo']='OPERARIO FLOTAS';
$_SESSION['User']=23;
$observaciones = $_POST['observaciones'];
$placa = $_POST['placa'];
$estado = $_POST['estado'];
$id_cita = $_POST['id_cita'];
if(!$id_cita) die('ERROR EN ID CITA');
$kilometraje = $_POST['kilometraje'];
$Archivo=fopen('planos/'.$placa.'_'.$id_cita.'.txt','w+');
fwrite($Archivo,"Usuario: $usuario $Fin_de_linea Observaciones: $observaciones $Fin_de_linea Placa: $placa $Fin_de_linea Estado: $estado $Fin_de_linea idCita: $id_cita $Fin_de_linea Kilometraje: $kilometraje $Fin_de_linea Id Siniestro:$id_siniestro ");
fclose($Archivo);

include('inc/link.php');
if($Cita=qom("select * from cita_servicio where id=$id_cita",$LINK))
{
	if($Cita->estado=='P' && $Cita->siniestro==$id_siniestro) // entrega
	{
		$Fec_entrega = aumentadias($Cita->fecha, $Cita->dias_servicio);
		mysql_query("update siniestro set estado=7,fecha_inicial='$Cita->fecha',fecha_final='$Fec_entrega' where id=$id_siniestro",$LINK);
		$Sincars=qom("select id,numero,aseguradora from siniestro where id=$id_siniestro",$LINK);
		graba_bitacora('siniestro','M',$id_siniestro,'Activa Servicio',$LINK);
		$idv = qo1m("select id from vehiculo where placa='$Cita->placa' ",$LINK);
		$Hora = date('H:i:s');
		mysql_query("update cita_servicio set estado='C',estadod='P',hora_llegada='$Hora',hora_devol=hora,fec_devolucion='$Fec_entrega' where id=$id_cita",$LINK);
		// busca la ultima ubicacion para actualizar la fecha final con la fecha inicial del nuevo estado
		if ($Ultimo = qom("select * from ubicacion where vehiculo=$idv and fecha_final > '$Cita->fecha' and estado=2",$LINK))
		{ if ($Ultimo->fecha_inicial == $Cita->fecha) // si la fecha inicial y final coinciden dentro del mismo dia del cambio del nuevo estado, se elimina ese estado
				mysql_query("delete from ubicacion where id=$Ultimo->id",$LINK);
			else mysql_query("update ubicacion set fecha_final='$Cita->fecha' where id=$Ultimo->id",$LINK); }
		$Ultimo= qom("select * from ubicacion where vehiculo=$idv and fecha_final= '$Cita->fecha' and estado=2 order by id desc limit 1",$LINK);
		if($kilometraje>$Ultimo->odometro_final)
		{
			if($Cita->dir_domicilio)
			{
				$km1=$Ultimo->odometro_final;$km2=$kilometraje;$tpq=$km2-$km1;
				mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) values
				('$Cita->oficina','$idv','$Cita->fecha','$Cita->fecha','96','$km1','$km2','$tpq',\"Domicilio de entrega.\",'$Sincars->aseguradora')",$LINK);
				$IDU1 =mysql_insert_id($LINK);
				// inserta la bitacora de la ubicacion
				graba_bitacora('ubicacion','A',$IDU1,'Adiciona automáticamente recorrido domicilio en cumplimiento de entrega.',$LINK);
			}
			else
			{
				$km1=$Ultimo->odometro_final;$km2=$kilometraje;$tpq=$km2-$km1;
				mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) values
				('$Cita->oficina','$idv','$Cita->fecha','$Cita->fecha','94','$km1','$km2','$tpq',\"Traslado entre parqueaderos automático en entrega.\",'$Sincars->aseguradora')",$LINK);
				$IDU1 =mysql_insert_id($LINK);
				// inserta la bitacora de la ubicacion
				graba_bitacora('ubicacion','A',$IDU1,'Adiciona automáticamente traslado entre parqueaderos en cumplimiento de entrega.',$LINK);
			}
		}
		mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) values
		('$Cita->oficina','$idv','$Cita->fecha','$Fec_entrega','1','$kilometraje','$kilometraje','0',\"$observaciones\",'$Sincars->aseguradora')",$LINK);
		$IDU1 =mysql_insert_id($LINK);
		// inserta la bitacora de la ubicacion
		graba_bitacora('ubicacion','A',$IDU1,'Adiciona automáticamente Servicio en cumplimiento de entrega.',$LINK);
		mysql_query("update siniestro set ubicacion=$IDU1 where id=$id_siniestro",$LINK);
		foreach ($_FILES as $i => $elarchivo)
		{
			$name = $elarchivo['name'];
			$name=strtolower(str_replace(' ','_',$name));
			$target_path = $Ruta_final.basename($name);
			if($elarchivo['error'][$i] == 4) {	continue;}
			if($elarchivo['error'][$i] == 0)
			{
				if ($elarchivo['size'][$i] > 99439443) {
					//$message[] = "$name exceeded file limit.</br>";
					continue;}
				if(move_uploaded_file($elarchivo['tmp_name'], $target_path)) $uploaded++;
				if(strpos($target_path,'_fre.jpg'))	mysql_query("update siniestro set fotovh1_f='$target_path' where id=$id_siniestro",$LINK);
				if(strpos($target_path,'_izq.jpg'))	mysql_query("update siniestro set fotovh2_f='$target_path' where id=$id_siniestro",$LINK);
				if(strpos($target_path,'_der.jpg'))	mysql_query("update siniestro set fotovh3_f='$target_path' where id=$id_siniestro",$LINK);
				if(strpos($target_path,'_atr.jpg'))	mysql_query("update siniestro set fotovh4_f='$target_path' where id=$id_siniestro",$LINK);
				if(strpos($target_path,'_con.jpg'))	mysql_query("update siniestro set img_contrato_f='$target_path' where id=$id_siniestro",$LINK);
				if(strpos($target_path,'_act.jpg'))	mysql_query("update siniestro set img_inv_salida_f='$target_path' where id=$id_siniestro",$LINK);
				if(strpos($target_path,'_odo.jpg'))	mysql_query("update siniestro set img_odo_salida_f='$target_path' where id=$id_siniestro",$LINK);
			}
		}
	}
	if($Cita->estado=='C' && $Cita->estadod=='P' && $Cita->siniestro==$id_siniestro) // devolucion
	{
		$Hoy=date('Y-m-d');
		$idv = qo1m("select id from vehiculo where placa='$Cita->placa'",$LINK);
		mysql_query("update siniestro set fecha_final='$Hoy',estado=8,obsconclusion=\"$observaciones\" where id=$id_siniestro",$LINK);
		graba_bitacora('siniestro','M',$id_siniestro,'Finaliza Servicio',$LINK);
		$Sincars=qom("select id,numero,aseguradora,ubicacion,asegurado_nombre from siniestro where id=$id_siniestro",$LINK);
		$Ubicacion=qom("select * from ubicacion where id=$Sincars->ubicacion",$LINK);
		$Consumo=$kilometraje-$Ubicacion->odometro_inicial;
		$Aseg=qom("select * from aseguradora where id=$Sincars->aseguradora",$LINK);
		mysql_query("update ubicacion set fecha_final='$Hoy',odometro_final='$kilometraje',odometro_diferencia=odometro_final-odometro_inicial,
								obs_mantenimiento=concat(obs_mantenimiento,' ',\"$observaciones\"),observaciones=concat(observaciones,' ',\"$observaciones\"),
								estado=7 where id=$Sincars->ubicacion",$LINK);
		graba_bitacora('ubicacion','M',$Sincars->ubicacion,'Concluye el servicio',$LINK);
		$Hora=date('H:i:s');
		mysql_query("update cita_servicio set estadod='C',hora_devol_real='$Hora',fec_devolucion='$Hoy',obs_devolucion=concat(obs_devolucion,\"$observaciones\") where id=$id_cita",$LINK);
		mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) values
		('$Cita->oficina','$idv','$Hoy','$Hoy','8','$kilometraje','$kilometraje','0',\"Alistamiento automatico\",'$Sincars->aseguradora')",$LINK);
		$IDU1 =mysql_insert_id($LINK);
		// inserta la bitacora de la ubicacion
		graba_bitacora('ubicacion','A',$IDU1,'Adiciona automáticamente Alistamiento en Devolucion.',$LINK);
		$Nusuario=$_SESSION['Nombre'];
		$Asignado_a=qo1m("select id from operario where por_defecto=1 and oficina=$C->oficina limit 1",$LINK);
		mysql_query("insert into alistamiento (vehiculo,fecha,programado_por,descripcion,asignado_a) values ('$idv','$Hoy','$Nusuario','Vehiculo pasa a revisión general.','$Asignado_a')",$LINK);
		$IDU1 =mysql_insert_id($LINK);
		// inserta la bitacora de la ubicacion
		graba_bitacora('alistamiento','A',$IDU1,'Adiciona automáticamente Pendiente de Alistamiento en Devolucion.',$LINK);
		foreach ($_FILES as $i => $elarchivo)
		{
			$name = $elarchivo['name'];
			$name=strtolower(str_replace(' ','_',$name));
			$target_path = $Ruta_final.basename($name);
			if($elarchivo['error'][$i] == 4) {	continue;}
			if($elarchivo['error'][$i] == 0)
			{
				if ($elarchivo['size'][$i] > 99439443) {
					//$message[] = "$name exceeded file limit.</br>";
					continue;}
				if(move_uploaded_file($elarchivo['tmp_name'], $target_path)) $uploaded++;
				if(strpos($target_path,'_fre.jpg'))	mysql_query("update siniestro set fotovh5_f='$target_path' where id=$id_siniestro",$LINK);
				if(strpos($target_path,'_izq.jpg'))	mysql_query("update siniestro set fotovh6_f='$target_path' where id=$id_siniestro",$LINK);
				if(strpos($target_path,'_der.jpg'))	mysql_query("update siniestro set fotovh7_f='$target_path' where id=$id_siniestro",$LINK);
				if(strpos($target_path,'_atr.jpg'))	mysql_query("update siniestro set fotovh8_f='$target_path' where id=$id_siniestro",$LINK);
				if(strpos($target_path,'_con.jpg'))	mysql_query("update siniestro set img_encuesta_f='$target_path' where id=$id_siniestro",$LINK);
				if(strpos($target_path,'_act.jpg'))	mysql_query("update siniestro set img_inv_entrada_f='$target_path' where id=$id_siniestro",$LINK);
				if(strpos($target_path,'_odo.jpg'))	mysql_query("update siniestro set img_odo_entrada_f='$target_path' where id=$id_siniestro",$LINK);
			}
		}
		if($Consumo>$Aseg->limite_kilometraje)
		{
			$Email_usuario=$DU->email;
			$Nusuario=$_SESSION['Nombre'];
			$Oficina=qom("select * from oficina where id=$Cita->oficina",$LINK);
			enviar_gmail($Email_usuario /*de */ ,$Nusuario /*nombre de */ ,
			"arturoquintero@aoacolombia.com,ARTURO QUINTERO;gabrielsandoval@aoacolombia.com,GABRIEL SANDOVAL" /*para */ ,
			""   /*Con copia*/ ,
			"Exceso consumo de kilometraje en servicio $Cita->placa $Oficina->nombre"  /*OBJETO*/,
			"<body>Ocurrio un exceso en consumo de kilometraje en el servicio<br><br> ".
			"Placa: $Cita->placa <br>Oficina: $Oficina->nombre<br>Fecha y hora de la cita: $Cita->fecha $Cita->hora<br>".
			"Numero Siniestro: $Sincars->numero $Sincars->asegurado_nombre <br>".
			"Kilometraje maximo permitido:  $Aseg->limite_kilometraje.  Kilometraje consumido: ".coma_format($Consumo)." </body>" /*mensaje */);
		}
	}
}
mysql_close($LINK);



echo $uploaded . ' archivos cargados';//NO MANIPULAR

?>