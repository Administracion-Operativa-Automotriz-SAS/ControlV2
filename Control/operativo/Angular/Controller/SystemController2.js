app.controller('SystemController2',['$scope','$timeout','SystemService','$window','$compile','$filter','NgTableParams',function($scope,$timeout,SystemService,$window,$compile,$filter,NgTableParams){

	var self = this;  

	$scope.causales = {};
	$scope.subcausales = {};
	$scope.copycat = {};
	$scope.causal = {};
	$scope.subcausal = {};


	$scope.cartas_causales_rechazo = new select_table('aoacol_aoacars.seguros');
	
	

	$scope.get_causales = function()
	{
		var request = SystemService.get_from_table($scope.cartas_causales_rechazo.selectinstance);
		request.then(function(response){
			$scope.causales = response.data.dataset;			
			//console.log($scope.causales);
			self.tableParams = new NgTableParams({count: 10}, { dataset: $scope.causales});
		});	
	}

	/*
	$scope.load_table = function(selector)
	{
		$timeout(function() {
				$(selector).dataTable().fnDestroy();
			    $(selector).DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Spanish.json"}});
		},1000);
	}*/ //Deprecated
	

	$scope.get_subcausales = function()
	{
		var request = SystemService.get_from_table($scope.cartas_subcausales_rechazo.selectinstance);
		request.then(function(response){
			$scope.subcausales = response.data.dataset;
			//console.log($scope.subcausales);
			//$scope.load_table("#table_subcausales");		
		});	
	}

	$scope.edit_causal = function(id)
	{
		console.log(id);

		var  causal = $filter('filter')($scope.causales, {id: id})[0];		
		//var index = $scope.prefacturas.indexOf(prefact);
		

		var causal =  JSON.stringify(causal, function (key, val) {
		     if (key == '$$hashKey') {
		       return undefined;
		     }
		     return val;
		});	
		//causal = $scope.add_property(causal,"table","cartas_causales_rechazo");

		causal = angular.fromJson(causal);

		causal.table  ="cartas_causales_rechazo";		

		var request = SystemService.persist(causal);

		request.then(function(response){
			if(response.data.status == 1)
			{
				alert("causal editado");
			}
			else
			{
				alert("Ocurrio un error");
			}
					
		});	

		
	}

	$scope.delete_causal = function(id)
	{
		//console.log(id);

		if(!confirm("Esta seguro/a de eliminar el causal no habrá forma de recuperarlo"))
		{
			return false;
		}	

		var  causal = $filter('filter')($scope.causales, {id: id})[0];			

		var index = $scope.causales.indexOf(causal);

		console.log(index);

		$scope.causales.splice(index, 1);

		console.log($scope.causales);

		//$scope.load_table("#table_causales");

		causal = angular.fromJson(causal);

		causal.table  ="cartas_causales_rechazo";

		var request = SystemService.delete(causal);

		request.then(function(response){
			if(response.data.status == 1)
			{
				alert("Causal eliminado");
			}
			else
			{
				alert("Ocurrio un error");
			}			
		});	
	}


	$scope.edit_subcausal = function(id)
	{		

		var  subcausal = $filter('filter')($scope.subcausales, {id: id})[0];		
		//var index = $scope.prefacturas.indexOf(prefact);
		var subcausal =  JSON.stringify(subcausal, function (key, val) {
		     if (key == '$$hashKey') {
		       return undefined;
		     }
		     return val;
		});

		subcausal = angular.fromJson(subcausal);

		subcausal.table = "cartas_subcausales_rechazo";

		var request = SystemService.persist(subcausal);

		request.then(function(response){
			if(response.data.status == 1)
			{
				alert("Subcausal editado");
			}
			else
			{
				alert("Ocurrio un error");
			}			
		});	

		console.log(subcausal);
	}

	$scope.delete_subcausal = function(id)
	{
		if(!confirm("Esta seguro/a de eliminar el subcausal no habrá forma de recuperarlo"))
		{
			return false;
		}

		var  subcausal = $filter('filter')($scope.subcausales, {id: id})[0];	

		var index = $scope.subcausales.indexOf(subcausal);
		$scope.subcausales.splice(index, 1);

		subcausal = angular.fromJson(subcausal);

		subcausal.table = "cartas_subcausales_rechazo";

		var request = SystemService.delete(subcausal);

		request.then(function(response){
			if(response.data.status == 1)
			{
				alert("Subcausal eliminado");
			}
			else
			{
				alert("Ocurrio un error");
			}			
		});	
		
	}

	$scope.add_property = function(data,property,value)
	{

		var adddata = [];
		var addobj = {};
		addobj.property = value;
		
		adddata.push(data);
		adddata.push(addobj);

		return adddata;
	}


	$scope.add_row = function(element,table,newelement)
	{

		if(isEmpty(newelement))
		{

			return alert("No puede crear datos con valores vacios");
		}	


		var last = element[element.length - 1];
		console.log(last);
		//Obtengo el ultimo
		newelement.id = (parseInt(last.id)+parseInt(1)).toString();	
		newelement.table = table;
		//Le agrego la tabla
		console.log(element);
		$scope.copycat = angular.copy(newelement);
		//Necesito copiarlo para mentener la integridad del objeto de creación
		element.push($scope.copycat);
		//Lo meto en la lista de objetos deseada
		console.log(element);
		$scope.copycat = {};
		//Reinicializo
		var request = SystemService.create(newelement);
		request.then(function(response){
			if(response.data.status == 1)
			{
				alert(response.data.message);
				newelement = {};
			}
			else
			{
				alert("Ocurrio un error");
			}
		});
		
	}

	
	
}]);



function select_table(table)
{
	this.selectinstance = "Select * from "+table; 
}

function isEmpty(obj) {
	for(var key in obj) {
		if(obj.hasOwnProperty(key))
			return false;
	}
	return true;
}