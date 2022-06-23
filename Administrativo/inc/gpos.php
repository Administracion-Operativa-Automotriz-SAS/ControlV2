<?PHP
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
