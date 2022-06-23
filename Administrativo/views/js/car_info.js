$("#placa_lists").change(function(){
	//alert(event.target.value);
	var pplaca = event.target.value;
	$.post( "Controllers/gestion_documental_helper.php?Acc=data_vehiculo",{placa:pplaca}, function( data ) {
		var vehiculo = JSON.parse(data);
		console.log(vehiculo);
		$("#ajax-marca").html(vehiculo.nom_marca);
		$("#ajax-linea").html(vehiculo.nom_linea);
		$("#ajax-color").html(vehiculo.color);
		$("#ajax-cilindraje").html(vehiculo.cilindraje);
		$("#ajax-clase").html(vehiculo.nom_clase);
		$("#ajax-modelo").html(vehiculo.modelo);
		$("#ajax-carroceria").html(vehiculo.nom_carroc);
		$("#ajax-propietario").html(vehiculo.nombre_propietario);
		$("#ajax-no-contrato").html(vehiculo.n_contrato);
		$("#ajax-poliza-seguro").html(vehiculo.poliza_seguros);
	});
});

function look_for_customer()
{
	
	if($("input[name='tipo_documentos']:checked").val() == null)
	{
		alert("Selecciona primero un tipo de documento");
		return false;
	}
	if(($("input[name='tipo_documentos']").val() == "CC" || $("input[name='tipo_documentos']").val() == "NIT") &&  $("#doc_number").text().length >=6 )
	{
		$.post( "Controllers/gestion_documental_helper.php?Acc=info_usuario",{tipo_documento:$("input[name='tipo_documentos']").val(),documento:$("#doc_number").text()}, function( data ) {
			var cliente = JSON.parse(data);
			console.log(cliente);
			$("#ajax-cliente-nombre").empty();
			$("#ajax-cliente-apellido").empty();
			$("#ajax-cliente-ciudad").empty();
			$("#ajax-cliente-direccion").empty();
			$("#ajax-cliente-telefono").empty();
			$("#ajax-cliente-email").empty();
			$("#ajax-cliente-celular").empty();
			$("#ajax-cliente-nombre").attr("contenteditable", "false");
			$("#ajax-cliente-apellido").attr("contenteditable", "false");
			$("#ajax-cliente-ciudad").attr("contenteditable", "false");
			$("#ajax-cliente-direccion").attr("contenteditable", "false");
			$("#ajax-cliente-telefono").attr("contenteditable", "false");
			$("#ajax-cliente-email").attr("contenteditable", "false");
			$("#ajax-cliente-celular").attr("contenteditable", "false");
			$("#ajax-cliente-nombre").html(cliente.nombre);
			$("#ajax-cliente-apellido").html(cliente.apellido);
			$("#ajax-cliente-ciudad").html(cliente.ciudad_nombre);
			$("#ajax-cliente-direccion").html(cliente.direccion);
			$("#ajax-cliente-telefono").html(cliente.telefono_casa);
			$("#ajax-cliente-email").html(cliente.email_e);
			$("#ajax-cliente-celular").html();
			
		});
	}	
}