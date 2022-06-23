<?php

/**
 *  Facturacion AOA
 *  Este modulo permite controlar la cartera de la empresa
 * @version $Id$
 * @copyright 2010
 */
include('inc/funciones_.php');
sesion();
$USUARIO = $_SESSION['User']; 
$Nusuario = $_SESSION['Nombre'];
$Nick = $_SESSION['Nick'];
$Hoyl = date('Y-m-d H:i:s');
$Hoy = date('Y-m-d');
$Hora = date('H:i:s');
$CONCILIADOR=0;
if($USUARIO==10 /* operario de oficina */) $OFIU=qo1("select oficina from usuario_oficina where id=".$_SESSION['Id_alterno']); // para directores de oficina obliga a filtrar solo lo de esa oficina
if($USUARIO==6 /* Facturacion */) $CONCILIADOR=qo1("select concilia_rc from usuario_facturacion where id=".$_SESSION['Id_alterno']);

if (!empty($Acc) && function_exists($Acc)){eval($Acc . '();');	die();}

cartera_inicial();

function cartera_inicial()
{
	global $USUARIO,$OFIU;
	if(inlist($USUARIO,'33'))
	{$OFIC=qo1("select oficina from usuario_tesoreria where id=".$_SESSION['Id_alterno']);}  // cuando el usuario es director de oficina trae la info de la oficina
	html('AOA - SISTEMA DE CARTERA'); // pinta las cabeceras html
	$FI=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-10)));
	$FF=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),15)));
	// pinta el formulario de consulta
	echo "<script language='javascript'>
		function carga() {	ajustar_tablero();}
		function ajustar_tablero(){document.getElementById('tablero_cartera').style.height=document.body.clientHeight-100;}
		function recargar(){document.forma.submit();}
		</script>
		<body onload='carga()'>
		<script language='javascript'>centrar();</script>
		<form action='zcartera.php' method='post' target='tablero_cartera' name='forma' id='forma'>
			Fecha Inicial: ".pinta_FC('forma','FI',$FI)." Fecha Final: ".pinta_FC('forma','FF',$FF)." Filtrar por:
			<select name='Filtropor'><option value='1'>Fecha Emision</option><option value='2'>Fecha Vencimiento</option></select>
			Oficina: ";
	// si el usuario es director de oficina solo muestra la oficina de el, de lo contrario muestra todas las oficinas
	if($OFIC) echo "<input type='hidden' name='OFICINA' value='$OFIC'><b>".qo1("select nombre from oficina where id=$OFIC")."</b> ";
	elseif($OFIU) echo "<input type='hidden' name='OFICINA' value='$OFIU'><b>".qo1("select nombre from oficina where id=$OFIU")."</b> ";
	else echo menu1('OFICINA',"Select id,nombre from oficina order by nombre",0,1);
	echo "Modo:<select name='Modo'><option value='1'>Todas</option><option value='2' disabled>Solo Recaudadas (en revision)</option><option value='3' disabled>Por Cobrar (en revision)</option><option value='4'>Por Conciliar</option></select>
			Agrupado por: <select name='Agrupado'><option value='1'>Ciudad</option><option value='2'>General - Causacion</option></select>
			Ver: <select name='Grupo'><option value='1'>Todas</option><option value='2'>Solo Aseguradoras</option><option value='3'>Solo Asegurados</option></select>
			<input type='submit' value=' APLICAR ' ><input type='hidden' name='Acc' value='consulta_cartera'>
			</form>
			<iframe id='tablero_cartera' name='tablero_cartera' width='100%'  height='500' frameborder='no' scrolling='auto' ></iframe></td>
			<iframe name='Oculto_cartera' id='Oculto_cartera' height=1 width=1></iframe>
			<script language='javascript'>ajustar_tablero();</script>
		</body>";
}

function consulta_cartera() // presenta el listado de facturas por oficina con su estado de pagos
{
	global $FI,$FF,$Hoy,$OFICINA,$Filtropor,$Modo,$Siniestro,$Agrupado,$USUARIO,$Grupo,$CONCILIADOR,$Nusuario;
	// crea un arreglo de bancos para usarlo mas adelante en la consulta
	$Bancos=q("select * from banco_aoa order by cuenta_contable");
	$ABancos=array();
	while($banco=mysql_fetch_object($Bancos)) {$ABancos[$banco->id]=$banco->nombre.' '.$banco->cuenta;}
	if($Agrupado==1) $Orden="nciudad,consecutivo"; else $Orden="fecha_emision,consecutivo";  // variacion del orden del query
	if($Siniestro) // si viene informacion de un siniestro especofico solo hace la consulta para ese siniestro
	{
		// construye la consulta teniendo en cuenta la tabla de historicos de siniestros.
		$Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,consecutivo,fecha_emision,fecha_vencimiento,total,t_siniestro(f.siniestro) as nsiniestro,
				t_cliente(cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones
				FROM factura f, siniestro s
				WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and s.id=$Siniestro
				UNION 
				select f.id,t_aseguradora(f.aseguradora) as naseguradora,consecutivo,fecha_emision,fecha_vencimiento,total,t_siniestro(f.siniestro) as nsiniestro,
				t_cliente(cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones
				FROM factura f, siniestro_hst s
				WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and s.id=$Siniestro
				ORDER by $Orden";

	}
	else
	{
		// construye los filtros, empezando por la fecha de emision o por fecha de vencimiento dependiendo de lo que haya selecionado el usuario en el formulario anterior
		if($Filtropor==1) $FFecha=" f.fecha_emision between '$FI' and '$FF' "; else $FFecha=" f.fecha_vencimiento between '$FI' and '$FF' ";
		//	si el usuario ha seleccionado solo una oficina o es de perfiles de oficina, solo muestra la informacion de la oficina
		if($OFICINA) $FCiudad=qo1("select ciudad from oficina where id=$OFICINA");
		//  dependiendo del grupo puede mostrar solo asegurados, solo aseguradoras o todos
		if($Grupo==2) // si solo solicita ver asegurados
		{
			// si se solicita ver todas las facturas
			if($Modo==1) $Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_cliente(f.cliente) as nsiniestro,
									t_cliente(f.cliente) as ncliente,'BOGOTA D.C. - BOGOTA' as nciudad,f.observaciones, ('') as sobs,('') as sobsc,f.siniestro as idsin,('') as numero
									FROM factura f
									WHERE f.siniestro=0 and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and '11001000'='$FCiudad' ":"")."
									ORDER BY $Orden";
			// si se solicita ver solo recaudadas
			elseif($Modo==2) $Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,('') as nsiniestro,
									t_cliente(f.cliente) as ncliente,'BOGOTA D.C. - BOGOTA' as nciudad,f.observaciones, ('') as sobs,('') as sobsc,f.siniestro as idsin,('') as numero
									FROM factura f
									WHERE f.siniestro=0 and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and '11001000'='$FCiudad' ":"")." and
									f.id in (select factura from recibo_caja where consignacion_numero!='')
									ORDER BY $Orden";
			// si se solicita ver por cobrar 
			elseif($Modo==3) $Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,('') as nsiniestro,
									t_cliente(f.cliente) as ncliente,'BOGOTA D.C. - BOGOTA' as nciudad,f.observaciones, ('') as sobs,('') as sobsc,f.siniestro as idsin,('') as numero
									FROM factura f
									WHERE f.siniestro=0 and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and '11001000'='$FCiudad' ":"")." and
									f.id not in (select factura from recibo_caja where consignacion_numero!='')
									ORDER BY $Orden";
			// si se solicita ver solo por conciliar contra bancos
			elseif($Modo==4) $Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,('') as nsiniestro,
									t_cliente(f.cliente) as ncliente,'BOGOTA D.C. - BOGOTA' as nciudad,f.observaciones, ('') as sobs,('') as sobsc,f.siniestro as idsin,('') as numero
									FROM factura f
									WHERE f.siniestro=0 and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and '11001000'='$FCiudad' ":"")."
									and f.id in (select distinct factura from recibo_caja where (conciliado=0 and anulado=0)  union 
											select distinct factura from nota_contable where conciliado=0 and anulado=0 union
											select distinct factura from nota_credito where conciliado=0 and anulado=0) 
									ORDER BY $Orden";
		}
		elseif($Grupo==3) // solo asegurados
		{
			// todas las facturas
			if($Modo==1) $Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")." $Fgrupo
									UNION
									select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro_hst s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")." $Fgrupo
									ORDER BY $Orden";
			// solo las recaudadas
			elseif($Modo==2) $Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")." and
									f.id in (select factura from recibo_caja where consignacion_numero!='')
									UNION 
									select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro_hst s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")." and
									f.id in (select factura from recibo_caja where consignacion_numero!='')
									ORDER BY $Orden";
			// solo las sin recaudo
			elseif($Modo==3) $Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")." and
									f.id not in (select factura from recibo_caja where consignacion_numero!='')
									UNION
									select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro_hst s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")." and
									f.id not in (select factura from recibo_caja where consignacion_numero!='')
									ORDER BY $Orden";
			// solo las por conciliar contra bancos
			elseif($Modo==4) $Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")."
									and f.id in (select distinct factura from recibo_caja where (conciliado=0 and anulado=0)  union 
											select distinct factura from nota_contable where conciliado=0 and anulado=0 union
											select distinct factura from nota_credito where conciliado=0 and anulado=0) 
									UNION
									select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro_hst s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")."
									and f.id in (select distinct factura from recibo_caja where (conciliado=0 and anulado=0)  union 
											select distinct factura from nota_contable where conciliado=0 and anulado=0 union
											select distinct factura from nota_credito where conciliado=0 and anulado=0) 
									ORDER BY $Orden";
		}
		else // ver aseguradoras y asegurados
		{
			// todas las facturas
			if($Modo==1) $Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")."
									UNION
									select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro_hst s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")."
									UNION select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_cliente(f.cliente) as nsiniestro,
									t_cliente(f.cliente) as ncliente,'BOGOTA D.C. - BOGOTA' as nciudad,f.observaciones, ('') as sobs,('') as sobsc,f.siniestro as idsin,('') as numero
									FROM factura f
									WHERE f.siniestro=0 and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and '11001000'='$FCiudad' ":"")."
									ORDER BY $Orden";
			// solo las recaudadas
			elseif($Modo==2) $Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")." and
									f.id in (select factura from recibo_caja where consignacion_numero!='')
									UNION
									select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro_hst s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")." and
									f.id in (select factura from recibo_caja where consignacion_numero!='')
									UNION select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,('') as nsiniestro,
									t_cliente(f.cliente) as ncliente,'BOGOTA D.C. - BOGOTA' as nciudad,f.observaciones, ('') as sobs,('') as sobsc,f.siniestro as idsin,('') as numero
									FROM factura f
									WHERE f.siniestro=0 and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and '11001000'='$FCiudad' ":"")." and
									f.id in (select factura from recibo_caja where consignacion_numero!='')
									ORDER BY $Orden";
			// solo las sin recaudo
			elseif($Modo==3) $Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")." and
									f.id not in (select factura from recibo_caja where consignacion_numero!='')
									UNION
									select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro_hst s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")." and
									f.id not in (select factura from recibo_caja where consignacion_numero!='')
									UNION select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,('') as nsiniestro,
									t_cliente(f.cliente) as ncliente,'BOGOTA D.C. - BOGOTA' as nciudad,f.observaciones, ('') as sobs,('') as sobsc,f.siniestro as idsin,('') as numero
									FROM factura f
									WHERE f.siniestro=0 and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and '11001000'='$FCiudad' ":"")." and
									f.id not in (select factura from recibo_caja where consignacion_numero!='')
									ORDER BY $Orden";
			// solo las por conciliar
			elseif($Modo==4) $Q="select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")."
									and f.id in (select distinct factura from recibo_caja where (conciliado=0 and anulado=0)  union 
											select distinct factura from nota_contable where conciliado=0 and anulado=0 union
											select distinct factura from nota_credito where conciliado=0 and anulado=0) 
									UNION
									select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,t_siniestro(f.siniestro) as nsiniestro,
									t_cliente(f.cliente) as ncliente,t_ciudad(s.ciudad) as nciudad,f.observaciones,s.observaciones as sobs,s.obsconclusion as sobsc,f.siniestro as idsin,s.numero
									FROM factura f,siniestro_hst s
									WHERE f.siniestro=s.id and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and s.ciudad ='$FCiudad' ":"")."
									and f.id in (select distinct factura from recibo_caja where (conciliado=0 and anulado=0)  union 
											select distinct factura from nota_contable where conciliado=0 and anulado=0 union
											select distinct factura from nota_credito where conciliado=0 and anulado=0) 
									UNION select f.id,t_aseguradora(f.aseguradora) as naseguradora,f.consecutivo,f.fecha_emision,f.fecha_vencimiento,f.total,('') as nsiniestro,
									t_cliente(f.cliente) as ncliente,'BOGOTA D.C. - BOGOTA' as nciudad,f.observaciones, ('') as sobs,('') as sobsc,f.siniestro as idsin,('') as numero
									FROM factura f
									WHERE f.siniestro=0 and f.anulada=0 and f.autorizadopor!='' and $FFecha ".($OFICINA?" and '11001000'='$FCiudad' ":"")."
									and f.id in (select distinct factura from recibo_caja where (conciliado=0 and anulado=0)  union 
											select distinct factura from nota_contable where conciliado=0 and anulado=0 union
											select distinct factura from nota_credito where conciliado=0 and anulado=0) 
									ORDER BY $Orden";
		}
	}

	$NT=tu('recibo_caja','id'); // obtiene el id del permiso de la tabla recibo de caja de acuerdo al perfil del usuario
	$NTnc=tu('nota_contable','id'); // obtiene el permiso de notas contables
	$NTncr=tu('nota_credito','id'); // obtiene el permiso de notas credito
	html(); // pinta las cabeceras html
	// pinta las herramientas en javascript
	echo "<script language='javascript'> 
			function conciliar(id)
			{if(confirm('Desea marcar esta consignacion como conciliada con el extracto bancario?')) modal('zcartera.php?Acc=conciliar&id='+id,0,0,10,10,'Oculto_cartera');}
			function desconciliar(id)
			{if(confirm('Desea des-conciliar esta conciliacion?')) modal('zcartera.php?Acc=desconciliar&id='+id,0,0,10,10,'Desconciliar');}
			
			function conciliar2(id)
			{ if(confirm('Desea marcar esta nota contable como conciliada con el extracto bancario?')) modal('zcartera.php?Acc=conciliar2&id='+id,0,0,10,10,'Oculto_cartera'); }
			function desconciliar2(id)
			{ if(confirm('Desea des-conciliar esta nota contable?')) modal('zcartera.php?Acc=desconciliar2&id='+id,0,0,10,10,'Desconciliar'); }
			
			function conciliar3(id)
			{ if(confirm('Desea marcar esta nota contable como conciliada con el extracto bancario?')) modal('zcartera.php?Acc=conciliar3&id='+id,0,0,10,10,'Oculto_cartera'); }
			function desconciliar3(id)
			{ if(confirm('Desea des-conciliar esta nota contable?')) modal('zcartera.php?Acc=desconciliar3&id='+id,0,0,10,10,'Desconciliar'); }
			
			function addobs(id) { modal('zcartera.php?Acc=addobs&id='+id,0,0,300,500,'AdObs'); }

			function nuevo_recibo_garantia(id)
			{modal('zcartera.php?Acc=nuevo_recibo_garantia&idcita='+id,0,0,600,600,'recg');	}
                        
                        function cobrar_garantia(id,u)
			{
                      
                        window.open('https://pot.aoacolombia.com/cobranza?f='+id+'&u='+u,'Cobros','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=600,height=500,left = 10,top = 70');
                        
                        }

			function consultar_custodia(id)
			{modal('zcontrol_custodia_garantia.php?Acc=consultar_garantias&IDsiniestro='+id,0,0,400,800,'garantia');}

			function valida_scroll()
			{ document.getElementById('_capa_titulo_superior_').style.left=-document.body.scrollLeft+8;
				var Altura=document.getElementById('_Tabla_').offsetTop;
				var Avance=document.body.scrollTop;
				if(Altura-Avance>0)	document.getElementById('_capa_titulo_superior_').style.top=Altura-Avance; else document.getElementById('_capa_titulo_superior_').style.top=0;
				if(document.getElementById('_capa_titulo_lateral_')) document.getElementById('_capa_titulo_lateral_').style.top=-document.body.scrollTop+Altura;}

			function fija_ancho(dato,descuento)
			{document.getElementById(dato+'_').width=document.getElementById(dato).clientWidth-descuento;}

			function asigna_banco(banco,id)
			{
				if(confirm('Desea asignar el banco a este recibo de caja?'))
				{
					window.open('zcartera.php?Acc=asignar_banco&id='+id+'&banco='+banco,'Oculto_cartera');
				}
			}
						
			function cambiacc(valor,id,consecutivo)
			{ // nueva funcion cambiac, en la que asigna el mismo consecutivo original del recibo de caja como consecutivo contable, ya no es necesario usar la variable ConsecutivoCC
				var Campo=document.getElementById('cc'+id);
				if(valor) 
				{ if(!Number(Campo.value))
					{	Campo.value=consecutivo;	Aplano[id]=consecutivo;	}
					else Aplano[id]=Campo.value;
				}
				else {Campo.value='0';Aplano[id]=0;} // elimina el consecutivo del arreglo de transferencia
			}
			
			var Aplano=new Array();
			
			function genera_planorc()
			{
				with(document.genplano)
				{
					recibos.value='';
					for(var i in Aplano) 
					{ 
						if(Aplano[i]!=0)
						{
							recibos.value+=i+','+Aplano[i]+';';
						}
					}
					submit();
				}
			}

			function genera_planorc_su()
			{
				with(document.genplano_su)
				{
					recibos.value='';
					for(var i in Aplano) 
					{ 
						if(Aplano[i]!=0)
						{
							recibos.value+=i+','+Aplano[i]+';';
						}
					}
					submit();
				}
			}
		</script>
		<body onscroll='valida_scroll()'><span id='mensajes' style='background-color:blue;color:yellow;font-weight:bold;'></span>
		<iframe name='Oculto_cartera' id='Oculto_cartera' height=1 width=1 style='visibility:hidden'></iframe>
		<script language='javascript'>document.getElementById('mensajes').innerHTML='Cargando consulta..';</script>";
		if($Consulta=q($Q)) // si la consulta es exitosa y retorna registros 
		{
			echo "<H3>AOA COLOMBIA S.A. - SISTEMA DE CONTROL DE CARTERA..</H3>";
			$Noficina='';$Total_oficina=0;$Total_general=0;$SALDO_OFICINA=0;$SALDO_GENERAL=0;
			// A_titulos es un arreglo para pintar los totulos de la consulta en una capa (layer) estatico y que se desplace el detalle por debajo.
			$A_titulos=array();
			$A_titulos['consecutivo']='Consecutivo';
			$A_titulos['emision']='Emision';
			$A_titulos['vencimiento']='Vencimiento';
			$A_titulos['siniestro']='Siniestro';
			$A_titulos['valor']='Valor';
			$A_titulos['ver1']='Ver';
			$A_titulos['pago']='Pago';
			$A_titulos['cheque']='Cheque';
			$A_titulos['t_credito']='T.Credito';
			$A_titulos['t_debito']='T.Dobito';
			$A_titulos['efectivo']='Efectivo';
			$A_titulos['ver2']='Ver';
			$A_titulos['rc']='R.C.';
			$A_titulos['consignacion']='Consignacion';
			$A_titulos['fecha_consig']='Fecha.Consign.';
			$A_titulos['abonado']='Abonado';
			$A_titulos['concilia']='Conciliacion';
			$A_titulos['contabilidad']='Contabilidad';
			$A_titulos['porcobrar']='Por Cobrar';
			$A_titulos['opciones']='Opciones';
			$A_titulos['contabilidad']='Contabilidad';

			fija_titulo_superior($A_titulos,"border style='empty-cells:show' cellspacing='0' width='120%' ",1); // fija los titulos
			fija_titulo_superior($A_titulos,"border style='empty-cells:show' cellspacing='0' width='120%' ",2); // fija los titulos por segunda vez

			require('inc/link.php'); // conexion con la base de datos
			while($C=mysql_fetch_object($Consulta))
			{
				$Consec=str_pad($C->consecutivo,6,'0',STR_PAD_LEFT); // enmascara el consecutivo
				$Valor=coma_format($C->total); // enmascara el valor 
				if($Agrupado==1) // pinta el rompiniento de la consulta totalizando por oficina
				{
					if($Noficina!=$C->nciudad)
					{
						if($Total_oficina!=0)
						{
							echo "<tr><td colspan=4 bgcolor='ffffdd'><b style='font-size:12'>Total $Noficina</b></td><td align='right' bgcolor='ffffdd'><b>".coma_format($Total_oficina)."</b></td>
										<td colspan='13' bgcolor='ffffdd' ></td><td bgcolor='ffffdd' align='right'><b>".coma_format($SALDO_OFICINA)."</b></td></tr>";
							$Total_general+=$Total_oficina;$Total_oficina=0;$SALDO_GENERAL+=$SALDO_OFICINA;
						}
						$Noficina=$C->nciudad;$SALDO_OFICINA=0;
						echo "<tr><td colspan=20 bgcolor='ffffcc'><b style='font-size:16'>$Noficina</b></td></tr>";
					}
				}
				// pinta el detalle del registro
				echo "<tr><td align='center'>$Consec</td><td align='center' nowrap='yes'>$C->fecha_emision</td><td align='center' nowrap='yes'>$C->fecha_vencimiento</td>
							<td nowrap='yes'>$C->ncliente</td><td align='right'>$Valor</td>
							<td nowrap='yes'><a class='info' style='cursor:pointer' onclick=\"modal('zfacturacion.php?Acc=imprimir_factura&id=$C->id',0,0,800,700,'fac');\">
							<img src='gifs/standar/Preview.png' border='0'><span>Ver Factura</span></a> ";
				if($C->observaciones)
					echo "<a class='info' style='cursor:pointer' onclick='addobs($C->id);'><img src='gifs/standar/noticias.png' border='0'><span>
								<table width='600px'><tr ><th >Observaciones de la Factura (click para adicionar)</th></tr><tr ><td >".nl2br($C->observaciones)."</td></tr></table></span></a>";
				else
					echo "<a class='info' style='cursor:pointer' onclick='addobs($C->id);'><img src='gifs/mas.gif' border='0'><span>Adicionar Observaciones</span></a>";
				if($C->sobs) echo " <a class='info' style='cursor:pointer' onclick='consultar_custodia($C->idsin);'><img src='gifs/standar/folder1.png' border='0'><span>
								<table width='700px'><tr ><th >Observaciones del Siniestro $C->numero </th></tr><tr ><td >".nl2br($C->sobs)."</td></tr></table></span></a>";
				if($C->sobsc) echo " <a class='info' style='cursor:pointer'  onclick='consultar_custodia($C->idsin);'><img src='gifs/standar/folder2.png' border='0'><span>
								<table width='700px'><tr ><th >Observaciones de Conclusion del Siniestro $C->numero</th></tr><tr ><td >".nl2br($C->sobsc)."</td></tr></table></span></a>";

				echo "</td>";
				$SALDO=$C->total; // calcula el saldo por recaudar
				$Total_pagado=0;$Pagado_notas_co=0;$Pagado_notas_cr=0;$salida=false;
				if($Recibos=mysql_query("select * from recibo_caja where factura=$C->id",$LINK)) // busca los recibos de caja de la factura
				{
					if(mysql_num_rows($Recibos))
					{
						while($R=mysql_fetch_object($Recibos)) // pinta recibo por recibo
						{
							if($salida) echo "<tr><td colspan=6>";
							if($R->autorizacion)
								$Autorizacion=qom("select *,t_franquisia_tarjeta(franquicia) as nfranquicia ,t_codigo_ach(banco) as nbanco from sin_autor where id=$R->autorizacion",$LINK); //verifica si el recibo tiene una autorizacion asociada
							else $Autorizacion=false;
							if($R->anulado) $R->tarjeta_debito_valor=$R->efectivo=$R->tarjeta_credito=$R->cheque=$R->valor=$R->total_abonado=0;
							// pinta el detalle del recibo de caja
							echo "<td align='center' style='color:dd4444' nowrap='yes'>".($R->anulado?"<a class='info'><strike style='color:dd4444'>":"")."<B alt='Recibo de Caja' title='Recibo de Caja'>RC ".str_pad($R->consecutivo, 6,'0',STR_PAD_LEFT)."<b>".
											($R->anulado?"</strike><span><table width='200px'><tr ><td>$R->motivo_anulacion</td></tr></table></span></a>":"")."</td>
										<td align='right'><a class='info'>".coma_format($R->cheque)."<span><table><tr><td nowrap='yes'>$R->banco<br>Nomero cheque: $R->numero_cheque</td></tr></table></span></a></td>
										<td align='right'><a class='info'>".coma_format($R->tarjeta_credito)."<span><table><tr><td nowrap='yes'>Auorizacion: ***".r($Autorizacion->num_autorizacion,3)."<br>".
										"Banco: $Autorizacion->nbanco Franqicia: $Autorizacion->nfranquicia</td></tr></table></span></a></td>".
										"<td align='right'>".coma_format($R->tarjeta_debito_valor)."</td>".
										"<td align='right'>".coma_format($R->efectivo)."</td>".
										"<td align='center'><a class='info' style='cursor:pointer' onclick=\"modal('zcartera.php?Acc=imprimir_recibo&id=$R->id',0,0,700,900,'fac');\">
										<img src='gifs/standar/Preview.png' border='0'><span style='width:100px'>Ver Recibo de Caja</span></a></td>".
										"<td align='center'>";
							if(!$R->anulado) $Total_pagado+=$R->valor;
							if($NT) // dependiendo del permiso que tenga le da la opcion al usuario de modificar el recibo de caja
								echo "<a class='info' style='cursor:pointer' onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NT&id=$R->id',0,0,700,900,'rc');\"><img src='gifs/standar/Pencil.png' border='0'><span style='width:100px'>Modificar Recibo de Caja</span></a>";
							// pinta las imogenes de la consignacion o consignaciones
							echo "</td><td align='right' nowrap='yes'>$R->consignacion_numero ".($R->consignacion_f?"<a class='info' style='cursor:pointer' onclick=\"modal('$R->consignacion_f',0,0,800,800,'vp');\"><img src='gifs/standar/Preview.png' border='0'><span style='width:100px;'>Ver Consignacion</span></a>":"")
										.($R->consignacion_numero2?"<br>$R->consignacion_numero2 ".($R->consignacion2_f?"<a class='info' style='cursor:pointer' onclick=\"modal('$R->consignacion2_f',0,0,800,800,'vp');\"><img src='gifs/standar/Preview.png' border='0'><span style='width:100px;'>Ver Consignacion</span></a>":""):"")."</td>
										<td align='center' nowrap='yes'>$R->consignacion_fecha ".($R->consignacion_numero2?"<br>$R->consignacion_fecha2":"")."</td>
										";
							echo "<td align='right'>".coma_format($R->total_abonado)."</td>";
							if($USUARIO==6 && $CONCILIADOR) // si el perfil es de facturacion, y si el usuario es conciliador permite marcar la conciliacion bancaria
							{
								if($R->conciliado) 
									echo "<td bgcolor='ffffff' id='td_$R->id' align='center' onmouseover=\"muestra('c_$R->id');\" onmouseout=\"oculta('c_$R->id');\"><a class='info' id='c_$R->id' style='cursor:pointer;visibility:hidden' onclick='desconciliar($R->id)'><img src='gifs/x.gif' border='0'><span>Des-Conciliar</span></a><img src='gifs/standar/si.png' border='0'>";
								else
									echo "<td bgcolor='ffffaa' id='td_$R->id' align='center' onmouseover=\"muestra('c_$R->id');\" onmouseout=\"oculta('c_$R->id');\"><a class='info' id='c_$R->id' style='cursor:pointer;visibility:hidden;'  onclick='conciliar($R->id)'>Conciliar<span>Conciliar</span></a>";
							} 
							else // de lo contrario solo muestra si esto o no conciliado
							{
								if($R->conciliado) echo "<td bgcolor='ffffff' id='td_$R->id' align='center' nowrap='yes'><img src='gifs/standar/si.png' border='0'>";
								else echo "<td bgcolor='ffffaa' id='td_$R->id' align='center' >";
							}
							// pinta los bancos para la eleccion de la conciliacion
							echo "<select name='banco_$R->id' id='banco_$R->id' style='width:90px;font-size:9px;' ";
							if(($USUARIO==6 && $CONCILIADOR) || $USUARIO==9)  {if($R->banco_aoa) echo "disabled";} // si ya selecciono un banco, se bloquea para el usuario conciliador. 
							elseif($USUARIO!=1) echo "disabled"; // si el usuario no es conciliador lo bloquea de todas formas
							echo " onchange='asigna_banco(this.value,$R->id);'><option value=''></option>"; // cuando cambia de banco lo asigna al recibo de caja en la conciliacion
							foreach($ABancos as $idb => $nbanco) // pinta los bancos
							{echo "<option value='$idb' ".($idb==$R->banco_aoa?"selected":"").">$nbanco</option>";}
							echo "</select></td><td bgcolor='ffffff' nowrap='yes'>";
							/// OPCIONES PARA EXPORTACION DE DOCUMENTOS PARA CONTABILIDAD
							if(inlist($USUARIO,'1,9') && $R->banco_aoa && $R->conciliado)
							{
								//	mediante una caja de chequeo se pueden marcar los recibos de caja que se desee exportar a contabilidad, y se puede asignar un consecutivo contable para la exportacion
								echo "<input type='checkbox' onchange='cambiacc(this.checked,$R->id,$R->consecutivo);'>
											<input class='numero' type='text' name='cc$R->id' id='cc$R->id' size=3 style='background-color:".($R->consec_contable?"ddffdd":"ffffaa")."'  value='$R->consec_contable'' readonly>"; 
							}
							echo "</td>";
							$salida=true;
						}
					}
				}

				if($Notasco=mysql_query("select * from nota_contable where factura=$C->id ",$LINK)) // busca la existencia de notas contables
				{
					if(mysql_num_rows($Notasco))
					{
						while($Nc=mysql_fetch_object($Notasco)) // pinta nota por nota 
						{
							if($salida) echo "<tr><td colspan=6>";
							echo "<td align='center' style='color:5B1F22' nowrap='yes'>".($Nc->anulado?"<a class='info'><strike style='color:dd4444'>":"")."<B title='NOTA CONTABLE' alt='NOTA CONTABLE'>NCO ".str_pad($Nc->consecutivo, 6,'0',STR_PAD_LEFT)."</B>".
											($Nc->anulado?"</strike><span><table width='200px'><tr ><td>$Nc->observaciones</td></tr></table></span></a>":"")."</td>
										<td align='right'><a class='info'></td><td align='right'><a class='info'></td><td align='right'></td><td align='right'></td>
										<td align='center'><a class='info' style='cursor:pointer' onclick=\"modal('zcartera.php?Acc=imprimir_ncontable&id=$Nc->id',0,0,700,900,'fac');\">
										<img src='gifs/standar/Preview.png' border='0'><span style='width:100px'>Ver Nota Contable</span></a>
										</td><td align='center'>";
							if($NTnc) // si tiene permiso de acuerdo al perfil, deja modificar la nota contable
								echo "<a class='info' style='cursor:pointer' onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTnc&id=$Nc->id',0,0,700,900,'rc');\"><img src='gifs/standar/Pencil.png' border='0'><span style='width:100px'>Modificar Nota Contable</span></a>";
							echo "</td><td align='right'></td><td align='center' nowrap='yes'>$Nc->fecha </td>";
							echo "<td align='right'>".coma_format($Nc->valor)."</td>";
							if(!$Nc->anulado) $Pagado_notas_co+=$Nc->valor;
							if($USUARIO==6) // si el usuario es de facturacion muestra la conciliacion de la nota contable.
							{
								if($Nc->conciliado) echo "<td bgcolor='ffffff' id='td_$Nc->id' align='center' onmouseover=\"muestra('c_$Nc->id');\" onmouseout=\"oculta('c_$Nc->id');\">
													<a class='info' id='c_$Nc->id' style='cursor:pointer;visibility:hidden' onclick='desconciliar2($Nc->id)'><img src='gifs/x.gif' border='0'>
													<span>Des-Conciliar</span></a><img src='gifs/standar/si.png' border='0'>";
								else echo "<td bgcolor='ffffaa' id='td_$Nc->id' align='center' onmouseover=\"muestra('c_$Nc->id');\" onmouseout=\"oculta('c_$Nc->id');\">
											<a class='info' id='c_$Nc->id' style='cursor:pointer;visibility:hidden;'  onclick='conciliar2($Nc->id)'>Conciliar<span>Conciliar</span></a>";
							}
							else
							{ // de lo contrario solo muestra si esto conciliado o no.
								if($Nc->conciliado)
									echo "<td bgcolor='ffffff' id='td_$Nc->id' align='center' ><a class='info' id='c_$Nc->id' style='cursor:pointer;visibility:hidden' ><img src='gifs/x.gif' border='0'><span>Des-Conciliar</span></a><img src='gifs/standar/si.png' border='0'>";
								else
									echo "<td bgcolor='ffffaa' id='td_$Nc->id' align='center' ><a class='info' id='c_$Nc->id' style='cursor:pointer;visibility:hidden;' >Conciliar<span>Conciliar</span></a>";
							}
							echo "</td><td bgcolor='ffffff'></td>";
							$salida=true;
						}
					}
					$Total_pagado+=$Pagado_notas_co;
				}
				
				if($Notascr=mysql_query("select * from nota_credito where factura=$C->id ",$LINK)) // busca la existencia de notas credito
				{
					
					if(mysql_num_rows($Notascr))
					{
						//AJAX FOR FACT ELECTRoNICA WITH NC
						echo '<script  src="https://code.jquery.com/jquery-2.2.4.min.js"  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>';
							echo "<script>
								function aprobar_nota_credito(idnc)
								{
									if(confirm('oEsta seguro de realizar la aprobacion?, !este proceso no podra deshacerseo'))
									{
										$.post('zcartera.php',{Acc:'Aprobar_nota_credito',id_nota_credito:idnc}).then(function(response){
											data = JSON.parse(response);
											console.log(data);	
											alert(data.MESSAGE);
											
										});
									}
								}
								
								function verificar_nota_credito_electronica(idnc)
								{
									window.open('zcartera.php?Acc=verificar_nota_credito_electronica&id='+idnc);
								}
							</script>";
						//------------------------------
							
						while($Nc=mysql_fetch_object($Notascr)) //  pinta nota por nota 
						{
							if($salida) echo "<tr><td colspan=6>";
							echo "<td align='center' style='color:5B115F' nowrap='yes'>".($Nc->anulado?"<a class='info'><strike style='color:dd4444'>":"")."<B title='NOTA CREDITO' alt='NOTA CREDITO'>NCR ".str_pad($Nc->consecutivo, 6,'0',STR_PAD_LEFT)."</B>".
											($Nc->anulado?"</strike><span><table width='200px'><tr ><td>$Nc->obsanulacion</td></tr></table></span></a>":"")."</td>
										<td align='right'><a class='info'></td><td align='right'><a class='info'></td><td align='right'></td><td align='right'></td>
										<td align='center'><a class='info' style='cursor:pointer' onclick=\"modal('zcartera.php?Acc=imprimir_ncredito&id=$Nc->id',0,0,700,900,'fac');\">
										<img src='gifs/standar/Preview.png' border='0'><span style='width:100px'>Ver Nota Credito</span></a></td><td align='center'>";
							if($NTncr) // de acuerdo al permiso del perfil deja modificar la nota credito
								echo "<a class='info' style='cursor:pointer' onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTncr&id=$Nc->id',0,0,700,900,'rc');\"><img src='gifs/standar/Pencil.png' border='0'><span style='width:100px'>Modificar Nota Credito</span></a>";									
							
							//AJAX FOR FACT ELECTRoNICA WITH NC
								echo "<a class='info' style='cursor:pointer' onclick=\"aprobar_nota_credito(".$Nc->id.");\"><img style='width:20px;' src='gifs/standar/chulo.png' border='0'><span style='width:100px'>Aprobar Nota Credito</span></a>";	
							//------------------------------
							
							if($Nc->aprobada_por != null)
							{
								//AJAX FOR get NC  dian data
									echo "<a class='info' style='cursor:pointer' onclick=\"verificar_nota_credito_electronica(".$Nc->id.");\"><img style='width:75%;' src='gifs/documenta_blanco.png' border='0'><span style='width:100px'>Informacion nota credito electronica</span></a>";	
								//------------------------------
							}
							
							echo "</td><td align='right'></td><td align='center' nowrap='yes'>$Nc->fecha </td>";
							echo "<td align='right'>".coma_format($Nc->total)."</td>";
							if(!$Nc->anulado) $Pagado_notas_cr+=$Nc->total;
							if($USUARIO==6) // si el perfil es de facturacion presenta la opcion de conciliacion 
							{
								if($Nc->conciliado) echo "<td bgcolor='ffffff' id='td_$Nc->id' align='center' onmouseover=\"muestra('c_$Nc->id');\" onmouseout=\"oculta('c_$Nc->id');\">
												<a class='info' id='c_$Nc->id' style='cursor:pointer;visibility:hidden' onclick='desconciliar3($Nc->id)'><img src='gifs/x.gif' border='0'>
												<span>Des-Conciliar</span></a><img src='gifs/standar/si.png' border='0'>";
								else echo "<td bgcolor='ffffaa' id='td_$Nc->id' align='center' onmouseover=\"muestra('c_$Nc->id');\" onmouseout=\"oculta('c_$Nc->id');\">
											<a class='info' id='c_$Nc->id' style='cursor:pointer;visibility:hidden;'  onclick='conciliar3($Nc->id)'>Conciliar<span>Conciliar</span></a>";
							}
							else // de lo contrario solo muestra si esta o no conciliado
							{
								if($Nc->conciliado) echo "<td bgcolor='ffffff' id='td_$Nc->id' align='center' ><a class='info' id='c_$Nc->id' style='cursor:pointer;visibility:hidden' ><img src='gifs/x.gif' border='0'><span>Des-Conciliar</span></a><img src='gifs/standar/si.png' border='0'>";
								else echo "<td bgcolor='ffffaa' id='td_$Nc->id' align='center' ><a class='info' id='c_$Nc->id' style='cursor:pointer;visibility:hidden;' >Conciliar<span>Conciliar</span></a>";
							}
							echo "</td><td bgcolor='ffffff'></td>";
							$salida=true;
						}
					}
					$Total_pagado+=$Pagado_notas_cr; // acumula los pagos
				}
				
				$SALDO-=$Total_pagado; // halla el saldo de la factura
				if(!$salida) {	echo "<td colspan=12 bgcolor='dddddd'></td>";}
				echo "<td align='right' ".($SALDO<0?"bgcolor='ffdddd' ":"").">".coma_format($SALDO)."</td>"; // pinta el saldo
				if($Total_pagado<$C->total)
				{ // si aun queda saldo, de acuerdo a los perfiles, deja elaborar los recibos de caja, note credito o nota contable. '1,5,6,10'
					echo "<td align='center' nowrap='yes'>";
					if(inlist($USUARIO,'1,5,6,10,34'))
					{
                                            
						echo "&nbsp;<a class='rinfo' style='cursor:pointer;' onclick=\"modal('zcartera.php?Acc=elaborar_recibo&idFac=$C->id',0,0,500,800,'recibos');\"><img src='gifs/standar/nuevo_registro.png' border='0'><span style='width:80px'>Elaborar Recibo</span></a>";
						if(inlist($USUARIO,'1,6,34'))
						{	
                                                    
                                            
                                                    $codificado = base64_encode($Consec.'310826#');
                                                    $namecodificado = base64_encode($Nusuario);
                                                    $f = '"'.$codificado.'"';
                                                    $u = '"'.$namecodificado.'"';
                                                    $AutoR=mysql_query("select contrato from sin_autor where estado='A' and siniestro=$C->idsin ",$LINK); // busca la existencia de notas credito
                                                    
                                                        $b = mysql_fetch_object($AutoR);
                                                        
                                                        if($b->contrato!=''){
                                                            $btnnn="&nbsp;<a href=\"javascript:void(null);\" onclick='cobrar_garantia($f,$u)' class='rinfo'><img src='img/gnucash.png' height='16px' border='0' align='top'><span style='width:120px'>Cobrar Garantia</span></a>";
                                                        }else{
                                                            $btnnn="";
                                                        }
                                                         //$btnnn="&nbsp;<a href=\"javascript:void(null);\" onclick='cobrar_garantia($f,$u)' class='rinfo'><img src='img/gnucash.png' height='16px' border='0' align='top'><span style='width:120px'>Cobrar Garantia</span></a>";
                                                    
                                                    if(inlist($USUARIO,'1,34'))
						     {
                                                        echo "&nbsp;<a class='rinfo' style='cursor:pointer;' onclick=\"modal('zcartera.php?Acc=elaborar_nota_credito&idFac=$C->id',0,0,500,800,'notacredito');\"><img src='gifs/standar/dsn_config.png' border='0'><span style='width:110px'>Elaborar Nota Credito</span></a>"
                                                                . "";
                                                    }
							echo $btnnn;
						}
						
						if($C->idsin) // BUSCA SI TIENE GARANTIA PARA CRUZAR CON LA FACTURA. $Nusuario
						{
                                                    $codificado = base64_encode($Consec.'310826#');
                                                    $namecodificado = base64_encode($Nusuario);
                                                    $f = '"'.$codificado.'"';
                                                    $u = '"'.$namecodificado.'"';
							if($Cita=qo1m("select id from cita_servicio where siniestro=$C->idsin and estado='C' ",$LINK ))
                                                                 if(inlist($USUARIO,'1,34'))
						     {
                                                                    echo "&nbsp;<a href=\"javascript:nuevo_recibo_garantia($Cita);void(null);\" class='rinfo'><img src='img/caja_registradora.png' height='16px' border='0' align='top'><span style='width:120px'>Recibo Caja x Garantia</span></a>"
                                                                . ""; 
                                                                 }
								
						}
					}
					
					echo "</td></tr>";
				}
				
				/*
				else
				{					
					echo "<td align='center' nowrap='yes'>";
					echo "&nbsp;<a class='rinfo' style='cursor:pointer;' onclick=\"aprobar_nota_credito()\"><img src='gifs/standar/chulo.png' border='0'><span style='width:80px'>Aprobar nota<br> credito</span></a>";;
					echo "</td></tr>";
				}
				
				*/
				
				
				echo "</tr>";
				$Total_oficina+=$C->total;
				$SALDO_OFICINA+=$SALDO;
			}
			mysql_close($LINK); // finaliza la presentacion del detalle y cierra la conexion con la base de datos
			// presenta totales de la ultima oficina
			echo "<tr><td colspan=4 bgcolor='ffffdd'><b style='font-size:12'>Total $Noficina</b></td><td align='right' bgcolor='ffffdd'><b>".coma_format($Total_oficina)."</b></td>
						<td colspan='13' bgcolor='ffffdd' ></td><td bgcolor='ffffdd' align='right'><b>".coma_format($SALDO_OFICINA)."</b></td></tr>";
			$Total_general+=$Total_oficina;$SALDO_GENERAL+=$SALDO_OFICINA;
			// presenta totales finales
			echo "<tr><td colspan=4 bgcolor='ffffdd'><b style='font-size:12'>Total GENERAL</b></td><td align='right' bgcolor='ffffdd'><b>".coma_format($Total_general)."</b></td>
						<td colspan='13' bgcolor='ffffdd' ></td><td bgcolor='ffffdd' align='right'><b>".coma_format($SALDO_GENERAL)."</b></td></tr>";
			echo "</table>";
			fija_titulo_superior($A_titulos,"",3); // ajusta la capa de los titulos estotica
			// pinta un formulario para la exportacion de los recibos de caja a contabilidad
			echo "<form action='zgenerador_contable.php' target='Oculto_cartera' method='POST' name='genplano' id='genplano'>
						<input type='hidden' name='Acc' value='exporta_rc_helisa'>
						<input type='hidden' name='recibos' >
					</form>
					<form action='zgenerador_contable.suno.php' target='Oculto_cartera' method='POST' name='genplano_su' id='genplano_su'>
						<input type='hidden' name='Acc' value='exporta_rc_uno'>
						<input type='hidden' name='recibos' >
					</form>
			<br>";
			if(inlist($USUARIO,'1,9')) 
			{
				echo "<a style='cursor:pointer' onclick='genera_planorc();'>Exportar Recibos de Caja a Contabilidad -> HELISA</a><br><br>
					<a style='cursor:pointer' onclick='genera_planorc_su();'>Exportar Recibos de Caja a Contabilidad -> SISTEMA UNO</a>";
			}
			echo "<br><br><br><br>";
		}
		else
		{
			echo "<b>NO HAY INFORMACION para el filtro solicitado.</b>";
		}
		echo "<script language='javascript'>document.getElementById('mensajes').innerHTML='';</script>";
		echo "</body>";
}

function Aprobar_nota_credito()
{
	global $id_nota_credito;
	
	$nota_credito = qo("select * from nota_credito where id = $id_nota_credito");
	
	
	$factura_seg = qo("select * from fact_electronica_seguimiento where factura = ".$nota_credito->factura." and estado = 1 order by id desc LIMIT 1 ");		
	
	
	if($factura_seg == null)
	{
		echo json_encode(array("STATUS"=>"ERROR","MESSAGE"=>utf8_encode("La factura asociada a la nota credito (id ".$nota_credito->factura.") aun no tiene una factura electronica")));			
		exit;	
	}
	
	require($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/factura_electronica.php");
	
	$special_invoices = array(43716,43717,43718,43719,43720);	
	
	$fact_elec = new factura_electronica(null);			
	$fact_elec->set_nota_credito($nota_credito);	
	
	$result = $fact_elec->generar_nota_credito();
	
	if(is_array($result))
	{
		$extra = $result["desc"];
	}
	else{
		$extra = "";
	}	
	
	if($nota_credito->aprobada_por == null)
	{	
		if(q("update nota_credito set aprobada_por = '".$_SESSION["Nombre"]."' where id = $id_nota_credito"))
		{
			echo json_encode(array("STATUS"=>"OK","MESSAGE"=>'Nota credito aprobada por '.$_SESSION["Nombre"]." ".$extra));			
		}
		else
		{
			echo json_encode(array("STATUS"=>"ERROR","MESSAGE"=>'Error'." ".$extra));			
		}
	}
	else
	{
		echo json_encode(array("STATUS"=>"OK","MESSAGE"=>'La nota credito ya fue aprobada'." ".$extra));
	}
	//echo json_encode(array("STATUS"=>"OK"));
	exit;
}

function asignar_banco() // asigna el banco en la conciliacion a un recibo de caja especofico
{
	global $id,$banco,$Nusuario;
	q("update recibo_caja set banco_aoa=$banco where id=$id");
	graba_bitacora('recibo_caja','M',$id,'Asigna banco');
	//echo "<body><script language='javascript'>parent.parent.recargar();</script></body>";
}

function conciliar() // marca el estado de conciliacion a un recibo de caja
{
	global $id,$Hoyl,$Nusuario;
	q("update recibo_caja set conciliado=1,observaciones=concat(observaciones,\"\n$Nusuario [$Hoyl] Concilia\")  where id=$id");
	graba_bitacora('recibo_caja','M',$id,'Concilia');
	echo "<body onload='carga()'><script language='javascript'>parent.document.getElementById('td_$id').innerHTML=\"<img src='gifs/standar/si.png' border='0'>\";window.close();void(null);</script></body>";
}

function desconciliar() // formulario para quitar la marca de estado de conciliacion a un recibo de caja controladamente pidiendo justificacion
{
	global $id;
	html('DES-CONCILIAR');
	echo "<script language='javascript'>
		function validae()
		{
			with(document.forma)
			{
				if(!alltrim(observaciones.value))
				{
					alert('Debe digitar el motivo por el cual desea des-conciliar este registro');
					observaciones.style.backgroundColor='ffffdd';observaciones.focus();return false;
				}
				submit();
			}
		}
		function quita_marca()
		{
			opener.document.getElementById('td_$id').innerHTML='Recargar para conciliar';
			window.close();void(null);
		}
		</script>
		<body><script language='javascript'>centrar(400,250);</script>
		<form action='zcartera.php' method='post' target='Oculto_desconciliar' name='forma' id='forma'>
			<center><b>Digite el motivo por el cual se necesita des-conciliar este registro:</b><br />
			<textarea name='observaciones' style='font-size:12px' cols=50 rows=4></textarea><br />
			<br /><input type='button' value='Continuar' onclick='validae()'>
			<input type='hidden' name='Acc' value='desconciliar_ok'>
			<input type='hidden' name='id' value='$id'></center>
		</form>
		<iframe name='Oculto_desconciliar' height=1 width=1 style='visibility:hidden'></iframe>
		</body>";
}

function desconciliar_ok() // quita la marca de conciliado controladamente a un recibo de caja
{
	global $id,$Hoyl,$Nusuario,$observaciones;
	q("update recibo_caja set conciliado=0,observaciones=concat(observaciones,\"\n$Nusuario [$Hoyl] No concilia motivo: $observaciones\")  where id=$id");
	graba_bitacora('recibo_caja','M',$id,'Des-Concilia');
	echo "<body onload='carga()'><script language='javascript'>alert('Proceso Satisfactorio');parent.quita_marca();</script></body>";
}

function conciliar2() // pone la marca de conciliacion a una nota contable
{
	global $id,$Hoyl,$Nusuario;
	q("update nota_contable set conciliado=1,observaciones=concat(observaciones,\"\n$Nusuario [$Hoyl] Concilia\")  where id=$id");
	graba_bitacora('nota_contable','M',$id,'Concilia');
	echo "<body onload='carga()'><script language='javascript'>parent.document.getElementById('td_$id').innerHTML=\"<img src='gifs/standar/si.png' border='0'>\";window.close();void(null);</script></body>";
}

function desconciliar2() // formulario para quitar la marca de conciliacion de nota contable solicitando justificacion
{
	global $id;
	html('DES-CONCILIAR');
	echo "<script language='javascript'>
		function validae()
		{
			with(document.forma)
			{
				if(!alltrim(observaciones.value))
				{
					alert('Debe digitar el motivo por el cual desea des-conciliar este registro');
					observaciones.style.backgroundColor='ffffdd';observaciones.focus();return false;
				}
				submit();
			}
		}
		function quita_marca()
		{
			opener.document.getElementById('td_$id').innerHTML='Recargar para conciliar';
			window.close();void(null);
		}
		</script>
		<body><script language='javascript'>centrar(400,250);</script>
		<form action='zcartera.php' method='post' target='Oculto_desconciliar' name='forma' id='forma'>
			<center><b>Digite el motivo por el cual se necesita des-conciliar este registro:</b><br />
			<textarea name='observaciones' style='font-size:12px' cols=50 rows=4></textarea><br />
			<br /><input type='button' value='Continuar' onclick='validae()'>
			<input type='hidden' name='Acc' value='desconciliar2_ok'>
			<input type='hidden' name='id' value='$id'></center>
		</form>
		<iframe name='Oculto_desconciliar' height=1 width=1 style='visibility:hidden'></iframe>
		</body>";
}

function desconciliar2_ok() // quita la marca de conciliacion a una nota contable
{
	global $id,$Hoyl,$Nusuario,$observaciones;
	q("update nota_contable set conciliado=0,observaciones=concat(observaciones,\"\n$Nusuario [$Hoyl] No concilia motivo: $observaciones\")  where id=$id");
	graba_bitacora('nota_contable','M',$id,'Des-Concilia');
	echo "<body onload='carga()'><script language='javascript'>alert('Proceso Satisfactorio');parent.quita_marca();</script></body>";
}

function conciliar3() // pone marca de conciliacion a una nota credito
{
	global $id,$Hoyl,$Nusuario;
	q("update nota_credito set conciliado=1,observaciones=concat(observaciones,\"\n$Nusuario [$Hoyl] Concilia\")  where id=$id");
	graba_bitacora('nota_credito','M',$id,'Concilia');
	echo "<body onload='carga()'><script language='javascript'>parent.document.getElementById('td_$id').innerHTML=\"<img src='gifs/standar/si.png' border='0'>\";window.close();void(null);</script></body>";
}

function desconciliar3() // formulario para quitar la marca de una conciliacion de nota crodito solicitando justificacion.
{
	global $id;
	html('DES-CONCILIAR');
	echo "<script language='javascript'>
		function validae()
		{
			with(document.forma)
			{
				if(!alltrim(observaciones.value))
				{
					alert('Debe digitar el motivo por el cual desea des-conciliar este registro');
					observaciones.style.backgroundColor='ffffdd';observaciones.focus();return false;
				}
				submit();
			}
		}
		function quita_marca()
		{
			opener.document.getElementById('td_$id').innerHTML='Recargar para conciliar';
			window.close();void(null);
		}
		</script>
		<body><script language='javascript'>centrar(400,250);</script>
		<form action='zcartera.php' method='post' target='Oculto_desconciliar' name='forma' id='forma'>
			<center><b>Digite el motivo por el cual se necesita des-conciliar este registro:</b><br />
			<textarea name='observaciones' style='font-size:12px' cols=50 rows=4></textarea><br />
			<br /><input type='button' value='Continuar' onclick='validae()'>
			<input type='hidden' name='Acc' value='desconciliar3_ok'>
			<input type='hidden' name='id' value='$id'></center>
		</form>
		<iframe name='Oculto_desconciliar' height=1 width=1 style='visibility:hidden'></iframe>
		</body>";
}

function desconciliar3_ok() // quita la marca de conciliacion de una nota crodito
{
	global $id,$Hoyl,$Nusuario,$observaciones;
	q("update nota_credito set conciliado=0,observaciones=concat(observaciones,\"\n$Nusuario [$Hoyl] No concilia motivo: $observaciones\")  where id=$id");
	graba_bitacora('nota_credito','M',$id,'Des-Concilia');
	echo "<body onload='carga()'><script language='javascript'>alert('Proceso Satisfactorio');parent.quita_marca();</script></body>";
}

function elaborar_recibo() // Permite elaborar un recibo de caja
{
	global $idFac;
	$Fac=qo("select * from factura where id=$idFac"); //trae los datos de la factura
	if($Fac->siniestro)
	{
		$Sin=qo("select * from siniestro where id=$Fac->siniestro"); // trae los datos del siniestro y de la oficina
		$idOfi=qo1("select id from oficina where ciudad='$Sin->ciudad'");
	}
	else $idOfi=1;
	$Cli=qo("select * from cliente where id=$Fac->cliente"); // trae los datos del cliente
	$Oficina=qo("select * from oficina where id=$idOfi"); //trae los datos de la oficina
	$Concepto=qo2("select concat(t_concepto_fac(concepto),' ',descripcion,' ') as nconcepto from facturad where factura=$idFac",'; '); // trae el concepto de facturacion
	$Franquicia=q("select * from franquisia_tarjeta"); // trae las franquicias
	// construye las distintas opciones de acuerdo al tipo de franquicia
	$MenuTD="<select name='franqt'><option value='0'></option>";
	$MenuTC="<select name='franqt'><option value='0'></option>";
	$MenuEF="<select name='franqt'><option value='0'></option>";
	while($Fr=mysql_fetch_object($Franquicia))
	{
		if($Fr->tipo=='D') $MenuTD.="<option value='$Fr->id'>$Fr->nombre</option>";
		if($Fr->tipo=='C') $MenuTC.="<option value='$Fr->id'>$Fr->nombre</option>";
		if($Fr->tipo=='E') $MenuEF.="<option value='$Fr->id'>$Fr->nombre</option>";
	}
	$MenuTD.="</select>";
	$MenuTC.="</select>";
	$MenuEF.="</select>";
	$Menu_banco="<select name='Banco'><option value=''></option>"; // construye el menu de bancos
	$Bancos=q("select id,nombre from codigo_ach order by nombre");
	while($B=mysql_fetch_object($Bancos))
	{
		$Menu_banco.="<option value='$B->id'>$B->nombre</option>";
	}
	$Menu_banco.="</select>";
	$CCheque="<table><tr ><td >Nomero del cheque:</td><td ><input type='text' name='Chequeno' size='15' maxlength='15'></td></tr>";
	$CCheque.="<tr ><td >Nombre del Banco:</td><td >$Menu_banco</td></tr>";
	$CCheque.="<tr ><td >Cuenta Bancaria:</td><td ><input type='text' name='Cuenta' size='20' maxlength='50'></td></tr></table>";
	$Autoprevia='';
	IF($Fac->siniestro)
	{
		if($Autorizacion_previa=q("Select id,concat(num_autorizacion,' [$',valor,' - ',fecha_solicitud,']') as nombre from sin_autor where siniestro=$Sin->id and estado='A'")) // busca si hay autorizaciones previas asociadas al siniestro
		{
			$Autoprevia="<select name='Autorizacion'><option value='0'></option>"; // construye un menu con las autorizaciones previas
			while($Aut=mysql_fetch_object($Autorizacion_previa))
			{
				$Autoprevia.="<option value='$Aut->id'>$Aut->nombre</option>";
			}
			$Autoprevia.="</select>";
		}
	}
	// crea una capa de captura para tarjetas debito o efectivo
	$CTD="<table><tr ><td >Franquicia:</td><td >$MenuTD</td></tr><tr ><td >Autorizacion:</td><td ><input type='text' name='autorizacion'></td></tr>";
	$CTD.="<tr ><td >Nombre del banco:</td><td >$Menu_banco</td></tr>";
	$CTD.="</table>";
	// crea una capa de captura para tarjetas credito
	$CTC="<table><tr ><td >Franquicia:</td><td >$MenuTC</td></tr><tr ><td >Autorizacion:</td><td ><input type='text' name='autorizacion'></td></tr>";
	$CTC.="<tr ><td >Nombre del Banco:</td><td >$Menu_banco</td></tr>";
	$CTC.="<tr ><td colspan='2'><hr></td></tr>";
	$CTC.="<tr ><td >Autorizacion previa:</td><td >$Autoprevia</td></tr></table>";
	html("ELABORACION RECIBO DE CAJA"); // pinta las cabeceras html y las rutinas javascript
	echo "<script language='javascript'>
			function validar_recibo()
			{
				document.forma.Acc.value='elaborar_recibo_ok';
				document.forma.submit();
			}
			function pinta_datos(dato)
			{
				var Capa=document.getElementById('Fpg')
				if(dato=='TD') Capa.innerHTML=\"$CTD\";
				if(dato=='TC') Capa.innerHTML=\"$CTC\";
				if(dato=='EF') Capa.innerHTML=\"<table><tr ><td >Tipo:</td><td >$MenuEF</td></tr></table>\";
				if(dato=='CH') Capa.innerHTML=\"$CCheque\";
			}
		</script>
		<body onload='carga()'>
		<script language='javascript'>centrar(700,500);</script>
		<form action='zcartera.php' method='post' target='_self' name='forma' id='forma'>
			Factura nomero: <b>$Fac->consecutivo</b> Fecha de emision: <b>$Fac->fecha_emision</b> Fecha de Vencimiento: <b>$Fac->fecha_vencimiento</b><br />
			Cliente: <b>$Cli->nombre $Cli->apellido</b><br />
			Oficina: <b>$Oficina->nombre</b><br />
			Concepto: <b>$Concepto</b><br />";
	if($Fac->siniestro)
	{
		echo "Siniestro: <b>$Sin->numero</b> Fecha de Autorizacion: <b>$Sin->fec_autorizacion</b><br /><hr>";
	}
	// pinta el formulario del recibo de caja
	echo "<table border=0 cellspacing='0'  cellpadding=3 align='center' bgcolor='eeeeee'>
				<tr><td align='right'>Fecha Recibo:</td><td>".pinta_FC('forma','Fecha',date('Y-m-d'))."</td><td align='right'>Oficina:</td><td><input type='hidden' name='Oficina' value='$Oficina->id'>$Oficina->nombre</td></tr>
				<tr><td align='right'>Recibido de:</td><td colspan=3>$Cli->nombre $Cli->apellido<input type='hidden' name='Cliente' value='$Fac->cliente'></td></tr>
				<t><td align='right'>Valor Recibido:</td><td>$ <input type='text' name='Valor' id='Valor' value='$Fac->total' style='font-weight:bold;font-size:12px;' class='numero'></td></tr>
				<tr><td align='right'>Concepto</td><td colspan=3><input type='text' name='Concepto' id='Concepto' size='100' maxlength='80' value='".L($Concepto,80)."' ></td></tr>
			</table><table border=0 cellspacing='0' align='center' bgcolor='eeeeee'>
			<tr><th colspan=8>FORMA DE PAGO</th></tr>
			<tr><td align='center'><b>Forma:</b></td>
			<td ><select name='Formap' id='Formap' onchange='pinta_datos(this.value);'><option value=''></option>
				<option value='TD'>Tarjeta Dobito</option>
				<option value='TC'>Tarjeta Credito</option>
				<option value='EF'>Efectivo</option>
				<option value='CH'>Cheque</option>
			</td>
			<td align='center' id='Fpg'>
			</td></tr>
			</table>";
	echo "
			<center><input type='button' value='Grabar' onclick='validar_recibo()' style='font-weight:bold'></center>
			<input type='hidden' name='Acc' value=''>
			<input type='hidden' name='Siniestro' value='$Fac->siniestro'>
			<input type='hidden' name='Factura' value='$idFac'>
		</form>
		</body>";
}

function elaborar_recibo_ok() // graba el recibo de caja
{
	global $Fecha,$Oficina,$Cliente,$Valor,$Concepto,$Siniestro,$Factura,$Tarjeta_credito,$Autorizacion,$Efectivo,$Cheque,$Banco,$Cuenta,$Chequeno,$Nusuario,$Formap,$franqt,$Hoyl,$autorizacion;
	$Fac=qo("select * from factura where id=$Factura"); // trae los datos de la factura
	$Cli=qo("select * from cliente where id=$Cliente"); // trae los datos del cliente
	
	$facturaConcepto = qo("select fd.concepto from factura fa inner join facturad fd on fa.id =  fd.factura where fa.id = $Factura");
	
	if($facturaConcepto->concepto == '81'){
			$con = '1';
	}else{
		    $con = '0';
	}

	
	$Consecutivo=qo1("select max(consecutivo) from recibo_caja where oficina=$Oficina")+1; // calcula el siguiente consecutivo de recibo de caja
	$IDN=q("insert into recibo_caja (oficina,fecha,consecutivo,cliente,valor,concepto,siniestro,factura,capturado_por) values 
	('$Oficina','$Fecha','$Consecutivo','$Cliente','$Valor',\"$Concepto\",'$Siniestro','$Factura','$Nusuario') "); // inserta el registro de recibo de caja
	if($Formap=='TD') // si es tarjeta debito, crea un registro de autorizacion automotico para la devolucion de esta garantoa dado el caso
	{
		
		$Autorizacion_id=q("insert into sin_autor (siniestro,nombre,identificacion,franquicia,valor,funcionario,estado,observaciones,num_autorizacion,fecha_solicitud,solicitado_por,fecha_proceso,procesado_por,banco,aut_fac)
		values ('$Siniestro','$Cli->nombre $Cli->apellido','$Cli->identificacion','$franqt','$Valor','$Nusuario','A','Valor recibido con Tarjeta Debito','$autorizacion','$Hoyl','$Nusuario','$Hoyl','$Nusuario','$Banco','$con')");
		q("update recibo_caja set tarjeta_debito_valor='$Valor',tarjeta_debito_autor='$autorizacion',autorizacion='$Autorizacion_id' where id=$IDN"); // asigna el registro de autorizacion al recibo de caja
	}
	if($Formap=='TC')
	{
		if(!$Autorizacion) // crea un registro automotico dentro de autorizaciones con los datos de la tarjeta crodito.
		{
			$Autorizacion_id=q("insert into sin_autor (siniestro,nombre,identificacion,franquicia,valor,funcionario,estado,observaciones,num_autorizacion,fecha_solicitud,solicitado_por,fecha_proceso,procesado_por,banco,aut_fac)
			values ('$Siniestro','$Cli->nombre $Cli->apellido','$Cli->identificacion','$franqt','$Valor','$Nusuario','A','Valor recibido con Tarjeta Debito','$autorizacion','$Hoyl','$Nusuario','$Hoyl','$Nusuario','$Banco','$con')");
			q("update recibo_caja set tarjeta_credito='$Valor',tarjeta_credit_autor='$autorizacion',autorizacion='$Autorizacion_id' where id=$IDN"); // asigna el registro de autorizacion al recibo de caja
		}
		else
			q("update recibo_caja set tarjeta_credito='$Valor',tarjeta_credit_autor='$autorizacion',autorizacion='$Autorizacion' where id=$IDN"); // asigna el registro de autorizacion al nuevo recibo de caja.
	}
	if($Formap=='EF') // inserta  la autorizacion respectiva para el efectivo.
	{
		$Autorizacion_id=q("insert into sin_autor (siniestro,nombre,identificacion,franquicia,valor,funcionario,estado,observaciones,num_autorizacion,fecha_solicitud,solicitado_por,fecha_proceso,procesado_por,aut_fac)
		values ('$Siniestro','$Cli->nombre $Cli->apellido','$Cli->identificacion','$franqt','$Valor','$Nusuario','A','Valor recibido en Efectivo','$autorizacion','$Hoyl','$Nusuario','$Hoyl','$Nusuario','$con')");
		q("update recibo_caja set efectivo='$Valor',autorizacion='$Autorizacion_id' where id=$IDN"); // asigna el regisro de autorizacion al recibo de caja
	}
	if($Formap=='CH') // si la forma de pago es con cheque
	{
		q("update recibo_caja set cheque='$Valor',banco='$Banco',cuenta='$Cuenta',numero_cheque='$Chequeno' where id=$IDN"); // actualiza el recibo de caja con los datos de cheque
	}

	// LIQUIDACION DE IMPUESTOS previo a la conciliacion.

	$base=$Fac->subtotal;
	$iva=$Fac->iva;
	$total=$Fac->total;
	if($Autorizacion) // calcula los impuestos de acuerdo a la franquicia
	{
		$Au=qo("select * from sin_autor where id=$Autorizacion");
		$Fr=qo("select * from franquisia_tarjeta where id=$Au->franquicia");
		$prete_ica=$Fr->prete_ica;
		$prete_fuente=$Fr->prete_fuente;
		$pcomision=$Fr->pcomision;
		$prete_iva=$Fr->prete_iva;
		$rete_ica=round($base*$prete_ica/1000,2);
		$rete_fuente=round($base*$prete_fuente/100,2);
		$comision=round($base*$pcomision/100,2);
		$rete_iva=round($iva*$prete_iva/100,2);
	}
	else
	{
		$prete_ica=$prete_fuente=$pcomision=$prete_iva=$rete_ica=$rete_fuente=$comision=$rete_iva=0;
	}
	$total_abonado=$total-($rete_ica+$rete_fuente+$comision+$rete_iva); // calcula lo que se recibe neto quitando los impuestos
	q("update recibo_caja set base='$base',iva='$iva',total='$total',prete_ica='$prete_ica',prete_fuente='$prete_fuente',pcomision='$pcomision',prete_iva='$prete_iva',rete_ica='$rete_ica',rete_fuente='$rete_fuente',
		comision='$comision', rete_iva='$rete_iva',total_abonado='$total_abonado' where id=$IDN"); // en la zona de conciliacion del recibo de caja quedaron los valores de los impuestos y el neto recibido
	graba_bitacora('recibo_caja','A',$IDN); // graba la bitacora del recibo de caja
	echo "<body onload='carga()'><script language='javascript'>window.close();void(null);opener.parent.recargar();</script></body>";
}

function imprimir_recibo() // imprime el recibo generando un documento en formato pdf
{
	global $id,$Hoyl;
	$sql = "select *,t_oficina(oficina) as noficina from recibo_caja where id=$id";
	//echo $sql; 
	$R=qo($sql);
	$Cli=qo("select * from cliente where id=$R->cliente");
	if($R->siniestro) $Sin=qo("select numero,fec_autorizacion from siniestro where id=$R->siniestro");
	if($R->autorizacion) $TC=qo("select *,t_codigo_ach(banco) as nbanco from sin_autor where id=$R->autorizacion");
	if($R->factura) $Fac=qo("select * from factura where id=$R->factura");
	include('inc/pdf/fpdf.php'); //incluye el objeto
	$P=new pdf('P','mm','letter');  // crea la instancia de la clase pdf
	$P->AddFont("c128a","","c128a.php"); // adicicona fuentes para codigo de barras
	$P->AliasNbPages();
	$P->setTitle("RECIBO DE CAJA");
	$P->setAuthor("Arturo Quintero www.aoacolombia.com arturoquintero@aoacolombia.com");
	$P->Numeracion=false;
	$P->SetAutoPageBreak(false);
	$P->setFillColor(250,250,250);
	//	$P->Header_texto='';
	//	$P->Header_alineacion='L';
	//	$P->Header_alto='8';
	$P->SetTopMargin('5');
	//	$P->Header_colores=array(0,0,0,255,255,255,50,50,100); # rgb texto, rgb fondo, rgb borde
	//	$P->Header_imagen='img/cnota_entrada.jpg';
	///	$P->Header_posicion_imagen=array(20,5,80,14);
	$P->AddPage('P'); // adiciona una pagina
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$ny=5;
	$P->Image('../img/LOGO_AOA_200.jpg',50,$ny,30,12);
	if($R->anulado) $P->image('gifs/ANULADO2.jpg',60,25,80,60);
	$P->SetXY(100,$ny);$P->SetTextColor(0,0,0);$P->setfont('Arial','B',10);$P->Cell(90,5,'ADMISTRACIoN OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
	$ny=$P->y+4;$P->setxy(100,$ny);$P->setfont('Arial','B',10);$P->Cell(90,5,'NIT.: 900.174.552-5',0,0,'C');
	$P->rect(110,$ny+5,70,14);
	$ny=$P->y+7;$P->setxy(120,$ny);$P->setfont('Arial','B',16);$P->Cell(80,5,'RECIBO DE CAJA',0,0,'L');
	$ny=$P->y+2;$P->setxy(20,$ny);$P->setfont('Arial','',10);$P->cell(90,4,'Carrera 69B 98A-10 Bogoto D.C.',0,0,'C');
	$ny=$P->y+3;$P->setxy(130,$ny);$P->setfont('Times','B',16);$P->Cell(10,5,'No.',0,0,'L');$P->setfont('Arial','B',16);$P->Cell(20,5,str_pad($R->consecutivo,6,'0',STR_PAD_LEFT),0,0,'L');
	$ny=$P->y+1;$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(90,4,'Pbx: (057) 1 7560510 Fax (057) 1 7560512',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->cell(90,4,'www.aoacolombia.com',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->Cell(22,4,'Ciudad:',1,0,'L');$P->Cell(108,4,$R->noficina,1,0,'L');$P->Cell(20,8,'Fecha:',1,0,'C');$P->Cell(30,8,$R->fecha,1,0,'C');
	$ny=$P->y+4;
	$P->setxy(20,$ny);
	if($R->siniestro)
	{
		$P->Cell(22,4,'Siniestro:',1,0,'L');
		$P->cell(108,4,$Sin->numero.' F.Autorizacion: '.$Sin->fec_autorizacion,1,0,'L');
	}
	else
	{
		$P->Cell(22,4,'',1,0,'L');
		$P->cell(108,4,' ',1,0,'L');
	}
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Recibido de:',1,0,'L');$P->Cell(108,4,trim($Cli->nombre.' '.$Cli->apellido.' '.coma_format($Cli->identificacion)),1,0,'L');$P->Cell(8,4,'$',1,0,'C');$P->Cell(42,4,coma_format($R->valor),1,0,'R',1);
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Direccion:',1,0,'L');$P->Cell(158,4,$Cli->direccion,1,0,'L');
	$P->setxy(20,$P->y+4);$P->setfont('Arial','',6);$P->multicell(180,4,'En Letras: '.enletras($R->valor,1),1,'J',1);
	$P->setxy(20,$P->y);$P->setfont('Arial','',8);$P->multicell(180,4,'Concepto: '.str_replace("\r\n","",$R->concepto).($R->factura?" FAC.No. ".str_pad($Fac->consecutivo,6,'0',STR_PAD_LEFT):""),1,'J');
	$P->setxy(20,$P->y+1);$P->setfont('Arial','B',8);$P->Cell(180,4,'FORMA DE PAGO',1,0,'C',1);$P->setfont('Arial','',8);
	$ny=$P->y+4;
	$P->setxy(20,$ny);
	if($R->tarjeta_debito_valor)
	{
		$P->cell(40,4,'Tarjeta Debito: $'.coma_format($R->tarjeta_debito_valor),1,0,'L');$P->cell(36,4,'Autorizacion: '.$R->tarjeta_debito_autor,1,0,'L');
		if($TC) $Franq='Franquicia: '.qo1("select t_franquisia_tarjeta($TC->franquicia) ")." $TC->nbanco"; else $Franq='';
		$P->cell(104,4,$Franq,1,0,'L');
	}
	if($R->efectivo)
	{
		$P->cell(40,4,'Efectivo: $'.coma_format($R->efectivo),1,0,'L');
		if($TC) $Autorizacion='Autorizacion: '.$TC->num_autorizacion; else $Autorizacion='';
		$P->cell(140,4,$Autorizacion,1,0,'L');
	}
	if($R->tarjeta_credito)
	{
		$P->cell(40,4,'Tarjeta Credito: $'.coma_format($R->tarjeta_credito),1,0,'L');$P->cell(36,4,'Autorizacion: '.$R->tarjeta_credit_autor,1,0,'L');
		if($TC) $Franq='Franquicia: '.qo1("select t_franquisia_tarjeta($TC->franquicia) ")." $TC->nbanco"; else $Franq='';
		$P->cell(104,4,$Franq,1,0,'L');
	}
	if($R->cheque)
	{
		$P->cell(40,4,'Cheque: $'.coma_format($R->cheque),1,0,'L');$P->cell(64,4,'Banco: '.$R->banco,1,0,'L');$P->cell(38,4,'Cuenta: '.$R->cuenta,1,0,'L');$P->cell(38,4,'No.Cheque: '.$R->numero_cheque,1,0,'L');
	}
	$ny=$P->y+4;
	$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(110,4,'Elaborado por: '.$R->capturado_por ,1,1,'L');$P->setxy(130,$ny);$P->cell(70,15,' ',1);
	$ny=$P->y+5;$P->setxy(20,$ny);$P->SetFont("c128a","",12);$P->cell(110,14, uccean128('FA'.str_pad($Fac->consecutivo,10,'0',STR_PAD_LEFT).str_pad($R->consecutivo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$ny=$P->y+10;$P->setxy(130,$ny);$P->setfont('Arial','',8);$P->cell(70,4,'Firma y sello',1,0,'L');
	$ny=$P->y+4;
	$P->setxy(100,$ny);$P->setfont('Arial','B',8);$P->cell(20,4,'ORIGINAL',0,0,'C');
	$P->setxy(170,$ny);$P->setfont('Arial','',6);$P->cell(30,4,$Hoyl,0,0,'R');

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$ny=$P->y+10;
	$P->Image('../img/LOGO_AOA_200.jpg',50,$ny,30,12);
	if($R->anulado) $P->image('gifs/ANULADO2.jpg',60,120,80,60);
	$P->SetXY(100,$ny);$P->SetTextColor(0,0,0);$P->setfont('Arial','B',10);$P->Cell(90,5,'ADMISTRACIoN OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
	$ny=$P->y+4;$P->setxy(100,$ny);$P->setfont('Arial','B',10);$P->Cell(90,5,'NIT.: 900.174.552-5',0,0,'C');
	$P->rect(110,$ny+5,70,14);
	$ny=$P->y+7;$P->setxy(120,$ny);$P->setfont('Arial','B',16);$P->Cell(80,5,'RECIBO DE CAJA',0,0,'L');
	$ny=$P->y+2;$P->setxy(20,$ny);$P->setfont('Arial','',10);$P->cell(90,4,'Carrera 69B 98A-10 Bogoto D.C.',0,0,'C');
	$ny=$P->y+3;$P->setxy(130,$ny);$P->setfont('Times','B',16);$P->Cell(10,5,'No.',0,0,'L');$P->setfont('Arial','B',16);$P->Cell(20,5,str_pad($R->consecutivo,6,'0',STR_PAD_LEFT),0,0,'L');
	$ny=$P->y+1;$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(90,4,'Pbx: (057) 1 7560510 Fax (057) 1 7560512',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->cell(90,4,'www.aoacolombia.com',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->Cell(22,4,'Ciudad:',1,0,'L');$P->Cell(108,4,$R->noficina,1,0,'L');$P->Cell(20,8,'Fecha:',1,0,'C');$P->Cell(30,8,$R->fecha,1,0,'C');
	$ny=$P->y+4;
	$P->setxy(20,$ny);
	if($R->siniestro)
	{
		$P->Cell(22,4,'Siniestro:',1,0,'L');
		$P->cell(108,4,$Sin->numero.' F.Autorizacion: '.$Sin->fec_autorizacion,1,0,'L');
	}
	else
	{
		$P->Cell(22,4,'',1,0,'L');
		$P->cell(108,4,' ',1,0,'L');
	}
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Recibido de:',1,0,'L');$P->Cell(108,4,trim($Cli->nombre.' '.$Cli->apellido.' '.coma_format($Cli->identificacion)),1,0,'L');$P->Cell(8,4,'$',1,0,'C');$P->Cell(42,4,coma_format($R->valor),1,0,'R',1);
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Direccion:',1,0,'L');$P->Cell(158,4,$Cli->direccion,1,0,'L');
	$P->setxy(20,$P->y+4);$P->setfont('Arial','',6);$P->multicell(180,4,'En Letras: '.enletras($R->valor,1),1,'J',1);
	$P->setxy(20,$P->y);$P->setfont('Arial','',8);$P->multicell(180,4,'Concepto: '.str_replace("\r\n","",$R->concepto).($R->factura?" FAC.No. ".str_pad($Fac->consecutivo,6,'0',STR_PAD_LEFT):""),1,'J');
	$P->setxy(20,$P->y+1);$P->setfont('Arial','B',8);$P->Cell(180,4,'FORMA DE PAGO',1,0,'C',1);$P->setfont('Arial','',8);
	$ny=$P->y+4;
	$P->setxy(20,$ny);
	if($R->tarjeta_debito_valor)
	{
		$P->cell(40,4,'Tarjeta Debito: $'.coma_format($R->tarjeta_debito_valor),1,0,'L');$P->cell(36,4,'Autorizacion: '.$R->tarjeta_debito_autor,1,0,'L');
		if($TC) $Franq='Franquicia: '.qo1("select t_franquisia_tarjeta($TC->franquicia) ")." $TC->nbanco"; else $Franq='';
		$P->cell(104,4,$Franq,1,0,'L');
	}
	if($R->efectivo)
	{
		$P->cell(40,4,'Efectivo: $'.coma_format($R->efectivo),1,0,'L');
		if($TC) $Autorizacion='Autorizacion: '.$TC->funcionario.' ['.$TC->fecha_proceso.']'; else $Autorizacion='';
		$P->cell(140,4,$Autorizacion,1,0,'L');
	}
	if($R->tarjeta_credito)
	{
		$P->cell(40,4,'Tarjeta Credito: $'.coma_format($R->tarjeta_credito),1,0,'L');$P->cell(36,4,'Autorizacion: '.$R->tarjeta_credit_autor,1,0,'L');
		if($TC) $Franq='Franquicia: '.qo1("select t_franquisia_tarjeta($TC->franquicia) ")." $TC->nbanco"; else $Franq='';
		$P->cell(104,4,$Franq,1,0,'L');
	}
	if($R->cheque)
	{
		$P->cell(40,4,'Cheque: $'.coma_format($R->cheque),1,0,'L');$P->cell(64,4,'Banco: '.$R->banco,1,0,'L');$P->cell(38,4,'Cuenta: '.$R->cuenta,1,0,'L');$P->cell(38,4,'No.Cheque: '.$R->numero_cheque,1,0,'L');
	}
	$ny=$P->y+4;
	$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(110,4,'Elaborado por: '.$R->capturado_por ,1,1,'L');$P->setxy(130,$ny);$P->cell(70,15,' ',1);
	$ny=$P->y+5;$P->setxy(20,$ny);$P->SetFont("c128a","",12);$P->cell(110,14, uccean128('FA'.str_pad($Fac->consecutivo,10,'0',STR_PAD_LEFT).str_pad($R->consecutivo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$ny=$P->y+10;$P->setxy(130,$ny);$P->setfont('Arial','',8);$P->cell(70,4,'Firma y sello',1,0,'L');
	$ny=$P->y+4;
	$P->setxy(100,$ny);$P->setfont('Arial','B',8);$P->cell(20,4,'TARJETA HABIENTE',0,0,'C');
	$P->setxy(170,$ny);$P->setfont('Arial','',6);$P->cell(30,4,$Hoyl,0,0,'R');
	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$ny=$P->y+10;
	$P->Image('../img/LOGO_AOA_200.jpg',50,$ny,30,12);
	if($R->anulado) $P->image('gifs/ANULADO2.jpg',60,205,80,60);
	$P->SetXY(100,$ny);$P->SetTextColor(0,0,0);$P->setfont('Arial','B',10);$P->Cell(90,5,'ADMISTRACIoN OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
	$ny=$P->y+4;$P->setxy(100,$ny);$P->setfont('Arial','B',10);$P->Cell(90,5,'NIT.: 900.174.552-5',0,0,'C');
	$P->rect(110,$ny+5,70,14);
	$ny=$P->y+7;$P->setxy(120,$ny);$P->setfont('Arial','B',16);$P->Cell(80,5,'RECIBO DE CAJA',0,0,'L');
	$ny=$P->y+2;$P->setxy(20,$ny);$P->setfont('Arial','',10);$P->cell(90,4,'Carrera 69B 98A-10 Bogoto D.C.',0,0,'C');
	$ny=$P->y+3;$P->setxy(130,$ny);$P->setfont('Times','B',16);$P->Cell(10,5,'No.',0,0,'L');$P->setfont('Arial','B',16);$P->Cell(20,5,str_pad($R->consecutivo,6,'0',STR_PAD_LEFT),0,0,'L');
	$ny=$P->y+1;$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(90,4,'Pbx: (057) 1 7560510 Fax (057) 1 7560512',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->cell(90,4,'www.aoacolombia.com',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->Cell(22,4,'Ciudad:',1,0,'L');$P->Cell(108,4,$R->noficina,1,0,'L');$P->Cell(20,8,'Fecha:',1,0,'C');$P->Cell(30,8,$R->fecha,1,0,'C');
	$ny=$P->y+4;
	$P->setxy(20,$ny);
	if($R->siniestro)
	{
		$P->Cell(22,4,'Siniestro:',1,0,'L');
		$P->cell(108,4,$Sin->numero.' F.Autorizacion: '.$Sin->fec_autorizacion,1,0,'L');
	}
	else
	{
		$P->Cell(22,4,'',1,0,'L');
		$P->cell(108,4,' ',1,0,'L');
	}
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Recibido de:',1,0,'L');$P->Cell(108,4,trim($Cli->nombre.' '.$Cli->apellido.' '.coma_format($Cli->identificacion)),1,0,'L');$P->Cell(8,4,'$',1,0,'C');$P->Cell(42,4,coma_format($R->valor),1,0,'R',1);
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Direccion:',1,0,'L');$P->Cell(158,4,$Cli->direccion,1,0,'L');
	$P->setxy(20,$P->y+4);$P->setfont('Arial','',6);$P->multicell(180,4,'En Letras: '.enletras($R->valor,1),1,'J',1);
	$P->setxy(20,$P->y);$P->setfont('Arial','',8);$P->multicell(180,4,'Concepto: '.str_replace("\r\n","",$R->concepto).($R->factura?" FAC.No. ".str_pad($Fac->consecutivo,6,'0',STR_PAD_LEFT):""),1,'J');
	$P->setxy(20,$P->y+1);$P->setfont('Arial','B',8);$P->Cell(180,4,'FORMA DE PAGO',1,0,'C',1);$P->setfont('Arial','',8);
	$ny=$P->y+4;
	$P->setxy(20,$ny);
	if($R->tarjeta_debito_valor)
	{
		$P->cell(40,4,'Tarjeta Debito: $'.coma_format($R->tarjeta_debito_valor),1,0,'L');$P->cell(36,4,'Autorizacion: '.$R->tarjeta_debito_autor,1,0,'L');
		if($TC) $Franq='Franquicia: '.qo1("select t_franquisia_tarjeta($TC->franquicia) ")." $TC->nbanco"; else $Franq='';
		$P->cell(104,4,$Franq,1,0,'L');
	}
	if($R->efectivo)
	{
		$P->cell(40,4,'Efectivo: $'.coma_format($R->efectivo),1,0,'L');
		if($TC) $Autorizacion='Autorizacion: '.$TC->funcionario.' ['.$TC->fecha_proceso.']'; else $Autorizacion='';
		$P->cell(140,4,$Autorizacion,1,0,'L');
	}
	if($R->tarjeta_credito)
	{
		$P->cell(40,4,'Tarjeta Credito: $'.coma_format($R->tarjeta_credito),1,0,'L');$P->cell(36,4,'Autorizacion: '.$R->tarjeta_credit_autor,1,0,'L');
		if($TC) $Franq='Franquicia: '.qo1("select t_franquisia_tarjeta($TC->franquicia) ")." $TC->nbanco"; else $Franq='';
		$P->cell(104,4,$Franq,1,0,'L');
	}
	if($R->cheque)
	{
		$P->cell(40,4,'Cheque: $'.coma_format($R->cheque),1,0,'L');$P->cell(64,4,'Banco: '.$R->banco,1,0,'L');$P->cell(38,4,'Cuenta: '.$R->cuenta,1,0,'L');$P->cell(38,4,'No.Cheque: '.$R->numero_cheque,1,0,'L');
	}
	$ny=$P->y+4;
	$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(110,4,'Elaborado por: '.$R->capturado_por ,1,1,'L');$P->setxy(130,$ny);$P->cell(70,15,' ',1);
	$ny=$P->y+5;$P->setxy(20,$ny);$P->SetFont("c128a","",12);$P->cell(110,14, uccean128('FA'.str_pad($Fac->consecutivo,10,'0',STR_PAD_LEFT).str_pad($R->consecutivo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$ny=$P->y+10;$P->setxy(130,$ny);$P->setfont('Arial','',8);$P->cell(70,4,'Firma y sello',1,0,'L');
	$ny=$P->y+4;
	$P->setxy(100,$ny);$P->setfont('Arial','B',8);$P->cell(20,4,'COPIA',0,0,'C');
	$P->setxy(170,$ny);$P->setfont('Arial','',6);$P->cell(30,4,$Hoyl,0,0,'R');
	$P->Output($Archivo); // presenta el archivo para ser visualizado en el browser o descargarlo al pc
}

function nuevo_recibo_garantia() // formulario para crear un nuevo recibo de caja por garantia. no depende de factura
{
	global $idcita,$USUARIO,$PRE_AUTORIZA;
	$Cita=qo("select * from cita_servicio where id=$idcita"); // trae los datos de la cita
	$Siniestro=qo("select * from siniestro where id=$Cita->siniestro"); // trae los datos del siniestro
	if(!$Cita->pre_autorizacion)
	{
		if(inlist($USUARIO,'1,2,5,10') || $PRE_AUTORIZA)
		{
			html(); // crea un codigo de pre autorizacion para el recibo de caja. sin ese codigo no se puede autorizar la elaboracion del recibo
			echo "<body><script language='javascript'>centrar(200,200);
						window.open('zcartera.php?Acc=genera_pre_autorizacion&id=$idcita','_self');
						</script></body>";
			die();
		}
		else
		{
			html();
			echo "<body><script language='javascript'>centrar(200,200);alert('Esta cita no tiene Pre-autorizacion para recibir garantoa de efectivo, debe solicitarla al departamento de Autorizaciones');window.close();void(null);</script></body>";
			die();
		}
	}
	//////  VALIDACION SI LA CITA DEL SINIESTRO YA TIENE MARCADO EL ARRIBO
	if($Cita->arribo=='0000-00-00 00:00:00')
	{
		echo "<body><script language='javascript'>alert('Aun no se ha marcado el indicador de Arribo del asegurado. $Cita->arribo');window.close();void(null);</script></body>";
		die();
	}
	html('TESORERIA - RECIBO DE CAJA'); // pinta las cabeceras html y las herramientas java script
	echo "<script language='javascript'>
			function validar_identificacion(id)
			{
				if(!id) {alert('Debe digitar una identifcacion valida');   document.forma.btn_grabar.style.visibility='hidden';document.forma.identificacion.focus();return false; }
				else {document.forma.btn_grabar.style.visibility='visible';}
				window.open('zcartera.php?Acc=valida_identificacion&id='+id,'Oculto_recibo');
			}
			function busqueda_ciudad2(Campo,Contenido)
			{
				var Ventana_ciudad=document.getElementById('Busqueda_Ciudad');
				Ventana_ciudad.style.visibility='visible';
				Ventana_ciudad.style.left=mouseX;
				Ventana_ciudad.style.top=mouseY-10;
				Ventana_ciudad.src='inc/ciudades.html';
				Ciudad_campo=Campo;
				Ciudad_forma='forma';
			}
			function oculta_busca_ciudad()
			{
				document.getElementById('Busqueda_Ciudad').style.visibility='hidden';
			}

			function validar_datos1()
			{
				with(document.forma)
				{
					if(!alltrim(identificacion.value)) { alert('Debe digitar la identificacion sin comas ni puntos, solo digitos numoricos.'); identificacion.style.backgroundColor='ffff44'; identificacion.focus(); return false; }
					if(!Number(identificacion.value)) { alert('Debe digitar la identificacion sin comas ni puntos, solo digitos numoricos.'); identificacion.style.backgroundColor='ffff44'; identificacion.focus(); return false; }
					if(!tipo_id.value) { alert('Debe seleccionar un tipo de identificacion.'); tipo_id.style.backgroundColor='ffff44'; tipo_id.focus(); return false; }
					if(!alltrim(nombre.value)) { alert('Debe digitar un nombre.'); nombre.style.backgroundColor='ffff44'; nombre.focus(); return false; }
					if(!alltrim(apellido.value)) { alert('Debe digitar un apellido.'); apellido.style.backgroundColor='ffff44'; apellido.focus(); return false; }
					if(!alltrim(lugar_expdoc.value)) { alert('Debe digitar el lugar de expedicion del documento de identificacion.'); lugar_expdoc.style.backgroundColor='ffff44'; lugar_expdoc.focus(); return false; }
					if(!sexo.value) {alert('Debe seleccionar el sexo');sexo.syle.backgroundColor='ffff44';sexo.focus();return false; }
					if(!pais.value) { alert('Debe seleccionar un pais.'); pais.style.backgroundColor='ffff44'; pais.focus(); return false; }
					if(!ciudad.value) { alert('Debe seleccionar una ciudad.'); ciudad.style.backgroundColor='ffff44'; ciudad.focus(); return false; }
					if(!alltrim(direccion.value)) { alert('Debe digitar la direccion de residencia.'); direccion.style.backgroundColor='ffff44'; direccion.focus(); return false; }
					if(!alltrim(telefono_oficina.value)) { alert('Debe digitar el telefono de oficina.'); telefono_oficina.style.backgroundColor='ffff44'; telefono_oficina.focus(); return false; }
					if(!alltrim(telefono_casa.value)) { alert('Debe digitar el telefono de la vivienda.'); telefono_casa.style.backgroundColor='ffff44'; telefono_casa.focus(); return false; }
					if(!alltrim(email_e.value)) { alert('Debe digitar el correo electronico.'); email_e.style.backgroundColor='ffff44'; email_e.focus(); return false; }
					if(!franqt.value) { alert('Debe seleccionar la franquicia de la tarjeta'); franqt.style.backgroundColor='ffff44'; franqt.focus(); return false; }
					if(!alltrim(cuenta.value)) { alert('Debe digitar el nomero de cuenta bancaria.'); cuenta.style.backgroundColor='ffff44'; cuenta.focus(); return false; }
					if(!banco.value) { alert('Debe seleccionar el banco.'); banco.style.backgroundColor='ffff44'; banco.focus(); return false; }
					if(!iddevol.value) { alert('Debe escribir la identificacion de la cuenta de devolucion.'); iddevol.style.backgroundColor='ffff44'; iddevol.focus(); return false; }
					if(!alltrim(anombrede.value)) { alert('Debe escribir a nombre de quien es la cuenta de devolucion.'); anombrede.style.backgroundColor='ffff44'; anombrede.focus(); return false; }
					if(!tipo.value) { alert('Debe seleccionar el tipo de cuenta bancaria.'); tipo.style.backgroundColor='ffff44'; tipo.focus(); return false; }
				}
				document.forma.submit();
			}

			function cancelar_captura()
			{
				window.close();void(null);
			}
			function registrar_ingreso_recepcion()
			{
				modal('zingreso_recepcion.php?n=&m=VEHICULO DE REEMPLAZO&idcita=$idcita',0,0,600,600,'ingreso_recepcion');
			}

		</script>
		<body>
		<form action='zcartera.php' method='post' target='_self' name='forma' id='forma'>
			<h3 align='center'>INFORMACION DEL TARJETAHABIENTE</h3>
			<table border cellspacing='0' align='center'>
				<tr><td align='right'>Identificacion:</td><td><input type='text' name='identificacion' id='identificacion' value='' class='numero' onblur='validar_identificacion(this.value);'></td></tr>
				<tr><td align='right'>Tipo de identificacion:</td><td>".menu1('tipo_id',"select codigo,nombre from tipo_identificacion",'',1)."</td></tr>
				<tr><td align='right'>Nombres:</td><td><input type='text' name='nombre' id='nombre' onblur=\"this.value=this.value.toUpperCase();\"></td></tr>
				<tr><td align='right'>Apellidos:</td><td><input type='text' name='apellido' id='apellido' onblur=\"this.value=this.value.toUpperCase();\"></td></tr>
				<tr><td align='right'>Lugar de expedicion de la Identificacion:</td><td><input type='text' name='lugar_expdoc' id='lugar_expdoc' onblur='this.value=this.value.toUpperCase();'></td></tr>
				<tr ><td align='right'>Sexo:</td><td ><select name='sexo' id='sexo'><option value=''></option><option value='M'>Masculino</option><option value='F'>Femenino</option></select></td></tr>
				<tr><td align='right'>Pais:</td><td>".menu1('pais',"select codigo,nombre from pais order by nombre",'CO',1)."</td></tr>
				<tr><td align='right'>Ciudad: </td><td><input type='text' style='color:#000099;background-color:#FFFFFF;' name='_ciudad' id='_ciudad' size='50' onclick=\"busqueda_ciudad2('ciudad','05001000');\" readonly>
							<input type='hidden' name=ciudad id=ciudad value=''><span id='bc_ciudad'></span></td></tr>
				<tr><td align='right'>Barrio:</td><td><input type='text' name='barrio' id='barrio' size='50' maxlength='50'></td></tr>
				<tr><td align='right'>Direccion Domicilio:</td><td><input type='text' name='direccion' id='direccion' size='50' maxlength='50' onblur='this.value=this.value.toUpperCase();'></td></tr>
				<tr><td align='right'>Telofono Oficina:</td><td><input type='text' name='telefono_oficina' id='telefono_oficina' size='50' maxlength='50'></td></tr>
				<tr><td align='right'>Telofono Vivienda:</td><td><input type='text' name='telefono_casa' id='telefono_casa' size='50' maxlength='50'></td></tr>
				<tr><td align='right'>Celular:</td><td><input type='text' name='celular' id='celular' size='50' maxlength='50'></td></tr>
				<tr><td align='right'>Email 1:</td><td><input type='text' name='email_e' id='email_e' size='50' maxlength='50'></td></tr>
				<tr><td align='right'>Email 2:</td><td><input type='text' name='email2_e' id='email2_e' size='50' maxlength='50'></td></tr>
				<tr><td align='right'>Observaciones:</td><td><input type='text' name='observaciones' id='observaciones' size='50' maxlength='50' onblur='this.value=this.value.toUpperCase();'></td></tr>
				<tr ><th colspan='2'><b>DATOS PARA LA TRANSACCION DE DEVOLUCION DE GARANTIA</b></th></tr>
				<tr ><td align='right'>Franquicia de la tarjeta:</td><td >".menu1("franqt","select f.id,f.nombre from franquisia_tarjeta f,ciudad_franq c where c.franquicia=f.id and c.oficina=$Cita->oficina
																										and concat(',',c.perfil,',') like '%,".$_SESSION['User'].",%' and concat(',',c.aseguradora,',') like '%,$Siniestro->aseguradora,%'
																										and tipo in ('E','D') ")."</td></tr>
				<tr><td align='right'>Numero de cuenta Bancaria:</td><td ><input type='text' name='cuenta' id='cuenta' size=20></td></tr>
				<tr><td align='right'>Tipo de cuenta:</td><td ><select name='tipo' id='tipo'><option value=''></option><option value='A'>Ahoros</option><option value='C'>Corriente</option></select></td></tr>
				<tr><td align='right'>Banco:</td><td >".menu1("banco","select id,nombre from codigo_ach where codigo!='' order by nombre",0,1)."</td></tr>
				<tr><td align='right'>A nombre de:</td><td><input type='text' name='anombrede' id='anombrede' value='' size='50' maxlength='50' onblur='this.value=this.value.toUpperCase();'></td></tr>
				<tr><td align='right'>Identificacion:</td><td><input type='text' name='iddevol' id='iddevol' value='' size='50' maxlength='50'></td></tr>
			</table><br />
			<center><input type='button' id='btn_grabar' name='btn_grabar' value='Grabar Informacion del Cliente' onclick='validar_datos1()' style='width:250px;height:30px;visibility:hidden;'>
			<input type='button' value='Cancelar' onclick='cancelar_captura()' style='width:100px;height:30px'>
			</center>
			<input type='hidden' name='Acc' value='nuevo_recibo_garantia_graba_cliente'>
			<input type='hidden' name='idcita' value='$idcita'>
		</form>
		<center><img id='foto' src='' border='0' height='300px' style='visibility:hidden'></center>
		<iframe name='Oculto_recibo' id='Oculto_recibo' style='visibility:hidden' width='1' height='1'></iframe>
		<iframe id='Busqueda_Ciudad' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#eeffee;z-index:200;' height='400px' width='250px' ></iframe>
		<script language='javascript'>document.forma.identificacion.focus();</script>
		</body>";
}

function valida_identificacion() // busca en la tabla de clientes si ya existe el cliente para traer la informacion a la pantalla 
{
	global $id;
	if(!$Ingreso=qo("select foto_f,nombre,apellido from aoacol_administra.ingreso_recepcion where identificacion='$id' order by id desc limit 1")) // busca en la tabla de ingresos de recepcion
	{
		echo "<body><script language='javascript'>if(confirm('No existe ingreso del Tarjeta-Habiente en el modulo de Recepcion. Desea realizar el registro en este momento?'))
		{
			parent.registrar_ingreso_recepcion();
			parent.document.btn_grabar.style.visibility='hidden';
		}
		</script></body>";
		die();
	}

	if($C=qo("select * from cliente where identificacion='$id' ")) // busca en la tabla de clientes
	{
		$Nciudad=qo1("select concat(departamento,' - ',nombre) from ciudad where codigo='$C->ciudad' ");
		echo "<body><script language='javascript'>
				with(parent.document.forma)
				{
					tipo_id.value='$C->tipo_id';nombre.value='$C->nombre';apellido.value='$C->apellido';lugar_expdoc.value='$C->lugar_expdoc';
					pais.value='$C->pais';ciudad.value='$C->ciudad';_ciudad.value='$Nciudad';barrio.value='$C->barrio';direccion.value='$C->direccion';
					telefono_oficina.value='$C->telefono_oficina';telefono_casa.value='$C->telefono_casa';celular.value='$C->celular';
					email_e.value='$C->email_e';email2_e.value='$C->email2_e';observaciones.value='$C->observaciones';sexo.value='$C->sexo';
				}";
		if($Ingreso) echo "parent.document.getElementById('foto').src='../../Administrativo/$Ingreso->foto_f';parent.document.getElementById('foto').style.visibility='visible';
										parent.document.forma.nombre.value='$Ingreso->nombre';
										parent.document.forma.apellido.value='$Ingreso->apellido';
										";
		else echo "parent.document.getElementById('foto').style.visibility='hidden';alert('No hay registro de ingreso en la Recepcion');";
		echo "</script></body>";
	}
	else
	{
		echo "<body><script language='javascript'>
				{
					with(parent.document.forma)
					{
						tipo_id.value='';nombre.value='';apellido.value='';lugar_expdoc.value='';
						pais.value='CO';ciudad.value='';_ciudad.value='';barrio.value='';direccion.value='';
						telefono_oficina.value='';telefono_casa.value='';celular.value='';
						email_e.value='';observaciones.value='';
					}
					alert('Cliente Nuevo, por favor ingrese toda la informacion');
				}";
		if($Ingreso) echo "parent.document.getElementById('foto').src='../../Administrativo/$Ingreso->foto_f';parent.document.getElementById('foto').style.visibility='visible';
									parent.document.forma.nombre.value='$Ingreso->nombre';
									parent.document.forma.apellido.value='$Ingreso->apellido';";
		else echo "parent.document.getElementById('foto').style.visibility='hidden';alert('No hay registro de ingreso en la Recepcion');";
		echo "</script></body>";
	}
}

function nuevo_recibo_garantia_graba_cliente() // graba la informacion del cliente en la tabla de clientes
{
	global $identificacion,$tipo_id,$nombre,$apellido,$lugar_expdoc,$pais,$ciudad,$barrio,$direccion,$telefono_oficina,$telefono_casa,$celular,$email_e,$email2_e,$observaciones;
	global $idcita,$cuenta,$tipo,$banco,$franqt,$anombrede,$iddevol;
	if($Cliente=qo1("select id from cliente where identificacion=$identificacion"))
	{
			// verifica la pre-existencia del cliente para actualizar la informacion
		q("update cliente set tipo_id='$tipo_id',nombre='$nombre',apellido='$apellido',lugar_expdoc='$lugar_expdoc',pais='$pais',ciudad='$ciudad',barrio='$barrio',direccion='$direccion',
			telefono_oficina='$telefono_oficina',telefono_casa='$telefono_casa',celular='$celular',email_e='$email_e',email2_e='$email2_e',observaciones='$observaciones' where id=$Cliente");
	}
	else
	{
		// inserta el nuevo cliente
		$Cliente=q("insert into cliente (identificacion,tipo_id,nombre,apellido,lugar_expdoc,pais,ciudad,barrio,direccion,telefono_oficina,telefono_casa,celular,email_e,email2_e,observaciones) values
				('$identificacion','$tipo_id','$nombre','$apellido','$lugar_expdoc','$pais','$ciudad','$barrio','$direccion','$telefono_oficina','$telefono_casa','$celular','$email_e','$email2_e','$observaciones')");
		graba_bitacora('cliente','A',"$Cliente","Adiciona registro");
	}
	
	if($Cliente)
	{
		// dispara un formulario hacia la elaboracion del recibo de caja
		echo "<body><form action='zcartera.php' method='post' target='_self' name='forma' id='forma'>
			<input type='hidden' name='idcita' value='$idcita'>
			<input type='hidden' name='Cliente' value='$Cliente'>
			<input type='hidden' name='cuenta' value='$cuenta'>
			<input type='hidden' name='franqt' value='$franqt'>
			<input type='hidden' name='tipo' value='$tipo'>
			<input type='hidden' name='banco' value='$banco'>
			<input type='hidden' name='devol_ncuenta' value='$anombrede'>
			<input type='hidden' name='identificacion_devol' value='$iddevol'>
			<input type='hidden' name='Acc' value='nuevo_recibo_garantia_pide_datos'>
		</form>
		
		<script language='javascript'>document.forma.submit();</script>";
	}

}

function nuevo_recibo_garantia_pide_datos() // formulario del recibo de caja por garantia.
{
	global $Cliente,$idcita,$Hoy,$cuenta,$tipo,$banco,$franqt,$devol_ncuenta,$identificacion_devol;
	html("Recibo de Caja - Garantoa de Servicio"); // pinta las cabeceras html
	$Franquicia=qo("select * from franquisia_tarjeta where id=$franqt"); // trae los datos de las franquicias
	$Cita=qo("select * from cita_servicio where id=$idcita"); // trae los datos de la cita
	$Siniestro=qo("select * from siniestro where id=$Cita->siniestro"); // trae los datos del siniestro
	$Aseguradora=qo("select * from aseguradora where id=$Siniestro->aseguradora"); // trae los datos de la aseguradora
	// pinta el formulario
	echo "<script language='javascript'>
				function validar_recibo()
				{
					if(!alltrim(document.forma.autorizacion.value)) {alert('Falta el numero de autorizacion');document.forma.autorizacion.style.backgroundColor='ffffaa';document.forma.autorizacion.focus();return false;}
					document.forma.submit();
				}
		</script>
		<body'><script language='javascript'>centrar(800,600);</script>
		<form action='zcartera.php' method='post' target='_self' name='forma' id='forma'>
			<center>
				 <b style='font-size:16px;'>$Aseguradora->nombre</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='$Aseguradora->emblema_f' height=50 align='middle'>
				 <br /><b style='font-size:18px;color:880000;'>RECIBO DE CAJA</b>
			</center>
			<hr width='70%'>
			<table align='center'>
				<tr><td>Fecha:</td><td><input type='text' name='fecha' id='fecha' value='$Hoy' readonly size='20'></td>
					<td>Oficina</td><td><input type='hidden' name='oficina' value='$Cita->oficina'><input type='text' value='".qo1("select t_oficina($Cita->oficina)")."' size=40 readonly></td></tr>
				<tr><td>Recibido de:</td><td><input type='hidden' name='cliente' value='$Cliente'><input type='text' value='".qo1("select t_cliente($Cliente)")."' size=50 readonly></td><td>Valor</td>
						<td><input type='text' name='valor' class=numero size=10 maxlength=10 value='$Aseguradora->garantia' style='font-weight:bold;font-size:14px;'></td></tr>
				<tr ><td >Franquicia de la tarjeta:</td><td >".qo1("select nombre from franquisia_tarjeta where id=$franqt")."</td></tr>
				<tr><td>Concepto:</td><td colspan=3><input type='text' name='concepto' value='GARANTIA PARA SERVICIO DE ".strtoupper($Aseguradora->nombre_servicio).". SINIESTRO NUMERO $Siniestro->numero' size='100'></td></tr>
				<tr ><td >Nomero de Autorizacion</td><td ><input type='text' name='autorizacion' class='numero' value='".($Franquicia->tipo=='E'?$Cita->pre_autorizacion:"")."' size=10 maxlength=10></td></tr>
			</table><br /><br />
			<center>
				<input type='button' value='GRABAR RECIBO' onclick='validar_recibo();' style='font-weight:bold;height:30;width:240px;'>
				<input type='button' value='CANCELAR' onclick='window.close();void(null);' style='font-weight:bold;height:30;width:200px;'>
			</center>
			<input type='hidden' name='Acc' value='nuevo_recibo_garantia_grabar'>
			<input type='hidden' name='siniestro' value='$Cita->siniestro'>
			<input type='hidden' name='cita' value='$idcita'>
			<input type='hidden' name='cuenta' value='$cuenta'>
			<input type='hidden' name='tipo' value='$tipo'>
			<input type='hidden' name='banco' value='$banco'>
			<input type='hidden' name='franqt' value='$franqt'>
			<input type='hidden' name='devol_ncuenta' value='$devol_ncuenta'>
			<input type='hidden' name='identificacion_devol' value='$identificacion_devol'>
		</form>
		</body>";
}

function nuevo_recibo_garantia_grabar() // graba los datos del recibo de caja
{
	global $siniestro,$cita;
	global $fecha,$oficina,$cliente,$valor,$concepto,$USUARIO,$Nusuario,$Hoy,$Hoyl,$cuenta,$tipo,$banco,$autorizacion,$franqt,$devol_ncuenta,$identificacion_devol;
	$Cliente=qo("select * from cliente where id=$cliente"); // trae los datos del cliente
	$Consecutivo=qo1("select max(consecutivo) from recibo_caja where oficina=$oficina")+1; // calcula el nuevo consecutivo
	//  insercion de la autorizacion automotica
	$Autorizacion_id=q("insert into sin_autor (siniestro,nombre,identificacion,franquicia,valor,funcionario,estado,observaciones,num_autorizacion,fecha_solicitud,solicitado_por,fecha_proceso,procesado_por,
								devol_banco,devol_cuenta_banco,devol_ncuenta,identificacion_devol,devol_tipo_cuenta,email,banco)
		values ('$siniestro','$Cliente->nombre $Cliente->apellido','$Cliente->identificacion','$franqt','$valor','$Nusuario','A','Valor recibido en Tarjeta Debito','$autorizacion','$Hoyl','$Nusuario','$Hoyl','$Nusuario',
		'$banco','$cuenta','$devol_ncuenta','$identificacion_devol','$tipo','$Cliente->email_e','$banco')");

	$Tipo_tarjeta=qo1("select tipo from franquisia_tarjeta where id=$franqt");
	if($Tipo_tarjeta=='C') {$Campo_aut='tarjeta_credit_autor';$Campo_valor='tarjeta_credito';}
	if($Tipo_tarjeta=='D') {$Campo_aut='tarjeta_debito_autor';$Campo_valor='tarjeta_debito_valor';}
	if($Tipo_tarjeta=='E') {$Campo_aut='';$Campo_valor='efectivo';}
	// inserta el recibo de caja
	$idRecibo=q("insert into recibo_caja (oficina,fecha,consecutivo,cliente,valor,concepto,siniestro,$Campo_valor,autorizacion,capturado_por,garantia".($Campo_aut?",$Campo_aut":"").") values
		('$oficina','$fecha','$Consecutivo','$cliente','$valor',\"$concepto\",'$siniestro','$valor','$Autorizacion_id','$Nusuario','1' ".($Campo_aut?",'$autorizacion' ":"").") ");
	header("location:zcartera.php?Acc=imprimir_recibo&id=$idRecibo"); // dispara la impresion del recibo
}

function genera_pre_autorizacion() // genera un codigo de autorizacion para los recibos de caja de garantia, debido a que no son cruzados contra facturas
{
	global $id;
	$Pre_autorizacion=round(rand(100000,999999),0);
	q("update cita_servicio set pre_autorizacion='$Pre_autorizacion' where id=$id ");
	graba_bitacora('sin_autor','M',$id,'Genera pre-autorizacion');
	html();
	echo "<body><script language='javascript'>window.close();void(null);</script></body>";
}

function addobs() // formulario para adicion de observaciones a la factura
{
	global $id;
	$Fac=qo("select * from factura where id=$id");
	html("Factura $Fac->consecutivo .:. Adicion de Observaciones");
	echo "<script language='javascript'>
		function valida()
		{
			with(document.forma)
			{
				if(!alltrim(observaciones.value)) {alert('Debe escribir algo para poder adicionar las observaciones');observaciones.style.backgroundColor='ffffdd';return false;}
			}
			document.forma.submit();
		}
	</script>
	<body >
	<h3>Observaciones de Seguimiento Factura Nomero $Fac->consecutivo</h3>
	<form action='zcartera.php' method='post' target='_self' name='forma' id='forma'>
		Observaciones: <br />
		<textarea name='observaciones' style='font-family:arial;font-size:11px;' cols=80 rows=8></textarea><br />
		<br /><input type='button' value='Continuar' onclick='valida();'>
		<input type='hidden' name='Acc' value='addobs_ok'>
		<input type='hidden' name='id' value='$id'>
	</form>
	</body>";
}

function addobs_ok() // adicion de observaciones a la factura
{
	global $id,$observaciones,$Nusuario;
	$Ahora=date('Y-m-d H:i:s');
	q("update factura set observaciones=concat(observaciones,\"\n$Nusuario [$Ahora]: $observaciones\") where id=$id");
	graba_bitacora('factura','M',$id,'observaciones');
	echo "<body><script language='javascript'>alert('Observaciones registradas sastisfactoriamente');window.close();void(null);opener.parent.recargar();</script></body>";
}

function fija_titulo_superior($Titulos,$Caracteristicas_table,$Modo=1) // creacion de una capa o layer para fijar titulos en la pantalla que no se desplacen con un detalle
{
	if($Modo==1)
	{ echo "<div id='_capa_titulo_superior_' style='position:fixed;visibility:hidden'><table id='_Tabla__' $Caracteristicas_table><tr >";
		foreach($Titulos as $Campo=>$Etiqueta) {$Idc=$Campo.'_';echo "<th id='$Idc'>$Etiqueta</th>";}
		echo "</tr></table></div>"; }
	elseif($Modo==3)
	{ echo "<script language='javascript'>fija_ancho('_Tabla_',-1);";
		foreach($Titulos as $Campo=>$Etiqueta) {
			echo "fija_ancho('$Campo',1);";
		}
		echo "document.getElementById('_capa_titulo_superior_').style.visibility='visible';</script>";}
	else
	{ echo "<table id='_Tabla_' $Caracteristicas_table><tr >";
		foreach($Titulos as $Campo=>$Etiqueta) echo "<th id='$Campo'>$Etiqueta</th>";
		echo "</tr>"; }
	return true;
}

function modificar_nota_credito() // formulario para la elaboracion de una nota crodito.
{
	
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
	global $id;
	$idNota = $id;
	html('NOTA CREDITO'); // pinta las cabeceras html
	
	
	$notaCredito = qo(" select * from nota_credito where id = '$idNota' ");

	if($notaCredito->aprobada_por)
	{
		echo "<h3> La nota credito ya fue aprobada </h3>";
		//exit;
	}
	
	//print_r($notaCredito);	
	
	$Factura=qo(" select *,t_siniestro(siniestro) as nsiniestro from factura where id = '$notaCredito->factura' ");	
	
	if(!$Factura)
	{
		echo "<h3> No hay facturas relacionadas a estos datos </h3>";
		exit;
	}
		
	$Cliente=qo("select * from cliente where id=$Factura->cliente"); // trae los datos del cliente
	$Aseguradora=qo("select * from aseguradora where id=$Factura->aseguradora"); // trae los datos de la aseguradora
	
	/*echo "select * from facturad inner join concepto_fac on 
		facturad.concepto = concepto_fac.id
	where facturad.factura = ".$Factura->id;*/
	
	$result = q("select nota_creditod.* , concepto_fac.nombre, concepto_fac.porc_iva from nota_creditod inner join concepto_fac on 
		nota_creditod.concepto = concepto_fac.id where nota_credito = ".$idNota);
	
	//print_r($result);
	
	$itemsFactura = array();
	
	while($row = mysql_fetch_object($result))
	{
			$itemsFactura[] = $row;
	}
	
	for($i = 0 ;  $i <  count($itemsFactura) ; $i++ )
	{
		$sql = " select * from nota_creditod where facturad  = ".$itemsFactura[$i]->id." and nota_credito !=  ".$notaCredito->id;
		
		$result = q($sql);		
		
		while($row = mysql_fetch_object($result))
		{
			$itemsFactura[$i]->cantidad = $itemsFactura[$i]->cantidad - $row->cantidad;
			
			$itemsFactura[$i]->total = $itemsFactura[$i]->total - $row->total;			
		
		} 
		
	}
	
	foreach($itemsFactura as $key => $value )
	{
		if($value->cantidad == 0 || $value->total == 0)
		{		
			unset($itemsFactura[$key]);
		}
	}
	
	/*if (count($itemsFactura) == 0)
	{
		echo "<h3>No se pueden hacer mas notas credito o para la factura ".$Factura->consecutivo." </h3>";
		exit;
	}*/
	
	$Porcentaje_iva=round($Factura->iva/$Factura->subtotal*100,2); // calcula el iva
	
	include("/var/www/html/public_html/Control/operativo/views/subviews/facturacion/GenerarNotaCredito.php");
}

function elaborar_nota_credito() // formulario para la elaboracion de una nota crodito.
{
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
	global $idFac;
	html('NOTA CREDITO'); // pinta las cabeceras html
	
	if($_GET['idFac'])
	{
		$Factura=qo(" select *,t_siniestro(siniestro) as nsiniestro from factura where id=$idFac "); // trae los datos del la factura	
	}
	
	if($_GET['consFac'])
	{
		$consFac = $_GET['consFac'];
		
		$Factura=qo(" select *,t_siniestro(siniestro) as nsiniestro from factura where consecutivo = '$consFac' ");
	}
	
	if(!$Factura)
	{
		echo "<h3> No hay facturas relacionadas a estos datos </h3>";
		exit;
	}
		
	$Cliente=qo("select * from cliente where id=$Factura->cliente"); // trae los datos del cliente
	$Aseguradora=qo("select * from aseguradora where id=$Factura->aseguradora"); // trae los datos de la aseguradora
	
	/*echo "select * from facturad inner join concepto_fac on 
		facturad.concepto = concepto_fac.id
	where facturad.factura = ".$Factura->id;*/
	
	
/* 	echo "select facturad.* , concepto_fac.nombre, concepto_fac.porc_iva from facturad inner join concepto_fac on 
		facturad.concepto = concepto_fac.id
	where facturad.factura = $Factura->id"; */
	//print_r($result);
	$result = q("select facturad.* , concepto_fac.nombre, concepto_fac.porc_iva from facturad inner join concepto_fac on 
		facturad.concepto = concepto_fac.id
	where facturad.factura = $Factura->id");
	echo $Factura->id;
	$itemsFactura = array();
	
	while($row = mysql_fetch_object($result))
	{
			$itemsFactura[] = $row;
	}
	
	for($i = 0 ;  $i <  count($itemsFactura) ; $i++ )
	{
		$sql = " select * from nota_creditod where facturad  = ".$itemsFactura[$i]->id;
		
		$result = q($sql);	
		
		
		while($row = mysql_fetch_object($result))
		{
			$itemsFactura[$i]->cantidad = $itemsFactura[$i]->cantidad - $row->cantidad;
			
			$itemsFactura[$i]->total = $itemsFactura[$i]->total - $row->total;			
		
		} 
		
	}
	
	foreach($itemsFactura as $key => $value )
	{
		if($value->cantidad == 0 || $value->total == 0)
		{		
			unset($itemsFactura[$key]);
		}
	}
	$saldo = 0;
	echo count($itemsFactura);
	//if (count($itemsFactura) == 0)
	//{
		
	
		$resulfac = qo("select total, iva from factura where id=$Factura->id ");
		$resul = qo("select sum(total) as total, sum(valor_iva) AS tiva from nota_credito where factura=$Factura->id ");
		
		$saldo_nc = $resulfac->total - $resul->total;
		$saldo_iva = $resulfac->iva - $resul->tiva;

		$diferencia_nc = $resul->total;
		$diferencia_iva = $resul->tiva;
		if($resul->total == $resulfac->total){
			echo "<h3>No se pueden hacer mas notas credito:".$saldo_nc.' =='. $resulfac->total.' Fact:'.$Factura->id;
			exit;
			
		}else{
			echo "<h3>Datos::".$saldo_nc.' =='. $resulfac->total.' Fact:'.$Factura->id;
			$result = q("select facturad.* , concepto_fac.nombre, concepto_fac.porc_iva from facturad inner join concepto_fac on 
			facturad.concepto = concepto_fac.id
		where facturad.factura = $Factura->id");
		$itemsFactura = array();
		$saldo = $resul->total;
		while($row = mysql_fetch_object($result))
		{
				$itemsFactura[] = $row;
		}
				foreach($itemsFactura as $key => $value )
			{
				if($value->cantidad == 0 || $value->total == 0)
				{		
					unset($itemsFactura[$key]);
				}
			}
			echo "<h3>Puede realizar NC,  ".$Factura->consecutivo."  * Total NC = ".$resul->total.", saldo por hacer nota credito ".$saldo_nc.", saldo iva: ".$saldo_iva."</h3>";
		}

		
	//}
	
	$Porcentaje_iva=round($Factura->iva/$Factura->subtotal*100,2); // calcula el iva
	
	include("/var/www/html/public_html/Control/operativo/views/subviews/facturacion/GenerarNotaCredito_test.php");
}

function elaborar_nota_credito_ok() // graba los datos de la nota credito.
{
	global $detalles, $valorBruto, $baseIva, $valorIva, $totalNota , $fechaEmision, $idFac, $registradoPor, $descripcion, $idNota;
	
	
	
	
	if(count($detalles) == 0 )
	{
		echo json_encode(array("status"=>"error","desc"=>"No hay items asignados para la nota credito"));
		exit;	
	}
	
	
	//Validate	
	
	if($idNota)
	{
		//hay que borrarlos para que vuelva a ingresarlos de 0
		$sql = "delete from nota_creditod where nota_credito = '".$idNota."'";
		q($sql);
		
	}
	
	if($idNota)
	{
		$consecutivo = $idNota;
	}
	else{
		$consecutivo=qo1("select max(consecutivo) from nota_credito")+1;	
	}
		
	
	
	
	foreach($detalles as $detalle)
	{	
	
		//Get previous  nditems
		
		$sql = "select * from nota_creditod where facturad = '".$detalle["facturad"]."'";		
		
		$result = q($sql);	
		
		$quantityToCompare = 0;
		
		$totalToCompare = 0;
		
		while($row = mysql_fetch_object($result))
		{			
			//print_r($row);
			$quantityToCompare += (int) $row->cantidad;			
			$totalToCompare += (int) $row->total;
		}		
		
		$quantityToCompare += $detalle["cantidad"];
		
		$totalToCompare += $detalle["total"];	
		
		$facturad = qo("select facturad.* , concepto_fac.nombre, concepto_fac.porc_iva from facturad inner join concepto_fac on 
		facturad.concepto = concepto_fac.id	where facturad.id = ".$detalle["facturad"]);	
		
		$notas = qo("SELECT sum(total) as total FROM nota_credito WHERE factura= ".$facturad->factura);

		$resulfac = qo("select total from factura where id=$facturad->factura ");

		
		$saldo_nc = $resulfac->total - $notas->total;


		
		/* if( (int) $quantityToCompare > (int) $facturad->cantidad || (int) $totalToCompare > (int) $facturad->total )
		{			
			$messageQuantity = ((int) $quantityToCompare > (int) $facturad->cantidad) ? $facturad->nombre." La cantidad de datos ".(int) $quantityToCompare." supera a la cantidad del item ".(int) $facturad->cantidad : false; 
			
			$messageAmount = ((int) $totalToCompare > (int) $facturad->total) ? $facturad->nombre." El total ".(int) $totalToCompare." supera al total del item ".(int) $facturad->total : false;			
			
			echo json_encode(array("status"=>"error","desc"=>$messageQuantity." ".$messageAmount, "facturad"=>$detalle["facturad"]));
			
			exit;
		} */

		if( (int) $saldo_nc == 0 )
		{			
			$messageQuantity = "Ya no puedes generar mas Notas Credito"; 
			
			
			echo json_encode(array("status"=>"error","desc"=>$messageQuantity, "facturad"=>$detalle["facturad"]));
			
			exit;
		}
	
		$sql = "insert into nota_creditod (nota_credito,concepto,cantidad,unitario,total,facturad) 
		values (
			'$consecutivo',
			'".$detalle["concepto"]."',
			'".$detalle["cantidad"]."',
			'".$detalle["unitario"]."',
			'".$detalle["total"]."',
			'".$detalle["facturad"]."'
		) ";
		
		q($sql);
	}
	
	
	if($idNota)
	{
		$sql = "update nota_credito  set fecha = '$fechaEmision',
		consecutivo = '$consecutivo',
		descripcion = '$descripcion',
		valor_bruto = '$valorBruto',
		base_iva = '$baseIva',
		valor_iva = '$valorIva',
		total = '$totalNota',
		registrado_por = '$registrado_por'
		where id = '$idNota';
		";	
	}
	else{
		$sql = "insert into nota_credito (fecha,consecutivo,factura,descripcion,valor_bruto,base_iva,porcentaje_iva,valor_iva,total,registrado_por) 
		values ('$fechaEmision','$consecutivo','$idFac',\"$descripcion\",'$valorBruto','$baseIva',null,'$valorIva','$totalNota','$registrado_por') ";
	}
	
	q($sql);	
	
	
	
	echo json_encode(array("status"=>"success","desc"=>"Nota credito generada"));
	
}

function verificar_nota_credito_electronica()
{
	sesion();
	//echo "Sub interfaz de factura electronica";
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/views/factura_electronica/interfaz_nc.html");
}

function verificar_nota_debito_electronica()
{
	sesion();
	
	//echo "Sub interfaz de factura electronica";
	header('Content-Type: text/html; charset=utf-8');
	include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/views/factura_electronica/interfaz_nd.html");
}

function imprimir_ncontable() // imprime la nota contable en un documento en formato pdf
{
	global $id,$Hoyl;
	$Nota=qo("select * from nota_contable where id=$id"); // trae la informacion de la nota contable
	IF(!$Siniestro=qo("select * from siniestro where id=$Nota->siniestro")) $Siniestro=qo("select * from siniestro_hst where id=$Nota->siniestro"); // trae la informacion del siniestro
	$Oficina=qo("select * from oficina where ciudad=$Siniestro->ciudad"); // trae la infromacion de la oficina
	$Factura=qo("select * from factura where id=$Nota->factura"); // trae la informacion de la factura
	$Cliente=qo("select * from cliente where id=$Factura->cliente"); // trae la infromacion del cliente
	$Autorizacion=qo("select * from sin_autor where id=$Nota->autorizacion"); // trae la infromacion de la autorizacion
	$Franquicia=qo("select * from franquisia_tarjeta where id=$Autorizacion->franquicia"); // trae la informacion de la franquicia
	$Recibo=qo("select * from recibo_caja where id=$Nota->recibo_caja"); // trae la informacion del recibo de caja
	
	include('inc/pdf/fpdf.php');
	$P=new pdf('P','mm','letter'); // crea la instancia de la clase
	$P->AddFont("c128a","","c128a.php"); // adiciona fuentes de codigo de barras
	$P->AliasNbPages();
	$P->setTitle("NOTA CONTABLE");
	$P->setAuthor("Arturo Quintero www.aoacolombia.com arturoquintero@aoacolombia.com");
	$P->Numeracion=false;
	$P->SetAutoPageBreak(false);
	$P->setFillColor(250,250,250);
	$P->SetTopMargin('5');
	$P->AddPage('P'); // adidiona la pogina
	$ny=15;
	$P->Image('../img/LOGO_AOA_200.jpg',50,$ny,30,12);
	$P->SetXY(100,$ny);$P->SetTextColor(0,0,0);$P->setfont('Arial','B',10);$P->Cell(90,5,'ADMISTRACIoN OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
	$ny=$P->y+4;$P->setxy(100,$ny);$P->setfont('Arial','B',10);$P->Cell(90,5,'NIT.: 900.174.552-5',0,0,'C');
	$P->rect(110,$ny+5,70,14);
	$ny=$P->y+7;$P->setxy(120,$ny);$P->setfont('Arial','B',16);$P->Cell(80,5,'NOTA CONTABLE',0,0,'L');
	$ny=$P->y+2;$P->setxy(20,$ny);$P->setfont('Arial','',10);$P->cell(90,4,'Carrera 69B 98A-10 Bogoto D.C.',0,0,'C');
	$ny=$P->y+3;$P->setxy(130,$ny);$P->setfont('Times','B',16);$P->Cell(10,5,'No.',0,0,'L');$P->setfont('Arial','B',16);$P->settextcolor(0,0,0);
	$P->Cell(20,5,str_pad($Nota->consecutivo,6,'0',STR_PAD_LEFT),0,0,'L');$P->settextcolor(0,0,0);
	$ny=$P->y+1;$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(90,4,'Pbx: (057) 1 7560510 Fax (057) 1 7560512',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->cell(90,4,'www.aoacolombia.com',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->Cell(22,4,'Ciudad:',1,0,'L');$P->Cell(108,4,$Oficina->nombre,1,0,'L');$P->Cell(20,8,'Fecha:',1,0,'C');$P->Cell(30,8,$Nota->fecha,1,0,'C');
	$ny=$P->y+4;
	$P->setxy(20,$ny);
	if($Nota->siniestro)
	{
		$P->Cell(22,4,'Siniestro:',1,0,'L');
		$P->cell(108,4,$Siniestro->numero.' F.Autorizacion: '.$Siniestro->fec_autorizacion,1,0,'L');
	}
	else
	{
		$P->Cell(22,4,'',1,0,'L');
		$P->cell(108,4,' ',1,0,'L');
	}
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Cliente:',1,0,'L');$P->Cell(108,4,trim($Cliente->nombre.' '.$Cliente->apellido.' '.coma_format($Cliente->identificacion)),1,0,'L');
	$P->Cell(8,4,'$',1,0,'C');$P->Cell(42,4,coma_format($Nota->valor),1,0,'R',1);
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Direccion:',1,0,'L');$P->Cell(158,4,$Cliente->direccion,1,0,'L');
	$P->setxy(20,$P->y+4);$P->setfont('Arial','',6);$P->multicell(180,4,'En Letras: '.enletras($Nota->valor,1),1,'J',1);
	$Concepto="Nota Contable para la Factura Nomero ".str_pad($Factura->consecutivo,6,'0',STR_PAD_LEFT)." emitida por valor de ".coma_format($Factura->total). " el doa ".
	fecha_completa($Factura->fecha_emision).
	" Descontando del Recibo de Caja Nomero ".$Oficina->sigla.'-'.str_pad($Recibo->consecutivo,6,'0',STR_PAD_LEFT)." recibido en calidad de Constitucion de Garantoa el doa ".
	fecha_completa($Recibo->fecha)." Autorizacion interna Nomero: $Autorizacion->id a nombre de $Autorizacion->nombre con Nomero de Identificacion $Autorizacion->identificacion ".
	"Franquicia: $Franquicia->nombre.";
	$P->setxy(20,$P->y);$P->setfont('Arial','',8);$P->multicell(180,4,'Concepto: '.str_replace("\r\n","",$Concepto),1,'J');
	$ny=$P->y+4;
	$Creador=qo1("select nombre from app_bitacora where tabla='nota_contable' and accion='A' and registro=$id");
	$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(110,4,'Elaborado por: '.$Creador ,1,1,'L');$P->setxy(130,$ny);$P->cell(70,15,' ',1);
	$ny=$P->y+5;$P->setxy(20,$ny);$P->SetFont("c128a","",12);$P->cell(110,14, uccean128('FA'.str_pad($Factura->consecutivo,10,'0',STR_PAD_LEFT).str_pad($Nota->consecutivo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$ny=$P->y+10;$P->setxy(130,$ny);$P->setfont('Arial','',8);$P->cell(70,4,'Firma y sello',1,0,'L');
	$ny=$P->y+4;
	$P->setxy(100,$ny);$P->setfont('Arial','B',8);$P->cell(20,4,'ORIGINAL',0,0,'C');
	$P->setxy(170,$ny);$P->setfont('Arial','',6);$P->cell(30,4,$Hoyl,0,0,'R');
	///////////////////////////   SEGUNDO TRAFICO //////////////////////////////////////////////////////
	$ny=155;
	$P->Image('../img/LOGO_AOA_200.jpg',50,$ny,30,12);
	$P->SetXY(100,$ny);$P->SetTextColor(0,0,0);$P->setfont('Arial','B',10);$P->Cell(90,5,'ADMISTRACIoN OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
	$ny=$P->y+4;$P->setxy(100,$ny);$P->setfont('Arial','B',10);$P->Cell(90,5,'NIT.: 900.174.552-5',0,0,'C');
	$P->rect(110,$ny+5,70,14);
	$ny=$P->y+7;$P->setxy(120,$ny);$P->setfont('Arial','B',16);$P->Cell(80,5,'NOTA CONTABLE',0,0,'L');
	$ny=$P->y+2;$P->setxy(20,$ny);$P->setfont('Arial','',10);$P->cell(90,4,'Carrera 69B 98A-10 Bogoto D.C.',0,0,'C');
	$ny=$P->y+3;$P->setxy(130,$ny);$P->setfont('Times','B',16);$P->Cell(10,5,'No.',0,0,'L');$P->setfont('Arial','B',16);$P->settextcolor(0,0,0);
	$P->Cell(20,5,str_pad($Nota->consecutivo,6,'0',STR_PAD_LEFT),0,0,'L');$P->settextcolor(0,0,0);
	$ny=$P->y+1;$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(90,4,'Pbx: (057) 1 7560510 Fax (057) 1 7560512',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->cell(90,4,'www.aoacolombia.com',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->Cell(22,4,'Ciudad:',1,0,'L');$P->Cell(108,4,$Oficina->nombre,1,0,'L');$P->Cell(20,8,'Fecha:',1,0,'C');$P->Cell(30,8,$Nota->fecha,1,0,'C');
	$ny=$P->y+4;
	$P->setxy(20,$ny);
	if($Nota->siniestro)
	{
		$P->Cell(22,4,'Siniestro:',1,0,'L');
		$P->cell(108,4,$Siniestro->numero.' F.Autorizacion: '.$Siniestro->fec_autorizacion,1,0,'L');
	}
	else
	{
		$P->Cell(22,4,'',1,0,'L');
		$P->cell(108,4,' ',1,0,'L');
	}
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Cliente:',1,0,'L');$P->Cell(108,4,trim($Cliente->nombre.' '.$Cliente->apellido.' '.coma_format($Cliente->identificacion)),1,0,'L');
	$P->Cell(8,4,'$',1,0,'C');$P->Cell(42,4,coma_format($Nota->valor),1,0,'R',1);
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Direccion:',1,0,'L');$P->Cell(158,4,$Cliente->direccion,1,0,'L');
	$P->setxy(20,$P->y+4);$P->setfont('Arial','',6);$P->multicell(180,4,'En Letras: '.enletras($Nota->valor,1),1,'J',1);
	$Concepto="Nota Contable para la Factura Nomero ".str_pad($Factura->consecutivo,6,'0',STR_PAD_LEFT)." emitida por valor de ".coma_format($Factura->total). " el doa ".
	fecha_completa($Factura->fecha_emision).
	" Descontando del Recibo de Caja Nomero ".$Oficina->sigla.'-'.str_pad($Recibo->consecutivo,6,'0',STR_PAD_LEFT)." recibido en calidad de Constitucion de Garantoa el doa ".
	fecha_completa($Recibo->fecha)." Autorizacion interna Nomero: $Autorizacion->id a nombre de $Autorizacion->nombre con Nomero de Identificacion $Autorizacion->identificacion ".
	"Franquicia: $Franquicia->nombre.";
	$P->setxy(20,$P->y);$P->setfont('Arial','',8);$P->multicell(180,4,'Concepto: '.str_replace("\r\n","",$Concepto),1,'J');
	$ny=$P->y+4;
	$Creador=qo1("select nombre from app_bitacora where tabla='nota_contable' and accion='A' and registro=$id");
	$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(110,4,'Elaborado por: '.$Creador ,1,1,'L');$P->setxy(130,$ny);$P->cell(70,15,' ',1);
	$ny=$P->y+5;$P->setxy(20,$ny);$P->SetFont("c128a","",12);$P->cell(110,14, uccean128('FA'.str_pad($Factura->consecutivo,10,'0',STR_PAD_LEFT).str_pad($Nota->consecutivo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$ny=$P->y+10;$P->setxy(130,$ny);$P->setfont('Arial','',8);$P->cell(70,4,'Firma y sello',1,0,'L');
	$ny=$P->y+4;
	$P->setxy(100,$ny);$P->setfont('Arial','B',8);$P->cell(20,4,'ORIGINAL',0,0,'C');
	$P->setxy(170,$ny);$P->setfont('Arial','',6);$P->cell(30,4,$Hoyl,0,0,'R');

	$P->Output($Archivo); // presenta el archivo en un visor pdf del navegador o permite descargarlo
}

function imprimir_ncredito() // imprime la nota crodito en un documento en formato pdf
{
	global $id,$Hoyl;
	$Nota=qo("select * from nota_credito where id=$id"); // trae la informacion de la nota crodito
	$Factura=qo("select * from factura where id=$Nota->factura"); // trae la informacion de la factura
	if(!$Factura->siniestro) $Siniestro->ciudad='11001000'; // calcula la ciudad si no es siniestro, la deja por defecto en bogoto
	else { if(!$Siniestro=qo("select * from siniestro where id=$Factura->siniestro")) $Siniestro=qo("select * from siniestro_hst where id=$Factura->siniestro"); } // trae los datos del siniestro
	$Oficina=qo("select * from oficina where ciudad=$Siniestro->ciudad"); // trae los datos de la oficina
	$Cliente=qo("select * from cliente where id=$Factura->cliente"); // trae la informacion del cliente
	
	include('inc/pdf/fpdf.php');
	$P=new pdf('P','mm','letter'); // crea la instancia de la clase
	$P->AddFont("c128a","","c128a.php"); // adiciona fuentes para codigo de barras
	$P->AliasNbPages();
	$P->setTitle("NOTA CREDITO");
	$P->setAuthor("Arturo Quintero www.aoacolombia.com arturoquintero@aoacolombia.com");
	$P->Numeracion=false;
	$P->SetAutoPageBreak(false);
	$P->setFillColor(250,250,250);
	$P->SetTopMargin('5');
	$P->AddPage('P'); // adiciona la pagina
	$ny=15;
	$P->Image('../img/LOGO_AOA_200.jpg',50,$ny,30,12);
	$P->SetXY(100,$ny);$P->SetTextColor(0,0,0);$P->setfont('Arial','B',10);$P->Cell(90,5,'ADMISTRACIoN OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
	$ny=$P->y+4;$P->setxy(100,$ny);$P->setfont('Arial','B',10);$P->Cell(90,5,'NIT.: 900.174.552-5',0,0,'C');
	$P->rect(110,$ny+5,70,14);
	$ny=$P->y+7;$P->setxy(120,$ny);$P->setfont('Arial','B',16);$P->Cell(80,5,'NOTA CREDITO',0,0,'L');
	$ny=$P->y+2;$P->setxy(20,$ny);$P->setfont('Arial','',10);$P->cell(90,4,'Carrera 69B 98A-10 Bogoto D.C.',0,0,'C');
	$ny=$P->y+3;$P->setxy(130,$ny);$P->setfont('Times','B',16);$P->Cell(10,5,'No.',0,0,'L');$P->setfont('Arial','B',16);$P->settextcolor(0,0,0);
	$P->Cell(20,5,str_pad($Nota->consecutivo,6,'0',STR_PAD_LEFT),0,0,'L');$P->settextcolor(0,0,0);
	$ny=$P->y+1;$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(90,4,'Pbx: (057) 1 7560510 Fax (057) 1 7560512',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->cell(90,4,'www.aoacolombia.com',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->Cell(22,4,'Ciudad:',1,0,'L');$P->Cell(108,4,$Oficina->nombre,1,0,'L');$P->Cell(20,8,'Fecha:',1,0,'C');$P->Cell(30,8,$Nota->fecha,1,0,'C');
	$ny=$P->y+4;
	$P->setxy(20,$ny);
	if($Nota->siniestro)
	{ $P->Cell(22,4,'Siniestro:',1,0,'L'); $P->cell(108,4,$Siniestro->numero.' F.Autorizacion: '.$Siniestro->fec_autorizacion,1,0,'L'); }
	else { $P->Cell(22,4,'',1,0,'L'); $P->cell(108,4,' ',1,0,'L'); }
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Cliente:',1,0,'L');$P->Cell(108,4,trim($Cliente->nombre.' '.$Cliente->apellido.' '.coma_format($Cliente->identificacion)),1,0,'L');
	$P->Cell(8,4,'$',1,0,'C');$P->Cell(42,4,coma_format($Nota->total),1,0,'R',1);
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Direccion:',1,0,'L');$P->Cell(158,4,$Cliente->direccion,1,0,'L');
	$P->setxy(20,$P->y+4);$P->setfont('Arial','',6);$P->multicell(180,4,'En Letras: '.enletras($Nota->total,1),1,'J',1);
	$Concepto="Nota Credito para la Factura Nomero ".str_pad($Factura->consecutivo,6,'0',STR_PAD_LEFT)." emitida por valor de ".coma_format($Factura->total). " el doa ".
	fecha_completa($Factura->fecha_emision)." Descripcion: $Nota->descripcion";
	$P->setxy(20,$P->y);$P->setfont('Arial','',8);$P->multicell(180,4,'Concepto: '.str_replace("\r\n","",$Concepto),1,'J');
	$ny=$P->y+2;
	$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(20,4,'Valor Bruto: ',1,0,'L');$P->cell(23,4,coma_format($Nota->valor_bruto),1,0,'R');
	$P->cell(20,4,'Base Iva:',1,0,'L');$P->cell(23,4,coma_format($Nota->base_iva),1,0,'R');
	$P->cell(10,4,'% Iva:',1,0,'L');$P->cell(10,4,$Nota->porcentaje_iva,1,0,'R');
	$P->cell(20,4,'Valor Iva:',1,0,'L');$P->cell(20,4,coma_format($Nota->valor_iva),1,0,'R');
	$P->cell(10,4,'Total:',1,0,'L');$P->cell(24,4,coma_format($Nota->total),1,0,'R');
	$ny=$P->y+6;
	$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(110,4,'Elaborado por: '.$Nota->registrado_por ,1,1,'L');$P->setxy(130,$ny);$P->cell(70,15,' ',1);
	$ny=$P->y+5;$P->setxy(20,$ny);$P->SetFont("c128a","",12);$P->cell(110,14, uccean128('FA'.str_pad($Factura->consecutivo,10,'0',STR_PAD_LEFT).str_pad($Nota->consecutivo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$ny=$P->y+10;$P->setxy(130,$ny);$P->setfont('Arial','',8);$P->cell(70,4,'Firma y sello',1,0,'L');
	$ny=$P->y+4;
	$P->setxy(100,$ny);$P->setfont('Arial','B',8);$P->cell(20,4,'ORIGINAL',0,0,'C');
	$P->setxy(170,$ny);$P->setfont('Arial','',6);$P->cell(30,4,$Hoyl,0,0,'R');
	///////////////////////////   SEGUNDO TRAFICO //////////////////////////////////////////////////////
	$ny=155;
	$P->Image('../img/LOGO_AOA_200.jpg',50,$ny,30,12);
	$P->SetXY(100,$ny);$P->SetTextColor(0,0,0);$P->setfont('Arial','B',10);$P->Cell(90,5,'ADMISTRACIoN OPERATIVA AUTOMOTRIZ S.A.',0,0,'C');
	$ny=$P->y+4;$P->setxy(100,$ny);$P->setfont('Arial','B',10);$P->Cell(90,5,'NIT.: 900.174.552-5',0,0,'C');
	$P->rect(110,$ny+5,70,14);
	$ny=$P->y+7;$P->setxy(120,$ny);$P->setfont('Arial','B',16);$P->Cell(80,5,'NOTA CREDITO',0,0,'L');
	$ny=$P->y+2;$P->setxy(20,$ny);$P->setfont('Arial','',10);$P->cell(90,4,'Carrera 69B 98A-10 Bogoto D.C.',0,0,'C');
	$ny=$P->y+3;$P->setxy(130,$ny);$P->setfont('Times','B',16);$P->Cell(10,5,'No.',0,0,'L');$P->setfont('Arial','B',16);$P->settextcolor(0,0,0);
	$P->Cell(20,5,str_pad($Nota->consecutivo,6,'0',STR_PAD_LEFT),0,0,'L');$P->settextcolor(0,0,0);
	$ny=$P->y+1;$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(90,4,'Pbx: (057) 1 7560510 Fax (057) 1 7560512',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->cell(90,4,'www.aoacolombia.com',0,0,'C');
	$ny=$P->y+4;$P->setxy(20,$ny);$P->Cell(22,4,'Ciudad:',1,0,'L');$P->Cell(108,4,$Oficina->nombre,1,0,'L');$P->Cell(20,8,'Fecha:',1,0,'C');$P->Cell(30,8,$Nota->fecha,1,0,'C');
	$ny=$P->y+4;
	$P->setxy(20,$ny);
	if($Nota->siniestro)
	{ $P->Cell(22,4,'Siniestro:',1,0,'L'); $P->cell(108,4,$Siniestro->numero.' F.Autorizacion: '.$Siniestro->fec_autorizacion,1,0,'L'); }
	else { $P->Cell(22,4,'',1,0,'L'); $P->cell(108,4,' ',1,0,'L'); }
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Cliente:',1,0,'L');$P->Cell(108,4,trim($Cliente->nombre.' '.$Cliente->apellido.' '.coma_format($Cliente->identificacion)),1,0,'L');
	$P->Cell(8,4,'$',1,0,'C');$P->Cell(42,4,coma_format($Nota->total),1,0,'R',1);
	$P->setxy(20,$P->y+4);$P->Cell(22,4,'Direccion:',1,0,'L');$P->Cell(158,4,$Cliente->direccion,1,0,'L');
	$P->setxy(20,$P->y+4);$P->setfont('Arial','',6);$P->multicell(180,4,'En Letras: '.enletras($Nota->total,1),1,'J',1);
	$Concepto="Nota Credito para la Factura Nomero ".str_pad($Factura->consecutivo,6,'0',STR_PAD_LEFT)." emitida por valor de ".coma_format($Factura->total). " el doa ".
	fecha_completa($Factura->fecha_emision)." Descripcion: $Nota->descripcion";
	$P->setxy(20,$P->y);$P->setfont('Arial','',8);$P->multicell(180,4,'Concepto: '.str_replace("\r\n","",$Concepto),1,'J');
	$ny=$P->y+2;
	$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(20,4,'Valor Bruto: ',1,0,'L');$P->cell(23,4,coma_format($Nota->valor_bruto),1,0,'R');
	$P->cell(20,4,'Base Iva:',1,0,'L');$P->cell(23,4,coma_format($Nota->base_iva),1,0,'R');
	$P->cell(10,4,'% Iva:',1,0,'L');$P->cell(10,4,$Nota->porcentaje_iva,1,0,'R');
	$P->cell(20,4,'Valor Iva:',1,0,'L');$P->cell(20,4,coma_format($Nota->valor_iva),1,0,'R');
	$P->cell(10,4,'Total:',1,0,'L');$P->cell(24,4,coma_format($Nota->total),1,0,'R');
	$ny=$P->y+6;
	$P->setxy(20,$ny);$P->setfont('Arial','',8);$P->cell(110,4,'Elaborado por: '.$Nota->registrado_por ,1,1,'L');$P->setxy(130,$ny);$P->cell(70,15,' ',1);
	$ny=$P->y+5;$P->setxy(20,$ny);$P->SetFont("c128a","",12);$P->cell(110,14, uccean128('FA'.str_pad($Factura->consecutivo,10,'0',STR_PAD_LEFT).str_pad($Nota->consecutivo,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$ny=$P->y+10;$P->setxy(130,$ny);$P->setfont('Arial','',8);$P->cell(70,4,'Firma y sello',1,0,'L');
	$ny=$P->y+4;
	$P->setxy(100,$ny);$P->setfont('Arial','B',8);$P->cell(20,4,'ORIGINAL',0,0,'C');
	$P->setxy(170,$ny);$P->setfont('Arial','',6);$P->cell(30,4,$Hoyl,0,0,'R');

	$P->Output($Archivo); // presenta el archivo en un visor pdf del navegador o permite descargarlo
}




?>