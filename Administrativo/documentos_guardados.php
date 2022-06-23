<?PHP
	// programa para presentar la documentación de calidad
	
	include('inc/funciones_.php');
	sesion();
	html('SISTEMA DE GESTION DE CALIDAD');
	if($_POST)
	{
		$query = "select d.id as id, d.alias as alias, q.codigo as codigo, d.usuario as usuario , d.consecutivo as consecutivo, p.nombre as proceso, q.nombre as nombre, d.fecha as fecha
		from documentos_guardados as d inner join q_documento as q on d.documento = q.id inner join q_proceso as p on q.proceso = p.id
		where usuario = '".$_POST['usuario']."'";
		$name = $_POST['usuario'];
	}
	else
	{
		$query = "select d.id as id, d.alias as alias, q.codigo as codigo, d.usuario as usuario , d.consecutivo as consecutivo, p.nombre as proceso, q.nombre as nombre, d.fecha as fecha
		from documentos_guardados as d inner join q_documento as q on d.documento = q.id inner join q_proceso as p on q.proceso = p.id
		where usuario = '".$_SESSION['Nombre']."'";	
		$name = $_SESSION['Nombre'];
	}
	//echo $query;
	$resultado=q($query);	
	$documentos = array();
   	while ($documento = mysql_fetch_object($resultado)) {
		array_push($documentos, $documento);
	}	
	//print_r($documentos);
	
?>
<html>
	<meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.min.js"></script> 
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	
	<body>
		<br>
		<h3>Documentos Guardados</h3>
		<div class="container">
			<?php if($_SESSION['User']==1 or $_SESSION['User']==12): ?>
				<button class="btn" onclick="documents_by_person()">Buscar documentos por persona</button>
			<?php endif ?>
		<br>
		<span style="font-weight:bold;">Usuario: <?php echo $name ?></span>
		<br>
		<table id="sample" class='table table-hover table-bordered'>
			<thead>
				<tr>
					<th>id</th>					
					<th>Nombre del proceso</th>
					<th>Nombre del documento</th>
					<th>Alias</th>
					<th>Consecutivo</th>
					<th>Fecha</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($documentos as $documento){ ?>
				<tr>
					<td><?php echo $documento->id ?></td>
					<td><?php echo $documento->proceso ?></td>
					<td><?php echo $documento->nombre ?></td>
					<td><span id="aliastext<?php echo $documento->id ?>"><?php echo $documento->alias ?><span></td>
					<td><?php echo $documento->codigo."-".$documento->consecutivo ?></td>
					<td><?php echo $documento->fecha ?></td>
					<td>
						<div style="text-align:center;">
							<form method="post" id="save_document" action="Controllers/gestion_documental_helper.php?Acc=load_save_document">
								<input type="hidden" name="id" value="<?php echo $documento->id ?>">	
								<button style="width:75px !important; margin-top:5px;" class="btn btn-xs btn-success">Editar</button>
							</form>
							<button onclick="create_alias(<?php echo $documento->id ?>)" style="width:75px !important; margin-top:5px;" class="btn btn-xs btn-primary">Crear<br>Alias</button>
							<button onclick="look_for_printed(<?php echo $documento->id ?>)" style="width:75px !important; margin-top:5px;" class="btn btn-xs btn-danger">Ver<br>impresiones</button>
						</div>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>			
		</div>
		
		<!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Impresiones realizadas al documento</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body">
					<div id="ajax-content"></div>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>			
				  </div>
				</div>
			</div>
		</div>
		
		<!-- Modal2 -->
		<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Impresiones de los usuarios</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body">
					<div id="ajax-content2"></div>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>			
				  </div>
				</div>
			</div>
		</div>
		
		<!-- Modal3 -->
		<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Crear Alias</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body">
					<input type="hidden" name="alid">
					<div class="form-group">						
						<label class="form-control">Alias del documento</label>
						<input name='name_alias' maxlength="150" class="form-control" type="text">					
					</div>
				  </div>
				  <div class="modal-footer">
					<button type="button" onclick="changue_alias()" class="btn btn-success" data-dismiss="modal">Guardar</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>			
				  </div>
				</div>
			</div>
		</div>
		
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" />
		<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	
	</body>
	<script>
		$( document ).ready(function(){
			$('#sample').DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Spanish.json"},"pageLength": 50});
			
		});	
			
		function look_for_printed(id)
		{
			$.post( "Controllers/gestion_documental_helper.php?Acc=look_prints_document",
			{foid:id},function(data){
				$("#ajax-content").html(data);
				$("#myModal").modal('show');
			});
		}
		
		function create_alias(id)
		{
			$("input[name='alid']").val(id);	
			$("#myModal3").modal('show');
		}
		
		function changue_alias()
		{
			var alias = $("input[name='name_alias']").val();
			var alid = $("input[name='alid']").val();
			$.post( "Controllers/gestion_documental_helper.php?Acc=changue_alias",
			{name_alias:alias,id:alid},function(data){
				$("#aliastext"+alid).html(alias);
			});			
		}
		
		<?php if($_SESSION['User']==1 or $_SESSION['User']==12): ?>
			function documents_by_person(id)
			{
				$.post( "Controllers/gestion_documental_helper.php?Acc=look_all_people_docs",
				{},function(data){
					$("#ajax-content2").html(data);
					$("#myModal2").modal('show');
				});
			}
		<?php endif ?>
	</script>
</html>