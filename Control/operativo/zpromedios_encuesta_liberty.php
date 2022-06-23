<?php

$app='zpromedios_encuesta_liberty.php';

include('inc/funciones_.php');

if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');die();}

inicio_promedio();

function inicio_promedio()
{
	global $app;
	include('inc/gpos.php');
	html();
	echo "
		<link rel='stylesheet' href='http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css'>
		<link rel='stylesheet' href='inc/css/fa/css/font-awesome.css'>
		<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js'></script>
		<script src='http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'></script>
		
		<body>
			<script language='javascript'>centrar(800,500);</script>
			<h3>Cálculo Ponderado de Encuesta Liberty</h3>
			<form action='$app' target='_self' method='POST' name='forma' id='forma'>
				Seleccione la fecha inicial :".pinta_FC('forma','FI',date('Y-m-d'))."<br>
				Seleccione la fecha final : ".pinta_FC('forma','FF',date('Y-m-d'))."<br>
				<br>
				<div class='btn-group'>
					<button type='button' class='btn btn-success' onclick='this.form.submit();'><i id='c1' class='fa fa-play'></i> Continuar</button>
					<button type='button' class='btn btn-danger' onclick='window.close();void(null);'><i class='fa fa-times'></i> Cancelar</button>
				</div>
				<input type='hidden' name='Acc' value='calcular_promedio'>
			</form>
		</body>";
}

function calcular_promedio()
{
	global $app;
	include('inc/gpos.php');
	$Mes_inicial=date('Y-m',strtotime($FI));
	$Mes_final=date('Y-m',strtotime($FF));
	$Promedio=array();
	
	$Aseguradoras=q("select distinct si.aseguradora,t_aseguradora(si.aseguradora) as naseg 
		from siniestro si, encuesta_liberty en where en.servicio=si.id and si.fecha_final between '$FI' and '$FF' ");
	
	while($AS=mysql_fetch_object($Aseguradoras))
	{
		$Mes=$Mes_inicial;
		$Contador=1;
		while($Mes<=$Mes_final)
		{
			$fi=$Mes.'-01';
			$ff=$Mes.'-'.ultimo_dia_de_mes(date('Y',strtotime($fi)),date('m',strtotime($fi)));
			
			for($pregunta=1;$pregunta<=10;$pregunta++)
			{
				$Promedio[$AS->naseg][$Mes][$pregunta]=qo("select avg(p1) as promedio,count(en.id) as cantidad
						from encuesta_liberty en, siniestro si 
						where en.servicio=si.id and si.aseguradora=$AS->aseguradora and si.fecha_final between '$fi' and '$ff' and p1>0");
			}
			$Mes=date('Y-m',strtotime(aumentameses($FI,$Contador)));
			$Contador++;
		}
	}
	
	
	
	
	//echo "<br> Fi: $FI FF: $FF Mes inicial $Mes_inicial  Mes final: $Mes_final ";
	//echo "<br><br>";
	// print_r($Promedio);
	
	html();
	echo "
		<link rel='stylesheet' href='http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css'>
		<link rel='stylesheet' href='inc/css/fa/css/font-awesome.css'>
		<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js'></script>
		<script src='http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'></script>
		<style type='text/css'>
		<!--
			td {font-size:15px;padding:5px;}
		-->
		</style>
		<body>
		<div class='container'>
			<header>
				<h3 align='center'><img src='img/LogoAOA.jpg' border=0 height='90'><br>RESULTADOS DE ENCUESTA</H3>
			</header>
			<table border cellspacing='0'>
				<tr><th>ASEGURADORA</th>
					<th>PERIODO</th>
					<th>PREGUNTA 1</th>
					<th>PREGUNTA 2</th>
					<th>PREGUNTA 3</th>
					<th>PREGUNTA 4</th>
					<th>PREGUNTA 5</th>
					<th>PREGUNTA 6</th>
					<th>PREGUNTA 7</th>
					<th>PREGUNTA 8</th>
					<th>PREGUNTA 9</th>
					<th>PREGUNTA 1O</th>
					<th>Promedio</th>
					<th>Cantidad</th>
					<th></th>
				</tr>
			";
	$Total=0;
	foreach($Promedio as $Aseguradora=>$Promedios)
	{
		foreach($Promedios as $periodo=>$respuestas)		
		{
			echo "<tr><td>$Aseguradora</td><td>$periodo</td>";
			$Mayor=0;$Suma=0;
			foreach($respuestas as $respuesta)
			{
				if($Mayor< $respuesta->cantidad) $Mayor=$respuesta->cantidad;
				$Suma+=$respuesta->promedio;
				echo "<td align='center'>".round($respuesta->promedio,2)."</td>";
			}
			$Prom=round($Suma/10,2);
			echo "<td align='center'>$Prom</td><td align='center'>$Mayor</td>";
			echo "</tr>";
			$Total+=$Mayor;
		}
	}
	echo "
				<tr><td align='center' colspan=13><b>TOTAL</b></td><td align='center'>$Total</td></tr>
			</table>
			<br>
			<br>
			<br>
			<p align='justify'>
				";
	echo "<br><br><hr><h4>Explicación de la encuesta</h4>
		<menu>
		<li> Pregunta Número 1: De 0 a 10 donde 0 es nada probable y 10 es muy probable, recomendaría a Liberty Seguros a amigos o familiares?
		<li> Pregunta Número 2: En escala de 0 a 10 donde 0 es totalmente insatisfecho y 10 es totalmente satisfecho, que tan satisfecho se encuentra con el servicio de vehículo sustituto de Liberty Seguros?
		<br>
		<b>En una escala de 0 a 10 (donde 0 = muy insatisfecho, 10 = muy satisfecho) en base a su experiencia de los servicios más recientes de vehículo sustituto por favor califique los
		siguientes factores:</b>
		<li> Pregunta Número 3: Facilidad de contacto con AOA
		<li> Pregunta Número 4: Amabilidad del funcionario
		<li> Pregunta Número 5: Claridad en la información recibida para la asignación de Vehículo Sustituto
		<li> Pregunta Número 6: Por favor indíquenos el número de veces que tuvo que llamar para recibir información del servicio y coordinar la entrega del vehículo
		<li> Pregunta Número 7: Tiempo para pa asignación del vehículo
		<li> Pregunta Número 8: Facilidad para la entrega del vehículo
		<li> Pregunta Número 9: Calidad del Vehículo asignado
		<li> Pregunta Número 10: La facilidad para la devolución del Vehículo
		</menu>";

	echo "<br><br><b>Nota:</b> Este informe toma como campo de referencia la fecha final del servicio.";
	echo "
			</p>
		</div>
	</body>";
}

?>