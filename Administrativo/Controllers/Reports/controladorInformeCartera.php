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
							$sqlRecivosCajas = "SELECT reci_caja.concepto,reci_caja.fecha,reci_caja.valor RECAUDO, reci_caja.consecutivo CONSECUTIVO, ciudad_recivo.nombre CIUDAD_RECIVO,reci_caja.anulado ANULADO
												FROM aoacol_aoacars.factura fac
												INNER JOIN aoacol_aoacars.recibo_caja reci_caja ON fac.id = reci_caja.factura
												INNER JOIN aoacol_aoacars.oficina ciudad_recivo ON reci_caja.oficina = ciudad_recivo.id
												WHERE fac.id = ".$_GET['id']."
												ORDER BY fac.id DESC";
											
							$cosultaRecivosCaja = fetch_objects_test($sqlRecivosCajas);
							
							
							
						    function fetch_objects_test($query){
								$conexion = mysql_connect("app.aoacolombia.com", "aoacol_arturo", "AOA0l1lwpdaa");
                             
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
								  <h4 class="modal-title">Estos son los recivos de caja asociado a la factura <?php echo $_GET['consecutivo']?></h4>
								</div>
								<div class="modal-body">
				<?php 
				               if(mysql_num_rows($cosultaRecivosCaja) >0){
											echo "<script>
													$(document).ready(function(){
														$('#myModal').modal('show');
													});
													</script>";
										echo "Numero de registros ".mysql_num_rows($cosultaRecivosCaja)."<br>";
										}else{
											echo "<script>alert('Por el momento no hay recivos de caja para esta factura ');
											       window.close();</script>";
										}
				
				?>
				
				<table class="table table-striped table-dark table-responsive">
			            <tr>
						<th>Concepto</th>
						<th>Fecha recaudo</th>
						<th>Recaudo</th>
						<th>Ciudad</th>
						<th>Consecutivo</th>
						<th>Anulado</th>
						</tr>
		               <?php 
					   while($row =  mysql_fetch_assoc($cosultaRecivosCaja)){
						   if($row['ANULADO'] !=1){
							$clase = "recivido";
							   echo "<style>
							   .recivido{
								 background: #d7eafb;  
							   } 
							   </style>";
							   $anulado = "RECIBIDO";
						   }else{
							   $clase = "anulacion";
							   echo "<style>
							   .anulacion{
								 background: antiquewhite;  
							   } 
							   </style>";
							   $anulado = "ANULADO";
						   }
						   
						   echo "<tr class='$clase'><td>".$row['concepto']."</td>".
						        "<td>".$row['fecha']."</td>".
								"<td>"."$".number_format($row['RECAUDO'])."</td>".
								"<td>".$row['CIUDAD_RECIVO']."</td>".
								"<td>".$row['CONSECUTIVO']."</td>".
								"<td>".$anulado."</td></tr>";
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