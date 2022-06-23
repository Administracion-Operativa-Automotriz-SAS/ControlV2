<?php

// PROGRAMA QUE dispara la toma de imagenes de un servicio de prueba en el area de desarrollo de AOA

echo "<body>
	<form action='http://app.aoacolombia.com/Control/desarrollo/m.aoacontrol.util.php' method='post' target='_self' name='forma' id='forma'>
	Id de la cita: <input type='number' name='id' id='id' value='' size='10' maxlength='10' placeholder='Id de la cita'>
	<input type='hidden' name='Acc' id='Acc' value='toma_imagenes_entrega'>
	<input type='submit' value='Continuar' >
</form>";

?>