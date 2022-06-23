$("#placa_lists").change(function(){
	//alert(event.target.value);
	var pplaca = event.target.value;
	$.post( "/Administrativo/Controllers/gestion_documental_helper.php?Acc=data_vehiculo",{placa:pplaca}, function( data ) {				
		var vehiculo = JSON.parse(data);
		$("#vehiculo_marca").html(vehiculo.nom_marca);
		$("#vehiculo_linea").html(vehiculo.nom_linea);
		$("#vehiculo_modelo").html(vehiculo.modelo);
		$("#vehiculo_clase").html(vehiculo.nom_clase);
		$("#vehiculo_color").html(vehiculo.color);
		$("#vehiculo_cilindraje").html(vehiculo.cilindraje);
		$("#vehiculo_tipo_carroceria").html(vehiculo.nom_carroc);
		$("#vehiculo_propietario").html(vehiculo.nombre_propietario);
		$("#vehiculo_contrato").html(vehiculo.n_contrato);
		console.log(vehiculo);
	});
});

	