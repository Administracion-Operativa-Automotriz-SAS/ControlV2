<?php

/**
 * FUNCIONES DE GRABACION
 *
 * @version $Id$
 * @copyright 2008
 */
function q($cadena, $Devolver_sql = 0)
{
	global $Nombre, $Id_alterno, $Num_Tabla,$LINK;
	if(!$LINK = mysql_connect(MYSQL_S, MYSQL_U, MYSQL_P)) die('Problemas con la conexion de la base de datos!');
	mysql_query('SET collation_connection = latin1_spanish_ci',$LINK);
	if(!mysql_select_db(MYSQL_D, $LINK)) die('Problemas con la seleccion de la base de datos');
	if($RQ = mysql_query($cadena, $LINK))
	{
		if(strpos(' ' . strtolower($cadena), 'insert '))
		{
			$IDR = mysql_insert_id($LINK);
			mysql_close($LINK);
			return $IDR;
		}
		if(strpos(' ' . strtolower($cadena), 'update '))
		{
			$AFECTADAS = mysql_affected_rows($LINK);
			mysql_close($LINK);
			return $AFECTADAS;
		}
		if((strpos(' ' . strtolower($cadena), 'select ') || strpos(' ' . strtolower($cadena), 'show ') || strpos(' ' . strtolower($cadena), 'analyze ') || strpos(' ' . strtolower($cadena), 'check ') || strpos(' ' . strtolower($cadena), 'optimize ') || strpos(' ' . strtolower($cadena), 'repair ')
					) && (!strpos(' ' . strtolower($cadena), 'insert ') || !strpos(' ' . strtolower($cadena), 'update ')))
		{
			mysql_close($LINK);
			if($Devolver_sql) return $RQ;
			if(mysql_num_rows($RQ))
			{
				return $RQ;
			}
			else
			{
				return false;
			}
		}
	}
	else
	{
		$Error_de_mysql = mysql_error();
		mysql_close($LINK);
		if(strpos(' ' . $Error_de_mysql, 'Duplicate entry'))
		{
			require('inc/html.php');
			echo "<h3>Entrada Duplicada, no se pudo ingresar el nuevo registro</h3>Debe ";
			if($Num_Tabla)
			{
				echo "<a href='javascript:oculta_edicion($Num_Tabla,false);'>cerrar esta ventana</a> e intentarlo nuevamente.";
			}
			else
				echo "<a href='javascript:window.close();void(null);'>cerrar esta ventana</a> e intentarlo nuevamente.";
			die();
		}
		else
		{
			# debug_print_backtrace();
			echo "<br><br><b>Error en :<br>" . $cadena . "</b><br>Error: $Error_de_mysql<br>";
			die();
		}
	}
}

function qo($cadena)
{
	if($Resultado = q($cadena))
		return mysql_fetch_object($Resultado);
	else return false;
}

function qo1($cadena)
{
	if($Resultado = q($cadena))
	{
		$Registro = mysql_fetch_row($Resultado);
		return $Registro[0];
	}
	else
		return false;
}

function qo1m($Cadenaq, $LINKM)
{
	if($LINKM)
	{
		if($Resultado_inicial = mysql_query($Cadenaq, $LINKM))
		{
			if($Resultado = mysql_fetch_row($Resultado_inicial))
			{
				return $Resultado[0];
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function qom($Cadenaq, $LINKM)
{
	if($LINKM)
	{
		if($Resultado_inicial = mysql_query($Cadenaq, $LINKM))
		{
			if($Resultado = mysql_fetch_object($Resultado_inicial))
			{
				return $Resultado;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function qo2($cadena, $Separador = ', ') ## traer información desde un sql y los resultados se retornan unidos en una cadena con un separador
{
	$Resultado_final = '';
	$Switch_separador = false;
	if($Resultado = q($cadena))
	{
		while($Re = mysql_fetch_row($Resultado))
		{
			$Resultado_final .= ($Switch_separador?$Separador:"") . $Re[0];
			$Switch_separador = true;
		}
		return $Resultado_final;
	}
	else
		return false;
}

function qp($cadena, $Devolver_sql = 0) ## correr un sql en posgres
{
	global $Nombre, $Id_alterno;
	if(!$LINK = pg_connect("host=" . PSQL_S . " port=" . PSQL_SP . " dbname=" . PSQL_D . " user=" . PSQL_U . " password=" . PSQL_P)) die('Problemas con la conexion de la base de datos!');
	if($RQ = pg_exec($LINK, $cadena))
	{
		if((strpos(' ' . strtolower($cadena), 'select ') || strpos(' ' . strtolower($cadena), 'show ')) && (!strpos(' ' . strtolower($cadena), 'insert ') || !strpos(' ' . strtolower($cadena), 'update ')))
		{
			pg_close($LINK);
			if($Devolver_sql) return $RQ;
			if(pg_num_rows($RQ))
			{
				return $RQ;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if(strpos(' ' . strtolower($cadena), 'insert '))
			{
				$IDR = pg_last_oid($RQ);
				pg_close($LINK);
				return $IDR;
			}
			else
			{
				pg_close($LINK);
				return true;
			}
		}
	}
	else
	{
		$Error_de_pgsql = pg_errormessage($LINK);
		pg_close($LINK);
		if(strpos(' ' . $Error_de_pgsql, 'Duplicate entry'))
		{
			echo "<h3>Entrada Duplicada, no se pudo ingresar el nuevo registro</h3>Debe <a href='javascript:window.close();void(null);'>cerrar esta ventana</a> e intentarlo nuevamente.";
			die();
		}
		else
		{
			# debug_print_backtrace();
			echo "<br><br><b>Error en :<br>" . $cadena . "</b><br>Error: $Error_de_pgsql<br>";
			die();
		}
	}
}

function qpo($cadena)
{
	if($Resultado = qp($cadena))
		return pg_fetch_object($Resultado);
	else return false;
}

function qpo1($cadena)
{
	if($Resultado = qp($cadena))
	{
		$Registro = pg_fetch_row($Resultado);
		return $Registro[0];
	}
	else
		return false;
}

function documenta_add()
{
	global $Tabla, $Perfil, $Desc, $id;
	include_once('html/spaw.inc.php');
	require('inc/html.php');
	echo "<body>" . titulo_modulo("<b><i>Documentación:</i> $Desc</b>", 1, 0);
	if($id)
	{
		$R = qo("Select * from app_documenta where id=$id");
		echo "$R->contenido_rt" . ($R->revisado?"":" <b><font color='red'>Contenido sin revisar</font></b>") . "<hr>";
	}
	echo "<font color='#000000'><b>Estimado usuario " . $_SESSION['Nombre'] . ", Usted puede adicionar contenidos, imágenes etc, a este documento de tal forma que sirva como
			documentación para que otros usuarios conozcan sobre esta tabla y sus usos. Gracias por su colaboración.</font> <br />Adicionar contenido:</b>:
			<FORM action='marcoindex.php' name='forma' id='forma'>
			<INPUT type='hidden' name='Acc' value='graba_documenta'>";

	$c_r = new spaweditor('Contenido_rt'/*nombre del campo*/ , stripslashes("<br>")/*valor del campo*/ , null/*tool barr*/ , null, null, '100%', 400);
	$c_r->show();

	echo "<hr>
			<input type='submit' value='GRABAR'> Usuario: " . $_SESSION['Nombre'] . ' - [' . $_SESSION['Nick'] . "]
			<INPUT type='hidden' name='Tabla' value='$Tabla'>
			<INPUT type='hidden' name='Perfil' value='$Perfil'><INPUT type='hidden' name='id' value='$id'>
			</FORM></body>";
}

function graba_documenta()
{
	global $Tabla, $Perfil, $Contenido_rt, $id;
	$Hoy = date('Ymd');
	$Contenido_rt = addslashes($Contenido_rt);
	$Usuario = $_SESSION['Nombre'] . ' [' . $_SESSION['Nick'] . ']';
	require('inc/link.php');
	if($id)
	{
		if(!mysql_query("update app_documenta set usuario='$Usuario',fecha='$Hoy',contenido_rt=concat(contenido_rt,\"$Contenido_rt\"),revisado=0 where id='$id'", $LINK))
		{
			die("Error " . mysql_error($LINK));
		}
	}
	else
	{
		if(!mysql_query("insert into app_documenta (tabla,perfil,usuario,fecha,contenido_rt) values
						('$Tabla','$Perfil','$Usuario','$Hoy',\"$Contenido_rt\")", $LINK))
		{
			die("Error " . mysql_error($LINK));
		}
	}
	mysql_close($LINK);
	echo "<body onload='window.close();void(null);opener.location.reload();'></body>";
}

function icono_sugerencia($Tabla, $Perfil)
{
	return "<a class='info' onclick=\"modal('marcoindex.php?Acc=sugerencia_add&Tabla=$Tabla&Perfil=$Perfil',0,0,400,800,'DOC');\">
				<img src='gifs/buzon_sugerencias.png' border=0><span>Buzón de Sugerencias y Soporte</span></a>";
}

function sugerencia_add()
{
	global $Tabla, $Perfil;
	include_once('html/spaw.inc.php');
	require('inc/html.php');
	echo "<body>" . titulo_modulo("<b><i>Sugerencias:</i></b>", 1, 0);
	echo "<FORM action='marcoindex.php' name='forma' id='forma'>
		  <INPUT type='hidden' name='Acc' value='graba_sugerencia'>";

	$c_r = new spaweditor('Sugerencia'/*nombre del campo*/ , stripslashes("<br>")/*valor del campo*/ , null/*tool barr*/ , null, null, '100%', 100);
	$c_r->show();

	echo "<hr>
			<input type='submit' value='GRABAR'> Usuario: " . $_SESSION['Nombre'] . ' - [' . $_SESSION['Nick'] . "]
			<INPUT type='hidden' name='Tabla' value='$Tabla'>
			<INPUT type='hidden' name='Perfil' value='$Perfil'>
			</FORM></body>";
}

function graba_sugerencia()
{
	global $Tabla, $Perfil, $Sugerencia;
	$Usuario = $_SESSION['Nombre'] . ' [' . $_SESSION['Nick'] . ']';
	$Hoy = date('Ymd');
	require('inc/link.php');
	if(mysql_query("insert into app_sugerencia (tabla,perfil,usuario,fecha,sugerencia_rt) values
						('$Tabla','$Perfil','$Usuario','$Hoy',\"$Sugerencia\")", $LINK))
	{
		$ID = mysql_insert_id($LINK);
	}
	else
		die("Error " . mysql_error($LINK));

	mysql_close($LINK);
	echo "<body onload=\"alert('Número de Registro de Sugerencia / Soporte: $ID Por favor tomelo en cuenta para futuras consultas. Gracias.');window.close();void(null);opener.location.reload();\"></body>";
}

function aplicar_registro($_Cerrando_al_grabar=0)
{
	global $id,$CAMPOSCHECK,$CAMPOSPASS,$Campos_Upd,$Num_Tabla,$D_tag,$VINCULOC,$VINCULOT,$Ultima_capa;
	setcookie('Ultima_capa_'.$Num_Tabla,"$Ultima_capa",time()+60*60*24*15);
	require('inc/conftab.php');
	$PRE_G = 0;	$POS_G = 0;
	if (haytabla($Nombre_tabla . "_s"))
	{
		if($RSS = qo("select * from " . $Nombre_tabla . "_s"))
		{	$PRE_G = $RSS->pre_grabar;	$POS_G = $RSS->post_grabar;}
	}
	if ($CAMPOSCHECK)
	{
		$_CHK=explode(';',$CAMPOSCHECK);	for($i=0;$i<count($_CHK)-1;$i++)	eval("\$_POST['".$_CHK[$i]."']=sino(\$_POST['".$_CHK[$i]."']);");
	}
	if ($CAMPOSPASS) eval($CAMPOSPASS);
	##############   INICIO DE LA GRABACION ###########################################################
	if ($PRE_G && @file_exists($PRE_G)) require($PRE_G);
	$_U=explode(',',$Campos_Upd);$Campos_Upd='';
	if(MODO_GRABACION_MYSQL==2)
		for($i=0;$i<count($_U);$i++) $Campos_Upd.= ($Campos_Upd?",":"").$_U[$i].'=\'".str_replace(chr(39),chr(92).chr(39),$_POST['.$_U[$i].'])."\'';
	else
		for($i=0;$i<count($_U);$i++) $Campos_Upd.= ($Campos_Upd?",":"").$_U[$i].'=\'".$_POST['.$_U[$i].']."\'';

	#########################################################################################################################
	#            VERIFICACION EN APLIACION DEL REGISTRO SI EXISTE O SI ESTA ADICIONANDO
	#########################################################################################################################
	if($id)
	{
		include('inc/link.php');
	   eval("if(!mysql_query(\"Update \$Nombre_tabla set ".$Campos_Upd." where id='\$id'\",\$LINK))
		{
			echo 'No se pudo actualizar el registro ';
			\$Error_de_mysql=mysql_error();
			mysql_close(\$LINK);
			if(strpos(' '.\$Error_de_mysql,'Duplicate entry'))
			{
				include('inc/html.php');
				echo \"<h3>Entrada Duplicada, no se pudo ingresar el nuevo registro</h3>
				Debe <a href='javascript:oculta_edicion(\$Num_Tabla,false);'>cerrar esta ventana</a> e intentarlo nuevamente.\";

			}
			else echo \$Error_de_mysql.'  '.\$Campos_Upd;
			die();
		}");
		mysql_query("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip)
			values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "',
			'" . date('s') . "','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','$Nombre_tabla','M','$id','" . $_SERVER['REMOTE_ADDR'] . "')",$LINK);

		mysql_close($LINK);
		IF($VINCULOC) q("update $Nombre_tabla SET $VINCULOC='$VINCULOT' WHERE id=$id");
		$R = qo("select * from $Nombre_tabla where id=$id");
		if ($POS_G && @file_exists($POS_G)) require($POS_G);
		if(strlen($VALIDACION_MODIFICA)) eval($VALIDACION_MODIFICA);
	}
	else
	{
		include('inc/link.php');
		eval("if(mysql_query(\"insert into \$Nombre_tabla set ".$Campos_Upd."\",\$LINK)) {
			\$id=mysql_insert_id(\$LINK);	}
		else {
			echo 'No se pudo actualizar el registro ';
			\$Error_de_mysql=mysql_error();
			mysql_close(\$LINK);
			if(strpos(' '.\$Error_de_mysql,'Duplicate entry'))
			{
				include('inc/html.php');
				echo \"<h3>Entrada Duplicada, no se pudo ingresar el nuevo registro</h3>
				Debe <a href='javascript:oculta_edicion(\$Num_Tabla,false);'>cerrar esta ventana</a> e intentarlo nuevamente.\";

			}
			else echo \$Error_de_mysql.'  '.\$Campos_Upd;
			die();
		}");
		mysql_query("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip)
			values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "',
			'" . date('s') . "','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','$Nombre_tabla','A','$id','" . $_SERVER['REMOTE_ADDR'] . "')",$LINK);
		mysql_close($LINK);
		IF($VINCULOC) q("update $Nombre_tabla SET $VINCULOC='$VINCULOT' WHERE id=$id");
		$R = qo("select * from $Nombre_tabla where id=$id");
		if($VALIDACION_ADICION) eval($VALIDACION_ADICION);
	}
	#########################################################################################################################
	########################  FINALIZACION DE LA GRABACION ################################################################
	if($_Cerrando_al_grabar)
	{
	   require('inc/html.php');
	   if(isset($_COOKIE['ACABA_DE_ADICIONAR']))
			echo "<body onload=\"oculta_edicion($Num_Tabla);parent.document.location='marcoindex.php?Acc=abre_tabla&Num_Tabla=$Num_Tabla&D_tag=$D_tag&VINCULOC=$VINCULOC&VINCULOT=$VINCULOT';\">";
		else
			echo "<body onload=\"oculta_edicion($Num_Tabla);\">";
	}
	ELSE
	header("location:marcoindex.php?Acc=mod_reg&Num_Tabla=$Num_Tabla&id=$id&VINCULOC=$VINCULOC&VINCULOT=$VINCULOT&D_tag=$D_tag");
}

function grabar_definicion_campo()
{
	require('inc/gpos.php');
	if($Capa)
	{
		setcookie('DC_Capa',$Capa,time()+60*60*24*15);
	}
	$coma = sino($coma);
	$caja = sino($caja);
	$password = sino($password);
	$pasa_descripcion = sino($pasa_descripcion);
	$nueva_tabla = sino($nueva_tabla);
	$nover = sino($nover);
	$blanco0 = sino($blanco0);
	$browdirecto = sino($browdirecto);
	$supermod = sino($supermod);
	$Nohtmladd = sino($Nohtmladd);
	$tagbusca = sino($tagbusca);
	$buscapopup = sino($buscapopup);
	$obliga = sino($obliga);
	$obligan = sino($obligan);
	$busca_ciudad = sino($busca_ciudad);
	if(MODO_GRABACION_MYSQL==2)
	{
		$cond_modi=addslashes(addcslashes($_POST['cond_modi'],"\24"));
		$nocapturar=addslashes(addcslashes($_POST['nocapturar'],"\24"));
		$traex=addslashes(addcslashes($_POST['traex'],"\24"));
		$verx=addslashes(addcslashes($_POST['verx'],"\24"));
		$scambio=addslashes(addcslashes($_POST['scambio'],"\24"));
	}
	else
	{
		$cond_modi=addcslashes($_POST['cond_modi'],"\0..\24");
		$nocapturar=addcslashes($_POST['nocapturar'],"\0..\24");
		$traex=addcslashes($_POST['traex'],"\0..\24");
		$verx=addcslashes($_POST['verx'],"\0..\24");
		$scambio=addcslashes($_POST['scambio'],"\0..\24");
	}
	require('inc/link.php');
	if(!mysql_query("update " . $Nombre_tabla . "_t set descripcion=\"$descripcion\",explicacion=\"$explicacion\",traen='$traen',trael='$trael',traec='$traec',coma=$coma,
		caja='$caja',password=$password,traet='$Traet',traex=\"$traex\",usuario='$usuario',cond_modi=\"$cond_modi\",columnas='$columnas',nueva_tabla='$nueva_tabla',
		orden='$orden',suborden='$suborden',fondo_desc='$fondo_desc',primer_desc='$primer_desc',coldes='$coldes',cols_text='$cols_text',ancho_tabla='$ancho_tabla',
		fondo_celda='$fondo_celda',fondo_campo='$fondo_campo',primer_campo='$primer_campo',pasa_descripcion='$pasa_descripcion',
		nover='$nover',rows_text='$rows_text',scambio=\"$scambio\",verx=\"$verx\",capa='$capa',nocapturar=\"$nocapturar\",blanco0='$blanco0',
		browdirecto='$browdirecto',supermod='$supermod',sizecap='$sizecap',rutaimg=\"$rutaimg\" ,tagbusca='$tagbusca',
		buscapopup='$buscapopup',rowspan1='$rowspan1',rowspan2='$rowspan2',obliga='$obliga',obligan='$obligan',busca_ciudad='$busca_ciudad' where id='$idcampo'",$LINK))
	{echo mysql_error(); mysql_close($LINK);die();}
	mysql_close($LINK);
	echo "<body onload='javascript:window.close();void(null);opener.location.reload();'>";
}

function grabar_addhtmlcampo()
{
	global $idcampo,$Nombre_tabla,$_POST,$contenido;
	if(MODO_GRABACION_MYSQL==2)
	  	$contenido=addslashes(addcslashes($_POST['contenido'],"\0..\39"));
	else
	  	$contenido=addcslashes($_POST['contenido'],"\0..\24");
	echo "$htmladd";
	require('inc/link.php');
	if(!mysql_query("update " . $Nombre_tabla . "_t set htmladd=\"$contenido\" where id=$idcampo",$LINK))
	{echo mysql_error(); mysql_close($LINK);die();}
	echo "<body onload=\"window.close();void(null);\"></body>";
}


function unificar_registros()
{
	global $Tabla,$IDnuevo,$IDviejo,$id;
	require('inc/html.php');
	if(!$IDnuevo || !$IDviejo)
	{

		echo "<body onload='centrar(600,400)'>".titulo_modulo("<B>Unificación de Registros</B>")."
		<FORM action='marcoindex.php' name='forma' id='forma' method='post' target='_self'>
		<INPUT type='hidden' name='Acc' value='unificar_registros'>
		<INPUT type='hidden' name='Tabla' value='$Tabla'>
		<INPUT type='hidden' name='IDviejo' id='IDviejo' value='$id'> Digite el id que va a quedar:
		<input type='text' name='IDnuevo' id='IDnuevo' value='0' size=15 maxlength=15
			onblur=\"if(this.value==document.forma.IDviejo.value) { alert('Ese es el registro viejo');document.forma.IDnuevo.focus();return false;}\">
		<input type='submit' value='Procesar'>
		</FORM></body>";
		die();
	}
	$Error_de_Integridad = 0;
	$TABLAS = tablas();
	$Control_integridad = '';
	echo "Tabla a analizar: $Tabla <table border cellspacing=0>";
	for ($i = 0;$i <= count($TABLAS);$i++)
	{

		if (r($TABLAS[$i], 2) == '_t')
		{
			$Busqueda = l($TABLAS[$i], strlen($TABLAS[$i])-2);
			if($V1 = q("select campo,descripcion,traet from " . $TABLAS[$i] . " where traet='$Tabla'"))
			{
				while ($RV1 = mysql_fetch_object($V1))
				{
					echo "<tr><td>".$TABLAS[$i]."</td><td>$RV1->campo</td><td><td>$RV1->descripcion</td>";
					if($Veces = qo1("select count($RV1->campo) as veces from $Busqueda where $RV1->campo=$IDviejo limit 1"))
					{
						q("update $Busqueda set $RV1->campo=$IDnuevo where $RV1->campo=$IDviejo");
						echo "<td>Procesado.</td>";
					}
					else echo "<td>Sin coincidencias.</td>";
					echo "</tr>";
				}
			}
		}
	}
	echo "</table><center><input type='button' value='CERRAR ESTA VENTANA' onclick='window.close();void(null);'></body>";
}


?>
