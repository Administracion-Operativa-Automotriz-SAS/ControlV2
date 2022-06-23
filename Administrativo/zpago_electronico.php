<?php
include('inc/funciones_.php');
sesion();
$Nusuario=$_SESSION['Nombre'];
if (!empty($Acc) && function_exists($Acc)){eval($Acc.'();');die();}

require('inc/link.php');
$por_ventanilla=sino($por_ventanilla);
foreach($_POST as $Campo => $Valor)
{
	if($Campo=='RePTE') $Id=$Valor;
	elseif($Campo=='T') $T=$Valor;
	elseif(strpos(' '.$Campo,'pe_'))
	{
		$Proveedor=substr($Campo,3);
		$Datos=mysql_query("select * from $T where pr_id=$Proveedor order by proveedor,concepto,oficina",$LINK);
		if(mysql_num_rows($Datos))
		{
			$Prov=qom("select * from proveedor where id=$Proveedor",$LINK);
			if($por_ventanilla)
			{
				if($Prov->ofipagov)
				{
					while($D=mysql_fetch_object($Datos))
					{
						mysql_query("insert into dpago (pago,aprobacion,factura,proveedor,valor) values
						('$RePTE','$D->ap_id','$D->fac_id','$D->pr_id','$D->aprobado')",$LINK);
						mysql_query("update factura_ap set girado=1 where id=$D->ap_id",$LINK);
					}
				}
			}
			else
			{
				if($Prov->certif_banco_f || $Prov->pago_servicios)
				{
					while($D=mysql_fetch_object($Datos))
					{
						mysql_query("insert into dpago (pago,aprobacion,factura,proveedor,valor) values
						('$RePTE','$D->ap_id','$D->fac_id','$D->pr_id','$D->aprobado')",$LINK);
						mysql_query("update factura_ap set girado=1 where id=$D->ap_id",$LINK);
					}
				}
			}
		}
	}
}
$Suma=qo1m("select sum(valor) from dpago where pago=$Id",$LINK);
mysql_query("update pago set valor=$Suma where id=$Id",$LINK);
mysql_close($LINK);
echo "<script language='javascript'>
alert('La información fue insertada en la tabla de pagos en el pago [$Id ]');
window.close();void(null);
</script>
</body>";

function generar_plano()
{
	global $id,$Inscripcion;
	$Pago=qo("select * from pago where id=$id");
	$Cuenta_dispersora=qo("select * from banco where id=$Pago->banco");
	if($Cuenta_dispersora->electronico)
	{
		$Rutina=$Cuenta_dispersora->procedimiento_pe;
		header("location:zpago_electronico.php?Acc=$Rutina&id=$id&Inscripcion=$Inscripcion");
	}
	else
	{
		html();
		echo "<body><h3>La cuenta dispersora no tiene definido el procedimiento de genereación de plano.</h3></body>";
	}
}

function generar_plano_bancolombia()
{
  global $id,$Inscripcion,$Nusuario;  // id es el registro de pago
  html();
  $Fin_de_linea="\r\n";
  $Pago=qo("select * from pago where id=$id");
  $Cuenta_dispersora=qo("select * from banco where id=$Pago->banco");
  //
  if($Detalle=q("select proveedor,sum(valor) as valor from dpago where pago=$id group by proveedor order by proveedor"))
  {
    $DESTINO_PLANO1 = 'planos/int1_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
    $Total_lineas="";
    echo "
      <script language='javascript'>
              function bajar_archivos()
              {
                  window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=t.txt','o1');
              }
      </script>
      <body>";
    echo "Total: $Pago->valor cuenta dispersora: $Cuenta_dispersora->numero $Cuenta_dispersora->nombre_banco";
    $Contador_registro=0;
    $Sumatoria=0;
    while($D=mysql_fetch_object($Detalle))
    {
      $P=qo("select * from proveedor where id=$D->proveedor");
    	if($P->beneficiario && $P->identif_beneficiario) {$NBeneficiario=$P->beneficiario; $IBeneficiario=$P->identif_beneficiario;} else {$NBeneficiario=$P->nombre;$IBeneficiario=$P->identificacion;}
      echo "<br>$NBeneficiario ".coma_format($D->valor);
      $Nit=str_pad($IBeneficiario,15,"0",STR_PAD_LEFT);
      $Nombre=str_pad(substr($NBeneficiario,0,30),30," ",STR_PAD_RIGHT);
      $Banco=str_pad(qo1("select codigo from codigo_ach where id=$P->banco"),9,'0',STR_PAD_LEFT);
      $Cuenta=str_pad($P->numero_cuenta,17,' ',STR_PAD_RIGHT);
      $ILP=' '; // indicador de lugar de pago
      $Tipo_transaccion=($P->tipo_cuenta=='C'?'27':'37');
      $Valor=prepara_valor(($Inscripcion?0:$D->valor),15,2);
      $Fecha=date('Ymd',strtotime($Pago->fecha));
      $Referencia='000000000000000000000';
      if($P->td=='NIT') $TDI='3';
      elseif($P->td=='CC') $TDI='1';
      elseif($P->td=='RUT') $TDI='3';
      $Oficina='00000'; // oficina pagadora si es el caso
      $Fax='               '; // fax de la oficina pagadora
      $Email='                                                                                '; // email del beneficiario si hay convenio
      $IDA='               '; // Identificacion del autorizado
      $Filler='                           '; // relleno
      $Linea="6".$Nit.$Nombre.$Banco.$Cuenta.$ILP.$Tipo_transaccion.$Valor.$Fecha.$Referencia.$TDI.$Oficina.$Fax;
      $Linea.=$Email.$IDA.$Filler;
      $Linea.=$Fin_de_linea;
      $Total_lineas.=$Linea;
      $Contador_registro++;
      $Sumatoria+=($Inscripcion?0:$D->valor);
    }
    if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
    $Cabeza="1".str_pad(900174552,15,'0',STR_PAD_LEFT); // nit de la empresa originadora
    $Cabeza.='I               '; // aplicacion I: inmediata
    $Cabeza.='220'; // Clase de transaccion 220: pago a proveedores
    $Cabeza.='          '; // descripcion del pago
    $Cabeza.=date('Ymd',strtotime($Pago->fecha)); // fecha de transmision
    $Cabeza.='AA'; // Secuencia de transmision si es en el mismo dia, esto debe cambiar
    $Cabeza.=date('Ymd',strtotime($Pago->fecha)); // fecha de aplicacion de la transaccion
    $Cabeza.=str_pad($Contador_registro,6,'0',STR_PAD_LEFT); // numero de registros
    $Cabeza.=prepara_valor(0,15,2); // sumatoria debitos ==0
    $Cabeza.=prepara_valor($Sumatoria,15,2); // sumatoria creditos
    $Cabeza.=$Cuenta_dispersora->numero; // cuenta dispersora
    $Cabeza.='D'; // tipo de cuenta D: corriente
    $Cabeza.=str_repeat(' ',144); // relleno para futuros usos
    $Cabeza.=$Fin_de_linea;
    $DD1 = fopen($DESTINO_PLANO1, 'w+');
    fwrite($DD1, $Cabeza);
    fwrite($DD1, $Total_lineas);
    fclose($DD1);
	/// EMAIL AUTOMATICO
	$Email_usuario=usuario('email');
	$Ahora=date('Y-m-d H:i:s');
	$Ip=$_SERVER['REMOTE_ADDR'];
	enviar_gmail($Email_usuario,$Nusuario,'shurtado@aoacolombia.co,SEBASTIAN HURTADO',
		'','TRANSFERENCIA BANCARIA',
		"Este mensaje es automatico y corresponde a una transferencia bancaria generada por $Nusuario el dia $Ahora desde la ip: $Ip ",
		"$DESTINO_PLANO1,transferencia_bancolombia.txt");
	
    echo "
      <br><br>
       <iframe name='o1' style='visibility:hidden' height='1'></iframe>
       <input type='button' value='CERRAR ESTA VENTANA' onclick='window.close();void(null)'>
       <script language='javascript'>bajar_archivos();</script>
      </body>";
  }
}

function generar_plano_bogota()
{
  global $id,$Inscripcion,$Nusuario;  // id es el registro de pago
  html();
  $Fin_de_linea="\r\n";
  $Pago=qo("select * from pago where id=$id");
  $Cuenta_dispersora=qo("select * from banco where id=$Pago->banco");
  //
  if($Detalle=q("select proveedor,sum(valor) as valor,factura from dpago where pago=$id group by proveedor order by proveedor"))
  {
    $DESTINO_PLANO1 = 'planos/int1_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
    $Total_lineas="";
    echo "
      <script language='javascript'>
              function bajar_archivos()
              {
                  window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=t.txt','o1');
              }
      </script>
      <body>";
    echo "Total: $Pago->valor cuenta dispersora: $Cuenta_dispersora->numero $Cuenta_dispersora->nombre_banco";
    $Contador_registro=0;
    $Sumatoria=0;
    while($D=mysql_fetch_object($Detalle))
    {
		$Fac=qo("Select * from factura where id=$D->factura");
		$P=qo("select * from proveedor where id=$D->proveedor");
		if($P->td=='NIT') $TDI='N';elseif($P->td=='CC') $TDI='C';elseif($P->td=='CE') $TDI='E';
		if($P->beneficiario && $P->identif_beneficiario)  {$NBeneficiario=$P->beneficiario; $IBeneficiario=$P->identif_banbogota;} 
		else {$NBeneficiario=$P->nombre;$IBeneficiario=$P->identificacion.($P->td=='NIT'?$P->dv:"");}
		echo "<br>$NBeneficiario ".coma_format($D->valor);
		$Nit=str_pad($IBeneficiario,11,"0",STR_PAD_LEFT);
		$Nombre=str_pad(substr($NBeneficiario,0,40),40," ",STR_PAD_RIGHT);
		$Tipo_cuenta_beneficiaria=($P->tipo_cuenta=='C'?"1":"2");	
		$Cuenta=str_pad($P->numero_cuenta,17,' ',STR_PAD_RIGHT);
		$Valor=prepara_valor($D->valor,16,2);
		$Banco=str_pad(qo1("select codigo_banrep from codigo_ach where id=$P->banco"),3,'0',STR_PAD_LEFT);
		$ILP=' '; // indicador de lugar de pago
		$Tipo_transaccion=($P->tipo_cuenta=='C'?'27':'37');
		$Fecha=date('Ymd',strtotime($Pago->fecha));
		$Referencia='000000000000000000000';
		$Oficina='00000'; // oficina pagadora si es el caso
		$Fax='               '; // fax de la oficina pagadora
		$Email='                                                                                '; // email del beneficiario si hay convenio
		$IDA='               '; // Identificacion del autorizado
		$Filler='                           '; // relleno
		$Linea="2".$TDI.$Nit.$Nombre."0".$Tipo_cuenta_beneficiaria.$Cuenta.$Valor."A"."000".$Banco.$P->ciudad_cuenta;
		$Linea.="PAG      "." ".str_pad("PAGO FACTURA $Fac->numero",70," ",STR_PAD_RIGHT);
		$Linea.="0".str_pad($Pago->comp_egreso,10,'0',STR_PAD_LEFT)."N"."        ";
		$Linea.="000000000000000000"."           "."           "."N"."        ";
		$Linea.=$Fin_de_linea;
		$Total_lineas.=$Linea;
		$Contador_registro++;
		$Sumatoria+=($Inscripcion?0:$D->valor);
    }
    if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
	$Cabeza="1";  // Tipo de registro 
	$Cabeza.=date('Ymd',strtotime($Pago->fecha)); // fecha del pago
	$Cabeza.="000000000000000000000000"; // 24 ceros
	$Cabeza.="1"; // Tipo de cuenta, 1: corriente 2: ahorros
	$Cabeza.="000000"; // 6 ceros
	$Cabeza.="00".$Cuenta_dispersora->numero; // cuenta dispersora con 2 ceros antes
	$Cabeza.="ADMINISTRACION OPERATIVA AUTOMOTRIZ     "; // NOMBRE DE LA EMPRESA DISPERSORA 40 CARACTERES
	$Cabeza.="09001745525"; // numero de identificacion con digito de chequeo
	$Cabeza.="002";// tipo de dispersion 002: proveedores 001: nomina 003: transferencias 995: otros
	$Cabeza.="0001"; // codigo de ciudad Bogota
	$Cabeza.=date('Ymd',strtotime($Pago->fecha)); // fecha de elaboracion
	$Cabeza.=l($Cuenta_dispersora->numero,3); // Codigo de la oficina de la cuenta dispersora, los 3 primeros digitos de la cuenta
	$Cabeza.="N"; // Tipo de identificacion del dispersor N: nit C: cedula T:tarjeta de identidad E:extranjeria
	$Cabeza.="                                                "; // 48 espacios en blanco
	$Cabeza.=" "; // indicador de archivo adicional de mensajes. S si se envia  espacio en blanco si no se envia nada
	$Cabeza.="                                                                                "; // 80 espacios en blanco
    $Cabeza.=$Fin_de_linea;
    $DD1 = fopen($DESTINO_PLANO1, 'w+');
    fwrite($DD1, $Cabeza);
    fwrite($DD1, $Total_lineas);
    fclose($DD1);
	/// EMAIL AUTOMATICO
	$Email_usuario=usuario('email');
	$Ahora=date('Y-m-d H:i:s');
	$Ip=$_SERVER['REMOTE_ADDR'];
	enviar_gmail($Email_usuario,$Nusuario,'shurtado@aoacolombia.co,SEBASTIAN HURTADO',
		'','TRANSFERENCIA BANCARIA',
		"Este mensaje es automatico y corresponde a una transferencia bancaria generada por $Nusuario el dia $Ahora desde la ip: $Ip ",
		"$DESTINO_PLANO1,transferencia_bancobogota.txt");
    echo "
      <br><br>
       <iframe name='o1' style='visibility:hidden' height='1'></iframe>
       <input type='button' value='CERRAR ESTA VENTANA' onclick='window.close();void(null)'>
       <script language='javascript'>bajar_archivos();</script>
      </body>";
  }
}

function generar_plano_ventanilla()
{
	global $id,$Nusuario;  // id es el registro de pago
	html();
	$Fin_de_linea="\r\n";
	$Pago=qo("select * from pago where id=$id");
	$Cuenta_dispersora=qo("select * from banco where id=$Pago->banco");
	//
	if($Detalle=q("select proveedor,t_proveedor(proveedor) as nproveedor,sum(valor) as valor from dpago where pago=$id group by proveedor order by proveedor"))
	{
		$DESTINO_PLANO1 = 'planos/int1_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
		$Total_lineas="";
		echo "
		  <script language='javascript'>
		          function bajar_archivos()
		          {
		              window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=t.txt','o1');
		          }
		  </script>
		  <body>";
		echo "Total: $Pago->valor cuenta dispersora: $Cuenta_dispersora->numero $Cuenta_dispersora->nombre_banco";
		$Contador_registro=0;
		$Sumatoria=0;
		while($D=mysql_fetch_object($Detalle))
		{
			$P=qo("select * from proveedor where id=$D->proveedor");
			$Ventanilla=qo1("select codigo from sucursal_banco where id='$P->ofipagov'");
			echo "<br>$D->proveedor $D->nproveedor ".coma_format($D->valor);
			$Nit=str_pad($P->identificacion,15,"0",STR_PAD_LEFT);
			$Nombre=str_pad(substr($P->nombre,0,30),30," ",STR_PAD_RIGHT);
			$Banco='000000000';
			$Cuenta='00000000000000000';
			$ILP=' '; // indicador de lugar de pago
			$Tipo_transaccion='25';
			$Valor=prepara_valor(($Inscripcion?0:$D->valor),15,2);
			$Fecha=date('Ymd',strtotime($Pago->fecha));
			$Referencia='000000000000000000000';
			$TD=($P->td_autorizado?$P->td_autorizado:$P->td);
			if($TD=='NIT') $TDI='3';elseif($TD=='CC') $TDI='1'; elseif($TD=='RUT') $TDI='3';
			$Oficina=str_pad($Ventanilla,5,'0',STR_PAD_LEFT); // oficina pagadora si es el caso
			$Fax='               '; // fax de la oficina pagadora
			$Email='                                                                                '; // email del beneficiario si hay convenio
			$IDA=str_pad(' ',15,' ',STR_PAD_RIGHT);
			$Filler='                           '; // relleno
			$Linea="6".$Nit.$Nombre.$Banco.$Cuenta.$ILP.$Tipo_transaccion.$Valor.$Fecha.$Referencia.$TDI.$Oficina.$Fax;
			$Linea.=$Email.$IDA.$Filler;
			$Linea.=$Fin_de_linea;
			$Total_lineas.=$Linea;
			$Contador_registro++;
			$Sumatoria+=($Inscripcion?0:$D->valor);
		}
		if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
		$Cabeza="1".str_pad(900174552,15,'0',STR_PAD_LEFT); // nit de la empresa originadora
		$Cabeza.='I               '; // aplicacion I: inmediata
		$Cabeza.='220'; // Clase de transaccion 220: pago a proveedores
		$Cabeza.='          '; // descripcion del pago
		$Cabeza.=date('Ymd',strtotime($Pago->fecha)); // fecha de transmision
		$Cabeza.='AA'; // Secuencia de transmision si es en el mismo dia, esto debe cambiar
		$Cabeza.=date('Ymd',strtotime($Pago->fecha)); // fecha de aplicacion de la transaccion
		$Cabeza.=str_pad($Contador_registro,6,'0',STR_PAD_LEFT); // numero de registros
		$Cabeza.=prepara_valor(0,15,2); // sumatoria debitos ==0
		$Cabeza.=prepara_valor($Sumatoria,15,2); // sumatoria creditos
		$Cabeza.=$Cuenta_dispersora->numero; // cuenta dispersora
		$Cabeza.='D'; // tipo de cuenta D: corriente
		$Cabeza.=str_repeat(' ',149); // relleno para futuros usos
		$Cabeza.=$Fin_de_linea;
		$DD1 = fopen($DESTINO_PLANO1, 'w+');
		fwrite($DD1, $Cabeza);
		fwrite($DD1, $Total_lineas);
		/// EMAIL AUTOMATICO
		$Email_usuario=usuario('email');
		$Ahora=date('Y-m-d H:i:s');
		$Ip=$_SERVER['REMOTE_ADDR'];
		enviar_gmail($Email_usuario,$Nusuario,'shurtado@aoacolombia.co,SEBASTIAN HURTADO',
		'','TRANSFERENCIA BANCARIA',
		"Este mensaje es automatico y corresponde a una transferencia bancaria generada por $Nusuario el dia $Ahora desde la ip: $Ip ",
		"$DESTINO_PLANO1,transferencia_bancolombia.txt");fclose($DD1);
		
		
		echo "
		  <br><br>
		   <iframe name='o1' style='visibility:hidden' height='1'></iframe>
		   <input type='button' value='CERRAR ESTA VENTANA' onclick='window.close();void(null)'>
		   <script language='javascript'>bajar_archivos();</script>
		  </body>";
	}
}

function prepara_valor($Valor=0,$Enteros=13,$Decimales=0)
{
		$Entero=intval($Valor);
		$Decimal=round($Valor-$Entero,$Decimales);
		$Cadena1=strval($Entero);
		$Resultado=str_pad($Cadena1,$Enteros,'0',STR_PAD_LEFT);
		if($Decimales)
		{
			$Cadena2=strval($Decimal);
			$Resultado.=str_pad(substr($Cadena2,strpos($Cadena2,'.')+1,$Decimales),$Decimales,'0',STR_PAD_RIGHT);
		}
		return $Resultado;
}

function inscribir_cuentas()
{
  global $id,$Inscripcion;  // id es el registro de pago
  html();
  $Fin_de_linea="\r\n";
  $Pago=qo("select * from pago where id=$id");
	if($Detalle=q("select proveedor,t_proveedor(proveedor) as nproveedor,sum(valor) as valor from dpago where pago=$id group by proveedor order by proveedor"))
	{
		$DESTINO_PLANO1 = 'planos/int1_' . $_SESSION['User'] . '_' . $_SESSION['Id_alterno'] . '.txt'; // plano para el documento
		echo "
      <script language='javascript'>
              function bajar_archivos()
              {
                  window.open('marcoindex.php?Acc=bajar_archivo&Archivo=$DESTINO_PLANO1&Salida=t.txt','o1');
              }
      </script>
      <body>";
		$Total_lineas='';
		while($D=mysql_fetch_object($Detalle))
		{
			$P=qo("select * from proveedor where id=$D->proveedor");
			if($P->td=='NIT') $TDI='3';
			elseif($P->td=='CC') $TDI='1';
			elseif($P->td=='RUT') $TDI='3';
			$Linea=$P->numero_cuenta.',7,"'.$P->nombre.'","'.str_pad(qo1("select codigo from codigo_ach where id=$P->banco"),9,'0',STR_PAD_LEFT).'",';
			$Linea.=$P->identificacion.',"'.$TDI.'","Si","'.$P->direccion.','.qo1("Select nombre from ciudad where codigo='$P->ciudad'").'","';
			$Linea.=$P->email.'",'.$P->telefono1.','.$P->telefono2.',000';
			$Linea.=$Fin_de_linea;
			$Total_lineas.=$Linea;
		}
		 if(@is_file($DESTINO_PLANO1)) @unlink($DESTINO_PLANO1);
		$DD1 = fopen($DESTINO_PLANO1, 'w+');
		fwrite($DD1, $Total_lineas);
		fclose($DD1);
		echo "
      <br><br>
       <iframe name='o1' style='visibility:hidden' height='1'></iframe>
       <input type='button' value='CERRAR ESTA VENTANA' onclick='window.close();void(null)'>
       <script language='javascript'>bajar_archivos();</script>
      </body>";
	}
}

function reversar_no_abonados()
{
	global $id;
	$Contador=0;
	$Pago=qo("select * from pago where id=$id");
	if($Pago->greversado==0)
	{
		if($Detalle=q("select * from dpago where pago=$id and no_abonado=1"))
		{
			while($D=mysql_fetch_object($Detalle))
			{
				q("update factura_ap set girado=0 where id=$D->aprobacion");
				$Contador++;
			}
		}
		echo "<script language='javascript'>
		function carga()
		{
			alert('$Contador registros reversados');
			window.close();void(null);
		}
		</script>
		<body onload='carga()'></body></html>";
	}
	else
	{
		echo "<script language='javascript'>
		function carga()
		{
			alert('Los registros de este pago ya fueron reversados');
			window.close();void(null);
		}
		</script>
		<body onload='carga()'></body></html>";
	}
}

function email_a_abonados()
{
	global $id,$Nusuario;
	$Email_usuario=usuario('email');
	$Contador=0;
	$Pago=qo("select * from pago where id=$id");
	if($Proveedores=q("select distinct proveedor from dpago where pago=$id and abonado=1"))
	{
		while($Prov=mysql_fetch_object($Proveedores))
		{
			$Proveedor=qo("select * from proveedor where id=$Prov->proveedor");
			if($Proveedor->email_tesoreria_e)
			{
				echo "<br />Enviando a $Proveedor->nombre $Proveedor->email_tesoreria_e";
				$Mensaje="<body>Estimado(s) Señor(es) $Proveedor->nombre:<br><br>Por medio del presente informamos sobre los pagos hechos el día $Pago->fecha mediante transferencia electrónica. ".
				"A continuación se relaciona las facturas canceladas:<br><br>";
				$Suma=0;
				if($Detalle=q("select *,t_factura(factura) as nfactura from dpago where pago=$id and proveedor=$Prov->proveedor and abonado=1"))
				{
					$Mensaje.="<table border cellspacing='0'><th>Factura Número</th><th>Valor</th></tr>";
					while($Det=mysql_fetch_object($Detalle))
					{
						$Mensaje.="<tr><td>$Det->nfactura</td><td align='right'>".coma_format($Det->valor)."</td></tr>";
						$Suma+=$Det->valor;
					}
					$Mensaje.="<tr><td><b>Total Pagado</b></td><td align='right'>".coma_format($Suma)."</td></tr></table>";
				}
				$Mensaje.="<br><br>Cordialmente,<br><br>Dirección Administrativa<br><br>AOA S.A.<br>Carrera 69B No. 98A-10 Morato<br>PBX +571 7560510<br>Bogota D.C., Colombia<br>www.aoacolombia.com<br><br>";
				$Mensaje.="<img src='http://app.aoacolombia.com/img/AOAlogo.jpg' title='AOA COLOMBIA S.A. SE MUEVE CONTIGO'/><br>";
				$Mensaje.="<p style='font-size:9px'>Este mensaje es confidencial, está amparado por secreto profesional y no puede ser usado ni divulgado por personas distintas de su(s) destinatario(s). Si no es el receptor autorizado, cualquier retención, difusión, distribución o copia de este mensaje es prohibida y será sancionada por la ley. Si por error recibe este mensaje, favor reenviarlo al remitente y borrar el mensaje recibido.</p>";
				$Mensaje.="<p style='font-size:9px'>This messajge is confidential, subject to professional secret and may not be used or disclosed by any person other than its addressee(s). If you are not the addressee(s), any retention, dissemination, distribution or copying of this message is strictly prohibited and sanctioned by law. If you receive this message in error, please send it back and delete the message received.<br>";
				$Mensaje.="</BODY>";

				$Envio=enviar_gmail($Email_usuario /*de */,
												$Nusuario /*Nombre de */ ,
												"$Proveedor->email_tesoreria_e,$Proveedor->nombre;claudiacastro@aoacolombia.com,CLAUDIA CASTRO" /*para */,
												"$Email_usuario,$Nusuario" /*con copia*/,
												"Transferencia Electronica AOA" /*Objeto */,
												$Mensaje);
				echo "<br>$Envio $Proveedor->email_tesoreria_e";
			}
			else
			{
				echo "<br />El proveedor $Proveedor->nombre no tiene correo";
			}
		}
	}
	else
	{
		echo "<br />No se encuentra el detalle";
	}
}








?>