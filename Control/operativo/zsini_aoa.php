<?php
include('inc/funciones_.php');

/* 
		RUTINA QUE AYUDA A CARGAR SINIESTROS DE LAS ASEGURADORAS MEDIANTE UN COPIADO Y PEGADO DE UN TEXTO Y ES ANALIZADO PARA SER
		INSERTADO EN LA TABLA DE SINIESTROS
*/

if(!empty($Acc) && function_exists($Acc)) { eval($Acc.'();'); die(); }

html(TITULO_APLICACION.' - CARGA DE INFORMACION DE SINIESTROS'); // pinta cabeceras html y herramientas javascript
echo "<script language='javascript'>
	function carga()
	{
		centrar(600,600);
		DD=opener.document.mod;
		if(Number(DD.aseguradora.value)!=0)
		{
			window.open('zsini_aoa.php?Acc=busca_proceso&Aseguradora='+DD.aseguradora.value,'Oculto_sini');
		}
		else
		{
			alert('Debe seleccionar una aseguradora antes de entrar a esta opción');
			window.close();void(null);
		}
	}
</script>
<body onload='carga()'>";
echo "<span id='n_aseguradora'></span><br>
<FORM action='zsini_aoa.php' method='post' target='_self' name='forma' id='forma'>
<h3><b>Señor Usuario copie el texto completo del email y peguelo a continuación:</b></h3><br>
<center><TEXTAREA ROWS='25' COLS='100'  ID='texto' name='texto' style='font-family:arial;font-size:9;'></textarea>
<br><BR>
<input type='submit' value='ANALIZAR EL TEXTO' style='width:200;height:30'>
<input type='hidden' name='Acc' value=''></center>
</form><script language='javascript'>document.forma.texto.focus();</script>
<iframe name='Oculto_sini' id='Oculto_sini' style='visibility:hidden' height=10 width=100></iframe><body>";

function busca_proceso() // de acuerdo a la aseguradora se busca el proceso que debe mostrar al usuario
{
	global $Aseguradora;
	$DA=qo("select * from aseguradora where id=$Aseguradora"); // trae los datos de la aseguradora
	if($DA->proceso_insercion)
	{
		echo "<script language='javascript'>
			function carga()
			{
				parent.document.getElementById('n_aseguradora').innerHTML='<h2 align=\'center\'><b>$DA->nombre</b></h2>';
				parent.document.forma.Acc.value='$DA->proceso_insercion';";
		if($DA->consecutivo_auto) echo "parent.document.forma.texto.value='$Aseguradora';parent.document.forma.submit();";
		echo "
			}
		</script>
		<body onload='carga()'></body>";
	}
	else
	{
		echo "<script language='javascript'>
			function carga()
			{
				parent.document.getElementById('n_aseguradora').innerHTML='<h2 align=\'center\'><b>$DA->nombre</b></h2>';
				alert('Infortunadamente no se ha definido el proceso de inserción automática de siniestros para esta aseguradora. Señor usuario, por favor inserte manualmente la información');
				parent.close();void(null);
			}
		</script>
		<body onload='carga()'></body>";
	}
}

function procesa_royal() // proceso de carga de siniestros de royal
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
		$Partes=split(':',$Texto);
		if($Partes[0] && $Partes[1])
		{
			if($Partes[0]=='CIUDAD') $DATOS['ciudad']=ajusta_ciudad($Partes[1]);
			$Nciudad=qo1("Select concat(departamento,' - ',nombre) from ciudad where codigo='".$DATOS['ciudad']."'");
			if($Partes[0]=='FECHA') $DATOS['fecha']=trim($Partes[1]);
			if($Partes[0]=='NUMERO SINIESTRO') $DATOS['numero']=trim($Partes[1]);
			if($Partes[0]=='PLACAS VEHICULO') $DATOS['placa']=trim($Partes[1]);
			//if($Partes[0]=='ASEGURADO') $DATOS['nombre_asegurado']=$Partes[1];
			if($Partes[0]=='IDENTIFICADO CON NIT/CC'){	$DATOS['iden_asegurado']=ajusta_iden_royal(trim($Partes[1]));}
			if($Partes[0]=='POLIZA NUMERO') $DATOS['poliza']=trim($Partes[1]);
			if($Partes[0]=='NOMBRE ASEGURADO') $DATOS['nombre_asegurado']=trim($Partes[1]);
			if($Partes[0]=='ASEGURADO') $DATOS['nombre_asegurado']=trim($Partes[1]);
			if($Partes[0]=='TELEFONO FIJO')
			{
				if($DATOS['nombre_conductor']) $DATOS['telresid_conductor']=trim($Partes[1]);
				else $DATOS['telefono_declarante']=trim($Partes[1]);
			}
			if($Partes[0]=='TELEFONO CELULAR')
			{
				if($DATOS['nombre_conductor']) $DATOS['celular_conductor']=trim($Partes[1]);
				else $DATOS['celular_declarante']=trim($Partes[1]);
			}
			if($Partes[0]=='NOMBRE CONDUCTOR') $DATOS['nombre_conductor']=trim($Partes[1]);
			if($Partes[0]=='NOMBRE ANALISTA') $DATOS['nombre_analista']=trim($Partes[1]);
		}
		$j++;
	}
	echo "<body onload=\" ";
	if($QQ=q("select * from siniestro where placa='".$DATOS['placa']."' ")) // busca siniestros previos de la misma placa
	{
		$Alerta='';
		while($Q=mysql_fetch_object($QQ)) // alerta si encuentra siniestros previos
		{
			$Alerta.="Siniestro previo: $Q->numero (Fecha Autorización: $Q->fec_autorizacion Poliza:$Q->poliza.";
		}
		echo "alert('$Alerta');";

	}
	$Es_especial=busca_placa_especial($DATOS['placa'],'2,5'); // busca placas especiales que tengan alguna condicion 
	if($Es_especial) echo "alert('$Es_especial->descripcion Condicion: $Es_especial->condicion');"; // si la encuentra, muestra la condicion
	echo "var OD=opener.document.mod;
	OD.numero.value='".$DATOS['numero']."';
	OD.ciudad.value='".$DATOS['ciudad']."';
	// OD._ciudad.value='$Nciudad';
	OD.ciudad_original.value='".$DATOS['ciudad']."';
	OD._ciudad_original.value='$Nciudad';
	OD.fec_autorizacion.value='".$DATOS['fecha']."';
	OD.fec_declaracion.value='".$DATOS['fecha']."';
	OD.poliza.value='".$DATOS['poliza']."';
	OD.intermediario.value='".$DATOS['nombre_analista']."';
	OD.placa.value='".$DATOS['placa']."';
	OD.asegurado_nombre.value='".$DATOS['nombre_asegurado']."';
	OD.asegurado_id.value='".$DATOS['iden_asegurado']."';
	OD.declarante_nombre.value='".$DATOS['nombre_asegurado']."';
	OD.declarante_telefono.value='".$DATOS['telefono_declarante']."';
	OD.declarante_celular.value='".$DATOS['celular_declarante']."';
	OD.conductor_nombre.value='".$DATOS['nombre_conductor']."';
	OD.conductor_tel_resid.value='".$DATOS['telresid_conductor']."';
	OD.conductor_celular.value='".$DATOS['celular_conductor']."';
	OD.vigencia_desde.value='';
	OD.vigencia_hasta.value='';
	window.close();void(null);
	opener.Asignando_calendario=true;
	OD.fec_autorizacion.focus();\";></body>";
	#echo "<br>Ciudad:".$DATOS['ciudad'].'.';
}

function procesa_liberty() // carga de siniestros de liberty. tiene dos opciones normal o pendiente
{
	html();
	echo "<body>
	<h3>LIBERTY SEGUROS - CARGA DE SINIESTROS</H3>
	<form action='zsini_aoa.php' target='_self' method='POST' name='forma' id='forma'>
		Seleccione el proceso: <select name='Acc'><option></option>
			<option value='procesa_liberty_normal'>Normal</option>
			<option value='procesa_liberty_pendiente'>Pendiente</option></select>
			<input type='hidden' name='texto' value=\"".$_POST['texto']."\">
			<input type='submit' value=' PROCESAR '>
	</form></body>";
}

function procesa_liberty_normal() // carga normal de siniestros de liberty es cuando vienen con numero de siniestro
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
		//echo "<br />".$Texto;
		if(strpos($Texto,' póliza '))
		{
			$np=substr($Texto,strpos($Texto,' póliza ')+8);
			$DATOS['poliza']=substr($np,0,strpos($np,' '));
			$np=substr($np,strpos($np,' en la vigencia ')+16);
			$fv1=substr($np,0,strpos($np,' '));
			$fv1=str_replace('/','-',$fv1);
			$np=substr($np,strpos($np,' a ')+3);
			$fv2=str_replace('/','-',$np);
			//echo "<br />-$np-";
		}

		$Partes=split(': ',$Texto);
		if($Partes[0] && $Partes[1])
		{
			if($Partes[0]=='Ciudad') $DATOS['ciudad']=ajusta_ciudad($Partes[1]);
			$Nciudad=qo1("Select concat(departamento,' - ',nombre) from ciudad where codigo='".$DATOS['ciudad']."'");
			if($Partes[0]=='Fecha') $DATOS['fecha']=ajusta_fec_liberty($Partes[1]);
			if($Partes[0]=='Placas del vehículo') $DATOS['placa']=trim($Partes[1]);
			if($Partes[0]=='Siniestro') $DATOS['numero']=trim($Partes[1]);
			if($Partes[0]=='Asegurado') $DATOS['nombre_asegurado']=trim($Partes[1]);
			if($Partes[0]=='Identificado con') $DATOS['iden_asegurado']=ajusta_iden_liberty(trim($Partes[1]));
		//	if($Partes[0]=='POLIZA NUMERO') $DATOS['poliza']=$Partes[1];
		//	if($Partes[0]=='NOMBRE ASEGURADO') $DATOS['nombre_asegurado']=$Partes[1];
			if($Partes[0]=='Teléfono Fijo')
			{
				if($DATOS['nombre_conductor']) $DATOS['telresid_conductor']=trim($Partes[1]);
				else $DATOS['telefono_declarante']=trim($Partes[1]);
			}
			if($Partes[0]=='Teléfono Celular')
			{
				if($DATOS['nombre_conductor']) $DATOS['celular_conductor']=trim($Partes[1]);
				else $DATOS['celular_declarante']=trim($Partes[1]);
			}
//			if($Partes[0]=='NOMBRE CONDUCTOR') $DATOS['nombre_conductor']=$Partes[1];
//			if($Partes[0]=='NOMBRE ANALISTA') $DATOS['nombre_analista']=$Partes[1];
		}
		$j++;
	}
	echo "<body onload=\" ";
	if($QQ=q("select * from siniestro where placa='".$DATOS['placa']."' ")) // busca coincidencia de siniestros anteriores de la misma placa
	{
		$Alerta='';
		while($Q=mysql_fetch_object($QQ)) // muestra los siniestros anteriores en una alerta
		{
			$Alerta.="Siniestro previo: $Q->numero (Fecha Autorización: $Q->fec_autorizacion Poliza:$Q->poliza.";
		}
		echo "alert('$Alerta');";
	}
	$Es_especial=busca_placa_especial($DATOS['placa'],'3,7'); // busca placas con condiciones especiales
	if($Es_especial) echo "alert('$Es_especial->descripcion Condicion: $Es_especial->condicion');"; // si nay condicion especial la muestra en una alerta
	echo "var OD=opener.document.mod;
	OD.numero.value='".$DATOS['numero']."';
	OD.ciudad.value='".$DATOS['ciudad']."';
	// OD._ciudad.value='$Nciudad';
	OD.ciudad_original.value='".$DATOS['ciudad']."';
	OD._ciudad_original.value='$Nciudad';
	OD.fec_autorizacion.value='".$DATOS['fecha']."';
	OD.fec_declaracion.value='".$DATOS['fecha']."';
	OD.poliza.value='".$DATOS['poliza']."';
//	OD.intermediario.value='".$DATOS['nombre_analista']."';
	OD.placa.value='".$DATOS['placa']."';
	OD.asegurado_nombre.value='".$DATOS['nombre_asegurado']."';
	OD.asegurado_id.value='".$DATOS['iden_asegurado']."';
	OD.declarante_nombre.value='".$DATOS['nombre_asegurado']."';
	OD.declarante_telefono.value='".$DATOS['telefono_declarante']."';
	OD.declarante_celular.value='".$DATOS['celular_declarante']."';
	OD.conductor_nombre.value='".$DATOS['nombre_conductor']."';
	OD.conductor_tel_resid.value='".$DATOS['telresid_conductor']."';
	OD.conductor_celular.value='".$DATOS['celular_conductor']."';
	OD.vigencia_desde.value='$fv1';
	OD.vigencia_hasta.value='$fv2';
	window.close();void(null);
	opener.Asignando_calendario=true;
	OD.fec_autorizacion.focus();\";></body>";
	#echo "<br>Ciudad:".$DATOS['ciudad'].'.';
}

function procesa_liberty_pendiente() // carga de siniestros de liberty que vienen sin numero de siniestro, se carga con la fecha y placa como numero de siniestro
{
	echo "<body>";
	$Texto=strtoupper($_POST['texto']);
	$Texto=str_replace(chr(10),'',$Texto);
	$Texto=str_replace(chr(13),'',$Texto);
	$Texto=str_replace(chr(9),'',$Texto);
	$Texto=str_replace(chr(237),'I',$Texto);
	$Texto=str_replace(chr(243),'O',$Texto);
	$Texto=str_replace(chr(233),'E',$Texto);
//	$Numeros='';
//	for($i=0;$i<strlen($Texto);$i++)
//	{
//		$Letra=substr($Texto,$i,1);
//		$letra=ord($Letra);
//		$Numeros.="[$letra]";
//		echo "<a alt='$letra' title='$letra'>$Letra</a>";
//	}
//	echo "<br><br>$Numeros<br><br>";
	$p=strpos(' '.$Texto,'CIUDAD:');
	$Texto=substr($Texto,$p+6);
	$p=strpos($Texto,'FECHA:');
	$Ciudad=substr($Texto,0,$p);
	Echo "$Ciudad ";
	$Ciudad=ajusta_ciudad($Ciudad);
	echo "<br>$Ciudad--<br>";
	$Texto=substr($Texto,strpos($Texto,'FECHA:')+6);
	$Fecha=substr($Texto,0,strpos($Texto,'PLACAS DEL VEHICULO:'));
	$Texto=substr($Texto,strpos($Texto,'PLACAS DEL VEHICULO:')+20);
	$Fecha=ajusta_fec_liberty($Fecha);
	echo "$Fecha--</br>";
	$Placa=substr($Texto,0,strpos($Texto,'TIPO DE CONVENIO:'));
	echo "$Placa--</br>";
	$Texto=substr($Texto,strpos($Texto,'ASEGURADO:')+10);
	$Asegurado=substr($Texto,0,strpos($Texto,'IDENTIFICADO CON:'));
	$Texto=substr($Texto,strpos($Texto,'IDENTIFICADO CON:')+17);
	echo "$Asegurado--</br>";
	$Identificacion=substr($Texto,0,strpos($Texto,'PARA HACER USO DEL VEHICULO DE MOVILIDAD'));
	$Identificacion=str_replace(' ','',$Identificacion);
	if(strpos($Identificacion,'-')) $Identificacion=substr($Identificacion,strpos($Identificacion,'-')+1);
	echo "$Identificacion--</br>";
	$Texto=substr($Texto,strpos($Texto,'RESULTO AFECTADA LA POLIZA ')+27);
	$Poliza=substr($Texto,0,strpos($Texto,' EN LA VIGENCIA '));
	echo "$Poliza--</br>";
	$Texto=substr($Texto,strpos($Texto,' EN LA VIGENCIA ')+16);
	$Vigencia_inicial=substr($Texto,0,10);
	$Vigencia_final=substr($Texto,13,10);
	echo "$Vigencia_inicial-$Vigencia_final--<br>";
	$Texto=substr($Texto,23);
	$Texto=substr($Texto,strpos($Texto,'NOMBRE:')+7);
	$Declarante=substr($Texto,0,strpos($Texto,'TELEFONO:'));
	echo "$Declarante--<br>";
	$Texto=substr($Texto,strpos($Texto,'TELEFONO:')+9);
	$Telefono1=substr($Texto,0,strpos($Texto,'TELEFONO CELULAR:'));
	echo "$Telefono1--<br>";
	$Texto=substr($Texto,strpos($Texto,'TELEFONO CELULAR:')+17);
	$Telefono2=substr($Texto,0,strpos($Texto,'CORDIAL SALUDO'));
	echo "$Telefono2--<br>";
//	echo $Texto;
	$numero_siniestro=date('Ymd').'-'.$Placa;
	echo "<script language='javascript'>";
	$Es_especial=busca_placa_especial($DATOS['placa'],'3,7');
	if($Es_especial) echo "alert('$Es_especial->descripcion Condicion: $Es_especial->condicion');";
	ECHO "var OD=opener.document.mod;
	OD.numero.value='$numero_siniestro';
	OD.ciudad.value='$Ciudad';
	OD.ciudad_original.value='$Ciudad';
	OD.fec_autorizacion.value='$Fecha';
	OD.fec_declaracion.value='$Fecha';
	OD.fec_siniestro.value='$Fecha';
	OD.poliza.value='$Poliza';
	OD.placa.value='$Placa';
	OD.asegurado_nombre.value='$Asegurado';
	OD.asegurado_id.value='$Identificacion';
	OD.declarante_nombre.value='$Declarante';
	OD.declarante_id.value='$Identificacion';
	OD.declarante_celular.value='$Telefono2';
	OD.conductor_nombre.value='$Declarante';
	OD.declarante_telefono.value='$Telefono1';
	OD.vigencia_desde.value='$Vigencia_inicial';
	OD.vigencia_hasta.value='$Vigencia_final';
	window.close();void(null);
	opener.Asignando_calendario=true;
	//OD.fec_autorizacion.focus();
	</script></body>";
}

function procesa_colseguros() // carga de siniestros que venian en un correo de colseguros. (dejado de usar.) ahora se usa la plataforma allia2net. no se puede usar analizador de texto
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
		$Alerta='';
		while($Q=mysql_fetch_object($QQ))
		{
			$Alerta.="Siniestro previo: $Q->numero (Fecha Autorización: $Q->fec_autorizacion Poliza:$Q->poliza.";
		}
		echo "alert('$Alerta');";
	}
	echo "var OD=opener.document.mod;
	OD.numero.value='".$DATOS['numero']."';
	OD.ciudad.value='".$DATOS['ciudad']."';
	// OD._ciudad.value='$Nciudad';
	OD.ciudad_original.value='".$DATOS['ciudad']."';
	OD._ciudad_original.value='$Nciudad';
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
	window.close();void(null);
	opener.Asignando_calendario=true;
	OD.fec_autorizacion.focus();\";></body>";
	#echo "<br>Ciudad:".$DATOS['ciudad'].'.';
}

function ajusta_fecha($dato) // analiza la fecha y la ajusta. obtiene el año, mes y dia de un texto
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


function ajusta_ciudad($ciudad) // a partir de un texto busca la ciudad. 
{
	if($ciudad=='BOGOTA, D.C.') $ciudad='BOGOTA';
	$ciudad=trim($ciudad);
	$cod_ciudad=qo1("select ciudad from oficina where nombre like '%$ciudad%'");
	if(inlist($cod_ciudad,'11001000,05001000,76001000,66001000,08001000,68001000,73001000'))
	return $cod_ciudad; else return ' no valido '.$cod_ciudad;
}

function ajusta_fec_liberty($Linea) // en las cartas de liberty analiza el texto correspondiente a la fecha y retorna año, mes y dia
{
	if(strpos($Linea,' DE ')) $Linea=str_replace(' DE ',' ',$Linea);
	$Partes=split(' ',$Linea);
	$Dia=$Partes[0];
	switch(strtoupper($Partes[1]))
	{
		case 'ENERO': $Mes='01';break;
		case 'FEBRERO': $Mes='02';break;
		case 'MARZO': $Mes='03';break;
		case 'ABRIL': $Mes='04';break;
		case 'MAYO': $Mes='05';break;
		case 'JUNIO': $Mes='06';break;
		case 'JULIO': $Mes='07';break;
		case 'AGOSTO': $Mes='08';break;
		case 'SEPTIEMBRE': $Mes='09';break;
		case 'OCTUBRE': $Mes='10';break;
		case 'NOVIEMBRE': $Mes='11';break;
		case 'DICIEMBRE': $Mes='12';break;
	}
	$Ano=$Partes[2];
	return ($Ano.'-'.$Mes.'-'.$Dia);
}

function ajusta_iden_liberty($Linea) // en las identificaciones de libarty busca comas, puntos y separadores y los elimina.
{
	$linea=trim($Linea);
	//if(strpos($Linea,' ')) { $Partes=split(' ',$Linea); $Cadena=$Partes[1];} else $Cadena=$linea;
	$Identificacion=str_replace(',', '', trim($Cadena));
	$Identificacion=str_replace('.','',$Identificacion);
	$Identificacion=str_replace('-','',$Identificacion);
	return $Identificacion;
}

function ajusta_iden_royal($Linea) // en las identificaciones de roya, busca comas, puntos y separadores y los elimina
{
	$linea=trim($Linea);
	$Identificacion=str_replace(',', '', $linea);
	$Identificacion=str_replace('.','',$Identificacion);
	$Identificacion=str_replace('-','',$Identificacion);
	return $Identificacion;
}

function busca_placa_especial($placa,$aseguradora) // busca una placa en la tabla de condiciones especiales 
{
	if($placa)
	{
		if($esp=qo("select * from placa_especial where placa like '%$placa%' and aseguradora in ($aseguradora) "))
		{return $esp;}
	}
	else return false;
}

function aviso_placa_especial() // obtiene un aviso para mostra en una alerta para placas especiales que tengan cierta condición.
{
	global $placa,$aseguradora;
	if($placa)
	{
		if($esp=qo("select * from placa_especial where placa like '%$placa%' and aseguradora in ($aseguradora) "))
		{
			echo "<body><script language='javascript'>alert('PLACA ESPECIAL: $esp->descripcion CONDICION: $esp->condicion');</script></body>";
		}
	}
}

function renta_automatico()
{
	include('inc/gpos.php');
	html();
	echo "
		<script language='javascript'>
		function cierra() {
			window.close();void(null);
			}
		</script>
	<body onload='cierra();'>PROCESO DE RENTA AUTOMATICO";
	$A=qo("select * from aseguradora where id=$texto"); // trae los datos de la aseguradora	
	$Caracteres_prefijo=strlen($A->prefijo_consecutivo);
	$Ultimo_consecutivo=qo1("select max(substr(numero,$Caracteres_prefijo+1)) from siniestro where left(numero,$Caracteres_prefijo)='$A->prefijo_consecutivo' ");
	$Consecutivo_nuevo=$A->prefijo_consecutivo.str_pad(($Ultimo_consecutivo+1),$A->entero_consec_auto,'0',STR_PAD_LEFT);
	$Placa=$A->prefijo_placa_auto.str_pad(($Ultimo_consecutivo+1),$A->enteros_placa_auto,'0',STR_PAD_LEFT);
	echo "<br>Nuevo consecutivo: $Consecutivo_nuevo";
	echo "<script language='javascript'>
				var D=opener.document.mod;
				D.numero.value='$Consecutivo_nuevo';
				D.placa.value='$Placa';
				D.renta.checked=true;
		</script>";
	echo "</body>";
}


























?>