/********************
 * Autor Original: Jesús Vega
 * 
 * Proyecto: Prefacturas 
 * Documentos relacionados: 
 * Descripción del script:
 * Este script es el controlador angular para poder desde una interfaz que funciona como adaptador cambiar el comportamiento
 * de las facturas y crear el concepto de prefacturas en el sistema. 
 * Cambios:
  *********************/



app.controller('ExtensionController',['$scope','ExtensionService','$window','$compile','$filter','$q',function($scope,ExtensionService,$window,$compile,$filter,$q){
	$scope.test = "demo";
	$scope.sms = {};
	$scope.customer = {};
	$scope.pre_factura = {};
	$scope.solicitudes = [];
	$scope.detalles = [];
	$scope.date = new Date();
	$scope.prefacturas = {};
	$scope.detalles_prefactura = {};
	$scope.prefact  = {};
	$scope.factura = {};
	$scope.aprobar = "";
	
	
	$scope.get_table_solicitud_facturacion = function(){
		
		var object = {"Acc":"get_solicitud_factura"}; 
		
		request = ExtensionService.get_solicitudes_facturas(object);
		request.then(function(response){
				$("#content-extension").html(response.data);
		});
		
		$("#myModal").modal("show");
		
		//Todas las solicitudes de facturación 	donde fecha_proceso es = 0
	}
	
	$scope.comeback = function()
	{		
		//alert("triggered");
		$("#form-content-extension").hide();
		$("#content-extension").show();
		$("#btn_table").show();
		//Regresar a la interfaz original
	}
	
	$scope.comeback2 = function()
	{		
		//alert("triggered");		
		$("#form2-content-extension").hide();
		$("#form-content-extension").show();
		//Regresar a la interfaz intermedia
	}
	
	$scope.facturar_lote = function()
	{
		
		var sList = "";
		var value_validation;
		$scope.solicitudes = [];
		$('input[type=checkbox]').each(function () {
			var sThisVal = (this.checked ? this.value:null);
			if(sThisVal!=null)
			{
				sList += (sList=="" ? sThisVal : "," + sThisVal);
				
				tds = $("#"+sThisVal).find('td');
				
				var id = tds.eq(0); 
				var id_siniestro = tds.eq(1);  
				var cita = tds.eq(2);
				var concepto = tds.eq(3);
				var aseguradora = tds.eq(4);
				var descripcion = tds.eq(5);
				var cantidad = tds.eq(6);
				var valor = $("#valor_facturar"+sThisVal).val();
				var iva = $("#iva_factura"+sThisVal).val();
				var concepto_id = $("#concepto_id"+sThisVal).val();				
				
				var obj = {};

				obj.id = id.text();
				obj.id_siniestro = id_siniestro.text();
				obj.cita = cita.text();
				obj.concepto = concepto.text();
				obj.aseguradora = aseguradora.text();
				obj.descripcion = descripcion.text();
				obj.cantidad = cantidad.text();
				obj.valor = valor;	
				obj.iva = iva;
				//obj.total = (valor * cantidad);
				obj.concepto_id = concepto_id; 
				//console.log(obj);
				
				var request = ExtensionService.verify_prefact_siniester(obj.id_siniestro);
				request.then(function(response){
					if(response.data.status == 1)
					{
						$scope.solicitudes.push(obj);
					}
					else
					{
						alert("El siniestro con id "+obj.id_siniestro+" ya se encuentra registrado a una prefactura"); 
					}
				});
				//Verficamos si cada solicitud de factura ya tiene una prefactura 

				//console.log($scope.solicitudes); Aca la variable aun no existe
				
				if(obj.valor == "")
				{
					value_validation = 0;
				}
			}			
		});
		//console.log(value_validation);
	
		if(value_validation == 0 )
		{
			return alert("Todos los datos a facturar deben tener un valor");
		}	
		
		
		
		if(sList=="")
		{
			return alert("Selecciona solicitudes de factura para continuar");
		}
		//console.log (sList);
		request = ExtensionService.facturar_lote(sList);
		request.then(function(response){
			//console.log(response);
			if(response.data.validacion_aseguradoras != true)
			{
				alert("Las solicitudes que intenta facturar no pertenecen a la misma aseguradora");
				$('input[type=checkbox]').each(function () {
					$(this).prop('checked', false);	
				});
				
				//Validación de aseguradoras, esto lo hace desde el servidor
				
			}
			else
			{
				//Si todo sale bien crea una vista con el archivo user_form_solicitud_facturacion_extension.html
				
				/*if($scope.solicitudes.length == 0 )
				{
					return alert("Los items seleccionados no sirven para realizar un proceso");
				}*/
				
				$("#content-extension").hide();
				$("#btn_table").hide();
				$("#form-content-extension").show();
				$("#form-content-extension").html(response.data.html);
				
				//$scope.pre_factura.cliente = response.data.aseguradora.id;
			}
		});
	}
	
	$scope.get_customer_data = function()
	{
		$scope.customer.documento = $("input[name='identificacion']").val();
		request = ExtensionService.get_customer_data($scope.customer);
		request.then(function(response){			
			angular.forEach(response.data, function(value, key) {
			  $("#"+key).val(value);
			});
			$scope.get_ciudad(response.data.ciudad);
			$scope.pre_factura.cliente = response.data.id;	
		});
	}
	
	
	$scope.user_data_process = function()
	{
		if($scope.solicitudes.length == 0 )
		{
			return alert("La lista de items de la factura se encuentra vacia");
		}
		var validate = 1;
		var element = $("#userform").serializeArray();
		//console.log(element);
		angular.forEach(element, function(value, key) {
			 if(value.name != "telefono_oficina" && value.name != "telefono_casa" && value.name != "observaciones")
			 {
				if(value.value == "")
				{
					validate = 0;
					return alert("El campo con nombre "+value.name+" No puede ir vacio");
				}
			 }			  
		});
		
		//console.log(validate);
		
		if(validate == 0)
		{
			return "";
		}
		
		request = ExtensionService.user_data_process(element);
		request.then(function(response){			
			if(response.data.status == 1)
			{
				$scope.generate_prefactura();
				//Si todo sale bien genera la prefactura visual
			}
		});
	
	}
	
	$scope.generate_prefactura = function()
	{		
		if($('#form2-content-extension').is(':empty'))
		{			
			var html = "<div ng-include src=\"'/Control/operativo/views/subviews/pre_factura.html'\"></div>";	  
			var comp_html = $compile(html)($scope);
			$("#form-content-extension").hide();
			$("#form2-content-extension").html(comp_html);
			$("#form2-content-extension").show();
		}
		else
		{
			$("#form-content-extension").hide();
			$("#form2-content-extension").show();
			
		}
		
	
	}
	
	$scope.get_ciudades = function()
	{
		var departamento = $("#departamento").val();
		request = ExtensionService.get_ciudades(departamento);
		request.then(function(response){
			$("#myModal2").modal('show');			
			$("#asistant_modal_content").html(response.data.html);
			
		});
	}
	
	$scope.get_ciudad = function()
	{
		var codigo = $("#ciudad").val();
		request = ExtensionService.get_ciudad(codigo);
		request.then(function(response){
			$("#n_ciudad").val(response.data.nombre);
		});
	}
	
	
	$scope.add_ciudad = function(codigo,ciudad)
	{
		$("#n_ciudad").val(ciudad);
		$("#ciudad").val(codigo);
		$("#myModal2").modal('hide');
	}
	
	$scope.getTotal = function(){
		var total = 0;
		for(var i = 0; i < $scope.solicitudes.length; i++){
			var solicitud = $scope.solicitudes[i];
			total += (solicitud.cantidad * solicitud.valor);
		}		
		return total;
	}
	
	$scope.getTotal_iva = function(){
		var total = 0;
		for(var i = 0; i < $scope.solicitudes.length; i++){
			var solicitud = $scope.solicitudes[i];
			total += (solicitud.cantidad * solicitud.valor * (solicitud.iva/100));
		}
		return total;
	}
	
	$scope.encode_utf8 = function(s) {
	  return unescape(encodeURIComponent(s));
	}

	$scope.decode_utf8 = function(s) {	  
		try {
			s = decodeURIComponent(escape(s));
		}
		catch(err) {
		   console.log("URI ERROR");
		}	
	  return s;
	}
	
	$scope.validate_datos_fact = function()
	{
		
		
		if($("#fecha_emision").val()=="" || $("#fecha_vencimiento").val()=="")
		{
			alert("Seleccione fechas validas para realizar este proceso");
			return false;
		}
		
		var emision = new Date($("#fecha_emision").val());
		var vencimiento = new Date($("#fecha_vencimiento").val());

		if(vencimiento < emision)
		{
			alert("La manera en que se "+$scope.decode_utf8('enviarón')+" las horas quedo mal hecha");
			return false;
		}
		
		else
		{
			return true;
		}
	}
	
	$scope.grabar_datos_prefactura = function()
	{
		if(!confirm($scope.decode_utf8("¿Esta seguro de generar una pre factura?")))
		{
			return "";
		}	
		
		var process = $scope.validate_datos_fact();			
		
		if(process)
		{
			$scope.pre_factura.fecha_emision = $("#fecha_emision").val();
			$scope.pre_factura.fecha_vencimiento = $("#fecha_vencimiento").val(); 
		
			$scope.pre_factura.subtotal = $scope.getTotal();
			$scope.pre_factura.iva = $scope.getTotal_iva();
			$scope.pre_factura.total = $scope.getTotal()+$scope.getTotal_iva();
		
		}		
				
		detalles = $scope.solicitudes_detalles($scope.solicitudes);
		
		var object = {"pre_factura":$scope.pre_factura,"solicitudes":$scope.solicitudes,"detalles":detalles,'Acc' : 'grabar_datos_prefactura'};		
		
		
		var request = ExtensionService.grabar_datos_prefactura(object);
		
		request.then(function(response){
			
			if(response.data.status == 1)
			{
				alert(response.data.message);
				$("#myModal").modal('hide');
			}
			
		});
		
	}
	
	$scope.solicitudes_detalles = function(solicitudes)
	{
		console.log(solicitudes);
		detalles = [];
	
		solicitudes.forEach(function(solicitud){
			var obj = {};
			
			obj.idsolicitud = solicitud.id;
			obj.concepto = solicitud.concepto_id;
			obj.cantidad = solicitud.cantidad;
			obj.prefactura = null;
			obj.unitario = solicitud.valor;
			obj.total = solicitud.cantidad * solicitud.valor;
			obj.descripcion = solicitud.descripcion;
			obj.iva =  solicitud.cantidad * solicitud.valor * (solicitud.iva/100);
			obj.id_siniestro = solicitud.id_siniestro;
			detalles.push(obj); 
		});		
	
		return detalles;
	}
	
	
	
	
	$scope.generate_consecutivo_fact = function(id)
	{
		var obj = {"Acc":"generate_consecutivo_fact","prefactura":$scope.prefact};
		var request = ExtensionService.connect_ws(obj);
		request.then(function(response){
			if(response.data.status == 1)
			{
				alert(response.data.message);	
			}			
		});
	}
	
	$scope.aprove_fact = function(id)
	{
		var obj = {"Acc":"aprove_fact","prefactura":$scope.prefact};
		var request = ExtensionService.connect_ws(obj);
		request.then(function(response){
			if(response.data.status == 1)
			{
				alert(response.data.message);	
			}			
		});	
		
	}	
	
	$scope.aprobar_factura = function (id)
	{
		$scope.aprobar = 1;
		grabar_datos_factura();
		//return request = ExtensionService.aprobar_factura(id);
	}
	
	$scope.update_fec_solicitud = function(id){
		return request = ExtensionService.update_fec_solicitud(id);
	}	
	
	$scope.get_table_prefacturas = function(){
		
		$("#form-content-extension").hide();
		$("#form2-content-extension").hide();
		$("#content-extension").show();
		$scope.prefacturas = {};
		var request = ExtensionService.get_prefacturas();
		request.then(function(response){
			if(response.data != null)
			{
				$scope.prefacturas = response.data;		
				var html = "<div ng-include src=\"'/Control/operativo/views/subviews/prefacturas.html'\"></div>";
				var comp_html = $compile(html)($scope);
				$("#content-extension").html(comp_html);
				$("#myModal").modal('show');	
			}					
		});
	}
	
	$scope.get_details_prefactura = function(prefactura,edit=false)
	{
		$scope.detalles_prefactura = {};
		var request = ExtensionService.get_prefacturas_detalles(prefactura.id);
		request.then(function(response){			
			$scope.detalles_prefactura = response.data;
			$scope.prefact = $filter('filter')($scope.prefacturas, {id: prefactura.id})[0];
			
			var html = "<div ng-include src=\"'/Control/operativo/views/subviews/detalles_prefacturas.html'\"></div>";
			var comp_html = $compile(html)($scope);
			$("#form-content-extension").html(comp_html);
					
			$("#form-content-extension").show();
			
		});
	}
	
	$scope.add_prefact_detail = function()
	{
		var object = {"Acc":"get_solicitud_factura","aseguradora":$scope.prefact.aseguradora};
		request = ExtensionService.get_solicitudes_facturas(object);
		request.then(function(response){
				$("#asistant_modal_content").html(response.data);
				$("#myModal2").modal('show');
		});
	}
	
	$scope.add_to_detail = function()
	{
		
		var totalchecks = $('input[type=checkbox]:checked').length; 
		if( totalchecks == 0)
		{
			return alert("Seleccione alguna solicitud para continuar");
		}		
		var create_solictudes_array = function(){
			
			var deferred = $q.defer();	
			solicitudes = [];
			var count = 0;
			
			$('input[type=checkbox]:checked').each(function () {				
				var sThisVal = (this.checked ? this.value:null);
				if(sThisVal!=null)
				{
					//sList += (sList=="" ? sThisVal : "," + sThisVal);
					
					tds = $("#"+sThisVal).find('td');
					
					var id = tds.eq(0); 
					var id_siniestro = tds.eq(1);  
					var cita = tds.eq(2);
					var concepto = tds.eq(3);
					var aseguradora = tds.eq(4);
					var descripcion = tds.eq(5);
					var cantidad = tds.eq(6);
					var valor = $("#valor_facturar"+sThisVal).val();
					var iva = $("#iva_factura"+sThisVal).val();
					var concepto_id = $("#concepto_id"+sThisVal).val();				
					
					var obj = {};

					obj.id = id.text();
					obj.id_siniestro = id_siniestro.text();
					obj.cita = cita.text();
					obj.concepto = concepto.text();
					obj.aseguradora = aseguradora.text();
					obj.descripcion = descripcion.text();
					obj.cantidad = cantidad.text();
					obj.valor = valor;	
					obj.iva = iva;		
					obj.concepto_id = concepto_id; 
					
					if(obj.valor == "")
					{
						deferred.reject("Todos los datos a facturar deben tener un valor");
						return deferred.promise;
					}			
					else
					{
						
						var request = ExtensionService.verify_prefact_siniester(obj.id_siniestro);
						request.then(function(response){
							
							if(response.data.status == 1)
							{
								count++;
								console.log(count);
								//console.log(obj);
								solicitudes.push(obj);
								if(count == totalchecks)
								{
									//console.log("triggered");
									console.log(solicitudes);
									detalles = $scope.solicitudes_detalles(solicitudes);
									var res = {"solicitudes":solicitudes,"detalles":detalles};
									deferred.resolve(res);
									return deferred.promise
								}
							}
							else
							{
								deferred.reject("El siniestro con id "+obj.id_siniestro+" ya se encuentra registrado a una prefactura");
								return deferred.promise;							
							}
							
						});
					
					}
					
				}			
			});
			
			return deferred.promise;
		}
			
		var process = create_solictudes_array();
		process.then(function(result){
				
			console.log(result);
			var obj = {"solicitudes":result.solicitudes,"Acc":"add_detalle_prefactura","prefactura":$scope.prefact,"detalles":result.detalles};
			console.log(obj);			
			var request = ExtensionService.connect_ws(obj);
			request.then(function(response){
				if(response.data.status == 1)
				{
					alert(response.data.message);
					$scope.get_details_prefactura($scope.prefact,true);
					$("#myModal2").modal("hide");
				}
			});
		},function(reject){
			alert(reject);
		});
	
		
	}
	
	
	$scope.edit_detalles_prefactura = function(detalles)
	{
		details = angular.copy(detalles);
		var obj = {"Acc":"edit_detalles_prefactura","detalles":details};
		delete obj.detalles.concepto_nombre;
		var request = ExtensionService.connect_ws(obj);
		request.then(function(response){
			if(response.data.status==1)
			{
				$scope.edit_prefactura();	
			}			
		});
	}
	
	$scope.edit_prefactura = function()
	{
		pre_fact = angular.copy($scope.prefact);
 		var obj = {"Acc":"edit_prefactura","prefactura":pre_fact};
		delete obj.prefactura.nombre;
		delete obj.prefactura.apellido;
		delete obj.prefactura.factura;
		delete obj.prefactura.aseguradora;
		var request = ExtensionService.connect_ws(obj);
		request.then(function(response){
			if(response.data.status==1)
			{
				alert(response.data.message);	
			}
		});
	}
	
	$scope.sum_values = function(num1,num2)
	{
		return parseInt(num1)+parseInt(num2);
	}
	
	$scope.delete_detalles_prefactura = function(id)
	{
		if(!confirm($scope.decode_utf8("¿Estas seguro?")))
		{
			return "";
		}
		
		var  detail = $filter('filter')($scope.detalles_prefactura, {id: id})[0];
	
		/*
		$scope.prefact.subtotal = ($scope.prefact.subtotal-detail.total);
		$scope.prefact.iva = ($scope.prefact.iva-detail.iva);
		$scope.prefact.total = parseInt($scope.prefact.subtotal)+parseInt($scope.prefact.iva);	*/	

		$scope.detalles_prefactura = $scope.delete_from_scope(detail,$scope.detalles_prefactura);
		
		var request = ExtensionService.delete_detalles_prefactura(id);
		request.then(function(response)
		{
			if(response.data.status == 1)
			{
				alert(response.data.message);
				$scope.edit_prefactura();	
				
			}
		});
		
	}
	
	
	
	$scope.delete_prefactura = function(id)
	{
		if(!confirm($scope.decode_utf8("¿Estas seguro, esto adicionalmente eliminara todos los detalles de la prefactura?")))
		{
			return "";
		}
		
		var  prefact = $filter('filter')($scope.prefacturas, {id: id})[0];
		
		$scope.prefacturas = $scope.delete_from_scope(prefact,$scope.prefacturas);	
		
		//console.log($scope.detalles_prefactura);
		
		if($scope.detalles_prefactura != null && !$scope.isEmpty($scope.detalles_prefactura))
		{
			var  details = $filter('filter')($scope.detalles_prefactura, {prefactura: id});
			
			console.log(details);	
			
			angular.forEach(details, function(value, key) {			
				  $scope.detalles_prefactura = $scope.delete_from_scope(value,$scope.detalles_prefactura);
			});
			
			$scope.prefact.subtotal = null;
			$scope.prefact.iva = null;
			$scope.prefact.total = null;
		}		
		
		//$scope.comeback();
		
		
		var request = request = ExtensionService.delete_prefactura(id);
		request.then(function(response){
			if(response.data.status == 1)
			{
				alert("Prefactura elminiada");
			}
		});
	}
	
	
	
	$scope.delete_from_scope = function(object,array)
	{
		var index = array.indexOf(object);
		array.splice(index, 1);
		return array;
	}

	$scope.isEmpty = function(obj) {
		for(var key in obj) {
			if(obj.hasOwnProperty(key))
				return false;
		}
		return true;
	}
	
	$scope.number_validation = function(detalles){
		var prev_text = $(event.currentTarget).text();
		console.log("key pressed"+event.keyCode);
		if(event.keyCode < 48 || event.keyCode > 57)
		{
			 //alert("Valor no valido");
			 event.preventDefault();
			 //$(event.currentTarget).text("0");
		}
	}
	
	$scope.modify_quantity = function(detalles){
		//console.log($(event.currentTarget).text());
		if(!isNaN($(event.currentTarget).text()) && $(event.currentTarget).text()!="")
		{
			detalles.cantidad = $(event.currentTarget).text();
		}
		else
		{
			detalles.cantidad = 0;
			$(event.currentTarget).text(0);
		}
		detalles.total = detalles.cantidad*detalles.unitario;
		var obj = {"detalle":detalles,"Acc":"get_concepto_fac"};
		var request = ExtensionService.connect_ws(obj);
		request.then(function(response){
			console.log(response.data.concepto.porc_iva);
			detalles.iva = (response.data.concepto.porc_iva/100)*detalles.total;
		});
	}
	
	$scope.modify_unitary_value = function(detalles){
		if(!isNaN($(event.currentTarget).text()) && $(event.currentTarget).text()!="")
		{
			detalles.unitario = $(event.currentTarget).text();
		}
		else
		{
			detalles.unitario = 0;
			$(event.currentTarget).text(0);
		}
		detalles.total = detalles.cantidad*detalles.unitario;
		var obj = {"detalle":detalles,"Acc":"get_concepto_fac"};
		var request = ExtensionService.connect_ws(obj);
		request.then(function(response){
			console.log(response.data.concepto.porc_iva);
			detalles.iva = (response.data.concepto.porc_iva/100)*detalles.total;
		});
	}
	
	$scope.get_detalles = function(data,property)
	{
		//console.log(data);
		return data[property];
	}
	
	$scope.sum_column = function(elements,column)
	{
		var acum = 0;
		elements.forEach(function(element){
			acum = parseInt(acum)+parseInt(element[column]);
		});
		return acum;
	}
	
	$scope.imprimir_factura = function()
	{
		var obj = {"Acc":"get_fact_prefact","prefactura":$scope.prefact};
		var request = ExtensionService.connect_ws(obj);
		request.then(function(response){
			if(response.data.status == 1)
			{
				if(response.data.mode == 1)
				{
					$window.open("http://app.aoacolombia.com/Control/operativo/zfacturacion.php?Acc=imprimir_factura&id="+response.data.factura, "_blank");	
				}
				
				if(response.data.mode == 2)
				{
					$window.open("http://app.aoacolombia.com/Control/operativo/zfacturacion.php?Acc=imprimir_fake_factura&id="+response.data.factura, "_blank");	
				}
			}
			if(response.data.status == 2)
			{
				alert(response.data.message);
			}
		});
		
	}
}]);