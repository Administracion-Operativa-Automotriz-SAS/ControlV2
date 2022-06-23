<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==414) { modal2('zcontrol_custodia_garantia.php',0,0,0,0,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==417) { modal2('zcartera.php',0,0,750,1000,'win4'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==413) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=413',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
				}
				function opciones_3(Dato)
			{
			if(Dato==403) { modal2('zcitas.php',0,0,0,0,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					
				}
				var Movil=<?=(browser_movil()?1:0)?>;
	</script><body topmargin=1 leftmargin=1 rightmargin=1 bottommargin=1
				onload='fija_destino();' onresize='fija_destino();' bgcolor='#ddddff'>
				<table id='Menu_Principal' name='Menu_Principal' border=0 cellspacing=1 width='' bgcolor='ddddff'><tr><td width='150' 
								onmouseover="document.getElementById('sp_1').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_1').style.visibility='visible';"
								onmouseout="document.getElementById('sp_1').style.visibility='hidden';" align='left'>
			<span id='sp_1' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>AUTORIZACIONES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(414);'>Control Garantias</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">AUTORIZACIONES</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CARTERA</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(417);'>Control de Cartera</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(413);'>Recibo de Caja</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">CARTERA</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_3').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_3').style.visibility='visible';"
								onmouseout="document.getElementById('sp_3').style.visibility='hidden';" align='left'>
			<span id='sp_3' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>OPERATIVO</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_3(403);'>Citas del dia</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_3').style.visibility='visible';">OPERATIVO</a></b></center></td></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal2('http://www.movilidadbogota.gov.co/SIMUR/INFO/',0,0,700,800,'win2'); ">
				<img src='imagenes/000/451/comparendo_electronico.png' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendo Electronico<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcontrol_custodia_garantia.php',0,0,0,0,'destino'); ">
				<img src='imagenes/000/414/dinero4.png' width='48' border='0'>
				<span style='width:300px'>Control Garantias<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcartera.php',0,0,750,1000,'win4'); ">
				<img src='imagenes/000/417/portafolio.png' width='48' border='0'>
				<span style='width:300px'>Control de Cartera<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcitas.php',0,0,0,0,'destino'); ">
				<img src='imagenes/182/anotacion.png' width='48' border='0'>
				<span style='width:300px'>Citas del dia<br></span></a>
				</td><td>
		<iframe name='destino' id='destino' src='bienvenida_aguila7.php' frameborder='no' height='600' width='99%' scrolling='auto'></iframe>
		</td></tr></table>
		<table width='100%' cellspacing='0' cellpadding='0'><tr><td>
			<a class='sinfo' style='cursor:pointer' onclick="mata_perfil();"><img src='gifs/standar/stop_16.png' border=0><span>Cerrar Sesión</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick="window.open('marcoindex.php','_self');"><img src='gifs/standar/home_16.png' border=0><span style='width:100px'>Ir al inicio de la aplicacion</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick='retrocede_destino()'><img src='gifs/standar/izquierda.png' border=0><span style='width:100px'>Retroceder.</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick='avanza_destino()'><img src='gifs/standar/derecha.png' border=0><span style='width:100px'>Avanzar.</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick="window.open('marcoindex.php?Acc=reconstruye_inicio_perfil','destino');"><img src='gifs/standar/Recycle.png' border=0>
				<span style='width:200px'>Reconstruir el menu principal.</span></a></td><td><a class='sinfo' style='cursor:pointer' onclick="window.open('marcoindex.php?Acc=cambio_pass','destino');"><img src='gifs/standar/candado2.png' height='18' border='0'>
				<span style='width:200px'>Cambiar contraseña</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick="modal('helpdesk.php',0,0,500,700,'helpinsert');"><img src='gifs/helpdesk.png' border='0' height='16px'><span style='width:200px'>Crear Tiket Help Desk</span></a></td>
		<td>
		<?php
		/* busqueda del cambio de perfil */
		if(is_file('inc/cp/'.$_SESSION['Nick'].'.php')) include_once('inc/cp/'.$_SESSION['Nick'].'.php');
		include_once('inc/misreportes.php');
		?></td><td>
		<i>Desarrollado en: Aguila 8.0 Diciembre de 2010 &reg <a href='mailto:administracion@intercolombia.net'>Arturo Quintero R.</a></i></td>