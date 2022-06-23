<?php

// PROGRAMA PARA CONTROL DE ACTIVOS FIJOS

include('inc/funciones_.php');
sesion(); // verifica que haya un usuario registrado activo
$NT_contrato=tu('gh_contrato','id');
$NT_activo=tu('af_activo','id');
$NT_asignacion=tu('af_asig_activo','id');
if(!$FECHA) $FECHA=date('Y-m-d');
if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}  // accion que busca funciones creadas dentro de este mismo script

inicio_activos(); // por defecto llama esta función si la variable Acc viene vacia

function inicio_activos()
{ // funcion para la pantalla principal de activos
	global $NT_contrato,$NT_activo,$NT_asignacion,$FECHA,$TIPO_ACT,$TIPO_COMP,$ARRIENDO;
	html('PROGRAMA PARA CONTROL DE ACTIVOS FIJOS');
	echo "
	
	<script language='javascript'>
		function aparece(dato,dato2)
		{
			var Ob=document.getElementById(dato);
			if(Ob.style.visibility=='hidden') {Ob.style.visibility='visible';Ob.style.position='relative';if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/menos_opciones.png';}
			else	{Ob.style.visibility='hidden';Ob.style.position='absolute';if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/mas_opciones.png';recoger(dato);}
		}
		function modifica_contrato(dato){modal('marcoindex.php?Acc=mod_reg&id='+dato+'&Num_Tabla=$NT_contrato',0,0,500,700,'mdcc');}
		function modifica_asignacion(dato){modal('marcoindex.php?Acc=mod_reg&id='+dato+'&Num_Tabla=$NT_asignacion',0,0,500,700,'mdcc');}
		function modifica_activo(dato){modal('marcoindex.php?Acc=mod_reg&id='+dato+'&Num_Tabla=$NT_activo',0,0,500,700,'mdcc');}
		function seguir() {document.forma.submit();}
		function ver_asignaciones_activo(dato) {modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$NT_asignacion&VINCULOT='+dato+'&VINCULOC=activo',0,0,600,800,'asignaciones');}
		function ver_asignaciones_contrato(dato) {modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$NT_asignacion&VINCULOT='+dato+'&VINCULOC=contrato',0,0,600,800,'asignaciones');}
		// MOSTRAR Y OCULTAR LOS BOTONES DE EMPLEADOS
		function mostrar_botones1(dato) {document.getElementById('bt1_'+dato).style.visibility='visible';document.getElementById('bt2_'+dato).style.visibility='visible';}
		function oculta_botones1(dato) {document.getElementById('bt1_'+dato).style.visibility='hidden';document.getElementById('bt2_'+dato).style.visibility='hidden';}
		// MOSTRAR Y OCULTAR LOS BOTONES DE TIPO DE ACTIVO
		function mostrar_botones2(dato) {document.getElementById('bt3_'+dato).style.visibility='visible';document.getElementById('bt4_'+dato).style.visibility='visible';}
		function oculta_botones2(dato) {document.getElementById('bt3_'+dato).style.visibility='hidden';document.getElementById('bt4_'+dato).style.visibility='hidden';}
		// MOSTRAR Y OCULTAR LOS BOTONES DE ACTIVO
		function mostrar_botones3(dato) {document.getElementById('bt5_'+dato).style.visibility='visible';}
		function oculta_botones3(dato) {document.getElementById('bt5_'+dato).style.visibility='hidden';}
		// MOSTRAR Y OCULTAR LOS BOTONES DE PERIFERICO
		function mostrar_botones4(dato) {document.getElementById('bt6_'+dato).style.visibility='visible';}
		function oculta_botones4(dato) {document.getElementById('bt6_'+dato).style.visibility='hidden';}
		// CONTROL DE NODOS HIJOS PARA OCULTAR O APARECER MASIVAMENTE
		var Hijos=new Array();
		// TOTALIZADORES POR NIVEL
		var Total_activos=0;
		var Activos_empleado=new Array();
		var Activos_sede=new Array();
		var Activos_ciudad=new Array();
		
		function recoger(dato)
		{
			// FUNCION PARA OCULTAR UN NODO Y TODOS SUS HIJOS
			if(Hijos[dato])
			{
				for(var i=0; i<Hijos[dato].length; i++)
				{
					var nuevodato=Hijos[dato][i];
					if(document.getElementById(nuevodato))
					{
						if(document.getElementById(nuevodato).style.visibility=='visible')
						{
							document.getElementById(nuevodato).style.visibility='hidden';
							document.getElementById(nuevodato).style.position='absolute';
							if(Ob=document.getElementById('img_'+nuevodato)) Ob.src='gifs/mas_opciones.png';
							recoger(nuevodato);
						}
					}
				}
			}		
		}
		
		function expandir(dato)
		{
			// FUNCION PARA EXPANDIR UN NODO Y TODOS SUS HIJOS
			document.getElementById(dato).style.visibility='visible';
			document.getElementById(dato).style.position='relative';
			if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/menos_opciones.png';
			if(Hijos[dato])
			{
				for(var i=0;i<Hijos[dato].length;i++)
				{
					var nuevodato=Hijos[dato][i];
					if(document.getElementById(nuevodato))
					{
						document.getElementById(nuevodato).style.visibility='visible';
						document.getElementById(nuevodato).style.position='relative';
						if(Ob=document.getElementById('img_'+nuevodato)) Ob.src='gifs/menos_opciones.png';
						expandir(nuevodato);
					}
				}
			}
		}
		
	</script>
	<style tyle='text/css'>
		td {font-size:9px;}
		a {cursor:pointer;}
	</style>
	<body><h3>PROGRAMA PARA CONTROL DE ACTIVOS FIJOS</h3>
	<form action='zactivos_fijos.php' target='_self' method='POST' name='forma' id='forma'>
		<input type='hidden' name='Acc' value='inicio_activos'>
		".pinta_FC('forma','FECHA',$FECHA)." 
		Tipo de Activo: ".menu1('TIPO_ACT',"select id,nombre from af_tipo_activo order by nombre","$TIPO_ACT",1)." 
		Tipo Computador: ".menu3('TIPO_COMP',"E,ESCRITORIO;P,PORTATIL;S,SERVIDOR",$TIPO_COMP,1,'',' ')."
		<select name='ARRIENDO'>
			<option value='0' ".(!$ARRIENDO?"selected":"").">Todos</option>
			<option value='1' ".($ARRIENDO==1?"selected":"").">Solo Propios</option>
			<option value='2' ".($ARRIENDO==2?"selected":"").">Solo Alquilados</option>
		</select> 
		<input type='button' name='continuar' id='continuar' value=' CONTINUAR ' onclick='seguir();'>
	</form><br>
	<span id='sp_total_activos'></span>";
	
	$Ciudades=q("select distinct ciudad,t_ciudad(ciudad) as nciudad from oficina");
	$A_ciudades=array();
	while($Ciu=mysql_fetch_object($Ciudades)) $A_ciudades[$Ciu->ciudad]=$Ciu->nciudad;
	unset($Ciudades);
	echo "<table cellspacing='0' celpadding='1' width='100%' bgcolor='ffffff'><tr><th>#</th><th colspan='2'>Ciudades</th></tr>";
	include('inc/link.php');
	$Contador_ciudades=0;
	foreach($A_ciudades as $Cod_ciudad => $Nom_ciudad)
	{
		$Contador_ciudades++;
		echo "<tr><td bgcolor='ffffff' width='10px' valign='top'>$Contador_ciudades</td><td bgcolor='ffffff' valign='top'  width='100px'>
					<img id='img_ciudad_$Cod_ciudad' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('ciudad_$Cod_ciudad');\"> 
					<a onclick=\"aparece('ciudad_$Cod_ciudad','img_ciudad_$Cod_ciudad');\"> $Nom_ciudad</a> 
					</td><td id='sp_ciudad_$Cod_ciudad' width='50px' valign='top' bgcolor='ffffff'>
					<script language='javascript'>Activos_ciudad['sp_ciudad_$Cod_ciudad']=0;</script></td><td>";
		$Sedes=mysql_query("select nombre,id from sede where ciudad='$Cod_ciudad' ",$LINK);
		echo "<table id='ciudad_$Cod_ciudad' cellspacing='0' cellpadding='1' bgcolor='ffffff' width='100%' style='visibility:hidden;position:absolute;'>
					<tr><th>#</th><th colspan='2'>Sedes</th></tr>";
		$Contador_sedes=0;
		while($Sede=mysql_fetch_object($Sedes))
		{
			$Contador_sedes++;
			echo "<tr><td width='10px' bgcolor='ffffff' valign='top'>$Contador_sedes</td><td bgcolor='ffffff' valign='top'  width='150px'>
						<img id='img_sede_$Sede->id' src='gifs/mas_opciones.png' border='0'  onclick=\"expandir('sede_$Sede->id');\">
						<a onclick=\"aparece('sede_$Sede->id','img_sede_$Sede->id');\"> $Sede->nombre</a>
						<script language='javascript'>
							if(!Hijos['ciudad_$Cod_ciudad']) Hijos['ciudad_$Cod_ciudad']=new Array();
							Hijos['ciudad_$Cod_ciudad'][Hijos['ciudad_$Cod_ciudad'].length]='sede_$Sede->id';
							Activos_sede['sp_sede_$Sede->id']=0;
						</script></td>
						<td id='sp_sede_$Sede->id' width='50px' valign='top' bgcolor='ffffff'></td><td>";
			$Empleados=mysql_query("select distinct a.contrato,t_empleado(b.empleado) as nemp,b.fecha_inicial,b.fecha_final
			 from af_asig_activo a,gh_contrato b,af_activo ac
			 where a.activo=ac.id and a.sede=$Sede->id  and a.contrato=b.id and b.fecha_inicial<='$FECHA' and
			 (b.fecha_final='0000-00-00' or b.fecha_final>='$FECHA')  ".($TIPO_ACT?" and ac.tipo_activo='$TIPO_ACT' ":"").
			 ($TIPO_COMP?" and ac.tipo_pc='$TIPO_COMP' ":"").
			 ($ARRIENDO==1?" and ac.alquilado=0":($ARRIENDO==2?" and ac.alquilado=1":"")).
			 " and (ac.inactivo_desde>='$FECHA' or ac.inactivo_desde='0000-00-00') and a.fecha_inicial<='$FECHA' 
			 and (a.fecha_final>='$FECHA' or a.fecha_final='0000-00-00')
			 order by nemp",$LINK);
			echo "<table id='sede_$Sede->id' cellspacing='0' cellpadding='1' bgcolor='ffffff' width='100%' style='visibility:hidden;position:absolute;'>
						<tr><th>#</th><th colspan='2'>Empleados</th></tr>";
			$Contador_empleados=0;
			while($Emp=mysql_fetch_object($Empleados))
			{
				$Contador_empleados++;
				echo "<tr><td width='10px' bgcolor='ffffff' valign='top'>$Contador_empleados</td><td bgcolor='ffffff' width='260px' alt='$Emp->fecha_inicial - $Emp->fecha_final' 
						title='$Emp->fecha_inicial - $Emp->fecha_final' valign='top' onmouseover=\"mostrar_botones1($Emp->contrato);\" onmouseout=\"oculta_botones1($Emp->contrato);\">
						<img id='img_empleado_$Emp->contrato' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('empleado_$Emp->contrato');\"> 
						<a onclick=\"aparece('empleado_$Emp->contrato','img_empleado_$Emp->contrato');\" >$Emp->nemp </a>".
						($NT_contrato?"<a id='bt1_$Emp->contrato' onclick='modifica_contrato($Emp->contrato);' style='background-color:ccccee;visibility:hidden;' title='Modificar Contrato de $Emp->nemp'>[+]</a> 
						<a id='bt2_$Emp->contrato' onclick='ver_asignaciones_contrato($Emp->contrato);'  style='background-color:ccccee;visibility:hidden;' title='Ver Historico de Asignaciones de $Emp->nemp'>[+]</a>":"")."
						<script language='javascript'>if(!Hijos['sede_$Sede->id']) Hijos['sede_$Sede->id']=new Array();
						Hijos['sede_$Sede->id'][Hijos['sede_$Sede->id'].length]='empleado_$Emp->contrato';
						Activos_empleado['sp_empleado_$Emp->contrato']=0;</script></td>
						<td id='sp_empleado_$Emp->contrato' width='50px' valign='top' bgcolor='ffffff'></td><td>
						";
				$Activos=mysql_query("select a.id,ac.codigo,a.activo,t_af_tipo_activo(ac.tipo_activo) as ntipo ,ac.nombre,a.fecha_inicial,a.fecha_final
						from af_asig_activo a,af_activo ac 
						where a.contrato=$Emp->contrato and sede=$Sede->id and a.activo=ac.id
						and ac.perifericode=0 and a.fecha_inicial<='$FECHA' and (a.fecha_final='0000-00-00' or a.fecha_final>='$FECHA')
						".($TIPO_ACT?" and ac.tipo_activo='$TIPO_ACT' ":"").($TIPO_COMP?" and ac.tipo_pc='$TIPO_COMP' ":"").
						($ARRIENDO==1?" and ac.alquilado=0":($ARRIENDO==2?" and ac.alquilado=1":"")).
						" and (ac.inactivo_desde>='$FECHA' or ac.inactivo_desde='0000-00-00')
						order by ntipo",$LINK);
				echo "<table id='empleado_$Emp->contrato' cellspacing='0' cellpadding='1' bgcolor='ffffff' width='100%' style='visibility:hidden;position:absolute;'>
							<tr><th>#</th><th>Tipo de Activo</th><th>Nombre</th></tr>";
				$Contador_activos=0;
				while($Ac=mysql_fetch_object($Activos))
				{
					$Contador_activos++;
					echo "<tr><td bgcolor='ffffff' width='10px' valign='top'>$Contador_activos</td>
						<td bgcolor='ffffff' width='200px' valign='top' onmouseover=\"mostrar_botones2($Ac->id);\" onmouseout=\"oculta_botones2($Ac->id);\">
							<img id='img_activo_$Ac->id' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('activo_$Ac->id');\"> 
							<a onclick=\"aparece('activo_$Ac->id','img_activo_$Ac->id');\"  title='$Ac->fecha_inicial - $Ac->fecha_final Ver perifericos'>$Ac->ntipo</a> ".
							($NT_asignacion?"<a id='bt3_$Ac->id' onclick='modifica_asignacion($Ac->id);' style='background-color:ccccee;visibility:hidden;' title='Modificar esta asignación'>[+]</a> 
							<a id='bt4_$Ac->id' onclick='ver_asignaciones_activo($Ac->activo);'  style='background-color:ccccee;visibility:hidden;' title='Ver Asignaciones de este Activo'>[+]</a>":"").
						"</td>";
					echo "<td bgcolor='ffffff' valign='top' id='td_$Ac->activo'  onmouseover=\"mostrar_botones3($Ac->activo);\" onmouseout=\"oculta_botones3($Ac->activo);\">
							<a onclick=\"aparece('activo_$Ac->id','img_activo_$Ac->id');\" title='Ver periféricos del activo'>$Ac->nombre ($Ac->codigo) </a> ".
							($NT_activo?" <a id='bt5_$Ac->activo' onclick='modifica_activo($Ac->activo);' style='background-color:ccccee;visibility:hidden;' title='Modificar Activo'>[+]</a> ":"").
							" <span id='sp_act_$Ac->activo' style='background-color:ddeedd;'></span>
							<script language='javascript'>if(!Hijos['empleado_$Emp->contrato']) Hijos['empleado_$Emp->contrato']=new Array();
							Hijos['empleado_$Emp->contrato'][Hijos['empleado_$Emp->contrato'].length]='activo_$Ac->id';
							Total_activos++;	Activos_ciudad['sp_ciudad_$Cod_ciudad']++;Activos_sede['sp_sede_$Sede->id']++;
							Activos_empleado['sp_empleado_$Emp->contrato']++;
					</script>";
					$Perifericos=mysql_query("select t_af_tipo_activo(a.tipo_activo) as ntipo,a.id,a.nombre,a.codigo  
																	from af_activo a where perifericode=$Ac->activo 
																	order by ntipo");
					if($cantidad=mysql_num_rows($Perifericos))
					{
						echo "<script language='javascript'>document.getElementById('sp_act_$Ac->activo').innerHTML='$cantidad Perifericos';
							document.getElementById('td_$Ac->activo').style.backgroundColor='cceecc';</script>";
						$Contador_periferico=0;
						echo "<br><table id='activo_$Ac->id' cellspacing='1' bgcolor='ffffdd' width='100%' style='visibility:hidden;position:absolute;'>
							<tr><th>#</th><th>Tipo de Activo</th><th>Periferico</th></tr>";
						while($Pe=mysql_fetch_object($Perifericos))
						{
							$Contador_periferico++;
							echo "<tr><td bgcolor='ffffdd' width='10px' valign='top'>$Contador_periferico</td><td bgcolor='ffffdd' width='150px'>$Pe->ntipo</td>
											<td bgcolor='ffffdd' onmouseover=\"mostrar_botones4($Pe->id);\" onmouseout=\"oculta_botones4($Pe->id);\">$Pe->nombre ($Pe->codigo) ".
											($NT_activo?" <a id='bt6_$Pe->id' onclick='modifica_activo($Pe->id);' style='background-color:ccccee;visibility:hidden;' title='Modificar Periferico'>[+]</a> ":"").
											"</td></tr>";
						}
						echo "</table>";
					}
					echo "</td></tr>";
				}
				echo "</table>";
				echo "</td></tr>";
			}
			echo "</table>";
			echo "</td></tr>";
		}
		echo "</table>";
		echo "</td></tr>";
	}
	mysql_close($LINK);
	echo "</table>
	<script language='javascript'>
		document.getElementById('sp_total_activos').innerHTML='<b>Total Activos encontrados='+Total_activos;
		for(var indice in Activos_ciudad) {document.getElementById(indice).innerHTML=Activos_ciudad[indice]+' Activos';}
		for(var indice in Activos_sede) {document.getElementById(indice).innerHTML=Activos_sede[indice]+' Activos';}
		for(var indice in Activos_empleado) {document.getElementById(indice).innerHTML=Activos_empleado[indice]+' Activos';}
	</script>
	</body>";
	
}

?>