<?php
function rep_relacion()
{
	global $idreporte;
	html();
	echo "<body bgcolor='#eeeeee' topmargin=0 leftmargin=0 rightmargin=0 bottonmargin=0>
	<table width='100%' cellspacing=0 border><tr>
	<td align='center'>Alias</td>
	<td align='center'>Alias</td>
	</tr>
	";
	if($Relaciones=q("select * from aqr_reporte_relacion where idreporte=$idreporte"))
	{
		while($R=mysql_fetch_object($Relaciones))
		{
			echo "<tr onclick=\"modal('reportes.php?Acc=ad_relacion&idrelacion=$R->id&idreporte=$idreporte',10,10,500,600,'dialogo');\" style='cursor:pointer;'>
				<td>$R->alias1</td><td>$R->alias2</td></tr>";
		}
	}
	echo "<tr><td colspan=4 onclick=\"modal('reportes.php?Acc=ad_relacion&idreporte=$idreporte',10,10,500,600,'dialogo');\" style='cursor:pointer;'>
			<img src='gifs/mas.gif' border=0 style='cursor:pointer;'> Adicionar Relacion</td></tr></table>";
}

function ad_relacion()
{
	global $idrelacion,$idreporte;
	if($idrelacion)
	{
		$D=qo("select * from aqr_reporte_relacion where id=$idrelacion");
	}
	html();
	echo "<body onload='centrar(700,800);'>".titulo_modulo("Adicionar / Modificar Relaciones del informe");
	if($Tablas=q("select * from aqr_reporte_table where idreporte=$idreporte order by nombre"))
	{
		echo "<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		Seleccione el primer campo / expresión de la relación:<br>
				<table border cellspacing=0>";
		while($T=mysql_fetch_object($Tablas))
		{
			$Tabla=$T->nombre.'_t';
			if(haytabla($Tabla))
			{
				$Campos=q("select campo,descripcion from $Tabla order by campo");
				echo "<tr>
					<td>$T->nombre</td>
					<td><select name='campo' onchange='document.forma.expresion1.value+=this.value;document.forma.expresion1.focus();'><option></option>";
				while ($C=mysql_fetch_object($Campos))
				{
					$Campo="$T->apodo.$C->campo";
					echo "<OPTION VALUE='$Campo' STYLE='font-size:12;'>$C->campo : $C->descripcion</OPTION>";
				}
				Echo "</select></td></tr>";
			}
		}
		echo "</table><br><center>
			Expresion 1:<textarea name='expresion1' rows=1 cols=50 style='font-family:arial;font-size:12;'>$D->alias1</textarea></center><br>
			Seleccione el segundo campo / expresión de la relación:<br>
		<table border cellspacing=0>";
		mysql_data_seek($Tablas,0);
		while($T=mysql_fetch_object($Tablas))
		{
			$Tabla=$T->nombre.'_t';
			if(haytabla($Tabla))
			{
				$Campos=q("select campo,descripcion from $Tabla order by campo");
				echo "<tr>
					<td>$T->nombre</td>
					<td><select name='campo' onchange='document.forma.expresion2.value+=this.value;document.forma.expresion2.focus();'><option></option>";
				while ($C=mysql_fetch_object($Campos))
				{
					$Campo="$T->apodo.$C->campo";
					echo "<OPTION VALUE='$Campo' STYLE='font-size:12;'>$C->campo : $C->descripcion</OPTION>";
				}
				Echo "</select></td></tr>";
			}
		}
		echo "</table>";
		echo "<br><br><center>
		Expresion 2:<textarea name='expresion2' rows=1 cols=50 style='font-family:arial;font-size:12;'>$D->alias2</textarea></center><br>
		<br>
		<input type='hidden' name='Acc' value='actualiza_relacion'>
		<input type='hidden' name='idreporte' value='$idreporte'>
		<input type='hidden' name='idrelacion' value='$idrelacion'>
		<center><input type='button' value='Adicionar relacion' onclick=\"valida_campos('forma','expresion1,expresion2');\" style='width:200;'>
		<input type='reset' value='Reiniciar Campos'></center></form>";
	}
	else
	echo "<h3>Primero debe seleccionar por lo menos una tabla</h3><input type='button' value='Cerrar esta ventana' onclick='javascript:window.close();void(null);'>";

	if($idrelacion) echo "<input type='button' value='Borrar esta Relacion'
		onclick=\"javascript:if(confirm('Desea elimninar esta relacion?')) document.location='reportes.php?Acc=del_relacion&idrelacion=$idrelacion';\">";
}

function actualiza_relacion()
{
	require('inc/gpos.php');
	if($idrelacion)
		q("update aqr_reporte_relacion set alias1='$expresion1',alias2='$expresion2' where id=$idrelacion");
	else
		q("insert into aqr_reporte_relacion (idreporte,alias1,alias2) values ('$idreporte','$expresion1','$expresion2')");
	echo "<body onload='javascript:window.close();void(null);opener.location.reload();'>";
}

function del_relacion()
{
	require('inc/gpos.php');
	q("delete from aqr_reporte_relacion where id=$idrelacion");
	echo "<body onload='javascript:window.close();void(null);opener.location.reload();'>";
}

function ver_relaciones($idreporte,$tabla,$apodo)
{
	if($Tablas=q("select nombre,apodo from aqr_reporte_table where idreporte=$idreporte and nombre!='$tabla'"))
	{
		while($T=mysql_fetch_object($Tablas))
		{
			if(haytabla($T->nombre."_t"))
			{
				if($Relacion=qo("select campo,trael from ".$T->nombre."_t where traet='$tabla'"))
				{
					$alias1=$apodo.'.'.$Relacion->trael;
					$alias2=$T->apodo.'.'.$Relacion->campo;
					if(!q("select id from aqr_reporte_relacion where idreporte=$idreporte and ((alias1='$alias1' and alias2='$alias2') or (alias2='$alias1' and alias1='$alias2'))"))
						q("insert into aqr_reporte_relacion (idreporte,alias1,alias2) values ($idreporte,'$alias1','$alias2')");
				}
			}
		}
		mysql_data_seek($Tablas,0);
		while($T=mysql_fetch_object($Tablas))
		{
			if(haytabla($tabla."_t"))
			{
				if($R=qo("select campo,trael from ".$tabla."_t where traet='$T->nombre'"))
				{
					$alias1=$apodo.'.'.$R->campo;
					$alias2=$T->apodo.'.'.$R->trael;
					if(!q("select id from aqr_reporte_relacion where idreporte=$idreporte and ((alias1='$alias1' and alias2='$alias2') or (alias2='$alias1' and alias1='$alias2'))"))
						q("insert into aqr_reporte_relacion (idreporte,alias1,alias2) values ($idreporte,'$alias1','$alias2')");
				}
			}
		}
	}
}

?>
