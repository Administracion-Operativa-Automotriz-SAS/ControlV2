<?php
/*c�digo php... */
header('Content-Type: text/html; charset=utf-8');



 $HTML = "
 

<template>
  <v-app>
 <b-modal v-model='modalShow' id='myModal'>
      <form>
        <div>
          <br>
            <input type='text' placeholder='Name' v-model='user.name'>
          <br>
            <input type='text' placeholder='Email' v-model='user.email'>
          <br>
            <input type='text' placeholder='Password' v-model='user.password'>
          <br>
        </div>
        <div>
          <b-btn @click='modalShow = false'>Cancel</b-btn>
          <b-btn variant='outline-primary' @click='addUser'>Create</b-btn>
        </div>
      </form>
    </b-modal>
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
                        <h3>Contacto</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Banner -->
    
   <!-- section -->
    <div class='section layout_padding'>
        <div class='container'>
            <div class='row'>
                <div class='col-md-12'>
                    <div class='full'>
                        <div class='heading_main text_align_center'>
                           <h2><span class='theme_color'></span>Contacto</h2>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end section -->

    <!-- contact_form -->
    <div class='section contact_form'>
        <div class='container'>
            <div class='row'>
                <div class='col-lg-6 col-sm-12 offset-lg-3'>
                    <div class='full'>
                        <form class='contact_form_inner' action='#'>
                            <fieldset>
                                <div class='field'>
                                    <input type='text' name='name' placeholder='Your name' />
                                </div>
                                <div class='field'>
                                    <input type='email' name='email' placeholder='Email' />
                                </div>
                                <div class='field'>
                                    <input type='text' name='phone_no' placeholder='Phone number' />
                                </div>
                                <div class='field'>
                                    <textarea placeholder='Message'></textarea>
                                </div>
                                <div class='field center'>
                                    <button>Enviar</button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end contact_form -->
   
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

    <!-- ALL JS FILES -->
   
   
     </v-app>


</template>
<script type='module' src='src/layouts/login.js'></script>

  ";

   echo html_entity_decode($HTML, ENT_NOQUOTES, "UTF-8");


?>