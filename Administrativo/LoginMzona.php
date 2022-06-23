   <?php 
     
    $pass = "CLARO2020";
    $usuario = "Claro_Co";
				
	echo"	
   <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js'></script>
	<script>
	
	$(document).ready(function() {
 
     document.getElementById('formLogin').submit();
});
      

	   

function postFunction(usuario){
	
	var frame = $('iframe#TheMZoneFrame');
	var parent = $('div#parent');
	parent.remove();
	var w, h;
    h = $(window).height();
    w = $(window).width();
    frame.height(h);
    frame.width(w);
    try{
    	var cross = frame.contents().find('div.parent');
    	window.location = URL+'/logout';
    }catch(e){
    	refresh++;
    }finally {
      setTimeout(function () {
      	if(refresh>2){
      	  aceptaTerminos(usuario);
      	  refresh=0;
      	}
      }, 10000);

    }

}

function logout(){
	window.location = URL+'/logout';
}

function acepto(usuario){
	var url = URL+'/saveUser';
	var data = {'user': usuario};
	$.ajax({
		url: url,
		data: JSON.stringify(data),
		contentType: 'application/json',
		type: 'POST',
		success: function(data){
			if(data['err']){
				alert('Hubo un problema al intentar guardar su informacion');
				window.location = URL+'/logout'
			}else {
				$('#myModal').append('body').modal('hide');
			}
		}

	});
}


function aceptaTerminos(usuario){
	var url = URL+'/usuarioTerminos/'+usuario
	$.ajax({
		url: url,
		type: 'GET',
		success: function(data){
		  if(data != 'OK'){
		  	console.log(data);
		  	 $('#myModal').append('body').modal('show');
		  	 $('#btnAccept').click(function(){
		  	 	acepto(data);
		  	 });
		  }
		}
	  });
}


function login(){
	var form = document.getElementById('formLogin');
	var parent = document.getElementById('parent');
	var usuario = document.getElementById('Username').value;

		form.action = 'https://live.mzoneweb.net/mzone6.web/account/CustomLogin';
		var iframe = document.getElementById('iframe');
		parent.style.visibility = 'hidden';
		iframe.style.visibility = 'visible';
		iframe.style.top='0px';
		$('iframe#TheMZoneFrame').load(function(){
			postFunction(usuario);
		});
}

function download(item){
	var file = ''
	if(item=='terms'){
		file = 'https://s3.amazonaws.com/public-circulocorp/Legal.pdf';
	}
	if(item=='manual'){
		file = 'https://s3.amazonaws.com/public-circulocorp/Userdoc.pdf';
	}

	if(file != ''){
		window.open(file, '_blank');
	}
}

	</script>
      



				


	<form method='post' id='formLogin' onload='login()' target='TheMZoneFrame'>
				<div class='logo'></div><div class='form-group'>
				<input class='form-control' type='text' 
				id='Username' name='Username' minlength='4' 
				maxlength='25' placeholder='Usuario' value='$usuario'
				required='required'>
				</div>
				<div class='form-group'>
				<input class='form-control' type='password'  value='$pass'
				name='Password' 
				placeholder='Password' required='required'>
				</div>
				<div class='form-group'>
				<input class='form-control' type='hidden' name='LogoutUrl' 
				value='http://public-circulocorp-774028919.us-east-1.elb.amazonaws.com/logout'>
				<input class='form-control' type='hidden' name='ErrorUrl' value='http://public-circulocorp-774028919.us-east-1.elb.amazonaws.com/logout'></div>
				<div class='form-group'>
				<button class='btn btn-primary cargar btn-block' type='submit'>Entrar</button></div></form>"


   
   
   ?>