<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	$Camino = '/var/www/html/public_html/Administrativo/INodes/settings.txt';
	
	$texstSettings = file_get_contents($Camino);
	echo $texstSettings;
	$settings = json_decode($texstSettings);
	
	//print_r($settings);
 ?>