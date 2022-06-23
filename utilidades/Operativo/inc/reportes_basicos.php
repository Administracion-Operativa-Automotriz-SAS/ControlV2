<?php
function rep_basicos()
{
	global $idreporte;
	html();
	$R=qo("select * from aqr_reporte where id=$idreporte");
	$Versql=$R->versql?'checked':'';
	$Construye=$R->construye?'checked':'';
	$Gen_csv=$R->gen_csv?'checked':'';
	$Distintos=$R->distintos?'checked':'';
	$Incluir_id=$R->incluir_id?'checked':'';
	$Etiqueta_id=$R->etiqueta_id?'checked':'';
	$Etiqueta_id=$R->etiqueta_id?'checked':'';
	$Csv_contitulos=$R->csv_contitulos?'checked':'';
	$Salida_exel=$R->salida_exel?'checked':'';
	#$Gen_info=$R->gen_info?'checked':'';  # generador de informes multiples
	echo "<body onload='centrar(800,700);'>".titulo_modulo("Parametros Basicos",0)."
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		<table>
			<tr><td>Clasificación en  la lista de informes</td><td><input type='text' name='clase' value='$R->clase' size='80' maxlength='50'></td></tr>
			<tr><td>Nombre del informe</td><td><input type='text' name='nombre' value='$R->nombre' size='80' maxlength='50'></td></tr>
			<tr><td>Construir automáticamente la instrucción SQL</td><td><input type='checkbox' name='construye' $Construye>
			Incluir en el resultado un contador <input type='checkbox' name='incluir_id' $Incluir_id> Etiqueta en gráfica <input type='checkbox' name='etiqueta_id' $Etiqueta_id></td>
			</tr>
			<tr><td>Obtener registros no repetidos (DISTINCT)</td><td><input type='checkbox' name='distintos' $Distintos></td></tr>
			<tr><td>Generar resultado en formato plano CSV</td><td><input type='checkbox' name='gen_csv' $Gen_csv >
			Incluir los titulos como primer registro del CSV <input type='checkbox' name='csv_contitulos' $Csv_contitulos>			<br />
			Nombre del plano csv : <input type='text' name='csv_nombre' value='$R->csv_nombre' maxlength='50'>
			Separador de campos en el csv: <input type='text' name='separador_csv' value='$R->separador_csv' size='1' maxlength=5></td></tr>
			<tr><td>Salida a Exel</td> <td><input type='checkbox' name='salida_exel' $Salida_exel> Nombre del archivo:
			<input type='text' name='salida_exel_nombre' value='$R->salida_exel_nombre' size=20 maxlength=50></td></tr>
			<tr><td>Tamaño del borde</td><td><input type='text' name='borde' value='$R->borde' size=2 maxlength=2></td></tr>
			<tr><td>Caracteristicas de TABLE</td><td><textarea name='ancho' rows=2 cols=80 style='font-family:arial;font-size:12;'>$R->ancho</textarea></td></tr>
			<tr><td>Ancho de la ventana <input type='text' name='vancho' value='$R->vancho' size='4' maxlength='4'></td>
			<td>Alto de la ventana<input type='text' name='valto' value='$R->valto' size=4 maxlength=4></td></tr>
			<tr><td>Titulos Adicionales</td><td><textarea name='tit_add' rows=2 cols=80 style='font-family:arial;font-size:12;'>$R->tit_add</textarea></td></tr>
			<tr><td>Usuarios que tienen acceso a este informe:<br>(en blanco da acceso a todos los usuarios)</td>
			<td><input type='text' name='usuarios' value='$R->usuarios' size='80' maxlength='50'><br>
			<select name='usuario_seleccionado' onchange=\"document.forma.usuarios.value=document.forma.usuarios.value+','+document.forma.usuario_seleccionado.value\";>
			<option></option>";
	if($Usuarios=q("Select * from usuario order by nombre"))
	{
		while($U=mysql_fetch_object($Usuarios))
		{
			echo "<option value='$U->id'>$U->id $U->nombre</option>";
		}
	}
	echo "</select></td></tr>
			<tr><td>Propiedades TR (php)</td><td><textarea name='propiedades_tr' rows=2 cols=80 style='font-family:arial;font-size:12;'>$R->propiedades_tr</textarea></td></tr>
			<tr><td><input type='button' value='Grabar' style='font-weight:bold;' onclick=\"valida_campos('forma','clase,nombre,borde:n,vancho:n,valto:n');\"></td>
			<td><input type='reset' value='Reiniciar los campos'>
			<input type='button' name='cancelar' id='cancelar' value='Cancelar' onclick='parent.activa_edrep();'>
			 </td></tr>
		</table>
		<input type='hidden' name='Acc' value='actualiza_basicos'>
		<input type='hidden' name='idreporte' value='$idreporte'>
	</form>
	</body>";
}

function actualiza_basicos()
{
	require('inc/gpos.php');
	$versql=sino($versql);
	$construye=sino($construye);
	$distintos=sino($distintos);
	$gen_csv=sino($gen_csv);
	$incluir_id=sino($incluir_id);
	$etiqueta_id=sino($etiqueta_id);
	$csv_contitulos=sino($csv_contitulos);
	$salida_exel=sino($salida_exel);
    $propiedades_tr=addslashes(addcslashes($_POST['propiedades_tr'],"\24"));
    $ancho=addslashes(addcslashes($_POST['ancho'],"\24"));
    /*
    	if(MODO_GRABACION_MYSQL==2)
	{
		$propiedades_tr=addslashes($propiedades_tr);
	}
    */
	if($idreporte)
		q("update aqr_reporte set clase='$clase', nombre='$nombre', versql='$versql', construye='$construye', distintos='$distintos' ,
			gen_csv='$gen_csv',borde='$borde',ancho='$ancho',vancho='$vancho',valto='$valto', tit_add=\"$tit_add\",
			usuarios='$usuarios',incluir_id='$incluir_id',etiqueta_id='$etiqueta_id',propiedades_tr=\"$propiedades_tr\",
			csv_contitulos='$csv_contitulos',csv_nombre='$csv_nombre',separador_csv='$separador_csv',salida_exel='$salida_exel',
			salida_exel_nombre='$salida_exel_nombre' where id=$idreporte");
	else
		q("insert into aqr_reporte (clase,nombre,versql,construye,distintos,gen_csv,borde,ancho,vancho,valto,tit_add,
			usuarios,incluir_id,etiqueta_id,propiedades_tr,csv_contitulos,csv_nombre,separador_csv,salida_exel,salida_exel_nombre)
			values ('$clase,'$nombre','$versql','$construye','$distintos','$gen_csv','$borde','$ancho',
			'$vancho','$valto',\"$tit_add\",'$usuarios','$incluir_id','$etiqueta_id',\"$propiedades_tr\",'$csv_contitulos','$csv_nombre',
			'$separador_csv','$salida_exel','$salida_exel_nombre')");
	echo "<script language='javascript'>function carga()
		{	//activa_edrep();
			parent.location='reportes.php?Acc=menu_rep&idreporte=$idreporte';
		}</script>
		<body onload='carga()'></body>";
}

function adreporte()
{
	html();
	echo "<body>".titulo_modulo("Adicionar informe",0);
	echo "<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		Clasificación del informe: <input type='text' name='clase' size='50' maxlength='50'><br>
		Nombre del informe: <input type='text' name='nombre' size='50' maxlength='50'>
		<input type='button' value='Grabar' onclick=\"valida_campos('forma','clase,nombre');\">
		<input type='hidden' name='Acc' value='adreporte_ok'>
	</form>
	";
}

function adreporte_ok()
{
	require('inc/gpos.php');
	$id=q("insert into aqr_reporte (clase,nombre) values ('$clase','$nombre')");
	echo "<body onload='javascript:window.close();void(null);opener.location.reload();'>";
}


?>
