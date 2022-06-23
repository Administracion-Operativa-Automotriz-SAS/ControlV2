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

	SystemService.getBy = function(get)
	{
		data = {};
		data.properties = get.get;
		data.Acc = 'getBy';
		data.table = get.table;
		return $http.post("Controllers/WsController.php",data);

	}

	SystemService.getMETA_COLUMNS = function(data)
	{
		var formData = new FormData();		
		formData.append('Acc', 'META_COLUMNS');
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


	SystemService.getInstancefromdb = function(data)
	{
		data.Acc = "return_instance";
		return $http.post("Controllers/WsController.php",data);
	}

	SystemService.saveInstancetodb = function(data)
	{
		data.Acc = "saving_instance";
		return $http.post("Controllers/WsController.php",data);
	}

	SystemService.upgradeInstancetodb = function(data)
	{
		data.Acc = "upgrade_instance";
		return $http.post("Controllers/WsController.php",data);
	}

	SystemService.save_object_from_instance = function(data)
	{
		data.Acc = "save_object_from_instance";
		return $http.post("Controllers/WsController.php",data);
	}


	SystemService.execute = function(data)
	{		
		return $http.post("Controllers/WsController.php",data);
	}
	
	return SystemService;
	
}]);
