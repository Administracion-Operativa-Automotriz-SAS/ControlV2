<?php
include('inc/funciones_.php');
$inicial="6.0000,-74.0000";
echo "
	<html>
	<head>
		<title>CSA - MAPS</title>
		<script type='text/javascript' src='http://maps.googleapis.com/maps/api/js?key=AIzaSyBZCR10exw2_N5QOqCk16sQKFtsHHNAZX0'></script>
		<script type='text/javascript'>
		function pinta_mapa()
		{
			var settings = {
						center: new google.maps.LatLng($inicial),
						zoom: 6,
						mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			var map = new google.maps.Map(document.getElementById('map'),settings);
	";
	
	$Oficinas=q("select nombre , t_ciudad(ciudad) as nciudad,direccion,latitud,longitud from aoacol_administra.sede where latitud!=0 and longitud!=0");
	while($O=mysql_fetch_object($Oficinas)) 
	{
			echo "
			var companyImage = new google.maps.MarkerImage('img/miniaoa6.png',
					new google.maps.Size(17,30),
					new google.maps.Point(0,0),
					new google.maps.Point(8,30));

			var companyShadow = new google.maps.MarkerImage('img/miniaoa6sombra2.png',
					new google.maps.Size(50,20),
					new google.maps.Point(0,0),
					new google.maps.Point(25,20));

			var companyPos = new google.maps.LatLng( $O->latitud , $O->longitud );

			var companyMarker = new google.maps.Marker({
					position: companyPos,
					map: map,
					icon: companyImage,
					shadow: companyShadow,
					title:'$O->nombre',
					zIndex: 4});
			";
		}
echo "		
	}

  </script>

  </head>

  <body onload='pinta_mapa()' ><center>
    <div id='map' style='width: 800px; height: 600px'></div></center>
  </body>
</html>";
?>