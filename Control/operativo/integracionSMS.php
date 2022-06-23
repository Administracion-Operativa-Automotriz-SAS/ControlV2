<?php 

set_time_limit(0);

ini_set('default_socket_timeout', 2600);


$usersms = "sercasti@aoacolombia.com";
$passsms = 'aoaCoLomB1a*';
//$keysms = 'D909djkjc@ak9-$kakj0';

$smsUrl = 'http://107.20.199.106/sms/1/text/query?username=' . urlencode($usersms) . '&password=' . urlencode($passsms);
$mensaje_sms = "Recuerde su cita de ENTREGA de vehiculo el dia 2020-07-23 a las 15:00 en CR 69B 98A-10 . Contamos con su puntualidad. AOA se mueve contigo.";
$sms_mensaje = "&text=" . urlencode($mensaje_sms);
$telefono_a_usar = 3123831550;
$conExtexion =  "57".$telefono_a_usar;
$phone_number = "&to=$conExtexion";







$url_sms = $smsUrl . $phone_number . $sms_mensaje;



$data = file_get_contents($url_sms);

var_dump($data);



