/*
    Tomar una fotografía y guardarla en un archivo v3
    @date 2018-10-22
    @author parzibyte
    @web parzibyte.me/blog
*/
function tieneSoporteUserMedia() {
	return !!(navigator.getUserMedia || (navigator.mozGetUserMedia || navigator.mediaDevices.getUserMedia) || navigator.webkitGetUserMedia || navigator.msGetUserMedia)
}
function _getUserMedia() {
	return (navigator.getUserMedia || (navigator.mozGetUserMedia || navigator.mediaDevices.getUserMedia) || navigator.webkitGetUserMedia || navigator.msGetUserMedia).apply(navigator, arguments);
}
 
// Declaramos elementos del DOM
const $video = document.querySelector("#video"),
	$canvas = document.querySelector("#canvas"),
	$boton = document.querySelector("#boton"),
	$estado = document.querySelector("#estado"),
	$listaDeDispositivos = document.querySelector("#listaDeDispositivos");
 
// La función que es llamada después de que ya se dieron los permisos
// Lo que hace es llenar el select con los dispositivos obtenidos
const llenarSelectConDispositivosDisponibles = () => {
 
	navigator
		.mediaDevices
		.enumerateDevices()
		.then(function (dispositivos) {
			const dispositivosDeVideo = [];
			dispositivos.forEach(function (dispositivo) {
				const tipo = dispositivo.kind;
				if (tipo === "videoinput") {
					dispositivosDeVideo.push(dispositivo);
				}
			});
 
			// Vemos si encontramos algún dispositivo, y en caso de que si, entonces llamamos a la función
			if (dispositivosDeVideo.length > 0) {
				// Llenar el select
				dispositivosDeVideo.forEach(dispositivo => {
					const option = document.createElement('option');
					option.value = dispositivo.deviceId;
					option.text = dispositivo.label;
					$listaDeDispositivos.appendChild(option);
					console.log("$listaDeDispositivos => ", $listaDeDispositivos)
				});
			}
		});
}
 
(function () {
	const fetchData = (url_api,IDN,foto) =>{
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
				acc:"asignar_imagen",
				idTablaRecepcion:IDN,
				fotoEnviar:foto
			}))
    })
   
}

	// Comenzamos viendo si tiene soporte, si no, nos detenemos
	if (!tieneSoporteUserMedia()) {
		alert("Lo siento. Tu navegador no soporta esta característica");
		$estado.innerHTML = "Parece que tu navegador no soporta esta característica. Intenta actualizarlo.";
		return;
	}
	//Aquí guardaremos el stream globalmente
	let stream;
 
 
	// Comenzamos pidiendo los dispositivos
	navigator
		.mediaDevices
		.enumerateDevices()
		.then(function (dispositivos) {
			// Vamos a filtrarlos y guardar aquí los de vídeo
			const dispositivosDeVideo = [];
 
			// Recorrer y filtrar
			dispositivos.forEach(function (dispositivo) {
				const tipo = dispositivo.kind;
				if (tipo === "videoinput") {
					dispositivosDeVideo.push(dispositivo);
				}
			});
 
			// Vemos si encontramos algún dispositivo, y en caso de que si, entonces llamamos a la función
			// y le pasamos el id de dispositivo
			if (dispositivosDeVideo.length > 0) {
				// Mostrar stream con el ID del primer dispositivo, luego el usuario puede cambiar
				mostrarStream(dispositivosDeVideo[0].deviceId);
			}
		});
 
 
 
	const mostrarStream = idDeDispositivo => {
		_getUserMedia(
			{
				video: {
					// Justo aquí indicamos cuál dispositivo usar
					deviceId: idDeDispositivo,
				}
			},
			function (streamObtenido) {
				// Aquí ya tenemos permisos, ahora sí llenamos el select,
				// pues si no, no nos daría el nombre de los dispositivos
				llenarSelectConDispositivosDisponibles();
 
				// Escuchar cuando seleccionen otra opción y entonces llamar a esta función
				$listaDeDispositivos.onchange = () => {
					// Detener el stream
					if (stream) {
						stream.getTracks().forEach(function (track) {
							track.stop();
						});
					}
					// Mostrar el nuevo stream con el dispositivo seleccionado
					mostrarStream($listaDeDispositivos.value);
				}
 
				// Simple asignación
				stream = streamObtenido;
 
				// Mandamos el stream de la cámara al elemento de vídeo
				$video.srcObject = stream;
				$video.play();
 
				//Escuchar el click del botón para tomar la foto
				$boton.addEventListener("click", function () {
					
					let IDN = parent.document.getElementById('idTablaRecepcion').value
 
					//Pausar reproducción
					$video.pause();
 
					//Obtener contexto del canvas y dibujar sobre él
					let contexto = $canvas.getContext("2d");
					$canvas.width = $video.videoWidth;
					$canvas.height = $video.videoHeight;
					contexto.drawImage($video, 0, 0, $canvas.width, $canvas.height);
 
					let foto = $canvas.toDataURL(); //Esta es la foto, en base 64
					$estado.innerHTML = "Enviando foto. Por favor, espera...";
					const API = '/Control/operativo/controllers/WebServices.php'
					
					const anotherFuntion = async (url_api) => {
							try{
							 let fotoEnviar = encodeURIComponent(foto)
							 const data = await /* El  await funciona para esperar una promesa solo
							 
							 funciona dentro de una funciona Asincrona definida con async*/ 
							 
							 fetchData(url_api,IDN,fotoEnviar) /*La funcion  fetchData() es una promesa es la que se cominica con el API*/
							 const character = await fetchData(`${API}`,IDN,fotoEnviar)
							 console.log(character.rutaImagenFinal)
							 let html = `<h4>Foto guardada con exito</h4><br>
							            <img src='${character.rutaImagenFinal}' style='width: 85%;height: auto;'>`;
							 Swal.fire({
							  title: '<strong>Exito</strong>',
							  type: 'info',
							  customClass: 'sweetalert-lg',
							  html:html
							});
							 // nombreDeLaFoto trae el nombre de la imagen que le dio PHP
							console.log("La foto fue enviada correctamente");
							$estado.innerHTML = `<h2>Foto guardada con exito, Puedes verla <a target='_blank' href='${character.rutaImagenFinal}'><h1>Aqui</h1></a><h2> `;
							}catch(error){
							 console.log(error)
							 $estado.innerHTML = 'No se puede acceder a la cámara, o no diste permiso.'
							}
						}
						
					anotherFuntion(API)
					
					//Reanudar reproducción
					$video.play();
				});
			}, function (error) {
				console.log("Permiso denegado o error: ", error);
				$estado.innerHTML = "No se puede acceder a la cámara, o no diste permiso.";
			});
	}
})();