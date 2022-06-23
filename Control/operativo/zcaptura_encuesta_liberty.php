<?php

/*

	   PROGRAMA PARA CAPTURAR LOS RESULTADOS DE LAS ENCUESTAS INDIVIDUALES POR SINIESTRO DE LA ASEGURADORA LIBERTY

*/
$app='zcaptura_encuesta_liberty.php';
include('inc/funciones_.php');
sesion();
if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}

function inicio_captura()
{
	global $app;
	html("Captura Encuesta Liberty");
	include('inc/gpos.php');
	if($Siniestro=qo("SELECT * FROM siniestro WHERE id=$id"))
	{
		$p1=$p2=$p3=$p4=$p5=$p6=$p7=$p8=$p9=$p10='';
		if($E=qo("select * from encuesta_liberty where servicio=$id"))
		{	$p1=$E->p1;	$p2=$E->p2;	$p3=$E->p3;	$p4=$E->p4;	$p5=$E->p5;	$p6=$E->p6;	$p7=$E->p7;	$p8=$E->p8;	$p9=$E->p9;	$p10=$E->p10;	}
		
		echo "
			<script language='javascript'>
				function valida_respuestas()
				{
					var respondio=false;
					with(document.forma)
					{
						if(p1.value) {respondio=true;}
						if(p2.value) {respondio=true;}
						if(p3.value) {respondio=true;}
						if(p4.value) {respondio=true;}
						if(p5.value) {respondio=true;}
						if(p6.value) {respondio=true;}
						if(p7.value) {respondio=true;}
						if(p8.value) {respondio=true;}
						if(p9.value) {respondio=true;}
						if(p10.value) {respondio=true;}
					}
					if(respondio)
						document.forma.submit();
					else
					{
						alert('Al menos debe responder una de las preguntas.');
						return false;
					}
					
				}
			</script>
			<body>
			<script language='javascript'>centrar(500,800);</script>
			<h4>Captura Encuesta Liberty</h4>
			<form action='$app' target='_self' method='POST' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='guardar_captura'>
				<table cellspacing=10>
					<tr>
						<td width='200'>De 0 a 10, Donde 0 es nada probable y 10 es muy probable, recomendaría a Liberty Seguros a amigos o familiares? NPS</td>
						<td>".pinta_pregunta('1',$p1)."</td>
					</tr>
					<tr>
						<td width='200'>En escala de 0 a 10, donde 0 es totalmente insatisfecho y 10 es totalmente satisfecho, que tan satisfecho se  encuentra con el servicio de vehículo sustituto de Liberty Seguros? INS – Vehiculo sustituto</td>
						<td>".pinta_pregunta('2',$p2)."</td>
					</tr>
					<tr>
						<td colspan=2 width='200'>En una escala de 0 a 10 (donde 0 = muy insatisfecho, 10 = muy satisfecho) en base a su experiencia de los servicios más reciente de vehículo sustituto por favor califique los siguientes factores:</td>
					</tr>
					<tr><td colspan=2><b>Calificación Call Center</b></td></tr>
					<tr><td>1. Facilidad de Contacto con AOA</td><td>".pinta_pregunta('3',$p3)."</td></tr>
					<tr><td>2. Amabilidad del funcionario de AOA</td><td>".pinta_pregunta('4',$p4)."</td></tr>
					<tr><td>3. Claridad de la información recibida para la asignación de vehículo sustituto</td><td>".pinta_pregunta('5',$p5)."</td></tr>
					<tr><td width='200'>4. Por favor indíquenos el número de veces que tuvo que llamar para recibir información del servicio y coordinar la entrega de vehículo</td><td>".pinta_pregunta('6',$p6)."</td></tr>
					<tr><td colspan=2><b>Entrega del Vehículo</b></td></tr>
					<tr><td>5. El tiempo para asignación del Vehículo</td><td>".pinta_pregunta('7',$p7)."</td></tr>
					<tr><td>6. La facilidad para la entrega del Vehículo</td><td>".pinta_pregunta('8',$p8)."</td></tr>
					<tr><td colspan=2><b>Vehículo Asignado</b></td></tr>
					<tr><td>7. Calidad del Vehículo Asignado</td><td>".pinta_pregunta('9',$p9)."</td></tr>
					<tr><td colspan=2><b>Devolución del Vehículo</b></td></tr>
					<tr><td>8. Facilidad para la devolución del Vehículo</td><td>".pinta_pregunta('10',$p10)."</td></tr>
				</table>
				<br>
				<center>
					<input type='button' name='seguir' id='seguir' value=' GRABAR ENCUESTA ' onclick='valida_respuestas();');\">
				</center>
				<input type='hidden' name='id' value='$id'>
			</form>
		</body>";
	}
	else
		echo "<body><script language='javascript'>alert('No se encuentra el registro del Siniestro.');window.close();void(null);</script></body>";
}

function guardar_captura()
{
	global $app;
	include('inc/gpos.php');
	if($ID=qo1("select id from encuesta_liberty where servicio=$id"))
	{
		q("update encuesta_liberty set p1='$p1',p2='$p2',p3='$p3',p4='$p4',p5='$p5',p6='$p6',p7='$p7',p8='$p8',p9='$p9',p10='$p10' where id=$ID");
		graba_bitacora('encuesta_liberty','M',$ID,'Actualiza la encuesta');
		$Aviso='Encuesta actualizada satisfactoriamente';
	}
	else
	{
		$NID=q("insert into encuesta_liberty (servicio,p1,p2,p3,p4,p5,p6,p7,p8,p9,p10) values ('$id','$p1','$p2','$p3','$p4','$p5','$p6','$p7','$p8','$p9','$p10')");
		graba_bitacora('encuesta_liberty','A',$NID,'Adiciona registro');
		$Aviso='Encuesta grabada satisfactoriamente';
	}
	echo "<body><script language='javascript'>alert('$Aviso');window.close();void(null);</script></body>";
}

function pinta_pregunta($numero,$dato)
{
	return "<select name='p$numero'><option value=''></option>
	<option value='0' ".($dato=='0'?"selected":"").">0</option>
	<option value='1' ".($dato=='1'?"selected":"").">1</option>
	<option value='2' ".($dato=='2'?"selected":"").">2</option>
	<option value='3' ".($dato=='3'?"selected":"").">3</option>
	<option value='4' ".($dato=='4'?"selected":"").">4</option>
	<option value='5' ".($dato=='5'?"selected":"").">5</option>
	<option value='6' ".($dato=='6'?"selected":"").">6</option>
	<option value='7' ".($dato=='7'?"selected":"").">7</option>
	<option value='8' ".($dato=='8'?"selected":"").">8</option>
	<option value='9' ".($dato=='9'?"selected":"").">9</option>
	<option value='10' ".($dato=='10'?"selected":"").">10</option></select>";
}



?>