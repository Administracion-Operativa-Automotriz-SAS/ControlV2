<html lang="en">
<head>
  <title>Reporte requisiciones administrativas</title>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
  
  
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>	
  <link href='https://fonts.googleapis.com/css?family=Cookie' rel='stylesheet' type='text/css'>
  <script src="https://use.fontawesome.com/ba7765318c.js"></script>
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js" ></script>
  <script src="https://angular-ui.github.io/bootstrap/ui-bootstrap-tpls-0.11.0.js"></script>
  
  <link rel="stylesheet"; href="https://unpkg.com/ng-table@2.0.2/bundles/ng-table.min.css">
   <script src="https://unpkg.com/ng-table@2.0.2/bundles/ng-table.min.js"></script>
  
  <script type="text/javascript" src="Angular/Libraries/datatable_excel.min.js"></script>
  <script src="Angular/Modules/app.js"></script>
  <script src="Angular/Services/ReportService.js"></script>
  <script src="Angular/Directives/loading.js"></script>
  <script src="Angular/Libraries/FileSaver.js"></script>
  <script src="Angular/Directives/JsonExportExcel.js"></script>
  <script src="Angular/Controllers/ReportController.js"></script>
  		
</head>
<style>
		
	/*table {
	  border-collapse: collapse;
	  border-spacing: 0px;
	}
	td {
	  border: 2px solid black;
	  padding: 0;
	  margin: 0px;
	  overflow: auto;
	}

	div {
	  resize: both;
	  overflow: auto;
	  width: 120px;
	  height: 120px;
	  margin: 0px;
	  padding: 0px;
	  border: 1px solid black;
	  display:block;

	}

	td div {
	  border: 0;
	  width: auto;
	  height: auto;
	  min-height: 20px;
	  min-width: 20px;
	}*/

	table{
	   border-collapse: collapse;
	  border-spacing: 0px;	
	  font-size:10px !important;
	}
	
	
	td {
	  overflow: auto;
	}

	.resize_column {
	  resize: both;
	  overflow: auto;
	  width: 120px;
	  height: 120px;
	  margin: 0px;
	  padding: 0px;
	  border: 1px solid black;
	  display:block;
	}
	
	th
	{
	  resize: both;
	  overflow: auto;
	  width: 120px;	
	  margin: 0px;
	  padding: 0px;
	  border: 1px solid black;
	  	
	}

	td div.resize_column {
	  border: 0;
	  width: auto;
	  height: auto;
	  min-height: 20px;
	  min-width: 20px;
	}

	.loading { border:1px solid #ddd; padding:20px; margin:40px 5px; width:80px;}


	.letrasize{
	font-size:130%;
	}

	.custom-class{
	background-color: gray;
	}

	.default-color{
	background-color: #9AB8B3;
	}

	.another-color{
	background-color: blue
	}
	.clasedate{
	display: flex;
	}

	.elemento5{
		margin: 24px;
	}
</style>
 
<body ng-app="Gapp" >



	<div class="container-fluid" ng-controller="ReportController as Rc">
		<h2>{{table_name}}</h2>
		<div class="clasedate">
		
			<div class="elemento elemento1">
			<label>Fecha Inicial</label>
			<input type="date" name="fecha_inicial" class="form-control" >
			</div>
		     <div class="elemento elemento2">
			<label>Fecha Final</label>
			<input type="date" name="fecha_final" class="form-control" >
		    </div>
		    <!--<div class="elemento elemento3">
			<button class="form-control"  ng-click="report_filter('reporte_info_cartera')" class="">Filtrar fechas</button>
		    </div> -->
		     <div class="elemento elemento4">
			<label>Fecha de corte</label>
			<input type="date" name="fecha_corte"   id="fecha_corte" value="" class="form-control" >
		    </div>
			<div class="elemento elemento5">
			<button class="form-control"  ng-click="report_filter('reporte_info_cartera_corte')" class="">Filtrar corte</button>
		    </div>
		</div>
		<script>
 var fecha = new Date(); //Fecha actual
  var mes = fecha.getMonth()+1; //obteniendo mes
  var dia = fecha.getDate(); //obteniendo dia
  var ano = fecha.getFullYear(); //obteniendo año
  if(dia<10)
    dia='0'+dia; //agrega cero si el menor de 10
  if(mes<10)
    mes='0'+mes //agrega cero si el menor de 10
  document.getElementById('fecha_corte').value=ano+"-"+mes+"-"+dia;
</script>	
		
		<loading></loading>
		
		<div  class="table-responsive">
		<button ng-json-export-excel data="data_export" 
		report-fields="{ID_FACTURA: 'ID_FACTURA',
		ESTADO: 'ESTADO',
		ASEGURADORA:'ASEGURADORA',
		CONSECUTIVO: 'CONSECUTIVO',
		FECHA_EMICION:'FECHA_EMICION',
		FECHA_VENCIMIENTO:'FECHA_VENCIMIENTO', 
		DIAS_VENCIMIENTO:'DIAS_VENCIMIENTO',
		NOMBRE_CLIENTE:'NOMBRE_CLIENTE',
		SUBTOTAL:'SUBTOTAL',
		BASE: 'BASE',
		IVA: 'IVA',
		TOTAL_FACTURA:'TOTAL_FACTURA',		
		TOTAL_NOTA_CREDITO:'TOTAL_NOTA_CREDITO',
		RECAUDO:'RECAUDO',
		SALDO:'SALDO',
		DESCRIPCION: 'DESCRIPCION',
		}"
		
		filename="'Report'" class="btn-sm btn-primary">Export Excel</button>
        <table ng-init="run_report('reporte_info_cartera');   colorClass =  'custom-class' ;" id="datatable"  ng-table-dynamic="Rc.ReportTable with Rc.columns" class="table table-striped table-dark table-responsive" show-filter='true'>
	       <tr ng-repeat="row in $data" ng-init=" $data[$index+1].CONSECUTIVO == row.CONSECUTIVO || $data[$index-1].CONSECUTIVO == row.CONSECUTIVO ?  changeColor = false : changeColor = true ; changeColor || $data[$index-1].CONSECUTIVO != row.CONSECUTIVO ?  $data[$index-1].colorClass == 'custom-class' ?  colorClass = 'default-color'  : colorClass = 'custom-class'  : colorClass = $data[$index-1].colorClass ; row.colorClass = colorClass ; "  >
			  <td class="letrasize" ng-repeat="col in $columns"  ng-class="colorClass">
					
					<div ng-if ="col.Field == 'ver_lineas'">
					<a class="btn btn-success" href='https://pot.aoacolombia.com/verlineasfactura?id={{row.ID_FACTURA}}&consecutivo={{row.CONSECUTIVO}}' target='blank'>Ver lineas de factura</a>
					</div>
					
					<div ng-if="col.Field == 'ver_recibos'">
					  <a class="btn btn-info" href='https://pot.aoacolombia.com/verreciboscaja?id={{row.ID_FACTURA}}&consecutivo={{row.CONSECUTIVO}}' target='blank'>Ver recibos de caja</a>
					  
				    </div>
					
					<div ng-if="col.Field == 'ver_notas_credito'">
					  <a class="btn btn-warning" href='https://pot.aoacolombia.com/vernotascredito?id={{row.ID_FACTURA}}&consecutivo={{row.CONSECUTIVO}}' target='blank'>Ver notas credito</a>
					  
				    </div>
					
					<div class="resize_column">					
						{{::row[col.Field]}}
					</div>
				  
					
			  </td>             
			</tr>             
		  </table>
		
		  <br>
		 <!--<a class="btn btn-success" download="Reporte.xls" href="#" onclick="return ngTableExcelExport.excel(this, 'datatable', 'Reporte');">Export to Excel</a>-->
		</div>
	
	</div>	
</body>

</html>