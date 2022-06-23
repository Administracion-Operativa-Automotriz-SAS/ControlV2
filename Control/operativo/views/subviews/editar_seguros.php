<!DOCTYPE html>
<html>
<head>
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Bootstrap -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- Librerias Angular -->
<script src="https://code.angularjs.org/1.3.11/angular.js"></script>
<script src="https://code.angularjs.org/1.3.11/angular-route.js"></script>
<link rel="stylesheet"; href="https://unpkg.com/ng-table@2.0.2/bundles/ng-table.min.css">
<script src="https://unpkg.com/ng-table@2.0.2/bundles/ng-table.min.js"></script>
<script src="https://angular-ui.github.io/bootstrap/ui-bootstrap-tpls-0.11.0.js"></script>
<!----------------------------->

<!-- Mis programas-->
<script src="Angular/Modules/app.js"></script> 
<script src="Angular/Directives/contenteditable.js"></script>
<script src="Angular/Services/SystemService.js"></script>
<script src="Angular/Controller/SystemController2.js"></script>
<script src="Angular/Controller/CrudController.js"></script>
<script src="Angular/Controller/BrandWpController.js"></script>
<!----------------------------->

<!-- Font Awesome -->
<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js" integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+" crossorigin="anonymous"></script>
<!----------------------------->

<!-- Sweet Alwert -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.1/sweetalert2.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.1/sweetalert2.all.js"></script>
	<title>Editar seguros</title>
</head>
<body>
<div ng-app="Appi">
  <div class="row">
  <div ng-controller="SystemController2 as vm">
  <div class="panel panel-default col-lg-6 col-md-6">
  <h4>Seguros</h4>
  <table ng-table="vm.tableParams" class="table table-bordered table-responsive table-hovered" id="table_causales" ng-init="get_seguros()" show-filter="true">
				    		<!--
				    		<thead>
				    			<tr><th>id</th>
				    				<th>Nombre</th>
				    				<th>Descripción</th>
				    				<th>Status</th>
				    				<th>Opciones</th></tr>
				    		</thead>
				    		-->				    	
				    			<tr ng-repeat="causal in $data">
				    				<td title="'Nombre'" filter="{ nombre: 'text'}" sortable="'n_poliza'" onclick="big_text_edit(this)" ng-model="causal.n_poliza" contenteditable="true">{{causal.n_poliza}}</td>
				    				<!--<button title="Eliminar" ng-click="delete_causal(causal.id)"><i class="fas fa-trash"></i></button>--></td>
				    			</tr>				    			
				    			<tr>
				    				<td></td>
				    				<td contenteditable="true" onclick="big_text_edit(this)"></td>
				    				<td contenteditable="true" onclick="big_text_edit(this)"></td>
				    				<td><button ng-click="add_row(causales,'cartas_causales_rechazo',causal)">Agregar</button></td>
				    			</tr>				    			
				    	
				    	</table>
  
  
  </div>
  </div>
  </div>

</div>
</body>
</html>