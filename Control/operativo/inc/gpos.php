<?PHP
	// foreach($_POST as $_post_Campo => $_valor_Campo)
	// {
		// eval("global \$".$_post_Campo.";");
		// eval("\$".$_post_Campo."=\"".addslashes($_valor_Campo)."\";");
	// }
	// foreach($_GET as $_post_Campo => $_valor_Campo)
	// {
		// eval("global \$".$_post_Campo.";");
		// eval("\$".$_post_Campo."=\"".$_valor_Campo."\";");
	// }
	// if(isset($_SESSION['User']))
	// {
		// $User=$_SESSION['User'];
		// $Id_alterno=$_SESSION['Id_alterno'];
		// $Nombre=$_SESSION['Nombre'];
		// $Disenador=$_SESSION['Disenador'];
		// $Ver_prog=$_SESSION['Ver_prog'];
		// $Nick=$_SESSION['Nick'];
	// }
	foreach($_POST as $_post_Campo => $_valor_Campo)
	{
		global $$_post_Campo;
		$$_post_Campo=$_POST[$_post_Campo];
	}
	foreach($_GET as $_get_Campo => $_valor_Campo)
	{
		global $$_get_Campo;
		$$_get_Campo=$_GET[$_get_Campo];
	}
?>
