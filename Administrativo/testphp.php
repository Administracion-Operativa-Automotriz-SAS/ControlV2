<?php
//$service_url = 'http://app.aoacolombia.com/Administrativo/Controllers/PHPwebService.test.php';
// Get cURL resource
session_start();
print_r($_SESSION);
for($i=0;$i<25;$i++)
{
	$curl = curl_init();
	// Set some options - we are passing in a useragent too here
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'http://app.aoacolombia.com/Administrativo/Controllers/PHPwebService.test.php',
		CURLOPT_USERAGENT => 'Codular Sample cURL Request'
	));
	// Send the request & save response to $resp
	$resp = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);

	print_r($resp);
}
?>