   const fetchData = (url_api) =>{
	return new Promise((resolve,reject)=>{
     const xhttp = new XMLHttpRequest()
        xhttp.open('POST',url_api,true)
		xhttp.setRequestHeader('Access-Control-Allow-Origin', '*')
		xhttp.onreadystatechange = (()=>{
             if(xhttp.readyState === 4){
                //if() ternario      
                (xhttp.status === 200)
                 ? resolve(JSON.parse(xhttp.responseText))
                 : reject(new Error('Error',url_api))  

                 
             }
        })
        xhttp.send(JSON.stringify({
				cod_usr: "AOA_COL",
				password: "H2FEp.1078718871",
				tip_docum: "NT",
				cod_docum: "900174552",
				email: "mailto:sergiocastillo@aoacolombia.com",
				mobile: "573043637333"
				
			}))
    })
   
}
const API = 'https://cotiza.mapfre.com.co/ofvservice/temp_token.jsp'

		const anotherFuntion = async (url_api) => {
							try{
							 
							 
							 
							 
							 
							 const dataTrae = await fetchData(`${API}`)
							 
							 console.log('test')
							 console.log(dataTrae)
							 
							}catch(error){
							 console.log(error)
							}
						}
						
					anotherFuntion(API)
					
					
					




