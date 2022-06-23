function add_row()
{
	var newline = $('input[name="max_num_row"]').val();
	//var flag = $("#add_after_this"+newline);
	var flag = $("#add_after_this20");
	console.log(flag);
	var addtext = "";
	addtext += '<tr id="add_after_this'+newline+'" >';
	//addtext += '<tr id="add_after_this20">';
	console.log(addtext);
	addtext += "<td   align='center'></td>";
	addtext += "<td contenteditable='true'></td>";
	addtext += "<td contenteditable='true'></td>";
	addtext += "<td class='writetd' onclick='make_visible("+(newline + 10)+")' ondblclick='make_invisible("+(newline + 10)+")' ><input class='date_input' id='einput"+(newline + 10)+"' type='date'></td>";
	addtext += "<td class='writetd' onclick='make_visible("+(newline + 11)+")' ondblclick='make_invisible("+(newline + 11)+")' ><input class='date_input' id='einput"+(newline + 11)+"' type='date'></td>";
	addtext += "<td><div style='text-align:center;'><input type='checkbox' name='unidad'></div></td>";
	addtext += "<td><div style='text-align:center;'><input type='checkbox' name='unidad'></div></td>";
	addtext += "<td><div style='text-align:center;'><input type='checkbox' name='unidad'></div></td>";
	addtext += "<td contenteditable='true'></td>";
	addtext += "<td><div style='text-align:center;'><input type='radio' name='digitalizado"+newline+"'></div></td>";
	addtext += "<td><div style='text-align:center;'><input type='radio' name='digitalizado"+newline+"'></div></td>";
	addtext += "<td><button  id='addrow_button'  onclick='remuve("+newline+")'>Remueve Linea</button></td>";
	addtext += "</tr>";
	flag.after(addtext);
	//flag.removeAttr('id');
	var plus = parseInt(newline)+1;
	$('input[name="max_num_row"]').val(plus);

}

/*function remuve(){
	var flag = document.getElementById('add_after_this');
	
	flag.parentNode.removeChild(flag);
	
	return false;
	
}*/

 function remuve(index){
	$("#add_after_this" + index).remove(); 
 }
 