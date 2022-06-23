<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==324) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=324',0,0,800,1000,'destino'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==422) { modal2('administracion_zcalidad.php',0,0,600,900,'win2'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
				}
				function opciones_3(Dato)
			{
			if(Dato==432) { modal2('zrequisicion.php?Acc=ver_balance',0,0,0,0,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					
				}
				function opciones_4(Dato)
			{
			if(Dato==464) { modal2('zproveedor.php?Acc=adicion_de_proveedor',0,0,0,0,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==328) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=328',0,0,600,1000,'win2'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					
				}
				function opciones_5(Dato)
			{
			if(Dato==78) { modal2('reportes2.php',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					
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
			<tr><td align='center' style='font-size:14px'><b>CAJA MENOR</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(324);'>Reembolsos</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">CAJA MENOR</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CALIDAD</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(422);'>SISTEMA DE GESTION DE CALIDAD</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">CALIDAD</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_3').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_3').style.visibility='visible';"
								onmouseout="document.getElementById('sp_3').style.visibility='hidden';" align='left'>
			<span id='sp_3' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>OPERATIVO</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_3(432);'>Solicitud de Requisición</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_3').style.visibility='visible';">OPERATIVO</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_4').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_4').style.visibility='visible';"
								onmouseout="document.getElementById('sp_4').style.visibility='hidden';" align='left'>
			<span id='sp_4' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>PROVEEDORES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_4(464);'>Adicion de Proveedor</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(328);'>PROVEEDORES</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_4').style.visibility='visible';">PROVEEDORES</a></b></center></td><td width='150' 
								onmouseover="document.getElementById('sp_5').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_5').style.visibility='visible';"
								onmouseout="document.getElementById('sp_5').style.visibility='hidden';" align='left'>
			<span id='sp_5' style='position:absolute;visibility:hidden;width:150;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='150' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>REPORTES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_5(78);'>Reportes</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_5').style.visibility='visible';">REPORTES</a></b></center></td></tr><tr></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=324',0,0,800,1000,'destino'); ">
				<img src='imagenes/000/324/dinero4.png' width='48' border='0'>
				<span style='width:300px'>Reembolsos<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('administracion_zcalidad.php',0,0,600,900,'win2'); ">
				<img src='imagenes/000/416/dicono_f_calidad.png' width='48' border='0'>
				<span style='width:300px'>SISTEMA DE GESTION DE CALIDAD<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zrequisicion.php?Acc=ver_balance',0,0,0,0,'destino'); ">
				<img src='imagenes/000/347/dicono_f_paquetes.png' width='48' border='0'>
				<span style='width:300px'>Solicitud de Requisición<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('zproveedor.php?Acc=adicion_de_proveedor',0,0,0,0,'destino'); ">
				<img src='img/adicionar_proveedor.png' width='48' border='0'>
				<span style='width:300px'>Adicion de Proveedor<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=328',0,0,600,1000,'win2'); ">
				<img src='imagenes/150/yast_group_add.png' width='48' border='0'>
				<span style='width:300px'>PROVEEDORES<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('reportes2.php',0,0,800,1000,'destino'); ">
				<img src='imagenes/datos/impresora.png' width='48' border='0'>
				<span style='width:300px'>Reportes<br></span></a>
				</td><td>
		<iframe name='destino' id='destino' src='bienvenida_aguila7.php' frameborder='no' height='600' width='99%' scrolling='auto'></iframe>
		</td></tr></table>
		<table width='100%' cellspacing='0' cellpadding='0'><tr><td>
			<a class='sinfo' style='cursor:pointer' onclick="mata_perfil();"><img src='gifs/standar/stop_16.png' border=0><span>Cerrar Sesi&oacute;n</span></a></td>
		<td><a class='sinfo' style='cursor:pointer' onclick="mata_v_sesion();"><img src='gifs/standar/home_16.png' border=0><span style='width:100px'>Ir al inicio de la aplicacion</span></a></td>
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
		<i>	Desarrollado por: Tecnologia AOA Diciembre de 2010 &reg </i></td>