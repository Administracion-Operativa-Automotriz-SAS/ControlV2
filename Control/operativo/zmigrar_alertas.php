<?php

/*PROGRAMA PARA MIGRACION DE ALERTAS METODO ANTERIOR AL NUEVO */
include('inc/funciones_.php');

html();
echo "<body><h3>MIGRACION DE ALERTAS</h3>";
$Vehiculos=q("select v.*,mv.manten_cada from vehiculo v,linea_vehiculo lv,marca_vehiculo mv where lv.id=v.linea and lv.marca=mv.id and v.inactivo_desde='0000-00-00' order by placa");
echo "<table>";
$Arreglo_novedades=tabla2arreglo('novedad_vehiculo',array('codigo','id'));
$Arreglo_alerta=tabla2arreglo('tipo_alerta',array('novedad_operativa','id'));
$Arreglo_ta=tabla2arreglo('tipo_alerta',array('id','control'));
//print_r($Arreglo_novedades);
include('inc/link.php');

while($V=mysql_fetch_object($Vehiculos))
{
	echo "<tr><td>$V->placa</td>";
	$Novedades=mysql_query("select distinct novedad from hv_vehiculo where placa='$V->placa' ",$LINK);
	while($Nov=mysql_fetch_object($Novedades))
	{
		echo "<td>$Nov->novedad</td>";
		$Ultimo_dato=qom("select * from hv_vehiculo where placa='$V->placa' and novedad='$Nov->novedad' order by fecha desc",$LINK);
		echo "<td>$Ultimo_dato->kilometraje $Ultimo_dato->fecha</td>";
		$Codigo_alerta=$Arreglo_alerta[$Arreglo_novedades[$Nov->novedad]];
		echo "<td bgcolor='ddddff'>- $Codigo_alerta -</td>";
		if($Codigo_alerta)
		{
			if($Arreglo_ta[$Codigo_alerta]=='T')
			{
				/// supone que el control es cada 365 dias
				mysql_query("insert ignore into cfg_alerta_vehiculo(vehiculo,alerta,dias_amarillo,dias_rojo,ultimo_fecha) 
					values ('$V->id','$Codigo_alerta',335,365,'$Ultimo_dato->fecha')",$LINK);
			}
			else
			{
				/// obtiene el kilometraje de control de acuerdo a la novedad
				if($Codigo_alerta==1) // mantenimientos
				{
					$amarillo=$V->manten_cada-1000;
					$rojo=$V->manten_cada;
					mysql_query("insert ignore into cfg_alerta_vehiculo(vehiculo,alerta,kilo_amarillo,kilo_rojo,ultimo_kilometraje) 
					values ('$V->id','$Codigo_alerta',$amarillo,$rojo,'$Ultimo_dato->kilometraje')",$LINK);
				}
				elseif($Codigo_alerta==4) // revision frenos
				{
					$amarillo=19000;$rojo=20000;
					mysql_query("insert ignore into cfg_alerta_vehiculo(vehiculo,alerta,kilo_amarillo,kilo_rojo,ultimo_kilometraje) 
					values ('$V->id','$Codigo_alerta',$amarillo,$rojo,'$Ultimo_dato->kilometraje')",$LINK);
				}
			}
		}
	}
	echo "</tr>";
}
mysql_close($LINK);
echo "</table></body>";
?>