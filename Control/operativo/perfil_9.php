<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==440) { modal2('zcontrol_custodia_garantia.php',0,0,0,0,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==252) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=252',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==423) { modal2('zcartera.php',0,0,750,1000,'win4'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==251) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=251',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==250) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=250',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==463) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=463',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==692) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=692',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
				}
				function opciones_3(Dato)
			{
			if(Dato==628) { modal2('zgenerador_contable.php?Acc=exportar_factura_helisa',0,0,0,0,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==629) { modal2('zgenerador_contable.php?Acc=exportacion_garantias',0,0,0,0,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==630) { modal2('zgenerador_contable.php?Acc=exportacion_notas_contables',0,0,0,0,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==631) { modal2('zgenerador_contable.php?Acc=exportacion_transferencias_garantia',0,0,0,0,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==627) { modal2('zgenerador_contable.php?Acc=exportacion_transferencias_garantia',0,0,0,0,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					
				}
				function opciones_4(Dato)
			{
			if(Dato==701) { modal2('zgenerador_contable.suno.php?Acc=exportar_factura_uno',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==706) { modal2('zgenerador_contable.suno.php?Acc=exportacion_garantias_uno',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==707) { modal2('zgenerador_contable.suno.php?Acc=exportacion_notas_contables_uno',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==708) { modal2('zgenerador_contable.suno.php?Acc=exportacion_transferencias_garantia_uno',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					
				}
				function opciones_5(Dato)
			{
			if(Dato==655) { modal2('zsiniestro.php',0,0,0,0,'win5'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==755) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=755',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					
				}
				function opciones_6(Dato)
			{
			if(Dato==767) { modal2('zcontrol_operativo3.php',0,0,700,900,'win'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					
				}
				function opciones_7(Dato)
			{
			if(Dato==334) { modal2('reportes2.php',0,0,0,0,'destino'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					
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
			<tr><td align='center' style='font-size:14px'><b>AUTORIZACIONES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(440);'>Control Garantias</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">AUTORIZACIONES</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CARTERA</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(252);'>Clientes</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(423);'>Control de Cartera</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(251);'>Detalle de Factura</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(250);'>Facturas</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(463);'>Notas Contables</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(692);'>Notas Credito</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">CARTERA</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_3').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_3').style.visibility='visible';"
								onmouseout="document.getElementById('sp_3').style.visibility='hidden';" align='left'>
			<span id='sp_3' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>INTERFASES HELISA</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_3(628);'>Exportación de Facturas (cuentas por cobrar)</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(629);'>Exportacion de Garantias de Servicio</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(630);'>Exportacion de Notas Contables</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(631);'>Exportacion de Transferencias Devoluciones de Gara</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(627);'>Exportar Transferencias de Garantías</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_3').style.visibility='visible';">INTERFASES HELISA</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_4').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_4').style.visibility='visible';"
								onmouseout="document.getElementById('sp_4').style.visibility='hidden';" align='left'>
			<span id='sp_4' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>INTERFASES S.UNO</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_4(701);'>Exportación de Facturas (Cuentas x Cobrar)</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(706);'>Exportacion de Garantias de Servicio</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(707);'>Exportacion de Notas Contables</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(708);'>Exportacion de Transferencias Devol. Garantias</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_4').style.visibility='visible';">INTERFASES S.UNO</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_5').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_5').style.visibility='visible';"
								onmouseout="document.getElementById('sp_5').style.visibility='hidden';" align='left'>
			<span id='sp_5' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>MENU PRINCIPAL</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_5(655);'>Buscar Siniestro</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(755);'>SINIESTROS</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_5').style.visibility='visible';">MENU PRINCIPAL</a></b></center></td></tr><tr><td width='200' 
								onmouseover="document.getElementById('sp_6').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_6').style.visibility='visible';"
								onmouseout="document.getElementById('sp_6').style.visibility='hidden';" align='left'>
			<span id='sp_6' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>OPERATIVO</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_6(767);'>TABLA DE CONTROL</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_6').style.visibility='visible';">OPERATIVO</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_7').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_7').style.visibility='visible';"
								onmouseout="document.getElementById('sp_7').style.visibility='hidden';" align='left'>
			<span id='sp_7' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>REPORTES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_7(334);'>Reportes</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_7').style.visibility='visible';">REPORTES</a></b></center></td></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal2('zcontrol_custodia_garantia.php',0,0,0,0,'destino'); ">
				<img src='imagenes/000/384/seguimiento.png' width='48' border='0'>
				<span style='width:300px'>Control Garantias<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcartera.php',0,0,750,1000,'win4'); ">
				<img src='imagenes/000/423/folder_empty.png' width='48' border='0'>
				<span style='width:300px'>Control de Cartera<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=463',0,0,800,1000,'destino'); ">
				<img src='imagenes/000/463/actualizacion.png' width='48' border='0'>
				<span style='width:300px'>Notas Contables<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zsiniestro.php',0,0,0,0,'win5'); ">
				<img src='imagenes/000/580/dicono_f_grua_lupa.png' width='48' border='0'>
				<span style='width:300px'>Buscar Siniestro<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=755',0,0,800,1000,'destino'); ">
				<img src='img/grua.png' width='48' border='0'>
				<span style='width:300px'>SINIESTROS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcontrol_operativo3.php',0,0,700,900,'win'); ">
				<img src='imagenes/227/control.png' width='48' border='0'>
				<span style='width:300px'>TABLA DE CONTROL<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('reportes2.php',0,0,0,0,'destino'); ">
				<img src='imagenes/334/impresora.png' width='48' border='0'>
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