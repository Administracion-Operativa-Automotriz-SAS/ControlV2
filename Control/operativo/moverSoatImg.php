<?php

	/* MODULO DE CALL CENTER VERSION 2
		FEBRERO 25 DE 2012
	*/

include('inc/funciones_.php');

function moverImg(){
	
	$sql = "SELECT id,caratula_soat_f FROM vehiculo WHERE caratula_soat_f !='' ORDER BY id DESC";
	$t = q($sql);
	
	while($x=mysql_fetch_object($t)){
		
		$sql = "SELECT id,caratula_soat_f FROM vehiculo WHERE id = $x->id";
		
		$consultaBusqueda = qo($sql);
		
		
		
		if($consultaBusqueda->caratula_soat_f){
			$sql = "UPDATE vehiculo SET tarjetar_f = '$consultaBusqueda->caratula_soat_f'  WHERE id = $consultaBusqueda->id";
			q($sql);
		}else{
			echo $consultaBusqueda->id."No foto<br>";
		}
		
	
	
	}
	
	
	
}

moverImg();

?>