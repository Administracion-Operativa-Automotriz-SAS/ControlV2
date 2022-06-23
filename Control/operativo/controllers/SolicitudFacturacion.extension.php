<?php


/*

	NOTAS: Para este proceso de prefacturas se cambio la logica en que funcionan las facturas normales de la organización
	Por lo tanto dentro del registro de factura no vamos a poder ver el siniestro, ya que al facturar en lote una factura
	puede tener varios siniestros asociados, por esta razón los siniestros se encuentran en los detalles de cada factura 
	en la tabla facturad , para acceder a esta información desde factura debe hacerse por el campo id de factura o el de 
	prefactura ya que facturad posee ambos campos.
	
	2. Existen unas tablas falsas llamadas factura_development , facturad_development, prefactura_development, fuerón 
	creadas para poder hacer pruebas sin afectar el proceso de la organización , hay una variable llamada developoment
	para desactivar esto en el codigo.



*/




ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE | E_NOTICE);


include_once(dirname(__FILE__).'/../config/config.php');

include("../factura_xml/factura_electronica.php");


if($_REQUEST){
	include_once(dirname(__FILE__).'/../config/resuelve.php');
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL); 

	//header('Content-type: text/html; charset=utf-8');
	if(isset($_REQUEST['Acc']))
	{
		$extension = new CurrentExtension($_REQUEST);
		$response = $extension->$_REQUEST['Acc']();
		$type = gettype($response);
		
		if($type != 'string')
		{
			
			echo json_encode($response);			
		}
		else
		{
			//header("Content-Type: text/html; charset=utf-8");
			echo $response;
		}
			
		
		
		
	}
	else
	{
		$request_body = file_get_contents('php://input');    	
		$request = json_decode($request_body);
		$acc = $request->Acc;				
		$extension = new CurrentExtension($request);
		$response = $extension->$acc();
		$type = gettype($response);
		if($type != 'string')
		{
			echo json_encode($response);			
		}
		else
		{
			header("Content-Type: text/html; charset=utf-8");
			echo $response;
		}
	
	}
	
}




class CurrentExtension
{
	 function __construct($REQUEST){
		//echo "me construyeron";
		$this->request = $REQUEST;
		$development = 0;
		if($development == 1)
		{
			$this->ext_table = "_development";
		}
		else
		{
			$this->ext_table = "";
		}
		
		$this->only_show_sentences = false;		
		
    }
	
	public function query($cadena, $Devolver_sql = 0,&$_Cantidad_registros_afectados=0) // corre un query invocado internamente
	{
		if($this->only_show_sentences)
		{
			if (strpos(strtoupper($cadena), 'INSERT') !== false  or strpos(strtoupper($cadena), 'UPDATE') !== false) {
				echo $cadena;
				echo "<br>";
				return "";
			}			
		}	
		
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
				html();
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
			else
			{
				# debug_print_backtrace();
				echo "<br><br><b>Error en :<br>" . $cadena . "</b><br>Error: $Error_de_mysql<br>";
				
				die();
			}
		}
	}
	
	public function insertar_desde_citas()
	{		
		$requestcitas = $this->request['citas'];		
		
		$sql = "SELECT s.*,es.nombre as estado_nombre, u.* , fl.nombre as nombre_flota  from cita_servicio as c inner join siniestro as s on c.siniestro = s.id inner join ubicacion as u  on  
		   s.ubicacion = u.id inner join estado_siniestro as es on es.id = s.estado inner join aseguradora as fl on c.flota = fl.id where c.id in ($requestcitas) ";
		$query = $this->query($sql);
		$SINIESTROS = $this->convert_objects($query);
		
	
		$validate = null;
		foreach($SINIESTROS as $SINIESTRO)
		{
			//echo "siniestro ".$SINIESTRO->id." aseguradora ".$SINIESTRO->aseguradora." ";
			//echo $SINIESTRO->aseguradora;
			if($validate == null)
			{
				$validate = $SINIESTRO->aseguradora;				
			}
			else{
				
				if($validate == "false" or $validate != $SINIESTRO->aseguradora)
				{
					$validate = "false";
				}
				else
				{
					$validate = $SINIESTRO->aseguradora;
				}
			}
			
		}
		

		
		if(is_numeric($validate))
		{			
			$query = $this->query("select * from aseguradora where id = $validate");
			$aseguradora =  $this->convert_objects($query);
			$aseguradora = $aseguradora[0];	
				
			$query = $this->query("select codigo,nombre from tipo_identificacion");
			$tipos_doc =  $this->convert_objects($query);
			$query = $this->query("select codigo,nombre from pais ");
			$paises =  $this->convert_objects($query);

			$query = $this->query("select distinct departamento from ciudad");
			$departamentos =  $this->convert_objects($query);
			
			
			$validate = true;
			ob_start();
			include('/var/www/html/public_html/Control/operativo/views/subviews/user_form_solicitud_facturacion_extension.html');
			$html = ob_get_clean();
			
		}
		else
		{
			$validate = false;
			$html = null;
			$aseguradora = null;
		}
		
		return $array = array("validacion_aseguradoras"=>$validate,"html"=>$html,"aseguradora"=>$aseguradora);	
		
		//Si es false significa que tienen distintas aseguradoras y no se pueden unir
		
	}
	
	
	public function get_cliente()
	{		
		$documento = $this->request['identificacion'];
		$sql = "Select * from cliente where identificacion = ".$documento;
		$query = $this->query($sql);
		$cliente = $this->convert_objects($query);
		$cliente = $cliente[0];
		return $cliente;
	}
	
	public function get_ciudades()
	{
		$departamento = $this->request['departamento'];
		$query = $this->query("Select codigo,nombre from ciudad where departamento = '$departamento' ");
		$ciudades = $this->convert_objects($query);
		
		ob_start();
		include('/var/www/html/public_html/Control/operativo/views/subviews/ciudades_departamento.html');
		$html = ob_get_clean();
		
		return array("html"=>$html);
	}
	
	
	public function get_ciudad()
	{
		$codigo = $this->request['codigo'];
		$query = $this->query("Select nombre  from ciudad where codigo =  $codigo ");
		$ciudad = $this->convert_objects($query);
		$ciudad = $ciudad[0];
		return $ciudad;		
	}	

	
	public function user_data_process()
	{
		unset($this->request['Acc']);
		$this->request = $_POST;
		$cliente = $this->get_cliente();
		if($cliente == null)
		{
			$sql = $this->insert("cliente",$this->request);
		}
		else
		{
			$sql = $this->update("cliente",$this->request,$cliente->id);
		}
		//echo "sql funcion";
		
		return $reponse = array("status"=>1);
	}
	
	
	public function insert($table,$array)
	{		
	
		$sql = "Insert into ".$table;
		$sql .= " (";
	
		
		foreach($array as $key => $value)
		{
			if($key != 'table' and $key != 'Acc')
			{
				$sql .= $key.",";
				
			}
	
			
		}
		$sql = substr_replace($sql, "", -1);
		$sql .= ") values ( ";
		
		foreach($array as $key => $value)
		{
			if($key != 'table' and $key != 'Acc')
			{
				$sql .= "'".$value."',";
				
			}
	
			
		}
		$sql = substr_replace($sql, "", -1);
		$sql .= ") ";		
		
		return $sql;
 	}
	
	
	public function update($table,$array,$id)
	{

		$sql = "update ".$table;
		$sql .= " SET ";
	
		
		foreach($array as $key => $value)
		{
			if($key != 'table' and $key != 'Acc')
			{
				$sql .= $key."  = '".$value."',";
				
			}
			
		}	

		$sql = substr_replace($sql, "", -1);	
		
		$sql .= " where id = ".$id;		
		
		//$this->query($sql);
		
		return $sql;
 	}
	
	public function persist_prefactura()
	{
		$sql = $this->update('prefactura'.$this->ext_table,$this->request->prefact,$this->request->id);
		echo $sql;
	}
	

	public function convert_objects($query)
	{
		if($query != null)
		{
			$rows = array();
			while($row = mysql_fetch_object($query)){
				
				array_push($rows, $row);
			}
			return $rows;
		}
		return null;
	}
	
	public function convert_object($query)
	{
		if($query != null)
		{
			$row = mysql_fetch_object($query);
			return $row;
		}
		return null;
	}
	
	
	public function get_solicitud_factura()
	{
		$process = 1;
		$and = " ";
		if(isset($this->request->aseguradora))
		{
			$process = 2;
			$and .= " and  a.nombre = '".$this->request->aseguradora."' ";
		}
		$sql = "select sol.*, a.nombre as aseguradora , s.numero as numero_siniestro, s.id as siniestro_id , conc.nombre as concepto , conc.porc_iva as iva , conc.id as concepto_id from solicitud_factura".$this->ext_table." as sol inner join siniestro as s on sol.siniestro = s.id inner join aseguradora as a on s.aseguradora = a.id inner join concepto_fac as conc on sol.concepto = conc.id  where fecha_proceso = 0 ".$and." ;";
		//echo $sql;
		$query = $this->query($sql);
		$solicitudes = $this->convert_objects($query);
		$sql = "Select * from aseguradora";
		$query = $this->query($sql);
		$aseguradoras = $this->convert_objects($query);
		ob_start();
		include('/var/www/html/public_html/Control/operativo/views/subviews/solicitudes_factura.html');
		$html = ob_get_clean();
		return $html;
		
	}
	
	public function reload_with_aseg()
	{
		$aseg = $_POST["aseg"];
		if(is_numeric($aseg))
		{
			$and = "and a.id = ".$aseg;
		}
		else
		{
			$and = "";
		}
		
		$sql = "select sol.*, a.nombre as aseguradora , s.numero as numero_siniestro, s.id as siniestro_id , conc.nombre as concepto , conc.porc_iva as iva , conc.id as concepto_id from solicitud_factura".$this->ext_table." as sol inner join siniestro as s on sol.siniestro = s.id inner join aseguradora as a on s.aseguradora = a.id inner join concepto_fac as conc on sol.concepto = conc.id  where fecha_proceso = 0 ".$and;
		//echo $sql;
		$query = $this->query($sql);
		$solicitudes = $this->convert_objects($query);
		$sql = "Select * from aseguradora";
		$query = $this->query($sql);
		$aseguradoras = $this->convert_objects($query);
		ob_start();
		include('/var/www/html/public_html/Control/operativo/views/subviews/tabla_solicitud_factura.html');
		$html = ob_get_clean();
		return $html;
	}
	
	
	public function get_concepto_fac()
	{
		$sql = "Select * from concepto_fac where id = ".$this->request->detalle->concepto." LIMIT 1";
		$query = $this->query($sql);
		$concepto = $this->convert_object($query);
		return array("status"=>1,"concepto"=>$concepto);
	}	
	
	public function grabar_datos_prefactura()
	{	
	
		foreach($this->request->solicitudes as $solicitud)
		{
			$sql = "select  * from facturad".$this->ext_table." where idsolicitud = ".$solicitud->id;
			$query = $this->query($sql);
			$detalles = $this->convert_object($query);
			
			if($detalles != null)
			{
				return array("status"=>1,"message"=>"Las solicitudes ya fuerón asignadas a otra prefactura");
			}
		
		}
		
		//Validación de detalles de factura previo a crear todo.
		
		$sql = $this->insert('prefactura'.$this->ext_table,$this->request->pre_factura);
		
		//echo $sql;
		$this->query($sql);
		//echo "<br>";
		$id = $this->get_last_id_prefact();
		
		//echo $id;	
		//echo "<br>";
		foreach($this->request->detalles as $detalle)
		{
			$detalle->prefactura = $id;
			$sql = $this->insert('facturad'.$this->ext_table,$detalle);
			$this->query($sql);
			//echo $sql;	
		}
		//echo "<br>";
		foreach($this->request->solicitudes as $solicitud)
		{			
			$array = array("fecha_proceso"=>date('Y-m-d'));
			$sql = $this->update("solicitud_factura".$this->ext_table,$array,$solicitud->id);		
			$this->query($sql);
			//echo $sql;	
		}
		
		return array("status"=>1,"message"=>"Prefactura creada");
	}
	
	public function add_detalle_prefactura()
	{
		foreach($this->request->solicitudes as $solicitud)
		{
			$sql = "select  * from facturad".$this->ext_table." where idsolicitud = ".$solicitud->id;
			$query = $this->query($sql);
			$detalles = $this->convert_object($query);
			
			if($detalles != null)
			{
				return array("status"=>1,"message"=>"Las solicitudes ya fuerón asignadas a otra prefactura");
			}
		
		}		
		
		$subtotal = $this->request->prefactura->subtotal;
		$iva = $this->request->prefactura->iva;
		$total = $this->request->prefactura->total;
		
		foreach($this->request->detalles as $detalle)
		{
			$detalle->prefactura = $this->request->prefactura->id;
			$sql = $this->insert('facturad'.$this->ext_table,$detalle);
			$this->query($sql);
			$subtotal += $detalle->total;
			$iva += $detalle->iva;
			$total += ($detalle->total+$detalle->iva);			
		}
		
		$array = array("subtotal"=>$subtotal,"iva"=>$iva,"total"=>$total);
		
		$sql = $this->update("prefactura".$this->ext_table,$array,$this->request->prefactura->id);
		$this->query($sql);
		
		
		foreach($this->request->solicitudes as $solicitud)
		{			
			$array = array("fecha_proceso"=>date('Y-m-d'));
			$sql = $this->update("solicitud_factura".$this->ext_table,$array,$solicitud->id);		
			$this->query($sql);
			
		}
	
		return array("status"=>1,"message"=>"Detalles de factura generados");
		
		
			
	}
	
	public function edit_detalles_prefactura()
	{
		$sql = $this->update("facturad".$this->ext_table,$this->request->detalles,$this->request->detalles->id);
		$this->query($sql);
		return array("status"=>1,"message"=>"Detalle de factura editado");
	}
	
	public function edit_prefactura()
	{
		$sql = $this->update("prefactura".$this->ext_table,$this->request->prefactura,$this->request->prefactura->id);
		$this->query($sql);
		return array("status"=>1,"message"=>"Prefactura editada");
	}
	
	public function get_last_id_fact()
	{
		$sql = "Select id from factura".$this->ext_table." order by id desc limit 1 ";
		$query = $this->query($sql);
		$factura = $this->convert_object($query);
		$factura = $factura;
		if($factura != null)
		{
			$id = $factura->id;	
		}
		else
		{
			$id = 1;	
		}	
		return $id;
	}
	
	public function get_last_id_prefact()
	{
		$sql = "Select id from prefactura".$this->ext_table." order by id desc limit 1 ";
		$query = $this->query($sql);
		$factura = $this->convert_object($query);
		if($factura != null)
		{
			$id = $factura->id;	
		}
		else
		{
			$id = 1;	
		}	
		return $id;
	}
	
	public function facturad_prefact_fact($prefact_id,$fact_id)
	{
		$sql = "select from facturad".$this->ext_table." where prefactura = ".$prefact_id;
		$query = $this->query($sql);
		$prefacturas = $this->convert_objects($query);

		foreach($prefacturas as $prefactura)
		{
			$array = array("factura"=>$fact_id);
			$sql = $this->update('detalled'.$this->ext_table,$array,$prefactura->id);
		}
	}
	
	public function get_prefacturas()
	{	
		
		$sql = "Select pf.*, c.nombre, c.apellido, fc.id as factura ,(select a.nombre from  facturad".$this->ext_table." as fd 
		inner join siniestro as s on fd.id_siniestro = s.id inner join aseguradora as a on s.aseguradora = a.id 
		where fd.prefactura = pf.id limit 1) as aseguradora from prefactura".$this->ext_table." as pf inner join cliente 
		as c on pf.cliente = c.id  left join factura".$this->ext_table." as fc on fc.prefactura = pf.id having factura is null";
		
		$query = $this->query($sql);
		$prefacturas = $this->convert_objects($query);
		
		return $prefacturas;
	}
	
	
	public function get_prefacturas_detalles()
	{
		$sql = "select f.*,c.nombre as concepto_nombre from facturad".$this->ext_table." as f inner join concepto_fac as c on f.concepto = c.id where prefactura = ".$this->request['factura'];
		
		//echo $sql;
		
		$query = $this->query($sql);
		$detalles = $this->convert_objects($query);
		
		$res = array();	
		
		foreach($detalles as $det)
		{
			$map = array_map('utf8_encode', (array)$det);
			array_push($res,$map);
		}
		
		return $res;
	}
	
	
	public function verify_prefact_siniester()
	{
		$sql = "select * from facturad".$this->ext_table." where id_siniestro = ".$this->request['id_siniestro'];
		$query = $this->query($sql);
		$detalled = $this->convert_objects($query);
		$detalled = $detalled[0];
		
		/*if($detalled == null)
		{
			return array("status"=>1);
		}
		else
		{
			return array("status"=>2);
		}*/
		
		return array("status"=>1); 
		
	}

	public function verify_prefact_on_fact($id)
	{
		$sql = "Select * from factura".$this->ext_table." where prefactura = ".$id;
		$query = $this->query($sql);
		$factura = $this->convert_object($query);
		return $factura;
	}	
	
	
	public function aprove_fact()
	{
		$cons_process = $this->generate_consecutivo_fact();
		$factura = $cons_process["factura"];	
			
		if(isset($factura->autorizadopor))
		{
			return array("status"=>1,"message"=>" La factura ya fue aprobada por ".$factura->autorizadopor,"consecutivo"=>$factura->consecutivo);	
		}			
		$array = array("autorizadopor"=>$this->request->prefactura->nombre." ".$this->request->prefactura->apellido);
		$sql = $this->update("factura".$this->ext_table,$array,$factura->id);
		$this->query($sql);
		
	
		return array("status"=>1,"message"=>" Factura aprobada con consecutivo ".$factura->consecutivo,"consecutivo"=>$factura->consecutivo);
	}
	
	public function generate_consecutivo_fact()
	{		
		$factura = $this->verify_prefact_on_fact($this->request->prefactura->id);		
		if($factura == null)
		{			
			
			$consecutivo = $this->gen_consecutivo();
			
			$sql = "select * from aseguradora where nombre = '".$this->request->prefactura->aseguradora."'";
			
			$query = $this->query($sql);
			
			$aseguradora = $this->convert_object($query);
			
			$last_factura = $this->get_last_id_fact();
			
			//echo $last_factura;
			
			
			$array = array(
				"id"=>$last_factura+1,
				"consecutivo"=>$consecutivo,
				"cliente"=>$this->request->prefactura->cliente,
				"fecha_emision"=>$this->request->prefactura->fecha_emision,
				"fecha_vencimiento"=>$this->request->prefactura->fecha_vencimiento,
				"subtotal"=>$this->request->prefactura->subtotal,
				"iva"=>$this->request->prefactura->iva,
				"total"=>$this->request->prefactura->total,				
				"aseguradora"=>$aseguradora->id,
				"prefactura"=>$this->request->prefactura->id
			);
			
			
			
			$sql = $this->insert("factura".$this->ext_table,$array);	
			
			$this->query ($sql);
			
			$object = (object) $array;
			
			$sql = "Select * from facturad".$this->ext_table." where prefactura =  ".$this->request->prefactura->id;
			
			$query = $this->query($sql);
			
			$detalles = $this->convert_objects($query);
			
			foreach($detalles as $detalle)
			{
				$array = array("factura"=>$last_factura+1);
				$sql = $this->update("facturad".$this->ext_table,$array,$detalle->id);
				$this->query($sql);	
			}		
			
			$this->update_consecutivo_fact($consecutivo);
			
			return array("status"=>1,"message"=>" Factura generada con consecutivo ".$object->consecutivo,"consecutivo"=>$consecutivo,"factura"=>$object);	
		}
		else
		{			
			$fact_elect = new factura_electronica($factura,$this->ext_table);
			$fact_elect->generar_factura_electronica();
			
			return array("status"=>1,"message"=>" Factura generada con consecutivo ".$factura->consecutivo,"consecutivo"=>$factura->consecutivo,"factura"=>$factura);
		}
		
	}
	
	
	public function update_consecutivo_fact($consecutivo)
	{
		$array = array("consecutivo_aoa"=>$consecutivo);
		$sql = $this->update('cfg_factura'.$this->ext_table,$array,'1');	
		$this->query ($sql);
	}	
	
	public function gen_consecutivo()
	{
		$sql = "select consecutivo_aoa from cfg_factura".$this->ext_table;
		$query = $this->query($sql);
		$consecutivo = $this->convert_object($query);
		$consecutivo = $consecutivo->consecutivo_aoa+1;
		return $consecutivo;
	}
	
	public function delete_detalles_prefactura()
	{
		$sql = "select * from facturad".$this->ext_table." where id = ".$this->request['id'];
		$query = $this->query($sql);
		$detalle = $this->convert_object($query);
		
		$sql="Select * from solicitud_factura".$this->ext_table." where id = ".$detalle->idsolicitud;
		$query = $this->query($sql);
		$solicitud = $this->convert_object($query);
		
		$array = array("fecha_proceso"=>0);
		
		$sql = $this->update("solicitud_factura".$this->ext_table,$array,$solicitud->id);
		$this->query($sql);
		
		
		$sql = "delete from facturad".$this->ext_table." where id = ".$this->request['id'];
		$this->query($sql);
		return array("status"=>1,"message"=>"Detalle de prefactura eliminado");
	}
	
	public function delete_prefactura()
	{
		$sql = "select * from facturad".$this->ext_table." where prefactura = ".$this->request['id'];
		$query = $this->query($sql);
		$detalles = $this->convert_objects($query);
		if($detalles != null)
		{			
			foreach($detalles as $detalle)
			{
				$sql = "update solicitud_factura".$this->ext_table." set fecha_proceso = 0 where id = ".$detalle->idsolicitud;	
				$this->query($sql);
				$sql = "Delete from facturad".$this->ext_table." where id = ".$detalle->id;				
				$this->query($sql);
			}	 
		}			
		$sql = "delete from prefactura".$this->ext_table." where id = ".$this->request['id'];
		$this->query($sql);
		return array("status"=>1,"message"=>"prefactura eliminada");
	}
	
	public function get_fact_prefact()
	{
		$factura = $this->verify_prefact_on_fact($this->request->prefactura->id);
		if($factura == null)
		{
			return array("status"=>2,"message"=>"Aun no existe una factura relacionada a esta prefactura");
		}
		else
		{			
			if($this->ext_table == "")
			{
				$mode = 1;
			}
			else
			{
				$mode = 2;
			}
			return array("status"=>1,"message"=>"Factura generada","factura"=>$factura->id,"mode"=>$mode);
		}
	}
	
}



?>