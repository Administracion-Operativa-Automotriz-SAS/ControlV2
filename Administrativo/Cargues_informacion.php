<?php
	include('inc/funciones_.php');		
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	
	$sql="select * from aoacol_administra.estado_requisicion";
	$result = q($sql);
	
	$estados = array();

	while($row = mysql_fetch_object($result))
	{
		array_push($estados,$row);
	}

	
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Cargues información</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<style>
	.table-condensed{
	  font-size: 10px;
	}
</style>
<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.min.js"></script> 
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

</script>
<body>
	<div class="container-fluid">
		<h1>Cargues de información</h1>
		<div class="col-lg-4 col-md-4">
			<div class="panel panel-default">
			  <div class="panel-heading">Inactivación de Proveedores</div>
			  <div class="panel-body">
				<form action="Controllers/ExcelController.php?acc='test'" method="post" enctype="multipart/form-data">					
					
					<div class="form-group">
						<label>Archivo a subir</label>
						<input type="file" name="archivo" class="form-control" required>
					</div>
					
					<button class="btn btn-success form-control">Subir</button>
				</form>
			  </div>
			</div>
		</div>
		<div class="col-lg-4 col-md-4">
			<div class="panel panel-default">
			  <div class="panel-heading">Asignación de nivel de criticidad y tipo de gasto proveedor</div>
			  <div class="panel-body">
				<form action="Controllers/ExcelController.php?acc=crit_proov" method="post" enctype="multipart/form-data">					
					
					<div class="form-group">
						<label>Archivo a subir</label>
						<input type="file" name="archivo" class="form-control" required>
					</div>
					
					<button class="btn btn-success form-control">Subir</button>
				</form>
			  </div>
			</div>
		</div>
		<div class="col-lg-4 col-md-4">
			<div class="panel panel-default">
			  <div class="panel-heading">Cambio de estado de requisiciones</div>
			  <div class="panel-body">
				<form action="Controllers/ExcelController.php?acc=crit_proov" method="post" enctype="multipart/form-data">					
					
					<div class="form-group">
						<label>Datos</label>
						<textarea  name="datos" class="form-control" required></textarea>
					</div>
					
					<div class="form-group">
						<label>Estados</label>
						
					</div>
					
					<button class="btn btn-success form-control">Subir</button>
				</form>
			  </div>
			</div>
		</div>
		
	</div>
</body>
</html>	