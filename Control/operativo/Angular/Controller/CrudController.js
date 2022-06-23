app.controller('CrudController',['$scope','$timeout','SystemService','$window','$compile','$filter','NgTableParams',function($scope,$timeout,SystemService,$window,$compile,$filter,NgTableParams){

	var self = this;

	$scope.load = function()
	{		
		html = generate_html($scope.centros_servicio);
		//console.log(html);
		var comp_html = $compile(html)($scope);
		$("#centros_atencion_table").html(comp_html);
		//alert("loaded");
	}

	//$scope.current_false_foreign = {};

	$scope.new_element = {};	
	
	$scope.centros_servicio = new table('aon_centro_atencion_comcel',SystemService,null,false);

	$scope.centros_servicio.alias = "Centros de servicio";	

	$scope.ciudades = new table('aon_ciudad',SystemService,null,false);

	$scope.zonas = new table('aon_zona',SystemService,null,false);

	$scope.centros_servicio = new table('aon_centro_atencion_comcel',SystemService,null,false);

	$scope.facturas_emai = new table('aon_cac_mom',SystemService,null,false);

	$scope.facturas_emai.alias = "Facturas emai";	

	$scope.evento_estado = new table("aon_estado_evento",SystemService,null,false);

	$scope.evento_estado.alias = "Estados del evento";

	$scope.otros_tickets = new table("otros_tickets",SystemService,null,false);

	$scope.aon_tipo_evento = new table("aon_tipo_evento",SystemService,null,false);

	$scope.modelosmoviles = new table("ModelosMoviles",SystemService,null,false);

	$scope.modelosmoviles.validation = [{type:'edit_or_create',columns:["plu"]}];
	
	$scope.valor_reparacion_pantalla = new table("valor_reparacion_pantalla",SystemService,null,false);

	$scope.valor_reparacion_pantalla.validation = [{type:'edit_or_create',columns:["PLU"]}];

	$scope.evento_estado.alias = "No eventos";

	$scope.fabricantes_moviles = new table('seguros.aon_fabricante_movil',SystemService,null,false);

	$scope.gamas_celulares = new table('seguros.gamas_celulares',SystemService,null,false);

	(function() {
		 
		  console.info("Start Crud process");

		  var tables_array = [$scope.centros_servicio,$scope.ciudades,$scope.zonas,$scope.centros_servicio,$scope.facturas_emai,$scope.evento_estado,$scope.otros_tickets,$scope.aon_tipo_evento,$scope.modelosmoviles,$scope.fabricantes_moviles];

	      var promises = [];

		  tables_array.forEach(function(table_array){
		  		promises = promises.concat(table_array.ready); 
		  });

		  console.log(promises);

		  Promise.all(promises).then(values => {			  
			  
			  //ready_to_render_view();
			  alert("Tablas cargadas");
		  });

		  
	 }()); 

	$scope.select_table = function()
	{		
		//console.log($scope.table_selected);
		if($scope.table_selected != null)
		{
			//console.info("here");
			self.tableParams = new NgTableParams({},{ dataset: $scope[$scope.table_selected].rows});
			$scope.current_columns = $scope[$scope.table_selected].columns;
			$scope.current_view = $scope[$scope.table_selected];					
			self.tableParams.reload();
		}
		
	}




	var false_foreignskey = [{ local_key:"CIU_ID", foreign_key:"CIU_ID", foreign_value:"CIU_NOMBRE", dataset: 'ciudades'},{ local_key:"ZON_ID", foreign_key:"ZON_ID",foreign_value:"ZON_NOMBRE", dataset: 'zonas'}];	
	$scope.centros_servicio.false_foreignskey_subscriber = false_foreignskey;

	var false_foreignskey_marca = [{ local_key:"marca", foreign_key:"FAM_ID", foreign_value:"FAM_NOMBRE", dataset: 'fabricantes_moviles'}];	
	$scope.modelosmoviles.false_foreignskey_subscriber = false_foreignskey_marca;
	

	$scope.edit = function(row,model)
	{
		console.log(row);		
		model.update(row);		
	}
	
	$scope.delete = function(row,model)
	{	
		model.delete(row,self.tableParams);	
	}
	
	$scope.create = function(row,model)
	{
		//console.log(row);
		if(isEmpty(row))
		{
			return alert("No puede crear datos con valores vacios");
		}
		else
		{
			model.create(row,$scope.copycat,self.tableParams);
		}
	}

	
	$scope.column_behavior = function(col,element)
	{	
		//console.log(col.Key);	
		if(col.Key != "")
		{
			return false;
		}

		return true;
		
	}



	$scope.evaluate_false_foreigners = function(col,falseforeigns)
	{
		var current = false;

		if(falseforeigns != null)
		{
			falseforeigns.forEach(function(element){
				if(col.Field == element.local_key)
				{
					current = element;
				}	
			});	
		}	
		
		//console.log(current);

		return current;	
	}

	$scope.dynamicArray = function(name){   	
		if($scope[name] != null)
		{
			return $scope[name].rows;	
		}
    	
	}



}]);

function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}