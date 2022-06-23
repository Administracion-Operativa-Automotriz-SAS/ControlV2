function add_row()
{
	var newline = $("input[name='max_num_row']").val();
	var flag = $("#add_after_this");
	flag.after("<tr id='add_after_this'><td class='writetd' onclick='make_visible("+newline+")' ondblclick='make_invisible("+newline+")'><input class='date_input' id='einput"+newline+"' type='date' style='visibility: visible;'></td><td class='writetd' contenteditable='true'></td><td class='writetd' contenteditable='true'></td><td class='writetd' contenteditable='true'></td><td class='writetd' contenteditable='true'></td><td class='writetd' contenteditable='true'></td><td class='writetd' contenteditable='true'></td><td class='writetd' contenteditable='true'></td><td class='writetd' contenteditable='true'></td><td class='writetd' contenteditable='true'></td></tr>");
	flag.removeAttr('id');
	var plus = parseInt(newline)+1;
	$("input[name='max_num_row']").val(plus);
}