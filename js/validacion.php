$(function(){
	if($('#Gest_index').length){
		OBJ_INDEX.Init();
	}
    

});

var OBJ_INDEX = {
    Init:function(){
         console.log('Modulo index');
        this.EVENTS();
        
       
        
    },
    EVENTS:function(){
       
        $('#masInformacionA').on('submit',OBJ_INDEX.MODELS.ingresarMensajeA);
     
    },
   
     MODELS:{
         validadorMsj(This, mensaje){
         
            if($(This).val() == ''){
              
                $('#MSGresult').show();
                $('#MSGresult').addClass('alert-danger');
                $('#MSGresult').text(mensaje); 
                $('#MSGresult').fadeOut(6000);
                
               
                return false;
            }else{
                $(This).removeClass('is-invalid');
                $(This).next().html('');
               // $(This).next().html('<p style="color:green">Campo ingresado </p>');
                
                //console.log($(This).val());
                return true;
            }
            
        },
        ingresarMensajeA:function(){
            
            var  error =  false;
            var formData = {
                action: 'enviar',
                nombreA :$('#nombreA').val(),
                celularA :$('#celularA').val(),
                emailA : $('#emailA').val(),
                aceptarA : $('#aceptarA').val(),
            };
            if(!OBJ_INDEX.MODELS.validadorMsj($("#nombreA"), 'El campo es requerido')){ error  = true; return false;};
            if(!OBJ_INDEX.MODELS.validadorMsj($("#celularA"), 'El campo es requerido')){ error  = true; return false;};
            if(!OBJ_INDEX.MODELS.validadorMsj($("#emailA"), 'El campo es requerido')){ error  = true; return false;};
            if($("input[type='checkbox']").is(':checked') === true)
                       $('.form-check-input').next().html('<span  style="color:black">Acepto tÃ©rminos y condiciones                       </span >')     
                    else{
                        error = true,
                        $('.form-check-label').next().addClass('text-danger');
                        $('.form-check-input').next().html('<span  style="color:red">Acepto tÃ©rminos y condiciones                        </span >')
                        return false; //Soy invalid
                    }
                     
                    var response = grecaptcha.getResponse(html_element_id);
                    if(response.length == 0){
                        captcha = false;
                        $('#MSGresult').show();
                        $('#MSGresult').addClass('alert-danger');
                        $('#MSGresult').html('<span  style="color:red">Debes selecionar no soy un robot</span >');
                        $('#MSGresult').fadeOut(15000);
                      
                    }    else {captcha = true;}

                if(captcha == true){
               		$('#enviar').attr("disabled", true);
                    $.ajax({
                            url: $(this).attr('action'),
						    type: $(this).attr('method'),
                            data: formData,
                    }).done(function() {
                        $('#MSGresult').show();
                        $('#MSGresult').removeClass('alert-danger');
                        $('#MSGresult').addClass('alert-success');
                        $('#MSGresult').text('Datos enviados');
                        $('#MSGresult').fadeOut(6000);
                        $("#masInformacionA")[0].reset();
                        window.setTimeout('location.reload()',2000);
                    }).fail(function(){
                        event.preventDefault();
                    });
                }else{
            
                $('.error').text('Por favor, prueba que no eres un robot.').animate({ 'left' : '0px' }, 500).fadeOut(8000).removeAttr('style');
            }
            event.preventDefault();
       
        
              return false;
            
           
        },
      

        
       },
 
 
    }