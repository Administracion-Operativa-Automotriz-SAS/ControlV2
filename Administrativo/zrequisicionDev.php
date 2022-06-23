<?php

include('inc/funciones_.php');
sesion();
$USER=$_SESSION['User'];
$NUSUARIO=$_SESSION['Nombre'];
$email_usu=$_SESSION['Email_usuario'];
$BDA='aoacol_administra';
$NT_req=tu('requisicion','id');
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');     die();}

 
function ver_requisicion()


{
	
		global $id,$BDA,$Aviso;
	$ER=qo("select requisicion.placa, 
				 concat( oficina.centro_operacion,' ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto,aseguradora.nombre as ASEGURADORA,ubicacion.flota,requisiciond.centro_operacion
				 from aoacol_administra.requisiciond 
				 LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
				 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 inner JOIN aoacol_aoacars.oficina on ubicacion.oficina = oficina.id
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion.id = $id");
	$D=qo("select * from $BDA.requisicion where id=$id"); // trae la informaci?n de la requisicion
	$Ciu=qo1("select t_ciudad('$D->ciudad')"); // trae la informaci?n de la ciudad
	
	$Pr=qo("select * from $BDA.perfil_requisicion where id=$D->perfil"); // trae la informaci?n del perfil que aprueba la requisici?n
	$Email_usuario=usuario('email'); // obtiene el email del usuario
	if(!$Email_usuario) {
	echo "<body><script language='javascript'>alert('SU SESION EN ESTE SISTEMA ESTA CAIDA, NO SE PUEDE ENVIAR EL CORREO DE SOLICITUD DE AUTORIZACION');</script></body>"; die();}
	$Hoy=date('Y-m-d H:i:s');
	$Detalle=qo("select requisiciond.requisicion,provee_produc_serv.nombre as item,tipo.nombre as tipo, unidad_de_medida.nombre as unidad_medida,
				 requisiciond.observaciones,requisiciond.cantidad,requisiciond.valor as valor_unitario, requisiciond.valor_total,requisicion.placa, 
				 concat( oficina.centro_operacion,' ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto, requisicion.fecha 
				 from aoacol_administra.requisiciond 
				 LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
				 LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id 
				 LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id 
				 LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id 
				 LEFT OUTER JOIN aoacol_administra.requisicionc on requisiciond.clase = requisicionc.id 
				 LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
				 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 inner JOIN aoacol_aoacars.oficina on ubicacion.oficina = oficina.id
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion =$id");
	if($Pr->contingencia){
		    

		$Email_aprobador=$Pr->email_aprobacion2;$Nombre_aprobador=$Pr->aprobado_por2;
		
		}elseif($ER->centrocosto == 411  || $ER->flota == 23  ||  $ER->centro_operacion == 20){
		$Email_aprobador = 'gabriel.sandoval@transorientesas.com';
	    $Nombre_aprobador = 'Gabriel Sandoval';
		}else{
			$varValidation =  $Detalle;
		 
		 if($varValidation->item == 'TRANSPORTE OPERATIVO' || 
			$varValidation->item == 'TRANSPORTES' || 
			$varValidation->item == 'PEAJE' || 
			$varValidation->item == 'LAVADO DE VEH?CULOS' ||
			$varValidation->item == 'RESTAURANTE' ||  
			$varValidation->item == 'TANQUEO DE COMBUSTIBLE' || 
			$varValidation->item == 'RECARGA EXTINTORES' || 
			$varValidation->item == 'BOTIQU?N VEH?CULOS'){
							$Email_aprobador = "adquisiciones@aoasoluciones.com"; 
							$Nombre_aprobador="Aquisiciones";
						}else{
							$Email_aprobador=$Pr->email_aprobacion;
		                    $Nombre_aprobador=$Pr->aprobado_por;
						}
	    
		} // perfil estandar de aprobaci?n
	// construye una ruta de correo para la aprobacion por el funcionario adecuado
      
	
	 $Ruta_correo="utilidades/Operativo/operativo.php?id=$id&user=".$_SESSION['Nick']."&email=$Email_usuario&Fecha=$Hoy&Usuario=$Nombre_aprobador&eUsuario=$Email_aprobador&Solicitado_por=".$_SESSION['Nombre']."&eSolicitado_por=$Email_usuario";
        
		$Cotizaciones='';
        if($D->cotizacion_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 1 </u></a><br>";
        if($D->cotizacion2_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion2_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 2 </u></a><br>";
        if($D->cotizacion3_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion3_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 3 </u></a><br>";
        if(!$Cotizaciones) $Cotizaciones="No hay imagenes cargadas";
      
   	    $Ruta_aprobacion=base64_encode("\$Programa='$Ruta_correo&Acc=rol_aprobar_requisicion&observaciones='.\$observaciones.'&cotapr='.\$cotapr;\$Fecha_control=date('Y-m-d');");
        $Ruta_daprobacion=base64_encode("\$Programa='$Ruta_correo&Acc=rol_daprobar_requisicion&observaciones='.\$observaciones;\$Fecha_control=date('Y-m-d');");
        $Ruta_Anular=base64_encode("\$Programa='$Ruta_correo&Acc=rol_anular_requisicion&observaciones='.\$observaciones;\$Fecha_control=date('Y-m-d');");
        $Ruta_aprobacion_sincorreo_pro=base64_encode("\$Programa='$Ruta_correo&Acc=rol_aprobar_sin_proveedor&observaciones='.\$observaciones.'&cotapr='.\$cotapr;\$Fecha_control=date('Y-m-d');");

	  $Fecha_control=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),30)));
        
		
	// incluye el detalle de la requisicion
	$Det="<table class='table' border cellspacing='0'><tr><th>Tipo de Requisici?n</th><th>Item</th><th>Unidad de medida</th><th>Descripcion</th><th>Centro de operacion</th><th>Cantidad</th><th>Valor unitario</th><th>Valor</th>";
	$Detalle=q("select requisiciond.requisicion,provee_produc_serv.nombre as item,tipo.nombre as tipo, unidad_de_medida.nombre as unidad_medida,
				 requisiciond.observaciones,requisiciond.cantidad,requisiciond.valor as valor_unitario, requisiciond.valor_total,requisicion.placa, 
				 concat( oficina.centro_operacion,' ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto, requisicion.fecha 
				 from aoacol_administra.requisiciond 
				 LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
				 LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id 
				 LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id 
				 LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id 
				 LEFT OUTER JOIN aoacol_administra.requisicionc on requisiciond.clase = requisicionc.id 
				 LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
				 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 inner JOIN aoacol_aoacars.oficina on ubicacion.oficina = oficina.id
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion =$id");
	while($Dt =mysql_fetch_object($Detalle ))
	{
		$Det.="<tr><td>$Dt->tipo</td><td>$Dt->item</td><td>$Dt->unidad_medida</td><td>$Dt->observaciones</td><td>$Dt->centrodeoperacion</td><td>$Dt->cantidad</td><td align='right'>$".coma_format($Dt->valor_unitario)."</td><td align='right'>$".coma_format($Dt->valor_total)."</td></tr>";
	}
	$Det.="</table>";
	
		
					$verificar=qo("select * from aoacol_administra.requisicion where id = $id");

								if($verificar->ubicacion == 0){
									 if($verificar->estado==5 ) {
										 
										 if($D->perfil == 3){
											 
											 include('anuladoPdfAdmOriente.php');	
												}else{
											
											include('anularPdfAdm.php');
												
											}
												
									
										
											}else {

										if($verificar->estado==2){
											
											
												 if($D->perfil == 3){
											include('pdfrequisicionAdmOriente.php'); 
												}else{
											
										    include('pdfrequisicionAdm.php');;
												
											}
										
										
											}
											if($verificar->estado==4){
												
												 if($D->perfil == 3){
											include('pdfrequisicionAdmOriente.php'); 
												}else{
											
								       	include('pdfrequisicionAdm.php');
												
											}	
												
										
										
											}
											}
											
								}else{
									 if($verificar->estado==5 ) {
									
									      if($D->perfil == 3){
										
											include('anularPdfOpeOriente.php');
												}else{
											
								       include('anularPdfOpe.php');
												
											}
									
									
										
									}else{

										if($verificar->estado==2){	
										
										     if($D->perfil == 3){
										
											
										  include('pdfrequisicionOpeOriente.php');
												}else{
											
								      include('pdfrequisicionOpe.php');
												
											}
									
									
										
									    }
										if($verificar->estado==3){	
										
										
										   if($D->perfil == 3){
										
											
										  include('pdfrequisicionOpeOriente.php');
												}else{
											
								     include('pdfrequisicionOpe.php');
												
											}
										
										
										
									    }
										if($verificar->estado==4){
											
											
											
										   if($D->perfil == 3){
										
											
									include('pdfrequisicionOpeOriente.php');
												}else{
											
								     include('pdfrequisicionOpe.php');
												
											}
										
										
											}
									
										}
								}		
	
	$Res="<table border cellspacing='4'><tr><th>Resultado</th>";
        //echo "select *,t_requisiciont(tipo) as ntipo, t_requisicionc(clase) as nclase from requisiciond where requisicion=$id";
		$retorno=q("select requisiciond.requisicion,requisiciond.valor_total,
		            sum(requisiciond.valor_total) as resultado 
					from aoacol_administra.requisiciond
					where requisicion  =$id");
        while($Dt =mysql_fetch_object($retorno))
        {
           $Res.="<tr><td>$".coma_format($Dt->resultado)."</td>";
        }
        $Res.="</table>";
		$DV=qo("select * from aoacol_aoacars.vehiculo where placa='$D->placa'");
		$HU=qo("select ubicacion from requisicion where id = $id");
		$HT=qo("select * from aoacol_aoacars.ubicacion where id='$HU->ubicacion'");
		$li=qo("select * from aoacol_aoacars.linea_vehiculo where id='$DV->linea'");
		$Mr=qo("select * from aoacol_aoacars.marca_vehiculo where id = '$li->marca'");
		$Prov=qo("select * from $BDA.proveedor where id=$D->proveedor");	
	
	//"$Email_aprobador,$Nombre_aprobador" /*para */,    "arturoquintero@aoacolombia.com,ARTURO QUINTERO",  
	$Ruta_alterna=base64_encode("header('location:../Control/operativo/zbalance_estado.php?Acc=aprobacion_requisicion&id=$id');");
	// envia el correo al funcionario que debe aprobar esa requisicion

			$formAprobar="
			
			
							"; 
							  if($D->estado==0) { echo"
										
											<div style='  margin-bottom: 5%; text-align : center;  '>
															 <h4>Cambiar estado de </h4>
					                       	<form  style='  margin-bottom: 2%; text-align : center;  '  action='https://app.aoacolombia.com/i.php' target='_blank' method='POST' name='forma' id='forma'>
												Selecione un estado: 
												<select style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'  style=' text-align:  center; '  name='i'>
										<option  value=\"$Ruta_aprobacion\">Aprobar</option>
										<option  value=\"$Ruta_aprobacion_sincorreo_pro\">Aprobar sin enviar al proveedor</option>
												</select><br>
								Observaciones: 
								<input type='text' style=' border: 1px solid rgba(118,136,29,1);'  name='observaciones' id='observaciones' value='' size='50' maxlength='200'>
								<br>
								<br>
								<input type='submit'   style=' color:#fff; background-color:rgba(118,136,29,1);  text-align: center;' value=' PROCEDER ' >
								<input type='hidden' name='Fecha_control' value='$Fecha_control'>
								<br></form>
									
								
											
											 </div>  ";}

							        if($D->estado==1) { echo"
										
											<div style='  margin-bottom: 5%; text-align : center;  '>
															 <h4>Cambiar estado de </h4>
					                       	<form  style='  margin-bottom: 2%; text-align : center;  '  action='https://app.aoacolombia.com/i.php' target='_blank' method='POST' name='forma' id='forma'>
												Selecione un estado: 
												<select style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'  style=' text-align:  center; '  name='i'>
										<option  value=\"$Ruta_aprobacion\">Aprobar</option>
																				<option  value=\"$Ruta_aprobacion_sincorreo_pro\">Aprobar sin enviar al proveedor</option>

										<option value=\"$Ruta_Anular\">Rechazar</option>
												</select><br>
								Observaciones: 
								<input type='text' style=' border: 1px solid rgba(118,136,29,1);'  name='observaciones' id='observaciones' value='' size='50' maxlength='200'>
								<br>
								<br>
								<input type='submit'   style=' color:#fff; background-color:rgba(118,136,29,1);  text-align: center;' value=' PROCEDER ' >
								<input type='hidden' name='Fecha_control' value='$Fecha_control'>
								<br></form>
									
								
											
											 </div>  ";}
								
										if($D->estado==2) {
											echo" 
											<div style='  margin-bottom: 5%; text-align : center;  '>
															 <h4>Cambiar estado </h4>
					                       	<form  style='  margin-bottom: 5%; text-align : center;  '  action='https://app.aoacolombia.com/i.php' target='_blank' method='POST' name='forma' id='forma'>
												Selecione un estado: 
											<select style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'  style=' text-align:  center; '  name='i'>
											<option  value=''>Selecionar un estado</option>
											<option value=\"$Ruta_Anular\">Anular</option>	</select><br>
								Observaciones: 
								<input type='text' style=' border: 1px solid rgba(118,136,29,1);'  name='observaciones' id='observaciones' value='' size='50' maxlength='200'>
								<br>
								<br>
								<input type='submit'   style=' color:#fff; background-color:rgba(118,136,29,1);  text-align: center;' value=' PROCEDER ' >
								<input type='hidden' name='Fecha_control' value='$Fecha_control'>
								<br></form>
									
								
											
											 </div>  ";
											};
										
								
									
										
								
												
									     	if($D->estado==5) { echo"
										
											<div style='  margin-bottom: 1%; text-align : center;  '>
															 <h4>Cambiar estado </h4>
					                       	<form  style='  margin-bottom: 1%; text-align : center;  '  action='https://app.aoacolombia.com/i.php' target='_blank' method='POST' name='forma' id='forma'>
												Selecione un estado: 
												<select style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'  style=' text-align:  center; '  name='i'>
										<option  value=\"$Ruta_aprobacion\">Aprobar</option>
										<option  value=\"$Ruta_aprobacion_sincorreo_pro\">Aprobar sin enviar al proveedor</option>

										<option value=\"$Ruta_daprobacion\">Rechazar</option>
												</select><br>
								Observaciones: 
								<input type='text' style=' border: 1px solid rgba(118,136,29,1);'  name='observaciones' id='observaciones' value='' size='50' maxlength='200'>
								<br>
								<br>
								<input type='submit'   style=' color:#fff; background-color:rgba(118,136,29,1);  text-align: center;' value=' PROCEDER ' >
								<input type='hidden' name='Fecha_control' value='$Fecha_control'>
								<br></form>
									
								
											
											 </div>  ";}
   
								echo"
							
								
								";
								
								
								if($D->estado==4) { 
								
									$formAprobar="";
								}

	//"$Email_aprobador,$Nombre_aprobador" /*para */,    "arturoquintero@aoacolombia.com,ARTURO QUINTERO",  
	$Ruta_alterna=base64_encode("header('location:../Control/operativo/zbalance_estado.php?Acc=aprobacion_requisicion&id=$id');");
      
		echo utf8_decode("$formAprobar");

}







 ?>