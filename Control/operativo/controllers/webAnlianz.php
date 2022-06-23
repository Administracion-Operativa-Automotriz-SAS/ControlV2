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




class siniestroAllianz
{
	
	function __construct($factura,$ext_table="")
	{
		$this->connect = new DbConnect();
	}
	
	public function registrarSiniestroAlllianz(){
	
	
					$aseguradora = $_POST['aseguradora'];
					 
					$numeroSiniestro = $_POST['numero_siniestro'];
					
					$placa = $_POST['placa']; 
					
					$usuario = $_POST['usuario']; 

                    $ip = $_POST['ip'];     
					
					$banco = $_POST['banco'];  

					$asegurado_nombre = $_POST['nombre_tomador'];
					
					$asegurado_id = $_POST['id_tomador'];
					
					$ciudad_atencion = $_POST['ciudad_atencion'];
					
					$declarante_nombre = $_POST['nombre_declarante'];
					
					$declarante_id = $_POST['id_declarante'];
					
					$declarante_email  = $_POST['email'];
					
					$dias_servicio  = $_POST['dias'];
					
					$valor = $_POST['valor'];

					
					$perdida_total = $_POST['perdida_total'];
					
					$celular = $_POST['celular'];
				
					$telefono  = $_POST['telefono']; 
					
					$marca = $_POST['marca'];
				
					$linea  = $_POST['linea']; 
					
					$tipo = $_POST['tipo']; 
					
					$clase = $_POST['clase']; 
					
					$ciudad_siniestro = $_POST['ciudad_siniestro']; 
					
					//$ciudad_original = $val[$i][19];
					
					$Departamento = str_pad($ciudad_siniestro."000",0,2);
					
					$codCiudad = str_pad($ciudad_siniestro."000",8,'0',STR_PAD_LEFT);
					
					$sql = "select oficina from aoacol_aoacars.corresp_ofic where left(departamento,2)='$Departamento' ";
					
					$alreadyOfi = $this->connect->query($sql); 
					
					$Depa = $this->connect->convert_object($alreadyOfi);	
					
					if($Depa->oficina){
						$ciudad = $Depa->oficina;
					}else{
						$ciudad = $codCiudad;
					}
					
					$ingreso = date("Y-m-d H:i:s");
					$fecha = date("Y-m-d");
					$hora = date("H:i:s");
					$arraySiniestro = array("asegurado_id" => $asegurado_id,
											"asegurado_nombre" => $asegurado_nombre,
											"declarante_nombre" => $declarante_nombre,
											"dias_servicio" => $dias_servicio,
										    "declarante_telefono" => $telefono,
											"declarante_celular" => $celular,
											"numero" => $numeroSiniestro,
											"perdida_total" => $perdida_total,
											"declarante_id" => $declarante_id,
											"declarante_email" => $declarante_email,
											"marca" => $marca,
											"placa" => $placa,
                                            "estado" => 5,
											"linea" => $linea,
											"ciudad" => $ciudad_atencion,
                                            "ciudad_original" => $ciudad_siniestro,
                                            "ciudad_siniestro" => $ciudad_siniestro,
											"clase" => $clase,
											"tipo_caja" => $tipo,
											"aseguradora" => $aseguradora,
											'valoraseg'=> $valor,
                                            "servicio" => $tipo,
											'fec_autorizacion'=> $ingreso,
                                            'fec_siniestro'=> $ingreso,
                                            'fec_declaracion'=> $ingreso,
                                            'ingreso'=> $ingreso,
                                            'observaciones'=> $fecha.$hora.$usuario.$ip,
                                            
											);
											
											$sql = $this->connect->insert("siniestro",$arraySiniestro);
					                        $sqlfinal = q($sql);
											
											
											
											 if($banco === '1'){					
										  
												   $sql_inse = "insert into placa_especial (aseguradora,placa,descripcion,condicion )
																			   value ('$aseguradora','$placa','Bancolombia '  , 'CLIENTE ESPECIAL ALLIANZ'); ";
												 $placa_especial = q($sql_inse);
										 
														
											 }	

											 
											 echo $sqlfinal  ;
											 
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


$sin = new siniestroAllianz();


$sin->registrarSiniestroAlllianz();
		  
		 
		 

?>