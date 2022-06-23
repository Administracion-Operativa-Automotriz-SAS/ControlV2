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
		 
		 
		
		$array = json_decode($utfJson);
		
		$EMAIL="sergiocastillo@aoacolombia,Sergio Castillo;gabrielsandoval@aoacolombia.com,Gabriel Sandoval";
		$EMAIL1="sergiocastillo@aoacolombia,Sergio Castillo";
		
		$Correo_repetidos='';
		$Cantidad_devueltos=1;
		$Cantidad_devueltosUno = 1;
		$Cadena_email='';
		$FECHA=date('Ymd');
		$Cadena_email.="<hr>Fecha: $FECHA $Cantidad Siniestros<br><table border cellspacing=0><tr><th>#</th><th>Siniestro</th><th>Fecha siniestro</th><th>Fecha declaracion</th><th>Placa</th><th>Ciudad Origen</th><th>Fec Ingreso a AOA</th><th>Ciudad</th></tr>";
	
	      foreach ($array as $val) {
				
		    $array_num = count($val);
				
			  for ($i = 1; $i < $array_num; ++$i){
				  
				    
					$total = $val[$i][6];
					
					
					$cadena_de_texto = $total;
					
				    $cadena_buscada   = 'TOTAL';
					
					$posicion_coincidencia  =  strpos($cadena_de_texto, $cadena_buscada);
				
					if($posicion_coincidencia === false) {
						
						$numero_perdida = 0;
					
					}else{
						
						$numero_perdida = 1;
					
					}

					
					$numeroSiniestro = $val[$i][7];
					
					$numeroPoliza = $val[$i][2];
					
					$vigencia_desde = $val[$i][3];
					
					$vigencia_hasta = $val[$i][4];
					
					$expediente = $val[$i][5];
					
					$asegurado_id =  $val[$i][8];
					
					$declarante_nombre = utf8_decode($val[$i][9]);
					
					$declarante_tel_resid = $val[$i][10];
					
					$declarante_celular = $val[$i][11];
					
					$fec_siniestro = $val[$i][13];
					
					
					$COD_RAMO = $val[$i][1];
					
					$fec_declaracion = $val[$i][14];
					
					$placa = $val[$i][15];
					
					$fasecolda = $val[$i][16];
					
					$modelo = $val[$i][18];
					
					
					$dias_servicio = $val[$i][22];
					
					$ciudad_siniestro = $val[$i][21]; 
					
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
					
					
                                        

					
					$ingreso = date("Y-m-d H:i:s");
					
					
					$sql = "SELECT dias_servicio as dia, ubicacion, declarante_email as email,declarante_nombre as nombre  ,numero ,
					perdida_total,id,estado FROM siniestro_copy WHERE numero = '$numeroSiniestro'";
					
				    $sql_solicitud_extra = "SELECT consecutivo FROM solicitud_extra_copy ORDER BY id DESC LIMIT 1";
											$solicitud_extra = $this->connect->query($sql_solicitud_extra);
											$object_solicitud = $this->connect->convert_object($solicitud_extra);	
					
					$already_exist = $this->connect->query($sql);
					
					$object_already_exist = $this->connect->convert_object($already_exist);	
					
					$dia = $object_already_exist->dia; 
					$id = $object_already_exist->id;
					$email = $object_already_exist->email;
					$nombre_si = $object_already_exist->nombre; 
					$consecutivo =$object_solicitud->consecutivo;
					
                                        if(strpos($fasecolda, 'TOYOTA') !== false) {
                                            $fase ='TOYOTA';
                                        }else{
                                            $fase ='OTRO';
                                        }

					if($object_already_exist->numero){
					
					   $con_romo_siniestro  = "UPDATE siniestro_copy SET cod_ramo ='$COD_RAMO' WHERE id = '$id' ";
													
					  $cod_ramo_sql = q($con_romo_siniestro);	
								
                                        	
							
					  if (strpos($modelo, 'PLUS') !== false  && $fase !== 'TOYOTA' && $COD_RAMO != '132'){
							
							
								$sql_placa_especial =  "SELECT * FROM placa_especial where placa = '$placa'  ";					
								$placa_especial = qo($sql_placa_especial);
															
								if(!$placa_especial){
									
									$placa_es = 'TOYOTA';
								
//								$sql_inse = "insert into placa_especial (aseguradora,placa,descripcion,condicion,poliza )
//									value ('4','$placa','TOYOTA '  , 'CLIENTE ESPECIAL MAPFRE','$numeroPoliza'); ";
//								 $placa_especial = q($sql_inse);
									
								};		

                                   
                                                                $mapfre_vip = "UPDATE siniestro_copy SET aseguradora ='262' WHERE id = '$id' ";
													
								$mapfre_vip_sql = q($mapfre_vip);		 							

					   }else if($COD_RAMO == 132 || $fase == 'TOYOTA') {
							
							
								$sql_placa_especial =  "SELECT * FROM placa_especial where placa = '$placa'  ";					
								$placa_especial = qo($sql_placa_especial);
															
								if(!$placa_especial){
									
									$placa_es = 'TOYOTA';
								
//								$sql_inse = "insert into placa_especial (aseguradora,placa,descripcion,condicion,poliza )
//									value ('4','$placa','TOYOTA '  , 'CLIENTE ESPECIAL MAPFRE','$numeroPoliza'); ";
//								 $placa_especial = q($sql_inse);
									
								};		

                                   
                                                                $mapfre_vip = "UPDATE siniestro_copy SET aseguradora ='10' WHERE id = '$id' ";
													
								$mapfre_vip_sql = q($mapfre_vip);		 							

					   }

					
							
		
					   
						if($numero_perdida == 1){
								
							
							if(!$object_already_exist->perdida_total){

								
								 
								$sql_solicitud = "SELECT * FROM solicitud_extra_copy where siniestro = $id ";
								   
								$object_solicitud_extra = qo($sql_solicitud);
							
									
								if($object_solicitud_extra->tipo == 'EXTENSION'){
										
										echo 'Tiene EXTENSION del s';
										
								}else{
											 
									if($object_already_exist->estado == '3'){

										$hoy = date('Y-m-d');
										$fecha_proceso = date('y-m-d h-i-s');
                                                                                if($dias_servicio>=20){
                                                                                    $numero_dia = $dias_servicio; 
                                                                                }else{
                                                                                   $numero_dia = 15;  
                                                                                }
										
										$dias_disas =  $dia - $numero_dia; 
										$dias_s = abs($dias_disas); 
												
												
										$dias = $dias_s +  $dia;
										$consecutivo = $consecutivo++;
//										$sql_perdida_o = "UPDATE siniestro_copy SET dias_servicio='$dias', perdida_total='1'
//														  WHERE id = '$id' ";
													
//										$editar_siniestro = q($sql_perdida_o);	
											
										$sql_cita_b = "SELECT  * FROM cita_servicio WHERE siniestro = '$id' and estado = 'C'";
										$sql_cita_bu = $this->connect->query($sql_cita_b);
										$sql_cita_buscar = $this->connect->convert_object($sql_cita_bu);	
										$fecha_cita =  $sql_cita_buscar->fec_devolucion;
										$Date = $fecha_cita;

							  
										$fec_de = date('Y-m-d', strtotime($Date. ' + '.$dias_s.' days'));
//										$sql_cita = "UPDATE cita_servicio SET   dias_servicio='$dias'
//													 WHERE siniestro = '$id' and  estado = 'P' ";
//										$cita_r = q($sql_cita);
																 
																
										$data_mail = array(
										"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
										"enviarEmail" => "true",
										"id" => $id,
										"nombre" => $nombre_si,
										"placa" => $placa_es,
										"fecha" => $fec_de,
										 "para" =>  $email,
										"copia" =>  'davidduque@aoacolombia.com',
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
											
										  if($dias_servicio>=20){
                                                                                      $total_dias = $dia - $dias_servicio; 
                                                                                      $xdia = $dias_servicio;
                                                                                  }else{
                                                                                      $total_dias = $dia - '15'; 
                                                                                      $xdia = 15;
                                                                                  }
										  

											 $sql_perdida_o = "UPDATE siniestro_copy 
																SET dias_servicio='$xdia',
																 perdida_total='1'
																WHERE id = '$id' 
																and perdida_total = 0";
											$no_adu = q($sql_perdida_o);
											
											
											
																
																
										  }
										  
										   if($object_already_exist->estado == '7'){

											  $hoy = date('Y-m-d');
											  $fecha_proceso = date('y-m-d h-i-s');
                                                                                          if($dias_servicio>=20){
                                                                                              $numero_dia = $dias_servicio;
                                                                                          }else{
                                                                                              $numero_dia = 15;
                                                                                          }
											  
												$dias_disas =  $dia - $numero_dia; 
												$dias_s = abs($dias_disas); 
												
												
												$dias = $dias_s +  $dia;
												$consecutivo = $consecutivo++;
												
												
												
										   $arraySolicitud_extra = array("fecha" => $hoy,
													"solicitado_por" => "WebService",
													"justificacion" => "Cambio de perdida parcial a total en mapfre",
													"siniestro" => $id,
													"observaciones" => $expediente,
													"tipo"=>"EXTENSION",
													"dias" => $dias,
													"fecha" => $hoy,
													"procesado_por" =>"WebService",
													"fecha_proceso" => $fecha_proceso,
													"consecutivo" => $consecutivo ,
													);
											
											$sql_solicitud_extra = $this->connect->insert("solicitud_extra_copy",$arraySolicitud_extra);
											
											q($sql_solicitud_extra);
											
											

											$sql_perdida_o = "UPDATE siniestro_copy 
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
//											$sql_cita = "UPDATE cita_servicio 
//															   SET 
//																 fec_devolucion ='$fec_de',
//																  dias_servicio='$dias'
//																WHERE siniestro = '$id' and  estado = 'C' ";
//																$cita_r = q($sql_cita);
//																 
//																
//											 
//												 
//																 
//												$sql_ubicacion = "UPDATE ubicacion 
//															   SET fecha_final ='$fec_de'
//																WHERE id = '$object_already_exist->ubicacion' ";				
//
//															$hhh=	 q($sql_ubicacion);	


											



																 
											 $data_mail = array(
											"APIKEYAOAAPP" => "yNPlsmOGgZoGmH$129",
											"enviarEmail" => "true",
											"id" => $id,
											"nombre" => $nombre_si,
											"placa" => $placa_es,
											"fecha" => $fec_de,
											 "para" =>  $email,
											"copia" =>  'davidduque@aoacolombia.com',
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
													"SELECT * FROM solicitud_extra_copy where consecutivo =(select max(consecutivo) from solicitud_extra_copy) ";
													
													
													
													$consulta = qo($sql_solicitud_exta);
													
													$conc_e = json_encode($consulta->consecutivo++);
													
													
												
													
													$clean_string = str_replace('"', '',  $conc_e);
													
													$numero_con =   intval($clean_string);
													
													

													$conc = $numero_con + 1;
													
													
													$numero_es ="EXTENSION ".$conc.' '.$numeroSiniestro ;
						 
						
													 
						
						
						
						
												
												
										   $arraySolicitud_extra = array("fecha" => $hoy,
													"solicitado_por" => "WebService",
													"justificacion" => "Cambio de perdida parcial a total en mapfre",
													"siniestro" => $id,
													"observaciones" => $expediente,
													"tipo"=>"EXTENSION",
													"dias" => $dias,
													"fecha" => $hoy,
													"procesado_por" =>"WebService",
													"fecha_proceso" => $fecha_proceso,
													"consecutivo" => $conc
													);
													
													
											$sql_solicitud_extra = $this->connect->insert("solicitud_extra_copy",$arraySolicitud_extra);
											
											q($sql_solicitud_extra);
											  
											  
											  
											   $numero_es ="EXTENSION ".$conc.' '.$numeroSiniestro ;
												
												// SE CREA EL SINIESTRO CUANDO ESTA CONCLUIDO Y AGREGA 5 DIAS
												if (strpos($modelo, 'PLUS') !== false && $fase !== 'TOYOTA'  && $COD_RAMO != '132') {
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
															"cod_ramo" => $COD_RAMO,
															"fasecolda" => $fasecolda,
															"modelo" => $modelo,
															"dias_servicio" => 5,
															"aseguradora" => 262,
															"ingreso" => $ingreso,
															"ciudad" => $ciudad,
															"ciudad_siniestro" => $codCiudad,
															"ciudad_original" => $codCiudad,
															"estado" => 5
															);
												
												        $sql = $this->connect->insert("siniestro_copy",$arraySiniestro);
												
												       q($sql);
                                                                                                }else{
												if($COD_RAMO == 132 || $fase == 'TOYOTA'){
													
											  
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
															"cod_ramo" => $COD_RAMO,
															"fasecolda" => $fasecolda,
															"modelo" => $modelo,
															"dias_servicio" => 5,
															"aseguradora" => 10,
															"ingreso" => $ingreso,
															"ciudad" => $ciudad,
															"ciudad_siniestro" => $codCiudad,
															"ciudad_original" => $codCiudad,
															"estado" => 5
															);
												
												        $sql = $this->connect->insert("siniestro_copy",$arraySiniestro);
												
												       q($sql);
												
												}else{
													
													
													
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
															"cod_ramo" => $COD_RAMO,
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
												
												        $sql = $this->connect->insert("siniestro_copy",$arraySiniestro);
												
												       q($sql);
													
												}
												
                                                                                           }
										  
										  
										  }
										
										
									}
						
									$sql_perdida = "SELECT  perdida_total FROM siniestro_copy WHERE numero = '$numeroSiniestro' and perdida_total = 0";
									$perdida_exist = $this->connect->query($sql_perdida);
									$object_perdida_exist = $this->connect->convert_object($perdida_exist);	
									
//									$sql_perdida = "UPDATE alumnos
//									SET perdida_total='secundaria'
//									WHERE numero = '$numeroSiniestro' and perdida_total = 1";
							}else{
								
								echo "El vehiculo esta marcado con perdita total";

						
							}
								
								
								
								
						}else{
						   
							  echo 'No se puede hacer nada por que esta en total '; 
							  echo "<br>";
						
						}

					
					}else{
					
					if (strpos($modelo, 'PLUS') !== false  && $fase !== 'TOYOTA' && $COD_RAMO != '132') {
                                            $sql_placa_especial =  "SELECT * FROM placa_especial where placa = '$placa'  ";
													
													
													
								$placa_especial = qo($sql_placa_especial);
															
								if(!$placa_especial){
									
									$placa_es = 'TOYOTA';
								
//								$sql_inse = "insert into placa_especial (aseguradora,placa,descripcion,condicion,poliza )
//									value ('4','$placa','TOYOTA '  , 'CLIENTE ESPECIAL MAPFRE','$numeroPoliza'); ";
//								 $placa_especial = q($sql_inse);
									
								};	
								
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
														"cod_ramo" => $COD_RAMO,
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
														"aseguradora" => 262,
														"ingreso" => $ingreso,
														"ciudad" => $ciudad,
														"ciudad_siniestro" => $codCiudad,
														"ciudad_original" => $codCiudad,
														"estado" => 5
														);
								
								$sql = $this->connect->insert("siniestro_copy",$arraySiniestro);
								echo $sql."<br>";
								q($sql);
                                        }else{
					if($COD_RAMO == 132 || $fase == 'TOYOTA') {
							
							
								$sql_placa_especial =  "SELECT * FROM placa_especial where placa = '$placa'  ";
													
													
													
								$placa_especial = qo($sql_placa_especial);
															
								if(!$placa_especial){
									
									$placa_es = 'TOYOTA';
								
//								$sql_inse = "insert into placa_especial (aseguradora,placa,descripcion,condicion,poliza )
//										value ('4','$placa','TOYOTA '  , 'CLIENTE ESPECIAL MAPFRE','$numeroPoliza'); ";
//								 $placa_especial = q($sql_inse);
									
								};	
								
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
														"cod_ramo" => $COD_RAMO,
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
														"aseguradora" => 10,
														"ingreso" => $ingreso,
														"ciudad" => $ciudad,
														"ciudad_siniestro" => $codCiudad,
														"ciudad_original" => $codCiudad,
														"estado" => 5
														);
								
								$sql = $this->connect->insert("siniestro_copy",$arraySiniestro);
								echo $sql."<br>";
								q($sql);
                                 
								 

								
							
					
						
					
					   }else{
						
						
						
						
						
						
							if($numero_perdida == 1){
								
								
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
														"cod_ramo" => $COD_RAMO,
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
								
								$sql = $this->connect->insert("siniestro_copy",$arraySiniestro);
								echo $sql."<br>";
								q($sql);
								
							}else{
								
								
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
															"cod_ramo" => $COD_RAMO,
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
									
									$sql = $this->connect->insert("siniestro_copy",$arraySiniestro);
									echo $sql."<br>";
									q($sql);
								
								
							}	

					
						
					   }

                                        }
						$sql = "select id,numero,ingreso,fec_siniestro,fec_autorizacion,placa,t_ciudad(ciudad_original) as nciudad,ingreso from siniestro_copy where aseguradora=4 and numero='$numeroSiniestro'";
						$QuerySin = qo($sql); 
						
						if($QuerySin){
							$H1=date('Y-m-d',strtotime($QuerySin->ingreso)); $H2=date('H:i:s',strtotime($QuerySin->ingreso));
							
							//q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo ) values ($QuerySin->id,'$H1','$H2','Webservice Mapfre','Ingreso a AOA',1)");
						
						}
					
						if($QuerySin->ingreso)
						{
							$Cadena_email.="<tr><td>$numNuevos</td><td>$QuerySin->numero</td><td>$QuerySin->fec_siniestro</td><td>$QuerySin->fec_autorizacion</td><td>$QuerySin->placa</td>".
							"<td>$QuerySin->nciudad</td><td>$QuerySin->ingreso</td><td></td>$QuerySin->nciudad<td>";
							
						}
						
						
						$sql = "select t_aseguradora(aseguradora) as naseg,numero,fec_autorizacion,t_estado_siniestro(estado) as nest,asegurado_nombre,ingreso,t_ciudad(ciudad) as nciu
														from siniestro_copy where placa='$placa' order by ingreso";
														
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
			
			
			
			}
			
			$Cadena_email.="</table>";
			
			$Cadena_email.='<br><br>Webservice de consumo:';
			
			if($numNuevos){
				
					$Cadena_email="<hr>Fecha: $FECHA $numNuevos Siniestros nuevos<br><table border cellspacing=0><tr><th>Siniestro</th><th>Fecha siniestro</th><th>Fecha declaracion</th><th>Placa</th><th>Ciudad Origen</th><th>Ingreso a AOA</th><th>CodC</th></tr>".
						$Cadena_email;
					    //$Envio1=enviar_gmail('sistemas@aoacolombia.com' /*de */,'Sistemas AOA Colombia' /*Nombre de */ ,$EMAIL /*para */,"" /*con copia*/,"Mapfre Webservice" /*Objeto */,$Cadena_email);
//					    $sql = "insert into web_service (fecha,aseguradora,descripcion) values ('".date('Y-m-d H:i:s')."','4',\"$Cadena_email\")";
//						q($sql);
				}else{
				
					$Cadena_email="<hr>Fecha: $FECHA 0 Siniestros nuevos<br><br>".$Cadena_email;
					
						//$Envio1=enviar_gmail('sistemas@aoacolombia.com' /*de */,'Sistemas AOA Colombia' /*Nombre de */ ,$EMAIL1 /*para */,"" /*con copia*/,"Mapfre Webservice" /*Objeto */,$Cadena_email);
//						$sql = "insert into web_service (fecha,aseguradora,descripcion) values ('".date('Y-m-d H:i:s')."','4',\"$Cadena_email\")";
//						
//						q($sql);
				}
			
			
			
			if($Correo_repetidos){
			$Cadena_emailr="<body>Correo de verificacion de siniestros con placas repetidas.<br>A continuacion se relacionan las placas repetidas:<br>".
				"<table border cellspacing='0'><tr ><th >Aseguradora</th><th >Numero</th><th >Fec.Autorizacion</th><th >Estado</th><th >Asegurado</th><th >Ingreso</th><th >Ciudad</th></tr>".
				$Correo_repetidos."</table><br><br>Cordialmente,<br><br>Sergio Castillo Castro.<br>Gestor de Procesos<br>AOA Colombia S.A.<br>sergiocastillo@aoacolombia.com</body></html>";
			
			/*$Envio=enviar_gmail('sistemas@aoacolombia.com',
			'Sistemas AOA Colombia',
			"sandraosorio@aoacolombia.com,Sandra Osorio;siniestros@aoacolombia.com,Gestor de Siniestros" ,
			"sergiocastillo@aoacolombia.com,Sergio Castillo,sergiourbina@aoacolombia.com,Sergio Urbina",
			"Siniestros con la misma placa.",
			$Cadena_emailr);
			*/
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