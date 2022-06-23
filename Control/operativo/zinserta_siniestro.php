<?php
/*  ACTUALIZACION DE SINIESTROS
 * el objetivo es que cada vez que se actualice un siniestro, se cambie el estado o cualquier novedad automáticamente en la base de datos de AOA-cars
 */
/// BUSQUEDA DEL REGISTRO EN AOACARS
include('inc/funciones_.php');
include_once('zcontrol_cfg.php');
html();
$R=qo("select * from siniestro where id=$id");
$JSM='';
if(!$LINK2=mysql_connect(MYSQL_S,resuelve_usuario_mysql(),MYSQL_P)) $JSM='Problemas con la conexion de la base de datos AOA-cars!';
elseif(!mysql_select_db(MYSQL_D2,$LINK2)) $JSM='Problemas con la seleccion de la base de datos';

if(!mysql_query("insert ignore into siniestro (numero,aseguradora) values ('$R->numero',$Numero_Aseguradora)",$LINK2))
	$JSM='No se pudo insertar el registro en AOA cars: '.mysql_error($LINK2);
else
{
	mysql_close($LINK2);
	$JSM='El registro se inserto satisfactoriamente en AOA - cars.';
	$SALIDA_INSERCION=true;
	require_once('zactualiza_siniestro2.php');
	echo "<body><script language='javascript'>window.close();void(null);</script></body>";
}
echo "<body><script language='javascript'>centrar(10,10);alert('$JSM');window.close();void(null);</script>
</body>";
?>