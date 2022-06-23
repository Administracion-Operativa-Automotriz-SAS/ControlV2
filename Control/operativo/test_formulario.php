<?php
if(isset($_POST['formulario'])){
	$name = $_FILES['img_odo_salida_f'];
	
	var_dump($name);
}

?>

<form method='post' enctype='multipart/form-data' action='test_formulario.php'>
Subir Imagen: <input type='file' name='img_odo_salida_f'>
<input type='hidden' name='formulario'>
<input type='submit' value='Subir'>
</form>
