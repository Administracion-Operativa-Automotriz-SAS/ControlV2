<?php

/*
PROGRAMA PARA CAPTURAR LOS DATOS DE GARANTIA DE AUTORIZACION PARA LOS CLIENTES
ESTA ES UNA OPCION PUBLICA SIN SESION. PERO REQUIERE DE LA VALIDACION DE LA PLACA

6LfWLiUTAAAAABMwEwNpKgKrta-HGtZ1OzWnWgMj
Clave secreta
?sala para las comunicaciones entre tu sitio y Google. Ten la precauci?n de no revel?rsela a nadie.

*/

include('inc/funciones_.php');
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');die();}

inicio_pantalla();

function inicio_pantalla()
{
	html('CAPTURA DATOS DE GARANTIA');
	echo "
		<meta charset='iso-8859-1'>
		<meta http-equiv='X-UA-Compatible' content='IE=edge'>
		<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'>
		<script src='https://www.google.com/recaptcha/api.js'></script>
		<body><img src='img/LOGO_AOA_200.png'><h3>CAPTURA INFORMACION DE GARANTIA</h3>
	<form action='wgarantiaservicio.php' target='_self' method='POST' name='forma' id='forma'>
		<p style='font-size:20px'>
			Por favor digite la placa del veh?culo Siniestrado o su C?digo de Servicio de Renta (sin espacios ni separadores):
			<input type='text' name='placa' id='placa' style='font-size:20px;text-align:center;' value='' size='10' maxlength='7' placeholder='ABC123' onkeyUp='this.value=this.value.toUpperCase();'>
		</p>
		<p style='font-size:20px'>
			Digite el n?mero de tel?fono registrado: <input type='password' style='font-size:20px;' size=10 name='telefono' id='telefono'>
		</p>
		<div class='g-recaptcha' data-sitekey='6LfWLiUTAAAAABMwEwNpKgKrta-HGtZ1OzWnWgMj'></div>
		<input type='button' name='seguir' id='seguir' value='CONTINUAR' class='button green large' style='width:400px' onclick=\"document.forma.placa.value=alltrim(document.forma.placa.value);valida_campos('forma','placa,telefono:n');\">
		<input type='hidden' name='Acc' value='consultar_placa'>
	</form>
	<script language='javascript'>document.forma.placa.focus();</script>
	</body>";
}

function consultar_placa()
{
	include('inc/gpos.php');
	html('CAPTURA INFORMACION DE GARANTIA');
	if(isset($_POST['g-recaptcha-response']))
	{
		if(!$_POST['g-recaptcha-response']) {echo "<body><script language='javascript'>alert('Debe indicar que NO ES UN ROBOT.');window.history.back();</script></body>";die();}
	}
	$Validacion_vencimiento=date('Ym');
	echo "
		<meta charset='iso-8859-1'>
		<meta http-equiv='X-UA-Compatible' content='IE=edge'>
		<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'>
		<script language='javascript'>

		var ConGarantia_tarjeta=true;
		var ConGarantia_noreembolsable=false;
		var ConGarantia_efectivo=false;

		function regresar()	{window.open('wgarantiaservicio.php','_self');}
		function busqueda_ciudad2(Campo,Contenido)
		{
			var Ventana_ciudad=document.getElementById('Busqueda_Ciudad');
			Ventana_ciudad.style.visibility='visible';Ventana_ciudad.style.left=mouseX;Ventana_ciudad.style.top=mouseY-10;Ventana_ciudad.src='inc/ciudades.html';
			Ciudad_campo=Campo;Ciudad_forma='forma';
		}
		function oculta_busca_ciudad()	{document.getElementById('Busqueda_Ciudad').style.visibility='hidden';}
		function valida_vencimiento()
		{
			var Vm=document.forma.vencimiento_mes.value;
			var Va=document.forma.vencimiento_ano.value;
			if(Vm && Va)
			{
				var V=Va+Vm;
				if(V<'$Validacion_vencimiento')
				{alert('No se puede aceptar una tarjeta vencida. Verifique la informaci?n o solicite otra tarjeta.');document.forma.Enviar.disabled=true;return false;}
				else	document.forma.Enviar.disabled=false;
			}
		}

		function validacion_general_campos()
		{
			with(document.forma)
			{
				if(Number(identificacion.value)<1000 || !identificacion.value) {alert('Debe escribir un n?mero de identificaci?n v?lido.');identificacion.style.backgroundColor='#ffffaa';identificacion.focus();return false;}
				lugar_expdoc.value=alltrim(lugar_expdoc.value);
				if(!lugar_expdoc.value) {alert('Debe escribir un lugar de expedici?n de la identificaci?n v?lido.');lugar_expdoc.style.backgroundColor='#ffffaa';lugar_expdoc.focus();return false;}
				nombre.value=alltrim(nombre.value);
				if(!nombre.value) {alert('Debe escribir un nombre v?lido.');nombre.style.backgroundColor='#ffffaa';nombre.focus();return false;}
				apellido.value=alltrim(apellido.value);
				if(!apellido.value) {alert('Debe escribir un apellido v?lido.');apellido.style.backgroundColor='#ffffaa';apellido.focus();return false;}
				if(!tipo_id.value) {alert('Debe seleccionar un tipo de identificaci?n v?lido.');tipo_id.style.backgroundColor='#ffffaa';tipo_id.focus();return false;}
				if(!sexo.value) {alert('Debe seleccionar un sexo v?lido.');sexo.style.backgroundColor='#ffffaa';sexo.focus();return false;}
				if(!ciudad.value) {alert('Debe seleccionar una ciudad v?lida.');_ciudad.style.backgroundColor='#ffffaa';_ciudad.focus();return false;}
				direccion.value=alltrim(direccion.value);
				if(!direccion.value) {alert('Debe escribir una direcci?n v?lida.');direccion.style.backgroundColor='#ffffaa';direccion.focus();return false;}
				telefono_oficina.value=alltrim(telefono_oficina.value);
				if(!telefono_oficina.value) {alert('Debe escribir un tel?fono de oficina v?lido o en su defecto el n?mero de celular actual.');telefono_oficina.style.backgroundColor='#ffffaa';telefono_oficina.focus();return false;}
				telefono_casa.value=alltrim(telefono_casa.value);
				if(!telefono_casa.value) {alert('Debe escribir un tel?fono de casa v?lido o en su defecto el n?mero de celular actual.');telefono_casa.style.backgroundColor='#ffffaa';telefono_casa.focus();return false;}
				celular.value=alltrim(celular.value);
				if(!celular.value) {alert('Debe escribir un n?mero de celular v?lido.');celular.style.backgroundColor='#ffffaa';celular.focus();return false;}
				if(!validaemail(document.forma.email_e)) {email_e.style.backgroundColor='#ffffaa';email_e.focus();return false;}
				if(ConGarantia_tarjeta)
				{
					numero_tarjeta.value=alltrim(numero_tarjeta.value);
					if(!numero_tarjeta.value) {alert('Debe escribir un n?mero de tarjeta v?lido.');numero_tarjeta.style.backgroundColor='#ffffaa';numero_tarjeta.focus();return false;}
					if(!franquicia.value) {alert('Debe seleccionar una franquicia.');franquicia.style.backgroundColor='#ffffaa';franquicia.focus();return false;}
					if(!banco.value) {alert('Debe seleccionar un banco.');banco.style.backgroundColor='#ffffaa';banco.focus();return false;}
					if(!vencimiento_mes.value) {alert('Debe seleccionar un mes de vencimiento.');vencimiento_mes.style.backgroundColor='#ffffaa';vencimiento_mes.focus();return false;}
					if(!vencimiento_ano.value) {alert('Debe seleccionar un a?o de vencimiento.');vencimiento_ano.style.backgroundColor='#ffffaa';vencimiento_ano.focus();return false;}
					codigo_seguridad.value=alltrim(codigo_seguridad.value);
					if(!codigo_seguridad.value) {alert('Debe escribir un c?digo de seguridad v?lido.');codigo_seguridad.style.backgroundColor='#ffffaa';codigo_seguridad.focus();return false;}
					devol_cuenta_banco.value=alltrim(devol_cuenta_banco.value);
					if(!devol_cuenta_banco.value) {alert('Debe escribir un n?mero de cuenta bancaria v?lido.');devol_cuenta_banco.style.backgroundColor='#ffffaa';devol_cuenta_banco.focus();return false;}
					if(!devol_tipo_cuenta.value) {alert('Debe seleccionar un tipo de cuenta.');devol_tipo_cuenta.style.backgroundColor='#ffffaa';devol_tipo_cuenta.focus();return false;}
					if(!devol_banco.value) {alert('Debe seleccionar un banco.');devol_banco.style.backgroundColor='#ffffaa';devol_banco.focus();return false;}
					devol_ncuenta.value=alltrim(devol_ncuenta.value);
					if(!devol_ncuenta.value) {alert('Debe escribir un nombre de titular de cuenta bancaria v?lido.');devol_ncuenta.style.backgroundColor='#ffffaa';devol_ncuenta.focus();return false;}
					if(Number(identificacion_devol.value)<1000 || !identificacion_devol.value) {alert('Debe escribir un n?mero de identificaci?n v?lido.');identificacion_devol.style.backgroundColor='#ffffaa';identificacion_devol.focus();return false;}
					if(!img_tarjeta_credito.value) {alert('Debe subir la imagen de la tarjeta de cr?dito');img_tarjeta_credito.style.backgroundColor='#ffffaa';img_tarjeta_credito.focus();return false;}
				}
				if(ConGarantia_noreembolsable)
				{
					if(!numero_comprobante.value) {alert('Debe escribir el n?mero de comprobante de consignaci?n.');numero_comprobante.style.backgroundColor='#ffffaa';numero_comprobante.focus();return false;}
					if(!comprobante_consignacion.value) {alert('Debe cargar la imagen del comprobante de consitnaci?n. ');comprobante_consignacion.style.backgroundColor='#ffffaa';numero_comprobante.focus();return false;}
					if(!fecha_consignacion.value) {alert('Debe seleccionar la fecha de consignaci?n. ');fecha_consignacion.style.backgroundColor='#ffffaa';fecha_consignacion.focus();return false;}
				}
				if(ConGarantia_efectivo)
				{
					if(!numero_comprobante1.value) {alert('Debe escribir el n?mero de comprobante de consignaci?n.');numero_comprobante.style.backgroundColor='#ffffaa';numero_comprobante.focus();return false;}
					if(!comprobante_consignacion1.value) {alert('Debe cargar la imagen del comprobante de consitnaci?n. ');comprobante_consignacion.style.backgroundColor='#ffffaa';numero_comprobante.focus();return false;}
					if(!fecha_consignacion1.value) {alert('Debe seleccionar la fecha de consignaci?n. ');fecha_consignacion.style.backgroundColor='#ffffaa';fecha_consignacion.focus();return false;}
				}
				submit();
			}
		}
		function cambio_tipo_garantia(dato)
		{
			if(dato==1)
			{
				document.getElementById('dtc').style.display='block';
				document.getElementById('dtco').style.display='none';
				document.getElementById('dtrnr').style.display='none';
				ConGarantia_tarjeta=true;ConGarantia_noreembolsable=false;ConGarantia_efectivo=false;
				document.forma.TIPO_GARANTIA.value='tarjeta';
				document.getElementById('resto_datos').style.display='block';
				document.getElementById('seguir').style.visibility='visible';
			}
			else if(dato==2)
			{
				document.getElementById('dtc').style.display='none';
				document.getElementById('dtco').style.display='none';
				document.getElementById('dtrnr').style.display='block';
				ConGarantia_tarjeta=false;ConGarantia_noreembolsable=true;ConGarantia_efectivo=false;
				document.forma.TIPO_GARANTIA.value='noreembolsable';
				document.getElementById('resto_datos').style.display='block';
				document.getElementById('seguir').style.visibility='visible';
			}
			else if(dato==3)
			{
				document.getElementById('dtco').style.display='block';
				document.getElementById('dtc').style.display='none';
				document.getElementById('dtrnr').style.display='none';
				ConGarantia_tarjeta=false;ConGarantia_noreembolsable=false;ConGarantia_efectivo=true;
				document.forma.TIPO_GARANTIA.value='efectivo';
				document.getElementById('resto_datos').style.display='block';
				document.getElementById('seguir').style.visibility='visible';
			}
		}
	</script>
	<style type='text/css'>
	<!--
		body {font-size:18px;}
		td {font-size:18px;}
		select {font-size:18px;}
		textarea {font-size:18px;}
		input {font-size:20px;}
	-->
	</style>
	<body><img src='img/LOGO_AOA_200.png'><h3>CAPTURA INFORMACION DE GARANTIA</h3>";
	if($P=qo("select id,estado from siniestro where placa='$placa' and declarante_celular='$telefono' order by id desc"))
	{
		if($P->estado==1) /* NO ADJUDICADO */
		{echo "<h4>La placa $placa se encuentra en estado NO ADJUDICADO.</h4><input type='button' class='button green large' name='volver' id='volver' value='REGRESAR' onclick='regresar();'>";}
		elseif($P->estado==5)
		{echo "<h4>La placa $placa se encuentra en estado PENDIENTE a?n no ha sido adjudicado el servicio.</h4><input type='button' class='button green large' name='volver' id='volver' value='REGRESAR' onclick='regresar();'>";}
		elseif($P->estado==7)
		{echo "<h4>La placa $placa se encuentra en SERVICIO no requiere de datos de garant?a.</h4><input type='button' class='button green large' name='volver' id='volver' value='REGRESAR' onclick='regresar();'>";}
		elseif($P->estado==8)
		{echo "<h4>La placa $placa se encuentra en estado SERVICIO CONCLUIDO no requiere de datos de garant?a.</h4><input type='button' class='button green large' name='volver' id='volver' value='REGRESAR' onclick='regresar();'>";}
		elseif($P->estado==3) /*ADJUDICADO*/
		{
			$S=qo("select * from siniestro where id=$P->id");
			$A=qo("select * from aseguradora where id=$S->aseguradora");
			$C=qo("select concat(departamento,' ',nombre) as nciu from ciudad where codigo='$S->ciudad' ");
			if($S->renta)
			{
				echo "<h3>INFORMACION DEL SERVICIO DE RENTA</H3><table>
						<tr><td>C?digo de Servicio de Renta:</td><td>$S->placa</td></tr>
						<tr><td>Numero de Servicio</td><td>$S->numero</td></tr>
						<tr><td>Cliente</td><td>$A->nombre</td></tr>
						<tr><td>Usuario</td><td>$S->asegurado_nombre</td></tr>
						<tr><td>Solicitante</td><td>$S->declarante_nombre</td></tr>
						<tr><td>Ciudad de Atenci?n</td><td>$C->nciu</td></tr>
						<tr><td>Fecha de Autorizaci?n</td><td>".fecha_completa($S->fec_autorizacion)."</td></tr>
					</table>";
			}
			else
			{
				echo "<h3>INFORMACION DEL SINIESTRO:</h3>
					<table>
						<tr><td>Placa del veh?culo Siniestrado</td><td>$S->placa</td></tr>
						<tr><td>Numero de Siniestro</td><td>$S->numero</td></tr>
						<tr><td>Aseguradora</td><td>$A->nombre</td></tr>
						<tr><td>Asegurado</td><td>$S->asegurado_nombre</td></tr>
						<tr><td>Declarante</td><td>$S->declarante_nombre</td></tr>
						<tr><td>Ciudad de Atenci?n</td><td>$C->nciu</td></tr>
						<tr><td>Fecha de Autorizaci?n</td><td>".fecha_completa($S->fec_autorizacion)."</td></tr>
					</table>";
			}
			if($Autorizaciones=q("select nombre,fecha_solicitud from sin_autor where siniestro=$P->id and estado='E' "))
			{
				echo "<h3>GARANTIA EN PROCESO</h3>Estimado Usuario: Ya se encuentra una o m?s garant?as en proceso. Debe esperar a que sea aprobada o rechazada para continuar con el proceso o volver a incluir datos de una nueva garant?a.";
				echo "<br><br>Garant?a(s) en proceso: <br><br><table border cellspacing='0'>
					<tr><th>Responsable</th><th>Fecha de Solicitud</th></tr>";
				while($Au=mysql_fetch_object($Autorizaciones))
				{
					echo "<tr><td>$Au->nombre</td><td>$Au->fecha_solicitud</td></tr>";
				}
				echo "</table></body>";
				die();
			}
			elseif($Autorizaciones=q("select nombre,fecha_solicitud,fecha_proceso from sin_autor where siniestro=$P->id and estado='A' "))
			{
				echo "<h3>GARANTIA PROCESADA</h3>Estimado Usuario: Ya se encuentra una o m?s garant?as procesadas. ";
				echo "<br><br>Garant?a(s) procesadas: <br><br><table border cellspacing='0'>
					<tr><th>Responsable</th><th>Fecha de Solicitud</th><th>Fecha de proceso</th><th>Resultado</th></tr>";
				while($Au=mysql_fetch_object($Autorizaciones))
				{
					echo "<tr><td>$Au->nombre</td><td>$Au->fecha_solicitud</td><td>$Au->fecha_proceso</td><td style='background-color:ddffdd;'>ACEPTADA</td></tr>";
				}
				echo "</table></body>";
				die();
			}
			if($Autorizaciones=q("select nombre,fecha_solicitud,fecha_proceso from sin_autor where siniestro=$P->id and estado='R' "))
			{
				echo "<h3>GARANTIA PROCESADA</h3>Estimado Usuario: Ya se encuentra una o m?s garant?as procesadas. ";
				echo "<br><br>Garant?a(s) procesadas: <br><br><table border cellspacing='0'>
					<tr><th>Responsable</th><th>Fecha de Solicitud</th><th>Fecha de proceso</th><th>Resultado</th></tr>";
				while($Au=mysql_fetch_object($Autorizaciones))
				{
					echo "<tr><td>$Au->nombre</td><td>$Au->fecha_solicitud</td><td>$Au->fecha_proceso</td><td style='background-color:ffdddd;'>RECHAZADA</td></tr>";
				}
				echo "</table>";
			}
			echo "<p style='font-size:20px'>Estimado Usuario: La garant?a que se constituye para acceder al servicio se puede hacer usando la documentaci?n propia o de cualquier persona de su confianza.
				Por favor diligencie la informaci?n de la persona que va a prestar la garant?a en el siguiente formulario:</p>
				<iframe id='Busqueda_Ciudad' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#eeffee;z-index:200;' height='400px' width='200px' ></iframe>
				<form action='wgarantiaservicio.php' enctype='multipart/form-data' target='_self' method='POST' name='forma' id='forma'>
					<input type='hidden' name='MAX_FILE_SIZE' value='50000000'>
					<h3>DATOS PERSONALES</h3>
					<table border cellspacing='0'>
						<tr><td>N?mero de Identificaci?n</td><td><input type='text' name='identificacion' id='identificacion' value='' size='30' maxlength='15' placeholder='N?mero sin comas ni puntos'></td></tr>
						<tr><td>Lugar de Expedici?n de la Identificaci?n</td><td><input type='text' name='lugar_expdoc' id='lugar_expdoc' value='' size='50' maxlength='50' onkeyUp='this.value=this.value.toUpperCase();'></td></tr>
						<tr><td>Nombres:</td><td ><input type='text' name='nombre' size='50' maxlength='50' onkeyUp='this.value=this.value.toUpperCase();' placeholder='Nombres'></td></tr>
						<tr><td>Apellidos:</td><td ><input type='text' name='apellido' size='50' maxlength='50' onkeyUp='this.value=this.value.toUpperCase();' placeholder='Apellidos'></td></tr>
						<tr ><td>Tipo de identificaci?n:</td><td >";
			echo menu1('tipo_id',"select codigo,nombre from tipo_identificacion",'',1);
			echo "</td></tr>
						<tr><td>Sexo:</td><td ><select name='sexo' id='sexo'><option value=''></option><option value='M'>Masculino</option><option value='F'>Femenino</option></select></td></tr>
						<tr ><td>Pais</td><td >";
			echo menu1('pais',"select codigo,nombre from pais order by nombre",'CO',1);
			echo "</td></tr>
						<tr><td>Ciudad de Domicilio </td><td ><input type='text' style='color:#000099;background-color:#FFFFFF;' name='_ciudad' id='_ciudad' size='30' onclick=\"busqueda_ciudad2('ciudad','05001000');\" readonly placeholder='click aqu?'><input type='hidden' name=ciudad id=ciudad value=''><span id='bc_ciudad'></span> <img src='gifs/standar/izquierda.png'> Click en la casilla para seleccionar la ciudad.</td></tr>
						<tr ><td>Direcci?n Domicilio</td><td ><input type='text' name='direccion' id='direccion' size='50' maxlength='50' onkeyUp='this.value=this.value.toUpperCase();' placeholder='Direcci?n de donde vive'></td></tr>
						<tr><td>Tel?fono Oficina</td><td ><input type='text' name='telefono_oficina' id='telefono_oficina' size='50' maxlength='50'></td></tr>
						<tr ><td>Tel?fono Vivienda</td><td ><input type='text' name='telefono_casa' id='telefono_casa' size='50' maxlength='50'></td></tr>
						<tr><td>Celular</td><td ><input type='text' name='celular' id='celular' size='50' maxlength='50'></td></tr>
						<tr ><td>Cotreo Electr?nico</td><td><input type='text' name='email_e' id='email_e' size='50' maxlength='70' onkeyUp='this.value=this.value.toLowerCase();' placeholder='correo electr?nico'></td></tr>
						<tr><td>Observaciones</td><td><textarea name='observaciones' cols=80 rows=2></textarea></td></tr>
					</table>
					<br>";
			// A PARTIR DE LA CITA 130508 empezo a funcionar la variable siniestro.tipogarantia que se asigna al momento de la adjudicaci?n
			// tipogarantia=1 es NO REEMBOLSABLE   tipogarantia=2 es REEMBOLSABLE
			if($S->tipogarantia!=1 && $S->no_garantia==0)
			{
				$Oficina=qo("Select * from oficina where ciudad='$S->ciudad'");

				// VALIDACION ANTERIOR SOBRE LA GARANTIA NO REEMBOLSABLE

				// Se valida si la aseguradora tiene la opci?n de Garantia no reembolsable dentro de sus tarifas
				// if($gnr=qo("SELECT * FROM tarifa WHERE aseguradora=$S->aseguradora and concepto=33 and activa=1"))
				// {
				//	se calcula el valor no reembolsable por el numero de dias del servicio mas el iva
					// $Valor_noreembolsable=$gnr->valor*$S->dias_servicio*1.16;
				// }
				// else {$Valor_noreembolsable=0;}

				// VALIDACION NUEVA SOBRE LA GARANTIA NO REEMBOLSABLE A PARTIR DE JUNIO 9 2015
				$Valor_noreembolsable=0;
				if($A->valor_no_reembols) $Valor_noreembolsable=$A->valor_no_reembols*$S->dias_servicio*1.16;
				// FIN nueva validacion sobre garantia no reembolsable.

				echo "<h3 style='color:blue;'>Seleccione un tipo de garant?a:</h3>
							<br>TIPO DE GARANTIA <input type='button' class='button small blue' value='Tarjeta de Cr?dito' onclick='cambio_tipo_garantia(1);'> <input type='button' class='button small blue' value='Consignaci?n Efectivo' onclick='cambio_tipo_garantia(3);'> ";
				if($Valor_noreembolsable) echo "<input type='button' class='button small blue' value='Todo Riesgo No Reembolsable' onclick='cambio_tipo_garantia(2);'>";
				echo "<div id='dtc' style='display:none;'>
					<h3>DATOS DE LA TARJETA DE CREDITO</h3>
					<table border cellspacing='0'>
						<tr><td>VALOR DE LA GARANTIA</td><td>$".coma_format($A->garantia)."</td></tr>
						<tr><td>NUMERO DE TARJETA</td><td><input type='text' name='numero_tarjeta' id='numero_tarjeta' value='' size='50' maxlength='16' placeholder='N?mero de tarjeta sin guiones ni separadores'></td></tr>
						<tr><td>FRANQUICIA</td><td>".menu1("franquicia","select f.id,f.nombre from franquisia_tarjeta f,ciudad_franq c where c.franquicia=f.id and c.oficina=$Oficina->id and concat(',',c.aseguradora,',') like '%,$S->aseguradora,%' and f.id in (1,2,3,4)",0,1,'')."</td></tr>
						<tr><td>BANCO AL QUE PERTENECE LA TARJETA</td><td>".menu1("banco","select id,nombre from codigo_ach order by nombre",0,1)."</td></tr>
						<tr><td>FECHA DE VENCIMIENTO</td><td>".menu3("vencimiento_mes","01,01;02,02;03,03;04,04;05,05;06,06;07,07;08,08;09,09;10,10;11,11;12,12",0,1,''," onchange='valida_vencimiento()' ");
						echo " - <select name='vencimiento_ano' id='vencimiento_ano' onchange='valida_vencimiento()'><option value=''></option>";for($a=date('Y');$a<2100;$a++) { echo "<option value='$a'>$a</option>"; } echo "</select></td></tr>
						<tr><td>CODIGO DE SEGURIDAD</td><td valign='top'><input type='password' id='codigo_seguridad' name='codigo_seguridad' class='numero' size='4' maxlength='4'> El c?digo de seguridad aparece en el reverso de su tarjeta <br />
						como aparece en las im?genes: <img src='img/codigo_seguridad_tc.jpg' height='150' align='top'></td></tr>
						<tr><td height='100' width='200'>Tarjeta de Cr?dito (solo el frente, sin c?digo de seguridad) </td><td><input type='file' name='img_tarjeta_credito'></td></tr>
					</table>
					</div>
					<div id='dtco' style='display:none;'>
						<h3>DATOS DE LA CONSIGNACION</h3>
						<table border cellspacing='0'>
							<tr><td>VALOR DE LA GARANTIA</td><td>$".coma_format($A->garantia_consignada)."</td></tr>
							<tr><td>NUMERO DE COMPROBANTE DE CONSIGNACION</td><td><input type='text' name='numero_comprobante1' id='numero_comprobante1' value='' size='30' maxlength='30' placeholder='No. Comprobante Consignaci?n'></td></tr>
							<tr><td colspan='2'>COMPROBANTE DE CONSIGNACION</td></tr>
							<tr><td colspan='2' height='100'><input type='file' name='comprobante_consignacion1'></td></tr>
							<tr><td>FECHA DE CONSIGNACION</td><td>".pinta_FC('forma','fecha_consignacion1')."</td></tr>
						</table>
						<b style='color:red'>NOTA: RECUERDE LLEVAR EL COMPROBANTE DE CONSIGNACION ORIGINAL EL DIA DE LA CITA</B>
					</div>
					<div id='dtrnr' style='display:none;'>
					<h3>DATOS DE LA CONSIGNACION</h3>
					<table border cellspacing='0'>
						<tr><td>VALOR DE LA GARANTIA</td><td>$".coma_format($Valor_noreembolsable)."</td></tr>
						<tr><td>NUMERO DE COMPROBANTE DE CONSIGNACION</td><td><input type='text' name='numero_comprobante' id='numero_comprobante' value='' size='30' maxlength='30' placeholder='No. Comprobante Consignaci?n'></td></tr>
						<tr><td colspan='2'>COMPROBANTE DE CONSIGNACION</td></tr>
						<tr><td colspan='2' height='100'><input type='file' name='comprobante_consignacion'></td></tr>
						<tr><td>FECHA DE CONSIGNACION</td><td>".pinta_FC('forma','fecha_consignacion')."</td></tr>
					</table>
					<b style='color:red'>NOTA: RECUERDE LLEVAR EL COMPROBANTE DE CONSIGNACION ORIGINAL EL DIA DE LA CITA</B>
					</div>
					";

				echo "
					<div id='resto_datos' style='display:none;'>
						<br><br><h3>DATOS FINANCIEROS PARA DEVOLUCIONES</h3>
						En algunos casos, si aplica, la garant?a puede usarse para cancelar facturas asociadas al servicio. Cuando esto sucede, se debe hacer devoluci?n del dinero sobrante mediante transferencia electr?nica.
						La informaci?n financiera para devoluciones es utilizada para agilizar el traslado electr?nico de dichos montos. Por favor llene completamente los siguientes campos:
						<table border cellspacing='0'>
						<tr><td>Numero de cuenta Bancaria</td><td><input type='text' name='devol_cuenta_banco' id='cuenta' size=50 maxlength='20' placeholder='N?mero de cuenta sin separadores'></td></tr>
						<tr><td>Tipo de cuenta</td><td><select name='devol_tipo_cuenta' id='tipo'><option value=''></option><option value='A'>Ahoros</option><option value='C'>Corriente</option></select></td></tr>
						<tr><td>Banco</td><td>";
				echo menu1("devol_banco","select id,nombre from codigo_ach where codigo!='' order by nombre",0,1);
				echo "</td></tr>
						<tr><td>Nombre del Titular</td><td><input type='text' name='devol_ncuenta' id='devol_ncuenta' value='' size='50' maxlength='50' onkeyup='this.value=this.value.toUpperCase();'></td></tr>
						<tr><td>Identificaci?n</td><td><input type='text' name='identificacion_devol' id='identificacion_devol' value='' size='30' maxlength=15 placeholder='sin comas ni puntos'></td></tr>
						</table>";

				echo "<br><br><h3>IMAGENES DE DOCUMENTOS - No obligatorias </h3>
						Se?or Usuario, Si desea puede adjuntar aqui las im?genes escaneadas de sus documentos para agilizar a?n mas la gesti?n de la cita de la entrega de su veh?culo.
						Estas im?genes no son obligatorias.
						<table border cellspacing='0'>
							<tr><td height='100' width='200'>Licencia de Conducci?n (frente)</td><td><input type='file' name='img_licencia_conduccion_frente'></td></tr>
							<tr><td height='100' width='200'>Licencia de Conducci?n (reverso)</td><td><input type='file' name='img_licencia_conduccion_reverso'></td></tr>
							<tr><td height='100' width='200'>C?dula de Ciudadan?a (frente)</td><td><input type='file' name='img_cedula_ciudadania_frente'></td></tr>
							<tr><td height='100' width='200'>C?dula de Ciudadan?a (reverso)</td><td><input type='file' name='img_cedula_ciudadania_reverso'></td></tr>
						</table>
					</div>
					<input type='hidden' name='TIPO_GARANTIA' value=''>";
			}
			else
			{
				echo "<input type='hidden' name='TIPO_GARANTIA' value='singarantia'>";
				if($S->no_garantia) echo "<input type='hidden' name='franquicia' value='10'>";
				elseif($S->tipogarantia==1) echo "<input type='hidden' name='franquicia' value='11'>";
			}
			echo"<br><br><br><center>
				<input type='button' name='seguir' id='seguir' value=' C O N T I N U A R ' class='button green large' style='width:400px;visibility:hidden;' onclick=\"validacion_general_campos();\"></center>
				<input type='hidden' name='Acc' value='grabar_informacion_garantia'><input type='hidden' name='siniestro' value='$P->id'>
				<input type='hidden' name='valor_congelamiento' value='$A->garantia'>
				<input type='hidden' name='valor_noreembolsable' value='$Valor_noreembolsable'>
			</form>
			<iframe name='Oculto_garantia' id='Oculto_garantia' style='display:none' width='1' height='1'></iframe>
			<br><br><br>
			";
		}
	}
	else
	{
		echo "<h4>No se encuentra la placa $placa con el tel?fono $telefono en la base de datos.</h4><input type='button' class='button green large' name='volver' id='volver' value='REGRESAR' onclick='regresar();'>";
	}
	echo "</body>";
}

function grabar_informacion_garantia()
{
	include('inc/gpos.php');
	$nuevoc=q("insert ignore into cliente (identificacion) values ('$identificacion')"); // inserta en la tabla de clientes el nuevo cliente
	graba_bitacora_garantiaservicio('cliente','A',$nuevoc,'Adiciona Registro');
	$idcli=qo1("select id from cliente where identificacion='$identificacion'");
	q("update cliente set nombre='$nombre',apellido='$apellido',lugar_expdoc='$lugar_expdoc',tipo_id='$tipo_id',sexo='$sexo',pais='$pais',ciudad='$ciudad',
		direccion='$direccion',telefono_oficina='$telefono_oficina',telefono_casa='$telefono_casa',celular='$celular',email_e='$email_e' where identificacion='$identificacion'");
	graba_bitacora_garantiaservicio('cliente','M',$idcli,'Actualizaci?n de datos del cliente via formulario web publico');
	$Hoy=date('Y-m-d H:i:s');
	if($TIPO_GARANTIA=='noreembolsable')
	{
		$Nid=q("insert into sin_autor (siniestro,nombre,identificacion,fecha_consignacion,numero_consignacion,franquicia,
			fecha_solicitud,solicitado_por,estado,valor,observaciones,email,devol_cuenta_banco,devol_tipo_cuenta,devol_banco,devol_ncuenta,identificacion_devol,formulario_web)
			values ('$siniestro','$nombre $apellido','$identificacion','$fecha_consignacion','$numero_comprobante',11,
			'$Hoy','$apellido $nombre','E','$valor_noreembolsable',\"$observaciones\",'$email_e','$devol_cuenta_banco','$devol_tipo_cuenta','$devol_banco','$devol_ncuenta','$identificacion_devol',1)");
			graba_bitacora_garantiaservicio('sin_autor','A',$Nid,'Adiciona Registro - Solicita Autorizacion via formulario web',$identificacion,$nombre,$apellido);
		$directorio='garantia';$Id=$Nid;$C='consignacion_f';$T='sin_autor';
		if(!$directorio) die('El directorio esta vacio');
		if(!is_dir($directorio)) { mkdir($directorio); 	chmod($directorio, 0777); }
		$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
		if(!is_dir($directorio.'/'.$Subdirectorio)) { mkdir($directorio.'/'.$Subdirectorio); chmod($directorio.'/'.$Subdirectorio, 0777);}
		if(!is_dir($directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($directorio.'/'.$Subdirectorio.'/'.$Id); chmod($directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}
		define("NAMETHUMB", "/tmp/thumbtemp");
		$mimetypes = array("image/jpeg", "image/pjpeg", "image/gif", "image/png","application/pdf");
		$name = $_FILES["comprobante_consignacion"]["name"];
		$type = $_FILES["comprobante_consignacion"]["type"];
		$tmp_name = $_FILES["comprobante_consignacion"]["tmp_name"];
		$size = $_FILES["comprobante_consignacion"]["size"];
		if(!in_array($type, $mimetypes))
		{
			echo "<script language='javascript'>
				function carga()
				{
					history.back();alert('no seleccion? una imagen v?lida ".$type." ');
				}
				</script>
				<body onload='carga()'></body>";
			die();
		}
		$Caracteristicas_imagen = getimagesize($tmp_name);
		$File_destino=$directorio.'/'.$Subdirectorio.'/'.$Id.'/'.$C.'_'.strtolower(str_replace(' ','_',$name));
		if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die('error en copy file'); }
		$Sub_Contenido=substr($File_destino,strrpos($File_destino,'/')+1);$Sub_Tumb='tumb_'.$Sub_Contenido;$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$File_destino);
		if(!file_exists($Tumb) && file_exists($File_destino))
		{
			if(strpos(strtolower($Sub_Contenido),'.jpg')) picresize($File_destino,TUMB_SIZE,'jpg',$Tumb);
			if(strpos(strtolower($Sub_Contenido),'.gif')) picresize($File_destino,TUMB_SIZE,'gif',$Tumb);
			if(strpos(strtolower($Sub_Contenido),'.png')) picresize($File_destino,TUMB_SIZE,'png',$Tumb);
		}
		if(strpos(strtolower($Sub_Contenido),'.pdf')) $Tumb=$File_destino;
		@unlink($tmp_name);
		q("update $T set $C='$File_destino' where id=$Id ");
		graba_bitacora_garantiaservicio('sin_autor','M',$Id,'Ingresa imagen de consignacion via formulario web publico',$identificacion,$nombre,$apellido);
		//$S=qo("select * from siniestro");
		/// LICENCIA CONDUCCION
		if($_FILES['img_licencia_conduccion_frente'])
		{
			$directorio='siniestro';$Id=$siniestro;$C='adicional1_f';$T='siniestro';
			if(!$directorio) die('El directorio esta vacio');
			if(!is_dir($directorio)) { mkdir($directorio); 	chmod($directorio, 0777); }
			$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
			if(!is_dir($directorio.'/'.$Subdirectorio)) { mkdir($directorio.'/'.$Subdirectorio); chmod($directorio.'/'.$Subdirectorio, 0777);}
			if(!is_dir($directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($directorio.'/'.$Subdirectorio.'/'.$Id); chmod($directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}
			define("NAMETHUMB", "/tmp/thumbtemp");
			$mimetypes = array("image/jpeg", "image/pjpeg", "image/gif", "image/png","application/pdf");
			$name = $_FILES["img_licencia_conduccion_frente"]["name"];
			$type = $_FILES["img_licencia_conduccion_frente"]["type"];
			$tmp_name = $_FILES["img_licencia_conduccion_frente"]["tmp_name"];
			$size = $_FILES["img_licencia_conduccion_frente"]["size"];
			if(!in_array($type, $mimetypes))
			{
				echo "<script language='javascript'>function carga(){history.back();alert('no seleccion? una imagen v?lida ".$type." ');}</script><body onload='carga()'></body>";die();
			}
			$Caracteristicas_imagen = getimagesize($tmp_name);
			$File_destino=$directorio.'/'.$Subdirectorio.'/'.$Id.'/'.$C.'_'.strtolower(str_replace(' ','_',$name));
			if(is_file($File_destino)) @unlink($File_destino);
			if (!@copy($tmp_name, $File_destino)) { die('error en copy file'); }
			$Sub_Contenido=substr($File_destino,strrpos($File_destino,'/')+1);$Sub_Tumb='tumb_'.$Sub_Contenido;$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$File_destino);
			if(!file_exists($Tumb) && file_exists($File_destino))
			{
				if(strpos(strtolower($Sub_Contenido),'.jpg')) picresize($File_destino,TUMB_SIZE,'jpg',$Tumb);
				if(strpos(strtolower($Sub_Contenido),'.gif')) picresize($File_destino,TUMB_SIZE,'gif',$Tumb);
				if(strpos(strtolower($Sub_Contenido),'.png')) picresize($File_destino,TUMB_SIZE,'png',$Tumb);
			}
			if(strpos(strtolower($Sub_Contenido),'.pdf')) $Tumb=$File_destino;
			@unlink($tmp_name);
			q("update $T set $C='$File_destino' where id=$Id ");
			graba_bitacora_garantiaservicio('siniestro','M',$Id,'Ingresa imagen de licencia-frente via formulario web publico',$identificacion,$nombre,$apellido);
		}
		if($_FILES['img_licencia_conduccion_reverso'])
		{
			$directorio='siniestro';$Id=$siniestro;$C='adicional2_f';$T='siniestro';
			if(!$directorio) die('El directorio esta vacio');
			if(!is_dir($directorio)) { mkdir($directorio); 	chmod($directorio, 0777); }
			$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
			if(!is_dir($directorio.'/'.$Subdirectorio)) { mkdir($directorio.'/'.$Subdirectorio); chmod($directorio.'/'.$Subdirectorio, 0777);}
			if(!is_dir($directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($directorio.'/'.$Subdirectorio.'/'.$Id); chmod($directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}
			define("NAMETHUMB", "/tmp/thumbtemp");
			$mimetypes = array("image/jpeg", "image/pjpeg", "image/gif", "image/png","application/pdf");
			$name = $_FILES["img_licencia_conduccion_reverso"]["name"];
			$type = $_FILES["img_licencia_conduccion_reverso"]["type"];
			$tmp_name = $_FILES["img_licencia_conduccion_reverso"]["tmp_name"];
			$size = $_FILES["img_licencia_conduccion_reverso"]["size"];
			if(!in_array($type, $mimetypes))
			{
				echo "<script language='javascript'>function carga(){history.back();alert('no seleccion? una imagen v?lida ".$type." ');}</script><body onload='carga()'></body>";die();
			}
			$Caracteristicas_imagen = getimagesize($tmp_name);
			$File_destino=$directorio.'/'.$Subdirectorio.'/'.$Id.'/'.$C.'_'.strtolower(str_replace(' ','_',$name));
			if(is_file($File_destino)) @unlink($File_destino);
			if (!@copy($tmp_name, $File_destino)) { die('error en copy file'); }
			$Sub_Contenido=substr($File_destino,strrpos($File_destino,'/')+1);$Sub_Tumb='tumb_'.$Sub_Contenido;$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$File_destino);
			if(!file_exists($Tumb) && file_exists($File_destino))
			{
				if(strpos(strtolower($Sub_Contenido),'.jpg')) picresize($File_destino,TUMB_SIZE,'jpg',$Tumb);
				if(strpos(strtolower($Sub_Contenido),'.gif')) picresize($File_destino,TUMB_SIZE,'gif',$Tumb);
				if(strpos(strtolower($Sub_Contenido),'.png')) picresize($File_destino,TUMB_SIZE,'png',$Tumb);
			}
			if(strpos(strtolower($Sub_Contenido),'.pdf')) $Tumb=$File_destino;
			@unlink($tmp_name);
			q("update $T set $C='$File_destino' where id=$Id ");
			graba_bitacora_garantiaservicio('siniestro','M',$Id,'Ingresa imagen de licencia-reverso via formulario web publico',$identificacion,$nombre,$apellido);
		}
		///// CEDULA
		if($_FILES['img_cedula_ciudadania_frente'])
		{
			$directorio='siniestro';$Id=$siniestro;$C='img_cedula_f';$T='siniestro';
			if(!$directorio) die('El directorio esta vacio');
			if(!is_dir($directorio)) { mkdir($directorio); 	chmod($directorio, 0777); }
			$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
			if(!is_dir($directorio.'/'.$Subdirectorio)) { mkdir($directorio.'/'.$Subdirectorio); chmod($directorio.'/'.$Subdirectorio, 0777);}
			if(!is_dir($directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($directorio.'/'.$Subdirectorio.'/'.$Id); chmod($directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}
			define("NAMETHUMB", "/tmp/thumbtemp");
			$mimetypes = array("image/jpeg", "image/pjpeg", "image/gif", "image/png","application/pdf");
			$name = $_FILES["img_cedula_ciudadania_frente"]["name"];
			$type = $_FILES["img_cedula_ciudadania_frente"]["type"];
			$tmp_name = $_FILES["img_cedula_ciudadania_frente"]["tmp_name"];
			$size = $_FILES["img_cedula_ciudadania_frente"]["size"];
			if(!in_array($type, $mimetypes))
			{
				echo "<script language='javascript'>function carga(){history.back();alert('no seleccion? una imagen v?lida ".$type." ');}</script><body onload='carga()'></body>";die();
			}
			$Caracteristicas_imagen = getimagesize($tmp_name);
			$File_destino=$directorio.'/'.$Subdirectorio.'/'.$Id.'/'.$C.'_'.strtolower(str_replace(' ','_',$name));
			if(is_file($File_destino)) @unlink($File_destino);
			if (!@copy($tmp_name, $File_destino)) { die('error en copy file'); }
			$Sub_Contenido=substr($File_destino,strrpos($File_destino,'/')+1);$Sub_Tumb='tumb_'.$Sub_Contenido;$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$File_destino);
			if(!file_exists($Tumb) && file_exists($File_destino))
			{
				if(strpos(strtolower($Sub_Contenido),'.jpg')) picresize($File_destino,TUMB_SIZE,'jpg',$Tumb);
				if(strpos(strtolower($Sub_Contenido),'.gif')) picresize($File_destino,TUMB_SIZE,'gif',$Tumb);
				if(strpos(strtolower($Sub_Contenido),'.png')) picresize($File_destino,TUMB_SIZE,'png',$Tumb);
			}
			if(strpos(strtolower($Sub_Contenido),'.pdf')) $Tumb=$File_destino;
			@unlink($tmp_name);
			q("update $T set $C='$File_destino' where id=$Id ");
			graba_bitacora_garantiaservicio('siniestro','M',$Id,'Ingresa imagen de cedula-frente via formulario web publico',$identificacion,$nombre,$apellido);
		}
		if($_FILES['img_cedula_ciudadania_reverso'])
		{
			$directorio='siniestro';$Id=$siniestro;$C='img_pase_f';$T='siniestro';
			if(!$directorio) die('El directorio esta vacio');
			if(!is_dir($directorio)) { mkdir($directorio); 	chmod($directorio, 0777); }
			$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
			if(!is_dir($directorio.'/'.$Subdirectorio)) { mkdir($directorio.'/'.$Subdirectorio); chmod($directorio.'/'.$Subdirectorio, 0777);}
			if(!is_dir($directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($directorio.'/'.$Subdirectorio.'/'.$Id); chmod($directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}
			define("NAMETHUMB", "/tmp/thumbtemp");
			$mimetypes = array("image/jpeg", "image/pjpeg", "image/gif", "image/png","application/pdf");
			$name = $_FILES["img_cedula_ciudadania_reverso"]["name"];
			$type = $_FILES["img_cedula_ciudadania_reverso"]["type"];
			$tmp_name = $_FILES["img_cedula_ciudadania_reverso"]["tmp_name"];
			$size = $_FILES["img_cedula_ciudadania_reverso"]["size"];
			if(!in_array($type, $mimetypes))
			{
				echo "<script language='javascript'>function carga(){history.back();alert('no seleccion? una imagen v?lida ".$type." ');}</script><body onload='carga()'></body>";die();
			}
			$Caracteristicas_imagen = getimagesize($tmp_name);
			$File_destino=$directorio.'/'.$Subdirectorio.'/'.$Id.'/'.$C.'_'.strtolower(str_replace(' ','_',$name));
			if(is_file($File_destino)) @unlink($File_destino);
			if (!@copy($tmp_name, $File_destino)) { die('error en copy file'); }
			$Sub_Contenido=substr($File_destino,strrpos($File_destino,'/')+1);$Sub_Tumb='tumb_'.$Sub_Contenido;$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$File_destino);
			if(!file_exists($Tumb) && file_exists($File_destino))
			{
				if(strpos(strtolower($Sub_Contenido),'.jpg')) picresize($File_destino,TUMB_SIZE,'jpg',$Tumb);
				if(strpos(strtolower($Sub_Contenido),'.gif')) picresize($File_destino,TUMB_SIZE,'gif',$Tumb);
				if(strpos(strtolower($Sub_Contenido),'.png')) picresize($File_destino,TUMB_SIZE,'png',$Tumb);
			}
			if(strpos(strtolower($Sub_Contenido),'.pdf')) $Tumb=$File_destino;
			@unlink($tmp_name);
			q("update $T set $C='$File_destino' where id=$Id ");
			graba_bitacora_garantiaservicio('siniestro','M',$Id,'Ingresa imagen de cedula-reverso via formulario web publico',$identificacion,$nombre,$apellido);
		}

	}
	elseif($TIPO_GARANTIA=='tarjeta')
	{
		$Nid=q("insert into sin_autor (siniestro,nombre,identificacion,numero,franquicia,banco,vencimiento_mes,vencimiento_ano,codigo_seguridad,
			fecha_solicitud,solicitado_por,estado,valor,observaciones,email,devol_cuenta_banco,devol_tipo_cuenta,devol_banco,devol_ncuenta,identificacion_devol,formulario_web)
			values ('$siniestro','$nombre $apellido','$identificacion','$numero_tarjeta','$franquicia','$banco','$vencimiento_mes','$vencimiento_ano','$codigo_seguridad',
			'$Hoy','$apellido $nombre','E','$valor_congelamiento',\"$observaciones\",'$email_e','$devol_cuenta_banco','$devol_tipo_cuenta','$devol_banco','$devol_ncuenta','$identificacion_devol',1)");
			graba_bitacora_garantiaservicio('sin_autor','A',$Nid,'Adiciona Registro - Solicita Autorizacion via formulario web',$identificacion,$nombre,$apellido);
	}
	elseif($TIPO_GARANTIA=='efectivo')
	{
		$Nid=q("insert into sin_autor (siniestro,nombre,identificacion,fecha_consignacion,numero_consignacion,franquicia,
			fecha_solicitud,solicitado_por,estado,valor,observaciones,email,devol_cuenta_banco,devol_tipo_cuenta,devol_banco,devol_ncuenta,identificacion_devol,formulario_web)
			values ('$siniestro','$nombre $apellido','$identificacion','$fecha_consignacion','$numero_comprobante',6,
			'$Hoy','$apellido $nombre','E','$valor_congelamiento',\"$observaciones\",'$email_e','$devol_cuenta_banco','$devol_tipo_cuenta','$devol_banco','$devol_ncuenta','$identificacion_devol',1)");
		graba_bitacora_garantiaservicio('sin_autor','A',$Nid,'Adiciona Registro - Solicita Autorizacion via formulario web',$identificacion,$nombre,$apellido);
		$directorio='garantia';$Id=$Nid;$C='consignacion_f';$T='sin_autor';
		if(!$directorio) die('El directorio esta vacio');
		if(!is_dir($directorio)) { mkdir($directorio); 	chmod($directorio, 0777); }
		$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
		if(!is_dir($directorio.'/'.$Subdirectorio)) { mkdir($directorio.'/'.$Subdirectorio); chmod($directorio.'/'.$Subdirectorio, 0777);}
		if(!is_dir($directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($directorio.'/'.$Subdirectorio.'/'.$Id); chmod($directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}
		define("NAMETHUMB", "/tmp/thumbtemp");
		$mimetypes = array("image/jpeg", "image/pjpeg", "image/gif", "image/png","application/pdf");
		$name = $_FILES["comprobante_consignacion1"]["name"];
		$type = $_FILES["comprobante_consignacion1"]["type"];
		$tmp_name = $_FILES["comprobante_consignacion1"]["tmp_name"];
		$size = $_FILES["comprobante_consignacion1"]["size"];
		if(!in_array($type, $mimetypes))
		{
			echo "<script language='javascript'>
				function carga()
				{
					history.back();alert('no seleccion? una imagen v?lida ".$type." ');
				}
				</script>
				<body onload='carga()'></body>";
			die();
		}
		$Caracteristicas_imagen = getimagesize($tmp_name);
		$File_destino=$directorio.'/'.$Subdirectorio.'/'.$Id.'/'.$C.'_'.strtolower(str_replace(' ','_',$name));
		if(is_file($File_destino)) @unlink($File_destino);
		if (!@copy($tmp_name, $File_destino)) { die('error en copy file'); }
		$Sub_Contenido=substr($File_destino,strrpos($File_destino,'/')+1);$Sub_Tumb='tumb_'.$Sub_Contenido;$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$File_destino);
		if(!file_exists($Tumb) && file_exists($File_destino))
		{
			if(strpos(strtolower($Sub_Contenido),'.jpg')) picresize($File_destino,TUMB_SIZE,'jpg',$Tumb);
			if(strpos(strtolower($Sub_Contenido),'.gif')) picresize($File_destino,TUMB_SIZE,'gif',$Tumb);
			if(strpos(strtolower($Sub_Contenido),'.png')) picresize($File_destino,TUMB_SIZE,'png',$Tumb);
		}
		if(strpos(strtolower($Sub_Contenido),'.pdf')) $Tumb=$File_destino;
		@unlink($tmp_name);
		q("update $T set $C='$File_destino' where id=$Id ");
		graba_bitacora_garantiaservicio('sin_autor','M',$Id,'Ingresa imagen de consignacion via formulario web publico',$identificacion,$nombre,$apellido);
	}

	html('CAPTURA INFORMACION DE GARANTIA');
	echo "
		<meta charset='iso-8859-1'>
		<meta http-equiv='X-UA-Compatible' content='IE=edge'>
		<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'>
	<style type='text/css'>
	<!--
		body {font-size:18px;}
		td {font-size:18px;}
		select {font-size:18px;}
		textarea {font-size:18px;}
		input {font-size:20px;}
	-->
	</style>
	<body><img src='img/LOGO_AOA_200.png'><h3>CAPTURA INFORMACION DE GARANTIA</h3>
		Su informaci?n ha quedado registrada en nuestra base de datos. <br>
		Gracias por cumplir con este paso, su servicio tendr? un ahorro de tiempo de aproximadamente 30 minutos en la cita de entrega del veh?culo. <br>
		<br>
		<input type='button' name='regresar' id='regresar' class='button green large' style='width:400px' value=' REGRESAR AL PORTAL DE AOA ' onclick=\"window.open('http://www.aoacolombia.com','_self');\">
		</body></html>";
}

function graba_bitacora_garantiaservicio($Nombre_tabla='',$Accion='',$Registro=0,$Cambios='',$identificacion='',$nombre='',$apellido='')
{
	q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."',
			'".date('s')."','$identificacion','$nombre $apellido','$Nombre_tabla','$Accion','$Registro','".$_SERVER['REMOTE_ADDR']."','$Cambios')");
}



















?>