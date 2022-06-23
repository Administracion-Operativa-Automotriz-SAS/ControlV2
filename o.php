<?php

echo"edaea";
$ruta =  $_POST["x_ruta"]; 
$observaciones =  $_POST["x_observaciones"];  
$Instruccion=base64_decode($ruta);
$Ruta_aprobacion="$Instruccion&observaciones=$observaciones";
//header("location:$Ruta_aprobacion");

	
	
	
?>