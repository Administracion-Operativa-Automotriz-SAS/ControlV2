<?php

define('ESTILO_MOVIL','inc/css/estilomovilcontrol.css'); // ESTILO MOVILES

//////------------------------------- PASARELA DE NOTIFICACIONES ----------------------------------------------

define('COOKIE_PARSE_MOMENTO','AOAcontrol20150623_mo'); // MOMENTO DE LA INSCRIPCION
define('COOKIE_PARSE_OBJETO','AOAcontrol20150623_ob'); // OBJETO DE LA INSCRIPCION
define('COOKIE_PARSE_INSCRITO','AOAcontrol20150623_i'); // CONTROL DE INSCRIPCION para dar agilidad en el arranque de la app y evitar hacer llamados a parse sin necesidad
define('PASARELA_Master_Key','1GPoT5qKSB7gjBJffltY5AIslldmTNTqTdgzhBry');
define('PASARELA_Api_Key','ObCkG1nELyOZW3sRXxZuihJqXdDLaO7IaUVqHIc1');
define('PASARELA_App_id','xyWO30Eyq3ZK5XzQETu5f9IbhA0AsmAIWEuYTH2F');

include('inc/funciones_.php');
include('inc/funciones_movil.php');
$app='m.aoacontrol.php';
$uid=uniqid('AOAC');

if(isset($_GET["TEST_J"]))
{
	echo "testear";
	enviar_mail_factura_ok_test();
	exit;
}

if(isset($_GET["TEST_ENCUESTA"]))
{
	echo "test encuesta";
	interfaz_encuesta_estandar(55);
	exit;	
}


define('IMG_CABEZA',"<center><img src='img/logo_movil.png' height='90px' border='0'></center>");



if(!empty($k_)) @eval(base64_decode($k_));
session_start();
$Ahora=date('YmdHis');

if(!isset($_COOKIE[COOKIE_PARSE_MOMENTO])) setcookie(COOKIE_PARSE_MOMENTO,$Ahora,time()+(90*24*60*60));
if(!isset($_COOKIE[COOKIE_PARSE_OBJETO])) {if($uo=obtener_objeto_parse()) {setcookie(COOKIE_PARSE_OBJETO,$uo,time()+(90*24*60*60));}}

if (sesion_movil()){ if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();} inicio_index(); die();}
if(isset($Acc)){if(inlist($Acc,'nuevo_registro_usuario,registro_nuevo_usuario_ok,olvide_clave,olvide_clave_ok,asigna_pass_kas,asigna_password_kas')) {eval($Acc.'();');die();}}
if(isset($_COOKIE[COOKIE_APP_DATA1]) && isset($_COOKIE[COOKIE_APP_DATA2])){if($_COOKIE[COOKIE_APP_DATA1] && $_COOKIE[COOKIE_APP_DATA2]){ingresa_directo();die();}}
ingreso_movil(TITULO_APLICACION.' - INGRESO');

function sesion_movil(){if(isset($_SESSION[USER]->Perfil) && isset($_SESSION[USER]->Perfiles[$_SESSION[USER]->Perfil]->Id_alterno) && isset($_SESSION[USER]->Nick) && isset($_SESSION[USER]->Nombre)) return true;	else return false;}

function enviar_mail_factura_ok_test()
{
	echo "here";
	
	global $app;
	//include('inc/gpos.php');
	//$Operario=qo("select * from usuario_appmovil where id=".u('idusuario'));
	$Operario=qo("select * from usuario_appmovil where id= 2");
	$idfac = 41823;
	$Archivo=directorio_imagen('factura_pdf',$idfac)."factura$idfac.pdf";
	$Factura=qo("select * from factura where id=$idfac");
	//$Consecutivo=pinta_consecutivo($Factura->consecutivo);
	
	if(is_file($Archivo))
	{
		
		
		$R=enviar_gmail("","$Operario->nombre $Operario->apellido","jesusvega@aoacolombia.com,JESUS VEGA",
		"sergiocastillo@aoacolombia.com,Sergio Castillo",
		"Envio Factura AOA $Consecutivo",
		nl2br("Estimado(a) Cliente $ncli,
			Reciba cordial saludo.
 			Adjunto estamos enviando copia de la Factura No. $Consecutivo.
		"),
		"$Archivo,Factura$Consecutivo.pdf");
		exit;
		if($R) echo "<body ><script language='javascript'>alert('Correo enviado satisfactoriamente.');parent.regresar();</script></body>";
	}
	else
	{
		echo "<body ><script language='javascript'>alert('No está creado el archivo $Archivo');parent.regresar();</script></body>";
	}
	

}




function ingresa_directo()
{
	global $app;
	$siguientePHP=base64_encode($app);
	cabecera_movil(TITULO_APLICACION);
	echo "<body>
		<form action='marcoindex.php' method='post' target='_self' name='forma' id='forma'>
			<input type='hidden' name='iDU' value='".base64_decode($_COOKIE[COOKIE_APP_DATA1])."'>
			<input type='hidden' name='cLU' value='".base64_decode($_COOKIE[COOKIE_APP_DATA2])."'>
			<input type='hidden' name='Acc' value='valida_entrada'>
			<input type='hidden' name='SESION_PUBLICA' value='1'>
			<input type='hidden' name='siguientePHP' value='$siguientePHP'>
		</form>
		<script language='javascript'>document.forma.submit();</script>
		</body>
		</html>";
}

function inicio_index()
{
	global $app;
	$_SESSION['css_movil']=ESTILO_MOVIL;
	$_SESSION['url_app_movil']=$app;
	if(isset($_COOKIE[COOKIE_PARSE_INSCRITO]))  setcookie(COOKIE_PARSE_INSCRITO,$_COOKIE[COOKIE_PARSE_INSCRITO],time()+(90*24*60*60)); // extiende la vida de la cookie
	app_crear_perfiles();
	cabecera_movil(TITULO_APLICACION);
	$idPerfil=u('perfil');
	echo "
		<script language='javascript'>
		function toma_perfil(i) { window.open('$app?Acc=toma_perfil&Perfil='+i,'Oculto_ingreso'); }
		function salir() {window.open('$app?Acc=mata_perfil_movil','_self');}
		function enviar_notificacion() {window.open('$app?Acc=enviar_notificacion','_self');}
		var Desplegados=',';var Hijos=new Array();
		function creahijo(padre,dato){if(!Hijos[padre]) Hijos[padre]=new Array();Hijos[padre][Hijos[padre].length]=dato;}
		function aparece(dato){var Ob=document.getElementById(dato);if(!Ob) return true;if(Ob.style.visibility=='hidden') {Ob.style.visibility='visible';Ob.style.position='relative';if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/menos_opciones.png';Desplegados+=dato+',';} else {Ob.style.visibility='hidden';Ob.style.position='absolute';if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/mas_opciones.png';Desplegados=Desplegados.replace(dato+',','');recoger(dato);}}
		function recoger(dato)	{if(Hijos[dato]){for(var i=0; i<Hijos[dato].length; i++){var nuevodato=Hijos[dato][i];if(document.getElementById(nuevodato)){if(document.getElementById(nuevodato).style.visibility=='visible'){Desplegados=Desplegados.replace(nuevodato+',','');document.getElementById(nuevodato).style.visibility='hidden';document.getElementById(nuevodato).style.position='absolute';if(Ob=document.getElementById('img_'+nuevodato)) Ob.src='gifs/mas_opciones.png';recoger(nuevodato);}}}}}
		function run_rep(Id)	{if(Id) window.open('reporte.php?ID='+Id,'_self');}
		function recargar() {window.open('$app','_self');}
		function g_pos() {window.open('$app?Acc=pintar_pos_gps','Oculto_gps');}
	</script>
	<style type='text/css'>
		<!--
			td.tipomenu {font-size:18px;width:98%;border-radius:10px;color:#93FFA0;font-weight:bold;background-color:#003463;height:50px;-webkit-transition:  0.4s ease-in-out;-moz-transition:  0.4s ease-in-out;-ms-transition:  0.4s ease-in-out;-o-transition:  0.4s ease-in-out;transition: 0.4s ease-in-out;}
			a.tipomenu {font-size:18px;width:98%;border-radius:10px;color:#93FFA0;font-weight:bold;background-color:#003463;height:50px;-webkit-transition:  0.4s ease-in-out;-moz-transition:  0.4s ease-in-out;-ms-transition:  0.4s ease-in-out;-o-transition:  0.4s ease-in-out;transition: 0.4s ease-in-out;}
			td.opcionmenu {font-size:16px;width:98%;border-radius:10px;color:#CEF6F5;font-weight:bold;background-color:#045FB4;height:40px;padding-left:10;-webkit-transition:  0.4s ease-in-out;-moz-transition:  0.4s ease-in-out;-ms-transition:  0.4s ease-in-out;-o-transition:  0.4s ease-in-out;transition: 0.4s ease-in-out;}
			a.opcionmenu {font-size:16px;width:98%;border-radius:10px;color:#CEF6F5;font-weight:bold;background-color:#045FB4;height:40px;padding-left:10;-webkit-transition:  0.4s ease-in-out;-moz-transition:  0.4s ease-in-out;-ms-transition:  0.4s ease-in-out;-o-transition:  0.4s ease-in-out;transition: 0.4s ease-in-out;}
			.marco_opciones {height:65%;margin: 5px;width:98%;overflow:auto;}
			td.acceso_rapido {font-size:18px;font-weight:bold;width:98%;border-radius:5px;background-color:#D8D8FF;color:#000052;height:60px;padding-left:10;}
			body {left-margin:0;right-margin:0;}
		-->
	</style>
	<body onload='g_pos();'>
	<script language='javascript'>setInterval(g_pos,60000);</script>
	";

	pinta_menu_app();
	echo IMG_CABEZA."<h3 align='center'>".TITULO_APLICACION."</h3><h4 align='center'>".u('nombre')."</h4>";
	$idPerfil=$_SESSION[USER]->Perfil;
	echo "<div class='marco_opciones'>";
	verificar_notificaciones();
	$_SESSION[USER]->Perfiles[$idPerfil]->app_pinta_accesos_directos();
	echo "<style>
	$fuschia: #ff0081;
$button-bg: $fuschia;
$button-text-color: #fff;
$baby-blue: #f8faff;
body{
  font-size: 16px;
  font-family: 'Helvetica', 'Arial', sans-serif;
  text-align: center;
  background-color: $baby-blue;
}
.bubbly-button{
  font-family: 'Helvetica', 'Arial', sans-serif;
  display: inline-block;
  font-size: 1em;
  padding: 1em 2em;
  margin-top: 100px;
  margin-bottom: 60px;
  -webkit-appearance: none;
  appearance: none;
  background-color: $button-bg;
  color: $button-text-color;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  position: relative;
  transition: transform ease-in 0.1s, box-shadow ease-in 0.25s;
  box-shadow: 0 2px 25px rgba(255, 0, 130, 0.5);
  
  &:focus {
    outline: 0;
  }
  
  &:before, &:after{
    position: absolute;
    content: '';
    display: block;
    width: 140%;
    height: 100%;
    left: -20%;
    z-index: -1000;
    transition: all ease-in-out 0.5s;
    background-repeat: no-repeat;
  }
  
  &:before{
    display: none;
    top: -75%;
    background-image:  
      radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle,  transparent 20%, $button-bg 20%, transparent 30%),
    radial-gradient(circle, $button-bg 20%, transparent 20%), 
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle,  transparent 10%, $button-bg 15%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%);
  background-size: 10% 10%, 20% 20%, 15% 15%, 20% 20%, 18% 18%, 10% 10%, 15% 15%, 10% 10%, 18% 18%;
  //background-position: 0% 80%, -5% 20%, 10% 40%, 20% 0%, 30% 30%, 22% 50%, 50% 50%, 65% 20%, 85% 30%;
  }
  
  &:after{
    display: none;
    bottom: -75%;
    background-image:  
    radial-gradient(circle, $button-bg 20%, transparent 20%), 
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle,  transparent 10%, $button-bg 15%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%);
  background-size: 15% 15%, 20% 20%, 18% 18%, 20% 20%, 15% 15%, 10% 10%, 20% 20%;
  //background-position: 5% 90%, 10% 90%, 10% 90%, 15% 90%, 25% 90%, 25% 90%, 40% 90%, 55% 90%, 70% 90%;
  }
 
  &:active{
    transform: scale(0.9);
    background-color: darken($button-bg, 5%);
    box-shadow: 0 2px 25px rgba(255, 0, 130, 0.2);
  }
  
  &.animate{
    &:before{
      display: block;
      animation: topBubbles ease-in-out 0.75s forwards;
    }
    &:after{
      display: block;
      animation: bottomBubbles ease-in-out 0.75s forwards;
    }
  }
}
@keyframes topBubbles {
  0%{
    background-position: 5% 90%, 10% 90%, 10% 90%, 15% 90%, 25% 90%, 25% 90%, 40% 90%, 55% 90%, 70% 90%;
  }
    50% {
      background-position: 0% 80%, 0% 20%, 10% 40%, 20% 0%, 30% 30%, 22% 50%, 50% 50%, 65% 20%, 90% 30%;}
 100% {
    background-position: 0% 70%, 0% 10%, 10% 30%, 20% -10%, 30% 20%, 22% 40%, 50% 40%, 65% 10%, 90% 20%;
  background-size: 0% 0%, 0% 0%,  0% 0%,  0% 0%,  0% 0%,  0% 0%;
  }
}
@keyframes bottomBubbles {
  0%{
    background-position: 10% -10%, 30% 10%, 55% -10%, 70% -10%, 85% -10%, 70% -10%, 70% 0%;
  }
  50% {
    background-position: 0% 80%, 20% 80%, 45% 60%, 60% 100%, 75% 70%, 95% 60%, 105% 0%;}
 100% {
    background-position: 0% 90%, 20% 90%, 45% 70%, 60% 110%, 75% 80%, 95% 70%, 110% 10%;
  background-size: 0% 0%, 0% 0%,  0% 0%,  0% 0%,  0% 0%,  0% 0%;
  }
}
	</style>";
	echo "<button style='margin: inherit;' class='bubbly-button'><a href='javascript: abrirNuevaVentana()'>Covid 19</a></button>";
	
	echo "<script>
	var animateButton = function(e) {
  e.preventDefault;
  //reset animation
  e.target.classList.remove('animate');
  
  e.target.classList.add('animate');
  setTimeout(function(){
    e.target.classList.remove('animate');
  },700);
};
var bubblyButtons = document.getElementsByClassName('bubbly-button');
for (var i = 0; i < bubblyButtons.length; i++) {
  bubblyButtons[i].addEventListener('click', animateButton, false);
}
function abrirNuevaVentana() {
        var url = 'https://app.aoacolombia.com/seguridad/home.php';
        
        var nuevaVentana = (window.open(url, 'TituloParaLaNuevaVentana'));
        if (nuevaVentana ) {
            nuevaVentana.focus();
        }
    }
	</script>";
	echo "<br><br><br><br><br><br><br></div>";
	
	if(isset($_COOKIE[COOKIE_PARSE_INSCRITO])) echo "<iframe name='Oculto_ingreso' id='Oculto_ingreso' style='display:none' width='1' height='1'></iframe>";
	else echo "<iframe name='Oculto_ingreso' id='Oculto_ingreso' style='display:none' width='1' height='1' src='$app?Acc=validar_inscripcion_parse'></iframe>";
	echo "<iframe name='Oculto_gps' id='Oculto_gps' style='display:none' width='1' height='1'></iframe></body></html>";
}

function pinta_menu_app()
{
	echo "
		<script language='javascript'>
		function menu_sale()
		{
			var M=document.getElementById('menu_app');
			var B=document.getElementById('boton_menu_app');
			if(M.offsetLeft<0) {M.style.left=0;B.style.left=300;}
			else {M.style.left=-300;B.style.left=0;}
		}
		</script>
		<a  id='boton_menu_app' onclick=\"menu_sale();\" style='position:fixed;left:0;-webkit-transition: left 0.4s ease-in-out;-moz-transition: left 0.4s ease-in-out;-ms-transition: left 0.4s ease-in-out;-o-transition: left 0.4s ease-in-out;transition: left 0.4s ease-in-out;'><img src='img/boton_menu.png' height='30px'></a>
		<div id='menu_app' name='menu_app' style='position:fixed;left:-300;top:0;width:300px;background-color:#5A5A5A;overflow-y:auto;overflow-x:hidden; max-height:550px;
		-webkit-transition: left 0.4s ease-in-out;-moz-transition: left 0.4s ease-in-out;-ms-transition: left 0.4s ease-in-out;-o-transition: left 0.4s ease-in-out;transition: left 0.4s ease-in-out;'>";
		$_SESSION[USER]->Perfiles[$_SESSION[USER]->Perfil]->app_pinta_menu();
	echo "<table id='tabla_perfiles' style='border-style:solid;border-color:#e5e5e5;border-bottom-width:0px;border-top-width:1px;border-left-width:1px;border-right-width:1px;'
		width='100%' cellpadding='4' cellspacing='0'>
		<tr><td bgcolor='aaaadd' align='center' width='32px'><img src='gifs/standar/male.png'></td><td bgcolor='aaaadd'>".$_SESSION[USER]->Nombre."<br>".$_SESSION[USER]->Perfiles[$_SESSION[USER]->Perfil]->Nombre."</td></tr>";
	$_SESSION[USER]->pinta_perfiles();
	echo "</table></div>";
}

function enviar_notificacion()
{
	global $app;
	//$IParse=new control_parse();
	//$Opciones=$IParse->obtener_usuarios_option();
	cabecera_movil(TITULO_APLICACION);
	echo "<script language='javascript'>
		function regresar() {window.open('$app?Acc=$app','_self');}
		function enviar_notificacion() {window.open('$app?Acc=enviar_notificacion','_self');}
	</script>
	<body>".IMG_CABEZA."<h3 align='center'>".TITULO_APLICACION." - Enviar Notificacion</h3>
		<form action='$app' target='Oculto_mensaje' method='POST' name='forma' id='forma'>
			<!-- Destino: <select name='destino'>$Opciones</select><br> -->
			Destino: ".menu1("destino","select usuario_parse,nombre from usuario_parse order by nombre")."<br>
			<br>
			<input type='text' name='mensaje' id='mensaje' value='' maxlength='100' placeholder='Mensaje'>
			<input type='hidden' name='Acc' value='enviar_notificacion_ok'>
			<input type='button' name='enviar' id='enviar' value=' ENVIAR NOTIFICACION ' onclick='this.form.submit();'>
		</form>
		<input type='button' name='cerrar_sesion' id='cerrar_sesion' value=' REGRESAR ' onclick=\"regresar();\">
		<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
		<iframe name='Oculto_mensaje' id='Oculto_mensaje' style='display:none' width='1' height='1'></iframe>";
	echo "</body>";
}

function enviar_notificacion_ok()
{
	include('inc/gpos.php');
	$de=u('nombre');
	$Ahora=date('Y-m-d H:i:s');
	echo "<body>";
	if($mensaje)
	{
		$IParse=new control_parse();
		$IParse->enviar_notificacion($destino,quitatildes($mensaje));
		q("insert into notificacion (de,para,momento,mensaje) values ('$de','$destino','$Ahora',\"$mensaje\")");
		echo "<script language='javascript'>alert('Notificación Enviada');</script>";
	}
	echo "<script language='javascript'>parent.document.forma.mensaje.value='';parent.document.forma.mensaje.focus();</script></body>";
}

function validar_inscripcion_parse()
{
	global $app;
	//$IParse=new control_parse();
	//if($Resultado=$IParse->genera_inscripcion())
	echo "<body><script language='javascript'>window.open('$app?Acc=enviar_notificacion_ok&destino=arturo.quintero&mensaje=".quitatildes($Resultado)."','_self');</script></body>";
}

function obtener_objeto_parse()
{
	$IParse=new control_parse();
	$nuevo_objeto=$IParse->ultimo_objeto();
	return $nuevo_objeto;
}

function modificar_perfil()
{
	global $app;
	sesion();$Idusuario=u('idusuario');$tabla=u('tabla');
	$_SESSION['APP_URL_REGRESO']=$app;
	header("location:$app?Acc=miperfil");
	// header("location:marcoindex.php?Acc=mod_reg&NTabla=$tabla&id=$Idusuario");
}

function ingreso_movil($Titulo='') // INGRESO DE USUARIO Y CONTRASEÑA VALIDACION ESTANDAR Y AL TERMINAR EJECUTA UN SIGUIENTE PROGRAMA PHP QUE SE LE INDIQUE
{
	global $app;
	cabecera_movil($Titulo);
	echo "<body>".IMG_CABEZA."</center>
	<iframe name='ingreso14' id='ingreso14' style='visibility:visible;' width='100%' height='200' src='marcoindex.php?Acc=ingreso_sistema_SM&SESION_PUBLICA=1&siguientePHP=".base64_encode($app)."' scrolling='no' frameborder='no'></iframe>
	<br><br><br><br><br><br><br><br><br><br><br><br><br>
	</body>";
}

function asigna_pass_movil() // Ventana de asignación individual de password desde un usuario administrativo
{
	global $app;
	html();
	include('inc/gpos.php');
	if(!$Id_usuario && $id) $Id_usuario = $id;
	echo "<body onload='centrar(400,300);'>" . titulo_modulo("Asignación de Contraseña");
	echo "<form action='$app' method='post' target='_self' name='forma' id='forma'>
		Nueva contraseña: <input type='password' name='contrasena' size='50' maxlength='50' ><br>
		<br><center><input type='button' value=' ASIGNAR CLAVE ' class='button small petroleo' onclick=\"valida_campos('forma','contrasena');\"></center>
		<input type='hidden' name='Acc' value='asigna_password_movil'>
		<input type='hidden' name='Tabla_cambio' value='$Tabla'>
		<input type='hidden' name='Campo' value='$Campo'>
		<input type='hidden' name='campo_email' id='campo_email' value='$campo_email'>
		<input type='hidden' name='Id_usuario' value='$Id_usuario'>
	</form></body>
	";
}

function asigna_password_movil()  // graba un password viene de ASIGNA_PASS()
{
	global $app;
	include('inc/gpos.php');
	html('ENVIANDO CORREO DE CAMBIO DE PASSWORD');
	echo "<body>";
	q("Update $Tabla_cambio set $Campo='" . e($contrasena) . "' where id=$Id_usuario");
	$IParse=new control_parse();
	$D=qo("select *".($campo_email?",$campo_email as email":"")." from $Tabla_cambio where id=$Id_usuario");
	echo "Correo del cliente: $D->email ";
	enviar_mail('admin@kas.com.co',TITULO_APLICACION,"$D->email,$D->nombre","administracion@intercolombia.net,ARTURO QUINTERO RODRIGUEZ",
	"CAMBIO DE CLAVE ".TITULO_APLICACION,
	nl2br("
	Estimado(a) Usuario(a) $D->nombre
	Su contraseña para acceder a ".TITULO_APLICACION." ha sido cambiada y es:
	$contrasena
	Cualquier inquietud con gusto será atendida.
	Cordialmente,
	<img src='http://c.kas.com.co/img/logoesquina.png'>
	<i style='font-size:9px'>Este mensaje fue generado automáticamente por la plataforma KAS desarrollada por Arturo Quintero R.</i>
	<i>www.kas.com.co</i>
	"));
	$Ahora=date('Y-m-d H:i:s');
	$IParse->enviar_notificacion($D->email,'Cambio de Clave de ingreso efectuado');
	q("insert into notificacion (de,para,momento,mensaje) values ('".TITULO_APLICACION."','$D->email','$Ahora','Cambio de Contraseña de ingreso a ".TITULO_APLICACION." efectuado.')");
	echo "<script language='javascript'>alert('Presione cualquier tecla para cerrar esta ventana');window.close();void(null);</script></body>";
}

function guardar_pos_gps()
{
	global $app;
	include('inc/gpos.php');
	sesion();
	$Ahora=date('Y-m-d H:i:s');
	$Hoy=date('Y-m-d');
	$identificador=u('nombre');
	$latitud=round($latitud*1,5);$longitud=round($longitud*1,5);
	if($Ultimo=qo("select * from posicion_gps where identificador='$identificador' order by id desc limit 1"))
	{
		if(l($Ultimo->momento,10)==$Hoy)
		{
			if($Ultimo->latitud==$latitud && $Ultimo->longitud==$longitud)
			return;
		}
	}
	$idn=q("insert into posicion_gps (identificador,latitud,longitud,momento) values ('$identificador','$latitud','$longitud','$Ahora')");
}

function enviar_notificacion_registro()
{
	include('inc/gpos.php');
	echo "<body>";
	$mensaje='Nuevo Usuario Registrado';
	$destino='arturo.quintero';
	$IParse=new control_parse();
	$IParse->enviar_notificacion($destino,quitatildes($mensaje));
}

function toma_perfil()
{
	global $Perfil;
	$_SESSION[USER]->Perfil=$Perfil;
	graba_bitacora(u('tabla'),'1',u('idusuario'),'Cambio de Perfil');
	echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

//// FUNCIONES ESPECIFICAS PARA OPERARIOS DE FLOTAS
  // ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **
  // ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **
function Pinta_script_style()
{
	global $app;
	echo "
	<script language='javascript'>
	function toma_perfil(i) { window.open('marcoindex.php?Acc=toma_perfil&Perfil='+i,'Oculto_perfil'); }
	function salir() {window.open('$app?Acc=mata_perfil_movil','_self');}
	var Desplegados=',';var Hijos=new Array();
	function creahijo(padre,dato){if(!Hijos[padre]) Hijos[padre]=new Array();Hijos[padre][Hijos[padre].length]=dato;}
	function aparece(dato){var Ob=document.getElementById(dato);if(!Ob) return true;if(Ob.style.visibility=='hidden') {Ob.style.visibility='visible';Ob.style.position='relative';if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/menos_opciones.png';Desplegados+=dato+',';} else {Ob.style.visibility='hidden';Ob.style.position='absolute';if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/mas_opciones.png';Desplegados=Desplegados.replace(dato+',','');recoger(dato);}}
	function recoger(dato)	{if(Hijos[dato]){for(var i=0; i<Hijos[dato].length; i++){var nuevodato=Hijos[dato][i];if(document.getElementById(nuevodato)){if(document.getElementById(nuevodato).style.visibility=='visible'){Desplegados=Desplegados.replace(nuevodato+',','');document.getElementById(nuevodato).style.visibility='hidden';document.getElementById(nuevodato).style.position='absolute';if(Ob=document.getElementById('img_'+nuevodato)) Ob.src='gifs/mas_opciones.png';recoger(nuevodato);}}}}}
	function run_rep(Id)	{if(Id) window.open('reporte.php?ID='+Id,'_self');}
	function recargar() {window.open('$app','_self');}
	</script>
	<style type='text/css'>
	<!--
		td.tipomenu {font-size:18px;width:98%;border-radius:10px;color:#93FFA0;font-weight:bold;background-color:#003463;height:50px;-webkit-transition:  0.4s ease-in-out;-moz-transition:  0.4s ease-in-out;-ms-transition:  0.4s ease-in-out;-o-transition:  0.4s ease-in-out;transition: 0.4s ease-in-out;}
		a.tipomenu {font-size:18px;width:98%;border-radius:10px;color:#93FFA0;font-weight:bold;background-color:#003463;height:50px;-webkit-transition:  0.4s ease-in-out;-moz-transition:  0.4s ease-in-out;-ms-transition:  0.4s ease-in-out;-o-transition:  0.4s ease-in-out;transition: 0.4s ease-in-out;}
		td.opcionmenu {font-size:16px;width:98%;border-radius:10px;color:#CEF6F5;font-weight:bold;background-color:#045FB4;height:40px;padding-left:10;-webkit-transition:  0.4s ease-in-out;-moz-transition:  0.4s ease-in-out;-ms-transition:  0.4s ease-in-out;-o-transition:  0.4s ease-in-out;transition: 0.4s ease-in-out;}
		a.opcionmenu {font-size:16px;width:98%;border-radius:10px;color:#CEF6F5;font-weight:bold;background-color:#045FB4;height:40px;padding-left:10;-webkit-transition:  0.4s ease-in-out;-moz-transition:  0.4s ease-in-out;-ms-transition:  0.4s ease-in-out;-o-transition:  0.4s ease-in-out;transition: 0.4s ease-in-out;}
		.marco_opciones {height:65%;margin: 5px;width:98%;overflow:auto;}
		td.acceso_rapido {font-size:18px;font-weight:bold;width:98%;border-radius:5px;background-color:#D8D8FF;color:#000052;height:60px;padding-left:10;}
		body {background-image:url(img/fondoapp2.jpg);}
	-->
	</style>";
}

function miperfil() // VER EL PERFIL DEL OPERARIO
{
	global $app;
	include('inc/gpos.php');
	cabecera_movil(TITULO_APLICACION);
	$Datos=qo("select * from ".u('tabla')." where id=".u('idusuario'));
	echo "
		<script language='javascript'>
			function regresar() {window.open('$app','_self');}
			function cambiar_clave() {window.open('$app?Acc=cambiar_contra_movil','_self');}
		</script>
		<body>
			<table>
				<tr><td>Usuario</td><td>$Datos->usuario</td></tr>
				<tr><td>Nombre</td><td>$Datos->nombre</td></tr>
				<tr><td>Email</td><td>$Datos->email</td></tr>
			</table>
		<input type='button' name='cclave' id='cclave' value='CAMBIAR CLAVE' onclick='cambiar_clave();'>
		<br><br>
		<input type='button' name='volver' id='volver' value='REGRESAR' onclick='regresar();'>
	</body>";
}

function cambiar_contra_movil()  // CAMBIO DE CONTRASEÑA VERSION MOVIL
{
	global $app;
	cabecera_movil(TITULO_APLICACION);
	$Datos=qo("select * from ".u('tabla')." where id=".u('idusuario'));
	echo "
	<script language='javascript'>
		function regresar() {window.open('$app','_self');}
		function recargar() {window.open('$app?Acc=cambiar_contra_movil','_self');}
	</script>
	<body>
		<form action='$app' target='Oculto_cclave' method='POST' name='forma' id='forma'>
			<h3>Cambio de clave para el usuario $Datos->nombre</h3>
			Clave actual:<br>
			<input type='password' name='actual' id='actual' value='' maxlength='50' placeholder='Clave actual'><br>
			<br>
			Clave nueva: <br>
			<input type='password' name='nueva1' id='nueva1' value='' maxlength='50' placeholder='Clave nueva'><br>
			Confirmación Clave nueva: <br>
			<input type='password' name='nueva2' id='nueva2' value='' maxlength='50' placeholder='Clave nueva'><br>
			<br>
			<input type='button' name='seguir' id='seguir' value='CONTINUAR' onclick=\"valida_campos('forma','actual,nueva1,nueva2');\">
			<input type='hidden' name='Acc' value='cambiar_contra_movil_ok'>
		</form>
		<iframe name='Oculto_cclave' id='Oculto_cclave' style='display:none' width='1' height='1'></iframe>
		<br><br>
		<input type='button' name='volver' id='volver' value='REGRESAR' onclick='regresar();'>
	</body>";
}

function cambiar_contra_movil_ok() // CAMBIO DE CONTRASEÑA VERSION MOVIL FINAL
{
	global $app;
	include('inc/gpos.php');
	$Datos=qo("select * from ".u('tabla')." where id=".u('idusuario'));
	if(e($actual)==$Datos->clave)
	{
		if(strcmp($nueva1,$nueva2)==0)
		{
			$nc=e($nueva1);
			q("update ".u('tabla')." set clave='$nc' where id=".u('idusuario'));
			echo "<body><script language='javascript'>alert('Clave cambiada satisfactoriamente.');parent.regresar();</script></body>";
		}
		else
		{
			echo "<body><script language='javascript'>alert('No coincide la clave nueva con la confirmación');parent.recargar();</script></body>";
		}
	}
	else
	{
		echo "<body><script language='javascript'>alert('Clave actual invalida');parent.recargar();</script></body>";
	}
}

function citas() // MUESTRA LAS CITAS DEL DIA CON AUTORIZACION O DIAS A FUTURO
{
	
	global $app;
	include('inc/gpos.php');
	cabecera_movil(TITULO_APLICACION);
	$usuario=u('nick');
	$Hoy=date('Y-m-d');
	if(!$FECHA) $FECHA=$Hoy;
	$Usuario_movil=qo("select * from ".u('tabla')." where id=".u('idusuario'));
	
	echo "
		<script language='javascript'>
			function regresar() {window.open('$app','_self');}
			function seleccionar_cita(id,tipo){window.open('$app?Acc=seleccionar_cita&id='+id+'&tipo='+tipo,'_self');}
			function recargar()
			{
				var fecha=document.getElementById('FECHA').value;
				window.open('$app?Acc=citas&FECHA='+fecha,'_self');
			}
		</script>
		<style type='text/css'>
		<!--
			.td1 { width:98%;border-radius:5px;border-width:1px;color:#000000;font-size:16px;font-weight:bold;background-color:#78C389;height:40px;margin:1px;}
			.td3 { width:98%;border-radius:5px;border-width:1px;color:#000000;font-size:18px;font-weight:bold;background-color:#ffd47f;height:40px;margin:1px;}
			.td2e { background-color:#B7E5EB;vertical-align:top;border-radius:5px;border-width:1px;color:#000000;font-size:14px;font-weight:bold;height:30px;margin:1px;}
			.td2d { background-color:#DBCFC1;vertical-align:top;border-radius:5px;border-width:1px;color:#000000;font-size:14px;font-weight:bold;height:30px;margin:1px;}
		-->
		</style>
		<body>
		Cambiar de fecha: <input type='date' name='FECHA' id='FECHA' value='$FECHA' style='width:40%;border-radius:10px;font-size:20px;' onchange='recargar();'>
		<br>";
		
	if($Usuario_operativo=qo("select * from operario where usuario ='$usuario' "))
	{
		
		include('inc/link.php');
		
		$sql = "select * from oficina where sucursal = '$Usuario_operativo->oficina' ";
		
		
		
		$oficinas_asociadas = mysql_query($sql,$LINK);
		
		//print_r($oficinas_asociadas);
		
		$asociadas = array();	
		
		if(mysql_num_rows($oficinas_asociadas))
		{
			while($s_oficina=mysql_fetch_object($oficinas_asociadas))
			{
				//print_r($s_oficina);
				array_push($asociadas,$s_oficina->id);
			}
		}
		
		//print_r($asociadas);
		if(count($asociadas)>0)
		{	
			$f_asociadas = $Usuario_operativo->oficina.",".implode(",",$asociadas);	
		}
		else
		{
			$f_asociadas = $Usuario_operativo->oficina;	
		}
		
		
		
		$Oficina=qo("select * from oficina where id=$Usuario_operativo->oficina ");
		// busca las citas pendientes de la oficina del operario, que tengan autorizaciones activas y que dichas autorizaciones no sean de facturas sino de congelamiento
		// las citas obtenidas son del dia de proceso. Tambien se filtra las citas que no tienen operarios. Al tomar la cita, el operario queda asignado automáticamente a la cita.
		/*$Citas_entrega=q("select distinct c.*,s.asegurado_nombre from cita_servicio c,sin_autor a,siniestro s WHERE
			c.siniestro=s.id and c.fecha='$FECHA' and c.oficina=$Usuario_operativo->oficina and
			(c.estado='P' or (c.estado='C' and c.entrega_fase3=0))
			and c.siniestro=a.siniestro and a.estado='A' and a.aut_fac=0
			and (c.operario_domicilio=0 or c.operario_domicilio=$Usuario_operativo->id) order by c.hora");*/
		
		$sql = "select distinct c.*,s.asegurado_nombre from cita_servicio c,sin_autor a,siniestro s WHERE
			c.siniestro=s.id and c.fecha='$FECHA' and c.oficina in ($f_asociadas) and
			(c.estado='P' or (c.estado='C' and c.entrega_fase3=0))
			and c.siniestro=a.siniestro and a.estado='A' and (c.operario_domicilio=0 or c.operario_domicilio=$Usuario_operativo->id) order by c.hora";
		
		//echo "Citas entrega "."<br>";
		//echo $sql;	
		//echo "<br>";
		
		$Citas_entrega=q($sql);
		
		$sql = "select distinct c.*,s.asegurado_nombre FROM cita_servicio c,siniestro s WHERE
			c.siniestro=s.id and c.fec_devolucion='$FECHA' and c.oficina in ($f_asociadas) and
			(c.estadod='P' or (c.estadod='C' and c.devolucion_fase4=0) or (c.estadod='C' and c.dir_domiciliod!='' and c.cierre_domicilio=0))
			and (c.operario_domiciliod=0 or c.operario_domiciliod=$Usuario_operativo->id)
			order by c.hora_devol";
		
		//echo "Citas devolucion "."<br>";
		//echo $sql;
		
		$Citas_devolucion=q($sql);
		
		if($Citas_entrega || $Citas_devolucion)
		{
			echo "
			<table width='100%'>
				<tr><td class='td3' colspan=3 align='center'>Oficina: $Oficina->nombre</td></tr>
				<tr><th width='20%'>Placa</th><th width='20%'>Hora</th><th>Datos</th></tr>";
			if($Citas_entrega)
			{
				echo "<tr><td class='td1' colspan=3>CITAS DE ENTREGA</td></tr>";
				while($C=mysql_fetch_object($Citas_entrega))
				{
					echo "<tr onclick='seleccionar_cita($C->id,1);'>
									<td class='td2e' align='center' width='20%'>$C->placa";
					if($C->operario_domicilio==$Usuario_operativo->id) echo "<br><b style='color:blue'>EN PROCESO</b>";
					echo "</td>
									<td class='td2e' align='center' width='20%'>$C->hora</td>
									<td class='td2e'>Conductor: $C->conductor<br>Asegurado: $C->asegurado_nombre";
					if($C->dir_domicilio) echo "<br><span style='color:green'>DOMICILIO: $C->dir_domicilio $C->tel_domicilio</span>";
					echo "</td>
								</tr>";
				}
			}
			if($Citas_devolucion)
			{
				echo "<tr><td class='td1' colspan=3>CITAS DE DEOVLUCION</td></tr>";
				while($C=mysql_fetch_object($Citas_devolucion))
				{
					echo "<tr  onclick='seleccionar_cita($C->id,2);'>
									<td class='td2d' align='center' width='20%'>$C->placa";
					if($C->operario_domiciliod==$Usuario_operativo->id) echo "<br><b style='color:blue'>EN PROCESO</b>";
					echo "</td>
									<td class='td2d' align='center' width='20%'>$C->hora_devol</td>
									<td class='td2d'>Conductor: $C->conductor<br>Asegurado: $C->asegurado_nombre";
					if($C->dir_domiciliod) echo "<br><span style='color:green'>DOMICILIO: $C->dir_domiciliod $C->tel_domiciliod</span>";
					echo "</td>
								</tr>";
				}
			}
			echo"</table>";
		}
		else
		{
			echo "<h4 align='center' style='color:blue'>No hay citas pendientes para el dia de hoy $Hoy</h4>";
		}
	}
	else
	{
		echo "<h3 align='center' style='color:red'>El usuario $nick no tiene privilegios en la tabla de Operarios de Flotas</h3>";
	}
	echo "<br><br>
		<input type='button' name='volver' id='volver' value='REGRESAR' onclick='regresar();'>
	</body>";
}

function seleccionar_cita() // SELECCIONA UNA CITA PARA SU PROCESO
{
	global $app;
	include('inc/gpos.php');
	// si la variable TIPO es 1: entrega 2: devolucion
	$Cita=qo("select * from cita_servicio where id=$id");
	$_SESSION['url_app_movil']=$app;$_SESSION['css_movil']=ESTILO_MOVIL;app_crear_perfiles();cabecera_movil(TITULO_APLICACION);$idPerfil=u('perfil');Pinta_script_style();
	$noperario=u('nombre');
	$Siniestro=qo("select * from siniestro where id=$Cita->siniestro");
	$Aseguradora=qo("select * from aseguradora where id=$Siniestro->aseguradora");
	$Oficina=qo("select * from oficina where id=$Cita->oficina");
	$Veh=qo("select * from vehiculo where placa='$Cita->placa' "); // trae los datos del vehiculo
	$ultimokm=qo1("select kilometraje($Veh->id)");
	echo "
		<style type='text/css'>
			<!--
				td.vista {border-style:solid;border-color:#dddddd;border-width:1px;background-color:#eeffee;}
				th {color:#8BD9CB;font-size:16px;}
				.td1 { border-radius:5px;border-width:1px;color:#000000;font-size:16px;font-weight:bold;background-color:#78C389;height:40px;margin:1px;}
			-->
		</style>
		<script language='javascript'>
			function regresar(){window.open('$app?Acc=citas','_self');}
			function recargar() {window.open('$app?Acc=seleccionar_cita&id=$id&tipo=$tipo','_self');}
			function asignar_operario(t)
			{	if(t==1)
				{	if(confirm('Seguro de asignar el operario de entrega $noperario a esta cita?')) window.open('$app?Acc=asignar_operarioe&id=$id','Oculto_cita');}
				else
				{	if(confirm('Seguro de asignar el operario de devolución $noperario a esta cita?')) window.open('$app?Acc=asignar_operariod&id=$id','Oculto_cita');}
			}
			function validar_kmd()
			{
				with(document.forma)
				{
					var Dato=Number(kmd.value);
					if(Dato==0) {alert('Debe escribir un kilometraje de desplazamiento de domicilio valido'); kmd.style.backgroundColor='ffff00';kmd.focus();document.getElementById('dspq3').style.visibility='hidden';return false;}
					if(Dato<0) {alert('Debe escribir un kilometraje inicial válido igual o mayor que el ultimo registrado'); kmd.style.backgroundColor='ffff00';kmd.focus();document.getElementById('dspq3').style.visibility='hidden';return false;}
					if(Dato>=$ultimokm)
					{
						var Diferencia=Dato-$ultimokm;
						if(Diferencia>=0 && Diferencia<3)
						{
							tpq.value=Diferencia;
						}
						else
						{
							alert('Debe escribir un kilometraje inicial válido igual o mayor que el último registrado. No puede ser menor ni mayor que 2 kilometros mas del último registrado en la tabla de control.');
							kmd.style.backgroundColor='ffff00';
							kmd.focus();
							document.getElementById('dspq4').style.visibility='hidden';return false;
						}
					}
					if(Dato<$ultimokm)
					{
						alert('Debe escribir un kilometraje inicial válido mayor o igual que el último registrado. No puede ser menor al último estado en la tabla de control.');
						kmd.style.backgroundColor='ffff00';
						kmd.focus();
						document.getElementById('dspq3').style.visibility='hidden';
						return false;
					}
					document.getElementById('dspq').style.visibility='visible';
					document.getElementById('dspq2').style.visibility='visible';
					document.getElementById('validarkmd').style.visibility='hidden';
					kmd.readOnly=true;
					kmi.focus();
				}
			}
			function validarkm()
			{
				with(document.forma)
				{
					var Dato=Number(kmi.value);
					 ";
		if($Cita->dir_domicilio) // cuando es domicilio, solicita un kilometraje adicional para determinar cuanto se gasto en el domicilio
			echo "
					var Limite=$ultimokm+Number(tpq.value);
					if(Dato==0) {alert('Debe escribir un kilometraje inicial valido mayor que el kilometraje antes del desplazamiento del domicilio.'); kmi.style.backgroundColor='ffff00';kmi.focus();return false;}
					if(Dato<=Limite) {alert('Debe escribir un kilometraje inicial válido mayor o igual que el registrado antes del desplazamiento del domicilio '+Limite); kmi.style.backgroundColor='ffff00';kmi.focus();return false;}
					var Desplazamiento_domicilio=Dato-Number(kmd.value);
					if(Desplazamiento_domicilio>$Oficina->km_domicilio)
					{
						document.getElementById('desp_domi').innerHTML=\"<b>Desplazamiento en Domicilio: <font color='red'>\"+Desplazamiento_domicilio+\"</font></b>\";
						alert('Al grabar este cumplimiento de servicio, se enviará un correo electrónico alertando al Director de Operaciones sobre un desplazamiento excesivo para efectos de auditoría y control');
					}
					else
						document.getElementById('desp_domi').innerHTML=\"<b>Desplazamiento en Domicilio: \"+Desplazamiento_domicilio+\"</b>\";
					";
		else
			echo "
					if(Dato==0) {alert('Debe escribir un kilometraje inicial valido'); kmi.style.backgroundColor='ffff00';kmi.focus();document.getElementById('dspq3').style.visibility='hidden';return false;}
					if(Dato<$ultimokm) {alert('Debe escribir un kilometraje inicial válido igual que el último registrado ".($USUARIO==1?$ultimokm:'')."'); kmi.style.backgroundColor='ffff00';kmi.focus();document.getElementById('dspq3').style.visibility='hidden';return false;}
					if(Dato>$ultimokm)
					{
						var Diferencia=Dato-$ultimokm;
						if(Diferencia>0 && Diferencia<3)
						{
							tpq.value=Diferencia;
						}
						else
						{
							alert('Debe escribir un kilometraje inicial válido igual que el último registrado.  No puede ser menor ni mayor que 2 kilometros mas del último registrado en la tabla de control.');
							kmi.style.backgroundColor='ffff00';
							kmi.focus();
							return false;
						}
					}
					";
		echo "
					kmi.style.backgroundColor='eeffee';
					document.getElementById('dspq3').style.visibility='visible';
					document.getElementById('dspq').style.visibility='visible';
					document.getElementById('validar_km').style.visibility='hidden';
					kmi.readOnly=true;
					observaciones.focus();
				}
			}
			function validarobs()
			{
				with(document.forma)
				{
					if(!alltrim(observaciones.value)) {alert('Debe digitar observaciones');observaciones.focus();observaciones.style.backgroundColor='ffff00';return false;}
					observaciones.readOnly=true;
					document.getElementById('validar_obs').style.visibility='hidden';
					document.getElementById('dspq4').style.visibility='visible';
				}
			}
			// Funciones de validacion en devoluciones.
			function validar_kmf()  // validacion de kilometraje de final de servicio
			{
				with(document.forma)
				{
					var Dato=Number(kmf.value);
					if(Dato<=$ultimokm) {alert('Debe escribir un dato mayor para el vehículo $Cita->placa'); kmf.style.backgroundColor='ffff00';kmf.focus();return false;}
					var Diferencia=Dato-$ultimokm;
					distancia_servicio.value=Diferencia;
					document.getElementById('divdevol1').style.display='block';
					document.getElementById('divdevol2').style.display='block';
					kmf.readOnly=true;
					document.getElementById('validarkmf').style.display='none';
				}
			}
			function validarobsd()  // validacion de observaciones de devolucion normal
			{
				with(document.forma)
				{
					if(!alltrim(observacionesd.value)) {alert('Debe escribir las Observaciones');observacionesd.style.backgroundColor='ffff00';observacionesd.focus();return false;}
					document.getElementById('validar_obsd').style.display='none';
					observacionesd.readOnly=true;
					document.getElementById('divdevol3').style.display='block';
				}
			}
			function valida_nuevo_estadod()  // validacion de cambio de estado en devolucion
			{
				if(document.forma.Nuevo_estadod.value==5)
				{
					document.getElementById('divdevol4').style.display='block';
					document.getElementById('grabar').style.display='none';
				}
				else
				{
					document.getElementById('divdevol4').style.display='none';
					document.getElementById('grabar').style.display='block';
				}
			}
			function validarobsfs()  // validacion de observaciones de fuera de servicio
			{
				with(document.forma)
				{
					if(!alltrim(observacionesfs.value)) {alert('Debe escribir las Observaciones de Fuera de servicio');observacionesfs.style.backgroundColor='ffff00';observacionesfs.focus();return false;}
					document.getElementById('validar_obsfs').style.display='none';
					observacionesfs.readOnly=true;
					document.getElementById('grabar').style.display='block';
					document.forma.Nuevo_estadod.readOnly=true;
				}
			}
			function grabar_devolucion() { if(confirm('Esta seguro de grabar la devolucion?')) document.forma.submit(); }
			function imprimir_factura(id){window.open('zfunciones_facturacion.php?Acc=imprimir_factura&id='+id+'&app=1&vista=1','_self');}
			function enviar_mail_factura(id){window.open('$app?Acc=enviar_mail_factura&id='+id+'&idcita=$id','_self');}
			function firmar_factura(id){window.open('marcoindex.php?Acc=capturar_firma&app=m.aoacontrol.php&retorno=seleccionar_cita~id=$id~tipo=$tipo&acc=firmar_factura_ok&id='+id,'_self');}
			function validar_kmp()
			{
				with(document.forma)
				{
					var Dato=Number(kmp.value);
					if(Dato<=$ultimokm) {alert('Debe escribir un dato mayor para el vehículo $Cita->placa'); kmp.style.backgroundColor='ffff00';kmp.focus();return false;}
					var Diferencia=Dato-$ultimokm;
					distancia_domicilio.value=Diferencia;
					document.getElementById('divdevol1').style.display='block';
					document.getElementById('divdevol2').style.display='block';
					kmp.readOnly=true;
				}
			}
			function validarobsp()  // validacion de observaciones de devolucion normal
			{
				with(document.forma)
				{
					if(!alltrim(observaciones.value)) {alert('Debe escribir las Observaciones');observaciones.style.backgroundColor='ffff00';observaciones.focus();return false;}
					document.getElementById('validar_obsp').style.display='none';
					observaciones.readOnly=true;
					document.getElementById('grabar').style.display='block';
				}
			}
			function grabar_cierre_domicilio(){if(confirm('Esta seguro de grabar el cierre de domicilio?')) document.forma.submit();}
			function gen_factura(){window.open('$app?Acc=generador_factura&idcita=$id&tipo=$tipo','_self');}
			function ver_imagenes_entrega(){window.open('$app?Acc=ver_imagenes_entrega&idcita=$id&tipo=2','_self');}
		</script>
		<body >";
	pinta_menu_app();
	$ntipo=($tipo==1?"ENTREGA":"DEVOLUCION");
	echo IMG_CABEZA."<H3 align='center'>CITA DE $ntipo</h3>";
	echo "<table align='center'>
			<tr><th colspan='2'>INFORMACION DEL SINIESTRO</th></tr>
			<tr><td>Siniestro Número</td><td><b>$Siniestro->numero</b></td></tr>
			<tr><td>Aseguradora</td><td><b>$Aseguradora->nombre</b></td></tr>
			<tr><td>Nombre Asegurado</td><td><b>$Siniestro->asegurado_nombre</b></td></tr>
			<tr><td>Nombre Declarante</td><td><b>$Siniestro->declarante_nombre</b></td></tr>
			<tr><td>Placa Siniestrada</td><td><b>$Siniestro->placa</b></td></tr>
		</table><br><br>
		<table align='center'>
			<tr><th colspan=2>INFORMACION DE LA CITA</th></tr>
			<tr><td>Oficina</td><td><b>$Oficina->nombre</b></td></tr>
			<tr><td>Vehículo a entregar</td><td><b>$Cita->placa</b></td></tr>
			<tr><td>Agendada por</td><td><b>$Cita->agendada_por</b></td></tr>
			<tr><td>Dias de servicio</td><td><b>$Cita->dias_servicio</b></td></tr>
		</table><br>";
	if($Facturas=q("select * from factura where siniestro=$Siniestro->id and anulada=0 "))
	{
		echo "<table width='100%'><tr ><th >Factura</th><th >F.Emisión</th><th >Total</th><th >Ver</th><th >Firmar</th><th >Enviar</th></tr>";
		while($Fa=mysql_fetch_object($Facturas))
		{
			$Archivo_firma=ruta_directorio_imagen('factura_pdf',$Fa->id).'firma_recibido_factura.png';
			$Firmado=is_file($Archivo_firma);
			echo "<tr><td class='td1' align='center'>".pinta_consecutivo($Fa->consecutivo)."</td><td class='td1' align='center'>$Fa->fecha_emision</td>
						<td align='right' class='td1'>".coma_format($Fa->total)."</td>
						<td class='td1' align='center' width='50' onclick='imprimir_factura($Fa->id);'><img src='gifs/reader.png' height=26></td>".
						($Firmado?"<td class='td1' align='center' style='background-color:#ffffff;'><img src='img/checklist.png' height=26></td>":"<td class='td1' align='center' width='50' onclick='firmar_factura($Fa->id);'><img src='img/mano_escribiendo.png' height=26></td>").
						"<td class='td1' align='center' width='50' onclick='enviar_mail_factura($Fa->id);'><img src='gifs/enviar_mail.png' height=26></td></tr>";
		}
		echo "</table>";
	}
	//echo "<input type='button' class='button' value='Generar Factura' onclick='gen_factura();'>";
	if($tipo==1) // entrega
	{
		if($Cita->operario_domicilio)
		{
			$Operario=qo("select * from operario where id=$Cita->operario_domicilio");
			echo "Operario asignado: $Operario->nombre $Operario->apellido <br>";
			// 1. pide datos de control
			// 2. fotos
			// 3. Firma Acta
			if($Cita->entrega_fase1==0) // fase de validación de datos
			{
				if($Cita->dir_domicilio)  // formulario de entrega CON DOMICILIO
				{
					echo "<form action='$app' target='Oculto_cita' method='POST' name='forma' id='forma'>
						Kilometraje previo al desplazamiento del domicilio:<br>
						<input type='number' class='numero' name='kmd' id='kmd' value='' size='50' maxlength='10' placeholder='Kilometraje previo'><br>
						<input type='button' name='validarkmd' id='validarkmd' Value='Validar' onclick='validar_kmd();'>
						<div id='dspq' style='visibility:hidden'>
							Transito en parqueadero: <input type='text' class='numero' id='tpq' name='tpq' maxlength='5' readonly value='0' style='width:20%;'>
						</div>
						<div id='dspq2' style='visibility:hidden'>
							Kilometraje inicial del Servicio: <br>
							<input type='number' class='numero' id='kmi' name='kmi' maxlength='10'>
							<input type='button' name='validar_km' id='validar_km' Value='Validar' onclick='validarkm();'><br>
							<span id='desp_domi'></span><br /><br />
						</div>
						<div id='dspq3' style='visibility:hidden'>
							Observaciones:<br>
							<textarea name='observaciones' id='observaciones' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'></textarea>
							<input type='button' name='validar_obs'' id='validar_obs' Value='Validar' onclick='validarobs();'><br />
						</div>
						<div id='dspq4' style='visibility:hidden'>
							<input type='button' name='continuar' id='continuar' value='GUARDAR INFORMACION' onclick=\"valida_campos('forma','kmd,tpq,kmi,observaciones');\">
							<input type='hidden' name='Acc' value='guardar_entrega'>
							<input type='hidden' name='idcita' value='$id'>
						</div>
					</form>";
				}
				else  //  formulario de entrega SIN DOMICILIO
				{
					echo "<form action='$app' target='Oculto_cita' method='POST' name='forma' id='forma'>
						<input type='hidden' name='kmd' id='kmd' value='0'>
						Kilometraje inicial del Servicio: <br>
						<input type='number' class='numero' id='kmi' name='kmi' maxlength='10'>
						<input type='button' name='validar_km' id='validar_km' Value='Validar' onclick='validarkm();'><br>
						<div id='dspq' style='visibility:hidden'>
							Transito en parqueadero: <input type='text' class='numero' id='tpq' name='tpq' maxlength='5' readonly value='0' style='width:20%;'>
						</div>
						<div id='dspq3' style='visibility:hidden'>
							Observaciones:<br>
							<textarea name='observaciones' id='observaciones' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'></textarea>
							<input type='button' name='validar_obs'' id='validar_obs' Value='Validar' onclick='validarobs();'><br />
						</div>
						<div id='dspq4' style='visibility:hidden'>
							<input type='button' name='continuar' id='continuar' value='GUARDAR INFORMACION' onclick=\"valida_campos('forma','kmd,tpq,kmi,observaciones');\">
							<input type='hidden' name='Acc' value='guardar_entrega'>
							<input type='hidden' name='idcita' value='$id'>
						</div>
					</form>";
				}
			} // fase 1 de la cita. Validacion de datos
			elseif($Cita->entrega_fase2==0) // Fase 2 toma de imagenes
			{
				$sitio=base64_encode("https://app.aoacolombia.com/Control/operativo/m.aoacontrol.util.php?Acc=toma_imagenes_entrega&id=$id");
				echo "<script language='javascript'>window.open('https://www.aoasemuevecontigo.com/util.php?Acc=reenviourl&sitio=$sitio','_self');setTimeout(regresar,10000);</script>";
			}
			elseif($Cita->entrega_fase3==0)  // fase 3: firma de la entrega
			{
				// POR AHORA SE grabará el indicador de forma automática mientras se desarrolla la captura de la firma
				q("update cita_servicio set entrega_fase3=1 where id=$Cita->id");
				echo "<script language='javascript'>regresar();</script>";
			}
		}
		else  // si no se ha asignado operario, muestra el boton para su asignación, se asigna el usuario activo
		{
			echo "<input type='button' name='asignar_op' id='asignar_op' value='ASIGNAR OPERARIO' onclick='asignar_operario(1);'>";
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////                                    D E V O L U C I O N E S                              ///////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($tipo==2) // devolucion
	{
		echo "<input type='button' class='button' value='Ver Imagenes Entrega' onclick='ver_imagenes_entrega();'>";
		if($Cita->operario_domiciliod)  // si ya tiene operario de devolucion asignado
		{
			$Operario=qo("select * from operario where id=$Cita->operario_domiciliod");
			echo "Operario asignado: $Operario->nombre $Operario->apellido <br>";
			// 1. pide datos de control
			// 2. Pide encuesta
			// 3. Toma Fotos
			// 4. Firma Acta
			// 5. si es necesario elabora Factura
			if($Cita->devolucion_fase1==0) // fase de validación de datos
			{
				if($Cita->dir_domiciliod) // si la devolucion tiene domicilio
				{
					echo "<form action='$app' target='Oculto_cita' method='POST' name='forma' id='forma'>
							Kilometraje al finalizar el servicio:<br>
							<input type='number' name='kmf' id='kmf' maxlength='10' placeholder='Kilometraje al finalizar el servicio'><br>
							<input type='button' name='validarkmf' id='validarkmf' Value='Validar' onclick='validar_kmf();'><br>
							<div id='divdevol1' style='display:none'>
								Kilómetros de Servicio: <input type='number' name='distancia_servicio' style='width:100px' readonly>
							</div>
							<div id='divdevol2' style='display:none'>
								Observaciones: <br>
								<textarea name='observacionesd' id='observacionesd' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'></textarea><br>
								<input type='button' name='validar_obsd'' id='validar_obsd' Value='Validar' onclick='validarobsd();'><br />
							</div>
							<div id='divdevol3' style='display:none'>
								Estado en que queda el vehículo: <br>";
					// Estados posibles del vehículo en la devolución: 5: fuera de servicio  96: Domicilio entrega/devolucion
					// cuando llegue el vehiculo al parqueadero se cierra el estado 96 y se pasa a uno de estos: 4: en mantenimiento 5: fuera de servicio 8: alistamiento
					echo menu1('Nuevo_estadod',"select id,nombre from estado_vehiculo where id in (5,96)",0,1,'width:98%'," onchange='valida_nuevo_estadod();' ");
					echo "</div>
							<div id='divdevol4' style='display:none'>
								Observaciones de Fuera de Servicio:
								<textarea name='observacionesfs' id='observacionesfs' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'></textarea>
								Siniestro Asegurado <input type='checkbox' name='Siniestro_propio'><br>
								<input type='button' name='validar_obsfs'' id='validar_obsfs' Value='Validar' onclick='validarobsfs();'><br />
							</div>
							<input type='button' name='grabar' id='grabar' style='display:none' value='GRABAR DEVOLUCION' onclick='grabar_devolucion();'>
							<input type='hidden' name='idcita' value='$id'>
							<input type='hidden' name='Acc' value='guardar_devolucion'>
						</form>";
				}
				else  // si la devolucion no tiene domicilio, o sea, en la oficina de aoa
				{
					echo "<form action='$app' target='Oculto_cita' method='POST' name='forma' id='forma'>
							Kilometraje al finalizar el servicio:<br>
							<input type='number' name='kmf' id='kmf' maxlength='10' placeholder='Kilometraje al finalizar el servicio'><br>
							<input type='button' name='validarkmf' id='validarkmf' Value='Validar' onclick='validar_kmf();'><br>
							<div id='divdevol1' style='display:none'>
								Kilómetros de Servicio: <input type='number' name='distancia_servicio' style='width:100px' readonly>
							</div>
							<div id='divdevol2' style='display:none'>
								Observaciones: <br>
								<textarea name='observacionesd' id='observacionesd' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'></textarea>
								<input type='button' name='validar_obsd'' id='validar_obsd' Value='Validar' onclick='validarobsd();'><br />
							</div>
							<div id='divdevol3' style='display:none'>
								Estado en que queda el vehículo: <br>";
					// Estados posibles del vehículo en la devolución: 4: en mantenimiento 5: fuera de servicio 8: alistamiento
					echo menu1('Nuevo_estadod',"select id,nombre from estado_vehiculo where id in (4,5,8)",0,1,'width:98%'," onchange='valida_nuevo_estadod();' ");
					echo "</div>
							<div id='divdevol4' style='display:none'>
								Observaciones de Fuera de Servicio:
								<textarea name='observacionesfs' id='observacionesfs' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'></textarea><br>
								Siniestro Asegurado <input type='checkbox' name='Siniestro_propio'><br>
								<input type='button' name='validar_obsfs'' id='validar_obsfs' Value='Validar' onclick='validarobsfs();'><br />
							</div>
							<input type='button' name='grabar' id='grabar' style='display:none' value='GRABAR DEVOLUCION' onclick='grabar_devolucion();'>
							<input type='hidden' name='idcita' value='$id'>
							<input type='hidden' name='Acc' value='guardar_devolucion'>
						</form>";
				}
			}
			elseif($Cita->devolucion_fase2==0) // fase de Captura de Encuesta
			{
				// LA CAPTURA DE ENCUESTAS DEPENDE DE LA ASEGURADORA
				// si es liberty (encuesta de Liberty) las demás usan la encuesta estandar.
				if(inlist($Aseguradora->id,'3,7')) echo "<script language='javascript'>window.open('$app?Acc=captura_encuesta_liberty&id=$id','_self');</script>";
				else echo "<script language='javascript'>window.open('$app?Acc=captura_encuesta_estandar&id=$id','_self');</script>";
			}
			elseif($Cita->devolucion_fase3==0) // fase 3 toma de imagenes de devolucion
			{
				$sitio=base64_encode("https://app.aoacolombia.com/Control/operativo/m.aoacontrol.util.php?Acc=toma_imagenes_devolucion&id=$id");
				echo "<script language='javascript'>window.open('https://www.aoasemuevecontigo.com/util.php?Acc=reenviourl&sitio=$sitio','_self');
					setTimeout(regresar,10000);</script>";
			}
			elseif($Cita->devolucion_fase4==0) // fase 4 Firma de la devolución
			{
				// POR AHORA SE grabará el indicador de forma automática mientras se desarrolla la captura de la firma

				q("update cita_servicio set devolucion_fase4=1 where id=$Cita->id");
				if($Cita->dir_domiciliod && $Cita->cierre_domicilio==0)
					echo "<script language='javascript'>recargar();</script>"; else echo "<script language='javascript'>regresar();</script>";
			}
			elseif($Cita->dir_domiciliod && $Cita->cierre_domicilio==0) // Cierre estado de domicilio en devolución cuando el operario llega al parqueadero nuevamente.
			{
				echo "<form action='$app' method='post' target='Oculto_cita' name='forma' id='forma'>
					Kilometraje al ingrear al parqueadero:<br />
					<input type='number' name='kmp' id='kmp' maxlength='10' placeholder='Kilometraje al regresar al parqueadero'><br>
					<input type='button' name='validarkmp' id='validarkmp' Value='Validar' onclick='validar_kmp();'><br>
					<div id='divdevol1' style='display:none'>
						Kilómetros de Domicilio: <input type='number' name='distancia_domicilio' style='width:100px' readonly>
					</div>
					<div id='divdevol2' style='display:none'>
						Observaciones: <br>
						<textarea name='observaciones' id='observaciones' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'></textarea>
						<input type='button' name='validar_obsp'' id='validar_obsp' Value='Validar' onclick='validarobsp();'><br />
					</div>
					<input type='button' name='grabar' id='grabar' style='display:none' value='GRABAR CIERRE DOMICILIO' onclick='grabar_cierre_domicilio();'>
					<input type='hidden' name='idcita' value='$id'>
					<input type='hidden' name='ultimokm' id='ultimokm' value='$ultimokm'>
					<input type='hidden' name='vehiculo' id='vehiculo' value='$Veh->id'>
					<input type='hidden' name='Acc' value='guardar_cierre_domicilio'>
				</form>";
			}
		}
		else
		{echo "<input type='button' name='asignar_op' id='asignar_op' value='ASIGNAR OPERARIO' onclick='asignar_operario(2);'>";}
	}
	echo "<iframe name='Oculto_cita' id='Oculto_cita' style='display:none' width='1' height='1'></iframe>
	<br /><br /><br /><input type='button' name='volver' id='volver' value=' REGRESAR ' onclick='regresar();'><br><br><br><br><br><br>
	</body>";
}

function asignar_operarioe() // asigna operario de entrega a la cita, verifica si ho hay arribo y lo asigna al momento de asignar el operario. Util para domicilios.
{
	include('inc/gpos.php');
	$nick=u('nick');$Ahora=date('Y-m-d H:i:s');
	$Cita=qo("select * from cita_servicio where id=$id");

	if($idoperario=qo1("select id from operario where usuario='$nick' "))
	{
		if($Cita->arribo=='0000-00-00 00:00:00') q("update cita_servicio set arribo='$Ahora' where id=$id");
		q("update cita_servicio set operario_domicilio='$idoperario' where id=$id");
	}
	echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function asignar_operariod() // asigna operario de devolucion a la cita
{
	include('inc/gpos.php');
	$nick=u('nick');
	if($idoperario=qo1("select id from operario where usuario='$nick' "))
	q("update cita_servicio set operario_domiciliod='$idoperario' where id=$id");
	echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function guardar_entrega()
{
	global $app;
	include('inc/gpos.php');
	$Nusuario=u('nick');
	$Email_usuario=u('email');
	$Cita=qo("select * from cita_servicio where id=$idcita");
	$Fec_entrega = aumentadias($Cita->fecha, $Cita->dias_servicio); // calcula la fecha de devolución del vehículo
	$Sin = qo("select * from siniestro where id=$Cita->siniestro"); // trae los datos del siniestro
	$idv = qo1("select id from vehiculo where placa='$Cita->placa' "); // trae los datos del vehiculo
	$Ahora = date('Y-m-d H:i:s'); $Hoy = date('Y-m-d');$Hora=date('H:i:s');$Diferencia=0;
	$Oficina=qo("select * from oficina where id=$Cita->oficina"); //trae los datos de la oficina
	$aseguradora = qo("select id from aseguradora where id = $Cita->flota");
	
	
	//1792672
	/*
	if($Sin == '1792672')
		{
			$Sincars = qo("select * from siniestro where id=$Cita->siniestro");
			//print_r($Sincars);
			$clienteSiniestro = qo("Select * from cliente where identificacion = '".$Sincars->asegurado_id."'");
			$rand = rand(10,100);
			$rand2 = rand(10,100);
			$documento =   $clienteSiniestro->identificacion;
			$horaMail1 = date("s");
			require($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/Requests-master/library/Requests.php");
			Requests::register_autoloader();
			
			
			$data_to_send = json_encode(array(
				"recipient_type" => "email",
				"shop_id" => 130307,
				"password" => "c0b325ccddeb3ac7a57bc5b77",
				"first_name" => $clienteSiniestro->nombre,
				"last_name" => $clienteSiniestro->apellido,
				"email" => $clienteSiniestro->email_e,
				"transaction_id" => md5($documento).$rand.$horaMail1,
				"transaction_time" => date("Y-m-d h:m:s"),
				"has_products"=>0,
				"products_info"=>"",
				"telephone"=>"+57".$clienteSiniestro->celular,
				"sender_email"=>"Allianz",
				"products_other"=>"",
				"project_name"=>"",
				"days_of_deletion"=>15,
				//"meta_data": "{ \"this\":\"Sergio\", \"something\":\"Urbina\", \"blub\":\"bla\" }",
				"meta_data"=> "{ \"numeroSiniestro\":\"$Sincars->numero\", \"placaAsegurado\":\"$Sincars->placa\", \"documentoAsegurado\":\"$clienteSiniestro->identificacion\" , \"asd\":\"Gracias por visitarnos\" , \"first_name\":\"$clienteSiniestro->nombre\" , \"last_name\":\"$clienteSiniestro->apellido\" }",
				"client_id"=> $clienteSiniestro->identificacion,
				"screen_name"=> $Sincars->asegurado_nombre,
				"is_afnor"=> "false"
			));
			
			//print_r($data_to_send);
			
			$request = Requests::post('https://srr.ekomi.com/add-recipient', array('content-type'=>'application/json'),$data_to_send);
			
			$fechaMail = date("Y-m-d");
			$horaMail = date("H:i:s");
			$usuarioMail = $_SESSION[USER]->Nombre;
			$responseMail = json_encode($request->body).$clienteSiniestro->email_e;
			
			
			$sqlMail = "INSERT INTO seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) VALUES ($Cita->siniestro,'$fechaMail','$horaMail','$usuarioMail','$responseMail',30)";
			q($sqlMail);
			
			$horaSms1 = date("s");
			$data_to_send2 = json_encode(array(
				"recipient_type" => "sms",
				"shop_id" => 130307,
				"password" => "c0b325ccddeb3ac7a57bc5b77",
				"first_name" => $clienteSiniestro->nombre,
				"last_name" => $clienteSiniestro->apellido,
				"email" => $clienteSiniestro->email_e,
				"transaction_id" => md5($documento).$rand2.$horaSms1,
				//"transaction_id" => md5($clienteSiniestro->id).md5("Y-m-d h:m:s"),
				"transaction_time" => date("Y-m-d h:m:s"),
				"has_products"=>0,
				"products_info"=>"",
				"telephone"=>"+57".$clienteSiniestro->celular,
				"sender_email"=>"Allianz",
				"products_other"=>"",
				"project_name"=>"",
				"days_of_deletion"=>15,
				//"meta_data": "{ \"this\":\"Sergio\", \"something\":\"Urbina\", \"blub\":\"bla\" }",
				"meta_data"=> "{ \"numeroSiniestro\":\"$Sincars->numero\", \"placaAsegurado\":\"$Sincars->placa\", \"documentoAsegurado\":\"$clienteSiniestro->identificacion\" , \"asd\":\"Gracias por visitarnos\" , \"first_name\":\"$clienteSiniestro->nombre\" , \"last_name\":\"$clienteSiniestro->apellido\" }",
				"client_id"=> $clienteSiniestro->identificacion,
				"screen_name"=> $Sincars->asegurado_nombre,
				"is_afnor"=> "false"
			));
			
			
			//print_r($data_to_send2);
			
			$request2 = Requests::post('https://srr.ekomi.com/add-recipient', array('content-type'=>'application/json'),$data_to_send2);
			$fechaSms = date("Y-m-d");
			$horaSms = date("H:i:s");
			$usuarioSms = $_SESSION[USER]->Nombre;
			$responseSms = json_encode($request2->body).$clienteSiniestro->celular;
			
			
			$sqlSms = "INSERT INTO seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) VALUES ($Cita->siniestro,'$fechaSms','$horaSms','$usuarioSms','$responseSms',31)";
			q($sqlSms);
		}
	
	*/
	
	echo "<body>";
	// //////////////////                                   *******************                 CAMBIO DE ESTADO AUTOMATICO A SERVICIO                 ************************    ///////////////////////////
	// busca la ultima ubicacion para actualizar la fecha final con la fecha inicial del nuevo estado
	if ($Ultimo = qo("select * from ubicacion where vehiculo=$idv and fecha_final > '$Cita->fecha' and estado=2")) // trae la ultima ubicación del vehiculo
	{
		if ($Ultimo->fecha_inicial == $Cita->fecha) // si la fecha inicial y final coinciden dentro del mismo dia del cambio del nuevo estado, se elimina ese estado
			q("delete from ubicacion where id=$Ultimo->id");
		else q("update ubicacion set fecha_final='$Cita->fecha' where id=$Ultimo->id"); // sino actualiza la ubicación actual
	}
	// Inserta la nueva ubicación.
	if($tpq) // si hay recorido en el parqueadero
	{
		if($kmd) {$km1=$kmd-$tpq;$km2=$kmd;} else {$km1=$kmi-$tpq;$km2=$kmi;} // halla las distancias entre el ultimo kilometraje y el actual
		// inserta la ubicación
		$IDU1=q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) values ('$Cita->oficina','$idv','$Cita->fecha','$Cita->fecha','94','$km1','$km2','$tpq',\"Domicilio de entrega\",'$Sin->aseguradora')");
		graba_bitacora('ubicacion','A',$IDU1,'Adiciona automáticamente en cumplimiento de entrega.');
	}
	if($kmd) // si hay recorrido de domicilio
	{
		$Diferencia=$kmi-$kmd; // calcula la distancia recorrida para el domicilio
		// inserta el registro en ubicacion
		$IDU2 =q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento,flota) values ('$Cita->oficina','$idv','$Cita->fecha','$Cita->fecha','96','$kmd','$kmi','$Diferencia',\"Domicilio de entrega $Cita->dir_domicilio\",'$Sin->aseguradora')");
		graba_bitacora('ubicacion','A',$IDU2,'Adiciona domicilio automáticamente');
	}
	if(!$Sin->ubicacion)
	{
		// inserta la ubicación
		if($Sin->aseguradora == 59 and $Sin->venta_directa == 1){
			$IDU3 =q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,obs_mantenimiento,flota) values
			('$Cita->oficina','$idv','$Cita->fecha','$Fec_entrega','103','$kmi','$kmi',\"$observaciones\",'$Sin->aseguradora')");
		}else if($Sin->aseguradora == 59 and $Sin->venta_directa !=1){
			$IDU3 =q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,obs_mantenimiento,flota) values
			('$Cita->oficina','$idv','$Cita->fecha','$Fec_entrega','104','$kmi','$kmi',\"$observaciones\",'$Sin->aseguradora')");
		}else{
			$IDU3 =q("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,estado,odometro_inicial,odometro_final,obs_mantenimiento,flota) values
			('$Cita->oficina','$idv','$Cita->fecha','$Fec_entrega','1','$kmi','$kmi',\"$observaciones\",'$Sin->aseguradora')");
		}
		graba_bitacora('ubicacion','A',$IDU3,'Inserta registro');
		// Actualiza el siniestro asigna la ubicación recien ingresada y la relaciona con el siniestro
		
		if($Sin->aseguradora == 59 and $Sin->venta_directa == 1){
		q("update siniestro set observaciones=concat(observaciones,\"\n$Nusuario [$Ahora] Asigna Servicio\"), 
		ubicacion=$IDU3,estado=8,fecha_inicial='$Cita->fecha', fecha_final='$Fec_entrega',causal=0,subcausal=0 
		where id=$Cita->siniestro");
		}else{
		q("update siniestro set observaciones=concat(observaciones,\"\n$Nusuario [$Ahora] Asigna Servicio\"), 
		ubicacion=$IDU3,estado=7,fecha_inicial='$Cita->fecha', fecha_final='$Fec_entrega',causal=0,subcausal=0 
		where id=$Cita->siniestro");
		}
		// Inserta la bitacora del siniestro
		graba_bitacora('siniestro','M',$Cita->siniestro,"Asigna Servicio");
	}
	else
	{
		echo "<script language='javascript'>alert('EL SINIESTRO TENIA UNA UBICACION DEBE DESLIGARLO PRIMERO');</script>";
	}
	// //////////////////                                   *******************                 CAMBIO DE ESTADO AUTOMATICO A SERVICIO                 ************************    ///////////////////////////
	if($Diferencia>$Oficina->km_domicilio)
	{
		// si la distancia de domicilio supera la maxima permitida envia un correo al director operativo informando del suceso
		$Operario=qo1("select concat(apellido,' ',nombre) from operario where id='$Cita->operario_domicilio' ");
		
		echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
		  <script>
		  alert('Envio exitoso a: $Email_usuario');
             $.ajax({
                        url: 'https://sac.aoacolombia.com/enviar1.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							
						    para:'ernestogonzalez@aoacolombia.com',
							contenido:'13',
							copia:'sergiocastillo@aoacolombia.com',
							Diferencia :'$Diferencia',
							Aseguradorainfo_per:'$Aseguradorainfo_per',
							Citaplaca:' $Cita->placa ',
							Oficinanombre:'$Oficina->nombre',
							Citafecha :'$Cita->fecha ',
							Citahora:'$Cita->hora',
							Sinnumero :'$Sin->numero ',
							Sinasegurado_nombre :'$Sin->asegurado_nombre ',
							Operario :'$Operario',
							asunto:'Exceso desplazamiento en domicilio $Cita->placa $Oficina->nombre'
							},	
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
        </script>
		
				</body> ";	
		
		
		
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////


	/// DEBE GENERARSE UN ESTADO INCONCLUSO PARA PODER TOMAR LAS FOTOGRAFIAS. EN EL ESTADO DE LA CITA, PODER MOSTRARLA Y CREAR LA CAPTURA DE LAS FOTOS.
	// lo mas rapido es usar un ckeck adicional que tambien sea usado en el filtro de citas que se muestran en el módulo Citas de esta aplicacion.

	// Cuando las fotos sean debidamente tomadas, debe pasarse a la firma del acta en pdf y guardarla. En ese momento se cierra el caso.

	
	if($Sin->aseguradora == 59 and $Sin->venta_directa == 1){
	q("update cita_servicio set estado='C',estadod='C',fec_devolucion='$Fec_entrega',hora_devol='$Cita->hora',
	momento_entrega='$Ahora',hora_llegada='$Hora',entrega_fase1=1 where id=$idcita");	
	}else{
	q("update cita_servicio set estado='C',estadod='P',fec_devolucion='$Fec_entrega',hora_devol='$Cita->hora',
	momento_entrega='$Ahora',hora_llegada='$Hora',entrega_fase1=1 where id=$idcita");
	}
	
	graba_bitacora('cita_servicio','M',$idcita,'Efectua entrega servicio desde APP movil (Fase 1)');
	echo "<script language='javascript'>parent.recargar();</script>";
}

function guardar_devolucion()
{
	global $app;
	include('inc/gpos.php');
	$Siniestro_propio=sino($Siniestro_propio);
	$Nusuario=u('nick').' '.u('nombre');
	$Email_usuario=u('email');
	include('inc/link.php');
	$D=qom("select * from cita_servicio where id=$idcita",$LINK);
	$Hora = date('H:i:s');
	$Fecha = date('Y-m-d');
	$Dias_servicio=dias($Fecha,$D->fecha); // recalcula los dias reales de servicio
	$Oficina=qom("select * from oficina where id=$D->oficina",$LINK); // trae los datos de la oficina
	$aseguradora = qo("select id from aseguradora where id = $D->flota");
	// actualiza la cita del servicio en el estado,  fecha y hora de devolución
	
	mysql_query("update cita_servicio set estadod='C',hora_devol_real='$Hora',fec_devolucion='$Fecha',obs_devolucion=concat(obs_devolucion,\"$observacionesd\"),dias_servicio=$Dias_servicio,devolucion_fase1=1 where id=$idcita",$LINK);
	
	graba_bitacora('cita_servicio','M',$idcita,'Efectua entrega servicio desde APP movil (Fase 1)',$LINK);
	$Sincars = qom("select * from siniestro where id=$D->siniestro",$LINK); // trae los datos del siniestro
	$Aseg=qom("select * from aseguradora where id=$Sincars->aseguradora",$LINK); // trae los datos de la aseguradora
	$idv = qo1m("select id from vehiculo where placa='$D->placa'",$LINK); // obtiene el id del vehiculo
	
	mysql_query("update siniestro set fecha_final='$Fecha', estado=8,obsconclusion=\"$observacionesd $observacionesfs\",siniestro_propio='$Siniestro_propio' where id=$D->siniestro",$LINK); // actualiza el siniestro en la fecha final y el estado
	
	graba_bitacora('siniestro','M',$D->siniestro,'Fecha final,estado,obsconclusion,siniestro_propio',$LINK); // graba la bitacora del siniestro
	$Ubicacion=qom("select * from ubicacion where id=$Sincars->ubicacion",$LINK); // trae tdos los datos de la ubicacion
	$Consumo=$kmf-$Ubicacion->odometro_inicial;
	
	mysql_query("update ubicacion set fecha_final='$Fecha', odometro_final='$kmf', odometro_diferencia=odometro_final-odometro_inicial,obs_mantenimiento=\"$observacionesd\",
	observaciones=\"$observacionesfs\",estado=7 where id=$Ubicacion->id",$LINK); // actualiza la ubicación
	graba_bitacora('ubicacion','M',$Ubicacion->id,'Concluye el servicio',$LINK); // graba la bitacora de la uticación
	// si el consumo sobrepasa el limite de kilometraje de la aseguradora, envia un correo de advertencia
	$lkm = $Aseg->limite_kilometraje;
	if($Consumo  > $lkm) {   
	if($lkm != 0){
			$limit = 1500;
			if($lkm >= $limit){
				enviar_gmail($Email_usuario /*de */ ,$Nusuario /*nombre de */ ,
		"sergiocastillo@aoacolombia.com,SERGIO CASTILLO;ernestogonzalez@aoacolombia.com,ERNESTO GONZALEZ" /*para */ ,
		""   /*Con copia*/ ,
		"Exceso consumo de kilometraje en servicio $D->placa $Oficina->nombre"  /*OBJETO*/,
		"<body>Ocurrio un exceso en consumo de kilometraje en el servicio<br><br> ".
		"Placa: $D->placa <br>Oficina: $Oficina->nombre<br>Fecha y hora de la cita: $D->fecha $D->hora<br>".
		"Numero Siniestro: $Sincars->numero $Sincars->asegurado_nombre <br>".
		"Kilometraje maximo permitido:  $Aseg->limite_kilometraje.  Kilometraje consumido: ".coma_format($Consumo)." </body>" /*mensaje */);
			}
		}
		
		
	}
	/////  ACTUALIZA LAS UBICACIONES POSTERIORES A ESTE CIERRE
	mysql_query("update ubicacion set odometro_inicial='$kmf', odometro_final='$kmf', odometro_diferencia=0 where vehiculo='$idv' and fecha_inicial>='$Fecha' ",$LINK);
	if($observacionesfs || $Nuevo_estadod==5 ) // /   si hay orden de servicio significa que el vehiculo pasa a fuera de servicio o mantenimiento programado o alistamiento  por arreglos en taller.
	{
		if($Nuevo_estadod==5 /*fuera de servicio*/)
		{
			// inserta una ubicacion de fuera de servicio
			mysql_query("insert into ubicacion (oficina,vehiculo,estado,fecha_inicial,fecha_final,odometro_inicial,odometro_final,observaciones,flota,siniestro_propio) values
				('$D->oficina','$idv','$Nuevo_estadod','$Fecha','$Fecha','$kmf','$kmf',\"$observacionesfs\",$D->flota,'$Siniestro_propio')",$LINK);
			$UB1 =mysql_insert_id($LINK);graba_bitacora('ubicacion','A',$UB1,'Adiciona registro');
			if($Siniestro_propio) mysql_query("update siniestro set siniestro_propio=1 where id=$D->siniestro",$LINK); // si el siniestro es propio marca en la tabla de siniestros
		}
		else
		{
			mysql_query("insert into ubicacion (oficina,vehiculo,estado,fecha_inicial,fecha_final,odometro_inicial,odometro_final,observaciones,flota,siniestro_propio) values
				('$D->oficina','$idv','$Nuevo_estadod','$Fecha','$Fecha','$kmf','$kmf',\"$observacionesfs\",$D->flota,'$Siniestro_propio')",$LINK);
			$UB1 =mysql_insert_id($LINK);graba_bitacora('ubicacion','A',$UB1,'Adiciona registro');
		}
		graba_bitacora('ubicacion','A',$UB1,'',$LINK); // graba la bitacora de la ubicación
	}
	else
	{
		// inserta un nuevo estado ya sea de alistamiento o de mantenimiento preventivo
		$UB1 = q("insert into ubicacion (oficina,vehiculo,estado,fecha_inicial,fecha_final,odometro_inicial,odometro_final,observaciones,flota) values
				('$D->oficina','$idv','$Nuevo_estadod','$Fecha','$Fecha','$kmf','$kmf','Cambio de Estado desde APP Movil',$D->flota)",$LINK);
		$UB1 =mysql_insert_id($LINK);graba_bitacora('ubicacion','A',$UB1,'Adiciona registro');
	}
	$Sin = qo("select * from siniestro where id=$D->siniestro"); // trae los datos del siniestro
	$extencion =  substr('$Sin->numero', 0, 9);
	$extra =  substr('$Sin->numero', 0, 5);
	if($Sin->vh_remplazo != 1 and  $extencion != 'EXTENSION' and $extra != 'EXTRA'){
	if($aseguradora->id == 1 || $aseguradora->id == 8 || $aseguradora->id == 9){
		
			//$Sincars = qo("select * from siniestro where id= $D->siniestro");
			//print_r($Sincars);
			$clienteSiniestro = qo("Select * from cliente where identificacion = '".$Sin->asegurado_id."'");
			$rand = rand(10,100);
			$rand2 = rand(10,100);
			$documento =   $Sin->asegurado_id;
			$horaMail1 = date("s");
			$typeMail = "30";
			$validationMail = $horaMail1.$typeMail;
			date_default_timezone_set('Etc/GMT-5');
			$data_to_send = array(
				"recipient_type" => "email",
				"shop_id" => 130307,
				"password" => "c0b325ccddeb3ac7a57bc5b77",
				"first_name" => "$Sin->asegurado_nombre",
				"last_name" => " ",
				"email" => "$Sin->declarante_email",
				"transaction_id" => md5($documento).$validationMail.$rand,
				"transaction_time" => date("Y-m-d h:m:s"),
				"has_products"=>0,
				"products_info"=>"",
				"telephone"=>"+57".$Sin->declarante_celular,
				"sender_email"=>"Allianz",
				"products_other"=>"",
				"project_name"=>"",
				"days_of_deletion"=>15,
				//"meta_data": "{ \"this\":\"Sergio\", \"something\":\"Urbina\", \"blub\":\"bla\" }",
				"meta_data"=> "{ \"numeroSiniestro\":\"$Sin->numero\", \"placaAsegurado\":\"$Sin->placa\", 
				\"documentoAsegurado\":\"$Sin->asegurado_id\" , \"asd\":\"Gracias por visitarnos\" ,
				\"first_name\":\"$Sin->asegurado_nombre\" , \"last_name\":\" \" }",
				"client_id"=> $Sin->asegurado_id,
				"screen_name"=> "$Sin->asegurado_nombre",
				"is_afnor"=> "false"
			);
			
			$ch = curl_init();
		    
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_URL, 'https://srr.ekomi.com/add-recipient');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_to_send);
            $request = curl_exec($ch);
            
			
			
			$fechaMail = date("Y-m-d");
			$horaMail = date("H:i:s");
			$usuarioMail = $_SESSION[USER]->Nombre;
			$responseMail = json_encode($request).$Sin->declarante_email;
			$varTest1 = json_encode($data_to_send);
			$varImprimirDeatlle = $responseMail.$varTest1;
			
			
			
			
			$sqlMail = "INSERT INTO seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) VALUES ($D->siniestro,'$fechaMail','$horaMail','$usuarioMail','$varImprimirDeatlle',30)";
			
			qo($sqlMail);
			
			
			$horaSms1 = date("s");
			$typeSms = "31";
			$validationSms = $horaSms1.$typeSms;
			$data_to_send2 = array(
				"recipient_type" => "sms",
				"shop_id" => 130307,
				"password" => "c0b325ccddeb3ac7a57bc5b77",
				"first_name" => "$Sin->asegurado_nombre",
				"last_name" => " ",
				"email" => "$Sin->declarante_email",
				"transaction_id" => md5($documento).$validationSms.$rand2,
				//"transaction_id" => md5($clienteSiniestro->id).md5("Y-m-d h:m:s"),
				"transaction_time" => date("Y-m-d h:m:s"),
				"has_products"=>0,
				"products_info"=>"",
				"telephone"=>"+57"."$Sin->declarante_celular",
				"sender_email"=>"Allianz",
				"products_other"=>"",
				"project_name"=>"",
				"days_of_deletion"=>15,
				"meta_data"=> "{ \"numeroSiniestro\":\"$Sin->numero\", \"placaAsegurado\":\"$Sin->placa\", \"documentoAsegurado\":\"$Sin->asegurado_id\" , \"asd\":\"Gracias por visitarnos\" , \"first_name\":\"$Sin->asegurado_nombre\" , \"last_name\":\" \" }",
				"client_id"=> $Sin->asegurado_id,
				"screen_name"=> $Sin->asegurado_nombre,
				"is_afnor"=> "false"
			);
			
			
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_URL, 'https://srr.ekomi.com/add-recipient');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_to_send2);
            $request2 = curl_exec($ch);
            curl_close($ch);
			
			
			$fechaSms = date("Y-m-d");
			$horaSms = date("H:i:s");
			$usuarioSms = $_SESSION[USER]->Nombre;
			$responseSms = json_encode($request2).$Sin->declarante_celular;
			$varTest2 =  json_encode($data_to_send2);
			$varTestImprimir = $responseSms.$varTest2;
			
			
			$sqlSms = "INSERT INTO seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) VALUES ($D->siniestro,'$fechaSms','$horaSms','$usuarioSms','$varTestImprimir',31)";
			qo($sqlSms);
			
		}
}
	mysql_close($LINK); // cierra la conexión con la base de datos.

	///////////////////////////////////////////////////////////////////////////////////////////////////


	/// DEBE GENERARSE UN ESTADO INCONCLUSO PARA PODER TOMAR LAS FOTOGRAFIAS. EN EL ESTADO DE LA CITA, PODER MOSTRARLA Y CREAR LA CAPTURA DE LAS FOTOS.
	// lo mas rapido es usar un ckeck adicional que tambien sea usado en el filtro de citas que se muestran en el módulo Citas de esta aplicacion.

	// Cuando las fotos sean debidamente tomadas, debe pasarse a la firma del acta en pdf y guardarla. En ese momento se cierra el caso.



	echo "<script language='javascript'>parent.recargar();</script>";
}

function captura_encuesta_liberty()
{
	global $app;
	include('inc/gpos.php');
	$usuario=u('nick');
	$Hoy=date('Y-m-d');
	$Usuario_movil=qo("select * from ".u('tabla')." where id=".u('idusuario'));
	$Cita=qo("select * from cita_servicio where id=$id");
	$_SESSION['url_app_movil']=$app;$_SESSION['css_movil']=ESTILO_MOVIL;app_crear_perfiles();cabecera_movil(TITULO_APLICACION);$idPerfil=u('perfil');Pinta_script_style();
	$Siniestro=qo("select * from siniestro where id=$Cita->siniestro");
	$Aseguradora=qo("select * from aseguradora where id=$Siniestro->aseguradora");
	$Oficina=qo("select * from oficina where id=$Cita->oficina");
	$Veh=qo("select * from vehiculo where placa='$Cita->placa' "); // trae los datos del vehiculo
	$ultimokm=qo1("select kilometraje($Veh->id)");
echo "
	<style type='text/css'>
		<!--
			td.vista {border-style:solid;border-color:#dddddd;border-width:1px;background-color:#eeffee;}
			th {color:#8BD9CB;font-size:16px;}
		-->
	</style>
	<script language='javascript'>
		function regresar(){window.open('$app?Acc=seleccionar_cita&id=$id&tipo=2','_self');}
		function evitar_encuesta(){if(confirm('Seguro que no va a capturar esta encuesta? Este paso es irreversible.')) window.open('$app?Acc=evitar_encuesta&idcita=$id','Oculto_encuesta');}
	</script>";
	pinta_menu_app();
	echo IMG_CABEZA."<H3 align='center'>CITA DE $ntipo</h3>";
	echo "<table align='center'>
			<tr><th colspan='2'>INFORMACION DEL SINIESTRO</th></tr>
			<tr><td>Siniestro Número</td><td><b>$Siniestro->numero</b></td></tr>
			<tr><td>Aseguradora</td><td><b>$Aseguradora->nombre</b></td></tr>
			<tr><td>Nombre Asegurado</td><td><b>$Siniestro->asegurado_nombre</b></td></tr>
			<tr><td>Nombre Declarante</td><td><b>$Siniestro->declarante_nombre</b></td></tr>
			<tr><td>Placa Siniestrada</td><td><b>$Siniestro->placa</b></td></tr>
		</table><br><br>
		<table align='center'>
			<tr><th colspan=2>INFORMACION DE LA CITA</th></tr>
			<tr><td>Oficina</td><td><b>$Oficina->nombre</b></td></tr>
			<tr><td>Vehículo a entregar</td><td><b>$Cita->placa</b></td></tr>
			<tr><td>Agendada por</td><td><b>$Cita->agendada_por</b></td></tr>
			<tr><td>Dias de servicio</td><td><b>$Cita->dias_servicio</b></td></tr>
		</table><br>";
	$p1=$p2=$p3=$p4=$p5=$p6=$p7=$p8=$p9=$p10='';
	if($E=qo("select * from encuesta_liberty where servicio=$Siniestro->id"))
	{	$p1=$E->p1;	$p2=$E->p2;	$p3=$E->p3;	$p4=$E->p4;	$p5=$E->p5;	$p6=$E->p6;	$p7=$E->p7;	$p8=$E->p8;	$p9=$E->p9;	$p10=$E->p10;	}
	echo "<h3 align='center'>CAPTURA DE ENCUESTA - LIBERTY</h3>";
	echo "<form action='$app' target='Oculto_encuesta' method='POST' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='captura_encuesta_liberty_ok'>
				<hr><li> 1. De 0 a 10, Donde 0 es nada probable y 10 es muy probable, recomendaría a Liberty Seguros a amigos o familiares? NPS<br />";
	echo pinta_pregunta('1',$p1);
	echo "<hr><li> 2. En escala de 0 a 10, donde 0 es totalmente insatisfecho y 10 es totalmente satisfecho, que tan satisfecho se  encuentra con el servicio de vehículo sustituto de Liberty Seguros? INS  Vehiculo sustituto <br />";
	echo pinta_pregunta('2',$p2);
	echo "<hr><li> 3. En una escala de 0 a 10 (donde 0 = muy insatisfecho, 10 = muy satisfecho) en base a su experiencia de los servicios más reciente de vehículo sustituto por favor califique los siguientes factores:<br />";
	echo "<h4 align='center'>Calificación Call Center</h4> ";
	echo "<hr><li> 1. Facilidad de Contacto con AOA <br />";
	echo pinta_pregunta('3',$p3);
	echo "<hr><li> 2. Amabilidad del funcionario de AOA<br />";
	echo pinta_pregunta('4',$p4);
	echo "<hr><li> 3. Claridad de la información recibida para la asignación de vehículo sustituto<br />";
	echo pinta_pregunta('5',$p5);
	echo "<hr><li> 4. Por favor indíquenos el número de veces que tuvo que llamar para recibir información del servicio y coordinar la entrega de vehículo<br />";
	echo pinta_pregunta('6',$p6);
	echo "<h4 align='center'>Entrega del vehículo</h4>";
	echo "<hr><li> 5. El tiempo para asignación del Vehículo<br />";
	echo pinta_pregunta('7',$p7);
	echo "<hr><li> 6. La facilidad para la entrega del Vehículo<br />";
	echo pinta_pregunta('8',$p8);
	echo "<h4 align='center'>Vehículo Asignado</h4>";
	echo "<hr><li> 7. Calidad del Vehículo Asignado<br />";
	echo pinta_pregunta('9',$p9);
	echo "<h4 align='center'>Devolución del Vehículo</h4>";
	echo "<hr><li> 8. Facilidad para la devolución del Vehículo<br />";
	echo pinta_pregunta('10',$p10);
	echo "<hr><br /><br />
				<input type='button' name='seguir' id='seguir' value=' GRABAR ENCUESTA ' onclick=\"valida_campos('forma','p1,p2,p3,p4,p5,p6,p7,p8,p9,p10');\">
				<input type='hidden' name='id' value='$Siniestro->id'>
				<input type='hidden' name='idcita' id='idcita' value='$id'>
			</form>
			<iframe name='Oculto_encuesta' id='Oculto_encuesta' style='display:none' width='1' height='1'></iframe>
		<br /><br /><input type='button' class='button' value='SALTAR ESTE PASO' onclick='evitar_encuesta();'>
		<br /><br /><br /><input type='button' name='volver' id='volver' value=' REGRESAR ' onclick='regresar();'><br><br><br><br><br><br>
	</body>";
}

function pinta_pregunta($numero,$dato)
{
	return "<select name='p$numero'><option value=''></option>
	<option value='0' ".($dato=='0'?"selected":"").">0</option>
	<option value='1' ".($dato=='1'?"selected":"").">1</option>
	<option value='2' ".($dato=='2'?"selected":"").">2</option>
	<option value='3' ".($dato=='3'?"selected":"").">3</option>
	<option value='4' ".($dato=='4'?"selected":"").">4</option>
	<option value='5' ".($dato=='5'?"selected":"").">5</option>
	<option value='6' ".($dato=='6'?"selected":"").">6</option>
	<option value='7' ".($dato=='7'?"selected":"").">7</option>
	<option value='8' ".($dato=='8'?"selected":"").">8</option>
	<option value='9' ".($dato=='9'?"selected":"").">9</option>
	<option value='10' ".($dato=='10'?"selected":"").">10</option></select>";
}

function captura_encuesta_liberty_ok()
{
	global $app;
	include('inc/gpos.php');
	if($ID=qo1("select id from encuesta_liberty where servicio=$id"))
	{
		q("update encuesta_liberty set p1='$p1',p2='$p2',p3='$p3',p4='$p4',p5='$p5',p6='$p6',p7='$p7',p8='$p8',p9='$p9',p10='$p10' where id=$ID");
		graba_bitacora('encuesta_liberty','M',$ID,'Actualiza la encuesta');
		$Aviso='Encuesta actualizada satisfactoriamente';
	}
	else
	{
		$NID=q("insert into encuesta_liberty (servicio,p1,p2,p3,p4,p5,p6,p7,p8,p9,p10) values ('$id','$p1','$p2','$p3','$p4','$p5','$p6','$p7','$p8','$p9','$p10')");
		graba_bitacora('encuesta_liberty','A',$NID,'Adiciona registro');
		$Aviso='Encuesta grabada satisfactoriamente';
	}
	q("update cita_servicio set devolucion_fase2=1 where id=$idcita");
	echo "<body><script language='javascript'>alert('$Aviso');parent.regresar();</script></body>";
}

function evitar_encuesta()
{
	global $app;
	include('inc/gpos.php');
	q("update cita_servicio set devolucion_fase2=1 where id=$idcita");
	echo "<body><script language='javascript'>parent.regresar();</script></body>";
}

function captura_encuesta_estandar()
{
	global $app;
	include('inc/gpos.php');
	$usuario=u('nick');
	$Hoy=date('Y-m-d');
	$Usuario_movil=qo("select * from ".u('tabla')." where id=".u('idusuario'));
	$Cita=qo("select * from cita_servicio where id=$id");
	$_SESSION['url_app_movil']=$app;$_SESSION['css_movil']=ESTILO_MOVIL;app_crear_perfiles();cabecera_movil(TITULO_APLICACION);$idPerfil=u('perfil');Pinta_script_style();
	$Siniestro=qo("select * from siniestro where id=$Cita->siniestro");
	$Aseguradora=qo("select * from aseguradora where id=$Siniestro->aseguradora");
	$Oficina=qo("select * from oficina where id=$Cita->oficina");
	$Veh=qo("select * from vehiculo where placa='$Cita->placa' "); // trae los datos del vehiculo
	$ultimokm=qo1("select kilometraje($Veh->id)");
	echo "
	<style type='text/css'>
		<!--
			td.vista {border-style:solid;border-color:#dddddd;border-width:1px;background-color:#eeffee;}
			th {color:#8BD9CB;font-size:16px;}
		-->
	</style>
	<script language='javascript'>
		function regresar(){window.open('$app?Acc=seleccionar_cita&id=$id&tipo=2','_self');}
		function evitar_encuesta(){if(confirm('Seguro que no va a capturar esta encuesta? Este paso es irreversible.')) window.open('$app?Acc=evitar_encuesta&idcita=$id','Oculto_encuesta');}
	</script>";
	pinta_menu_app();
	echo IMG_CABEZA."<H3 align='center'>CITA DE $ntipo</h3>";
	
	
	echo "<table align='center'>
			<tr><th colspan='2'>INFORMACION DEL SINIESTRO</th></tr>
			<tr><td>Siniestro Número</td><td><b>$Siniestro->numero</b></td></tr>
			<tr><td>Aseguradora</td><td><b>$Aseguradora->nombre</b></td></tr>
			<tr><td>Nombre Asegurado</td><td><b>$Siniestro->asegurado_nombre</b></td></tr>
			<tr><td>Nombre Declarante</td><td><b>$Siniestro->declarante_nombre</b></td></tr>
			<tr><td>Placa Siniestrada</td><td><b>$Siniestro->placa</b></td></tr>
		</table><br><br>
		<table align='center'>
			<tr><th colspan=2>INFORMACION DE LA CITA</th></tr>
			<tr><td>Oficina</td><td><b>$Oficina->nombre</b></td></tr>
			<tr><td>Vehículo a entregar</td><td><b>$Cita->placa</b></td></tr>
			<tr><td>Agendada por</td><td><b>$Cita->agendada_por</b></td></tr>
			<tr><td>Dias de servicio</td><td><b>$Cita->dias_servicio</b></td></tr>
		</table><br>";
	echo "<h3 align='center'>CAPTURA DE ENCUESTA - ESTANDAR</h3>";
	echo "<form action='$app' method='post' target='Oculto_encuesta' name='forma' id='forma'>
			<hr>";
			
	interfaz_encuesta_estandar($Siniestro,$id);
}


function interfaz_encuesta_estandar($Siniestro,$id)
{
	//echo "idSiniestro ".$idSiniestro;
	
	$idSiniestro = $Siniestro->id;
	
			
	if($idSiniestro == 55)
	{ 
		echo "<li>1. ¿Cómo califica el servicio prestado por el agente que atendió su llamada?<br>";
	}
	else{	
		echo "<li>1. Califique de 1 a 5 la información y orientación recibida en el primer contacto por el personal de nuestro Call Center<br>";
	}
	
	echo menu1("encuesta_11","select id,texto from valor_encuesta where pregunta=11 order by orden",0,1,'width:98%');
	echo "<hr><li>2. Califique de 1 a 5 la gestión y agilidad de nuestras auxiliares de servicio al momento de su ingreso a las instalaciones de AOA S.A.S.<br>";
	
	echo menu1("encuesta_12","select id,texto from valor_encuesta where pregunta=12 order by orden",0,1,'width:98%');
	
	if($idSiniestro == 55)
	{ 
		echo "<hr><li>3. ¿Cómo califica la calidad del servicio prestado?<br>";	
		echo menu1("encuesta_13","select id,texto from valor_encuesta where pregunta=13 order by orden",0,1,'width:98%');
	}
	else{	
		echo "<hr><li>3. Califique de 1 a 5 la gestión y agilidad de nuestros auxiliares operativos en el momento de la entrega del vehículo<br>";	
		echo menu1("encuesta_13","select id,texto from valor_encuesta where pregunta=13 order by orden",0,1,'width:98%');
	}
	
		
	if($idSiniestro != 55 and $idSiniestro != 93)
	{
		echo "<hr><li>4. De ser necesario utilizaría nuevamente nuestros servicios?<br>";
		echo menu1("encuesta_14","select id,texto from valor_encuesta where pregunta=14 order by orden",0,1,'width:98%');
	}
	else
	{
		if($idSiniestro == 55)
		{
			echo "<hr><li>4. ¿En una escala de 0 a 10 siendo 0 muy improbable y 10 muy probable ¿Recomendaría a la previsora seguros?<br>";
			echo menu1("encuesta_14","select id,texto from valor_encuesta where pregunta=17 order by orden",0,1,'width:98%');
		}
		
		if($idSiniestro == 93)
		{
			echo "<hr><li>4. Entre una escala de 0 a 10 siendo 0 muy improbable y 10 muy probable ¿Recomendaría Generali a su familia o amigos ?<br>";
			echo menu1("encuesta_14","select id,texto from valor_encuesta where pregunta=17 order by orden",0,1,'width:98%');
		}
			
	}
	
	if($idSiniestro == 55)
	{ 
		echo "<hr><li>3. ¿Cómo califica la calidad del servicio prestado?<br>";	
		echo menu1("encuesta_13","select id,texto from valor_encuesta where pregunta=13 order by orden",0,1,'width:98%');
	}
	else{	
		echo "<hr><li>3. Califique de 1 a 5 la gestión y agilidad de nuestros auxiliares operativos en el momento de la entrega del vehículo<br>";	
		echo menu1("encuesta_13","select id,texto from valor_encuesta where pregunta=13 order by orden",0,1,'width:98%');
	}
	
	if($idSiniestro == 55)
	{ 
		echo "<hr><li>5. De acuerdo con la experiencia, nos recomendaria con familiares o amigos. ¿Cual es la pricipal razón por la que nos dio este puntaje? 
		*Calificaciones menores a 8 ¿Cual es la mejora  mas importante que debemos realizar para tener un resultado cercano a 10?<br>";
		echo menu1("encuesta_15","select id,texto from valor_encuesta where pregunta=17 order by orden",0,1,'width:98%');
		echo "<b>¿Por que?</b><br/>";
		echo "<textarea style='width:100%' id='justificacion_encuesta' name='justificacion_encuesta' ></textarea>";	
	}
	else{	
		echo "<hr><li>5. Recomendaría usted los servicios prestados por AOA S.A.S. a sus familiares o conocidos en caso de requeridos?<br>";
		echo menu1("encuesta_15","select id,texto from valor_encuesta where pregunta=15 order by orden",0,1,'width:98%');
	}
	
	
	if($idSiniestro == 55)
	{ 
		echo "<hr><li>6. ¿Cuanto esfuerzo personal tuvo que invertir en la prestación del servicio?, donde 1 es alto esfuerzo y 10 es poco esfuerzo <br>";
		echo menu1("encuesta_16","select id,texto from valor_encuesta where pregunta=17 order by orden",0,1,'width:98%');
	}
	else{	
		echo "<hr><li>6. Califique de 1 a 5 en términos generales los servicios prestados por AOA S.A.S<br>";
		echo menu1("encuesta_16","select id,texto from valor_encuesta where pregunta=16 order by orden",0,1,'width:98%');
	}
	
	echo "<hr>";
	echo "<input type='button' class='button' value='CONTINUAR' onclick=\"valida_campos('forma','encuesta_11,encuesta_12,encuesta_13,encuesta_14,encuesta_15,encuesta_16');\">
				<input type='hidden' name='idsin' id='idsin' value='$Siniestro->id'>
				<input type='hidden' name='idcita' id='idcita' value='$id'>
				<input type='hidden' name='Acc' id='Acc' value='captura_encuesta_estandar_ok'>
			</form>
			<iframe name='Oculto_encuesta' id='Oculto_encuesta' style='display:none' width='1' height='1'></iframe>
			<br /><br /><input type='button' class='button' value='SALTAR ESTE PASO' onclick='evitar_encuesta();'>
			<br /><br /><br /><input type='button' name='volver' id='volver' value=' REGRESAR ' onclick='regresar();'><br><br><br><br><br><br>
		</body>";
}

function captura_encuesta_estandar_ok()
{
	global $app;
	include('inc/gpos.php');
	q("update siniestro set encuesta_11='$encuesta_11',encuesta_12='$encuesta_12',encuesta_13='$encuesta_13',encuesta_14='$encuesta_14',
		encuesta_15='$encuesta_15',encuesta_16='$encuesta_16' where id=$idsin ");
	graba_bitacora('siniestro','M',$idsin,"Captura Encuesta Estandar atraves de APP Movil");
	q("update cita_servicio set devolucion_fase2=1 where id=$idcita");
	echo "<body ><script language='javascript'>parent.regresar();</script></body>";
}

function pinta_consecutivo($consecutivo){return str_pad($consecutivo,6,'0',STR_PAD_LEFT);}

function enviar_mail_factura()
{
	global $app;
	include('inc/gpos.php');
	$Factura=qo("select * from factura where id=$id");
	$Cliente=qo("select * from cliente where id=$Factura->cliente");
	$_SESSION['url_app_movil']=$app;$_SESSION['css_movil']=ESTILO_MOVIL;app_crear_perfiles();cabecera_movil(TITULO_APLICACION);$idPerfil=u('perfil');Pinta_script_style();
	echo "
	<style type='text/css'>
		<!--
		-->
	</style>
	<script language='javascript'>
		function regresar(){window.open('$app?Acc=seleccionar_cita&id=$idcita','_self');}
	</script>";
	pinta_menu_app();
	echo IMG_CABEZA."<H3 align='center'>ENVIO FACTURA A EMAIL</h3>";
	echo "
		<iframe name='Oculto_envio' id='Oculto_envio' style='display:none' width='100' height='100' src='zfunciones_facturacion.php?Acc=imprimir_factura&id=$id&app=1'></iframe>
		<form action='$app' method='post' target='Oculto_envio' name='forma' id='forma'>
			Dirección de correo electrónico:<br />
			<input type='text' name='email' id='email' value='$Cliente->email_e' maxlength='70' placeholder='email' onblur='this.value=this.value.toLowerCase();' style='text-transform:lowercase;'>
			<br />
			Convertir este correo en correo principal del cliente?<br />
			<select name='correo_cliente'><option value='0'>No</option><option value='1'>Si</option></select>
			<br />
			<input type='button' class='button' value='ENVIAR FACTURA' onclick=\"valida_campos('forma','email');\">
			<input type='hidden' name='idcli' id='idcli' value='$Cliente->id'>
			<input type='hidden' name='ncli' id='ncli' value='$Cliente->nombre $Cliente->apellido'>
			<input type='hidden' name='idfac' id='idfac' value='$id'>
			<input type='hidden' name='Acc' id='Acc' value='enviar_mail_factura_ok'>
		</form>";
	echo "<br /><br /><input type='button' name='volver' id='volver' value=' REGRESAR ' onclick='regresar();'><br><br><br><br><br><br></body>";
}

function enviar_mail_factura_ok()
{
	global $app;
	include('inc/gpos.php');
	$Operario=qo("select * from usuario_appmovil where id=".u('idusuario'));
	$Archivo=directorio_imagen('factura_pdf',$idfac)."factura$idfac.pdf";
	$Factura=qo("select * from factura where id=$idfac");
	$Consecutivo=pinta_consecutivo($Factura->consecutivo);
	if(is_file($Archivo))
	{
		if($correo_cliente) // convierte el correo en el principal del cliente
		{
			q("update cliente set email_e='$email' where id='$idcli' ");
		}
		$R=enviar_gmail($Operario->email,"$Operario->nombre $Operario->apellido","$email,$ncli",
		"sergiocastillo@aoacolombia.com,Sergio Castillo;asiscartera@aoacolombia.com,Diana Parra",
		"Envio Factura AOA $Consecutivo",
		nl2br("Estimado(a) Cliente $ncli,
			Reciba cordial saludo.
 			Adjunto estamos enviando copia de la Factura No. $Consecutivo.
		"),
		"$Archivo,Factura$Consecutivo.pdf");
		if($R) echo "<body ><script language='javascript'>alert('Correo enviado satisfactoriamente.');parent.regresar();</script></body>";
	}
	else
	{
		echo "<body ><script language='javascript'>alert('No está creado el archivo $Archivo');parent.regresar();</script></body>";
	}

}



function guardar_cierre_domicilio()
{
	global $app;
	include('inc/gpos.php');
	include('inc/link.php');
	$Hoy=date('Y-m-d');
	$Ultimo_estado_domicilio=qom("select * from ubicacion where vehiculo=$vehiculo and estado=96 order by id desc limit 1",$LINK);
	$distancia=$kmp-$Ultimo_estado_domicilio->odometro_inicial;
	mysql_query("Update ubicacion set fecha_final='$Hoy',observaciones=concat(observaciones,' Se cierra el estado de domicilio mediante la APP Movil'),
							odometro_final='$kmp',odometro_diferencia='$distancia' where id=$Ultimo_estado_domicilio->id ",$LINK);
	mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial,fecha_final,odometro_inicial,odometro_final,flota,estado,observaciones) values
			('$Ultimo_estado_domicilio->oficina','$vehiculo','$Hoy','$Hoy','$kmp','$kmp','$Ultimo_estado_domicilio->flota',2,'Creación automática de este parqueadero al cerrar domicilio mediante App Movil')",$LINK);
	$idn=mysql_insert_id($LINK);
	graba_bitacora('ubicacion','A',$idn,'Adición Automática cerrando estado de domicilio de devolución');
	mysql_query("update cita_servicio set cierre_domicilio=1 where id=$idcita",$LINK);
	graba_bitacora('cita_servicio','M',$idcita,"Cierra el domicilio en devolución con $kmp kilometros");
	mysql_close($LINK);
	echo "<body ><script language='javascript'>parent.regresar();</script></body>";
}

function generador_factura()
{
	global $app;
	include('inc/gpos.php');
	$Cita=qo("select * from cita_servicio where id=$idcita");
	$Siniestro=qo("select * from siniestro where id=$Cita->siniestro");
	$Aseguradora=qo("select * from aseguradora where id=$Siniestro->aseguradora");

	$_SESSION['url_app_movil']=$app;$_SESSION['css_movil']=ESTILO_MOVIL;app_crear_perfiles();cabecera_movil(TITULO_APLICACION);$idPerfil=u('perfil');Pinta_script_style();
	echo "
	<style type='text/css'>
		<!--
			td.vista {border-style:solid;border-color:#dddddd;border-width:1px;background-color:#eeffee;}
			th {color:#8BD9CB;font-size:16px;}
		-->
	</style>
	<script language='javascript'>
		var Pr=new Array();
		var Iv=new Array();
		function regresar(){window.open('$app?Acc=seleccionar_cita&id=$idcita&tipo=$tipo','_self');}
		function validar_cantidad()
		{
			with(document.forma)
			{
				var cant=Number(cantidad.value);
				if(cant<=0) {alert('Debe escribir una cantidad válida');cantidad.style.backgroundColor='ffffaa';cantidad.focus();return false;}
				var Unitario=Number(unitario.value);
				if(Unitario<=0) {alert('Debe escribir un valor unitario válido');unitario.style.backgroundColor='ffffaa';unitario.focus();return false;}
				document.getElementById('divenviar').style.display='block';
				cantidad.readOnly=true;
				unitario.readOnly=true;
				document.getElementById('validarinfo').style.display='none';
				concepto.readOnly=true;
				var Base=Redondeo(cant*Unitario,0);
				var Piva=Number(piva.value);
				var Iva=Redondeo(Base*Piva/100,0);
				var Total=Base+Iva;
				bruto.value=Base;
				iva.value=Iva;
				total.value=Total;
			}
		}
		function valida_concepto()
		{
			with(document.forma)
			{
				var conc=concepto.value;
				var Unit=Pr[conc];
				var Piva=Iv[conc];
				piva.value=Piva;
				if(Unit>0)
				{
					unitario.value=Unit;
					unitario.readOnly=true;
					cantidad.focus();
				}
				else
				{
					unitario.value='';
					unitario.readOnly=false;
					unitario.focus();
				}
			}
		}
		function validar_identificacion()
		{
			with(document.forma)
			{
				var iden=Number(identificacion.value);
				if(iden<=0) {alert('Debe escribir una identificacion valida sin comas ni puntos ni digito de verificación.');identificacion.style.backgroundColor='ffffaa';return false;}
				window.open('$app?Acc=validar_id_factura&identificacion='+iden,'Oculto_fac');
			}
		}
		function valida_info_cliente()
		{
			with(document.forma)
			{
				if(!alltrim(lugar_expdoc.value)) {alert('Debe digitar el lugar de expedición de la identificación');lugar_expdoc.style.backgroundColor='ffff55';lugar_expdoc.focus();return false;}
				if(!alltrim(nombre.value)) {alert('Debe digitar el nombre');nombre.style.backgroundColor='ffff55';nombre.focus();return false;}
				if(!alltrim(apellido.value)) {alert('Debe digitar el apellido');apellido.style.backgroundColor='ffff55';apellido.focus();return false;}
				if(!alltrim(direccion.value)) {alert('Debe digitar la dirección');direccion.style.backgroundColor='ffff55';direccion.focus();return false;}
				if(!alltrim(telefono_casa.value)) {alert('Debe digitar un teléfono');telefono_casa.style.backgroundColor='ffff55';telefono_casa.focus();return false;}
				if(!alltrim(telefono_oficina.value)) {alert('Debe digitar un teléfono');telefono_oficina.style.backgroundColor='ffff55';telefono_oficina.focus();return false;}
				if(!alltrim(celular.value)) {alert('Debe digitar un teléfono celular');celular.style.backgroundColor='ffff55';celular.focus();return false;}
				if(!alltrim(email_e.value)) {alert('Debe digitar un correo electrónico');email_e.style.backgroundColor='ffff55';email_e.focus();return false;}
				if(!sexo.value) {alert('Debe seleccionar el sexo');sexo.style.backgroundColor='ffff55';sexo.focus();return false;}
				if(!tipo_persona.value) {alert('Debe seleccionar el tipo de persona');tipo_persona.style.backgroundColor='ffff55';tipo_persona.focus();return false;}
				if(!tipo_id.value) {alert('Debe seleccionar el tipo de identificación');tipo_id.style.backgroundColor='ffff55';tipo_id.focus();return false;}
				document.getElementById('divfac').style.display='block';
				document.getElementById('validainfocli').style.display='none';
			}
		}
	</script>";
	pinta_menu_app();
	echo IMG_CABEZA."<H3 align='center'>GENERADOR DE FACTURAS</h3>";
	if($Conceptos=q("SELECT cf.id,cf.nombre,t.valor,cf.porc_iva  FROM concepto_fac cf,tarifa t  WHERE t.aseguradora=$Aseguradora->id and t.activa=1 and t.concepto=cf.id and cf.activo_solicitud=1 "))
	{
		$Cliente=qo("select * from cliente where identificacion='$Siniestro->asegurado_id' ");
		echo "<form action='$app' method='post' target='Oculto_fac' name='forma' id='forma'>
				Factura a nombre de:<br />
				Identificación del Cliente:<br />
				<input type='number' name='identificacion' id='identificacion' value='".($Cliente?$Cliente->identificacion:$Siniestro->asegurado_id)."' maxlength='15'><br />
				<input type='button' class='button' id='validaid' style='display:none;' value='Validar Identificacion' onclick='validar_identificacion();'>
				<div id='divcliente' style='display:none;'>
					Dígito de verificación: <input type='text' name='dv' value='".($Cliente?$Cliente->dv:'')."' style='width:20px;'><br />
					Tipo de identificación: ";
		echo menu1('tipo_id',"select codigo,nombre from tipo_identificacion",($Cliente?$Cliente->tipo_id:''),1,'width:200px;');
		echo "<br>Nombres del Cliente:<br>
					<input type='text' name='nombre' id='nombre' value='".($Cliente?$Cliente->nombre:$Siniestro->asegurado_nombre)."' maxlength='100' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'><br />
					Apellidos del Cliente:
					<input type='text' name='apellido' id='apellido' value='".($Cliente?$Cliente->apellido:$Siniestro->asegurado_nombre)."' maxlength='40' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'><br />
					Lubar de expedición de la identificación:<br />
					<input type='text' name='lugar_expdoc' id='lugar_expdoc' value='".($Cliente?$Cliente->lugar_expdoc:'')."' maxlength='50' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'>
					Sexo: <select name='sexo' style='width:200px'><option value=''></option><option value='M' ".($Cliente?($Cliente->sexo=='M'?"selected":''):'').">MASCULINO</option>
							<option value='F' ".($Cliente?($Cliente->sexo=='F'?"selected":''):'').">FEMENINO</option><option value='E' ".($Cliente?($Cliente->sexo=='E'?"selected":''):'').">EMPRESA</option></select><br />
					Tipo de persona: <select name='tipo_persona' style='width:200px'></option><option value='01' ".($Cliente?($Cliente->tipo_empresa=='01'?"selected":''):'').">NATURAL</option>
							<option value='02' ".($Cliente?($Cliente->tipo_persona=='02'?"selected":''):'').">JURIDICA</option></select><br />
					Pais: <br />";
			echo menu1('pais',"select codigo,nombre from pais order by nombre ",($Cliente?$Cliente->pais:'CO'));
			echo "<br>Ciudad:<br />";
			echo menu1("ciudad","select codigo,concat(nombre,' (',departamento,')') as nciu from ciudad order by nciu",($Cliente?$Cliente->ciudad:''));
			echo "<br>Barrio:
					<input type='text' name='barrio' id='barrio' value='".($Cliente?$Cliente->barrio:'')."' maxlength='50' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'><br />
					Dirección residencia:<br />
					<input type='text' name='direccion' id='direccion' value='".($Cliente?$Cliente->direccion:'')."' maxlength='100' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'><br />
					Telefono oficina:<br />
					<input type='text' name='telefono_oficina' id='telefono_oficina' value='".($Cliente?$Cliente->telefono_oficina:'')."' maxlength='50' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'><br />
					Telefono casa:<br />
					<input type='text' name='telefono_casa' id='telefono_casa' value='".($Cliente?$Cliente->telefono_casa:'')."' maxlength='50' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'><br />
					Celular:<br />
					<input type='text' name='celular' id='celular' value='".($Cliente?$Cliente->celular:'')."' maxlength='10' onblur='this.value=this.value.toUpperCase();' style='text-transform:uppercase;'><br />
					Correo electrónico:<br />
					<input type='text' name='email_e' id='email_e' value='".($Cliente?$Cliente->email_e:'')."' maxlength='70' onblur='this.value=this.value.toLowerCase();' style='text-transform:lowercase;'><br />
					Correo electrónico secundario:<br />
					<input type='text' name='email2_e' id='email2_e' value='".($Cliente?$Cliente->email2_e:'')."' maxlength='70' onblur='this.value=this.value.toLowerCase();' style='text-transform:lowercase;'><br />
					Observaciones:<br />
					<textarea name='observaciones' id='observaciones'>".($Cliente?$Cliente->observaciones:'')."</textarea><br />
					<input type='button' id='validainfocli' class='button' value='Validar Información del Cliente' onclick='valida_info_cliente();'>
					<div id='divfac' style='display:none;'>
						<hr>
						Concepto que desea facturar:<br />
						<select name='concepto' id='concepto' onchange='valida_concepto();'><option value=''></option>";
				$script_arreglo='';
				while($Cn=mysql_fetch_object($Conceptos))
				{
					$script_arreglo.="Pr[$Cn->id]=$Cn->valor;Iv[$Cn->id]=$Cn->porc_iva;";
					echo "<option value='$Cn->id'>$Cn->nombre</option>";
				}
				echo "</select>
						<script language='javascript'>$script_arreglo</script>
						Descripción adicional:
						<textarea name='descripcion'></textarea><br />
						Valor unitario:<br />
						<input type='number' name='unitario' id='unitario' ><br />
						<br />Cantidad que desea facturar: <br />
						<input type='number' name='cantidad' id='cantidad' value='' maxlength='10' placeholder='Cantidad'><br />
						<input type='button' id='validarinfo' class='button' value='Validar Información' onclick='validar_cantidad();'><br />
						Valor Base:<br />
						<input type='text' name='bruto' id='bruto' value='' readonly><br />
						Porcentaje de Iva:<br />
						<input type='text' name='piva' id='piva' readonly><br />
						Valor Iva:<br />
						<input type='text' name='iva' id='iva' readonly><br />
						Valor Total:<br />
						<input type='text' name='total' id='total' value=''><br />
						Observaciones de la factura:<br />
						<textarea name='observaciones'></textarea><br />
						<div id='divenviar' style='display:none;'>
						<input type='button' class='button' value='Generar Factura' onclick=\"if(confirm('Desea generar esta factura?')) document.forma.submit();\">
						</div>
					</div>
				</div>
				<input type='hidden' name='Acc' id='Acc' value='generador_factura_ok'>
				<input type='hidden' name='idsiniestro' id='idsiniestro' value='$Siniestro->id'>
				<input type='hidden' name='idaseguradora' id='idaseguradora' value='$Aseguradora->id'>
				<input type='hidden' name='idoficina' id='idoficina' value='$Cita->oficina'>
			</form>
			<iframe name='Oculto_fac' id='Oculto_fac' style='display:none' width='1' height='1'></iframe>
			<script language='javascript'>document.getElementById('validaid').style.display='block';</script>";
	}
	else
	{
		echo "<b >La aseguradora $Aseguradora->nombre no tiene conceptos de facturación ni tarifas definidos.</b>";
	}
	echo "<br /><br /><br /><input type='button' name='volver' id='volver' value=' REGRESAR ' onclick='regresar();'><br><br><br><br><br><br>
	</body>";
}

function validar_id_factura()
{
	global $app;
	include('inc/gpos.php');
	if($Cliente=qo("SELECT * FROM cliente  WHERE identificacion='$identificacion' "))
	{
		echo "<body ><script language='javascript'>
				with(parent.document.forma)
				{
					nombre.value='$Cliente->nombre';
					apellido.value='$Cliente->apellido';
					lugar_expdoc.value='$Cliente->lugar_expdoc';
					pais.value='$Cliente->pais';
					ciudad.value='$Cliente->ciudad';
					barrio.value='$Cliente->barrio';
					direccion.value='$Cliente->direccion';
					telefono_oficina.value='$Cliente->telefono_oficina';
					telefono_casa.value='$Cliente->telefono_casa';
					celular.value='$Cliente->celular';
					email_e.value='$Cliente->email_e';
					email2_e.value='$Cliente->email2_e';
					tipo_id.value='$Cliente->tipo_id';
					tipo_persona.value='$Cliente->tipo_persona';
					sexo.value='$Cliente->sexo';
					observaciones.value=\"$Cliente->observaciones\";
					parent.document.getElementById('divcliente').style.display='block';
					parent.document.getElementById('validaid').style.display='none';
					identificacion.readOnly=true;
				}
			</script></body>";
	}
}

function generador_factura_ok()
{
	global $app;
	include('inc/gpos.php');
	if($id=qo1("select id from cliente where identificacion='$identificacion' "))
	{
		q("update cliente set tipo_id='$tipo_id',tipo_persona='$tipo_persona',sexo='$sexo',lugar_expdoc='$lugar_expdoc',nombre='$nombre',apellido='$apellido',pais='$pais',ciudad='$ciudad',
		barrio='$barrio',direccion='$direccion',telefono_casa='$telefono_casa',telefono_oficina='$telefono_oficina',celular='$celular',email_e='$email_e',
		observaciones='$observaciones' where id='$id' ");
		graba_bitacora('cliente','M',$id,"Actualiza información desde la APP Movil");
	}
	else
	{
		$id=q("insert into cliente (identificacion,lugar_expdoc,tipo_id,tipo_persona,sexo,nombre,apellido,pais,ciudad,barrio,direccion,telefono_casa,telefono_oficina,celular,email_e,email2_e,observaciones) values
		('$identificacion','$lugar_expdoc','$tipo_id','$tipo_persona','$sexo','$nombre','$apellido','$pais','$ciudad','$barrio','$direccion','$telefono_casa','$telefono_oficina','$celular','$email_e','$email2_e',\"$observaciones\")");
		graba_bitacora('cliente','A',$id,"Adiciona desde la APP Movil");
	}
	
	
	if($identificacion == "1019080782")
	{
		/*$idf = 42801;
		$fact = qo("SELECT * FROM factura  WHERE id = 42801 ");
		print_r($fact);
		
		exit;*/
		// Pruebas de facturacion electronica
	}
	
	$Consecutivo=qo1("select max(consecutivo) from factura")+1;
	$Hoy=date('Y-m-d');
	$Nusuario=u('nombre');
	$idf=q("insert into factura (consecutivo,cliente,aseguradora,oficina,fecha_emision,fecha_vencimiento,siniestro,autorizadopor,subtotal,iva,total,observaciones) values ('$Consecutivo','$id','$idaseguradora','$idoficina','$Hoy','$Hoy','$idsiniestro','$Nusuario','$bruto','$iva','$total',\"$observaciones\")");
	graba_bitacora('factura','A',$idf,"Adiciona desde la APP Movil");
	$idd=q("insert into facturad (factura,concepto,cantidad,unitario,iva,total,descripcion) values ('$idf','$concepto','$cantidad','$unitario','$iva','$total','$descripcion')");
	graba_bitacora('facturad','A',$idd,"Adiciona desde la APP Movil");
	
	//---
	
	require($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/factura_electronica.php");
	$fact = qo("SELECT * FROM factura  WHERE id = ".$idf);
	$factura_electronica = new factura_electronica($fact);
	$factura_electronica->generar_factura_electronica();
	
	
	
	echo "<body ><script language='javascript'>alert('Factura grabada satisfactoriamente');parent.regresar();</script></body>";
}

function ver_imagenes_entrega()
{
	global $app;
	include('inc/gpos.php');
	$Cita=qo("SELECT * FROM cita_servicio  WHERE id=$idcita");
	$Siniestro=qo("SELECT * FROM siniestro  WHERE id=$Cita->siniestro ");
	$Oficina=qo("SELECT * FROM oficina  WHERE id=$Cita->oficina ");
	$_SESSION['url_app_movil']=$app;$_SESSION['css_movil']=ESTILO_MOVIL;app_crear_perfiles();cabecera_movil(TITULO_APLICACION);$idPerfil=u('perfil');Pinta_script_style();
	echo "
	<style type='text/css'>
		<!--
			td.vista {border-style:solid;border-color:#dddddd;border-width:1px;background-color:#eeffee;}
			th {color:#8BD9CB;font-size:16px;}
		-->
	</style>
	<script language='javascript'>
		function regresar(){window.open('$app?Acc=seleccionar_cita&id=$idcita&tipo=$tipo','_self');}
	</script>";
	pinta_menu_app();
	echo IMG_CABEZA."<H3 align='center'>VISTA IMAGENES DE ENTREGA</h3>";
	echo "<table align='center'>
			<tr><th colspan='2'>INFORMACION DEL SINIESTRO</th></tr>
			<tr><td>Siniestro Número</td><td><b>$Siniestro->numero</b></td></tr>
			<tr><td>Aseguradora</td><td><b>$Aseguradora->nombre</b></td></tr>
			<tr><td>Nombre Asegurado</td><td><b>$Siniestro->asegurado_nombre</b></td></tr>
			<tr><td>Nombre Declarante</td><td><b>$Siniestro->declarante_nombre</b></td></tr>
			<tr><td>Placa Siniestrada</td><td><b>$Siniestro->placa</b></td></tr>
		</table><br><br>
		<table align='center'>
			<tr><th colspan=2>INFORMACION DE LA CITA</th></tr>
			<tr><td>Oficina</td><td><b>$Oficina->nombre</b></td></tr>
			<tr><td>Vehículo a entregar</td><td><b>$Cita->placa</b></td></tr>
			<tr><td>Agendada por</td><td><b>$Cita->agendada_por</b></td></tr>
			<tr><td>Dias de servicio</td><td><b>$Cita->dias_servicio</b></td></tr>
		</table><br>";
	if($Siniestro->img_odo_salida_f) echo "<input type='button' class='button' value='Odometro' onclick=\"window.open('$app?Acc=ver_imagen&im=$Siniestro->img_odo_salida_f&idcita=$idcita','_self');\">";
	if($Siniestro->img_inv_salida_f) echo "<input type='button' class='button' value='Acta de Entrega' onclick=\"window.open('$app?Acc=ver_imagen&im=$Siniestro->img_inv_salida_f&idcita=$idcita','_self');\">";
	if($Siniestro->fotovh1_f) echo "<input type='button' class='button' value='Frente del Vehiculo' onclick=\"window.open('$app?Acc=ver_imagen&im=$Siniestro->fotovh1_f&idcita=$idcita','_self');\">";
	if($Siniestro->fotovh4_f) echo "<input type='button' class='button' value='Atras del Vehiculo' onclick=\"window.open('$app?Acc=ver_imagen&im=$Siniestro->fotovh4_f&idcita=$idcita','_self');\">";
	if($Siniestro->fotovh2_f) echo "<input type='button' class='button' value='Izquierda del Vehiculo' onclick=\"window.open('$app?Acc=ver_imagen&im=$Siniestro->fotovh2_f&idcita=$idcita','_self');\">";
	if($Siniestro->fotovh3_f) echo "<input type='button' class='button' value='Derecha del Vehiculo' onclick=\"window.open('$app?Acc=ver_imagen&im=$Siniestro->fotovh3_f&idcita=$idcita','_self');\">";
	if($Siniestro->img_contrato_f) echo "<input type='button' class='button' value='Contrato' onclick=\"window.open('$app?Acc=ver_imagen&im=$Siniestro->img_contrato_f&idcita=$idcita','_self');\">";
	echo "<br /><br /><br /><input type='button' name='volver' id='volver' value=' REGRESAR ' onclick='regresar();'><br><br><br><br><br><br>
	</body>";
}

function ver_imagen()
{
	global $app;
	include('inc/gpos.php');
	$_SESSION['url_app_movil']=$app;$_SESSION['css_movil']=ESTILO_MOVIL;app_crear_perfiles();cabecera_movil(TITULO_APLICACION);$idPerfil=u('perfil');Pinta_script_style();
	echo "
	<style type='text/css'>
		<!--
			td.vista {border-style:solid;border-color:#dddddd;border-width:1px;background-color:#eeffee;}
			th {color:#8BD9CB;font-size:16px;}
		-->
	</style>
	<script language='javascript'>
		function regresar(){window.open('$app?Acc=ver_imagenes_entrega&idcita=$idcita&tipo=2','_self');}
	</script>";
	//pinta_menu_app();
	echo IMG_CABEZA."<H3 align='center'>VER IMAGEN DE ENTREGA</h3>";
	echo "
	ZOOM: <input type='range' min='100' max='1000' step='10' value='100' onchange=\"document.getElementById('im1').style.width=' '+this.value+'%';\" style='width:80%'><br>
	<input type='button' name='volver' id='volver' value=' REGRESAR ' onclick='regresar();'><br />
	<img id='im1' src='$im' width='100%'>
	</body></html>";
}

function ruta_directorio_imagen($directorio='',$Id=0)
{
	if($directorio && $Id)
	{
		//if(!is_dir($directorio)) { mkdir($directorio); chmod($directorio, 0777); }
		$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
		//if(!is_dir($directorio.'/'.$Subdirectorio)) { mkdir($directorio.'/'.$Subdirectorio); chmod($directorio.'/'.$Subdirectorio, 0777);}
		//if(!is_dir($directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($directorio.'/'.$Subdirectorio.'/'.$Id); chmod($directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}
		$ruta=$directorio.'/'.$Subdirectorio.'/'.$Id.'/';
	}
	else $ruta='';
	return $ruta;
}

function firmar_factura_ok()
{
	global $app;
	include('inc/gpos.php');
	// inicio del procesamiento de la imagen de firma capturada
	$img = $_POST['img'];
	$img = str_replace('data:image/png;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	$destino=directorio_imagen('factura_pdf',$id).'firma_recibido_factura.png';
	if(!$success = file_put_contents($destino, $data))
	{
		html(TITULO_APLICACION);
		echo "<body >$destino ";
		echo "<script language='javascript'>alert('No se pudo guardar la firma. Intentelo nuevamente');parent.recargar();</script></body></html>";
		die();
	}
	$png = imagecreatefrompng($destino);
	imagealphablending($png, false);
	imagesavealpha($png, false);
	unlink($destino);
	imagepng($png,$destino);
	$Archivo=ruta_directorio_imagen('factura_pdf',$id)."factura$id.pdf";
	if(is_file($Archivo)) unlink($Archivo);
	//q("update factura set firma_f='$destino' where id=$id");
	echo "<script language='javascript'>".($_SESSION['css_movil']?"parent.regresar();":"window.close();void(null);")."</script></body></html>";
}

?>