<?php
			$Det="<table border cellspacing='0'><tr><th>Tipo de Requisicion</th><th>Item</th><th>Unidad de medida</th><th>Descripcion</th><th>Cantidad</th><th>Valor unitario</th><th>Valor</th>";
	        
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
			
			$ER=qo("select requisicion.placa,
     	concat( oficina.centro_operacion,' ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto,aseguradora.nombre as ASEGURADORA, ubicacion.flota 
				 from aoacol_administra.requisiciond 
				 LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
				 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 inner JOIN aoacol_aoacars.oficina on ubicacion.oficina = oficina.id
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion.id = $id");
			
			
			  q("update $BDA.requisicion set cerrada=1,estado=1 where id=$id");
        
				$verificar=qo("select ubicacion from requisicion where id = $id");
				
				if($verificar->ubicacion == 0){
					include('pdfrequisicionAdm.php');
				}else{
					include('pdfrequisicionOpe.php');
				}
			

			 
				 $correo = "davidduque@aoacolombia.com";


				
				$attachment= $pdf->Output('certificadoIndividual.pdf', 'S');
				//require("inc/PHPMailer-master/PHPMailerAutoload.php");
				$mail = new PHPMailer;
				$mail->IsSMTP();                                    // tell the class to use SMTP
				$mail->SMTPAuth   = true;                           // enable SMTP authentication
				$mail->Port       = 25;                             // set the SMTP server port
				$mail->Host       = "mail.aoasoluciones.com";           // SMTP server
				$mail->Username   = "contacto@aoasoluciones.com";  // SMTP server username
				$mail->Password   = "CorreoAoa2019*.*";            // SMTP server password
				$mail->SMTPSecure = 'tls';
				
				
				

				//$mail->IsSendmail();  // tell the class to use Sendmail
				//$mail->AddReplyTo("aherrera@akiris.net","Anibal Herrera");

				$mail->setFrom('sistemas@aoacolombia.com','Sistema de Control Operativo');
				//$mail->From       = "no-responder@acinco.com.co";
				//$mail->FromName   = utf8_decode("Protección Móvil.");
				$mail->AddAddress($correo);

					$mail->addCC("davidduque@aoacolombia.com");
				$mail->addCC('sergiocastillo@aoacolombia.com');
				$mail->addCC("sergiourbina@aoacolombia.com");
				

				$mail->AddStringAttachment($attachment, 'certificadoIndividual.pdf');
				$mail->WordWrap   = 80; // set word wrap
				
				$mail->Subject = "CONFIRMACION DE APROBACION DE PEDIDO $id";

				
				
				
				$body  ="
				
				
				 
				
				<html>
			  <head>
				<meta name='viewport' content='width=device-width, initial-scale=1.0' />
				<meta name='x-apple-disable-message-reformatting' />
				<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
				<title></title>
				<style type='text/css' rel='stylesheet' media='all'>
				/* reset */

			*
			{
				border: 0;
				box-sizing: content-box;
				color: inherit;
				font-family: inherit;
				font-size: inherit;
				font-style: inherit;
				font-weight: inherit;
				line-height: inherit;
				list-style: none;
				margin: 0;
				padding: 0;
				text-decoration: none;
				vertical-align: top;
			}

			/* content editable */

			*[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

			*[contenteditable] { cursor: pointer; }

			*[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 1em 0.5em #DEF; }

			span[contenteditable] { display: inline-block; }

			/* heading */

			h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }

			/* table */

			table { font-size: 75%; table-layout: fixed; width: 100%; }
			table { border-collapse: separate; border-spacing: 2px; }
			th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
			th, td { border-radius: 0.25em; border-style: solid; }
			th { background: #EEE; border-color: #BBB; }
			td { border-color: #DDD; }

			/* page */

			html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; padding: 0.5in; }
			html { background: #fff; cursor: default; }

			body { box-sizing: border-box; height:nome; margin: 0 auto; overflow: hidden; padding: 0.5in; width:nome; }
			body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }

			/* header */

			header { margin: 0 0 3em; }
			header:after { clear: both; content: ''; display: table; }

			header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
			header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
			header address p { margin: 0 0 0.25em; }
			header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
			header img { max-height: 100%; max-width: 100%; }
			header input { cursor: pointer; -ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity=0)'; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

			/* article */

			article, article address, table.meta, table.inventory { margin: 0 0 3em; }
			article:after { clear: both; content: ''; display: table; }
			article h1 { clip: rect(0 0 0 0); position: absolute; }

			article address { float: left; font-size: 125%; font-weight: bold; }

			/* table meta & balance */

			table.meta, table.balance { float: right; width: 36%; }
			table.meta:after, table.balance:after { clear: both; content: ''; display: table; }

			/* table meta */

			table.meta th { border: 1px solid black; border-color: rgba(118,136,29,1);width: 40%; }
			table.meta td {border: 1px solid black; border-color: rgba(118,136,29,1); width: 60%; }

			/* table items */
			.titulo{
			font-size:15px;
			font-weight:900;
			color:rgba(118,136,29,1);
			}
			table.inventory { clear: both; width: 100%;border: 1px solid black; border-color: rgba(118,136,29,1); }
			table.inventory th {  background-color:rgba(118,136,29,1); font-weight: bold; text-align: center; color:#fff; }

			table.inventory td:nth-child(1) { width: 26%; }
			table.inventory td:nth-child(2) { width: 38%; }
			table.inventory td:nth-child(3) { text-align: right; width: 12%; }
			table.inventory td:nth-child(4) { text-align: right; width: 12%; }
			table.inventory td:nth-child(5) { text-align: right; width: 12%; }

			/* table balance */

			table.balance th, table.balance td { width: 50%; }
			table.balance td { text-align: right; }

			/* aside */

			aside h1 { border: none; border-width: 0 0 1px; margin: 0 0 1em; }
			aside h1 { border-color: #999; border-bottom-style: solid; }

			/* javascript */

			.add, .cut
			{
				border-width: 1px;
				display: block;
				font-size: .8rem;
				padding: 0.25em 0.5em;	
				float: left;
				text-align: center;
				width: 0.6em;
			}
				p.sub {
				  font-size: 13px;
				}
				/* Utilities ------------------------------ */
				
				.align-right {
				  text-align: right;
				}
				
				.align-left {
				  text-align: left;
				}
				
				.align-center {
				  text-align: center;
				}
				/* Buttons ------------------------------ */
				
				.button {
				  background-color: #3869D4;
				  border-top: 10px solid #3869D4;
				  border-right: 18px solid #3869D4;
				  border-bottom: 10px solid #3869D4;
				  border-left: 18px solid #3869D4;
				  display: inline-block;
				  color: #FFF;
				  text-decoration: none;
				  border-radius: 3px;
				  box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
				  -webkit-text-size-adjust: none;
				  box-sizing: border-box;
				}
				
				.button--green {
				  background-color: #22BC66;
				  border-top: 10px solid #22BC66;
				  border-right: 18px solid #22BC66;
				  border-bottom: 10px solid #22BC66;
				  border-left: 18px solid #22BC66;
				}
				
				.button--red {
				  background-color: #FF6136;
				  border-top: 10px solid #FF6136;
				  border-right: 18px solid #FF6136;
				  border-bottom: 10px solid #FF6136;
				  border-left: 18px solid #FF6136;
				}
				
				@media only screen and (max-width: 500px) {
				  .button {
					width: 100% !important;
					text-align: center !important;
				  }
				}
				/* Attribute list ------------------------------ */
				
				.attributes {
				  margin: 0 0 21px;
				}
				
				.attributes_content {
				  background-color: #F4F4F7;
				  padding: 16px;
				}
				
				.attributes_item {
				  padding: 0;
				}
				/* Related Items ------------------------------ */
				
				.related {
				  width: 100%;
				  margin: 0;
				  padding: 25px 0 0 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				}
				
				.related_item {
				  padding: 10px 0;
				  color: #CBCCCF;
				  font-size: 15px;
				  line-height: 18px;
				}
				
				.related_item-title {
				  display: block;
				  margin: .5em 0 0;
				}
				
				.related_item-thumb {
				  display: block;
				  padding-bottom: 10px;
				}
				
				.related_heading {
				  border-top: 1px solid #CBCCCF;
				  text-align: center;
				  padding: 25px 0 10px;
				}
				/* Discount Code ------------------------------ */
				
				.discount {
				  width: 100%;
				  margin: 0;
				  padding: 24px;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  background-color: #F4F4F7;
				  border: 2px dashed #CBCCCF;
				}
				
				.discount_heading {
				  text-align: center;
				}
				
				.discount_body {
				  text-align: center;
				  font-size: 15px;
				}
				/* Social Icons ------------------------------ */
				
				.social {
				  width: auto;
				}
				
				.social td {
				  padding: 0;
				  width: auto;
				}
				
				.social_icon {
				  height: 20px;
				  margin: 0 8px 10px 8px;
				  padding: 0;
				}
				/* Data table ------------------------------ */
				
				.purchase {
				  width: 100%;
				  margin: 0;
				  padding: 35px 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				}
				
				.purchase_content {
				  width: 100%;
				  margin: 0;
				  padding: 25px 0 0 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				}
				
				.purchase_item {
				  padding: 10px 0;
				  color: #51545E;
				  font-size: 15px;
				  line-height: 18px;
				}
				
				.purchase_heading {
				  padding-bottom: 8px;
				  border-bottom: 1px solid #EAEAEC;
				}

				.purchase_heading p {
				  margin: 0;
				  color: #85878E;
				  font-size: 12px;
				}
				
				.purchase_footer {
				  padding-top: 15px;
				  border-top: 1px solid #EAEAEC;
				}
				
				.purchase_total {
				  margin: 0;
				  text-align: right;
				  font-weight: bold;
				  color: #333333;
				}
				
				.purchase_total--label {
				  padding: 0 15px 0 0;
				}
				
				body {
				  background-color: #F4F4F7;
				  color: #51545E;
				}
				
				p {
				  color: #51545E;
				}
				
				p.sub {
				  color: #6B6E76;
				}
				
				.email-wrapper {
				  width: 100%;
				  margin: 0;
				  padding: 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  background-color: #F4F4F7;
				}
				
				.email-content {
				  width: 100%;
				  margin: 0;
				  padding: 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				}
				/* Masthead ----------------------- */
				
				.email-masthead {
				  padding: 25px 0;
				  text-align: center;
				}
				
				.email-masthead_logo {
				  width: 94px;
				}
				
				.email-masthead_name {
				  font-size: 16px;
				  font-weight: bold;
				  color: #A8AAAF;
				  text-decoration: none;
				  text-shadow: 0 1px 0 white;
				}
				/* Body ------------------------------ */
				
				.email-body {
				  width: 100%;
				  margin: 0;
				  padding: 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  background-color: #FFFFFF;
				}
				
				.email-body_inner {
				  width: 570px;
				  margin: 0 auto;
				  padding: 0;
				  -premailer-width: 570px;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  background-color: #FFFFFF;
				}
				
				.email-footer {
				  width: nome;
				  margin: 0 auto;
				  padding: 0;
				  -premailer-width: 570px;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  text-align: center;
				}
				
				.email-footer p {
				  color: #6B6E76;
				}
				
				.body-action {
				  width: 100%;
				  margin: 30px auto;
				  padding: 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  text-align: center;
				}
				
				.body-sub {
				  margin-top: 25px;
				  padding-top: 25px;
				  border-top: 1px solid #EAEAEC;
				}
				
				.content-cell {
				  padding: 35px;
				}
			.add, .cut
			{
				background: #9AF;
				box-shadow: 0 1px 2px rgba(0,0,0,0.2);
				background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
				background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
				border-radius: 0.5em;
				border-color: #0076A3;
				color: #FFF;
				cursor: pointer;
				font-weight: bold;
				text-shadow: 0 -1px 2px rgba(0,0,0,0.333);
			}
			input[type=text]:focus {
			  border: 3px solid #555;
			}
			.add { margin: -2.5em 0 0; }

			.add:hover { background: #00ADEE; }

			.cut { opacity: 0; position: absolute; top: 0; left: -1.5em; }
			.cut { -webkit-transition: opacity 100ms ease-in; }

			tr:hover .cut { opacity: 1; }

			@media print {
				* { -webkit-print-color-adjust: exact; }
				html { background: none; padding: 0; }
				body { box-shadow: none; margin: 0; }
				span:empty { display: none; }
				.add, .cut { display: none; }
			}

			@page { margin: 0; }
				</style>
				<!--[if mso]>
				<style type='text/css'>
				  .f-fallback  {
					font-family: Arial, sans-serif;
				  }
				</style>
			  <![endif]-->
			  </head>

					<header>
					   <table  style='margin-bottom: 2%;   text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
							  <tr>
								<td    style=' text-align: center;'  class='content-cell' align='center' >
									   <img   style=' text-align: center;'  src='https://app.aoacolombia.com/Administrativo/img/banner-vehiculo-sustituto-AOA.jpg'>
								</td>
							  </tr>

							</table>
							<br>
							  <table   style='margin-bottom: 6%;  text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
							  <tr>
								<td  style='margin-bottom: 3%; '  class='content-cell' align='center' >
								   <b  style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);' class='titulo'><b>Estimado Señor $Nprov,</b></b><br> </td>
							  </tr>

							</table>
						<address  style='margin-bottom: 1%; ' contenteditable>
							<h3 class='titulo'  style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);'  >Reciba cordial saludo.</h3>
						
			
								Por medio de este correo se le notifica formalmente sobre la 
								aprobación de <b><u>Requisición Interna Número $id</u>
								</b>de la ciudad de&nbsp;<b>$ciudad</b>&nbsp;con el departamento&nbsp;<b>$departamento</b>&nbsp; 
								en nuestra empresa para adquirir los siguientes bienes/servicios:
						</address>
						
					</header>
					<br>
					<hr>
					<hr>
					<article style='margin-bottom: 3%; '>
					
					  $Det
					</article>
					
					<p style='margin-top: 3%; color: 1px solid rgba(118,136,29,1);' class='titulo' >Valor total<br></p>
					<address  style='margin-bottom: 3%; ' contenteditable>
					  $Res
					</address>	
				
					<address  style='margin-bottom: 2%; ' contenteditable>
						Agradecemos de antemano su gentil atención.
					</address>	
					
					<address  style='margin-bottom: 1%; ' contenteditable>
						Cordialmente,
					</address>	
					 
					 <address  style='margin-bottom: 5%; ' contenteditable>
						$Usuario
					</address>
													

					<aside>
						   <table class='email-footer'    align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
							  <tr>
								<td   style=' text-align: center;'  class='content-cell' align='center' >
								  <p  style=' text-align: center;'  class='f-fallback sub align-center'>
									
									018000186262
									<br> AOA COLOMBIA S.A.
									<br>+(571) 8837069
									<br>$eUsuario
									<br>http://www.aoacolombia.com
									<br><i style='font-size:8px'>Mensaje automático del sistema de Requisiciones de AOA Colombia S.A. desarrollado por Tecnologia AOA. (it@aoacolombia.co)</i>
								  </p>
								<img   style=' text-align: center;'  class='f-fallback email-masthead_name' src='https://app.aoacolombia.com/Administrativo/img/logo-footer-mail-AOA.jpg'>
								</td>
							  </tr>

							</table>
					</aside>
				</body>
			</html>";
				
									
				
			   $mail->MsgHTML($body);
				$mail->IsHTML(true);
				if($mail->send()){
					 echo "<body><script language='javascript'>alert('Email enviado satisfactoriamente a $Email_aprobador');</script></body>";
				}else{
					echo "<body><script language='javascript'>alert('No se pudo enviar a $Email_aprobador');</script></body>";
				}
?>