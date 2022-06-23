<?php

function file_put_contents($destino,$contenido)
{
	$Archivo=fopen($destino,"w");
	fwrite($Archivo,$contenido);
	fclose($Archivo);
}

function stripos($cadena,$pajar)
{
	return strpos(strtolower($cadena),strtolower($pajar));
}

function strripos($cadena,$pajar)
{
	return strrpos(strtolower($cadnea),strtolower($pajar));
}


?>
