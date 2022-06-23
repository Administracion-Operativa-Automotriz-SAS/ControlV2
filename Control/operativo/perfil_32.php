<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==387) { modal2('zcontrol_custodia_garantia.php',0,0,700,900,'win4'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==676) { modal2('http://app.aoacolombia.com/Administrativo/administracion_zcalidad.php',0,0,600,900,'win3'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
				}
				function opciones_3(Dato)
			{
			if(Dato==486) { modal2('zanalisis_comparendos.php',0,0,0,0,'win4'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==600) { modal2('zsiniestro.php',0,0,0,0,'win5'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==563) { modal2('zpqr.php',0,0,800,800,'_blank'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==377) { modal2('http://consultas.transitobogota.gov.co:8083/publico/index.php',0,0,700,900,'win3'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==378) { modal2('http://www2.simit.org.co/Simit/menu/menu_inicial.jsp',0,0,700,900,'win5'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==460) { modal2('zcontrol_calidad_servicio.php',0,0,600,600,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==375) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=375',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==695) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=695',0,0,0,0,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==374) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=374',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==376) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=376',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					
				}
				function opciones_4(Dato)
			{
			if(Dato==373) { modal2('zcitas.php',0,0,0,0,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==372) { modal2('zcontrol_operativo3.php',0,0,800,800,'win'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==394) { modal2('zingreso_recepcion.php',0,0,800,900,'win'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					
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
			<tr><td align='center' style='font-size:14px'><b>AUTORIZACIONES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(387);'>Control Garantias</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">AUTORIZACIONES</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CALIDAD</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(676);'>SISTEMA DE GESTION DE CALIDAD</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">CALIDAD</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_3').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_3').style.visibility='visible';"
								onmouseout="document.getElementById('sp_3').style.visibility='hidden';" align='left'>
			<span id='sp_3' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>MENU PRINCIPAL</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_3(486);'>An?lisis Comparendos</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(600);'>Buscar Siniestro</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(563);'>CAPTURA - PQR</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(377);'>Consulta Comparendos BOGOTA</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(378);'>Consulta Comparendos NACIONAL</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(460);'>Control de Calidad al Servicio</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(375);'>HISTORICO DE ESTADOS</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(695);'>OFICINAS</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(374);'>SINIESTROS</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(376);'>VEHICULOS</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_3').style.visibility='visible';">MENU PRINCIPAL</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_4').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_4').style.visibility='visible';"
								onmouseout="document.getElementById('sp_4').style.visibility='hidden';" align='left'>
			<span id='sp_4' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>OPERATIVO</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_4(373);'>Citas del dia</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(372);'>Control Operativo</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(394);'>Ingreso a Recepcion</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_4').style.visibility='visible';">OPERATIVO</a></b></center></td></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal2('http://consultas.transitobogota.gov.co:8083/publico/index.php',0,0,700,800,'win2'); ">
				<img src='imagenes/000/451/comparendo_electronico.png' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendo Electronico<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('http://app.aoacolombia.com/Administrativo/administracion_zcalidad.php',0,0,600,900,'win3'); ">
				<img src='imagenes/000/663/dicono_f_calidad.png' width='48' border='0'>
				<span style='width:300px'>SISTEMA DE GESTION DE CALIDAD<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zsiniestro.php',0,0,0,0,'win5'); ">
				<img src='imagenes/000/580/dicono_f_grua_lupa.png' width='48' border='0'>
				<span style='width:300px'>Buscar Siniestro<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zpqr.php',0,0,800,800,'_blank'); ">
				<img src='imagenes/000/549/dicono_f_pqr_aoa_200.png' width='48' border='0'>
				<span style='width:300px'>CAPTURA - PQR<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('http://consultas.transitobogota.gov.co:8083/publico/index.php',0,0,700,900,'win3'); ">
				<img src='imagenes/000/349/consulta_comparendos.jpg' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendos BOGOTA<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('http://www2.simit.org.co/Simit/menu/menu_inicial.jsp',0,0,700,900,'win5'); ">
				<img src='imagenes/000/349/consulta_comparendos.jpg' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendos NACIONAL<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcontrol_calidad_servicio.php',0,0,600,600,'destino'); ">
				<img src='imagenes/137/my_documents2.png' width='48' border='0'>
				<span style='width:300px'>Control de Calidad al Servicio<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=695',0,0,0,0,'destino'); ">
				<img src='imagenes/000/10/dicono_f_package_network.png' width='48' border='0'>
				<span style='width:300px'>OFICINAS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=374',0,0,800,1000,'destino'); ">
				<img src='img/grua.png' width='48' border='0'>
				<span style='width:300px'>SINIESTROS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=376',0,0,800,1000,'destino'); ">
				<img src='img/vehiculo.png' width='48' border='0'>
				<span style='width:300px'>VEHICULOS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcitas.php',0,0,0,0,'destino'); ">
				<img src='imagenes/182/anotacion.png' width='48' border='0'>
				<span style='width:300px'>Citas del dia<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcontrol_operativo3.php',0,0,800,800,'win'); ">
				<img src='img/control.png' width='48' border='0'>
				<span style='width:300px'>Control Operativo<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zingreso_recepcion.php',0,0,800,900,'win'); ">
				<img src='imagenes/000/394/webcam.png' width='48' border='0'>
				<span style='width:300px'>Ingreso a Recepcion<br></span></a>
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