const Ajax = {
    data() {
      return {
        resultAjax:[]
      }
    },
    methods: {
        ajax(url, data, mode)
        {
           return new Promise((resolve, reject) => {
            
              var urlFinal = '';
              Vue.http.options.emulateJSON = true;
            
              if(mode=='fullUrl'){
                urlFinal = url;
              }
   
              this.$http.post(urlFinal,data).then(
                response=>{
                  retorno = response.body;
                  resolve(retorno);
                },
                response=>{
                    //retorno = response;
                }
              );
          });
        },
    		ajaxFile(url,fileInput,data,callback){
    			let formData = new FormData();
    			formData.append('fileSend', fileInput);

    			Object.entries(data).forEach(entry => {
    				let key = entry[0];
    				let value = entry[1];
    				formData.append(key,value);
    			});

    			axios.post(url,
    			formData,
    			{
    				headers: {
    					'Content-Type': 'multipart/form-data'
    				}
    			  }
    			).then(function(r){
    			  callback(r.data);
    			})
    			.catch(function(){
    			  callback(r.data);
    			});
    		}
    }
}
