<?php
function rep_campos()
{
	global $idreporte,$Cerrar;
	html();
	echo "<body bgcolor='#eeeeff' topmargin=0 leftmargin=0 rightmargin=0 bottonmargin=0>";

	if($Cerrar) echo titulo_modulo("Vista ampliada",1,1);
	echo "
	<table width='100%' cellspacing=0 border><tr bgcolor='#ffffaa'>
	<td align='center'>Ord</td>
	<td align='center'>Campo/expr</td>
	<td align='center'>Alias</td>
	<td align='center'>Titulo</td>
	<td align='center'>Condicion</td>
	<td align='center'>Valor Condicion</td>".($Cerrar?"<td align='center'>Totales</td>":"")."</tr>
	";
	$Campos_seleccionados=',,';
	if($Campos=q("select * from aqr_reporte_field where idreporte=$idreporte order by orden"))
	{
		while($C=mysql_fetch_object($Campos))
		{
			echo "<tr>
				<td ondblclick='parent.duplicar_columna($C->id);' alt='Duplicar con doble click' title='Duplicar con doble click'>$C->orden</td>
				<td  style='cursor:pointer;' width='200' onclick='parent.editar_columna($C->id);' alt='Editar' title='Editar'>$C->nombre</td>
				<td>".($C->ver?"<b>":"").$C->apodo.($C->ver?"</b>":"")."</td>
				<td bgcolor='#ffffff'><font color='#000055'>$C->cabecera</font></td>
				<td align='center'>$C->condicion</td><td>$C->valorcondicion</td>";
			if($Cerrar) echo "<td>$C->operaciont</td>";
			echo "</tr>";
			$Campos_seleccionados.=$C->nombre.',';
		}
	}
	echo "<tr><td colspan=6 onclick='parent.adicionar_columna();' style='cursor:pointer;'>
			<img src='gifs/mas.gif' border=0 style='cursor:pointer;'> Adicionar Campo</td></tr></table>";
}

function ad_campo()
{
	require('inc/gpos.php');
	html();
	echo "<body onload='centrar(900,800);'>".titulo_modulo("Adicion de campo al informe",0)."Solamente seleccione el campo necesario que corresponda a la tabla correcta.<br><br>";
	if($Tablas=q("select * from aqr_reporte_table where idreporte=$idreporte order by nombre"))
	{
		echo "<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
				<table border cellspacing=0>";
		while($T=mysql_fetch_object($Tablas))
		{
			$Tabla=$T->nombre.'_t';
			if(haytabla($Tabla))
			{
				$Campos=q("select campo,descripcion,traex from $Tabla order by campo");
				echo "<tr>
					<td>$T->nombre</td>
					<td><select name='campo' onchange='document.forma.expresion.value+=this.value;document.forma.expresion.focus();' style='font-size:12;'><option></option>";
				while ($C=mysql_fetch_object($Campos))
				{
					$Campo="$T->apodo.$C->campo";
					echo "<OPTION VALUE='$Campo' ";
					if(strpos($C->traex,',') && strpos($C->traex,';'))
					{
						echo "onclick=\"document.forma.menu_opciones.value='$C->traex';\"";
					}
					echo ">$C->campo : $C->descripcion</OPTION>";
				}
				Echo "</select></td></tr>";
			}
		}
		echo "</table><br><br><center>Expresion:<br>
		<textarea name='expresion' rows=5 cols=100 style='font-family:arial;font-size:12;'></textarea></center><br>
		<input type='hidden' name='Acc' value='ad_campo_detalle'>
		<input type='hidden' name='idreporte' value='$idreporte'>
		<input type='hidden' name='menu_opciones' value=''>
		<input type='button' value='Adicionar Campo / Expresión' onclick=\"valida_campos('forma','expresion');\">
		<input type='reset' value='Limpiar Campo'>
		<input type='button' value='Cancelar' onclick='parent.activa_edrep()';</form>";
	}
	else
	echo "<h3>Primero debe seleccionar por lo menos una tabla</h3><input type='button' value='Cerrar esta ventana' onclick='javascript:window.close();void(null);'>";

}

function ad_campo_detalle()
{
	require('inc/gpos.php');
	if ($idcampo)
	{
		$C=qo("select * from aqr_reporte_field where id=$idcampo");
		$Orden=$C->orden;
		if($C->ver) $Ver='checked'; else $Ver='';
		$Alineacion=rep_alineacion_campo($C->alinea);
		if($C->caja) $Caja='checked'; else $Caja='';
		if($C->coma) $Coma='checked'; else $Coma='';
		if($C->imagen) $Imagen='checked'; else $Imagen='';
		if($C->grafica) $Grafica='checked'; else $Grafica='';
		if($C->lgrafica) $LGrafica='checked'; else $LGrafica='';
		if($C->filtro_rapido) $LFiltro_rapido='checked'; else $LFiltro_rapido='';
		$Condicion=rep_condicion_campo($C->condicion);
		$Disabled='';
		$Expresion=$C->nombre;
		$Operacion=rep_operacion_campo($C->operacion);
		$Operaciont=rep_operacion_total($C->operaciont);
		$Alias=$C->apodo;
		$Cabecera=$C->cabecera;
	}
	else
	{
		$Orden=qo1("select max(orden) from aqr_reporte_field where idreporte=$idreporte")+2;
		$Ver='checked';
		$Alineacion=rep_alineacion_campo();
		$Caja='';$Coma='';$Imagen='';$Grafica='';$LGrafica='';$LFiltro_rapido='';
		$Condicion=rep_condicion_campo();
		$Disabled='disabled';
		$Expresion=stripslashes($expresion);
		$Operacion=rep_operacion_campo();
		$Operaciont=rep_operacion_total();
		$Alias=str_replace('.', '_', $Expresion);
		if(!$Cabecera=qo1("select cabecera from aqr_reporte_field where nombre=\"$Expresion\" order by id desc limit 1"))
		$Cabecera=$Alias;
	}
	html();
	echo "<script language='javascript'>
		function verifica_escape(Evento)
		{
			var keynum;
			var Caracter;
			if(window.event) // IE
				keynum = Evento.keyCode;
			else if(Evento.which) // Netscape/Firefox/Opera
				keynum = Evento.which;
			if( keynum==27)
			{
			   parent.activa_edrep();
			}
		}
		function carga()
		{
			document.forma.nombre.focus();
		}
	</script>
	<body onload='carga();' onkeydown='verifica_escape(event);'>".titulo_modulo("Adicion de campo al informe",0)."
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		<table><tr><td align='right'>Expresión / campo:";
		if($menu_opciones)
		{
			$menu_opciones=str_replace(',', "' then '", $menu_opciones);
			$menu_opciones="case $Expresion when '".str_replace(';', "' when '", $menu_opciones)."' end";
			echo "<br /><input type='button' value='case'
			onclick=\"javascript:var menu_opciones='".addslashes($menu_opciones)."';
			document.forma.nombre.value=menu_opciones;\">";
		}
		echo "</td>
		<td colspan=3><textarea name='nombre' style='font-family:arial;font-size:12;' rows='3' cols='130'>$Expresion</textarea></td></tr>
		<tr><td align='right'>Orden:</td>
		<td colspan=3><input type='text' name='orden' value='$Orden' size='3' maxlength='3'>
			&nbsp;&nbsp;Filtro Rápido<input type='checkbox' name='filtro_rapido' $LFiltro_rapido >
			&nbsp;&nbsp;Visible<input type='checkbox' name='ver' $Ver>
			&nbsp;&nbsp;Check<input type='checkbox' name='caja' $Caja>
			&nbsp;&nbsp;Monetaria<input type='checkbox' name='coma' $Coma>
			Decimales:<input type='text' name='comad' class='numero' value='$C->comad' size='1' maxlength='2'>
			&nbsp;&nbsp;Imagen<input type='checkbox' name='imagen' $Imagen>
			&nbsp;&nbsp;Gráfica: - Serie<input type='checkbox' name='grafica' $Grafica>
			&nbsp;&nbsp;Label<input type='checkbox' name='lgrafica' $LGrafica>

		</td></tr>
		<tr><td align='right'>Alias:</td><td colspan=3><input type='text' name='apodo' value='$Alias' size=30 maxlength=50
			ondblclick=\"this.value=document.forma.nombre.value.replace(/\./g,'_');\";>
		   &nbsp;&nbsp;Cabecera:<input type='text' name='cabecera' value='$Cabecera' size=50 maxlength=50
			ondblclick=\"this.value=document.forma.nombre.value.replace(/\./g,'_');\";></td></tr>
		<tr><td align='right'>Operación en sql:</td><td colspan=3>$Operacion
			&nbsp;&nbsp;Totalizar con la operación:$Operaciont
			&nbsp;&nbsp;Alineación:$Alineacion</td></tr>
		<tr><td align='right'>Condicion:</td><td>$Condicion</td><td>
		Valor de la condicion:</td><td><textarea name='valorcondicion' style='font-family:arial;font-size:12;' rows=1 cols='40'>$C->valorcondicion</textarea></td></tr>

		<tr>
			<td align='right'>Hipervinculo:<br /><input type='button' value='Tabla' style='font-size:8;' height=15
			onclick=\"document.forma.hipervinculo.value='marcoindex.php?Acc=abre_tabla&Num_Tabla=xx';\"</td>
			<td colspan=3><textarea name='hipervinculo' style='font-family:arial;font-size:12;' rows=3 cols=130 scroll='auto'>$C->hipervinculo</textarea></td>
		</tr>

		<tr>
			<td align='right'>Script:<br />JavaScript</td>
			<td colspan=3><textarea name='script' style='font-family:arial;font-size:12;' rows=3 cols=130 scroll='auto'>$C->script</textarea></td>
		</tr>

		<tr>
			<td align='right'>Propiedades TD: (php)</td>
			<td colspan=3><textarea name='propiedades_td' style='font-family:arial;font-size:12;' rows=3 cols=130 scroll='auto'>$C->propiedades_td</textarea></td>
		</tr>

		<tr>

			<td align='center'><input type='button' value='Grabar' style='font-weight:bold;' onclick=\"valida_campos('forma','nombre,orden:n,apodo,cabecera');\"></td>
			<td align='center'><input type='reset' value='Reiniciar'></td>
			<td align='center'><input type='button' value='Cancelar' onclick='parent.activa_edrep();'></td>
			<td align='center'><input type='button' value='Borrar este campo' onclick=\"javascript:if(confirm('Desea elimninar este campo?')) document.location='reportes.php?Acc=del_campo&idcampo=$idcampo';\" $Disabled></td></tr>
		<input type='hidden' name='Acc' value='actualiza_campo'><input type='hidden' name='idcampo' value='$idcampo'><input type='hidden' name='idreporte' value='$idreporte'>
		</table>
	</form>
	<table border cellspacing=0><tr>
	<td>
		<iframe name='c_tablas' id='c_tablas' frameborder='no' src='reportes.php?Acc=rep_campos_tablas&idreporte=$idreporte' height='200' width='200' scrolling='auto'></iframe>
	</td>
	<td>
		<iframe name='c_campos' id='c_campos' frameborder='no' src='reportes.php?Acc=rep_campos_campos&idreporte=$idreporte' height='200' width='600' scrolling='auto'></iframe>
	</td>
	</tr></table>
	</body>";
}

function duplica_columna()
{
	global $idcampo,$idreporte;
	require('inc/link.php');
	mysql_query("insert into aqr_reporte_field (idreporte,nombre,orden,apodo,operacion,cabecera,ver,alinea,caja,coma,condicion,valorcondicion,
			imagen,grafica,lgrafica,operaciont,hipervinculo,script,filtro_rapido,propiedades_td,comad) select idreporte,nombre,orden,apodo,operacion,cabecera,ver,alinea,caja,coma,condicion,valorcondicion,
			imagen,grafica,lgrafica,operaciont,hipervinculo,script,filtro_rapido,propiedades_td,comad from aqr_reporte_field where id=$idcampo",$LINK);
	$idn=mysql_insert_id($LINK);
	mysql_close($LINK);
	header("location:reportes.php?Acc=ad_campo_detalle&idcampo=$idn&idreporte=$idreporte");
}

function del_campo()
{
	global $idcampo;
	q("delete from aqr_reporte_field where id='$idcampo'");
	echo "<script language='javascript'>function carga()
		{
			parent.activa_edrep();
			parent.pinta_columnas();
		}</script>
		<body onload='carga()'></body>";
}

function actualiza_campo()
{
	global $ver,$caja,$coma,$imagen,$grafica,$lgrafica,$filtro_rapido,$nombre,$orden,$apodo,$cabecera,$operacion,$alinea,
	$condicion,$valorcondicion,$operaciont,$hipervinculo,$script,$propiedades_td,$idcampo,$idreporte,$comad;
	$ver=sino($ver);
	$caja=sino($caja);
	$coma=sino($coma);
	$imagen=sino($imagen);
	$grafica=sino($grafica);
	$lgrafica=sino($lgrafica);
	$filtro_rapido=sino($filtro_rapido);
    /*
	if(MODO_GRABACION_MYSQL==3)
	{
		$script=addcslashes($_POST['script'],"\24");
		$propiedades_td=addcslashes($_POST['propiedades_td'],"\24");
		$hipervinculo=addcslashes($_POST['hipervinculo'],"\24");
		$valorcondicion=addcslashes($_POST['valorcondicion'],"\24");
		$nombre=addcslashes($_POST['nombre'],"\24");
	}
	elseif(MODO_GRABACION_MYSQL==2)
	{
		$script=addslashes(addcslashes($_POST['script'],"\24"));
		$propiedades_td=addslashes(addcslashes($_POST['propiedades_td'],"\24"));
		$hipervinculo=addslashes(addcslashes($_POST['hipervinculo'],"\24"));
		$valorcondicion=addslashes(addcslashes($_POST['valorcondicion'],"\24"));
		$nombre=addslashes(addcslashes($_POST['nombre'],"\24"));
	}
	elseif(MODO_GRABACION_MYSQL==1)
	{
		$script=addslashes($script);
		$propiedades_td=addslashes($propiedades_td);
		$hipervinculo=addslashes($hipervinculo);
		$valorcondicion=addslashes($valorcondicion);
		$nombre=addslashes($nombre);
	}
    */
    

    //$script=addslashes($script);
		//$propiedades_td=addslashes($propiedades_td);
		//$hipervinculo=addslashes($hipervinculo);
		//$valorcondicion=addslashes($valorcondicion);
		//$nombre=addslashes($nombre);
		
	require('inc/link.php');
	if($idcampo)
	{	if(!mysql_query("update aqr_reporte_field set nombre=\"$nombre\",orden='$orden',apodo='$apodo',cabecera='$cabecera',operacion='$operacion',
			ver='$ver',alinea='$alinea',caja='$caja',coma='$coma',condicion='$condicion',valorcondicion=\"$valorcondicion\",
			imagen='$imagen',grafica='$grafica',lgrafica='$lgrafica',operaciont='$operaciont',hipervinculo=\"$hipervinculo\",
			script=\"$script\",filtro_rapido='$filtro_rapido',propiedades_td=\"$propiedades_td\",comad='$comad' where id=$idcampo",$LINK)) die("Error ".mysql_error());
	}
	else
	{	if(!mysql_query("insert into aqr_reporte_field (idreporte,nombre,orden,apodo,operacion,cabecera,ver,alinea,caja,coma,condicion,valorcondicion,
			imagen,grafica,lgrafica,operaciont,hipervinculo,script,filtro_rapido,propiedades_td,comad) values ('$idreporte',\"$nombre\",'$orden','$apodo','$operacion','$cabecera','$ver',
			'$alinea','$caja','$coma','$condicion',\"$valorcondicion\",'$imagen','$grafica','$lgrafica','$operaciont',\"$hipervinculo\",
			\"$script\",'$filtro_rapido',\"$propiedades_td\",'$comad')",$LINK)) die("Error ".mysql_error());
	}
	mysql_close($LINK);
	echo "<script language='javascript'>function carga()
		{
			parent.activa_edrep();
			parent.pinta_columnas();
		}</script>
		<body onload='carga()'></body>";
}

function rep_condicion_campo($Dato='')
{
	$Resultado="<select name='condicion' style='font-family:arial;font-size:9;'><option value=''></option>";
	$Resultado.="<option value='=' ";if($Dato=='=') $Resultado.='selected'; $Resultado.=">= (igual a)</option>";
	$Resultado.="<option value='!=' ";if($Dato=='!=') $Resultado.='selected'; $Resultado.=">!= (distinto a)</option>";
	$Resultado.="<option value='>=' ";if($Dato=='>=') $Resultado.='selected'; $Resultado.=">>= (mayor o igual que)</option>";
	$Resultado.="<option value='<=' ";if($Dato=='<=') $Resultado.='selected'; $Resultado.="><= (menor o igual que)</option>";
	$Resultado.="<option value='>' ";if($Dato=='>') $Resultado.='selected'; $Resultado.=">> (mayor que)</option>";
	$Resultado.="<option value='<' ";if($Dato=='<') $Resultado.='selected'; $Resultado.=">< (menor que)</option>";
	$Resultado.="<option value=' Like ' ";if($Dato==' Like ') $Resultado.='selected'; $Resultado.=">Like (que contenga) (comodin = % )</option>";
	$Resultado.="<option value=' !Like ' ";if($Dato==' !Like ') $Resultado.='selected'; $Resultado.=">!Like (que no contenga) (comodin = % )</option>";
	$Resultado.="<option value=' Between ' ";if($Dato==' Between ') $Resultado.='selected'; $Resultado.=">Between (entre dos limites: x and y )</option>";
	$Resultado.="<option value=' In ' ";if($Dato==' In ') $Resultado.='selected'; $Resultado.=">In (entre una lista de datos (a,b,c..) )</option>";
	$Resultado.="</select>";
	return $Resultado;
}
function rep_alineacion_campo($Dato='')
{
	$Resultado="<select name='alinea'>";
	$Resultado.="<option value='I' "; if($Dato=='I') $Resultado.=" selected";$Resultado.=">Izquierda</option>";
	$Resultado.="<option value='D' "; if($Dato=='D') $Resultado.=" selected";$Resultado.=">Derecha</option>";
	$Resultado.="<option value='C' "; if($Dato=='C') $Resultado.=" selected";$Resultado.=">Centro</option>";
	$Resultado.="<option value='J' "; if($Dato=='J') $Resultado.=" selected";$Resultado.=">Justificado</option>";
	$Resultado.="</select>";
	return $Resultado;
}

function rep_operacion_campo($Dato='')
{
	$Resultado="<select name='operacion'><option></option><option value='AVG' "; if($Dato=='AVG') $Resultado.=" selected"; $Resultado.=">Promedio</option>";
	$Resultado.="<option value='COUNT' "; if($Dato=='COUNT') $Resultado.=" selected";$Resultado.=">Conteo</option>";
	$Resultado.="<option value='MAX' "; if($Dato=='MAX') $Resultado.=" selected";$Resultado.=">Maximo</option>";
	$Resultado.="<option value='MIN' "; if($Dato=='MIN') $Resultado.=" selected";$Resultado.=">Minimo</option>";
	$Resultado.="<option value='STD' "; if($Dato=='STD') $Resultado.=" selected";$Resultado.=">Desviacion Estandar</option>";
	$Resultado.="<option value='SUM' "; if($Dato=='SUM') $Resultado.=" selected";$Resultado.=">Sumatoria</option>";
	$Resultado.="</select>";
	return $Resultado;
}

function rep_operacion_total($Dato='')
{
	$Resultado="<select name='operaciont'><option></option><option value='AVG' "; if($Dato=='AVG') $Resultado.=" selected";
	$Resultado.=">Promedio</option><option value='COUNT' "; if($Dato=='COUNT') $Resultado.=" selected";
	$Resultado.=" >Conteo</option><option value='MAX' "; if($Dato=='MAX') $Resultado.=" selected";
	$Resultado.=" disabled >Maximo</option><option value='MIN' "; if($Dato=='MIN') $Resultado.=" selected";
	$Resultado.=" disabled>Minimo</option><option value='SUM' "; if($Dato=='SUM') $Resultado.=" selected";
	$Resultado.=">Sumatoria</option></select>";
	return $Resultado;
}

?>
