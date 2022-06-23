<?php
	IF(!$Salida) $Salida = 'plano.txt';
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"$Salida\"");
	header("Content-Description: File Transfert");
	@readfile($Archivo);
?>