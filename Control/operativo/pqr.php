<?php
include('inc/funciones_.php');

$Ahora=date('Y-m-d H:i:s');

if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}
captura();

function captura()
{
  global $Ahora;
  html('AOA COLOMBIA S.A. - REGISTRO DE PQR');
  echo "
  <script language='javascript'>
  function valida_solicitud()
  {
    with(document.forma)
    {
		if(!alltrim(cliente.value)) { alert('Debe digitar nombres y apellidos.'); cliente.style.backgroundColor='ffffcc'; cliente.focus(); return false;}
		if(!alltrim(direccion.value) && !alltrim(telefono.value) && !alltrim(celular.value) && !alltrim(email_e.value)) 
			{ alert('Debe digitar al menos una dirección, teléfono, celular o correo electrónico para contactarnos con usted.'); 
		direccion.style.backgroundColor='ffffcc';telefono.style.backgroundColor='ffffcc'; celular.style.backgroundColor='ffffcc'; email_e.style.backgroundColor='ffffcc'; direccion.focus(); return false;}
		if(!alltrim(placa.value)) { alert('Debe digitar la placa de su vehículo.'); placa.style.backgroundColor='ffffcc'; placa.focus();return false;}
		if(!aseguradora.value) { alert('Debe seleccionar la Aseguradora.'); aseguradora.style.backgroundColor='ffffcc'; return false;}
		if(!oficina.value) { alert('Debe seleccionar la oficina.'); oficina.style.backgroundColor='ffffcc'; return false;}
		if(!tipo.value) { alert('Debe seleccionar el tipo de solicitud.'); tipo.style.backgroundColor='ffffcc';return false;}
		if(!alltrim(descripcion.value)) { alert('Debe digitar la descripción de su petición, queja, reclamo o suerencia.'); descripcion.style.backgroundColor='ffffcc'; descripcion.focus();return false;}
		enviar.disabled=true;
		submit();
    }
  }
  function cerrar_fpqr()  {	window.close();void(null);  }
   </script>
  <body>
  <center><img src='img/PQR_AOA_200.png' border='0' height='100px'></center>
  <h3 align='center'>CAPTURA DE PETICION - QUEJA - RECLAMO - SUGERENCIA</H3>
  <form action='pqr.php' method='POST' target='pqr_oculto' name='forma' id='forma'>
	<table align='center' bgcolor='eeeeee'>
    <tr><td align='right'>Nombres completos:</td><td><input type='text' name='cliente' id='cliente' value='' size='70' maxlength='100' onblur='this.value=this.value.toUpperCase();' alt='Nombres y Apellidos' title='Nombres y Apellidos'></td><tr>
	<tr><td align='right'>Dirección:</td><td><input type='text' name='direccion' id='direccion' value='' size='70' maxlength='100'></td></tr>
	<tr><td align='right'>Teléfono:</td><td><input type='text' name='telefono' id='telefono' value='' size='20' maxlength='20'></td></tr>
	<tr><td align='right'>Celular:</td><td><input type='text' name='celular' id='celular' value='' size='20' maxlength='20'></td></tr>
	<tr><td align='right'>Correo electrónico:</td><td><input type='text' name='email_e' id='email_e' value='' size='70' maxlength='100'></td></tr>
	<tr><td align='right'>Placa Vehículo:</td><td><input type='text' name='placa' id='placa' value='' size='10' maxlength='8' onblur='this.value=this.value.toUpperCase();' ></td></tr>
	<tr><td align='right'>Aseguradora:</td><td>".menu1("aseguradora","select id,nombre from pqr_aseguradora ",0,1,"font-size:12px;")."</td></tr>
    <tr><td align='right'>Ciudad:</td><td> ".menu1("oficina","select id,nombre from oficina",0,1,"font-size:12px;")."</td></tr>
	<tr><td align='right'>Tipo Solicitud:</td><td> ".menu1("tipo","select id,nombre from pqr_tipo",0,1,"font-size:12px;"," ")."</td></tr>
    <tr><td align='right'>Fecha y Hora:</td><td> <input type='text' name='fecha' value='$Ahora' size=20 readonly></td></tr>
    <tr><td align='right'>Descripcion:</td><td><textarea name='descripcion' rows=4 cols=80 style='font-size:12px'></textarea></tr></td>
    <input type='hidden' name='Acc' value='grabar_solicitud'>
	<input type='hidden' name='registrado_por' value='Portal web AOA'><input type='hidden' name='estado' value='1'>
    <tr><td colspan='2' align='center'><input type='button' name='enviar' id='enviar' value=' Enviar PQR '  style='font-family:arial;font-size:18px;font-weight:bold; width:400px;' onclick='valida_solicitud()'></tr><td>
	</table>
  </form> 
  <iframe name='pqr_oculto' id='pqr_oculto' width=1 height=1 style='visibility:hidden'></iframe>
  <script language='javascript'>centrar(800,600);</script>
  </body>";
}

function grabar_solicitud()
{
  global $cliente,$tipo,$fecha,$descripcion,$registrado_por,$estado,$oficina,$direccion,$telefono,$celular,$email_e,$placa,$aseguradora;
  $Id_nuevo=q("insert into pqr_solicitud (cliente,tipo_solicitud,fecha,descripcion,registrado_por,estado,oficina,direccion,telefono,celular,email_e,placa,aseguradora) values 
  ('$cliente','$tipo','$fecha','$descripcion','$registrado_por','$estado','$oficina','$direccion','$telefono','$celular','$email_e','$placa','$aseguradora')");
  $Id_nuevo=str_pad($Id_nuevo,5,'0',STR_PAD_LEFT);
  echo "<body><script language='javascript'>alert('Solicitud PQR $Id_nuevo grabada satisfactoriamente');parent.cerrar_fpqr();</script></body>";
}

?>