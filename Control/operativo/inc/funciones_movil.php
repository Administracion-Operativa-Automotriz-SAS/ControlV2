<?php

/* ARCHIVO DE FUNCIONES PARA HTML5 Y MOVILES */

function cabecera_movil($Titulo='')
{
	?>
	<HTML><TITLE><?php echo $Titulo?></TITLE>
	<head>
	 <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<style type='text/css'>@import url(<?php echo ESTILO_MOVIL; ?>);</style>
	<script language='javascript' src='inc/js/funciones.js'></script>
	<script language='javascript' src='inc/js/valida_campos2.js'></script>
	<?
}

function app_crear_perfiles()
{
	include('inc/link.php');
	$idPerfil=array();$Contador=0;
	foreach($_SESSION[USER]->Perfiles as $Per)
	{
		$_SESSION[USER]->Perfiles[$Per->Id]->Mr=Array();
		$idPerfil[$Contador]=$Per->Id;$Contador++;
	}
	for($Contador=0;$Contador<count($idPerfil);$Contador++)
	{
		$idP=$idPerfil[$Contador];
		$Tipos=mysql_query("Select distinct tipo from usuario_tab where usuario=$idP order by orden,tipo",$LINK);
		$Contador2=0;
		while($T=mysql_fetch_object($Tipos))
		{
			$_SESSION[USER]->Perfiles[$idP]->Menu[$Contador2]=new AQRMenu($T->tipo,$idP,$LINK);
			$Contador2++;
		}
		if(!$_SESSION[USER]->Cambia_clave)
		{
			if($_SESSION[USER]->Perfiles[$idP]->Cambia_clave)
			{
				$_SESSION[USER]->Cambia_clave=1;
			}
		}
	}
	mysql_close($LINK);
}

function mata_perfil_movil() // Funcion que elimina las variables de sesion para pedir nuevamente usuario y contraseña
{
	global $app;
	session_start();session_unset();session_destroy();
	if(defined('COOKIE_APP_DATA1') && defined('COOKIE_APP_DATA2')) {setcookie(COOKIE_APP_DATA1, false, time()-10);setcookie(COOKIE_APP_DATA2, false, time()-10);}
	echo "<body><script language='javascript'>window.open('$app','_self');</script></body>";
}

function verificar_notificaciones()
{
	$usuario=u('nick');
	$Ahora=date('Y-m-d');
	if($Notificaciones=q("select * from notificacion where para='$usuario' and (leido=0 or vence>='$Ahora') order by momento"))
	{
		echo "<table border cellspacing='0' bgcolor='#ffffff' width='90%' align='center'><tr><th style='color:000000;'>Notificaciones:</th></tr>";
		while($Nota=mysql_fetch_object($Notificaciones))
		{
			echo "<tr><td style='padding:10px;color:000000;'>De: <b>$Nota->de</b><br>Fecha:$Nota->momento<br>$Nota->mensaje</td></tr>";
			if($Nota->leido==0) q("update notificacion set leido=1 where id=$Nota->id");
		}
		echo "</table>";
	}
	//else echo $usuario;
}

function pintar_pos_gps()
{
	global $app;
	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><html xmlns='http://www.w3.org/1999/xhtml'>
	<head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
			<script language='javascript'>
				function exito(position) {window.open('$app?Acc=guardar_pos_gps&latitud='+position.coords.latitude+'&longitud='+position.coords.longitude,'Guardar_pos_gps');}
				function error(msg) {alert(typeof msg == 'string' ? msg : 'failed');}
				function enviar_pos() {var options= {enableHighAccuracy:true,timeout:10000,maximumAge:0};if(navigator.geolocation) navigator.geolocation.getCurrentPosition(exito, error, options); else  error('not supported');}
			</script>
	</head><body onload='enviar_pos();'><iframe name='Guardar_pos_gps' id='Guardar_pos_gps' style='display:none' width='1' height='1'></iframe></body></html>";
}

function pintar_agente()
{
	cabecera_movil(TITULO_APP);
	echo "<script language='javascript'>
		function regresar() {window.open('$app?Acc=$app','_self');}
	</script>
	<body>".IMG_CABEZA."<h3 align='center'>".TITULO_APP." - Enviar Notificacion</h3>
		AGENTE: ".$_SERVER['HTTP_USER_AGENT']."
		<input type='button' name='cerrar_sesion' id='cerrar_sesion' value=' REGRESAR ' onclick=\"regresar();\">
		<br><br><br><br><br><br><br>
	</body>";
}

class control_parse
{
	var $App_id=PASARELA_App_id;
	var $Rest_Api_key=PASARELA_Api_Key;
	var $Master_key=PASARELA_Master_Key;
	var $Campo_parse_usuario='email';
	var $Campo_parse_nombre='nombre';
	var $URL_parse='https://api.parse.com/1/';
	// DEFINICION DE CAMPOS DEL PORTAL
	var $Tablas_usuarios=array(); // tablas que se va a usar para obtener un identificador de la plataforma
	var $Usuarios='usuario_parse';

	function control_parse()
	{
		$Usuarios_control_parse=q("select alt_tabla,alt_nombre,alt_id from usuario where control_parse=1");
		$Contador=0;
		while($UCP=mysql_fetch_object($Usuarios_control_parse))
		{
			$this->Tablas_usuarios[$Contador]=array('tabla_usuario' =>$UCP->alt_tabla,'campo_nombre'=>$UCP->alt_nombre,'campo_usuario'=>$UCP->alt_id);
			$Contador++;
		}
	}

	function genera_inscripcion()
	{
		error_reporting(E_ALL);
		if(isset($_COOKIE[COOKIE_PARSE_MOMENTO]) && isset($_COOKIE[COOKIE_PARSE_OBJETO]))
		{
			if(!$_COOKIE[COOKIE_PARSE_OBJETO]) return false;
			foreach($this->Tablas_usuarios as $TU)
			{
				//debug_movil('Buscando perfil en '.$TU['tabla_usuario']);
				$query="select ".$TU['campo_nombre']." as nombre, ".$TU['campo_usuario']." as usuario from ".$TU['tabla_usuario']." where ".$TU['campo_usuario']."='".u('nick')."'";

				if($U=qo($query))
				{
					$U->nombre=quitatildes($U->nombre);
					//debug_movil('Perfil encontrado '.$U->nombre);
					if($Existe=qo("select * from ".$this->Usuarios." where usuario_parse='$U->usuario' "))
					{
						//debug_movil("Existe en ".$this->Usuarios." id= $Existe->id objeto: $Existe->objectid");
						if($Existe->objectid !=$_COOKIE[COOKIE_PARSE_OBJETO]) // cambio de objectid de parse
						{
							//debug_movil("borrando el objeto de parse. Objeto anterior: ".$_COOKIE[COOKIE_PARSE_OBJETO]);
							$url=$this->URL_parse.'installations/'.$Existe->objectid;
							$c = curl_init();
							curl_setopt($c,CURLOPT_URL,$url);
							curl_setopt($c,CURLOPT_PORT,443);
							curl_setopt($c,CURLOPT_CUSTOMREQUEST,'DELETE');
							curl_setopt($c,CURLOPT_HTTPHEADER,array("X-Parse-Application-Id: ".$this->App_id,"X-Parse-Master-Key: ".$this->Master_key,"Content-Type: application/json"));
							$respuesta = curl_exec($c);
							curl_close($c);
							//debug_movil("Asignando datos al nuevo objeto en parse $U->usuario $U->nombre");
							$url=$this->URL_parse.'installations/'.$_COOKIE[COOKIE_PARSE_OBJETO];
							$comando_push =json_encode(array($this->Campo_parse_usuario=>"$U->usuario",$this->Campo_parse_nombre=>"$U->nombre"));
							$c = curl_init();
							curl_setopt($c,CURLOPT_URL,$url);
							curl_setopt($c,CURLOPT_PORT,443);
							curl_setopt($c,CURLOPT_CUSTOMREQUEST,'PUT');
							curl_setopt($c,CURLOPT_POSTFIELDS,$comando_push);
							curl_setopt($c,CURLOPT_HTTPHEADER,array("X-Parse-Application-Id: ".$this->App_id,"X-Parse-Master-Key: ".$this->Master_key,"Content-Type: application/json"));
							$respuesta = curl_exec($c);
							curl_close($c);
							//debug_movil("actualizando ".$this->Usuarios." Momento: ".$_COOKIE[COOKIE_PARSE_MOMENTO]." Objeto: ".$_COOKIE[COOKIE_PARSE_OBJETO]);
							q("update ".$this->Usuarios." set momento_inscripcion='".$_COOKIE[COOKIE_PARSE_MOMENTO]."',nombre='$U->nombre',objectid='".$_COOKIE[COOKIE_PARSE_OBJETO]."' where usuario_parse='$U->usuario' ");
							return 'Actualizacion de Instalacion '.$U->nombre;
						}
					}
					else
					{
						//debug_movil("No existia en ".$this->Usuarios." asignando datos en parse ".$_COOKIE[COOKIE_PARSE_OBJETO]);
						$url=$this->URL_parse.'installations/'.$_COOKIE[COOKIE_PARSE_OBJETO];
						$comando_push =json_encode(array($this->Campo_parse_usuario=>"$U->usuario",$this->Campo_parse_nombre=>"$U->nombre"));
						$c = curl_init();
						curl_setopt($c,CURLOPT_URL,$url);
						curl_setopt($c,CURLOPT_PORT,443);
						curl_setopt($c,CURLOPT_CUSTOMREQUEST,'PUT');
						curl_setopt($c,CURLOPT_POSTFIELDS,$comando_push);
						curl_setopt($c,CURLOPT_HTTPHEADER,array("X-Parse-Application-Id: ".$this->App_id,"X-Parse-Master-Key: ".$this->Master_key,"Content-Type: application/json"));
						$respuesta = curl_exec($c);
						curl_close($c);
						// VERIFICACION SI EL OBJECT ID ya existia
						if($Existe=qo("select * from ".$this->Usuarios." where objectid = '".$_COOKIE[COOKIE_PARSE_OBJETO]."' "))
						{
							//debug_movil("actualiza en ".$this->Usuarios." porque el objeto ya existia ");
							q("update ".$this->Usuarios." set momento_inscripcion='".$_COOKIE[COOKIE_PARSE_MOMENTO]."',nombre='$U->nombre',usuario_parse='$U->usuario'  where objectid='".$_COOKIE[COOKIE_PARSE_OBJETO]."'  ");
						}
						else
						{
							//debug_movil("inserta en ".$this->Usuarios." $U->usuario ");
							q("insert into ".$this->Usuarios." (usuario_parse) values ('$U->usuario')");
							//debug_movil("insertando nuevos datos objectid ".$_COOKIE[COOKIE_PARSE_OBJETO]." Momento: ".$_COOKIE[COOKIE_PARSE_MOMENTO]);
							q("update ".$this->Usuarios." set momento_inscripcion='".$_COOKIE[COOKIE_PARSE_MOMENTO]."',nombre='$U->nombre',objectid='".$_COOKIE[COOKIE_PARSE_OBJETO]."' where usuario_parse='$U->usuario' ");
						}
						//debug_movil("crea la cookie de instalacion con el dato del objeto");
						setcookie(COOKIE_PARSE_INSCRITO,$_COOKIE[COOKIE_PARSE_OBJETO],time()+(90*24*60*60));
						return 'Registro de nueva instalacion '.$U->nombre;
					}
					break;
				}
			}
			return false;
		}
		else echo "No hay cookies ";
		return false;
	}

	function ultimo_objeto()
	{
		$query=urlencode('order=-createdAt');
		$limite=urlencode('limit=1');
		$url = $this->URL_parse.'installations?'.$query.'&'.$limite;
		$c = curl_init();
		curl_setopt($c,CURLOPT_URL,$url);
		curl_setopt($c,CURLOPT_HTTPGET,true);
		curl_setopt($c,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c,CURLOPT_HTTPHEADER,array("X-Parse-Application-Id: ".$this->App_id,"X-Parse-Master-Key: ".$this->Master_key,"Content-Type: application/json"));
		$respuesta = curl_exec($c);
		curl_close($c);
		$Respuesta=json_decode($respuesta);
		if(isset($Respuesta))
		{
			if(!isset($Respuesta->results[0]->email)) return $Respuesta->results[0]->objectId;
		}
		else return false;
	}

	function obtener_usuarios_option()
	{
		$url = $this->URL_parse.'installations';
		$c = curl_init();
		curl_setopt($c,CURLOPT_URL,$url);
		curl_setopt($c,CURLOPT_HTTPGET,true);
		curl_setopt($c,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c,CURLOPT_HTTPHEADER,array("X-Parse-Application-Id: ".$this->App_id,"X-Parse-Master-Key: ".$this->Master_key,"Content-Type: application/json"));
		$respuesta = curl_exec($c);
		curl_close($c);
		$Usuarios=json_decode($respuesta);
		$Options="";
		foreach($Usuarios->results as $Instalacion) eval("\$Options.=\"<option value='\$Instalacion->".$this->Campo_parse_usuario."'>\$Instalacion->".$this->Campo_parse_nombre."</option>\";");
		return $Options;
	}

	function enviar_notificacion($destino='',$mensaje='')
	{
		$url = $this->URL_parse.'push';
		$push_payload = json_encode(array("where" => array($this->Campo_parse_usuario=> $destino),"data" => 	array("alert" => $mensaje)));
		$c=curl_init();
		curl_setopt($c,CURLOPT_URL,$url);
		curl_setopt($c,CURLOPT_PORT,443);
		curl_setopt($c,CURLOPT_POST,1);
		curl_setopt($c,CURLOPT_POSTFIELDS,$push_payload);
		curl_setopt($c,CURLOPT_HTTPHEADER,array("X-Parse-Application-Id: ".$this->App_id,"X-Parse-REST-API-Key: ".$this->Rest_Api_key,"Content-Type: application/json"));
		$respuesta = curl_exec($c);
		curl_close($c);
	}

	function enviar_notificaciones($destino='',$mensaje='')
	{
		$url = $this->URL_parse.'push';
		$destinos=str_replace(',','","',$destino);
		$push_payload = '{"where":{"email":{"$in":["'.$destinos.'"]}},"data":{"alert":"'.$mensaje.'"}}';
		$c=curl_init();
		curl_setopt($c,CURLOPT_URL,$url);
		curl_setopt($c,CURLOPT_PORT,443);
		curl_setopt($c,CURLOPT_POST,1);
		curl_setopt($c,CURLOPT_POSTFIELDS,$push_payload);
		curl_setopt($c,CURLOPT_HTTPHEADER,array("X-Parse-Application-Id: ".$this->App_id,"X-Parse-REST-API-Key: ".$this->Rest_Api_key,"Content-Type: application/json"));
		$respuesta = curl_exec($c);
		curl_close($c);
	}

}

function debug_movil($linea)
{
	//return true;
	$Fin_de_linea="\r\n";
	$f=fopen('planos/debug.txt','a');
	fwrite($f,$linea.$Fin_de_linea);
	fclose($f);
}
?>