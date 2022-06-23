<?php
########## PERFILES DE SEGURIDAD #############
# 2: ADMINISTRADOR
# 3: CAPTURA - CESAR
# 4: CALL CENTER - ALEXANDRA
# 5: AUTORIZACIONES - DIANA
# 6: ADJUDICACIONES  - PENDIENTE

include('inc/funciones_.php');
set_time_limit(0);
sesion();
$USUARIO=$_SESSION['User'];
if($USUARIO>99)
{ html('TABLA DE CONTROL');
    echo"<body><script language='javascript'>centrar(300,300);</script>
    Este módulo está en mantenimiento. Pronto estará al aire nuevamente.
    Atte. Arturo Quintero.
    </body>";
    die(); }
$NUSUARIO=$_SESSION['Nombre'];
$Tem_Placas='tmpi_ctrl_placa_'.$USUARIO.'_'.$_SESSION['Id_alterno'];
$Tem_Disponibles='tmpi_disponibles_'.$USUARIO.'_'.$_SESSION['Id_alterno'];
$OFIU=0;
$ASEU=0;
$Odofinal=0;
$US=array();
$NTcitas=tu('cita_servicio','id');

// $OFIU=2; /* MEDELLIN */

include('inc/link.php');

if($USUARIO==10 /* operario de oficina */) $OFIU=qo1m("select oficina from usuario_oficina where id=".$_SESSION['Id_alterno'],$LINK);
if(inlist($USUARIO,'11,29')/* Aseguradora 1*/) $ASEU=qo1m("select aseguradora from  usuario_aseguradora2 where id=".$_SESSION['Id_alterno'],$LINK);
if($USUARIO==23 /* Operarios de flotas */) $OFIU=qo1m("select oficina from operario where id=".$_SESSION['Id_alterno'],$LINK);
if($USUARIO==32 /* Recepcion */) $OFIU=qo1m("select oficina from usuario_recepcion where id=".$_SESSION['Id_alterno'],$LINK);
if($USUARIO==13 /* Auxiliar de Información */) $OFIU=qo1m("select oficina from usuario_auxiliarop where id=".$_SESSION['Id_alterno'],$LINK);


$Estados=mysql_query("select * from estado_vehiculo order by id",$LINK);
$EST=array();
while($e=mysql_fetch_object($Estados)) $EST[$e->id]=$e;
$Emb_Aseg=array();
$Asegs=mysql_query("select id,emblema_f from aseguradora",$LINK);
while($A=mysql_fetch_object($Asegs)) $Emb_Aseg[$A->id]=$A->emblema_f;
$Nsin=array();
$Hdevolucion=array();
mysql_close($LINK);
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');	die();}

inicio_operativo();

function inicio_operativo()
{
	global $OFIU,$ASEU,$USUARIO;
	html();
	$FI=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-10)));
	$FF=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),15)));
	pinta_js1();
	echo "<form action='zcontrol_operativo2.php' method='post' target='Oculto_control' name='forma' id='forma' onsubmit='pinta_dibujo()'>
					Fecha Inicial: ".pinta_FC('forma','FI',$FI)." Fecha final: ".pinta_FC('forma','FF',$FF)."
					Flota: ";
	echo ($ASEU?menu1('ASE',"select id,nombre from aseguradora where id=$ASEU",0,0):menu1('ASE','select id,nombre from aseguradora',0,1))." Oficina: ";
	echo ($OFIU?menu1('OFI',"select id,nombre from oficina where id =$OFIU ",0,0):menu1('OFI',"select id,nombre from oficina ",0,1));
	echo "Marca: ";
	echo ($ASEU==4?menu1('MARCA','select distinct m.id,m.nombre from marca_vehiculo m,linea_vehiculo l,vehiculo v where v.linea=l.id and l.marca=m.id and m.id in (2,5) ',0,1," width:100px;"):
		menu1('MARCA','select distinct m.id,m.nombre from marca_vehiculo m,linea_vehiculo l,vehiculo v where v.linea=l.id and l.marca=m.id',0,1," width:100px;"));
	if(!inlist($USUARIO,'11,29')) echo "Placa: ".menu1('PI',"select placa,placa from vehiculo order by placa",'',1,"width:80px;");
	echo "Resumen de Estadisticas <input type='checkbox' name='Resumen_estadisticas'> ";
		//		" Solo Disponibles <input type='checkbox' name='Disponibles'> ".
	echo " <input type='button' value=' APLICAR ' onclick='recargar_datos();' ></form>
				<table cellspacing='0' cellpadding='0' width='100%'>
				<tr><td width=200px'><span id='estatus'>&nbsp;&nbsp;</span></td>
				<td><iframe id='fechas' name='fechas' width='100%' height='40px' frameborder='no' scrolling='no' ></iframe></td></tr>
				<tr><td  width='200px' valign='top'><iframe id='placas' name='placas' width='100%' frameborder='no' scrolling='no' ></iframe></td>
				<td><iframe id='tablero' name='tablero' width='100%' frameborder='no' scrolling='auto' ></iframe></td>
				<td width='110px' valign='top'><iframe id='hvec' name='hvec' width='100%' frameborder='no' scrolling='no'></iframe></td></tr>
				</table>
				<iframe name='Oculto_control' id='Oculto_control' height=1 width=1></iframe></body>";
}

function pinta_js1()
{
	global $USUARIO;
	echo "
		<style type='text/css'>
			tr:hover {background-color: #ffffff;}
		</style>
		<script language='javascript'>
		function carga()
		{
			ajustar_tablero();
		}

		function ajustar_tablero()
		{
			document.getElementById('tablero').style.height=document.body.clientHeight-100;
			document.getElementById('placas').style.height=document.body.clientHeight-100;
			document.getElementById('hvec').style.height=document.body.clientHeight-100;
			document.getElementById('fechas').style.width=document.body.clientWidth-268;
		}

		function recargar_datos()
		{
			with(document.forma)
			{
				document.getElementById('estatus').innerHTML=\"<img src='img/cargando2.gif' width='180px' border='0' align='middle'>\";
		        var RE=0;if(Resumen_estadisticas.checked) RE=1;
				window.open('zcontrol_operativo2.php?Acc=pinta_fechas&FI='+FI.value+'&FF='+FF.value+'&Tamano=50&ASE='+ASE.value+'&OFI='+OFI.value+'&RE='+RE+'&PI='+PI.value+'&MARCA='+MARCA.value,'fechas');
			}
		}
		function pinta_dibujo()
		{document.getElementById('estatus').innerHTML=\"<img src='img/cargando1.gif' width='180px' border='0' align='middle'>\";}

		</script>
		<body leftmargin='0' rightmargin='0' topmargin='0' bottommargin='0' onload='carga();' onresize='ajustar_tablero();'>
		<script language='javascript'>centrar();</script>";

}

class cls_estado
{
	var $id=0;
	var $Color='ffffff';
	var $Nombre='';
	var $Cantidad=0;
	function cls_estado($id,$Color,$Nombre)
	{ $this->id=$id;
		$this->Color=$Color;
		$this->Nombre=$Nombre;
		$this->Cantidad=1; }
	function pinta()
	{ echo "<tr><td style='background-color:".$this->Color."'>&nbsp;</td><td nowrap='yes'>".$this->Nombre."</td><td align='center'>".$this->Cantidad."</td></tr>"; }
}

class cls_estado_ciudad
{
	var $Id=0;
	var $Nombre='';
	var $Estados=array();
	var $Total=0;
	function cls_estado_ciudad($id,$Oficina,$estado,$Color,$Nombre)
	{
		$this->id=$id;
		$this->Nombre=$Oficina;
		$this->Estados[$estado]=new cls_estado($estado,$Color,$Nombre);
	}
	function pinta()
	{
		$this->totaliza();
		ksort($this->Estados);
		echo "<table align='center' cellspacing='3'><tr><th colspan=3>".$this->Nombre." - ".$this->Total."</th></tr><tr><th>Clr</th><th>Estado</th><th>Cantidad</th></tr>";
		foreach($this->Estados as $Estado) $Estado->pinta();
		echo "</table>";
	}
	function totaliza()
	{
		foreach($this->Estados as $Estado)
		{
			$this->Total+=$Estado->Cantidad;
		}
	}
}

function control_operativo()
{
	global $FI,$FF,$ASE,$OFI,$EST,$ASEU,$USUARIO,$Nsin,$Hdevolucion,$Resumen_estadisticas,$Tem_Placas,$PI,$Odofinal,$ED,$US;

	if(inlist($USUARIO,'11,29') /*Aseguradora 1*/)
	{
		$Siniestros=q("select s.numero,s.ubicacion from siniestro s,ubicacion u	WHERE s.ubicacion=u.id and s.aseguradora=$ASEU and u.fecha_final >='$FI' and u.fecha_inicial<='$FF' ");
		while($Sin=mysql_fetch_object($Siniestros)) $Nsin[$Sin->ubicacion]="$Sin->numero";
	}
	if($Hdevol=q("select u.id,c.hora_devol FROM ubicacion u,vehiculo v,cita_servicio c,siniestro s
							WHERE  u.vehiculo=v.id and c.placa=v.placa and u.estado=1 and u.fecha_final>='$FI' and u.fecha_inicial<='$FF'
							and u.id=s.ubicacion and c.siniestro=s.id and c.estado ='C' "))
	{while($H=mysql_fetch_object($Hdevol)) $Hdevolucion[$H->id]="$H->hora_devol";}

	html();
	echo "<script language='javascript' src='inc/chart/JSClass/FusionCharts.js'></script>";
	echo "<style type='text/css'>
			<!--
			";
	foreach($EST as $est)
	{
		echo "
	a.c".$est->id." {font-size:12px;display:inline-block;background-color:".$est->color_co.";text-align:center;margin-left:1px;margin-bottom:1px;margin-top:0px;position:relative;z-index:24;}
	a.c".$est->id.":hover {z-index:100;font-size:11px;border:1px solid #000000;margin-left:1px #000000;margin-bottom:0px;margin-top:0px;margin-right:0px;}
	a.c".$est->id." span {display: none;}
	a.c".$est->id.":hover span {display:block;position:absolute;top:0em;left:5em;
   border:1px solid #0055ff;background-color:#ffffE4;color:#000000;text-align: left;font-family: Arial, Helvetica, sans-serif;
   font-size: 11px;padding: 5px;opacity: 0.90;}
		";
	}
	echo "
	a.cn {font-size:12px;display:inline-block;background-color:ddddff;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;}
	a.cb {font-size:12px;display:inline-block;background-color:ffffff;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;}
	a.cc {font-size:12px;display:inline-block;background-color:eeeeee;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;margin-top:0px;position:relative;z-index:24;}
	a.cc:hover {z-index:100;font-size:11px;border:1px solid #000000;margin-left:1px #000000;margin-bottom:0px;margin-top:0px;margin-right:0px;}
	a.cc span {display: none;}
	a.cc:hover span  {display:block;position:absolute;top:0em;left:4em;border:1px solid #0055ff;background-color:#e4e4ff;color:#000000;text-align: left;font-family: Arial, Helvetica, sans-serif;font-size: 11px;padding: 5px;opacity: 0.90;}

	a.cnt {font-size:12px;display:inline-block;font-weight:bold;background-color:006644;color:ffffaa;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;}
	sp_placa {font-size:14px;color:000088;}
	tr:hover {background-color: #aaaaaa;}
		-->
	</style>
	<script language='javascript'>
	var Contenido_celda='';
	var Cargado=false;
	var Objeto;
		function sincroniza()
		{
			var Wtop=document.body.scrollTop;
			var Wleft=document.body.scrollLeft;
			parent.document.getElementById('fechas').contentWindow.document.body.scrollLeft=Wleft;
			parent.document.getElementById('placas').contentWindow.document.body.scrollTop=Wtop;
			parent.document.getElementById('hvec').contentWindow.document.body.scrollTop=Wtop;
		}

		function mc_evento(idub,fecha,objeto)
		{
			if(Cargado)
			{
				objeto.style.cursor='wait';
				Objeto=objeto;
				window.open('zcontrol_operativo2.php?Acc=menu_contextual_celda&idub='+idub+'&fecha='+fecha+'&objeto='+objeto.id,'Menu_contextual_celda');
			}
			else
				alert('No ha terminado de cargar la información');
		}

		function mc_evento_texto(dato)
		{
			Menu=document.getElementById('Menu_contextual_celda');
			Cuad=document.getElementById('cuadricula');
			if(!dato)
			{
				if(mouseX-15+Menu.clientWidth>Cuad.clientWidth)
					Menu.style.left=Cuad.clientWidth-Menu.clientWidth;
				else
					Menu.style.left=mouseX-15;
				Menu.style.top=mouseY-15;
			}
			Menu.style.visibility='visible';
		}

		function oculta_menu_celda()
		{
			with(document.getElementById('Menu_contextual_celda'))
			{
				style.visibility='hidden';
				style.width=1;
				style.height=1;
			}
		}
		function menu_blanco(idplaca,fecha)
		{
			if(Cargado) window.open('zcontrol_operativo2.php?Acc=adicionar_evento&vehiculo='+idplaca+'&fecha='+fecha,'Menu_contextual_celda');
			else alert('No ha terminado de cargar la información');
		}
		function muestra_diario_parqueadero(Dato,Fecha)
		{
			window.open('zcontrol_operativo2.php?Acc=muestra_diario_parqueadero&D='+Dato+'&F='+Fecha,'Oculto_tablero');
		}
	</script>
	<body leftmargin='0' topmargin='1' rightmargin='0' bottommargin='0' bgcolor='eeffee' onscroll='sincroniza()' >
	<script language='javascript'>parent.document.getElementById('estatus').innerHTML=\"<img src='img/cargando1.gif' width='180px' border='0' align='middle'>\";</script>
	<table cellspacing='0' id='cuadricula' name='cuadricula' cellpadding='0' bgcolor='cccccc'>";

	if($Placas=q("select * from $Tem_Placas "))
	{
	//	$US=array();
		if($Ubicaciones=q("select u.*,ev.nombre as nestado,ase.nombre as nflota,o.nombre as noficina FROM ubicacion u, vehiculo v,estado_vehiculo ev,aseguradora ase,oficina o, $Tem_Placas p
								WHERE u.fecha_final>='$FI' and u.fecha_inicial<='$FF'  and v.id=u.vehiculo and p.id=v.id and u.estado=ev.id and ase.id=u.flota and u.oficina=o.id
								ORDER BY u.vehiculo,u.fecha_inicial,u.fecha_final,u.id",$LINK))
		while($U=mysql_fetch_object($Ubicaciones))	{$US[$U->vehiculo][count($US[$U->vehiculo])]=$U;}

		if(!inlist($USUARIO,'11,29'))
		{
			if($Citas=q("select v.id,c.placa,t_siniestro(c.siniestro) as nsiniestro,c.fecha,c.hora,c.conductor,c.agendada_por,c.id as idc
				FROM cita_servicio c,vehiculo v WHERE c.placa=v.placa and c.fecha between '$FI' and '$FF' and c.estado='P' "))
				{while($C=mysql_fetch_object($Citas))	{	$CS[$C->id][count($CS[$C->id])]=$C;} }
		}
		$Estados=array();
		$Estados_ciudad=array();


		$Hoy=date('Y-m-d');
		IF($FF<$Hoy) $Hoy=$FF;
		require('inc/link.php');
		$Conteo_placas=0;
		$Linea_total='';
		while($P=mysql_fetch_object($Placas))
		{
			$Odofinal=0;
			echo "<tr><td nowrap='yes'>";
			for ($dia=$FI;$dia<=$FF;$dia=date('Y-m-d',strtotime(aumentadias($dia,1)))) echo pinta_dia_placa($CS,$dia,$P);
			mysql_query("update $Tem_Placas set odofinal='$Odofinal' where id=$P->id",$LINK);
			echo "</td><td>&nbsp;&nbsp;</td></tr>";
			if($Resumen_estadisticas)
			{
		        $Noficina=qo1m("select t_oficina($P->oficina)",$LINK);
		        $Estado=qom("select ev.id,ev.nombre,ev.color_co as color from ubicacion u,estado_vehiculo ev
                      where u.estado=ev.id and u.vehiculo=$P->id and u.fecha_inicial<='$Hoy' and u.fecha_final>='$Hoy' order by u.id desc limit 1 ",$LINK);
		        if($Estados[$Estado->id]) $Estados[$Estado->id]->Cantidad++;
		        else $Estados[$Estado->id]=new cls_estado($Estado->id,$Estado->color,$Estado->nombre);
		        if($Estados_ciudad[$P->oficina])
		          if($Estados_ciudad[$P->oficina]->Estados[$Estado->id]) $Estados_ciudad[$P->oficina]->Estados[$Estado->id]->Cantidad++;
		          else $Estados_ciudad[$P->oficina]->Estados[$Estado->id] = new cls_estado($Estado->id,$Estado->color,$Estado->nombre);
		        else $Estados_ciudad[$P->oficina]= new cls_estado_ciudad($P->oficina,$Noficina,$Estado->id,$Estado->color,$Estado->nombre);
			}
			$Conteo_placas++;
		}
		mysql_close($LINK);
		echo $Linea_total;
	}
	else
	{
		echo "No hay información";
	}

  echo "</table><br /><br />
        <iframe id='Menu_contextual_celda' name='Menu_contextual_celda' style='visibility:hidden;position:absolute;z-index:100;' frameborder='no' scrolling='auto' height='10px' width='100px'></iframe>
        <script language='javascript'>
          parent.document.getElementById('estatus').innerHTML='';
          Cargado=true; ";
	if(!inlist($USUARIO,'11,29') /*Aseguradora 1*/)	echo "	window.open('zcontrol_operativo2.php?Acc=pinta_hv&FI=$FI&FF=$FF&ASE=$ASE&OFI=$OFI','hvec'); ";
	echo " </script><iframe id='Oculto_tablero' name='Oculto_tablero' style='visibility:hidden' height='1' width='1'></iframe>";
if($Resumen_estadisticas)
{
	if($USUARIO==29 /* Aseguradora2 */)
	{
		$Estados=q("select * from estado_vehiculo where id not in (93)");
		echo "<table align='center'><tr>";
		while($E=mysql_fetch_object($Estados))
		{
			echo "<td bgcolor='$E->color_co'>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>$E->nombre</td>";
		}
		echo "</tr></table>";
	}
	else
	{
		if(is_array($Estados))
		{
			echo "<table align='center'><tr><th colspan=20>RESUMEN GENERAL DE ESTADOS</TH></tr><tr>
			<td valign='top'><table align='center' cellspacing='3'><tr><th colspan=3>Total $Hoy - $Conteo_placas</th></tr><tr><th>Clr</th><th>Estado</th><th>Cantidad</th></tr>";
			foreach($Estados as $Estado) $Estado->pinta();
			echo "</table></td>";
			foreach($Estados_ciudad as $Estados){echo "<td valign='top'>";$Estados->pinta();echo "</td>";}
			echo "</tr></table>";
		}
		include('inc/link.php');
		if($Oficinas=mysql_query("select distinct oficina,t_oficina(oficina) as noficina from $Tem_Placas order by oficina",$LINK))
		{
			echo "<hr><b>PROMEDIOS DE PARQUEADEROS Y ALISTAMIENTOS POR CIUDAD</B> Puede hacer click sobre la cantidad de cada día para descargar un detallado de las placas.<BR>
			<table border cellspacing='0'><tr>";
			$Dias=array();
			while($Ofi=mysql_fetch_object($Oficinas))
			{
				echo "<td valign='top'><table><tr><td colspan='2'><b>Oficina: $Ofi->noficina</td></tr><tr><th>Diario</th><th>Cantidad</th></tr>";
				$Adiario=array();
				$D=$FI;
				$Diario=0;$Tdiario=0;$Contador=0;
				while($D<=$FF && $D<=$Hoy)
				{
					$Diario=0;
					echo "<tr><td>$D</td>";
					if($Vehiculos=mysql_query("select * from $Tem_Placas where oficina=$Ofi->oficina order by placa",$LINK))
					{
						$Adiario["$D"]='';
						while($V=mysql_fetch_object($Vehiculos))
						{
							//$Estado=qom("select estado,t_estado_vehiculo(estado) as nestado from ubicacion where vehiculo=$V->id and fecha_inicial<='$D' and fecha_final>='$D' order by id desc limit 1",$LINK);
							$Estado=$ED["$V->id-$D"];
							if($Estado==2  /*Parqueadero*/ || $Estado==8 /*alistamiento*/)
							{
								$Diario++;
								$Adiario["$D"].=$V->placa.',';
							}
						}
						echo  "<td align='right'><a class='info' style='cursor:pointer' onclick=\"muestra_diario_parqueadero('".$Adiario["$D"]."','$D');\">$Diario <span style='width:300px'>Click para descargar las placas en parqueadero / alistamiento.</span></a></td></tr>";
					}
					else echo "<td align='right'>".mysql_error()."</td>";
					$Dias["$D"]+=$Diario;
					$D=date('Y-m-d',strtotime(aumentadias($D,1)));
					$Contador++;
					$Tdiario+=$Diario;
				}
				$Promedio=($Contador?round($Tdiario/$Contador,2):0);
				echo "<tr><td>Promedio:</td><td align='right'><b>$Promedio</b></td></tr></table></td>";
			}
			echo "</tr></table><hr>";
			include('inc/chart/Includes/FusionCharts.php');
			$xml="<chart caption='TOTAL PARQUEADEROS POR DIA'>";
			foreach($Dias as $Dia => $cantidad)
			{
				$xml.="<set label='$Dia' value='$cantidad' />";
			}
			$xml.='</chart>';
			echo renderChart('inc/chart/Charts/Line.swf','',$xml,"parqueadero",800,300,false,false);
			echo "<hr><b>ACUMULADO FUERA DE SERVICIO POR CIUDAD entre $FI y $FF</b></br>";
			$Oficinas=mysql_query("select distinct oficina,t_oficina(oficina) as noficina from $Tem_Placas order by oficina",$LINK);
			echo "<table cellspacing=2><tr><th>Oficina</th><th>Cantidad</th><th>Siniestro Asegurado</th>";
			$Cantidad=$Propio=0;
			$xml2="<chart caption='ACUMULADO DE FUERA DE SERVICIO POR CIUDAD' xAxisName='Ciudades' yAxisName='Cantidad' showValues='1' >";
			$categorias="<categories>";
			$serie1="<dataset seriesName='Fuera de Servicio'>";
			$serie2="<dataset seriesName='Siniestros de Asegurados'>";
			while($Ofi=mysql_fetch_object($Oficinas))
			{
				$Fueras=qom("Select count(t.id) as cantidad,sum(t.siniestro_propio) as propio from ubicacion t where t.oficina=$Ofi->oficina and t.estado=5 ".($ASE?" and t.flota=$ASE ":"")." ",$LINK);
				echo "<tr><td>$Ofi->noficina</td><td align='right'>$Fueras->cantidad</td><td align='center'>$Fueras->propio</td></tr>";
				$categorias.="<category label='$Ofi->noficina'/>";
				$serie1.="<set value='$Fueras->cantidad'/> ";
				$serie2.="<set value='$Fueras->propio'/> ";
				$Cantidad+=$Fueras->cantidad;
				$Propio+=$Fueras->propio;
			}
			$categorias.="</categories>";
			$serie1.="</dataset>";
			$serie2.="</dataset>";
			$xml2.=$categorias.$serie1.$serie2."</chart>";

			echo "<tr><th>Total</th><td align='right' bgcolor='dddddd'>$Cantidad</td><td align='center' bgcolor='dddddd'>$Propio</td></tr></table>";
			echo renderChart("inc/chart/Charts/MSColumn3D.swf","",$xml2,"siniestros",800,300,false,false);
		}
		mysql_close($LINK);
	}
}
	echo "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></body>";
}

function pinta_dia_placa($CS /* citas */,$Fec,$P /* placa*/)
{
	global $USUARIO,$Nsin,$Hdevolucion,$Odofinal,$ED,$US,$NTcitas;
	$Contador=0;
	$dia=array();
	for($i=0;$i<count($US[$P->id]);$i++)
	{
		if($US[$P->id][$i]->fecha_inicial<=$Fec && $Fec<=$US[$P->id][$i]->fecha_final)
		{
			if($US[$P->id][$i]->estado==7 /* Servicio Concluido */ )
			{
		          if($Fec<$US[$P->id][$i]->fecha_final)  // Si la fecha es menor que la fecha de conclusión
		          {
						$dia[$Contador]['placa']=$P;
			          	$dia[$Contador]['ubicacion']=$US[$P->id][$i];
			          	if( dias($Fec,$US[$P->id][$i]->fecha_inicial)>7 )
			          	{
			          		$dia[$Contador]['estado']=91;
			          		if($US[$P->id][$i]->fecha_final==$Fec) $dia[$Contador]['span']="$Fec<br><font color='BLUE'>SERVICIO CONCLUIDO ".(inlist($USUARIO,'11,29')?" No.".$Nsin[$US[$P->id][$i]->id]:"")."</font>";
			          	}
			          //	elseif($US[$P->id][$i]->fecha_inicial==$Fec || $US[$P->id][$i]->fecha_final==$Fec)
			          //	{
			          //		$dia[$Contador]['estado']=1;
			          //		$dia[$Contador]['span']="$Fec<br><font color='BLUE'>SERVICIO CONCLUIDO ".(inlist($USUARIO,'11,29')?" No.".$Nsin[$US[$P->id][$i]->id]:"")."</font>";
			          //	}
			          	else
			          	{
			          		$dia[$Contador]['estado']=1;
				            $dia[$Contador]['span']="$Fec<br><font color='BLUE'>SERVICIO CONCLUIDO ".(inlist($USUARIO,'11,29')?" No.".$Nsin[$US[$P->id][$i]->id]:"")."</font>";
			          	}
						$Contador++;
		          }
		          else                          //  si la fecha es la fecha de conclusión muestra 2 estados: servicio y concluido
		          {
			          	$dia[$Contador]['placa']=$P;
			          	$dia[$Contador]['ubicacion']=$US[$P->id][$i];

			          	if( dias($Fec,$US[$P->id][$i]->fecha_inicial)>7 )
			          	{
			          		$dia[$Contador]['estado']=91;
			          		$dia[$Contador]['span']="$Fec<br><font color='BLUE'>SERVICIO CONCLUIDO ".(inlist($USUARIO,'11,29')?" No.".$Nsin[$US[$P->id][$i]->id]:"")."</font>";
			          	}
			          	else
			          	{
			          		$dia[$Contador]['estado']=1;
			          		$dia[$Contador]['span']="$Fec<br><font color='BLUE'>SERVICIO CONCLUIDO ".(inlist($USUARIO,'11,29')?" No.".$Nsin[$US[$P->id][$i]->id]:"")."</font>";
			          	}
			          	$Contador++;
			          	///////////////////   segundo estado de conclusion ///////////////////////
			            $dia[$Contador]['estado']=7;
			            $dia[$Contador]['span']="$Fec<br><font color='BLUE'>CONCLUYO EL SERVICIO ".(inlist($USUARIO,'11,29')?" No.".$Nsin[$US[$P->id][$i]->id]:"")."</font>";
			            $dia[$Contador]['placa']=$P;
			            $dia[$Contador]['ubicacion']=$US[$P->id][$i];
			            $Contador++;
		          }
			}
			elseif($US[$P->id][$i]->estado==1 /* En Servicio */)
			{
				$dia[$Contador]['placa']=$P;
				$dia[$Contador]['ubicacion']=$US[$P->id][$i];
				$dia[$Contador]['estado']=1;
				if (dias($US[$P->id][$i]->fecha_inicial,$Fec)>7)  // si hay sobrepaso de 7 dias en el servicio
				{
					$dia[$Contador]['estado']=91;
					if($US[$P->id][$i]->fecha_final==$Fec) $dia[$Contador]['span']="$Fec<br><font color='red'>SOBREPASO DE 7 DIAS EN EL SERVICIO ".(inlist($USUARIO,'11,29')?" No.".$Nsin[$US[$P->id][$i]->id]:"")."</font>";
				}
				elseif($US[$P->id][$i]->fecha_final==$Fec )
				{
					$dia[$Contador]['span']="$Fec<br><font color='green'>PRESTANDO SERVICIO ".(inlist($USUARIO,'11,29')?" No.".$Nsin[$US[$P->id][$i]->id]:" Regresa a las ".$Hdevolucion[$US[$P->id][$i]->id])."</font>";
					if($USUARIO!=29) $dia[$Contador]['espacio']=l($Hdevolucion[$US[$P->id][$i]->id],5);
				}
				//elseif($US[$P->id][$i]->fecha_inicial==$Fec)
				else $dia[$Contador]['span']="$Fec<br><font color='green'>PRESTANDO SERVICIO ".(inlist($USUARIO,'11,29')?" No.".$Nsin[$US[$P->id][$i]->id]:"")."</font>";
				$Contador++;
			}
			elseif($US[$P->id][$i]->estado==4 ) // mantenimiento
			{
				$dia[$Contador]['placa']=$P;
				$dia[$Contador]['ubicacion']=$US[$P->id][$i];
				$dia[$Contador]['estado']=4;
				if(dias($US[$P->id][$i]->fecha_inicial,$Fec)>2 && !inlist($USUARIO,'11,29'))
				{
					if($US[$P->id][$i]->fecha_final==$Fec) $dia[$Contador]['span']="$Fec<br><font color='green'>".$US[$P->id][$i]->nestado." Sobrepaso de 2 días, requiere justificación.</font>";
					$dia[$Contador]['estilo']="text-decoration:blink;color:ffffff;font-weight:bold;";
					$dia[$Contador]['espacio']='X';
				}
				elseif($US[$P->id][$i]->fecha_inicial==$Fec || $US[$P->id][$i]->fecha_final==$Fec) $dia[$Contador]['span']="$Fec<br><font color='green'>".$US[$P->id][$i]->nestado."</font>";
				$Contador++;
			}
			elseif($US[$P->id][$i]->estado==5 ) // fuera de servicio
			{
				$dia[$Contador]['placa']=$P;
				$dia[$Contador]['ubicacion']=$US[$P->id][$i];
				$dia[$Contador]['estado']=5;
				if(dias($US[$P->id][$i]->fecha_inicial,$Fec)>7 && !inlist($USUARIO,'11,29'))
				{
					if($US[$P->id][$i]->fecha_final==$Fec) $dia[$Contador]['span']="$Fec<br><font color='green'>".$US[$P->id][$i]->nestado." Sobrepaso de 7 días, requiere justificación.</font>";
					$dia[$Contador]['estilo']="text-decoration:blink;color:ffffff;font-weight:bold;";
					$dia[$Contador]['espacio']='X';
				}
				//elseif($US[$P->id][$i]->fecha_inicial==$Fec || $US[$P->id][$i]->fecha_final==$Fec) $dia[$Contador]['span']="$Fec<br><font color='green'>".$US[$P->id][$i]->nestado."</font>";
				else $dia[$Contador]['span']="$Fec<br><font color='green'>".$US[$P->id][$i]->nestado."</font>";
				$Contador++;
			}
			else                                        // demás estados: Parqueadero, Mantenimiento, Mant. programado, Gerencia, Transito, traslados, fuera de servicio
			{
				$dia[$Contador]['placa']=$P;
				$dia[$Contador]['ubicacion']=$US[$P->id][$i];
				$dia[$Contador]['estado']=$US[$P->id][$i]->estado;
				//if($US[$P->id][$i]->fecha_inicial==$Fec || $US[$P->id][$i]->fecha_final==$Fec)
					$dia[$Contador]['span']="$Fec<br><font color='green'>".$US[$P->id][$i]->nestado."</font>";
				$Contador++;
			}
			$Odofinal=($US[$P->id][$i]->odometro_final>$Odofinal?$US[$P->id][$i]->odometro_final:$Odofinal);
		}
	}
	if(count($dia))
	{
		switch(count($dia))
		{
			case 1:$Resultado=pinta_est($dia[0],$Fec,50);$ED["$P->id-$Fec"]=$dia[0]['estado']; break;
			case 2:$Resultado=pinta_est($dia[0],$Fec,25).pinta_est($dia[1],$Fec,24);$ED["$P->id-$Fec"]=$dia[1]['estado'];break;
			case 3:$Resultado=pinta_est($dia[0],$Fec,16).pinta_est($dia[1],$Fec,16).pinta_est($dia[2],$Fec,16);$ED["$P->id-$Fec"]=$dia[2]['estado'];break;
			case 4:$Resultado=pinta_est($dia[0],$Fec,12).pinta_est($dia[1],$Fec,12).pinta_est($dia[2],$Fec,11).pinta_est($dia[3],$Fec,12);$ED["$P->id-$Fec"]=$dia[3]['estado'];break;
			case 5:$Resultado=pinta_est($dia[0],$Fec,9).pinta_est($dia[1],$Fec,9).pinta_est($dia[2],$Fec,9).pinta_est($dia[3],$Fec,9).pinta_est($dia[4],$Fec,10);$ED["$P->id-$Fec"]=$dia[4]['estado'];break;
			case 6:$Resultado=pinta_est($dia[0],$Fec,8).pinta_est($dia[1],$Fec,7).pinta_est($dia[2],$Fec,8).pinta_est($dia[3],$Fec,7).pinta_est($dia[4],$Fec,8).pinta_est($dia[5],$Fec,7);$ED["$P->id-$Fec"]=$dia[5]['estado'];break;
			case 7:$Resultado=pinta_est($dia[0],$Fec,6).pinta_est($dia[1],$Fec,6).pinta_est($dia[2],$Fec,6).pinta_est($dia[3],$Fec,7).pinta_est($dia[4],$Fec,7).pinta_est($dia[5],$Fec,6).pinta_est($dia[6],$Fec,6);$ED["$P->id-$Fec"]=$dia[6]['estado'];break;
			case 8:$Resultado=pinta_est($dia[0],$Fec,5).pinta_est($dia[1],$Fec,5).pinta_est($dia[2],$Fec,6).pinta_est($dia[3],$Fec,6).pinta_est($dia[4],$Fec,6).pinta_est($dia[5],$Fec,5).pinta_est($dia[6],$Fec,5).pinta_est($dia[7],$Fec,5);$ED["$P->id-$Fec"]=$dia[7]['estado'];break;
			case 9:$Resultado=pinta_est($dia[0],$Fec,4).pinta_est($dia[1],$Fec,5).pinta_est($dia[2],$Fec,5).pinta_est($dia[3],$Fec,5).pinta_est($dia[4],$Fec,5).pinta_est($dia[5],$Fec,5).pinta_est($dia[6],$Fec,5).pinta_est($dia[7],$Fec,4).pinta_est($dia[8],$Fec,4);$ED["$P->id-$Fec"]=$dia[8]['estado'];break;
		}
		return $Resultado;
	}
	$Clase_blanco='cb';
	$Span='';
	if(!inlist($USUARIO,'11,29'))
	{
		for($i=0;$i<count($CS[$P->id]);$i++)
		{
			$Fec_fin=date('Y-m-d',strtotime(aumentadias($CS[$P->id][$i]->fecha,7)));
			if($Fec>=$CS[$P->id][$i]->fecha && $Fec<=$Fec_fin)
			{
				$Clase_blanco='cc';
				$Span="<span ".($USUARIO==1?"onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTcitas&id=".$CS[$P->id][$i]->idc." ',0,0,800,900,'mc');\"":"")."><table><tr><th>Cita de Entrega</th></tr><tr><td nowrap='yes'>Siniestro: <b>".$CS[$P->id][$i]->nsiniestro."</b></td></tr>";
				$Span.="<tr><td>Fecha y hora: <b>".$CS[$P->id][$i]->fecha.' '.$CS[$P->id][$i]->hora."</b></td></tr>";
				$Span.="<tr><td>Conductor: <b>".$CS[$P->id][$i]->conductor."</b></td></tr>";
				$Span.="<tr><td>Agendada por: <b>".$CS[$P->id][$i]->agendada_por."</b></td></tr></table></span>";
				break;
			}
		}
	}
	if(inlist($USUARIO,'1,2,7,10,13,27'))
		$Resultado="<a class='$Clase_blanco' oncontextmenu=\"menu_blanco($P->id,'$Fec');void(null);return false;\">&nbsp;$Span</a>";
	else
		$Resultado="<a class='$Clase_blanco' oncontextmenu=\"alert('No tiene acceso a esta opción\");void(null);return false;\">&nbsp;$Span</a>";
	return $Resultado;
}

function pinta_est($P,$F,$t)
{
	global $Emb_Aseg,$USUARIO;

	$espacios=$P['espacio']?$P['espacio']:"&nbsp;";
	if($P['span'])
	{
		if(!inlist($USUARIO,'11,29') /*Aseguradora 1*/)
		{
			$Dif_odometros=$P['ubicacion']->odometro_final-$P['ubicacion']->odometro_inicial;
			if($Dif_odometros<0) $Dif="<font color='red'>".coma_format($Dif_odometros)."</font>";
			elseif($Dif_odometros==0) $Dif="0";
			elseif($Dif_odometros>700) $Dif="<font color='ff3355'>".coma_format($Dif_odometros)."</font>";
			else $Dif="<font color='green'>".coma_format($Dif_odometros)."</font>";
		}
		if($P['ubicacion']->siniestro_propio) $Siniestro_propio="<tr><td colspan=2><font color='red'><b>Siniestro Asegurado</b></font></td></tr>"; else $Siniestro_propio="";
		$Resultado="<a class='c".$P['estado']."' style='width:".$t."px;".$P['estilo']."' ";
		$Resultado.=(!inlist($USUARIO,'11,29')?"oncontextmenu=\"mc_evento(".$P['ubicacion']->id.",'$F',this);return false;void(null);\" onclick=\"mc_evento(".$P['ubicacion']->id.",'$F',this);\" ":"");
		$Resultado.=">$espacios<span>".
		"<table><tr><td rowspan=2><img src='".($P['placa']->flota==6?$Emb_Aseg[$P['ubicacion']->flota]:($P['placa']->flota==$P['ubicacion']->flota?$Emb_Aseg[$P['ubicacion']->flota]:$P['placa']->emb1)).
		"' border='0' height='30px'></td><td><b class='sp_placa'>" . $P['placa']->placa."</b></td></tr>".
		"<tr><td nowrap='yes'>".$P['span']."</td></tr><tr><td nowrap='yes' colspan=2>".$P['ubicacion']->noficina."</td></tr>".$Siniestro_propio;
		if(!inlist($USUARIO,'11,29') /*Aseguradora 1*/)
		$Resultado.="<tr><td colspan='2'>Fecha: ".$P['ubicacion']->fecha_inicial." - ".$P['ubicacion']->fecha_final."</td></tr>".
								"<tr><td nowrap='yes' colspan='2' style='color:333399;font-weight:bold;'>Kilometraje: ".
								coma_format($P['ubicacion']->odometro_inicial)." - ".coma_format($P['ubicacion']->odometro_final)." Diferencia: $Dif</td></tr>";
		$Resultado.="</table></span></a>";
	}
	else
	{
		$Resultado="<a class='c".$P['estado']."' style='width:".$t."px;".$P['estilo']."' ";
		$Resultado.=(!inlist($USUARIO,'11,29')?"oncontextmenu=\"alert('busque el extremo del estado');return false;void(null);\" onclick=\"alert('busque el extremo del estado');\" ":"");
		$Resultado.=">$espacios</a>";
	}

	return $Resultado;
}

function pinta_fechas()
{
	global $FI,$FF,$ASE,$OFI,$RE,$PI,$MARCA;
	html();
	echo "<style type='text/css'>
					.cn {display:inline-block;font-weight:bold;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;cursor:pointer;}
					.cnt {display:inline-block;font-weight:bold;background-color:006644;color:ffffaa;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;}
					tr:hover {background-color: #ddddbb;}

				</style>
				<script language='javascript'>
					function cambia_fi(fecha)
					{
						parent.document.forma.FI.value=fecha;
						parent.document.forma.submit();
					}
				</script>
	<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0' bgcolor='eeffee' >
	<table cellspacing='0' cellpadding='0'><tr><td nowrap='yes'>";
	pinta_meses($FI,$FF,50);
	for ($dia=$FI;$dia<=$FF;$dia=date('Y-m-d',strtotime(aumentadias($dia,1))))
	{
		$dd=date('d',strtotime($dia));
		$Dia=date('w',strtotime($dia));
		$Ndia=dia_semana($Dia);
		if($Dia==0 || $Dia==6)
		{
			if($Dia==0) $Fondo='ffaaaa'; else $Fondo='aaaaff';
		}
		else $Fondo='dddddd';

		echo "<span class='cn' style='background-color:$Fondo' onclick=\"cambia_fi('$dia');\">$Ndia $dd</span>";
	}
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>
	<script language='javascript'>window.open('zcontrol_operativo2.php?Acc=pinta_placas&FI=$FI&FF=$FF&ASE=$ASE&OFI=$OFI&RE=$RE&PI=$PI&MARCA=$MARCA','placas');</script>
	</body>";
}

function dia_semana($d)
{
	switch($d)
	{
		case 0: return 'Do';
		case 1: return 'Lu';
		case 2: return 'Ma';
		case 3: return 'Mi';
		case 4: return 'Ju';
		case 5: return 'Vi';
		case 6: return 'Sá';
	}
}

function pinta_meses($FI,$FF,$tamano)
{
	$mm=0;
	$tam=0;
	for ($dia=$FI;$dia<=$FF;$dia=date('Y-m-d',strtotime(aumentadias($dia,1))))
	{
		$mes=date('Y-m',strtotime($dia));
		if($mm!=$mes)
		{
			if($mm)
			{
				$tam--;
				echo "<script language='javascript'>document.getElementById('m$mm').style.width=$tam;</script>";
			}
			$mm=$mes;
			$tam=0;
			echo "<span class='cnt' id='m$mes'>".date('Y',strtotime($dia)).' - '.mes(date('m',strtotime($dia)))."</span>";
		}
		$tam+=$tamano+1;
	}
	if($mm)
	{
		$tam--;
		echo "<script language='javascript'>document.getElementById('m$mm').style.width=$tam;</script>";
	}
	echo "<br>";
}

function pinta_placas()
{
	global $FI,$FF,$ASE,$OFI,$USUARIO,$RE,$PI,$Tem_Placas,$MARCA,$Vhe;
	html();
	$Nt1=tu('ubicacion','id');
	$Nt2=tu('vehiculo','id');
	$Nt3=tu('hv_vehiculo','id');
	echo "<style type='text/css'>
					.pt {font-size:11px;display:inline-block;font-weight:bold;background-color:dddeee;color:561409;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;margin-top:0px;
					cursor:pointer;}
					tr:hover {background-color: #ddeedd;}
					.mcpl {font-size:14px;background-color:ffffdd; }
				</style>
				<script language='javascript'>

				function menuplaca(Placa,idPlaca,Odofinal)     // menu contextual del top de la tabla o de los titulos de las columnas
				{
					Menu=document.getElementById('Menu_contextual_placa');
					Menu.style.visibility='visible';
					Menu.style.left=10;
					Menu.style.top=mouseY-10;
					var Contenido=\"<table border=3 cellspacing='0' cellpadding='0' bgcolor='#bbffff' name='Context_Opciones' id='Context_Opciones'>\";
					Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"oculta_menuplaca();\\\" align='center'><img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar menu</b></td></tr>\";
					Contenido+=\"<tr><td align='center' class='placa' style='background-image:url(img/placa.jpg);'>\"+Placa+\"</td></tr>\"; ";
	if(inlist($USUARIO,'1,2,3,7,10,13,23,27'))
	{
		echo "Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt1&VINCULOT=\"+idPlaca+\"&VINCULOC=vehiculo',0,0,600,900,'hp');\\\">Historia</b></td></tr>\"; ";
	}
	if(inlist($USUARIO,'1,2,7,10,13,23'))
	{
		echo "Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt3&VINCULOT=\"+Placa+\"&VINCULOC=placa',0,0,600,900,'hp');\\\">Hoja de Vida</b></td></tr>\"; ";
	}
	if(inlist($USUARIO,'1,2,7'))
	{
		echo "Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$Nt2&id=\"+idPlaca+\"',0,0,600,900,'hp');\\\">Definición</b></td></tr></table>\";";
	}
	echo "	Menu.innerHTML=Contenido;
				}

					function oculta_menuplaca()
					{
						Menu=document.getElementById('Menu_contextual_placa');
						Menu.style.visibility='hidden';
					}

					function marca_placa(Objeto)
					{
						parent.document.getElementById('hvec').contentWindow.document.getElementById(Objeto.id).style.backgroundColor='ddeedd';
					}
					function desmarca_placa(Objeto)
					{
						parent.document.getElementById('hvec').contentWindow.document.getElementById(Objeto.id).style.backgroundColor='';
					}
				</script>
		<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0'><span id='Menu_contextual_placa' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#ddfdfd;'></span>";

	include('inc/link.php');
	mysql_query("drop table if exists $Tem_Placas",$LINK);
	mysql_query("create table $Tem_Placas (id int(10) primary key not null auto_increment, placa char(10), emb1 varchar(200), flota smallint(4) default 0,
		emb3 varchar(200),sigla varchar(10),oficina smallint(4) not null default 0,odofinal int(10) not null default 0,linea smallint(4) not null default 0)",$LINK);
	if($Placas=mysql_query("select v.id,v.placa,if(l.emblema_f!='',l.emblema_f,m.emblema_f) as emb1,v.flota,a.emblema_f as emb3,o.sigla as noficina,v.ultima_ubicacion,v.linea
		FROM vehiculo v,linea_vehiculo l,marca_vehiculo m,aseguradora a,oficina o
		WHERE m.id=l.marca and l.id=v.linea and v.flota=a.id and (v.inactivo_desde='0000-00-00' || v.inactivo_desde>'$FI' ) ".($ASE?" and v.flota=$ASE ":"").($OFI?" and o.id=$OFI ":"").
		($PI?" and v.placa='$PI' ":"").($MARCA?" and l.marca=$MARCA ":"")." and o.id=v.ultima_ubicacion
		 order by v.placa ",$LINK))
	{
		echo "<table width='100%' cellspacing='1' cellpadding='0' bgcolor='aaaaaa'>";
		$Contador=1;
		while($P=mysql_fetch_object($Placas))
		{
			mysql_query("insert into $Tem_Placas (id,placa,emb1,flota,emb3,sigla,oficina,linea) values ('$P->id','$P->placa','$P->emb1','$P->flota','$P->emb3','$P->noficina','$P->ultima_ubicacion','$P->linea')",$LINK);
			echo "<tr id='p_$Contador' onmouseover='marca_placa(this)' onmouseout='desmarca_placa(this)'><td>$Contador</td><td align='right' nowrap='yes' bgcolor='ffffff'>
								<img src='$P->emb3' border='0' height='11'  vspace='0'>
								<img src='$P->emb1' border='0' height='11' width='16'  vspace='0'>";
			if(!inlist($USUARIO,'11,29') /*Aseguradora 1*/)
				echo "<span class='pt' id='pt_$P->placa' oncontextmenu=\"menuplaca('$P->placa',$P->id);void(null);return false;\" onclick=\"menuplaca('$P->placa',$P->id);\">$P->placa</span>";
			else
				echo "<span class='pt'>$P->placa</span>";
				echo "</td><td>$P->noficina</td></tr>";
			$Contador++;
			$Vhe[$P->id]['placa']=$P->placa;
			$Vhe[$P->id]['oficina']=$P->ultima_ubicacion;
		}
		echo "</table>";
	}
	else
	{
		echo "No hay información";
	}
	echo "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
				<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
				<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";
	if(!inlist($USUARIO,'11,29') /*Aseguradora 1*/) echo "<script language='javascript'>window.open('zcontrol_operativo2.php?Acc=control_operativo&FI=$FI&FF=$FF&ASE=$ASE&OFI=$OFI&PI=$PI&Resumen_estadisticas=$RE','tablero');</script>";
	echo "</body>";
}

function pinta_hv()
{
	global $FI,$FF,$ASE,$OFI,$USUARIO,$Tem_Placas;
	html();
	$Hoy=date('Y-m-d');
	$Nt3=tu('hv_vehiculo','id');
	echo "<style type='text/css'>
					.pt {font-size:11px;display:inline-block;font-weight:bold;color:561409;margin-left:1px;margin-bottom:1px;margin-top:0px;
					cursor:pointer;}
					.pt:hover {background-color: #ddeedd;}
					tr:hover {background-color: #ddeedd;}
					.mcpl {font-size:14px;background-color:ffffdd; }
				</style>
				<script language='javascript'>

					function historia_fotografica(idPlaca)
					{	modal('zcontrol_operativo2.php?Acc=historia_fotografica&idPlaca='+idPlaca,0,0,500,800,'hf');	}
					function adiciona_mantenimiento(Placa)
					{	modal('zcontrol_operativo2.php?Acc=adiciona_mantenimiento&Placa='+Placa,0,0,500,800,'hf');	}
					function adiciona_soat(Placa)
					{	modal('zcontrol_operativo2.php?Acc=adiciona_soat&Placa='+Placa,0,0,500,800,'hf');	}

					function menuplaca(Placa,idPlaca,ProxMant)     // menu contextual del top de la tabla o de los titulos de las columnas
					{
						Menu=document.getElementById('Menu_contextual_placa');
						Menu.style.visibility='visible';
						Menu.style.left=0;
						Menu.style.top=mouseY-10;
						var Contenido=\"<table border=3 cellspacing='0' cellpadding='0' bgcolor='#bbffff' name='Context_Opciones' id='Context_Opciones'>\";
						Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"oculta_menuplaca();\\\" align='center'><img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar menu</b></td></tr>\";
						Contenido+=\"<tr><td align='center' class='placa' style='background-image:url(img/placa.jpg);'>\"+Placa+\"</td></tr>\";
						Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"historia_fotografica('\"+idPlaca+\"');\\\"><img src='gifs/camara.png' border='0'> Ver historia<br>fotográfica</td></tr>\";
						Contenido+=\"<tr><td nowrap='yes'>Próximo<br>Mantenimiento:<br><b>\"+ProxMant+\"</b></td></tr>\";
						Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"adiciona_mantenimiento('\"+Placa+\"');\\\"><img src='gifs/standar/si.png' border='0'> Insertar<br>Mantenimiento</td></tr>\";
						Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"adiciona_soat('\"+Placa+\"');\\\"><img src='gifs/standar/si.png' border='0'> Insertar<br>SOAT</td></tr>\"; ";
	if(inlist($USUARIO,'1,2,7,13'))
	{
		echo "Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt3&VINCULOT=\"+Placa+\"&VINCULOC=placa',0,0,600,900,'hp');\\\">Hoja de Vida</b></td></tr>\"; ";
	}
	echo "	Contenido+=\"</table>\";
						Menu.innerHTML=Contenido;
					}

					function oculta_menuplaca()
					{
						Menu=document.getElementById('Menu_contextual_placa');
						Menu.style.visibility='hidden';
					}

					function marca_placa(Objeto)
					{	parent.document.getElementById('placas').contentWindow.document.getElementById(Objeto.id).style.backgroundColor='ddeedd';}
					function desmarca_placa(Objeto)
					{	parent.document.getElementById('placas').contentWindow.document.getElementById(Objeto.id).style.backgroundColor='';}
				</script>
		<body leftmargin='0' topmargin='0' rightmargin='1' bottommargin='0'>";

	if($Placas=q("select v.*,m.manten_cada from $Tem_Placas v,linea_vehiculo l , marca_vehiculo m where v.linea=l.id and l.marca=m.id order by v.placa "))
	{
		echo "<table width='100%' cellspacing='1' cellpadding='0' bgcolor='cccccc'>";
		require('inc/link.php');
		$Contador=1;
		while($P=mysql_fetch_object($Placas))
		{
			$odofinal=coma_format($P->odofinal);
			echo "<tr id='p_$Contador' onmouseover='marca_placa(this)' onmouseout='desmarca_placa(this)' >";
			$Km_ultimo_mantenimiento=qo1m("select kilometraje from hv_vehiculo where placa='$P->placa' and novedad='MNT' order by kilometraje desc limit 1",$LINK);
			$BGcolor='eeeeee';
			$Decoracion='';
			if($Km_ultimo_mantenimiento <= $P->odofinal) // si el ultimo mantenimiento es menor que el odometro actual (esta deberia ser la condicion normal)
			{
				// Se calcula el proximo mantenimiento de acuerdo a la  marca menos 500 kilometros para la olgura
				$Proximo_mantenimiento = $Km_ultimo_mantenimiento+$P->manten_cada;
				$Proximo_umbral=$Proximo_mantenimiento-700;
				if($P->odofinal > $Proximo_umbral)
				{
					if($P->odofinal > ($Proximo_mantenimiento))
						$BGcolor='ff0000'; // si el odometro supera el próximo mantenimiento despues del umbral, se pone rojo
					else
						$BGcolor='ffff00';  // si el odometro supera el proximo mantenimiento dentro del umbral, se pone amarillo
					echo "<td bgcolor='$BGcolor'><img src='img/mantenimiento.png' height='11' border='0' alt='Mantenimiento' title='Mantenimiento'></td>";
					$Decoracion="style='text-decoration:blink'";
				}
				else
					echo "<td bgcolor='$BGcolor'></td>";
			}
			else
			{
				echo "<td bgcolor='$BGcolor'></td>";
			}
			if($FS=qo1m("select fecha from hv_vehiculo where placa='$P->placa' and novedad='SOA' order by fecha desc limit 1",$LINK))
			{
				$FS=date('Y-m-d',strtotime(aumentameses($FS,12)));
				if(date('Y-m-d',strtotime($FS))<date('Y-m-d',strtotime($Hoy)))
				{
					echo "<td bgcolor='ff0000'><img src='img/soat.png' height='11' border='0' alt='SOAT' title='SOAT'></td>";
				}
				else
				{
					if(date('Y-m-d',strtotime(aumentameses($FS,-1)))<date('Y-m-d',strtotime($Hoy)))
					{
						echo "<td bgcolor='ffff00'><img src='img/soat.png' height='11' border='0' alt='SOAT' title='SOAT'></td>";
					}
					else
					{
						echo "<td></td>";
					}
				}
			}
			else
			{
				echo "<td bgcolor='ff0000'><img src='img/soat.png' height='11' border='0' alt='SOAT' title='SOAT'></td>";
			}
			echo "<td align='right' onclick=\"menuplaca('$P->placa',$P->id,'".coma_format($Proximo_mantenimiento)."');\" $Decoracion><span class='pt'>$odofinal</span></td><td align='right'>$Contador</td></tr>";
			$Contador++;
		}
		mysql_close($LINK);
		echo "</table>";
	}
	else
	{
		echo "No hay información";
	}
	echo "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
				<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
				<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
				<span id='Menu_contextual_placa' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#ddfdfd;'></span>
				</body>";
}

//****-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function menu_contextual_celda()
{
	global $idub,$fecha,$objeto,$Emb_Aseg,$Tem_Placas,$USUARIO;
	$D=qo("Select u.*,t_vehiculo(vehiculo) as nvehiculo,e.nombre as nestado,e.color_co from ubicacion u,estado_vehiculo e where u.id=$idub and e.id=u.estado");
	$V=qo("select * from $Tem_Placas where id=$D->vehiculo ");
	$Ofi=qo("select * from oficina where id=$V->oficina");
	$Nplaca=substr($D->nvehiculo,0,3).'-'.substr($D->nvehiculo,3,3);
	$U=qo("select * from ubicacion where id=$idub");
	$Fc=fecha_completa($fecha);
	$Ultimo=qo("select * from ubicacion where vehiculo=$D->vehiculo and fecha_final<='$fecha' order by fecha_final desc, id desc limit 1");
	html();
	$Nt1=tu('siniestro','id');
	$Nt2=tu('ubicacion','id');
	$Nt3=tu('cita_servicio','id');
	echo "
		<script language='javascript'>
			function abre_siniestro(ids)
			{modal('marcoindex.php?Acc=mod_reg&id='+ids+'&Num_Tabla=$Nt1',0,0,700,1000,'siniestro');}

			function abre_ubicacion()
			{modal('marcoindex.php?Acc=mod_reg&id=$idub&Num_Tabla=$Nt2',0,0,700,1000,'ubicacion');}

			function abre_cita(id)
			{modal('marcoindex.php?Acc=mod_reg&id='+id+'&Num_Tabla=$Nt3',0,0,700,1000,'cita');}

			function borrar_ubicacion()
			{if(confirm('Seguro de eliminar esta ubicacion?'))
				{window.open('zcontrol_operativo2.php?Acc=borrar_ubicacion&id='+$idub,'Oculto_tablero');}}

			function desliga_siniestro(ids)
			{if(confirm('Seguro de desligar el estado de este siniestro?'))
				{window.open('zcontrol_operativo2.php?Acc=desligar_siniestro&ids='+ids,'Oculto_tablero');}}

			function ajustar_fecha_inicial()
			{window.open('zcontrol_operativo2.php?Acc=ajustar_fecha_inicial&id=$idub&fecha=$fecha','Oculto_tablero');}

			function ajustar_fecha_final()
			{window.open('zcontrol_operativo2.php?Acc=ajustar_fecha_final&id=$idub&fecha=$fecha','Oculto_tablero');}

			function adicionar_evento()
			{window.open('zcontrol_operativo2.php?Acc=adicionar_evento&vehiculo=$D->vehiculo&fecha=$fecha&Nomueve=1','Menu_contextual_celda');}

			function cerrar_mantenimiento()
			{window.open('zcontrol_operativo2.php?Acc=cerrar_mantenimiento&idub=$idub&Nomueve=1','Menu_contextual_celda');}

			function cerrar_fuera_servicio()
			{window.open('zcontrol_operativo2.php?Acc=cerrar_fuera_servicio&idub=$idub&Nomueve=1','Menu_contextual_celda');}

			function cerrar_transito()
			{window.open('zcontrol_operativo2.php?Acc=cerrar_transito&idub=$idub&Nomueve=1','Menu_contextual_celda');}

			function cerrar_alistamiento()
			{window.open('zcontrol_operativo2.php?Acc=cerrar_alistamiento&idub=$idub&Nomueve=1','Menu_contextual_celda');}

			function activar_mantenimiento()
			{window.open('zcontrol_operativo2.php?Acc=activar_mantenimiento&idub=$idub&Nomueve=1','Menu_contextual_celda');}

			function cerrar_servicio_especial()
			{window.open('zcontrol_operativo2.php?Acc=cerrar_servicio_especial&idub=$idub&Nomueve=1','Menu_contextual_celda');}

			function adicionar_observaciones()
			{window.open('zcontrol_operativo2.php?Acc=adicionar_observaciones&id=$idub','Menu_contextual_celda');}

			function cambiar_a_fueraservicio()
			{window.open('zcontrol_operativo2.php?Acc=cambiar_a_fueraservicio&id=$idub','Menu_contextual_celda');}

			function cambiar_a_mantenimiento()
			{window.open('zcontrol_operativo2.php?Acc=cambiar_a_mantenimiento&id=$idub','Menu_contextual_celda');}

			function marcar_siniestro_propio()
			{window.open('zcontrol_operativo2.php?Acc=marcar_siniestro_propio&id=$idub','Menu_contextual_celda');}

			function desmarcar_siniestro_propio()
			{window.open('zcontrol_operativo2.php?Acc=desmarcar_siniestro_propio&id=$idub','Menu_contextual_celda');}

			function ver_autorizaciones(id)
			{modal('zautorizaciones.php?Acc=ver_autorizaciones&id='+id,0,0,500,900,'Autorizaciones');}

			function ver_pendientes_alistamiento()
			{modal('zalistamiento.php?Acc=ver_pendientes&id=$D->vehiculo',0,0,500,900,'Pendientes');}

			function cambio_temporal()
			{modal('zcontrol_operativo2.php?Acc=cambio_temporal&id=$idub',0,0,400,500,'Cambio_temporal');}

			function retorno_flota()
			{if(confirm('Desea retornar este Vehículo a su flota original?')) {modal('zcontrol_operativo2.php?Acc=retorno_flota&id=$idub',0,0,10,10,'Retorno');}}

			function cambiar_fecha_devolucion()
			{modal('zcontrol_operativo2.php?Acc=cambiar_fecha_devolucion&id=$idub',0,0,400,500,'cambio_fecha');}

		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#bbbbbb' name='Context_Celda' id='Context_Celda' align='center' width='400px'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();'><img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar menu</b></td>
		<td rowspan='3' align='center' bgcolor='eeeeee'>".($U->flota==$V->flota?"<img src='".$Emb_Aseg[$U->flota]."' border='0' align='middle' height='30px'>":"")." <img src='$V->emb1' border='0' align='middle' height='30px'></td></tr>
		<tr><td colspan='1' align='center' bgcolor='eeeeee'>Fecha: <b>$Fc</b></td></tr>
		<tr><td align='center' colspan='1' bgcolor='eeeeee' class='placa' style='background-image:url(img/placa.jpg);'>$Nplaca</td></tr>
		<tr><td bgcolor='eeeeee'>Oficina: <b>$Ofi->nombre</b></td>
		<td bgcolor='eeeeee'>Odómetro: <b>".coma_format($D->odometro_inicial)."</b> - <b>".coma_format($D->odometro_final)."</b> Diferencia: <span style='background-color:ffffff'>";
	$Diferencia_odometros=$D->odometro_final-$D->odometro_inicial;
	if($Diferencia_odometros==0) echo "<b> 0</b>";
	elseif($Diferencia_odometros<0) echo "<b style='color:red'>".coma_format($Diferencia_odometros)."</b>";
	elseif($Diferencia_odometros>700) echo "<b style='color:ff3344;'>".coma_format($Diferencia_odometros)."</b>";
	else echo "<b style='color:green;'>".coma_format($Diferencia_odometros)."</b>";
	echo "</span></td></tr>
		<tr><td nowrap='yes' bgcolor='eeeeee'>Estado: <b>$D->nestado</b></td><td bgcolor='eeeeee' align='center'><div style='width:50px;background-color:$D->color_co;'>&nbsp;</div></td></tr>
		<tr><td nowrap='yes' colspan='2' bgcolor='eeeeee'>Fec.Evento: <b>$D->fecha_inicial - $D->fecha_final</b></td></tr>";
	if(inlist($USUARIO,'1,2,7,10,13,26,27,23'))
	{
		if(($fecha==date('Y-m-d') && $D->estado!=1 && $D->estado!=91) || $USUARIO==1)
		{
			if($Ultimo->estado==92 || $D->estado==92 /* Mantenimiento Programado*/)
			{echo "<tr><td onclick='activar_mantenimiento();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Activar el Mantenimiento</td></tr>";}
			elseif($Ultimo->estado==93 || $D->estado==93 /* Servicio especial Gerencia */)
			{echo "<tr><td onclick='cerrar_servicio_especial();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cerrar el Servicio Especial Gerencia</td></tr>";}
			elseif($Ultimo->estado==2 /*parqueadero */ || $Ultimo->estado==7 /*concluido*/ || $Ultimo->estado==94 /*traslados*/ || $Ultimo->estado==96 /*Domicilio*/)
			{
				if(inlist($USUARIO,'1,2,7,10,13,27'))  /* solamente pueden adicionar Gerencia y Jefe Operativo, Operario Oficina, Auxiliar de Informacion, Jefatura de Flotas */
				{
					echo "<tr><td onclick='adicionar_evento();' style='cursor:pointer' colspan='2'><img src='gifs/standar/nuevo_registro_blanco.png' border='0'> Adicionar un Estado</td></tr>";
				}
				if(inlist($USUARIO,'1,2,7,26')) /* Gerencia, Jefe Operativo, Coordinador Call Center*/
				{
					if($U->flota==$V->flota)
						echo "<tr><td onclick='cambio_temporal();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cambio Temporal de Flota</td></tr>";
					else
						echo "<tr><td onclick='retorno_flota();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Retorno de Flota</td></tr>";
				}
			}
			elseif($Ultimo->estado==4 /*Mantenimiento*/)
			{echo "<tr><td onclick='cerrar_mantenimiento();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cerrar el Mantenimiento</td></tr>";}
			elseif($Ultimo->estado==5 /*Fuera de servicio*/)
			{echo "<tr><td onclick='cerrar_fuera_servicio();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cerrar el Fuera de Servicio</td></tr>";}
			elseif($Ultimo->estado==6 /*En transito*/)
			{echo "<tr><td onclick='cerrar_transito();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cerrar el Transito</td></tr>";}
			elseif($Ultimo->estado==8 /*Alistamiento*/)
			{echo "<tr><td onclick='ver_pendientes_alistamiento();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Ver Pendientes de Alistamiento</td></tr>";}
	//		if(inlist($USUARIO,'1') && $D->estado==8)  // ALISTAMIENTO y los usuarios: Administrador, Jefe Operativo, Jefe de Flotas, Jefes de Ciudad
	//		{echo "<tr><td onclick='cerrar_alistamiento();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cerrar el Alistamiento</td></tr>";}
		}
	}


	if(inlist($USUARIO,'1,2,7') && $D->estado==4) // jefe operativo
	{echo "<tr><td onclick='cambiar_a_fueraservicio();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'>Cambiar a <b>Fuera de Servicio</b></td></tr>";}
	if(inlist($USUARIO,'1,2,7') && $D->estado==5) // jefe operativo
	{echo "<tr><td onclick='cambiar_a_mantenimiento();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'>Cambiar a <b>Mantenimiento</b></td></tr>";}
	if(inlist($USUARIO,'1,2,3') && $D->estado==5) // control operativo
	{echo "<tr><td onclick='marcar_siniestro_propio();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'>Marcar <b>Siniestro Asegurado</b></td></tr>";}
	if(inlist($USUARIO,'1,2,3') && $D->estado==5) // control operativo
	{echo "<tr><td onclick='desmarcar_siniestro_propio();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'>Des-marcar <b>Siniestro Asegurado</b></td></tr>";}
	if($D->flota==$V->flota)
	{echo "<tr><td colspan='2' width='400px' bgcolor='ffffff'><b>Observaciones de Estado:</b>",nl2br($D->observaciones)."<br>".nl2br($D->obs_mantenimiento)."</td></tr>";}
	if($D->estado==1 || $D->estado==7)
	{
		if($Sin=qo("select s.id,s.numero,a.nombre as anombre,a.emblema_f as aemb,s.observaciones,s.obsconclusion
								from siniestro s,aseguradora a where a.id=s.aseguradora  and s.ubicacion=$idub"))
		{
			echo "<tr><td colspan=2 style='cursor:pointer' onclick='abre_siniestro($Sin->id);' nowrap='yes'>
						<img src='gifs/standar/opcionazul.png' border='0'>Ver / Editar <b style='font-size:16px'>$Sin->numero</b><b> ".($D->flota==$V->flota?"<font color='blue'>$Sin->anombre</font>":"")."</b></td></tr>";
			if(inlist($USUARIO,'1,2'))
				echo "<tr><td onclick='ver_autorizaciones($Sin->id);' style='cursor:pointer'><img src='gifs/standar/opcionazul.png' border='0'> Ver Autorizaciones</td></td></tr>";
			if($FACS=q("select id,consecutivo,anulada from factura where siniestro=$Sin->id"))
			{
				while($FAC=mysql_fetch_object($FACS))
				{
					$Anulada=$FAC->anulada?"<b>Anulada</b>":"";
					echo "<tr><td colspan=2 style='cursor:pointer' onclick=\"modal('zfacturacion.php?Acc=imprimir_factura&id=$FAC->id',0,0,700,900,'eds');\" nowrap='yes'>
								<img src='gifs/standar/opcionazul.png' border='0'>Ver Factura $FAC->consecutivo $Anulada</td></tr>";
					if($RC=qo("select * from recibo_caja where factura=$FAC->id"))
					{
						echo "<tr><td colspan=2 style='cursor:pointer' onclick=\"modal('zcartera.php?Acc=imprimir_recibo&id=$RC->id',0,0,700,900,'eds');\" nowrap='yes'>
									<img src='gifs/standar/opcionazul.png' border='0'>Ver Recibo de caja $RC->consecutivo</td></tr>";
						if($RC->consignacion_numero && $RC->consignacion_f)
						{
							echo "<tr><td colspan=2 style='cursor:pointer' onclick=\"modal('$RC->consignacion_f',0,0,700,900,'eds');\" nowrap='yes'>
										<img src='gifs/standar/opcionazul.png' border='0'>Ver Consignación $RC->consignacion_numero $RC->consignacion_fecha</td></tr>";
						}
					}
				}
			}
			if(inlist($USUARIO,'1,2,7'))
				echo "<tr><td colspan=2 style='cursor:pointer' onclick='desliga_siniestro($Sin->id);' nowrap='yes'><img src='gifs/standar/Cancel.png' border='0'>Desligar el siniestro de este estado</td></tr>";
			echo "<tr><td colspan=2 bgcolor='eeeeee'><b>Observaciones:</b> ".nl2br($Sin->observaciones)."</td></tr>";
			echo "<tr><td colspan=2 bgcolor='eeeeee'><b>Obs. Conclusión:</b> ".nl2br($Sin->obsconclusion)."</td></tr>";
		}
		else
		{
			echo "<tr><td colspan=2 bgcolor='ffeeee'>Este servicio no tiene un siniestro asignado</td></tr>
					<tr><td colspan=2 nowrap='yes' bgcolor='eeeeee'><form action='zcontrol_operativo2.php' method='post' target='Oculto_tablero' name='forma1' id='forma1'>
						Asignar: <input type='text' name='numero' id='numero'><input type='button' value='Asignar' onclick=\"if(this.form.numero.value) {
						this.form.submit();} else {alert('No ha escrito un número de siniestro para asignar');return false;}\">
						<input type='hidden' name='Acc' value='asignar_siniestro'><input type='hidden' name='idub' value='$idub'></form>
					</td></tr>
					";
		}
		if($Cita=qo("select * from cita_servicio where placa='$V->placa' and fecha='$U->fecha_inicial' and estado='C' "))
		{
			echo "<tr><td colspan='2' bgcolor='eeeeee'><b>Obs Cita:</b> ".nl2br($Cita->observaciones)."</td></tr>
						<tr><td colspan='2' bgcolor='eeeeee'><b>Obs Devolución:</b> ".nl2br($Cita->obs_devolucion)."</td></tr>";
			if(inlist($USUARIO,'1,2,4,7,10,13'))
			{
				echo "<tr><td colspan='2' style='cursor:pointer' onclick='abre_cita($Cita->id);'><img src='gifs/standar/opcionazul.png' border='0'>Ver/Editar Cita</td></tr>";
			}

		}
	}
	if(inlist($USUARIO,'1,2,7'))
		echo "<tr><td onclick='abre_ubicacion();' style='cursor:pointer'><img src='gifs/standar/opcionazul.png' border='0'> Ver / Editar estado</td>";
	if(inlist($USUARIO,'1,2'))
		echo "<td onclick='borrar_ubicacion();' style='cursor:pointer'><img src='gifs/standar/Cancel.png' border='0'> Borrar este estado</td></tr>";

	if(inlist($USUARIO,'1'))
		echo "<tr><td onclick='ajustar_fecha_inicial();' style='cursor:pointer'><img src='gifs/standar/Down.png' border='0'> Ajustar fecha inicial</td>
				<td onclick='ajustar_fecha_final();' style='cursor:pointer'><img src='gifs/standar/Down.png' border='0'> Ajustar fecha final</td></tr>";
	if(inlist($USUARIO,'1,3,13'))
				echo "<tr><td onclick='cambiar_fecha_devolucion();' style=cursor:pointer'><img src='gifs/standar/opcionazul.png' border='0'>Fecha Devolución</td></tr> ";

	echo "<tr><td onclick='adicionar_observaciones();' style='cursor:pointer'><img src='gifs/standar/edita_registro.png' border='0'> Adicionar Observaciones</td></tr>";
	echo "</table>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+40;
			parent.mc_evento_texto();
			parent.Objeto.style.cursor='auto';
		</script></body></html>";
}

function borrar_ubicacion()
{
	global $id;
	q("delete from ubicacion where id=$id");
	graba_bitacora('ubicacion','D',$id,'Borra registro');
	echo "<body><script language='javascript'>
			parent.parent.document.getElementById('estatus').innerHTML=\"<img src='img/cargando1.gif' width='180px' border='0' align='middle'>\";
			parent.parent.recargar_datos();
	</script></body>";
}

function ajustar_fecha_inicial()
{
	global $id,$fecha;
	q("update ubicacion set fecha_inicial='$fecha' where id=$id");
	graba_bitacora('ubicacion','M',$id,"Ajusta fecha inicial a $fecha");
	echo "<body><script language='javascript'>
		parent.parent.document.getElementById('estatus').innerHTML=\"<img src='img/cargando1.gif' width='180px' border='0' align='middle'>\";
		parent.parent.recargar_datos();
	</script></body>";
}

function ajustar_fecha_final()
{
	global $id,$fecha;
	q("update ubicacion set fecha_final='$fecha' where id=$id");
	graba_bitacora('ubicacion','M',$id,"Ajusta fecha final a $fecha");
	echo "<body><script language='javascript'>
		parent.parent.document.getElementById('estatus').innerHTML=\"<img src='img/cargando1.gif' width='180px' border='0' align='middle'>\";
		parent.parent.recargar_datos();
	</script></body>";
}

function adicionar_evento()
{
	global $vehiculo,$fecha,$Nomueve,$USUARIO;
	$V=qo("select placa,flota_distinta from vehiculo where id=$vehiculo");
	$fini=date('Y-m-d');$ffin=date('Y-m-d');$oficina=0;$flota=0;$kmi=0;$kmf=0;
	$Fecha_Posterior='3000-01-01';
	if($Ultimo=qo("select * from ubicacion where vehiculo=$vehiculo and fecha_final<='$fecha' order by fecha_final desc,id desc limit 1"))
	{
		$fini=$Ultimo->fecha_final;if($fini>$ffin) $ffin=$fini; 	$oficina=$Ultimo->oficina;$flota=$Ultimo->flota;$kmi=$Ultimo->odometro_final;$kmf=$Ultimo->odometro_final;
	}
	if($Posterior=qo("select * from ubicacion where vehiculo=$vehiculo and fecha_inicial > '$fecha' order by fecha_inicial asc,id asc limit 1"))
	{
		$ffin=$Posterior->fecha_inicial;$kmf=$Posterior->odometro_inicial;
		$Fecha_Posterior=$ffin;
	}
	if($fecha>date('Y-m-d'))
	{
		$fini=$fecha;$ffin=$fecha;
	}
	$Diferencia=$kmf-$kmi;
	if($V->flota_distinta==0)
	{
		$flota=6;
	}
	html();
	echo "
		<script language='javascript'>
			function validar_nuevo_estado()
			{
				with(document.forma2)
				{
					if(FF.value<FI.value) {alert('La fecha final no puede ser menor que la fecha inicial'); return false;}
					if(FF.value>'$Fecha_Posterior') {alert('La fecha final no puede ser mayor que $Fecha_Posterior');return false;}
					if(OFI.value==0) {alert('Debe seleccionar una oficina válida'); return false;}
					if(FLOT.value==0) {alert('Debe seleccionar una flota de vehículos válida'); return false;}
					if(Number(KMI.value)==0) {alert('Debe escribir un kilometraje inicial válido'); return false;}
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMI.value)<$kmi) {alert('Debe escribir un kilometraje mayor o igual a $kmi'); return false;}
					";
	if($Posterior)
	{
		echo "if(Number(KMF.value)>$kmf) {alert('Debe escribir un kilometraje menor o igual a $kmf'); return false;}";
	}

	echo "if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(Estado.value==0) {alert('Debe seleccionar un estado válido'); return false;}
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Adicion de un evento</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td></tr><tr><td width='10%'>Fecha final:</td><td>".pinta_FC('forma2','FF',$ffin)."</td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>";
	if(inlist($USUARIO,'1'))
		echo menu1('OFI',"select id,nombre from oficina",$oficina,1);
	else
		echo qo1("select t_oficina($oficina)")."<input type='hidden' name='OFI' id='OFI' value='$oficina'>";

	echo "</td><td align='right'>Flota:</td><td>";
	if(inlist($USUARIO,'1'))
		echo menu1('FLOT',"select id,nombre from aseguradora",$flota,1);
	else
		echo qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'>";
	echo "</td></tr><tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>";
	if(inlist($USUARIO,'1'))
		echo menu1("Estado","Select id,nombre from estado_vehiculo",2,1);
	else
	{
		if($Ultimo->estado==1 )
		{
			echo menu1("Estado","Select id,nombre from estado_vehiculo where id in (92)",2,1);
		}
		else
		{
			if($USUARIO==10)
				echo menu1("Estado","Select id,nombre from estado_vehiculo where id in (2,4,5,8,94,96)",2,1);
			else
				echo menu1("Estado","Select id,nombre from estado_vehiculo where id in (2,4,5,6,8,92,93,94,96)",2,1);
		}
	}
	echo "</td><td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_nuevo_estado()'>
				<input type='hidden' name='Acc' id='Acc' value='adiciona_ubicacion_ok'><input type='hidden' name='Vehiculo' id='Vehiculo' value='$vehiculo'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function adiciona_ubicacion_ok()
{
	global $FI,$FF,$OFI,$FLOT,$KMI,$KMF,$Estado,$OBS,$Vehiculo,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$dif=$KMF-$KMI;
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$OFI','$Vehiculo','$Estado','$FLOT','$FI','$FF','$KMI','$KMF','$dif',\"$NUSUARIO [$Hoy] $OBS\") ");
	graba_bitacora('ubicacion','A',$IDU,'Adiciona Registro');
	echo "<script language='javascript'>
		function carga()
		{
			parent.parent.document.forma.submit();
		}
	</script>
	<body onload='carga()'></body>";
}

function cerrar_mantenimiento()
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub");
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo");
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html();
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(Number(KMF.value)==Number(KMI.value)) {alert('El cierre de Mantenimiento exige que se registre un kilometraje final que no puede ser igual al inicial');return false;}
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Mantenimiento</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cierre_mantenimiento_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cierre_mantenimiento_ok()
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub");
	$UB=qo("select * from ubicacion where id=$idub");
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') ");
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro");
	echo "<script language='javascript'>
		function carga()
		{
			parent.parent.document.forma.submit();
		}
	</script>
	<body onload='carga()'></body>";
}

function cerrar_servicio_especial()
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub");
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo");
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html();
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto al cierre del servicio especial'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Servicio Especial Gerencia</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_servicio_especial_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cerrar_servicio_especial_ok()
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub");
	$UB=qo("select * from ubicacion where id=$idub");
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') ");
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro");
	echo "<script language='javascript'>
		function carga()
		{
			parent.parent.document.forma.submit();
		}
	</script>
	<body onload='carga()'></body>";
}

function cerrar_fuera_servicio()
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub");
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo");
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html();
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Fuera de Servicio</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_fuera_servicio_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cerrar_fuera_servicio_ok()
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub");
	$UB=qo("select * from ubicacion where id=$idub");
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') ");
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro");
	echo "<script language='javascript'>
		function carga()
		{
			parent.parent.document.forma.submit();
		}
	</script>
	<body onload='carga()'></body>";
}

function cerrar_alistamiento()
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub");
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo");
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html();
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Alistamiento</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_alistamiento_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cerrar_alistamiento_ok()
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub");
	$UB=qo("select * from ubicacion where id=$idub");
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') ");
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro");
	echo "<script language='javascript'>
		function carga()
		{
			parent.parent.document.forma.submit();
		}
	</script>
	<body onload='carga()'></body>";
}

function cerrar_transito()
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub");
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo");
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html();
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Transito</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Oficina Destino:</td><td>".menu1("OFID","select id,nombre from oficina")."</td>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_transito_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cerrar_transito_ok()
{
	global $idub,$KMF,$OBS,$NUSUARIO,$OFID;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub");
	$UB=qo("select * from ubicacion where id=$idub");
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$OFID','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') ");
	q("update vehiculo set ultima_ubicacion=$UB->oficina where id=$UB->vehiculo");
	graba_bitacora('ubicacion','A',$IDU,"Aiciona Registro");
	echo "<script language='javascript'>
		function carga()
		{
			parent.parent.document.forma.submit();
		}
	</script>
	<body onload='carga()'></body>";
}

function activar_mantenimiento()
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub");
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo");
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html();
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Activación de Mantenimiento</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".menu1("EST","select id,nombre from estado_vehiculo where id=4",4,0)."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3>$UB->obs_mantenimiento</td></tr>
				<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='activar_mantenimiento_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function activar_mantenimiento_ok()
{
	global $idub,$EST,$OBS,$KMI,$KMF,$Diferencia,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set fecha_final='$Fecha' where id='$idub' ");
	$UB=qo("select * from ubicacion where id=$idub");
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','4','$UB->flota','$Fecha','$Fecha','$KMI','$KMF','$Diferencia',\"$UB->obs_mantenimiento \n$NUSUARIO [$Hoy] $OBS\") ");
	graba_bitacora('ubicacion','A',$IDU,'Adiciona Registro');
	echo "<script language='javascript'>
		function carga()
		{
			parent.parent.document.forma.submit();
		}
	</script>
	<body onload='carga()'></body>";
}

function adicionar_observaciones()
{
	global $id,$Emb_Aseg,$Tem_Placas;
	$U=qo("select * from ubicacion where id=$id");
	$UT=qo("select * from ubicacion where id=$id");
	$V=qo("select * from $Tem_Placas where id=$U->vehiculo");
	$O=qo("select * from oficina where id=$U->oficina");
	$Nplaca=substr($V->placa,0,3).'-'.substr($V->placa,3,3);
	html();
	echo "
		<script language='javascript'>
			function valida_nuevas_observaciones()
			{
				if(!alltrim(document.forma3.Obs.value))
				{ alert('Debe escribir algo en el campo de observaciones para ser grabado'); return false;}
				document.forma3.submit();
			}
		</script>
	<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma3' id='forma3'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='300px'><tr><td>
			<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
			<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();'>
			<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b></td>
			<td align='center' valign='middle' rowspan=3 bgcolor='eeeeee'><img src='".$Emb_Aseg[$U->flota]."' border='0' align='middle' height='30px'> <img src='$V->emb1' border='0' align='middle' height='30px'></td></tr>
			<tr><td align='center' colspan='1' bgcolor='eeeeee' class='placa'  style='background-image:url(img/placa.jpg);'>$Nplaca</td></tr>
			<tr><td nowrap='yes' bgcolor='eeeeee'>Estado: <b>$UT->nestado</b></td></tr>
			<tr><td nowrap='yes' bgcolor='eeeeee' colspan=2>Fechas Evento: <b>$UT->fecha_inicial - $UT->fecha_final</b></td></tr>";
	if($U->obs_mantenimiento || $U->observaciones)
		echo "<tr><td colspan=2><b>Observaciones anteriores:</b> ".nl2br($U->observaciones).'<br />'.nl2br($U->obs_mantenimiento)."</td></tr>";
	echo "
			<tr><td colspan=2>Observaciones nuevas:<br /><textarea name='Obs' style='font-size:14' cols=50 rows=3></textarea></td></tr>
			<tr><td colspan=2 align='center'><input type='button' value='Grabar' onclick='valida_nuevas_observaciones()'></td></tr>
		</table>
		</TD></TR></TABLE>
		<input type='hidden' name='Acc' id='Acc' value='adicionar_observaciones_ok'><input type='hidden' name='id' id='id' value='$id'>
		</form>
	<script language='javascript'>
		parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
		parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
		parent.mc_evento_texto(1);
		document.forma3.Obs.focus();
	</script></body></html>";
}

function adicionar_observaciones_ok()
{
	global $id,$Obs,$NUSUARIO;
	$Hoy=date('Y-m-d H:i');
	q("update ubicacion set obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $Obs\") where id=$id");
	if($Sin=qo1("select id from siniestro where ubicacion=$id"))
	{
		q("update siniestro set obsconclusion=concat(obsconclusion,\"\n$NUSUARIO [$Hoy] $Obs\") where id=$Sin");
		if($Cita=qo1("select id from cita_servicio where siniestro=$Sin"))
		{
			q("update cita_servicio set obs_devolucion=concat(obs_devolucion,\"\n$NUSUARIO [$Hoy] $Obs\") where id=$Cita");
		}
	}
	graba_bitacora('ubicacion','M',$id,'Modifica Registro: Observaciones');
	echo "<script language='javascript'>
		function carga()
		{
			parent.oculta_menu_celda();
		}
	</script>
	<body onload='carga()'></body>";
}

function desligar_siniestro()
{
	global $ids;
	q("update siniestro set ubicacion=0 where id=$ids");
	graba_bitacora('siniestro','M',$ids,"Desliga Siniestro");
	echo "<script language='javascript'>
		function carga()
		{
			parent.parent.document.forma.submit();
		}
	</script>
	<body onload='carga()'></body>";
}

function asignar_siniestro()
{
	global $numero,$idub;
	if($Sin=q("select id from siniestro where numero like '%$numero%'  "))
	{
		if(mysql_num_rows($Sin)==1)
		{
			$Sin=mysql_fetch_object($Sin);
			$D=qo("select * from ubicacion where id=$idub");
			q("update siniestro set ubicacion=$idub,fecha_inicial='$D->fecha_inicial',fecha_final='$D->fecha_final' where id=$Sin->id");
			graba_bitacora('siniestro','M',$Sin->id,"Asigna el siniestro $Sin->id la ubicación $idub $D->fecha_inicial - $D->fecha_final");
			echo "<script language='javascript'>
			function carga()
			{
				alert('Siniestro asignado');
				parent.parent.document.forma.submit();
			}
		</script>
		<body onload='carga()'></body>";
		}
		else
		{
			echo "<script language='javascript'>
				function carga()
				{
					window.open('zcontrol_operativo2.php?Acc=asignar_siniestro_selecciona&numero=$numero&idub=$idub','Menu_contextual_celda');
				}
			</script>
			<body onload='carga()'></body>";
		}
	}
	else
	{
		echo "<script language='javascript'>
			function carga()
			{
				parent.oculta_menu_celda();
				alert('No se encuentra un siniestro con el numero dado');
			}
		</script>
		<body onload='carga()'></body>";
	}
}

function asignar_siniestro_selecciona()
{
	global $numero,$idub;
	html();
	echo "<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0' bgcolor='eeffee'>
			<table border='0' cellspacing='1' cellpadding='0' bgcolor='#bbbbbb' name='Context_Celda' id='Context_Celda' align='center' width='400px'>
			<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();'><img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar menu</b></td>
			<tr><td bgcolor='eeffee'>
			Por favor seleccione uno de los siguientes siniestros para asignar:<br />
			<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma' id='forma'>".
			menu1("SIN","select id,concat(numero,' ',fec_autorizacion,' ',asegurado_nombre,' [',t_estado_siniestro(estado),']') from siniestro where numero like '%$numero%' order by numero");
	echo "
			<input type='hidden' name='Acc' id='Acc' value='asignar_siniestro_seleccionado'>
			<input type='hidden' name='idub' id='idub' value='$idub'>
			<input type='submit' value='Asignar' >
			</form>
			</td></tr></table>
			<script language='javascript'>
				parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
				parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
				parent.mc_evento_texto();
			</script></body>";
}

function asignar_siniestro_seleccionado()
{
	global $SIN,$idub;
	$D=qo("select * from ubicacion where id=$idub");
	q("update siniestro set ubicacion=$idub,fecha_inicial='$D->fecha_inicial', fecha_final='$D->fecha_final' where id=$SIN");
	graba_bitacora('siniestro','M',$SIN,"Asigna siniestro $SIN a la ubicación $idub $D->fecha_inicial - $D->fecha_final");
	echo "<script language='javascript'>
				function carga()
				{
					alert('Asignación Satisfactoria');
					parent.document.forma.submit();
				}
			</script>
			<body onload='carga()'></body>";
}

function historia_fotografica()
{
	global $idPlaca,$Tem_Placas,$pagina;
	$V=qo("Select * from vehiculo where id=$idPlaca");
	if(!$pagina) $pagina=1;

	html("HISTORIA PLACA $V->placa");
	// BUSCA LA HISTORIA FOTOGRAFICA DEL VEHICULO EXCLUYENDO LA FLOTA SIN LOGO.
	if($U=qo("select *,t_estado_vehiculo(estado) as nestado,t_aseguradora(flota) as nflota,t_oficina(oficina) as noficina from ubicacion where vehiculo='$idPlaca' and estado in (1,7)
		 order by odometro_inicial desc, fecha_inicial desc limit ".($pagina-1).",1"))
	{
		echo "<body ><script language='javascript'>centrar();</script>
		<i style='font-size:14'>La historia fotográfica de los vehiculos empieza en marzo de 2010. Los siniestros que correspondan a fechas anteriores, no tienen el registro fotográfico. <br><br>
		Las imágenes de los vehículos se muestran siniestro por siniestro empezando por el mas reciente hacia atras. Para ver cada siniestro, se da click en <b>SINIESTRO ANTERIOR</b></I><BR><BR>
		<h3><b>HISTORIA FOTOGRAFICA DEL VEHICULO $V->placa</b></h3>
		<a href='zcontrol_operativo2.php?Acc=historia_fotografica&idPlaca=$idPlaca&pagina=".($pagina+1)."' target='_self'>SINIESTRO ANTERIOR</a> &nbsp;&nbsp;&nbsp;&nbsp;";
		if($pagina>1) echo "<a href='zcontrol_operativo2.php?Acc=historia_fotografica&idPlaca=$idPlaca&pagina=".($pagina-1)."' target='_self'>SINIESTRO SIGUIENTE</a>";
		if($Sin=qo("select * from siniestro where ubicacion=$U->id"))
		{
			$Aseguradora=qo1("select t_aseguradora($Sin->aseguradora)");
			echo "<h3><i>Oficina:</i> $U->noficina <i>Estado:</i> $U->nestado <i>Aseguradora:</i> $Aseguradora <i>Siniestro:</i> $Sin->numero
							<i>Fecha:</i> $U->fecha_inicial - $U->fecha_final
							<i>Odometros:</i> $U->odometro_inicial - $U->odometro_final</h3>
							<font color='GREEN' style='font-size:26;font-weight:bold'>IMAGENES DE ENTREGA</FONT><BR>";
			echo ($Sin->img_inv_salida_f?"<img src='$Sin->img_inv_salida_f'><br /><br />":"");
			echo ($Sin->fotovh1_f?"<img src='$Sin->fotovh1_f'><br /><br />":"");
			echo ($Sin->fotovh2_f?"<img src='$Sin->fotovh2_f'><br /><br />":"");
			echo ($Sin->fotovh3_f?"<img src='$Sin->fotovh3_f'><br /><br />":"");
			echo ($Sin->fotovh4_f?"<img src='$Sin->fotovh4_f'><br /><br />":"");
			if($Sin->img_inv_entrada_f)
			{
				echo "<font color='BLUE' style='font-size:26;font-weight:bold'>IMAGENES DE DEVOLUCION</font><br>";
				echo ($Sin->img_inv_entrada_f?"<img src='$Sin->img_inv_entrada_f'><br /><br />":"");
				echo ($Sin->fotovh5_f?"<img src='$Sin->fotovh5_f'><br /><br />":"");
				echo ($Sin->fotovh6_f?"<img src='$Sin->fotovh6_f'><br /><br />":"");
				echo ($Sin->fotovh7_f?"<img src='$Sin->fotovh7_f'><br /><br />":"");
				echo ($Sin->fotovh8_f?"<img src='$Sin->fotovh8_f'><br /><br />":"");
				echo ($Sin->fotovh9_f?"<img src='$Sin->fotovh9_f' >":"");
			}
		}
		else
		{
			echo "No se encuentra la información correspondiente al siniestro.";
		}
		echo "<hr><a href='zcontrol_operativo2.php?Acc=historia_fotografica&idPlaca=$idPlaca&pagina=".($pagina+1)."' target='_self'>SINIESTRO ANTERIOR</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		if($pagina>1) echo "<a href='zcontrol_operativo2.php?Acc=historia_fotografica&idPlaca=$idPlaca&pagina=".($pagina-1)."' target='_self'>SINIESTRO SIGUIENTE</a>";
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
	<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma' id='forma'>
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
	$NID=q("insert into hv_vehiculo (placa,novedad,kilometraje,fecha) values ('$placa','$novedad','$kilometraje','$fecha') ");
	graba_bitacora('hv_vehiculo','A',$NID,'Adiciona Registro');
	echo "<script language='javascript'>
		function carga()
		{
			window.close();
			void(null);
			opener.location.reload();
		}
	</script>
	<body onload='carga()'></body>";
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
	<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma' id='forma'>
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

function cambiar_a_fueraservicio()
{
	global $id;
	q("update ubicacion set estado=5 where id=$id");
	graba_bitacora('ubicacion','M',$id,"Cambia de mantenimiento a fuera de servicio ");
	echo "<script language='javascript'>
				function carga()
				{
					parent.parent.document.forma.submit();
				}
			</script>
			<body onload='carga()'></body>";
}

function cambiar_a_mantenimiento()
{
	global $id;
	q("update ubicacion set estado=4 where id=$id");
	graba_bitacora('ubicacion','M',$id,"Cambia de fuera de servicio a mantenimiento");
	echo "<script language='javascript'>
				function carga()
				{
					parent.parent.document.forma.submit();
				}
			</script>
			<body onload='carga()'></body>";
}

function marcar_siniestro_propio()
{
	global $id;
	q("update ubicacion set siniestro_propio=1 where id=$id");
	graba_bitacora('ubicacion','M',$id,"Marca Siniestro Asegurado");
	echo "<script language='javascript'>
				function carga()
				{
					parent.parent.document.forma.submit();
				}
			</script>
			<body onload='carga()'></body>";
}

function desmarcar_siniestro_propio()
{
	global $id;
	q("update ubicacion set siniestro_propio=0 where id=$id");
	graba_bitacora('ubicacion','M',$id,"Des-marca Siniestro Asegurado");
	echo "<script language='javascript'>
				function carga()
				{
					parent.parent.document.forma.submit();
				}
			</script>
			<body onload='carga()'></body>";
}

function cambio_temporal()
{
	global $id,$NUSUARIO;
	$Actual=qo("select * from ubicacion where id=$id");
	html('Cambio Temporal de Flota');
	echo "<body>
		<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma' id='forma'>
			<h3>Solicitud de cambio temporal de flota</h3>
			Usuario: <input type='text' name='usuario' value='$NUSUARIO' readonly size=50><br>
			Seleccione la flota: ".menu1("flota","Select id,nombre from aseguradora where id!=6 and id!=$Actual->flota ",0,1,"",
				" onchange=\"this.form.generar.style.visibility='visible';\" ")."<br>
			<br><input type='submit' name='generar' id='generar' value='GENERAR SOLICITUD' style='visibility:hidden'>
			<input type='hidden' name='Acc' value='cambio_temporal_ok'>
			<input type='hidden' name='id' value='$id'>
		</form></body>";
}

function cambio_temporal_ok()
{
	global $usuario,$id,$flota;
	$Hoy=date('Y-m-d H:i:s');
	$Email_usuario=usuario('email');
	$Ruta_arturo="utilidades/Operativo/operativo.php?Acc=autorizar_cambio_temporal&id=$id&Fecha=$Hoy&Usuario=ARTURO QUINTERO RODRIGUEZ&solicitadopor=$usuario&flota=$flota";
	$Ruta_gabriel="utilidades/Operativo/operativo.php?Acc=autorizar_cambio_temporal&id=$id&Fecha=$Hoy&Usuario=GABRIEL SANDOVAL PAVAJEAU&solicitadopor=$usuario&flota=$flota";

	$UB=qo("select * from ubicacion where id=$id");
	$V=qo("select placa,t_aseguradora(flota) as nflota from vehiculo where id=$UB->vehiculo");
	$A=qo1("select t_aseguradora($flota)");

	$Mensaje="<body><b>SOLICITUD DE CAMBIO TEMPORAL DE FLOTA</B><BR><BR>Vehiculo: $V->placa Flota principal: $V->nflota<br>".
			"Solicita cambio de flota a: $A<br>Funcionario que solicita: $usuario Fecha de solicitud: $Hoy <br><br>";
	$Mensaje.="Para AUTORIZAR el cambio temporal haga click aquí: <a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='$Ruta_arturo';\$Fecha_control='".date('Y-m-d')."';")."' target='_blank'>AUTORIZAR</a></body>";
	$Envio1=enviar_gmail($Email_usuario /*de */,
				$NUSUARIO /*Nombre de */ ,
				"arturoquintero@aoacolombia.com,Arturo Quintero" /*para */,
				"" /*con copia*/,
				"SOLICITUD CAMBIO TEMPORAL FLOTA $V->placa" /*Objeto */,
				$Mensaje);

	$Mensaje="<body><b>SOLICITUD DE CAMBIO TEMPORAL DE FLOTA</B><BR><BR>Vehiculo: $V->placa Flota principal: $V->nflota<br>".
			"Solicita cambio de flota a: $A<br>Funcionario que solicita: $usuario Fecha de solicitud: $Hoy <br><br>";
	$Mensaje.="Para AUTORIZAR el cambio temporal haga click aquí: <a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='$Ruta_gabriel';\$Fecha_control='".date('Y-m-d')."';")."' target='_blank'>AUTORIZAR</a></body>";
	$Envio2=enviar_gmail($Email_usuario /*de */,
				$NUSUARIO /*Nombre de */ ,
				"gabrielsandoval@aoacolombia.com,Gabriel Sandoval" /*para */,
				"" /*con copia*/,
				"SOLICITUD CAMBIO TEMPORAL FLOTA $V->placa" /*Objeto */,
				$Mensaje);

	if($Envio1 && $Envio2)
	{
		echo "<script language='javascript'>alert('Envio de solicitud satisfactorio');window.close();void(null);</script>";
	}
	else
	{
		echo "<script language='javascript'>alert('El envio de solicitud fallo. Intente nuevamente.');window.close();void(null);</script>";
	}
}

function retorno_flota()
{
	global $id;
	$Ub=qo("select vehiculo from ubicacion where id=$id");
	$Vh=qo("select flota from vehiculo where id=$Ub->vehiculo");
	q("update ubicacion set flota=$Vh->flota where id=$id");
	echo "<script language='javascript'>alert('Cambio realizado satisfactoriamente');window.close();void(null);</script>";
}

function cambiar_fecha_devolucion()
{
	global $id;
	$D=qo("select * from ubicacion where id=$id");
	html('Cambio Fecha de Devolución');
	echo "<script language='javascript'>
			function carga()
			{
				centrar(700,500);
			}
		</script>
		<body onload='carga()'>
		<form action='zcontrol_operativo2.php' method='post' target='_self' name='forma' id='forma'>
			Fecha de devolución: ".pinta_FC('forma','FD',$D->fecha_final)."
			<br><br><input type='submit' value='Continuar'>
			<input type='hidden' name='Acc' value='cambiar_fecha_devolucion_ok'>
			<input type='hidden' name='id' value='$id'>
		</form>
		</body>";
}

function cambiar_fecha_devolucion_ok()
{
	global $id,$FD;
	$D=qo("select * from ubicacion where id=$id");
	$V=qo("select placa from vehiculo where id=$D->vehiculo");
	q("update ubicacion set fecha_final='$FD' where id=$id");
	html('Cambio Fecha de Devolución');
	echo "<body><script language='javascript'>centrar(400,300);</script><br>Cambio de fecha de la ubicación satisfactorio a $FD";
	if($Cita=qo("select id from cita_servicio where placa='$V->placa' and fecha='$D->fecha_inicial' and estado='C' "))
	{
		q("update cita_servicio set fec_devolucion='$FD' where id=$Cita->id ");
		echo "<br>Cambio de fecha de devolución en la Cita satisfactorio";
	}
	if($Al=qo1("select id from ubicacion where vehiculo=$D->vehiculo and estado=8 and fecha_inicial='$D->fecha_final'  "))
	{
		q("update ubicacion set fecha_inicial='$FD',fecha_final='$FD' where id=$Al");
		echo "<br>Cambio de la fecha inicial y final del alistamiento satisfactorio";
	}
	if($P=qo1("select id from ubicacion where vehiculo=$D->vehiculo and estado=2 and fecha_inicial>='$D->fecha_final'  "))
	{
		q("update ubicacion set fecha_inicial='$FD',fecha_final='$FD' where id=$P");
		echo "<br>Cambio de la fecha inicial y final del parqueadero satisfactorio";
	}
}

function muestra_diario_parqueadero()
{
	global $D,$F;
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=parqueaderos_$F.xls");
	echo "<table border cellspacing='0'><tr><th>Parqueaderos / Alistamientos fecha: $F</th></tr><tr><td>".str_replace(',',"</td></tr><tr><td>",$D)."</td></tr></table>";
}

























?>