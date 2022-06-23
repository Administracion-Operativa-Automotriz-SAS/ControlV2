<?php
include('inc/funciones_.php');
sesion();

$Ahora=date('Y-m-d H:i:s');

if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}
captura();

function captura()
{
  global $Ahora;
  html('AOA COLOMBIA S.A. - REGISTRO DE PQR');
  echo "
  <script language='javascript'>
  function valida_solicitud()
  {
    with(document.forma)
    {
		if(!alltrim(cliente.value)) { alert('Debe digitar nombres y apellidos.'); cliente.style.backgroundColor='ffffcc'; cliente.focus(); return false;}
		if(!alltrim(direccion.value) && !alltrim(telefono.value) && !alltrim(celular.value) && !alltrim(email_e.value)) 
			{ alert('Debe digitar al menos una dirección, teléfono, celular o correo electrónico para contactarnos con usted.'); 
		direccion.style.backgroundColor='ffffcc';telefono.style.backgroundColor='ffffcc'; celular.style.backgroundColor='ffffcc'; email_e.style.backgroundColor='ffffcc'; direccion.focus(); return false;}
		if(!alltrim(placa.value)) { alert('Debe digitar la placa de su vehículo.'); placa.style.backgroundColor='ffffcc'; placa.focus();return false;}
		if(!aseguradora.value) { alert('Debe seleccionar la Aseguradora.'); aseguradora.style.backgroundColor='ffffcc'; return false;}
		if(!oficina.value) { alert('Debe seleccionar la oficina.'); oficina.style.backgroundColor='ffffcc'; return false;}
		if(!tipo.value) { alert('Debe seleccionar el tipo de solicitud.'); tipo.style.backgroundColor='ffffcc';return false;}
		if(!alltrim(descripcion.value)) { alert('Debe digitar la descripción de su petición, queja, reclamo o suerencia.'); descripcion.style.backgroundColor='ffffcc'; descripcion.focus();return false;}
		enviar.disabled=true;
		submit();
    }
  }
	function cerrar_fpqr()  {	window.close();void(null);  }
	</script>
	<body>
		<center><img src='https://app.aoacolombia.com/Control/operativo/img/LOGO_AOA_200.png' border='0' height='100px'></center>
		<h3 align='center'>CAPTURA DE PETICION - QUEJA - RECLAMO - SUGERENCIA</H3>
		<form action='zpqr.php' method='POST' target='pqr_oculto' name='forma' id='forma'>
		<table align='center' bgcolor='eeeeee'>
		<tr><td align='right'>Nombres completos:</td><td><input type='text' name='cliente' id='cliente' value='' size='70' maxlength='100' onblur='this.value=this.value.toUpperCase();' alt='Nombres y Apellidos' title='Nombres y Apellidos'></td><tr>
		<tr><td align='right'>Dirección:</td><td><input type='text' name='direccion' id='direccion' value='' size='70' maxlength='100'></td></tr>
		<tr><td align='right'>Teléfono:</td><td><input type='text' name='telefono' id='telefono' value='' size='20' maxlength='20'></td></tr>
		<tr><td align='right'>Celular:</td><td><input type='text' name='celular' id='celular' value='' size='20' maxlength='20'></td></tr>
		<tr><td align='right'>Correo electrónico:</td><td><input type='text' name='email_e' id='email_e' value='' size='70' maxlength='100'></td></tr>
		<tr><td align='right'>Placa Vehículo:</td><td><input type='text' name='placa' id='placa' value='' size='10' maxlength='8' onblur='this.value=this.value.toUpperCase();' ></td></tr>
		<tr><td align='right'>Aseguradora:</td><td>".menu1("aseguradora","select id,nombre from pqr_aseguradora where activo != 1 ",0,1,"font-size:12px;")."</td></tr>
		<tr><td align='right'>Ciudad:</td><td> ".menu1("oficina","select id,nombre from oficina where 
		nombre !='W TALLER AOACOLOMBIA' and 
		nombre !='Z PATIO BOGOTA' and 
		nombre != 'Y USADOS' and 
		nombre != 'XTRANS ORIENTE' and
		nombre != 'Y VENTA FLOTA' and
		nombre != 'SAN ANDRES';",0,1)."</td></tr>
		<tr><td align='right'>Tipo Solicitud:</td><td> ".menu1("tipo","select id,nombre from pqr_tipo where activo != 1",0,1,"font-size:12px;"," ")."</td></tr>
		<tr><td align='right'>Fecha y Hora:</td><td> <input type='text' name='fecha' value='$Ahora' size=20 readonly></td></tr>
		
		<tr><td align='right'>Fecha de recibido:</td><td> ".pinta_FCHORA('forma','FA',$FA)."</td></tr>
		<tr><td align='right'>Fecha de alta:</td><td>".pinta_FCHORA('forma','FB',$FB)." </td></tr>
		<tr><td align='right'>Fecha de vencimiento:</td><td> ".pinta_FCHORA('forma','FC',$FC)."</td></tr>
		
		<tr><td align='right'>Fuente queja:</td><td>".menu1("queja","SELECT id,nombre FROM fuente_queja",0,1,"font-size:12px;")."</td></tr>
		
		<tr><td align='right'>Consecutivo aseguradora:</td><td><input type='text' name='consecutivo' id='consecutivo' value='' size='20' maxlength='100'></td></tr>
		<tr><td align='right'>Descripcion:</td><td><textarea name='descripcion' rows=4 cols=80 style='font-size:12px'></textarea></tr></td>
		
		<input type='hidden' name='Acc' value='grabar_solicitud'>
		<input type='hidden' name='registrado_por' value='".$_SESSION['Nombre']."'><input type='hidden' name='estado' value='1'>
		<tr><td colspan='2' align='center'><input type='button' name='enviar' id='enviar' value=' Enviar PQR '  style='font-family:arial;font-size:18px;font-weight:bold; width:400px;' onclick='valida_solicitud()'></tr><td>
		</table>
		</form> 
		<iframe name='pqr_oculto' id='pqr_oculto' width=1 height=1 style='visibility:hidden'></iframe>
		<script language='javascript'>centrar(800,600);</script>
	</body>";
}

function grabar_solicitud()
{
	global $cliente,$tipo,$fecha,$descripcion,$registrado_por,$estado,$oficina,$direccion,$telefono,$celular,$email_e,$placa,$aseguradora,$FA,$FB,$FC,$queja,$consecutivo;
	$Id_nuevo=q("insert into pqr_solicitud (cliente,tipo_solicitud,fecha,descripcion,registrado_por,estado,oficina,direccion,telefono,celular,email_e,placa,aseguradora,consecutivo,fecha_recibido,fecha_alta,fecha_vencimiento,fuente_queja_id) values 
	('$cliente','$tipo','$fecha','$descripcion','$registrado_por','$estado','$oficina','$direccion','$telefono','$celular','$email_e','$placa','$aseguradora','$consecutivo','$FA','$FB','$FC','$queja')");
	graba_bitacora('pqr_solicitud','A',$Id_nuevo,'Adiciona registro');
	$Id_nuevo=str_pad($Id_nuevo,5,'0',STR_PAD_LEFT);
	echo "<body><script language='javascript'>alert('Solicitud PQR $Id_nuevo grabada satisfactoriamente');parent.cerrar_fpqr();</script></body>";
}
  
/*

function buscar_cliente()
{	
	global $busqueda;
	html('BUSQUEDA DE CLIENTES');
	$Partes=explode(" ",$busqueda);
	$instruccion='';
	for($i=0;$i<count($Partes);$i++)
	{
		$instruccion.=($instruccion?" and ":"")." (nombre like '%".$Partes[$i]."%' or apellido like '%".$Partes[$i]."%') ";
	}
	$Clientes=q("select * from cliente where ($instruccion) order by apellido,nombre");
	if($Clientes)
	{
		echo "
		<script language='javascript'>
			function selecciona(dato)
			{
				opener.document.forma.cliente.value=dato;
				window.close();void(null);
				opener.busca_aseguradora(dato);
			}
		</script>
		<body><h3>Busqueda de clientes con el criterio: $busqueda</h3><br>
		Estimado usuario: Seleccione la fila que desea tomar y de doble click.<br><br>
		<table border cellspacing=0><tr>
			<th>Identificacion</th>
			<th>Nombre</th>
			</tr>";
		while($F=mysql_fetch_object($Clientes))
		{
			echo "<tr ondblclick='selecciona($F->id)'><td align='right'>$F->identificacion</td><td>$F->apellido $F->nombre</td></tr>";
		}		
		echo "</table>";
	}
	else
	{
		$NT=tu('cliente','id');
		echo "
		<script language='javascript'>
			function repinta_detalle()
			{
				window.close();void(null);opener.location.reload();
			}
		</script>
		<body><h3>No se encuentra la información, se le solicitará la información completa del cliente.</h3>
		<iframe src='marcoindex.php?Acc=mod_reg&Num_Tabla=$NT&id=0' width='100%' height='80%'></iframe>";
	}
}

function busca_aseguradora()
{
	global $cliente;
	echo $cliente;
	if($Aseguradora=qo1("select aseguradora from factura where cliente=$cliente and aseguradora!=0 order by id desc limit 1"))
	{
		echo "<body><script language='javascript'>parent.document.forma.aseguradora.value=$Aseguradora;</script></body>";
	}
	elseif($Aseguradora=qo1("select aseguradora from factura,recibo_caja where factura.id=recibo_caja.factura and recibo_caja.cliente=$cliente order by factura.id desc limit 1"))
	{
		echo "<body><script language='javascript'>parent.document.forma.aseguradora.value=$Aseguradora;</script></body>";
	}
	else
		echo "<body><script language='javascript'>alert('no encuentro la aseguradora');</script></body>";

}
*/

function centro_de_control_pqr()
{
	global 
	$ASGP, $OFCP, $TIPOP, $ESTADO;
	html('CENTRO DE CONTROL PQR');
	$FI=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-10)));
	$FF=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),15)));
	
	echo "<script language='javascript'>
		function cargar_solicitud()
		{
			if(document.forma.FI.value>document.forma.FF.value)
			{
				alert('Debe seleccionar una fecha inicial menor que la fecha final');
				document.forma.FI.style.backgroundColor='ffffcc';
				document.forma.FF.style.backgroundColor='ffffcc';
				return false;
			}
			document.forma.submit();
		}
	</script>
	<body>
	<h3>CENTRO DE CONTROL PQR</H3>
	<form action='zpqr.php' method='POST' name='forma' target='pqr_control' id='forma'>
	Fecha inicial:".pinta_FC('forma','FI',$FI)." 
	Fecha final: ".pinta_FC('forma','FF',$FF)."
	Aseguradora: ".menu1("ASEG","select id,nombre from aseguradora where id!=6 ",0,1)."
	Oficina: ".menu1("OFC","select id,nombre from oficina",0,1)."
	Numero de PQR: <input type='text' name='PQR' id='PQR' size='5' maxlength='10'>
	Tipo de Solicitud: ".menu1("TIPO","select id,nombre from pqr_tipo",0,1)."
	Estado: ".menu1("EST","select id,nombre from pqr_estado",0,1)."
	<input type='button' value='Consultar' onclick='cargar_solicitud()'>
	<input type='hidden' name='Acc' value='cargar_solicitud'>
	</form> 
	<iframe name='pqr_control' id='pqr_control' width='100%' height='500' FRAMEBORDER='NO' ></iframe>
	</body>";
}
 
function cargar_solicitud()
{
	global $FI,$FF,$ASEG,$OFC,$TIPO,$EST,$id,$PQR;
	
	html();
	$Consulta="select s.id,s.cliente as ncliente, t_oficina(s.oficina) as noficina , tin.nombre as tipo_solicitud_nombre,
               t_pqr_tipo(s.tipo_solicitud) as ntipo, s.fecha, paseg.nombre as naseg,s.descripcion,e.nombre 
               as nestado,e.color_co as cestado,s.registrado_por,t_respuestas_pqr(s.id) as respondido,consecutivo,fecha_recibido,fecha_alta,fecha_vencimiento,placa,s.numero_documento NUMERO_DOCUMENTO 
			   FROM
               pqr_solicitud as s 
			   LEFT join pqr_estado as e on s.estado = e.id 
			   LEFT join pqr_aseguradora as paseg on s.aseguradora = paseg.id 
			   LEFT JOIN pqr_tipo_nombre as tin on s.tipo_solicitud_nombre = tin.id
			   where date_format(s.fecha,'%Y-%m-%d') between '$FI' and '$FF' ".
			   ($OFC?" and s.oficina='$OFC' ":"").($TIPO?" and s.tipo_solicitud='$TIPO' ":"").($ASEG?" and s.aseguradora='$ASEG' ":"").
			   ($EST?" and s.estado='$EST' ":"").($PQR?" and s.id='$PQR' ":"").
				"order by noficina";
	if($Datos=q($Consulta))
	{
		echo "<script language='javascript'>
			function ver_respuestas(dato)
			{
				modal('zpqr.php?Acc=ver_respuestas&id='+dato,0,0,500,800,'respuestas');
			}
		</script>
		<body>
		<h3>Consulta de Solicitudes PQR</h3>
			<table border cellspacing='0' width='100%' style='empty-cells:show;'><tr>
			<th>#</th>
			<th>Numero PQR</th>
			<th>Cliente</th>
			<th>Tipo</th>
			<th>Tipo solicitud</th>
			<th>Fecha Solicitud</th>
			<th>Aseguradora</th>
			<th>Numero de documento</th>
			<th>Descripcion</th>
			<th>Estado</th>
			<th>Consecutivo aseguradora</th>
			<th>Fecha recibido</th>
			<th>Fecha alta</th>
			<th>Fecha vencimiento</th>
			<th>Placa</th>
			<th>Registrado Por</th>
			<th>Con Respuesta</th>
			</tr>
			";
		$Contador=0;
		$Ultima_oficina='';
		//include('inc/link.php');
		while($D=mysql_fetch_object($Datos))
		{
			$Contador++;
			if($Ultima_oficina!=$D->noficina)
			{
				echo "<tr><td colspan=15 align='center' bgcolor='777777' style='color:ffffff'><b>$D->noficina</b></td></tr>";
				$Ultima_oficina=$D->noficina;$Contador=1;
			}
			//if(qo1m("select id from pqr_respuesta where solicitud=$D->id limit 1",$LINK)) 
			if($D->respondido) $icono="<img src='gifs/standar/opcionverde.png' border='0'>"; else $icono='';
			echo "<tr onclick='ver_respuestas($D->id);'>
				<td align='right'>$Contador</td>
				<td align='left'>$D->id</td>
				<td align='left'>$D->ncliente</td>
				<td align='left'>$D->ntipo</td>
				<td align='left'>$D->tipo_solicitud_nombre</td>
				<td align='left'>$D->fecha</td>
				<td align='left'>$D->naseg</td>
				<td align='left'>$D->NUMERO_DOCUMENTO</td>
				<td align='justify'>$D->descripcion</td>
				<td align='left' bgcolor=".cabio_color($D->id,$D->nestado).">".cambia_estado($D->id,$D->nestado)."</td>
				<td align='justify'>$D->consecutivo</td>
				<td align='justify'>$D->fecha_recibido</td>
				<td align='justify'>$D->fecha_alta</td>
				<td align='justify'>$D->fecha_vencimiento</td>
				<td align='justify'>$D->placa</td>
				<td align='left'>$D->registrado_por</td>
				<td align='center'>$icono</td>
				</tr>";
		}
		//mysql_close($LINK);
		echo "</table>";
	}
	else
	{
		echo "No encuentro información.";
	}
}

function cambia_estado($id,$estado){
	
	$Consulta2="select id,t_pqr_solicitud(solicitud) as nsolicitud, t_pqr_tipo_accion(pqr_tipo_accion) as ntipoaccion,descripcion,
	           t_pqr_estado_respuesta(pqr_estado_respuesta) as nestado, fecha, procesado_por, t_resarcimiento_pqr(id) as resarcimiento from pqr_respuesta where solicitud=$id order by id desc limit 1";
	
	/*if ($Datos=q($Consulta2)){
		while($C=mysql_fetch_array($Datos)){
			   $respuesta_estado  = $C->nestado;
		}
	}
	  if(isset($respuesta_estado)){
			$estado_respuesta = $respuesta_estado;
			
		}else{
		  echo $id;
			$estado_respuesta = $estado;
		}*/
	
	$ultimo_estado = qo($Consulta2);
	
	//print_r($ultimo_estado);
	
	$estado_respuesta = "dsdsd";
	
	if(isset($ultimo_estado->nestado))
	{
		return $ultimo_estado->nestado;
	}
	else{
		return "PENDIENTE";
	}
	//return $ultimo_estado->nestado;
}

function cabio_color($id,$estado){
	$Consulta_color = "select pqr_estado.color_co from pqr_respuesta  
       inner join pqr_estado on pqr_respuesta.pqr_estado_respuesta = pqr_estado.id where solicitud = $id";
	   
	$con_color =  qo($Consulta_color);

      if(isset($con_color)){
		  return  $con_color->color_co;
	  }else{
		  return "#FFFFC1";
	  }	
	   
}

function ver_respuestas()
{
	global $id;
	html();
	$Solicitud=qo("select *,cliente as ncliente,paseg.nombre as naseguradora , t_oficina(oficina) as noficina,
				t_pqr_tipo(tipo_solicitud) as ntipo, t_pqr_estado(estado) as nestado
				from pqr_solicitud as s 
                LEFT join pqr_estado as e on s.estado = e.id 
                LEFT join pqr_aseguradora as paseg on s.aseguradora = paseg.id where s.id=$id");
				
	$Cestado=qo1("select color_co from pqr_estado where id=$Solicitud->estado");
	$Consulta="select id,t_pqr_solicitud(solicitud) as nsolicitud, t_pqr_tipo_accion(pqr_tipo_accion) as ntipoaccion,descripcion,
	           t_pqr_estado_respuesta(pqr_estado_respuesta) as nestado, fecha, procesado_por, t_resarcimiento_pqr(id) as resarcimiento from pqr_respuesta where solicitud=$id";
	
	if ($Datos=q($Consulta)){
		while($C=mysql_fetch_object($Datos)){
			  $respuesta_estado  = $C->nestado;
		}
	}
	  if(isset($respuesta_estado)){
			$estado_respuesta = $respuesta_estado;
		}else{
			$estado_respuesta = $Solicitud->nestado;
		}
	$sql = "SELECT numero,pqr_asociado FROM siniestro WHERE pqr_asociado = $id";
	$validarPqr = qo($sql);
	if($validarPqr->pqr_asociado != 0 || $validarPqr->pqr_asociado != ""){
		$varHtmlPqr = "<tr><td>Asociado al siniestro #: $validarPqr->numero</td></tr>";
	}else{
		$varHtmlPqr = "<tr><td>Asociar a siniestro</td><td bgcolor='' class=''><input type='number' name='siniestroPqr' placeholder='Solo el id del siniestro' id='siniestroPqr' value='' size='50%'><button onclick='busqueda_popup($id)'>Asociar</button></td></tr>";
		
	}
	
	
	
	
	echo "<script language='javascript'>
					
					function cargar_respuesta()
						{
							modal('zpqr.php?Acc=cargar_respuesta&id=$id',0,0,500,700,'cr');
						}
					function ver_resarcimiento(dato)
					{
						modal('zpqr.php?Acc=ver_resarcimiento&id='+dato,0,0,500,800,'vr');
					}
					function busqueda_popup(idPqr){
						
						console.log(idPqr);
						var valueId = document.getElementById('siniestroPqr').value;
						console.log(valueId);
						modal('zpqr.php?Acc=busqueda_popup&idPqr='+idPqr+'&id='+valueId,0,0,500,800,'vr');
					}
				</script>
				<style type='text/css'>
					.d1 {text-align:left; font-family: Arial; font-size: 12px; color:#3333aa;}
				</style>
		<body>
		<script language='javascript'>centrar(800,500);</script>
		<h3>Información del PQR</h3><br>
		<table>
			<tr><td>Aseguradora</td><td class='d1'>$Solicitud->naseguradora</td><td>Oficina</td><td class='d1'>$Solicitud->noficina</td></tr>
			<tr><td>Tipo de PQR</td><td class='d1'>$Solicitud->ntipo</td><td>Fecha de Solicitud</td><td class='d1'>$Solicitud->fecha</td></tr>
			<tr><td>Placa</td><td class='d1'>$Solicitud->placa</td><td>Cliente</td><td class='d1'>$Solicitud->ncliente</td></tr>
			<tr><td>Email:</td><td class='d1'>$Solicitud->email_e</td></tr>
			<tr><td>Descripción</td><td colspan=3>$Solicitud->descripcion</td></tr>
			<tr><td>Estado</td><td bgcolor='$Cestado' class='d1'>$estado_respuesta</td></tr>
			$varHtmlPqr
		</table>";
	if ($Datos=q($Consulta))
	{
			echo "  <h3>Respuesta de Solicitud PQR <input type='button' value='Adicionar Respuesta' onclick='cargar_respuesta()'></h3> 
					<table border cellspacing='0' width='100%' bordercolor='CDECF2' style='empty-cells:show;'> <tr>
					<th>Tipo de Acción</th>
					<th>Descripción</th>
					<th>Estado</th>
					<th>Fecha</th>
					<th>Procesado por</th>
					<th>Con Resarcimiento</th>
					</tr>";
			while($C=mysql_fetch_object($Datos))
			{
				if($C->resarcimiento) $icono="<img src='gifs/standar/opcionverde.png' border='0'>"; else $icono='';
				echo "<tr onclick='ver_resarcimiento($C->id);'>
					<td>$C->ntipoaccion</td>
					<td align='justify'>$C->descripcion</td>
					<td>$C->nestado</td>
					<td>$C->fecha</td>
					<td>$C->procesado_por</td>
					<td align='center'>$icono</td>
					</tr>";
			}
			echo "</table>";
	}
	else
	{
		echo "<body>No se encuentro información. <input type='button' value='Adicionar Respuesta' onclick='cargar_respuesta()'>";
	}			
}

function busqueda_popup(){
	global $id ,$idPqr;
	$sql = "UPDATE siniestro SET pqr_asociado = $idPqr WHERE id = $id";
	qo($sql);
	
	echo "<h3>Se a añadido el PQR al siniestro con el ID $id<h3>";
	
}

function cargar_respuesta()
{
	
	global $Ahora,$id;
	html();
	$Solicitud=qo("select * from pqr_solicitud where id=$id");
	
	echo "<script language='javascript'>
	
	function cargar_respuesta_ok()
	{
		with(document.forma)
		{
		  /*if(!tipo.value) { alert('Debe seleccionar el tipo de acción.'); tipo.style.backgroundColor='ffffcc';return false;}*/
			if(!alltrim(descripcion.value)) { alert('Debe digitar la descripción del PQR.'); descripcion.style.backgroundColor='ffffcc'; return false;}
		  submit();
		}
	}
	
	</script>
	<style type='text/css'>
		.d1 {text-align:left; font-family: Arial; font-size: 12px; color:#3333aa;}
	</style>
	<body><h3>RESPUESTA </h3>
	<script src='//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
	Solicitud del cliente: <span class='d1'>$Solicitud->cliente <!-- $Solicitud->email_e --></span><br>
	Descripción:</td><td> $Solicitud->descripcion
	<hr>
	<table>
	<form action='zpqr.php' method='POST' name='forma'>
	<tr><td>Descripción:</td><td><textarea name='descripcion' rows=3 cols=80 style='font-size:12px'></textarea></td></tr>
	
	<tr><td>Fecha:</td><td><input type='text' name='fecha' value='$Ahora' size=20 radonly></td></tr>
	
	<tr><td>Estado: </td><td>".menu1("estado","select id,nombre from pqr_estado_respuesta",0,1)."</td></tr>
	
	<tr class='ocultar'><td>Causales: </td><td>".menu1("causales","select id,nombre from causales",0,1)."</td></tr>
	
	<tr class='ocultar'><td>Área implicada: </td><td>".menu1("area_implicada","select id,nombre from areaimplicada",0,1)."</td></tr>
	
	<tr class='ocultar'><td>Proceso al que afecta: </td><td>".menu1("proceso_afecta","select id,nombre from proceso_afectado",0,1)."</td></tr>
	
	<tr class='ocultar'><td>Procedencia: </td><td>".menu1("procendencia","select id,nombre from procedencia",0,1)."</td></tr>
	
	<tr class='ocultar'><td>Toma el servicio:</td><td>".menu1("toma_servicio","select id,nombre from toma_servicio",0,1)."</td></tr>
	
	<tr class='ocultar'><td>Tipo de Acción: </td><td>".menu1("tipo","select id,nombre from pqr_tipo_accion",0,1)."</td></tr>
	
	<tr class='ocultar1'><td>Numero de siniestro:</td><td><input type='text' name='num_siniestro' size=40></tr></td>
	
	<tr><td>Procesado por:</td><td><input type='text' name='registrado_por' value='".$_SESSION['Nombre']."' size=40 readonly></tr></td>
	<input type='hidden' name='idsolicitud' value='$id'>
	<input type='hidden' name='Acc' value='cargar_respuesta_ok'>
	<tr><td colspan='2'><input type='button' value='Grabar' onclick='cargar_respuesta_ok()'></td></tr>
	</table>
	</form>
	 <script language='javascript'>centrar(800,400);</script>
	 
	 <script>
	 
	 $(document).ready(function(){
	
			if($('#estado').val() == 3){
				$('.ocultar').show();
			}else{
				$('.ocultar').hide();
			}
			
			if($('#toma_servicio').val() == 1){
				$('.ocultar1').show();
			}else{
			   $('.ocultar1').hide();
			}
	$('select[name=estado]').change(function(){
            
			if($('#estado').val() == 3){
				$('.ocultar').show();
			}else{
				$('.ocultar').hide();
			}
        });
	$('select[name=toma_servicio]').change(function(){
			if($('#toma_servicio').val() == 1){
				$('.ocultar1').show();
			}else{
				$('.ocultar1').hide();
			}
			});
		
	 });
	 </script>

	</body>";
	
	
}

function cargar_respuesta_ok()
{
	global $idsolicitud,$tipo,$descripcion,$fecha,$registrado_por,$estado,$causales,$area_implicada,$proceso_afecta,$procendencia,$toma_servicio,$num_siniestro;
	$cargar=q("insert into pqr_respuesta (solicitud,pqr_tipo_accion,descripcion,fecha,procesado_por,pqr_estado_respuesta,causales_id,area_implicada_id,proceso_afectado_id,procedencia_id,toma_servicio_id,numero_siniestro) values 
	('$idsolicitud','$tipo','$descripcion','$fecha','$registrado_por','$estado','$causales','$area_implicada','$proceso_afecta','$procendencia','$toma_servicio','$num_siniestro')");
	
	echo "<body><script language='javascript'>alert('La información fue grabada satisfactoriamente');window.close();void(null);opener.location.reload();</script></body>";
}



function ver_resarcimiento()
{
	global $id;
		html();
		$Consulta="select id, t_pqr_respuesta(respuesta) as nrespuesta, fecha_emision, fecha_vencimiento, 
		t_pqr_estado_rst(estado) as nestado, t_pqr_tipo_rst(tipo_rst) as ntipo from pqr_resarcimiento where respuesta=$id";
		$Respuesta=qo("select *, t_pqr_solicitud(solicitud) as nsolicitud, t_pqr_tipo_accion(pqr_tipo_accion) as ntipo,
						t_pqr_estado_respuesta(pqr_estado_respuesta) as nestado from pqr_respuesta where id=$id");
		
		$Solicitud=qo("select *,cliente as ncliente,paseg.nombre as naseguradora , t_oficina(oficina) as noficina,
				t_pqr_tipo(tipo_solicitud) as ntipo, t_pqr_estado(estado) as nestado
				from pqr_solicitud as s 
                inner join pqr_estado as e on s.estado = e.id 
                inner join pqr_aseguradora as paseg on s.aseguradora = paseg.id where s.id=$Respuesta->solicitud");
				
	echo "<script language javascript>
				function cargar_resarcimiento()
					{
						modal('zpqr.php?Acc=cargar_resarcimiento&id=$id',0,0,300,400,'rs');
					}
				</script>
			<style type='text/css'>
				.d1 {text-align:left; font-family: Arial; font-size: 12px; color:#3333aa;}
			</style><body>
			<script language='javascript'>centrar(800,500);</script>
			<h3>Información del PQR</h3><br>
		<table>
			<tr><td>Aseguradora</td><td class='d1'>$Solicitud->naseguradora</td></tr>
			<tr><td>Oficina</td><td class='d1'>$Solicitud->noficina</td></tr>
			<tr><td>Tipo de PQR</td><td class='d1'>$Solicitud->ntipo</td></tr>
			<tr><td>Fecha de Solicitud</td><td class='d1'>$Solicitud->fecha</td></tr>
			<tr><td>Cliente</td><td class='d1'>$Solicitud->ncliente <!-- $Solicitud->email_e --></td></tr>
			<tr><td>Descripción</td><td class='d1'>$Solicitud->descripcion</td></tr>
			<tr><td>Estado</td><td class='d1'>$Solicitud->nestado</td></tr>
		</table>
			<h3>RESPUESTA AL PQR</h3>
			<table>
			<tr><td>Solicitud:</td><td class='d1'>$Respuesta->nsolicitud</td></tr>
			<tr><td>Tipo de Acción:</td><td class='d1'>$Respuesta->ntipo</td></tr>
			<tr><td>Descripción:</td><td class='d1'>$Respuesta->descripcion</td></tr>
			<tr><td>Fecha:</td><td class='d1'>$Respuesta->fecha</td></tr>
			<tr><td>Procesado por:</td><td class='d1'>$Respuesta->procesado_por</td></tr>
			<tr><td>Estado:</td><td class='d1'>$Respuesta->nestado</td></tr>
			</table>";
	if($Datos=q($Consulta))
	{
		echo "  
				<h3>Resarcimiento PQR <input type='button' value='Adicionar Resarcimiento' onclick='cargar_resarcimiento()'></h3>
				<table border cellspacing='0' width='100%' bordercolor='CDECF2' style='empty-cells:show;'> <tr>
				<th>Respuesta</th>
				<th>Fecha de Emisión</th>
				<th>Fecha de Vencimiento</th>
				<th>Estado</th>
				<th>Tipo</th>
				</tr>";
		while($C=mysql_fetch_object($Datos))
		{						
			echo "<tr>
				<td>$C->nrespuesta</td>
				<td>$C->fecha_emision</td>
				<td>$C->fecha_vencimiento</td>
				<td>$C->nestado</td>
				<td>$C->ntipo</td>
				</tr>";
		}
		echo "</table>";
	}
	else
	{
		echo "No se encontro información<br><br><input type='button' value='Adicionar Resarcimiento' onclick='cargar_resarcimiento()'>";
	}
}

function cargar_resarcimiento()
{
    global $Ahora,$id;
	html();
	$Respuesta=qo("select * from pqr_respuesta where id=$id");
	$Solicitud=qo("select * from pqr_solicitud where id=$Respuesta->solicitud");
	$Cliente=qo("select * from cliente where id=$Solicitud->id");
	$FI=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-10)));
	$FF=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),15)));
	echo "<script language='javascript'>
		function cargar_resarcimiento_ok()
		{
			with(document.forma)
			{
			  if(!tipo.value) { alert('Debe seleccionar el tipo de resarcimiento.'); tipo.style.backgroundColor='ffffcc';return false;}
				 submit();
			}
		}
		</script>
		<body><h3>RESARCIMIENTO</h3>
		<form action='zpqr.php' method='POST' name='forma'>
		<table>
		Cliente: $Cliente->apellido $Cliente->nombre<br>
		Respuesta: $Respuesta->descripcion
		<hr>
		<tr><td>Fecha Emisión: </td> <td><input type='text' name='fecha' value='$Ahora' size=20 radonly></td></tr>
		<tr><td>Fecha de Vencimiento: </td> <td>".pinta_FC('forma','FI',$FI)."</td></tr>
		<tr><td>Tipo de Resarcimiento:</td> <td> ".menu1("tipo","select id, nombre from pqr_tipo_rst",0,1)."</td></tr>
		<input type='hidden' name='id_resarcimiento' value='$id'>
		<input type='hidden' name='estado' value='1'>
		<input type='hidden' name='Acc' value='cargar_resarcimiento_ok'>
		<tr><td colspan='2'><input type='button' value='Guardar' onclick='cargar_resarcimiento_ok()'></tr></td>
		</table></form>
		<script language='javascript'>centrar(400,300);</script>
		</body>";
}

function cargar_resarcimiento_ok()
	{
		global $id_resarcimiento, $fecha, $FI, $tipo, $estado;
		$cargsar=q("insert into pqr_resarcimiento(respuesta, fecha_emision, fecha_vencimiento, tipo_rst, estado) values ('$id_resarcimiento', '$fecha', '$FI', '$tipo', '$estado')");
		echo "<body><script language='javascript'> alert('La información fue grabada satisfactoriamente');window.close();void(null);opener.location.reload();</script></body>";
		
	}








?>