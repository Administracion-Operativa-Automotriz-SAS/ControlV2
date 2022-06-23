<?php
$GET='';
foreach($_GET as $_post_Campo => $_valor_Campo) 
{
	$GET.="&$_post_Campo=$_valor_Campo";
}
header("location:reportes.php?Acc=ejecutar&ID=$ID".$GET)
?>
