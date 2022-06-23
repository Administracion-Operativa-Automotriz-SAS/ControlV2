/********************
 * Autor Original: Jesús Vega
 * 
 * Proyecto:  
 * Documentos relacionados: 
 * Descripción del script:
 * El script es para realizar funciones dentro del sistema sin que este asociado un proceso en especifico, contiene sesión
 * y control sobre las acciones del usuario.
 * Cambios:
 *Autor: Jesús Vega
 * 1. Se agregó la función de sesión de usuario  
 * Fecha:25/02/2019
 *********************/

app.controller('SystemController',['$scope','SystemServices','$timeout','$window','$compile','$filter','NgTableParams','$http',function($scope,SystemServices,$timeout,$window,$compile,$filter,NgTableParams,$http){
	
	let self = this;
	
	$scope.FormData = {};
		
	let initial_request = SystemServices.getSession();
	initial_request.then((response)=>{
		$scope.Session = response.data;
	});
	
	
	$scope.generatePostRequest = function(data,parameters){
		
		$("#loadingDiv").show();
		
		let forceExit = false;
		
		//console.log(data);
		//console.log($scope.FormData);
		parameters.forEach((parameter)=>{
			if($scope.FormData[parameter] == null || $scope.FormData[parameter].length == 0 )
			{
				forceExit = true;
				return alert("El parametro "+parameter+" no tiene datos");				
			}
			
			data.request[parameter] = $scope.FormData[parameter];
		});
		
		if(forceExit)
		{
			$("#loadingDiv").hide();
			return null;
		}
		
		const request = SystemServices.generatePostRequest(data);
		request.then((response)=>{
			console.log("Exec Done");
			$scope.FormData = {};
			if(response.data.status != "Ok")
			{
				alert(response.data.desc);
			}
			$("#loadingDiv").hide();
		});
		request.catch((error)=>{
			//$scope.FormData = {};
		});
		
	}
	
	$scope.formInModal = function(modalId)
	{
		$("#"+modalId).modal("show");		
		
	}
	
	
	
	
	
}]);




