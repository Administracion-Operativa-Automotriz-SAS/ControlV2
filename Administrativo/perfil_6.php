<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==308) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=308',0,0,800,1000,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==311) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=311',0,0,800,1000,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==372) { modal2('zactivos_fijos.php',0,0,0,0,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==310) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=310',0,0,800,1000,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==309) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=309',0,0,800,1000,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==312) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=312',0,0,700,500,'win4'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==313) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=313',0,0,700,500,'win5'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==421) { modal2('administracion_zcalidad.php',0,0,600,900,'win2'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
				}
				function opciones_3(Dato)
			{
			if(Dato==314) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=314',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					
				}
				function opciones_4(Dato)
			{
			if(Dato==336) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=336',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					
				}
				var Movil=<?=(browser_movil()?1:0)?>;
	</script><body topmargin=1 leftmargin=1 rightmargin=1 bottommargin=1
				onload='fija_destino();' onresize='fija_destino();' bgcolor='#ddddff'>
				<table id='Menu_Principal' name='Menu_Principal' border=0 cellspacing=1 width='' bgcolor='ddddff'><tr><td width='200' 
								onmouseover="document.getElementById('sp_1').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_1').style.visibility='visible';"
								onmouseout="document.getElementById('sp_1').style.visibility='hidden';" align='left'>
			<span id='sp_1' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>ACTIVOS FIJOS</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(308);'>ACTIVOS</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(311);'>ASIGNACION DE ACTIVOS</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(372);'>Centro de Control Activos Fijos</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(310);'>DEPRECIACIONES ACTIVOS</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(309);'>MANTENIMIENTO ACTIVOS</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(312);'>[CFG] Marcas de Activos</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(313);'>[CFG] Tipo Activos</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">ACTIVOS FIJOS</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CALIDAD</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(421);'>SISTEMA DE GESTION DE CALIDAD</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">CALIDAD</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_3').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_3').style.visibility='visible';"
								onmouseout="document.getElementById('sp_3').style.visibility='hidden';" align='left'>
			<span id='sp_3' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>GESTION HUMANA</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_3(314);'>CONTRATOS</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_3').style.visibility='visible';">GESTION HUMANA</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_4').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_4').style.visibility='visible';"
								onmouseout="document.getElementById('sp_4').style.visibility='hidden';" align='left'>
			<span id='sp_4' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>PROVEEDORES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_4(336);'>CUENTAS POR PAGAR - FACTURAS</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_4').style.visibility='visible';">PROVEEDORES</a></b></center></td></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=308',0,0,800,1000,'destino'); ">
				<img src='imagenes/000/308/computadora.png' width='48' border='0'>
				<span style='width:300px'>ACTIVOS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('administracion_zcalidad.php',0,0,600,900,'win2'); ">
				<img src='imagenes/000/416/dicono_f_calidad.png' width='48' border='0'>
				<span style='width:300px'>SISTEMA DE GESTION DE CALIDAD<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=336',0,0,800,1000,'destino'); ">
				<img src='imagenes/152/folder.png' width='48' border='0'>
				<span style='width:300px'>CUENTAS POR PAGAR - FACTURAS<br></span></a>
				</td><td>
		<iframe name='destino' id='destino' src='bienvenida_aguila7.php' frameborder='no' height='600' width='99%' scrolling='auto'></iframe>
		</td></tr></table>
		<table width='100%' cellspacing='0' cellpadding='0'><tr><td>
			<a class='sinfo' style='cursor:pointer' onclick="mata_perfil();"><img src='gifs/standar/stop_16.png' border=0><span>Cerrar Sesión</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick="mata_v_sesion();"><img src='gifs/standar/home_16.png' border=0><span style='width:100px'>Ir al inicio de la aplicacion</span></a></td>
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