<?php
if(!$LINK=mysql_connect(MYSQL_S,MYSQL_U,MYSQL_P)) die('Problemas con la conexion de la base de datos!');
mysql_query('SET collation_connection = utf8_general_ci',$LINK);
if(!mysql_select_db(MYSQL_D,$LINK)) die('Problemas con la seleccion de la base de datos');
mysql_query("set innodb_lock_wait_timeout=80",$LINK);
?>
