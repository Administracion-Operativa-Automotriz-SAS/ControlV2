<?
include('inc/funciones_.php');

sesion();
html();


echo "<script>
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
</script>";
echo "
	<body bgcolor='ffffff'>
	<table width='100%'><TR><td align='center'><img src='img/LOGO_AOA_200.png' ></td></TR></table>
	<br />
	<table width='80%' align='center' border bgcolor='#dddddd' cellpadding='20'><tr><td align='center'>
	<p align='center'><FONT COLOR='#0099cc' style='font-size:14px'><b>".NOMBRE_APLICACION."</B></FONT></p>
	</td></tr></table><br />
	<!-- <center><embed style='z-index:1' src='img/aoa_flash2.swf' type='application/x-shockwave-flash' name='Objeto_flash' align='middle' height='160px' width='340px'></center> -->
	<br><br>
	<table border=0 align='center' cellspacing=3 cellpadding=5><tr>
	<td bgcolor='eeeeff'>Usuario: <b>".$_SESSION['Nick']."</b></td>
	<td bgcolor='eeeeff'>Nombre: <b>".$_SESSION['Nombre']."</b></td>
	<td bgcolor='eeeeff'>Perfil de seguridad: <b>".$_SESSION['Ngrupo']."</b></td>
	</tr></table><br /><br />";
//IF($_SESSION['User']!=11 && $_SESSION['User']!=8 && $_SESSION['User']!=29)
		//echo "<center><iframe name='mapa' width=850 height=650 src='http://www.aoasemuevecontigo.com/googlemaps_aoa.php'></iframe></center>";
if($_SESSION['User']==6)
{
	if($Pendientes=qo1("select count(id) from solicitud_factura where procesado_por='' "))
	{
		echo "<script language='javascript'>alert('Hay Solicitudes de Facturaci�n pendientes por procesar');</script>";
	}
}
echo "<br>
	<br>
	<br>
	<h4 align='center' style='font-size:12'><i>(Desarrollado en Aguila v.9 Julio de 2012)</i><br />
	Dise&ntilde;ado y desarrollado por Tecnologia AOA. it@aoacolombia.co</h4>";
if($_SESSION['Disenador']==1)
	echo "<hr>Base de datos: ".MYSQL_D." Usuario:".MYSQL_U."<hr>";
echo "</body></html>";
?>