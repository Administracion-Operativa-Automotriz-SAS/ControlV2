<?PHP
	#die("  ESTE SOFTWARE ESTA EN MANTENIMIENTO... SE RESTABLECERA SU USO EN 10 MINUTOS ");
	session_start();
	if (!$_SESSION['User'] or !$_SESSION['Id_alterno'] or !$_SESSION['Nick'])
	{
		session_unset();session_destroy();
       echo "<script language='javascript' src='inc/js/aqrl.js'></script>
        <body onload='re_sesion()'></body></html>";
        die();
	}
	else
	{
	//	if($_SESSION['Nick']!='arturo.quintero') die ('<font color=red>En mantenimiento, pedimos excusas. Pronto estar� nuevamente en funcionamiento SAOD.<br> </font>Arturo Quintero.');
		//session_write_close();
		//session_start();
	}
?>
