module.exports = {
    mixins:[Ajax],
    vuetify: new Vuetify(),
	
	components: {
    'vue-recaptcha': VueRecaptcha
  },

    mounted: function () {
		mostrarDatos(tokenItm,tokenItm1,tokenItm2); 

				
    },
    created() {
		
    },   

    data: function() {
        return {
        dialog: false,
      
			cats:TOKEN[0],
			 tokenNick:'',
				   tokenPeril:'',
				    tokenUser:'',
       tokenCiudad:'',
      newCat:null,
		perfil:[],
		perfilList:'',
		mensaje:'',
        show2: true,
        word: false,
	   sele: false,
	   validacion:'',
	   alerta:false,
       valid: true,
      usuario: '',
	    tabla_usuario: '',
      nameRules: [
        v => !!v || 'El usuario es requerido ',
      ],
      clave: '',
      emailRules: [
        v => !!v || 'La contraseña es requrida ',
      ],
	  
    
     
          }
      },
      
 
   
    methods: {
		
		 addCat() {
      // ensure they actually typed something
      if(!this.newCat) return;
      this.cats.push(this.newCat);
      this.newCat = '';
      this.saveCats();
	  alert(cats);
    },
    removeCat(x) {
      this.cats.splice(x,1);
      this.saveCats();
    },
    saveCats() {
      let parsed = JSON.stringify(this.cats);
      localStorage.setItem('cats', parsed);
    },
		
	    
		validate () {
        this.$refs.form.validate()
			 var datos = { 
					"iDU":this.usuario,
					"cLU":this.clave,
					};
					this.ajax('https://app.aoacolombia.com/Control/operativo/login_novedad.php',datos,'fullUrl')
					.then(info => {
						    if(!info.isArray)
					           {
								   this.perfil = info;
								   console.log(this.perfil);
									 this.sele = true;
									this.mensaje = "Debe selecionar un perfil!!";	
								  
							   }else{
									 this.word = true;
									this.mensaje = info;		  
						   }
						   
					})  
      },
      reset () {
        this.$refs.form.reset()
      },
      resetValidation () {
        this.$refs.form.resetValidation()
      },
	     printValue() {
      if( this.perfilList.Nombre_Perfil == 'CALL CENTER' )
	  {
		   this.tabla_usuario = 'usuario_callcenter';
		  sessionStorage.setItem(tokenItm3,this.tabla_usuario); 
		  this.tabla_usuario =sessionStorage.getItem(tokenItm3);  
		 this.word = true;
		this.mensaje = "Bienvenido al perfil de call center  ";		
     	  TOKEN[0] = this.perfilList;
		  tokenPeril =TOKEN[0]['Nombre_Perfil'];
					tokenUser = TOKEN[0]['Nombre'];
					tokenNick = TOKEN[0]['Nick'];
					addProducto(tokenUser,tokenPeril,tokenNick);
		  this.$router.push('dashboard');
	  } 
	 else if( this.perfilList == 'ADMINISTRADOR DEL SISTEMA' )
	  {
			  this.tabla_usuario = 'usuario_callcenter';
		  sessionStorage.setItem(tokenItm3,this.tabla_usuario); 
		  this.tabla_usuario =sessionStorage.getItem(tokenItm3);  
		 this.word = true;
		this.mensaje = "Bienvenido al perfil de call center  ";		
     	  TOKEN[0] = this.perfilList;
		  tokenPeril =TOKEN[0]['Nombre_Perfil'];
					tokenUser = TOKEN[0]['Nombre'];
					tokenNick = TOKEN[0]['Nick'];
					addProducto(tokenUser,tokenPeril,tokenNick);
		  this.$router.push('dashboard');
	  }
	   else if( this.perfilList == 'CONTROL OPERATIVO' )
	  {
		   this.tabla_usuario = 'usuario_callcenter';
		  sessionStorage.setItem(tokenItm3,this.tabla_usuario); 
		  this.tabla_usuario =sessionStorage.getItem(tokenItm3);  
		 this.word = true;
		this.mensaje = "Bienvenido al perfil de call center  ";		
     	  TOKEN[0] = this.perfilList;
		  tokenPeril =TOKEN[0]['Nombre_Perfil'];
					tokenUser = TOKEN[0]['Nombre'];
					tokenNick = TOKEN[0]['Nick'];
					addProducto(tokenUser,tokenPeril,tokenNick);
		  this.$router.push('dashboard');

	  }
	     else if( this.perfilList == 'APP MOVIL' )
	  {
		  this.tabla_usuario = 'usuario_callcenter';
		  sessionStorage.setItem(tokenItm3,this.tabla_usuario); 
		  this.tabla_usuario =sessionStorage.getItem(tokenItm3);  
		 this.word = true;
		this.mensaje = "Bienvenido al perfil de call center  ";		
     	  TOKEN[0] = this.perfilList;
		  tokenPeril =TOKEN[0]['Nombre_Perfil'];
					tokenUser = TOKEN[0]['Nombre'];
					tokenNick = TOKEN[0]['Nick'];
					addProducto(tokenUser,tokenPeril,tokenNick);
		  this.$router.push('dashboard');

	  }
	  
	  
	     else ( this.perfilList == 'OPERARIO FLOTAS' )
	  {
			 this.tabla_usuario = 'operario';
		  sessionStorage.setItem(tokenItm3,this.tabla_usuario); 
		  this.tabla_usuario =sessionStorage.getItem(tokenItm3);  
		 this.word = true;
		this.mensaje = "Bienvenido al perfil de call center  ";		
     	  TOKEN[0] = this.perfilList;
		  
		  tokenPeril =TOKEN[0]['Nombre_Perfil'];
					tokenUser = TOKEN[0]['Nombre'];
					tokenNick = TOKEN[0]['Nick'];
					addProducto(tokenUser,tokenPeril,tokenNick);
		  this.$router.push('mis_novedades');

	  }
	  
      },
	  
	  
	  onCaptchaVerified: function (recaptchaToken) {
      const self = this;
      self.status = "submitting";
      self.$refs.recaptcha.reset();
      axios.post("https://vue-recaptcha-demo.herokuapp.com/signup", {
        email: self.email,
        password: self.password,
        recaptchaToken: recaptchaToken
      }).then((response) => {
        self.sucessfulServerResponse = response.data.message;
      }).catch((err) => {
        self.serverError = getErrorMessage(err);


        //helper to get a displayable message to the user
        function getErrorMessage(err) {
          let responseBody;
          responseBody = err.response;
          if (!responseBody) {
            responseBody = err;
          }
          else {
            responseBody = err.response.data || responseBody;
          }
          return responseBody.message || JSON.stringify(responseBody);
        }

      }).then(() => {
        self.status = "";
      });


    },
    onCaptchaExpired: function () {
      this.$refs.recaptcha.reset();
    }
    
 }
}


















