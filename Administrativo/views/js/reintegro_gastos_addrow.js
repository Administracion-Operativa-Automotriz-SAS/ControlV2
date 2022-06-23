function add_row()
{
	var newline = $('input[name="max_num_row"]').val();
	var flag = $('#add_after_this');
	var addtext = "";
	addtext += '<tr id="add_after_this">';
	addtext +="<td height='30' class='writetd' contenteditable='true'></td>";
	addtext +="<td height='30' class='writetd' onclick='make_visible("+newline+")' ondblclick='make_invisible("+newline+")'><input class='date_input' id='einput"+newline+"'  type='date'></td>";
	addtext +="<td height='30' class='writetd' contenteditable='true'></td>";
	addtext +="<td height='30' class='writetd' contenteditable='true'></td>";
	addtext +="<td height='30' class='writetd' contenteditable='true'></td>	";		
	addtext +="<td height='30' class='writetd' contenteditable='true'></td>";
	addtext +="<td height='30' class='writetd' contenteditable='true'></td>";
	addtext +="<td height='30' class='writetd' contenteditable='true'></td>";
	addtext +="<td height='30' class='writetd' contenteditable='true'></td>";
	addtext +="<td height='30' class='writetd' contenteditable='true'></td>	";
	addtext +="<td height='30' class='writetd' contenteditable='true'></td>	";
	addtext +="</tr> ";
	flag.after(addtext);
	flag.removeAttr('id');
	var plus = parseInt(newline)+1;
	$('input[name="max_num_row"]').val(plus);

}
