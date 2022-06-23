<?php

include('inc/funciones_.php');
include_once('inc/Crypt.php');
html('ENCRIPCION DE AUTORIZACIONES');
echo "<body><h3>Encripción de Autorizaciones</h3>";
if($Ids=q("select id from sin_autor where estado!='E' and data='' and franquicia!=6 and franquicia!=5 and franquicia!=7 and franquicia!=8 and franquicia!=9 and franquicia!=10 
	and numero!='' and banco!=0 and franquicia!=0 and  vencimiento_mes!=0 and vencimiento_ano!=0  and codigo_seguridad!='' order by id limit 200"))
{
	include('inc/link.php');
	$Contador=0;
	while($Id=mysql_fetch_object($Ids))
	{
		$Contador++;
		echo " <b>[$Contador]</b> ";
		$id=$Id->id;
		$D=qom("select *,t_codigo_ach(banco) as nbanco from sin_autor where id=$id",$LINK);
		if($D->identificacion && $D->numero && $D->vencimiento_mes && $D->vencimiento_ano && $D->codigo_seguridad)
		{
			$Datos=$D->identificacion.'|'.$D->numero.'|'.$D->nbanco.'|'.$D->vencimiento_mes.'|'.$D->vencimiento_ano.'|'.$D->num_autorizacion.'|'.$D->funcionario.'|'.$D->codigo_seguridad;
			$C = new Crypt();
			$C->Mode = Crypt::MODE_HEX;
			$C->Key  = '!'.$D->id.'+';
			$Datose = $C->encrypt($Datos);
			if($Datose)
			{
				mysql_query("update sin_autor set data=\"$Datose\",numero='',banco='',vencimiento_mes='',vencimiento_ano='',num_autorizacion='',codigo_seguridad='' where id=$id",$LINK);
				echo "Encripcion satisfactoria registro: $id";
			}
			else
			{
				echo "<b style='color:ff0000'>Encripcion Fallida, debe verificar la informacion del registro</b> $id";
			}
		}
		else
			echo "<b style='color:ffff00'>No hay datos suficientes para encriptar.</b> Registro: $id. ";
	}
}
else
	echo "No se hizo ninguna encripcion.";
mysql_close($LINK);
if($volviendo==1)
echo "<script language='javascript'>parent.volver();</script>";
echo "</body>";

?>