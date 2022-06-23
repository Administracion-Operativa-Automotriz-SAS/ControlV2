var app = angular.module("consult_nc_electronica",[]);
app.controller("ConsultController", function ($scope,$http,$filter) {
	$scope.ncs = {};
	$scope.interfaz_mode = null;
	$scope.estados_nc = ["Facturado","A verificar","En error"];
	
	$scope.init = function(){
		let request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"get_nc_electronica",nota_credito:findGetParameter("id")});
		request.then(function(response){
			console.log("Begin process of nc");
			
			$scope.ncs = response.data.nc_electronicas;			
			if($scope.ncs != null)
			{
				$scope.complete_ncs = $filter('filter')($scope.ncs, {estado:1});
				
				if($scope.complete_ncs.length>0)
				{ 
					$scope.interfaz_mode = 1;
					console.log($scope.complete_ncs);
					//last positive
					console.log($scope.complete_ncs[$scope.complete_ncs.length-1]);
					$scope.nota_credito = $scope.complete_ncs[$scope.complete_ncs.length-1];
					$scope.nota_credito_estado = JSON.parse($scope.nota_credito.descr);
					console.log($scope.nota_credito_estado);
					
				}
				else{
					$scope.interfaz_mode = 2;
					$scope.nota_credito = $scope.ncs[$scope.ncs.length-1];
					$scope.nota_credito_estado = JSON.parse($scope.nota_credito.descr);
				}
			}
			else{
				$scope.interfaz_mode = 3;
			}	
			
		});
	}
	
	$scope.generate_xml = function(){
		window.open("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO.php?XMLNC="+$scope.nota_credito.nota_credito+"&XMLCONSE="+$scope.nota_credito.ptesa_conse, '_blank');
	}
$scope.generate_xml_new = function(){
		window.open("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO_james.php?XMLNC="+$scope.nota_credito.nota_credito+"&XMLCONSE="+$scope.nota_credito.ptesa_conse, '_blank');
	}
	
	
	$scope.graphic_representation = function(){
		document.querySelector(".loader").style.display = 'block';	
		let request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"representacion_grafica",cufe:$scope.nota_credito_estado.respuesta.cufe,tipo_documento:2});
		request.then(function(response){
			if(response.data.status == "OK")
			{
				let pdfWindow = window.open("")
				pdfWindow.document.write("<iframe width='100%' height='100%' src='data:application/pdf;base64, " + encodeURI(response.data.pdfbase64)+"'></iframe>")
				document.querySelector(".loader").style.display = 'none';			
			}
			else
			{
				alert("Ocurrio un error al consultar la representación grafica");
			}
		});
	}
	
	$scope.resend_bill = function()
	{
		document.querySelector(".loader").style.display = 'block';
		let request;
		if($scope.nota_credito != null)
		{
			 request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO.php",{acc:"enviar_nota_credito",nota_credito:$scope.nota_credito.nota_credito});
		
		}
		else{
			request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO.php",{acc:"enviar_nota_credito",nota_credito:findGetParameter("id")});			
		}
		request.then(function(response){
			document.querySelector(".loader").style.display = 'none';
			if(response.data.status=="OK")
			{
				location.reload(true);
			}
			
			if(response.data.desc)
			{
				alert(response.data.desc);
			}
			
		});
	}
	
      $scope.resend_bill_new = function()
	{
		document.querySelector(".loader").style.display = 'block';
		let request;
		if($scope.nota_credito != null)
		{
			 request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO_james.php",{acc:"enviar_nota_credito",nota_credito:$scope.nota_credito.nota_credito});
		
		}
		else{
			request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO_james.php",{acc:"enviar_nota_credito",nota_credito:findGetParameter("id")});			
		}
		request.then(function(response){
			document.querySelector(".loader").style.display = 'none';
			if(response.data.status=="OK")
			{
				location.reload(true);
			}
			
			if(response.data.desc)
			{
				alert(response.data.desc);
			}
			
		});
	}
}); 


function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
          tmp = item.split("=");
          if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}