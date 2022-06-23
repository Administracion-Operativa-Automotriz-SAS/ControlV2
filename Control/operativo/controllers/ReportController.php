<?php
include_once(dirname(__FILE__).'/../config/config.php');


if($_POST){
	include_once(dirname(__FILE__).'/../config/resuelve.php');
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL); 

	header('Content-type: text/html; charset=utf-8');
	$inside = new ReportController();
	if(isset($_POST['excel_generate']))
	{
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=Reporte Siniestros citas.xls");  
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
		//print_r($_POST);
		/*if($_POST['excel_generate']!=null)
		{
			if($_POST['param2']!=null){$inside->excel_generate($_POST['excel_generate'],$_POST['param2'],$_POST['param3']);}
			else{$inside->excel_generate($_POST['excel_generate'],null,null);}			
		}*/
	}
	if(isset($_POST['excel_attr']))
	{
		if($_POST['excel_attr']==1)
		{
			//echo "genero excel";
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=Reporte Siniestros citas.xls");  
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
		}
		//print_r($_POST);
		//$inside->excel_finan_generate($_POST['finanparam1'],$_POST['finanparam2'],$_POST['finanparam3'],$_POST['cober9'],$_POST['cober12'],$_POST['iva_val']);
		
	}
	
	if(isset($_POST['gen_cartera']))
	{
		//echo "web service";
		$report = new ReportController;
		$report->ver_cartera($_POST['siniestro']);
		
	}
	
}




class ReportController
{
	 function __construct(){
		//echo "me construyeron";
       
    }
	
	public function query($cadena, $Devolver_sql = 0,&$_Cantidad_registros_afectados=0) // corre un query invocado internamente
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
				enviar_gmail("sistemas@aoaoclombia.com",'Gestion de Procesos','sergiocastillo@aoacolombia.com,Sergio Castillo','',"Mysql Error",
				"<H3>Error MySQL </H3>Instruccion: $cadena<br>Error: $Error_de_mysql <br>Usuario: ".$_SESSION['User']."-".$_SESSION['Nick']);
				die();
			}
		}
	}
	
	public function siniestro($id){
		
		$query = $this->query("Select * from siniestro where id = ".$id);
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
	

	//informes siniestros
	public function siniestros($aseguradora,$fecha1,$fecha2){
		if($fecha1!=null)
		{
			$secondcondition = " and s.fecha_inicial between '".$fecha1."' and '".$fecha2."' ";
		}
		else{
			$secondcondition = " ";
		}
		$query = $this->query("Select id from aseguradora where razon_social like '%".$aseguradora."%'");
		$arrstring = "(";
		while($row = mysql_fetch_object($query)){
				$arrstring = $arrstring.$row->id.",";
		}
		$arrstring = substr_replace($arrstring, "", -1);
		$arrstring = $arrstring.")";
		$query = $this->query("Select s.*,c.flota as flota,c.placa as cita_placa from siniestro as s inner join cita_servicio as c on c.siniestro = s.id  where  c.estado = 'C' and s.aseguradora in ".$arrstring.$secondcondition);
		//$query = $this->query("Select distinct (s.id),s.*,c.flota as flota,c.placa as cita_placa from siniestro as s inner join cita_servicio as c on c.siniestro = s.id  where s.id = 1216054 and s.aseguradora = 55");
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
	
	//informes control garantias
	public function siniestros_control_garantias($fecha1,$fecha2){
		if($fecha1!=null)
		{			
			$fecha2 = date('Y-m-d', strtotime($fecha2 .' +1 day'));
			$secondcondition = " and sin.fecha_proceso between '".$fecha1."' and '".$fecha2."'";
			//echo $fecha2;
		}
		else{
			$time = time();
			$tomorrow = date("Y-m-d", mktime(0,0,0,date("n", $time),date("j",$time)+ 1 ,date("Y", $time)));
			$default_text = " and sin.fecha_proceso between '".date('Y-m-01')."' and '".$tomorrow."' ";
			$secondcondition = $default_text;
		}
		$sql = "select s.id as id, sin.fecha_proceso, s.numero , ase.nombre as aseguradora , es.nombre as estado, fr.nombre as tipo_garantia, sin.valor as valor_garantia, fac.total as valor_total, re.valor as recibo_valor, nota.valor as nota_valor, sin.metodo_devol  from siniestro as s inner join estado_siniestro as es on s.estado = es.id inner join aseguradora as ase on ase.id = s.aseguradora inner join sin_autor as sin on sin.siniestro = s.id inner join franquisia_tarjeta as fr on fr.id = sin.franquicia left join factura as fac on fac.siniestro = s.id left join recibo_caja as re on re.autorizacion = sin.id left join nota_contable as nota on nota.factura = fac.id left join factura as fact2 on fact2.garantia = sin.id where s.estado in (3,7,8) and sin.franquicia != 10 and sin.estado = 'A' and sin.aut_fac = 0 and fac.siniestro != 0 and fac.anulada != 1  and  (fact2.id is null and fac.garantia = 0 or fact2.id is not null and fac.id = fact2.id or fac.siniestro is null  and fac.anulada is null) ".$secondcondition." 
		union select s.id as id, sin.fecha_proceso, s.numero , ase.nombre as aseguradora , es.nombre as estado, concf.nombre as tipo_garantia, sin.valor as valor_garantia, fac.total as valor_total, re.valor as recibo_valor, nota.valor as nota_valor, sin.metodo_devol  from siniestro as s inner join estado_siniestro as es on s.estado = es.id left join factura as fac on fac.siniestro = s.id inner join facturad as facd on facd.factura = fac.id inner join aseguradora as ase on ase.id = s.aseguradora inner join sin_autor as sin on sin.siniestro = s.id inner join concepto_fac as concf on concf.id = facd.concepto left join recibo_caja as re on re.autorizacion = sin.id  left join nota_contable as nota on nota.factura = fac.id where facd.concepto = 33 and s.estado in (3,7,8) and sin.estado = 'A' and fac.siniestro != 0 and fac.anulada != 1 and re.factura = fac.id ".$secondcondition."  order by id desc limit 10000";
		
		//echo $sql;
		$query = $this->query($sql);

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
	
	public function cities(){
		$query = $this->query("Select codigo,nombre from ciudad ");
		$rows = array();
		while($row = mysql_fetch_object($query)){
			$rows[$row->codigo]=$row->nombre;
		}
		return $rows;
	}
	
	//consulta para ver tabla de carros, solo es de consulta
	
	public function cars(){
		$query = $this->query("Select * from vehiculo where placa like '%HKT158%'");
		$rows = array();
		while($row = mysql_fetch_object($query)){
			array_push($rows, $row);
		}
		return $rows;
	}
	
	//consulta para ver las tablas de BD solo es de consulta
	public function tablesn(){
		$query = $this->query("SELECT table_name FROM information_schema.tables where table_schema='aoacol_aoacars';");
		$rows = array();
		while($row = mysql_fetch_object($query)){
			array_push($rows, $row);
		}
		return $rows;
	}
	
	public function aseguradoras(){
		$query = $this->query("SELECT distinct(razon_social) from aseguradora order by razon_social");
		$rows = array();
		while($row = mysql_fetch_object($query)){
			array_push($rows, $row);
		}
		return $rows;
	}
	
	public function aseguradora($aseguradora)
	{
		$query = $this->query("SELECT nombre from aseguradora where id = ".$aseguradora);
		$rows = array();
		while($row = mysql_fetch_object($query)){
			array_push($rows, $row);
		}
		return $rows[0]->nombre;
	}
	
	//consulta para mostrar los resultados de la cita de un siniestro especifico, solo es de consulta
	//la columna siniestro no hace ref al No. del siniestro si no al ID.
	public function citas(){
		$query = $this->query("SELECT * FROM  cita_servicio where siniestro = 1231654 and estado = 'C'");
		$rows = array();
		while($row = mysql_fetch_object($query)){
			array_push($rows, $row);
		}
		return $rows;
	}
	
	//consulta para ver el contenido de la tabla, solo es de consulta
	public function vehiculos_linea(){
		$query = $this->query("SELECT * FROM  linea_vehiculo");
		$rows = array();
		while($row = mysql_fetch_object($query)){
			array_push($rows, $row);
		}
		return $rows;	
	}
	
	public function cita_placa_vehiculo($siniestro)
	{
		$query = $this->query("SELECT * FROM  cita_servicio where estado = 'C' and siniestro = ".$siniestro);
		if($query != null)
		{
			$rows = array();
			while($row = mysql_fetch_object($query)){
				array_push($rows, $row);
			}
		}
		else{return null;}		
		$query = $this->query("Select * from vehiculo where placa like '%".$rows[0]->placa."%'");
		if($query != null)
		{
			$rows = array();
			while($row = mysql_fetch_object($query)){
				array_push($rows, $row);
			}
				//return print_r($rows);
		}
		else{return null;}		
		$query = $this->query("SELECT * FROM  linea_vehiculo where id = ".$rows[0]->linea);
		if($query != null)
		{
			$rows = array();
			while($row = mysql_fetch_object($query)){
				array_push($rows, $row);
			}
			return $rows[0]->nombre;
		}
		else{return null;}
	}
	
	public function seguimiento($siniestro)
	{
		$query = $this->query("Select * from seguimiento where siniestro = ".$siniestro);
		$rows = array();
		while($row = mysql_fetch_object($query)){
			array_push($rows, $row);
		}
		return $rows;
		
	}
	
	
	
	
	
	public function column_name()
	{
		$query = $this->query("Select distinct TABLE_NAME from INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME IN ('Contacto') and TABLE_SCHEMA = 'aoacol_aoacars' ");
		$rows = array();
		while($row = mysql_fetch_object($query)){
			array_push($rows, $row);
		}
		return $rows;
	}
	
	public function cita_entrega($siniestro)
	{
		$query = $this->query("SELECT * FROM  cita_servicio where estado = 'C' and siniestro =  ".$siniestro);
		if($query != null)
		{
			$rows = array();
			while($row = mysql_fetch_object($query)){
				array_push($rows, $row);
			}
			return $rows[0]->momento_entrega;
		}
		
		return null;
		
	}
	
	public function cita_contacto($siniestro)
	{
		$query = $this->query("select concat(fecha,' ',hora) as momento from seguimiento where siniestro =".$siniestro." and tipo>2 order by id limit 1");
		if($query != null)
		{
			$rows = array();
			while($row = mysql_fetch_object($query)){
				array_push($rows, $row);
			}
			return $rows[0]->momento;
		}
		
		return null;
		
	}
	
	public function cita($siniestro)
	{
		$query = $this->query("SELECT * FROM  cita_servicio where estado = 'C' and siniestro =  ".$siniestro);
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
	
	public function excel_generate($param1,$fecha1,$fecha2)
	{		
		$siniestros = $this->siniestros($param1,$fecha1,$fecha2);
		$ciudades = $this->cities();
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=Reporte Siniestros citas.xls");  
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		$tablehead = "<table>
				<thead>
					<tr>
						<th>ID</th>
						<th>CIUDAD ".utf8_encode("ATENCIÓN")."</th>
						<th>CIUDAD ORIGEN</th>
						<th>CIUDAD SINIESTRO</th>
						<th>INGRESO</th>
						<th>NOMBRE ASEGURADO</th>
						<th>NUMERO ".utf8_encode("TELEFÓNICO")."</th>
						<th>PLACA</th>
						<th>COBERTURA</th>
						<th>CONTACTO</th>
						<th>SINIESTRO</th>
						<th>ENTREGA</th>
						<th>ANS.</th>
						<th>FECHA INICIO SERVICIO</th>
						<th>FECHA FIN</th>
						<th>PLACA DE LA CITA</th>
						<th>VEHICULO</th>
						<th>SUCURSAL RADICADORA</th>					
					</tr>
				</thead>";
		$tablebody = "<tbody>";
					 if($siniestros!=null) {foreach($siniestros as $siniestro){ 
		$tablebody .=		"<tr>";
		$tablebody .=				"<td>".$siniestro->id."</td>";
		if(isset($ciudades[$siniestro->ciudad]))
		{ $ciudad = $ciudades[$siniestro->ciudad];}
		else{ $ciudad = ""; }
		$tablebody .=				"<td>".$ciudad."</td>";
		if(isset($ciudades[$siniestro->ciudad_original]))
		{ $ciudad = $ciudades[$siniestro->ciudad_original];}
		else{ $ciudad = ""; }
		$tablebody .=				"<td>".$ciudad."</td>";
		if(isset($ciudades[$siniestro->ciudad_siniestro]))
		{ $ciudad = $ciudades[$siniestro->ciudad_siniestro];}
		else{ $ciudad = ""; }
		$tablebody .=				"<td>".$ciudad."</td>";
		$tablebody .=				"<td>".$siniestro->ingreso ."</td>";
		$tablebody .=				"<td>".utf8_encode($siniestro->asegurado_nombre)."</td>";
		$tablebody .=				"<td>".$siniestro->declarante_celular ."</td>";
		$tablebody .=				"<td>".$siniestro->placa."</td>";
		$tablebody .=				"<td>".$siniestro->dias_servicio ."</td>";
		$tablebody .=				"<td>".$this->cita_contacto($siniestro->id)."</td>";
		$tablebody .=				"<td>".$siniestro->numero."</td>";
		$tablebody .=				"<td>".$this->cita_entrega($siniestro->id)."</td>";
		$ans = $this->difference_hours($siniestro->ingreso,$this->cita_contacto($siniestro->id));
		$tablebody .=				"<td>".$ans."</td>";
		$tablebody .=				"<td>".$siniestro->fecha_inicial ."</td>";
		$tablebody .=				"<td>".$siniestro->fecha_final ."</td>";
		$tablebody .=				"<td>".$siniestro->placa ."</td>";
		$tablebody .=				"<td>".$this->cita_placa_vehiculo($siniestro->id) ."</td>";
		$tablebody .=				"<td>".$siniestro->sucursal_radicadora ."</td>";
		$tablebody .=			"</tr>";
					 }} 
		$tablebody .= 	"</tbody>";
		$tablebody .= "</table>";

		$table = $tablehead.$tablebody;
		echo $table;
		
	}
	

	
	private function difference_hours($ingreso,$contacto)
	{
		$date1 = strtotime($ingreso);
		$date2 = strtotime($contacto);
		$all = floor(($date2 - $date1) / 60);
		$d = floor ($all / 1440);
		$h = floor (($all - $d * 1440) / 60);
		$m = $all - ($d * 1440) - ($h * 60);
		//return $all;
		return $d." dias con ".$h." horas con ".$m." minutos";
	}
	
	public function ver_cartera($id)
	{
		$sql = "select fac.id, fac.consecutivo, fac.fecha_emision, fac.fecha_vencimiento, fac.total  from siniestro as s inner join factura as fac on s.id = fac.siniestro where s.id = ".$id;
		$query = $this->query($sql);
		$rows = array();
		while($row = mysql_fetch_object($query)){
			array_push($rows, $row);
		}
		
		$total_fac = 0;
		
		$table = "<table class='table table-bordered table-hover' style='text-align:center;'>
			<thead>
				<tr>
					<th>Consecutivo</th>
					<th>Fecha emisión</th>
					<th>Fecha vencimiento</th>
					<th>Valor factura</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody>";
		foreach($rows as $row)
		{
			$total_fac += $row->total;
			$table .= "<tr>
				<td>".$row->consecutivo."</td>
				<td>".$row->fecha_emision."</td>
				<td>".$row->fecha_vencimiento."</td>
				<td>".$row->total."</td>
				<td> <a target='_blank' href='zfacturacion.php?Acc=imprimir_factura&id=".$row->id."'><button class='btn-xs btn btn-success'>Ver<br>factura</button></a> </td>
			</tr>";
		}		
		$table .= "<tr>
			<td colspan=3>Total</td>
			<td colspan=2>".$total_fac."</td>
			</tr>";
		$table .="</tbody>
		</table>";
		
		echo $table;

	}
	
}



?>