<?php
/**
 *  JEFE OPERATIVO AOA
 *
 *		activaci�n de veh�culos de flota AOA
 *
 * @version $Id$
 * @copyright 2010
 **/
 

include('inc/funciones_.php');


if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}
session_start();session_unset();session_destroy();html();
$Hacer=base64_encode('operativo');
echo "<script language='javascript' src='inc/js/aqrenc.js'></script>
		<script type='text/javascript' language='JavaScript'>
		var futuro= new Date();futuro.setSeconds(130);var actualiza = 1000;function faltan()
		{var ahora = new Date();	var faltan = futuro - ahora;
			if (faltan > 0)
			{var segundos = Math.round(faltan/1000);
				document.formulario.reloj.value=  segundos + ' SEGUNDOS PARA INGRESAR' ;
				setTimeout('faltan()',actualiza);
			}	else{document.formulario.reloj.value= '0 segundos' ;
				window.open('operativo.php?Acc=bye','_self');
				return true;}}	</script></head>
		<body onload='faltan()' leftmargin='0' rightmargin='0' bgcolor='#ffffff' topmargin='0' bottommargin='0'>
		<form name='formulario' style='font-family: Corbel; padding: 0'>
		<p align='center'><font face='Corbel'><input type='text' name='reloj' value='' size='55' style='font-size:14px;border-style:solid; border-width:0; padding:0; text-align : center; font-family:Corbel; color:#000000; background-color:#ffffff'>
		</font></p></form> <FORM name='entrada' id='entrada'><TABLE BORDER='0' CELLSPACING=0 ALIGN='center'>
		<TR><TD ALIGN='right'>USUARIO:</TD><TD><INPUT TYPE='text' NAME='IDuser' MAXLENGTH='30' SIZE='20'></TD></TR>	<TR >	<TD ALIGN='right'>
		CONTRASE�A:</TD><TD><INPUT TYPE='password' NAME='password' MAXLENGTH='20' SIZE='20'> </td> </tr> <tr>
		<td align='center' colspan=2> <INPUT TYPE='button' VALUE='Ingresar' onclick=\"var dato1=encripta(document.entrada.IDuser.value,AqrSoftware);
		var dato2=encripta(document.entrada.password.value,AqrSoftware); setCookie('IDU',dato1); setCookie('CLU',dato2); valida_entrada(0, 0);
		document.entrada.password.value='';\"> </td> </TR> </TABLE> 	</FORM>";

function bye()
{html();echo "<body bgcolor='blue' ><h3 style='color:ffffff'>AOA COLOMBIA S.A. SESION FINALIZADA</H3></BODY>";die();}

function operativo(){html();echo "HOLA";}

function activar_sinlogo()
{
	global $Placa,$Fecha,$Usuario;
	if($au=qo("select * from solicitud_faoa where placa='$Placa' and fecha='$Fecha' "))
	{$Mensaje=urlencode(base64_encode("<font color='blue'>El vehiculo de placas <b>$Placa</b> ya fue autorizado en la fecha <b>$Fecha</b> por <b>$au->autorizadopor</b> </font>"));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");}
	else
	{q("insert into solicitud_faoa (placa,fecha,autorizadopor) values ('$Placa','$Fecha','$Usuario')") ;
		$Mensaje=urlencode(base64_encode("<font color='green'>Autorizaci�n satisfactoria del vehiculo <b>$Placa</b> en la fecha <b>$Fecha</b> por <b>$Usuario</b></font>"));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");}
}

function mensaje_operativo(){global $Mensaje;html('AUTORIZACION');echo "<body>".base64_decode($Mensaje)."</body>";}

function modificar_siniestro()
{
	global $idm,$Usuario;
	if($Modificacion=qo("select * from solicitud_modsin where id=$idm"))
	{
		if($Modificacion->aprobado_por)
		{$Mensaje=urlencode(base64_encode("<font color='blue'>Esta Solicitud ya fue procesada por $Modificacion->aprobado_por en la fecha $Modificacion->fec_aprobacion</font>" ));
			header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();}
		$H1=date('Y-m-d');$H2=date('H:i:s');
		if($Modificacion->cambio_estado)
		{q("update siniestro set estado=5,causal=0,observaciones=concat(observaciones,\"\n$Usuario [".date('Y-m-d H:i:s')."] Cambia estado a Pendiente: $justificacion1\") where id=$Modificacion->siniestro ");
			q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($Modificacion->siniestro,'$H1','$H2','$Usuario',\"Cambia estado a Pendiente: $Modificacion->justificacion1\",10)");
			q("update solicitud_modsin set aprobado_por='$Usuario',fec_aprobacion='".date('Y-m-d H:i:s')."' where id=$idm");}
		if($Modificacion->cambio_ciudad)
		{$Nciudad_old=qo1("select t_ciudad(ciudad) from siniestro where id=$Modificacion->siniestro ");
			$Nciudad=qo1("select t_ciudad('$Modificacion->ciudad') ");
			q("update siniestro set ciudad='$Modificacion->ciudad',observaciones=concat(observaciones,\"\n$Usuario [".date('Y-m-d H:i:s')."] Cambia ciudad de $Nciudad_old a $Nciudad: $justificacion2\") where id=$Modificacion->siniestro ");
			q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($Modificacion->siniestro,'$H1','$H2','$Usuario',\"Cambia ciudad de $Nciudad_old a $Nciudad: $Modificacion->justificacion2\",9)");
			q("update solicitud_modsin set aprobado_por='$Usuario',fec_aprobacion='".date('Y-m-d H:i:s')."' where id=$idm");}
	}
	else
	{$Mensaje=urlencode(base64_encode("<font color='red'>ERROR: La modificaci�n no se encuentra registrada.</font>" ));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");}
	$Mensaje=urlencode(base64_encode("<font color='green'>Modificaci�n del siniestro n�mero <b>".qo1("select numero from siniestro where id=$Modificacion->siniestro")."</b> satisfactoria.</font>" ));
	header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");
}

function autorizar_garantia_efectivo() 
{
	global $idg,$Usuario;
	$Hoy=date('Y-m-d H:i:s');$Ahora=date('Y-m-d');
	$D=qo("select * from sin_autor where id=$idg");
	$Observaciones=$D->observaciones;
	if($D->estado=='A')
	{$Mensaje=urlencode(base64_encode("<font color='red'>Esta solicitud ya fue aprobada por $D->funcionario el dia $D->fecha_proceso </font>" ));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();}
	else
	{$Mensaje=urlencode(base64_encode("<form action='operativo.php' method='post' target='_self' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='pre_autorizacion_garantia_efectivo'>
				<input type='hidden' name='idg' value='$idg'>
				<input type='hidden' name='Usuario' value='$Usuario'>
			</form>
			<script language='javascript'>document.forma.submit();</script>
			"));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");
		die();
	}
}

function pre_autorizacion_garantia_efectivo()
{
	global $idg,$Usuario;
	html('AUTORIZACION PARA RECIBIR GARANTIA EN EFECTIVO');
		echo "<script language='javascript'>
				function autorizar(){window.open('operativo.php?Acc=autorizar_garantia_efectivo_ok&idg=$idg&Usuario=$Usuario','_self');}
			</script>
				<body><h3>AUTORIZACION PARA RECIBIR GARANTIA EN EFECTIVO</h3><br /><br />
				Esta Solicitud aun no ha sido aprobada. <br /><br />Click en el siguiente link para aprobarla:<br /><br />
				<input type='button' value='AUTORIZAR' onclick=\"autorizar();\"><br /><br /></body>";
}

function autorizar_garantia_efectivo_ok()
{
	global $idg,$Usuario;
	$Hoy=date('Y-m-d H:i:s');$Ahora=date('Y-m-d');
	$D=qo("select * from sin_autor where id=$idg");
	if($D->estado=='A')
	{$Mensaje=urlencode(base64_encode("<font color='red'>Esta solicitud ya fue aprobada por $D->funcionario el dia $D->fecha_proceso </font>" ));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();}
	q("update sin_autor set funcionario='$Usuario',estado='A', observaciones='Autorizado para recibir garant�a de servicio en efectivo.', num_autorizacion='Efectivo',
	fecha_proceso='$Hoy',procesado_por='$Usuario' where id='$idg' ");
	$Mensaje=urlencode(base64_encode("<font color='green'>Autorizacion satisfactoria. </font>" ));
	header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");
}

function autorizar_cambio_temporal()
{
	global $id,$Usuario,$solicitadopor,$flota;
	$Hoy=date('Y-m-d H:i:s');$Ahora=date('Y-m-d');
	$Actual=qo("select * from ubicacion where id=$id");
	if($Ya=qo1("select id from ubicacion where vehiculo=$Actual->vehiculo and flota=$flota and id>$id"))
	{$Mensaje=urlencode(base64_encode("<font color='red' style='font-size:14px'>Esta Autorizaci�n ya fue realizada con anterioridad</font>"));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();}
	$IDN=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento)
	values ('$Actual->oficina','$Actual->vehiculo','$Actual->estado','$flota','$Actual->fecha_final','$Actual->fecha_final','$Actual->odometro_final','$Actual->odometro_final',
	'0','Parqueadero') ");
	$Mensaje=urlencode(base64_encode("<font color='green' style='font-size:14px'>Autorizaci�n Satisfactoria</font>"));
	header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");
}

function descargar_imagen_garantia()
{	
	global $id;
	header('Content-Type: text/html; charset=utf-8');
	include("/var/www/html/public_html/Control/operativo/views/subviews/clientes/cliente_login.html");	
	
}


function descargar_imagen_garantia_ok()
{
	global $id,$identificacion;
	
	$sql = "select devolucion_f, devolucion2_f, fecha_devolucion  from sin_autor where id= '$id' and identificacion = '$identificacion' ";
	
	//echo $sql;
	
	$Imagen=qo($sql);
	
	//print_r($Imagen);
	
	
	
	if($Imagen == null)
	{
		header('Content-Type: text/html; charset=utf-8');
		$message = utf8_encode("<h4>No hay datos asociados,  Si necesita asistencia con su solicitud comuniquese con nosotros al tel�fono 018000186262 o en Bogot� al tel�fono 8837069</h4>");
		include("/var/www/html/public_html/Control/operativo/views/subviews/clientes/cliente_mensaje.html");	
		exit;
	}
	else{
		$now = time(); // or your date as well
		$_date = strtotime($Imagen->fecha_devolucion);
		$datediff = $now - $_date;
		$days = round($datediff / (60 * 60 * 24));	
		
		//echo $days;
			
		if($days>16 || ($Imagen->devolucion_f == null and $Imagen->devolucion2_f == null))
		{
			header('Content-Type: text/html; charset=utf-8');
			$message = utf8_encode("<h4>Lo sentimos, su informaci�n ya no se encuentra en el sistema. Si necesita asistencia con su solicitud comuniquese con nosotros al tel�fono 018000186262 o en Bogot� al tel�fono 8837069 </h4>");
			include("/var/www/html/public_html/Control/operativo/views/subviews/clientes/cliente_mensaje.html");	
			exit;
		}
	}
	
	if($Imagen->devolucion_f)
	{
		if(!strpos(strtoupper($Imagen->devolucion_f),"PDF")){
			header("Pragma: public");header("Expires: 0");header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=\"imagen.jpg\"");
			header("Content-Description: File Transfert");@readfile("../../Control/operativo/$Imagen->devolucion_f");		
		}else{
			header("Content-type:application/pdf");
			header("Content-Disposition:attachment;filename='downloaded.pdf'");
			readfile("../../Control/operativo/$Imagen->devolucion_f");
		}	
	}
	if($Imagen->devolucion2_f)
	{
		if(!strpos(strtoupper($Imagen->devolucion_f),"PDF")){
			header("Pragma: public");header("Expires: 0");header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=\"imagen.jpg\"");
			header("Content-Description: File Transfert");@readfile("../../Control/operativo/$Imagen->devolucion2_f");
		}else{
			header("Content-type:application/pdf");
			header("Content-Disposition:attachment;filename='downloaded.pdf'");
			readfile("../../Control/operativo/$Imagen->devolucion2_f");
		}
	}
}

function autorizar_visualizacion_garantia()
{
	global $idn,$Usuario;
	$Hoy=date('Y-m-d H:i:s');$Ahora=date('Y-m-d');
	$D=qo("select * from solicitud_dataautor where siniestro =$idn AND autorizado_por=''");

	if($D->autorizado_por)
	{$Mensaje=urlencode(base64_encode("<font color='red'>Esta solicitud ya fue aprobada por $D->autorizado_por el dia $D->fecha_aprobacion </font>" ));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();}
	else
	{
		$Mensaje=urlencode(base64_encode("<form action='operativo.php' method='post' target='_self' name='forma' id='forma'>
				<input type='hidden' name='Acc' value='autorizar_visualizacion_garantia_ok'>
				<input type='hidden' name='idn' value='$D->id'><input type='hidden' name='Usuario' value='$Usuario'></form>
			<script language='javascript'>document.forma.submit();</script>"));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();
	}
}

function autorizar_visualizacion_garantia_ok()
{
	global $idn,$Usuario;$Fecha=date('Y-m-d H:i:s');
     q("update aoacol_aoacars.solicitud_dataautor set autorizado_por='$Usuario',fecha_aprobacion='$Fecha' where id=$idn"); 

	$Mensaje=urlencode(base64_encode("<font color='green'>Autorizacion satisfactoria. </font>" ));
	header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");
	
	}
	
function aprobar_requisicion()
{

	global $id,$user,$email,$Fecha ,$Usuario,$eUsuario,$Solicitado_por,$eSolicitado_por,$observaciones,$observa_aprobacion,$cotapr;
		
	$D=qo("select * from aoacol_administra.requisicion where id=$id");
	
	//return print_r($D);
	if($D->estado==2) {$Mensaje=urlencode(base64_encode("El estado de esta requisici�n ya fue procesado y es: Aprobado." ));
	header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	if($D->estado==3) {$Mensaje=urlencode(base64_encode("El estado de esta requisici�n ya fue procesado y es: Rechazado." ));
	header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	if($D->estado==4) {$Mensaje=urlencode(base64_encode("El estado de esta requisici�n ya fue procesado y es: Calificado." ));
	header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
   
 	//q("update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observaciones=\"$observaciones\",cotapr='$cotapr' where id=$id");
	
	//echo "update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observa_aprobacion='$observa_aprobacion',cotapr='$cotapr' where id=$id";
	
	q("update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observa_aprobacion='$observa_aprobacion',cotapr='$cotapr' where id=$id");
	 
	 q("insert into aoacol_administra.app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."',
			'".date('s')."',' $user','$Solicitado_por','requisicion','M','$id','".$_SERVER['REMOTE_ADDR']."','Cambair el estado de la requisicion a Aprobar desde el correo')");

	 	    $verificar=qo("select ubicacion from aoacol_administra.requisicion where id = $id");
				if($verificar->ubicacion == 0){
					if($D->perfil == 3){
					 include('pdfrequisicionAdmTo.php');	
					}else{
					include('pdfrequisicionAdm.php');
					}
				}else{
					if($D->perfil == 3){
					 include('pdfrequisicionOpeTo.php');	
					}else{
					include('pdfrequisicionOpe.php');
					}
					
				}
				
				
				 $correo = $eUsuario;
				 
				   $pdf->Output('/var/www/html/public_html/Administrativo/pdfrequisiciones/requicion'.$id.'.pdf','F');
               $archivo = "https://app.aoacolombia.com/Administrativo/pdfrequisiciones/requicion$id.pdf";
				$nameArchivo = "RequisicionAprobar.pdf";
				 

				$contenido = 4;
				 echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'>
		  </script>
		  <script>
            $.ajax(
                    {
                          url: 'https://sac.aoacolombia.com/ServiEmail.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							enviarEmail:'true ',
							APIKEYAOAAPP:'yNPlsmOGgZoGmH$129',
							para: '$eSolicitado_por',
							paraNombre: '$Solicitado_por',
						    copia: '$eUsuario',
							nombreCopia: '$Usuario',
							contenido:'$contenido',
							asunto:'CONFIRMACION DE APROBACION DE PEDIDO $id',
							id:'$id',
							usuario:'$user  $email',
							nameArchivo:'$nameArchivo',
							observaciones : '$observaciones',
							archivo:'$archivo'
					    },
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
					
					alert('Email enviado satisfactoriamente a $eSolicitado_por');	
                </script>
				</body>";	
			
       $Proveedor=qo("select * from aoacol_administra.proveedor where id=$D->proveedor");
	   
	$EmailDestino='';
	if($Proveedor->nombre) $Nprov=$Proveedor->nombre;elseif($Proveedor->nombre) $Nprov=$Proveedor->nombre;else $Nprov='PROVEEDOR';
	// busqueda del correo electronico de acuerdo a la sede registrada en la requisici�n
	if($D->sede)
	{
		if($Sede=qo("select * from aoacol_administra.prov_sede where id=$D->sede"))
		{
			if($Sede->email) $EmailDestino=$Sede->email;
		}
	}
	
	$Mensaje=urlencode(base64_encode("Autorizacion satisfactoria Sede: " ));
	// si no hay sede registrada en la requisici�n, se toma el email del registro principal del proveedor
	if(!$EmailDestino)
	{
		if($Proveedor->email) {$EmailDestino="$Proveedor->email";}
	}
	// si hay correo electronico, se envia el mensaje
	
	if($EmailDestino)
	{
		$Det="<table class='table' border cellspacing='0'><tr><th>Tipo de Requisicion</th><th>Item</th><th>Unidad de medida</th><th>Descripcion</th><th>Cantidad</th><th>Valor unitario</th><th>Valor</th>";
	        $Detalle=q("select provee_produc_serv.nombre as item,tipo.nombre as tipo,unidad_de_medida.nombre as unidad_medida,requisiciond.observaciones,requisiciond.cantidad,
                    requisiciond.requisicion,requisiciond.valor_total,requisiciond.cantidad,requisiciond.valor as valor_unitario
					from aoacol_administra.requisiciond
					inner join aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					inner join aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					inner join aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
					where requisicion =$id");
	        while($Dt =mysql_fetch_object($Detalle ))
	        {
		    $Det.="<tr><td>$Dt->tipo</td><td>$Dt->item</td><td>$Dt->unidad_medida</td><td>$Dt->observaciones</td><td>$Dt->cantidad</td><td align='right'>$".coma_format($Dt->valor_unitario)."</td><td align='right'>$".coma_format($Dt->valor_total)."</td></tr>";
	        }
	        $Det.="</table>";
			
			$Res="<table border cellspacing='4'><tr><th>Resultado</th>";
        
		     $retorno=q("select requisiciond.requisicion,requisiciond.valor_total,
		            sum(requisiciond.valor_total) as resultado 
					from aoacol_administra.requisiciond
					where requisicion  =$id");
			while($Dt =mysql_fetch_object($retorno))
			{
			   $Res.="<tr><td>$".coma_format($Dt->resultado)."</td>";
			}
			$Res.="</table>";
			
			$Ciudades=qo("select requisicion.ciudad as campoCity ,ciudad.nombre as ciudad, 
                    ciudad.departamento
					from aoacol_administra.requisiciond
					inner join aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id
                    inner join aoacol_administra.ciudad on requisicion.ciudad = ciudad.codigo
                    where requisiciond.requisicion = $id limit 1");
		
		$ciudad = $Ciudades->ciudad;
		
		   	$departamento = $Ciudades->departamento;
			  
				q("update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observa_aprobacion='$observa_aprobacion',cotapr='$cotapr' where id=$id");
  
			    $correo = $Proveedor->email ;
				
			   $pdf->Output('/var/www/html/public_html/Administrativo/pdfrequisiciones/requicion'.$id.'.pdf','F');
               $archivo = "https://app.aoacolombia.com/Administrativo/pdfrequisiciones/requicion$id.pdf";
			   $nameArchivo = "RequisicionAprobar.pdf";
               $contenido = 5; 

					 echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
		  <script>
		  alert('$Mensaje');
            $.ajax(
                    {
                           url: 'https://sac.aoacolombia.com/ServiEmail.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							enviarEmail:'true ',
							APIKEYAOAAPP:'yNPlsmOGgZoGmH$129',
							enviarEmail:'true ',
							APIKEYAOAAPP:'yNPlsmOGgZoGmH$129',
							para:'$correo',
						    copia:'$eUsuario',
							nombreCopia:'$Solicitado_por',
							contenido:'$contenido',
							asunto:'CONFIRMACION DE APROBACION DE PEDIDO $id',
							id:'$id',
							usuario:'$Usuario',
							nameArchivo:'$nameArchivo',
							archivo:'$archivo',
							ciudad:'$ciudad',
							departamento:'$departamento',
							Nprov:'$Nprov'
							},
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
					
				alert('Email enviado satisfactoriamente a $correo');	
        </script>
				</body>";	
				
					exit();	
	}
	else 
	echo "<b>El proveedor no tiene correo electr�nico definido.</b>";
}


function rol_aprobar_requisicion()
{

	global $id,$user,$email,$Fecha ,$Usuario,$eUsuario,$Solicitado_por,$eSolicitado_por,$observaciones,$observa_aprobacion,$cotapr;
		
	$D=qo("select * from aoacol_administra.requisicion where id=$id");
	
	//return print_r($D);
	
 	//q("update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observaciones=\"$observaciones\",cotapr='$cotapr' where id=$id");
	
	//echo "update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observa_aprobacion='$observa_aprobacion',cotapr='$cotapr' where id=$id";
	
	q("update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observa_aprobacion='$observa_aprobacion',cotapr='$cotapr' where id=$id");
	 
	 q("insert into aoacol_administra.app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."',
			'".date('s')."',' $user','$Solicitado_por','requisicion','M','$id','".$_SERVER['REMOTE_ADDR']."','Cambair el estado de la requisicion a Aprobar desde el correo')");

	 	    $verificar=qo("select ubicacion from aoacol_administra.requisicion where id = $id");
				if($verificar->ubicacion == 0){
					if($D->perfil == 3){
					 include('pdfrequisicionAdmTo.php');	
					}else{
					include('pdfrequisicionAdm.php');
					}
				}else{
					if($D->perfil == 3){
					 include('pdfrequisicionOpeTo.php');	
					}else{
					include('pdfrequisicionOpe.php');
					}
					
				}
				
				 $correo = $eUsuario;
				 
				   $pdf->Output('/var/www/html/public_html/Administrativo/pdfrequisiciones/requicion'.$id.'.pdf','F');
               $archivo = "https://app.aoacolombia.com/Administrativo/pdfrequisiciones/requicion$id.pdf";
				$nameArchivo = "RequisicionAprobar.pdf";
				 
				 if($D->perfil == 3){
				   
					$contenido = 44;
					 
					}else{
						
					$contenido = 4;
					
					}
			   
				
				
				 echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'>
		  </script>
		  <script>
            $.ajax(
                    {
                          url: 'https://sac.aoacolombia.com/ServiEmail.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							enviarEmail:'true ',
							APIKEYAOAAPP:'yNPlsmOGgZoGmH$129',
							para: '$eSolicitado_por;davidduque@aoacolombia.com',
							paraNombre: '$Solicitado_por',
						    copia: '$eUsuario',
							nombreCopia: '$Usuario',
							contenido:'$contenido',
							asunto:'CONFIRMACION DE APROBACION DE PEDIDO $id',
							id:'$id',
							usuario:'$user  $email',
							nameArchivo:'$nameArchivo',
							observaciones : '$observaciones',
							archivo:'$archivo'
					    },
                        success: function (response)
                        {
                            alert(data.para);
                        }
                    });
					
					alert('Email enviado satisfactoriamente a $eSolicitado_por');	
                </script>
				</body>";	
			
       $Proveedor=qo("select * from aoacol_administra.proveedor where id=$D->proveedor");
	   
	$EmailDestino='';
	if($Proveedor->nombre) $Nprov=$Proveedor->nombre;elseif($Proveedor->nombre) $Nprov=$Proveedor->nombre;else $Nprov='PROVEEDOR';
	// busqueda del correo electronico de acuerdo a la sede registrada en la requisici�n
	if($D->sede)
	{
		if($Sede=qo("select * from aoacol_administra.prov_sede where id=$D->sede"))
		{
			if($Sede->email) $EmailDestino=$Sede->email;
		}
	}
	
	$Mensaje=urlencode(base64_encode("Autorizacion satisfactoria Sede: " ));
	// si no hay sede registrada en la requisici�n, se toma el email del registro principal del proveedor
	if(!$EmailDestino)
	{
		if($Proveedor->email) {$EmailDestino="$Proveedor->email";}
	}
	// si hay correo electronico, se envia el mensaje
	
	if($EmailDestino)
	{
		$Det="<table class='table' border cellspacing='0'><tr><th>Tipo de Requisicion</th><th>Item</th><th>Unidad de medida</th><th>Descripcion</th><th>Cantidad</th><th>Valor unitario</th><th>Valor</th>";
	        $Detalle=q("select provee_produc_serv.nombre as item,tipo.nombre as tipo,unidad_de_medida.nombre as unidad_medida,requisiciond.observaciones,requisiciond.cantidad,
                    requisiciond.requisicion,requisiciond.valor_total,requisiciond.cantidad,requisiciond.valor as valor_unitario
					from aoacol_administra.requisiciond
					inner join aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					inner join aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					inner join aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
					where requisicion =$id");
	        while($Dt =mysql_fetch_object($Detalle ))
	        {
		    $Det.="<tr><td>$Dt->tipo</td><td>$Dt->item</td><td>$Dt->unidad_medida</td><td>$Dt->observaciones</td><td>$Dt->cantidad</td><td align='right'>$".coma_format($Dt->valor_unitario)."</td><td align='right'>$".coma_format($Dt->valor_total)."</td></tr>";
	        }
	        $Det.="</table>";
			
			$Res="<table border cellspacing='4'><tr><th>Resultado</th>";
        
		     $retorno=q("select requisiciond.requisicion,requisiciond.valor_total,
		            sum(requisiciond.valor_total) as resultado 
					from aoacol_administra.requisiciond
					where requisicion  =$id");
			while($Dt =mysql_fetch_object($retorno))
			{
			   $Res.="<tr><td>$".coma_format($Dt->resultado)."</td>";
			}
			$Res.="</table>";
			
			$Ciudades=qo("select requisicion.ciudad as campoCity ,ciudad.nombre as ciudad, 
                    ciudad.departamento
					from aoacol_administra.requisiciond
					inner join aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id
                    inner join aoacol_administra.ciudad on requisicion.ciudad = ciudad.codigo
                    where requisiciond.requisicion = $id limit 1");
		
		$ciudad = $Ciudades->ciudad;
		
		   	$departamento = $Ciudades->departamento;
			  
				q("update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observa_aprobacion='$observa_aprobacion',cotapr='$cotapr' where id=$id");
  
			    $correo = $Proveedor->email ;
				
			   $pdf->Output('/var/www/html/public_html/Administrativo/pdfrequisiciones/requicion'.$id.'.pdf','F');
               $archivo = "https://app.aoacolombia.com/Administrativo/pdfrequisiciones/requicion$id.pdf";
			   $nameArchivo = "RequisicionAprobar.pdf";
                if($D->perfil == 3){
				   
					$contenido = 55;
					 
					 
					}else{
						
					$contenido = 5;
					
					}
			   
			   
			   
			   
			   
			   

					 echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
		  <script>
		
            $.ajax(
                    {
                         url: 'https://sac.aoacolombia.com/ServiEmail.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							enviarEmail:'true ',
							APIKEYAOAAPP:'yNPlsmOGgZoGmH$129',
							para:'$correo',
						    copia:'$eUsuario',
							nombreCopia:'$Solicitado_por',
							contenido:'$contenido',
							asunto:'CONFIRMACION DE APROBACION DE PEDIDO $id',
							id:'$id',
							usuario:'$Usuario',
							nameArchivo:'$nameArchivo',
							archivo:'$archivo',
							ciudad:'$ciudad',
							departamento:'$departamento',
							Nprov:'$Nprov'
							},
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
					
				alert('Email enviado satisfactoriamente a $correo');	
        </script>
				</body>";	
				
					exit();	
	}
	else 
	echo "<b>El proveedor no tiene correo electr�nico definido.</b>";
}


function rol_aprobar_sin_proveedor()
{

	global $id,$user,$email,$Fecha ,$Usuario,$eUsuario,$Solicitado_por,$eSolicitado_por,$observaciones,$observa_aprobacion,$cotapr;
		
	$D=qo("select * from aoacol_administra.requisicion where id=$id");
	
	//return print_r($D);
	
 	//q("update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observaciones=\"$observaciones\",cotapr='$cotapr' where id=$id");
	
	//echo "update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observa_aprobacion='$observa_aprobacion',cotapr='$cotapr' where id=$id";
	
	q("update aoacol_administra.requisicion set estado=2,aprobado_por='$Usuario',observa_aprobacion='$observa_aprobacion',cotapr='$cotapr' where id=$id");
	 
	 q("insert into aoacol_administra.app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."',
			'".date('s')."',' $user','$Solicitado_por','requisicion','M','$id','".$_SERVER['REMOTE_ADDR']."','Cambair el estado de la requisicion a Aprobar desde el correo')");

	 	    $verificar=qo("select ubicacion from aoacol_administra.requisicion where id = $id");
				if($verificar->ubicacion == 0){
					include('pdfrequisicionAdm.php');
				}else{
					include('pdfrequisicionOpe.php');
				}
				
				 $correo = $eUsuario;
				 
				   $pdf->Output('/var/www/html/public_html/Administrativo/pdfrequisiciones/requicion'.$id.'.pdf','F');
               $archivo = "https://app.aoacolombia.com/Administrativo/pdfrequisiciones/requicion$id.pdf";
				$nameArchivo = "RequisicionAprobar.pdf";
				 

				$contenido = 4;
				 echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'>
		  </script>
		  <script>
            $.ajax(
                    {
                         url: 'https://sac.aoacolombia.com/ServiEmail.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							enviarEmail:'true ',
							APIKEYAOAAPP:'yNPlsmOGgZoGmH$129',
							para: '$eSolicitado_por',
							paraNombre: '$Solicitado_por',
						    copia: '$eUsuario',
							nombreCopia: '$Usuario',
							contenido:'$contenido',
							asunto:'CONFIRMACION DE APROBACION DE PEDIDO $id',
							id:'$id',
							usuario:'$user  $email',
							nameArchivo:'$nameArchivo',
							observaciones : '$observaciones',
							archivo:'$archivo'
					    },
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
					
					alert('Email enviado satisfactoriamente a $eSolicitado_por');	
                </script>
				</body>";	
						
					exit();	
	}
	

	

function rol_anular_requisicion()
{
	
			global $id,$user,$email,$Fecha ,$Usuario,$eUsuario,$Solicitado_por,$eSolicitado_por,$observaciones,$observa_aprobacion,$cotapr;
	
      $D=qo("select * from aoacol_administra.requisicion where id=$id");
	
	q("update aoacol_administra.requisicion set estado=5,aprobado_por='$Usuario',observaciones=\"$observaciones\",cotapr='$cotapr' where id=$id");
	
	
	q("update aoacol_administra.requisicion set estado=5,aprobado_por='$Usuario',observa_aprobacion='$observa_aprobacion',cotapr='$cotapr' where id=$id");
    q("insert into aoacol_administra.app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."',
			'".date('s')."',' $user','$Solicitado_por','requisicion','M','$id','".$_SERVER['REMOTE_ADDR']."','Cambair el estado de la requisicion a Anulada  desde el sitio web ')");


	$verificar=qo("select ubicacion from aoacol_administra.requisicion where id = $id");
				
				
		       if($verificar->ubicacion == 0){
					if($D->perfil == 3){
					 include('anuladoPdfAdmTo.php');	
					}else{
					include('anuladoPdfAdm.php');
					}
				}else{
					if($D->perfil == 3){
					 include('anuladoPdfOpeTo.php');	
					}else{
					include('anuladoPdfOpe.php');
					}
					
				}	
			
			    
			
				
				 $correo = $eUsuario;

                  $pdf->Output('/var/www/html/public_html/Administrativo/pdfrequisiciones/requicion'.$id.'.pdf','F');
               $archivo = "https://app.aoacolombia.com/Administrativo/pdfrequisiciones/requicion$id.pdf";
				$nameArchivo = "RequisicionAprobar.pdf";
				 
               $contenido = 14;
			   
			   if($D->perfil == 3){
				   
					 $contenido = 16;
					 
					}else{
						
					$contenido = 14;
					
					}
			   
				 echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'>
		  </script>
		  
		  <script>
            $.ajax(
                    {
                         url: 'https://sac.aoacolombia.com/ServiEmail.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							enviarEmail:'true ',
							APIKEYAOAAPP:'yNPlsmOGgZoGmH$129',
							copia:'$eUsuario',
							nombreCopia:'$Usuario',
						    para:'$eSolicitado_por',
							paraNombre:'$Solicitado_por',
							contenido:'$contenido',
							asunto:'CONFIRMACION LA ANULACINON DE PEDIDO $id',
							id:'$id',
							usuario:'$user  $email',
							nameArchivo:'$nameArchivo',
							observaciones : '$observaciones',
							archivo:'$archivo'
							},
							
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
					
				
				
        </script>
	
				
				</body>";	

				
				 
				 
				 
       $Proveedor=qo("select * from aoacol_administra.proveedor where id=$D->proveedor");
	$EmailDestino='';
	if($Proveedor->nombre) $Nprov=$Proveedor->nombre;elseif($Proveedor->nombre) $Nprov=$Proveedor->nombre;else $Nprov='PROVEEDOR';
	// busqueda del correo electronico de acuerdo a la sede registrada en la requisici�n
	if($D->sede)
	{
		if($Sede=qo("select * from aoacol_administra.prov_sede where id=$D->sede"))
		{
			if($Sede->email) $EmailDestino=$Sede->email;
		}
	}
	$Mensaje=urlencode(base64_encode("Autorizacion satisfactoria Sede: " ));
	// si no hay sede registrada en la requisici�n, se toma el email del registro principal del proveedor
	if(!$EmailDestino)
	{
		
		if($Proveedor->email) {$EmailDestino="$Proveedor->email";}
	}
	// si hay correo electronico, se envia el mensaje
	if($EmailDestino)
	{
		$Det="<table class='table' border cellspacing='0'><tr><th>Tipo de Requisicion</th><th>Item</th><th>Unidad de medida</th><th>Descripcion</th><th>Cantidad</th><th>Valor unitario</th><th>Valor</th>";
	        $Detalle=q("select provee_produc_serv.nombre as item,tipo.nombre as tipo,unidad_de_medida.nombre as unidad_medida,requisiciond.observaciones,requisiciond.cantidad,
                    requisiciond.requisicion,requisiciond.valor_total,requisiciond.cantidad,requisiciond.valor as valor_unitario
					from aoacol_administra.requisiciond
					inner join aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					inner join aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					inner join aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
					where requisicion =$id");
	        while($Dt =mysql_fetch_object($Detalle ))
	        {
		    $Det.="<tr><td>$Dt->tipo</td><td>$Dt->item</td><td>$Dt->unidad_medida</td><td>$Dt->observaciones</td><td>$Dt->cantidad</td><td align='right'>$".coma_format($Dt->valor_unitario)."</td><td align='right'>$".coma_format($Dt->valor_total)."</td></tr>";
	        }
	        $Det.="</table>";
			
			
			$Res="<table border cellspacing='4'><tr><th>Resultado</th>";
        
		     $retorno=q("select requisiciond.requisicion,requisiciond.valor_total,
		            sum(requisiciond.valor_total) as resultado 
					from aoacol_administra.requisiciond
					where requisicion  =$id");
			while($Dt =mysql_fetch_object($retorno))
			{
			   $Res.="<tr><td>$".coma_format($Dt->resultado)."</td>";
			}
			$Res.="</table>";
			
			
			$Ciudades=qo("select requisicion.ciudad as campoCity ,ciudad.nombre as ciudad, 
                    ciudad.departamento
					from aoacol_administra.requisiciond
					inner join aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id
                    inner join aoacol_administra.ciudad on requisicion.ciudad = ciudad.codigo
                    where requisiciond.requisicion = $id limit 1");
			$ciudad = $Ciudades->ciudad;
			$departamento = $Ciudades->departamento;
			  
				q("update aoacol_administra.requisicion set estado=5,aprobado_por='$Usuario',observa_aprobacion='$observa_aprobacion',cotapr='$cotapr' where id=$id");
  
			      
			    $correo = $Proveedor->email ;

			   $pdf->Output('/var/www/html/public_html/Administrativo/pdfrequisiciones/requicion'.$id.'.pdf','F');
               $archivo = "https://app.aoacolombia.com/Administrativo/pdfrequisiciones/requicion$id.pdf";
			   $nameArchivo = "RequisicionAprobar.pdf";

				
				if($D->perfil == 3){
				   
					 $contenido = 19;
					 
					}else{
						
					$contenido = 15;
					
					}
				
               
				
					 echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
		  <script>
            $.ajax(
                    {
                          url: 'https://sac.aoacolombia.com/ServiEmail.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
							enviarEmail:'true ',
							APIKEYAOAAPP:'yNPlsmOGgZoGmH$129',
							para:'$correo',
						    copia:'$eUsuario',
							nombreCopia:'$Solicitado_por',
							contenido:'$contenido',
							asunto:'CONFIRMACION LA ANULACINON DE PEDIDO $id',
							id:'$id',
							usuario:'$Usuario',
							nameArchivo:'$nameArchivo',
							archivo:'$archivo',
							ciudad: '$ciudad',
							departamento: '$departamento',
							Nprov : '$Nprov'
							
							},
							
                        success: function (response)
                        {

                        }
						
								  

                    });
					
					    alert('Email enviado satisfactoriamente a $correo');	

        </script>
				</body>";	

				
					exit();
								
				
	}
	else 
	echo "<b>El proveedor no tiene correo electr�nico definido.</b>";
	
	//******************************************************************************************************
}



function rol_daprobar_requisicion()
{
		global $id,$user,$Fecha ,$Usuario,$eUsuario,$Solicitado_por,$eSolicitado_por,$observaciones,$observa_aprobacion,$cotapr;
	
    $D=qo("select * from aoacol_administra.requisicion where id=$id");
	//return print_r($D);
	//if($D->estado==2) {$Mensaje=urlencode(base64_encode("El estado de esta requisici�n ya fue procesado y es: Aprobado." ));
	//header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	//if($D->estado==3) {$Mensaje=urlencode(base64_encode("El estado de esta requisici�n ya fue procesado y es: Rechazado." ));
	//header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	//if($D->estado==4) {$Mensaje=urlencode(base64_encode("El estado de esta requisici�n ya fue procesado y es: Calificado." ));
	//header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	q("update aoacol_administra.requisicion set estado=3,aprobado_por='$Usuario',observaciones=\"$observaciones\" where id=$id");
	$Mensaje=urlencode(base64_encode("Autorizacion negada satisfactoriamente." ));
		
		q("insert into aoacol_administra.app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."',
			'".date('s')."',' $user','$Solicitado_por','requisicion','M','$id','".$_SERVER['REMOTE_ADDR']."','Cambair el estado de la requisicion a Deprobado desde el correo')");

						if($D->perfil == 3){
				   
					 $contenido = 012;
					 
					}else{
						
					$contenido = 12;
					
					}
			
			echo "<body>
					<script
          src='https://code.jquery.com/jquery-3.4.1.min.js'
          integrity='sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='
          crossorigin='anonymous'></script>
		  <script>
            $.ajax(
                    {
                        url: 'https://sac.aoacolombia.com/ServiEmail.php',
                        type: 'POST',
                        dataType: 'text',
                        data: {
                            copia:'$eUsuario',
							nombreCopia:'$Usuario',
						    para:'$eSolicitado_por',
							paraNombre:'$Solicitado_por',
							contenido:'$contenido',
							
							asunto:'NOTIFICACION DE RECHAZADO DE REQUISICION  $id',
							id:'$id',
							usuario:'$user [$email]'  
							},
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
						alert('Email enviado satisfactoriamente a $eSolicitado_por');	
        </script>	
				
				</body>";	

}

function daprobar_requisicion()
{
		global $id,$user,$Fecha ,$Usuario,$eUsuario,$Solicitado_por,$eSolicitado_por,$observaciones,$observa_aprobacion,$cotapr;
	
    $D=qo("select * from aoacol_administra.requisicion where id=$id");
	//return print_r($D);
	//if($D->estado==2) {$Mensaje=urlencode(base64_encode("El estado de esta requisici�n ya fue procesado y es: Aprobado." ));
	//header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	//if($D->estado==3) {$Mensaje=urlencode(base64_encode("El estado de esta requisici�n ya fue procesado y es: Rechazado." ));
	//header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	//if($D->estado==4) {$Mensaje=urlencode(base64_encode("El estado de esta requisici�n ya fue procesado y es: Calificado." ));
	//header("location:operativo.php?Acc=mensaje_operativo_alerta&Mensaje=$Mensaje");die();}
	q("update aoacol_administra.requisicion set estado=3,aprobado_por='$Usuario',observaciones=\"$observaciones\" where id=$id");
	$Mensaje=urlencode(base64_encode("Autorizacion negada satisfactoriamente." ));
		
		q("insert into aoacol_administra.app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."',
			'".date('s')."',' $user','$Solicitado_por','requisicion','M','$id','".$_SERVER['REMOTE_ADDR']."','Cambair el estado de la requisicion a Deprobado desde el correo')");

			$correo = $eUsuario;
			$contenido = 12;
			
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
							para:'$eSolicitado_por',
							paraNombre:'$Solicitado_por',
						    copia:'$eUsuario',
							nombreCopia:'$Usuario',
							contenido:'$Usuario',
							asunto:'NOTIFICACION DE RECHAZADO DE REQUISICION  $id',
							id:'$id',
							usuario:'$user [$email]'  
							},
                        success: function (response)
                        {
                            alert(response);
                        }
                    });
						alert('Email enviado satisfactoriamente a $eSolicitado_por');	
        </script>	
				
				</body>";	

}

function descargar_imagen_requisicion()
{
	global $img;
	if($img)
	{
		if(!strpos(strtoupper($img),"PDF"))
		{
			header("Pragma: public");header("Expires: 0");header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=\"imagen.jpg\"");
			header("Content-Description: File Transfert");@readfile("../../Administrativo/$img");
		}
		else
		{
			header("Content-type:application/pdf");
			header("Content-Disposition:attachment;filename='downloaded.pdf'");
			readfile("../../Administrativo/$img");
		}
	}
	else
	{
		$Mensaje=urlencode(base64_encode("<font color='red'><b>No hay ninguna im�gen.</b></font>" ));
		header("location:operativo.php?Acc=mensaje_operativo&Mensaje=$Mensaje");die();
	}
}

function mensaje_operativo_alerta()
{
	global
 $Mensaje;html('AUTORIZACION');
 echo "<body>".base64_decode($Mensaje)." <script language='javascript'>alert('".base64_decode($Mensaje)."');</script></body>";}













?>