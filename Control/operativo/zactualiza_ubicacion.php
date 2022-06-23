<?php
/*  ACTUALIZACION DE SINIESTROS
 * el objetivo es que cada vez que se actualice un siniestro, se cambie el estado o cualquier novedad automáticamente en la base de datos de AOA-cars
 */
/// BUSQUEDA DEL REGISTRO EN AOACARS

if($R->estado==1 || $R->estado=7)
{
  if($Ub=qo1("select id from siniestro where ubicacion=$R->id"))
  {
    q("update siniestro set fecha_inicial='$R->fecha_inicial',fecha_final='$R->fecha_final' where id=$Ub");
  }
}
q("update ubicacion set odometro_inicial=if(odometro_inicial<$R->odometro_final,$R->odometro_final,odometro_inicial), odometro_final=if(odometro_final<$R->odometro_final,$R->odometro_final,odometro_final),
	odometro_diferencia=odometro_final-odometro_inicial where vehiculo='$R->vehiculo' and fecha_inicial>='$R->fecha_final' and id!=$R->id ");

?>