<?php

	include('inc/funciones_.php');
	$direccion="4.646236,-74.083557";
	if(!$Fecha) $Fecha=date('Y-m-d');
	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
			<html xmlns='http://www.w3.org/1999/xhtml'>
			<head>
				<meta http-equiv='content-type' content='text/html; charset=utf-8'/>
				<title>CSA - MAPS</title>
				<script type='text/javascript' src='https://maps.google.com/maps/api/js?sensor=false'></script>
				<script type='text/javascript'>
					function load()
					{
						var latlng = new google.maps.LatLng($direccion);
						var myOptions = {zoom:11,center: latlng,mapTypeId: google.maps.MapTypeId.ROADMAP};
						var map = new google.maps.Map(document.getElementById('map'),myOptions);
						setMarkers(map, beaches);
					}
					var beaches = [ ";
	$Posicion=q("select * from gp where date_format(fecha,'%Y-%m-%d')='$Fecha' order by id desc ");
	$res='';
		while($C=mysql_fetch_object($Posicion))
		{
			$res.=($res?',':'')."['$C->fecha',$C->lat,$C->lon,4]";
		}
		echo $res;
	echo "];
					
					function setMarkers(map, locations) 
					{
						var image = new google.maps.MarkerImage('http://app.aoacolombia.com/img/miniaoa3.png',
											new google.maps.Size(15, 20),
											new google.maps.Point(0,0),
											new google.maps.Point(0,8));
											var shape = { coord: [1, 1, 1, 20, 15, 20, 15 , 1], type: 'poly' };
						for (var i = 0; i < locations.length; i++) 
						{
							var beach = locations[i];
							var myLatLng = new google.maps.LatLng(beach[1], beach[2]);
							var marker = new google.maps.Marker({ position: myLatLng,map: map,icon: image,shape: shape,title: beach[0],zIndex: beach[3] });
						}
					} 
				</script>
			</head>
			<body onload='load()'>
			<form action='zmapa1.php' target='_self' method='POST' name='forma' id='forma'>
				Fecha: ".menu1('Fecha',"select distinct date_format(fecha,'%Y-%m-%d'),date_format(fecha,'%Y-%m-%d') from gp order by fecha desc",$Fecha)."
			<input type='submit' name='continuar' id='continuar' value='VER'>
			</form>
			<center><div id='map' style='width:100%; height: 600px'></div></center>
		</body>
		</html>";
	
	?>
	