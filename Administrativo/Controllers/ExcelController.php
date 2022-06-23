<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);

include_once(dirname(__FILE__).'/../config/config.php');

require_once(dirname(__FILE__).'/../inc/gpos.php');

require_once('Classes/PHPExcel/IOFactory.php');

if($_GET['acc']=="test")
{	
	try {
		test();
	} catch (Exception $e) {
		echo 'Excepción capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($_GET['acc']=="crit_proov")
{	
echo "catched";
	try {
		crit_proov();
	} catch (Exception $e) {
		echo 'Excepción capturada: ',  $e->getMessage(), "\n";
	}
	
}

function query($cadena, $Devolver_sql = 0,&$_Cantidad_registros_afectados=0) // corre un query invocado internamente
{
	global $Nombre, $Id_alterno, $Num_Tabla,$LINK;

	if(!$LINK = mysql_connect(MYSQL_S, MYSQL_U, MYSQL_P)) die('Problemas con la conexion de la base de datos!');
	if(!mysql_select_db(MYSQL_D, $LINK)) die('Problemas con la seleccion de la base de datos');
	//mysql_set_charset('iso-8859-1',$LINK);
	if($RQ = mysql_query($cadena, $LINK))
	{
		if($Devolver_sql) { mysql_close($LINK); return $RQ; }
		if(strpos(' ' . strtolower($cadena), 'insert ')) { $IDR = mysql_insert_id($LINK); $_Cantidad_registros_afectados=mysql_affected_rows($LINK); mysql_close($LINK); return $IDR; }
		if(strpos(' ' . strtolower($cadena), 'update ')) { $AFECTADAS = mysql_affected_rows($LINK); mysql_close($LINK); return $AFECTADAS; }
		//if(strpos(' ' . strtolower($cadena), 'create')) { $_Cantidad_registros_afectados=mysql_affected_rows($LINK); mysql_close($LINK); return true; }
		if((strpos(' ' . strtolower($cadena), 'select ') || strpos(' ' . strtolower($cadena), 'show ') || strpos(' ' . strtolower($cadena), 'analyze ') || strpos(' ' . strtolower($cadena), 'check ') || strpos(' ' . strtolower($cadena), 'optimize ') || strpos(' ' . strtolower($cadena), 'repair ')
			) && (!strpos(' ' . strtolower($cadena), 'insert ') || !strpos(' ' . strtolower($cadena), 'update ') ))
		{
			mysql_close($LINK); if($Devolver_sql) return $RQ; if(mysql_num_rows($RQ)) return $RQ; else return false;
		}
	}
	else
	{
		$Error_de_mysql = mysql_error();	mysql_close($LINK);
		if(strpos(' ' . $Error_de_mysql, 'Duplicate entry'))
		{
			html();
			echo "<h3>Entrada Duplicada, no se pudo ingresar el nuevo registro</h3><script language='javascript'>alert('ENTRADA DUPLICADA, el registro no se pudo modificar o guardar.');</script>Debe ";
			if($Num_Tabla) { echo "<a href='javascript:oculta_edicion($Num_Tabla,false);'>cerrar esta ventana</a> e intentarlo nuevamente."; }
			else echo "<a href='javascript:window.close();void(null);'>cerrar esta ventana</a> e intentarlo nuevamente.";
			if(u('perfil')==1) echo "<br />$Error_de_mysql";
			die();
		}
		else
		{
			echo "<br><br><b>Error en :<br>" . $cadena . "</b><br>Error: $Error_de_mysql<br>";
			enviar_mail(FROM_SOPORTE,NOMBRE_APLICACION.' - Error Mysql',FROM_SOPORTE,false,"Mysql Error",
			"<H3>Error MySQL </H3>Instruccion: $cadena<br>Error: $Error_de_mysql <br>Usuario: ".$_SESSION[USER]->Perfil."-".$_SESSION[USER]->Nick."<br>Script:".$_SERVER['SCRIPT_FILENAME']);
			die();
		}
	}
}

function test()
{
	//echo getcwd();
	$name = basename($_FILES['archivo']['name']);
	$name = str_replace("ñ","n",$name);
	$name = str_replace("Ñ","N",$name);
	//echo $name;
	$route = "archivos_cargue/".$name;	
	move_uploaded_file($_FILES['archivo']['tmp_name'], $route);
	
	$objPHPExcel = PHPExcel_IOFactory::load($route);

	$objPHPExcel->setActiveSheetIndex(0);

	$numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	
	

	for ($i = 1; $i <= $numRows; $i++) {
		$nit = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$sql = "SELECT * from aoacol_administra.proveedor where identificacion = '$nit' limit 1 ";
		$resultado = query($sql);
		echo $i." ";
		echo "NIT:";
		echo $nit." ";
		$fila = mysql_fetch_object($resultado);
		if($fila != null)
		{
			$sql = "UPDATE  aoacol_administra.proveedor SET activo = 0 , causal_inactivacion = 1 where identificacion = '$nit' ";
			$resultado = query($sql);
			echo "Proovedor inactivado";
		}
		else{
			echo "no existe";
		}
		echo "<br>";
		
	}
		
	echo "<br>";
	echo "Proceso finalizado";
	exit;	
}

function crit_proov()
{
	//echo getcwd();
	$name = basename($_FILES['archivo']['name']);
	$name = str_replace("ñ","n",$name);
	$name = str_replace("Ñ","N",$name);
	//echo $name;
	$route = "archivos_cargue/".$name;	
	move_uploaded_file($_FILES['archivo']['tmp_name'], $route);
	
	$objPHPExcel = PHPExcel_IOFactory::load($route);

	$objPHPExcel->setActiveSheetIndex(0);

	$numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	
	

	for ($i = 1; $i <= $numRows; $i++) {
		$nit = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$nivel_criticidad = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$tipo_gasto_proveedor = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$sql = "SELECT * from aoacol_administra.proveedor where identificacion = '$nit' limit 1 ";
		$resultado = query($sql);
		echo $i." ";
		echo "NIT:";
		echo $nit." ";
		$fila = mysql_fetch_object($resultado);
		if($fila != null)
		{
			$sql = "UPDATE  aoacol_administra.proveedor SET nivel_criticidad = '$nivel_criticidad' , tipo_gasto_proveedor = '$tipo_gasto_proveedor'  where identificacion = '$nit' ";
			$resultado = query($sql);
			echo "Proovedor Actualizado";
		}
		else{
			echo "no existe";
		}
		echo "<br>";
		
	}
		
	echo "<br>";
	echo "Proceso finalizado";
	exit;	
}



?>