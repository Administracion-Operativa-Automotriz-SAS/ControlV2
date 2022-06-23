<?php

/**** PROGRAMA PARA VERIFICAR INCONSISTENCIAS EN LAS GARANTIAS ****/

/* 
	El objetivo principal es verificar si existen las mismas cuentas con distintas cédulas o las mismas cédulas con cuentas distintas  o cédulas con nombres distintos.
	Adicionalmente hacer la misma verificación contra la base de datos administrativa en el área de proveedores.
*/

include('inc/funciones_.php');
sesion();
$USUARIO=$_SESSION['User'];
include('zfunciones_autorizaciones.php');
include_once('inc/Crypt.php');


if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}

inicio_verificacion();

function inicio_verificacion()
{
	html('VERIFICACION DE GARANTIAS');
	echo "<script language='javascript'>
	function click_continuar()
	{	document.forma.submit();}
	</script><h3>SISTEMA DE AUDITORIA DE GARANTIAS</H3>
	<form action='zauditoria_garantias.php' target='Tablero_garantias' method='POST' name='forma' id='forma'>
		Fecha Inicial: ".pinta_FC('forma','FI',primer_dia_de_mes(date('Y-m-d')))." Fecha final: ".pinta_FC('forma','FF',date('Y-m-d'))." 
		
		Tipo de consulta: <select name='Acc' >
			<option value='cuentas_distintas'>Cédulas con Cuentas distintas</option>
			<option value='cedulas_distintas'>Cuentas con Cédulas distitnas</option>
		</select>
		<input type='button' name='continuar' id='continuar' value=' CONTINUAR ' onclick='click_continuar();'>
	</form><iframe name='Tablero_garantias' id='Tablero_garantias' style='visibility:visible' width='100%' frameborder='no' height='80%'></iframe>
	</body>";
}

function cuentas_distintas()
{
	global $FI,$FF,$USUARIO;
	$tmp='tmpi_ag_'.$USUARIO.'_'.$_SESSION['Id_alterno'];
	html();
	echo "
	<script language='javascript'>
	function estado(dato) {document.getElementById('estatus').innerHTML=dato;}
	function modificar_devol_cuenta(id){modal('zauditoria_garantias.php?Acc=modificar_devol_cuenta&id='+id,0,0,500,500,'mdc');}
	function limpiar_devol_cuenta(id){if(confirm('Seguro de limpiar la cuenta de devolucion?')) modal('zauditoria_garantias.php?Acc=limpiar_devol_cuenta&id='+id,0,0,500,500,'mdc');}
	</script>
	<body><h3>Rango de fechas: $FI - $FF</h3>
	<span id='estatus' style='color:blue;font-weight:bold;'></span>
	";
	/*** CONSULTA DE LA TABLA DE GARANTIAS PARA BUSCAR CUENTAS BANCARIAS CON CEDULAS DISTINTAS. ***/
	echo "<script language='javascript'>estado('CONSULTANDO INFORMACION..');</script>";
	q("drop table if exists tmpi_g_repetidos_previo");
	q("create table tmpi_g_repetidos_previo select distinct identificacion_devol,devol_cuenta_banco from sin_autor where 
		date_format(fecha_solicitud,'%Y-%m-%d') between '$FI' and '$FF' and devol_cuenta_banco!='' order by identificacion_devol");
	q("drop table if exists tmpi_g_repetidos");
	q("create table tmpi_g_repetidos select identificacion_devol, count(devol_cuenta_banco) as cantidad
			FROM tmpi_g_repetidos_previo GROUP BY identificacion_devol ORDER BY identificacion_devol");
	if($Dobles=q("select t.identificacion_devol,t.cantidad FROM tmpi_g_repetidos t where t.cantidad>1 and t.identificacion_devol!=0 order by t.identificacion_devol"))
	{
		include('inc/link.php');
		echo "<table border cellspacing='0' width=1800>";
		while($D=mysql_fetch_object($Dobles))
		{
		//	echo "<tr><td>$D->identificacion_devol</td><td align='right'>$D->cantidad</td></tr>
		//				<tr><td colspan=2>";
			$Registros=mysql_query("select *,t_siniestro(siniestro) as nsin,t_codigo_ach(devol_banco) as nban,
					t_franquisia_tarjeta(franquicia) as nfranq
					from sin_autor where identificacion_devol='$D->identificacion_devol' and devol_cuenta_banco!='' ",$LINK);			
			echo "<tr>
				<th width=200>Siniestro</th>
				<th width=200>Franquicia</th>
				<th width=200>Asegurado</th>
				<th width=200>Tarjeta Habiente</th>
				<th width=100>Banco</th>
				<th>Cuenta</th>
				<th>Identificación</th>
				<th width=100>Fecha Solicitud</th>
				<th>Estado</th>
				<th width=200>Procesado por</th>
				</tr>";
			while($R=mysql_fetch_object($Registros))
			{
				echo "<tr bgcolor='ffffff'>
						<td>$R->nsin</td>
						<td>$R->nfranq</td>
						<td>$R->nombre</td>
						<td>$R->devol_ncuenta</td>
						<td>$R->nban</td>
						<td>".
						($USUARIO==1?"<a style='cursor:pointer' onclick='modificar_devol_cuenta($R->id);'><img src='gifs/standar/edita_registro.png' border='0' alt='Modificar' title='Modificar'></a> 
						&nbsp;<a style='cursor:pointer' onclick='limpiar_devol_cuenta($R->id);'><img src='gifs/canasta.gif' border='0' alt='Limpiar' title='Limpiar'></a>&nbsp;":""). 
						"<span id='dcb$R->id'>$R->devol_cuenta_banco</span></td>
						<td align='right'>".coma_format($R->identificacion_devol)."</td>
						<td align='center'>$R->fecha_solicitud</td>
						<td align='center' bgcolor='".($R->estado=='R'?"ffdddd":($R->estado=='A'?'ddffcc':'ffffff'))."'>$R->estado</td>
						<td>$R->procesado_por</td>
						</tr>";
			}
			//echo "</tr>";
		}
		mysql_close($LINK);
		echo "</table>";
		echo "<script language='javascript'>estado('');</script>";
	}
	echo "</body>";
}

function recuperar_identificacion()
{
	if($Autorizaciones=q("select * from sin_autor where identificacion='' and numero='' and data!='' order by id limit 500"))
	{
		echo "<body> Recuperando id: ";
		include('inc/link.php');
		while($Au=mysql_fetch_object($Autorizaciones))
		{
			echo "$Au->id, ";
			$Rd=desencripta_data2($Au);
			$Au->identificacion=$Rd['identificacion'];
			$Au->funcionario=$Rd['funcionario'];
			mysql_query("update sin_autor set identificacion='$Au->identificacion',funcionario='$Au->funcionario' where id='$Au->id' ",$LINK);
		}
		mysql_close($LINK);
		echo "<script language='javascript'>window.open('zauditoria_garantias.php?Acc=recuperar_identificacion','_self');</script></body>";
	}
	else
	{
		echo "<body><b>Fin del procedimiento de recuperación de identificaciones</b></body>";
	}
}

function modificar_devol_cuenta()
{
	global $id;
	$D=qo("select devol_cuenta_banco from sin_autor where id=$id");
	html('MODIFICACION DE CUENTA');
	echo "<body><form action='zauditoria_garantias.php' target='_self' method='POST' name='forma' id='forma'>
		Cuenta: <input type='text' name='devol_cuenta_banco' id='devol_cuenta_banco' value='$D->devol_cuenta_banco' size='20' maxlength='20'>
		<br><input type='submit' name='continuar' id='continuar' value=' CONTINUAR '><input type='hidden' name='Acc' value='modificar_devol_cuenta_ok'>
		<input type='hidden' name='id' value='$id'>
	</form></body>";
}


function modificar_devol_cuenta_ok()
{
	global $id,$devol_cuenta_banco;
	q("update sin_autor set devol_cuenta_banco='$devol_cuenta_banco' where id='$id' ");
	graba_bitacora('sin_autor','M',"$id","devol_cuenta_banco");
	echo "<body><script language='javascript'>opener.document.getElementById('dcb$id').innerHTML='$devol_cuenta_banco';window.close();void(null);</script></body>";
}

function limpiar_devol_cuenta()
{
	global $id,$devol_cuenta_banco;
	q("update sin_autor set devol_cuenta_banco='' where id='$id' ");
	graba_bitacora('sin_autor','M',"$id","devol_cuenta_banco");
	echo "<body><script language='javascript'>opener.document.getElementById('dcb$id').innerHTML='';window.close();void(null);</script></body>";
}

function cedulas_distintas()
{
	html();
	echo "<body>";
	echo "<h3>Opción no desarrolada</h3>";
	echo "</body>";
}

?>