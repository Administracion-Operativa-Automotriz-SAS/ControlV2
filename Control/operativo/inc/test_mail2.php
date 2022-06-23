<?php

//return print_r($_POST);

require 'PHPMailer-master/PHPMailerAutoload.php';

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               

$mail->isSMTP();                                      
$mail->Host = 'correo.aoacolombia.com';  
$mail->SMTPAuth = true;                               
$mail->Username = 'jesusvega@aoacolombia.com';                 
$mail->Password = 'SumaikunCorreo1';                          
$mail->SMTPSecure = 'tls';                            
$mail->Port = 25;                                    

$mail->setFrom('jesusvega@aoacolombia.com');
$mail->addAddress('sergio.castillo@helpnow.com.co');     

$mail->isHTML(true);                                  

$mail->Subject = "Test mail 2";
$mail->Body    = "Body test for mail with zimbra server part 2";

$mail->AltBody = "Alt body XD";

$mail->XMailer = 'AOA Mailer';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}

exit;
