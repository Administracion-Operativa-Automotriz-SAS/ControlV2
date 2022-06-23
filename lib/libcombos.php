<?php
function combosfecha($ruta_http,$anoInicial,$anoFinal,$dia,$mes,$ano,$top,$left,$id){
//cmb que muestra rango de años a seleccion
echo '<select size="1" name="cmb'.$id.'_ano" class="default-style" style="position:absolute; top:'.$top.'px; left:'.$left.'px; padding:0; overflow:visible" onchange="javascript:cmbdia(\''.$ruta_http.'cmb_dia.php\',\'cmb'.$id.'_dia\',\'cmb'.$id.'_mes\',\'cmb'.$id.'_ano\',\'div'.$id.'_dia\','.$id.')" id="cmb'.$id.'_ano">';
for ($i = $anoInicial; $i <= $anoFinal; $i++){
	echo '<option ';
  	if($ano == $i)echo 'selected ';
  	echo 'value="'.$i.'">'.$i.'</option>'."\n";
}
echo '</select>&nbsp;&nbsp;&nbsp;';
 
//cmb que muestra los meses del año.
$left=$left+57;
echo '<select size="1" name="cmb'.$id.'_mes" class="default-style" style="position:absolute; top:'.$top.'px; left:'.$left.'px; padding:0; overflow:visible" onchange="javascript:cmbdia(\''.$ruta_http.'cmb_dia.php\',\'cmb'.$id.'_dia\',\'cmb'.$id.'_mes\',\'cmb'.$id.'_ano\',\'div'.$id.'_dia\','.$id.')" id="cmb'.$id.'_mes">';
$meses = Array ('ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE');
for($i = 1; $i <= 12; $i++){
  echo '<option '; 
  if($mes == $i)echo 'selected ';
  echo 'value="'.$i.'">'.$meses[$i-1].'</option>'."\n";
}
echo '</select>&nbsp;&nbsp;&nbsp;';

//cmb que muestra los dias del mes de acuerdo a lo que se escojio en los otros combos.
$left=$left+116;
echo '<div id="div'.$id.'_dia" class="default-style" style="position:absolute; top:'.$top.'px; left:'.$left.'px; padding:0; overflow:visible">
<select size="1" name="cmb'.$id.'_dia" class="default-style"  id="cmb'.$id.'_dia">';
$ultimoDia = mktime(0,0,0,$mes,1,$ano);
$ultimoDia = date('t',$ultimoDia);
for ($i = 1; $i <= $ultimoDia; $i++){
  	echo '<option ';
  	if($dia == $i)echo 'selected ';
  	echo 'value="'.$i.'">'.$i.'</option>'."\n";
}
echo '</select></div>';
}

function combosfecha1($ruta_http,$anoInicial,$anoFinal,$dia,$mes,$ano,$top,$left,$id){
//cmb que muestra rango de años a seleccion
echo '<select size="1" name="cmb'.$id.'_ano" class="default-style" style="position:absolute; top:'.$top.'px; left:'.$left.'px; padding:0; overflow:visible" onchange="javascript:cmbdia(\''.$ruta_http.'cmb_dia1.php\',\'cmb'.$id.'_dia\',\'cmb'.$id.'_mes\',\'cmb'.$id.'_ano\',\'div'.$id.'_dia\','.$id.')" id="cmb'.$id.'_ano">';
for ($i = $anoInicial; $i <= $anoFinal; $i++){
	echo '<option ';
  	if($ano == $i)echo 'selected ';
  	echo 'value="'.$i.'">'.$i.'</option>'."\n";
}
echo '</select>&nbsp;&nbsp;&nbsp;';
 
//cmb que muestra los meses del año.
$left=$left+57;
echo '<select size="1" name="cmb'.$id.'_mes" class="default-style" style="position:absolute; top:'.$top.'px; left:'.$left.'px; padding:0; overflow:visible" onchange="javascript:cmbdia(\''.$ruta_http.'cmb_dia1.php\',\'cmb'.$id.'_dia\',\'cmb'.$id.'_mes\',\'cmb'.$id.'_ano\',\'div'.$id.'_dia\','.$id.')" id="cmb'.$id.'_mes">';
$meses = Array ('ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE');
for($i = 1; $i <= 12; $i++){
  echo '<option '; 
  if($mes == $i)echo 'selected ';
  echo 'value="'.$i.'">'.$meses[$i-1].'</option>'."\n";
}
echo '</select>&nbsp;&nbsp;&nbsp;';

//cmb que muestra los dias del mes de acuerdo a lo que se escojio en los otros combos.
$left=$left+116;
echo '<div id="div'.$id.'_dia" class="default-style" style="position:absolute; top:'.$top.'px; left:'.$left.'px; padding:0; overflow:visible">
<select size="1" name="cmb'.$id.'_dia" class="default-style"  id="cmb'.$id.'_dia">';
$ultimoDia = mktime(0,0,0,$mes,1,$ano);
$ultimoDia = date('t',$ultimoDia);
for ($i = 1; $i <= $ultimoDia; $i++){
 $ldia=date("l", mktime(0,0,0,$mes,$i,$ano));
 if(!($ldia=="Sunday")){
  	echo '<option ';
  	if($dia == $i)echo 'selected ';
  	echo 'value="'.$i.'">'.$i.'</option>'."\n";
 }
}
echo '</select></div>';
}

function cmb_anidado($lbl1,$cmb1_array1,$cmb1_array2,$cmb1_rest,$lbl2,$cmb2_MyQuery,$cmb2_rest,$div_ajax){
 //toca construit una tabla para ingresar este codigo que construye las columnas con select anidados
 echo '<tr>
 <td style="text-align: right;" valign="undefined">'.$lbl1.'&nbsp;:</td>
 <td align="undefined" valign="undefined">
 <select size="1" name="actividades" onchange="javascript:cargarCombo(\''.$div_ajax.'\',\'actividades\',
 \'Div_Subactividades\')" id="actividades">';
 llenar_select($cmb1_array1,$cmb1_array2,$cmb1_rest);
 echo '</select>
 </td>
 </tr>
 <tr>
 <td style="text-align: right;" valign="undefined">'.$lbl2.'&nbsp;:</td>
 <td align="undefined" valign="undefined">
 <div id="Div_Subactividades" class="default-style" style="position:absolute; padding:0; overflow:visible">
 <select name="subactividades"  id="subactividades" class="default-style">';
 cargar_select($cmb2_MyQuery,$cmb2_rest);
 echo '</select>
 </div><br><br>
 </td>
 </tr>';
 echo '';
}

function cmb_anidado1($lbl1,$cmb1_MyQuery,$cmb1_rest,$lbl2,$cmb2_MyQuery,$cmb2_rest,$div_ajax){
 //toca construit una tabla para ingresar este codigo que construye las columnas con select anidados
 echo '<tr>
 <td style="text-align: right;" valign="undefined">'.$lbl1.'&nbsp;:</td>
 <td align="undefined" valign="undefined">
 <select size="1" name="actividades" id="actividades"
 onchange="javascript:cargarCombo(\''.$div_ajax.'\',\'actividades\',\'Div_Subactividades\')">';
 cargar_select($cmb1_MyQuery,$cmb1_rest);
 echo '</select>
 </td>
 </tr>
 <tr>
 <td style="text-align: right;" valign="undefined">'.$lbl2.'&nbsp;:</td>
 <td align="undefined" valign="undefined">
 <div id="Div_Subactividades" class="default-style" style="position:absolute; padding:0; overflow:visible">
 <select size="1" name="subactividades"  id="subactividades">';
 cargar_select($cmb2_MyQuery,$cmb2_rest);
 echo '</select>
 </div><br><br>
 </td>
 </tr>';
 echo '';
}

function cmb_anidado2($lbl1,$cmb1_MyQuery,$cmb1_rest,$lbl2,$cmb2_MyQuery,$cmb2_rest,$div_ajax){
 //toca construit una tabla para ingresar este codigo que construye las columnas con select anidados
 echo '<tr>
 <td style="text-align: right;  width: 19%;" valign="undefined">'.$lbl1.'&nbsp;:</td>
 <td style="text-align: left;  width: 31%;" valign="undefined">
 <select size="1" name="actividades" id="actividades"
 onchange="javascript:cargarCombo(\''.$div_ajax.'\',\'actividades\',\'Div_Subactividades\')">';
 cargar_select($cmb1_MyQuery,$cmb1_rest);
 echo '</select>
 </td>
 <td style="text-align: right;  width: 19%;" valign="undefined">'.$lbl2.'&nbsp;:</td>
 <td style="text-align: left;  width: 31%;" valign="undefined">
 <div id="Div_Subactividades" class="default-style" style="position:absolute; padding:0; overflow:visible">
 <select size="1" name="subactividades"  id="subactividades">';
 cargar_select($cmb2_MyQuery,$cmb2_rest);
 echo '</select>
 </div><br><br>
 </td>
 </tr>';
 echo '';
}
?>