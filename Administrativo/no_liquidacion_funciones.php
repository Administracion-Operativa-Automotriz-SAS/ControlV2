<?php

/**
* RUTINAS DE APOYO DEL PROGRAMA NO_LIQUIDACION
* Este programa debe ser incluido dentro del principal que es no_liquidacion.php
*
* @copyright 2008
*/

function inicio()
{
	global $Nt_planillas, $Nt_empleados;
	$SECCION = 'LIQUIDACIONES NOMINA';
	$ESTILO = '_nomina';
	html();
	echo "
	<script language='javascript' src='inc/nomina/no_liquidacion.js'></script>
	<body leftmargin=0 topmargin=0 bottommargin=0 rightmargin=0 style='background-color:#000093;'>
	<script language='javascript'>centrar();</script>" .
	titulo_modulo("<b><font color='#ffffff'>LIQUIDACIONES DE NOMINA</font></B>");
	echo "
	<iframe id='Liquidacion_nomina' name='Liquidacion_nomina' height='500' width='600'
		style='visibility:hidden;position:fixed;top:20;left:30;border-style:solid;border-width:7px;background-color:#fdfdfd;
		border-top-color:#4FA6A9;
		border-left-color:#4FA6A9;
		border-right-color:#004344;
		border-bottom-color:#004344;z-index:99;' border='2'
		frameborder='yes' src='marcoindex.php?Acc=cargando_informacion'></iframe>
		<table border cellspacing=0 cellpadding=0 width='100%' style='font-size:15;height:90% ' bgcolor='#000093'>
			<tr>
				<th width='50%'><a class='info'";
	if($Nt_planillas) echo " style='cursor:pointer;' onclick=\"modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt_planillas',0,0,500,500,'planillas');\" ";
	echo "><span>Modificar planillas</span><font color='#ffffff'><b>Planillas</b></font></a></th>
				<form name='filtroe' id='filtroe'>
				<th><font color='#ffffff'>SUC:<select name='SUC' style='font-family:arial;font-size:9;font-weight:normal;width:180px;'
				onchange=\"var DF=document.filtroe;Iempleados.location='no_liquidacion.php?Acc=empleados&Filtro='+DF.filtro.value+'&Filtro2='+DF.SUC.value+'&Filtro3='+DF.CONT.value;\">
				<option style='width:300px;' value='*'> - - Mostrar todos - - </option>";
	if($Sucursales = q("select id,nombre,nomina_co from no_sucursal order by nombre"))
	{
		while($S = mysql_fetch_object($Sucursales))
		{
			echo "<option value='$S->id' style='background-color:$S->nomina_co;color:#000000;width:300px;' ".
			(isset($_COOKIE['NOL_empleado_filtro2'])?($_COOKIE['NOL_empleado_filtro2']==$S->id?"Selected":""):"").">$S->nombre</option>";
		}
	}
	echo "</select>";
	if($Tipos_contrato=q("select id,nombre from no_mod_contrato order by nombre"))
	{
		echo "Contr: <select name='CONT' style='font-family:arial;font-size:9;font-wieght:normal;width:180px;'
		onchange=\"var DF=document.filtroe;Iempleados.location='no_liquidacion.php?Acc=empleados&Filtro='+DF.filtro.value+'&Filtro2='+DF.SUC.value+'&Filtro3='+DF.CONT.value;\">
		<option style='width:300px;' value='*'> - - Mostrar todos - - </option>";
		while($TC=mysql_fetch_object($Tipos_contrato))
		{
			echo "<option value='$TC->id' style='color:#000000;width:300px;' ".
			(isset($_COOKIE['NOL_empleado_filtro3'])?($_COOKIE['NOL_empleado_filtro3']==$TC->id?"Selected":""):"").">$TC->nombre</option>";
		} // while
		echo "</select> Empleados</font> <input type='text' size='10' name='filtro' ".(isset($_COOKIE['NOL_empleado_filtro'])?"Value='".$_COOKIE['NOL_empleado_filtro']."'":"")."
				onkeyup=\"var DF=document.filtroe; if(DF.filtro.value=='') DF.filtro.value='*';Iempleados.location='no_liquidacion.php?Acc=empleados&Filtro='+DF.filtro.value+'&Filtro2='+DF.SUC.value+'&Filtro3='+DF.CONT.value;\">";
	}
	echo "</th>
				</form>
			</tr>
			<tr>
				<td bgcolor='#FFFFcC' style='height:50%'><iframe name='Iplanillas' id='Iplanillas' frameborder='no' height='100%' width='100%' scrolling='auto' src='no_liquidacion.php?Acc=planillas#NA_" . $_COOKIE['NOL_planilla'] . "' ></iframe></td>
				<td bgcolor='#FFFFcC'><iframe name='Iempleados' id='Iempleados' frameborder='no' height='100%' width='100%' scrolling='auto' src='no_liquidacion.php?Acc=empleados#NA_" . $_COOKIE['NOL_empleado'] . "' ></iframe></td>
			</tr>
			<tr>
				<th><font color='#ffffff' style='font-size:11;'><b>Novedades</b></font></th>
				<th><font color='#ffffff' style='font-size:11;'><b>Liquidaciones</b></font></th>
			</tr>
			<tr>
				<td bgcolor='#FFFFcC'><iframe name='Inovedades' id='Inovedades' frameborder='no' height='100%' width='100%' scrolling='auto' src='no_liquidacion.php?Acc=novedades#Captura' ></iframe></td>
				<td bgcolor='#FFFFcC'><iframe name='Iliquidaciones' id='Iliquidaciones' frameborder='no' height='100%' width='100%' scrolling='auto' src='no_liquidacion.php?Acc=liquidaciones' ></iframe></td>
			</tr>
		</table>
		<script language='javascript'>
		var PP=document.getElementById('Inovedades');
		PP.height=window.innerHeight*0.6;
		PP=document.getElementById('Iliquidaciones');
		PP.height=window.innerHeight*0.6;
		</script>
		<input type='button' value='Optimizar Tablas' onclick=\"modal('no_liquidacion.php?Acc=optimizar_tablas',0,0,10,10,'Liquidacion_nomina');this.value='Optimizar Tablas OK';\">&nbsp;
		<input type='button' value='Limpiar cookies' onclick=\"modal('no_liquidacion.php?Acc=limpiar_cookies',0,0,10,10,'Liquidacion_nomina');this.value='Limpiar Cookies OK';\">
		<input type='button' name='Ml' id='Ml' value='".
		($_COOKIE['NOL_mostrar_liquidaciones']?"Ocultar liquidaciones":"Mostrar liquidaciones").
		"' onclick=\"modal('no_liquidacion.php?Acc=mostrar_liquidaciones',0,0,10,10,'Liquidacion_nomina');\">
		<input type='button' name='Ocultar_Fijos' id='Ocultar_Fijos' value='".
		($_COOKIE['NOL_ocultar_conceptos_fijos']?"Mostrar conceptos Fijos":"Ocultar conceptos Fijos").
		"' onclick=\"modal('no_liquidacion.php?Acc=ocultar_conceptos_fijos',0,0,10,10,'Liquidacion_nomina');\">
		";
}


# -------------------------------------------------------------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------------------------------------------------------------


function liquidaciones()
{
	global $Nt_hpago;
	$ESTILO = '_nomina';
	html();
	echo"
	<script language='javascript' src='inc/nomina/no_liquidacion.js'></script>
	<body topmargin=0 leftmargin=0 rightmargin=0   style='background-color:#FFFFff;'>";
	if($_COOKIE['NOL_contrato'] && $_COOKIE['NOL_empleado'])
	{
		$idcon = $_COOKIE['NOL_contrato'];
		$idemp = $_COOKIE['NOL_empleado'];
		$PL = qo("select * from no_planilla where id=" . $_COOKIE['NOL_planilla']);
		$Nem = qo1("select concat(apellidos,' ',nombres) from profesor where id=$idemp");
		$Sal = qo1("select salario($idcon,'$PL->fecha_inicial')");
		$Co = qo("select * from no_contrato where id=$idcon");
	}
	else
	{
		echo die("SELECCIONE UN EMPLEADO");
	}
	echo "<b>$Nem</b> Básico: <b><font color='black'>$" . coma_format($Sal) . "</font></b> $Co->fecha_vinculacion - $Co->fecha_finalizacion	";
	if($Pago = qo("Select * from no_hpago where contrato=$idcon and planilla=$PL->id "))
	{
		echo "<table border cellspacing=0 width='100%'>";
		echo "<tr><td onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$Nt_hpago&id=$Pago->id',0,0,500,500,'hpago');\">
		Dias: $Pago->dias_trabajados</td><td>Sueldo: " . coma_format($Pago->sueldo) . "</td><td>Fm.Pago: " .
		menu3('forma_pago', "C,CHEQUE;B,BANCO", $Pago->forma_pago) . "</td><td>Fec.Pago: $Pago->fecha_pago</td><td " .
		($Pago->observaciones?"bgcolor='ff5533'":"") . "><img src='gifs/jus.gif' border=0
		onclick=\"modal('no_liquidacion.php?Acc=liq_obs&id=$Pago->id',50,50,350,500,'liqobs');\" style='cursor:pointer;'></td></tr>";
		echo "</table>";
		if($Conceptos_pago = q("select h.id,h.cantidad,h.valor,c.nombre,c.sigla,c.tipo,tc.color_co,tc.nombre as tn
									from no_hp_concepto h,no_concepto c,no_tipo_concepto tc
									where h.concepto=c.id and c.tipo=tc.id and h.pago=$Pago->id and
									c.tipo in (1,2) order by c.tipo,c.nombre"))
		{
			echo "<table border cellspacing=0 width='100%' style=''><tr>
			<td><b>CONCEPTO</b></td>
			<td><b>CANTIDAD</b></td>
			<td><b>DEVENGADOS</b></td>
			<td><b>DEDUCCIONES</b></td>
			</tr>";
			$Total_devengados = 0;
			$Total_deducciones = 0;
			while($CP = mysql_fetch_object($Conceptos_pago))
			{
				echo "<tr>
						<td bgcolor='$CP->color_co'><a class='info'>$CP->sigla<span>$CP->nombre ($CP->tn)</span></a></td>
						<td align='right'>$CP->cantidad</td>" .
				($CP->tipo == 1?"<td bgcolor='$CP->color_co' align='right'>" . coma_format($CP->valor) . "</td><td></td>":
					"<td></td><td bgcolor='$CP->color_co' align='right'>" . coma_format($CP->valor) . "</td>") . "
						</tr>";
			}
			echo "<tr bgcolor='#eeeeee'><td colspan='2' ><b><font color='blue'>TOTALES</font></b></td>
			<td align='right'><font color='blue'>" . coma_format($Pago->devengados) . "</font></td>
			<td align='right'><font color='blue'>" . coma_format($Pago->deducciones) . "</font></td>
			</tr><tr>
			<td align='center' colspan=2><font style='font-size:11px'><b>NETO</b></font></td>
			<td align='center' colspan=2 bgcolor='#dddddd'><font color='black' style='font-size:11px'><b>" . coma_format($Pago->neto) . "</b></font></td></tr>
			</table>";
		}
		else
		{
			echo "No hay conceptos..";
		}
		if($Conceptos_pago = q("select h.id,h.cantidad,h.valor,c.nombre,c.sigla,c.tipo,tc.color_co,tc.nombre as tn
									from no_hp_concepto h,no_concepto c,no_tipo_concepto tc
									where h.concepto=c.id and c.tipo=tc.id and h.pago=$Pago->id and
									c.tipo>2 order by c.tipo,c.nombre"))
		{
			echo "<table border cellspacing=0 width='100%' style=''><tr>
			<td ALIGN='CENTER'><b>CONCEPTO</b></td>
			<td ALIGN='CENTER'><b>CANTIDAD</b></td>
			<td ALIGN='CENTER'><b>VALOR</b></td>
			</tr>";
			while($CP = mysql_fetch_object($Conceptos_pago))
			{
				echo "<tr>
						<td><a class='info'>$CP->sigla<span>$CP->nombre ($CP->tn)</span></a></td>
						<td align='right'>$CP->cantidad</td><td bgcolor='$CP->color_co' align='right'>" . coma_format($CP->valor) . "</td>
						</tr>";
			}
			echo "</table><br /><br /><br /><br /><br />";
		}
		echo $Pago->observaciones;
	}
	else
		echo "<br /><br /><br /><br /><br /><center><b>No hay liquidación realizada.</b></center>";
	echo "</body>";
}
# -------------------------------------------------------------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------------------------------------------------------------
# -------------------------------------------------------------------------------------------------------------------------------------------------



# ##########################################################   ACUMULACION DE BASES POR PERIODO ##################################################


function optimizar_tablas()
{
	q("optimize table no_hpago,no_hp_concepto,no_novedad,no_concepto_fijo");
	echo "<body onload=\"alert('Optimizacion completada');\"></body>";
}

function limpiar_cookies()
{
	setcookie('NOL_planilla', false, time()-10);
	setcookie('NOL_empleado', false, time()-10);
	setcookie('NOL_contrato', false, time()-10);
	echo "<body onload=\"alert('Optimizacion completada');\"></body>";
}

function mostrar_liquidaciones()
{
	if(isset($_COOKIE['NOL_mostrar_liquidaciones']))
	{
		setcookie('NOL_mostrar_liquidaciones',false,time()-1);
		echo "<body onload=\"var Boton=opener.document.getElementById('Ml'); Boton.value='Mostrar liquidaciones';window.close();void(null);\"></body>";
	}
	else
	{
		setcookie('NOL_mostrar_liquidaciones','1',time() + 60 * 60 * 24 * 30);
		echo "<body onload=\"var Boton=opener.document.getElementById('Ml'); Boton.value='No mostrar liquidaciones';window.close();void(null);\"></body>";
	}
}


function ocultar_conceptos_fijos()
{
	if(isset($_COOKIE['NOL_ocultar_conceptos_fijos']))
	{
		setcookie('NOL_ocultar_conceptos_fijos',false,time()-1);
		echo "<body onload=\"var Boton=opener.document.getElementById('Ocultar_Fijos'); Boton.value='Ocultar conceptos fijos';window.close();void(null);\"></body>";
	}
	else
	{
		setcookie('NOL_ocultar_conceptos_fijos','1',time() + 60 * 60 * 24 * 30);
		echo "<body onload=\"var Boton=opener.document.getElementById('Ocultar_Fijos'); Boton.value='Mostrar conceptos fijos';window.close();void(null);\"></body>";
	}
}

function planillas()
{
	$ESTILO = '_nomina';
	html();
	echo"<body topmargin=0 leftmargin=0 rightmargin=0 style='background-color:#FFFFff;'>
	<table cellspacing=0 border width='100%'><tr>
	<th class='thn'>Periodo Pago</th>
	<th class='thn'>Código</th>
	<th class='thn'>Nombre</th>
	<th class='thn'>Fec Ini</th>
	<th class='thn'>Fec Fin</th>
	<th class='thn'>#Q</th>
	</tr>";
	if($Planillas = q("select tp.nombre as periodo,pl.* from no_planilla pl,no_tipo_planilla tp where pl.periodo_pago=tp.id order by periodo,codigo"))
	{
		while($P = mysql_fetch_object($Planillas))
		{
			echo "<a name='NA_$P->id'></a>
			<tr class='" . ($_COOKIE['NOL_planilla'] == "$P->id"?"trp":"trn") . "' >
			<td onclick=\"modal('reportes.php?Acc=ejecutar&ID=283&id=$P->id&_Previo_Ok=1',0,0,500,500,'Resumen');\">$P->periodo</td>
			<td onclick=\"modal('no_liquidacion.php?Acc=seleccionaplanilla&id=$P->id',0,0,10,10,'Liquidacion_nomina');\">$P->codigo</td>
			<td onclick=\"modal('no_liquidacion.php?Acc=seleccionaplanilla&id=$P->id',0,0,10,10,'Liquidacion_nomina');\">$P->nombre</td>
			<td onclick=\"modal('no_liquidacion.php?Acc=seleccionaplanilla&id=$P->id',0,0,10,10,'Liquidacion_nomina');\">$P->fecha_inicial</td>
			<td onclick=\"modal('no_liquidacion.php?Acc=seleccionaplanilla&id=$P->id',0,0,10,10,'Liquidacion_nomina');\">$P->fecha_final</td>
			<td align='center' onclick=\"modal('no_liquidacion.php?Acc=seleccionaplanilla&id=$P->id',0,0,10,10,'Liquidacion_nomina');\">$P->nquincena</td>
			</tr>";
		}
	}
	echo "</table></body>";
}

function empleados()
{
	global $Filtro, $ide, $Filtro2, $Filtro3,$_COOKIE;
	if($Filtro)
	{
		if($Filtro=='*')
		{
			setcookie('NOL_empleado_filtro',false,time()-1);
			$Filtro='';
		}
		else
		{
			setcookie('NOL_empleado_filtro',"$Filtro");
		}
	}
	else
	{
		$Filtro=$_COOKIE['NOL_empleado_filtro'];
	}

	if($Filtro2)
	{
		if($Filtro2=='*')
		{
			setcookie('NOL_empleado_filtro2',false,time()-1);
			$Filtro2='';
		}
		else
		{
			setcookie('NOL_empleado_filtro2',"$Filtro2");
		}
	}
	else
	{
		$Filtro2=$_COOKIE['NOL_empleado_filtro2'];
	}
	if($Filtro3)
	{
		if ($Filtro3=='*')
		{
			setcookie('NOL_empleado_filtro3',false,time()-1);
			$Filtro3='';
		}
		else
		{
			setcookie('NOL_empleado_filtro3',"$Filtro3");
		}
	}
	else
	{
		$Filtro3=$_COOKIE['NOL_empleado_filtro3'];
	}
	if(!$ide) $ide = $_COOKIE['NOL_empleado'];
	$ESTILO = '_nomina';
	html();
	if($Planilla_seleccionada)
	{
		$PL = qo("select * from no_planilla where id=$Planilla_seleccionada");
	}
	elseif($_COOKIE['NOL_planilla'])
	{
		$PL = qo("select * from no_planilla where id=" . $_COOKIE['NOL_planilla']);
	}
	else
		echo die("SELECCIONE UNA PLANILLA");
	echo"<body topmargin=0 leftmargin=0 rightmargin=0 style='background-color:#FFFFff;'>
	<table cellspacing=0 border width='100%'><tr>
	<th class='thn'>#</th>
	<th class='thn'>Nombres</th>
	<th class='thn'>Fec Ini</th>
	<th class='thn'>Fec Fin</th>
	</tr>";

	if($Filtro) $Filtroa = " and em.apellidos like '$Filtro%' ";
	else $Filtroa = '';
	if($Filtro2) $Filtroa2 = "and co.sucursal=$Filtro2 ";
	else $Filtroa2 = '';
	if($Filtro3) $Filtroa3 = "and co.modelo_contrato=$Filtro3 ";
	else $Filtroa3 = '';


	if($Empleados = q("select concat(em.apellidos,' ',em.nombres) as nemp,co.fecha_vinculacion as fv,em.identificacion,
							co.fecha_finalizacion as ff,em.id,co.id as idc,su.nomina_co as bcolor,su.nombre as nsuc,em.foto_f
							FROM profesor em, no_contrato co, no_sucursal su
							WHERE em.id=co.empleado and su.id=co.sucursal and co.id=contrato_activo(co.empleado,'$PL->fecha_final') $Filtroa $Filtroa2 $Filtroa3
							ORDER BY nemp"))
	{
		require('inc/link.php');
		$Contador=1;
		while($E = mysql_fetch_object($Empleados))
		{
			echo "
			<tr style='cursor:pointer;' class='" . ($ide == $E->id?"trp":"trn") . "' onclick=\"modal('no_liquidacion.php?Acc=seleccionaempleado&id=$E->id&idc=$E->idc&Filtro=$Filtro&Filtro2=$Filtro2',0,0,10,10,'Liquidacion_nomina');
			this.backgroundColor='#E8E8C5';\"><td>$Contador</td>
			<td bgcolor='$E->bcolor' ><a name='NA_$E->id'>$E->nemp</a></td>
			<td " . ($E->fv >= $PL->fecha_inicial && $E->fv <= $PL->fecha_final?"bgcolor='#bbffbb'":"") . ">$E->fv</td>
			<td " . ($E->ff >= $PL->fecha_inicial && $E->ff <= $PL->fecha_final?"bgcolor='#ffbbbb'":"") . ">$E->ff</td>
			</tr>";
			$Contador++;
		}
		mysql_close($LINK);
	}
	else
	{
		echo "<tr><td colspan=3> No encuentro empleados $SQL</td></tr>";
	}
	echo "</table><br /><br /><br /><br /><br /><br /><br /><br /></body>";
}

function adiciona_novedad()
{
	global $idcon, $idpla, $Novedad, $Cantidad, $Valor, $id;
	if($id)
	{
		q("update no_novedad set concepto='$Novedad',cantidad='$Cantidad',valor='$Valor' where id=$id");
	}
	else
	{
		q("insert into no_novedad (contrato,planilla,concepto,cantidad,valor) values
		('$idcon','$idpla','$Novedad','$Cantidad','$Valor')");
	}
	echo "<body onload=\"parent.Inovedades.location='no_liquidacion.php?Acc=novedades';\"></body>";
}

function adiciona_novedad_fija()
{
	global $idcon, $Concepto, $Cantidad, $Valor, $Fecha_inicial, $Fecha_final, $id, $Nquincena, $orden;
	if($id)
	{
		q("update no_concepto_fijo set concepto='$Concepto',cantidad='$Cantidad',valor='$Valor',fecha_inicial='$Fecha_inicial',
			fecha_final='$Fecha_final',nquincena='$Nquincena',orden='$orden' where id=$id");
	}
	else
	{
		q("insert into no_concepto_fijo (contrato,concepto,cantidad,valor,fecha_inicial,fecha_final,nquincena,orden) values
		('$idcon','$Concepto','$Cantidad','$Valor','$Fecha_inicial','$Fecha_final','$Nquincena','$orden')");
	}
	echo "<body onload=\"parent.Inovedades.location='no_liquidacion.php?Acc=novedades';\"></body>";
}

function borra_novedad()
{
	global $id;
	q("delete from no_novedad where id=$id");
	echo "<body onload=\"opener.location.reload();window.close();void(null);\"></body>";
}

function borra_novedad_fija()
{
	global $id;
	q("delete from no_concepto_fijo where id=$id");
	echo "<body onload=\"opener.location.reload();window.close();void(null);\"></body>";
}

function borra_novedades()
{
	global $idcon, $Pl;
	q("delete from no_novedad where contrato=$idcon and planilla=$Pl");
	echo "<body onload=\"opener.location.reload();window.close();void(null);\"></body>";
}

function borra_novedades_fijas()
{
	global $idcon;
	q("delete from no_concepto_fijo where contrato=$idcon");
	echo "<body onload=\"opener.location.reload();window.close();void(null);\"></body>";
}

function seleccionaplanilla()
{
	global $id;
	setcookie('NOL_planilla', "$id", time() + 60 * 60 * 24 * 30);
	echo "<body onload=\"parent.Iplanillas.location='no_liquidacion.php?Acc=planillas';
	parent.Iempleados.location='no_liquidacion.php?Acc=empleados&Planilla_seleccionada=$id#NA_" . $_COOKIE['NOL_empleado'] . "';
	parent.Inovedades.location='no_liquidacion.php?Acc=novedades';
	parent.Iliquidaciones.location='no_liquidacion.php?Acc=liquidaciones';
	parent.Liquidacion_nomina.location='marcoindex.php?Acc=cargando_informacion';\"></body>";
}

function seleccionaempleado()
{
	global $id, $idc, $Filtro, $Filtro2;
	setcookie('NOL_empleado', "$id", time() + 60 * 60 * 24 * 30);
	setcookie('NOL_contrato', "$idc", time() + 60 * 60 * 24 * 30);
	echo "<body onload=\"parent.Iempleados.location='no_liquidacion.php?Acc=empleados&ide=$id&Filtro=$Filtro&Filtro2=$Filtro2#NA_$id';
	parent.Inovedades.location='no_liquidacion.php?Acc=novedades';
	parent.Iliquidaciones.location='no_liquidacion.php?Acc=liquidaciones';
	parent.Liquidacion_nomina.location='marcoindex.php?Acc=cargando_informacion';\"></body>";
}

function liq_obs()
{
	global $id;
	$Pago = qo("select * from no_hpago where id=$id");
	html();
	echo "<body>" . titulo_modulo("<b>Observaciones</b>");
	echo "<form action='no_liquidacion.php' method='post' target='_self' name='forma' id='forma'>
	Observaciones:<br />
	<textarea name='observaciones' cols='80' rows='10' style='font-family:arial;font-size:11px;'>$Pago->observaciones</textarea>
	<input type='hidden' name='Acc' value='liq_obs_ok'>
	<input type='hidden' name='id' value='$id'>
	<br /><input type='submit' value='GRABAR OBSERVACIONES'>
	</form></body>";
}

function liq_obs_ok()
{
	global $id, $observaciones;
	q("update no_hpago set observaciones='$observaciones' where id=$id");
	echo "<body onload='window.close();void(null);'></body>";
}
# ###############################  INICIO FUNCIONES PROPIAS DE NOMINA ####################################################################
# ###############################  INICIO FUNCIONES PROPIAS DE NOMINA ####################################################################
# ###############################  INICIO FUNCIONES PROPIAS DE NOMINA ####################################################################
# ###############################  INICIO FUNCIONES PROPIAS DE NOMINA ####################################################################
# ###############################  INICIO FUNCIONES PROPIAS DE NOMINA ####################################################################
# ###############################  INICIO FUNCIONES PROPIAS DE NOMINA ####################################################################

function define_funciones_mysql_nomina()
{
	# # PER(fecha) Retorna los cuatro digitos del año y el numero del semestre a partir de la fecha dada
	q("drop function if exists per");
	q("create function per (fecha date) returns int(6) no sql
		begin
		declare mes int(2) default 0;
		declare ano int(4) default 0;
		set mes=month(fecha);
		set ano=year(fecha);
		return if(mes>0,if(mes<7,ano*100+1,ano*100+2),0);
		end", 1);
	# # CONTRATO_ACTIVO(id_empleado,fecha). devuelve el id del contrato si está activo en la fecha dada
	q("drop function if exists contrato_activo");
	q("create function contrato_activo (iden_empleado int(10),Fecha date) returns int(10) READS SQL DATA
		BEGIN
		DECLARE iden_contrato int(10) default 0;
		SELECT id into iden_contrato FROM no_contrato Contrato
		WHERE ((Fecha between Contrato.fecha_vinculacion and Contrato.fecha_finalizacion) or (Fecha>=Contrato.fecha_vinculacion and Contrato.fecha_finalizacion='0000-00-00'))
		and Contrato.empleado=iden_empleado ORDER BY Contrato.fecha_vinculacion desc limit 1;
		return iden_contrato;
		END", 1);
	# # CARGO_ACTIVO(id_contrato,fecha). devuelve el id del cargo si está activo en la fecha dada
	q("drop function if exists cargo_activo");
	q("create function cargo_activo (iden_contrato int(10),Fecha_cargo date) returns int(10) READS SQL DATA
		BEGIN
		DECLARE iden_cargo int(10) default 0;
		SELECT id into iden_cargo FROM no_emp_cargo Cargo
		WHERE ((Fecha_cargo between Cargo.fecha_inicial and Cargo.fecha_final) or (Fecha_cargo>=Cargo.fecha_inicial and Cargo.fecha_final='0000-00-00'))
		and Cargo.contrato=iden_contrato ORDER BY Cargo.fecha_inicial desc limit 1;
		return iden_cargo;
		END", 1);
	# # CARGO_ACTIVOPER(id_contrato,periodo). devuelve el id del cargo si está activo en el periodo dado
	q("drop function if exists cargo_activoper");
	q("create function cargo_activoper (iden_contrato int(10),Periodo char(6)) returns int(10) READS SQL DATA
		BEGIN
		DECLARE iden_cargo int(10) default 0;
		DECLARE Fecha_per_ini date;
		DECLARE Fecha_per_fin date;
		SET Fecha_per_ini=str_to_date(concat(left(periodo,4),if(right(periodo,2)='01','0101','0701')),'%Y%m%d');
		SET Fecha_per_fin=str_to_date(concat(left(periodo,4),if(right(periodo,2)='01','0630','1231')),'%Y%m%d');
		SELECT id into iden_cargo FROM no_emp_cargo Cargo
		WHERE (Cargo.fecha_inicial between Fecha_per_ini and Fecha_per_fin or
		Cargo.fecha_final between Fecha_per_ini and Fecha_per_fin or
		(Cargo.fecha_inicial<=Fecha_per_ini and Cargo.fecha_final>=Fecha_per_fin) or
		(Cargo.fecha_inicial<=Fecha_per_fin and Cargo.fecha_final='0000-00-00' ))
		and Cargo.contrato=iden_contrato ORDER BY Cargo.fecha_inicial desc limit 1;
		return iden_cargo;
		END", 1);
	# # CONTRATO_ACTIVOPER(id_empleado,periodo) devuelve el id del contrato si está activo en el periodo dado
	q("drop function if exists contrato_activoper");
	q("create function contrato_activoper (Id_empleado int(10),Periodo char(6)) returns int(10) READS SQL DATA
		BEGIN
		DECLARE Id_contrato int(10) default 0;
		DECLARE Fecha_per_ini date;
		DECLARE Fecha_per_fin date;
		SET Fecha_per_ini=str_to_date(concat(left(periodo,4),if(right(Periodo,2)='01','0101','0701')),'%Y%m%d');
		SET Fecha_per_fin=str_to_date(concat(left(periodo,4),if(right(Periodo,2)='01','0630','1231')),'%Y%m%d');
		SELECT id into id_contrato FROM no_contrato Contrato
		WHERE (Contrato.fecha_vinculacion between Fecha_per_ini and Fecha_per_fin or
		Contrato.fecha_finalizacion between Fecha_per_ini and Fecha_per_fin or
		(Contrato.fecha_vinculacion<=Fecha_per_ini and Contrato.fecha_finalizacion>=Fecha_per_fin) or
		(Contrato.fecha_vinculacion<=Fecha_per_fin and Contrato.fecha_finalizacion='0000-00-00' ))
		and Contrato.empleado=Id_empleado ORDER BY Contrato.fecha_vinculacion desc limit 1;
		return Id_contrato;
		END", 1);
	# # MES(fecha). Retorna el nombre del mes a partir de una fecha dada
	q("drop function if exists mes");
	q("create function mes (fecha date) returns varchar(10) NO SQL
		BEGIN
		DECLARE nombre_mes varchar(10) default '';
		DECLARE num_mes int(2) default 0;
		SET num_mes=month(fecha);
		SELECT case num_mes when 1 then 'enero' when 2 then 'febrero' when 3 then 'marzo' when 4 then 'abril' when 5 then 'mayo' when 6 then 'junio'
		when 7 then 'julio' when 8 then 'agosto' when 9 then 'septiembre' when 10 then 'octubre' when 11 then 'noviembre' when 12 then 'diciembre' end into nombre_mes;
		return nombre_mes;
		END", 1);
	# # FECHA_COMPLETA(fecha) retorna en español la redacción de la fecha completa
	q("drop function if exists fecha_completa");
	q("create function fecha_completa (fecha date) returns varchar(50) NO SQL
		BEGIN
		DECLARE nombre_mes varchar(10) default '';
		DECLARE num_mes int(2) default 0;
		SET num_mes=month(fecha);
		SELECT case num_mes when 1 then 'enero' when 2 then 'febrero' when 3 then 'marzo' when 4 then 'abril' when 5 then 'mayo' when 6 then 'junio'
		when 7 then 'julio' when 8 then 'agosto' when 9 then 'septiembre' when 10 then 'octubre' when 11 then 'noviembre' when 12 then 'diciembre' end into nombre_mes;
		return concat(day(fecha),' de ',nombre_mes,' de ',year(fecha));
		END", 1);
	# # SALARIO(idcontrato,fecha). Trae el salario activo de acuerdo a la fecha dada
	q("drop function if exists salario");
	q("create function salario (idcontrato int(10),fecha date) returns bigint(12) READS SQL DATA
		BEGIN
		declare salario bigint(12) default 0;
		SELECT monto_salario into salario from no_salario Salario where Salario.contrato=idcontrato and Salario.fecha_salario<=fecha order by
		Salario.fecha_salario desc limit 1;
		return salario;
		END", 1);
	# #  MALTERNA(plan_estudio,historico_ag,materia). trae el nombre de una materia alterna en informes para poder mostrar materias originales y electivas y organizaciones musicales
	q("drop function if exists malterna");
	q("create function malterna (ple int(10),his int(10), mat int(10)) returns varchar(200) READS SQL DATA
		BEGIN
		DECLARE nueva_materia int(10) default 0;
		DECLARE nombre_nueva_materia varchar(200);
		SELECT cambio into nueva_materia from altmat where historia=his and plan_estudio=ple and materia=mat limit 1;
		SELECT concat(nombre,' ',sigla,' ',codigo) into nombre_nueva_materia from materia where id=nueva_materia;
		return nombre_nueva_materia;
		END", 1);
	# #  EGRESADO_ANO (idegresado) retorna el año del egresado
	q("drop function if exists egresado_ano");
	q("create function egresado_ano (egr int(10)) returns smallint(4) READS SQL DATA
		BEGIN
		DECLARE ano_egresado int(10) default 0;
		SELECT year(fecha_egreso) into ano_egresado from egresados where alumno=egr limit 1;
		return ano_egresado;
		END", 1);
	# # TITULO_ACTIVOPER(id_empleado,periodo,nivel (00-07 snies o 00-86 todos ideal <80 para solo snies)) retorna el id del titulo maximo de estudios o nivel de estudios
	q("drop function if exists titulo_activoper");
	q("create function titulo_activoper (Id_empleado int(10),Periodo char(6),Nivel char(2)) returns int(10) READS SQL DATA
		BEGIN
		DECLARE Id_titulo int(10) default 0;
		DECLARE Fecha_per_fin date;
		SET Fecha_per_fin=str_to_date(concat(left(Periodo,4),if(right(Periodo,2)='01','0630','1231')),'%Y%m%d');
		SELECT id into Id_titulo FROM pro_titulo Titulo
		WHERE Titulo.docente=id_empleado and Titulo.fecha_graduacion<=Fecha_per_fin and Titulo.nivel<Nivel order by Titulo.fecha_graduacion desc limit 1;
		return Id_titulo;
		END", 1);
	# # ESTUDIO_ACTIVOPER(id_titulo,periodo) determina si el id del titulo dado se inició o se finalizó dentro de las fechas limites del periodo dado
	q("drop function if exists estudio_activoper");
	q("create function estudio_activoper (id_titulo int(10),periodo char(6)) returns int(10) READS SQL DATA
		BEGIN
		DECLARE id_titulo_buscado int(10) default 0;
		DECLARE fecha_per_ini date;
		DECLARE fecha_per_fin date;
		SET fecha_per_ini=str_to_date(concat(left(periodo,4),if(right(periodo,2)='01','0101','0701')),'%Y%m%d');
		SET fecha_per_fin=str_to_date(concat(left(periodo,4),if(right(periodo,2)='01','0630','1231')),'%Y%m%d');
		SELECT id into id_titulo_buscado FROM pro_titulo Titulo
		WHERE Titulo.id=id_titulo and
		(   (Titulo.fecha_inicial between fecha_per_ini and fecha_per_fin) or
			 (Titulo.fecha_inicial<=fecha_per_fin and Titulo.fecha_graduacion='0000-00-00') or
			 (Titulo.fecha_inicial<fecha_per_ini and Titulo.fecha_graduacion>=fecha_per_fin) or
			 (Titulo.fecha_graduacion between fecha_per_ini and fecha_per_fin)
		)	 order by Titulo.fecha_graduacion desc limit 1;
		return id_titulo_buscado;
		END", 1);
	# # SNIES_TIPO_ID(dato) retorna el codigo snies de un tipo de identidad
	q("drop function if exists snies_tipo_id");
	q("create function snies_tipo_id (dato char(1)) returns char(2) NO SQL
		BEGIN
		DECLARE resultado char(2) default 'CC';
		SELECT CASE dato when 'C' then 'CC' when 'T' then 'TI' when 'E' then 'CE' when 'P' then 'PS'
		when 'R' then 'RC' when 'I' then 'NP' when 'S' then 'SE' when 'K' then 'CA' when 'F' then 'TP' when 'U' then 'SNP' when 'V' then 'VA'  when '' then 'CC' END into resultado;
		return resultado;
		END", 1);
	# # SNIES_ESTADO_CIVIL(dato) retorna el codigo snies de un estado civil
	q("drop function if exists snies_estado_civil");
	q("create function snies_estado_civil (dato char(1)) returns char(2) NO SQL
		BEGIN
		DECLARE resultado char(2) default '02';
		SELECT CASE dato when 'C' then '02' when 'S' then '01' when 'F' then '03' when 'V' then '04'
		when 'U' then '05' when 'R' then '06' when 'M' then '07' when '' then '01' END into resultado;
		return resultado;
		END", 1);
#	# # SNIES PLAN_ACTIVOPER(periodo) retorna el id del plan activo hasta el periodo dado
#	q("drop function if exists plan_activoper");
#	q("create function plan_activoper (Periodo char(6)) returns int(10) READS SQL DATA
#		BEGIN
#		DECLARE Resultado int(10) default 0;
#		DECLARE Fecha_per_fin date;
#		SET Fecha_per_fin=str_to_date(concat(left(Periodo,4),if(right(Periodo,2)='01','0630','1231')),'%Y%m%d');
#		SELECT id into Resultado FROM plan_estudio where fecha_inicial<=Fecha_per_fin order by fecha_inicial desc limit 1;
#		return Resultado;
#		END", 1);
	# # PARTE_NOMBRE(dato) retorna una parte del nombre o apellido
	q("drop function if exists parte_nombre");
	q("create function parte_nombre (dato varchar(100),parte tinyint(1)) returns varchar(100) no sql
		BEGIN
		return if(parte=1,if(instr(dato,' '),left(dato,instr(dato,' ')-1),dato),if(instr(dato,' '),substr(dato,instr(dato,' ')+1),'null'));
		END", 1);
	html();

	echo "<body>" . titulo_modulo("Definición de funciones MySQL") . "<br />
		Si no hay errores puede cerrar esta ventana, las funciones fueron creadas correctamente
		<input type='button' value='Cerrar esta Ventana' onclick='window.close();void(null);'></body>";
}

function exporta_fijo()
{
	global $id;
	$PL = qo("select * from no_planilla where id=" . $_COOKIE['NOL_planilla']);
	$DF=qo("Select * from no_concepto_fijo where id='$id'");
	html();
	echo "<script language='javascript' src='inc/nomina/no_liquidacion.js'></script>
	<body onload='centrar(500,500);'>" . titulo_modulo("Exportar fijos:", 1);
	if(isset($_COOKIE['NOL_empleado_filtro'])) $Filtro1=$_COOKIE['NOL_empleado_filtro']; else $Filtro1='';
	if(isset($_COOKIE['NOL_empleado_filtro2'])) $Filtro2=$_COOKIE['NOL_empleado_filtro2']; else $Filtro2='';
	if(isset($_COOKIE['NOL_empleado_filtro3'])) $Filtro3=$_COOKIE['NOL_empleado_filtro3']; else $Filtro3='';
	echo "Filtros encontrados: <br />Filtro por nombre: $Filtro1<br />Filtro por Sucursal: $Filtro2<br />Filtro por modelo de contrato: $Filtro3<br />";
	if($Empleados=q("select pr.id,co.id as coid,pr.apellidos,pr.nombres from profesor pr, no_contrato co where pr.id=co.empleado ".
		($Filtro1?" and pr.apellidos like '$Filtro1%' ":"").
		($Filtro2?" and co.sucursal='$Filtro2' ":"").
		($Filtro3?" and co.modelo_contrato='$Filtro3' ":"").
		" and co.id=contrato_activo(co.empleado,'$PL->fecha_final') order by pr.apellidos,pr.nombres" ))
	{
		$Cantidad_seleccionados=mysql_num_rows($Empleados);
		echo "Número de empleados seleccionados: $Cantidad_seleccionados<br />
		<table border cellspacing='0'>";
		require('inc/link.php');
		while($E=mysql_fetch_object($Empleados))
		{
			echo "<tr><td>$E->apellidos $E->nombres</td>";
			if($Esta=mysql_query("select id from no_concepto_fijo where contrato=$E->coid and concepto=$DF->concepto",$LINK))
			{
				if(mysql_num_rows($Esta)) {echo "<td>Ya existe</td>";}
				else
				{
					if(mysql_query("insert into no_concepto_fijo (contrato,concepto,orden,cantidad,valor,fecha_inicial,fecha_final,nquincena)
						values ('$E->coid','$DF->concepto','$DF->orden','$DF->cantidad','$DF->valor','$DF->fecha_inicial','$DF->fecha_final','$DF->nquincena')",$LINK))
					{
						echo "<td>Concepto insertado</td>";
					}
					else
					{
						echo "<td>No se pudo insertar</td>";
					}
				}
			}
			else
			{
				echo "<td>No se pudo consultar</td>";
			}
			echo "</tr>";
		}
		mysql_close($LINK);
		echo "</body>";
	}
	else
	{
		echo "No existen empleados con los filtros actuales. Cambie los filtros e intentelo de nuevo.<br /><br />
		<input type='button' value='Cerrar esta ventana' onclick=\"window.close();void(null);\"></body>";
	}

}

function exporta_novedad()
{
	global $id;
	$PL = qo("select * from no_planilla where id=" . $_COOKIE['NOL_planilla']);
	$DF=qo("Select * from no_novedad where id='$id'");
	html();
	echo "<script language='javascript' src='inc/nomina/no_liquidacion.js'></script>
	<body onload='centrar(500,500);'>" . titulo_modulo("Exportar novedades:", 1);
	if(isset($_COOKIE['NOL_empleado_filtro'])) $Filtro1=$_COOKIE['NOL_empleado_filtro']; else $Filtro1='';
	if(isset($_COOKIE['NOL_empleado_filtro2'])) $Filtro2=$_COOKIE['NOL_empleado_filtro2']; else $Filtro2='';
	if(isset($_COOKIE['NOL_empleado_filtro3'])) $Filtro3=$_COOKIE['NOL_empleado_filtro3']; else $Filtro3='';
	echo "Filtros encontrados: <br />Filtro por nombre: $Filtro1<br />Filtro por Sucursal: $Filtro2<br />Filtro por modelo de contrato: $Filtro3<br />";
	if($Empleados=q("select pr.id,co.id as coid,pr.apellidos,pr.nombres from profesor pr, no_contrato co where pr.id=co.empleado ".
		($Filtro1?" and pr.apellidos like '$Filtro1%' ":"").
		($Filtro2?" and co.sucursal='$Filtro2' ":"").
		($Filtro3?" and co.modelo_contrato='$Filtro3' ":"").
		" and co.id=contrato_activo(co.empleado,'$PL->fecha_final') order by pr.apellidos,pr.nombres" ))
	{
		$Cantidad_seleccionados=mysql_num_rows($Empleados);
		echo "Número de empleados seleccionados: $Cantidad_seleccionados<br />
		<table border cellspacing='0'>";
		require('inc/link.php');
		while($E=mysql_fetch_object($Empleados))
		{
			echo "<tr><td>$E->apellidos $E->nombres</td>";
			if($Esta=mysql_query("select id from no_novedad nov where nov.contrato=$E->coid and nov.planilla=$PL->id and nov.concepto=$DF->concepto",$LINK))
			{
				if(mysql_num_rows($Esta)) {echo "<td>Ya existe</td>";}
				else
				{
					if(mysql_query("insert into no_novedad (contrato,planilla,concepto,cantidad,valor)
						values ('$E->coid','$PL->id','$DF->concepto','$DF->cantidad','$DF->valor')",$LINK))
					{
						echo "<td>Concepto insertado</td>";
					}
					else
					{
						echo "<td>No se pudo insertar</td>";
					}
				}
			}
			else
			{
				echo "<td>No se pudo consultar</td>";
			}
			echo "</tr>";
		}
		mysql_close($LINK);
		echo "</body>";
	}
	else
	{
		echo "No existen empleados con los filtros actuales. Cambie los filtros e intentelo de nuevo.<br /><br />
		<input type='button' value='Cerrar esta ventana' onclick=\"window.close();void(null);\"></body>";
	}

}

function traer_fijos()
{
	global $idcon;
	$PL = qo("select * from no_planilla where id=" . $_COOKIE['NOL_planilla']);
	html();
	echo "<script language='javascript' src='inc/nomina/no_liquidacion.js'></script>
	<body>" . titulo_modulo("Traer conceptos fijos de:", 0);
	echo "<form action='no_liquidacion.php' method='post' target='_self' name='forma' id='forma'>" .
	menu1("ORIGEN", "Select co.id,concat(em.apellidos,' ',em.nombres) as nemp
							FROM profesor em, no_contrato co
							WHERE em.id=co.empleado and co.id=contrato_activo(co.empleado,'$PL->fecha_final') ORDER BY nemp") . "<input type='submit' value='Importar'>
	<input type='hidden' name='Acc' value='traer_fijos_ok'>
	<input type='hidden' name='idcon' value='$idcon'>
	</form>
	<input type='button' value='Cancelar' onclick=\"window.close();void(null);\">
	</body>";
}

function traer_fijos_ok()
{
	global $idcon, $ORIGEN;
	html();
	q("insert into no_concepto_fijo (contrato,concepto,orden,cantidad,valor,fecha_inicial,fecha_final,nquincena)
		select $idcon,nc.concepto,nc.orden,nc.cantidad,nc.valor,nc.fecha_inicial,nc.fecha_final,nc.nquincena from no_concepto_fijo nc where
		nc.contrato=$ORIGEN ");
	echo "<script language='javascript' src='inc/nomina/no_liquidacion.js'></script>
	<body onload=\"opener.location.reload();window.close();void(null);\">";
}



?>
