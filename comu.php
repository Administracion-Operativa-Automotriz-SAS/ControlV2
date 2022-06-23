<?php
/*código php... */
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-type: text/html; charset=utf-8');

echo utf8_encode("

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
  <head>
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <meta name='x-apple-disable-message-reformatting' />
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <title></title>
    <style type='text/css' rel='stylesheet' media='all'>
    /* Base ------------------------------ */
    
    @import url('https://fonts.googleapis.com/css?family=Nunito+Sans:400,700&display=swap');
    body {
      width: 100% !important;
      height: 100%;
      margin: 0
   
      -webkit-text-size-adjust: none;
    }
    
    a {
      color: #3869D4;
    }
    
    a img {
      border: none;
    }
    
    td {
      word-break: break-word;
    }
    
    .preheader {
      display: none !important;
      visibility: hidden;
      mso-hide: all;
      font-size: 1px;
      line-height: 1px;
      max-height: 0;
      max-width: 0;
      opacity: 0;
      overflow: hidden;
    }
    /* Type ------------------------------ */
    
    body,
    td,
    th {
      font-family: 'Nunito Sans', Helvetica, Arial, sans-serif;
    }
    
    h1 {
      margin-top: 0;
      color: #333333;
      font-size: 22px;
      font-weight: bold;
      text-align: left;
    }
    
    h2 {
      margin-top: 0;
      color: #333333;
      font-size: 16px;
      font-weight: bold;
      text-align: left;
    }
    
    h3 {
      margin-top: 0;
      color: #333333;
      font-size: 14px;
      font-weight: bold;
      text-align: left;
    }
    
    td,
    th {
      font-size: 16px;
    }
    
    p,
    ul,
    ol,
    blockquote {
      margin: .4em 0 1.1875em;
      font-size: 16px;
      line-height: 1.625;
    }
    
    p.sub {
      font-size: 13px;
    }
    /* Utilities ------------------------------ */
    
    .align-right {
      text-align: right;
    }
    
    .align-left {
      text-align: left;
    }
    
    .align-center {
      text-align: center;
    }
    /* Buttons ------------------------------ */
    
    .button {
      background-color: #3869D4;
      border-top: 10px solid #3869D4;
      border-right: 18px solid #3869D4;
      border-bottom: 10px solid #3869D4;
      border-left: 18px solid #3869D4;
      display: inline-block;
      color: #FFF;
      text-decoration: none;
      border-radius: 3px;
      box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
      -webkit-text-size-adjust: none;
      box-sizing: border-box;
    }
    
    .button--green {
      background-color: #22BC66;
      border-top: 10px solid #22BC66;
      border-right: 18px solid #22BC66;
      border-bottom: 10px solid #22BC66;
      border-left: 18px solid #22BC66;
    }
    
    .button--red {
      background-color: #FF6136;
      border-top: 10px solid #FF6136;
      border-right: 18px solid #FF6136;
      border-bottom: 10px solid #FF6136;
      border-left: 18px solid #FF6136;
    }
    
    @media only screen and (max-width: 500px) {
      .button {
        width: 100% !important;
        text-align: center !important;
      }
    }
    /* Attribute list ------------------------------ */
    
    .attributes {
      margin: 0 0 21px;
    }
    
    .attributes_content {
      background-color: #F4F4F7;
      padding: 16px;
    }
    
    .attributes_item {
      padding: 0;
    }
    /* Related Items ------------------------------ */
    
    .related {
      width: 100%;
      margin: 0;
      padding: 25px 0 0 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
    }
    
    .related_item {
      padding: 10px 0;
      color: #CBCCCF;
      font-size: 15px;
      line-height: 18px;
    }
    
    .related_item-title {
      display: block;
      margin: .5em 0 0;
    }
    
    .related_item-thumb {
      display: block;
      padding-bottom: 10px;
    }
    
    .related_heading {
      border-top: 1px solid #CBCCCF;
      text-align: center;
      padding: 25px 0 10px;
    }
    /* Discount Code ------------------------------ */
    
    .discount {
      width: 100%;
      margin: 0;
      padding: 24px;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      background-color: #F4F4F7;
      border: 2px dashed #CBCCCF;
    }
    
    .discount_heading {
      text-align: center;
    }
    
    .discount_body {
      text-align: center;
      font-size: 15px;
    }
    /* Social Icons ------------------------------ */
    
    .social {
      width: auto;
    }
    
    .social td {
      padding: 0;
      width: auto;
    }
    
    .social_icon {
      height: 20px;
      margin: 0 8px 10px 8px;
      padding: 0;
    }
    /* Data table ------------------------------ */
    
    .purchase {
      width: 100%;
      margin: 0;
      padding: 35px 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
    }
    
    .purchase_content {
      width: 100%;
      margin: 0;
      padding: 25px 0 0 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
    }
    
    .purchase_item {
      padding: 10px 0;
      color: #51545E;
      font-size: 15px;
      line-height: 18px;
    }
    
    .purchase_heading {
      padding-bottom: 8px;
      border-bottom: 1px solid #EAEAEC;
    }
    
    .purchase_heading p {
      margin: 0;
      color: #85878E;
      font-size: 12px;
    }
    
    .purchase_footer {
      padding-top: 15px;
      border-top: 1px solid #EAEAEC;
    }
    
    .purchase_total {
      margin: 0;
      text-align: right;
      font-weight: bold;
      color: #333333;
    }
    
    .purchase_total--label {
      padding: 0 15px 0 0;
    }
    
    body {
      background-color: #F4F4F7;
      color: #51545E;
    }
    
    p {
      color: #51545E;
    }
    
    p.sub {
      color: #6B6E76;
    }
    
    .email-wrapper {
      width: 100%;
      margin: 0;
      padding: 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      background-color: #F4F4F7;
    }
    
    .email-content {
      width: 100%;
      margin: 0;
      padding: 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
	  
    }
    /* Masthead ----------------------- */
    
    .email-masthead {
      padding: 25px 0;
      text-align: center;
    }
    
    .email-masthead_logo {
      width: 94px;
    }
    
    .email-masthead_name {
      font-size: 16px;
      font-weight: bold;
      color: #A8AAAF;
      text-decoration: none;
      text-shadow: 0 1px 0 white;
    }
    /* Body ------------------------------ */
    
    .email-body {
      width: 100%;
      margin: 0;
      padding: 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
     background: transparent url(https://www.creativografico.dev/webAOA/wp-content/uploads/2019/10/fondo-intranet-AOA-Colombia.jpg) repeat center top;
    background-size: auto;
    }
    
    .email-body_inner {
      width: 570px;
      margin: 0 auto;
      padding: 0;
      -premailer-width: 570px;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      background-color: #FFFFFF;
    }
	
    
    .email-footer {
      width: 570px;
      margin: 0 auto;
      padding: 0;
      -premailer-width: 570px;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      text-align: center;
    }
    
    .email-footer p {
      color: #6B6E76;
    }
    
    .body-action {
      width: 100%;
      margin: 30px auto;
      padding: 0;
      -premailer-width: 100%;
      -premailer-cellpadding: 0;
      -premailer-cellspacing: 0;
      text-align: center;
    }
    
    .body-sub {
      margin-top: 25px;
      padding-top: 25px;
      border-top: 1px solid #EAEAEC;
    }
    
    .content-cell {
      padding: 35px;
    }
    /*Media Queries ------------------------------ */
    
    @media only screen and (max-width: 600px) {
      .email-body_inner,
      .email-footer {
        width: 100% !important;
      }
    }
    
    @media (prefers-color-scheme: dark) {
      body,
      .email-body,
      .email-body_inner,
      .email-content,
      .email-wrapper,
      .email-masthead,
      .email-footer {
        background-color: #333333 !important;
        color: #FFF !important;
      }
      p,
      ul,
      ol,
      blockquote,
      h1,
      h2,
      h3 {
        color: #FFF !important;
      }
      .attributes_content,
      .discount {
        background-color: #222 !important;
      }
      .email-masthead_name {
        text-shadow: none !important;
      }
    }
    </style>
    <!--[if mso]>
    <style type='text/css'>
      .f-fallback  {
        font-family: Arial, sans-serif;
      }
    </style>
  <![endif]-->
  </head>
  <body>
    <table class='email-wrapper' width='100%' cellpadding='0' cellspacing='0' role='presentation'>
      <tr>
        <td align='center'>
          <table class='email-content' width='100%' cellpadding='0' cellspacing='0' role='presentation'>
            <tr>
              <td class='email-masthead'>
                <a href='https://example.com' class='f-fallback email-masthead_name'>
                <img src='js/banner-vehiculo-sustituto-AOA.jpg' > 
              </a>
              </td>
            </tr>
            <!-- Email Body -->
            <tr>
              <td class='email-body' width='100%' cellpadding='0' cellspacing='0'>
                <table class='email-body_inner' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
                  <!-- Body content -->
                  <tr>
                    <td class='content-cell'>
                      <div class='f-fallback'>
                       
                        <br>
                        <br>
                        <table class='attributes' width='100%' cellpadding='0' cellspacing='0' role='presentation'>
                          <tr>
                            <td class='attributes_content'>
                              <table width='100%' cellpadding='0' cellspacing='0' role='presentation'>
                            Con el tempo estimado la cuarentena obligatoria en Colombia. y el poco o cero uso de tu vehículo en estos días, es necesario que le prestes atención. El hecho de que esté detenido también exigen de ciertos cuidados para que siga funcionando. 
                              </table>
                            </td>
                          </tr>
                        </table>
                       
                        <p>  5 pasos básicos para el cuidado del vehículo de reemplazo. </p>
						<table class='attributes' width='100%' cellpadding='0' cellspacing='0' role='presentation'>
                          <tr>
                            <td class='attributes_content'>
                              <table width='100%' cellpadding='0' cellspacing='0' role='presentation'>
									1.	Prender el vehículo al menos una vez por semana, dejarlo al ralentí de 5 a 10 minutos sin acelerar.
                              </table>
							   </tr>
							   <tr>
                            </td>
							 <td class='attributes_content'>
                              <table width='100%' cellpadding='0' cellspacing='0' role='presentation'>
							     	2.	Chequear los niveles de aceite, líquido de frenos, refrigerante y aceite de dirección si aplica. 
							 </table>
                            </td>
                          </tr>
						     <tr>
                            </td>
							 <td class='attributes_content'>
                              <table width='100%' cellpadding='0' cellspacing='0' role='presentation'>
							    3.	Si es posible, calibrar la presión de los neumáticos, en algunos casos, cuando el vehículo está inmovilizado por varios días, la presión de las ruedas baja.
							 </table>
                            </td>
                          </tr>
						     <tr>
                            </td>
							 <td class='attributes_content'>
                              <table width='100%' cellpadding='0' cellspacing='0' role='presentation'>
                                     4.	Procure mover el vehículo, aunque sea pocos metros en el parqueadero o garaje, esto ayudará a evitar que se deformen los reumáticos.  							 </table>
                            </td>
                          </tr>
						     <tr>
                            </td>
							 <td class='attributes_content'>
                              <table width='100%' cellpadding='0' cellspacing='0' role='presentation'>
						     	  5.	Cuando el vehículo esté encendido por un par de minutos encienda las luces, encienda el radio y algunos componentes electrónicos, esto ayudara a que exista un flujo de corriente en el sistema, activa la batería y los sistemas.  
							 </table>
                            </td>
                          </tr>
                        </table>
                      </div>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td>
                <table class='email-footer' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
                  <tr>
                    <td class='content-cell' align='center'>
                      <img src='js/logo-footer-mail-AOA.jpg' > 
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>"
)

?>