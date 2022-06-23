<html lang="en">
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
</html>
<div class="modal fade" id="myModal" role="dialog">
							<div class="modal-dialog modal-lg">
							<!-- Modal content-->
							<?php
							$sqlLienasFactura = "SELECT confac.nombre NOMBRE_CONCEPTO ,facd.descripcion DESCRIPCION_LINEA,facd.cantidad,facd.unitario VALOR_UNITARIO,facd.total TOTAL  
												FROM factura fac
												INNER JOIN facturad facd ON fac.id = facd.factura
												INNER JOIN concepto_fac confac ON facd.concepto = confac.id
												WHERE fac.id = ".$_GET['id'];
												
							
							$cosultaLienasFactura = fetch_objects_test($sqlLienasFactura);
							
							
							
						    function fetch_objects_test($query){
								$conexion = mysql_connect("app.aoacolombia.com:3306", "aoacol_arturo", "AOA0l1lwpdaa");
                             
							    mysql_select_db("aoacol_aoacars");
								$result = mysql_query($query) or die(mysql_error());
								
								if($result == null){
									return false;
								}
							
								return $result;	
								
								mysql_close($conexion);
							}
							
							 
							?>
							 
							  <div class="modal-content">
								<div class="modal-header">
								  <h4 class="modal-title">Estas son las lineas de la factura <?php echo $_GET['consecutivo']?></h4>
								</div>
								<div class="modal-body">
				<?php 
				               if(mysql_num_rows($cosultaLienasFactura)>0){
											echo "<script>
													$(document).ready(function(){
														$('#myModal').modal('show');
													});
													</script>";
										echo "Numero de registros ".mysql_num_rows($cosultaLienasFactura)."<br>";
										}else{
											
											echo "<script>alert('Hola no existe tal registro, intenta de nuevo.');
											       </script>";
										
										}
				
				?>
				
				<table class="table table-striped table-dark table-responsive">
			            <tr>
						<th>Nombre Concepto</th>
						<th>Descripcion Linea</th>
						<th>Cantidad</th>
						<th>Valor Unitario</th>
						<th>Total</th>
						</tr>
		               <?php 
					   while($row =  mysql_fetch_assoc($cosultaLienasFactura)){
						   echo "<tr><td>".$row['NOMBRE_CONCEPTO']."</td>".
						        "<td>".$row['DESCRIPCION_LINEA']."</td>".
								"<td>".$row['cantidad']."</td>".
								"<td>"."$".number_format($row['VALOR_UNITARIO'])."</td>".
								"<td>"."$".number_format($row['TOTAL'])."</td></tr>";
						}
					   
					   ?>
					   </table>
								</div>
								<div class="modal-footer">
								  <button type="button" onclick="cerrarVentana()" class="btn btn-default" data-dismiss="modal">Cerrar ventana</button>
								</div>
							  </div>
							  <script>
							  function cerrarVentana(){
								  window.close();
							  }
							  </script>
							</div>
				  </div>