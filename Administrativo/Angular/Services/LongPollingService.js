app.factory('LongPollingService',['$http','$q',function($http,$q){
	
	var LongPollingService = {};
    var defered = $q.defer();
    var promise = defered.promise;

	LongPollingService.echo = function(data)
	{
		data.method = "echo";
		return $http.post("http://app.aoacolombia.com/Administrativo/Controllers/NodeGraph.map.php",data);
	}

	
	
	return LongPollingService;
	
}]);
