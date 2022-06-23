<?php
require_once('../config/config.php');
$Cm=$C.'_mime';
if(!$Link_blob = mysql_connect(MYSQL_S, MYSQL_U, MYSQL_P)) die('Problemas con la conexion de la base de datos!');
if(!mysql_select_db(MYSQL_D, $Link_blob)) die('Problemas con la seleccion de la base de datos');
if(!$Reg_blob=mysql_query("select $C as img, $Cm as mime from $T where id=$Id",$Link_blob)) die(mysql_error());
mysql_close($Link_blob);
if($Img_blob=mysql_fetch_object($Reg_blob))
{
	header("Content-Type: $Img_blob->mime");
	echo $Img_blob->img;
}
else
{
	echo "No encuentro el registro";
}
?>