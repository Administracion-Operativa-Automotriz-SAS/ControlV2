<?php

/**
 *    MENU INDEX ... FUNCIONES ESPECIALES DEL PANEL DE CONTROL
 *
 * @version $Id$
 * @copyright 2008
 */
html();
include_once('inc/Zip/Zip.php');

function centro_de_control()
{
	echo "<HTML><TITLE>Centro de control de diseño de tablas</TITLE>
	<FRAMESET FRAMEBORDER='0' BORDER='5' ROWS='100px,*' COLS='100%' >
	<FRAME SRC='marcoindex.php?Acc=menu_centro_de_control' NAME='indice' SCROLLING='auto'>
	<FRAME NAME='dest' SCROLLING='auto'></frameset>";
}

function menu_centro_de_control()
{
	html();
	echo "<body oncontextmenu='return false' bgcolor='#dddddd'>" . titulo_modulo('CENTRO DE CONTROL DESARROLLADOR', 0) . "
	<TABLE WIDTH='100%' BORDER>
	<tr><td>";
	$TABLAS = q("show tables");
	echo "<form action='marcoindex.php' target='dest' method='post'>
		Seleccione la tabla que desea modificar: <select name='nt' onChange='this.form.submit();'><option></option>";
	while ($TABLA = mysql_fetch_row($TABLAS))
	{
		$L = strlen($TABLA[0]);
		$UL = substr($TABLA[0], $L-2, 2);
		if($UL != '_t' && substr($TABLA[0], 0, 15) != 'control_reporte' && $UL != '_s') echo "<option value=$TABLA[0]>$TABLA[0]</option>";
	}
	echo "<input type='hidden' name='Acc' value='control_vertabla'>
	</select></form></td>
	<form name='creat' id='creat'>
	<td>Crear Tabla: <INPUT TYPE='text' NAME='nt' SIZE=20 STYLE='font-family:Arial;font-size:11;'>
	<input type='button' value='Crear Tabla' onclick=\"modal('marcoindex.php?Acc=control_crear_tabla&nt='+document.creat.nt.value,0,0,100,100,'Crear_tabla');\">
	</td><td>
	<a href='marcoindex.php?Acc=control_especiales' target='dest'>Opciones Especiales</a></td>
	<td><a href='marcoindex.php?Acc=sql' target='dest'>My SQL</a> <a href='marcoindex.php?Acc=pgsql' target='dest'>Postgres</a> </td>
	</tr></table>
	";
}

function control_vertabla()
{
	global $nt,$SOLO_ADICION;
	require('inc/control.general.tabla.php');
}

function control_crear_tabla()
{
	global $nt;
	q("create table $nt (id int(10) auto_increment not null primary key)");
	q("create table " . $nt . "_t like usuario_tab_t ");
	q("insert into " . $nt . "_t (id,campo,tipo,descripcion,ver,alinea)VALUES (1, 'id', ' int(11)', 'id', 1, 'I')");
	echo "
	<script language='javascript'>
	function carga()
	{
		if(opener)
		{
			opener.location.reload();
		}
		else
		{
			parent.location.reload();
		}
		window.close();void(null);
	}
	</script><body onload='carga()'>";
}



function control_especiales()
{
	html();
	echo "<body><h3 align='center'><b>CONTROLES ESPECIALES</B></H3>
	<TABLE BORDER CELLSPACING=0>
	<TR><TD>" .
	titulo_modulo('Duplicar permisos de un usuario en otro', 0) . "<br>
	<form action='marcoindex.php' method='post' target='_self' name='forma' id='forma'>
		Usuario Original:<BR>".menu1("usuario1","select id,nombre from usuario order by nombre")."<BR><BR>Usuario que recibira los permisos:<br>".menu1("usuario2","select id,nombre from usuario order by nombre");
	echo "<br><br><input type='submit' value='Duplicar Permisos'><input type='hidden' name='Acc' value='control_duplicar_permisos'>
	</form></TD><TD valign='top'>" .
	titulo_modulo('Borrar permisos de un usuario', 0) . "<br>
	<form action='marcoindex.php' method='post' target='_self' name='forma1' id='forma1'>
		Seleccione el usuario:<BR>".menu1("usuario1","select id,nombre from usuario order by nombre");
	ECHO "<br><input type='button' value='Borrar Permisos' onclick=\"if(confirm('Desea borrar los permisos?')) document.forma1.submit();\">
		<input type='hidden' name='Acc' value='control_borrar_permisos'>
	</form></TD>
	<td valign='top'>".titulo_modulo('Mantenimiento Tablas',0)."
	<input type='button' value='Ingresar al módulo de mantenimiento de tablas' onclick=\"window.open('marcoindex.php?Acc=control_mantenimiento_tablas','_self');\">
	</td></tr></table>";
}

function control_borrar_permisos()
{
	global $usuario1;
	html();
	if($usuario1 == 1) die("No se permite borrar los permisos del SuperUsuario");
	$NU = qo1("select concat(idnombre,' : ',nombre) from usuario where id='$usuario1'");
	echo "<body oncontextmenu='return false' bgcolor='#dddddd'>" . titulo_modulo('BORRAR PERMISOS', 0) . "
	Borrando permisos del usuario: $NU ...<br>";
	q("delete from usuario_tab where usuario='$usuario1'");
	echo "PERMISOS ELIMINADOS</BODY>";
}

function control_duplicar_permisos()
{
	global $usuario1, $usuario2;
	html();
	echo "<body oncontextmenu='return false' bgcolor='#dddddd'>" . titulo_modulo('DUPLICAR PERMISOS', 0) . "
	Generando la estructura actual<br>";
	$Estructura = q("show create table usuario_tab",1);
	echo "Ajustando la estructura obtenida..<br>";
	$Est = mysql_fetch_row($Estructura);
	$Est_final = str_replace("\n", "", $Est[1]);
	$Est_final = str_replace("usuario_tab", "temp", $Est_final);
	echo "Estructura final:<br>";
	echo $Est_final;
	$Est_final = l($Est_final, strpos($Est_final, 'ENGINE=')-1);
	echo "Creando tabla temporal..<br> $Est_final";
	q($Est_final);
	echo "Poblando tabla temporal..<br>";
	$id = q("insert into temp select * from usuario_tab where usuario=$usuario1");
	echo "Ajustando datos en la tabla temporal..<br>";
	$Mayor = qo1("select max(id) from usuario_tab") + 1;
	q("update temp set usuario=$usuario2");
	# q("alter table temp drop primary key");
	q("alter table temp drop column id");
	q("alter table temp auto_increment=$Mayor");
	q("alter table temp add column id int auto_increment primary key first");
	echo "Insertando nuevo perfil de seguridad..<br>";
	q("insert into usuario_tab select * from temp");
	q("drop table temp");
	echo "FIN DE LA OPERACION.</body>";
}

function control_add_campo()
{
	global $Ver,$Nover,$Coma,$Caja,$Password,$Modificar,$Llave,$Pasainfo,$Nombrecampo,$nt,$Tipocampo,$Sizecampo,$Defectocampo,$Capa,$Descripcion,
		$Traet,$Trael,$Traen,$Pasainfo,$Orden,$suborden,$rowspan1,$rowspan2,$rutaimg,$alinea,$Sizecap,$alinea,$fondo_desc,$fondo_celda,$obligan,$cols_text,$rows_text,
		$rutaimg,$tamrecimg;
	if($Tipocampo=='vchar ') $Tipocampo='varchar ';
	$Ver = sino($Ver);
	$Nover = sino($Nover);
	$Coma = sino($Coma);
	$Caja = sino($Caja);
	$Password = sino($Password);
	$Modificar = sino($Modificar);
	$Llave = sino($Llave);
	$Pasainfo = sino($Pasainfo);
	$obligan=sino($obligan);
	if(!empty($Nombrecampo))
	{
		$Nombrecampo = strtolower($Nombrecampo);
		$Nombre = str_replace(" ", "_", $Nombrecampo);
		$Campos = q("show columns from $nt");
		$Encuentra = 0;
		while($RC = mysql_fetch_row($Campos))
		{
			if(strcmp($RC[0], $Nombrecampo) == 0) $Encuentra = 1;
		}
		if($Encuentra == 1)
		{
			$Comando = "alter table $nt change column $Nombrecampo ";
		}
		else $Comando = "alter table $nt add column ";
		$Comando .= " $Nombrecampo ";
		if(strpos('  char varchar int unsigned tinyint unsigned smallint unsigned bigint unsigned float unsigned decimal unsigned double unsigned', $Tipocampo))
			$Tipocampo = str_replace(" ", "($Sizecampo)", $Tipocampo);
		$Comando .= " $Tipocampo ";
		if(!empty($Defectocampo)) $Comando .= " default \"$Defectocampo\" ";
		$Comando .= " not null ";
		if($Encuentra == 1)
		{
			q("update " . $nt . "_t set tipo='$Tipocampo' where campo='$Nombrecampo'");
		}
		q($Comando);
		if($I = q("select id from " . $nt . "_t where campo='$Nombrecampo'"))
			q("update " . $nt . "_t set tipo='$Tipocampo', capa='$Capa', descripcion='$Descripcion',ver='$Ver',traet='$Traet',traen='$Traen',trael='$Trael',
				coma='$Coma',caja='$Caja', modificar='$Modificar',llave='$Llave', pasa_descripcion='$Pasainfo',orden='$Orden',suborden='$suborden',
				rowspan1='$rowspan1',rowspan2='$rowspan2',rutaimg='$rutaimg',alinea='$alinea' where id='$I'");
		else
			q("insert into " . $nt . "_t (orden,suborden,campo,tipo,descripcion,ver,traet,traen,trael,coma,caja,modificar,
												llave,pasa_descripcion,nover,capa,sizecap,alinea,fondo_desc,fondo_celda,obligan,cols_text,rows_text,rutaimg,tamrecimg) values
											('$Orden','$suborden','$Nombrecampo','$Tipocampo','$Descripcion','$Ver','$Traet',\"$Traen\",'$Trael',$Coma,$Caja,
											$Modificar,$Llave,$Pasainfo,$Nover,'$Capa',$Sizecap,'$alinea','$fondo_desc','$fondo_celda','$obligan','$cols_text','$rows_text',
											'$rutaimg','$tamrecimg')");
	}

	if($SOLO_ADICION)
	echo "
	<script language='javascript'>
	function carga()
	{
		if(opener)
		{
			opener.location.reload();
		}
		else
		{
			parent.location='marcoindex.php?Acc=control_vertabla&nt=$nt';
		}
		window.close();void(null);
	}
	</script><body onload='carga()'>";
	else
	echo "
	<script language='javascript'>
	function carga()
	{
		if(opener)
		{
			opener.location.reload();
		}
		else
		{
			parent.location='marcoindex.php?Acc=control_vertabla&nt=$nt';
		}
		window.close();void(null);
	}
	</script><body onload='carga()'>";

}

function control_add_index()
{
	global $nt,$nombrei,$campos,$unico;
	$unico=sino($unico);
	q("alter table $nt add ".($unico?" unique ":"")." index $nombrei ($campos)");
	echo "<body onload=\"window.close();void(null);opener.location='marcoindex.php?Acc=control_vertabla&nt=$nt';\">";
}

function control_del_index()
{
	global $nt,$nombrei;
	q("alter table $nt drop index $nombrei ");
	echo "<body onload=\"window.close();void(null);opener.location='marcoindex.php?Acc=control_vertabla&nt=$nt';\">";
}

function control_inicializar_tabla()
{
	global $nt;
	q("create table ".$nt."_t like usuario_tab_t");
	$Columnas=q("show columns from $nt");
	$orden=2;
	while($R=mysql_fetch_object($Columnas))
	{
		q("insert into ".$nt."_t (orden,campo,tipo,descripcion,ver,modificar) values ('$orden','$R->Field','$R->Type','$R->Field',1,1)");
		$orden+=2;
	}
	echo "<body onload=\"window.close();void(null);opener.location='marcoindex.php?Acc=control_vertabla&nt=$nt';\">";
}

function control_orden_campo()
{
	require('inc/gpos.php');
	$ver = sino($ver);
	$nover = sino($nover);
	$modificar = sino($modificar);
	$llave = sino($llave);
	q("update " . $nt . "_t set capa='$capa', orden='$orden',suborden='$suborden',
		coldes='$coldes', columnas='$columnas', descripcion='$descripcion', tipo='$tipo', ver='$ver', alinea='$alinea',nover='$nover',
		modificar='$modificar',llave='$llave' where campo='$campo'");
	echo "<script language='javascript'>
		function carga()
		{
			parent.document.getElementById('mensajes').innerHTML='';
		}
	</script>
	<body onload='carga()'></body>";
}

function control_elimina_campo()
{
	global $nt, $campo;
	q("alter table $nt drop column $campo");
	q("delete from " . $nt . "_t where strcmp('$campo',campo)=0");
	echo "<body onload=\"parent.location='marcoindex.php?Acc=control_vertabla&nt=$nt';\">";
}


function control_incrementa_orden()
{
	global $tabla,$campo;
	$orden=qo1("Select orden from $tabla where id=$campo");
	q("update $tabla set orden=orden+1 where orden>=$orden");
	echo "<body onload=\"parent.location='marcoindex.php?Acc=control_vertabla&nt=".substr($tabla,0,strlen($tabla)-2)."';\">";
}

function permisos()
{
	html();
	echo "<body><h3 align='center'><b>CONTROLES ESPECIALES</B></H3>
	<TABLE BORDER CELLSPACING=0>
	<TR><TD>" .
	titulo_modulo('Duplicar permisos de un usuario en otro', 0) . "<br>
	<form action='marcoindex.php' method='post' target='_self' name='forma' id='forma'>
		Usuario Original:<BR>";
	$CAMPO = 'usuario1';
	$TABLA = 'usuario';
	$COLUMNAS = 'nombre,id';
	$BOUND = 1;
	include('inc/combo.php');
	ECHO "<BR><BR>Usuario que recibira los permisos:<br>";
	$CAMPO = 'usuario2';
	$TABLA = 'usuario';
	$COLUMNAS = 'nombre,id';
	$BOUND = 1;
	include('inc/combo.php');
	echo "<br><br><input type='submit' value='Duplicar Permisos'><input type='hidden' name='Acc' value='control_duplicar_permisos'>
	</form></TD><TD>" .
	titulo_modulo('Borrar permisos de un usuario', 0) . "<br>
	<form action='marcoindex.php' method='post' target='_self' name='forma' id='forma'>
		Usuario Original:<BR>";
	$CAMPO = 'usuario1';
	$TABLA = 'usuario';
	$COLUMNAS = 'nombre,id';
	$BOUND = 1;
	include('inc/combo.php');
	ECHO "<br><input type='submit' value='Borrar Permisos'><input type='hidden' name='Acc' value='control_borrar_permisos'>
	</form></TD></tr></table>";
}

function control_mantenimiento_tablas()
{
	echo "

	<body>
	<form action='marcoindex.php' method='post' target='_blank' name='forma' id='forma'>
	<input type='hidden' name='Acc' value='control_mantenimiento_orden'>
	<table border cellspacing=0><tr>
	<th>Nombre</th>
	<th>Marcación</th>
	<th>Operaciones</th>
	</tr>";
	$TABLAS = q("show tables");
	$mt='';
	$mn='';
	$tt='';
	$mttmpi='';
	$tti='';
	$Alfabeto=array();
	while ($TABLA = mysql_fetch_row($TABLAS))
	{
		$usada=qo1("select id from usuario_tab where tabla='".$TABLA[0]."'");
		echo "<tr><td ".($usada?"bgcolor='#bbbbdd' ":"").">".$TABLA[0]."</td>
		<td align='center'><input type='checkbox' name='mc_".$TABLA[0]."' onclick=\"verifica(this.checked,'".$TABLA[0]."');\"></td>
		<td>
		<input type='button' value='Reparar' onclick=\"modal('marcoindex.php?Acc=control_mantenimiento_orden&orden=reparar&t=".$TABLA[0]."',0,0,300,400,'orden')\">
		<input type='button' value='Eliminar' onclick=\"if(confirm('Desea eliminar ".$TABLA[0]." ?')) modal('marcoindex.php?Acc=control_mantenimiento_orden&orden=eliminar&t=".$TABLA[0]."',0,0,300,400,'orden')\">
		</td>
		</tr>";
		$mt.='Ob.mc_'.$TABLA[0].'.checked=true;';
		$mn.='Ob.mc_'.$TABLA[0].'.checked=false;';
		$tt.=', '.$TABLA[0];
		if(substr($TABLA[0],0,5)=='tmpi_') {$mttmpi.='Ob.mc_'.$TABLA[0].'.checked=true;';$tti.=','.$TABLA[0];}
		$Letra=substr($TABLA[0],0,1);
		$Alfabeto["$Letra"].='Ob.mc_'.$TABLA[0].'.checked=!Ob.mc_'.$TABLA[0].'.checked;';
	}
	echo "</table>
	<script language='javascript'>
		function marcar_todos()
		{	var Ob=document.forma;
			$mt
			Ob.tt.value='$tt';
		}
		function marcar_ninguno()
		{	var Ob=document.forma;
			$mn
			Ob.tt.value='';
		}
		function marcar_temporales_informes()
		{
			marcar_ninguno();
			var Ob=document.forma;
			$mttmpi
			Ob.tt.value='$tti';
		}
		function marcar_a_m()
		{
			var Ob=document.forma;".
			$Alfabeto['a'].$Alfabeto['b'].$Alfabeto['c'].$Alfabeto['d'].$Alfabeto['e'].$Alfabeto['f'].$Alfabeto['g'].$Alfabeto['h'].$Alfabeto['i'].
			$Alfabeto['j'].$Alfabeto['k'].$Alfabeto['l'].$Alfabeto['m']."
		}
		function marcar_n_z()
		{
			var Ob=document.forma;".
			$Alfabeto['n'].$Alfabeto['o'].$Alfabeto['p'].$Alfabeto['q'].$Alfabeto['r'].$Alfabeto['s'].$Alfabeto['t'].$Alfabeto['u'].$Alfabeto['v'].
			$Alfabeto['w'].$Alfabeto['x'].$Alfabeto['y'].$Alfabeto['z']."
		}
		function verifica(d,t)
		{
			if(d)
			{	document.forma.tt.value+=','+t;}
			else
			{
				var cadena=document.forma.tt.value;
				if(cadena.indexOf(','+t+',')>-1)
					document.forma.tt.value=cadena.replace(','+t+',',',');
				else
					document.forma.tt.value=cadena.replace(','+t,'');
			}
		}
	</script>
	<input type='hidden' name='tt' value=''>
	<input type='button' value='Marcar Todos' onclick=\"marcar_todos();\">
	<input type='button' value='Marcar ninguno' onclick=\"marcar_ninguno();\">
	<input type='button' value='Marcar Temporales de Informes' onclick=\"marcar_temporales_informes();\">
	<hr>
	Con las tablas marcadas:
	<select name='orden' onchange=\"if(this.value=='eliminar') alert('Ojo va a eliminar las tablas marcadas');\"><option></option>
	<option value='analizar'>Analizar</option>
	<option value='chequear'>Chequear</option>
	<option value='optimizar'>Optimizar</option>
	<option value='reparar'>Reparar</option>
	<option value='charset'>Convertir a Latin_spanish_ci</option>
	<option value='eliminar'>Eliminar</option>
	<option value='' disabled >--------</option>
	<option value='backup1'>Backup sin datos</option>
	<option value='backup2'>Backup con datos</option>
	<option value='backup3'>Backup a un archivo comprimido parcial</option>
	<option value='backup4'>Backup a un archivo comprimido total</option>
	</select>
	<input type='submit' value='Procesar'>
	</form>";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['a']."this.style.color='#990000';\">A</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['b']."this.style.color='#990000';\">B</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['d']."this.style.color='#990000';\">C</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['d']."this.style.color='#990000';\">D</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['e']."this.style.color='#990000';\">E</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['f']."this.style.color='#990000';\">F</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['g']."this.style.color='#990000';\">G</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['h']."this.style.color='#990000';\">H</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['i']."this.style.color='#990000';\">I</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['j']."this.style.color='#990000';\">J</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['k']."this.style.color='#990000';\">K</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['l']."this.style.color='#990000';\">L</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['m']."this.style.color='#990000';\">M</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['n']."this.style.color='#990000';\">N</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['o']."this.style.color='#990000';\">O</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['p']."this.style.color='#990000';\">P</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['q']."this.style.color='#990000';\">Q</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['r']."this.style.color='#990000';\">R</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['s']."this.style.color='#990000';\">S</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['t']."this.style.color='#990000';\">T</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['u']."this.style.color='#990000';\">U</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['v']."this.style.color='#990000';\">V</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['w']."this.style.color='#990000';\">W</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['x']."this.style.color='#990000';\">X</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['y']."this.style.color='#990000';\">Y</a> ";
	echo "<a style='cursor:pointer;' onclick=\"var Ob=document.forma;".$Alfabeto['z']."this.style.color='#990000';\">Z</a> ";
	echo "<a style='cursor:pointer;' onclick=\"marcar_a_m();this.style.color='#990000';\">A-M</a> ";
	echo "<a style='cursor:pointer;' onclick=\"marcar_n_z();this.style.color='#990000';\">N-Z</a> ";
	echo "<hr>".$_SERVER['DOCUMENT_ROOT']."<HR>";
	echo "</body>";
}

function control_mantenimiento_orden()
{
	require('inc/gpos.php');
	$Directorio=DIRECTORIO_BACKUPS;

	$t='';
	foreach($_POST as $Campo => $Valor)
	{
		if(l($Campo,3)=='mc_')
		{
			$t.=','.substr($Campo,3);
		}
	}
	global $orden,$tt,$t1;
	if($tt) $t=substr($t,1);
	if($t1) $t=$t1;
	if($orden && $t)
	{
		if($orden=='eliminar')
		{
			q("drop table $t");
			echo "<body onload='window.close();void(null);opener.location.reload();'>";
			die();
		}

		html();
		echo "<body onload='centrar(500,500);'>";
		switch($orden)
		{
			case 'analizar':
									echo titulo_modulo("Análisis de ".str_replace(',',' ',$t));
									if($RS=q("analyze table $t")) control_mantenimiento_resultado($RS,"Análisis");
									break;
			case 'chequear':
									echo titulo_modulo("Chequeo extendido de ".str_replace(',',' ',$t));
									if($RS=q("check table $t extended")) control_mantenimiento_resultado($RS,"Chequeo extendido");
									break;
			case 'optimizar':
									echo titulo_modulo("Optimización de ".str_replace(',',' ',$t));
									if($RS=q("optimize table $t")) control_mantenimiento_resultado($RS,"Optimización");
									break;
			case 'reparar':
									echo titulo_modulo("Reparación extendida de ".str_replace(',',' ',$t));
									if($RS=q("repair table $t extended")) control_mantenimiento_resultado($RS,"Reparación extendida");
									break;
			case 'backup1':   echo titulo_modulo("Backup sin datos de ".str_replace(',',' ',$t));
									q("set SQL_QUOTE_SHOW_CREATE=0");
									$tts=explode(',',$t);
									for ($i=0;$i<count($tts);$i++)
									{
										if($RS=q("show create table ".$tts[$i]))
										{
											$Rs=mysql_fetch_row($RS);
											echo "<br /><br />".$Rs[1];
										}
									}
									break;
			case 'backup2':   echo titulo_modulo("Backup con datos SQL de ".str_replace(',',' ',$t));
									q("set SQL_QUOTE_SHOW_CREATE=0");
									$tts=str_replace(',',' ',$t);
									$Archivo_destino=MYSQL_D."_".date(Ymd).".sql";
									$Comando="mysqldump --host=".MYSQL_S." --user=".MYSQL_U." --password=".MYSQL_P." --compact --add-drop-table --extended-insert --default-character-set=latin1 --skip-set-charset --skip-comments --skip-quote-names ".MYSQL_D." $tts > $Archivo_destino";
									system($Comando);
									echo "<HR><a href='".$Archivo_destino."' target='_blank'>Click aqui para descargar</a> $Comando<Hr>";
									break;
			case 'backup3':   echo titulo_modulo("Backup con datos ZIP parcial de ".str_replace(',',' ',$t));
									q("set SQL_QUOTE_SHOW_CREATE=0");
									$tts=str_replace(',',' ',$t);
									$Archivo_destino=MYSQL_D."_".date('Ymd').".7z";
									$Comando="mysqldump --host=".MYSQL_S." --user=".MYSQL_U." --password=".MYSQL_P." --compact --add-drop-table --extended-insert --default-character-set=latin1 --skip-set-charset --skip-comments --skip-quote-names ".MYSQL_D." ".$tts." | bzip2 > $Archivo_destino";
									if(@file($Archivo_destino)) unlink($Archivo_destino);
									system($Comando);
									echo "<HR><a href='".$Archivo_destino."' target='_blank'>Click aqui para descargar el resultado</a>";
									break;
			case 'backup4':   echo titulo_modulo("Backup con datos ZIP total de ".str_replace(',',' ',$t));
									q("set SQL_QUOTE_SHOW_CREATE=0");
									$tts=str_replace(',',' ',$t);
									$Archivo_destino=MYSQL_D."_".date('Ymd').".zip";
									echo "$Archivo_destino Document Root: ".$_SERVER['DOCUMENT_ROOT']." ".URL."<br><br>";
									$Comando="mysqldump --host=".MYSQL_S." --user=".MYSQL_U." --password=".MYSQL_P." --compact --add-drop-table --extended-insert --default-character-set=latin1 --skip-set-charset --skip-comments --skip-quote-names ".MYSQL_D." | bzip2 > $Archivo_destino";
									if(@file($Archivo_destino)) unlink($Archivo_destino);
									system($Comando);
									echo "<HR><a href='".$Archivo_destino."' target='_blank'>Click aqui para descargar</a> $Comando<Hr>";
									break;
			case 'charset':
			                  echo titulo_modulo("conversion a charset latin_spanish_ci");
			                  $tts=explode(',',$t);
			                  require('inc/link.php');
									for ($i=0;$i<count($tts);$i++)
									{
										$RS=mysql_query("alter table ".$tts[$i]." convert to character set latin1 collate latin1_spanish_ci",$LINK);
										echo "<br /><br />".$tts[$i];
									}
									mysql_close($LINK);
		}
		echo "</body>";
	}
}

function control_mantenimiento_resultado($RS,$titulo)
{
	echo "
	<h2>$titulo</h2>
	<table border cellspacing=0><tr><th>Tabla</th><th>Opcion</th><th>Tipo mensaje</th><th>Mensaje</th></tr>";
	while ($R=mysql_fetch_object($RS))
	{
		echo "<tr>
			<td>$R->Table</td>
			<td>$R->Op</td>
			<td>$R->Msg_type</td>
			<td>$R->Msg_text</td>
			</tr>";
	}
	echo "</table>";
}

function control_campo_reportes()
{
	global $Tabla,$Campo;
	html();
	echo "<body>".titulo_modulo("Reportes con el campo <B>$Tabla.$Campo</B>");
	if($Reportes=q("select distinct rp.clase,rp.nombre,rp.instruccion from aqr_reporte rp, aqr_reporte_table ta, aqr_reporte_field fi
			where ta.idreporte=rp.id and ta.nombre='$Tabla' and (fi.nombre like concat('%',ta.apodo,'.$Campo','%') or rp.donde like '%$Campo%' )
			and fi.idreporte=rp.id order by rp.clase,rp.nombre"))
	{
		echo "<table cellspacing=0 border><tr><th colspan=8>REPORTES</th></tr><tr>
		<th>Clase</th>
		<th>Nombre</th>
		<th>Instrucción</th>
		</tr>";
		while($R=mysql_fetch_object($Reportes))
		{
			 echo "<tr>
			 <td>$R->clase</td>
			 <td>$R->nombre</td>
			 <td>$R->instruccion</td>
			 </tr>";
		}
		echo "</table>";
	}
	else
	echo "El campo no está siendo usado en ningún informe";

}

function control_campo_tablas()
{
	global $Tabla,$Campo;
	html();
	echo "<body>".titulo_modulo("Tablas con el campo <B>$Tabla.$Campo</B>");
	$TABLAS = q("show tables");
	echo "<table border cellspacing=0><tr><th colspan=8>TABLAS</th></tr><tr>
		<th>Tabla</th>
		<th>Campo</th>
		<th>Descripción</th>
		<th>Tabla Relacionada</th>
		<th>Campo Relacionado</th>
		<th>Nombre Relacionado</th>
		<th>Formula Relacionada</th>
		<th>Vista Relacionada</th>
		</tr>";
	while ($TABLA = mysql_fetch_row($TABLAS))
	{

		$L = strlen($TABLA[0]);
		$UL = substr($TABLA[0], $L-2, 2);
		$TO=substr($TABLA[0],0,$L-2);
		if($UL == '_t')
		{
			$Tabla_ver=$TABLA[0];
			if($Campos=q("select * from $Tabla_ver
				where (traet='$Tabla' and (traen like '%$Campo%' or trael like'%$Campo%')) or
				(traex like '%$Tabla%' and traex like'%$Campo%') or (verx like '%$Tabla%' and verx like'%$Campo%')"))
			{
				while($C=mysql_fetch_object($Campos))
				{
					echo "<tr>
					<td>$TO</td>
					<td>$C->campo</td>
					<td>$C->descripcion</td>
					<td>$C->traet</td>
					<td>$C->trael</td>
					<td>$C->traen</td>
					<td>$C->traex</td>
					<td>$C->verx</td>
					</tr>";
				}
			}
		}
	}
	echo "</table>";
}


function control_campo_tablasreportes()
{
	global $Tabla;
	html();
	echo "<body>".titulo_modulo("Tablas y Reportes con la tabla <B>$Tabla</B>");
	if($Reportes=q("select distinct rp.clase,rp.nombre from aqr_reporte rp, aqr_reporte_table ta
			where ta.idreporte=rp.id and ta.nombre='$Tabla' order by rp.clase,rp.nombre"))
	{
		echo "<table cellspacing=0 border><tr><th colspan=8>REPORTES</th></tr><tr>
		<th>Clase</th>
		<th>Nombre</th>
		</tr>";
		while($R=mysql_fetch_object($Reportes))
		{
			 echo "<tr>
			 <td>$R->clase</td>
			 <td>$R->nombre</td>
			 </tr>";
		}
		echo "</table>";
	}
	else
	echo "El campo no está siendo usado en ningún informe";
	$TABLAS = q("show tables");
	echo "<br /><br /><table border cellspacing=0><tr><th colspan=8>TABLAS</th></tr><tr>
		<th>Tabla</th>
		<th>Campo</th>
		<th>Descripción</th>
		<th>Tabla Relacionada</th>
		<th>Campo Relacionado</th>
		<th>Nombre Relacionado</th>
		<th>Formula Relacionada</th>
		<th>Vista Relacionada</th>
		</tr>";
	while ($TABLA = mysql_fetch_row($TABLAS))
	{

		$L = strlen($TABLA[0]);
		$UL = substr($TABLA[0], $L-2, 2);
		$TO=substr($TABLA[0],0,$L-2);
		if($UL == '_t')
		{
			$Tabla_ver=$TABLA[0];
			if($Campos=q("select * from $Tabla_ver
				where traet='$Tabla' or traex like '%$Tabla%' or verx like '%$Tabla%' "))
			{
				while($C=mysql_fetch_object($Campos))
				{
					echo "<tr>
					<td>$TO</td>
					<td>$C->campo</td>
					<td>$C->descripcion</td>
					<td>$C->traet</td>
					<td>$C->trael</td>
					<td>$C->traen</td>
					<td>$C->traex</td>
					<td>$C->verx</td>
					</tr>";
				}
			}
		}
	}
	echo "</table>";


}

function control_campos_tabla()
{
	global $nt;
	html();
	echo "<body>".titulo_modulo("Campos de $nt");
	echo "<form name='forma' id='forma'>
	Campo a mostrar: <input type='text' name='campom' id='campom' size=50 maxlength='200'><br>
	<input type='button' value='Asignar información' onclick=\"opener.document.forma1.Traen.value=document.forma.campom.value;window.close();void(null);\">
	<iframe src='marcoindex.php?Acc=control_campos_tabla_marco&NT=$nt' frameborder='no' name='Campos' id='Campos' width='100%' height='80%' scrolling='auto'></iframe>
	</form></body>";
}

function control_campos_tabla_marco()
{
	global $NT;
	$NTt=$NT.'_t';
	html();
	echo "<body>";
	$Campos = q("show columns from $NT");
	if(haytabla($NTt))
	{
		if($Relacionados=q("select campo,traet from $NTt where traet!='' "))
		{
			while($Rel=mysql_fetch_object($Relacionados))
			{
				$Campo='t_'.$Rel->traet.'('.$Rel->campo.')';
				echo "<input type='radio' onclick=\"
					var PD=parent.document.forma.campom; var LPD=PD.value.length;
					if(PD.value)
					{
						if(PD.value.search('concat')>=0)
						{
							PD.value='concat'+'('+PD.value.substr(7,LPD-8)+',\' - \',$Campo'+')';
						}
						else
						{
							PD.value = 'concat'+'('+PD.value+',\' - \',$Campo'+')';
						}
					}
					else
					{
						PD.value='$Campo';
					}
					\">$Campo <br />";
			}
		}
	}
	echo "<hr>";
	while($C = mysql_fetch_object($Campos))
	{
		echo "<input type='radio' onclick=\"
			var PD=parent.document.forma.campom; var LPD=PD.value.length;
			if(PD.value)
			{
				if(PD.value.search('concat')>=0)
				{
					PD.value='concat'+'('+PD.value.substr(7,LPD-8)+',\' - \',$C->Field'+')';
				}
				else
				{
					PD.value = 'concat'+'('+PD.value+',\' - \',$C->Field'+')';
				}
			}
			else
			{
				PD.value='$C->Field';
			}
			\">$C->Field <br>";
	}
	echo "</body>";
}


function control_cargatabla()
{
	global $t;
	html('CARGA DE TABLA CON ARCHIVO PLANO');
	echo "<script language='javascript'>
		function carga()
		{
			centrar(800,500);
		}
	</script>
	<body onload='carga()'>
	<FORM enctype='multipart/form-data' ACTION='marcoindex.php' METHOD='post' NAME='msubir' ID='msubir' target='_self'>
		<input type='hidden' name='MAX_FILE_SIZE' value='40000000'>
		<input type='hidden' name='Acc' value='control_cargatabla_subir_archivo'>
		<input type='hidden' name='directorio' value='planos/'>
		Archivo que desea subir <input name='userfile' type='file'><br />
		<br />Este archivo debe ser subido en un comprimido .zip<br /><br />
		<input type='submit' value=' Subir el archivo ' style='font-size:14;height:40px'>
		</form>
	</body>";
}

function control_cargatabla_subir_archivo() // permite subir un archivo al servidor
{
	global $directorio;
	if(!$directorio) $directorio='planos/';
	if($up=getFilesVar('userfile'))
	{
		if(is_uploaded_file($up['tmp_name']))
		{
			$uplfile_name = $up['name'];
            $i = 1;
            // pick unused file name
            while (file_exists($directorio.$uplfile_name)) {
              $uplfile_name = ereg_replace('(.*)(\.[a-zA-Z]+)$', '\1_'.$i.'\2', $up['name']);
              $i++;
            }
			$File_destino=strtolower($uplfile_name);
			if (!@move_uploaded_file($up['tmp_name'], $directorio.$File_destino))
			{
				die('error en move_uploaded_file');
            }
			else
			{
				chmod($directorio.$File_destino,0777);
				echo "<script language='javascript'>
					function carga()
					{
						window.open('marcoindex.php?Acc=control_cargatabla_procesa&Acarga=$directorio$File_destino','_self');
					}
				</script>
				<body onload='carga()'></body>";
			}
		}
		else die('fallo en is_uploaded_file');
	}
	else die('Fallo en getFilesVar');
}



function control_cargatabla_procesa()
{
	global $Acarga;
	if(strpos($Acarga,'.zip'))
	{
		html();
		echo "<body onload='carga();centrar();'>
		Archivo correcto <b>$Acarga</b><br />Procesando..";
		$zip = new Archive_Zip($Acarga);
		$Archivodestino=$zip->ListContent();
		$A = $Archivodestino[0]['filename'];
		//echo "Verificando la existencia de $A<br>";
		if( @file_exists( $A ) )
		{
			// si existe lo borra.
			unlink( $A );
			PEAR::setErrorHandling(PEAR_ERROR_PRINT);
			//die();
		}
		if ( !$Lista = $zip->extract(array('add_path'=>dirname($Acarga)) ))
		{
				echo '<br />Error extracting ZIP archive:<br />';
			echo $zip->_error_code . ' : ' . $zip->_error_string . '<br />';
		}
		else
		{
			$Archivofinal = $Lista[0]['filename'];
			$junk = exec("/usr/bin/sudo /bin/chmod 777 $Archivofinal");
			//echo "Extracción Exitosa del archivo <br>$Archivofinal";
			$Lista = $zip->ListContent();
		}
		echo "<br />Archivo=$Archivofinal<br /><br />
		<iframe name='Oculto' id='Oculto' src='' width='100%' height=600 style='visibility:visible' scrolling='auto'></iframe>
		<br />
		<center><span id='Resultado'></span></center>
		<script language='javascript'>
			window.open('util.php?Acc=procesar_archivo_importado_sql&A=$Archivofinal','Oculto');
		</script>

		</body></html>";

	}
	else
	{
		html();
		echo "El archivo cargado no corresponde a un comprimido .zip ";
	}
}






















?>