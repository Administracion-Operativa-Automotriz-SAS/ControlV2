<?php
	/*Archvio inicial */
	?>
	function opciones_1(Dato)
			{
			if(Dato==420) { modal2('administracion_zcalidad.php',0,0,600,900,'win2'); document.getElementById('sp_1').style.visibility='hidden'; return true;}
					
				}
				function opciones_2(Dato)
			{
			if(Dato==279) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=279',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==283) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=283',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==284) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=284',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					if(Dato==288) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=288',0,0,800,1000,'destino'); document.getElementById('sp_2').style.visibility='hidden'; return true;}
					
				}
				function opciones_3(Dato)
			{
			if(Dato==280) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=280',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==281) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=281',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					if(Dato==289) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=289',0,0,800,1000,'destino'); document.getElementById('sp_3').style.visibility='hidden'; return true;}
					
				}
				function opciones_4(Dato)
			{
			if(Dato==290) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=290',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==252) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=252',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==291) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=291',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==292) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=292',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==293) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=293',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==294) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=294',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==295) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=295',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					if(Dato==296) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=296',0,0,800,1000,'destino'); document.getElementById('sp_4').style.visibility='hidden'; return true;}
					
				}
				function opciones_5(Dato)
			{
			if(Dato==361) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=361',0,0,800,1000,'destino'); document.getElementById('sp_5').style.visibility='hidden'; return true;}
					
				}
				function opciones_6(Dato)
			{
			if(Dato==282) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=282',0,0,800,1000,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					if(Dato==251) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=251',0,0,800,1000,'destino'); document.getElementById('sp_6').style.visibility='hidden'; return true;}
					
				}
				function opciones_7(Dato)
			{
			if(Dato==278) { modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=278',0,0,800,1000,'destino'); document.getElementById('sp_7').style.visibility='hidden'; return true;}
					
				}
				function opciones_8(Dato)
			{
			if(Dato==285) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=285',0,0,800,1000,'destino'); document.getElementById('sp_8').style.visibility='hidden'; return true;}
					if(Dato==286) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=286',0,0,800,1000,'destino'); document.getElementById('sp_8').style.visibility='hidden'; return true;}
					if(Dato==287) { modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=287',0,0,800,1000,'destino'); document.getElementById('sp_8').style.visibility='hidden'; return true;}
					
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
			<tr><td align='center' style='font-size:14px'><b>CALIDAD</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_1(420);'>SISTEMA DE GESTION DE CALIDAD</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_1').style.visibility='visible';">CALIDAD</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_2').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_2').style.visibility='visible';"
								onmouseout="document.getElementById('sp_2').style.visibility='hidden';" align='left'>
			<span id='sp_2' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CARGOS</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_2(279);'>CARGOS</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(283);'>FUNCIONES DEL CARGO</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(284);'>FUNCIONES ESPECIF.x CARGO</td></tr><tr ><td class='menuprincipal' onclick='opciones_2(288);'>Historico de Cargos</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_2').style.visibility='visible';">CARGOS</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_3').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_3').style.visibility='visible';"
								onmouseout="document.getElementById('sp_3').style.visibility='hidden';" align='left'>
			<span id='sp_3' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CONCEPTOS</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_3(280);'>CONCEPTOS FIJOS</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(281);'>CONCEPTOS NOMINA</td></tr><tr ><td class='menuprincipal' onclick='opciones_3(289);'>NOVEDADES</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_3').style.visibility='visible';">CONCEPTOS</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_4').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_4').style.visibility='visible';"
								onmouseout="document.getElementById('sp_4').style.visibility='hidden';" align='left'>
			<span id='sp_4' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CONFIGURACION</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_4(290);'>PLANILLAS</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(252);'>Sedes</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(291);'>[CFG] C.Costo x H.Cargo</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(292);'>[CFG] Conceptos x Grupo Contable</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(293);'>[CFG] Conf. Bases</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(294);'>[CFG] T.Concepto Nómina</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(295);'>[CFG] Tipo Entidad</td></tr><tr ><td class='menuprincipal' onclick='opciones_4(296);'>[CFG] Tipo Planilla</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_4').style.visibility='visible';">CONFIGURACION</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_5').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_5').style.visibility='visible';"
								onmouseout="document.getElementById('sp_5').style.visibility='hidden';" align='left'>
			<span id='sp_5' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>CONTABILIDAD</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_5(361);'>GRUPOS CONTABLES NOMINA</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_5').style.visibility='visible';">CONTABILIDAD</a></b></center></td></tr><tr><td width='200' 
								onmouseover="document.getElementById('sp_6').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_6').style.visibility='visible';"
								onmouseout="document.getElementById('sp_6').style.visibility='hidden';" align='left'>
			<span id='sp_6' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>EMPLEADOS</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_6(282);'>CONTRATOS</td></tr><tr ><td class='menuprincipal' onclick='opciones_6(251);'>EMPLEADOS</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_6').style.visibility='visible';">EMPLEADOS</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_7').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_7').style.visibility='visible';"
								onmouseout="document.getElementById('sp_7').style.visibility='hidden';" align='left'>
			<span id='sp_7' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>ENTIDADES</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_7(278);'>ENTIDADES</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_7').style.visibility='visible';">ENTIDADES</a></b></center></td><td width='200' 
								onmouseover="document.getElementById('sp_8').style.visibility='visible';"
								onclick="if(browser_movil()) document.getElementById('sp_8').style.visibility='visible';"
								onmouseout="document.getElementById('sp_8').style.visibility='hidden';" align='left'>
			<span id='sp_8' style='position:absolute;visibility:hidden;width:200;z-index:255'>
			<table bgcolor='ddeeee' border=0 cellspacing=1 width='200' cellpadding=0>
			<tr><td align='center' style='font-size:14px'><b>HISTORICOS</b></td></tr><tr ><td class='menuprincipal' onclick='opciones_8(285);'>Hist. Entidad</td></tr><tr ><td class='menuprincipal' onclick='opciones_8(286);'>Hist. Liquidaciones</td></tr><tr ><td class='menuprincipal' onclick='opciones_8(287);'>Hist. Salarios</td></tr></tr></table></span><center><b style='font-size:13px'><a onclick="document.getElementById('sp_8').style.visibility='visible';">HISTORICOS</a></b></center></td></tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr><td width='100px' valign='top'><a class='info' style='cursor:pointer;' onclick="modal2('reportes2.php',0,0,800,1000,'destino'); ">
				<img src='imagenes/datos/impresora.png' width='48' border='0'>
				<span style='width:300px'>Reportes<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal2('administracion_zcalidad.php',0,0,600,900,'win2'); ">
				<img src='imagenes/000/416/dicono_f_calidad.png' width='48' border='0'>
				<span style='width:300px'>SISTEMA DE GESTION DE CALIDAD<br></span></a>
				<a class='info' style='cursor:pointer;' onclick="modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=251',0,0,800,1000,'destino'); ">
				<img src='imagenes/000/251/empleados.png' width='48' border='0'>
				<span style='width:300px'>EMPLEADOS<br></span></a>
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