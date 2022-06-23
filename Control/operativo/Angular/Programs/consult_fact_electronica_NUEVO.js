var app = angular.module("consult_fact_electronica",[]);
app.controller("ConsultController", function ($scope,$http,$filter) {
	$scope.facts = {};
	$scope.interfaz_mode = null;
	$scope.estados_factura = ["Facturado","A verificar","En error"];
	
	$scope.init = function(){
		let request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO.php",{acc:"get_fact_electronica",factura:findGetParameter("id")});
		request.then(function(response){
			console.log("Begin process of fact");
			
			
			
			$scope.facts = response.data.fac_electronicas;			
			
			if($scope.facts != null)
			{
				$scope.complete_facts = $filter('filter')($scope.facts, {estado:1});
				
				$scope.fact_electronica = $scope.complete_facts[$scope.complete_facts.length-1];
				
				 
				 
				if($scope.complete_facts.length>0)
				{
					console.log('test 1');
					$scope.interfaz_mode = 1;
					console.log($scope.complete_facts);
					//last positive
					console.log($scope.complete_facts[$scope.complete_facts.length-1]);
					$scope.fact_electronica = $scope.complete_facts[$scope.complete_facts.length-1];
					$scope.fact_electronica_estado = JSON.parse($scope.fact_electronica.descr);
					console.log($scope.fact_electronica_estado);
					if($scope.fact_electronica.fecha <= '2020-01-20')
				    {
						console.log('test 2');
					$scope.interfaz_mode = 4; 
					}
					
				}
				else{
					
					$scope.fact_electronica = $scope.facts[$scope.facts.length-1];
					
					if($scope.fact_electronica.estado == 2){
						console.log('test 3');
					$scope.interfaz_mode = 2;
					$scope.fact_electronica = $scope.facts[$scope.facts.length-1];
					$scope.fact_electronica_estado = JSON.parse($scope.fact_electronica.descr);
					}else{
						console.log('test 4');
						$scope.interfaz_mode = 5;
					}
					
					
					
					
				}
			}
			else{
				console.log('test 5');
				$scope.interfaz_mode = 3;
			}	
			
		});
	}
	
	$scope.generate_xml = function(){
		window.open("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO.php?XMLFACT="+$scope.fact_electronica.factura+"&XMLCONSE="+$scope.fact_electronica.ptesa_conse, '_blank');
	}
	$scope.generate_xml_new = function(){
		
		window.open("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO_james.php?XMLFACT="+$scope.fact_electronica.factura+"&XMLCONSE="+$scope.fact_electronica.ptesa_conse, '_blank');
	}
	
	$scope.graphic_representation = function(){
		document.querySelector(".loader").style.display = 'block';	
		let request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO.php",{acc:"representacion_grafica",cufe:$scope.fact_electronica_estado.respuesta.cufe,tipo_documento:1});
		request.then(function(response){
			if(response.data.status == "OK")
			{ 
				let pdfWindow = window.open("")
				pdfWindow.document.write("<iframe width='100%' height='100%' src='data:application/pdf;base64, " + encodeURI(response.data.pdfbase64)+"'></iframe>")
				document.querySelector(".loader").style.display = 'none';
			}
			else{
				alert("Ocurre un error al consultar la representación");
			}
		});
	}
	
	$scope.resend_bill = function()
	{
		document.querySelector(".loader").style.display = 'block';
		let request;
		if($scope.fact_electronica != null)
		{
			 request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO.php",{acc:"enviar_factura",factura:$scope.fact_electronica.factura});
		
		}
		else{
			request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO.php",{acc:"enviar_factura",factura:findGetParameter("id")});			
		}
		request.then(function(response){
			document.querySelector(".loader").style.display = 'none';
			if(response.data.status=="OK")
			{
				location.reload(true);
			}
			if(response.data.status=="ERROR")
			{
				alert(response.data.desc);
			}
		});
	}
	
$scope.resend_bill_james = function()
	{
		document.querySelector(".loader").style.display = 'block';
		let request;
		if($scope.fact_electronica != null)
		{
			 request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO_james.php",{acc:"enviar_factura",factura:$scope.fact_electronica.factura});
		
		}
		else{
			request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO_james.php",{acc:"enviar_factura",factura:findGetParameter("id")});			
		}
		request.then(function(response){
			document.querySelector(".loader").style.display = 'none';
			if(response.data.status=="OK")
			{
				location.reload(true);
			}
			if(response.data.status=="ERROR")
			{
				alert(response.data.desc);
			}
		});
	}
	
	$scope.cufe = function()
	{
		alert(333);
		
		document.querySelector(".loader").style.display = 'block';
		let request;
		if($scope.fact_electronica != null)
		{
			 request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO.php",{acc:"enviar_confirmacion_cufe",factura:$scope.fact_electronica.factura});
		
		}
		else{
			request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO.php",{acc:"enviar_confirmacion_cufe",factura:findGetParameter("id")});			
		}
		request.then(function(response){
			document.querySelector(".loader").style.display = 'none';
			if(response.data.status=="OK")
			{
				location.reload(true);
			}
			if(response.data.status=="ERROR")
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