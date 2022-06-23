<?php

/*  este programa se dispara desde cualquiera de las otras bases de datos,
 * toma los proveedores de administracion, se corre en aoa
 * datos que recibe:
 * observaciones
 * id de la ubicacion
 * nombre de la base de datos
 */
include('inc/funciones_.php');
sesion();
if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}
html('ORDEN DE SERVICIO');
$Ub=qo("select * from $base.ubicacion where id=$idub");
$Ciudad=qo1("select ciudad.codigo from ciudad,oficina where oficina.id=$Ub->oficina and ciudad.codigo=oficina.ciudad ");
$Vh=qo("select * from $base.vehiculo where id=$Ub->vehiculo");
$Hoy=date('Y-m-d');
echo "<script language='javascript'>
		var Numero_orden=0;
		function imprime_os()
		{
			window.open('zorden_servicio.php?Acc=imprime_os&id='+Numero_orden,'_blank');
		}
		function solicita_aprobacion()
		{
			window.open('zorden_servicio.php?Acc=solicita_aprobacion&id='+Numero_orden,'Oculto_os');
		}
		function valida_os()
		{
			if(!document.forma.fr.value) { alert('Debe especificar si esta solicitud cruzará o no contra una Factura de Reintegro');return;}
			if(!document.forma.Taller.value) {alert('Debe seleccionar un taller');return;}
			if(!document.forma.descripcion.value && !document.forma.descripciong.value) {alert('Debe digitar la descripcion del servicio');return;}
			document.forma.submit();
		}
	</script>
	<body onload='centrar(700,700);'>
	<form action='zorden_servicio.php' method='post' target='Oculto_os' name='forma' id='forma'>
		<table align='center' bgcolor='eeeeee'>
		<tr><td colspan=2>
		<center><img src='../img/LOGO_AOA_200.png' height='70' border=0>&nbsp;&nbsp;
		<font color='blue' style='font-size:16;font-weight:bold'>ORDEN DE SERVICIO No. &nbsp;&nbsp; </font>
		<span id='_nos' style='font-size:16;color:red;font-weight:bold'>-----</span></center><br><br>
		</td></tr>
		<tr>
			<td>Expedida para:</td><td>".menu1('Taller',"select id,concat(nombre,'  [ ',t_ciudad(ciudad),' ]') from aoacol_administra.proveedor where operaciones=1 and ciudad='$Ciudad' order by nombre",0,1)." Taller sugerido.</td>
		</tr>
		<tr>
			<td>Descripción del servicio:</td>
			<td>$descripcion<br><textarea name='descripcion' rows=4 cols=80 style='font-family:arial;font-size:12'  onblur='this.value=this.value.toUpperCase();'></textarea></td></tr>
		<tr>
			<td>Descripción del servicio<br>Por Garantia:</td>
			<td><textarea name='descripciong' rows=4 cols=80 style='font-family:arial;font-size:12' onblur='this.value=this.value.toUpperCase();'></textarea></td></tr>
		<tr>
			<td>Placa del vehículo:</td><td><input type='text' name='placa1' value='$Vh->placa ".nombre_aseguradora($base)."' size='50' readonly></td></tr>
		<tr><td><b>Esta orden será cruzada contra una Factura de reintegro ?</b></td>
				<td><select name='fr' id='fr'><option value=''></option><option value='no'>No</option><option value='si'>Si</option></select></td></tr>
		<tr>
			<td>Kilometraje de salida:</td><td><input type='text' name='odo_salida' value='$Ub->odometro_final' size=5 readonly class='numero'></td></tr>
		<tr>
			<td>Fecha:</td><td><input type='text' name='fecha' value='$Hoy' size=11 readonly></td></tr>
		<tr>
			<td>Usuario quien diligencia esta orden:</td><td><input type='text' name='solicitado_por' size=90 value='".$_SESSION['Nombre'].'-'.$_SESSION['Nick']."' readonly></td></tr>
		<tr><td align='center' colspan='2'>
			<input type='button' name='bt1' id='bt1' value='GENERAR SOLICITUD' style='font-size:14;font-weight:bold;height:40;width:200' onclick='valida_os()'>
			<input type='hidden' name='Acc' value='guardar_orden_servicio'>
			<input type='hidden' name='aseguradora' value='".nombre_aseguradora($base)."'>
			<input type='hidden' name='placa' value='$Vh->placa'>
			<input type='hidden' name='descripcion_inicial' id='descripcion_inicial' value='$descripcion'>
			<input type='hidden' name='idub' value='$idub'>
			<input type='hidden' name='base' value='$base'>
			<input type='button' name='bt2' id='bt2' value='Vista Previa' style='font-size:14;font-weight:bold;height:40;width:1;visibility:hidden;' onclick='imprime_os()'>
		</td></tr></table>
	</form>
	<iframe name='Oculto_os' id='Oculto_os' height=10 width=10 style='visibility:hidden'></iframe>
	</body>";



function guardar_orden_servicio()
{
	global $Taller,$descripcion_inicial,$descripcion,$descripciong,$placa,$odo_salida,$fecha,$solicitado_por,$aseguradora,$base,$idub,$fr;
	if($fr=='si') $fr=1; else $fr=0;
	$IDN=q("insert into aoacol_administra.orden_servicio (fecha,taller,descripcion,descripciong,placa,aseguradora,odometro_ini,solicitado_por,base,idub,fr) values
	('$fecha','$Taller','$descripcion_inicial $descripcion','$descripciong','$placa','$aseguradora','$odo_salida','$solicitado_por','$base','$idub','$fr')");
	$SIDN=str_pad($IDN,4,'0',STR_PAD_LEFT);
	$Aprueba=qo("select * from aoacol_aoacars.usuario_operativo where aprueba_os=1");
	$Contenido="<body><h3>Solicitud de Aprobación de Orden de Servicio</h3>".
	"Orden número: $IDN<br>".
	"Fecha: $fecha  <br>".
	"Vehiculo: $placa - $aseguradora Odometro de salida: $odo_salida<br>".
	"Descripcion: $descripcion<br>".
	"Descripcion por garantia: $descripciong<br>".
	"Solicitado por: $solicitado_por<br>".
	"<br>Por favor ingrese al sistema de Control de Vehiculos de AOA con el perfil de seguridad OPERATIVO y busque la opción: ORDENES DE SERVICIO, para aprobar esta solicitud.<br><br>".
	"Cordialmente,<br><br>".$_SESSION['Nombre'].' - ['.$_SESSION['Nick'].']</body>';
	$Remitente=usuario('email');
	$Envio=enviar_mail2($Remitente,$_SESSION['Nombre'],$Aprueba->email,"Solicitud Aprobacion $IDN",$Contenido,$Remitente);
	echo "<script language='javascript'>
			function carga()
			{
				var P=parent.document.forma;
				P.bt1.style.visibility='hidden';
				parent.document.getElementById('_nos').innerHTML='$SIDN';
				parent.Numero_orden=$IDN;
				alert('La solicitud fue grabada satisfactoriamente');
				".($Envio?"alert('Se envio la solicitud a $Aprueba->email');":"").
				"P.bt2.style.visibility='visible';P.bt2.style.width='150';
				P.bt1.style.width='1';
				P.descripcion.disabled=true;
				P.descripciong.disabled=true;
				P.Taller.disabled=true;
			}
		</script>
		<body onload='carga()'></body>";
}

function imprime_os($Archivo='')
{
	global $id;
	$D=qo("select * from aoacol_administra.orden_servicio where id=$id");
	$Taller=qo("select * from aoacol_administra.proveedor where id=$D->taller");
	$Ciudad=qo1("select concat(nombre,' - ',departamento) from aoacol_administra.ciudad where codigo='$Taller->ciudad'");
	$SID=str_pad($id,4,'0',STR_PAD_LEFT);
	include('inc/pdf/fpdf.php');
	$P=new pdf('P','mm','letter');
	$P->AddFont("c128a","","c128a.php");
	$P->AliasNbPages();
	$P->setTitle("ORDEN DE SERVICIO");
	$P->setAuthor("Arturo Quintero www.aoacolombia.com arturoquintero@aoacolombia.com");
	$P->Numeracion=false;
	$P->SetAutoPageBreak(false);
//	$P->Header_texto='';
//	$P->Header_alineacion='L';
//	$P->Header_alto='8';
	$P->SetTopMargin('5');
//	$P->Header_colores=array(0,0,0,255,255,255,50,50,100); # rgb texto, rgb fondo, rgb borde
//	$P->Header_imagen='img/cnota_entrada.jpg';
///	$P->Header_posicion_imagen=array(20,5,80,14);
	$P->AddPage('P');
	if(!$D->aprobado_por)
	{
		$P->image('../img/no_aprobado.jpg',50,10,140,110);
	}

	$P->Image('../img/LOGO_AOA_200.jpg',35,10,50,20);
	$P->setfont('Arial','B',16);
	$P->SetXY(100,10);$P->SetTextColor(0,0,255);$P->Cell(70,5,'ORDEN DE SERVICIO No.');$P->SetTextColor(255,0,0);$P->Cell(14,5,$SID,0,0,'L');
	$P->setfont('Arial','',10);$P->SetTextColor(0,0,0);
	$P->setxy(120,15);$P->setFont('Arial','b',10);$P->cell(40,5,'FECHA: '.$D->fecha);
	$P->setxy(120,20);$P->SetFont("c128a","",12);	$P->cell(44,11, uccean128('FA'.str_pad($SID,10,'0',STR_PAD_LEFT)), 1, 0, 'C' );
	$P->SetXY(15,35);	$P->setfont('Arial','',10);$P->cell(30,5,"Expedida para:");$P->SetFont('Arial','b',10);$P->cell(80,5,$Taller->nombre);
	$P->setfont('Arial','',10);$P->cell(50,5,$Ciudad);
	$P->setxy(45,40);$P->cell(180,5,$Taller->direccion.'  Tel: '.($Taller->telefono1?$Taller->telefono1:'').' '.($Taller->telefono2?$Taller->telefono2:'').' '.($Taller->telefono3?$Taller->telefono3:'').' '.($Taller->celular?$Taller->celular:''));
	$P->setxy(15,45);$P->cell(30,5,'Placa vehiculo:');$P->setFont('Arial','b',10);$P->cell(20,5,$D->placa);
	$P->setFont('Arial','',10);$P->cell(40,5,'Kilometraje de salida:');$P->setFont('Arial','b',10);$P->cell(20,5,$D->odometro_ini);
	$P->setxy(15,50);$P->setFont('Arial','',10);$P->cell(50,5,'DESCRIPCION :');
	$P->setxy(19,55);$P->setFont('Arial','B',10);$P->MultiCell(180,5,$D->descripcion,1,'J');
	$P->setxy(15,$P->y+5);$P->setFont('Arial','',10);$P->cell(50,5,'DESCRIPCION POR GARANTIA :');
	$P->setxy(19,$P->y+5);$P->setFont('Arial','B',10);$P->MultiCell(180,5,$D->descripciong,1,'J');
	$P->setxy(15,$P->y+10);$P->SetFont('Arial','',10);$P->Cell(30,5,'Solicitado por:');;$P->setFont('Arial','B',10);$P->Cell(100,5,$D->solicitado_por);
	$P->setxy(15,$P->y+5);$P->SetFont('Arial','',10);$P->Cell(30,5,'Aprobado por:');$P->setFont('Arial','B',10);$P->Cell(100,5,$D->aprobado_por);
	$P->setxy(35,$P->y+6);$P->SetFont('Arial','',8);$P->Cell(180,4,'Señor Proveedor: Favor hacer referencia del número de esta Orden de Servicio en su factura.');
	$P->Rect(15,9,190,$P->y-2);
	if($Archivo)
	{
		$P->Output($Archivo);
		$Envio=enviar_mail2($Remitente,$_SESSION['Nombre'],
			'arturo__quintero@hotmail.com',
			'Orden de Servicio',
			"Orden de Servicio No. $id",
			'','',
			"Orden_$id.pdf",$Archivo	);
	}
	else $P->Output($Archivo);
}

function solicita_aprobacion()
{
	global $id;
	$IDN=str_pad($id,4,'0',STR_PAD_LEFT);
	$OS=qo("select * from aoacol_administra.orden_servicio where id=$id");
	$Ub=qo("select * from $OS->base.ubicacion where id=$OS->idub");
	$Ciudad=qo1("select ciudad.codigo from $OS->base.ciudad,$OS->base.oficina where oficina.id=$Ub->oficina and ciudad.codigo=oficina.ciudad ");
	$Vh=qo("select * from $OS->base.vehiculo where id=$Ub->vehiculo");
	$Hoy=date('Y-m-d');
	html('APROBACION DE ORDEN DE SERVICIO');
	echo "<script language='javascript'>
		function carga()
		{
			centrar(800,700);
		}
		function aprobar()
		{";
	if($OS->fr)
		echo "if(!document.forma.FR.value)
				{
					alert('Debe seleccionar una Factura de Reintegro');
					document.getElementById('tfr').style.textDecoration='blink';
					return;
				}";
	echo "document.forma.Acc.value='aprueba_os';
			document.forma.submit();
		}
		function desaprobar()
		{
			document.forma.Acc.value='desaprueba_os';
			document.forma.submit();
		}
		function cerrar()
		{
			window.close();void(null);
		}
		</script>
		<body onload='carga()'>
		<form action='zorden_servicio.php' method='post' target='_self' name='forma' id='forma'>
			<table border cellspacing=0 align='center' bgcolor='eeeeee' style='empty-cells:show;'>
			<tr><td colspan=2>
			<center><img src='../img/LOGO_AOA_200.png' height='70' border=0>&nbsp;&nbsp;
			<font color='blue' style='font-size:16;font-weight:bold'>ORDEN DE SERVICIO No. &nbsp;&nbsp; </font>
			<span id='_nos' style='font-size:16;color:red;font-weight:bold'>$IDN</span></center><br><br>
			</td></tr>
			<tr><td nowrap='yes' valign='top'>Taller sugerido:</td><td>".
			menu1("Taller","select id,concat(nombre,'  [ ',t_ciudad(ciudad),' ]') from aoacol_administra.proveedor where operaciones=1 and ciudad='$Ciudad' order by nombre",$OS->taller);
	echo "<br>Puede cambiar de taller si lo desea. Si el taller no aparece en esta lista, por favor comuniquese con el Departamento Administrativo
			para la creación correcta del nuevo proveedor.<br></td></tr>
			<tr><td>Fecha de la Solicitud:</td><td>$OS->fecha</td></tr>
			<tr><td>Vehiculo:</td><td>$OS->placa - $OS->aseguradora Odometro de salida: $OS->odometro_ini</td></tr>
			<tr><td>Descripción:</td><td>$OS->descripcion<br>
			<textarea name='descripcion' rows=4 cols=100 style='font-family:arial;font-size:12'  onblur='this.value=this.value.toUpperCase();'></textarea>
			</td></tr>
			<tr><td>Descripción por garantía:</td><td>$OS->descripciong<br>
			<textarea name='descripciong' rows=4 cols=100 style='font-family:arial;font-size:12'  onblur='this.value=this.value.toUpperCase();'></textarea></td></tr>";
		if($OS->fr)
			echo "<tr><td colspan=2 bgcolor='ffffbb' align='center'><b>Esta Orden de Servicio requiere una Factura de Reintegro, por favor seleccionela: </b></td></tr>
					<tr><td colspan=2 align='center'><span id='tfr'>FACTURA DE REINTEGRO : </span>".menu1("FR","select id,concat(consecutivo,' ',ncliente) from aoacol_administra.cxc_factura
					order by consecutivo desc",0,1," font-weight:bold;")."</td></tr>";
		echo "<tr><td>Solicitado por:</td><td>$OS->solicitado_por</td></tr>
			<tr><td colspan=2 align='center'>
			<input type='button' name='bt1' id='bt1' value=APROBAR SOLICITUD' style='font-size:14;font-weight:bold;height:40;width:150' onclick='aprobar()'>
			<input type='button' id='bt2' name='bt2' value='DESAPROBAR SOLICITUD' style='font-size:14;font-weight:bold;height:40;width:220' onclick='desaprobar()'>
			<input type='button' id='bt3' name='bt3' value='DECIDIR MAS TARDE'  style='font-size:14;font-weight:bold;height:40;width:200' onclick='cerrar()'>
			<input type='hidden' name='id' id='id' value='$id'>
			</td></tr>
			</table>
			<input type='hidden' name='Acc' id='Acc' value=''>
		</form></body>";
}


function lista_ordenes()
{
	global $Fecha_inicial,$Fecha_final,$Filtro;
	html('Ordenes de Servicio - '.$_SESSION['Nombre']);
	if(!$Fecha_inicial)
	{
		echo "<body>
		<form action='zorden_servicio.php' method='post' target='_self' name='forma' id='forma'>
			Seleccione la fecha inicial: ".pinta_FC('forma','Fecha_inicial',date('Y-m-d'))."<br><br>
			Seleccione la fecha final: ".pinta_FC('forma','Fecha_final',date('Y-m-d'))."<br><br>
			Ver las: <select name='Filtro'><option value='' ".(!$Filtro?"selected":"").">Todas</option><option value='1' ".($Filtro==1?"selected":"").">Aprobadas</option>
			<option value='2' ".($Filtro==2?"selected":"").">Desaprobadas</option><option value='3' ".($Filtro==3?"selected":"").">Pendientes</option></select><br><br>
			<input type='submit' value='CONTINUAR'>
			<input type='hidden' name='Acc' value='lista_ordenes'>
		</form></body>";
		die();
	}
	echo "
			<script language='javascript'>
				function ver_os(id)
				{
					modal('zorden_servicio.php?Acc=imprime_os&id='+id,50,50,700,1000,'veros');
				}

				function aprobar_os(id)
				{
					if(confirm('Desea aprobar la Orden de Servicio Número '+id+' ?'))
					{
						modal('zorden_servicio.php?Acc=solicita_aprobacion&id='+id,50,50,10,10,'aprueba');
					}
				}

				function email_os(id)
				{
					if(Confirm('Desea enviar la Orden de Servicio Número '+id+' por correo electrónico?'))
					{
						modal('zorden_servicio.php?Acc=enviarmail_os&id='+id,10,10,500,500,'enviarmail');
					}
				}
			</script>
			<body bgcolor='fffffa'><h2>ORDENES DE SERVICIOS</H2>
			<form action='zorden_servicio.php' method='post' target='_self' name='forma' id='forma'>
			Desde: ".pinta_FC('forma','Fecha_inicial',$Fecha_inicial)." Hasta : ".pinta_FC('forma','Fecha_final',$Fecha_final)."
			Ver las: <select name='Filtro'><option value='' ".(!$Filtro?"selected":"").">Todas</option><option value='1' ".($Filtro==1?"selected":"").">Aprobadas</option>
			<option value='2' ".($Filtro==2?"selected":"").">Desaprobadas</option><option value='3' ".($Filtro==3?"selected":"").">Pendientes</option></select>
			<input type='submit' value='CONTINUAR'>
			<input type='hidden' name='Acc' value='lista_ordenes'></form>";
	if($Filtro)
	{
		switch($Filtro)
		{
				case 1:	$filtro=" and aprobado_por!='' and fec_aprobacion!='0000-00-00 00:00:00' and desaprobada=0 ";	break;
				case 2:	$filtro=" and desaprobada=1 ";	break;
				case 3:	$filtro=" and desaprobada=0 and aprobado_por='' and fec_aprobacion='0000-00-00 00:00:00' ";	break;
		}
	}
	else
		$filtro='';
	IF($Ordenes=q("select ser.id,ser.fecha,ser.taller,prov.nombre as nprov,ciu.nombre as nciu,ser.placa,ser.aseguradora,
					ser.odometro_ini,ser.solicitado_por,ser.aprobado_por,ser.fec_aprobacion,ser.desaprobada,ser.fr,
					aoacol_administra.t_cxc_factura(ser.factura_reintegro) as factura_reintegro
					 from aoacol_administra.orden_servicio ser,aoacol_administra.proveedor prov, aoacol_administra.ciudad ciu
					 where prov.id=ser.taller and prov.ciudad=ciu.codigo and ser.fecha between '$Fecha_inicial' and '$Fecha_final' $filtro
					 order by fecha desc,id desc"))
	{
		echo "<table border cellspacing=0 align='center'><tr>
					<th>Numero de Orden</th>
					<th>Fecha</th>
					<th>Proveedor</th>
					<th>Ciudad</th>
					<th>Vehiculo</th>
					<th>Kilometraje</th>
					<th>Fac.Reintegro</th>
					<th>Solicitado Por</th>
					<th>Opciones</th>
					</tr>";
		while($OS=mysql_fetch_object($Ordenes))
		{
			$Nos=str_pad($OS->id,4,'0',STR_PAD_LEFT);
			echo "<tr><td align='center'>$Nos</td>
					<td align='center'>$OS->fecha</td>
					<td align='left'>$OS->nprov</td>
					<td align='left'>$OS->nciu</td>
					<td align='left'>$OS->placa - $OS->aseguradora</td>
					<td align='right'>".coma_format($OS->odometro_ini)."</td>
					<td align='center'>".($OS->fr?($OS->factura_reintegro?$OS->factura_reintegro:"<font color='red'>Falta</font>"):'--')."</td>
					<td align='left'>$OS->solicitado_por</td>
					<td align='left'><a href='javascript:ver_os($OS->id)' class='info'><img src='gifs/pdf.jpg' border=0>
						<span style='width:200px'>Ver el documento en formato PDF</span></a>&nbsp;&nbsp;";
			if($OS->aprobado_por && $OS->fec_aprobacion!='00000-00-00 00:00:00')
				echo "<a class='info'><img src='gifs/standar/si.png' border=0><span>APROBADA</span></a>&nbsp;&nbsp;
					<a href='javascript:email_os($OS->id)' class='info'><img src='gifs/send_mail.png' border=0>
					<span style='width:200px'>Enviar Solicitud via Email @</span></a>&nbsp;&nbsp;";
			elseif($OS->desaprobada)
				echo  "<a class='info'><img src='gifs/standar/Cancel.png' border=0><span>DESAPROBADA</span></a>&nbsp;&nbsp;";
			else
				echo "<a href='javascript:aprobar_os($OS->id)' class='info'><img src='gifs/standar/seguir.png' border=0>
						<span style='width:200px'>Aprobar la Orden de Servicio No. $Nos</span></a>&nbsp;&nbsp;";
			echo "</td>
					</tr>";
		}
		echo "</table>";
	}
	else
	{
		echo "<br><br>No hay ordenes de servicios.";
	}
	echo "</body>";
}

function aprueba_os()
{
	global $id,$descripcion,$descripciong,$Taller,$FR;
	q("update aoacol_administra.orden_servicio set descripcion=concat(descripcion,' $descripcion'),descripciong=concat(descripciong,' $descripciong'),
		taller='$Taller',aprobado_por='".$_SESSION['Nombre'].'-'.$_SESSION['Nick']."',
		fec_aprobacion='".date('Y-m-d')."' ,factura_reintegro='$FR' where id=$id");
	html();
	echo "<script language='javascript'>
			function carga()
			{
				centrar(10,10);
				alert('La Orden de Servicio Número $id fue aprobada correctamente');
				window.close();
				void(null);
				opener.location.reload();
			}
			</script>
			<body onload='carga()'></body>";
}

function desaprueba_os()
{
	global $id;
	q("update aoacol_administra.orden_servicio set desaprobada=1 where id=$id");
	html();
	echo "<script language='javascript'>
		function carga()
		{
			centrar(10,10);
			alert('La Orden de Servicio Número $id fue desaprobada');
			window.close();
			void(null);
			opener.location.reload();
		}
		</script>
		<body onload='carga()'></body>";
}

function enviarmail_os()
{
	global $id;
	$Remitente=usuario('email');
	imprime_os("planos/os_$id.pdf");
}




function nombre_aseguradora($B)
{
	switch($B)
	{
		case 'aoacol_aoacolombia': return 'COLSEGUROS';
		case 'aoacol_aoacolombia2': return 'ROYAL - BASICO';
		case 'aoacol_aoacolombia3': return 'ROYAL - BMW';
		case 'aoacol_libertyseguros': return 'LIBERTY';
		case 'aoacol_mapfre': return 'MAPFRE';
		case 'aoacol_aoacars': return 'AOA SIN LOGO';
	}
}









?>