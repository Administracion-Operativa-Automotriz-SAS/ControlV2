<?php
include('inc/funciones_.php');
$UB=qo("select * from ubicacion where id=$id");
echo "<script language='javascript'>
			function  carga()
			{
				document.forma.submit();
			}
			</script><body onload='carga()'>
			<form action='../aoa/zorden_servicio.php' method='post' target='_self' name='forma' id='forma'>
			<input type='hidden' name='descripcion' value=''>
			<input type='hidden' name='idub' value='$UB->id'>
			<input type='hidden' name='base' value='".MYSQL_D."'>
			</form>
			</body>";
?>