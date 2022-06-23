<?php
if(!$Num_Tabla) {
	echo "<body><script language='javascript'>alert('Sesión cerrada. Vuelva a ingresar');</script></body>";
	die();
}
if( $CONFIGURACION_TABLA=q("Select * from usuario_tab where id=$Num_Tabla and usuario=".$_SESSION['User']))
{
	if($RConfiguracion_Tabla=mysql_fetch_object($CONFIGURACION_TABLA))
	{
		$Nombre_tabla=$RConfiguracion_Tabla->tabla;
		$DESCRIPCION=$RConfiguracion_Tabla->descripcion;
		$CABEZA=$RConfiguracion_Tabla->cabeza;
		$PIE=$RConfiguracion_Tabla->pie;
		$VERCAMPOS=$RConfiguracion_Tabla->vercampos;
		$LPP=$RConfiguracion_Tabla->lineas_por_pagina;
		$DISENO=$RConfiguracion_Tabla->diseno;
		$EXPLICACION=$RConfiguracion_Tabla->explicacion;
		$SCRIPT_ADD=$RConfiguracion_Tabla->script_adicion;
		$SCRIPT_PREMOD=$RConfiguracion_Tabla->script_premod;
		$muestra_adiciona=$RConfiguracion_Tabla->adiciona==1?'si':'no';
		$muestra_modifica=$RConfiguracion_Tabla->modifica==1?'si':'no';
		$muestra_borra=$RConfiguracion_Tabla->borra==1?'si':'no';
		$condicion_tabla=$RConfiguracion_Tabla->condicion;
		$orden_tabla=$RConfiguracion_Tabla->ordeninicial;
		$Condi_Modi=$RConfiguracion_Tabla->condi_modi;
		$Condi_Elim=$RConfiguracion_Tabla->condi_elim;
		$No_vista_impresion=$RConfiguracion_Tabla->novistaimpresion;
		$VINCULOS=explode("\r",$RConfiguracion_Tabla->vinculos);
		$VALIDACION_ADICION=$RConfiguracion_Tabla->validacion_adicion;
		$VALIDACION_MODIFICA=$RConfiguracion_Tabla->validacion_modifica;
		$VALIDACION_ENLINEA=$RConfiguracion_Tabla->validacion_enlinea;
		$VALIDACION_CANCELAR=$RConfiguracion_Tabla->validacion_cancelar;
		$VINCULOS_EDICION=explode("\r",$RConfiguracion_Tabla->vinculoed);
		$LINEAS_VINCULOED=$RConfiguracion_Tabla->lineas_vinculoed;
		$JAVA_HEAD=$RConfiguracion_Tabla->java_head;
		$Imagen_tabla=$RConfiguracion_Tabla->dicono_f;
		mysql_free_result($CONFIGURACION_TABLA);
	}
	else
		die('Error en la configuracion de la tabla');
}
else
{
	q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip)
			values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "','" . date('s') . "','$Nick','$Nombre','$Num_Tabla','X','','" . $_SERVER['REMOTE_ADDR'] . "')");
	die('Problema de Seguridad, este evento ha quedado registrado en la bitacora de seguridad.');
}
?>
