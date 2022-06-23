<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ERROR | E_PARSE);

	
	function wh_log($msg)
	{
		$logfile = "Logs/log_".date('d-M-Y').".log";
		/*if(!is_dir($logfile))
		{
			mkdir($logfile, 0777, true);
		}*/
		file_put_contents($logfile, date('Y-m-d H:i:s').", ".$msg, FILE_APPEND);
	}


?>