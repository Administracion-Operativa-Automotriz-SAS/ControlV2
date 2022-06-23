<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE | E_NOTICE);

	$version = curl_version();
	$ssl_supported= ($version['features'] & CURL_VERSION_SSL);
	
	
	print_r($version);
	
	
	//print_r($ssl_supported);

	$v = curl_version(); 
	print $v['ssl_version'];

?>