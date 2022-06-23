app.factory('BrainService',['$http','$q',function($http,$q){
	
	var BrainService = {};
    var defered = $q.defer();
    var promise = defered.promise;

	BrainService.send_message = function(data){
		console.log(data);
		return $http.post("http://107.20.199.106",data);
	}
	
	BrainService.test_ws = function(){
		
		return $http.get("http://app.aoacolombia.com/Control/operativo/marcoindex.php?Acc=ingreso_sistema&SESION_PUBLICA=1");
	}
	
	
	return BrainService;	
	
}]);
