<?php

/**
 *   PROGRAMA DE CONTROL DE ALISTAMIENTOS
 *
 * @version $Id$
 * @copyright 2010
 */
include('inc/funciones_.php');
sesion();

if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}

function registrar_desde_citas()
{
	global $P,$F;
	if($P) $id=qo1("select id from vehiculo where placa='$P' ");
	$Vehiculo=qo("select *,t_linea_vehiculo(linea) as nlinea from vehiculo where id=$id");
	html("CONTROL DE ALISTAMIENTO: $Vehiculo->placa ");
	echo "<script language='javascript'>
			function fija()
			{
				document.getElementById('dalista').style.height=document.body.clientHeight-50;
			}
			function pinta_detalle()
			{
				window.open('zalistamiento.php?Acc=ver_pendientes&id=$id','dalista');
			}
			function traslada_contenido(c)
			{
				opener.document.$F.ords.value+=c+'. ';
				opener.document.$F.Siniestro_propio.style.visibility='visible';
				opener.document.getElementById('gr').style.visibility='visible';
			}
		</script>
		<body onresize='fija()' onload='pinta_detalle()'><script language='javascript'>centrar(600,600);fija();</script>
		<h3>CONTROL DE ALISTAMIENTO: $Vehiculo->placa  $Vehiculo->nlinea Modelo: $Vehiculo->modelo</h3>
		<iframe  name='dalista' id='dalista' width='100%' height='450px'></iframe>
		</body>";
}

function ver_pendientes()
{
	global $id;
	$Vehiculo=qo("select *,t_linea_vehiculo(linea) as nlinea from vehiculo where id=$id");
	html('');
	echo "<script language='javascript'>
					function validar_nuevo_alistamiento()
					{
						with(document.forma)
						{
							if(!alltrim(descripcion.value))
							{
								alert('Debe escribir la descripción del alistamiento');
								descripcion.style.backgroundColor='ffffdd';
								descripcion.focus();
								return false;
							}
							submit();
						}
					}

					function cumplir_alistamiento(id)
					{
						if(confirm('Desea dar cumplimiento a este alistamiento?'))
						{
							document.getElementById('if_alistamiento2').style.visibility='visible';
							window.open('zalistamiento.php?Acc=cumplir_alistamiento&id='+id,'if_alistamiento2');
						}
					}
					function ocultar_cumplimiento()
					{
						document.getElementById('if_alistamiento2').style.visibility='hidden';
					}
					function recargar()
					{
						window.open('zalistamiento.php?Acc=ver_pendientes&id=$id','_self');
					}
					function adiciona_obs(id)
					{
						var Obs=prompt('Observaciones:','');
						Obs=alltrim(Obs);
						if(Obs.length) {window.open('zalistamiento.php?Acc=inserta_obs&id='+id+'&Obs='+Obs,'if_alistamiento2');}
					}
					function imprimir_formato(id)
					{
						modal('zalistamiento.php?Acc=imprimir_formato&id='+id,0,0,700,900,'formato_alistamiento');
					}

			</script>
			<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0'>";
	if(inlist($_SESSION['User'],'1,2,7,10,13,27,23'))
	$Linea=qo("select * from linea_vehiculo where id=$Vehiculo->linea");
	$Marca=qo("select * from marca_vehiculo where id=$Linea->marca");
	echo "<form action='zalistamiento.php' method='post' target='_self' name='forma' id='forma'>
				<table align='center' cellspacing='0'><tr>
				<td align='center' colspan='1' class='placa' style='background-image:url(img/placa.jpg);'>$Vehiculo->placa</td></tr>
				<tr><td>Marca: <b>$Marca->nombre</b> Linea: <b>$Linea->nombre</b> <img src='$Linea->emblema_f' border='0' height='30'></td></tr>
				</table>
				<table align='center' bgcolor='dadada'>
				<tr><td colspan=2 align='center'><b>REGISTRAR UN NUEVO ALISTAMIENTO</b></td></tr>
				<tr><td align='right'>Fecha</td><td><input type='text' name='fecha' value='' readonly></td></tr>
				<tr><td align='right'>Programado por:</td><td><input type='text' name='programado_por' value='' readonly size='80'></td></tr>
				<tr><td align='right'>Descripción:</td><td><textarea name='descripcion' cols=80 rows=2 style='font-family:arial;font-size:11'>Vehículo pasa a revisión general.</textarea></td></tr>
				<tr><td align='center' colspan=2><input type='button' value='Grabar Alistamiento' style='width:200px;height:25px' onclick='validar_nuevo_alistamiento();'></td></tr>
				</table>
				<input type='hidden' name='Acc' value='registrar_alistamiento_ok'>
				<input type='hidden' name='Vehiculo' value='$id'>
				<input type='hidden' name='Retorno' value='zalistamiento.php?Acc=ver_pendientes&id=$id'>
				</form><hr>
				<h1 ALIGN='CENTER'>PENDIENTES ALISTAMIENTO</H1>
				<script language='javascript'>
					with(document.forma)
					{
						fecha.value='".date('Y-m-d H:i:s')."';
						programado_por.value='".$_SESSION['Nombre']."';
						descripcion.focus();
					}
				</script>";

	if($As=q("select *,t_operario(asignado_a) as noper from alistamiento where vehiculo=$id and fecha_cumplimiento='0000-00-00 00:00:00' order by fecha desc "))
	{
		echo "<b>Historico de alistamientos del vehiculo</b>
					<iframe name='if_alistamiento2' id='if_alistamiento2' style='position:fixed;visibility:hidden;' height='300' width='500'></iframe>
					<table bgcolor='eeeeee'><tr>
						<th>Fecha</th>
						<th>Programado por</th>
						<th width='200px'>Descripción</th>
						<th>Asignado a</th>
						<th>Fecha Cumplimiento</th>
						</tr>";
		while($A=mysql_fetch_object($As))
		{
			echo "<tr>
						<td nowrap='yes'>$A->fecha</td>
						<td nowrap='yes'>$A->programado_por</td>
						<td width='200px'>$A->descripcion <a class='info' style='cursor:pointer' onclick='adiciona_obs($A->id)'><img src='gifs/mas.gif' border='0'><span>Adicionar Observaciones</span></a></td>
						<td nowrap='yes'>$A->noper</td>
						<td  nowrap='yes' align='center'>";
			if($A->fecha_cumplimiento!='0000-00-00 00:00:00')
				echo $A->fecha_cumplimiento;
			else
			{
				echo demora_cumplimiento($A->fecha);
				if(inlist($_SESSION['User'],'1,2,7,10,13,27,23'))
					echo " <a onclick='cumplir_alistamiento($A->id);' style='cursor:pointer'>Cumplir</a>";
			}

			echo "</td><td><a class='rinfo' style='cursor:pointer' onclick='imprimir_formato($A->id);'><img src='gifs/print.png' border='0'><span>Producir Acta de Alistamiento</span></a></td>
						</tr>";
		}
		echo "</table>";
	}
	else
		echo "<h3>Este vehiculo no tiene pendientes de alistamiento. Hora de consulta: ".fecha_hora_completa(date('Y-m-d H:i:s'))."</h3>";
	echo "</body>";
}

function registrar_alistamiento_ok()
{
	global $Vehiculo,$fecha,$programado_por,$descripcion,$Retorno;

	$IDN=q("insert into alistamiento (vehiculo,fecha,programado_por,descripcion) values ('$Vehiculo','$fecha','$programado_por','$descripcion')");

	if($Defecto=qo1("select id from operario where oficina= ultima_ubicacion($Vehiculo) and por_defecto=1"))
	{
		q("update alistamiento set asignado_a=$Defecto where id=$IDN");
	}

	echo "<body><script language='javascript'>parent.traslada_contenido(\"$descripcion\");window.open('zalistamiento.php?Acc=ver_pendientes&id=$Vehiculo','_self');</script>";
	if($Retorno) echo "<script language='javascript'>window.open('$Retorno','_self');</script>";
	echo "</body>";
}

function estado_pendientes()
{
	global $Tipo_informe;
	if(!$Tipo_informe) $Tipo_informe='vehiculo';
	html('Pendientes de Alistamiento');
	echo "<script language='javascript'>
			function asignar_operario(id)
			{
				modal('zalistamiento.php?Acc=asignar_operario&id='+id,0,0,500,500,'asigna_operario');
			}
			function cambia_tipo_informe(tipo)
			{
				window.open('zalistamiento.php?Acc=estado_pendientes&Tipo_informe='+tipo,'_self');
			}
			function cumplir_alistamiento(id)
			{
				if(confirm('Desea dar cumplimiento a este alistamiento?'))
				{
					document.getElementById('if_alistamiento2').style.visibility='visible';
					window.open('zalistamiento.php?Acc=cumplir_alistamiento&id='+id,'if_alistamiento2');
				}
			}
			function ocultar_cumplimiento()
			{
				document.getElementById('if_alistamiento2').style.visibility='hidden';
			}
			function recargar()
			{
				window.open('zalistamiento.php?Acc=estado_pendientes&Tipo_informe=$Tipo_informe','_self');
			}
			function adiciona_obs(id)
			{
				var Obs=prompt('Observaciones:','');
				Obs=alltrim(Obs);
				if(Obs.length) {window.open('zalistamiento.php?Acc=inserta_obs&id='+id+'&Obs='+Obs,'if_alistamiento2');}
			}
			function imprimir_formato(id)
			{
				modal('zalistamiento.php?Acc=imprimir_formato&id='+id,0,0,700,900,'formato_alistamiento');
			}
		</script>
		<body ><script language='javascript'>centrar();</script>
		<h3>Pendientes de Alistamiento por:
		<select name='Tipo_informe' onchange='cambia_tipo_informe(this.value);'>
		<option value='vehiculo' ".($Tipo_informe=='vehiculo'?"selected":"").">Vehículo</option>
		<option value='operario'".($Tipo_informe=='operario'?"selected":"").">Operario</option>
		</select>
		</h3>
		<iframe name='if_alistamiento2' id='if_alistamiento2' style='position:fixed;visibility:hidden;' height='300' width='500'></iframe>";
	if($Tipo_informe=='vehiculo')
	{
		if($Placas=q("select distinct vehiculo,t_vehiculo(vehiculo) as nvehiculo from alistamiento WHERE fecha_cumplimiento='0000-00-00 00:00:00' order by nvehiculo"))
		{
			echo "<table width='100%'>";
			while($P=mysql_fetch_object($Placas))
			{
				$DV=qo("select t_linea_vehiculo(linea) as nlinea,kilometraje(id) as kilometraje,t_oficina(ultima_ubicacion(id)) as ciudad from vehiculo where id=$P->vehiculo");
				echo "<tr><th colspan=5 style='font-size:18px'>$P->nvehiculo $DV->nlinea Kilometraje: ".coma_format($DV->kilometraje)." Ciudad: $DV->ciudad</th></tr>";
				$Pendientes=q("select *,t_operario(asignado_a) as noperario from alistamiento where vehiculo=$P->vehiculo and fecha_cumplimiento='0000-00-00 00:00:00' order by fecha");
				echo "<tr><th>Fecha</th><th>Programado por</th><th>Descripcion</th><th>Asignado a</th><th>Cumplimiento</th></tr>";
				while($Pe=mysql_fetch_object($Pendientes))
				{
					echo "<tr>
									<td>$Pe->fecha ".demora_cumplimiento($Pe->fecha)."</td>
									<td>$Pe->programado_por</td>
									<td>".nl2br($Pe->descripcion)." <a class='info' style='cursor:pointer' onclick='adiciona_obs($Pe->id)'><img src='gifs/mas.gif' border='0'><span>Adicionar Observaciones</span></a></td>
									<td>";
					if($Pe->asignado_a)
					{
						echo $Pe->noperario;
						if(inlist($_SESSION['User'],'1,2,27,23'))
							echo " <a onclick='asignar_operario($Pe->id);' style='cursor:pointer;color:0000ff;background-color:ffffdd;'>Reasignar</a>";
					}
					else
					{
						if(inlist($_SESSION['User'],'1,2,27,23'))
							echo " <a onclick='asignar_operario($Pe->id);' style='cursor:pointer;color:0000ff;background-color:ffffdd;'>Asignar</a>";
					}
					echo "</td>";
					if($Pe->asignado_a)
						echo "<td  nowrap='yes' align='center'>".($Pe->fecha_cumplimiento!='0000-00-00 00:00:00'?$Pe->fecha_cumplimiento:"<a style='cursor:pointer' onclick='cumplir_alistamiento($Pe->id)'>Registrar cumplimiento</a>")."</td>";
					echo "<td><a class='rinfo' style='cursor:pointer' onclick='imprimir_formato($Pe->id);'><img src='gifs/print.png' border='0'><span>Producir Acta de Alistamiento</span></a></td>";
					echo "</tr>";
				}
			}
			echo "</table>";
		}
		else
		{
			echo "<br /><br /><font color='red'>No hay Alistamientos pendientes.</font>";
		}
	}
	elseif($Tipo_informe=='operario')
	{
		if($Operarios=q("select distinct asignado_a,t_operario(asignado_a) as noperario from alistamiento
										WHERE fecha_cumplimiento='0000-00-00 00:00:00' order by noperario"))
		{
			echo "<table width='100%'>";
			while($O=mysql_fetch_object($Operarios))
			{
				$DO=qo("select t_oficina(oficina) as noficina from operario where id=$O->asignado_a");
				echo "<tr><th colspan=4 style='font-size:18px'>$O->noperario $DO->noficina</th></tr>";
				$Pendientes=q("select *,t_vehiculo(vehiculo) as nvehiculo from alistamiento where asignado_a=$O->asignado_a and fecha_cumplimiento='0000-00-00 00:00:00' order by fecha");
				echo "<tr><th>Fecha</th><th>Programado por</th><th>Descripcion</th><th>Vehiculo</th></tr>";
				while($Pe=mysql_fetch_object($Pendientes))
				{
					echo "<tr>
									<td>$Pe->fecha ".demora_cumplimiento($Pe->fecha)."</td>
									<td>$Pe->programado_por</td>
									<td>".nl2br($Pe->descripcion)." <a class='info' style='cursor:pointer' onclick='adiciona_obs($Pe->id)'><img src='gifs/mas.gif' border='0'><span>Adicionar Observaciones</span></a></td>
									<td>$Pe->nvehiculo</td><td>";
					if($Pe->asignado_a)
					{
						if($Pe->fecha_cumplimiento!='0000-00-00 00:00:00') echo "$Pe->fecha_cumplimiento";
						else
						{
							if(inlist($_SESSION['User'],'1,2,27,23'))
								echo "<a onclick='asignar_operario($Pe->id);' style='cursor:pointer;color:0000ff;background-color:ffffdd;'>Reasignar</a> ";
							echo "<a style='cursor:pointer' onclick='cumplir_alistamiento($Pe->id)'>Registrar cumplimiento</a>";
						}
					}
					else
					{
						echo "<a onclick='asignar_operario($Pe->id);' style='cursor:pointer;color:0000ff;background-color:ffffdd;'>Asignar</a>";
					}
					echo "</td><td><a class='rinfo' style='cursor:pointer' onclick='imprimir_formato($Pe->id);'><img src='gifs/print.png' border='0'><span>Producir Acta de Alistamiento</span></a></td></tr>";
				}
			}
			echo "</table>";
		}
		else
		{
			echo "<br /><br /><font color='red'>No hay Alistamientos pendientes.</font>";
		}
	}

	echo "</body>";
}

function asignar_operario()
{
	global $id;
	$D=qo("select *,t_vehiculo(vehiculo) as placa from alistamiento where id=$id");
	$VD=qo("select t_oficina(ultima_ubicacion($D->vehiculo)) as ciudad,kilometraje($D->vehiculo) as kilometraje ");
	$Linea=qo("select t_linea_vehiculo(linea) as linea from vehiculo where id=$D->vehiculo");
	html('ASIGNACION DE OPERARIO');
	echo "<script language='javascript'>
					function validar_asignar()
					{
						with(document.forma)
						{
							if(operario.value=='')
							{
								alert('Debe seleccionar un operario');
								operario.style.backgroundColor='ffffdd';
								operario.focus();
								return false;
							}
							submit();
						}
					}

		</script>
		<body><script language='javascript'>centrar(500,500);</script><h3>Asignación de Operario</h3>
		Alistamiento de fecha: <b>$D->fecha</b><br />
		Vehículo: <b>$D->placa $Linea->linea</b> Ciudad actual: <b>$VD->ciudad</b> Kilometraje: <b>".coma_format($VD->kilometraje)."</b><br />
		Programado por: <b>$D->programado_por</b><br>
		Descripción: <b>$D->descripcion</b><br>
		<form action='zalistamiento.php' method='post' target='_self' name='forma' id='forma'>
		Operario que desea asignar: <select name='operario'><option value=''>Seleccione un operario</option>";
	$Oficinas=q("select distinct oficina,t_oficina(oficina) as nofi from operario order by nofi");
	while($Ofi=mysql_fetch_object($Oficinas))
	{
		echo "<optgroup label='$Ofi->nofi' title='$Ofi->nofi'>";
		if($Operarios=q("select id,concat(apellido,' ',nombre) as noper from operario where oficina=$Ofi->oficina order by noper "))
		{
			while($Oper=mysql_fetch_object($Operarios))
			{
				echo "<option value='$Oper->id'>$Oper->noper</option>";
			}
		}
		echo "</optgroup>";
	}
	echo "</select>
		<br><br><center><input type='button' value='Asignar Operario' onclick='validar_asignar();' style='font-weight:bold;height:25px;width:200px'></center>
		<input type='hidden' name='Acc' value='asignar_operario_ok'>
		<input type='hidden' name='id' value='$id'>
		</form>";
	echo "</body>";
}

function asignar_operario_ok()
{
	global $operario,$id;
	q("update alistamiento set asignado_a='$operario' where id='$id' ");
	echo "<script language='javascript'>
			function carga()
			{
				alert('Asignación satisfactoria');
				window.close();void(null);
				opener.location.reload();
			}
		</script>
		<body onload='carga()'><script language='javascript'>centrar(10,10);</script></body>";
}


function demora_cumplimiento($fecha)
{
	$Hoy=date('Y-m-d');
	$diferencia=dias($Hoy,$fecha);
	if($diferencia==0) return "<span style='background-color:ffffff;'>&nbsp;0 días&nbsp;</span>";
	if($diferencia==1) return "<span style='background-color:ffffbb;'>&nbsp;1 día&nbsp;</span>";
	if($diferencia==2) return "<span style='background-color:ffff00'>&nbsp;2 días&nbsp;</span>";
	if($diferencia==3) return "<span style='background-color:ffbb00'>&nbsp;3 días&nbsp;</span>";
	if($diferencia==4) return "<span style='background-color:ff7700'>&nbsp;4 días&nbsp;</span>";
	if($diferencia==5) return "<span style='background-color:ff2200'>&nbsp;5 días&nbsp;</span>";
	if($diferencia==6) return "<span style='background-color:880000'>&nbsp;6 días&nbsp;</span>";
	return "<span style='background-color:ff00ff'>&nbsp;7+ días&nbsp;</span>";
}

function mis_pendientes()
{
	global $id;
	if($_SESSION['User']==23)
	{
		$Usuario=$_SESSION['Id_alterno'];
	}
	else
	{
		$Usuario=$id;
	}
	$Operario=qo("select * from operario where id=$Usuario ");
	html("Alistamientos Pendientes de $Operario->nombre $Operario->apellido");
	echo "<script language='javascript'>
			function cumplir_alistamiento(id)
			{
				document.getElementById('if_alistamiento').style.visibility='visible';
				window.open('zalistamiento.php?Acc=cumplir_alistamiento&id='+id,'if_alistamiento');
			}
			function ocultar_cumplimiento()
			{
				document.getElementById('if_alistamiento').style.visibility='hidden';
			}

			function recargar()
			{
				window.open('zalistamiento.php?Acc=mis_pendientes&id=$id','_self');
			}
			function adiciona_obs(id)
			{
				var Obs=prompt('Observaciones:','');
				Obs=alltrim(Obs);
				if(Obs.length) {window.open('zalistamiento.php?Acc=inserta_obs&id='+id+'&Obs='+Obs,'if_alistamiento');}
			}
			function imprimir_formato(id)
			{
				modal('zalistamiento.php?Acc=imprimir_formato&id='+id,0,0,700,900,'formato_alistamiento');
			}

		</script>
		<body ><script language='javascript'>centrar();</script>
		<h3>Alistamientos Pendientes de $Operario->nombre $Operario->apellido</h3>
		<iframe name='if_alistamiento' id='if_alistamiento' style='position:fixed;visibility:hidden;' height='300' width='500'></iframe>
		";
	if($Pendientes=q("select *,t_vehiculo(vehiculo) as placa from alistamiento where asignado_a=$Usuario and fecha_cumplimiento='0000-00-00 00:00:00' order by fecha"))
	{
		echo "<table width='100%'><tr>
						<th>#</th>
						<th>Vehículo</th>
						<th>Descripción</th>
						<th>Asignado por</th>
						<th>Fecha</th>
						<th>Antigüedad</th>
						<th>Cumplimiento</th>
						</tr>";
		$Contador=1;
		while($P=mysql_fetch_object($Pendientes))
		{
			echo "<tr>
						<td align='center'>$Contador</td>
						<td>$P->placa</td>
						<td>$P->descripcion <a class='info' style='cursor:pointer' onclick='adiciona_obs($P->id)'><img src='gifs/mas.gif' border='0'><span>Adicionar Observaciones</span></a></td>
						<td>$P->programado_por</td>
						<td>$P->fecha</td>
						<td>".demora_cumplimiento($P->fecha)."</td>
						<td align='center'><a onclick='cumplir_alistamiento($P->id);' style='cursor:pointer'>Cumplir</a></td>
						<td><a class='rinfo' style='cursor:pointer' onclick='imprimir_formato($P->id);'><img src='gifs/print.png' border='0'><span>Producir Acta de Alistamiento</span></a></td>
						</tr>";
			$Contador++;
		}
		echo "</table>";
	}
	else
	{
		echo "<br /><br /><font color='red'>$Operario->nombre $Operario->apellido no tiene alistamientos pendientes.</font>";
	}
	echo "</body></html>";
}

function cumplir_alistamiento()
{
	global $id;
	$D=qo("select *,t_vehiculo(vehiculo) as placa from alistamiento where id=$id");
	$Operario=qo("select * from operario where id=".$_SESSION['Id_alterno']);
	$Momento=date('Y-m-d H:i:s');
	html("CUMPLIMIENTO DE ALISTAMIENTO");
	echo "<script language='javascript'>
			function cumplir_alistamiento()
			{
				parent.ocultar_cumplimiento();
				window.open('zalistamiento.php?Acc=cumplir_alistamiento_ok&id=$id&M=".base64_encode($Momento)."','_self');
			}
		</script>
		<body bgcolor='ffffee'><script language='javascript'>centrar(400,400);</script>
		<h3>CUMPLIMIENTO DE ALISTAMIENTO.<br />Operario: $Operario->nombre $Operario->apellido</h3>
		Vehículo: $D->placa Fecha de programación: $D->fecha<br />
		Programado por: $D->programado_por<br />
		Descripción: $D->descripcion<br /><br />
		Dar cumplimiento en este momento: <b>$Momento</b><br /><br />
		<input type='button' value='Continuar' onclick='cumplir_alistamiento()'> <input type='button' value='Cancelar' onclick='parent.ocultar_cumplimiento();'>
		</body>";
}

function cumplir_alistamiento_ok()
{
	global $id,$M;
	$Momento=base64_decode($M);
	$USUARIO=$_SESSION['Nombre'];
	q("update alistamiento set fecha_cumplimiento='$Momento',cumplido_por='$USUARIO' where id=$id");
	$Al=qo("select vehiculo from alistamiento where id=$id");
	$Placa=qo1("select placa from vehiculo where id=$Al->vehiculo");
	$Pendientes=qo1("select count(id) from alistamiento where vehiculo=$Al->vehiculo and fecha_cumplimiento='0000-00-00 00:00:00' ");
	$Hoy=date('Y-m-d');
	$Ultimo_estado=qo("select ub.* from ubicacion ub where ub.vehiculo=$Al->vehiculo and ub.fecha_final>='$Hoy' order by fecha_final,id desc limit 1");
	echo "<body>";
	if($Ultimo_estado->estado==8 /*Alistamiento*/)
	{
		if($Pendientes) echo "<script language='javascript'>alert('Este vehículo tiene $Pendientes pendientes');parent.recargar();</script>";
		else
		{
			echo "<script language='javascript'>if(confirm('Este vehículo no tiene mas pendientes y está en ALISTAMIENTO, desea cerrar el alistamiento?'))
					{
						while(true)
						{
							var Kilometraje=prompt('Digite el kilometraje actual del vehículo $Placa:',0);
							Kilometraje=Number(Kilometraje);
							if(Kilometraje<=0 || isNaN(Kilometraje))
							{
								alert('Debe digitar un kilometraje valido sin comas ni puntos');
							}
							else
							{

								if(Kilometraje<$Ultimo_estado->odometro_final)
								{
									alert('Debe digitar un kilometraje mayor que el último registrado');
								}
								else
								{
									break;
								}
							}
						}
						window.open('zalistamiento.php?Acc=cierra_alistamiento&id=$Ultimo_estado->id&km='+Kilometraje,'_self');
					}
				</script>";
		}
	}
	else
	{
		echo "<script language='javascript'>parent.recargar();</script>";
	}

}

function cierra_alistamiento()
{
	global $id,$km;
	$Hoy=date('Y-m-d');
	$Ac=qo("select * from ubicacion where id=$id");
	q("update ubicacion set fecha_final='$Hoy',odometro_final=$km,odometro_diferencia=odometro_final-odometro_inicial where id=$id");
	$IDN=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones)
		values ($Ac->oficina,$Ac->vehiculo,2,$Ac->flota,'$Hoy','$Hoy',$km,$km,0,'Creación de estado automática')");
	graba_bitacora('ubicacion','A',$IDN,'Adiciona');
	echo "<script language='javascript'>parent.recargar();</script>";
}

function inserta_obs()
{
	global $id,$Obs;
	$USUARIO=$_SESSION['Nombre'];
	$Hoy=date('Y-m-d H:i:s');
	q("update alistamiento set descripcion=concat(descripcion,\"\n$USUARIO [$Hoy] $Obs\") where id= $id");
	echo "<script language='javascript'>parent.recargar();</script>";
}

function imprimir_formato()
{
	global $id;
	$Al=qo("select * from alistamiento where id=$id");
	$V=qo("select * from vehiculo where id=$Al->vehiculo");
	$L=qo("select * from linea_vehiculo where id=$V->linea");
	$M=qo("select * from marca_vehiculo where id=$L->marca");
	$Uu=qo("select * from ubicacion where vehiculo=$Al->vehiculo order by id desc limit 1");
	$Flota=qo1("select t_aseguradora($Uu->flota) ");
	$Oficina=qo("select * from oficina where id=$Uu->oficina");
	$Operario=qo("select * from operario where id=$Al->asignado_a");
	include('inc/pdf/fpdf.php');
	$P=new pdf('P','mm','Letter');
	$P->AddFont("c128a","","c128a.php");
	$P->AliasNbPages();
	$P->setTitle("ACTA DE ENTREGA/DEVOLUCION");
	$P->setAuthor("Arturo Quintero Rodriguez www.aoacolombia.com arturoquintero@aoacolombia.com");
	$P->Numeracion=false;
	$P->SetAutoPageBreak(false);
	$P->SetTopMargin('5');
	$P->AddPage('P');
	$P->Image('../img/LOGO_AOA_200.jpg',10,5,40,18);
	$P->setfont('Arial','B',16);
	$P->setxy(80,10);
	$P->Cell(110,5,'ACTA DE ALISTAMIENTO',0,0,'L');
	$P->setfont('Arial','B',10);
	$P->setxy(10,28);
	$P->cell(40,6,'PLACA: '.$V->placa,1,0,'L');
	$P->cell(80,6,'MARCA: '.$M->nombre.' '.$L->nombre,1,0,'L');
	$P->cell(70,6,'OFICINA: '.$Oficina->nombre,1,0,'L');
	$P->setxy(10,$P->y+6);
	$P->cell(70,6,'FLOTA: '.$Flota,1,0,'L');
	$P->cell(120,6,'RESPONSABLE: '.$Operario->nombre.' '.$Operario->apellido,1,0,'L');
	$P->setxy(10,$P->y+6);
	$P->cell(70,6,'Kilometraje: '.coma_format($Uu->odometro_final),1,0,'L');
	$P->cell(120,6,'FECHA: '.fecha_completa(date('Y-m-d')),1,0,'L');
	$P->setxy(10,$P->y+10);
	$P->cell(100,4,'INTERNO',1,0,'C');$P->cell(90,4,'EXTERNO',1,0,'C');
	$P->setfont('Arial','B',9);
	$P->setxy(10,$P->y+4);$P->cell(100,4,'DOCUMENTOS DEL VEHICULO VIGENTES',1,0,'L');	$P->cell(90,4,'NIVELES Y FLUIDOS',1,0,'L');
	$P->setfont('Arial','',9);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Tarjeta de propiedad',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Nivel de aceite motor',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - S.O.A.T.',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Nivel de liquido de frenos',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Revisión Técnico Mecánica y de Gases',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Nivel de agua lava-parabrisas',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Información Aseguradora',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Nivel de refrigerante',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Manuales',1,0,'L');$P->cell(5,4,' ',1);$P->cell(90,4,' ',1,0,'L');
	$P->setfont('Arial','B',10);
	$P->setxy(10,$P->y+8);$P->cell(100,4,'ACCESORIOS',1,0,'C');$P->cell(90,4,'PARTES',1,0,'C');
	$P->setfont('Arial','',9);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Luz de cortesía',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Rines y llantas incluido repuesto ('.$Oficina->libras_llantas.' libras de presión)',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Cinturones en posición',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Kit de carretera',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Sillas en posición',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Gato',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Tapicería y aspecto interior',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Palanca',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Aire acondicionado y rejillas',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Gancho de arrastre',1,0,'L');$P->cell(5,4,' ',1);
	$P->setfont('Arial','B',10);
	$P->setxy(10,$P->y+8);$P->cell(100,4,'RADIO',1,0,'C');$P->cell(90,4,'LUCES',1,0,'C');
	$P->setfont('Arial','',9);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Volúmen nivel 4',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Luces delanteras',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Emisora: '.$Oficina->emisora,1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Exploradoras',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Hora ajustada Radio/Tablero',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Cocuyos',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(100,4,' ',1,0,'L');$P->cell(85,4,' - Luces traseras, 3er Stop',1,0,'L');$P->cell(5,4,' ',1);
	$P->setfont('Arial','B',10);
	$P->setxy(10,$P->y+8);$P->cell(190,4,'OTROS',1,0,'C');//$P->cell(90,4,'OTROS',1,0,'L');
	$P->setfont('Arial','',9);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Odómetro parcial en ceros',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Limpieza externa del vehículo',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Encendedor',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Limpieza del motor',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Cenicero limpio y abierto',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Estado de las plumillas',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Eleva vidrios funcionando',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Sellos de seguridad',1,0,'L');$P->cell(5,4,' ',1);
	$P->setxy(10,$P->y+4);$P->cell(95,4,' - Collarines y calcomanias',1,0,'L');$P->cell(5,4,' ',1);$P->cell(85,4,' - Sombrilla',1,0,'L');$P->cell(5,4,' ',1);
	$P->setfont('Arial','B',10);
	$P->setxy(10,$P->y+8);$P->cell(100,4,'LATAS',1,0,'C');$P->cell(90,4,'PINTURA',1,0,'C');
	$P->setfont('Arial','',9);
	$P->setxy(10,$P->y+4);$P->cell(28,4,'Bueno',1,0,'C');$P->cell(5,4,' ',1);$P->cell(29,4,'Regular',1,0,'C');$P->cell(5,4,' ',1);$P->cell(28,4,'Malo',1,0,'C');$P->cell(5,4,' ',1);
	$P->cell(25,4,'Bueno',1,0,'C');$P->cell(5,4,' ',1);$P->cell(25,4,'Regular',1,0,'C');$P->cell(5,4,' ',1);$P->cell(25,4,'Malo',1,0,'C');$P->cell(5,4,' ',1);
	$P->setfont('Arial','B',9);
	$P->setxy(10,$P->y+4);$P->cell(100,4,'Observaciones',1,0,'L');$P->cell(90,4,'Observaciones',1,0,'L');
	$P->setfont('Arial','',9);
	$P->setxy(10,$P->y+4);$P->cell(100,5,' ',1,0,'L');$P->cell(90,5,' ',1,0,'L');
	$P->setxy(10,$P->y+5);$P->cell(100,5,' ',1,0,'L');$P->cell(90,5,' ',1,0,'L');
	$P->setxy(10,$P->y+5);$P->cell(100,5,' ',1,0,'L');$P->cell(90,5,' ',1,0,'L');
	$P->setxy(10,$P->y+5);$P->cell(100,5,' ',1,0,'L');$P->cell(90,5,' ',1,0,'L');
	$P->setfont('Arial','B',10);
	$P->setxy(10,$P->y+8);$P->cell(190,4,'Correcciones',1,0,'L');
	$P->setfont('Arial','',9);
	$P->setxy(10,$P->y+4);$P->cell(190,5,' ',1,0,'L');
	$P->setxy(10,$P->y+5);$P->cell(190,5,' ',1,0,'L');
	$P->setxy(10,$P->y+5);$P->cell(190,5,' ',1,0,'L');
	$P->setxy(10,$P->y+5);$P->cell(190,5,' ',1,0,'L');
	$P->setfont('Arial','B',10);
	$P->setxy(10,$P->y+8);$P->cell(100,4,'RESPONSABLE',0,0,'L');$P->Cell(90,4,'FIRMA: ',0,0,'L');
	$P->setxy(10,$P->y);$P->cell(100,24,' ',1,0,'L');$P->cell(90,24,' ',1,0,'L');
	$P->setxy(10,$P->y+20);$P->cell(100,4,'Nombre Completo',1,0,'L');

	$P->Output();
}









?>