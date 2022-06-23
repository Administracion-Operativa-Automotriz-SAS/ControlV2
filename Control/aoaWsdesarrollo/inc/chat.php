<?php

/**
 * FUNCIONES DE CHAT
 *
 * estas funciones se incluyen automáticamente desde Funciones y Marcoindex.php cuando se invoca cualquier Acc que tenga la palabra chat_
 *
 * @version $Id$
 * @copyright 2010
 */


///////////////////////////////    FUNCIONES DE CHAT  ////////////////////////////

function chat_conversacion()
{
	global $U,$NU,$EU,$Momento,$i;

	if(!$Momento) $Momento=date('Y-m-d H:i:s');
	else
	{
		q("update chat_histo set visto=1 where usuario1='".$_SESSION['Nick']."' and usuario2='$U' and visto=0 ");
	}
	html("$NU");
	$Nombre_cookie='Chat_'.$U;
	$Nick=$_SESSION['Nick'];$Nombre=$_SESSION['Nombre'];
	echo "<script language='javascript'>
			var Momento='$Momento';
			var Chat_estados=new Array('','gifs/chat/disponible.png','gifs/chat/ocupado.png','gifs/chat/ausente.png');
			function carga()
			{
				document.getElementById('estado').innerHTML=\"<img src='\"+Chat_estados[$EU]+\"' border='0'>\";
				document.forma.mensaje.focus();
				setCookie('$Nombre_cookie','$U');
			}

			function muestra_conversacion()
			{
				document.getElementById('conversacion').src='marcoindex.php?Acc=chat_trae&U1=$Nick&U2=$U&M=$Momento';
			}

			function traer_anterior(id)
			{
				window.open('marcoindex.php?Acc=chat_traer_anterior&id='+id,'_self');
			}

		</script>
		<body onload='carga()' onunload=\"delCookie('$Nombre_cookie');\">
		<iframe name='conversacion' id='conversacion' width='100%' height='300' src='marcoindex.php?Acc=chat_trae&U1=$Nick&U2=$U&M=$Momento' frameborder='no'></iframe>
		<form action='marcoindex.php' method='post' target='chat_oculto' name='forma' id='forma'>
			<table align='center'><tr><td><span id='estado'></span></td>
			<td><input type='text' name='mensaje' size='50'></td><td><input type='submit' value='Enviar' style='font-size:7'></td></tr></table>
			<input type='hidden' name='Acc' value='chat_enviar_mensaje'>
			<input type='hidden' name='Momento' value='$Momento'>
			<input type='hidden' name='Usuario' value='$U'>
			<input type='hidden' name='NUsuario' value='$NU'>
		</form>
		<iframe name='chat_oculto' id='chat_oculto' height='1' width='1' style='visibility:hidden'></iframe>Conversaciones anteriores:".
		menu1("ant","select id,concat(usuario2,' - ',fecha) from chat_histo where usuario1='$Nick' order by fecha desc ",0,1,""," onchange='traer_anterior(this.value);' ")
		."
		</body>";
}

function chat_enviar_mensaje()
{
	global $mensaje,$Momento,$Usuario,$NUsuario;
	echo "<body>";
	if(trim($mensaje))
	{
		$Nick=$_SESSION['Nick'];$Nombre=$_SESSION['Nombre'];$Hora=date('Y-m-d H:i');
		q("insert ignore into chat_histo (usuario1,usuario2,fecha) values ('$Nick','$Usuario','$Momento')");
		q("insert ignore into chat_histo (usuario1,usuario2,fecha) values ('$Usuario','$Nick','$Momento')");
		q("update chat_histo set conversacion=concat(conversacion,\"\n<b style='color:55ff55'>Yo:</b> [<font color='aaaaaa' style='font-size:8'>$Hora</font>]<br>$mensaje \"), visto=1 where usuario1='$Nick' and usuario2='$Usuario' and fecha='$Momento' ");
		q("update chat_histo set conversacion=concat(conversacion,\"\n<b style='color:5555ff'>$Nombre:</b> [<font color='aaaaaa' style='font-size:8'>$Hora</font>]<br>$mensaje\"),visto=0 where usuario1='$Usuario' and usuario2='$Nick' and fecha='$Momento'");
	}
	echo "
		<script language='javascript'>
		parent.document.forma.mensaje.value='';
		parent.muestra_conversacion();
		parent.document.forma.mensaje.focus();

		</script>
		</body>";
}

function chat_trae()
{
	global $U1,$U2,$M;
	if($D=qo("select conversacion from chat_histo where usuario1='$U1' and usuario2='$U2' and fecha='$M' "))
	{
		html();
		echo "<script language='javascript'>
				function repetir()
				{
					window.open('marcoindex.php?Acc=chat_trae&U1=$U1&U2=$U2&M=$M','_self');
				}

				Repetir=setTimeout(repetir,8000);

			</script>
			<body topmargin='0' leftmargin='0' rightmargin='0'>".nl2br($D->conversacion)."<br /><br /><br /><br /><br />
			<script language='javascript'>document.body.scrollTop=document.body.clientHeight+1000;</script></body>";
	}
	else
	{
		html();
		echo "<body></body>";
	}
}

function chat_traer_anterior()
{
	global $id;
	if($D=qo("select * from chat_histo where id=$id"))
	{
		echo "<body><script language='javascript'>window.open('marcoindex.php?Acc=chat_conversacion&U=$D->usuario2&EU=2&Momento=$D->fecha','_self');
		</script></body>";
	}
}

?>