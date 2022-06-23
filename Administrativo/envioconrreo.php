	<?php


header('Content-Type: text/html; charset=utf-8');



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
			  


				
require("inc/smtp/mailEnviar.php");


$gmail = new Gmail('noresponder@aoasoluciones.com', 'SmtpControl2020*');
$gmail->send('sergiourbina@aoacolombia', 'subject', 'body');


/*
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'shared10.hostgator.co';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'noresponder@aoasoluciones.com';                     // SMTP username
    $mail->Password   = 'SmtpControl2020*';                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('noresponder@aoasoluciones.com', 'Sistema de Control Operativo ');
    $mail->addAddress('sergiourbina@aoacolombia');     // Add a recipient
    
    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
*/
	
?>
  
  
  
  
  
  
