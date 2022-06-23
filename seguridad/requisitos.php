<?php
/*código php... */
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=UTF-8');

include('head.php');
echo utf8_encode("

    <div class='slide-one-item home-slider owl-carousel'>
      
      <div class='site-blocks-cover inner-page overlay' loading='lazy' style='background-image: url(images/tapa.jpg);' data-aos='fade' data-stellar-background-ratio='0.5'>
        <div class='container'>
          <div class='row align-items-center justify-content-center'>
            <div class='col-md-12 text-center' data-aos='fade'>
              <h1 class='font-secondary  font-weight-bold text-uppercase'>Requisitos del usuario</h1>
            </div>
          </div>
        </div>
      </div>  

	       <div class='site-blocks-cover inner-page overlay' loading='lazy' style='background-image: url(images/con34.jpg);' data-aos='fade' data-stellar-background-ratio='0.5'>
        <div class='container'>
          <div class='row align-items-center justify-content-center'>
            <div class='col-md-12 text-center' data-aos='fade'>
              <h1 class='font-secondary font-weight-bold text-uppercase'>Tú seguridad es más importante.</h1>
            </div>
          </div>
        </div>
      </div> 
	  
      <div class='site-blocks-cover inner-page overlay' loading='lazy' style='background-image: url(images/cuibu7787.jpg);' data-aos='fade' data-stellar-background-ratio='0.5'>
        <div class='container'>
          <div class='row align-items-center justify-content-center'>
            <div class='col-md-12 text-center' data-aos='fade'>
              <h1 class='font-secondary font-weight-bold text-uppercase'>Estamos para atender tú dudas</h1>
            </div>
          </div>
        </div>
      </div> 
	  
	    <div class='site-blocks-cover inner-page overlay' loading='lazy' style='background-image: url(images/con20.png);' data-aos='fade' data-stellar-background-ratio='0.5'>
        <div class='container'>
          <div class='row align-items-center justify-content-center'>
            <div class='col-md-12 text-center' data-aos='fade'>
              <h1 class='font-secondary font-weight-bold text-uppercase'>Gracias por cuidarte y cuidanos </h1>
            </div>
          </div>
        </div>
      </div> 
    </div>

    <div class='slant-1'></div>

    <br>
<div class='site-section bg-light'>
      <div class='container'>
        <div class='row mb-5'>
          <div class='col-md-12' data-aos='fade'>
            <h2 class='site-section-heading text-center text-uppercase'>Requerimientos de usuario </h2>
          </div>
        </div>
        <div class='row justify-content-center'>
          <div class='col-md-6 text-center mb-5' data-aos='fade-up' data-aos-delay='100'>
            <img loading='lazy'  src='images/cliente.png' alt='Image' class='img-fluid rounded w-50 mb-4'>
            <h2 class='h5 text-uppercase'>Primer Requisito</h2>
            <span class='d-block mb-4'></span>
            <p class='lead'>
			>Deberá ingresar la información al sistema antes de solicitar el servicio</p>
      
          </div>
          <div class='col-md-6 text-center mb-5' data-aos='fade-up' data-aos-delay='200'>
            <img  loading='lazy' src='images/con23.jpg' alt='Image' class='img-fluid rounded w-50 mb-4'>
            <h2 class='h5 text-uppercase'>Sengundo Requisito</h2>
            <span class='d-block mb-4'></span> 
            <p class='lead'>
			  Deberá conservar una distancia mínima de dos metros con respecto a las
				demás personas que están esperando recibir el servicio.</p>
   
          </div>
          <div class='col-md-6 text-center mb-5' data-aos='fade-up' data-aos-delay='300'>
            <img  loading='lazy' src='images/tapa.jpg' alt='Image' class='img-fluid rounded w-50 mb-4'>
            <h2 class='h5 text-uppercase'>Tercer Requisito</h2>
            <span class='d-block mb-4'></span>
            <p class='lead'>
	      	Deberá tener tapabocas</p>
          </div>
          <div class='col-md-6 text-center mb-5' data-aos='fade-up' data-aos-delay='400'>
            <img loading='lazy'  src='images/con34.jpg' alt='Image' class='img-fluid rounded w-50 mb-4'>
            <h2 class='h5 text-uppercase'>Cuarto Requisito</h2>
            <span class='d-block mb-4'></span> 
            <p class='lead'>
			Antes de ingresar a las instalaciones se deberá limpiar las manos con gel desinfectante .</p>
          </div>
		        <div class='col-md-6 text-center mb-5' data-aos='fade-up' data-aos-delay='400'>
            <img  loading='lazy' src='images/co122.jpg' alt='Image' class='img-fluid rounded w-50 mb-4'>
            <h2 class='h5 text-uppercase'>Quinto Requisito</h2>
            <span class='d-block mb-4'></span> 
            <p class='lead'>
			 Se informa a operaciones el vehículo que debe ser alistado.</p>
          </div>
		        <div class='col-md-6 text-center mb-5' data-aos='fade-up' data-aos-delay='400'>
            <img  loading='lazy' src='images/co908.jpg' alt='Image' class='img-fluid rounded w-50 mb-4'>
            <h2 class='h5 text-uppercase'>Sexto Requisito</h2>
            <span class='d-block mb-4'></span> 
            <p class='lead'>
			Se realiza el proceso de Autorizaciones.</p>
          </div>
        </div>
      </div>  
    </div>
  

");
include('footer.php');

?>