function valida_campos(forma,campos)
{
	if(campos.length>0)
	{
		var fallos=false;
		var fallon=false;
		Arreglo_campos = campos.split(",");
		for(i=0;i<Arreglo_campos.length;i++)
		{
			Arreglo_campo= Arreglo_campos[i].split(':');
			switch(Arreglo_campo[1])
			{
				case 'n':
					var campo= "if(document."+forma+"."+Arreglo_campo[0]+") {if(isNaN(document."+forma+"."+Arreglo_campo[0]+".value) || document."+forma+"."+Arreglo_campo[0]+".value=='') {document."+forma+"."+Arreglo_campo[0]+".style.backgroundColor='#bbffbb';fallon=true;}}";
					eval(campo);
					break;
				case 'f':
					eval("var Campo=document."+forma+"."+Arreglo_campo[0]+";");
					if(Campo)
					{
						if(Campo.value==''  || Campo.value=='0000-00-00')
						{
							eval("var BCampo=document.getElementById('eventdate_span"+Arreglo_campo[0]+"');");
							if(BCampo)
							{
								BCampo.style.backgroundColor='#ffbbbb';
							}
							fallos=true;
						}
					}
					break;
				default:
					var campo= "if(document."+forma+"."+Arreglo_campo[0]+") {if(document."+forma+"."+Arreglo_campo[0]+".value=='') {document."+forma+"."+Arreglo_campo[0]+".style.backgroundColor='#ffbbbb';fallos=true;}}";
					eval(campo);
					var campo= "if(document."+forma+"._"+Arreglo_campo[0]+") {document."+forma+"._"+Arreglo_campo[0]+".style.backgroundColor='#ffbbbb';}";
					eval(campo);
					break;
			}
		}
		if(fallos) {alert('Debe diligenciar la información correspondiente a los campos rosados.');  }
		if(fallon) {alert('Debe diligenciar la información NUMERICA (sin comas) correspondiente a los campos verdes.');  }
		if(!fallon && !fallos) eval("document."+forma+".submit();");
	}
	else
	eval("document."+forma+".submit();");
}
