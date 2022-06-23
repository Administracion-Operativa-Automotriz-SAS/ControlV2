<?php
function rep_script_sql()
{
	global $idreporte;
	$T=qo1("select instruccion from aqr_reporte where id=$idreporte");
	html();
	echo "<body onload='centrar(700,500);window.focus();'>".titulo_modulo("Script SQL",0)."
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		Script SQL:<br>
		<textarea name='instruccion' rows='20' cols='200' style='font-family:arial;font-size:12;'
		ondblclick=\"modal('marcoindex.php?Acc=ventana_text&Campo=forma.instruccion&Comentario=SQL&Contenido='+escape(this.value),0,0,10,10);\">$T</textarea><br>
		<br>
		<input type='submit' value='Grabar'> <input type='reset' value='Reiniciar'>
		<input type='button' value='Cancelar' onclick='parent.activa_edrep();'>
		<input type='hidden' name='Acc' value='rep_actualiza_sql'><input type='hidden' name='idreporte' value='$idreporte'>
	</form></body>";
}

function rep_actualiza_sql()
{
	global $instruccion,$idreporte;
#		$instruccion=addcslashes($_POST['instruccion'],"\24");
		$instruccion=addslashes(addcslashes($_POST['instruccion'],"\24"));
#		$instruccion=addslashes($instruccion);
	require('inc/link.php');
	mysql_query("update aqr_reporte set instruccion=\"$instruccion\" where id=$idreporte",$LINK);
	mysql_close($LINK);
	echo "<script language='javascript'>function carga()
	{
		parent.activa_edrep();
		//parent.location='reportes.php?Acc=menu_rep&idreporte=$idreporte';
	}</script>
	<body onload='carga()'></body>";
}

function rep_script_condiciones()
{
	global $idreporte;
	$T=qo1("select donde from aqr_reporte where id=$idreporte");
	html();
	echo "<body onload='window.focus();'>".titulo_modulo("Script Condiciones",0)."
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		Script Condiciones:<br>
		<textarea name='donde' rows='20' cols='200' style='font-family:arial;font-size:12;'
		ondblclick=\"modal('marcoindex.php?Acc=ventana_text&Campo=forma.donde&Comentario=Condiciones&Contenido='+escape(this.value),0,0,10,10);\">$T</textarea><br>
		<br>
		<input type='submit' value='Grabar'> <input type='reset' value='Reiniciar'>
		<input type='button' value='Cancelar' onclick='parent.activa_edrep();'>
		<input type='hidden' name='Acc' value='rep_actualiza_condiciones'><input type='hidden' name='idreporte' value='$idreporte'>
	</form>
	<table border cellspacing=0><tr>
	<td>
		<iframe name='c_tablas' id='c_tablas' frameborder='no' src='reportes.php?Acc=rep_campos_tablas&idreporte=$idreporte' height='200' width='200' scrolling='auto'></iframe>
	</td>
	<td>
		<iframe name='c_campos' id='c_campos' frameborder='no' src='reportes.php?Acc=rep_campos_campos&idreporte=$idreporte' height='200' width='500' scrolling='auto'></iframe>
	</td>
	</tr></table>
	</body>";
}

function rep_actualiza_condiciones()
{
	global $donde,$idreporte;
#		$donde=addcslashes($_POST['donde'],"\24");
		$donde=addslashes(addcslashes($_POST['donde'],"\24"));
#		$donde=addslashes($_POST['donde']);
	require('inc/link.php');
	mysql_query("update aqr_reporte set donde=\"$donde\" where id=$idreporte",$LINK);
	mysql_close($LINK);
	echo "<script language='javascript'>function carga()
	{
		parent.activa_edrep();
		//parent.location='reportes.php?Acc=menu_rep&idreporte=$idreporte';
	}</script>
	<body onload='carga()'></body>";
}

function rep_script_resumen()
{
	global $idreporte;
	$T=qo1("select resumen from aqr_reporte where id=$idreporte");
	html();
	echo "<body onload='window.focus();'>".titulo_modulo("Script Resumen",0)."
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		Script Resumen:<br>
		<textarea name='resumen' rows='20' cols='200' style='font-family:arial;font-size:12;'
		ondblclick=\"modal('marcoindex.php?Acc=ventana_text&Campo=forma.resumen&Comentario=Resumen&Contenido='+escape(this.value),0,0,10,10);\">$T</textarea><br>
		<br>
		<input type='submit' value='Grabar'> <input type='reset' value='Reiniciar'>
		<input type='button' value='Cancelar' onclick='parent.activa_edrep();'>
		<input type='hidden' name='Acc' value='rep_actualiza_resumen'><input type='hidden' name='idreporte' value='$idreporte'>
	</form>
	<table border cellspacing=0><tr>
	<td>
		<iframe name='c_tablas' id='c_tablas' frameborder='no' src='reportes.php?Acc=rep_campos_tablas&idreporte=$idreporte' height='200' width='200' scrolling='auto'></iframe>
	</td>
	<td>
		<iframe name='c_campos' id='c_campos' frameborder='no' src='reportes.php?Acc=rep_campos_campos&idreporte=$idreporte' height='200' width='500' scrolling='auto'></iframe>
	</td>
	</tr></table>
	</body>";
}

function rep_actualiza_resumen()
{
	global $resumen,$idreporte;
#		$resumen=addcslashes($_POST['resumen'],"\24");
		$resumen=addslashes(addcslashes($_POST['resumen'],"\24"));
#		$resumen=addslashes($resumen);
	require('inc/link.php');
	mysql_query("update aqr_reporte set resumen=\"$resumen\" where id=$idreporte",$LINK);
	mysql_close($LINK);
	echo "<script language='javascript'>function carga()
	{
		parent.activa_edrep();
		//parent.location='reportes.php?Acc=menu_rep&idreporte=$idreporte';
	}</script>
	<body onload='carga()'></body>";
}

function rep_script_detalle()
{
	global $idreporte;
	$T=qo1("select fdetalle from aqr_reporte where id=$idreporte");
	html();
	echo "<body onload='window.focus();'>".titulo_modulo("Script Detalle",0)."
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		Script Detalle:<br>
		<textarea name='fdetalle' rows='20' cols='200' style='font-family:arial;font-size:12;'
		ondblclick=\"modal('marcoindex.php?Acc=ventana_text&Campo=forma.fdetalle&Comentario=Fórmula Detalle&Contenido='+escape(this.value),0,0,10,10);\">$T</textarea><br>
		<br>
		<input type='submit' value='Grabar'> <input type='reset' value='Reiniciar'>
		<input type='button' value='Cancelar' onclick='parent.activa_edrep();'>
		<input type='hidden' name='Acc' value='rep_actualiza_detalle'><input type='hidden' name='idreporte' value='$idreporte'>
	</form>
	<table border cellspacing=0><tr>
	<td>
		<iframe name='c_tablas' id='c_tablas' frameborder='no' src='reportes.php?Acc=rep_campos_tablas&idreporte=$idreporte' height='200' width='200' scrolling='auto'></iframe>
	</td>
	<td>
		<iframe name='c_campos' id='c_campos' frameborder='no' src='reportes.php?Acc=rep_campos_campos&idreporte=$idreporte' height='200' width='500' scrolling='auto'></iframe>
	</td>
	</tr></table>
	</body>";
}

function rep_actualiza_detalle()
{
	global $fdetalle,$idreporte;
#		$fdetalle=addcslashes($_POST['fdetalle'],"\24");
		$fdetalle=addslashes(addcslashes($_POST['fdetalle'],"\24"));
#		$fdetalle=addslashes($fdetalle);
	require('inc/link.php');
	if(!mysql_query("update aqr_reporte set fdetalle=\"$fdetalle\" where id=$idreporte",$LINK)) die("Error :  ".mysql_error());
	mysql_close($LINK);
	echo "<script language='javascript'>function carga()
	{
		parent.activa_edrep();
		//parent.location='reportes.php?Acc=menu_rep&idreporte=$idreporte';
	}</script>
	<body onload='carga()'></body>";
}

function rep_script_titulo()
{
	global $idreporte;
	$T=qo1("select titulo from aqr_reporte where id=$idreporte");
	html();
	echo "<body onload='window.focus();'>".titulo_modulo("Script Titulo",0)."
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		Script Titulo:<br>
		<textarea name='titulo' rows=20 cols='200' style='font-family:arial;font-size:12;'
		ondblclick=\"modal('marcoindex.php?Acc=ventana_text&Campo=forma.titulo&Comentario=Titulo&Contenido='+escape(this.value),0,0,10,10);\">$T</textarea><br>
		<br>
		<input type='submit' value='Grabar' name='cerrar'>
		<input type='reset' value='Reiniciar'>
		<input type='button' value='Cancelar' onclick='parent.activa_edrep();'>
		<input type='hidden' name='Acc' value='rep_actualiza_titulo'><input type='hidden' name='idreporte' value='$idreporte'>
	</form>
	<table border cellspacing=0><tr>
	<td>
		<iframe name='c_tablas' id='c_tablas' frameborder='no' src='reportes.php?Acc=rep_campos_tablas&idreporte=$idreporte' height='200' width='200' scrolling='auto'></iframe>
	</td>
	<td>
		<iframe name='c_campos' id='c_campos' frameborder='no' src='reportes.php?Acc=rep_campos_campos&idreporte=$idreporte' height='200' width='500' scrolling='auto'></iframe>
	</td>
	</tr></table>
	</body>";
}

function rep_actualiza_titulo()
{
	global $titulo,$idreporte,$cerrar;
#		$titulo=addcslashes($_POST['titulo'],"\24");
		$titulo=addslashes(addcslashes($_POST['titulo'],"\24"));
#		$titulo=addslashes($titulo);
	require('inc/link.php');
	mysql_query("update aqr_reporte set titulo=\"$titulo\" where id=$idreporte",$LINK);
	mysql_close($LINK);
	echo "<script language='javascript'>function carga()
	{
		parent.activa_edrep();
		//parent.location='reportes.php?Acc=menu_rep&idreporte=$idreporte';
	}</script>
	<body onload='carga()'></body>";
}


function rep_script_inicial()
{
	global $idreporte;
	$T=qo1("select pre from aqr_reporte where id=$idreporte");
	html();
	echo "<body onload='window.focus();'>".titulo_modulo("Script Inicial",0)."
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		Script Inicial:<br>
		<textarea name='pre' rows=20 cols='200' style='font-family:arial;font-size:12;'
		ondblclick=\"modal('marcoindex.php?Acc=ventana_text&Campo=forma.pre&Comentario=Script Previo&Contenido='+escape(this.value),0,0,10,10);\">$T</textarea><br>
		<br>
		<input type='submit' value='Grabar'> <input type='reset' value='Reiniciar'>
		<input type='button' value='Cancelar' onclick='parent.activa_edrep();'>
		<input type='hidden' name='Acc' value='rep_actualiza_inicial'><input type='hidden' name='idreporte' value='$idreporte'>
	</form>
	<table border cellspacing=0><tr>
	<td>
		<iframe name='c_tablas' id='c_tablas' frameborder='no' src='reportes.php?Acc=rep_campos_tablas&idreporte=$idreporte' height='200' width='200' scrolling='auto'></iframe>
	</td>
	<td>
		<iframe name='c_campos' id='c_campos' frameborder='no' src='reportes.php?Acc=rep_campos_campos&idreporte=$idreporte' height='200' width='500' scrolling='auto'></iframe>
	</td>
	</tr></table>
	</body>";
}

function rep_actualiza_inicial()
{
	global $pre,$idreporte;
#		$pre=addcslashes($_POST['pre'],"\24");
		$pre=addslashes(addcslashes($_POST['pre'],"\24"));
#		$pre=addcslashes($_POST['pre'],"\24");
#		$pre=addslashes($pre);
	require('inc/link.php');
	mysql_query("update aqr_reporte set pre=\"$pre\" where id=$idreporte",$LINK);
	mysql_close($LINK);
	echo "<script language='javascript'>function carga()
	{
		parent.activa_edrep();
		//parent.location='reportes.php?Acc=menu_rep&idreporte=$idreporte';
	}</script>
	<body onload='carga()'></body>";
}

function rep_portada()
{
	global $idreporte;
	html();
	$Contenido_texto=qo1("Select titulo_rt from aqr_reporte where id=$idreporte");
	echo "<body onload='centrar(800,650);'>".titulo_modulo("Portada del informe",0)."
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>";
		$campo_richedit=new spaweditor('titulo_rt' /*nombre del campo*/, stripslashes($Contenido_texto) /* contenido del campo */);
		$campo_richedit->show();
	echo "<input type='submit' value='Grabar'>
	<input type='button' value='cancelar' onclick='parent.activa_edrep();'>
	<input type='hidden' name='Acc' value='actualiza_portada'>
	<input type='hidden' name='idreporte' value='$idreporte'>
	</form>
	</body>
	";
}

function actualiza_portada()
{
	global $titulo_rt,$idreporte;
	if(MODO_GRABACION_MYSQL==2) $titulo_rt=addslashes($titulo_rt);
	require('inc/link.php');
	mysql_query("update aqr_reporte set titulo_rt=\"$titulo_rt\" where id=$idreporte",$LINK);
	mysql_close($LINK);
	echo "<script language='javascript'>function carga()
	{
		parent.activa_edrep();
		//parent.location='reportes.php?Acc=menu_rep&idreporte=$idreporte';
	}</script>
	<body onload='carga()'></body>";
}

function rep_resumen()
{
	global $idreporte;
	html();
	$Contenido_texto=qo1("Select resumen_rt from aqr_reporte where id=$idreporte");
	echo "<body onload='centrar(800,650);'>".titulo_modulo("Resumen del informe",0)."
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>";
	$campo_richedit=new spaweditor('resumen_rt' , stripslashes($Contenido_texto) );
	$campo_richedit->show();
	echo "<input type='submit' value='Grabar'>
	<input type='button' value='cancelar' onclick='parent.activa_edrep();'>
	<input type='hidden' name='Acc' value='actualiza_resumen'>
	<input type='hidden' name='idreporte' value='$idreporte'>
	</form>
	</body>";
}

function actualiza_resumen()
{
	global $resumen_rt,$idreporte;
	if(MODO_GRABACION_MYSQL==2) $resumen_rt=addslashes($resumen_rt);
	require('inc/link.php');
	mysql_query("update aqr_reporte set resumen_rt=\"$resumen_rt\" where id=$idreporte",$LINK);
	mysql_close($LINK);
	echo "<script language='javascript'>function carga()
	{
		parent.activa_edrep();
		//parent.location='reportes.php?Acc=menu_rep&idreporte=$idreporte';
	}</script>
	<body onload='carga()'></body>";
}

function rep_explicacion()
{
	global $idreporte;
	html();
	$Contenido_texto=qo1("Select explicacion from aqr_reporte where id=$idreporte");
	echo "<body onload='centrar(800,650);'>".titulo_modulo("Explicacion del Informe",0)."
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>";
	$campo_richedit=new spaweditor('explicacion' /*nombre del campo*/, stripslashes($Contenido_texto) /*valor del campo*/);
	$campo_richedit->show();
	echo "<input type='submit' value='Grabar'>
	<input type='button' value='cancelar' onclick='parent.activa_edrep();'>
	<input type='hidden' name='Acc' value='actualiza_explicacion'>
	<input type='hidden' name='idreporte' value='$idreporte'>
	</form>
	</body>
	";
}

function actualiza_explicacion()
{
	global $explicacion,$idreporte;
	if(MODO_GRABACION_MYSQL==2) $explicacion=addslashes($explicacion);
	html();
	mysql_query("update aqr_reporte set explicacion=\"$explicacion\" where id=$idreporte",$LINK);
	mysql_close($LINK);
	echo "<script language='javascript'>function carga()
	{
		parent.activa_edrep();
		//parent.location='reportes.php?Acc=menu_rep&idreporte=$idreporte';
	}</script>
	<body onload='carga()'></body>";
}

?>
