<?php
include('inc/funciones_.php');
q("set SQL_QUOTE_SHOW_CREATE=0");
require('inc/link.php');
$TABLAS = mysql_query("show tables like 'tmpi_%'",$LINK);
while($T=mysql_fetch_row($TABLAS)) mysql_query("drop table ".$T[0],$LINK);
$TABLAS = mysql_query("show tables",$LINK);
$Cantidad_tablas=mysql_num_rows($TABLAS);
$tt='';
while ($T=mysql_fetch_row($TABLAS)) $tt.=($tt?',':'').$TABLA[0];
mysql_query("repair table $tt extended",$LINK);
mysql_close($LINK);

$Archivo_destino=MYSQL_D."_".date('Ymd').".zip";
$Comando="mysqldump --host=".MYSQL_S." --user=".MYSQL_U." --password=".MYSQL_P." --compact --add-drop-table --extended-insert --default-character-set=latin1 --skip-set-charset --skip-comments --skip-quote-names ".MYSQL_D." | bzip2 > $Archivo_destino";
if(@file($Archivo_destino)) unlink($Archivo_destino);
system($Comando);
sleep(60);
if(enviar_mail2("administracion@intercolombia.com" /*de */,
			"AOA BACKUP" /* nombre remitente */,
			"ggonzalez61@gmail.com,administracion@intercolombia.com" /*destinatarios*/,
			"Backup Base de datos COLSEGUROS" /*subject */,
			"<BODY>Adjunto backup de base de datos: ".MYSQL_D."</BODY>" /* Contenido */,
			'Backup.7z',$Archivo_destino))
echo "Exito"; else echo "Falla";


?>