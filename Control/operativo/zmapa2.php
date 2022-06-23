<?php
echo "<head>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<link rel='stylesheet' href='http://cdn.leafletjs.com/leaflet-0.5/leaflet.css' />
 <!--[if lte IE 8]><link rel='stylesheet' href='http://cdn.leafletjs.com/leaflet-0.5/leaflet.ie.css' /><![endif]-->
 </head> 

<body>
<div id='map' style='width:600px;height:400px;></div>

<script src='http://cdn.leafletjs.com/leaflet-0.5/leaflet.js'></script>
<script language='javascript'>
var map = L.map('map').setView([51.505, -0.09], 13);

L.tileLayer('http://{s}.tile.cloudmade.com/{key}/{styleId}/256/{z}/{x}/{y}.png', 
{	key: 'API-key',
	styleID:997,
    attribution: 'Map data &copy; ',
    maxZoom: 18
}).addTo(map);

var marker = L.marker([51.5, -0.09]).addTo(map);

var circle = L.circle([51.508, -0.11], 500, {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5
}).addTo(map);

marker.bindPopup('<b>Hello world!</b><br>I am a popup.').openPopup();
circle.bindPopup('I am a circle.');

var popup = L.popup()
    .setLatLng([51.5, -0.09])
    .setContent('I am a standalone popup.')
    .openOn(map);
	
	
function onMapClick(e) {
    alert('You clicked the map at ' + e.latlng);
}

map.on('click', onMapClick);	
	
</script>


</body>
 ";


?>