<!-- Jquery -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


<?php header('Content-Type: text/html; charset=utf-8'); ?>

	<body><h3>ELABORACION DE NOTA CREDITO</h3>
	<button onclick="closeWindow()" >Cerrar</button>
	<div id="FormContent" >
		<input type="hidden" value="<?php  echo $Factura->id ?>" name="idFac" >
		
		Factura: <b><?php  echo $Factura->consecutivo ?></b>
		Cliente: <b><?php  echo $Cliente->nombre." ".$Cliente->apellido ?> </b><br>
		Aseguradora: <b><?php  echo $Aseguradora->nombre ?></b>
		Fecha de emisión: <b><?php  echo $Factura->fecha_emision ?></b>
		Fecha vencimiento: <b><?php  echo $Factura->fecha_vencimiento ?></b><br>
		Siniestro: <b><?php  echo $Factura->nsiniestro ?></b> <br>
		Valor:
			<b style='color:3333aa'>
			
			$ <?php echo coma_format($Factura->total)." ".enletras($Factura->total); ?>
			
			</b> <br>
		<hr>
		<table><tr><td align='right'>Fecha de emisión:</td><td> <?php echo  pinta_FC('forma','fecha_emision',date('Y-m-d')) ?></td></td>
		<tr><td align='right'>Descripción:</td>
		
		<td><textarea name='descripcion' style='font-family:arial;font-size:12px' cols=80 rows=4>
			<?php if(isset($notaCredito->descripcion)){
					echo trim($notaCredito->descripcion);
			}  ?>
		</textarea></td></tr>
		<tr>
			
			<td align='right'>Items de la factura:</td>
			<td>
				<table>
					<thead>
						<tr>
							<th>Agregar item</th>
							<th>Item</th>
							<th>Cantidad</th>
							<th>Porc iva</th>
							<th>Valor Iva</th>
							<th>Valor unitario</th>
							<th>Seguro</th>
							<th>Total</th>	
							
						</tr>
					</thead>
					<tbody>
						
						<?php
						$c = 0;
						 foreach($itemsFactura as $item){  
							 if($c == 0){
								if($saldo>0){
									$item->unitario = $item->unitario - ($saldo - $diferencia_iva);
									$item->iva = $item->iva - $diferencia_iva ;
								}
						    }
							$c++;
						?>
						<tr>
						
							<td style="text-align:center;">
								<input type="checkbox"  class="addItem" >
								<input type = "hidden" class="itemId" value="<?php echo $item->id ?>" />
							</td>
							<td>
								<input type = "hidden" class="itemConcepto" value="<?php echo $item->concepto ?>" />
								<?php echo $item->nombre ?>
							</td>
							<td>
								<input type = "hidden" class="quantityMax" value="<?php echo $item->cantidad ?>" />
								<input class="quantity" value="<?php echo $item->cantidad ?>" type="number" max="<?php echo $item->cantidad ?>"   style="width:80px"/>  
							</td>
							<td>
								<span class="porc_iva" ><?php echo $item->porc_iva ?></span>
							</td>
							<td>
								<input class="val_iva" value="<?php echo $item->iva ?>" type="number" max="<?php echo $item->iva ?>"  style="width:80px"/>
							</td>
							<td>
								<input type = "hidden" class="valueMax" value="<?php echo $item->unitario ?>" />
								<input type = "hidden" class="val_canon" value="<?php echo $item->canon ?>" />
								<input class="unitaryValue" value="<?php echo $item->unitario ?>" step="1000" type="number" max="<?php echo $item->unitario ?>" min="1000" />
							</td>	
							<td>
								<input class="" value="<?php echo $item->seguro ?>" type="number" disabled  style="width:80px"/>
							</td>					
							<td>
								<span class="totalItem"> <?php
								if($item->canon!=0){
									echo ($item->cantidad*$item->unitario)+( $item->iva );
								}else{
									echo ($item->cantidad*$item->unitario)+( ($item->porc_iva / 100 ) * ($item->cantidad*$item->unitario) );
								}
								
								  ?>  </span>
							</td>
							
						</tr>	
						<?php } ?>
						
					</tbody>
				</table>	
			</td>	
		
		</tr>
		<tr><td align='right'>NC Realizados: (-)</td><td><input type='text' name='saldo' id='saldo' class='numero' value='<?php echo $saldo;  ?>' size='10' maxlength='10' readonly> <!-- onblur='valida_bruto();' --> </td></tr>
		<tr><td align='right'>Valor de la Nota Crédito:</td><td><input type='text' name='valor_bruto' id='valor_bruto' class='numero' value='0' size='10' maxlength='10' readonly> <!-- onblur='valida_bruto();' --> </td></tr>
		<tr><td align='right'>Base del Iva:</td><td><input type='text' name='base_iva' id='base_iva' class='numero' value='0' size='10' maxlength='10' readonly> <!--onblur='valida_base_iva();--> </td></tr>
		
		<!--<tr><td align='right'>Porcentaje del Iva:</td><td><input type='text' name='porcentaje_iva' id='porcentaje_iva' class='numero' value='0.00' size='10' maxlength='10' readonly></td></tr>-->
		
		<tr><td align='right'>Valor Iva:</td><td><input type='text' name='valor_iva' id='valor_iva' class='numero' value='0' size='10' maxlength='10' readonly></td></tr>
		<tr><td align='right'>Total Nota Crédito:</td><td><input type='text' name='total' id='total' class='numero' value='0' size='10' maxlength='10' readonly>  <!--onblur='valida_total();'--> </td></tr>
		<tr><td align='right'>Registrado por:</td><td><input type='text' name='registrado_por' id='registrado_por' value='<?php echo $_SESSION['Nombre'] ?>' size='50' readonly></td></tr>
		<tr><td colspan=2 align='center'><input type='button' name='grabar' id='grabar' value='Grabar' onclick='generarNotaCredito();'></td></tr>
		</table>
		<input type='hidden' name='Acc' value='elaborar_nota_credito_ok'>
		<input type='hidden' name='idFac' value='<?php echo $idFac ?>'>
		<input type='hidden' name='idNota' value='<?php echo $_GET['idNota'] ?>'>
	</div>
	
	<h4 id="loadingTitle" >Cargando</h4>
	
	<script>
	
		$("#loadingTitle").hide();
		
		let valorBruto = $("input[name='valor_bruto']");
		let baseIva = $("input[name='base_iva']");
		let valorIva = $("input[name='valor_iva']");
		let totalNota = $("input[name='total']");
		let saldo = $("input[name='saldo']");
		let itemsToSend = [];	
	
		// Trigger al modificar valor
		$(".unitaryValue").change( data => {
			
			let reference = $(data.target).parent("td").parent("tr");				
			
			console.log(data.target.value);
			
			console.log($( reference ).find(".valueMax").val());
			
			if( parseInt(data.target.value) > parseInt($( reference ).find(".valueMax").val()) )
			{
				alert("Los valores no pueden ser superiores a los definidos en la factura ");
				$(data.target).val(( reference ).find(".valueMax").val());
				return false;
			}
			
			if($( reference ).find(".addItem").is(":checked"))
			{
				addItemToNote(reference);
			}
			
			reCalculeValues(reference);			
		});
		
		//Trigger al modificar cantidad
		$(" .quantity ").change( data => {
			
			let reference = $(data.target).parent("td").parent("tr");
			
			if( parseInt(data.target.value) > parseInt($( reference ).find(".quantityMax").val()) )
			{
				alert("Los valores no pueden ser superiores a los definidos en la factura ");
				$(data.target).val(( reference ).find(".quantityMax").val());
				return false;
			}
			
			reCalculeValues(reference);
		
		});
		
		//Recalcular valores en el item
		function reCalculeValues(reference)
		{
			let quantity = $(reference).find(".quantity").val();
			let porc_iva = $(reference).find(".porc_iva").text();
			let unitaryValue = $(reference).find(".unitaryValue").val();
			let val_iva = $(reference).find(".val_iva").val();
			let val_canon = $(reference).find(".val_canon").val();


			console.log(quantity);
			console.log(porc_iva);
			console.log(unitaryValue);
			if(val_canon==0){
				$(reference).find(".totalItem").text((quantity*unitaryValue) + (quantity*unitaryValue*(porc_iva/100)) );
				
				$(reference).find(".val_iva").val((quantity*unitaryValue*(porc_iva/100)));
			}else{
				$(reference).find(".totalItem").text((quantity*unitaryValue) + parseInt(val_iva) );
			}
			console.log(unitaryValue);
						
		}
		
		//Trigger de agregar item
		$(".addItem").change( data => {
			
			if(data.target.checked){
				addItemToNote($(data.target).parent("td").parent("tr"));
			}
			else{
				addItemToNote($(data.target).parent("td").parent("tr"),false);
			}
			
		});
		
		
		//Agregar itsms a la factura
		function addItemToNote(reference,add = true)
		{			
			let quantity = $(reference).find(".quantity").val();
			let porc_iva = $(reference).find(".porc_iva").text();
			let unitaryValue = $(reference).find(".unitaryValue").val();
			let val_iva = $(reference).find(".val_iva").val();
			let val_canon = $(reference).find(".val_canon").val();
			
			let itemId = $(reference).find(".itemId");
			let itemConcepto = $(reference).find(".itemConcepto");
			console.log('canon'+val_canon);
			let totalItem;
			if(val_canon==0){
				 totalItem = parseInt( quantity *  unitaryValue ) + parseInt( quantity *  unitaryValue * (porc_iva/100) );
			}else{
				 totalItem = parseInt( quantity *  unitaryValue ) + parseInt( val_iva );
			}
			
						 
			
			let data = {
				facturad:itemId.val(),
				concepto:itemConcepto.val(),
				cantidad:quantity,
				unitario:unitaryValue,
				total:totalItem	,
				porcIva:porc_iva,
				valiva:val_iva
			}
			
			manageItemsToSend(data,add);			

			let vBruto = 0;
			let bIva = 0;
			let vIva = 0;
			let tNota = 0;
			var sld = saldo.val();
			itemsToSend.forEach( item => {
				
				vBruto = (parseInt(vBruto) + parseInt(item.cantidad*item.unitario));
				
				if(item.porcIva > 0)
				{
					bIva = parseInt(bIva) + parseInt(item.cantidad*item.unitario);
				}
				if(val_canon==0){
					vIva = parseInt(vIva) + parseInt( item.unitario*item.cantidad*(item.porcIva/100));
					tNota = parseInt(tNota) + parseInt(item.cantidad*item.unitario) + parseInt( item.unitario*item.cantidad*(item.porcIva/100)) ;
				}else{
					vIva = parseInt(vIva) + parseInt(val_iva);
					tNota = parseInt(tNota) + parseInt(item.cantidad*item.unitario) + parseInt(val_iva) ;
				}
				
				
				
				
			});	
            

			console.log(sld);

			valorBruto.val(vBruto);
			baseIva.val(bIva);
			valorIva.val(vIva);
			totalNota.val(tNota);			
			
		}
		
		//manejar lista de objetos
		function manageItemsToSend(data,add=true)
		{
			
			console.log(data);
			
			if(add)
			{
								
				index = itemsToSend.findIndex(el => {
					return el.facturad ===  data.facturad
				});
				
				//console.log(index);
				
				if(index !== -1){
					itemsToSend[index] = data;	
				}
				else{
					itemsToSend.push(data);
				}
			}
			else{
				
				console.log("to Delete");
				
				let index = itemsToSend.findIndex(el => {
					return el.facturad ===  data.facturad
				});
				
				console.log(index);
				
				if(index !== -1){
					itemsToSend.splice(index, 1);					
				}
				
			}
			
			console.log(itemsToSend);
		}
		
		//Servicio web para generar nota credito
		function generarNotaCredito()
		{
			$("#loadingTitle").show();
			
			let data = {
				Acc:"elaborar_nota_credito_ok",
				valorBruto: valorBruto.val(),
				baseIva: baseIva.val(),
				valorIva: valorIva.val(),
				totalNota: totalNota.val(),
				detalles: itemsToSend,
				idFac: $("input[name='idFac']").val(),
				idNota: $("input[name='idNota']").val(),
				fechaEmision: $("input[name='fecha_emision']").val(),
				descripcion: $("textarea[name='descripcion']").val(),
				registradoPor: $("input[name='registrado_por']").val()
			}
			
			console.log(data);
			
			$.post("zcartera.php",data).then(
				response =>{
					
					$("#loadingTitle").hide();
					
					response = JSON.parse(response);
					
					console.log(response);					
					
					if(response.status == "error")
					{
						alert("Error: "+response.desc);
					}
					
					if(response.status == "success")
					{
						alert(response.desc);
						$("#FormContent").html("<span>No a credito generada, cierre esta pantalla</span>");
					}
				
				}			
			).catch(err => { $("#loadingTitle").hide(); })
		}
		
		function closeWindow(){
			console.log("try to close window");	
			window.open('','_parent','');
			window.close();
		}
		
	
	</script>