	
<?php
if(!$SESION_PUBLICA) require('inc/sess.php');
include_once('inc/funciones_.php');
prepara_rutinas($Acc);
//verificar_directorios();

if(!empty($Acc) && function_exists($Acc)) {
	
	eval($Acc.'();');die();
	
	}

html(TITULO_APLICACION.' - '.$_SESSION['Nombre']);
if (isset($_SESSION['modulo']) or $_SESSION['modulo'] != null) {

	if ($_SESSION['modulo']=='control') {
	
		session_unset();session_destroy();
        echo "<script language='javascript'>
		window.close();
		parent.window.location.reload(true);
		location.href = 'https://app.aoacolombia.com/intranet/admin.php?Acc=ingreso_sistema&SESION_PUBLICA=1';
		</script>		
        ";
	}
	$_SESSION['modulo']="admin";
	setcookie('modulo', 'admin', time() + (86400 * 30), "/");
}
else {

	$_SESSION['modulo']="admin";
	setcookie('modulo', 'admin', time() + (86400 * 30), "/");
}
echo"
<script src='https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js'></script>
<script language='javascript'>
setInterval(buscar, 1000);
function buscar() {
	var name = document.cookie.match(/PHPSESSID=[^;]+/);
	var modulo = Cookies.get('modulo');
	if (name == null) {
		console.log('no');
		document.body.innerHTML = 'LOGUATE';
		console.log('no');
		parent.window.location.reload(true);
		window.close();
		location.href = 'https://app.aoacolombia.com/intranet/admin.php?Acc=ingreso_sistema&SESION_PUBLICA=1';
		return false;
	}
	if (modulo == 'control') {
		console.log('no');
		document.body.innerHTML = 'LOGUATE';
		console.log('no');
		parent.window.location.reload(true);
		window.close();
		location.href = 'https://app.aoacolombia.com/intranet/admin.php?Acc=ingreso_sistema&SESION_PUBLICA=1';
		return false;
	}  
   
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