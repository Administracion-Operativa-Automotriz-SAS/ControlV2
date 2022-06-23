<?php
//DIE('APLICACION EN SUSPENCION . Atte. Gestion de Tecnologia y Planeacion.');
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include('inc/gpos.php');
if(!$SESION_PUBLICA) require('inc/sess.php');
include_once('inc/funciones_.php');

prepara_rutinas($Acc);
//verificar_directorios();

if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}
html(TITULO_APLICACION.' - '.$_SESSION['Nombre']);

setcookie("sessioncontrol", "viva", time() + (60 * 1));

if (isset($_SESSION['modulo']) or $_SESSION['modulo'] != null) {
	if ($_SESSION['modulo']=='admin') {
		session_unset();session_destroy();
        echo "<script language='javascript'>
		window.close();
		parent.window.location.reload(true);
		location.href = 'https://app.aoacolombia.com/intranet/control.php?Acc=ingreso_sistema&SESION_PUBLICA=1';
		</script>		
        ";
	}
	$_SESSION['modulo']="control";
	setcookie('modulo', 'control', time() + (86400 * 30), "/");
}
else {
	$_SESSION['modulo']="control";
	setcookie('modulo', 'control', time() + (86400 * 30), "/");
}
echo"
<script src='https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js'></script>
<script language='javascript'>
setInterval(buscar, 1000);
function buscar() {
	var name = document.cookie.match(/PHPSESSID=[^;]+/);
	var session = 'echo $_SESSION ';
	var modulo = Cookies.get('modulo');
	var aoasession = Cookies.get('sessioncontrol');
	// console.log(name[0]);
	// console.log(session[0]);
	
	if(session == null){
		reloadpageaoa();
	}
	if (name[0] == null) {
		reloadpageaoa();
	}
	if (modulo == 'admin') {
		reloadpageaoa();
	}
	if (modulo == 'admin') {
		reloadpageaoa();
	}
	   
   
}

var registrarInactividad = function () {
    var t;
    window.onload = reiniciarTiempo;
    // Eventos del DOM
    document.onmousemove = reiniciarTiempo;
    document.onkeypress = reiniciarTiempo;
    document.onload = reiniciarTiempo;
    document.onmousemove = reiniciarTiempo;
    document.onmousedown = reiniciarTiempo; // aplica para una pantalla touch
    document.ontouchstart = reiniciarTiempo;
    document.onclick = reiniciarTiempo;     // aplica para un clic del touchpad
    document.onscroll = reiniciarTiempo;    // navegando con flechas del teclado
    document.onkeypress = reiniciarTiempo;

    function tiempoExcedido() {
		reloadpageaoa();
	}

    function reiniciarTiempo() {
        clearTimeout(t);
        // t = setTimeout(tiempoExcedido, 300000);
        // 1000 milisegundos = 1 segundo
    }
};

registrarInactividad(); //Esto activa el contador

function reloadpageaoa(){
	console.log('no');
	document.body.innerHTML = 'LOGUATE';
	console.log('no');
	parent.window.location.reload(true);
	window.close();
	location.href = 'https://app.aoacolombia.com/intranet/control.php?Acc=ingreso_sistema&SESION_PUBLICA=1';
	return false;
}
</script>
";
inicio();
die();


function verificar_directorios()
{
	if(!is_dir('imagenes')) mkdir('imagenes',0777);
	if(!is_dir('imagenes/reportes')) mkdir('imagenes/reportes',0777);
	if(!is_dir('imagenes/datos')) mkdir('imagenes/datos',0777);
}






?>

