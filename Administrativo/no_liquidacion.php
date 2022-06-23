<?php

/**
* CAPTURA DE NOVEDADES Y LIQUIDACION DE NOMINA
*
* @version $Id$
* @copyright 2008
*/
include('inc/sess.php');
include('inc/funciones_.php');
include('no_liquidacion_funciones.php');

$Nt_planillas = tu('no_planilla', 'id');
$Nt_empleados = tu('profesor', 'id');
$Nt_conceptos = tu('no_concepto', 'id');
$Nt_salarios = tu('no_salario', 'id');
$Nt_hpago = tu('no_hpago', 'id');
$Nt_beneficios = tu('no_beneficio', 'id');
$Nt_prestamos = tu('no_emp_prestamo', 'id');
$Nt_ley100 = tu('no_emp_ley100', 'id');
$Nt_Contrato=tu('no_contrato','id');
$Nt_Anexos=tu('no_anexo','id');


if($Acc == 'liquidar')
{
	$ACUMULADO_MENSUAL=true;
	$Observaciones_pago='';
	$F =qo1("select fecha_final from no_planilla where id=" . $_COOKIE['NOL_planilla']);
	$PL=qo("select pl.*,tp.dias from no_planilla pl,no_tipo_planilla tp where pl.periodo_pago=tp.id and pl.id=". $_COOKIE['NOL_planilla']);
	$Ano_liquidacion = date('Y', strtotime($F));
	if(!$PARAMETROS = qo("select * from no_parametros where ano=$Ano_liquidacion")) die("Parametros de nomina no cargados.");

}
if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc . '();');
	die();
}
html();
echo "<body onload=\"modal('no_liquidacion.php?Acc=inicio',0,0,500,500,'Nomina');window.close();void(null);\"></body>";
# ###########################################################################################################################################################
# ###########################################################################################################################################################
# ###########################################################################################################################################################
# ###########################################################################################################################################################
# ###########################################################################################################################################################
# ###########################################################################################################################################################
# ###########################################################################################################################################################
function liquidar()
{
	require_once('no_liquidacion_rutinas.php');
	global $ACUMULADO_MENSUAL, $PL, $Observaciones_pago;
	$TConteo=$_COOKIE['Termo_conteo']+1;
	$TTotal=$_COOKIE['Termo_tiempo_total'];
	setcookie('Termo_conteo',$TConteo);
	$Tiempo_actual=microtime(true);
	$Diferencia_tiempo=$Tiempo_actual-$_COOKIE['Termo_tiempo'];
	setcookie('Termo_tiempo_total',$TTotal+$Diferencia_tiempo);
	setcookie('Termo_tiempo',$Tiempo_actual);
	if($Empleado=qo1("select idemp from tmpi_planilla order by id limit 1"))
	{
		html();
		echo "<body onload=\"document.location.reload();\">";
	}
	else
	{
		echo "<body onload=\"window.close();void(null);opener.parent.Iliquidaciones.location='no_liquidacion.php?Acc=liquidaciones';L_oculta();\"\"></body>";
		die();
	}
	$Em = qo("select * from profesor where id=$Empleado");
	$Observaciones_pago='';
	$Faltan=$_COOKIE['Termo_limite']-$TConteo;
	$Promedio=$_COOKIE['Termo_tiempo_total']/($TConteo?$TConteo:1);
	$Tiempo_que_falta=round($Promedio*$Faltan,0);
	$Minutos_que_faltan=floor($Tiempo_que_falta/60);$Tiempo_que_falta=$Tiempo_que_falta-($Minutos_que_faltan*60);
	$TF="$Minutos_que_faltan:$Tiempo_que_falta";
	echo "<b>$Em->apellidos $Em->nombres</b><br>".termo()."<br />Faltan $Faltan Finalizará en aproximadamente: $TF.";
	if($Contrato = qo("select no_contrato.*,salario(no_contrato.id,'$PL->fecha_final') as salario
							from no_contrato where empleado=$Empleado and no_contrato.id=contrato_activo($Empleado,'$PL->fecha_final')"))
	{
		if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.="Dias a pagar: $PL->dias<br />";
		# ## busqueda del historico de pagos, si no esta, lo crea aqui
		if(!$HP = qo1("select id from no_hpago where contrato=$Contrato->id and planilla=$PL->id "))
			$HP = q("insert into no_hpago (contrato,planilla) values ($Contrato->id,$PL->id)");
		q("delete from no_hp_concepto where pago=$HP");
		$UltimoId=qo1("select max(id) from no_hp_concepto")+1;
		q("alter table no_hp_concepto auto_increment=$UltimoId");
		q("update no_hpago set dias_trabajados=0,salario_basico=0,sueldo=0,base_ibc=0,base_ibc_adicional=0,
			base_prima=0,base_cesantias=0,base_ces_adicional=0,base_bonificacion=0,base_parafiscales=0,devengados=0,
			deducciones=0,neto=0,base_retencion=0,base_ret_adicional=0,dret_pension_obl=0,dret_pension_vol=0,
			dret_afc=0,base_ret_gravable=0,base_ret_gravable_c=0,dret_vivienda=0,dret_saludeduc=0,dret_saludeduc_c=0,
			base_ret_final=0,base_ret_final_c=0,retencion_valor=0,retencion_contingent=0 where id=$HP");
		# #------------
		# #------------
		$SQL_FIJOS = "select con.id,con.nombre,cf.cantidad,cf.valor,cf.fecha_inicial,cf.fecha_final,rut.rutina,rut.pad1,cf.orden
				from no_concepto_fijo cf,no_concepto con,no_con_formula rut,no_tipo_concepto tc
				WHERE cf.contrato=$Contrato->id and cf.concepto=con.id and con.rutina=rut.id and con.tipo=tc.id and
				((cf.fecha_inicial='0000-00-00' and cf.fecha_final='0000-00-00') or (if(cf.fecha_inicial!='0000-00-00',cf.fecha_inicial<='$PL->fecha_final',1)
				 and if(cf.fecha_final!='0000-00-00',cf.fecha_final>='$PL->fecha_inicial',1))) and if(cf.nquincena!=0,cf.nquincena='$PL->nquincena',1)";
		$SQL_NOVEDADES = "select con.id,con.nombre,nv.cantidad,nv.valor,rut.rutina,rut.pad1 from no_novedad nv,no_concepto con,no_con_formula rut,no_tipo_concepto tc
			WHERE nv.contrato=$Contrato->id and nv.concepto=con.id and con.rutina=rut.id and con.tipo=tc.id and nv.planilla=$PL->id";

		# # CONCEPTOS MOMENTO: 2 PREVIO A LOS CONCEPTOS FIJOS DEVENGADOS
		if($PL->nquincena!=3) if($CPS = q("$SQL_FIJOS and tc.momento=2 order by con.tipo,cf.orden")) procesa_conceptos($CPS, $Contrato , $PL , $HP);
		if($CPS = q("$SQL_NOVEDADES and tc.momento=2 order by con.tipo")) procesa_conceptos($CPS, $Contrato , $PL , $HP);

		# # CONCEPTOS MOMENTO: 4 CONCEPTOS DEVENGADOS (FIJOS Y NOVEDADES)
		if($PL->nquincena!=3) if($CPS = q("$SQL_FIJOS and tc.momento=4 order by con.tipo,cf.orden")) procesa_conceptos($CPS, $Contrato , $PL , $HP);
		if($CPS = q("$SQL_NOVEDADES and tc.momento=4 order by con.tipo")) procesa_conceptos($CPS, $Contrato , $PL , $HP);

		# ## acumula bases de devengados de la quincena o periodo liquidado
		# ## ----------------------------------------------------------------
		acumular_bases($HP);
		$ACUMULADO_MENSUAL = acumular_mes($Contrato->id, $PL->id);

		# # CONCEPTOS MOMENTO: 10 PREVIO A LOS CONCEPTOS FIJOS DEDUCCIONES
		if($PL->nquincena!=3) if($CPS = q("$SQL_FIJOS and tc.momento=10 order by con.tipo,cf.orden")) procesa_conceptos($CPS, $Contrato , $PL , $HP);
		if($CPS = q("$SQL_NOVEDADES and tc.momento=10 order by con.tipo")) procesa_conceptos($CPS, $Contrato , $PL , $HP);

		# # CONCEPTOS MOMENTO: 12 CONCEPTOS DEDUCCIONES (FIJOS Y NOVEDADES)
		if($PL->nquincena!=3) if($CPS = q("$SQL_FIJOS and tc.momento=12 order by con.tipo,cf.orden")) procesa_conceptos($CPS, $Contrato , $PL , $HP);
		if($CPS = q("$SQL_NOVEDADES and tc.momento=12 order by con.tipo")) procesa_conceptos($CPS, $Contrato , $PL , $HP);

		# # CONCEPTOS MOMENTO: 18 DESPUES DE CONCEPTOS DEDUCCIONES
		if($PL->nquincena!=3) if($CPS = q("$SQL_FIJOS and tc.momento=18 order by con.tipo,cf.orden")) procesa_conceptos($CPS, $Contrato , $PL , $HP);
		if($CPS = q("$SQL_NOVEDADES and tc.momento=18 order by con.tipo")) procesa_conceptos($CPS, $Contrato , $PL , $HP);
		acumular_bases($HP);
		q("Update no_hpago set observaciones=\"$Observaciones_pago\" where id=$HP");
	}
#	else
#		$Observaciones_pago.= "No hay contrato activo para este empleado<br />";
	q("delete from tmpi_planilla where idemp='$Empleado' ");
}

function liquidar_planilla()
{
	global $Planilla,$Empleado;
	$PL = qo("select tp.dias,pl.* from no_planilla pl,no_tipo_planilla tp where tp.id=pl.periodo_pago and pl.id=$Planilla");
	if(isset($_COOKIE['NOL_empleado_filtro'])) $Filtro1=$_COOKIE['NOL_empleado_filtro']; else $Filtro1='';
	if(isset($_COOKIE['NOL_empleado_filtro2'])) $Filtro2=$_COOKIE['NOL_empleado_filtro2']; else $Filtro2='';
	if(isset($_COOKIE['NOL_empleado_filtro3'])) $Filtro3=$_COOKIE['NOL_empleado_filtro3']; else $Filtro3='';
	q("drop table if exists tmpi_planilla");
	$SQL="select pr.id as idemp from profesor pr, no_contrato co where pr.id=co.empleado ".
		($Filtro1?" and pr.apellidos like '$Filtro1%' ":"").
		($Filtro2?" and co.sucursal='$Filtro2' ":"").
		($Filtro3?" and co.modelo_contrato='$Filtro3' ":"").
		($Empleado?" and pr.id='$Empleado' ":"").
		" and co.id=contrato_activo(co.empleado,'$PL->fecha_final') order by pr.apellidos,pr.nombres";
	q("create table tmpi_planilla ".$SQL ,1);
	q("alter table tmpi_planilla add column id int(10) not null auto_increment primary key");
	if($Empleados=q("select * from tmpi_planilla"))
	{
		$Cantidad_seleccionados=mysql_num_rows($Empleados);
		## inicio parametrización del termo
		setcookie('Termo_limite',$Cantidad_seleccionados);
		setcookie('Termo_conteo',0);
		setcookie('Termo_tiempo',microtime(true));
		setcookie('Termo_tiempo_total',0);
		html();
		echo "<script language='javascript' src='inc/nomina/no_liquidacion.js'></script>
			<body onload=\"termo('no_liquidacion.php?Acc=liquidar');\">".
			titulo_modulo("Liquidación masiva:", 0).
			"Número de empleados seleccionados: $Cantidad_seleccionados<br />
			<input type='button' value='Cerrar esta ventana' onclick=\"parent.Iliquidaciones.location='no_liquidacion.php?Acc=liquidaciones';L_oculta();\">
			<table border cellspacing='0'></body>";
	}
	else
	{
		html();
		echo "<script language='javascript' src='inc/nomina/no_liquidacion.js'></script>
			<body>No existen empleados con los filtros actuales. <br /><br />$SQL<br /><br />
			Cambie los filtros e intentelo de nuevo.<br /><br />
		<br /><input type='button' value='Cerrar esta ventana' onclick=\"parent.Iliquidaciones.location='no_liquidacion.php?Acc=liquidaciones';L_oculta();\"></body>";
	}
}

function procesa_conceptos($CONCEPTOS, $Contrato, $PL, $HP)
{
	global $Observaciones_pago,$Empleado, $Planilla, $ACUMULADO_MENSUAL;
	while($Concepto = mysql_fetch_object($CONCEPTOS))
	{
		if($_COOKIE['NOL_mostrar_liquidaciones']=='1') $Observaciones_pago.= "<br /><B>$Concepto->nombre ($Concepto->rutina)</B>";
		if(function_exists($Concepto->rutina)) eval($Concepto->rutina . '($Contrato,$Concepto,$PL,$HP,' . $Concepto->pad1 . ');');
	}
}
# ###########################################################################################################################################################
# ###########################################################################################################################################################
# ###########################################################################################################################################################
# ###########################################################################################################################################################
# ###########################################################################################################################################################
# ###########################################################################################################################################################
# ###########################################################################################################################################################





# ##########################################################---------------------------------------- ##################################################
# ##########################################################            NOVEDADES        ##################################################
# ##########################################################            NOVEDADES        ##################################################
# ##########################################################            NOVEDADES        ##################################################
# ##########################################################            NOVEDADES        ##################################################
function novedades()
{
	global $Nt_empleados, $Nt_conceptos, $Nt_salarios, $Nt_beneficios, $Nt_prestamos, $Nt_ley100, $Nt_Contrato ,$Nt_Anexos;
	$ESTILO = '_nomina';
	html();
	echo"
	<script language='javascript' src='inc/nomina/no_liquidacion.js'></script>
	<body topmargin=0 leftmargin=0 rightmargin=0   style='background-color:#FFFFff;'";
	if($_COOKIE['NOL_contrato'] && $_COOKIE['NOL_empleado'])
	{
		$idcon = $_COOKIE['NOL_contrato'];
		$idemp = $_COOKIE['NOL_empleado'];
		$PL = qo("select * from no_planilla where id=" . $_COOKIE['NOL_planilla']);
		$Emp=qo("select concat(apellidos,' ',nombres) as nombre,foto_f as foto,identificacion from profesor where id=$idemp");
		$Sal = qo1("select salario($idcon,'$PL->fecha_final')");
		echo "onload=\"document.forma.Novedad.focus();\">";
	}
	else
	{
		echo die(">SELECCIONE UN EMPLEADO");
	}
	require('inc/link.php');
	$Cargos_emp = '';
	if(!$Cargos_empleado = mysql_query("select ca.nombre from no_cargo ca,no_emp_cargo ec
								WHERE ec.cargo=ca.id and ec.id=cargo_activo($idcon,'$PL->fecha_final')", $LINK)) die("Error ".mysql_error($LINK));
	while($Ce = mysql_fetch_object($Cargos_empleado))
	{
		$Cargos_emp .= $Ce->nombre . "<br>";
	}
	mysql_close($LINK);
	if(empty($Cargos_emp)) $Cargos_emp = '<font color=red>Cargo Sin Definir</font>';
	echo "\n<table width='100%'>
	<tr><td><a class='info'>$Emp->nombre<span>$Cargos_emp<br /><img src='$Emp->foto' height=200 border=3><br />
	Id: ".coma_format($Emp->identificacion)."</span><a> $" . coma_format($Sal) . "</td><td align='right'>
	<a " . ($Nt_empleados?"href=\"javascript:modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$Nt_empleados&id=$idemp',0,0,800,800,'empleado');\"":"") . " style='cursor:pointer;' class='info'><img src='gifs/nomina/hojavida.png' border=0><span>Hoja de vida de<br />$Emp->nombre</span></a>
	<a " . ($Nt_Contrato?"href=\"javascript:modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$Nt_Contrato&id=$idcon',0,0,800,800,'empleado');\"":"") . " style='cursor:pointer;' class='info'><img src='gifs/nomina/contrato.png' border=0 height=18><span>Contrato $idcon de<br />$Emp->nombre</span></a>
	<a " . ($Nt_ley100?"href=\"javascript:modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt_ley100&VINCULOC=empleado&VINCULOT=$idemp',0,0,400,400,'empleado');\"":"") . " style='cursor:pointer;' class='info'><img src='gifs/nomina/ley100.png' border=0><span>Ley 100 de<br />$Emp->nombre</span></a>
	<a " . ($Nt_salarios?"href=\"javascript:modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt_salarios&VINCULOC=contrato&VINCULOT=$idcon',0,0,300,500,'salarios');\"":"") . " style='cursor:pointer;' class='info'><img src='gifs/nomina/sueldo.png' border=0><span>Salarios de<br />$Emp->nombre</span></a>
	<a href=\"javascript:modal('no_liquidacion.php?Acc=traer_fijos&idcon=$idcon',50,50,500,500,'Traer');void(null);\"
		style='cursor:pointer;' class='info'><img src='gifs/nomina/importar.png' border=0><span>Traer conceptos<br />fijos de<br />otro<br />empleado</span></a>
	<a " . ($Nt_beneficios?"href=\"javascript:modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt_beneficios&VINCULOC=empleado&VINCULOT=$idemp',0,0,400,400,'beneficios');\"":"") . " style='cursor:pointer;' class='info'><img src='gifs/nomina/beneficio.png' border=0><span>Beneficios</span></a>
	<a " . ($Nt_prestamos?"href=\"javascript:modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt_prestamos&VINCULOC=contrato&VINCULOT=$idcon',0,0,400,400,'prestamos');\"":"") . " style='cursor:pointer;' class='info'><img src='gifs/nomina/prestamo.png' border=0><span>Prestamos</span></a>
	<a " . ($Nt_Anexos?"href=\"javascript:modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt_Anexos&VINCULOC=contrato&VINCULOT=$idcon',0,0,400,400,'Anexos');\"":"") . " style='cursor:pointer;' class='info'><img src='gifs/nomina/anexo.png' border=0><span>Anexos</span></a>";
	if($PL->bloqueo==0)
		echo " <a href=\"javascript:window.open('no_liquidacion.php?Acc=liquidar_planilla&Planilla=$PL->id&Empleado=$idemp','Liquidacion_nomina');void(null);\"
			style='cursor:pointer;' class='info'><img src='gifs/nomina/liquidar.png' border=0><span>Liquidar este empleado</span></a>
			<a href=\"javascript:if(confirm('Desea liquidar todos los empleados?')) {
			window.open('no_liquidacion.php?Acc=liquidar_planilla&Planilla=$PL->id','Liquidacion_nomina');void(null);}\"
			style='cursor:pointer;' class='rinfo'><img src='gifs/nomina/liquidar_todos.png' border=0><span>Liquidar todos los empleados</span></a>";
	echo "</td><td width='10%'></td></tr></table>

	<table cellspacing=0 border width='100%'><tr>
	<td>#</td>
	<th class='thn' " . ($Nt_conceptos?" style='cursor:pointer;' onclick=\"modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt_conceptos',0,0,400,400,'con')\"":"") . ">Concepto</th>
	<th class='thn'>Cant</th>
	<th class='thn'>Valor</th>
	<td align='center'>";
	if($PL->bloqueo==0)
		echo "<img src='gifs/canasta.gif' border=0 style='cursor:pointer;' onclick=\"if(confirm('Desea eliminar las novedades de este periodo?'))
				modal('no_liquidacion.php?Acc=borra_novedades&idcon=$idcon&Pl=$PL->id',0,0,10,10,'ss');\">";
	echo "</td></tr>";
	$Contador = 1;
	if($Novedades = q("select con.nombre,con.sigla,con.tipo,nov.*,tc.color_co,con.captura_cantidad,con.captura_valor
						from no_novedad nov,no_concepto con,no_tipo_concepto tc
						where nov.contrato=$idcon and nov.planilla=" . $_COOKIE['NOL_planilla'] . "
						and nov.concepto=con.id and con.tipo=tc.id
						order by con.tipo,con.nombre"))
	{
		while($N = mysql_fetch_object($Novedades))
		{
			echo "<tr class='trn' ";
			if($PL->bloqueo==0) echo "ondblclick=\"var DD=document.forma;
											DD.id.value='$N->id';DD.Novedad.value='$N->concepto';
											DD.Novedad.style.backgroundColor='$N->color_co';
											DD.Cantidad.value='$N->cantidad';DD.Valor.value='$N->valor';
											if($N->captura_cantidad==1) DD.Cantidad.disabled=false; else DD.Cantidad.disabled=true;
											if($N->captura_valor==1) DD.Valor.disabled=false; else DD.Valor.disabled=true;
											DD.Novedad.focus();\" ";
			echo ">
			<td>$Contador</td>
			<td>$N->sigla</td>
			<td align='center'>$N->cantidad</td>
			<td align='right'>" . coma_format($N->valor) . "</td>
			<td align='center'>";
			if($PL->bloqueo==0)
				echo "<a class='rinfo'><img src='gifs/canasta.gif' border=0 style='cursor:pointer;' onclick=\"if(confirm('Desea eliminar esta novedad?'))
					modal('no_liquidacion.php?Acc=borra_novedad&id=$N->id',0,0,10,10,'ss');\"><span>Borrar esta novedad</span></a>
					<a class='rinfo'><img src='gifs/nomina/exportar.png' border=0 style='cursor:pointer;' onclick=\"if(confirm('Desea exportar esta novedad a los demas empleados?'))
					modal('no_liquidacion.php?Acc=exporta_novedad&id=$N->id',0,0,10,10,'ss');\"><span>Exportar este concepto a otros empleados</span></a>";

			echo "</td></tr>";
			$Contador++;
		}
	}

	if($PL->bloqueo==0)
	{
		echo "<form action='no_liquidacion.php' method='post' target='Liquidacion_nomina' name='forma' id='forma'>
		<tr><td colspan=2>";
		if($Conceptos = q("select con.*,tc.color_co from no_concepto con,no_tipo_concepto tc where con.tipo=tc.id order by con.tipo,con.nombre"))
		{
			echo "<select name='Novedad' style='color:#000000;background-color:#ffffff;width:250px;'><option></option>";
			while($C = mysql_fetch_object($Conceptos))
			{
				echo "<option value='$C->id' " . ($C->tipo == 1) . " style='background-color:$C->color_co;color:#000000;width:300px;'
				onclick=\"var DD=document.forma; DD.Novedad.style.backgroundColor='$C->color_co';
				if($C->captura_cantidad==1) {	DD.Cantidad.disabled=false;DD.Cantidad.focus();}
				else { DD.Cantidad.disabled=true; }
				if($C->captura_valor==1) { DD.Valor.disabled=false; if(DD.Cantidad.disabled) { DD.Valor.focus(); } }
				else { DD.Valor.disabled=true; if(DD.Cantidad.disabled) { DD.Grabar.focus(); } } " .
				($C->aviso_captura?"alert('$C->aviso_captura');":"") . "\">$C->nombre</option>";
			}
			echo "</select>";
		}

		ECHO "</td>
		<td align='center'><input type='text' id='Cantidad1' name='Cantidad' size=5 maxlength=10 value='' disabled
				onkeydown=\"verificanumero(event,'Cantidad1');\" onfocus=\"this.select();\"></td>
		<td align='center'><input type='text' id='Valor1' name='Valor' size=10 value='' maxlength=15 disabled
				onkeydown=\"verificanumero(event,'Valor1');\" onfocus=\"this.select();\"></td>
		<td><input type='button' value='OK' name='Grabar' style='width:20px;'
		onclick=\"valida_campos('forma','Novedad');\"></td></tr>
		<input type='hidden' name='Acc' value='adiciona_novedad'>
		<input type='hidden' name='idcon' value='$idcon'>
		<input type='hidden' name='idpla' value='$PL->id'>
		<input type='hidden' name='id' value=''>
		</form>";
	}
	echo "</table>";
	if(!isset($_COOKIE['NOL_ocultar_conceptos_fijos']))
	{
		echo "<br />
		<hr color='#000099'>";
		###################################################   F I J O S  ///////////////////////////////////////////////////////////////////////////////

		echo "<center><span class='thn'>Conceptos Fijos</span>&nbsp;&nbsp;
		 </center>
		<table cellspacing=0 border width='100%' style='font-size:7px'><tr>
		<th class='thn'>Ord</th><th class='thn'>Concepto</th><th class='thn'>Cant</th><th class='thn'>Valor</th><th class='thn'>Desde</th><th class='thn'>Hasta</th><th class='thn'>#Q</th>
		<td align='center'><img src='gifs/canasta.gif' border=0 style='cursor:pointer;' onclick=\"if(confirm('Desea eliminar todas las novedades fijas?'))
		window.open('no_liquidacion.php?Acc=borra_novedades_fijas&idcon=$idcon','Liquidacion_nomina');\"></td></tr>";
		if($Fijos = q("select cf.orden,con.nombre,con.sigla,con.tipo,cf.*,tc.color_co,con.aviso_captura
							from no_concepto_fijo cf,no_concepto con,no_tipo_concepto tc
							where cf.contrato=$idcon and cf.concepto=con.id and con.tipo=tc.id and
							((cf.fecha_inicial='0000-00-00' and cf.fecha_final='0000-00-00') or (if(cf.fecha_inicial!='0000-00-00',cf.fecha_inicial<='$PL->fecha_final',1)
							and if(cf.fecha_final!='0000-00-00',cf.fecha_final>='$PL->fecha_inicial',1))) order by tc.momento,cf.orden,con.tipo,con.nombre"))
		{
			$maxorden = -1;
			while($F = mysql_fetch_object($Fijos))
			{
				echo "
				<tr class='trn' ondblclick=\"var DF=document.formaf;
				DF.id.value='$F->id';DF.orden.value='$F->orden';DF.Concepto.value='$F->concepto';DF.Concepto.style.backgroundColor='$F->color_co';
				DF.Cantidad.value='$F->cantidad';DF.Valor.value='$F->valor';DF.Fecha_inicial.value='$F->fecha_inicial';DF.Fecha_final.value='$F->fecha_final';
				DF.Nquincena.value='$F->nquincena';DF.orden.focus();\">
				<td align='center'>$F->orden</td><td bgcolor='$F->color_co'>".($F->aviso_captura?"<a class='info'>":"")."$F->sigla".
				($F->aviso_captura?"<span>$F->aviso_captura</span></a>":"")."</td><td align='center'>$F->cantidad</td>
				<td align='right'>".coma_format($F->valor)."</td><td align='center'>$F->fecha_inicial</td>
				<td align='center'>$F->fecha_final</td><td align='center'>$F->nquincena</td>
				<td align='center'>
					<a class='rinfo'><img src='gifs/canasta.gif' border=0 style='cursor:pointer;'
					onclick=\"if(confirm('Desea eliminar este concepto fijo?'))
					window.open('no_liquidacion.php?Acc=borra_novedad_fija&id=$F->id','Liquidacion_nomina');\"><span>Borrar el concepto</span></a>
					<a class='rinfo'><img src='gifs/nomina/exportar.png' border=0 style='cursor:pointer;'
					onclick=\"if(confirm('Desea exportar este concepto fijo a los demas empleados?'))
					modal('no_liquidacion.php?Acc=exporta_fijo&id=$F->id',0,0,10,10,'ss');\"><span>Exportar este concepto a otros empleados</span></a>
				</td></tr>";
				$maxorden = $maxorden < $F->orden?$F->orden:$maxorden;
			}
		}
		$maxorden++;

		echo "<form action='no_liquidacion.php' method='post' target='Liquidacion_nomina' name='formaf' id='formaf'>
		<tr class='trn' ><td valign='top' colspan=2><input type='text' name='orden' size=2 value='$maxorden'>";
		if($Conceptos)
		{
			mysql_data_seek($Conceptos, 0);
			echo "<select name='Concepto' onchange='document.formaf.Cantidad.focus()' style='color:#000000;background-color:#ffffff;width:130px;'><option></option>";
			while($C = mysql_fetch_object($Conceptos))
			{
				echo "<option value='$C->id' " . ($C->tipo == 1) . " style='background-color:$C->color_co;color:#000000;width:300px;'
				onclick=\"document.formaf.Concepto.style.backgroundColor='$C->color_co';" .
				($C->aviso_captura?"alert('$C->aviso_captura');":"") . "\">$C->nombre</option>";
			}
			echo "</select>";
		}
		ECHO "</td>
		<td align='center' valign='top'><input type='text' id='Cantidad2' name='Cantidad' size=5 maxlength=10 value=''
					onkeydown=\"verificanumero(event,'Cantidad2');\" onfocus=\"this.select();\"></td>
		<td align='center' valign='top'><input type='text' id='Valor2' name='Valor' size=10 maxlength=15 value=''
					onkeydown=\"verificanumero(event,'Valor2');\" onfocus=\"this.select();\"></td>
		<td>".pinta_FC('formaf','Fecha_inicial')."</td><td>".pinta_FC('formaf','Fecha_final')."</td><td>
		<select name='Nquincena' style='width:30px'>
		<option value='0' style='width:200px'>0 = todas</option>
		<option value='1' style='width:200px'>1 = Quincena 1</option>
		<option value='2' style='width:200px'>2 = Quindena 2 / Mensual</option>
		<option value='3' style='width:200px'>3 = Otras planillas</option>
		</select></td>
		<td><input type='button' value='OK' style='width:20px;'
		onclick=\"valida_campos('formaf','Concepto');\"></td></tr>
		<input type='hidden' name='Acc' value='adiciona_novedad_fija'>
		<input type='hidden' name='idcon' value='$idcon'>
		<input type='hidden' name='id' value=''>
		</form></table>";
	}
	echo "</body>";
}




?>
