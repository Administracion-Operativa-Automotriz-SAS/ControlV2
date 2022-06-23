var app = angular.module("generar_factura_manual",["ngTable"]);

app.directive('formatterDirective', function() {
  return {
    restrict: 'A',
    require: 'ngModel',
    link: function(scope, element, attrs, ngModel) {
		ngModel.$formatters.push(function(val) {
		  console.log(val);
          return parseInt(val, 10);
      });
    }
  };
});
app.controller("InterfazController", function ($scope,$http,$filter) {
	
	$scope.customers = [];
	$scope.customer_name = "";
	$scope.customer_iden = "";
	
	$scope.consecutive = {};

	$scope.customer = {};
	
	$scope.city = {};
	
	$scope.items = {};
	
	$scope.doc_details = {};
	
	$scope.bill_dates = {};
	
	$scope.bill_items = [];
	
	let selected_items = [];
	
	$scope.items_to_delete = [];
	
	$scope.tax_types = [{name:"19%",value:19},{name:"16%",value:16},{name:"0%",value:0}];

	
	$scope.find_customers = function(writed){
		event.preventDefault();
		if(writed != null && writed.length > 0)
		{
			let request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"get_customers_service",name:writed});
			request.then(function(response){
				console.log(response.data);	
				$scope.customers = response.data.customers;
			});	
		}
	}
	
	$scope.find_customer_by_iden = function(iden)
	{
		let request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"get_customer",iden:iden});
			request.then(function(response){
				console.log(response.data);	
				if(response.data.customer == null)
				{
					 swal({
						title: 'Título',
						text: 'El cliente no existe',
						html: '',
						type: 'info',
						timer: 3000,
					  });	
				}
				else
				{	
					$scope.customer = response.data.customer;
					$scope.city = response.data.city;
					console.log($scope.customer);
				}
			});
	}
	
	$scope.get_invoice_items = function(consecutive)
	{
		if($("select[name='tipo_documento']").val() == "ND")
		{
			return null;	
		}
		
		let request = $http.post("/Control/operativo/controllers/WebServices.php",{acc:"get_info_from_docs_M",consecutive:consecutive,type:$("select[name='tipo_documento']").val()});
		request.then(function(response){
			console.log(response.data);	
			if(response.data.document == null)
			{
				 swal({
					title: 'Título',
					text: 'La factura no existe',
					html: '',
					type: 'info',
					timer: 3000,
				  });	
			}
			else
			{	
				let response_invoice_meta = JSON.parse(response.data.document.json);
				console.log(response_invoice_meta);
				
				$scope.bill_items = response_invoice_meta.bill_items;
				
				console.log($scope.bill_items);
				
				$scope.bill_items.forEach(function(bill_item){					
					bill_item.amount = Number(bill_item.amount);
					bill_item.value = Number(bill_item.value);
					//bill_item.tax = Number(bill_item.tax);
					console.log(bill_item);
				});
				
				console.log($scope.bill_items);
				
			}
		});		
	}
	
	$scope.generate_invoice_details = function()
	{
		event.preventDefault();
		
		console.log($scope.bill_items);
		
		if($scope.bill_items != null)
		{
			$scope.doc_details.ingresos_generan_iva = 0;
			$scope.doc_details.ingresos_no_generan_iva = 0;
			$scope.doc_details.subtotal = 0;
			$scope.doc_details.iva = 0;
			$scope.doc_details.total = 0;
			
			$scope.bill_items.forEach(function(bill_item){
				
				console.log(bill_item);
				
				if(bill_item.tax != 0)
				{
					console.log(bill_item.amount * bill_item.value);
					$scope.doc_details.ingresos_generan_iva += (bill_item.amount * bill_item.value);				
				}
				else
				{
					$scope.doc_details.ingresos_no_generan_iva += (bill_item.amount * bill_item.value);
				}

				$scope.doc_details.subtotal += (bill_item.amount * bill_item.value);

				$scope.doc_details.iva += (bill_item.amount * bill_item.value)*(bill_item.tax/100);

				$scope.doc_details.total += ((bill_item.amount * bill_item.value))+((bill_item.amount * bill_item.value)*(bill_item.tax/100)); 
				
			});
		}
	}
	
	$scope.verify_item = function(index)
	{
		console.log(index);
		
		if(selected_items.indexOf(index))
		{
			selected_items.push(index);
		}else{
			let filtered_index = selected_items.indexOf(index);
			selected_items.splice(filtered_index,1);
		}
		
		console.log(selected_items);
	}
	
	$scope.add_item = function()
	{
		$scope.bill_items.push({});
	}
	
	$scope.delete_item = function()
	{
		let filtered_index;
		
		selected_items.forEach(function(s_item){	
			filtered_index = $scope.bill_items.indexOf(s_item);
			$scope.bill_items.splice(filtered_index,1);
		});
		
		selected_items = [];
	}
	
	$scope.send_document_to_server = function()
	{
		let alerts=[];
		
		event.preventDefault();
		
		$scope.consecutive =   document.querySelector("select[name='tipo_documento']").value+document.querySelector("#consecutivo").value;		
		
		console.log($scope.consecutive);		
		
		$scope.bill_dates.fecha_elaboracion = document.querySelector("input[name='fecha_elaboracion']").value;
		$scope.bill_dates.fecha_vencimiento = document.querySelector("input[name='fecha_vencimiento']").value;
		
		
		
		let fecha_ini = new Date($scope.bill_dates.fecha_elaboracion);
		let fecha_last = new Date($scope.bill_dates.fecha_vencimiento);

		if(fecha_last <= fecha_ini)
		{
			$scope.loading = false;
			return swal(
	                  'Opps',
	                  'La fecha mayor no puede ser menor o igual a la fecha menor',
	                  'question'
	                )
		}	
		
		$scope.orden = $("#orden").val();
		
		console.log($scope.bill_dates);
		
		console.log($( "input[name^='item']" ));
		
		if($scope.bill_items.length == 0)
		{
			alerts.push("No hay items a facturar");
		}
		
		if($scope.consecutive.length < 1)
		{
			alerts.push("Consecutivo invalido");
		}
		
		
		if($scope.customer.nombre  == null )
		{
			alerts.push("No existe el cliente");
		}
		
		
		if($scope.bill_dates.fecha_elaboracion  == "" )
		{
			alerts.push("No ha ingresado fecha de elaboración");
		}	
		
		if($scope.bill_dates.fecha_vencimiento == "")
		{
			alerts.push("No ha ingresado fecha de vencimiento");
		}
		
		if($( "input[name^='item']" ).length == 0 && $scope.bill_items == null)
		{
			alerts.push("Es necesario ponerle items a la factura");
		}
		
		if($scope.bill_items == null)
		{			
			assign_by_old_functions();
		}	
		
		
		
		let kilometers = document.querySelector("#kilometraje").value;
		
		if(kilometers == "" && ("select[name='tipo_documento']").val() != "ND" )
		{
			alerts.push("Faltan los datos del kilometraje");
		}	
		
		let observations = document.querySelector("#observaciones").value;
		
		//console.log("TIME TO SEND");
		
		$scope.comments = "Kilometraje "+kilometers+","+observations;
		
		if(alerts.length > 0)
		{
			let inner_text = "<ul>";
			
			for(i =0 ; i < alerts.length ; i++)
			{
				inner_text += "<li>"+alerts[i]+"</li>";
			}
			
			inner_text += "</ul>";
			
			console.log(inner_text);
			
			 swal({
				title: 'Título',
				text: 'No puedo guardar la factura',
				html: inner_text,
				type: 'info',
				timer: 3000,
			  });	
		}
		else
		{			
			let request;
			if($("select[name='tipo_documento']").val() == "TA")
			{	
				request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"make_elec_document",bill_dates:$scope.bill_dates,doc_details:$scope.doc_details,bill_items:$scope.bill_items,customer:$scope.customer,city:$scope.city,consecutive:$scope.consecutive,comments:$scope.comments,doc:1,orden:$scope.orden});
			}
			if($("select[name='tipo_documento']").val() == "KT")
			{	
				request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO3.php",{acc:"make_elec_document",bill_dates:$scope.bill_dates,doc_details:$scope.doc_details,bill_items:$scope.bill_items,customer:$scope.customer,city:$scope.city,consecutive:$scope.consecutive,comments:$scope.comments,doc:1,orden:$scope.orden});
			}
			if($("select[name='tipo_documento']").val() == "TV")
			{	
				request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO3.php",{acc:"make_elec_document",bill_dates:$scope.bill_dates,doc_details:$scope.doc_details,bill_items:$scope.bill_items,customer:$scope.customer,city:$scope.city,consecutive:$scope.consecutive,comments:$scope.comments,doc:1,orden:$scope.orden});
			}
			if($("select[name='tipo_documento']").val() == "ND")
			{	
				if($("#consecutivo_factura").val()=="")
				{
					return alert("Necesita enviar el consecutivo de la factura");
				}
				request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"make_elec_document",bill_dates:$scope.bill_dates,doc_details:$scope.doc_details,bill_items:$scope.bill_items,customer:$scope.customer,city:$scope.city,consecutive:$scope.consecutive,doc:3,fact_cons:$("#consecutivo_factura").val(),orden:$scope.orden});
			}
			if($("select[name='tipo_documento']").val() == "DT")
			{	
				if($("#consecutivo_factura").val()=="")
				{
					return alert("Necesita enviar el consecutivo de la factura");
				}
				request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO3.php",{acc:"make_elec_document",bill_dates:$scope.bill_dates,doc_details:$scope.doc_details,bill_items:$scope.bill_items,customer:$scope.customer,city:$scope.city,consecutive:$scope.consecutive,doc:2,fact_cons:$("#consecutivo_factura").val(),orden:$scope.orden});
			}
			
			request.then(function(response){
				console.log(response.data);	
				if(response.data.status == "NO VALID" || response.data.status == "ERROR")
				{	
					return swal(
					  'Opps',
					   response.data.desc,
					  'question'
					);
				}
				else{
					if(response.data.status == "OK")
					{
						swal(
						  '¡Bien!',
						  'Factura generada',
						  'success'
						);
					}
					else{
						alert("Sucedio un error inesperado");
					}
				}
			});
		}	
		
		
		
	}
	
	function assign_by_old_functions()
	{
		for(k =0 ; k< $( "input[name^='item']" ).length; k++)
		{
			console.log(k);
			console.log("input[name='item"+(parseInt(k)+1)+"']");
			$scope.bill_items[k] = {};
			
			$scope.bill_items[k].desc = document.querySelector("input[name='item"+(parseInt(k)+parseInt(1))+"']").value;
			$scope.bill_items[k].amount = document.querySelector("input[name='cant"+(parseInt(k)+parseInt(1))+"']").value;
			$scope.bill_items[k].value = document.querySelector("input[name='valor"+(parseInt(k)+parseInt(1))+"']").value;
			$scope.bill_items[k].tax = document.querySelector("select[name='iva"+(parseInt(k)+parseInt(1))+"']").value;
			
			if($scope.bill_items[k].desc == "" || $scope.bill_items[k].amount == "" || $scope.bill_items[k].value == "" || $scope.bill_items[k].tax == "")
			{
				alerts.push("Los items no se encuentran completos");
			}
		
		}
		
		$scope.doc_details.ingresos_generan_iva = document.querySelector("#pre_iva").innerText.replace("$", "");		
		
		if($scope.doc_details.ingresos_generan_iva == "")
		{
			alerts.push("Faltan los datos que generan iva");
		}
		
		
		$scope.doc_details.ingresos_no_generan_iva = document.querySelector("#pre_noiva").innerText.replace("$", "");;	
		
		if($scope.doc_details.ingresos_no_generan_iva == "")
		{
			alerts.push("Faltan los datos que no generan iva");
		}
		
		$scope.doc_details.subtotal = document.querySelector("#pre_subtotal").innerText.replace("$", "");
		
		
		if($scope.doc_details.subtotal == "")
		{
			alerts.push("Faltan los datos de subtotal");
		}
		
		
		$scope.doc_details.iva = document.querySelector("#pre_valoriva").innerText.replace("$", "");;
		
		if($scope.doc_details.iva == "")
		{
			alerts.push("Faltan los datos de iva");
		}
		
		$scope.doc_details.total = document.querySelector("#pre_total").innerText.replace("$", "");;
		
		if($scope.doc_details.total == "")
		{
			alerts.push("Faltan los datos del total");
		}
		
		console.log($scope.doc_details);
		
	}
	
	
});




function encode_utf8(s)
{
	return unescape(encodeURIComponent(s));
}

function decode_utf8(s)
{
	return decodeURIComponent(escape(s));
}
