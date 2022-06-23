<?php
# PARAMETROS DE CONFIGURACION
ini_set('get_magic_quotes_gpc',0);
putenv('TZ=America/Bogota');
DEFINE('GLOBALES',false);
#$gestor_errores_anterior = set_error_handler("miGestorErrores");
# Dirección de correo electrónico de quien envia mensajes de soporte o reenvio de claves
DEFINE('MODO_GRABACION_MYSQL',3);   # 1: basica #2 segura
DEFINE('FROM_SOPORTE', 'administracion@intercolombia.com'); # correo electronico para envios de tipo soporte
DEFINE('DIRECTORIO_BACKUPS','Control/operativo/');
DEFINE('URL_INICIAL', 'http://174.132.105.98/~aoacol/Control');
//DEFINE('URL_INICIAL', 'http://localhost/aoafull/Control');
DEFINE('ENCRIPCION',2);   #  1: encripcion original   2: encripcion baja seguridad   3: encripcion alta seguridad

# DEFINICION DE LAS RUTAS PARA LAS GALERIAS DE IMAGENES
$GALERIAS[0]['ruta'] = 'img/'; $GALERIAS[0]['nombre'] = 'Imagenes';
$GALERIAS[1]['ruta'] = 'imagenes/datos/'; $GALERIAS[1]['nombre'] = 'Datos';
$GALERIAS[2]['ruta'] = 'imagenes/reportes/'; $GALERIAS[2]['nombre'] = 'Reportes';
# #########  PARAMETROS DE MYSQL
DEFINE('MYSQL_S', 'localhost');
DEFINE('MYSQL_D', 'aoacol_aoacars');
DEFINE('MYSQL_U', 'aoacol_arturo');
DEFINE('MYSQL_P', 'AOA0l1lwpdaa');
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

?>
