<?php
//DIE('APLICACION EN SUSPENCION POR 2 MINUTOS. Atte. Departamento de Tecnologia de Informacion.');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
header('Content-type: text/html; charset=utf-8');


include_once('../controllers/ReportController.php');

//prepara_rutinas($Acc);
//verificar_directorios();

if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}
//html(TITULO_APLICACION.' - '.$_SESSION['Nombre']);
//inicio();
//die();

$report = new ReportController();
$siniestros = null;
$ciudades = $report->cities();
//$cars = $report->cars();
//$citas = $report->citas();
//$tablesn = $report->tablesn();
$aseguradoras = $report->aseguradoras();
//$lineas = $report->vehiculos_linea();
//$seguimiento = $report->seguimiento();
//$columnas = $report->column_name();
//echo '<br>';
//echo '<br>';
//print_r($cars);
//echo $siniestros[0]->observaciones;
if(isset($_POST['flotaselect']))
{
	if($_POST['fecha1']!=null)
	{$siniestros = $report->siniestros($_POST['flotaselect'],$_POST['fecha1'],$_POST['fecha2']);}
	else{$siniestros = $report->siniestros($_POST['flotaselect'],null,null);}
	
}

function difference_hours($ingreso,$contacto)
{
	$date1 = strtotime($ingreso);
	$date2 = strtotime($contacto);
	$all = floor(($date2 - $date1) / 60);
	$d = floor ($all / 1440);
	$h = floor (($all - $d * 1440) / 60);
	$m = $all - ($d * 1440) - ($h * 60);
	//return $all;
	return $d." dias con ".$h." horas con ".$m." minutos";
}
?>

<html lang="en">
<head>
  <title>Informes Siniestro</title>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.min.js"></script> 
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="inc/css/headboot.css">
	
  <link href='http://fonts.googleapis.com/css?family=Cookie' rel='stylesheet' type='text/css'>
</head>
<style>
	.table-condensed{
	  font-size: 10px;
	}
	
</style>

<script src="//cdn.jsdelivr.net/webshim/1.14.5/polyfiller.js"> </script>
<script>
webshims.setOptions('forms-ext', {types: 'date'});
webshims.polyfill('forms forms-ext');
$.webshims.formcfg = {
en: {
    dFormat: '-',
    dateSigns: '-',
    patterns: {
        d: "yy-mm-dd"
    }
}
};

</script>


    
   
	
<body>

	<header class="header-basic">

	<div class="header-limiter">

		<h1><a href="#">Detalles de servicio prestados X <span>Fecha inicial</span></a></h1>
		
		<!--
		<nav>
			<a href="#">Home</a>
			<a href="#" class="selected">Blog</a>
			<a href="#">Pricing</a>
			<a href="#">About</a>
			<a href="#">Faq</a>
			<a href="#">Contact</a>
		</nav>
		-->
	</div>

</header>

<div class="menu">
	<div class="container">
		<form action="marcoindex2.php" onsubmit="return filter_form(this)" id="filter_data" method="post">
		<div class="form-group">
			<label>Aseguradora</label>
			<select class="form-control" name="flotaselect" onclick="excel_op(this.value)" required>
				<option value="">Selecciona</option>
				<?php foreach($aseguradoras as $aseguradora){ ?>
				<option value="<?php echo $aseguradora->id ?>"<?php if(isset($_POST['flotaselect']) and $aseguradora->id ==  $_POST['flotaselect']) {echo "selected";} ?>><?php echo utf8_decode($aseguradora->nombre) ?></option>
				<?php } ?>
			</select>
			<br>
			<label>Fecha Inicial</label>
			<input type="date" class="form-control" max=<?php echo date('Y-m-d'); ?>  name="fecha1" id="fecha1">
			<br>
			<label>Fecha Final</label>
			<input type="date" class="form-control" max=<?php echo date('Y-m-d'); ?>  name="fecha2" id="fecha2">
		</div>
		<button class="btn btn-warning form-control">Filtrar</button>
		</form>
		
		<form id="excel_data" onsubmit="return fill_excel_form_data(this)" action="controllers/ReportController.php" method="post">
			<input type="hidden" id="param1" name="excel_generate" >
			<input type="hidden" id="param2" name="param2" >
			<input type="hidden" id="param3" name="param3" >
			<button class="btn btn-success form-control" type="submit">Excel</button>
		</form>
		
		<br>
		<div style="max-width:1150px;"  class="table-responsive ocultar_400px">
			<table  class="table table-condensed  table-bordered" id="InformTable">
				<thead>
					<tr>
						<th>ID</th>
						<th>CIUDAD ATENCIÓN</th>
						<th>CIUDAD ORIGEN</th>
						<th>CIUDAD SINIESTRO</th>
						<th>INGRESO</th>
						<th>NOMBRE ASEGURADO</th>
						<th>NUMERO TELEFÓNICO</th>
						<th>PLACA</th>
						<th>COBERTURA</th>
						<th>CONTACTO</th>
						<th>SINIESTRO</th>
						<th>ENTREGA</th>
						<th>ANS.</th>
						<th>FECHA INICIO SERVICIO</th>
						<th>FECHA FIN</th>
						<th>PLACA DE LA CITA</th>
						<th>VEHICULO</th>
						<th>SUCURSAL RADICADORA</th>					
					</tr>
				</thead>
				<tbody style="text-align:center;">
					<?php if($siniestros!=null) {foreach($siniestros as $siniestro){ ?>
					<tr>
						<td><?php echo $siniestro->id?></td>
						<td><?php  if(isset($ciudades[$siniestro->ciudad])) echo $ciudades[$siniestro->ciudad]?></td>
						<td><?php  if(isset($ciudades[$siniestro->ciudad_original])) echo $ciudades[$siniestro->ciudad_original] ?></td>
						<td><?php  if(isset($ciudades[$siniestro->ciudad_siniestro])) echo $ciudades[$siniestro->ciudad_siniestro] ?></td>
						<td><?php echo $siniestro->ingreso ?></td>
						<td><?php echo $siniestro->asegurado_nombre ?></td>
						<td><?php echo $siniestro->declarante_celular ?></td>
						<td><?php echo $siniestro->placa?></td>
						<td><?php echo $siniestro->dias_servicio ?></td>
						<td><?php echo $report->cita_contacto($siniestro->id)?></td>
						<td><?php echo $siniestro->numero?></td>
						<td><?php echo $report->cita_entrega($siniestro->id)?></td>
						<td><?php echo difference_hours($siniestro->ingreso,$report->cita_contacto($siniestro->id));?></td>
						<td><?php echo $siniestro->fecha_inicial ?></td>
						<td><?php echo $siniestro->fecha_final ?></td>
						<td><?php echo $siniestro->placa ?></td>
						<td><?php echo $report->cita_placa($siniestro->id) ?></td>
						<td><?php echo $siniestro->sucursal_radicadora ?></td>
					</tr>
					<?php }} ?>
				</tbody>
			</table>
		</div>	
	</div>
</div>
	
</body>
<footer class="footer1">
<div class="container">

<div class="row"><!-- row -->
            
                
</div>
</footer>
<!--header-->
<style>

@import url(http://fonts.googleapis.com/css?family=Fjalla+One);
@import url(http://fonts.googleapis.com/css?family=Gudea);
.footer1 {
    background: #fff url("../images/footer/footer-bg.png") repeat scroll left top;
	padding-top: 40px;
	padding-right: 0;
	padding-bottom: 20px;
	padding-left: 0;/*	border-top-width: 4px;
	border-top-style: solid;
	border-top-color: #003;*/
}

.footerp p {font-family: 'Gudea', sans-serif; }


#social:hover {
    			-webkit-transform:scale(1.1); 
-moz-transform:scale(1.1); 
-o-transform:scale(1.1); 
			}
			#social {
				-webkit-transform:scale(0.8);
                /* Browser Variations: */
-moz-transform:scale(0.8);
-o-transform:scale(0.8); 
-webkit-transition-duration: 0.5s; 
-moz-transition-duration: 0.5s;
-o-transition-duration: 0.5s;
			}           
/* 
    Only Needed in Multi-Coloured Variation 
                                               */
			.social-fb:hover {
				color: #3B5998;
			}
			.social-tw:hover {
				color: #4099FF;
			}
			.social-gp:hover {
				color: #d34836;
			}
			.social-em:hover {
				color: #f39c12;
			}
			.nomargin { margin:0px; padding:0px;}

.footer-bottom {
    background-color: #15224f;
    min-height: 30px;
    width: 100%;
}

.copyright {
    color: #fff;
    line-height: 30px;
    min-height: 30px;
    padding: 7px 0;
}

.design a {
    color: #fff;
}

</style>
<div class="footer-bottom">

	<div class="container">

		<div class="row">

			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

				<div class="copyright">

					Reporte Siniestros

				</div>

			</div>

			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

				<div class="design">

					

				</div>

			</div>

		</div>

	</div>

</div>
<script>
     $('#InformTable').DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Spanish.json"}});
	 function excel_op(elem)
	 {
		 if(elem != null)
		 {
			 $("input[name='excel_generate']").val(elem);
		 }
	 }
	 
	 function fill_excel_form_data(elem)
	 {
		 event.preventDefault();
			console.log("param"+$("#param1").val());
			if($("#param1").val() == "")
			{
				alert("Seleccione una aseguradora");				
			}
			else{
				if($("#fecha1").val() != "" && $("#fecha2").val() != ""){$("#param2").val($("#fecha1").val());$("#param3").val($("#fecha2").val())}					
				document.getElementById("excel_data").submit();
			}
		 return false;
	 }
	 

	 
	$("#fecha1").click(function(){
		$("#fecha2").prop('required',true);
	});
	
	$("#fecha2").click(function(){	
		$("#fecha1").prop('required',true);
	});
	
</script>

</html>	
