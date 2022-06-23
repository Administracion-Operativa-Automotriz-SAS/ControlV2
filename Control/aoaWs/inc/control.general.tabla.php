<?PHP
	html();
 ?>
<SCRIPT LANGUAGE="javascript">
function selecciona_todos_los_campos(campos)
	{	document.msubir.campos_seleccionados.value=campos;}

function selecciona_todos_los_camposb(campos)
	{	document.mbajar.campos_seleccionados.value=campos;}

function valida1()
	{
		with (document.msubir)
		 { if (campos_seleccionados.value.length>0) campos_seleccionados.value+=',';
			campos_seleccionados.value+=campo_seleccionado.value; }
 	}
function valida2()
	{
		with (document.mbajar)
		 { if (campos_seleccionados.value.length>0) campos_seleccionados.value+=',';
			campos_seleccionados.value+=campo_seleccionado.value; }
 	}

function grabando()
{
	document.getElementById('mensajes').innerHTML='Grabando..';
}

</script>
<BODY onload="document.body.scrollTop=leerCookie('SC_TOP');document.body.scrollLeft=leerCookie('SC_LEFT');"
onunload="guardascroll();" Onscroll='fijascroll();' BGCOLOR="#cccccc" >
<span id='mensajes'style='font-size:16;font-weight:bold;color:ffffcc;background-color:3333aa;position:fixed;'></span><br />
<?php
	if(!$nt) die();
	global $AdCapa,$Mayor;
	echo "Control de la Tabla: <B>".$nt."</B><BR>";
	$Nombre_tabla=$nt;
	if(!haytabla($Nombre_tabla.'_t'))
	{
		echo "No existe una tabla de control para $Nombre_tabla. Desea crear la tabla de control?
		<input type='button' value='Si deseo crear la tabla de control' onclick=\"modal('marcoindex.php?Acc=control_inicializar_tabla&nt=$Nombre_tabla');\">";
		die();
	}
	verifica_campos_completos($nt);
	if($AdCapa  && !$Mayor)
	{
		$Mayor=qo1("select max(orden) as mayor from ".$nt."_t where capa='$AdCapa'")+1;
	}



	if(!$SOLO_ADICION)
	{
			echo "<form action='marcoindex.php' method='post' target='Oculto2'>
			<b>Generar opcion</b><br>
			Nombre del menu: <input type='text' name='nmenu' value='Tablas' > Descripcion: <input type='text' name='Descripcion' value='".ucfirst($nt)."'>
			Usuario: ".menu1("USUARIO","Select id,nombre from usuario order by nombre",0,1,'',"onchange='this.form.submit();'")."
			<input type='hidden' name='Acc' value='generar_permiso'><input type='hidden' name='NT' value='$nt'>
			</form>";
			echo "<TABLE BORDER='0' CELLSPACING='0'><tr>
			<TH>Capa</th>
			<th>Orden</TH>
			<th>Col</TH>
			<th>Csp1</TH>
			<th>Csp2</TH>
			<TH>Campo</TH>
			<TH>Tipo</TH>
			<TH>Descripcion</TH>
			<TH>Ver</TH>
			<TH>NoVer</TH>
			<TH>Alineación</TH>
			<TH>Mod</TH>
			<TH>Llave</TH>
			<TH>Opcion</TH></tr>";
		$Mayor=0;
		$Tablat=$nt.'_t';
		if($S=q("select * from $Tablat ORDER BY capa,orden"))
		{
			while ($rc=mysql_fetch_object($S))
			{
				$ULT_ORDEN=$rc->orden;
				if($rc->orden>=$Mayor) $Mayor=$rc->orden+1;
				if($rc->ver) $Ver='checked'; else $Ver='';
				if($rc->nover) $NoVer='checked'; else $NoVer='';
				if($rc->modificar) $Modificar='checked'; else $Modificar='';
				if($rc->llave) $Llave='checked'; else $Llave='';
				echo "<TR ><a name='A_$rc->campo'></a>
				<form action='marcoindex.php' method='post' target='Oculto2' name='forma' id='forma'>
					<input type='hidden' name='Acc' value='control_orden_campo'>
					<td ><A NAME='$rc->id'></A><input type='text' name='capa' value='$rc->capa' size='20' maxlength='30' STYLE='font-size:11;font-family:arial;'></td>
					<td nowrap='yes'><input type='text' name='orden' value='$rc->orden' size='3' maxlength='3' STYLE='font-size:11;font-family:arial;'>
						<input type='button' value='+' style='height:10;width:10;font-size:8;' alt='Incrementar 1' title='Incremententar 1'
						onclick=\"window.open('marcoindex.php?Acc=control_incrementa_orden&tabla=$Tablat&campo=$rc->id','Oculto2');\">
					</td>
					<td><input type='text' name='suborden' value='$rc->suborden' size='3' maxlength='3' STYLE='font-size:11;font-family:arial;'></td>
					<td><input type='text' name='coldes' value='$rc->coldes' size='3' maxlength='3' STYLE='font-size:11;font-family:arial;'></td>
					<td><input type='text' name='columnas' value='$rc->columnas' size='3' maxlength='3' STYLE='font-size:11;font-family:arial;'></td>
					<td><input type='text' name='campo' value='$rc->campo' size='20' maxlength='50' STYLE='font-size:11;font-family:arial;'>
					<td><input type='text' name='tipo' value='$rc->tipo' size='15'  STYLE='font-size:11;font-family:arial;'></td>
					<td><input type='text' name='descripcion' value='$rc->descripcion' size='30' maxlength='100' STYLE='font-size:11;font-family:arial;'></td>
					<td align='center'><input type='checkbox' NAME='ver' $Ver </td>
					<td align='center'><input type='checkbox' NAME='nover' $NoVer </td>
					<td><select name='alinea' style='font-family:arial;font-size:11'>
							<OPTION VALUE='I' "; if($rc->alinea=='I') echo 'SELECTED'; echo ">Izquierda</OPTION>
							<OPTION VALUE='D' "; if($rc->alinea=='D') echo 'SELECTED'; echo ">Derecha</OPTION>
							<OPTION VALUE='C' "; if($rc->alinea=='C') echo 'SELECTED'; echo ">Centro</OPTION>
							<OPTION VALUE='J' "; if($rc->alinea=='J') echo 'SELECTED'; echo ">Justificado</OPTION>
						</select></td>
					<td><input type='checkbox' name='modificar' $Modificar></td>
					<td><input type='checkbox' name='llave' $Llave></td>
					<td nowrap='yes'><input type='hidden' name='nt' value='$nt'><input type='hidden' name='id' value='$rc->id'>
					<input type='submit' value='Ok' style='height:20;width=20;font-family:arial;font-size:11;' onclick='grabando()'>
					<input type='button' value='...' style='height:15;font-family:arial;font-size:11;'
					onclick=\"modal('marcoindex.php?Acc=definicion_campo&Nombre_tabla=$nt&idcampo=$rc->id',10,10,800,900,'Definicion_campo')\">
					<input type='button' value='Del' onclick=\"if(confirm('Desea eliminar el campo $rc->campo?'))
					window.open('marcoindex.php?Acc=control_elimina_campo&nt=$nt&campo=$rc->campo','Oculto2');\" STYLE='font-size:8;font-family:arial;'></td>
					<td nowrap='yes'>"; if($rc->traet && $rc->trael) echo "<b>$rc->traet</b> $rc->trael";
					echo "</td>
				</form>
				</tr>";
			}
		}
	}
	echo "</TABLE>
	<br><table border cellspacing=0><tr><td>
	<form action='marcoindex.php' method='post' target='".($SOLO_ADICION?"_self":"Oculto2")."' name='forma1' id='forma1'>
		<input type='hidden' name='nt' value='$nt'><input type='hidden' name='Acc' value='control_add_campo'>
		<table bgcolor='#ffffff'>
			<tr><td colspan=6 align='center' bgcolor='#eeeeee'><b>Creación de campo</b></td></tr>
			<tr><td valign='top'>Capa:</td><td valign='top'>
			<input type='text' name='Capa' size='20' maxlength='50' value='$AdCapa'></td>
			<td>Descripción:</td><td rowspan=2><TEXTAREA name='Descripcion' style='font-size:12;font-family:arial;' rows=2 cols=70></textarea></td></tr>
			<tr><td>Fil:</td><td><input type='text' name='Orden' value='$Mayor' size='5' maxlength='5'>
			Col: <input type='text' name='suborden' value='0' size=3 maxlength=2></td>
			</tr>
			<tr>
				<td>Nombre del campo:</td><td><input type='text' name='Nombrecampo' size='20' maxlength='20'></td>
				<td>Tipo:</td><td>".tipo()."</td>
				<td>Tam:</td><td><input type='text' name='Sizecampo' value='1' size='4' maxlength='5' onchange=\"javascript:document.forma1.Sizecap.value=this.value;\">
				Capt:<input type='text' name='Sizecap' value='1' size='4' maxlength='5'></td>
			</tr>
			<tr>
				<td>Valor por defecto:</td><td><input type='text' name='Defectocampo' size='20' maxlength='50'></td>
				<td>Tabla:</td><td>".ctablas()." Campo:<input type='text' name='Traen' size='40' maxlength='200'
					ondblclick=\"modal('marcoindex.php?Acc=control_campos_tabla&nt='+this.form.Traet.value,5,5,500,500,'BCT');\"></td>
				<td>Rel</td><td><input type='text' name='Trael' value='id' size='10' maxlength='50'></td>
			</tr>
			<tr>
				<td colspan=6>
				<table border cellspacing=0><tr><td>Ver en Brow <input type='checkbox' name='Ver' checked onchange=\"javascript:if(this.checked==true) document.forma1.Nover.checked=false;\"></td>
				<td>No ver <input type='checkbox' name='Nover' onchange=\"javascript:if(this.checked==true) document.forma1.Ver.checked=false;\"></td>
				<td>Monetario <input type='checkbox' name='Coma' onchange=\"javascript:if(this.checked==true) document.forma1.Caja.checked=false;\"></td>
				<td>Checkbox <input type='checkbox' name='Caja' onchange=\"javascript:if(this.checked==true) document.forma1.Coma.checked=false;\"></td>
				<td>Pasar información a subtablas <input type='checkbox' name='Pasainfo'></td>
				<td>Modificable <input type='checkbox' name='Modificar' checked></td>
				<td>LLave <input type='checkbox' name='Llave'></td>
				<TD> Alineación:
					<select name='alinea' style='font-family:arial;font-size:11'>
						<OPTION VALUE='I' >Izquierda</OPTION>
						<OPTION VALUE='D' >Derecha</OPTION>
						<OPTION VALUE='C' >Centro</OPTION>
						<OPTION VALUE='J' >Justificado</OPTION>
					</select>
				</td>
				</tr>
				<tr>
					<td colspan=2>Color fondo1: <input type='text' name='fondo_desc' value='ffffff' size=5></td>
					<td colspan=2>Color fondo2: <input type='text' name='fondo_celda' value='ffffff' size=5></td>
					<td>Captura Numerica <input type='checkbox' name='obligan'></td>
					<td colspan=2>Area: c:<input type='text' name='cols_text' size='2' class='numero'> r:<input type='text' name='rows_text' size='2' class='numero'></td>
					<td>Resolución: <input type='text' size='2' class='numero' name='tamrecimg' value='300'></td>
				</tr>
				<tr>
					<td colspan=4>Ruta imágenes:<input type='text' name='rutaimg' size=15></td>
				</tr>
				</table>
				</td>

			</tr>
			<tr><td><br>
			<input type='button' value='Crear Campo' onclick=\"valida_campos('forma1','Sizecampo');\">
			</td><td><br><input type='reset' value='Limpiar'></td></tr>
		</table>
		<input type='hidden' name='SOLO_ADICION' value='$SOLO_ADICION'>
	</form>
	<iframe name='Oculto2' id='Oculto2' src='' width=1 height=1 style='visibility:hidden'></iframe>
	";
	if(!$SOLO_ADICION)
	{
		echo "</td></tr></table>
			<hr color='red'>
			<h3><b><font color='red'>INDICES</b></H3><table border cellspacing=0><tr>
			<th>Tabla</th>
			<th>Unico</th>
			<th>Nombre</th>
			<th>Sequencia</th>
			<th>Campo</th>
			<th>&nbsp;</th>
			</tr>";
		$Indices=q("show index from $nt");
		while($I=mysql_fetch_object($Indices))
		{
			$I->Non_unique=($I->Non_unique?0:1);
			echo "<tr>
			<td>$I->Table</td>
			<td align='center'>".($I->Non_unique?"<img src='gifs/standar/si.png' border=0>":"")."</td>
			<td>$I->Key_name</td>
			<td>$I->Seq_in_index</td>
			<td>$I->Column_name</td>
			<td><a onclick=\"if(confirm('Desea borrar el índice de nombre $I->Key_name?'))
					modal('marcoindex.php?Acc=control_del_index&nt=$nt&nombrei=$I->Key_name',0,0,10,10,'di');\">Borrar índice</a></td>";
		}
		echo "</table>
			<form action='marcoindex.php' method='post' target='_blank'>
				<b>Adicionar indice:</b>  Nombre del indice: <input type='text' name='nombrei' value='llave' size=20 maxlength=50> Unico:
				<input type='checkbox' name='unico'><br />
				Campos del indice (Separados por coma): <input type='text' name='campos' size=100><br />
				<input type='submit' value='Crear nuevo indice'>
				<input type='hidden' name='nt' value='$nt'>
				<input type='hidden' name='Acc' value='control_add_index'>
			</form></font>
			<hr color='green'>
			<input type='button' value='Analizar' onclick=\"modal('marcoindex.php?Acc=control_mantenimiento_orden&orden=analizar&t=$nt',0,0,300,400,'orden')\">
			<input type='button' value='Chequear' onclick=\"modal('marcoindex.php?Acc=control_mantenimiento_orden&orden=chequear&t=$nt',0,0,300,400,'orden')\">
			<input type='button' value='Optimizar' onclick=\"modal('marcoindex.php?Acc=control_mantenimiento_orden&orden=optimizar&t=$nt',0,0,300,400,'orden')\">
			<input type='button' value='Reparar' onclick=\"modal('marcoindex.php?Acc=control_mantenimiento_orden&orden=reparar&t=$nt',0,0,300,400,'orden')\">";
	}
	echo "</body>";

function tipo()
{
	return "<SELECT NAME='Tipocampo' STYLE='font-size:11;'>
				<option value='char '>char (1 a 255)</option>
				<option value='vchar '>varchar (1 a 255)</option>
				<option value='int ' onclick=\"this.form.Sizecampo.value='10';this.form.Sizecap.value='10';this.form.Defectocampo.value='0';this.form.alinea.value='D';\"  >int (-2147483648 a 2147483648)</option>
				<option value='int unsigned'   onclick=\"this.form.Sizecampo.value='10';this.form.Sizecap.value='10';this.form.Defectocampo.value='0';this.form.alinea.value='D';\">int unsigned (0 a 2147483648)</option>
				<option value='text '>text (1 a 65.535)</option>
				<option value='date '>date  (1000-01-01 a 9999-12-31)</option>
				<option disabled></option>
				<option value='tinytext '>tinytext (1 a 255)</option>
				<option value='mediumtext '>mediumtext (1 a 16.777.215)</option>
				<option value='longtext '>longtext (1 a 4.294.967.295)</option>
				<option disabled></option>
				<option value='tinyint '  onclick=\"this.form.Defectocampo.value='0';\">tinyint (-127 a 128)</option>
				<option value='tinyint unsigned'   onclick=\"this.form.Defectocampo.value='0';\">tinyint unsigned (0 a 255)</option>
				<option value='smallint '  onclick=\"this.form.Sizecampo.value='4';this.form.Sizecap.value='4';this.form.Defectocampo.value='0';this.form.alinea.value='D';\">smallint (-32768 a 32767)</option>
				<option value='smallint unsigned' onclick=\"this.form.Sizecampo.value='4';this.form.Sizecap.value='4';this.form.Defectocampo.value='0';this.form.alinea.value='D';\">smallint unsigned (0 a 65535)</option>
				<option value='bigint ' onclick=\"this.form.Sizecampo.value='15';this.form.Sizecap.value='15';this.form.Defectocampo.value='0';this.form.alinea.value='D';\">bigint (-9223372036854775808 a 9223372036854775807)</option>
				<option value='bigint unsigned' onclick=\"this.form.Sizecampo.value='15';this.form.Defectocampo.value='0';this.form.alinea.value='D';\">bigint unsigned (0 a 18446744073709551615)</option>
				<option disabled></option>
				<option value='float ' onclick=\"this.form.Sizecampo.value='10,2';this.form.Sizecap.value='10';this.form.Defectocampo.value='0';this.form.alinea.value='D';\">float (presicion de 38 decimales)</option>
				<option value='float unsigned' onclick=\"this.form.Sizecampo.value='10,2';this.form.Sizecap.value='10';this.form.Defectocampo.value='0';this.form.alinea.value='D';\">float (presicion de 38 decimales positivos)</option>
				<option value='double ' onclick=\"this.form.Sizecampo.value='10,2';this.form.Sizecap.value='10';this.form.Defectocampo.value='0';this.form.alinea.value='D';\">double  (presicion de 308 decimales)</option>
				<option value='double unsigned' onclick=\"this.form.Sizecampo.value='10,2';this.form.Sizecap.value='10';this.form.Defectocampo.value='0';this.form.alinea.value='D';\">double  unsigned (presicion de 308 decimales positivos)</option>
				<option value='decimal ' onclick=\"this.form.Sizecampo.value='10,2';this.form.Sizecap.value='10';this.form.Defectocampo.value='0';this.form.alinea.value='D';\">decimal (presicion de 38 decimales $)</option>
				<option disabled></option>
				<option value='datetime '>datetime  (1000-01-01 00:00:00 a 9999-12-31 23:59:59)</option>
				<option value='time '>time  (-838:59:59 a 838:59:59)</option>
				<option value='year '>year  (1901 a 2155)</option>
				<option value='timestamp '>timestamp  (19700101000000 a 20371231235959)</option>
				<option disabled></option>
				<option value='blob '>blob (1 a 65.535)</option>
				<option value='mediumblob '>mediumblob</option>
				<option value='longblob '>longblob</option>
			</SELECT>";
}

function ctablas()
{
	$R="<select name='Traet'><option></option>";
	$st=q("show tables");
	while ($rt=mysql_fetch_row($st))
	{
		$ul=r($rt[0],2);
		if ($ul != '_t' && substr($rt[0],0,15) != 'control_reporte' && $ul != '_s')
		{
			$R.="<OPTION VALUE='$rt[0]'";
			if($rc[5]==$rt[0])  $R.=" SELECTED";
			$R.=">$rt[0]</OPTION>";
		}
	}
	$R.="</select>";
	return $R;
}

?>