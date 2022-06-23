<?php
include('inc/funciones_.php');
sesion();
html('MANUAL DE USUARIO');
echo "<body><H3 align='center'>ADMINISTRACION OPERATIVA AUTOMOTRIZ<BR>
MANUAL DE USUARIO DEL SISTEMA</H3>";
if($Manual=q("select * from manual_usuario where concat(',',perfil,',') like '%,".$_SESSION['User'].",%' order by tipo,nombre"))
{
	echo "<table border cellspacing='0' align='center'><tr>
		<th>Tipo</th>
		<th>Nombre</th>
		<th>Descripción</th>
		<th>Ver</th>
		</tr>";
	while($M =mysql_fetch_object($Manual ))
	{
		echo "<tr>
		<td>$M->tipo</td>
		<td>$M->nombre</td>
		<td>$M->descripcion</td>
		<td align='center'><a style='cursor:pointer' onclick=\"modal('$M->url_w',0,0,600,900,'vman');\"><img src='gifs/standar/Preview.png' border='0'></a></td>
		</tr>";
	}
	echo "</table>";
}
else
{
	echo "<center><b>No tiene acceso a ningún tema del manual de usuario.</b></center>";
}
echo "</body>";
?>