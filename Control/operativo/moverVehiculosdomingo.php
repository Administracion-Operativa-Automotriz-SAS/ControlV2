<?php
include_once('inc/funciones_.php');
sesion();

$sql = "SELECT ve.id AS idVh FROM vehiculo ve WHERE ve.inactivo_desde = '0000-00-00'";

$consulta = q($sql);


while($iu = mysql_fetch_object($consulta)){
	
	
	$domingo = date("Y-m-d");
	
	$sabado = date("Y-m-d",strtotime($domingo."- 1 days"));
	
	
	
	
	$sqlUbicacion = "SELECT id as idUbi FROM ubicacion WHERE vehiculo = ".$iu->idVh." AND fecha_final >= '$sabado' ORDER BY id DESC LIMIT 1";
	$t = q($sqlUbicacion);
   
   
   while($u = mysql_fetch_object($t)){
	   
	   $sqlFilDos = "SELECT id idUbiCa FROM  ubicacion WHERE id = '$u->idUbi' AND fecha_inicial = '$sabado' AND fecha_final > '$sabado' ORDER BY idUbiCa DESC LIMIT 1";
	   $fTwo = qo($sqlFilDos);
	   
	   $sqlFilTres = "SELECT id idUbiCaTres FROM  ubicacion WHERE id = '$u->idUbi' AND fecha_final = '$sabado' ORDER BY idUbiCaTres DESC LIMIT 1";
	   $fTre = qo($sqlFilTres);
	   
	   
	   
	   if($fTwo){
		   echo $fTwo->idUbiCa."No hacer nada"."<br>";
	   }else{
		  if($fTre){
			   $updateUbicacion = "UPDATE ubicacion SET fecha_final = '$domingo' WHERE id = ".$fTre->idUbiCaTres;
			  q($updateUbicacion);
		  }
		
		}
	   
	}
   
	
}

 ?>