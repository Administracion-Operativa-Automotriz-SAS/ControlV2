<?php
  $directorio='planos/';
  $name = $_FILES["imagen"]["name"];
	$type = $_FILES["imagen"]["type"];
	$tmp_name = $_FILES["imagen"]["tmp_name"];
	$size = $_FILES["imagen"]["size"];
  $File_destino=$directorio.$name;
  @copy($tmp_name, $File_destino);
  unlink($tmp_name);
?>