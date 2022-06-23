		$( document ).ready(function() {
			//console.log(print_ver);
			if (typeof print_ver !== 'undefined'){
				console.log("triggered");
				$("#document_save_button").hide();
				$("#reset_button").hide();
				$("#addrow_button").hide();
			}
			$('input[type=radio]').each(function () {				
				if (this.hasAttribute("checked")) {
					//this.setAttribute("checked", "checked");
					if ($(this).attr("id")!= null && $(this).attr("id").indexOf('einput') >= 0){
						$(this).addClass('date_input');
					}										
				}				
			});
		});
		 
		window.onafterprint = function()
		{
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
						alert("Impresión guardada");
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
					alert("Impresión guardada");
					recover_html_for_edit();
					resize_inputs();//resize text inputs
				}			
				if(res.estado == 2){
					alert("Documento creado e impresión guardada");
					recover_html_for_edit();
					resize_inputs();//resize text inputs
				}
				});
			}
			//alert(url);
			/* 	*/		
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
						alert("No es posible guardar en este momento el documento");
						location.reload();
					}
					
					location.reload();
					
					/*$('input[type=radio]').each(function () {				
					if ($(this).is(':checked')) {
						this.removeAttribute("checked", "checked");
						$(this).addClass('date_input');
					}
					});*/
					
				});
				resize_inputs();
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
					alert("El consecutivo actual ya fue guardado por otra persona  vuelva  a intentarlo");
					currentcons = $('input[name="consecutivo"]').val();
					next = parseInt(currentcons) + parseInt("1"); 
					$('input[name="consecutivo"]').val(next);
					$("#subtitle").html("Consecutivo Interno Número "+next);
				}
				if(res.estado == 2){
					alert("No es posible guardar en este momento el documento");
					location.reload();
				}
				
				recover_html_for_edit();
				
			});
			
			
		}
		
		function prepared_html()
		{
			$('input[type=radio]').each(function () {				
				if($(this).is(':checked'))
				{
					this.setAttribute("checked", "checked");
					if ($(this).attr("id")!= null && $(this).attr("id").indexOf('einput') >= 0){
						$(this).removeClass('date_input');
					}
				}
				
				if (this.hasAttribute("checked")) {
					if(!$(this).is(':checked'))
					{
						this.removeAttribute("checked", "checked");
					}					
				}				
			});
			
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
			
			$('input[type="number"]').each(function(){
				if($(this).val()!="")
				{
					var innertext = $(this).val();					
					$(this).attr("value",innertext);
				}
				if($(this).attr("value") != null && $(this).val() == "")
				{					
					$(this).removeAttr("value");
				}
			});
			
			$('input[type="date"]').each(function(){
				if($(this).val()!="")
				{				
					var innertext = $(this).val();
					
					$(this).attr("value",innertext);
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
			
			$('input[type=checkbox]').each(function () {				
				if($(this).is(':checked'))
				{
					this.setAttribute("checked", "checked");
					if ($(this).attr("id")!= null && $(this).attr("id").indexOf('einput') >= 0){
						$(this).removeClass('date_input');
					}
				}
				
				if (this.hasAttribute("checked")) {
					if(!$(this).is(':checked'))
					{
						this.removeAttribute("checked", "checked");
					}					
				}				
			});
			
			$("select").each(function(){
				var selected_value = $(this).val();
				$("option",this).each(function(){
				  if ($(this).text() == selected_value)
				  {$(this).attr("selected","selected");}
				  else{
					$(this).removeAttr("selected");  
				  }		
				});
				//alert($(this).val());
			});
			
			$(".input_lists").each(function(){
				
				var innertext = $(this).val();					
				$(this).attr("value",innertext);
				if($(this).attr("value") != null && $(this).val() == "")
				{
					//alert($(this).attr("value"));
					$(this).removeAttr("value");
				}
			});
			
		}
		
		function recover_html_for_edit()
		{
			$('input[type=radio]').each(function () {				
				if ($(this).is(':checked')) {
					this.removeAttribute("checked", "checked");
					if ($(this).attr("id")!= null && $(this).attr("id").indexOf('einput') >= 0){
						$(this).addClass('date_input');
					}
				}
				});
				
				$('input[type=checkbox]').each(function () {				
				if ($(this).is(':checked')) {
					this.removeAttribute("checked", "checked");
					if ($(this).attr("id")!= null && $(this).attr("id").indexOf('einput') >= 0 ){
						$(this).addClass('date_input');
					}
				}
				});			
		}
		
	