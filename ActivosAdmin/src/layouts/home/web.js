module.exports = {
    mixins:[Ajax],
    vuetify: new Vuetify(),	
    mounted: function () {
			for(var i=0;i<sessionStorage.length;i++)
					{  
					   this.tokenNick=sessionStorage.getItem(tokenItm2);  
					   this.tokenUser=sessionStorage.getItem(tokenItm);  
					   this.tokenPeril=sessionStorage.getItem(tokenItm1);  
					   this.tokenTabla=sessionStorage.getItem(tokenItm3);  
					    this.tokenEmail=sessionStorage.getItem(tokenItm4);  

					  }
					  

		mostrarDatos(tokenItm,tokenItm1,tokenItm2,tokenItm3,tokenItm4); 

   this.sesesion();
   
    },
    created() {
		
    },   
	
	 watch: {
      loader () {
        const l = this.loader
        this[l] = !this[l]

        setTimeout(() => (this[l] = false), 3000)

        this.loader = null
      },
    },

    data: function() {
        return {
		
			 loader: null,
        loading: false,
        loading2: false,
        loading3: false,
        loading4: false,
        loading5: false,
			mensaje:'',
			dr:false ,
			sesion:token[0],
			Tabla_usuario:'',
			valid:'',
			notSecion: false,
			dialog: false,
			word: false,
			dialog1: false,
			pintarhtml:[],
			session:{},
			pintarpintar:'',
			pintar:[],
			 dato:[],
			  valid: true,
			  placa: '',
			  mini:false,
			  nameRules: [
				v => !!v || 'La placa es requerida',
				v => (v && v.length <= 6) || 'Placa no puede ser mayor y menor 6 caracteres ',
			  ],
			  mante  : '',
			  emailRules: [
				v => !!v || 'Tipo de mantenimiento es requerida',
			  ],
			   fav: true,
			  menu: false,
			  message: false,
			  hints: true,
			
			  select: null,
			  tipo1: [
				'Mantenimiento preventido',
				'Mantenimiento por sinestro parcial perdida',
				'Mantenimiento por el cliente',
			  ],
			  checkbox: false,
					title: 'AOA',
					collapseOnScroll: true,
				  drawer: false,
			items: [
				  {
					icon: "mdi-view-dashboard",
					title: "Inicio",
					to: "/dashboard",
				  },
				  {
					icon: "mdi-account",
					title: "Peril",
					to: "/usuarios",
					items: [
					  {
						title: "Ver perfil",
						icon: "mdi-clipboard-outline",
						to: "/usuarios",
					  },
					  {
						title: "Cambiar contraseña",
						icon: "mdi-format-font",
						to: "/contraseña",
					  },
					],
				  },
				  {
					title: "Novedad",
					icon: "mdi-clipboard-outline",
					to: "/home",
					items: [
					  {
						title: "Mis novedades",
						icon: "mdi-clipboard-outline",
						to: "/mis_novedades",
					  },
					 {
						title: "Reportes novedades",
						icon: "mdi-clipboard-outline",
						to: "/home",
					  },
					  {
						title: "Reportes novedades",
						icon: "mdi-clipboard-outline",
						to: "/ingresar_novedad",
					  },
					],
				  },
				  {
					title: "Acta de entrega ",
					icon: "mdi-bell",
					to: "/acta_entrega",
				  },
				  {
					title: "Pequeños asesorios",
					icon: "mdi-chart-bubble",
					to: "/planos",
				  },
				  
				],
				
				  mini: true,
				  tokenNick:'',
				   tokenPeril:'',
				    tokenUser:'',
			         tokenTabla:'',
					   tokenEmail:'',
                     overlay :  false,
     
          }
      },
      
  
    methods: {
		 Buscar_session(){
			
       			    var datos = { 
				   'usuario':this.tokenUser,
				     'tabla_usuario':this.tokenTabla
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=datos_session',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								   
							   }else{

							      TOKEN[0] =  JSON.parse(info);  
							    sessionStorage.setItem(tokenItm4,TOKEN[0].email); 
						   }
					})  
	 },
		
	 sesesion(){
		 
	 if (sessionStorage.getItem(tokenItm2) == null && sessionStorage.getItem(tokenItm1) == null) 

			   {
				  this.$router.push('login');
				  
				  }else{
		
			    	 this.Buscar_session();
      
				   this.dialog1 = true;
				   
					
			   }
       
	 
	    
	 },
		ingresar () {
        window.open( "../Control/operativo/", "ventana1", "height=500,width=1000,left=300,location=yes,menubar=no,resizable=no,scrollbars=yes,status=no,titlebar=yes,top=150" );

      }, 
	  
		validate () {
        this.$refs.form.validate()
         alert("Desacargar archivo");
      },
      reset () {
        this.$refs.form.reset()
      },
      resetValidation () {
        this.$refs.form.resetValidation()
      },
	  
	  cerrarSession () {
      borrarTodo();
	  this.$router.push('login');
      },
    
    
 }
}