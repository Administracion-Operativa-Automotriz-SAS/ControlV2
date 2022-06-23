app.controller("BrandController", function ($scope,$http,$filter,NgTableParams){
  	
  	$scope.title = "Propiedades de la App";

  	$scope.new_element = {};
     
    $scope.columns = [{Field:"term_id",title:"id"},{Field:"name",title:"Nombre marca",filter:{linea:"text"}}];

       $http.post("wordpressservices.php",{Acc:"get_terms_and_taxonomy",taxonomy:"marca"}).then(function(response){
       	  $scope.tableParams = new NgTableParams({},{ dataset: response.data.data});			
          $scope.tableParams.reload();
          $scope.current_term = "marca";
       }).catch(function(err){ alert("Ocurrio un error"); });


    $scope.create_new_term = function(new_element,taxonomy)
    {
    	swal({
		  title: '¿Estas seguro/a?',
		  text: "!No podras modificarlo sin el area de tecnología!",
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Si, Adelante!'
		}).then((result) => {
		  if (result.value) {
	    	$http.post("wordpressservices.php",{Acc:"create_term_taxonomy",taxonomy:taxonomy,name:new_element.name}).then(function(response){
	       	  console.log(response);
	        }).catch(function(err){ alert("Ocurrio un error"); });
		  }
		  else{
		  	//alert("cancelado");
		  }
		});    	
    	
    }   
});