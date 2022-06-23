<?php
//DIE('APLICACION EN SUSPENCION POR 2 MINUTOS. Atte. Departamento de Tecnologia de Informacion.');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
header('Content-type: text/html; charset=utf-8');

include_once('inc/funciones_.php');
include_once('controllers/ReportController.php');

//prepara_rutinas($Acc);
//verificar_directorios();

if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}
//html(TITULO_APLICACION.' - '.$_SESSION['Nombre']);
//inicio();
//die();

$report = new ReportController();
$siniestros = null;


if(isset($_POST['html']))
{
	if($_POST['fecha1']!=null)
	{$siniestros = $report->siniestros_control_garantias($_POST['fecha1'],$_POST['fecha2']);}
	else{$siniestros = $report->siniestros_control_garantias(null,null);}
	
}

if(isset($_POST['excel_attr']))
{
	if($_POST['param2']!=null){
		$siniestros = $report->siniestros_control_garantias($_POST['param2'],$_POST['param3']);
		}
		else{$siniestros = $report->siniestros_control_garantias(null,null);}
	
}




?>

<html lang="en">
<head>
  <title>Informe de control de garantias</title>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.min.js"></script> 
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="inc/css/headboot.css">
	
  <link href='http://fonts.googleapis.com/css?family=Cookie' rel='stylesheet' type='text/css'>
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
	
	#InformTable{ display:none}
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

		<h1><a href="http://app.aoacolombia.com/Control/operativo/InformesControlGarantias.php">Informe de Control de <span>Garantias</span></a></h1>
		
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
		<?php
			if(isset($_POST['excel_attr']))  { 
				if($_POST['excel_attr'] == 1)  { $forms = 1; } else { $forms = 0;}
			}
			else{ $forms = 0;}
		 ?>
	
		<?php if( $forms == 0 and !isset($_POST['excel_generate'])) { ?>
		<form action="InformesControlGarantias.php"  id="filter_data" method="post">
		<div class="form-group">
			
			<br>
			<label class="form-control">Fecha de constitución de la garantia</label>
			<input type="hidden" name="html" value="1">
			<label>Desde</label>
			<input type="date" class="form-control" max=<?php echo date('Y-m-d'); ?>  name="fecha1" <?php if(isset($_POST['fecha1'])) { echo "value = '".$_POST['fecha1']."'"; } else{echo "value = '".date('Y-m-01')."'";} ?>  id="fecha1">
			<br>			
			<label>Hasta</label>
			<input type="date" class="form-control" max=<?php echo date('Y-m-d'); ?>  name="fecha2" <?php if(isset($_POST['fecha2'])) { echo "value = '".$_POST['fecha2']."'"; } else{echo "value = '".date('Y-m-d')."'";} ?> id="fecha2">
		</div>
		<button class="btn btn-warning form-control">Filtrar</button>
		</form>
		
		<form id="excel_data"  action="InformesControlGarantias.php" method="post">
			<input type="hidden" name="excel_attr" value="1">
			<input type="hidden" id="param2" name="param2" >
			<input type="hidden" id="param3" name="param3" >
			<button class="btn btn-success form-control" type="submit">Excel</button>
		</form>
		
		<br>	
		<?php } ?>
		<br>
		<div style="max-width:1150px;"  class="table-responsive ocultar_400px">
			<table  class="table table-condensed  table-bordered" id="InformTable">
				<thead>
					<tr>
						<th>Fecha proceso de<br> garantia</th>
						<th>Numero Siniestro</th>
						<th>Aseguradora</th>
						<th>Estado</th>
						<th>Tipo Garantia</th>
						<th>Valor Garantia</th>
						<th>Valor Factura</th>
						<th>Valor Recibo</th>
						<th>Valor nota <br> contable</th>
						<th>Metodo de devolución</th>
						<th>Opciones</th>
					</tr>
				</thead>
				<tbody style="text-align:center;">
					<?php if($siniestros!=null) {foreach($siniestros as $siniestro){ ?>
					<tr>
						<td><?php echo $siniestro->fecha_proceso ?></td>
						<td><?php echo $siniestro->numero ?></td>
						<td><?php echo $siniestro->aseguradora ?></td>
						<td><?php echo $siniestro->estado ?></td>
						<td><?php echo $siniestro->tipo_garantia ?></td>
						<td><?php echo $siniestro->valor_garantia ?></td>
						<td><?php echo $siniestro->valor_total ?></td>
						<td><?php echo $siniestro->recibo_valor ?></td>
						<td><?php echo $siniestro->nota_valor ?></td>
						<td><?php echo $siniestro->metodo_devol ?></td>
						<td> <button class='btn btn-warning' onclick="generar_cartera(<?php echo $siniestro->id ?>)" >Ver caja</button> </td>
					</tr>
					<?php }} ?>
				</tbody>
			</table>
		</div>	
	</div>
</div>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Caja</h4>
      </div>
      <div class="modal-body">
        <div id="ajax-content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
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
<div class="footer-bottom" style="margin-top:100%">

	<div class="container">

		<div class="row">

			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

				<div class="copyright">

					Informe de Control de Garantias

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
		$( document ).ready(function(){
			$('#InformTable').DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Spanish.json"},"pageLength": 50});
			$('#InformTable').show();
		});	 
	 function fill_excel_form_data(elem)
	 {		 
			if($("#fecha1").val() == "" || $("#fecha2").val() == "")
			{
				alert("Seleccione las fechas");				
			}
			else{
				$("param2").val($("#fecha1").val());
				$("param3").val($("#fecha2").val());				
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
	
	$("#fecha1").change(function(){
		var minattr = event.target.value;
		$("#fecha2").attr('min', minattr);
	});
	$("#fecha2").change(function(){
		var attr = event.target.value;
		if($("#fecha1").val() == '')
		{
			alert('Seleccione primero la fecha de inicio');
			$("#fecha2").val('');
		}
		else{
			var d1 = new Date($("#fecha1").val());
			var d2 = new Date($("#fecha2").val());
			if(d1>d2)
			{
				alert('la fecha de inicio no puede ser mayor que la fecha final');
				$("#fecha2").val('');
				$("#fecha1").val('');	
			}
		}

	});
	
	function generar_cartera(id)
	{
		$.post("controllers/ReportController.php",{siniestro:id,gen_cartera:1}, function(res, sta){
			$("#ajax-content").empty();
			$("#ajax-content").append(res);
		});
		$("#myModal").modal('show');
	}
	
	
</script>

</html>	