<template>
<v-container fluid>
 <vue-recaptcha sitekey="Your key here"></vue-recaptcha>
  </v-container>
</template>
<script >
module.exports = {
      mixins:[Ajax],
	    name: 'DynamicComponent',
  mounted: function () {
           
          
     
  },
  created() {
  },    
  data: function() {
      return {
     
      }
    },
    methods: {
            
            
      },
    
      validate () {
      
      },
      }
    }


</script>
<style>

</style>