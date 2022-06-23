<?php   
    include('inc/funciones_.php');
		sesion();
		$USER=$_SESSION['User'];
		$NUSUARIO=$_SESSION['Nombre'];
		$BDA='aoacol_administra';
		$NT_req=tu('requisicion','id');

		
		
		
		
    $causacion = $_POST['causacion'];
	
	
	if($causacion == NULL)
	{
	    echo"<script>
		          $('#filterModal1').modal('show');   
		</script>";
	
	echo "  	<div class='alert alert-danger' role='alert'>
			  Debe ingresar la causación para realizar la consulta!
		     	</div> ";




		}else{
	
    $sql = "select * from aoacol_administra.requisiciond where consecutivo_suno = '$causacion'  " ;
    
	  $result = q($sql);
	  
	  $sl = "select requisicion from aoacol_administra.requisiciond where id  = '$result->requisiciond'  " ;
	          
		                 	    $ret = q($sl);
								echo $ret;
	$facturas = array();

	if($result ==null){
		
		
		    
	          $sql = "select 
						aoacol_aoacars.requisiciond_facturas.id as fid,
						aoacol_aoacars.requisiciond_facturas.requisiciond  as fre,
						aoacol_aoacars.requisiciond_facturas.valor_factura  as ff,
						aoacol_administra.requisiciond.requisicion as fr
						 from aoacol_aoacars.requisiciond_facturas
                           INNER JOIN  aoacol_administra.requisiciond  ON aoacol_aoacars.requisiciond_facturas.requisiciond =aoacol_administra.requisiciond.id 
						   where requisiciond_facturas.consecutivo_suno = '$causacion'  " ;
	          
			    $result = q($sql);
	         $facturas = array();
			 
			 
			 	if($result ==null){
			 
			 	    echo"<script>
		          $('#filterModal1').modal('show');   
		</script>";
		
	 	echo "  	<div class='alert alert-danger' role='alert'>
			  La causación $causacion no esta registrada en el sistema
		     	</div> ";
			 	}else {
					
				$Det="
				<table   class='table table-striped'  border cellspacing='0' ><tr> <th>ID </th> <th>Requisición</th> <th>observaciones</th><th>Ver</th> </tr>";

			while($Dt =mysql_fetch_object($result ))
				{
					echo"	<script>
									 $('#filterModal1').modal('show');   
									
								</script>";
								
				
              	$Det.="<tr><td>$Dt->fid</td><td>$Dt->fre</td><td>$Dt->ff</td><td align='right'><a href='http://app.aoacolombia.com/Administrativo/zrequisicion.php?Acc=ver_requisicion&id=$Dt->fr' target='_framename '>Ver </a></td></tr>";

												
				}
				$Det.="</table>";
				echo $Det;
				
	}
			 
			 
			 
		
		
	}else {

	
				$Det="
				<table   class='table table-striped'  border cellspacing='0' ><tr> <th>ID </th> <th>Requisición</th> <th>observaciones</th><th>Ver</th> </tr>";
				while($Dt =mysql_fetch_object($result ))
				{
					echo"	<script>
									 $('#filterModal1').modal('show');   
									
								</script>";
										$Det.="<tr><td>$Dt->id</td><td>$Dt->requisicion</td><td>$Dt->observaciones</td><td align='right'><a href='http://app.aoacolombia.com/Administrativo/zrequisicion.php?Acc=ver_requisicion&id=$Dt->requisicion' target='_framename '>Ver </a></td></tr>";
			
				}
				$Det.="</table>";
				echo $Det;
				
	}
	
	}
	
	

   
?>