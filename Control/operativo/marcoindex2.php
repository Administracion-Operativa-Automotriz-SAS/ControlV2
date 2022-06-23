<script src="//cdnjs.cloudflare.com/ajax/libs/annyang/2.6.0/annyang.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js" ></script>
<script src="Angular/Modules/app.js"></script>
<script src="Angular/Services/BrainService.js"></script>
<script src="Angular/Controller/BrainController.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<div ng-app="Appi">
	<div ng-controller="BrainController">
		{{test}}
		De:
		<input type="text" name="numero" ng-model="sms.from">
		Para:
		<input type="text" name="numero" ng-model="sms.to">
		Mensaje:
		<input type="text" name="numero" ng-model="sms.text">
		<button ng-click="send_message()">Probar mensaje</button>
		<button ng-click="test_ws()">Test</button>
		<div id="innerhtml">
			inner html
		</div>
	</div>
</div>
<?php
//DIE('APLICACION EN SUSPENCION POR 2 MINUTOS. Atte. Departamento de Tecnologia de Informacion.');

if(!$SESION_PUBLICA) require('inc/sess.php');
include_once('inc/funciones_.php');

prepara_rutinas($Acc);
//verificar_directorios();

if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}
html(TITULO_APLICACION.' - '.$_SESSION['Nombre']);
inicio();
die();


function verificar_directorios()
{
	if(!is_dir('imagenes')) mkdir('imagenes',0777);
	if(!is_dir('imagenes/reportes')) mkdir('imagenes/reportes',0777);
	if(!is_dir('imagenes/datos')) mkdir('imagenes/datos',0777);
}






?>

