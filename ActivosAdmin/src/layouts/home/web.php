<?php



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
    <v-toolbar-title>  &nbsp  &nbsp  &nbsp  &nbsp  AOA COLOMBIA {{tokenUser}} </v-toolbar-title>

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
			
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title>{{tokenNick}}</v-list-item-title>
              <v-list-item-subtitle>{{tokenUser}}</v-list-item-subtitle>
			  <v-list-item-subtitle>{{tokenPeril}}</v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action>
            
                <v-icon>mdi-heart</v-icon>
         
            </v-list-item-action>
          </v-list-item>
        </v-list>

        <v-divider></v-divider>

        <v-list>
    
        </v-list>|

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn text @click='menu = false'>Salir</v-btn>
          <v-btn color='primary' text  v-on:click='cerrarSession()' >Cerrar Session</v-btn>
        </v-card-actions>
      </v-card>
    </v-menu>
	  
	  
	  
</v-app-bar>

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
		

      
          <v-btn
            color='primary'
            text
            @click='word = !word'
          >
           Salir
          </v-btn>

      </v-alert>
    </v-dialog>

 <v-overlay  :absolute='absolute'
          :value='overlay' >
      <v-progress-circular indeterminate size='64'></v-progress-circular>
    </v-overlay>


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
		   <div class='subheading'>{{tokenPeril}}</div>
		   	<div class='subheading'>{{tokenUser}}</div>
		    <div class='subheading'>{{tokenNick}}</div>
          </v-col>
        </v-row>
      </v-img>

      <v-list>
	   <v-divider></v-divider>

  <v-list>
      <v-list-group
        v-for='item in items'
        :key='item.title'
        v-model='item.active'
        :prepend-icon='item.icon'
        no-action
      >
        <template v-slot:activator>
          <v-list-item-content>
            <v-list-item-title v-text='item.title'></v-list-item-title>
          </v-list-item-content>
        </template>

        <v-list-item
          v-for='subItem in item.items'
          :key='subItem.title'
          link
          router
		  :prepend-icon='subItem.icon'
          :to='subItem.to'
        >
		
          <v-list-item-content>
            <v-list-item-title v-text='subItem.title'></v-list-item-title>
          </v-list-item-content>
		   <v-list-item-icon>
              <v-icon v-text='subItem.icon'></v-icon>
          </v-list-item-icon>
        </v-list-item>
      </v-list-group>
    </v-list>

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
          <v-btn block v-on:click='cerrarSession()' >Cerrar sesión</v-btn>
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
	<div class='subheading'>{{ tokenNick}}</div>
	 <div class='subheading'>{{tokenUser}}</div>
            <div class='subheading'>{{tokenPeril}}</div>
    </v-alert>
		

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color='primary'
            text
            @click='dialog1 = !dialog1'
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
        {{ new Date().getFullYear()  }} — <strong>AOA COLOMBIA</strong>
      </v-col>
  </v-footer>
      </v-content>
    </v-app>


</template>
<script type='module' src='src/layouts/web.js'></script>



";

   echo html_entity_decode($HT, ENT_NOQUOTES, "UTF-8");


?>