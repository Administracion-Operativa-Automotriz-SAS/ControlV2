const checkedElement = document.getElementById('replacement')
	
	checkedElement.addEventListener('change',(event) => {
	
	const fetchData = (url_api,idSiniestro) =>{
	return new Promise((resolve,reject)=>{
     const xhttp = new XMLHttpRequest()
        xhttp.open('POST',url_api,true)
		xhttp.onreadystatechange = (()=>{
             if(xhttp.readyState === 4){
                //if() ternario      
                (xhttp.status === 200)
                 ? resolve(JSON.parse(xhttp.responseText))
                 : reject(new Error('Error',url_api))  

                 
             }
        })
        xhttp.send(JSON.stringify({
				acc:"crearRemplazoSiniestro",
				idSi:idSiniestro
				
			}))
    })
   
}
const API = '/Control/operativo/controllers/WebServices.php'

		const anotherFuntion = async (url_api) => {
							try{
							 let idSiniestro = document.getElementById('idSiniestro').value
							 let checked = document.getElementById('replacement')
							 
							 
							 
							 const data = await /* El  await funciona para esperar una promesa solo
							 
							 funciona dentro de una funciona Asincrona definida con async*/ 
							 
							 fetchData(url_api,idSiniestro) /*La funcion  fetchData() es una promesa es la que se cominica con el API*/
							 const character = await fetchData(`${API}`,idSiniestro)
							 
							 let html = `<h4>Siniestro creado con exito</h4>
							            Este es el siniestro de remplazo que creamos para usted: ${character.siniestro}`;
							 
							
							 
							 Swal.fire({
							  title: '<strong>Super Bien!</strong>',
							  type: 'info',
							  customClass: 'sweetalert-lg',
							  html:html
							});
							
							checked.disabled = true
							
							 // nombreDeLaFoto trae el nombre de la imagen que le dio PHP
							
							
							}catch(error){
							 console.log(error)
							}
						}
						
					//anotherFuntion(API)
					
					
					Swal.fire({
					  title: 'Siniestro de remplazo',
					  text: 'Iniciaremos a crear el siniestro de remplazo',
					  type: 'warning',
					  showCancelButton: true,
					  confirmButtonText: 'Continuar',
					  cancelButtonText: 'No, gracias'
					}).then((result) => {
					  if (result.value) {
						anotherFuntion(API)
					  } else if (result.dismiss === Swal.DismissReason.cancel) {
						  document.getElementById('replacement').checked = false
						Swal.fire(
						  'Cancelado',
						  'Vale lo comprendemos, queda cancelado.',
						  'info'
						)
					  }
})



})
