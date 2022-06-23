<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==777) { modal2('http://app.aoacolombia.com/Administrativo/administracion_zcalidad.php',0,0,600,900,'win3'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==439) { modal2('zcontrol_custodia_garantia.php',0,0,0,0,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
				}
				function opciones_3(Dato)
			{
			if(Dato==670) { modal2('http://app.aoacolombia.com/Administrativo/administracion_zcalidad.php',0,0,600,900,'win3'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					
				}
				function opciones_4(Dato)
			{
			if(Dato==516) { modal2('zcartera.php',0,0,750,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==465) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=465',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==434) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=434',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==466) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=466',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					
				}
				function opciones_5(Dato)
			{
			if(Dato==733) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=733',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					
				}
				function opciones_6(Dato)
			{
			if(Dato==540) { modal2('manual.php',0,0,600,800,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					
				}
				function opciones_7(Dato)
			{
			if(Dato==596) { modal2('zsiniestro.php',0,0,0,0,'win5'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==562) { modal2('zpqr.php',0,0,800,800,'_blank'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==368) { modal2('http://www.barranquilla.gov.co:8080/infracciones_trans/ConsultaDocumento.jsp',0,0,700,900,'win3'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==356) { modal2('https://consultas.transitobogota.gov.co:8010/publico/index3.php',0,0,700,900,'win3'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==369) { modal2('http://www.transitobucaramanga.gov.co/pendientes.php',0,0,700,900,'win3'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==366) { modal2('http://www.serviciosdetransito.com:8080/serviciosExternos/multas/consultarMultasPersona.jsp',0,0,700,900,'win3'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==367) { modal2('https://www.simit.org.co/consulta-de-infracciones',0,0,700,900,'win3'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==370) { modal2('http://201.236.227.175:8080/touch/touch.php',0,0,700,900,'win3'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==206) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=206',0,0,800,1000,'destino'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==255) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=255',0,0,800,1000,'destino'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					
				}
				function opciones_8(Dato)
			{
			if(Dato==205) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=205',0,0,800,1000,'destino'); document.getElementById('sp_8').style.visibility='hidden'; return true;}
					if(Dato==196) { modal2('zcitas.php',0,0,0,0,'destino'); document.getElementById('sp_8').style.visibility='hidden'; return true;}
					if(Dato==574) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=574',0,0,800,1000,'destino'); document.getElementById('sp_8').style.visibility='hidden'; return true;}
					if(Dato==256) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=256',0,0,800,1000,'destino'); document.getElementById('sp_8').style.visibility='hidden'; return true;}
					if(Dato==226) { modal2('zcontrol_operativo3.php',0,0,700,900,'win'); document.getElementById('sp_8').style.visibility='hidden'; return true;}
					if(Dato==774) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=774',0,0,800,1000,'destino'); document.getElementById('sp_8').style.visibility='hidden'; return true;}
					
				}
				function opciones_9(Dato)
			{
			if(Dato==716) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=716',0,0,800,1000,'destino'); document.getElementById('sp_9').style.visibility='hidden'; return true;}
					
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
			<tr><td align='center' style='font-size:14px'><b>ADM FLOTA CARGA</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(777);'>CARGUE DE CONTROLES</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">ADM FLOTA CARGA</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>AUTORIZACIONES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(439);'>Control Garantias</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">AUTORIZACIONES</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_3').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_3').style.visibility='visible';"
								onmouseout="document.getElementById('sp_3').style.visibility='hidden';" align='left'>
			<span id='sp_3' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CALIDAD</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_3(670);'>SISTEMA DE GESTION DE CALIDAD</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_3').style.visibility='visible';">CALIDAD</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_4').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_4').style.visibility='visible';"
								onmouseout="document.getElementById('sp_4').style.visibility='hidden';" align='left'>
			<span id='sp_4' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CARTERA</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_4(516);'>Control de Cartera</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(465);'>Facturas</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(434);'>Recibo Caja Provisional</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(466);'>Recibo de Caja</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_4').style.visibility='visible';">CARTERA</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_5').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_5').style.visibility='visible';"
								onmouseout="document.getElementById('sp_5').style.visibility='hidden';" align='left'>
			<span id='sp_5' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>FLOTAS</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_5(733);'>[CFG] Alerta x Vehiculo</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_5').style.visibility='visible';">FLOTAS</a></b></center></td></tr><tr><td width='150' 
								onmouseover="document.getElementById('sp_6').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_6').style.visibility='visible';"
								onmouseout="document.getElementById('sp_6').style.visibility='hidden';" align='left'>
			<span id='sp_6' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>MANUAL</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_6(540);'>MANUAL DE USUARIO</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_6').style.visibility='visible';">MANUAL</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_7').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_7').style.visibility='visible';"
								onmouseout="document.getElementById('sp_7').style.visibility='hidden';" align='left'>
			<span id='sp_7' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>MENU PRINCIPAL</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_7(596);'>Buscar Siniestro</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(562);'>CAPTURA - PQR</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(368);'>Consulta Comparendos BARRANQUILLA</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(356);'>Consulta Comparendos BOGOTA</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(369);'>Consulta Comparendos BUCARAMANGA</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(366);'>Consulta Comparendos CALI</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(367);'>Consulta Comparendos NACIONAL</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(370);'>Consulta Comparendos PEREIRA</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(206);'>SINIESTROS</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(255);'>UBICACIONES</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_7').style.visibility='visible';">MENU PRINCIPAL</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_8').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_8').style.visibility='visible';"
								onmouseout="document.getElementById('sp_8').style.visibility='hidden';" align='left'>
			<span id='sp_8' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>OPERATIVO</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_8(205);'>Citas de Servicio</td></tr><tr ><td class='menuprincipal' onclick='opciones_8(196);'>Citas del dia</td></tr><tr ><td class='menuprincipal' onclick='opciones_8(574);'>Comparendos</td></tr><tr ><td class='menuprincipal' onclick='opciones_8(256);'>Novedades H.V.Vehiculos</td></tr><tr ><td class='menuprincipal' onclick='opciones_8(226);'>TABLA DE CONTROL</td></tr><tr ><td class='menuprincipal' onclick='opciones_8(774);'>VEHICULOS</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_8').style.visibility='visible';">OPERATIVO</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_9').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_9').style.visibility='visible';"
								onmouseout="document.getElementById('sp_9').style.visibility='hidden';" align='left'>
			<span id='sp_9' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>TABLAS</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_9(716);'>Encuesta Liberty</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_9').style.visibility='visible';">TABLAS</a></b></center></td></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal2('https://consultas.transitobogota.gov.co:8010/publico/index3.php',0,0,700,800,'win2'); ">
				<img src='imagenes/000/451/comparendo_electronico.png' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendo Electronico<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('reportes2.php',0,0,800,1000,'destino'); ">
				<img src='imagenes/datos/impresora.png' width='48' border='0'>
				<span style='width:300px'>Reportes<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('http://app.aoacolombia.com/Administrativo/administracion_zcalidad.php',0,0,600,900,'win3'); ">
				<img src='imagenes/000/776/dicono_f_ci.png' width='48' border='0'>
				<span style='width:300px'>CARGUE DE CONTROLES<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcontrol_custodia_garantia.php',0,0,0,0,'destino'); ">
				<img src='imagenes/000/439/seguimiento.png' width='48' border='0'>
				<span style='width:300px'>Control Garantias<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('http://app.aoacolombia.com/Administrativo/administracion_zcalidad.php',0,0,600,900,'win3'); ">
				<img src='imagenes/000/663/dicono_f_calidad.png' width='48' border='0'>
				<span style='width:300px'>SISTEMA DE GESTION DE CALIDAD<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcartera.php',0,0,750,1000,'destino'); ">
				<img src='imagenes/000/254/mochila.png' width='48' border='0'>
				<span style='width:300px'>Control de Cartera<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=465',0,0,800,1000,'destino'); ">
				<img src='imagenes/000/465/1060.jpg' width='48' border='0'>
				<span style='width:300px'>Facturas<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zsiniestro.php',0,0,0,0,'win5'); ">
				<img src='imagenes/000/580/dicono_f_grua_lupa.png' width='48' border='0'>
				<span style='width:300px'>Buscar Siniestro<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zpqr.php',0,0,800,800,'_blank'); ">
				<img src='imagenes/000/549/dicono_f_pqr_aoa_200.png' width='48' border='0'>
				<span style='width:300px'>CAPTURA - PQR<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('http://www.barranquilla.gov.co:8080/infracciones_trans/ConsultaDocumento.jsp',0,0,700,900,'win3'); ">
				<img src='imagenes/000/349/consulta_comparendos.jpg' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendos BARRANQUILLA<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('https://consultas.transitobogota.gov.co:8010/publico/index3.php',0,0,700,900,'win3'); ">
				<img src='imagenes/000/349/consulta_comparendos.jpg' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendos BOGOTA<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('http://www.transitobucaramanga.gov.co/pendientes.php',0,0,700,900,'win3'); ">
				<img src='imagenes/000/349/consulta_comparendos.jpg' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendos BUCARAMANGA<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('http://www.serviciosdetransito.com:8080/serviciosExternos/multas/consultarMultasPersona.jsp',0,0,700,900,'win3'); ">
				<img src='imagenes/000/349/consulta_comparendos.jpg' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendos CALI<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('https://www.simit.org.co/consulta-de-infracciones',0,0,700,900,'win3'); ">
				<img src='imagenes/000/349/consulta_comparendos.jpg' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendos NACIONAL<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('http://201.236.227.175:8080/touch/touch.php',0,0,700,900,'win3'); ">
				<img src='imagenes/000/349/consulta_comparendos.jpg' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendos PEREIRA<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=206',0,0,800,1000,'destino'); ">
				<img src='imagenes/206/towtruckyellow.png' width='48' border='0'>
				<span style='width:300px'>SINIESTROS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcitas.php',0,0,0,0,'destino'); ">
				<img src='imagenes/182/anotacion.png' width='48' border='0'>
				<span style='width:300px'>Citas del dia<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcontrol_operativo3.php',0,0,700,900,'win'); ">
				<img src='imagenes/226/control.png' width='48' border='0'>
				<span style='width:300px'>TABLA DE CONTROL<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=774',0,0,800,1000,'destino'); ">
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