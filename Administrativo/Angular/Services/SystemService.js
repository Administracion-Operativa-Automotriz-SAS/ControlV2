app.factory('SystemService',['$http','$q',function($http,$q){
	
	var SystemService = {};
    var defered = $q.defer();
    var promise = defered.promise;

	SystemService.get_from_table = function(query)
	{
		var formData = new FormData();		
		formData.append('Acc', 'get_from_table');
		formData.append('query', query);
		return $http({
			url: "Controllers/WsController.php",
			method: "POST",
			data: formData,
			headers: { 
			  'Content-Type': undefined
			}
	   });
	}
	
	
	

	SystemService.persist = function(data)
	{		
		return $http.put("Controllers/WsController.php",data);		
	}

	SystemService.delete = function(data)
	{
		return $http({
			url: "Controllers/WsController.php",
			method: "DELETE",
			data: data,
			headers: { 
			  'Content-Type': 'json'
			}
	   });
				
	}

	SystemService.create = function(data)
	{
		return $http.post("Controllers/WsController.php",data);
				
	}
	
	SystemService.test = function()
	{
		alert("test");
	}
	
	SystemService.getAll = function(data)
	{
		var formData = new FormData();		
		formData.append('Acc', 'getAll');
		formData.append('table', data.table);
		return $http({
			url: "Controllers/WsController.php",
			method: "POST",
			data: formData,
			headers: { 
			  'Content-Type': undefined
			}
	   });
	   

	}
	
	return SystemService;
	
}]);
