<?php

/**
 *  Programa para registro y control de compromisos de call center
 *
 *  id es el id del siniestro
 *
 * @version $Id$
 * @copyright 2010
 */
include('inc/funciones_.php');
sesion();
$USUARIO=$_SESSION['User'];
$Nusuario=$_SESSION['Nombre'];
$Nick=$_SESSION['Nick'];
$Hoy=date('Y-m-d H:i:s');

if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}

function crear_compromiso()
{
	global $id,$USUARIO,$Nusuario,$Nick,$Hoy,$refrescar,$DesdeCall,$Desde_proceso_call;
	$Sin=qo1("select t_siniestro($id)");
	html('CREACION DE COMPROMISO');
	echo "<script language='javascript'>
			function validar_compromiso()
			{
				with(document.forma)
				{
					if(!alltrim(descripcion.value))
					{ alert('Debe digitar una descripción del compromiso que quiere registrar');descripcion.style.backgroundColor='ffffdd';descripcion.focus();return false;}
					if(!alltrim(hora.value))
					{ alert('Debe seleccionar una hora de compromiso valida.');hora.style.backgroundColor='ffffdd';hora.focus();return false;}
					if(!tipo.value) 
					{alert('Debe seleccionar el tipo de compromiso.');tipo.style.backgroundColor='ffffdd';tipo.focus();return false;}
					submit();
				}
			}

			function busca_hora_disponible()
			{
				var Fecha=document.forma.fecha.value;
				modal('zcompromiso.php?Acc=pinta_hora_disponible&fecha='+Fecha,0,0,500,800,'hd');
			}

		</script>
		<body bgcolor='eeffff'>
		<form action='zcompromiso.php' method='post' target='_self' name='forma' id='forma'>
			<h3>Creación de compromiso Siniestro: $Sin</h3>
			<table>
				<tr><td align='right'>Siniestro:</td><td><input type='text' name='siniestro' value='$id' size=5 readonly> $Sin</td></tr>
				<tr><td align='right'>Usuario:</td><td><input type='text' name='usuario' value='$Nick' readonly> $Nusuario</td></tr>
				<tr><td align='right'>Fecha de compromiso:</td><td>".pinta_FC('forma','fecha',date('Y-m-d'))." <input type='text' name='hora' onclick='busca_hora_disponible()' readonly size=6></td></tr>
				<tr><td align='right'>Descripcion:</td><td><textarea name='descripcion' cols=80 rows=4 style='font-family:arial;font-size:11px'></textarea></td></tr>
				<tr><td align='right'>Tipo Compromiso</td><td>".menu1('tipo',"select id,nombre from tipo_compromiso",0,1)."</td></tr>
			</table>
			<hr>
			<center><input type='button' value='Grabar Compromiso' onclick='validar_compromiso()' style='height:24px;width:300px;'></center>
			<input type='hidden' name='Acc' value='crear_compromiso_ok'>
			<input type='hidden' name='DesdeCall' value='$DesdeCall'>
			<input type='hidden' name='Desde_proceso_call' value='$Desde_proceso_call'>
			<input type='hidden' name='refrescar' value='$refrescar'>
		</form></body>";
}

function pinta_hora_disponible()
{
	global $fecha;
	html('Hora disponible para compromiso');
	$Compromisos=q("select distinct hora from compromiso where fecha='$fecha' and estado='P'  order by hora");
	$Hora=' |';
	if($Compromisos) while($C=mysql_fetch_object($Compromisos)) $Hora.=l($C->hora,5).'|';

	echo "<script language='javascript'>
		function asigna(dato)
		{
			opener.document.forma.hora.value=dato;
			window.close();void(null);
		}
		</script>
	<body><h3>HORAS DISPONIBLES PARA COMPROMISO FECHA: $fecha</H3>
	 <table bgcolor='dddddd'> ";
	for($h=7;$h<19;$h++)
	{
		echo "<tr><td style='background-color:000000;color:ffffff;font-weight:bold' width=10 align='center'>$h</td>";
		for($m=0;$m<59;$m+=5)
		{
			$h1=date('H:i',strtotime($fecha.' '.$h.':'.$m.':00'));
			$hh="<a onclick='asigna(\"$h1\")' style='cursor:pointer'>".date('h:i A',strtotime($fecha.' '.$h.':'.$m.':00'))."</a>";
			if(strpos($Hora,'|'.$h1.'|')) $hh='-';
			echo "<td align='center' bgcolor='ffffff'>$hh</td>";
		}
		echo "</tr>";
	}
	echo "</table></body>";


}

function crear_compromiso_ok()
{
	global $siniestro,$usuario,$fecha,$hora,$descripcion,$Hoy,$refrescar,$DesdeCall,$Desde_proceso_call,$Nusuario,$tipo;
	$Ahora=date('Y-m-d H:i:s');
	$Nid=q("insert into compromiso (siniestro,fecha,hora,usuario,descripcion,fecha_programacion,tipo) values
			('$siniestro','$fecha','$hora','$usuario',\"$descripcion\",'$Hoy','$tipo')");
	q("update siniestro set observaciones=concat(observaciones,'\n$Nusuario [$Ahora]: Adiciona Compromiso para $fecha $hora: $descripcion') where id=$siniestro");
	graba_bitacora('siniestro','M',$id,'Observaciones');
	if($refrescar==1)  // adicion desde ver_compromisos
	{
		echo "<script language='javascript'>
				function carga()
				{
					".($DesdeCall?"parent.adiciona_compromiso($Nid,'$fecha','$hora','$usuario',\"$descripcion\",$siniestro); ":"").
					($Desde_proceso_call?"parent.marcar_procesado();parent.cerrar1(); ":"").
					"window.open('zcompromiso.php?Acc=ver_compromisos&id=$siniestro&DesdeCall=$DesdeCall&Desde_proceso_call=$Desde_proceso_call','_self');
				}
			</script>
			<body onload='carga()'></body>";
	}
	elseif($refrescar==2) // adicion desde Compromisos por operario
	{
		echo "<script language='javascript'>
				function carga()
				{
					window.open('zcompromiso.php?Acc=compromisos_operario&OPERARIO=$usuario','_self');
				}
			</script>
			<body onload='carga()'></body>";
	}
	else
		echo "<script language='javascript'>
			function carga()
			{
				alert('Registro de compromiso hecho satisfactoriamente');
				window.close();void(null);
			}
		</script>
		<body onload='carga()'><script language='javascript'>centrar(10,10);</script></body>";
}

function ver_compromisos()
{
	global $id,$Hoy,$USUARIO,$DesdeCall,$Desde_proceso_call;
	$Sin=qo1("select t_siniestro($id)");
	html('Revisión de compromisos');
	echo "
		<script language='javascript'>
		function cumplimiento(id,objeto)
		{
			var estado=objeto.value;
			if(estado=='C')
			{
				if(confirm('Seguro de cambiar el estado?'))
				{
					modal('zcompromiso.php?Acc=cumplir_compromiso&id='+id+'&DesdeCall=$DesdeCall&Desde_proceso_call=$Desde_proceso_call',0,0,10,10,'comp_oculto');
				}
			}
			else
			{
				alert('Este compromiso ya fue cumplido, no se puede reversar este estado');
				objeto.value='C';
			}
		}
		function adicionar_compromiso(id)
		{
			window.open('zcompromiso.php?Acc=crear_compromiso&id='+id+'&refrescar=1&DesdeCall=$DesdeCall&Desde_proceso_call=$Desde_proceso_call','_self');
		}
		function modulo_callcenter(id)
		{
			modal('zctrlseg.php?Acc=call_center_obs&id='+id,0,0,800,1000,'CallCenter');
		}
		</script>
		<body bgcolor='eeffff'><h3>Compromisos del siniestro $Sin</h3>

		<a style='cursor:pointer' onclick='adicionar_compromiso($id);'><img src='gifs/standar/nuevo_registro.png' border='0'> Crear nuevo compromiso </a> ";
	if($DesdeCall) echo "<a style='cursor:pointer' onclick='parent.cierra_compromisos();'><img src='gifs/standar/Cancel.png' border='0'> Cerrar</a>";
	elseif($Desde_proceso_call) echo "<a style='cursor:pointer' onclick=\"window.open('zcallcenter.php?Acc=$Desde_proceso_call&id=$id','_self');\"><img src='gifs/atras.png' border='0'> Volver</a>
		<a style='cursor:pointer' onclick='parent.marcar_procesado();parent.cerrar1();'><img src='gifs/standar/Cancel.png' border='0'> Finalizar Proceso Call Center</a>";
	else echo "<a style='cursor:pointer' onclick='modulo_callcenter($id);'><img src='img/callphone.png' border='0'> Módulo Call Center</a>";
	echo "<hr>";
	if($Compromisos=q("select *,t_tipo_compromiso(tipo) as ntipo from compromiso where siniestro=$id order by estado desc, fecha desc, hora desc"))
	{
		echo "<table border cellspacing='0' width='100%'><tr>
					<th>Usuario</th>
					<th>Fecha Compromiso</th>
					<th>Tipo</th>
					<th>Descripción</th>
					<th>Fecha de programación</th>
					<th>Estado</th>
					<th>Fecha de Cumplimiento</th>
					</tr>";
		while($C=mysql_fetch_object($Compromisos))
		{
			$t=date('Y-m-d H:i:s',strtotime("$C->fecha $C->hora"));
			if($C->estado=='P')
			{
				if($t>$Hoy) $Color='ffffdd';
				else $Color='ffdddd';
			}
			else
				$Color='ffffff';
			echo "<tr>
						<td>$C->usuario</td>
						<td bgcolor='$Color'>$C->fecha $C->hora</td>
						<td>$C->ntipo</td>
						<td>$C->descripcion</td>
						<td>$C->fecha_programacion</td>
						<td>";
			if(inlist($USUARIO,'1,4,26') )  /* usuarios admitidos, 1: primario 4: call center 26: coordinador call center */
			{
				if(date('Y-m-d H:i:s')<="$C->fecha $C->hora")
				{
						echo "<select name='estado' onchange='cumplimiento($C->id,this);'><option value='P' ".($C->estado=='P'?"selected":"").">Pendiente</option>
							<option value='C' ".($C->estado=='C'?"selected":"").">Cumplido</option>
							</select>";
				}
				else
				{
					if($C->estado=='C') echo "Cumplido"; else echo "Pendiente";
				}
			}
			else
			{
				if($C->estado=='C') echo "Cumplido"; else echo "Pendiente";
			}
			echo "</td>
						<td align='center'>".($C->estado=='C'?$C->fecha_cumplimiento:" - ")."</td>
						</tr>";
		}
		echo "</table></body>";
	}
	else
	{
		echo "No hay compromisos registrados para este siniestro.";
	}
}

function cumplir_compromiso()
{
	global $id,$Hoy,$DesdeCall,$Desde_proceso_call,$Nusuario;
	$Siniestro=qo1("select siniestro from compromiso where id=$id");
	q("update compromiso set estado='C',fecha_cumplimiento='$Hoy',cumplido_por='$Nusuario' where id=$id");
	html();
	echo "<script language='javascript'>
			function carga()
			{
				".($DesdeCall?"opener.parent.retira_compromiso($Siniestro,$id);":"").
				 "opener.location.reload();
				window.close();void(null);
			}
		</script>
		<body onload='carga()'><script language='javascript'>centrar(10,10);</script></body>";
}

function compromisos_operario()
{
	global $OPERARIO,$Hoy,$USUARIO,$Nick;
	if($USUARIO==4) $OPERARIO=$Nick;
	$Consulta="select compromiso.*,t_siniestro(siniestro) as nsiniestro,
		t_tipo_compromiso(tipo) as ntipo 
		from compromiso ".($OPERARIO?" where usuario like '%$OPERARIO%' ":"")." order by estado desc, fecha desc, hora desc";
	html('Revisión de compromisos');
	echo "
		<script language='javascript'>
		function cumplimiento(id,objeto)
		{
			var estado=objeto.value;
			if(estado=='C')
			{
				if(confirm('Seguro de cambiar el estado?'))
				{
					modal('zcompromiso.php?Acc=cumplir_compromiso&id='+id,0,0,10,10,'compo_oculto');
				}
			}
			else
			{
				alert('Este compromiso ya fue cumplido, no se puede reversar este estado');
				objeto.value='C';
			}
		}
		function adicionar_compromiso(id)
		{
			window.open('zcompromiso.php?Acc=crear_compromiso&id='+id+'&refrescar=2','_self');
		}
		function modulo_callcenter(id)
		{
			modal('zctrlseg.php?Acc=call_center_obs&id='+id,0,0,800,1000,'CallCenter');
		}
		</script>
		<body>
		<script language='javascript'>centrar();</script>
		<h3>Compromisos ".($OPERARIO?" del operario $OPERARIO":"")."  $Hoy</h3>";
	if($Compromisos=q($Consulta))
	{
		echo "<table border cellspacing='0' width='100%'><tr>
					<th>Usuario</th>
					<th>Siniestro</th>
					<th>Fecha Compromiso</th>
					<th>Tipo</th>
					<th>Descripción</th>
					<th>Fecha de programación</th>
					<th>Estado</th>
					<th>Fecha de Cumplimiento</th>
					<th>Adicionar</th>
					<th>Call Center</th>
					</tr>";
		while($C=mysql_fetch_object($Compromisos))
		{
			$t=date('Y-m-d H:i:s',strtotime("$C->fecha $C->hora"));
			if($C->estado=='P')
			{
				if($t>$Hoy) $Color='ffffdd';
				else $Color='ffdddd';
			}
			else
				$Color='ffffff';
			echo "<tr>
						<td>$C->usuario</td>
						<td>$C->nsiniestro</td>
						<td bgcolor='$Color'>$C->fecha $C->hora</td>
						<td>$C->ntipo</td>
						<td>$C->descripcion</td>
						<td>$C->fecha_programacion</td>
						<td>";
			if(inlist($USUARIO,'1,4,26'))  /* usuarios admitidos, 1: primario 4: call center 26: coordinador call center */
			{
				echo "<select name='estado' onchange='cumplimiento($C->id,this);'><option value='P' ".($C->estado=='P'?"selected":"").">Pendiente</option>
							<option value='C' ".($C->estado=='C'?"selected":"").">Cumplido</option>
							</select>";
			}
			echo "</td>
						<td align='center'>".($C->estado=='C'?$C->fecha_cumplimiento:" - ")."</td>
						<td align='center'><a style='cursor:pointer' onclick='adicionar_compromiso($C->siniestro);'><img src='gifs/standar/nuevo_registro.png' border='0'></a></td>
						<td align='center'><a style='cursor:pointer' onclick='modulo_callcenter($C->siniestro);'><img src='img/callphone.png' border='0'></a></td>
						</tr>";
		}
		echo "</table></body>";
	}
	else
	{
		echo "No hay compromisos registrados para este siniestro.";
	}


}

















?>