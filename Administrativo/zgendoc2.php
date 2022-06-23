<?php
//echo "file finded";
//Este archivo no se encuentra en producción solo se utilizaba para realizar pruebas.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);

include_once('inc/funciones_2.php');

include_once('zgendoc_funciones.php');
echo "here";
sesion();
require_once('inc/DocGenerator/clsMsDocGenerator.php');
echo "<strong><h1>Esta es una interfaz para realizar pruebas y mantenimiento</h1></strong>";
if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}
inicio_generador();
	
function inicio_generador()
{	
	echo "inicio generador";
	global $Nusuario;
	html('GENERADOR DE FORMATOS - AOA');
	echo "<body style='font-size:12px'>
	<h3>GENERADOR DE FORMATOS - AOA</h3>
	Estimado usuario(a) <b>$Nusuario</b>: Este módulo le permitirá generar las plantillas correspondientes a los distintos documentos oficialmente registrados en el Listado Maestro de Documentos y Formatos del 
	sistema de Gestión de Calidad de AOA.<br><br>
	Este programa llevará el consecutivo de cada formato cada vez que un usuario genere el documento. Todos los documentos son generados en formato .DOC <br><br>
	Al generar el documento se debe guardar y se puede abrir con cualquier procesador de texto (word, open office, etc.). <br><br>
	No se deben imprimir multiples copias del mismo consecutivo para mantener la trazabilidad. Cada vez que se vaya a usar un formato se debe generar a partir de este módulo.<br><br>
	
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		Seleccione el tipo de documento que desea generar:<br>
		".menu_search("TIPO","Select id,concat(sigla,' - ',nombre) from tipo_formatoaoa where rutina!='' and activo=1 order by sigla,nombre",0,1,"width:700px","size=10")."<br><br>
		<input type='submit' name='continuar' id='continuar' value=' CONTINUAR ' style='width:300px;height:60px;font-weight:bold;'>
		<input type='hidden' name='Acc' value='busca_rutina_formato'>
	</form></body>".javascript_search("TIPO");
	
	echo "<form action='zgendoc2.php' target='_self' method='POST' >
		
		<input type='hidden' name='Acc' value='formato_reintegro_gastos_ok'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
		<input type='SUBMIT' name='continuar' id='continuar' value=' TEST DOC  ' style='font-size:16px;font-weight:bold;' '>
	</form>";
}

function busca_rutina_formato()
{	
	global $TIPO;
	//echo $TIPO;
	//return "";
	if($TIPO)
	{
		$Formato=qo("select * from tipo_formatoaoa where id=$TIPO");
		$Consecutivo=qo1("select max(consecutivo) from formatos_aoa where tipo_formato=$TIPO")+1;
		if($Formato->rutina)
		{
			echo "<body>
			<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='$Formato->rutina'>
				<input type='hidden' name='consecutivo' value='$Consecutivo'>
				<input type='hidden' name='tipo' value='$TIPO'>
			</form>
			<script language='javascript'>document.forma.submit();</script>
			</body>";
		}
		else
		{echo "<body><script language='javascript'>alert('No hay definida una rutina para generar este tipo de documento. $Formato->nombre');window.close();void(null);</script></body>";}
	}
	else
	{echo "<body><script language='javascript'>alert('No seleccionó un tipo de documento.');history.back(1);</script></body>";}
}

function cabecera_documento($Titulo='')
{
	return "<table width='90%' align='center'><tr><td width='160'>
			<img src='http://app.aoacolombia.com/Administrativo/img/nlogo_aoa_200.jpg' border='0' height='90' width='190'>
		</td><td align='center'><b style='font-size:18px'>$Titulo</b></td><td width='160'></td></tr></table>";
}

//  *****************************************************   RUTINAS DE CAPTURA PREVIA A LA GENERACION DE DOCUMENTOS *******************************************

function gendoc_memorando()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<table>
		<tr><td align='right'>Dirigido a:</td><td><input type='text' name='para' id='para' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>De:</td><td><input type='text' name='de' id='de' value='$NUSUARIO' size='80' maxlength='100'></td></tr>
		<tr><td align='right'>Alcance:</td><td><input type='text' name='alcance' id='alcance' value='' size='80' maxlength='100'></td></tr>
		<tr><td align='right'>Asunto:</td><td><input type='text' name='asunto' id='asunto' value='' size='80' maxlength='100'></td></tr>
		<tr><td align='right' valign='top'>Texto:</td><td><textarea name='contenido' cols=100 rows=15></textarea></td></tr>
		<tr><td align='right'>Firma:</td><td><input type='text' name='firma' id='firma' value='$NUSUARIO' size='100' maxlength='100'></td></tr>
		<tr><td align='right'>Cargo:</td><td><input type='text' name='cargo' id='cargo' value='$NCargo' size='100' maxlength='100'></td></tr>
		</table>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_comunicacion_interna_ok'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_entrega_soat()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<table>
			<tr><td align='right'>Para:</td><td><input type='text' name='para' id='para' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
			<tr><td align='right'>De:</td><td><input type='text' name='de' id='de' value='$NUSUARIO' size='80' maxlength='100'></td></tr>
			<tr><td align='right'>Asunto:</td><td><input type='text' name='asunto' id='asunto' value='ENTREGA SOATs' size='80' maxlength='100'></td></tr>
			<tr><td align='right' valign='top'>Texto:</td><td><textarea name='contenido' cols=100 rows=15>Por medio de la presente hago entrega de los siguientes SOATs, para los vehículos:
			</textarea></td></tr>
			<tr><td align='right'>Firma:</td><td><input type='text' name='firma' id='firma' value='$NUSUARIO' size='100' maxlength='100'></td></tr>
			<tr><td align='right'>Cargo:</td><td><input type='text' name='cargo' id='cargo' value='$NCargo' size='100' maxlength='100'></td></tr>
		</table>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_entrega_soat_ok'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_autorizacion_retiro_vehiculo()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<table>
			<tr><td align='right'>Para:</td><td><input type='text' name='para' id='para' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
			<tr><td align='right'>De:</td><td><input type='text' name='de' id='de' value='$NUSUARIO' size='80' maxlength='100'></td></tr>
			<tr><td align='right'>Asunto:</td><td><input type='text' name='asunto' id='asunto' value='Autorización Retiro de Vehículo' size='80' maxlength='100'></td></tr>
			<tr><td align='right' valign='top'>Texto:</td><td><textarea name='contenido' cols=100 rows=15>Respetados señores,

Por medio de la presente informamos que al señor _ identificado (a) con Cédula de  Ciudadanía No. _, se le autorizó para conducir el vehículo _ de placas _ de propiedad de nuestra compañía AOA- ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A. con Nit. No. 900.174.552-5.</textarea></td></tr>
			<tr><td align='right'>Firma:</td><td><input type='text' name='firma' id='firma' value='SEBASTIAN HURTADO' size='100' maxlength='100'></td></tr>
			<tr><td align='right'>Cargo:</td><td><input type='text' name='cargo' id='cargo' value='Representante Legal' size='100' maxlength='100'></td></tr>
		</table>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_autorizacion_retiro_vehiculo_ok'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_siniestros_envio_papeleria()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<table>
			<tr><td align='right'>Para:</td><td><input type='text' name='para' id='para' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
			<tr><td align='right'>Oficina:</td><td><input type='text' name='oficina' id='oficina' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
			<tr><td align='right'>De:</td><td><input type='text' name='de' id='de' value='$NUSUARIO' size='80' maxlength='100'></td></tr>
			<tr><td align='right'>Asunto:</td><td><input type='text' name='asunto' id='asunto' value='Envío de Papelería' size='80' maxlength='100'></td></tr>
			<tr><td align='right' valign='top'>Texto:</td><td><textarea name='contenido' cols=100 rows=15>Adjunto hago envío de la siguiente papelería:
			
			</textarea></td></tr>
			<tr><td align='right'>Firma:</td><td><input type='text' name='firma' id='firma' value='$NUSUARIO' size='100' maxlength='100'></td></tr>
			<tr><td align='right'>Cargo:</td><td><input type='text' name='cargo' id='cargo' value='$NCargo' size='100' maxlength='100'></td></tr>
		</table>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_siniestros_envio_papeleria_ok'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_circular_informativa()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<table>
			<tr><td align='right'>De:</td><td><input type='text' name='de' id='de' value='$NUSUARIO' size='80' maxlength='100'></td></tr>
			<tr><td align='right'>Asunto:</td><td><input type='text' name='asunto' id='asunto' value='' size='80' maxlength='100'></td></tr>
			<tr><td align='right'>Firma:</td><td><input type='text' name='firma' id='firma' value='$NUSUARIO' size='100' maxlength='100'></td></tr>
			<tr><td align='right'>Cargo:</td><td><input type='text' name='cargo' id='cargo' value='$NCargo' size='100' maxlength='100'></td></tr>
		</table>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_circular_informativa_ok'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_retroalimentacion()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<table>
			<tr><td align='right'>De:</td><td><input type='text' name='de' id='de' value='$NUSUARIO' size='80' maxlength='100'></td></tr>
			<tr><td align='right'>Asunto:</td><td><input type='text' name='asunto' id='asunto' value='' size='80' maxlength='100'></td></tr>
			<tr><td align='right'>Firma:</td><td><input type='text' name='firma' id='firma' value='$NUSUARIO' size='100' maxlength='100'></td></tr>
			<tr><td align='right'>Cargo:</td><td><input type='text' name='cargo' id='cargo' value='$NCargo' size='100' maxlength='100'></td></tr>
		</table>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_circular_informativa_ok'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

//************************

function gendoc_formato_acta()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<table>
		<tr><td align='right'>Nombre de reunión:</td><td><input type='text' name='nombre_reunion' id='nombre_reunion' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>Lugar:</td><td><input type='text' name='lugar' id='lugar' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>Firma:</td><td><input type='text' name='firma' id='firma' value='$NUSUARIO' size='100' maxlength='100'></td></tr>
		<tr><td align='right'>Cargo:</td><td><input type='text' name='cargo' id='cargo' value='$NCargo' size='100' maxlength='100'></td></tr>
		</table>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_formato_acta_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_prestamo_documentos()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_prestamo_documentos_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_formato_normograma()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA'); 
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_formato_normograma_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_acta_eliminacion()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_acta_eliminacion_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_tabla_retencion_documental()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_tabla_retencion_documental_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_inventario_entrega_documental()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_inventario_entrega_documental_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_rev_aprob_docum_y_formatos()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_rev_aprob_docum_y_formatos_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_listado_maestro_docyform()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_listado_maestro_docyform_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_solicitud_manejo_documentos()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_solicitud_manejo_documentos_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_induccion()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		Nombre del empleado: <input type='text' name='nombre_empleado' id='nombre_empleado' value='' size='50' maxlength='50' onkeyup='this.value=this.value.toUpperCase();'><br>
		Fecha de realización de la inducción: ".pinta_FC('forma','fecha_induccion',date('Y-m-d'))."<br>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_induccion_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_programa_capacitacion()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	alert('RECUERDE: Al abrir el documento debe ajustar el formato de la página a horizontal.');
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_programa_capacitacion_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_verificacion_referencias()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		Nombre del empleado: <input type='text' name='nombre_empleado' id='nombre_empleado' value='' size='50' maxlength='50' onkeyup='this.value=this.value.toUpperCase();'><br>
		Firma: <input type='text' name='firma' id='firma' value='$NUSUARIO' size='100' maxlength='100'><br>
		Cargo: <input type='text' name='cargo' id='cargo' value='$NCargo' size='100' maxlength='100'><br>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_verificacion_referencias_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_registro_capacitacion()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_registro_capacitacion_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_eval_periodo_prueba()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_eval_periodo_prueba_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_requisitos_ingreso()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_requisitos_ingreso_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_aprobacion_anticipo()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_aprobacion_anticipo_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_solicitud_anticipo()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_solicitud_anticipo_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_bys_calidad()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_bys_calidad_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_legalizacion_anticipo()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_legalizacion_anticipo_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_lista_convenios()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_lista_convenios_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_definicion_bys()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_definicion_bys_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_informe_auditoria()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_informe_auditoria_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_lista_chequeo()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_lista_chequeo_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_plan_auditoria()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_plan_auditoria_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_programacion_auditoria()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_programacion_auditoria_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_acciones()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_acciones_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_consolidado_acciones()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_consolidado_acciones_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_programa_mantenimiento_preventivo()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_programa_mantenimiento_preventivo_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_comunicacion_externa()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_comunicacion_externa_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
	/*<table>
		<tr><td align='right'>Dirigido a:</td><td><input type='text' name='para' id='para' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>Cargo:</td><td><input type='text' name='cargo_para' id='cargo_para' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>Empresa:</td><td><input type='text' name='empresa' id='empresa' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>Dirección:</td><td><input type='text' name='direccion' id='direccion' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>Asunto:</td><td><input type='text' name='asunto' id='asunto' value='' size='80' maxlength='100'></td></tr>
		<tr><td align='right'>De:</td><td><input type='text' name='de' id='de' value='$NUSUARIO' size='80' maxlength='100'></td></tr>
		<tr><td align='right' valign='top'>Texto:</td><td><textarea name='contenido' cols=100 rows=15></textarea></td></tr>
		<tr><td align='right'>Firma:</td><td><input type='text' name='firma' id='firma' value='$NUSUARIO' size='100' maxlength='100'></td></tr>
		<tr><td align='right'>Cargo:</td><td><input type='text' name='cargo' id='cargo' value='$NCargo' size='100' maxlength='100'></td></tr>
	</table>*/
}

function gendoc_actaentrega_equipos()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_actaentrega_equipos_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_solicitud_vacaciones()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_solicitud_vacaciones_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_aprobacion_comite_compras()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_aprobacion_comite_compras_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

if(function_exists($_GET['f'])) {
   $_GET['f']();
}

function doctest()
{
	
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA -</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='submit' name='continuar' id='continuar' value=' GENERAR DOCUMENTO  ' style='font-size:16px;font-weight:bold;'>
		<input type='hidden' name='Acc' value='prueba_doc2'>
	</form>";
	echo "</body>";
}

function prueba_doc()
{
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	//header("Content-type: application/vnd.ms-word");
	//header("Content-Disposition: attachment;Filename=LISTA DE CHEQUEO VENTA VEHICULO_$Nconsecutivo.doc");
	$html = "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 5px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
}
        .Estilo4 {font-size: 10px; font-weight: bold; }
        .Estilo7 {font-size: 12px; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
		.Estilo12 {font-size: 8}
        .Estilo13 {font-size: 10px; color: #666666; 
		
		</style>
<body>
".cabecera_formato("tipo","FORMATO INGRESO AL DATACENTER",45)."
	<table border cellspacing='0' width='100%' align='left'>
		
		<tr>
			<th scope='col' width='20%' bgcolor='#CCCCCC' ><span class='Estilo4'>FECHA</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>NOMBRE VISITANTE</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>CEDULA VISITANTE</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>FIRMA VISITANTE</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>SUPERVISOR</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>FIRMA SUPERVISOR</span></th>
		</tr>";
	 for($i=0;$i<30;$i++)
	{
		$html = $html."<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>";
	}
	
	$html = $html."</table>
 </body></html>";	
	echo $html;
}

function acta_entrega_venta_vehiculo()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='acta_entrega_venta_vehiculo_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}
		
	


function prueba_doc2()
{
	$Tam_Fuente='12px';
	$Tam_Fuente1='8px';
	//header("Content-type: application/vnd.ms-word");
	//header("Content-Disposition: attachment;Filename=LISTA DE CHEQUEO VENTA VEHICULO_$Nconsecutivo.doc");
	$html =  "<html><meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
		<style>
body {margin-top: 5px; margin-left: 20px; margin-right: 20px; margin-bottom: 30px;font-family:arial;}
			td {margin-top: 2px; margin-left: 5px; margin-right: 5px; margin-bottom: 2px;font-size:$Tam_fuente;font-family:arial;}
		.Estilo1 {font-size: 10px}
        .Estilo2 {
	font-size: 12px;
	font-weight: bold;
}
        .Estilo4 {font-size: 10px; font-weight: bold; }
        .Estilo7 {font-size: 12px; }
        .Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 10px; }
        .Estilo11 {font-size: 10}
		.Estilo12 {font-size: 8}
        .Estilo13 {font-size: 10px; color: #666666; 
		
		</style>
<body>
".cabecera_formato("tipo","FORMATO INGRESO AL DATACENTER",45)."
	<table border cellspacing='0' width='100%' align='left'>
		
		<tr>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>FECHA</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>RESPONSABLE</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>RECURSO</span></th>
			<th scope='col' width='20%' bgcolor='#CCCCCC' ><span class='Estilo4'>RUTA</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>NOMBRE SUPERVISOR</span></th>
			<th scope='col' bgcolor='#CCCCCC' colspan='2'><span class='Estilo4'>SUPERVISIÓN</span></th>
			<th scope='col' bgcolor='#CCCCCC' ><span class='Estilo4'>FIRMA</span></th>
		</tr>";
	for($i=0;$i<30;$i++)
	{ $html = $html."<tr> <td height='30'></td>
			<td height='30'></td>
			<td height='30'></td>
			<td height='30'></td>
			<td height='30'></td>			
			<td height='30' width='5%' class='Estilo11'>Aprobación</td>
			<td height='30'width='10%'></td>
			<td height='30'></td>
			
		</tr>"; }
		$html = $html."<tr>
		<td height='30' class='Estilo12' colspan='8'>
			En la parte de supervisión poner Ok si la supervición es positiva y poner una X si es negativa.
		</td>
	</tr>";
	$html = $html."</table> </body></html>";
	echo $html;
	
}

function lista_chequeo_venta_vehiculo()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='lista_chequeo_venta_vehiculo_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function gendoc_chequeo_antes_de_marcha()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_chequeo_antes_de_marcha_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function formato_reintegro_gastos()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='formato_reintegro_gastos_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function control_permisos()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='control_permisos_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function requisicion_personal()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='requisicion_personal_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function formato_acceso_datacenter()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='formato_acceso_datacenter_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function auditoria_backup()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='auditoria_backup_ok'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function acoso_laboral()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='acoso_laboral_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function autoreporte_condiciones_inseguras()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc2.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='autoreporte_condiciones_inseguras_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}

function formato_solicitud_gestion_humana()
{
	global $consecutivo,$tipo;
	$Tipo=qo("select * from tipo_formatoaoa where id=$tipo");
	$USUARIO=$_SESSION['User'];
	$NUSUARIO=$_SESSION['Nombre'];
	$Email_usuario=$_SESSION['Email'];
	$Empleado=qo("select * from empleado where correo_e='$Email_usuario' ");
	$NCargo='';
	if($Empleado->activo)
	{
		if($Contrato=qo("select * from gh_contrato where empleado=$Empleado->id and fecha_final='0000-00-00' "))
		{
			if($Cargo=qo("select * from gh_hcargo where contrato=$Contrato->id  and fecha_final='0000-00-00' "))
			{
				$NCargo=qo1("select nombre from gh_cargo where id=$Cargo->cargo");
			}
		}
	}
	html('GENERADOR DE FORMATOS - AOA');
	echo "<script language='javascript'>
	function generar_documento()
	{
	document.forma.continuar.style.visibility='hidden';
	document.forma.submit();
	}</script>
	<body><h3>GENERADOR DE FORMATOS AOA - $Tipo->nombre</H3>
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='formato_solicitud_gestion_humana_ok_2'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}
// *********** ----------------- **************** -------------------  *********** ----------------- **************** -------------------  *********** ----------------- **************** -------------------  
?>