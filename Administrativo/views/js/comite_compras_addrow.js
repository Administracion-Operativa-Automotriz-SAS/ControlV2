function add_row()
{
	alert("here");
	var newline = $('input[name="max_num_row"]').val();
	var flag = $('#add_after_this');
	var addtext = "";
	addtext +=			"<table id='add_after_this' cellspacing='0' width='100%' align='left' border='1'>";
	addtext +=				'<tr>';
	addtext +=					"<td width='78' bgcolor='#CCCCCC'>";
	addtext +=						"<span class='Estilo10'>PROVEEDOR"+newline+"</span>";								
	addtext +=					"</td>";
	addtext +=					decode_utf8("<td width='87'><span class='Estilo4'>Razón Social y/o Nombre </span></td>");
	addtext +=					"<td colspan='4'>";
	addtext +=						'<input class="input_lists" onchange="look_proov_data(this,'+newline+')" id="prov1_lists" style="width:100%" list="proveedor1">';
	addtext +=							'<datalist id="proveedor1">';
											raiz_datos.forEach(function(element) {
												addtext += "<option>"+element+"</option>";
											});									
	addtext +=							'</datalist>'; 
	addtext +=						'</td>';
	addtext +=					  "</tr>";
	addtext +=					  "<tr>";
	addtext +=						"<td><span class='Estilo1'><strong>NIT/C.C</strong></span></td>";
	addtext +=						'<td id="prov_'+newline+'_cedula">&nbsp;</td>';
	addtext +=						"<td width='79'><span class='Estilo1'><strong>Actividad CIIU</strong></span></td>";
	addtext +=						"<td width='34' class='writetd' contenteditable='true'>&nbsp;</td>";
	addtext +=						decode_utf8("<td width='82'><span class='Estilo1'><strong>Descripción</strong></span></td>");
	addtext +=						'<td width="127" class="writetd" contenteditable="true">&nbsp;</td>';
	addtext +=					  "</tr>";
	addtext +=					  "<tr>";
	addtext +=						decode_utf8("<td><span class='Estilo4'>Dirección</span></td>");
	addtext +=						 "<td id='prov_1_direccion' colspan='2' class='writetd' contenteditable='true'>&nbsp;</td>";
	addtext +=						  "<td colspan='2'><div ><span class='Estilo4'>Ciudad</span></div></td>";
	addtext +=						  '<td id="prov_1_ciudad" class="writetd" contenteditable="true">&nbsp;</td>';
	addtext +=					  "</tr>";
	addtext +=					  "<tr>";
	addtext +=						"<td><span class='Estilo4'>Contacto</span></td>";
	addtext +=						 '<td colspan="2" id="prov_1_contacto" class="writetd" contenteditable="true">&nbsp;</td>';
	addtext +=						 "<td colspan='2'><div ><span class='Estilo4'>Tel&eacute;fono</span></div></td>";
	addtext +=						 '<td id="prov_1_telefono" class="writetd" contenteditable="true">&nbsp;</td>';
	addtext +=					  "</tr>";
	addtext +=					  "<tr>";
	addtext +=						"<td><span class='Estilo4'>Email</span></td>";
	addtext +=						'<td colspan="2" id="prov_1_email" class="writetd" contenteditable="true">&nbsp;</td>';
	addtext +=						"<td colspan='2'><div ><span class='Estilo4'>Celular</span></div></td>";
	addtext +=						'<td id="prov_1_celular" class="writetd" contenteditable="true">&nbsp;</td>';
	addtext +=					  "</tr>";
	addtext +=					  "<tr>";
	addtext +=						 decode_utf8("<td colspan='3' class='Estilo4'>Resultado calificación de evaluación proveedor</td>");
	addtext +=						 '<td colspan="3" contenteditable="true"></td>';
	addtext +=					  "</tr>";
	addtext +=					   "<tr>";
	addtext +=						  decode_utf8("<td colspan='3' class='Estilo4'>Requisión u orden de compra No.</td>");
	addtext +=						  '<td colspan="3" contenteditable="true"></td>';
	addtext +=						"</tr>";
	addtext +=					  "<tr>";
	addtext +=						 "<td colspan='3' class='Estilo4'>Comentarios</td>";
	addtext +=						 "<td colspan='3' contenteditable='true'></td>";
	addtext +=					  "</tr>";
	addtext +=					"</table>";
	//console.log(addtext);

	flag.after(addtext);
	flag.removeAttr('id');
	var plus = parseInt(newline)+1;
	$('input[name="max_num_row"]').val(plus);

}


function encode_utf8(str)
{
	return unescape(encodeURIComponent(str));
}

function decode_utf8(str)
{
	return decodeURIComponent(escape(str));
}