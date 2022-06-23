<?php 
	if(!$SESION_PUBLICA) require('inc/sess.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Cargues informacion</title>  
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="https://code.angularjs.org/1.3.11/angular.js"></script>
  <script src="Angular/Libraries/xlsx.full.min.js"></script>
  <script src="Angular/Libraries/angular-js-xlsx.js"></script>
  <script src="Angular/Libraries/date.js"></script>
  <script src="Angular/Programs/loadprogram.js"></script>
</head>
<script>
	//var test = Date.parse("December 24, 2017").toString("yyyy-MM-dd");
	//console.log(test);
</script>
<style> 
	.table-condensed{
	  font-size: 10px;
	}
</style>
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://cdn.jsdelivr.net/webshim/1.14.5/polyfiller.js"> </script>
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

</script>
<body ng-app="myApp">
	<div class="container-fluid" ng-controller="LoadController">
		<h1>Cargues de informacion</h1>
		<div class="col-lg-4 col-md-4">
			<div class="panel panel-default">
			  <div class="panel-heading">Actualizacion de clientes Mapfre</div>
			  <div class="panel-body">
				<form ng-submit="data_load('load_mapfre_data')">					
					<div class="form-group">
						<label class="form-control">Datos a cargar</label>
						<js-xlsx onread="read" onerror="error"></js-xlsx>											
					</div>			
					<div class="form-group">
						<button class="btn btn-success form-control">Subir</button>
					</div>
				</form>
				<span><b>NOTA:</b></span> 
				<ul>
					<li>Encabezado NUMERO_SINIESTRO para el numero del siniestro</li>
					<li>Encabezado FECHA_INICIO_POLIZA para la fecha de inicio de la poliza</li>
					<li>Encabezado FECHA_VENCE_POLIZA para la fecha final de poliza</li>
				</ul>
			  </div>
			  <div id="results" style="margin-left:3em;"></div>
			</div>
		</div>
		<div class="col-lg-4 col-md-4">
			<div class="panel panel-default">
			  <div class="panel-heading">Placas para futura actualizacion de clientes Mapfre</div>
			  <div class="panel-body">
				<form ng-submit="data_load('load_mapfre_placas')">					
					<div class="form-group">
						<label class="form-control">Datos a cargar</label>
						<js-xlsx onread="read" onerror="error"></js-xlsx>					
					</div>			
					<div class="form-group">
						<button class="btn btn-success form-control">Subir</button>
					</div>
				</form>
				<span><b>NOTA:</b></span>
				<ul>
					<li>Encabezado PLACA para el valor de la placa</li>					
				</ul>
			  </div>
			  <div id="results" style="margin-left:3em;"></div>
			</div>
		</div>
        <div class="col-lg-4 col-md-4">
			<div class="panel panel-default">
			  <div class="panel-heading">CALIDAD - Bienes y Servicios</div>
			  <div class="panel-body">
				<form ng-submit="data_load('load_bienes_servicios')">					
					<div class="form-group">
						<label class="form-control">Datos a cargar</label>
						<js-xlsx onread="read" onerror="error"></js-xlsx>					
					</div>			
					<div class="form-group">
						<button class="btn btn-success form-control">Subir</button>
					</div>
				</form>
				<span><b>NOTA:</b></span>
				<ul>
					<li>Encabezado PLACA para el valor de la placa</li>					
				</ul>
			  </div>
			  <div id="results" style="margin-left:3em;"></div>
			</div>
		</div>		
	</div>
</body>
</html>	