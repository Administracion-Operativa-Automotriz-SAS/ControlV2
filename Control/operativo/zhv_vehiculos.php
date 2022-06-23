<?php

/**
 *   CONTROL DE HOJAS DE VIDA DE VEHICULOS TODAS LAS FLOTAS
 *
 * @version $Id$
 * @copyright 2010
 */

include('inc/funciones_.php');
sesion();
set_time_limit(0);
$USUARIO=$_SESSION['User'];
$TV='tmpi_hvehiculo_'.$USUARIO.'_'.$_SESSION['Id_alterno'];
$TH='tmpi_hisveh_'.$USUARIO.'_'.$_SESSION['Id_alterno'];
$Hoy=date('Y-m-d');
if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}

inicio_hv();

function inicio_hv()
{
	global $Filtro,$Filtro_Aseguradora,$Filtro_Oficina,$Filtro_marca,$TV,$TH,$Hoy;
	html(TITULO_APLICACION.' - HV.VEHICULOS - '.$_SESSION['Nombre']);
	q("drop table if exists $TV");
	q("create table $TV
				select v.id,v.placa,6 as aseguradora,aoacol_aoacars.t_aseguradora(6) as naseguradora,concat(aoacol_aoacars.t_marca_vehiculo(l.marca),' ',l.nombre) as nmarca,
				v.numero_motor,v.numero_serie,v.numero_chasis,l.marca as idmarca
			from aoacol_aoacars.vehiculo v,aoacol_aoacars.linea_vehiculo l where l.id=v.linea
			and (inactivo_desde='00000-00-00' or inactivo_desde>'$Hoy')
	order by placa ");
	q("drop table if exists $TH");
	//  campos: placa,noficina,fecha_inicial,fecha_final,odometro_inicial,odometro_final,obserbaciones,obs_mantenimiento,aseguradora,nestado

	q("create table $TH
		select v.placa,o.nombre as noficina,u.fecha_inicial,u.fecha_final,u.odometro_inicial,u.odometro_final,u.observaciones,u.obs_mantenimiento,
			6 as aseguradora,aoacol_aoacars.t_estado_vehiculo(u.estado) as nestado,u.id
			from aoacol_aoacars.vehiculo v,aoacol_aoacars.ubicacion u,aoacol_aoacars.oficina o
			where u.vehiculo=v.id and u.oficina=o.id
		ORDER BY placa,fecha_inicial desc,odometro_inicial desc");

	$Vehiculos=q("Select distinct v.* from $TV v,$TH h where h.placa=v.placa ".
		($Filtro?"and (observaciones like '%$Filtro%' or obs_mantenimiento like '%$Filtro%')":"").
		($Filtro_Aseguradora?" and v.aseguradora=$Filtro_Aseguradora ":"").
		($Filtro_marca?" and v.nmarca='$Filtro_marca' ":"").
		"order by placa");

	echo "<script language='javascript'>
			function historia_placa(placa)
			{
				modal('zhv_vehiculos.php?Acc=historia_vehiculo&placa='+placa,0,0,500,800,'hp');
			}
			function historia_fotografica(placa)
			{
				modal('zhv_vehiculos.php?Acc=historia_fotografica&placa='+placa,0,0,500,800,'hf');
			}
			function novedades_placa(placa)
			{
				modal('zhv_vehiculos.php?Acc=novedades_placa&placa='+placa,0,0,500,800,'np');
			}
			function eventos_futuros(placa)
			{
				modal('zhv_vehiculos.php?Acc=eventos_futuros&placa='+placa,0,0,500,800,'fp');
			}

			function recargar()
			{
				var F='';
				var F1=document.getElementById('Filtro').value;
				var F2=document.getElementById('Filtro_Aseguradora').value;
				var F3=document.getElementById('Filtro_Oficina').value;
				var F4=document.getElementById('Filtro_marca').value;
				window.open('zhv_vehiculos.php?Acc=inicio_hv&Filtro='+F1+'&Filtro_Aseguradora='+F2+'&Filtro_Oficina='+F3+'&Filtro_marca='+F4,'_self');
			}

			function adiciona_mantenimiento(Placa)
			{
				modal('zhv_vehiculos.php?Acc=adiciona_mantenimiento&Placa='+Placa,0,0,400,700,'am');
			}

			function adiciona_mantenimientof(Placa)
			{
				modal('zhv_vehiculos.php?Acc=adiciona_mantenimientof&Placa='+Placa,0,0,400,700,'am');
			}

			function adiciona_soat(Placa)
			{
				modal('zhv_vehiculos.php?Acc=adiciona_soat&Placa='+Placa,0,0,400,700,'am');
			}

			</script><body bgcolor='ffffff'>
			Convenciones: ";
			$Convenciones=q("select * from estado_vehiculo order by id");
			while($Con=mysql_fetch_object($Convenciones))
			{
				echo "<span style='background-color:$Con->color_co;color:00000;font-weight:bold;'>$Con->nombre</span>&nbsp;";
			}
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='#Ayuda'><font color='blue'>Click aquí para visualizar la ayuda correspondiente a esta herramienta.</font></a>
			<hr>Filtro en observaciones: <input type='text' name='Filtro' id='Filtro' value='$Filtro'> <input type='button' value='Aplicar' onclick='recargar()'>
			<br>Aseguradora: ".menu1("Filtro_Aseguradora","Select id,nombre from aseguradora",$Filtro_Aseguradora,1,''," onchange='recargar();' ")."
			Oficina: ".menu1("Filtro_Oficina","Select id,nombre from oficina",$Filtro_Oficina,1,''," onchange='recargar();' ");
			echo " Marca: ".menu1("Filtro_marca","select distinct nmarca,nmarca from $TV order by nmarca",$Filtro_marca,1,''," onchange='recargar()' ");

	if($Vehiculos)
	{
		echo "<hr><table border cellspacing=0><tr>
				<th>#</th>
				<th>Placa</th>
				<th>No. Motor</th>
				<th>No. Serie</th>
				<th>No. Chasis</th>
				<th>Aseguradora Actual</th>
				<th>Oficina</th>
				<th>Marca</th>
				<th>Kilometraje</th>
				<th>Historia</th>
				<th>Próximo.Mant.</th>
				<th>Próximo.SOAT</th>
				<th>Ver Novedades</th>
				</tr>";
		require('inc/link.php');$Contador=1;
		while($V=mysql_fetch_object($Vehiculos))
		{
//			$Base_aseguradora=base_aseguradora($V->aseguradora);
			$DV=qom("select if(u.odometro_final>0,u.odometro_final,u.odometro_inicial) as odo,t_oficina(u.oficina) as noficina,u.oficina as idofi,e.color_co as color
							from ubicacion u,estado_vehiculo e
							where u.vehiculo=$V->id and u.estado=e.id and u.fecha_inicial<='$Hoy'
							order by u.odometro_inicial desc,u.fecha_final desc limit 1",$LINK);
			$DVF=qo1m("select count(*) as cantidad from $TH	where placa='$V->placa' and (fecha_inicial>='$Hoy'  or fecha_final>='$Hoy') and nestado='EN MANTENIMIENTO' order by fecha_final ",$LINK);
			if($Filtro_Oficina) {if($Filtro_Oficina==$DV->idofi) $Sale=true; else $Sale=false;} else $Sale=true;
			if($Sale)
			{
				echo "<tr><td>$Contador</td><td>$V->placa</td>
					<td>$V->numero_motor</td>
					<td>$V->numero_serie</td>
					<td>$V->numero_chasis</td>
					<td>$V->naseguradora</td><td>$DV->noficina</td>
					<td>$V->nmarca</td>
					<td align='right'>".coma_format($DV->odo)."</td>
					<td align='center' bgcolor='$DV->color'>
					<a href=\"javascript:historia_placa('$V->placa');void(null);\" alt='Ver historia de $V->placa' title='Ver historia de $V->placa'><img src='gifs/standar/Preview.png' border='0'></a>
					<a href=\"javascript:historia_fotografica('$V->placa');void(null);\" alt='Ver historia fotográfica de $V->placa' title='Ver historia fotográfica de $V->placa'>
					<img src='gifs/camara.png' border='0'></a></td>";
				pinta_estadov($V->placa,$DV->odo,$DVF,$LINK); // estado del mantenimiento general
				pinta_estadov3($V->placa,$LINK);  // estado del vencimiento del soat
				echo "<td align='center'>
					<a href=\"javascript:novedades_placa('$V->placa');void(null);\" alt='Ver novedades de $V->placa' title='Ver novedades de $V->placa'><img src='gifs/tv.png' border='0'></a></td></tr>";
				$Contador++;
			}
		}
		mysql_close($LINK);
		echo "</table>
			<hr color='blue'>
			<a name='Ayuda'><br /><br />
			<i><b>INSTRUCCIONES:</b><br><br />Este listado se puede filtrar usando el <B>Filtro en observaciones</b>, escribiendo en la casilla alguna palabra o parte de la palabra y al darle <b>Aplicar</b> el sistema
			utilizará esta palabra como criterio de búsqueda en toda la historia de cada uno de los vehículos.<br><br />
			Adicionalmente se puede combinar el filtro de observaciones con <b>Aseguradora</b>. Al cambiar de aseguradora el sistema mostrará sólamente los vehículos que en este momento
			pertenezcan a la aseguradora seleccionada. <br><br />
			Igualmente se puede filtrar la información usando el menú <b>Oficina</b> y el menú <b>Marca</b><br><br />
			La columna <b>Kilometraje</b> muestra el kilometraje actual hasta la fecha de cada uno de los vehículos.<br><br />
			La columa <b>Historia</b> tiene un ícono <img src='gifs/standar/Preview.png' border='0'> que nos permitirá abrir una ventana con la información de la historia completa del vehículo correspondiente.<br><br />
			La columna <b>Próximo Mant.</b> muestra al usuario el próximo mantenimiento calculado a partir del último registrado en la tabla de novedades de la hoja de vida del vehículo. Si aparece en color blanco
			significa que aun no se acerca el momento de la revisión general del vehículo. Si aparece en color <span style='background-color:ffffaa'>amarillo</span>
			significa que el momento de la revisión general del vehículo está cercana y debe programarse
			con anticipación la revisión general. Si aparece en color <span style='background-color:ffdddd'>rojo</span>, significa que ya debia haberse hecho la revisión general y debe programarse inmediatamente.<br><br />
			La imágen <img src='gifs/standar/seguir.png' border='0'> sirve para ver los eventos o estados próximos de mantenimiento del vehículo inclusive desde la fecha actual.
			Si aparece significa que el vehículo provablemente ya esté programado para un mantenimiento con la debida anticipación.<br><br />
			La imágen <img src='gifs/calendar.png' border='0'> lleva al usuario a la tabla de control de la aseguradora actual. Abre una nueva ventana con la tabla de control correspondiente.<br><br />
			La columna <b>Próximo.Frenos</b> corresponde al kilometraje en el que se debe hacer la próxima revisión de frenos. Si aparece en color blanco, significa que aun no se acerca el momento de la revisión.
			Si aparece en color <span style='background-color:ffffaa'>amarillo</span>, significa que el momento de la revisión de frenos esta cercano.
			Si aparece en color <span style='background-color:ffdddd'>rojo</span> significa que el kilometraje amerita la revisión urgente de frenos.<br /><br />
			La columna <b>Próximo.SOAT</b> corresponde a la fecha de vencimiento del SOAT. Si aparece en color blanco, significa que aun no se acerca el vencimiento del SOAT.
			Si aparece en color <span style='background-color:ffffaa'>amarillo</span>, significa que en menos de 30 dias se vence el SOAT.
			Si aparece en color <span style='background-color:ffdddd'>rojo</span> significa que el SOAT ya está vencido.<br /><br />
			La imágen <img src='gifs/standar/si.png' border=0> sirve para incluir un nuevo registro de mantenimiento o de revisión de pastillas de frenos o de SOAT en la hoja de vida del vehículo.
			Cuando un evento haya pasado por ejemplo de mantenimiento en la ubicación o tabla de control
			del vehículo ya se haya registrado el cierre del mantenimiento, o sea, que ya se tenga el kilometraje de vuelta y la fecha de finalización del mantenimiento; al tener la información completa se incluya en
			la hoja de vida del vehículo la novedad correspondiente a ese mantenimiento al igual que la revisión de frenos y el SOAT.  Con ese ícono se puede incluir las novedades en la hoja de vida del
			vehículo.<br><br />
			La imágen <img src='gifs/tv.png' border='0'> le muestra al usuario las novedades de la hoja de vida del vehículo.<br /><br />
			</i></a>";
	}
	else
	{
		echo "<h3 align='center'><b>No se encuentra información que coincida con los criterios de búsqueda</b></h3>";
	}


	echo "</body>";
}

function pinta_estadov($Placa,$Odo,$DVF,$LINK)
{
	global $TV;
	$Ve=qom("select * from $TV where placa='$Placa' ",$LINK);
	$M=qom("select * from aoacol_aoacars.marca_vehiculo where id=$Ve->idmarca",$LINK);
	//////////////////////////////   ESTADO DE MANTENIMIENTOS /////////////////////
	$HS=mysql_query("select * from aoacol_aoacars.hv_vehiculo where placa='$Placa' and novedad='MNT' order by kilometraje",$LINK);
	// Verificación del kilometraje actual contra el mantenimiento del vehiculo   el id de la novedad Mantenimiento=1
	$Km_ultimo_mantenimiento=0;
	if(mysql_num_rows($HS))
	{
		while($H=mysql_fetch_object($HS))
		{
			if($H->kilometraje > $Km_ultimo_mantenimiento)
				$Km_ultimo_mantenimiento=$H->kilometraje;
		}
	}
	$BGcolor='ffffff';
	if($Km_ultimo_mantenimiento <= $Odo) // si el ultimo mantenimiento es menor que el odometro actual (esta deberia ser la condicion normal)
	{
		// Se calcula el proximo mantenimiento de acuerdo a la  marca menos 500 kilometros para la olgura
		$Proximo_mantenimiento = $Km_ultimo_mantenimiento+$M->manten_cada;
		$Proximo_umbral=$Proximo_mantenimiento-700;
		if($Odo > $Proximo_umbral)
		{
			if($Odo > ($Proximo_mantenimiento))
				$BGcolor='ffdddd'; // si el odometro supera el próximo mantenimiento despues del umbral, se pone rojo
			else
				$BGcolor='ffffaa';  // si el odometro supera el proximo mantenimiento dentro del umbral, se pone amarillo
			echo "<td bgcolor='$BGcolor' align='right'>";
			echo coma_format($Proximo_mantenimiento);
		}
		else
			echo "<td bgcolor='$BGcolor' align='right'>".coma_format($Proximo_mantenimiento);
		if(inlist($_SESSION['User'],'1,2,7,20'))
			echo "<a href=\"javascript:adiciona_mantenimiento('$Placa');\" alt='Insertar mantenimiento' title='Insertar mantenimiento'><img src='gifs/standar/si.png' border=0></a>";
	}
	else
		echo "<td bgcolor='$BGcolor' align='right'>".coma_format($Proximo_mantenimiento);
	if($DVF)
	{
		echo "&nbsp;<a href=\"javascript:eventos_futuros('$Placa');\" alt='Ver programacion futura' title='Ver programación futura'><img src='gifs/standar/seguir.png' border='0'></a>";
	}
	echo "</td>";
}

function pinta_estadov2($Placa,$Odo,$LINK)
{
	global $TV;
	$Ve=qom("select * from $TV where placa='$Placa' ",$LINK);
	$M=qom("select * from aoacol_aoacars.marca_vehiculo where id=$Ve->idmarca",$LINK);
	//////////////////////////////   ESTADO DE MANTENIMIENTOS /////////////////////
	$HS=mysql_query("select * from hv_vehiculo where placa='$Placa' and novedad='FRE' order by kilometraje",$LINK);
	// Verificación del kilometraje actual contra el mantenimiento del vehiculo   el id de la novedad Mantenimiento=1
	$Km_ultimo_mantenimiento=0;
	if(mysql_num_rows($HS))
	{
		while($H=mysql_fetch_object($HS))
		{
			if($H->kilometraje > $Km_ultimo_mantenimiento)
				$Km_ultimo_mantenimiento=$H->kilometraje;
		}
	}
	$BGcolor='ffffff';
	if($Km_ultimo_mantenimiento <= $Odo) // si el ultimo mantenimiento es menor que el odometro actual (esta deberia ser la condicion normal)
	{
		// Se calcula el proximo mantenimiento de acuerdo a la  marca menos 500 kilometros para la olgura
		$Proximo_mantenimiento = $Km_ultimo_mantenimiento+$M->frenos_cada;
		$Proximo_umbral=$Proximo_mantenimiento-1000;
		if($Odo > $Proximo_umbral)
		{
			if($Odo > ($Proximo_mantenimiento))
				$BGcolor='ffdddd'; // si el odometro supera el próximo mantenimiento despues del umbral, se pone rojo
			else
				$BGcolor='ffffaa';  // si el odometro supera el proximo mantenimiento dentro del umbral, se pone amarillo
			echo "<td bgcolor='$BGcolor' align='right'>";
			echo coma_format($Proximo_mantenimiento);
			if(inlist($_SESSION['User'],'1,2,7,20'))
			{
				echo "<a href=\"javascript:adiciona_mantenimientof('$Placa');\" alt='Insertar mantenimiento' title='Insertar mantenimiento'><img src='gifs/standar/si.png' border=0></a>";
			}
		}
		else
			echo "<td bgcolor='$BGcolor' align='right'>".coma_format($Proximo_mantenimiento);
	}
	else
		echo "<td bgcolor='$BGcolor' align='right'>".coma_format($Proximo_mantenimiento);
	echo "</td>";
}

function pinta_estadov3($Placa,$LINK)
{
	global $TV,$Hoy;
	echo "<td align='center' ";
	$Ve=qom("select * from $TV where placa='$Placa' ",$LINK);
	if($FS=qo1m("select fecha from aoacol_aoacars.hv_vehiculo where placa='$Placa' and novedad='SOA' order by fecha desc limit 1",$LINK))
	{
		$FS=date('Y-m-d',strtotime(aumentameses($FS,12)));
		if(date('Y-m-d',strtotime($FS))<date('Y-m-d',strtotime($Hoy)))
		{
			if(inlist($_SESSION['User'],'1,2,7,20'))
			{
				echo "bgcolor='ffdddd'>$FS <a href=\"javascript:adiciona_soat('$Placa');\" alt='Insertar SOAT' title='Insertar SOAT'><img src='gifs/standar/si.png' border=0></a>";
			}
		}
		else
		{
			if(date('Y-m-d',strtotime(aumentameses($FS,-1)))<date('Y-m-d',strtotime($Hoy)))
			{
				echo "bgcolor='ffffaa'>$FS <a href=\"javascript:adiciona_soat('$Placa');\" alt='Insertar SOAT' title='Insertar SOAT'><img src='gifs/standar/si.png' border=0></a>";
			}
			else
			{
				echo ">$FS";
			}
		}

	}
	else
	{
		echo " bgcolor='ffdddd'>$Hoy <a href=\"javascript:adiciona_soat('$Placa');\" alt='Insertar SOAT' title='Insertar SOAT'><img src='gifs/standar/si.png' border=0></a>";
	}
	echo "</td>";
}

function base_aseguradora($base)
{
	switch($base)
	{
		case 1:return 'aoacol_aoacolombia';	break;
		case 2:return 'aoacol_aoacolombia2';	break;
		case 3:return 'aoacol_libertyseguros';	break;
		case 4:return 'aoacol_mapfre';	break;
		case 5:return 'aoacol_aoacolombia3';	break;
		case 6:return 'aoacol_aoacars';	break;
	}
}

function control_aseguradora($base)
{
	switch($base)
	{
		case 1:return '../colseguros/zcontrol.php';	break;
		case 2:return '../royal/zcontrol.php';	break;
		case 3:return '../liberty/zcontrol.php';	break;
		case 4:return '../mapfre/zcontrol.php';	break;
		case 5:return '../bmw/zcontrol.php';	break;
		case 6:return '../aoa/zcontrol_aoa.php';	break;
	}
}

function url_aseguradora($base)
{
	switch($base)
	{
		case 1:return 'colseguros/';	break;
		case 2:return 'royal/';	break;
		case 3:return 'liberty/';	break;
		case 4:return 'mapfre/';	break;
		case 5:return 'bmw/';	break;
		case 6:return 'aoa/';	break;
	}
}

function historia_vehiculo()
{
	global $placa,$Filtro,$TH,$TV;
	if($Filtro)
		$TF="and (observaciones like '%$Filtro%' or obs_mantenimiento like '%$Filtro%') ";
	else
		$TF="";
	html("HISTORIA PLACA $placa");
	if($Historia=q("select * from $TH where placa='$placa' $TF"))
	{
		echo "<script language='javascript'>
			function recargar()
			{
				window.open('zhv_vehiculos.php?Acc=historia_vehiculo&placa=$placa&Filtro='+document.getElementById('Filtro').value,'_self');
			}
		</script>
		<body>
		<h3><b>HISTORIA DEL VEHICULO $placa</b></h3>
		Filtro de busqueda: <input type='text' name='Filtro' id='Filtro' value='$Filtro'> <input type='button' value='Buscar' onclick='recargar()'><br>
		<table border cellspacing=0><tr>
						<th>Estado</th>
						<th>Oficina</th>
						<th>Fecha</th>
						<th>Odometro</th>
						<th>Observaciones</th>
						<th>Aseguradora</th>
						</tr>";
		require('inc/link.php');
		$Fondo='ffffee';
		$Mes=0;
		while($H=mysql_fetch_object($Historia))
		{
			$Aseguradora=qo1m("select t_aseguradora($H->aseguradora)",$LINK);
			if(date('Ym',strtotime($H->fecha_inicial))!=$Mes)
			{
				$Mes=date('Ym',strtotime($H->fecha_inicial));
				$Fondo=($Fondo=='ffffee'?'eeffee':'ffffee');
			}
			$Color=qo1m("select color_co from estado_vehiculo where nombre='$H->nestado'",$LINK);
			if($H->estado==1 || $H->estado==7)
			{
				$Siniestro=qo1m("select numero from aoacol_aoacars.siniestro where ubicacion=$H->id",$LINK);
			}
			else $Siniestro='';
			echo "<tr bgcolor='$Fondo'>
						<td nowrap='yes' style='background-color:$Color;color:000000;'>$H->nestado</td>
						<td>$H->noficina</td>
						<td nowrap='yes'>$H->fecha_inicial - $H->fecha_final</td>
						<td nowrap='yes'>$H->odometro_inicial - $H->odometro_final</td>
						<td><b>$Siniestro</b> $H->observaciones <br>$H->obs_mantenimiento</td>
						<td nowrap='yes'>$Aseguradora</td>
						</tr>";
		}
		echo "</table></body>";
		mysql_close($LINK);
	}
	else
	{
		echo "<script language='javascript'>
			function recargar()
			{
				window.open('zhv_vehiculos.php?Acc=historia_vehiculo&placa=$placa&Filtro='+document.getElementById('Filtro').value,'_self');
			}
			</script>
			<body>
			Filtro de busqueda: <input type='text' name='Filtro' id='Filtro' value='$Filtro'> <input type='button' value='Buscar' onclick='recargar()'><br>
			<br><b>ATENCION: EL FILTRO DE BUSQUEDA UTILIZADO NO ENCONTRÓ NINGÚN REGISTRO CON ESA COINCIDENCIA O EL VEHÍCULO AÚN NO TIENE HISTORIA.</B>";
	}
}

function historia_fotografica()
{
	global $placa,$TH,$TV,$pagina;

	if(!$pagina) $pagina=1;
	html("HISTORIA PLACA $placa");
	// BUSCA LA HISTORIA FOTOGRAFICA DEL VEHICULO EXCLUYENDO LA FLOTA SIN LOGO.
	if($H=qo("select * from $TH where placa='$placa' and nestado like '%SERVICIO%' and nestado!='FUERA DE SERVICIO'
		and aseguradora!=6 order by odometro_inicial desc, fecha_inicial desc limit ".($pagina-1).",1"))
	{
		$Base=base_aseguradora($H->aseguradora);
		$Aseguradora=qo1("select aoacol_aoacars.t_aseguradora($H->aseguradora)");
		$Url='http://app.aoacolombia.com/Control/'.url_aseguradora($H->aseguradora);
		echo "<script language='javascript'>
		</script>
		<body >
		<script language='javascript'>
		centrar();
		</script>
		<i style='font-size:14'>La historia fotográfica de los vehiculos empieza en marzo de 2010. Los siniestros que correspondan a fechas anteriores, no tienen el registro fotográfico. <br><br>
		Las imágenes de los vehículos se muestran siniestro por siniestro empezando por el mas reciente hacia atras. Para ver cada siniestro, se da click en <b>SINIESTRO ANTERIOR</b></I><BR><BR>
		<h3><b>HISTORIA FOTOGRAFICA DEL VEHICULO $placa</b></h3>
		<a href='zhv_vehiculos.php?Acc=historia_fotografica&placa=$placa&pagina=".($pagina+1)."' target='_self'>SINIESTRO ANTERIOR</a> &nbsp;&nbsp;&nbsp;&nbsp;";
		if($pagina>1) echo "<a href='zhv_vehiculos.php?Acc=historia_fotografica&placa=$placa&pagina=".($pagina-1)."' target='_self'>SINIESTRO SIGUIENTE</a>";
		if($Sin=qo("select * from $Base.siniestro where ubicacion=$H->id"))
		{
			echo "<h3><i>Oficina:</i> $H->noficina <i>Estado:</i> $H->nestado <i>Aseguradora:</i> $Aseguradora <i>Siniestro:</i> $Sin->numero
							<i>Fecha:</i> $H->fecha_inicial - $H->fecha_final
							<i>Odometros:</i> $H->odometro_inicial - $H->odometro_final</h3>
							<font color='GREEN' style='font-size:26;font-weight:bold'>IMAGENES DE ENTREGA</FONT><BR>
							<img src='".$Url."$Sin->img_inv_salida_f'><br /><br />
							<img src='".$Url."$Sin->fotovh1_f' ><br><br />
							<img src='".$Url."$Sin->fotovh2_f' ><br><br />
							<img src='".$Url."$Sin->fotovh3_f' ><br><br />
							<img src='".$Url."$Sin->fotovh4_f' ><br><br />";
			if($Sin->img_inv_entrada_f)
			{
				echo "<font color='BLUE' style='font-size:26;font-weight:bold'>IMAGENES DE DEVOLUCION</font><br>
							<img src='".$Url."$Sin->img_inv_entrada_f'><br /><br />
							<img src='".$Url."$Sin->fotovh5_f' ><br><br />
							<img src='".$Url."$Sin->fotovh6_f' ><br><br />
							<img src='".$Url."$Sin->fotovh7_f' ><br><br />
							<img src='".$Url."$Sin->fotovh8_f' ><br><br />".
							($Sin->fotovh9_f?"<img src='".$Url."$Sin->fotovh9_f' >":"");
			}
		}
		else
		{
			echo "No se encuentra la información correspondiente al siniestro.";
		}
		echo "<hr><a href='zhv_vehiculos.php?Acc=historia_fotografica&placa=$placa&pagina=".($pagina+1)."' target='_self'>SINIESTRO ANTERIOR</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		if($pagina>1) echo "<a href='zhv_vehiculos.php?Acc=historia_fotografica&placa=$placa&pagina=".($pagina-1)."' target='_self'>SINIESTRO SIGUIENTE</a>";
	}
	else
	{
		echo "No hay historia de servicios de este vehículo.";
	}
}

function adiciona_mantenimiento()
{
	global $Placa;
	html('Adicion de mantenimiento');
	echo "<script language='javascript'>
		function carga()
		{
			centrar(800,500);
		}
		function enviar()
		{
			if(Number(document.forma.kilometraje.value)<=0)
			{
				alert('Debe escribir un kilometraje valido');
				return;
			}
			if(!esfecha(document.forma.fecha.value))
			{
				alert('Debe seleccionar una fecha valida');
				return;
			}
			document.forma.Acc.value='adiciona_mantenimiento_ok';
			document.forma.submit();
		}
		function cancelar()
		{
			window.close();void(null);
		}
	</script>
	<body onload='carga()'>
	<form action='zhv_vehiculos.php' method='post' target='_self' name='forma' id='forma'>
		<input type='hidden' name='Acc' id='Acc' value=''>
		Placa: <input type='text' name='placa' value='$Placa' style='font-size:14;font-weight:bold' size=6 readonly><br>
		Novedad: ".menu1('Nov',"select codigo,nombre from novedad_vehiculo order by codigo",'MNT',0,''," disabled ")."<br>
		<input type='hidden' name='novedad' value='MNT'>
		Kilometraje: <input type='text' name='kilometraje' id='kilometraje' class='numero' size='10' maxlength='10'><br>
		Fecha del mantenimiento: ".pinta_FC('forma','fecha')."<BR>
		<input type='button' onclick='enviar()' value='GRABAR' style='width:100;height:30;font-weight:bold'>
		<input type='button' onclick='cancelar()' value='CANCELAR' style='width:100;height:30;font-weight:bold'>
	</form></body>";
}

function adiciona_mantenimiento_ok()
{
	global $placa,$novedad,$kilometraje,$fecha;
	q("insert into hv_vehiculo (placa,novedad,kilometraje,fecha) values ('$placa','$novedad','$kilometraje','$fecha') ");
	echo "<script language='javascript'>
		function carga()
		{
			window.close();
			void(null);
			opener.recargar();
		}
	</script>
	<body onload='carga()'></body>";
}

function novedades_placa()
{
	global $placa;
	html("NOVEDADES POR PLACA $placa");
	echo "<script language='javascript'>
		function carga()
		{
			centrar(500,500);
		}
	</script>
	<body onload='carga()'>
	<h3><b>NOVEDADES POR PLACA $placa</B></H3>";
	if($Novedades=q("select h.*,n.nombre from aoacol_aoacars.hv_vehiculo h,aoacol_aoacars.novedad_vehiculo n where h.placa='$placa' and
											n.codigo=h.novedad order by h.fecha desc"))
	{
		echo "<table border cellspacing=0><tr>
			<th>Placa</th>
			<th>Novedad</th>
			<th>Kilometraje</th>
			<th>Fecha</th>
			</tr>";
		while($N=mysql_fetch_object($Novedades))
		{
			echo "<tr ";
			if(inlist($_SESSION['User'],'1,2'))
				echo "ondblclick=\"modal('marcoindex.php?Acc=mod_reg&NTabla=hv_vehiculo&id=$N->id',0,0,800,800,'ednv');\"";
			echo "><td>$N->placa</td><td>$N->nombre</td><td align='right'>".coma_format($N->kilometraje)."</td><td align='center'>$N->fecha</td></tr>";
		}
		echo "</table>";
	}
	else
	{
		echo "<font color='red'>No hay novedades registradas para este vehiculo.</font>";
	}
	echo "</body>";
}

function eventos_futuros()
{
	global $placa,$aseguradora,$TH,$Hoy;
	html("EVENTOS FUTUROS DEL VEHICULO $placa $aseguradora");
	echo "<body><h3><b>EVENTOS FUTUROS DEL VEHICULO $placa</b> Se muestran los eventos a partir de $Hoy</h3>";
	if($Eventos=q("select * from $TH where placa='$placa' and (fecha_inicial>='$Hoy'  or fecha_final>='$Hoy') and aseguradora=$aseguradora order by fecha_final "))
	{
		echo "<table border cellspacing=0><tr><th>Fechas de programación</th><th>Odometros</th><th>Estado</th><th>Observaciones</th></tr>";
		while($E=mysql_fetch_object($Eventos))
		{
			echo "<tr><td align='center' nowrap='yes'>$E->fecha_inicial - $E->fecha_final</td>
				<td align='center' nowrap='yes'><font color='".($E->odometro_inicial<=0?'red':'')."'>".coma_format($E->odometro_inicial)."</font> -
				<font color='".($E->odometro_final<=0?'red':'')."'>".coma_format($E->odometro_final)."</font></td>
				<td align='center'>$E->nestado</td><td>$E->observaciones<br>$E->obs_mantenimiento</td></tr>";
		}
		echo "</table>";
	}
	else
	{
		echo "<font color='red'>No hay eventos futuros para este vehiculo</font>";
	}
	echo "</body>";
}

function adiciona_mantenimientof()
{
	global $Placa;
	html('Adicion de mantenimiento de frenos');
	echo "<script language='javascript'>
		function carga()
		{
			centrar(800,500);
		}
		function enviar()
		{
			if(Number(document.forma.kilometraje.value)<=0)
			{
				alert('Debe escribir un kilometraje valido');
				return;
			}
			if(!esfecha(document.forma.fecha.value))
			{
				alert('Debe seleccionar una fecha valida');
				return;
			}
			document.forma.Acc.value='adiciona_mantenimiento_ok';
			document.forma.submit();
		}
		function cancelar()
		{
			window.close();void(null);
		}
	</script>
	<body onload='carga()'>
	<form action='zhv_vehiculos.php' method='post' target='_self' name='forma' id='forma'>
		<input type='hidden' name='Acc' id='Acc' value=''>
		Placa: <input type='text' name='placa' value='$Placa' style='font-size:14;font-weight:bold' size=6 readonly><br>
		Novedad: ".menu1('Nov',"select codigo,nombre from novedad_vehiculo order by codigo",'FRE',0,''," disabled ")."<br>
		<input type='hidden' name='novedad' value='FRE'>
		Kilometraje: <input type='text' name='kilometraje' id='kilometraje' class='numero' size='10' maxlength='10'><br>
		Fecha del mantenimiento: ".pinta_FC('forma','fecha')."<BR>
		<input type='button' onclick='enviar()' value='GRABAR' style='width:100;height:30;font-weight:bold'>
		<input type='button' onclick='cancelar()' value='CANCELAR' style='width:100;height:30;font-weight:bold'>
	</form></body>";
}

function adiciona_soat()
{
	global $Placa;
	html('Adicion de SOAT');
	echo "<script language='javascript'>
		function carga()
		{
			centrar(800,500);
		}
		function enviar()
		{
			if(!esfecha(document.forma.fecha.value))
			{
				alert('Debe seleccionar una fecha valida');
				return;
			}
			document.forma.Acc.value='adiciona_mantenimiento_ok';
			document.forma.submit();
		}
		function cancelar()
		{
			window.close();void(null);
		}
	</script>
	<body onload='carga()'>
	<form action='zhv_vehiculos.php' method='post' target='_self' name='forma' id='forma'>
		<input type='hidden' name='Acc' id='Acc' value=''>
		Placa: <input type='text' name='placa' value='$Placa' style='font-size:14;font-weight:bold' size=6 readonly><br>
		Novedad: ".menu1('Nov',"select codigo,nombre from novedad_vehiculo order by codigo",'SOA',0,''," disabled ")."<br>
		<input type='hidden' name='novedad' value='SOA'>
		<input type='hidden' name='kilometraje' id='kilometraje' size='10' maxlength='10' value='0'><br>
		Fecha del mantenimiento: ".pinta_FC('forma','fecha')."<BR>
		<input type='button' onclick='enviar()' value='GRABAR' style='width:100;height:30;font-weight:bold'>
		<input type='button' onclick='cancelar()' value='CANCELAR' style='width:100;height:30;font-weight:bold'>
	</form></body>";
}


?>