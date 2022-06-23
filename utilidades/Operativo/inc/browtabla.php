<?php

class brow_columna
{
	var $campo=''; // nombre fisico del campo
	var $titulo=''; // descripcion del campo
	var $tipo=''; // tipo de campo
	var $relacionado=false; // se supone que el campo no es relacionado, si lo es esta variable se activa
	var $tabla_relacion=''; // si este campo está relacionado con una tabla aqui queda el nombre fisico de la tabla
	var $campo_relacion=''; // si estecampo está relacionado con una tabla aqui queda el campo conel cual está relacionado
	var $contenido_relacion=''; // si el campo está relacionado conuna tabla, aqui queda lo que se desea que aparezca en el brow
	var $menudirecto=false; // Este campo corresponde a un menu directo donde las opciones están separadas por ; y el value y la descripcion por ,
	var $idcampo=0;	// id interno del campo

	function brow_columna($C,$LINK,$Tabla)
	{
		$this->idcampo=$C->id;
		$this->campo=$C->campo;
		$this->titulo=$C->descripcion;
		$this->tipo=$C->tipo;
		$this->ancho=$C->sizecap?$C->sizecap:50;
		if($C->traet && $C->traen && $C->trael)
		{
			$this->tabla_relacion=$C->traet;
			$this->campo_relacion=$C->trael;
			$this->contenido_relacion=$C->traen;
			$this->relacionado=true;
			$Nombre_funcion='T_'.$this->tabla_relacion;

			// --------------    BUSQUEDA DE LA FUNCION T_TABLA Para ser usada directamente en el query ------------------------------
			$Esta=false;
			if($Funcion=mysql_query("show function status like '$Nombre_funcion' ",$LINK))
			{
				while($Funcion_detalle=mysql_fetch_row($Funcion))
				{
					if($Funcion_detalle[0]==MYSQL_D && $Funcion_detalle[1]==$Nombre_funcion)
					{
						$Esta=true; break;
					}
				}
			}
			if(!$Esta)
			{
				$Tipo=qom("show columns from $this->tabla_relacion like '$this->campo_relacion'",$LINK);
				$Comando="create function $Nombre_funcion(Dato_ $Tipo->Type) returns varchar(200) reads sql data
	              begin Declare Resultado_ varchar(200) default '';
                 Select $this->contenido_relacion into Resultado_ from $this->tabla_relacion where $this->campo_relacion = Dato_ limit 1;
		           return Resultado_; end";
				if(!mysql_query($Comando,$LINK)) die($Comando.'<br>'.$Tabla.'<br>'.$this->campo.'<br>'.mysql_error($LINK) );
			}
			// ------------------------------------------------------------------------------------------------------------------------
		}
		if(!$C->traet && $C->traex && strpos($C->traex,';'))
		{
			$Cases=str_replace(';', "' When '",$C->traex);
			$Cases=str_replace(',',"' Then '",$Cases);
			$Nombre_funcion='M_'.$Tabla.'_'.$this->campo;
			$this->menudirecto=true;
			$Esta=false;
			if($Funcion=mysql_query("show function status like '$Nombre_funcion' ",$LINK))
			{
				while($Funcion_detalle=mysql_fetch_row($Funcion))
				{
					if($Funcion_detalle[0]==MYSQL_D && $Funcion_detalle[1]==$Nombre_funcion)
					{
						$Esta=true; break;
					}
				}
			}
			if(!$Esta)
			{
				$Tipo=qom("show columns from $Tabla like '$this->campo' ",$LINK);
				$Comando="create function $Nombre_funcion(Dato $Tipo->Type) returns varchar(200) no sql
					begin Declare Resultado varchar(200) default '';
					select case Dato when '$Cases' end into Resultado; return Resultado; end";
				if(!mysql_query($Comando,$LINK)) die($Comando.'<br>'.$Tabla.'<br>'.$this->campo.'<br>'.mysql_error($LINK) );
			}
		}
	}

	function pinta_titulo()
	{
		echo "<th nowrap='yes' id='th_$this->campo' name='th_$this->campo'  bgcolor='ffffff' oncontextmenu=\"m_c('$this->campo',$this->idcampo);return false;\" >$this->titulo</th>";
	}
}

class brow_tabla
{
	var $id_tabla=0; // identificador de la tabla en usuario_tab
	var $nombre_tabla='';  // Nombre fisico de la tabla
	var $titulo_tabla=''; // Titulo identificador de la tabla
	var $TextoTabla=''; // Texto adicional en la presentación de la tabla.
	var $Condicion_sql=''; // Condición en formato sql para la seleccion de los registros
	var $Ancho_vent=1000; // Ancho de la ventana
	var $Alto_vent=700; // Alto de la ventana
	var $centrar=false; // Se activa si el TAG no es ni destino ni cabeza  ni detalle
	var $Vercampos=''; // campos que se van a ver
	var $Cols=array(); // Columnas que se muestran en el brow
	var $Query=''; // query para la consulta de registros
	var $E_query=''; // instruccion encriptada del query de registros
	var $E_orden_query=''; // instruccion encriptada del orden del query
	var $E_cols=''; // Columnas del brow encriptadas
	var $E_where_query=''; // condiciones del query encriptadas
	var $Pagina=1; // Págin de registros
	var $Cantidad_paginas=1; // cantidad de paginas
	var $Lineas_por_pagina=20; // lineas por página
	var $Vinculo_externo_campo=''; // si la tabla debe tener un filtro automático por un campo externo
	var $Vinculo_externo_contenido=''; // si la tabla debe tener un filtro automático  por un contenido externo
	var $Orden_query='id'; // orden del query
	var $Condiciones_query=''; // condiciones del query
	var $Cantidad_registros=0; // cantidad de registros de la tabla
	var $Java_onload_body='';  // programacion en javascript para el onload del body
	var $Java_columnas='';  // programacion en javascript para crear un arreglo de campos usando la clase (columna)
	var $Icono=''; // imagen de la tabla
	var $Modifica=1; // Modifica el registro para ser usado con el doble click
	var $Adiciona=1;  // Permidso para adicion de registros
	var $Vinculo_Campo=''; // Vinculo externo para filtrar desde una tabla madre
	var $Vinculo_Contenido=''; // Vinculo externo para filtrar desde una tabla madre
	var $Campo_busqueda1=''; // campo de busqueda
	var $Contenido_busqueda1=''; // contenido de busqueda
	var $Campo_busqueda2=''; // campo de busqueda
	var $Contenido_busqueda2=''; // contenido de busqueda
	var $Campo_busqueda3=''; // campo de busqueda
	var $Contenido_busqueda3=''; // contenido de busqueda
	var $Busqueda_exacta=''; // busqueda exacta
	var $Error=''; // mensaje de error en caso de algun inconveniente.
	var $Vinculo_primario=''; // En caso de que la información dependa de un vínculo primario, es un campo donde se incluye la información del Id Alterno del usurario y se utiliza para filtrar las tablas
	var $Info_cabecera=''; // Se usa para la información adicional que se debe mostrar en la cabecera.
	var $Buscador_relacionado='';

	function brow_tabla($Id=0)  // recibe el id del numero de la tabla
	{
		global $VINCULOT /*contenido de vinculo */,
		$VINCULOC /*campo de vinculo */,
		$CaB /*campo de busqueda */,
		$CoB /*contenido de busqueda */,
		$ExB /*busqueda exacta */,
		$TI  /* Trae Información para mostrar en la cabecera */;
		if($TT=qo("select * from usuario_tab where id='$Id'"))
		{
			$this->id_tabla=$Id;
			$this->nombre_tabla=$TT->tabla;
			$this->titulo_tabla=$TT->descripcion;
			$this->centrar=0;//($TT->destino!='destino000' && $TT->destino!='cabeza');
			$this->Ancho_vent=$TT->vancho;
			$this->Alto_vent=$TT->valto;
			$this->Vercampos=$TT->vercampos;
			$this->Vinculo_primario=$TT->vinculoc;
			$this->TextoTabla=$TT->explicacion;
			$this->Lineas_por_pagina=20;
			$this->Orden_query=($TT->ordeninicial?$TT->ordeninicial:'id');
			$this->Java_onload_body=($this->centrar?"centrar($this->Ancho_vent,$this->Alto_vent);":"");
			$this->Java_onload_body.='inicial();repinta_detalle();';
			$this->Icono=$TT->dicono_f;
			$this->Modifica=($TT->modifica && !$TT->condi_modi?1:0);
			$this->Adiciona=$TT->adiciona;
			$this->Condicion_sql=$this->Verifica_variables($TT->condicion);
			if($VINCULOT && $VINCULOC)
			{$this->Vinculo_Contenido=$VINCULOT; $this->Vinculo_Campo=$VINCULOC;}
			if($Cab && $CoB) {$this->Campo_busqueda1=$CaB; $this->Contenido_busqueda1=$CoB; $this->Busqueda_exacta=$ExB; }
			if($TI) $this->Info_cabecera=$TI;
			$this->busca_columnas();
			$this->construye_query();
		}
		else
		{
			$this->Error='No tiene acceso a esta tabla.';
		}
	}

	function Verifica_variables($Dato)		# REEMPLAZO DE VALORES DE VARIABLES  DENTRO DE LAS FORMULAS
	{
		foreach($_SESSION as $Clave => $Valor)
		{
			$Dato=str_replace("\$".$Clave,$Valor,$Dato);
		}
		return $Dato;
	}

	function aparece()
	{
		html($this->titulo_tabla);
		echo "
			<script language='javascript'>
				var Pagina_actual=1;
				var Cantidad_paginas=$this->Cantidad_paginas;
				var Lineas_por_pagina=$this->Lineas_por_pagina;
				var Modifica=$this->Modifica;
				var EQ='$this->E_query';
				var OQ='$this->E_orden_query';
				var CQ='$this->E_where_query';
				var Orden_Campo='';
				var Orden_Tipo='';
				var Menu_contextual=false;
				var Campo_Vinculo='$this->Vinculo_Campo';
				var Contenido_Vinculo='$this->Vinculo_Contenido';
				var Campo_busqueda1='';	var Contenido_busqueda1='';
				var Campo_busqueda2='';	var Contenido_busqueda2='';
				var Campo_busqueda3='';	var Contenido_busqueda3='';
				var Busqueda_exacta=0;
				var Vinculo_primario='$this->Vinculo_primario';
				var Borrado_masivo=0;
				var Modificacion_directa=new Array();
                var TextoTabla=\"$this->TextoTabla\";
                var Buscador_relacionado=new Array($this->Buscador_relacionado);

                if(parent.document.getElementById('Smarco'))
                	var Tamano_marco=parent.document.getElementById('Smarco').rows;
                else
                	var Tamano_marco=0;

                function cambio_tamano_marco(Dato)
                {
	                if(parent.document.getElementById('Smarco'))
	                {
						var Marco=parent.document.getElementById('Smarco');
		                if(Dato==1) {Marco.rows='10%,*';return true;}
				        if(Dato==2) {Marco.rows='100%,*';return true;}
				        Marco.rows=Tamano_marco;
					}
			        return true;
                }

				function inicial()
				{
					cambia_pagina(0);
					delCookie('SC_TOP');delCookie('SC_LEFT');
				}

				function fija()
				{
					var DD=document.getElementById('Detalle_tabla$this->id_tabla');
					var TT=document.getElementById('Titulos_tabla');
					DD.height=document.body.clientHeight-TT.clientHeight-50;

				}

				function cambia_pagina(Dato)
				{
					if(Dato==-1 && Pagina_actual>1) {Pagina_actual--;repinta_detalle();}
					if(Dato==1 && Pagina_actual<Cantidad_paginas) {Pagina_actual++;repinta_detalle();}
				}

				function ir_pagina(Dato)
				{
					if(Dato==0) {Pagina_actual=1;repinta_detalle();}
					if(Dato==1) {Pagina_actual=Cantidad_paginas;repinta_detalle();}
				}

				function add_campo_modificacion(Campo)
				{
					var Esta=false;
					for(i=0;i<Modificacion_directa.length;i++)
					{
						if(Campo===Modificacion_directa[i]) Esta=true;
					}
					if(!Esta)
					{
						Modificacion_directa[Modificacion_directa.length]=Campo;
					}
				}

				function repinta_detalle()
				{
					document.getElementById('tPagina').innerHTML='<b>'+Pagina_actual+'</b>';
					var Url='marcoindex.php?Acc=brow_tabla_det';
					Url+='&OC='+Orden_Campo+'&OT='+Orden_Tipo+'&LPP='+Lineas_por_pagina+'&P='+Pagina_actual;
					Url+='&Q='+EQ+'&CQ='+CQ+'&OQ='+OQ+'&IdT=$this->id_tabla&Nombre_tabla=$this->nombre_tabla';
					Url+='&VP='+Vinculo_primario;
	                if(Campo_busqueda1)
	                { Url+='&CaB1='+Campo_busqueda1+'&CoB1='+Contenido_busqueda1+'&ExB='+Busqueda_exacta;}
	                if(Campo_busqueda2)
	                { Url+='&CaB2='+Campo_busqueda2+'&CoB2='+Contenido_busqueda2;}
	                if(Campo_busqueda3)
	                { Url+='&CaB3='+Campo_busqueda3+'&CoB3='+Contenido_busqueda3;}
	                if(Borrado_masivo) Url+='&BMM=1';
					Url+='&MoDir='+extrae_modificacion_directa();
					window.open(Url,'Detalle_tabla$this->id_tabla');
				}

				function repinta_detalle_registro(Span,Contenido)
				{
					var Detalle=document.getElementById('Detalle_tabla$this->id_tabla');
					var Docdetalle=Detalle.contentWindow;
					var Objeto=Docdetalle.document.getElementById(Span);
					Objeto.innerHTML=Contenido;
				}

				function extrae_modificacion_directa()
				{
					var Resultado='';
					for(i=0;i<Modificacion_directa.length;i++)
					{
						if(Resultado.length) Resultado+=',';
						Resultado+=Modificacion_directa[i];
					}
					return Resultado;
				}

				function m_ct()     // menu contextual del top de la tabla o de los titulos de las columnas
				{
					Menu=document.getElementById('Menu_context_titulo');
					Menu.style.visibility='visible';
					Menu.style.left=mouseX;
					Menu.style.top=mouseY-10;
					var Contenido=\"<table border cellspacing='0' cellpadding='0' bgcolor='#eeddff' name='Context_Opciones' id='Context_Opciones'>\";
					Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"oculta_mc();\\\" align='center'><b>Cerrar menu</b></td></tr>\";
					Contenido+=\"<tr><td style='cursor:pointer' onclick='orden_original();' nowrap='yes'><img src='gifs/standar/Next.png' border='0' align='bottom'> Establecer Orden Original</td></tr>\";
					if(Campo_busqueda1 && Contenido_busqueda1)
					{
						Contenido+=\"<tr><td nowrap='yes' style='cursor:pointer' onclick=\\\"vertodos()\\\">\";
						Contenido+=\"<img src='gifs/standar/Next.png' border='0' align='bottom'> Ver todos los registros</td></tr>\";
					}";
			if($_SESSION['Disenador'])
			{
				echo "Contenido+=\"<tr><td style='cursor:pointer' onclick='cambia_borrado_masivo();' nowrap='yes'><img src='gifs/standar/Next.png' border='0' align='bottom'> Activar borrado Masivo</td></tr>\";";
			}
			echo "Contenido+=\"</table>\";
					Menu.innerHTML=Contenido;
					Menu_contextual=true;
				}

				function cambia_borrado_masivo()
				{
					if(Borrado_masivo==0)
					{oculta_mc();Borrado_masivo=1;repinta_detalle();}
					else
					{oculta_mc();Borrado_masivo=0;repinta_detalle();}
				}

				function m_c(Campo,Idcampo)
				{
					var Valor_busqueda=getCookie('B_".$this->nombre_tabla."_'+Campo);
					Menu=document.getElementById('Menu_context_titulo');
					Menu.style.visibility='visible';
					Menu.style.left=mouseX;
					Menu.style.top=mouseY-10;
					var Contenido=\"<table border cellspacing='0' cellpadding='0' bgcolor='#eeddff' name='Context_Opciones' id='Context_Opciones'>\";
					Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"oculta_mc();\\\" align='center'><b>Cerrar menu</b></td></tr>\";
					Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"orden_campo('\"+Campo+\"',1);\\\">\";
					Contenido+=\"<IMG SRC='gifs/standar/Up.png' border='0' height='12' vspace='0' hspace='0' align='bottom'> Ordenar Ascencentemente</td></tr>\";
					Contenido+=\"<tr><td style='cursor:pointer' nowrap='yes' onclick=\\\"orden_campo('\"+Campo+\"',2);\\\">\";
					Contenido+=\"<IMG SRC='gifs/standar/Down.png' border='0' height='16' vspace='0' hspace='0' align='bottom'> Ordenar Descendentemente</td></tr>\";
					Contenido+=\"<tr><td nowrap='yes' >Buscar: (Y<input type='checkbox' name='bmultiple' id='bmultiple'>)\";
					Contenido+=\"<INPUT TYPE='text' name='T_LIKE' id='T_LIKE' value='\"+Valor_busqueda+\"' onkeydown=\\\"onkeybusqueda('\"+Campo+\"',this.value,event,1);\\\" size='18' style='font-size:10;font-family:arial;'>\";
					Contenido+=\"<img src='gifs/standar/Preview.png' border='0' height='16' align='middle' style='cursor:pointer' onclick=\\\"activar_busqueda('\"+Campo+\"',document.getElementById('T_LIKE').value,1);\\\"></td></tr>\";
					Contenido+=\"<tr><td nowrap='yes'>Busqueda exacta: <INPUT TYPE='text' name='T_LIKE2' id='T_LIKE2' \";
					Contenido+=\"onkeydown=\\\"onkeybusqueda('\"+Campo+\"',this.value,event,2);\\\" size='18' style='font-size:10;font-family:arial;'>\";
					Contenido+=\"<img src='gifs/standar/Preview.png' border='0' height='16' align='middle' style='cursor:pointer' onclick=\\\"activar_busqueda('\"+Campo+\"',document.getElementById('T_LIKE2').value,2);\\\"></td></tr>\";
					if(Campo_busqueda1 && Contenido_busqueda1)
					{
						Contenido+=\"<tr><td nowrap='yes' style='cursor:pointer' onclick=\\\"vertodos()\\\">\";
						Contenido+=\"<img src='gifs/standar/Next.png' border='0' align='bottom'> Ver todos los registros</td></tr>\";
					} ";
		if($_SESSION['Disenador'])
		{
			echo "Contenido+=\"<tr><td bgcolor='#ffffdd' align='center'><b>".$this->nombre_tabla.".\"+Campo+\"</b></td></tr>\";
					Contenido+=\"<tr><td style='cursor:pointer' onclick=\\\"oculta_mc();modal('marcoindex.php?Acc=definicion_campo&Nombre_tabla=".$this->nombre_tabla."&idcampo=\"+Idcampo+\"',10,10,600,850,'Definicion_campo')\\\">\";
					Contenido+=\"<img src='gifs/standar/dsn_config.png' border=0 height=16 vspace=0 hspace=0 align='bottom'> Configurar este campo</td></tr>\";
					Contenido+=\"<tr><td style='cursor:pointer' onclick=\\\"oculta_mc();modal('marcoindex.php?Acc=ajuste_tit_columna&idc=\"+Idcampo+\"&t=ai&Not=".$this->nombre_tabla."');\\\">\";
					Contenido+=\"<img src='gifs/izq.gif' border=0 vspace=0 hspace=0 align='bottom'> Justificar a la Izquierda</td></tr>\";
					Contenido+=\"<tr><td style='cursor:pointer' onclick=\\\"oculta_mc();modal('marcoindex.php?Acc=ajuste_tit_columna&idc=\"+Idcampo+\"&t=ad&Not=".$this->nombre_tabla."');\\\">\";
					Contenido+=\"<img src='gifs/der.gif' border=0 vspace=0 hspace=0 align='bottom'> Justificar a la Derecha</td></tr>\";
					Contenido+=\"<tr><td style='cursor:pointer' onclick=\\\"oculta_mc();modal('marcoindex.php?Acc=ajuste_tit_columna&idc=\"+Idcampo+\"&t=ac&Not=".$this->nombre_tabla."');\\\">\";
					Contenido+=\"<img src='gifs/cen.gif' border=0 vspace=0 hspace=0 align='bottom'> Ajustar al Centro</td></tr>\";
					Contenido+=\"<tr><td style='cursor:pointer' onclick=\\\"oculta_mc();modal('marcoindex.php?Acc=ajuste_tit_columna&idc=\"+Idcampo+\"&t=aj&Not=".$this->nombre_tabla."');\\\">\";
					Contenido+=\"<img src='gifs/jus.gif' border=0 vspace=0 hspace=0 align='bottom'> Justificado completo</td></tr>\";
					Contenido+=\"<tr><td style='cursor:pointer' onclick=\\\"oculta_mc();modal('marcoindex.php?Acc=ajuste_tit_columna&idc=\"+Idcampo+\"&t=cs&Not=".$this->nombre_tabla."');\\\">\";
					Contenido+=\"<img src='gifs/com.gif' border=0 vspace=0 hspace=0 align='bottom'> Poner separador de miles</td></tr>\";
					Contenido+=\"<tr><td style='cursor:pointer' onclick=\\\"oculta_mc();modal('marcoindex.php?Acc=ajuste_tit_columna&idc=\"+Idcampo+\"&t=cn&Not=".$this->nombre_tabla."');\\\">\";
					Contenido+=\"<img src='gifs/noc.gif' border=0 vspace=0 hspace=0 align='bottom'> Quitar separador de miles</td></tr>\";";
		}
		echo "	Contenido+=\"<tr><td style='cursor:pointer;' onclick=\\\"oculta_mc();add_campo_modificacion('\"+Campo+\"');repinta_detalle();\\\">\";
					Contenido+=\"<img src='gifs/standar/edita_registro.png' border=0 vspace=0 hspace=0 align='bottom'>Activar modificacion</td></tr>\";
					Contenido+=\"<tr><td style='cursor:pointer' onclick=\\\"oculta_mc();modal('marcoindex.php?Acc=ajuste_tit_columna&idc=\"+Campo+\"&t=oc&Not=".$this->id_tabla."');\\\">\";
					Contenido+=\"<img src='gifs/standar/ocultar.png' border=0 vspace=0 hspace=0 align='bottom'> Ocultar esta columna</td></tr>\";
					Contenido+=\"</table>\";
					Menu.innerHTML=Contenido;
					document.getElementById('T_LIKE').focus();
					document.getElementById('T_LIKE').select();
					Menu_contextual=true;
				}

				function orden_original()
				{
					oculta_mc();Orden_Campo='';Orden_Tipo='';repinta_detalle();
				}

				function vertodos()
				{
					Campo_busqueda='';Contenido_busqueda1='';Busqueda_exacta=0;oculta_mc();repinta_detalle();
				}

				function onkeybusqueda(Campo,Valor,Evento,Exacta)
				{
					var keynum;var Caracter;
					if(window.event) keynum = Evento.keyCode;
					else if(Evento.which) keynum = Evento.which;
					if(keynum==13)
					{
						activar_busqueda(Campo,Valor,Exacta);
					}
				}

				function activar_busqueda(Campo,Valor,Exacta)
				{
					var Expira=new Date();
					Expira.setTime(Expira.getTime()+30*24*60*60*1000);
					setCookie('B_".$this->nombre_tabla."_'+Campo,Valor,Expira);
					for(var i=0;i<Buscador_relacionado.length;i++)
					{
						if(Buscador_relacionado[i]==Campo)
						{
							Campo=Buscador_relacionado[i+1];
							break;
						}
					}
					var BMultiple=document.getElementById('bmultiple');
					if(BMultiple.checked)
					{
						if(!Campo_busqueda1)
						{
							Campo_busqueda1=Campo;
							Contenido_busqueda1=Valor;
						}
						else if(!Campo_busqueda2)
						{
							Campo_busqueda2=Campo;
							Contenido_busqueda2=Valor;
						}
						else
						{
							Campo_busqueda3=Campo;
							Contenido_busqueda3=Valor;
						}
					}
					else
					{
						Campo_busqueda1=Campo;
						Contenido_busqueda1=Valor;
						Campo_busqueda2='';
						Campo_busqueda3='';
					}
					if(Exacta==1) Busqueda_exacta=0; else Busqueda_exacta=1;
					oculta_mc();
					ir_pagina(0);
				}

				function orden_campo(Campo,Tipo)
				{
					if(Tipo=='1') Orden_Tipo='asc'; else Orden_Tipo='desc';
					Orden_Campo=Campo;
					repinta_detalle();
					oculta_mc();
				}

				function oculta_mc()
				{
					Menu=document.getElementById('Menu_context_titulo');
					Menu.style.visibility='hidden';
				}

				function oculta_mct()   // oculta el menu contextual de registro
				{
					if(Menu_contextual)
					{
						Menu=document.getElementById('context_tabla');
						Menu.style.visibility='hidden';
						Menu.style.height=50;
						Menu.style.width=50;
						Menu.src='gifs/standar/loading.gif';
						Menu_contextual=false;
					}
				}

				function funcion_click(Objeto)
				{
					var Idobjeto=Objeto.id;
					if(Idobjeto.substr(0,3)=='th_') oculta('c'+Idobjeto);
					if(Idobjeto.substr(0,4)=='top_') oculta('c'+Idobjeto);
				}

				function verifica_escape(Evento)
				{
					var keynum;
					var Caracter;
					if(window.event) // IE
						keynum = Evento.keyCode;
					else if(Evento.which) // Netscape/Firefox/Opera
						keynum = Evento.which;
					if( keynum==27)
					{
					   if(Menu_contextual) oculta_mc();
					   else
					   {
						if(confirm('Desea Cerrar la ventana?'))
						{
							window.close();
							void(null);
						}
					   }
					}
					if(keynum==33)
					{
						cambia_pagina(-1);
					}
					if(keynum==34)
					{
						cambia_pagina(1);
					}
				}

				function cambia_lineas_por_pagina(Dato)
				{
					Lineas_por_pagina=Number(Dato);
					Cantidad_paginas=parseInt($this->Cantidad_registros/Lineas_por_pagina)+1;
					document.getElementById('tCantidad_paginas').innerHTML=Cantidad_paginas;
					repinta_detalle();
				}

				function activa_modificacion_global()
				{
					Modificacion_directa=new Array();
					";
		for($i=0;$i<count($this->Cols);$i++)
		echo "add_campo_modificacion('".$this->Cols[$i]->campo."');";
		echo "
					repinta_detalle();
				}


		</script>";
		if($this->Error)
		{
			echo "<body leftmargin=20 topmargin=20 rightmargin=20 bottommargin=20>
               <table><tr><td><img src='gifs/standar/stop.png' border=0></td><td><font color='green'><b>No tiene acceso a esta tabla</b></font></td></table></body>";
		}
		else
		{
			echo "<body leftmargin='2' topmargin='0' rightmargin='5' bottommargin='0'
		   		onload='$this->Java_onload_body' onresize='fija();' bgcolor='#eeeeff'
				onkeydown='verifica_escape(event);' >

         <table width='100%' border=0 cellspacing=1 oncontextmenu=\"m_ct();return false;\">
            <tr><td id='top_$this->nombre_tabla' width='30%' nowrap='yes' >".
			($this->Icono?"<img src='$this->Icono' height=20 border=0 align='top'> ":"").
   			"<b><font style='font-size:16'>$this->titulo_tabla</font></b> [<span id='tCantidad_registros'>".coma($this->Cantidad_registros)."</span> registros]
   			[<span id='tCantidad_paginas'>$this->Cantidad_paginas</span> páginas]".
			($_SESSION['Disenador']?" <span style='background-color:ffffdd'>&nbsp;$this->nombre_tabla&nbsp;</span>":"").
            "</td><td width='20%' nowrap='yes'><b><font style='font-size:12'>$this->Info_cabecera</font></b></td><td></td></table>

   		<iframe id='context_tabla' name='context_tabla' width='50' height='50' frameborder='yes' src='gifs/standar/loading.gif'
   				style='visibility:hidden;position:absolute;border-style:solid;border-width:2px;background-color:#fdfdfd;'></iframe>
		<span id='Menu_context_titulo' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#fdfdfd;'></span>";
			$this->pinta_titulos();
			$this->pinta_detalle();
			$this->pinta_tools();
			echo "</body>";
		}
	}

	function busca_columnas()
	{

		$Tabla_control=$this->nombre_tabla.'_t';
		require('inc/link.php');
		if($Campos=mysql_query("select * from $Tabla_control where find_in_set(campo,'$this->Vercampos') order by capa,orden,suborden",$LINK))
		{
			$Contador=0;
			while($Campo=mysql_fetch_object($Campos))
			{
				$this->Cols[$Contador] = new brow_columna($Campo,$LINK,$this->nombre_tabla);    // Pasa el conector LINK para verificar la existencia de funciones T_
				if($this->Cols[$Contador]->relacionado)
				{
					$this->Buscador_relacionado.=($this->Buscador_relacionado?',':'')."'".$this->Cols[$Contador]->campo."','T_".$this->Cols[$Contador]->tabla_relacion."(".$this->Cols[$Contador]->campo.")'";
				}
				elseif($this->Cols[$Contador->menudirecto])
				{
					$this->Buscador_relacionado.=($this->Buscador_relacionado?',':'')."'".$this->Cols[$Contador]->campo."','M_".$this->nombre_tabla."_".$this->Cols[$Contador]->campo."(".$this->Cols[$Contador]->campo.")'";
				}
				$Contador++;
			}
		}
		mysql_close($LINK);
	}

	function pinta_titulos()
	{
		echo "<table id='Titulos_tabla' cellspacing=1 style='empty-cells:show;' bgcolor='#bbddcc'><tr>";
		for($i=0;$i<count($this->Cols);$i++) $this->Cols[$i]->pinta_titulo();
		echo "</tr></table>
		";
	}

	function pinta_detalle()
	{
		if(strlen($this->Vercampos)<2) echo 'No ha seleccionado campos';
		else
		echo "
			<iframe name='Detalle_tabla$this->id_tabla' id='Detalle_tabla$this->id_tabla'
			src='marcoindex.php?Acc=cargando_informacion'
			frameborder='no' height='300' width='100%' scrolling='auto'></iframe>";
	}

	function construye_query()
	{
		$Where=false;
		$this->Query="Select ";
		for($i=0;$i<count($this->Cols);$i++)
		{
			if($this->Cols[$i]->relacionado)
			{
				$this->Query.=($i>0?',':'').'T_'.$this->Cols[$i]->tabla_relacion.'('.$this->Cols[$i]->campo.') as '.$this->Cols[$i]->campo;
			}
			elseif($this->Cols[$i]->menudirecto)
			{
				$this->Query.=($i>0?',':'').'M_'.$this->nombre_tabla.'_'.$this->Cols[$i]->campo.'('.$this->Cols[$i]->campo.') as '.$this->Cols[$i]->campo;
			}
			else
			{
				$this->Query.=($i>0?',':'').$this->Cols[$i]->campo;
			}
		}
		$this->Query.=" from $this->nombre_tabla ";
		if($this->Condicion_sql) { $Where=true; $this->Condiciones_query=" Where ( $this->Condicion_sql ) ";}
		if($this->Vinculo_Campo) { $this->Condiciones_query.=($Where?" and ":" Where")." $this->Vinculo_Campo='$this->Vinculo_Contenido' "; $Where=true;}
		$this->Cantidad_registros=qo1("select count(*) from $this->nombre_tabla ".($Where?$this->Condiciones_query:""));
		$this->Cantidad_paginas=intval($this->Cantidad_registros/$this->Lineas_por_pagina)+1;
		if($this->Cantidad_paginas==0) $this->Cantidad_paginas=1;
		$Limite_inicial=($this->Pagina-1)*$this->Lineas_por_pagina;
		$this->E_query=urlencode(base64_encode($this->Query));
		$this->E_orden_query=urlencode(base64_encode($this->Orden_query));
		$this->E_where_query=urlencode(base64_encode($this->Condiciones_query));
	}

	function pinta_tools()
	{
		echo "<table><tr><td valign='top' nowrap='yes'>
            <img src='gifs/standar/refrescar.png' align='top' border=0 height=20
                     onmouseover=\"this.src='gifs/standar/refrescar_ovr.png';\"
                     onmouseout=\"this.src='gifs/standar/refrescar.png';\"  alt='Refrescar el detalle' title='Refrescar el detalle'
                     onclick='repinta_detalle();' >

               <img src='gifs/standar/pagina_inicial.png' border=0 align='top' onclick='ir_pagina(0);'>
        		<img src='gifs/standar/pagina_menos.png' border=0 align='top' onclick='cambia_pagina(-1);'>
        		<span id='tPagina'><b>0</b></span>
				<img src='gifs/standar/pagina_mas.png' border=0 align='top' onclick='cambia_pagina(1);''>
				<img src='gifs/standar/pagina_final.png' border=0 align='top' onclick='ir_pagina(1);'>
				Lineas por página: <select onchange='cambia_lineas_por_pagina(this.value);' style='font-size:9px;padding:0;'>
					<option value=20>20</option>
					<option value=50>50</option>
					<option value=100>100</option>
					<option value=200>200</option>
					<option value=400>400</option>
				</select></td>";
		if($this->Adiciona) echo "<td valign='top' nowrap='yes'>&nbsp;&nbsp;&nbsp;&nbsp;<a class='sinfo' style='cursor:pointer' onclick='Detalle_tabla$this->id_tabla.mod_reg(0);'>
		                            <img src='gifs/standar/nuevo_registro.png' border='0' align='bottom'><span style='width:150px'>Adicionar un registro</span></a></td>";

		echo "</td>
         <td nowrap='yes'><A class='sinfo' style='cursor:pointer;' onclick=\"window.open('marcoindex.php?Acc=ver_mas_campos&Num_Tabla=$this->id_tabla','Detalle_tabla$this->id_tabla');\" >
	        <img src='gifs/standar/ver_mas_campos.png' border=0><span style='width:200px'>Ver mas campos en esta ventana</span></A>
	        <a class='sinfo' style='cursor:pointer;' onclick='cambio_tamano_marco(1);'>
		        <img src='gifs/standar/marcoup.png' hspace=3 border=0'><span style='width:300px'>Reducir el tamaño del marco</span></a>
		    <a class='sinfo' style='cursor:pointer;' onclick='cambio_tamano_marco(0);'>
			    <img src='gifs/standar/marco.png' hspace=3 border='0'><span style='width:300px'>Normalizar el tamaño del marco</span></a>
	        <a class='sinfo' style='cursor:pointer;' onclick='cambio_tamano_marco(2);'>
		        <img src='gifs/standar/marcodown.png' hspace=3 border=0'><span style='width:300px'>Ampliar el tamaño del marco</span></a>

         </td>";
		if($_SESSION['Disenador'])
		{
			$NT = tu('usuario_tab', 'id');
			echo "<td nowrap='yes'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a class='sinfo' onclick=\"modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$NT&id=$this->id_tabla',0,0,700,900);\">
            <IMG SRC='gifs/standar/dsn_config.png' HSPACE='5' BORDER='0' ><span style='width:150px'>Modificar cofiguracion</span></a>
            <a class='sinfo' onclick=\"window.open('marcoindex.php?Acc=control_vertabla&nt=$this->nombre_tabla','Detalle_tabla$this->id_tabla');\">
            <IMG SRC='gifs/standar/dsn_estructura.png' HSPACE='5' BORDER='0' ><span style='width:150px'>Modificar Estructura</span></a>
            <a class='sinfo' onclick=\"modal('marcoindex.php?Acc=sql',10,10,700,900,'_blank');\">
            <IMG SRC='gifs/standar/dsn_sql.png' HSPACE='5' BORDER=0 ><span style='width:100px'>Correr sql</span></a>
            <a class='sinfo' onclick=\"modal('marcoindex.php?Acc=duplicar_permiso&NT=$this->id_tabla',10,10,200,200,'_blank');\">
            <IMG SRC='gifs/standar/dsn_usuario.png' HSPACE='5' BORDER='0' ><span style='width:300px'>Duplicar Permiso a otro perfil de seguridad</span></a>
				<a class='sinfo' onclick=\"modal('marcoindex.php?Acc=control_mantenimiento_orden&t1=".$this->nombre_tabla.",".$this->nombre_tabla."_t&orden=backup3',10,10,200,200,'_blank');\">
				<img src='gifs/standar/exportar.png' hspace='5' border='0'><span style='width:300px'>Descargar una copia de esta tabla</span></a>
				<a class='sinfo' onclick=\"modal('marcoindex.php?Acc=control_cargatabla&t=".$this->nombre_tabla."',10,10,500,800,'_blank');\">
				<img src='gifs/standar/importar.png' hspace='5' border='0'><span style='width:300px'>Cargar esta tabla con archivo plano</span></a>

			</td>";
		}
		echo "</tr></table>";
	}

}

//------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------


class brow_campo
{
	var $nombre_campo;  // nombre fisico del campo
	var $alineacion; // alineacion del campo
	var $formato_coma=false;
	var $decimales_coma='0';
	var $Imagen=0; // si la presentación es imagen
	var $Pdf=0; // si la presentación es un archivo pdf
	var $Checkbox=0; // si la presentación es check
	var $Url=0; // si es una dirección url de internet
	var $Email=0; // si es una dirección de correo electrónico
	var $Color=0; // si es una casilla de color
	var $tdp=''; // propiedades td del campo de captura en el brow
	var $MoDirecta=false; // modificacion directa
	var $Configuracion=false; //
	var $Titulo='';
	var $Blob=false; // si el campo es de tipo blob
	var $Area=false; // si el campo es de tipo text area

	function brow_campo($Campo,$LINK)
	{
		global $MoDir,$Nombre_tabla;
		/* la variable $Campo consta de:
	  0 = nombre del campo
	  1 = alineacion
	  2 = formato coma
	  3 = cantidad decimales
	  4 = Imagen
	  */
		$this->nombre_campo=$Campo[0];
		if(strpos(',,'.$MoDir.',,',','.$this->nombre_campo.','))
		{
			$this->MoDirecta=true;
			$this->Configuracion=qom("select * from ".$Nombre_tabla."_t where campo='".$this->nombre_campo."'",$LINK);
		}
		switch($Campo[1])
		{
			case "I": $this->alineacion="left";break;
			case "D": $this->alineacion="right";break;
			case "C": $this->alineacion="center";break;
			case "J": $this->alineacion="justify";break;
		}
		if($Campo[2]) // formato numerico separado por comas
		{
			$this->formato_coma=true;
			$this->decimales_coma=$Campo[3]; // numero de decimales
		}
		$this->Imagen=$Campo[4]; // imagen
		$this->Checkbox=$Campo[5]; // presentacion checkbox
		$this->Url=$Campo[6]; // dirección Url
		$this->Email=$Campo[7]; // dirección de correo electrónico
		$this->Color=$Campo[8]; // casilla para capturar color
		if(strpos(' '.strtoupper($Campo[9]),'BLOB')) $this->Blob=true;
		if(strpos(' '.strtoupper($Campo[9]),'TEXT')) $this->Area=true;
	}

	function pinta($R,$BMM)
	{
		global $Nombre_tabla;
		$Capa='cd_'.$R->id.'_'.$this->nombre_campo;
		eval("\$Contenido=\$R->$this->nombre_campo;");
		echo "<td align='$this->alineacion' valign='top'".
			($this->MoDirecta?" onmouseover=\"muestra('$Capa');\"
			onclick=\"oculta_ultima_captura();Ultima_captura='$Capa';\"
			onmouseout=\"oculta('$Capa');\" ":"");
//			($this->Area?" width='600' ":"");
		if($this->nombre_campo=='id') echo " onclick='m_c($R->id);' ";
		if($BMM && $this->nombre_campo=='id') echo " nowrap='yes' ><input type='checkbox' name='Bmm_$R->id' id='Bmm_$R->id'";
		echo ">";
		if($this->MoDirecta)
		{
			$Span=$this->nombre_campo.'_'.$R->id;
			echo captura_directa($R,$this->nombre_campo,$Contenido,$Nombre_tabla,$this->Configuracion)."<span name='$Span' id='$Span'>";
		}
		if($this->formato_coma) 	echo coma_formatd($Contenido,$this->decimales_coma);
		elseif($this->Imagen)
		{
			if(strpos(' '.strtolower($Contenido),'.pdf')) echo "<img src='gifs/pdf.jpg' border=0' onclick=\"modal('$Contenido',0,0,600,800);\" >";
			else echo "<img src='".($Contenido?$Contenido."' onclick='modal(this.src,0,0,600,800);'":"gifs/foto_gris1.gif")."' border=0 height=40 >";
		}
		elseif($this->Checkbox)	echo ($Contenido?"<img src='gifs/standar/si.png' border=0 hspace=0 vspace=0>":'&nbsp;');
		elseif($this->Url) echo "<a href='$Contenido' target='_blank'>$Contenido</a>";
		elseif($this->Email) echo "<a href='mailto:$Contenido' >$Contenido</a>";
		elseif($this->Color) echo "<span STYLE='background-color:$Contenido'>$Contenido</span>";
		elseif($this->Blob)
		{
			if($Contenido)
			{
				echo "<img src='inc/imgblob.php?T=$Nombre_tabla&C=$this->nombre_campo&Id=$R->id' border='0' height='40'
				onclick=\"modal('inc/imgblob.php?T=$Nombre_tabla&C=$this->nombre_campo&Id=$R->id',0,0,700,900,'_blank');\">";
			}
			else
			{
				echo "<img src='gifs/standar/img_neutra.png' border='0'>";
			}
		}
		elseif($this->Area)
		{
			$Capat='ct_'.$R->id.'_'.$this->nombre_campo;
			echo "<a class='info' >".substr($Contenido,0,70).'...'."<span id='$Capat' style='width:400px;text-align:justify;'>".nl2br($Contenido)."</span></a>";
		}
		else	echo $Contenido;
		if($this->MoDirecta) echo "</span>";
		echo "</td>";
	}
}

class brow_tabla_detalle
{
	var $Query='';  // instruccion sql
	var $Orden_query=''; // orden inicial del query
	var $Condiciones_query=''; // condiciones del query
	var $Limit_query=''; // Limit del query
	var $Campos=array(); // lista de campos separada por coma
	var $Java_fija='';  // instrucciones javascript para el onload del body
	var $Java_refija=''; // instrucciones javascript para volver a fijar los anchos del cuerpo de acuerdo al titulo
	var $Limite_inicial=0; // limite inicial, endonde empiezan los registros
	var $Limite_final=0; // Limite final en donde terminan los registros
	var $Lineas_por_pagina=20; // lineas por pagina
	var $Pagina=1; // Pagina actual
	var $id_tabla=0;  // id de la tabla en usuario_tab
	var $nombre_tabla='';  // nombre de la tabla
	var $Cantidad_registros=0; // cantidad de registros obtenidos en el query completo
	var $Borrado_masivo=false; // Control de activacion de checkbox para borrado masivo de registros
	var $Titulos=''; // titulos de las columnas
	var $Sombreado=false; // intercambio de sombras en la impresion del detalle

	function brow_tabla_detalle($Query,$CQ,$Pagina,$Lineas_por_pagina,$OQ,$OC='',$OT='asc',$Id_tabla=0)
	{
		global $CaB1,$CoB1,$CaB2,$CoB2,$CaB3,$CoB3,$ExB,$Nombre_tabla,$VP,$BMM,$MoDir;
		$this->id_tabla=$Id_tabla;
		$this->nombre_tabla=$Nombre_tabla;
		$this->Query=stripcslashes(base64_decode($Query));
		if(l($this->Query,10)!='Select id,') $this->Query='Select id,'.substr($this->Query,7);
		$this->Condiciones_query=stripcslashes(base64_decode($CQ));
		if($VP)
		{
			$this->Condiciones_query.=($this->Condiciones_query?" and ":" Where ").$VP." = '".$_SESSION['Id_alterno']."' ";
		}
		if($CaB1)
		{
			$this->Condiciones_query.=($this->Condiciones_query?" and ":" Where ").($ExB?"$CaB1 = '$CoB1' ":"$CaB1 like '%$CoB1%' ");
			if($CaB2 && $CoB2) $this->Condiciones_query.=" and $CaB2 like '%$CoB2%' ";
			if($CaB3 && $CoB3) $this->Condiciones_query.=" and $CaB3 like '%$CoB3%' ";
		}
		$this->Cantidad_registros=qo1("select count(*) from $this->nombre_tabla $this->Condiciones_query");
		$this->Orden_query=" Order by ".($OC && $OT?" $OC $OT ":urldecode(base64_decode($OQ)));
		include('inc/link.php');
		$Vercampos=qo1m("select vercampos from usuario_tab where id=$this->id_tabla",$LINK);
		if($LCampos=mysql_query("select campo,alinea,coma,tipo,caja from ".$this->nombre_tabla."_t where find_in_set(campo,'$Vercampos') order by capa,orden,suborden",$LINK))
		{
			while($Campo=mysql_fetch_object($LCampos))
			{
				if($Pd=strpos($Campo->tipo,','))
				{$Decimales=substr($Campo->tipo,$Pd+1);$Decimales=substr($Decimales,0,strpos($Decimales,')'));}
				else $Decimales=0;
				if(r($Campo->campo,2)=='_f') $Imagen=1; else $Imagen=0;
				if(r($Campo->campo,2)=='_w') $Url=1; else $Url=0;
				if(r($Campo->campo,2)=='_e') $Email=1; else $Email=0;
				if(r($Campo->campo,3)=='_co') $Color=1; else $Color=0;

				$Campito[0]=$Campo->campo;
				$Campito[1]=$Campo->alinea;
				$Campito[2]=$Campo->coma;
				$Campito[3]=$Decimales;
				$Campito[4]=$Imagen;
				$Campito[5]=$Campo->caja;
				$Campito[6]=$Url;
				$Campito[7]=$Email;
				$Campito[8]=$Color;
				$Campito[9]=$Campo->tipo;
				$this->Campos[]= new brow_campo($Campito,$LINK);
				if(strpos(' '.strtoupper($Campo->tipo),'TEXT'))
				{
					$Ancho=" width='300' ";
				}
				else $Ancho="";
				$this->Java_fija.="fija_ancho('".$Campito[0]."');";
				$this->Titulos.="<th style='background-color:ffffff;color:ffffff;'nowrap='yes' id='td_".$Campito[0]."' $Ancho>".qo1m("select descripcion from ".$this->nombre_tabla."_t where campo='".$Campito[0]."' ",$LINK)."</th>";
			}
		}
		$this->Titulos.='</tr>';
		mysql_close($LINK);
		$this->Lineas_por_pagina=$Lineas_por_pagina;
		$this->Pagina=$Pagina;
		$this->Limite_inicial=($Pagina-1)*$this->Lineas_por_pagina;
		$this->Limite_final=$this->Limite_inicial+$this->Lineas_por_pagina;
		$this->Limit_query=" Limit $this->Limite_inicial,$this->Lineas_por_pagina ";
		if($BMM) $this->Borrado_masivo=true;
	}

	function aparece()
	{
		html();
		echo "
		<script language='javascript'>
		var Menu_contextual=false;
		var Ventana_edicion=false;
		var Topscrolled=0;
		var Leftscrolled=0;
		var Cajas= new Array();
		var Contador_Bm=0;
		var Ultima_captura='';
		var Iniscroll=0;
		var MC_Reg=0;
		var MC_Reg_color='';

		function oculta_ultima_captura()
		{
			if(Ultima_captura.length>0)
			{
				document.getElementById('Ultima_captura').onmouseout='oculta(\"'+Ultima_captura+'\");';
				oculta(Ultima_captura);
			}
		}

		function fija() // invoca la fijacion de columnas y titulos de columnas, adicionalmente el ancho total de la tabla
		{
			var Ob=document.getElementById('Edicion_$this->id_tabla');
			Ob.style.height=document.body.clientHeight-10;
			Ob.style.width=parent.document.body.clientWidth-10;
			document.body.scrollTop=leerCookie('SC_TOP');
			document.body.scrollLeft=leerCookie('SC_LEFT');
			parent.document.getElementById('tCantidad_registros').innerHTML=$this->Cantidad_registros;
			parent.Cantidad_paginas=parseInt($this->Cantidad_registros/$this->Lineas_por_pagina)+1;
			parent.document.getElementById('tCantidad_paginas').innerHTML=parent.Cantidad_paginas;
			fija_campos();

			document.getElementById('mensajes').innerHTML='';
		}

		function fija_campos()
		{
			var Titulos=parent.document.getElementById('Titulos_tabla');
			var Idetalle=parent.document.getElementById('Detalle_tabla$this->id_tabla');
			var Detalle=document.getElementById('Contenido_tabla_$this->id_tabla');
			if(Detalle.clientWidth>Idetalle.clientWidth)
			{
				Idetalle.width=Detalle.clientWidth+30;
			}
			$this->Java_fija;
			if(document.body.scrollTop<Iniscroll) document.body.scrollTop=Iniscroll;
		}

		function grabascroll()
		{
			if(document.body.scrollTop<Iniscroll) document.body.scrollTop=Iniscroll;
			Topscrolled=document.body.scrollTop;
			Leftscrolled=document.body.scrollLeft;
			setCookie('SC_TOP',Topscrolled);
			setCookie('SC_LEFT',Leftscrolled);
		}

		function fija_ancho(Objeto)
		{
			var D=document.getElementById('td_'+Objeto);
			var T=parent.document.getElementById('th_'+Objeto);
			if(Browser=='IE')
				T.width=D.clientWidth;
			else
				if(Browser=='Chrome')
					T.width=D.clientWidth;
				else
					T.width=D.clientWidth-2;
			Iniscroll=D.clientHeight;
		}

		function m_c(Id)     // menu contextual de registro
		{
			oculta_mc();
			MC_Reg='Reg_'+Id;
			var REG=document.getElementById(MC_Reg);
			MC_Reg_color=REG.style.backgroundColor;
			REG.style.backgroundColor='ddddaa';
		    var Menu=document.getElementById('context_registro');
			Menu.style.visibility='visible';
			Menu.style.height=80;
			Menu.style.width=100;
			Menu.style.left=mouseX;
			Menu.style.top=mouseY-10;
			Menu.src='marcoindex.php?Acc=menu_contextual_registro&Id='+Id+'&Id_tabla=$this->id_tabla';
			Menu_contextual=true;
		}

		function oculta_mc()   // oculta el menu contextual de registro
		{
			if(Menu_contextual)
			{
				var REG=document.getElementById(MC_Reg);
				if(REG)
				{
					REG.style.backgroundColor=MC_Reg_color;
				}
				Menu=document.getElementById('context_registro');
				Menu.style.visibility='hidden';
				Menu.style.height=50;
				Menu.style.width=50;
				Menu.src='gifs/standar/loading.gif';
				Menu_contextual=false;
			}
		}

		function verifica_escape(Evento)   // verifica escape para cerrar u ocultar objetos
		{
			var keynum;
			var Caracter;
			if(window.event) // IE
				keynum = Evento.keyCode;
			else if(Evento.which) // Netscape/Firefox/Opera
				keynum = Evento.which;
			if( keynum==27)
			{
				if(Menu_contextual) oculta_mc();
				if(Ventana_edicion) oculta_ve();
			}
		}

		function mod_reg(Id) // modifica el registro
		{
			var Ob=document.getElementById('Edicion_$this->id_tabla');
			Ob.src='gifs/standar/loading.gif';
			Ob.style.visibility='visible';
			Ob.src='marcoindex.php?Acc=mod_reg&Num_Tabla=$this->id_tabla&id='+Id+'&VINCULOC='+parent.Campo_Vinculo+'&VINCULOT='+parent.Contenido_Vinculo;
			Ventana_edicion=true;
		}

		function oculta_ve()  // oculta la ventana de edicion
		{
			var Ob=document.getElementById('Edicion_$this->id_tabla');

			Ventana_edicion=false;
			Ob.style.visibility='hidden';
		}

		function bm_todos(Dato)
		{
			if(Dato==1)
				for(i=0;i<Cajas.length;i++) document.getElementById('Bmm_'+Cajas[i]).checked=true;
			else
				for(i=0;i<Cajas.length;i++) document.getElementById('Bmm_'+Cajas[i]).checked=false;

		}

		function bm_borrar()
		{
			if(Contador_Bm<Cajas.length)
			{
				var IDBM=Cajas[Contador_Bm];
				Contador_Bm++;
				if(document.getElementById('Bmm_'+IDBM).checked)
					window.open('marcoindex.php?Acc=borrado_masivo&Id_tabla=$this->id_tabla&Id='+IDBM,'context_registro');
				else
					bm_borrar();
			}
			else
			{
				parent.repinta_detalle();
			}
		}

       function texto_tabla()
       {
         document.getElementById('TextoTabla').innerHTML='<b>'+parent.TextoTabla+'</b>';
       }

       function valida_scroll()
       {
       	if(document.body.scrollTop<Iniscroll) document.body.scrollTop=Iniscroll;
       }

		</script>
		<body leftmargin='0' rightmargin='0' topmargin='0' bottommargin='0'
			onload='fija();texto_tabla();parent.fija();' bgcolor='#ffffff'
			onkeydown='verifica_escape(event);' onunload='grabascroll();'
			onscroll='valida_scroll();'
			onresize='fija();'>
		<span id='mensajes'style='font-size:16;font-weight:bold;color:ffffcc;background-color:3333aa;position:fixed;width:400px;text-align:center;'>Cargando...</span>
        <table border=0 cellspacing=1 id='Contenido_tabla_$this->id_tabla' style='empty-cells:show;top:-20' bgcolor='#bbddcc' width='100%'>";
			echo $this->Titulos;
			if($Datos=q($this->Query.$this->Condiciones_query.$this->Orden_query.$this->Limit_query))
			{
				$Contador=0;
				while($R=mysql_fetch_object($Datos))
				{
					if($this->Borrado_masivo) echo "<script language='javascript'>Cajas[$Contador]=$R->id;</script>";
					$this->pinta_campos($R);
					$Contador++;
				}

			}
			echo "</table>
      <br><br><span name='TextoTabla' id='TextoTabla'></span><br><br>";
			if($_SESSION['User']==1 || $_SESSION['Nick']=='arturo.quintero')
			{
				$Pre_instruccion=urlencode("update $this->nombre_tabla set CAMPO=VALOR $this->Condiciones_query");
				echo "<br><br><a class='sinfo' onclick=\"modal('marcoindex.php?Acc=sql&Pre_instruccion=$Pre_instruccion',10,10,0,0,'_blank');\">
            <IMG SRC='gifs/standar/dsn_sql.png' HSPACE='5' BORDER=0 ><span style='width:100px'>Correr sql</span></a>";
			}
			#         echo "$this->Query $this->Condiciones_query $this->Orden_query $this->Limit_query<br>";
			if($this->Borrado_masivo)
			echo "<br><br><a style='cursor:pointer;' onclick='bm_todos(1);'>Marcar todos los registros</a>
					<a style='cursor:pointer;' onclick='bm_todos(0);'>Desmarcar todos los registros</a>
					<a style='cursor:pointer;' onclick='bm_borrar();'>Borrar los registros marcados</a>";
			echo "<iframe id='context_registro' name='context_registro' width='50' height='50' frameborder='yes' src='gifs/standar/loading.gif'
				style='visibility:hidden;position:absolute;border-style:solid;border-width:2px;background-color:#fdfdfd;' scrolling='no'></iframe>";
			$this->pinta_marco_edicion();
			echo "<br><br><br><br><br><br><br><br><br><br><br><br>";
	}

	function pinta_marco_edicion()
	{
		echo "<iframe id='Edicion_$this->id_tabla' name='Edicion_$this->id_Tabla' height=100' width='100'
				style='visibility:hidden;position:fixed;top:0;left:0;border-style:solid;border-width:2px;background-color:#fdfdfd;
				z-index:119;' border='1' frameborder='yes' src='gifs/standar/loading.gif'></iframe>
			<script language='javascript'>
			if(Browser=='IE')
			{
				var DD=document.getElementById('Edicion_$this->id_tabla');
				DD.style.position='absolute';
				DD.style.top=50;
				DD.style.width=document.body.clientWidth-5;

				DD=document.getElementById('mensajes');
				DD.style.position='absolute';
			}
			</script>
			";
	}
	function pinta_campos($R)
	{
		if($this->Sombreado) $Fondo='ffffff'; else $Fondo='eeeeff'; $this->Sombreado=!$this->Sombreado;
		echo "
		<tr id='Reg_$R->id' oncontextmenu=\"m_c($R->id);return false;\" ondblclick='if(parent.Modifica==1) mod_reg($R->id);' bgcolor='$Fondo' >";
		for($i=0;$i<count($this->Campos);$i++)
		{
			$this->Campos[$i]->pinta($R,$this->Borrado_masivo);
		}
		echo "</tr>";
	}

}





?>