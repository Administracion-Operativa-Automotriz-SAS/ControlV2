function add_row()
{
	var newline = $('input[name="max_num_row"]').val();
	var flag = $('#add_after_this');
	var addtext = "";
	addtext += '<tr id="add_after_this">';
	addtext += '<td class="writetd" contenteditable="false" width="5%" onclick="make_visible('+newline+')" ondblclick="make_invisible('+newline+')" height="30"> <input class="date_input" id="einput'+newline+'"  type="date"></td>';
	addtext += '<td class="writetd" contenteditable="true" height="30"></td>';
	addtext += '<td class="writetd" contenteditable="true" height="30"></td>';
	addtext += '<td class="writetd" contenteditable="true" height="30"></td>';
	addtext += '<td class="writetd" contenteditable="true" height="30"></td>';
	addtext += '<td class="writetd" contenteditable="true" height="30"></td>';			
	addtext += '<td contenteditable="false" height="30" width="5%" class="Estilo11">Aprobación</td>';
	addtext += '<td class="writetd" contenteditable="false" height="30"width="10%">';
	addtext += '<select>';
	addtext += '<option></option>';
	addtext += '<option>Aprobado</option>';
	addtext += '<option>Rechazado</option>';
	addtext += '</select>';
	addtext +=	'</td>';
	addtext += '<td class="writetd" contenteditable="true" height="30"></td>';
	addtext += "</tr>";
	flag.after(addtext);
	flag.removeAttr('id');
	var plus = parseInt(newline)+1;
	$('input[name="max_num_row"]').val(plus);

}