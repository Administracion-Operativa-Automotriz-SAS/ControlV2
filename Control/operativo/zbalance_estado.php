<?php
/***
BALANCE DE ESTADO, muestra la cartera, requisiciones, facturas caja menor y datos de control para cada estado del vehiculo

***/

/********************
 * Autor Original: Sergio Urbina
 * 
 * Proyecto: Administrativo Adici�n ITEM DE REQUISICION
* Documentos relacionados: 
 * Descripci�n del script:
 * Este el 
 * Cambios:
 *Autor: Sergio Urbina
 * 1. Se agrego la funcionalida de multiplicacion en Control zbalance_estado
 *
 * 
 * Fecha:27/02/2019
 *********************/

include('inc/funciones_.php');
sesion();
$USUARIO=$_SESSION['User'];
$NUSUARIO=$_SESSION['Nombre'];
$NICK=$_SESSION['Nick'];
$BDA='aoacol_administra';
$Calificados=false;$Empleados=false;
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');	die();}

function ver_balance() // vista general del balance de estado
{
	global $id,$BDA;
	html('BALANCE DE ESTADO');
	java($id);
	
		echo '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		';

		
	include("views/subviews/agregar_facturas_requisicion.html");
	
	$U=qo("select *,t_estado_vehiculo(estado) as nestado from ubicacion where id=$id"); // trae los datos del estado de  vehiculo
	$V=qo("select *,kilometraje(id) as km from vehiculo where id=$U->vehiculo"); // trae elulitimo kilometraje del vehicul o
	$L=qo("select * from linea_vehiculo where id=$V->linea"); // linea de marca del vehiculo
	if(!$Siniestro=qo("Select * from siniestro where ubicacion=$U->id")) $Siniestro=qo("Select * from siniestro_hst where ubicacion=$U->id"); // info del siniestro
	$Of=qo("Select * from oficina where id=$U->oficina"); // oficina
	
	
	echo "<body>
		<script language='javascript'>centrar(s_ancho(),s_alto()/1.2);window.moveTo(0,0);</script>
	<h3>Estado: $id $U->nestado [$U->fecha_inicial - $U->fecha_final] [".coma_format($U->odometro_inicial)." - ".coma_format($U->odometro_final)."]</h3>
	<table align='center' bgcolor='ffffee'><tr><th>Placa</th><th>Linea</th><th>Emblema</th><th>Kilometraje actual</th><th>Oficina</th></tr>
	<tr><td align='center' class='placa' style='background-image:url(img/placa.jpg);' width='90px'><b>$V->placa</b></td>
	<td><b style='font-size:18px'>$L->nombre</b></td>
	<td align='center'><img src='$L->emblema_f' border='0' height='30'></td>
	<td align='center'><b style='font-size:18px'>".coma_format($V->km)."</b></td>
	<td align='center'><b style='font-size:18px'>$Of->nombre</b></td>
	<td><a onclick='recargar();' class='info'><img src='gifs/standar/Refresh.png' border='0'><span>Refrescar</span></a></td>
	</tr></table><hr>
	<b>Observaciones:</b> $U->observaciones<br>
	<b>Observaciones de mantenimiento:</b> $U->obs_mantenimiento
	<table border cellspacing='0' cellpadding='5' width='90%' align='center'>
		<tr><th>CARTERA</th></tr>
		<tr><td>";ver_cartera($Siniestro,$id);echo "</td></tr>
		<tr><th>REQUISICIONES</th></tr>
		<tr><td>";ver_requisiciones($id);echo "</td></tr>
		<tr><th>PROVEEDORES</th></tr>
		<tr><td>";ver_proveedores($id);echo "</td></tr>
		<tr><th>CAJA MENOR</th></tr>
		<tr><td>";ver_caja_menor($id);echo "</td></tr>
		<tr><th>CONTROL DE OPERACIONES</th></tr>
		<tr><td>";ver_control_operacion($id);echo "</td></tr>
		<tr><td>";
	echo "</td></tr>
	</table>
	</body>";
}

function ver_balance_test() // vista general del balance de estado
{
	global $id,$BDA;
	html('BALANCE DE ESTADO');
	java($id);
	
		echo '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
		
	include("views/subviews/agregar_facturas_requisicion.html");
	
	$U=qo("select *,t_estado_vehiculo(estado) as nestado from ubicacion where id=$id"); // trae los datos del estado de  vehiculo
	$V=qo("select *,kilometraje(id) as km from vehiculo where id=$U->vehiculo"); // trae elulitimo kilometraje del vehicul o
	$L=qo("select * from linea_vehiculo where id=$V->linea"); // linea de marca del vehiculo
	if(!$Siniestro=qo("Select * from siniestro where ubicacion=$U->id")) $Siniestro=qo("Select * from siniestro_hst where ubicacion=$U->id"); // info del siniestro
	
	$Of=qo("Select * from oficina where id=$U->oficina"); // oficina
	
	echo "<body>
		<script language='javascript'>centrar(s_ancho(),s_alto()/1.2);window.moveTo(0,0);</script>
	<h3>Estado: $id $U->nestado [$U->fecha_inicial - $U->fecha_final] [".coma_format($U->odometro_inicial)." - ".coma_format($U->odometro_final)."]</h3>
	<table align='center' bgcolor='ffffee'><tr><th>Placa</th><th>Linea</th><th>Emblema</th><th>Kilometraje actual</th><th>Oficina</th></tr>
	<tr><td align='center' class='placa' style='background-image:url(img/placa.jpg);' width='90px'><b>$V->placa</b></td>
	<td><b style='font-size:18px'>$L->nombre</b></td>
	<td align='center'><img src='$L->emblema_f' border='0' height='30'></td>
	<td align='center'><b style='font-size:18px'>".coma_format($V->km)."</b></td>
	<td align='center'><b style='font-size:18px'>$Of->nombre</b></td>
	<td><a onclick='recargar();' class='info'><img src='gifs/standar/Refresh.png' border='0'><span>Refrescar</span></a></td>
	</tr></table><hr>
	<b>Observaciones:</b> $U->observaciones<br>
	<b>Observaciones de mantenimiento:</b> $U->obs_mantenimiento
	<table border cellspacing='0' cellpadding='5' width='90%' align='center'>
		<tr><th>CARTERA</th></tr>
		<tr><td>";ver_cartera($Siniestro,$id);echo "</td></tr>
		<tr><th>REQUISICIONES</th></tr>
		<tr><td>";ver_requisiciones($id);echo "</td></tr>
		<tr><th>PROVEEDORES</th></tr>
		<tr><td>";ver_proveedores($id);echo "</td></tr>
		<tr><th>CAJA MENOR</th></tr>
		<tr><td>";ver_caja_menor($id);echo "</td></tr>
		<tr><th>CONTROL DE OPERACIONES</th></tr>
		<tr><td>";ver_control_operacion($id);echo "</td></tr>
		<tr><td>";
	echo "</td></tr>
	</table>
	</body>";
}

function java($id) // pinta herramientas javascript para la pantalla general
{
	echo "<script language='javascript'>
			function adicionar_requisicion() { window.open('zbalance_estado.php?Acc=adicionar_requisicion&id=$id','_self'); }
			function ver_imagen_requisicion(id) { modal('zbalance_estado.php?Acc=ver_imagen_requisicion&id='+id,0,0,600,600,'imgrq'); }
			function adicionar_detaller(id) { modal('zbalance_estado.php?Acc=adicionar_detaller&idr='+id+'&id=$id',0,0,300,300,'add_dreq'); }
			function recargar() { window.open('zbalance_estado.php?Acc=ver_balance&id=$id','_self'); }
			function cerrar_requisicion(id) { if(confirm('Desea cerrar la requisicion?')) { window.open('zbalance_estado.php?Acc=cerrar_requisicion&id='+id,'Oculto_req'); } }
			function re_enviar_solicitud_aprobacion(id) { window.open('zbalance_estado.php?Acc=enviar_mail_solicitud_aprobacion&id='+id+'&Aviso=1','Oculto_req'); }
			function asociar_facprov() { modal('zbalance_estado.php?Acc=asociar_facprov&id=$id',50,100,600,800,'asociar_facprov'); }
			function asociar_cajamenor() { modal('zbalance_estado.php?Acc=asociar_cajamenor&id=$id',50,100,600,800,'asociar_cajamenor'); }
			function add_control() { modal('zbalance_estado.php?Acc=adicionar_control_operaciones&id=$id',50,100,500,800,'adicionar_ctrol'); }
			function desasociar_cajamenor(id) { if(confirm('Desea des-asociar esta caja menor?')) { window.open('zbalance_estado.php?Acc=desasociar_cajamenor&id='+id,'Oculto_cajamenor'); } }
			function desasociar_facprov() { if(confirm('Desea des-asociar esta factura de proveedor?')) { window.open('zbalance_estado.php?Acc=desasociar_facprov&id='+id,'Oculto_proveedores'); } }
			function borrar_item_requisicion(id) {if(confirm('Desea borrar este item?')) {window.open('zbalance_estado.php?Acc=borrar_item_requisicion&id='+id,'Oculto_req');} }
			function actualizar_factura_referencia(factura,id) {if(confirm('Desea dejar '+factura+' como factura referencia?')) {window.open('zbalance_estado.php?Acc=actualizar_factura_referencia&factura='+factura+'&id='+id,'Oculto_req');return true;}}
			function realizar_calificacion() {window.open('../../Administrativo/zproveedor.php?Acc=realizar_calificacion&refrescar_opener=1','_self');}
			function cambia_tipo1(id_detalle)
                {var T1=document.getElementById('tipo1_'+id_detalle);
                        if(T1.value)    {
                                if(confirm('Desea ajustar el bien/servicio de este item?'))
                                window.open('zbalance_estado.php?Acc=ajusta_bs&id='+id_detalle+'&dato='+T1.value,'Oculto_detallerq');
                                else T1.value='';}
                        return true;}
			function crear_nuevo_proveedor() {modal('../../Administrativo/zproveedor.php?Acc=adicion_de_proveedor','crear_nuevo_proveedor');}
			function realizar_calificacion() {window.open('../../Administrativo/zproveedor.php?Acc=realizar_calificacion&refrescar_opener=1','_self');}
			function valida_cambio_proveedor(idp,idr) {if(confirm('Desea asignar ese proveedor a la requisici�n?')) window.open('zbalance_estado.php?Acc=asigna_proveedor&idp='+idp+'&idr='+idr,'Oculto_detallerq');}
			function evaluar_requisicion(id){modal('zbalance_estado.php?Acc=evaluar_requisicion&id='+id,0,0,500,500,'evrq');}
			function asigna_sede(sede,req) {window.open('zbalance_estado.php?Acc=asignar_sede&sede='+sede+'&requisicion='+req,'Oculto_detallerq');}
			function adicionar_observaciones(idreq){modal('zbalance_estado.php?Acc=adicionar_observaciones&id='+idreq,0,0,100,100,'adobs');}
		</script>";
}

function asignar_sede()
{
	global $sede,$requisicion,$BDA;
	q("update $BDA.requisicion set sede='$sede' where id='$requisicion' ");
	echo "<body><script language='javascript'>alert('Sede Asignada');</script></body>";
}

function asigna_proveedor() // asignacion de un proveedor a una requisicion
{
	global $idr,$idp,$BDA;
	q("update $BDA.requisicion set proveedor='$idp' where id='$idr' ");
	 echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function ajusta_bs() // ajusta bien, o servicio en el detalle de una requisicion
{
        global $id,$dato,$BDA; // id es el id del detalle de requisicion y dato es el id del bien o servicio que se est� asignando.
        q("update $BDA.requisiciond set tipo1='$dato' where id='$id' ");
        echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function ver_cartera($Siniestro,$id=0) // pinta las facturas y pagos asociados al estado
{
	
	
	global $BDA;
	
	$Requisiciones=q("select * from $BDA.requisicion where ubicacion=$id"); // busca las requisiciones.
	
		
		$arrayCadena = array();
		while($Req=mysql_fetch_object($Requisiciones))
		{			
			
			if($Req->factura_referencia) // busca facturas de referencia en las requisiciones
			{
				$sql = "select siniestro from factura where consecutivo='$Req->factura_referencia'";
				//echo $sql."<br>";
				$idSiniestro=qo1($sql); // halla el numero de siniestro asociado a la factura
				$sql = "select * from siniestro where id='$idSiniestro' ";
			    //echo $sql."<br>";
				if(!$Siniestro=qo("select * from siniestro where id='$idSiniestro' ")) 
			    $Siniestro=qo("select * from siniestro_hst where id='$idSiniestro'");
			    $row = "'$Req->factura_referencia'";
				if($row!=null)
				{$var = array_push($arrayCadena,$row);}	
				
			//break;
			}
			    
			    
			    $sqlOtherTable = qo("select * from  aoacol_administra.facturas_venta_requisicion where requisicion = $Req->id");
				
				$row1 = $sqlOtherTable->factura_referencia;				
				
				if($row1)
				{
				
					$row = "'$Req->factura_referencia'".",'$row1'";
					$var = array_push($arrayCadena,$row);
				}
				
			//break;			
		}
		
		$varId =  implode(",",$arrayCadena);
		
		        
				
		
		//var_dump($varId);
		
		
		
	
		
		
		/*$Aseguradora=qo("select * from aseguradora where id=$Siniestro->aseguradora"); 
		echo "<table><tr><td>Aseguradora</td><td>$Aseguradora->nombre</td></tr>
				<tr><td>Siniestro No.</td><td>$Siniestro->numero [$Siniestro->id]</td></tr>
				<tr><td>Asegurado</td><td>$Siniestro->asegurado_nombre</td></tr></table>";
		// busca todas las facturas asociadas al siniestro*/
		
		if(empty($varId)){
			/*$sql = "select c.id,c.consecutivo,c.fecha_emision,c.total,t_concepto_fac(d.concepto) as nconcepto,
								d.cantidad,d.unitario,d.iva,d.total,d.descripcion 
								from factura c, facturad d where d.factura=c.id and c.siniestro=$Siniestro->id and c.anulada=0";*/
			return "";					
			
		}else{
			
		$sql = "select c.id,c.consecutivo,c.fecha_emision,c.total,t_concepto_fac(d.concepto) as nconcepto,
								d.cantidad,d.unitario,d.iva,d.total,d.descripcion 
								from factura c, facturad d where d.factura=c.id and c.consecutivo  in($varId)  and c.anulada=0";	
		}
		
		if($Facturas=q($sql))
		{
			
			echo "<table border cellspacing='0' width='100%'><tr>
				<td align='center' bgcolor='777777'><b>Factura No.</b></td>
				<td align='center' bgcolor='777777'><b>Fecha</b></td>
				<td align='center' bgcolor='777777'><b>Concepto</b></td>
				<td align='center' bgcolor='777777'><b>Valor</b></td>
				<td align='center' bgcolor='777777'><b>Pago</b></td>
				</tr>";
			$Total_facturado=0;
			$Total_pagado=0;
			while($F =mysql_fetch_object($Facturas))
			{
				echo "<tr>
				<td align='center'><b>$F->consecutivo</b> 
				<a onclick=\"modal('zfacturacion.php?Acc=imprimir_factura&id=$F->id',0,0,700,700,'vfac');\"><img src='gifs/standar/Search.png' border='0'></a>
				</td>
				<td align='center'>$F->fecha_emision</td>
				<td>$F->nconcepto .:. $F->descripcion</td>
				<td align='right'>".coma_format($F->total)."</td><td>";
				$Total_facturado+=$F->total;
				//  BUSQUEDA DE LOS RECIBOS DE CAJA DE ESTA FACTURA
				if($Recibos=q("select * from recibo_caja where factura=$F->id"))
				{
					echo "<table border cellspacing='0' width='100%'><tr>
									<td><b>Recibo Caja</b></td>
									<td><b>Fecha</b></td>
									<td><b>Valor</b></td>
									</tr>";
					while($Rc =mysql_fetch_object($Recibos ))
					{
						echo "<tr>
						<td align='center'><b>$Rc->consecutivo</b></td>
						<td align='center'>$Rc->fecha</td>
						<td align='right'>".coma_format($Rc->valor)."</td>
						</tr>";
						$Total_pagado+=$Rc->valor;
					}
					echo "</table>";
				}
				//  BUSQUEDA DE NOTAS CREDITO DE ESTA FACTURA
				if($NotasCr=q("select * from nota_credito where factura=$F->id"))
				{
					echo "<table border cellspacing='0' width='100%'><tr>
									<td><b>Nota Cr�dito</b></td>
									<td><b>Fecha</b></td>
									<td><b>Valor</b></td>
									</tr>";
					while($Ncr =mysql_fetch_object($NotasCr ))
					{
						echo "<tr>
						<td align='center'><b>$Ncr->consecutivo</b></td>
						<td align='center'>$Ncr->fecha</td>
						<td align='right'>".coma_format($Ncr->total)."</td>
						</tr>";
						$Total_pagado+=$Ncr->valor;
					}
					echo "</table>";
				}
				//  BUSQUEDA DE NOTAS CONTABLES DE ESTA FACTURA
				if($NotasCo=q("select * from nota_contable where factura=$F->id"))
				{
					echo "<table border cellspacing='0' width='100%'><tr>
									<td><b>Nota Contable</b></td>
									<td><b>Fecha</b></td>
									<td><b>Valor</b></td>
									</tr>";
					while($Nco =mysql_fetch_object($NotasCo ))
					{
						echo "<tr>
						<td align='center'><b>$Nco->consecutivo</b></td>
						<td align='center'>$Nco->fecha</td>
						<td align='right'>".coma_format($Nco->valor)."</td>
						</tr>";
						$Total_pagado+=$Nco->valor;
					}
					echo "</table>";
				}
				
				echo "</td></tr>";
			}
			// halla el saldo de la cartera total
			$Total_saldo=$Total_facturado-$Total_pagado;
			echo "<tr><td colspan='3' bgcolor='ffddaa'><b style='color:880000'>TOTAL FACTURADO</b></td>
					<td align='right'  bgcolor='ffddaa'><b style='color:880000'>".coma_format($Total_facturado)."</b></td>
					<td  bgcolor='ffddaa'><b style='color:000088'>Total Pagado: $ ".coma_format($Total_pagado)." Saldo: $ ".coma_format($Total_saldo)."</b></td>
			</tr></table>";
		}
	
//return "";
}

function actualizar_factura_referencia() // actualiza la factura de referencia en una requisici�n
{
	global $id,$factura,$BDA;
	q("update $BDA.requisicion set factura_referencia='$factura' where id='$id' ");
	echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function ver_requisiciones($id) // muestra todas las requisiciones de un balance de estado
{
	global $BDA,$USUARIO,$Calificados,$Empleados;	
	
	if($Requisiciones=q("select * from $BDA.requisicion where ubicacion=$id")) // busca las requisiciones
	{
		if(inlist($USUARIO,'1,7,10,13,23,27')) $Modifica=true; else $Modifica=false;
		echo "<table border cellspacing='0' cellpadding='10' width='100%' bgcolor='aaaaaa'>";
		
		while($Rq=mysql_fetch_object($Requisiciones))
		{
			
			include('inc/link.php');
			// busca el proveedor y su calificacion
			//echo "id requisicion:";
			//echo $Rq->id;
			$query = " select id,nombre,calificacion_actual,$BDA.t_ciudad(ciudad) as nciudad,tipo,
				calificacion_actual from $BDA.proveedor where id=$Rq->proveedor limit 1";
				
			$resultado = mysql_query($query);
			//print_r($resultado);
			//$Proveedor=qom($query,$LINK);
			$proov= mysql_fetch_object($resultado);
			//print_r($proov);
			$bgc='cccccc';if($Proveedor->calificacion_actual=='M') $bgc='ffffaa';if($Proveedor->calificacion_actual=='C') $bgc='aaffaa';
			$Total_rq=qo1m("select sum(valor_total) from $BDA.requisiciond where requisicion=$Rq->id",$LINK);
			
			echo "<tr>
			<td><table border cellspacing='0' width='100%'>
					<tr><td bgcolor='bbbbff'><b>Numero</b></td>
						<td bgcolor='bbbbff'><b>Fecha</b></td>
						<td bgcolor='bbbbff'><b>Solicitado Por</b></td>
						<td bgcolor='bbbbff'><b>Proveedor</b></td>
						<td bgcolor='bbbbff'><b>Cotizaciones</b></td>
						<td bgcolor='bbbbff'><b>Valor total</b></td>
						<td bgcolor='bbbbff'><b>Estado</b></td>
						<td bgcolor='bbbbff'><b>Facturas asociadas</b></td>
						<td bgcolor='bbbbff'><b>Agregar factura</b></td>
						<td bgcolor='bbbbff'><b>Calidad</b></td>
				</tr>
					<tr><td bgcolor='eeeeee'>$Rq->id</td>
						<td bgcolor='eeeeee'>$Rq->fecha</td>
						<td bgcolor='eeeeee'>$Rq->solicitado_por</td>
						<td bgcolor='$bgc'>";
				/*Hola qui vamos*/
				
				if($proov){			
					$Proveedor = $proov; 
						
						echo $Proveedor->nombre;
						$sql = "select *,t_ciudad(ciudad) as nciudad from $BDA.prov_sede where proveedor=$Proveedor->id";
					  
				   if($Sedes=q($sql)){
						echo "Hola";
						
						echo "<br>Sede: <select name='sede' style='width:200px;' 
						onchange='asigna_sede(this.value,$Rq->id);'><option value=''>Seleccionar una sede</option>";
						while($Sede=mysql_fetch_object($Sedes))
						{
							echo "<option value='$Sede->id' ".($Rq->sede==$Sede->id?"selected":"").">$Sede->nombre $Sede->nciudad 
							($Sede->direccion) - $Sede->email </option>";
						}
						echo "</select>";
						
					}
					
					
				}else{
					// permite seleccionar un proveedor para ser asignado a la requisicion
					echo " La lista que aparece a continuaci�n corresponde a los proveedores totalmente confiables y medianamente confiables, de acuerdo a la selecci�n realizada siguiendo los lineamientos del proceso de Calidad. <br>
                        Tambi�n aparecen los empleados de AOA a los que se les hace anticipos.<br>
                        ";
					if($Proveedores=q("select id,nombre,calificacion_actual,$BDA.t_ciudad(ciudad) as nciudad from $BDA.proveedor where calificacion_actual in ('M','C') or tipo='E' order by nombre"))
					{ 
					// muestra los proveedores medianamente y totalmente confiables.
						echo "<select name='proveedor' id='proveedor' style='width:300px' onchange='valida_cambio_proveedor(this.value,$Rq->id);'><option value=''></option>";
						while($P=mysql_fetch_object($Proveedores))
						{
								$bgc='cccccc';if($P->calificacion_actual=='M') $bgc='ffffaa';if($P->calificacion_actual=='C') $bgc='aaffaa';
								echo "<option value='$P->id' style='background-color:#$bgc'>$P->nombre [$P->nciudad]</option>";
						}
						echo "</select><br><br>
						Si el proveedor que busca no aparece en la lista, existen dos posibilidades: <br>
						<li> Que no est� creado en la base de datos. Por lo tanto puede crearlo dando click aqu�: <a onclick='crear_nuevo_proveedor();' class='info'><img src='img/adicionar_proveedor.png' height='30' border='0'><span style='width:100px'>Crear Proveedor</span></a>
						<li> Que si est� creado pero no haya sido seleccionado. Puede realizar la <b><i>Selecci�n</i></b> para que aparezca en la lista de proveedores dando click aqu�: <a onclick='realizar_calificacion()' class='info'><img src='img/diploma.png' height='30' border='0'><span style='width:100px'>Realizar Calificaci�n</span></a>
						";
					}
				}
				
			echo"</td>
			
			<td align='center' bgcolor='eeeeee'><a onclick='ver_imagen_requisicion($Rq->id)' style='cursor:pointer'>";
			if($Rq->cotizacion_f && $Rq->cotizacion2_f && $Rq->cotizacion3_f) echo "<img src='gifs/standar/Search.png' border='0'>"; 
			else echo "<img src='gifs/standar/Warning.png' border='0'> ".(($Rq->cotizacion_f?1:0)+($Rq->cotizacion2_f?1:0)+($Rq->cotizacion3_f?1:0));
			echo "</a></td><td bgcolor='eeeeee' align='right'>$ ".coma_format($Total_rq)."</td>
					<td ";
			if($Rq->estado==1) echo "bgcolor='ffffaa'><b>Solicitado</b> <a style='cursor:pointer' onclick='re_enviar_solicitud_aprobacion($Rq->id);'><img src='gifs/standar/derecha.png' border='0'></a>";
			elseif($Rq->estado==2) echo "bgcolor='aaffaa'><b>Aprobada</b>";
			elseif($Rq->estado==3) echo "bgcolor='ffaaaa'><b>Rechazada</b>";
			elseif($Rq->estado==4) echo "bgcolor='aaaaff'><b>Evaluada</b>";
			else echo "bgcolor='dddddd'><b>Requisici�n sin cerrar</b>";
			echo "</td>";
			
			
			echo "<td>";
				echo "<button onclick='ver_Facturas($Rq->id)' style='width:100%'>Ver</button>";
			/*echo "<td bgcolor='ffffbb' align='center'>
					<input type='text' class='numero' name='factura_referencia' id='factura_referencia' value='$Rq->factura_referencia' size='5' 
					maxlength='10' ".($Modifica?"onchange='actualizar_factura_referencia(this.value,$Rq->id);' onfocus='this.select();' " :" readonly " ).">";
			if($Sinfac=qo1("select siniestro from factura where consecutivo='$Rq->factura_referencia' "))
			{
				echo " <a onclick=\"modal('zsiniestro.php?Acc=buscar_siniestro&id=$Sinfac',0,0,600,800,'vsin');\"><img src='gifs/standar/Search.png' title='Ver Siniestro'></a>";
			}*/
			
			echo "</td>";
			echo "<td>";
				echo "<button onclick='agregar_Facturas($Rq->id)' style='width:100%'>Agregar</button>";
			echo "</td>";
			
			if($Proveedor)
			{
				if($Proveedor->tipo=='P')
				{
					if($Rq->estado==2)
					{ // si ya esta aprobada la requisicion, permite evaluar el bien o servicio del proveedor
						echo "<td bgcolor='eeeeee' align='center'><a id='evaluarbs' onclick='evaluar_requisicion($Rq->id);' style='cursor:pointer;'><b style='color:red' >
									<img src='img/diploma.png' height='20px'> Evaluar Bien o Servicio</b></a></td>";
					}
					elseif($Rq->estado==4)
					{ // si ya esta evaluada muestra informaci�n de la evaluaci�n
						echo "<td bgcolor='eeeeee' align='center'><b>Evaluaci�n</b><br>Fecha: $Rq->fecha_evaluacion<br>Por: $Rq->evaluada_por<br>Calificacion: $Rq->calificacion</td>";
						$Calificados=true;
					}
				}
				else
				{ // si no es proveedor sino empleado de aoa
					echo "<b>Empleado de AOA</b>";
					$Empleados=true;
				}
			}else{echo "<b style='color:red'>Debe seleccionar un proveedor</b>";}
			
			
			
			
			echo "</tr>
					<tr>
						<td>OBSERVACIONES:</td>
						<td colspan=3>
							".nl2br($Rq->observaciones)."<br>
							<a class='info' onclick='adicionar_observaciones($Rq->id);'><img src='gifs/standar/dsn_config.png'><span>Adicionar observaciones</span></a>
						</td>
					</tr>
				</table>";
			// muestra el detalle de la requisicion
			$sql = "select provee_produc_serv.nombre as ntipo,aoacol_administra.t_provee_produc_serv(tipo1) 
									as ntipo1,requisicionc.nombre as nclase,tipo.nombre as tipoBS, unidad_de_medida.nombre 
									as unidad_medida,requisiciond.observaciones,requisiciond.cantidad, requisiciond.requisicion,requisiciond.valor_total,requisiciond.valor 
									as valor_unitario,requisicion.fecha,requisiciond.id 
                                    from aoacol_administra.requisiciond 
                                    LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
									LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id 
									LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id 
									LEFT OUTER JOIN aoacol_administra.requisicionc on requisiciond.clase = requisicionc.id 
									LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
									LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id where requisicion=$Rq->id";
			$DetalleR=q($sql);
			
			//echo "existo aqui";
			
			//print_r($DetalleR);
			
			echo "<br> 
			<table border cellspacing='0' width='100%'><tr>
				<td align='center' bgcolor='bbbbff'><b>Item</b></td>
				<td align='center' bgcolor='bbbbff'><b>Descripcion</b></td>
				<td align='center' bgcolor='bbbbff'><b>Unidad de medida</b></td>
				<td align='center' bgcolor='bbbbff'><b>Tipo</b></td>
				<td align='center' bgcolor='bbbbff'><b>Clase</b></td>
				<td align='center' bgcolor='bbbbff'><b>Cantidad</b></td>
				<td align='center' bgcolor='bbbbff'><b>Valor Unitario</b></td>
				<td align='center' bgcolor='bbbbff'><b>Valor</b></td>
				<td align='center' bgcolor='bbbbff'><b>Utilidades</b></td>
				</tr>";
			while($DR =mysql_fetch_object($DetalleR))
			{
				echo "<tr><td bgcolor='eeeeee'>";
				if($DR->ntipo1) echo "$DR->ntipo1";
				elseif($Proveedor)
				{
					
					if($Proveedor->tipo=='P')
					{
						
						if(inlist($Proveedor->calificacion_actual,'M,C'))
						{ // solicita al usuario que identifique si el detalle corresponde a un bien o servicio
							if($Bienes=q("select bs.id,bs.nombre from $BDA.provee_produc_serv bs,$BDA.provee_ofrece po
														where bs.tipo='B' and po.proveedor='$Rq->proveedor' and po.producto_servicio=bs.id
														order by bs.nombre"))
							{ // pinta las opciones de bienes 
									$Opciones.="<optgroup label='BIENES'>";
									while($B=mysql_fetch_object($Bienes))   $Opciones.="<option value='$B->id'>$B->nombre</option>";
									$Opciones.="</optgroup>";
							}
							if($Servicios=q("select bs.id,bs.nombre from $BDA.provee_produc_serv bs,$BDA.provee_ofrece po
									where bs.tipo='S' and po.proveedor='$Rq->proveedor' and po.producto_servicio=bs.id
									order by bs.nombre"))
							{// pinta las opciones de servicios
									$Opciones.="<optgroup label='SERVICIOS'>";
									while($S=mysql_fetch_object($Servicios))        $Opciones.="<option value='$S->id'>$S->nombre</option>";
									$Opciones.="</optgroup>";
							}
							if($Opciones) // pinta el menu
									echo "<select class='tipo1' name='tipo1_$DR->id' id='tipo1_$DR->id' style='width:300px' onchange='cambia_tipo1($DR->id);'><option value=''></option>$Opciones</select>";
									
									
							else
									echo "<b style='color:red'>No hay bienes o servicios configurados en este proveedor</b>";
						}
						else
						{// verificacion de confiabilidad del proveedor
							if($Proveedor->calificacion_actual=='N') echo "<b style='color:red'>ESTE PROVEEDOR NO ES CONFIABLE</b><br>";
							echo "Realizar la <b><i>Selecci�n</i></b> aqu�: <a onclick='realizar_calificacion()' class='info'><img src='img/diploma.png' height='30' border='0'><span style='width:100px'>Realizar Selecci�n</span></a>";
						}
					}
					else
					{
						echo "<b>Empleado de AOA</b>";
					}
				}
				else
				{
					
					echo "<a onclick='asignar_proveedor()'>Asignar Proveedor</a>";
				}
				echo "</td><td bgcolor='eeeeee'>$DR->ntipo .:. $DR->observaciones</td>
				<td bgcolor='eeeeee' width='100'>$DR->unidad_medida</td>
				<td bgcolor='eeeeee' width='100'>$DR->tipoBS</td>
				<td bgcolor='eeeeee' width='100'>$DR->nclase</td>
				<td bgcolor='eeeeee' width='100'>$DR->cantidad</td>
				<td bgcolor='eeeeee' align='right' width='100'>$".coma_format($DR->valor_unitario)."</td>
				<td bgcolor='eeeeee' align='right' width='100'>$".coma_format($DR->valor_total)."</td>
				<td bgcolor='eeeeee' align='center'>";
				if(!$Rq->cerrada) echo "<a class='rinfo' onclick='borrar_item_requisicion($DR->id);'><img src='gifs/standar/Cancel.png' border='0'><span>Borrar</span></a>";
				echo "</td>
				</tr>";
				
			}
			echo "</table>"; 
			if(!$Rq->cerrada and !$Rq->estado==2 and !$Rq->estado==1)
				  
			echo "<a class='info' href='javascript:adicionar_detaller($Rq->id);'><img src='gifs/standar/nuevo_registro.png' border='0' height='12'>Adicionar item<span style='width:200px'>Adicionar un item al detalle de la requisicion</span></a>
				&nbsp;&nbsp;&nbsp;&nbsp;<a class='info' href='javascript:cerrar_requisicion($Rq->id);'><img src='gifs/standar/stop_16.png' border='0' height='12'> Cerrar Requisici�n<span style='width:200px'>Cerrar Requisici�n</span></a>";
			echo "</td>
				</tr>";
		}
		mysql_close($LINK);
		echo "</table><iframe name='Oculto_req' id='Oculto_req' style='visibility:hidden' width='1' height='1'></iframe>";
	}
	echo "<a class='info' href='javascript:adicionar_requisicion();'><img src='gifs/standar/nuevo_registro.png' border='0'>  Adicionar Requisicion<span>Adicionar Requisici�n</span></a>";
	echo "<iframe name='Oculto_detallerq' id='Oculto_detallerq' style='visibility:hidden' width='1' height='1'></iframe>";
	
}

function validar_factura_consecutivo($consecutivo)
{
	$sql = "SELECT * from aoacol_aoacars.factura where consecutivo = '$consecutivo'";
	$factura = qo($sql);
	if($factura == null)
	{
		return false;
	}
	else
	{
		return true;
	}	
}

function tabla_facturas_requisicion()
{
	global $id;
	$sql = "select r.id, r.id as requisicion , factura_referencia, siniestro, 'inrow' as tipo 
	from aoacol_administra.requisicion as r 
	inner join aoacol_aoacars.factura as f on f.consecutivo = r.factura_referencia  
	where r.id = '$id' union select r.id, requisicion, factura_referencia, siniestro, 'extra' as tipo 
	from aoacol_administra.facturas_venta_requisicion as r inner join aoacol_aoacars.factura as f 
	on f.consecutivo = r.factura_referencia  where requisicion = '$id'";
	//echo $sql."<br>";
	$result = q($sql);
	$facturas = array();
	while($fila = mysql_fetch_object($result))
	{
		//print_r($fila);
		array_push($facturas,$fila);
	}	
	
	header('Content-Type: text/html; charset=utf-8');
	
	include("views/subviews/facturas_requisicion_table.html");
}

function agregar_factura_requisicion(){
	global $id,$factura_referencia;
	
	$validation = validar_factura_consecutivo($factura_referencia);
	
	if($validation)
	{
		//$validation = validar_factura_otras_requisiciones($factura_referencia);
		
		if($validation)
		{
			$sql = "INSERT INTO aoacol_administra.facturas_venta_requisicion (factura_referencia,requisicion) values ('$factura_referencia','$id') ";
				//echo $sql; 
			q($sql);
			
			echo json_encode(array("status"=>1,"message"=>"Factura guardada","sql"=>$sql));
		}
		else
		{
			echo json_encode(array("status"=>3,"message"=>"La factura ya fue asignada a otra requisicion"));
		}
	}
	else
	{
		echo json_encode(array("status"=>2,"message"=>"No existe una factura relacionada a ese consecutivo")); 
	}	
	
}

function validar_factura_otras_requisiciones($consecutivo)
{
	$sql = "Select factura_referencia from aoacol_administra.requisicion where factura_referencia = '$consecutivo' union 
	select factura_referencia from aoacol_administra.facturas_venta_requisicion where factura_referencia = '$consecutivo' ";
	$requisicion = qo1($sql);
	if($requisicion == null)
	{
		return true;
	}	
	else
	{
		return false;
	}
}

function ver_proveedores($id) // muestra las facturas de proveedores asociadas al estado 
{
	global $BDA,$Calificados,$Empleados;
	// busca las facturas de los proveedores relacionadas con este estado. las busca en la base de datos administrativa
	if($Facturasp=q("select c.id,p.nombre as nprov,td.nombre as ntd,c.numero,c.fecha_emision,
		cf.nombre as ncon,scf.nombre as nscon,concat(c.descripcion,' .:. ',c.descripcion_exp) as dscr,
		concat(puc.cuenta,' - ',puc.nombre) as ncuenta, d.debito,d.credito,d.ubicacion,d.id as idd,
		c.factura_f as f1,c.factura1_f as f2,c.factura2_f as f3,c.factura3_f as f4,c.provisional1_f as p1,c.provisional2_f as p2
		FROM $BDA.factura c, $BDA.fac_detalle d,$BDA.proveedor p, $BDA.tipo_documento td ,
		$BDA.concepto_factura cf,$BDA.sub_concepto scf,$BDA.puc
		WHERE d.factura=c.id and d.ubicacion=$id and c.proveedor=p.id and td.id=c.tipo_doc and cf.id=c.concepto and 
		scf.id=c.sub_concepto and puc.cuenta=d.cuenta
		ORDER BY nprov ,debito desc"))
	{
		echo "<table border cellspacing='0' cellpadding='0' width='100%' bgcolor='ddffff' align='center'><tr>
			<td align='center' bgcolor='777777'><b>Proveedor</b></td>
			<td align='center' bgcolor='777777'><b>Factura</b></td>
			<td align='center' bgcolor='777777'><b>Fecha</b></td>
			<td align='center' bgcolor='777777'><b>Concepto</b></td>
			<td align='center' bgcolor='777777'><b>Cuenta Contable</b></td>
			<td align='center' bgcolor='777777'><b>Debito</b></td>
			<td align='center' bgcolor='777777'><b>Credito</b></td>
			<td align='center' bgcolor='777777'><b>Utilidades</b></td>
			</tr>";
			$Path='../../Administrativo/';
		while($Fp=mysql_fetch_object($Facturasp))  // muestra las imagenes de las facturas de los proveedores.
		{
			echo "<tr>
				<td>$Fp->nprov</td>
				<td>$Fp->ntd - $Fp->numero ";
				if($Fp->f1) echo "<a onclick=\"modal('$Path$Fp->f1',0,0,700,700,'verf');\" style='cursor:pointer'><img src='gifs/standar/Search.png' border='0' height='10px' alt='Ver factura' title='Ver factura'> ";
				if($Fp->f2) echo "<a onclick=\"modal('$Path$Fp->f2',0,0,700,700,'verf');\" style='cursor:pointer'><img src='gifs/standar/Search.png' border='0' height='10px' alt='Ver factura' title='Ver factura'> ";
				if($Fp->f3) echo "<a onclick=\"modal('$Path$Fp->f3',0,0,700,700,'verf');\" style='cursor:pointer'><img src='gifs/standar/Search.png' border='0' height='10px' alt='Ver factura' title='Ver factura'> ";
				if($Fp->f4) echo "<a onclick=\"modal('$Path$Fp->f4',0,0,700,700,'verf');\" style='cursor:pointer'><img src='gifs/standar/Search.png' border='0' height='10px' alt='Ver factura' title='Ver factura'> ";
				if($Fp->p1) echo "<a onclick=\"modal('$Path$Fp->p1',0,0,700,700,'verf');\" style='cursor:pointer'><img src='gifs/standar/Search.png' border='0' height='10px' alt='Ver factura' title='Ver factura'> ";
				if($Fp->p2) echo "<a onclick=\"modal('$Path$Fp->p2',0,0,700,700,'verf');\" style='cursor:pointer'><img src='gifs/standar/Search.png' border='0' height='10px' alt='Ver factura' title='Ver factura'> ";
				
				echo "<td>$Fp->fecha_emision</td>
				<td alt='$Fp->dscr' title='$Fp->dscr'>$Fp->ncon - $Fp->nscon</td>
				<td>$Fp->ncuenta</td>
				<td align='right'>".coma_format($Fp->debito)."</td>
				<td align='right'>".coma_format($Fp->credito)."</td>
				<td align='center'><a class='rinfo' onclick='desasociar_facprov($Fp->idd);'><img src='gifs/standar/Cancel.png' border='0'><span>Des-Asociar</span></a></td>
				</tr>";
		}
		echo "</table>
		<iframe name='Oculto_proveedores' id='Oculto_proveedores' style='visibility:hidden' width='1' height='1'></iframe>";
	}
	if($Calificados || $Empleados) // permite asociar facturas de proveedores al estado 
		echo "<a href='javascript:asociar_facprov();'><img src='gifs/standar/nuevo_campo.png' border='0'> Asociar una factura de proveedores</a>";
	else
		echo "<b style='color:red'>Debe calificar bienes y/o servicios para poder asociar Facturas de proveedores.</b>";
}

function ver_caja_menor($id)
{ // muestra las asociaciones de caja menor , los busca en la base de datos administrativa
	global $BDA,$Calificados,$Empleados;
	if($Cajamenor=q("select $BDA.t_oficina(c.oficina) as noficina,c.consecutivo,c.elaborado_por,c.td_contable,c.consecutivo_contable,
		d.fecha as dfecha, $BDA.t_proveedor(d.tercero) as nprov,$BDA.t_tipo_caja_menor(d.tipo) as ntipo,d.concepto,d.valor,d.id as idd
		FROM $BDA.caja_menor c,$BDA.caja_menord d
		WHERE c.id=d.caja and d.ubicacion=$id 
		ORDER BY dfecha"))
	{
		echo "<table border cellspacing='0' cellpadding='0' width='100%' bgcolor='ddccff' align='center'><tr>
			<td align='center' bgcolor='777777'><b>Consecutivo Caja</b></td>
			<td align='center' bgcolor='777777'><b>Proveedor</b></td>
			<td align='center' bgcolor='777777'><b>Tipo</b></td>
			<td align='center' bgcolor='777777'><b>Fecha</b></td>
			<td align='center' bgcolor='777777'><b>Concepto</b></td>
			<td align='center' bgcolor='777777'><b>Valor</b></td>
			<td align='center' bgcolor='777777'><b>Utilidades</b></td>
			</tr>";
		$Total_gasto=0;
		while($Cm=mysql_fetch_object($Cajamenor)) // muestra los datos de cada registro de caja menor 
		{
			echo "<tr><td nowrap='yes' alt='Elaborado por: $Cm->elaborado_por' title='Elaborado por: $Cm->elaborado_por'>$Cm->noficina [$Cm->consecutivo] ($Cm->idd)</td>
				<td>$Cm->nprov</td>
				<td>$Cm->ntipo</td>
				<td>$Cm->dfecha</td>
				<td>$Cm->concepto</td>
				<td align='right'>".coma_format($Cm->valor)."</td>
				<td align='center'><a class='rinfo' onclick='desasociar_cajamenor($Cm->idd);'><img src='gifs/standar/Cancel.png' border='0'><span>Des-Asociar</span></a></td>
				</tr>";
				$Total_gasto+=$Cm->valor;
		}
		echo "<tr><td colspan='5' bgcolor='dddddd' align='center'><b>TOTAL GASTOS</b></td><td align='right' bgcolor='dddddd'><b>".coma_format($Total_gasto)."</b></td><td bgcolor='dddddd'>&nbsp;</td></tr></table>
		<iframe name='Oculto_cajamenor' id='Oculto_cajamenor' style='visibility:hidden' width='1' height='1'></iframe>";
	}
	if($Calificados || $Empleados) // permite asociar mas gastos de caja menor al estado
		echo "<a href='javascript:asociar_cajamenor();'><img src='gifs/standar/nuevo_campo.png' border='0'> Asociar gasto de caja menor</a>";
	else
		echo "<b style='color:red'>Debe Evaluar bienes y/o servicios de requisici�n para asociar gastos de Caja Menor</b>";
}

function ver_control_operacion($id) // muestra los controles adicionales de operacion, kilometrajes, consumos etc.
{
	global $BDA;
	// busca los controles operativos asociados al estado en la base de control
	if($Control=q("select *,t_tipo_ctrl_operacion(tipo) as ntipo, t_operario(operario) as noperario, t_tipo_lavado(tipo_lavado) as nlavado,
						t_forma_pago(forma_pago) as nfpago, t_taller(taller) as ntaller
						FROM control_operacion
						WHERE ubicacion=$id order by fecha"))
	{
		echo "<table border cellspacing='0' cellpadding='0' width='100%' bgcolor='ddccff' align='center'><tr>
			<td align='center' bgcolor='777777'><b>Consecutivo interno</b></td>
			<td align='center' bgcolor='777777'><b>Tipo de Control</b></td>
			<td align='center' bgcolor='777777'><b>Fecha</b></td>
			<td align='center' bgcolor='777777'><b>Operario</b></td>
			<td align='center' bgcolor='777777'><b>Concepto</b></td>
			<td align='center' bgcolor='777777'><b>Detalles</b></td>
			</tr>";
		while($C=mysql_fetch_object($Control)) // muestra los registros de controles operativos.
		{
			echo "<tr>
				<td align='center'>$C->id</td>
				<td>$C->ntipo</td>
				<td>$C->fecha</td>
				<td>$C->noperario</td>
				<td>$C->concepto</td>
				<td>".Pinta_control($C)."</td>
				</tr>";
		}
		echo "</table>";
	}
	echo "<a href='javascript:add_control();'><img src='gifs/standar/nuevo_campo.png' border='0'> Adicionar Control de Operaciones</a>";
}

function adicionar_requisicion() // formulario para adicionar requisicion
{
	global $id,$BDA,$USUARIO;
	
	$U=qo("select * from ubicacion where id=$id"); // trae los datos de la ubicacion
	
	$V=qo("select placa from vehiculo where id=$U->vehiculo"); // trae los datos del vehiculo
	$O=qo("select ciudad from aoacol_aoacars.oficina where id=$U->oficina"); // trae los datos de la oficina
	$C=qo1("select t_ciudad('$O->ciudad')"); // trae los datos de la ciudad
	html('ADICION DE REQUISICION'); // pinta cabeceras html
	$Hoy=date('Y-m-d H:i:s');
	$Nusuario=$_SESSION['Nombre'];
	echo "<script language='javascript'>
		function grabar_n_req() {document.forma.submit();}
		function volver() {window.open('zbalance_estado.php?Acc=ver_balance&id=$id','_self');}
		function crear_nuevo_proveedor() {modal('../../Administrativo/zproveedor.php?Acc=adicion_de_proveedor','crear_nuevo_proveedor');}
        function realizar_calificacion() {window.open('../../Administrativo/zproveedor.php?Acc=realizar_calificacion&refrescar_opener=1','_self');}
		function valida_cambio_proveedor() {if(document.forma.proveedor.value) document.forma.grabar.style.visibility='visible'; else document.forma.grabar.style.visibility='hidden';}
	</script><body><h3>Adici�n de Requisici�n</h3>
	<form action='zbalance_estado.php' target='_self' method='POST' name='forma' id='forma'>
		<table>";
			
			
			if($USUARIO == 15 ){echo "<tr><td>Selecione fecha:</td><td bgcolor='ffffff'><input name='fecha' type='date'></td></tr>";
				   }else{echo "<tr><td>Fecha:</td><td bgcolor='ffffff'><input type='text' name='fecha' value='$Hoy' readonly></td></tr>";}
			
			
			echo "<tr><td>Solicitado Por:</td><td><input type='text' name='solicitado_por' value='$Nusuario' size='80' readonly></td></tr>
			<tr><td>Placa:</td><td><input type='text' name='placa' value='$V->placa' readonly></td></tr>
			<tr><td>Ciudad:</td><td><input type='hidden' name='ciudad' value='$O->ciudad'>
			<input type='text' name='nciudad' id='nciudad' value='$C' size='80' maxlength='80' readonly></td></tr>
		<!--<tr><td>Documento Referencia</td><td><input type='text' name='referencia' id='referencia' value='' size='20' maxlength='20'></td></tr>
			 <tr><td>Factura Referencia</td><td><input type='text' name='factura_referencia' id='factura_referencia' value='' size='20' maxlength='20'></td></tr> -->
			 <tr><td style='font-size:18px'>Proveedor:</td><td bgcolor='ffffff'><br>
                        La lista que aparece a continuaci�n corresponde a los proveedores totalmente confiables y medianamente confiables, de acuerdo a la selecci�n realizada siguiendo los lineamientos del proceso de Calidad. <br>
                        Tambi�n aparecen los empleados de AOA a los que se les hace anticipos.<br>
                        ";
		// busca los proveedores que sean mediana y totalmente confiables. tambi�n muestra los tipo empleados de AOA
        if($Proveedores=q("select id,nombre,calificacion_actual,$BDA.t_ciudad(ciudad) as nciudad from $BDA.proveedor where calificacion_actual in ('M','C') or tipo='E' order by nombre"))
        {
			echo "<select name='proveedor' style='width:300px' onchange='valida_cambio_proveedor();'><option value=''></option>";
			while($P=mysql_fetch_object($Proveedores)) // pinta opciones para seleccionar un proveedor
			{
					$bgc='cccccc';if($P->calificacion_actual=='M') $bgc='ffffaa';if($P->calificacion_actual=='C') $bgc='aaffaa';
					echo "<option value='$P->id' style='background-color:#$bgc'>$P->nombre [$P->nciudad]</option>";
			}
			echo "</select><br><br>
			Si el proveedor que busca no aparece en la lista, existen dos posibilidades: <br>
			<li> Que no est� creado en la base de datos. Por lo tanto puede tramitar con adquisiciones: <a style='display:none' onclick='crear_nuevo_proveedor();' class='info'><img src='img/adicionar_proveedor.png' height='30' border='0'><span style='width:100px'>Crear Proveedor</span></a>
			<li> Que si est� creado pero no haya sido seleccionado. Puede realizar la <b><i>Selecci�n</i></b> para que aparezca en la lista de proveedores dando click aqu�: <a onclick='realizar_calificacion()' class='info'><img src='img/diploma.png' height='30' border='0'><span style='width:100px'>Realizar Calificaci�n</span></a>
			";
        }
        else echo "<b style='color:red'>No hay proveedores confiables</b><br>
                Si desea puede <b><i>Seleccionar</i></b> un proveedor espec�fico dando click aqu�: <a onclick='realizar_calificacion();' class='info'><img src='img/diploma.png' height='30' border='0'><span style='width:100px'>Realizar Calificaci�n</span></a><br>
                Si desea crear un nuevo proveedor puede dar click aqu�: <a onclick='crear_nuevo_proveedor();' class='info'><img src='img/adicionar_proveedor.png' height='30' border='0'><span style='width:100px'>Crear Proveedor</span></a><br> ";
        echo "</td></tr>
			<tr><td align='center' colspan='2'><input type='button' name='grabar' id='grabar' value=' GRABAR NUEVA REQUISICION ' onclick='grabar_n_req();' style='visibility:hidden;'></td></tr>
		</table><input type='hidden' name='Acc' value='adicionar_requisicion_ok'><input type='hidden' name='id' value='$id'>
	</form>
	<input type='button' name='volver' id='volver' value=' REGRESAR AL BALANCE DE ESTADO ' onclick='volver();'>
	<br><br>NOTA: El documento referencia se utilizar� �nicamente para hacer cruce de informaci�n con requisiciones previas de grandes vol�menes de insumos o partes.
	</body>";
}

function adicionar_requisicion_ok() // guarda la informaci�n de una nueva requisici�n
{
	global $id,$fecha,$solicitado_por,$placa,$ciudad,$BDA,$referencia,$factura_referencia,$proveedor;
	/// $id es la id de la ubicacion del estado
	// $idr es la id de la cabecera de la requisicion
	
	
	$Nid=q("insert into $BDA.requisicion (fecha,solicitado_por,placa,ciudad,ubicacion,estado,perfil,referencia,factura_referencia,proveedor) 
		values ('$fecha','$solicitado_por','$placa','$ciudad','$id','','1','$referencia','$factura_referencia','$proveedor')"); // inserta la requisicion
	 echo "<body>
		<form action='zbalance_estado.php' target='_self' method='POST' name='forma' id='forma'>
			<input type='hidden' name='Acc' value='adicionar_detaller'>
			<input type='hidden' name='idr' value='$Nid'>
			<input type='hidden' name='id' value='$id'>
		</form><script language='javascript'>document.forma.submit();</script></body>";
}

function borrar_item_requisicion() // borra un item de la requisicion
{
	global $id,$BDA;
	q("delete from $BDA.requisiciond where id=$id"); // borra el item 
	// graba la bitacora del registro en la base de datos administrativa.
	q("insert into $BDA.app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."', 
			'".date('s')."','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','requisiciond','D','$id','".$_SERVER['REMOTE_ADDR']."','Borra registro')");
	echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function ver_imagen_requisicion() // muestra la imagen de la requisicion 
{
	global $id,$BDA;
	html('IMAGEN DE REQUISICION - COTIZACION');
	$D=qo("select * from $BDA.requisicion where id=$id");
	
	echo '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
	
	echo "<script language='javascript'>
		function salir()
		{
			opener.location.reload();
		}
		</script><body onunload='salir()'><h3>IMAGEN DE COTIZACION EN REQUISICION</h3>";
	// busca dentro de 3 campos del registro si ha sido cargada la imagen y da la opcion de visualizarla ampliada.
	
	
	echo '<div class="container">';
	
	$prov_select = proov_select(null);
	
	//print_r($proovedor);
	echo ' <div class="panel panel-primary">
      <div class="panel-heading">Cotizaci�n 1</div>
      <div class="panel-body">
		<div class="col-lg-6  col-md-6">';
			if($D->cotizacion_f_valor != null  and $D->cotizacion_f_proveedor != null)
			{
				$proovedor=qo("Select * from aoacol_administra.proveedor where id = ".$D->cotizacion_f_proveedor);
				echo "<br><b><span>Valor Cotizaci�n:</span><b>
				<br><br>
				<span>".$D->cotizacion_f_valor."</span>
				<br>";
				echo "<br><b><span>Proveedor:</span><b>
				<br><br>
				<p>".$proovedor->nombre."</p>
				<br>";
				if($D->cotizacion_seleccionada == null)
				{
					echo "
					<form onsubmit='asignar_cotizacion(this)'>
						<input type='hidden' name='Req_id' value='".$D->id."'>
						<input type='hidden' name='cotizacion' value='1'>
						<button class='btn btn-primary form-control'>Asignar como cotizaci�n seleccionada para compra</button>
					</form>";
				}
				else
				{
					if($D->cotizacion_seleccionada == 1)
					{
						echo "<span><strong>ESTA ES LA FACTURA SELECCIONADA</strong></span>";
					}
				}				
			}
			else
			{			
				echo '<form id="form1" action="../../Administrativo/zrequisicion.php">
					<label>Valor de la cotizaci�n</label>
					<input name="valor" min="1000" class="form-control" required>
					<input type="hidden" name="Req_id" value="'.$D->id.'">
					<input type="hidden" name="Acc" value="cotizacion_1_save">
					<label>Proveedor</label>'.$prov_select.'
					<br>
					<button class="form-control btn btn-primary">Proceder a subir imagen</button>
				</form>';	
			}
		echo '</div>
		<div class="col-lg-6  col-md-6">
			<h4>Imagen cotizaci�n</h4>';
			if($D->cotizacion_f_valor == null or $D->cotizacion_f_proveedor == null)
			{
				$display = "style='display:none'";
			}
			else{
				$display = '';
			}
			echo "<div id='cotizacion1_image' $display>";
				if($D->cotizacion_f){
					if(strpos($D->cotizacion_f,"pdf"))
					{
						echo "<embed src='../../Administrativo/$D->cotizacion_f' width='100%' height='100%' type='application/pdf'>";
					}
					else {echo "<img src='../../Administrativo/$D->cotizacion_f' width='100%' height='100%' border='0'>";}
				}
				else
				{
					echo "<iframe id='simg_cotizacion_f' name='simg_cotizacion_f' width='100%' src='../../Administrativo/marcoindex.php?Acc=reg_sube_img&T=requisicion&C=cotizacion_f&Id=$id&tri=1000&ruta=requisicion&rfr=parent.parent.location.reload()' height='100%'></iframe>";
				}
			echo "</div>";	
	echo '</div>
	  </div>
    </div>';
	
	echo "<br>";
	
	$prov_select = proov_select(null);
	
	echo ' <div class="panel panel-primary">
      <div class="panel-heading">Cotizaci�n 2</div>
      <div class="panel-body">
		<div class="col-lg-6  col-md-6">';
			if($D->cotizacion2_f_valor != null  and $D->cotizacion2_f_proveedor != null)
			{
				$proovedor=qo("Select * from aoacol_administra.proveedor where id = ".$D->cotizacion2_f_proveedor);
				echo "<br><b><span>Valor Cotizaci�n:</span><b>
				<br><br>
				<span>".$D->cotizacion2_f_valor."</span>
				<br>";
				echo "<br><b><span>Proveedor:</span><b>
				<br><br>
				<p>".$proovedor->nombre."</p>
				<br>";
				if($D->cotizacion_seleccionada == null)
				{
					echo "
					<form onsubmit='asignar_cotizacion(this)'>
						<input type='hidden' name='Req_id' value='".$D->id."'>
						<input type='hidden' name='cotizacion' value='2'>
						<button class='btn btn-primary form-control'>Asignar como cotizaci�n seleccionada para compra</button>
					</form>";
				}
				else
				{
					if($D->cotizacion_seleccionada == 2)
					{
						echo "<span><strong>ESTA ES LA FACTURA SELECCIONADA</strong></span>";
					}
				}				
			}
			else
			{
				echo '<form id="form2" action="../../Administrativo/zrequisicion.php">
					<label>Valor de la cotizaci�n</label>
					<input name="valor" min="1000" class="form-control">
					<input type="hidden" name="Req_id" value="'.$D->id.'">
					<input type="hidden" name="Acc" value="cotizacion_2_save" required>
					<label>Proveedor</label>'.$prov_select.'
					<br>
					<button class="form-control btn btn-primary">Proceder a subir imagen</button>
				</form>';
			}	
		echo '</div>
		<div class="col-lg-6  col-md-6">
			<h4>Imagen cotizaci�n</h4>';
			if($D->cotizacion2_f_valor == null or $D->cotizacion2_f_proveedor == null)
			{
				$display = "style='display:none'";
			}
			else{
				$display = '';
			}
			echo "<div id='cotizacion2_image' $display>";
			if($D->cotizacion2_f){
				if(strpos($D->cotizacion2_f,"pdf"))
				{
					echo "<embed src='../../Administrativo/$D->cotizacion2_f' width='100%' height='100%' type='application/pdf'>";
				}
				else {echo "<img src='../../Administrativo/$D->cotizacion2_f' width='100%' height='100%' border='0'>";}
			}
			else
			{
				echo "<iframe id='simg_cotizacion2_f' name='simg_cotizacion2_f' width='100%' src='../../Administrativo/marcoindex.php?Acc=reg_sube_img&T=requisicion&C=cotizacion2_f&Id=$id&tri=1000&ruta=requisicion&rfr=parent.parent.location.reload()' height='100%'></iframe>";
			}
			echo "</div>";	
	echo '</div>
	  </div>
    </div>';
	
	echo "<br>";
	
	$prov_select = proov_select(null);
	
	echo ' <div class="panel panel-primary">
      <div class="panel-heading">Cotizaci�n 3</div>
      <div class="panel-body">
		<div class="col-lg-6  col-md-6">';
			if($D->cotizacion3_f_valor != null  and $D->cotizacion3_f_proveedor != null)
			{
				$proovedor=qo("Select * from aoacol_administra.proveedor where id = ".$D->cotizacion3_f_proveedor);
				echo "<br><b><span>Valor Cotizaci�n:</span><b>
				<br><br>
				<span>".$D->cotizacion3_f_valor."</span>
				<br>";
				echo "<br><b><span>Proveedor:</span><b>
				<br><br>
				<p>".$proovedor->nombre."</p>
				<br>";
				if($D->cotizacion_seleccionada == null)
				{
					echo "
					<form onsubmit='asignar_cotizacion(this)'>
						<input type='hidden' name='Req_id' value='".$D->id."'>
						<input type='hidden' name='cotizacion' value='3'>
						<button class='btn btn-primary form-control'>Asignar como cotizaci�n seleccionada para compra</button>
					</form>";
				}
				else{
					if($D->cotizacion_seleccionada == 3)
					{
						echo "<span><strong>ESTA ES LA FACTURA SELECCIONADA</strong></span>";
					}
				}				
				
			}
			else{
				echo '<form id="form3" action="../../Administrativo/zrequisicion.php">
					<input type="hidden" name="Acc" value="cotizacion_3_save">
					<input type="hidden" name="Req_id" value="'.$D->id.'">
					<label>Valor de la cotizaci�n</label>
					<input name="valor" min="1000" class="form-control" required>
					<label>Proveedor</label>'.$prov_select.'
					<br>
					<button class="form-control btn btn-primary">Proceder a subir imagen</button>	
				</form>';
			}	
		echo '</div>
		<div class="col-lg-6  col-md-6">
			<h4>Imagen cotizaci�n</h4>';
			if($D->cotizacion3_f_valor == null or $D->cotizacion3_f_proveedor == null)
			{
				$display = "style='display:none'";
			}
			else{
				$display = '';
			}
			echo "<div id='cotizacion3_image' $display>";
			if($D->cotizacion3_f){
				if(strpos($D->cotizacion3_f,"pdf"))
				{
					echo "<embed src='../../Administrativo/$D->cotizacion3_f' width='100%' height='100%' type='application/pdf'>";
				}
				else {echo "<img src='../../Administrativo/$D->cotizacion3_f' width='100%' height='100%' border='0'>";}
			}
			else
			{
				echo "<iframe id='simg_cotizacion3_f' name='simg_cotizacion3_f' width='100%' src='../../Administrativo/marcoindex.php?Acc=reg_sube_img&T=requisicion&C=cotizacion3_f&Id=$id&tri=1000&ruta=requisicion&rfr=parent.parent.location.reload()' height='100%'></iframe>";
			}
			echo "</div>";	
	echo '</div>
	  </div>
    </div>';
	
	echo "</div>";	
	echo "</body>";
	
		echo "<script>
			$('#form1').submit(function(){
				 event.preventDefault();
				 action_url = $( '#form1' ).attr( 'action' );
				 //return alert(action_url);
				 var formData = new FormData($(this)[0]);
					$.ajax({
						url: action_url,
						type: 'POST',
						data: formData,
						async: false,
					  success: function (data) {
						  if(!data.includes('datos'))
						  {
							 location.reload(); 
						  }
						  else
						  {
							alert(data);
						    $('#cotizacion1_image').show();  
						  }						  
					  },
					  cache: false,
					  contentType: false,
					  processData: false
				  });
				//alert('submit');
			});		
		</script>";

	echo "<script>
			$('#form2').submit(function(){
				 event.preventDefault();
				 action_url = $( '#form2' ).attr( 'action' );
				 //return alert(action_url);
				 var formData = new FormData($(this)[0]);
					$.ajax({
						url: action_url,
						type: 'POST',
						data: formData,
						async: false,
					  success: function (data) {
						  if(!data.includes('datos'))
						  {
							 location.reload(); 
						  }
						  else
						  {
							alert(data);
							$('#cotizacion2_image').show();
						  }	
					  },
					  cache: false,
					  contentType: false,
					  processData: false
				  });
				//alert('submit');
			});		
		</script>";	
		
	echo "<script>
			$('#form3').submit(function(){
				 event.preventDefault();
				 action_url = $( '#form3' ).attr( 'action' );
				 //return alert(action_url);
				 var formData = new FormData($(this)[0]);
					$.ajax({
						url: action_url,
						type: 'POST',
						data: formData,
						async: false,
					  success: function (data) {
						  if(!data.includes('datos'))
						  {
							 location.reload(); 
						  }
						  else
						  {
							alert(data);
							$('#cotizacion3_image').show();
						  }	
					  },
					  cache: false,
					  contentType: false,
					  processData: false
				  });
				a//lert('submit');
			});		
		</script>";

	echo "<script>
		function asignar_cotizacion(element)
		{
			event.preventDefault();
			if(!confirm('�Esta seguro de escoger esta cotizaci�n?'))
			{
				return false;
			}
			action_url = $( '#form3' ).attr( 'action' );
			var formData = new FormData($(element)[0]);
			formData.append('Acc','asignar_cotizacion');
			console.log(action_url);
			console.log(formData);
			$.ajax({
				url: action_url,
				type: 'POST',
				data: formData,
				async: false,
			  success: function (data) {
				  alert(data);
				  location.reload();
			  },
			  cache: false,
			  contentType: false,
			  processData: false
		  });
		}		
	</script>";
	
	echo "<script>
		
		$('select[name='prov']').on('change', function(ev) {
		
		  var self = this;
		  
		  var global_id = self.value;
		  
		  $.post( 'zbalance_estado.php', { Acc:'get_data_proveedor',id:this.value }).done(function( data ) {
			response = JSON.parse(data);
			var s_proveedor =  response.proveedor;	
			if(s_proveedor.activo == 0)
			{
				if(s_proveedor.causal_inactivacion == 2)
				{
					alert('El proveedor esta inactivo por incumplimiento acuerdos comerciales');
					return false;
				}
				if(s_proveedor.causal_inactivacion == 1)
				{
					 $.post( 'zbalance_estado.php', { Acc:'check_eval_proveedor',id:self.value }).done(function( data ) {
						response = JSON.parse(data);
						
						var q = new Date();
						var m = q.getMonth()+1;
						var d = q.getDay();
						var y = q.getFullYear();

						var today = new Date(y,m,d);
						
						console.log(today);
						
						eval_date = new Date(response.evaluacion.fecha_seleccion);						
							
						var timeDiff = Math.abs(today.getTime() - eval_date.getTime());
						var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
						
						if(s_proveedor.nivel_criticidad!=null)
						{
							var max_days = 180;
						}
						else
						{
							if(s_proveedor.nivel_criticidad == 1 || s_proveedor.nivel_criticidad == 2 || s_proveedor.nivel_criticidad == 4)
							{var max_days = 360;}
							else
							{var max_days = 180;}
						}
						
						console.log(max_days);
						
						if(diffDays>max_days)
						{
							$(self).val('');
							alert('Proceda a evalular nuevamente al proveedor');
							window.open('http://app.aoacolombia.com/Administrativo/zproveedor.php?Acc=add_bienes_y_servicios&id='+global_id,'winname','directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no,width=400,height=350');
							return false;
						}
						else
						{
							 $.post( 'zbalance_estado.php', { Acc:'activate_proveedor',id:self.value }).done(function( data ) {
								console.log('Estado de proveedor actualizado');
							 });
						}
						
					 });
					
				}
				
			}
		  });
		});
	
	
	</script>";
	
}

function get_data_proveedor()
{
	$sql = "Select prov.* , selecc.nota from aoacol_administra.proveedor as prov inner join provee_seleccion as selecc on prov.id = selecc.proveedor
	where prov.id = ".$_POST["id"];
	$proveedor = qo($sql);
	$response = array("proveedor"=>$proveedor);
    echo json_encode($response);	
}

function check_eval_proveedor()
{
	$sql = "select * from aoacol_administra.provee_seleccion where proveedor = ".$_POST["id"]." order by id desc";
	$evaluation = qo($sql);
	if($evaluation != null)
	{
		$response = array("status"=>"OK","evaluacion"=>$evaluation);
	}
	else{$response = array("status"=>"NOT FOUND");}
	
	echo json_encode($response);
}

function activate_proveedor()
{
	$sql = "Update proveedor SET activo = 1 , causal_inactivacion = null where id = ".$_POST["id"];
	q($sql);
}

function proov_select($selectedvalue)
{
	if($selectedvalue == null)
	{
		$selectedvalue = 0;
	}
	
	$proovs = q("select * from aoacol_administra.proveedor order by nombre");
	
	$proovs_array = array();
	
	while($fila = mysql_fetch_object($proovs))
	{
		array_push($proovs_array, $fila);
	}
	
	$proovedor_select = "<select name='prov' class='form-control' style='width:100%;' required>";		
	$proovedor_select .= "<option value=''>Selecciona</option>";
	foreach($proovs_array as $proov)
	{
		if($proov->id == $selectedvalue)
		{
			$proovedor_select .= "<option value='".$proov->id."' selected>".$proov->nombre."</option>";
		}
		else
		{
			$proovedor_select .= "<option value='".$proov->id."'>".$proov->nombre."</option>";	
		}			
	}		
	$proovedor_select .= "</select>";
	return $proovedor_select;
}

function get_prod_type()
{
	header('Content-Type: text/html; charset=utf-8');
	global $type;
	$sql = "SELECT provee_produc_serv.id,concat(provee_produc_serv.nombre , ' = ' ,sistema.nombre, ' = ',unidad_de_medida.nombre)  as nombre
																	FROM aoacol_administra.provee_produc_serv 
																	INNER JOIN aoacol_administra.sistema ON provee_produc_serv.sistema = sistema.id  
																	INNER JOIN aoacol_administra.unidad_de_medida ON provee_produc_serv.unidad_de_medida = unidad_de_medida.id
																	where provee_produc_serv.activacion = 1  and provee_produc_serv.uso in (1,3) and tipo =  $type
																	order by provee_produc_serv.nombre";
	

	$result = q($sql);
	
	$rows = array();
	
	while($row = mysql_fetch_object($result))
	{
		$row->nombre = utf8_encode($row->nombre);
		array_push($rows, $row);
	}
	
	//print_r($rows);
	
	echo json_encode($rows);
		
}

function adicionar_detaller() // adiciona un nuevo item al detalle de la requisicion
{
	echo '<!-- Jquery  -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		
	<!-- select jquery --> 

	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script  src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

	<script>	
		$(document).ready(function() {
			/*readOnly de javascript funciona solo para leer el input y disable solo*/
			document.forma.valorTotal.readOnly = true;
			//alert("jquery is working");
			$("#tipo1").select2();
			
			$("#tipo2").change(function(){
				console.log(this);
				let valor = $(this).val();
				
				$.post( "zbalance_estado.php",{Acc:"get_prod_type",type:valor}, function( data ) {
						//console.log( data );
						let html = "";
						
						data = JSON.parse(data);
						
						console.log(data);
						
						data.forEach(function(data){
								html += "<option value="+data.id+" >"+data.nombre+"</option>";
						});
						$("#tipo1").html(html);
						$("#tipo1").select2();
				});
			});
		
		});
		
		
	</script>
	';
	
	global $idr,$id,$BDA,$USER;
	html('ADICION DE ITEM DE REQUISICION'); // pinta cabeceras y el formulario para adicionar nuevo item al detalle de la requisicion
	$Req=qo("select * from $BDA.requisicion where id=$idr");
	if($USER == 15){
		$varSqlItem = "SELECT provee_produc_serv.id,concat(provee_produc_serv.nombre , ' = ' ,sistema.nombre, ' = ', unidad_de_medida.nombre)  as nombre
																	FROM $BDA.provee_produc_serv 
																	INNER JOIN $BDA.sistema ON provee_produc_serv.sistema = sistema.id  
																	INNER JOIN $BDA.unidad_de_medida ON provee_produc_serv.unidad_de_medida = unidad_de_medida.id
																	where provee_produc_serv.activacion = 1  and provee_produc_serv.uso in (1,2,3)
																	order by provee_produc_serv.nombre";
	}else{
		$varSqlItem =  "SELECT provee_produc_serv.id,concat(provee_produc_serv.nombre , ' = ' ,sistema.nombre, ' = ', unidad_de_medida.nombre)  as nombre
                                                    FROM $BDA.provee_produc_serv
													INNER JOIN $BDA.sistema ON provee_produc_serv.sistema = sistema.id 
													INNER JOIN  $BDA.unidad_de_medida ON provee_produc_serv.unidad_de_medida = unidad_de_medida.id
													where provee_produc_serv.activacion = 1  and provee_produc_serv.uso in (1,3)
													order by provee_produc_serv.nombre";
	}
	echo "<script language='javascript'>
	function creartipos()
	{
		modal('zbalance_estado.php?Acc=tipos_requisicion',0,0,600,600,'tr');
	}
	function validar_nuevo_item(continua)
	{
		with(document.forma)
		{
			if(continua) Continuar.value='1';else Continuar.value='0';
			if(!Number(valor.value)) {alert('Debe digitar un valor presupuestado estimado, sin comas ni puntos');valor.style.backgroundColor='ffffaa';valor.focus();return false;}
			
			if(!Number(cantidad.value)) {alert('Debe digitar la cantidad, sin comas ni puntos');cantidad.style.backgroundColor='ffffaa';cantidad.focus();return false;}
			if(!alltrim(observaciones.value)) {alert('Debe digitar los comentarios respetivos de este item');observaciones.style.backgroundColor='ffffaa';observaciones.focus();return false;}
			if(!alltrim(tipo1.value)) {alert('Debera seleccionar un �tem');tipo1.style.backgroundColor='ffffaa';tipo1.focus();return false;}
			if(confirm('Desea grabar este item?'))
			{
				submit();
			}
		}
	}
	function cerrar() { opener.location.reload(); window.close(); void(null); }
	function continuar() {window.open('zbalance_estado.php?Acc=adicionar_detaller&idr=$idr&id=$id','_self');}
	function solicitar_nuevo_tipo() { window.open('zbalance_estado.php?Acc=solicitar_nuevo_bien_servicio','_self');}
	
	function Multiplicar(){
		let var1 = $('#valor').val();
		let var2 = $('#cantidad').val();
		
		let rest = var1* var2;
		console.log(rest);
		
		let v2 = rest.toFixed(2);
		document.forma.valorTotal.value = v2;
	    } 
	</script>
	
	<body><script language='javascript'>centrar(700,400);</script>
	<h3>REQUISICION: $Req->id Fecha: $Req->fecha <br>Por: $Req->solicitado_por</h3>
	<form action='zbalance_estado.php' target='Oculto_item' method='POST' name='forma' id='forma'>
		<table>

<tr><td>Seleccion� el tipo</td><td>".menu1("tipo2","SELECT id,nombre FROM $BDA.tipo;",0,1,"width:300px;")."  </td><br></tr>	
			
			<tr><td>Seleccione item</td><td>".menu1("tipo1","$varSqlItem",0,1,"width:400px;")."<br>
			Se�or Usuario, si el tipo de requisici�n no aparece en la lista, puede solicitar su creaci�n<br>
                        mediante este link:
                        <a class='info' onclick='solicitar_nuevo_tipo();'><img src='img/nuevotipo.png' height='20px'> Solicitar creaci�n de nuevo tipo.<span style='width:100px'>Solicitar creaci�n de nuevo tipo de requisici�n.</span></a>
			</td></tr>
			<tr><td>Detalles del intem</td><td><textarea name='observaciones' cols=80 rows=5></textarea></td></tr>
			<tr><td>Clase de Requisici�n</td><td>".menu1("clase","select * from $BDA.requisicionc where id = 2 or id = 1 OR id = 5 order by nombre",1)." Tipo de Cobro: <select name='tipo_cobro'>
			
			
			
			<option value='S'>SIN RECOBRO GASTO COMPA�IA</option>
									<option value='C'>CON RECOBRO</option>
									<option value='STP'>SIN RECOBRO PROTECCION TOTAL</option>
									<option value='SNR'>SIN RECOBRO GARANTIA NO REEMBOLSABLE</option>
									<option value='SAF'>SIN RECOBRO ASUME ASESOR</option>
			
			
			
			
			</select></td></tr>
			
			<tr><td>Cantidad</td><td><input type='number' class='numero' name='cantidad' id='cantidad' value='' size='10' maxlength='10' OnKeyUp='Multiplicar()'></td></tr>
			
			<tr>
				<td>Valor unitario:</td>
				<td> <input type='number' name='valor' id='valor' value='' size='10' maxlength='10' class='numero'  alt='digite el valor sin comas ni puntos' title='digite el valor sin comas ni puntos' OnKeyUp='Multiplicar()'>
			</tr>
				</td>
			<tr>
				<td>Valor total:</td>
				<td> <input type='number' name='valorTotal' id='valorTotal'  value='' size='10' maxlength='10'>
				</td>
			</tr>
			<tr>
				<td colspan=2>
					<strong><p>Nota:<br>
						IVA: Impuesto al Valor Agregado IVA (Impuesto a las Ventas IVA).<br>
						Proveedores de R�gimen Simplificado: Persona Natural no factura IVA.<br>
						Proveedores de R�gimen Com�n: Persona Natural � Jur�dica que factura IVA.<br>
						El valor que presenta la cotizaci�n puede estar incluido el IVA. Ejemplo: Valor+ IVA o Valor IVA incluido. En caso tal usted deber� realizar la operaci�n para hallar la base antes de IVA.</p>
					</strong>
				</td>
			</tr>
			<tr><td align='center' colspan='2'><input type='button' name='grabar_item' id='grabar_item' value=' GRABAR ITEM  Y VOLVER AQUI' onclick='validar_nuevo_item(1);'>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='grabar_item' id='grabar_item' value=' GRABAR ITEM  Y CERRAR' onclick='validar_nuevo_item(0);'></td></tr>
		</table>
		<input type='hidden' name='Acc' value='adicionar_detaller_ok'>
		<input type='hidden' name='idr' value='$idr'>
		<input type='hidden' name='id' value='$id'>
		<input type='hidden' name='Continuar' value=''>
	</form>
	<iframe name='Oculto_item' id='Oculto_item' style='visibility:hidden' width='1' height='1'></iframe>
	</body>
	";
}

function adicionar_detaller_ok() // inserta el nuevo registro al detalle de la requisicion
{
	global $BDA,$idr,$id,$tipo1,$clase,$valor,$observaciones,$Continuar,$tipo_cobro,$cantidad,$valorTotal;
	q("insert into $BDA.requisiciond (requisicion,tipo1,clase,valor,observaciones,tipo_cobro,cantidad,valor_total) values
			('$idr','$tipo1','$clase','$valor',\"$observaciones\",'$tipo_cobro','$cantidad','$valorTotal')");
	
	$sqlValidar = "select clase from $BDA.requisicion where id = $idr";
	
	
	
	$validar = qo($sqlValidar);
	
	if($validar->clase == 0){
		q("UPDATE $BDA.requisicion SET clase = $clase WHERE id = $idr");
	}  
	

	
	if($Continuar) echo "<body><script language='javascript'>parent.continuar();</script></body>";
	else echo "<body><script language='javascript'>parent.cerrar();</script></body>";
}

function tipos_requisicion() // funcion anterior para denotar los tipos de requisicion (dejada de usar )
{
	global $BDA;
	html('TIPOS DE REQUISICION');
	echo "<script language='javascript'>
	function validar_nuevo_tipo()
	{
		with(document.forma)
		{
			if(!alltrim(nombre.value)) {alert('Debe digitar el nombre del nuevo tipo.');nombre.style.backgroundColor='ffffdd';nombre.focus();return false;}
			document.forma.continuar.style.visibility='hidden';
			submit();
		}
	}
	</script><body><h3>TIPOS DE REQUISICION</h3>";
	$Tipos=q("select * from $BDA.requisiciont order by nombre"); 
	echo "<table border cellspacing='0'><tr>
		<th>Id</th>
		<th>Nombre</th>
		</tr>";
	while($T =mysql_fetch_object($Tipos ))
	{
		echo "<tr>
		<td align='center'>$T->id</td>
		<td>$T->nombre</td>
		</tr>";
	}
	echo "</table>
	<form action='zbalance_estado.php' target='_self' method='POST' name='forma' id='forma'>
		Nuevo Tipo: <input type='text' name='nombre' id='nombre' value='' size='60' maxlength='250'>
		<input type='button' name='continuar' id='continuar' value=' GRABAR ' onclick='validar_nuevo_tipo();'>
		<input type='hidden' name='Acc' value='crear_nuevo_tipo_requisicion'>
	</form>
	<input type='button' name='regresar' id='regresar' value=' REGRESAR AL DETALLE DE REQUISICION ' onclick='opener.location.reload();window.close();void(null);'></body>";
}

function crear_nuevo_tipo_requisicion() // funcion que crea nuevo tipo de requisicion (dejada de usar)
{
	global $nombre,$BDA;
	q("insert into $BDA.requisiciont (nombre) values ('$nombre')");
	header("location:zbalance_estado.php?Acc=tipos_requisicion");
}

function cerrar_requisicion() // cierra una requisicion 
{
	global $id,$BDA;
	q("update $BDA.requisicion set cerrada=1,estado=1 where id=$id"); // actualiza los estados y el cierre de la requisicion
	echo "<body><script language='javascript'>parent.recargar();</script></body>";
	enviar_mail_solicitud_aprobacion(); // llama la funci�n de envio del correo para aprobacion
}

function enviar_mail_solicitud_aprobacion() // envia un correo para aprobacion de la requisicion control operativo
{
	global $id,$BDA,$Aviso;
	
	$ER=qo("select requisicion.placa,
     	concat( oficina.centro_operacion,' ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto,aseguradora.nombre as ASEGURADORA, ubicacion.flota,requisiciond.centro_operacion 
				 from aoacol_administra.requisiciond 
				 LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
				 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 inner JOIN aoacol_aoacars.oficina on ubicacion.oficina = oficina.id
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion.id = $id");
				 
				 
	$D=qo("select * from $BDA.requisicion where id=$id"); // trae la informaci�n de la requisicion
	$DV=qo("select * from aoacol_aoacars.vehiculo where placa='$D->placa'");
	$HU=qo("select ubicacion from $BDA.requisicion where id = $id");
	$HT=qo("select * from aoacol_aoacars.ubicacion where id='$HU->ubicacion'");
	$li=qo("select * from aoacol_aoacars.linea_vehiculo where id='$DV->linea'");
	$Mr=qo("select * from aoacol_aoacars.marca_vehiculo where id = '$li->marca'");
	$Prov=qo("select * from $BDA.proveedor where id=$D->proveedor");
	$Ciu=qo1("select t_ciudad('$D->ciudad')"); // trae la informaci�n de la ciudad
	$Pr=qo("select * from $BDA.perfil_requisicion where id=$D->perfil"); // trae la informaci�n del perfil que aprueba la requisici�n
	$Email_usuario=usuario('email'); // obtiene el email del usuario
	if(!$Email_usuario) {
	echo "<body><script language='javascript'>alert('SU SESION EN ESTE SISTEMA ESTA CAIDA, NO SE PUEDE ENVIAR EL CORREO DE SOLICITUD DE AUTORIZACION');</script></body>"; die();}
	$Hoy=date('Y-m-d H:i:s');
	
	if($Pr->contingencia) {$Email_aprobador=$Pr->email_aprobacion2;$Nombre_aprobador=$Pr->aprobado_por2;}
	//obtiene perfil de contingencia para la aprobaci�n
	elseif($ER->centrocosto == 411 || $ER->flota == 23 || $ER->centro_operacion == 20){
		$Email_aprobador = 'gabriel.sandoval@transorientesas.com';
	    $Nombre_aprobador = 'Gabriel Sandoval';
		}else{
	    $Email_aprobador=$Pr->email_aprobacion;
		$Nombre_aprobador=$Pr->aprobado_por;
		} // perfil estandar de aprobaci�n
	// construye una ruta de correo para la aprobacion por el funcionario adecuado
	$Ruta_correo="utilidades/Operativo/operativo.php?id=$id&Fecha=$Hoy&Usuario=$Nombre_aprobador&eUsuario=$Email_aprobador&Solicitado_por=".$_SESSION['Nombre']."&eSolicitado_por=$Email_usuario";
	$Cotizaciones='';
	// incluye las rutas para ver las imagenes de las cotizaciones
	if($D->cotizacion_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 1 </u></a><br>";
	if($D->cotizacion2_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion2_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 2 </u></a><br>";
	if($D->cotizacion3_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion3_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 3 </u></a><br>";
	if(!$Cotizaciones) $Cotizaciones="No hay imagenes cargadas";
	// hay dos rutas una para aprobaci�n y una para el rechazo
	$Ruta_aprobacion=base64_encode("\$Programa='$Ruta_correo&Acc=aprobar_requisicion&observaciones='.\$observaciones.'&cotapr='.\$cotapr;\$Fecha_control=date('Y-m-d');"); 
	$Ruta_daprobacion=base64_encode("\$Programa='$Ruta_correo&Acc=daprobar_requisicion&observaciones='.\$observaciones;\$Fecha_control=date('Y-m-d');"); 
	$Fecha_control=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),30)));
	// incluye el detalle de la requisicion
	$Det="<table border class='table' cellspacing='0'><tr><th>Tipo de Requisicion</th><th>Item</th><th>Unidad de medida</th><th>Descripcion</th><th>Centro de operacion</th><th>Cantidad</th><th>Valor unitario</th><th>Valor</th>";
	$Detalle=q("select requisiciond.requisicion,provee_produc_serv.nombre as item,tipo.nombre as tipo, unidad_de_medida.nombre as unidad_medida,
				 requisiciond.observaciones,requisiciond.cantidad,requisiciond.valor as valor_unitario, requisiciond.valor_total,requisicion.placa, 
				 concat( oficina.centro_operacion,' ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto, requisicion.fecha 
				 from aoacol_administra.requisiciond 
				 LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
				 LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id 
				 LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id 
				 LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id 
				 LEFT OUTER JOIN aoacol_administra.requisicionc on requisiciond.clase = requisicionc.id 
				 LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
				 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 inner JOIN aoacol_aoacars.oficina on ubicacion.oficina = oficina.id
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion =$id");
	while($Dt =mysql_fetch_object($Detalle ))
	{
		$Det.="<tr><td>$Dt->tipo</td><td>$Dt->item</td><td>$Dt->unidad_medida</td><td>$Dt->observaciones</td><td>$Dt->centrodeoperacion</td><td>$Dt->cantidad</td><td align='right'>$".coma_format($Dt->valor_unitario)."</td><td align='right'>$".coma_format($Dt->valor_total)."</td></tr>";
	}
	$Det.="</table>";
	
	$Res="<table border cellspacing='4'><tr><th>Resultado</th>";
        //echo "select *,t_requisiciont(tipo) as ntipo, t_requisicionc(clase) as nclase from requisiciond where requisicion=$id";
		$retorno=q("select requisiciond.requisicion,requisiciond.valor_total,
		            sum(requisiciond.valor_total) as resultado 
					from aoacol_administra.requisiciond
					where requisicion  =$id");
        while($Dt =mysql_fetch_object($retorno))
        {
           $Res.="<tr><td>$".coma_format($Dt->resultado)."</td>";
        }
        $Res.="</table>";
		
	//"$Email_aprobador,$Nombre_aprobador" /*para */,    "arturoquintero@aoacolombia.com,ARTURO QUINTERO",  
	$Ruta_alterna=base64_encode("header('location:../Control/operativo/zbalance_estado.php?Acc=aprobacion_requisicion&id=$id');");
	// envia el correo al funcionario que debe aprobar esa requisicion
	 echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
		  <script>
            $.ajax(
                    {
                        url: 'https://sac.aoacolombia.com/enviar.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							para:'$Email_aprobador',
						    copia:'',
							contenido:'3',
							asunto:'REQUISICION NUMERO $id',
							id:'$id',
							proveedor :  '$Prov->nombre',
							fecha: '$D->fecha',
							ciudad:'$Ciu',
							solicitado:'$D->solicitado_por',
							Hoy  : ' $Hoy',
	                        Nombre_aprobador  :  '$Nombre_aprobador',
	                        Email_aprobador  :  '$Email_aprobador',
	                        Email_usuario:'  $Email_usuario',
                            user :'$user',
							Nick :'$Nick ',
							placa:' $D->placa',
							modelo :' $DV->modelo',
							marca :' $Mr->nombre',
							linea:' $li->nombre',
							fecha_inicial :'$HT->fecha_inicial',
							Fecha_control: '$Fecha_control',
							odometro_inicial :'$HT->odometro_inicial'
							},
							
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
					window.close();
        </script>
	
				</body>";	
	
	      echo "<body><script language='javascript'>alert('Email enviado satisfactoriamente');</script></body>";
}

function asociar_facprov() // formulario que asocia una factura de proveedor al balance de estado
{
	global $id,$BDA;
	html('ASOCIACION DE FACTURA DE PROVEEDOR');
	echo "<script language='javascript'>
	function buscar_asociaciones()
	{
		document.forma.submit();
	}</script><body><script language='javascript'>centrar(s_ancho(),600);</script>
		<h3>ASOCIACION DE FACTURA DE PROVEEDORES</h3>
		<form action='zbalance_estado.php' target='tablero_asociacion' method='POST' name='forma' id='forma'>
			<B>CRITERIOS DE BUSQUEDA</B><BR>
			Busqueda por proveedor: <input type='text' name='proveedor' id='proveedor' value='' size='80' maxlength='100' onblur='this.value=this.value.toUpperCase();' ><br>
			Fecha inicial: ".pinta_FC('forma','FI')." Fecha final: ".pinta_FC('forma','FF')." <br>
			Busqueda por Factura: <input type='text' name='factura' id='factura' value='' size='10' maxlength='20'>
			Descripcion: <input type='text' name='descripcion' id='descripcion' value='' size='20' maxlength='100'>
			<input type='button' name='buscar' id='buscar' value=' BUSCAR ' onclick='buscar_asociaciones();'>
			<input type='hidden' name='Acc' value='asociar_facprov_buscar'><input type='hidden' name='ubicacion' value='$id'>
		</form>
		<iframe name='tablero_asociacion' id='tablero_asociacion' style='visibility:visible' width='100%' height='80%'></iframe>
		</body>";
}

function asociar_facprov_buscar() // busca las facturas de proveedores que coincidan con unos criterios dados.
{
	global $BDA,$proveedor,$FI,$FF,$factura,$descripcion,$ubicacion;
	html();
	$U=qo("select * from ubicacion where id=$ubicacion"); // dato de la ubicaci�n del estado
	$V=qo("select * from vehiculo where id=$U->vehiculo"); // datos del vehiuculo
	
	// AQUI HAY QUE TENER EN CUENTA QUE EL DETALLE DE UNA FACTURA DONDE HAY RELACION DEBE IR POR PLACA SEPARADAMENTE.
	// EJEMPLO LA FACTURA DE MARTINEZ DIAZ CARLOS ENRIQUE NO. 58488 DE 21 DE JUNIO 2012 QUE ES POR LAVADO DE VEHICULOS.
	
	echo "<script language='javascript'>
		function asociar_facprov(id)
		{if(confirm('Desea asociar este gasto al balance de estado del vehiculo?'))
			{window.open('zbalance_estado.php?Acc=asociar_facprov_ok&id_detalle='+id+'&ubicacion=$ubicacion','Oculto_asocia');}}
		</script><body>
		<h3>ASIGNACION DE FACTURAS DE PROVEEDORES</h3>";
	if($proveedor || $FI || $FF || $factura || $descripcion) // construye filtros de acuerdo a los criterios dados
	{
		$Filtro='';
		if($proveedor) $Filtro.=" and p.nombre like '%$proveedor%' ";
		if($FI) $Filtro.=" and c.fecha_emision >='$FI' ";
		if($FF) $Filtro.=" and c.fecha_emision <='$FF' ";
		if($factura) $Filtro.=" and c.numero like '%$factura%' ";
		if($descripcion) $Filtro.=" and concat(c.descripcion,' ',c.descripcion_exp) like '%$descripcion%' ";
		// arma la consulta en la tabla
		$Consulta=" SELECT p.nombre as nprov,c.numero,c.fecha_emision,cf.nombre as ncon,scf.nombre as nscon,concat(c.descripcion,' .:. ',c.descripcion_exp) as dscr,
							concat(puc.cuenta,' - ',puc.nombre) as ncuenta,if(d.placa='',c.placa,d.placa) as placa,d.debito,d.credito,d.ubicacion,d.id as idd,
							c.factura_f as f1,c.factura1_f as f2,c.factura2_f as f3,c.factura3_f as f4,c.provisional1_f as p1,c.provisional2_f as p2
							FROM $BDA.factura c, $BDA.fac_detalle d, $BDA.proveedor p, $BDA.concepto_factura cf, $BDA.sub_concepto scf, $BDA.puc 
							WHERE c.id=d.factura and p.id=c.proveedor and cf.id=c.concepto and scf.id=c.sub_concepto and 
							puc.cuenta=d.cuenta and if(d.placa!='',d.placa='$V->placa',if(c.placa!='',c.placa='$V->placa',1)) 
							$Filtro ORDER BY nprov ,debito desc";
		if($Facturas=q($Consulta)) // si encuentra registros los muestra 
		{
			echo "<table border cellspacing='0' width='100%'><tr>
				<th>PROVEEDOR</th>
				<th>FACTURA</th>
				<th>FECHA</th>
				<th>CONCEPTO</th>
				<th>CUENTA CONTABLE</th>
				<th>PLACA</th>
				<th>DEBITO</th>
				<th>CREDITO</th>
				<th>Asociaci�n</th>
				</tr>";
			$Path='../../Administrativo/';
			while($F =mysql_fetch_object($Facturas)) // muestra registro por registro 
			{
				echo "<tr>
				<td>$F->nprov</td>
				<td>$F->numero "; // si hay imagenes de facturas en el registro del proveedor las muestra
				if($F->f1) echo "<a onclick=\"modal('$Path$F->f1',0,0,700,700,'verf');\" style='cursor:pointer'><img src='gifs/standar/Search.png' border='0' height='10px' alt='Ver factura' title='Ver factura'> ";
				if($F->f2) echo "<a onclick=\"modal('$Path$F->f2',0,0,700,700,'verf');\" style='cursor:pointer'><img src='gifs/standar/Search.png' border='0' height='10px' alt='Ver factura' title='Ver factura'> ";
				if($F->f3) echo "<a onclick=\"modal('$Path$F->f3',0,0,700,700,'verf');\" style='cursor:pointer'><img src='gifs/standar/Search.png' border='0' height='10px' alt='Ver factura' title='Ver factura'> ";
				if($F->f4) echo "<a onclick=\"modal('$Path$F->f4',0,0,700,700,'verf');\" style='cursor:pointer'><img src='gifs/standar/Search.png' border='0' height='10px' alt='Ver factura' title='Ver factura'> ";
				if($F->p1) echo "<a onclick=\"modal('$Path$F->p1',0,0,700,700,'verf');\" style='cursor:pointer'><img src='gifs/standar/Search.png' border='0' height='10px' alt='Ver factura' title='Ver factura'> ";
				if($F->p2) echo "<a onclick=\"modal('$Path$F->p2',0,0,700,700,'verf');\" style='cursor:pointer'><img src='gifs/standar/Search.png' border='0' height='10px' alt='Ver factura' title='Ver factura'> ";
				echo "</td><td nowrap='yes'>$F->fecha_emision</td>
					<td alt='$F->dscr' title='$F->dscr'>$F->ncon - $F->nscon</td>
					<td>$F->ncuenta</td>
					<td ".($F->placa==$V->placa?" bgcolor='ffff22' ":"").">$F->placa</td>
					<td align='right'>".coma_format($F->debito)."</td>
					<td align='right'>".coma_format($F->credito)."</td>";
				// si ya aparece asociada, muestra en la pantalla que ya tiene asociacion con un estado 
				if($F->ubicacion) echo "<td align='center' bgcolor='aaffbb'><img src='gifs/standar/si.png' border='0'> Asociada</td>";
				else echo "<td align='center'><a style='cursor:pointer' onclick='asociar_facprov($F->idd);'><img src='gifs/standar/derecha.png' border='0' alt='Asociar' title='Asociar'></td>";
				echo "</tr>";
			}
			echo "</table>
			<iframe name='Oculto_asocia' id='Oculto_asocia' style='visibility:hidden' width='1' height='1'></iframe>";
		}
		else
		{
			echo "NO SE ENCUENTRAN RESULTADOS CON LOS CRITERIOS DADOS";
		}
	}
	else
	{
		echo "DEBE DIGITAR POR LO MENOS UNO DE LOS CRITERIOS DE BUSQUEDA.";
	}
	echo "</body>";
}

function solicitar_nuevo_bien_servicio()
{
	$retorno=q("select * from aoacol_administra.sistema");while($Dt =mysql_fetch_object($retorno)){$Res.= "<option value='$Dt->nombre'>$Dt->nombre</option>";}
        html('SOLICITUD DE CREACION BIEN-SERVICIO');
        echo "<script language='javascript'>
                function enviar_solicitud()
                {
                        with(document.forma)
                        {
                                if(!tipo.value) {alert('Debe seleccionar si es un bien o un servicio');tipo.style.backgroundColor='ffffaa';return false;}
                                if(!alltrim(nombre.value)) {alert('Debe escribir un nombre'); nombre.style.backgroundColor='ffffaa'; return false;}
                                submit();
                        }
                }
                </script><body><h3>SOLICITUD DE CREACION DE BIEN O SERVICIO</h3>
				
				<style>
				.alin{
					display: flex;
                    /*justify-content: space-evenly;*/
                    align-items: center;
					
				}
				.aliniar{
					
                    /*justify-content: space-evenly;*/
                    width: 100%;
					display: flex;
					align-items: center;
					
				}
				.aliniar-select{
					
				}
				.boton_continuar{
					display: flex;
                    justify-content: center;
					
				}
				</style>
                <form action='' target='_self' method='POST' name='forma' id='forma'>
                        <div class='alin'>
						Tipo : <select name='tipo'><option value=''></option>
                                        <option value='B'>BIEN</option>
                                        <option value='S'>SERVICIO</option></select><br><br>
                        
						
						<p>Nombre:</p> <input type='text' name='nombre' id='nombre' value='' size='80' maxlength='200' onkeyup='this.value=this.value.toUpperCase();'><br><br>
						</div>
						<div class='aliniar'>
						<label>Descripcion de su solicitud:</label> <textarea type='textarea' rows='8' cols='90' name='descricion_nuevo' id='descricion_nuevo' value='' size='80' maxlength='200'></textarea><br><br>
						</div>
						<div class='centrar'>
						
						<div class='aliniar-select'>
						<p>Frecuancia de compra:</p>  
						<select name='frecuencia_compra' id='frecuencia_compra'>
						<option value='DIARIO'>DIARIO</option>
						<option value='MENSUAL'>MENSUAL</option>
						<option value='ANUAL'>ANUAL</option>
						</select>
						</div>
						<div class='aliniar-select'>
						<p>Uso:</p>  
						<select name='uso' id='uso'>
						<option value='OPERATIVO'>OPERATIVO</option>
						<option value='ADMINISTRATIVO'>ADMINISTRATIVO</option>
						<option value='COMUN'>COMUN</option>
						<option value='OPERACIONES'>OPERACIONES</option>
						</select>
						</div>
						<div class='aliniar-select'>
						<p>Sistema:</p>  
					    <select name='sistema' id='sistema'>
						$Res
						</select>
						</div>
						<div class='aliniar-select'>
						<p>Unidad de medida:</p>  
						<select name='unidad_medida' id='unidad_medida'>
						<option value='UD-UNIDAD'>UD-UNIDAD</option>
						<option value='CUARTO-GALON'>CUARTO-GALON</option>
						</select>
						</div>
						</div>
                        <div class='boton_continuar'>
						<input type='button' name='continuar' id='continuar' value=' CONTINUAR ' onclick='enviar_solicitud();'>
						</div>
						
                        
						<input type='hidden' name='Acc' value='solicitar_nuevo_bien_servicio_ok'>
                </form></body>";
}

function solicitar_nuevo_bien_servicio_ok()
{
        global $NUSUARIO,$tipo,$nombre,$descricion_nuevo,$frecuencia_compra,$uso,$sistema,$unidad_medida;
        $Email_usuario=usuario('email');
        if($tipo=='B') $Ntipo='BIEN';
        if($tipo=='S') $Ntipo='SERVICIO';
        if(enviar_gmail($Email_usuario,$NUSUARIO,'claudiacastro@aoacolombia.com,CLAUDIA CASTRO',
        'sergiocastillo@aoacolombia.com;dirop@aoacolombia.com;
		rociocruz@aoacolombia.com','SOLICITUD DE CREACION DE BIEN O SERVICIO',
        nl2br("Estimados Se�or@s Claudia Castro y/o Director Operativo,
        Reciba cordial saludo.

        Por medio del presente correo solicito el favor de crear el siguiente item dentro de la tabla de bienes y servicios:

        Tipo: $Ntipo
        Nombre: $nombre
		Descripcion: $descricion_nuevo
		Frecuencia de compra: $frecuencia_compra
		Uso: $uso
		Sistema: $sistema
		Unidad de medida: $unidad_medida

        Cordialmente,

        $NUSUARIO
        $Email_usuario
        ")))
        echo "<body><script language='javascript'>alert('Solicitud enviada satisfactoriamente'); window.close();void(null);</script></body>";
}




function registrar_nuevo_seguro(){
	header('Content-Type: text/html; charset=utf-8');
    echo "<form action='zbalance_estado.php'  enctype='multipart/form-data' method='POST' name='forma' id='forma'>";
	
    include("views/subviews/formulario_seguros.html");
	$cotizacion1 = $Req->cotizacion_f; 
		
		$cotizaciones_table ="<div>
						<table class='table ' border width='33%'>
							<thead>
								<tr>
									<th colspan='3'>Subir Caratula</th>
								</tr>
							</thead>
							<tbody>";
		$cotizaciones_table .=	"<tr>";
		$cotizaciones_table .=	"<td width='33%' height='165px'>";
		$cotizaciones_table .= "<input type='file' name='image' required>";
		$cotizaciones_table .= "<br>";
		$cotizaciones_table .=	"</td>";
            $cotizaciones_table .=	"</tr>
							</tbody>	
						</table>	
					</div></div>";
					
		
		echo $cotizaciones_table;
		echo "<input type='submit' name='continuar' id='continuar' value=' CONTINUAR '>
		<input type='hidden' name='Acc' value='registrar_nuevo_seguro_ok'>
		</form>
		";
		

		}
function registrar_nuevo_seguro_ok(){
	global $n_poliza,$vigencia_hasta,$vigencia_desde,$corredor,$poliza,$codigo_fasecolda,$poliza_seguros,$n_asistencia,$caratula_imagen_f;
    
	$file_url = '/var/www/html/public_html/Control/operativo/seguros_img/';
	opendir($file_url);
	$destino = $file_url.$_FILES['image']['name'];
	copy($_FILES['image']['tmp_name'],$destino);
	//move_uploaded_file($_FILES['image']['tmp_name'],$destino);
	$nombre = $_FILES['image']['name'];
	//echo '<img src=seguros_img/'.$nombre.'>';
	$var_caratula_poliza = 'seguros_img/'.$nombre.'';
    q("insert into seguros (n_poliza,vigencia_desde,vigencia_hasta,
		corredor,poliza,codigo_fasecolda,poliza_seguros,linea_asistencia,
		caratula_imagen_f) values ($n_poliza,'$vigencia_desde','$vigencia_hasta', '$corredor','$poliza',$codigo_fasecolda,'$poliza_seguros','$n_asistencia','$var_caratula_poliza')");
	echo "<body><script language='javascript'>alert('Informacion grabada satisfactoriamente');parent.cerrar();</script></body>";
}


function asociar_facprov_ok() // asocia la factura de proveedor a un estado
{
	global $id_detalle,$ubicacion,$BDA;
	q("update $BDA.fac_detalle set ubicacion='$ubicacion' where id='$id_detalle' "); // hace la asociacion
	echo "<body><script language='javascript'>parent.parent.buscar_asociaciones();</script></body>";
}

function desasociar_facprov() // quita la asociaci�n de una factura al balance de estado.
{
	global $id,$BDA;
	q("update $BDA.fac_detalle set ubicacion=0 where id=$id"); // quita la asociacion
	echo "<body><script language='javascript'>parent.parent.recargar();</script></body>";
}

function asociar_cajamenor() // formulario para asociar una caja menor al balance de estado
{
	global $id,$BDA;
	html('ASOCIACION DE CAJAMENOR');
	echo "<script language='javascript'>
	function buscar_asociaciones2()
	{
		document.forma.submit();
	}
	</script><body><script language='javascript'>centrar(s_ancho(),600);</script>
		<h3>ASOCIACION DE CAJA MENOR</h3>
		<form action='zbalance_estado.php' target='tablero_asociacion2' method='POST' name='forma' id='forma'>
			<B>CRITERIOS DE BUSQUEDA</B><BR>
			Busqueda por proveedor: <input type='text' name='proveedor' id='proveedor' value='' size='80' maxlength='100' onblur='this.value=this.value.toUpperCase();' ><br>
			Fecha inicial: ".pinta_FC('forma','FI')." Fecha final: ".pinta_FC('forma','FF')." <br>
			Concepto: <input type='text' name='concepto' id='concepto' value='' size='20' maxlength='100'>
			<input type='button' name='buscar' id='buscar' value=' BUSCAR ' onclick='buscar_asociaciones2();'>
			<input type='hidden' name='Acc' value='asociar_cajamenor_buscar'><input type='hidden' name='ubicacion' value='$id'>
		</form>
		<iframe name='tablero_asociacion2' id='tablero_asociacion2' style='visibility:visible' width='100%' height='80%'></iframe>
		</body>";
}

function asociar_cajamenor_buscar() // buysca los detalles de la caja menor para asociarlos al balance de estado de acuerdo a un criterio
{
	global $BDA,$proveedor,$FI,$FF,$concepto,$ubicacion;
	html();
	$U=qo("select * from ubicacion where id=$ubicacion"); // datos de la ubicacion
	$V=qo("select * from vehiculo where id=$U->vehiculo"); // datos del vehiculo
	$O=qo("select nombre from oficina where id=$U->oficina"); // datos de la oficina
	// AQUI HAY QUE TENER EN CUENTA QUE EL DETALLE DE UNA FACTURA DONDE HAY RELACION DEBE IR POR PLACA SEPARADAMENTE.
	// EJEMPLO LA FACTURA DE MARTINEZ DIAZ CARLOS ENRIQUE NO. 58488 DE 21 DE JUNIO 2012 QUE ES POR LAVADO DE VEHICULOS
	echo "<script language='javascript'>
		function asociar_cajamenor(id)
		{
			if(confirm('Desea asociar este gasto al balance de estado del vehiculo?'))
			{
				window.open('zbalance_estado.php?Acc=asociar_cajamenor_ok&id_detalle='+id+'&ubicacion=$ubicacion','Oculto_asocia2');
			}
		}
		</script><body>
		<h3>ASIGNACION DE GASTOS DE CAJA MENOR</h3>";
	if($proveedor || $FI || $FF || $concepto) // si el usuario dio criterios de asociacion construye la consulta de lo contrario no la hace
	{
		$Filtro='';
		if($proveedor) $Filtro.=" and p.nombre like '%$proveedor%' ";
		if($FI) $Filtro.=" and d.fecha >='$FI' ";
		if($FF) $Filtro.=" and d.fecha <='$FF' ";
		if($descripcion) $Filtro.=" and d.concepto like '%$concepto%' ";
		// construye la consulta
		$Consulta=" SELECT p.nombre as nprov,c.consecutivo,d.fecha,t.nombre as ntipo,d.concepto,d.valor,d.placa,d.id as idd,d.ubicacion,o.nombre as nofic
							FROM $BDA.caja_menor c,$BDA.caja_menord d,$BDA.proveedor p,$BDA.tipo_caja_menor t,$BDA.oficina o
							WHERE c.id=d.caja and p.id=d.tercero and t.id=d.tipo and if(d.placa!='',d.placa='$V->placa',1) and 
							o.id=c.oficina and o.nombre like '%$O->nombre%' 							
							$Filtro ORDER BY nprov";
		if($Facturas=q($Consulta)) // si encuentra registros de caja menor
		{
			echo "<table border cellspacing='0' width='100%'><tr>
				<th>OFICINA</th>
				<th>PROVEEDOR</th>
				<th>CONSECUTIVO</th>
				<th>FECHA</th>
				<th>TIPO</th>
				<th>CONCEPTO</th>
				<th>PLACA</th>
				<th>VALOR</th>
				<th>Asociaci�n</th>
				</tr>";
			$Path='../../Administrativo/';
			while($F =mysql_fetch_object($Facturas)) // muestra registro por registro de caja menor
			{
				echo "<tr>
				<td>$F->nofic</td>
				<td>$F->nprov</td>
				<td>$F->consecutivo</td>
				<td nowrap='yes'>$F->fecha</td>
				<td>$F->ntipo</td>
				<td>$F->concepto</td>
				<td ".($F->placa==$V->placa?" bgcolor='ffff22' ":"").">$F->placa</td>
				<td align='right'>".coma_format($F->valor)."</td>";
				// si el registro de caja menor ya est� asociado lo muestra 
				if($F->ubicacion) echo "<td align='center' bgcolor='aaffbb'><img src='gifs/standar/si.png' border='0'> Asociada</td>";
				else echo "<td align='center'><a style='cursor:pointer' onclick='asociar_cajamenor($F->idd);'><img src='gifs/standar/derecha.png' border='0' alt='Asociar' title='Asociar'></td>";
				echo "</tr>";
			}
			echo "</table>
			<iframe name='Oculto_asocia2' id='Oculto_asocia2' style='visibility:hidden' width='1' height='1'></iframe>";
		}
		else
		{
			echo "NO SE ENCUENTRAN RESULTADOS CON LOS CRITERIOS DADOS. ".($_SESSION['User']==1?$Consulta:"");
		}
	}
	else
	{
		echo "DEBE DIGITAR POR LO MENOS UNO DE LOS CRITERIOS DE BUSQUEDA.";
	}
	echo "</body>";
}

function asociar_cajamenor_ok() // asocia el registro de caja menor al balance de estado
{
	global $id_detalle,$ubicacion,$BDA;
	q("update $BDA.caja_menord set ubicacion='$ubicacion' where id='$id_detalle' "); // hase la asociacion
	echo "<body><script language='javascript'>parent.parent.buscar_asociaciones2();</script></body>";
}

function desasociar_cajamenor() // quita la asociacion de una caja menor del balance de estado
{
	global $id,$BDA;
	q("update $BDA.caja_menord set ubicacion=0 where id=$id"); // quita la asociacion
	echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function adicionar_control_operaciones() // formulario para adicionar control de operaciones.
{
	global $id;
	html('ADICION DE CONTROL DE OPERACIONES');
	// de acuerdo al tipo de operacion el sistema muestra unos menus y opciones en un formulario para adicionar datos adicionales relacionados al estado o ubicaci�n del vehiculo.
	$menu_tipo_domicilio="<select name='tipo_domicilio'><option value=''></option><option value='E'>Entrega</option><option value='D'>Devolucion</option></select>";
	$menu_hora_inicial=pinta_chora(1);
	$menu_hora_evento=pinta_chora(2);
	$menu_hora_final=pinta_chora(3);
	$captura_domicilio="<table><tr><td>Tipo Domicilio</td><td>$menu_tipo_domicilio</td></tr>".
									"<tr><td>Ruta</td><td><input type='text' name='ruta' id='ruta' value='' size='50' maxlength='100'></td></tr>".
									"<tr><td>Hora Salida</td><td>$menu_hora_inicial</td></tr>".
									"<tr><td>Hora Entrega/Devolucion</td><td>$menu_hora_evento</td></tr>".
									"<tr><td>Hora Llegada</td><td>$menu_hora_final</td></tr>".
									"<tr><td>Kilometraje inicial:</td><td><input type='text' name='km_inicial' id='km_inicial' value='' size='10' maxlength='10'></td></tr>".
									"<tr><td>Kilometraje final:</td><td><input type='text' name='km_final' id='km_final' value='' size='10' maxlength='10'></td></tr>".
									"</table>";
	$menu_tipo_lavado="<select name='tipo_lavado'><option value=''></option>";
	$Tipos_lavado=q("select id,nombre from tipo_lavado order by id");
	while($Tl=mysql_fetch_object($Tipos_lavado)) $menu_tipo_lavado.="<option value='$Tl->id'>$Tl->nombre</option>";
	$menu_tipo_lavado.="</select>";
	$captura_lavado="<table><tr><td>Tipo de lavado:</td><td>$menu_tipo_lavado</td></tr>"."<tr><td>Kilometraje inicial:</td><td><input type='text' name='km_inicial' id='km_inicial' value='' size='10' maxlength='10'></td></tr>".
									"<tr><td>Kilometraje final:</td><td><input type='text' name='km_final' id='km_final' value='' size='10' maxlength='10'></td></tr>".
									"</table>";
	$menu_forma_pago="<select name='forma_pago'><option value=''></option>";
	$Formas_pago=q("select id,nombre from forma_pago order by id");
	while($Fp=mysql_fetch_object($Formas_pago)) $menu_forma_pago.="<option value='$Fp->id'>$Fp->nombre</option>";
	$menu_forma_pago.="</select>";
	$captura_combustiblen="<table><tr><td>Numero de Galones</td><td><input type='text' class='numero' name='galones' id='galones' value='' size='10' maxlength='10'></td></tr>".
											"<tr><td>Forma de Pago</td><td>$menu_forma_pago</td></tr></table>";
	$menu_talleres="<select name='taller'><option value=''></option>";
	$Talleres=q("select id,concat(t_ciudad(ciudad),' : ',nombre) as ntaller from taller order by ntaller");
	while($Ta=mysql_fetch_object($Talleres)) $menu_talleres.="<option value='$Ta->id'>$Ta->ntaller</option>";
	$menu_talleres.="</select> <a style='cursor:pointer' onclick='mas_talleres();'>Crear mas talleres</a>";
	$captura_combustiblet="<table><tr><td>Numero de Galones</td><td><input type='text' class='numero' name='galones' id='galones' value='' size='10' maxlength='10'></td></tr>".
											"<tr><td>Forma de Pago</td><td>$menu_forma_pago</td></tr>".
											"<tr><td>Taller</td><td>$menu_talleres</td></tr>".
											"</table>";
	$captura_transporte="<table><tr><td>Ruta</td><td><input type='text' name='ruta' id='ruta' value='' size='50' maxlength='100'></td></tr>".
											"</table>";
	$captura_traslado="<table><tr><td>Ruta</td><td><input type='text' name='ruta' id='ruta value='' size='50' maxlength='100'></td></tr>".
										"<tr><td>Kilometraje inicial:</td><td><input type='text' name='km_inicial' id='km_inicial' value='' size='10' maxlength='10'></td></tr>".
										"<tr><td>Kilometraje final:</td><td><input type='text' name='km_final' id='km_final' value='' size='10' maxlength='10'></td></tr>".
										"</table>";
	$captura_parqueadero="<table><tr><td>Numero de horas:</td><td><input type='text' name='horas_parqueadero' id='horas_parqueadero' value='' size='50' maxlength='100'></td></tr>".
											"</table>";
	$captura_mantenimiento_preventivo="<table><tr><td>Taller</td><td>$menu_talleres</td></tr>".
											"<tr><td>Kilometraje inicial:</td><td><input type='text' name='km_inicial' id='km_inicial' value='' size='10' maxlength='10'></td></tr>".
											"</table>";
	echo "<script language='javascript'>
			function activa_tipo(dato)
			{
				var D=document.getElementById('detalle');
				switch(dato)
				{
					case '1':D.innerHTML=\"$captura_domicilio\";document.forma.continuar.value=' GRABAR ';break;
					case '2':D.innerHTML=\"$captura_lavado\";document.forma.continuar.value=' GRABAR ';break;
					case '3':D.innerHTML=\"$captura_combustiblen\";document.forma.continuar.value=' GRABAR ';break;
					case '4':D.innerHTML=\"$captura_combustiblet\";document.forma.continuar.value=' GRABAR ';break;
					case '5':D.innerHTML=\"$captura_combustiblen\";document.forma.continuar.value=' GRABAR ';break;
					case '6':D.innerHTML=\"$captura_transporte\";document.forma.continuar.value=' GRABAR ';break;
					case '7':D.innerHTML=\"$captura_traslado\";document.forma.continuar.value=' GRABAR ';break;
					case '8':D.innerHTML=\"$captura_parqueadero\";document.forma.continuar.value=' GRABAR ';break;
					case '9':D.innerHTML=\"$captura_mantenimiento_preventivo\";document.forma.continuar.value=' GRABAR ';break;
					case '10':D.innerHTML=\"$captura_mantenimiento_preventivo\";document.forma.continuar.value=' GRABAR ';break;
					case '11':D.innerHTML=\"$captura_mantenimiento_preventivo\";document.forma.continuar.value=' GRABAR ';break;
				}
			}
			function arma_hora(dato)
			{	document.getElementById('hora_'+dato).value=document.getElementById('hora'+dato).value+':'+document.getElementById('minuto'+dato).value	}
			
			function valida_formulario()
			{
				with(document.forma)
				{
					if(!operario.value) {alert('Debe seleccionar el operario');operario.style.backgroundColor='ffff44';return false;}
					if(!alltrim(concepto.value)) {alert('Debe escribir el concepto del control');concepto.style.backgroundColor='ffff44';return false;}
					switch(tipo.value)
					{
						case '1':if(!tipo_domicilio.value) {alert('Debe seleccionar el tipo de domicilio');tipo_domicilio.style.backgroundColor='ffff44';return false;}
									if(!alltrim(ruta.value)) {alert('Debe digitar la ruta');ruta.style.backgroundColor='ffff44';return false;}
									if(!hora_1.value) {alert('Debe indicar la hora inicial');hora1.style.backgroundColor='ffff44';minuto1.style.backgroundColor='ffff44';return false;}
									if(!hora_2.value) {alert('Debe indicar la hora de Entrega/Devolucion');hora2.style.backgroundColor='ffff44';minuto2.style.backgroundColor='ffff44';return false;}
									if(!hora_3.value) {alert('Debe indicar la hora de Llegada');hora3.style.backgroundColor='ffff44';minuto3.style.backgroundColor='ffff44';return false;}
									if(!Number(km_inicial.value)) {alert('Debe digitar el kilometraje inicial sin coma ni puntos');km_inicial.style.backgroundColor='ffff44';return false;}
									if(!Number(km_final.value)) {alert('Debe digitar el kilometraje final sin coma ni puntos');km_final.style.backgroundColor='ffff44';return false;}
									break;
						case '2':if(!tipo_lavado.value) {alert('Debe seleccionar el tipo de lavado');tipo_lavado.style.backgroundColor='ffff44';return false;} 
									if(!Number(km_inicial.value)) {alert('Debe digitar el kilometraje inicial sin coma ni puntos');km_inicial.style.backgroundColor='ffff44';return false;}
									if(!Number(km_final.value)) {alert('Debe digitar el kilometraje final sin coma ni puntos');km_final.style.backgroundColor='ffff44';return false;}
									break;
						case '3':if(!Number(galones.value)) {alert('Debe digitar los galones sin coma ni puntos');galones.style.backgroundColor='ffff44';return false;}
									if(!forma_pago.value) {alert('Debe seleccionar la forma de pago');forma_pago.style.backgroundColor='ffff44';return false;} 
									break;
						case '4':if(!Number(galones.value)) {alert('Debe digitar los galones sin coma ni puntos');galones.style.backgroundColor='ffff44';return false;}
									if(!forma_pago.value) {alert('Debe seleccionar la forma de pago');forma_pago.style.backgroundColor='ffff44';return false;} 
									if(!taller.value) {alert('Debe seleccionar el taller');taller.style.backgroundColor='ffff44';return false;} 
									break;
						case '5':if(!Number(galones.value)) {alert('Debe digitar los galones sin coma ni puntos');galones.style.backgroundColor='ffff44';return false;}
									if(!forma_pago.value) {alert('Debe seleccionar la forma de pago');forma_pago.style.backgroundColor='ffff44';return false;} 
									break;
						case '6':if(!alltrim(ruta.value)) {alert('Debe digitar la ruta');ruta.style.backgroundColor='ffff44';return false;}
									break;
						case '7':if(!alltrim(ruta.value)) {alert('Debe digitar la ruta');ruta.style.backgroundColor='ffff44';return false;}
									if(!Number(km_inicial.value)) {alert('Debe digitar el kilometraje inicial sin coma ni puntos');km_inicial.style.backgroundColor='ffff44';return false;}
									if(!Number(km_final.value)) {alert('Debe digitar el kilometraje final sin coma ni puntos');km_final.style.backgroundColor='ffff44';return false;}
									break;
						case '8':if(!Number(horas_parqueadero.value)) {alert('Debe digitar las horas de parqueadero sin coma ni puntos');horas_parqueadero.style.backgroundColor='ffff44';return false;}
									break;
						case '9':if(!Number(km_inicial.value)) {alert('Debe digitar el kilometraje inicial sin coma ni puntos');km_inicial.style.backgroundColor='ffff44';return false;}
									if(!taller.value) {alert('Debe seleccionar el taller');taller.style.backgroundColor='ffff44';return false;} 
									break;
						case '10':if(!Number(km_inicial.value)) {alert('Debe digitar el kilometraje inicial sin coma ni puntos');km_inicial.style.backgroundColor='ffff44';return false;}
									if(!taller.value) {alert('Debe seleccionar el taller');taller.style.backgroundColor='ffff44';return false;} 
									break;
						case '11':if(!Number(km_inicial.value)) {alert('Debe digitar el kilometraje inicial sin coma ni puntos');km_inicial.style.backgroundColor='ffff44';return false;}
									if(!taller.value) {alert('Debe seleccionar el taller');taller.style.backgroundColor='ffff44';return false;} 
									break;
					}
					submit();
				}
			}
			
			function cerrar()
			{
				window.close();void(null);
				opener.location.reload();
			}
			
			function mas_talleres()
			{
				modal('zbalance_estado.php?Acc=mas_talleres',0,0,500,500,'mast');
			}
		</script><body>
		<form action='zbalance_estado.php' target='Oculto_ctrl' method='POST' name='forma' id='forma'>
		<table>
			<tr><td>Tipo de Control:</td><td>".menu1("tipo","Select id,nombre from tipo_ctrl_operacion order by id",0,1,'',"onchange='activa_tipo(this.value);' ")."</td></tr>
			<tr><td>Fecha:</td><td>".pinta_FC('forma','fecha',date('Y-m-d'))."</td></tr>
			<tr><td>Operario:</td><td>".menu1("operario","Select id,t_operario(id) as noper from operario order by noper",0,1)."</td></tr>
			<tr><td>Concepto:</td><td><input type='text' name='concepto' id='concepto' value='' size='50' maxlength='250'></td></tr>
		</table>
		<span id='detalle'></span>
		<input type='button' name='continuar' id='continuar' value=' DEBE SELECCIONAR EL TIPO DE CONTROL ' onclick='valida_formulario();'>
		<input type='hidden' name='Acc' value='adicionar_control_operaciones_ok'>
		<input type='hidden' name='id' value='$id'>
		<br>";
	echo "</form>
	<iframe name='Oculto_ctrl' id='Oculto_ctrl' style='visibility:hidden' width='1' height='1'></iframe>
	</body>";
}

function pinta_chora($dato) // pinta opciones de horas 
{
	return "<input type='hidden' name='hora_$dato' id='hora_$dato' value=''><select name='hora$dato' id='hora$dato' onchange='arma_hora($dato);'>".
			"<option value=''></option><option value='04'>04</option><option value='04'>04</option><option value='06'>06</option>".
			"<option value='07'>07</option><option value='08'>08</option><option value='09'>09</option><option value='10'>10</option>".
			"<option value='11'>11</option><option value='12'>12</option><option value='13'>13</option><option value='14'>14</option>".
			"<option value='15'>15</option><option value='16'>16</option><option value='17'>17</option><option value='18'>18</option>".
			"<option value='19'>19</option><option value='20'>20</option></select>:".
			"<select name='minuto$dato' id='minuto$dato' onchange='arma_hora($dato);'>".
			"<option value=''></option><option value='00'>00</option><option value='05'>05</option>".
			"<option value='10'>10</option><option value='15'>15</option><option value='20'>20</option><option value='25'>25</option>".
			"<option value='30'>30</option><option value='35'>35</option><option value='40'>40</option><option value='45'>45</option>".
			"<option value='50'>50</option><option value='55'>55</option></select>"; 
}

function adicionar_control_operaciones_ok() // inserta el registro de control de operaciones en la tabla
{
	global $id,$tipo,$fecha,$operario,$concepto,$tipo_domicilio,$ruta,$hora_1,$hora_2,
	$hora_3,$tipo_lavado,$forma_pago,$galones,$taller,$km_inicial,$km_final,$horas_parqueadero;
	if(q("insert into control_operacion (ubicacion,tipo,fecha,operario,concepto,tipo_domicilio,
	ruta_domicilio,hora_inicial,hora_final,hora_evento,tipo_lavado,galones,forma_pago,taller,
	km_inicial,km_final,horas_parqueadero) values ('$id','$tipo','$fecha','$operario',\"$concepto\",
	'$tipo_domicilio','$ruta','$hora_1','$hora_3','$hora_2','$tipo_lavado','$galones',
	'$forma_pago','$taller','$km_inicial','$km_final','$horas_parqueadero')"))
	echo "<body><script language='javascript'>alert('Informacion grabada satisfactoriamente');parent.cerrar();</script></body>";
}

function pinta_control($C) // de acuerdo al tipo de control muestra unas etiquetas
{
	$Resultado='';
	switch($C->tipo)
	{
		case '1':  $Tipo=($C->tipo_domicilio=='E'?'Entrega':'Devolucion');
						$Hora1=l($C->hora_inicial,5);
						$Hora2=l($C->hora_evento,5);
						$Hora3=l($C->hora_final,5);
						$Resultado= "Tipo Domicilio: <b>$Tipo</b>  Ruta: <b>$C->ruta_domicilio</b> Hora inicial: <b>$Hora1</b> Hora Entrega/Devolucion: 
							<b>$Hora2</b> Hora final: <b>$Hora3</b> Km inicial: <b>".coma_format($C->km_inicial)."</b> Km final: <b>".coma_format($C->km_final)."</b>";
						break;
		case '2': $Resultado="Tipo Lavado: <b>$C->nlavado</b> ";
						break;
		case '3': $Resultado="No. Galones: <b>$C->galones</b> Forma de Pago: <b>$C->nfpago</b>";
						break;
		case '4': $Resultado="No. Galones: <b>$C->galones</b> Forma de Pago: <b>$C->nfpago</b> Taller: <b>$C->ntaller</b>";
						break;
		case '5': $Resultado="No. Galones: <b>$C->galones</b> Forma de Pago: <b>$C->nfpago</b>";
						break;
		case '6': $Resultado="Ruta: <b>$C->ruta_domicilio</b>";
						break;
		case '7': $Resultado="Ruta: <b>$C->ruta_domicilio</b> Km inicial: <b>".coma_format($C->km_inicial)."</b> Km final: <b>".coma_format($C->km_final)."</b>";
						break;
		case '8': $Resultado="Numero Horas: <b>$C->horas_parqueadero</b>";
						break;
		case '9': $Resultado="Taller: <b>$C->ntaller</b> Km inicial: <b>".coma_format($C->km_inicial)."</b>";
						break;
		case '10': $Resultado="Taller: <b>$C->ntaller</b> Km inicial: <b>".coma_format($C->km_inicial)."</b>";
						break;
		case '11': $Resultado="Taller: <b>$C->ntaller</b> Km inicial: <b>".coma_format($C->km_inicial)."</b>";
						break;
	}
	return $Resultado;
}

function mas_talleres() // permite crear talleres. 
{
	//	funcion para crear mas talleres en la base aoacars
	html('TALLERES');
	echo "<script language='javascript'>
		function busqueda_ciudad2(Campo,Contenido)
		{
			var Ventana_ciudad=document.getElementById('Busqueda_Ciudad');
			Ventana_ciudad.style.visibility='visible';Ventana_ciudad.style.left=mouseX;Ventana_ciudad.style.top=mouseY-10;Ventana_ciudad.src='inc/ciudades.html';
			Ciudad_campo=Campo;Ciudad_forma='forma';
		}
		function oculta_busca_ciudad()
		{document.getElementById('Busqueda_Ciudad').style.visibility='hidden';}
		function valida_nuevot()
		{
			with(document.forma)
			{
				if(!alltrim(nombre.value)) {alert('Debe escribir un nombre'); nombre.style.backgroundColor='ffffaa';nombre.focus();return false;}
				if(!alltrim(ciudad.value)) {alert('Debe seleccionar una ciudad'); _ciudad.style.backgroundColor='ffffaa';_ciudad.focus();return false;}
				if(confirm('Desea crear el nuevo taller?')) {submit();}
			}
		}
	</script><body><h3>TALLERES</h3>
	<iframe id='Busqueda_Ciudad' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#eeffee;z-index:200;' height='400px' width='200px' ></iframe>";
	if($Talleres=q("select *,t_ciudad(ciudad) as nciudad from taller order by nciudad,nombre")) // muestra las ciudades
	{
		echo "<table border cellspacing='0'><tr>
			<th>id</th>
			<th>Ciudad</th>
			<th>Nombre</th>
			</tr>";
		$Contador=0;
		while($T =mysql_fetch_object($Talleres ))
		{
			$Contador++;
			echo "<tr>
			<td align='center'>$Contador</td>
			<td>$T->nciudad</td>
			<td>$T->nombre</td>
			</tr>";
		}
		echo "</table>";
	}
	echo "<br>
	<form action='zbalance_estado.php' target='_self' method='POST' name='forma' id='forma'>
		<h4>Creaci�n de nuevo Taller</h4>
		Ciudad: <input type='text' style='color:#000099;background-color:#FFFFFF;' name='_ciudad' id='_ciudad' size='30' onclick=\"busqueda_ciudad2('ciudad','05001000');\" readonly>
		<input type='hidden' name=ciudad id=ciudad value=''><span id='bc_ciudad'></span>
		<br>Nombre <input type='text' name='nombre' id='nombre' value='' size='50' maxlength='100' onblur='this.value=this.value.toUpperCase();' ><br>
		<input type='button' name='Continuar' id='Continuar' value=' CREAR NUEVO TALLER' onclick='valida_nuevot();'>
		<input type='hidden' name='Acc' value='crear_nuevo_taller'>
	</form>
	</body>";
}

function crear_nuevo_taller() // inserta nuevo taller en la tabla de talleres
{
	global $ciudad,$nombre;
	q("insert into taller (ciudad,nombre) values ('$ciudad','$nombre')");
	header('location:zbalance_estado.php?Acc=mas_talleres');
}

function aprobacion_requisicion() // aprobacion de la requisicion 
{
	global $id,$BDA;
	echo "soy id ".$id;
	html('APROBACION REQUISICION');
	$D=qo("select * from $BDA.requisicion where id=$id"); // trae los datos de la requiisicion
	//return print_r($D);
	if($D->estado==2) {$Mensaje=urlencode(base64_encode("El estado de esta requisici�n ya fue procesado y es: Aprobado." ));
	header("location:zbalance_estado.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();} // no deja aprobar sino una vez la requisicion
	if($D->estado==3) {$Mensaje=urlencode(base64_encode("El estado de esta requisici�n ya fue procesado y es: Rechazado." ));
	header("location:zbalance_estado.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();} // si el estado es rechazado sale el aviso
	if($D->estado==4) {$Mensaje=urlencode(base64_encode("El estado de esta requisici�n ya fue procesado y es: Calificado." ));
	header("location:zbalance_estado.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();} // si el estado es Calificado sale el aviso
	$Ciu=qo1("select t_ciudad('$D->ciudad')"); // trae los datos de la ciudad
	$Pr=qo("select * from $BDA.perfil_requisicion where id=$D->perfil"); // trae los datos del perfil de aprobacion de la requisicion
	$Email_usuario=usuario('email'); // obtiene el correo electronico del usuario
	if(!$Email_usuario) {
	echo "<body><script language='javascript'>alert('SU SESION EN ESTE SISTEMA ESTA CAIDA, NO SE PUEDE CONTINUAR');</script></body>"; die();}
	$Hoy=date('Y-m-d H:i:s');	
	// si hay una contingencia se usan los funcionarios que aprueban requisiciones o si no se usan los funcionarios originales
	if($Pr->contingencia) {$Email_aprobador=$Pr->email_aprobacion2;$Nombre_aprobador=$Pr->aprobado_por2;}
	else {$Email_aprobador=$Pr->email_aprobacion;$Nombre_aprobador=$Pr->aprobado_por;}
	// arma una ruta de correo para la aprobacion
	$Ruta_correo="utilidades/Operativo/operativo.php?id=$id&Fecha=$Hoy&Usuario=$Nombre_aprobador&eUsuario=$Email_aprobador&Solicitado_por=".$_SESSION['Nombre']."&eSolicitado_por=$Email_usuario";
	$Cotizaciones='';
	// si hay imagenes de cotizaciones las incluye en el correo
	if($D->cotizacion_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 1 </u></a><br>";
	if($D->cotizacion2_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion2_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 2 </u></a><br>";
	if($D->cotizacion3_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion3_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 3 </u></a><br>";
	if(!$Cotizaciones) $Cotizaciones="No hay imagenes cargadas";
	// calcula una ruta de aprobacion
	$Ruta_aprobacion=base64_encode("\$Programa='$Ruta_correo&Acc=aprobar_requisicion&observaciones='.\$observaciones.'&cotapr='.\$cotapr;\$Fecha_control=date('Y-m-d');"); 
	// calcula una ruta de no aprobaci�n o rechazo
	$Ruta_daprobacion=base64_encode("\$Programa='$Ruta_correo&Acc=daprobar_requisicion&observaciones='.\$observaciones;\$Fecha_control=date('Y-m-d');"); 
	$Fecha_control=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),30)));
	// pinta los datos de la requisicion
	$Det="<table border cellspacing='0'><tr><th>Clase</th><th>Tipo de Requisicion</th><th>Descripcion</th><th>Valor</th>";
	$Detalle=q("select *,$BDA.t_requisiciont(tipo) as ntipo, $BDA.t_requisicionc(clase) as nclase from $BDA.requisiciond where requisicion=$id");
	while($Dt =mysql_fetch_object($Detalle))
	{
		$Det.="<tr><td>$Dt->nclase</td><td>$Dt->ntipo</td><td>$Dt->observaciones</td><td align='right'>".coma_format($Dt->valor)."</td></tr>";
	}
	$Det.="</table>";
	echo " <body><b>Solicitud de aprobaci�n Requisici�n N�mero $id</b><br>
				<table><tr><td>Fecha de Requisici�n:</td><td><b>$D->fecha</b></td></tr>
				 <tr><td>Solicitado por: </td><td><b>$D->solicitado_por</b></td></tr>
				<tr><td>Placa: </td><td><b>$D->placa</b></td></tr>
				<tr><td>Ciudad: </td><td><b>$Ciu</b></td></tr>
				<tr><td>Cotizaciones: </td><td>$Cotizaciones</td></tr></table>
				<br>Detalle de la requisicion:<br>$Det<br>
				<br><a href='http://app.aoacolombia.com/Control/operativo/zbalance_estado.php?Acc=ver_balance&id=$D->ubicacion' target='_blank'>Click aqui para ver el Balance de Estado</a>
				<br>
				<form action='http://app.aoacolombia.com/i.php' target='_blank' method='GET' name='forma' id='forma'>
					<select name='i'><option value=\"$Ruta_aprobacion\">Aprobar</option><option value=\"$Ruta_daprobacion\">Rechazar</option></select><br>
						No. Cotizaci�n Aprobada: <select name='cotapr'><option value=''></option><option value='1'>1</option><option value='2'>2</option><option value='2'>2</option></select><br>
						<br>Observaciones: <input type='text' name='observa_aprobacion' id='observa_aprobacion' value='' size='50' maxlength='200'><br>
						<br><input type='submit' value=' PROCEDER ' >
						<input type='hidden' name='Fecha_control' value='$Fecha_control'>
				</form>
				<br><br></body>
	";
}

function mensaje_operativo_alerta(){global $Mensaje;html('AUTORIZACION');echo "<body>".base64_decode($Mensaje)." <script language='javascript'>alert('".base64_decode($Mensaje)."');</script></body>";}

function evaluar_requisicion() // formulario para evaluar una requisicion de un proveedor
{
        global $id,$BDA;
        $Req=qo("select * from $BDA.requisicion where id=$id"); // obtiene los datos de la requisicion
        $Proveedor=qo("select * from $BDA.proveedor where id=$Req->proveedor"); // obtiene los datos del proveedor
        $Criterios_evaluacion=q("select * from $BDA.prov_criterio_eval order by id"); // carga los criterios de evaluacion
        html('EVALUAR REQUISICION $id');
        echo "<script language='javascript'>
                A_criterio=new Array();
                Calificaciones=new Array();
                function cambio_opcion(criterio,opcion)
                {
                        document.getElementById('cal_'+criterio).value=A_criterio[criterio]['opciones'][opcion];
                        Calificaciones[criterio]['opcion']=opcion;
                        Calificaciones[criterio]['calificacion']=A_criterio[criterio]['opciones'][opcion];
                }
                function valida_envio()
                {
                        with(document.forma)
                        {
                                calificaciones.value='';
                                for(indice in Calificaciones)
                                {
                                        calificaciones.value+=indice+'|'+Calificaciones[indice]['opcion']+'|'+Calificaciones[indice]['calificacion']+',';
                                }
                                submit();
                        }

                }
        </script><body>
        <h3>EVALUACION DE REQUISICION</H3>
        <h4>Proveedor: $Proveedor->nombre</h4>
        <form action='zbalance_estado.php' target='_self' method='POST' name='forma' id='forma'>
                <table><tr><th>Criterio</th><th>Opci�n</th><th>Resultado</th></tr>";
                include('inc/link.php');
                $Js='';
                while($Cev=mysql_fetch_object($Criterios_evaluacion)) // pinta los criterios de evaluacion con sus opciones de calificaicon
                {
                        $Js.="
                        A_criterio[$Cev->id]=new Array();A_criterio[$Cev->id]['nombre']='$Cev->nombre';
                        Calificaciones[$Cev->id]=new Array();";
                        echo "<tr><td>$Cev->nombre</td><td>";
                        $Opciones_criterio=mysql_query("select id,nombre,calificacion from $BDA.prov_rangos_eval where criterio=$Cev->id"); // carga las opciones de acuerdo a cada criterio
                        if(mysql_num_rows($Opciones_criterio))
                        {
                                echo "<select name='opcion_calificacion' style='width:200px' onchange='cambio_opcion($Cev->id,this.value);'><option value=''></option>";
                                $Js.="
                                A_criterio[$Cev->id]['opciones']=new Array();   ";
                                while($Op=mysql_fetch_object($Opciones_criterio)) // arma el arreglo de opciones de calificacion para cada criterio de efaluacion
                                {
                                        echo "<option value='$Op->id'>$Op->nombre</option>";
                                        $Js.="
                                        A_criterio[$Cev->id]['opciones'][$Op->id]=$Op->calificacion; ";
                                }
                                echo "</select>";
                        }
                        else
                        {
                                echo "<b style='color:red'>No tiene opciones</b>";
                        }
                        echo "</td><td align='center'><input type='text' name='cal_$Cev->id' id='cal_$Cev->id' value='' class='numero' size='2' maxlength='3' readonly></td></tr>";
                }
                echo "<script language='javascript'>$Js</script>
                </table>
                <br><center><input type='button' name='continuar' id='continuar' value=' GRABAR LA EVALUACION ' style='font-size:18px;font-weight:bold;height:40px;width:400px'
                        onclick='valida_envio();'></center>
                        <input type='hidden' name='Acc' value='evaluar_requisicion_ok'>
                        <input type='hidden' name='id' value='$id'>
                        <input type='hidden' name='calificaciones' value=''>
                        ";
                mysql_close($LINK);
        echo "</form></body>";
}

function evaluar_requisicion_ok() // guarda la evaluaci�n hecha por el funcionario
{
        global $id,$BDA; // id de la requisicion
        global $calificaciones,$NUSUARIO,$NICK;
		$Ahora=date('Y-m-d H:i:s');
        //html();
        //echo "Calificaciones: $calificaciones ";
        $Criterios=explode(',',$calificaciones);
		$Total_calificacion=0;
        foreach($Criterios as $Criterio) // para todos los criterios evaluados, los inserta en la tabla de evaluaciones.
        {
                if(strlen($Criterio))
                {
                        $Partes=explode('|',$Criterio);
                        $criterio=$Partes[0];$opcion=$Partes[1];$calificacion=$Partes[2];
                        //echo "<br>Criterio: $criterio opcion: $opcion calificacion: $calificacion";
                        q("insert ignore into $BDA.prov_detalle_evaluacion (requisicion,criterio) values ('$id','$criterio')");
                        q("update $BDA.prov_detalle_evaluacion set opcion='$opcion',calificacion='$calificacion' where requisicion='$id' and criterio='$criterio' ");
						$Total_calificacion+=$calificacion;
                }
        }
		
        q("update $BDA.requisicion set estado=4,evaluada_por='$NUSUARIO',fecha_evaluacion='$Ahora',calificacion='$Total_calificacion' where id=$id "); // actualiza la requisicion
        graba_bitacora('requisicion','M',$id,'Evalua requisici�n'); // graba la bitacora de requisicion
		q("insert into $BDA.app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."','".date('s')."','$NICK','$NUSUARIO','requisicion','M','$id','".$_SERVER['REMOTE_ADDR']."','Evalua requisici�n.')"); // graba la bitacora de requisicion
        echo "<body><script language='javascript'>alert('Evaluaci�n guardada satisfactoriamente.');window.close();void(null);opener.recargar();</script></body>";
}

function adicionar_observaciones()
{
	include('inc/gpos.php');
	sesion();
	html();
	echo "<title>ADICION DE OBSERVACIONES</title>
			<script language='javascript'>
				function cerrar(){window.close();void(null);opener.recargar();}
			</script>
		</head>
		<body><script language='javascript'>centrar(600,400);</script>
			<form action='zbalance_estado.php' target='Oculto_obsreq' method='POST' name='forma' id='forma'>
				Observaciones:<br>
				<textarea name='obs' id='obs' style='width:100%;height:70%;' placehoder='Observaciones...'></textarea><br>
				<input type='button' class='button' name='btn_guardar' id='btn_guardar' value='CONTINUAR' onclick=\"valida_campos('forma','obs');\">
				<input type='button' class='button' name='btn_cancelar' id='btn_cancelar' value='CANCELAR' onclick=\"window.close();void(null);\">
				<input type='hidden' name='id' value='$id'>
				<input type='hidden' name='Acc' value='adicionar_observaciones_ok'>
			</form>
			<iframe name='Oculto_obsreq' id='Oculto_obsreq' style='display:none' width='1' height='1'></iframe>
		</body>";
}

function adicionar_observaciones_ok()
{
	global $app,$BDA;
	include('inc/gpos.php');
	sesion();
	$Usuario=$_SESSION['Nick'].'-'.$_SESSION['Nombre'];
	$Ahora=date('Y-m-d H:i:s');
	q("UPDATE $BDA.requisicion SET observaciones=concat(observaciones,\"\n[$Usuario : $Ahora] $obs\") WHERE id=$id ");
	
	q("insert into $BDA.app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."','".date('s')."','$NICK',
			'$NUSUARIO','requisicion','M','$id','".$_SERVER['REMOTE_ADDR']."','Adiciona observaciones')"); // graba la bitacora de requisicion
			
	echo "<body><script language='javascript'>parent.cerrar();</script></body>";
}
?>