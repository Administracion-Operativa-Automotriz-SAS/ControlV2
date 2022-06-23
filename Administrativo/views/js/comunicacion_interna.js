	$( document ).ready(function() {
		$(".other_codes").remove();	
		$(".print-content").remove();
		$(".pagebreak").remove();
		$(".page-numeration").remove();
		$("#first_numeration").hide();
	});	
	
	var Nconsecutivo = $("input[name='page_consecutivo']").val();	
	
	var global_last_string = null;
	var string_last_line = null;
		
		function make_visible(id)
		{
			console.log("in");
			$("#einput"+id).css('visibility', 'visible');
		}
		
		function make_invisible(id)
		{		
			console.log("invisible");
			$("#einput"+id).css('visibility', 'hidden');
		}
		
		function resizeInput()
		{
			if($(this).val().length>4)
			{				
				$(this).attr('size', ($(this).val().length-3));				
			}
			else
			{				
				$(this).attr('size', ($(this).val().length+1));				
			}			
		}

	$('input[type="text"]')
		// event handler
		.keyup(resizeInput)
		// resize on page load
		
		
	$('textarea').on('paste input', function () {
    if ($(this).outerHeight() > this.scrollHeight){
        $(this).height(1)
    }
    while ($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))){
        $(this).height($(this).height() + 1)
		}
	});
	
	window.onbeforeprint = function () {
		//alert("before printing");
        $('.print-content').remove();
		$('.pagebreak').remove();
		$('.other_codes').remove();
		$(".page-numeration").remove();	
        $('textarea').each(function () {
			var text = $(this).val();
			$(this).after('<p class="well print-content" id="pagex">' + text + '</p>');
			var lines = text.split(/\r|\r\n|\n/);
			var count = lines.length;
			//alert(count);			
			
			var divheight = $(".print-content").height(); 
			//alert("altura del div"+divheight);
			var lineheight = $(".print-content").css('line-height').replace("px","");
			//alert(Math.round(divheight/parseInt(lineheight)));
			var print_lines = Math.round(divheight/parseInt(lineheight));
			
			var fbreak = 24;
			
			if(print_lines<=fbreak)
			{
				//alert("first condition");
				innertext = string_array_range(lines,0,fbreak);
				console.log("soy inner text "+innertext);
				$(this).after('<p class="well print-content" id="page1">' + innertext + '</p>');
				//$("#page1").after('<div class="pagebreak" id="break1"></div>');
				//$("#break1").after('<p class="well print-content" id="page2">' + innertext + '</p>');
			}
			
            if(print_lines>fbreak)
			{
				//alert("second condition");
				fbreak = 25;
				innertext = string_array_range(lines,0,fbreak);
				$(this).after('<p class="well print-content" id="page0">' + innertext + '</p>');
				
				
				html = "";				
				lines_per_page = 32;
				//alert("otra opción");
				
				var flag = (print_lines - fbreak);	
				//alert("soy flag "+flag);
				//var text = $(this).val();
				pages = Math.ceil(flag/lines_per_page);				
				$("#first_numeration").show();
				$("#first_numeration").html("pagina 1 de "+parseInt(pages+1));
				//alert("pages "+pages);
				
				for(i=0;i<pages;i++)
				{
					dinapos1 = parseInt(fbreak)+ (lines_per_page * i);
					if(print_lines< dinapos1+lines_per_page)
					{
						dinapos2 = print_lines ;
					}
					else{
						dinapos2 = dinapos1 + lines_per_page;
				    }
					//alert("pos1 "+dinapos1+" pos2"+dinapos2);
					innertext = string_array_range(lines,dinapos1,dinapos2);
					$("#page"+i).after('<div class="pagebreak" id="break'+i+'"></div>');					
					$("#break"+i).after("<p align='left' class='other_codes'>"+Nconsecutivo+"</p>"+"<span class='page-numeration' style='margin-left:75%;margin-bottom:10px !important;'>Pagina "+parseInt(i+2)+" de "+parseInt(pages+1)+"</span>"+"<p class='well print-content' id=page"+(parseInt(i)+1)+">" + innertext + "</p>");
					//$("#page"+(parseInt(i)+1)).after('<div class="pagebreak" id="break'+(parseInt(i)+1)+'"></div>');
				}			
			}				
				$(".print-content").css("display", "none");
        });		
    }
	
	window.onafterprint = function(){
		$(".other_codes").remove();	
		$(".print-content").remove();
		$(".pagebreak").remove();
		$(".page-numeration").remove();
		$("#first_numeration").hide();
		
		  if ($('body').hasClass('no_prt') ) {
			  //alert("No act doc");
			  return "";
		  }
		
		if (typeof print_ver !== 'undefined'){
			//alert("No genero registro");
			return "";
		}
		prepared_html();
		
		var url = window.location.toString();
		if(url.indexOf("load_save_document")>1)
		{
			//alert("edición de documento");
			$.post( "gestion_documental_helper.php?Acc=save_print_document",
			{html:document.getElementsByTagName('html')[0].innerHTML,modo:1,consecutivo:null},function(data){
				var res = JSON.parse(data);
				if(res.estado == 1){
					alert(decodeURIComponent(escape("Impresión guardada")));
					location.reload();
				}				
			});
			
		}
		if(url.indexOf("zgendoc_funciones")>1)
		{
			//alert("creación de documento");
			$.post( "Controllers/gestion_documental_helper.php?Acc=save_print_document",
			{html:document.getElementsByTagName('html')[0].innerHTML,modo:2,consecutivo:$('input[name="consecutivo"]').val()},function(data){
				var res = JSON.parse(data);
			if(res.estado == 1){
				alert(decodeURIComponent(escape("Impresión guardada")));
				
			}			
			if(res.estado == 2){
				alert(decodeURIComponent(escape("Documento creado e impresión guardada")));
				
			}
			});
		}
	}
	
	function imprimir_pagina()
	{
		//alert("altura del text area "+$('#history').val().length);		
		if(confirm(decodeURIComponent(escape("Al imprimir se guardarán todos los datos del documento para un posterior seguimiento,¿Desea Continuar? "))))
		{
			$('body').removeClass('no_prt');
			window.print();
		}	
	}
	
	function string_array_range(str,pos1,pos2)
	{
		//pos1 = pos1+1;
		var innertext = "";	
		var returntext = "";
		//var res = str.split(" ");
		var lineheight = $("#pagex").css('line-height').replace("px","");
		var i = 0;
		console.log("soy pos2 "+pos2);
		do{
			$("#pagex").empty();
			innertext += str[i]+"\n";
			//alert(innertext);
			console.log(innertext);
			$("#pagex").append(innertext);
			var divheight = $("#pagex").height(); 
			//alert("altura del div"+divheight);			
			//alert(Math.round(divheight/parseInt(lineheight)));
			var print_lines = Math.round(divheight/parseInt(lineheight));
			if(print_lines>pos1)
			{
				if(typeof str[i] != 'undefined')
				{
					if(global_last_string==null)
					{
						returntext += str[i]+"\n";
						global_last_string = str[i];
					}
					else
					{
						if(str[i] != string_last_line)
						{
							returntext += str[i]+"\n";
							global_last_string = str[i];
						}
					}					
				}				
			}
			console.log("soy el numero de lineas "+print_lines);
			i++;			
			
		}while( print_lines < pos2);
		$("#pagex").empty();
		//alert(print_lines);
		string_last_line = global_last_string; 
		//alert(global_last_string);
		
		return returntext;
	}
	
	function save_document()
	{
		prepared_html();
		
		//return alert("test");
		
		/*$('td').each(function () {		
			$(this).css("background-color", "white");		
		});*/
		
		console.log(typeof edit);
		
		if (typeof edit !== 'undefined') {
		  //alert("updating");
		  $.post( "gestion_documental_helper.php?Acc=update_document",
			{html:document.getElementsByTagName('html')[0].innerHTML},function(data){
				var res = JSON.parse(data);
				if(res.estado == 1){
					alert("Documento guardado");
				}
				if(res.estado == 3){
					alert("El consecutivo actual ya fue guardado por otra persona , recargue y vuelva intentarlo");
				}
				if(res.estado == 2){
					alert("Existen problemas para guardar el documento");
				}
				
				location.reload();
				
				/*$('input[type=radio]').each(function () {				
				if ($(this).is(':checked')) {
					this.removeAttribute("checked", "checked");
					$(this).addClass('date_input');
				}
				});*/
				
			});
		  return "";
		  
		}
		
		//alert("save");
		//return "";
		
		$.post( "Controllers/gestion_documental_helper.php?Acc=save_document",
		{html:document.getElementsByTagName('html')[0].innerHTML,consecutivo:$('input[name="consecutivo"]').val()},function(data){
			var res = JSON.parse(data);
			if(res.estado == 1){
				alert("Documento guardado");
			}
			if(res.estado == 3){
				alert("El consecutivo actual ya fue guardado por otra persona , recargue y vuelva intentarlo");
			}
			if(res.estado == 2){
				alert("Existen problemas para guardar el documento");
			}
			
			
			
		});
		
		
	}
	
	function prepared_html()
	{			
		$('input[type="text"]').each(function(){
			if($(this).val()!="")
			{
				//alert($(this).val());
				$(this).removeAttr( "size" );
				var innertext = $(this).val();
				//alert(innertext);
				$(this).attr("value",innertext);
			}
			if($(this).attr("value") != null && $(this).val() == "")
			{
				//alert($(this).attr("value"));
				$(this).removeAttr("value");
			}
		});	

		$("textarea").each(function(){
			if($(this).val()!="")
			{
				//alert($(this).val());
				$(this).html($(this).val());	
			}
			//this.value = this.value.replace("AFFURL",producturl);
		});
	}
