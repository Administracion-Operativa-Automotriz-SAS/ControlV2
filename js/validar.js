function ValidarString(strNombre,txt){
 var strExpReg=/[^a-zA-ZñÑ ]/;
 if(strNombre==""){
  alert("Ingreso "+txt+" obligatorio.");
  return false;
 }
 else{
  if(strExpReg.test(strNombre)){
   alert(txt+" solo debe contener letras entre (A-Z).");
   return false;
  }
  else return true;
 }
}

function ValidarInt(strInt,txt){
 var strExpReg=/[^0-9]/;
 if (strInt==""){
  alert("Ingreso "+txt+" obligatorio.");
  return false;
 }
 else{
  if(strExpReg.test(strInt)){
   alert(txt+" solo puede contener digitos entre (0-9).");
   return false;
  }
  else return true;
 }
}

function ValidarAlfaNum(strAN,txt){
 var strExpReg=/[^a-zA-Z0-9ñÑ ]/;
 if (strAN==""){
  alert("Ingreso "+txt+" obligatorio.");
  return false;
 }
 else{
  if(strExpReg.test(strAN)){
   alert(txt+" solo puede contener digitos (0-9) o letras entre la a y la z.");
   return false;
  }
  else return true;
 }
}

function Validardocumento(strInt,txt){
  var strExpReg=/[^0-9]/;
 if (strInt.length<8 || strInt==""){
  alert("Ingreso "+txt+" valido es obligatorio.");
  return false;
 }
 else{
  if(strExpReg.test(strInt)){
   alert(txt+" solo puede contener digitos entre (0-9).");
   return false;
  }
  else return true;
 }
 }

function Validartelefono(strInt,txt){
  var strExpReg=/[^0-9]/;
 if (strInt.length<7 || strInt==""){
  alert("Ingreso "+txt+" obligatorio.");
  return false;
 }
 else{
  if(strExpReg.test(strInt)){
   alert(txt+" solo puede contener digitos entre (0-9).");
   return false;
  }
  else return true;
 }
 }

function ValidarCorreo(strCorreo){
 //Verificamos si se ingreso un correo ya que no es un campo obligatorio
 if(strCorreo=="") return true;
 else{
  //Este es un método un poco largo pero la lógica es muy simple de entender
  var strExpRegular1=/[^a-zA-Z0-9ñÑ_.]/
  var strExpRegular2=/[^a-zA-Z0-9]/
  //Primero dividimos la cadena a partir del @
  strCadena=strCorreo.split("@");
  if(strCadena.length!=2){
   alert("Debe ingresar un correo valido (@)");
   return false;
  }
  else{
   if(strCadena[0].length<2){
    alert("Debe ingresar un correo valido (Nombre de usuario muy corto)");
	return false;
   }
   else{
    if(strExpRegular1.test(strCadena[0])){
	 alert("Debe ingresar un correo valido (Caracteres no validos en Nombre de usuario)");
	 return false;
	}
	else{
	 strCadena2=strCadena[1].split(".");
	 if(strCadena2.length<2){
	  alert("Debe ingresar un correo valido (.)");
	  return false;
	 }
	 else{
	  if(strCadena2[0].length<2 || strExpRegular2.test(strCadena2[0])){
	   alert("Debe ingresar un correo valido (Servidor no valido)");
	   return false;
	  }
	  else{
	   if(strCadena2[1].length<2 || strExpRegular2.test(strCadena2[1])){
	    alert("Debe ingresar un correo valido (Dominio no valido)");
		return false;
	   }
	   else return true;	   
      }
	 }
    }
   }
  }
 }
}

function ValidarPlacaV(strPlaca){
 //Este es un método con lógica muy simple de entender
 var strExpReg=/[^a-zA-Z]/;
 var intExpReg=/[^0-9]/;
 if(strPlaca.length<6 || strPlaca.length>6){
  alert("Una placa valida es de seis caracteres.");
  return false;
 }
 else{
  strplaca=strPlaca.slice(0,3)
  intplaca=strPlaca.slice(3)
  if (strExpReg.test(strplaca)){
   alert("los tres primeros caracteres solo debe contener letras entre (A-Z).");
   return false;
  }
  else{
   if (intExpReg.test(intplaca)){
    alert("los tres ultimos caracteres solo puede contener digitos entre (0-9).");
	return false;
   }
   else return true;	
  }
 }
}

function ValidarPlacaM(strPlaca){
 //Este es un método con lógica muy simple de entender
 var strExpReg=/[^a-zA-Z]/;
 var intExpReg=/[^0-9]/;
 if(strPlaca.length<5 || strPlaca.length>5){
  alert("Una placa valida es de cinco caracteres.");
  return false;
 }
 else{
  strplaca=strPlaca.slice(0,3)
  intplaca=strPlaca.slice(3)
  if (strExpReg.test(strplaca)){
   alert("los tres primeros caracteres solo debe contener letras entre (A-Z).");
   return false;
  }
  else{
   if (intExpReg.test(intplaca)){
    alert("los dos ultimos caracteres solo puede contener digitos entre (0-9).");
	return false;
   }
   else return true;	
  }
 }
}