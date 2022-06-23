<?php
function rep_orden()
{
	global $idreporte;
	html();
	echo "<body bgcolor='#eeeeee' topmargin=0 leftmargin=0 rightmargin=0 bottonmargin=0>
	<table width='100%' cellspacing=0 border><tr>
	<td align='center'>Ord</td>
	<td align='center'>Campo</td>
	<td align='center'>Tipo</td>
	<td align='center'>Agrupado</td>
	</tr>
	";
	if($Campos=q("select * from aqr_reporte_order where idreporte=$idreporte order by orden"))
	{
		while($C=mysql_fetch_object($Campos))
		{
			$Agrupado=$C->agrupado?"<input type='checkbox' checked disabled>":"<input type='checkbox' disabled>";
			$Color=$C->total?"bgcolor='$C->color'":"";
			$G=$C->lgrafica?'G':'';
			$Alt=($C->agrupado?'Agrupado':'').($C->lgrafica?' Gráfica':'');
			echo "<tr onclick=\"modal('reportes.php?Acc=ad_orden&idorden=$C->id&idreporte=$idreporte',10,10,500,600,'dialogo');\" style='cursor:pointer;' alt='$Alt' title='$Alt'>
				<td>$C->orden</td><td width=30 $Color>$C->nombre</td><td>$C->tipo</td><td align='center'>$Agrupado $G</td></tr>";
		}
	}
	echo "<tr><td colspan=4 onclick=\"modal('reportes.php?Acc=ad_orden&idreporte=$idreporte',10,10,500,600,'dialogo');\" style='cursor:pointer;'>
			<img src='gifs/mas.gif' border=0 style='cursor:pointer;'> Adicionar Orden</td></tr></table>";

}

function ad_orden()
{
	global $idorden,$idreporte;
	if($idorden)
	{
		$D=qo("select * from aqr_reporte_order where id=$idorden");
		if($D->agrupado) $Agrupado='checked'; else $Agrupado='';
		if($D->lgrafica) $Lgrafica='checked'; else $Lgrafica='';
		if($D->total) $Total='checked'; else $Total='';
		$Orden=$D->orden;
	}
	else
	{
		$Orden=qo1("select max(orden) from aqr_reporte_order where idreporte='$idreporte'")+2;
		$Agrupado='';$Total='';$Lgrafica='';
	}
	html();
	echo "<body onload='centrar(800,700);'>";
	if($idorden) echo titulo_modulo("Actualización de orden del informe");
	else echo titulo_modulo("Adicion de orden del informe");
	echo "<br><br>
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		Seleccione el campo por el cual necesita ordenar el informe: <br><table>
	<tr><td>Orden</td><td><input type='text' name='orden' value='$Orden' size='4' maxlength='4'> Presentar Totales:
	<input type='checkbox' name='total' $Total> Color:
	<input type='text' name='color' value='$D->color' size='7' maxlength='7' ondblclick=\"pickcolor('forma','color',this.value);\" ></td></tr>
	<tr><td>Campo:</td><td>";
	if($Campos=q("select * from aqr_reporte_field where idreporte=$idreporte order by orden"))
	{
		echo "<select name='nombre'><option></option>";
		while($C=mysql_fetch_object($Campos))
		{
			echo "<option value='$C->apodo' ";if($D->nombre==$C->apodo) echo "selected"; echo ">$C->nombre ($C->apodo) $C->cabecera </option>";
		}
		echo "</select>";
	}
	echo "</td></tr>
	<tr><td>Tipo de Orden:</td><td><select name='tipo'><option></option><option value='ASC' ";if($D->tipo=='ASC') echo 'selected';
	echo ">Ascendente</option><option value='DESC' ";if($D->tipo=='DESC') echo 'selected'; echo ">Descendente</option></select></td></tr>
	<tr><td>Agrupado:</td><td><input type='checkbox' name='agrupado' $Agrupado></td></tr>
	<tr><td>Label de Gráfica:</td><td><input type='checkbox' name='lgrafica' $Lgrafica></td></tr>
	<tr><td>Cabecera:</td><td><textarea name='cabecera' rows=7 cols=100 style='font-family:arial;font-size:12;'>$D->cabecera</textarea></td></tr>
	<tr><td>Pie:</td><td><textarea name='pie' rows=7 cols=100 style='font-family:arial;font-size:12;'>$D->pie</textarea></td></tr>
	<tr><td colspan=2 align='center'><input type='button' value='Grabar' onclick=\"javascript:valida_campos('forma','campo,tipo');\" style='width:200;'></tr></table>
	<input type='hidden' name='Acc' value='actualiza_orden'><input type='hidden' name='idorden' value='$idorden'>
	<input type='hidden' name='idreporte' value='$idreporte'>
	</form>";
	if($idorden) echo "<input type='button' value='Borrar este orden'
		onclick=\"javascript:if(confirm('Desea elimninar este orden?')) document.location='reportes.php?Acc=del_orden&idorden=$idorden';\">";
}

function del_orden()
{
	global $idorden;
	q("delete from aqr_reporte_order where id='$idorden'");
	echo "<body onload='javascript:window.close();void(null);opener.location.reload();'>";
}
function actualiza_orden()
{
	require('inc/gpos.php');
    /*
	if(MODO_GRABACION_MYSQL==3)
	{
		$cabecera=addcslashes($_POST['cabecera'],"\24");
		$pie=addcslashes($_POST['pie'],"\24");
	}
	elseif(MODO_GRABACION_MYSQL==2)
	{
		$cabecera=addslashes(addcslashes($_POST['cabecera'],"\24"));
		$pie=addslashes(addcslashes($_POST['pie'],"\24"));
	}
	elseif(MODO_GRABACION_MYSQL==1)
	{
		$cabecera=addslashes($cabecera);
		$pie=addslashes($pie);
	}
    */
    $cabecera=$_POST['cabecera'];
    $pie=$_POST['pie'];
	$agrupado=sino($agrupado);$total=sino($total);$lgrafica=sino($lgrafica);
	if($idorden)
		q("update aqr_reporte_order set orden='$orden', nombre='$nombre', agrupado='$agrupado', tipo='$tipo', cabecera=\"$cabecera\", pie=\"$pie\",
			color='$color',total='$total',lgrafica='$lgrafica' where id=$idorden");
	else
		q("insert into aqr_reporte_order (idreporte,orden,nombre,agrupado,tipo,cabecera,pie,color,total,lgrafica) values
			('$idreporte','$orden','$nombre','$agrupado','$tipo',\"$cabecera\",\"$pie\",'$color','$total','$lgrafica')");
	echo "<body onload='javascript:window.close();void(null);opener.location.reload();'>";
}

?>
