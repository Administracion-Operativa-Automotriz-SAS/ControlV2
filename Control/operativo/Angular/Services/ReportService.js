app.factory('ReportService',['$http','$q',function($http,$q){
	
	var ReportService = {};
    var defered = $q.defer();
    var promise = defered.promise;

	ReportService.get_report = function(data)
	{		
		return $http.post("https://app.aoacolombia.com/Control/operativo/controllers/Reports/ReportController.php",data);				
	}
	
	ReportService.getone = function(data)
	{		
		
		return $http.post("https://app.aoacolombia.com/Control/operativo/controllers/Reports/ReportController.php",data);				
	}
	
	return ReportService;
	
}]);
