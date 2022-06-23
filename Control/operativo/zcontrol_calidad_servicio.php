<?php
/* SISTEMA DE CONTROL DE CALIDAD AL SERVICIO DE VEHICULO DE REEMPLAZO DE AOA
 * Este sistema se usa para controlar que el servicio haya sido ofrecido de forma correcta y conveniente.
 * Consiste en hallar los siniestros que tuvieron servicio y estén en estado CONCLUIDO, se efectua una llamada
 * para determinar con el cliente si el servicio fue o no ejecutado de forma correcta.
 * Cualquier aclaración o inconveniente se registra en una tabla de llamadas.
 * La tabla de llamadas debe estar conectada con el registro de siniestros que han sido evaluados, debe contener
 * las observaciones y no debe ser modificado después de su grabación.
 */

include('inc/funciones_.php');
sesion();

if(!empty($Acc) && function_exists($Acc)) { eval($Acc.'();'); die(); }
html(TITULO_APLICACION.' - CONTROL DE CALIDAD AL SERVICIO - '.$_SESSION['Nombre']);
echo "<script language='javascript'>

		function carga()
		{
			var DD=document.getElementById('Llamada');
			DD.height=document.body.clientHeight-50;
		}
		function activa_llamada(Id)
		{
			carga();
			var DD=document.getElementById('Llamada');

			DD.contentWindow.location='zcontrol_calidad_servicio.php?Acc=presenta_siniestro&id='+Id;
			DD.style.visibility='visible';
		}
	</script>
	<body onload='carga()' >
		<h3 align='center'>CONTROL DE CALIDAD AL SERVICIO</h3>
		<iframe name='Llamada' id='Llamada' height=100 width='98%'
			style='visibility:hidden;position:fixed;top:0;left:0;border-style:solid;border-width:2px;background-color:#fdfdfd;z-index:119;' border='1' frameborder='yes'>
		</iframe>";
busca_concluidos();
echo "</body>
	</html>";


function busca_concluidos()
{
	q("update siniestro s, calidad_servicio c set s.control_calidad=1 where s.id=c.siniestro and s.control_calidad=0");
	$Concluidos=q("select id,aseguradora, t_aseguradora(aseguradora) as naseguradora,numero,t_ciudad(ciudad) as ciudad,
		poliza,asegurado_nombre,conductor_nombre,fecha_final
		FROM siniestro
		WHERE estado=8 and control_calidad=0
		ORDER BY fecha_final ");
	html();
	echo "<body bgcolor='eeeeee'>";
	if($Concluidos)
	{
		$Contador=1;
		echo "<table bgcolor='ffffff' cellspacing=3><tr>
			<th>#</th>
			<th>Fec.Conclusión</th>
			<th>Aseguradora</th>
			<th>Ciudad</th>
			<th>Numero Siniestro</th>
			<th>Asegurado</th>
			<th>Conductor</th>
			</tr>";
		while($C=mysql_fetch_object($Concluidos))
		{
			echo "<tr onclick='activa_llamada($C->id);'>
				<td align='right'>$Contador</td>
				<td align='center'>$C->fecha_final</td>
				<td align='left'>$C->naseguradora</td>
				<td align='left'>$C->ciudad</td>
				<td align='right'>$C->numero</td>
				<td align='left'>$C->asegurado_nombre</td>
				<td align='left'>$C->conductor_nombre</td>
				</tr>";
			$Contador++;
		}
		echo "</table>";
	}
	else
	{
		echo "<center><font color='red'><b>No se encuentran siniestros concluidos por el momento</b></center>";
	}
}

function presenta_siniestro()
{
	global $id;
	html();
	echo "<script language='javascript'>
			function cierra_llamada()
			{
				var DD=parent.document.getElementById('Llamada');
				DD.style.visibility='hidden';
			}

			function carga()
			{
				document.getElementById('i_enc').height=document.body.clientHeight-50;
				document.getElementById('i_enc').width=document.body.clientWidth/2;
			}

			function validar_control()
			{
				if(document.forma.observaciones.value)
				{
					document.forma.submit();
				}
				else
				{
					alert('Debe digitar las observaciones de control de calidad');
				}
			}
			</script>";
	if($id)
	{
		$S=qo("select * from siniestro where id=$id");
		$A=qo("select * from aseguradora where id=$S->aseguradora");
		$U=qo("select *,t_vehiculo(vehiculo) as nvehiculo from ubicacion where id=$S->ubicacion");
		$Ciudad=qo1("select t_ciudad('$S->ciudad')");
		echo "<body onload='carga()' >
			<input type='button' value='Cerrar ventana' style='width:200px' onclick='cierra_llamada()'>
			<table width='100%'>
				<tr>
					<td width='52%' valign='top'>";
		if($S->img_encuesta_f)
			echo "<iframe id='i_enc' src='$S->img_encuesta_f' width=300 height=300></iframe>";
		else
			echo "No tiene imagen de encuesta";
		echo "</td>
					<td valign='top'>
						Aseguradora: <b>$A->nombre</b><br>
						Numero de Siniestro: <b>$S->numero</b> Número de póliza: <b>$S->poliza</b><br>
						Ciudad: <b>$Ciudad</b><br>
						Fecha de Autorización: <b>$S->fec_autorizacion</b><br>
						Fecha del Siniestro: <b>$S->fec_siniestro</b><br>
						Observaciones generales: <b>$S->observaciones</b><br>
						<b>DATOS DEL CLIENTE</b><br>
						Asegurado: <b>$S->asegurado_nombre</b><br>
						Declarante: <b>$S->declarante_nombre</b> Teléfonos: <b>$S->declarante_telefono - $S->declarante_tel_resid -
						$S->declarante_tel_ofic - $S->declarante_celular - $S->declarate_tel_otro</b><br>
						Conductor: <b>$S->conductor_nombre</b> Teléfonos: <b>$S->conductor_tel_resid - $S->conductor_tel_ofic -
						$S->conductor_celular - $S->conductor_tel_otro</b><br>
						<b>VEHICULO DEL CLIENTE:</B><BR>
						Placa: <b>$S->placa</b> Marca: <b>$S->marca</b> Tipo: <b>$S->tipo</b>
						Línea: <b>$S->linea</b> Modelo: <b>$S->modelo</b> Clase: <b>$S->clase</b><br><br>
						<b><u>VEHICULO DE AOA</u></b><br>
						Vehiculo: <b>$U->nvehiculo</b> Servicio desde <b>$U->fecha_inicial</b> hasta <b>$U->fecha_final</b><br>
						Observaciones: <i>Generales:</i> <b>$U->observaciones</b> | <i>Mantenimiento:</i> <b>$U->obs_mantenimiento</b><br>
						".($O->img_adicional_f?"<a style='cursor:pointer;' onclick=\"document.getElementById('i_enc').contentWindow.location='$S->img_adicional_f';\"><u>Ver Factura</u></a>":"")."
						<a style='cursor:pointer;' onclick=\"document.getElementById('i_enc').contentWindow.location='$S->img_inv_salida_f';\"><u>Ver Inventario entrega</u></a>
						<a style='cursor:pointer;' onclick=\"document.getElementById('i_enc').contentWindow.location='$S->img_inv_entrada_f';\"><u>Ver Inventario devolución</u></a>
						<a style='cursor:pointer;' onclick=\"document.getElementById('i_enc').contentWindow.location='$S->img_encuesta_f';\"><u>Ver Encuesta</u></a><br>

						<form action='zcontrol_calidad_servicio.php' method='post' target='_self' name='forma' id='forma'>
							<h3>Tabulación:</h3>
							<table><tr><td valign='top'>
							<b>Hasta junio 30/2011</b><br />
							Primera pregunta: ".menu1('encuesta_1',"select id,texto from valor_encuesta where pregunta=1 order by orden",$S->encuesta_1,1)."<br />
							Segunda pregunta: ".menu1('encuesta_2',"select id,texto from valor_encuesta where pregunta=2 order by orden",$S->encuesta_2,1)."<br />
							Tercera pregunta: ".menu1('encuesta_3',"select id,texto from valor_encuesta where pregunta=3 order by orden",$S->encuesta_3,1)."<br />
							Cuarta pregunta: ".menu1('encuesta_4',"select id,texto from valor_encuesta where pregunta=4 order by orden",$S->encuesta_4,1)."<br />
							Quinta pregunta: ".menu1('encuesta_5',"select id,texto from valor_encuesta where pregunta=5 order by orden",$S->encuesta_5,1)."<br />
							</td><td valign='top'>
							<b>A partir de julio 1 de 2011</b><br />
							Primera pregunta: ".menu1('encuesta_11',"select id,texto from valor_encuesta where pregunta=11 order by orden",$S->encuesta_11,1)."<br />
							Segunda pregunta: ".menu1('encuesta_12',"select id,texto from valor_encuesta where pregunta=12 order by orden",$S->encuesta_12,1)."<br />
							Tercera pregunta: ".menu1('encuesta_13',"select id,texto from valor_encuesta where pregunta=13 order by orden",$S->encuesta_13,1)."<br />
							Cuarta pregunta: ".menu1('encuesta_14',"select id,texto from valor_encuesta where pregunta=14 order by orden",$S->encuesta_14,1)."<br />
							Quinta pregunta: ".menu1('encuesta_15',"select id,texto from valor_encuesta where pregunta=15 order by orden",$S->encuesta_15,1)."<br />
							Sexta pregunta: ".menu1('encuesta_16',"select id,texto from valor_encuesta where pregunta=16 order by orden",$S->encuesta_16,1)."<br />

							</td></tr></table>
							<br>OBSERVACIONES:<br>
							<textarea name='observaciones' rows=5 cols=80></textarea><br>
							<input type='button' value='GUARDAR OBSERVACIONES' onclick='validar_control()'>
							<input type='hidden' name='Acc' value='guardar_obs'>
							<input type='hidden' name='aseguradora' value='$A->id'>
							<input type='hidden' name='siniestro' value='$id'>
						</FORM>
						<script language='javascript'>
							document.forma.observaciones.focus();
						</script>
					</td>
				</tr>
			</table>
			</body>
			</html>";
	}
	else
	{

	echo "<body><br><br>No hay información correspondiente al siniestro. O no se seleccionó ninguno.
			<br><br>
			<input type='button' value='Cerrar esta ventana' onclick='cierra_llamada()'>";
	}
}

function guardar_obs()
{
	global $observaciones,$aseguradora,$siniestro,$encuesta_2,$encuesta_3,$encuesta_4,$encuesta_5,$encuesta_1;
	$Hoy=date('Y-m-d H:i:s');$Usuario=$_SESSION['Nick'];
	q("insert into calidad_servicio (aseguradora,siniestro,fecha,observaciones,usuario) values
	('$aseguradora','$siniestro','$Hoy','$observaciones','$Usuario')");
	q("update siniestro set encuesta_1='$encuesta_1',encuesta_2='$encuesta_2',encuesta_3='$encuesta_3',encuesta_4='$encuesta_4',encuesta_5='$encuesta_5' where id=$siniestro ");
	echo "<script language='javascript'>
		function carga()
		{
			parent.location='zcontrol_calidad_servicio.php';
//			parent.document.getElementById('Llamada').style.visibility='hidden';
		}
		</script>
		<body onload='carga();'></body></html>";
}

?>