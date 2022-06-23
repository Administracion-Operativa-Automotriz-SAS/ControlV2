<?php
include('inc/funciones_.php');
function parseToXML($htmlStr)
{
$xmlStr=str_replace('<','&lt;',$htmlStr);
$xmlStr=str_replace('>','&gt;',$xmlStr);
$xmlStr=str_replace('"','&quot;',$xmlStr);
$xmlStr=str_replace("'",'&#39;',$xmlStr);
$xmlStr=str_replace("&",'&amp;',$xmlStr);
return $xmlStr;
}

$Marcas=q("select nombre as nombre, t_ciudad(ciudad) as nciudad,direccion,latitud,longitud from sede where latitud!=0 and longitud!=0");

header("Content-type: text/xml");
echo '<markers>';
while($M=mysql_fetch_object($Marcas))
{
		echo '<marker ';
  	echo 'name="' . parseToXML($M->nombre) . '" ';
  	echo 'address="' . parseToXML($M->direccion) . '" ';
	echo 'pais="' . parseToXML($M->nciudad) . '" ';
	echo 'zona="" ';
	echo 'web="" ';
  	echo 'lat="' . $M->latitud . '" ';
  	echo 'lng="' . $M->longitud . '" ';
  	echo 'type="bar" ';
  	echo '/>';
}
echo '</markers>';
?>