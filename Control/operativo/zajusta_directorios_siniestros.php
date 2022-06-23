<?php

/**
 *  UTILIDAD PARA crear directorios de 1000 subdirectorios para imágenes de cantidades de registros mayores a 5000
 *
 * @version $Id$
 * @copyright 2010
 */

include('inc/funciones_.php');
html();
if($id)
{
	mover_nuevo_directorio($id);
//	die();
	$RR=q("select id from siniestro where id>$id order by id limit 500");
//	$RR=q("select id from siniestro where id=$id ");
	while($rr=mysql_fetch_object($RR))
	{
		mover_nuevo_directorio($rr->id);
	}
}

function mover_nuevo_directorio($id)
{
	echo "<br><br><b>Registro: $id</b><br>";
	$D=qo("select * from siniestro where id=$id");
	$Columnas=q("show columns from siniestro");
	$Movido=false;
	while($Campo = mysql_fetch_row($Columnas))
	{
		$campo=$Campo[0];
		if(r($campo,2)=='_f')
		{
			eval("\$Contenido=\$D->$campo;");
			if($Contenido)
			{
				$Cadena_verificacion='/'.substr(str_pad($id,6,'0',STR_PAD_LEFT),0,3);
				$Cadena_verificacion2='/000';
				if(strpos($Contenido,$Cadena_verificacion.'/')>0)
				{
					if(!is_file($Contenido))
					{
						$Veces=1;
						$Definicion=qo("select rutaimg from siniestro_t where campo='$campo' ");
						echo "<br />Procesando $campo: $Definicion->rutaimg";
						$Nuevo_directorio=$Definicion->rutaimg.$Cadena_verificacion;

						if(!is_dir($Nuevo_directorio))
						{
							mkdir($Nuevo_directorio);
							chmod($Nuevo_directorio,0755);
						}
						$Antigua_ruta=$Definicion->rutaimg.'/000/'.$id;
						$Nueva_ruta=$Nuevo_directorio.'/'.$id;

						if(!is_dir($Nueva_ruta))
						{
							echo "<br /> <font color='blue'>moviendo $Antigua_ruta a $Nueva_ruta</font>";
							rename($Antigua_ruta,$Nueva_ruta);
						}
						else
						{
							$Antigua_ruta=$Contenido;
							$Nueva_ruta=str_replace($Definicion->rutaimg.'/000/',$Nuevo_directorio.'/',$Contenido,&$Veces);
							if(is_file($Antigua_ruta)) rename($Antigua_ruta,$Nueva_ruta);
						}
						q("update siniestro set $campo='".str_replace($Definicion->rutaimg.'/000/',$Nuevo_directorio.'/',$Contenido,&$Veces)."' where id=$id");
					}
					echo "<br /><font color='green'>$campo ya reubicado. </font>";
				}
				elseif(strpos($Contenido,$Cadena_verificacion2.'/')>0)
				{
					$Veces=1;
					$Definicion=qo("select rutaimg from siniestro_t where campo='$campo' ");
					echo "<br />Procesando $campo: $Definicion->rutaimg";
					$Nuevo_directorio=$Definicion->rutaimg.$Cadena_verificacion;

					if(!is_dir($Nuevo_directorio))
					{
						mkdir($Nuevo_directorio);
						chmod($Nuevo_directorio,0755);
					}
					$Antigua_ruta=$Definicion->rutaimg.'/000/'.$id;
					$Nueva_ruta=$Nuevo_directorio.'/'.$id;

					if(!is_dir($Nueva_ruta))
					{
						echo "<br /> <font color='blue'>moviendo $Antigua_ruta a $Nueva_ruta</font>";
						rename($Antigua_ruta,$Nueva_ruta);
					}
					else
					{
						$Antigua_ruta=$Contenido;
						$Nueva_ruta=str_replace($Definicion->rutaimg.'/000/',$Nuevo_directorio.'/',$Contenido,&$Veces);
						if(is_file($Antigua_ruta)) rename($Antigua_ruta,$Nueva_ruta);
					}
					q("update siniestro set $campo='".str_replace($Definicion->rutaimg.'/000/',$Nuevo_directorio.'/',$Contenido,&$Veces)."' where id=$id");
				}
				else
				{
					$Veces=1;
					$Definicion=qo("select rutaimg from siniestro_t where campo='$campo' ");
					echo "<br />Procesando $campo: $Definicion->rutaimg";
					$Nuevo_directorio=$Definicion->rutaimg.$Cadena_verificacion;

					if(!is_dir($Nuevo_directorio))
					{
						mkdir($Nuevo_directorio);
						chmod($Nuevo_directorio,0755);
					}
					$Antigua_ruta=$Definicion->rutaimg.'/'.$id;
					$Nueva_ruta=$Nuevo_directorio.'/'.$id;

					if(!is_dir($Nueva_ruta))
					{
						echo "<br /> <font color='blue'>moviendo $Antigua_ruta a $Nueva_ruta</font>";
						rename($Antigua_ruta,$Nueva_ruta);
					}
					else
					{
						$Antigua_ruta=$Contenido;
						$Nueva_ruta=str_replace($Definicion->rutaimg.'/',$Nuevo_directorio.'/',$Contenido,&$Veces);
						if(is_file($Antigua_ruta)) rename($Antigua_ruta,$Nueva_ruta);
					}
					q("update siniestro set $campo='".str_replace($Definicion->rutaimg.'/',$Nuevo_directorio.'/',$Contenido,&$Veces)."' where id=$id");
				}
			}
		}
	}
}

?>