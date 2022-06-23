<?php
// Motrar todos los errores de PHP
//error_reporting(-1);


// Motrar todos los errores de PHP
//error_reporting(E_ALL);

// Motrar todos los errores de PHP
error_reporting(E_ALL);
header('Access-Control-Allow-Origin: *');
	
include("Requests-master/library/Requests.php");


include("DbConnect.php"); 


include("../inc/funciones_.php");




class siniestroMapref
{
	
	function __construct($factura,$ext_table="")
	{
		$this->connect = new DbConnect();
	}
	
	public function registrarSiniestroMapref(){
	
	
	
	Requests::register_autoloader();
    
	$data_to_send = array(
          "cod_usr" => "AOA_COL",
		  "password"=> "H2FEp.1078718871",
		  "tip_docum"=> "NT",
		  "cod_docum"=> "900174552",
		  "email"=> "mailto:sergiocastillo@aoacolombia.com",
		  "mobile"=> "573043637333");
		
		
		$headers = array(
		"content-type" => "application/json;charset=ISO-8859-1");
		
		$options = array('timeout' => 200,
		                 );
		
		$requestEmissionToken = Requests::post('https://cotiza.mapfre.com.co/ofvservice/temp_token.jsp',$headers,json_encode($data_to_send),$options);
		
		sleep(10);
        

		$response =  $requestEmissionToken->body;
		  
		 $resDeco =  json_decode($response);
		 
		 $claveMd5 = urlencode($resDeco->claveMd5); 
		 
		 
		 
		 
		 
		 
		 
		 $hoy = date("d/m/Y");
		 
		 $data_to_sendNewData = array(
          "P_COD_CIA" => 1,
		  "P_FEC_DENUN_SINI" =>  $hoy);
         
		 
		 $url = "https://cotiza.mapfre.com.co/restVarious/api?alias=aoa_consulta&tk=$claveMd5";
		 
		 
		 $requestConsulta = Requests::post($url,$headers,json_encode($data_to_sendNewData),$options);
		 sleep(10);
		 $body = $requestConsulta->body;
		 
		 $sin = new siniestroMapref();
		 
		 $utfJson = $sin->utf8ize($body);
		 echo $utfJson;
		 
		 exit();
		 
		
		$array = json_decode($utfJson);
		
		
}
	public function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } else if (is_string ($mixed)) {
        return utf8_encode($mixed);
    } else if (is_object($mixed)) {
        $a = (array)$mixed; // from object to array
        return utf8ize($a);
    }
    return $mixed;
    }
}


$sin = new siniestroMapref();


$sin->registrarSiniestroMapref();
		  
		 
		 

?>