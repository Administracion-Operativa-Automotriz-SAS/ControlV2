<?php 
header('Content-Type: text/html; charset=utf-8');
function documentos_venta($Siniestro,$fechaEntrega,$Oficina,$Vehiculo,$Linea,$vehiculo2,$kilome,$claseVehiculo,$carroceria,$modalidadDeTendecia,$servicio){
?>	
	<html>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta http-equiv='Content-Type' content='text/html; charset=Windows-1252'>	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="/Administrativo/views/js/jquery-2.1.3.js"></script>
	<body>
	<style>
			td{
				font-size: 9px;
			}
			select
			{	
				font-size: 7px;
			}
	</style>
	<table width='100%' class='table_image' border cellspacing='0' align='center'>
		<tr>
			<td width='20%' rowspan=3 align='center' valign='middle'>
				<img src='http://app.aoacolombia.com/Administrativo/img/nlogo_aoa_200.jpg' border='0' class='print_image' height='50' width='110'>
			</td>
			<td align='center' rowspan=3 width='50%'><b style='font-size:16px'>Formato Acta de entrega venta vehiculo </b></td>
			<td width='30%' style='font-size:12px'>CODIGO:  AOA-OP-F-04</td>
		</tr>
		<tr>
			<td style='font-size:12px'>VERSION: 01</td>
		</tr>
		<tr>
			<td style='font-size:10px'>FECHA DE VIGENCIA: 2017-12-05 </td>
		</tr>
	</table>
	<table border='0' cellspacing='0' cellpadding='0' width='90%' align='center'>
		<tr>
			<td width='20%'></td>
			<td align='center' style='font-size:8px;' width='50%'>
				<span id='subtitle'></span>:<?php echo $Nconsecutivo ?></td>
			<td width='30%'></td>
		</tr>
	</table>
		<!--cabecera-->
	
	
		
	<link rel="stylesheet" href="/Administrativo/views/css/styles1.css">
	
		<table border cellspacing='0' width='100%' align='left' class="print_table">
			<!--Seccion info inicial-->			
			<tr>				
				<td>FECHA DE ENTREGA</td>
				<td  class="writetd">
					<?php echo $fechaEntrega ?>
				</td>
				<td bgcolor='#CCCCCC' class="theader" >CIUDAD</td>
				<td  class="writetd">
					<?php echo $Oficina->nombre ?>
				</td>
			</tr>
			<tr>
				<td>ENTREGADO A</td>
				<td>COMPRADOR FINAL</td>
				<td  class="writetd" ><div style="text-align:center;"><input type="radio" name="entregado"></div></td>
				<td>CANAL DE VENTA</td>
				<td  class="writetd" ><div style="text-align:center;"><input type="radio" name="entregado"></div></td>
				<td colspan="4"></td>
			</tr>
			<tr>
				<td>CANAL DE VENTA</td>
				<td  colspan="9" class="writetd" contenteditable="true"></td>
			</tr>
			<tr>
				<th colspan="9" bgcolor='#CCCCCC' class="theader" ><span class='Estilo4'>DATOS DEL COMPRADOR</span></th>
			</tr>
			<tr>
				<td>PLACA</td>
				<td  colspan="3" class="writetd">
					<?php echo $Vehiculo->placa ?>
					 
					</datalist>
				</td>
				<th>CONVENCION</th>
				<th  colspan="2" class="writetd" >RAYON(&nbsp&nbsp)</th>
				<th  colspan="2" class="writetd" >GOLPE(&nbsp&nbsp)</th>
			</tr>
			<tr>
				<td >MARCA</td>
				
				<td id="ajax-marca" class="writetd">
				<?php echo $vehiculo2->nom_marca ?>
				
				
				</td>
				<td >MODELO</td>
				<td  id="ajax-modelo" colspan="2" class="writetd">
				<?php echo $Linea->nombre ?>
				</td>
				<td colspan="4" rowspan="10"  align='center'>
				<img src='http://app.aoacolombia.com/Control/operativo/img/formatos/todo.png'  class="img img-responsive">
				<!--
					<img src='http://app.aoacolombia.com/Control/operativo/img/formatos/vehiculo.png' border='0' height='140' width='20%'>
					<img src='http://app.aoacolombia.com/Control/operativo/img/formatos/text.png' border='0' height='160' width='20%'>
					<img src='http://app.aoacolombia.com/Control/operativo/img/formatos/medidores.png' border='0' height='140' width='20%'>
					<br>
					<img src='http://app.aoacolombia.com/Control/img/1.png' border='0' height='140' width='100%'>
				-->
				</td>
			</tr>
			<tr>
				<td>LINEA</td>
				<td id="ajax-linea" class="writetd" contenteditable="false">
				<?php echo $Linea->nombre ?>
				</td>
				<td >COLOR</td>
				<td  colspan="2" id="ajax-color" class="writetd" contenteditable="false">
				<?php echo $Vehiculo->color ?>
				</td>
				</tr>
			<tr>
				<td>CLASE</td>
				<td id="ajax-clase" class="writetd" contenteditable="false">
				<?php echo $claseVehiculo->clase ?>
				
				
				</td>				
				<td>CILINDRAJE</td>
				<td  colspan="2" id="ajax-cilindraje" class="writetd" contenteditable="false">
				<?php echo $Vehiculo->cilindraje ?>
				</td>						
			</tr>
			<tr>
				<td>KILOMETRAJE ENTREGA</td>
				<td colspan="4" contenteditable="false">
				<?php echo $kilome ?>
				
				
				</td>
			</tr>
			<tr>
				<td >TIPO CARROCERIA</td>
				<td colspan="4" class="writetd" id="ajax-carroceria" contenteditable="false">
				<?php echo $carroceria->nombre ?>
				
				
				</td>			
			</tr>
			<tr>
				<td>PROPIETARIO AUTOMOTOR </td>
				<td  colspan="4"  class="writetd" id="ajax-propietario" contenteditable="true"></td>			
			</tr>
			<tr>
				<td  >CONTRATO DE LEASING </td>
				<td  colspan="4"  class="writetd" id="ajax-propietario" contenteditable="false">
				<?php if($modalidadDeTendecia->modalidad == null || $modalidadDeTendecia->modalidad == ""){
					   echo "NO HAY CONTRATO  LEASING";
				}else{
					echo $modalidadDeTendecia->modalidad;
				} ?>
				</td>			
				
			</tr>			
			
			<tr>
				<td >POLIZA DE SEGURO</td>
				<td  colspan="4" class="writetd" id="ajax-poliza-seguro" contenteditable="true"></td>			
			</tr>
			<tr style="height:15px;">
				<td >FECHA VENCIMIENTO SOAT</td>
				<td  colspan="4" onclick="make_visible(0)" ondblclick="make_invisible(0)" ><input class="date_input" id="einput0"  type="date"></td>			
			</tr>
			<tr>
				<td  style=" border-right: solid 1px #FFF;">NOMBRE CLARO COORDINADOR CIUDAD</td>
				<td  colspan="4" contenteditable="true" ></td>			
			</tr>
		</table>
			<!--Seccion info inicial-->
		
		<table border cellspacing='0' width='100%' align='left' class="print_table">	
			<!--Seccion documentos-->
			<tr>
				<th bgcolor='#CCCCCC' class="theader" colspan='15'><span class='Estilo4'>DOCUMENTOS ENTREGADOS CON EL VEHICULO</span></th>
			</tr>
			<tr>
				<td >TARJETA DE PROPIEDAD</td>
				<td >ORIGINAL</td>
				<td ><div style="text-align:center;"><input type="radio" name="tarjeta_propiedad"></div></td>
				<td >COPIA</td>
				<td ><div style="text-align:center;"><input type="radio" name="tarjeta_propiedad"></div></td>
				<td >SEGURO OBLIGATORIO</td>
				<td >ORIGINAL</td>
				<td ><div style="text-align:center;"><input type="radio" name="seguro_obligatorio"></div></td>
				<td >COPIA</td>
				<td ><div style="text-align:center;"><input type="radio" name="seguro_obligatorio"></div></td>
				<td >DUPLICADO DE LLAVE</td>
				<td >SI</td>
				<td ><div style="text-align:center;"><input type="radio" name="duplicado_llave"></div></td>
				<td >NO</td>
				<td ><div style="text-align:center;"><input type="radio" name="duplicado_llave"></div></td>
			</tr>
			<tr>
				<td >MANUAL DE VEHICULO</td>
				<td >SI</td>
				<td ><div style="text-align:center;"><input type="radio" name="manual_vehiculo"></div></td>
				<td >NO</td>
				<td ><div style="text-align:center;"><input type="radio" name="manual_vehiculo"></div></td>
				<td class='Estilo4'>TOMA DE IMPRONTAS</td>
				<td >SI</td>
				<td ><div style="text-align:center;"><input type="radio" name="toma_improntas"></div></td>
				<td >NO</td>
				<td ><div style="text-align:center;"><input type="radio" name="toma_improntas"></div></td>
				<td>CUANTOS JUEGOS</td>
				<td colspan="2" class="writetd" contenteditable="true"></td>
				<td colspan="2">Mínimo 3 juegos se deben tomar </td>
			</tr>
		
			<tr>
				<td  height='41'>COMENTARIOS</td>
				<td colspan='14' height='41' class="writetd" contenteditable="true"></td>
			</tr>
			<!--Seccion documentos-->
			
			<!--Seccion pago-->
			<tr>
				<th bgcolor='#CCCCCC' class="theader" colspan='15'><span class='Estilo4'><span style="color:red;">ESPACIO EXCLUSIVO PARA SER DILIGENCIADO UNA VEZ REALIZADA LA VENTA VEHICULO</span></th>
			</tr>			
			
			<tr>
				<td  height='41'>CONDICIONES DE VENTA (Indicar si existe cobro de valor adicional) </td>				
				<td colspan='14' height='41' class="writetd" contenteditable="true"></td>
			</tr>			
			
			<tr>
				<td>VALOR VENTA VEHICULO</td>
				<td colspan='4' class="writetd" contenteditable="true" >$</td>
				<td>VALOR GASTO DE TRÁMITES</td>
				<td colspan='4' class="writetd" contenteditable="true" >$</td>	
				<td>VALOR ADICIONAL</td>
				<td colspan='4' class="writetd" contenteditable="true" >$</td>				
			</tr>	

			<tr>
				<td rowspan="3">MODO PAGO</td>
				<td colspan='3'>EFECTIVO</td>
				<td colspan='2'><div style="text-align:center;"><input type="radio" name="metodo_pago"></div></td>
				<td rowspan="3" colspan="9" style="text-align:center">
					<br>
					__________________________________________<br>
					Vo. Bo. VENTA ACTIVO <br>
					GERENTE GENERAL 
				
				</td>	
			</tr>
			<tr>
				<td colspan='3'>TRANSFERENCIA</td>
				<td colspan='2'><div style="text-align:center;"><input type="radio" name="metodo_pago"></div></td>
			</tr>
			<tr>
				<td colspan='3'>CHEQUE</td>
				<td colspan='2'><div style="text-align:center;"><input type="radio" name="metodo_pago"></div></td>				
			</tr>	
		</table>
		
			<!--Seccion pago-->
		<table border cellspacing='0' width='100%' align='left' class="print_table">		
			<!--COMPRADOR-->
			<tr>
				<th bgcolor='#CCCCCC' class="theader" colspan='10'><span class='Estilo4'>DATOS DEL COMPRADOR</span></th>
			</tr>
			<tr>
				<td colspan="3">PERSONA NATURAL</td>
				<td colspan="2"><div style="text-align:center;"><input type="radio" name="tipo_persona" value="1"></div></td>
				<td colspan="3">PERSONA JURIDICA</td>
				<td colspan="2"><div style="text-align:center;"><input type="radio" name="tipo_persona" value="2"></div></td>
			</tr>
			<tr>
				<td colspan="2">NOMBRES Y APELLIDOS</td>
				<td colspan='8' id="ajax-cliente-nombre" class="writetd" contenteditable="true"></td>
			</tr>
			
			<tr>			
				<td width="5%">CC</td>
				<td ><div style="text-align:center;"><input type="radio" name="tipo_documentos" value="CC"></div></td>
				<td width="5%">CE</td>
				<td><div style="text-align:center;"><input type="radio" name="tipo_documentos" value="CE"></div></td>
				<td width="5%">NIT</td>
				<td><div style="text-align:center;"><input type="radio" name="tipo_documentos" value="NIT"></div></td>
				<td width="5%">OTROS</td>
				<td><div style="text-align:center;"><input type="radio" name="tipo_documentos" value="OTROS"></div></td>
				<td  width='10%'>No. de identificacion</td>
				<td class="writetd" id="doc_number" width='20%'   onblur="look_for_customer()" contenteditable="true"></td>
			</tr>
			<tr>
				<td >DIRECCIÓN</td>
				<td colspan='4' class="writetd" id="ajax-cliente-direccion" contenteditable="true"></td>
				<td >CIUDAD</td>
				<td colspan='4' class="writetd" id="ajax-cliente-ciudad" contenteditable="true"></td>
			</tr>
			<tr>
				<td >TELEFONO FIJO</td>
				<td colspan='2' class="writetd" id="ajax-cliente-telefono" contenteditable="true"></td>
				<td >CELULAR</td>
				<td colspan='2' class="writetd" id="ajax-cliente-celular" contenteditable="true"></td>
				<td >EMAIL</td>
				<td colspan='3' class="writetd" id="ajax-cliente-email" contenteditable="true"></td>
			</tr>
			<tr>
				<td  height='41'>OBSERVACIONES</td>
				<td colspan='5' height='41' class="writetd" contenteditable="true"></td>
				<td colspan='2'  height='41'>
					Documentos para personal natural:
					&nbsp;
					<li>Cedula.</li>
				</td>
				<td colspan='2'  height='41'>
					Documentos para persona juridica:
					&nbsp;
					<li>Camara de comercio no mayor a 30 díƒÂ­as.</li>
					<li>Cedula representante legal.</li>
					<li>Rut.</li>
				</td>
			</tr>
			<!--COMPRADOR-->
		</table>
		
		<table border cellspacing='0' width='100%' align='left' class="print_table">	
			<!--DOCUMENTOS-->
			<tr>
				<th bgcolor='#CCCCCC' class="theader" colspan='10'><span class='Estilo4'>DOCUMENTOS DE TRASPASO</span></th>
			</tr>
			<tr>
				<th  colspan='2'>FLOTA AOA - PERSONA NATURAL</th>
				<th  colspan='2'>FLOTA AOA - PERSONA JURIDICA</th>
				<th  colspan='2'>FLOTA LEASING - PERSONA NATURAL</th>
				<th  colspan='2'>FLOTA LEASING - PERSONA JURIDICA</th>
				<th  colspan='2'>INSCRIPCION DE PRENDA</th>
			</tr>			
			<tr>
				<td >Contrato de Compraventa</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Contrato de Compraventa</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Solicitar Contratos a Leasing</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Solicitar Contratos a Leasing</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Formulario de solicitud FUN</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
			</tr>
			<tr>
				<td >Formulario de  solicitud FUN</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Formulario de  solicitud FUN</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Contrato de transferencia</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Contrato de transferencia</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Copia de la TP a Nombre del nuevo Dueí±o</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
			</tr>
				<td >Contrato de Mandato</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Contrato de Mandato</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Formulario de solicitud FUN</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Formulario de solicitud FUN</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Certificación nueva inscripción de prenda </td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
			<tr>
				<td >Copia cédula del comprador</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Cámara de Comercio entidad Jurídica no mayor a 30 días </td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Contrato de mandato</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Contrato de mandato</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td>Improntas</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
			</tr>
			<tr>
				<td >RECIBIO SIM</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Copia de la cédula del representante legal  </td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Recibo Sim </td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Cámara de Comercio entidad Jurídica no mayor a 30 días </td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Contrato de Mandato </td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
			</tr>
			<tr>
				<td >Levantamiento prenda según sea el caso</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Copia de la cédula del representante legal  </td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Improntas</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >opia de la cédula del representante legal </td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Cédula del comprador</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
			</tr>
			<tr>
				<td>Improntas</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Levantamiento Prenda Segíºn sea el Caso </td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td colspan="2"></td>				
				<td >Recibo del Sim</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Cédula del Autorizado </td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
			</tr>
			<tr>
				<td  colspan='2'></td>
				<td >Improntas</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td colspan='2' ></td>
				<td >Improntas</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td >Recibio Sim</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>							
			</tr>
		</table>
		
		<table border cellspacing='0' width='100%' align='left' class="print_table">
			<!--DOCUMENTOS-->
			<tr>
				<th bgcolor='#CCCCCC' class="theader" colspan="15" ><span class='Estilo4' style="color:red">TRAMITE FINALIZADO - LISTA DE CHEQUEO CARPETA DEL VEHICULO </span></th>
			</tr>
			<tr>
				<td>TARJETA DE PROPIEDAD TRAMITE FINALIZADO</td>
				<td >ORIGINAL</td>
				<td><div style="text-align:center;"><input type="radio" name="tarjeta_propiedad"></div></td>
				<td >COPIA</td>
				<td><div style="text-align:center;"><input type="radio" name="tarjeta_propiedad"></div></td>
				<td colspan="2">SEGURO OBLIGATORIO</td>
				<td >COPIA</td>
				<td><div style="text-align:center;"><input type="checkbox"></div></td>
				<td colspan="2">CERTIFICADO DE GASES</td>
				<td >SI</td>
				<td><div style="text-align:center;"><input type="radio" name="certificado_gases"></div></td>
				<td >NO</td>
				<td><div style="text-align:center;"><input type="radio" name="certificado_gases"></div></td>
			</tr>
			<tr>				
				<td >DUPLICADO DE LLAVE</td>
				<td >SI</td>
				<td><div style="text-align:center;"><input type="radio" name="duplicado_luz"></div></td>
				<td >NO</td>
				<td><div style="text-align:center;"><input type="radio" name="duplicado_luz"></div></td>
				<td >EMPADRONAMIENTO</td>
				<td >SI</td>
				<td><div style="text-align:center;"><input type="radio" name="empadronamiento"></div></td>
				<td >NO</td>
				<td><div style="text-align:center;"><input type="radio" name="empadronamiento"></div></td>
				<td >FACTURA COMPRA VEHICULO</td>
				<td >SI</td>
				<td><div style="text-align:center;"><input type="radio" name="factura_compra"></div></td>
				<td >NO</td>
				<td><div style="text-align:center;"><input type="radio" name="factura_compra"></div></td>
			</tr>
			<tr>				
				<td >RECIBOS  PAGOS IMPUESTOS Aí‘OS</td>
				<td colspan="14" class="writetd" contenteditable="true"></td>
			</tr>			
			<tr>
				<td  height='41'>OBSERVACIONES</td>
				<td colspan="14" height='41'class="writetd" contenteditable="true"></td>
			</tr>
			<br><br>
		</table>
		
		<!--Formato lista de chequeo venta vehiculo -->
		<table border='0' cellspacing='0' cellpadding='0' width='100%' align='center'>
		<tr>
			<td width='20%'></td>
			<td align='center' style='font-size:8px;' width='50%'>
				<span id='subtitle'></span>:<?php echo $Nconsecutivo ?></td>
			<td width='30%'></td>
		</tr>
	</table>
		<br>
		<table width='100%' class='table_image' border cellspacing='0' align='center'>
		<tr>
		<td width='20%' rowspan=3 align='center' valign='middle'>
			
				<img src='http://app.aoacolombia.com/Administrativo/img/nlogo_aoa_200.jpg' border='0' class='print_image' height='50' width='110'>
			</td>
			<td align='center' rowspan=3 width='50%'><b style='font-size:16px'>Formato lista chequeo venta vehiculo</b></td>
			<td width='30%' style='font-size:12px'>CODIGO: AOA-OP-F-05</td>
		</tr>
		<tr>
			<td style='font-size:12px'>VERSION: 02</td>
		</tr>
		<tr>
			<td style='font-size:10px'>FECHA DE VIGENCIA: 2018-09-19 </td>
		</tr>
	</table>
	<table border='0' cellspacing='0' cellpadding='0' width='100%' align='center'>
		<tr>
			<td width='20%'></td>
			<td align='center' style='font-size:8px;' width='50%'>
				<span id='subtitle'>Consecutivo Interno Número</span>:<?php echo $Nconsecutivo ?></td>
			<td width='30%'></td>
		</tr>
	</table>
		<!--cabecera-->
	<link rel="stylesheet" href="/Administrativo/views/css/styles1.css">
	
	<table border cellspacing='0' width='100%' align='left'>
		<tr>
			<td class="writetd" width="25%"  bgcolor='#CCCCCC' ><b>PLACA</b></td>
			<td width="25%">
				<?php echo $Vehiculo->placa ?>
			</td>
			<td class="writetd" width="25%" bgcolor='#CCCCCC'><b>MARCA</b></td>
			<td class="writetd" contenteditable="false" width="25%" id="vehiculo_marca">
			<?php echo $vehiculo2->nom_marca ?>
			</td>		
		</tr>
		<tr>
			<td bgcolor='#CCCCCC'><b>LINEA</b></td>
			<td class="writetd" contenteditable="false" id="vehiculo_linea">
			<?php echo $Linea->nombre ?>
			</td>
			<td bgcolor='#CCCCCC'><b>MODELO</b></td>
			<td class="writetd" contenteditable="false" id="vehiculo_modelo">
			<?php echo $Linea->nombre ?>
			</td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC'><b>CLASE</b></td>
			<td class="writetd" contenteditable="false" id="vehiculo_clase">
			<?php echo $claseVehiculo->clase ?>
			</td>
			<td bgcolor='#CCCCCC'><b>COLOR</b></td>
			<td class="writetd" contenteditable="false" id="vehiculo_color">
			<?php echo $Vehiculo->color ?>
			</td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC'><b>CILINDRAJE</b></td>
			<td class="writetd" contenteditable="false" id="vehiculo_cilindraje">
			<?php echo $Vehiculo->cilindraje ?>
			</td>
			<td bgcolor='#CCCCCC'><b>TIPO CARROCERIA</b></td>
			<td class="writetd" contenteditable="false" id="vehiculo_tipo_carroceria">
			<?php echo $carroceria->nombre ?>
			</td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC'><b>PROPIETARIO</b></td>
			<td class="writetd" contenteditable="true" id="vehiculo_propietario"></td>
			<td bgcolor='#CCCCCC'><b>CONTRATO</b></td>
			<td class="writetd" contenteditable="true" id="vehiculo_contrato"></td>
		</tr>
		<tr>
			<td bgcolor='#CCCCCC'><b>CANAL DE VENTA</b></td>
			<td colspan="3" style="text-align:center;" contenteditable="true"></td>
		</tr>
	</table>
	<table border cellspacing='0' width='100%' align='left'>
		<tr>
			<td  rowspan="2"><b>VISTO BUENO VENTA ACTIVO</b></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td width="14%" onclick="make_visible(0)" ondblclick="make_invisible(0)"><input class="date_input" id="einput0" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>
		<tr>
			<td><b>FIRMA/NOMBRE</b></td>
			<td contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td onclick="make_visible(1)" ondblclick="make_invisible(1)"><input class="date_input" id="einput1" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td contenteditable="true"></td>
		</tr>
		<tr>
			<td colspan="7" bgcolor='#CCCCCC' style="text-align:center;"><b>OBSERVACIONES Y/O CONDICIONES DE VENTA DE FLOTA</b></td>
		</tr>
		<tr>
			<td colspan="7" contenteditable="true" style="height:80px;"></td>
		</tr>
		<tr>
			<td  ><b>VALIDAR SINIESTRO</b></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td width="14%" onclick="make_visible(2)" ondblclick="make_invisible(2)"><input class="date_input" id="einput2" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>
		<tr>
			<td  ><b>PLATAFORMA DE CONTROL <br> (Cambio a nueva Ubicación  "USADOS") </b></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td width="14%" onclick="make_visible(3)" ondblclick="make_invisible(3)"><input class="date_input" id="einput3" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>		
		<tr>
			<td colspan="7" bgcolor='#CCCCCC' style="text-align:center;"><b>OBSERVACIONES</b></td>
		</tr>
		<tr>
			<td colspan="7" contenteditable="true" style="height:80px;"></td>
		</tr>
		<tr>
			<td  ><b>LEV. DE PRENDA</b></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td width="14%" onclick="make_visible(5)" ondblclick="make_invisible(5)"><input class="date_input" id="einput5" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>
		<tr>
			<td  ><b>PAGO SALDO LEASING</b></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td width="14%" onclick="make_visible(4)" ondblclick="make_invisible(4)"><input class="date_input" id="einput4" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>
		<tr>
			<td colspan="7" bgcolor='#CCCCCC' style="text-align:center;"><b>OBSERVACIONES</b></td>
		</tr>
		<tr>
			<td colspan="7" contenteditable="true" style="height:80px;"></td>
		</tr>
		<tr>
			<td  ><b>PLATAFORMA DE CONTROL<br>(Cambio a nueva Ubicación  "VENTA DE FLOTA")</b></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td width="14%" onclick="make_visible(6)" ondblclick="make_invisible(6)"><input class="date_input" id="einput6" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>
		<tr>
			<td><span ><b>FACTURA DE VENTA</b></span><input type="text" placeholder="No." style="width:100%;"></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td width="14%" onclick="make_visible(7)" ondblclick="make_invisible(7)"><input class="date_input" id="einput7" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>		
		<tr>
			<td width="14%" onclick="make_visible(8)" ondblclick="make_invisible(8)"><b>BANCO</b> Fecha ingreso<input class="date_input" id="einput8" type="date" /></td>	
			<td><b>VALOR</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td contenteditable="true"></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>
		<tr>
			<td><b>RECIBO CAJA</b><input type="text" placeholder="No." style="width:100%;"></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td width="14%" onclick="make_visible(9)" ondblclick="make_invisible(9)"><input class="date_input" id="einput9" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>
		<tr>
			<td colspan="7" bgcolor='#CCCCCC' style="text-align:center;"><b>OBSERVACIONES</b></td>
		</tr>
		<tr>
			<td colspan="7" contenteditable="true" style="height:80px;"></td>
		</tr>
		<tr>
			<td><b>EXCLUSIÓN POLIZA COLECTIVA</b><input type="text" placeholder="Aseguradora" style="width:100%;"></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td width="14%" onclick="make_visible(10)" ondblclick="make_invisible(10)"><input class="date_input" id="einput10" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>
		<tr>
			<td  ><b>PLATAFORMA DE CONTROL (Inactividad del vehiculo)</b></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td width="14%" onclick="make_visible(11)" ondblclick="make_invisible(11)"><input class="date_input" id="einput11" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>
		<tr>
			<td colspan="7" bgcolor='#CCCCCC' style="text-align:center;"><b>OBSERVACIONES</b></td>
		</tr>
		<tr>
			<td colspan="7" contenteditable="true" style="height:80px;"></td>
		</tr>
		<tr>
			<td><b>FACTURA DE TRAMITES</b><input type="text" placeholder="No." style="width:100%;"></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td width="14%" onclick="make_visible(12)" ondblclick="make_invisible(12)"><input class="date_input" id="einput12" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>
		<tr>
			<td width="14%" onclick="make_visible(13)" ondblclick="make_invisible(13)"><b>BANCO</b> Fecha ingreso:<input class="date_input" id="einput13" type="date" /></td>
			<td><b>VALOR</b></td> 
			<td width="14%" contenteditable="true"></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td contenteditable="true"></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>
		<tr>
			<td><b>RECIBO CAJA</b><input type="text" placeholder="No." style="width:100%;"></td>
			<td><b>FIRMA/NOMBRE</b></td>
			<td width="14%" contenteditable="true"></td>
			<td><b>FECHA</b></td>
			<td width="14%" onclick="make_visible(14)" ondblclick="make_invisible(14)"><input class="date_input" id="einput14" type="date" /></td>
			<td><b>ÁREA</b></td>
			<td width="14%" contenteditable="true"></td>
		</tr>
		<tr>
			<td colspan="7" bgcolor='#CCCCCC' style="text-align:center;"><b>COMENTARIOS EN GENERAL</b></td>
		</tr>
		<tr>
			<td colspan="7" contenteditable="true" style="height:80px;"></td>
		</tr>
	</table>
	<table border='0' cellspacing='0' cellpadding='0' width='100%' align='center'>
		<tr>
			<td width='20%'></td>
			<td align='center' style='font-size:8px;' width='50%'>
				<span id='subtitle'></span>:<?php echo $Nconsecutivo ?></td>
			<td width='30%'></td>
		</tr>
	</table>
	<table border='0' cellspacing='0' cellpadding='0' width='100%' align='center'>
		<tr>
			<td width='20%'></td>
			<td align='center' style='font-size:8px;' width='50%'>
				<span id='subtitle'></span>:<?php echo $Nconsecutivo ?></td>
			<td width='30%'></td>
		</tr>
	</table>
	
	<center><b>CONTRATO DE MANDATO</b></center>
	<p>Entre los suscritos a saber  SEBASTIAN HURTADO LARREAMENDI mayor de edad, vecino de esta ciudad, 
	identificado con la cedula de ciudadanía No.11.202.032 de Chía (Cundinamarca), en representación de ADMINISTRACIÓN 
	OPERATIVA AUTOMOTRIZ S.A.S. identificado con Nit 900174552-5, quien para efecto del presente contrato se denominará 
	EL MANDANTE, y de otro  el señor ________________________ también mayor de edad, y vecino de esta ciudad, identificado 
	con la cédula de ciudadanía No ___________ de ____________ (_______________), quien para efecto del presente contrato se 
	denominara EL MANDATARIO, hemos acordado suscribir el siguiente contrato de mandato dando cumplimiento a la Resolución 
	12379 expedida en el Ministerio de Transporte el 28 de diciembre del 2012 (Art. 5)  que se regirá por las normas civiles y 
	comerciales que regulan la materia en concordancia con el Art. 2149 C.C. Según las siguientes cláusulas:</p>
	<br>
	<p>PRIMERA: OBJETO DE CONTRATO:</p>
	<br>
	<p>EL MANDATARIO por cuenta y riesgo del MANDANTE queda facultado para solicitar, realizar, radicar y retirar 
	el trámite de  _                                                   _ del vehículo de propiedad del 
	MANDANTE, identificado con placas    ______   EL MANDATARIO queda facultado para realizar todas las gestiones 
	propias de este mandato y en especial para representar notificarse, recibir, impugnar, transigir, desistir,
	sustituir, reasumir, pedir, conciliar o asumir obligaciones en nombre de MANDANTE.</p>
	<p>SEGUNDA: OBLIGACIONES DEL MANDANTE:
	EL MANDANTE declara que la información contenida en los documentos que se anexa a la solicitud del trámite es veraz y 
	autentica, razón por la que se hace responsable ante la autoridad competente de cualquier irregularidad que los mismos puedan contener.
    </p>
	<p>Atentamente,</p>
	<br>
	<div style="display: flex;justify-content: space-between;">
	<p>MANDANTE</p><p>MANDATARIO</P>
	</div>
	<br>
	<b>ADMINISTRACION OPERATIVA AUTOMOTRIZ SAS</b>
	<br>
	<div style="display: flex;justify-content: center;">
	<br>
		<div>			__________________________________________<br>
					SEBASTIAN HURTADO LARREAMENDI <br>
					C.C. 11.202.032
					<br>
				</div>
			<div>				
					 &nbsp; &nbsp;__________________________________________<br>
					  <br>
					C.C. 
					</div>
	</div>
	<table border='0' cellspacing='0' cellpadding='0' width='100%' align='center'>
		<tr>
			<td width='20%'></td>
			<td align='center' style='font-size:8px;' width='50%'>
				<span id='subtitle'></span>:<?php echo $Nconsecutivo ?></td>
			<td width='30%'></td><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
			<br><br><br><br><br><br><br><br><br><br>
		</tr>
	</table>
	<center><b>CONTRATO DE COMPRAVENTA DE VEHÍCULO AUTOMOTOR</b></center>
	<p><b>ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S.</b>identificada con el Nit. 900.174.552-5, mediante su representante legal 
	SEBASTIAN HURTADO LARREAMENDI identificado con cédula de ciudadanía número 11.202.032 de Chía tal y como aparece al  pie de su 
	firma, quien en adelante se denominará EL VENDEDOR, y <b style="width: 35%;" contenteditable="true"><?php echo $Siniestro->asegurado_nombre; ?></b>&nbsp; &nbsp;identificado con cedula No.&nbsp; &nbsp;<b style="width: 35%;" contenteditable="true"><?php echo $Siniestro->asegurado_id ?></b>()
	&nbsp; &nbsp;tal y como aparece al pie de su firma y en adelante se denominará EL COMPRADOR, hemos acordado celebrar CONTRATO DE COMPRAVENTA que 
	se regirá por las normas civiles y comerciales que regulan la materia, según las siguientes cláusulas:</p>
	<br>
	<p><b>Primera. Objeto: EL VENDEDOR</b> transferir a EL COMPRADOR la propiedad del vehículo automotor que a continuación se identifica:						
	</p>
	<div>
	<p>Placa:&nbsp; &nbsp;<?php echo $Vehiculo->placa ?><br>
	Modelo:&nbsp; &nbsp;<?php echo $Vehiculo->modelo ?><br>
	Linea:&nbsp; &nbsp;<?php echo $Linea->nombre ?><br>
	Clase:&nbsp; &nbsp;<?php echo $claseVehiculo->clase ?><br>
	Carroceria:&nbsp; &nbsp;<?php echo $carroceria->nombre ?><br>
	Motor:&nbsp; &nbsp;<?php echo $Vehiculo->numero_motor ?><br>
	Color:&nbsp; &nbsp;<?php echo $Vehiculo->color ?><br>
	Chasis:&nbsp; &nbsp;<?php echo $Vehiculo->numero_chasis ?><br>
	Servicio:&nbsp; &nbsp;<?php echo $servicio->nombre ?><br>
	</p>
	<p><b>Segunda. Precio:</b> Las partes pactan la suma__________________________________________  ($                       )</p>
	<p><b>Tercera. Forma de pago: EL COMPRADOR</b> Paga el precio a que se refiere la cláusula anterior en la siguiente forma: EFECTIVO</p>
	<p><b>Cuarta. Obligaciones de EL VENDEDOR: EL VENDEDOR</b> se obliga a  hacer entrega del vehículo automotor en buen estado, libre 
	de gravámenes embargos, multas, impuestos, pactos de reserva de dominio y cualquiera otra circunstancia que afecte el libre comercio del 
	bien objeto del presente contrato. </p>
	<p><b>Parágrafo: EL VENDEDOR</b> se obliga a firmar el formulario de traspaso dentro de los TREINTA  (30) días posteriores a la 
	firma del presente escrito.</p>
	<p><b>Quinta. Gastos:</b> Los gastos como impuestos, multas y demás que recaigan sobre el vehículo automotor antes de la inscripción del traspaso 
	ante la Oficina de Tránsito corre por cuenta de<b> EL VENDEDOR.</b> Los gastos de registro se pagarán en partes iguales, excepto la Retención en la 
	Fuente a título de impuesto de renta que corre por cuenta de <b>EL VENDEDOR.</b></p>
	
	<p>Sexta. Responsabilidad del Comprador: Actualmente la propiedad del vehículos se encuentra inscrita a nombre de 
	ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S., pero con objeto del presente contrato desde la firma del presente documento, 
	la parte responsable sobre cualquier hecho, o eventualidad que pueda generar responsabilidad civil o penal, es del <b>EL COMPRADOR,</b>
	el cual se obliga a resarcir cualquier perjuicio que cause a ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S. por la posesión y tenencia del 
	vehículo,  mientras este permanezca  a su nombre.<b>EL COMPRADOR</b> entiende que  cuando el vehículo salga de las instalaciones de AOA, no estará 
	cubierto por póliza de seguros, por lo tanto en caso de hurto o accidente no se podrá hacer reclamación  a ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S., 
	así mismo<b>EL COMPRADOR</b> se responsabiliza ante cualquier autoridad de transito por conducir el vehículo con un fotocopia de la tarjeta de propiedad, 
	mientras se genera la nueva licencia de traspaso a mi nombre. Igualmente asumo todas las multas, infracciones de tránsito que puedan interponer 
	desde el día de la firma del presente contrato, al vehículo identificado con placas</p>
	
	<p><b>Séptima. Cláusula penal:</b> las partes establecen que quien incumpla cualquiera de las estipulaciones  derivadas  de este contrato, pagará a la otra 
	como sanción la suma de equivalente al 10% del valor estipulado en la Cláusula Segunda. Entre el incumplimiento tambien se incluirá cuando el COMPRADOR 
	no cancele dentro de los 10 dias siguientes de reportada la noveddad de multas, comparendos, inscripciones en el RUNT, para realizar 
	los traspasos. Dicho 
	valor se podra cobrar con este contrato el cual presta merito ejecutivo.</p>
	
	<p>Esta acta de Compraventa se firma en dos (2) ejemplares iguales, en  la ciudad de Bogotá a los__ días del mes_______ de  del año ______</p>
	
	
	<div style="display: flex;justify-content: space-between;">
	<p>EL VENDEDOR</p><p>EL COMPRADOR</P>
	</div>
	
	<div style="display: flex;justify-content: center;">
	<br>
		<div>			__________________________________________<br>
					Representante Legal <br>
					SEBASTIAN HURTADO LARREAMENDI<br>
					C.C. 11.202.032<br>
					ADMINISTRACION OPERATIVA AUTOMOTRIZ SAS
					<br>
				</div>
			<div>				
					 
					 Nombre____________________________________<br>
					&nbsp; &nbsp;C.C.__________________________________________
					
					</div>
	</div>
	</div>
	
	
	
	<!--VEHICULO leasing -->
	
	
	<?php 
	
	if($modalidadDeTendecia->modalidad == null || $modalidadDeTendecia->modalidad == ""){
		
	echo $varHtmlContratoAoa = "<div>
	<table border='0' cellspacing='0' cellpadding='0' width='100%' align='center'>
		<tr>
			<td width='20%'></td>
			<td align='center' style='font-size:8px;' width='50%'>
				<span id='subtitle'></span>:<?php echo $Nconsecutivo ?>
				</td>
			<td width='30%'></td><br><br><br><br><br><br><br><br><br><br><br><br><br>
			
		</tr>
	</table>
	<div>
	<p>Cuando el vehículo es de propiedad de AOA</p>
	<p>Bogotá D.C., ___________</p>
	<p>ACTA DE CONSIGNACION DE VEHÍCULOS </p>
	<p>Yo _______________________________ identificado con la __________________, hacemos constar que en la fecha estamos 
	recibiendo de ADMINISTRACION OPERATIVA AUTOMOTRIZ SAS, el vehículo descrito con las siguientes características:</p>
	</div>
	<div>
	<p>
	Clase:&nbsp; &nbsp;$claseVehiculo->clase<br>
	Marca:&nbsp; &nbsp;$vehiculo2->nom_marca<br>
	Linea:&nbsp; &nbsp;$Linea->nombre<br>
	Modelo:&nbsp; &nbsp;$Vehiculo->modelo<br>
	Placa:&nbsp; &nbsp;$Vehiculo->placa<br>
	Motor:&nbsp; &nbsp;$Vehiculo->numero_motor<br>
	Serie:&nbsp; &nbsp;$Vehiculo->numero_serie<br>
	Color:&nbsp; &nbsp;$Vehiculo->color<br>
	</p>
	</div>
	<p>Actualmente la propiedad de este vehículo se encuentra inscrita a nombre de <b> ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S.</b>, 
	pero en adelante será responsabilidad de ________________________________. Cualquier hecho o eventualidad que pueda generar 
	responsabilidad civil o penal y nos obligamos a resarcir cualquier perjuicio que causemos a<b> ADMINISTRACION OPERATIVA AUTOMOTRIZ 
	S.A.S.</b>, por la posesión y tenencia del vehículo, mientras este permanezca a su nombre. </p>
	
	<p>Entiendo que el vehículo al salir de las instalaciones de<b> ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S.</b>, No estará cubierto por la 
	póliza de seguro de este, por lo tanto, en caso de hurto o accidente, no se podrá hacer reclamación a <b>ADMINISTRACION OPERATIVA AUTOMOTRIZ 
	S.A.S.</b>, así mismo me responsabilizó ante cualquier autoridad de transito por conducir el vehículo con una fotocopia de la tarjeta de 
	propiedad, mientras generan la nueva licencia de traspaso a mi nombre.</p>
	
	<p>Nota: Me comprometo como CONSIGNATARIO a cancelar las multas adicionales que se generen de placa: _____, según características descritas 
	anteriormente.</p>
	
	<p>Igualmente informo a<b> ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S.</b>, que conozco y acepto la situación actual del SIM, en cuanto a la demora 
	en la entrega de documentos sin perjuicio alguno para <b>ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S.</b></p>
	<p>Atentamente,</p>
	<div>
		__________________________________________<br>
					Representante Legal <br>
					Nit/C.C:____________________ <br>
					CONSIGNATARIO
					<br>
	</div>
	</div>";
	}else{
		
	
	echo $varHtmlContratoleasing = "<div>
	<table border='0' cellspacing='0' cellpadding='0' width='100%' align='center'>
		<tr>
			<td width='20%'></td>
			<td align='center' style='font-size:8px;' width='50%'>
				<span id='subtitle'></span>:<?php echo $Nconsecutivo ?>
				</td>
			<td width='30%'></td><br><br><br><br><br><br><br><br><br><br><br><br><br>
			
		</tr>
	</table>
	<p>Cuando es un vehículo en Leasing </p>
	<p>Bogotá D.C., ___________</p>
	<p>ACTA DE CONSIGNACION DE VEHÍCULOS </p>
	
	<p>Yo _______________________________ identificado con la __________________, hacemos constar que en 
	la fecha estamos recibiendo de <b>ADMINISTRACION OPERATIVA AUTOMOTRIZ SAS</b>, como Locatarios del vehículo descrito con 
	las siguientes características</p>
	
	<div>
	<p>
	Clase:&nbsp; &nbsp;$claseVehiculo->clase<br>
	Marca:&nbsp; &nbsp;$vehiculo2->nom_marca<br>
	Linea:&nbsp; &nbsp;$Linea->nombre<br>
	Modelo:&nbsp; &nbsp;$Vehiculo->modelo<br>
	Placa:&nbsp; &nbsp;$Vehiculo->placa<br>
	Motor:&nbsp; &nbsp;$Vehiculo->numero_motor<br>
	Serie:&nbsp; &nbsp;_____________________________________<br>
	Color:&nbsp; &nbsp;$Vehiculo->color<br>
	</p>
	</div>
	<p>Actualmente la propiedad de este vehículo se encuentra inscrita a nombre de Leasing ___________________, donde 
	<b>ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S.</b> es el Locatario, según Contrato de Leasing ____________, pero en adelante será 
	responsabilidad de ________________________________. Cualquier hecho o eventualidad que pueda generar responsabilidad civil o
	penal, nos obligamos a resarcir cualquier perjuicio que causemos a<b> ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S.</b>, por 
	la posesión y tenencia del vehículo, mientras este permanezca a su nombre. </p>
	
	<p>Entiendo que el vehículo al salir de las instalaciones de <b>ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S.</b>, No estará cubierto por la 
	póliza de seguro de este, por lo tanto, en caso de hurto o accidente, no se podrá hacer reclamación a 
	<b>ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S.</b>, así mismo me responsabilizó ante cualquier autoridad de transito por conducir el 
	vehículo con una fotocopia de la tarjeta de propiedad, mientras se genere el traspaso de la propiedad a nombre del nuevo dueño.</p>
	
	<p>Nota: Me comprometo como CONSIGNATARIO a cancelar las multas adicionales que se generen de placa: _____, según características descritas 
	anteriormente.</p>
	
	<p>Igualmente informo a<b> ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S.</b>, como Locatario del Vehículo, que conozco y acepto la situación actual 
	del SIM y de la Leasing, en cuanto a la demora en la entrega de documentos sin perjuicio alguno para <b>ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S.</b></p>
	
	<p>Atentamente,</p>
	<div>
		__________________________________________<br>
					Representante Legal <br>
					Nit/C.C:____________________ <br>
					CONSIGNATARIO
					<br>
	</div>
	
	</div>";
	}
	?>
	</body>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
	<script src="/Administrativo/views/js/editable_table.js"></script>
	<script src="/Administrativo/views/js/basicinput.js"></script>
	<script src="/Administrativo/views/js/save_document.js"></script>
	<script src="/Administrativo/views/js/car_info.js"></script>
 </html>
	
	
		
<?php		
}
?>