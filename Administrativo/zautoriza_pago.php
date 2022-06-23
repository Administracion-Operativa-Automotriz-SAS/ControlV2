<?php
/* Programa para autorizar pagos
 *  solamente debe funcionar desde el perfil de gerencia
 *
 */
include('inc/funciones_.php');
sesion();
$Nusuario=$_SESSION['Nombre'];

if (!empty($Acc) && function_exists($Acc))
{
	eval($Acc . '();');
	die();
}

function solicitar_aprobacion()
{
	global $id;
	$Factura = qo("Select * from factura where id=$id");
	html();
	echo "<body onload='centrar(700,500);'>
	<form action='zautoriza_pago.php' method='post' target='_self' name='forma' id='forma'>
		<h3><b>SOLICITUD DE APROBACION DE PAGO</B></H3>
		<font style='font-size:16'>Fecha de vencimiento de esta factura: <b>$Factura->fecha_vence</b><br>
		Por favor seleccione la fecha programada de pago: " . pinta_FC('forma', 'FPP', $Factura->fecha_vence) . "<br><br>
		<input type='submit' value='ENVIAR LA SOLICITUD' >
		<input type='hidden' name='id' value='$id'>
		<input type='hidden' name='Acc' value='solicitar_aprobacion_enviar'>
	</form></body>";
}

function solicitar_aprobacion_enviar()
{
	global $id, $FPP,$Nusuario;
	$Factura = qo("Select * from factura where id=$id");
	//// SI EL TIPO DE DOCUMENTO TIENE AUTO APROBACIÓN CAMBIA EL PROCESO EN ESTE PUNTO
	$Tipo_documento=qo("select * from tipo_documento where id=$Factura->tipo_doc ");
	if($Tipo_documento->auto_aprobacion)
	{
		q("update factura set aprob_solicitada=1 where id=$id");
		header("location:zautoriza_pago.php?Acc=efectua_aprobacion&f=$id&v=$Factura->valor_a_pagar&a=$FPP");
		die();
	}
	$Proveedor = qo1("select nombre from proveedor where id=$Factura->proveedor");
	$Oficina = qo1("select nombre from oficina where id=$Factura->oficina");
	$Concepto = qo1("select nombre from concepto_factura where id=$Factura->concepto");
	$Remitente = usuario('email');
	$Aprobado = qo1("select sum(valor) from factura_ap where factura=$id and anulada=0");
	if ($Aprobado) $Saldo = $Factura->valor_a_pagar - $Aprobado;
	else $Saldo = $Factura->valor_a_pagar;
	Echo "<body>Remitente: $Remitente <br>";
	if ($Destinatario = qo("select email,nombre from usuario_gerencia where aprueba_pago=1"))
	{
		echo "Destinatario: $Destinatario->nombre $Destinatario->email<br>";

		$Contenido = "<body><b>Solicitud de aprobación de pago de la factura:</b><br><br>" .
		"<table border cellspacing=0 bgcolor='eeeeee'><tr><td>Oficina:</td><td><b>$Oficina</b></td></tr>" .
		"<tr><td>Número de factura:</td><td><b>$Factura->numero</b></td></tr>" .
		"<tr><td>Proveedor:</td><td><b>$Proveedor</b></td></tr>" .
		"<tr><td>Fecha de emisión:</td><td><b>$Factura->fecha_emision</b></td></tr><tr><td>Fecha vencimiento:</td><td><b>$Factura->fecha_vence</b></td></tr>" .
		"<tr><td>Concepto:</td><td><b>$Concepto</b></td></tr>" .
		"<tr><td>Descripcion:</td><td><b>$Factura->descripcion</b></td></tr>" .
		"<tr><td>Descripcion extendida:</td><td><b>$Factura->descripcion_ext</b></td></tr>" .
		"<tr><td>Valor Antes de iva:</td><td><b>$" . coma_format($Factura->valor_factura) . "</b></td></tr>" .
		"<tr><td>Valor del Iva:</td><td><b>$" . coma_format($Factura->iva) . "</b></td></tr>" .
		"<tr><td>Retención en la Fuente:</td><td><b>$" . coma_format($Factura->rete_fuente) . " [$Factura->porc_retefuente %]</b></td></tr>" .
		"<tr><td>Retención de Ica:</td><td><b>$" . coma_format($Factura->rete_ica) . " [$Factura->porc_reteica /1000]</b></td></tr>" .
		"<tr><td>Valor a pagar:</td><td><b>$ " . coma_format($Factura->valor_a_pagar) . "</b></td></tr>";
		if ($Aprobado)
			$Contenido .= "<tr><td>Aprobado con anterioridad:</td><td><b>$ " . coma_format($Aprobado) . "</b></td></tr>
										<tr><td>Saldo por aprobar: </td><td><b>$ " . coma_format($Saldo) . "</b></td></tr>";
		$Contenido .= "</table><br>";
		if ($Saldo)
		{
			$Ruta1="utilidades/Administrativo/operativo.php?Acc=aprobar_pago&f=$Factura->id&fpp=$FPP&Usuario=".qo1("select nombre from usuario_gerencia where aprueba_pago=1");
			$Contenido .= "Para aprobar esta factura, por favor de click en el siguiente link:<br>" .
			"<a href='http://app.aoacolombia.com/Administrativo/ze.php?i=" .
			base64_encode("zautoriza_pago.php?Acc=forma_autorizacion&f=$Factura->id&fpp=$FPP") .
			"' target='autorizacion_pago'>Click para autorizar factura</a><br><br>".
			"<a href=http://app.aoacolombia.com/ip.php?i=".base64_encode("\$Programa='$Ruta1';")."' target='autorizacion_pago'>Autorizar desde dispositivo movil</a>";
		}
		else
			$Contenido .= "Esta factura no requiere aprobación.";
		$Contenido.="</body>";

		$Envio=enviar_gmail($Remitente /*de */,
			$Nusuario /*Nombre de */ ,
			"$Destinatario->email,$Destinatario->nombre" /*destinatario*/ ,
			"" /*con copia*/,
			"Solicitud Aprobacion de Pago Factura $Factura->numero" /*Objeto */,
			$Contenido);

		$Envio2 = enviar_gmail("sistemas@aoacolombia.com"/*de */ ,
			'Gestion de Procesos AOA'/* nombre de*/ ,
			"$Remitente,$Nusuario" /*destinatario*/ ,
			"" /*con copia*/,
			"Solicitud aprobacion de pago Fac: $Factura->numero"/*subject*/ ,
			"<body>Se solicito autorizacion de la factura No. <b>$Factura->numero</b>.</body>"/*contenido*/);

		if ($Envio)
		{
			echo "
				<script language='javascript'>
					alert('Envio satisfactorio de la solicitud a $Destinatario->email, $Destinatario->nombre');
					window.close();void(null);
					opener.parent.location.reload();
				</script>
			";
			q("update factura set aprob_solicitada=1 where id=$id");
		}
		else
			echo " Fallo en el envio.";
	}
}

function forma_autorizacion()
{
	global $f, $fpp;
	$Factura = qo("select * from factura where id=$f");
	$Proveedor = qo1("select nombre from proveedor where id=$Factura->proveedor");
	$Oficina = qo1("select nombre from oficina where id=$Factura->oficina");
	$Concepto = qo1("select nombre from concepto_factura where id=$Factura->concepto");
	$Aprobado = qo1("select sum(valor) from factura_ap where factura=$f and anulada=0");
	if ($Aprobado) $Valor_a_pagar = $Factura->valor_a_pagar - $Aprobado;
	else $Valor_a_pagar = $Factura->valor_a_pagar;
	html('SISTEMA DE APROBACION DE PAGOS');
	echo "<body><h3><img src='img/LOGO_AOA_200.png'><br>SISTEMA DE APROBACION DE PAGOS</h3>
	Usuario: " . $_SESSION['Nombre'] . ' - ' . $_SESSION['Nick'] . " Fecha: " . date('Y-m-d') . "
		<form action='zautoriza_pago.php' target='_self' method='post' name='forma' id='forma'>
		<br><b>INFORMACION DE LA FACTURA:</b>
		<table border cellspacing=0 bgcolor='eeeeee'><tr><td>Oficina</td><td>$Oficina</td></tr>
		<tr><td>Proveedor:</td><td>$Proveedor</td></tr>
		<tr><td>Factura número:</td><td>$Factura->numero</td></tr>
		<tr><td>Concepto:</td><td>$Concepto</td></tr>
		<tr><td>Descripción:</td><td>$Factura->descripcion</td></tr>
		<tr><td>Descripción expandiad:</td><td>$Factura->descripcion_exp</td></tr>
		";
	echo "<tr><td>Valor de la factura:</td><td>$Factura->valor_a_pagar</td></tr>
		<tr><td>Fecha de emision:</td><td>$Factura->fecha_emision</td></tr>
		<tr><td>Fecha de vencimiento:</td><td>$Factura->fecha_vence</td></tr>";
	if ($Aprobado)
		echo "<tr><td>Valor a pagar de la factura:</td><td><b>$ " . coma_format($Factura->valor_a_pagar) . "</b></td></tr>
		<tr><td>Aprobado:</td><td><b>$ " . coma_format($Aprobado) . "</b></td></tr>";
	if ($Valor_a_pagar <= 0)
	{
		echo "</table><h3 align='center'><b>Esta factura no requiere autorizacion</b></h3></body>";
	}
	else
	{
		if (!$FPP) $FPP = $Factura->fecha_vence;
		echo "<tr><td>Valor que aprueba:</td><td><input type='text' name='valor_aprobado' value='$Valor_a_pagar' size=15 maxlength=15 style='font-size:12' class='numero'></td></tr>
			<tr><td>Fecha programada de pago:</td><td>" . pinta_FC('forma', 'fecha_de_pago', ($fpp?$fpp:$Factura->fecha_vence)) . "</td></tr></table>
			<br><input type='submit' value='CONTINUAR' style='font-weight:bold;width:100px;height:60px;'>
			<input type='hidden' name='Acc' value='confirma_autorizacion'>
			<input type='hidden' name='idf' value='$f'>
			</form>";
	}
	if($Factura->factura_f) { echo "<h3>IMAGEN</H3><img src='$Factura->factura_f' border=1>"; }
	if ($Factura->factura1_f) { echo "<h3>IMAGEN ADICIONAL</H3><img src='$Factura->factura1_f' border=1>"; }
	if ($Factura->factura2_f) { echo "<h3>IMAGEN ADICIONAL</H3><img src='$Factura->factura2_f' border=1>"; }
	if ($Factura->factura3_f) { echo "<h3>IMAGEN ADICIONAL</H3><img src='$Factura->factura3_f' border=1>"; }
	ECHO "</body></html>";
}

function confirma_autorizacion()
{
	global $idf, $valor_aprobado, $fecha_de_pago;
	html('SISTEMA DE APROBACION DE PAGOS');
	$Factura = qo("select * from factura where id=$idf");
	$Proveedor = qo1("select nombre from proveedor where id=$Factura->proveedor");
	$Oficina = qo1("select nombre from oficina where id=$Factura->oficina");
	$Concepto = qo1("select nombre from concepto_factura where id=$Factura->concepto");
	$Aprobado = qo1("select sum(valor) from factura_ap where factura=$idf and anulada=0");
	if ($Aprobado) $Valor_a_pagar = $Factura->valor_a_pagar - $Aprobado;
	else $Valor_a_pagar = $Factura->valor_a_pagar;
	echo "<body>
	<h3><img src='img/LOGO_AOA_200.png'><br>SISTEMA DE APROBACION DE PAGOS</h3>
	Usuario: " . $_SESSION['Nombre'] . ' - ' . $_SESSION['Nick'] . " Fecha: " . date('Y-m-d') . "
	<form action='zautoriza_pago.php' target='_self' method='post' name='forma' id='forma'>
		<br><b>INFORMACION DE LA FACTURA:</b>
		<table border cellspacing=0 bgcolor='eeeeee'><tr><td>Oficina:</td><td>$Oficina</td></tr>
		<tr><td>Proveedor:</td><td>$Proveedor</td></tr>
		<tr><td>Factura número:</td><td>$Factura->numero</td></tr>
		<tr><td>Concepto:</td><td>$Concepto</td></tr>
		<tr><td>Descripción:</td><td>$Factura->descripcion</td></tr>";
	if ($Factura->factura_f)
		echo "<tr><td>Ver la imagen de la factura:</td><td><a href='$Factura->factura_f' target='imagenfac'>Click aqui</a></td></tr>";
	else
		echo "<tr><td>Ver la imagen de la factura:</td><td>Imagen no cargada</td></tr>";
	if ($Factura->factura1_f) echo "<tr><td>Ver la imagen adicional de la factura:</td><td><a href='$Factura->factura1_f' target='imagenfac'>Click aqui</a></td></tr>";
	if ($Factura->factura2_f) echo "<tr><td>Ver la imagen adicional de la factura:</td><td><a href='$Factura->factura2_f' target='imagenfac'>Click aqui</a></td></tr>";
	if ($Factura->factura3_f) echo "<tr><td>Ver la imagen adicional de la factura:</td><td><a href='$Factura->factura3_f' target='imagenfac'>Click aqui</a></td></tr>";
	echo "<tr><td>Valor de la factura:</td><td><b>$ " . coma_format($Factura->valor_a_pagar) . "</b></td></tr>";
	if ($Aprobado)
		echo "<tr><td>Aprobado con anterioridad:</td><td><b>$ " . coma_format($Aprobado) . "</b></td></tr>";
	echo "<tr><td>Valor que aprueba:</td><td><b>$ " . coma_format($valor_aprobado) . "</b></td></tr>
		<tr><td>EN LETRAS:</td><td><b>" . enletras($valor_aprobado, 1, 'PESOS') . "---</b></td></tr>
		<tr><td>Fecha de pago aprobada:</td><td><b>$fecha_de_pago</b></td></tr></table>
		<BR><input type='submit' value='CONFIRMAR LA APROBACION' style='font-weight:bold;width:200px;height:60px;'>
		<input type='hidden' name='f' value='$idf'>
		<input type='hidden' name='v' value='$valor_aprobado'>
		<input type='hidden' name='a' value='$fecha_de_pago'>
		<input type='hidden' name='Acc' value='efectua_aprobacion'>
		</form></body></html>";
}

function efectua_aprobacion()
{
	global $f, $v, $a;
	$Hoy = date('Y-m-d');
	$Aprobado_por = $_SESSION['Nombre'];
	$Factura=qo("select id,tipo_doc,proveedor from factura where id=$f");
	$Tipo_documento=qo("select * from tipo_documento where id=$Factura->tipo_doc");
	if (!$Id_aprobacion=qo1("select id from factura_ap where factura='$f' and aprobado_por='$Aprobado_por' and fec_aprobacion='$Hoy' and fec_pago='$a' and valor='$v' "))
	{
		$Id_aprobacion=q("insert into factura_ap (factura,aprobado_por,fec_aprobacion,fec_pago,valor) values ($f,'$Aprobado_por','$Hoy','$a','$v')");
		q("update factura set con_aprobacion=1 where id=$f");
		html('APROBACION FACTURA x PAGAR');
		echo "<body><b>Aprobación Satisfactoria. Identificador de aprobación: $Id_aprobacion </b></body>";
	}
	else
	{
		html('APROBACION FACTURA x PAGAR .');
		echo "<body><b>Aprobación ya realizada. Identificador de aprobación: $Id_aprobacion </b></body>";
	}
	if($Tipo_documento->auto_aprobacion && $Tipo_documento->tipo_pago)
	{
		$Aprobado=qo("select * from factura_ap where id=$Id_aprobacion");
		if($Aprobado->anulada==0 && $Aprobado->girado==0)
		{
			q("update factura_ap set girado=1 where id=$Id_aprobacion");
			$Consecutivo=qo1("select max(comp_egreso) from pago where tipo=$Tipo_documento->tipo_pago")+1;
			$IDP=q("insert into pago (fecha,tipo,comp_egreso,valor) values ('$a',$Tipo_documento->tipo_pago,$Consecutivo,'$v') ");
			q("insert into dpago (pago,factura,valor,aprobacion,proveedor,abonado) values ($IDP,$f,$v,$Id_aprobacion,$Factura->proveedor,1)");
		}
	}
}

function envio_a_contabilidad()
{
	global $id;
	html();
	$Factura = qo("Select * from factura where id=$id");
	$Proveedor = qo1("select nombre from proveedor where id=$Factura->proveedor");
	$Oficina = qo1("select nombre from oficina where id=$Factura->oficina");
	$Concepto = qo1("select nombre from concepto_factura where id=$Factura->concepto");
	$Remitente = usuario('email');
	Echo "<body>Remitente: $Remitente <br>";
	if ($Contador = qo("select * from contador where activo=1"))
	{
		echo "Destinatario: $Contador->email<br>";

		$Contenido = nl2br($Contador->texto);
		$Contenido .= "<br><br>" .
		"<table border cellspacing=0><tr><td>Oficina:</td><td><b>$Oficina</b></td></tr>" .
		"<tr><td>Número de factura:</td><td><b>$Factura->numero</b></td></tr>" .
		"<tr><td>Proveedor:</td><td><b>$Proveedor</b></td></tr>" .
		"<tr><td>Fecha de emisión:</td><td><b>$Factura->fecha_emision</b></td></tr><tr><td>Fecha vencimiento:</td><td><b>$Factura->fecha_vence</b></td></tr>" .
		"<tr><td>Concepto:</td><td><b>$Concepto</b></td></tr>" .
		"<tr><td>Descripcion:</td><td><b>$Factura->descripcion</b></td></tr>" .
		"<tr><td>Valor Antes de iva:</td><td><b>$" . coma_format($Factura->valor_factura) . "</b></td></tr>" .
		"<tr><td>Valor del Iva:</td><td><b>$" . coma_format($Factura->iva) . "</b></td></tr>" .
		"<tr><td>Retención en la Fuente:</td><td><b>$" . coma_format($Factura->rete_fuente) . " [$Factura->porc_retefuente %]</b></td></tr>" .
		"<tr><td>Retención de Ica:</td><td><b>$" . coma_format($Factura->rete_ica) . " [$Factura->porc_reteica /1000]</b></td></tr>" .
		"<tr><td>Valor a pagar:</td><td><b>$ " . coma_format($Factura->valor_a_pagar) . "</b></td></tr></table><br>";
		$Contenido .= "Para bajar esta factura por favor de click en el siguiente link: <br><br>" .
		"<a href='http://app.aoacolombia.com/Administrativo/ze.php?i=" .
		base64_encode("zbajadoc.php?Archivo=$Factura->factura_f&Salida=FAC$Factura->numero.jpg") .
		"' target='_blank'>Click para descargar factura</a><br>";
		if ($Factura->factura1_f)
			$Contenido .= "<br><br>" .
			"<a href='http://app.aoacolombia.com/Administrativo/ze.php?i=" .
			base64_encode("zbajadoc.php?Archivo=$Factura->factura1_f&Salida=FAC2$Factura->numero.jpg") .
			"' target='_blank'>Click para descargar imagen adicional</a><br>";
		$Contenido . "<br><br>";
		$Contenido .= nl2br($Contador->cierre);

		$Envio = enviar_mail2($Remitente/* de */ ,
			$_SESSION['Nombre']/*nombre de */ ,
			$Contador->email/*para*/ ,
			'Envio imagen Factura ' . $Factura->numero/*subject*/ ,
			$Contenido/*contenido*/ ,
			$Contador->concopia);
		// $Envio2=enviar_mail($Remitente /* de */,
		// $_SESSION['Nombre'] /*nombre de */,
		// $Contador->concopia /*para*/,
		// 'Envio imagen Factura '.$Factura->numero /*subject*/ ,
		// $Contenido /*contenido*/);
		if ($Envio)
		{
			echo "
				<script language='javascript'>
					centrar(50,50);
					alert('Envio satisfactorio del correo');
					window.close();void(null);
					opener.parent.location.reload();
				</script>
			";
			$Hoy = date('Y-m-d H:i:s');
			q("update factura set fec_envio='$Hoy' where id=$id");
		}
		else
			echo " Fallo en el envio.";
	}
}

function solicitud_anulacion()
{
	global $id;
	$Factura = qo("Select * from factura where id=$id");
	$Proveedor = qo1("select nombre from proveedor where id=$Factura->proveedor");
	html('SOLICITUD ANULACION');
	echo "<body onload='centrar(500,400);'><form action='zautoriza_pago.php' method='post' target='_self' name='forma' id='forma'>
		<b>Seguro de anular la factura numero : $Factura->numero del proveedor $Proveedor?</b><br><br>
		<input type='submit' value='ANULAR FACTURA' style='font-weight:bold;width:200px;height:60px;'>
		<input type='button' value='CANCELAR' onclick='window.close();void(null);' style='font-weight:bold;width:200px;height:60px;'>
		<input type='hidden' name='Acc' value='solicitud_anulacion_ok'>
		<input type='hidden' name='id' value='$id'>
		</form></body>
	";
}

function solicitud_anulacion_ok()
{
	global $id;
	html();
	$Factura = qo("Select * from factura where id=$id");
	$Proveedor = qo1("select nombre from proveedor where id=$Factura->proveedor");
	$Oficina = qo1("select nombre from oficina where id=$Factura->oficina");
	$Concepto = qo1("select nombre from concepto_factura where id=$Factura->concepto");
	q("update factura set anulada=1 where id=$id");
	q("update factura_ap set anulada=1 where factura=$id");
	$Remitente = usuario('email');
	Echo "<body>Remitente: $Remitente <br>";
	if ($Contador = qo("select * from contador where activo=1"))
	{
		echo "Destinatario: $Contador->email<br>";

		$Contenido = nl2br($Contador->texto_anulacion);
		$Contenido .= "<br><br>" .
		"<table border cellspacing=0><tr><td>Oficina:</td><td><b>$Oficina</b></td></tr>" .
		"<tr><td>Número de factura:</td><td><b>$Factura->numero</b></td></tr>" .
		"<tr><td>Proveedor:</td><td><b>$Proveedor</b></td></tr>" .
		"<tr><td>Fecha de emisión:</td><td><b>$Factura->fecha_emision</b></td></tr><tr><td>Fecha vencimiento:</td><td><b>$Factura->fecha_vence</b></td></tr>" .
		"<tr><td>Concepto:</td><td><b>$Concepto</b></td></tr>" .
		"<tr><td>Descripcion:</td><td><b>$Factura->descripcion</b></td></tr>" .
		"<tr><td>Valor Antes de iva:</td><td><b>$" . coma_format($Factura->valor_factura) . "</b></td></tr>" .
		"<tr><td>Valor del Iva:</td><td><b>$" . coma_format($Factura->iva) . "</b></td></tr>" .
		"<tr><td>Retención en la Fuente:</td><td><b>$" . coma_format($Factura->rete_fuente) . " [$Factura->porc_retefuente %]</b></td></tr>" .
		"<tr><td>Retención de Ica:</td><td><b>$" . coma_format($Factura->rete_ica) . " [$Factura->porc_reteica /1000]</b></td></tr>" .
		"<tr><td>Valor a pagar:</td><td><b>$ " . coma_format($Factura->valor_a_pagar) . "</b></td></tr></table><br>";

		$Contenido .= nl2br($Contador->cierre);
		$Envio = enviar_mail2($Remitente/* de */ ,
			$_SESSION['Nombre']/*nombre de */ ,
			$Contador->email/*para*/ ,
			'Solicitud Anulación Factura ' . $Factura->numero/*subject*/ ,
			$Contenido/*contenido*/ ,
			$Contador->concopia);
		// $Envio=enviar_mail($Remitente /* de */,
		// $_SESSION['Nombre'] /*nombre de */,
		// $Contador->concopia /*para*/,
		// 'Solicitud Anulación Factura '.$Factura->numero /*subject*/ ,
		// $Contenido /*contenido*/);
		if ($Envio)
		{
			echo "
				<script language='javascript'>
					centrar(50,50);
					alert('Envio satisfactorio del correo');
					window.close();void(null);
					opener.parent.location.reload();
				</script>
			";
		}
		else
		{
			echo " Fallo en el envio.";
		}
	}
}

function misma_fecha()
{
	global $id;
	$D=qo("select pago,fecha_abono from dpago where id=$id");
	q("update dpago set fecha_abono='$D->fecha_abono' where pago='$D->pago' ");
	echo "<body>Fecha abono: $D->fecha_abono   Pago: $D->pago
	<script language='javascript'>window.close();opener.parent.location.reload();void(null);</script></body>";
}

?>