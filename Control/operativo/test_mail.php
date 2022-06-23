<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('inc/funciones_.php');
//include("inc/smtp2/class.phpmailer.php");

//echo phpinfo();
enviar_gmail('sistemas@aoacolombia.com','prueba correo','jesusvega@aoacolombia.com');