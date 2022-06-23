$(function(){
	if($('#Gest_admin').length){
		OBJ_INDEX.Init();
	}
    

});

var OBJ_INDEX = {
    Init:function(){
         console.log('Modulo index');
        this.EVENTS();
       
       
       
	var recaptchas = document.querySelectorAll('div[class=g-recaptcha]');
				
		html_element_id = grecaptcha.render( "captcha", {
		'sitekey' : '6Ld-3OAUAAAAAI49_SbKJQC2qB0HOi3wsaCGsCIr',
		});
	

       
        
    },
    EVENTS:function(){
       
        $('#entrada').on('submit',OBJ_INDEX.MODELS.entrada);
     
    },
   
     MODELS:{
         validadorMsj(This, mensaje){
         
            if($(This).val() == ''){
              
                $('#MSGresult').show();
                $('#MSGresult').addClass('alert-danger');
                $('#MSGresult').text(mensaje); 
                $('#MSGresult').fadeOut(3000);
				
				$(This).next().show();
                $(This).next().addClass('alert-danger');
                $(This).next().text(mensaje); 
				 $(This).css
                $(This).next().fadeOut(4000);
                $(This).css("cssText", "background-color: #FAEBE8 !important;border-color: #dc3545;");
			  
               
                return false;
            }else{
                $(This).removeClass('is-invalid');
                $(This).next().html('');
			    $(This).css("cssText", "background-color: #E7FACC !important;border-color: #41EA1C ;");

               // $(This).next().html('<p style="color:green">Campo ingresado </p>');
                
                //console.log($(This).val());
                return true;
            }
            
        },
        entrada:function(){
            
            var  error =  false;
            var formData = {
                action: 'enviar',
                usuario :$('#usuario').val(),
                txtPassword :$('#txtPassword').val(),
                
            };
            if(!OBJ_INDEX.MODELS.validadorMsj($("#usuario"), 'El campo es requerido')){ error  = true; return false;};
            if(!OBJ_INDEX.MODELS.validadorMsj($("#txtPassword"), 'El campo es requerido')){ error  = true; return false;};
            
                     
              

                if(error == false){
               
                    
					window.setTimeout('location.reload()',2000);
					$('#MSGresult').text('Datos enviados');
					document.forma.iDU.value=document.entrada.Usuario.value;
					document.forma.cLU.value=document.entrada.Clave.value;
					document.entrada.Usuario.value='**********';
					document.entrada.Clave.value='**********';
					document.forma.submit();
                }else{
            
                $('.error').text('Por favor, prueba que no eres un robot.').animate({ 'left' : '0px' }, 500).fadeOut(8000).removeAttr('style');
            }
            event.preventDefault();
       
        
              return false;
            
           
        },
      

        
       },
 
 
    }