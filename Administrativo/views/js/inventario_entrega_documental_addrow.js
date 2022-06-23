function add_row()
{
	var newline = $('input[name="max_num_row"]').val();
	var flag = $('#add_after_this');
	var addtext = "";
	addtext +='<tr id="add_after_this">';
	addtext +='<td contenteditable="true" class="writetd">&nbsp;</td>';
	addtext +='<td contenteditable="true" class="writetd"></td>';
	addtext +='<td contenteditable="true" class="writetd"></td>';
	addtext +='<td contenteditable="true" class="writetd"></td>';			
	addtext +='<td class="writetd" contenteditable="false" width="5%" onclick="make_visible('+newline+')" ondblclick="make_invisible('+newline+')" height="30"> <input class="date_input" id="einput'+newline+'"  type="date"></td>';
	addtext +='<td class="writetd" contenteditable="false" width="5%" onclick="make_visible('+parseInt(newline+100)+')" ondblclick="make_invisible('+parseInt(newline+100)+')" height="30"> <input class="date_input" id="einput'+parseInt(newline+100)+'"  type="date"></td>';
	addtext +='<td contenteditable="true" class="writetd"></td>';
	addtext +='<td contenteditable="true" class="writetd"></td>';
	addtext +='<td contenteditable="true" class="writetd"></td>';
	addtext +='<td contenteditable="true" class="writetd"></td>';
	addtext +='<td contenteditable="true" class="writetd"></td>';
	addtext +='<td contenteditable="true" class="writetd"></td>';
	addtext +='<td contenteditable="true" class="writetd"></td>';
	addtext +='<td contenteditable="true" class="writetd"></td>';
	addtext += "</tr>";
	flag.after(addtext);
	flag.removeAttr('id');
	var plus = parseInt(newline)+1;
	$('input[name="max_num_row"]').val(plus);

}

var charged = false; 
window.addEventListener("DOMContentLoaded",() =>{charged = true;});
var tareasEliminadas = 0;
function Quitar() {
   var div; 
    if(charged) div = $('input[name="max_num_row"]').val(); // el div
  var inputs = document.getElementsByTagName("INPUT");
    if(tareasEliminadas > inputs.length-1) {
		console.log("No tienes tareas creadas..");
    return;
    }
  tareasEliminadas++; // sumo
  inputs[inputs.length-tareasEliminadas].style.display = "none"; 
  tareasEliminadas++; // sumo
  inputs[inputs.length-tareasEliminadas].style.display = "none";
  console.log("Tarea eliminada..");
}