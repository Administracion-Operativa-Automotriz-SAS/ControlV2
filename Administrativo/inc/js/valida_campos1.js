function valida_campos(forma,campos)
{
	var bcolor_error1='#FFFF00';
	var bcolor_error2='#AEFF00';
	var color_error1='#000000';
	var color_error2='#000000';

	if(campos.length>0)
	{
		var fallos=false;
		var fallon=false;
		Arreglo_campos = campos.split(",");
		for(i=0;i<Arreglo_campos.length;i++)
		{
			Arreglo_campo= Arreglo_campos[i].split(':');
			eval("var obj=document."+forma+"."+Arreglo_campo[0]+";");
			if(obj)
			{
				switch(Arreglo_campo[1])
				{
					case 'n':
					console.log('Hola 1');
					//console.log(obj);
						if(isNaN(obj.value) || obj.value=='')
						{
							obj.style.backgroundColor=bcolor_error2;
							obj.style.color=color_error2;
							fallon=true;
						}
						break;
					case 'f':
					console.log('Hola 2');
					//console.log(obj);
						eval("var obj_ano=document."+forma+"."+Arreglo_campo[0]+"_ano;");
						eval("var obj_mes=document."+forma+"."+Arreglo_campo[0]+"_mes;");
						eval("var obj_dia=document."+forma+"."+Arreglo_campo[0]+"_dia;");
						if(obj_ano && obj_mes && obj_dia)
						{
							
							if(obj_ano.value=='' || obj_mes.value=='' || obj_dia.value=='')
							{
								obj_ano.style.backgroundColor=bcolor_error1;
								obj_mes.style.backgroundColor=bcolor_error1;
								obj_dia.style.backgroundColor=bcolor_error1;
								obj_ano.style.color=color_error1;
								obj_mes.style.color=color_error1;
								obj_dia.style.color=color_error1;
								fallos=true;
							}
						}
						break;
					default:
					console.log('Hola 3');
						console.log(obj);	
							if(obj.value=='')
							{ 
						        console.log(obj);
								obj.style.backgroundColor=bcolor_error1;
								obj.style.color=color_error1;
								fallos=true;
							}
							var campo= "if(document."+forma+"._"+Arreglo_campo[0]+") {document."+forma+"._"+Arreglo_campo[0]+".style.backgroundColor=bcolor_error1;}";
							eval(campo);
							break;
				}
			}
		}
		if(color_error1==color_error2)
		{
			if(fallos || fallon ) {alert('Debe diligenciar la información correspondiente a los campos resaltados.');  }
		}
		else
		{
			if(fallos) {alert('Debe diligenciar la información correspondiente a los campos resaltados.');  }
			if(fallon) {alert('Debe diligenciar la información NUMERICA (sin comas) correspondiente a los campos verdes.');  }
		}
		if(!fallon && !fallos) eval("document."+forma+".submit();");
	}
	else
	eval("document."+forma+".submit();");
}
