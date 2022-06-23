
<?php

 $HThome = "

<template>
 
 


		 
   <div class='row justify-content-center align-items-center'>
 <el-form    v-if='sele' >
 	 <h6 class='row justify-content-center align-items-center'>Tipo de modalidad </h6>
  			<el-select
				v-model='val'
				 v-model='val'    v-if='sele'
			  @change='printValue()'
				allow-create
				placeholder='Selecion modalidad '>
				<el-option
				  :remote-method='printValue'
				  v-for='item in pintar'
				  :key='item'
				  :label='item'
				  :value='item'>
				</el-option>
			  </el-select>
	      </el-form>


	
	   <form-wizard @on-complete='onComplete'  v-if='sinistro' 
   title='Ingresar novedad Siniestro' ref='wizard'
   :start-index.sync='activeTabIndex'
   subtitle='Busca el vehiculo por la Siniestro '
   step-size='sm'   shape='circle'   color='gray'  error-color='#ff4949'>

    <tab-content title='Buscar sinestro ' icon='ti-user' 
	:before-change='validateFirstStep'>
      <el-form :inline='true'  id='miForm' 
		:model='formInline' class='demo-form-inline' 
		:rules='rules' ref='ruleForm'>
		
		   <h6 class='row justify-content-center align-items-center'>Buscar vehiculo por Siniestro  </h6>
		   <br>
         <el-form-item label='Siniestro' prop='user'>
          <el-input v-model='formInline.user' 
		  placeholder='Ingresa el Siniestro'></el-input>
        </el-form-item>
		
      </el-form>
    </tab-content>
	
<tab-content icon='fa fa-check-square-o' title='Ingresar novedad' 
:before-change='validateSines'>
 
 <el-form  v-if='activeCall'  :inline='true' :model='formInlin' class='demo-form-inline' :rules='rule' ref='ruleFocall'>
      <h6 class='row justify-content-center align-items-center'>Ingresar novedad del Siniestro &nbsp {{formInline.user}}</h6>
	     <br>
		 
		 <el-form-item    label='Reportado por' prop='reportado'>
          <el-input v-model='formInlin.reportado'  :disabled='disableInputBool'  type='textarea'  placeholder='Ingresar reporte por'></el-input>
        </el-form-item>
		<el-form-item  label='telefono reporte' prop='ciudad_reporte'>
          <el-input  :disabled='disableInputBool'  v-model='formInlin.tele_reporte'  type='textarea'  placeholder='Ingresar telefono de reporte '></el-input>
        </el-form-item>
		<br>
		<el-form-item  label='Ciudad de reporte ' prop='ciudad_reporte'>
          <el-input v-model='formInlin.ciudad_reporte'   :disabled='disableInputBool'   type='textarea'  placeholder='Ingresar ciudad de resporte'></el-input>
        </el-form-item>
				<el-form-item  label='Email de reporte ' prop='email_reporte'>
          <el-input v-model='formInlin.email_reporte'   :disabled='disableInputBool'  type='textarea'  placeholder='Ingresar email de resporte'></el-input>
        </el-form-item>
                   <br>
	  <el-form-item  label='Novedad' prop='user'>
          <el-input v-model='formInlin.user'  type='textarea'  placeholder='Ingresar Novedad'></el-input>
        </el-form-item>
		
        <el-form-item label='Tipo de novedad' prop='region'>
          <el-select
					class=' mr-sm-2 align-items-center' 
					 v-model='formInlin.region'
					filterable
					allow-create
					placeholder='Selecione un tipo de novedad '>
					<el-option
					  v-for='item in tipo'
					  :key='item.id'
					  :label='item.nombre'
					  :value='item.id'>
			</el-option>
			
		  </el-select>
		  
        </el-form-item>
		 <br>
       <el-form-item  label='Cerrar novedad' prop='checkState'>
    	<br>
		<el-checkbox-group v-model='formInlin.checkState'
		fill='#2A9AB7' size='small' text-color='#ffffff' @change='handleChange'>
				<el-checkbox  false-label='null' true-label='true' name='state'>La puede cerrar </el-checkbox>
				<el-checkbox  false-label='null' true-label='false' name='state'>No la puede cerrar </el-checkbox>
	   </el-checkbox-group>
	   
	  
	     </el-form-item>
		  <br>
		  <el-form-item v-if='usuarioencargado' label='Encargado ' >
		  
          <el-select
					class=' mr-sm-2 align-items-center' 
					 v-model='formInlin.encargado'
					filterable
					allow-create
					  @change='buscar_encargado()'
					placeholder='Selecione un encargado '>
					<el-option
					  v-for='item in usuarioEncargado_array'
					  :key='item.id'
					  :label='item.nombre'
					  :value='item.id'>
			</el-option>
			
		  </el-select>
		  </el-form-item>
		  
		  
		    <br> 
			
		   <el-form-item   v-if='descriCall'   label='Descripción de novedad ' prop='cierre'>
          <el-input v-model='formInlin.cierre'  type='textarea'  placeholder='Ingresar Novedad'></el-input>
        </el-form-item>
		
      </el-form>
		
		
		    <br>
        <div class='row'>
    <div class='col text-center'>
	  			<el-button  v-if='activeCall'  class='text-center' 
				 @click='validateSinesCalll'
				type='primary'  plain>Cerrar caso</el-button>

    </div>
  </div>
      </el-form>
	  
	  
	  
	  <el-form  v-if='!activeCall'  :inline='true' :model='formInlin' class='demo-form-inline' :rules='rule' ref='ruleFo'>
      <h6 class='row justify-content-center align-items-center'>Ingresar novedad del Siniestro &nbsp {{formInline.user}}</h6>
	     <br>
	  <el-form-item  label='Novedad' prop='user'>
          <el-input v-model='formInlin.user'  type='textarea'  placeholder='Ingresar Novedad'></el-input>
        </el-form-item>
		
        <el-form-item label='Tipo de novedad' prop='region'>
          <el-select
					class=' mr-sm-2 align-items-center' 
					 v-model='formInlin.region'
					filterable
					allow-create
					placeholder='Selecione un tipo de novedad '>
					<el-option
					  v-for='item in tipo'
					  :key='item.id'
					  :label='item.nombre'
					  :value='item.id'>
			</el-option>
			
		  </el-select>
		  
        </el-form-item>
		 
      </el-form>

    </tab-content>
	  <tab-content title='Agregar cotización' icon='ti-check' :before-change='validateCotisacion'>
	   <el-form :inline='true' :model='formCont'  id='formCo'
	   class='demo-form-inline' :rules='rulesCon' ref='formCo'>
      <h6 class='row justify-content-center align-items-center'>Ingresar cotización de la novedad  {{novedadId}}</h6>
       <br>
		<el-form-item label='Poveedor' prop='proveedor'>
               <el-select
				title='Poveedor'
					class=' mr-sm-2 align-items-center' 
					filterable
					
					allow-create
				v-model='formCont.proveedor'
					filterable
					allow-create
                    placeholder='Selecione un tipo  proveedor'>
                  <el-option
                      	  v-for='item in proveedor'
					  :key='item.id'
					  :label='item.nombre'
					  :value='item.id'>
                </el-option>
            </el-select>
		  </el-form-item>
		  
		 <el-form-item  label='Descripción' prop='descripcion'>
          <el-input type='textarea' v-model='formCont.descripcion' placeholder='Ingresar  descripción'></el-input>
        </el-form-item>
		<br>
		 <el-form-item   label='Actividad Solicitante ' prop='actividad_solitante'>
          <el-input type='textarea'    v-model='formCont.actividad_solitante' placeholder='Ingresar actividad solicitamte'></el-input>
        </el-form-item>
		 <el-form-item   label='Actividad  Proveedor' prop='actividad_provedor'>
          <el-input type='textarea'   v-model='formCont.actividad_provedor' placeholder='Ingresar actividad proveedor'></el-input>
        </el-form-item>

      </el-form>

	  </tab-content>
  <tab-content title='Imprimir pdf' icon='ti-check'>

<v-tabs
  color='cyan'
  darken-4
  fixed-tabs
  slider-color='yellow'
  v-model='tab'  
   icons-and-text
      class='elevation-2'
>
<v-tabs-slider></v-tabs-slider>

          <v-tab
            ripple
            class='primary--text'>
            Ingresar detalles de la cotización  
        <v-icon>fact_check</v-icon>
        </v-tab>
	  
	  
          <v-tab
            ripple
            class='primary--text'>
          
             Ver novedad
        <v-icon>fact_check</v-icon>
      </v-tab>

          <v-tab
           ripple
            class='primary--text'
          >
		  
			  Imprimir novedad
            <v-icon>picture_as_pdf</v-icon>
          </v-tab>

          <v-tab
            ripple
            class='primary--text'
          >
		    Mis cotizaciones
            <v-icon>exposure_plus_1</v-icon>
			
          </v-tab>
		  
		    <v-tab-item>

		        <v-col cols='12'  class='row justify-content-center align-items-center' sm='12' md='12'>
		 

 <b-container fluid>
        <!-- User Interface controls -->
        <b-row>
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
                <b-form-group label-cols-sm='3' label='Sort' class='mb-0'>
                    <b-input-group>
                        <b-form-select v-model='sortBy' :options='sortOptions'>
                            <option slot='first' :value='null'>-- Nombre --</option>
                        </b-form-select>
                        <b-form-select v-model='sortDesc' :disabled='!sortBy' slot='append'>
                            <option :value='false'>Ascendente </option> <option :value='true'>Descendente</option>
                        </b-form-select>
                    </b-input-group>
                </b-form-group>
            </b-col>

            <b-col md='6' class='my-1'>
              <v-btn color='primary' @click='dialogConfi = true' >Nueva Contización </v-btn>

            </b-col>

            <b-col md='6' class='my-1'>
                <b-form-group label-cols-sm='3' label='Por página' class='mb-0'>
                    <b-form-select v-model='perPage' :options='pageOptions'></b-form-select>
                </b-form-group>
            </b-col>
        </b-row>

        <!-- Main table element -->
        <b-table
		         empty-filtered-text='No hay registros que coincidan con su solicitud' mod='ioweb_slocator'
                show-empty 
                striped hover
                stacked='md'
                :items='cotizacion_novedad_array'
                :fields='cotizacion_novedad_array_labe'
                :current-page='currentPage'
                :per-page='perPage'
                :filter='filter'
                :sort-by.sync='sortBy'
                :sort-desc.sync='sortDesc'
                :sort-direction='sortDirection'
                @filtered='onFiltered'
        >
        <template v-slot:cell(requision)='{ detailsShowing, item }' >
         <b-btn v-if='solicitar'  :disabled='pinta_solisitud == 1'   @click='solicitar_apro_req(item)'>Solicitar</b-btn>
		 <b-btn v-if='!solicitar'   :disabled='pinta_solisitud == 1'  @click='solicitar_apro_req(item)'>Convertir</b-btn>
       </template>
           <template v-slot:cell(detalle)='{ detailsShowing, item }' >
        <b-btn @click='buscar_detalle_id(item)'>{{ detailsShowing ? 'Cerrar' : 'Ver'}} Detalle</b-btn>
      </template>
	  <template slot='empty'>
		  <span class='text-center' aria-live='off'>No hay registros para mostra</span>
		</template>
	  
	   <template v-slot:cell(proveedor)='{ detailsShowing, item }' >
        <b-btn @click='toggleDetails(item)'>{{ detailsShowing ? 'Cerrar ' : 'Ver'}} Provedor</b-btn>
      </template> 
	  <template v-slot:cell(opciones)='{ detailsShowing, item }' >
  		 <b-button @click='ingresa_cotizacion(item)'>Ingresar detalle</b-button>
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
	 
	 
      <template v-slot:row-details='{ item }'>
        <b-card v-if='!ver_tabla'>
          <b-table :items='buscar_proveedor_id_array'
		 :fields='proveedor_labe' bordered fixed>
		  </b-table>
        </b-card>
		  <b-card v-if='ver_tabla'>
          <b-table :items='detalle_id_array'
		 :fields='detalle_labe' bordered fixed>
		  </b-table>
        </b-card>
      </template>
	  
	  
           
        </b-table>

        <b-row>
            <b-col md='6' class='my-1'>
                <b-pagination
                        v-model='currentPage'
                        :total-rows='totalRows'
                        :per-page='perPage'
                        class='my-0'
                ></b-pagination>
            </b-col>
        </b-row>

        <!-- Info modal -->
        <b-modal id='modal-info' @hide='resetModal' :title='modalInfo.title' ok-only>
            <pre>{{ modalInfo.content }}</pre>
        </b-modal>
    
    <b-modal
      v-model='show'
      title='  Ingresar detalle de cotización '
      header-bg-variant='dark'
      header-text-variant='light'
      body-bg-variant='light'
      body-text-variant='dark'
      footer-bg-variant='dark'
      footer-text-variant='light'
    >
	
	
      <b-container fluid>
	  
	    <el-form :inline='true' :model='formformItenes' 
		   class='demo-form-inline'  
			class=' justify-content-center align-items-center'
		   :rules='rulesformItenes' ref='forItenes'>
				   
				   
  

        <b-row class='mb-1'>
          <b-col >
		  <el-form-item label='Tipo de bien' prop='tipoBien'>
						     <el-select
							   title='	Tipo de  bien'
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.tipoBien'
							  @change='tipo_biien()'
								allow-create
								placeholder='Selecion modalidad '>
								<el-option
						
								  v-for='item in bien'
								  :key='item.id'
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
								</el-form-item>
		  
		  </b-col>
          <b-col>
            		<el-form-item label='Clase de Requisición'  prop='clase'>	  
							   <el-select
							   title='Clase de Requisición'
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.clase'
								allow-create
								placeholder='Selecion clase '>
								<el-option
								v-for='item in clase'
                                 :key='item.id'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
          </b-col>
          <b-col>
            						<el-form-item label='Tipo de Item'   prop='tipoItem'>	  
							   <el-select
							   title='Tipo de Item'
							 :disabled='disabled == 1' 
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.tipoItem'
								allow-create
								placeholder='Selecion modalidad '>
								<el-option
								v-for='(item, index) in servicioList'
                                 :key='index'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
							  
					  	</el-form-item>
          </b-col>
        </b-row>

        <b-row class='mb-1'>
          <b-col >
		   <v-dialog
      v-model='modal_factura'
      width='500'
    >
	
	
      <v-card v-if='ver'>
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
      border='left'>
	{{mensaje_factura}}
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color='primary'
            
            @click='modal_factura = !modal_factura'
          >
           Salir
          </v-btn>
        </v-card-actions>
      </v-card>
	  
	  
      <v-card v-if='!ver'>
          <v-card-title>
            <span class='headline'>Email</span>
          </v-card-title>
          <v-form ref='form'>
            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs12>
                    <v-text-field label='Consecutivo factura' required    v-model='factura'  ></v-text-field>
                  </v-flex>
                </v-layout>
              </v-container>
              <small>*indicates required field</small>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color='blue darken-1' flat @click.native='modal_factura = false'>Cancelar </v-btn>
              <v-btn color='blue darken-1' flat @click.native='onSave'>Save</v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
     
    </v-dialog>		
		  	</el-form-item>
							<el-form-item label='Cobro' prop='cobro'>	  
							   <el-select
							   title='Cobro'
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.cobro'
								allow-create
								 @change='tipo_cobro()'
								placeholder='Selecion cobro '>
								<el-option
								v-for='item in cobro'
                                 :key='item.id'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
		  </b-col>
          <b-col>
            		 <el-form-item  label='Cantidad' prop='cantidad'>
					  <el-input type='number' v-model='formformItenes.cantidad'   @input='Multiplicar'  placeholder='Ingresar  cantidad'></el-input>
					</el-form-item>
          </b-col>
          <b-col>
            	 <el-form-item   label='Valor ' prop='valor'>
					  <el-input type='number'  @input='Multiplicar'  v-model='formformItenes.valor' placeholder='Ingresar  valor'></el-input>
					</el-form-item>
          </b-col>
        </b-row>

        <b-row>
          <b-col >
		   <el-form-item   label='Valor total'    :disabled='disabled1 == 0'  prop='valor_total'>
					  <el-input type='number' v-model='formformItenes.valor_total'
					  placeholder='Ingresar  valor total'></el-input>
					</el-form-item>
		</b-col>
          <b-col>
            	 <el-form-item   label='Centro Operacion' prop='centro_operacion'>
					  <el-input type='text' v-model='formformItenes.centro_operacion'
					  placeholder='Ingresar  :disabled='disabled1 == 0'  centro operacion'></el-input>
					</el-form-item>
          </b-col>
          <b-col>
            <el-form-item   label='Centro de Costo' prop='centro_costo'>
					  <el-input type='text' v-model='formformItenes.centro_costo'
					  placeholder='Ingresar :disabled='disabled1 == 0'   centro costo'></el-input>
					</el-form-item>
          </b-col>
        </b-row>
		<b-row>
	
						
				
          <b-col >
		   	 <el-form-item   label='Descripcion  ' prop='descripcion_item'>
					  <el-input   v-model='formInline.descripcion_item'
					  :disabled='disabled == 1'  placeholder='Ingresar  flota'></el-input>
					</el-form-item>
		</b-col>
          <b-col>
            		 <el-form-item   label='Siniestro ' prop='siniestro'>
					  <el-input   v-model='formInline.user'  :disabled='disabled1 == 0'  placeholder='Ingresar  Siniestro'></el-input>
					</el-form-item>
          </b-col>
          <b-col>
          		 <el-form-item   label='Flota ' prop='factor'>
					  <el-input   v-model='formInline.factor'  
					  :disabled='disabled1 == 0'  placeholder='Ingresar  flota'></el-input>
					</el-form-item>
          </b-col>
        </b-row>
		
      </el-form>
 </v-container>
   <v-card-actions>
   

		

      </b-container>

      <template v-slot:modal-footer>
        <div class='w-100'>
		 
	  <b-button @click='validar_ingresar_detalle' :disabled='disabled == 1'  variant='outline-success'>Ingresar detalle</b-button>
          <b-button
            variant='primary'
            size='sm'
            class='float-right'
            @click='show=false'
          >
            Cancelar 
          </b-button>
        </div>
      </template>
    </b-modal>
		
		
  </v-col>   
	  </v-tab-item>
    

  <v-tab-item>
    	<v-card flat>
          <v-row>
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
     INFORMACION DE SOLICITUD
    </v-chip>
        </v-col>

        <v-col cols='12' sm='4' md='4'>
        <v-text-field
            label='ID Novedad'
            outlined
            dense
			v-model='novedad_siniestro.id_novedad'
          ></v-text-field>
        </v-col>

        <v-col cols='12' sm='4' md='4'>

          <v-text-field
            label='Novedad'
            outlined
            dense
			v-model='novedad_siniestro.novedad'
          ></v-text-field>
        </v-col>

		
	  <v-col cols='12' sm='4' md='4'>
          <v-text-field
            label='Solicitante' 
            outlined
            dense
			v-model='novedad_siniestro.solicitante'
          ></v-text-field>
        </v-col>
		
		
		<v-col cols='12' sm='4' md='4'>
        <v-text-field
            label='Tipo novedad'
            outlined
            dense
			v-model='tipo_novedad_array.nombre'
          ></v-text-field>
        </v-col>

        <v-col cols='12' sm='4' md='4'>

          <v-text-field
            label='Fecha creacion'
            outlined
            dense
			v-model='novedad_siniestro.fecha_creacion'
          ></v-text-field>
        </v-col>

		
	  <v-col cols='12' sm='4' md='4'>
          <v-text-field
            label='Ciudad' 
            outlined
            dense
			v-model='ciudad_ciudad_soli.nombre'
          ></v-text-field>
        </v-col>
		
		

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
     INFORMACIÓN DE LA SINISTRO {{formInline.user}}
    </v-chip>
        </v-col>
      
        <v-col cols='12' sm='4' md='4'>
          <v-text-field
            label='Placa'
            outlined
            dense
			v-model='novedad_siniestro.id_placa'
          ></v-text-field>
        </v-col>
		 <v-col cols='12' sm='4' md='4'>

          <v-text-field
           label='Marca'
            outlined
            dense
			v-model='marca_array.nombre'
          ></v-text-field>
        </v-col>
        <v-col cols='12' sm='4' md='4'>
          <v-text-field
            label='Linea'
            outlined
            dense
			v-model='linea_array.nombre'
          ></v-text-field>
        </v-col>
		
		<v-col cols='12' sm='4' md='4'>

          <v-text-field
           label='Tipo de servicio '
            outlined
            dense
			v-model='tipo_servicio'
          ></v-text-field>
        </v-col>
        <v-col cols='12' sm='4' md='4'>
          <v-text-field
            label='Usuaro'
            outlined
            dense
				v-model='asegurado_nombre'
          ></v-text-field>
        </v-col>
		 <v-col cols='12' sm='4' md='4'>
          <v-text-field
           label='AOA OFICINA'
            outlined
            dense
		   v-model='ciudad_array.nombre'
          ></v-text-field>
        </v-col>
		
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
     ESTADO DEL VEHÍCULO {{novedad_siniestro.id_placa}}
    </v-chip>
        </v-col>
		
		<v-col cols='12' sm='6' md='6'>

          <v-text-field
           label='Fecha de Ingreso '
            outlined
            dense
		    v-model='novedad.fechaIngreso'
          ></v-text-field>
        </v-col>
		
         <v-col cols='12' sm='6' md='6'>

          <v-text-field
           label='Kilomentraje ingreso'
            outlined
            dense
			v-model='novedad.kmIngreso'
          ></v-text-field>
        </v-col>
		   <v-col cols='12' sm='6' md='6'>

          <v-text-field
           label='Fecha  de salida '
            outlined
            dense
		    v-model='novedad.fechaSalida'
          ></v-text-field>
        </v-col>
         <v-col cols='12' sm='6' md='6'>

          <v-text-field
           label='Kilomentraje de salida'
            outlined
            dense
			v-model='novedad.kmIngreso'
          ></v-text-field>
        </v-col>
		
		

	
		
      </v-row>
	  
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
     COTIZACIONES DE NOVEDAD {{novedadId}}
    </v-chip>
        </v-col>   
		
		    <v-col cols='12'  class='row justify-content-center align-items-center' sm='12' md='12'>
		 

 <b-container fluid>
        <!-- User Interface controls -->
        <b-row>
            <b-col md='6' class='my-1'>
                <b-form-group label-cols-sm='3' label='Filtrar'  bordered fixed class=' form-control mb-0'>
                    <b-input-group>
                        <b-form-input v-model='filter' placeholder='Escribe para buscar'></b-form-input>
                        <b-input-group-append>
                         <b-button :disabled='!filter' @click='filter = NULL'>Cancelar</b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-col>

            <b-col md='6' class='my-1'>
                <b-form-group label-cols-sm='3' label='Sort' class='mb-0'>
                    <b-input-group>
                        <b-form-select v-model='sortBy' :options='sortOptions'>
                            <option slot='first' :value='null'>-- Nombre --</option>
                        </b-form-select>
                        <b-form-select v-model='sortDesc' :disabled='!sortBy' slot='append'>
                            <option :value='false'>Ascendente </option> <option :value='true'>Descendente</option>
                        </b-form-select>
                    </b-input-group>
                </b-form-group>
            </b-col>

            <b-col md='6' class='my-1'>
                <b-form-group label-cols-sm='3' label='Ordenar dirección' class='mb-0'>
                    <b-input-group>
                        <b-form-select v-model='sortDirection' slot='append'>
                            <option value='asc'>Ascendente</option> <option value='desc'>Descendente</option>
                            <option value='last'>Última</option>
                        </b-form-select>
                    </b-input-group>
                </b-form-group>
            </b-col>

            <b-col md='6' class='my-1'>
                <b-form-group label-cols-sm='3' label='Por página' class='mb-0'>
                    <b-form-select v-model='perPage' :options='pageOptions'></b-form-select>
                </b-form-group>
            </b-col>
        </b-row>

        <!-- Main table element -->
        <b-table
				         empty-filtered-text='No hay registros que coincidan con su solicitud' mod='ioweb_slocator'
                 empty-text='No hay registros para mostrar'
                show-empty
                striped hover
                stacked='md'
                :items='cotizacion_novedad_array'
                :fields='cotizacion_novedad_array_labe'
                :current-page='currentPage'
                :per-page='perPage'
                :filter='filter'
                :sort-by.sync='sortBy'
                :sort-desc.sync='sortDesc'
                :sort-direction='sortDirection'
                @filtered='onFiltered'
        >
           <template v-slot:cell(detalle)='{ detailsShowing, item }' >
        <b-btn @click='buscar_detalle_id(item)'>{{ detailsShowing ? 'Cerrar' : 'Ver'}} Detalle</b-btn>
      </template>
	  
	   <template v-slot:cell(proveedor)='{ detailsShowing, item }' >
        <b-btn @click='toggleDetails(item)'>{{ detailsShowing ? 'Cerrar ' : 'Ver'}} Provedor</b-btn>
      </template> 
	  <template v-slot:cell(opciones)='{ detailsShowing, item }' >
  		 <b-button @click='ingresa_cotizacion(item)'>Ingresar detalle</b-button>
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
	 
	 
      <template v-slot:row-details='{ item }'>
        <b-card v-if='!ver_tabla'>
          <b-table :items='buscar_proveedor_id_array'
		 :fields='proveedor_labe' bordered fixed>
		  </b-table>
        </b-card>
		  <b-card v-if='ver_tabla'>
          <b-table :items='detalle_id_array'
		 :fields='detalle_labe' bordered fixed>
		  </b-table>
        </b-card>
      </template>
	  
	  
           
        </b-table>

        <b-row>
            <b-col md='6' class='my-1'>
                <b-pagination
                        v-model='currentPage'
                        :total-rows='totalRows'
                        :per-page='perPage'
                        class='my-0'
                ></b-pagination>
            </b-col>
        </b-row>

        <!-- Info modal -->
        <b-modal id='modal-info' @hide='resetModal' :title='modalInfo.title' ok-only>
            <pre>{{ modalInfo.content }}</pre>
        </b-modal>
    
    <b-modal
      v-model='show'
      title='  Ingresar detalle de cotización '
      header-bg-variant='dark'
      header-text-variant='light'
      body-bg-variant='light'
      body-text-variant='dark'
      footer-bg-variant='dark'
      footer-text-variant='light'
    >
	
	
      <b-container fluid>
	  
	    <el-form :inline='true' :model='formformItenes' 
		   class='demo-form-inline'  
			class=' justify-content-center align-items-center'
		   :rules='rulesformItenes' ref='forItenes'>
				   
				   
  

        <b-row class='mb-1'>
          <b-col >
		  <el-form-item label='Tipo de bien' prop='tipoBien'>
						     <el-select
							   title='	Tipo de  bien'
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.tipoBien'
							  @change='tipo_biien()'
								allow-create
								placeholder='Selecion modalidad '>
								<el-option
						
								  v-for='item in bien'
								  :key='item.id'
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
								</el-form-item>
		  
		  </b-col>
          <b-col>
            		<el-form-item label='Clase de Requisición'  prop='clase'>	  
							   <el-select
							   title='Clase de Requisición'
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.clase'
								allow-create
								placeholder='Selecion clase '>
								<el-option
								v-for='item in clase'
                                 :key='item.id'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
          </b-col>
          <b-col>
            						<el-form-item label='Tipo de Item'   prop='tipoItem'>	  
							   <el-select
							   title='Tipo de Item'
							 :disabled='disabled == 1' 
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.tipoItem'
								allow-create
								placeholder='Selecion modalidad '>
								<el-option
								v-for='(item, index) in servicioList'
                                 :key='index'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
							  
					  	</el-form-item>
          </b-col>
        </b-row>

        <b-row class='mb-1'>
          <b-col >
		   <v-dialog
      v-model='modal_factura'
      width='500'
    >
	
	
      <v-card v-if='ver'>
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
      border='left'>
	{{mensaje_factura}}
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color='primary'
            
            @click='modal_factura = !modal_factura'
          >
           Salir
          </v-btn>
        </v-card-actions>
      </v-card>
	  
	  
      <v-card v-if='!ver'>
          <v-card-title>
            <span class='headline'>Email</span>
          </v-card-title>
          <v-form ref='form'>
            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs12>
                    <v-text-field label='Consecutivo factura' required    v-model='factura'  ></v-text-field>
                  </v-flex>
                </v-layout>
              </v-container>
              <small>*indicates required field</small>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color='blue darken-1' flat @click.native='modal_factura = false'>Cancelar </v-btn>
              <v-btn color='blue darken-1' flat @click.native='onSave'>Save</v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
     
    </v-dialog>		



  	</el-form-item>
							<el-form-item label='Cobro' prop='cobro'>	  
							   <el-select
							   title='Cobro'
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.cobro'
								allow-create
								 @change='tipo_cobro()'
								placeholder='Selecion cobro '>
								<el-option
								v-for='item in cobro'
                                 :key='item.id'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
		  </b-col>
          <b-col>
            		 <el-form-item  label='Cantidad' prop='cantidad'>
					  <el-input type='number' v-model='formformItenes.cantidad'   @input='Multiplicar'  placeholder='Ingresar  cantidad'></el-input>
					</el-form-item>
          </b-col>
          <b-col>
            	 <el-form-item   label='Valor ' prop='valor'>
					  <el-input type='number'  @input='Multiplicar'  v-model='formformItenes.valor' placeholder='Ingresar  valor'></el-input>
					</el-form-item>
          </b-col>
        </b-row>

        <b-row>
          <b-col >
		   <el-form-item   label='Valor total'    :disabled='disabled1 == 0'  prop='valor_total'>
					  <el-input type='number' v-model='formformItenes.valor_total'
					  placeholder='Ingresar  valor total'></el-input>
					</el-form-item>
		</b-col>
          <b-col>
            	 <el-form-item   label='Centro Operacion' prop='centro_operacion'>
					  <el-input type='text' v-model='formformItenes.centro_operacion'
					  placeholder='Ingresar  centro operacion'></el-input>
					</el-form-item>
          </b-col>
          <b-col>
            <el-form-item   label='Centro de Costo' prop='centro_costo'>
					  <el-input type='text' v-model='formformItenes.centro_costo'
					  placeholder='Ingresar  centro costo'></el-input>
					</el-form-item>
          </b-col>
        </b-row>
		<b-row>
	
						
				
          <b-col >
		   	 <el-form-item   label='Flota ' prop='factor'>
					  <el-input   v-model='formInline.factor'  :disabled='disabled1 == 0'  placeholder='Ingresar  flota'></el-input>
					</el-form-item>
		</b-col>
          <b-col>
            		 <el-form-item   label='Siniestro ' prop='siniestro'>
					  <el-input   v-model='formInline.user'  :disabled='disabled1 == 0'  placeholder='Ingresar  Siniestro'></el-input>
					</el-form-item>
          </b-col>
          <b-col>
          		 <el-form-item   label='Flota ' prop='factor'>
					  <el-input   v-model='formInline.factor'  :disabled='disabled1 == 0'  placeholder='Ingresar  flota'></el-input>
					</el-form-item>
          </b-col>
        </b-row>
		
      </el-form>
 </v-container>
   <v-card-actions>
   

		

      </b-container>

      <template v-slot:modal-footer>
        <div class='w-100'>
		 
	  <b-button @click='validar_ingresar_detalle' :disabled='disabled == 1'  variant='outline-success'>Ingresar detalle</b-button>
          <b-button
            variant='primary'
            size='sm'
            class='float-right'
            @click='show=false'
          >
            Cancelar 
          </b-button>
        </div>
      </template>
    </b-modal>
		
		
  </v-col>   
		
 
	
  
  
		
	
    </v-card>
  </v-tab-item>
  <v-tab-item>
    <v-card flat>
	<br>
   <v-card
    class='mx-auto'
    max-width='344'
    outlined
	shaped
	class='v-card v-sheet theme--light elevation-24'
  >
   <v-card-title>Imprimir pdf</v-card-title>
      <v-form
    ref='formpdf'
    v-model='valid'
    lazy-validation
  >
    <v-text-field
      v-model='nombrepdf'
      :counter='10'
      :rules='nombrepdfr'
      label='Nombre pdf'
      required
            outlined
            clearable
          ></v-text-field>

  

  

    <v-card-actions>
        <v-btn
      :disabled='!valid'
      color='success'
      class='mr-4'
      @click='validate'
    >
      Imprimir
	  
    </v-btn>
    </v-card-actions>
	</v-form>
  </v-card>
   <div id='resulta'></div>
  <br>
  
    </v-card>
  </v-tab-item>  
  
    <v-tab-item>
    <v-card flat>
	
	
       <v-card
    class='mx-auto'
    
    outlined
	shaped
	 icons-and-text
	class='v-card v-sheet theme--light elevation-24'
  >
   <v-card-title>Agregar una nueva cotización a la {{novedadId}} </v-card-title>
     <v-data-table
    :headers='headers'
    :items='desserts'
    sort-by='calories'
    class='elevation-1'
  >
    <template v-slot:top>
      <v-toolbar flat color='white'>
        <v-toolbar-title>Mis cotizaciones </v-toolbar-title>
        <v-divider
          class='mx-4'
          inset
          vertical
        ></v-divider>
        <v-spacer></v-spacer>
        <v-dialog v-model='dialog' max-width='500px'>
          <template v-slot:activator='{ on, attrs }'>
            <v-btn
              color='primary'
              dark
              class='mb-2'
			   @click='dialogConfi = true'
            >Nueva cotización</v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class='headline'>{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
         
		 
            </v-card-text>

			
			
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color='blue darken-1' text @click='close'>Cancel</v-btn>
              <v-btn color='blue darken-1' text @click='save'>Save</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>
    <template v-slot:item.actions='{ item }'>
      <v-icon
        small
        class='mr-2'
        @click='editItem(item)'
      >
        mdi-pencil
      </v-icon>
      <v-icon
        small
        @click='deleteItem(item)'
      >
        mdi-delete
      </v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn color='primary' @click='initialize'>Reset</v-btn>
    </template>
  </v-data-table>


  
    <v-card-actions>
        <v-btn
      :disabled='!valid'
      color='success'
      class='mr-4'
      @click='validate'
    >
      Imprimir
    </v-btn>
    </v-card-actions>
	 </el-form>
  </v-card>
  
    </v-card>
  </v-tab-item>  
</v-tabs>		


  			
    </tab-content>
    <el-button type='primary' slot='prev'>Anterior</el-button>
    <el-button type='primary' slot='next'>Siguiente</el-button>
    <el-button type='primary' slot='finish'>Finalizar </el-button>
  </form-wizard>
  
  
  
 
 <el-dialog title='Cotización' :visible.sync='dialogConfi'>
 		 <el-form :inline='true' :model='formContN'  id='formCoN'
	   class='demo-form-inline' :rules='rulesConN' ref='formCoN'>
	  <b-alert   v-if='!editar' show variant='primary'>
	  <h6 class='row justify-content-center align-items-center'>
	  Ingresar Cotización novedad {{novedadId}}</h6> </b-alert>
	  
	   <b-alert   v-if='editar'  show variant='warning'>
	  <h6 class='row justify-content-center align-items-center'>
	  Editar Cotización  {{novedadId}}</h6> </b-alert>
	  
	  
       <br>
		<el-form-item label='Poveedor' prop='proveedor'>
               <el-select
				title='Poveedor'
					class=' mr-sm-2 align-items-center' 
					filterable
					
					allow-create
				v-model='formContN.proveedor'
					filterable
					allow-create
                    placeholder='Selecione un tipo  proveedor'>
                  <el-option
                      	  v-for='item in proveedor'
					  :key='item.id'
					  :label='item.nombre'
					  :value='item.id'>
                </el-option>
            </el-select>
		  </el-form-item>
		  
		 <el-form-item  label='Descripción' prop='descripcion'>
          <el-input type='textarea' v-model='formContN.descripcion' placeholder='Ingresar  descripción'></el-input>
        </el-form-item>
		<br>
		 <el-form-item   label='Actividad Solicitante ' prop='actividad_solitante'>
          <el-input type='textarea'  v-model='formContN.actividad_solitante' placeholder='Ingresar actividad solicitamte'></el-input>
        </el-form-item>
		 <el-form-item   label='Actividad  Proveedor' prop='actividad_provedor'>
          <el-input type='textarea'  v-model='formContN.actividad_provedor' placeholder='Ingresar actividad proveedor'></el-input>
        </el-form-item>

      </el-form>
  <span slot='footer' class='dialog-footer'>
    <el-button @click='dialogConfi = false'>Cancelar </el-button>
	
    <el-button v-if='!editar' type='primary' @click='ingresarCotizacionNueva'>Registrar cotifación</el-button>
	<b-button  v-if='editar' type='warning' @click='editarCotizacionNueva' variant='outline-warning'>
      <b-icon icon='exclamation-circle-fill' variant='warning'></b-icon> Editar cotifación
      </b-button>
	
  </span>
</el-dialog>
    
	  
 
		
	    <v-overlay  :absolute='absolute'
          :value='overlay' >
      <v-progress-circular indeterminate size='64'></v-progress-circular>
    </v-overlay>
	
	
	

	
	
	
	
	
	
   <form-wizard @on-complete='onComplete'  v-if='placa' 
   title='Ingresar novedad placa'
   subtitle='Busca el vehiculo por la placa '
   step-size='sm'   shape='circle'   color='gray'  error-color='#ff4949'>

    <tab-content title='Buscar sinestro ' icon='ti-user' 
	:before-change='validateFirsPlaca'>
      <el-form :inline='true'  id='miForm' 
		:model='formInline' class='demo-form-inline' 
		:rules='rules' ref='ruleForm'>
		
		   <h6 class='row justify-content-center align-items-center'>Buscar vehiculo por placa  </h6>
		   <br>
         <el-form-item label='Placa' prop='user'>
          <el-input v-model='formInline.user' 
		  placeholder='Ingresa el Placa'></el-input>
        </el-form-item>
      </el-form>
    </tab-content>
	
<tab-content icon='fa fa-check-square-o' title='Ingresar novedad' 
:before-change='validateF'>
 
 <el-form :inline='true' :model='formInlin' class='demo-form-inline' :rules='rule' ref='ruleFo'>
      <h6 class='row justify-content-center align-items-center'>Ingresar novedad del vehiculo {{formInline.user}}</h6>
	     <br>
	  <el-form-item  label='Novedad' prop='user'>
          <el-input v-model='formInlin.user'  type='textarea'  placeholder='Ingresar Novedad'></el-input>
        </el-form-item>
		
        <el-form-item label='Tipo de novedad' prop='region'>
          <el-select
					class=' mr-sm-2 align-items-center' 
					 v-model='formInlin.region'
					filterable
					allow-create
					placeholder='Selecione un tipo de novedad '>
					<el-option
					  v-for='item in tipo'
					  :key='item.id'
					  :label='item.nombre'
					  :value='item.id'>
			</el-option>
		  </el-select>
        </el-form-item>

    	
      </el-form>

    </tab-content>
	  <tab-content title='Agregar cotización' icon='ti-check' :before-change='validateCotisacion'>
	   <el-form :inline='true' :model='formCont'  id='formCo'
	   class='demo-form-inline' :rules='rulesCon' ref='formCo'>
      <h6 class='row justify-content-center align-items-center'>Ingresar cotización de la novedad  {{novedadId}}</h6>
       <br>
		<el-form-item label='Poveedor' prop='proveedor'>
               <el-select
				title='Poveedor'
					class=' mr-sm-2 align-items-center' 
					filterable
					
					allow-create
				v-model='formCont.proveedor'
					filterable
					allow-create
                    placeholder='Selecione un tipo  proveedor'>
                  <el-option
                      	  v-for='item in proveedor'
					  :key='item.id'
					  :label='item.nombre'
					  :value='item.id'>
                </el-option>
            </el-select>
		  </el-form-item>
		  
		 <el-form-item  label='Descripción' prop='descripcion'>
          <el-input type='textarea' v-model='formCont.descripcion' placeholder='Ingresar  descripción'></el-input>
        </el-form-item>
		<br>
		 <el-form-item   label='Actividad Solicitante ' prop='actividadSoli'>
          <el-input type='textarea'    v-model='formCont.actividadSoli' placeholder='Ingresar actividad solicitamte'></el-input>
        </el-form-item>
		 <el-form-item   label='Actividad  Proveedor' prop='actividadProe'>
          <el-input type='textarea'   v-model='formCont.actividadProe' placeholder='Ingresar actividad proveedor'></el-input>
        </el-form-item>

      </el-form>

	  </tab-content>
  <tab-content title='Imprimir pdf' icon='ti-check'>

<v-tabs
  color='cyan'
  darken-4
  fixed-tabs
  slider-color='yellow'
  v-model='tab'  
   icons-and-text
      class='elevation-2'
>
<v-tabs-slider></v-tabs-slider>

          <v-tab
            ripple
            class='primary--text'>
            Ingresar detalles de la cotización  
        <v-icon>fact_check</v-icon>
        </v-tab>
	  
	  
          <v-tab
            ripple
            class='primary--text'>
          
             Ver novedad
        <v-icon>fact_check</v-icon>
      </v-tab>

          <v-tab
           ripple
            class='primary--text'
          >
		  
			  Imprimir novedad
            <v-icon>picture_as_pdf</v-icon>
          </v-tab>

          <v-tab
            ripple
            class='primary--text'
          >
		    Mis cotizaciones
            <v-icon>exposure_plus_1</v-icon>
			
          </v-tab>
		  
		    <v-tab-item>

		    <v-col cols='12'  class='row justify-content-center align-items-center' sm='12' md='12'>
	
	
		
	
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
   Ingresar detalle de la cotización {{cotizacionid}}
    </v-chip>
        </v-col>
		
		<v-card   class='mx-auto'>
	

	
				<v-container grid-list-md text-xs-center>
							   
			   <el-form :inline='true' :model='formformItenes' 
				   class='demo-form-inline'  
				    class=' justify-content-center align-items-center'
				   :rules='rulesformItenes' ref='forItenes'>
				    
					<el-form-item label='Tipo de bien' prop='tipoBien'>
						     <el-select
							   title='	Tipo de  bien'
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.tipoBien'
							  @change='tipo_biien()'
								allow-create
								placeholder='Selecion modalidad '>
								<el-option
						
								  v-for='item in bien'
								  :key='item.id'
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
								</el-form-item>
								
						<el-form-item label='Tipo de Item'   prop='tipoItem'>	  
							   <el-select
							   title='Tipo de Item'
							 :disabled='disabled == 1' 
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.tipoItem'
								allow-create
								placeholder='Selecion modalidad '>
								<el-option
								v-for='(item, index) in servicioList'
                                 :key='index'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
							  
					  	</el-form-item>
						
							<el-form-item label='Clase de Requisición'  prop='clase'>	  
							   <el-select
							   title='Clase de Requisición'
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.clase'
								allow-create
								placeholder='Selecion clase '>
								<el-option
								v-for='item in clase'
                                 :key='item.id'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
							  
					  	</el-form-item>
							<el-form-item label='Cobro' prop='cobro'>	  
							   <el-select
							   title='Cobro'
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.cobro'
								allow-create
								placeholder='Selecion cobro '>
								<el-option
								v-for='item in cobro'
                                 :key='item.id'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
							  
					  	</el-form-item>
													
					 <el-form-item  label='Cantidad' prop='cantidad'>
					  <el-input type='number' v-model='formformItenes.cantidad'   @input='Multiplicar'  placeholder='Ingresar  cantidad'></el-input>
					</el-form-item>
				
					 <el-form-item   label='Valor ' prop='valor'>
					  <el-input type='number'  @input='Multiplicar'  v-model='formformItenes.valor' placeholder='Ingresar  valor'></el-input>
					</el-form-item>
					 <el-form-item   label='Valor total' prop='valor_total'>
					  <el-input type='number' v-model='formformItenes.valor_total' placeholder='Ingresar  valortotal'></el-input>
					</el-form-item>
					
					
					<el-form-item label='Centro operacion' prop='centro_operacion'>	  
							   <el-select
							   title='Centro operacion'
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.centro_operacion'
								allow-create
							:disabled='disabled == 1' 
								placeholder='Selecion centro operacion '>
								<el-option
								v-for='item in centro_operacion'
                                 :key='item.id'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
							  
					  	</el-form-item>
						
							<el-form-item label='Centro de costo' prop='centro_costo'>	  
							   <el-select
							   title='Centro de costo'
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.centro_costo'
								allow-create
								:disabled='disabled == 1' 
								placeholder='Selecion centro de costo '>
								<el-option
								v-for='item in centro_costo'
                                 :key='item.id'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
							  </el-form-item>
						 
							  
							  	<el-form-item label='Flota' prop='factor'>	  
							   <el-select
							   title='Centro de costo'
							  class=' mr-sm-2 align-items-center' 
							  v-model='formformItenes.factor'
								allow-create
								placeholder='Selecion una  flota '>
								<el-option
								v-for='item in flota'
                                 :key='item.id'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
							  
					  	</el-form-item>
						 
						
						 <el-form-item   label='Placa ' prop='placa'>
					  <el-input   v-model='formInline.user'  :disabled='disabled == 1'  placeholder='Ingresar  placa'></el-input>
					</el-form-item>
						
						<el-form-item   label='Descripcion item' prop='descripcion_item'>
					  <el-input type='textarea'  v-model='formformItenes.descripcion_item' placeholder='Ingresar descripcion item'></el-input>
					</el-form-item>

      </el-form>
 </v-container>
   <v-card-actions>
   
        <v-col cols='12'  class='row justify-content-center align-items-center' sm='12' md='12'>
         <v-btn
       :disabled='disabled == 1' 
      color='success'
      class='mr-4'
      @click='validar_ingresar_detalle'
    >
      Ingresar detalle de cotización {{cotizacionid}}
    </v-btn>
        </v-col>
		
		

    </v-card-actions>
				</v-card>
				
         </v-col>
	  </v-tab-item>
    

  <v-tab-item>
    <v-card flat>
          <v-row>
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
     INFORMACION DE SOLICITUD
    </v-chip>
        </v-col>

        <v-col cols='12' sm='4' md='4'>
          <v-text-field
            label='Consecutivo'
            outlined
            dense

			v-modal='novedad.id_novedad'
          ></v-text-field>
        </v-col>

        <v-col cols='12' sm='4' md='4'>

          <v-text-field
             label='Fecha de creación '
            outlined
            dense
		   v-modal='novedad.fecha_creacion'
          ></v-text-field>
        </v-col>

		
	  <v-col cols='12' sm='4' md='4'>
          <v-text-field 
             label='Cuidad'
            outlined
            dense
			v-modal='novedad.ciudad_novedad'
          ></v-text-field>
        </v-col>

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
     INFORMACIÓN DE LA PLACA
    </v-chip>
        </v-col>
      
        <v-col cols='12' sm='4' md='4'>
          <v-text-field
            label='Placa'
            outlined
            dense
			v-model='novedad.placa'
          ></v-text-field>
        </v-col>
		 <v-col cols='12' sm='4' md='4'>

          <v-text-field
           label='Marca'
            outlined
            dense
			v-model='novedad.marca'
          ></v-text-field>
        </v-col>
        <v-col cols='12' sm='4' md='4'>
          <v-text-field
            label='Linea'
            outlined
            dense
			v-model='novedad.linea'
          ></v-text-field>
        </v-col>
		
		<v-col cols='12' sm='4' md='4'>

          <v-text-field
           label='Kilometraje'
            outlined
            dense
			v-model='novedad.km'
          ></v-text-field>
        </v-col>
        <v-col cols='12' sm='4' md='4'>
          <v-text-field
            label='Usuaro'
            outlined
            dense
				v-model='novedad.usuario'
          ></v-text-field>
        </v-col>
		 <v-col cols='12' sm='4' md='4'>
          <v-text-field
           label='AOA OFICINA'
            outlined
            dense
		   v-model='novedad.oficina'
          ></v-text-field>
        </v-col>

		<v-col cols='12' sm='6' md='6'>

          <v-text-field
           label='Proveedor'
            outlined
            dense
				v-model='novedad.proveedor_nombre'
          ></v-text-field>
        </v-col>
        <v-col cols='12' sm='6' md='6'>
          <v-text-field
            label='Ubicación '
            outlined
            dense
			v-model='novedad.ubicacion'
          ></v-text-field>
        </v-col>
		
		<v-col cols='12' sm='6' md='6'>

          <v-text-field
           label='Ciudad'
            outlined
            dense
		    v-model='novedad.ubicacion_proveedor'
          ></v-text-field>
        </v-col>
         <v-col cols='12' sm='6' md='6'>

          <v-text-field
           label='Correo'
            outlined
            dense
			v-model='novedad.correo'
          ></v-text-field>
        </v-col>
		   <v-col cols='12' sm='12' md='12'>

          <v-textarea
			  background-color='grey lighten-2'
			  color='cyan'
			  label='Novedad'
			  v-model='novedad.novedad'
			></v-textarea>
        </v-col>
         <v-col cols='12' sm='6' md='6'>

          <v-text-field
           label='Fecha de Ingreso '
            outlined
            dense
		    v-model='novedad.fechaIngreso'
          ></v-text-field>
        </v-col>
		
         <v-col cols='12' sm='6' md='6'>

          <v-text-field
           label='Kilomentraje ingreso'
            outlined
            dense
			v-model='novedad.kmIngreso'
          ></v-text-field>
        </v-col>
		   <v-col cols='12' sm='6' md='6'>

          <v-text-field
           label='Fecha  de salida '
            outlined
            dense
		    v-model='novedad.fechaSalida'
          ></v-text-field>
        </v-col>
         <v-col cols='12' sm='6' md='6'>

          <v-text-field
           label='Kilomentraje de salida'
            outlined
            dense
			v-model='novedad.kmIngreso'
          ></v-text-field>
        </v-col>
			   <v-col cols='12' sm='12' md='12'>

          <v-textarea
			  background-color='grey lighten-2'
			  color='cyan'
			  label='Describa brevemente lo realizado para dejar el vehículo en buenas condiciones'
			  v-model='novedad.descripcion'
			></v-textarea>
        </v-col>
		
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
  Mis cotizaciones
    </v-chip>
        </v-col>
		   <v-col cols='12' sm='6' md='6'>

		   
          <v-textarea
			  background-color='grey lighten-2'
			  color='cyan'
			  label='Solicitante'
			  v-model='novedad.actividad_solitante'
			></v-textarea>
        </v-col>
		<v-col cols='12' sm='6' md='6'>

          <v-textarea
			  background-color='grey lighten-2'
			  color='cyan'
			  label='Provedor a  Mecanico '
			  v-model='novedad.actividad_provedor'
			></v-textarea>
        </v-col>
		
      </v-row>
  
  
		
	
    </v-card>
  </v-tab-item>
  <v-tab-item>
    <v-card flat>
	<br>
   <v-card
    class='mx-auto'
    max-width='344'
    outlined
	shaped
	class='v-card v-sheet theme--light elevation-24'
  >
   <v-card-title>Imprimir pdf</v-card-title>
      <v-form
    ref='formpdf'
    v-model='valid'
    lazy-validation
  >
    <v-text-field
      v-model='nombrepdf'
      :counter='10'
      :rules='nombrepdfr'
      label='Nombre pdf'
      required
            outlined
            clearable
          ></v-text-field>

  

  

    <v-card-actions>
        <v-btn
      :disabled='!valid'
      color='success'
      class='mr-4'
      @click='validate'
    >
      Imprimir
	  
    </v-btn>
    </v-card-actions>
	</v-form>
  </v-card>
   <div id='resulta'></div>
  <br>
  
    </v-card>
  </v-tab-item>  
  
    <v-tab-item>
    <v-card flat>
	
	
       <v-card
    class='mx-auto'
    
    outlined
	shaped
	 icons-and-text
	class='v-card v-sheet theme--light elevation-24'
  >
   <v-card-title>Agregar una nueva cotización a la {{novedadId}}} </v-card-title>
     <v-data-table
    :headers='headers'
    :items='desserts'
    sort-by='calories'
    class='elevation-1'
  >
    <template v-slot:top>
      <v-toolbar flat color='white'>
        <v-toolbar-title>Mis cotizaciones </v-toolbar-title>
        <v-divider
          class='mx-4'
          inset
          vertical
        ></v-divider>
        <v-spacer></v-spacer>
        <v-dialog v-model='dialog' max-width='500px'>
          <template v-slot:activator='{ on, attrs }'>
            <v-btn
              color='primary'
              dark
              class='mb-2'
			   @click='dialogConfi = true'
            >Nueva cotización</v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class='headline'>{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
         
		 
            </v-card-text>

			
			
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color='blue darken-1' text @click='close'>Cancel</v-btn>
              <v-btn color='blue darken-1' text @click='save'>Save</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>
    <template v-slot:item.actions='{ item }'>
      <v-icon
        small
        class='mr-2'
        @click='editItem(item)'
      >
        mdi-pencil
      </v-icon>
      <v-icon
        small
        @click='deleteItem(item)'
      >
        mdi-delete
      </v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn color='primary' @click='initialize'>Reset</v-btn>
    </template>
  </v-data-table>


  
    <v-card-actions>
        <v-btn
      :disabled='!valid'
      color='success'
      class='mr-4'
      @click='validate'
    >
      Imprimir
    </v-btn>
    </v-card-actions>
	 </el-form>
  </v-card>
  
    </v-card>
  </v-tab-item>  
</v-tabs>		


  			
    </tab-content>
    <el-button type='primary' slot='prev'>Anterior</el-button>
    <el-button type='primary' slot='next'>Siguiente</el-button>
    <el-button type='primary' slot='finish'>Finalizar </el-button>
  </form-wizard>
 <v-dialog
      v-model='modal_factura'
      width='500'
    >
	
	
      <v-card v-if='ver'>
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
      border='left'>
	{{mensaje_factura}}
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color='primary'
            
            @click='modal_factura = !modal_factura'
          >
           Salir
          </v-btn>
        </v-card-actions>
      </v-card>
	  
	  
      <v-card v-if='!ver'>
          <v-card-title>
            <span class='headline'>Email</span>
          </v-card-title>
          <v-form ref='form'>
            <v-card-text>
              <v-container grid-list-md>
                <v-layout wrap>
                  <v-flex xs12>
                    <v-text-field label='Consecutivo factura' required    v-model='factura'  ></v-text-field>
                  </v-flex>
                </v-layout>
              </v-container>
              <small>*indicates required field</small>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color='blue darken-1' flat @click.native='modal_factura = false'>Cancelar </v-btn>
              <v-btn color='blue darken-1' flat @click.native='onSave'>Save</v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
     
    </v-dialog>			 



  </div>
  
  
  
</template>
<script type='module' src='src/pages/home/home.js'></script>

";

   echo html_entity_decode($HThome, ENT_NOQUOTES, "UTF-8");
?>

