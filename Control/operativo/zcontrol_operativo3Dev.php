<?php
/*
Programa para presentar en forma de cuadricula los estados de los vehiculos, en el eje y son las placas por ciudades, flotas, marcas, en el eje x estan dias del mes y
el contenido de las celdas internas son los estados de los vehiculos en el tiempo.

Este módulo permite adicionar estados a los vehículos de acuerdo a los perfiles de seguridad.

*/


include('inc/funciones_.php');
set_time_limit(0);
sesion(); // verifica la sesion del usuario si está activa.
$USUARIO=$_SESSION['User']; // obtiene el perfil del usuario
if($USUARIO>99) // si el perfil del usuario sobrepasa el id 99 no deja entrar al usuario
{ html('TABLA DE CONTROL');
    echo"<body><script language='javascript'>centrar(300,300);</script>
    Este módulo está en mantenimiento. Pronto estará al aire nuevamente.
    Atte. AOA COLOMBIA S.A.
    </body>";
    die(); }
$NUSUARIO=$_SESSION['Nombre']; // obtiene el nombre del usuario
$Tem_Disponibles='tmpi_disponibles_'.$USUARIO.'_'.$_SESSION['Id_alterno']; // tabla temporal para los disponibles
$OFIU=0; // variable de filtro para los perfiles que solo deben ver una ciudad
$ASEU=0; // variable de filtro para los perfiles que solo deben ver una aseguradora
$Odofinal=0; // variable de trabajo
$Siniestros_propios=0; // contador de siniestros de los vehiculos de AOA
$US=array(); // arreglo de ubicaciones
$CS=array();  // arreglo de citas de servicio
$NTcitas=tu('cita_servicio','id');  // obtiene el id del acceso de tabla del usuario de la tabla CITA_SERVICIO
$Hoy=date('Y-m-d'); // fecha actual aaaa-mm-dd
$Autorizaciones=array(); // Arreglo de autorizaciones activas

// $OFIU=2; /* MEDELLIN */

include('inc/link.php');

// para ciertos perfiles el sistema obtiene la oficina a la que pertenece cada usuario al momento del ingreso a este programa.

if($USUARIO==10 /* operario de oficina */) $OFIU=qo1m("select oficina from usuario_oficina where id=".$_SESSION['Id_alterno'],$LINK);
if(inlist($USUARIO,'11,29,')/* Aseguradora 1*/)
{
	$ASEU=qo1m("select aseguradora from  usuario_aseguradora2 where id=".$_SESSION['Id_alterno'],$LINK);
	if($ASEU==4) $ASEU='4,10';
}
if($USUARIO==23 /* Operarios de flotas */) $OFIU=qo1m("select oficina from operario where id=".$_SESSION['Id_alterno'],$LINK);
if($USUARIO==32 /* Recepcion */) $OFIU=qo1m("select oficina from usuario_recepcion where id=".$_SESSION['Id_alterno'],$LINK);
if($USUARIO==13 /* Auxiliar de Información */) $OFIU=qo1m("select oficina from usuario_auxiliarop where id=".$_SESSION['Id_alterno'],$LINK);
//if($_SESSION['Adjudicacion_OFICINA']) {$OFIU=$_SESSION['Adjudicacion_OFICINA'];}
//if($_SESSION['Adjudicacion_ASEGURADORA']) {$ASEU=$_SESSION['Adjudicacion_ASEGURADORA'];}


// CREA UN ARREGLO CON LOS ESTADOS DE LOS VEHICULOS
$Estados=mysql_query("select * from estado_vehiculo  order by id",$LINK);
$EST=array();
while($e=mysql_fetch_object($Estados)) $EST[$e->id]=$e;
// CREA UN ARREGLO CON LOS EMBLEMAS DE LAS ASEGURADORAS
$Emb_Aseg=array();
$Asegs=mysql_query("select id,emblema_f from aseguradora",$LINK);
while($A=mysql_fetch_object($Asegs)) $Emb_Aseg[$A->id]=$A->emblema_f;

$Nsin=array();   /// ARREGLO DE SINIESTROS CON UBICACIONES LOS QUE NO TIENEN UBICACIONES NO ESTAN EN ESTE ARREGLO
$Hdevolucion=array(); // ARREGLO DE HORAS DE DEVOLUCION POR SINIESTRO
$PyP=array(); // ARREGLO DE PICOS Y PLACAS

if($FF && $FI)
{
	// POBLA ARREGLO DE PICOS Y PLACAS POR OFICINA
	if(!$Picos=mysql_query("select o.id as oficina,p.ciudad,p.fecha_inicial,p.fecha_final,p.dia,p.placas from picoyplaca p, oficina o
						where o.ciudad=p.ciudad and p.fecha_inicial<='$FF' and p.fecha_final>='$FI' ",$LINK)) echo mysql_error();
	while($P=mysql_fetch_object($Picos)) {$PyP[$P->oficina][$P->dia]=$P;}
}
mysql_close($LINK);

$Efectividad=array();  // ARREGLO DE EFECTIVIDADES
$Aplacas=array(); // Arreglo de Placas Activas
$AAlertas=array(); // Arreglo de Placas con Alertas

class cls_efectividad
{
	var $Servicios=0; // contador de numero de servicios
	var $Siniestralidad=0; // Siniestralidad de los vehiculos de AOA por culpa de los asegurados
	var $Correctivos=0; // fueras de servicio por arreglos o talleres correctivos
	var $Preventivos=0; // mantenimientos programados
	var $Parqueaderos=0; // parqueaderos, alistamientos
	var $Otros=0; // demas estados de los vehiculos.
	var $Periodo=''; // Año y mes

	function cls_efectividad($Per)
	{
		$this->Periodo=$Per; // crea una instancia con el periodo
	}

	function acumula($Tipo) // de acuerdo al tipo de actividad, acumula en cada variable de la clase
	{
		switch($Tipo)
		{
			case 'serv': $this->Servicios++; break;
			case 'sin': $this->Siniestralidad++; break;
			case 'corr': $this->Correctivos++; break;
			case 'prev': $this->Preventivos++; break;
			case 'parq': $this->Parqueaderos++; break;
			case 'otr': $this->Otros++; break;
		}
	}

	/*
	Esta función pinta cada uno de los acumuladores de los vehículos calculando los porcentajes de cada evento
	*/
	function pinta($Carros /* cantidad de carros */)
	{
		$Total=$this->Servicios+$this->Siniestralidad+$this->Correctivos+$this->Preventivos+$this->Parqueaderos+$this->Otros;
		if($Total)
		{
			$PServicios=round($this->Servicios/$Total*100,2); // obtiene el porcentaje de servicios frente a los demás eventos
			$Efectividad_mes=round($this->Servicios/7/$Carros,2); // obtiene el porcentaje de Efectividad sotre el total de vehiculos
			//$Efectividad_mes=round(4*$PServicios/100,2);
			$PSiniestralidad=round($this->Siniestralidad/$Total*100,2); // obtiene el porcentaje de siniestralidad frente a los demás eventos
			$PCorrectivos=round($this->Correctivos/$Total*100,2); // obtiene el porcentaje de mantenimientos correctivos frente a los demás eventos
			$PPreventivos=round($this->Preventivos/$Total*100,2); // obtiene el porcentaje de mantenimientos preventivos frente a los dmeás eventos
			$PParqueaderos=round($this->Parqueaderos/$Total*100,2); // obtiene el porcentaje de parqueaderos frente a los demás eventos
			$POtros=round($this->Otros/$Total*100,2); // obtiene el porcentaje de los demás eventos distintos de servicios, fuera de servicio y mentenimientos frente a todos los eventos
		}
		else $PServicios=$PSiniestralidad=$PCorrectivos=$PPreventivos=$PParqueaderos=$POtros=0;
		// pinta todos los resultados.
		echo "<table border cellspacing='0'><tr><th colspan='3'>Periodo: $this->Periodo</th></tr>
				<tr><td>Servicio</td><td align='right'>".coma_format($this->Servicios)."</td><td align='right' nowrap='yes'>".coma_formatd($PServicios,2)." %</td></tr>
				<tr><td>Siniestralidad</td><td align='right'>".coma_format($this->Siniestralidad)."</td><td align='right' nowrap='yes'>".coma_formatd($PSiniestralidad,2)." %</td></tr>
				<tr><td>Correctivos</td><td align='right'>".coma_format($this->Correctivos)."</td><td align='right' nowrap='yes'>".coma_formatd($PCorrectivos,2)." %</td></tr>
				<tr><td>Preventivos</td><td align='right'>".coma_format($this->Preventivos)."</td><td align='right' nowrap='yes'>".coma_formatd($PPreventivos,2)." %</td></tr>
				<tr><td>Parqueaderos</td><td align='right'>".coma_format($this->Parqueaderos)."</td><td align='right' nowrap='yes'>".coma_formatd($PParqueaderos,2)." %</td></tr>
				<tr><td>Otros</td><td align='right'>".coma_format($this->Otros)."</td><td align='right' nowrap='yes'>".coma_formatd($POtros,2)." %</td></tr>
				<tr><td nowrap='yes'>Total Eventos:</td><td align='right'>".coma_format($Total)."</td><td align='right' nowrap='yes'>100.00 %</td></tr>
				<tr><td nowrap='yes' bgcolor='ffffdd' colspan='2'>Efectividad del mes:</td><td align='right' bgcolor='ffffdd'>$Efectividad_mes</td></tr></table>";
	}
}


/*
Clase que sirve para pintar las placas en la zona lateral izquierda del tablero de control
*/
class cplaca
{
	var $id=0; // id de la tabla de vehiculos
	var $placa=''; // placa del vehiculo
	var $tipo_caja=''; //Tipo placa
	var $tipo_caja_sigla=''; //Tipo sigla
	var $tipo_traccion='';
	var $tipo_traccion_sigla ='';
	var $digito=0; // ultimo digito de la placa
	var $emblema_marca=''; // emblema de la linea del vehiculo
	var $flota=0; // flota a la que pertenece el vehiculo
	var $emblema_aseguradora=''; // emblema aseguradora
	var $sigla_oficina=''; // sigla de la oficina
	var $nombre_oficina=''; // nombre de la oficina
	var $ultima_ubicacion=0; // id de la ultima oficina
	var $linea=0; // id de la linea
	var $kilometraje=0; // odometro final
	var $mantenimiento_cada=0; // mantenimiento cada x kilometros
	var $ultimo_mantenimiento=0; // Kilometraje del ultimo mantenimiento
	var $ultimo_soat=''; // Fecha del último soat
	var $bgcolor_mantenimiento='eeeeee'; // color de fondo para la columna de mantenimiento
	var $bgcolor_soat='eeeeee';//color de fondo para la columna de SOAT
	var $bgcolor_rtm='eeeeee';//color de fondo para la columna de Revisión Técnico Mecánica
	var $proximo_mantenimiento=0; // proximo mantenimiento del vehiculo
	var $proximo_soat=''; // fecha del siguiente soat
	var $par=0; // control de par o impar
	var $primera_revisiontm=''; // Fecha de la primera Revisión Técnico Mecánica
	var $ultima_revisiontm='';  // Fecha de la última Revisión Técnico Mecáncia
	var $proxima_revisiontm=''; // Fecha de la próxima Revisión Técnico Mecánica
	var $Alertas=array(); // ARREGLO DE CONTROL DE ALERTAS POR PLACA

	function __construct($P)  // $P es la variable objeto que va a insertarse viene de un registro tipo select
	{
		$this->id=$P->id;
		$this->placa=$P->placa;
		$this->tipo_caja=$P->tipo_caja;
		$this->tipo_caja_sigla=$P->tipo_caja_sigla;
		$this->tipo_traccion=$P->tipo_traccion;
		$this->tipo_traccion_sigla=$P->tipo_traccion_sigla;
		
		$this->digito=r($P->placa,1); // obtiene el último dígito para controles de pico y placa
		$this->emblema_marca=$P->emb1; // emblema de la marca
		$this->flota=$P->flota; // flota primaria a la que pertenece
		$this->emblema_aseguradora=$P->emb3;// emblema de la aseguradora
		$this->sigla_oficina=$P->noficina; // sigla de la oficina
		$this->ultima_ubicacion=$P->ultima_ubicacion;// ultima ubicación del vehículo
		$this->linea=$P->linea; // linea de la marca a la que pertenece el vehículo
		$this->nombre_oficina=$P->nombre_oficina; // nombre de la oficina
		$this->mantenimiento_cada=$P->manten_cada; // cada cuantos km se hace mantenimiento
		$this->ultimo_mantenimiento=$P->ultimo_mantenimiento; // fecha del último mantenimiento
		$this->ultimo_soat=$P->ultimo_soat; // fecha del último soat
		$this->primera_revisiontm=$P->fecha_revisiontm; // fecha de la última revisión tecnico-mecánica
		$this->ultima_revisiontm=($P->ultima_revisiontm?$P->ultima_revisiontm:$P->fecha_matricula); // si no hay fecha de la última revisión tecnicomecánica, usa la fecha de la matricula del carro
		$this->par=$P->par; // averigua si es par el último dígito
	}

	function odofinal($odofinal,$LINK=0)
	{
		// verificacion del odometro final
		// si ya esta cerca de un mantenimiento o si ya es hora de hacer mantenimiento o si se pasó del limite del mantenimiento
		// en esta rutina también se verifica la proximidad del siguiente SOAT.
		global $Hoy;
		$this->kilometraje=$odofinal;

		/// las siguientes lineas serán eliminadas cuando entre en vigencia el nuevo sistema de alarmas

		// if($this->ultimo_mantenimiento<=$this->kilometraje)
		// {
			// $this->proximo_mantenimiento = $this->ultimo_mantenimiento+$this->mantenimiento_cada;
			// $Proximo_umbral=$this->proximo_mantenimiento-700;
			// if($this->kilometraje > $Proximo_umbral)
			// {
				// if($this->kilometraje > ($this->proximo_mantenimiento)) $this->bgcolor_mantenimiento='ff0000'; // si el odometro supera el próximo mantenimiento despues del umbral, se pone rojo
				// else $this->bgcolor_mantenimiento='ffff00';  // si el odometro supera el proximo mantenimiento dentro del umbral, se pone amarillo
			// }
		// }
		// $this->proximo_soat=date('Y-m-d',strtotime(aumentameses($this->ultimo_soat,12))); // calcula el tiempo del próximo soat
		// if(date('Y-m-d',strtotime($this->proximo_soat))<$dHoy) $this->bgcolor_soat='ff0000'; // si se pasa del tiempo del soat se pone rojo o si no se pone amarillo
		// elseif(date('Y-m-d',strtotime(aumentameses($this->proximo_soat,-1)))<$dHoy) $this->bgcolor_soat='ffff00';
		// $this->proxima_revisiontm=($this->ultima_revisiontm?date('Y-m-d',strtotime(aumentameses($this->ultima_revisiontm,12))):$this->fecha_matricula); // calcula la fecha de la proxima revison tm
		// if($this->primera_revisiontm>$this->proxima_revision) $this->proxima_revision=$this->primera_revisiontm; // si esta dentro del umbral se pone amarillo, si se pasa del umbral se pone rojo
		// if($this->proxima_revisiontm<$dHoy) $this->bgcolor_rtm='ff0000';
		// elseif(date('Y-m-d',strtotime(aumentameses($this->proxima_revisiontm,-1)))<$dHoy) $this->bgcolor_rtm='ffff00';

		/// fin de eliminacion de lineas cuando entre en vigencia el nuevo sistema de alertas

		$this->verifica_alertas($LINK);
	}

	function verifica_alertas($LINK=0)
	{
		$cfg_alertas=mysql_query("Select a.*,t.nombre,t.control,t.icono_f as icono from cfg_alerta_vehiculo a , tipo_alerta t where a.vehiculo=$this->id and a.alerta=t.id ",$LINK);
		if(mysql_num_rows($cfg_alertas))
		{
			while($al=mysql_fetch_object($cfg_alertas))
			{
				if($al->control=='K')
				{
					$amarillo=$al->ultimo_kilometraje+$al->kilo_amarillo;
					$rojo=$al->ultimo_kilometraje+$al->kilo_rojo;
					
					if($amarillo <= $this->kilometraje  && $this->kilometraje < $rojo)
					{
						$this->Alertas[]=new alertaplaca($al,'ffffaa',coma_format($rojo),$this->kilometraje);
					}
					elseif($rojo <= $this->kilometraje)
					{
						$this->Alertas[]=new alertaplaca($al,'ffaaaa',coma_format($rojo),$this->kilometraje);
					}
				}
				if($al->control=='T')
				{
					$proximo_control_fecha_amarillo=date('Y-m-d',strtotime(aumentadias($al->ultimo_fecha,$al->dias_amarillo)));
					$proximo_control_fecha_rojo=date('Y-m-d',strtotime(aumentadias($al->ultimo_fecha,$al->dias_rojo)));
					$Hoy=date('Y-m-d');
					if($proximo_control_fecha_amarillo <= $Hoy && $Hoy < $proximo_control_fecha_rojo)
					{
						$this->Alertas[]=new alertaplaca($al,'ffffaa',"$proximo_control_fecha_rojo",$Hoy);
					}
					elseif($proximo_control_fecha_rojo <= $Hoy)
					{
						$this->Alertas[]=new alertaplaca($al,'ffaaaa',"$proximo_control_fecha_rojo",$Hoy);
					}
				}
			}
		}
	}

	function pinta_alertas()
	{
		for($i=0;$i<count($this->Alertas);$i++)
			$this->Alertas[$i]->pinta_alerta();
	}

	function pinta_alerta()
	{
		global $AAlertas;
		// Esta función pinta el alerta de mantenimiento y/o soat, de acuerdo al tipo de alerta, pinta un link apropiado para actualizar un evento que permita retirar el alerta
		$Alerta='';
		if($this->bgcolor_mantenimiento!='eeeeee')
		{
			// pinta un botón para adicionar un mantenimiento
			$Alerta="&nbsp;<span style='background-color:".$this->bgcolor_mantenimiento.";cursor:pointer' onclick=\\\"adiciona_mantenimiento('".$this->placa."');\\\">".
			"<img src='img/mantenimiento.png' height='11' border='0' alt='Próximo Mantenimiento: ".coma_format($this->proximo_mantenimiento)."' title='Próximo Mantenimiento ".coma_format($this->proximo_mantenimiento)."'></span>";
			if(!$AAlertas[$this->placa]) $AAlertas[$this->placa]=new alerta_placa($this->placa);
			$AAlertas[$this->placa]->Kilometraje=$this->kilometraje;
			$AAlertas[$this->placa]->Mantenimiento=$this->proximo_mantenimiento; // acumula en el arreglo de alertas en el contador de mantenimientos
		}
		if($this->bgcolor_soat!='eeeeee')
		{
			// pinta un botón para adicionar un soat
			$Alerta.="&nbsp;<span style='background-color:".$this->bgcolor_soat.";cursor:pointer' onclick=\\\"adiciona_soat('".$this->placa."');\\\">".
			"<img src='img/soat.png' height='12' border='0' alt='Próximo Soat ".$this->proximo_soat."' title='Próximo Soat ".$this->proximo_soat."'></span>";
			if(!$AAlertas[$this->placa]) $AAlertas[$this->placa]=new alerta_placa($this->placa);
			$AAlertas[$this->placa]->Soat=$this->proximo_soat;// acumula en el arreglo de alertas en el contador de soat
		}
		if($this->bgcolor_rtm!='eeeeee')
		{
			// pinta un botón para adionar una revisión técnico mecánica
			$Alerta.="&nbsp;<span style='background-color:".$this->bgcolor_rtm.";cursor:pointer' onclick=\\\"adiciona_rtm('".$this->placa."');\\\">".
			"<img src='img/rtm3.png' height='12' border='0' alt='Próxima Revisión Técnico Mecánica ".$this->proxima_revisiontm."' title='Próxima Revisión Técnico Mecánica ".$this->proxima_revisiontm."'></span>";
			if(!$AAlertas[$this->placa]) $AAlertas[$this->placa]=new alerta_placa($this->placa);
			$AAlertas[$this->placa]->Revision=$this->proxima_revisiontm;// acumula en el arreglo de alertas en el contador de revisiones tm
		}
		// via javascript, actualiza las alertas en el tablero de control después de haberlo pintado todo
		if($Alerta) echo "<script language='javascript'>document.getElementById('al_".$this->id."').innerHTML=\"$Alerta\";</script>";
	}
}

class alerta_placa // clase anterior que muestra las alertas viejas
{
	var $Placa=''; // placa del vehiculo que tiene la alerta
	var $Kilometraje=0; // kilometraje actual del vehículo
	var $Mantenimiento=0; // próximo mantenimiento
	var $Soat=''; // próximo soat
	var $Revision=''; // próxima Revisión Técnico Mecánica

	function alerta_placa($Placa)
	{
		$this->Placa=$Placa;
	}
}

class alertaplaca  // clase nueva que muestra las alertas nuevas
{
	var $Id_vehiculo=0; // id del vehiculo
	var $Tipo_alerta=0; // id del tipo de alerta
	var $NTipo_alerta=''; // Nombre del tipo de alerta
	var $icono_alerta=''; // icono del alerta
	var $Control = ''; // Controll  K o T (por kilometraje o por tiempo)
	var $color_fondo='eeeeee'; // Color de fondo inicial
	var $Proximo=''; // proximo control
	var $Actual; // kilometraje o fecha actual

	function alertaplaca($Alerta,$fondo,$proximo,$actual)
	{
		$this->Id_vehiculo=$Alerta->vehiculo;
		$this->Tipo_alerta=$Alerta->alerta;
		$this->NTipo_alerta=$Alerta->nombre;
		$this->icono_alerta=$Alerta->icono;
		$this->Control=$Alerta->control;
		$this->color_fondo=$fondo;
		$this->Proximo=$proximo;
		$this->Actual=$actual;
	}

	function pinta_alerta()
	{
		$txtalerta="&nbsp;<a class='info' style='background-color:".$this->color_fondo.";padding:2px;cursor:pointer' onclick=\\\"control_alerta(".$this->Id_vehiculo.",".$this->Tipo_alerta.", '".$this->NTipo_alerta."','".$this->Actual."');\\\"><img src='".$this->icono_alerta."' height='12' border='0' alt='".$this->NTipo_alerta."".$this->Proximo."' title='".$this->NTipo_alerta." ".$this->Proximo."'><span><img src='$this->icono_alerta' height='50px'><br><b>$this->NTipo_alerta a $this->Proximo</b></span></a>";
		echo "<script language='javascript'>document.getElementById('al_".$this->Id_vehiculo."').innerHTML+=\"$txtalerta\";</script>";
	}
}


/////////////////////////////////////////////////  INICIO PROGRAMA PRINCIPAL //////////////////////////////////////////////////

if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');	die();}

inicio_operativo(); // rutina principal

function inicio_operativo()
{
	global $OFIU,$ASEU,$USUARIO;
	global $Instruccion_Externa;
	if($_SESSION['Adjudicacion_OFICINA']) // variable de sessión para las adjudicaciones desde Call Center
	{
		$OFIU=$_SESSION['Adjudicacion_OFICINA'];
		
	    if($companeras=qo1("select ofi_compas from oficina where id=$OFIU")) $OFIU=$compañeras;
		//if(inlist($OFIU,'1,11,12,13')) $OFIU='1,11,12,13'; // Si la oficina es cualquiera de Bogotá, muestra las 4 sedes
	}
	if($_SESSION['Adjudicacion_ASEGURADORA'] )
	{
		if($_SESSION['Adjudicacion_NIVEL']<2) // para nivel 2 de call center, muestra cada aseguradora por separado, de lo contrario muestra todas las flotas de las aseguradoras
		{
			$ASEU=$_SESSION['Adjudicacion_ASEGURADORA'];
			if(inlist($ASEU,'1,8,9')) $ASEU='1,8,9,6'; // Allianz y AOA
			if(inlist($ASEU,'3,7')) $ASEU='3,7,6'; // Liberty y AOA
			if(inlist($ASEU,'2,5')) $ASEU='2,5,6'; // Royal y AOA
			if(inlist($ASEU,'4,10')) $ASEU='4,6,10'; // Mapfre y AOA
			if(inlist($ASEU,'73,74,60,55,76,78,83,93,94,105,95,110,111,112,143,70,144,145,146,173,181,182,59,183,184,186,188,192,193,194,195,198,199,200,39,201,202,204,206,208,209,89,211,208,213,215,216,218')) 
			$ASEU='73,74,60,55,76,78,83,93,94,105,95,110,111,112,143,70,144,145,146,173,183,184,186,188,192,193,89,211,212,213,214,215,216,208,213,215,216,218,229'; // Parex
		}
		else $ASEU='1,2,3,4,5,6,7,8,9,10,73,74,60,55,76,78,83,93,94,105,95,110,143,70,144,145,146,173,181,182,59,183,184,186,188,192,193,194,195,198,199,200,39,201,202,204,206,208,209,89,211,212,213,214,215,216,208,213,215,216,218,229'; 
		// todas las flotas
	}


	html();  // pinta cabeceras html
	$FI=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),-5)));  // a la fecha actual le resta 5 dias
	$FF=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),15))); // a la fecha actual le suma 15 dias
	pinta_js1($FI,$FF); // pinta toda el ambiente javascript teniendo en cuenta las fechas de 20 dias
	// PINTA EL FORMULARIO SUPERIOR CON LOS PARAMETROS DE FILTRO DEL TABLERO DE CONTROL
	
	/*FormIden*/
	echo "<form action='zcontrol_operativo3Dev.php' method='post' target='Oculto_control' name='forma' id='forma' onsubmit='pinta_dibujo()'>
					Fec:".pinta_FC('forma','FI',$FI)." - ".pinta_FC('forma','FF',$FF)." Flt: "; // pinta la captura de fecha inicial  y final
	// de acuerdo a si el usuario es limitado a una flota especifica pinta el menu de flotas de las aseguradoras tambien tiene en cuenta si se está adjudicando desde callcenter
	echo ($ASEU?menu1('ASE',"select id,nombre from aseguradora where id in ($ASEU) order by nombre asc",$_SESSION['Adjudicacion_ASEGURADORA'],($_SESSION['Adjudicacion_ASEGURADORA'] && $USUARIO!=4?1:0),"width:200px"," onchange='cambio_aseguradora(this.value);' "):
				menu1('ASE','select id,nombre from aseguradora order by nombre asc',0,($USUARIO==4?0:1),"width:200px"," onchange='cambio_aseguradora(this.value);' "))." Ofi:";
	// de acuerdo a si el usuario pertenece a una oficina, solo muestra las opciones de esa oficina. de lo contrario muestra todas las oficinas. tambien tiene en cuenta si se esta adjudicando desde call center
	echo ($OFIU?menu1('OFI',"select id,nombre from oficina where id in ($OFIU) order by nombre asc",0,($_SESSION['Adjudicacion_ASEGURADORA'] && $USUARIO!=4?1:0),"width:100px"):menu1('OFI',"select id,nombre from oficina where activa=1 order by nombre asc",0,($USUARIO==4?0:1),"width:100px"));
	echo "Mrc:";
	// Si el usuario es de mapfre, solo muestra dos marcas, para el resto de los usuarios muestra todas las marcas
	echo ($ASEU==4 || $ASEU=='4,10'?menu1('MARCA',"select distinct m.id,m.nombre from marca_vehiculo m,linea_vehiculo l,vehiculo v where v.linea=l.id and l.marca=m.id and m.id
			in (select distinct marca from linea_vehiculo,vehiculo where vehiculo.linea=linea_vehiculo.id and vehiculo.flota in (4,10) ) ",0,1," width:100px;"):
		menu1('MARCA','select distinct m.id,m.nombre from marca_vehiculo m,linea_vehiculo l,vehiculo v where v.linea=l.id and l.marca=m.id order by nombre asc',0,1," width:100px;"));
	// si el usuario no es aseguradora avanzada muestra el menu de placas. tiene en cuenta si pertenece solo a una oficina
	if(!inlist($USUARIO,'11,29')) echo "Plc: ".menu1('PI',"select placa,placa from vehiculo".($OFIU?" where ultima_ubicacion in ($OFIU) ":"")." order by placa asc",'',1,"width:80px;");
	echo "Est: <input type='checkbox' name='Resumen_estadisticas' alt='Con Estadisticas' title='Con Estadisticas'> "; // si se marca esta variable, se muestran las estadisticas al final del tablero de control
	echo "Estado: ".menu1("ultimo_estado","select id,nombre from estado_vehiculo order by nombre asc",0,1,"width:100px"); // muestra un menu para buscar los vehículos cuyo ultimo estado coincida con esta selección
	echo "Linea: ".menu1("linea_vehiculo","select id,nombre from linea_vehiculo order by nombre asc",0,1,"width:100px");
	echo "Clase de servicio: ".menu1("clase_servicio","select id,nombre from clase_servicio order by nombre asc",0,1,"width:100px");
	echo "Tipo de caja: ".menu1("vehiculo_tipo_caja","select id,nombre from vehiculo_tipo_caja order by nombre asc",0,1,"width:100px");
	echo " Adj <input type='checkbox' name='Adjudicados' title='Solo Adjudicados' alt='Solo Adjudicados'>";	//		" Solo Disponibles  ".
	// pinta en html todos los objetos necesarios para el tablero de control
	echo " <input type='button' id='btn_aplicar' value=' Aplicar ' onclick='recargar_datos();' ></form>
				<table cellspacing='0' cellpadding='0' width='100%'>
				<tr><td width=200px'></td></tr>
				<tr><td><iframe id='tablero' name='tablero' width='100%' frameborder='no' scrolling='auto' src='zcontrol_operativo3Dev.php?Acc=pinta_ciudades_inicial&FI=$FI&FF=$FF'></iframe></td></tr>
				</table>
				<div id='estatus' style='position:fixed;top:0;left:0;visibility:hidden;opacity:0.5;width:900px'>
				<table width='100%'><tr ><td id='celda_status' height='500px' align='center' bgcolor='ffffff'>
				<img src='img/cargando1.gif' width='300px' border='0' ></td></tr></table></center></div>
				<script language='javascript'>document.getElementById('estatus').style.width=document.body.clientWidth;
				document.getElementById('celda_status').style.height=document.body.clientHeight;</script>
				<iframe name='Oculto_control' id='Oculto_control' height=1 width=1></iframe>";
	if($Instruccion_Externa)	// Cuando hay una instrucción externa es porque se está seleccionando un vehiculo en una fecha especifica. Usado en Control de balance de estados por vehiculo
	{
		$Instrucciones=explode(';',$Instruccion_Externa); // Convierte a un arreglo separado por puntos y comas cada instrucción
		echo "<script language='javascript'>";
		foreach($Instrucciones as $Instruccion)
		{
			$Partes_instruccion=explode(',',$Instruccion);	//	Cada instrucción es separada en dos partes, una que es un campo dentro de un formulario y la otra el valor que se le asigna.
			if($Partes_instruccion[0] && $Partes_instruccion[1] ) echo "document.forma.".$Partes_instruccion[0].".value='".$Partes_instruccion[1]."'; ";
			elseif($Instruccion) echo $Instruccion.';';
		}
		echo "</script>";
	}
	echo "</body>";
}

function pinta_ciudades_inicial() // Pinta las ciudades con sus vehiculos a la entrada del programa
{
	global $OFIU,$ASEU,$FI,$FF,$ASE;
	if($_SESSION['Adjudicacion_OFICINA'])  // valida si está en adjudicación desde call center
	{
		$OFIU=$_SESSION['Adjudicacion_OFICINA'];
		if($companeras=qo1("select ofi_compas from oficina where id=$OFIU")) $OFIU=$compañeras;
		//if(inlist($OFIU,'1,11,12,13')) $OFIU='1,11,12,13'; // valida si la ciudad es bogota, pinta todas las oficinas de la misma ciudad
	}
	if($_SESSION['Adjudicacion_ASEGURADORA'])
	{
		if($_SESSION['Adjudicacion_NIVEL']<2) // valida si el agente de callcenter es menor que un nivel 2
		{
			$ASEU=$_SESSION['Adjudicacion_ASEGURADORA'];
			// solo muestra las flotas de las aseguradoras individualmente
			if(inlist($ASEU,'1,8,9')) $ASEU='1,8,9,6';
			if(inlist($ASEU,'3,7')) $ASEU='3,7,6';
			if(inlist($ASEU,'2,5')) $ASEU='2,5,6';
			if(inlist($ASEU,'4,10')) $ASEU='4,6,10';
			if(inlist($ASEU,'73,74,60,55,76,83,93,94,110,111,112,143,144,70,145,146,173,181,182,59,183,184,186,188,192,193,194,195,198,199,200,39,201,202,204,206,208,209,89,211,208,213,215,216,218')) 
			$ASEU='73,74,60,55,76,83,93,94,105,95,110,111,112,143,144,70,145,173,181,182,59,183,184,186,188,192,193,194,195,198,199,200,39,201,202,204,206,208,209,89,211,212,213,214,215,216,208,213,215,218,229';
		}
		else $ASEU='1,2,3,4,5,6,7,8,9,10,73,60,55,76,83,93,94,105,95,143,70,144,146,173,181,182,59,183,184,186,188,192,193,199,201,202,204,206,208,209,211,212,213,214,215,216,208,213,215,218,229'; // si el nivel es dos en adelante, muestra todas las flotas
		if(!$_SESSION['Adjudicacion_READJUDICAR']) // Valida si no está readjudicando, deja adjudicar un vehiculo si el siniestro está en estado pendiente.
		{	if(qo1("select estado from siniestro where id=".$_SESSION['Adjudicacion_SINIESTRO'])!=5)
			{echo "<body><script language='javascript'>alert('El estado del Servicio no es PENDIENTE, no se puede asignar ningún vehículo.');parent.adjudicacion_finalizada()</script></body>";die(); }
		}
	}

	if($ASE) $ASEU=$ASE;
	// INSTRUCCION PRINCIPAL PARA PINTAR LAS OFICINAS
	if($OFIU) $Ciudades=q("select id,nombre,oficina_taller,oficinas_atiende from oficina where id in ($OFIU) "); else 
		$Ciudades=q("SELECT id,nombre,oficina_taller,oficinas_atiende FROM oficina WHERE activa=1 ORDER BY nombre");
	// ------------------------------------------------------------------------------
	$Cantidad=mysql_num_rows($Ciudades); // obtiene la cantidad de ciudades de acuerdo al perfil del usuario si solo pertenece a una o si puede ver todas
	$Porc_tam=round(100/$Cantidad,2); // Con la cantidad de ciudades obtiene el porcentaje en tamaño para pintar las columnas de vehiculos por ciudad
	include('inc/link.php');
	$SCpla="";$SCpl="";
	$Todo="<table border cellspacing='0' width='90%' align='center'><tr>";
	while($Ciu=mysql_fetch_object($Ciudades))
	{
		$SCpla.="Arr$Ciu->id=new Array(); "; // en javascript crea un arreglo de vehiculos por cada ciudad
		$Todo.="<td width='$Porc_tam%' align='center' valign='top'>
			<input type='button' value=' $Ciu->nombre ' onclick='selecciona_ciudad($Ciu->id);'><br>";
		// obtiene las placas de cada ciudad
		if($Ciu->oficina_taller)
		{
			$Query_placas="select distinct v.id,v.placa
				FROM vehiculo v,ubicacion u
				WHERE (v.inactivo_desde='0000-00-00' || v.inactivo_desde>'$FI' ) ".($ASEU?" and v.flota in($ASEU) ":"")." 
					and v.id=u.vehiculo and 
						(
							(u.fecha_inicial<='$FI' and u.fecha_final>='$FI' ) or
							(u.fecha_inicial<='$FF' and u.fecha_final>='$FF') or
							(u.fecha_inicial>='$FI' and u.fecha_final<='$FF') or
							(u.fecha_inicial<='$FI' and u.fecha_final>='$FF')
						)
					and u.oficina_taller = $Ciu->id
					and u.estado=10 
				ORDER BY v.placa ";
				
				//debug_control($Query_placas);
		}
		else
			$Query_placas="select v.id,v.placa
				FROM vehiculo v
				WHERE (v.inactivo_desde='0000-00-00' || v.inactivo_desde>'$FI' ) ".($ASEU?" and v.flota in($ASEU) ":"")." and v.ultima_ubicacion=$Ciu->id
				ORDER BY v.placa ";
				//echo $Query_placas."test 2";
		
		if($Placas=mysql_query($Query_placas,$LINK))
		{
			// pinta la captura para filtrar por placa dentro de cada ciudad
			$Todo.="<input type='text' name='fl' size=5 onkeyup='valida_key(event,this,$Ciu->id,Arr$Ciu->id);'>
						<span id='npl$Ciu->id'>".mysql_num_rows($Placas)."</span>
						<br>
						<select name='pl$Ciu->id' id='pl$Ciu->id' ondblclick='selecciona(this.value);' size='30' >";
			// crea las opciones de cada select de ciudad
			while($P=mysql_fetch_object($Placas))
			{
				$Todo.="<option value='$P->placa' >$P->placa</option>";
				$SCpl.="Arr$Ciu->id[Arr$Ciu->id.length]='$P->placa'; "; // en javascript crea la instrucción para ir llenando el arreglo de placas
			}
			$Todo.="</select>";
		}
		else $Todo.="No hay ".mysql_error($LINK);
		$Todo.="</td>";
	}
	mysql_close($LINK);
	$Todo.="</tr></table>";

	html();
	echo "<script language='javascript'>$SCpla;
	function valida_key(codigo,objeto,objeto2,arreglo) // validacion para cada pisada de tecla
	{
		var keynum;
		var Sel=document.getElementById('pl'+objeto2);
		if(window.event) keynum = codigo.keyCode; else if(codigo.which) keynum = codigo.which;
		//alert(keynum);
		objeto.value=objeto.value.toUpperCase();
		if(keynum==8 || keynum==46)
		{
			for(var x=0;x<Sel.length;x++) Sel[x]=null;
			var y=0;
			for(var x=0;x<arreglo.length;x++) // reconstruye las opciones del select de la ciudad con las coincidencias de lo que va digitando el usuario
				if(arreglo[x].indexOf(objeto.value)!=-1) {Sel[y]=new Option(arreglo[x],arreglo[x]);y++;}
		}
		else
		{
			for(var x=0; x<Sel.length;x++)
			{
				var pos=Sel[x].text.indexOf(objeto.value); // borra todas las opciones del select de vehiculos de la ciudad
				if(pos==-1) {Sel[x]=null;x--;}
			}
		}
		document.getElementById('npl'+objeto2).innerHTML=Sel.length;
	}

	function selecciona(dato){parent.selecciona(dato);}
	function selecciona_ciudad(dato){parent.selecciona_ciudad(dato);}
	</script><body><script language='javascript'>$SCpl</script>$Todo<br><br>
	<center><a onclick=\"modal('manual/NUEVATABLACONTROL/nuevatablacontrol.html',0,0,600,800,'ntc');\" style='cursor:pointer'><b style='color:ff0000'>VER VIDEO NUEVA TALBA DE CONTROL</b></A></center>
	$ASEU - $OFIU</body>";
}

function pinta_js1($FI,$FF) // pinta todas las herramientas java script que se necesitan en el tablero de control
{
	global $USUARIO;
	echo "
		<style type='text/css'>
			tr:hover {background-color: #ffffff;}
		</style>
		<script language='javascript'>
		function carga() { ajustar_tablero(); }
		function ajustar_tablero() {document.getElementById('tablero').style.height=document.body.clientHeight-50;} // ajusta el alto del area del tablero
		function recargar_datos()
		{
			//alert('Recargar datos exec');
			document.getElementById('btn_aplicar').style.visibility='hidden';  // oculta el boton aplicar
			with(document.forma)
			{
				pinta_dibujo(); // activa una capa llamada estatus que no deja clickear en ningún botón antes de que se termine de cargar  el tablero de control
				// crea una cadena de comando get http para abrir en el marco adecuado la presentacion del detalle del tablero
				
				var Comando='zcontrol_operativo3Dev.php?Acc=control_operativo&FI='+FI.value+'&FF='+FF.value+'&Tamano=50&ASE='+ASE.value+'&OFI='+OFI.value+'&UE='+ultimo_estado.value+'&LV='+linea_vehiculo.value+'&CL='+clase_servicio.value+'&CAJ='+vehiculo_tipo_caja.value;
				//console.log(Comando);
				if(document.forma.ultimo_estado2) Comando+='&ultimo_estado2='+ultimo_estado2.value;
				if(Resumen_estadisticas.checked) Comando+='&Resumen_estadisticas=1';
				if(document.forma.PI) Comando+='&PI='+PI.value;
				if(document.forma.MARCA)  Comando+='&MARCA='+MARCA.value;
				if(document.forma.Adjudicados.checked) Comando+='&Adjudicados=1';
				window.open(Comando,'tablero');
			}
		}
		function adjudicacion_finalizada() { opener.re_comenzar(); window.close();void(null);}
		function pinta_dibujo(){document.getElementById('estatus').style.width=document.body.clientWidth;document.getElementById('celda_status').style.height=document.body.clientHeight;document.getElementById('estatus').style.visibility='visible';}
		function selecciona(dato)	{document.forma.PI.value=dato;	recargar_datos();}
		function selecciona_ciudad(dato)	{document.forma.OFI.value=dato;	recargar_datos();}
		function cambio_aseguradora(dato) {window.open('zcontrol_operativo3Dev.php?Acc=pinta_ciudades_inicial&FI=$FI&FF=$FF&ASE='+dato,'tablero');}
		</script>
		<body leftmargin='0' rightmargin='0' topmargin='0' bottommargin='0' onload='carga();' onresize='ajustar_tablero();'>
		<script language='javascript'>centrar();</script>";
}
/*
Clase para identificar cada estado del vehiculo, lo correspondiente a una micro celda dentro de una celda de cada dia
*/
class cls_estado
{
	var $id=0;   // id del estado
	var $Color='ffffff'; //  color del estado
	var $Nombre=''; // nombre del estado
	var $Cantidad=0;  //  cantidad de eventos del estado
	var $Siniestro_propio=0; // estados de fuera de servicio marcados como siniestro propio
	function cls_estado($id,$Color,$Nombre)
	{ $this->id=$id;
		$this->Color=$Color;
		$this->Nombre=$Nombre;
		$this->Cantidad=1; }
	function pinta()
	{ echo "<tr><td style='background-color:".$this->Color."'>&nbsp;</td><td nowrap='yes'>".$this->Nombre."</td><td align='center'>".$this->Cantidad."</td></tr>"; }
}
/*
Clase para acumular estados por cada ciudad
*/
class cls_estado_ciudad
{
	var $Id=0;  // id de la oficina
	var $Nombre=''; // nombre de la oficina
	var $Estados=array(); // estados de la oficina y sus acumulados
	var $Total=0; // total sumatoria de estados de la oficina
	var $Vehiculos=array();

	function cls_estado_ciudad($id,$Oficina,$estado,$Color,$Nombre) // funcion creadora
	{
		$this->id=$id;
		$this->Nombre=$Oficina;
		$this->Estados[$estado]=new cls_estado($estado,$Color,$Nombre); // para cada estado crea una clase de estado dentro del arreglo de estados por ciudad
	}
	function pinta() // primero totaliza los estados y los ordena, luego pinta el resultado
	{
		$this->totaliza();
		ksort($this->Estados);
		echo "<table align='center' cellspacing='3'><tr><th colspan=3>".$this->Nombre." - ".$this->Total."</th></tr><tr><th>Clr</th><th>Estado</th><th>Cantidad</th></tr>";
		foreach($this->Estados as $Estado) $Estado->pinta();
		echo "</table>";
	}

	function totaliza() { foreach($this->Estados as $Estado) { $this->Total+=$Estado->Cantidad;}} // totaliza los acumulados de estados para saber el total de estados de la ciudad

	function inserta_vehiculo($P) { $this->Vehiculos[$P->id]=$P->placa;} // inserta el vehiculo en la ciudad
}

function control_operativo() // funcion principal creadora de las consultas en la base de datos para obtener la información de cada vehiculo
{

	global $FI,$FF,$ASE,$OFI,$EST,$ASEU,$USUARIO,$Nsin,$Hdevolucion,$Resumen_estadisticas,$PI,$Odofinal,$ED,$US,$Efectividad,$UE;
	global $MARCA,$CS,$Siniestros_propios,$Efectividad,$Adjudicados,$AAlertas,$Autorizaciones,$LV,$CL,$CAJ;
	// obtiene los id de los permisos para el perfil de usuario de las tablas ubicacion, vehiculo y hoja de vida de  los vehiculos
	$Nt1=tu('ubicacion','id');
	$Nt2=tu('vehiculo','id');
	$Nt3=tu('hv_vehiculo','id');

	include('inc/link.php');
	if(inlist($USUARIO,'1,11,29') /*Aseguradora 1*/)
	{
		// pobla el arreglo de siniestros dentro de las dos fechas limites
		if($USUARIO==1)  // usuario super administrador
			$Q_S="select s.numero,s.ubicacion from siniestro s,ubicacion u WHERE s.ubicacion=u.id ".($ASE?" and s.aseguradora=$ASE":" ").
			" and u.fecha_final >='$FI' and u.fecha_inicial<='$FF' ";
			
			
		ELSE
			$Q_S="select s.numero,s.ubicacion from siniestro s,ubicacion u WHERE s.ubicacion=u.id and s.aseguradora=$ASE and u.fecha_final >='$FI' and u.fecha_inicial<='$FF' ";
		if(!$Siniestros=mysql_query($Q_S,$LINK)) {echo "Error  $Q_S <br>".mysql_error($LINK);die();}
		// como no todos los siniestros tienen una ubicacion (adjudicacion) en el siguiente arreglo se guardan los siniestros dentro del rango de fechas que si tienen una ubicacion
		while($Sin=mysql_fetch_object($Siniestros)) $Nsin[$Sin->ubicacion]="$Sin->numero";  // guarda en el arreglo de siniestros los que tengan una ubicacion
	}
	if(!inlist($USUARIO,'11,29'))
	{	// pobla el arreglo de autorizaciones para ser consultado por call center
		$QAutorizaciones=mysql_query("select a.siniestro,a.id,c.arribo,a.estado from sin_autor a,siniestro s,cita_servicio c
			where a.siniestro=s.id and s.id=c.siniestro and a.siniestro=c.siniestro and c.estado='P' and a.estado in ('A','E')  ",$LINK);
		// en el siguiente arreglo guarda las autorizaciones pendientes o autorizadas correspondientes a las citas pendientes
		while($Au=mysql_fetch_object($QAutorizaciones)) $Autorizaciones[$Au->siniestro]=$Au;
	}
	

	// pobla el arreglo de horas de devoluciones
	if($Hdevol=mysql_query("select u.id,c.hora_devol FROM ubicacion u,vehiculo v,cita_servicio c,siniestro s
							WHERE  u.vehiculo=v.id and c.placa=v.placa and u.estado=1 and u.fecha_final>='$FI' and u.fecha_inicial<='$FF'
							and u.id=s.ubicacion and c.siniestro=s.id and c.estado ='C' order by id desc",$LINK))
	// por cada vehiculo obtiene la hora de devolucion, la llave del arreglo es la ubicacion
	{while($H=mysql_fetch_object($Hdevol)) $Hdevolucion[$H->id]="$H->hora_devol";}
	//  -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	html();
	echo "<script language='javascript' src='inc/chart/JSClass/FusionCharts.js'></script>";
	echo "<style type='text/css'>
			<!--
			";
	// crea una rutina de estilos css para identificar por colores los estados. cada color se configura en la tabla de estados del vehiculo
	foreach($EST as $est)
	{
		echo "
		a.c".$est->id." {font-size:12px;display:inline-block;background-color:".$est->color_co.";text-align:center;margin-left:1px;margin-bottom:1px;margin-top:0px;position:relative;z-index:24;}
		a.c".$est->id.":hover {z-index:100;font-size:11px;border:1px solid #000000;margin-left:1px #000000;margin-bottom:0px;margin-top:0px;margin-right:0px;}
		a.c".$est->id." span {display: none;}
		a.c".$est->id.":hover span {display:block;position:absolute;top:0em;left:5em;border:1px solid #0055ff;background-color:#ffffE4;color:#000000;text-align: left;font-family: Arial, Helvetica, sans-serif;font-size: 11px;padding: 5px;opacity: 0.90;}
		";
	}
	echo "
	a.cn {font-size:12px;display:inline-block;background-color:ddddff;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;}
	a.cb {font-size:12px;display:inline-block;background-color:ffffff;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;}
	a.cc {font-size:12px;display:inline-block;background-color:dddddd;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;margin-top:0px;position:relative;z-index:24;}
	a.cc:hover {z-index:100;font-size:11px;border:1px solid #000000;margin-left:1px #000000;margin-bottom:0px;margin-top:0px;margin-right:0px;}
	a.cc span {display: none;}
	a.cc:hover span  {display:block;position:absolute;top:0em;left:4em;border:1px solid #0055ff;background-color:#e4e4ff;color:#000000;text-align: left;font-family: Arial, Helvetica, sans-serif;font-size: 11px;padding: 5px;opacity: 0.90;}

	a.cnt {font-size:12px;display:inline-block;font-weight:bold;background-color:006644;color:ffffaa;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;}
	sp_placa {font-size:14px;color:000088;}
	tr:hover {background-color: #aaaaaa;}

	.cn {display:inline-block;font-weight:bold;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;cursor:pointer;}
	.cnt {display:inline-block;font-weight:bold;background-color:006644;color:ffffaa;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;}

	.pt {font-size:12px;display:inline-block;font-weight:bold;color:561409;width:50px;text-align:center;margin-left:1px;margin-bottom:1px;margin-top:0px;cursor:pointer;}
	.mcpl {font-size:14px;background-color:ffffdd; }

		-->
	</style>
	<script language='javascript'>
	var Contenido_celda='';
	var Cargado=false;
	var Objeto;
	var Pos_Lateral=0;
		// funcion para sincronizar los paneles superio y lateral de la tabla de control para que se queden estaticos y solo se mueva el detalle por debajo
		function sincroniza() { var Wtop=document.body.scrollTop;var Wleft=document.body.scrollLeft;document.getElementById('_capa_fechas').style.left=-Wleft+Pos_Lateral;document.getElementById('_capa_placas').style.top=-Wtop;}
		// funcion para activar un menu contextual por evento cuando se le da click normal o click derecho a una microcelda
		function mc_evento(idub,fecha,objeto){if(Cargado){objeto.style.cursor='wait';Cargado=false;Objeto=objeto;window.open('zcontrol_operativo3Dev.php?Acc=menu_contextual_celda&idub='+idub+'&fecha='+fecha+'&objeto='+objeto.id+'&Adjudicacion_oficina=$Adjudicacion_oficina&Adjudicacion_aseguradora=$Adjudicacion_aseguradora','Menu_contextual_celda');}	else alert('No ha terminado de cargar la información');}
		function mc_evento_texto(dato){Menu=document.getElementById('Menu_contextual_celda');Cuad=document.getElementById('cuadricula');if(!dato){if(mouseX-15+Menu.clientWidth>Cuad.clientWidth) Menu.style.left=Cuad.clientWidth-Menu.clientWidth;else Menu.style.left=mouseX-15; Menu.style.top=mouseY; } Menu.style.visibility='visible'; }
		// funcion que oculta elmenu contextual de una microcelda
		function oculta_menu_celda()	{with(document.getElementById('Menu_contextual_celda'))	{style.visibility='hidden';style.width=1;style.height=1;}}
		// funcion para pintar un menu de una celda en blanco
		function menu_blanco(idplaca,fecha){if(Cargado) window.open('zcontrol_operativo3Dev.php?Acc=adicionar_evento&vehiculo='+idplaca+'&fecha='+fecha,'Menu_contextual_celda');else alert('No ha terminado de cargar la información');}
		// funcion para pintar opciones de adjudicacion cuando se carga esta aplicación como efecto de una adjudicacion desde call center
		function menu_adjudicar(idplaca,fecha) {if(Cargado) modal('zcallcenter2.php?Acc=adjudicar_vehiculo&v='+idplaca+'&f='+fecha,0,0,500,500,'Adjudicacion');}
		// muestra la cita del servicio
		function menu_citaservicio(id_cita) {if(Cargado) modal('zcallcenter2.php?Acc=mostrar_citaservicio&id='+id_cita,0,0,500,500,'muestra_cita');}
		// cuando finaliza una adjudicacion desde call center
		function adjudicacion_finalizada() {parent.adjudicacion_finalizada();}
		// opcion que descarga los vehiculos que estan en parqueadero o alistamiento
		function muestra_diario_parqueadero(Dato,Fecha){ window.open('zcontrol_operativo3Dev.php?Acc=muestra_diario_parqueadero&D='+Dato+'&F='+Fecha,'Oculto_tablero'); }
		// para cambiar la fecha inicial desde el panel de fechas
		function cambia_fi(fecha){ parent.document.forma.FI.value=fecha; parent.document.forma.submit(); }
		// para poblar el contenido de las celdas laterales de las placas con un contenido html
		function lateral(dato){ document.getElementById('_idpl').innerHTML+=dato;}
		// menu contextual de placa
		function menuplaca(Placa,idPlaca)     // menu contextual del top de la tabla o de los titulos de las columnas
		{
			Menu=document.getElementById('Menu_contextual_placa');
			Menu.style.visibility='visible';
			Menu.style.left=10;
			Menu.style.top=mouseY-10;
			var Contenido=\"<table border=3 cellspacing='0' cellpadding='0' bgcolor='#ffffd0' name='Context_Opciones' id='Context_Opciones'>\";
			Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"oculta_menuplaca();\\\" align='center'><img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar menu</b></td></tr>\";
			Contenido+=\"<tr><td align='center' class='placa' style='background-image:url(img/placa.jpg);'>\"+Placa+\"</td></tr>\"; ";
			// de acuerdo al perfil, se muestran o no ciertas opciones
			if(inlist($USUARIO,'1,2,3,7,10,13,23,27'))
			{echo "Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt1&VINCULOT=\"+idPlaca+\"&VINCULOC=vehiculo',0,0,600,900,'hp');\\\"><img src='img/cronograma.png' border='0' height='16'> Historia</b></td></tr>\"; ";}
			if(inlist($USUARIO,'1,2,7,10,13,23'))
			{echo "Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt3&VINCULOT=\"+Placa+\"&VINCULOC=placa',0,0,600,900,'hp');\\\"><img src='img/my-reports.png' border='0' height='16'> Hoja de Vida</b></td></tr>\"; ";}
			if(inlist($USUARIO,'1,2,7,23'))
			{	echo "Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$Nt2&id=\"+idPlaca+\"',0,0,600,900,'hp');\\\"><img src='img/vehiculo.png' border='0' height='16'> Definición</b></td></tr>\";";}
			echo "Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"historia_fotografica('\"+idPlaca+\"');\\\"><img src='gifs/camara.png' border='0' height='14'> Ver historia foto</td></tr>\";
				Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"control_alertas('\"+idPlaca+\"');\\\"><img src='gifs/alarma.png' border='0' height='14'> Control Alertas</td></tr>\";
			Contenido+=\"</table>\";
			Menu.innerHTML=Contenido;
		}
		// para ocultar el menu contextual de placa
		function oculta_menuplaca() {Menu=document.getElementById('Menu_contextual_placa');Menu.style.visibility='hidden';}
		// funcion que muestra el historial hacia atras de las imagenes tomadas por los agentes de servicio al vehiculo
		function historia_fotografica(idPlaca) {modal('zcontrol_operativo3Dev.php?Acc=historia_fotografica&idPlaca='+idPlaca,0,0,500,800,'hf');	}
		// funcion que muestra el control de alertas por vehiculo
		function control_alertas(idPlaca) {modal('marcoindex.php?Acc=abre_tabla&NTabla=cfg_alerta_vehiculo&VINCULOC=vehiculo&VINCULOT='+idPlaca,0,0,600,600,'cav');}
		// adiciona un estado de mantenimiento a la hoja de vida de cada vehiculo
		function adiciona_mantenimiento(Placa) {if(confirm('Desea aicionar un Mantenimiento a la placa '+Placa+' ?')) modal('zcontrol_operativo3Dev.php?Acc=adiciona_mantenimiento&Placa='+Placa,0,0,500,800,'hf');	}
		// adiciona un estado de soat a la hoja de vida del vehiculo
		function adiciona_soat(Placa) {if(confirm('Desea aicionar un SOAT a la placa '+Placa+' ?')) modal('zcontrol_operativo3Dev.php?Acc=adiciona_soat&Placa='+Placa,0,0,500,800,'hf');	}
		// adiciona una revisión tecnico mecanica a la hoja de vida del vehiculo
		function adiciona_rtm(Placa) {if(confirm('Desea aicionar una Revisión Técnico Mecánica a la placa '+Placa+' ?')) modal('zcontrol_operativo3Dev.php?Acc=adiciona_rtm&Placa='+Placa,0,0,500,800,'hf');	}
		// ajusta la posición y tamaño de los paneles de superior.fechas y de izquierda.placas
		function ajusta_paneles() {document.getElementById('_capa_fechas').style.left=document.getElementById('_capa_placas').clientWidth;Pos_Lateral=document.getElementById('_capa_placas').clientWidth;document.getElementById('celda1').style.width=document.getElementById('_capa_placas').clientWidth-1;}
		function control_alerta(idv,alerta,ntipo_alerta,actual)
		{
			if(confirm('El vehículo presenta ALERTA de '+ntipo_alerta+'.\\n'+'Desea insertar actualización de este evento?'))
			{
				modal('zcontrol_operativo3Dev.php?Acc=insertar_hv&v='+idv+'&ta='+alerta+'&actual='+actual,50,50,400,600,'iev');
			}
		}
	</script>
	<body leftmargin='0' topmargin='1' rightmargin='0' bottommargin='0' bgcolor='eeffee' onscroll='sincroniza()' onload='ajusta_paneles();'>
	<script language='javascript'>
	//parent.document.getElementById('estatus').style.visibility='visible'; // hace visible la capa protectora del estatus hasta que termine de cargar el tablero de control.
	</script>
	";
	// obtiene las placas de acuerdo a los filtros de marca, ciudad, aseguradora, ultimo estado, y adjudicados, fecha inicial y final, oficina
	
	$sql = "select mod(right(v.placa,1),2) as par,v.id,v.placa,if(l.emblema_f!='',l.emblema_f,m.emblema_f) as emb1,v.flota,a.emblema_f as emb3,
											t_ofi_sigla(v.ultima_ubicacion) as noficina,v.ultima_ubicacion,v.linea,
											t_oficina(v.ultima_ubicacion) as nombre_oficina,m.manten_cada,t_ultimo_mantenimiento(v.placa,'MNT') as ultimo_mantenimiento,
											t_ultimo_mantenimiento(v.placa,'SOA') as ultimo_soat,t_ultimo_mantenimiento(v.placa,'RTM') as ultima_revisiontm,v.fecha_revisiontm,
											v.fecha_matricula,v.tipo_caja , v.tipo_traccion, tc.nombre AS tipo_caja,
											tt.nombre AS tipo_traccion,
											tc.sigla AS tipo_caja_sigla,
											tt.sigla AS tipo_traccion_sigla
		FROM vehiculo v,linea_vehiculo l,marca_vehiculo m,aseguradora a,oficina o,vehiculo_tipo_caja as tc,       
		vehiculo_tipo_traccion as tt".($CL?", clase_servicio cla":"")."
		WHERE tc.id = v.tipo_caja ".($CAJ?"and tc.id = $CAJ":"")."  and ".($CL?"a.clase_servicio = cla.id and cla.id = $CL and":"")."
        tt.id = v.tipo_traccion ".($LV?" and v.linea='$LV' ":"")." and  m.id=l.marca and l.id=v.linea and v.flota=a.id and (v.inactivo_desde='0000-00-00' || v.inactivo_desde>'$FI' ) ".($ASE?" and v.flota=$ASE ":"").($OFI?" and o.id=$OFI ":"").
		($PI?" and v.placa='$PI' ":"").($MARCA?" and l.marca=$MARCA ":"")." ".($UE?" and ultimo_estado(v.id) = $UE ":"").
		($Adjudicados?" and v.id in (select v.id from vehiculo v, cita_servicio c where c.placa=v.placa and c.fecha between '$FI' and '$FF' and c.estado='P')":"").
		" and 
			if(o.oficina_taller=1 and o.id='$OFI',
				v.id in (
							select distinct u.vehiculo from ubicacion u WHERE u.oficina_taller='$OFI' and 
								(	
									(u.fecha_inicial<='$FI' and u.fecha_final>='$FI' ) or
									(u.fecha_inicial<='$FF' and u.fecha_final>='$FF' ) or
									(u.fecha_inicial>='$FI' and u.fecha_final<='$FF' ) or
									(u.fecha_inicial<='$FI' and u.fecha_final>='$FF' )
								)
						), 
				o.id=v.ultima_ubicacion
			)
		 and o.activa=1 order by par,v.placa ";
		 
	if($Placas=mysql_query($sql,$LINK))
	{
		$Contador=0;$idPlacas='';
		$Margen_lateral_izquierdo=230;
		// en el arreglo de placas crea una clase por cada placa
		
		
		while($P=mysql_fetch_object($Placas))
		{
		   //$b_p =$P;
			
			
			
			$Contador++;$Aplacas[$Contador]=new cplaca($P);$idPlacas.=($idPlacas?",":"").$P->id;
	    }
		if($Contador)
		{						
			//crea una capa lateral flotante para pintar las placas y algunas caracteristicas que deben permanecer estaticas
			echo "<div id='_capa_placas' style='position:fixed;left=0;top=0;z-index:100'><font style='font-size:12px'><br /><br /></font><table cellspacing='0' cellpadding='0' bgcolor='eeeeee'><tbody id='_idpl'></tbody></table></div>
						<span id='Menu_contextual_placa' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#ddfdfd;z-index:110;'></span>";
			// crea una capa superior flotante para pintar los dias,semanas y meses
			echo "<div id='_capa_fechas' style='position:fixed;left:$Margen_lateral_izquierdo;z-index:100;'><table cellspacing='0' id='cuadricula' name='cuadricula' cellpadding='0' bgcolor='cccccc'><tr ><td colspan=2></td><td nowrap='yes'>";
			// PiNTA LOS TITULOS DE LAS FECHAS
			$mm=0;$tamano=50;$tam=0;
			for ($dia=$FI;$dia<=$FF;$dia=date('Y-m-d',strtotime(aumentadias($dia,1))))
			{ $mes=date('Y-m',strtotime($dia));
				if($mm!=$mes) // por cada cambio de mes calcula el tamaño de la celda donde se pinta el nombre del mes
				{ if($mm) { $tam--; echo "<script language='javascript'>document.getElementById('m$mm').style.width=$tam;</script>"; }
					$mm=$mes;$tam=0;
					echo "<span class='cnt' id='m$mes'>".date('Y',strtotime($dia)).' - '.mes(date('m',strtotime($dia)))."</span>";
					$Efectividad[$mm]=new cls_efectividad($mm); // en el arreglo de efectividades por mes crea una clase de efectividad por cada mes pintado
				}
				$tam+=$tamano+1;
			}
			if($mm) { $tam--; echo "<script language='javascript'>document.getElementById('m$mm').style.width=$tam;</script>"; } // ajusta el ultimo mes
			echo "</td></tr><tr ><td colspan=2></td><td >";
			// pinta los dias de cada mes
			for ($dia=$FI;$dia<=$FF;$dia=date('Y-m-d',strtotime(aumentadias($dia,1))))
			{
				$dd=date('d',strtotime($dia));
				$Dia=date('w',strtotime($dia));
				$Ndia=dia_semana($Dia);
				if($Dia==0 || $Dia==6){if($Dia==0) $Fondo='ffaaaa'; else $Fondo='aaaaff';}	else $Fondo='dddddd'; // evalua si es sabado, domingo o dia normal
				echo "<span class='cn' style='background-color:$Fondo' onclick=\"cambia_fi('$dia');\">$Ndia $dd</span>"; // pinta cada dia
			}
			echo "</td></tr></table></div><font style='font-size:12px'><br /><br /></font>";
			//   ************* FIN DE PINTADA DE TITULOS DE FECHAS **********************************
			// obtiene las ubicaciones de los vehiculos que encontró en la pintada de las placas en la rutina anteiror por eso usa idPlacas como filtro para no volver a compara contra otras tablas
			if($Ubicaciones=mysql_query("select u.*,ev.nombre as nestado,ase.nombre as nflota,o.nombre as noficina FROM ubicacion u, vehiculo v,estado_vehiculo ev,aseguradora ase,oficina o
									WHERE u.fecha_final>='$FI' and u.fecha_inicial<='$FF'  and v.id=u.vehiculo and v.id in ($idPlacas) and u.estado=ev.id and ase.id=u.flota and u.oficina=o.id
									ORDER BY u.vehiculo,u.fecha_inicial,u.fecha_final,u.id",$LINK))
			while($U=mysql_fetch_object($Ubicaciones))	{$US[$U->vehiculo][count($US[$U->vehiculo])]=$U;} // llena el arreglo de ubicaciones con el objeto Ubicacion lo ordena por vehiculo
			if(!inlist($USUARIO,'11,29')) // excluye el perfil aseguradoras1 y 2
			{
				$FIc=date('Y-m-d',strtotime(aumentadias($FI,-10)));
				if(!$Citas=mysql_query("select v.id,c.placa,s.numero,c.fecha,c.hora,c.conductor,c.agendada_por,c.id as idc,c.dias_servicio,c.siniestro
					FROM cita_servicio c,vehiculo v,siniestro s WHERE c.siniestro=s.id and c.placa=v.placa and c.fecha between '$FIc' and '$FF' and c.estado in ('P','C') and v.id in ($idPlacas) order by c.id desc",$LINK)) die(mysql_error($LINK));
				while($C=mysql_fetch_object($Citas)) {$CS[$C->id][count($CS[$C->id])]=$C;} //llena el arreglo de citas pendientes de los vehiculos obtenidos
			}
			$Estados=array(); // inicializa el arreglo de estados
			$Estados_ciudad=array(); // inicializa el arreglo de estados por ciudad
			echo "<table cellspacing='0' id='cuadricula' name='cuadricula' cellpadding='0' bgcolor='cccccc'><tr ><td><span class='cn' id='celda1' style='width:$Margen_lateral_izquierdo'></span></td></tr>";
			$Hoy=date('Y-m-d');
			IF($FF<$Hoy) $Hoy=$FF;
			$Conteo_placas=0;			
			
			for($ic=1;$ic<=count($Aplacas);$ic++) // por cada placa hace el recorrido y debe pintar el detalle dentro del tablero
			{
				if($US[$Aplacas[$ic]->id])  // solo toma en cuenta las placas que tienen ubicaciones, o si no no las pinta. esto sirve para los vehiculos que tienen fecha de inactividad
				{
					$Odofinal=0;$Siniestros_propios=0;
					$Conteo_placas++;
					//Toco crear esta variable
					
					
					$P=$Aplacas[$ic];
					$BG_placa=($P->par?"ffffdd":"ddffff");
					
					//print_r($P);
					//print_r($b_p);
					// usando javascript se va llenando el panel lateral de placas 
					echo "<script language='javascript'>
							lateral(\"<tr ><td >$Conteo_placas</td><td align='center' nowrap='yes' bgcolor='ffffff'><img src='$P->emblema_aseguradora' border='0' height='13'  vspace='0'></td>".
							"<td ><img src='$P->emblema_marca' border='0' height='13' width='16'  vspace='0'></td>".
							"<td ".(!inlist($USUARIO,'11,29')?"onclick=menuplaca('$P->placa',$P->id); ":"")." bgcolor='$BG_placa'><span class='pt'>$P->placa</span></td>".
							
							"<td><span style='color:red; font-size:8px;' title='Tipo caja $P->tipo_caja'><b>$P->tipo_caja_sigla</b></span></td>".
							
							"<td><span style='color:blue; font-size:8px;' title='Tipo tracción $P->tipo_traccion'><b>$P->tipo_traccion_sigla</b></span></td>".
							
							"<td >$P->sigla_oficina</td><td align='right' bgcolor='ffffff'>&nbsp;<span id='splaca$P->id'></span></td>".
							"<td id='al_$P->id' align='center' bgcolor='aaaaaa'></td></tr>\");
							</script>
							<td>&nbsp;</td><td nowrap='yes'>";
					// pínta para cada uno de los dias dentro del rango lo que encuentre en las clases usando una funcion y de paso obtiene el ultimo kilometraje en la variable ODOFINAL
					for ($dia=$FI;$dia<=$FF;$dia=date('Y-m-d',strtotime(aumentadias($dia,1)))) echo pinta_dia_placa($dia,$P);
					$P->odofinal($Odofinal,$LINK); // obtiene el último kilometraje de la placa, usa una funcion de la clase, luego pinta usando javascript
					if(!inlist($USUARIO,'11,29')) echo "<script language='javascript'>document.getElementById('splaca$P->id').innerHTML='".coma_format($P->kilometraje)."';</script>";
					echo "</td><td>&nbsp;&nbsp;</td></tr>";
					// oculta las estadisticas para el perfil de callcenter

					if($Resumen_estadisticas && $USUARIO!=4)
					{
						$Estado=$EST[$US[$P->id][count($US[$P->id])-1]->estado]; //extrae un sub-objeto a una variable sencilla para manipular el objeto
						if($Estados[$Estado->id])  //  acumula por estado
							$Estados[$Estado->id]->Cantidad++;
						else
							$Estados[$Estado->id]=new cls_estado($Estado->id,$Estado->color_co,$Estado->nombre); // si no existe el estado crea la clase para acumular
						if($Estados_ciudad[$P->ultima_ubicacion]) // busca si esta creado el objeto por ciudad
							if($Estados_ciudad[$P->ultima_ubicacion]->Estados[$Estado->id]) // busca que el estado este en la ciudad
									$Estados_ciudad[$P->ultima_ubicacion]->Estados[$Estado->id]->Cantidad++; // acumula por ciudad
							else
								$Estados_ciudad[$P->ultima_ubicacion]->Estados[$Estado->id] = new cls_estado($Estado->id,$Estado->color_co,$Estado->nombre); // crea el estado en la ciudad
						else
							$Estados_ciudad[$P->ultima_ubicacion]= new cls_estado_ciudad($P->ultima_ubicacion,$P->nombre_oficina,$Estado->id,$Estado->color_co,$Estado->nombre); // crea la ciudad en el arreglo
						$Estados_ciudad[$P->ultima_ubicacion]->inserta_vehiculo($P); // si el vehiculo es de la ciudad lo mete en la clase que maneja los vehiculos por ciudad
						$Estados_ciudad[$P->ultima_ubicacion]->Estados[$Estado->id]->Siniestro_propio+=$Siniestros_propios; // acumula los fuera de servicio marcados como siniestro propio de AOA
					}
				}
			}
			echo "</table>";
			IF($USUARIO!=4) for($i=1;$i<=count($Aplacas);$i++)
			{
				$Aplacas[$i]->pinta_alertas();
			}
			mysql_close($LINK);
			// ajusta el ancho de la zona lateral de las placas despues de cargar
			echo "<script language='javascript'>
						Pos_Lateral=document.getElementById('_capa_placas').clientWidth;
						document.getElementById('celda1').style.width=Pos_Lateral-1;
						</script>";
		}
		else
		{
			echo "No hay información";
		}
	}
	else
		echo "<b>No encuentro la informacion ".mysql_error();
	// crea las capas ocultas para menu contextual de celda, al terminar de cargar oculta la capa de estatus, y vuelve a dejar visible el boton aplicar.
	echo "</table><br /><br />
        <iframe id='Menu_contextual_celda' name='Menu_contextual_celda' style='visibility:hidden;position:absolute;z-index:100;' frameborder='no' scrolling='auto' height='10px' width='100px'></iframe>
        <script language='javascript'>
          parent.document.getElementById('estatus').style.visibility='hidden';
          Cargado=true;
		  parent.document.getElementById('btn_aplicar').style.visibility='visible';";
	echo " </script><iframe id='Oculto_tablero' name='Oculto_tablero' style='visibility:hidden' height='1' width='1'></iframe>";
	//
	/////////// -----------------------------------------------------------  ESTADISTICAS -------------------------------------------------------------------
	//
	sleep(1);

	if($Resumen_estadisticas)
	{
		if($USUARIO==29 /* Aseguradora2 */)
		{
			$Estados=q("select * from estado_vehiculo where id not in (93)");
			echo "<table align='center'><tr>"; while($E=mysql_fetch_object($Estados)) echo "<td bgcolor='$E->color_co'>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>$E->nombre</td>"; echo "</tr></table>";
		}
		else
		{
			if(is_array($Estados)) // pinta los acumuladores por estados
			{
				echo "<table align='center'><tr><th colspan=20>RESUMEN GENERAL DE ESTADOS</TH></tr><tr>
				<td valign='top'><table align='center' cellspacing='3'><tr><th colspan=3>Total $Hoy - $Conteo_placas</th></tr><tr><th>Clr</th><th>Estado</th><th>Cantidad</th></tr>";
				foreach($Estados as $Estado) $Estado->pinta();
				echo "</table></td>";
				foreach($Estados_ciudad as $Estados){echo "<td valign='top'>";$Estados->pinta();echo "</td>";} // pinta los acumuladores por estados por ciudad
				echo "</tr></table>";
			}
			include('inc/link.php');
			echo "<hr><b>PROMEDIOS DE PARQUEADEROS Y ALISTAMIENTOS POR CIUDAD</B> Puede hacer click sobre la cantidad de cada día para descargar un detallado de las placas.<BR>
			<table border cellspacing='0'><tr>";
			$Dias=array();
			foreach($Estados_ciudad as $Ofi) // por cada clase de estados por ciudad pinta los acumuladores
			{
				echo "<td valign='top'><table><tr><td colspan='2'><b>Oficina: $Ofi->Nombre</td></tr><tr><th>Diario</th><th>Cantidad</th></tr>";
				$Adiario=array();
				$D=$FI;
				$Diario=0;$Tdiario=0;$Contador=0;
				while($D<=$FF && $D<=$Hoy)
				{
					$Diario=0;
					echo "<tr><td>$D</td>";
					$Adiario["$D"]='';
					foreach($Ofi->Vehiculos as $idv => $V)
					{
						$Estado=$ED["$idv-$D"];
						if($Estado==2  /*Parqueadero*/ || $Estado==8 /*alistamiento*/)
						{
							$Diario++;
							$Adiario["$D"].=$V->placa.',';
						}
					}
					echo  "<td align='right'><a class='info' style='cursor:pointer' onclick=\"muestra_diario_parqueadero('".$Adiario["$D"]."','$D');\">$Diario <span style='width:300px'>Click para descargar las placas en parqueadero / alistamiento.</span></a></td></tr>";
					$Dias["$D"]+=$Diario;
					$D=date('Y-m-d',strtotime(aumentadias($D,1)));
					$Contador++;
					$Tdiario+=$Diario;
				}
				$Promedio=($Contador?round($Tdiario/$Contador,2):0);
				echo "<tr><td>Promedio:</td><td align='right'><b>$Promedio</b></td></tr></table></td>";
			}
			echo "</tr></table><hr>";
			include('inc/chart/Includes/FusionCharts.php'); // incluye rutinas para pintar las graficas
			$xml="<chart caption='TOTAL PARQUEADEROS POR DIA'>";
			foreach($Dias as $Dia => $cantidad) $xml.="<set label='$Dia' value='$cantidad' />";
			$xml.='</chart>';
			echo renderChart('inc/chart/Charts/Line.swf','',$xml,"parqueadero",800,300,false,false); // pinta la grafica de parqueaderos por dia
			echo "<hr><b>ACUMULADO FUERA DE SERVICIO POR CIUDAD entre $FI y $FF</b></br> <table cellspacing=2><tr><th>Oficina</th><th>Cantidad</th><th>Siniestro Asegurado</th>";
			$Cantidad=$Propio=0;
			// prepara la grafica de acumulados de fuera de servicio por ciudad
			$xml2="<chart caption='ACUMULADO DE FUERA DE SERVICIO POR CIUDAD' xAxisName='Ciudades' yAxisName='Cantidad' showValues='1' >";
			$categorias="<categories>";
			$serie1="<dataset seriesName='Fuera de Servicio'>";
			$serie2="<dataset seriesName='Siniestros de Asegurados'>";
			foreach($Estados_ciudad as $Ofi)
			{
				$Fueras_cantidad=$Ofi->Estados[5]->Cantidad;
				$Fueras_propios=$Ofi->Estados[5]->Cantidad;
				echo "<tr><td>$Ofi->Nombre</td><td align='right'>$Fueras_cantidad</td><td align='center'>$Fueras_propios</td></tr>";
				$categorias.="<category label='$Ofi->Nombre'/>";
				$serie1.="<set value='$Fueras_cantidad'/> ";
				$serie2.="<set value='$Fueras_propios'/> ";
				$Cantidad+=$Fueras_cantidad;
				$Propio+=$Fueras_propios;
			}
			$categorias.="</categories>";
			$serie1.="</dataset>";
			$serie2.="</dataset>";
			$xml2.=$categorias.$serie1.$serie2."</chart>";

			echo "<tr><th>Total</th><td align='right' bgcolor='dddddd'>$Cantidad</td><td align='center' bgcolor='dddddd'>$Propio</td></tr></table>";
			echo renderChart("inc/chart/Charts/MSColumn3D.swf","",$xml2,"siniestros",800,300,false,false); // pinta la grafica de acumulado de fueras de servicio por ciudad
			mysql_close($LINK);
		}
	}
	if(inlist($USUARIO,'1,2,3,7,26,27')) // usuarios: administrador, control operativo, jefe operativo, coordinador call center, jefatura flotas
	{
		echo "<table><tr>";
		// pinta la efectividad por acumulado de estados del mes
		foreach($Efectividad as $mes => $Efec)
		{echo "<td>";$Efec->pinta($Conteo_placas);echo "</td>";}
		echo "</tr></table>";
		// pinta el arreglo de alertas general, mantenimientos, soat y rtm
		if(count($AAlertas))
		{
			echo "<h3>LISTADO DE PLACAS CON ALERTAS</H3>
			<TABLE cellspacing='10'><TR><TD valign='top'><h4>REPORTE GENERAL</H4>
			<table border cellspacing='0'><tr>
						<th>#</th>
						<th>PLACA</th>
						<th>Kilometraje</th>
						<th>Mantenimiento</th>
						<th>SOAT</th>
						<th>Revisión T.M.</th>
						</tr>";
			$Contador=0;
			foreach($AAlertas as $Placa => $Datos)
			{
				$Contador++;
				echo "<tr><td align='center'>$Contador</td><td>$Placa</td><td align='right'>".($Datos->Kilometraje?coma_format($Datos->Kilometraje):'')."</td>
							<td align='right'>".($Datos->Mantenimiento?coma_format($Datos->Mantenimiento):'')."</td>
							<td align='center'>$Datos->Soat</td><td align='center'>$Datos->Revision</td></tr>";
			}
			echo "</table>
			</TD><TD valign='top'><H4>MANTENIMIENTOS</H4>
			<table border cellspacing='0'><tr><th>#</th><th>PLACA</th><th>Kilometraje</th><th>Mantenimiento</th></tr>";
			$Contador=0;
			// pinta mantenimientos
			foreach($AAlertas as $Placa => $Datos)
			{
				if($Datos->Kilometraje && $Datos->Mantenimiento)
				{
					$Contador++;
					echo "<tr><td align='center'>$Contador</td><td>$Placa</td><td align='right'>".coma_format($Datos->Kilometraje)."</td><td align='right'>".coma_format($Datos->Mantenimiento)."</td></tr>";
				}
			}
			echo "</table>
			</TD><TD valign='top'><H4>SOAT</H4>
			<table border cellspacing='0'><tr><th>#</th><th>PLACA</th><th>SOAT</th></tr>";
			$Contador=0;
			// pinta soat
			foreach($AAlertas as $Placa => $Datos)
			{
				if($Datos->Soat)
				{
					$Contador++;
					echo "<tr><td align='center'>$Contador</td><td>$Placa</td><td align='center'>$Datos->Soat</td></tr>";
				}
			}
			echo "</table>
			</TD><TD valign='top'><H4>REVISION</H4>
			<table border cellspacing='0'><tr><th>#</th><th>PLACA</th><th>Revisión T.M.</th></tr>";
			$Contador=0;
			// pinta revisiones tecnomecanicas
			foreach($AAlertas as $Placa => $Datos)
			{
				if($Datos->Revision)
				{
					$Contador++;
					echo "<tr><td align='center'>$Contador</td><td>$Placa</td><td align='center'>$Datos->Revision</td></tr>";
				}
			}
			echo "</table>
			</TD></tr></table>";
		}
		 // SI SE HA SOLICITADO UNA OFICINA-TALLER PINTA TODAS LAS ASOCIACIONES EN CONTROLES DE ESTADO RESPECTO A TALLERES.
		if($OFI)
		{
			$Oficina=qo("select * from oficina where id=$OFI");
			if($Oficina->oficina_taller)
			{
				if($Consulta=q("SELECT  t.nombre as ntaller,ciu.nombre as nciu ,v.placa
										FROM  taller t,ciudad ciu,ubicacion u,vehiculo v
										WHERE u.taller=t.id and t.ciudad=ciu.codigo and u.oficina_taller=$OFI and v.id=u.vehiculo
										and 
											(	
												(u.fecha_inicial<='$FI' and u.fecha_final>='$FI' ) or
												(u.fecha_inicial<='$FF' and u.fecha_final>='$FF' ) or
												(u.fecha_inicial>='$FI' and u.fecha_final<='$FF' ) or
												(u.fecha_inicial<='$FI' and u.fecha_final>='$FF' )
											)
										ORDER BY nciu,ntaller,placa
										"))
				{
					echo "<table border cellspacing='0'><tr><th>Ciudad</th><th>Taller</th><th>Vehiculo</th></tr>";
					while($tv=mysql_fetch_object($Consulta))
					{
						echo "<tr><td>$tv->nciu</td><td>$tv->ntaller</td><td>$tv->placa</td></tr>";
					}
					echo "</table>";
				}
			}
		}
	}
	echo "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></body>";
}



function pinta_dia_placa($Fec,$P /* placa*/)
{
	global $USUARIO,$Nsin,$Hdevolucion,$Odofinal,$ED,$US,$NTcitas,$CS,$Siniestros_propios,$PyP,$Efectividad,$Autorizaciones;
	//global $Adjudicacion_call2,$Adjudicacion_oficina,$Adjudicacion_aseguradora;
	$Contador=0;
	$mm=date('Y-m',strtotime($Fec));
	$Siniestro=false;
	$dia=array();
	for($i=0;$i<count($US[$P->id]);$i++)
	{
		if($US[$P->id][$i]->fecha_inicial<=$Fec && $Fec<=$US[$P->id][$i]->fecha_final)
		{
			// busqueda de los dias de servicio de las citas de este  vehiculo
			$Dias_servicio=7;
			if($US[$P->id][$i]->estado==7 || $US[$P->id][$i]->estado==1)  //  servicio o servicio concluido
			{
				for($ci=0;$ci<count($CS[$P->id]);$ci++) // recorre el arreglo de citas
				{
					if($CS[$P->id][$ci]->fecha==$US[$P->id][$i]->fecha_inicial)
					{
						$Dias_servicio=$CS[$P->id][$ci]->dias_servicio; // obtiene los dias de servicio a partir de la cita
						//break;
					}
				}
			}

			if($US[$P->id][$i]->estado==7 /* Servicio Concluido */ )
			{
				//$Efectividad[$mm]->acumula('serv');
				// en la variable dia lleva un contador de estados para pintarlos en una sola celda
				$dia[$Contador]['nsin']=$Nsin[$US[$P->id][$i]->id];
				if($USUARIO==1) if(!$dia[$Contador]['nsin']) $dia[$Contador]['espacio']='-';
				if($Fec<$US[$P->id][$i]->fecha_final)  // Si la fecha es menor que la fecha de conclusión
				{
					$dia[$Contador]['placa']=$P;
					$dia[$Contador]['ubicacion']=$US[$P->id][$i];
					if( dias($Fec,$US[$P->id][$i]->fecha_inicial)>$Dias_servicio )
					{
						$dia[$Contador]['estado']=91; // cambia de estado para pintar otro color
						if($US[$P->id][$i]->fecha_final==$Fec) $dia[$Contador]['span']="$Fec<br><font color='BLUE'>SERVICIO CONCLUIDO ".(inlist($USUARIO,'1,11,29')?" No.".$dia[$Contador]['nsin']:"")."</font>";
					}
					else
					{
						$dia[$Contador]['estado']=1;
						$dia[$Contador]['span']="$Fec<br><font color='BLUE'>SERVICIO CONCLUIDO ".(inlist($USUARIO,'1,11,29')?" No.".$dia[$Contador]['nsin']:"")."</font>";
					}
					$Contador++;
				}
				else                          //  si la fecha es la fecha de conclusión muestra 2 estados: servicio y concluido
				{
					$dia[$Contador]['placa']=$P;
					$dia[$Contador]['ubicacion']=$US[$P->id][$i];
					if( dias($Fec,$US[$P->id][$i]->fecha_inicial)>$Dias_servicio )
					{
						$dia[$Contador]['estado']=91; // cambia de estado para pintar otro color
						$dia[$Contador]['span']="$Fec<br><font color='BLUE'>SERVICIO CONCLUIDO ".(inlist($USUARIO,'1,11,29')?" No.".$dia[$Contador]['nsin']:"")."</font>";
					}
					else
					{
						$dia[$Contador]['estado']=1;
						$dia[$Contador]['span']="$Fec<br><font color='BLUE'>SERVICIO CONCLUIDO ".(inlist($USUARIO,'1,11,29')?" No.".$dia[$Contador]['nsin']:"")."</font>";
					}
					$Contador++;
			        //////////////////   segundo estado de conclusion ///////////////////////
					$dia[$Contador]['nsin']=$Nsin[$US[$P->id][$i]->id];
					if($USUARIO==1) if(!$dia[$Contador]['nsin']) $dia[$Contador]['espacio']='-';
					$dia[$Contador]['estado']=7;  // camtia de estado para pintar otro color
					$dia[$Contador]['span']="$Fec<br><font color='BLUE'>CONCLUYO EL SERVICIO ".(inlist($USUARIO,'1,11,29')?" No.".$dia[$Contador]['nsin']:"")."</font>";
					$dia[$Contador]['placa']=$P;
					$dia[$Contador]['ubicacion']=$US[$P->id][$i];
					$Contador++;
				}
			}
			elseif($US[$P->id][$i]->estado==1 /* En Servicio */)
			{
				$dia[$Contador]['nsin']=$Nsin[$US[$P->id][$i]->id];
				if($USUARIO==1) if(!$dia[$Contador]['nsin']) $dia[$Contador]['espacio']='-';
				//$Efectividad[$mm]->acumula('serv');
				$dia[$Contador]['placa']=$P;
				$dia[$Contador]['ubicacion']=$US[$P->id][$i];
				$dia[$Contador]['estado']=1;
				if (dias($US[$P->id][$i]->fecha_inicial,$Fec)>$Dias_servicio)  // si hay sobrepaso de 7 dias en el servicio
				{
					$dia[$Contador]['estado']=91; // cambia de estado para pintar otro color
					if($US[$P->id][$i]->fecha_final==$Fec) $dia[$Contador]['span']="$Fec<br><font color='red'>SOBREPASO DE $Dias_servicio DIAS EN EL SERVICIO ".(inlist($USUARIO,'1,11,29')?" No.".$dia[$Contador]['nsin']:"")."</font>";
				}
				elseif($US[$P->id][$i]->fecha_final==$Fec )
				{
					$dia[$Contador]['span']="$Fec<br><font color='green'>PRESTANDO SERVICIO ".(inlist($USUARIO,'1,11,29')?" No.".$dia[$Contador]['nsin']:" Regresa a las ".$Hdevolucion[$US[$P->id][$i]->id])."</font>";
					if($USUARIO!=29) $dia[$Contador]['espacio']=l($Hdevolucion[$US[$P->id][$i]->id],5); // de acuerdo al perfil busca mostrar la hora de devolución del servicio
				}
				else $dia[$Contador]['span']="$Fec<br><font color='green'>PRESTANDO SERVICIO ".(inlist($USUARIO,'1,11,29')?" No.".$dia[$Contador]['nsin']:"")."</font>";
				$Contador++;
			}
			elseif($US[$P->id][$i]->estado==4 ) // mantenimiento
			{
				//$Efectividad[$mm]->acumula('prev');
				$dia[$Contador]['placa']=$P;
				$dia[$Contador]['ubicacion']=$US[$P->id][$i];
				$dia[$Contador]['estado']=4;
				if(dias($US[$P->id][$i]->fecha_inicial,$Fec)>2 && !inlist($USUARIO,'11,29'))
				{
					// busca el sobrepaso de dos dias en un mantenimiento y alerta en cada celda de sobrepaso
					if($US[$P->id][$i]->fecha_final==$Fec) $dia[$Contador]['span']="$Fec<br><font color='green'>".$US[$P->id][$i]->nestado." Sobrepaso de 2 días, requiere justificación.</font>";
					$dia[$Contador]['estilo']="text-decoration:blink;color:ffffff;font-weight:bold;";
					$dia[$Contador]['espacio']='X';
				}
				elseif($US[$P->id][$i]->fecha_inicial==$Fec || $US[$P->id][$i]->fecha_final==$Fec) $dia[$Contador]['span']="$Fec<br><font color='green'>".$US[$P->id][$i]->nestado."</font>";
				$Contador++;
			}
			elseif($US[$P->id][$i]->estado==5 ) // fuera de servicio
			{
				//if($US[$P->id][$i]->siniestro_propio) $Efectividad[$mm]->acumula('sin'); else $Efectividad[$mm]->acumula('corr');
				if($US[$P->id][$i]->siniestro_propio) $Siniestro=true; else $Siniestro=false;
				$dia[$Contador]['placa']=$P;
				$dia[$Contador]['ubicacion']=$US[$P->id][$i];
				$dia[$Contador]['estado']=5;
				if(dias($US[$P->id][$i]->fecha_inicial,$Fec)>7 && !inlist($USUARIO,'11,29'))
				{
					// busca el sobrepaso de 7 dias y alerta en cada celda de sobrepaso
					if($US[$P->id][$i]->fecha_final==$Fec) $dia[$Contador]['span']="$Fec<br><font color='green'>".$US[$P->id][$i]->nestado." Sobrepaso de 7 días, requiere justificación.</font>";
					$dia[$Contador]['estilo']="text-decoration:blink;color:ffffff;font-weight:bold;";
					$dia[$Contador]['espacio']='X';
				}
				else $dia[$Contador]['span']="$Fec<br><font color='green'>".$US[$P->id][$i]->nestado."</font>";
				$Contador++;
				if($US[$P->id][$i]->siniestro_propio) $Siniestros_propios++;
			}
			else // demás estados: Parqueadero, Mantenimiento, Mant. programado, Gerencia, Transito, traslados
			{
				//if($US[$P->id][$i]->estado==2) $Efectividad[$mm]->acumula('parq'); else $Efectividad[$mm]->acumula('otr');
				$dia[$Contador]['placa']=$P;
				$dia[$Contador]['ubicacion']=$US[$P->id][$i];
				$dia[$Contador]['estado']=$US[$P->id][$i]->estado;
				$dia[$Contador]['span']="$Fec<br><font color='green'>".$US[$P->id][$i]->nestado."</font>";
				$Contador++;
			}
			$Odofinal=($US[$P->id][$i]->odometro_final>$Odofinal?$US[$P->id][$i]->odometro_final:$Odofinal); // siempre obtiene el odometro final para cada vehiculo
		}
	}
	// de acuerdo al número de eventos por día, se calcula el tamaño de cada sub-celda para presentar la celda completa
	if(count($dia))
	{
		$ultimo_del_dia=count($dia)-1;
		switch(count($dia))
		{
			case 1:$Resultado=pinta_est($dia[0],$Fec,50);$ED["$P->id-$Fec"]=$dia[0]['estado']; break;
			case 2:$Resultado=pinta_est($dia[0],$Fec,25).pinta_est($dia[1],$Fec,24);$ED["$P->id-$Fec"]=$dia[1]['estado'];break;
			case 3:$Resultado=pinta_est($dia[0],$Fec,16).pinta_est($dia[1],$Fec,16).pinta_est($dia[2],$Fec,16);$ED["$P->id-$Fec"]=$dia[2]['estado'];break;
			case 4:$Resultado=pinta_est($dia[0],$Fec,12).pinta_est($dia[1],$Fec,12).pinta_est($dia[2],$Fec,11).pinta_est($dia[3],$Fec,12);$ED["$P->id-$Fec"]=$dia[3]['estado'];break;
			case 5:$Resultado=pinta_est($dia[0],$Fec,9).pinta_est($dia[1],$Fec,9).pinta_est($dia[2],$Fec,9).pinta_est($dia[3],$Fec,9).pinta_est($dia[4],$Fec,10);$ED["$P->id-$Fec"]=$dia[4]['estado'];break;
			case 6:$Resultado=pinta_est($dia[0],$Fec,8).pinta_est($dia[1],$Fec,7).pinta_est($dia[2],$Fec,8).pinta_est($dia[3],$Fec,7).pinta_est($dia[4],$Fec,8).pinta_est($dia[5],$Fec,7);$ED["$P->id-$Fec"]=$dia[5]['estado'];break;
			case 7:$Resultado=pinta_est($dia[0],$Fec,6).pinta_est($dia[1],$Fec,6).pinta_est($dia[2],$Fec,6).pinta_est($dia[3],$Fec,7).pinta_est($dia[4],$Fec,7).pinta_est($dia[5],$Fec,6).pinta_est($dia[6],$Fec,6);$ED["$P->id-$Fec"]=$dia[6]['estado'];break;
			case 8:$Resultado=pinta_est($dia[0],$Fec,5).pinta_est($dia[1],$Fec,5).pinta_est($dia[2],$Fec,6).pinta_est($dia[3],$Fec,6).pinta_est($dia[4],$Fec,6).pinta_est($dia[5],$Fec,5).pinta_est($dia[6],$Fec,5).pinta_est($dia[7],$Fec,5);$ED["$P->id-$Fec"]=$dia[7]['estado'];break;
			case 9:$Resultado=pinta_est($dia[0],$Fec,4).pinta_est($dia[1],$Fec,5).pinta_est($dia[2],$Fec,5).pinta_est($dia[3],$Fec,5).pinta_est($dia[4],$Fec,5).pinta_est($dia[5],$Fec,5).pinta_est($dia[6],$Fec,5).pinta_est($dia[7],$Fec,4).pinta_est($dia[8],$Fec,4);$ED["$P->id-$Fec"]=$dia[8]['estado'];break;
			// cuando se pasa de 9 estados, pinta únicamente el último estado del día.
			default: $Resultado=pinta_est($dia[$ultimo_del_dia],$Fec,50);$ED["$P->id-$Fec"]=$dia[$ultimo_del_dia]['estado']; break;
		}
		// de acuerdo a cada estado y otras opciones, se acumula en la estadistica de efectividad
		if($dia[count($dia)-1]['estado']==7) $Efectividad[$mm]->acumula('serv');
		elseif($dia[count($dia)-1]['estado']==1) $Efectividad[$mm]->acumula('serv');
		elseif($dia[count($dia)-1]['estado']==4) $Efectividad[$mm]->acumula('prev');
		elseif($dia[count($dia)-1]['estado']==5 && $Siniestro) $Efectividad[$mm]->acumula('sin');
		elseif($dia[count($dia)-1]['estado']==5 && !$Siniestro) $Efectividad[$mm]->acumula('corr');
		elseif($dia[count($dia)-1]['estado']==2) $Efectividad[$mm]->acumula('parq');
		else $Efectividad[$mm]->acumula('otr');
		return $Resultado;
	}
	$Clase_blanco='cb';
	$Span='';
	$id_Cita=0;
	// BUSQUEDA DE CITAS O ADJUDICACIONES PARA MOSTRAR EN GRIS LA CELDA
	if(!inlist($USUARIO,'11,29'))
	{
		for($i=0;$i<count($CS[$P->id]);$i++) // recorre la tabla de citas
		{
			if($Autorizaciones[$CS[$P->id][$i]->siniestro]) // recorre la tabla de autorizaciones y busca el estado actual de cada autorización
			{
				$Aut=$Autorizaciones[$CS[$P->id][$i]->siniestro];
				if($Aut->estado=='E') $Autorizacion="<b style='color:AAAA00'>EN PROCESO DE AUTORIZACION</b>";
				if($Aut->estado=='A') $Autorizacion="<b style='color:22AA22'>AUTORIZADO</b>";
			}
			else $Autorizacion="<b style='color:bbbb55'>Pendiente</b>";

			$Fec_fin=date('Y-m-d',strtotime(aumentadias($CS[$P->id][$i]->fecha,$CS[$P->id][$i]->dias_servicio)));
			if($Fec>=$CS[$P->id][$i]->fecha && $Fec<=$Fec_fin)  // pinta en gris los días de servicio que dice la cita
			{
				$id_Cita=$CS[$P->id][$i]->idc;
				$Clase_blanco='cc';
				$Fec_span=date('Y-m-d',strtotime(aumentadias($CS[$P->id][$i]->fecha,1)));
				if($Fec<=$Fec_span) $Span=$Autorizacion; else $Span='';
				$Span.="<span style='width:300px' '";
				//".($USUARIO==1?"onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NTcitas&id=".$CS[$P->id][$i]->idc." ',0,0,800,900,'mc');\"":"").
				$Span.="><table><tr><th>Cita de Entrega</th></tr><tr><td nowrap='yes'>Siniestro: <b>".$CS[$P->id][$i]->numero."</b></td></tr>";
				$Span.="<tr><td>Fecha y hora: <b>".$CS[$P->id][$i]->fecha.' '.$CS[$P->id][$i]->hora."</b></td></tr>";
				$Span.="<tr><td>Conductor: <b>".$CS[$P->id][$i]->conductor."</b></td></tr>";
				$Span.="<tr><td>Agendada por: <b>".$CS[$P->id][$i]->agendada_por."</b></td></tr>";
				$Span.="<tr><td>Estado de Cita: $Autorizacion</td></tr></table></span>";
				break;
			}
		}
	}
	// BUSQUEDA DE PICO Y PLACA
	$diasemana=date('w',strtotime($Fec));
	$Ultimod=r($P->placa,1);
	if($PyP[$P->ultima_ubicacion][$diasemana]) {if(strpos(' '.$PyP[$P->ultima_ubicacion][$diasemana]->placas,$Ultimod)) $Span.="<img src='gifs/picoplaca.png' border='0' height='11' alt='Pico y Placa' title='Pico y Placa'>";}
	if(!$id_Cita && inlist($USUARIO,'1,2,7,10,13,27'))
		$Resultado="<a class='$Clase_blanco' oncontextmenu=\"menu_blanco($P->id,'$Fec');void(null);return false;\" onclick=\"menu_blanco($P->id,'$Fec');void(null);return false;\"  >&nbsp;$Span</a>"; // en pico y placa no deja adjudicar
	elseif(inlist($USUARIO,'1,4,26') && $_SESSION['Adjudicacion_SINIESTRO']) // si esta adjudicando un servicio desde call center
		$Resultado="<a class='$Clase_blanco' oncontextmenu=\"menu_adjudicar($P->id,'$Fec');void(null);return false;\" onclick=\"menu_adjudicar($P->id,'$Fec');void(null);return false;\" >&nbsp;$Span</a>";
	elseif($id_Cita && (inlist($USUARIO,'1,26') || ($USUARIO==4 && $_SESSION['nivel_CALLCENTER']>=2))) // en nivel 2 de call center permite re-adjudicar una cita
		$Resultado="<a class='$Clase_blanco' oncontextmenu=\"menu_citaservicio($id_Cita);void(null);return false;\" onclick=\"menu_citaservicio($id_Cita);void(null);return false;\" >&nbsp;$Span</a>";
	else
		$Resultado="<a class='$Clase_blanco'>&nbsp;$Span</a>"; // acceso normal
	return $Resultado;
}

function pinta_est($Celda,$F,$t) // funcion que pinta cada sub-celda
{
	global $Emb_Aseg,$USUARIO;

	$espacios=$Celda['espacio']?$Celda['espacio']:"&nbsp;";
	if($Celda['span'])
	{
		if(!inlist($USUARIO,'11,29') /*Aseguradora 1*/)
		{
			$Dif_odometros=$Celda['ubicacion']->odometro_final-$Celda['ubicacion']->odometro_inicial;  // halla la diferencia en odometros del estado
			if($Dif_odometros<0) $Dif="<font color='red'>".coma_format($Dif_odometros)."</font>"; // si la diferencia es negatiuva la pinta en rojo
			elseif($Dif_odometros==0) $Dif="0";
			elseif($Dif_odometros>700) $Dif="<font color='ff3355'>".coma_format($Dif_odometros)."</font>";// si la diferencia pasa de 700km lo pinta en rojo
			else $Dif="<font color='green'>".coma_format($Dif_odometros)."</font>";
		}
		if($Celda['ubicacion']->siniestro_propio) $Siniestro_propio="<tr><td colspan=2><font color='red'><b>Siniestro Asegurado</b></font></td></tr>"; else $Siniestro_propio=""; // verifica la condición de siniestro propio
		$Resultado="<a class='c".$Celda['estado']."' style='width:".$t."px;".$Celda['estilo']."' ";
		$Resultado.=(!inlist($USUARIO,'11,29')?"oncontextmenu=\"mc_evento(".$Celda['ubicacion']->id.",'$F',this);return false;void(null);\" onclick=\"mc_evento(".$Celda['ubicacion']->id.",'$F',this);\" ":""); // menu de celda para ver el contenido del evento
		$Resultado.=">$espacios<span>".
		"<table><tr><td rowspan=2><img src='".($Celda['placa']->flota==6?$Emb_Aseg[$Celda['ubicacion']->flota]:($Celda['placa']->flota==$Celda['ubicacion']->flota?$Emb_Aseg[$Celda['ubicacion']->flota]:$Celda['placa']->emblema_marca)).
		"' border='0' height='30px'></td><td><b class='sp_placa'>" . $Celda['placa']->placa."</b></td></tr>".
		"<tr><td nowrap='yes'>".$Celda['span']."</td></tr><tr><td nowrap='yes' colspan=2>".$Celda['ubicacion']->noficina."</td></tr>".$Siniestro_propio; // pinta los emblemas de la flota y de la marca del vehiculo
		if(!inlist($USUARIO,'11,29') /*Aseguradora 1*/)
		$Resultado.="<tr><td colspan='2'>Fecha: ".$Celda['ubicacion']->fecha_inicial." - ".$Celda['ubicacion']->fecha_final."</td></tr>".
								"<tr><td nowrap='yes' colspan='2' style='color:333399;font-weight:bold;'>Kilometraje: ".
								coma_format($Celda['ubicacion']->odometro_inicial)." - ".coma_format($Celda['ubicacion']->odometro_final)." Diferencia: $Dif</td></tr>";
		$Resultado.="</table></span></a>";
	}
	else
	{
		$Resultado="<a class='c".$Celda['estado']."' style='width:".$t."px;".$Celda['estilo']."' ";
		$Resultado.=(!inlist($USUARIO,'11,29')?"oncontextmenu=\"alert('busque el extremo del estado');return false;void(null);\" onclick=\"alert('busque el extremo del estado');\" ":"");
		$Resultado.=">$espacios</a>";
	}
	return $Resultado;
}

function dia_semana($d) // retorna las iniciales de los dias de la semana
{
	switch($d)
	{
		case 0: return 'Do';
		case 1: return 'Lu';
		case 2: return 'Ma';
		case 3: return 'Mi';
		case 4: return 'Ju';
		case 5: return 'Vi';
		case 6: return 'Sá';
	}
}

//****-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------





function menu_contextual_celda() // cuando se da click sobre una sub-celda aparece este menu
{
	global $idub,$fecha,$objeto,$Emb_Aseg,$USUARIO,$Aplacas;
	global $Adjudicacion_oficina,$Adjudicacion_aseguradora;
	include('inc/link.php');  // conexion a la base de datos
	$D=qom("Select u.*,t_vehiculo(vehiculo) as nvehiculo,e.nombre as nestado,e.color_co from ubicacion u,estado_vehiculo e where u.id=$idub and e.id=u.estado",$LINK); // trae los datos del vehiculo y la hubicación
	$V=qom("select * from vehiculo where id=$D->vehiculo ",$LINK); //trae los datos del vehiculo
	$Linea=qom("select * from linea_vehiculo where id=$V->linea",$LINK); // trae los datos de la linea del vehiculo
	$Marca=qom("select * from marca_vehiculo where id=$Linea->marca",$LINK); // trae los datos de la marca del vehiculo
	$Ofi=qom("select * from oficina where id=$V->ultima_ubicacion",$LINK); // trae los datos de la oficina
	
	$Nplaca=substr($D->nvehiculo,0,3).'-'.substr($D->nvehiculo,3,3); // le da mascara a la placa
	$U=qom("select * from ubicacion where id=$idub",$LINK); // trae los datos de la ubicacion
	$Fc=fecha_completa($fecha); // le da formato a la fecha
	if(inlist($USUARIO,'1,4,26')) // CALL CENTERA
	$Ultimo=qom("select * from ubicacion where vehiculo=$D->vehiculo order by fecha_final desc, id desc limit 1",$LINK); // obtiene el último estado del vehículo
	else  // TODOS
	$Ultimo=qom("select * from ubicacion where vehiculo=$D->vehiculo and fecha_final<='$fecha' order by fecha_final desc, id desc limit 1",$LINK);
	mysql_close($LINK); // cierra la conexion con la base de datos
	html(); // pinta las cabeceras html
	$Nt1=tu('siniestro','id'); // obtiene la llave id de la tabla de siniestros
	$Nt1_old=tu('siniestro_hst','id'); // obtiene la llave id de la tabla de siniestros historicos
	$Nt2=tu('ubicacion','id'); // obtiene la llave id de la tabla de ubicaciones
	$Nt3=tu('cita_servicio','id'); // obtiene la llave id de la tabla de cita de servicios
	// pinta todas las herramientas javascript
	echo "
		<script language='javascript'>
			function abre_siniestro(ids) {modal('marcoindex.php?Acc=mod_reg&id='+ids+'&Num_Tabla=".($fecha<'2013-02-01'?$Nt1_old:$Nt1)."',0,0,700,1000,'siniestro');}
			function abre_ubicacion() {modal('marcoindex.php?Acc=mod_reg&id=$idub&Num_Tabla=$Nt2',0,0,700,1000,'ubicacion');}
			function abre_cita(id) {modal('marcoindex.php?Acc=mod_reg&id='+id+'&Num_Tabla=$Nt3',0,0,700,1000,'cita');}
			function borrar_ubicacion() {if(confirm('Seguro de eliminar esta ubicacion?')){window.open('zcontrol_operativo3Dev.php?Acc=borrar_ubicacion&id='+$idub,'Oculto_tablero');}}
			function desliga_siniestro(ids){if(confirm('Seguro de desligar el estado de este siniestro?'))	{window.open('zcontrol_operativo3Dev.php?Acc=desligar_siniestro&ids='+ids,'Oculto_tablero');}}
			function ajustar_fecha_inicial(){window.open('zcontrol_operativo3Dev.php?Acc=ajustar_fecha_inicial&id=$idub&fecha=$fecha','Oculto_tablero');}
			function ajustar_fecha_final(){window.open('zcontrol_operativo3Dev.php?Acc=ajustar_fecha_final&id=$idub&fecha=$fecha','Oculto_tablero');}
			function adicionar_evento(){window.open('zcontrol_operativo3Dev.php?Acc=adicionar_evento&vehiculo=$D->vehiculo&fecha=$fecha&Nomueve=1','Menu_contextual_celda');}
			function cerrar_mantenimiento(){window.open('zcontrol_operativo3Dev.php?Acc=cerrar_mantenimiento&idub=$idub&Nomueve=1','Menu_contextual_celda');}
			function cerrar_fuera_servicio(){window.open('zcontrol_operativo3Dev.php?Acc=cerrar_fuera_servicio&idub=$idub&Nomueve=1','Menu_contextual_celda');}
			function cerrar_fuera_operacion(){window.open('zcontrol_operativo3Dev.php?Acc=cerrar_fuera_operacion&idub=$idub&Nomueve=1','Menu_contextual_celda');}
			function cerrar_transito(){window.open('zcontrol_operativo3Dev.php?Acc=cerrar_transito&idub=$idub&Nomueve=1','Menu_contextual_celda');}
			function cerrar_alistamiento(){window.open('zcontrol_operativo3Dev.php?Acc=cerrar_alistamiento&idub=$idub&Nomueve=1','Menu_contextual_celda');}
			function cerrar_domicilio(){window.open('zcontrol_operativo3Dev.php?Acc=cerrar_domicilio&idub=$idub&Nomueve=1','Menu_contextual_celda');}

			function cerrar_siniestro_pp(){window.open('zcontrol_operativo3Dev.php?Acc=cerrar_siniestro_pp&idub=$idub&Nomueve=1','Menu_contextual_celda');}
			
			function cerrar_siniestro_pt(){window.open('zcontrol_operativo3Dev.php?Acc=cerrar_siniestro_pt&idub=$idub&Nomueve=1','Menu_contextual_celda');}

			function adicionar_estado(){window.open('zcontrol_operativo3Dev.php?Acc=adicionar_estado&vehiculo=$D->vehiculo&fecha=$fecha&Nomueve=1','Menu_contextual_celda');}

			function cerrar_reparacion(){window.open('zcontrol_operativo3Dev.php?Acc=cerrar_reparacion&idub=$idub&Nomueve=1','Menu_contextual_celda');}

			
			function cerrar_entre_parque(){window.open('zcontrol_operativo3Dev.php?Acc=cerrar_entre_parque&idub=$idub&Nomueve=1','Menu_contextual_celda');}

			
			function activar_mantenimiento(){window.open('zcontrol_operativo3Dev.php?Acc=activar_mantenimiento&idub=$idub&Nomueve=1','Menu_contextual_celda');}
			function cerrar_servicio_especial()	{window.open('zcontrol_operativo3Dev.php?Acc=cerrar_servicio_especial&idub=$idub&Nomueve=1','Menu_contextual_celda');}
			function adicionar_observaciones(){window.open('zcontrol_operativo3Dev.php?Acc=adicionar_observaciones&id=$idub','Menu_contextual_celda');}
			function cambiar_a_fueraservicio(){window.open('zcontrol_operativo3Dev.php?Acc=cambiar_a_fueraservicio&id=$idub','Menu_contextual_celda');}
			function cambiar_a_mantenimiento(){window.open('zcontrol_operativo3Dev.php?Acc=cambiar_a_mantenimiento&id=$idub','Menu_contextual_celda');}
			function marcar_siniestro_propio(){window.open('zcontrol_operativo3Dev.php?Acc=marcar_siniestro_propio&id=$idub','Menu_contextual_celda');}
			function desmarcar_siniestro_propio(){window.open('zcontrol_operativo3Dev.php?Acc=desmarcar_siniestro_propio&id=$idub','Menu_contextual_celda');}
			function insertar_comparendo(){if(confirm('Seguro que desea insertar comparendo?')) window.open('zcontrol_operativo3Dev.php?Acc=insertar_comparendo&id=$idub','Oculto_tablero');}
			function ver_autorizaciones(id){modal('zautorizaciones.php?Acc=ver_autorizaciones&id='+id,0,0,500,900,'Autorizaciones');}
			function ver_pendientes_alistamiento(){modal('zalistamiento.php?Acc=ver_pendientes&id=$D->vehiculo',0,0,500,900,'Pendientes');}
			function cambio_temporal()	{modal('zcontrol_operativo3Dev.php?Acc=cambio_temporal&id=$idub',0,0,400,500,'Cambio_temporal');}
			function retorno_flota(){if(confirm('Desea retornar este Vehículo a su flota original?')) {modal('zcontrol_operativo3Dev.php?Acc=retorno_flota&id=$idub',0,0,10,10,'Retorno');}}
			function cambiar_fecha_devolucion(){modal('zcontrol_operativo3Dev.php?Acc=cambiar_fecha_devolucion&id=$idub',0,0,400,500,'cambio_fecha');}
			function balance_estado(){modal('zbalance_estado.php?Acc=ver_balance&id=$idub',0,0,400,500,'balance_est');}
			function menu_adjudicar() {modal('zcallcenter2.php?Acc=adjudicar_vehiculo&v=$V->id&f=$fecha',0,0,500,500,'Adjudicacion');}
			function adjudicacion_finalizada() {parent.parent.adjudicacion_finalizada();}
			function tomarid2balance(){window.open('zcontrol_operativo3Dev.php?Acc=tomar_id_para_balance&id=$idub','Oculto_tablero');}
			function asignarid2balance(){window.open('zcontrol_operativo3Dev.php?Acc=asignar_id_para_balance&id=$idub','Oculto_tablero');}
			function crear_taller(){window.open('zcontrol_operativo3Dev.php?Acc=crear_estado_taller&idub=$idub','Menu_contextual_celda');}
			function cerrar_taller(){window.open('zcontrol_operativo3Dev.php?Acc=cerrar_estado_taller&idub=$idub','Menu_contextual_celda');}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#bbbbbb' name='Context_Celda' id='Context_Celda' align='center' width='500px'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();'><img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar menu</b></td>
		<td rowspan='3' align='center' bgcolor='eeeeee'>".($U->flota==$V->flota?"<img src='".$Emb_Aseg[$U->flota]."' border='0' align='middle' height='30px'>":"<img src='".$Emb_Aseg[$V->flota]."' border='0' align='middle' height='30px'>")." <img src='".($Linea->emblema_f?$Linea->emblema_f:$Marca->emblema_f)."' border='0' align='middle' height='30px'></td></tr>
		<tr><td colspan='1' align='center' bgcolor='eeeeee'>Fecha: <b>$Fc</b></td></tr>
		<tr><td align='center' colspan='1' bgcolor='eeeeee' class='placa' style='background-image:url(img/placa.jpg);'>$Nplaca</td></tr>
		<tr><td bgcolor='eeeeee'>Oficina: <b>$Ofi->nombre</b></td>
		<td bgcolor='eeeeee'>Odómetro: <b>".coma_format($D->odometro_inicial)."</b> - <b>".coma_format($D->odometro_final)."</b> Diferencia: <span style='background-color:ffffff'>";
	$Diferencia_odometros=$D->odometro_final-$D->odometro_inicial; // halla la diferencia de odometros
	if($Diferencia_odometros==0) echo "<b> 0</b>";
	elseif($Diferencia_odometros<0) echo "<b style='color:red'>".coma_format($Diferencia_odometros)."</b>"; // si es negativa la pinta en rojo
	elseif($Diferencia_odometros>700) echo "<b style='color:ff3344;'>".coma_format($Diferencia_odometros)."</b>"; // si pasa de 700km la pinta en rojo
	else echo "<b style='color:green;'>".coma_format($Diferencia_odometros)."</b>";
	echo "</span></td></tr>
		<tr><td nowrap='yes' bgcolor='eeeeee'>Estado: <b>$D->nestado</b></td><td bgcolor='eeeeee' align='center'><div style='width:50px;background-color:$D->color_co;'>&nbsp;</div></td></tr>
		<tr><td nowrap='yes' colspan='2' bgcolor='eeeeee'>Fecha(s) de Estado: <b>$D->fecha_inicial - $D->fecha_final</b></td></tr>";
	if(inlist($USUARIO,'1,2,7,10,13,26,27,23'))
	{
		if(($fecha==date('Y-m-d') && $D->estado!=1 && $D->estado!=91) || $USUARIO==1)
		{
			// MUESTRA LA ACTIVACION DE MANTENIMIENTO a partir de un MANTENIMIENTO PROGRAMADO
			if($Ultimo->estado==92 || $D->estado==92 /* Mantenimiento Programado*/) 
			{echo "<tr><td onclick='activar_mantenimiento();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Activar el Mantenimiento</td></tr>";}
			// MUESTRA EL CIERRE DE SERVICIO ESPECIAL GERENCIA
			elseif($Ultimo->estado==93 || $D->estado==93 /* Servicio especial Gerencia */)
			{echo "<tr><td onclick='cerrar_servicio_especial();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cerrar el Servicio Especial Gerencia</td></tr>";}
			// PERMITE ADICIONAR UN ESTADO DESDE PARQUEADERO, CONCLUIDO, TRASLADOS Y DOMICILIO
			elseif($Ultimo->estado==2 /*parqueadero */ || $Ultimo->estado==7 /*concluido*/ || $Ultimo->estado==94 /*traslados*/ || $Ultimo->estado==96 /*Domicilio*/)
			{
				if(inlist($USUARIO,'1,2,7,10,13,27'))  /* solamente pueden adicionar Gerencia y Jefe Operativo, Operario Oficina, Auxiliar de Informacion, Jefatura de Flotas */
				{
					echo "<tr><td onclick='adicionar_evento();' style='cursor:pointer' colspan='2'><img src='gifs/standar/nuevo_registro_blanco.png' border='0'> Adicionar un Estado</td></tr>";
				}
				// MUESTRA CAMBIO TEMPORAL DE FLOTA Y RETORNO DE FLOTA
				if(inlist($USUARIO,'1,2,7,26')) /* Gerencia, Jefe Operativo, Coordinador Call Center*/
				{
					if($U->flota==$V->flota)
						echo "<tr><td onclick='cambio_temporal();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cambio Temporal de Flota</td></tr>";
					else
						echo "<tr><td onclick='retorno_flota();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Retorno de Flota</td></tr>";
				}
				
			}
			// MUESTRA EL CIERRE DE MANTENIMIENTO
			elseif($Ultimo->estado==4 /*Mantenimiento*/)
			{echo "<tr><td onclick='cerrar_mantenimiento();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cerrar el Mantenimiento</td></tr>";}
			// MUESTRA EL CIERRE DE FUERA DE SERVICIO
			elseif($Ultimo->estado==5 /*Fuera de servicio*/)
			{echo "<tr><td onclick='cerrar_fuera_servicio();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cerrar el Fuera de Servicio</td></tr>";}
			// MUESTRA EL CIERRE DE TRANSITO
			elseif($Ultimo->estado==6 /*En transito*/)
			{echo "<tr><td onclick='cerrar_transito();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cerrar el Transito</td></tr>";}
			// MUESTRA EL CIERRE DE ALISTAMIENTO
			elseif($Ultimo->estado==8 /*Alistamiento*/)
			{echo "<tr><td onclick='cerrar_alistamiento();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'>
			Cerrar Alistamiento</td></tr>";}
			
			if($Ultimo->estado==96)
			{echo "<tr><td onclick='cerrar_domicilio();' style='cursor:pointer' colspan='2'>
			<img src='gifs/standar/Next.png' border='0'>
			Cerrar Domicilio </td></tr>";}
			
			
			if($Ultimo->estado==106)
			{echo "<tr><td onclick='cerrar_siniestro_pp();' style='cursor:pointer' colspan='2'>
			<img src='gifs/standar/Next.png' border='0'>
			Cerrar Siniestro Perdida Parcial  </td></tr>";}
			
			if($Ultimo->estado==107)
			{echo "<tr><td onclick='cerrar_siniestro_pt();' style='cursor:pointer' colspan='2'>
			<img src='gifs/standar/Next.png' border='0'>
			Cerrar Siniestro Perdida Total  </td></tr>";}
			
			if($Ultimo->estado==108)
			{echo "<tr><td onclick='cerrar_reparacion();' style='cursor:pointer' colspan='2'>
			<img src='gifs/standar/Next.png' border='0'>
			Cerrar Reparación   </td></tr>";}
			
			
			if($Ultimo->estado==94)
			{echo "<tr><td onclick='cerrar_entre_parque();' style='cursor:pointer' colspan='2'>
			<img src='gifs/standar/Next.png' border='0'>
			Cerrar Traslado Entre Parqueaderos    </td></tr>";}
			

			if($Ultimo->estado==108 /*SERVICIO */ || $Ultimo->estado==106 || $Ultimo->estado==105)
			{echo "<tr><td onclick='adicionar_estado();' style='cursor:pointer' colspan='2'>
			<img src='gifs/standar/Next.png' border='0'>
			 Adicionar estado   </td></tr>";}
			
			// MUESTRA EL CIERRE DE FUERA DE OPERACION
			elseif($Ultimo->estado==9 /*Fuera de Operación*/)
			{echo "<tr><td onclick='cerrar_fuera_operacion();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cerrar el Fuera de Operación</td></tr>";}
			// MUESTRA LA CREACION DE ESTADO DE TALLER
			if(inlist($USUARIO,'1,2,7,10,27') && inlist($Ultimo->estado,'2,4,5,6,8,92,93,94') && inlist($D->estado,'2,4,5,6,8,92,93,94'))
			{echo "<tr><td onclick='crear_taller();' style='cursor:pointer' colspan=2><img src='gifs/martillo.gif' border='0' style='height:16px;margin-right:4px;'>Crear estado de Taller</td></tr>";}
			// MUESTRA EL CIERRER DE ESTADO DE TALLER
			if(inlist($USUARIO,'1,2,7,10,27') && inlist($Ultimo->estado,'10') )
			{echo "<tr><td onclick='cerrar_taller();' style='cursor:pointer' colspan=2><img src='gifs/standar/dsn_sql.png' border='0' style='height:16px;margin-right:4px;'>Cerrar estado de Taller</td></tr>";}

		}
	}
	
		// adicionar_estado
	
	
	//  MUESTRA CAMBIO A FUERA DE SERVICIO
	if(inlist($USUARIO,'1,2,7') && $D->estado==4) 
	{echo "<tr><td onclick='cambiar_a_fueraservicio();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cambiar a <b>Fuera de Servicio</b></td></tr>";}
	
	
	// MUESTRA CAMBIO A MANTENIMIENTO
	if(inlist($USUARIO,'1,2,7') && $D->estado==5) 
	{echo "<tr><td onclick='cambiar_a_mantenimiento();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Cambiar a <b>Mantenimiento</b></td></tr>";}
	// MUESTRA SINIESTRO ASEGURADO
	if(inlist($USUARIO,'1,2,3,7,13') && $D->estado==5) 
	{echo "<tr><td onclick='marcar_siniestro_propio();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Marcar <b>Siniestro Asegurado</b></td></tr>
				<tr><td onclick='desmarcar_siniestro_propio();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Des-marcar <b>Siniestro Asegurado</b></td></tr>";}
	// MUESTRA INSERCION EN TABLA DE COMPARENDOS
	if(inlist($USUARIO,'1,2,3,6,7,13')) 
	{echo "<tr><td onclick='insertar_comparendo();' style='cursor:pointer' colspan='2'><img src='gifs/standar/Next.png' border='0'> Insertar en tabla de Comparendos</td></tr>";}
	// si la flota del vehiculo es la misma de la primaria del vehiculo MUESTRA OBSERVACIONES de lo contrario las oculta
	if($D->flota==$V->flota)  
	{echo "<tr><td colspan='2' width='400px' bgcolor='ffffff'><b>Observaciones de Estado:</b>",nl2br($D->observaciones)."<br>".nl2br($D->obs_mantenimiento)."</td></tr>";}
	
	if($D->estado==1 || $D->estado==7 || $D->estado==103 || $D->estado==104 ) // servicio o servicio concluido
	{
		// obtiene los datos del siniestro asociado
		if(!$Sin=qo("select s.id,s.numero,a.nombre as anombre,a.emblema_f as aemb,s.observaciones,s.obsconclusion,img_inv_salida_f as acta1,img_inv_entrada_f as acta2,t_estado_siniestro(s.estado) as nestsin from siniestro s,aseguradora a where a.id=s.aseguradora  and s.ubicacion=$idub"))
		$Sin=qo("select s.id,s.numero,a.nombre as anombre,a.emblema_f as aemb,s.observaciones,s.obsconclusion,img_inv_salida_f as acta1,img_inv_entrada_f as acta2,t_estado_siniestro(s.estado) as nestsin from siniestro_hst s,aseguradora a where a.id=s.aseguradora  and s.ubicacion=$idub");
		if(!$Sin && $fecha<'2013-01-01') $Sin=qo("select s.id,s.numero,a.nombre as anombre,a.emblema_f as aemb,s.observaciones,s.obsconclusion,img_inv_salida_f as acta1,img_inv_entrada_f as acta2,t_estado_siniestro(s.estado) as nestsin from siniestro_hst_2012 s,aseguradora a where a.id=s.aseguradora  and s.ubicacion=$idub");
		if($Sin) presenta_datos_siniestro($Sin,$Nt1,$D,$V); // muestra los datos del siniestro
		else	 // si no hay siniestro asociado, muestra una casilla para asociar el siniestro apropiado buscando por numero de siniestro
		echo "<tr><td colspan=2 bgcolor='ffeeee'>Este servicio no tiene un siniestro asignado</td></tr>
					<tr><td colspan=2 nowrap='yes' bgcolor='eeeeee'><form action='zcontrol_operativo3Dev.php' method='post' target='Oculto_tablero' name='forma1' id='forma1'>
						Asignar: <input type='text' name='numero' id='numero'><input type='button' value='Asignar' onclick=\"if(this.form.numero.value) {
						this.form.submit();} else {alert('No ha escrito un número de siniestro para asignar');return false;}\">
						<input type='hidden' name='Acc' value='asignar_siniestro'><input type='hidden' name='idub' value='$idub'></form>
					</td></tr>";
		
		if($Cita=qo("select * from cita_servicio where placa='$V->placa' and fecha='$U->fecha_inicial' and estado='C' ")) // obtiene los datos de la cita de ese siniestro
		{
			echo "<tr><td colspan='2' bgcolor='eeeeee'><b>Obs Cita:</b> ".nl2br($Cita->observaciones)."</td></tr>
						<tr><td colspan='2' bgcolor='eeeeee'><b>Obs Devolución:</b> ".nl2br($Cita->obs_devolucion)."</td></tr>";
			if(inlist($USUARIO,'1,2,4,7,10,13')) // MUESTRA MODIFICACION DE LA CITA
			{ // pinta el boton de modificacion de la cita
				echo "<tr><td colspan='2' style='cursor:pointer' onclick='abre_cita($Cita->id);'><img src='gifs/standar/opcionazul.png' border='0'>Ver/Editar Cita</td></tr>";
			}
		}
	}
	if($D->estado==9) // reemplazo
	{
		if($D->siniestro_reemplazo) // si hay siniestro de reemplazo
		{
			// obtiene los datos del siniestro
			if(!$Sin=qo("select s.id,s.numero,a.nombre as anombre,a.emblema_f as aemb,s.observaciones,s.obsconclusion,img_inv_salida_f as acta1,img_inv_entrada_f as acta2,t_estado_siniestro(s.estado) as nestsin from siniestro s,aseguradora a where a.id=s.aseguradora  and s.id=$D->siniestro_reemplazo"))
			$Sin=qo("select s.id,s.numero,a.nombre as anombre,a.emblema_f as aemb,s.observaciones,s.obsconclusion,img_inv_salida_f as acta1,img_inv_entrada_f as acta2,t_estado_siniestro(s.estado) as nestsin from siniestro_hst s,aseguradora a where a.id=s.aseguradora  and s.id=$D->siniestro_reemplazo");
			if(!$Sin && $fecha<'2013-01-01') $Sin=qo("select s.id,s.numero,a.nombre as anombre,a.emblema_f as aemb,s.observaciones,s.obsconclusion,img_inv_salida_f as acta1,img_inv_entrada_f as acta2,t_estado_siniestro(s.estado) as nestsin from siniestro_hst_2012 s,aseguradora a where a.id=s.aseguradora  and s.id=$D->siniestro_remplazo");
			if($Sin)	presenta_datos_siniestro($Sin,$Nt1,$D,$V); // pinta los datos del siniestro
			else // si no se encuentra el siniestro da la opcion de asociar un siniestro apropiado
			echo "<tr><td colspan='2' bgcolor='ffeeee'>No tiene Siniestro de reemplazo.</td></tr>
							<tr><td colspan='2'>
							<form action='zcontrol_operativo3Dev.php' method='post' target='Oculto_tablero' name='forma1' id='forma1'>
								Asignar: <input type='text' name='numero' id='numero'><input type='button' value='Asignar' onclick=\"if(this.form.numero.value) {
								this.form.submit();} else {alert('No ha escrito un número de siniestro para asignar');return false;}\">
								<input type='hidden' name='Acc' value='asignar_siniestro_reemplazo'><input type='hidden' name='idub' value='$idub'>
							</form>
							</td></tr>";
		}
		else
		{ // si no hay siniestro de reemplazo tiene la opcion de asociar el siniestro de reemplazo apropiado
			echo "<tr><td colspan='2' bgcolor='ffeeee'>No tiene Siniestro de reemplazo.</td></tr>
						<tr><td colspan='2'>
							<form action='zcontrol_operativo3Dev.php' method='post' target='Oculto_tablero' name='forma1' id='forma1'>
								Asignar: <input type='text' name='numero' id='numero'><input type='button' value='Asignar' onclick=\"if(this.form.numero.value) {
								this.form.submit();} else {alert('No ha escrito un número de siniestro para asignar');return false;}\">
								<input type='hidden' name='Acc' value='asignar_siniestro_reemplazo'><input type='hidden' name='idub' value='$idub'>
							</form>
							</td></tr>";
		}
	}
	if(inlist($USUARIO,'1,2,7')) // permite modificar la ubicacion
		echo "<tr><td onclick='abre_ubicacion();' style='cursor:pointer'><img src='gifs/standar/opcionazul.png' border='0'> Ver / Editar estado</td>";
	if(inlist($USUARIO,'1,2')) // permite borrar la ubicacion
		echo "<td onclick='borrar_ubicacion();' style='cursor:pointer'><img src='gifs/standar/Cancel.png' border='0'> Borrar este estado</td></tr>";

	if(inlist($USUARIO,'1'))
	{ // permite ajustar la fecha inicial y la final de acuerdo a la celda donde se le dio click originalmente
		echo "<tr><td onclick='ajustar_fecha_inicial();' style='cursor:pointer'><img src='gifs/standar/Down.png' border='0'> Ajustar fecha inicial</td>
				<td onclick='ajustar_fecha_final();' style='cursor:pointer'><img src='gifs/standar/Down.png' border='0'> Ajustar fecha final</td></tr>
				<tr><td colspan='2' onclick='tomarid2balance();' style='cursor:pointer'><img src='gifs/standar/Next.png' border='0'> Tomar para asignar balance de estado</td></tr>";
		if($_SESSION['Id_para_balance']) // permite cambiar de balance de estado de otro estado, trae el balance de otro estado a este estado
		{echo "<tr><td colspan='2' onclick='asignarid2balance(".$_SESSION['Id_para_balance'].");' style='cursor:pointer'><img src='gifs/standar/Next.png' border='0'> Asignar balance ".$_SESSION['Id_para_balance']." a este estado</td></tr>";}
	}
	if(inlist($USUARIO,'1,3,13'))  // permite cambiar la fecha de devolucion final del vehiculo
	echo "<tr><td onclick='cambiar_fecha_devolucion();' style=cursor:pointer'><img src='gifs/standar/opcionazul.png' border='0'>Fecha Devolución</td>";
	if(inlist($USUARIO,'1,2,3,7,10,23,27,15')) // permite ingresar al balance de este estado
	echo "<td onclick='balance_estado();' style='cursor:pointer'><img src='gifs/standar/folder1.png' border='0'> Balance de Estado</td>";
	echo "</tr> ";
	// permite adicionar observaciones a cualquier usuario
	echo "<tr><td onclick='adicionar_observaciones();' style='cursor:pointer'><img src='gifs/standar/edita_registro.png' border='0'> Adicionar Observaciones</td></tr>";
	//  ************************* ADJUDICACION DESDE CALL CENTER ***************************************************************************************
	if(inlist($USUARIO,'1,4,26') && $_SESSION['Adjudicacion_SINIESTRO']) // si se está adjudicando desde call center
	{
		if(inlist($Ultimo->estado,'1,2,8'))
		{
			if(($D->estado==2 && $D->id==$Ultimo->id) || ($D->estado==1 && $D->fecha_final==$fecha && $D->id==$Ultimo->id)
			|| ($D->estado==8 && $D->id==$Ultimo->id))	// verifica el ultimo estado y las fechas para poder pintar el boton de adjudicación para call center
			echo "<tr><td onclick='menu_adjudicar();' style='cursor:pointer'><img src='img/adjudicacion1.png' height='20px'> Adjudicar</td></tr>";
		}
	}
	//******************************************************************************************************************************************************************
	echo "</table>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20; // ajusta el tamaño de la capa donde se pinta el menu contextual
			var Altura=document.getElementById('Context_Celda').clientHeight; // ajusta la altura del menu contextual
			if(Altura>500) Altura=500; // limita la altura a 500 pixeles
			parent.document.getElementById('Menu_contextual_celda').style.height=Altura+40; // aumenta la altura a 40 pixeles
			parent.mc_evento_texto(); // ubica el menu contextual cerca al cursor
			parent.Objeto.style.cursor='auto';
			parent.Cargado=true;
		</script></body></html>";
}









function presenta_datos_siniestro($Sin,$Nt1,$D,$V) // presenta los datos de un siniestro
{
	global $USUARIO;
	echo "<tr><td colspan=2  nowrap='yes'><a ".($Nt1?"onclick='abre_siniestro($Sin->id);' ":"onclick=\"alert('No tiene acceso a la tabla de siniestros');\"").
			" style='cursor:pointer'><img src='gifs/standar/opcionazul.png' border='0'>Ver / Editar <b style='font-size:16px'>$Sin->numero</b><b> ".
			($D->flota==$V->flota?"<font color='blue'>$Sin->anombre</font>":"")."</b></a> $Sin->nestsin".
			($Sin->acta1?" &nbsp;&nbsp;<a onclick=\"modal('$Sin->acta1',0,0,800,800,'ace');\" style='cursor:pointer' class='info'><img src='gifs/standar/si.png'><span>Acta Entrega</span></a>":"").
			($Sin->acta2?" &nbsp;&nbsp;<a onclick=\"modal('$Sin->acta2',0,0,800,800,'acd');\" style='cursor:pointer' class='info'><img src='gifs/standar/si.png'><span>Acta Devolución</span></a>":"").
			"</td></tr>";
	// permite ver las autorizaciones solamente al super usuario.
	if(inlist($USUARIO,'1')) echo "<tr><td onclick='ver_autorizaciones($Sin->id);' style='cursor:pointer'><img src='gifs/standar/opcionazul.png' border='0'> Ver Autorizaciones</td></td><td align='center'>$Sin->numero</td></tr>";
	if($FACS=q("select id,consecutivo,anulada from factura where siniestro=$Sin->id")) // busca si hay facturas asociadas al siniestro
	{
		while($FAC=mysql_fetch_object($FACS)) // pinta las facturas asociadas
		{
			$Anulada=$FAC->anulada?"<b>Anulada</b>":""; // verifica si la factura está anulada
			echo "<tr><td colspan=2 style='cursor:pointer' onclick=\"modal('zfacturacion.php?Acc=imprimir_factura&id=$FAC->id',0,0,700,900,'eds');\" nowrap='yes'>
						<img src='gifs/standar/opcionazul.png' border='0'>Ver Factura $FAC->consecutivo $Anulada</td></tr>"; // pinta la factura y permite visualizarla en pantalla
			if($RC=qo("select * from recibo_caja where factura=$FAC->id")) // busca si hay recibos de caja para la factura
			{
				echo "<tr><td colspan=2 style='cursor:pointer' onclick=\"modal('zcartera.php?Acc=imprimir_recibo&id=$RC->id',0,0,700,900,'eds');\" nowrap='yes'>
							<img src='gifs/standar/opcionazul.png' border='0'>Ver Recibo de caja $RC->consecutivo</td></tr>"; // pinta el recibo de caja y permite visualizarlo en pantalla
				if($RC->consignacion_numero && $RC->consignacion_f) // busca si hay consignaciones del recibo de caja
				{
					echo "<tr><td colspan=2 style='cursor:pointer' onclick=\"modal('$RC->consignacion_f',0,0,700,900,'eds');\" nowrap='yes'>
								<img src='gifs/standar/opcionazul.png' border='0'>Ver Consignación $RC->consignacion_numero $RC->consignacion_fecha</td></tr>";
								// pinta la consignación con la opción de visualizarla
				}
			}
		}
	}
	if(inlist($USUARIO,'1,2,7'))  // MUESTRA DESLIGUE DE SINIESTRO
		echo "<tr><td colspan=2 style='cursor:pointer' onclick='desliga_siniestro($Sin->id);' nowrap='yes'><img src='gifs/standar/Cancel.png' border='0'>Desligar el siniestro de este estado</td></tr>"; // permite desligar el siniestro del estado actual
	echo "<tr><td colspan=2 bgcolor='eeeeee'><b>Observaciones:</b> ".nl2br($Sin->observaciones)."</td></tr><tr><td colspan=2 bgcolor='eeeeee'><b>Obs. Conclusión:</b> ".nl2br($Sin->obsconclusion)."</td></tr>"; // muestra las observaciones de la conclusión del servicio
}

function borrar_ubicacion() // borra la ubicación
{
	global $id;
	q("delete from ubicacion where id=$id");
	graba_bitacora('ubicacion','D',$id,'Borra registro');
	echo "<body><script language='javascript'>
			parent.oculta_menu_celda();
	</script></body>";
}

function ajustar_fecha_inicial() // ajusta la fecha inicial del estado de acuerdo a la celda donde se dio click originalmente
{
	global $id,$fecha;
	q("update ubicacion set fecha_inicial='$fecha' where id=$id");
	graba_bitacora('ubicacion','M',$id,"Ajusta fecha inicial a $fecha");
	echo "<body><script language='javascript'>
		parent.oculta_menu_celda();
	</script></body>";
}

function ajustar_fecha_final() // ajusta la fecha final del estado de acuerdo a la celda donde se dio click originalmente
{
	global $id,$fecha;
	q("update ubicacion set fecha_final='$fecha' where id=$id");
	graba_bitacora('ubicacion','M',$id,"Ajusta fecha final a $fecha");
	echo "<body><script language='javascript'>
		//alert('Cambio realizado');
		parent.oculta_menu_celda();
	</script></body>";
}

function adicionar_evento() // formulario para adicionar una nueva ubicación
{
	global $vehiculo,$fecha,$Nomueve,$USUARIO;
	
	$V=qo("select placa,flota_distinta from vehiculo where id=$vehiculo"); // busca la información del vehiculo
	$fini=date('Y-m-d');$ffin=date('Y-m-d');$oficina=0;$flota=0;$kmi=0;$kmf=0;
	$Fecha_Posterior='3000-01-01';
	if($Ultimo=qo("select * from ubicacion where vehiculo=$vehiculo and fecha_final<='$fecha' order by fecha_final desc,id desc limit 1")) // Busca la ultima ubicacion anterior a la fecha donde se desea adicionar el estado
	{
		$fini=$Ultimo->fecha_final;if($fini>$ffin) $ffin=$fini;$oficina=$Ultimo->oficina;$flota=$Ultimo->flota;$kmi=$Ultimo->odometro_final;$kmf=$Ultimo->odometro_final;
	}
	if($Posterior=qo("select * from ubicacion where vehiculo=$vehiculo and fecha_inicial > '$fecha' order by fecha_inicial asc,id asc limit 1")) // busca la primera ubicación posterior a la fecha donde se desea adicionar el estado
	{
		$ffin=$Posterior->fecha_inicial;$kmf=$Posterior->odometro_inicial;
		$Fecha_Posterior=$ffin;
	}
	if($fecha>date('Y-m-d'))
	{
		$fini=$fecha;$ffin=$fecha;
	}
	$Diferencia=$kmf-$kmi;
	if($V->flota_distinta==0)  // si no hay flota, le asigna la flota de AOA
	{$flota=6;}
	html(); // pinta las cabeceras html
	// pinta las herramientas javascript
	echo "
		<script language='javascript'>
			function validar_nuevo_estado()
			{
				with(document.forma2)
				{
					if(FF.value<FI.value) {alert('La fecha final no puede ser menor que la fecha inicial'); return false;}
					if(FF.value>'$Fecha_Posterior') {alert('La fecha final no puede ser mayor que $Fecha_Posterior');return false;}
					if(OFI.value==0) {alert('Debe seleccionar una oficina válida'); return false;}
					if(FLOT.value==0) {alert('Debe seleccionar una flota de vehículos válida'); return false;}
					if(Number(KMI.value)==0) {alert('Debe escribir un kilometraje inicial válido'); return false;}
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMI.value)<$kmi) {alert('Debe escribir un kilometraje mayor o igual a $kmi'); return false;}
					";
				if($Posterior)
				{
					echo "if(Number(KMF.value)>$kmf) {alert('Debe escribir un kilometraje menor o igual a $kmf'); return false;}";
				}

				echo "if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(Estado.value==0) {alert('Debe seleccionar un estado válido'); return false;}
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{	Diferencia.value=Number(KMF.value)-Number(KMI.value);	}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Adicion de un evento</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td></tr><tr><td width='10%'>Fecha final:</td><td>".pinta_FC('forma2','FF',$ffin)."</td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>";
	if(inlist($USUARIO,'1')) // pinta un select de oficinas
		echo menu1('OFI',"select id,nombre from oficina where activa=1",$oficina,1);
	else
		
		echo qo1("select t_oficina($oficina)")."<input type='hidden' name='OFI' id='OFI' value='$oficina'>"; // trae una oficina especifica y proteje el campo

	echo "</td>

	<td align='right'>Flota:</td><td>";
	if(inlist($USUARIO,'1,10,27')) // trae un select de flotas
		echo menu1('FLOT',"select id,nombre from aseguradora",$flota,1);
	else
	echo qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'>";  // trae una flota específica y proteje el campo
	echo "</td></tr><tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>";
	if(inlist($USUARIO,'1')) // trae un slelect de estados
		echo menu1("Estado","Select id,nombre from estado_vehiculo where activacion = 1 ",2,1);
	else
	{
		if($Ultimo->estado==1 )
		{ // si el estado es servicio, solo muestra mantenimiento programado
			echo menu1("Estado","Select id,nombre from estado_vehiculo where id in (92) and activacion = 1  ",2,1);
		}
		else

		{
			if($USUARIO==10) // DIRECTOR DE CIUDAD: parqueadero,en mantenimiento,fuera de servicio,alistamiento,traslado parq.domicilio,garantia, reemplazo
				echo menu1("Estado","Select id,nombre from estado_vehiculo where id in (2,4,5,8,94,96,98,9,94,106,107,108) and activacion = 1 ",2,1);
			else // todos los demas: parqueadero, mantenimiento, en transito, alistamiento, mantenimiento programado, parqueadero gerencia, traslado entre parqueaderos, domicilio, garantia, reemplazo
				echo menu1("Estado","Select id,nombre from estado_vehiculo where id in (2,4,5,6,8,92,93,94,96,98,9,94,106,107,108) and activacion = 1 ",2,1);
		}
	}
	echo "</td><td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_nuevo_estado()'>
				<input type='hidden' name='Acc' id='Acc' value='adiciona_ubicacion_ok'><input type='hidden' name='Vehiculo' id='Vehiculo' value='$vehiculo'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}



function adicionar_estado() // formulario para adicionar una nueva ubicación
{
	global $vehiculo,$fecha,$Nomueve,$USUARIO;
	
	$V=qo("select placa,flota_distinta from vehiculo where id=$vehiculo"); // busca la información del vehiculo
	$fini=date('Y-m-d');$ffin=date('Y-m-d');$oficina=0;$flota=0;$kmi=0;$kmf=0;
	$Fecha_Posterior='3000-01-01';
	if($Ultimo=qo("select * from ubicacion where vehiculo=$vehiculo and fecha_final<='$fecha' order by fecha_final desc,id desc limit 1")) // Busca la ultima ubicacion anterior a la fecha donde se desea adicionar el estado
	{
		$fini=$Ultimo->fecha_final;if($fini>$ffin) $ffin=$fini;$oficina=$Ultimo->oficina;$flota=$Ultimo->flota;$kmi=$Ultimo->odometro_final;$kmf=$Ultimo->odometro_final;
	}
	if($Posterior=qo("select * from ubicacion where vehiculo=$vehiculo and fecha_inicial > '$fecha' order by fecha_inicial asc,id asc limit 1")) // busca la primera ubicación posterior a la fecha donde se desea adicionar el estado
	{
		$ffin=$Posterior->fecha_inicial;$kmf=$Posterior->odometro_inicial;
		$Fecha_Posterior=$ffin;
	}
	if($fecha>date('Y-m-d'))
	{
		$fini=$fecha;$ffin=$fecha;
	}
	$Diferencia=$kmf-$kmi;
	if($V->flota_distinta==0)  // si no hay flota, le asigna la flota de AOA
	{$flota=6;}
	html(); // pinta las cabeceras html
	// pinta las herramientas javascript
	echo "
		<script language='javascript'>
			function validar_nuevo_estado()
			{
				with(document.forma2)
				{
					if(FF.value<FI.value) {alert('La fecha final no puede ser menor que la fecha inicial'); return false;}
					if(FF.value>'$Fecha_Posterior') {alert('La fecha final no puede ser mayor que $Fecha_Posterior');return false;}
					if(OFI.value==0) {alert('Debe seleccionar una oficina válida'); return false;}
					if(FLOT.value==0) {alert('Debe seleccionar una flota de vehículos válida'); return false;}
					if(Number(KMI.value)==0) {alert('Debe escribir un kilometraje inicial válido'); return false;}
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMI.value)<$kmi) {alert('Debe escribir un kilometraje mayor o igual a $kmi'); return false;}
					";
				if($Posterior)
				{
					echo "if(Number(KMF.value)>$kmf) {alert('Debe escribir un kilometraje menor o igual a $kmf'); return false;}";
				}

				echo "if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(Estado.value==0) {alert('Debe seleccionar un estado válido'); return false;}
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{	Diferencia.value=Number(KMF.value)-Number(KMI.value);	}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Adicion de un estado</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td></tr><tr><td width='10%'>Fecha final:</td><td>".pinta_FC('forma2','FF',$ffin)."</td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>";
	if(inlist($USUARIO,'1')) // pinta un select de oficinas
		echo menu1('OFI',"select id,nombre from oficina where activa=1",$oficina,1);
	else
		
		echo qo1("select t_oficina($oficina)")."<input type='hidden' name='OFI' id='OFI' value='$oficina'>"; // trae una oficina especifica y proteje el campo

	echo "</td>

	<td align='right'>Flota:</td><td>";
	if(inlist($USUARIO,'1,10,27')) // trae un select de flotas
		echo menu1('FLOT',"select id,nombre from aseguradora",$flota,1);
	else
	echo qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'>";  // trae una flota específica y proteje el campo
	echo "</td></tr><tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>";
	if(inlist($USUARIO,'1')) // trae un slelect de estados
		echo menu1("Estado","Select id,nombre from estado_vehiculo where activacion = 1 ",2,1);
	else
	{
		if($Ultimo->estado==1 )
		{ // si el estado es servicio, solo muestra mantenimiento programado
			echo menu1("Estado","Select id,nombre from estado_vehiculo where id in (92) and activacion = 1  ",2,1);
		}
		else

		{
			if($USUARIO==10) // DIRECTOR DE CIUDAD: parqueadero,en mantenimiento,fuera de servicio,alistamiento,traslado parq.domicilio,garantia, reemplazo
				echo menu1("Estado","Select id,nombre from estado_vehiculo where id in (2,4,5,8,94,96,98,9,94,106,107,108) and activacion = 1 ",2,1);
			else // todos los demas: parqueadero, mantenimiento, en transito, alistamiento, mantenimiento programado, parqueadero gerencia, traslado entre parqueaderos, domicilio, garantia, reemplazo
				echo menu1("Estado","Select id,nombre from estado_vehiculo where id in (2,4,5,6,8,92,93,94,96,98,9,94,106,107,108) and activacion = 1 ",2,1);
		}
	}
	echo "</td><td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_nuevo_estado()'>
				<input type='hidden' name='Acc' id='Acc' value='adiciona_ubicacion_ok'><input type='hidden' name='Vehiculo' id='Vehiculo' value='$vehiculo'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}


function adiciona_ubicacion_ok() // funcion para adicionar un estado al vehiculo recibe los datos del formulario
{
	global $FI,$FF,$OFI,$FLOT,$KMI,$KMF,$Estado,$OBS,$Vehiculo,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$dif=$KMF-$KMI;
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$OFI','$Vehiculo','$Estado','$FLOT','$FI','$FF','$KMI','$KMF','$dif',\"$NUSUARIO [$Hoy] $OBS\") "); // adiciona el registro
	graba_bitacora('ubicacion','A',$IDU,'Adiciona Registro'); // graba la bitacora del registro
	echo "<body><script language='javascript'>parent.oculta_menu_celda();</script></body>";
}

function cerrar_mantenimiento() // funcion para cerrar un estado de mantenimiento y dejar el vehiculo en parqueadero
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos de la ubicacion
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html
	// pinta las funciones js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(Number(KMF.value)==Number(KMI.value)) {alert('El cierre de Mantenimiento exige que se registre un kilometraje final que no puede ser igual al inicial');return false;}
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Mantenimiento</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cierre_mantenimiento_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cierre_mantenimiento_ok() // cierre del mantenimiento y adicion del registro de parqueadero automático
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub"); // actualiza la ubicación con los odómetros y las observaciones
	$UB=qo("select * from ubicacion where id=$idub"); //  vuelve a cargar la ubicacion
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') "); // adiciona el estado de parqueadero
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro"); // graba la bitacora del parqueadero
	echo "<script language='javascript'>
		function carga()
		{
			parent.parent.document.forma.submit();
		}
	</script>
	<body onload='carga()'></body>";
}

function cerrar_servicio_especial() // cierra un estado donde la gerencia utiliza el vehiculo pero se ve como un parqueadero en el tablero de control
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos de la ubicacion
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehículo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html
	// pinta las herramientas js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto al cierre del servicio especial'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Servicio Especial Gerencia</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_servicio_especial_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cerrar_servicio_especial_ok() // cierra el servicio especial y crea un estado de parqueadero automático
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub"); // actualiza el odometro y las observaciones del estado actual
	$UB=qo("select * from ubicacion where id=$idub"); // recarga la ubicacion
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') "); // inserta el registro de parqueadero
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro"); // graba la bitacora del registro de parqueadero
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}

function cerrar_fuera_servicio() // funcion que sirve para cerrar un estado de fuera de servicio del vehiculo
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos de la ubicacion
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html, las herramientas js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Fuera de Servicio</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_fuera_servicio_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cerrar_fuera_servicio_ok() // graba el cierre de fuera de servicio y adiciona un estado de parqueadero automatico
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub"); // actualiza la ubicacion
	$UB=qo("select * from ubicacion where id=$idub"); // recarga la ubicacion
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') "); // inserta el estado de parqueadero automático
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro"); // graba la bitacora del parqueadero
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}

function cerrar_fuera_operacion() // Cierra un fuera de operación y adiciona un estado de parqueadero automáticamente
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos de la ubicación
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html, las herramientas js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Fuera de Operación</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_fuera_operacion_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cerrar_fuera_operacion_ok() // graba el cierre de fuera de operación y crea el estado de parqeuadero automáticamente
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub"); // actualiza el estado
	$UB=qo("select * from ubicacion where id=$idub"); // recarga los datos de la ubicacion
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') "); // inserta el estado de parqueadero automático
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro"); // graba la bitacora del parqueadero
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}

function cerrar_alistamiento() // cierra un estado de alistamiento y crea un estado de parqueadero automático
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos de ubicación
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html, las herramientas js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Alistamiento</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_alistamiento_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}


function cerrar_domicilio() // cierra un estado de alistamiento y crea un estado de parqueadero automático
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos de ubicación
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html, las herramientas js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Domicilio</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_domicilio_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}



function cerrar_domicilio_ok() // graba el registro de cierre de alistamiento y crea el parqueadero automatico
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub"); // actualiza el registro
	$UB=qo("select * from ubicacion where id=$idub"); // recarga los datos de la ubicacion
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','8','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') "); // crea el estado de parqueadero
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro"); // graba la bitacora del parqueadero
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}



function cerrar_siniestro_pp() // cierra un estado de alistamiento y crea un estado de parqueadero automático
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos de ubicación
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html, las herramientas js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Siniestro Perdida Parcial</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_siniestro_pp_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}



function cerrar_siniestro_pp_ok() // graba el registro de cierre de alistamiento y crea el parqueadero automatico
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub"); // actualiza el registro
	$UB=qo("select * from ubicacion where id=$idub"); // recarga los datos de la ubicacion
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') "); // crea el estado de parqueadero
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro"); // graba la bitacora del parqueadero
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}


function cerrar_siniestro_pt() // cierra un estado de alistamiento y crea un estado de parqueadero automático
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos de ubicación
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html, las herramientas js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Siniestro Perdida Total</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_siniestro_pt_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cerrar_siniestro_pt_ok() // graba el registro de cierre de alistamiento y crea el parqueadero automatico
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub"); // actualiza el registro
	$UB=qo("select * from ubicacion where id=$idub"); // recarga los datos de la ubicacion
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') "); // crea el estado de parqueadero
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro"); // graba la bitacora del parqueadero
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}


function cerrar_reparacion() // cierra un estado de alistamiento y crea un estado de parqueadero automático
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos de ubicación
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html, las herramientas js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre Reparación </h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_reparacion_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cerrar_reparacion_ok() // graba el registro de cierre de alistamiento y crea el parqueadero automatico
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub"); // actualiza el registro
	$UB=qo("select * from ubicacion where id=$idub"); // recarga los datos de la ubicacion
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') "); // crea el estado de parqueadero
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro"); // graba la bitacora del parqueadero
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}


function cerrar_entre_parque() // cierra un estado de alistamiento y crea un estado de parqueadero automático
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos de ubicación
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html, las herramientas js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre Traslado Entre Parqueadero </h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_entre_parque_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cerrar_entre_parque_ok() // graba el registro de cierre de alistamiento y crea el parqueadero automatico
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub"); // actualiza el registro
	$UB=qo("select * from ubicacion where id=$idub"); // recarga los datos de la ubicacion
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') "); // crea el estado de parqueadero
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro"); // graba la bitacora del parqueadero
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}




function cerrar_alistamiento_ok() // graba el registro de cierre de alistamiento y crea el parqueadero automatico
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub"); // actualiza el registro
	$UB=qo("select * from ubicacion where id=$idub"); // recarga los datos de la ubicacion
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') "); // crea el estado de parqueadero
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro"); // graba la bitacora del parqueadero
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}

function cerrar_transito()  // cierra el estado de transito y crea un estado de parqueadero automático
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos del estado
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html, las herramientas js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Cierre de Transito</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Oficina Destino:</td><td>".menu1("OFID","select id,nombre from oficina where activa=1")."</td>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cerrar_transito_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cerrar_transito_ok() // graba el cierre del estado de transito y crea el estado de parqueadero
{
	global $idub,$KMF,$OBS,$NUSUARIO,$OFID;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub"); // actualiza el registro
	$UB=qo("select * from ubicacion where id=$idub"); // recarga los datos del estado
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$OFID','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') "); // crea el estado de parqueadero
	q("update vehiculo set ultima_ubicacion=$UB->oficina where id=$UB->vehiculo"); // actualiza la ultima ubicación del vehiculo
	graba_bitacora('ubicacion','A',$IDU,"Aiciona Registro"); // graba la bitacora del parqueadero
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}

function activar_mantenimiento() // cierra un estado de mantenimiento programado y crea un estado de mantenimiento preventivo
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos del estado actual
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html, las herramientas js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=2><h3>Activación de Mantenimiento</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".menu1("EST","select id,nombre from estado_vehiculo where id=4",4,0)."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3>$UB->obs_mantenimiento</td></tr>
				<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='activar_mantenimiento_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function activar_mantenimiento_ok() // graba el cierre del registro de mantenimiento programado y crea el estado de mantenimiento preventivo
{
	global $idub,$EST,$OBS,$KMI,$KMF,$Diferencia,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set fecha_final='$Fecha' where id='$idub' "); // actualiza el estado actual
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos del estado
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','4','$UB->flota','$Fecha','$Fecha','$KMI','$KMF','$Diferencia',\"$UB->obs_mantenimiento \n$NUSUARIO [$Hoy] $OBS\") "); // crea el estado de mantenimiento
	graba_bitacora('ubicacion','A',$IDU,'Adiciona Registro'); // graba la bitacora del mantenimiento
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}

function adicionar_observaciones() // adiciona observaciones a cualquier estado
{
	global $id,$Emb_Aseg;
	$U=qo("select * from ubicacion where id=$id"); // trae los datos del estado
	$UT=qo("select * from ubicacion where id=$id"); // carga los mismos datos en una variable aparte
	$V=qo("select * from vehiculo where id=$U->vehiculo"); // trae los datos del vehiculo
	$O=qo("select * from oficina where id=$U->oficina"); // trae los datos de la oficina
	$Nplaca=substr($V->placa,0,3).'-'.substr($V->placa,3,3); // enmascara la placa
	html(); // pinta cabecera html
	echo "
		<script language='javascript'>
			function valida_nuevas_observaciones()
			{
				if(!alltrim(document.forma3.Obs.value))
				{ alert('Debe escribir algo en el campo de observaciones para ser grabado'); return false;}
				document.forma3.submit();
			}
		</script>
	<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma3' id='forma3'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='300px'><tr><td>
			<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
			<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();'>
			<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b></td>
			<td align='center' valign='middle' rowspan=3 bgcolor='eeeeee'><img src='".$Emb_Aseg[$U->flota]."' border='0' align='middle' height='30px'> <img src='$V->emb1' border='0' align='middle' height='30px'></td></tr>
			<tr><td align='center' colspan='1' bgcolor='eeeeee' class='placa'  style='background-image:url(img/placa.jpg);'>$Nplaca</td></tr>
			<tr><td nowrap='yes' bgcolor='eeeeee'>Estado: <b>$UT->nestado</b></td></tr>
			<tr><td nowrap='yes' bgcolor='eeeeee' colspan=2>Fechas Evento: <b>$UT->fecha_inicial - $UT->fecha_final</b></td></tr>";
	if($U->obs_mantenimiento || $U->observaciones) // pinta las anteriores observaciones
		echo "<tr><td colspan=2><b>Observaciones anteriores:</b> ".nl2br($U->observaciones).'<br />'.nl2br($U->obs_mantenimiento)."</td></tr>";
	echo "
			<tr><td colspan=2>Observaciones nuevas:<br /><textarea name='Obs' style='font-size:14' cols=50 rows=3></textarea></td></tr>
			<tr><td colspan=2 align='center'><input type='button' value='Grabar' onclick='valida_nuevas_observaciones()'></td></tr>
		</table>
		</TD></TR></TABLE>
		<input type='hidden' name='Acc' id='Acc' value='adicionar_observaciones_ok'><input type='hidden' name='id' id='id' value='$id'>
		</form>
	<script language='javascript'>
		parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
		parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
		parent.mc_evento_texto(1);
		document.forma3.Obs.focus();
	</script></body></html>";
}

function adicionar_observaciones_ok() // graba las observaciones
{
	global $id,$Obs,$NUSUARIO;
	$Hoy=date('Y-m-d H:i');
	q("update ubicacion set obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $Obs\") where id=$id"); // actualiza el registro del estado
	if($Sin=qo1("select id from siniestro where ubicacion=$id")) // busca si hay un siniestro ligado al estado
	{
		q("update siniestro set obsconclusion=concat(obsconclusion,\"\n$NUSUARIO [$Hoy] $Obs\") where id=$Sin"); // actualiza el siniestro en caso de que este ligado al estado
		if($Cita=qo1("select id from cita_servicio where siniestro=$Sin")) // busca si hay una cita asociada al siniestro
		{
			q("update cita_servicio set obs_devolucion=concat(obs_devolucion,\"\n$NUSUARIO [$Hoy] $Obs\") where id=$Cita"); // actualiza las observaciones de la cita
		}
	}
	graba_bitacora('ubicacion','M',$id,'Modifica Registro: Observaciones'); // graba la bitacroa del estado
	echo "<body><script language='javascript'>parent.oculta_menu_celda();</script></body>";
}

function desligar_siniestro() // borra la ubicacion de un siniestro asociado
{
	global $ids;
	q("update siniestro set ubicacion=0 where id=$ids"); // actualiza el registro en la tabla de siniestros
	graba_bitacora('siniestro','M',$ids,"Desliga Siniestro"); // graba la bitacora del siniestro
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}

function asignar_siniestro() // asigna a un siniestro la ubicación del estado para ligarlos
{
	global $numero,$idub;
	if(!$Sin=q("select id from siniestro where numero like '%$numero%'  ")) $Sin=q("select id from siniestro_hst where numero like '%$numero%'  "); // busca el siniestro
	if($Sin)
		echo "<body><script language='javascript'>window.open('zcontrol_operativo3Dev.php?Acc=asignar_siniestro_selecciona&numero=$numero&idub=$idub','Menu_contextual_celda');</script></body>"; // envia a una rutina de seleccion si es mas de un siniestro encontrado en el filtro
	else
		echo "<body><script language='javascript'>parent.oculta_menu_celda();alert('No se encuentra un siniestro con el numero dado');</script></body>"; // cuando no encuentra el siniestro
}

function asignar_siniestro_selecciona() // seleccion de varios siniestros en una asignación de ubicación
{
	global $numero,$idub;
	html(); // pinta cabecera html
	echo "<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0' bgcolor='eeffee'>
			<table border='0' cellspacing='1' cellpadding='0' bgcolor='#bbbbbb' name='Context_Celda' id='Context_Celda' align='center' width='400px'>
			<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();'><img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar menu</b></td>
			<tr><td bgcolor='eeffee'>
			Por favor seleccione uno de los siguientes siniestros para asignar:<br />
			<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma' id='forma'>".
			menu1("SIN","select id,concat(numero,' ',fec_autorizacion,' ',asegurado_nombre,' [',t_estado_siniestro(estado),']') from siniestro where numero like '%$numero%' order by numero");
	echo "	Estado: ".menu1("Nestado","select id,nombre from estado_siniestro",8)."
			<input type='hidden' name='Acc' id='Acc' value='asignar_siniestro_seleccionado'>
			<input type='hidden' name='idub' id='idub' value='$idub'>
			<input type='submit' value='Asignar' >
			</form>
			</td></tr></table>
			<script language='javascript'>
				parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
				parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
				parent.mc_evento_texto();
			</script></body>";
}

function asignar_siniestro_seleccionado() // asigna el siniestro seleccionado de una lista de varios siniestros a una ubicación
{
	global $SIN,$idub,$Nestado;
	$D=qo("select * from ubicacion where id=$idub"); // trae los datos de la ubicacion
	q("update siniestro set ubicacion=$idub,fecha_inicial='$D->fecha_inicial', fecha_final='$D->fecha_final',estado='$Nestado' where id=$SIN"); // actualiza el siniestro
	graba_bitacora('siniestro','M',$SIN,"Asigna siniestro $SIN a la ubicación $idub $D->fecha_inicial - $D->fecha_final"); // graba la bitacora del siniestro
	echo "<body><script language='javascript'>alert('Asignación Satisfactoria');parent.oculta_menu_celda();</script></body>";
}

function asignar_siniestro_reemplazo() // asigna un siniestro de reemplazo a una ubicación cuando dos vehiculos son prestados para un mismo siniestro por daño del primer vehiculo
{
	global $numero,$idub;
	$Hst=0;
	if(!$Sin=qo1("select id from siniestro where numero like '%$numero%'  ")) {$Sin=qo1("select id from siniestro_hst where numero like '%$numero%'  ");$Hst=1;} // busca el siniestro
	if($Sin)
		echo "<body><script language='javascript'>window.open('zcontrol_operativo3Dev.php?Acc=asignar_siniestro_reemplazo_selecciona&numero=$numero&idub=$idub&Hst=$Hst&Sin=$Sin','Menu_contextual_celda');</script></body>"; // si aparecen mas de un siniestro, se muestra un menu de seleccion para la asignacion
	else
		echo "<body><script language='javascript'>parent.oculta_menu_celda();alert('No se encuentra un siniestro con el numero dado');</script></body>"; // cuando no encuentra el siniestro
}

function asignar_siniestro_reemplazo_selecciona() // seleccion de varios siniestros al estado de reemplazo
{
	global $numero,$idub,$Hst,$Sin;
	html(); // pinta la cabecera html
	echo "<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0' bgcolor='eeffee'>
			<h3>Reemplazo de Vehiculo en servicio $Hst $Sin</h3>
			<table border='0' cellspacing='1' cellpadding='0' bgcolor='#bbbbbb' name='Context_Celda' id='Context_Celda' align='center' width='400px'>
			<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();'><img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar menu</b></td>
			<tr><td bgcolor='eeffee'>
			Por favor seleccione uno de los siguientes siniestros para asignar:<br />
			<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma' id='forma'>";
	if($Hst) echo menu1("SIN","select id,concat(numero,' ',fec_autorizacion,' ',asegurado_nombre,' [',t_estado_siniestro(estado),']') from siniestro_hst where numero like '%$numero%' order by numero"); // pinta opciones del historico de siniestros
	else echo menu1("SIN","select id,concat(numero,' ',fec_autorizacion,' ',asegurado_nombre,' [',t_estado_siniestro(estado),']') from siniestro where numero like '%$numero%' order by numero"); // pinta opciones de la tabla de siniestros actual
	echo "	Estado: ".menu1("Nestado","select id,nombre from estado_siniestro order by nombre asc",8)."
			<input type='hidden' name='Acc' id='Acc' value='asignar_siniestro_reemplazo_seleccionado'>
			<input type='hidden' name='idub' id='idub' value='$idub'>
			<input type='submit' value='Asignar' >
			</form>
			</td></tr></table>
			<script language='javascript'>
				parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
				parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
				parent.mc_evento_texto();
			</script></body>";
}

function asignar_siniestro_reemplazo_seleccionado() // asignación del siniestro a la ubicación o estado de reemplazo
{
	global $SIN,$idub,$Nestado;
	q("update ubicacion set siniestro_reemplazo='$SIN' where id='$idub' "); // actualiza la ubicación, no el siniestro
	$D=qo("select * from ubicacion where id=$idub"); // recarga los datos de la ubicacion
	graba_bitacora('siniestro','M',$SIN,"Asigna siniestro en reemplazo $SIN a la ubicación $idub $D->fecha_inicial - $D->fecha_final"); // graba la bitacora al siniestro
	graba_bitacora('ubicacion','M',$idub,"Asigna siniestro en reemplazo $SIN"); // graba la bitacora de la ubicación
	echo "<body><script language='javascript'>alert('Asignación Satisfactoria');parent.oculta_menu_celda();</script></body>";
}

function historia_fotografica() // muestra las fotos de un vehiculo en el tiempo de adelante hacia atras servicio por servicio
{
	global $idPlaca,$pagina;
	$V=qo("Select * from vehiculo where id=$idPlaca"); // trae los datos del vehiculo
	if(!$pagina) $pagina=1;

	html("HISTORIA PLACA $V->placa"); // pinta la cabecera html
	// BUSCA LA HISTORIA FOTOGRAFICA DEL VEHICULO EXCLUYENDO LA FLOTA SIN LOGO.
	$Tmp='tmpi_hf_'.$_SESSION['User'].'_'.$_SESSION['Id_alterno']; // crea una variable para tabla temporal
	q("drop table if exists $Tmp");  // elimina la tabla temporal para volver a crearla
	q("create table $Tmp select * from ubicacion where vehiculo='$idPlaca' and estado in (1,7) "); // crea la tabla temporal
	if($U=qo("select u.*,t_estado_vehiculo(u.estado) as nestado,t_aseguradora(u.flota) as nflota,t_oficina(u.oficina) as noficina
		FROM	$Tmp u where vehiculo='$idPlaca' and estado in (1,7)
		 order by u.odometro_inicial desc, u.fecha_inicial desc limit ".($pagina-1).",1")) // busca las ubicaciones ligadas a siniestros
	{
		echo "<body ><script language='javascript'>centrar();</script>
		<i style='font-size:14'>La historia fotográfica de los vehiculos empieza en marzo de 2010. Los siniestros que correspondan a fechas anteriores, no tienen el registro fotográfico. <br><br>
		Las imágenes de los vehículos se muestran siniestro por siniestro empezando por el mas reciente hacia atras. Para ver cada siniestro, se da click en <b>SINIESTRO ANTERIOR</b></I><BR><BR>
		<h3><b>HISTORIA FOTOGRAFICA DEL VEHICULO $V->placa</b></h3>
		<a href='zcontrol_operativo3Dev.php?Acc=historia_fotografica&idPlaca=$idPlaca&pagina=".($pagina+1)."' target='_self'>SINIESTRO ANTERIOR</a> &nbsp;&nbsp;&nbsp;&nbsp;";
		if($pagina>1) echo "<a href='zcontrol_operativo3Dev.php?Acc=historia_fotografica&idPlaca=$idPlaca&pagina=".($pagina-1)."' target='_self'>SINIESTRO SIGUIENTE</a>";
		if($Sin=qo("select * from siniestro where ubicacion=$U->id")) // busca el primer siniestro según la ubicación
		{
			$Aseguradora=qo1("select t_aseguradora($Sin->aseguradora)"); // trae la información de la seguradora
			echo "<h3><i>Oficina:</i> $U->noficina <i>Estado:</i> $U->nestado <i>Aseguradora:</i> $Aseguradora <i>Siniestro:</i> $Sin->numero
							<i>Fecha:</i> $U->fecha_inicial - $U->fecha_final
							<i>Odometros:</i> $U->odometro_inicial - $U->odometro_final</h3>
							<font color='GREEN' style='font-size:26;font-weight:bold'>IMAGENES DE ENTREGA</FONT><BR>";
			// pinta todas las imágenes de la salida del servicio
			echo ($Sin->img_inv_salida_f?"<img src='$Sin->img_inv_salida_f'><br /><br />":"");
			echo ($Sin->fotovh1_f?"<img src='$Sin->fotovh1_f'><br /><br />":"");
			echo ($Sin->fotovh2_f?"<img src='$Sin->fotovh2_f'><br /><br />":"");
			echo ($Sin->fotovh3_f?"<img src='$Sin->fotovh3_f'><br /><br />":"");
			echo ($Sin->fotovh4_f?"<img src='$Sin->fotovh4_f'><br /><br />":"");
			if($Sin->img_inv_entrada_f) // pinta todas las imagenes de la entrada del servicio
			{
				echo "<font color='BLUE' style='font-size:26;font-weight:bold'>IMAGENES DE DEVOLUCION</font><br>";
				echo ($Sin->img_inv_entrada_f?"<img src='$Sin->img_inv_entrada_f'><br /><br />":"");
				echo ($Sin->fotovh5_f?"<img src='$Sin->fotovh5_f'><br /><br />":"");
				echo ($Sin->fotovh6_f?"<img src='$Sin->fotovh6_f'><br /><br />":"");
				echo ($Sin->fotovh7_f?"<img src='$Sin->fotovh7_f'><br /><br />":"");
				echo ($Sin->fotovh8_f?"<img src='$Sin->fotovh8_f'><br /><br />":"");
				echo ($Sin->fotovh9_f?"<img src='$Sin->fotovh9_f' >":"");
			}
		}
		else echo "No se encuentra la información correspondiente al siniestro.";
		echo "<hr><a href='zcontrol_operativo3Dev.php?Acc=historia_fotografica&idPlaca=$idPlaca&pagina=".($pagina+1)."' target='_self'>SINIESTRO ANTERIOR</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		if($pagina>1) echo "<a href='zcontrol_operativo3Dev.php?Acc=historia_fotografica&idPlaca=$idPlaca&pagina=".($pagina-1)."' target='_self'>SINIESTRO SIGUIENTE</a>";
	}
	else echo "No hay historia de servicios de este vehículo.";
}

function adiciona_mantenimiento() // formulario para adicionar un mantenimiento a la hoja de vida de los vehiculos
{
	global $Placa;
	html('Adicion de mantenimiento');
	echo "<script language='javascript'>
		function carga() {	centrar(800,500);}
		function enviar()
		{
			if(Number(document.forma.kilometraje.value)<=0)
			{	alert('Debe escribir un kilometraje valido');	return;}
			if(!esfecha(document.forma.fecha.value))
			{alert('Debe seleccionar una fecha valida');return;}
			document.forma.Acc.value='adiciona_mantenimiento_ok';
			document.forma.submit();
		}
		function cancelar(){window.close();void(null);}
	</script>
	<body onload='carga()'>
	<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma' id='forma'>
		<input type='hidden' name='Acc' id='Acc' value=''>
		Placa: <input type='text' name='placa' value='$Placa' style='font-size:14;font-weight:bold' size=6 readonly><br>
		Novedad: ".menu1('Nov',"select codigo,nombre from novedad_vehiculo order by codigo",'MNT',0,''," disabled ")."<br>
		<input type='hidden' name='novedad' value='MNT'>
		Kilometraje: <input type='text' name='kilometraje' id='kilometraje' class='numero' size='10' maxlength='10'><br>
		Fecha del último mantenimiento: ".pinta_FC('forma','fecha')."<BR>
		<input type='button' onclick='enviar()' value='GRABAR' style='width:100;height:30;font-weight:bold'>
		<input type='button' onclick='cancelar()' value='CANCELAR' style='width:100;height:30;font-weight:bold'>
	</form></body>";
}

function adiciona_mantenimiento_ok() // graba un estado nuevo de mantenimiento
{
	global $placa,$novedad,$kilometraje,$fecha;
	$NID=q("insert into hv_vehiculo (placa,novedad,kilometraje,fecha) values ('$placa','$novedad','$kilometraje','$fecha') "); // inseta el registro en la tabla de hojas de vida de los vehiculos
	graba_bitacora('hv_vehiculo','A',$NID,'Adiciona Registro'); // graba la bitacora de la hoja de vida
	echo "<body><script language='javascript'> window.close();void(null);opener.location.reload();</script></body>";
}

function adiciona_soat() // adiciona un estado de soat a la tabla de control de  hojas de vida de los vehiculos
{
	global $Placa;
	html('Adicion de SOAT');
	echo "<script language='javascript'>
		function carga(){centrar(800,500);}
		function enviar(){	if(!esfecha(document.forma.fecha.value))	{alert('Debe seleccionar una fecha valida');return;}	document.forma.Acc.value='adiciona_mantenimiento_ok';document.forma.submit();}
		function cancelar(){	window.close();void(null);}
	</script>
	<body onload='carga()'>
	<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma' id='forma'>
		<input type='hidden' name='Acc' id='Acc' value=''>
		Placa: <input type='text' name='placa' value='$Placa' style='font-size:14;font-weight:bold' size=6 readonly><br>
		Novedad: ".menu1('Nov',"select codigo,nombre from novedad_vehiculo order by codigo",'SOA',0,''," disabled ")."<br>
		<input type='hidden' name='novedad' value='SOA'>
		<input type='hidden' name='kilometraje' id='kilometraje' size='10' maxlength='10' value='0'><br>
		Fecha de la última adquisición del SOAT: ".pinta_FC('forma','fecha')."<BR>
		<input type='button' onclick='enviar()' value='GRABAR' style='width:100;height:30;font-weight:bold'>
		<input type='button' onclick='cancelar()' value='CANCELAR' style='width:100;height:30;font-weight:bold'>
	</form></body>";
}

function adiciona_rtm() // formulario para adicionar un estado de revisión técnico mecánica a la hoja de vida de los vehiculos
{
	global $Placa;
	html('Adicion de REVISION TECNICO MECANICA');
	echo "<script language='javascript'>
		function carga(){centrar(800,500);}
		function enviar(){	if(!esfecha(document.forma.fecha.value)){alert('Debe seleccionar una fecha valida');return;}document.forma.Acc.value='adiciona_mantenimiento_ok';document.forma.submit();}
		function cancelar(){	window.close();void(null);}
	</script>
	<body onload='carga()'>
	<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma' id='forma'>
		<input type='hidden' name='Acc' id='Acc' value=''>
		Placa: <input type='text' name='placa' value='$Placa' style='font-size:14;font-weight:bold' size=6 readonly><br>
		Novedad: ".menu1('Nov',"select codigo,nombre from novedad_vehiculo order by codigo",'RTM',0,''," disabled ")."<br>
		<input type='hidden' name='novedad' value='RTM'>
		<input type='hidden' name='kilometraje' id='kilometraje' size='10' maxlength='10' value='0'><br>
		Fecha de la última Revisión Técnico Mecánica: ".pinta_FC('forma','fecha')."<BR>
		<input type='button' onclick='enviar()' value='GRABAR' style='width:100;height:30;font-weight:bold'>
		<input type='button' onclick='cancelar()' value='CANCELAR' style='width:100;height:30;font-weight:bold'>
	</form></body>";
}

function cambiar_a_fueraservicio() // cambia una ubicacion de estado a fuera de servicio
{
	global $id;
	q("update ubicacion set estado=5 where id=$id"); // actualiza el registro de ubicación
	graba_bitacora('ubicacion','M',$id,"Cambia de mantenimiento a fuera de servicio "); // graba la bitacora del registro
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}

function cambiar_a_mantenimiento() // cambia una ubicación de estado a mantenimiento
{
	global $id;
	q("update ubicacion set estado=4 where id=$id"); // actualiza el registro de la ubicación
	graba_bitacora('ubicacion','M',$id,"Cambia de fuera de servicio a mantenimiento"); // graba la bitacora del registro
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}

function marcar_siniestro_propio() // marca un fuera de servicio como siniestro propio, de la flota de AOA, o sea, un siniestro de vehículo de AOA causado por un asegurado
{
	global $id;
	q("update ubicacion set siniestro_propio=1 where id=$id"); // actualiza la uticación
	graba_bitacora('ubicacion','M',$id,"Marca Siniestro Asegurado"); // graba la bitácora del registro
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}

function desmarcar_siniestro_propio() // quita la marca de siniestro propio, en caso de que sea una responsabilidad de un funcionario de AOA el fuera de servicio
{
	global $id;
	q("update ubicacion set siniestro_propio=0 where id=$id"); //actualiza la ubicación
	graba_bitacora('ubicacion','M',$id,"Des-marca Siniestro Asegurado"); // graba la bitacora del registro.
	echo "<body><script language='javascript'>parent.parent.document.forma.submit();</script></body>";
}

function cambio_temporal() // formulario para cambiar temporalmente un vehiculo de flota  para ser usado en un servicio de otra flota.
{
	global $id,$NUSUARIO;
	$Actual=qo("select * from ubicacion where id=$id"); // trae la informacion de la ubicación
	html('Cambio Temporal de Flota'); // pinta la cabecera html
	echo "<body>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma' id='forma'>
			<h3>Solicitud de cambio temporal de flota</h3>
			Usuario: <input type='text' name='usuario' value='$NUSUARIO' readonly size=50><br>
			Seleccione la flota: ".menu1("flota","Select id,nombre from aseguradora where id!=6 and id!=$Actual->flota ",0,1,"",
				" onchange=\"this.form.generar.style.visibility='visible';\" ")."<br>
			<br><input type='submit' name='generar' id='generar' value='GENERAR SOLICITUD' style='visibility:hidden'>
			<input type='hidden' name='Acc' value='cambio_temporal_ok'>
			<input type='hidden' name='id' value='$id'>
		</form></body>";
}

function cambio_temporal_ok() // graba el cambio temporal de flota, crea un nuevo estado de parqueadero en la nueva flota seleccionada
{
	global $usuario,$id,$flota;
	$Hoy=date('Y-m-d H:i:s');
	$Email_usuario=usuario('email'); //obtiene el email del usuario
	//$Ruta_arturo="utilidades/Operativo/operativo.php?Acc=autorizar_cambio_temporal&id=$id&Fecha=$Hoy&Usuario=ARTURO QUINTERO RODRIGUEZ&solicitadopor=$usuario&flota=$flota";
	// configura la ruta para que el director operativo pueda aprobar la solicitud desde un correo electrónico.
	$Ruta_gabriel="utilidades/Operativo/operativo.php?Acc=autorizar_cambio_temporal&id=$id&Fecha=$Hoy&Usuario=GABRIEL SANDOVAL PAVAJEAU&solicitadopor=$usuario&flota=$flota";

	$UB=qo("select * from ubicacion where id=$id"); // trae los datos de la ubicacion
	$V=qo("select placa,t_aseguradora(flota) as nflota from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$A=qo1("select t_aseguradora($flota)"); // trae el nombre de la aseguradora
	// configura el mensaje para el correo electronico
	$Mensaje="<body><b>SOLICITUD DE CAMBIO TEMPORAL DE FLOTA</B><BR><BR>Vehiculo: $V->placa Flota principal: $V->nflota<br>".
			"Solicita cambio de flota a: $A<br>Funcionario que solicita: $usuario Fecha de solicitud: $Hoy <br><br>";
	// encripta la ruta de aprobación para que no quede evidente en el correo electronico
	$Mensaje.="Para AUTORIZAR el cambio temporal haga click aquí: <a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='$Ruta_gabriel';\$Fecha_control='".date('Y-m-d')."';")."' target='_blank'>AUTORIZAR</a></body>";
	$Envio2=enviar_gmail($Email_usuario /*de */,
				$NUSUARIO /*Nombre de */ ,
				"dirop@aoacolombia.com,Direccion Operativa" /*para */,
				"" /*con copia*/,
				"SOLICITUD CAMBIO TEMPORAL FLOTA $V->placa" /*Objeto */,
				$Mensaje); // envia el correo electronico

	if($Envio2)	echo "<script language='javascript'>alert('Envio de solicitud satisfactorio');window.close();void(null);</script>";
	else echo "<script language='javascript'>alert('El envio de solicitud fallo. Intente nuevamente.');window.close();void(null);</script>";
}

function retorno_flota() // funcion que retorna un vehiculo a su flota original
{
	global $id;
	$Ub=qo("select vehiculo from ubicacion where id=$id"); // trae los datos de la ubicacion
	$Vh=qo("select flota from vehiculo where id=$Ub->vehiculo"); // trae los datos del vehiculo
	q("update ubicacion set flota=$Vh->flota where id=$id"); // actualiza la ubicación
	echo "<script language='javascript'>window.close();void(null);parent.location.reload();</script>";
}

function cambiar_fecha_devolucion() // formulario para cambiar la fecha de devolución de un vehículo en un servicio mal cerrado
{
	global $id;
	$D=qo("select * from ubicacion where id=$id"); // trae los datos de la ubicación
	html('Cambio Fecha de Devolución'); // pinta la cabecera html
	echo "<script language='javascript'>
			function carga(){centrar(700,500);}
		</script>
		<body onload='carga()'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma' id='forma'>
			Fecha de devolución: ".pinta_FC('forma','FD',$D->fecha_final)."
			<br><br><input type='submit' value='Continuar'>
			<input type='hidden' name='Acc' value='cambiar_fecha_devolucion_ok'>
			<input type='hidden' name='id' value='$id'>
		</form>
		</body>";
}

function cambiar_fecha_devolucion_ok() // cambia la fecha de devolución de un vehiculo en un servicio mal cerrado,
{
	global $id,$FD;
	$D=qo("select * from ubicacion where id=$id"); // trae los datos de la ubicacion
	$V=qo("select placa from vehiculo where id=$D->vehiculo"); // trae los datos del vehiculo
	q("update ubicacion set fecha_final='$FD' where id=$id"); // actualiza la fecha de la ubicacion
	html('Cambio Fecha de Devolución'); // pinta la cabecera html
	echo "<body><script language='javascript'>centrar(400,300);</script><br>Cambio de fecha de la ubicación satisfactorio a $FD";
	if($Cita=qo("select id from cita_servicio where placa='$V->placa' and fecha='$D->fecha_inicial' and estado='C' ")) // busca la cita correspondiente al siniestro
	{
		q("update cita_servicio set fec_devolucion='$FD' where id=$Cita->id "); // actualiza la fecha de devolucion de la cita
		echo "<br>Cambio de fecha de devolución en la Cita satisfactorio";
	}
	if($Al=qo1("select id from ubicacion where vehiculo=$D->vehiculo and estado=8 and fecha_inicial='$D->fecha_final'  ")) // busca estado de alistamiento posterior a la fecha de devolución
	{
		q("update ubicacion set fecha_inicial='$FD',fecha_final='$FD' where id=$Al"); // actualiza la fecha inicial y final del estado de alistamiento
		echo "<br>Cambio de la fecha inicial y final del alistamiento satisfactorio";
	}
	if($P=qo1("select id from ubicacion where vehiculo=$D->vehiculo and estado=2 and fecha_inicial>='$D->fecha_final'  ")) // busca estado de parqueadero posterior a la fecha de devolucion
	{
		q("update ubicacion set fecha_inicial='$FD',fecha_final='$FD' where id=$P"); // actualiza la fecha incial y final del estado de parqueadero
		echo "<br>Cambio de la fecha inicial y final del parqueadero satisfactorio";
	}
}

function muestra_diario_parqueadero() // en forma de hoja electrónica descarta el estado de los parqueaderos
{
	global $D,$F;
	header("Content-type: application/vnd.ms-excel"); // cabecera html
	header("Content-Disposition: attachment; filename=parqueaderos_$F.xls"); // cabecera html
	echo "<table border cellspacing='0'><tr><th>Parqueaderos / Alistamientos fecha: $F</th></tr><tr><td>".str_replace(',',"</td></tr><tr><td>",$D)."</td></tr></table>";
}

function insertar_comparendo() // inserta en la tabla de comparendos la infromación de un comparendo
{
	global $id;   // id de la ubicacion
	$Ub=qo("select * from  ubicacion where id=$id"); // trae los datos de la ubicación
	if(!$Sin=qo("select * from siniestro where ubicacion=$id")) $Sin=qo("select * from siniestro_hst where ubicacion=$id"); // busca el siniestro
	if($Sin)
	{
		$Garantia=qo("select t_franquisia_tarjeta(franquicia) as nfran,nombre from sin_autor where siniestro=$Sin->id and estado='A' "); // trae los datos de la garantia
		$IDC=q("insert into comparendo (siniestro,vehiculo,autorizado,garantia) values ('$Sin->id','$Ub->vehiculo','$Garantia->nombre','$Garantia->nfran')"); // inserta el registro en la tabla de comparendos
		graba_bitacora('comparendo','A',$IDC,'Adiciona Registro'); // graba la bitacora del comparendo
	}
	else
	{
		$IDC=q("insert into comparendo (vehiculo) values ('$Ub->vehiculo')"); // solo graba el registro del comparendo sin datos de siniestro ni garantia
		graba_bitacora('comparendo','A',$IDC,'Adiciona Registro');	// graba la bitacora del comparendo
	}
	echo "<body><script language='javascript'>alert('Registro numero $IDC insertado en tabla de Comparendos');</script></body>";
}

function tomar_id_para_balance() // funcion que crea una variable de sesion de un id de ubicación para trasladar un balance de estado a otro estado
{
	global $id;
	$_SESSION['Id_para_balance']=$id; // creación de la variable de sesión
	echo "<body><script language='javascript'>alert('Id $id tomado para trasladar balance de estado. Ahora seleccione el nuevo estado.');</script></body>";
}

function asignar_id_para_balance() // reasignación del balance de estado a una nueva ubcación
{
	global $id;
	$BDA='aoacol_administra';
	$idbalance=$_SESSION['Id_para_balance']; // variable de sesión de la ubicación anterior
	q("update $BDA.requisicion set ubicacion=$id where ubicacion=$idbalance"); // actualiza las requisiciones del balance de ese estado
	q("update $BDA.fac_detalle set ubicacion=$id where ubicacion=$idbalance"); // actualiza los detalles de facturadión del balance de ese estado
	q("update $BDA.caja_menord set ubicacion=$id where ubicacion=$idbalance"); // actualiza las asociaciones de caja menor
	$_SESSION['Id_para_balance']=false; // apaga la variable de sesion
	session_unregister('Id_para_balance'); //  elimina la variable de sesion
	echo "<body><script language='javascript'>alert('Balance trasladado satisfactoriamente.');</script></body>";
}

function insertar_hv()
{
	global $app;
	include('inc/gpos.php');
	$Tipo_alerta=qo("select * from tipo_alerta where id=$ta");
	$Codigo_novedad=qo("select * from novedad_vehiculo where id=$Tipo_alerta->novedad_operativa");
	$Alerta_vehiculo=qo("select * from cfg_alerta_vehiculo where vehiculo='$v' and alerta='$ta' ");
	$Vehiculo=qo("select placa from vehiculo where id=$v");
	html();
	echo "
		<script language='javascript'>
			function regresar()
			{
				opener.parent.recargar_datos();window.close();void(null);
			}
		</script>
		<body><h3 align='center'>Inserción de Evento - $Tipo_alerta->nombre<br><img src='$Tipo_alerta->icono_f' height='50px'></h3>";
	if($Tipo_alerta->control=='K')		
	{
		$Siguiente_evento=$Alerta_vehiculo->ultimo_kilometraje+$Alerta_vehiculo->kilo_rojo;
		echo "<h4>Control por Kilometraje</h4>
			Cada ".coma_format($Alerta_vehiculo->kilo_rojo)."<br>
			Ultimo reporte: ".coma_format($Alerta_vehiculo->ultimo_kilometraje)."<br>
			El evento debe registrarse a los ".coma_format($Siguiente_evento)." kilometros <br>
			El vehículo tiene en este momento ".coma_format($actual)." Kilometros ";
	}	
	if($Tipo_alerta->control=='T')		
	{
		$Siguiente_evento=date('Y-m-d',strtotime(aumentadias($Alerta_vehiculo->ultimo_fecha,$Alerta_vehiculo->dias_rojo)));
		echo "<h4>Control por Tiempo programado</h4>
			Cada $Alerta_vehiculo->dias_rojo días <br>
			Ultimo reporte: $Alerta_vehiculo->ultimo_fecha<br>
			El evento debe registrarse en la fecha $Siguiente_evento<br>
			Hoy estamos a ".fecha_completa($actual);
	}	
	echo "<form action='zcontrol_operativo3Dev.php' target='Oculto_ihv' method='POST' name='forma' id='forma'>";
	if($Tipo_alerta->control=='K')		
	{
		echo "<br><br>Kilometraje del evento: <input type='number' name='kilometros' id='kilometros' value='$actual' size=10><br><br>
					<input type='button' class='button' name='btn_continuar' id='btn_continuar' value='GRABAR NOVEDAD' onclick=\"valida_campos('forma','kilometros');\">
					<input type='hidden' name='Acc' value='insertar_hvkm'>";
	}
	if($Tipo_alerta->control=='T')
	{
		echo "<br><br>Fecha del evento: ".pinta_fc('forma','fecha',date('Y-m-d'))."<br><br>
					<input type='button' class='button' name='btn_continuar' id='btn_continuar' value='GRABAR NOVEDAD' onclick=\"valida_campos('forma','fecha:f');\">
					<input type='hidden' name='Acc' value='insertar_hvfc'>";
	}
	echo "
				<input type='hidden' name='vehiculo' value='$v'>
				<input type='hidden' name='placa' value='$Vehiculo->placa'>
				<input type='hidden' name='alerta' value='$ta'>
				<input type='hidden' name='novedad' value='$Codigo_novedad->codigo'>
				<input type='hidden' name='idav' value='$Alerta_vehiculo->id'>
			</form>
		<iframe name='Oculto_ihv' id='Oculto_ihv' style='display:none' width='1' height='1'></iframe>
	</body>";
	
}

function insertar_hvkm()
{
	global $app;
	include('inc/gpos.php');
	$Hoy=date('Y-m-d');
	sesion();
	$idn=q("INSERT INTO hv_vehiculo (placa,novedad,kilometraje,fecha) values ('$placa','$novedad','$kilometros','$Hoy')");
	q("UPDATE cfg_alerta_vehiculo SET ultimo_kilometraje='$kilometros',ultimo_fecha='$Hoy' WHERE id='$idav' ");
	graba_bitacora('hv_vehiculo','A',$idn,"Adiciona desde el Tablero de Control");
	echo "<body><script language='javascript'>parent.regresar();</script></body>";
}

function insertar_hvfc()
{
	global $app;
	include('inc/gpos.php');
	sesion();
	$idn=q("INSERT INTO hv_vehiculo (placa,novedad,fecha) values ('$placa','$novedad','$fecha')");
	q("UPDATE cfg_alerta_vehiculo SET ultimo_fecha='$fecha' WHERE id='$idav' ");
	graba_bitacora('hv_vehiculo','A',$idn,"Adiciona desde el Tablero de Control");
	echo "<body><script language='javascript'>parent.regresar();</script></body>";
}

function debug_control($linea)
{
	$Fin_de_linea="\r\n";
	$f=fopen('planos/debug_control.txt','a');
	fwrite($f,$linea.$Fin_de_linea);
	fclose($f);
}

////////////// JULIO 19 DE 2016

function crear_estado_taller() // formulario para adicionar un ESTADO DE TALLER
{
	global $idub,$USUARIO;
	
	$Ubicacion=qo("SELECT *,t_oficina(oficina) as nofi,t_aseguradora(flota) as nflota FROM ubicacion WHERE id=$idub");
	$Ciudad=qo1("select ciudad from oficina where id=$Ubicacion->oficina");
	$V=qo("select placa,flota_distinta from vehiculo where id=$Ubicacion->vehiculo"); // busca la información del vehiculo
	$estado=10;
	$nestado=qo1("select t_estado_vehiculo($estado)");
	$kmi=$kmf=$Ubicacion->odometro_final;
	html(); // pinta las cabeceras html
	$fini=$Ubicacion->fecha_final; // obtiene la fecha inicial
	// pinta las herramientas javascript
	echo "
		<script language='javascript'>
			function cerrar_ventana_taller() {parent.oculta_menu_celda();}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
			<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
				<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'>
					<tr>
						<td>
							<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
								<tr>
									<td style='cursor:pointer' nowrap='yes'  onclick='cerrar_ventana_taller();' colspan=4>
										<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b>
									</td>
								</tr>
								<tr>
									<td colspan=4><h3>Creación de evento Taller</h3></td>
								</tr>
								<tr>
									<td align='right'>Fecha inicial:</td>
									<td><input type='text' name='FI' value='$fini' readonly size='10'></td>
									<td align='right'>Oficina</td>
									<td>$Ubicacion->nofi<input type='hidden' name='OFI' id='OFI' value='$Ubicacion->oficina'></td>
									
								</tr>
								<tr>
									<td align='right'>Fecha final:</td>
									<td>".pinta_FC('forma2','FF',$fini)."</td>
									<td align='right'>Flota:</td>
									<td>$Ubicacion->nflota<input type='hidden' name='FLOT' id='FLOT' value='$Ubicacion->flota'></td>
								</tr>
								<tr>
									<td align='right'>Kilometraje inicial:</td>
									<td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly></td>
									<td align='right'>Kilometraje final:</td>
									<td><input type='text' name='KMF' class='numero' value='$kmf' size='10' readonly></td>
								</tr>
								<tr>
									<td align='right'>Estado:</td><td>$nestado</td>
								</tr>
								<tr>
									<td align='right'>Oficina Taller AOA:</td>
									<td>".menu1("oficina_taller","select id,nombre from oficina where oficina_taller=1",21,0)."</td>
								</tr>
								<tr>
									<td align='right'>Taller:</td>
									<td>".menu1("taller","select id,nombre from taller where ciudad='$Ciudad' order by nombre",0,1)."</td>
								</tr>
								<tr>
									<td align='right'>Observaciones:</td>
									<td colspan=3><textarea name='observaciones' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td>
								</tr>
								<tr>
									<td align='center' colspan=4>
										<input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick=\"valida_campos('forma2','oficina_taller,observaciones,taller');\">
										<input type='hidden' name='Acc' id='Acc' value='adiciona_taller_ok'>
										<input type='hidden' name='vehiculo' value='$Ubicacion->vehiculo'>
										<input type='hidden' name='oficina' value='$Ubicacion->oficina'>
										<input type='hidden' name='estado' value='$estado'>
										<input type='hidden' name='flota' value='$Ubicacion->flota'>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td bgcolor='ffffff'>
							<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
						</td>
					</tr>
				</table>
			</form>
			<script language='javascript'>
				parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
				parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
				parent.mc_evento_texto(1);
			</script>
		</body>
	</html>"; 
}

function adiciona_taller_ok()
{
	global $app;
	include('inc/gpos.php');
	sesion();
	$idn=q("INSERT INTO ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,observaciones,oficina_taller,taller) 
			values ('$oficina','$vehiculo','$estado','$flota','$FI','$FF','$KMI','$KMF',0,\"$observaciones\",'$oficina_taller','$taller')");
	graba_bitacora('ubicacion','A',$idn,"Adiciona registro desde Tabla de Control");
	echo "<body><script language='javascript'>parent.parent.recargar_datos();</script></body>";
}

function cerrar_estado_taller() // funcion para cerrar un estado de mantenimiento y dejar el vehiculo en parqueadero
{
	global $idub,$Nomueve,$USUARIO;
	$UB=qo("select * from ubicacion where id=$idub"); // trae los datos de la ubicacion
	$V=qo("select placa,flota_distinta from vehiculo where id=$UB->vehiculo"); // trae los datos del vehiculo
	$fini=$UB->fecha_inicial;
	$ffin=date('Y-m-d');
	$oficina=$UB->oficina;
	$flota=$UB->flota;
	$kmi=$UB->odometro_inicial;
	$kmf=$UB->odometro_final;
	$Diferencia=$kmf-$kmi;
	html(); // pinta la cabecera html
	// pinta las funciones js y el formulario
	echo "
		<script language='javascript'>
			function validar_cierre()
			{
				with(document.forma2)
				{
					if(Number(KMF.value)==0) {alert('Debe escribir un kilometraje final válido'); return false;}
					if(Number(KMF.value)<Number(KMI.value)) {alert('El kilometraje final no puede ser menor que el kilometraje inicial');return false; }
					if(Number(KMF.value)==Number(KMI.value)) {alert('El cierre de Mantenimiento exige que se registre un kilometraje final que no puede ser igual al inicial');return false;}
					if(!alltrim(OBS.value)) {alert('Debe escribir una observación con respecto a la creación de este estado'); return false; }
				}
				document.forma2.submit();
			}
			function calcula_diferencia()
			{
				with(document.forma2)
				{
					Diferencia.value=Number(KMF.value)-Number(KMI.value);
				}
			}
		</script>
		<body topmargin='0' bottommargin='0' leftmargin='0' rightmargin='0'>
		<form action='zcontrol_operativo3Dev.php' method='post' target='_self' name='forma2' id='forma2'>
		<table border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' name='Context_Celda' id='Context_Celda' align='center' width='700px'><tr><td>
		<table border='0' cellspacing='0' cellpadding='3' bgcolor='ffffff' width='100%'>
		<tr><td style='cursor:pointer' nowrap='yes'  onclick='parent.oculta_menu_celda();' colspan='2'>
		<img src='gifs/standar/stop_16.png' border='0'> <b>Cerrar ventana</b> Placa: <b style='color:0000aa'>$V->placa</b></td></tr>
		<tr><td colspan=4><h3>Cierre de estado TALLER</h3></td></tr>";
	echo "<tr><td width='10%'>Fecha inicial:</td><td><input type='text' name='FI' value='$fini' readonly size='10'></td>
				<td width='10%'>Fecha final:</td><td><input type='text' name='FF' value='$ffin' readonly size='10'></td></tr>
				</table><table border='0' cellspacing='0' cellpadding='2' bgcolor='fffff' width='100%'>
				<tr><td align='right'>Oficina</td><td>".qo1("select t_oficina($oficina)")."</td>
				<td align='right'>Flota:</td><td>".qo1("select t_aseguradora($flota)")."<input type='hidden' name='FLOT' id='FLOT' value='$flota'></td></tr>
				<tr><td align='right'>Kilometraje inicial:</td><td><input type='text' name='KMI' class='numero' value='$kmi' size='10' readonly onclick=\"alert('no se puede modificar el kilometraje inicial');\"></td>
				<td align='right'>Kilometraje final:</td><td><input type='text' name='KMF' class='numero' value='$kmf' size='10' onblur='calcula_diferencia();'></td></tr>
				<tr><td align='right'>Estado:</td><td>".qo1("select nombre from estado_vehiculo where id=$UB->estado")."</td>
				<td align='right'>Diferencia en kilometraje:</td><td><input type='text' name='Diferencia' value='$Diferencia' class='numero' size='10' readonly></td></tr> ";
	echo "<tr><td align='right'>Observaciones:</td><td colspan=3><textarea name='OBS' cols='80' rows='4' style='font-family:arial;font-size:14px'></textarea></td></tr>
				<tr><td align='center' colspan=4><input type='button' value='Grabar' style='width:200px;height:30px;font-weight:bold' onclick='validar_cierre()'>
				<input type='hidden' name='Acc' id='Acc' value='cierre_taller_ok'><input type='hidden' name='idub' id='idub' value='$idub'>
				</td></tr>";
	echo "</table><tr><td bgcolor='ffffff'><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></td></tr></table></form>";
	echo "<script language='javascript'>
			parent.document.getElementById('Menu_contextual_celda').style.width=document.getElementById('Context_Celda').clientWidth+20;
			parent.document.getElementById('Menu_contextual_celda').style.height=document.getElementById('Context_Celda').clientHeight+20;
			parent.mc_evento_texto($Nomueve);
		</script></body></html>";
}

function cierre_taller_ok() // cierre del mantenimiento y adicion del registro de parqueadero automático
{
	global $idub,$KMF,$OBS,$NUSUARIO;
	$Hoy=date('Y-m-d H:i:s');
	$Fecha=date('Y-m-d');
	q("update ubicacion set odometro_final=$KMF, odometro_diferencia=odometro_final-odometro_inicial,
		obs_mantenimiento=concat(obs_mantenimiento,\"\n$NUSUARIO [$Hoy] $OBS\") where id=$idub"); // actualiza la ubicación con los odómetros y las observaciones
	$UB=qo("select * from ubicacion where id=$idub"); //  vuelve a cargar la ubicacion
	$IDU=q("insert into ubicacion (oficina,vehiculo,estado,flota,fecha_inicial,fecha_final,odometro_inicial,odometro_final,odometro_diferencia,obs_mantenimiento) values
	('$UB->oficina','$UB->vehiculo','2','$UB->flota','$Fecha','$Fecha','$KMF','$KMF','0','$NUSUARIO [$Hoy] Creación de estado automática') "); // adiciona el estado de parqueadero
	graba_bitacora('ubicacion','A',$IDU,"Adiciona Registro"); // graba la bitacora del parqueadero
	echo "<script language='javascript'>
		function carga()
		{
			parent.parent.document.forma.submit();
		}
	</script>
	<body onload='carga()'></body>";
}
?>