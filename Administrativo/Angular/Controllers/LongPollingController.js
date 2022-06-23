app.controller('LongPollingController',['$scope','$timeout','$interval','LongPollingService','$window','$compile','$filter','$polling',function($scope,$timeout,$interval,LongPollingService,$window,$compile,$filter,$polling){

	$scope.test = "Testeo";
	$scope.data = {"placeholder":"placeholder"};
	$scope.last_packet = {method:"echo",last_packet:{}};
	
	$scope.processData = function(data)
	{
		console.log(data.data.data);
		$scope.last_packet.last_packet = data.data.data;
		$("#log_content").append(data.data.data+"<br>");
	}	
	
	$polling.startPolling('fetchNotifications', 'http://app.aoacolombia.com/Administrativo/Controllers/NodeGraph.map.php', 1000, $scope.processData,$scope.last_packet);
	

	
	
	
	
	
}]);



