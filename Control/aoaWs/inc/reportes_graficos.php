<?php

function rep_grafica()
{
	global $idreporte;
	html();
	$R=qo("select * from aqr_reporte where id=$idreporte");
	$Grafica=$R->grafica?'checked':'';
	echo "<body onload='centrar(600,400);'>".titulo_modulo("Parametros Gráficos")."
	<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		<table>
			<tr><td>Activar salida Gráfica</td><td><input type='checkbox' name='grafica' $Grafica></td></tr>
			<tr><td>Título</td><td><input type='text' name='grafica_titulo' value='$R->grafica_titulo' size='50' maxlength='100'></td></tr>
			<tr><td>Sub Título</td><td><input type='text' name='grafica_subtitulo' value='$R->grafica_subtitulo' size='50' maxlength='100'></td></tr>
			<tr><td>Alto de la Gráfica</td><td><input type='text' name='grafica_alto' value='$R->grafica_alto' size='4' maxlength='4'></td></tr>
			<tr><td>Ancho de la Gráfica</td><td><input type='text' name='grafica_ancho' value='$R->grafica_ancho' size='4' maxlength='4'></td></tr>
			<tr><td>Tipo de Gráfica</td><td><select name='grafica_tipo'>
				<option value='PIE' ".($R->grafica_tipo=='PIE'?' selected ':'').">PIE</option>
				<option value='BARRAS1' ".($R->grafica_tipo=='BARRAS1'?' selected ':'').">BARRAS</option>
				<option value='BARRAS2' ".($R->grafica_tipo=='BARRAS2'?' selected ':'').">BARRAS HORIZONTALES</option>
				<option value='BARRAS3' ".($R->grafica_tipo=='BARRAS3'?' selected ':'').">BARRAS MULTIPLES</option>
				<option value='BARRAS4' ".($R->grafica_tipo=='BARRAS4'?' selected ':'').">BARRAS ACUMULADAS</option>
				</select></td></tr>
			<tr><td>Script Adicional</td><td><textarea name='grafica_script' style='font-size:10;font-family:arial;' ROWS=5 COLS=80>$R->grafica_script</textarea></td></tr>
			<tr><td><input type='button' value='Grabar' onclick=\"valida_campos('forma','grafica_alto:n,grafica_ancho:n');\"></td>
			<td><input type='reset' value='Reiniciar los campos'></td></tr>
		</table>
		<input type='hidden' name='Acc' value='actualiza_grafica'>
		<input type='hidden' name='idreporte' value='$idreporte'>
	</form>
	</body>";
}

function actualiza_grafica()
{
	require('inc/gpos.php');
	$grafica=sino($grafica);
//		$grafica_script=addcslashes($_POST['grafica_script'],"\24");
//		$grafica_script=addslashes(addcslashes($_POST['grafica_script'],"\24"));
//		$grafica_script=addslashes($grafica_script);
	$grafica_script=addslashes(addcslashes($_POST['grafica_script'],"\24"));
	q("update aqr_reporte set grafica='$grafica', grafica_titulo='$grafica_titulo', grafica_subtitulo='$grafica_subtitulo', grafica_ancho='$grafica_ancho',
		grafica_alto='$grafica_alto' ,grafica_tipo='$grafica_tipo',grafica_script=\"$grafica_script\" where id=$idreporte");
	echo "<script language='javascript'>function carga()
	{
		parent.activa_edrep();
		//parent.location='reportes.php?Acc=menu_rep&idreporte=$idreporte';
	}</script>
	<body onload='carga()'></body>";}

function reporte_pinta_grafica($ID)
{
	$R=qo("select * from aqr_reporte where id=$ID");
	echo "<iframe name='grafica' src='reportes.php?Acc=reporte_pintar_grafica&ID=$ID' height=$R->grafica_alto width=$R->grafica_ancho frameborder='no' scrolling='auto'></iframe>";
}

function reporte_pintar_grafica()
{
	global $ID;
	$Datos=array();
	$Etiquetas=array();
	$Variablesd=explode(',',$_COOKIE['GCOOKIESD']);
	setcookie('GCOOKIESD',null,-1);
	foreach($Variablesd as $Variabled)
	{
		if($Variabled)
		{
			eval("\$Datos[]=\$_SESSION['SERIE_d_".$Variabled."'];");
		}
	}
	$Variablesl=explode(',',$_COOKIE['GCOOKIESL']);
	setcookie('GCOOKIESL',null,-1);
	foreach($Variablesl as $Variablel)
	{
		if($Variablel)
		{
			eval("\$EtiquetasX[]=\$_SESSION['SERIE_l_".$Variablel."'];");
		}
	}
	if(is_array($_SESSION['Etiquetas_series'])) $Etiquetas_series=$_SESSION['Etiquetas_series']; else $Etiquetas_series=array();
	$R=qo("select * from aqr_reporte where id=$ID");
	switch($R->grafica_tipo)
	{
		case 'PIE':grafica_pie($ID,$R->grafica_titulo,$R->grafica_subtitulo,$R->grafica_alto-20,$R->grafica_ancho-20,$Datos[0],$EtiquetasX[0]);break;
		case 'BARRAS1':grafica_barras1($ID,$R->grafica_titulo,$R->grafica_subtitulo,$R->grafica_alto-20,$R->grafica_ancho-20,$Datos[0],$EtiquetasX[0]);break;
		case 'BARRAS2':grafica_barras2($ID,$R->grafica_titulo,$R->grafica_subtitulo,$R->grafica_alto-20,$R->grafica_ancho-20,$Datos[0],$EtiquetasX[0]);break;
		case 'BARRAS3':grafica_barras3($ID,$R->grafica_titulo,$R->grafica_subtitulo,$R->grafica_alto-20,$R->grafica_ancho-20,$Datos,$EtiquetasX[0],$Etiquetas_series);break;
		case 'BARRAS4':grafica_barras4($ID,$R->grafica_titulo,$R->grafica_subtitulo,$R->grafica_alto-20,$R->grafica_ancho-20,$Datos,$EtiquetasX[0],$Etiquetas_series);break;
	}
}
## PIE
function grafica_pie($ID,$Tit,$Subt,$Alto,$Ancho,$Datos,$Labels)
{
	$GS=qo1("select grafica_script from aqr_reporte where id=$ID");
	include ("inc/graficos/jpgraph.php");
	include ("inc/graficos/jpgraph_pie.php");
	include ("inc/graficos/jpgraph_pie3d.php");
	$graph = new PieGraph($Ancho,$Alto,"auto");
	$graph->SetShadow();
	$graph->title->Set($Tit);
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->subtitle->Set($Subt);
	$p1 = new PiePlot3D($Datos);
	$p1->setAngle(60);
	$p1->SetSize(0.35);
	$p1->SetCenter(0.4,0.55);
	$p1->SetLegends($Labels);
	if($GS) eval($GS);
	$graph->Add($p1);
	$graph->Stroke();
}

## BARRAS HORIZONTALES
function grafica_barras2($ID,$Tit,$Subt,$Alto,$Ancho,$Datos,$Labels)
{
	$GS=qo1("select grafica_script from aqr_reporte where id=$ID");
	include ("inc/graficos/jpgraph.php");
	include ("inc/graficos/jpgraph_bar.php");

	$datay=array(1992,1993,1995,1996,1997,1998,2001);

	// Size of graph
	$width=$Ancho;
	$height=$Alto;

	// Set the basic parameters of the graph
	$graph = new Graph($width,$height,'auto');
	$graph->SetScale("textlin");

	$top = 80;
	$bottom = 30;
	$left = 120;
	$right = 30;
	$graph->Set90AndMargin($left,$right,$top,$bottom);

	// Nice shadow
	$graph->SetShadow();

	// Setup labels
	$graph->xaxis->SetTickLabels($Labels);

	// Label align for X-axis
	$graph->xaxis->SetLabelAlign('right','center','right');

	// Label align for Y-axis
	$graph->yaxis->SetLabelAlign('center','bottom');

	// Titles
	$graph->title->Set($Tit);
	$graph->subtitle->Set($Subt);

	// Create a bar pot
	$bplot = new BarPlot($Datos);
	$bplot->value->Show();
	$bplot->SetFillColor("orange");
	$bplot->SetWidth(0.5);
	$bplot->SetYMin(0);

	if($GS) eval($GS);

	$graph->Add($bplot);

	$graph->Stroke();
}

### BARRAS
function grafica_barras1($ID,$Tit,$Subt,$Alto,$Ancho,$Datos,$Labels)
{
	$GS=qo1("select grafica_script from aqr_reporte where id=$ID");
	include ("inc/graficos/jpgraph.php");
	include ("inc/graficos/jpgraph_bar.php");

	// Create the graph. These two calls are always required
	$graph = new Graph($Ancho,$Alto,"auto");
	$graph->SetScale("textlin");
	$graph->yaxis->scale->SetGrace(20);

	// Add a drop shadow
	$graph->SetShadow();

	// Adjust the margin a bit to make more room for titles
	$graph->img->SetMargin(50,40,20,40);

	// Create a bar pot
	$bplot = new BarPlot($Datos);

	// Adjust fill color
	$bplot->SetFillColor('orange');
	$bplot->SetShadow();
	$bplot->value->Show();
	#$bplot->value->SetFont(FF_ARIAL,FS_BOLD,10);
	#$bplot->value->SetAngle(45);
	$bplot->value->SetFormat('%0.1f');

	// Setup the titles
	$graph->title->Set($Tit);
	$graph->subtitle->Set($Subt);
	$graph->xaxis->SetTickLabels($Labels);
	$graph->xaxis->title->Set("X");
	$graph->yaxis->title->Set("Y");

	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
	if($GS) eval($GS);

	$graph->Add($bplot);
	// Display the graph
	$graph->Stroke();
}

####  BARRAS MULTIPLES
function grafica_barras3($ID,$Tit,$Subt,$Alto,$Ancho,$Datos,$Labels,$ETseries)
{

	$GS=qo1("select grafica_script from aqr_reporte where id=$ID");
	include ("inc/graficos/jpgraph.php");
	include ("inc/graficos/jpgraph_bar.php");
	$Colores=array('orange','blue','yellow','green','gray');
	$Mes=array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
	// Create the graph. These two calls are always required
	$graph = new Graph($Ancho,$Alto,"auto");
	$graph->SetScale("textlin");

	$graph->SetShadow();
	$graph->img->SetMargin(50,40,20,40);

	// Create the bar plots

	$arreglo='';
	for($id=0;$id<count($Datos);$id++)
	{
		$Datos_uno=$Datos[$id];
		eval('$b'.$id.'plot= new Barplot($Datos_uno);');
		eval('$b'.$id.'plot->SetFillColor("'.$Colores[$id].'");');
		eval('$b'.$id.'plot->SetShadow();');
		eval('$b'.$id.'plot->value->Show();');
		eval('$b'.$id.'plot->value->SetFormat("%0.1f");');
		eval('$b'.$id.'plot->SetLegend("'.$ETseries[$id].'");');
		$arreglo.=(strlen($arreglo)?',':'').'$b'.$id.'plot';
	}

	// Create the grouped bar plot
	eval('$gbplot = new GroupBarPlot(array('.$arreglo.'));');
	// ...and add it to the graPH


	// Setup the titles
	$graph->title->Set($Tit);
	$graph->subtitle->Set($Subt);
	$graph->xaxis->SetTickLabels($Labels);
	$graph->xaxis->title->Set("X");
	$graph->yaxis->title->Set("Y");
	$graph->yaxis->scale->SetGrace(20);
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

	if($GS) eval($GS);

	$graph->Add($gbplot);
	// Display the graph
	$graph->Stroke();
}

####  BARRAS ACUMULADAS
function grafica_barras4($ID,$Tit,$Subt,$Alto,$Ancho,$Datos,$Labels,$ETseries)
{
	$GS=qo1("select grafica_script from aqr_reporte where id=$ID");
	include ("inc/graficos/jpgraph.php");
	include ("inc/graficos/jpgraph_bar.php");
	$Colores=array('orange','blue','yellow','green','gray');
	// Create the graph. These two calls are always required
	$graph = new Graph($Ancho,$Alto,"auto");
	$graph->SetScale("textlin");

	$graph->SetShadow();
	$graph->img->SetMargin(50,40,20,40);

	// Create the bar plots

	$arreglo='';
	for($id=0;$id<count($Datos);$id++)
	{
		$Datos_uno=$Datos[$id];
		eval('$b'.$id.'plot= new Barplot($Datos_uno);');
		eval('$b'.$id.'plot->SetFillColor("'.$Colores[$id].'");');
		eval('$b'.$id.'plot->SetShadow();');
		eval('$b'.$id.'plot->value->Show();');
		eval('$b'.$id.'plot->value->SetFormat("%0.1f");');
		eval('$b'.$id.'plot->SetLegend("'.$ETseries[$id].'");');
		$arreglo.=(strlen($arreglo)?',':'').'$b'.$id.'plot';
	}

	// Create the grouped bar plot
	eval('$gbplot = new AccBarPlot(array('.$arreglo.'));');
	// ...and add it to the graPH


	// Setup the titles
	$graph->title->Set($Tit);
	$graph->subtitle->Set($Subt);
	$graph->xaxis->SetTickLabels($Labels);
	$graph->xaxis->title->Set("X");
	$graph->yaxis->title->Set("Y");
	$graph->yaxis->scale->SetGrace(20);
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

	if($GS) eval($GS);

	$graph->Add($gbplot);
	// Display the graph
	$graph->Stroke();
}
?>
