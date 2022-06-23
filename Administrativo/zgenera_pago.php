<?php
include('inc/funciones_.php');
$Datos=q("select * from $T where pr_id=$Pr order by proveedor,concepto,oficina");
html('GENERACION AUTOMATICA DE PAGOS');
echo "<body onload='centrar(700,700)'>";
$Aprov='';
require('inc/link.php');
while($D=mysql_fetch_object($Datos))
{
	if($D->proveedor!=$Aprov)
	{
		$Aprov=$D->proveedor;
		echo "<br><br><b>Insertando $D->proveedor </b>";
		if(!mysql_query("insert into pago (fecha) values ('$D->fec_pago')",$LINK)) die(mysql_error($LINK));
		$IDN=mysql_insert_id($LINK);
	}
	echo "<li>$D->proveedor $D->fac_numero $D->descripcion ";
	mysql_query("insert into dpago (pago,aprobacion,factura,proveedor,valor) values 
		('$IDN','$D->ap_id','$D->fac_id','$D->pr_id','$D->aprobado')",$LINK);
	if($Suma=mysql_query("select sum(valor) as total from dpago where pago=$IDN",$LINK))
	{
		$Sum=mysql_fetch_object($Suma);mysql_query("update pago set valor=$Sum->total where id=$IDN",$LINK);
	}
	
	mysql_query("update factura_ap set girado=1 where id=$D->ap_id",$LINK);
}

mysql_close($LINK);
echo "<script language='javascript'>
opener.document.getElementById('l_$Pr').style.visibility='hidden';
alert('La información fue insertada en la tabla de pagos');
window.close();void(null);
</script>
</body>"
?>