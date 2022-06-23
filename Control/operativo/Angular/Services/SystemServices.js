app.factory('SystemServices',['$http','$q',function($http,$q){
	
/********************
 * Autor Original: Jesús Vega
 * 
 * Proyecto:  
 * Documentos relacionados: 
 * Descripción del script:
 * El script es un pool de servicios que sirve como helper para solucionar problemas que hallan con cualquier tipo de servicio
 * Cambios:
 *Autor: Jesús Vega
 * 1. Se creo script 
 * Fecha:25/02/2019
 *********************/

	
	let SystemServices = {};	
	
	SystemServices.getSession = function()
	{		
		
		return $http.post("http://app.aoacolombia.com/Control/operativo/controllers/WebServices.php",{acc:"getSessions"});				
	}
	
	SystemServices.generatePostRequest = function(data)
	{
		return $http.post(data.endPoint,data.request);
	}
	return SystemServices;
	
}]);