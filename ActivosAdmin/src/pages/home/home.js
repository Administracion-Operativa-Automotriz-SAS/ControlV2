module.exports = {
	
   mixins:[Ajax],
  mounted: function () {
	  this.tokemes();
	  if(sessionStorage.getItem(tokenItm1)== 'OPERARIO FLOTAS'){
		  this.formInline.user = siniestro;
	      this.formInlin.ciudad_reporte = declarante_ciudad;
	      this.formInlin.tele_reporte = declarante_telefono;
	      this.formInlin.email_reporte = declarante_email;
	      this.formInlin.reportado = declarante_nombre;
		  this.solicitar = true;
		  this.placa = false;
		  this.sinistro = true;
		  this.sele = false;
		  this.activeCall = true;
		  this.buscar_novedad_operario();
		  id_noveda = this.id_noveda_ope;
		  this.activeTabIndex = 2;
	      this.buscar();
	      this.Buscar_tipo();
	  }
	  
	   if(sessionStorage.getItem(tokenItm1)== 'CALL CENTER'){
 
		  this.solicitar = true;
		  this.placa = false;
		  this.sinistro = true;
		  this.sele = false;
		  this.activeCall = true;
	  
	  this.formInline.user = siniestro;
	
	  this.formInlin.ciudad_reporte = declarante_ciudad;
	  this.formInlin.tele_reporte = declarante_telefono;
	  this.formInlin.email_reporte = declarante_email;
	   this.formInlin.reportado = declarante_nombre;


			  this.tabla_usuario = 'usuario_callcenter';
		  sessionStorage.setItem(tokenItm3,this.tabla_usuario); 
		  this.tabla_usuario =sessionStorage.getItem(tokenItm3);  
			this.$message({
				type: 'success',
				message: 'Agregar la novedad del siniestro '+this.formInline.user 
		});
		this.activeTabIndex = 1;
	       this.buscar();
	   	this.Buscar_tipo();

	  }
	  
	  
	  
 
	 var currentdate = new Date();
			var datetime = "Last Sync: " + currentdate.getDay() + "/"+currentdate.getMonth() 
			+ "/" + currentdate.getFullYear() + " @ " 
			+ currentdate.getHours() + ":" 
			+ currentdate.getMinutes() + ":" + currentdate.getSeconds();  
			
			




            // Set the initial number of items
            this.totalRows = this.cotizacion_novedad_array.length

            axios
                .get('http://localhost:3000/ticket')
                .then(response => (this.cotizacion_novedad_array = response.data));

        },
  computed: {
	  
	  
	 formTitle () {
      return this.editedIndex === -1 ? 'Editar  cotizacion' : 'Ingresar cotizacion '
    },
            sortOptions() {
                // Create an options list from our fields
                return this.cotizacion_novedad_array_labe
                    .filter(f => f.sortable)
                    .map(f => {
                        return { text: f.label, value: f.key }
                    })
            }
        },

  data: function() {
    return {
		 disableInputBool: true,
		activeCall: false,
		descriCall: false,
		usuarioencargado:false,
		activeTabIndex : 0,
		editar:false,
		pinta_solisitud:1,
		buscar_novedad_operario_array:[],
		usuarioEncargado_array:[],
		solicitar:false,
		dialog: true,
		tokenEmail:'',
		id_noveda_ope:'',
		tabla_usuario:'',
		filters: { 'name': [], 'calories': [], 'status': [] },
		activeFilters: {},
	    novedad_pdf: [], 
		desserts: [],
		info_encargado: [],
		editedIndex: -1,
		editedItem: {
		  id: '',
		  requision: 0,
		  proveedor: 0,
		  descripcion: 0,
		  actividad_provedor: 0,
		   actividad_solitante: 0,
		},
    defaultItem: {
      name: '',
      calories: 0,
      fat: 0,
      carbs: 0,
      protein: 0,
    },
		  items: [],
		  
                fields: [
                    { key: 'ticket', label: 'Ticket Id', sortable: true, sortDirection: 'desc' },
                    { key: 'Requestor', label: 'Requestor Id', sortable: true, class: 'text-center' },
                    { key: 'ITOwner', label: 'IT Owner Id', sortable: true },
                    { key: 'FiledAgainst', label: 'Filed Against', sortable: true },
                    { key: 'TicketType', label: 'Ticket Type', sortable: true },
                    { key: 'Severity', label: 'Severity', sortable: true },
                    { key: 'Priority', label: 'Priority', sortable: true },
                    { key: 'Ticket Creation Date', label: 'Date Created', sortable: true },
                    { key: 'actions', label: 'Actions' }
                ],
                totalRows: 1,
                currentPage: 1,
                perPage: 5,
                pageOptions: [5, 10, 15],
                sortBy: null,
                sortDesc: false,
                sortDirection: 'asc',
                filter: null,
                modalInfo: { title: '', content: '' },
            
		ver_tabla:false,
		   infoModal: {
          id: 'info-modal',
          title: '',
          content: ''
        },   
		  show: false,
		  cotizacion_id:'',
        variants: ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'light', 'dark'],
        headerBgVariant: 'dark',
        headerTextVariant: 'light',
        bodyBgVariant: 'light',
        bodyTextVariant: 'dark',
        footerBgVariant: 'warning',
        footerTextVariant: 'dark',
		totalRows: 1,
	     codigo:'',
		 tokenNick:'',
		    tipo_servicio:'',
				   tokenPeril:'',
				    tokenUser:'',
       tokenCiudad:'',
	   asegurado_nombre:'',
		novedadid:'',
		detalle_id_array:[],
		vehículo_array:[],
		buscar_proveedor_id_array:[],
		cotizacion_novedad_array:[],
		servicio_array:[],
		ciudad_ciudad_soli:[],
		tipo_novedad_array:[],
		centro_costo_array:[],
		idcotizacion:[],
		cotizacionid:'',
		array_factura:[],
		factura:'',
        currentItems: [],
        cotizacion_novedad_array_labe: [
          { key: 'id'  ,label :'id ' , sortable: true, sortDirection: 'desc' }, 
		  { key: 'requision',label :'Requisición  ' , sortable: true, class: 'text-center bark' }, 
		  { key: 'proveedor' ,label :'Proveedor  ', sortable: true},
		  { key: 'descripcion',label :'Descripcion ', sortable: true },
		  { key: 'actividad_solitante' ,label :'Actividad Solitante ', sortable: true,  class: 'text-center'  },
		  { key: 'actividad_provedor' ,label :'Actividad Solitante ', sortable: true ,  class: 'text-center' },
		  { key: 'detalle' ,label :'Detalle ', sortable: true},
		  { key: 'opciones',label :'Opciones ', sortable: true }
		  
        ],
		
		  proveedor_labe: [
          { key: 'identificacion' }, { key: 'nombre' }, { key: 'ciudad' }, { key: 'telefono1' },
		  { key: 'celular' },{ key: 'contacto' },{ key: 'email' }
		  
        ],
		
		 detalle_labe: [
          { key: 'tipo' }, { key: 'clase' }, { key: 'tipoItem' }, 
		  { key: 'observaciones' },{ key: 'cantidad' }, { key: 'valor' },
		  { key: 'valor_total' }
		  
        ],
		
		mensaje_factura:'',
		centro_costo:[],
			marca_array:[],
			 ciudad_array:[],
		 absolute: true,
		  ver: false,
	 modal_factura: false,
		  word: true,
      overlay: false,
	    alertCoti:false,
		 tab: null,
        novedadId:'',
        icons: false,
        centered: false,
        grow: false,
        vertical: false,
        prevIcon: false,
        nextIcon: false,
        right: false,
        tabs: 3,
		         cotizacion: '',
		idnovedad:[],
		 formInlin: {
			 
           user: '',
           region: '',
		   checkState:'',
		   encargado:'',
		   cierre:'',
		   tipo_cierre:'',
		   reportado:'',
		   tele_reporte:'',
		   ciudad_reporte:'',
		   email_reporte:''
		   
         },
		 linea_array:[],
		 	 linea_array:[],
		 formformItenes: {
           tipoBien: '',
           tipoItem: '',
		   clase: '',
           cobro: '',
		   valor:'',
		   cantidad:'',
		   validad_total:'',
		   centro_costo:'',
		   factor:'',
		   proyecto_placa:'',
		   descripcion_item:'',
		   emailRules: [v => !!v || 'Name is required'],
         },
		  
		 rulesformItenes: {
			 tipoBien: [{
             required: true,
             message: 'Por favor debe selecinar el tipo de bien   ',
             trigger: 'change'
           }],
		   	 tipoItem: [{
             required: true,
             message: 'Por favor debe selecinar el tipo de items  ',
             trigger: 'change'
           }],	 	   
        	 clase: [{
             required: true,
             message: 'Por favor debe selecinar la clase  ',
             trigger: 'change'
           }],
		    cobro: [{
             required: true,
             message: 'Por favor debe selecinar el cobro  ',
             trigger: 'change'
           }],
		     cantidad: [{
             required: true,
             message: 'La cantidad es requerida ',
             trigger: 'blur'
           }, {
             min: 0,
             max: 100,
             message: 'Debe ingresar de 1 a 25 caracteres ',
             trigger: 'blur'
           }],
		     valor: [{
		      min: 0,
             max: 100,
             message: 'La activida de proveedor  es requerida ',
             trigger: 'blur'
           }, {
             min: 0,
             max: 100,
             message: 'Debe ingresar de 5 a 25 caracteres ',
             trigger: 'blur'
           }],  
             valor_total: [{
             message: 'La activida de proveedor  es requerida ',
             trigger: 'blur'
           }],  
		     centro_operacion: [{
             message: 'Por favor debe selecinar el centro de operación ',
             trigger: 'blur'
           }],
		    centro_costo: [{
             required: true,
             message: 'Por favor debe selecinar el centro de costo ',
             trigger: 'blur'
           }],
		    factor: [{
             required: true,
             message: 'Por favor debe selecinar la factor ',
             trigger: 'blur'
           }],
		    proyecto_placa: [{
             required: true,
             message: 'Por favor debe selecinar la proyecto placa ',
             trigger: 'change'
           }],
		    descripcion_item: [{
             required: true,
             message: 'Por favor debe selecinar la descripcion de item ',
             trigger: 'change'
           }],
		    
         },
      
		 
		 
		formContN: {
           proveedor: '',
           descripcion: '',
		    actividad_solitante: '',
           actividad_provedor: '',
         },
		 
		 rulesConN: {
			       descripcion: [{
             required: true,
             message: 'La despcrición es requerida ',
             trigger: 'blur'
           }, {
             min: 6,
             max: 45,
             message: 'Debe ingresar de 5 a 25 caracteres ',
             trigger: 'blur'
           }],
			 proveedor: [{
             required: true,
             message: 'Por favor debe selecinar el provedor  ',
             trigger: 'change'
           }],
		   	 	   
     
		     actividadSoli: [{
             required: true,
             message: 'La activida de solicitante es requerida ',
             trigger: 'blur'
           }, {
             min: 6,
             max: 45,
             message: 'Debe ingresar de 5 a 25 caracteres ',
             trigger: 'blur'
           }],
		     actividadProe: [{
             required: true,
             message: 'La activida de proveedor  es requerida ',
             trigger: 'blur'
           }, {
             min: 6,
             max: 45,
             message: 'Debe ingresar de 5 a 25 caracteres ',
             trigger: 'blur'
           }],  
           
         },
      
		formCont: {
           proveedor: '',
           descripcion: '',
		    actividadSoli: '',
           actividadProe: '',
         },
		 
		 rulesCon: {
			       descripcion: [{
             required: true,
             message: 'La despcrición es requerida ',
             trigger: 'blur'
           }, {
             min: 6,
             max: 45,
             message: 'Debe ingresar de 5 a 25 caracteres ',
             trigger: 'blur'
           }],
			 proveedor: [{
             required: true,
             message: 'Por favor debe selecinar el provedor  ',
             trigger: 'change'
           }],
		   	 servicio:[],	   
     
		     actividadSoli: [{
             required: true,
             message: 'La activida de solicitante es requerida ',
             trigger: 'blur'
           }, {
             min: 6,
             max: 45,
             message: 'Debe ingresar de 5 a 25 caracteres ',
             trigger: 'blur'
           }],
		     actividadProe: [{
             required: true,
             message: 'La activida de proveedor  es requerida ',
             trigger: 'blur'
           }, {
             min: 6,
             max: 45,
             message: 'Debe ingresar de 5 a 25 caracteres ',
             trigger: 'blur'
           }],  
           
         },
      
	  rule: {
           user: [{
             required: true,
             message: 'La novedad es requerida ',
             trigger: 'blur'
           }, {
             min: 2,
             max: 45,
             message: 'Debe ingresar de 2 a 45 caracteres ',
             trigger: 'blur'
           }],
           region: [{
             required: true,
             message: 'Por favor debe selecinar un tipo de novedad ',
             trigger: 'change'
           }],
		   checkState: [{
             required: true,
             message: 'Por favor selecione un tipo de cierre  ',
             trigger: 'change'
           }],
		 cierre: [{
             required: true,
             message: 'La descripción de cierre  es requerida ',
             trigger: 'blur'
           }, {
             min: 2,
             max: 45,
             message: 'Debe ingresar de 2 a 45 caracteres ',
             trigger: 'blur'
           }],
         },
		 novedad_siniestro: [],
		  novedad: [],
		 novedadValidateForm: {
          novedad: ''
        },
		value: '',
		tipo:[],
		  filterForm: {
                client: ''
           },
		  formplaca: {
           placa: '',
         },  
		 
		    rul: {
           placa: [{
             required: true,
             message: 'Por favor ingrese el placa',
             trigger: 'blur'
           }, {
             min: 5,
             max: 13,
             message: 'La longitud debe ser de 5 a 13',
             trigger: 'blur'
           }],
         },
		 sele:true,
		   formSlect: {
           value: '',
         },
		  ruleSelct: {
           value: '',
         },
		 rulesSelect: {
           value: [{
             required: true,
             message: 'Por favor ingrese el siniestro',
             trigger: 'blur'
           }, {
             min: 5,
             max: 13,
             message: 'La longitud debe ser de 5 a 13',
             trigger: 'blur'
           }],
         },
		 loading: false,
		  formInline: {
           user: '',
         },
		 validated:true,
         rules: {
           user: [{
             required: true,
             message: 'Por favor ingrese el siniestro',
             trigger: 'blur'
           }, {
             min: 5,
             max: 13,
             message: 'La longitud debe ser de 5 a 13',
             trigger: 'blur'
           }],
         },
      nombrepdfr: [
        v => !!v || 'Nombre es requerido de pdf ',
        v => (v && v.length <= 10) || 'Bede ingresar al nombre de PDF',
      ],
	  servicioList:[],
       name: '',
	   			valid:'',
      email: '',
      select: null,
	  validated:true,
      checkbox: false,
	   val: null,
      placa: false ,
	  sinistro: false,
	   pintar: ['Placa','Sinistro'],
	   bien: [
	   {
		   nombre: 'Bien',
		   id:1,
	   },
	  
	   {
	     nombre: 'Servicio',
		   id:2,
	   }
	   ],
	   
	      clase: [
		
	   {
		   nombre: 'Correctivo',
		   id:0,
	   },
	 
	  
	   {
	     nombre: 'Administrativo',
		   id:1,
	   }
	   ],
  cobro: [
		    {
		 nombre: 'Selecinar un cobro',
		   id:'',
	   },
	   {
		   nombre: 'Con cobro',
		   id:'C',
	   },
	   {
		   nombre: 'Sin cobro',
		   id:'S',
	   },
	
	   ],
	   
	     flota: [
		    {
		 nombre: 'Selecinar una flota',
		   id:'',
	   },
	   {
		   nombre: 'Flota',
		   id:'Flota',
	   },
	   
	
	   ],
	   
	   

	      stepIndex:0,
                formError: false,
                purchases: {
                    purpose: 'internal',
                    internal: {
                        preferredShippingAddress: null,
                    },
                },
                title: '',
                subtitle: '',
				formNovedad: {
					  novedad: ''
					},
					nombrepdf:'',
					proveedor:[],
					 formy: {},
					 dialogConfi:false,
      ruls: {
        nombre: [
          {
            required: true,
            message: "Please input title",
            trigger: "blur"
          }
        ],
	  },
        disabled: 1,
		 disabled1: 0,
	   centro_operacion:[],
	        dialog: false,
      headers: [
        {
          text: 'Dessert (100g serving)',
          align: 'start',
          sortable: false,
          value: 'name',
        },
        { text: 'Calories', value: 'calories' },
        { text: 'Fat (g)', value: 'fat' },
        { text: 'Carbs (g)', value: 'carbs' },
        { text: 'Protein (g)', value: 'protein' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      desserts: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0,
      },
      defaultItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0,
      },
	
	}
},
 
 watch: {
		formTitle () {
           return this.editedIndex === -1 ? 'Nueva contización ' : 'Editar contización'
          },
      overlay (val) {
        val && setTimeout(() => {
          
        }, 3000)
      },
     dialog (val) {
        val || this.close()
      },
    },
	 created () {
      this.initialize()
    },

 methods: {
	  
	   tokemes() {
	       this.tokenNick=sessionStorage.getItem(tokenItm2);  
		   this.tokenUser=sessionStorage.getItem(tokenItm);  
		   this.tokenPeril=sessionStorage.getItem(tokenItm1);  
		   this.tokenCiudad=sessionStorage.getItem(tokenItm3);  
		   this.tokenEmail=sessionStorage.getItem(tokenItm4); 
	 },
	 initialize () {
      this.desserts = [
        {
          name: 'Frozen Yogurt',
          calories: 159,
          fat: 6.0,
          carbs: 24,
          protein: 4.0,
          status: 'DIET'
        },
        {
          name: 'Ice cream sandwich',
          calories: 237,
          fat: 9.0,
          carbs: 37,
          protein: 4.3,
          status: 'NO DIET'
        },
        {
          name: 'Eclair',
          calories: 262,
          fat: 16.0,
          carbs: 23,
          protein: 6.0,
          status: 'DIET'
        },
        {
          name: 'Cupcake',
          calories: 305,
          fat: 3.7,
          carbs: 67,
          protein: 4.3,
          status: 'NO DIET'
        },
        {
          name: 'Gingerbread',
          calories: 356,
          fat: 16.0,
          carbs: 49,
          protein: 3.9,
          status: 'DIET'
        },
        {
          name: 'Jelly bean',
          calories: 375,
          fat: 0.0,
          carbs: 94,
          protein: 0.0,
          status: 'NO DIET'
        },
        {
          name: 'Lollipop',
          calories: 392,
          fat: 0.2,
          carbs: 98,
          protein: 0,
          status: 'NO DIET'
        },
        {
          name: 'Honeycomb',
          calories: 408,
          fat: 3.2,
          carbs: 87,
          protein: 6.5,
          status: 'NO DIET'
        },
        {
          name: 'Donut',
          calories: 452,
          fat: 25.0,
          carbs: 51,
          protein: 4.9,
          status: 'FAT DIET'
        },
        {
          name: 'KitKat',
          calories: 518,
          fat: 26.0,
          carbs: 65,
          protein: 7,
          status: 'FAT DIET'
        },
      ];
      //this.initFilters()
    },
    
    initFilters() {
      for (col in this.filters) {
        this.filters[col] = this.desserts.map((d) => { return d[col] }).filter(
          (value, index, self) => { return self.indexOf(value) === index }
        )
      }
      // TODO restore previous activeFilters before add/remove item
      this.activeFilters = Object.assign({}, this.filters)
      /*if (Object.keys(this.activeFilters).length === 0) this.activeFilters = Object.assign({}, this.filters)
      else {
        setTimeout(() => {
          console.log(this.activeFilters)
          //this.activeFilters = Object.assign({}, this.filters)
        }, 1)
      }*/
    },
    
    toggleAll (col) {
      this.activeFilters[col] = this.desserts.map((d) => { return d[col] }).filter(
        (value, index, self) => { return self.indexOf(value) === index }
      )
    },
    
    clearAll (col) {
      this.activeFilters[col] = []
    },

   

    eliminarCotizacion(item) {
              var datos = { 
				   'id':item.id,   
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=eliminarNovedadRequisicion',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								 this.resulIngres = 'No sé pudo eliminar la novedad';
							   }else{
								        this.overlay =  false; 
								   this.$confirm('Desea eliminar la cotización?', 'Eliminar', {
									  confirmButtonColor: '#DD6B55',
									  confirmButtonText: 'Aceptar eliminación',
									  cancelButtonColor: '#42A5',
									  cancelButtonText: 'Agregar nueva cotización ',
									  type: 'info',
									  center: true
									}).then(() => {
										 this.dialogConfi = false;
									  this.$message({
									
										type: 'success',
										message: 'Cotización fue eliminada con exito'
									  });
									}).catch(() => {
										 this.dialogConfi = true;
									  this.$message({
										type: 'info',
										message: 'Agregar una nueva cotización'
									  });
									});
								 
									   this.buscar_cotizacion_novedad();
						   }
					})  
    },

  
	  info(item, index, button) {
                this.modalInfo.title = `Row index: ${index}`
                this.modalInfo.content = JSON.stringify(item, null, 2)
                this.$root.$emit('bv::show::modal', 'modalInfo', button)
            },
            resetModal() {
                this.modalInfo.title = '';
                this.modalInfo.content = ''
            },
            onFiltered(filteredItems) {
                // Trigger pagination to update the number of buttons/pages due to filtering
                this.totalRows = filteredItems.length
                this.currentPage = 1
            },
	       initialize () {
        this.desserts = [
          {
            name: 'Frozen Yogurt',
            calories: 159,
            fat: 6.0,
            carbs: 24,
            protein: 4.0,
          },
          {
            name: 'Ice cream sandwich',
            calories: 237,
            fat: 9.0,
            carbs: 37,
            protein: 4.3,
          },
          {
            name: 'Eclair',
            calories: 262,
            fat: 16.0,
            carbs: 23,
            protein: 6.0,
          },
          {
            name: 'Cupcake',
            calories: 305,
            fat: 3.7,
            carbs: 67,
            protein: 4.3,
          },
          {
            name: 'Gingerbread',
            calories: 356,
            fat: 16.0,
            carbs: 49,
            protein: 3.9,
          },
          {
            name: 'Jelly bean',
            calories: 375,
            fat: 0.0,
            carbs: 94,
            protein: 0.0,
          },
          {
            name: 'Lollipop',
            calories: 392,
            fat: 0.2,
            carbs: 98,
            protein: 0,
          },
          {
            name: 'Honeycomb',
            calories: 408,
            fat: 3.2,
            carbs: 87,
            protein: 6.5,
          },
          {
            name: 'Donut',
            calories: 452,
            fat: 25.0,
            carbs: 51,
            protein: 4.9,
          },
          {
            name: 'KitKat',
            calories: 518,
            fat: 26.0,
            carbs: 65,
            protein: 7,
          },
        ]
      },

	   solicitar_apro_req(item) {
          this.$confirm('Desea solicitar la converción de  cotización a requisición ?', 'Solicitar requisición', {
									  confirmButtonColor: '#DD6B55',
									  confirmButtonText: 'Aceptar',
									  cancelButtonColor: '#42A5',
									  cancelButtonText: 'Cancelar ',
									  type: 'info',
									  center: true
									}).then(() => {
										
									  this.$message({
										type: 'success',
										message: 'Cotización fue convertida con exito'
									  });
									    this.buscar_novedad_placa_pdf(item);
									}).catch(() => {
									   this.$message({
										type: 'warning ',
										message: 'No puede solicitada la requisición '
									  });
									});
								
      },
	  
	  solicitar_requicion(item) {
		  
      var datos = { 
				   'para':'davidduque@aoacolombia.com;sergiocastillo@aoacolombia.com ',   
				   'copía':this.tokenEmail,    
					'asunto':'Solicitud de converción de novedad'+this.novedadId+'de la cotización'+item.id,    
					'idnovedad':this.novedadId,
					'cotizacion_id' : item.id,
					'contenido' : 100,
					'eUsuario':this.tokenUser ,
				     'array': JSON.stringify(this.novedad_pdf),
					 
					};
					this.ajax('https://sac.aoacolombia.com/enviar.php',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								    this.$message({
										type: 'success',
										message: 'El mensaje fue enviado con Exito a  '+ datos.id +' con copia a su correo ' +this.tokenEmail  +' gracias!!'
									  });
								   this.buscar_novedad_placa_pdf(item);
							   }else{  
							     this.$message({
										type: 'warning ',
										message: 'No puede enviar el mensaje  '
									  });

							   }
					})  
								 
		
	 },
	  buscar_novedad_placa_pdf(item){
			  this.overlay =  true;
       			    var datos = { 
				   'id_novedad':this.idnovedad[0]['id'],
				   'cotizacion_id':item.id
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_novedad_pdf',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
							   }else{
								
								  this.overlay =  false;   
							    this.novedad_pdf =  JSON.parse(info);  
								this.solicitar_requicion(item);
						   }
					})  
	 },
	 
	 usuarioEncargado(){
		 
			  this.overlay =  true;
       			    var datos = { 
				   'ciudad':ciudad_siniestro,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=usuarioEncargado',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								   
							   }else{
								  this.overlay =  false;   
								  
								      this.$message({
										type: 'info ',
										message: 'Selecione un encargado de la ciudad:'+ciudad_siniestro
									  });
									  
							      this.usuarioEncargado_array =  JSON.parse(info);  
								console.log(this.usuarioEncargado_array);  
						   }
					})  
	 },
	 
      editarCotizacion(item) {
        this.editedIndex = this.desserts.indexOf(item)
		this.formContN = item;
		console.log(this.formContN);
        this.dialogConfi = true;
		 this.editar = true;
	    this.editedIndex = 2;
      },
	  

  editarCotizacionNueva(){
		 var f = new Date();
              var datos = { 
			        
				   'novedad':this.formContN.novedad,   
				   'id':this.formContN.id,    
					'proveedor':this.formContN.proveedor,    
					'descripcion':this.formContN.descripcion,    
					'actividad_solitante':this.formContN.actividad_solitante,    
					'actividad_provedor':this.formContN.actividad_provedor
					
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=editarNovedadRequisicion',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								   
								 this.resulIngres = 'No sé pudo ingresar la novedad';
							   }else{
								        this.overlay =  false; 
								   this.$confirm('Desea Editar la cotización?', 'Editar', {
									  confirmButtonColor: '#DD6B55',
									  confirmButtonText: 'Aceptar',
									  cancelButtonColor: '#42A5',
									  cancelButtonText: 'Agregar nueva cotización ',
									  type: 'info',
									  center: true
									}).then(() => {
										 this.dialogConfi = false;
									  this.$message({
									
										type: 'success',
										message: 'Cotización fue editada con exito'
									  });
									}).catch(() => {
										 this.dialogConfi = true;
									  this.$message({
										type: 'info',
										message: 'Agregar una nueva cotización'
									  });
									});
								  this.formContN.proveedor ='';
					             this.formContN.descripcion ='';   
					             this.formContN.actividad_solitante = '';  
				                 this.formContN.actividad_solitante ='',
								  	 this.editar = false;
						   }
					})  
	 },
	 

      close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        if (this.editedIndex > -1) {
          Object.assign(this.desserts[this.editedIndex], this.editedItem)
        } else {
          this.desserts.push(this.editedItem)
        }
        this.close()
      },
 
	   open() {
        this.$confirm('This will permanently delete the file. Continue?', 'Warning', {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
          center: true
        }).then(() => {
          this.$message({
            type: 'success',
            message: 'Delete completed'
          });
        }).catch(() => {
          this.$message({
            type: 'info',
            message: 'Delete canceled'
          });
        });
      },
	    submitForm(formName) {
      this.$refs[formName].validate(async valid => {
        if (valid) {
          if (this.edit) {
            await this.editMovie(this.form);
          } else {
            await this.addMovie(this.form);
          }
          const { data } = await this.getMovies();
          this.$store.commit("setMovies", data);
          this.$emit("saved");
        }
        return false;
      });
    },
    cancel() {
      this.$emit("cancelled");
    },
    onChangeFileUpload($event) {
      const file = $event.target.files[0];
      const reader = new FileReader();
      reader.onload = () => {
        this.$refs.photo.src = reader.result;
        this.form.photo = reader.result;
      };
      reader.readAsDataURL(file);
    },
  
	    validateF() {
           return new Promise((resolve, reject) => {
             this.$refs.ruleFo.validate((valid) => {
               resolve(valid);
			       this.ingresaNovedad();
             });
           })

		   
         },
		     validateSinesCalll() {
           return new Promise((resolve, reject) => {
             this.$refs.ruleFocall.validate((valid) => {
               resolve(valid);
			   if(valid === true){
				   this.ingresaNovedadCall();
				     this.$message({
						type: 'success',
						message: 'El sistema esta validando la información para el registro  '
					  });
			   }else{
				    this.$message({
						type: 'error',
						message: 'Bebe ingresar todos los campos'
					  });
			   }
             }
			 );
           })
         },
		     validateSines() {
           return new Promise((resolve, reject) => {
             this.$refs.ruleFo.validate((valid) => {
               resolve(valid);
			   
			       this.ingresaNovedadSine();
             }
			 
			 );
           })

		   
         },
		 
		 	validate () {
        this.$refs.formpdf.validate()
         this.imprimir_novedad();
      },
	  
	   handleChange(val) {
		   this.descriCall = true;
		if(val === 'true'){
			this.$message({
				type: 'success',
				message: 'La novedad de finalizo con exito'
			  });
			  	this.usuarioencargado = false;
				this.formInlin.tipo_cierre = 0 ;
		}else{
			this.formInlin.tipo_cierre = 1 ;
			
			this.usuarioencargado = true;
			this.usuarioEncargado();
		}
			
			
			},


           validar_ingresar_detalle() {
           return new Promise((resolve, reject) => {
             this.$refs.forItenes.validate((valid) => {
               resolve(valid);
			       this.ingresar_detalle();
             });
           })

		   
         },
		  
		  
		    validateCotisacion() {
           return new Promise((resolve, reject) => {
             this.$refs.formCo.validate((valid) => {
               resolve(valid);
			   this.alertCoti = true;
			       this.ingresarCotizacion();
             });
           })

         },
		  validateCotisacionNueva() {
           return new Promise((resolve, reject) => {
             this.$refs.formCoN.validate((valid) => {
               resolve(valid);
			   this.alertCoti = true;
			  
			       this.ingresarCotizacionNueva();
             });
           })

         },
	   onSubmit() {
        this.ingresaNovedad();
      },
	  submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
			    this.ingresaNovedad(); 
          } else {
            return false;
          }
        });
      },
      resetForm(formName) {
        this.$refs[formName].resetFields();
      },
	   validarSlect(){
		     return new Promise((resolve, reject) => {
             this.$refs.formSle.validate((valid) => {
			   resolve(valid);
             });
           }) 
		   
	   },
	   printValue() {
      if( this.val == 'Placa' )
	  {
		  this.placa = true;
		  this.sinistro = false;
		  this.sele = false;

	  }
	  else{
		  this.placa = false;
		  this.sinistro = true;
		  this.sele = false;
	  }
      },
         onComplete: function() {
         },
         validateFirstStep() {
           return new Promise((resolve, reject) => {
             this.$refs.ruleForm.validate((valid) => {
			   this.buscar();
			   resolve(valid);
			   	this.Buscar_tipo();
       
             });
           })

         },
		  beforeTabSwitch: function(s) {
                if (s === 2) {
                    if (this.purchases.internal.preferredShippingAddress !== null) {
                        return true;
                    } else {

                    }
                }
            },
		 
		  validateFirsPlaca() {
        this.buscarplaca();
	   return new Promise((resolve, reject) => {
             this.$refs.ruleForm.validate((valid) => {
			 
			   resolve(valid);
			   	this.Buscar_tipo();
       
             });
           })
         },
		 
		  validateFirsSines() {
      return new Promise((resolve, reject) => {
             this.$refs.ruleForm.validate((valid) => {
			   this.buscar();
			   resolve(valid);
			   	this.Buscar_tipo();
       
             });
           })
         },
		
	 tipo_cobro(){
	 if(this.formformItenes.cobro == 'C'){
		 
		 this.modal_factura = true;
		
	 }
	   },
    	


		tipo_biien(){
			  this.overlay =  true;
			      
       			   var datos = { 
					'tipoServicio':this.formformItenes.tipoBien
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=tipo_servicio',datos,'fullUrl')
					.then(info => {
						 this.sinestro = info ;
							   if(!info)
					           {
							     setInterval(location.reload(),1000);
							   }else{
								     this.overlay =  false;
								   	this.servicioList=  JSON.parse(info);
										   this.centro_ope();
										      this.centro_cos();
										this.disabled  = 0;
										this.disabled1 = 0;
											  
									 
						   }
					})  
	   },
    	


		buscar(){
			
       			   var datos = { 
					'siniestro':this.formInline.user,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_siniestro',datos,'fullUrl')
					.then(info => {
						 this.sinestro = info ;
							   if(!info)
					           {
							     setInterval(location.reload(),1000);
							   }else{
							      this.sinestro_array =  JSON.parse(info);
								this.asegurado_nombre = this.sinestro_array.asegurado_nombre;
								this.tipo_servicio = this.sinestro_array.servicio;
								this.ciudad_siniestro();
								 this.centro_costo1();
								this.buscar_placa();
								this.ciudad_soli();
						   }
						 
						 
						   
					})  
       
        
	 },
	 tipo_servicio(){
			
			  this.overlay =  true;
       			   var datos = { 
					'servicio':this.sinestro_array.servicio,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=tipo_servicio_sini',datos,'fullUrl')
					.then(info => {
							   if(info == false)
					           {
				
							   }else{
					           
							   this.servicio_array =JSON.parse(info);
						   }
						 
						 
						   
					})  
       
        
	 },
	 
	 centro_costo1(){
			
			  this.overlay =  true;
       			   var datos = { 
					'centro_costo':this.sinestro_array.aseguradora,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=centro_costo',datos,'fullUrl')
					.then(info => {
							   if(info == false)
					           {
				
							   }else{
					          this.centro_costo_array=JSON.parse(info);
							 this.formformItenes.centro_costo= this.centro_costo_array.ccostos_uno;
							 this.formformItenes.factor = this.sinestro_array.aseguradora;
						   }
						 
						 
						   
					})  
       
        
	 },
	  info(item, index, button) {
        this.infoModal.title = `Row index: ${index}`
        this.infoModal.content = JSON.stringify(item, null, 2)
        this.$root.$emit('bv::show::modal', this.infoModal.id, button)
      },
	   resetInfoModal() {
        this.infoModal.title = ''
        this.infoModal.content = ''
      },
      onFiltered(filteredItems) {
        // Trigger pagination to update the number of buttons/pages due to filtering
        this.totalRows = filteredItems.length
        this.currentPage = 1
      },
	 	  ciudad_soli(){
			
			  this.overlay =  true;
       			   var datos = { 
					'codigo':this.sinestro_array.ciudad,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=ciudad_siniestro',datos,'fullUrl')
					.then(info => {
							   if(info == false)
					           {
				
							   }else{
					          this.ciudad_ciudad_soli  = JSON.parse(info);
						   }
						 
						 
						   
					})  
       
        
	 },
	 
	  	  ciudad_siniestro(){
			
			  this.overlay =  true;
       			   var datos = { 
					'codigo':this.sinestro_array.ciudad,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=ciudad_siniestro',datos,'fullUrl')
					.then(info => {
							   if(info == false)
					           {
				
							   }else{
					          this.ciudad_array=JSON.parse(info);
							 this.formformItenes.centro_operacion = this.ciudad_array.nombre;
						   }
						 
						 
						   
					})  
       
        
	 },
	 
	 	  Buscar_factura(){
			
			  this.overlay =  true;
       			   var datos = { 
					'factura':this.factura,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=Buscar_factura',datos,'fullUrl')
					.then(info => {
							   if(info == false)
					           {
								 	  this.overlay =  false;
									  this.ver = true;
									  this.mensaje_factura ="La factura no fue encontrada";
							         setInterval(
									this.modal_factura = false
									 ,1800);
									 this.ver = false;
							   }else{
								 this.array_factura = JSON.parse(info);  
							    	  this.overlay =  false;
									  this.ver = true;
									  this.mensaje_factura ="La factura fue asociada al cotización";
									 JSON.parse(info);  
									 this.consuecuetivo = this.factura;
							  
						   }
						 
						 
						   
					})  
       
        
	 },
	 

	   buscar_marca(){
       			   var datos = { 
					'marca':this.linea_array.marca
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_marca',datos,'fullUrl')
					.then(info => {
							   if(info == false)
					           {
								 
							   }else{
								 
									  this.marca_array = JSON.parse(info); 
							  
						   }
						 
						 
						   
					})  
       
        
	 },
	  buscar_linea(){
       			   var datos = { 
					'linea':this.vehículo_array.linea
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_linea',datos,'fullUrl')
					.then(info => {
							   if(info == false)
					           {
								 
							   }else{
								 
									  this.linea_array = JSON.parse(info); 
									  this.buscar_marca();
							  
						   }
						 
						 
						   
					})  
       
        
	 },
  
      	  buscar_placa(){
       			   var datos = { 
					'placa':this.sinestro_array.placa,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_placa',datos,'fullUrl')
					.then(info => {
							   if(info == false)
					           {
								 
							   }else{
								 
									  this.vehículo_array = JSON.parse(info); 
									  this.buscar_linea();
							  
						   }
						 
						 
						   
					})  
       
        
	 },
	  buscarplaca(){
			
			  this.overlay =  true;
       			   var datos = { 
					'placa':this.formInline.user,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_placa',datos,'fullUrl')
					.then(info => {
							   if(info == false)
					           {
								 
							     setInterval(location.reload(),1000);
							   }else{
								 
							    	  this.overlay =  false;
							  
						   }
						 
						 
						   
					})  
       
        
	 },

	    buscar_novedad_operario(){
			
			  this.overlay =  true;
       			   var datos = { 
				   'id':id_novedad
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_novedad_operario',datos,'fullUrl')
					.then(info => {
							   if(info == false)
					           {
								    this.$message({
										type: 'error',
										message: 'No tiene cargada una novedad resiente '
									  });
							
							   }else{
								   
								       this.$message({
										type: 'info',
										message: 'Tienes una novedad asignada '
									  });
								   
								      this.overlay =  false;
								this.buscar_novedad_operario_array =  JSON.parse(info);   
								    alert(  this.buscar_novedad_operario_array[0].novedad );
								     this.buscar_novedad_operario_array[0].encarga  =     this.formInlin.novedad;
							       this.buscar_novedad_operario_array[0].novedad  =     this.formInlin.user;
								     this.buscar_novedad_operario_array[0].region  =     this.formInlin.region;
									   this.buscar_novedad_operario_array[0].user  =     this.formInlin.user;
									     this.buscar_novedad_operario_array[0].user  =     this.formInlin.user;
										 
								   	   'id_sinistro':this.formInline.user,
				   'id_placa':this.sinestro_array.placa,
				   'id_tipo':this.formInlin.region,
					'novedad':this.formInlin.user,
				    'fecha_creacion': (f.getDate() + "/" + (f.getMonth() +1) + "/" + f.getFullYear()),
                    'email':TOKEN[0]['email'],  
				     'ciudad':this.ciudad_array.nombre,  
					'solicitante':tokenUser,     
					 'encargado':this.formInlin.encargado,
					'tipo_cierre':this.formInlin.tipo_cierre,
					'cierre':this.formInlin.cierre,
					'reportado':this.formInlin.reportado,
					 'tele_reporte':this.formInlin.tele_reporte,
					'ciudad_reporte':this.formInlin.ciudad_reporte,
					'email_reporte':this.formInlin.email_reporte
						   }
						 
					})  
	 },
	 
	   centro_ope(){
			
			  this.overlay =  true;
       			   var datos = { 
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=centros_operacion',datos,'fullUrl')
					.then(info => {
							   if(info == false)
					           {
								 
							  
							   }else{
							     this.overlay =  false;
								this.centro_operacion =  JSON.parse(info);   
						   }
						 
					})  
	 },
	 
	 	   centro_cos(){
			
			  this.overlay =  true;
       			   var datos = { 
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=centros_costo',datos,'fullUrl')
					.then(info => {
							   if(info == false)
					           {
								 
							  
							   }else{
							     this.overlay =  false;
								this.centro_costo =  JSON.parse(info);   
						   }
						 
					})  
	 },

	 Multiplicar(){
	 this.formformItenes.valor_total   =     this.formformItenes.valor * this.formformItenes.cantidad ;
	 },
	 
	 
	 
		Buscar_proveedor(){
			    this.overlay =  true;
						   var datos = { 
							};
							this.ajax('../Control/operativo/zdatos.php?Acc=buscar_proveedor',datos,'fullUrl')
							.then(info => {
									   if(!info)
									   {
									   }else{
										this.proveedor =  JSON.parse(info);   
										 this.overlay =  false;
										
								   }
							})  
			 },
	 
	 Buscar_tipo(){
		  this.overlay =  true;
       			   var datos = { 
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_tipo',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
							   }else{
								    this.overlay =  false;
							    this.tipo =  JSON.parse(info);   ;
						   }
					})  
	 },
	 ingresarCotizacion(){
		 this.overlay =  true;
		 var f = new Date();
		
              var datos = { 
				   'novedad':this.idnovedad[0]['id'],
					'proveedor':this.formCont.proveedor,    
					'descripcion':this.formCont.descripcion,    
					'actividad_solitante':this.formCont.actividad_solitante,    
					'actividad_provedor':this.formCont.actividad_provedor
					
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=ingresaNovedadRequisicion',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								   
								 this.resulIngres = 'No sé pudo ingresar la novedad';
							   }else{
								      this.overlay = false;   
								   this.$confirm('La cotización fue realizada con exito?', 'Exitosa', {
									  confirmButtonColor: '#DD6B55',
									  confirmButtonText: 'Aceptar',
									  cancelButtonColor: '#42A5',
									  cancelButtonText: 'Agregar nueva cotización ',
									  type: 'info',
									  center: true
									}).then(() => {
									  this.$message({
										type: 'success',
										message: 'Cotización fue creada con exito'
									  });
									}).catch(() => {
										 this.dialogConfi = true;
									  this.$message({
										type: 'info',
										message: 'Agregar una nueva cotización'
								       
									  });
									});
								  
								   this.buscar_cotizacion();
									 this.buscar_novedad_placa();
									this.Buscar_idnovedad();
								  
						   }
					})  
	 },
	 toggleRowDetails(row, data) {
      this.detailsMask = data.split(', ')
      row.toggleDetails()
    },
	 ingresarCotizacionNueva(){
		 this.overlay =  true;
		
		 var f = new Date();
              var datos = { 
				   'novedad':this.idnovedad[0]['id'],
					'proveedor':this.formContN.proveedor,    
					'descripcion':this.formContN.descripcion,    
					'actividad_solitante':this.formContN.actividad_solitante,    
					'actividad_provedor':this.formContN.actividad_provedor
					
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=ingresaNovedadRequisicion',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								   
								 this.resulIngres = 'No sé pudo ingresar la novedad';
							   }else{
								        this.overlay =  false; 
								   this.$confirm('La cotización fue realizada con exito?', 'Exitosa', {
									  confirmButtonColor: '#DD6B55',
									  confirmButtonText: 'Aceptar',
									  cancelButtonColor: '#42A5',
									  cancelButtonText: 'Agregar nueva cotización ',
									  type: 'info',
									  center: true
									}).then(() => {
										 this.dialogConfi = false;
									  this.$message({
									
										type: 'success',
										message: 'Cotización fue creada con exito'
									  });
									}).catch(() => {
										 this.dialogConfi = true;
									  this.$message({
										type: 'info',
										message: 'Agregar una nueva cotización'
								       
									  });
									});
								 this.formContN.proveedor ='';
					             this.formContN.descripcion ='';   
					             this.formContN.actividad_provedor = '';  
				                 this.formContN.actividad_solitante ='',
								 this.buscar_novedad_placa();
								  this.buscar_cotizacion();
								  
						   }
					})  
	 },
	 
	 cerrar(){
		 this.alertCoti = false;
	 },
	ingresaNovedadSine(){
		  this.overlay =  true;
		 var f = new Date();
              var datos = { 
				   'id_sinistro':this.formInline.user,
				   'id_placa':this.sinestro_array.placa,
				   'id_tipo':this.formInlin.region,
					'novedad':this.formInlin.user,
				    'fecha_creacion': (f.getDate() + "/" + (f.getMonth() +1) + "/" + f.getFullYear()),
                    'email':TOKEN[0]['email'],  
				     'ciudad':this.ciudad_array.nombre,  
					'solicitante':tokenUser,     
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=ingresaNovedad_siniestro',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								   
								 this.resulIngres = 'No sé pudo ingresar la novedad';
								 
							   }else{
								  	this.Buscar_proveedor();
                                	this.Buscar_novedad();
								    this.overlay =  false;
									this.editedIndex = -1;
								
						   }
					})  
	 },
	 ingresaNovedad(){
		  this.overlay =  true;
		 var f = new Date();
              var datos = { 
				   'id_placa':this.formInline.user,
				   'id_tipo':this.formInlin.region,
					'novedad':this.formInlin.user,
				    'fecha_creacion': (f.getDate() + "/" + (f.getMonth() +1) + "/" + f.getFullYear()),
                    'ciudad':TOKEN[0]['Ciudad'],  
					'solicitante':TOKEN[0]['Nombre'],     
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=ingresaNovedad',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								   
								 this.resulIngres = 'No sé pudo ingresar la novedad';
								 
							   }else{
								  	this.Buscar_proveedor();
                                	this.Buscar_novedad();
								    this.overlay =  false;
								this.editedIndex = -1;
						   }
					})  
	 },
	 
   ingresaNovedadCall(){
	  	  this.overlay =  true;
		  var f = new Date();
		  var datos = { 
				   'id_sinistro':this.formInline.user,
				   'id_placa':this.sinestro_array.placa,
				   'id_tipo':this.formInlin.region,
					'novedad':this.formInlin.user,
				    'fecha_creacion': (f.getDate() + "/" + (f.getMonth() +1) + "/" + f.getFullYear()),
                    'email':TOKEN[0]['email'],  
				     'ciudad':this.ciudad_array.nombre,  
					'solicitante':tokenUser,     
					 'encargado':this.formInlin.encargado,
					'tipo_cierre':this.formInlin.tipo_cierre,
					'cierre':this.formInlin.cierre,
					'reportado':this.formInlin.reportado,
					 'tele_reporte':this.formInlin.tele_reporte,
					'ciudad_reporte':this.formInlin.ciudad_reporte,
					'email_reporte':this.formInlin.email_reporte
			};
   			this.ajax('../Control/operativo/zdatos.php?Acc=ingresaNovedadCall',datos,'fullUrl')
			.then(info => {
						
							   if(!info)
					           {
								    this.$message({
										type: 'error',
										message: 'No sé púede ingresar la novedad   '
									  });
								 
							   }else{
									   this.idnovedad =  JSON.parse(info);  
							        	this.novedadId= this.idnovedad[0]['id'];
								     this.overlay =  false;
								     this.$message({
										type: 'success',
										message: 'La novedad fue registrado con exito  '
									  });
								  	 if(!this.formInlin.encargado ){
										 
										this.NovedadCall_envio(datos);
										
										 this.$message({
										type: 'info',
										message: 'Se envio el correo al encargado'
								     	  });
										}else{
										
										this.NovedadCall_envio(datos);
											 this.$message({
										type: 'info',
										message: 'Se envio de la novedad reportada por ' 
								       
									  });
										}
																		




                                    
						   }
					})  
	 },
	  buscar_encargado(){
		var id =  this.formInlin.encargado ;
		   this.overlay =  true;
       			    var datos = { 
					'id':id
				  
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_encargado',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								   
							   }else{
								this.overlay =  false;
							    this.info_encargado  =  JSON.parse(info);  
								console.log(this.info_encargado);
						   }
					})  
					
	 },
 
	NovedadCall_envio_encargado(datos) {
	   var dato = { 
		    'para':this.info_encargado.email+';davidduque@aoacolombia.com;sergiocastillo@aoacolombia.com ',   
		    'copía':datos.email+';'+datos.email_reporte,    
		   	'asunto':'Cierre de la novedad '+this.novedadId  +'  reportado por '+datos.reportado +'   y remitido por el usuario   '+ datos.solicitante +'  al operario '+this.info_encargado.nombre +' '+ this.info_encargado.apellido,    
		    'idnovedad':this.novedadId,
			 'reportado':this.formInlin.reportado,
			'ciudorte ' : this.formInlin.ciudad_reporte,
			'encanombre' :this.info_encargado.nombre +' '+ this.info_encargado.apellido,
			'contenido' : 101
			

			};
			this.ajax('https://sac.aoacolombia.com/enviar.php',dato,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								    this.$message({
										type: 'success',
										message: 'El mensaje fue enviado con Exito a  '+ dato.para +' con copia a su correo ' +dato.copia  +' gracias!!'
									  });
							   }else{  
						 	   this.$message({
										type: 'success',
										message: 'El mensaje fue enviado con Exito a  '+ dato.para +' con copia a su correo ' +dato.copia  +' gracias!!'
									  });
							     this.$message({
										type: 'warning ',
										message: 'No puede enviar el mensaje  '
									  });

							   }
					}) 
                                 this.$message({
										type: 'success',
										message: 'El mensaje fue enviado con Exito a  '+ dato.para +' con copia a su correo ' +dato.copia  +' gracias!!'
									  });					
	 },

	

   NovedadCall_envio(datos) {
	   var dato = { 
		    'para':datos.email_reporte + 'davidduque@aoacolombia.com;sergiocastillo@aoacolombia.com ',   
		    'copía':datos.email,    
		   	'asunto':'Cierre de la novedad '+this.novedadId  +'  reportado por '+datos.reportado +'   y  cerada por el usuario ',
			'idnovedad':this.novedadId,
			 'reportado':this.formInlin.reportado,
			'ciudorte ' : this.formInlin.ciudad_reporte,
			'encanombre' :this.info_encargado.nombre +' '+ this.info_encargado.apellido,
			'contenido' : 101
			

			};
			this.ajax('https://sac.aoacolombia.com/enviar.php',dato,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								    this.$message({
										type: 'success',
										message: 'El mensaje fue enviado con Exito a  '+ dato.para +' con copia a su correo ' +dato.copia  +' gracias!!'
									  });
							   }else{  
						 	   this.$message({
										type: 'success',
										message: 'El mensaje fue enviado con Exito a  '+ dato.para +' con copia a su correo ' +dato.copia  +' gracias!!'
									  });
							     this.$message({
										type: 'warning ',
										message: 'No puede enviar el mensaje  '
									  });

							   }
					}) 
                                 this.$message({
										type: 'success',
										message: 'El mensaje fue enviado con Exito a  '+ dato.para +' con copia a su correo ' +dato.copia  +' gracias!!'
									  });					
	 },

	 	
	 
	 Buscar_novedad(){
		   this.overlay =  true;
       			    var datos = { 
				  
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_novedad',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
							   }else{
								    this.overlay =  false;
							    this.idnovedad =  JSON.parse(info);  
								this.novedadId= this.idnovedad[0]['id'];
								
								 this.Buscar_idnovedad(this.novedadId);
						   }
					})  
	 },
	 
	 
	  buscar_cotizacion(){
		    this.overlay =  true;
       			    var datos = { 
				  
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_cotizacion',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
							   }else{
								     this.overlay =  false;
							    this.idcotizacion=  JSON.parse(info);  
								this.cotizacionid = this.idcotizacion[0]['id'];
								
								this.buscar_cotizacion_novedad();
							  
						   }
					})  
	 },
	 
	 ingresa_cotizacion(item){
		this.show = true ;
		 this.cotizacion_id = item.id;
	 },
	 
	 
	 
	    toggleDetails(row) {
			this.ver_tabla = false;
			   this.overlay =  true;
       			    var datos = { 
				      'id':row.proveedor,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_proveedor_id',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
							   }else{
								     this.overlay =  false;
							   this.buscar_proveedor_id_array =  JSON.parse(info);  
							 
							  
						   }
					})  
        if(row._showDetails){
          this.$set(row, '_showDetails', false)
        }else{
          this.currentItems.forEach(item => {
            this.$set(item, '_showDetails', false)
          })

          this.$nextTick(() => {
            this.$set(row, '_showDetails', true)
          })
        }
      },
	   
	  	    buscar_detalle_id(row) {
				this.ver_tabla = true;
			   this.overlay =  true;
       			    var datos = { 
				      'id':row.id,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_detalle_id',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								    this.pinta_solisitud = 1;
							   }else{
								     this.overlay =  false;
									  this.pinta_solisitud = 0;
							   this.detalle_id_array =  JSON.parse(info);  
							 
						   }
					})  
        if(row._showDetails){
          this.$set(row, '_showDetails', false)
        }else{
          this.currentItems.forEach(item => {
            this.$set(item, '_showDetails', false)
          })

          this.$nextTick(() => {
            this.$set(row, '_showDetails', true)
          })
        }
      },
	    buscar_cotizacion_novedad(){
		    this.overlay =  true;
       			    var datos = { 
				      'novedad':this.novedadId,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_cotizacion_novedad',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
							   }else{
								     this.overlay =  false;
							   this.cotizacion_novedad_array =  JSON.parse(info);  
							 
							  
						   }
					})  
	 },
	 
	 
	 
	   novedad_tipo(){
		    this.overlay =  true;
       			    var datos = { 
				      'tipo':this.novedad_siniestro.id_tipo,
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=novedad_tipo',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
							   }else{
								     this.overlay =  false;
							    this.tipo_novedad_array=  JSON.parse(info);  

							  
						   }
					})  
	 },
	 
	  Buscar_idnovedad(){
		    this.overlay =  true;
       			    var datos = { 
				   'innovedad':this.idnovedad[0]['id']
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_idnovedad',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
							   }else{          
								   
							    this.novedad_siniestro  =  JSON.parse(info);  
								  this.overlay =  true;
								  this.novedad_tipo();
						   }
					})  
	 },
       ingresar_detalle(){
              var datos = { 
				   'novedad_requisicion':this.cotizacion_id,
				   'tipo':this.formformItenes.tipoBien,
					'clase':this.formformItenes.clase,
					'tipoItem':this.formformItenes.tipoItem,
				    'valor':this.formformItenes.valor,
                    'observaciones':this.formformItenes.descripcion_item,
					'tipo_cobro':this.formformItenes.cobro,     
				     'cantidad':this.formformItenes.cantidad,
                    'centro_costo':this.formformItenes.centro_costo,
					'centro_operacion':this.formformItenes.centro_operacion,
					'valor_total':this.formformItenes.valor_total,     
					 'id_vehiculo':this.formInline.user,  
					'factor':this.formformItenes.factor,  
					'consuecuetivo':this.consuecuetivo
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=ingresar_detalle',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								     this.$message({
										type: 'warning',
										message: 'No sé puede ingresar el detalle de la contización'
									  });
								    this.show = false;
								   
							   }else{
								  	   this.$message({
										type: 'success',
										message: 'Se ingreso exitosamente el detalle de lacontización  '
									  });
									  
									  this.show = false;
						   }
					})  
	 },
	
	 
	 	  imprimir_novedad(){
			  
				window.open("http://app.aoacolombia.com/Administrativo/novedad.php?id_novedad="+this.idnovedad[0]['id']+"&novedad="+this.nombrepdf+"");
						  
	 },
	 
	 
	    buscar_novedad_placa(){
			  this.overlay =  true;
       			    var datos = { 
				   'id_novedad':this.idnovedad[0]['id']
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_novedad_placa',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
							   }else{
								  this.overlay =  false;   
							    this.novedad =  JSON.parse(info);  
						   }
					})  
	 },
		onSave() {
			  if (!this.factura) {
			  this.modal_factura = false
			  }else{
				  this.Buscar_factura();
				  
			  }
			  
			},
		


     }
   
  }
  
  
  

  
