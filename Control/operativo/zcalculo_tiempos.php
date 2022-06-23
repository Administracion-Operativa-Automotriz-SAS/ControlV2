<?php
/**
 *   Programa informe de Tiempos por Aseguradora.
 *
 * @version $Id$
 * @copyright 2010
 */
include('inc/funciones_.php');
sesion();

if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}
$Rango_colores=array();
$Rango_colores[0]['ini']=0;        $Rango_colores[0]['fin']=0;             $Rango_colores[0]['color']='ffffff';            $Rango_colores[0]['cantidad']=0; $Rango_colores[0]['tag']='Pendiente';
$Rango_colores[1]['ini']=1;        $Rango_colores[1]['fin']=14400;      $Rango_colores[1]['color']='C6FFC2';    $Rango_colores[1]['cantidad']=0; $Rango_colores[1]['tag']='de 0 a 4 horas';
$Rango_colores[2]['ini']=14401;$Rango_colores[2]['fin']=28800;      $Rango_colores[2]['color']='FFFFA4';    $Rango_colores[2]['cantidad']=0; $Rango_colores[2]['tag']='de 4 a 8 horas';
$Rango_colores[3]['ini']=28801;$Rango_colores[3]['fin']=43200;       $Rango_colores[3]['color']='FFC1A3';   $Rango_colores[3]['cantidad']=0; $Rango_colores[3]['tag']='de 8 a 12 horas';
$Rango_colores[4]['ini']=43201; $Rango_colores[4]['fin']=86400;       $Rango_colores[4]['color']='C8F9FF';   $Rango_colores[4]['cantidad']=0; $Rango_colores[4]['tag']='de 12 a 24 horas';
$Rango_colores[5]['ini']=86401; $Rango_colores[5]['fin']=115200;     $Rango_colores[5]['color']='63BBFF';    $Rango_colores[5]['cantidad']=0; $Rango_colores[5]['tag']='de 24 a 32 horas';
//$Rango_colores[6]['ini']=115201;$Rango_colores[6]['fin']=345602;    $Rango_colores[6]['color']='D30000';     $Rango_colores[6]['cantidad']=0; $Rango_colores[6]['tag']='de 48 a 96 horas';
$Rango_colores[6]['ini']=115201;$Rango_colores[6]['fin']=99999999; $Rango_colores[6]['color']='C64FFF';     $Rango_colores[6]['cantidad']=0; $Rango_colores[6]['tag']='Más de 32 horas';


if(!$ASEG)  {pide_datos(); die();}
$Aseg=qo("select * from aseguradora where id=$ASEG");

$_Exel=sino($_Exel);
$Causales=array();
$NTS=tu('siniestro','id');
if($_Exel)
{
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=calculo_tiempos.xls");
}
else
{
	html("CALCULO DE TIEMPOS PARA $Aseg->nombre");
}
echo "<script language='javascript'>
		function verseguimiento(id,contador)
		{
			modal('zcalculo_tiempos.php?Acc=verseguimiento&id='+id+'&Contador='+contador,0,0,10,10,'seg');
		}
		function reconstruye_seguimiento(id)
		{
			modal('zcalculo_tiempos.php?Acc=reconstruye_seguimiento&id='+id,0,0,10,10,'seg2');
		}
		function versiniestro(id)
		{
			modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTS&id='+id,0,0,700,900,'seg');
			//modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$NTS&VINCULOC=id&VINCULOT='+id,0,0,700,700,'seg');

		}
	</script>
	<body>";
	if($_Exel)
	{
		echo "Convenciones de colores:<table><tr><td valign='top'><table border cellspacing='0'>";
		for($i=0;$i<count($Rango_colores);$i++)
		{
			echo "<tr><td colspan=2><b>Rango $i</b></td>
			<td bgcolor='".$Rango_colores[$i]['color']."'>-----</td>
			<td>".$Rango_colores[$i]['tag']."</td></tr>";
			if(($i+1) % 4 ==0) echo "</tr></table></td><td valign='top'><table border cellspacing='0'>";
		}
		echo "</table></td></tr></table>";
	}

echo "
	<h3 align='center'>ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A. - Informe de Tiempos - Aseguradora: $Aseg->nombre</h3>";

if($EST) $F_Estados=" and estado=$EST "; else $F_Estados="";
if($CAU) $F_Causales=" and causal=$CAU "; else $F_Causales="";

q("drop table if exists tmpi_tiempos");
q("create table tmpi_tiempos select id,numero,fec_autorizacion,estado,t_estado_siniestro(estado) as nestado,ingreso, ingreso as contacto, t_causal(causal) as ncausal,
		observaciones
		from siniestro where ".($MOD=='Ingreso'?" date_format(ingreso,'%Y-%m-%d') ":" fec_autorizacion ")." between '$FI' and '$FF' and aseguradora=$ASEG $F_Estados $F_Causales order by fec_autorizacion ");
q("alter table tmpi_tiempos add column remitido datetime default '0000-00-00 00:00:00'  ");
q("alter table tmpi_tiempos add column actual datetime default '0000-00-00 00:00:00'  ");
q("alter table tmpi_tiempos add unique index id (id) ");

if($Siniestros=q("select * from tmpi_tiempos"))
{
	include('inc/link.php');
	while($S=mysql_fetch_object($Siniestros))
	{
		if($Cox=qom("select concat(fecha,' ',hora) as momento from seguimiento where siniestro=$S->id and tipo=3",$LINK))
		{
			mysql_query("update tmpi_tiempos set contacto='$Cox->momento' where id=$S->id",$LINK);
		}
		if($Rem=qom("select concat(fecha,' ',hora) as momento from seguimiento where siniestro=$S->id and tipo=4",$LINK))
		{
			mysql_query("update tmpi_tiempos set remitido='$Rem->momento' where id=$S->id",$LINK);
		}
		if($Act=qom("select concat(fecha,' ',hora) as momento from seguimiento where siniestro=$S->id and tipo=8",$LINK))
		{
			mysql_query("update tmpi_tiempos set actual='$Act->momento'  where id=$S->id",$LINK);
		}
		if($_SESSION['User']==1) reconstruye_seguimiento($LINK,$S->id);
	}
	mysql_close($LINK);
}
if($Siniestros=q("select * from tmpi_tiempos"))
{


	echo "<table border cellspacing='0' style='empty-cells:show'><tr><th>#</th><th>Numero</th><th>Fecha Autorización</th><th>Estado</th>
				<th>Ingreso</th><th>Contacto</th><th>Diferencia</th><th>Causal</th><th>Remitido</th><th>Actualización</th><th>Diferencia</th></tr>";
	$Contador=0;
	$Cantidad=0;
	$Contador1=0;
	$Erroneos=0;
	while($S=mysql_fetch_object($Siniestros))
	{
		$Contador++;
		echo "<tr><td align='center'>$Contador</td><td align='center' ".(inlist($_SESSION['User'],'1,2,3,26')?"onclick='verseguimiento($S->id,$Contador);' style='cursor:pointer' ":"")." >$S->numero</td>
					<td align='center' ";
		if(inlist($_SESSION['User'],'1,2,3,26')) echo " style='cursor:pointer;' onclick='versiniestro($S->id);'";
		echo ">";
		if($_Exel)
			echo $S->fec_autorizacion;
		else
			echo "<a class='info' >$S->fec_autorizacion".(inlist($_SESSION['User'],'1,2,3,26')?"<span style='width:500px'>".nl2br($S->observaciones)."</span>":"<span>Fecha de Autorización</span>" )."</a>";
		echo "</td><td align='center' ";
		if(inlist($_SESSION['User'],'1')) echo "ondblclick='reconstruye_seguimiento($S->id);'";
		echo ">$S->nestado</td><td align='center'>$S->ingreso</td><td align='center'>$S->contacto</td>";
		$Segundos=segundos_habiles(($S->actual!='0000-00-00 00:00:00'?$S->actual:$S->ingreso),$S->contacto);
		for($i=0;$i<count($Rango_colores);$i++)
		{
			if($Segundos >= $Rango_colores[$i]['ini'] && $Segundos<= $Rango_colores[$i]['fin'])
			{
				$Color=$Rango_colores[$i]['color'];
				$Rango_colores[$i]['cantidad']++;
			}
		}
		echo "<td align='center' bgcolor='$Color'>".segundos2horas($Segundos)."</td>";
		if($S->estado==1)
		{
			echo "<td>$S->ncausal</td>";
			$Causal[$S->ncausal]++;
		}
		else echo "<td>&nbsp;</td>";
		echo "<td>".($S->remitido!='0000-00-00 00:00:00'?$S->remitido:'')."</td><td>".($S->actual!='0000-00-00 00:00:00'?$S->actual:'')."</td>";
		if($S->remitido && $S->actual)
		{
			$Segundosa=segundos_habiles($S->remitido,$S->actual);
			for($i=0;$i<count($Rango_colores);$i++)
			{
				if($Segundosa >= $Rango_colores[$i]['ini'] && $Segundosa<= $Rango_colores[$i]['fin'])
				{
					$Color=$Rango_colores[$i]['color'];
				}
			}
			echo "<td align='center' bgcolor='$Color'>".segundos2horas($Segundosa)."</td>";
		}
		else
		{
			echo "<td></td>";
		}
		if($S->remitido) $Erroneos++;
		if($Segundos>0)
		{
			$Cantidad+=$Segundos;$Contador1++;
		}
		echo "</tr>";
	}
	echo "</table>
	<table cellpadding=5><tr><th colspan=4>Estadisticas</th></tr>
	<tr><td valign='top'>";
	if(inlist($_SESSION['User'],'1,2,3,26'))
	{
		if($Contador1) $Promedio=round($Cantidad/$Contador1,2); else $Promedio=0;
		echo "<table><tr><th>Promedio entre Ingreso <br />y Contacto Exitoso:</th></tr><tr><td>".segundos2horas($Promedio)."</td></tr></table>";
	}

	echo "</td><td valign='top'><table border cellspacing='0'><tr><th colspan=4>Cantidad y porcentaje por rangos de contactos exitosos</th></tr>
			<tr><th>Rango</th><th>Desde - Hasta</th><th>Cantidad</th><th>Porcentaje</th></tr>";
	$Sporc=0;
	for($i=0;$i<count($Rango_colores);$i++)
	{
		$Cantidad=$Rango_colores[$i]['cantidad'];
		$Porcentaje=coma_formatd(round($Cantidad/$Contador*100,2),2);
		$Inicial=$Rango_colores[$i]['tag'];
	//	$Final=segundos2tiempo($Rango_colores[$i]['fin']);
		echo "<tr><td>Rango $i</td><td>$Inicial </td><td align='right'>$Cantidad</td><td align='right'>$Porcentaje %</td>";
		$Sporc+=$Porcentaje;
	}
	echo "</table></td><td>";
	$No_adjudicados=0;
	if(count($Causal))
	{
		foreach($Causal as $Id => $Cantidad) $No_adjudicados+=$Cantidad;
		$Porcentaje=coma_formatd(round($No_adjudicados/$Contador*100,2),2);
		echo "<table border cellspacing='0'><th colspan=2>No adjudicados : $No_adjudicados de $Contador = $Porcentaje % </th><th>Cantidad</th><th>%</th></tr>";
		foreach($Causal as $Id => $Cantidad)
		{
			$Porcentaje=coma_formatd(round($Cantidad/$No_adjudicados*100,2),2);
			echo "<tr><td colspan=2>$Id</td><td align='right'>$Cantidad</td><td align='right'>$Porcentaje %</td></tr>";
		}
		echo "</table>";
	}
	else echo "No hay Causales.";
	echo "</td><td align='center' valign='top'>";
	$Porcentaje=round($Erroneos/$Contador*100,2);
	echo "<table><tr><th>Información erronea:</th></tr><tr><td> $Erroneos de $Contador = $Porcentaje % </td></tr></table></td></table>";
}
else
{
	echo "No hay información que coincida con: Fecha inicial: $FI   Fecha final: $FF  Aseguradora: $Aseg->nombre ";
}
echo "</body>";


function pide_datos()
{
	global $Rango_colores;
	if($_SESSION['User']==11) // ASEGURADORA NIVEL AVANZADO 1
	{
		$Aseg=qo1("select aseguradora from usuario_aseguradora1 where id=".$_SESSION['Id_alterno']);
		if($Aseg==3 || $Aseg==7) $Aseg='3,7';
	}
	elseif($_SESSION['User']==29) // ASEGURADORA NIVEL AVANZADO 2
	{
		$Aseg=qo1("select aseguradora from usuario_aseguradora2 where id=".$_SESSION['Id_alterno']);
		if($Aseg==3 || $Aseg==7) $Aseg='3,7';
	}
	else
		$Aseg=0;

	html('CALCULO DE TIEMPOS POR ASEGURADORA');
	echo "<script language='javascript'>
			function carga()
			{
				centrar();
				document.getElementById('IResultado').style.height=document.body.clientHeight-180;
			}
			function validar()
			{
				carga();
				document.forma.submit();
			}
		</script>
		<body onload='carga()'>
		<form action='zcalculo_tiempos.php' method='post' target='IResultado' name='forma' id='forma'>
			Aseguradora: ".menu1("ASEG","Select id,nombre from aseguradora ".($Aseg?" where id in ($Aseg) ":""))."
			Fecha inicial: ".pinta_FC('forma','FI',date('Y-m-d'))." Fecha Final : ".pinta_FC('forma','FF',date('Y-m-d'))."
			Estado: ".menu1("EST","select id,nombre from estado_siniestro order by id",0,1,"width:100px")."
			Causal: ".menu1("CAU","select id,nombre from causal where id not in (4) order by id ",0,1,"width:100px")."
			Filtra por: ".menu3("MOD","Ingreso,Ingreso;Autorizacion,Autorización",'Autorizacion',0,"","")."
			Excel <input type='checkbox' name='_Exel'>
			<input type='button' value='APLICAR' onclick='validar()' style='height:20px;font-weight:bold;'>
		</form>
		<table cellpadding=5><tr><th>Convenciones</th><th>Ayudas</th></tr>
		<tr><td>Convenciones de colores:<table><tr><td valign='top'><table border cellspacing='0'>";
	for($i=0;$i<count($Rango_colores);$i++)
	{
		echo "<tr><td><b>Rango $i</b></td>
		<td><span style='background-color:".$Rango_colores[$i]['color'].";'>-----</span></td>
		<td><td>".$Rango_colores[$i]['tag']."</td></tr>";
		if(($i+1) % 4 ==0) echo "</tr></table></td><td valign='top'><table border cellspacing='0'>";
	}
	echo "</table></td></tr></table></td><td valign='top'><ul><li>El reporte filtra tomando como criterio la fecha de autorización";
	if(inlist($_SESSION['User'],'1,2,3,26'))
		echo "<li>Puede ver el seguimiento de cada siniestro dando un click sobre el número del siniestro.
				 <li>Puede ver el siniestro dando un click sobre la fecha de autorización del siniestro.";
	echo " <li>Si no se selecciona un <i>Estado</i>, se muestran todos los registros.
		 <li>Si no se selecciona una <i>Causal</i>, se muestran todos los registros.
		 </ul></td></table>
		<iframe name='IResultado' id='IResultado' width='100%' border='0' frameborder='no'></iframe>

		</body>";
}

function verseguimiento()
{
	global $id,$Contador;
	$Numero=qo1("select numero from siniestro where id=$id");
	$NT=tu('seguimiento','id');
	html("POSICION: $Contador - Seguimiento  del siniestro: $Numero");
	echo "<script language='javascript'>
	var Marca1=0;
	var Marca2=0;
	function abre_seguimiento(id)
	{
		modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NT&id='+id,0,0,700,700,'seg1');
	}
	function convierte_a_exitoso(id)
	{
		modal('zcalculo_tiempos.php?Acc=convierte_a_exitoso&id='+id,0,0,10,10,'seg2');
	}
	function marca_fecha(id)
	{
		if(Marca1==0)
		{
				Marca1=id;document.getElementById('marca1').innerHTML='Marca1 :'+id;
		}
		else
		{
			if(Marca2==0)
			{
				Marca2=id;
				document.getElementById('marca2').innerHTML='Marca2 :'+id;
				modal('zcalculo_tiempos.php?Acc=cambia_fechas&id1='+Marca1+'&id2='+Marca2,0,0,10,10,'cf');
			}
		}
	}
	</script><body><script language='javascript'>centrar(800,500);</script><span id='marca1'></span><span id='marca2'></span>";
	if($Seguimiento=q("select *,t_tipo_seguimiento(tipo) as ntipo from seguimiento where siniestro=$id order by fecha, hora"))
	{
		echo "<table><tr><th>Fecha</th><th>Hora</th><th>Usuario</th><th>Descripcion</th><th>Tipo</th></tr>";
		while($S=mysql_fetch_object($Seguimiento))
		{
			echo "<tr><td ondblclick='marca_fecha($S->id);' nowrap='yes'>$S->fecha</td><td>$S->hora</td><td>$S->usuario</td><td ondblclick='convierte_a_exitoso($S->id);'>$S->descripcion</td><td onclick='abre_seguimiento($S->id);'>$S->ntipo</td></tr>";
		}
		echo "</table>";
	}
}

function convierte_a_exitoso()
{
	global $id;
	q("update seguimiento set tipo=3 where id=$id");
	echo "<script language='javascript'>
			function carga()
			{
			window.close();void(null);
			}
		</script>
		<body onload='carga()'></body>";
}

function reconstruye_seguimiento($LINK=0,$id1=0)
{
	global $id;
	if($id1) $id=$id1;
	if(!$LINK) include('inc/link.php');
	$Sin=qom("select * from siniestro where id=$id",$LINK);
	$Fechai=date('Y-m-d',strtotime($Sin->ingreso));
	$Horai=date('H:i:s',strtotime($Sin->ingreso));
	if(!qo1m("select id from seguimiento where siniestro=$Sin->id and tipo=1",$LINK)) 	mysql_query("insert into seguimiento (siniestro,fecha,hora,descripcion,tipo) values ($Sin->id,'$Fechai','$Horai','Ingresa a la base de datos',1)",$LINK);
	$Arreglo=explode("\n",$Sin->observaciones);
	$Usuario='';
	$Consulto=false;
	$Stop=false;
	$Contacto=qo1m("select id from seguimiento where siniestro=$Sin->id and tipo=3",$LINK);
//	include('inc/link.php');
	for($i=0;$i<count($Arreglo);$i++)
	{
		$Linea=$Arreglo[$i];
		if(strlen($Linea))
		{
			$Fecha=$Fechai;
			$Hora=$Horai;
			$Tipo=7;
			$Descripcion=$Linea;
			if(strpos($Linea,'[') && strpos($Linea,']'))
			{
				$Usuario=substr($Linea,0,strpos($Linea,'['));
				$Fecha=substr($Linea,strpos($Linea,'[')+1,10);
				$Hora=substr($Linea,strpos($Linea,'[')+12,8);
				$Descripcion=substr($Linea,strpos($Linea,']')+1);
				$Descripcion=trim(addslashes(addcslashes($Descripcion,"\24")));
				if(strpos(' '.strtolower($Descripcion),'agenda cita') ||  strpos(' '.strtolower($Descripcion),'asigna cita') )
				{
					if(!$Contacto)
					{
						$query="insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($Sin->id,'$Fecha','$Hora','$Usuario',\"Contacto exitoso\",3)";
						if(!mysql_query($query,$LINK))
						{
							echo "Error en: <br />$query<br /><br />".mysql_error($LINK);
							mysql_close($LINK);
							die();
						}
						$Contacto = true;
					}
					$Tipo=5;
				}
				elseif($Fecha.' '.$Hora==$Sin->contacto_exitoso)
				{
					$Contacto=true;
					$Tipo=3;
				}
				elseif(strpos(' '.strtolower($Descripcion),'remite')) $Tipo=4;
				elseif($Descripcion=='Consultó.') $Tipo=2;
				elseif(strpos(' '.strtolower($Descripcion),'se recibe correo electronico') || strpos(' '.strtolower($Descripcion),'se recibe actualizaci')
					) $Tipo=8;

			}
			if($Descripcion=='Consultó.' && $Consulto) continue;
			if(!qo1m("select id from seguimiento where siniestro=$Sin->id and fecha='$Fecha' and hora='$Hora' ",$LINK))
			{
				$query="insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($Sin->id,'$Fecha','$Hora','$Usuario',\"$Descripcion\",$Tipo)";
				if(!mysql_query($query,$LINK))
				{
					echo "Error en: <br />$query<br /><br />".mysql_error($LINK);
					mysql_close($LINK);
					die();
				}
			}
			if($Descripcion=='Consultó.') $Consulto=true;
		}
	}
//	mysql_close($LINK);
/*	echo "<script language='javascript'>
		function carga()
		{
		window.close();void(null);
		}
	</script>
	<body onload='carga()'></body>";
   */
}

function cambia_fechas()
{
	global $id1,$id2;
	$D2=qo("select fecha,hora from seguimiento where id=$id2");
	q("update seguimiento set fecha='$D2->fecha',hora='$D2->hora' where id=$id1");
	echo "<script language='javascript'>
		function carga()
		{
		window.close();void(null);opener.location.reload();
		}
	</script>
	<body onload='carga()'></body>";
}

?>