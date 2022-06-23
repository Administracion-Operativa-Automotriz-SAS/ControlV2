<?php
/*código php... */
header('Content-Type: text/html; charset=utf-8');



 $HTML = "
 
<template>
  <v-app>

    <v-dialog v-model='dialog' max-width='290'>
      <v-card>
        <v-card-title class='headline'>Use Google's location service?</v-card-title>
        <v-card-text>
					<v-form
      ref='form'
      v-model='valid'
      :lazy-validation='lazy'
    >
	
	<div class='formulario-entrada'>
<h3>Bienvenido a Módulo novedad </h3>
<div class='contenedor-formulario-entrada-izq-der'>
<div class='formulario-entrada-izq'>
    <div class='input-group mb-3'>
	  
 <v-text-field style=' color: #a8ad00; width: 80%;' id='usuario' v-model='usuario'
        :counter='10'
		 append-icon='mdi-account-box'
        :rules='nameRules'
		 label='Usuario'
        name='Usuario' type='text' placeholder='Usuario' title='Ingresar el usuario ' size='20'></v-text-field>

        </div>
</div>

<div class='formulario-entrada-der '>
    <div class='input-group mb-3'>
	
	<v-text-field
            :append-icon='show2 ? 'mdi-eye' : 'mdi-eye-off''
            :type='show2 ? 'text' : 'password''
             v-model='clave'
            :rules='emailRules'
            label='Contaseña'
            required
            name='Clave' placeholder='Contraseña' title='Ingresar el contraseña ' size='20'
            class='input-group--focused'
            @click:append='show2 = !show2'
          ></v-text-field>

	 
	   <div></div>
	
	
        </div>



</div>


</div>

			  <div class='contenedor-enviar'> 
                     <div class=' text-center contenedor-enviar'>			  
              </div>
			  		
		 <div class='contenedor-enviar'> 
							 <div style='color:red' id='MSGresult'></div>	  
              </div>
			  <div class='contenedor-enviar'> 
		  <v-btn
        :disabled='!valid'
        color='success'
        class='mr-4'
        @click='validate()'
      >
        Ingresaer
      </v-btn>
		</div>  
			   </div>
</div>
<div class='wpcf7-response-output wpcf7-display-none'></div>

    </v-form>
	<v-dialog
      v-model='sele'
      width='500'
    >
	
	
    
      <v-card>
        <v-card-title
          class='headline grey lighten-2'
          primary-title
        >
         Perfil AOA 
        </v-card-title>

        <v-card-text>
        </v-card-text>

        
		<v-alert
    outlined
      type='info'
      prominent
      border='left'
    >
	
	{{mensaje}}
	
<el-select
				  title='Nombre Perfil  '
							  class=' mr-sm-2 align-items-center' 
							  v-model='perfilList'
								allow-create
								:disabled='disabled == 1' 
								placeholder='Selecion perfil de usuario '
			  @change='printValue()'>
				
				<el-option
								v-for='item in perfil'
                                 :key='item.Nombre_Perfil'							
								  :label='item.Nombre_Perfil'
								  :value='item'>
								</el-option>
			  </el-select>
		
							 
								
							  </el-select>
		

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color='primary'
            text
            @click='sele = !sele'
          >
           Salir
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
	
	<v-dialog
      v-model='word'
      width='500'
    >
     

      <v-card>
        <v-card-title
          class='headline grey lighten-2'
          primary-title
        >
         Validación de ingreso 
        </v-card-title>

        <v-card-text>
        </v-card-text>

        
		<v-alert
    outlined
      type='warning'
      prominent
      border='left'
    >
	
	{{mensaje}} 
		

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color='primary'
            text
            @click='word = !word'
          >
           Salir
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
		
		/v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color='green darken-1' flat='flat' @click.native='dialog = false'>Disagree</v-btn>
          <v-btn color='green darken-1' flat='flat' @click.native='dialog = false'>Agree</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
                            <li>	<v-btn color='primary' dark @click.native.stop='dialog = true'>Ingresar</v-btn>
							<a class='join_bt' href='#'> </a></li>
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
                        <h3>Nuevo</h3>
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
                           <h2><span class='theme_color'></span>Nuevo</h2>    
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-4 col-sm-6 col-xs-12'>
                    <div class='full Nuevo_blog'>
                       <img class='img-responsive' src='style/images/b1.png' alt='#' />
                       <div class='overlay'><a class='main_bt transparent' href='#'>View</a></div>
                       <div class='blog_details'>
                         <h3>Bitcoin Nuevo</h3>
                         <p>pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                       </div>
                    </div>
                </div>
                <div class='col-md-4 col-sm-6 col-xs-12'>
                    <div class='full Nuevo_blog'>
                        <img class='img-responsive' src='style/images/b2.png' alt='#' />
                        <div class='overlay'><a class='main_bt transparent' href='#'>View</a></div>
                       <div class='blog_details'>
                         <h3>Ethereum Nuevo</h3>
                         <p>pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                       </div>
                    </div>
                </div>
                <div class='col-md-4 col-sm-6 col-xs-12'>
                    <div class='full Nuevo_blog'>
                        <img class='img-responsive' src='style/images/b3.png' alt='#' />
                        <div class='overlay'><a class='main_bt transparent' href='#'>View</a></div>
                       <div class='blog_details'>
                         <h3>Light Nuevo</h3>
                         <p>pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                       </div>
                    </div>
                </div>
                <div class='col-md-4 col-sm-6 col-xs-12'>
                    <div class='full Nuevo_blog'>
                       <img class='img-responsive' src='style/images/b1.png' alt='#' />
                       <div class='overlay'><a class='main_bt transparent' href='#'>View</a></div>
                       <div class='blog_details'>
                         <h3>Bitcoin Nuevo</h3>
                         <p>pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                       </div>
                    </div>
                </div>
                <div class='col-md-4 col-sm-6 col-xs-12'>
                    <div class='full Nuevo_blog'>
                        <img class='img-responsive' src='style/images/b2.png' alt='#' />
                        <div class='overlay'><a class='main_bt transparent' href='#'>View</a></div>
                       <div class='blog_details'>
                         <h3>Ethereum Nuevo</h3>
                         <p>pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                       </div>
                    </div>
                </div>
                <div class='col-md-4 col-sm-6 col-xs-12'>
                    <div class='full Nuevo_blog'>
                        <img class='img-responsive' src='style/images/b3.png' alt='#' />
                        <div class='overlay'><a class='main_bt transparent' href='#'>View</a></div>
                       <div class='blog_details'>
                         <h3>Light Nuevo</h3>
                         <p>pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                       </div>
                    </div>
                </div>
                <div class='col-md-4 col-sm-6 col-xs-12'>
                    <div class='full Nuevo_blog'>
                       <img class='img-responsive' src='style/images/b1.png' alt='#' />
                       <div class='overlay'><a class='main_bt transparent' href='#'>View</a></div>
                       <div class='blog_details'>
                         <h3>Bitcoin Nuevo</h3>
                         <p>pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                       </div>
                    </div>
                </div>
                <div class='col-md-4 col-sm-6 col-xs-12'>
                    <div class='full Nuevo_blog'>
                        <img class='img-responsive' src='images/b2.png' alt='#' />
                        <div class='overlay'><a class='main_bt transparent' href='#'>View</a></div>
                       <div class='blog_details'>
                         <h3>Ethereum Nuevo</h3>
                         <p>pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                       </div>
                    </div>
                </div>
                <div class='col-md-4 col-sm-6 col-xs-12'>
                    <div class='full Nuevo_blog'>
                        <img class='img-responsive' src='style/images/b3.png' alt='#' />
                        <div class='overlay'><a class='main_bt transparent' href='#'>View</a></div>
                       <div class='blog_details'>
                         <h3>Light Nuevo</h3>
                         <p>pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                       </div>
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