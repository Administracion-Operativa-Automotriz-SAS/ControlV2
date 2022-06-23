<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==208) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=208',0,0,800,1000,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==220) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=220',0,0,800,1000,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==333) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=333',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
				}
				function opciones_3(Dato)
			{
			if(Dato==417) { modal2('zcalidad.php',0,0,600,900,'win2'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					
				}
				function opciones_4(Dato)
			{
			if(Dato==439) { modal2('zgendoc.php',0,0,600,800,'win'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					
				}
				function opciones_5(Dato)
			{
			if(Dato==378) { modal2('zcontrol_balance_estado.php',0,0,0,0,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==172) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=172',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==241) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=241',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					
				}
				function opciones_6(Dato)
			{
			if(Dato==209) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=209',0,0,800,1000,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					if(Dato==211) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=211',0,0,800,1000,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					
				}
				function opciones_7(Dato)
			{
			if(Dato==212) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=212',0,0,800,1000,'destino'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==213) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=213',0,0,800,1000,'destino'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==214) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=214',0,0,800,1000,'destino'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==210) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=210',0,0,800,1000,'destino'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==215) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=215',0,0,800,1000,'destino'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==216) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=216',0,0,800,1000,'destino'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					if(Dato==217) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=217',0,0,800,1000,'destino'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					
				}
				function opciones_8(Dato)
			{
			if(Dato==205) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=205',0,0,600,600,'destino'); document.getElementById('sp_8').style.visibility='hidden'; return true;}
					if(Dato==203) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=203',0,0,800,1000,'destino'); document.getElementById('sp_8').style.visibility='hidden'; return true;}
					if(Dato==224) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=224',0,0,800,1000,'destino'); document.getElementById('sp_8').style.visibility='hidden'; return true;}
					
				}
				var Movil=<?=(browser_movil()?1:0)?>;
	</script><body topmargin=1 leftmargin=1 rightmargin=1 bottommargin=1
				onload='fija_destino();' onresize='fija_destino();' bgcolor='#ddddff'>
				<table id='Menu_Principal' name='Menu_Principal' border=0 cellspacing=1 width='' bgcolor='ddddff'><tr><td width='300' 
								onmouseover="document.getElementById('sp_1').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_1').style.visibility='visible';"
								onmouseout="document.getElementById('sp_1').style.visibility='hidden';" align='left'>
			<span id='sp_1' style='position:absolute;visibility:hidden;width:300;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='300' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>ACH</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(208);'>Codigos ACH</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(220);'>Sucursales Bancos</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">ACH</a></b></center></td><td width='300' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:300;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='300' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CAJA MENOR</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(333);'>Reembolsos</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">CAJA MENOR</a></b></center></td><td width='300' 
								onmouseover="document.getElementById('sp_3').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_3').style.visibility='visible';"
								onmouseout="document.getElementById('sp_3').style.visibility='hidden';" align='left'>
			<span id='sp_3' style='position:absolute;visibility:hidden;width:300;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='300' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CALIDAD</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_3(417);'>SISTEMA DE GESTION DE CALIDAD</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_3').style.visibility='visible';">CALIDAD</a></b></center></td><td width='300' 
								onmouseover="document.getElementById('sp_4').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_4').style.visibility='visible';"
								onmouseout="document.getElementById('sp_4').style.visibility='hidden';" align='left'>
			<span id='sp_4' style='position:absolute;visibility:hidden;width:300;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='300' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>GESTION DOCUMENTAL</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_4(439);'>Generador de Documentos</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_4').style.visibility='visible';">GESTION DOCUMENTAL</a></b></center></td><td width='300' 
								onmouseover="document.getElementById('sp_5').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_5').style.visibility='visible';"
								onmouseout="document.getElementById('sp_5').style.visibility='hidden';" align='left'>
			<span id='sp_5' style='position:absolute;visibility:hidden;width:300;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='300' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>MENU PRINCIPAL</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_5(378);'>Control Balance de Estados</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(172);'>HISTORICO DE FACTURAS</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(241);'>Ingreso a Recepcion</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_5').style.visibility='visible';">MENU PRINCIPAL</a></b></center></td><td width='300' 
								onmouseover="document.getElementById('sp_6').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_6').style.visibility='visible';"
								onmouseout="document.getElementById('sp_6').style.visibility='hidden';" align='left'>
			<span id='sp_6' style='position:absolute;visibility:hidden;width:300;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='300' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>PROVEEDORES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_6(209);'>PAGOS</td></tr><tr ><td class='menuprincipal' onclick='opciones_6(211);'>PROVEEDORES</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_6').style.visibility='visible';">PROVEEDORES</a></b></center></td></tr><tr><td width='300' 
								onmouseover="document.getElementById('sp_7').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_7').style.visibility='visible';"
								onmouseout="document.getElementById('sp_7').style.visibility='hidden';" align='left'>
			<span id='sp_7' style='position:absolute;visibility:hidden;width:300;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='300' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>TABLAS</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_7(212);'>Bancos</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(213);'>Conceptos de factura</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(214);'>Contador</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(210);'>Detalle de Pago</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(215);'>Regimen Tributario</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(216);'>RteFte:Conceptos Tributarios</td></tr><tr ><td class='menuprincipal' onclick='opciones_7(217);'>RteIca:Actividades Tributarias</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_7').style.visibility='visible';">TABLAS</a></b></center></td><td width='300' 
								onmouseover="document.getElementById('sp_8').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_8').style.visibility='visible';"
								onmouseout="document.getElementById('sp_8').style.visibility='hidden';" align='left'>
			<span id='sp_8' style='position:absolute;visibility:hidden;width:300;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='300' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>USUARIOS</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_8(205);'>U. Administrativos</td></tr><tr ><td class='menuprincipal' onclick='opciones_8(203);'>U. Contadores</td></tr><tr ><td class='menuprincipal' onclick='opciones_8(224);'>U. Gerencia</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_8').style.visibility='visible';">USUARIOS</a></b></center></td></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal2('reportes2.php',0,0,800,1000,'destino'); ">
				<img src='imagenes/datos/impresora.png' width='48' border='0'>
				<span style='width:300px'>Reportes<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=333',0,0,800,1000,'destino'); ">
				<img src='imagenes/000/321/dinero4.png' width='48' border='0'>
				<span style='width:300px'>Reembolsos<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zcalidad.php',0,0,600,900,'win2'); ">
				<img src='imagenes/000/416/dicono_f_calidad.png' width='48' border='0'>
				<span style='width:300px'>SISTEMA DE GESTION DE CALIDAD<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zgendoc.php',0,0,600,800,'win'); ">
				<img src='imagenes/000/375/dicono_f_folder_documentos.png' width='48' border='0'>
				<span style='width:300px'>Generador de Documentos<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=172',0,0,800,1000,'destino'); ">
				<img src='imagenes/152/folder.png' width='48' border='0'>
				<span style='width:300px'>HISTORICO DE FACTURAS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=209',0,0,800,1000,'destino'); ">
				<img src='imagenes/170/dinero2_1.png' width='48' border='0'>
				<span style='width:300px'>PAGOS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=211',0,0,800,1000,'destino'); ">
				<img src='imagenes/150/yast_group_add.png' width='48' border='0'>
				<span style='width:300px'>PROVEEDORES<br></span></a>
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