<?php

if($Seguridad = qo("select * from usuario_tab where tabla='reportes2.php' and usuario='".$_SESSION['User']."'"))
{
	if($_SESSION['User'] == 1)
	{$Cond = '';$Cond1 = '';}
	else{	$Cond = "where find_in_set('".$_SESSION['User']."',usuarios)";	$Cond1 = " and find_in_set('".$_SESSION['User']."',usuarios)";}
	echo "<select onchange='var Id=this.value;this.value=0;run_rep(Id);' style='font-size:9;width:100px'><option value='0'> Mis Reportes </opbion>";
	$MisReportes=q("select id,concat(clase,' - ',nombre,' (',id,')') as nr,clase from aqr_reporte $Cond order by clase,nombre");
	$Ant_clase='';
	if($MisReportes)
	while($MR=mysql_fetch_object($MisReportes))
	{
		if($Ant_clase!=$MR->clase)
		{
			if($BGC=='f0fff0') $BGC='ddffdd'; else $BGC='f0fff0';
			$Ant_clase=$MR->clase;
		}
		echo "<option value='$MR->id' style='background-color:$BGC;'>$MR->nr</option>";
	}
	echo "</select>";
}

?>
