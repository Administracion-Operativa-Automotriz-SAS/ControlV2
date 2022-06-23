<?php
include('inc/funciones_.php');

if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}

html();
echo "<body onload='centrar(600,600)'>";
echo "<FORM action='zsini.php' method='post' target='_self' name='forma' id='forma'>
<h3><b>Señor Usuario copie el texto completo del email y peguelo a continuación:</b></h3><br>
<center><TEXTAREA ROWS='25' COLS='100'  ID='texto' name='texto' style='font-family:arial;font-size:9;'></textarea>
<br><BR>
<input type='submit' value='ANALIZAR EL TEXTO' style='width:200;height:30'>
<input type='hidden' name='Acc' value='procesa'></center>
</form><body>";


function procesa()
{
	$Texto=str_replace(chr(10),chr(13),$_POST['texto']);
	$Texto=str_replace(chr(9),chr(32),$Texto);
	$Arreglo=split(chr(13),$Texto);
	$j=0;
	$inicia=false;
	$DATOS=array();
	while($j<count($Arreglo))
	{
		$Texto=$Arreglo[$j];
		$Longitud=strlen($Texto);
		if(strpos(' '.$Texto,'AVISO'))
		{
			$inicia=true;$j++;continue;
		}
		if($inicia && $Longitud>0)
		{
			if(!$DATOS['ciudad'])
			{
				if($p=strpos($Texto,', '))
				{

					$DATOS['ciudad']=substr($Texto,0,$p);
					$Texto=substr($Texto,$p+5);
					$DATOS['fecha']=$Texto;
					$j++;continue;
				}
			}
			if(!$DATOS['numero'])
			{
				if($p=strpos($Texto,' Declaración'))
				{
					$Texto=substr($Texto,$p+14);
					$DATOS['numero']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,' Declaración')+14);
					$DATOS['fecha_declaracion']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,' Siniestro')+12);
					$DATOS['fecha_siniestro']=substr($Texto,0,strpos($Texto,' a las'));
					$j++;continue;
				}
			}
			if(!$DATOS['poliza'])
			{
				if(strpos(' '.$Texto,'Póliza:') && strpos(' '.$Texto,'Plan:'))
				{
					$Texto=substr($Texto,strpos($Texto,'a:')+2);
					$DATOS['poliza']=substr($Texto,0,strpos($Texto,'     '));
					$Texto=substr($Texto,strpos($Texto,'Radicadora:')+12);
					$DATOS['sucursal']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'diario:')+8);
					$DATOS['intermediario']=$Texto;
					$j++;continue;
				}
				else
				{$j++;continue;}
			}
			if(!$DATOS['vig_poliza'])
			{
				if(strpos($Texto,'Vigencia:'))
				{
					$Texto=substr($Texto,strpos($Texto,'Vigencia:')+10);
					$DATOS['vig_poliza']=substr($Texto,0,strpos($Texto,' A '));
					$Texto=substr($Texto,strpos($Texto,' A ')+3);
					$DATOS['vigh_poliza']=$Texto;
					$j++;continue;
				}
			}
			if(!$DATOS['nombre_asegurado'])
			{
				if(strpos(' '.$Texto,'Nombre:'))
				{
					$Texto=substr($Texto,8);
					$DATOS['nombre_asegurado']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'CC ó NIT:')+10);
					$DATOS['iden_asegurado']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Dirección:')+11);
					$DATOS['dir_asegurado']=$Texto;
					$j++;continue;
				}
				else
				{$j++;continue;}
			}
			if(!$DATOS['placa'])
			{
				if(strpos(' '.$Texto,'Placa:'))
				{
					$Texto=substr($Texto,7);
					$DATOS['placa']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Marca:')+7);
					$DATOS['marca']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Tipo:')+7);
					$DATOS['tipo']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Línea:')+7);
					$DATOS['linea']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Modelo:')+8);
					$DATOS['modelo']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Clase:')+7);
					$DATOS['clase']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Color:')+7);
					$DATOS['color']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Servicio:')+10);
					$DATOS['servicio']=substr($Texto,0,strpos($Texto,'    '));
					$j++;continue;
				}
				else
				{$j++;continue;}
			}
			if(!$DATOS['nombre_declarante'])
			{
				if(strpos(' '.$Texto,'Nombre:'))
				{
					$Texto=substr($Texto,8);
					$DATOS['nombre_declarante']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'CC:')+4);
					$DATOS['iden_declarante']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Teléfono:')+10);
					$DATOS['telefono_declarante']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Dirección:')+11);
					$DATOS['dir_declarante']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Ciudad:')+8);
					$DATOS['ciudad_declarante']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Residencia:')+12);
					$DATOS['telresid_declarante']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Oficina:')+9);
					$DATOS['telofic_declarante']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Celular:')+9);
					$DATOS['celular_declarante']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Otro:')+6);
					$DATOS['otrotel_declarante']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Correo electrónico:')+20);
					$DATOS['email_declarante']=substr($Texto,0,strpos($Texto,'    '));
					$j++;continue;
				}
				else
				{$j++;continue;}
			}
			if(!$DATOS['nombre_conductor'])
			{
				if(strpos(' '.$Texto,'Nombre:'))
				{
					$Texto=substr($Texto,8);
					$DATOS['nombre_conductor']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Residencia:')+12);
					$DATOS['telresid_conductor']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Oficina:')+9);
					$DATOS['telofic_conductor']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Celular:')+9);
					$DATOS['celular_conductor']=substr($Texto,0,strpos($Texto,'    '));
					$Texto=substr($Texto,strpos($Texto,'Dirección Residencia:')+22);
					$DATOS['dir_conductor']=substr($Texto,0,strpos($Texto,'    '));
					$j++;continue;
				}
				else
				{$j++;continue;}
			}
			else
			{
				break;
			}
			echo "$j:$Texto [$Longitud]<br>";
			for($i=0;$i<strlen($Texto);$i++)
			{
				$Caracter=substr($Texto,$i,1);
				$Codigo=ord($Caracter);
				echo "$Caracter = $Codigo<br>";
			}
		}
		$j++;
	}
	$Mensaje='';

	$DATOS['fecha']=ajusta_fecha($DATOS['fecha']);
	$DATOS['fecha_declaracion']=ajusta_fecha($DATOS['fecha_declaracion']);
	$DATOS['fecha_siniestro']=ajusta_fecha($DATOS['fecha_siniestro']);
	$DATOS['vig_poliza']=ajusta_fecha($DATOS['vig_poliza']);
	$DATOS['vigh_poliza']=ajusta_fecha($DATOS['vigh_poliza']);
	$Nciudad=$DATOS['ciudad'];
	$DATOS['ciudad']=ajusta_ciudad($DATOS['ciudad']);
	$Nciudad=qo1("Select concat(departamento,' - ',nombre) from ciudad where codigo='".$DATOS['ciudad']."'");
	echo "<body onload=\" ";
	if($QQ=q("select * from siniestro where placa='".$DATOS['placa']."' "))
	{
		while($Q=mysql_fetch_object($QQ))
		{
			echo "alert('Siniestro previo: $Q->numero (Fecha Autorización: $Q->fec_autorizacion Poliza:$Q->poliza).');";
		}
	}
	echo "var OD=opener.document.mod;
	OD.numero.value='".$DATOS['numero']."';
	OD.ciudad.value='".$DATOS['ciudad']."';
	OD._ciudad.value='$Nciudad';
	OD.fec_autorizacion.value='".$DATOS['fecha']."';
	OD.fec_siniestro.value='".$DATOS['fecha_siniestro']."';
	OD.fec_declaracion.value='".$DATOS['fecha_declaracion']."';
	OD.poliza.value='".$DATOS['poliza']."';
	OD.sucursal_radicadora.value='".$DATOS['sucursal']."';
	OD.intermediario.value='".$DATOS['intermediario']."';
	OD.vigencia_desde.value='".$DATOS['vig_poliza']."';
	OD.vigencia_hasta.value='".$DATOS['vigh_poliza']."';
	OD.placa.value='".$DATOS['placa']."';
	OD.marca.value='".$DATOS['marca']."';
	OD.tipo.value='".$DATOS['tipo']."';
	OD.linea.value='".$DATOS['linea']."';
	OD.modelo.value='".$DATOS['modelo']."';
	OD.clase.value='".$DATOS['clase']."';
	OD.color.value='".$DATOS['color']."';
	OD.servicio.value='".$DATOS['servicio']."';
	OD.asegurado_nombre.value='".$DATOS['nombre_asegurado']."';
	OD.asegurado_id.value='".$DATOS['iden_asegurado']."';
	OD.asegurado_direccion.value='".$DATOS['dir_asegurado']."';
	OD.declarante_nombre.value='".$DATOS['nombre_declarante']."';
	OD.declarante_id.value='".$DATOS['iden_declarante']."';
	OD.declarante_telefono.value='".$DATOS['telefono_declarante']."';
	OD.declarante_direccion.value='".$DATOS['dir_declarante']."';
	OD.declarante_ciudad.value='".$DATOS['ciudad_declarante']."';
	OD.declarante_tel_resid.value='".$DATOS['telresid_declarante']."';
	OD.declarante_tel_ofic.value='".$DATOS['telofic_declarante']."';
	OD.declarante_celular.value='".$DATOS['celular_declarante']."';
	OD.declarate_tel_otro.value='".$DATOS['otrotel_declarante']."';
	OD.declarante_email.value='".$DATOS['email_declarante']."';
	OD.conductor_nombre.value='".$DATOS['nombre_conductor']."';
	OD.conductor_tel_resid.value='".$DATOS['telresid_conductor']."';
	OD.conductor_tel_ofic.value='".$DATOS['telofic_conductor']."';
	OD.conductor_celular.value='".$DATOS['celular_conductor']."';
	window.close();void(null);\";></body>";
	#echo "<br>Ciudad:".$DATOS['ciudad'].'.';
}

function ajusta_fecha($dato)
{
	$dato=str_replace('de ','',$dato);
	$dia=substr($dato,0,strpos($dato,' '));
	$dato=substr($dato,strpos($dato,' ')+1);
	$mes=substr($dato,0,strpos($dato,' '));
	$ano=substr($dato,strpos($dato,' ')+1);
	$mes=str_replace('Enero','-01-',$mes);
	$mes=str_replace('Febrero','-02-',$mes);
	$mes=str_replace('Marzo','-03-',$mes);
	$mes=str_replace('Abril','-04-',$mes);
	$mes=str_replace('Mayo','-05-',$mes);
	$mes=str_replace('Junio','-06-',$mes);
	$mes=str_replace('Julio','-07-',$mes);
	$mes=str_replace('Agosto','-08-',$mes);
	$mes=str_replace('Septiembre','-09-',$mes);
	$mes=str_replace('Octubre','-10-',$mes);
	$mes=str_replace('Noviembre','-11-',$mes);
	$mes=str_replace('Diciembre','-12-',$mes);
	return $ano.$mes.$dia;
}


function ajusta_ciudad($ciudad)
{
	return qo1("select ciudad from oficina where nombre like '%$ciudad%'");
}
?>
