<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==675) { modal2('http://app.aoacolombia.com/Administrativo/administracion_zcalidad.php',0,0,600,900,'win3'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==405) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=405',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
				}
				function opciones_3(Dato)
			{
			if(Dato==732) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=732',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==491) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=491',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					
				}
				function opciones_4(Dato)
			{
			if(Dato==546) { modal2('manual.php',0,0,600,800,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					
				}
				function opciones_5(Dato)
			{
			if(Dato==598) { modal2('zsiniestro.php',0,0,0,0,'win5'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==559) { modal2('zpqr.php',0,0,800,800,'_blank'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==358) { modal2('https://consultas.transitobogota.gov.co:8010/publico/index3.php',0,0,700,900,'win3'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==408) { modal2('http://www.transitobucaramanga.gov.co/pendientes.php',0,0,700,900,'win3'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==310) { modal2('zcontrol_operativo3.php',0,0,700,900,'win2'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==635) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=635',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					
				}
				function opciones_6(Dato)
			{
			if(Dato==307) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=307',0,0,800,1000,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					if(Dato==309) { modal2('zcitas.php',0,0,0,0,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					if(Dato==308) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=308',0,0,800,1000,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					if(Dato==312) { modal2('zalistamiento.php?Acc=estado_pendientes',0,0,0,0,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					if(Dato==314) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=314',0,0,800,1000,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					if(Dato==313) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=313',0,0,800,1000,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					
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
			<tr><td align='center' style='font-size:14px'><b>CALIDAD</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(675);'>SISTEMA DE GESTION DE CALIDAD</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">CALIDAD</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CALLCENTER</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(405);'>SINIESTROS</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">CALLCENTER</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_3').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_3').style.visibility='visible';"
								onmouseout="document.getElementById('sp_3').style.visibility='hidden';" align='left'>
			<span id='sp_3' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>FLOTAS</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_3(732);'>[CFG] Alerta x Vehiculo</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(491);'>[CFG] Concepto Control Factura</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_3').style.visibility='visible';">FLOTAS</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_4').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_4').style.visibility='visible';"
								onmouseout="document.getElementById('sp_4').style.visibility='hidden';" align='left'>
			<span id='sp_4' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>MANUAL</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_4(546);'>MANUAL DE USUARIO</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_4').style.visibility='visible';">MANUAL</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_5').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_5').style.visibility='visible';"
								onmouseout="document.getElementById('sp_5').style.visibility='hidden';" align='left'>
			<span id='sp_5' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>MENU PRINCIPAL</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_5(598);'>Buscar Siniestro</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(559);'>CAPTURA - PQR</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(358);'>Consulta Comparendos BOGOTA</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(408);'>Consulta Comparendos BUCARAMANGA</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(310);'>CONTROL</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(635);'>[HST] Siniestros 2012</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_5').style.visibility='visible';">MENU PRINCIPAL</a></b></center></td></tr><tr><td width='150' 
								onmouseover="document.getElementById('sp_6').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_6').style.visibility='visible';"
								onmouseout="document.getElementById('sp_6').style.visibility='hidden';" align='left'>
			<span id='sp_6' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>OPERATIVO</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_6(307);'>Alistamiento</td></tr><tr ><td class='menuprincipal' onclick='opciones_6(309);'>Citas del dia</td></tr><tr ><td class='menuprincipal' onclick='opciones_6(308);'>Operarios de Flotas</td></tr><tr ><td class='menuprincipal' onclick='opciones_6(312);'>Pendientes de Alistamiento</td></tr><tr ><td class='menuprincipal' onclick='opciones_6(314);'>UBICACIONES</td></tr><tr ><td class='menuprincipal' onclick='opciones_6(313);'>VEHICULOS</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_6').style.visibility='visible';">OPERATIVO</a></b></center></td></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal2('https://consultas.transitobogota.gov.co:8010/publico/index3.php',0,0,700,800,'win2'); ">
				<img src='imagenes/000/451/comparendo_electronico.png' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendo Electronico<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('reportes2.php',0,0,800,1000,'destino'); ">
				<img src='imagenes/datos/impresora.png' width='48' border='0'>
				<span style='width:300px'>Reportes<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('http://app.aoacolombia.com/Administrativo/administracion_zcalidad.php',0,0,600,900,'win3'); ">
				<img src='imagenes/000/663/dicono_f_calidad.png' width='48' border='0'>
				<span style='width:300px'>SISTEMA DE GESTION DE CALIDAD<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=405',0,0,800,1000,'destino'); ">
				<img src='img/grua.png' width='48' border='0'>
				<span style='width:300px'>SINIESTROS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zsiniestro.php',0,0,0,0,'win5'); ">
				<img src='imagenes/000/580/dicono_f_grua_lupa.png' width='48' border='0'>
				<span style='width:300px'>Buscar Siniestro<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zpqr.php',0,0,800,800,'_blank'); ">
				<img src='imagenes/000/549/dicono_f_pqr_aoa_200.png' width='48' border='0'>
				<span style='width:300px'>CAPTURA - PQR<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('https://consultas.transitobogota.gov.co:8010/publico/index3.php',0,0,700,900,'win3'); ">
				<img src='imagenes/000/349/consulta_comparendos.jpg' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendos BOGOTA<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('http://www.transitobucaramanga.gov.co/pendientes.php',0,0,700,900,'win3'); ">
				<img src='imagenes/000/349/consulta_comparendos.jpg' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendos BUCARAMANGA<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcontrol_operativo3.php',0,0,700,900,'win2'); ">
				<img src='img/control.png' width='48' border='0'>
				<span style='width:300px'>CONTROL<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcitas.php',0,0,0,0,'destino'); ">
				<img src='imagenes/182/anotacion.png' width='48' border='0'>
				<span style='width:300px'>Citas del dia<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=308',0,0,800,1000,'destino'); ">
				<img src='imagenes/308/operarios.png' width='48' border='0'>
				<span style='width:300px'>Operarios de Flotas<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zalistamiento.php?Acc=estado_pendientes',0,0,0,0,'destino'); ">
				<img src='imagenes/312/package_settings.png' width='48' border='0'>
				<span style='width:300px'>Pendientes de Alistamiento<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=313',0,0,800,1000,'destino'); ">
				<img src='img/vehiculo.png' width='48' border='0'>
				<span style='width:300px'>VEHICULOS<br></span></a>
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