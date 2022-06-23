<?php
/////  DISTRIBUCION DE ASIENTOS CONTABLES
include('inc/funciones_.php');
sesion();
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');die();}

function distribuir_asiento()
{
	global $id;
	html('DISTRIBUCION DE UN ASIENTO CONTABLE');
	if($D=qo("select *,t_factura(factura) as nfac,t_puc(cuenta) as ncuenta,t_proveedor(tercero) as ntercero from fac_detalle where id=$id"))
	{
		echo "Factura: <b>$D->nfac</b><br>Cuenta: <b>$D->ncuenta</b><br>Concepto: <b>$D->concepto</b><br>Tercero: <b>$D->ntercero</b><br>
				Placa: <b>$D->placa</b> Base: <b>".coma_format($D->base)."</b> Valor: <b  style='color:000088'>".coma_format($D->debito)."</b> 
		<br><br>
		<form action='zdistribucioncontable.php' enctype='multipart/form-data'  target='Tablero_dist' method='POST' name='forma' id='forma'>
			Archivo a distribuir: <input type='file' name='userfile' >	<input type='submit' name='enviar' id='enviar' value=' ANALIZAR ' >
			<input type='hidden' name='Acc' value='analizar_archivo'><input type='hidden' name='id' value='$id'>
			<input type='hidden' name='MAX_FILE_SIZE' value='2000000'>
		</form>
		<iframe name='Tablero_dist' id='Tablero_dist' style='visibility:visible' width='100%' height='80%'></iframe>
		</html>";
	}
	else
	echo "<b>No hay información.</b>";
}

function analizar_archivo()
{
	global $userfile,$id;
	$directorio='planos';
	# Nombre del archivo temporal del thumbnail
	define("NAMETHUMB", "/tmp/thumbtemp"); //Esto en servidores Linux, en Windows podría ser:
	$name = $_FILES["userfile"]["name"];
	$type = $_FILES["userfile"]["type"];
	$tmp_name = $_FILES["userfile"]["tmp_name"];
	$size = $_FILES["userfile"]["size"];
  	$File_destino=$directorio.'/'.strtolower(str_replace(' ','_',$name));
    if(is_file($File_destino)) @unlink($File_destino);
	if (!@copy($tmp_name, $File_destino)) { die('error en copy file '.$File_destino); }
	// Guardamos todo en la base de datos
	html();
	echo "<body>Archivo cargado $File_destino ";
	sleep(1);
	echo "... Redirigiendo al analizador.<script language='javascript'>window.open('zdistribucioncontable.php?Acc=analizar_archivo2&id=$id&archivo=$File_destino','_self');</script></body>";
}

function analizar_archivo2()
{
	global $archivo,$id;
	html();
	echo "<script language='javascript'>
	function valida_formulario()
	{
		with(document.forma)
		{
			if(!alltrim(LetraPlaca.value)) {alert('Digite la letra correspondiente a la columa de las placas.');LetraPlaca.style.backgroundColor='ffffcc';LetraPlaca.focus();return false;}
			if(!alltrim(LetraValor.value)) {alert('Digite la letra correspondiente a la columa de los valores.');LetraValor.style.backgroundColor='ffffcc';LetraValor.focus();return false;}
			if(!Number(FilaInicial.value)) {alert('Digite la fila inicial de la distribución.');FilaInicial.style.backgroundColor='ffffcc';FilaInicial.focus();return false;}
			if(!Number(FilaFinal.value)) {alert('Digite la fila final de la distribución.');FilaFinal.style.backgroundColor='ffffcc';FilaFinal.focus();return false;}
			submit();
		}
	}</script><body><h3>ANALISIS DE ARCHIVO $archivo</h3>";
	echo "<form action='zdistribucioncontable.php' target='_self' method='POST' name='forma' id='forma'>
		<input type='hidden' name='Acc' value='analizar_archivo3'>
		<input type='hidden' name='archivo' value='$archivo'>
		<input type='hidden' name='id' value='$id'>
		Digite la Letra de la columna de placas: <input type='text' name='LetraPlaca' id='LetraPlaca' value='K' size='2' maxlength='3'><br>
		Digite la Letra de la columna de los valores: <input type='text' name='LetraValor' id='LetraValor' value='L' size='2' maxlength='3'><br>
		Digite el número de la fila inicial de la distribución: <input type='text' class='numero' name='FilaInicial' id='FilaInicial' value='8' size='5' maxlength='5'><br>
		Digite el número de la fila final de la distribución: <input type='text' class='numero' name='FilaFinal' id='FilaFinal' value='212' size='5' maxlength='5'><br>
		<input type='button' name='continuar' id='continuar' value=' CONTINUAR ' onclick='valida_formulario();'>
	</form>";
	
	echo "</body>";
}

function analizar_archivo3()
{
	global $id,$archivo,$LetraPlaca,$LetraValor,$FilaInicial,$FilaFinal;
	$A_letras=array('A'=>1,'B'=>2,'C'=>3,'D'=>4,'E'=>5,'F'=>6,'G'=>7,'H'=>8,'I'=>9,'J'=>10,'K'=>11,'L'=>12,'M'=>13,'N'=>14,'O'=>15,'P'=>16,'Q'=>17,'R'=>18,'S'=>19,'T'=>20,'U'=>21,'V'=>22,'W'=>23,'X'=>24,'Y'=>25,'Z'=>26);
	include('inc/excel2/reader.php');
	html();
	echo "<script language='javascript'>function validarplacas() {window.open('zdistribucioncontable.php?Acc=validar_placas','Validacion_placas');}
	function distribuir() {window.open('zdistribucioncontable.php?Acc=distribuir_asientos&id=$id','Validacion_placas');}</script>
	<body><h3>ANALISIS DE ARCHIVO</h3>
	Columna de Placas: <b>$LetraPlaca</b> Columna de Valores: <b>$LetraValor</b> De la fila <b>$FilaInicial</b> a la fila <b>$FilaFinal</b><br>";
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('CP1251');
	$data->read( $archivo);
	$Temporal='tmpi_distribucion_'.$_SESSION['Id_alterno'];
	q("drop table if exists $Temporal");
	q("create table $Temporal (id int not null auto_increment primary key,
		placa char(10) not null,
		valor int(10) default 0,
		descripcion varchar(200))");
	echo "<br>Alimentando la tabla temporal...";
	
	include('inc/link.php');
	for($i=$FilaInicial;$i<=$FilaFinal;$i++)
	{
		$Placa=$data->sheets[0]['cells'][$i][$A_letras[$LetraPlaca]];
		$Valor=$data->sheets[0]['cells'][$i][$A_letras[$LetraValor]];
		mysql_query("insert into $Temporal (placa,valor) values ('$Placa','$Valor')",$LINK);
	}
	unlink($data);
	mysql_close($LINK);
	if($Datos=q("select * from $Temporal"))
	{
		echo "<br>Tabla cargada: <table border cellspacing='0'><tr><th>#</th><th>Placa</th><th>Valor</th><th>Validación</th></tr>";
		$Contador=$Sumatoria=0;
		while($D=mysql_fetch_object($Datos))
		{
			$Contador++;
			echo "<tr><td>$Contador</td><td>$D->placa</td><td align='right'>".coma_format($D->valor)."</td>
				<td id='val_$D->placa'></td></tr>";
			$Sumatoria+=$D->valor;
		}
		echo "<tr><td colspan='2' align='center'>Total</td><td align='right'><b  style='color:000088'>".coma_format($Sumatoria)."</b></td></tr></table>";
		$R=qo("select *,t_factura(factura) as nfac,t_puc(cuenta) as ncuenta,t_proveedor(tercero) as ntercero from fac_detalle where id=$id");
		if($Sumatoria==$R->debito) echo "<b style='color:008800'>El valor de la distribución es correcto</b>"; else
		echo "<b style='color:550000'>El valor de la distribución No es correcto</b>";
		echo "<iframe name='Validacion_placas' id='Validacion_placas' style='visibility:visible' width='10' height='10'></iframe>
		<input type='button' name='validar_placas' id='validar_placas' value=' VALIDAR PLACAS ' onclick='validarplacas()'> 
		<input type='button' name='distribuir' id='distribuir' value=' DISTRIBUIR ASIENTO ' onclick='distribuir()'> 
		";
	}
	else
	{
		echo "<b>No se pudo cargar la información a la tabla temporal.</b>";
	}
}

function validar_placas()
{
	$Temporal='tmpi_distribucion_'.$_SESSION['Id_alterno'];
	if($Datos=q("select * from $Temporal"))
	{
		echo "
		<script language='javascript'>
		function pintar(id,dato)
		{
			parent.document.getElementById('val_'+id).innerHTML=dato;
		}
		</script>
		<body><script language='javascript'>";
		include('inc/link.php');
		while($D=mysql_fetch_object($Datos))
		{
			if($Vehiculo=qo("select * from aoacol_aoacars.vehiculo where placa='$D->placa' "))
			{
				if($Vehiculo->inactivo_desde=='0000-00-00')
				{
					echo "pintar('$D->placa','Ok');";
				}
				else
				{
					echo "pintar('$D->placa','Vehiculo fuera de servicio desde $Vehiculo->inactivo_desde');";
				}
			}
			else
			{
				echo "pintar('$D->placa','Vehiculo inexistente' );";
			}
		}
		mysql_close($LINK);
		echo "</script></body>";
	}
}

function distribuir_asientos()
{
	global $id;
	$R=qo("select *,t_factura(factura) as nfac,t_puc(cuenta) as ncuenta,t_proveedor(tercero) as ntercero from fac_detalle where id=$id");
	$Temporal='tmpi_distribucion_'.$_SESSION['Id_alterno'];
	if($Datos=q("select * from $Temporal"))
	{
		echo "
		<script language='javascript'>
		function pintar(id,dato){parent.document.getElementById('val_'+id).innerHTML=dato;}
		</script>
		<body><script language='javascript'>";
		include('inc/link.php');
		while($D=mysql_fetch_object($Datos))
		{
			mysql_query("insert into fac_detalle (factura,cuenta,concepto,tercero,placa,debito) values
			('$R->factura','$R->cuenta','$R->concepto','$R->tercero','$D->placa','$D->valor')",$LINK);
			echo "pintar('$D->placa','Asiento insertado.');";
		}
		mysql_close($LINK);
		echo "</script></body>";
	}
}

?>