app.controller('SystemController',['$scope','$timeout','SystemService','$window','$compile','$filter',function($scope,$timeout,SystemService,$window,$compile,$filter){

	$scope.test = "working";
	
	$scope.copycat = {};
	$scope.categoria = {};
	
	var index_to_filter = null;
	
	//$scope.current_filter_index = 100;
	
	$scope.categorias_cabecera = [{"title":"id"},{"title":"nombre"},{"title":"orden"},{"title":"opciones"}];	
	
	$scope.categorias = new table('q_proceso',SystemService,$scope.categorias_cabecera,false);	
	
	var categorias_validation = { with:"q_documento", foreign_key:"proceso", type:"FOREIGN_VALUES" };
	
	$scope.categorias.delete_validation_subscriber = categorias_validation;		
	
	$scope.documento_versiones = new table('q_documento_versiones',SystemService,[{"title":"id"},{"title":"version"},{"title":"fecha"},{"title":"archivo"}],false);
	
	
	$scope.filtered_data = function(data,filtered_object)
	{		
		//console.log(window.index_to_filter);
		var f_data = $filter('filter')(data,{id_q_documento:window.index_to_filter});
		//console.log(f_data);
		return f_data;	
	}
	
	$scope.number_validation = function(detalles){
		var prev_text = $(event.currentTarget).text();
		//console.log("key pressed"+event.keyCode);
		if(event.keyCode < 48 || event.keyCode > 57)
		{
			 //alert("Valor no valido");
			 event.preventDefault();
			 //$(event.currentTarget).text("0");
		}
	}
	
	$scope.check_ver_ant = function(id)
	{
		//$scope.$apply(function () {
         window.index_to_filter = id;
        //});
		//console.log(window.index_to_filter);	
		$("#myModal4").modal("show");		
	}	

	
	$scope.test = function()
	{		
		$scope.categorias.test();
	}
	
	$scope.start = function()
	{		
		$("#AngularModal").modal("show");		
	}
	
	$scope.edit = function(row,model)
	{		
		model.update(row);		
	}
	
	$scope.delete = function(row,model)
	{	
		model.delete(row);
		
	}
	
	$scope.create = function(row,model)
	{
		//console.log(row);
		if(isEmpty(row))
		{
			return alert("No puede crear datos con valores vacios");
		}
		else
		{
			model.create(row,$scope.copycat);
		}
	}
	
	
	
	
}]);

function isEmpty(obj) {
	for(var key in obj) {
		if(obj.hasOwnProperty(key))
			return false;
	}
	return true;
}


