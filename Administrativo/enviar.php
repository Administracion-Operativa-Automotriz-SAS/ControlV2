					<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



require_once('../utilidades/Operativo/PHPMailer-master/PHPMailerAutoload.php');




$body ='DFJSDF';
$mail = new PHPMailer();
$mail->SMTPDebug = 2;
$mail->IsSMTP();
$mail->Helo = "gmail.com";
$mail->SMTPAuth         = true;
$mail->Port             = 587; //puerto seguro
$mail->SMTPSecure       = 'tls'; //Conexion cifrada TLS
$mail->Host      =    gethostbyname('smtp.gmail.com');



$mail->Username   = "it@aoacolombia.co";
$mail->Password   = "Sarcasti54321.";
$mail->AddReplyTo('aguirrecon7@gmail.com','Duwuewe');

    $mail->setFrom('aguirrecon7@gmail.com', 'fsdfsdfsdf');          //This is the email your form sends From
               $mail->addAddress("sergiocastillo@aoacolombia.com");
	            $mail->addCC("jduque785@misena.edu.co");
				 $mail->addCC("sergiocastillo@aoacolombia.com");
				    $mail->addCC("");
					  $mail->addCC("ahiezerhet@hotmail.com");
					 $mail->addCC("sercasti@hotmail.com");
                     $mail->addCC("jduque785@misena.edu.co");

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Subject line goes here';
    $mail->Body    = 'Body text goes here';
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
  	$mail->DKIM_domain = 'gmail.com';
	$mail->DKIM_private = 'ruta de la llave';
	$mail->DKIM_selector = 'default';

    if (!$mail->send()) {
        echo $mail->ErrorInfo;
    } else {
        echo "Message sent!";
    }



      
?>				
					
					
					
				
				
