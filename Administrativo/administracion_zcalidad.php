<?PHP
	// programa para presentar la documentación de calidad
	
	include('inc/funciones_.php');
	sesion();
	html('SISTEMA DE GESTION DE CALIDAD');
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);

	if($_GET)
	{
	
		
		if($_GET['acc']=='descargar_documento')
		{
			//echo "descargar documento";
			//exit;
			$id = $_GET['id'];
			$resultado=q("Select * from q_documento where id = '$id' limit 1");
			$documento = mysql_fetch_object($resultado);
			//print_r($documento);
			$path ='/var/www/html/public_html/Administrativo';
			//echo $path.'/'.$documento->archivo_f;
			
			
			//echo strlen($fichero);
			//exit;
			if(strlen($documento->archivo_f)<2){
				echo"<a href='administracion_zcalidad.php' class='btn'>Regresar</a>";
				echo "<br>";
				echo "No hay archivo para descarga";
				
				exit;
			}
			
			if (file_exists($path.'/'.$documento->archivo_f)) {
				echo "Archivo existe";
				
				header("Content-type:application/pdf");
				//header('Content-Disposition: attachment; filename="'.basename($fichero).'"');				
				readfile($path.'/'.$documento->archivo_f);
				//exit;
			}
			
			//exit;
		}
		
		if($_GET['acc']=='eliminar_documento')
		{
			if($_SESSION['User']!=1 and $_SESSION['User']!=12){
				echo "No tiene permiso";
				exit;
			}
			//echo "eliminar documento";
			//exit;
			$id = $_GET['id'];
			$resultado=q("update q_documento set estado = 0 where id = '$id'");
			echo "Documento eliminado";
			header('Location: administracion_zcalidad.php');
		}
	}
	
	if($_POST)
	{
		if($_SESSION['User']!=1 and $_SESSION['User']!=12 ){
			echo "No tiene permiso tu eres sesion ".$_SESSION['User'];
			exit;
		}
		
		
		
		if($_POST['acc'] == 'crear_documento')
		{
			$result=q("select * from q_documento where codigo= '".$_POST['codigo']."' LIMIT 1");
			$n = mysql_num_rows($result);
			if($n>0)
			{
				echo "Ya hay un documento con el codigo solicitado ";
				exit;
			}
			$resultado=q("Select max(id) as max from q_documento ");
			$documento = mysql_fetch_object($resultado);
			$id = ($documento->max+1);
			
			uploading_file($id);
			echo "<br>";
			
			$nombre = $_POST['nombre'];
			$codigo = $_POST['codigo'];
			$proceso = $_POST['tipo_proceso'];
			$accion = $_POST['accion'];
			
			
			$camino = $custompath .'/'. basename($_FILES['archivo']['name']);
			$date = date("Y-m-d");	
			$resultado=q("insert into  q_documento  (id,proceso,nombre,archivo_f,codigo,accion,fecha_version) values ($id,$proceso,'$nombre','$camino','$codigo','$accion','$date') ");
			echo "documento creado";
			header('Location: administracion_zcalidad.php');
			
		}
		if($_POST['acc'] == 'editar_documento')
		{
			$id = $_POST['id'];
			$camino = uploading_file($id);
			if($camino != "")
			{				
				$file_edit = ", archivo_f = '$camino'";
			}
			else{
				$file_edit = ""; 
			}			
		
			echo "<br>";
			
			$nombre = $_POST['nombre'];
			$codigo = $_POST['codigo'];
			$proceso = $_POST['tipo_proceso'];			
			
			
			
			if($_SESSION['User']==1)
			{
				$accion = $_POST['accion'];
				$accion_query = ", accion = '$accion' ";
			}
			else
			{
				$accion_query = "";
			}
			
			$edit_query = "update q_documento set nombre = '$nombre' , codigo = '$codigo' , proceso = '$proceso' ".$file_edit." ".$accion_query."  where id = '$id' ";
			
			//echo $edit_query;
			q($edit_query);
			echo "editado";
			//exit;
			header('Location: administracion_zcalidad.php');
		}
		if($_POST['acc'] == 'actualizar_version')
		{
			$sql = "Select * from q_documento where id = ".$_POST["id"];
			$result = q($sql);
			$documento = mysql_fetch_object($result);
			$date = date("Y-m-d");
			$version = $documento->version + 1;
			$rutadoc = uploading_file($documento->id."/V".$version);	
			
			if($version < 10)
			{
				$version = "0".$version;
			}
			
			$sql = "update q_documento set version = '$version' , archivo_f = '$rutadoc', fecha_version = '$date' where id = '$documento->id' ";
			q($sql);
			$sql = "Insert into q_documento_versiones (id_q_documento,version,ruta,fecha) VALUES ('$documento->id','$version','$documento->archivo_f','$date') ";
			q($sql);
			echo "<br>";
			echo "Version Actualizada";
			header('Location: administracion_zcalidad.php');
		}
		
	}
	
	function uploading_file($id)
	{
		if(basename($_FILES['archivo']['name']) != null)
		{
				$allowed =  array('pdf','PDF');
				$filename = $_FILES['archivo']['name'];
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				if(!in_array($ext,$allowed) ) {
					echo 'error tipo de archivo no permitido';
					exit;
				}
				
				$path ='/var/www/html/public_html/Administrativo';	
			
				$custompath = 'q_documento/000/'.$id;
				$path  = $path."/".$custompath;
				
				if(!is_dir($path))
				{
					mkdir($path, 0777, true);
				}
		
				$uploadfile = $path .'/'. basename($_FILES['archivo']['name']);
				
				
				echo $uploadfile;
				
				echo "<br>";
						
				if (move_uploaded_file($_FILES['archivo']['tmp_name'], $uploadfile)) {
				  echo "File is valid, and was successfully uploaded.";
				  
				} else {
				   echo "Upload failed";
				   exit;
				}
				
				$camino = $custompath .'/'. basename($_FILES['archivo']['name']);
				return $camino;
		}
		else
		{
			return "";
		}		
	}


	$resultado=q("select * from q_proceso where estado = 1 order by orden");	
	$procesos = array();
   	while ($proceso = mysql_fetch_object($resultado)) {
		array_push($procesos, $proceso);
	}
	
?>
<html>
    <meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js" ></script>
	<script src="https://unpkg.com/ng-table/bundles/ng-table.js" ></script>
	<script src="Angular/Modules/app.js"></script>
	<script src="Angular/Directives/contenteditable.js"></script>
	<script src="Angular/Services/SystemService.js"></script>
	<script src="Angular/Controllers/SystemController.js"></script>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	
	<body ng-app="Gapp">
	<div class="container">
		<div ng-controller="SystemController">
			<a href="administracion_zcalidad.php"><h3>Interfaz de administración de formatos en calidad</h3></a>
			<?php if($_SESSION['User']==1 or $_SESSION['User']==12): ?>
			<button onclick="nuevo_documento()" class="btn btn-success form-control">Crear Nuevo Documento</button>
			<?php endif; ?>
			<br>
			<a href="documentos_guardados.php" target="_blank" class="btn btn-warning form-control"><span style="color:white; font-size:14px;">Documentos guardados</span></a>
			<br>
			<?php if($_SESSION['User']==1 or $_SESSION['User']==12): ?>
			<div >
				
				
					<button ng-click="start()" class="btn btn-primary form-control">Parametros de categorias</button>
					
					<!-- Modal -->
					<div class="modal fade" id="AngularModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">Categorias</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						  </div>
						  <div class="modal-body">
							<table class="table">
								<thead>
									<tr>
										<th ng-repeat="cabecera in categorias.table_headers">{{cabecera.title}}</th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="body in categorias.table_body">
										<td>{{body.id}}</td>
										<td ng-model="body.nombre" contenteditable ="true">{{body.nombre}}</td>
										<td ng-model="body.orden" ng-keypress="number_validation(detalles)" contenteditable ="true">{{body.orden}}</td>
										<td>
											<button title="Editar" ng-click="edit(body,categorias)"><i class="fas fa-edit"></i></button><button title="Eliminar" ng-click="delete(body,categorias)"><i class="fas fa-trash"></i></button>
										</td>
									</tr>
									<tr>
										<td></td>
										<td ng-model="categoria.nombre" contenteditable ="true"></td>
										<td><button ng-click="create(categoria,categorias)">Agregar</button></td>
									</tr>
								</tbody>
							</table>
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>						
						  </div>
						</div>
					  </div>
					</div>
					
			
				
			</div>
			<form action="zgendoc_funciones.php" method="POST" target="_blank">
				<input type='hidden' name='Acc' value='test_document'> 
				<button  class="btn btn-danger form-control"><span style="color:white; font-size:14px;">Testear Documento</span></button>
			</form>
			
			<?php endif; ?>
			<br>		
			<table class='table table-hover table-bordered' ng-controller="SystemController">
				<tr>
					<th>id</th>
					<th>Nombre del proceso</th>
					<th>Documentos</th>
				</tr>
				<?php foreach($procesos as $proceso){ ?>
					<tr>
						<td><?php echo $proceso->id ?></td>
						<td><?php echo utf8_decode($proceso->nombre) ?></td>
						<td>
							<?php							
								$foid = $proceso->id;
								$query = "select * from aoacol_administra.q_registro where proceso = ".$foid;
								//echo $query;
								
								$resultado=q("select * from q_documento where proceso =  $foid and estado = 1 order by codigo ");	
								//$varTest = "select * from q_documento where proceso =  $foid and estado = 1 order by codigo";
								
								$documentos = array();
								while ($documento = mysql_fetch_object($resultado)) {
									array_push($documentos, $documento);
								}
								//print_r($documentos);
								//print_r($registros);							
							?>
							<table class="table">
								<thead>
								<tr>
									<th width="50%">Nombre</th>
									<th width="25%">Codigo</th>
									<th>Versión</th>
									<th>Opciones</th>
									<th>Gestion Doc</th>
								</tr>
								</thead>
								<tbody>								
									<?php foreach($documentos as $documento){ ?>
										<tr>
											<td width="50%"><?php echo $documento->nombre ?></td>
											<td width="25%"><?php echo $documento->codigo ?></td>
											<td><?php echo $documento->version ?></td>
											<td>
												<a style="width:50px !important;" href="administracion_zcalidad.php?id=<?php echo $documento->id ?>&acc=descargar_documento" class="btn btn-xs btn-success">Ver</a>											
												<?php if($_SESSION['User']==1 or $_SESSION['User']==12): ?>
												<br>
												<button style="width:50px !important; margin-top:5px;" class=" btn btn-xs btn-warning" onclick="editar_documento('<?php echo $documento->id ?>','<?php echo $documento->proceso ?>','<?php echo $documento->nombre ?>','<?php echo $documento->codigo ?>','<?php echo $documento->accion ?>')">Editar</button>
												<br>
												<a style="width:50px !important;  margin-top:5px;" href="administracion_zcalidad.php?id=<?php echo $documento->id ?>&acc=eliminar_documento" class="btn btn-xs btn-danger" onclick="if (! confirm('¿Continuar?')) { return false; }">Eliminar</a>
												<?php endif; ?>
											</td>
											<td>
												<?php if($_SESSION['User']==1 or $_SESSION['User']==12): ?>
													<button ng-click="check_ver_ant(<?php echo $documento->id ?>)" class="btn btn-xs btn-warning">Ver. Anteriores del<br> documento</button>
												<?php endif; ?>
												<?if(strlen($documento->accion)>3): ?>
												<form action="zgendoc_funciones.php" method="POST" target="_blank">
													<input type='hidden' name='Acc' value='<?php echo $documento->accion  ?>'>
													<input type='hidden' name='sigla' value='<?php echo $documento->codigo  ?>'>
													<input type='hidden' name='docid' value='<?php echo $documento->id  ?>'> 
													<button style="margin-top:20px;" class="btn btn-xs btn-primary">Generar</button>
												</form>
												<?php endif ?>
											</td>
										</tr>
									<?php } ?>								
								</tbody>
							</table>
						</td>
					</tr>
				<?php } ?>
			</table>
		
				<!-- Modal -->
		<div id="myModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<form action="administracion_zcalidad.php" enctype="multipart/form-data" method="post">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Editar documento</h4>
				  </div>		  
				  <div class="modal-body">			
						<input type="hidden" name="id" id="eid"> 
						<div class="form-group">  
							<label class="form-control">Nombre</label>
							<input class="form-control" id="enombre" name="nombre" required></input>
						</div>
						<div class="form-group">
							<label class="form-control">Codigo</label>
							<input class="form-control" id="ecodigo" name="codigo" required></input>
						</div>
						<div class="form-group">
							<label class="form-control">Proceso relacionado</label>
							<select class="form-control" name="tipo_proceso" id="cproceso" required>
								<option>Selecciona</option>
								<?php foreach($procesos as $proceso){ ?>
									<option value="<?php echo $proceso->id ?>" > <?php echo utf8_decode($proceso->nombre) ?> </option>
								<?php } ?>
								<option value="10">Papelera</option>
							</select>
						</div>
						<div class="form-group">
							<label class="form-control">Archivo</label>
							<input class="form-control" type="file"  name="archivo"></input>
						</div>
						<?php if($_SESSION['User']==1): ?>
						<div class="form-group">
							<label class="form-control">Accion</label>
							<input class="form-control" id="eaccion" name="accion" <?php if($_SESSION['User']!=1): ?> disabled <?php endif ?>></input>
						</div>
						<?php endif ?>
						<input type="hidden" name="acc" value="editar_documento"> 
						
					
				  </div>
				  <div class="modal-footer">
					<button type="submit" class="btn-warning btn">Editar</button>
					
					<button type="button"  class="btn btn-success" onclick="new_doc_ver()">Subir una versión del documento</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  </div>
				</form>
			</div>
		  </div>
		</div>
		
		<input type="hidden" id="filter_index" value="1">
		
				<!-- Modal -->
		<div id="myModal4" class="modal fade" role="dialog">
		  <div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
			   <div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Versiones Anteriores</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			  </div>
			  <div class="modal-body">			
						
					
					{{current_filter_index}}
						<table class="table">
							<thead>
								<tr>
									<th ng-repeat="cabecera in documento_versiones.table_headers">{{cabecera.title}}</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="body in filtered_data(documento_versiones.table_body,'id_q_documento') " >
									<td>{{body.id}}</td>
									<td >{{body.version}}</td>
									<td >{{body.fecha}}</td>
									<td>									
										<a ng-show="body.ruta.length > 3"  class="btn btn-success btn-xs" href="{{body.ruta}}" target="_blank">VER</a>
									</td>								
								</tr>							
							</tbody>
						</table>
					
				
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>			
			  </div>
			</div>
		  </div>
		</div>
		
		<script>
		
				
			
			function new_doc_ver()
			{
				$("#vid").val($("#eid").val());
				$("#myModal3").modal("show");
				
			}
			
			function are_you_sure()
			{
				
				if(confirm("¿Esta seguro?"))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			
		</script>
		
						<!-- Modal -->
		<div id="myModal3" class="modal fade" role="dialog">
		  <div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
			<form action="administracion_zcalidad.php" onsubmit="return are_you_sure()" enctype="multipart/form-data" method="post">
				 <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Cambiar de versión</h4>
				 </div>
				  <div class="modal-body">				
						<input type="hidden" name="id" id="vid">
						<div class="form-group">
							<label class="form-control">Seleccione el nuevo archivo para cambio de versión</label>
							<input class="form-control" type="file"  name="archivo" required></input>
						</div>			
						<input type="hidden" name="acc" value="actualizar_version">		
					
				  </div>
				  <div class="modal-footer">
					<button type="submit" class="btn btn-primary">Actualizar</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  </div>
				</div>
			</form>
		  </div>
		</div>
		
					<!-- Modal -->
		<div id="myModal2" class="modal fade" role="dialog">
		  <div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Crear documento</h4>
			  </div>
			  <div class="modal-body">
				<form action="administracion_zcalidad.php" enctype="multipart/form-data" method="post">
					<div class="form-group">  
						<label class="form-control">Nombre</label>
						<input class="form-control"  name="nombre" required></input>
					</div>
					<div class="form-group">
						<label class="form-control">Codigo</label>
						<input class="form-control"  name="codigo" required></input>
					</div>
					<div class="form-group">
						<label class="form-control">Proceso relacionado</label>
						<select class="form-control" name="tipo_proceso" required>
							<option>Selecciona</option>
							<?php foreach($procesos as $proceso){ ?>
								<option value="<?php echo $proceso->id ?>" > <?php echo utf8_decode($proceso->nombre) ?> </option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group">
						<label class="form-control">Archivo</label>
						<input class="form-control" type="file"  name="archivo"></input>
					</div>
					
					<div class="form-group">
						<label class="form-control">Accion</label>
						<input class="form-control"  name="accion" ></input>
					</div>
					
					<input type="hidden" name="acc" value="crear_documento"> 
					<button type="submit" class="btn-warning btn form-control">Crear</button>
				</form>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			  </div>
			</div>

		  </div>
		</div>
	</div>
</body>
	

	

	<script>
	 function editar_documento(id,proceso,nombre,codigo,accion)
	 {
		$("#enombre").val(nombre);
		$("#ecodigo").val(codigo);
		$("#eaccion").val(accion);
		$("#eid").val(id);
		$("#cproceso").val(proceso);
		$("#myModal").modal('show');
	 }
	 
	 function nuevo_documento()
	 {
		$("#myModal2").modal('show');
	 }
	 

	 
	</script>
</html>