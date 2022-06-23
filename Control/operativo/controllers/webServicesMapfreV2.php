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
	
	
	
	
					$total  = 'PERDIDA TOTAL';
					

					$cadena_de_texto = $total;
				   $cadena_buscada   = 'TOTAL';
					
					$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);
				
					if ($posicion_coincidencia === false) {
						
						$numero_perdida = 0;
					
					}else{
						$numero_perdida = 1;
					}

  
					

					 
					$numeroSiniestro = '8021818433';
					
					$numeroPoliza = '8021818433';
					
					$vigencia_desde = '8021818433';
					
					$vigencia_hasta = '8021818433';
					
					$expediente = '8021818433';
					
					$asegurado_id =  '8021818433';
					
					$declarante_nombre = 'Juan duque';
					
					$declarante_tel_resid = '8021818433';
					
					$declarante_celular = '8021818433';
					
					$fec_siniestro = '2020-10-22';
					
					$fec_declaracion = '2020-10-22';
					
					$placa = 'DDJ995';
					
					$fasecolda = '8021818433';
					
					$modelo = '8021818433';
					
					$COD_RAMO = '8021818433';
					
					$dias_servicio = '8021818433';
					
					$ciudad_siniestro = '11001';
					
					//$ciudad_original = $val[$i][19];
					
					$Departamento = str_pad($ciudad_siniestro."000",0,2);
					
					$codCiudad = str_pad($ciudad_siniestro."000",8,'0',STR_PAD_LEFT);
					
					$sql = "select oficina from aoacol_aoacars.corresp_ofic where left(departamento,2)='$Departamento' ";
					
					$alreadyOfi = $this->connect->query($sql); $Depa = $this->connect->convert_object($alreadyOfi);	
					
					if($Depa->oficina){
						$ciudad = $Depa->oficina;
					}else{
						$ciudad = $codCiudad;
					}
					
					$COD_RAMO =132;


					
					$ingreso = date("Y-m-d H:i:s");
					
					
					$sql = "SELECT dias_servicio as dia, ubicacion, declarante_email as email,declarante_nombre as nombre  ,numero ,
					perdida_total,id,estado FROM siniestro WHERE numero = '$numeroSiniestro'";
					
					 $sql_solicitud_extra = "SELECT consecutivo FROM solicitud_extra ORDER BY id DESC LIMIT 1";
							             	$solicitud_extra = $this->connect->query($sql_solicitud_extra);
				                            $object_solicitud = $this->connect->convert_object($solicitud_extra);	
					
					$already_exist = $this->connect->query($sql);
					
					$object_already_exist = $this->connect->convert_object($already_exist);	
					
					$dia = $object_already_exist->dia; 
					$id = $object_already_exist->id;
					$email = $object_already_exist->email;
					$nombre_si = $object_already_exist->nombre; 
					$consecutivo =$object_solicitud->consecutivo;
					


					
					
					
					
					if($object_already_exist->numero){
                    
					
                    if($COD_RAMO == 132) {
					
						$placa_es = 'TOYOTA';
						
						$sql_inse = "insert into placa_especial (aseguradora,placa,descripcion,condicion,poliza )
																			   value ('4','$placa','TOYOTA '  ,

																			   'CLIENTE ESPECIAL MAPFRE','$numeroPoliza'); ";
												 $placa_especial = q($sql_inse);
												 
					 
					
					}else{
						
						
						$placa_es = '';
					
					
}

					
					
						
						$numNuevosUno =  $Cantidad_devueltosUno++;
						
						
						
							if($numero_perdida == 1){
								


							
						if(!$object_already_exist->perdida_total){

                            
							 
							$sql_solicitud =
						"SELECT * FROM solicitud_extra 
						where siniestro = $id ";
						echo $sql_solicitud;
						
						
						
						$object_solicitud_extra = qo($sql_solicitud);
						
								
								

								if($object_solicitud_extra->tipo == 'EXTENSION'){
									
									
									echo 'tiene EXTENSION del s';
									
			         exit();
					
 
								}else{
										 
                                    if($object_already_exist->estado == '3'){

										  $hoy = date('Y-m-d');
										  $fecha_proceso = date('y-m-d h-i-s');
										  $numero_dia = 15;
											$dias_disas =  $dia - $numero_dia; 
											$dias_s = abs($dias_disas); 
											
											
											$dias = $dias_s +  $dia;
											$consecutivo = $consecutivo++;
											
											
											
									  
										
										

										$sql_perdida_o = "UPDATE siniestro 
										                    SET dias_servicio='$dias',
															 perdida_total='1'
															WHERE id = '$id' ";
												
												
										 $editar_siniestro = q($sql_perdida_o);	
										
										$sql_cita_b = "SELECT  * FROM cita_servicio WHERE siniestro 
							= '$id' and estado = 'C'";
								$sql_cita_bu = $this->connect->query($sql_cita_b);
				            	$sql_cita_buscar = $this->connect->convert_object($sql_cita_bu);	
		
								$fecha_cita =  $sql_cita_buscar->fec_devolucion;
								
							
	
										$Date = $fecha_cita;

              
										$fec_de = date('Y-m-d', strtotime($Date. ' + '.$dias_s.' days'));
										$sql_cita = "UPDATE cita_servicio 
										                   SET 
															 
															  dias_servicio='$dias'
															WHERE siniestro = '$id' and  estado = 'P' ";
															$cita_r = q($sql_cita);
															 
															
									     
										     
															 
											

                                             echo email;

															 
										 $data_mail = array(
										"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
										"enviarEmail" => "true",
										"id" => $id,
										"nombre" => $nombre_si,
										"placa" => $placa_es,
										"fecha" => $fec_de,
										 "para" =>  $email,
										"copia" =>  '',
										"contenido" => 305
										);

										$ch = curl_init();
										curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
										curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/ServiEmail.php');
										curl_setopt($ch, CURLOPT_POST, 1);
										curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
										curl_exec($ch);
										curl_close($ch);
                                            
										 

                                       
										
										  
									  }
									  
									
									 if($object_already_exist->estado == '1'){
										
									
                                      $total_dias = $dia - '15'; 

										 $sql_perdida_o = "UPDATE siniestro 
										                    SET dias_servicio='15',
															 perdida_total='1'
															WHERE id = '$id' 
															and perdida_total = 0";
										$no_adu = q($sql_perdida_o);
										echo $no_adu;
										
										
															
															
									  }
									  
									   if($object_already_exist->estado == '7'){

										  $hoy = date('Y-m-d');
										  $fecha_proceso = date('y-m-d h-i-s');
										  $numero_dia = 15;
											$dias_disas =  $dia - $numero_dia; 
											$dias_s = abs($dias_disas); 
											
											
											$dias = $dias_s +  $dia;
											$consecutivo = $consecutivo++;
											
											
											
									   $arraySolicitud_extra = array("fecha" => $hoy,
												"solicitado_por" => "WerbService",
												"justificacion" => "Cambio de perdida parcial a total en mapfre",
												"siniestro" => $id,
												"observaciones" => $expediente,
												"tipo"=>"EXTENSION",
												"dias" => $dias,
												"fecha" => $hoy,
												"procesado_por" =>"WerbService",
												"fecha_proceso" => $fecha_proceso,
												"consecutivo" => $consecutivo ,
												);
										
										$sql_solicitud_extra = $this->connect->insert("solicitud_extra",$arraySolicitud_extra);
										
										q($sql_solicitud_extra);
										
										

										$sql_perdida_o = "UPDATE siniestro 
										                    SET dias_servicio='$dias',
															 perdida_total='1'
															WHERE numero = '$numeroSiniestro' ";
												
												
										 $editar_siniestro = q($sql_perdida_o);	
										
										$sql_cita_b = "SELECT  * FROM cita_servicio WHERE siniestro 
							= '$id' and estado = 'C'";
								$sql_cita_bu = $this->connect->query($sql_cita_b);
				            	$sql_cita_buscar = $this->connect->convert_object($sql_cita_bu);	
		
								$fecha_cita =  $sql_cita_buscar->fec_devolucion;
								
							
	
										$Date = $fecha_cita;

              
										$fec_de = date('Y-m-d', strtotime($Date. ' + '.$dias_s.' days'));
										$sql_cita = "UPDATE cita_servicio 
										                   SET 
															 fec_devolucion ='$fec_de',
															  dias_servicio='$dias'
															WHERE siniestro = '$id' and  estado = 'C' ";
															$cita_r = q($sql_cita);
															 
															
									     
										     
															 
											$sql_ubicacion = "UPDATE ubicacion 
										                   SET fecha_final ='$fec_de'
															WHERE id = '$object_already_exist->ubicacion' ";				
 
														$hhh=	 q($sql_ubicacion);	


										

echo email;

															 
										 $data_mail = array(
										"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
										"enviarEmail" => "true",
										"id" => $id,
										"nombre" => $nombre_si,
										"placa" => $placa_es,
										"fecha" => $fec_de,
										 "para" =>  $email,
										"copia" =>  '',
										"contenido" => 303
										);

										$ch = curl_init();
										curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
										curl_setopt($ch, CURLOPT_URL, 'https://sac.aoacolombia.com/ServiEmail.php');
										curl_setopt($ch, CURLOPT_POST, 1);
										curl_setopt($ch, CURLOPT_POSTFIELDS, $data_mail);
										curl_exec($ch);
										curl_close($ch);
                                            
										 

                                       
										
										  
									  }
									  
									  
									  if($object_already_exist->estado == '8'){
										 
										 $hoy = date('Y-m-d');
										  $fecha_proceso = date('y-m-d h-i-s');
										  $numero_dia = 15;
											$dias_disas =  $dia - $numero_dia; 
											$dias_s = abs($dias_disas); 
											
											
											$dias = $dias_s +  $dia;
											
											
											
											
											
						$sql_solicitud_exta =
						"SELECT * FROM solicitud_extra where consecutivo =(select max(consecutivo) from solicitud_extra) ";
						
						
						
						$consulta = qo($sql_solicitud_exta);
						
						$conc_e = json_encode($consulta->consecutivo++);
						
						
					
						
						$clean_string = str_replace('"', '',  $conc_e);
						
						$numero_con =   intval($clean_string);
						
						

						$conc = $numero_con + 1;
						
						
					    $numero_es ="EXTENSION ".$conc.' '.$numeroSiniestro ;
					 
					
					                             
					
					
					
					
											
											
									   $arraySolicitud_extra = array("fecha" => $hoy,
												"solicitado_por" => "WerbService",
												"justificacion" => "Cambio de perdida parcial a total en mapfre",
												"siniestro" => $id,
												"observaciones" => $expediente,
												"tipo"=>"EXTENSION",
												"dias" => $dias,
												"fecha" => $hoy,
												"procesado_por" =>"WerbService",
												"fecha_proceso" => $fecha_proceso,
												"consecutivo" => $conc
												);
												
												
										$sql_solicitud_extra = $this->connect->insert("solicitud_extra",$arraySolicitud_extra);
										
										q($sql_solicitud_extra);
										  
										  
										  
										   $numero_es ="EXTENSION ".$conc.' '.$numeroSiniestro ;
											
											
											
											
										  
															   $arraySiniestro = array("poliza" => $numeroPoliza,
																	"vigencia_desde" => $vigencia_desde,
																	"perdida_total" => $numero_perdida,
																	"vigencia_hasta" => $vigencia_hasta,
																	"expediente" => $expediente,
																	"numero" => $numero_es,
																	"asegurado_id" => $asegurado_id,
																	"declarante_id" => $asegurado_id,
																	"declarante_nombre" => $declarante_nombre,
																	"asegurado_nombre" => $declarante_nombre,
																	"declarante_tel_resid" => $declarante_tel_resid,
																	"declarante_celular" => $declarante_celular,
																	"fec_siniestro" => $fec_siniestro,
																	"fec_declaracion" => $fec_declaracion,
																	"placa" => $placa,
																	"fasecolda" => $fasecolda,
																	"modelo" => $modelo,
																	"dias_servicio" => 5,
																	"aseguradora" => 4,
																	"ingreso" => $ingreso,
																	"ciudad" => $ciudad,
																	"ciudad_siniestro" => $codCiudad,
																	"ciudad_original" => $codCiudad,
																	"estado" => 5
																	);
											
											$sql = $this->connect->insert("siniestro",$arraySiniestro);
											
											q($sql);
											
											
									  }
									
									
								}
								
								$sql_perdida = "SELECT  perdida_total FROM siniestro WHERE numero = '$numeroSiniestro' and perdida_total = 0";
								$perdida_exist = $this->connect->query($sql_perdida);
				            	$object_perdida_exist = $this->connect->convert_object($perdida_exist);	
								
								$sql_perdida = "UPDATE alumnos
                                SET perdida_total='secundaria'
								WHERE numero = '$numeroSiniestro' and perdida_total = 1";
					 
							}else{
					   

                    echo 'No se puede hacer nada'; 
					
					exit();
							}

					   }
						
					}
					else{
						
						if($COD_RAMO == 132) {
					
						$placa_es = 'TOYOTA';
						
						$sql_inse = "insert into placa_especial (aseguradora,placa,descripcion,condicion,poliza )
																			   value ('4','$placa','TOYOTA '  , 'CLIENTE ESPECIAL MAPFRE','$numeroPoliza'); ";
												 $placa_especial = q($sql_inse);
												 
					 
					
					}else{
						
						
						$placa_es = '';
					}

					
						
						$sql = "select * from aoacol_aoacars.futuras_placa_mapfre where placa= '$placa'";
						$pla = $this->connect->query($sql); $QueryPlaca = $this->connect->convert_object($pla);	
						
							if(isset($QueryPlaca->placa))
							{
								$dias_servicio = 10;	
							}
							
							$numNuevos =  $Cantidad_devueltos++;
							
							
							
						       $arraySiniestro = array("poliza" => $numeroPoliza,
						                        "vigencia_desde" => $vigencia_desde,
												"perdida_total" => $numero_perdida,
												"vigencia_hasta" => $vigencia_hasta,
												"expediente" => $expediente,
												"numero" => $numeroSiniestro,
												"asegurado_id" => $asegurado_id,
												"declarante_id" => $asegurado_id,
												"declarante_nombre" => $declarante_nombre,
												"asegurado_nombre" => $declarante_nombre,
												"declarante_tel_resid" => $declarante_tel_resid,
												"declarante_celular" => $declarante_celular,
												"fec_siniestro" => $fec_siniestro,
												"fec_declaracion" => $fec_declaracion,
												"placa" => $placa,
												"fasecolda" => $fasecolda,
												"modelo" => $modelo,
												"dias_servicio" => $dias_servicio,
												"aseguradora" => 4,
												"ingreso" => $ingreso,
												"ciudad" => $ciudad,
												"ciudad_siniestro" => $codCiudad,
												"ciudad_original" => $codCiudad,
												"estado" => 5
											    );
						
						$sql = $this->connect->insert("siniestro",$arraySiniestro);
						echo $sql."<br>";
						q($sql);
						
						$sql = "select id,numero,ingreso,fec_siniestro,fec_autorizacion,placa,t_ciudad(ciudad_original) as nciudad,ingreso from siniestro where aseguradora=4 and numero='$numeroSiniestro'";
						$QuerySin = qo($sql); 
						
						if($QuerySin){
						    $H1=date('Y-m-d',strtotime($QuerySin->ingreso)); $H2=date('H:i:s',strtotime($QuerySin->ingreso));
							
							q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo ) values ($QuerySin->id,'$H1','$H2','Webservice Mapfre','Ingreso a AOA',1)");
						
						}
					
						if($QuerySin->ingreso)
						{
							$Cadena_email.="<tr><td>$numNuevos</td><td>$QuerySin->numero</td><td>$QuerySin->fec_siniestro</td><td>$QuerySin->fec_autorizacion</td><td>$QuerySin->placa</td>".
							"<td>$QuerySin->nciudad</td><td>$QuerySin->ingreso</td><td></td>$QuerySin->nciudad<td>";
							
						}
						
						
						$sql = "select t_aseguradora(aseguradora) as naseg,numero,fec_autorizacion,t_estado_siniestro(estado) as nest,asegurado_nombre,ingreso,t_ciudad(ciudad) as nciu
														from siniestro where placa='$placa' order by ingreso";
														
						$asegu = q($sql); 
						
						if($asegu){
							if(mysql_num_rows($asegu) > 1){
								
								$Correo_repetidos.="<tr ><td >Placa: $placa</td></tr>";
									while($Var=mysql_fetch_object($asegu)){
										$Correo_repetidos.="<tr ><td >$Var->naseg</td><td >$Var->numero</td><td >$Var->fec_autorizacion</td><td >$Var->nest</td><td >$Var->asegurado_nombre</td><td >$Var->ingreso</td><td >$Var->nciu</td></tr>";
									}
							}
						}
					}
					
				
			
					
						
				
			
			
			
  
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