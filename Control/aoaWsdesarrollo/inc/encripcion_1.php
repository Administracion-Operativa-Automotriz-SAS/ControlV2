<?php

function e($texto) 
{ 
	if($texto=="") 
		return ""; 
	else 
		return crypt($texto,l($texto,2)); 
} 
	
function ev($texto)		// encripcion de una variable para enviar con metodo post
{ 
	if($texto=="") return ""; 
	else 
	{
		$r1=crypt($texto,r($texto,2));
		$r2=str_replace(".","_",$r1);
		$r2=str_replace("/","_",$r2);
		$r2=str_replace("\n","_",$r2);
		$r2=str_replace("\r","_",$r2);
		return $r2;
	}
}
?>