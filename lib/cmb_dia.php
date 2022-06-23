<?php
$dia = $_REQUEST['dia'];
$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$id = $_REQUEST['id'];
echo '<select size="1" name="cmb'.$id.'_dia" class="default-style"  id="cmb'.$id.'_dia">';
$ultimoDia = mktime(0,0,0,$mes,1,$ano);
$ultimoDia = date('t',$ultimoDia);
for ($i = 1; $i <= $ultimoDia; $i++){
  	echo '<option ';
  	if($dia == $i)echo 'selected ';
  	echo 'value="'.$i.'">'.$i.'</option>'."\n";
}
echo '</select>';
?>
