<?php
########## PERFILES DE SEGURIDAD #############
# 2: ADMINISTRADOR
# 3: CAPTURA - CESAR
# 4: CALL CENTER - ALEXANDRA
# 5: AUTORIZACIONES - DIANA
# 6: ADJUDICACIONES  - PENDIENTE
include('inc/funciones_.php');
sesion();
$USUARIO=$_SESSION['User'];
include('zcontrol_cfg.php');
if(!$FI)
{
	if($_COOKIE['CONTROL_FI']) $FI=$_COOKIE['CONTROL_FI']; else $FI=date('Y-m-d',strtotime(aumentadias(date('Ymd'),-5)));
}
if(!$FF)
{
	if($_COOKIE['CONTROL_FF']) $FF=$_COOKIE['CONTROL_FF']; else $FF=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),5)));
}
if($_SESSION['User']==10)
{
	$OFICINA=qo1("select oficina from usuario_oficina where id='".$_SESSION['Id_alterno']."'");
	$todasoficinas=0;
}
elseif($OFICINA && $todasoficinas)
{	$OFICINA=0;}
elseif(!$OFICINA && !$todasoficinas)
{
	if($_COOKIE['CONTROL_OFICINA'])
	{
		if(!$todasoficinas)
			$OFICINA=$_COOKIE['CONTROL_OFICINA'];
		else
			$OFICINA=0;
	}
	else
		$OFICINA=0;
}

$NT_ubicacion=tu('ubicacion','id');
$NT_siniestro=tu('siniestro','id');
$Ocultaobs=sino($Ocultaobs);
$Color_servicio=qo1("select color_co from estado_vehiculo where id=1");
$Color_concluido=qo1("select color_co from estado_vehiculo where id=7");
$Color_extra='#6affff';
$tControl="tmpi_".$_SESSION['Id_alterno']."_".$_SESSION['User']."_control";
$tServicio="tmpi_".$_SESSION['Id_alterno']."_".$_SESSION['User']."_contrato";
$Brw=$_SERVER['HTTP_USER_AGENT'];
//verificar_hoy();

if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}
control();


function verificar_hoy()
{
	$Hoy=date('Y-m-d');
	require('inc/link.php');
	if($Vehiculos=mysql_query("select id from vehiculo where order by id",$LINK))
	{
		while($V=mysql_fetch_object($Vehiculos))
		{
			if($Ultimo_estado=mysql_query("select * from ubicacion where vehiculo=$V->id order by fecha_final desc,fecha_inicial desc limit 1"))
			{
				if($UE=mysql_fetch_object($Ultimo_estado))
				{
					if($UE->fecha_final<$Hoy)
					{
						if($UE->estado!=7)
						{
							mysql_query("update ubicacion set fecha_final='$Hoy' where id=$UE->id",$LINK);
						}
						else
						{
							$FechaI=aumentadias($UE->fecha_final,1);
							mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones,estado) values
							($UE->oficina,$V->id,'$UE->fecha_final','$Hoy',$UE->odometro_final,$UE->odometro_final,0,'Creación de estado automática',2)",$LINK);
						}
					}
				}
			}
		}
	}
	mysql_close($LINK);
}

function control()
{
	global $FF,$FI,$OFICINA,$Refrescar,$Brw,$NT_siniestro,$NT_ubicacion,$Ocultaobs,$USUARIO;
	setcookie('CONTROL_FI',$FI,time()+(60*60*24*8));  ## cookies de 1 semana
	setcookie('CONTROL_FF',$FF,time()+(60*60*24*8));
	setcookie('CONTROL_OFICINA',$OFICINA,time()+(60*60*24*8));
	html();
	echo "<style type='text/css'>
	tr:hover {background-color: #ddddbb;}
	</style>
	<script language='javascript'>
		var Topscrolled=0;
		var Leftscrolled=0;
		function fija()
		{
			var Scrolled=document.body.scrollTop;
			var Scrolled2=document.body.scrollLeft;
			Topscrolled=Scrolled;
			Topscrolled2=Scrolled2;
			Leftscrolled=document.body.scrollLeft;
			var Titulo=document.getElementById('Titulos');
			var Titulo2=document.getElementById('Titulos2');
			Titulo.style.top=Scrolled+".(strpos($Brw,'MSIE ')?100:80).";
			Titulo2.style.left=Scrolled2+0;
		}
		function grabascroll()
		{
			document.cookie='SC_TOP = '+Topscrolled+'; ';
			document.cookie='SC_LEFT = '+Leftscrolled+'; ';
		}

		function ae(Fecha,Vehiculo) // asignar estado
		{
			modal('zcontrol.php?Acc=asignar_estado&fecha='+Fecha+'&Vehiculo='+Vehiculo+'&FI=$FI&FF=$FF&OFICINA=$OFICINA',50,50,500,500,'Nestado');
		}

		function cierre1(Fecha,Id,Ids)  // cerrar un servicio
		{
			if(confirm('Desea concluir este servicio el dia '+Fecha+' ?'))
			{
				modal('zcontrol.php?Acc=cierra_servicio&id='+Id+'&fecha='+Fecha+'&FI=$FI&FF=$FF&OFICINA=$OFICINA',0,0,500,500,'cs');
				return;
			}
			else
			{
				if(confirm('Desea verificar/editar el siniestro?'))
				{
					eds(Ids);
				}
			}
			".($USUARIO<3?"me(Id);":"")."
		}

		function eds(Ids)
		{
			modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NT_siniestro&id='+Ids,0,0,600,600,'siniestro');
			return;
		}

		function cierre2(Fecha,Id)  // cerrar un mantenimiento  recibe la fecha y el id de la ubicacion
		{
			if(confirm('Desea cerrar este mantenimiento/fuera de servicio ?'))
			{
				modal('zcontrol.php?Acc=cierra_mantenimiento&id='+Id+'&fecha='+Fecha+'&FI=$FI&FF=$FF&OFICINA=$OFICINA',0,0,500,500,'cs');
				return;
			}
			else
			{
				if(confirm('Desea modificar las observaciones del mantenimiento?'))
				{
					modal('zcontrol.php?Acc=observaciones_mantenimiento&id='+Id+'&fecha='+Fecha+'&FI=$FI&FF=$FF&OFICINA=$OFICINA',0,0,500,500,'cs');
					return;
				}
			}
			".($USUARIO<3?"me(Id);":"")."
		}

		function asi(Id)
		{
			if(confirm('Desea asignar un siniestro a este servicio?'))
			{
				modal('zcontrol.php?Acc=reasigna_siniestro&id='+Id+'&FI=$FI&FF=$FF&OFICINA=$OFICINA',0,0,500,500,'cs');
				return;
			}
		}

		function me(Id)  // modifica la ubicación solo para los super usuarios  Administrador y Primario, tambien operativo.
		{
			modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NT_ubicacion&id='+Id,0,0,500,500,'Nestado');
		}

		</script>
	</head>
	<BODY leftmargin=0 onload=\" centrar();document.body.scrollTop=leerCookie('SC_TOP');
				document.body.scrollLeft=leerCookie('SC_LEFT');\"
				onunload=\"grabascroll();\" Onscroll='fija();' >
	".titulo_modulo("CUADRO DE CONTROL OPERATIVO - ".NOMBRE_APLICACION);
	echo "<form action='zcontrol.php' target='_self' method='post' name='forma' id='forma'>
			Fecha inicial: ".pinta_FC('forma','FI',$FI)."  Fecha final: ".pinta_FC('forma','FF',$FF)."
			Oficina: ".menu1("OFICINA","select id,nombre from oficina order by nombre",$OFICINA,1)." Ver todas las oficinas
			<input type='checkbox' name='todasoficinas'>
			Ocultar observaciones <input type='checkbox' name='Ocultaobs' ".($Ocultaobs?'checked':'').">
			<input type='submit' value='Ver'>
			</form>";
	cuadricula();
}

function cuadricula()
{
	global $FI,$FF,$OFICINA,$tControl,$tServicio,$Brw;
	$Fi1=$FI;
	$Ancho_celda=60;
	$Cantidad_dias=1;
	while(date('Ymd',strtotime($Fi1))<=date('Ymd',strtotime($FF)))
	{
		$Cantidad_dias++;
		$Fi1=aumentadias($Fi1,1);
	}
	$Ancho_total=($Cantidad_dias*($Ancho_celda+3))+50;

	q("drop table if exists $tControl ");
	q("drop table if exists $tServicio ");
	q("create table $tControl select ub.vehiculo,ub.id,ev.nombre as estado,ev.sigla,ev.color_co as cestado,ub.fecha_inicial,ub.fecha_final,
		ub.observaciones,ub.obs_mantenimiento,ofi.nombre as oficina,ub.estado as est,ub.odometro_inicial as odi,ub.odometro_final as odf,ub.odometro_diferencia as odif
		from ubicacion ub,estado_vehiculo ev, oficina ofi,vehiculo v
		where ub.fecha_inicial <= '$FF' and ub.fecha_final >= '$FI' and ub.estado=ev.id and ub.vehiculo=v.id
		and if(v.flota_aoa=1,ub.estado!=2,1)
		and ub.oficina=ofi.id ".($OFICINA?"and ofi.id=$OFICINA":""),1);
	q("create table $tServicio select sn.numero,sn.declarante_nombre as declarante ,esn.nombre as estado,sn.observaciones,sn.ubicacion,sn.id
		from siniestro sn, estado_siniestro esn
		where  sn.ubicacion in (select id from $tControl) and sn.estado=esn.id");
	q("alter table $tServicio add index llave (ubicacion)");
	q("alter table $tControl add index llave (vehiculo,fecha_inicial,fecha_final)",1);
	IF($Vehiculos=q("select id,placa,flota_aoa from vehiculo where
		(inactivo_desde='0000-00-00' || inactivo_desde>'$FI')  and id in (select distinct vehiculo from $tControl)  order by flota_aoa,placa "))
	{
		$Numero_vehiculos=mysql_num_rows($Vehiculos);
		$Alto_frame=$Numero_vehiculos*22;
		$Top1=(strpos($Brw,'MSIE ')?100:80);
		$Top2=(strpos($Brw,'MSIE ')?157:120);
		if(strpos($Brw,'Chrome') && $_SESSION['User']==1) $Top2+=5;
		//  TITULOS DE LAS FECHAS
		echo "<iframe style='position:Absolute;top:$Top1;left:0;z-index:101;border-width:0px;' id='Titulos' width='".($Ancho_total+10)."' HEIGHT='34' border=0 frameborder='no' scroll='no'
				src='zcontrol.php?Acc=pinta_titulos_fechas&Ancho_total=$Ancho_total&Ancho_celda=$Ancho_celda&FI=$FI&FF=$FF&tControl=$tControl&OFICINA=$OFICINA'></iframe>";
		// TITULOS DE LAS PLACAS
		echo "<iframe style='position:Absolute;top:$Top2;left:0;z-index:101;border-width:0px;' id='Titulos2' width='83' HEIGHT='$Alto_frame' border=0 frameborder='no' scroll='no'
				src='zcontrol.php?Acc=pinta_titulos_placas&tControl=$tControl&OFICINA=$OFICINA&FI=$FI'></iframe>";

		echo "<br><br><br><table border cellspacing=0 style='empty-cells:show;' width='$Ancho_total'>";
		require('inc/link.php');

		while($V=mysql_fetch_object($Vehiculos))
		{
			$Contenido='';
			$Contenido.="<tr><td width='76'>$V->placa</td>";
			$Contenido.=busca_estado($V,$tControl,$Ancho_celda,$tServicio,$LINK);
			$Contenido.="</tr>";
			echo $Contenido;
		}
		mysql_close($LINK);
		echo "</table>
		<br><br><font color='ffffff'>$Brw</font>";
	}
}

function busca_estado($Vehiculo,$tControl,$Ancho_celda,$tServicio,$LINK)
{
	global $FI,$FF,$OFICINA,$tControl,$USUARIO;
	$Resultado='';
	for($fecha=date('Ymd',strtotime($FI));date('Ymd',strtotime($fecha))<=date('Ymd',strtotime($FF));$fecha=aumentadias($fecha,1))
	{
		$Ac=(date('Ymd',strtotime($fecha))<date('Ymd',strtotime($FF))?$Ancho_celda:0);
		$Ac=$Ancho_celda;
		$fechab=date('Y-m-d',strtotime($fecha));
		if($Estado_SQL=mysql_query("select * from $tControl where vehiculo=$Vehiculo->id and '$fechab' between fecha_inicial and fecha_final ",$LINK))
		{
			$NR=mysql_num_rows($Estado_SQL);
			if($Estado=mysql_fetch_object($Estado_SQL)) ///  LO QUE SIGUE DEBE MOSTRARLO SI HAY UN REGISTRO EN LA TABLA DE UBICACIONES
			{
				if($NR==2)
				{
					$Estado2=mysql_fetch_object($Estado_SQL);
					$Resultado.="<td width='$Ac'><table cellspacing=0 cellpadding=0 width='100%'><tr>";
					$Resultado.=pinta_estado($Estado,$LINK,$fechab,$Vehiculo,$Ac/2);
					$Resultado.=pinta_estado($Estado2,$LINK,$fechab,$Vehiculo,$Ac/2);
					$Resultado.="</tr></table></td>";
				}
				elseif($fecha==date('Ymd') && $Estado->est!=1 /* 1= servicio*/)
				{
					$Resultado.="<td width='$Ac'><table cellspacing=0 cellpadding=0 width='100%'><tr>";
					$Resultado.=pinta_estado($Estado,$LINK,$fechab,$Vehiculo,$Ac/2);
					$Resultado.="<td width='50%' ";
					if($USUARIO!=4 /*call center*/ && $USUARIO!=8 /*aseguradora*/ )   /* asigna estado ae */
					{
						if($Vehiculo->flota_aoa==1)
							$Resultado.="ondblclick=\"alert('No se puede asignar ningun estado desde esta base de datos');\"";
						else
							$Resultado.="ondblclick=\"ae('$fecha',$Vehiculo->id);\"";
					}
					$Resultado.="></td></tr></table></td>";
				}
				elseif($Estado->fecha_final==$fechab)
				{
					$Resultado.=pinta_estado($Estado,$LINK,$fechab,$Vehiculo,$Ac);
				}
				else
				{
					$Cantidad_dias=dias($fecha,$Estado->fecha_final);
					if($Cantidad_dias>1 && $Estado->est!=1)
					{
						$Resultado.=pinta_estado($Estado,$LINK,$fechab,$Vehiculo,$Ac,true,$Cantidad_dias); /* dentro de un estado  no pinta los spam*/
						$fecha=aumentadias($fecha,($Cantidad_dias-1));
					}
					else
						$Resultado.=pinta_estado($Estado,$LINK,$fechab,$Vehiculo,$Ac,true); /* dentro de un estado  no pinta los spam*/
				}
			}
			else
			{
				$Resultado.="<td  width='$Ac' ";
				if($USUARIO!=4 /*call center*/ && $USUARIO!=8 /*aseguradora*/)  /* asigna estado */
				{
					if($Vehiculo->flota_aoa==1)
						$Resultado.="ondblclick=\"alert('No se puede asignar ningun estado desde esta base de datos');\"";
					else
						$Resultado.="ondblclick=\"ae('$fecha',$Vehiculo->id);\"";
				}
				$Resultado.="></td>";
			}
			#mysql_free_result($Estado_SQL);
		}
		else
		{
			$Resultado.="<td  width='$Ac' ";
			if($USUARIO!=4 /*call center*/ && $USUARIO!=8 /* aseguradora */)  /* asigna estado */
			{
				if($Vehiculo->flota_aoa==1)
					$Resultado.="ondblclick=\"alert('No se puede asignar ningun estado desde esta base de datos');\"";
				else
					$Resultado.="ondblclick=\"ae('$fecha',$Vehiculo->id);\"";
			}
			$Resultado.="></td>";
		}
	}
	return $Resultado;
}


function pinta_estado($E,$LINK,$fecha,$Vehiculo,$Ac,$Excluye=false,$Colspan=0)
{
	global $NT_ubicacion,$Color_servicio,$Color_concluido,$Color_extra,$NT_siniestro,$FI,$FF,$OFICINA,$tServicio,$USUARIO,$Ocultaobs;
	$Resultado2='';
	$SPlaca="<font color='green'><b>$Vehiculo->placa</b></font>";
	if($E->est==7 || $E->est==1)   ## SERVICIO CONCLUIDO O SERVICIO ACTIVO  BUSCA LOS DATOS DEL SINIESTRO
	{
		$SS=qom("Select concat('Número de Siniestro:',numero,'<br>Declarante:',declarante,'<br>Obs:',observaciones,'<br>Estado:',estado) as span,id from
					$tServicio where ubicacion=$E->id ",$LINK);
		$Span=$SS->span;
		$IDS=$SS->id;
		$Estado_concluido=false;
		if($E->est==7 && $fecha<$E->fecha_final)  // Servicio concluido y la fecha aun no es la final
		{
			$E->estado='SERVICIO';
			$E->sigla='Serv';
			$E->est=1;
			$E->cestado=(dias($fecha,$E->fecha_inicial)>7?$Color_extra:$Color_servicio);
			$Estado_concluido=true;
		}
		IF($E->est==1)  // En servicio
		{
			$E->cestado=(dias($fecha,$E->fecha_inicial)>7?$Color_extra:$Color_servicio);
			if($USUARIO!=4 /*call center*/ && $USUARIO!=8 /*aseguradora*/)
			{
				if($Span) /*si tiene servicio asignado */
				{
					if($Estado_concluido)
					{
						if($USUARIO==7 /* control operativo*/  && $Vehiculo->flota_aoa==0)
						{
							$ONCLICK="if(confirm('Ver/Modificar Ubicacion?')) me($E->id);else if(confirm('Ver/Modificar Siniestro?')) eds($IDS);"; /* editar la ubicacion desde control operativo*/
						}
						else
						{
							$ONCLICK="alert('Servicio ya concluido');";  /*servicio ya concluido, solo sale una alerta*/
						}
					}
					else
					{
						if($Vehiculo->flota_aoa==0)
							$ONCLICK="cierre1('$fecha',$E->id,$IDS);";  /*cierra servicio*/
						else
							$ONCLICK="alert('Si desea cerrar o modificar cualquier caracteristica de este servicio lo debe hacer desde la base de datos de AOA');";
					}
				}
				else
				{
					$ONCLICK="asi($E->id)"; /* asignar numero de siniestro */
				}
			}
			else $ONCLICK='';

			//  BUSQUEDA DE CAMBIO DE COLOR CUANDO SE PASA DE 7 DIAS
			$Dias=dias($fecha,$E->fecha_inicial);
			if($Dias>7)
			{
				$Color=$Color_extra;
				$Blink1="<font style='text-decoration:blink;'>";
				$Blink2="</font>";
				$Blink1="";
				$Blink2="";
				$Span.="<br><font color='red'><b>Este auto sobrepaso el limite de dias</b></font>";
			}
			elseif($fecha==$E->fecha_final && $fecha<=date('Y-m-d'))
			{
				$Color=$E->cestado;
				$Blink1="<font style='text-decoration:blink;'>";
				$Blink2="</font>";
				$Span.="<br><font color='red'><b>Este auto deberia ser devuelto en esta fecha</b></font>";
			}
			else
			{
				$Color=$E->cestado;
				$Blink1="";
				$Blink2="";
			}
		}
		$Resultado2.="<td style='cursor:pointer;' bgcolor='$E->cestado' width='$Ac' colspan='$Colspan' ";
		IF($USUARIO!=4 /*call center*/ && $USUARIO!=8 /* aseguradora*/)
		{
			$Resultado2.=" onclick=\"$ONCLICK\" ";
		}
		$Resultado2.=">";
		if(!$Excluye)  /* indicador si se excluyen o no las observaciones para hacer mas liviana la presentaicon */
		{
			if($Ocultaobs)
			{
				$Resultado2.=$Blink1.$E->sigla.$Blink2;
			}
			else
			{
				$Resultado2.="<a class='info' href='javascript:;'>".$Blink1.$E->sigla.$Blink2.
					"<span style='width:300px;'>$E->estado $SPlaca<br>OFICINA: $E->oficina<br>$Span<br>".
					"<br>Odometro inicial: $E->odi  Odometro Final: $E->odf ".
					($E->odif>700?"<font color='red'>Diferencia: $E->odif</font>":"Diferencia: $E->odif").'<br>'.
					($E->obs_mantenimiento?"<br>Obs. Conclusión: $E->obs_mantenimiento":"").
					($E->observaciones?"<br>Obs. Orden Servicio: $E->observaciones":"").
					"</span></a>";
			}
		}
		$Resultado2.="</td>";
	}
	elseif(($E->est==4 /*estado en mantenimiento */ || $E->est==5 /* estado fuera de servicio*/)
			 && ($USUARIO<=3  /*captura y administradores*/ || $USUARIO==10 /* operativo oficina */)&& $fecha==date('Y-m-d') ) /* si es usuario de captura debe poder cerrar el mantenimiento/fuera de servicio */
	{
		$Resultado2.="<td style='cursor:pointer;' bgcolor='$E->cestado' width='$Ac' colspan='$Colspan' ";
		$Resultado2.="onclick=\"cierre2('$fecha',$E->id);\""; // Cierre del Mantenimiento
		$Resultado2.=">";
		if(!$Excluye) /* indicador si se excluyen o no las observaciones para hacer mas liviana la presentaicon */
		{
			if($Ocultaobs)
			{
				$Resultado2.=$E->sigla;
			}
			else
			{
				$Resultado2.="<a class='info' href='javascript:;'>$E->sigla<span style='width:300px;'>$E->estado $SPlaca<br>OFICINA: $E->oficina<br>".
					"<br>Odometro inicial: $E->odi  Odometro Final: $E->odf ".
					($E->odif>700?"<font color='red'>Diferencia: $E->odif</font>":"Diferencia: $E->odif").'<br>'.
					($E->obs_mantenimiento?"<br>Obs. Conclusión: $E->obs_mantenimiento":"").
					($E->observaciones?"<br>Obs. Orden Servicio: $E->observaciones":"").
					"</span></a>";
			}
		}
		$Resultado2.="</td>";
	}
	else
	{
		$Resultado2.="<td style='cursor:pointer;' bgcolor='$E->cestado' width='$Ac' colspan='$Colspan'";
		IF($USUARIO!=4 /*call center*/ && $USUARIO!=8 /* aseguradora*/)
		{
			if($USUARIO<3 /*administradores*/)
			{
				$Resultado2.="ondblclick=\"me($E->id);\""; // modificacion de la ubicacion
			}
		}
		$Resultado2.=">";
		if(!$Excluye) /* indicador si se excluyen o no las observaciones para hacer mas liviana la presentaicon */
		{
			if($Ocultaobs)
			{
				$Resultado2.=$E->sigla;
			}
			else
			{
				$Resultado2.="<a class='info' href='javascript:;'>$E->sigla<span style='width:300px;'>$E->estado $SPlaca<br>OFICINA: $E->oficina<br>".
					"<br>Odometro inicial: $E->odi  Odometro Final: $E->odf ".
					($E->odif>700?"<font color='red'>Diferencia: $E->odif</font>":"Diferencia: $E->odif").'<br>'.
					($E->obs_mantenimiento?"<br>Obs. Conclusión: $E->obs_mantenimiento":"").
					($E->observaciones?"<br>Obs. Orden Servicio: $E->observaciones":"").
					"</span></a>";
			}
		}
		$Resultado2.="</td>";
	}
	return $Resultado2;
}

/*

                                                              FIN DE LA PRESENTACION DE LA CUADRICULA   ---------------------------------------------------------
*/

function cierra_servicio()
{
	global $id,$fecha,$FF,$FI,$OFICINA;
	html();
	echo "<body onload='centrar(600,600);'>".titulo_modulo("Cerrar Servicio");
	$U=qo("select * from ubicacion where id=$id");
	$Dias=dias($U->fecha_inicial,$U->fecha_final);
	echo "Oficina: <b>".qo1("select nombre from oficina where id=$U->oficina")." </b>
		Vehiculo: <b>".qo1("select placa from vehiculo where id=$U->vehiculo")." </b>
		Fecha de Entrega: <b>$U->fecha_inicial</b>
		<form action='zcontrol.php' method='post' target='_self' name='forma' id='forma'>
		<table cellspacing=0 border >
		<tr><td>Odómetro inicial: </td><td><input type='text' name='oi' value='$U->odometro_inicial' readonly style='background-color:eeeeee'></td></tr>
		<tr><td>Odómetro final: </td><td><input type='text' name='ofinal' id='ofinal' value='' onkeydown=\"verificanumero(event,'ofinal');\"
		onblur=\" if(!this.value)
		{
			alert('Debe digitar el odómetro final');
			document.forma.enviar.disabled=true;
			//document.forma.ofinal.focus();
		}
		".($Dias>7?"alert('Debe registrar en las observaciones la razón por la cual se superó el límite de uso en días.');":"")."
		var Odometrof=parseInt(document.forma.ofinal.value);
		if(Odometrof<$U->odometro_inicial)
		{
			alert('El odometro final no puede ser menor que el odometro inicial.');
			document.forma.ofinal.style.backgroundColor='ffbbbb';
			document.forma.difodometro.value=0;
			document.forma.enviar.disabled=true;
		}
		else
		{
			document.forma.ofinal.style.backgroundColor='ffffff';
			document.forma.difodometro.value=Odometrof-$U->odometro_inicial;
			if(document.forma.difodometro.value>700)
			{
				document.forma.difodometro.style.color='ff4444';
				alert('Debe registrar en las observaciones la razón por la cual el servicio superó el límite de uso en kilometros');
			}
			else
				document.forma.difodometro.style.color='000000';
			document.forma.enviar.disabled=false;
		}\"></td></tr>
		<tr><td>Diferencia Odometro: </td><td><input type='text' name='difodometro' id='difodometro' readonly style='background-color:eeeeee'></td></tr>
		<tr><td>Fecha de Devolución: </td><td><input type='text' name='fecha_devolucion' value='$fecha' readonly style='background-color:eeeeee' size=10></td></tr>
		<tr><td>Observaciones en la<br>Conclusión del servicio:</td><td>$U->obs_mantenimiento<br>
		<textarea name='obs_mantenimiento' id='obs_mantenimiento' style='font-size:11;font-family:arial' rows=3 cols=80></textarea></td></tr>
		<tr bgcolor='fffdddd'><td>Solicitud de Orden de Servicio:</td><td>$U->observaciones<br>
		<textarea name='observaciones' id='observaciones' style='font-size:11;font-family:arial' rows=3 cols=80></textarea></td></tr>
		</table>
		<center><input type='button' value='Concluir Servicio' name='enviar' id='enviar' style='font-weight:bold' disabled onclick=\"valida_campos('forma','ofinal:n,obs_mantenimiento')\"></center>
		<input type='hidden' name='Acc' value='concluir_servicio'>
		<input type='hidden' name='id' value='$id'>
		<input type='hidden' name='FI' value='$FI'>
		<input type='hidden' name='FF' value='$FF'>
		<input type='hidden' name='OFICINA' value='$OFICINA'>
		</form></body>";
}

function concluir_servicio()
{
	global $id,$ofinal,$fecha_devolucion,$observaciones,$obs_mantenimiento,$difodometro,$FI,$FF,$OFICINA,$Numero_Aseguradora;
	q("update siniestro set estado=8,obsconclusion=concat(obsconclusion,'$obs_mantenimiento'),fecha_final='$fecha_devolucion' where ubicacion=$id");
	q("update ubicacion set fecha_final='$fecha_devolucion',estado=7,odometro_final='$ofinal',odometro_diferencia='$difodometro',
		observaciones=concat(observaciones,' $observaciones') ,obs_mantenimiento=concat(obs_mantenimiento,' $obs_mantenimiento') where id=$id");
	q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."','".date('s')."','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','ubicacion','M','$id','".$_SERVER['REMOTE_ADDR']."','Concluye Servicio')");
	$UB=qo("select * from ubicacion where id=$id");
	$SN=qo("select * from siniestro where ubicacion=$id");
	q("update aoacol_aoacars.siniestro set estado=8, obsconclusion=\"$SN->obsconclusion\" ,fecha_final='$fecha_devolucion' where numero='$SN->numero'
	and aseguradora='$Numero_Aseguradora' ");
	///  BUSCA ESTADOS POSTERIORES DE ESTE MISMO VEHICULO PARA VERIFICAR LA ACTUALIZACION AUTOMATICA DE ODOMETROS
	q("update ubicacion set odometro_inicial='$ofinal', odometro_final='$ofinal' where vehiculo='$UB->vehiculo' and fecha_inicial>='$fecha_devolucion' ");
	////------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	if($observaciones)
	{
		echo "<script language='javascript'>
			function  carga()
			{
				document.forma.submit();
			}
			</script><body onload='carga()'>
			<form action='../aoa/zorden_servicio.php' method='post' target='_self' name='forma' id='forma'>
			<input type='hidden' name='descripcion' value='$observaciones'>
			<input type='hidden' name='idub' value='$UB->id'>
			</form>
			</body>";
	}
	else
	{
		echo "<body onload=\"window.close();void(null);opener.location='zcontrol.php?FI=$FI&FF=$FF&OFICINA=$OFICINA';\"></body>";
	}
}

function pinta_titulos_fechas()
{
	global $Ancho_celda,$Ancho_total,$FI,$FF,$tControl,$OFICINA;
	html();
	echo "<body leftmargin=0 topmargin=0 rightmargin=0 bottommargin=0 bgcolor='#eeeeff'>
	<TABLE cellspacing=0 BORDER width='$Ancho_total' style='empty-cells:show;'>";
	echo "<tr><td width='76'><b>Placa</b></td>";
	require('inc/link.php');
	for($fecha=date('Ymd',strtotime($FI));date('Ymd',strtotime($fecha))<=date('Ymd',strtotime($FF));$fecha=aumentadias($fecha,1))
	{
		echo "<td align='center' width='$Ancho_celda'><a style='cursor:pointer;'
			onclick=\"modal2('zcalcula_diario.php?".($OFICINA?"ID=2&FECHA=$fecha&OFICINA=$OFICINA":"ID=1&FECHA=$fecha")."',20,20,500,500,'ed');\">
			<b>$fecha</b></a></td>";
	}
	mysql_close($LINK);
	echo "</tr></table></body>";
}

#modal2('reporte.php?".($OFICINA?"ID=2&FECHA=$fecha&OFICINA=$OFICINA":"ID=1&FECHA=$fecha")."',20,20,500,500,'ed');

function pinta_titulos_placas()
{
	html();
	global $NT_ubicacion,$OFICINA,$tControl,$FI;
	echo "<body leftmargin=0 topmargin=0 rightmargin=0 bottommargin=0 bgcolor='#eeeeff'>";
	IF($Vehiculos=q("select id,placa,flota_aoa from vehiculo where
	(inactivo_desde='0000-00-00' || inactivo_desde>'$FI') and id in (select distinct vehiculo from $tControl) order by flota_aoa,placa"))
	{
		echo "<table border cellspacing=0 width='82' style='empty-cells:show;'>";
		while($V=mysql_fetch_object($Vehiculos))
		{
			echo "<tr><td width='82' ".($V->flota_aoa?"bgcolor='444455' alt='FLOTA DE AOA' title='FLOTA DE AOA'":"").">
					<a style='cursor:pointer;".($V->flota_aoa?"color:ffffff;":"")."'
					onclick=\"modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$NT_ubicacion&VINCULOC=vehiculo&VINCULOT=$V->id',0,0,500,1100,'Ubicaciones');\">
					<b>$V->placa</b></a></td></tr>";
		}
		echo "</table>";
	}
}

function asignar_estado()
{
	global $Vehiculo,$fecha,$FI,$FF,$OFICINA,$Numero_Aseguradora;
	$fecha_final=aumentadias($fecha,7);
	html();
	echo "<script language='javascript'>
	var Validar_obs=false;
	var Validar_numsiniestro=false;
	var Enviar_formulario=true;
	</script><body>".titulo_modulo("Asignar Estado");
	$Placa=qo1("select placa from vehiculo where id=$Vehiculo");
	$Ult=qo("select * from ubicacion where vehiculo=$Vehiculo and fecha_final<='$fecha' order by fecha_final desc limit 1");
	if($Ult->estado==1)
	{
		echo "<h3 align='center'><font color='red'><b>No puede asignar un nuevo servicio sin concluir el anterior</b></font></h3></body>";
		die();
	}
	echo "<form action='zcontrol.php' target='_self' method='post' name='mod' id='mod'>
		<input type='hidden' name='Acc' value='asignar_estado_grabar'>
		<table border cellspacing=0 width='90%'>
		<tr><td align='right'>Vehículo</td><td>$Placa</td>";
	if($Ult)
	{
		$FP1=aumentadias($Ult->fecha_final,1);
		$FP2=aumentadias($fecha,-1);
		if(strtotime($FP1)>strtotime($FP2)) {$FP1='';$FP2='';}
		echo "<tr><td align='right'>Fechas de parqueo</td><td colspan=3>".pinta_FC('mod','FP1',$FP1)." - ".pinta_FC('mod','FP2',$FP2)."</td></tr>";
	}
	echo "<td align='right'>Oficina</td><td>".menu1("oficina","select id,nombre from oficina order by nombre",($Ult?$Ult->oficina:0),1)."</td></tr>
		<tr><td align='right'>Fecha Inicial</td><td>";
	if($_SESSION['User']<3)
		echo pinta_FC('mod','fecha_inicial',date('Y-m-d',strtotime($fecha)));
	else
	{
		$un_dia_atras=date('Y-m-d',strtotime(aumentadias($fecha,-1)));
		$un_dia_atrasf=date('Y-m-d',strtotime(aumentadias($fecha_final,-1)));
		echo "<input type='button' value='1<' onclick=\"document.mod.fecha_inicial.value='$un_dia_atras';document.mod.fecha_final.value='$un_dia_atrasf';\">
		<input type='text' name='fecha_inicial' size=10 value='".date('Y-m-d',strtotime($fecha))."' readonly>";
	}
	echo "</td>
		<td align='right'>Fecha final</td><td>".pinta_FC('mod','fecha_final',date('Y-m-d',strtotime($fecha_final)))."</td></tr>
		<tr><td align='right'>Estado</td><td>".menu1('estado','select id,nombre from estado_vehiculo where id in (2,4,5,6) order by id',0,1,'',
		"onchange=\"if(this.value==1 || this.value==7) {muestra('capa_Contrato');muestra('control_odometros');Validar_numsiniestro=true;}
		if(this.value==6) {alert('Indique en las observaciones de donde a donde se traslado el vehiculo');
		muestra('control_odometros');}
		if(this.value==5) {alert('Indique en las observaciones la causa del fuera de servicio');muestra('control_odometros');
		this.form.odometro_devolucion.value=this.form.odometro_entrega.value;}
		if(this.value==2) {muestra('control_odometros');
		this.form.odometro_devolucion.value=this.form.odometro_entrega.value;}
		\"")."</td></tr>
		</table>";
		capa('capa_Contrato',1,'Relative','');
	if($Siniestro_Cita=qo1("select s.numero from aoacol_aoacars.cita_servicio  c,aoacol_aoacars.siniestro s where c.siniestro=s.id and s.aseguradora=$Numero_Aseguradora
		and c.placa='$Placa' and c.fecha='".date('Y-m-d',strtotime($fecha))."' and oficina=$Ult->oficina "))
	{
		$idSiniestro=qo1("select id from siniestro where numero='$Siniestro_Cita'");
	}
	else
		$idSiniestro=0;
	echo "<table border cellspacing=0 width='90%'><tr><th colspan=6>Datos del Contrato</th></tr>
		<tr><td align='right'>Siniestro</td><td colspan=5>".
		menu1("siniestro","select id,concat(numero,' ',declarante_nombre) from siniestro sn where estado=3 and ubicacion=0 order by numero ",$idSiniestro,1,'')."</td></tr></table>";
		fincapa();
		capa('control_odometros',1,'Relative','');
		echo "<table border cellspacing=0 width='90%'><tr><th colspan=6>Control de Odometro</th></tr>
		<tr><td align='right'>Odometro Entrega</td><td><input type='text' name='odometro_entrega' value='".($Ult?$Ult->odometro_final:0)."'size=10 maxlength=10
			onblur=\"if(this.value!=$Ult->odometro_final) {alert('Debe justificar la razón por qué el odómetro inicial no concuerda');
			Validar_obs=true;muestra('capa_obs');document.mod.observaciones.focus();}
			if(Number(this.form.odometro_devolucion.value)<Number(this.value)) this.form.odometro_devolucion.value=this.value;
			document.mod.odometro_diferencia.value=document.mod.odometro_devolucion.value-document.mod.odometro_entrega.value;\"></td>
		<td align='right'>Devolución</td><td><input type='text' name='odometro_devolucion' size=10 maxlength=10
			onblur=\"if(Number(this.value)<Number(this.form.odometro_entrega.value)) {alert('el odometro final no puede ser menor que el odometro inicial');
			this.value=this.form.odometro_entrega.value;}
			document.mod.odometro_diferencia.value=document.mod.odometro_devolucion.value-document.mod.odometro_entrega.value;\"></td>
		<td align='right'>Diferencia</td><td><input type='text' name='odometro_diferencia' size=10 maxlength=10 readonly></td>
		</tr>
		</table>";
		fincapa();

		echo "<table border cellspacing=0 width='90%'><tr><th colspan=2>Observaciones</th></tr>
		<tr><td align='right'>Observaciones:</td><td><textarea name='observaciones' style='font-family:arial;font-size:11' rows=3 cols=50></textarea></td>
		<tr><td>Mantenimiento y<br>Reparaciones:</td><td><textarea name='obs_mantenimiento' id='obs_mantenimiento' style='font-size:11;font-family:arial' rows=3 cols=50></textarea></td></tr>
		</tr></table>";
		echo "<center>
		<input type='button' value='Grabar' onclick=\"
		Enviar_formulario=true;
		if(!document.mod.estado.value)
		{
			Enviar_formulario=false;alert('Debe seleccionar un estado');
		}
		if(Validar_obs)
		{
			if(!document.mod.observaciones.value)
			{
				Enviar_formulario=false;
				alert('No ha justificado debidamente la razón por la cual el odómetro inicial no concuerda');
				document.mod.observaciones.focus();
			}
		}
		if(Validar_numsiniestro)
		{
			if(!document.mod.siniestro.value)
			{
				Enviar_formulario=false;
				alert('Debe seleccionar un numero de siniestro');
				document.mod.siniestro.focus();
			}
		}
		if(Afecha(document.mod.fecha_inicial.value)>Afecha(document.mod.fecha_final.value))
		{
			Enviar_formulario=false;
			alert('No puede asignar una fecha final menor que la fecha inicial');
		}
		if(Enviar_formulario)
		{
			this.form.submit();
		}\"></center>
		<input type='hidden' name='vehiculo' value='$Vehiculo'>
		<input type='hidden' name='FI' value='$FI'>
		<input type='hidden' name='FF' value='$FF'>
		<input type='hidden' name='OFICINA' value='$OFICINA'>
		</form></body>";
}

function asignar_estado_grabar()
{
	global $vehiculo,$oficina,$fecha_inicial,$fecha_final,$estado,$siniestro,$odometro_entrega,$odometro_devolucion,
			$FI,$FF,$FP1,$FP2,$observaciones,$obs_mantenimiento,$odometro_diferencia,$OFICINA,$Numero_Aseguradora;
	if($estado==7  /* servicio */)
	{
		$Dsin=qo("select img_cedula_f,img_pase_f,numero_voucher,numero_voucher1,numero_voucher2 from siniestro where id=$siniestro");
		if(!($Dsin->img_cedula_f && $Dsin->img_pase_f && ($Dsin->numero_voucher!='' || $Dsin->numero_voucher1!='' || $Dsin->numero_voucher2!='')))
		{
			html();
			echo "
				<script language='javascript'>
					function carga()
					{
						centrar(10,10);
						alert('El registro del siniestro esta sin imagenes de documentos o numero de voucher. Aun no se puede pasar a servicio');
						window.close();void(null);
					}
				</script>
				<body onload=carga()></body>";
		}
	}
	// busca la ultima ubicacion para actualizar la fecha final con la fecha inicial del nuevo estado
	if($Ultimo=qo("select * from ubicacion where vehiculo=$vehiculo and fecha_final>'$fecha_inicial' and estado=2"))
	{
		if($Ultimo->fecha_inicial==$fecha_inicial) // si la fecha inicial y final coinciden dentro del mismo dia del cambio del nuevo estado, se elimina ese estado
			q("delete from ubicacion where id=$Ultimo->id");
		else
			q("update ubicacion set fecha_final='$fecha_inicial' where id=$Ultimo->id");
	}
	// Inserta la nueva ubicación.
	$IDU=q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,observaciones,obs_mantenimiento,odometro_diferencia) values
			('$oficina','$vehiculo','$fecha_inicial','$fecha_final','$estado','$odometro_entrega','$odometro_devolucion','$observaciones','$obs_mantenimiento','$odometro_diferencia')");
	// inserta la bitacora de la ubicacion
	q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."','".date('s')."','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','ubicacion','A','$IDU','".$_SERVER['REMOTE_ADDR']."')");
	if(($estado==1 /*No adjudicado */ || $estado==7 /* servicio */) && $siniestro)
	{
		q("update siniestro set ubicacion=$IDU,estado=7,fecha_inicial='$fecha_inicial',fecha_final='$fecha_final' where id=$siniestro ");
		// actualiza el siniestro en AOA cars si existe un siniestro de igual numero y de la misma empresa
		$Numero_Siniestro=qo1("select numero from siniestro where id=$siniestro");
		q("update aoacol_aoacars.siniestro set estado=7, fecha_inicial='$fecha_inicial',fecha_final='$fecha_final' where numero='$Numero_Siniestro' and aseguradora=$Numero_Aseguradora ");
		// inserta la bitacora del siniestro
		q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."','".date('s')."','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','siniestro','M','$siniestro','".$_SERVER['REMOTE_ADDR']."','Asigna Servicio')");
	}
	if($FP1 && $FP2)
	{
		#SI SE ENVIAN FECHAS DE PARQUEO, SE INSERTA UNA UBICACION DE PARQUEO CON ESOS DATOS
		$IDP=q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final) values
			('$oficina','$vehiculo','$FP1','$FP2',2,'$odometro_entrega','$odometro_entrega')");
		q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."','".date('s')."','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','ubicacion','A','$IDP','".$_SERVER['REMOTE_ADDR']."','Asigna Servicio')");
	}
	echo "<body onload=\"window.close();void(null);opener.location='zcontrol.php?FI=$FI&FF=$FF&OFICINA=$OFICINA';\"></body>";
}

function reasigna_siniestro()
{
	global $id,$FI,$FF,$OFICINA;
	html();
	$U=qo("Select * from ubicacion where id=$id");
	$Placa=qo1("select placa from vehiculo where id=$U->vehiculo");
	$Oficina=qo1("select nombre from oficina where id=$U->oficina");
	echo "<body>".titulo_modulo("Asignación de siniestro");
	echo "<form action='zcontrol.php' method='post' target='_self' name='cap' id='cap'>
		Vehículo <b>$Placa</b> Oficina: <b>$Oficina</b><br>
		Fechas de: <b>$U->fecha_inicial</b> a <b>$U->fecha_final</b><br>
		Odómetro: <b>$U->odometro_inicial</b> - <b>$U->odometro_final</b> : <b>$U->odometro_diferencia</b><br>
		Observaciones: $U->observaciones<br><br>
		Por favor seleccione el siniestro que necesita asignar a este servicio:<br>
		".menu1("siniestro","select id,concat(numero,' ',declarante_nombre) from siniestro sn where estado=3 and ubicacion=0 order by numero ",0,1,'')."<br>
		<br><input type='hidden' name='id' value='$id'>
		<br><input type='hidden' name='Acc' value='reasigna_siniestro_ok'>
		<input type='hidden' name='FI' value='$FI'>
		<input type='hidden' name='FF' value='$FF'>
		<input type='hidden' name='OFICINA' value='$OFICINA'>
		<input type='submit' value='ASIGNAR'>
		</form></body>";
}

function reasigna_siniestro_ok()
{
	global $id,$siniestro,$FI,$FF,$OFICINA;
	$Ub=qo("select * from ubicacion where id=$id");
	q("update siniestro set ubicacion=$id,fecha_inicial='$Ub->fecha_inicial',fecha_final='$Ub->fecha_final' where id=$siniestro");
	echo "<body onload=\"window.close();void(null);opener.location='zcontrol.php?FI=$FI&FF=$FF&OFICINA=$OFICINA';\"></body>";
}

function cierra_mantenimiento()
{
	global $id,$fecha,$FF,$FI,$OFICINA;
	html();
	$U=qo("select * from ubicacion where id=$id");
	echo "<script language='javascript'>
		function vc_mant() // validacion cierre de mantenimiento
		{
			var Ofinal=Number(document.forma.ofinal.value);
			if(Ofinal<=0)
			{
				alert('Debe escribir un valor válido en el odómetro final');
				document.forma.enviar.disabled=true;
				return false;
			}
			if(Ofinal<$U->odometro_inicial)
			{
				alert('El odómetro final no puede ser menor que el odómetro inicial');
				document.forma.enviar.disabled=true;
				return false;
			}
			if(!document.forma.obs_mantenimiento.value)
			{
				alert('Debe escribir alguna observación con respecto al mantenimiento');
				document.forma.enviar.disabled=true;
				return false;
			}
			if(confirm('Está seguro de cerrar el mantenimiento con la información capturada?'))
			{
				document.forma.submit();
			}
		}

		function validar_odofinal()
		{
			var Ofinal=Number(document.forma.ofinal.value);
			if(Ofinal<=0)
			{
				alert('Debe escribir un valor válido en el odómetro final');
				document.forma.enviar.disabled=true;
				return false;
			}
			if(Ofinal<$U->odometro_inicial)
			{
				alert('El odómetro final no puede ser menor que el odómetro inicial');
				document.forma.ofinal.style.backgroundColor='ffbbbb';
				document.forma.difodometro.value=0;
				document.forma.enviar.disabled=true;
				return false;
			}
			document.forma.ofinal.style.backgroundColor='ffffff';
			document.forma.difodometro.value=Ofinal-$U->odometro_inicial;
			document.forma.difodometro.style.color='000000';
			document.forma.enviar.disabled=false;
		}
	</script>
	<body onload='centrar(500,450);'>".titulo_modulo("Cerrar Mantenimiento/Fuera de servicio");

	$Dias=dias($U->fecha_inicial,$U->fecha_final);
	$fecha_conclucion=aumentadias($fecha,-1);
	echo "Oficina: <b>".qo1("select nombre from oficina where id=$U->oficina")." </b>
		Vehiculo: <b>".qo1("select placa from vehiculo where id=$U->vehiculo")." </b>
		Fecha Inicial del mantenimiento: <b>$U->fecha_inicial</b>
		<form action='zcontrol.php' method='post' target='_self' name='forma' id='forma'>
		<table cellspacing=0>
		<tr><td>Odómetro inicial: </td><td><input type='text' name='oi' value='$U->odometro_inicial' readonly style='background-color:eeeeee'></td></tr>
		<tr><td>Odómetro final: </td><td><input type='text' name='ofinal' id='ofinal' value='' onkeydown=\"verificanumero(event,'ofinal');\"
		onblur='validar_odofinal();'></td></tr>
		<tr><td>Diferencia Odometro: </td><td><input type='text' name='difodometro' id='difodometro' readonly style='background-color:eeeeee'></td></tr>
		<tr><td>Fecha de finalización del mantenimiento: </td><td><input type='text' name='fecha_devolucion' value='$fecha_conclucion' style='background-color:eeeeee' size=10></td></tr>
		<tr><td>Observaciones:</td><td><textarea name='observaciones' id='observaciones' style='font-size:11;font-family:arial' rows=3 cols=50>$U->observaciones</textarea></td></tr>
		<tr><td>Mantenimiento y<br>Reparaciones:</td>
		<td><textarea name='obs_mantenimiento' id='obs_mantenimiento' style='font-size:11;font-family:arial' rows=3 cols=50
			onblur=>$U->obs_mantenimiento</textarea></td></tr>
		</table>
		<center><input type='button' value='Concluir Mantenimiento' name='enviar' id='enviar' style='font-weight:bold' disabled onclick='vc_mant();'></center>
		<input type='hidden' name='Acc' value='concluir_mantenimiento'>
		<input type='hidden' name='id' value='$id'>
		<input type='hidden' name='FI' value='$FI'>
		<input type='hidden' name='FF' value='$FF'>
		<input type='hidden' name='fecha' id='fecha' value='$fecha' >
		<input type='hidden' name='OFICINA' value='$OFICINA'>
		</form></body>";
}

function concluir_mantenimiento()
{
	global $id,$ofinal,$fecha_devolucion,$observaciones,$obs_mantenimiento,$difodometro,$FI,$FF,$OFICINA;
	global $fecha; // fecha para el estado de parqueadero
	$U=qo("select * from ubicacion where id=$id");
	q("update ubicacion set fecha_final='$fecha_devolucion',odometro_final='$ofinal',odometro_diferencia='$difodometro',
		observaciones=concat(observaciones,' $observaciones') ,obs_mantenimiento=concat(obs_mantenimiento,' $obs_mantenimiento') where id=$id");
	q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."','".date('s')."','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','ubicacion','M','$id','".$_SERVER['REMOTE_ADDR']."','Concluye Mantenimiento')");
	q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones,estado) values
							($U->oficina,$U->vehiculo,'$fecha','$fecha',$ofinal,$ofinal,0,'Creación de estado automática',2)");
	echo "<body onload=\"window.close();void(null);opener.location='zcontrol.php?FI=$FI&FF=$FF&OFICINA=$OFICINA';\"></body>";
}


function observaciones_mantenimiento()
{
	global $id,$fecha,$FF,$FI,$OFICINA;
	html();
	$U=qo("select * from ubicacion where id=$id");
	echo "
	<head>
	<script language='javascript'>
	function vc_mant() // validacion cierre de mantenimiento
	{
		if(!alltrim(document.forma.observaciones.value))
		{
			alert('Debe escribir alguna observación con respecto al mantenimiento');
			return false;
		}
		document.forma.submit();
	}
	</script>
	</head>
	<body onload='centrar(500,450);'>".titulo_modulo("Cerrar Mantenimiento/Fuera de servicio");

	echo "Oficina: <b>".qo1("select nombre from oficina where id=$U->oficina")." </b>
		Vehiculo: <b>".qo1("select placa from vehiculo where id=$U->vehiculo")." </b>
		Fecha Inicial del mantenimiento: <b>$U->fecha_inicial</b>
		<form action='zcontrol.php' method='post' target='_self' name='forma' id='forma'>
		<table cellspacing=0>
		<tr><td>Odómetro inicial:</td><td><input type='text' name='odometro_inicial' value='$U->odometro_inicial' class='numero' size='10' maxlength='10' ".($U->odometro_inicial!=0?"readonly":"")." ></td></tr>
		<tr><td>Observaciones:</td><td><textarea name='observaciones' id='observaciones' style='font-size:11;font-family:arial' rows=10 cols=50></textarea></td></tr>
		</table>
		<center><input type='button' value='Grabar Observaciones' name='enviar' id='enviar' style='font-size:14;font-weight:bold' onclick='vc_mant();'></center>
		<input type='hidden' name='Acc' value='observaciones_mantenimiento_ok'>
		<input type='hidden' name='id' value='$id'>
		<input type='hidden' name='FI' value='$FI'>
		<input type='hidden' name='FF' value='$FF'>
		<input type='hidden' name='OFICINA' value='$OFICINA'>
		</form></body>";
}

function observaciones_mantenimiento_ok()
{
	global $id,$observaciones,$FI,$FF,$OFICINA,$odometro_inicial;
	$U=qo("select * from ubicacion where id=$id");
	q("update ubicacion set obs_mantenimiento=concat(obs_mantenimiento,' $observaciones'),odometro_inicial='$odometro_inicial' where id=$id");
	q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."','".date('s')."','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','ubicacion','M','$id','".$_SERVER['REMOTE_ADDR']."','Observaciones,odometro_inicial')");
	echo "<body onload=\"window.close();void(null);opener.location='zcontrol.php?FI=$FI&FF=$FF&OFICINA=$OFICINA';\"></body>";
}


?>