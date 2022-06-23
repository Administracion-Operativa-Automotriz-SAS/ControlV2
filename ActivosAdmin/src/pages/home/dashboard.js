module.exports = {
    mixins:[Ajax],
    vuetify: new Vuetify(),
	
	components: {
    'vue-recaptcha': VueRecaptcha
  },

    mounted: function () {
        
	
				
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
    
     
          }
      },
      
 
   
    methods: {
	home () {
	  this.$router.push('home');
      },
    
    
 }
}


















