<?php
include_once('inc/funciones_.php');
sesion();

$sql = "SELECT ub.id FROM ubicacion ub
		WHERE ub.flota = 212
		ORDER BY ub.id DESC";

$consulta = q($sql);


while($iu = mysql_fetch_object($consulta)){
	
  $hoy = date("Y-m-d");
  
  $ultimaSql = "SELECT * FROM ubicacion  WHERE  id  = ".$iu->id." and  fecha_final = '$hoy' order by id desc limit 1";
  
  $ConsultaUltima = qo($ultimaSql);
  
  if($ConsultaUltima->id){
	  $sqlEstado = "SELECT * FROM  estado_vehiculo where id  = ".$ConsultaUltima->estado;
	  $consultaEstado = qo($sqlEstado);
	  $vehiculoSql = "SELECT * FROM vehiculo WHERE id = $ConsultaUltima->vehiculo";
	  $consultaVe =  qo($vehiculoSql);
	  
	  echo $consultaEstado->nombre."  ".$consultaVe->placa."<br>";
  }else{
	$ultimaSql2 = "SELECT * FROM ubicacion  WHERE  id  = ".$iu->id." and  fecha_final > '$hoy' order by id desc limit 1";
	
	$ConsultaUltimaDos = qo($ultimaSql2);
	
	if($ConsultaUltimaDos->id){
		$sqlEstado = "SELECT * FROM  estado_vehiculo where id = ".$ConsultaUltimaDos->estado;
		
	  $consultaEstado = qo($sqlEstado);
	  $vehiculoSql = "SELECT * FROM vehiculo WHERE id = $ConsultaUltimaDos->vehiculo";
	  $consultaVe =  qo($vehiculoSql);
	  
	echo $consultaEstado->nombre."  ".$consultaVe->placa."<br>";
	}
	
	
	
	
  }
   
   //$updateUbicacion = "UPDATE ubicacion SET fecha_final = '2020-07-07' WHERE id = ".$ConsultaUltima->id.";";

     //q($updateUbicacion);
//echo $updateUbicacion."<br>";
	
}



/*while($i = mysql_fetch_object($consulta)){
   
		$ultimaSql = "SELECT id FROM ubicacion  WHERE  vehiculo = ".$i->id." AND fecha_final = '2020-06-06' ORDER BY id DESC LIMIT 1";
		
		echo $ultimaSql."<br>";
		
		
	
		$ultimaUbicacion = qo($ultimaSql);
		
		if($ultimaUbicacion->id != null || $ultimaUbicacion->id != ""){
			
			$sqlUpdate = "UPDATE ubicacion SET fecha_final = '2020-06-11' WHERE id = ".$ultimaUbicacion->id." AND oficina IN(3)";
		    echo $sqlUpdate;
			//$actualizar = q($sqlUpdate);
		
		if($actualizar){
			echo "Actualizo";
			
		}else{
			
			echo "NoActualiza";
		}
		}
		
	 


}*/






?>