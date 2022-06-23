<?php

/**
 * Extracción de observaciones de siniestros por columnas
 *
 * @version $Id$
 * @copyright 2010
 */
function extraeobs($Dato)
{
	$Arreglo=explode("\n",$Dato);
	for($i=0;$i<count($Arreglo);$i++)
	{
		if(strpos($Arreglo[$i],']') && !strpos($Arreglo[$i],'] Consultó.'))
		{
		//	echo "<td>".$Arreglo[$i]."</td>";
			$Linea=substr($Arreglo[$i],strpos($Arreglo[$i],'[')+1);
			$Partes=explode(']',$Linea);
			if(strlen($Partes[0])>20) $Partes[0]=r($Partes[0],19);
			echo "<td>".$Partes[0]."</td><td>".$Partes[1]."</td>";
		}
	}

}


?>