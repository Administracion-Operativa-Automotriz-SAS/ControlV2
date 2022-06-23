<?php
include('inc/funciones_.php');
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');die();}
$Cantidad=coma_format(qo1("select count(id) from teleone_mensual"));
html('CONTOL DE CONSUMO TELEONE');
echo "<body><h3>CONTROL DE CONSUMO AOA - TELEONE</H3>
		Este programa se utiliza para hacer el cruce de información entre lo facturado por Teleone y la base de datos de siniestros.<br /><br />
		La información que se carga debe ser un archivo en formato csv (DOS) separado por puntos y comas. Los campos requeridos son:DESDE; NUMERO MARCADO ; DESCRIPCION; FECHA-HORA; TIEMPO.<br />
		No deben existir mas columnas. Un ejemplo del archivo es el siguiente:<br /><br />

		109;5715897547;Armenia;2011-04-30 14:01:17;2;<br />
		105;573203494595;Armenia;2011-04-30 13:43:25;3;<br />
		105;5715897545;Armenia;2011-04-30 13:41:17;3;<br />
		102;5715897547;Armenia;2011-04-30 13:28:18;4;<br /><br />

		Este archivo se puede producir a partir de la hoja electrónica que envía Teleone como soporte de la facturación. Se borran los títulos y resumen dejando solo el detalle y deben quedar las columas:
		<b>From</b> (Desde), <b>To</b> (numero marcado),<b>Description</b> (Descripción), <b>Date/Time </b> (Fecha - Hora), y <b>Charged time min/sec</b> (Tiempo al aire). Las demás columas se borran. Luego se ingresa a la opción: Archivo -
		Guardar Como - Otros Formatos - En Tipo se selecciona: CSV (MS DOS) *.csv y se le da nombre al archivo y se guarda en el disco local. Luego se carga este archivo en el
		siguiente diálogo.<br /><br />
		El archivo se puede cargar todas las veces que sea necesario, siempre se borrará la información de la tabla de trabajo y se volverá a alimentar automáticamente.<br /><br />Se podrá observar
		el resultado del análisis en el informe <b>CALLCENTER - Análisis Teleone</b><br /><br />

		<form action='zteleone.php' method='post' target='_self' enctype='multipart/form-data' name='forma' id='forma'>
		Seleccione el archivo CSV a cargar: <input name='archivo' type='file' id='archivo' size='80'><br />
		<input type='submit' value='Cargar Plano'>
		<input type='hidden' name='Acc' value='cargar_plano'>
		</form><br /><br />Cantidad de registros actualmente en la tabla de Teleone: $Cantidad<br /><br />
		<input type='button' value='Inicializar control de análisis' onclick=\"modal('zteleone.php?Acc=inicializar_analisis',0,0,10,10,'inicializar');\">
		<input type='button' value='Archivo ya cargado, solamente procesar' onclick=\"window.open('zteleone.php?Acc=separar_datos_siniestros','_self');\">
		</BODY>";

function inicializar_analisis()
{
	q("update teleone_mensual set analizado=0");
	echo "<body><script>window.close();void(null);</script ></body>";
}

function cargar_plano()
{
	global $archivo;
	if(is_uploaded_file($archivo))
	{
		$Destino='planos/teleone.csv';
		if(file_exists($Destino)) unlink($Destino);
		copy($archivo, $Destino);
		echo "<body><script language='javascript'>alert('Archivo $archivo subido exitosamente');
			window.open('zteleone.php?Acc=alimentar_tabla','_self');
			</script>";
	}
}

function alimentar_tabla()
{
	q("truncate table teleone_mensual");
	q("load data local infile 'planos/teleone.csv' into table teleone_mensual fields terminated by ';' (desde,numero,descripcion,fecha,minutos)");
//	q("alter table teleone_mensual add key llave1 (numero) ");
	echo "<body><script language='javascript'>alert('Carga de datos en la tabla realizada.');
			window.open('zteleone.php?Acc=separar_datos_siniestros','_self');</script></body>";
}

function separar_datos_siniestros()
{
	html("Analisis Teleone");
	echo "<body>";
	$Hoy=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-180)));
	echo "Fecha de control: $Hoy<br />";
	q("drop table if exists tmpi_sin_teleone");
	echo "Creando tabla temporal: <br />";
	q("create table tmpi_sin_teleone select id,concat(declarante_telefono,' ',declarante_tel_resid,' ',declarante_tel_ofic,' ',declarante_celular,' ',declarate_tel_otro) as declarante,
		concat(conductor_tel_resid,' ',conductor_tel_ofic,' ',conductor_celular,' ',conductor_tel_otro) as conductor
		,actualizacion_aseg, observaciones from siniestro where date_format(ingreso,'%Y-%m-%d')>='$Hoy' ");
	echo "Creando indices: <br />";
	q("alter table tmpi_sin_teleone add primary key id (id)");
	q("alter table tmpi_sin_teleone add key llave1 (declarante) ");
	q("alter table tmpi_sin_teleone add key llave2 (conductor) ");
	//q("alter table tmpi_sin_teleone add key llave3 (actualizacion_aseg) ");
	echo "Registro analizado: <span id='indicador'></span><br /><br />
			<iframe name='Oculto_analisis' src='zteleone.php?Acc=analizar_datos' height='1'></iframe></body>";
}

function analizar_datos()
{
	if($Datos=q("select * from teleone_mensual where analizado=0 order by id limit 500"))
	{
		html();
		echo "<script language='javascript'>
				function actualiza(dato)
				{
					parent.document.getElementById('indicador').innerHTML='FASE 1: '+dato;
				}
			</script>
			<body><h3>Procesando registros..</h3>";
		require('inc/link.php');
		$Numero_ya=',,';
		while($D=mysql_fetch_object($Datos))
		{
			if(!strpos($Numero_ya,','.$D->numero.','))
			{
				$Numero=substr($D->numero,2);
				if(strlen($Numero)<10) $Numero=substr($Numero,1);

				if($Sin=qom("select id from tmpi_sin_teleone where declarante like '%$Numero%' or conductor like '%$Numero%' limit 1",$LINK))
				{
					mysql_query("update teleone_mensual set siniestro=$Sin->id where numero='$D->numero' ",$LINK);
				}
				mysql_query("update teleone_mensual set analizado=1 where numero='$D->numero' ",$LINK);
				$Numero_ya.=$D->numero.',';
			}
			echo "<script language='javascript'>actualiza($D->id);</script>";
		}
		mysql_close($LINK);
		echo "<script language='javascript'>window.open('zteleone.php?Acc=analizar_datos','_self');</script></body>";
		die();
	}
	q("update teleone_mensual set analizado=0 where siniestro=0");
	echo "<body><script language='javascript'>window.open('zteleone.php?Acc=analizar_datos2','_self')</script></body>";
}

function analizar_datos2()
{
	if($Datos=q("select * from teleone_mensual where analizado=0 order by id limit 300"))
	{
		html();
		echo "<script language='javascript'>
				function actualiza(dato)
				{
					parent.document.getElementById('indicador').innerHTML='FASE 2: '+dato;
				}
			</script>
			<body><h3>Procesando registros..(analisis 2) </h3>";
		require('inc/link.php');
		while($D=mysql_fetch_object($Datos))
		{
			$Numero=substr($D->numero,2);
			if(strlen($Numero)<10) $Numero=substr($Numero,1);

			if($Sin=qom("select id from tmpi_sin_teleone where actualizacion_aseg like '%$Numero%' or observaciones like '%$Numero%' limit 1",$LINK))
			{
				mysql_query("update teleone_mensual set siniestro=$Sin->id where numero='$D->numero' ",$LINK);
			}
			mysql_query("update teleone_mensual set analizado=1 where numero='$D->numero' ",$LINK);
			echo "<script language='javascript'>actualiza($D->id);</script>";
		}
		mysql_close($LINK);
		echo "<script language='javascript'>window.open('zteleone.php?Acc=analizar_datos2','_self');</script></body>";
		die();
	}
	echo "<body><script language='javascript'>parent.document.getElementById('indicador').innerHTML+=' FIN DEL PROCESO';</script></body>";
}

























?>