<?php	
	include('inc/funciones_.php');
	html(); 
	echo "<body><h3>LIMPIEZA DE TEMPORALES DE INFORMES</h3> Tablas eliminadas: <br>"; 
	$TABLAS = qi("SHOW TABLES like 'tmpi_%'"); 
	while($T=mysqli_fetch_row($TABLAS)) {
		echo "<br>".$T[0]; qi("DROP TABLE ".$T[0]);
	}
	echo "<br><b>FIN DEL PROCESO</b></body>";
?>
