<?php
/*código php... */
header('Content-Type: text/html; charset=utf-8');



 $HTML = "
 
 <template>
 <v-app>
 
<body id='inner_page' data-spy='scroll' data-target='#navbar-wd' data-offset='98'>


    <!-- end loader -->
    <!-- END LOADER -->

    <!-- Start header -->
    <header class='top-header'>
        <div class='header_top'>
            
            <div class='container'>
                <div class='row'>
                    <div class='logo_section'>
                        <a class='navbar-brand' href=''><img src='http://app.aoacolombia.com/img/LOGO_AOA_200.png' alt='image'></a>
                    </div>
                    <div class='site_information'>
                        <ul>
                            <li><a href='davidduque@aoacolombia.com'><img src='style/images/mail_icon.png' alt='#' />davidduque@aoacolombia.com</a></li>
                            <li><a href='davidduque@aoacolombia.com'><img src='style/images/phone_icon.png' alt='#' />+53 3197614394</a></li>
                            <li><a class='join_bt' href='#/loginSI'>Ingresar </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        
        </div>
        <div class='header_bottom'>
          <div class='container'>
            <div class='col-sm-12'>
                <div class='menu_orange_section' style='background: #ff880e;'>
                   <nav class='navbar header-nav navbar-expand-lg'> 
                     <div class='menu_section'>
                        <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbar-wd' aria-controls='navbar-wd' aria-expanded='false' aria-label='Toggle navigation'>
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <div class='collapse navbar-collapse justify-content-end' id='navbar-wd'>
                    <ul class='navbar-nav'>
                        <li><a class='nav-link' href='#/login'>Inicio</a></li>
                        <li><a class='nav-link' href='#/acerca'>Acerca</a></li>
                        <li><a class='nav-link' href='#/ingresar'>Ingresar </a></li>
                        <li><a class='nav-link' href='#/servicios'>Servicios</a></li>
                        <li><a class='nav-link' href='#/nuevo'>Nueveo </a></li>
                        <li><a class='nav-link' href='#/contacto'>Contacto</a></li>
                    </ul>
                </div>
                     </div>
                 </nav>
                 <div class='search-box'>
                    <input type='text' class='search-txt' placeholder='Search'>
                    <a class='search-btn'>
                        <img src='style/images/search_icon.png' alt='#' />
                    </a>
                </div> 
                </div>
            </div>
          </div>
        </div>
        
   
    </header>
    <!-- End header -->
	<div id='app'>	
		<router-view>
		
		</router-view>
	</div>
 <!-- end contact_form -->
    <!-- end section -->
    <!-- Start Footer -->
    <footer class='footer-box'>
        <div class='container'>
            <div class='row'>
               <div class='col-md-12 white_fonts'>
                    <div class='row'>
                        <div class='col-sm-6 col-md-6 col-lg-3'>
                            <div class='full'>
                                <img class='img-responsive' src='http://app.aoacolombia.com/img/LOGO_AOA_200.png' alt='#' />
                            </div>
                        </div>
                        <div class='col-sm-6 col-md-6 col-lg-3'>
                            <div class='full'>
                                <h3>ADMIN ASSET</h3>
                            </div>
                            <div class='full'>
                                <ul class='menu_footer'>
                                    <li><a href='#/login'>>Inicio</a></li>
                                    <li><a href='#/acerca'>> Acerca</a></li>
                                    <li><a href='#/ingresar'>> Ingresar</a></li>
                                    <li><a href='#/servicios'>> Servicios</a></li>
                                    <li><a href='#/nuevo'>> Nuevo</a></li>
                                    <li><a href='#/contacto'>> Contacto</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class='col-sm-6 col-md-6 col-lg-3'>
                            <div class='full'>
                                <div class='footer_blog full white_fonts'>
                             <h3>Boletin informativo</h3>
                             <p>Ingresa tu correo </p>
                             <div class='newsletter_form'>
                                <form action='#/login'>
                                   <input type='email' placeholder='Tu  Email' name='#' required=''>
                                   <button>Enviar </button>
                                </form>
                             </div>
                         </div>
                            </div>
                        </div>
                        <div class='col-sm-6 col-md-6 col-lg-3'>
                            <div class='full'>
                                <div class='footer_blog full white_fonts'>
                             <h3>Contacto</h3>
                             <ul class='full'>
                               <li><img src='style/images/i6.png'><span>davidduque@aoacolombia</span></li>
                               <li><img src='style/images/i7.png'><span>+53 3197614394</span></li>
                             </ul>
                         </div>
                            </div>
                        </div>
					</div>
                </div>
			 </div>
        </div>
    </footer>
    <!-- End Footer -->

    <div class='footer_bottom'>
        <div class='container'>
            <div class='row'>
                <div class='col-12'>
                    <p class='crp'>David Duque Company software adu 2020 </p>
                </div>
            </div>
        </div>
    </div>

    <a href='#' id='scroll-to-top' class='hvr-radial-out'><i class='fa fa-angle-up'></i></a>
 </v-app>
 </template>


 <script type='module' src='src/layouts/home/home.js'></script>

 
 
 
 ";

   echo html_entity_decode($HTML, ENT_NOQUOTES, "UTF-8");


?>