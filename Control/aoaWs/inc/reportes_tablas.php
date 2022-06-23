<?php

function rep_tablas()
{
	global $idreporte;
	html();
	echo "<body bgcolor='#eeeeee' topmargin=0 leftmargin=0 rightmargin=0 bottonmargin=0>
	<table border width='100%' cellspacing=0>";
	if($Tablas=q("select * from aqr_reporte_table where idreporte=$idreporte order by nombre"))
	{
		while($T=mysql_fetch_object($Tablas))
		{
			echo "<tr onclick=\"modal('reportes.php?Acc=mod_tabla&id=$T->id',10,10,200,200,'dialogo');\" style='cursor:pointer;'><td >$T->nombre</td><td>$T->apodo</td></tr>";
		}
	}
	echo "<tr><td colspan=2 onclick=\"modal('reportes.php?Acc=ad_tabla&idreporte=$idreporte',10,10,400,400,'dialogo');\" style='cursor:pointer;'>
			<img src='gifs/mas.gif' border=0 style='cursor:pointer;'> Adicionar Tabla</td></tr></table>";
}

function ad_tabla()
{
	global $idreporte;
	$Ts=',,';
	if($Tablassel=q("select nombre from aqr_reporte_table where idreporte=$idreporte"))
		while($TS=mysql_fetch_object($Tablassel)) { $Ts.=$TS->nombre.','; }
	html();
	echo "<body onload='centrar(500,700);'>".titulo_modulo("Adicion de tabla")."Solamente seleccione la tabla de la cual necesita obtener información.<br><br>";
	$Tablas=q("show tables");
	echo "<table border cellspacing=0><tr><td colspan=2><b>Tablas Disponibles</b></td></tr>";
	while ($R=mysql_fetch_row($Tablas))
	{
		$L=strlen($R[0]);
		$ul=substr($R[0],$L-2,2);
		if($ul != '_t' && $ul != '_s' && substr($R[0],0,15) != 'control_reporte' && substr($R[0],0,11) != 'aqr_reporte' && !strpos($Ts,','.$R[0].',') && $R[0] != 'usuario' && $R[0] !='usuario_custom' && $R[0] != 'usuario_tab')
		{
			$Dtabla=qo1("select descripcion from usuario_tab where tabla='$R[0]'");
			echo "<tr onclick=\"document.location='reportes.php?Acc=ad_tabla_ok&idreporte=$idreporte&tabla=$R[0]';\" style='cursor:pointer;'><td>$R[0]</td><td>$Dtabla</td></tr>";
		}
	}
	echo "</tabla>";
}

function ad_tabla_ok()
{
	global $idreporte,$tabla;
	if(!$apodo=qo1("select apodo from aqr_reporte_table where nombre='$tabla' order by id desc limit 1"))
	$apodo=substr($tabla,0,2);
	$apodo_final=$apodo;
	$contador=1;
	while (1)
	{
		if(q("select apodo from aqr_reporte_table where idreporte=$idreporte and apodo='$apodo_final'"))
			$apodo_final=$apodo.$contador;
		else
			break;
		$contador++;
	}
	q("insert into aqr_reporte_table (idreporte,nombre,apodo) values ($idreporte,'$tabla','$apodo_final')");
	ver_relaciones($idreporte,$tabla,$apodo);
	echo "<body onload='javascript:window.close();void(null);opener.parent.r_relacion.location.reload();opener.location.reload();'>";

}

function mod_tabla()
{
	global $id;
	$T=qo("select * from aqr_reporte_table where id=$id");
	html();
	echo "<body onload='centrar(500,300);'>".titulo_modulo("Tabla");
	echo "<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		Tabla: <b><input type='text' name='nombre' value='$T->nombre'></b><br>Alias: <input type='text' name='apodo' value='$T->apodo' size='5' maxlength='5'>
		<input type='hidden' name='Acc' value='mod_tabla_ok'>
		<input type='hidden' name='id' value='$id'>
		<input type='hidden' name='aapodo' value='$T->apodo'>
		<input type='hidden' name='idreporte' value='$T->idreporte'>
		<br>
		<input type='button' value='Grabar' onclick=\"valida_campos('forma','apodo');\" style='width:150;'>
		<br><br><input type='button' value='Borrar esta tabla del informe' onclick=\"if(confirm('Desea borrar la tabla del informe?')) document.location='reportes.php?Acc=del_tabla&id=$id';\">
	</form></body>";
}

function mod_tabla_ok()
{
	require('inc/gpos.php');
	$Nombre="$nombre.";$Apodo="$apodo.";$Aapodo="$aapodo.";
	q("update aqr_reporte_table set nombre='$nombre',apodo='$apodo' where id='$id'");
	$Tabla=qo1("select nombre from aqr_reporte_table where id='$id'");
	q("update aqr_reporte_field set nombre=replace(nombre,'$Nombre','$Apodo') where idreporte=$idreporte");
	q("update aqr_reporte_field set nombre=replace(nombre,'$Aapodo','$Apodo') where idreporte=$idreporte");
	q("update aqr_reporte_relacion set alias1=replace(alias1,'$Aapodo','$Apodo') where idreporte=$idreporte");
	q("update aqr_reporte_relacion set alias2=replace(alias2,'$Aapodo','$Apodo') where idreporte=$idreporte");
	ver_relaciones($idreporte,$Tabla,$apodo);
	echo "<body onload='javascript:opener.location.reload();opener.parent.r_relacion.location.reload();opener.parent.r_campos.location.reload();window.close();void(null);'>";
}

function del_tabla()
{
	global $id;
	$R=qo("select idreporte,apodo from aqr_reporte_table where id=$id");
	q("delete from aqr_reporte_table where id='$id'");
	q("delete from aqr_reporte_field where idreporte=$R->idreporte and locate('".$R->apodo.".',nombre)!=0");
	q("delete from aqr_reporte_relacion where idreporte=$R->idreporte and (locate('".$R->apodo.".',alias1)!=0 or locate('".$R->apodo.".',alias2)!=0)");
	echo "<body onload='javascript:window.close();void(null);opener.parent.r_campos.location.reload();opener.parent.r_relacion.location.reload();opener.location.reload();'>";
}
?>
