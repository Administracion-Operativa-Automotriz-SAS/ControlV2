<?php
/* INCLUIDO EN EL INICIAL DEL REPORTE NUMERO 92 */

echo "<b>RESULTADOS DE ENCUESTA</b>";
echo "Fecha inicial: ".pinta_FC('forma','FI');
echo " Fecha Final: ".pinta_FC('forma','FF');

if($_SESSION['User']==8) $ASEG=qo1("select aseguradora from usuario_aseguradora where id=".$_SESSION['Id_alterno']);
elseif(inlist($_SESSION['User'],'11')) $ASEG=qo1("select aseguradora from usuario_aseguradora1 where id=".$_SESSION['Id_alterno']);
elseif($_SESSION['User']==29) $ASEG=qo1("select aseguradora from usuario_aseguradora2 where id=".$_SESSION['Id_alterno']);
elseif($_SESSION['User']==36) $ASEG='1,8,9';
else 
{
	echo "<script language='javascript'>
		function vl_aseg(d1,d2)
		{
			var Cadena=document.forma.ASEG.value;
			if(d1)
			{
				Cadena+=(Cadena?',':'')+d2;
			}
			else
			{
				
				if(Cadena.indexOf(','+d2+',')>-1) Cadena=Cadena.replace(','+d2+',',',');
				else 
				{
					if(Cadena.indexOf(d2+',') == 0) Cadena=Cadena.replace(d2+',','');
					else
					{
						if(Cadena.lastIndexOf(','+d2)>-1) Cadena=Cadena.replace(','+d2,'');
					}
				}
			}
			document.forma.ASEG.value=Cadena;
		}
	</script>";
	$Aseguradoras=q("select id,nombre from aseguradora where id!=6");
	echo "<br><br>Seleccione la(s) aseguradora(s): 
		<div style='height:300px;overflow:auto;'><table>";
	while($a=mysql_fetch_object($Aseguradoras))
	{
		echo "<tr><td>$a->nombre</td><td><input type='checkbox' onclick='vl_aseg(this.checked,$a->id);'></td></tr>";
	}
	echo "</table></div>
		<input type='hidden' name='ASEG' value=''>";
	
}
if(inlist($_SESSION['User'],'8,11,29')) echo "<input type='hidden' name='ASEG' value='".$ASEG."'>";


?>