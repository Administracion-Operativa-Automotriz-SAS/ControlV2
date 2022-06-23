
var banco ;

$('.shopitem-list').on('change', function() {
		    $('.shopitem-list').not(this).prop('checked', false);  
				 var miCheckbox = document.getElementById('miElementoCheckbox');
				if( miCheckbox.checked == false ){
					 banco = 0 
				}else{
					
					 banco = 1
					
				}
				
				
			
		});
       
 var usuario;

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
        $('#exampleModal').modal({backdrop: 'static', keyboard: false});
	    var a = 4;
			if (a == 4) {
				
				$('#exampleModal').modal('show');
			} else {
				
				$('#exampleModal').modal('hide');
			}
		
		$('#buscartoken').on('submit',OBJ_INDEX.MODELS.buscarToekn);
		
    },
   
     MODELS:{
         validadorMsj(This, mensaje){
         
            if($(This).val() == ''){
              
				$('#MSGresult').show();
				$('#MSGresult').addClass('alert-danger');
				$('#MSGresult').text(mensaje); 
				$('#MSGresult').fadeOut(6000);
				$(This).next().html('<p style="color:red">Campo no ingresado </p>');
				$(This).attr('style', 'border: 1px solid red;');
               
			    return false;
            }else{
				  
				
			   $(This).next().html('<p style="color:green">Campo ingresado </p>');
			   
			   $(This).attr('style', 'border: 1px solid #33FF46;');
              
			  return true;
            }
            
        },
		

        buscarToekn:function(){


            var  error =  false;
            var formData = {
                token:$('#token').val(),
            };
			
			
			
			if(!OBJ_INDEX.MODELS.validadorMsj($("#token"), 'El campo es requerido')){error  =true; return false;
			};
            
			if(formData.token){
				
				  $.ajax({
                            url: 'https://app.aoacolombia.com/Control/operativo/controllers/tokenCallAllianz.php',
						    type:'post',
                            data: formData
								
                    }).done(function(msg) {
					 
					  usuario = JSON.parse(msg);	
					if(!usuario.nombre){

                        
						
						$('#token').next().html('<p style="color:red">El token es incorrecto  </p>');
						
				        $('#token').attr('style', 'border: 1px solid red;');
                        setTimeout('location.reload()',1000);
						
					}else{
                      
						if(usuario.ip_public === ip_public ){

                        console.log('ee'+usuario.ip_public);
                        $('#exampleModal').modal('hide');
                        $('#examplebiene').modal('show');
                        $('.nombre').text(usuario.nombre); 
	
						setTimeout($('#examplebiene').modal('hide'),9000);
						}else{
                         $('#exampleModal').modal('hide');
                         $('#ip_error').modal('show');
                          setTimeout('location.reload()',4000);

                       }
    
        
					}
						
                    }).fail(function(){
                        event.preventDefault();
                    });
					
					
			   
				
			}else{
				event.preventDefault();
       
              return false;
				
			}
           
			 event.preventDefault();
       
              return false;
            
           
        },
      



        ingresarMensajeA:function(){

           
            var  error =  false;
            var formData = {
                action:'enviar',
                usuario:usuario.nombre,
                ip:usuario.ip_public,
                clase:$('#clase').val(),
				valor:$('#valor').val(),
				banco:banco,
				aseguradora:$('#aseguradora').val(),
                numero_siniestro:$('#numero_siniestro').val(),
                ciudad_siniestro:$('#ciudad_siniestro').val(),
                ciudad_atecion:$('#ciudad_atecion').val(),
				nombre_tomador:$('#nombre_tomador').val(),
                id_tomador:$('#id_tomador').val(),
				nombre_declarante:$('#nombre_declarante').val(),
                id_declarante:$('#id_declarante').val(),
				celular:$('#celular').val(),
                telefono:$('#telefono').val(), 
				email:$('#email').val(), 
				perdida_total:$('#perdida_total').val(), 
				marca:$('#marca').val(),
				linea:$('#linea').val(),
				dias:$('#dias').val(),
                placa:$('#placa').val(),
                ciudad_atencion:$('#ciudad_atencion').val(),
				tipo:$('#tipo').val()
				
            };
			
			
			
			if(!OBJ_INDEX.MODELS.validadorMsj( $("#aseguradora") , 'El campo es requerido')){error  =true; return false;
			};
			if(!OBJ_INDEX.MODELS.validadorMsj($("#placa"), 'El campo es requerido')){error  =true; return false;
						};
            if(!OBJ_INDEX.MODELS.validadorMsj($("#numero_siniestro"), 'El campo es requerido')){ error  = true; return false;};
			
			if(!OBJ_INDEX.MODELS.validadorMsj($("#ciudad_siniestro"), 'El campo es requerido')){ error  = true; return false;};
			
            if(!OBJ_INDEX.MODELS.validadorMsj($("#ciudad_atecion"), 'El campo es requerido')){ error  = true; return false;};
			
			if(!OBJ_INDEX.MODELS.validadorMsj($("#nombre_tomador"), 'El campo es requerido')){ error  = true; return false;};
			 
			if(!OBJ_INDEX.MODELS.validadorMsj($("#id_tomador"), 'El campo es requerido')){ error  = true; return false;};
			
			if(!OBJ_INDEX.MODELS.validadorMsj($("#nombre_declarante"), 'El campo es requerido')){ error  = true; return false;};
			
			if(!OBJ_INDEX.MODELS.validadorMsj($("#id_declarante"), 'El campo es requerido')){ error  = true; return false;};
			
			if(!OBJ_INDEX.MODELS.validadorMsj($("#celular"), 'El campo es requerido')){ error  = true; return false;};
			
			if(!OBJ_INDEX.MODELS.validadorMsj($("#telefono"), 'El campo es requerido')){ error  = true; return false;};
			
            if(!OBJ_INDEX.MODELS.validadorMsj($("#email"), 'El campo es requerido')){ error  = true; return false;};
			
			if(!OBJ_INDEX.MODELS.validadorMsj($("#valor"), 'El campo es requerido')){ error  = true; return false;};
			
	        if(!OBJ_INDEX.MODELS.validadorMsj($("#dias"), 'El campo es requerido')){ error  = true; return false;};
			
            if(!OBJ_INDEX.MODELS.validadorMsj($("#radiobutton"), 'El campo es requerido')){ error  = true; return false;};
			
			if(!OBJ_INDEX.MODELS.validadorMsj($("#marca"), 'El campo es requerido')){ error  = true; return false;};
			
			if(!OBJ_INDEX.MODELS.validadorMsj($("#tipo"), 'El campo es requerido')){ error  = true; return false;};
			
			if(!OBJ_INDEX.MODELS.validadorMsj($("#linea"), 'El campo es requerido')){ error  = true; return false;};
			
            if(!OBJ_INDEX.MODELS.validadorMsj($("#clase"), 'El campo es requerido')){ error  = true; return false;};
			
			
			if(formData.numero_siniestro){
               		
				   $.ajax({
                            url: $(this).attr('action'),
						    type: $(this).attr('method'),
                            data: formData,
                    }).done(function(msg) {


                       var  id  = msg;
                        $('#id').text(id);
                        $('#MSGresult').show();
                        $('#MSGresult').removeClass('alert-danger');
                        $('#MSGresult').addClass('alert-success');
                        $('#MSGresult').text('Datos enviados');
                        $('#MSGresult').fadeOut(6000);
                        $("#masInformacionA")[0].reset();

                        $('#exito_ingreso').modal('show');
                        setTimeout('location.reload()',7000);


                       
                    }).fail(function(){
                        event.preventDefault();
                    });
                }else{
            
                
            }
           
			 event.preventDefault();
       
              return false;
            
           
        },
      

        
       },
 
 
    }