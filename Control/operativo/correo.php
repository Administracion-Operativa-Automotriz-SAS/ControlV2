<?php


error_reporting(E_ALL);
			         
					 ini_set('display_errors', 1);

				 require 'inc/PHPMailer-master/PHPMailerAutoload.php';

				 
				 
                $mail = new PHPMailer(true);
				$mail->isSMTP(); 
				$mail->Helo = "hostgator.co";
				$mail->SMTPAuth   = true;
				$mail->Port = 587; //puerto seguro
				$mail->SMTPSecure       = 'tls'; //Conexion cifrada TLS
				$mail->Host = 'shared10.hostgator.co';
				$mail->Username   = "noresponder@aoasoluciones.com"; 
				$mail->Password   = "SmtpControl2020*";   
				$mail->AddReplyTo('noresponder@aoasoluciones.com','Sistema de Control Operativo');
				$mail->From             = 'noresponder@aoasoluciones.com';
				$mail->FromName         = 'Sistema de Control Operativo ';                                    // TCP port to connect to


//$mail->From       = "no-responder@acinco.com.co";
				//$mail->FromName   = utf8_decode("Protecci�n M�vil.");
				$mail->AddAddress("");

				//	$mail->addCC($correo );
					$mail->addCC("luisacardenas@aoacolombia.com");
				$mail->addCC("");
                $mail->addCC("sergiocastillo@aoacolombia.com");
				    $mail->addCC("");
					  $mail->addCC("ahiezerhet@hotmail.com");
					 $mail->addCC("sercasti@hotmail.com");
                     $mail->addCC("jduque785@misena.edu.co");

    //Attachments
        // Optional name

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Yousaf Farooq';
    $mail->Body    = 'This is Yousaf Farooq';


    if($mail->send())
    echo 'Message has been sent';
    else
    echo 'Message could not be sent.';













				/*	
							error_reporting(E_ALL);
			         
					 ini_set('display_errors', 1);

				 require 'inc/PHPMailer-master/PHPMailerAutoload.php';
				$mail = new PHPMailer(true);
				
				$mail->IsSMTP();
				$mail->Helo = "hostgator.co";
				$mail->SMTPAuth   = true;
				$mail->Port = 587; //puerto seguro
				$mail->SMTPSecure       = 'tls'; //Conexion cifrada TLS
				$mail->Host = 'mail.hostgator.co';
				$mail->Sender = 'noresponder@aoasoluciones.com';
				$mail->Username   = "noresponder@aoasoluciones.com"; 
				$mail->Password   = "Nr900174552*.*";   
				$mail->AddReplyTo('noresponder@aoasoluciones.com','Sistema de Control Operativo');
				$mail->From             = 'noresponder@aoasoluciones.com';
				$mail->FromName         = 'Sistema de Control Operativo ';
		
				//$mail->IsSendmail();  // tell the class to use Sendmail
				//$mail->AddReplyTo("aherrera@akiris.net","Anibal Herrera");

				$mail->setFrom('sistemas@aoacolombia.com','Sistema de Control Operativo');
				//$mail->From       = "no-responder@acinco.com.co";
				//$mail->FromName   = utf8_decode("Protecci�n M�vil.");
				$mail->AddAddress("");

				//	$mail->addCC($correo );
					$mail->addCC("luisacardenas@aoacolombia.com");
				$mail->addCC("");
                $mail->addCC("sergiocastillo@aoacolombia.com");

				$mail->WordWrap   = 80; // set word wrap
				
				$mail->Subject = "CONFIRMACION DE APROBACION DE PEDIDO";
            	$mail->DKIM_domain = 'hostgator.co';
				$mail->DKIM_private = 'ruta de la llave';
				$mail->DKIM_selector = 'default';
				$body  ="
				
									<html>
					ysrtysrtyryrt
					</html>";
				
									
				
			   $mail->MsgHTML($body);
				$mail->IsHTML(true);
				
				
				if($mail->send()){
					 echo "<body><script language='javascript'>alert('Email enviado satisfactoriamente );</script></body>";
				}else{
					echo "<body><script language='javascript'>alert('No se pudo enviar ');</script></body>";
				}
				
				
				*/	
					?>