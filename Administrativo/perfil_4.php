<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==326) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=326',0,0,800,1000,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					if(Dato==335) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=335',0,0,800,1000,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==419) { modal2('administracion_zcalidad.php',0,0,600,900,'win2'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
				}
				function opciones_3(Dato)
			{
			if(Dato==373) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=373',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					
				}
				function opciones_4(Dato)
			{
			if(Dato==204) { modal2('reportes2.php',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					
				}
				function opciones_5(Dato)
			{
			if(Dato==447) { modal2('zproveedor.php?Acc=adicion_de_proveedor',0,0,0,0,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==446) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=446',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==248) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=248',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==227) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=227',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==443) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=443',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					if(Dato==226) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=226',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					
				}
				function opciones_6(Dato)
			{
			if(Dato==374) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=374',0,0,800,1000,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					if(Dato==246) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=246',0,0,800,1000,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					if(Dato==316) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=316',0,0,800,1000,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					if(Dato==245) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=245',0,0,800,1000,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					
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
			<tr><td align='center' style='font-size:14px'><b>CAJA MENOR</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(326);'>Reembolsos</td></tr><tr ><td class='menuprincipal' onclick='opciones_1(335);'>[CFG] Tipo de Gasto</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">CAJA MENOR</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CALIDAD</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(419);'>SISTEMA DE GESTION DE CALIDAD</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">CALIDAD</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_3').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_3').style.visibility='visible';"
								onmouseout="document.getElementById('sp_3').style.visibility='hidden';" align='left'>
			<span id='sp_3' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CUENTAS POR PAGAR</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_3(373);'>5. APROBACIONES DE PAGOS</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_3').style.visibility='visible';">CUENTAS POR PAGAR</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_4').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_4').style.visibility='visible';"
								onmouseout="document.getElementById('sp_4').style.visibility='hidden';" align='left'>
			<span id='sp_4' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>MENU PRINCIPAL</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_4(204);'>Reportes</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_4').style.visibility='visible';">MENU PRINCIPAL</a></b></center></td></tr><tr><td width='150' 
								onmouseover="document.getElementById('sp_5').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_5').style.visibility='visible';"
								onmouseout="document.getElementById('sp_5').style.visibility='hidden';" align='left'>
			<span id='sp_5' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>PROVEEDORES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_5(447);'>Adicion de Proveedor</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(446);'>CALIDAD - Bienes y Servicios</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(248);'>Comprobante Contable</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(227);'>CUENTAS POR PAGAR - FACTURAS</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(443);'>PAGOS</td></tr><tr ><td class='menuprincipal' onclick='opciones_5(226);'>PROVEEDORES</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_5').style.visibility='visible';">PROVEEDORES</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_6').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_6').style.visibility='visible';"
								onmouseout="document.getElementById('sp_6').style.visibility='hidden';" align='left'>
			<span id='sp_6' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>TABLAS</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_6(374);'>Detalle de Pago</td></tr><tr ><td class='menuprincipal' onclick='opciones_6(246);'>Puc</td></tr><tr ><td class='menuprincipal' onclick='opciones_6(316);'>Sub Conceptos de factura</td></tr><tr ><td class='menuprincipal' onclick='opciones_6(245);'>Tipos de Documentos</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_6').style.visibility='visible';">TABLAS</a></b></center></td></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=326',0,0,800,1000,'destino'); ">
				<img src='imagenes/000/321/dinero4.png' width='48' border='0'>
				<span style='width:300px'>Reembolsos<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('administracion_zcalidad.php',0,0,600,900,'win2'); ">
				<img src='imagenes/000/416/dicono_f_calidad.png' width='48' border='0'>
				<span style='width:300px'>SISTEMA DE GESTION DE CALIDAD<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=373',0,0,800,1000,'destino'); ">
				<img src='imagenes/181/carpeta.png' width='48' border='0'>
				<span style='width:300px'>5. APROBACIONES DE PAGOS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('reportes2.php',0,0,800,1000,'destino'); ">
				<img src='imagenes/datos/impresora.png' width='48' border='0'>
				<span style='width:300px'>Reportes<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zproveedor.php?Acc=adicion_de_proveedor',0,0,0,0,'destino'); ">
				<img src='img/adicionar_proveedor.png' width='48' border='0'>
				<span style='width:300px'>Adicion de Proveedor<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=227',0,0,800,1000,'destino'); ">
				<img src='imagenes/152/folder.png' width='48' border='0'>
				<span style='width:300px'>CUENTAS POR PAGAR - FACTURAS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=443',0,0,800,1000,'destino'); ">
				<img src='imagenes/170/dinero2_1.png' width='48' border='0'>
				<span style='width:300px'>PAGOS<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=226',0,0,800,1000,'destino'); ">
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