function add_row()
{
	var newline = $('input[name="max_num_row"]').val();
	var flag = $('#add_after_this');
	var addtext = "";
	 $('input[name="nombre"]').val(8); 
	addtext += '<tr id="add_after_this">';
	addtext += "<td   align='center'>"+newline+".</td>";
	addtext += "<td contenteditable='true'></td>";
	addtext += "<td colspan=3 contenteditable='true'></td>";
	addtext += "</tr>";
	flag.after(addtext);
	flag.removeAttr('id');
	var plus = parseInt(newline)+1;
	$('input[name="max_num_row"]').val(plus);

}