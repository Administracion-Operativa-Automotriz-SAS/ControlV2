<?php
ini_set('error_reporting', E_ALL);

include("class.phpmailer.php");
include("class.smtp.php");

function getConexion() {
	$LINK = mysql_pconnect('localhost', 'dpa', 'GzL78R8LJAGZWNyP');
	mysql_select_db('dpa',$LINK);
	return $LINK;
}

$LINK = getConexion();

function make_token($user) {
	return sha1( md5 ( microtime() . $user ) );
}
 
$sql = "SELECT 	usr 
				
		FROM 	get_ws  
		WHERE 	rol='Aldia Logistica'
		";
$query = @mysql_query ($sql, $LINK);
$nRows = @mysql_num_rows ($query);

	if ($nRows > 0) {
		for ($i=0; $i<$nRows; $i++) {
		$rowReg = @mysql_fetch_array($query);
		$usr = $rowReg['usr'];

		$sql = "UPDATE 	get_ws SET token='".make_token($usr)."', status=1

				WHERE	usr='".$usr."'
				AND		rol='Aldia Logistica'
				";
		$query = @mysql_query ($sql, $LINK);
		
			}		
		}

@mysql_free_result($query);

function dataSumMinuts($dateTimeActual, $valueRest) {
	
	$piecesDateTimeActual = @explode(" ", $dateTimeActual);
	$explodeDateActual = $piecesDateTimeActual[0];
	$dataDate = @explode("-", $explodeDateActual);
	$yearData = $dataDate[0];
	$monthData = $dataDate[1];
	$dayData = $dataDate[2];
	$explodeTimeActual = $piecesDateTimeActual[1];
	$dataTime = @explode(":", $explodeTimeActual);
	$hourData = $dataTime[0];
	$minutData = $dataTime[1];
	$secondData = $dataTime[2];
	
	$dataMinus15 = @date ( "Y-m-d H:i:s" , @mktime($hourData, ($minutData + $valueRest), $secondData, $monthData, $dayData, $yearData) );
	return $dataMinus15;
		
	}

#######################################################################################################################################
#######################################################################################################################################

function processSendTokenWs($user, $LINK)	{

$dif=(@date_default_timezone_set("America/Bogota"));
$fecha=@time();
$fecha_hora=@date ( "Y-m-d" )." ".@date ( "H:i:s" , $fecha );

// Obtenemos y traducimos el nombre del día
$_dia=@date("l");
if ($_dia=="Monday") $_dia="Lunes";
if ($_dia=="Tuesday") $_dia="Martes";
if ($_dia=="Wednesday") $_dia="Miércoles";
if ($_dia=="Thursday") $_dia="Jueves";
if ($_dia=="Friday") $_dia="Viernes";
if ($_dia=="Saturday") $_dia="Sabado";
if ($_dia=="Sunday") $_dia="Domingo";

// Obtenemos el número del día
$_dia2=date("d");

// Obtenemos y traducimos el nombre del mes
$_mes=date("F");
if ($_mes=="January") $_mes="Enero";
if ($_mes=="February") $_mes="Febrero";
if ($_mes=="March") $_mes="Marzo";
if ($_mes=="April") $_mes="Abril";
if ($_mes=="May") $_mes="Mayo";
if ($_mes=="June") $_mes="Junio";
if ($_mes=="July") $_mes="Julio";
if ($_mes=="August") $_mes="Agosto";
if ($_mes=="September") $_mes="Setiembre";
if ($_mes=="October") $_mes="Octubre";
if ($_mes=="November") $_mes="Noviembre";
if ($_mes=="December") $_mes="Diciembre";
// Obtenemos el año
$_ano=date("Y");

// Imprimimos la fecha completa
$public=$_dia.' '.$_dia2.' de '.$_mes.' de '.$_ano;

$dia=@date('j');
$mes=@date('n');
$anho=@date('Y');
$nombreRadicado="SC ".$dia .'/'. $mes .'-' . $anho;

$html='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Mailing Service</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
</head>
<body marginheight="0" topmargin="0" marginwidth="0" bgcolor="#c5c5c5" leftmargin="0">
<table cellspacing="0" border="0" height="100%" style="background-color: #fafafa;" cellpadding="0" width="100%">
    <tr>
        <td valign="top">
            <!-- main table -->
            <table cellspacing="0" border="0" align="center" cellpadding="0" width="675">
                <!-- note -->
                <tr>
                    <td valign="top">
                        <table cellspacing="0" border="0" align="center" cellpadding="0" width="646">
                            <tr>
                                <td width="646" valign="top" class="note" style="text-align: center; font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #2b2b2b; line-height: 18px;"> <br />
								Estás recibiendo este boletín porque te has registrado en www.aoacolombia.com. ¿Tiene problemas para leer este e-mail? <a href="dpa.aoacolombia.com" style="color: #3b464f; font-weight: bold; text-decoration: none;">AOA Colombia Project DPA</a>. 
                                    <br />
                                  <webversion style="color: #3b464f; font-weight: bold; text-decoration: none;">Usted puede verlo en su navegador web</webversion></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- / note -->
                <!-- header -->
                <tr>
                    <td valign="top">
                        <table cellspacing="0" border="0" align="center" cellpadding="0" width="675">
                            <!-- top -->
                            <tr>
                                <td height="9" valign="top"> <img src="http://190.85.62.37/tnt/images/header-top.gif" alt="" style="display: block;" /> </td>
                            </tr>
                            <!-- / top -->
                            <!-- middle -->
                            <tr>
                                <td valign="top">
                                    <table cellspacing="0" border="0" cellpadding="0" width="675">
                                        <tr>
                                            <td valign="top" width="5"> <img src="http://190.85.62.37/tnt/images/header-side.gif" alt="" style="display: block;" /> </td>
                                            <td height="90" valign="top">
                                                <table cellspacing="0" border="0" height="90" cellpadding="0" width="665">
                                                    <tr>
                                                        <td class="header-content" height="90" valign="top" style="background-color: #bbcedd; background-image: url(\'http://190.85.62.37/tnt/images/header-content.gif\');">
                                                            <table cellspacing="0" border="0" height="90" cellpadding="0" width="665">
                                                                <tr>
                                                                    <!-- circle -->
                                                                    <td height="81" align="right" valign="middle" width="81">
                                                                        <table cellspacing="0" border="0" height="81" cellpadding="0" width="101">
                                                                            <tr>
                                                                                <td rowspan="3"> <img src="http://190.85.62.37/tnt/images/spacer.gif" height="1" style="display: block;" width="19" /> </td>
                                                                                <td class="circle" height="56" align="left" valign="top" width="81" style="color: #fff; text-transform: uppercase; font-size: 9px; text-align: center; font-family: Arial, Helvetica, sans-serif; width: 81px; height: 56px; background-color: #8494A1; background-image: url(\'http://190.85.62.37/tnt/images/circle.png\'); padding: 25px 0 0 0; background-repeat: no-repeat;">
                                                                                    <p style="color: #fff; text-transform: uppercase; font-size: 9px; text-align: center; font-family: Arial, Helvetica, sans-serif; margin-top: 0px; margin-bottom: 0px; padding: 0px; -webkit-text-size-adjust: none; clear: both;">
                                                                                    </p>
                                                                                    <strong style="font-size: 12px;">
                                                                                    '.$fecha_hora.'
                                                                                    </strong> </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <!-- / circle -->
                                                                    <td class="main-title" align="right" valign="middle" width="545" style="color: #4c545b; font-family: Georgia, serif; font-size: 41px; font-weight: bold; font-style: italic;"> AOA Colombia</td>
                                                                    <td valign="top"> <img src="http://190.85.62.37/tnt/images/spacer.gif" height="1" alt="" style="display: block;" width="20" /></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td valign="top" width="5"> <img src="http://190.85.62.37/tnt/images/header-side.gif" alt="" style="display: block;" /> </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- / middle -->
                            <!-- botom -->
                            <tr>
                                <td height="35" valign="top"> <img src="http://190.85.62.37/tnt/images/header-bottom.png" alt="" style="display: block;" /> </td>
                            </tr>
                            <!-- / bottom -->
                        </table>
                    </td>
                </tr>
                <!-- / header -->
                <!-- content -->
                <tr>
                    <td valign="top">
                        <table cellspacing="0" border="0" align="center" cellpadding="0" width="670">
                            <!-- article title -->
                            <tr>
                                <td valign="top"> <img src="http://190.85.62.37/tnt/images/double-line.gif" alt="" style="display: block;" /> </td>
                            </tr>
                            <tr>
                                <td class="article-title" height="30" valign="middle" style="text-transform: uppercase; font-family: Georgia, serif; font-size: 16px; color: #2b2b2b; font-style: italic; border-bottom: 1px solid #c1c1c1;"> Token. Consumo Web Service.</td>
                            </tr>
                            <!-- / article title -->
                            <!-- article -->
                            <tr>
                                <td class="copy" valign="top" style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #2b2b2b; line-height: 18px; text-align: justify;">
                                <div align="center">
                                <img src="http://190.85.62.37/tnt/images/_logo_seg.JPG" alt="" width="284" height="179" align="middle" style="padding-left: 10px; padding-bottom: 10px;" />
                                </div>
                                </td>
                          </tr>


                            <tr>
                                <td valign="top"> <img src="http://190.85.62.37/tnt/images/double-line.gif" alt="" style="display: block;" /> </td>
                            </tr>
                            <tr>
                                <td class="article-title" height="30" valign="middle" style="text-transform: uppercase; font-family: Georgia, serif; font-size: 16px; color: #2b2b2b; font-style: italic; border-bottom: 1px solid #c1c1c1;"> INFORMACI&Oacute;N TOKEN</td>';

$dif=(@date_default_timezone_set("America/Bogota"));
$fecha=@time();
$fecha_hora=@date ( "Y-m-d" )." ".@date ( "H:i:s" , $fecha );

$fechaSumada =	dataSumMinuts($fecha_hora, 15);

$sql = "SELECT 	token 
				
		FROM 	get_ws  
		WHERE 	usr='".$user."'
		AND		rol='Aldia Logistica'
		";
$query = @mysql_query ($sql, $LINK);
$rowsReg = @mysql_fetch_array ($query);

$html.='	<tr>
				<td class="copy" valign="top" style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #2b2b2b; line-height: 18px;">
					<font face="Trebuchet MS, Arial, Helvetica, sans-serif" size="3">
					  <p>
					  	<strong>Por medio de este mail, se notifica el recibo del TOKEN para consumo de la información para consumo del Web Service. </strong>
					  	<p>La  información pertinente es la siguiente: </p>
						<p>Usuario WS: <strong>'.$user.'</strong></p>
						<p>Password WS: <em> ****** </em></p>
						<p>TOKEN: <strong>'.$rowsReg['token'].'</strong></p>
						<p>
						<p>
						<em>** Se hace la salvedad que este TOKEN tendrá validez, solo en el lapso de los 15 minutos siguientes a esta hora exacta, es decir cumplidas las <strong>'.$fechaSumada.'</strong> acabará su tiempo <u>ACTIVIDAD</u> y será necesario <strong><u>RENOVARLO</u></strong>, autenticándose en la interfaz User/Pass del WS.</em>
						<p>Para renovarlo, por favor remitase al siguiente <a href="http://190.85.62.37/logWs/login.php" style="color: #3b464f; font-weight: bold; text-decoration: none;">LINK</a>.
                        <br>
                        <br>
						</td>
						</tr>

                            <!-- / article -->
                            <!-- article title -->
                            <tr>
                                <td valign="top"> <img src="http://190.85.62.37/tnt/images/double-line.gif" alt="" style="display: block;" /> 
																</td>
                            </tr>
														
                            <!-- / article -->
                            <!-- article title -->
                            <tr>
                                <td valign="top"> <img src="http://190.85.62.37/tnt/images/double-line.gif" alt="" style="display: block;" /> </td>
                            </tr>
                            <tr>
                                <td class="article-title" height="30" valign="middle" style="text-transform: uppercase; font-family: Georgia, serif; font-size: 16px; color: #2b2b2b; font-style: italic; border-bottom: 1px solid #c1c1c1;"> CLAUSULA DE CONFIDENCIALIDAD</td>
                            </tr>
                            <!-- / article title -->
                            <!-- article -->
                            <tr>
                                <td class="copy" valign="top" style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #2b2b2b; line-height: 18px;"> <br />
                            <p align="justify">
							<font face="Trebuchet MS, Arial, Helvetica, sans-serif" size="2">						
							La información transmitida a través de este correo electrónico es confidencial y dirigida única y exclusivamente para uso de su (s) destinatario (s). Su reproducción, lectura o uso está prohibido a cualquier persona o entidad diferente, sin autorización previa por escrito. Si usted lo ha recibido por error, por favor notifíquelo inmediatamente al remitente y elimínelo de su sistema. Usted no debe copiar, imprimir o distribuir este correo o sus anexos, ni usarlos para propósito alguno, ni dar a conocer su contenido a persona alguna. Las opiniones, conclusiones y otra información contenida en este correo, no relacionadas con el negocio oficial de la compañia, deben entenderse como personales y de ninguna manera son avaladas por la compañía. Aunque AOA Colombia y las empresas que lo conforman han realizado su mejor esfuerzo para asegurar que el presente mensaje y sus archivos anexos se encuentran libre de virus y defectos que puedan llegar a afectar los computadores o sistemas que lo reciban, no se hace responsable por la eventual transmisión de virus o programas dañinos por este conducto, y por lo tanto es responsabilidad del destinatario confirmar la existencia de este tipo de elementos al momento de recibirlo y abrirlo. AOA Colombia, ni ninguna de sus divisiones o dependencias aceptan responsabilidad alguna por eventuales daños o alteraciones derivados de la recepción o uso del presente mensaje.
</font>
									</p>
                                    <br />
                                </td>
                            </tr>
														
                           <tr>
                                <td valign="top"> <img src="http://190.85.62.37/tnt/images/double-line.gif" alt="" style="display: block;" /> </td>
                            </tr>

                            <tr>
                                <td class="article-title" height="30" valign="middle" style="text-transform: uppercase; font-family: Georgia, serif; font-size: 16px; color: #2b2b2b; font-style: italic; border-bottom: 1px solid #c1c1c1;"> Contact center</td>
                            </tr>
                            <!-- / article title -->
                            <!-- article -->
                            <tr>
                                <td class="copy" valign="top" style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #2b2b2b; line-height: 18px;"> <br />
                                    <p align="justify">
									<font face="Trebuchet MS, Arial, Helvetica, sans-serif" size="2">
									Este mensaje ha sido enviado por un sistema automático, por favor no responder a este mail, para cualquier inquietud o cualquier información adicional por favor acercarse a nuestras oficinas ubicadas en la Carrera 69B 98A-10 Barrio Morato.
</font>																		
									</p>
                                    <br />
                                </td>
                            </tr>

                            <!-- / article -->
                        </table>
                    </td>
                </tr>
                <!-- / content -->
                <!-- footer -->
                <tr>
                    <td valign="top">
                        <table cellspacing="0" border="0" cellpadding="0" width="675">
                            <tr>
                                <td valign="top"> </td>
                            </tr>
                            <tr>
                                <td align="center" class="footer" valign="top" style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #2b2b2b; line-height: 18px;"> <img src="http://190.85.62.37/tnt/images/double-line.gif" alt="" width="675" style="display: block;" />
                                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="15"></td>
                                        </tr>
                                    </table>
                                    AOA Service Mailing
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="15"></td>
                                      </tr>
                                  </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- / footer -->
            </table>
            <!-- / main table -->
        </td>
    </tr>
</table>
</body>
</html>';


$mail = new PHPMailer();

$mail->SetLanguage('en');
//$mail->SetLanguage("/language/phpmailer.lang-en.php");

$mail->IsSMTP();
$mail->SMTPAuth = true;
//$mail->SMTPSecure = "ssl"; // Comentariada !!

$mail->Host = "mail.aoacolombia.com"; // ** Por defecto
//$mail->Port = 465;
//$mail->Port = 587;

$mail->CharSet = "UTF-8"; // Nuevo!!
$mail->Username = "dpa@aoacolombia.com";// Este es el correo del "CONT�