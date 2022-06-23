<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include_once(dirname(__FILE__).'/../config/config.php');
	include_once(dirname(__FILE__).'/../config/resuelve.php');
	
	 
	
	
	
	$request_body = file_get_contents('php://input');
	if($request_body)
	{
		$request = json_decode($request_body);
		if($request->method == "echo")
		{
			ini_set('max_execution_time', 60);
			$flag = false;
			$i = 0;			
			do {
				
				$flag = check_existance($request->last_packet);
				
			} while ($flag==false);
			
			if($flag)
			{
				echo json_encode(array("status"=>1,"data"=>$flag));
			}
			else
			{
				echo json_encode(array("status"=>2));
			}
			
		}
	}
	
	function check_existance($last_packet)
	{
		$Camino = '/var/www/html/public_html/Administrativo/INodes/settings.txt';	
		$Settings = file_get_contents($Camino);
		
		if(($Settings!=null or $Settings!="") and $Settings!=$last_packet)
		{
			return $Settings;
			
		}
		else
		{			
			return false;			
		}
	}
	
	
	if($_REQUEST)
	{
			
		

		$settings = json_encode($_REQUEST);
		
		$Camino = '/var/www/html/public_html/Administrativo/INodes/settings.txt';
		
		file_put_contents($Camino, $settings);

		//$request = json_encode($_REQUEST);	
		//setcookie("echo","hola",time()+45);

		/*$ws = new WsController();
		$array = array("data"=>json_encode($_REQUEST));
		$sql = $ws->insert("nodes",$array);
		$ws->query($sql);
		echo $sql;
		echo "<br>";
		echo "<br>";*/		
		/*echo "request";
		echo "<br>";
		echo "<br>";
		echo json_encode($_REQUEST);
		echo "<br>";
		echo "<br>";
		echo "server";
		echo "<br>";
		echo "<br>";
		echo json_encode($_SERVER);
		echo "<br>";
		echo "<br>";
		echo "method";
		$METHOD = $_SERVER["REQUEST_METHOD"];
		$string_method = "_".$METHOD;
		echo json_encode($$string_method);*/
	}
	
	
	/*$cookie_name = "long_name";
	$cookie_value = "test".time();
	setcookie($cookie_name, $cookie_value, time() + 60, "/");*/ 
	
	/*if(isset($_COOKIE[$cookie_name])) {
		echo "<br>";
		echo $_COOKIE[$cookie_name];
	}*/
	

	

?>
