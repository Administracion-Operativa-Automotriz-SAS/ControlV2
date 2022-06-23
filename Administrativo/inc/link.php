<?php
if(!$LINK=mysql_connect(MYSQL_S,MYSQL_U,MYSQL_P)) die('Problemas con la conexion de la base de datos!');
if(!mysql_select_db(MYSQL_D,$LINK)) die('Problemas con la seleccion de la base de datos');
?>
