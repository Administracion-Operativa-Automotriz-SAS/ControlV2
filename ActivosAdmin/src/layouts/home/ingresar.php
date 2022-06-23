 <?php
/*código php... */
header('Content-Type: text/html; charset=utf-8');



 $HTML = "
 
 template>
  <v-app>

    <!-- Start Banner -->
    <div class='section inner_page_banner'>
        <div class='container'>
            <div class='row'>
                <div class='col-md-12'>
                    <div class='banner_title'>
                        <h3>Registrate</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Banner -->
    
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
                           <h2><span class='theme_color'>Registra</span> tus activos</h2>    
                        </div>
                    </div>
                    <div class='full paddding_left_15'>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                    </div>
                    <div class='full paddding_left_15'>
                        <a class='main_bt' href='#'>Registrarte></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
 
 ";

   echo html_entity_decode($HTML, ENT_NOQUOTES, "UTF-8");


?>