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




class tokenAllianz
{
	
	function __construct($factura,$ext_table="")
	{
		$this->connect = new DbConnect();
	}
	
	public function buscar_tokenAllianz(){
	
	
					$token = $_POST['token'];
					 
					$sql = "SELECT * FROM aoacol_aoacars.usuario_callallianz WHERE usuario_callallianz.token = $token";
					
					$tokenB = qo($sql);	
					echo json_encode($tokenB);				
											 
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


$sin = new tokenAllianz();


$sin->buscar_tokenAllianz();
		  
		 
		 

?>