<?php
include('inc/funciones_.php');
	html();
	echo "
	<script language='javascript'>
		var Posicion=1;
		function cambia(P)
		{
			document.getElementById('p_'+P).style.backgroundColor='ffff00';
		}
		function restablece(P)
		{
			document.getElementById('p_'+P).style.backgroundColor='ffffff';
		}

		function vk(Evento,objeto,posicion)
		{
			var keynum;
			var Caracter;
			if(window.event) // IE
				keynum = Evento.keyCode;
			else if(Evento.which) // Netscape/Firefox/Opera
				keynum = Evento.which;
			if(keynum==40 && Posicion<5)   // flecha abajo
			{
				restablece(Posicion);
				Posicion++;
				cambia(Posicion);
				return
			}
			if(keynum==38 && Posicion>1)  // flecha arriba
			{
				restablece(Posicion);
				Posicion--;
				cambia(Posicion);
				return
			}
			if(keynum==113)
			{
				alert('acabo de presionar F2');
			}
			alert(keynum);
		}
	</script>
	<body onload='centrar(500,500); cambia(1)'  onkeyup='vk(event);'>
	<table border>
	<tr id='p_1'><td>primer campo</td></tr>
	<tr id='p_2'><td>segundo campo</td></tr>
	<tr id='p_3'><td>tercer campo</td></tr>
	<tr id='p_4'><td>cuarto campo</td></tr>
	<tr id='p_5'><td>quinto campo</td></tr>
	</table>
	";

?>