<?php

// PROGRAMA PARA MOSTRAR LOS RESULTADOS DE LOS WEB SERVICES //

include('inc/funciones_.php');
html();
if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}

if(!$Pagina) $Pagina=1;

echo "<script language='javascript'>
function ver(dato){modal('zrws.php?Acc=ver_detalle&id='+dato,0,0,500,500,'vd');}
</script><body><h3>Estado de Lecturas Web Service  </h3>";
$Li=($Pagina-1)*100;
if($Lecturas=q("select *,t_aseguradora(aseguradora) as naseg from web_service order by id desc limit $Li,100"))
{
	echo "
	<input type='button' value=' ANTERIOR ' onclick=\"window.open('zrws.php?Pagina=".($Pagina-1)."','_self');\">
	<input type='button' value=' SIGUIENTE ' onclick=\"window.open('zrws.php?Pagina=".($Pagina+1)."','_self');\">
	<hr>";
	echo "<table border cellspacing='0'><tr>
		<th>Id</th>
		<th>Aseguradora</th>
		<th>Fecha</th>
		<th>Resultado</th>
		</tr>";
	while($L =mysql_fetch_object($Lecturas ))
	{
		echo "<tr>
		<td valign='top'>$L->id</td>
		<td valign='top'>$L->naseg</td>
		<td valign='top'>$L->fecha</td>
		<td><a href='javascript:ver($L->id);'>Ver</a></td>
		</tr>";
	}
	echo "</table>";
}
echo "</body>";

function ver_detalle()
{
	global $id;
	html("VER DETALLE $id");
	echo "<body><script language='javascript'>centrar();</script>";
	$D=qo("select *,t_aseguradora(aseguradora) as naseg from web_service where id=$id");
	echo "<table><tr><td>Aseguradora</td><td>$D->naseg</td></tr>
		<tr><td>Fecha</td><td>$D->fecha</td></tr>
		<tr><td>Descripcion:</td><td>$D->descripcion</td></tr>
	</table>
	<input type='button' name='cerrar' id='cerrar' value=' CERRAR ' onclick='window.close();void(null);'>";
	echo "</body>";
}
?>