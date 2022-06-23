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

	
	   <form-wizard @on-complete='onComplete'  v-if='sinistro'  title='Ingresar novedad de siniestro' subtitle='Busca el sinestro o la claca del vehiculo '
   step-size='sm' shape='circle'   color='gray'  error-color='#ff4949'>
    <tab-content title='Buscar sinestro ' icon='ti-user'
	:before-change=' validateFirstStep '>
	
	
     <el-form :inline='true'  id='miForm' :model='formInline' class='demo-form-inline' 
	 :rules='rules' ref='ruleForm'>
	  <h6>Ingresar Novedad </h6>
	      <br>
        <el-form-item label='Siniestro' prop='user'>
          <el-input v-model='formInline.user' placeholder='Ingresa el siniestro'></el-input>
        </el-form-item>
      </el-form>
	  

    </tab-content>
    <tab-content title='Tipo de novedad'  
	:before-change=' validarSlect ' icon='ti-settings'>
       		       <el-form label='Novedad' :inline='true'   :model='value' class='demo-form-inline' 
				   id='formSle' prop=' value'  >
  		<el-select
			v-model='value'
			filterable
			allow-create
			placeholder='Selecione un tipo de nvedad '>
			<el-option
			  v-for='item in tipo'
			  :key='item.id'
			  :label='item.nombre'
			  :value='item.id'>
    </el-option>
  </el-select>
    </tab-content>
	    <tab-content title='Imprimir pdf' icon='ti-check'>
      Imprimir
    </tab-content>

    <el-button type='primary' slot='prev'>Anterior</el-button>
    <el-button type='primary' slot='next'>Siguiente</el-button>
    <el-button type='primary' slot='finish'>Finalizar </el-button>
  </form-wizard>
  
  
  
	
	
 
 
 <el-dialog title="Shipping address" :visible.sync="dialogConfi">
 		 <el-form :inline='true' :model='formContN'  id='formCoN'
	   class='demo-form-inline' :rules='rulesConN' ref='formCoN'>
      <h6 class='row justify-content-center align-items-center'>Ingresar nueva cotización {{novedadId}}</h6>
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
          <el-input type="textarea" v-model='formContN.descripcion' placeholder='Ingresar  descripción'></el-input>
        </el-form-item>
		<br>
		 <el-form-item   label='Actividad Solicitante ' prop='actividadSoli'>
          <el-input type="textarea"  v-model='formContN.actividadSoli' placeholder='Ingresar actividad solicitamte'></el-input>
        </el-form-item>
		 <el-form-item   label='Actividad  Proveedor' prop='actividadProe'>
          <el-input type="textarea"  v-model='formContN.actividadProe' placeholder='Ingresar actividad proveedor'></el-input>
        </el-form-item>

      </el-form>
  <span slot="footer" class="dialog-footer">
    <el-button @click="dialogConfi = false">Cancel</el-button>
    <el-button type="primary" @click="ingresarCotizacionNueva">Confirm</el-button>
  </span>
</el-dialog>
    
	  
 
		
	    <v-overlay  :absolute="absolute"
          :value="overlay" >
      <v-progress-circular indeterminate size="64"></v-progress-circular>
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
		  placeholder='Ingresa el siniestro'></el-input>
        </el-form-item>
      </el-form>
    </tab-content>
	
<tab-content icon='fa fa-check-square-o' title='Ingresar novedad' :before-change='validateF'>
 
 <el-form :inline='true' :model='formInlin' class='demo-form-inline' :rules='rule' ref='ruleFo'>
      <h6 class='row justify-content-center align-items-center'>Ingresar novedad del vehiculo {{formInline.user}}</h6>
	     <br>
	  <el-form-item  label='Novedad' prop='user'>
          <el-input v-model='formInlin.user'  type="textarea"  placeholder='Ingresar Novedad'></el-input>
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
          <el-input type="textarea" v-model='formCont.descripcion' placeholder='Ingresar  descripción'></el-input>
        </el-form-item>
		<br>
		 <el-form-item   label='Actividad Solicitante ' prop='actividadSoli'>
          <el-input type="textarea"    v-model='formCont.actividadSoli' placeholder='Ingresar actividad solicitamte'></el-input>
        </el-form-item>
		 <el-form-item   label='Actividad  Proveedor' prop='actividadProe'>
          <el-input type="textarea"   v-model='formCont.actividadProe' placeholder='Ingresar actividad proveedor'></el-input>
        </el-form-item>

      </el-form>

	  </tab-content>
  <tab-content title='Imprimir pdf' icon='ti-check'>

<v-tabs
  color="cyan"
  darken-4
  fixed-tabs
  slider-color="yellow"
  v-model="tab"  
   icons-and-text
      class="elevation-2"
>
<v-tabs-slider></v-tabs-slider>

          <v-tab
            ripple
            class="primary--text">
            Ingresar detalles de la cotización  
        <v-icon>fact_check</v-icon>
        </v-tab>
	  
	  
          <v-tab
            ripple
            class="primary--text">
          
             Ver novedad
        <v-icon>fact_check</v-icon>
      </v-tab>

          <v-tab
           ripple
            class="primary--text"
          >
		  
			  Imprimir novedad
            <v-icon>picture_as_pdf</v-icon>
          </v-tab>

          <v-tab
            ripple
            class="primary--text"
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
   Ingresar detalle de la cotización {{cotizacion}}
    </v-chip>
        </v-col>
		
		<v-card   class="mx-auto">
	

	
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
								v-for="(item, index) in servicioList"
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
								v-for="item in clase"
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
								v-for="item in cobro"
                                 :key='item.id'							
								  :label='item.nombre'
								  :value='item.id'>
								</el-option>
								
							  </el-select>
							  
					  	</el-form-item>
													
					 <el-form-item  label='Cantidad' prop='cantidad'>
					  <el-input type="number" v-model='formformItenes.cantidad'   @input="Multiplicar"  placeholder='Ingresar  cantidad'></el-input>
					</el-form-item>
				
					 <el-form-item   label='Valor ' prop='valor'>
					  <el-input type="number"  @input="Multiplicar"  v-model='formformItenes.valor' placeholder='Ingresar  valor'></el-input>
					</el-form-item>
					 <el-form-item   label='Valor total' prop='valor_total'>
					  <el-input type="number" v-model='formformItenes.valor_total' placeholder='Ingresar  valortotal'></el-input>
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
								v-for="item in centro_operacion"
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
								v-for="item in centro_costo"
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
								v-for="item in flota"
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
					  <el-input type="textarea"  v-model='formformItenes.descripcion_item' placeholder='Ingresar descripcion item'></el-input>
					</el-form-item>

      </el-form>
 </v-container>
   <v-card-actions>
   
        <v-col cols='12'  class='row justify-content-center align-items-center' sm='12' md='12'>
         <v-btn
       :disabled='disabled == 1' 
      color="success"
      class="mr-4"
      @click="validar_ingresar_detalle"
    >
      Ingresar detalle de cotización 
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
    class="mx-auto"
    max-width="344"
    outlined
	shaped
	class="v-card v-sheet theme--light elevation-24"
  >
   <v-card-title>Imprimir pdf</v-card-title>
      <v-form
    ref="formpdf"
    v-model="valid"
    lazy-validation
  >
    <v-text-field
      v-model="nombrepdf"
      :counter="10"
      :rules="nombrepdfr"
      label="Nombre pdf"
      required
            outlined
            clearable
          ></v-text-field>

  

  

    <v-card-actions>
        <v-btn
      :disabled="!valid"
      color="success"
      class="mr-4"
      @click="validate"
    >
      Imprimir
	  
    </v-btn>
    </v-card-actions>
	</v-form>
  </v-card>
   <div id="resulta"></div>
  <br>
  
    </v-card>
  </v-tab-item>  
  
    <v-tab-item>
    <v-card flat>
	
	
       <v-card
    class="mx-auto"
    
    outlined
	shaped
	 icons-and-text
	class="v-card v-sheet theme--light elevation-24"
  >
   <v-card-title>Agregar una nueva cotización a la {{novedadId}}} </v-card-title>
     <v-data-table
    :headers="headers"
    :items="desserts"
    sort-by="calories"
    class="elevation-1"
  >
    <template v-slot:top>
      <v-toolbar flat color="white">
        <v-toolbar-title>Mis cotifacin</v-toolbar-title>
        <v-divider
          class="mx-4"
          inset
          vertical
        ></v-divider>
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" max-width="500px">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              color="primary"
              dark
              class="mb-2"
			   @click="dialogConfi = true"
            >Nueva cotización</v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12" sm="6" md="4">
                    <v-text-field v-model="editedItem.name" label="Dessert name"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <v-text-field v-model="editedItem.calories" label="Calories"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <v-text-field v-model="editedItem.fat" label="Fat (g)"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <v-text-field v-model="editedItem.carbs" label="Carbs (g)"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <v-text-field v-model="editedItem.protein" label="Protein (g)"></v-text-field>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
              <v-btn color="blue darken-1" text @click="save">Save</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>
    <template v-slot:item.actions="{ item }">
      <v-icon
        small
        class="mr-2"
        @click="editItem(item)"
      >
        mdi-pencil
      </v-icon>
      <v-icon
        small
        @click="deleteItem(item)"
      >
        mdi-delete
      </v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn color="primary" @click="initialize">Reset</v-btn>
    </template>
  </v-data-table>


  
    <v-card-actions>
        <v-btn
      :disabled="!valid"
      color="success"
      class="mr-4"
      @click="validate"
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
  



  </div>
  
  
  
</template>
<script type='module' src='src/pages/home/home.js'></script>