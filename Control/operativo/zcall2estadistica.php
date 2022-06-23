<?php

include('inc/funciones_.php');
sesion();
$USUARIO=$_SESSION['User'];
$NUSUARIO=$_SESSION['Nombre'];
$IDUSUARIO=$_SESSION['Id_alterno'];
$Hoy=date('Y-m-d');$Ahora=date('Y-m-d H:i:s');

if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}
inicio_pantalla();

function inicio_pantalla()
{
	html('ESTADISTICA CALL CENTER VERSION 2');
	echo "<script language='javascript'>
	function correr_estadistica(dato) {
		if(dato==1) {document.forma.Acc.value='control_diario_ingresos';document.forma.submit();}
		if(dato==2) {document.forma.Acc.value='control_diario_agentes';document.forma.submit();}
		if(dato==3) {document.forma.Acc.value='estadistica_tiempo_agente';document.forma.submit();}
	}
	</script><body><script language='javascript'>centrar();</script>
	<form action='zcall2estadistica.php' target='Tablero_estadistica_call2' method='POST' name='forma' id='forma'>
		Desde ".pinta_FC('forma','FI',date('Y-m-d'))." hasta ".pinta_FC('forma','FF',date('Y-m-d'))." Tipo Estadistica: 
		<input type='button' name='e1' id='e1' value='Control Diario por siniestros' onclick='correr_estadistica(1);'>
		<input type='button' name='e2' id='e2' value='Control Diario por agentes' onclick='correr_estadistica(2);'>
		<input type='button' name='e2' id='e2' value='Tiempos Productivos vs No Productivos' onclick='correr_estadistica(3);'>
		<input type='hidden' name='Acc' value=''>
	</form>
	<iframe name='Tablero_estadistica_call2' id='Tablero_estadistica_call2' width='98%' height='90%'></iframe>
	</body>";
}

function control_diario_ingresos()
{
	html();
	global $FI,$FF;
	echo "<body><h3>Control Diario de Ingresos entre $FI y $FF</h3>";
	if($Consulta=q("select *,t_estado_siniestro(estado) as nestado from siniestro where date_format(ingreso,'%Y-%m-%d') between '$FI' and '$FF' "))
	{
		echo "Cantidad de Siniestros = ".mysql_num_rows($Consulta);
		$Preadj=array();if($Pre=q("select * from call2cola2 where aceptado=0"))  while($P=mysql_fetch_object($Pre)) $Preadj[$P->siniestro]=$P->fecha;
		$A=array();$Tpreadj=0;$NA=array();
		while($C=mysql_fetch_object($Consulta))
		{
			$A[$C->nestado]+=1;	if($C->estado==5) if($Preadj[$C->id]) $Tpreadj++;
			if($C->estado==1) 
			{
				$NA[$C->causal][$C->subcausal]++;
			}
		}
		$A_causal=tabla2arreglo('causal',array('id','nombre'));
		$A_subcausal=tabla2arreglo('subcausal',array('id','nombre'));
		echo "<table border cellspacing='0'>";
		foreach($A as $estado => $cantidad)
		{
			echo "<tr><td>$estado :</td><td>$cantidad</td>"; 
			if($estado=='PENDIENTE') echo "<td valign='top'>($Tpreadj pre-adjudicados)</td>";
			if($estado=='NO ADJUDICADO')
			{
				echo "<td valign='top'><table border cellspacing='0'>";
				foreach($NA as $causal => $subcausales)
				{
					echo "<tr><td valign='top'>".$A_causal[$causal]." <span id='tc$causal'></span></td><td><table>";
					$tc=0;
					foreach($subcausales as $subcausal => $cantidad)
					{
						echo "<tr><td>".$A_subcausal[$subcausal]."</td><td>$cantidad</td></tr>";
						$tc+=$cantidad;
					}
					echo "<script language='javascript'>document.getElementById('tc$causal').innerHTML='$tc';</script>";
					echo "</table></td></tr>";
				}
				echo "</table></td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
	else echo "<b>No hay información entre $FI y $FF</b>";
	echo "</body>";
}

function control_diario_agentes()
{
	html();
	global $FI,$FF;
	echo "
	<script language='javascript'>
	var A_nivel=new Array();
	var A_nagente=new Array();
	var A_agente_procesados=new Array();
	var A_nseg=new Array();
	var A_agente_tipo=new Array();
	var A_ciudad_tipo=new Array();
	var A_agente_verdes=new Array();
	var A_agente_rojos=new Array();
	var A_agente_amarillos=new Array();
	var Tipos=' ,';
	var Resultado_html='';
	function ver(dato,tipo)
	{
		if(dato==true) Tipos+=tipo+','; else Tipos=Tipos.replace(','+tipo+',',',');
		var Efectividad=0;var Eficiencia=0;
		var Cantidad=0;
		Resultado_html=\"<table border cellspacing=0 width='100%' bgcolor='ffffff'><tr><th>Agente</th><th>Procesados</th><th>Filtro</th><th>Final</th><th>Efectividad</th><th>Eficiencia</th></tr>\";
		A_agente_tipo.forEach( function(contenido,indice,array)
		{
			Efectividad=0;Eficiencia=0;Cantidad=0;
			Resultado_html+=\"<tr bgcolor='ffffff'><td valign='top'>\"+A_nagente[indice]+'<br>Nivel: '+A_nivel[indice]+'</td>';
			Resultado_html+=\"<td align='right'>\"+A_agente_procesados[indice]+'</td>';
			Resultado_html+=\"<td><table border cellspacing='0' width='100%'>\";
			contenido.forEach(function(cont,ind,array) 
			{
				if(Tipos.indexOf(','+ind+',')>0)
				{	
					Resultado_html+='<tr><td>'+A_nseg[ind]+'</td><td>'+cont+'</td></tr>';
					if(ind==17 || ind==5) Cantidad=cont;
				}
			});
			Resultado_html+='</table></td><td>';
			if(A_agente_verdes[indice]>0) 
			{
				Resultado_html+=\" <span style='background-color:bbffbb'> \"+A_agente_verdes[indice]+' </span> ';
				if(Cantidad)	
				{
					Efectividad=Redondeo(A_agente_verdes[indice]/Cantidad*100,2); 
					Eficiencia=Redondeo(A_agente_verdes[indice]/A_agente_procesados[indice] * 100,2); 
				}
				else Efectividad='';
			}
			if(A_agente_amarillos[indice]>0) Resultado_html+=\" <span style='background-color:ffffbb'> \"+A_agente_amarillos[indice]+' </span> ';
			if(A_agente_rojos[indice]>0) Resultado_html+=\" <span style='background-color:ffbbbb'> \"+A_agente_rojos[indice]+' </span> ';
			Resultado_html+=\"</td><td align='right'>\"+Efectividad+\"</td><td align='right'>\"+Eficiencia+'</td></tr>';
		});
		Resultado_html+='</table>';
		document.getElementById('resultado').innerHTML=Resultado_html;
	}
	
	function ver_agente(dato)
		{with(document.getElementById('ag_'+dato)){if(style.visibility=='hidden'){style.visibility='visible';style.position='relative';}else{style.visibility='hidden';style.position='absolute';}}}
	function mod_siniestro(dato){modal('zsiniestro.php?Acc=buscar_siniestro&id='+dato,0,0,500,500,'vs');}
	</script>
	<body><h3>Control Diario de Agentes entre $FI y $FF</h3> 
	<a href='zcall2estadistica.php?Acc=estadistica_tiempo_agente&FI=$FI&FF=$FF' target='_blank'>Estadistica de Tiempo x Agente</a><br>";

	include('inc/link.php');
	$A_tseg=array();$A_tsegu=array();
	$Tseg=mysql_query("select id,nombre from tipo_seguimiento order by id",$LINK);
	while($Ts=mysql_fetch_object($Tseg)) {$A_tseg[$Ts->id]=$Ts->nombre;echo "<script language='javascript'>A_nseg[$Ts->id]='".$Ts->nombre."';</script>";}
	$A_agentes=array();$Q_agentes=mysql_query("select id,nombre,nivel from usuario_callcenter order by id",$LINK);
	while($a=mysql_fetch_object($Q_agentes)) 
	{$A_agentes[$a->id]=$a->nombre;echo "<script language='javascript'>A_nivel[$a->id]=$a->nivel;</script>";}
	$A_estadosin=tabla2arreglo('estado_siniestro');
	
	if($Agentes=mysql_query("select distinct agente from call2proceso where date_format(fecha,'%Y-%m-%d') between '$FI' and '$FF' ",$LINK))
	{
		echo "<table width='100%'><tr><td valign='top'>";
		echo "<table border cellspacing='0'>";
		while($Agente=mysql_fetch_object($Agentes))
		{
			echo "<script language='javascript'>A_nagente[$Agente->agente]='".$A_agentes[$Agente->agente]."';
			A_agente_verdes[$Agente->agente]=0;
			A_agente_rojos[$Agente->agente]=0;
			A_agente_amarillos[$Agente->agente]=0;
			</script>";
			echo "<tr><td valign='top'>".$A_agentes[$Agente->agente]."</td><td valign='top'>";
			$A_siniestros=array();
			if(!$Siniestros=mysql_query("select c.siniestro,s.numero,m_call2proceso_estado(c.estado) as nestado, s.estado as estados
				FROM call2proceso c,siniestro s
				WHERE c.siniestro=s.id and c.agente=$Agente->agente and date_format(c.fecha,'%Y-%m-%d') between '$FI' and '$FF' order by c.fecha",$LINK)) 
				{die(mysql_error($LINK));}
			while($S=mysql_fetch_object($Siniestros))	{$A_siniestros[$S->siniestro.' ['.$S->numero-']']=$S;}	
			echo "<a style='cursor:pointer' onclick='ver_agente($Agente->agente);'>Siniestros procesados: ".coma_format(count($A_siniestros))."</a><br>
			<script language='javascript'>A_agente_procesados[$Agente->agente]=".count($A_siniestros).";</script>
			<div id='ag_".$Agente->agente."' style='visibility:hidden;position:absolute;'>
			<table border cellspacing='0' width='100%'>";
			foreach($A_siniestros as $ids => $DS)
			{
				$Sinq=mysql_query("select ciudad from siniestro where id=$ids",$LINK);
				$Sin=mysql_fetch_object($Sinq);
				$Seguimiento=mysql_query("select tipo,count(*) as cantidad from seguimiento where siniestro=$ids and 
										usuario='".$A_agentes[$Agente->agente]."' and fecha between '$FI' and '$FF' group by tipo order by tipo",$LINK);
				$A_seg=array();	while($S=mysql_fetch_object($Seguimiento)) $A_seg[$S->tipo]=$S->cantidad;
				echo "<tr><td style='cursor:pointer' onclick='mod_siniestro($ids);'>$ids</td><td>";
				echo "<table width='100%'><tr><th>Tipo Seguimiento</th><th>Cantidad</th></tr>";
				$cs17=false;
				foreach($A_seg as $cs => $cts)
				{
					if($cs==17 || $cs==5) $cs17=true;
					echo "<tr><td>".$A_tseg[$cs]."</td><td align='right'>$cts</td></tr>";
					$A_tsegu[$cs]+=$cts;
					echo "<script language='javascript'>
					if(!A_agente_tipo[$Agente->agente]) A_agente_tipo[$Agente->agente]=new Array();
					if(!A_agente_tipo[$Agente->agente][$cs]) A_agente_tipo[$Agente->agente][$cs]=0;
					A_agente_tipo[$Agente->agente][$cs]+=($cs==17 || $cs==5?1:$cts);</script>";
				}
				echo "</table>";
				echo "<script language='javascript'>";
				if(inlist($DS->estados,'3,5')) {$Bgc='ffffbb';if($cs17) echo "A_agente_amarillos[$Agente->agente]++;";}
				if(inlist($DS->estados,'1')) {$Bgc='ffbbbb';if($cs17) echo "A_agente_rojos[$Agente->agente]++;";}
				if(inlist($DS->estados,'7,8')) {$Bgc='bbffbb';if($cs17) echo "A_agente_verdes[$Agente->agente]++;";}
				echo "</script></td><td valign='top' bgcolor='$Bgc'>".$A_estadosin[$DS->estados]."</td></tr>";
			}
			echo "</table>
			</div>
			</td></tr>";
		}
		echo "</table>";
		echo "<h2 align='center'>NIVEL 1</H2>";pinta_estadistica_diaria(1,$LINK);
		echo "<h2 align='center'>NIVEL 2</H2>";pinta_estadistica_diaria(2,$LINK);
		echo "</td><td valign='top'>";
		/////////////////////////   PRESENTACION DE ESCALAFONES DE LOS AGENTES /////////////////////////////////
		$Temp='tmpi_escalafon_'.$_SESSION['Id_alterno'];
		mysql_query("drop table if exists $Temp",$LINK);
		if(!mysql_query("create table $Temp select t.nivel, e.agente,sum(e.puntaje) as tp from call2escalafon e,call2tescalafon t 
			where e.codigo=t.id and date_format(e.fecha,'%Y-%m-%d') between '$FI' and '$FF' group by t.nivel,e.agente order by t.nivel,e.agente ",$LINK)) die(mysql_error($LINK));
		$Escalafones=mysql_query("select * from $Temp order by nivel,tp desc",$LINK);
		if(mysql_num_rows($Escalafones))
		{
			echo "<table border cellspacing='0'><tr><th>Posicion</th><th>Nivel</th><th>Agente</th><th>Escalafon</th></tr>";
			$Contador=0;
			while($Es=mysql_fetch_object($Escalafones))
			{
				$Contador++;
				echo "<tr><td align='center'>$Contador</td><td align='center'>$Es->nivel</td>
				<td align='left'>".$A_agentes[$Es->agente]."</td>
				<td align='center'>$Es->tp</td></tr>";
			}
			echo "</table>";
		}
		else echo "<b>No hay datos de escalafon entre $FI y $FF</b>";
		/////////////////   FILTRO POR TIPOS DE SEGUIMIENTOS ////////////////////////////////////////////////////////////////////
		echo "Tipos de seguimiento utilizados";
		echo "<table border cellspacing='0'><tr><th>Tipo</th><th>Cantidad</th><th>Ver</th></tr>";
		foreach($A_tsegu as $cs => $cts)
		{
			echo "<tr><td>".$A_tseg[$cs]."</td><td align='right'>$cts</td><td align='center'><input type='checkbox' onchange='ver(this.checked,$cs);'></td></tr>";
		}
		echo "</table>
		<span id='resultado'></span>";
		echo "</td></tr></table>";
	}
	mysql_close($LINK);
	echo "</body>";
}

function pinta_estadistica_diaria($Nivel,$LINK)
{
	$Hoy=date('Y-m-d');
	echo "<table align='center'><tr><td align='center' colspan=2><h3>ESTADISTICA HOY $Hoy</h3></td></tr><tr><td>";
	if($Pos_gestionados=mysql_query("select u.nombre, c.gestionados
	FROM call2est_diaria c, usuario_callcenter u 
	WHERE c.agente=u.id and fecha='$Hoy' and c.nivel=$Nivel
	ORDER BY c.gestionados desc",$LINK))
	{
		echo "<table border cellspacing='0'><tr><th colspan=3>Siniestros Gestionados</th></tr><tr><th>Puesto</th><th>Agente</th><th>Cantidad</th></tr>";
		$Contador=0;
		while($G=mysql_fetch_object($Pos_gestionados))
		{
			$Contador++;
			echo "<tr><td align='center'>$Contador</td><td>$G->nombre</td><td align='right'>$G->gestionados</td></tr>";
		}
		echo "</table>";
	}
	echo "</td><td>";
	if($Pos_efectivos=mysql_query("select u.nombre, c.efectivos
	FROM call2est_diaria c, usuario_callcenter u 
	WHERE c.agente=u.id and fecha='$Hoy' and c.nivel=$Nivel
	ORDER BY c.efectivos desc",$LINK))
	{
		echo "<table border cellspacing='0'><tr><th colspan=3>Siniestros Efectivos</th></tr><tr><th>Puesto</th><th>Agente</th><th>Cantidad</th></tr>";
		$Contador=0;
		while($G=mysql_fetch_object($Pos_efectivos))
		{
			$Contador++;
			echo "<tr><td align='center'>$Contador</td><td>$G->nombre</td><td align='right'>$G->efectivos</td></tr>";
		}
		echo "</table>";
	}
	///////////-------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$Primer_dia_semana=primer_dia_de_semana($Hoy);
	echo "</td></tr><tr><td align='center' colspan=2><h3>Estadistica Semanal $Primer_dia_semana - $Hoy </h3></td></tr><tr><td>";
	if($Pos_gestionados=mysql_query("select u.nombre, c.gestionados
	FROM call2est_semanal c, usuario_callcenter u 
	WHERE c.agente=u.id and fecha ='$Primer_dia_semana' and c.nivel=$Nivel
	ORDER BY c.gestionados desc",$LINK))
	{
		echo "<table border cellspacing='0'><tr><th colspan=3>Siniestros Gestionados</th></tr><tr><th>Puesto</th><th>Agente</th><th>Cantidad</th></tr>";
		$Contador=0;
		while($G=mysql_fetch_object($Pos_gestionados))
		{
			$Contador++;
			echo "<tr><td align='center'>$Contador</td><td>$G->nombre</td><td align='right'>$G->gestionados</td></tr>";
		}
		echo "</table>";
	}
	echo "</td><td>";
	if($Pos_efectivos=mysql_query("select u.nombre, c.efectivos
	FROM call2est_semanal c, usuario_callcenter u 
	WHERE c.agente=u.id and fecha = '$Primer_dia_semana' and c.nivel=$Nivel
	ORDER BY c.efectivos desc",$LINK))
	{
		echo "<table border cellspacing='0'><tr><th colspan=3>Siniestros Efectivos</th></tr><tr><th>Puesto</th><th>Agente</th><th>Cantidad</th></tr>";
		$Contador=0;
		while($G=mysql_fetch_object($Pos_efectivos))
		{
			$Contador++;
			echo "<tr><td align='center'>$Contador</td><td>$G->nombre</td><td align='right'>$G->efectivos</td></tr>";
		}
		echo "</table>";
	}
	echo "</td></tr></table>";
}

function estadistica_tiempo_agente()
{
	global $FI,$FF;
	html('ESTADISTICA DE TIEMPO POR AGENTE');
	echo "
	<script language='javascript'>
	function ver_detalle(dato)
	{
		with(document.getElementById(dato))
		{if(style.visibility=='hidden'){style.visibility='visible';style.position='relative';}else{style.visibility='hidden';style.position='absolute';}}
	}
	function mod_siniestro(dato){modal('zsiniestro.php?Acc=buscar_siniestro&id='+dato,0,0,500,500,'vs');}
	</script>
	<body><h3>ESTADISTICA DE TIEMPO POR AGENTE ENTRE $FI Y $FF</h3>";
	if($Agentes=q("select distinct u.nombre,u.foto_f,u.id from call2proceso p,usuario_callcenter u where u.id=p.agente and p.fecha between '$FI 00:00:00' and '$FF 23:59:59' order by u.nombre "))
	{
		$Cantidad=mysql_num_rows($Agentes);
		echo "<table><tr><th>#</th><th>Agente</th></tr>";
		include('inc/link.php');
		$Contador_ag=0;$Ccolor='ffffdd';
		while($Ag=mysql_fetch_object($Agentes))
		{
			if($Ccolor=='ffffdd') $Ccolor='dddddd'; else $Ccolor='ffffdd';
			$Contador_ag++;$Total_casos_agente=0;$Total_rojo_agente=0;$Total_verde_agente=0;
			echo "<tr bgcolor='$Ccolor'><td align='center' valign='top'>$Contador_ag</td><td valign='top'><a class='info' >$Ag->nombre<span><img src='$Ag->foto_f' height='100px'></span></a></td><td>";
			$Fechas=mysql_query("Select distinct date_format(fecha,'%Y-%m-%d') as dia from call2proceso where agente=$Ag->id and fecha between '$FI 00:00:00' and '$FF 23:59:59' order by dia",$LINK);
			$Contador_f=0;
			echo "<table><tr><th>#</th><th>Fecha</th><th>Detalle</th></tr>";
			while($F=mysql_fetch_object($Fechas))
			{
				$Capa_detalle='dia_'.$Ag->id.'_'.$F->dia;
				$Span='span_'.$Ag->id.'_'.$F->dia;
				$Contador_f++;
				echo "<tr><td align='center' valign='top'>$Contador_f</td><td valign='top'><a  style='cursor:pointer' onclick=\"ver_detalle('$Capa_detalle');\">$F->dia</a></td><td>
				<table border cellspacing='0'><tbody id='$Span'></tbody></table>";
				$Procesos=mysql_query("select p.fecha,p.fecha_cierre,p.siniestro,s.numero from call2proceso p, siniestro s
					where p.siniestro=s.id and p.agente=$Ag->id and p.fecha between '$F->dia 00:00:00' and '$F->dia 23:59:59' order by p.fecha",$LINK);
				$U=false;$Verde=0;$Rojo=0;
				echo "<table border cellspacing='0' id='$Capa_detalle' style='visibility:hidden;position:absolute'><tbody>
						<tr><th>#</th>
						<th>Siniestro</th>
						<th>Apertura</th><th>Cierre</th>
						<th colspan=2>Tiempo no productivo</th><th colspan=2>Tiempo productivo</th></tr>";
				$Contador_p=0;$Suma_rojo=0;$Suma_verde=0;
				while($P=mysql_fetch_object($Procesos))
				{
					$Contador_p++;
					if($P->fecha!='0000-00-00 00:00:00'){	if($U) $Rojo=segundos($U,$P->fecha); } else $Rojo=0;
					if($P->fecha!='0000-00-00 00:00:00' && $P->fecha_cierre!='0000-00-00 00:00:00') $Verde=segundos($P->fecha,$P->fecha_cierre); else $Verde=0;
					$U=$P->fecha_cierre;
					$TRojo=segundos2tiempo($Rojo);
					$TVerde=segundos2tiempo($Verde);
					$Suma_rojo+=$Rojo; $Suma_verde+=$Verde;
					echo "<tr><td align='center'>$Contador_p</td>
							<td><a style='cursor:pointer' onclick='mod_siniestro($P->siniestro);' title='Ver siniestro'>$P->numero</a></td>
							<td>$P->fecha</td><td>$P->fecha_cierre</td><td align='right' bgcolor='ffdddd'>$Rojo</td>
							<td align='right' bgcolor='ffdddd'>$TRojo</td><td align='right' bgcolor='ddffdd'>$Verde</td><td align='right' bgcolor='ddffdd'>$TVerde</td></tr>";
				}
				$TSuma_rojo=segundos2tiempo($Suma_rojo);
				$TSuma_verde=segundos2tiempo($Suma_verde);
				$Promedio1=round($Suma_rojo/$Contador_p,0);$TPromedio1=segundos2tiempo($Promedio1);
				$Promedio2=round($Suma_verde/$Contador_p,0);$TPromedio2=segundos2tiempo($Promedio2);
				$Tbody="<tr><td width='80px'>Casos: $Contador_p</td>";
				$Tbody.="<td width='350px'>Total Tiempo No productivo: $Suma_rojo segundos ($TSuma_rojo)</td>";
				$Tbody.="<td width='350px'>Total Tiempo productivo: $Suma_verde segundos  ($TSuma_verde)</td>";
				$Tbody.="</tr><tr><td></td>";
				$Tbody.="<td width='350px'>Promedio T. No productivo $Promedio1 segundos ($TPromedio1)</td>";
				$Tbody.="<td width='350px'>Promedio T. productivo $Promedio2 segundos ($TPromedio2)</td>";
				$Tbodi.="</tr>";
				$Total_casos_agente+=$Contador_p;
				$Total_rojo_agente+=$Suma_rojo;
				$Total_verde_agente+=$Suma_verde;
				echo "</tbody></table>
					
					<script language='javascript'>
						document.getElementById('$Span').innerHTML=\"$Tbody\";
					</script></td></tr>";
			}
			$Tm_rojo_agente=segundos2tiempo($Total_rojo_agente);
			$Tm_verde_agente=segundos2tiempo($Total_verde_agente);
			$Promedio1=round($Total_rojo_agente/$Total_casos_agente,0);$TPromedio1=segundos2tiempo($Promedio1);
			$Promedio2=round($Total_verde_agente/$Total_casos_agente,0);$TPromedio2=segundos2tiempo($Promedio2);
			echo "</table>
				<table border cellspacing='0' width='100%'>
					<tr bgcolor='ddddff'><td style='font-weight:bold'>Total Casos:</td><td width='80px' align='center' style='font-weight:bold'>$Total_casos_agente</td>
						<td width='350px' style='font-weight:bold'>Total tiempo No productivo $Total_rojo_agente segundos ($Tm_rojo_agente)</td>
						<td width='350px' style='font-weight:bold'>Total tiempo Productivo $Total_verde_agente segundos ($Tm_verde_agente)</td>
					</tr>
					<tr bgcolor='ddddff'>
						<td style='font-weight:bold'>Total Fechas</td><td align='center' style='font-weight:bold'>$Contador_f</td>
						<td width='350px' style='font-weight:bold'>Promedio T. No productivo: $Promedio1 segundos ($TPromedio1)</td>
						<td width='350px' style='font-weight:bold'>Promedio T. Productivo: $Promedio2 segundos ($TPromedio2)</td>
					</tr>
				</table>
			</td></tr>";
		}
		mysql_close($LINK);
		echo "</table>";
	}
	else
	{
		echo "<b style='color:red'>NO HAY INFORMACION PARA LAS FECHAS $FI - $FF</b>";
	}
	echo "</body>";
}

?>