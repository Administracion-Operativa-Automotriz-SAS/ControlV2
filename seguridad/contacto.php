<?php
/*código php... */
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=UTF-8');
mb_http_output( 'UTF-8' );

include('head.php');
echo utf8_encode("

    <div class='slide-one-item home-slider owl-carousel'>
      
      <div class='site-blocks-cover inner-page overlay' style='background-image: url(images/co97.jpg);' data-aos='fade' data-stellar-background-ratio='0.5'>
        <div class='container'>
          <div class='row align-items-center justify-content-center'>
            <div class='col-md-12 text-center' data-aos='fade'>
              <h1 class='font-secondary  font-weight-bold text-uppercase'>Contactenos, esparemos dispuesto para atenderlo</h1>
            </div>
          </div>
        </div>
      </div>  

	       <div class='site-blocks-cover inner-page overlay' style='background-image: url(images/co908.jpg);' data-aos='fade' data-stellar-background-ratio='0.5'>
        <div class='container'>
          <div class='row align-items-center justify-content-center'>
            <div class='col-md-12 text-center' data-aos='fade'>
              <h1 class='font-secondary font-weight-bold text-uppercase'>LLama! Nuestro colaboradores estan pendientes de atenderte  </h1>
            </div>
          </div>
        </div>
      </div> 
	  
      <div class='site-blocks-cover inner-page overlay' style='background-image: url(images/co877.jpg);' data-aos='fade' data-stellar-background-ratio='0.5'>
        <div class='container'>
          <div class='row align-items-center justify-content-center'>
            <div class='col-md-12 text-center' data-aos='fade'>
              <h1 class='font-secondary font-weight-bold text-uppercase'>Gracias por cuidarnos y cuidarte </h1>
            </div>
          </div>
        </div>
      </div> 
	  
	  </div> 

    <div class='slant-1'></div>

	
  <div class='site-section first-section' data-aos='fade'>
      <div class='container'>
        <div class='row'>
       
          <div class='col-md-12 col-lg-8 mb-5'>
		  ");
		  include('ifreme.php');

        echo utf8_encode("
          
          </div>

          <div class='col-lg-4'>
            <div class='p-4 mb-3 bg-white'>
              <h3 class='h5 text-black mb-3'>Información de contacto</h3>
              <p class='mb-0 font-weight-bold'>Ubicación </p>
              <p class='mb-4'>Bogota D.C</p>

              <p class='mb-0 font-weight-bold'>Teléfono</p>
              <p class='mb-4'><a href='#'>018000186262</a></p>
              <p class='mb-4'><a href='#'>+(571) 8837069</a></p>
              <p class='mb-0 font-weight-bold'>Sitio Web</p>
              <p class='mb-0'><a href='#'>http://www.aoacolombia.com</a></p>

            </div>
            
            
          </div>
        </div>
      </div>
    </div>

  

  



 
");

include('footer.php');

?>