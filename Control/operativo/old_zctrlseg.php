<?php

/**
 *   CONTROL DE CALL CENTER Y DIGITACION DE OBSERVACIONES EN UBICACIONES
 *
 * @version $Id$
 * @copyright 2010
 */
include('inc/funciones_.php');

sesion();
$Disp_flota=array();
$Disp_aoa=array();
$Estado=array();
$Citado=array();
$Pyp=array();

$USUARIO=$_SESSION['User'];
$Nusuario=$_SESSION['Nombre'];
$Nick=$_SESSION['Nick'];
$Hoy=date('Y-m-d H:i:s');
$CAUSAL="NO TIENE GARANTIA,NO TIENE GARANTIA;NO LE INTERESA,NO LE INTERESA;TIENE OTRO VEHICULO,TIENE OTRO VEHICULO;OTRO,OTRO;DESBORDE,DESBORDE;VEHICULO NO DEMORA EN TALLER,VEHICULO NO DEMORA EN TALLER;SALE DE VIAJE,SALE DE VIAJE;ESTA INCAPACITADO EL ASEGURADO,ESTA INCAPACITADO EL ASEGURADO;NO USA VEHICULO PRESTADO,NO USA VEHICULO PRESTADO;CANCELACION POST-ADJUDICACION,CANCELACION POST-ADJUDICACION;REQUIERE VEHICULO AUTOMATICO,REQUIERE VEHICULO AUTOMATICO;REQUIERE CAMPERO O CAMIONETA,REQUIERE CAMPERO O CAMIONETA;RESIDENCIA EN OTRA CIUDAD,RESIDENCIA EN OTRA CIUDAD;POCOS DIAS DE SERVICIO,POCOS DIAS DE SERVICIO";
if($id)
{
	$CONTACTO=qo1("select contacto_exitoso from siniestro where id=$id");
	$D=qo("select ingreso from siniestro where id=$id");
	$fingreso=date('Y-m-d',strtotime($D->ingreso));
	$hingreso=date('H:i:s',strtotime($D->ingreso));
	$Hoy1=date('Y-m-d');$Hoy2=date('H:i:s');
	if(!qo1("select id from seguimiento where siniestro=$id and tipo=1")) q("insert into seguimiento (siniestro,fecha,hora,descripcion,tipo) values ('$id','$fingreso','$hingreso','Ingreso a AOA',1)");
	q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ('$id','$Hoy1','$Hoy2','$Nusuario','Consulta desde Módulo de Call Center',2)");
	if($CONTACTO!='0000-00-00 00:00:00' && !qo1("select id from seguimiento where siniestro=$id and tipo=3")) q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'".date('Y-m-d',strtotime($CONTACTO))."','".date('H:i:s',strtotime($CONTACTO))."','$Nusuario','Contacto exitoso',3)");
}
 else $CONTACTO='0000-00-00 00:00:00';

if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}

echo "<script language='javascript'>
	function carga()
	{
		centrar(50,50);
		alert('Debe especificar una función valida');
	}
</script>
<body onload='carga()'><h3><font color='red'>Debe usar una función valida</font></h3></body>";

function call_center_obs()
{
	global $USUARIO,$Nusuario,$Nick,$Hoy,$CAUSAL;
	global $id;
	if($_SESSION['User']!=1) q("update siniestro set observaciones=concat(observaciones,'\n$Nusuario [$Hoy] Consultó.') where id=$id");
	$S=qo("select * from siniestro where id=$id");
	$Oficina=qo1("select id from oficina where ciudad='$S->ciudad'");
	$Ciudad=qo1("select t_ciudad('$S->ciudad')");
	$Aseguradora=qo("Select * from aseguradora where id=$S->aseguradora");
	if($S->ciudad_original) $Ciudado=qo1("select t_ciudad('$S->ciudad_original')"); else $Ciudado=false;
	$Observaciones=nl2br($S->observaciones);
	html('Control de seguimiento Call Center');
	echo "
		<style type='text/css'>
			a.notar {font-size:12;color:000000;cursor:pointer;}
			a.notar:hover {font-size:14;font-weight:bold;background-color:ffffCC;color:335533;}
		</style>
		<script language='javascript'>
		function carga()
		{
			//centrar();
		}
		function guardar_obs(Modo)
		{
			if(Modo==1)
			{
				if(!alltrim(document.forma.observaciones.value)) {alert('Debe digitar las observaciones');return false;}
				if(document.forma.TS.value==0) {alert('Debe seleccionar el tipo de comunicación para poder continuar');return false;}
				if(!document.forma.Causal.value) {alert('Debe seleccionar una causal para la no adjudicación');return false;}
				document.forma.Acc.value='call_center_noadj';
				document.forma.submit();
			}
			else
			{
				if(!alltrim(document.forma.observaciones.value)) {alert('Debe digitar las observaciones');return false;}
				if(document.forma.TS.value==0) {alert('Debe seleccionar el tipo de comunicación para poder continuar');return false;}
				document.forma.Acc.value='call_center_obs_ok';
				document.forma.submit();
			}
		}
		function agendar_cita(Fecha,Placa,Oficina,Flota)
		{
			var Hora=document.getElementById('cita_'+Fecha+Placa).value;
			document.formaa.Acc.value='agendar_cita';
			document.formaa.id.value='$id';
			document.formaa.placa.value=Placa;
			document.formaa.fecha.value=Fecha;
			document.formaa.hora.value=Hora;
			document.formaa.oficina.value=Oficina;
			document.formaa.flota.value=Flota;
			document.formaa.submit();
		}
		function nota_rapida(dato,ts)
		{
			document.forma.observaciones.value=document.forma.observaciones.value+dato;
			document.forma.TS.value=ts;
		}
		function envio_erronea()
		{
			if(confirm('Desea marcar como erronea la información de contacto de este siniestro?'))
			{
				modal('zctrlseg.php?Acc=envia_info_erronea&ids=$id&destino='+document.forma.emailaseguradora.value,0,0,500,500,'infoerronea');
			}
		}

		function contacto_exitoso()
		{
			if(confirm('Desea marcar este momento como contacto exitoso con el Asegurado?'))
			{
				modal('zctrlseg.php?Acc=contacto_exitoso&ids=$id',0,0,10,10,'Cexitoso');
			}
			document.getElementById('bcontacto').style.visibility='hidden';
		}

		function solicita_activacion(Fecha,Placa)
		{
		 	modal('zctrlseg.php?Acc=solicita_activacion&Fecha='+Fecha+'&Placa='+Placa+'&Siniestro=$id',0,0,500,500,'solicitud_activacion');
		}

		function crear_compromiso()
		{
			modal('zcompromiso.php?Acc=crear_compromiso&id=$id',0,0,400,800,'Compromiso');
		}

		function ver_compromisos()
		{
			modal('zcompromiso.php?Acc=ver_compromisos&id=$id',0,0,400,800,'Compromiso');
		}

		function adjudicar_cita()
		{
			window.open('zctrlseg.php?Acc=pinta_adjudicacion&id=$id','_self');
		}
	</script>
	<body onload='carga()' style='font-size:14' bgcolor='ffffdd' onunload=\"opener.parent.location.reload();\">
	<script language='javascript'>
		centrar();
	</script>
	<h3>Control de Seguimiento Call Center <i style='color:00000'>$Aseguradora->nombre  Póliza Número: $S->poliza Siniestro No. $S->numero </i></h3>
	Ciudad: <b>$Ciudad</b> ".($Ciudado?"Ciudad original: <b>$Ciudado</b>":"");
	echo "<br />Fecha del siniestro: <b>$S->fec_siniestro</b> Fecha de declaración del siniestro: <b>$S->fec_declaracion</b> FECHA DE AUTORIZACION: <b style='color:ff0000'>$S->fec_autorizacion</b>";
	echo "<br />Vigencia de la póliza:  Desde <b>$S->vigencia_desde</b> hasta <b>$S->vigencia_hasta</b>";
	echo "<br />Placa: <b>$S->placa</b> Marca: <b>$S->marca</b> Tipo: <b>$S->tipo</b> Línea: <b>$S->linea</b> Modelo: <b>$S->modelo</b> Clase: <b>$S->clase</b>";
	echo "<hr color='eeeeee'><b><u>ASEGURADO:</u></b> Nombre: <b>$S->asegurado_nombre</b> Identificación: <b>$S->asegurado_id</b> ";
	echo "<hr color='eeeeee'><b><u>Declarante:</u></b> Nombre: <b>$S->declarante_nombre</b> Identificación: <b>$S->declarante_id</b> Telefonos: <b>$S->declarante_telefono / $S->declarante_tel_resid
				/ $S->declarante_tel_ofic / $S->declarante_celular / $S->declarate_tel_otro / $S->declarante_email</b>";
	echo "<hr color='eeeeee'><b><u>Conductor:</u></b> Nombre: <b>$S->conductor_nombre</b> Telefonos: <b>$S->declarante_telefono / $S->conductor_tel_resid
				/ $S->conductor_tel_ofic / $S->conductor_celular / $S->conductor_tel_otro</b>";
	echo "<form action='zctrlseg.php' method='post' target='_self' name='forma' id='forma'>
				";
	if($S->estado==5)
	{
		echo "<b>Guion $Aseguradora->nombre</b><table border=2 bgcolor='ddffdd'><tr><td align='justify' style='font-size:12'>".guion_call_center($S,$Aseguradora)."</td></tr></table>";
	}
	elseif($S->estado==3)
	{
		echo "<br /><br /><center><font style='font-size:16;font-weight:bold;background-color:000000;color:ffff00'>&nbsp;&nbsp;&nbsp;&nbsp;ESTE SINIESTRO YA ESTÁ ADJUDICADO&nbsp;&nbsp;&nbsp;&nbsp;</FONT></center>";
		echo "<iframe src='marcoindex.php?Acc=abre_tabla&Num_Tabla=".tu('cita_servicio','id')."&VINCULOT=$S->id&VINCULOC=siniestro' height='300px' width='100%'></iframe>";
	}
	echo "<br /><br /><center>Si existe algun inconveniente o errores en la información del contacto, por favor marque la siguiente casilla para informar debidamente:<br />";
	if($Aseguradora->email_soporte_e || $_SESSION['User']==1 || $S->email_analista)
	{
		echo "Se enviará un email a <input type='text' name='emailaseguradora' value='$Aseguradora->email_soporte_e".($S->email_analista? ($Aseguradora->email_soporte_e?",":"")."$S->email_analista":"")."' size='200'> <br />";
		echo "<input type='button' id='bInfoerronea' name='bInfoerronea' value='Marcar el siniestro como INFORMACION ERRONEA DE CONTACTO' onclick='envio_erronea();'>";
	}
	else echo "No se ha configurado el email de soporte de la aseguradora.";

	echo "<hr color='eeeeee'><b>Observaciones registradas:</b><font style='font-size:10;color:00000'>$Observaciones</font><br>";
	$Seguimientos=q("select *,t_tipo_seguimiento(tipo) as ntipo from seguimiento where siniestro=$id order by fecha,hora");
	echo "<table border cellspacing='0' align='center'><tr><th>Fecha</th><th>Hora</th><th>Usuario</th><th>Descripción</th><th>Tipo</th></tr>";
	while($Seg=mysql_fetch_object($Seguimientos))
	{
		echo "<tr><td>$Seg->fecha</td><td>$Seg->hora</td><td>$Seg->usuario</td><td>$Seg->descripcion</td><td>$Seg->ntipo</td></tr>";
	}
	echo "</table>
				<hr color='eeeeee'>
				<br /><br />Tipo de comunicación: ".menu1("TS","select id, nombre from tipo_seguimiento where id in (".($S->contacto_exitoso=='0000-00-00 00:00:00'?'3,':'')."7,8)",0,1,"font-size:14;font-weight:bold;");
	if($S->contacto_exitoso=='0000-00-00 00:00:00')
	{
		echo "<br /><br />Aun no se ha registrado contacto exitoso con este asegurado. ";
		$C_exitoso=false;
	}
	else
	{
		 echo "<br /><br />Contacto exitoso con el asegurado : <b>$S->contacto_exitoso</b>";$C_exitoso=true;
	}

//	if($S->contacto_exitoso=='0000-00-00 00:00:00')
//		echo "<br /><br /><input type='button' id='bcontacto' name='bcontacto' value='CLICK AQUI PARA retistrar que en esta llamada se logró contacto exitoso con el Asegurado' onclick='contacto_exitoso();'>";
//	else
//		echo "<br /><br />Contacto exitoso con el asegurado : <b>$S->contacto_exitoso</b>";
	echo "
				</center>
				<br /><br /><b>DIGITE EN SEGUIDA LAS OBSERVACIONES:</b><br />
				<table bgcolor='eeffee' width='90%' align='center'><tr><td align='center'>
				<textarea name='observaciones' id='observaciones' style='font-size:14' rows='5' cols='150'></textarea></td></tr>
				<tr><td>
					<table align='center' bgcolor='ffffff'><tr><td>
						<center style='background-color:ffff00'><b>NOTAS RAPIDAS CONTACTO EXITOSO</b></center>
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").");' class='notar'>Se le brinda la información al Asegurado(a). Desea tomar el servicio.</a><br />
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").");' class='notar'>El Asegurado(a) queda pendiente en confirmar si toma el servicio.</a><br />
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").");document.forma.Causal.value=6;' class='notar'>No desea tomar el servicio porque ya pronto le hacen entrega del vehículo propio.</a><br />
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").");document.forma.Causal.value=3;' class='notar'>No desea tomar el servicio porque tiene otro vehículo.</a><br />
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").");document.forma.Causal.value=2;' class='notar'>No toma el servicio porque no se encuentra interesado en el momento.</a><br />
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").")' class='notar'>Queda pendiente en confirmar cuando toma el servicio.</a><br />
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").");document.forma.Causal.value=6;' class='notar'>No toma el servicio porque ya le entregaron el vehículo propio.</a><br />
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").")' class='notar'>Tiene inconvenientes con la tarjeta de crédito queda pendiente en comunicarse para confirmar si toma el servicio.</a><br />
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").");document.forma.Causal.value=10;' class='notar'>El Asegurado(a) cancela la cita, informa no toma el servicio por cuestión laboral no tiene tiempo disponible para acercase y tomar el servicio.</a><br />
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").");document.forma.Causal.value=1;' class='notar'>No toma el servicio porque no tiene tarjeta de crédito.</a><br />
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").")' class='notar'>El Asegurado(a) informa que no le han recibido el vehículo en el taller, queda en confirmar cuando toma el servicio.</a><br />
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").")' class='notar'>Se remite el caso a la Aseguradora, el Asegurado(a) necesita el vehículo urgentemente y la disponibilidad en esta ciudad está lejana, el asegurado afirma  no estar de acuerdo.</a><br />
						<a onclick='nota_rapida(this.innerHTML,".($C_exitoso?"7":"3").")' class='notar'>Se contacta e informa que se le presento un inconveniente queda pendiente en confirmar cuando retoma el servicio.</a><br />
					</td></tr>
					<tr><td>
						<center style='background-color:ffff00'><b>NOTAS RAPIDAS CONTACTO NO EXITOSO</b></center>
						<a onclick='nota_rapida(this.innerHTML,7)' class='notar'>Se deja el mensaje en el buzón de llamadas.</a><br />
						<a onclick='nota_rapida(this.innerHTML,7)' class='notar'>Se deja el mensaje en el buzón de llamadas y número fijo no contestan.</a><br />

					</td></tr></table>
				</td>
				</tr></table><br />
				Señor usuario $Nusuario $Nick, las observaciones registradas aqui, aparecerán a nombre suyo con fecha y hora de registro.<br /><br /><center>";
	if($S->estado==5)
	{
			echo "<input type='button' id='Enviar' name='Enviar' value='SIGUE PENDIENTE' style='font-size:14;font-weight:bold;width:200px;height:30px;' onclick='document.forma.estado.value=5;guardar_obs(0)'>
					<input type='button' id='Compromiso' name='Compromiso' value='Registrar Compromiso' style='font-size:13;font-weight:bold;width:200px;height:30px;' onclick='crear_compromiso()'>";
		if($Compromisos=qo1("select count(id) from compromiso where siniestro=$id"))
		{
			echo "<input type='button' id='ver_comp' name='ver_comp' value='Ver Compromisos' style='font-size:13;font-weight:bold;width:200px;height:30px;' onclick='ver_compromisos()'>";
		}
	}
	elseif($S->estado==3)
	{
		echo "<input type='button' id='Enviar' name='Enviar' value='VUELVE A PENDIENTE' style='font-size:14;font-weight:bold;width:200px;height:30px;'
					onclick=\"if(confirm('Esta seugro de cambiar el estado de Adjudicado a Pendiente?')) {document.forma.estado.value=5;guardar_obs(0);}\">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='button' id='Enviar' name='Enviar' value='SIGUE ADJUDICADO' style='font-size:14;font-weight:bold;width:200px;height:30px;' onclick='document.forma.estado.value=3;guardar_obs(0)'>";
	}
	echo "</center><br /><br />
				<hr color='brown'>
				<H2> NO ADJUDICACION </H3>
				<center><B>Debe colocar en las observaciones el motivo por el cual no se adjudica este servicio.<br />
				<br />SELECCIONE LA CAUSAL:</B> ".
				menu1('Causal',"select id,nombre from causal where id!=4 order by id",0,1,"font-size:14;font-weight:bold;",' ')." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type='button' id='Enviar' name='Enviar' value='NO ADJUDICADO' style='font-size:14;font-weight:bold;height:30;' onclick='guardar_obs(1)'>
				<input type='hidden' name='Acc' id='' value=''><input type='hidden' name='id' id='id' value='$id'><input type='hidden' name='estado' value=''></center>
				</form><br />";
	$Base=$Aseguradora->base;

	if($S->contacto_exitoso!='0000-00-00 00:00:00')
	{
		echo "<hr><center><input type='button' value='ADJUDICAR CITA' onclick='adjudicar_cita()' style='font-size:14px; font-weight:bold;height:30;width:400'></center>";

		////////////////////////////////////////////////////////////-----------------------------------  PRESENTACION DE LA ADJUDICACION --------------------------------------------------------///////////////////////////////////////////////
	}

}

class Pdispon
{
	var $Placa='';
	var $Id_vehiculo=0;
	var $Kilometraje=0;
	var $Oficina=0; // id de la oficina
	var $Servicio=0;
	var $Servicio_desde='';
	var $Servicio_hasta='';
	var $Servicio_hora_retorno='06:00:00';
	var $Adjudicado=0;
	var $Adjudicado_numero_siniestro='';
	var $Mantenimiento=0;
	var $Fuera_servicio=0;
	var $Id_estado=0;
	var $Flota=0;
	var $Contador=0;
	var $Ultimo='';

	function Pdispon($Objeto,$Flota)
	{
		$this->Placa=$Objeto->placa;
		$this->Ultimo=r($this->Placa,1);
		$this->Id_vehiculo=$Objeto->id;
		$this->Kilometraje=$Objeto->kmf;
		$this->Oficina=$Objeto->oficina;
		$this->Flota=$Flota;
		$this->verifica_estado();
	}

	function pinta($Limite_dias,$Oficina,$Linea,$Flota,$LINK)
	{

		$Hoy=date('Y-m-d');
		echo "<tr><td align='right' nowrap='yes'>".coma_format($this->Kilometraje)." <b style='font-size:14;color:0000ff'>$this->Placa</b></td>";
		for($i=0;$i<=$Limite_dias-1;$i++)
		{
			$Fecha=date('Y-m-d',strtotime(aumentadias($Hoy,$i)));
			if($this->Mantenimiento) echo "<td bgcolor='709F9C'>&nbsp;</td>";
			elseif($this->Fuera_servicio) echo "<td bgcolor='BF5652'>&nbsp;</td>";
			elseif($this->Adjudicado) $this->pinta_adjudicado($Fecha);
			elseif($this->Servicio) $this->pinta_servicio($Fecha,$Oficina,$Linea,$Flota,$LINK);
			else
			{
				$id='celda_'.$Linea.'_'.$Flota.'_'.$this->Contador;
				echo "<td bgcolor='ffffff' nowrap='yes' onmouseover=\"muestra('$id');\" onmouseout=\"oculta('$id');\">";
				$this->pinta_celda_cita($Fecha,$Oficina,$Linea,$Flota,$LINK);
				echo "</td>";
			}
		}
	}

	function pinta_adjudicado($Fecha)
	{
		echo "<td bgcolor='dddddd' nowrap='yes' alt='$this->Adjudicado_numero_siniestro' title='$this->Adjudicado_numero_siniestro'>&nbsp;</td>";
	}

	function pinta_servicio($Fecha,$Oficina,$Linea,$Flota,$LINK)
	{
		$id='celda_'.$Linea.'_'.$Flota.'_'.$this->Contador;
		if($Fecha>=$this->Servicio_desde && $Fecha<$this->Servicio_hasta)
		{
			echo "<td bgcolor='C2FFC2' nowrap='yes'>&nbsp;</td>";
		}
		elseif($Fecha==$this->Servicio_hasta)
		{
			echo "<td bgcolor='7196FF' nowrap='yes' onmouseover=\"muestra('$id');\" onmouseout=\"oculta('$id');\">$this->Servicio_hora_retorno";
			if($this->Adjudicado) $this->pinta_adjudicado($Fecha);
			else $this->pinta_celda_cita($Fecha,$Oficina,$Linea,$Flota,$LINK);
			echo "</td>";
		}
		else
		{
			echo "<td bgcolor='ffffff' nowrap='yes' onmouseover=\"muestra('$id');\" onmouseout=\"oculta('$id');\">";$this->pinta_celda_cita($Fecha,$Oficina,$Linea,$Flota,$LINK);echo "</td>";
		}
	}

	function pinta_celda_cita($Fecha,$Oficina,$Linea,$Flota,$LINK)
	{
		global $Pyp;
		$Pico_y_placa=false;
		$id='celda_'.$Linea.'_'.$Flota.'_'.$this->Contador;
		$this->Contador++;
		$Dia=date('w',strtotime($Fecha));
		for($i=0;$i<count($Pyp);$i++)
		{
			if($Fecha>=$Pyp[$i]->Fecha_inicial && $Fecha<=$Pyp[$i]->Fecha_final )
			{
				if($Dia==$Pyp[$i]->Dia && strpos(' '.$Pyp[$i]->Placas,$this->Ultimo))
				{
					echo "<a class='info'><img src='gifs/picoplaca.png' border='0' height='26'><span>Pico y Placa</span></a>";
					$Pico_y_placa=true;
				}
			}
		}
		echo "<div id='$id' style='visibility:hidden'>";

		if($Fecha>$this->Servicio_hasta)
			$h1=l($Oficina->hora_inicial,5);
		else
			$h1=aumentaminutos($this->Servicio_hora_retorno,90);
		if($Pico_y_placa)
		{
			if($h1<'19:30') $h1='19:30';
		}
		$Permite=false;
		if($this->Flota==6)
		{
			if(qo1m("select id from solicitud_faoa where placa='$this->Placa' and fecha='$Fecha'",$LINK))
				$Permite=true;
			else
				echo "<input type='button' value='Solic.Activacion' onclick=\"solicita_activacion('$Fecha','$this->Placa')\"; style='font-size=9px'>";
		}
		else
		{ $Permite=true;}
		if($Permite)
		{
			echo "<select name='cita_".$Fecha.$this->Placa."' id='cita_".$Fecha.$this->Placa."'>";
			$Pinta=false;
			for($H=l($Oficina->hora_inicial,2);$H<l($Oficina->hora_final,2);$H++)
			{
				//------------------ PRIMERA MEDIA HORA -------------------------------------
				$Hora1=str_pad($H,2,'0',STR_PAD_LEFT).':00:00';
				if($Hora1>$h1)
				{
					echo "<option value='$Hora1'>".date('h:i A',strtotime($Fecha.' '.$Hora1))."</option>";$Pinta=true;
				}
				//------------------  SEGUNDA MEDIA HORA ----------------------------------
				$Hora2=str_pad($H,2,'0',STR_PAD_LEFT).':30:00';
				if($Hora2>$h1)
				{
					echo "<option value='$Hora2'>".date('h:i A',strtotime($Fecha.' '.$Hora2))."</option>";$Pinta=true;
				}
				//------------------------------------------------------------------------
			}
			echo "</select>";
			if($Pinta)
				echo "<a href=\"javascript:agendar_cita('$Fecha','$this->Placa',$this->Oficina,$this->Flota);void(null);\"><img src='gifs/standar/Next.png' border='0'></a>";
			else
				echo "<a class='info' style='cursor:pointer'><img src='gifs/standar/Cancel.png' border='0'><span style='width:100px'>No hay horario disponible para adjudicar</span></a>";
		}
		echo "</div>";
	}

	function verifica_estado()
	{
		global $Estado,$Citado;
		$Ultimo=false;
		for($i=0; $i<count($Estado);$i++)
		{
			if($Estado[$i]->Id_vehiculo==$this->Id_vehiculo)
			{
				if($this->Id_estado<$Estado[$i]->id)
				{
					$this->Id_estado=$Estado[$i]->id;
					$Ultimo=$Estado[$i];
				}
			}
		}
		if($Ultimo)
		{
			if($Ultimo->Estado==1  /*Servicio*/)
			{
				$this->Servicio=1;
				$this->Servicio_desde=$Ultimo->Desde;
				$this->Servicio_hasta=$Ultimo->Hasta;
				$this->Servicio_hora_retorno=$Ultimo->Hora_devolucion;
			}
			if($Ultimo->Estado==4 /*Mantenimiento*/ || $Ultimo->Estado==92 /*Mantenimiento programado*/) $this->Mantenimiento=1;
			if($Ultimo->Estado==5 /*Fuera de Servicio*/) $this->Fuera_servicio=1;
		}

		for($i=0; $i<count($Estado);$i++)
		{
			if($Estado[$i]->Id_vehiculo==$this->Id_vehiculo && $Estado[$i]->Desde>date('Y-m-d'))
			{
				if($Estado[$i]->Estado==4 /*Mantenimiento*/ || $Estado[$i]->Estado==92 /*Mantenimiento programado*/) $this->Mantenimiento=1;
				if($Estado[$i]->Estado==5 /*Fuera de Servicio*/) $this->Fuera_servicio=1;
			}
		}

		for($i=0;$i<count($Citado);$i++)
		{
			if($Citado[$i]->Id_vehiculo==$this->Id_vehiculo)
			{
				if($Citado[$i]->Estado=='P' || $Citado[$i]->Estado=='C')
				{
					$this->Adjudicado=1;
					$this->Adjudicado_numero_siniestro=$Citado[$i]->NSiniestro;
				}
			}
		}
	}
}


class Pestado
{
	var $Estado=0;
	var $Nestado='';
	var $Id_vehiculo=0;
	var $Orden=0;
	var $Desde='';
	var $Hasta='';
	var $Hora_devolucion='';
	var $id=0;

	function Pestado($Objeto)
	{
		$this->id=$Objeto->id;
		$this->Estado=$Objeto->estado;
		$this->Nestado=$Objeto->nestado;
		$this->Id_vehiculo=$Objeto->vehiculo;
		$this->Orden=$Objeto->id;
		$this->Desde=$Objeto->fecha_inicial;
		$this->Hasta=$Objeto->fecha_final;
		if($this->Estado==1) $this->Hora_devolucion=$Objeto->hdev;

	}
}

class Pcita
{
	var $Id_vehiculo=0;
	var $Siniestro=0;
	var $NSiniestro='';
	var $Fecha='';
	var $Hora='';
	var $Estado='';

	function Pcita($Objeto)
	{
		$this->Id_vehiculo=$Objeto->idv;
		$this->Siniestro=$Objeto->siniestro;
		$this->NSiniestro=$Objeto->nsiniestro;
		$this->Fecha=$Objeto->fecha;
		$this->Hora=$Objeto->hora;
		$this->Estado=$Objeto->estado;
	}
}

class pico_y_placa{
	var $Fecha_inicial;
	var $Fecha_final;
	var $Dia;
	var $Placas;

	function pico_y_placa($Objeto)
	{
		$this->Fecha_inicial=$Objeto->fecha_inicial;
		$this->Fecha_final=$Objeto->fecha_final;
		$this->Dia=$Objeto->dia;
		$this->Placas=$Objeto->placas;
	}
}

function pinta_adjudicacion()
{
	global $id,$Disp_flota,$Disp_aoa,$Estado,$Citado,$Pyp;
	html('ADJUDICACION DE CITA');
	$S=qo("select * from siniestro where id=$id");
	$Oficina=qo("select id,hora_inicial,hora_final from oficina where ciudad='$S->ciudad'");
	$Ciudad=qo1("select t_ciudad('$S->ciudad')");
	$Aseguradora=qo("Select * from aseguradora where id=$S->aseguradora");
	if($S->ciudad_original) $Ciudado=qo1("select t_ciudad('$S->ciudad_original')"); else $Ciudado=false;
	if($S->estado==5)
	{
		$Hoy1=date('Y-m-d');
		$Ahora=date('Y-m-d');
		$CFI=aumentadias(date('Y-m-d'),-2);
		$CFF=aumentadias(date('Y-m-d'),10);
		echo "<script language='javascript'>
				function agendar_cita(Fecha,Placa,Oficina,Flota)
				{
					var Hora=document.getElementById('cita_'+Fecha+Placa).value;
					document.formaa.Acc.value='agendar_cita';
					document.formaa.id.value='$id';
					document.formaa.placa.value=Placa;
					document.formaa.fecha.value=Fecha;
					document.formaa.hora.value=Hora;
					document.formaa.oficina.value=Oficina;
					document.formaa.flota.value=Flota;
					document.formaa.submit();
				}
				function solicita_activacion(Fecha,Placa)
				{
				 	modal('zctrlseg.php?Acc=solicita_activacion&Fecha='+Fecha+'&Placa='+Placa+'&Siniestro=$id',0,0,500,500,'solicitud_activacion');
				}
			</script>

			<body>
			<hr color='brown'><h3>ADJUDICACION Y ASIGNACION DE CITA 	<i>Ciudad: $Ciudad</i></h3>
				<b>Se puede asignar cita buscando las placas que aparecen en <font style='color:00000;background-color:ffffff'> color negro y fondo blanco</font>,
				ajustadas hacia la izquierda. Las placas que aparecen en fondo <font style='background-color:ddffdd;color:000000;'> verde claro</font> significa que el vehículo está prestando servicio.
				Las placas que aparecen en fondo <font style='background-color:7387A3;color:000000;'>azul petróleo</font> significa que el vehículo está en mantenimiento.
				Las placas que aparecen en fondo <font style='background-color:FFC1BF;color:000000;'>rojo</font> significa que el vehículo está fuera de servicio. Si aparece una fila entera
				en fondo <font style='background-color:aaaaaa;color:000000;'>gris</font> significa que ese vehículo ya está agendado y la hora aparece en alguna de las celdas de la fila. Las placas
				que aparecen en color <font color='red'>rojo</font> significa que tienen pico y placa.<br /><br />
				<table border cellspacing=0 width='100%' bgcolor='dddddd'>";$Sec=1;

		echo "<tr><td>Kilometraje</td>";
		$Limite_dias=15;
		while($Sec<=$Limite_dias)
		{
			$Dia=date('w',strtotime($Ahora));
			$Ndia=dia_semana($Dia);
			if($Dia==0 || $Dia==6) $Fondo='ffdddd'; else $Fondo='ffffff';
			echo "<th nowrap='yes' style='font-size:12;font-weight:bold;background-color:$Fondo;color:000000'>$Ahora $Ndia</th>";
			$Sec++;
			$Ahora=date('Y-m-d',strtotime(aumentadias($Ahora,1)));
		}
		////////////  CITAS PRE PROGRAMADAS //////

		///////-----------------------------------------------------------------------
		////////////////////////////////////////////////////          VEHICULOS DE LA FLOTA               ////////////////////////////////////////////////////////////////////////////////
		echo "</tr>";
		require('inc/link.php');
		$Temp_vehiculos1='tmpi_vehiculos1_'.$_SESSION['User'].'_'.$_SESSION['Id_alterno'];
		$Temp_vehiculos2='tmpi_vehiculos2_'.$_SESSION['User'].'_'.$_SESSION['Id_alterno'];
		$Temp_estados='tmpi_estados_'.$_SESSION['User'].'_'.$_SESSION['Id_alterno'];
		$Ahora=date('Y-m-d');
		//------------------------------------------------------------------------------------------------------------------------------------
		mysql_query("drop table if exists $Temp_vehiculos1",$LINK);
		mysql_query(" create table $Temp_vehiculos1 select distinct v.placa, v.id, kilometraje(v.id) as kmf,o.id as oficina
										FROM vehiculo v,ubicacion u,oficina o
										WHERE u.flota=$S->aseguradora and
										u.vehiculo=v.id and u.fecha_final>='$Ahora' and u.oficina=o.id and o.ciudad='$S->ciudad' and (v.inactivo_desde='00000-00-00' or v.inactivo_desde>'$Ahora')
										and v.flota_distinta=1 Order by kmf ",$LINK);
		if($Picos=mysql_query("select * from picoyplaca	where ciudad='$S->ciudad' and  fecha_final>'$Ahora' ",$LINK))
		{$Contador=0;while($Pi=mysql_fetch_object($Picos))  { $Pyp[$Contador]=new pico_y_placa($Pi);$Contador++; }	}

		if($Disponibles=mysql_query("select * from $Temp_vehiculos1",$LINK))
		{
			mysql_query("drop table if exists $Temp_estados",$LINK);
			mysql_query("create table $Temp_estados select u.*,t_estado_vehiculo(u.estado) as nestado, hdevol(u.vehiculo,u.fecha_final) as hdev
									FROM ubicacion u,$Temp_vehiculos1 v
									WHERE u.vehiculo=v.id and u.fecha_final>='$Ahora' order by u.vehiculo,u.estado ",$LINK);
			$Estados=mysql_query("select * from $Temp_estados",$LINK);
			$IniCitas=date('Y-m-d',strtotime(aumentadias($Ahora,-8)));
			$Citas=mysql_query("select c.*,t_siniestro(c.siniestro) as nsiniestro,v.id as idv
									FROM cita_servicio c,vehiculo v,$Temp_vehiculos1 tv
									WHERE ((c.fecha >='$IniCitas' and c.estado='P') or (c.fecha>='$Ahora' and c.estado in ('P','C')))  and c.oficina=$Oficina->id
									and c.placa=v.placa and v.id=tv.id ",$LINK);
			$Contador=0;while($C=mysql_fetch_object($Citas)) {$Citado[$Contador]= new Pcita($C);$Contador++;}
			$Contador=0; while($D=mysql_fetch_object($Estados)) { $Estado[$Contador]=new Pestado($D); $Contador++;	}
			$Contador=0; while($D=mysql_fetch_object($Disponibles)) {$Disp_flota[$Contador]=new Pdispon($D,$S->aseguradora);	$Contador++;	}
			echo "<tr><th colspan=".($Limite_dias+1)." style='font-size:16;font-weight:bold;'>FLOTA: $Aseguradora->nombre</TH></tr>";
			for($i=0;$i<count($Disp_flota);$i++)
			{
				 $Disp_flota[$i]->pinta($Limite_dias,$Oficina,$i,1,$LINK);
			}
		}



		////////////////////////////////////////////////////          VEHICULOS DE AOA               ////////////////////////////////////////////////////////////////////////////////
		mysql_query("drop table if exists $Temp_vehiculos2",$LINK);
		mysql_query(" create table $Temp_vehiculos2 select distinct v.placa, v.id, kilometraje(v.id) as kmf,o.id as oficina
										FROM vehiculo v,ubicacion u,oficina o
										WHERE
										u.vehiculo=v.id and u.fecha_final>='$Ahora' and u.oficina=o.id and o.ciudad='$S->ciudad' and (v.inactivo_desde='00000-00-00' or v.inactivo_desde>'$Ahora')
										and v.flota_distinta=0 Order by kmf ",$LINK);
		if($Disponibles=mysql_query("select * from $Temp_vehiculos2",$LINK))
		{
			mysql_query("drop table if exists $Temp_estados",$LINK);
			mysql_query("create table $Temp_estados select u.*,t_estado_vehiculo(u.estado) as nestado, hdevol(u.vehiculo,u.fecha_final) as hdev
									FROM ubicacion u,$Temp_vehiculos2 v
									WHERE u.vehiculo=v.id and u.fecha_final>='$Ahora' ",$LINK);
			$Estados=mysql_query("select * from $Temp_estados",$LINK);
			$IniCitas=date('Y-m-d',strtotime(aumentadias($Ahora,-8)));
			$Citas=mysql_query("select c.*,t_siniestro(c.siniestro) as nsiniestro,v.id as idv
									FROM cita_servicio c,vehiculo v,$Temp_vehiculos2 tv
									WHERE ((c.fecha >='$IniCitas' and c.estado='P') or (c.fecha>='$Ahora' and c.estado in ('P','C'))) and c.oficina=$Oficina->id
									and c.placa=v.placa and v.id=tv.id ",$LINK);
			$Contador=0;while($C=mysql_fetch_object($Citas)) {$Citado[$Contador]= new Pcita($C);$Contador++;}
			$Contador=0; while($D=mysql_fetch_object($Estados)) { $Estado[$Contador]=new Pestado($D); $Contador++;	}
			$Contador=0; while($D=mysql_fetch_object($Disponibles)) {$Disp_aoa[$Contador]=new Pdispon($D,6);	$Contador++;	}
			echo "<tr><th colspan=".($Limite_dias+1)." style='font-size:16;font-weight:bold;'>FLOTA: AOA</TH></tr>";
			for($i=0;$i<count($Disp_aoa);$i++)
			{
				$Disp_aoa[$i]->pinta($Limite_dias,$Oficina,$i,2,$LINK);
			}
		}
		mysql_close($LINK);
		echo "</table>";
		echo "<form action='zctrlseg.php' method='post' target='_self' name='formaa' id='formaa'>
					<input type='hidden' name='id' id='id' value=''>
					<input type='hidden' name='Acc' id='Acc' value=''>
					<input type='hidden' name='placa' id='placa' value=''>
					<input type='hidden' name='fecha' id='fecha' value=''>
					<input type='hidden' name='hora' id='hora' value=''>
					<input type='hidden' name='oficina' id='oficina' value=''>
					<input type='hidden' name='flota' id='flota' value=''>
					</form><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></body>";
	}
}

function Busca_citas($Citas,$D,$Ahora,$LINK)
{
	$Resultado=array();
	if($Citas)
	{
		mysql_data_seek($Citas,0);
		while($ci=mysql_fetch_object($Citas))
		{
			if($ci->placa==$D->placa)
			{
				$Resultado['momento']=$ci->fecha.'|'.$ci->hora;   // obtiene la fecha y la hora de la cita para el siniestro
				$Resultado['siniestro']=qo1m("select concat(t_aseguradora(aseguradora),' - ',numero) from siniestro where id=$ci->siniestro",$LINK); // obtiene alguna información del siniestro
			}
			if($ci->fecha==$Ahora)
			{
				$Resultado['citas_dia'].=$ci->hora.',';  //  acumula las citas del dia
			}
		}
	}
	return $Resultado;  // retorna toda la variable
}

function analiza_estado($est,$Ahora)
{
	$Resultado=array();
	$Resultado['estado']=$est->estado;
	$Resultado['nestado']=$est->nestado;
	echo "Alt=\"$est->observaciones $est->obs_mantenimiento\" title=\"$est->observaciones $est->obs_mantenimiento\" ";
	if($est->estado==1 /*servicio*/ )
	{
		$Resultado['registro']=$est;
	}
	return $Resultado;
}

function Pinta_celda($Celda,$Tiene_cita,$D,$S,$Flota,$LINK,$Ahora)
{
	switch($Celda['estado'])
	{
		case 1:
			$SE=$Celda['registro'];
			if($Ahora==$SE->fecha_final)
			{
				$idSin=qo1m("select id from siniestro where ubicacion=$SE->id",$LINK);
				$Hora_entrega=qo1m("select hora_devol from cita_servicio where siniestro=$idSin and flota=$Flota and estado='C' ",$LINK);
				echo "<span style='background-color:ffffff' onclick=\"muestra('c_$Ahora$D->placa');\" alt='Este dia finaliza servicio $idSin' title='Este dia finaliza servicio $idSin'>
				&nbsp;&nbsp;&nbsp;<b style='color:blue'>$D->placa ".date('h:i A',strtotime($Hora_entrega))."</b>&nbsp;&nbsp;&nbsp;</span> ";
				if($Tiene_cita['momento'])
				{
					if(l($Tiene_cita['momento'],10)==$Ahora)
					{
						$Hora=r($Tiene_cita['momento'],8);
						echo "<a class='info' href=\"javascript:alert('".$Tiene_cita['siniestro']."');\">$D->placa ".date('h:i A',strtotime($Hora))."<span style='width:200px'>".$Tiene_cita['siniestro']."</span></a>";
					}
					else
						echo "<a class='info' href=\"javascript:alert('".$Tiene_cita['siniestro']."');\">$D->placa<span style='width:200px'>".$Tiene_cita['siniestro']."</span></a>";
				}
				else
				{
					pinta_cita($Ahora,$D->placa,$S->aseguradora,$Flota,$Tiene_cita['citas_dia'],$D->oficina,$LINK,$Hora_entrega);
				}
			}
			else
			{
				echo "<span style='background-color:ddffdd;".(l($Tiene_cita['momento'],10)==$Ahora?'text-decoration:blink;':'')."' alt='Aún está en servicio' title='Aún está en servicio'>
 												 <i>$D->placa ".(l($Tiena_cita['momento'],10)==$Ahora?date('h:i A',strtotime(r($Tiene_cita['momento'],8))):'')."</i> </span> ";
			}
			break;
		case 4:echo "<span style='background-color:7387A3;".(l($Tiene_cita['momento'],10)==$Ahora?'text-decoration:blink;':'')."'> <i>$D->placa ".(l($$Tiene_cita['momento'],10)==$Ahora?date('h:i A',strtotime(r($Tiene_cita['momento'],8))):'')."</i> </span>";	break;
		case 5:echo "<span style='background-color:FFC1BF'> <i>$D->placa</i> </span>";	break;
		case 6:echo "<span style='background-color:c89fff'> <i>$D->placa</i> </span>";	break;
		case 91:echo "<span style='background-color:00eeFF'> <i>$D->placa</i> </span>";	break;
		case 92:echo "<span style='background-color:2F3D66;color:ffffff;'> <i>$D->placa</i> </span>";	break;
		case 93:echo "<span style='background-color:4F2D00;color:ffffff;'> <i>$D->placa</i> </span>";	break;
		case 7:
			if($Tiene_cita['momento'])
				echo "<a class='info' href=\"javascript:alert('".$Tiene_cita['siniestro']."');\">$D->placa<span style='width:200px'>".$Tiene_cita['siniestro']."</span></a>";
			else
				echo "<span onclick=\"muestra('c_$Ahora$D->placa');\" style='background-color:ffffff;cursor:pointer;'>
								&nbsp;&nbsp;&nbsp;<b>$D->placa</b>&nbsp;&nbsp;&nbsp;</span>"; pinta_cita($Ahora,$D->placa,$S->aseguradora,$Flota,$Tiene_cita['citas_dia'],$D->oficina,$LINK);break;
		case 2:
			if($Tiene_cita['momento'])
			{
				if(l($Tiene_cita['momento'],10)==$Ahora)
				{
					$Hora=r($Tiene_cita['momento'],8);
					echo "<a class='info' href=\"javascript:alert('".$Tiene_cita['siniestro']."');\">$D->placa ".date('h:i A',strtotime($Hora))."<span style='width:200px'>".$Tiene_cita['siniestro']."</span></a>";
				}
				else
					echo "<a class='info' href=\"javascript:alert('".$Tiene_cita['siniestro']."');\">$D->placa<span style='width:200px'>".$Tiene_cita['siniestro']."</span></a>";
				break;
			}
			$Numero=r($D->placa,1);$Dia=date('w',strtotime($Ahora));
			if(qo1m("select id from aoacol_aoacars.picoyplaca	where ciudad='$S->ciudad' and '$Ahora' between fecha_inicial and fecha_final	and dia=$Dia and placas like'%$Numero%'",$LINK))
			{
				echo "<font color='red'> <i>$D->placa</i> </font>";
			}
			else
			{
				echo "<span onclick=\"muestra('c_$Ahora$D->placa');\" style='background-color:ffffff;cursor:pointer;'>
								&nbsp;&nbsp;&nbsp;<b>$D->placa</b>&nbsp;&nbsp;&nbsp;</span>"; pinta_cita($Ahora,$D->placa,$S->aseguradora,$Flota,$Tiene_cita['citas_dia'],$D->oficina,$LINK);
			}
			break;
	}
}

function pinta_cita($fecha,$Placa,$Aseguradora,$Flota,$horas_cita,$Oficina,$LINK,$Hora_entrega='00:00:00')
{
	global $CONTACTO;
	$Activa=false;
	if($CONTACTO!='0000-00-00 00:00:00') $Activa=true;
	$h1=aumentaminutos($Hora_entrega,90);
	echo "<span id='c_$fecha$Placa' style='visibility:hidden'>";
	$Permite=false;
	if($Flota==6)
	{
		if(qo1m("select id from solicitud_faoa where placa='$Placa' and fecha='$fecha'",$LINK))
			$Permite=true;
		else
			echo "<input type='button' value='Solicitar Activacion' onclick=\"solicita_activacion('$fecha','$Placa')\";>";
	}
	else
	{ $Permite=true;}
	if($Permite)
	{
		echo "<select name='cita_$fecha$Placa' id='cita_$fecha$Placa'>";
		$Pinta=false;
		for($H=8;$H<18;$H++)
		{
			//------------------ PRIMERA MEDIA HORA -------------------------------------
			$Hora1=str_pad($H,2,'0',STR_PAD_LEFT).':00:00';
			if($Hora1>$h1)
			{
				echo "<option value='$Hora1'>".date('h:i A',strtotime($fecha.' '.$Hora1))."</option>";$Pinta=true;
			}
			//------------------  SEGUNDA MEDIA HORA ----------------------------------
			$Hora2=str_pad($H,2,'0',STR_PAD_LEFT).':30:00';
			if($Hora2>$h1)
			{
				echo "<option value='$Hora2'>".date('h:i A',strtotime($fecha.' '.$Hora2))."</option>";$Pinta=true;
			}
			//------------------------------------------------------------------------
		}
		echo "</select>";
		if($Pinta)
			echo "<a href=\"javascript:".($Activa?"agendar_cita('$fecha','$Placa',$Oficina,$Flota);void(null);":"alert('No hay registro de contacto exitoso');void(null);")."\"><img src='gifs/standar/Next.png' border='0'></a>";
		else
			echo "<a class='info' style='cursor:pointer'><img src='gifs/standar/Cancel.png' border='0'><span style='width:100px'>No hay horario disponible para adjudicar</span></a>";
	}

	echo "</span>";
}

function call_center_obs_ok()
{
	global $id,$observaciones,$USUARIO,$Nusuario,$Hoy,$estado,$TS;
	$H1=date('Y-m-d'); $H2=date('H:i:s');
	q("update siniestro set observaciones=concat(observaciones,'\n$Nusuario [$Hoy]:$observaciones'),estado=$estado where id=$id");
	q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'$H1','$H2','$Nusuario','$observaciones',$TS)");
	if($TS==3) q("update siniestro set contacto_exitoso='$Hoy' where id=$id ");
	q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."',
			'".date('s')."','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','siniestro','M','$id','".$_SERVER['REMOTE_ADDR']."','Observaciones')");
	echo "<script language='javascript'>
		function carga()
		{
			window.close();void(null);
		}
	</script>
	<body onload='carga()'></body>";
}

function guion_call_center($S,$A)
{
	global $Hoy;
	$Hora=date('H',strtotime($Hoy));
	if($Hora>11) $Tarde='Buenas tardes'; else $Tarde='Buenos días';
	$Oficina=qo1("select t_ciudad($S->ciudad)");
	switch($S->aseguradora)
	{
		case 1:
			$Guion="$Tarde Señor (a) <b>$S->asegurado_nombre</b>.<br /><br /> Le habla <b>".$_SESSION['Nombre']."</b> de AOA  (ADMISTRACION OPERATIVA  AUTOMOTRIZ);
			La empresa que le provee el <b>VEHICULO DE REEMPLAZO DE COLSEGUROS</B>;  <br />
			Le informamos que Colseguros autorizó el préstamo de un vehículo por 7 días o 700 km, lo primero que se cumpla;  <b>este servicio no tiene ningún costo</b>.<br /><br />
 			Señor (a) <b>$S->asegurado_nombre</b> se encuentra  interesado(a) en utilizar este servicio?.<br /><br />
			<font color='blue'>Si el asegurado  no está interesado  se  indaga  el motivo y se  digita en las observaciones del sistema de A.O.A.</font><br />
			<font color='blue'>Si el asegurado  esta interesado se le informa los requisitos para acceder al servicio.</font><br /><br />
			Para acceder al servicio y hacer entrega del vehículo debemos acordar una cita y los requisitos son: <br />
			<li>Presentar su cedula de ciudadanía original,
			<li>Su licencia de conducción original y
			<li>Su tarjeta de crédito con un cupo disponible de <b style='color:brown'> $ ".coma_format($A->garantia)."</b> pesos, con el fin de dejar un Boucher firmado en <b>calidad de garantía</b> mientras usted utiliza el vehículo.<br /><br />
			<font color='blue'>Si el asegurado  manifiesta no cumplir con los requisitos  se  indaga  el motivo y se busca la forma de solucionar el inconveniente para que pueda acceder al servicio;
			si definitivamente no cumple con los requisitos no se  adjudica el servicio y se digita en las observaciones del sistema de A.O.A.</font><br /><br />
			<font color='blue'>Si el asegurado  cumple con los requisitos para acceder al servicio.: </font><br />
			Le informamos que tenemos disponibilidad  para entregar el vehículo el día _______a las ____<br />
			<font color='blue'>Si el asegurado  no está de acuerdo  con la disponibilidad:</font><br />
			Tenemos la posibilidad de entregarle del vehículo antes de la fecha inicialmente proporcionada, solamente en caso de que algún asegurado con cita agendada  presenta alguna eventualidad
			ya sea que nos cancele la cita o cuando al asistir a la cita no cumpla con alguno de  los requisitos. En ese momento lo contactaríamos nuevamente para reprogramar la cita .<br /><br />
			<font color='blue'>Si definitivamente el asegurado no acepta la disponibilidad:</font><br />
			Señor(a) <b>$S->asegurado_nombre</b> le informo que remitiremos nuevamente a Colseguros la solicitud de servicio para que  se le asigne otro proveedor.<br />
			<font color='blue'>y se  digita en las observaciones del sistema de A.O.A que esta solicitud FUE DESBORDADA.</font><br /><br />
			<font color='blue'>Si el asegurado está de acuerdo con la disponibilidad se programa la cita y se informa lugar de entrega.</font><br />
			En Bogota Nuestra oficina está ubicada en el Centro Comercial Hacienda Santa Bárbara  Av. 7 No 115- 60  oficina D (de dedo) 507, le confirmo su cita queda asignada para el día ________  a las _____
			<br />LE INFORMO QUE SU SERVICIO INICIA A PARTIR DE LA HORA PACTADA EN ESTA LLAMADA.
			<br /><br />
			<font color='blue'>Para las otras ciudades:</font>
			Señor(a) <b>$S->asegurado_nombre</b> le informo que en pocos minutos lo contactará el funcionario de (<b>$Oficina</b>) para acordar el lugar y hora para la entrega del vehiculo.
			En el eje Cafetero el vehiculo se entrega en Pereira.<br />
			<font color='blue'>En caso de que el asegurado pregunte sobre qué vehículo le entregariamos:</font><br />
			Se le entregará un vehículo marca Renault Logan mecánico 2008 y 2009 color gris o su equivalente en otra marca.
			<br /><br />Muchas gracias por su tiempo y recuerde que habló con <b>".$_SESSION['Nombre']."</b>";
			break;
		case 2:
			$Guion="$Tarde Señor (a) <b>$S->asegurado_nombre</b>.<br /><br /> Le habla <b>".$_SESSION['Nombre']."</b> de AOA  (ADMISTRACION OPERATIVA  AUTOMOTRIZ);
			La empresa que le provee el <B>VEHICULO DE CORTESIA DE ROYAL</B>;  <br />
			Le informamos que Royal autorizó el préstamo de un vehículo por 7 días;  <b>este servicio no tiene ningún costo</b>.<br /><br />
 			Señor (a) <b>$S->asegurado_nombre</b> se encuentra  interesado(a) en utilizar este servicio?.<br /><br />
			<font color='blue'>Si el asegurado  no está interesado  se  indaga  el motivo y se  digita en las observaciones del sistema de A.O.A.</font><br />
			<font color='blue'>Si el asegurado  esta interesado se le informa los requisitos para acceder al servicio.</font><br /><br />
			Para acceder al servicio y hacer entrega del vehículo debemos acordar una cita y los requisitos son: <br />
			<li>Presentar su cedula de ciudadanía original,
			<li>Su licencia de conducción original y
			<li>Su tarjeta de crédito con un cupo disponible de <b style='color:brown'>$ ".coma_format($A->garantia)."</b> pesos, con el fin de dejar un Boucher firmado en <b>calidad de garantía</b> mientras usted utiliza el vehículo.<br /><br />
			<font color='blue'>Si el asegurado  manifiesta no cumplir con los requisitos  se  indaga  el motivo y se busca la forma de solucionar el inconveniente para que pueda acceder al servicio;
			si definitivamente no cumple con los requisitos no se  adjudica el servicio y se digita en las observaciones del sistema de A.O.A.</font><br /><br />
			<font color='blue'>Si el asegurado  cumple con los requisitos para acceder al servicio.: </font><br />
			Le informamos que tenemos disponibilidad  para entregar el vehículo el día _______a las ____<br />
			<font color='blue'>Si el asegurado está de acuerdo con la disponibilidad se programa la cita y se informa lugar de entrega.</font><br />
			En Bogota Nuestra oficina está ubicada en el Centro Comercial Hacienda Santa Bárbara  Av. 7 No 115- 60  oficina D (de dedo) 507, le confirmo su cita queda asignada para el día ________  a las _____
			<br />LE INFORMO QUE SU SERVICIO INICIA A PARTIR DE LA HORA PACTADA EN ESTA LLAMADA.
			<br /><br />
			<font color='blue'>Para las otras ciudades:</font>
			Señor(a) <b>$S->asegurado_nombre</b> le informo que en pocos minutos lo contactará el funcionario de (<b>$Oficina</b>) para acordar el lugar y hora para la entrega del vehiculo.
			En el eje Cafetero el vehiculo se entrega en Pereira, Armenia o Manizales.<br /><br />
			<font color='blue'>En caso de que el asegurado pregunte sobre qué vehículo le entregariamos:</font><br />
			Se le entregará un vehículo marca Chevrolet Aveo mecánico 2009 y 2010 color gris o su equivalente en otra marca.
			<br /><br />Muchas gracias por su tiempo y recuerde que habló con <b>".$_SESSION['Nombre']."</b>";
			break;
		case 3:
			$Guion="$Tarde Señor (a) <b>$S->asegurado_nombre</b>.<br /><br /> Le habla <b>".$_SESSION['Nombre']."</b> de AOA  (ADMISTRACION OPERATIVA  AUTOMOTRIZ);
			La empresa que le provee el <B>VEHICULO DE CORTESIA DE LIBERTY</B>;  <br />
			Le informamos que Liberty autorizó el préstamo de un vehículo por 7 días o 700 kilómetros lo primero que se cumpla; <b>este servicio no tiene ningún costo</b>.<br /><br />
	 		Señor (a) <b>$S->asegurado_nombre</b> se encuentra  interesado(a) en utilizar este servicio?.<br /><br />
			<font color='blue'>Si el asegurado  no está interesado  se  indaga  el motivo y se  digita en las observaciones del sistema de A.O.A.</font><br />
			<font color='blue'>Si el asegurado  esta interesado se le informa los requisitos para acceder al servicio.</font><br /><br />
			Para acceder al servicio y hacer entrega del vehículo debemos acordar una cita y los requisitos son: <br />
			<li>Presentar su cedula de ciudadanía original,
			<li>Su licencia de conducción original y
			<li>Su tarjeta de crédito con un cupo disponible de <b style='color:brown'>$ ".coma_format($A->garantia)."</b> pesos, con el fin de dejar un Boucher firmado en <b>calidad de garantía</b> mientras usted utiliza el vehículo.<br /><br />
			<font color='blue'>Si el asegurado  manifiesta no cumplir con los requisitos  se  indaga  el motivo y se busca la forma de solucionar el inconveniente para que pueda acceder al servicio;
			si definitivamente no cumple con los requisitos no se  adjudica el servicio y se digita en las observaciones del sistema de A.O.A.</font><br /><br />
			<font color='blue'>Si el asegurado  cumple con los requisitos para acceder al servicio.: </font><br />
			Le informamos que tenemos disponibilidad  para entregar el vehículo el día _______a las ____<br />
			<font color='blue'>Si el asegurado está de acuerdo con la disponibilidad se programa la cita y se informa lugar de entrega.</font><br />
			En Bogota Nuestra oficina está ubicada en el Centro Comercial Hacienda Santa Bárbara  Av. 7 No 115- 60  oficina D (de dedo) 507, le confirmo su cita queda asignada para el día ________  a las _____
			<br />LE INFORMO QUE SU SERVICIO INICIA A PARTIR DE LA HORA PACTADA EN ESTA LLAMADA.
			<br /><br />
			<font color='blue'>Para las otras ciudades:</font>
			Señor(a) <b>$S->asegurado_nombre</b> le informo que en pocos minutos lo contactará el funcionario de (<b>$Oficina</b>) para acordar el lugar y hora para la entrega del vehiculo.
			En el eje Cafetero el vehiculo se entrega en Pereira.<br />
			<font color='blue'>En caso de que el asegurado pregunte sobre qué vehículo le entregariamos:</font><br />
			Se entrega un vehículo marca Volkswagen Jetta automático 2009 color gris o su equivalente en otra marca.
			<br /><br />Muchas gracias por su tiempo y recuerde que habló con <b>".$_SESSION['Nombre']."</b>";
			break;
		case 7:
			$Guion="$Tarde Señor (a) <b>$S->asegurado_nombre</b>.<br /><br /> Le habla <b>".$_SESSION['Nombre']."</b> de AOA  (ADMISTRACION OPERATIVA  AUTOMOTRIZ);
			La empresa que le provee el <B>VEHICULO DE CORTESIA DE LIBERTY</B>;  <br />
			Le informamos que Liberty autorizó el préstamo de un vehículo por 7 días o 700 kilómetros lo primero que se cumpla; <b>este servicio no tiene ningún costo</b>.<br /><br />
	 		Señor (a) <b>$S->asegurado_nombre</b> se encuentra  interesado(a) en utilizar este servicio?.<br /><br />
			<font color='blue'>Si el asegurado  no está interesado  se  indaga  el motivo y se  digita en las observaciones del sistema de A.O.A.</font><br />
			<font color='blue'>Si el asegurado  esta interesado se le informa los requisitos para acceder al servicio.</font><br /><br />
			Para acceder al servicio y hacer entrega del vehículo debemos acordar una cita y los requisitos son: <br />
			<li>Presentar su cedula de ciudadanía original,
			<li>Su licencia de conducción original y
			<li>Su tarjeta de crédito con un cupo disponible de <b style='color:brown'>$ ".coma_format($A->garantia)."</b> pesos, con el fin de dejar un Boucher firmado en <b>calidad de garantía</b> mientras usted utiliza el vehículo.<br /><br />
			<font color='blue'>Si el asegurado  manifiesta no cumplir con los requisitos  se  indaga  el motivo y se busca la forma de solucionar el inconveniente para que pueda acceder al servicio;
			si definitivamente no cumple con los requisitos no se  adjudica el servicio y se digita en las observaciones del sistema de A.O.A.</font><br /><br />
			<font color='blue'>Si el asegurado  cumple con los requisitos para acceder al servicio.: </font><br />
			Le informamos que tenemos disponibilidad  para entregar el vehículo el día _______a las ____<br />
			<font color='blue'>Si el asegurado está de acuerdo con la disponibilidad se programa la cita y se informa lugar de entrega.</font><br />
			En Bogota Nuestra oficina está ubicada en el Centro Comercial Hacienda Santa Bárbara  Av. 7 No 115- 60  oficina D (de dedo) 507, le confirmo su cita queda asignada para el día ________  a las _____
			<br />LE INFORMO QUE SU SERVICIO INICIA A PARTIR DE LA HORA PACTADA EN ESTA LLAMADA.
			<br /><br />
			<font color='blue'>Para las otras ciudades:</font>
			Señor(a) <b>$S->asegurado_nombre</b> le informo que en pocos minutos lo contactará el funcionario de (<b>$Oficina</b>) para acordar el lugar y hora para la entrega del vehiculo.
			En el eje Cafetero el vehiculo se entrega en Pereira.<br />
			<font color='blue'>En caso de que el asegurado pregunte sobre qué vehículo le entregariamos:</font><br />
			Se entrega un vehículo marca Renault Symbol mecánico color gris o su equivalente en otra marca.
			<br /><br />Muchas gracias por su tiempo y recuerde que habló con <b>".$_SESSION['Nombre']."</b>";
			break;
		case 4:
			$Guion="$Tarde Señor (a) <b>$S->asegurado_nombre</b>.<br /><br /> Le habla <b>".$_SESSION['Nombre']."</b> de AOA  (ADMISTRACION OPERATIVA  AUTOMOTRIZ);
			La empresa que le provee el <B>VEHICULO DE CORTESIA DE MAPFRE</B>;  <br />
			Le informamos que Mapfre autorizó el préstamo de un vehículo por 7 días 1.000 kilómetros lo primero que se cumpla; <b>este servicio no tiene ningún costo</b>.<br /><br />
	 		Señor (a) <b>$S->asegurado_nombre</b> se encuentra  interesado(a) en utilizar este servicio?.<br /><br />
			<font color='blue'>Si el asegurado  no está interesado  se  indaga  el motivo y se  digita en las observaciones del sistema de A.O.A.</font><br />
			<font color='blue'>Si el asegurado  esta interesado se le informa los requisitos para acceder al servicio.</font><br /><br />
			Para acceder al servicio y hacer entrega del vehículo debemos acordar una cita y los requisitos son: <br />
			<li>Presentar su cedula de ciudadanía original,
			<li>Su licencia de conducción original y
			<li>Su tarjeta de crédito con un cupo disponible de <b style='color:brown'>$ ".coma_format($A->garantia)."</b> pesos, con el fin de dejar un Boucher firmado en <b>calidad de garantía</b> mientras usted utiliza el vehículo.<br /><br />
			<font color='blue'>Si el asegurado  manifiesta no cumplir con los requisitos  se  indaga  el motivo y se busca la forma de solucionar el inconveniente para que pueda acceder al servicio;
			si definitivamente no cumple con los requisitos no se  adjudica el servicio y se digita en las observaciones del sistema de A.O.A.</font><br /><br />
			<font color='blue'>Si el asegurado  cumple con los requisitos para acceder al servicio.: </font><br />
			Le informamos que tenemos disponibilidad  para entregar el vehículo el día _______a las ____<br />
			<font color='blue'>Si el asegurado está de acuerdo con la disponibilidad se programa la cita y se informa lugar de entrega.</font><br />
			En Bogota Nuestra oficina está ubicada en el Centro Comercial Hacienda Santa Bárbara  Av. 7 No 115- 60  oficina D (de dedo) 507, le confirmo su cita queda asignada para el día ________  a las _____
			<br />LE INFORMO QUE SU SERVICIO INICIA A PARTIR DE LA HORA PACTADA EN ESTA LLAMADA.
			<br /><br />
			<font color='blue'>Para las otras ciudades:</font>
			Señor(a) <b>$S->asegurado_nombre</b> le informo que en pocos minutos lo contactará el funcionario de (<b>$Oficina</b>) para acordar el lugar y hora para la entrega del vehiculo.
			En el eje Cafetero el vehiculo se entrega en Pereira.<br /><br />
			<font color='blue'>En caso de que el asegurado pregunte sobre qué vehículo le entregariamos:</font><br />
			Se le entregará un vehículo marca Renault Logan mecánico 2010 color gris o su equivalente en otra marca.
			<br /><br />Muchas gracias por su tiempo y recuerde que habló con <b>".$_SESSION['Nombre']."</b>";
			break;
		case 5:
			$Guion="$Tarde Señor (a) <b>$S->asegurado_nombre</b>.<br /><br /> Le habla <b>".$_SESSION['Nombre']."</b> de AOA  (ADMISTRACION OPERATIVA  AUTOMOTRIZ);
			La empresa que le provee el <B>VEHICULO DE CORTESIA DE ROYAL</B>;  <br />
			Le informamos que Royal autorizó el préstamo de un vehículo por 7 días;  <b>este servicio no tiene ningún costo</b>.<br /><br />
	 		Señor (a) <b>$S->asegurado_nombre</b> se encuentra  interesado(a) en utilizar este servicio?.<br /><br />
			<font color='blue'>Si el asegurado  no está interesado  se  indaga  el motivo y se  digita en las observaciones del sistema de A.O.A.</font><br />
			<font color='blue'>Si el asegurado  esta interesado se le informa los requisitos para acceder al servicio.</font><br /><br />
			Para acceder al servicio y hacer entrega del vehículo debemos acordar una cita y los requisitos son: <br />
			<li>Presentar su cedula de ciudadanía original,
			<li>Su licencia de conducción original y
			<li>Su tarjeta de crédito con un cupo disponible de <b style='color:brown'>$ ".coma_format($A->garantia)."</b> pesos, con el fin de dejar un Boucher firmado en <b>calidad de garantía</b> mientras usted utiliza el vehículo.<br /><br />
			<font color='blue'>Si el asegurado  manifiesta no cumplir con los requisitos  se  indaga  el motivo y se busca la forma de solucionar el inconveniente para que pueda acceder al servicio;
			si definitivamente no cumple con los requisitos no se  adjudica el servicio y se digita en las observaciones del sistema de A.O.A.</font><br /><br />
			<font color='blue'>Si el asegurado  cumple con los requisitos para acceder al servicio.: </font><br />
			Le informamos que tenemos disponibilidad  para entregar el vehículo el día _______a las ____<br />
			<font color='blue'>Si el asegurado está de acuerdo con la disponibilidad se programa la cita y se informa lugar de entrega.</font><br />
			<b>En Bogota el vehículo se entrega en las oficinas de AUTOGERMANA de la 127</b>, le confirmo su cita queda asignada para el día ________  a las _____
			<br />LE INFORMO QUE SU SERVICIO INICIA A PARTIR DE LA HORA PACTADA EN ESTA LLAMADA.
			<br /><br />
			<font color='blue'>Para las otras ciudades:</font>
			Señor(a) <b>$S->asegurado_nombre</b> le informo que en pocos minutos lo contactará el funcionario de (<b>$Oficina</b>) para acordar el lugar y hora para la entrega del vehiculo.
			En el eje Cafetero el vehiculo se entrega en Pereira.<br /><br />
			<font color='blue'>En caso de que el asegurado pregunte sobre qué vehículo le entregariamos:</font><br />
			Se le entregará un vehículo marca BMW 318 mecánico 2009 color gris o su equivalente en otra marca.
			<br /><br />Muchas gracias por su tiempo y recuerde que habló con <b>".$_SESSION['Nombre']."</b>";
			break;

	}
	return $Guion;
}

function dia_semana($d)
{
	switch($d)
	{
		case 0: return 'Domingo';
		case 1: return 'Lunes';
		case 2: return 'Martes';
		case 3: return 'Miércoles';
		case 4: return 'Jueves';
		case 5: return 'Viernes';
		case 6: return 'Sábado';
	}
}

function agendar_cita()
{
	global $id,$placa,$fecha,$hora,$oficina,$flota;
	$OF=qo("select * from oficina where id=$oficina");
	$S=qo("select * from siniestro where id=$id");
	$Ciudad=qo1("select t_ciudad($S->ciudad)");
	$Aseguradora=qo("Select * from aseguradora where id=$S->aseguradora");
	if($S->ciudad_original) $Ciudado=qo1("select t_ciudad($S->ciudad_original)"); else $Ciudado=false;
	$Observaciones=nl2br($S->observaciones);
	$Nhora=date('h:i A',strtotime($fecha.' '.$hora));
	html('AGENDAMIENTO DE CITA');
	echo "<script language='javascript'>
		function carga()
		{
			centrar(900,700);
		}
		function guardar_cita()
		{
			if(confirm('Esta seguro de Agendar esta cita?'))
			{
				document.forma.Acc.value='agendar_cita_ok';
				document.forma.submit();
			}
		}
	</script>
	<body onload='carga()' style='font-size:14' bgcolor='ffffdd'>
	<h3>Control de Seguimiento Call Center <i style='color:00000'>$Aseguradora->nombre  Póliza Número: $S->poliza Siniestro No. $S->numero </i></h3>
	Ciudad: <b>$Ciudad</b> ".($Ciudado?"Ciudad original: <b>$Ciudado</b>":"");
	echo "<br />Fecha del siniestro: <b>$S->fec_siniestro</b> Fecha de declaración del siniestro: <b>$S->fec_declaracion</b> FECHA DE AUTORIZACION: <b style='color:ff0000'>$S->fec_autorizacion</b>";
	echo "<br />Vigencia de la póliza:  Desde <b>$S->vigencia_desde</b> hasta <b>$S->vigencia_hasta</b>";
	echo "<br />Placa: <b>$S->placa</b> Marca: <b>$S->marca</b> Tipo: <b>$S->tipo</b> Línea: <b>$S->linea</b> Modelo: <b>$S->modelo</b> Clase: <b>$S->clase</b>";
	echo "<hr color='eeeeee'><b><u>ASEGURADO:</u></b> Nombre: <b>$S->asegurado_nombre</b> Identificación: <b>$S->asegurado_id</b> ";
	echo "<hr color='eeeeee'><b><u>Declarante:</u></b> Nombre: <b>$S->declarante_nombre</b> Identificación: <b>$S->declarante_id</b> Telefonos: <b>$S->declarante_telefono / $S->declarante_tel_resid
				/ $S->declarante_tel_ofic / $S->declarante_celular</b>";
	echo "<hr color='eeeeee'><b><u>Conductor:</u></b> Nombre: <b>$S->conductor_nombre</b> Telefonos: <b>$S->declarante_telefono / $S->conductor_tel_resid
				/ $S->conductor_tel_ofic / $S->conductor_celular / $S->conductor_tel_otro</b>";
	echo "<form action='zctrlseg.php' method='post' target='_self' name='forma' id='forma'>
				<hr color='eeeeee'><a href='javascript:void(null);' class='sinfo'><b><u>Observaciones registradas (pase el mouse para ver las observaciones)</u></b><span style='width:800'><font style='font-size:10;color:00000'>$Observaciones</font></span></a><br>
				<hr color='eeeeee'><H3>AGENDAMIENTO DE CITA</H3>
				<br />Oficina: <b style='color:000000'>$OF->nombre ($OF->direccion)</b> <input type='hidden' name='oficina' value='$oficina'>
				<input type='hidden' name='siniestro' id='siniestro' value='$id'>
				<input type='hidden' name='flota' id='flota' value='$flota'>
				<br /><br />Vehiculo asignado: <input type='text' name='placa' value='$placa' readonly style='font-size:12;font-weight:bold' size=7>
				Fecha y hora de la cita: <input type='hidden' name='fecha' value='$fecha'><b style='font-size:12;font-weight:bold;color:000000'>".fecha_completa($fecha)."</b> HORA:
				<input type='hidden' name='hora' value='$hora'> <b style='font-size:12;font-weight:bold;color:000000'>$Nhora</b>
				<br /><br />Persona quien va a recoger el vehículo o conductor: <input type='text' name='conductor' value='$S->asegurado_nombre' style='font-size:12;font-weight:bold' size='50'>
				<br /><br />Observaciones:
				<br /><textarea name='observaciones' id='observaciones' style='font-size:14' rows='5' cols='100'></textarea><br />
				Señor usuario ".$_SESSION['Nombre'].", el agendamiento de la cita quedará registrada a nombre suyo con fecha y hora de registro.<br /><br />
				<input type='button' id='Enviar' name='Enviar' value='AGENDAR CITA' style='font-size:14;font-weight:bold' onclick='guardar_cita()'>
				<input type='hidden' name='Acc' id='' value=''><input type='hidden' name='siniestro' id='siniestro' value='$id'>
				</form></body>";
}

function agendar_cita_ok()
{
	global $oficina,$siniestro,$flota,$placa,$fecha,$hora,$conductor,$observaciones;
	$Hoy=date('Y-m-d H:i');
	$Usuario=$_SESSION['Nombre'];
	$Dia=dia_semana(date('w',strtotime($fecha)));
	$Nciudad=qo1("select t_ciudad(ciudad) from siniestro where id=$siniestro");
	q("update siniestro set observaciones=concat(observaciones,\"\n$Usuario [$Hoy] Agenda cita para $Dia  $fecha a la(s) $hora en la ciudad de $Nciudad \"),estado=3 where id=$siniestro");
	$H1=date('Y-m-d'); $H2=date('H:i:s');
	q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($siniestro,'$H1','$H2','$Usuario','Agenda cita para $Dia $fecha a la(s) $hora con el vehículo $placa en la ciudad de $Nciudad',5)");
	$S=qo("select * from siniestro where id=$siniestro");
	$Idn=q("insert into cita_servicio (oficina,siniestro,flota,placa,fecha,hora,conductor,observaciones,agendada_por,fecha_agenda,estado)
			values ('$oficina','$siniestro','$flota','$placa','$fecha','$hora','$conductor','$observaciones','".$_SESSION['Nombre']."','".date('Y-m-d H:i:s')."','P')");
	q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "',
			'" . date('s') . "','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','cita_servicio','A','$Idn','" . $_SERVER['REMOTE_ADDR'] . "','')",$LINK);
	echo "<script language='javascript'>
	</script>
	<body><script language='javascript'>window.open('zctrlseg.php?Acc=enviar_mail_servicio&idcita=$Idn','_self');</script>
	</body>";
}

function enviar_mail_servicio()
{
	global $idcita;
	$Cita=qo("select * from cita_servicio where id=$idcita");
	$Ndia=dia_semana(date('w',strtotime($Cita->fecha))).' '.date('d',strtotime($Cita->fecha)).' de '.mes(date('m',strtotime($Cita->fecha))).' de '.date('Y',strtotime($Cita->fecha));
	$Ofi=qo("select * from oficina where id=$Cita->oficina");
	$S=qo("select * from siniestro where id=$Cita->siniestro");
	$BA=qo("select nombre from aseguradora where id=$S->aseguradora");
	$Correo="<html><body>Señor(a) $Ofi->contacto Reciba cordial saludo.<br><br>Por medio del presente e-mail se le informa oficialmente sobre la programación de cita para el dia $Ndia, ".
						"asegurado de $BA->nombre. La información se detalla a continuación:<br><br>".
						"<table border cellspacing='0'>".
						"<tr><td>Numero de Siniestro:</td><td>$S->numero</td></tr>".
						"<tr><td>Fecha y Hora de la cita:</td><td>$Ndia a las ".date('h:i A',strtotime($Cita->hora))."</td></tr>".
						"<tr><td>Vehículo Asignado:</td><td>$Cita->placa</td></tr>".
						"<tr><td>Nombre del asegurado:</td><td>$S->asegurado_nombre</td></tr>".
						"<tr><td>Nombre del declarante:</td><td>$S->declarante_nombre</td></tr>".
						"<tr><td>Telefonos del declarante:</td><td>$S->declarante_telefono $S->declarante_tel_resid $S->declarante_tel_ofic $S->declarante_celular $S->declarate_tel_otro</td></tr>".
						"<tr><td>Nombre del conductor:</td><td>$S->conductor_nombre</td></tr>".
						"<tr><td>Telefonos del conductor:</td><td>$S->conductor_tel_resid $S->conductor_tel_ofic $S->conductor_celular $S->conductor_tel_otro</td></tr>".
						"</table><br><br>Observaciones de la cita: $Cita->observaciones<br><br>Cordialmente,<br><br>".$_SESSION['Nombre']."<BR>Asesor de Servicio al Cliente<br>".
						"Call Center AOA Colombia S.A.<br>Pbx: 6293096 - 6293097 Ext 101<br>Fax: 6200967<br>contacto@aoacolombia.com<br></body></html>";
	$Correo.="\n\n Señor(a) $Ofi->contacto Reciba cordial saludo.\n\nPor medio del presente e-mail se le informa oficialmente sobre la programación de cita para el dia $Ndia, ".
						"asegurado de $BA->nombre. La información se detalla a continuación:\n\n".
						"Numero de Siniestro:   $S->numero\n".
						"Fecha y Hora de la cita: $Ndia a las ".date('h:i A',strtotime($Cita->hora))."\n".
						"Vehículo Asignado: $Cita->placa \n".
						"Nombre del asegurado: $S->asegurado_nombre\n".
						"Nombre del declarante: $S->declarante_nombre \n".
						"Telefonos del declarante: $S->declarante_telefono $S->declarante_tel_resid $S->declarante_tel_ofic $S->declarante_celular $S->declarate_tel_otro \n".
						"Nombre del conductor: $S->conductor_nombre\n".
						"Telefonos del conductor: $S->conductor_tel_resid $S->conductor_tel_ofic $S->conductor_celular $S->conductor_tel_otro\n".
						"\n\nObservaciones de la cita: $Cita->observaciones\n\nCordialmente,\n\n".$_SESSION['Nombre']."\nAsesor de Servicio al Cliente\n".
						"Call Center AOA Colombia S.A.\nPbx: 6293096 - 6293097 Ext 101\nFax: 6200967\ncontacto@aoacolombia.com\n<br><br>".
						"\n\nNOTA: Este correo se envia en formato html y en formato plano para compatibilidad con los distintos sistemas de correo.";
	enviar_mail('contacto@aoacolombia.com' /* de */,
	"CITA PROGRAMADA " /*nombre de */,
	"$Ofi->email_e,contacto@aoacolombia.com" /*para*/,
	'CITA PROGRAMADA '.$Ndia /*subject*/ ,
	$Correo /*contenido*/);
	echo "<script language='javascript'>
	</script>
	<body onunload='opener.location.reload();'>Envio de mail $Ofi->contacto
	<br /><br /><center><input type='button' value='Cerrar esta Ventana' onclick='window.close();void(null);' style='font-family:18;font-weight:bold'></center></body>";
}

function call_center_noadj()
{
	global $id,$observaciones,$USUARIO,$Nusuario,$Hoy,$Causal,$TS;
	$H1=date('Y-m-d');
	$H2=date('H:i:s');
	q("update siniestro set observaciones=concat(observaciones,'\n$Nusuario [$Hoy]:$observaciones'),causal='$Causal',estado=1 where id=$id");
	q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'$H1','$H2','$Nusuario','$observaciones',$TS)");
	q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'$H1','$H2','$Nusuario','Cambia estado a No adjudicado',6)");
	q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."',
			'".date('s')."','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','siniestro','M','$id','".$_SERVER['REMOTE_ADDR']."','Observaciones, No adjudica')");
	echo "<script language='javascript'>
		function carga()
		{
			window.close();void(null);
			opener.parent.location.reload();
		}
	</script>
	<body onload='carga()'></body>";
}

function envia_info_erronea()
{
	global $ids,$destino;
	$Numero_Aviso=qo1("select count(id) from seguimiento where siniestro=$ids and tipo=4")+1;
	$Email_usuario=usuario('email');
	$S=qo("select * from siniestro where id=$ids");
	$Oficina=qo1("select id from oficina where ciudad='$S->ciudad'");
	$Ciudad=qo1("select t_ciudad('$S->ciudad')");
	$Aseguradora=qo("Select * from aseguradora where id=$S->aseguradora");
	if($S->ciudad_original) $Ciudado=qo1("select t_ciudad('$S->ciudad_original')"); else $Ciudado=false;
	html('ENVIO MENSAJE INFORMACION ERRONEA DE CONTACTO');
	echo "
<script language='javascript'>
	function enviar_mensaje()
	{
		document.forma.submit();
	}
</script>
<body><script language='javascript'>centrar(800,600);</script>
<h3>Envio de E-mail a: $destino</h3>
<br />
<hr color='eeeeee'><br>
<form action='zctrlseg.php' method='post' target='_self' name='forma' id='forma'>
<input type='hidden' name='destinatario' id='destinatario' value='$destino'>

Asunto del mensaje: <input type='text' name='asunto' value='Información Erronea Sinisetro $S->numero' size='50'><br><br>
Mensaje: <br />
<textarea name='mensaje' id='mensaje' cols='100' rows='15'>Reciban cordial saludo. Por medio del presente informamos que los datos suministrados correspondientes al Siniestro Número: <b>$S->numero</b> son erróneos o inconsistentes.

AVISO NUMERO: $Numero_Aviso

Póliza Número: <b>$S->poliza</b>
Ciudad: <b>$Ciudad</b> ".($Ciudado?"
Ciudad original: <b>$Ciudado</b>":"")."
Fecha del siniestro: <b>$S->fec_siniestro</b>
Fecha de declaración del siniestro: <b>$S->fec_declaracion</b>
FECHA DE AUTORIZACION: <b>$S->fec_autorizacion</b>
Vigencia de la póliza:  Desde <b>$S->vigencia_desde</b> hasta <b>$S->vigencia_hasta</b>
Placa: <b>$S->placa</b> Marca: <b>$S->marca</b> Tipo: <b>$S->tipo</b> Línea: <b>$S->linea</b> Modelo: <b>$S->modelo</b> Clase: <b>$S->clase</b>
ASEGURADO: Nombre: <b>$S->asegurado_nombre</b> Identificación: <b>$S->asegurado_id</b>
Declarante: Nombre: <b>$S->declarante_nombre</b> Identificación: <b>$S->declarante_id</b>
Telefonos: <b>$S->declarante_telefono</b> / <b>$S->declarante_tel_resid</b> / <b>$S->declarante_tel_ofic</b> / <b>$S->declarante_celular</b>
Conductor: Nombre: <b>$S->conductor_nombre</b>
Telefonos: <b>$S->declarante_telefono</b> / <b>$S->conductor_tel_resid</b> / <b>$S->conductor_tel_ofic</b> / <b>$S->conductor_celular</b> / <b>$S->conductor_tel_otro</b>

NOTA.:

Solicitamos el favor de retornar lo más pronto posible la información correcta através de este mismo medio, a las siguientes direcciones de correo electrónico:

A: controloperativo@aoacolombia.com
CC: $Email_usuario

Cordialmente,

<b>Departamento de Call Center
Administración Operativa Automotriz S.A.</b>
</textarea><br /><br />
La información erronea es: <input type='text' name='info_erronea1' value=' el número telefónico o celular' size=80><br><br>
<input type='hidden' name='Acc' id='Acc' value='enviar_info_erronea_ok'>

<center><input type='button' value='ENVIAR MENSAJE' onclick='enviar_mensaje()'>
<input type='hidden' name='ids' id='ids' value='$ids'>
</form></body>";
}

function enviar_info_erronea_ok()
{
	global $mensaje,$asunto,$destinatario,$ids,$info_erronea1;
	$mensaje=str_replace('NOTA.:','NOTA: Los datos con posibilidad de error son '.$info_erronea1,$mensaje);
	$Email_usuario=usuario('email');
	include_once('inc/Mail/SMTP.php');
	$Conexion_smtp=SMTP::Connect('mail.aoacolombia.com',25,'sistemas@aoacolombia.com','Sistemas2010');
	$m="From: $Email_usuario\nTo: $destinatario,gabrielsandoval@aoacolombia.com,controloperativo@aoacolombia.com\nSubject: $asunto\nContent-Type: text/html\n\n".
	"<body>".nl2br($mensaje)."</body>";
	$Destino=array();
	$Destino[0]='gabrielsandoval@aoacolombia.com';
	$Destino[1]='controloperativo@aoacolombia.com';
	$Destino[2]=$Email_usuario;
	$destinos=explode(',',$destinatario);
	for($i=0;$i<count($destinos);$i++) $Destino[$i+3]=$destinos[$i];
	for($i=0;$i<count($Destino);$i++) echo "<br />$i ".$Destino[$i];
	$s1=SMTP::Send($Conexion_smtp,$Destino,$m,$Email_usuario);
	SMTP::disconnect($Conexion_smtp);
	q("update siniestro set info_erronea=1,observaciones=concat(observaciones,'\n".$_SESSION['Nombre'].' '.date('Y-m-d H:i')." Se remite a $destinatario por información de contacto erronea') where id='$ids'");
	$H1=date('Y-m-d'); $H2=date('H:i:s');
	q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($ids,'$H1','$H2','".$_SESSION['Nombre']."','Se remite a $destinatario por información de contacto erronea',4)");
	if($s1)
		echo "<script language='javascript'>
				function carga()
				{
					alert('Generación de Email hecha satisfactoriamente');
					window.close();void(null);
				}
			</script>
			<body onload='carga()'></body>";
}

function solicita_activacion()
{
	global $Fecha,$Placa,$Siniestro;
	$Sin=qo("select * from siniestro where id=$Siniestro");
	$Ciudad=qo("select * from ciudad where codigo='$Sin->ciudad' ");
	$Ruta1="utilidades/Operativo/operativo.php?Acc=activar_sinlogo&Placa=$Placa&Fecha=$Fecha&Usuario=GABRIEL SANDOVAL";
	$Email_usuario=usuario('email');
	include_once('inc/Mail/SMTP.php');
	$c=SMTP::Connect('mail.aoacolombia.com',25,'sistemas@aoacolombia.com','Sistemas2010');
	$m="From: $Email_usuario\nTo: gabrielsandoval@aoacolombia.com\nSubject: Solicitud Activacion $Placa Flota AOA\nContent-Type: text/html\n\n".
		"<body>Solicitud de Activación Vehículo de AOA<br><br>Placa: <b>$Placa</b><br>Fecha: <b>$Fecha</b><br>".
		"Funcionario que solicita: ".$_SESSION['Nombre']." <br>Siniestro: <b>$Sin->numero $Sin->asegurado_nombre</b><br>Ciudad: <b>$Ciudad->nombre ($Ciudad->departamento)</b><br>".
		"Para activar haga click aquí: <a href='http://174.132.105.98/~aoacol/i.php?i=".base64_encode("\$Programa='$Ruta1';\$Fecha_control='".date('Y-m-d')."';")."' target='_blank'>Autorizar</a></body>";
	$s1=SMTP::Send($c,array('gabrielsandoval@aoacolombia.com'),$m,$Email_usuario);

	$m="From: $Email_usuario\nTo: henrygonzalez@aoacolombia.com\nSubject: Solicitud Activacion $Placa Flota AOA\nContent-Type: text/html\n\n".
		"<body>Solicitud de Activación Vehículo de AOA<br><br>Placa: <b>$Placa</b><br>Fecha: <b>$Fecha</b><br>".
		"Funcionario que solicita: ".$_SESSION['Nombre']." <br>Siniestro: <b>$Sin->numero $Sin->asegurado_nombre</b><br>Ciudad: <b>$Ciudad->nombre ($Ciudad->departamento)</b><br>".
		"Para activar haga click aquí: <a href='http://174.132.105.98/~aoacol/i.php?i=".base64_encode("\$Programa='$Ruta1';\$Fecha_control='".date('Y-m-d')."';")."' target='_blank'>Autorizar</a></body>";
	$s3=SMTP::Send($c,array('henrygonzalez@aoacolombia.com'),$m,$Email_usuario);
	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$m='From: '.$Email_usuario."\n".'To: '.$Email_usuario."\n".
	'CC: arturoquintero@aoacolombia.com'."\n".
	'Subject: Solicitud Activacion '.$Placa.' Flota AOA'."\n".'Content-Type: text/html'."\n\n".
	"<body>Solicitud de Activación Vehículo de AOA<br><br>Placa: <b>$Placa</b><br>Fecha: <b>$Fecha</b><br>".
		"Funcionario que solicita: ".$_SESSION['Nombre']." <br>Siniestro: <b>$Sin->numero $Sin->asegurado_nombre</b><br>Ciudad: <b>$Ciudad->nombre ($Ciudad->departamento)</b><br>".
		"Para activar haga click aquí: <a href='http://174.132.105.98/~aoacol/i.php?i=".base64_encode("\$Programa='$Ruta1';\$Fecha_control='".date('Y-m-d')."';")."' target='_blank'>Autorizar</a></body>";
	$s2=SMTP::Send($c,array('arturoquintero@aoacolombia.com'),$m,$Email_usuario);
	SMTP::disconnect($c);
	if($s1 && $s2 && $s3)
		echo "Envio exitoso a: gabrielsandoval@aoacolombia.com, arturoquintero@aoacolombia.com, henrygonzalez@aoacolombia.com, $Email_usuario ";
	 else
	 	echo "Falla en el envío del mail.";

}

function contacto_exitoso()
{
	global $ids;
	$Ahora=date('Y-m-d H:i:s');
	q("update siniestro set contacto_exitoso='$Ahora' where id=$ids");
	echo "<script language='javascript'>
			function carga()
			{
				alert('Marcación de Contacto exitoso Realizada satisfactoriamente');
				window.close();void(null);
			}
		</script>
		<body onload='carga()'></body>";
}

function solicita_reactivacion()
{
	global $id;
	$Sin=qo("select * from siniestro where id=$id");
	$Ciudad=qo("select * from ciudad where codigo='$Sin->ciudad' ");
	if($Sin->ciudad_original) $Ciudado=qo("select * from ciudad where codigo='$Sin->ciudad_original' ");
	else $Ciudado=$Ciudad;
	$Nusuario=$_SESSION['Nombre'];
	$Fecha=date('Y-m-d H:i');
	html('Solicitud de Modificación');
	$Ciudades=menu1("ciudad","select ciu.codigo,ciu.nombre as nciudad from ciudad ciu,oficina ofi where ofi.ciudad=ciu.codigo order by nciudad ",$Ciudad,1);
	echo "<script language='javascript'>
			function carga() {centrar(500,500);}
			function activaruno() { if(document.forma.uno.checked) {document.getElementById('tduno').style.visibility='visible';document.forma.justificacion1.focus();} else document.getElementById('tduno').style.visibility='hidden'; }
			function activardos() { if(document.forma.dos.checked) document.getElementById('tddos').style.visibility='visible'; else document.getElementById('tddos').style.visibility='hidden'; }
			function enviar_solicitud()
			{
				with(document.forma)
				{
					if(uno.checked)
					{
						if(!alltrim(justificacion1.value))
						{
							alert('Debe justificar por qué se desea pasar a Pendiente el estado de este siniestro');
							justificacion1.style.backgroundColor='ffffdd';
							return false;
						}
					}
					if(dos.checked)
					{
						if(!alltrim(justificacion2.value))
						{
							alert('Debe justificar por qué se desea cambiar de ciudad este siniestro');
							justificacion2.style.backgroundColor='ffffdd';
							return false;
						}
						if(!ciudad.value)
						{
							alert('Debe especificar una ciudad válida');
							ciudad.style.backgroundColor='ffffdd';
							return false;
						}
					}
					if(!(uno.checked || dos.checked))
					{
						alert('La solicitud debe contener uno o los dos conceptos que son Reactivación o Cambio de Ciudad');
						return false;
					}
				}
				document.forma.btn_enviar.style.visibility='hidden';
				document.forma.submit();
			}
		</script>
		<body onload='carga()'>
		<form action='zctrlseg.php' method='post' target='_self' name='forma' id='forma'>
		<h3>Solicitud de Modificación de Siniestro</h3>
		Usuario: $Nusuario   Fecha: $Fecha <br />
		<table border cellspacing='0' width='100%'>
			<tr><td>Reactivación <input type='checkbox' name='uno' onchange='activaruno();' ".($Sin->estado==1?'':"disabled")."> El estado actual es: <b>".qo1("select t_estado_siniestro($Sin->estado)")."</b></td></tr>
			<tr><td  id='tduno' style='visibility:hidden;'>Esta opción solicita pasar el estado a PENDIENTE por favor escriba la justificación: <br /><textarea name='justificacion1' style='font-family:arial;font-size:12' rows='4' cols='80' valign='top'></textarea></td></tr>
			<tr><td>Cambio de Ciudad <input type='checkbox' name='dos' onchange='activardos();'></td></tr>
			<tr><td id='tddos' style='visibility:hidden;'>Ciudad: $Ciudades Justificación: <br><textarea name='justificacion2' style='font-family:arial;font-size:12' rows='4' cols='80' valign='top'></textarea></td></tr>
		</table>
		<center><input type='button' id='btn_enviar' name='btn_enviar' value='Enviar Solicitud' onclick='enviar_solicitud()'></center>
		<input type='hidden' name='id' value='$id'><input type='hidden' name='Acc' value='solicita_reactivacion_ok'>
	</form>";

	die();
}

function solicita_reactivacion_ok()
{
	global $id,$uno,$dos,$justificacion1,$justificacion2,$ciudad,$Nusuario,$Hoy;

	$uno=sino($uno);
	$dos=sino($dos);
	$IDM=q("insert into solicitud_modsin (siniestro,cambio_estado,justificacion1,cambio_ciudad,ciudad,justificacion2,solicitado_por,fec_solicitud) values
		('$id','$uno',\"$justificacion1\",'$dos','$ciudad',\"$justificacion2\",'$Nusuario','$Hoy' )");
	$Nueva_ciudad=qo1("select t_ciudad('$ciudad') ");
	$Sin=qo("select id,numero,asegurado_nombre,ciudad,ciudad_original,aseguradora,fec_autorizacion from siniestro where id=$id");
	$Ciudad=qo("select * from ciudad where codigo='$Sin->ciudad' ");
	if($Sin->ciudad_original) $Ciudado=qo("select * from ciudad where codigo='$Sin->ciudad_original' ");
	else $Ciudado=$Ciudad;
	$Aseguradora=qo1("select nombre from aseguradora where id=$Sin->aseguradora");
	$Ruta1="utilidades/Operativo/operativo.php?Acc=modificar_siniestro&idm=$IDM&Fecha=$Hoy&Usuario=GABRIEL SANDOVAL";
	$Email_usuario=usuario('email');
	include_once('inc/Mail/SMTP.php');
	$ConSMTP=SMTP::Connect('mail.aoacolombia.com',25,'sistemas@aoacolombia.com','Sistemas2010');
	$Mensaje="From: $Email_usuario\nTo: gabrielsandoval@aoacolombia.com,germangonzalez@aoacolombia.com,henrygonzalez@aoacolombia.com,arturoquintero@aoacolombia.com\nSubject: SOLICITUD MODIFICACION SINIESTRO $Sin->numero\nContent-Type: text/html\n\n";
//	$Mensaje="From: $Email_usuario\nTo: arturoquintero@aoacolombia.com\nSubject: Solicitud Modificación Siniestro $Sin->numero\nContent-Type: text/html\n\n";
	$Mensaje.="<body><b>SOLICITUD DE MODIFICACION DE SINIESTROS</B><BR><BR>Numero Siniestro: $Sin->numero - $Aseguradora<br>".
	"Asegurado:$Sin->asegurado_nombre<br>Ciudad: $Ciudad->nombre ($Ciudad->departamento) <br>Ciudad Original: $Ciudado->nombre ($Ciudado->departamento)<br>".
	"Fecha de autorización: $Sin->fec_autorizacion<br>";
	if($uno)
	{
		$Mensaje.="<br><b>Cambio de estado a PENDIENTE: </b>$justificacion1<br>";
	}
	if($dos)
	{
		$Mensaje.="<br><b>Cambio de ciudad a $Nueva_ciudad: </b>$justificacion2<br>";
	}
	$Mensaje.="<br>Funcionario que solicita: $Nusuario Fecha de solicitud: $Hoy <br><br>";
	$Mensaje.="Para activar haga click aquí: <a href='http://174.132.105.98/~aoacol/i.php?i=".base64_encode("\$Programa='$Ruta1';\$Fecha_control='".date('Y-m-d')."';")."' target='_blank'>Aprobar la modificación</a></body>";

//	$Envio=SMTP::Send($ConSMTP,array('arturoquintero@aoacolombia.com'),$Mensaje,$Email_usuario);
	$Envio=SMTP::Send($ConSMTP,array('gabrielsandoval@aoacolombia.com','germangonzalez@aoacolombia.com','henrygonzalez@aoacolombia.com','arturoquintero@aoacolombia.com',$Email_usuario),$Mensaje,$Email_usuario);

	SMTP::disconnect($ConSMTP);
	if($Envio)
		echo "Envio exitoso a: gabrielsandoval@aoacolombia.com, germangonzalez@aoacolombia.com, henrygonzalez@aoacolombia.com, arturoquintero@aoacolombia.com, $Email_usuario";
	else
		echo "Falla en el envío del mail.";
	echo "<br /><br /><CENTER><INPUT TYPE='BUTTON' VALUE='CERRAR ESTA VENTANA' onclick='window.close();void(null);' style='font-size:16;font-weight:bold; height=30px;'></center>";
}














?>