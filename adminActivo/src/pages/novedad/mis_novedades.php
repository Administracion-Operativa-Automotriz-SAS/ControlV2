
<?php

 $HThome = "
<template>

  
<div class='content'>
   <v-col cols='12'  class='row justify-content-center align-items-center' sm='12' md='12'>
 <b-container fluid>
        <!-- User Interface controls -->
        <b-row>
		   <v-col cols='12'  class='row justify-content-center align-items-center' sm='12' md='12'>
        <v-chip
      class='ma-2'
      color='green'
      text-color='white'
    >
      <v-avatar
        left
        class='green darken-4'
		
      >
        <v-icon>mdi-account-circle</v-icon>
      </v-avatar>
    <h5>Mis Novedades </h5>
    </v-chip>
        </v-col>
            <b-col md='6' class='my-1'>
                <b-form-group label-cols-sm='3' label='Filtrar'  bordered fixed class=' form-control mb-0'>
                    <b-input-group>
                        <b-form-input v-model='filter' placeholder='Escribe para buscar'></b-form-input>
                        <b-input-group-append>
                         <b-button :disabled='!filter' @click='filter = NULL'>Cancelar </b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-col>

          

         

            <b-col md='6' class='my-1'>
                <b-form-group label-cols-sm='3' label='Por página' class='mb-0'>
                    <b-form-select v-model='perPage' :options='pageOptions'></b-form-select>
                </b-form-group>
            </b-col>
        </b-row>
 <b-table
		         empty-filtered-text='No hay registros que coincidan con su solicitud' mod='ioweb_slocator'
                show-empty 
                striped hover
                stacked='md'
                :items='encargado_novedad_array'
                :fields='encargado_novedad_array_labe'
                :current-page='currentPage'
                :per-page='perPage'
                :filter='filter'
                :sort-by.sync='sortBy'
                :sort-desc.sync='sortDesc'
                :sort-direction='sortDirection'
                @filtered='onFiltered'
        >
       
	     <template v-slot:cell(tipo_cierre)='{ detailsShowing, item }' >
        <h6 v-if='item.tipo_cierre == 1'>
				En proceso
        </h6>
      </template> 
	  
	  <template v-slot:cell(opciones)='{ detailsShowing, item }' >
  		 <b-button variant='outline-primary' @click='ingresar_novedad(item)'>Cerrar novedad</b-button>
		  &nbsp
		 
		  &nbsp
		 <v-icon
        small
        class='mr-2'
        @click='editarCotizacion(item)'
      >
        mdi-pencil
      </v-icon>
      <v-icon
        small
        @click='eliminarCotizacion(item)'
      >
        mdi-delete
      </v-icon>
      </template> 
	  
           
        </b-table>
		</v-col>
</div>
			
			
			</template>

  <script type='module' src='src/pages/novedad/mis_novedades.js'></script>

";

   echo html_entity_decode($HThome, ENT_NOQUOTES, "UTF-8");


?>