module.exports = {
    mixins:[Ajax],
    vuetify: new Vuetify(),
	
	components: {
    'vue-recaptcha': VueRecaptcha
  },

    mounted: function () {
		
			for(var i=0;i<sessionStorage.length;i++)
					{  
					   this.tokenNick=sessionStorage.getItem(tokenItm2);  
					   this.tokenUser=sessionStorage.getItem(tokenItm);  
					   this.tokenPeril=sessionStorage.getItem(tokenItm1);  
					   this.tokenTabla=sessionStorage.getItem(tokenItm3);  
					    this.tokenEmail=sessionStorage.getItem(tokenItm4);  

		    }
        this.Buscar_session();
	
				 this.buscar_novedad();
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
			   id_encargado:'',
		  overlay: false,
			 totalRows: 1,
                currentPage: 1,
                perPage: 5,
                pageOptions: [5, 10, 15],
                sortBy: null,
                sortDesc: false,
                sortDirection: 'asc',
                filter: null,
				  tokenNick:'',
				   tokenPeril:'',
				    tokenUser:'',
			         tokenTabla:'',
					   tokenEmail:'',
                modalInfo: { title: '', content: '' },
		  encargado_novedad_array_labe: [
          { key: 'id_novedad'  ,label :'Id Novedad ' , sortable: true, sortDirection: 'desc' }, 
		  { key: 'reportado',label :'Reportado por  ' , sortable: true, class: 'text-center bark' }, 
		  { key: 'tele_reporte' ,label :'Telefono Reportado  ', sortable: true},
		  	  { key: 'email_reporte' ,label :'Email Reportado  ', sortable: true},
		  { key: 'ciudad_reporte',label :'Ciudad Reportado ', sortable: true },
		  { key: 'id_sinistro' ,label :' Sinistro ', sortable: true,  class: 'text-center'  },
		  { key: 'solicitante' ,label :' Solicitante ', sortable: true ,  class: 'text-center' },
		  { key: 'nombre_tipoN' ,label :'Tipo de Novedad ', sortable: true},
		   { key: 'novedad' ,label :'Novedad', sortable: true},
		  { key: 'cierre',label :'Descripción cierre ', sortable: true },
		  { key: 'tipo_cierre' ,label :'Tipo de cierre ', sortable: true,  class: 'text-center'  },
		  { key: 'nombre_opera' ,label :' Nombre operario ', sortable: true ,  class: 'text-center' },
		  { key: 'opciones',label :'Opciones ', sortable: true }
		  
        ],
		encargado_array:[],
		encargado_novedad_array:[],
	       	 loader: null,
        loading: false,
        loading2: false,
        loading3: false,
        loading4: false,
        loading5: false,
    
     
          }
      },
      
 
   
    methods: {
	home () {
	  this.$router.push('home');
      },
	   onFiltered(filteredItems) {
                // Trigger pagination to update the number of buttons/pages due to filtering
                this.totalRows = filteredItems.length
                this.currentPage = 1
            },

	  Buscar_session(){
			
       			    var datos = { 
				   'usuario':this.tokenNick,
				     'tabla_usuario':this.tokenTabla
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=datos_session_id',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
								   
							   }else{

							    
								 this.encargado_array =  JSON.parse(info);  
								 
								 console.log(this.encargado_array[0].id);
								 this.buscar_novedad();
						   }
					})  
	 },
	 
	     
    buscar_novedad(){
			  this.overlay =  true;
       			    var datos = { 
				   'encargado':this.encargado_array[0].id
					};
					this.ajax('../Control/operativo/zdatos.php?Acc=buscar_encargado_mis_novedades',datos,'fullUrl')
					.then(info => {
							   if(!info)
					           {
							   }else{
								  this.overlay =  false;   
							    this.encargado_novedad_array =  JSON.parse(info);  
						   }
					})  
	 },
	 cerrar_novedad(item) {
		 alert('https://app.aoacolombia.com/conVue/#/home&ciudad_siniestro='+item.reportado+'&declarante_ciudad='+item.email_reporte+'&declarante_email='+item.enail+'&declarante_telefono='+item.tele_reporte+'&declarante_nombre='+item.reportado+'&tokenNick='+this.tokenNick+'&siniestro='+item.id_sinistro+'&tokenPeril='+this.tokenPeril+'&tokenUser='+this.tokenUser+'');
		  window.close();
    window.open('https://app.aoacolombia.com/conVue/#/home&ciudad_siniestro='+item.reportado+'&declarante_ciudad='+item.email_reporte+'&declarante_email='+item.enail+'&declarante_telefono='+item.tele_reporte+'&declarante_nombre='+item.reportado+'&tokenNick='+this.tokenNick+'&siniestro='+item.id_sinistro+'&tokenPeril='+this.tokenPeril+'&tokenUser='+this.tokenUser+'');

		 
			
	},
	ingresar_novedad(item) {if(confirm('Desea cerrar una novedad '+item.id_novedad +' del siniestro   '+item.id_sinistro)) 
	 window.open('https://app.aoacolombia.com/conVue/?#/home&id_novedad='+item.id_novedad +'&ciudad_siniestro='+item.ciudad_reporte+'&declarante_ciudad='+item.ciudad_siniestro+'&declarante_email='+item.email_reporte+'&declarante_telefono='+item.tele_reporte+'&declarante_nombre='+item.reportado+'&tokenNick='+this.tokenNick+'&siniestro='+item.id_sinistro+'&tokenPeril='+this.tokenPeril+'&tokenUser='+this.tokenUser+'');

			},

	 
 }
}


















