function look_proov_data(element,id)
{		
	var pprov = event.target.value;
	$.post( "Controllers/gestion_documental_helper.php?Acc=data_proveedor",{prov:pprov}, function( data )
	{
		$("#prov"+id+"cedula").empty();
		$("#prov"+id+"celular").empty();
		$("#prov"+id+"ciudad").empty();
		$("#prov"+id+"contacto").empty();
		$("#prov"+id+"direccion").empty();
		$("#prov"+id+"email").empty();
		$("#prov"+id+"telefono").empty();
		var _prov = JSON.parse(data);
		$("#prov"+id+"cedula").html(_prov.identificacion);
		$("#prov"+id+"celular").html(_prov.celular);
		$("#prov"+id+"ciudad").html(_prov.ciudad);
		$("#prov"+id+"contacto").html(_prov.celular);
		$("#prov"+id+"direccion").html(_prov.direccion);
		$("#prov"+id+"email").html(_prov.email);
		$("#prov"+id+"telefono").html(_prov.telefono);
	});
};

