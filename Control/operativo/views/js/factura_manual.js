var cont = 0;
var reembolso = 0;
function add_item()
{
  event.preventDefault();

  cont += 1;
 // console.log('contador '+cont);
  document.getElementById("cont_items").value = cont;
 
      var div = document.createElement("div");
      div.className = "form-group";
      div.id = "item"+cont;  
      var label = document.createElement("label");
      var text = document.createTextNode("Item"+cont);
      label.appendChild(text);
      label.style = 'color:#aaaaaa';
      div.appendChild(label);
      
      var input = document.createElement("input");
      input.type = "text";
      input.className = "form-control";
      input.name = "item"+cont;
      input.id = "meti"+cont;
      input.required = "required"
      div.appendChild(input);
      var label = document.createElement("label");
      var text = document.createTextNode("Cantidad");
      label.appendChild(text);
      label.style = 'color:#aaaaaa';
      div.appendChild(label);
      
      var input = document.createElement("input");
      input.type = "number";
      input.className = "form-control";
      input.value = 1;
      input.min = 1;
      input.step = 'any';
      input.required = "required"
      input.id = "cant"+cont;
      input.name = "cant"+cont;
      div.appendChild(input);
      var label = document.createElement("label");
      var text = document.createTextNode("Valor");
      label.appendChild(text);
      label.style = 'color:#aaaaaa';
      div.appendChild(label);
      
      var input = document.createElement("input");
      input.type = "number";
      input.min = "1000";
      input.max = "9999000000";
      input.className = "form-control";
      input.name = "valor unitario"+cont;
      input.id = "valor"+cont;
      input.required = "required"
      div.appendChild(input);

      var label = document.createElement("label");
      var text = document.createTextNode("Valor del iva");
      label.appendChild(text);
      label.style = 'color:#aaaaaa';
      div.appendChild(label);
	  
	  var select = document.createElement("select");
      select.className = "form-control";
	  select.name = "iva"+cont;
      select.id = "iva"+cont;
	  
	  
	  var opt = document.createElement('option');
	  opt.value = 0;
	  opt.innerHTML = "0%";
	  select.appendChild(opt);
	  
	  var opt = document.createElement('option');
	  opt.value = 16;
	  opt.innerHTML = "16%";
	  select.appendChild(opt);
	  
	  var opt = document.createElement('option');
	  opt.value = 19;
	  opt.innerHTML = "19%";
	  select.appendChild(opt);
      
	  div.appendChild(select);
	  
      /*var input = document.createElement("input");
      input.type = "checkbox";
      input.className = "form-control";
      input.name = "check"+cont;
      input.id = "check"+cont;
      input.value = "1";
      input.checked = "checked";
      input.onclick = function(){
        if(this.value==1)
        {
          this.value=0;
        }
        else{
          this.value=1; 
        }  
      }
      div.appendChild(input);*/
      
      container.appendChild(div);
      var hr = document.createElement("hr");
      help = cont+100;
      hr.id = "item"+help;
      container.appendChild(hr);
}

function remove_item()
{
  event.preventDefault();

  //var pointer = document.getElementsById("cont_items").value;
  var pointer = document.getElementById("cont_items").value;
  
  if(pointer>0)
  {
    //console.log('este es '+pointer);
    document.getElementById("item"+pointer).remove();
    pointer= 100+parseInt(pointer);
    //console.log(pointer);
    document.getElementById("item"+pointer).remove();
    cont -= 1;
    document.getElementById("cont_items").value = cont;

  }  
  
}

function description()
{
  var total_iva = 0;
  var total_noiva = 0;
  var iva = 0;
  $("#pre_desc").empty();
  $("#pre_valor").empty();
  $("#pre_mult").empty();
  $("#pre_iva").empty();
  $("#pre_noiva").empty();
  $("#pre_subtotal").empty();
  $("#pre_valoriva").empty();
  $("#pre_total").empty();

  
  let item_desc;
  let item_cantidad;
  let item_valor;
  
  let item_cantidad_valor_iva;
  
  var content = '<ul>';
  var content2 = '<ul>';
  var content3 = '<ul>';
  //console.log('consigue el valor '+$("#meti1").val());
  if(cont!=0)
  {
    for(var i =0 ; i<cont ; i++ )
    {
      
	  item_desc = $("#meti"+(parseInt(i)+1)).val();
	  item_valor = parseInt($("#valor"+(parseInt(i)+1)).val());
	  item_cantidad_valor = $("#valor"+(parseInt(i)+1)).val()*$("#cant"+(parseInt(i)+1)).val();
	  
	  content = content+'<li>'+item_desc+'</li>';
      content2 = content2+'<li> $'+item_valor+'</li>';
	  
	  if($("#iva"+(parseInt(i)+1)).val()!=0){
		
		item_cantidad_valor_iva =   parseInt(item_cantidad_valor)+item_cantidad_valor*($("#iva"+(parseInt(i)+1)).val()/100);
        
		iva = parseInt(iva)+item_cantidad_valor*($("#iva"+(parseInt(i)+1)).val()/100);
		
		content3 = content3+'<li> $'+item_cantidad_valor.format()+'</li>';
		
		//total_iva = parseInt(total_iva) + item_cantidad_valor_iva;
		
		total_iva = parseInt(total_iva) + item_cantidad_valor;
		
      }
      else{
		content3 = content3+'<li> $'+item_cantidad_valor.format()+'</li>';  
        total_noiva = parseInt(total_noiva) + parseInt(item_cantidad_valor); 
      }	

    }
  }

  content = content+'</ul>';  
  content2 = content2+'</ul>';
  content3 = content3+'</ul>';

  subtotal = parseInt(total_noiva)+parseInt(total_iva)+parseInt(reembolso);

   

  if($("#total_iva").val()!='')
  {
    //iva = parseFloat(iva) + (parseFloat($("#total_iva").val())*parseInt(total_iva))/100;
    //console.log('valor del iva '+iva); 
  }

  var total = parseFloat(subtotal)+parseFloat(iva);
  $("#pre_desc").append(content);
  $("#pre_valor").append(content2);
  $("#pre_mult").append(content3);
  $("#pre_iva").append('$'+parseInt(total_iva).format());
  $("#pre_noiva").append('$'+parseInt(total_noiva).format());
  $("#pre_subtotal").append('$'+parseInt(subtotal).format());
  $("#pre_valoriva").append('$'+parseInt(iva).format());
  $("#pre_total").append('$'+parseInt(total).format());
  
  
  $("#comments").html("Kilometraje: "+$("#kilometraje").val()+","+$("#observaciones").val());
  
}


$("select[name='tipo_documento']").change(function(){
	if($(this).val() == "ND" || $(this).val() == "DT")
	{
		$("#consecutivo_factura").parent(".form-group").show();	
		
		if($(this).val() == "ND"){
			$("#kilometraje").parent(".form-group").hide();
			$("#orden").parent(".form-group").hide();
		}
		else{
			$("#kilometraje").parent(".form-group").show();
			$("#orden").parent(".form-group").show();
		}		
	}
	else{
		$("#consecutivo_factura").parent(".form-group").hide();
	}
});

function mult_items(a,b){
  return (a*b); 
}

$(document).ready(function() {
$("#fecha_elaboracion").change(event => {
  $('#pre_inicio').empty()    
  //console.log("Estoy llegando"+`${event.target.value}`);
    var min = `${event.target.value}`;
    var input = document.getElementById("fecha_vencimiento");

    input.setAttribute("min", min);

    $('#pre_inicio').append(`${event.target.value}`);
  });

});

$(document).ready(function() {
$("#fecha_vencimiento").change(event => {
    $('#pre_final').empty();
  //console.log("Estoy llegando"+`${event.target.value}`);      
    $('#pre_final').append(`${event.target.value}`);
  });

});

Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};