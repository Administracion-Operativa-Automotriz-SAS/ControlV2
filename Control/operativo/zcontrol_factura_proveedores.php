<?php

/**
 *  Sistema de control de facturación de proveedores
 *
 *  Diseñado para controlar la facturación de los talleres, por mantenimientos, arreglos, lavadas, despinchadas, tanqueadas, etc.
 *
 * El objetivo es controlar y documentar en el sistema cada uno de los cobros y obtener los que hagan falta por facturar por parte de los proveedores.
 *
 * @version $Id$
 * @copyright 2011
 */
include('inc/funciones_.php');
sesion();
$USUARIO=$_SESSION['User'];
$NUSUARIO=$_SESSION['Nombre'];

if(!empty($Acc) && function_exists($Acc)) { eval($Acc.'();'); die(); }

iniciar_control();

function iniciar_control()
{

	html('CONTROL FACTURACION PROVEEDORES');
	echo "<script language='javascript'>
		function consultar()
		{document.forma.submit();}
	</script><body><h3>CONTROL DE FACTURACION DE PROVEEDORES</H3>
	<form action='zcontrol_factura_proveedores.php' method='post' target='Consulta_control' name='forma' id='forma'>
		Fecha Inicial: ".pinta_FC('forma','FI',primer_dia_de_mes(date('Y-m-d')))." Fecha Final: ".pinta_FC('forma','FF',date('Y-m-d'))."
		<input type='button' value='Consultar' onclick='consultar();'><input type='hidden' name='Acc' value='consultar_ubicaciones'>
	</form>
	<iframe name='Consulta_control' id='Consulta_control' height='80%' width='98%' frameborder='no'></iframe>
	 ";
}

function consultar_ubicaciones()
{
	global $FI,$FF;
	html();
	echo "<script language='javascript'>
			function fija_ancho(dato,descuento)
			{document.getElementById(dato+'_').width=document.getElementById(dato).clientWidth-descuento;}

		function valida_scroll()
			{ document.getElementById('_capa_titulo_superior_').style.left=-document.body.scrollLeft+8;
				var Altura=document.getElementById('_Tabla_').offsetTop;
				var Avance=document.body.scrollTop;
				if(Altura-Avance>0)	document.getElementById('_capa_titulo_superior_').style.top=Altura-Avance; else document.getElementById('_capa_titulo_superior_').style.top=0;
				if(document.getElementById('_capa_titulo_lateral_')) document.getElementById('_capa_titulo_lateral_').style.top=-document.body.scrollTop+Altura;}
		</script>
		<body >";
	if($Ubicaciones=q("select u.*,t_oficina(oficina) as noficina,t_estado_vehiculo(estado) as nestado from ubicacion as u
									where u.fecha_final >='$FI' and u.fecha_inicial<='$FF' and u.estado in (4,5,8)
									order by noficina,u.fecha_inicial,u.fecha_final "))  // 4:mantenimiento  5:fuera de servicio 8:alistamiento
	{
		$A_titulos=array("oficina"=>"Oficina","estado"=>"Estado","tiempo"=>"Tiempo","observaciones"=>"Observaciones");
		fija_titulo_superior($A_titulos,"cellspacing=4",1);
		fija_titulo_superior($A_titulos,"cellspacing=4",2);
		$BGC='ffffff';
		while($U=mysql_fetch_object($Ubicaciones))
		{
			echo "<tr bgcolor='$BGC'><td >$U->noficina</td><td >$U->nestado</td><td >$U->fecha_inicial - $U->fecha_final</td><td width='400px'>$U->observaciones<hr color='efefef'>$U->obs_mantenimiento</td></tr>";
			$BGC=$BGC=='ffffff'?'eeeeee':'ffffff';
		}
		echo "</table>";
		fija_titulo_superior($A_titulos,"",3);
	}
}
?>