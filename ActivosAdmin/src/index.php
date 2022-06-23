<?php
/*código php... */
header('Content-Type: text/html; charset=utf-8');


 $HTML = "
<!doctype html>
<html>
<head>
  <script type='application/javascript'>
     var TOKEN = Array();
	  var tokenTabla= '';
	  var tokenEmail= '';
	var tokenItm ='tokenItm';
	var tokenItm1 ='tokenItm1'; 
	var tokenItm2 ='tokenItm2'; 
	var tokenItm3 ='tokenItm3';
	var tokenItm4 ='tokenItm4';		
	 var token = Array();  
	 
	 var query = window.location.href;
	 function parse_query_string(query) {
  var vars = query.split('&');
  var query_string = {};
  for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split('=');
    var key = decodeURIComponent(pair[0]);
    var value = decodeURIComponent(pair[1]);
    // If first entry with this name
    if (typeof query_string[key] === 'undefined') {
      query_string[key] = decodeURIComponent(value);
      // If second entry with this name
    } else if (typeof query_string[key] === 'string') {
      var arr = [query_string[key], decodeURIComponent(value)];
      query_string[key] = arr;
      // If third or later entry with this name
    } else {
      query_string[key].push(decodeURIComponent(value));
    }
  }
  return query_string;
}
     var qs = parse_query_string(query);
	
	
	
	if(qs.tokenPeril == 'OPERARIO FLOTAS' ){
	
		
		 var siniestro = qs.siniestro;
			console.log(qs);
	     var tokenNick = qs.tokenNick;
		 var tokenUser = qs.tokenUser; 
	     var tokenPeril = qs.tokenPeril;
         var tokenEmail = qs.tokenEmail;
		 var siniestro = qs.siniestro;
		 var declarante_ciudad = qs.id_novedad;
		 var id_novedad = qs.id_novedad;
		 alert(id_novedad);
		 var declarante_telefono = qs.declarante_telefono;
		 
		window.location ='https://app.aoacolombia.com/conVue/?#/home';
	 
	 
	 
	 
	 
	 
	}
	 
	if(qs.tokenNick == null ){
		 var tokenNick = '';
         var tokenUser = ''; 
         var tokenPeril = '';
		
	 }else{
	
		 var tokenNick = qs.tokenNick;
		 var tokenUser = qs.tokenUser; 
	     var tokenPeril = qs.tokenPeril;
         var tokenEmail = qs.tokenEmail;
		 var siniestro = qs.siniestro;
		 var declarante_ciudad = qs.declarante_ciudad;
		 var declarante_telefono = qs.declarante_telefono;
		 var declarante_email = qs.declarante_email;
		 var declarante_nombre = qs.declarante_nombre;
		 var ciudad_siniestro = qs.ciudad_siniestro;
		addProducto(tokenUser,tokenPeril,tokenNick,tokenEmail);
		window.location ='https://app.aoacolombia.com/conVue/?#/home';
	 }
	 
	
function addProducto(tokenUser,tokenPeril,tokenNick,tokenEmail){
	
var tokenUser= tokenUser;
var tokenNick =tokenNick;
var tokenPeril = tokenPeril;
var tokenEmail = tokenEmail;
 sessionStorage.setItem(tokenItm2,tokenNick); 
 sessionStorage.setItem(tokenItm1,tokenPeril); 
  sessionStorage.setItem(tokenItm1,tokenPeril); 
 sessionStorage.setItem(tokenItm,tokenUser); 
mostrarDatos(tokenItm,tokenItm1,tokenItm2,tokenItm3,tokenItm4); 

} 

function mostrarDatos(){ 


for(var i=0;i<sessionStorage.length;i++)
{  
    tokenNick=sessionStorage.getItem(tokenItm2);  
   tokenUser=sessionStorage.getItem(tokenItm);  
    tokenTabla =sessionStorage.getItem(tokenItm3);  
   tokenPeril=sessionStorage.getItem(tokenItm1);  
      tokenEmail=sessionStorage.getItem(tokenItm4);  

  }
  

  
  
}
  function limpiarVista()
  {
	  var datosDisponibles=document.getElementById('datosDisponibles'); 
  datosDisponibles.innerHTML='Limpiada vista. Los datos permanecen.';

  }
  
  
  function borrarTodo() {sessionStorage.clear(); mostrarDatos(); }


  </script> 
	<link href='//unpkg.com/bootstrap/dist/css/bootstrap.min.css' rel='stylesheet'/>
<link href='//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.css' rel='stylesheet'/>
<script src='https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js'></script>
<script src='//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.js'></script>
	
  <link rel='stylesheet' href='webroot/assets/css/bootstrap.min.css'>
  <link href='https://cdn.jsdelivr.net/npm/@mdi/font@3.x/css/materialdesignicons.min.css' rel='stylesheet'>
  <link href='https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' integrity='sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T' crossorigin='anonymous'>
<link href='https://fonts.googleapis.com/icon?family=Material+Icons'
      rel='stylesheet'>
  <link rel='stylesheet' href='webroot/assets/css/main.css'>
  <link rel='stylesheet' href='webroot/assets/css/mobile.css'>
  <link rel='stylesheet' href='webroot/assets/css/font-awesome.min.css'>
  <link rel='stylesheet' href='webroot/assets/css/fonts.css'>
  <link rel='stylesheet' href='webroot/assets/css/animate.css'>
  <link rel='stylesheet' type='text/css' href='webroot/assets/css/lightcase.css'/>
  <link rel='stylesheet' href='webroot/assets/css/owl.carousel.min.css'>
 <link rel='stylesheet' href='https://unpkg.com/element-ui/lib/theme-chalk/index.css'>
  <link rel='stylesheet' href='https://rawgit.com/lykmapipo/themify-icons/master/css/themify-icons.css

'>


    <link
      type='text/css'
      rel='stylesheet'
      href='https://unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.css'
    >


  <link rel='stylesheet' href='webroot/assets/css/owl.theme.default.min.css'>
  <link rel='stylesheet' href='https://unpkg.com/vue-form-wizard/dist/vue-form-wizard.min.css'>
</head>
<body>
	<div id='app'>	

		<router-view></router-view>
	</div>
</body>

 
  <script src='https://code.jquery.com/jquery-3.3.1.slim.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js' integrity='sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1' crossorigin='anonymous'></script>
  
  <script src='https://www.google.com/recaptcha/api.js?hl=es'></script>
  <script src='https://unpkg.com/vue-recaptcha@latest/dist/vue-recaptcha.min.js'></script>
  <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.js' defer></script>


  <script src='webroot/assets/js/jquery.js'></script>
  <script src='webroot/assets/js/animatescroll.js'></script>
  <script src='webroot/assets/smove/dist/jquery.smoove.js'></script>
  <script src='webroot/assets/js/core/jquery.min.js'></script>
  <script src='webroot/assets/js/core/popper.min.js'></script>
  <script src='webroot/assets/js/core/bootstrap-material-design.min.js'></script>
  <script src='webroot/assets/js/plugins/perfect-scrollbar.jquery.min.js'></script>
  <script src='webroot/assets/js/owl.carousel.min.js'></script>
  <script src='webroot/assets/js/lightcase.js'></script>
  <script src='webroot/assets/js/jquery.growl.js'></script>
  <script src='//unpkg.com/babel-polyfill@latest/dist/polyfill.min.js'></script>
  <script src='//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.js'></script>
 
  <script src='https://unpkg.com/vue@latest'></script>
  <script src='https://unpkg.com/vue-select@latest'></script>
  <script src='js/axios.min.js'></script>
  <script src='js/vue-resource.min.js'></script>
  <script src='https://unpkg.com/vue-multiselect@2.1.0'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/vuex/3.1.1/vuex.js' integrity='sha256-IgCwxs3F9eMC8h5HvCbhLdm25jOpUm0mFyQ/wAvj9XQ=' crossorigin='anonymous'></script>	 
  <script src='https://cdnjs.cloudflare.com/ajax/libs/vuex/3.1.1/vuex.js'></script>
  <script src='https://unpkg.com/vue-router/dist/vue-router.js'></script>
  <script src='https://unpkg.com/http-vue-loader'></script>
  <script src='js/conts.js'></script>
  <script src='src/helpers/ajax_helper.js'></script>
  <script src='js/routes.js'></script>
  <script src='js/helpers.js'></script>
  <script src='js/app.js'></script>
  <script src='webroot/assets/js/UP.js'></script>

    <!-- Load polyfills to support older browsers -->
    <script src='https://polyfill.io/v3/polyfill.min.js?features=es2015%2CIntersectionObserver'></script>

    <!-- Required scripts -->
    <script src='https://unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.js'></script>
    <style src='vue-multiselect/dist/vue-multiselect.min.css'></style>


	

	<script type='text/javascript'>
	
	

	function mostrarPassword(){
		
				var cambio = document.getElementById('txtPassword');
				if(cambio.type == 'password'){
					cambio.type = 'text';
					document.getElementById('imageid').src='https://app.aoacolombia.com/intranet/img/eye.png';
					$('.icon').removeClass('fa fa-eye-slash').addClass('fa fa-eye');
				}else{
					cambio.type = 'password';
					document.getElementById('imageid').src='https://app.aoacolombia.com/intranet/img/hide.png';
					$('.icon').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
				}
			} 
var onloadCallback = function() {
	
		$('.check-contra').click(function () {
        console.log('hola mundo');
        if ($(this).is(':checked')) {
          $('.contra-input').attr('type', 'text');
          $(this).siblings('label.box-icon').find('.far').removeClass('fa-eye').addClass('fa-eye-slash font-orange');
        } else {
          $('.contra-input').attr('type', 'password');
          $(this).siblings('label.box-icon').find('.far').removeClass('fa-eye-slash font-orange').addClass('fa-eye');
        }
	  });
	
}
</script>
   
     


<script src='https://unpkg.com/element-ui/lib/index.js'></script>
	 
<script src='https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js'></script>
<script src='https://unpkg.com/vue-form-wizard/dist/vue-form-wizard.js'></script>


  <script src='webroot/assets/demo/demo.js'></script>


<script type='text/javascript' src='https://app.aoacolombia.com/intranet/js/captcha.js'></script>
<script type='text/javascript' src='https://app.aoacolombia.com/intranet/js/validacion.js'></script>
  
</html>

";

   echo html_entity_decode($HTML, ENT_NOQUOTES, "UTF-8");


?>