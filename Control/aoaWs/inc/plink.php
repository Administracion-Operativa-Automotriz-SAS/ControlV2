<?php
if(!$PLINK=pg_connect("host=".PSQL_S." port=".PSQL_SP." dbname=".PSQL_D." user=".PSQL_U." password=".PSQL_P)) die('Problemas con la conexion de la base de datos!');
?>
