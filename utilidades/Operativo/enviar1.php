					<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);





require 'PHPMailer-5.2.16/PHPMailerAutoload.php';


$body ='DFJSDF';

                            // Enable verbose debug output

				$mail = new PHPMailer;
                    $mail->CharSet = 'UTF-8';   
					$mail->isSMTP();
					$mail->SMTPDebug = 2;
					$mail->Debugoutput = 'html';
					$mail->Host = 'shared10.hostgator.co';
					$mail->Port = 587;
					$mail->SMTPAuth = true;
				$mail->CharSet = 'UTF-8';
				$mail->Username   = "noresponder@aoasoluciones.com"; 
				$mail->Password   = "SmtpControl2020*";   
			                          // TCP port to connect to
            $mail->setFrom('noresponder@aoasoluciones.com', 'Sistema de Control Operativo ');
				  				  $mail->AddReplyTo('noresponder@aoasoluciones.com', 'Prueba PHPMailer');
				  					$mail->AddAddress("davidduque@aoacolombia.com");
                $mail->addCC("sergiocastillo@aoacolombia.com");
	            $mail->addCC("jduque785@misena.edu.co");
				 $mail->addCC("sergiocastillo@aoacolombia.com");
				    $mail->addCC("davidduque@aoacolombia.com");
					  $mail->addCC("ahiezerhet@hotmail.com");
					 $mail->addCC("sercasti@hotmail.com");
                     $mail->addCC("jduque785@misena.edu.co");
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

				
				
				if (!$mail->send()) {
					echo $mail->ErrorInfo;
				} else {
					echo "Message sent!";
				}
				
?>				
					
					
					
				
				
