<?php
// PROGRAMA PARA OBTENER ESTADISTICAS DE LOS BALANCES DE ESTADO
// Esta aplicación se diseña de tal forma que se corre desde el ambiente Administrativo y trae información a modo de consulta del ambiente de Control.
include('inc/funciones_.php');
error_reporting(E_ALL);
sesion();
$BDC='aoacol_aoacars';
$A_oficina=$A_aseguradora=$A_vehiculo=$A_estado_vehiculo=array();

if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');	die();}
inicio_control();

function inicio_control()
{
	global $MI,$MF,$BDC;
	html();
	echo "<script language='javascript'>
	function mantiene_altura_tablero(){document.getElementById('Tablero_cbe').height=document.body.clientHeight-60;}
	function recargar(){document.forma.submit();}
	</script><body onresize='mantiene_altura_tablero();'>
	<form action='zcontrol_balance_estado.php' target='Tablero_cbe' method='POST' name='forma' id='forma'>
		<input type='hidden' name='Acc' value='ver_control'>
		Desde: <select name='MI'>".pinta_periodo(date('Ym'))."</select> Hasta: <select name='MF'>".pinta_periodo(date('Ym'))."</select>
		Estado: ".menu1("EST","select id,nombre from $BDC.estado_vehiculo",0,1)." Aseguradora: ".
		menu1("ASE","Select id,nombre from $BDC.aseguradora",0,1)."
		<input type='submit' name='continuar' id='continuar' value=' CONSULTAR '>
	</form>
	<iframe name='Tablero_cbe' id='Tablero_cbe' style='visibility:visible' width='100%' height='80%'></iframe>";
	
	echo "<script language='javascript'>
	mantiene_altura_tablero();
	</script></body>";
}

Class Vehiculo_balance
{
	var $idvehiculo=0; // id del vehiculo
	var $idarbol=''; // ubicacion dentro del arbol, contiene el periodo, la oficina, la aseguradora, para concatenar con el vehiculo y hacerlo independiente.
	var $idpadre=''; // identificacion del arbol padre.
	var $Placa=''; // placa del vehiculo
	var $Ubicaciones=array();  // Ubicaciones del vehiculo donde hay daños en el mes
	var $Valor=0; // sumatoria de todas las facturas detodas las ubicaciones de este vehiculo
	var $contadorUbicacion=0;
	
	function Vehiculo_balance($Ubicacion_vehiculo,$Placa,$Nas,$LINK)
	{
		$this->idvehiculo=$Ubicacion_vehiculo->idvehiculo;
		$this->idpadre=$Nas;
		$this->Placa=$Placa;
		$this->idarbol=$Nas.'_'.$this->Placa;
		$this->adiciona_ubicacion($Ubicacion_vehiculo,$LINK);
	}
	
	function adiciona_ubicacion($Ubicacion,$LINK)
	{
		$this->Ubicaciones[$this->contadorUbicacion]=new Ubicacion_Vehiculo($Ubicacion,$this->idarbol,$LINK);
		$this->Valor+=$this->Ubicaciones[$this->contadorUbicacion]->Valor+$this->Ubicaciones[$this->contadorUbicacion]->Valor_cm;
		$this->contadorUbicacion++;
	}
	
	function pinta($contador)
	{
		echo "<tr><td align='center' valign='top'>$contador</td><td valign='top' nowrap='yes'>
			<img id='img_$this->idarbol' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('$this->idarbol');\">
			<a onclick=\"aparece('$this->idarbol','img_$this->idarbol');\">$this->Placa</a>
			<script language='javascript'>
				if(!Hijos['$this->idpadre']) Hijos['$this->idpadre']=new Array();
				Hijos['$this->idpadre'][Hijos['$this->idpadre'].length]='$this->idarbol';
			</script>
			</td><td align='right' valign='top'>".coma_format($this->Valor)."</td><td>
				<table id='$this->idarbol' cellspacing='0' style='visibility:hidden;position:absolute;'>
					<tr><th>#</th><th>Estado</th><th>Valor</th></tr>";
			for($i=0;$i<count($this->Ubicaciones);$i++)
			{
				$this->Ubicaciones[$i]->pinta($i+1);
			}
		echo "</table></td></tr>";
	}
}

Class Ubicacion_Vehiculo
{
	var $idubicacion=0; // id de la ubicacion
	var $idpadre=''; // id del arbol padre
	var $Fecha_inicial=''; // fecha inicial del estado
	var $Fecha_final=''; // fecha final del estado
	var $idEstado=0; // id del estado de la ubicacion
	var $nEstado=''; // nombre del estado
	var $Km_inicial=0; // kilometraje inicial del estado
	var $Km_final=0; // kilometraje final del estado
	var $observaciones='';
	var $obs_mantenimiento='';
	var $Facturas=array(); // Facturas correspondientes a una misma ubicacion
	var $Caja_menor=array(); // Registros asociados de caja menor
	var $contadorFactura=0; // contador de facturas
	var $contadorCaja=0; // contador de cajas menores
	var $Valor=0; // sumatoria de las facturas de esta ubicacion
	var $Valor_cm=0; // sumatoria de las cajas menores
	
	function Ubicacion_Vehiculo($Ubicacion,$idpadre,$LINK)
	{
		global $A_estado_vehiculo;
		$this->idubicacion=$Ubicacion->idubicacion;
		$this->idpadre=$idpadre;
		$this->Fecha_inicial=$Ubicacion->fecha_inicial;
		$this->Fecha_final=$Ubicacion->fecha_final;
		$this->idEstado=$Ubicacion->estado;
		$this->nEstado=$A_estado_vehiculo[$this->idEstado];
		$this->Km_inicial=$Ubicacion->odometro_inicial;
		$this->Km_final=$Ubicacion->odometro_final;
		$this->observaciones=$Ubicacion->observaciones;
		$this->obs_mantenimiento=$Ubicacion->obs_mantenimiento;
		$this->obtener_facturas($LINK);
		$this->obtener_caja_menor($LINK);
	}
	
	function obtener_facturas($LINK)
	{
		$QFacturas=mysql_query("select distinct f.id,f.valor_a_pagar,f.numero,f.fecha_emision 
											FROM fac_detalle d,factura f
											WHERE f.id=d.factura and d.ubicacion=$this->idubicacion 
											order by f.id");
		while($Fa=mysql_fetch_object($QFacturas))
		{
			$this->Facturas[$this->contadorFactura]=new Factura_ubicacion($Fa,$this->idubicacion,$LINK);
			$this->Valor+=$Fa->valor_a_pagar;
			$this->contadorFactura++;
		}
	}
	
	function obtener_caja_menor($LINK)
	{
		$QCaja=mysql_query("select d.*,c.consecutivo from caja_menord d,caja_menor c where 
			d.caja=c.id and d.ubicacion=$this->idubicacion order by d.fecha");
		while($Ca=mysql_fetch_object($QCaja))
		{
			$this->Caja_menor[$this->contadorCaja]=new Caja_menor_ubicacion($Ca,$this->idubicacion,$LINK);
			$this->Valor_cm+=$Ca->valor;
			$this->contadorCaja++;
		}
	}
	
	function pinta($contador)
	{
		echo "<tr><td valign='top' align='center'>$contador</td><td valign='top' nowrap='yes'>";
		if(count($this->Facturas) || count($this->Caja_menor)) echo "<img id='img_$this->idubicacion' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('$this->idubicacion');\">
			<a class='info' onclick=\"aparece('$this->idubicacion','img_$this->idubicacion');\">";
		else echo "<a class='info'>";
		$Fec_inicial_tc=date('Y-m-d',strtotime(aumentadias($this->Fecha_inicial,-5)));
		$Fec_final_tc=date('Y-m-d',strtotime(aumentadias($this->Fecha_final,5)));
		echo "$this->nEstado<span>
				<table bgcolor='cccccc' width='400px'>
					<tr><td bgcolor='ffffff'>Tiempo:</td><td bgcolor='ffffff' nowrap='yes'>$this->Fecha_inicial - $this->Fecha_final</td></tr>
					<tr><td bgcolor='ffffff'>Kilometraje:</td><td bgcolor='ffffff' nowrap='yes'>".coma_format($this->Km_inicial)." - ".coma_format($this->Km_final)."</td></tr>
					<tr><td bgcolor='ffffff'>Estado:</td><td bgcolor='ffffff' nowrap='yes'>$this->nEstado</td></tr>
					<tr><td bgcolor='ffffff' valign='top'>Observaciones:</td><td bgcolor='ffffff'>".nl2br($this->observaciones)."</td></tr>
					<tr><td bgcolor='ffffff' valign='top'>Observaciones:</td><td bgcolor='ffffff'>".nl2br($this->obs_mantenimiento)."</td></tr>
				</table>
				</span></a>
			
			<a class='info' onclick='ver_balance($this->idubicacion);'><img src='gifs/standar/folder1.png' border='0' align='top'><span style='width:100px'>Ver Balance de Estado</span></a>
			<a class='info' onclick=\"ver_tabla_control('$this->idpadre','$Fec_inicial_tc','$Fec_final_tc');\"><img src='gifs/standar/folder2.png' border='0' align='top'><span style='width:100px'>Ver Tabla de Control</span></a>
			<a class='info' onclick='cambiar_estado($this->idubicacion);'><img id='img_cambio_$this->idubicacion' src='gifs/standar/cambiar.png' border='0' align='top'><span style='width:100px'>Cambiar de estado</span></a>
			
			<script language='javascript'>
				if(!Hijos['$this->idpadre']) Hijos['$this->idpadre']=new Array();
				Hijos['$this->idpadre'][Hijos['$this->idpadre'].length]='$this->idubicacion';
			</script>
			</td><td align='right' valign='top'>".coma_format($this->Valor+$this->Valor_cm)."</td>";
		if(count($this->Facturas) || count($this->Caja_menor))
		{
			echo "<td><table id='$this->idubicacion' cellspacing='0' style='visibility:hidden;position:absolute;'>";
			if(count($this->Facturas)) 
			{
				echo "<tr><th>#</th><th>Fac.Proveedor</th><th>Fecha</th><th>Valor</th></tr>";
				for($i=0;$i<count($this->Facturas);$i++)
				{
					$this->Facturas[$i]->pinta($i+1);
				}
			}
			if(count($this->Caja_menor)) 
			{
				echo "<tr><th>#</th><th>Caja Menor</th><th>Fecha</th><th>Valor</th></tr>";
				for($i=0;$i<count($this->Caja_menor);$i++)
				{
					$this->Caja_menor[$i]->pinta($i+1);
				}
			}
			echo "</table></td>";
		}
		echo "</tr>";
	}
}

Class Factura_ubicacion
{
	var $idfactura=0; // id de la factura
	var $idpadre=''; // id del arbol padre
	var $Consecutivo=0; //consecutivo de la factura
	var $Fecha=''; // Fecha de la factura
	var $Valor=0; // Valor a pagar despues de impuestos
	
	function Factura_ubicacion($Factura,$idpadre,$LINK)
	{
		$this->idfactura=$Factura->id;
		$this->idpadre=$idpadre;
		$this->Consecutivo=$Factura->numero;
		$this->Fecha=$Factura->fecha_emision;
		$this->Valor=$Factura->valor_a_pagar;
	}
	
	function pinta($contador)
	{
		echo "<tr><td>$contador</td><td>$this->Consecutivo
			<script language='javascript'>
				if(!Hijos['$this->idpadre']) Hijos['$this->idpadre']=new Array();
				Hijos['$this->idpadre'][Hijos['$this->idpadre'].length]='$this->idfactura';
			</script>
			</td>
			<td>$this->Fecha</td><td align='right'>".coma_format($this->Valor)."</td></tr>";
	}
}

Class Caja_menor_ubicacion
{
	var $idcaja=0; // id de la factura
	var $idpadre=''; // id del arbol padre
	var $Consecutivo=0; //consecutivo de la caja menor
	var $Fecha=''; // Fecha de la caja menor
	var $Valor=0; // Valor a pagar despues de impuestos
	
	function Caja_menor_ubicacion($Caja,$idpadre,$LINK)
	{
		$this->idfactura=$Caja->id;
		$this->idpadre=$idpadre;
		$this->Consecutivo=$Caja->consecutivo;
		$this->Fecha=$Caja->fecha;
		$this->Valor=$Caja->valor;
	}
	
	function pinta($contador)
	{
		echo "<tr><td>$contador</td><td>$this->Consecutivo
			<script language='javascript'>
				if(!Hijos['$this->idpadre']) Hijos['$this->idpadre']=new Array();
				Hijos['$this->idpadre'][Hijos['$this->idpadre'].length]='$this->idfactura';
			</script>
			</td>
			<td>$this->Fecha</td><td align='right'>".coma_format($this->Valor)."</td></tr>";
	}
}

function ver_control()
{
	global $MI,$MF,$BDC,$EST,$ASE,$A_oficina,$A_aseguradora,$A_vehiculo,$A_estado_vehiculo;
	html();
	$A_oficina=tabla2arreglo("$BDC.oficina");
	$A_aseguradora=tabla2arreglo("$BDC.aseguradora");
	$A_vehiculo=tabla2arreglo("$BDC.vehiculo",array('id','placa'));
	$A_estado_vehiculo=tabla2arreglo("$BDC.estado_vehiculo",array('id','nombre'));
	echo "<script language='javascript'>
		function aparece(dato,dato2)
		{	var Ob=document.getElementById(dato);
			if(Ob.style.visibility=='hidden') {Ob.style.visibility='visible';Ob.style.position='relative';if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/menos_opciones.png';}
			else	{Ob.style.visibility='hidden';Ob.style.position='absolute';if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/mas_opciones.png';recoger(dato);}
		}
		
		function recargar() {parent.recargar();}
		
		function ver_balance(id)	{modal('../Control/operativo/zbalance_estado.php?Acc=ver_balance&id='+id,0,0,500,500,'vbl');}
		
		function cambiar_estado(idubicacion) 
		{document.getElementById('img_cambio_'+idubicacion).style.backgroundColor='333333';
		modal('zcontrol_balance_estado.php?Acc=cambiar_estado&id='+idubicacion,0,0,500,500,'cub');}
		
		function ver_tabla_control(dato,fec_inicial_tc,fec_final_tc) 
		{
			//var Periodo=dato.substr(12,6);
			//var Fecha_inicial=Periodo.substr(0,4)+'-'+Periodo.substr(5,2)+'-1';
			//Fecha_inicial=dmy(Afecha(Fecha_inicial));
			//var Fecha_final=adicionadia(Fecha_inicial,30);
			var Placa=dato.substr(dato.lastIndexOf('_')+1);
			var Fecha_inicial=fec_inicial_tc;
			var Fecha_final=fec_final_tc;
			var IE='FI,'+Fecha_inicial+';FF,'+Fecha_final+';PI,'+Placa+';';
			IE+='recargar_datos();';
			modal('../Control/operativo/zcontrol_operativo3.php?Acc=inicio_operativo&Instruccion_Externa='+IE);
		}
		
		// CONTROL DE NODOS HIJOS PARA OCULTAR O APARECER MASIVAMENTE
		var Hijos=new Array();
		// CONTROL DE TOTALIZADORES 
		var Casos_oficina=new Array();
		var Casos_periodo=new Array();
		
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
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0'>
		<h3> Balances de Estado desde $MI hasta $MF </h3>
		<table cellspacing='0' bgcolor='ffffff'><tr><th>Periodo</th></tr>";
	include('inc/link.php');
	while($MI<=$MF)
	{
		$Fi=l($MI,4).'-'.r($MI,2).'-01';
		$Ff=l($MI,4).'-'.r($MI,2).'-'.ultimo_dia_de_mes(l($MI,4), r($MI,2));
		echo "<tr><td valign='top'>
				<img id='img_periodo_$MI' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('periodo_$MI');\">
				<a onclick=\"aparece('periodo_$MI','img_periodo_$MI');\"> $MI</a> <span id='puntos_$MI'><img  src='gifs/puntos.gif'></span></td><td>
				<span id='sp_$MI'></span>
				<script language='javascript'>
					Casos_periodo['sp_$MI']=new Array();
					Casos_periodo['sp_$MI']['cantidad']=0;
					Casos_periodo['sp_$MI']['valor']=0;
				</script>
				<table cellspacing='0' id='periodo_$MI' bgcolor='ffffff' style='visibility:hidden;position:absolute;'><tr><th>Oficinas</th><th>Estadisticas</th></tr>";
		$Oficinas=mysql_query("select distinct oficina from $BDC.ubicacion u where u.fecha_final between '$Fi' and '$Ff' ".
						($EST?"and u.estado=$EST ":"").($ASE?"and u.flota=$ASE ":"").
						"ORDER BY oficina",$LINK);
		while($Of=mysql_fetch_object($Oficinas))
		{
			$Nof='oficina_'.$MI.'_'.$Of->oficina;
			echo "<tr><td valign='top' nowrap='yes'>
						<img id='img_$Nof' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('$Nof');\">
						<a onclick=\"aparece('$Nof','img_$Nof');\">".$A_oficina[$Of->oficina]."</a></td>
					<td  valign='top'>
						<span id='sp_oficina_$Nof'></span>
						<script language='javascript'>
							if(!Hijos['periodo_$MI']) Hijos['periodo_$MI']=new Array();
							Hijos['periodo_$MI'][Hijos['periodo_$MI'].length]='$Nof';
							Casos_oficina['sp_oficina_$Nof']=new Array();
							Casos_oficina['sp_oficina_$Nof']['cantidad']=0;
							Casos_oficina['sp_oficina_$Nof']['valor']=0;
						</script>
					
					<table cellspacing='0' id='$Nof' style='visibility:hidden;position:absolute;' bgcolor='ffffff'><tr>
						<th>Aseguradora</th><th>Vehículos</th><th>Valor</th><td></td></tr>";
			$Aseguradoras=mysql_query("select distinct flota from $BDC.ubicacion u where u.fecha_final between '$Fi' and '$Ff' and oficina=$Of->oficina ".
							($EST?"and u.estado=$EST ":"").($ASE?"and u.flota=$ASE ":"").
							"ORDER BY flota",$LINK);
			while($As=mysql_fetch_object($Aseguradoras))
			{
				$Nas='aseguradora_'.$MI.'_'.$As->flota.'_'.$Of->oficina;
				echo "<tr><td valign='top' bgcolor='ffffdd' nowrap='yes'>
						<img id='img_$Nas' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('$Nas');\">
						<a onclick=\"aparece('$Nas','img_$Nas');\">".$A_aseguradora[$As->flota]."</a>
						<script language='javascript'>
							if(!Hijos['$Nof']) Hijos['$Nof']=new Array();
							Hijos['$Nof'][Hijos['$Nof'].length]='$Nas';
						</script></td>
						<td bgcolor='ffffdd' align='center' nowrap='yes'><b><span id='sp1_$Nas'> </span></b></td>
						<td valign='top' align='right' bgcolor='ffffdd'><b> $ <span id='sp2_$Nas'></span> </b></td><td></td></tr>
						<tr><td colspan=3></td><td>				
						<table cellspacing='0' id='$Nas' style='visibility:hidden;position:absolute;' bgcolor='ffffff'><tr><th>#</th><th>Placa</th><th>Valor</th></tr>";
				if(!$Ubicaciones_Vehiculos=mysql_query("select u.id as idubicacion,u.vehiculo as idvehiculo,u.fecha_inicial,u.fecha_final,u.estado,
									u.odometro_inicial,u.odometro_final,u.observaciones,u.obs_mantenimiento
									FROM $BDC.ubicacion u 
									WHERE u.fecha_final between '$Fi' and '$Ff' and oficina='$Of->oficina'  
									and u.flota=$As->flota ".($EST?"and u.estado=$EST ":"")." 
									ORDER BY u.vehiculo,u.fecha_final,u.fecha_inicial ",$LINK)) die(mysql_error($LINK));
				$A_vub=array();
				while($UV=mysql_fetch_object($Ubicaciones_Vehiculos))
				{
					if($A_vub[$UV->idvehiculo]) $A_vub[$UV->idvehiculo]->adiciona_ubicacion($UV,$LINK);
					else $A_vub[$UV->idvehiculo]=new Vehiculo_balance($UV,$A_vehiculo[$UV->idvehiculo],$Nas,$LINK);
				}
				$Contador_vehiculos=0;$Sumatoria_valores=0;
				foreach($A_vub as $idV => $Veh)
				{
					$Contador_vehiculos++;
					$Veh->pinta($Contador_vehiculos);
					$Sumatoria_valores+=$Veh->Valor;
				}
				echo "<tr><td>
							<script language='javascript'>
								document.getElementById('sp1_$Nas').innerHTML='$Contador_vehiculos';
								document.getElementById('sp2_$Nas').innerHTML='".coma_format($Sumatoria_valores)."';
								Casos_oficina['sp_oficina_$Nof']['cantidad']+=$Contador_vehiculos;
								Casos_oficina['sp_oficina_$Nof']['valor']+=$Sumatoria_valores;
								Casos_periodo['sp_$MI']['cantidad']+=$Contador_vehiculos;
								Casos_periodo['sp_$MI']['valor']+=$Sumatoria_valores;
							</script>
							</td></tr></table></td></tr>";
			}
			echo "</table></td></tr>";
		}
		echo "</table></td></tr>";
		echo "<script language='javascript'>document.getElementById('puntos_$MI').innerHTML='';</script>";
		$MI=aumentaperiodo($MI,1);
	}
	echo "</table>
	<script language='javascript'>
		for(var indice in Casos_oficina) 
			{
				var Casos=Casos_oficina[indice]['cantidad'];
				var Valor=Casos_oficina[indice]['valor'];
				var Promedio=(Casos?Redondeo(Valor/Casos,0):0);
				var Cadena=\"<table><tr><td width='30px'>Casos:</td><td width='16px' align='right'>\"+Casos+\"</td>\";
				Cadena+=\"<td width='30px'>Total:</td><td width='50px' align='right'>\"+monetario(Valor,0)+\"</td>\";
				Cadena+=\"<td width='40px'>Promedio:</td><td width='50px' align='right'>\"+monetario(Promedio,2)+\"</td></tr></table>\";
				document.getElementById(indice).innerHTML=Cadena;
			}
		for(var indice in Casos_periodo)
		{
			var Casos=Casos_periodo[indice]['cantidad'];
			var Valor=Casos_periodo[indice]['valor'];
			var Promedio=(Casos?Redondeo(Valor/Casos,0):0);
			var Cadena=\"<table><tr><td width='30px'>Casos:</td><td width='16px' align='right'>\"+Casos+\"</td>\";
			Cadena+=\"<td width='30px'>Total:</td><td width='50px' align='right'>\"+monetario(Valor,0)+\"</td>\";
			Cadena+=\"<td width='40px'>Promedio:</td><td width='50px' align='right'>\"+monetario(Promedio,2)+\"</td></tr></table>\";
			document.getElementById(indice).innerHTML=Cadena;
		}
	</script>
	";
	
	echo "</body>";
}

function pinta_periodo($dato='')
{
	$Resultado='';
	$P=200801;
	while($P<=date('Ym'))
	{
		$Resultado.="<option value='$P' ".($P==$dato?"selected":"").">$P</option>";
		$P=aumentaperiodo($P,1);
	}
	return $Resultado;
}

function cambiar_estado()
{
	global $id,$BDC;
	html('CAMBIO DE ESTADO');
	echo "
	<script language='javascript'>
		function continua()
		{
			with(document.forma)
			{
				if(NESTADO.value)	{ if(confirm('Desea cambiar el estado actual?')) {Acc.value='cambiar_estado_ok';submit();}}
				else {alert('Debe seleccionar un nuevo estado');NESTADO.style.backgroundColor='ff5555';return false;}
			}
		}
		function cancelar(){window.close();void(null);}
	</script>
	<body><script language='javascript'>centrar(900,550);</script>";
	$A_estado_vehiculo=tabla2arreglo("$BDC.estado_vehiculo",array('id','nombre'));
	$D=qo("select *,$BDC.t_vehiculo(vehiculo) as nveh,$BDC.t_oficina(oficina) as nofi,$BDC.t_aseguradora(flota) as naseg
				from $BDC.ubicacion where id=$id");
	
	echo "<form action='zcontrol_balance_estado.php' target='_self' method='POST' name='forma' id='forma'>
		<h3>Datos de la ubicación</h3>
		<table border cellspacing='0' width='100%'>
			<tr><td>Vehículo</td><td align='center'><b>$D->nveh</b></td><td>Estado Actual:</td><td align='center'><b>".$A_estado_vehiculo[$D->estado]."</b></td></tr>
			<tr><td>Kilometraje Inicial</td><td align='center'>".coma_format($D->odometro_inicial)."</td><td>Kilometraje Final:</td><td align='center'>".coma_format($D->odometro_final)."</td></tr>
			<tr><td>Oficina:</td><td align='center'>$D->nofi</td><td>Aseguradora:</td><td align='center'>$D->naseg</td></tr>
			<tr><td>Fecha inicial</td><td align='center'>$D->fecha_inicial</td><td>Fecha Final:</td><td align='center'>$D->fecha_final</td></tr>
			<tr><td>Observaciones</td><td colspan=3>".nl2br($D->observaciones)."</td></tr>
			<tr><td>Observaciones</td><td colspan=3>".nl2br($D->obs_mantenimiento)."</td></tr>
		</table><br>
		CAMBIAR ESTADO POR: ".menu1("NESTADO","Select id,nombre from $BDC.estado_vehiculo order by nombre",0,1)."<br>
		Motivo: <textarea name='motivo' cols=160 rows=2 style='font-size:11px' valign='top'></textarea><br><br><br>
		<center><input type='button' name='continuar' id='continuar' value=' CONTINUAR ' onclick='continua();' style='font-size:18px;font-weight:bold;width:200px;height:50px;'>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='salir' id='salir' value=' CANCELAR ' onclick='cancelar();' style='font-size:18px;font-weight:bold;width:200px;height:40px;'>
		<input type='hidden' name='Acc' value=''></center><input type='hidden' name='id' value='$id'>
	</form><br><br>
	</body>"; 
}

function cambiar_estado_ok()
{
	global $id,$NESTADO,$motivo,$BDC;
	$NUSUARIO=$_SESSION['Nombre'];
	$Ahora=date('Y-m-d H:i');
	q("update $BDC.ubicacion set observaciones=concat(observaciones,'\n".$NUSUARIO.' '.$Ahora." $motivo'),estado='$NESTADO' where id=$id");
	echo "<body><script language='javascript'>if(confirm('Cambio de Estado realizado. Desea recargar?')) {opener.recargar();window.close();void(null);} else {window.close();void(null);} </script></body>";
	
}























?>