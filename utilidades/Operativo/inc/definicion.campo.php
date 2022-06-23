<?PHP // DEFINICION DE CAMPO
#require('html/spaw_control.class.php');
//verifica_campos_completos($Nombre_tabla);
?>
<script language="JAVASCRIPT">
var micampo = '';

function valida_mkuser()
{
	with (document.advanced)
	{
		if (usuario.value.length>0) usuario.value+=',';
		usuario.value+=usuario_seleccionado.value;
	}
}

function compara(este)
{ r1 = (micampo=este)? true:false;return r1; }

function convierte_a_mayusculas()
{
	document.advanced.scambio.value+="javascript:this.value=this.value.toUpperCase();"
}
function convierte_a_minusculas()
{
	document.advanced.scambio.value+="javascript:this.value=this.value.toLowerCase();"
}
function validacion_de_email()
{
	document.advanced.scambio.value+="javascript:validaemail(this);"
}

function referirse()
{
	document.advanced.scambio.value+="javascript:document.mod.CAMPO.value;"
	alert("Por favor cambie la palabra CAMPO en la expresión por el nombre del campo que desee manipular, si quisiera referirse a este mismo campo puede cambiar todo por 'this.value.' ");
}
function s_escribe()
{ document.advanced.scambio.value+=document.advanced.javaoption.value+'=\"javascript: \" '; }

function mostrar_colores(actual,rutina)
{
	var ventana = window.open('html/dialogs/colorpicker.php?lang=sp&theme=default&callback='+rutina,'color_picker','status=no,modal=yes,width=350,height=250');
	ventana.dialogArguments = actual;
}
</SCRIPT>
<?php
	if(!$RE=qo("select * from ".$Nombre_tabla."_t where id=$idcampo")) die ('Problemas en la apertura de configuracion');
?>
<TITLE>Explicacion del campo <?=$RE->descripcion?></TITLE>
<BODY BGCOLOR="#99CCCC" oncontextmenu='return false' STYLE="font-size:10;" <?
if($_COOKIE['DC_Capa'])
echo " onload=\"muestra('".$_COOKIE['DC_Capa']."');\"";
?>
>
<h2> Tabla: <font color='blue' size=5><i><?=$Nombre_tabla?></i></font> Campo: <font color='blue' size=5><i><?=$RE->campo?> </i></font><font color='green'><?=$RE->tipo?></font></h2>
<FORM ACTION="marcoindex.php" METHOD="post" TARGET="_self" name='advanced' id='advanced'>
	<INPUT TYPE="submit" VALUE="Grabar" STYLE="height:20;width:80;">
	<INPUT TYPE='hidden' id='Capa' name='Capa' value='<?=$_COOKIE['DC_Capa']?>'>
	<INPUT TYPE='hidden' NAME='Acc' value='grabar_definicion_campo'>
	<input type='hidden' name='Nombre_tabla' value='<?=$Nombre_tabla?>'>

	<input type='button' onclick="muestra('Conexion');oculta('Seguridad');oculta('Visualizacion');oculta('Html');document.advanced.Capa.value='Conexion';" STYLE='font-size:10;cursor:pointer;height:20;width:80;' value='Conexiones'>
	<input type='button' onclick="muestra('Seguridad');oculta('Conexion');oculta('Visualizacion');oculta('Html');document.advanced.Capa.value='Seguridad';" STYLE='font-size:10;cursor:pointer;height:20;width:80;' Value='Seguridad'>
	<input type='button' onclick="muestra('Visualizacion');oculta('Conexion');oculta('Seguridad');oculta('Html');document.advanced.Capa.value='Visualizacion';" STYLE='font-size:10;cursor:pointer;height:20;width:80;' value='Visualizacion'>
	<input type='button' onclick="oculta('Visualizacion');oculta('Conexion');oculta('Seguridad');muestra('Html');document.advanced.Capa.value='Html';" STYLE='font-size:10;cursor:pointer;height:20;width:80;' value='Html'>
	<INPUT TYPE='button' VALUE='Cerrar' onclick='javascript:window.close();void(null);' STYLE='height:20;width:80;'>
	<table STYLE="font-size:small;">
	<tr><td>
			Capa:<INPUT TYPE='text' NAME='capa' value='<?=$RE->capa?>' size=20 STYLE='font-size_10;'><br>
			Descripcion:<br>
			<TEXTAREA name='descripcion' style='font-size:12;font-family:arial;' rows=3 cols=100><?=$RE->descripcion?></textarea>
	</td></tr>
	<tr><td valign='top'>Explicacion del campo <?=$RE->descripcion?>:
		<TEXTAREA NAME="explicacion" ROWS="1" COLS="80" onfocus="javascript:select();"><?=$RE->explicacion?></TEXTAREA>
	</td></tr></table>
	<?php capa('Conexion',1,'Absolute',''); ?>
	<TABLE BORDER="1" bgcolor='#dddddd'>
		<tr>
		<th colspan=2 bgcolor='#bbbbdd'>Conexión del campo <b><?=strtoupper($RE->descripcion)?></b> con la tabla <b><?=strtoupper($RE->traet)?></b></th>
		<td colspan=2>Tabla
			<SELECT NAME="Traet"><OPTION></OPTION>
				<?php
				$st=q("show tables");
				while ($rt=mysql_fetch_row($st))
				{	$ul=r($rt[0],2);
					if ($ul != '_t' && substr($rt[0],0,15) != 'control_reporte' && $ul != '_s')
					{	echo "<OPTION VALUE='$rt[0]'"; if($RE->traet==$rt[0]) echo " SELECTED"; echo ">$rt[0]</OPTION>";}
				} ?>
			</SELECT>

			<a class='info' style='cursor:pointer;'
            onclick="var D=document.advanced;
               modal('marcoindex.php?Acc=generar_t_mysql&Tabla=<?php echo $Nombre_tabla;?>&Campo=<?php echo $idcampo;?>',0,0,10,10,'GenT');">Generar funcion T_<span style='width:200px'>Generar la funci&oacute;n T_ en mysql para ser usado en Browtabla</span></a>
		</td></tr>
		<TR>
			<td align='right' NOWRAP="YES"></b> Mostrar el campo:</td>
			<TD><INPUT TYPE="text" onfocus="javascript:select();" NAME="traen" VALUE="<?=$RE->traen?>" SIZE="70"></TD>
			<TD align='right' NOWRAP="YES">Campo rel. de la tabla <b><?=strtoupper($RE->traet)?></b>:</td>
			<TD><INPUT TYPE="text" onfocus="javascript:select();" NAME="trael" VALUE="<?=$RE->trael?>" SIZE="10"></TD>
		</TR>
		<TR>
			<td align='right' >Condición (MySql):</td>
			<TD COLSPAN="3"><INPUT TYPE="text" onfocus="javascript:select();" NAME="traec" VALUE="<? echo $RE->traec;?>" SIZE="50">
				No incluir opcion nula <input type='checkbox' name='blanco0' <?php if($RE->blanco0) echo 'CHECKED'; ?>>
				<b><font color='blue'>B&uacute;squeda con Popup</font></b> <input type='checkbox' name='buscapopup' <?php if($RE->buscapopup) echo 'Checked'; ?>>
				Buscar Ciudad: <input type='checkbox' name='busca_ciudad' <?php if($RE->busca_ciudad) echo 'Checked'; ?>>
				Alfa: <input type='checkbox' name='balfabeto' <?php if($RE->balfabeto) echo 'Checked'; ?>>
			</TD>
		</TR>
		<TR>
			<td  valign='top' colspan=4>Instrucción especial del select del combo: Si se requiere un menu fijo se puede usar (1;OPCION 1;2;OPCION 2;..etc)<br>
			<TEXTAREA NAME="traex" ROWS="5" COLS="120" style='font-family:arial;font-size:12;'
			ondblclick="modal('marcoindex.php?Acc=ventana_text&Campo=advanced.traex&Contenido='+escape(this.value),0,0,10,10);"><?=$RE->traex?></TEXTAREA>
			</TD>
		</TR>
		<TR>
			<td  valign='top' colspan=4>Select para ver los registros<br>
			<TEXTAREA NAME="verx" ROWS="2" COLS="120"  style='font-family:arial;font-size:12;'
			ondblclick="modal('marcoindex.php?Acc=ventana_text&Campo=advanced.verx&Contenido='+escape(this.value),0,0,10,10);"><?=$RE->verx?></TEXTAREA>
			</TD>
		</TR>
	</TABLE>
	<?php
	echo "<input type='button' value='Ver en que Reportes es usado este campo' stile='width:300;' onclick=\"modal('marcoindex.php?Acc=control_campo_reportes&Tabla=$Nombre_tabla&Campo=$RE->campo',0,0,500,800,'Reportescampo');\">
	&nbsp;&nbsp;<input type='button' value='Ver en que Tablas es usado este campo' stile='width:300;' onclick=\"modal('marcoindex.php?Acc=control_campo_tablas&Tabla=$Nombre_tabla&Campo=$RE->campo',0,0,500,1000,'Reportescampo');\">
	&nbsp;&nbsp;<input type='button' value='Ver en que Tablas o Reportes es usada esta tabla' stile='width:300;' onclick=\"modal('marcoindex.php?Acc=control_campo_tablasreportes&Tabla=$Nombre_tabla',0,0,500,1000,'Reportescampo');\">
	<br />";

	fincapa(); capa('Seguridad',1,'Absolute',''); ?>
	<table BORDER="1" STYLE="font-size:small;" bgcolor='#dddddd'>
	<tr><th colspan=2 bgcolor='#bbbbdd'>Seguridad del Campo</th></tr>
	<tr>
		<td>
			No ver este campo:<INPUT TYPE="checkbox" NAME="nover" <?php if($RE->nover) echo 'CHECKED'; ?> STYLE="font-size:x-small;">
			No capturar este campo cuando: <INPUT TYPE="text" name="nocapturar" value="<?=$RE->nocapturar?>" size=50 style='font-size:11;'>
		</TD>
	</tr>
	<tr>
		<TD>Usuarios permitidos a la modificacion de este campo:<INPUT TYPE='text' NAME='usuario' value="<?=$RE->usuario?>" STYLE='font-size:x-small;'><br>En blanco: acceso para todos
			<select name="usuario_seleccionado" onchange="valida_mkuser();">
			<OPTION VALUE=""></OPTION>
			<?PHP
				$SS=q("Select concat(id,' ',nombre) as nombre,id from usuario order by id");
				while ($RU=mysql_fetch_object($SS)) echo "<OPTION VALUE=$RU->id>$RU->nombre</option>";
			?>
			</select>
		</TD>
	</tr>
	<TR>
		<td  valign='top'>Condición para poder accesar este campo en la ventana de modificación:<br>
		<INPUT TYPE="text" onfocus="javascript:select();" NAME="cond_modi"VALUE="<?=$RE->cond_modi?>" SIZE="120" STYLE="font-size:12;"></TD>

	</TR>
	<TR>
		<td  valign='top'>
		Java Script Ejemplo: (
		<a href="javascript:convierte_a_mayusculas();">Convertir a mayusculas</a>
		<a href="javascript:convierte_a_minusculas();">Convertir a minusculas</a>
		<a href="javascript:validacion_de_email();">Validar un email</a>
		<a href="javascript:referirse();">Referirse a un campo</a>
		<SELECT name="javaoption" onchange="s_escribe();">
		<option ></option>
		<option >OnBlur</option>
		<option >OnFocus</option>
		<option >OnChange</option>
		<option >OnClick</option>
		<option >OnDblClick</option>
		<option >OnKeyPress</option>
		<option >OnKeyDown</option>
		<option >OnKeyUp</option>
		<option >OnMouseDown</option>
		<option >OnMouseMove</option>
		<option >OnMouseOver</option>
		<option >OnMouseOut</option>
		<option >OnMouseUp</option>
		<option >OnSelect</option>
		<option >OnFocus="select()";</option>
		</select> )<br>
		<textarea name="scambio" ROWS=5 COLS=120 STYLE="font-family:'arial';font-size:12;"
		ondblclick="modal('marcoindex.php?Acc=ventana_text&Campo=advanced.scambio&Contenido='+escape(this.value),0,0,10,10);"><?=$RE->scambio?></textarea>
		</TD>
	</TR>
		<tr>
			<td>Mostrar la informacion directamente en el brow <input type='checkbox' name='browdirecto' <?php if($RE->browdirecto) echo 'CHECKED'; ?> >
			Modificacion directa solo por el superusuario <input type='checkbox' name='supermod' <?php if($RE->supermod) echo 'CHECKED'; ?> >
			<font color='blue'>Campo Obligatorio <input type='checkbox' name='obliga' <?php if($RE->obliga) echo 'CHECKED'; ?> >
			Num&eacute;rico <input type='checkbox' name='obligan' <?php if($RE->obligan) echo 'CHECKED'; ?> ></font></TD>
		</tr>
</table>
<?php fincapa();capa('Visualizacion',1,'Absolute',''); ?>
<table BORDER="1" STYLE="font-family:arial;font-size:10;" bgcolor='#dddddd'>
	<tr>
		<th colspan=4 bgcolor='#bbbbdd'>Visualización del campo</th>
	</tr>
	<tr>
		<td>Fila:  <INPUT TYPE="text" SIZE="2" NAME="orden" VALUE="<?=$RE->orden?>" STYLE="font-size:12;" onfocus="javascript:select();">
		Orden:  <INPUT TYPE="text" SIZE="2" NAME="suborden" VALUE="<?=$RE->suborden?>" STYLE="font-size:12;" onfocus="javascript:select();">
		Buscador:  <INPUT TYPE='checkbox' NAME='tagbusca' <?php if($RE->tagbusca) echo 'CHECKED'; ?>>
		</td>
		<td>Tamaño de Captura: <input type='text' name='sizecap' value='<?=$RE->sizecap?>' STYLE="font-size:12;" onfocus="javascript:select();" size='5' maxlength='5'></td>
		<td>COLSPAN 1:  <INPUT TYPE="text" NAME="coldes" VALUE="<?=$RE->coldes?>" SIZE="2" STYLE="font-size:12;" onfocus="javascript:select();"><br />
		COLSPAN 2:  <INPUT TYPE="text" NAME="columnas" VALUE="<?=$RE->columnas?>" SIZE="2" STYLE="font-size:12;" onfocus="javascript:select();"></td>
		<td>ROWSPAN 1:  <INPUT TYPE="text" NAME="rowspan1" VALUE="<?=$RE->rowspan1?>" SIZE="2" STYLE="font-size:12;" onfocus="javascript:select();"><br />
		ROWSPAN 2:  <INPUT TYPE="text" NAME="rowspan2" VALUE="<?=$RE->rowspan2?>" SIZE="2" STYLE="font-size:12;" onfocus="javascript:select();"></td>
	</tr>
	<TR>
		<td>Nueva Tabla  <INPUT TYPE='checkbox' NAME='nueva_tabla' <?php if($RE->nueva_tabla) echo 'CHECKED'; ?>></td>
		<td>Ancho: <INPUT TYPE="text" NAME="ancho_tabla" VALUE="<?=$RE->ancho_tabla?>" SIZE="5" STYLE="font-size:12;" onfocus="javascript:select();"></td>
		<td>COLTEXT: <INPUT TYPE="text" NAME="cols_text"VALUE="<?=$RE->cols_text?>" SIZE="2" STYLE="font-size:12;" onfocus="javascript:select();"></td>
		<td>ROWTEXT: <INPUT TYPE="text" NAME="rows_text"VALUE="<?=$RE->rows_text?>" SIZE="2" STYLE="font-size:12;" onfocus="javascript:select();"></td>
	</TR>
	<tr>
		<TD align='right' NOWRAP="YES">Presentación: monetaria: <INPUT TYPE="checkbox" NAME="coma" <?php if($RE->coma) echo 'CHECKED'; ?> STYLE="font-size:12;"></TD>
		<TD align='right' NOWRAP="YES">Presentación: Checkbox: <INPUT TYPE="checkbox" NAME="caja" <?php if($RE->caja) echo 'CHECKED'; ?> STYLE="font-size:12;"></TD>
		<TD align='right' NOWRAP="YES">Presentación: encriptada: <INPUT TYPE='checkbox' NAME='password' <?php if($RE->password) echo 'CHECKED'; ?> STYLE='font-size:12l;'></TD>
		<TD align='right' NOWRAP="YES">Trasladar descripcion: <INPUT TYPE='checkbox' NAME='pasa_descripcion' <?php if($RE->pasa_descripcion) echo 'CHECKED'; ?> STYLE='font-size:12;'></TD>
	</tr>
	<TR>
		<td  valign='top' colspan=4 style="font-family:arial;font-size:8;">
		Color del fondo de la descripcion:<INPUT TYPE="text" SIZE="7" STYLE="background-color:<?=$RE->fondo_desc?>;" onfocus="javascript:select();" ondblclick="pickcolor('advanced','fondo_desc',this.value);" NAME="fondo_desc" VALUE="<?=$RE->fondo_desc?>" STYLE="font-size:10;">&nbsp;
		Color de fuente de la descripcion:<INPUT TYPE="text" SIZE="7" STYLE="background-color:<?=$RE->primer_desc?>;" onfocus="javascript:select();" ondblclick="pickcolor('advanced','primer_desc',this.value);" NAME="primer_desc" VALUE="<?=$RE->primer_desc?>" STYLE="font-size:10;"><br>
		Color de fondo celda de captura:<INPUT TYPE="text" SIZE="7" STYLE="background-color:<?=$RE->fondo_celda?>;" onfocus="javascript:select();" ondblclick="pickcolor('advanced','fondo_celda',this.value);" NAME="fondo_celda" VALUE="<?=$RE->fondo_celda?>" STYLE="font-size:10;">&nbsp;
		Color fondo casilla captura:<INPUT TYPE="text" SIZE="7" STYLE="background-color:<?=$RE->fondo_campo?>;" onfocus="javascript:select();" ondblclick="pickcolor('advanced','fondo_campo',this.value);" NAME="fondo_campo" VALUE="<?=$RE->fondo_campo?>" STYLE="font-size:10;">&nbsp;
		Color fuente casilla captura:<INPUT TYPE="text" SIZE="7" STYLE="background-color:<?=$RE->primer_campo?>;" onfocus="javascript:select();" ondblclick="pickcolor('advanced','primer_campo',this.value);" NAME="primer_campo" VALUE="<?=$RE->primer_campo?>" STYLE="font-size:10;">
		</td>
	</TR>
	<tr>
		<td colspan=2>
			Ruta para im&aacute;genes independientes <input type='text' name='rutaimg' value="<?=$RE->rutaimg?>" STYLE="font-size:12;" onfocus="javascript:select();">
		</td>
		<td colspan=2>Tamaño recomendado: <input type='text' name='tamrecimg' value='<?=$RE->tamrecimg?>' size='4' style='font-size:12;' onfocus='javascript:select();'></td>
	</tr>
	<tr>
		<td align='top' colspan=7>
			Propiedades del TD (escribir instrucciones en modo de concatenacion (condicion?verdadero:falso) entre dobles comillas : <textarea name='td_propiedades' ROWS="5" COLS="120" style='font-family:arial;font-size:12;'
			ondblclick="modal('marcoindex.php?Acc=ventana_text&Campo=advanced.td_propiedades&Contenido='+escape(this.value),0,0,10,10);"><?=$RE->td_propiedades?></TEXTAREA>
		</td>
	</tr>
</table>
<INPUT TYPE='hidden' NAME='idcampo' VALUE='<?=$idcampo?>'><INPUT TYPE='hidden' NAME='id' VALUE='<?=$id?>'>
<?php fincapa();

capa('Html',1,'Absolute',''); ?>
<table BORDER="1" STYLE="font-family:arial;font-size:10;" bgcolor='#dddddd'>
	<tr>
		<th colspan=4 bgcolor='#bbbbdd'>Código HTML Adicional en este campo</th>
	</tr>
	<tr>

		<?php
		echo "<td style='cursor:pointer;' title='CLICK PARA MODIFICAR' onclick=\"modal('marcoindex.php?Acc=addhtmlcampo&idcampo=$idcampo&Nombre_tabla=$Nombre_tabla',0,0,800,800,'Htmladd');\">Click para modificar<br />";
		echo $RE->htmladd;
		#	$campo_richedit=new spaweditor('htmladd' /*nombre del campo*/, stripslashes($RE->htmladd) /* contenido del campo */);
		#	$campo_richedit->show();
		?>
		</td>
	</tr>
	<tr>
		<textarea name='htmladd' cols=100 rows=5><?php echo $RE->htmladd;?></textarea>
	</tr>
</table>
<INPUT TYPE='hidden' NAME='idcampo' VALUE='<?=$idcampo?>'><INPUT TYPE='hidden' NAME='id' VALUE='<?=$id?>'>
<?php fincapa();
if(!haycampo('tamrecimg', $Nombre_tabla .'_t')) q("alter table ".$Nombre_tabla."_t add column tamrecimg smallint(4) unsigned default 300 not null");
?>
</FORM>

