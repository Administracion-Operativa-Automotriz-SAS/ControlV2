<?php

/**
 * Funciones para menu de inicio
 *
 * @version $Id$
 * @copyright 2008
 */


function inicio()
{
	$Archivo_perfil='perfil_'.$_SESSION['User'].'.php';
	$Archivo_bienvenida='bienvenida_aguila7.php';
	if($Script_Inicial=qo1("select script_entrada from usuario where id='".$_SESSION['User']."'")) eval($Script_Inicial);

	if(file_exists($Archivo_perfil))
		include($Archivo_perfil);
	else
	{
		$A=fopen($Archivo_perfil,"w+");
		$UU=qo("select ancho_menu_superior as am,inicio_cols as nc from usuario where id=".$_SESSION['User']);
		fputs($A,"<?php
			/*Archvio inicial */
			?>
			<script language='javascript'>
				function mod_rep(Id)	{if(Id) modal('reportes.php?Acc=menu_rep&idreporte='+Id,0,0,750,1150);}
				function run_rep(Id)	{if(Id) modal2('reporte.php?ID='+Id,200,100,500,700,'_blank');}

			function fija_destino()
			{
				var PP=document.getElementById('destino');
				PP.height=document.body.clientHeight-document.getElementById('Menu_Principal').clientHeight-30;
			}

			function avanza_destino()
			{
				var DD=document.getElementById('destino').contentWindow;
				DD.history.forward();
			}

			function retrocede_destino()
			{
				var DD=document.getElementById('destino').contentWindow;
				DD.history.back();
			}



			");
		if($_SESSION['Disenador'])
		{
			$T_ut=tu('usuario_tab','id');
			fputs($A, "
				function modifica_opcion(id)
				{
					modal2('marcoindex.php?Acc=mod_reg&Num_Tabla=$T_ut&id='+id,0,0,700,900);
				}
			");
		}
		if ($Menus = q("select distinct tipo from usuario_tab where usuario='" . $_SESSION['User'] . "' and tipo!='' order by tipo"))
		{
			$Contador_funciones=1;
			$Contador_lineas=1;
			$Cadena_opciones="<body topmargin=1 leftmargin=1 rightmargin=1 bottommargin=1
					onload='fija_destino();' onresize='fija_destino();'
					bgcolor='#ddddff'>
					<table id='Menu_Principal' name='Menu_Principal' border=0 cellspacing=1 width='' bgcolor='ddddff'><tr>";

			while($M=mysql_fetch_object($Menus))
			{
				$Cadena_opciones.="<td width='$UU->am' onmousemove=\"document.getElementById('sp_$Contador_funciones').style.visibility='visible';\"
									onmouseout=\"document.getElementById('sp_$Contador_funciones').style.visibility='hidden';\" align='left'>
				<span id='sp_$Contador_funciones' style='position:absolute;visibility:hidden;width:$UU->am;z-index:255'>
				<table bgcolor='ddeeee' border=0 cellspacing=1 width='$UU->am' cellpadding=0>
				<tr><td align='center' style='font-size:14px'><b>".strtoupper($M->tipo)."</b></td></tr>";
				fputs($A,"function opciones_$Contador_funciones(Dato)
				{
				");

				if($OPCIONES=q("select * from usuario_tab where usuario='" . $_SESSION['User'] . "' and tipo='$M->tipo' order by tipo,descripcion"))
				{
					while ($O = mysql_fetch_object($OPCIONES))
					{
						$Cadena_opciones.="<tr ";
						if($_SESSION['Disenador']) $Cadena_opciones.= "oncontextmenu='modifica_opcion($O->id);return false;' ";
						$Cadena_opciones.="><td class='menuprincipal' onclick='opciones_$Contador_funciones($O->id);'>$O->descripcion</td></tr>";
						fputs($A,"if(Dato==$O->id) { ");
						if($O->tabla=='centro_de_control')
							fputs($A,"modal2('marcoindex.php?Acc=centro_de_control',0,0,$O->valto,$O->vancho,'centro_de_control');");
						elseif (strpos($O->tabla, '.php') != 0 || strpos(' '.$O->tabla, 'http://') != 0 or strpos(' '.$O->tabla, 'https://') != 0 or strpos(' '.$O->tabla, 'chrome://') != 0)
							fputs($A,"modal2('$O->tabla',0,0,$O->valto,$O->vancho,'$O->destino');");
						elseif (esta($O->tabla))
						{
							if ($O->destino=='cabeza')
							{
								fputs($A,"modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=$O->id',0,0,$O->valto,$O->vancho,'destino');");
							}
							else
							{
								fputs($A,"modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=$O->id',0,0,$O->valto,$O->vancho,'$O->destino');");
							}
						}
						fputs($A,"Objeto.value=1; return true;}
						");
					}
				}
				$Cadena_opciones.="</tr></table></span><center><b style='font-size:13px'>".strtoupper($M->tipo)."</b></center></td>";
				fputs($A,"
					}
					");
				$Contador_funciones++;
				$Contador_lineas++;
   				if($Contador_lineas>$UU->nc)
   				{
					$Cadena_opciones.="</tr><tr>";
					$Contador_lineas=0;
   				}
			}
			$Cadena_opciones.="</tr></table><table width='100%' cellspacing=0 cellpaddig=0><tr>";

		}
		fputs($A,"</script>");
		fputs($A,$Cadena_opciones);
		if ($OPCIONES = q("select * from usuario_tab where usuario='" . $_SESSION['User'] . "' and icono=1 order by tipo,descripcion"))
		{
			fputs($A,"<td width='100px' valign='top'>");
			while ($O = mysql_fetch_object($OPCIONES))
			{
				if($O->tabla=='centro_de_control')
					$Onclick = "modal2('marcoindex.php?Acc=centro_de_control',0,0,$O->valto,$O->vancho,'centro_de_control');";
				elseif (strpos($O->tabla, '.php') != 0 || strpos(' '.$O->tabla, 'http://') != 0 or strpos(' '.$O->tabla, 'https://') != 0)
					$Onclick = "modal2('$O->tabla',0,0,$O->valto,$O->vancho,'$O->destino');";
				elseif(esta($O->tabla))
				{
					if($O->destino=='cabeza')
					{
						$Onclick = "modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=$O->id',0,0,$O->valto,$O->vancho,'destino');";
					}
					else
					{
						$Onclick = "modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=$O->id',0,0,$O->valto,$O->vancho,'$O->destino');";
					}
				}
				fputs($A,"<a class='info' style='cursor:pointer;' onclick=\"$Onclick \">
					<img src='$O->dicono_f' width='48' border='0'>
					<span style='width:300px'>$O->descripcion<br>$O->explicacion</span></a>
					");
			}
			fputs($A,"</td>");
		}
		fputs($A,"<td>
		<iframe name='destino' id='destino' src='bienvenida_aguila7.php' frameborder='no' height='600' width='99%' scrolling='auto'></iframe>
		</td></tr></table>
		<a class='sinfo' style='cursor:pointer' onclick=\"mata_perfil();\"><img src='gifs/standar/stop_16.png' border=0><span>Cerrar Sesión</span></a>
		<a class='sinfo' style='cursor:pointer'
			onclick=\"document.location='marcoindex.php'\"><img src='gifs/standar/home_16.png' border=0><span style='width:100px'>Ir al inicio de la aplicacion</span></a>
		<a class='sinfo' style='cursor:pointer' onclick='retrocede_destino()'><img src='gifs/standar/izquierda.png' border=0><span style='width:100px'>Retroceder.</span></a>
		<a class='sinfo' style='cursor:pointer' onclick='avanza_destino()'><img src='gifs/standar/derecha.png' border=0><span style='width:100px'>Avanzar.</span></a>
		<a class='sinfo' style='cursor:pointer'
			onclick=\"window.open('marcoindex.php?Acc=reconstruye_inicio_perfil','_self')\"><img src='gifs/standar/Recycle.png' border=0>
			<span style='width:200px'>Reconstruir el menu principal.</span></a>
		<?php
		/* busqueda del cambio de perfil */
		if(is_file('inc/cp/'.\$_SESSION['Nick'].'.php')) include_once('inc/cp/'.\$_SESSION['Nick'].'.php');
		include_once('inc/misreportes.php');
		?>
		<i>Desarrollado en: Aguila 7.0 Enero de 2010 &reg <a href='mailto:administracion@intercolombia.com'>Arturo Quintero R.</a></i>
		<iframe name='if_cron' id='if_cron' src='marcoindex.php?Acc=runcron' width='2' height='2' frameborder='no'></iframe>
		<iframe name='if_sesion' id='if_sesion' src='marcoindex.php?Acc=mantiene_sesion' width=2 height=2 frameborder='no'></iframe>
		</body>");
		fclose($A);
		echo "<script language='javascript'>document.location.reload();</script>";
	}
}

function reconstruye_inicio_perfil()
{
   if($U=$_SESSION['User'])
   {
      $Archivo_perfil='perfil_'.$U.'.php';
      unlink($Archivo_perfil);
   }
   header('location:marcoindex.php');
}

?>