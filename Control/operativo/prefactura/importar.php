<?php
include('conexion.php');
session_start();
//mysql_query("truncate tanqueos");
$foto = $_FILES["foto"]["name"];

                     unlink($foto);
                     //$ingresar = mysqli_query($con, "update conf_referencias set imagenes ='".$foto."' where referencia='".$sobre."' ");
                     move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
                     $destino = $foto;
                     $fp = fopen($destino, "r");
                     $fila = 0;
                     while (!feof($fp)) 
                         { 
                            $fila++;
                            $data = explode(";", fgets($fp));
                          if($fila!=1 && $data[3]!=''){
                     
                              $placa = $data[3];
                              $galon = trim(str_replace("G","", $data[14]));
                              $precio = trim(str_replace(".","", $data[15]));
                              $total = trim(str_replace(".","", $data[17]));
                              $rodamineto = $data[11];
                              $descripcion = 'TANQUEADO EN '.$data[6].' POR '.$data[5].' '.$data[9].', GL: '.$galon.' Vlr Gl:$'.$precio.', TOTAL:$'.number_format($total);
                              $galon = trim(str_replace(",",".", $galon));
                                          $comprobar = mysql_num_rows(mysql_query("SELECT * FROM tanqueos WHERE placa = '".$placa."' and fechaproceso = '".$data[0]."' "));
                                         if($comprobar == 0){ 
                                             
                                          $ok = mysql_query("INSERT INTO tanqueos (placa,descripcion,ciudad,kilometro,galon,valorgl,fechaproceso, estado,total,rodamiento) VALUES ('".trim($placa)."','".trim($descripcion)."','".trim($data[7])."','".trim($data[10])."','".$galon."','".$precio."','".trim($data[0])."','0','".$total."','".$rodamineto."') ") or die(mysql_error());
                                          echo ' Se registro con exito '.$placa.' - Fecha de registro'.trim($data[0]).'<br>';
                                         
                                         }else{
                                             echo '<b> <font color="red">Ya se cargo la placa '.trim($placa).' con fecha de proceso '.trim($data[0]).'</font> </b><br>';
                                         } 
                     
                          }
                     
                         }

        