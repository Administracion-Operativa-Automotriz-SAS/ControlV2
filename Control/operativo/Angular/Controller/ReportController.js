/********************
 * Autor Original: Jesús Vega
 * 
 * Proyecto:
 * Documentos relacionados:
 * Descripción del script:
 * Este archivo hace parte del framework desarrollado en Javascript para la generación de reportes y tablas
 *con la minima cantidad de instrucciones front end posibles, este archivo permite crear reportes dinamicos y veloces
 *Autor: Jesús Vega
 *1. Se modifico la manera en que opera el reporte documentos_manuales_electronicos_generados agregando un filtro a los datos
 *2. Se creo la función y la logica del metodo pasteCopiedRows
 *Fecha:23/02/2019
 ****************************************************/

app.controller('ReportController',['$scope','$timeout','ReportService','$window','$compile','$filter','NgTableParams','$http',function($scope,$timeout,ReportService,$window,$compile,$filter,NgTableParams,$http){
	
	var self = this;
	
	let copyOfRows = [];
	
	$scope.data_export = [];

	$scope.export_fields = {};	
	
	$scope.ready_export_excel = false;
	
	$scope.run_report = function(report_name)
	{
		$scope.loading = true;
		var request = ReportService.get_report({name:report_name});
		request.then(function(response){
			self.columns = [];
			self.rows = response.data.rows;
			var response_columns = response.data.columns;
			
			angular.forEach(response.data.columns, function(field, key) {
				$scope.export_fields[field] = field;
		    });
			
			
			$scope.data_export = response.data.rows;
			
			
			//console.info($scope.export_fields);
			//console.info($scope.data_export);
			
			switch(report_name) {
				
				case "documentos_manuales_electronicos_generados":
					console.log("Custom filters");
					for(let i = 0 ; i<Object.keys(response_columns).length ; i++)
					{		
						let object_add;
						//console.log(response_columns[i]);
						if(response_columns[i] == "estadot")
						{
							let filter_object = {};
							filter_object[response_columns[i]] = 'select';
							object_add = {title:response_columns[i],Field:response_columns[i],field:response_columns[i],filter:filter_object,filterData:[{id:"Facturado",title:"Facturado"},{id:"A verificar",title:"A verificar"}],sortable:response_columns[i]};		
							console.log(object_add);
						}
						else
						{
							let filter_object = {};
							filter_object[response_columns[i]] = 'text';
							object_add = {title:response_columns[i],Field:response_columns[i],field:response_columns[i],filter:filter_object,sortable:response_columns[i]};							
						}
						self.columns.push(object_add);
					}				
				break;				
				default:				
					for(let i = 0 ; i<Object.keys(response_columns).length ; i++)
					{		
						let filter_object = {};
						filter_object[response_columns[i]] = 'text';
						let object_add = {title:response_columns[i],Field:response_columns[i],field:response_columns[i],filter:filter_object,sortable:response_columns[i]};
					
						self.columns.push(object_add);
					}
				break;
			}	
			
			
			
			
			
			
			//console.log(self.columns);
			self.ReportTable = new NgTableParams({},{ dataset: self.rows});			
			$scope.table_name = get_table_name(report_name);
			
			
			if(self.rows.length > 0)
			{
				custom_columns(report_name);
			}
			
			$scope.loading = false;
		
			$scope.ready_export_excel = true;
			//console.log(self.ReportTable);
			
		});
	}
	
	$scope.report_filter = function(report_name)
	{

			switch(report_name) {
			case "reporte_requisiciones_fechas":
			console.log($("input[name='fecha_inicial']").val()+" "+$("input[name='fecha_final']").val());
			$scope.loading = true;
			var request = ReportService.get_report({name:report_name,fecha_inicio:$("input[name='fecha_inicial']").val(),fecha_final:$("input[name='fecha_final']").val()});
			request.then(function(response){
				self.rows = response.data.rows;
				self.ReportTable = new NgTableParams({},{ dataset: self.rows});			
				$scope.data_export = response.data.rows;
				$scope.loading = false;
			});
			break;
			
			default:
			break;
		}
		switch(report_name) {
			case "listado_vehiculos_flota":
			console.log($("select[name='estados']").val());
			$scope.loading = true;
			var request = ReportService.get_report({name:report_name,estados:$("input[name='estados']").val()});
			request.then(function(response){
				self.rows = response.data.rows;
				self.ReportTable = new NgTableParams({},{ dataset: self.rows});			
				$scope.data_export = response.data.rows;
				$scope.loading = false;
			});
			break;
			default:
			break;
		}
		switch(report_name) {
			case "listado_vehiculos_flota_dos":
			console.log($("select[name='estados']").val());
			$scope.loading = true;
			var request = ReportService.get_report({name:report_name,estados:$("input[name='estados']").val()});
			request.then(function(response){
				self.rows = response.data.rows;
				self.ReportTable = new NgTableParams({},{ dataset: self.rows});			
				$scope.data_export = response.data.rows;
				$scope.loading = false;
			});
			break;
			default:
			break;
		}
		switch(report_name) {
			case "citas_servicio":
			console.log($("input[name='fecha_inicial']").val()+" "+$("input[name='fecha_final']").val());
			$scope.loading = true;
			var request = ReportService.get_report({name:report_name,fecha_inicio:$("input[name='fecha_inicial']").val(),fecha_final:$("input[name='fecha_final']").val()});
			request.then(function(response){
				self.rows = response.data.rows;
				self.ReportTable = new NgTableParams({},{ dataset: self.rows});			
				$scope.data_export = response.data.rows;
				$scope.loading = false;
			});
			break;
			default:
			break;
		}
		switch(report_name) {
			case "control_siniestro":
			console.log($("input[name='fecha_inicial']").val()+" "+$("input[name='fecha_final']").val());
			$scope.loading = true;
			var request = ReportService.get_report({name:report_name,fecha_inicio:$("input[name='fecha_inicial']").val(),fecha_final:$("input[name='fecha_final']").val()});
			request.then(function(response){
				self.rows = response.data.rows;
				self.ReportTable = new NgTableParams({},{ dataset: self.rows});			
				$scope.data_export = response.data.rows;
				$scope.loading = false;
			});
			break;
			default:
			break;
		}
	}
	

	function get_table_name(report_name)
	{
		console.log(report_name);
		switch(report_name) {
			case "listado_vehiculos_flota":
				return "Listado de vehiculos por flota";
			break;
			case "citas_servicio":
				return "Citas de Servicio ";
			case "control_siniestro":
				return "Control de siniestro";
			case "documentos_manuales_electronicos_generados":
				return "Documentos electronicos de taller";
			case "facturacion_electronica_reporte":
			    return "Facturacion electronica reporte";
			case "listado_vehiculos_flota_dos":
			    return "Estado vehiculos y alertas";
			case "nota_credito_electronica_reporte":
			    return "Nota electronica reporte";
			default:
			break;
		}
		
		return false;
	}
	
	
	function custom_columns(report_name)
	{	
	/*Variable para ocultar la fila descr*/ let descr_col;
		
		return new Promise( (resolve, reject) => {
			switch(report_name) {
				case "reporte_requisiciones_fechas":				
					console.log("exec");
					break;
				case "documentos_manuales_electronicos_generados":
								
					let estado_col = $filter('filter')(self.columns,{Field:"estado"})[0];
					
					console.log(estado_col);
					estado_col.show = false;
					
					descr_col = $filter('filter')(self.columns,{Field:"descr"})[0];
					
					descr_col.show = false;
					
					let json_col = $filter('filter')(self.columns,{Field:"json"})[0];
					
					json_col.show = false;				
					
					self.columns.push({title:"desc proceso",field:"desc_proceso",Field:"desc_proceso",filter:{desc_proceso:"text"}},{title:"Opciones",Field:"Opciones",actions:[{text:"XML",title:"xml",action:"generate_xml"},{icon:"fas fa-eye",title:"Consulta representación gráfica",action:"query_gr_rep",conditional:[{field:"estado",value:1,type:"equal"}]},{icon:"fas fa-forward",title:"Reenviar al servidor",action:"resend_service",conditional:[{field:"estado",value:1,type:"different"}]}]});					
					
					copyOfRows = angular.copy(self.rows);
					
					self.rows.forEach(function(row){
						
						
						// Agrego validación para mostrar solo los datos en su ultimo estado
						
						if(row.estadot == "Facturado")
						{
							let index; 
							$filter('filter')(self.rows,function(f_row){
								
								if(f_row.consecutivo == row.consecutivo && f_row.estadot != "Facturado")
								{
									//console.log(row);
									//console.log(f_row);
									return f_row;
								}
								
							}).forEach(
							function(other_row){
								//console.log(other_row);
								index = self.rows.indexOf(other_row);
								if (index > -1) 
								{
								  self.rows.splice(index, 1);
								}							
							});	
						}
						
						
						
						
						if(isJson(row.descr))
						{
							inner_json = JSON.parse(row.descr);
							row["desc_proceso"] = inner_json.respuesta.mensajeRespuesta;
							
						}
						else{
							row["desc_proceso"] = row.descr;
						}
						
						
						
						
						
						
					});
					
					
					
					break;
                 case "facturacion_electronica_reporte":
				 
					 self.rows.forEach(function(row){
						 if(row.estado == "Facturado")
						 {
							let index; 
							$filter('filter')(self.rows,function(f_row){
								
								if(f_row.consecutivo == row.consecutivo && f_row.estado != "Facturado")
								{
									return f_row;
								}
								
							}).forEach(
							function(other_row){
								//console.log(other_row);
								index = self.rows.indexOf(other_row);
								if (index > -1) 
								{
								  self.rows.splice(index, 1);
								}							
							});	
						 }
						
					 });
				 
				 
				    self.columns.push({title:"Opciones",Field:"Opciones",actions:[{text:"XML",title:"xml",action:"generate_xml_report"},{icon:"fas fa-eye",title:"Consulta representación gráfica",action:"query_gr_report",conditional:[{field:"estado",value:'Facturado',type:"equal"}]},{icon:"fas fa-forward",title:"Reenviar al servidor",action:"resend_service_report",conditional:[{field:"estado",value:'Facturado',type:"different"}]}]});
				 
					descr_col = $filter('filter')(self.columns,{Field:"descr"})[0];
					
					descr_col.show = false;
				    
					self.rows.forEach(function(row){
						if(isJson(row.descr))
						{							
							inner_json = JSON.parse(row.descr);
							if(inner_json != null )
							{	
								row["desc_proceso"] = inner_json.respuesta.mensajeRespuesta;
							}
						}
						else{
							row["desc_proceso"] = row.descr;
						}
					});
				 
				 break;
				case "nota_credito_electronica_reporte":
					
					self.rows.forEach(function(row){
						 if(row.estado == "Facturado")
						 {
							let index; 
							$filter('filter')(self.rows,function(f_row){
								
								if(f_row.consecutivo == row.consecutivo && f_row.estado != "Facturado")
								{
									return f_row;
								}
								
							}).forEach(
							function(other_row){
								//console.log(other_row);
								index = self.rows.indexOf(other_row);
								if (index > -1) 
								{
								  self.rows.splice(index, 1);
								}							
							});	
						 }
						
					 });
					 
					descr_col = $filter('filter')(self.columns,{Field:"descr"})[0];
					
					descr_col.show = false;
					 
					 self.columns.push({title:"Opciones",Field:"Opciones",actions:[{text:"XML",title:"xml",action:"generate_xml_report_for_NC"},{icon:"fas fa-eye",title:"Consulta representación gráfica",action:"query_gr_report",conditional:[{field:"estado",value:'Facturado',type:"equal"}]},{icon:"fas fa-forward",title:"Reenviar al servidor",action:"resend_service_report",conditional:[{field:"estado",value:'Facturado',type:"different"}]}]});				 
					
					
					break;
				default:
					break;
			}
			resolve("¡Done!");	
		});
		
	}
	
	
	//BEGIN Json Conditional
	
	$scope.check_conditional = function(row,conditional)
	{
		if(!conditional)
		{
			return true;
		}
		else{
			
			let check_condition = true;
			
			conditional.forEach(function(condition){
				
				if(check_condition == false)
				{
					return check_condition;
				}
				
				check_condition = false;
				
				switch(condition.type) {
				  case "equal":					
					if(row[condition.field] ==  condition.value)
					{
						return check_condition = true;
					}
					break;
				  case "different":
					if(row[condition.field] !=  condition.value)
					{
						return check_condition = true;		
					}
					break;
				  default:
						return check_condition = true;
					break;
				}
				
			});
			
			return check_condition;
			
		}
	}
	
	//END Json Conditional
	
	
	//BEGIN Json custom fucntions
	
	$scope.execute_function = function(function_name,row)
	{
		console.log(function_name);
		console.log(row);
		
		if(function_name == "generate_xml")
		{
			
			if(row.consecutivo.includes("TA"))
			{
				
				return window.open("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php?XMLMANUAL_FACT="+row.id);
			}
			
			
			if(row.consecutivo.includes("ND"))
			{
				
				return window.open("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php?XMLMANUAL_ND="+row.id);
			}
			
			if(row.consecutivo.includes("DT")){
				
				console.log(row.id);
				
				return window.open("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php?XMLMANUAL_DT="+row.id);
			}
		     	
			alert("No se puede generar el xml");
			
			return;
			
		}
		if(function_name == "generate_xml_report"){
			return window.open("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php?XMLFACT="+row.id+"&XMLCONSE="+row.consecutivo);
		}
		
		if(function_name == "generate_xml_report_for_NC")
		{
			return window.open("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php?XMLNC="+row.id+"&XMLCONSE="+row.consecutivo);
		}
		
		if(function_name == "query_gr_rep")
		{
			$scope.loading = true;
			
			let json_response =  JSON.parse(row.descr);		
			console.log(json_response);
			let cufe_code = json_response.respuesta.cufe;
			let request;
			
			if(row.consecutivo.includes("TA"))
			{
				request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"representacion_grafica",cufe:cufe_code,tipo_documento:1});
				request.then(function(response){
					$scope.loading = false;
					let pdfWindow = window.open("")
					pdfWindow.document.write("<iframe width='100%' height='100%' src='data:application/pdf;base64, " + encodeURI(response.data.pdfbase64)+"'></iframe>")
					
				});
			}
			if(row.consecutivo.includes("ND"))
			{
				request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"representacion_grafica",cufe:cufe_code,tipo_documento:3});
				request.then(function(response){
					$scope.loading = false;
					let pdfWindow = window.open("")
					pdfWindow.document.write("<iframe width='100%' height='100%' src='data:application/pdf;base64, " + encodeURI(response.data.pdfbase64)+"'></iframe>")
					
				});
			}
			
			/*Adicion de representacion grafica para nota credito*/
			if(row.consecutivo.includes("DT")){
				request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"representacion_grafica",cufe:cufe_code,tipo_documento:2});
				request.then(function(response){
				    console.log(request);
					$scope.loading = false;
					let pdfWindow = window.open("")
					pdfWindow.document.write("<iframe width='100%' height='100%' src='data:application/pdf;base64, " + encodeURI(response.data.pdfbase64)+"'></iframe>")
					
				});
			}
			
			
		}
		
		
		
		
		if(function_name == "query_gr_report"){
			
			$scope.loading = true;
			
			let json_response =  JSON.parse(row.descr);		
			console.log(json_response);
			let cufe_code = json_response.respuesta.cufe;
			let request;
			
			if(row.consecutivo.includes("NC"))
			{
				request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"representacion_grafica",cufe:cufe_code,tipo_documento:2});
				request.then(function(response){
					$scope.loading = false;
					let pdfWindow = window.open("")
					pdfWindow.document.write("<iframe width='100%' height='100%' src='data:application/pdf;base64, " + encodeURI(response.data.pdfbase64)+"'></iframe>")
					
				});
			}
			
			
			if(row.consecutivo.includes("FE"))
			{
				request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"representacion_grafica",cufe:cufe_code,tipo_documento:1});
				request.then(function(response){
					$scope.loading = false;
					let pdfWindow = window.open("")
					pdfWindow.document.write("<iframe width='100%' height='100%' src='data:application/pdf;base64, " + encodeURI(response.data.pdfbase64)+"'></iframe>")
					
				});			
			}
		}
		
		
		
		if(function_name == "resend_service")
		{
			let done = $filter('filter')(self.rows,{estado:1,consecutivo:row.consecutivo})[0];
			
			if(done)
			{
				return alert("Ya hay una documento enviado y exitoso , no se puede realizar este proceso");
			}
			console.log(row);
			request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular_NUEVO3.php",row.json);
			request.then(function(response){
				console.log(response);
				$scope.run_report("documentos_manuales_electronicos_generados");
			});
		}
        if(function_name == "resend_service_report"){
			
			let done = $filter('filter')(self.rows,{estado:'Facturado',consecutivo:row.consecutivo})[0];
			
			if(done)
			{
				return alert("Ya hay una documento enviado y exitoso , no se puede realizar este proceso");
			}
			console.log(row);
			
			if(row.consecutivo.includes("FE"))
			{
			
				request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"enviar_factura",factura:row.id}); /*Aqui vamos*/
				request.then(function(response){
				//var string = JSON.stringify(row.desc_proceso);	solo si se nesesita
				 alert('La factura se reenvio');
					console.log(response);
					console.log(row.desc_proceso);
					//console.log(row.desc_proceso);
					//console.log(response.data.factura);
					$scope.run_report("facturacion_electronica_reporte");
				});
			
			}
			
			if(row.consecutivo.includes("NC"))
			{
				request = $http.post("/Control/operativo/controllers/Fact_electronica_angular/Fact_electronica_angular.php",{acc:"enviar_nota_credito",nota_credito:row.id}); /*Aqui vamos*/
				request.then(function(response){
				//var string = JSON.stringify(row.desc_proceso);	solo si se nesesita
				 alert('La factura se reenvio');
					console.log(response);
					console.log(row.desc_proceso);
					//console.log(row.desc_proceso);
					//console.log(response.data.factura);
					$scope.run_report("nota_credito_electronica_reporte");
				});				
			}
		}
		
	}
	
	//END Json custom functions
	
	
	
	$scope.report_column_function = function(functname,row)
	{
		switch(functname) {			
			default:
			break;
		}
	}
	
	
	$scope.Show_all = function()
	{
		console.log(self.ReportTable);
		var total = self.ReportTable.total();
		console.log(total);
	}
	
	
	
	$scope.pasteCopiedRows = function()
	{
		console.log(copyOfRows);
		self.rows = copyOfRows;
		$scope.data_export = copyOfRows;
		self.ReportTable = new NgTableParams({},{ dataset: self.rows});		
		self.ReportTable.reload();
	}
	
	
}]);



function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
