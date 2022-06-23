<?php
	
    session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include_once(dirname(__FILE__).'/../../config/config.php');
	include_once(dirname(__FILE__).'/../../config/resuelve.php');
	header('Content-Type: application/json');
	
	$request_body = file_get_contents('php://input');
	if($request_body)
	{
	
		$request = json_decode($request_body);	
		$report = new ReportController();			
		$name = $request->name; 
		$report->$name($request);
	}
	else
	{
		$report = new ReportController();		
		$report->reporte_requisiciones_fechas(null);
		$report->reporte_administrativo_requicision(null);
		$report->reporte_caja_menor(null);
		$report->reporte_requicision_administrativo(null);
		$report->reporte_requisicion_facturas(null);
		$report->reporte_extras_extencion(null);
		$report->reporte_pqrs(null);
		$report->reporte_info_cartera(null);	
	}
	
	class ReportController
	{
		public function __construct()
		{
			
		}
		
		private function query($cadena, $Devolver_sql = 0,&$_Cantidad_registros_afectados=0) // corre un query invocado internamente
		{
			global $Nombre, $Id_alterno, $Num_Tabla,$LINK;
			
			

			if(!$LINK = mysql_connect(MYSQL_S, resuelve_usuario_mysql($cadena), MYSQL_P)) die('Problemas con la conexion de la base de datos!');
			mysql_query('SET collation_connection = utf8_general_ci',$LINK);
			if(!mysql_select_db(MYSQL_D, $LINK)) die('Problemas con la seleccion de la base de datos');
			if(strpos(' '.$cadena,'update ') || strpos(' '.$cadena,'alter table') || strpos(' '.$cadena,'insert '))
				mysql_query("set innodb_lock_wait_timeout=80",$LINK);
			else
				mysql_query("set innodb_lock_wait_timeout=20",$LINK);
			if($RQ = mysql_query($cadena, $LINK))
			{
				
				//print_r($RQ);				
				if($Devolver_sql)
				{
					mysql_close($LINK);
					return $RQ;
				}
				if(strpos(' ' . strtolower($cadena), 'insert '))
				{
					$IDR = mysql_insert_id($LINK);
					$_Cantidad_registros_afectados=mysql_affected_rows($LINK);
					mysql_close($LINK);
					return $IDR;
				}
				if(strpos(' ' . strtolower($cadena), 'update '))
				{
					$AFECTADAS = mysql_affected_rows($LINK);
					mysql_close($LINK);
					return $AFECTADAS;
				}
				if(strpos(' ' . strtolower($cadena), 'create'))
				{
					$_Cantidad_registros_afectados=mysql_affected_rows($LINK);
					mysql_close($LINK);
					return true;
				}
				if((strpos(' ' . strtolower($cadena), 'select ') || strpos(' ' . strtolower($cadena), 'show ') || strpos(' ' . strtolower($cadena), 'analyze ') || strpos(' ' . strtolower($cadena), 'check ') || strpos(' ' . strtolower($cadena), 'optimize ') || strpos(' ' . strtolower($cadena), 'repair ')
							) && (!strpos(' ' . strtolower($cadena), 'insert ') || !strpos(' ' . strtolower($cadena), 'update ')|| !strpos(' ' . strtolower($cadena), 'create ')))
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
				
				echo $cadena;
				
				echo "<br>";
				
				echo $Error_de_mysql;
				
				mysql_close($LINK);
				if(strpos(' ' . $Error_de_mysql, 'Duplicate entry'))
				{
					//html();
					echo "<h3>Entrada Duplicada, no se pudo ingresar el nuevo registro</h3><script language='javascript'>alert('ENTRADA DUPLICADA, el registro no se pudo modificar o guardar.');</script>Debe ";
					if($Num_Tabla)
					{
						echo "<a href='javascript:oculta_edicion($Num_Tabla,false);'>cerrar esta ventana</a> e intentarlo nuevamente.";
					}
					else
						echo "<a href='javascript:window.close();void(null);'>cerrar esta ventana</a> e intentarlo nuevamente.";
					die();
				}
				elseif(strpos(' '.$Error_de_mysql,'Lock wait timeout exceeded') && strpos(' '.$cadena,'update') )
				{
					q($cadena);
				}
				
			}
		}
		
   private function sanear_string($string) {
    $string = trim($string);

    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );

    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );

    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );

    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );

    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );

    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );

    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "< ", ";", ",", ":",
             "."),
        '',
        $string
    );


    return $string;
}
		public function reporte_administrativo_requicision($request)
		{	//"Select  re.id as re_id, t_proveedor(proveedor) as proveedor, t_ciudad(re.ciudad) as ciudad, es.nombre as estadoreq, re.fecha as re_fecha, t_provee_produc_serv(tipo1) as tipo_servicio, case tipo_cobro when 'S' then 'SIN RECOBRO' when 'C' then 'CON RECOBRO' when 'N' then 'NO APLICA' end as tipo_cobro, re.factura_referencia as factura_referencia, t_factura(factura_proveedor) as fact_prov, re.placa as placa, IF(sin.ubicacion = 0, null, sin.numero) as siniestro_numero From estado_requisicion es, requisicion re, requisiciond rd, aoacol_aoacars.siniestro sin Where re.estado = es.id and rd.requisicion = re.id and re.fecha between '2018-06-27 00:00:00' and '2018-06-28 23:59:59' LIMIT 1500 "
			if(isset($request->factura_proveedor))
			{
				$interval = "'" .$request->factura_proveedor."'  ";
			}
			else
			{	
				$interval = " 12138";
			}
			
			$sql = "select provee_produc_serv.nombre as item,tipo.nombre as tipo,unidad_de_medida.nombre as unidad_medida,requisiciond.id,requisiciond.factura_proveedor,requisiciond.factura_proveedor,requisiciond.observaciones,requisiciond.cantidad,
                    requisiciond.requisicion,requisiciond.valor_total,requisiciond.valor as valor_unitario,requisiciond.factor,aoacol_aoacars.vehiculo.placa,
                    aoacol_aoacars.oficina.nombre as centrodeoperacion,requisiciond.centro_costo as centrocosto 
					from aoacol_administra.requisiciond
					inner join aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					inner join aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					inner join aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
                    LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id
                    LEFT OUTER JOIN aoacol_aoacars.oficina on requisiciond.centro_operacion = aoacol_aoacars.oficina.id
                    LEFT OUTER JOIN requisicionc on requisiciond.clase = requisicionc.id Where requisiciond.factura_proveedor  ";
			
			$reporte = $this->fetch_objects($sql);			
			
			
		
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		}
		
		public function reporte_requisiciones_fechas($request)
		{	//"Select  re.id as re_id, t_proveedor(proveedor) as proveedor, t_ciudad(re.ciudad) as ciudad, es.nombre as estadoreq, re.fecha as re_fecha, t_provee_produc_serv(tipo1) as tipo_servicio, case tipo_cobro when 'S' then 'SIN RECOBRO' when 'C' then 'CON RECOBRO' when 'N' then 'NO APLICA' end as tipo_cobro, re.factura_referencia as factura_referencia, t_factura(factura_proveedor) as fact_prov, re.placa as placa, IF(sin.ubicacion = 0, null, sin.numero) as siniestro_numero From estado_requisicion es, requisicion re, requisiciond rd, aoacol_aoacars.siniestro sin Where re.estado = es.id and rd.requisicion = re.id and re.fecha between '2018-06-27 00:00:00' and '2018-06-28 23:59:59' LIMIT 1500 "
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = " CURDATE() - INTERVAL 1 DAY and CURDATE() + INTERVAL 1 DAY";
			}
			
			$sql = "Select  re.id as re_id, re.ubicacion as ubicacion,t_proveedor(proveedor) as proveedor, t_ciudad(re.ciudad) as ciudad, es.nombre as estadoreq, re.fecha as re_fecha, t_provee_produc_serv(tipo1) as tipo_servicio, case tipo_cobro when 'S' then 'SIN RECOBRO' when 'C' then 'CON RECOBRO' when 'N' then 'NO APLICA' end as tipo_cobro, re.factura_referencia as factura_referencia, t_factura(factura_proveedor) as fact_prov, re.placa as placa , sin.numero From estado_requisicion as es inner join requisicion as re ON re.estado = es.id inner join requisiciond as rd on rd.requisicion = re.id left join aoacol_aoacars.siniestro as sin on sin.ubicacion = CASE WHEN re.ubicacion = 0 THEN null ELSE re.ubicacion END  Where re.fecha between ".$interval.";";
			
			$reporte = $this->fetch_objects($sql);			
			
			
		
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		}
		
		
		
		public function reporte_caja_menor($request)
		{	
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = " CURDATE() - INTERVAL 1 DAY and CURDATE() + INTERVAL 1 DAY";
			}
			
			$sql = "select requisicion.fecha as FECHA,requisiciond.requisicion as REQUISICION,ciudad.nombre as CIUDAD,provee_produc_serv.nombre as ITEM, requisicion.placa as PLACA,requisiciond.observaciones as OBSERVACIONES_O_COMENTARIOS,
					requisiciond.valor as VALOR_UNITARIO,requisiciond.cantidad as CANTIDAD,requisiciond.valor_total AS VALOR_TOTAL,
					case requisicion.cerrada when 0 then 'NO' when 1 then 
					'SI' end as CERRADA,t_estado_requisicion(requisicion.estado) as ESTADO, ubicacion.id as id_ubicacion, requisicion.observaciones as observaciones,
					requisiciond.tipo_cobro , requisiciond.factura_proveedor,
					concat( oficina.centro_operacion,'  ',oficina.nombre) as CENTRO_OP,aseguradora.ccostos_uno as CENTRO_COSTOS,aseguradora.nombre as ASEGURADORA,t_ubica(ubicacion) as UBICACION, 
					tipo.nombre as TIPO_REQUISICION,unidad_de_medida.nombre as UNIDAD_MEDIDA,p.identificacion AS DOCU_PROVEEDOR,p.td as TIPO_DOC_PROVEEDOR, p.nombre AS NOMBRE_PROVEEDOR,sistema.nombre as SISTEMA,ubicacion.odometro_inicial as KM_INI,ubicacion.odometro_final AS KM_FINAL, mar.nombre MARCA,lin.nombre LINEA,
					requisiciond.consecutivo_suno as CONSECUTIVO_UNO, requisiciond.consecutivo_provee AS CONSECUTIVO_PROVEEDOR, requisiciond.valor_factura AS 
                      VALOR_FACTURA, (SELECT a.factura_referencia FROM aoacol_administra.facturas_venta_requisicion a, aoacol_aoacars.factura b WHERE a.factura_referencia=b.consecutivo AND a.requisicion=requisicion.id limit 1) as FACTURAS,
                      (SELECT b.siniestro FROM aoacol_administra.facturas_venta_requisicion a, aoacol_aoacars.factura b WHERE a.factura_referencia=b.consecutivo AND a.requisicion=requisicion.id limit 1) as SINIESTROS
				from aoacol_administra.requisiciond
					LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					LEFT OUTER JOIN aoacol_administra.sistema on provee_produc_serv.sistema = sistema.id
					LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
					LEFT OUTER JOIN requisicionc on requisiciond.clase = requisicionc.id
					LEFT OUTER JOIN aoacol_administra.requisicion ON requisiciond.requisicion = requisicion.id
					 LEFT OUTER JOIN aoacol_aoacars.requisiciond_facturas on requisiciond.id = requisiciond_facturas.requisiciond 
					LEFT OUTER JOIN aoacol_administra.ciudad on requisicion.ciudad = ciudad.codigo
					LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
					LEFT OUTER JOIN aoacol_administra.proveedor p  on requisicion.proveedor = p.id
					INNER JOIN aoacol_aoacars.oficina on  ubicacion.oficina = oficina.id
                    LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id
					LEFT OUTER JOIN aoacol_aoacars.vehiculo vehi on ubicacion.vehiculo = vehi.id
                    LEFT OUTER JOIN aoacol_aoacars.linea_vehiculo lin on vehi.linea = lin.id
					LEFT OUTER JOIN aoacol_aoacars.marca_vehiculo mar on lin.marca = mar.id
					WHERE date_format(requisicion.fecha,'%Y-%m-%d') between ".$interval." order by REQUISICION;";
		   //echo $sql;
		     
			 
			 
		        $reporte = $this->fetch_objects_test($sql);
			
			foreach($reporte as $ireporte)
			{
				$ireporte->OBSERVACIONES_O_COMENTARIOS = $this->sanear_string(utf8_encode($ireporte->OBSERVACIONES_O_COMENTARIOS));
				$ireporte->ITEM = utf8_encode($ireporte->ITEM);
				$ireporte->CIUDAD = utf8_encode($ireporte->CIUDAD);
				(!$ireporte->CENTRO_OP) ? $ireporte->CENTRO_OP = 'NACIONAL'  : $ireporte->CENTRO_OP = utf8_encode($ireporte->CENTRO_OP) ; 
				$ireporte->VALOR_UNITARIO = "$".($ireporte->VALOR_UNITARIO);
				$ireporte->VALOR_TOTAL = "$".($ireporte->VALOR_TOTAL);
				$ireporte->observaciones =  ""; //$this->sanear_string(utf8_encode($ireporte->observaciones));
				
				
                        if($ireporte->tipo_cobro=='S') $ireporte->tipo_cobro='SIN RECOBRO';
                        if($ireporte->tipo_cobro=='C') $ireporte->tipo_cobro='CON RECOBRO';
                        if($ireporte->tipo_cobro=='N') $ireporte->tipo_cobro='NO APLICA';
						if($ireporte->tipo_cobro=='P') $ireporte->tipo_cobro='SIN RECOBRO PROTECCION TOTAL';
                        if($ireporte->tipo_cobro=='G') $ireporte->tipo_cobro='SIN RECOBRO GARANTIA NO REEMBOLSABLE';
                        if($ireporte->tipo_cobro=='A') $ireporte->tipo_cobro='SIN RECOBRO ASUME ASESOR';
						
				
			}
			
			//print_r($reporte);
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
			
		}
		
		public function reporte_consulta_siniestro($request){
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = "CURDATE() - INTERVAL 2 MONTH and CURDATE() + INTERVAL 2 MONTH";
			}
			
			$sql = "SELECT a.nombre ASEGURADORA,s.numero NUMERO_SINIESTRO,s.poliza POLIZA,s.placa PLACA,
			o.nombre CIUDAD,s.fec_autorizacion FECHA_AUTORIZACION,s.ingreso FECHA_INGRESO,es.nombre ESTADO,
			s.declarante_nombre DECLARANTE_NOMBRE,ca.nombre CAUSAL,s.info_erronea INFO_ERRONEA,
			s.declarante_celular DECLARANTE_CELULAR,cita.dias_servicio DIAS_SERVICIO ,veh.placa PLACA_UBICACION,
			u.fecha_inicial FEC_INICIAL_UBICACION ,u.fecha_final FECHA_FINAL_UBICACION,est.nombre NOMBRE_ESTADO_UBICACION
			,a.nombre_servicio NOMBRE_SERVICIO,s.tipo_caja as TIPO_CAJA_POR_CALLCENTER
			 FROM aoacol_aoacars.siniestro s
			 LEFT JOIN aoacol_aoacars.cita_servicio cita on s.id = cita.siniestro
			 LEFT JOIN aoacol_aoacars.ubicacion u ON s.ubicacion = u.id
			 LEFT JOIN aoacol_aoacars.vehiculo veh on u.vehiculo = veh.id
			 LEFT JOIN aoacol_aoacars.estado_vehiculo est ON u.estado = est.id
			 LEFT JOIN aoacol_aoacars.oficina ofi ON u.oficina = ofi.id
			 LEFT JOIN aoacol_aoacars.aseguradora a ON s.aseguradora = a.id
			 LEFT JOIN aoacol_aoacars.ciudad o ON s.ciudad = o.codigo
			 LEFT JOIN aoacol_aoacars.estado_siniestro es ON s.estado = es.id
			 LEFT JOIN aoacol_aoacars.causal ca ON s.causal = ca.id
			 where date_format(s.ingreso,'%Y-%m-%d') between ".$interval." order by s.id desc";
			
			//echo $sql;
			
			$reporte = $this->fetch_objects_test($sql);
			
			foreach($reporte as $ireporte){
				$ireporte->DECLARANTE_NOMBRE = str_replace(",","",utf8_encode($ireporte->DECLARANTE_NOMBRE));
				$ireporte->ASEGURADORA = str_replace(",","",utf8_encode($ireporte->ASEGURADORA));
				
			}
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		}
		public function informe_facturacion($request){
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = "CURDATE() - INTERVAL 2 MONTH and CURDATE() + INTERVAL 2 MONTH";
			}
			
			$sql = "SELECT fact.id ID_FACTURA,fact.subtotal VALOR_ANTES_IVA,fact.iva IVA,ase.nombre ASEGURADORA,
					sini.numero NUMERO_SINIESTRO,facd.descripcion DESCRIPCION,concep.nombre CONCEPTO, 
					fact.fecha_emision FECHA_EMICION, fact.consecutivo CONSECUTIVO,ciu.nombre CIUDAD,reca.concepto RESIVO_CAJA
					FROM aoacol_aoacars.factura fact 
					LEFT JOIN aoacol_aoacars.siniestro sini ON fact.siniestro = sini.id
					LEFT JOIN aoacol_aoacars.aseguradora ase ON fact.aseguradora = ase.id
					LEFT JOIN aoacol_aoacars.facturad facd ON fact.id = facd.factura
					LEFT JOIN aoacol_aoacars.concepto_fac concep ON facd.concepto = concep.id
					LEFT JOIN aoacol_aoacars.ciudad ciu ON sini.ciudad = ciu.codigo
					LEFT JOIN aoacol_aoacars.recibo_caja reca ON fact.id = reca.factura
					where date_format(fact.fecha_emision,'%Y-%m-%d') between ".$interval." order by fact.id desc";
			
			//echo $sql;
			
			$reporte = $this->fetch_objects_test($sql);
			
			foreach($reporte as $ireporte){
				$ireporte->CONCEPTO = str_replace(",","",utf8_encode($ireporte->CONCEPTO));
				$ireporte->DESCRIPCION = str_replace(",","",utf8_encode($ireporte->DESCRIPCION));
				
			}
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		}
		
		public function informe_encuestas($request){
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = "CURDATE() - INTERVAL 2 MONTH and CURDATE() + INTERVAL 2 MONTH";
			}
			
			$sql = "SELECT fact.id ID_FACTURA,fact.subtotal VALOR_ANTES_IVA,fact.iva IVA,ase.nombre ASEGURADORA,
					sini.numero NUMERO_SINIESTRO,facd.descripcion DESCRIPCION,concep.nombre CONCEPTO, 
					fact.fecha_emision FECHA_EMICION, fact.consecutivo CONSECUTIVO,ciu.nombre CIUDAD,reca.concepto RESIVO_CAJA
					FROM aoacol_aoacars.factura fact 
					LEFT JOIN aoacol_aoacars.siniestro sini ON fact.siniestro = sini.id
					LEFT JOIN aoacol_aoacars.aseguradora ase ON fact.aseguradora = ase.id
					LEFT JOIN aoacol_aoacars.facturad facd ON fact.id = facd.factura
					LEFT JOIN aoacol_aoacars.concepto_fac concep ON facd.concepto = concep.id
					LEFT JOIN aoacol_aoacars.ciudad ciu ON sini.ciudad = ciu.codigo
					LEFT JOIN aoacol_aoacars.recibo_caja reca ON fact.id = reca.factura
					where date_format(fact.fecha_emision,'%Y-%m-%d') between ".$interval." order by fact.id desc";
			
			//echo $sql;
			
			$reporte = $this->fetch_objects_test($sql);
			
			foreach($reporte as $ireporte){
				$ireporte->CONCEPTO = str_replace(",","",utf8_encode($ireporte->CONCEPTO));
				$ireporte->DESCRIPCION = str_replace(",","",utf8_encode($ireporte->DESCRIPCION));
				
			}
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		}
		
		public function informe_gestion_consultores($request){
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = "CURDATE() - INTERVAL 2 MONTH and CURDATE() + INTERVAL 2 MONTH";
			}
			
			$sql = "SELECT fact.id ID_FACTURA,fact.subtotal VALOR_ANTES_IVA,fact.iva IVA,ase.nombre ASEGURADORA,
					sini.numero NUMERO_SINIESTRO,facd.descripcion DESCRIPCION,concep.nombre CONCEPTO, 
					fact.fecha_emision FECHA_EMICION, fact.consecutivo CONSECUTIVO,ciu.nombre CIUDAD,reca.concepto RESIVO_CAJA
					FROM aoacol_aoacars.factura fact 
					LEFT JOIN aoacol_aoacars.siniestro sini ON fact.siniestro = sini.id
					LEFT JOIN aoacol_aoacars.aseguradora ase ON fact.aseguradora = ase.id
					LEFT JOIN aoacol_aoacars.facturad facd ON fact.id = facd.factura
					LEFT JOIN aoacol_aoacars.concepto_fac concep ON facd.concepto = concep.id
					LEFT JOIN aoacol_aoacars.ciudad ciu ON sini.ciudad = ciu.codigo
					LEFT JOIN aoacol_aoacars.recibo_caja reca ON fact.id = reca.factura
					where date_format(fact.fecha_emision,'%Y-%m-%d') between ".$interval." order by fact.id desc";
			
			//echo $sql;
			
			$reporte = $this->fetch_objects_test($sql);
			
			foreach($reporte as $ireporte){
				$ireporte->CONCEPTO = str_replace(",","",utf8_encode($ireporte->CONCEPTO));
				$ireporte->DESCRIPCION = str_replace(",","",utf8_encode($ireporte->DESCRIPCION));
				
			}
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		}
		
		public function informe_siniestro_modificado($request){
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = "CURDATE() - INTERVAL 2 MONTH and CURDATE() + INTERVAL 2 MONTH";
			}
			
			$sql = "SELECT s.id ID,s.numero NUMERO_SINIESTRO,s.placa PLACA_ASEGURADO,cita.dias_servicio DIAS_SERVICIO ,a.nombre ASEGURADORA,
					s.ingreso  GESTION,s.ingreso CONTACTO,
					o.nombre CIUDAD,s.declarante_nombre DECLARANTE_NOMBRE,s.declarante_celular DECLARANTE_CELULAR,
					s.declarante_email DECLARANTE_MAIL,veh.placa VEHICULO_ASIGNADO,
					veh.modelo MODELO_VEHICULO,clas.nombre CLASE,ti_ca.nombre AS TIPO_CAJA,
					s.tipo_caja as TIPO_CAJA_POR_CALLCENTER, s.sucursal_radicadora SUCURSAL_RADICADORA, estado.nombre ESTADO_SINIESTRO
					FROM aoacol_aoacars.cita_servicio cita
					LEFT JOIN aoacol_aoacars.siniestro s on  cita.siniestro = s.id
					LEFT JOIN aoacol_aoacars.estado_siniestro estado on s.estado = estado.id
					LEFT JOIN aoacol_aoacars.vehiculo veh on cita.placa = veh.placa
					LEFT JOIN aoacol_aoacars.clase_vehiculo clas ON veh.clase = clas.id
					LEFT JOIN aoacol_aoacars.vehiculo_tipo_caja ti_ca ON veh.tipo_caja = ti_ca.id
					LEFT JOIN aoacol_aoacars.aseguradora a ON s.aseguradora = a.id
					LEFT JOIN aoacol_aoacars.ciudad o ON s.ciudad = o.codigo
			        where date_format(s.ingreso,'%Y-%m-%d') between ".$interval." order by s.id desc";
				
			$reporte = $this->fetch_objects_test($sql);
			
			foreach($reporte as $ireporte){
				
				$ireporte->DECLARANTE_NOMBRE = str_replace(",","",utf8_encode($ireporte->DECLARANTE_NOMBRE));
				$ireporte->ASEGURADORA = str_replace(",","",utf8_encode($ireporte->ASEGURADORA));
				$sqlBusca = "SELECT CONCAT(fecha,' ',hora) AS GESTION
							FROM aoacol_aoacars.seguimiento
							WHERE siniestro=".$ireporte->ID."
							and tipo>2
							ORDER BY fecha,hora
							LIMIT 1";
				$sqlBuscaContacto = "SELECT CONCAT(fecha,' ',hora) AS CONTACTO
									FROM aoacol_aoacars.seguimiento
									WHERE siniestro=".$ireporte->ID." AND tipo IN (3,5,6,14,15,17)
									ORDER BY fecha,hora
									LIMIT 1";
							
			//echo $sqlBusca;
			$sqlBuscaDos = $this->fetch_objects_test($sqlBusca);
			$sqlBuscaCont = $this->fetch_objects_test($sqlBuscaContacto);
				
				if(!$sqlBuscaDos || !$sqlBuscaCont){
					$buscaGestion = $reporte;
					$buscaContacto = $reporte;
				}else{
					$buscaGestion = $sqlBuscaDos;
					
					$buscaContacto = $sqlBuscaCont;
				}
			
				foreach($buscaGestion as $rowBusca){
					
					$ireporte->GESTION = $rowBusca->GESTION;
				}
				foreach($buscaContacto as $rowContacto){
					$ireporte->CONTACTO = $rowContacto->CONTACTO;
				}
			}
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		}
		
		
		public function reporte_ubicaciones($request){
			
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = "CURDATE() - INTERVAL 2 MONTH and CURDATE() + INTERVAL 2 MONTH";
			}
			
			
			
			$sql = "select u.id ID_UBICACION,vehi.placa PLACA,ofici.nombre OFICINA, marc.nombre MARCA,ase.nombre ASEGURADORA, 
					linea.nombre LINEA, vehi.modelo MODELO,u.odometro_inicial ODOMETRO_INICIAL,u.odometro_final ODOMETRO_FINAL,u.odometro_diferencia ODOMETRO_DIFERENCIA,esvehi.nombre ESTADO, 
					u.fecha_inicial FECHA_INICIAL, 
					u.fecha_final FECHA_FINAL,u.obs_mantenimiento OBSERVACIONES_MANTENI
                    from aoacol_aoacars.ubicacion u
					LEFT OUTER JOIN aoacol_aoacars.vehiculo vehi on u.vehiculo = vehi.id
					LEFT OUTER JOIN aoacol_aoacars.oficina ofici on u.oficina = ofici.id
					LEFT OUTER JOIN aoacol_aoacars.aseguradora ase on u.flota = ase.id
					LEFT OUTER JOIN aoacol_aoacars.marca_vehiculo marc on vehi.dv = marc.id
					LEFT OUTER JOIN aoacol_aoacars.linea_vehiculo linea on vehi.linea = linea.id
					LEFT OUTER JOIN aoacol_aoacars.estado_vehiculo esvehi on u.estado = esvehi.id
					WHERE date_format(u.fecha_inicial,'%Y-%m-%d') between ".$interval." ORDER BY vehi.placa DESC,u.id DESC,u.fecha_inicial DESC;";
		//echo $sql;
		        $reporte = $this->fetch_objects_test($sql);
				
				
			
			foreach($reporte as $ireporte)
			{ 
				$ireporte->ID_UBICACION = str_replace(",","",utf8_encode($ireporte->ID_UBICACION));
				$ireporte->ESTADO = str_replace(",","",utf8_encode($ireporte->ESTADO));
				$ireporte->OBSERVACIONES_MANTENI = str_replace(",","",utf8_encode($ireporte->OBSERVACIONES_MANTENI));
				$ireporte->OBSERVACIONES_MANTENI = trim($ireporte->OBSERVACIONES_MANTENI);
				$ireporte->OBSERVACIONES_MANTENI = str_replace('"','',utf8_encode($ireporte->OBSERVACIONES_MANTENI));
				$ireporte->OBSERVACIONES_MANTENI = rtrim($ireporte->OBSERVACIONES_MANTENI);
				$ireporte->OBSERVACIONES_MANTENI = str_replace("	","",utf8_encode($ireporte->OBSERVACIONES_MANTENI));
				$ireporte->OBSERVACIONES_MANTENI = str_replace(".","",utf8_encode($ireporte->OBSERVACIONES_MANTENI));
				if($ireporte->MARCA == "" || $ireporte->MARCA == null){
					$ireporte->MARCA = "NO DEFINIDO";
				}else{
					$ireporte->MARCA ;
				} 
			}
			
			//print_r($reporte);
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
			
			
		}
		
		public function reporte_requisicion_facturas($request){
			function quitar_tildes($cadena) {
			$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹",",");
			$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E","");
			$texto = str_replace($no_permitidas, $permitidas ,$cadena);
			return $texto;
			}
			
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = " CURDATE() - INTERVAL 4 DAY and CURDATE() + INTERVAL 4 DAY";
			}
			
			
			
			$sql = "select requisicion.fecha as FECHA,requisiciond.requisicion as REQUISICION,ciudad.nombre as CIUDAD,provee_produc_serv.nombre as ITEM, requisicion.placa as PLACA,requisiciond.observaciones as OBSERVACIONES_O_COMENTARIOS,
					requisiciond.valor as VALOR_UNITARIO,requisiciond.cantidad as CANTIDAD,requisiciond.valor_total AS VALOR_TOTAL,
					case requisicion.cerrada when 0 then 'NO' when 1 then 
					'SI' end as CERRADA,t_estado_requisicion(requisicion.estado) as ESTADO,
					concat( oficina.centro_operacion,'  ',oficina.nombre) as CENTRO_OP,aseguradora.ccostos_uno as CENTRO_COSTOS,aseguradora.nombre as ASEGURADORA,t_ubica(ubicacion) as UBICACION,
					tipo.nombre as TIPO,unidad_de_medida.nombre as UNIDAD_MEDIDA,refactura.id ID_FACTURA_PROVEDOR,refactura.fecha_creacion FECHA_FAC_PROVEDOR,refactura.valor_factura VALOR_FACTURA_PROVEEDOR, refactura.consecutivo_provee CONSECUTIVO_PROVEEDOR
					from aoacol_administra.requisiciond
                    LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
					LEFT OUTER JOIN requisicionc on requisiciond.clase = requisicionc.id
					LEFT OUTER JOIN aoacol_administra.requisicion ON requisiciond.requisicion = requisicion.id
					LEFT OUTER JOIN aoacol_administra.ciudad on requisicion.ciudad = ciudad.codigo
					LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
					INNER JOIN aoacol_aoacars.oficina on  ubicacion.oficina = oficina.id
					LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id 
					LEFT OUTER JOIN aoacol_aoacars.requisiciond_facturas refactura ON requisiciond.id = refactura.requisiciond
					WHERE date_format(requisicion.fecha,'%Y-%m-%d') between ".$interval." order by REQUISICION;";
		
		        $reporte = $this->fetch_objects_test($sql);
			
			foreach($reporte as $ireporte)
			{
				$observacionesSinTildes = quitar_tildes(utf8_encode($ireporte->OBSERVACIONES_O_COMENTARIOS));
				$ireporte->OBSERVACIONES_O_COMENTARIOS = $observacionesSinTildes;
				$ireporte->ITEM = utf8_encode($ireporte->ITEM);
				$ireporte->CIUDAD = utf8_encode($ireporte->CIUDAD);
				(!$ireporte->CENTRO_OP) ? $ireporte->CENTRO_OP = 'NACIONAL'  : $ireporte->CENTRO_OP = utf8_encode($ireporte->CENTRO_OP) ; 
				$ireporte->VALOR_UNITARIO = "$".number_format($ireporte->VALOR_UNITARIO);
				$ireporte->VALOR_TOTAL = "$".number_format($ireporte->VALOR_TOTAL);
			}
			
			//print_r($reporte);
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
			
			
		}
		
		public function reporte_requicision_administrativo($request)
		{
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = "CURDATE() - INTERVAL 15 DAY and CURDATE() + INTERVAL 15 DAY";
			}
			
			
			
			$sql = "select requisicion.fecha as FECHA,requisicion.placa as PLACA,requisiciond.requisicion as REQUISICION,ciudad.nombre as CIUDAD,requisicion.solicitado_por as SOLICITADO_POR,provee_produc_serv.nombre as ITEM, requisiciond.observaciones as OBSERVACIONES_O_COMENTARIOS,
					requisiciond.valor as VALOR_UNITARIO,requisiciond.cantidad as CANTIDAD,requisiciond.valor_total AS VALOR_TOTAL,
					case requisicion.cerrada when 0 then 'NO' when 1 then 
					'SI' end as CERRADA,aoacol_administra.t_estado_requisicion(requisicion.estado) as ESTADO,
					concat(oficina.centro_operacion,' ',oficina.nombre) as CENTRO_OP,requisiciond.centro_costo as CENTRO_COSTOS,
					tipo.nombre as TIPO,unidad_de_medida.nombre as UNIDAD_MEDIDA,
                    requisiciond.factor as FACTOR,aoacol_aoacars.vehiculo.placa as PLACA_AOACARS,refactura.id ID_FACTURA_PROVEDOR,refactura.fecha_creacion FECHA_FAC_PROVEDOR,refactura.valor_factura VALOR_FACTURA_PROVEEDOR,refactura.consecutivo_provee CONSECUTIVO_PROVEEDOR
					from aoacol_administra.requisiciond
					LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
					LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id
					LEFT OUTER JOIN aoacol_aoacars.oficina on requisiciond.centro_operacion = aoacol_aoacars.oficina.id 
					LEFT OUTER JOIN aoacol_administra.requisicionc on requisiciond.clase = requisicionc.id
					LEFT OUTER JOIN aoacol_administra.requisicion ON requisiciond.requisicion = requisicion.id
					LEFT OUTER JOIN aoacol_administra.ciudad on requisicion.ciudad = ciudad.codigo 
					LEFT OUTER JOIN aoacol_aoacars.requisiciond_facturas refactura ON requisiciond.id = refactura.requisiciond
					WHERE requisicion.ubicacion = 0  and date_format(requisicion.fecha,'%Y-%m-%d')  between ".$interval." order by REQUISICION;";
			$reporte = $this->fetch_objects($sql);
			
			foreach($reporte as $ireporte)
			{
				$ireporte->OBSERVACIONES_O_COMENTARIOS = str_replace(",","",utf8_encode($ireporte->OBSERVACIONES_O_COMENTARIOS));
				$ireporte->ITEM = utf8_encode($ireporte->ITEM);
				$ireporte->CIUDAD = utf8_encode($ireporte->CIUDAD);
				(!$ireporte->CENTRO_OP) ? $ireporte->CENTRO_OP = 'NACIONAL'  : $ireporte->CENTRO_OP = utf8_encode($ireporte->CENTRO_OP) ; 
		        $ireporte->VALOR_UNITARIO = "$".($ireporte->VALOR_UNITARIO);
				$ireporte->VALOR_TOTAL = "$".($ireporte->VALOR_TOTAL);
			}
			
			//print_r($reporte);
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
			
		}
		public function reporte_extras_extencion($request){
			
			function quitar_tildes($cadena) {
			$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
			$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
			$texto = str_replace($no_permitidas, $permitidas ,$cadena);
			return $texto;
			}
			
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = "CURDATE() - INTERVAL 1 DAY and CURDATE() + INTERVAL 1 DAY";
			}
			
			$Tmp='tmpi_hf_'.$_SESSION['User'].'_'.$_SESSION['Id_alterno'];
			
			$sqlBorrarTabla = "drop table if exists aoacol_aoacars.$Tmp;";
			
			$var = $this->query($sqlBorrarTabla);
			
			//var_dump($var);
			
			$temporarySql = "create table aoacol_aoacars.$Tmp 
			select so.id,aoacol_aoacars.t_siniestro(so.siniestro_asignado) 
			as so_siniestro_asignado,so.fecha 
			from aoacol_aoacars.solicitud_extra 
			so where aoacol_aoacars.t_siniestro(so.siniestro_asignado) !='';";
			
			//echo $temporarySql."<br>";
			
			$var = $this->query($temporarySql);
			
			
			
			$sql = "select si.numero as NUMERO_SINIESTRO,aoacol_aoacars.t_aseguradora(si.aseguradora) 
			as ASEGURADORA, $Tmp.fecha as so_fecha,aoacol_aoacars.t_siniestro(so.siniestro) 
			as SINIESTRO_ASIGNADO,so.fecha_proceso as FECHA_PROCESO, 
			if(so.anulado=1,'ANULADO','') as ANULADO, so.solicitado_por as SOLICITADO_POR, 
			so.tipo as TIPO, si.fecha_inicial as FECHA_INICIAL, si.fecha_final as FECHA_FINAL, DATEDIFF(si.fecha_inicial,si.fecha_final)  as NUMERO_DIAS
			from aoacol_aoacars.siniestro si 
			left join aoacol_aoacars.$Tmp on so_siniestro_asignado = numero 
			left join aoacol_aoacars.solicitud_extra so on si.id = so.siniestro where date_format($Tmp.fecha,'%Y-%m-%d') between ".$interval." 
			order by aseguradora ASC,$Tmp.fecha ASC;";
			
			//echo $sql; 
			$reporte = $this->fetch_objects_test($sql);
			//$reporte = $this->fetch_objects_test($this->query($sql));			
			
		     //var_dump($reporte);
			
			//exit;
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		}
		public function reporte_pqrs($request){
			function quitar_tildes($cadena) {
			$no_permitidas= array (".",",","á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
			$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
			$texto = str_replace($no_permitidas, $permitidas ,$cadena);
			return $texto;
			}
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = "CURDATE() - INTERVAL 2 MONTH and CURDATE() + INTERVAL 2 MONTH";
			}
			$sql = "select s.id as ID,s.cliente as CLIENTE, aoacol_aoacars.t_oficina(s.oficina) 
					as OFICINA , aoacol_aoacars.t_pqr_tipo(s.tipo_solicitud) 
					as TIPO, s.fecha as FECHA, paseg.nombre 
					as ASEGURADORA,s.descripcion as DESCRIPCION_PQR_INICIAL_POR_PARTE_CLIENTE,aoacol_aoacars.t_pqr_estado_respuesta(r.pqr_estado_respuesta) 
                    as ESTADO,s.registrado_por as REGISTRADO_POR,consecutivo 
					as CONSECUTIVO
					,fecha_recibido as FECHA_RECIBODO,fecha_alta as FECHA_ALTA,fecha_vencimiento 
                    as FECHA_VENCIMIENTO,placa as PLACA,r.descripcion as DESCRICION_RESPUESTA
					from aoacol_aoacars.pqr_solicitud as s 
					inner join aoacol_aoacars.pqr_aseguradora as paseg on s.aseguradora = paseg.id 
					left join aoacol_aoacars.pqr_respuesta r on s.id = r.solicitud
					where date_format(s.fecha,'%Y-%m-%d') 
					between ".$interval."  order by id DESC;";
				//echo $sql;
					
					$reporte = $this->fetch_objects_test($sql);
					
					foreach($reporte as $ireporte){
						$varDescripcion = quitar_tildes(str_replace(",","",utf8_encode($ireporte->DESCRIPCION_PQR_INICIAL_POR_PARTE_CLIENTE)));
						$ireporte->DESCRIPCION_PQR_INICIAL_POR_PARTE_CLIENTE = utf8_encode($varDescripcion);
						$ireporte->DESCRICION_RESPUESTA = quitar_tildes(str_replace(",","",utf8_encode($ireporte->DESCRICION_RESPUESTA)));
				      if($ireporte->ESTADO == ""){
					     $ireporte->ESTADO =  "PENDIENTE";
					 }else{
					$ireporte->ESTADO;
					}
			}
			//$reporte = $this->fetch_objects_test($this->query($sql));			
			
		     //var_dump($reporte);
			
			//exit;
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
					
		}
		
		public function reporte_info_cartera($request){
			function quitar_tildes($cadena) {
			$no_permitidas= array (".",",","á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
			$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
			$texto = str_replace($no_permitidas, $permitidas ,$cadena);
			return $texto;
			}
			
			if(isset($request->fecha_corte)){
				$intervalCorte = $request->fecha_corte;
				if($request->fecha_inicio == ""){
					$interval = "CURDATE() - INTERVAL 2 MONTH and CURDATE() + INTERVAL 2 MONTH";
				}else{
					$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
					
				}
				
			}else{
				$intervalCorte = date('Y-m-d');
				$interval = "CURDATE() - INTERVAL 2 MONTH and CURDATE() + INTERVAL 2 MONTH";
			}
		
			
			
			$sql = "SELECT fac.id ID_FACTURA,fac.consecutivo CONSECUTIVO,CONCAT(clien.nombre,' ',clien.apellido) NOMBRE_CLIENTE,
					fac.fecha_emision FECHA_EMICION,fac.fecha_vencimiento FECHA_VENCIMIENTO,fac.total TOTAL_FACTURA
					FROM  aoacol_aoacars.factura fac
					INNER JOIN aoacol_aoacars.cliente clien ON fac.cliente = clien.id
					WHERE date_format(fac.fecha_emision,'%Y-%m-%d') between ".$interval." ORDER BY fac.id DESC;";
				
				
				
			$reporte = $this->fetch_objects_test($sql);
			
			foreach($reporte as $ireporte){
			$varDescripcion = quitar_tildes(str_replace(",","",utf8_encode($ireporte->NOMBRE_CLIENTE)));
			$ireporte->NOMBRE_CLIENTE = utf8_encode($varDescripcion);
			$sqlEstaFacturados = "SELECT fac_segui.estado ESTADO
							FROM aoacol_aoacars.factura fac
							LEFT JOIN aoacol_aoacars.fact_electronica_seguimiento fac_segui ON fac_segui.factura = fac.id
							WHERE fac.id = ".$ireporte->ID_FACTURA." ORDER BY fac_segui.id desc
							LIMIT 1";
			$estadoSeguimiento = $this->fetch_objects_test($sqlEstaFacturados);	
			
						
						foreach($estadoSeguimiento as $iestados){
							switch($iestados->ESTADO){case "1":$ireporte->ESTADO = "FACTURADO";break;case "2":$ireporte->ESTADO 
						     = "NO FACTURADO";break;case "3":$ireporte->ESTADO = "ERROR SERVIDOR";case "4":
							$ireporte->ESTADO = "NO ENVIO FACTURA";case "":$ireporte->ESTADO = "NO HAY RESPONSE";break;}
						}
						
							
							
						$sqlRecibosCaja = "SELECT SUM(reci_caja.valor) RECAUDO
							   FROM aoacol_aoacars.factura fac
							   LEFT JOIN aoacol_aoacars.recibo_caja reci_caja ON fac.id = reci_caja.factura
							   WHERE date_format(fac.fecha_emision,'%Y-%m-%d') between ".$interval." AND fac.id = ".$ireporte->ID_FACTURA." AND reci_caja.anulado != 1
							   ORDER BY fac.id DESC";
						$sqlNotasCredito = "SELECT SUM(nota_cre.total) TOTAL_NOTA_CREDITO
							   FROM aoacol_aoacars.factura fac
							   LEFT JOIN aoacol_aoacars.nota_credito nota_cre ON fac.id = nota_cre.factura
							   WHERE date_format(fac.fecha_emision,'%Y-%m-%d') between ".$interval." AND fac.id = ".$ireporte->ID_FACTURA." AND nota_cre.anulado != 1
							   ORDER BY fac.id DESC";
						
						$reporteNotas = $this->fetch_objects_test($sqlNotasCredito);
						$reporteCaja = $this->fetch_objects_test($sqlRecibosCaja);
						
						foreach($reporteCaja as $ireporteRecivo){
							if($ireporteRecivo->RECAUDO == "" or $ireporteRecivo->RECAUDO == null){
							$pagado = 0;
							}else{
								$pagado = $ireporteRecivo->RECAUDO;	
							}
						foreach($reporteNotas as $iNotas){
							$valorNota = $iNotas->TOTAL_NOTA_CREDITO;
						}
						
						
							
						$validacionSaldo = $pagado - $ireporte->TOTAL_FACTURA;
						if($valorNota != 0){
							$recaudo = 0;
							$saldo = 0;
							
						}else{
							$recaudo = "$".number_format($pagado - 0);
							$saldo = $validacionSaldo;
							
						}
						$ireporte->RECAUDO =  $recaudo;
						$ireporte->TOTAL_NOTA_CREDITO = "$".number_format($valorNota);
						$ireporte->SALDO = $saldo;
						}
						
						
						
						
						
						/*validar dias de vencimiento*/
						$ireporte->ID_FACTURA = $ireporte->ID_FACTURA - 0;
						
						$ireporte->TOTAL_FACTURA = "$".number_format($ireporte->TOTAL_FACTURA - 0);
						
						$fechaCorte = new DateTime($intervalCorte);
						$fechaVencimiento = new  DateTime($ireporte->FECHA_VENCIMIENTO);
						$dias = $fechaCorte->diff($fechaVencimiento);
						
						if($ireporte->SALDO == 0){
							$ireporte->DIAS_VENCIMIENTO = 0;
						}else{
							$ireporte->DIAS_VENCIMIENTO = $dias->days;
						}
						
						
					}	
			
			//$reporte = $this->fetch_objects_test($this->query($sql));			
			
		     //var_dump($reporte);
			
			//exit;
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		}
		public function reporte_info_detalle_factura($request){
			function quitar_tildes($cadena) {
			$no_permitidas= array (".",",","á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
			$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
			$texto = str_replace($no_permitidas, $permitidas ,$cadena);
			return $texto;
			}
			
			if(isset($request->fecha_inicio))
			{
				$interval = "'".$request->fecha_inicio."' and '".$request->fecha_final."' ";
			}
			else
			{	
				$interval = "CURDATE() - INTERVAL 2 MONTH and CURDATE() + INTERVAL 2 MONTH";
			}
		
			
			
			$sql = "SELECT fac.id ID_FACTURA,fac.consecutivo CONSECUTIVO,fac.fecha_emision FECHA_EMICION,concep_fac.nombre ITEM,facd.descripcion DESCRIPCION,
					facd.cantidad CANTIDAD,facd.unitario VALOR_UNITARIO,fac.id VALOR_ANTES_IVA,facd.iva IVA,
					fac.total TOTAL_FACTURA,nota.total NOTA_CREDITO,reci_caja.valor RECAUDO, ciuda.nombre CIUDAD
					FROM aoacol_aoacars.factura fac
					INNER JOIN aoacol_aoacars.facturad facd ON fac.id = facd.factura
					INNER JOIN aoacol_aoacars.concepto_fac concep_fac ON  facd.concepto = concep_fac.id
					LEFT JOIN aoacol_aoacars.recibo_caja reci_caja ON fac.id = reci_caja.factura
					LEFT JOIN aoacol_aoacars.siniestro siniestroc ON fac.siniestro = siniestroc.id
					LEFT JOIN aoacol_aoacars.ciudad ciuda ON siniestroc.ciudad = ciuda.codigo
					LEFT JOIN aoacol_aoacars.nota_credito nota ON (fac.id = nota.factura AND nota.anulado != 1)
					WHERE date_format(fac.fecha_emision,'%Y-%m-%d') between ".$interval." ORDER BY fac.id DESC;";
			
			$reporte = $this->fetch_objects_test($sql);
			
			
			foreach($reporte as $ireporte){
				$sqlCiudad = "SELECT ofi.nombre OFICINA FROM aoacol_aoacars.factura fact
					LEFT JOIN aoacol_aoacars.oficina ofi ON fact.oficina = ofi.id WHERE fact.id = ".$ireporte->ID_FACTURA;
			$ciudadFac = $this->fetch_objects_test($sqlCiudad);	
			
			foreach($ciudadFac as $iReporteCi){
				$oficina = $iReporteCi->OFICINA;
				if($ireporte->CIUDAD == ""){
 					if($oficina == ""){
						$ireporte->CIUDAD = "BOGOTA D.C";
					}else{
						$ireporte->CIUDAD = $oficina;
					}
				}
			}
			/*validar dias de vencimiento*/
			$ireporte->ITEM = quitar_tildes(str_replace(",","",utf8_encode($ireporte->ITEM)));
		    $ireporte->ID_FACTURA = $ireporte->ID_FACTURA - 0;
			$ireporte->VALOR_ANTES_IVA = "$".number_format(($ireporte->CANTIDAD * $ireporte->VALOR_UNITARIO));
			$ireporte->VALOR_UNITARIO = "$".number_format($ireporte->VALOR_UNITARIO);
			$ireporte->DESCRIPCION = quitar_tildes(str_replace(",","",utf8_encode($ireporte->DESCRIPCION)));
			//$ireporte->TOTAL_FACTURA = number_format($ireporte->TOTAL_FACTURA);
			
			$ireporte->IVA = "$".number_format($ireporte->IVA);
			$ireporte->NOTA_CREDITO = ($ireporte->NOTA_CREDITO - 0);
			if($ireporte->NOTA_CREDITO != 0){
				$ireporte->RECAUDO =  0;
				$ireporte->SALDO = 0;
				$ireporte->SALDADA = "NOTA CREDITO";
			}else{
				$ireporte->RECAUDO =  ($ireporte->RECAUDO - 0);
				$ireporte->SALDO = ($ireporte->TOTAL_FACTURA - $ireporte->RECAUDO);
			}
			if($ireporte->RECAUDO == $ireporte->TOTAL_FACTURA){
				$ireporte->SALDADA = "PAGADA";
			}else{
				$ireporte->SALDADA = "NO PAGADA";
			}
			if($ireporte->NOTA_CREDITO != 0){
					$ireporte->SALDADA = "NOTA CREDITO";
				}
			
			
		}	
		
		//$reporte = $this->fetch_objects_test($this->query($sql));			
			
		    //var_dump($reporte);
			
			//exit;
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		}
		
		public function siniestro_requisicion_ubicacion($request)
		{
			$sql = "Select numero from aoacol_aoacars.siniestro where ubicacion = ".$request->ubicacion." LIMIT 1";
			$siniestro = $this->fetch_object($sql);
			if($siniestro == null)
			{$numero_siniestro = null;}
			else
			{$numero_siniestro = $siniestro->numero;}
			
           $response = array("siniestro"=>$siniestro);
			echo json_encode($response);
		}
		
		
	
		
		private function fetch_objects($query)
		{
			$result = $this->query($query);
			
			//print_r($result);
			$rows = array();
			if($result != null)
			{
				while ($row = mysql_fetch_object($result))
				{
					array_push($rows, $row);
				}
				
				return $rows;
			}
			else
			{
				return null;	
			}			
		}
		
		private function fetch_objects_test($query)
		{
			$result = $this->query($query);
		
			$rows = array();
			if($result != null)
			{
				while ($row = mysql_fetch_object($result))
				{
					array_push($rows, $row);
				}
				
				return $rows;
			}
			else
			{
				return null;	
			}			
		}
		
		private function fetch_object($query)
		{
			$result = $this->query($query);
			//var_dump($result);
			if($result != null)
			{
				$row = mysql_fetch_object($result);				
				return $row;
			}
			else
			{
				return null;	
			}
		}
		
		private function get_columns($objects)
		{
			$columns = array();
			foreach($objects as $key => $value)
			{
				array_push($columns,$key);
			}
			return (object)$columns;
		}
		
	}
	
	
	
	
	
	