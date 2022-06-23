<?php
header('Content-Type: text/html; charset=utf-8');


 $HT = "
 
 
<template>


  <v-app>
  
    <v-app-bar
	   
      color='grey darken-3'
      dark
    >
      <v-app-bar-nav-icon @click='drawer = true'></v-app-bar-nav-icon>
       <v-toolbar-title >
      <img  v-text='title' white='180' height='50'  src='https://app.aoacolombia.com/Administrativo/img/banner-vehiculo-sustituto-AOA.jpg' >
     </v-toolbar-title>
    <v-toolbar-title>  &nbsp  &nbsp  &nbsp  &nbsp  AOA COLOMBIA  </v-toolbar-title>

      <v-spacer></v-spacer>
	
	 <v-menu
      v-model='menu'
      :close-on-content-click='false'
      offset-x
    >
      <template v-slot:activator='{ on }'>
          <v-btn icon v-on='on'>
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>

      <v-card>
        <v-list>
          <v-list-item>
            <v-list-item-avatar>
              <img src='https://cdn.vuetifyjs.com/images/john.jpg' alt='John'>
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title>{{pintarhtml['Nombre'] }}</v-list-item-title>
              <v-list-item-subtitle>{{pintarhtml['Nick']}}</v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action>
              <v-btn
                :class='fav ? 'red--text' : '''
                icon
                @click='fav = !fav'
              >
                <v-icon>mdi-heart</v-icon>
              </v-btn>
            </v-list-item-action>
          </v-list-item>
        </v-list>

        <v-divider></v-divider>

        <v-list>
          <v-list-item>
            <v-list-item-action>
              <v-switch v-model='message' color='purple'></v-switch>
            </v-list-item-action>
            <v-list-item-title>Buscar Requisición </v-list-item-title>
          </v-list-item>

          <v-list-item>
            <v-list-item-action>
              <v-switch v-model='hints' color='purple'></v-switch>
            </v-list-item-action>
            <v-list-item-title>Bucar siniestro</v-list-item-title>
          </v-list-item>
        </v-list>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn text @click='menu = false'>Salir</v-btn>
          <v-btn color='primary' text @click='menu = false'>Enviar</v-btn>
        </v-card-actions>
      </v-card>
    </v-menu>
	  
	  
	  
</v-app-bar>






  <v-navigation-drawer
      v-model='drawer'
      absolute
      temporary
	 
	   expand-on-hover
      :mini-variant='mini'

      dark
      temporary
    >
	
	  
  <v-img :aspect-ratio='16/9'
        src='http://www.aoacolombia.com/img/destacados/redimension_4.jpg?d6f177'>
        <v-row align='end' class='lightbox white--text pa-2 fill-height'>
          <v-col>
		         <v-list-item class='px-2'>
              <v-list-item-avatar>
                <v-img src='https://randomuser.me/api/portraits/women/85.jpg'>
				</v-img>
              </v-list-item-avatar>
            </v-list-item>
            <div class='subheading'>{{pintarhtml['Nick']}}</div>
		   <div class='subheading'>{{pintarhtml['Tabla_usuario']}}</div>
          </v-col>
        </v-row>
      </v-img>

      <v-list>
	

        <template v-for='(item, i) in sesion'>
          <v-divider v-if='item.divider' :key='i'></v-divider>
          <v-list-item v-else :key='item.Nick' @click>
            <v-list-item-action>
              <v-icon>mdi-account</v-icon>
            </v-list-item-action>
            <v-list-item-title>{{ item.Nick }}</v-list-item-title>
          </v-list-item>
        </template>
      </v-list>
	     <template v-slot:append>
        <div class='pa-2'>
          <v-btn block>Cerrar sesión</v-btn>
        </div>
      </template>
    </v-navigation-drawer>
	
 <v-container  v-if='notSecion' dark class='fill-height'>
      <v-row
        align='center'
        justify='center'
      >
	  <v-dialog
      v-model='dialog'
      width='500'
    >
     

      <v-card>
        <v-card-title
          class='headline grey lighten-2'
          primary-title
        >
          Control Novedad
        </v-card-title>

        <v-card-text>
        </v-card-text>

        
		<v-alert
      border='left'
     
	  prominent
	  color='red'
      type='error'
      elevation='8'
    >
	Bebes ingresar a Control !!
    </v-alert>
		

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color='primary'
            text
			v-on:click='ingresar'
          >
           Ingresar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
	   
<v-alert
      prominent
      type='error'
    >
      <v-row align='center'>
        <v-col class='grow'>Bebes ingresar a control!!</v-col>
        <v-col class='shrink'>
          <v-btn  v-on:click='ingresar'>Ingresar</v-btn>
        </v-col>
      </v-row>
    </v-alert>
      </v-row>
    </v-container>



        

  
    <v-container  v-if='!notSecion' dark class='fill-height'>
	
	

      
<v-dialog
      v-model='dialog1'
      width='500'
    >
     

      <v-card>
        <v-card-title
          class='headline grey lighten-2'
          primary-title
        >
          Control Novedad
        </v-card-title>

        <v-card-text>
        </v-card-text>

        
		<v-alert
      border='top'
     
	  prominent
      type='info'
      elevation='8'
    >
	Bienvevido a novedad !!
	<div class='subheading'>{{pintarhtml['Nombre'] }}</div>
            <div class='subheading'>{{pintarhtml['Nick']}}</div>
    </v-alert>
		

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color='primary'
            text
          >
           Ingresar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
   
   
   <div class='row justify-content-center align-items-center'>

  	<router-view></router-view>
  
	   </div>
   
    </v-container>



         <v-footer
    dark
    padless
	
	  class='font-weight-medium'
    >
      <v-col
        class='text-center'
        cols='12'
      >
        {{ new Date().getFullYear() }} — <strong>AOA COLOMBIA</strong>
      </v-col>
  </v-footer>
      </v-content>
    </v-app>


</template>
<script type='module' src='src/layouts/web.js'></script>



";

   echo html_entity_decode($HT, ENT_NOQUOTES, "UTF-8");


?>