<?PHP
	// programa para presentar la documentación de calidad	
	
	include('inc/funciones_.php');
	sesion();
	//echo "soy la variable de sesión";
	//print_r($_SESSION); 
	html('SISTEMA DE GESTION DE CALIDAD');
	echo "<script language='javascript'>
		function descargar_archivo(dato,salida)
		{
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo='+dato+'&Salida='+salida,'Oculto_calidad');
		}
	</script>
	<body><h3>SISTEMA DE GESTION DE CALIDAD</h3><script language='javascript'>centrar();</script>
	<iframe name='Oculto_calidad' id='Oculto_calidad' style='visibility:hidden' width='1' height='1'></iframe>
	<table cellspacing='2'><tr><th>Proceso</th><th></th></tr>";
	$Procesos=q("select * from q_proceso order by id");
	include('inc/link.php');
	while($Pro=mysql_fetch_object($Procesos))
	{
		echo "<tr><td valign='top'>$Pro->nombre</td><td>";
		echo "<table>";
		$Documentos=mysql_query("select * from q_documento where proceso=$Pro->id",$LINK);
		if(mysql_num_rows($Documentos))
		{
			echo "<tr><td valign='top' >Documentos</td><td>
						<table cellspacing='2'><tr><th>Nombre</th><th>Codigo</th><th>Descarga</th><th>Generar<br>Formato</th></tr>"; 
			while($Doc=mysql_fetch_object($Documentos))
			{
				$Partes_ruta=pathinfo($Doc->archivo_f);$Salida=$Partes_ruta['basename'];
				echo "<tr><td width='300'>$Doc->nombre</td><td>$Doc->codigo</td><td align='center'><a style='cursor:pointer;' 
						onclick=\"descargar_archivo('$Doc->archivo_f','$Salida');\"><img src='gifs/standar/exportar.png' border='0'></a></td>
						<td><button>Generar</button></td></tr>";
			}
			echo "</table></td></tr>";
		}
		$Registros=mysql_query("select * from q_registro where proceso=$Pro->id",$LINK);
		if(mysql_num_rows($Registros))
		{
			echo "<tr><td valign='top'>Registros</td><td>
						<table cellspacing='2'><tr><th>Nombre</th><th>Codigo</th><th>Descarga</th></tr>"; 
			while($Reg=mysql_fetch_object($Registros))
			{
				$Partes_ruta=pathinfo($Reg->archivo_f);$Salida=$Partes_ruta['basename'];
				echo "<tr><td width='300'>$Reg->nombre</td><td>$Reg->codigo</td><td align='center'><a style='cursor:pointer;' 
						onclick=\"descargar_archivo('$Reg->archivo_f','$Salida');\"><img src='gifs/standar/exportar.png' border='0'></a></td></tr>";
			}
			echo "</table></td></tr>";
		}
		$Indicadores=mysql_query("select * from q_indicador where proceso=$Pro->id",$LINK);
		if(mysql_num_rows($Indicadores))
		{
			echo "<tr><td valign='top'>Indicadores</td><td>
						<table cellspacing='2'><tr><th>Nombre</th><th>Codigo</th><th>Descarga</th></tr>"; 
			while($Ind=mysql_fetch_object($Indicadores))
			{
				$Partes_ruta=pathinfo($Ind->archivo_f);$Salida=$Partes_ruta['basename'];
				echo "<tr><td width='300'>$Ind->nombre</td><td>$Ind->codigo</td><td align='center'><a style='cursor:pointer;' 
						onclick=\"descargar_archivo('$Ind->archivo_f','$Salida');\"><img src='gifs/standar/exportar.png' border='0'></a></td></tr>";
			}
			echo "</table></td></tr>";
		}
		$Acciones=mysql_query("select * from q_accion where proceso=$Pro->id",$LINK);
		if(mysql_num_rows($Acciones))
		{
			echo "<tr><td valign='top'>Acciones</td><td>
						<table cellspacing='2'><tr><th>Nombre</th><th>Codigo</th><th>Descarga</th><th>Generar</th></tr>"; 
			while($Acc=mysql_fetch_object($Acciones))
			{
				$Partes_ruta=pathinfo($Acc->archivo_f);$Salida=$Partes_ruta['basename'];
				echo "<tr><td width='300'>$Acc->nombre</td><td>$Acc->codigo</td><td align='center'><a style='cursor:pointer;' 
						onclick=\"descargar_archivo('$Acc->archivo_f','$Salida');\"><img src='gifs/standar/exportar.png' border='0'></a>
						</td><td><button>Generar</button></td></tr>";
			}
			echo "</table></td></tr>";
		}
		echo "</table>";
		echo "</td></tr>";
	}
	mysql_close($LINK);
	echo "</body>";
	
	
?>