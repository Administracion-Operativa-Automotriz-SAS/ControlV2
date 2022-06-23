app.controller('ReportController',['$scope','$timeout','ReportService','$window','$compile','$filter','NgTableParams',
function($scope,$timeout,ReportService,$window,$compile,$filter,NgTableParams){
	
	var self = this;
	
	$scope.data_export = {};
	
	$scope.run_report = function(report_name)
	{
		$scope.loading = true;
		var request = ReportService.get_report({name:report_name});
		request.then(function(response){
			self.columns = [];
			self.rows = response.data.rows;
			var response_columns = response.data.columns;
			
			for(var i = 0 ; i<Object.keys(response_columns).length ; i++)
			{		
				var filter_object = {};
				filter_object[response_columns[i]] = 'text';
				var object_add = {title:response_columns[i],Field:response_columns[i],field:response_columns[i],filter:filter_object,sortable:response_columns[i]};
			
				self.columns.push(object_add);
				
				
			
			}
		
			
			$scope.data_export = response.data.rows;
			
			console.log(self.columns);
			self.ReportTable = new NgTableParams({},{ dataset: self.rows});			
			$scope.table_name = get_table_name(report_name);
				
			if(self.rows.length > 0)
			{
				custom_columns(report_name);
			}
			
			$scope.loading = false;
		
			
			console.log(self.ReportTable);
			
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
			case "reporte_administrativo_requicision":
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
			case "reporte_caja_menor":
			console.log($("input[name='fecha_inicial']").val()+" "+$("input[name='fecha_final']").val());
			$scope.loading = true;
			var request = ReportService.get_report({name:report_name,fecha_inicio:$("input[name='fecha_inicial']").val(),fecha_final:$("input[name='fecha_final']").val(),placa:$("input[name='placa']").val()});
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
			case "reporte_ubicaciones":
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
			case "reporte_requisicion_facturas":
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
			case "reporte_requicision_administrativo":
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
			case "reporte_extras_extencion":
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
			case "reporte_pqrs":
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
			case "reporte_consulta_siniestro":
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
			case "informe_siniestro_modificado":
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
			case "informe_facturacion":
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
			case "informe_encuestas":
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
			case "reporte_info_detalle_factura":
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
			case "reporte_info_cartera_corte":
			var report_name2 = "reporte_info_cartera";
			console.log($("input[name='fecha_corte']").val());
			$scope.loading = true;
			var request = ReportService.get_report({name:report_name2,fecha_corte:$("input[name='fecha_corte']").val(),fecha_inicio:$("input[name='fecha_inicial']").val(),fecha_final:$("input[name='fecha_final']").val()});
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
		switch(report_name) {
			case "reporte_requisiciones_fechas":
			return "Reporte de requisiciones por fechas";
			break;
			default:
			break;
		}
		switch(report_name) {
			case "reporte_administrativo_requicision":
			return "Reporte de requisiciones Administrativo";
			break;
			default:
			break;
		}
		switch(report_name) {
			case "reporte_caja_menor":
			return "Asociaciones a balances de estado con requisiciones OPERATIVAS";
			break;
			default:
			break;
		}
		switch(report_name) {
			case "reporte_ubicaciones":
			return "Reporte ubicaciones";
			break;
			default:
			break;
		}
		switch(report_name) {
			case "reporte_requisicion_facturas":
			return "Asociaciones a balances de estado con requisiciones OPERATIVAS FACTURA";
			break;
			default:
			break;
		}
		
		switch(report_name) {
			case "reporte_requicision_administrativo":
			return "Asociaciones a balances de estado con requisiciones ADMINISTRATIVAS";
			break;
			default:
			break;
		}
		
		switch(report_name) {
			case "reporte_extras_extencion":
			return "Reporte extras con extención";
			break;
			default:
			break;
		}
		switch(report_name) {
			case "reporte_pqrs":
			return "Reporte PQR";
			break;
			default:
			break;
		}
		switch(report_name) {
			case "reporte_consulta_siniestro":
			return "Reporte consulta siniestro";
			break;
			default:
			break;
		}
		switch(report_name) {
			case "informe_siniestro_modificado":
			return "Informe siniestros";
			break;
			default:
			break;
		}
		
		switch(report_name) {
			case "informe_facturacion":
			return "Informe facturacion";
			break;
			default:
			break;
		}
		switch(report_name) {
			case "informe_encuestas":
			return "Informe encuestas";
			break;
			default:
			break;
		}
		switch(report_name) {
			case "reporte_info_cartera":
			return "Informe cartera";
			break;
			default:
			break;
		}
		switch(report_name) {
			case "reporte_info_detalle_factura":
			return "Informe detalle de factura";
			break;
			default:
			break;
		}
		return false;
	}
	
	
	function custom_columns(report_name)
	{		
		return new Promise( (resolve, reject) => {
			switch(report_name) {
				case "reporte_requisiciones_fechas":				
					console.log("exec");
					break;				
				default:
					break;
			}
			switch(report_name) {
				case "reporte_administrativo_requicision":				
					console.log("exec");
					self.columns.push({title:"Ver total requicision",Field:"ver_requisicion"});
					break;				
				default:
					break;
			}
			switch(report_name) {
				case "reporte_caja_menor":				
					console.log("exec");
					self.columns.push({title:"Ver total requicision",Field:"ver_requisicion"});
					break;				
				default:
					break;
			}
			
			switch(report_name) {
				case "reporte_requisicion_facturas":
					console.log("exec");
					self.columns.push({title:"Ver total requicision",Field:"ver_requisicion"});
					break;				
				default:
					break;
			}
			switch(report_name) {
				case "reporte_requicision_administrativo":				
					console.log("exec");
					self.columns.push({title:"Ver total requicision",Field:"ver_requisicion"});
					break;				
				default:
					break;
			}
			switch(report_name) {
				case "informe_siniestro_modificado":
					console.log("exec");
					self.columns.push({title:"Ver total requicision",Field:"ver_requisicion"});
					break;				
				default:
					break;
			}
			switch(report_name) {
				case "informe_facturacion":
					console.log("exec");
					self.columns.push({title:"Ver total requicision",Field:"ver_requisicion"});
					break;				
				default:
					break;
			}
			switch(report_name) {
				case "informe_encuestas":
					console.log("exec");
					self.columns.push({title:"Ver total requicision",Field:"ver_requisicion"});
					break;				
				default:
					break;
			}
			switch(report_name) {
				case "reporte_info_cartera":
					console.log("exec");
					self.columns.push({title:"VER LINEAS FACTURA",Field:"ver_lineas"});
					self.columns.push({title:"VER RECIBOS CAJA",Field:"ver_recibos"});
					self.columns.push({title:"VER NOTAS CREDITO",Field:"ver_notas_credito"});
					break;				
				default:
					break;
			}
			switch(report_name) {
				case "reporte_info_detalle_factura":
					console.log("exec");
					self.columns.push({title:"VER LINEAS FACTURA",Field:"ver_lineas"});
					self.columns.push({title:"VER RECIBOS CAJA",Field:"ver_recibos"});
					self.columns.push({title:"VER NOTAS CREDITO",Field:"ver_notas_credito"});
					break;				
				default:
					break;
			}
			resolve("¡Done!");
		});
		
	}
	
	$scope.report_column_function = function(functname,row)
	{
		switch(functname) {			
			default:
			break;
		}
	}
	
	
	$scope.Show_all =function()
	{
		console.log(self.ReportTable);
		var total = self.ReportTable.total();
		console.log(total);
	}
	
	
}]);