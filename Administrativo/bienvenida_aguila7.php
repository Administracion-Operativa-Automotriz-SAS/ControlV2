<?
include('inc/funciones_.php');
sesion();
html();


?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
<meta http-equiv='Cache-Control' content='no-cache; must-revalidate; proxy-revalidate; max-age=10'>
<style type='text/css'>@import url(inc/css/estilo".$ESTILO.".css);</style>
<title>BIENVENIDA</title>
</head>
<body bgcolor="#ffffff">
<table width='100%'><TR><td align='center'><img src='img/LOGO_AOA_200.png'></td></TR></table>
<br />
<table width='80%' align='center' border bgcolor='#dddddd' cellpadding='20'><tr><td>
<p align='center'><FONT COLOR='#0099cc' style='font-size:14px'><b><?php echo NOMBRE_APLICACION; ?></B></FONT></p>
</td></tr></table><br /><br />
<br><br>
<?php echo "<table border=0 align='center' cellspacing=3 cellpadding=5><tr>
	<td bgcolor='eeeeff'>Usuario: <b>".$_SESSION['Nick']."</b></td>
	<td bgcolor='eeeeff'>Nombre: <b>".$_SESSION['Nombre']."</b></td>
	<td bgcolor='eeeeff'>Perfil de seguridad: <b>".$_SESSION['Ngrupo']."</b></td>
	</tr></table>"; ?>
<br />



 

<h4 align='center' style='font-size:12'><i>(Desarrollado en Aguila v.9 Julio 2012)</i><br />
Dise&ntilde;ado y desarrollado por Arturo Quintero Rodriguez. administracion@intercolombia.net</h4>
<?php
if($_SESSION['Disenador']==1)
echo "<hr>Base de datos: ".MYSQL_D."<hr>";
?>
</body>
</html>