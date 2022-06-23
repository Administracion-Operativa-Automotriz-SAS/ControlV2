<?php
/*código php... */
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=UTF-8');


echo utf8_encode("

<!DOCTYPE html>
<html lang='en'>
  <head>
    <title>Control </title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>


    <link href='https://fonts.googleapis.com/css?family=Oswald:400,700|Work+Sans:300,400,700' rel='stylesheet'>
    <link rel='stylesheet' href='fonts/icomoon/style.css'>

    <link rel='stylesheet' href='css/bootstrap.min.css'>
    <link rel='stylesheet' href='css/magnific-popup.css'>
    <link rel='stylesheet' href='css/jquery-ui.css'>
    <link rel='stylesheet' href='css/owl.carousel.min.css'>
    <link rel='stylesheet' href='css/owl.theme.default.min.css'>
    <link rel='stylesheet' href='css/bootstrap-datepicker.css'>
    <link rel='stylesheet' href='css/animate.css'>    
    
    <link rel='stylesheet' href='fonts/flaticon/font/flaticon.css'>
  
    <link rel='stylesheet' href='css/aos.css'>

    <link rel='stylesheet' href='css/style.css'>
    
  </head>
  <body>
  
  <div id='overlayer'></div>
  <div class='loader'>
    <div class='spinner-border text-primary' role='status'>
      <span class='sr-only'>Cargando...</span>
    </div>
  </div>

  <div class='site-wrap'>

    

    <div class='site-mobile-menu'>
      <div class='site-mobile-menu-header'>
        <div class='site-mobile-menu-close mt-3'>
          <span class='icon-close2 js-menu-toggle'></span>
        </div>
      </div>
      <div class='site-mobile-menu-body'></div>
    </div> <!-- .site-mobile-menu -->
    
    
    <div class='site-navbar-wrap js-site-navbar bg-white'>
      
      <div class='container'>
        <div class='site-navbar bg-light'>
          <div class='row align-items-center'>
            <div class='col-2'>
              <h2 class='mb-0 site-logo'><a href='home.php' class='font-weight-bold text-uppercase'>
			    <img  width='257' src='images/banner-vehiculo-sustituto-AOA.jpg' >
			  </a></h2>
            </div>
            <div class='col-10'>
              <nav class='site-navigation text-right' role='navigation'>
                <div class='container'>
                  <div class='d-inline-block d-lg-none ml-md-0 mr-auto py-3'><a href='#' class='site-menu-toggle js-menu-toggle text-black'><span class='icon-menu h3'></span></a></div>
                  <ul class='site-menu js-clone-nav d-none d-lg-block'>
                    <li><a href='home.php'>Inicio</a></li>
                    <li class='has-children'>
                      <a >Usuario</a>
                      <ul class='dropdown arrow-top'>
                        <li><a href='requisitos.php'>Requisitos de usuario </a></li>
                        <li><a href='entrega.php'>Entrega de vehiculo</a></li> 
						 <li><a href='devolucionu.php'>Devolución de vehiculo </a></li> </ul>
					
                    </li>
					<li><a href='assesor.php'>Assesor</a></li>
			      <li class='has-children'>
                      <a >Servicios</a>
                      <ul class='dropdown arrow-top'>
                        <li><a href='devolucion.php'>Protocolo Devolución de Vehículos </a></li>
                        <li><a href='entregaV.php'>Protocolo Entrega de Vehículos</a></li>   </ul>
                    </li>
					
                   
                    <li><a  class='contacto'><span class='d-inline-block bg-primary text-white btn btn-primary'>Como te sientes </span></a></li>
                  </ul>
                </div>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
")


?>