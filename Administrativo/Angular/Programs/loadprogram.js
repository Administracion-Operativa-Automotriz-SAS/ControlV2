var app = angular.module("myApp",['angular-js-xlsx']);
app.service('parseDate', function() {
    this.default = function (date) {
        return Date.parse(date).toString("yyyy-MM-dd");
    }
	this.expression = function (date,expression) {
        return Date.parse(date).toString(expression);
    }
});		
app.controller("LoadController", function ($scope,$http,parseDate){
    
    $scope.load_info = {};
	$scope.excel_info = {};
    
    $scope.data_load = function(acc){		
		
		$scope.load_info.acc = acc;
		
		if($scope.excel_info == null)
		{
			return alert("No se ha cargado archivo de excel");
		}
		else
		{
			$scope.load_info.dataset = $scope.excel_info;
		}
		
        var request = $http.post("/Control/operativo/controllers/LoadController.php",$scope.load_info);
        request.then(function(response){
			
			if(response.data.status == null)
			{
				return alert("Ocurrio un error");
			}
			
			console.log(response);
			var res = "";
			response.data.details.forEach(function(data){
				res += "<span> Linea: "+data.linea+" , resultado "+data.estado+"</span><br>";
			});
            $("#results").html(res);
			
        });
    }
	
	$scope.read = function (workbook) {
		/* DO SOMETHING WITH workbook HERE */
		for (var sheetName in workbook.Sheets) {
			
		  var jsonData = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName]);
		  var excel_data = [];
		  //console.log(excel_data);
		  jsonData.forEach(function(data){
			  
			  if(data.FECHA_INICIO_POLIZA != null){
				data.FECHA_INICIO_POLIZA = parseDate.default(data.FECHA_INICIO_POLIZA);
				data.FECHA_VENCE_POLIZA = parseDate.default(data.FECHA_VENCE_POLIZA);	
			  }
			  
			  excel_data.push(data);
			  
		  });
		  
		  console.log(excel_data);
		  $scope.excel_info = excel_data;
		}
    }

	$scope.error = function (e) {
		/* DO SOMETHING WHEN ERROR IS THROWN */
		console.log(e);
    }

});