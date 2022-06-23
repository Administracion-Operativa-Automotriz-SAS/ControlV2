<?php
function llenar_select ($arreglo1, $arreglo2, $restriccion) {
 for ($i=0; $i<= (count($arreglo1)-1);$i++){
  $select="";
  if (isset($restriccion)){
   if ($arreglo1[$i]==$restriccion){
    $select="selected ";
   }
  }
  echo '<option '.$select.'value="'.$arreglo1[$i].'">'.$arreglo2[$i].'</option>';
 }
}

function llenar_select1 ($arreglo1, $arreglo2, $restriccion) {
 $str_select="";
 for ($i=0; $i<= (count($arreglo1)-1);$i++){
  $select="";
  if (isset($restriccion)){
   if ($arreglo1[$i]==$restriccion){
    $select="selected ";
   }
  }
  $str_select=$str_select.'<option '.$select.'value="'.$arreglo1[$i].'">'.$arreglo2[$i].'</option>';
 }
 return $str_select;
}

function cargar_select ($MyQuery,$restriccion){
 include_once("libMYSQL.php");
 $link=Conectarse();
 $loResult=mysql_query($MyQuery,$link);
 @$testselect=mysql_num_rows($loResult);
 if ($testselect > 0){
  while ($name_row = mysql_fetch_row($loResult)){
   $select="";
   if (isset($restriccion)){
    if ($name_row[0]==$restriccion){
	 $select="selected ";
    }
   }
   echo '<option '.$select.'value="'.$name_row[0].'">'.$name_row[1].'</option>';
  }
  return true;
 }
 else{
  return false;
 }
 @mysql_free_result($loResult);
 mysql_close($link);
}

function cargar_select1 ($MyQuery,$restriccion){
 include_once("libMYSQL.php");
 $link=Conectarse();
 $loResult=mysql_query($MyQuery,$link);
 @$testselect=mysql_num_rows($loResult);
 $str_select="";
 if ($testselect > 0){
  while ($name_row = mysql_fetch_row($loResult)){
   $select="";
   if (isset($restriccion)){
    if ($name_row[0]==$restriccion){
	 $select="selected ";
    }
   }
   $str_select=$str_select.'<option '.$select.'value="'.$name_row[0].'">'.$name_row[1].'</option>';
  }
 }
 @mysql_free_result($loResult);
 mysql_close($link);
 return $str_select;
}

function cargar_datos ($arreglo1, $arreglo2) {
 for ($i=0; $i<= (count($arreglo1)-1);$i++){
  echo '<input type="hidden" name="'.$arreglo1[$i].'" value="'.$arreglo2[$i].'">';
 }
}

function cargar_url ($arreglo1, $arreglo2){
 $url="";
 for ($i=0; $i<= (count($arreglo1)-1);$i++){
  if ($i>0){
   $conector="&";
  }
  else{
   $conector="";
  }
  $url=$url.$conector.$arreglo1[$i].'='.$arreglo2[$i];
 }
 return $url;
}

function cargar_option ($MyQuery,$restriccion,$nombre){
 include_once("libMYSQL.php");
 $link=Conectarse();
 $loResult=mysql_query($MyQuery,$link);
 @$testselect=mysql_num_rows($loResult);
 if ($testselect > 0){
  while ($name_row = mysql_fetch_row($loResult)){
   echo '<label for="opt_pista">'.$name_row[1].'</label>';
   $checked="";
   if (isset($restriccion)){
   if ($name_row[0]==$restriccion){
    $checked='checked="checked"';
   }
  }
  else{
   	$restriccion=0;
   	$checked='checked="checked"';
  }
   echo '<input value="'.$name_row[0].'" name="'.$nombre.'" type="radio" '.$checked.'>';
  }
 }
 @mysql_free_result($loResult);
 mysql_close($link);
}

function cargar_registro ($MyQuery){
 include_once("libMYSQL.php");
 $link=Conectarse();
 $loResult=mysql_query($MyQuery,$link);
 @$testselect=mysql_num_rows($loResult);
 if ($testselect > 0){
  $name_row = mysql_fetch_row($loResult);
 }
 return $name_row[0];
 @mysql_free_result($loResult);
 mysql_close($link);
}

function cargar_registro1 ($MyQuery){
 include_once("libMYSQL.php");
 $link=Conectarse();
 $loResult=mysql_query($MyQuery,$link);
 @$testselect=mysql_num_rows($loResult);
 if ($testselect > 0){
  $name_row = mysql_fetch_row($loResult);
 }
 @mysql_free_result($loResult);
 mysql_close($link);
 return $name_row;
}

function test($MyQuery){
 include_once("libMYSQL.php");
 $link=Conectarse();
 $loResult=mysql_query($MyQuery,$link);
 @$testselect=mysql_num_rows($loResult);
 return $testselect;
 @mysql_free_result($loResult);
 mysql_close($link);
}

function mensajes_mysql($Test,$mensaje1,$mensaje2){
 echo '<table style="text-align: center; width: 100%; height: 100%;"
 border="0" cellpadding="2" cellspacing="2" class="textorojo">
  <tbody>
    <tr>
      <td>';
      if ($Test > 0){
	   echo $mensaje1;
      }
	  else{
	   echo $mensaje2;    
      }
      echo '</td>
    </tr>
  </tbody>
 </table>';
}

function mensaje_mysql($Test,$mensaje1,$mensaje2,$frm,$form,$txt,$dato){
 echo '<table style="text-align: center; width: 100%; height: 100%;"
 border="0" cellpadding="2" cellspacing="2" class="textorojo">
  <tbody>
    <tr>
      <td style="text-align: center; vertical-align: bottom; height: 50%;">';
      if ($Test==0){
	   echo $mensaje1;
      }
	  else{
	   echo $mensaje2;    
      }
      echo '</td>
    </tr>
	<tr>
    <td style="text-align: center; vertical-align: top; height: 50%;">
	<form action="'.$form.'?'.SID.'" method="post" class="form_Prop" name="agregcita">';
	if($frm=="regeditarcita"){
	 cargar_datos ($txt,$dato);
	 if ($Test==0){echo '<input name="submit" value="Continuar" class="botones" type="submit">';}
	}
	echo'</form>	
    </td>	
    </tr>
	</tbody>
 </table>';
}

function mensaje_sistema($mensaje){
 echo '<table style="text-align: left; width: 100%; height: 100%; background-color:#5060BA;" border="0" cellpadding="2" 
 cellspacing="2">
  <tbody>
   <tr style="font-weight: bold; color:#FFFFFF;">
    <td style="text-align: center; vertical-align: bottom; height: 50%;">
     '.$mensaje.'
    </td>
   </tr>
   <tr>
    <td style="text-align: center; vertical-align: top; height: 50%;">
     <form action="validar.php" name="frmusuario" method="post" target="iframemenu">
	  <input name="submit" value="Volver" class="botones" type="submit">
	 </form>
    </td>
   </tr>
  </tbody>
 </table>';
 exit;
}

function mensaje($mensaje){
 echo '<table style="text-align: center; width: 100%; height: 100%;" border="0" cellpadding="2" cellspacing="2" 
 class="textorojo">
  <tbody>
    <tr>
      <td>'.$mensaje.'</td>
    </tr>
  </tbody>
 </table>';
 exit;
}

function VerificaExistencia($login,$password,$tipo){
 $login=stripslashes(trim($login));
 $password=stripslashes(trim($password));
 include_once("libMYSQL.php");
 $link=Conectarse();
 $loResult=mysql_query("select id from usuario where  documento='$login' and password=md5('$password') 
 and tipo='$tipo' and activo='TRUE'",$link) or die (mysql_errno().": ".mysql_error()."<BR>");
 $fila=mysql_fetch_row($loResult);
 if ($fila[0]==0){
  $usuario="FALSE";  
 } 
 else{
  @session_start();
  $_SESSION['id_usuario'] = $fila[0];
  $_SESSION['tipo_usuario'] = $tipo;
  $usuario="TRUE";
 }
 return $usuario;
 @mysql_free_result($loResult);
 mysql_close($link);
}

function VerificaSiniestro($login){
 $login=stripslashes(trim($login));
 include_once("libMYSQL.php");
 $link=Conectarse();
 $loResult=mysql_query("SELECT s.id FROM siniestro s,cliente c WHERE c.id=s.id_cliente AND c.documento='$login' AND s.activo='TRUE'",
 $link) or die (mysql_errno().": ".mysql_error()."<BR>");
 $fila=mysql_fetch_row($loResult);
 if ($fila[0]==0){
  $usuario="FALSE";  
 } 
 else{
  @session_start();
  $_SESSION['id_siniestro'] = $fila[0];
  $usuario="TRUE";
 }
 return $usuario;
 @mysql_free_result($loResult);
 mysql_close($link);
}

function No_session(){
 //poner el link de inicio de la pagina web.
  echo '<table style="text-align: left; width: 100%; height: 100%; background-color:#5060BA;" border="0" cellpadding="2" 
 cellspacing="2">
  <tbody>
   <tr style="font-weight: bold; color:#FFFFFF;">
    <td style="text-align: center; vertical-align: bottom; height: 50%;">
     CONEXION DE USUARIO PERDIDA CON EL SISTEMA.
    </td>
   </tr>
   <tr>
    <td style="text-align: center; vertical-align: top; height: 50%;">
     <form action="../index.php" name="frmusuario" method="post" target="_top">
	  <input name="submit" value="Volver" class="botones" type="submit">
	 </form>
    </td>
   </tr>
  </tbody>
 </table>';
 exit;
}

function suma_fechas($fecha,$ndias){
	if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
	 list($dia,$mes,$año)=split("/", $fecha);
	if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
	 list($dia,$mes,$año)=split("-",$fecha);
	 $nueva = mktime(0,0,0, $mes,$dia,$año) + $ndias * 24 * 60 * 60;
	 $nuevafecha=date("d-m-Y",$nueva);
    return ($nuevafecha);
}
 

?>