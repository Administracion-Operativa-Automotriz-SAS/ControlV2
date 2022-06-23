<?php
function Conectarse(){
 $host="localhost";
 //$usuario="aoacol_aoa";
 //$password="4d4T9a7";
 //$bd="aoacol_aoa";
 $usuario="root";
 $password="1234";
 $bd="aoa";
 if (!($link=mysql_connect($host,$usuario,$password))){
  echo mysql_errno().": ".mysql_error()."<BR>";
  exit(); 
 } 
 if (!mysql_select_db($bd,$link)){
  echo mysql_err_db($bd);
  echo mysql_errno().": ".mysql_error()."<BR>";
  exit(); 
 } 
 return $link; 
} 
?>