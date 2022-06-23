<?php

/**
 *  Facturacion AOA
 *
 * @version $Id$
 * @copyright 2010
 */
 
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once('inc/funciones_.php');
sesion();

//print_r($_SESSION);

  if($_SESSION["User"] != 1 )
  {	  
	//echo "Facturación inhabilitada";

	//exit; 
  }


include_once('zfunciones_facturacion.php');




//die('PROGRAMA EN MANTENIMIENTO');


$Hoyl = date('Y-m-d H:i:s');
$Hoy = date('Y-m-d');

if (!empty($Acc) && function_exists($Acc)) { eval($Acc . '();'); die(); }
factura_general();

function factura_general()
{
	include('inc/gpos.php');
	sesion();
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	$Nick = $_SESSION['Nick'];
	$D=qo("select * from factura where id=$id");
	if($D->siniestro)
	{
		$identificacion=qo1("select identificacion from cliente where id=$D->cliente");
		if($idCita=qo1("select id from cita_servicio where siniestro='$D->siniestro' and estado='C' "))
		{
			echo "<body><script language='javascript'>window.open('zfacturacion.php?Acc=generar_factura&identificacion=$identificacion&idCita=$idCita','_self');</script></body>";
			die();
		}
	}
	html("SISTEMA DE FACTURACION - AOA ($Nusuario)");
	$Conceptos=q("select * from concepto_fac");
	
	echo "<style>
			.loader {
				border: 16px solid #f3f3f3; /* Light grey */
				border-top: 16px solid #3498db; /* Blue */
				border-radius: 50%;
				width: 120px;
				height: 120px;
				animation: spin 2s linear infinite;
			}

			@keyframes spin {
				0% { transform: rotate(0deg); }
				100% { transform: rotate(360deg); }
			}
		</style>
		
		<div class='loader' ></div>";
		
	echo "<script language='javascript'>
			var prevent_leave = false;
			var Concepto=new Array();
			function Concepto_clase()
			{	this.rutina='';this.iva='';	}	";
	while($Con=mysql_fetch_object($Conceptos))
	{
		echo "Concepto[$Con->id]=new Concepto_clase();
					Concepto[$Con->id].rutina='$Con->rutina';Concepto[$Con->id].iva='$Con->porc_iva';
				";
	}
	echo "		
		
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelector('.loader').style.display = 'none';
		});
		
		window.onbeforeunload = function() {
			if(prevent_leave)
			{
				return 'some warning message';	
			}	
		    
		};
		
		function carga() {centrar(800,650);}
		function selecciona_factura(id)
		{
			window.open('zfacturacion.php?Acc=factura_general&id='+id,'_self');
		}
		function ingresar_al_detalle()
		{
			window.open('zfacturacion.php?Acc=detalle_factura&factura=$id&Aseguradora='+document.forma.aseguradora.value,'dfactura');
		}
		//function imprimir_factura()	{	modal('zfacturacion.php?Acc=imprimir_factura&id=$id',0,0,800,1000,'imp');}
		//function imprimir_factural()	{	modal('zfacturacion.php?Acc=imprimir_factural&id=$id',0,0,800,1000,'imp');}

		function aprobar_factura()
		{ 
			alert('Cuando se aprueba la factura se genera una factura electrónica debe esperar a que el sistema la genere para terminar el proceso');
			prevent_leave = true;
			console.log('Got process');if(confirm('Desea aprobar la factura? Nota: Esta opción es requerida para poder imprimirla y es irreversible.'))
			{ window.open('zfacturacion.php?Acc=aprobar_factura&consecutivo='+document.forma.consecutivo.value+'&idCita=$idCita','Oculto_facturacion');
				//window.open('zfacturacion.php?Acc=aprobar_factura&consecutivo='+document.forma.consecutivo.value+'&idCita=$idCita');
				document.querySelector('.loader').style.display = 'block';
				setTimeout('close_loader()', 8000);
			}
		}
			
		function close_loader(){
			document.querySelector('.loader').style.display = 'none';
		}	
		
		
		
		function grabar_datos_factura()
		{
			with(document.forma)
			{
				var co=consecutivo.value;
				var fe=fecha_emision.value;
				var fv=fecha_vencimiento.value;
				var cl=cliente.value;
				var ase=aseguradora.value;
			}
			window.open('zfacturacion.php?Acc=grabar_datos_factura&fe='+fe+'&fv='+fv+'&co='+co+'&cl='+cl+'&ase='+ase,'Oculto_facturacion');
		}
		/*function borrar_factura()
		{
			if(confirm('Desea borrar esta factura y todo su detalle?'))
			{
				window.open('zfacturacion.php?Acc=borrar_factura&consecutivo='+document.forma.consecutivo.value+'&idCita=$idCita','Oculto_facturacion');
			}
		}*/
			function grabar_garantia()
			{
				var idga=document.forma.Garantia.value;
				if(confirm('Desea utilizar la garantía para pagar la factura?'))
				{
					window.open('zfacturacion.php?Acc=aplicar_garantia&idfactura=$id&idgarantia='+idga,'Oculto_facturacion');
				}
			}
			
			
		

	</script>
	<body onload='carga()'>";

	echo "<h3>SISTEMA DE FACTURACION - AOA</H3>
	Facturas anteriores: ".menu1('IdAnt',"select id,concat(consecutivo,' [',fecha_emision,'] ',t_cliente(cliente)) from factura order by consecutivo desc",$id,1,'',
		" onchange='selecciona_factura(this.value)'");
	if($id)
	{
		if($F=qo("select * from factura where id=$id"))
		{
			echo "<form action='zfacturacion.php' method='post' target='_self' name='forma' id='forma'><br />
				<table border cellspacing='0' align='center'><tr><th>Cabecera de la Factura</th></tr>
				<tr><td>Numero de Factura: <b style='font-size:16;color:990000'>".str_pad($F->consecutivo,6,' ',STR_PAD_LEFT)."</b>
				<input type='hidden' name='consecutivo' id='consecutivo' value='$F->consecutivo'>
				</td></tr>
				<tr><td>Cliente: ".menu1("cliente","select id,concat(nombre,' ',apellido) as nom from cliente order by nom",$F->cliente);
			echo "</td></tr><tr><td>Aseguradora: ".menu1("aseguradora","select id,nombre from aseguradora",$F->aseguradora);
			echo "</td></tr>
			<td>Fecha de emisión: ".pinta_FC('forma','fecha_emision',$F->fecha_emision)." Fecha de vencimiento: ".
				pinta_FC('forma','fecha_vencimiento',$F->fecha_vencimiento);
			echo "</td></tr></table>
				<input type='button' id='ingdet' name='ingdet' onclick='ingresar_al_detalle()' value='Ingresar al detalle' style='font-weight:bold;'>
			&nbsp;&nbsp;<a style='cursor:pointer' onclick='grabar_datos_factura();' class='info'><img src='gifs/standar/aplicar.png' border='0' height='16'  align='absmiddle' >Aplicar<span style='width:100px'>Grabar los datos de la cabecera de la factura</span></a>
			&nbsp;&nbsp;<a style='cursor:pointer' onclick='aprobar_factura();' class='info'><img src='gifs/standar/permisos.png' border='0' height='16'  align='absmiddle' >Aprobar<span style='width:100px'>Aprobar la factura</span></a>
			&nbsp;&nbsp;<a style='cursor:pointer' onclick='imprimir_factura();' class='info'><img src='gifs/Print.png' border='0' height='16'  align='absmiddle' >Imprimirr<span style='width:100px'>Imprimir la Factura</span></a>
			&nbsp;&nbsp;<a style='cursor:pointer' onclick='imprimir_factural();' class='info'><img src='gifs/Print.png' border='0' height='16'  align='absmiddle' >Imprimirr<span style='width:100px'>Imprimir la Factura - Preimpreso</span></a>
				<br /><b>Cruzar esta Factura con la Garantía :  </b>  ".menu1("Garantia","select id,concat(nombre,' - ',fecha_solicitud,' - ',num_autorizacion,' - ',estado,' - $',valor) from sin_autor where siniestro=$F->siniestro and estado='A' ",0,1,'width:200px')."
			&nbsp;&nbsp;<a style='cursor:pointer' onclick='grabar_garantia();' class='info'><img src='gifs/standar/aplicar.png' border='0' height='16'  align='absmiddle' >Aplicar Garantía<span style='width:100px'>Pagar esta factura con la Garantía.</span></a>
			</form><center><iframe name='dfactura' id='dfactura' src='zfacturacion.php?Acc=detalle_factura&factura=$id&Aseguradora=$F->aseguradora'
				height='300' width='750' border='0' frameborder='no' scrolling='auto'></iframe></center>
			<iframe name='Oculto_facturacion' style='visibility:hidden' height='1' width='1' frameborder='no'></iframe>
			";
		}
	}
	echo "</body>";
}

function inserta_desde_cita()
{
	include('inc/gpos.php');
	sesion();
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	$Nick = $_SESSION['Nick'];
	html("SISTEMA DE FACTURACION - AOA ($Nusuario)");
	if($Cita=qo("select * from cita_servicio where id=$idCita"))
	{
		$Sin=qo("select * from siniestro where id=$Cita->siniestro");

		$Aseguradora=qo("select * from aseguradora where id=$Sin->aseguradora");
		$Flota=qo("select * from aseguradora where id=$Cita->flota");
		$Estado=qo("select * from estado_siniestro where id=$Sin->estado");
		$Ubicacion=qo("select * from ubicacion where id=$Sin->ubicacion");
		$Cantidad_dias=dias($Ubicacion->fecha_inicial,$Ubicacion->fecha_final);
		if($Cantidad_dias>8) $Dias_exceso=$Cantidad_dias-8; else $Dias_exceso=0;
		$Cantidad_kilometros=$Ubicacion->odometro_final-$Ubicacion->odometro_inicial;
		if($Cantidad_kilometros>$Aseguradora->limite_kilometraje) $Kilometros_exceso=$Cantidad_kilometros-$Aseguradora->limite_kilometraje; else $Kilometros_exceso=0;
		$D=qo("select * from cliente where identificacion='$Sin->asegurado_id'");
		$Ciudad=$D?$D->ciudad:($Sin->ciudad_original?$Sin->ciudad_original:($Sin->ciudad?$Sin->ciudad:''));
		$NCiudad=qo1("select concat(departamento,' - ',nombre) from ciudad where codigo='$Ciudad'");
		echo "
		<style type='text/css'>
		<!--
			body {background-color:#ffffff;}
		-->
		</style>
		<script language='javascript'>
			function busqueda_ciudad(Campo,Contenido)
			{
				modal('marcoindex.php?Acc=pide_ciudad&Campo='+Campo+'&Dato='+Contenido+'&Forma=forma',0,0,200,600,'PC');
			}
			function validar_nuevo_cliente()
			{
				with(document.forma)
				{
					
					if(!alltrim(email_e.value)) {alert('Es obligatorio tener un correo electrónico');email_e.style.backgroundColor='ffff55';email_e.value.focus(); return false;}					
					
					if(!alltrim(identificacion.value)) {
					alert('Debe digitar la identificación');
					identificacion.style.backgroundColor='ffff55';
					identificacion.focus(); 
					return false;
					}
					
					if(identificacion.value < 6){
						alert('El numero de identificación debe de tenre minimo 6 caracteres');
						identificacion.style.backgroundColor='ffff55';
						identificacion.focus(); 
						return false;
					}
					
					if(!alltrim(lugar_expdoc.value)) {alert('Debe digitar el lugar de expedición de la identificación');lugar_expdoc.style.backgroundColor='ffff55';lugar_expdoc.focus();return false;}
					if(!alltrim(nombre.value)) {alert('Debe digitar el nombre');nombre.style.backgroundColor='ffff55';nombre.focus();return false;}
					if(!alltrim(apellido.value)) {alert('Debe digitar el apellido');apellido.style.backgroundColor='ffff55';apellido.focus();return false;}
					if(!alltrim(direccion.value)) {alert('Debe digitar la dirección');direccion.style.backgroundColor='ffff55';direccion.focus();return false;}
					if(!alltrim(direccion.value)) {alert('Debe digitar la dirección');direccion.style.backgroundColor='ffff55';direccion.focus();return false;}
					if(!alltrim(telefono_casa.value)) {alert('Debe digitar un teléfono');telefono_casa.style.backgroundColor='ffff55';telefono_casa.focus();return false;}
					if(!alltrim(telefono_oficina.value)) {alert('Debe digitar un teléfono');telefono_oficina.style.backgroundColor='ffff55';telefono_oficina.focus();return false;}
					if(!alltrim(telefono_oficina.value)) {alert('Debe digitar un teléfono');telefono_oficina.style.backgroundColor='ffff55';telefono_oficina.focus();return false;}
				}
				document.forma.submit();
			}
			function identifica_tercero(dato)
			{
				window.open('zfacturacion.php?Acc=identifica_tercero&identificacion='+dato,'Oculto_facturacion');
			}
			function changeFunc(){
			var codigoVerificacionId = document.getElementById('codigoVerificacionId');
			var selector = document.getElementById('seleCode');
			var option = selector.options[selector.selectedIndex].value;
			var trPadre = document.getElementById('trPadre');
			
			
			if(option == 02){
			trPadre.style.display = 'contents';
			}else if(option == 01){
			trPadre.style.display = 'none';
			codigoVerificacionId.value = '';
			}
		}

		</script>
		<body><script language='javascript'>centrar(800,700);</script>
			<h3 align='center'>GENERACION DE FACTURA A PARTIR DE CITA DE SINIESTRO ".($VS==1?"<a onclick='parent.cerrar_facturacion();' ><img src='gifs/standar/Cancel.png'></a>":"")."</H3>
			<form action='zfacturacion.php' method='post' target='_self' name='forma' id='forma'>
			<table cellspacing=3><tr><td rowspan=3><img src='$Aseguradora->emblema_f'></td><td align='right'>Siniestro numero</td><td><b>$Sin->numero $Aseguradora->nombre</b></td>
			<td><a class='info' href='$Sin->img_cedula_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Cedula</span></a>
					<a class='info' href='$Sin->img_pase_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Pase</span></a>
					<a class='info' href='$Sin->img_contrato_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Contrato</span></a>
			</td></tr>
			<tr><td align='right'>Estado del Siniestro:</td><td><b>$Estado->nombre</b></td>
			<td><a class='info' href='$Sin->img_odo_salida_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Odometro Entrega</span></a>
					<a class='info' href='$Sin->img_inv_salida_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Acta de Entrega</span></a>
					<a class='info' href='$Sin->fotovh1_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Frontal Vehiculo</span></a>
					<a class='info' href='$Sin->fotovh2_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Izquierda Vehiculo</span></a>
					<a class='info' href='$Sin->fotovh3_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Derecha Vehiculo</span></a>
					<a class='info' href='$Sin->fotovh4_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Posterior Vehiculo</span></a>
			</td></tr>
			<tr><td align='right'>Tiempo del Servicio:</td><td><b>$Ubicacion->fecha_inicial - $Ubicacion->fecha_final Dias: $Cantidad_dias ".($Dias_exceso>0?" <font color='red'>Dias de exceso: $Dias_exceso</font>":"")."</b></td>
			<td><a class='info' href='$Sin->img_odo_entrada_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Odometro Devolución</span></a>
					<a class='info' href='$Sin->img_inv_entrada_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Acta de Entrega</span></a>
					<a class='info' href='$Sin->fotovh5_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Frontal Vehiculo</span></a>
					<a class='info' href='$Sin->fotovh6_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Izquierda Vehiculo</span></a>
					<a class='info' href='$Sin->fotovh7_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Derecha Vehiculo</span></a>
					<a class='info' href='$Sin->fotovh8_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Posterior Vehiculo</span></a>".
					($Sin->fotovh9?"<a class='info' href='$Sin->fotovh9_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Adicional</span></a>":"")."</td></tr>
			<tr><td align='right'>Kilometraje utilizado:</td><td colspan=2><b>$Ubicacion->odometro_inicial - $Ubicacion->odometro_final Kilómetros: $Cantidad_kilometros ".($Kilometros_exceso>0?" <font color='red'>kilómetros de exceso: $Kilometros_exceso":"")."</b></td></tr>
			<tr><td align='right'>Vehículo prestado:</td><td><b>$Cita->placa</b></td><td>Flota: <b>$Flota->nombre</b></td></tr>
			<tr><td align='right'>Tipo de identificación del Asegurado:</td><td colspan='2'>".menu1("tipo_id","select codigo,nombre from tipo_identificacion",'',$D->tipo_id)."</td></tr>";
		
		echo "
		
		<tr><td align='right'>Identificación del Asegurado:</td><td colspan='2'><input type='text' name='identificacion' value='$Sin->asegurado_id' size=15 class='numero' onblur='identifica_tercero(this.value);'></td></tr>";
		
		if($D->tipo_persona == "01"){
		 $select1 = "selected"; 
		$style = "none";
		}else if($D->tipo_persona == "02"){
		$select2 = "selected";
		$style = "contents";
		}
		echo  "<tr><td align='right'>Selecciona si es juridica o natural</td><td><select id='seleCode' name='seleCode' onchange='changeFunc();'><option>Seleccione</option><option value='02' $select2>Juridica</option><option value='01' $select1>Natural</option></select></td></tr>";
		
		
		 echo	"<tr id='trPadre' style='display:$style'><td align='right'>Digito de verificaciòn</td><td><input type='text' style='width: 5%;' name='codigoVerificacion' id='codigoVerificacionId' maxlength='1' value='".($D->dv)."' id='codigoVerificacionId' ></td></tr>";	
		
		
		echo "	<tr><td align='right'>Lugar de expedición de la Identificación:</td><td colspan='2'><input type='text' name='lugar_expdoc' value='$D->lugar_expdoc' size=50></td></tr>
			<tr><td align='right'>Nombres del Asegurado:</td><td colspan='2'><input type='text' name='nombre' value='".($D?$D->nombre:$Sin->asegurado_nombre)."' size=50></td></tr>
			<tr><td align='right'>Apellidos del Asegurado:</td><td colspan='2'><input type='text' name='apellido' value='".($D?$D->apellido:$Sin->asegurado_nombre)."' size=50></td></tr>
			<tr><td align='right'>Pais del Asegurado:</td><td colspan='2'>".menu1("pais","select codigo,nombre from pais",'CO',$D->pais)."</td></tr>";
		echo "<tr><td align='right'>Ciudad:</td><td colspan='2'><input type='text' name='_ciudad' id='_ciudad' value='$NCiudad' size='70' onclick=\"busqueda_ciudad('ciudad','$Ciudad');\" readonly><input type='hidden' name=ciudad id=ciudad value='$Ciudad'></td></tr>
			<tr><td align='right'>Barrio</td><td colspan='2'><input type='text' name='barrio' value='$D->barrio' size='50'></td></tr>
			<tr><td align='right'>Dirección Domicilio</td><td colspan='2'><input type='text' name='direccion' value='$D->direccion' size='50'></td></tr>
			<tr><td align='right'>Teléfono Oficina</td><td colspan='2'><input type='text' name='telefono_oficina' value='".($D?$D->telefono_oficina:$Sin->declarante_tel_ofic)."' size='50'></td></tr>
			<tr><td align='right'>Teléfono Casa</td><td colspan='2'><input type='text' name='telefono_casa' value='".($D?$D->telefono_casa:$Sin->declarante_telefono)."' size='50'></td></tr>
			<tr><td align='right'>Celular</td><td colspan='2'><input type='text' name='celular' size='10' maxlength='10' value='".($D?$D->celular:$Sin->declarante_celular)."' class='numero'></td></tr>
			<tr><td align='right'>Dirección de correo electrónico</td><td colspan='2'><input type='text' name='email_e' value='".($D?$D->email_e:$Sin->declarante_email)."' size='50'></td></tr>
			<tr><td align='right'>Observaciones</td><td colspan='2'><textarea name='observaciones' rows='1' cols='60'>".($D?$D->observaciones:"")."</textarea></td></tr>
			<tr><td align='center' colspan=4><input type='button' id='Grabar' name='Grabar' value='".($D?"Actualizar Cliente":"Grabar Nuevo Cliente")."' style='font-size:14;font-weight:bold;width:400px;' onclick=\"validar_nuevo_cliente();\">
			</table>
			<input type='hidden' name='Acc' id='Acc' value='grabar_nuevo_cliente'><input type='hidden' name='idCita' id='idCita' value='$idCita'>
			<input type='hidden' name='VS' value='$VS'><input type='hidden' name='IDSF' value='$IDSF'>
			</form>
			<iframe name='Oculto_facturacion' id='Oculto_facturacion' style='display:none' width='1' height='1'></iframe>
			</body>";
	}
	else
	{

		echo "<body><script language='javascript'>
			centrar(400,300);
		</script><br /><br /><h3 align='center'><font color='red'>No se encuentra la información de la cita $idCita</font></h3></body>";
	}
}

function identifica_tercero()
{
	include('inc/gpos.php');
	if($Existe=qo("select * from cliente where identificacion=$identificacion"))
	{
		$NCiudad=qo1("select t_ciudad($Existe->ciudad)");
		echo "<body><script language='javascript'>
			with(parent.document.forma)
			{
				tipo_id.value='$Existe->tipo_id';
				lugar_expdoc.value='$Existe->lugar_expdoc';
				nombre.value='$Existe->nombre';
				apellido.value='$Existe->apellido';
				pais.value='$Existe->pais';
				ciudad.value='$Existe->ciudad';
				_ciudad.value='$NCiudad';
				barrio.value='$Existe->barrio';
				direccion.value='$Existe->direccion';
				telefono_oficina.value='$Existe->telefono_oficina';
				telefono_casa.value='$Existe->telefono_casa';
				celular.value='$Existe->celular';
				email_e.value='$Existe->email_e';
				observaciones.value='$Existe->observaciones';

			}
		</script>
		</body>";
	}
}

function grabar_nuevo_cliente()
{
	include('inc/gpos.php');
	sesion();
	$idSin=qo1("select siniestro from cita_servicio where id=$idCita");
	$nuevoc=q("insert ignore into cliente (identificacion) values ('$identificacion')");
	graba_bitacora('cliente','A',$nuevoc,'Adiciona Registro');
	q("update cliente set tipo_id='$tipo_id',lugar_expdoc='$lugar_expdoc',nombre='$nombre',apellido='$apellido',pais='$pais',ciudad='$ciudad',
		barrio='$barrio',direccion='$direccion',telefono_casa='$telefono_casa',telefono_oficina='$telefono_oficina',celular='$celular',email_e='$email_e',
		observaciones='$observaciones',dv='$codigoVerificacion', tipo_persona = '$seleCode' where identificacion='$identificacion' ");
	//q("update siniestro set asegurado_id='$identificacion' where id=$idSin");
	echo "<script language='javascript'>
		function carga()
		{
			alert('Informacion del cliente registrada satisfactoriamente');
			window.open('zfacturacion.php?Acc=generar_factura&idCita=$idCita&identificacion=$identificacion&VS=$VS&IDSF=$IDSF','_self');
		}
	</script>
	<body onload='carga()'></body>";
}

function generar_factura()
{
	include('inc/gpos.php');
	sesion();
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	$Nick = $_SESSION['Nick'];
	$Conceptos=q("select * from concepto_fac");
	html("SISTEMA DE FACTURACION - AOA (".$_SESSION['Nombre'].") ");
	if($Cita=qo("select * from cita_servicio where id=$idCita"))
	{
		$Sin=qo("select * from siniestro where id=$Cita->siniestro");
		$Aseguradora=qo("select * from aseguradora where id=$Sin->aseguradora");
		$Flota=qo("select * from aseguradora where id=$Cita->flota");
		$Estado=qo("select * from estado_siniestro where id=$Sin->estado");
		if($Ubicacion=qo("select * from ubicacion where id=$Sin->ubicacion"))
		{
			$Linea=qo1("select linea from vehiculo where id=$Ubicacion->vehiculo");
			$Cantidad_dias=dias($Ubicacion->fecha_inicial,$Ubicacion->fecha_final);
			if($Cantidad_dias>7) $Dias_exceso=$Cantidad_dias-7; else $Dias_exceso=0;
			$Cantidad_kilometros=$Ubicacion->odometro_final-$Ubicacion->odometro_inicial;
			if($Cantidad_kilometros>$Aseguradora->limite_kilometraje) $Kilometros_exceso=$Cantidad_kilometros-$Aseguradora->limite_kilometraje; else $Kilometros_exceso=0;
		}
		if($identificacion) $D=qo("select * from cliente where identificacion='$identificacion'"); else $D=qo("select * from cliente where identificacion='$Sin->asegurado_id'");
		$Ciudad=$D?$D->ciudad:($Sin->ciudad_original?$Sin->ciudad_original:($Sin->ciudad?$Sin->ciudad:''));
		$NCiudad=qo1("select concat(departamento,' - ',nombre) from ciudad where codigo='$Ciudad'");
		
		echo "<style>
			.loader {
				border: 16px solid #f3f3f3; /* Light grey */
				border-top: 16px solid #3498db; /* Blue */
				border-radius: 50%;
				width: 120px;
				height: 120px;
				animation: spin 2s linear infinite;
			}

			@keyframes spin {
				0% { transform: rotate(0deg); }
				100% { transform: rotate(360deg); }
			}
		</style>
		
		<div class='loader' >Cargando</div>
		<h1 class='loaderDos'>Por favor deje cargar la pagina</h1>
		";
		
		echo "
		<style type='text/css'>
		<!--
			body {background-color:#ffffff;}
		-->
		</style>
		
		<script language='javascript'>
			
			document.addEventListener('DOMContentLoaded', function() {
				document.querySelector('.loader').style.display = 'none';
				document.querySelector('.loaderDos').style.display = 'none';
			});
			
			window.onbeforeunload = function() {
				if(prevent_leave)
				{
					return 'some warning message';	
				}	
				
			};
		
			var Concepto=new Array();
			var Linea_vehiculo='$Linea';
			var prevent_leave = false;
			function Concepto_clase()
			{
				this.rutina='';
				this.iva='';
			}
			";
		while($Con=mysql_fetch_object($Conceptos))
		{
			echo "Concepto[$Con->id]=new Concepto_clase();
						Concepto[$Con->id].rutina='$Con->rutina';Concepto[$Con->id].iva='$Con->porc_iva';
					";
		}
		echo "
			function crear_consecutivo()
			{
				with(document.forma)
				{
					cf.style.visibility='hidden';
					var fe=fecha_emision.value;
					var fv=fecha_vencimiento.value;
					var cl=cliente.value;
				}
				window.open('zfacturacion.php?Acc=crear_consecutivo&fe='+fe+'&fv='+fv+'&cl='+cl+'&as=$Sin->aseguradora&sin=$Sin->id&IDSF=$IDSF','Oculto_facturacion');
			}
			
			
			function ingresar_al_detalle()
			{window.open('zfacturacion.php?Acc=detalle_factura&factura='+document.forma.idfactura.value+'&Aseguradora=$Sin->aseguradora','dfactura');}
			function traer_factura(id)
			{window.open('zfacturacion.php?Acc=traer_factura&id='+id,'Oculto_facturacion');	}
			function grabar_datos_factura()
			{
				with(document.forma)
				{
					var co=consecutivo.value;
					var fe=fecha_emision.value;
					var fv=fecha_vencimiento.value;
					var cl=cliente.value;
					var ase=aseguradora.value;
				}
				window.open('zfacturacion.php?Acc=grabar_datos_factura&fe='+fe+'&fv='+fv+'&co='+co+'&cl='+cl+'&ase='+ase,'Oculto_facturacion');
			}
			/*function borrar_factura()
			{
				if(confirm('Desea borrar esta factura y todo su detalle?'))
				{
					window.open('zfacturacion.php?Acc=borrar_factura&consecutivo='+document.forma.consecutivo.value+'&idCita=$idCita','Oculto_facturacion');
				}
			}*/

			function aprobar_factura()
			{ 
				alert('Cuando se aprueba la factura se genera una factura electrónica debe esperar a que el sistema la genere para terminar el proceso');
				prevent_leave = true;
				console.log('Got process');if(confirm('Desea aprobar la factura? Nota: Esta opción es requerida para poder imprimirla y es irreversible.'))
				{ window.open('zfacturacion.php?Acc=aprobar_factura&consecutivo='+document.forma.consecutivo.value+'&idCita=$idCita','Oculto_facturacion');
					//window.open('zfacturacion.php?Acc=aprobar_factura&consecutivo='+document.forma.consecutivo.value+'&idCita=$idCita');
					document.querySelector('.loader').style.display = 'block';
					document.querySelector('.loaderDos').style.display = 'block';
					setTimeout('close_loader()', 65000);
				}
			}
				
			function close_loader(){
				document.querySelector('.loader').style.display = 'none';
				document.querySelector('.loaderDos').style.display = 'none';
			}

			function imprimir_factura()	{	modal('zfacturacion.php?Acc=imprimir_factura&consecutivo='+document.forma.consecutivo.value,0,0,800,1000,'imp');}
			function imprimir_factural()	{	modal('zfacturacion.php?Acc=imprimir_factural&consecutivo='+document.forma.consecutivo.value,0,0,800,1000,'imp');}

			function genera_solicitud_autorizacion()
			{
				if(Number(document.forma.consecutivo.value)==0) {alert('Debe generar una factura nueva o seleccionar una factura anterior del cliente');return false;}
				if(!alltrim(document.forma.voucher.value)) {alert('Debe digitar el numero del voucher para realizar la solicitud de autorización de cupo');
					document.forma.voucher.style.backgroundColor='ffff00';document.forma.voucher.focus();return false;}
				with(document.forma)
				{
					window.open('zfacturacion.php?Acc=genera_solicitud_autorizacion&sin=$Sin->id&fac='+consecutivo.value+'&vou='+voucher.value,'Oculto_facturacion');
				}
			}
			function grabar_garantia()
			{
				var idfac=document.forma.idfactura.value;
				var idga=document.forma.Garantia.value;
				if(idfac)
				{
					if(confirm('Desea utilizar la garantía para pagar la factura?'))
					{
						window.open('zfacturacion.php?Acc=aplicar_garantia&idfactura='+idfac+'&idgarantia='+idga,'Oculto_facturacion');
					}
				}
				else
				{
					alert('No ha seleccionado una factura o no ha creado un nuevo consecutivo de factura');
				}
			}
		</script>
		<body><script language='javascript'>centrar(800,650);</script>
		<h3 align='center'>GENERACION DE FACTURA A PARTIR DE CITA DE SINIESTRO ".($VS==1?"<a onclick='parent.cerrar_facturacion();' ><img src='gifs/standar/Cancel.png'></a>":"")."</H3>
		<table cellspacing=3><tr><td rowspan=3><img src='$Aseguradora->emblema_f'></td><td align='right'>Siniestro numero</td><td><b>$Sin->numero $Aseguradora->nombre</b></td>
		<td>".($Sin->img_cedula_f?"<a class='info' href='$Sin->img_cedula_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Cedula</span></a>":"").
				($Sin->img_pase_f?"<a class='info' href='$Sin->img_pase_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Pase</span></a>":"").
				($Sin->img_contrato_f?"<a class='info' href='$Sin->img_contrato_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Contrato</span></a>":"").
		"</td></tr>
		<tr><td align='right'>Estado del Siniestro:</td><td><b>$Estado->nombre</b></td>
		<td>".($Sin->img_odo_salida_f?"<a class='info' href='$Sin->img_odo_salida_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Odometro Entrega</span></a>":"").
				($Sin->img_inv_salida_f?"<a class='info' href='$Sin->img_inv_salida_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Acta de Entrega</span></a>":"").
				($Sin->fotohv1_f?"<a class='info' href='$Sin->fotovh1_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Frontal Vehiculo</span></a>":"").
				($Sin->fotohv2_f?"<a class='info' href='$Sin->fotovh2_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Izquierda Vehiculo</span></a>":"").
				($Sin->fotohv3_f?"<a class='info' href='$Sin->fotovh3_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Derecha Vehiculo</span></a>":"").
				($Sin->fotohv4_f?"<a class='info' href='$Sin->fotovh4_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Posterior Vehiculo</span></a>":"").
		"</td></tr>
		<tr><td align='right'>Tiempo del Servicio:</td><td><b>$Ubicacion->fecha_inicial - $Ubicacion->fecha_final Dias: $Cantidad_dias ".($Dias_exceso>0?" <font color='red'>Dias de exceso: $Dias_exceso</font>":"")."</b></td>
		<td>".($Sin->img_odo_entrada_f?"<a class='info' href='$Sin->img_odo_entrada_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Odometro Devolución</span></a>":"").
				($Sin->img_inv_entrada_f?"<a class='info' href='$Sin->img_inv_entrada_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Acta de Entrega</span></a>":"").
				($Sin->fotohv5_f?"<a class='info' href='$Sin->fotovh5_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Frontal Vehiculo</span></a>":"").
				($Sin->fotohv6_f?"<a class='info' href='$Sin->fotovh6_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Izquierda Vehiculo</span></a>":"").
				($Sin->fotohv7_f?"<a class='info' href='$Sin->fotovh7_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Derecha Vehiculo</span></a>":"").
				($Sin->fotohv8_f?"<a class='info' href='$Sin->fotovh8_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Posterior Vehiculo</span></a>":"").
				($Sin->fotovh9?"<a class='info' href='$Sin->fotovh9_f' target='vimg'><img src='gifs/camara.png' border='0'><span>Imagen Adicional</span></a>":"")."</td></tr>
				<tr><td align='right'>Kilometraje utilizado:</td><td colspan=2><b>$Ubicacion->odometro_inicial - $Ubicacion->odometro_final Kilómetros: $Cantidad_kilometros ".($Kilometros_exceso>0?" <font color='red'>kilómetros de exceso: $Kilometros_exceso":"")."</b></td></tr>
				<tr><td align='right'>Vehículo prestado:</td><td><b>$Cita->placa</b></td><td>Flota: <b>$Flota->nombre</b></td></tr>
		</table>
		<form action='zfacturacion.php' method='post' target='Oculto_facturacion' name='forma' id='forma'>
			<table><tr><td align='right'>Consecutivo de Factura:</td><td colspan=3><input type='text' name='consecutivo' id='consecutivo' value='0' class='numero' size='10' readonly style='font-size:14;color:aa0000'>
				<input type='button' id='cf' name='cf' onclick='crear_consecutivo()' value='Crear Consecutivo' style='font-weight:bold'>
				<input type='button' id='ingdet' name='ingdet' onclick='ingresar_al_detalle()' value='Ingresar al detalle' style='font-weight:bold;visibility:hidden'>
				Facturas anteriores: ".menu1('fac_ant',"Select id,t_factura(id) from factura where cliente=$D->id order by consecutivo desc",0,1,"","onchange='traer_factura(this.value);' ");
		echo "</td></tr>
			<tr><td align='right'>Cliente:</td><td colspan='3'><input type='text' name='ncliente' id='ncliente' value='$D->apellido $D->nombre' size='80' readonly>
					<input type='hidden' name='cliente' value='$D->id'></td></tr>
			<tr><td align='right'>Fecha de emisión:</td><td><input type='text' name='fecha_emision' value='".date('Y-m-d')."' size='10' readonly > Fecha de vencimiento: ".pinta_FC('forma','fecha_vencimiento',date('Y-m-d'))."
			&nbsp;&nbsp;<a style='cursor:pointer' onclick='grabar_datos_factura();' class='info'><img src='gifs/standar/aplicar.png' border='0' height='16'  align='absmiddle' >Aplicar<span style='width:100px'>Grabar los datos de la cabecera de la factura</span></a>			&nbsp;&nbsp;<a style='cursor:pointer' onclick='aprobar_factura();' class='info'><img src='gifs/standar/permisos.png' border='0' height='16'  align='absmiddle' >Aprobar<span style='width:100px'>Aprobar la factura</span></a>
			<!--&nbsp;&nbsp;<a style='cursor:pointer' onclick='imprimir_factura();' class='info'><img src='gifs/print.png' border='0' height='16'  align='absmiddle' >Imprimirr<span style='width:100px'>Imprimir la Factura</span></a>-->
			<br /><b>Solicitar Nueva autorización : </b> Número de voucher: <input type='text' name='voucher' size=10 class='numero'>&nbsp;&nbsp;
			<a style='cursor:pointer' onclick='genera_solicitud_autorizacion();' class='info'><img src='img/solicita_autorizacion.png' border='0' height='16'  align='absmiddle' >Generar Autorización<span style='width:100px'>Solicitar nueva Autorización</span></a>
			<br /><b>Cruzar esta Factura con la Garantía :  </b>  ".menu1("Garantia","select s.id,concat(s.nombre,' - ',s.num_autorizacion,' - ',s.estado,' - $',s.valor,' ',f.nombre)
				from sin_autor s,franquisia_tarjeta f where s.siniestro=$Sin->id and s.estado='A' and s.franquicia=f.id ",0,1,'width:200px')."
			&nbsp;&nbsp;<a style='cursor:pointer' onclick='grabar_garantia();' class='info'><img src='gifs/standar/aplicar.png' border='0' height='16'  align='absmiddle' >Aplicar Garantía<span style='width:100px'>Pagar esta factura con la Garantía.</span></a>
			</td></tr>
			</table>
			<input type='hidden' name='idfactura' id='idfactura' value=''>
		</form>Detalle de la factura:<br>
		<center><iframe name='dfactura' id='dfactura' src='zfacturacion.php?Acc=detalle_factura&factura=0' height='200' width='750' border='0' frameborder='no' scrolling='auto'></iframe></center>
		<iframe name='Oculto_facturacion' style='visibility:hidden' height='1' width='1' frameborder='no'></iframe>
		Observaciones:<br />
		<textarea name='observaciones' id='observaciones' cols='120' rows='3'></textarea><br />
		<input type='button' value='Grabar observaciones' style='font-size:14' onclick=\"if(document.forma.idfactura.value>0) {
		window.open('zfacturacion.php?Acc=grabar_observaciones&id='+document.forma.idfactura.value+'&contenido='+document.getElementById('observaciones').value,'Oculto_facturacion');
		} else alert('No se ha creado o seleccionado un consecutivo de Factura.');\";>
		</body>";
	}
}

function grabar_observaciones()
{
	include('inc/gpos.php');
	q("update factura set observaciones=\"$contenido\" where id=$id");
	echo "<body><script language='javascript'>alert('Observaciones grabadas correctamente');</script>";
}

function generar_consecutivo_prefijo($resolucion_factura)
{
	$factura = qo("Select * from factura where consecutivo like  '%".$resolucion_factura->prefijo."%' order by id desc LIMIT 1 ");
	if($factura)
	{
		 $g_cons = str_ireplace($resolucion_factura->prefijo,"",$factura->consecutivo);
		 $g_cons += 1;		 
		 
		 
		 $g_cons =  $resolucion_factura->prefijo."".$g_cons; 
		 
		 
	}
	else
	{
		$g_cons = $resolucion_factura->prefijo."".$resolucion_factura->consecutivo_inicial;
	}
	return $g_cons;
}

function crear_consecutivo()
{
	
	
	include('inc/gpos.php');
	sesion();
	
	
	$resolucion_pre = qo("Select * from resolucion_factura order by fecha desc limit 1");
	
	if($resolucion_pre->prefijo != null)
	{
		//----- BEGIN nueva logica consecutivo con prefijos
		
		
		$Consecutivo = generar_consecutivo_prefijo($resolucion_pre);
		
		if($factura=q("insert into factura (consecutivo,fecha_emision,fecha_vencimiento,cliente,aseguradora,siniestro) values ('$Consecutivo','$fe','$fv','$cl','$as','$sin')"))
		{
			echo "<script language='javascript'>
				function carga()
				{
					with(parent.document.forma)
					{
						consecutivo.value='$Consecutivo';
						cf.style.visibility='hidden';
						ingdet.style.visibility='visible';
						idfactura.value=$factura;
					}
					window.open('zfacturacion.php?Acc=detalle_factura&factura=$factura&Aseguradora=$as','dfactura');
				}
			</script>
			<body onload='carga()'></body>";	
			
		}
		else
		{
			echo "<script language='javascript'>
				function carga()
				{
					alert('No se pudo crear la factura nueva');
				}
			</script>
			<body onload='carga()'></body>";
		}
		
		
	
		//----- END nueva logica consecutivo con prefijos
	}
	else
	{
			
		$Consecutivo=qo1("select consecutivo_aoa from cfg_factura where id=1")+1;
		q("update cfg_factura set consecutivo_aoa=$Consecutivo where id=1");
		//$Consecutivo=qo1("select max(consecutivo) from factura")+1;
		$NConsecutivo=str_pad($Consecutivo,6,' ',STR_PAD_LEFT);
		echo "<body>$Consecutivo";
		
		if($factura=q("insert into factura (consecutivo,fecha_emision,fecha_vencimiento,cliente,aseguradora,siniestro) values ('$Consecutivo','$fe','$fv','$cl','$as','$sin')"))
		{
			graba_bitacora('factura','A',$factura,'Adiciona factura');
			// Inserción automática de detalle cuando la factura se elabora desde la vista general de SINIESTRO
			IF($IDSF)
			{
				$Conceptos_a_facturar=explode(',',$IDSF);
				include('inc/link.php');
				$inserto_items=false;
				for($i=0;$i<=count($Conceptos_a_facturar);$i++)
				{
					if($Conceptos_a_facturar[$i]>0)
					{
						$id_solicitud=$Conceptos_a_facturar[$i];
						$Solicitud=qom("SELECT * FROM solicitud_factura WHERE id=$id_solicitud ",$LINK);
						$concepto=qom("select * from concepto_fac where id=$Solicitud->concepto",$LINK);
						$tarifa=qom("select * from tarifa where aseguradora='$as' and concepto='$Solicitud->concepto' ",$LINK);
						$neto=$Solicitud->cantidad*$tarifa->valor;
						$iva=round($neto*$concepto->porc_iva/100,0);
						$total=$neto+$iva;
						mysql_query("insert into facturad (factura,concepto,cantidad,unitario,iva,total,descripcion) values ('$factura','$concepto->id','$Solicitud->cantidad','$tarifa->valor','$iva','$total','$Solicitud->descripcion')",$LINK);
						$inserto_items=true;
					}
				}
				$Totales=qom("select sum(unitario*cantidad) as subtotal,sum(iva) as iva,sum(total) as total from facturad where factura='$factura' ",$LINK);
				mysql_query("update factura set subtotal='$Totales->subtotal',iva='$Totales->iva',total='$Totales->total' where id='$factura' ",$LINK);
				mysql_close($LINK);
			}

			// *-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-

			echo "<script language='javascript'>
				function carga()
				{
					with(parent.document.forma)
					{
						consecutivo.value='$NConsecutivo';
						cf.style.visibility='hidden';
						ingdet.style.visibility='visible';
						idfactura.value=$factura;
					}
					window.open('zfacturacion.php?Acc=detalle_factura&factura=$factura&Aseguradora=$as','dfactura');
				}
			</script>
			<body onload='carga()'></body>";
		}
		else
		{
			echo "<script language='javascript'>
				function carga()
				{
					alert('No se pudo crear la factura nueva');
				}
			</script>
			<body onload='carga()'></body>";
		}
	}

	
	
	
}

function traer_factura()
{
	include('inc/gpos.php');
	$D=qo("select * from factura where id=$id");
	$NConsecutivo=str_pad($D->consecutivo,6,' ',STR_PAD_LEFT);
	
	$iframe_Tag = '<iframe width="100%" height="100%" id="elec_frame"  src="https://app.aoacolombia.com/Control/operativo/zfacturacion.php?Acc=verificar_factura_electronica&id='.$D->id.'"  ></iframe>';
	
	echo "<script>
			console.log('no iframe');
			console.log(parent.document.getElementById('elec_frame'));
			elem = parent.document.getElementById('elec_frame');
			if(elem != null)
			{elem.parentNode.removeChild(elem);}
		</script>";
	
	if(!is_numeric($D->consecutivo) and $D->autorizadopor != null)
    {		
		echo "
			<script>
				
				function load_iframe()
				{
					let new_element = document.createElement('div');
					new_element.innerHTML = '$iframe_Tag';
					console.log(new_element);					
					
					parent.document.body.appendChild(new_element);
					
					console.log(parent.document);
					console.log(new_element);
					alert('Verifique el proceso de factura electrónica');
				}
			
				load_iframe();
				
			</script>
		";	
	}
	else{
		
		
	}
	
	echo "<script language='javascript'>
		function carga()
		{
			with(parent.document.forma)
			{
				consecutivo.value='$NConsecutivo';
				fecha_emision.value='$D->fecha_emision';
				fecha_vencimiento.value='$D->fecha_vencimiento';
				idfactura.value=$id;
				cf.style.visibility='hidden';
				ingdet.style.visibility='visible';
				Garantia.value='$D->garantia';
			}
		//	parent.document.getElementById('observaciones').value=\"\";
			window.open('zfacturacion.php?Acc=detalle_factura&factura=$id&Aseguradora=$D->aseguradora','dfactura');
		}
	</script>
	<body onload='carga()'></body>";
}

function detalle_factura()
{
	include('inc/gpos.php');
	$Fac=qo("select * from factura where id=$factura");
	html();
	if($factura)
	{
		echo "<script language='javascript'>
			function valida_concepto(concepto)
			{
				var Acc=parent.Concepto[concepto].rutina;
				if(Acc)
				{
					window.open('zfacturacion.php?Acc='+Acc+'&concepto='+concepto+'&Aseguradora=$Aseguradora&linea_vehiculo='+parent.Linea_vehiculo,'Oculto_detallefac');
				}
			}
			function liquida_item()
			{
				with(document.forma_detalle)
				{
					iva.value=Redondeo(Number(cantidad.value)*Number(unitario.value)*parent.Concepto[concepto.value].iva/100,0);
					total.value=Redondeo(Number(cantidad.value)*Number(unitario.value)+Number(iva.value),0);
					descripcion.focus();
					return true;
				}
			}
			function valida_item()
			{
				with(document.forma_detalle)
				{
					if(!concepto.value) {alert('No ha seleccionado un concepto');return false;}
					if(Number(cantidad.value)<=0) {alert('Debe digitar una cantidad valida');return false;}
					if(Number(unitario.value)<=0) {alert('Debe digitar un valor unitario valido'); return false;}
					submit();
				}
			}
			function borrar_item(id)
			{
				if(confirm('Desea borrar este item?'))
				{
					window.open('zfacturacion.php?Acc=borrar_item&id='+id,'Oculto_detallefac');
				}
			}
		</script>
		<body bgcolor='ffffdd' topmargin='0' leftmargin='0' rightmargin='0'>";

		echo "<form action='zfacturacion.php' method='post' target='_self' name='forma_detalle' id='forma_detalle'>
			<table bgcolor='eeeeee' width='100%'><tr>
					<th>Concepto</th>
					<th>Cantidad</th>
					<th>Unitario</th>
					<th>Iva</th>
					<th>Total</th>
					<th>Observaciones</th>
					</tr>";
		if($Detalle=q("select *,t_concepto_fac(concepto) as nconcepto from facturad where factura='$factura' "))
		{
			$Subtotal=$Iva=$Total=0;
			while($D=mysql_fetch_object($Detalle))
			{
				echo "<tr><td bgcolor='ffffff'>$D->nconcepto</td>
							<td bgcolor='ffffff' align='right'>".coma_formatd($D->cantidad,2)."</td>
							<td bgcolor='ffffff' align='right'>".coma_format($D->unitario)."</td>
							<td bgcolor='ffffff' align='right'>".coma_format($D->iva)."</td>
							<td bgcolor='ffffff' align='right'>".coma_format($D->total)."</td><td bgcolor='ffffff'>$D->descripcion</td>";
				if(!$Fac->autorizadopor) echo "<td bgcolor='ffffff' align='center'><img src='gifs/x.gif' border='0' style='cursor:pointer' onclick='borrar_item($D->id);'></td>";
				echo "</tr>";
				$Subtotal+=$D->cantidad*$D->unitario;
				$Iva+=$D->iva;
				$Total+=$D->total;
			}
			echo "<tr bgcolor='dddddd'><td><b>TOTALES</b></td>
							<td align='right' colspan=2><b>Subtotal: ".coma_formatd($Subtotal,2)."</b></td>
							<td align='right'><b>".coma_format($Iva)."</b></td>
							<td align='right'><b>".coma_format($Total)."</b></td><td></td><td></td>
							</tr>";
		}
		if(!$Fac->autorizadopor)
		{
			echo "<tr><td bgcolor='ffffff' align='center'>".menu1('concepto',"select concepto,t_concepto_fac(concepto) from tarifa where aseguradora=$Aseguradora and activa=1",0,1,' width:200px;',"onchange='valida_concepto(this.value);' ")."</td>";
			echo "<td bgcolor='ffffff' align='right'><input type='text' class='numero' name='cantidad' id='cantidad' value='' size='5' onblur='liquida_item();'></td>";
			echo "<td bgcolor='ffffff' align='right'><input type='text' class='numero' name='unitario' id='unitario' value='' size='8' onblur='liquida_item();'></td>";
			echo "<td bgcolor='ffffff' align='right'><input type='text' class='numero' name='iva' id='iva' value='' size='5' readonly></td>";
			echo "<td bgcolor='ffffff' align='right'><input type='text' class='numero' name='total' id='total' value='' size='10' readonly></td>";
			echo "<td bgcolor='ffffff' align='center'><textarea name='descripcion' id='descripcion' rows=2 cols=30 style='font-size:12px' maxlength='300'></textarea></td>";
			echo "</tr></table><center><input type='button' id='Grabar' name='Grabar' value='Grabar Item' style='font-weight:bold;' onclick='valida_item()'></center>
			<input type='hidden' name='Acc' id='Acc' value='grabar_nuevo_item'><input type='hidden' name='factura' id='factura' value='$factura'>";
		}
		echo "</form>";
		echo "<iframe name='Oculto_detallefac' id='Oculto_detalle_fac' style='visibility:hidden' frameborder='no' border='0' height='1' width='1'></iframe></body>";
	}
	else
	{
		echo "<body bgcolor='ffffdd'><h3>Debe generar un nuevo consecutivo para poder crear la factura</h3></body>";
	}
}

function trae_unitario()
{
	include('inc/gpos.php');
	if($Valor=qo1("select valor from tarifa where concepto='$concepto' and aseguradora=$Aseguradora"))
	{
		echo "<script language='javascript'>
			function carga()
			{
				parent.document.getElementById('unitario').value='$Valor';
			}
		</script>
		<body onload='carga()'></body>";
	}
	else
	{
		echo "<script language='javascript'>
			function carga()
			{
				parent.document.getElementById('unitario').value='';
			}
		</script>
		<body onload='carga()'></body>";
	}
}

function trae_unitario_gasolina()
{
	include('inc/gpos.php');
	if(!$linea_vehiculo) die("<body><script language='javascript'>alert('No encuentro linea de vehiculo');</script></body>");
	$Hoy=date('Y-m-d');
	$D=qo("select galones,rayas_medidor from linea_vehiculo where id=$linea_vehiculo");
	$precio_galon=qo1("select valor_galon from precio_gasolina where fecha_inicial<='$Hoy' order by fecha_inicial desc limit 1");
	$precio_raya=round(($D->galones/$D->rayas_medidor)*$precio_galon,0);
	echo "<script language='javascript'>
		function carga()
		{
			parent.document.getElementById('unitario').value='$precio_raya';
		}
	</script>
	<body onload='carga()'></body>";
}

function grabar_nuevo_item()
{
	include('inc/gpos.php');
	if($total>0)
	{
		//detalles previos
		
		$sql = "Select  c.porc_iva as iva_percent  from facturad as d inner join concepto_fac as c on d.concepto = c.id where factura = '$factura' ";
		
		//echo $sql;
		
		$result = q($sql);
		
		$rows = array();
		
		while($row = mysql_fetch_object($result))
		{
			array_push($rows,$row);
		}
		
		if(count($rows) > 0 )
		{
			/*$sql = "Select * from concepto_fac where id =  '$concepto' ";
			
			$concept_to_fact = qo($sql);
					
			foreach($rows as $row)
			{
				if($row->iva_percent != $concept_to_fact->porc_iva)
				{
					echo "<script>
					
						alert('No se pueden agregar items a la factura con diferentes tasas del iva ');
						
						parent.parent.ingresar_al_detalle();
					
					</script>"; 
					
					exit;
						
					//Innecesario con la ultima versión de facturación electrónica
				}					
			}*/
		
		}	
		
		
	
		
		$idn=q("insert into facturad (factura,concepto,cantidad,unitario,iva,total,descripcion) values ('$factura','$concepto','$cantidad','$unitario','$iva','$total','$descripcion')");
	
	}
	else{
		echo "<script language='javascript'>
			function carga()
			{
				alert('El total no puede ser 0 ');
			}
		</script>
		<body onload='carga()'></body>";
		exit;
	}
	
	$Total=qo("select sum(cantidad*unitario) as subtotal, sum(iva) as iva, sum(total) as total from facturad where factura='$factura' ");
	
	q("update factura set subtotal=$Total->subtotal, iva=$Total->iva, total=$Total->total where id='$factura'");
	
	echo "<script language='javascript'>
			function carga()
			{
				parent.parent.ingresar_al_detalle();
			}
		</script>
		<body onload='carga()'></body>";
}

function borrar_item()
{
	include('inc/gpos.php');
	$factura=qo1("select factura from facturad where id=$id");
	q("delete from facturad where id=$id");
	$Total=qo("select sum(cantidad*unitario) as subtotal, sum(iva) as iva, sum(total) as total from facturad where factura='$factura' ");
	q("update factura set subtotal=$Total->subtotal, iva=$Total->iva, total=$Total->total where id='$factura'");
	echo "<script language='javascript'>
		function carga()
		{
			parent.parent.ingresar_al_detalle();
		}
	</script>
	<body onload='carga()'></body>";
}

function grabar_datos_factura()
{
	include('inc/gpos.php');
	q("update factura set fecha_emision='$fe',fecha_vencimiento='$fv',cliente='$cl',aseguradora='$ase' where consecutivo=$co");
	echo "<script language='javascript'>
		function carga()
		{
			alert('Datos grabados satisfactoriamente');
		}
	</script>
	<body onload='carga()'></body>";
}

function borrar_factura()
{
	include('inc/gpos.php');
	if($id=qo1("select id from factura where consecutivo='$consecutivo'"))
	{
		q("delete from factura where id=$id");
		q("delete from facturad where factura=$id");
	}
	echo "<script language='javascript'>
		function carga()
		{
			parent.location='zfacturacion.php?Acc=generar_factura&idCita=$idCita';
		}
	</script>
	<body onload='carga()'></body>";
}

function imprimir_factural()
{
	include('inc/gpos.php');
	if($id) $D=qo("select * from factura where id=$id");
	elseif($consecutivo) $D=qo("select * from factura where consecutivo='$consecutivo'");
	if($D)
	{
		if(!$D->autorizadopor)
		{
			echo "<script language='javascript'>function carga() {alert('Esta factura no ha sido aprobada, debe aprobarla para imprimirla.');window.close();void(null);} </script><body onload='carga()'></body>";
			die(); 
		}
		$Sin=qo("select numero,ciudad from siniestro where id=$D->siniestro");
		$OrdenServicio=qo1("select left(nombre,2) from aseguradora where id=$D->aseguradora").'-'.$Sin->numero;
		$Ciudad_sin=qo1("select left(nombre,3) from ciudad where codigo='$Sin->ciudad' ");
		$Cliente=qo("select * from cliente where id=$D->cliente");
		if($D->impresion_resumida) $Qd="select (1) as cantidad, sum(cantidad*unitario) as unitario,(' ') as desripcion,t_concepto_fac($D->concepto_resumido) as nconcepto from facturad where factura=$D->id";
		else $Qd="select *,t_concepto_fac(concepto) as nconcepto from facturad where factura=$D->id";
		if($Det=q($Qd))
		{
			include('inc/pdf/fpdf.php');
			$P=new pdf('P','mm','letter');
			$P->AddFont("c128a","","c128a.php");
			$P->AliasNbPages();
			$P->setTitle("ORDEN DE SERVICIO");
			$P->setAuthor("TECNOLOGIA AOA www.aoacolombia.com sercasti@aoacolombia.com");
			$P->Numeracion=false;
			$P->SetAutoPageBreak(false);
			$P->setFillColor(230,230,230);
			//	$P->Header_texto='';
			//	$P->Header_alineacion='L';
			//	$P->Header_alto='8';
			$P->SetTopMargin('5');
			//	$P->Header_colores=array(0,0,0,255,255,255,50,50,100); # rgb texto, rgb fondo, rgb borde
			//	$P->Header_imagen='img/cnota_entrada.jpg';
			///	$P->Header_posicion_imagen=array(20,5,80,14);
			$P->AddPage('P');
//			$P->Image('../img/LOGO_AOA_200.jpg',20,5,60,24);
//			$P->setfont('Arial','B',10);
//			$P->SetXY(100,5);
//			$P->SetTextColor(0,0,0);
//			$P->Cell(90,5,'ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
//			$P->setxy(100,9);
//			$P->Cell(90,5,'NIT.: 900.174.552-5 - I.V.A. RÉGIMEN COMÚN',0,0,'C');
//			$P->setfont('Arial','',8);
//			$P->setxy(100,14);
//			$P->Cell(90,5,'ACTIVIDADES ECONÓMICAS No. 7710 Y 7020',0,0,'C');
//			$P->setxy(100,17);
//			$P->Cell(90,5,'NO SOMOS GRANDES CONTRIBUYENTES - NO SOMOS AUTORRETENEDORES',0,0,'C');
//			$P->setxy(100,21);
//			$P->Cell(90,5,'CENTRO COMERCIAL HACIENDA SANTA BÁRBARA OFICINA D-503',0,0,'C');
//			$P->SETXY(100,24);
//			$P->CELL(90,5,'PBX: (571) 629 3096 - FAX (571) 620 0967',0,0,'C');
//			$P->SETXY(100,27);
//			$P->CELL(90,5,'www.aoacolombia.com - Bogotá, D.C. - Colombia',0,0,'C');
//			$P->SETFONT('Arial','B',8);
//			$P->SETXY(10,37);	$P->CELL(20,4,'FECHA DE EMISION:');
//			$P->SETXY(90,37);	$P->CELL(40,4,'FECHA DE VENCIMIENTO:',0,0,'R');
//			$P->SETXY(10,44);	$P->CELL(20,4,'CLIENTE:');
//			$P->SETXY(90,44);$P->CELL(40,4,'NIT/CC:',0,0,'R');
//			$P->SETXY(10,51);	$P->CELL(20,4,'DIRECCION:');
//			$P->SETXY(90,51);	$P->CELL(40,4,'TELEFONO:',0,0,'R');
//			$P->SETFONT('Arial','B',10);
//			$P->SETXY(170,34);$P->CELL(40,6,'FACTURA DE VENTA',0,0,'C',1);
//			$P->SETXY(170,46);$P->cell(40,5,'Orden de Servicio No.',0,0,'L',1);
//			$P->SETFONT('Times','B',12);
//			$P->settextColor(150,0,0);
//			$P->setxy(170,39); $P->Cell(40,7,'No.   '.str_pad($D->consecutivo,6,'0',STR_PAD_LEFT),0,0,'C',1);
			$P->settextcolor(0,0,0);
			$P->setfont('Arial','',8);
			if($Ciudad_sin) {$P->setxy(170,53);$P->cell(40,6,$OrdenServicio,0,0,'C',0);}
			$P->setxy(40,39);$P->cell(40,4,fecha_completa($D->fecha_emision));
			$P->setxy(125,39);$P->cell(40,4,fecha_completa($D->fecha_vencimiento));
			$P->setxy(24,46);$P->cell(40,4,$Cliente->nombre.' '.$Cliente->apellido);
			$P->setxy(125,46);$P->cell(40,4,$Cliente->identificacion);
			$P->setxy(28,55);$P->cell(40,4,$Cliente->direccion);
			$P->setxy(125,55);$P->cell(40,4,$Cliente->celular.' '.$Cliente->telefono_casa);
//			$P->line(170,34,170,57);
//			$P->line(10,42,170,42);
//			$P->line(10,49,170,49);
//			$P->line(170,45,210,45);
//			$P->settextcolor(255,255,255);
//			$P->setfillcolor(0,0,0);
//			$P->setfont('Arial','B',10);
//			$P->setxy(10,60);$P->cell(149,5,'DETALLE',1,0,'C',1);
//			$P->setxy(160,60);$P->cell(50,5,'VALOR',1,0,'C',1);
			$Y=75;
			$P->settextcolor(0,0,0);
			$P->setfillcolor(230,230,230);
			$P->setfont('Arial','',10);
			$Base_Iva=0;
			while($I=mysql_fetch_object($Det))
			{
				if($I->iva) $Base_Iva+=$I->cantidad*$I->unitario;
				$P->setxy(170,$Y);$P->cell(30,5,'$ '.coma_formatd($I->cantidad*$I->unitario,2),0,0,'R');
				$P->setxy(15,$Y);
				$P->multicell(140,5,$I->nconcepto.' '.$I->descripcion);
				$Y=$P->y;
			}
			$Y+=5;
			if($Ciudad_sin)
			{
				$P->setxy(15,$Y);$P->cell(100,5,"$Ciudad_sin",0,0,'L');
			}

			$Y=198;
//			$P->setxy(110,$Y+28);$P->cell(100,36,' ',0,0,'C',1);
//			$P->rect(10,34,200,23);
//			$P->rect(10,67,200,$Y-41);
//			$P->rect(10,$Y+28,200,36);
//			$P->line(110,$Y+28,110,$Y+64);
//			$P->line(114,$Y+50,206,$Y+50);
//			$P->line(15,$Y+54,105,$Y+54);
			$P->setxy(150,$Y-5);$P->cell(20,5,'SUBTOTAL');
			$P->setxy(170,$Y-5);$P->cell(30,5,'$ '.coma_formatd($D->subtotal,2),0,0,'R');
			$P->setxy(150,$Y);$P->cell(20,5,'BASE IVA');
			$P->setxy(170,$Y);$P->cell(30,5,'$ '.coma_formatd($Base_Iva,2),0,0,'R');
			$P->setxy(150,$Y+5);$P->cell(20,5,'IVA 16%');
			$P->setxy(170,$Y+5);$P->cell(30,5,'$ '.coma_formatd($D->iva,2),0,0,'R');
			$P->setxy(150,$Y+10);$P->cell(20,5,'TOTAL');
			$P->setfont('Arial','B',10);
			$P->setxy(170,$Y+10);$P->cell(30,5,'$ '.coma_formatd($D->total,2),0,0,'R');
			$P->setfont('Arial','',10);
			$P->setxy(15,$Y+18);$P->multicell(170,5,'EN LETRAS: '.enletras($D->total,1),0,'J');
//			$P->setfont('Arial','B',6);
//			$P->setxy(13,$Y+28);$P->cell(30,4,'ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ');
//			$P->setxy(13,$Y+54);$P->cell(90,4,'FIRMA Y SELLO AUTORIZADOS',0,0,'C');
//			$P->setxy(15,$Y+58);$P->cell(90,4,'Esta Factura de Venta es un Título Valor de conformidad con lo establecido en',0,0,'C');
//			$P->setxy(15,$Y+60);$P->cell(90,4,'la Ley 231 de julio 17 de 2008 y demás normas que lo complementan.',0,0,'C');
//			$P->setxy(110,$Y+50);$P->cell(90,4,'FIRMA Y SELLO DEL COMPRADOR',0,0,'C');
//			$P->setfont('Arial','',6);
//			$P->setxy(110,$Y+54);$P->cell(90,4,'NOMBRE DE QUIEN RECIBE:');
//			$P->setxy(110,$Y+57);$P->cell(90,4,'DOCUMENTO DE IDENTIDAD:');
//			$P->setxy(110,$Y+60);$P->cell(90,4,'FECHA DE RECIBIDO:');


/*			$P->setfont('Arial','',10);$P->SetTextColor(0,0,0);
   $P->setxy(120,15);$P->setFont('Arial','b',10);$P->cell(40,5,'FECHA: '.$D->fecha);
   $P->setxy(120,20);$P->SetFont("c128a","",12);	$P->cell(44,11, uccean128('FA'.str_pad($SID,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
   $P->SetXY(15,35);	$P->setfont('Arial','',10);$P->cell(30,5,"Expedida para:");$P->SetFont('Arial','b',10);$P->cell(80,5,$Taller->nombre);
   $P->setfont('Arial','',10);$P->cell(50,5,$Ciudad);
   $P->setxy(45,40);$P->cell(180,5,$Taller->direccion.'  Tel: '.($Taller->telefono1?$Taller->telefono1:'').' '.($Taller->telefono2?$Taller->telefono2:'').' '.($Taller->telefono3?$Taller->telefono3:'').' '.($Taller->celular?$Taller->celular:''));
   $P->setxy(15,45);$P->cell(30,5,'Placa vehiculo:');$P->setFont('Arial','b',10);$P->cell(20,5,$D->placa);
   $P->setFont('Arial','',10);$P->cell(40,5,'Kilometraje de salida:');$P->setFont('Arial','b',10);$P->cell(20,5,$D->odometro_ini);
   $P->setxy(15,50);$P->setFont('Arial','',10);$P->cell(50,5,'DESCRIPCION :');
   $P->setxy(19,55);$P->setFont('Arial','B',10);$P->MultiCell(180,5,$D->descripcion,1,'J');
   $P->setxy(15,$P->y+5);$P->setFont('Arial','',10);$P->cell(50,5,'DESCRIPCION POR GARANTIA :');
   $P->setxy(19,$P->y+5);$P->setFont('Arial','B',10);$P->MultiCell(180,5,$D->descripciong,1,'J');
   $P->setxy(15,$P->y+10);$P->SetFont('Arial','',10);$P->Cell(30,5,'Solicitado por:');;$P->setFont('Arial','B',10);$P->Cell(100,5,$D->solicitado_por);
   $P->setxy(15,$P->y+5);$P->SetFont('Arial','',10);$P->Cell(30,5,'Aprobado por:');$P->setFont('Arial','B',10);$P->Cell(100,5,$D->aprobado_por);
   $P->setxy(35,$P->y+6);$P->SetFont('Arial','',8);$P->Cell(180,4,'Señor Proveedor: Favor hacer referencia del número de esta Orden de Servicio en su factura.');
   $P->Rect(15,9,190,$P->y-2);
   if($Archivo)
   {
   $P->Output($Archivo);
   $Envio=enviar_mail2($Remitente,$_SESSION['Nombre'],
   'arturo__quintero@hotmail.com',
   'Orden de Servicio',
   "Orden de Servicio No. $id",
   '','',
   "Orden_$id.pdf",$Archivo	);
   }
   else */
			$P->Output($Archivo);
		}
		else
			echo "<script language='javascript'>
				function carga()
				{
					centrar(10,10);
					alert('La factura no tiene detalle para imprimir');
					window.close();
					void(null);
				}
			</script>
			<body onload='carga()'></body>";
	}
	else
		echo "<script language='javascript'>
			function carga()
			{
				centrar(10,10);
				alert('No se encuentra información de la factura número $consecutivo');
				window.close();
				void(null);
			}
		</script>
		<body onload='carga()'></body>";
}

function genera_solicitud_autorizacion()
{
	include('inc/gpos.php');
	sesion();
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	$Nick = $_SESSION['Nick'];
	$ahora=date('Y-m-d H:i:s');
	$por=$_SESSION['Nombre'];
	$Valor=qo1("Select total from factura where consecutivo=$fac");
	q("insert into sin_autor (siniestro,nombre,identificacion,numero,banco,franquicia,vencimiento_mes,vencimiento_ano,codigo_seguridad,valor,estado,numero_voucher,fecha_solicitud,solicitado_por)
	SELECT siniestro,nombre,identificacion,numero,banco,franquicia,vencimiento_mes,vencimiento_ano,codigo_seguridad,($Valor) as valor,('E') as estado,($vou) as numero_voucher,
	('$ahora') as fecha_solicitud,('$por') as solicitado_por from sin_autor where siniestro='$sin' and estado='A'");
	echo "<script language='javascript'>
		function carga()
		{
			alert('Generación de Solicitud hecha satisfactoriamente');
		}
	</script>
	<body onload='carga()'></body>";
}

function aprobar_factura() 
{
	include('inc/gpos.php');
	sesion();
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	$Nick = $_SESSION['Nick'];
	if(isset($id)){ $consecutivo=qo1("select consecutivo from factura where id=$id");};
	
	$consecutivo = trim($consecutivo);
	
	
	$Fac=qo("select * from factura where consecutivo='$consecutivo'");
	
	//print_r($Fac);

	if($Fac->id == 43408)
	{
		/*echo "Modo de prueba para fact electroníca";	
		require("factura_xml/factura_electronica.php");
	
		$fact_elec = new factura_electronica($Fac);			
		$fact_elec->generar_factura_electronica();
			
				
		exit;*/		
	} 
	
	if($Fac->autorizadopor)
	{
		//echo "<div>contenido nuevo</div>";			
		echo "<body><script language='javascript'>alert('Esta factura ya fue aprobada anteriormente por $Fac->autorizadopor');</script></body>";
	}
	else
	{
		$varTest = "<style>
			.loader {
				border: 16px solid #f3f3f3; /* Light grey */
				border-top: 16px solid #3498db; /* Blue */
				border-radius: 50%;
				width: 120px;
				height: 120px;
				animation: spin 2s linear infinite;
			}

			@keyframes spin {
				0% { transform: rotate(0deg); }
				100% { transform: rotate(360deg); }
			}
		</style>";
	$test1 =  "<h1>Ya esta tu factura electronica ! </h1>";	
	
	$Ahora=date('Y-m-d H:i:s');
		q("update factura set autorizadopor='$Nusuario' where consecutivo='$consecutivo'");
		
		$sql = "select * from factura where consecutivo='$consecutivo'";
		
		//echo $sql;
		
		$Fac=qo($sql);
		
		//print_r($Fac);
		
		//q("update siniestro set obsconclusion=concat(obsconclusion, \"\n$Nusuario [$Ahora]: Elabora Factura de Reintegro número $Fac->consecutivo de fecha $Fac->fecha_emision por valor de $ ".coma_format($Fac->total)."\") where id=$Fac->siniestro ");
		
		
		
		
		require("factura_xml/factura_electronica_NUEVO.php");
		echo "<script>
	             function carga_load(){
			            let new_element = document.createElement('div');
						let new_element2 = document.createElement('div');
						new_element.className = 'loader';
						new_element2.innerHTML = '$test1';
						console.log(new_element);					
						parent.document.body.appendChild(new_element);
						parent.document.body.appendChild(new_element2);
		            }
                  carga_load();
		
		</script>";
	
		$fact_elec = new factura_electronica($Fac);			
		$fact_elec->generar_factura_electronica();
			
		
		$Email_usuario=usuario('email');

		$m="<body><table border cellspacing='0'><td>Factura Número</td><td colspan='3'>$consecutivo</td></tr>".
			"<tr><td>Fecha de Factura:</td><td>$Fac->fecha_emision</td><td>Vencimiento:</td><td>$Fac->fecha_vencimiento</td></tr>".
			"<tr><td>Cliente:</td><td colspan='3'>".qo1("select concat(apellido,' ',nombre) from cliente where id=$Fac->cliente")."</td></tr>".
			"<tr><td>Aseguradora:</td><td colspan=3>".qo1("select nombre from aseguradora where id=$Fac->aseguradora")."</td></tr>";
		if($Fac->siniestro)
		{
		      $Sin=qo("select numero,ciudad,ciudad_original from siniestro where id=$Fac->siniestro");
		      $m.="<tr><td>Siniestro:</td><td>".$Sin->numero."</td></tr>";
		      $m.="<tr><td>Ciudad:</td><td>".qo1("Select concat(departamento,' ',nombre) from ciudad where codigo='$Sin->ciudad'")."</td></tr>";
		      if($Sin->ciudad_original)
		      {
			        $m.="<tr><td>Ciudad Original:</td><td>".qo1("Select concat(departamento,' ',nombre) from ciudad where codigo='$Sin->ciudad_original'")."</td></tr>";
		      }
		}
		$m.="</table><br>";

		if($Detalle=q("select *,t_concepto_fac(concepto) as nconcepto from facturad where factura=$Fac->id"))
		{
		      $m.="<br><table border cellspacing='0'><td>Concepto</td><td>Cantidad</td><td>Unitario</td><td>Iva</td><td>Total</td></tr>";
		      while($D=mysql_fetch_object($Detalle))
		      {
			        $m.="<tr><td>$D->nconcepto $D->descripcion</td><td align='right'>".coma_format($D->cantidad)."</td>".
			            "<td align='right'>".coma_format($D->unitario)."</td><td align0'right'>".coma_format($D->iva)."</td>".
			            "<td align='right'>".coma_format($D->total)."</TD></tr>";
		      }
		      $m.="</table><br>";
		}
		$m.="<table><tr><td>Subtotal:</td><td align='right'>".coma_format($Fac->subtotal)."</td></tr>".
			"<tr><td>Iva:</td><td align='right'>".coma_format($Fac->iva)."</td></tr>".
			"<tr><td>Total:</td><td align='right'>".coma_format($Fac->total)."</td></tr>".
			"<tr><td>Elaborada por:</td><td>$Fac->autorizadopor</td></tr></table><br>".
			"<br>Este es un correo automático generado por el Sistema de Control.</body>";

		$Envio=enviar_gmail($Email_usuario /*de */,
						$Nusuario /*Nombre de */ ,
						"s.ospina@valps.com,Contabilidad" /*para */,
						"$Email_usuario,$Nusuario" /*con copia*/,
						"GENERACION FACTURA $consecutivo" /*Objeto */,
						$m);
						
						
		header('Content-Type: text/html;');
		
		$iframe_Tag = '<iframe width="100%" height="100%" src="https://app.aoacolombia.com/Control/operativo/zfacturacion.php?Acc=verificar_factura_electronica&id='.$Fac->id.'"  ></iframe>';
		
		
		header('Content-Type: text/html; charset=utf-8');
		echo "
		<body>
			<div>
				Interfaz factura eléctronica
				<script>
					
					function load_iframe()
					{
						let new_element = document.createElement('div');
						new_element.innerHTML = '$iframe_Tag';
						console.log(new_element);					
						
						parent.document.body.appendChild(new_element);
						
						console.log(parent.document);
						console.log(new_element);
						alert('Verifique el proceso de factura electrónica');
					}
					load_iframe();
					//setTimeout(function(){ fin_carga(); }, 5000);

					
				
				</script>
			</div>
			<script language='javascript'>console.log('Make another process');   alert('Aprobacion satisfactoria por $Nusuario , enviado a $Email_usuario'); window.scrollTo(0,parent.document.body.scrollHeight); ";
		
		
		if($Solicitud=qo("select * from solicitud_factura where siniestro=$Fac->siniestro and procesado_por='' ")) echo "window.open('zfacturacion.php?Acc=marca_solicitud_procesada&id=$Solicitud->id','_self');";
		
		
			echo "</script>
		</body>";
	}
}

function aprobar_factura_ANTERIOR_19_11_30()
{
	include('inc/gpos.php');
	sesion();
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	$Nick = $_SESSION['Nick'];
	if(isset($id)){ $consecutivo=qo1("select consecutivo from factura where id=$id");};
	
	$consecutivo = trim($consecutivo);
	
	
	$Fac=qo("select * from factura where consecutivo='$consecutivo'");
	
	//print_r($Fac);

	if($Fac->id == 43408)
	{
		/*echo "Modo de prueba para fact electroníca";	
		require("factura_xml/factura_electronica.php");
	
		$fact_elec = new factura_electronica($Fac);			
		$fact_elec->generar_factura_electronica();
			
				
		exit;*/		
	} 
	
	if($Fac->autorizadopor)
	{
		//echo "<div>contenido nuevo</div>";			
		echo "<body><script language='javascript'>alert('Esta factura ya fue aprobada anteriormente por $Fac->autorizadopor');</script></body>";
	}
	else
	{	
		$Ahora=date('Y-m-d H:i:s');
		q("update factura set autorizadopor='$Nusuario' where consecutivo='$consecutivo'");
		
		$sql = "select * from factura where consecutivo='$consecutivo'";
		
		//echo $sql;
		
		$Fac=qo($sql);
		
		//print_r($Fac);
		
		q("update siniestro set obsconclusion=concat(obsconclusion, \"\n$Nusuario [$Ahora]: Elabora Factura de Reintegro número $Fac->consecutivo de fecha $Fac->fecha_emision por valor de $ ".coma_format($Fac->total)."\") where id=$Fac->siniestro ");
		
		
		require("factura_xml/factura_electronica_NUEVO.php");
	
		$fact_elec = new factura_electronica($Fac);			
		$fact_elec->generar_factura_electronica();
			
		
		$Email_usuario=usuario('email');

		$m="<body><table border cellspacing='0'><td>Factura Número</td><td colspan='3'>$consecutivo</td></tr>".
			"<tr><td>Fecha de Factura:</td><td>$Fac->fecha_emision</td><td>Vencimiento:</td><td>$Fac->fecha_vencimiento</td></tr>".
			"<tr><td>Cliente:</td><td colspan='3'>".qo1("select concat(apellido,' ',nombre) from cliente where id=$Fac->cliente")."</td></tr>".
			"<tr><td>Aseguradora:</td><td colspan=3>".qo1("select nombre from aseguradora where id=$Fac->aseguradora")."</td></tr>";
		if($Fac->siniestro)
		{
		      $Sin=qo("select numero,ciudad,ciudad_original from siniestro where id=$Fac->siniestro");
		      $m.="<tr><td>Siniestro:</td><td>".$Sin->numero."</td></tr>";
		      $m.="<tr><td>Ciudad:</td><td>".qo1("Select concat(departamento,' ',nombre) from ciudad where codigo='$Sin->ciudad'")."</td></tr>";
		      if($Sin->ciudad_original)
		      {
			        $m.="<tr><td>Ciudad Original:</td><td>".qo1("Select concat(departamento,' ',nombre) from ciudad where codigo='$Sin->ciudad_original'")."</td></tr>";
		      }
		}
		$m.="</table><br>";

		if($Detalle=q("select *,t_concepto_fac(concepto) as nconcepto from facturad where factura=$Fac->id"))
		{
		      $m.="<br><table border cellspacing='0'><td>Concepto</td><td>Cantidad</td><td>Unitario</td><td>Iva</td><td>Total</td></tr>";
		      while($D=mysql_fetch_object($Detalle))
		      {
			        $m.="<tr><td>$D->nconcepto $D->descripcion</td><td align='right'>".coma_format($D->cantidad)."</td>".
			            "<td align='right'>".coma_format($D->unitario)."</td><td align0'right'>".coma_format($D->iva)."</td>".
			            "<td align='right'>".coma_format($D->total)."</TD></tr>";
		      }
		      $m.="</table><br>";
		}
		$m.="<table><tr><td>Subtotal:</td><td align='right'>".coma_format($Fac->subtotal)."</td></tr>".
			"<tr><td>Iva:</td><td align='right'>".coma_format($Fac->iva)."</td></tr>".
			"<tr><td>Total:</td><td align='right'>".coma_format($Fac->total)."</td></tr>".
			"<tr><td>Elaborada por:</td><td>$Fac->autorizadopor</td></tr></table><br>".
			"<br>Este es un correo automático generado por el Sistema de Control.</body>";

		$Envio=enviar_gmail($Email_usuario /*de */,
						$Nusuario /*Nombre de */ ,
						"s.ospina@valps.com,Contabilidad" /*para */,
						"$Email_usuario,$Nusuario" /*con copia*/,
						"GENERACION FACTURA $consecutivo" /*Objeto */,
						$m);
						
						
		header('Content-Type: text/html;');
		
		$iframe_Tag = '<iframe width="100%" height="100%" src="https://app.aoacolombia.com/Control/operativo/zfacturacion.php?Acc=verificar_factura_electronica&id='.$Fac->id.'"  ></iframe>';
		
	
		
		echo "
		<body>
			<div>
				Interfaz factura eléctronica
				<script>
					
					function load_iframe()
					{
						let new_element = document.createElement('div');
						new_element.innerHTML = '$iframe_Tag';
						console.log(new_element);					
						
						parent.document.body.appendChild(new_element);
						
						console.log(parent.document);
						console.log(new_element);
						alert('Verifique el proceso de factura electrónica');
					}
				
					load_iframe();
					
				</script>
			</div>
			<script language='javascript'>console.log('Make another process');   alert('Aprobación satisfactoria por $Nusuario , enviado a $Email_usuario'); window.scrollTo(0,parent.document.body.scrollHeight); ";	
		
		
		if($Solicitud=qo("select * from solicitud_factura where siniestro=$Fac->siniestro and procesado_por='' ")) echo "window.open('zfacturacion.php?Acc=marca_solicitud_procesada&id=$Solicitud->id','_self');";
		
		
			echo "</script>
		</body>";
	}
}

function anular_factura()
{
	include('inc/gpos.php');
	sesion();
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	$Nick = $_SESSION['Nick'];
	$F=qo("select *,t_cliente(cliente) as ncliente,t_aseguradora(aseguradora) as naseg from factura where id=$id");
	$Dia_uno=primer_dia_de_mes(date('Y-m-d'));
	if($F->fecha_emision<$Dia_uno)
	{
		echo "<body><script language='javascript'>alert('No puede anular una facturas de meses anteriores.');window.close();void(null);</script></body>";
		die();
	}
	if(inlist($_SESSION['Id_alterno'],'13,16'))
	{
		if(!$F->siniestro)
		{
			echo "<body><script language='javascript'>alert('No tiene privilegios para anular esta factura.');window.close();void(null);</script></body>";
			die();
		}
	}

	html('ANULACION DE FACTURA');
	echo "<script language='javascript'>
			function carga()
			{	centrar(800,500);}

			function validar()
			{
				with(document.forma)
				{
					if(!alltrim(motivo.value))
					{
						alert('Debe especificar el motivo por el cual se anula esta factura');
						return false;
					}
				}
				document.forma.Acc.value='anular_factura_ok';
				document.forma.submit();
			}
		</script>
		<body onload='carga()'>
		<form action='zfacturacion.php' method='post' target='_self' name='forma' id='forma'>
			<font style='font-size:14'>
			Proceso de anulación de la factura: <b>$F->consecutivo</b><br />
			Cliente: <b>$F->ncliente</b><br />
			Aseguradora: <b>$F->naseg</b><br />
			<br />Digite por favor el motivo de la anulación:<br /></font>
			<textarea name='motivo' id='motivo' style='font-size:12;font-family:arial' cols=100 rows=5></textarea><br /><br />
			<input type='button' value='Continuar' onclick='validar()'>
			<input type='hidden' name='Acc' id='Acc' value=''>
			<input type='hidden' name='id' id='id' value='$id'>
		</form></body>";
}

function anular_factura_ok()
{
	include('inc/gpos.php');
	sesion();
	html();
	echo "<body>";
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	$Nick = $_SESSION['Nick'];
	q("update factura set anulada=1,observaciones=\"$motivo\" where id=$id ");
	graba_bitacora('factura','M',$id,"Anula factura");
	$Fac=qo("select * from factura where id=$id");
	$Email_usuario=usuario('email');
	$m="<body><H3>ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.</H3><BR>
	POR MEDIO DEL PRESENTE SE INFORMA SOBRE LA ANULACION DE LA SIGUIENTE FACTURA:<BR><BR>".
	"<table border cellspacing='0'><td>Factura Número</td><td colspan='3'>$Fac->consecutivo</td></tr>".
	"<tr><td>Fecha de Factura:</td><td>$Fac->fecha_emision</td><td>Vencimiento:</td><td>$Fac->fecha_vencimiento</td></tr>".
	"<tr><td>Cliente:</td><td colspan='3'>".qo1("select concat(apellido,' ',nombre) from cliente where id=$Fac->cliente")."</td></tr>".
	"<tr><td>Aseguradora:</td><td colspan=3>".qo1("select nombre from aseguradora where id=$Fac->aseguradora")."</td></tr>";
	if($Fac->siniestro)
	{
		$Sin=qo("select numero,ciudad,ciudad_original from siniestro where id=$Fac->siniestro");
		$m.="<tr><td>Siniestro:</td><td>".$Sin->numero."</td></tr>";
		$m.="<tr><td>Ciudad:</td><td>".qo1("Select concat(departamento,' ',nombre) from ciudad where codigo='$Sin->ciudad'")."</td></tr>";
		if($Sin->ciudad_original)
		{
			$m.="<tr><td>Ciudad Original:</td><td>".qo1("Select concat(departamento,' ',nombre) from ciudad where codigo='$Sin->ciudad_original'")."</td></tr>";
		}
	}
	$m.="</table><br>";

	if($Detalle=q("select *,t_concepto_fac(concepto) as nconcepto from facturad where factura=$Fac->id"))
	{
		$m.="<br><table border cellspacing='0'><td>Concepto</td><td>Cantidad</td><td>Unitario</td><td>Iva</td><td>Total</td></tr>";
		while($D=mysql_fetch_object($Detalle))
		{
			$m.="<tr><td>$D->nconcepto $D->descripcion</td><td align='right'>".coma_format($D->cantidad)."</td>".
			    "<td align='right'>".coma_format($D->unitario)."</td><td align0'right'>".coma_format($D->iva)."</td>".
			    "<td align='right'>".coma_format($D->total)."</TD></tr>";
		}
		$m.="</table><br>";
	}
	$m.="<table><tr><td>Subtotal:</td><td align='right'>".coma_format($Fac->subtotal)."</td></tr>".
	"<tr><td>Iva:</td><td align='right'>".coma_format($Fac->iva)."</td></tr>".
	"<tr><td>Total:</td><td align='right'>".coma_format($Fac->total)."</td></tr>".
	"<tr><td>Elaborada por:</td><td>$Fac->autorizadopor</td></tr></table><br>".
	"<br><b>MOTIVO DE LA ANULACION: $Fac->observaciones</b><br><br>".
	"<br>Este es un correo automático generado por el Sistema de Control.</body>";

	if($Fac->movilidad)
	{
		echo "<script language='javascript'>modal('https://www.aoasemuevecontigo.com/util.php?Acc=eliminacion_factura_aoa&factura=$Fac->consecutivo',0,0,100,100,'efacmov');</script>";
	}
	
	$Envio=enviar_gmail($Email_usuario /*de */,
									$Nusuario /*Nombre de */ ,
									"s.ospina@valps.com,Sandra Ospina" /*para */,
									"$Email_usuario,$Nusuario" /*con copia*/,
									"ANULACION FACTURA $Fac->consecutivo" /*Objeto */,
									$m);
	
	echo "<script language='javascript'>function carga() {alert('Anulación satisfactoria por $Nusuario');window.close();void(null);} </script><body onload='carga()'></body>";
}

function exportar_factura()
{


}

function aplicar_garantia()
{
	include('inc/gpos.php');
	sesion();
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	$Nick = $_SESSION['Nick'];
	$Hoy=date('Y-m-d');
	$F=qo("select * from factura where id=$idfactura");
	if($F->garantia)
	{
		echo "<body><script language='javascript'>alert('Ya fue efectuada la aplicación de pago de esta factura con la Garantía');</script>";
		die();
	}
	q("update factura set garantia='$idgarantia' where id='$idfactura' ");
	graba_bitacora('factura','M',$idfactura,"Aplica garantia $idgarantia");
	$RC=qo1("select id from recibo_caja where  autorizacion=$idgarantia and garantia=1");
	$Cons=qo1("select max(consecutivo) from nota_contable")+1;
	$NID=q("insert into nota_contable (siniestro,fecha,factura,consecutivo,autorizacion,recibo_caja,valor) values ($F->siniestro,'$Hoy',$idfactura,$Cons,$idgarantia,'$RC',$F->total)");
	graba_bitacora('nota_contable','A',$NID);
	echo "<body><script language='javascript'>alert('Garantia aplicada satisfactoriamente');</script>";
}

function marca_solicitud_procesada()
{
	include('inc/gpos.php');
	html('SOLICITUD FACTURACION');
	$S=qo("select *,t_siniestro(siniestro)  as nsiniestro, t_concepto_fac(concepto) as nconcepto from solicitud_factura where id=$id");
	echo "<body><h3>Marcación Solicitud de Facturación Procesada</h3>
		<form action='zfacturacion.php' method='post' target='_self' name='forma' id='forma'>
			<table>
				<tr ><td >Siniestro</td><tr >$S->nsiniestro</tr></tr>
				<tr ><td >Concepto</td><td >$S->nconcepto</td></tr>
				<tr ><td >Descripción</td><td >$S->descripcion</td></tr>
				<tr ><td >Solicitado por</td><td >$S->solicitado_por</td></tr>
				<tr ><td >Fecha</td><td >$S->fecha_solicitud</td></tr>
				<tr ><td >Valor</td><td >".coma_format($S->valor)."</td></tr>
			</table><br /><br />
			<input type='submit' value='Marcar como procesado'>
			<input type='hidden' name='Acc' value='marca_solicitud_procesada_ok'>
			<input type='hidden' name='id' value='$id'>
		</form>
		<script language='javascript'>document.forma.submit();</script></body>";
}

function marca_solicitud_procesada_ok()
{
	include('inc/gpos.php');
	sesion();
	$USUARIO = $_SESSION['User'];
	$Nusuario = $_SESSION['Nombre'];
	$Nick = $_SESSION['Nick'];
	$Hoyl = date('Y-m-d H:i:s');
	q("update solicitud_factura set procesado_por='$Nusuario',fecha_proceso='$Hoyl' where id=$id");
	graba_bitacora('solicitud_factura','M',$id,'Marca procesado');
	echo "<body><script language='javascript'>alert('Marcación satisfactoria');window.close();void(null);</script></body>";
}
/*Funcion que redirecciona a la interface de angular para las funciones de enviar factura
Generar xml y reprensentacion grafica
*/
function verificar_factura_electronica()
{
	sesion();
	//echo "Sub interfaz de factura electronica";
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/views/factura_electronica/interfazNUEVOdevDJ.html");
}

function verificar_factura_electronica_test()
{
	sesion();
	//echo "Sub interfaz de factura electronica";
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/views/factura_electronica/interfazNUEVO2.html");
}


function facturacion_electronica_manual()
{
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/views/factura_electronica/creacion_manual.html");

}
function facturacion_electronica_manual_test()
{
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/views/factura_electronica/creacion_manual_Nuevo.html");

}

function facturacion_electronica_reporte(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/views/factura_electronica/facturacion_electronica_reporte.html");
}



?>