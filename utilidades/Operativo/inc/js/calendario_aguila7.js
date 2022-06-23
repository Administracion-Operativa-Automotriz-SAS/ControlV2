var Arreglo_calendarios=new Array();

function Calendario_clase(Objeto,Formato_fecha)
{
	this.Forma=Objeto.form.name;
	this.Campo=Objeto.name;
	this.Valor=Objeto.value;
	this.Left=document.getElementById(this.Campo).clientLeft;
	this.Top=document.getElementById(this.Campo).clientTop;
	this.Formato=Formato_fecha;
	this.Span='';				// contenido del objeto span para pintar el calendario
	this.Ano=0;
	this.Mes=0;
	this.Dia=0;
	this.Nombre='';				// nombre del objeto clase
	this.NObjeto='';			// nombre del objeto html
	this.NMes=new Array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	this.TMes=new Array('Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic');
	this.Semana=new Array('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');
	this.Hora=0;
	this.Minuto=0;

	this.iniciar=function()
	{
		this.Nombre=this.Forma+'_'+this.Campo;
		this.NObjeto=Arreglo_calendarios[this.Nombre];
		if(this.verifica_contenido())
		{
			if(this.verifica_span())
			{
				this.pintacalendario();
			}
			else
			{
				this.Span="No hay span definido";
			}
		}
		else
		{
			this.Span="Contenido de fecha invalido";
		}
		document.getElementById('d_'+this.Campo).style.top=mouseY-(this.Forma=='mod'?50:0);
		document.getElementById('d_'+this.Campo).style.left=mouseX-50;
		document.getElementById('d_'+this.Campo).innerHTML=this.Span;
	}
	this.pintacalendario=function()
	{
		var Hoy=new Date();
		var FechaHoy=Hoy.getFullYear()+'-'+(Hoy.getMonth()+1)+'-'+Hoy.getDate();
		if(this.Formato=='t') FechaHoy+=' '+Hoy.getHours()+':'+Hoy.getMinutes()+':'+Hoy.getSeconds();
		this.Span="<table cellspacing=1 bgcolor='ffffff' ondblclick=\"Arreglo_calendarios['"+this.Nombre+"'].cerrar();\">";
		this.Span+="<tr><td bgcolor='eeddcc'><a onclick=\"Arreglo_calendarios['"+this.Nombre+"'].asignarhoy();\" style='cursor:pointer;'>Asignar hoy: "+FechaHoy+"</a></td>";
		this.Span+="<td bgcolor='eeddcc' align='center'><a onclick=\"Arreglo_calendarios['"+this.Nombre+"'].blanquear();\" style='cursor:pointer;'>Blanquear</a></td>";
		if(this.Formato=='t') this.hora();
		this.masyear();
		this.Span+="</tr><tr><td colspan=5>";
		this.year();
		this.Span+="</td></tr><tr><td colspan=5 bgcolor='eeeeff' align='center'><font color='blue'>Calendario Aguila v.7 <i>Arturo Quintero Rodriguez &reg;</i></font></td></tr></table>";
	}
	this.hora=function()
	{
		this.Span+="<td bgcolor='eeddcc' align='center'>Hora: <select style='background-color:eeddcc;color:000000;' onchange=\"Arreglo_calendarios['"+this.Nombre+"'].Hora=this.value;\">";
		for(var i=0;i<=23;i++) this.Span+="<option value='"+(i<10?'0':'')+i+"' '"+(i==this.Hora?'selected':'')+">"+(i<10?'0':'')+i+"</option>";
		this.Span+="</select><select style='background-color:eeddcc;color:000000;'  onchange=\"Arreglo_calendarios['"+this.Nombre+"'].Minuto=this.value;\">";
		for(i=0;i<=59;i++) this.Span+="<option value='"+(i<10?'0':'')+i+"' '"+(i==this.Minuto?'selected':'')+">"+(i<10?'0':'')+i+"</option>";

	}
	this.mostrar=function()
	{
		//this.verifica_contenido();
		document.getElementById('d_'+this.Campo).style.visibility='visible';
	}
	this.masyear=function()
	{
		this.Span+="<td bgcolor='eeddcc' align='center' valign='middle'> Cambio de año: ";
		this.Span+="<a onclick=\"Arreglo_calendarios['"+this.Nombre+"'].cambia_ano("+(Number(this.Ano)-1)+");\" style='cursor:pointer;'>";
		this.Span+="<img src='gifs/standar/calendario_anterior.png' border='0' ></a>&nbsp;";
		this.Span+="<select style='background-color:eeddcc;color:000000;' onchange=\"Arreglo_calendarios['"+this.Nombre+"'].cambia_ano(this.value);\">";
		for(var i=2200;i>=1900;i--)
		this.Span+="<option value='"+i+"' "+(i==this.Ano?" selected ":"")+">"+i+"</option>";
		this.Span+="</select>";
		this.Span+="&nbsp;<a onclick=\"Arreglo_calendarios['"+this.Nombre+"'].cambia_ano("+(Number(this.Ano)+1)+");\" style='cursor:pointer;'>";
		this.Span+="<img src='gifs/standar/calendario_siguiente.png' border='0' ></a> ";
		this.Span+="</td>";
		this.Span+="<td bgcolor='eeddcc' align='center' valign='middle'>";
		this.Span+="<a onclick=\"Arreglo_calendarios['"+this.Nombre+"'].cerrar();\" style='cursor:pointer'>";
		this.Span+="<img src='gifs/standar/Cancel.png' border='0'></a></td>";
	}
	this.cambia_ano=function(Nuevoano)
	{
		this.Ano=Nuevoano;
		this.pintacalendario();
		document.getElementById('d_'+this.Campo).innerHTML=this.Span;
	}
	this.verifica_contenido=function()
	{
		// Extrae el contenido para determinar el año, mes y dia
		var Hoy=new Date();
		var FechaHoy=Hoy.getFullYear()+'-'+(Hoy.getMonth()+1)+'-'+Hoy.getDate();
		this.Ano=this.Valor.substr(0,4);
		if(this.Valor.indexOf('-'))
		{
			this.Mes=this.Valor.substr(5,2);
			this.Dia=this.Valor.substr(8,2);
			if(this.Formato=='t')
			{
				this.Hora=this.Valor.substr(11,2);
				this.Minuto=this.Valor.substr(14,2);
			}
		}
		else
		{
			this.Mes=this.Valor.substr(4,2);
			this.Dia=this.Valor.substr(6,2);
			if(this.Formato=='t')
			{
				this.Hora=this.Valor.substr(9,2);
				this.Minuto=this.Valor.substr(12,2);
			}
		}

		if(Number(this.Ano)==0) this.Ano=Number(Hoy.getFullYear());
		if(Number(this.Mes)==0) this.Mes=Number(Hoy.getMonth())+1;
		if(Number(this.Dia)==0) this.Dia=Number(Hoy.getDate());
		var Fecha=new Date(this.Ano,this.Mes-1,this.Dia);
		if(Fecha) return true; else return false;
	}

	this.verifica_span=function()
	{
		// Verifica la existencia del span correspondiente a la fecha
		var VSpan=document.getElementById('d_'+this.Campo);
		if(VSpan)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	this.year=function()
	{
		this.Span+="<table bgcolor='dfdfdd' cellspacing='0' cellpadding='1'><tr>";
		for(var i=0;i<=11;i++)
		{
			this.Span+="<td valign='top'>";
			this.mes(i);
			this.Span+'</td>';
			if(i==5) this.Span+='<tr><tr>';
		}
		this.Span+='</tr></table>';
		return true;
	}

	this.mes=function(m)
	{
		this.Span+="<table bgcolor='eeeeee' cellspacing='1' cellpadding='0' style='empty-cells:show;'>";
		this.Span+="<tr><td colspan=7 align='center'";
		if((m+1)==this.Mes) this.Span+=" bgcolor='ddffdd' ";
		this.Span+=">"+this.TMes[m]+"</td></tr>";
		this.Span+="<tr><td algin='center'>L</td><td align='center'>M</td><td align='center'>M</td><td align='center'>J</td><td align='center'>V</td><td align='center'>S</td><td align='center'>D</td></tr>";
		for(var d=1;d<=31;d++)
		{
			var f=new Date(this.Ano,m,d)
			var p=f.getDay();if(p==0) p=7;
			var mm=f.getMonth();
			if(mm!=m) break;
			if(p==1) this.Span+='<tr>';
			if(d==1) this.Span+=this.repetir('<td></td>',p-1);
			this.Span+="<td class='diacalendario' onclick=\"Arreglo_calendarios['"+this.Nombre+"'].asigna("+m+","+d+");\" ";
			this.Span+=(((m+1)==this.Mes && d==this.Dia)?"bgcolor='ddffdd'":(p==7?"bgcolor='ffeeee'":"bgcolor='ffffff'"));
			this.Span+="><a class='info' style='cursor:pointer;'>"+d+"<span style='width:200px'>";
			this.Span+=this.Semana[p-1]+' '+d+' de '+this.NMes[m]+' de '+this.Ano;
			this.Span+="</span></a></td>";
			if(p==7) this.Span+='</tr>';
		}
		this.Span+="</table>";
		return true;
	}

	this.cerrar=function()
	{
		document.getElementById('d_'+this.Campo).style.visibility='hidden';
	}

	this.repetir=function(Cadena,Veces)
	{
		var Resultado='';
		for(var i=0;i<Veces;i++)
		Resultado+=Cadena;
		return Resultado;
	}
	this.nmes=function(m)
	{
		switch(m) {	case 1:return 'Ene';case 2:return 'Feb';case 3:return 'Mar';case 4:return 'Abr';case 5:return 'May';case 6:return 'Jun';
		case 7:return 'Jul';case 8:return 'Ago';case 9:return 'Sep';case 10:return 'Oct';case 11:return 'Nov';case 12:return 'Dic';}
	}
	this.asigna=function(m,d)
	{
		if((m+1)<10) var M='0'+(m+1); else M=(m+1);
		if(d<10) var D='0'+d; else D=d;
		eval('document.'+this.Forma+'.'+this.Campo+'.value="'+this.Ano+'-'+M+'-'+D+(this.Formato=='t'?' '+this.Hora+':'+this.Minuto+':00':'')+'";');
		this.cerrar();
		return true;
	}
	this.asignarhoy=function()
	{
		var Hoy=new Date();
		var FechaHoy=Hoy.getFullYear()+'-'+(Hoy.getMonth()+1)+'-'+Hoy.getDate();
		if(this.Formato=='t') FechaHoy+=' '+Hoy.getHours()+':'+Hoy.getMinutes()+':'+Hoy.getSeconds();
		eval('document.'+this.Forma+'.'+this.Campo+'.value="'+FechaHoy+'";');
		this.cerrar();
		return true;
	}
	this.blanquear=function()
	{
		if(confirm('Desea inicializar esta fecha?'))
		{
			eval('document.'+this.Forma+'.'+this.Campo+'.value="0000-00-00'+(this.Formato=='t'?'00:00:00':'')+'";');
			this.cerrar();
		}
		return true;
	}
}

function Calendario_(Objeto,Formato_fecha)
{
	var Forma=Objeto.form.name; var Campo=Objeto.name; var Nombre=Forma+'_'+Campo;
	if(!Arreglo_calendarios[Nombre])
	{
		Arreglo_calendarios[Nombre]= new Calendario_clase(Objeto,Formato_fecha);
		Arreglo_calendarios[Nombre].iniciar();
	}
	Arreglo_calendarios[Nombre].mostrar();
}
