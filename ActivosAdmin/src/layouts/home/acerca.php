<?php
/*código php... */
header('Content-Type: text/html; charset=utf-8');



 $HTML = "

   

<template>
  <v-app>

<body id='inner_page' data-spy='scroll' data-target='#navbar-wd' data-offset='98'>

    <!-- LOADER -->
    <div id='preloader'>
        <div class='loader'>
            <img src='style/images/loader.gif' alt='#' />
        </div>
    </div>
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
                            <li><a href='davidduque@aoacolombia.com'><img src='style/images/mail_icon.png' alt='#' />exchang@gmail.com</a></li>
                            <li><a href='davidduque@aoacolombia.com'><img src='style/images/phone_icon.png' alt='#' />+53 3197614394</a></li>
                            <li><a class='join_bt' href='#'>Ingresar </a></li>
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

    <!-- Start Banner -->
    <div class='section inner_page_banner'>
        <div class='container'>
            <div class='row'>
                <div class='col-md-12'>
                    <div class='banner_title'>
                        <h3>Sobre nosotras</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Banner -->
    
    <!-- section -->
    <div class='section layout_padding about_bg'>
        <div class='container'>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='full paddding_left_15'>
                        <div class='heading_main text_align_left'>
                           <h2>ACAERCA </h2>    
                        </div>
                    </div>
                    <div class='full paddding_left_15'>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                    </div>
                    <div class='full paddding_left_15'>
                        <a class='main_bt' href='#'>Read More ></a>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='full text_align_right_img'>
                        <img src='style/images/img3.png' alt='#' />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end section -->

	
    <!-- section -->
    <div class='section'>
        <div class='container'>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='full text_align_right_img'>
                        <img src='style/images/img1.png' alt='#' />
                    </div>
                </div>
                <div class='col-md-6 layout_padding'>
                    <div class='full paddding_left_15'>
                        <div class='heading_main text_align_left'>
						   <h2><span class='theme_color'>Welcome</span> To Exchange</h2>	
                        </div>
                    </div>
                    <div class='full paddding_left_15'>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                    </div>
                    <div class='full paddding_left_15'>
                        <a class='main_bt' href='#'>Sobre más ></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<!-- end section -->
   
    <!-- section -->
    <div class='section white_fonts' style='background: #111;'>
        <div class='container'>
            <div class='row'>
                <div class='col-md-6' style='background: #fff;'>
                    <div class='full text_align_right_img'>
                        <img src='style/images/img2.png' alt='#' />
                    </div>
                </div>
                <div class='col-md-6 layout_padding'>
                    <div class='full paddding_left_15'>
                        <div class='heading_main text_align_left'>
                           <h2><span class='theme_color'>Apply for</span> Exchange</h2>    
                        </div>
                    </div>
                    <div class='full paddding_left_15'>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                    </div>
                    <div class='full paddding_left_15'>
                        <a class='main_bt' href='#'>Exchange ></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end section -->
    <!-- Start Footer -->
    <footer class='footer-box'>
        <div class='container'>
            <div class='row'>
               <div class='col-md-12 white_fonts'>
                    <div class='row'>
                        <div class='col-sm-6 col-md-6 col-lg-3'>
                            <div class='full'>
                                <img class='img-responsive' src='style/images/footer_logo.png' alt='#' />
                            </div>
                        </div>
                        <div class='col-sm-6 col-md-6 col-lg-3'>
                            <div class='full'>
                                <h3>ADMIN ASSET</h3>
                            </div>
                            <div class='full'>
                                <ul class='menu_footer'>
                                    <li><a href='login'>> Home</a></li>
                                    <li><a href='about'>> About</a></li>
                                    <li><a href='exchange'>> Exchange</a></li>
                                    <li><a href='services.html'>> Services</a></li>
                                    <li><a href='new.html'>> New</a></li>
                                    <li><a href='contact.html'>> Contact</a></li>
                                </ul>
                            </div>
                        </div>
                         <div class='col-sm-6 col-md-6 col-lg-3'>
                            <div class='full'>
                                <div class='footer_blog full white_fonts'>
                             <h3>Boletin informativo</h3>
                             <p>Ingresa tu correo </p>
                             <div class='newsletter_form'>
                                <form action='index.html'>
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
                             <h3>Contact us</h3>
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
<script type='module' src='src/layouts/login.js'></script>
 
 ";

   echo html_entity_decode($HTML, ENT_NOQUOTES, "UTF-8");


?>