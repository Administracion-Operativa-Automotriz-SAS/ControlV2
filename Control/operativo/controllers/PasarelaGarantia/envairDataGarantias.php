<?php



//error_reporting(-1);


// Motrar todos los errores de PHP
//error_reporting(E_ALL);

// Motrar todos los errores de PHP
ini_set('error_reporting', E_ALL);
header('Access-Control-Allow-Origin: *');
	
include("Requests-master/library/Requests.php");


include("DbConnect.php"); 


include("../inc/funciones_.php");
include('../inc/pdf/fpdf.php'); 

//sesion();

$request_body = file_get_contents('php://input');



if($request_body)
	{
		   //print_r($request_body);
			$request = json_decode($request_body);
			//echo "make print";
			
			if(isset($request->apikeyaoa)){
				if($request->apikeyaoa !='yNP3sfONgZoGmH$7'){
					echo json_encode("No tiene acceso");
				    exit;
				}else{
					
			if(isset($request->acc)){
				$Wservices = new EnvioGarantias($request);			
				
				$acc = $request->acc; 
				
				if(method_exists($Wservices,$acc))
				{
					$Wservices->$acc($request);	
				}
				else{
					echo json_encode(array("desc"=>"Metodo no existe: ".$acc));
					
						}
				
					}
				}
			
			}else{
				echo json_encode("No tiene acceso");
				exit;
			}
			
	}
	

	
	
	
class EnvioGarantias{
	
	function  __construct($request){
		$this->request = $request;
		$this->connect = new DbConnect();
		$this->Nusuario = "Sistema de pago online";
		
	}
	
    public function sendGarantia(){
		 echo json_encode(array("status"=>"NO","viewFactura"=>"Hubo un error"));
	}
  
  

	
}
?>