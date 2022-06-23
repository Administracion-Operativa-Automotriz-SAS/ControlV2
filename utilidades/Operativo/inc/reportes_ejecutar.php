<?php
function ejecutar()
{
	global $Plano_csv;
	require('inc/gpos.php');
	###############################  SOLICITUDES DE VARIABLES DE ACUERDO A LA FORMULA INICIAL ###################
	$Reporte=qo("select * from aqr_reporte where id=$ID");
	if($Reporte->pre && !$_Previo_Ok)
	{
		html();
		$FORMA='forma';
		echo "
		<script language='javascript'>
				function cambia_target()
				{
					if(document.forma.Cambiatarget.checked)
					{
						document.forma.target='_blank';
					}
					else
					{
						document.forma.target='_self';
					}
				}
		</script>
		<body><H3 ALIGN='center'>$Reporte->nombre</H3>
			<center>
			<FORM ACTION='reportes.php' TARGET='_self' METHOD='post' name='forma' id='forma'>
			<table><tr><td><h2>$Reporte->nombre</h2>$Reporte->explicacion</td></tr><tr><td>
				<INPUT TYPE='hidden' NAME='Acc' VALUE='ejecutar'>
				<INPUT TYPE='hidden' NAME='ID' VALUE='$ID'>
				<INPUT TYPE='hidden' NAME='_Previo_Ok' VALUE='1'>
				<INPUT TYPE='hidden' NAME='Impr' VALUE='$Impr'>
				<input type='hidden' name='Muestra' value='$Muestra'>";

		eval($Reporte->pre);
		echo "</td></tr><tr><td nowrap='yes'>
			Producir el resultado en otra ventana: <input type='checkbox' name='Cambiatarget' onclick='cambia_target();'>
			<input type='submit' value='Procesar' name='Procesar' id='Procesar' onclick='this.form.submit();' $Procesar_style>
			</FORM></center></body>";
		die();
	}
	######################################## FIN SOLICITUD DE VARIABLES ####################################
	$_Where=ejec_relaciones($ID);		#PRIMERO CONFIGURA LAS RELACIONES BASICAS
	$_Condiciones_campos=ejec_condiciones_campos($ID);	# CONFIGURA LOS CONDICIONANTES QUE HAY CAMPO POR CAMPO
	$_Where.=(strlen($_Condiciones_campos)? (strlen($_Where)?" and " : " Where ") : "").$_Condiciones_campos;
	$_Where.=(strlen($Reporte->donde)       ? (strlen($_Where)?" and " : " Where ") : "").$Reporte->donde;  # ADICIONA LA FORMULA DE CONDICION MANUAL
	$_Where=Verifica_variables($_Where);		# REEMPLAZA TODAS LAS VARIABLES DENTRO DE LAS CONDICIONES
	$_Agrupaciones=ejec_agrupamiento($ID);
	$_Tabla_temporal='tmpi_'.$Id_alterno.'_'.$ID;		# SE CREA UNA TABLA TEMPORAL PARA CADA USUARIO
	q("delete from aqr_reporte_filtro where idreporte='$ID'");
	q("drop table if exists $_Tabla_temporal");
	if(!$Reporte->construye) $Comando_Sql=$Reporte->instruccion;
	else
		$Comando_Sql="Create Table $_Tabla_temporal Select ".($Reporte->distintos?"DISTINCT ":" ").ejec_campos($ID,strlen($_Agrupaciones)).
		" From ".ejec_tablas($ID).$_Where.$_Agrupaciones.ejec_orden($ID);		# TERMINA DE CONSTRUIR TODA LA INSTRUCCION SQL
	$Comando_Sql=Verifica_variables($Comando_Sql);
	if($Reporte->construye) q("update aqr_reporte set instruccion=\"$Comando_Sql\" where id=$ID",1);	#GRABA LA INSTRUCCION EN EL MODULO AVANZADOS
	if(q($Comando_Sql.($Muestra?" Limit $Muestra":""),1))		#EJECUTA LA INSTRUCCION SQL
	{
		$Parametros='';
		foreach($_POST as $Clave => $Valor)
		{
			if($Clave!='Acc') $Parametros.='&'.$Clave.'='.$Valor;
		}
		foreach($_GET as $Clave => $Valor)
		{
			if($Clave!='Acc') $Parametros.='&'.$Clave.'='.$Valor;
		}
		$LOCATION_exel='reportes.php?_Exel=1&Acc=ejec_pinta&ID='.$ID.$Parametros;
		$LOCATION='reportes.php?Acc=ejec_pinta&ID='.$ID.$Parametros;
		header("location:$LOCATION&_EX=".base64_encode($LOCATION_exel));		# INVOCA LA PRESENTACION DEL INFORME
	}
	else
	{
		echo "No se pudieron obtener datos";
	}
}

function ejec_pinta()
{
	require('inc/gpos.php');
	global $Inicia_Rompimientos,$Plano_csv,$_EX;
	include('inc/reportes_ejecutar_class.php');
	$Reporte=qo("select * from aqr_reporte where id=$ID");
	$Filtros_adicionales=ejec_filtros_adicionales($ID);		# BUSCA SI HAY FILTROS ADICIONALES O REORDEN
	$Filtros_adicionales=Verifica_variables($Filtros_adicionales);		# REEMPLAZA LAS VARIABLES EN LOS FILTROS ADICIONALES
	$_Tabla_temporal='tmpi_'.$Id_alterno.'_'.$ID;							# CONFIGURA EL NOMBRE DE LA TABLA TEMPORAL.
	$Gseries='';$Glabels='';$Gcookiesd='';$Gcookiesl='';
	$Campos_totales='';$Formulas_totales='';
	if($Reporte->gen_csv)
	{
		if($Reporte->csv_nombre)
			$Plano_csv=$Reporte->csv_nombre;
		else
			$Plano_csv='csv_'.$Id_alterno.'_'.$ID.'.csv';
	}
	else $Plano_csv='';
	#-------------------------------------------------------------------------------------------------------------------------------------------------
	ejec_configura_grafico($Gseries,$Glabels,&$Gcookiesd,&$Gcookiesl,$Reporte);	#INVOCA LA CONFIGURACION DE LAS VARIABLES DE SESION PARA GRAFICOS
	#-------------------------------------------------------------------------------------------------------------------------------------------------
	$i=1;
	$Grafica_por_rompimientos=false;
	if($Ordenes=q("select * from aqr_reporte_order where idreporte=$ID and total=1 order by orden"))
	{
		while($O=mysql_fetch_object($Ordenes))
		{
			$ROMPIMIENTO[$i]=new rompimiento($O,&$Gcookiesd,&$Gcookiesl);		# CREACION DE TODOS LOS ROMPIMIENTOS USANDO LA CLASE ROMPIMIENTO
			if($ROMPIMIENTO[$i]->Label_grafica)
				$Grafica_por_rompimientos=true;
			$i++;
		}
	}

	setcookie('GCOOKIESD',$Gcookiesd);
	setcookie('GCOOKIESL',$Gcookiesl);

	if($_Exel)
	{
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$Reporte->salida_exel_nombre");
	}
	else
	{
		html();
	}
	echo "<body onload='centrar();'>";
	if($Reporte->titulo_rt)
		echo $Reporte->titulo_rt;	# IMPRIME EL TEXTO ENRIQUECIDO DEL TITULO
	if($Reporte->titulo)
		eval($Reporte->titulo);		# EJECUTA LAS INSTRUCCIONES DEL TITULO
	$Propiedades_TR=$Reporte->propiedades_tr;
	echo "\n<Table Border=$Reporte->borde $Reporte->ancho >";
	## A CONTINUACION SE PINTAN LOS TITULOS Y SE CREA TODA LA INSTRUCCION CORRESPONDIENTE A LOS CAMPOS DEL DETALLE
	$Campos_Detalle=ejec_pinta_titulos($ID,$Reporte->tit_add,$Campos_totales,$Formulas_totales,$Reporte->incluir_id,$_Tabla_temporal,$Filtros_adicionales);
	# -------------------------------------------------------------------------------------------------------------------------------------------------------
	if($Reporte->gen_csv)
	{
		$Fcsv = fopen ("planos/$Plano_csv","w");
		if($Reporte->csv_contitulos)
		{
			$Tit_csv=qo2("select cabecera from aqr_reporte_field where idreporte=$ID and ver=1 order by orden",$Reporte->separador_csv);
			fwrite($Fcsv,$Tit_csv."\r\n");
		}
	}

	if($INFORME=q("Select * from $_Tabla_temporal $Filtros_adicionales"))
	{
		$Inicia_Rompimientos=true;
		$Contador=1;
		while($I=mysql_fetch_object($INFORME))
		{
			if($Reporte->gen_csv)
			{
				$Cantidad_campos=mysql_num_fields($INFORME);
				for($csvi = 0;$csvi < $Cantidad_campos;$csvi++)
				{
					$Instruccion="fwrite(\$Fcsv,\$I->".mysql_field_name($INFORME, $csvi).(($csvi+1)<$Cantidad_campos?".'$Reporte->separador_csv'":"").");";
					eval($Instruccion);
				}
				fwrite($Fcsv,"\r\n");
			}
			if($Inicia_Rompimientos)
			{
				inicializa_rompimientos($ROMPIMIENTO,$I);		## INICIALIZACION DE TODOS LOS ROMPIMIENTOS
				$Inicia_Rompimientos=false;
			}
			else
			{
				valida_rompimiento($I,$ROMPIMIENTO,$Contador,$I_ANT);	#VALIDACION DEL REGISTRO PARA VER SI HAY CAMBIO DE ROMPIMIENTO O INCREMENTO
			}
			$I_ANT=$I;
			echo "\n<tr ";
			eval($Propiedades_TR);
			echo ">";
			eval("echo $Campos_Detalle;");		# IMPRESION DEL DETALLE
			if($Reporte->fdetalle)
				eval($Reporte->fdetalle);		# FORMULA DE DETALLE ADICIONAL DESPUES DE LA IMPRESION
			echo "</tr>";
			IF($Reporte->grafica && !$Grafica_por_rompimientos)  ## Se ejecutan las instrucciones de series y de labels siempre y cuando solo sea por detalle y no por rompimientos
			{
				if($Gseries) eval($Gseries);  # EJECUTA LA FORMULA QUE GUARDA LAS SERIES PARA GRAFICOS
				if($Glabels) eval($Glabels);	# EJECUTA LA FORMULA QUE GUARDA LAS ETIQUETAS PARA GRAFICOS
			}
			if($Formulas_totales) eval($Formulas_totales);	#EJECUTA LA FORMULA QUE ACUMULA LOS TOTALIZADORES AL FINAL DEL INFORME
			## CONTADOR DE REGISTROS IMPRESOS
			$Contador++;

		}
		valida_rompimiento(null,$ROMPIMIENTO,$Contador,$I_ANT);		# VALIDA EL ULTIMO REGISTRO PARA IMPRIMIR LOS ULTIMOS ROMPIMIENTOS
		if($Formulas_totales)
		{
			eval("echo \"<tr bgcolor='#bbbbbb'>$Campos_totales</tr>\";");	#IMPRESION DE LOS TOTALIZADORES GENERALES AL FINAL DEL INFORME
		}
	}
	if($Reporte->gen_csv)
	{
		fclose($Fcsv);
	}
	echo "</Table>";
	if($Reporte->resumen)
		eval($Reporte->resumen);		# EJECUTA LA FORMULA POSTERIOR AL INFORME
	if($Reporte->resumen_rt)
		echo $Reporte->resumen_rt;	#IMPRIME EL TEXTO ENRIQUECIDO AL FINAL DEL INFORME
	if($Reporte->grafica)
		reporte_pinta_grafica($ID);	# SI LA GRAFICA ESTA ACTIVADA, SE INVOCA EL PROGRAMA QUE GRAFICA reportes_grafica.php
	if($Reporte->gen_csv)
		echo "<img src='gifs/csv.gif' border=0 style='cursor:pointer;' onclick=\"this.src=''; this.style.height=1;this.style.width=1;
				window.open('marcoindex.php?Acc=bajar_archivo&Archivo=planos/$Plano_csv&Salida=$Plano_csv','Reporte_oculto');\"> ";
	if($Reporte->salida_exel)
	{
		echo "<br />
		<a onclick=\"window.open('".base64_decode($_EX)."','Reporte_Oculto');\" style='cursor:pointer;'>Descargar en Excel</a>";
	}
	echo "<iframe name='Reporte_Oculto' style='visibility:hidden;' width=1 height=1 frameborder='no'></iframe></body>";
}


function Verifica_variables($Dato)		# REEMPLAZO DE VALORES DE VARIABLES  DENTRO DE LAS FORMULAS
{
	require('inc/gpos.php');
	$Metodo=$_SERVER['REQUEST_METHOD'];
	foreach($_REQUEST as $Clave => $Valor)
	{
		$Dato=str_replace("\$".$Clave,$Valor,$Dato);
	}
	foreach($_SESSION as $Clave => $Valor)
	{
		$Dato=str_replace("\$".$Clave,$Valor,$Dato);
	}
	return $Dato;
}

function valida_rompimiento($I,&$R,$Contador,$IA)
{
	for($i=count($R);$i>0;$i--)		# BUSCA DESDE EL ROMPIMIENTO MAS INTERNO HASTA EL MAS EXTERNO
	{
		$Obliga_por_externos=false;
		for($j=1;$j<$i;$j++)
		{
			eval('$Valor_recibido=$I->'.$R[$j]->Nombre_rompimiento.';');
			if($Valor_recibido != $R[$j]->Valor_control) $Obliga_por_externos=true;		# SI ENCUENTRA ALGUN CAMBIO EN LOS CONTROLES DE ROMPIMIENTOS EXTERNOS,
		}																										# SE DEBE EJECUTAR EL CAMBIO DE ROMPIMIENTO INTERNO
		eval('$Valor_recibido=$I->'.$R[$i]->Nombre_rompimiento.';');
		if($Valor_recibido != $R[$i]->Valor_control or $Obliga_por_externos)				#VALIDACION DEL CAMBIO DE CONTROL INTERNO
		{
			if($R[$i]->Acumuladores)
			{
				eval("echo \"<tr>".$R[$i]->Pinta_acumuladores."</tr>\";");					# SI EL ROMPIMIENTO TIENE ACUMULADORES, LOS IMPRIME
				eval($R[$i]->Serie_grafica);
			}
			$R[$i]->Valor_control=$Valor_recibido;													# REASIGNA EL NUEVO CONTROL DE ROMPIMIENTO
			for($in=count($R);$in>=$i;$in--)									# DESDE EL ROMPIMIENTO MAS INTERNO HASTA EL ACTUAL, O SEA, EL QUE ACTIVO EL CAMBIO
			{
				eval('$Valor_control=$I->'.$R[$in]->Nombre_rompimiento.';');		# REASIGNA EL NUEVO CONTROL DE ROMPIMIENTO
				$R[$in]->Valor_control=$Valor_control;
				$R[$in]->inicializa_acumuladores($I,$IA);										# INICIALIZA LOS ACUMULADORES
			}
		}
		else
		{
			$R[$i]->incrementa_acumuladores($I);		# INCREMENTA LOS ACUMULADORES CUANDO NO HAY CAMBIO DE ROMPIMIENTO
		}
	}
}

function inicializa_rompimientos(&$R,$I)				# INICIALIZACION DE TODOS LOS ROMPIMIENTOS
{
	for($i=1;$i<=count($R);$i++)
	{
		eval('$R[$i]->Valor_control=$I->'.$R[$i]->Nombre_rompimiento.';');  #ASIGNACION DEL PRIMER DATO AL VALOR DE CONTROL EN CADA ROMPIMIENTO
		$R[$i]->inicializa_acumuladores($I);		# INICIALIZA LOS ACUMULADORES DE CADA ROMPIMIENTO
	}
}

function ejec_configura_grafico(&$Gseries,&$Glabels,&$Gcookiesd,&$Gcookiesl,$Reporte)
{
	# SE CREAN DOS VARIABLES TIPO FORMULA, GSERIES Y GLABELS QUE SE EJECUTAN DESPUES DE LA IMPRESION DE CADA REGISTRO.
	# ESAS DOS VARIABLES CREAN VARIABLES DE SESION CON LOS DATOS Y LAS ETIQUETAS, QUE LUEGO SON USADAS EN LA IMPRESION DE LA GRAFICA
	if($Reporte->grafica)
	{
		$Glabels='';
		if($Reporte->incluir_id && $Reporte->etiqueta_id)
		{
			$Glabels.="\$_SESSION['SERIE_l_Contador_".$Reporte->id."'][]=\$Contador;";
			$Gcookiesl.="Contador_".$Reporte->id.",";
			eval("\$_SESSION['SERIE_l_Contador_".$Reporte->id."']=array();");
		}
		if($Campos=q("select apodo from aqr_reporte_field where idreporte=$Reporte->id and lgrafica=1 order by orden"))  # BUSQUEDA DE LAS ETIQUETAS
		{
			while($C=mysql_fetch_object($Campos))
			{
				$Glabels.="\$_SESSION['SERIE_l_".$Reporte->id."_".$C->apodo."'][]=\$I->".$C->apodo.";";
				$Gcookiesl.=$Reporte->id."_$C->apodo,";
				eval("\$_SESSION['SERIE_l_".$Reporte->id."_".$C->apodo."']=array();");
			}
		}
		if($Glabels)  ## si hay labels marcados dentro del detalle del informe, las series se extraen del detalle de lo contrario es posible que sean de algun rompimiento
		{
			$Gseries='';
			if($Campos=q("select apodo from aqr_reporte_field where idreporte=$Reporte->id and grafica=1 order by orden"))  # BUSQUEDA DE LAS SERIES
			{
				while($C=mysql_fetch_object($Campos))
				{
					$Gseries.="\$_SESSION['SERIE_d_".$Reporte->id."_".$C->apodo."'][]=\$I->".$C->apodo.";";
					$Gcookiesd.=$Reporte->id."_$C->apodo,";
					eval("\$_SESSION['SERIE_d_".$Reporte->id."_".$C->apodo."']=array();");
				}
			}
		}
		else  ## si no hay labels pero si hay series, es posible que sean de rompimientos
		{
			$Gseries='';
		}
	}
}


function ejec_pinta_titulos($ID,$Titulo_adicional,&$Campos_totales,&$Formulas_totales,$Incluir_id,$TT,$FA)
{
	$Campos_totales='';		# CREA LAS INSTRUCCIONES QUE IMPRIMEN LOS TOTALIZADORES GENERALES
	$Colspan_totales=0;		# CUENTA LAS CELDAS ANTES DEL PRIMER TOTALIZADOR
	if($Incluir_id)
	{
		$Campos_totales="<td>&nbsp;</td>";
		#$Colspan_totales=1;
	}
	$Para_colspan=false;	# PARA EL CONTEO DE CELDAS TAN PRONTO ENCUENTRA EL PRIMER TOTALIZADOR
	$Primera_celda=true;
	$Campos_Detalle='';		# CREA LAS INSTRUCCIONES QUE IMPRIMEN EL DETALLE DEL REGISTRO
	if($Titulos=q("select * from aqr_reporte_field where idreporte=$ID and ver=1 order by orden"))
	{
		echo "<tr>";
		if($Incluir_id)
		{
			echo "<th> # </th>";#$Colspan_totales=1;
		}
		while($T=mysql_fetch_object($Titulos))
		{
			echo "<th onclick=\"modal('reportes.php?Acc=ejec_filtro&idreporte=$ID&apodo=$T->apodo&cabecera=$T->cabecera',20,20,300,500,'Dialogo');\">";
			if(q("select id from aqr_reporte_filtro where idreporte=$ID and apodo='$T->apodo' and ((length(condicion)>0 and length(valorcondicion)>0) or length(orden)>0)"))
				echo "<u>$T->cabecera</u>"; # CABECERA CON FILTRO O REORDEN
			else
				echo $T->cabecera;	# CABECERA SIN FILTRO NI REORDEN
			echo "</th>";
			$Campos_Detalle.="<td align='".ejec_alinea($T->alinea)."' ";
			if($T->hipervinculo)
				$Campos_Detalle.=" style='cursor:pointer;' onclick=\\\"V_ancho=screen.availWidth-60;V_alto=screen.availHeight-80; modal('$T->hipervinculo',0,0,V_alto,V_ancho,'_blank');\\\" ";
			if($T->script)
				$Campos_Detalle.=$T->script;
			if($T->propiedades_td)
				$Campos_Detalle.=" \".".$T->propiedades_td.".\" ";
			$Campos_Detalle.=">\".";
			if($T->coma)
				$Campos_Detalle.="coma_formatd(\$I->$T->apodo,$T->comad)";	# FORMATO NUMERICO SEPARADO POR COMAS
			elseif($T->caja)
				$Campos_Detalle.="gsino(\$I->$T->apodo)";		# FORMATO CHECKBOX
			elseif($T->imagen)
				$Campos_Detalle.="gfoto(\$I->$T->apodo)";		# FORMATO IMAGEN
			else
				$Campos_Detalle.="\$I->$T->apodo";		# FORMATO ESTANDAR
			$Campos_Detalle.=".\"</td>";
			if(!$Para_colspan)
				$Colspan_totales++;
			if($Primera_celda)
				$Campos_totales.="<td align='center' colspan='0'><b>TOTAL GENERAL</b> "; #CONSTRUCCION DE LA PRIMERA CELDA DEL TOTALIZADOR GENERAL
			if($T->operaciont)
			{
				if(!$Primera_celda)
					$Campos_totales.="<td align='".ejec_alinea($T->alinea)."'>";
				$Para_colspan=true;
				$Campos_totales.="<b>\".coma_formatd(\$TOTAL['$T->apodo'],$T->comad).\"</b>"; # INSTRUCCION PARA LA IMPRESION DEL TOTALIZADOR GENERAL
				switch($T->operaciont)
				{
					case 'SUM':$Formulas_totales.="\$TOTAL['$T->apodo']+=\$I->$T->apodo;";break;		# FORMULA QUE ACUMULA
					case 'COUNT':$Formulas_totales.="\$TOTAL['$T->apodo']+=1;";break;						# FORMULA QUE CUENTA
					case 'AVG': $Formulas_totales.="\$TOTAL['S_$T->apodo']+=\$I->$T->apodo;\$TOTAL['C_$T->apodo']+=1;\$TOTAL['$T->apodo']=\$TOTAL['S_$T->apodo']/\$TOTAL['C_$T->apodo']; ";break;  # FORMULA PROMEDIO
				}
				if($Primera_celda)
					$Campos_totales.="</td>";
			}
			if($Para_colspan)
				$Campos_totales.="</td>";
			$Primera_celda=false;
		}
		if($Colspan_totales>1)
			$Colspan_totales--;
		$Campos_totales=str_replace("colspan='0'","colspan='$Colspan_totales'",$Campos_totales);			# AJUSTA EL COLSPAN DE LA PRIMERA CELDA DE TOTALIZACION GENERAL
		echo "$Titulo_adicional</tr>";		# PINTA LOS TITULOS ADICIONALES DE LA SECCION BASICOS
		echo "<tr>";
		if($Incluir_id)
		{
			echo "<td>&nbsp;</td>";#$Colspan_totales=1;
		}
		mysql_data_seek($Titulos,0 );
		if($hay=strpos($FA,'Group by')) $FA=substr($FA,0,$hay);
		if($hay=strpos($FA,'Order by')) $FA=substr($FA,0,$hay);
		while($T=mysql_fetch_object($Titulos))
		{
			if($hay=strpos($FA,' '.$T->apodo.' = '))
			{
				$CI=substr($FA,$hay+strlen(' '.$T->apodo.' = ')+1);
				if(strpos($CI,"' and ")) $CI=substr($CI,0,strpos($CI,"' and ")); else $CI=substr($CI,0,strpos($CI,"'"));
			}
			else $CI='';
	#		echo $CI;
			if($Drop=q("select distinct $T->apodo as dato from $TT $FA order by $T->apodo"))
			{
				echo "<td>";
				if($T->filtro_rapido==1)
				{
					echo "<select onchange=\"modal('reportes.php?Acc=set_filtro_rapido&valorcondicion='+this.options[this.selectedIndex].value+'&idreporte=$ID&apodo=$T->apodo',0,0,100,200,'_bottom');\"><option value=''>(Todos)</option>";
					while($D=mysql_fetch_object($Drop))
					{
						echo "<option value='$D->dato' ";
						if($CI)
						{
							if($CI==$D->dato) echo "selected ";
						}
						ECHO ">$D->dato</option>";
					}
					echo "</select>";
				} else echo "&nbsp;";
				echo "</td>";
			}
		}
		echo "</tr>";
		#echo "<tr><td colspan=30>$FA</td></tr>";
	}
	if($Incluir_id) $Campos_Detalle="<td align='center'>\".\$Contador.\"</td>".$Campos_Detalle;
	return '"'.$Campos_Detalle.'"';
}

function set_filtro_rapido()
{
	global $id,$valorcondicion,$idreporte,$apodo;
	echo "<title>Cargando Información...</title><body onload=\"opener.location.reload();window.close();void(null);\"></body>";
	if(!$id=qo1("select id from aqr_reporte_filtro where idreporte=$idreporte and apodo='$apodo'"))
		$id=q("insert into aqr_reporte_filtro (idreporte,apodo) values ($idreporte,'$apodo')");
	q("update aqr_reporte_filtro set condicion='=', valorcondicion='$valorcondicion' where id=$id");

}

function ejec_alinea($D)			# ALINEACION DE CADA CELDA
{
	switch($D)
	{
		case 'I': return 'Left';
		case 'D': return 'Right';
		case 'J': return 'Justify';
		case 'C': return 'Center';
	}
}

function ejec_orden($ID)		#CONFIGURACION DEL ORDEN DEL INFORME
{
	$Orden='';
	if($Ordenes=q("select * from aqr_reporte_order where idreporte=$ID order by orden"))
	{
		while($O=mysql_fetch_object($Ordenes))
		{
			$Orden.=(strlen($Orden)?",":" Order by ")."$O->nombre $O->tipo";
		}
	}
	return $Orden;
}

function ejec_agrupamiento($ID)		#CONFIGURACION DEL AGRUPAMIENTO DEL INFORME
{
	$Grupo='';
	if($Ordenes=q("select * from aqr_reporte_order where idreporte=$ID and agrupado=1 order by orden"))
	{
		while($O=mysql_fetch_object($Ordenes))
		{
			$Grupo.=(strlen($Grupo)?",":" Group by ")."$O->nombre ";
		}
	}
	return $Grupo;
}

function ejec_condiciones_campos($ID)	# CONFIGURACION DE LAS CONDICIONES BASICAS POR CAMPO
{
	$Condiciones='';
	if ($Campos=q("select * from aqr_reporte_field where char_length(condicion)>0 and char_length(valorcondicion)>0 and idreporte=$ID order by orden"))
	{
		while($C=mysql_fetch_object($Campos))
		{
			if($C->condicion!=' Between ' && $C->condicion!=' In ')
				$Comilla=(strpos('  '.$C->valorcondicion,"'")>0)?"":"'";
			else
				$Comilla='';
			$Condiciones.=(strlen($Condiciones)?" and ":"")."$C->nombre $C->condicion $Comilla$C->valorcondicion$Comilla";
		}
	}
	return $Condiciones;
}

function ejec_relaciones($ID)	# CONFIGURACION DE LAS RELACIONES DEL INFORME
{
	$Relaciones_sql='';
	if($Relaciones=q("select * from aqr_reporte_relacion where idreporte=$ID"))
	{
		while($Rel=mysql_fetch_object($Relaciones))
		{
			$Relaciones_sql.=(strlen($Relaciones_sql)?" and ":"")."$Rel->alias1 = $Rel->alias2";
		}
	}
	return strlen($Relaciones_sql)?" Where ".$Relaciones_sql:"";
}

function ejec_tablas($ID)	# CONFIGURACION DE LAS TABLAS DEL INFORME
{
	$From_sql='';
	$Tablas=q("select * from aqr_reporte_table where idreporte=$ID");
	while($T=mysql_fetch_object($Tablas))
	{
		$From_sql.=(strlen($From_sql)?", ":"")."$T->nombre $T->apodo";
	}
	return $From_sql;
}

function ejec_campos($ID,$Agrupando)		# CONFIGURACION DE LOS CAMPOS DEL INFORME, ADICIONALMENTE SI HAY GROUP BY, AJUSTA LAS OPERACIONES
{
	if(!$Campos=q("select * from aqr_reporte_field where idreporte=$ID order by orden")) die("<h3 align='center'>No hay campos definidos para el informe</h3>");
	$Campos_sql='';
	while($C=mysql_fetch_object($Campos))
	{
		if($C->operacion && $Agrupando) $Campos_sql.=(strlen($Campos_sql)?", ":"")."$C->operacion($C->nombre) as $C->apodo";
		else $Campos_sql.=(strlen($Campos_sql)?", ":"")."$C->nombre as $C->apodo";
	}
	$Campos_sql=Verifica_variables($Campos_sql);
	return $Campos_sql;
}

#############    FILTROS ####################

function ejec_filtro()
{
	require('inc/gpos.php');
	html();
	echo "<body>".titulo_modulo("Filtro / Orden para: <b>$cabecera</b>");
	$Filtro=qo("select * from aqr_reporte_filtro where idreporte=$idreporte and apodo='$apodo'");
	$Condicion=rep_condicion_campo($Filtro->condicion);
	echo "<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		<table>
			<tr>
				<td>Orden: </td>
				<td><table>
						<tr><td><input type='radio' name='orden' value='' ".($Filtro->orden==''?" checked":"")."></td><td>Ninguno</td></tr>
						<tr><td><input type='radio' name='orden' value='ASC' ".($Filtro->orden=='ASC'?" checked":"")."></td><td>Ascendente</td></tr>
						<tr><td><input type='radio' name='orden' value='DESC' ".($Filtro->orden=='DESC'?" checked":"")."></td><td>Descendente</td></tr>
						<tr><td colspan=2>Reiniciar todos los ordenamientos <input type='checkbox' name='reinicia_orden'></td></tr>
					   </table>
				</td>
			</tr>
			<tr>
			<tr><td>Condicion para este campo:</td><td>$Condicion</td></tr>
			<tr><td>Valor de la condicion:</td><td><textarea name='valorcondicion' style='font-family:arial;font-size:12;' rows=1 cols=50>$Filtro->valorcondicion</textarea></td></tr>
			</tr>
		</table>
		<center>
			<input type='submit' value='Aplicar' style='width:200;'>
			<input type='reset' value='Reiniciar'>
			<input type='button' value='Cancelar' onclick='javascript:window.close();void(null);'>
		</center>
		<input type='hidden' name='Acc' value='set_ejec_filtro'>
		<input type='hidden' name='idreporte' value='$idreporte'>
		<input type='hidden' name='apodo' value='$apodo'>
	</form></body>";
}

function set_ejec_filtro()
{
	require('inc/gpos.php');
	$reinicia_orden=sino($reinicia_orden);
	if(!$id=qo1("select id from aqr_reporte_filtro where idreporte=$idreporte and apodo='$apodo'"))
		$id=q("insert into aqr_reporte_filtro (idreporte,apodo) values ($idreporte,'$apodo')");
	if($reinicia_orden)
	{
		q("update aqr_reporte_filtro set orden='',norden=0 where idreporte=$idreporte");
	}
	$Norden=qo1("Select max(norden) from aqr_reporte_filtro where idreporte=$idreporte")+1;
	if($orden=='') $Norden=0;
	q("update aqr_reporte_filtro set condicion='$condicion', valorcondicion='$valorcondicion', orden='$orden',norden='$Norden' where id=$id");

	echo "<body onload=\"opener.location.reload();window.close();void(null);\">";
}

function ejec_filtros_adicionales($ID)
{
	$Orden='';
	$Condicion='';
	if($Filtros=q("select * from aqr_reporte_filtro where idreporte=$ID order by norden"))
	{
		while($F=mysql_fetch_object($Filtros))
		{
			if($F->orden && $F->norden)
				$Orden.=(strlen($Orden)?", ":" Order by ")."$F->apodo $F->orden";
			if($F->condicion && $F->valorcondicion)
			{
				if($F->condicion!=' Between ' && $F->condicion!=' In ')
					$Comilla=(strpos('  '.$F->valorcondicion,"'")>0)?"":"'";
				else
					$Comilla='';
				$Condicion.=(strlen($Condicion)?" and ":" Where ")."$F->apodo $F->condicion $Comilla$F->valorcondicion$Comilla";
			}
		}
	}
	return $Condicion.$Orden;
}

?>