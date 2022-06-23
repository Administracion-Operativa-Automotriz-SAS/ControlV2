var mouseX=0, mouseY=0;
var Browser='IE';
if (document.all) Browser='IE';
else if (document.layers) Browser='NS';
else if (navigator.userAgent.toLowerCase().indexOf('chrome/') > -1) Browser='Chrome';
else if (navigator.userAgent.toLowerCase().indexOf('safari/') > -1) Browser='Safari';
else Browser='7';


function getMousePos(e)
{if(!e) e=window.event||window.Event;if('undefined'!=typeof e.pageX){mouseX=e.pageX;mouseY=e.pageY+10;}else {mouseX=e.clientX+document.body.scrollLeft;mouseY=e.clientY+document.body.scrollTop+10;}}

if(Browser=='IE')
{  document.onmousemove=Mouse_IE; } else { document.onmousemove = getMousePos; }

function Mouse_IE(evnt)
{ mouseX = event.x+20; mouseY = event.y+10; }

function muestra(campo)
{
	var tipob1;
	tipob1=document.all;
	if (tipob1)	this.Browser='IE';
	else { tipob1=document.layers; if (tipob1) this.Browser='NS'; else this.Browser='7';}
	switch(this.Browser)
	{case 'IE':	eval(campo+'.style.visibility = "visible"');break;
	case 'NS':	eval('document.'+campo+'.visibility = "visible"');break;
	case '7' || 'Chrome' : if(document.getElementById(campo)) document.getElementById(campo).style.visibility = 'visible';break;}
}

function oculta(campo)
{
	var tipob1; tipob1=document.all;
	if (tipob1) this.Browser='IE';
	else { tipob1=document.layers;
	if(tipob1) this.Browser='NS'; else this.Browser='7'; }
	switch(this.Browser)
	{ case 'IE': eval(campo+'.style.visibility = "hidden"'); break;
	case 'NS': eval('document.'+campo+'.visibility = "hide"'); break;
	case '7' || 'Chrome' : document.getElementById(campo).style.visibility = 'hidden'; break;}
}


function validaemail(dato)
{
	var enc1=0;
	var enc2=0;

	for(i=0;i<dato.value.length;i++)
	{
		if( dato.value[i]=='@') enc1=1;
	}
	if (dato.value.indexOf('.')!=-1)
	{
		enc2=1;
	}
	if (enc1==0)
	{
		alert('Debe escribir una dirección de correo electrónico válida, hace falta @');
		dato.focus();
	}
	if (enc2==0)
	{
		alert('Debe escribir una dirección de correo electrónico válida, hace falta .');
		dato.focus();
	}
}

function adicionames(fecha,cantidad)
{
	nfecha=fecha.replace(/-/g,'');
	ano=nfecha.substr(0,4)*1;
	mes=nfecha.substr(4,2)*1;
	dia=nfecha.substr(6,2)*1;
	mes+=cantidad;
	while(mes>12) { mes-=12; ano++; }
	if(mes==2 && dia>28) dia=28;
	if( ((mes==4) || (mes==6) || (mes==9) || (mes==11)) & (dia>30)) dia=30;
	resultado=ano+'-'+mes+'-'+dia;
	return resultado;
}

function adicionadia(fecha,cantidad)
{
	nfecha=fecha.replace(/-/g,'');
	ano=nfecha.substr(0,4)*1;
	mes=nfecha.substr(4,2)*1;
	dia=nfecha.substr(6,2)*1;
	dia+=cantidad;
	while(dia>31) { dia-=31; mes++;}
	while(mes>12) {mes-=12; ano++; }
	if(mes==2 && dia >28) {dia-=28; mes++}
	if(((mes==4) || (mes==6) || (mes==9) || (mes==11)) && (dia>30)) {dia=30; mes++; }
	resultado=ano+'-'+mes+'-'+dia;
	return resultado;
}

function aumentadia(fecha,cantidad)
{
	if(fecha.indexOf('-')==-1) fecha=fecha.substr(0,4)+'-'+fecha.substr(4,2)+'-'+fecha.substr(6,2);
	var Arreglo=fecha.split('-');
	var Ano=parseInt(Arreglo[0]*1); var Mes=parseInt(Arreglo[1]*1)-1; var Dia=parseInt(Arreglo[2]*1);
	var Fecha=new Date(Ano,Mes,Dia);
	var Nuevodia=Dia+cantidad;
	Fecha.setDate(Nuevodia);
	return dmy(Fecha);
}


function Afecha(fecha)
{
	if(fecha.indexOf('-')==-1) fecha=fecha.substr(0,4)+'-'+fecha.substr(4,2)+'-'+fecha.substr(6,2);
	var Arreglo=fecha.split('-');
	var Ano=parseInt(Arreglo[0]*1); var Mes=parseInt(Arreglo[1]*1)-1; var Dia=parseInt(Arreglo[2]*1);
	var Fecha=new Date(Ano,Mes,Dia);
	return Fecha;
}

function dmy(Fecha)
{
	var Ano=Fecha.getFullYear();
	var Mes=parseInt(Fecha.getMonth())+1;
	var Dia=Fecha.getDate();
	if(Mes<10) Mes='0'+Mes;
	if(Dia<10) Dia='0'+Dia;
	return Ano+'-'+Mes+'-'+Dia;
}

function esfecha(Dato)
{
	var tmpfecha=Afecha(Dato);
	var tmpfecha1=dmy(tmpfecha);
	if(Dato==tmpfecha1) return true; else return false;
}

function rtrim( str )
{
	var resultStr = "";
	var i = 0;
	if (str+"" == "undefined" || str == null)	return null;
	str += "";
	if (str.length == 0) resultStr = "";
	else
	{
  		i = str.length - 1;
  		while ((i >= 0) && (str.charAt(i) == " ")) i--;
  		resultStr = str.substring(0, i + 1);
  	}
  	return resultStr;
}

function ltrim( str )
{
	var resultStr = "";
	var i = len = 0;
	if (str+"" == "undefined" || str == null)	return null;
	str += "";
	if (str.length == 0) resultStr = "";
	else
	{
		len = str.length;
  		while ((i <= len) && (str.charAt(i) == " "))	i++;
  		resultStr = str.substring(i, len);
  	}
  	return resultStr;
}

function alltrim( str )
{
	var resultStr = "";
	resultStr = ltrim(str);
	resultStr = rtrim(resultStr);
	return resultStr;
}

/**
 *
 * @access public
 * @return void
 **/

function modal(url,top,left,alto,ancho,destino)
{
	if(!top) top=0;if(!left) left=0;if(!alto) alto=50;if(!ancho) ancho=100;if(!destino) destino='_blank';
	var caracteristicas = "height="+alto+", width="+ancho+", channelmode=0, dependent=1, chrome=yes, location=0, toolbar=0, directories=0,status=0, statusbar=0, linemenubar=0, menubar=0, modal=1, left="+left+", top="+top+", resizable=1, scrollbars=1";

	var NuevaVentana = window.open(url,destino,caracteristicas);
	if(NuevaVentana==null) alert('SU NAVEGADOR ESTA BLOQUEANDO VENTANAS EMERGENTES');
	NuevaVentana.focus();
	//NuevaVentana.resizeTo(ancho,alto);
	//if(mouseX+ancho>screen.availWidth) mouseX=screen.availWidth-ancho;
	//if(mouseY+alto>screen.availHeight) mouseY=screen.availHeight-alto-100;
	//NuevaVentana.moveTo(mouseX,mouseY);
}

function modal2(url,top,left,alto,ancho,destino,mouse)
{
	if(!top) top=0;if(!left) left=0;if(!alto) alto=50;if(!ancho) ancho=100;if(!destino) destino='_blank';
	var caracteristicas = "height="+alto+", width="+ancho+", channelmode=0, dependent=0, chrome=yes, location=0, toolbar=0, directories=0,status=0,statusbar=0, linemenubar=0, menubar=1, modal=1, left="+left+", top="+top+", resizable=1, scrollbars=1";
	var NuevaVentana = window.open(url,destino,caracteristicas);
	if(NuevaVentana==null) alert('SU NAVEGADOR ESTA BLOQUEANDO VENTANAS EMERGENTES');
	NuevaVentana.focus();
}

function s_alto()
{ return screen.availHeight;}

function s_ancho()
{ return screen.availWidth; }

function valida_entrada(perfil,CP)
{
	if(perfil)
		window.open('valida_operativo.php?PerFil='+perfil+(CP?'&CAMBIA_PERFIL=1':''),'destino000');
	else
		window.open('valida_operativo.php?PerFil='+perfil+(CP?'&CAMBIA_PERFIL=1':''),'_self');
}

function crea_perfil(comando,Ventana)
{
  if(!Ventana) Ventana='destino000';
	modal('operativo.php?Acc=selecciona_perfil&SESION_PUBLICA=1&C='+comando,0,0,screen.availWidth,screen.availHeight,Ventana);
}

function re_crea_perfil(comando)
{
	window.open('operativo.php?Acc=re_selecciona_perfil&SESION_PUBLICA=1&C='+comando,'_self');
}

function mata_perfil()
{
  if(confirm('Realmente desea cerrar sesión ?'))
  window.open('operativo.php?Acc=mata_perfil&SESION_PUBLICA=1','_top');
}

function pickcolor(Formulario,Campo,Dato)
{
	left=0;top=0;
	alto=350;
	ancho=400;
	destino='Recogecolor';
	Dato=Dato.replace(/#/,'');
	url='html/colorpicker/index.php?Forma='+Formulario+'&Campo='+Campo+'&Dato='+Dato;

	if(window.showModalDialog)
	{
		var caracteristicas="height="+alto+", width="+ancho+", channelmode=0, location=0, toolbar=0, directories=0,status=0, linemenubar=0, menubar=0, modal=1, innerleft="+left+", innertop="+top+", dialog=1,resizable=1, scrollbars=1";
		var NuevaVentana = window.open(url, destino, caracteristicas);
		if(mouseX+ancho>screen.availWidth) mouseX=screen.availWidth-ancho;
		if(mouseY+alto>screen.availHeight) mouseY=screen.availHeight-alto-100;
		NuevaVentana.moveTo(mouseX,mouseY);
	}
	else
	{
		var caracteristicas = "height="+alto+", width="+ancho+", channelmode=0, location=0, toolbar=0, directories=0,status=0, linemenubar=0, menubar=0, modal=1, innerleft="+left+", innertop="+top+", dialog=1,resizable=1, scrollbars=1";
		var NuevaVentana = window.open(url,destino,caracteristicas);
		if(mouseX+ancho>screen.availWidth) mouxeX=screen.availWidth-ancho;
		if(mouseY+alto>screen.availHeight) mouseY=screen.availHeight-alto-100;
		NuevaVentana.moveTo(mouseX,mouseY);
		NuevaVentana.focus();
	}
}

function cambiacolor(Color,Nombre_tabla,idcampo,ncampo)
{
	var WC=window.open('','CambioColor','width=100,height=100,toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,dependent=1,resizable=0,z-lock=1');
	var doc = WC.document;
	doc.open("text/html", "replace");
	doc.write("<HTML> <TITLE>Cambio de Color</TITLE><head><script src='inc/js/funciones.js'></script></head>");
	doc.write("<BODY onload=\"pickcolor('cambiocolor','Color','"+Color+"');\" onfocus='document.cambiocolor.submit();'>");
	doc.write("<form action='operativo.php' name='cambiocolor'>");
	doc.write("<input type='hidden' name='Acc' value='cambio_color' >");
	doc.write("<input type='hidden' name='Nombre_tabla' value='"+Nombre_tabla+"'>");
	doc.write("<input type='hidden' name='idcampo' value='"+idcampo+"'>");
	doc.write("<input type='hidden' name='ncampo' value='"+ncampo+"'>");
	doc.write("<input type='hidden' name='Color' value='"+Color+"'><input type='submit' value='Cambiar Color' ></form>");
	doc.write("</BODY></HTML>");
	doc.close();
}

function centrar(ancho,alto)
{
	if(!ancho) ancho=s_ancho();
	if(!alto) alto=s_alto();
	window.resizeTo(ancho,alto);
	window.moveTo((screen.availWidth-ancho)/2,(screen.availHeight-alto)/2);
}

function esquina(ancho,alto,esq)
{
	if(!esq) esq='supder';
	if(!ancho) ancho=s_ancho()/2;
	if(!alto) alto=s_alto()/2;
	window.resizeTo(ancho,alto);
	switch(esq)
	{
		case 'supder':	window.moveTo((screen.availWidth-ancho),1);break;
		case 'supizq':	window.moveTo(1,1);break;
		case 'infiz1':	window.moveTo(1,(screen.availHeight-alto));break;
		case 'infder':	window.moveTo((screen.availWidth-ancho),(screen.availHeight-alto));break;
	}
}

function modifica_registro(Num_Tabla,Id,Solover,Cerrar_ventana)
{
	V_ancho=screen.availWidth-60;V_alto=screen.availHeight-80;
	modal('operativo.php?Acc=mod_reg&Num_Tabla='+Num_Tabla+'&id='+Id+'&sV='+Solover+'&CERRAR_VENTANA='+Cerrar_ventana,5,10,V_alto,V_ancho,'Adicionar_modificar');
}


function abrir_tabla(Num_Tabla,destino,parametros)
{
	V_ancho=screen.availWidth-60;V_alto=screen.availHeight-80;
	modal('operativo.php?Acc=abre_tabla&Num_Tabla='+Num_Tabla+'&D_tag='+destino+'&'+parametros,5,10,V_alto,V_ancho,destino);
}

function verificanumero(Evento,Nodo,entero)
{
	var keynum;
	if(window.event) // IE
		keynum = Evento.keyCode;
	else if(Evento.which) // Netscape/Firefox/Opera
		keynum = Evento.which;
	if(keynum==8 || keynum==13  || keynum==37 || keynum==39 || keynum==46 || keynum==9 ||
		keynum==16 || keynum==17 || keynum==18 || keynum==27 || keynum==20 || keynum==144 ||
		keynum==110 || keynum==190 ||
		(keynum>95 && keynum<106) || (keynum>=48 && keynum<=57))
	{
		if(entero)
        {
            if(keynum!=110 || keynum!=190)
            {
                return true;
            }
        }
        else
        {
    		return true;
    	}
	}
	else
	{
		var Texto=document.getElementById(Nodo).value;
		if(keynum==110 || keynum==190) var Caracter='.'; else var Caracter=String.fromCharCode(keynum);
		Texto=Texto.toUpperCase();Texto=Texto.replace(Caracter,'');
		document.getElementById(Nodo).value=Texto;
		document.getElementById(Nodo).style.backgroundColor='#ffddbb';
		alert('Caracer no permitido !! debe escribir solamente números ');
        return false;
	}
	return true;
}

function activa_edicion(NT)
{
	var ventana=document.getElementById('Edicion_'+NT);
	ventana.height=window.innerHeight-50;
	ventana.width=window.innerWidth-60;
	ventana.style.visibility='visible';
	ventana.focus();
}

function oculta_edicion(NT,refrescando)
{

	if(!refrescando) refrescando=true; else refrescando=false;
	if(parent.document.getElementById('Edicion_'+NT))
	{
		var ventana=parent.document.getElementById('Edicion_'+NT);
		var refrescar=parent.document.location;
	}
	else
	{
		if(document.getElementById('Edicion_'+NT))
		{
			var ventana=document.getElementById('Edicion_'+NT);
			var refrescar=document.location;
		}
		else
		{
			window.close();
			void(null);
			opener.location.reload();
		}
	}
	if(ventana)
	{
	  	ventana.src='gifs/standar/loading.gif';
		ventana.style.visibility='hidden';
	}
	if(refrescando) parent.location=refrescar;
}

function termo(Direccion)
{
	var caracteristicas = "height=200, width=400, channelmode=0, location=0, toolbar=0, directories=0,status=0, linemenubar=0, menubar=0, modal=1, left="+((screen.availWidth-400)/2)+", top="+(screen.availHeight-100)/2+", dialog=1, scrollbars=0 ";
	var VVbuscando = window.open(Direccion,'TERMO',caracteristicas);
	VVbuscando.focus();
}

function leerCookie(nombre)
{
	a = document.cookie.substring(document.cookie.indexOf(nombre + '=') + nombre.length + 1,document.cookie.length);
	if(a.indexOf(';') != -1)a = a.substring(0,a.indexOf(';'))
	return a;
}

function setCookie(name, value, expires, path, domain, secure)
{
  document.cookie =
    name+"="+escape(value)+
    (expires ? "; expires="+expires.toGMTString() : "")+
    (path    ? "; path="   +path   : "")+
    (domain  ? "; domain=" +domain : "")+
    (secure  ? "; secure" : "");
}

function setCookieLT(name, value, lifetime, path, domain, secure)
{
  if (lifetime) lifetime = new Date(Date.parse(new Date())+lifetime*1000);
  setCookie(name, value, lifetime, path, domain, secure);
}

function getCookie(name)
{
  var cookie, offset, end;
  cookie  = " "+document.cookie;
  offset  = cookie.indexOf(" "+name+"=");
  if (offset == -1) return '';
  offset += name.length+2;
  end     = cookie.indexOf(";", offset)
  if (end    == -1) end = cookie.length;
  return unescape(cookie.substring(offset, end));
}

function delCookie(name, path, domain)
{
  if (getCookie(name))
    setCookie(name, "", new Date("January 01, 2000 00:00:01"), path, domain);
}

var Topscrolled=0;
var Leftscrolled=0;

function fijascroll()
{
	Topscrolled=document.body.scrollTop;
	Leftscrolled=document.body.scrollLeft;
}

function guardascroll()
{
	document.cookie="SC_TOP = "+Topscrolled+"; ";
	document.cookie="SC_LEFT = "+Leftscrolled+"; ";
}

function videoayuda(dato,ancho,alto)
{
	modal('operativo.php?Acc=videoayuda&Numero='+dato+'&ancho='+ancho+'&alto='+alto,0,0,alto+180,ancho+60,'videoayuda');
}

function videoflv(dato,ancho,alto)
{
	modal('operativo.php?Acc=videoayuda&Numero='+dato+'&ancho='+ancho+'&alto='+alto+'&FLV=1',0,0,alto+180,ancho+60,'videoayuda');
}

function dscroll()
{
	var vHeight = 0;
	if (document.all)
	{
		if (document.documentElement)
		{
			vHeight = document.documentElement.clientHeight;
		}
		else
		{
			vHeight = document.body.clientHeight
		}
	}
	else
	{
		vHeight = window.innerHeight;
	}
	if (document.body.offsetHeight > vHeight)
	{
		//insert code for whatever happens
		//when theres a scrollbar
		return true;
	}
	return false;
}

function posiciona(Objeto)
{
	if (!e) e = window.event||window.Event;
	if('undefined'!=typeof e.pageX)
	{
		mouseX = e.pageX;
		mouseY = e.pageY+10;
	}
	else
	{
		mouseX = e.clientX + document.body.scrollLeft;
		mouseY = e.clientY + document.body.scrollTop+10;
	}
	if(window.document.body.scrollTop > 0)
	{
		mouseY = (window.screen.Height) ? e.clientY + window.document.body.scrollTop -20 : e.clientY -20;
	}
	else if (window.pageYOffset)
	{
		mouseY = (window.pageYOffset > 0) ? e.clientY + window.pageYOffset -20 : e.clientY -20;
	}

	 /* * */
	document.getElementById(Objeto).style.left = mouseX + 'px';
	document.getElementById(Objeto).style.top = mouseY + 'px';
	return false;
}

function Redondeo(Dato,Decimales)
{
	if(Decimales)
	{
		if(Decimales>0)
		{
			Decimales=Math.pow(10,Decimales);
			return Math.round(Dato*Decimales)/Decimales;
		}
		else
		{
			Decimales=-Decimales;
			Decimales=Math.pow(10,Decimales);
			return Math.round(Dato/Decimales)*Decimales;
		}
	}
	else
		return Math.round(Dato);
}

function monetario(Dato,Decimales,Enfoque)
{
	if(!Enfoque) Enfoque=false;
	if(!Decimales) Decimales=2;
	if(!isNaN(Dato)) Dato=Dato.toString();
	if(Enfoque)
	{
		var Busqueda=/,/g;
		Dato=Dato.replace(Busqueda,'');
		return Dato;
	}
	var Longitud=Dato.length;
	var Resultado='';
	var Parte='';
	if(Longitud)
	{
		if(Dato.indexOf('.')==-1) // no tiene decimales
		{
			while(Longitud>3)
			{
				Parte=Dato.substr(Longitud-3,3);
				Resultado=','+Parte+Resultado;
				Dato=Dato.substr(0,Longitud-3);
				Longitud=Dato.length;
			}
			Resultado=Dato+Resultado;
			return Resultado;
		}
		else // con decimales
		{
			Parte=Dato.substr(Dato.indexOf('.'));
			Dato=Dato.substr(0,Dato.indexOf('.'));
			Resultado=Redondeo(Parte,Decimales);
			Resultado=Resultado.toString();
			Resultado=Resultado.substr(1);
			Coma=false;
			while(Longitud>3)
			{
				Parte=Dato.substr(Longitud-3,3);
				if(Coma)
					Resultado=','+Parte+Resultado
				else
					Resultado=Parte+Resultado;
				Dato=Dato.substr(0,Longitud-3);
				Longitud=Dato.length;
				Coma=true;
			}
			Resultado=Dato+Resultado;
			return Resultado;
		}
	}
	else return '';
}

function getSelected(opt)
{
  var selected = new Array();
  var index = 0;
  for (var intLoop=0; intLoop < opt.length; intLoop++)
  {
     if (opt[intLoop].selected)
	 {
        index = selected.length;
        selected[index] = new Object;
        selected[index].value = opt[intLoop].value;
        selected[index].index = intLoop;
     }
  }
  return selected;
}


// *********************************************************************************************************************************














