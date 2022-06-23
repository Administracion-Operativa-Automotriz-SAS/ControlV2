<?php 

include('inc/funciones_.php');
sesion();
$USER=$_SESSION['User'];
$NUSUARIO=$_SESSION['Nombre'];
$BDA='aoacol_administra';
$NT_req=tu('requisicion','id');

if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');     die();}
echo "<head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.css'>
        <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
		<script type='text/javascript' src='https://code.jquery.com/jquery-3.4.1.js'></script>
        <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.js'></script>
		</head>
		";
function bateriaLlantas(){
	global $id;
	$qAccesorios = q("select * from  accesorios_vehiculos");
	$qItemMarcaLlanta = q("select * from marcas_accesorios where tipo_accesorio = 2");
	$qItemMarcaBatery = q("select * from marcas_accesorios where tipo_accesorio = 1");
	$qGetLlantas = q("select a.id,a.lote_serial,a.serie,a.ancho,a.perfil,a.diametro
	                 ,a.codigo_velocidad,a.descripcion,m.nombre  nombre_llantas,t.nombre tipo, a.fecha_postura
					from accesorios_vehiculos a
					inner join marcas_accesorios m on a.marca_id = m.id
					inner join tipo_accesorios t on a.tipo = t.id  where  vehiculo_id = $id  and a.tipo = 2 and a.estado != 2");
	
	$tipoAccesorios = q("select * from tipo_accesorios");				
	$contLlantas = q("select COUNT(*) total_llantas
					from accesorios_vehiculos a
					inner join marcas_accesorios m on a.marca_id = m.id
					inner join tipo_accesorios t on a.tipo = t.id  where  vehiculo_id = $id  and a.tipo = 2 and a.estado != 2");
	
	include('views/subviews/vista_llantas_bateria/vista_llantas_batery.php');
}

function Insertar_accesorios_ok(){
	
	global $tipo, $marca_id_llantas, $marca_id_bateria ,$serie, $ancho, $perfil, $diametro, 
	$codigo_velocidad, $lote_serial, $tipo_bateria, $amperaje, $voltaje, $descripcion_bateria, 
	$descripcion_llantas,$idVehiculo,$dateLlantas,$dateBatery, $contadorLlantas, $decision, $idLlantas;
	echo "<head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.css'>
        <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
		<script type='text/javascript' src='https://code.jquery.com/jquery-3.4.1.js'></script>
        <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.js'></script>
		</head>
		";
	if($tipo == 1){
		$sql = "INSERT INTO accesorios_vehiculos (tipo,marca_id,vehiculo_id,amperaje,tipo_bateria,voltaje,fecha_postura,descripcion) VALUES ('$tipo',$marca_id_bateria,$idVehiculo,'$amperaje','$tipo_bateria','$voltaje','$dateBatery','$descripcion_bateria');";
		q($sql);
		echo "
			<script>
		$(document).ready(function(){
                    Swal.fire({
                        title: 'Bateria insertada',
                        text: 'Que bien!',
                        type: 'success',
                    });
    var url = 'https://app.aoacolombia.com/Control/operativo/bateriaLlantasVehiculos.php?Acc=bateriaLlantas&TABLA=vehiculo&id=$idVehiculo';
     window.onload = function() {
    setTimeout(function() {
       window.location.href = url; 
    },3000);
}
});
		</script>
		";
		
	}else if($tipo == 2){
		if($contadorLlantas >= 6){
			echo "
			<script>
		$(document).ready(function(){
                    Swal.fire({
                        title: 'Son maximo 6 llantas',
                        text: 'Si quiere incluir otra llanta debes sustituir alguna',
                        type: 'error',
                    });
    var url = 'https://app.aoacolombia.com/Control/operativo/bateriaLlantasVehiculos.php?Acc=bateriaLlantas&TABLA=vehiculo&id=$idVehiculo&disp=1';
     window.onload = function() {
    setTimeout(function() {
       window.location.href = url; 
    },4000);
}
});
		</script>
		";
		exit();
			
		}
		
		$sql = "INSERT INTO accesorios_vehiculos (tipo,marca_id,vehiculo_id,lote_serial,serie,ancho,perfil,diametro,codigo_velocidad,fecha_postura,descripcion) VALUES ('$tipo',$marca_id_llantas,$idVehiculo,'$lote_serial','$serie','$ancho','$perfil','$diametro','$codigo_velocidad','$dateLlantas','$descripcion_llantas')";
		q($sql);
		echo "
		<script>
		$(document).ready(function(){
			Swal.fire({
                        title: 'Que bien',
                        text: 'Llanta insertada',
                        type: 'success',
                    });
			var url = 'https://app.aoacolombia.com/Control/operativo/bateriaLlantasVehiculos.php?Acc=bateriaLlantas&TABLA=vehiculo&id=$idVehiculo';
			window.onload = function() {
		    setTimeout(function() {
			window.location.href = url; 
			},2000);
		}
		});
		</script>";
		
	}else{
		
	}

}

function actualizar_llantas(){
	echo "<head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.css'>
        <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
		<script type='text/javascript' src='https://code.jquery.com/jquery-3.4.1.js'></script>
        <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.js'></script>
		</head>
		";
	/*Inabilitar llantas*/
	
		global $idLlantas, $id;
		
		
		$sql = "UPDATE accesorios_vehiculos SET estado = 2 WHERE vehiculo_id = $id AND tipo = 2 AND id = $idLlantas";
		q($sql);
		echo "
		<script>
		$(document).ready(function(){
			Swal.fire({
                        title: 'Que bien',
                        text: 'Llanta inabilitada ya puedes cambiarla',
                        type: 'success',
                    });
			var url = 'https://app.aoacolombia.com/Control/operativo/bateriaLlantasVehiculos.php?Acc=bateriaLlantas&TABLA=vehiculo&id=$id';
			window.onload = function() {
		    setTimeout(function() {
			window.location.href = url; 
			},2000);
		}
		});
		</script>";
}




?>