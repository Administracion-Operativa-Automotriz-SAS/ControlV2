<?php

//echo "file finded";
include_once('inc/funciones_.php');
include_once('zgendoc_funciones.php');
sesion();
require_once('inc/DocGenerator/clsMsDocGenerator.php');

if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}
inicio_generador();

function inicio_generador()
{
	global $Nusuario;
	html('GENERADOR DE FORMATOS - AOA');
	echo "<body style='font-size:12px'>
	<h3>GENERADOR DE FORMATOS - AOA</h3>
	Estimado usuario(a) <b>$Nusuario</b>: Este módulo le permitirá generar las plantillas correspondientes a los distintos documentos oficialmente registrados en el Listado Maestro de Documentos y Formatos del 
	sistema de Gestión de Calidad de AOA.<br><br>
	Este programa llevará el consecutivo de cada formato cada vez que un usuario genere el documento. Todos los documentos son generados en formato .DOC <br><br>
	Al generar el documento se debe guardar y se puede abrir con cualquier procesador de texto (word, open office, etc.). <br><br>
	No se deben imprimir multiples copias del mismo consecutivo para mantener la trazabilidad. Cada vez que se vaya a usar un formato se debe generar a partir de este módulo.<br><br>
	
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		Seleccione el tipo de documento que desea generar:<br>
		".menu_search("TIPO","Select id,concat(sigla,' - ',nombre) from tipo_formatoaoa where rutina!='' and activo=1 order by sigla,nombre",0,1,"width:700px","size=10")."<br><br>
		<input type='submit' name='continuar' id='continuar' value=' CONTINUAR ' style='width:300px;height:60px;font-weight:bold;'>
		<input type='hidden' name='Acc' value='busca_rutina_formato'>
	</form></body>".javascript_search("TIPO");
}

function busca_rutina_formato()
{
	global $TIPO;
	if($TIPO)
	{
		$Formato=qo("select * from tipo_formatoaoa where id=$TIPO");
		$Consecutivo=qo1("select max(consecutivo) from formatos_aoa where tipo_formato=$TIPO")+1;
		if($Formato->rutina)
		{
			echo "<body>
			<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
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
		<input type='hidden' name='Acc' value='gendoc_memorando_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<table>
		<tr><td align='right'>Nombre de reunión:</td><td><input type='text' name='nombre_reunion' id='nombre_reunion' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>Lugar:</td><td><input type='text' name='lugar' id='lugar' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>Firma:</td><td><input type='text' name='firma' id='firma' value='$NUSUARIO' size='100' maxlength='100'></td></tr>
		<tr><td align='right'>Cargo:</td><td><input type='text' name='cargo' id='cargo' value='$NCargo' size='100' maxlength='100'></td></tr>
		</table>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_formato_acta_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_prestamo_documentos_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_formato_normograma_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_acta_eliminacion_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_tabla_retencion_documental_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_inventario_entrega_documental_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_rev_aprob_docum_y_formatos_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_listado_maestro_docyform_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_solicitud_manejo_documentos_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		Nombre del empleado: <input type='text' name='nombre_empleado' id='nombre_empleado' value='' size='50' maxlength='50' onkeyup='this.value=this.value.toUpperCase();'><br>
		Fecha de realización de la inducción: ".pinta_FC('forma','fecha_induccion',date('Y-m-d'))."<br>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_induccion_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_programa_capacitacion_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		Nombre del empleado: <input type='text' name='nombre_empleado' id='nombre_empleado' value='' size='50' maxlength='50' onkeyup='this.value=this.value.toUpperCase();'><br>
		Firma: <input type='text' name='firma' id='firma' value='$NUSUARIO' size='100' maxlength='100'><br>
		Cargo: <input type='text' name='cargo' id='cargo' value='$NCargo' size='100' maxlength='100'><br>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_verificacion_referencias_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_registro_capacitacion_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_eval_periodo_prueba_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_requisitos_ingreso_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_aprobacion_anticipo_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_solicitud_anticipo_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_bys_calidad_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_legalizacion_anticipo_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_lista_convenios_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_definicion_bys_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_informe_auditoria_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_lista_chequeo_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_plan_auditoria_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_programacion_auditoria_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_acciones_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_consolidado_acciones_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_programa_mantenimiento_preventivo_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<table>
		<tr><td align='right'>Dirigido a:</td><td><input type='text' name='para' id='para' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>Cargo:</td><td><input type='text' name='cargo_para' id='cargo_para' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>Empresa:</td><td><input type='text' name='empresa' id='empresa' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>Dirección:</td><td><input type='text' name='direccion' id='direccion' value='' size='80' maxlength='100' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();'></td></tr>
		<tr><td align='right'>Asunto:</td><td><input type='text' name='asunto' id='asunto' value='' size='80' maxlength='100'></td></tr>
		<tr><td align='right'>De:</td><td><input type='text' name='de' id='de' value='$NUSUARIO' size='80' maxlength='100'></td></tr>
		<tr><td align='right' valign='top'>Texto:</td><td><textarea name='contenido' cols=100 rows=15></textarea></td></tr>
		<tr><td align='right'>Firma:</td><td><input type='text' name='firma' id='firma' value='$NUSUARIO' size='100' maxlength='100'></td></tr>
		<tr><td align='right'>Cargo:</td><td><input type='text' name='cargo' id='cargo' value='$NCargo' size='100' maxlength='100'></td></tr>
		</table>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_comunicacion_externa_ok'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_actaentrega_equipos_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='gendoc_solicitud_vacaciones_ok'>
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
		<input type='hidden' name='Acc' value='formato_solicitud_gestion_humana_ok'>
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
		<input type='hidden' name='Acc' value='control_permisos_ok'>
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
		<input type='hidden' name='Acc' value='requisicion_personal_ok'>
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
	<form action='zgendoc.php' target='_self' method='POST' name='forma' id='forma'>
		<br><br><input type='button' name='continuar' id='continuar' value=' GENERAR DOCUMENTO $consecutivo ' style='font-size:16px;font-weight:bold;' onclick='generar_documento();'>
		<input type='hidden' name='Acc' value='formato_acceso_datacenter_ok'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
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
		<input type='hidden' name='Acc' value='lista_chequeo_venta_vehiculo_ok'>
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
		<input type='hidden' name='Acc' value='acoso_laboral_ok'>
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
		<input type='hidden' name='Acc' value='autoreporte_condiciones_inseguras_ok'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
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
		<input type='hidden' name='Acc' value='acta_entrega_venta_vehiculo_ok'>
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
		<input type='hidden' name='Acc' value='gendoc_chequeo_antes_de_marcha_ok'>
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
		<input type='hidden' name='Acc' value='gendoc_aprobacion_comite_compras_ok'>
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
		<input type='hidden' name='Acc' value='formato_reintegro_gastos_ok'>
		<input type='hidden' name='consecutivo' value='$consecutivo'>
		<input type='hidden' name='tipodoc' value='$tipo'>
	</form>";
	echo "</body>";
}
// *********** ----------------- **************** -------------------  *********** ----------------- **************** -------------------  *********** ----------------- **************** -------------------  
?>