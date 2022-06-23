<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==747) { modal2('zcontrol_custodia_garantia.php',0,0,700,1000,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==748) { modal2('zautorizaciones.php?Acc=estado',0,0,500,500,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==749) { modal2('zautorizaciones.php',0,0,500,500,'win3'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==750) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=750',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==645) { modal2('zcartera.php',0,0,750,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==752) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=752',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==740) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=740',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==443) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=443',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==744) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=744',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==646) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=646',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==745) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=745',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==751) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=751',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==737) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=737',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==754) { modal2('http://app.aoacolombia.com/Control/operativo/zfacturacion.php?Acc=facturacion_electronica_reporte',0,0,800,1000,'win2'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
				}
				function opciones_3(Dato)
			{
			if(Dato==773) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=773',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==738) { modal2('zsiniestro.php',0,0,800,1000,'win'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==446) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=446',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==448) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=448',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==447) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=447',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					
				}
				function opciones_4(Dato)
			{
			if(Dato==445) { modal2('zcontrol_operativo3.php',0,0,800,800,'win'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					
				}
				function opciones_5(Dato)
			{
			if(Dato==742) { modal2('reportes2.php',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					
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
			<tr><td align='center' style='font-size:14px'><b>AUTORIZACIONES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(747);'>Control Garantias</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(748);'>Estado de Solicitudes</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(749);'>Solicitud de Autorizacion</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">AUTORIZACIONES</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CARTERA</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(750);'>Clientes</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(645);'>Control de Cartera</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(752);'>Detalle de Factura</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(740);'>Facturas</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(443);'>Notas Contables</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(744);'>Notas Credito</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(646);'>Recibo de Caja</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(745);'>Recibo de Caja</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(751);'>Solicitud de Facturaci?n Total</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(737);'>Solicitud de Facturacion</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(754);'>Verificar Factura Electronica</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">CARTERA</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_3').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_3').style.visibility='visible';"
								onmouseout="document.getElementById('sp_3').style.visibility='hidden';" align='left'>
			<span id='sp_3' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>MENU PRINCIPAL</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_3(773);'>ASEGURADORAS</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(738);'>Buscar Siniestro</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(446);'>HISTORICO DE ESTADOS</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(448);'>SINIESTROS</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(447);'>VEHICULOS</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_3').style.visibility='visible';">MENU PRINCIPAL</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_4').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_4').style.visibility='visible';"
								onmouseout="document.getElementById('sp_4').style.visibility='hidden';" align='left'>
			<span id='sp_4' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>OPERATIVO</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_4(445);'>Control Operativo</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_4').style.visibility='visible';">OPERATIVO</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_5').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_5').style.visibility='visible';"
								onmouseout="document.getElementById('sp_5').style.visibility='hidden';" align='left'>
			<span id='sp_5' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>REPORTES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_5(742);'>Reportes</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_5').style.visibility='visible';">REPORTES</a></b></center></td></tr><tr></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal2('https://consultas.transitobogota.gov.co:8010/publico/index3.php',0,0,700,800,'win2'); ">
				<img src='imagenes/000/451/comparendo_electronico.png' width='48' border='0'>
				<span style='width:300px'>Consulta Comparendo Electronico<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcontrol_custodia_garantia.php',0,0,700,1000,'destino'); ">
				<img src='imagenes/000/388/seguimiento.png' width='48' border='0'>
				<span style='width:300px'>Control Garantias<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zautorizaciones.php?Acc=estado',0,0,500,500,'destino'); ">
				<img src='imagenes/194/estado_autorizaciones.png' width='48' border='0'>
				<span style='width:300px'>Estado de Solicitudes<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zautorizaciones.php',0,0,500,500,'win3'); ">
				<img src='imagenes/190/solicita_autorizacion_1.png' width='48' border='0'>
				<span style='width:300px'>Solicitud de Autorizacion<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcartera.php',0,0,750,1000,'destino'); ">
				<img src='imagenes/000/254/mochila.png' width='48' border='0'>
				<span style='width:300px'>Control de Cartera<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=751',0,0,800,1000,'destino'); ">
				<img src='imagenes/000/438/1060.jpg' width='48' border='0'>
				<span style='width:300px'>Solicitud de Facturaci?n Total<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=773',0,0,800,1000,'destino'); ">
				<img src='imagenes/130/aseguradora.png' width='48' border='0'>
				<span style='width:300px'>ASEGURADORAS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zsiniestro.php',0,0,800,1000,'win'); ">
				<img src='imagenes/000/580/dicono_f_grua_lupa.png' width='48' border='0'>
				<span style='width:300px'>Buscar Siniestro<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=448',0,0,800,1000,'destino'); ">
				<img src='img/grua.png' width='48' border='0'>
				<span style='width:300px'>SINIESTROS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=447',0,0,800,1000,'destino'); ">
				<img src='img/vehiculo.png' width='48' border='0'>
				<span style='width:300px'>VEHICULOS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcontrol_operativo3.php',0,0,800,800,'win'); ">
				<img src='img/control.png' width='48' border='0'>
				<span style='width:300px'>Control Operativo<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('reportes2.php',0,0,800,1000,'destino'); ">
				<img src='imagenes/datos/impresora.png' width='48' border='0'>
				<span style='width:300px'>Reportes<br></span></a>
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