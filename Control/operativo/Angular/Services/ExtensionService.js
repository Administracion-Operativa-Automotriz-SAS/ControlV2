app.factory('ExtensionService',['$http','$q',function($http,$q){
	
	var ExtensionService = {};
    var defered = $q.defer();
    var promise = defered.promise;

	ExtensionService.facturar_lote = function(citas){
		var formData = new FormData();
		formData.append('citas', citas);
		formData.append('Acc', "insertar_desde_citas");
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: {
			  'Content-Type': undefined
			}
	   });	
	}
	
	ExtensionService.get_customer_data = function(data)
	{
		var formData = new FormData();
		formData.append('identificacion', data.documento);
		formData.append('Acc', 'get_cliente');
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: {
			  'Content-Type': undefined
			}
	   });	 	
	}
	
	
	ExtensionService.get_ciudades = function(departamento)
	{
		var formData = new FormData();
		formData.append('departamento', departamento);
		formData.append('Acc', 'get_ciudades');
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: {
			  'Content-Type': undefined
			}
	   });	 	
	}
	
	ExtensionService.get_ciudad = function(ciudad)
	{
		var formData = new FormData();
		formData.append('codigo', ciudad);
		formData.append('Acc', 'get_ciudad');
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: {
			  'Content-Type': undefined
			}
	   });	 	
	}
	
	ExtensionService.get_solicitudes_facturas = function(data)
	{
		
		return $http.post("controllers/SolicitudFacturacion.extension.php",data);
	}

	ExtensionService.connect_ws = function(data)
	{
		
		return $http.post("controllers/SolicitudFacturacion.extension.php",data);
	}	
	
	ExtensionService.user_data_process = function(element)
	{
		
		var formData = new FormData();
		angular.forEach(element, function(value, key) {
			//console.log(value.name+" "+value.value);
			 formData.append(value.name, value.value); 
		});
		formData.append('Acc', 'user_data_process');
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: {
			  'Content-Type': undefined
			}
	   });	 	
		
	}
	
	ExtensionService.grabar_datos_prefactura = function(element)
	{		
		return $http.post("controllers/SolicitudFacturacion.extension.php",element);		 	
		
	}
	
	
	ExtensionService.get_last_id_prefact = function()
	{
		var formData = new FormData();		
		formData.append('Acc', 'get_last_id_prefact');
		
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: { 
			  'Content-Type': undefined
			}
	   });
	}
	
	ExtensionService.get_prefacturas = function()
	{
		var formData = new FormData();		
		formData.append('Acc', 'get_prefacturas');
		
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: { 
			  'Content-Type': undefined
			}
	   });
	}
	
	ExtensionService.get_prefacturas_detalles = function(id)
	{
		var formData = new FormData();		
		formData.append('Acc', 'get_prefacturas_detalles');
		formData.append('factura', id);
		
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: { 
			  'Content-Type': undefined
			}
	   });
	}

	ExtensionService.verify_prefact_siniester = function(id)
	{
		var formData = new FormData();		
		formData.append('Acc', 'verify_prefact_siniester');
		formData.append('id_siniestro', id);
		
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: { 
			  'Content-Type': undefined
			}
	   });
	}
	
	ExtensionService.generate_consecutivo_fact = function(id)
	{
		var formData = new FormData();		
		formData.append('Acc', 'generate_consecutivo_fact');
		formData.append('id', id);
		
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: { 
			  'Content-Type': undefined
			}
	   });
	}
	
	
	ExtensionService.aprobar_factura = function(id)
	{
		var formData = new FormData();		
		formData.append('Acc', 'aprobar_factura');
		formData.append('id', id);
		
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: { 
			  'Content-Type': undefined
			}
	   });
	}

	ExtensionService.delete_detalles_prefactura = function(id)
	{
		var formData = new FormData();		
		formData.append('Acc', 'delete_detalles_prefactura');
		formData.append('id', id);
		
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: { 
			  'Content-Type': undefined
			}
	   });
	}

	ExtensionService.delete_prefactura = function(id)
	{
		var formData = new FormData();		
		formData.append('Acc', 'delete_prefactura');
		formData.append('id', id);
		
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: { 
			  'Content-Type': undefined
			}
	   });
	}

	ExtensionService.update_fec_solicitud = function(id)
	{
		var formData = new FormData();		
		formData.append('Acc', 'update_fec_solicitud');
		formData.append('id', id);
		
		return $http({
			url: "controllers/SolicitudFacturacion.extension.php",
			method: "POST",
			data: formData,
			headers: { 
			  'Content-Type': undefined
			}
	   });
	}
	
	return ExtensionService;
	
}]);
