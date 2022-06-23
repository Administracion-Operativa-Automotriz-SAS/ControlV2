<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==323) { modal2('marcoindex.php?Acc=cambio_pass',0,0,500,500,'win'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==325) { modal2('zconsulta_siniestro1.php',0,0,800,1000,'win'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==326) { modal2('zcontrol_operativo3.php',0,0,0,0,'win'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==348) { modal2('zmonitor_operativo_aseguradora.php',0,0,700,1000,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==322) { modal2('zcalculo_tiempos.php',0,0,50,50,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==324) { modal2('reportes2.php',0,0,0,0,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
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
			<tr><td align='center' style='font-size:14px'><b>MENU PRINCIPAL</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(323);'>Cambio de contrase?a</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(325);'>CONSULTA SINIESTROS</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(326);'>CONTROL</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(348);'>Monitor x Aseguradora</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">MENU PRINCIPAL</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>REPORTES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(322);'>Calculo de Tiempos</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(324);'>Reportes</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">REPORTES</a></b></center></td></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal2('zconsulta_siniestro1.php',0,0,800,1000,'win'); ">
				<img src='img/grua.png' width='48' border='0'>
				<span style='width:300px'>CONSULTA SINIESTROS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcontrol_operativo3.php',0,0,0,0,'win'); ">
				<img src='img/control.png' width='48' border='0'>
				<span style='width:300px'>CONTROL<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zmonitor_operativo_aseguradora.php',0,0,700,1000,'destino'); ">
				<img src='imagenes/000/346/monitor_aseguradora.png' width='48' border='0'>
				<span style='width:300px'>Monitor x Aseguradora<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcalculo_tiempos.php',0,0,50,50,'destino'); ">
				<img src='imagenes/294/carpeta.png' width='48' border='0'>
				<span style='width:300px'>Calculo de Tiempos<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('reportes2.php',0,0,0,0,'destino'); ">
				<img src='imagenes/datos/impresora.png' width='48' border='0'>
				<span style='width:300px'>Reportes<br></span></a>
				</td><td>
		<iframe name='destino' id='destino' src='bienvenida_aguila7.php' frameborder='no' height='600' width='99%' scrolling='auto'></iframe>
		</td></tr></table>
		<table width='100%' cellspacing='0' cellpadding='0'><tr><td>
			<a class='sinfo' style='cursor:pointer' onclick="mata_perfil();"><img src='gifs/standar/stop_16.png' border=0><span>Cerrar Sesión</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick="window.open('marcoindex.php','_self');"><img src='gifs/standar/home_16.png' border=0><span style='width:100px'>Ir al inicio de la aplicacion</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick='retrocede_destino()'><img src='gifs/standar/izquierda.png' border=0><span style='width:100px'>Retroceder.</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick='avanza_destino()'><img src='gifs/standar/derecha.png' border=0><span style='width:100px'>Avanzar.</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick="window.open('marcoindex.php?Acc=reconstruye_inicio_perfil','destino');"><img src='gifs/standar/Recycle.png' border=0>
				<span style='width:200px'>Reconstruir el menu principal.</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick="modal('helpdesk.php',0,0,500,700,'helpinsert');"><img src='gifs/helpdesk.png' border='0' height='16px'><span style='width:200px'>Crear Tiket Help Desk</span></a></td>
		<td>
		<?php
		/* busqueda del cambio de perfil */
		if(is_file('inc/cp/'.$_SESSION['Nick'].'.php')) include_once('inc/cp/'.$_SESSION['Nick'].'.php');
		include_once('inc/misreportes.php');
		?></td><td>
		<i>Desarrollado en: Aguila 8.0 Diciembre de 2010 &reg <a href='mailto:administracion@intercolombia.net'>Arturo Quintero R.</a></i></td>