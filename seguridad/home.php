<?php
/*código php... */
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=UTF-8');
mb_http_output( 'UTF-8' );

include('head.php');
echo utf8_encode("

       <div class='slide-one-item home-slider owl-carousel'>
      
      <div class='site-blocks-cover inner-page overlay' loading='lazy'  style='background-image: url(images/covid.jpg);' data-aos='fade' data-stellar-background-ratio='0.5'>
        <div class='container'>
          <div href='home.php' class='row align-items-center justify-content-center'>
            <div class='col-md-12 text-center' data-aos='fade'>
              <h1 class='font-secondary  font-weight-bold text-uppercase'>Gracias por cuidarte y cuidarnos</h1>
            </div>
          </div>
        </div>
      </div>  

	       <div  href='requisitos.php' class='site-blocks-cover inner-page overlay' loading='lazy' style='background-image: url(images/tapa.jpg);' data-aos='fade' data-stellar-background-ratio='0.5'>
        <div class='container'>
          <div class='row align-items-center justify-content-center'>
            <div   class='col-md-12 text-center' data-aos='fade'>
              <h1 class='font-secondary font-weight-bold text-uppercase'>Requerimientos de usuario </h1>
            </div>
          </div>
        </div>
      </div> 
	  
      <div class='site-blocks-cover inner-page overlay' loading='lazy' style='background-image: url(images/con21.jpg);' data-aos='fade' data-stellar-background-ratio='0.5'>
        <div class='container'>
          <div  href='entregav.php'  class='row align-items-center justify-content-center'>
            <div class='col-md-12 text-center' data-aos='fade'>
              <h1  href='entregav.php'   class='font-secondary font-weight-bold text-uppercase'>Protocolo Devolución de Vehículos</h1>
            </div>
          </div>
        </div>
      </div> 
	  
	    <div class='site-blocks-cover inner-page overlay' loading='lazy' style='background-image: url(images/con12.jpg);' data-aos='fade' data-stellar-background-ratio='0.5'>
        <div class='container'>
          <div href='devolucion.php' class='row align-items-center justify-content-center'>
            <div class='col-md-12 text-center' data-aos='fade'>
              <h1 class='font-secondary font-weight-bold text-uppercase'>Protocolo Entrega de Vehículos</h1>
            </div>
          </div>
        </div>
      </div> 
    </div>

    <div class='slant-1'></div>

    <div class='site-section first-section'>
      <div class='container'>
        <div class='row mb-5'>
          <div class='col-md-12 text-center' data-aos='fade'> 
		  <br><br>
            <span class='caption d-block mb-2 font-secondary font-weight-bold'>AOA Colombia</span>
            <h2 class='site-section-heading text-uppercase text-center font-secondary'>Te cuida y nos cuida.</h2>
          </div>
        </div>
        <div class='row border-responsive'>
          <div class='col-md-6 col-lg-3 mb-4 mb-lg-0 border-right' data-aos='fade-up' data-aos-delay=''>
            <div class='text-center'>
              <span class='flaticon-money-bag-with-dollar-symbol display-4 d-block mb-3 text-primary'></span>
              <h3 class='text-uppercase h4 mb-3'>Requerimientos de usuario</h3>
              <p>Para el usuario son obligatorios los requisitos para cuidar su salud y la nuestro se recomienda tener plena atención enlas suguerencias. No podrán asistir con niños</p>
            </div>
          </div>
          <div class='col-md-6 col-lg-3 mb-4 mb-lg-0 border-right' data-aos='fade-up' data-aos-delay='100'>
            <div class='text-center'>
              <span class='flaticon-bar-chart display-4 d-block mb-3 text-primary'></span>
              <h3 class='text-uppercase h4 mb-3'>Actividades del assesor </h3>
              <p>El asesor de servicio procede a desinfectar el vehículo siguiendo el paso a paso las instrucciones</p>
            </div>
          </div>
          <div class='col-md-6 col-lg-3 mb-4 mb-lg-0 border-right' data-aos='fade-up' data-aos-delay='200'>
            <div class='text-center'>
              <span class='flaticon-medal display-4 d-block mb-3 text-primary'></span>
              <h3 class='text-uppercase h4 mb-3'>Protocolo Entrega de Vehículos</h3>
              <p>Para el retiro de los vehículos de reemplazo solo podrá asistir una persona a retirar el vehículo, en caso de que el tarjetahabiente sea una persona diferente es posible que asista, pero deberá retirarse una vez inicie la entrega del vehículo. No podrán asistir con niños.</p>
            </div>
          </div>
          <div class='col-md-6 col-lg-3 mb-4 mb-lg-0' data-aos='fade-up' data-aos-delay='300'>
            <div class='text-center'>
              <span class='flaticon-box display-4 d-block mb-3 text-primary'></span>
              <h3 class='text-uppercase h4 mb-3'>Protocolo Devolución de Vehículos</h3>
              <p>Una vez arriba en las instalaciones de AOA, el cliente no debe salir del vehículo hasta que sea atendido. En la ciudades dónde el parqueadero se encuentre retirado de la oficina, se deberá coordinar con el cliente para el encuentro en el parqueadero.  mira solo le cambie para encontrarlo  no se si queda mejor el encuentro.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  

  <div class='site-section '>
      <div class='container'>
        <div class='row'>
          <div class='col-md-12 text-center'>
            <span class='caption d-block mb-2 font-secondary font-weight-bold'>Usuario</span>
            <h2 class='site-section-heading text-uppercase text-center font-secondary'>Requerimientos de usuario</h2>
                  
		 </div>
        </div>
        <div class='row'>
          <div class='col-md-12 block-13 nav-direction-white'>
            <div class='nonloop-block-13 owl-carousel'>
              <div class='media-image'>
                <img loading='lazy'  src='images/con786.png' alt='Image'  width='400' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Piso 1</h2>
				   <p>Deberá ingresar la información al sistema antes de solicitar el servicio.</p>
				 <p><a href='requisitos.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
				</div>
              </div>
              <div class='media-image'>
                <img  loading='lazy'  src='images/con23.jpg' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                <h2 class='font-secondary text-uppercase'>Paso 2</h2>
                <p>Deberá conservar una distancia mínima de dos metros con respecto a las demás personas que están esperando recibir el servicio.</p>                		
						<p><a href='requisitos.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
			   </div>
              </div>
              <div class='media-image'>
                <img  loading='lazy'  src='images/co97.jpg' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 3</h2>
				  <p>Deberá tener tapabocas.</p>    
				 <p><a href='requisitos.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
                </div>
              </div>
              <div class='media-image'>
                <img loading='lazy'   src='images/con34.jpg' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 4 </h2>
                   <p>Antes de ingresar a las instalaciones se deberá limpiar las manos con gel desinfectante.</p>             
			<p><a href='requisitos.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
		   </div>
				
              </div>
              <div class='media-image'>
                <img  loading='lazy'  src='images/co122.jpg' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 5</h2>
                    <p>Se informa  al assesor que el vehículo debe ser alistado.</p>             
			  <p><a href='requisitos.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
				</div>
              </div>
              <div class='media-image'>
                <img loading='lazy'   src='images/ccoo998.jpeg' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 6</h2>
             <p>Se realiza el proceso de Autorizaciones.</p>         
           	<p><a href='requisitos.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

	
	  <div class='site-section '>
      <div class='container'>
        <div class='row'>
          <div class='col-md-12 text-center'>
            <span class='caption d-block mb-2 font-secondary font-weight-bold'>Actividades del assesor</span>
            <h2 class='site-section-heading text-uppercase text-center font-secondary'>desinfección  del vehiculo</h2>
          </div>
        </div>
        <div class='row'>
          <div class='col-md-12 block-13 nav-direction-white'>
            <div class='nonloop-block-13 owl-carousel'>
              <div class='media-image'>
                <img loading='lazy'  src='images/con67.jpg' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 1</h2>
				   <p>Las llaves y el control remoto de apertura o alarma.</p>
				   <p><a href='assesor.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
				</div>
              </div>
              <div class='media-image'>
                <img loading='lazy'  src='images/con76.gif '  alt='Image' class='img-fluid'>
			
                <div class='media-image-body'>
                <h2 class='font-secondary text-uppercase'>Paso 2</h2>
                		 <p>Deberá iniciar por las manijas, marcos externos de las 
				puertas, lunetas y cubierta de espejos y tapa de baúl.</p>
						<p><a href='assesor.php'  class='btn btn-primary text-white px-4'>Ver más</a></p>
			   </div>
              </div>
              <div class='media-image'>
                <img  loading='lazy'  src='images/mc2.gif' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 3</h2>
				 <p>Al abrir la puerta del lado conductor 
				  desinfectar el marco interno de la puerta, el paral de la carrocería, los botones o manija 
				  elevavidrios, los descansabrazos, los ceniceros, los bolsillos de la cartera y la manija de
				  abrir la puerta</p>
				<p><a href='assesor.php'  class='btn btn-primary text-white px-4'>Ver más</a></p>
                </div>
              </div>
              <div class='media-image'>
                <img loading='lazy'   src='images/co98.gif'  alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 4 </h2>
              <p>La palanca de ajuste de la silla</p>
			 <p><a  href='assesor.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
		   </div>
				
              </div>
              <div class='media-image'>
                <img loading='lazy'   src='images/con56.gif' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 5</h2>
                 <p>El volante de la dirección</p>              
			  <p><a href='assesor.php'  class='btn btn-primary text-white px-4'>Ver más</a></p>
				</div>
              </div>
              <div class='media-image'>
                <img loading='lazy'  src='images/98con.gif' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 6 </h2>
                 <p>El interruptor o botón de encendido</p>
			   <p><a href='assesor.php'  class='btn btn-primary text-white px-4'>Ver más</a></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

	
	
	
	  <div class='site-section '>
      <div class='container'>
        <div class='row'>
          <div class='col-md-12 text-center'>
            <span class='caption d-block mb-2 font-secondary font-weight-bold'></span>
            <h2 class='site-section-heading text-uppercase text-center font-secondary'>Entrega de vehiculo</h2>
          </div>
        </div>
        <div class='row'>
          <div class='col-md-12 block-13 nav-direction-white'>
            <div class='nonloop-block-13 owl-carousel'>
              <div class='media-image'>
                <img loading='lazy'  src='images/co877.jpg' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 1</h2>
                              <p>Se desinfecta las manos con gel desinfectante o alcohol</p>				   
				   <p><a href='entrega.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
				</div>
              </div>
              <div class='media-image'>
                <img loading='lazy'   src='images/cor556.jpg'alt='Image' class='img-fluid'>
			
                <div class='media-image-body'>
                <h2 class='font-secondary text-uppercase'>Paso 2</h2>
                		   <p>El asesor pasa a recibir al usuario, debiendo guardar una distancia mínima de 2 metros,
				sin  establecer contacto físico con el usuario. Deja los documentos que deben ser revisados y
				firmados por el cliente sobre el escritorio para que él los reciba.</p>
						<p><a  href='entrega.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
			   </div>
              </div>
              <div class='media-image'>
                <img loading='lazy'  src='images/ccoo998.jpeg'  alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 3</h2>
				   <p>El usuario pasa a revisar el estado del vehículo.</p>
				 <p><a href='entrega.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
                </div>
              </div>
              <div class='media-image'>
                <img loading='lazy'   src='images/c9055.png'   alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 4</h2>
           <p>El asesor toma las fotografías 
				  del vehículo y odómetro frente al usuario.</p>
						<p><a  href='entrega.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
		   </div>
				
              </div>
              <div class='media-image'>
                <img loading='lazy'   src='images/con23.jpg' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 5</h2>
                 <p>El usuario firma los documentos,
				  y los deja sobre el vehículo para que el asesor los recoja.</p>
						<p><a  href='entrega.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
				</div>
              </div>
              <div class='media-image'>
                <img  loading='lazy'  src='images/images (7).jpg' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 6</h2>
                <p>El usuario toma las llaves ingresa al vehículo y se retira con él.</p>
						<p><a  href='entrega.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
                </div>
              </div>
			    <div class='media-image'>
                <img loading='lazy'   src='images/con34.jpg' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 7</h2>
               <p>El asesor procede a finalizar el proceso.</p>
						<p><a  href='entrega.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
                </div>
              </div>
			   <div class='media-image'>
                <img  loading='lazy'  src='images/images (7).jpg' alt='Image' class='img-fluid'>
                <div class='media-image-body'>
                  <h2 class='font-secondary text-uppercase'>Paso 8</h2>
               <p>El asesor se desinfecta las manos con gel antibacterial y alcohol.</p>
						<p><a  href='entrega.php' class='btn btn-primary text-white px-4'>Ver más</a></p>
                </div>
              </div>
			  
            </div>
          </div>
        </div>
      </div>
    </div>
   
        

    <div class='site-section section-counter'>
      <div class='container'>
        <div class='row'>
          <div class='col-lg-5'>
            <p class='mb-5'><img src='images/CAR45.jpg' alt='Image' class='img-fluid'></p>
          </div>
          <div class='col-lg-7 ml-auto'>
            <h2 class='site-section-heading mb-3 font-secondary text-uppercase'>
			Protocolo Devolución de Vehículos</h2>
        
            <div class='row'>
	    	<p class='mb-5'>
			Una vez arriba a las instalaciones de AOA el cliente no debe salir del vehículo hasta que sea atendido. En las ciudades en donde el parqueadero está retirado de la oficina se deberá coordinar con el cliente para encontrarlo en el parqueadero</p>
            </div>
            <div class='row'>
              <p class='mb-5'>Una vez llegue, el asesor permite que el usuario se baje del vehículo, quien deberá tener tapabocas y estar a una distancia mínima de 2 metros con respecto al asesor y se le pide que cierre la puerta y deje las llaves del vehículo sobre el techo, se debe tener cuidado de no recibirle las llaves al usuario</p>
            </div>
            
          </div>
        </div>
      </div>
    </div>

    
    <div class='site-section block-14 nav-direction-white'>

      <div class='container'>
        
        <div class='row mb-5'>
          <div class='col-md-12'>
            <h2 class='site-section-heading text-center text-uppercase'>Requerimientos de devolución </h2>
          </div>
        </div>

        <div class='nonloop-block-14 owl-carousel'>
          

            <div class='d-block block-testimony mx-auto text-center'>
              <div class='person w-25 mx-auto mb-4'>
                <img  loading='lazy'  src='images/con34.jpg' alt='Image' class='rounded-circle '>
              </div>
              <div>
                <h2 class='h5 mb-4'>Paso 1</h2>
                <blockquote>
				Se le pide que desinfecte sus manos con gel antibacterial</blockquote>
             
			 </div>
            </div>

            <div class='d-block block-testimony mx-auto text-center'>
              <div class='person w-25 mx-auto mb-4'>
                <img loading='lazy'   src='images/co544.jpeg' alt='Image' class='rounded-circle'>
              </div>
              <div>
                <h2 class='h5 mb-4'>Paso 2</h2>
                <blockquote>
				Se toman las fotografías externas del vehículo antes de ingresar a él  </blockquote>
              </div>
            </div>

            <div class='d-block block-testimony mx-auto text-center'>
              <div class='person w-25 mx-auto mb-4'>
                <img loading='lazy'  src='images/con67.jpg' alt='Image' class='rounded-circle '>
              </div>
              <div>
                <h2 class='h5 mb-4'>Paso 3</h2>
                <blockquote>
				Antes de tomar la llave esta deberá ser limpiada con desinfectante </blockquote>
              </div>
            </div>

          
      
		      <div class='d-block block-testimony mx-auto text-center'>
              <div class='person w-25 mx-auto mb-4'>
                <img loading='lazy'   src='images/con56.gif' alt='Image' class='rounded-circle '>
              </div>
              <div>
                <h2 class='h5 mb-4'>Paso 5</h2>
                <blockquote>
				Desinfecta el botón o interruptor de encendido, para abrirlo, no deberá sentarse en el vehículo</blockquote>
              </div>
            </div>
      
            <div class='d-block block-testimony mx-auto text-center'>
              <div class='person w-25 mx-auto mb-4'>
                <img loading='lazy'   src='images/cco98.jpg' alt='Image' class='rounded-circle '>
              </div>
              <div>
                <h2 class='h5 mb-4'>Paso 6</h2>
                <blockquote>
				Al ingresar no deben cerrar la puerta y una vez dentro del vehículo se toma la fotografía del kilometraje</blockquote>
              </div>
            </div>

      
    </div>

    <div class='site-section'>
      <div class='container'>
        <div class='row mb-5'>
          <div class='col-md-12' data-aos='fade'>
            <h2 class='site-section-heading text-center text-uppercase'>Acta de entrega </h2>
          </div>
        </div>
        <div class='row'>
          <div class='col-md-6 col-lg-4 mb-5' data-aos='fade-up' data-aos-delay='100'>
            <div class='media-image'>
              <a href='single.html'><img  loading='lazy' src='images/cuibu7787.jpg' alt='Image' class='img-fluid'></a>
              <div class='media-image-body'>
                <h2 class='font-secondary text-uppercase'><a href='single.html'>Paso 1  </a></h2>
                <span class='d-block mb-3'></span>
                <p>Se diligencia el Acta de entrega y se deja sobre el vehículo para que el usuario la tome, la valide y firme el documento</p>
                 <p><a href='#'>Ver más </a></p>
              </div>
            </div>
          </div>
          <div class='col-md-6 col-lg-4 mb-5' data-aos='fade-up' data-aos-delay='200'>
            <div class='media-image'>
              <a href='single.html'><img  loading='lazy'  src='images/566con.jpg' alt='Image' class='img-fluid'></a>
              <div class='media-image-body'>
                <h2 class='font-secondary text-uppercase'><a href='single.html'>Paso 2</a></h2>
                <span class='d-block mb-3'></span>
                <p>En caso de requerir cobros adicionales se le indica al cliente y se trasladan hacia la zona correspondiente</p>
                <p><a href='#'>Ver más </a></p>
              </div>
            </div>
          </div>
          <div class='col-md-6 col-lg-4 mb-5' data-aos='fade-up' data-aos-delay='300'>
            <div class='media-image'>
              <a href='single.html'><img loading='lazy'  src='images/ccoo998.jpeg' alt='Image' class='img-fluid'></a>
              <div class='media-image-body'>
                <h2 class='font-secondary text-uppercase'><a href='single.html'>PASO 3</a></h2>
                <span class='d-block mb-3'></span>
                <p>Se realiza el proceso de cobro teniendo presente no entregar o recibir directamente del usuario documentos.</p>
                <p><a href='#'>Read More</a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

 
");

include('footer.php');

?>