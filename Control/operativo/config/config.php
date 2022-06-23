<?php
# PARAMETROS DE CONFIGURACION
ini_set('get_magic_quotes_gpc',0);
putenv('TZ=America/Bogota');
DEFINE('GLOBALES',false);
#$gestor_errores_anterior = set_error_handler("miGestorErrores");
# Dirección de correo electrónico de quien envia mensajes de soporte o reenvio de claves
DEFINE('MODO_GRABACION_MYSQL',1);   # 1: basica #2 segura
DEFINE('FROM_SOPORTE', 'administracion@intercolombia.net'); # correo electronico para envios de tipo soporte
DEFINE('DIRECTORIO_BACKUPS','Control/operativo/');
DEFINE('EMAIL_BACKUP','sergiocastillo@aoacolombia.com');
DEFINE('CONTADOR_BOTONES',10);
DEFINE('URL_INICIAL', 'https://www.aoacolombia.com');
//DEFINE('URL_INICIAL', 'https://localhost/aoafull/Control');
DEFINE('ENCRIPCION',3);   #  1: encripcion original   2: encripcion baja seguridad   3: encripcion alta seguridad
DEFINE('INFO_VERSION',1);
DEFINE('RESETEAR_CLAVE',0);
DEFINE('TUMB_SIZE',160); // Tamaño de las imágenes reducidas.
DEFINE('DIM',"https://app.aoacolombia.com/Control/operativo/");
# DEFINICION DE LAS RUTAS PARA LAS GALERIAS DE IMAGENES
$GALERIAS[0]['ruta'] = 'img/'; $GALERIAS[0]['nombre'] = 'Imagenes';
$GALERIAS[1]['ruta'] = 'imagenes/datos/'; $GALERIAS[1]['nombre'] = 'Datos';
$GALERIAS[2]['ruta'] = 'imagenes/reportes/'; $GALERIAS[2]['nombre'] = 'Reportes';
# #########  PARAMETROS DE MYSQL
DEFINE('MYSQL_S', 'database-controlaoa.cve2mwii9ck7.us-east-2.rds.amazonaws.com');
DEFINE('MYSQL_D', 'controlaoa');
DEFINE('MYSQL_U', 'controlaoa');
DEFINE('MYSQL_P','0l1lwpdaa');

# #########  PARAMETROS DE POSTGRES
DEFINE('PSQL_S', '');
DEFINE('PSQL_SP', '');
DEFINE('PSQL_D', '');
DEFINE('PSQL_U', '');
DEFINE('PSQL_P', '');

DEFINE('DEBUG', 'no');
# ##########   PARAMETROS SMTP  ########
DEFINE("SMTP_VALIDACION", false); # opciones: false, true, 'ssl' , 'tls'
DEFINE("SMTP_AUTORIZACION", 'autodetect'); # opciones:  autodetect, login, plain
DEFINE("SMTP_SERVIDOR", 'localhost');
DEFINE("SMTP_USUARIO", '');
DEFINE("SMTP_PASSWORD", '');
DEFINE("SMTP_PUERTO", 25);

# ##########   PARAMETROS SMTP  ########
DEFINE("GOGA_email",'@aoacolombia.com');

DEFINE('NOMBRE_APLICACION','CONTROL OPERATIVO - AOA');
DEFINE('TITULO_APLICACION','CTRL-AOA');
DEFINE('USER','CtrPerfyn5Qphast'.date('WzY'));
define('LOGO13','img/logoesquina.png');
//// VARIABLE PARA APP MOVILES
define('COOKIE_APP_DATA1','AOAcontrolAPP20150623_a');
define('COOKIE_APP_DATA2','AOAcontrolAPP20150623_b');
define('COOKIE_APP_PHP','m.aoacontrol.php');
?>
