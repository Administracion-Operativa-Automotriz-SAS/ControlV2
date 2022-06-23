<?php
//DIE('APLICACION EN SUSPENCION POR 2 MINUTOS. Atte. Departamento de Tecnologia de Informacion.');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
header('Content-type: text/html; charset=utf-8');

include_once('inc/funciones_.php');
sesion();
include_once('controllers/ReportController.php');


if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}


$report = new ReportController();
$siniestros = null;
$ciudades = $report->cities();

$aseguradoras = $report->aseguradoras();

$extrachevy = false;

if(isset($_POST['flotaselect']))
{
	if($_POST['flotaselect'] == "LA PREVISORA S.A" or $_POST['flotaselect'] == "M MAPFRE" or $_POST['flotaselect'] == "MAPFRE SEGUROS GENERALES DE COLOMBIA")
	{
		$extrachevy = true;
	}
	else{
		$extrachevy = false;
	}
	if($_POST['fecha1']!=null)
	{$siniestros = $report->siniestros($_POST['flotaselect'],$_POST['fecha1'],$_POST['fecha2']);}
	else{$siniestros = $report->siniestros($_POST['flotaselect'],null,null);}
	
}

if(isset($_POST['excel_generate']))
{
	if($_POST['excel_generate'] == "LA PREVISORA S.A" or $_POST['excel_generate'] == "M MAPFRE" or $_POST['excel_generate'] == "MAPFRE SEGUROS GENERALES DE COLOMBIA")
	{
		$extrachevy = true;
	}
	else{
		$extrachevy = false;
	}
	if($_POST['param2']!=null){
		$siniestros = $report->siniestros($_POST['excel_generate'],$_POST['param2'],$_POST['param3']);
		}
		else{$siniestros = $report->siniestros($_POST['excel_generate'],null,null);}
	
}

if(isset($_POST['finanparam1']))
{
	//print_r($_POST);
	$siniestros = $report->siniestros($_POST['finanparam1'],$_POST['finanparam2'],$_POST['finanparam3']);
	$tableamp = true;
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
	//return $d." dias con ".$h." horas con ".$m." minutos";
	return $h."h ".$m."";
}

function difference_days($ingreso,$salida)
{
	$date1 = strtotime($ingreso);
	$date2 = strtotime($salida);
	$all = floor(($date2 - $date1) / 60);
	$d = floor ($all / 1440);
	return $d;
	
}

function days_fin_res($dias1,$dias2)
{
	$res = $dias2-$dias1;
	if($res>=0)
	{
		return $res;
	}
	else{
		return "A verificar";
	}
}

?>

<html lang="en">
<head>
  <title>Informes Siniestro</title>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="inc/css/headboot.css">
	
  <link href='https://fonts.googleapis.com/css?family=Cookie' rel='stylesheet' type='text/css'>
  <script src="https://use.fontawesome.com/ba7765318c.js"></script>
</head>
<style>
	.table-condensed{
	  font-size: 10px;
	}
	#accordion{
		display:none;
	}
	.panel-heading {background-color: red!important}
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

<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" />
<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    
   
	
<body>

	<header class="header-basic">

	<div class="header-limiter">

		<h1><a href="#">Detalles de servicio prestados X <span>Fecha inicial</span></a></h1>
		


	</div>

</header>

<div class="menu">
	<div class="container">
		<?php
			if(isset($_POST['excel_attr']))  { 
				if($_POST['excel_attr'] == 1)  { $forms = 1; } else { $forms = 0;}
			}
			else{ $forms = 0;}
		 ?>
	
		<?php if( $forms == 0 and !isset($_POST['excel_generate'])) { ?>
		<form action="InformesSiniestros.php" onsubmit="return filter_form(this)" id="filter_data" method="post">
		<div class="form-group">
			<label>Aseguradora</label>
			<select class="form-control" name="flotaselect" onclick="excel_op(this.value)" onblur="finan_option()" required>
				<option value="">Selecciona</option>
				<?php foreach($aseguradoras as $aseguradora){ ?>
				<option value="<?php echo $aseguradora->razon_social ?>"<?php if(isset($_POST['flotaselect']) and $aseguradora->razon_social ==  $_POST['flotaselect']) {echo "selected";} ?>><?php echo utf8_encode($aseguradora->razon_social) ?></option>
				<?php } ?>
				<option value="MAPFRE">MAPFRE TODAS</option>
			</select>
			<br>
			<label class="form-control">Fecha Inicial</label>
			
			<label>Desde</label>
			<input type="date" class="form-control" max=<?php echo date('Y-m-d'); ?> <?php if(isset($_POST['fecha1'])){ ?> value="<?php echo $_POST['fecha1'] ?>" <?php } ?> name="fecha1" id="fecha1">
			<br>			
			<label>Hasta</label>
			<input type="date" class="form-control" max=<?php echo date('Y-m-d'); ?> <?php if(isset($_POST['fecha2'])){ ?>
			value="<?php echo $_POST['fecha2'] ?>" <?php } ?> name="fecha2" id="fecha2">
		</div>
		<button class="btn btn-warning form-control">Filtrar</button>
		</form>
		
		<form id="excel_data" onsubmit="return fill_excel_form_data(this)" action="InformesSiniestros.php" method="post">
			<input type="text" id="param1" name="excel_generate" >
			<input type="hidden" id="param2" <?php if(isset($_POST['fecha1'])){ ?> value="<?php echo $_POST['fecha1'] ?>" <?php } ?> name="param2" >
			<input type="hidden" id="param3" <?php if(isset($_POST['fecha2'])){ ?> value="<?php echo $_POST['fecha2'] ?>" <?php } ?> name="param3" >
			<button class="btn btn-success form-control" type="submit">Excel</button>
		</form>
		
		<br>
		<!--Accordeon-->
		<div id="accordion" class="panel-group">
			<div class="panel panel-default">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
				<div class="panel-heading ">
					<h4 class="panel-title">
						<strong><span><font color="white">Parametros financieros</font></span></strong>
						<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
					</h4>
				</div>
				</a>
				<div id="collapseOne" class="panel-collapse collapse">
					<form id="financial_form" method="post" action="InformesSiniestros.php">
						<input type="hidden" name="finanparam1" >
						<input type="hidden" id="finanparam2" name="finanparam2" >
						<input type="hidden" id="finanparam3" name="finanparam3" >
						<input type="hidden" id="excel_attr" name="excel_attr" value="0">
						<div class="panel-body">
							<div class="col-lg-6 col-md-6 col-sm-6">
								<label class="form-control">Cobertura 9</label>
								<label class="form-control">Cobertura 12</label>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6">
								<input class="form-control" min="1000" name="cober9" type="number" required>
								<input class="form-control" min="1000" name="cober12" type="number" required>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<select name='iva_val' class='form-control'>
									<option value="0.19">Iva del 19%</option>
									<option value="0.16">Iva del 16%</option>
								</select>
							</div>
						</div>						
					</form>
					<div class="col-lg-6 col-md-6 col-sm-6">
						<button class="btn btn-danger form-control" onclick="financial_form()">Filtrar con valores financieros</button>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6">
						<button class="btn btn-primary form-control" onclick="financial_excel()">Excel con valores financieros</button>
					</div>					
				</div>
			</div>    
		</div>
		<!--Accordeon-->
		<?php } ?>
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
						<th>CEDULA ASEGURADO</th>
						<th>NUMERO TELEFÓNICO</th>
						<th>PLACA</th>
						<th>VIGENCIA POLIZA(DESDE)</th>
						<th>VIGENCIA POLIZA(HASTA)</th>
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
						<th>ASEGURADORA</th>
						<th>DIAS SERVICIO</th>
						<th>DIFERENCIA</th>
						<?php if($extrachevy){?>
						<th>CHEVY SEGURO</th>
						<?php } ?>
						<?php if(isset($tableamp)){ ?>						
						<th>VALOR</th>
						<th>IVA</th>
						<th>TOTAL</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody style="text-align:center;">
					<?php if($siniestros!=null) {foreach($siniestros as $siniestro){ ?>
					<tr>
						<td><?php echo $siniestro->id?></td>
						<td><?php  if(isset($ciudades[$siniestro->ciudad])) echo utf8_encode($ciudades[$siniestro->ciudad])?></td>
						<td><?php  if(isset($ciudades[$siniestro->ciudad_original])) echo utf8_encode($ciudades[$siniestro->ciudad_original]) ?></td>
						<td><?php  if(isset($ciudades[$siniestro->ciudad_siniestro])) echo utf8_encode($ciudades[$siniestro->ciudad_siniestro]) ?></td>
						<td><?php echo $siniestro->ingreso ?></td>
						<td><?php echo utf8_encode($siniestro->asegurado_nombre) ?></td>
						<td><?php echo utf8_encode($siniestro->asegurado_id) ?></td>
						<td><?php echo $siniestro->declarante_celular ?></td>
						<td><?php echo $siniestro->placa?></td>
						<td><?php echo $siniestro->vigencia_desde ?></td>
						<td><?php echo $siniestro->vigencia_hasta ?></td>
						<td><?php echo $siniestro->dias_servicio ?></td>
						<td><?php echo $report->cita_contacto($siniestro->id)?></td>
						<td><?php echo $siniestro->numero?></td>
						<td><?php echo $report->cita_entrega($siniestro->id)?></td>
						<td><?php echo difference_hours($siniestro->ingreso,$report->cita_contacto($siniestro->id));?></td>
						<td><?php echo $siniestro->fecha_inicial ?></td>
						<td><?php echo $siniestro->fecha_final ?></td>
						<td><?php echo $siniestro->cita_placa ?></td>
						
						<td><?php echo $report->cita_placa_vehiculo($siniestro->id) ?></td>
						<td><?php echo utf8_encode($siniestro->sucursal_radicadora) ?></td>
						<td><?php echo $report->aseguradora($siniestro->flota) ?></td>
						<td><?php echo difference_days($siniestro->fecha_inicial,$siniestro->fecha_final); echo " dias";?></td>
						<td><?php echo days_fin_res($siniestro->dias_servicio,difference_days($siniestro->fecha_inicial,$siniestro->fecha_final));?></td>
						<?php if($extrachevy){?>
						<td><?php if($siniestro->chevyseguro == 0) {echo "NO";} else{ echo "SI";}  ?></td>
						<?php } ?>
						<?php if(isset($tableamp)){ ?>						
						<?php if(isset($_POST['cober'.$siniestro->dias_servicio])){ ?>
						<td><?php echo $_POST['cober'.$siniestro->dias_servicio]; ?></td>
						<td><?php echo $_POST['cober'.$siniestro->dias_servicio]*$_POST['iva_val']; ?></td>
						<td><?php echo ($_POST['cober'.$siniestro->dias_servicio]*$_POST['iva_val'])+$_POST['cober'.$siniestro->dias_servicio]; ?></td>
						<?php } else{?>							
							<td><?php echo $_POST['cober9']; ?></td>
							<td><?php echo $_POST['cober9']*$_POST['iva_val']; ?></td>
							<td><?php echo ($_POST['cober9']*$_POST['iva_val'])+$_POST['cober9']; ?></td>				
							<?php } ?>
						<?php } ?>
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

@import url(https://fonts.googleapis.com/css?family=Fjalla+One);
@import url(https://fonts.googleapis.com/css?family=Gudea);
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
<div class="footer-bottom" style="margin-top:100%">

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
			 $("input[name='finanparam1']").val(elem);
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
	
	function finan_option()
	{
		if($("select[name='flotaselect']").val() == "LA PREVISORA S.A")
		{
			$("#accordion").show();
		}
		//alert($("select[name='flotaselect']").val());
	}
	
	function financial_form()
	{
		if($("input[name='cober9']").val() < 1000 || $("input[name='cober12']").val() < 1000)
		{ alert("ingrese valores financieros validos"); return false;}
		//alert("eventos del form");
		if($("#fecha1").val() != "" && $("#fecha2").val() != ""){$("#finanparam2").val($("#fecha1").val());$("#finanparam3").val($("#fecha2").val());}					
		document.getElementById("financial_form").submit();
	}
	
	function financial_excel()
	{
		if($("input[name='cober9']").val() < 1000 || $("input[name='cober12']").val() < 1000)
		{ alert("ingrese valores financieros validos"); return false;}
		if($("#fecha1").val() != "" && $("#fecha2").val() != ""){$("#finanparam2").val($("#fecha1").val());$("#finanparam3").val($("#fecha2").val());}
		$('#excel_attr').val(1);
		//$('#financial_form').attr('action', 'controllers/ReportController.php');		
		document.getElementById("financial_form").submit();
	}
	
</script>

</html>	