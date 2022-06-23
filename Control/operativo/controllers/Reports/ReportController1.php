<?php
	
/********************
 * Autor Original: Jesús Vega
 * 
 * Proyecto: 
 * Documentos relacionados: 
 * Descripción del script:
 * Es un script que permite conectar servicios Angular con consultas mysql , regresa objetos JSON que
 * permiten generación de tablas con la librería ng table * 
 * Cambios:
 *Autor: Jesús Vega
 * 1. Se modificó la consulta de la función facutra_electronica_reporte
 * 2. Se agregó una validación para que el acceso a la clase ReporController no se rompa si el metodo no existe
 * 
 * Fecha:22/02/2019
 *********************/


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
		if(method_exists($report,$name))
		{
			$report->$name($request);	
		}
		else{
			echo json_encode(array("desc"=>"Metodo no existe: ".$name));
			
		}
	}
	else
	{
		$report = new ReportController();		
		$report->reporte_requisiciones_fechas(null);
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
		
		public function listado_vehiculos_flota($request)
		{
			
			
			$sql = "SELECT  ve.id AS ve_id, aseg.nombre AS nombre_aseguradora, mo.nombre 
			AS Modalidad_de_Tenencia, ve.placa AS placa, ma.nombre AS marca, ve.modelo AS modelo, li.nombre AS linea, 
			ve.nombre_propietario AS nombre_propietario, ve.fecha_matricula AS fecha_matricula, 
			ve.cilindraje AS cilindraje, ve.numero_chasis AS numero_chasis, ve.numero_motor AS numero_motor, 
			se.aseguradora_nombre AS poliza_seguro,se.n_poliza numero_poliza, ve.n_contrato AS contrato_leasing, 
			ve.valorcompra AS valor_compra
			FROM vehiculo  AS ve 
			left join modalidad_tenencia mo on mo.id = ve.modalidad_de_compra
			left join aseguradora aseg on ve.flota = aseg.id
			left JOIN seguros se on ve.n_poliza = se.id
			left JOIN linea_vehiculo AS li ON li.id = ve.linea
			left JOIN marca_vehiculo AS ma ON li.marca = ma.id
			WHERE inactivo_desde='0000-00-00'";
			
			//echo $sql;
			
			
			$reporte = $this->fetch_objects($sql);

			foreach($reporte as $report)
			{
				
				$subsql = "Select * from ubicacion where vehiculo= '".$report->ve_id."' order by id desc limit 1";
				
				$ubicacionVehi = $this->fetch_objects($subsql);
				
				$oficinaSql = "SELECT nombre FROM oficina where id = ".$ubicacionVehi[0]->oficina;
				
				$oficinaVer = $this->fetch_objects($oficinaSql);
				
				$report->ultima_ubicacion = $oficinaVer[0]->nombre;
				
				$report->fecha_inicial = $ubicacionVehi[0]->fecha_inicial;
				
				$report->fecha_final = $ubicacionVehi[0]->fecha_final;
				
				$report->odometro_final = $ubicacionVehi[0]->odometro_final;
				
				$estadoSql = "SELECT nombre FROM estado_vehiculo where id = ".$ubicacionVehi[0]->estado;
				
				$estadoVer = $this->fetch_objects($estadoSql);
				
				$report->estado = utf8_encode($estadoVer[0]->nombre);
				
				$report->Modalidad_de_Tenencia = utf8_encode($report->Modalidad_de_Tenencia);
				
			}	
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		
		}
		
		public function listado_vehiculos_flota_dos($request)
		{
			$sql = "SELECT  ve.id AS ve_id, aseg.nombre AS nombre_aseguradora,
			mo.nombre AS Modalidad_de_Tenencia, ve.placa AS placa,
			ma.nombre AS marca,
			ve.modelo AS modelo, 
			li.nombre AS linea, 
            ve.nombre_propietario AS nombre_propietario,
			ve.fecha_matricula AS fecha_matricula, 
			ve.cilindraje AS cilindraje, ve.numero_chasis AS numero_chasis,
			ve.numero_motor AS numero_motor, 
			se.aseguradora_nombre AS poliza_seguro,se.n_poliza numero_poliza,
			ve.n_contrato AS contrato_leasing, 
			ve.valorcompra AS valor_compra, 
			alert.ultimo_kilometraje AS ultimo_kilometraje, 
			alert.ultimo_fecha, 
			tialert.nombre AS estado_alerta,
			ve.zona AS zona, 
			ve.operacion AS operacion, 
			ve.gerente_cargo AS gerente_cargo, 
			ve.ubicacion_parqueadero AS ubicacion_parqueadero, 
			ve.segmento AS segmento, 
			ve.municipio_operacion AS municipio_operacion, 
			ve.tipo_uso AS tipo_uso, 
			ve.direccion_parqueadero AS direccion_parqueadero
             FROM vehiculo  AS ve 
            left join modalidad_tenencia mo on mo.id = ve.modalidad_de_compra
			left join aseguradora aseg on ve.flota = aseg.id
			left JOIN seguros se on ve.n_poliza = se.id
			left JOIN linea_vehiculo AS li ON li.id = ve.linea
			left JOIN marca_vehiculo AS ma ON li.marca = ma.id
			LEFT JOIN cfg_alerta_vehiculo alert ON ve.id = alert.vehiculo
			LEFT JOIN tipo_alerta tialert ON alert.alerta = tialert.id
			WHERE inactivo_desde='0000-00-00'";
			
			//echo $sql;
			
			
			$reporte = $this->fetch_objects($sql);

			foreach($reporte as $report)
			{
				$subsql = "Select * from ubicacion where vehiculo= '".$report->ve_id."' order by id desc limit 1";
				
				$ubicaVehi = "select ultima_ubicacion,tipo_caja,tipo_traccion  from vehiculo where id =".$report->ve_id;
				$subproVehi = $this->fetch_objects($ubicaVehi);
				$subpro = $this->fetch_objects($subsql);
				$report->Modalidad_de_Tenencia = utf8_encode($report->Modalidad_de_Tenencia);
				if($subpro)
				{
					$report->Kilometraje = $subpro[0]->odometro_final;	
					if(count($subpro) > 0)
					{
						
						$subsql = "Select * from oficina where id = ".$subpro[0]->oficina." ";
						$subsqlSegUbica = "Select * from oficina where id = ".$subproVehi[0]->ultima_ubicacion." ";
						$tipoCaja = "select nombre from vehiculo_tipo_caja where id =".$subproVehi[0]->tipo_caja;
						$tipoTraccion = "select sigla from vehiculo_tipo_traccion where id =".$subproVehi[0]->tipo_traccion;
						
						//echo $subsql;
						$subpro = $this->fetch_objects($subsql);
						$subproVehi = $this->fetch_objects($subsqlSegUbica);
						$subproTipoCaja = $this->fetch_objects($tipoCaja);
						$tipoTrac = $this->fetch_objects($tipoTraccion);
						
						$report->ubicacion_estado = utf8_encode($subpro[0]->nombre);
						$report->ubicacion_vehiculo = utf8_encode($subproVehi[0]->nombre);
						
						
						if($report->ubicacion_estado == $report->ubicacion_vehiculo){
						$report->son_iguales = "IGUALES";
						}else{
						$report->son_iguales = "NO IGUALES";	
						}
						
						$report->tipo_caja = utf8_encode($subproTipoCaja[0]->nombre);
						$report->tipo_traccion = $tipoTrac[0]->sigla;
						
						$report->centro_costos = $subpro[0]->ccostos; 					
					
					}
				}
				else
				{
					$report->Kilometraje = "";
					$report->ultima_ubicacion = "";
					$report->centro_costos = "";
				}	
			}	
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		
		}
		
		
		public function documentos_manuales_electronicos_generados($request)
		{
			$sql = "Select docs.fecha,docs.id,docs.json,tipos.nombre as tipo_documento,docs.estado, 
			es.nombre as estadot, docs.consecutivo,docs.doc_relacionado,docs.usuario,docs.descr 
			from docs_manuales_electronicos as docs 
			inner join docs_manuales_electronicos_tipos as tipos on docs.tipo = tipos.id 
			inner join fact_electronica_estados as es  on docs.estado =  es.id 
			order by docs.consecutivo , docs.fecha";			
			$reporte = $this->fetch_objects($sql);			
			
			$columns = $this->get_columns($reporte[0]);
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			echo json_encode($response);
		
		}
		
		public function facturacion_electronica_reporte(){
			$sql = "SELECT DISTINCT(factura.id),factura.consecutivo as consecutivo, fecha, descr, usuario,cliente.nombre as Cliente_nombre ,cliente.apellido as Cliente_apellido,  fact_electronica_estados.nombre as estado, factura.total as Valor 
					FROM factura
					LEFT JOIN fact_electronica_seguimiento  ON fact_electronica_seguimiento.factura = factura.id					
                    LEFT JOIN fact_electronica_estados on fact_electronica_seguimiento.estado = fact_electronica_estados.id					
                    LEFT JOIN  cliente on factura.cliente = cliente.id WHERE factura.consecutivo like '%FE%'  order by fecha desc";
					
			$reporte = $this->fetch_objects($sql);		
			
			$columns = $this->get_columns($reporte[0]);
			
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			
			echo json_encode($response);
		}
		
		public function nota_credito_electronica_reporte(){
			$sql = "SELECT DISTINCT(ptesa_conse) as consecutivo,nota_credito.id, descr, nota_credito.fecha, factura, nota_credito_electronica_seguimiento.fecha as fecha_transaccion, usuario,cliente.nombre as Cliente_nombre ,cliente.apellido as Cliente_apellido,  fact_electronica_estados.nombre as estado, nota_credito.total as Valor 
					FROM nota_credito                    
					LEFT JOIN nota_credito_electronica_seguimiento ON nota_credito_electronica_seguimiento.nota_credito = nota_credito.id					
                    LEFT JOIN fact_electronica_estados on nota_credito_electronica_seguimiento.estado = fact_electronica_estados.id					
                    INNER JOIN factura ON nota_credito.factura = factura.id
					LEFT JOIN  cliente on factura.cliente = cliente.id where nota_credito.id >= 290 order by fecha_transaccion desc;";
					
			$reporte = $this->fetch_objects($sql);		
			
			$columns = $this->get_columns($reporte[0]);
			
			$response = array("columns"=>$columns,"rows"=>$reporte,"sql"=>$sql);
			
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
		
		private function fetch_object($query)
		{
			$result = $this->query($query);
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
			if(count($objects) > 0)
			{	
				foreach($objects as $key => $value)
				{
					array_push($columns,$key);
				}
			}
			return (object)$columns;
		}
		
	}
	
	
	
	
	
	