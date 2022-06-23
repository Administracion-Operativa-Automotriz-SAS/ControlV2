<?php
//phpinfo();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require 'PHPMailer-master/PHPMailerAutoload.php';

$mail = new PHPMailer;

$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();         

$mail->SMTPAuth   = true;                           // enable SMTP authentication
	    

// Set mailer to use SMTP
$mail->Host = 'shared10.hostgator.co';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'noresponder@aoasoluciones.com';                 // SMTP username
$mail->Password = 'SmtpControl2020*';                           // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to

$mail->setFrom('noresponder@aoasoluciones', 'No responder');
$mail->addAddress('sergiocastillo@aoacolombia.com', 'Joe User');     // Add a recipient
$mail->addAddress('sergiourbina@aoacolombia.com');               // Name is optional
//$mail->addReplyTo('info@example.com', 'Information');
$mail->addCC('davidduque@aoacolombia.com');
//$mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
/*
$mail = new PHPMailer\PHPMailer\PHPMailer();

try {
    //Server settings
    $mail->IsSMTP();
	$mail->Helo = "hostgator.co";                                        // Send using SMTP
    $mail->Host = 'shared10.hostgator.co';
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'noresponder@aoasoluciones.com';                     // SMTP username
    $mail->Password   = 'Nr900174552*.*';                               // SMTP password
    $mail->SMTPSecure = 'tls';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('sergiourbina@aoacolombia.com', 'Sergio Urbina');
    $mail->addAddress('sergiourbina@aoacolombia.com', 'Stiven Bayona');     // Add a recipient
    //$mail->addAddress('ellen@example.com');               // Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    // Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'Hola correo de prueba!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'El mensaje se envio con exito';
} catch (Exception $e) {
    echo "Ubo un error: {$mail->ErrorInfo}";
}
*/


?>
  
  
  
  
  
  
