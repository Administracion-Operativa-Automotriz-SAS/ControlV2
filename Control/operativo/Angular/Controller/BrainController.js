app.controller('BrainController',['$scope','BrainService','$window',function($scope,BrainService,$window){
	$scope.test = "demo";
	$scope.sms = {};

	$scope.send_message = function(){
		console.log($scope.sms);
		BrainService.send_message($scope.sms);		
	}
	
	$scope.test_ws = function(){
		var request = BrainService.test_ws();
		request.then(function(response){
			console.log(response);
			$("#innerhtml").html(response.data);
		});
	}
	
	
}]);