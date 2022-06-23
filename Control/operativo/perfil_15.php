<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==726) { modal2('zp1.php',0,0,0,0,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==723) { modal2('m.aoacontrol.php?Acc=mata_perfil_movil',0,0,0,0,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==725) { modal2('m.aoacontrol.php?Acc=citas',0,0,0,0,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==724) { modal2('m.aoacontrol.php?Acc=enviar_notificacion',0,0,0,0,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==722) { modal2('m.aoacontrol.php?Acc=modificar_perfil',0,0,0,0,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
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
			<tr><td align='center' style='font-size:14px'><b>DESARROLLO</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(726);'>AREA DE PRUEBAS</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">DESARROLLO</a></b></center></td></tr><tr><td width='150' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>MENU PRINCIPAL</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(723);'>Cerrar Sesion</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(725);'>CITAS</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(724);'>Enviar Notificacion</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(722);'>Mi Perfil</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">MENU PRINCIPAL</a></b></center></td></tr><tr></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal2('m.aoacontrol.php?Acc=mata_perfil_movil',0,0,0,0,'destino'); ">
				<img src='imagenes/000/720/dicono_f_cerrarsesion.png' width='48' border='0'>
				<span style='width:300px'>Cerrar Sesion<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('m.aoacontrol.php?Acc=citas',0,0,0,0,'destino'); ">
				<img src='imagenes/000/725/dicono_f_anotacion.png' width='48' border='0'>
				<span style='width:300px'>CITAS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('m.aoacontrol.php?Acc=modificar_perfil',0,0,0,0,'destino'); ">
				<img src='imagenes/000/722/dicono_f_user.png' width='48' border='0'>
				<span style='width:300px'>Mi Perfil<br></span></a>
				</td><td>
		<iframe name='destino' id='destino' src='bienvenida_aguila7.php' frameborder='no' height='600' width='99%' scrolling='auto'></iframe>
		</td></tr></table>
		<table width='100%' cellspacing='0' cellpadding='0'><tr><td>
			<a class='sinfo' style='cursor:pointer' onclick="mata_perfil();"><img src='gifs/standar/stop_16.png' border=0><span>Cerrar Sesi&oacute;n</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick="window.open('marcoindex.php','_self');"><img src='gifs/standar/home_16.png' border=0><span style='width:100px'>Ir al inicio de la aplicacion</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick='retrocede_destino()'><img src='gifs/standar/izquierda.png' border=0><span style='width:100px'>Retroceder.</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick='avanza_destino()'><img src='gifs/standar/derecha.png' border=0><span style='width:100px'>Avanzar.</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick="window.open('marcoindex.php?Acc=reconstruye_inicio_perfil','destino');"><img src='gifs/standar/Recycle.png' border=0>
				<span style='width:200px'>Reconstruir el menu principal.</span></a></td><td><a class='sinfo' style='cursor:pointer' onclick="window.open('marcoindex.php?Acc=cambio_pass','destino');"><img src='gifs/standar/candado2.png' height='18' border='0'>
				<span style='width:200px'>Cambiar contrase&ntilde;a</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick="modal('helpdesk.php',0,0,500,700,'helpinsert');"><img src='gifs/helpdesk.png' border='0' height='16px'><span style='width:200px'>Crear Tiket Help Desk</span></a></td>
		<td>
		<?php
		/* busqueda del cambio de perfil */
		if(is_file('inc/cp/'.$_SESSION['Nick'].'.php')) include_once('inc/cp/'.$_SESSION['Nick'].'.php');
		include_once('inc/misreportes.php');
		?></td><td>
		<i>	Desarrollado por: Tecnologia AOA Diciembre de 2010 &reg</i></td>