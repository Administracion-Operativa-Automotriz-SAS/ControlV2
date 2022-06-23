<?php

/**
 *  JEFE OPERATIVO AOA
 *
 *		activación de vehículos de flota AOA
 *
 * @version $Id$
 * @copyright 2010
 */

include('inc/funciones_.php');

if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}


function aprobar_pago()
{
	global $f,$fpp,$Usuario;
	$Factura = qo("select * from factura where id=$f");
	$Proveedor = qo1("select nombre from proveedor where id=$Factura->proveedor");
	$Oficina = qo1("select nombre from oficina where id=$Factura->oficina");
	$Concepto = qo1("select nombre from concepto_factura where id=$Factura->concepto");
	$Aprobado = qo1("select sum(valor) from factura_ap where factura=$f and anulada=0");
	if ($Aprobado) $Valor_a_pagar = $Factura->valor_a_pagar - $Aprobado;
	else $Valor_a_pagar = $Factura->valor_a_pagar;
	html('SISTEMA DE APROBACION DE PAGOS');
	echo "<body><h3><img src='../../Administrativo/img/LOGO_AOA_200.png'><br>SISTEMA DE APROBACION DE PAGOS</h3>
	Usuario: $Usuario Fecha: " . date('Y-m-d') . "
	<form action='operativo.php' target='_self' method='post' name='forma' id='forma'>
	<br><b>INFORMACION DE LA FACTURA:</b>
	<table border cellspacing=0 bgcolor='eeeeee'><tr><td>Oficina</td><td>$Oficina</td></tr>
	<tr><td>Proveedor:</td><td>$Proveedor</td></tr>
	<tr><td>Factura número:</td><td>$Factura->numero</td></tr>
	<tr><td>Concepto:</td><td>$Concepto</td></tr>
	<tr><td>Descripción:</td><td>$Factura->descripcion</td></tr>
	<tr><td>Descripción extendida:</td><td>$Factura->descripcion_exp</td></tr>
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
			<input type='hidden' name='Acc' value='confirma_autorizacion'><input type='hidden' name='Usuario' value='$Usuario'>
			<input type='hidden' name='idf' value='$f'>
			</form>";
	}
	if($Factura->factura_f) {echo "<h3>IMAGEN</H3><img src='../../Administrativo/$Factura->factura_f' border=1>"; }
	if ($Factura->factura1_f) { echo "<h3>IMAGEN ADICIONAL</H3><img src='../../Administrativo/$Factura->factura1_f' border=1>"; }
	if ($Factura->factura2_f) { echo "<h3>IMAGEN ADICIONAL</H3><img src='../../Administrativo/$Factura->factura2_f' border=1>"; }
	if ($Factura->factura3_f) { echo "<h3>IMAGEN ADICIONAL</H3><img src='../../Administrativo/$Factura->factura3_f' border=1>"; }

	ECHO "</body></html>";
}


function confirma_autorizacion()
{
	global $idf, $valor_aprobado, $fecha_de_pago,$Usuario;
	html('SISTEMA DE APROBACION DE PAGOS');
	$Factura = qo("select * from factura where id=$idf");
	$Proveedor = qo1("select nombre from proveedor where id=$Factura->proveedor");
	$Oficina = qo1("select nombre from oficina where id=$Factura->oficina");
	$Concepto = qo1("select nombre from concepto_factura where id=$Factura->concepto");
	$Aprobado = qo1("select sum(valor) from factura_ap where factura=$idf and anulada=0");
	if ($Aprobado) $Valor_a_pagar = $Factura->valor_a_pagar - $Aprobado;
	else $Valor_a_pagar = $Factura->valor_a_pagar;
	echo "<body>
	<h3><img src='../../Administrativo/img/LOGO_AOA_200.png'><br>SISTEMA DE APROBACION DE PAGOS</h3>
	Usuario: $Usuario Fecha: " . date('Y-m-d') . "
	<form action='operativo.php' target='_self' method='post' name='forma' id='forma'>
		<br><b>INFORMACION DE LA FACTURA:</b>
		<table border cellspacing=0 bgcolor='eeeeee'><tr><td>Oficina:</td><td>$Oficina</td></tr>
		<tr><td>Proveedor:</td><td>$Proveedor</td></tr>
		<tr><td>Factura número:</td><td>$Factura->numero</td></tr>
		<tr><td>Concepto:</td><td>$Concepto</td></tr>
		<tr><td>Descripción:</td><td>$Factura->descripcion</td></tr>
		<tr><td>Descripción extendida:</td><td>$Factura->descripcion_exp</td></tr>
		";
	if ($Factura->factura_f)
		echo "<tr><td>Ver la imagen de la factura:</td><td><a href='../../Administrativo/$Factura->factura_f' target='imagenfac'>Click aqui</a></td></tr>";
	else
		echo "<tr><td>Ver la imagen de la factura:</td><td>Imagen no cargada</td></tr>";
	if ($Factura->factura1_f) echo "<tr><td>Ver la imagen adicional de la factura:</td><td><a href='../../Administrativo/$Factura->factura1_f' target='imagenfac'>Click aqui</a></td></tr>";
	if ($Factura->factura2_f) echo "<tr><td>Ver la imagen adicional de la factura:</td><td><a href='../../Administrativo/$Factura->factura2_f' target='imagenfac'>Click aqui</a></td></tr>";
	if ($Factura->factura3_f) echo "<tr><td>Ver la imagen adicional de la factura:</td><td><a href='../../Administrativo/$Factura->factura3_f' target='imagenfac'>Click aqui</a></td></tr>";
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
		<input type='hidden' name='Usuario' value='$Usuario'>
		<input type='hidden' name='Acc' value='efectua_aprobacion'>
		</form></body></html>";
}

function efectua_aprobacion()
{
	global $f, $v, $a,$Usuario;
	$Hoy = date('Y-m-d');
	$Aprobado_por = $Usuario;
	if (!q("select id from factura_ap where factura='$f' and aprobado_por='$Aprobado_por' and fec_aprobacion='$Hoy' and fec_pago='$a' and valor='$v' "))
	{
		q("insert into factura_ap (factura,aprobado_por,fec_aprobacion,fec_pago,valor) values ($f,'$Aprobado_por','$Hoy','$a','$v')");
		q("update factura set con_aprobacion=1 where id=$f");
		html();
		echo "<script language='javascript'>
			function carga()
			{
				centrar(10,10);
				alert('La aprobacion se realizo con exito');
				window.close();void(null);
			}
		<body onload='carga()'></body>";
	}
	else
	{
		html();
		echo "<script language='javascript'>
			function carga()
			{
				centrar(10,10);
				alert('La aprobacion ya se habia realizado con exito');
				window.close();void(null);
			}
		<body onload='carga()'></body>";
	}
}

function mensaje_operativo()
{
	global $Mensaje;
	html('APROBACION');
	echo "<body>".base64_decode($Mensaje)."</body>";
}

?>