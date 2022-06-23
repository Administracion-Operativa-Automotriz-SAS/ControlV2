function add_row()
{
	//var newline = $('input[name="max_num_row"]').val();
	var flag = $('#add_after_this');
	var addtext = "";
	addtext +='<tr id="add_after_this">';
	addtext +='<td class="writetd" contenteditable="true">&nbsp;</td>';
	addtext +='<td class="writetd" contenteditable="true"></td>';
	addtext +='<td class="writetd" contenteditable="true"></td>';
	addtext +='<td></td>';
	addtext += "</tr>";
	flag.after(addtext);
	flag.removeAttr('id');
	//var plus = parseInt(newline)+1;
	//$('input[name="max_num_row"]').val(plus);

}