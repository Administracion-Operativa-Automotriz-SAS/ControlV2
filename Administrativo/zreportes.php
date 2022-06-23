<?php

include_once('inc/funciones_.php');
sesion();

$Hoyl = date('Y-m-d H:i:s');
$Hoy = date('Y-m-d');

if (!empty($Acc) && function_exists($Acc)) { eval($Acc . '();'); die(); }


function caja_menor(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/caja_menor.html");
}
function ubicaciones(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/ubicaciones.html");
}

function caja_menor_administrativo(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/requicisiones_admin.html");
	
}
function reporte_administrativo_requicision(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/reporte-Administrativa.html");
	
}
function requicisiones_control_facturas(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/requiciones_control_facturas.html");
}
function extras_extencion(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/extras_extencion.html");
}
function reporte_pqr(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/reporte_pqr.html");
}
function consulta_siniestro_fecha_ingreso(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/consulta_siniestro_fecha_ingreso.html");
}
/*Primer informe*/
function informe_siniestro(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/informe_siniestro.html");
}
/*Segundo informe*/
function informe_facturacion(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/informe_facturacion.html");
}
/*Tercer informe*/
function informe_encuestas(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/informe_encuestas.html");
}
/*Cuarto informe*/
function informe_gestion_consultores(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/informe_gestion_consultores.html");
}

/*Reporte de cartera*/

function informe_cartera(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/informe_cartera.php");
}

function informe_detalle_lineas(){
	sesion();
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Administrativo/views/reports/informeDetalleLinea.html");
}

?>