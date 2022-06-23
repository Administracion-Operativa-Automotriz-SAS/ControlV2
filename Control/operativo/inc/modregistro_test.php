<?php
//-----------------------------------------------------------------------------------------------------   CLASE PARA CADA  CAMPO -----------------------------------------------------------------------------------

ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ERROR | E_PARSE);
	
	
include ("/var/www/html/public_html/Control/operativo/controllers/DbConnect.php");





class campomod
{
	var $campo=''; // nombre fisico del campo
	var $idcampo=0; // id del campo dentro de la tabla de configuracion  _t
	var $fila=0; // fila en la que aparece el campo
	var $columna=0; // columna en la que aparece el campo
	var $tipo=''; // tipo de campo
	var $Config=false; // resto de la configuración del campo
	var $Sololectura=false;  // en caso de que este campo sea protejido por alguna circunstancia
	var $Ocultar=false; // oculta la captura de acuerdo a una condición
	var $Tamano=0; // tamaño de la captura
	var $Condicion_modificacion=''; // condicion para la modificaion, se evalua al momento de mostrar cada campo
	var $Relacionado=false; // si el campo está relacionado con alguna tabla
	var $Relacion_tabla=''; // tabla con la que se está relacionado el campo
	var $Relacion_campo=''; // campo de la tabla con la que está relacionado este campo
	var $Relacion_mostrar=''; // campos que se muestran en la relación
	var $Relacion_mostrar_sql=''; // instrucción en sintaxys mysql para mostrar el menu tipo dropdown con la o las tablas que tenga relación este campo (opcional)
	var $Relacion_brow=''; // instruccion para mostrar en el browtabla
	var $Menu_rapido='';  // metodo de captura de menus rapidos
	var $Estilo=''; // variables de style de  la celda de captura.
	var $Javascript=''; // rutinas de javascript para el campo de captura.
	var $Checkbox=false; // determina si el campo es caja de chequeo
	var $Richtext=false; // determina si el campo es una captura de contenido enriquecido. Richtext.
	var $Color=false; // determina si el campo es una captura de color para activar el dialogo de colores.
	var $Texto=false; // determina si es un área de texto.
	var $Fechahora=false; // determina si es un campo que tiene fecha y hora
	var $Fecha=false; // determina si es un campo que tiene fecha
	var $Hora=false; // determina si es un campo que tiene hora
	var $Imagen=false; // determina si es un campo que contiene una url de una imagen.
	var $Blob=false;  // determina si es un campo tipo blob para manejar imagenes.
	var $Tabla=''; // nombre de la tabla principal de datos
	var $Ncapa='Inicio'; // nombre de la capa que contiene el campo
	var $TamRecImg=300; // Tamaño recomendado para la imagen

	function campomod($C,$Tabla,$NCapa,$aseguradora)
	{
		$this->aseguradora = $aseguradora;
		$this->Tabla=$Tabla;
		$this->Ncapa=$NCapa;
		$this->campo=$C->campo;
		$this->idcampo=$C->id;
		$this->fila=$C->orden;
		$this->columna=$C->suborden;
		$this->tipo=$C->tipo;
		if(r($this->campo,3)=='_rt') $this->Richtext=true;
		elseif(r($this->campo,3)=='_co') $this->Color=true;
		elseif(r($this->campo,2)=='_f') $this->Imagen=true;
		elseif($C->caja==1) $this->Checkbox=true;
		elseif(strpos(strtoupper(" ".$this->tipo), "TEXT")) $this->Texto=true;
		elseif(strpos(strtoupper(" ".$this->tipo), "DATETIME")) $this->Fechahora=true;
		elseif(strpos(strtoupper(" ".$this->tipo), "DATE")) $this->Fecha=true;
		elseif(strpos(strtoupper(" ".$this->tipo), "TIME")) $this->Hora=true;
		elseif(strpos(strtoupper(" ".$this->tipo), "BLOB")) $this->Blob=true;
		$this->Config=$C;		
		$this->behavior_content = false;			
		if($this->Config->custom_behavior != null)
		{	
			//echo "<br>";
			//echo "Behavior switched";
				//echo "<br>";
				
			$DbConnect = new DbConnect();
			
			$siniestroEs = $DbConnect->convert_object($DbConnect->query("Select * from siniestro where id = ".$_GET['id']));		  	
				
			//print_r($siniestroEs);	
			
			$this->aseguradoras_ids = explode('/n',$this->Config->custom_behavior);	
			
			unset($this->aseguradoras_ids[count($this->aseguradoras_ids)-1]);
			
			$this->aseguradoras_content = explode('/n',$this->Config->custom_behavior_content);	
			
			unset($this->aseguradoras_content[count($this->aseguradoras_content)-1]);
			
			$this->aseguradoras_pull = explode('/n',$this->Config->custom_behavior_pull);
			
			//print_r($this->aseguradoras_pull);
			
			//unset($this->aseguradoras_pull[count($this->aseguradoras_pull)-1]);
			
			$this->take_key = null;
			
			//print_r($this->aseguradoras_pull);
			
			foreach($this->aseguradoras_ids as $key => $value)
			{
				if($value == $this->aseguradora)
				{
					$this->take_key = $key;
				}
			}		
			
			if(isset($this->aseguradoras_content[$this->take_key]))
			{				
				$this->behavior_content = true;
				$C->traex = $this->aseguradoras_pull[$this->take_key];				
			}
			
			//echo $C->traex;
			//echo "<br>";
		}
		
		if($C->traet && $C->traen && $C->trael)
		{
			$this->Relacion_tabla=$C->traet;
			$this->Relacion_mostrar=$C->traen;
			$this->Relacion_campo=$C->trael;
			if($C->traex) $this->Relacion_mostrar_sql=$C->traex;			
			if($C->verx) $this->Relacion_brow=$C->verx;
			$this->Relacionado=true;
		}
		if($C->traex && strpos($C->traex, ";")) $this->Menu_rapido=$C->traex;
		$this->Tamano=$this->tamano();
		$this->Estilo='color:'.$this->Config->primer_campo.';background-color:'.$this->Config->fondo_campo.';'.($this->Relacionado?'width:'.$this->Tamano.';':'');
		if(!aparece(strtoupper($this->Config->scambio), 'ONBLUR,ONFOCUS,ONCHANGE,ONCLICK,ONDBLCLICK,ONKEYPRESS,ONKEYDOWN,ONKEYUP,ONMOUSEDOWN,ONMOUSEMOVE,ONMOUSEOVER,ONMOUSEOUT,ONMOUSEUP,ONSELECT,READONLY,DISABLED,STYLE'))
			$this->Javascript = " onchange=\"".$this->Config->scambio."\" ";
		else
			$this->Javascript = $this->Config->scambio;
		if(aparece(strtoupper($this->Config->scambio),'STYLE')) $this->Estilo='';
		$this->permisousuario();
	}

	function aparece(&$R /*recibe el contenido del registro*/,&$LINK)
	{
		
		global $VINCULOT,$VINCULOC; // verifica si vienen vinculos que correspondan a este campo, si es asi lo reasigna inmediatamente y lo proteje
		if($VINCULOT && $VINCULOC && $VINCULOC==$this->campo) { eval("\$R->$this->campo=\$VINCULOT;"); $this->Sololectura=true;}
		if(!$this->Sololectura) $this->permisomodificacion($R /* envia el contenido del registro */); // averigua si no hay restricciones por usuario
		$this->ocultar_campo($R);
		if(!$this->Ocultar)
		{
			if ($this->Config->nueva_tabla) $this->pintanuevatabla();
			$this->pinta_descripcion();
			eval("\$Contenido=\$R->$this->campo;"); // saca el contenido del campo
			
			$this->pinta_contenido($Contenido,$LINK,$R);
		}
		else
		{
			if ($this->Config->nueva_tabla) $this->pintanuevatabla();
			$this->pinta_descripcion();
			
			eval("\$Contenido=\$R->$this->campo;"); // saca el contenido del campo
			$this->pinta_oculto($Contenido,$LINK,$R);
		}
	}

	function pinta_oculto(&$Info,&$LINK,&$R)
	{
		echo "<td class='tdedit' ".($this->Config->columnas?" colspan='".$this->Config->columnas."'":"").
			($this->Config->rowspan2?" rowspan='".$this->Config->rowspan2."'":"");
		if($this->Config->td_propiedades) eval('echo '.$this->Config->td_propiedades.';');
		echo " bgcolor='".$this->Config->fondo_celda."' >";
		echo "<input type='hidden' name='$this->campo' id='$this->campo' value=\"".$Info."\" ></td>";
	}

	function pinta_contenido(&$Info,&$LINK,&$R /*informacion del registro*/)
	{	
		
		echo "<td class='tdedit' ".($this->Config->columnas?" colspan='".$this->Config->columnas."'":"").
			($this->Config->rowspan2?" rowspan='".$this->Config->rowspan2."'":"");
		if($this->Config->td_propiedades) eval('echo '.$this->Config->td_propiedades.';');
		echo " bgcolor='".$this->Config->fondo_celda."'".
		($this->Config->explicacion?" alt='".$this->Config->explicacion."' title='".$this->Config->explicacion."' ":"")	." >";
		if($this->Checkbox) 			$this->pinta_checkbox($Info,$LINK,$R);
		elseif($this->Relacionado) 		$this->pinta_relacionado($Info,$LINK,$R);
		elseif($this->Menu_rapido) 		$this->pinta_menurapido($Info,$LINK,$R);
		elseif($this->Config->password) $this->pinta_password($Info,$LINK,$R);
		elseif($this->Richtext) 		$this->pinta_textoenriquecido($Info,$LINK,$R);
		elseif($this->Color) 			$this->pinta_colores($Info,$LINK,$R);
		elseif($this->Texto) 			$this->pinta_areatexto($Info,$LINK,$R);
		elseif($this->Fechahora) 		$this->pinta_fechahora($Info,$LINK,$R);
		elseif($this->Fecha) 			$this->pinta_fecha($Info,$LINK,$R);
		elseif($this->Hora) 			$this->pinta_hora($Info,$LINK,$R);
		elseif($this->Imagen) 			$this->pinta_imagen($Info,$LINK,$R);
		elseif($this->Blob)				$this->pinta_blob($Info,$LINK,$R);
		else $this->pinta_caja($Info,$LINK,$R);
		if($this->Config->obliga) echo "&nbsp;<img src='gifs/obligatorio.png' border=0 alt='Campo obligatorio' title='Campo Obligatorio'>";
		echo "</td>";
	}
	
	function get_fact_last_cons()
	{
		$resolucion_factura = qo("Select * from resolucion_factura order by fecha desc limit 1");
				
		$factura = qo("Select * from factura where consecutivo like  '%".$resolucion_factura->prefijo."%' order by id desc LIMIT 1 ");
		if($factura)
		{
			 $g_cons = str_ireplace($resolucion_factura->prefijo,"",$factura->consecutivo);
			 $g_cons += 1;		 
			 
			 
			 $g_cons =  $resolucion_factura->prefijo."".$g_cons; 
			 
			 
		}
		else
		{
			$g_cons = $resolucion_factura->prefijo."".$resolucion_factura->consecutivo_inicial;
		}
		
		return $g_cons;
		
	}

	function pinta_caja(&$Info,&$LINK,$R)
	{
		if($this->Tabla == 'factura' && $this->campo == 'consecutivo' && $Info == 1)
		{			
			$last_cons = $this->get_fact_last_cons();
			$Info = $last_cons;
		}
		echo "<input Type='text' style='$this->Estilo' name='$this->campo' id='$this->campo' size='$this->Tamano' VALUE=\"".$Info."\" ".
			($this->Sololectura?"readonly":$this->Javascript)." ".($this->Config->obligan?"onkeyup=\"verificanumero(event,'$this->campo');\" class='numero' ":"").">";
	}

	function pinta_imagen(&$Info,&$LINK,&$R)
	{		
		global $Num_Tabla;
		echo "<table border=0 cellspacing=0 cellpadding=0><tr><td bgcolor='eeeeee'>";
		$Ancho=$this->Config->cols_text;
		$Alto=$this->Config->rows_text;

		$Sub_Contenido=substr($Info,strrpos($Info,'/')+1);$Sub_Tumb='tumb_'.$Sub_Contenido;$Tumb=str_replace($Sub_Contenido,$Sub_Tumb,$Info);
		if(!file_exists($Tumb) && file_exists($Info))
		{
			if(strpos(strtolower($Sub_Contenido),'.jpg')) picresize($Info,TUMB_SIZE,'jpg',$Tumb);
			if(strpos(strtolower($Sub_Contenido),'.gif')) picresize($Info,TUMB_SIZE,'gif',$Tumb);
			if(strpos(strtolower($Sub_Contenido),'.png')) picresize($Info,TUMB_SIZE,'png',$Tumb);
		}
		
		if(strpos(strtolower($Sub_Contenido),'.pdf')) $Tumb=$Info; 
		echo "<iframe id='simg_$this->campo' name='simg_$this->campo' src='$Tumb' height='$Alto' width='$Ancho' frameborder='0' ></iframe>";
		echo "</td><td valign='top' bgcolor='efefef'>";
		if(!$this->Sololectura && $R->id)
		{
			echo "<a style='cursor:pointer;' class='info' onclick=\"cambio_imagen_std('$this->campo','".$this->Config->rutaimg."',".$this->Config->tamrecimg.");\">
	 				<img src='gifs/standar/Pencil.png' border='0'>
					<span style='width:100px'>Cambiar la imagen</span></a><br>
					<a style='cursor:pointer;' class='info' onclick=\"cambio_imagen_webcam('$this->campo','".$this->Config->rutaimg."',".$this->Config->tamrecimg.",$Alto,$Ancho);\"><img src='gifs/webcam.png' height='24px' border='0'>
					<span style='width:100px'>Cambiar la imagen usando webcam</span></a><br><br>";

			if($Info)
			echo "
				<a  id='img_del_$this->campo' style='cursor:pointer;' class='info' onclick=\"borra_imagen_std('$this->campo');\">
				<img src='gifs/standar/borra_registro.png' border='0'>
				<span style='width:100px'>Borrar la imagen</span></a><br><br>";
		}
		if($Info)
		echo "<a id='img_ver_$this->campo' style='cursor:pointer;' class='info' onclick=\"modal('$Info',0,0,s_alto(),s_ancho());\">
				<img src='gifs/standar/zoom_blob.png' border='0'>
				<span style='width:100px'>Ver la imagen ampliada</span></a><br><br>";
		echo "</td></tr></table>";
		if($_SESSION['User']==1)
			echo "<input type='text' name='$this->campo' id='$this->campo' value='$Info' size='20'>";
		else
			echo "<input type='hidden' name='$this->campo' id='$this->campo' value='$Info'>";
	}

	function pinta_blob(&$Info,&$LINK,&$R)
	{
		
		global $Num_Tabla;
		echo "<table border=0 cellspacing=0 cellpadding=0><tr><td bgcolor='eeeeee'>";
		
		$Ancho=$this->Config->cols_text;
		$Alto=$this->Config->rows_text;
		if($Info)
		{
			echo "<iframe id='simg_$this->campo' name='simg_$this->campo' src='inc/imgblob.php?T=$this->Tabla&C=$this->campo&Id=$R->id' height='$Alto' width='$Ancho' frameborder='0' ></iframe>";
		}
		else
		{
			
			echo "<iframe id='simg_$this->campo' name='simg_$this->campo' src='gifs/standar/img_neutra.png' height='$Alto' width='$Ancho' frameborder='0' ></iframe>";
		}
		echo "</td><td valign='top' bgcolor='efefef'>";
		if(!$this->Sololectura && $R->id)
		{	echo "<a style='cursor:pointer;' class='info' onclick=\"cambio_imagen_blob('$this->campo');\">
					<img src='gifs/standar/Pencil.png' border='0'>
					<span style='width:100px'>Cambiar la imagen</span></a><br><br>";
		}
		echo "<a id='img_ver_$this->campo' style='cursor:pointer;visibility:".($Info?"visible":"hidden")."' class='info' onclick=\"modal('inc/imgblob.php?T=$this->Tabla&C=$this->campo&Id=$R->id',0,0,s_alto(),s_ancho());\">
				<img src='gifs/standar/zoom_blob.png' border='0'>
				<span style='width:100px'>Ver la imagen ampliada</span></a><br><br>
				<a  id='img_del_$this->campo' style='cursor:pointer;visibility:".($Info?"visible":"hidden")."' class='info' onclick=\"borra_imagen_blob('$this->campo');\">
				<img src='gifs/standar/borra_registro.png' border='0'>
				<span style='width:100px'>Borrar la imagen</span></a><br><br>";
		echo "</td></tr></table>";
	}

	function pinta_hora(&$Info,&$LINK,&$R)
	{
		
		if($this->Sololectura)
			echo "<input type='text' $this->Estilo name='$this->campo' id='$this->campo' size='$this->Tamano' value='".$Info."' readonly>";
		else
			{
				$Onchange="document.mod.".$this->campo.".value=document.mod.".$this->campo."_hora.value+':'+document.mod.".$this->campo."_minutos.value+':'+document.mod.".$this->campo."_segundos.value;";
				echo "<input type='hidden' id='$this->campo' name='$this->campo' value='".$Info."'>";
				if($Info=='') $Horas=$Minutos=$Segundos='';
				else {$Horas=Date('H',strtotime($Info));$Minutos=Date('i',strtotime($Info));$Segundos=Date('s',strtotime($Info));}
				echo "<select $this->Estilo name='".$this->campo."_hora' id='".$this->campo."_hora' onchange=\"$Onchange\" >";
				for($i=0;$i<=23;$i++)
				{
					$Value=str_pad($i,2,'0',STR_PAD_LEFT);
					echo "<option value='$Value' ".($Value==$Horas?"selected ":"").">$Value</option>";
				}
				echo "</select>";
				echo "<select $this->Estilo name='".$this->campo."_minutos' id='".$this->campo."_minutos' onchange=\"$Onchange\" >";
				for($i=0;$i<=59;$i++)
				{
					$Value=str_pad($i,2,'0',STR_PAD_LEFT);
					echo "<option value='$Value' ".($Value==$Minutos?"selected ":"").">$Value</option>";
				}
				echo "</select>";
				echo "<select $this->Estilo name='".$this->campo."_segundos' id='".$this->campo."_segundos' onchange=\"$Onchange\" >";
				for($i=0;$i<=59;$i++)
				{
					$Value=str_pad($i,2,'0',STR_PAD_LEFT);
					echo "<option value='$Value' ".($Value==$Segundos?"selected ":"").">$Value</option>";
				}
				echo "</select>";
			}
	}

	function pinta_fecha(&$Info,&$LINK,&$R)
	{
		if($this->Sololectura)
			echo "<input type='text' $this->Estilo name='$this->campo' id='$this->campo' size='$this->Tamano' value='".$Info."' readonly>";
		else
			echo pinta_FC('mod',$this->campo,$Info,'f',$this->Estilo,$this->Javascript);
	}

	function pinta_fechahora(&$Info,&$LINK,&$R)
	{
		if($this->Sololectura)
			echo "<input type='text' $this->Estilo name='$this->campo' id='$this->campo' size='$this->Tamano' value='".$Info."' readonly>";
		else
			echo pinta_FC('mod',$this->campo,$Info,'t',$this->Estilo,$this->Javascript);
	}

	function pinta_areatexto(&$Info,&$LINK,&$R)
	{
		echo "<textarea rows='".$this->Config->rows_text."' cols='".$this->Config->cols_text."' id='$this->campo' name='$this->campo' $this->Estilo ".
				($this->Sololectura?"readonly":" ondblclick=\"ventana_texto('$this->campo','".addcslashes($this->Config->descripcion,"\00..\24")."');\"
				$this->Javascript").">".$Info."</textarea>";
	}

	function pinta_colores(&$Info,&$LINK,&$R)
	{
		echo "<input type='text' $this->Estilo name='$this->campo' id='$this->campo' value='".$Info."' size='$this->Tamano' ".
				($this->Sololectura?'readonly':" $this->Javascript ondblclick=\"pickcolor('mod','$this->campo',this.value);\" ").">";
	}

	function pinta_textoenriquecido(&$Info,&$LINK,&$R)
	{
		if($this->Sololectura) echo "$Contenido<input type='hidden' name='$this->campo' id='$this->campo' value='$Info'>";
		else
		{
			$campo_richedit[$this->fila] = new spaweditor($this->campo/*nombre del campo*/ , stripslashes($Info)/*valor del campo*/ , null/*tool barr*/ , null, null, $this->Config->cols_text, $this->Config->rows_text);
			$campo_richedit[$this->fila]->show();
		}
	}

	function pinta_checkbox(&$Info,&$LINK,&$R)
	{
		if($this->Sololectura)
			echo "<input type='checkbox' name='_$this->campo' id='_$this->campo' ".($Info?'checked':'')." disabled>
						<input type='hidden' name='$this->campo' id='$this->campo' value='".($Info?'on':'')."'>";
		else
			echo "<input type='checkbox' name='$this->campo' id='$this->campo' ".($Info?'checked':'')." ".$this->Javascript.">";
	}

	function pinta_relacionado(&$Info,&$LINK,&$R)
	{
		global $VINCULOT;
		
		$jquery_script = false;
		
		if($this->Sololectura)
		{
			if($this->Config->browdirecto) echo $Contenido;
			elseif($this->Relacion_brow) eval('echo qo1m("'.$this->Relacion_brow.'",$LINK);');
			else echo qo1m("select $this->Relacion_mostrar from $this->Relacion_tabla where $this->Relacion_campo='$Info'",$LINK);
			echo "<input type='hidden' name='$this->campo' id='$this->campo' value='".$Info."'>";
		}
		else
		{
			if($this->Config->buscapopup)
			{
				echo "<input type='text' style='$this->Estilo' name='_$this->campo' id='_$this->campo' value=\"";
				if($this->Relacion_brow) eval("echo qo1m(\"".$this->Relacion_brow."\",\$LINK);");
				else echo qo1m("select $this->Relacion_mostrar from $this->Relacion_tabla where $this->Relacion_campo='$Info'",$LINK);
				echo "\" size='$this->Tamano' onclick=\"busqueda_popup($this->idcampo,'$Info','$this->campo');\" readonly>
					<input type='hidden' name=".$this->campo." id=".$this->campo." value='".$Info."'>";
			}
			elseif($this->Config->busca_ciudad)
			{
				echo "<input type='text' style='$this->Estilo' name='_$this->campo' id='_$this->campo' value=\"";
				if($this->Relacion_brow) eval("echo qo1m(\"".$this->Relacion_brow."\",\$LINK);");
				else echo qo1m("select $this->Relacion_mostrar from $this->Relacion_tabla where $this->Relacion_campo='$Info'",$LINK);
				echo "\" size='$this->Tamano' onclick=\"busqueda_ciudad2('$this->campo','$Info');\" readonly>
					<input type='hidden' name=".$this->campo." id=".$this->campo." value='".$Info."'> <span id='bc_$this->campo'></span>";
			}
			else
			{
				if($this->Relacion_mostrar_sql)
				{
					
					
					/* Reescribir sql es necesario por que hay interfaces que se ponen muy lentas cuando son demasiados resultados en los select*/
					
					
					if($this->Tabla == 'factura' && $this->campo == 'cliente')
					{						
						$this->Relacion_mostrar_sql = "select concat(apellido,' ',nombre) as ncliente,id from cliente order by ncliente limit 0";
						
						if(!$jquery_script)
						{
							echo '<script
							  src="https://code.jquery.com/jquery-3.3.1.min.js"
							  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
							  crossorigin="anonymous"></script>';
							  
							  $jquery_script = true;
						}
						
						
						echo "<input type='text' autocomplete='off' name='search_customer_value' id='search_customer_value'> <button onclick='search_customer()'>Buscar</button>";
						
						echo "<script>
							
							function search_customer()
							{								
								event.preventDefault();
								
								if($('#search_customer_value').val().length < 3)
								{
									return alert('Debe mandar almenos 3 caracteres en la consulta ');
								}
								
								$.post('/Control/operativo/controllers/WebServices.php',{acc:'get_customer_by_name',name:$('#search_customer_value').val()},function(response){
									console.log(response);
									response = JSON.parse(response);
									let customers = response.customers;
									
									$('select[name=\"cliente\"]').empty();
									
									$('select[name=\"cliente\"]').append('<option>Selecciona</option>');	
									
									customers.forEach(function(customer){
										$('select[name=\"cliente\"]').append('<option value=\"'+customer.id+'\" >'+customer.nombre+' '+customer.apellido+'</option>');	
										console.log(customer);
									});
								});
							}
						
						</script>";						
						
					}					
					if($this->Tabla == 'factura' && $this->campo == 'cliente' && is_numeric($Info))
					{
						$this->Relacion_mostrar_sql = "select concat(apellido,' ',nombre) as ncliente,id from cliente where id = '".$Info."' order by ncliente";						
					}
					
					if(strpos($this->Relacion_mostrar_sql,'$'))
					{						
						$w_contador=1;
						while (strpos($this->Relacion_mostrar_sql,'$'))
						{
							$Tcampo=substr($this->Relacion_mostrar_sql,strpos($this->Relacion_mostrar_sql,'$'));
							$Tcampo=l($Tcampo,strpos($Tcampo,' '));
							eval("\$Contenidoc=$Tcampo;");
							$this->Relacion_mostrar_sql=str_replace(' '.$Tcampo.' ',$Contenidoc,$this->Relacion_mostrar_sql);
							$w_contador++;
							if($w_contador>200) die("error en la construccion del select ".$this->Relacion_mostrar_sql.". Recuerde dejar por lo menos un espacio en blanco despues de una variable.");
						}
					}
					echo menu2($this->campo,$this->Relacion_mostrar_sql,$Info,($this->Config->blanco0?0:1),$this->Estilo,$this->Javascript,$LINK);
				
				
				}
				else
					echo menu1($this->campo,"select $this->Relacion_campo,$this->Relacion_mostrar from $this->Relacion_tabla ".
					($this->Config->traec?" where ".$this->Config->traec:"")." order by $this->Relacion_campo",$Info,($this->Config->blanco0?0:1),
						$this->Estilo,$this->Javascript,$LINK);
			}
		}
	}

	function pinta_menurapido(&$Info,&$LINK,&$R)
	{
		echo menu3($this->campo,$this->Menu_rapido,$Info,($this->Config->blanco0?0:1),$this->Estilo,($this->Sololectura?' disabled ':$this->Javascript));
		if($this->Sololectura) echo "<input type='hidden' name='$this->campo' id='$this->campo' value='".$Info."'>";
	}

	function pinta_password(&$Info,&$LINK,&$R)
	{
		echo "<INPUT TYPE='password' $this->Estilo NAME='$this->campo' id='$this->campo' VALUE='".$Info." size='$this->Tamano' ".
				($this->Sololectura?'readonly':'')." $this->Javascript>";
	}

	function pinta_descripcion()
	{
		//echo ($_SESSION['Disenador']?"si":"no");
		
		echo "<td class='tdedit' align='right' bgcolor='".$this->Config->fondo_desc."' ".
			($_SESSION['Disenador']?" ondblclick=\"m_c('$this->idcampo','$this->campo',$this->fila,$this->columna,".$this->Config->coldes.",".$this->Config->columnas.",".$this->Config->rowspan1.",".$this->Config->rowspan2.",'".$this->Config->fondo_desc."','".$this->Config->fondo_celda."',$this->Tamano,'$this->Ncapa','".$this->Config->rows_text."','".$this->Config->cols_text."','".$this->Config->usuario."');return false;\" ":"").
			($this->Config->coldes?" colspan='".$this->Config->coldes."'":"").
			($this->Config->rowspan1?" rowspan='".$this->Config->rowspan1."'":"").">";
		if($this->Config->htmladd && !$this->Config->nueva_tabla) echo $this->Config->htmladd;
		if($_SESSION['Disenador'])
		{
			
			echo "<a class='info'><font style='font-family:Arial;' color='".$this->Config->primer_desc."' >".($this->behavior_content?$this->aseguradoras_content[$this->take_key]:$this->Config->descripcion)."</font><span style='width:200px;'>".
			($this->Config->explicacion?$this->Config->explicacion."<br>":"")."<b>".$this->campo."</b> ".$this->tipo." <br>
			Orden: <b>".$this->fila.",".$this->columna."</b><br />
			Colspan 1: <b>".$this->Config->coldes."</b> Colspan 2: <b>".$this->Config->columnas."</b><br />
			Area Texto: <b>".$this->Tamano.",".$this->Config->cols_text."</b><br>".
			($this->Relacionado?"Menu: [".$this->Relacion_tabla."]-[".$this->Relacion_campo."]-[".$this->Relacion_mostrar."]-[".$this->Relacion_mostrar_sql."]</span></a>":"");	
		}
			
		else echo ($this->Config->explicacion?"<a class='info'><font style='font-family:Arial;' color='".$this->Config->primer_desc."' >".
						$this->Config->descripcion."</font><span>".$this->Config->explicacion."</span><a>":
						"<font style='font-family:Arial;' color='".$this->Config->primer_desc."' >".$this->Config->descripcion."</font>");
		echo "</td>";
	}

	function tamano() // averigua el tamaño del campo para la celda o para ser mostrado
	{
		if($this->Config->sizecap) return $this->Config->sizecap;
		$Tam = substr($this->tipo, strpos($this->tipo, '(') + 1);
		if(strpos($Tam, ',')) $Tam = substr($Tam, 0, strpos($Tam, ',')); else $Tam = substr($Tam, 0, strpos($Tam, ')'));
		return $Tam;
	}

	function permisousuario() // averigua si el campo tiene restricciones por usuario
	{
		if(strlen($this->Config->usuario))
		{
			if (!strpos(",,".$this->Config->usuario.",", ",".$_SESSION['User'].",")) $this->Sololectura=true;
		}
	}

	function permisomodificacion(&$R /* recibe el contenido del registro */)
	{
		if($this->Config->cond_modi) eval("\$this->Sololectura=!(".$this->Config->cond_modi.");");
	}

	function ocultar_campo(&$R)
	{
		if($this->Config->nocapturar) eval("\$this->Ocultar=(".$this->Config->nocapturar.");");
	}

	function pintanuevatabla()
	{
		echo "</tr></table>".($this->Config->htmladd?$this->Config->htmladd:"").
					"<TABLE class='tableedit' align='center' ".($this->Config->ancho_tabla?" WIDTH='".$this->Config->ancho_tabla."' ":"").">";
	}
}
//-----------------------------------------------------------------------------------------------------   CLASE PARA CADA  CAPA -----------------------------------------------------------------------------------
class capamod
{
	var $Nombre='Inicio';  // nombre de la capa
	var $Id='';
	var $Campo=array(); // coleccion de campos de la capa
	var $aseguradora;
	
	
	function capamod($N /*nombre de la capa */,$Tabla_Control,$Tabla,$Add /*adicionando*/,$LINK)
	{
		//echo "<br>";
		$sql = "Select * from siniestro where id = ".$_GET['id']." LIMIT 1";
		$query = mysql_query($sql,$LINK);
		$siniestro=mysql_fetch_object($query);
		$this->aseguradora = $siniestro->aseguradora;
		
		$this->Nombre=($N?$N:'Inicio');
		$sqlLlave=mysql_query("select count(*) from $Tabla_Control where llave=1",$LINK);
		$rLlave=mysql_fetch_row($sqlLlave);
		if($rLlave[0]) $Llave=true; else $Llave=false;
		
		$sql = "select * from $Tabla_Control where capa='$N' and modificar=1 ".(($Add && $Llave)?"and llave=1":"")." order by orden,suborden";
		
		//echo "<br>";
		//echo $sql;		
		//echo $sql;		
		
		$Campos=mysql_query($sql,$LINK);
		while($C=mysql_fetch_object($Campos))
		{
			$this->Campo[]= new campomod($C,$Tabla,$N,$this->aseguradora);
		}	
		
		$this->Id=uniqid('Capa_');
	}

	function aparece(&$R /* recibe el contenido del registro */,&$LINK)
	{
		capa($this->Id, 1, 'absolute', '');
		echo "<table class='tableedit' align='center'><tr>";
		$NL=$this->Campo[0]->fila;
		for($i=0;$i<count($this->Campo);$i++)
		{
			if($this->Campo[$i]->fila!=$NL)
			{
				$NL=$this->Campo[$i]->fila;
				echo "</tr><tr>";
			}
			$this->Campo[$i]->aparece($R /* envia el contenido del registro */,$LINK);
		}
		echo "</tr></table><br><br><br><br><br><br>";
		fincapa();
	}
}
//-----------------------------------------------------------------------------------------------------   CLASE PARA EL REGISTRO  -----------------------------------------------------------------------------------
class modregistro
{
	var $Idtabla=0; // id de la tabla en la tabla usuario_tab contiene las caracteristicas de la tabla
	var $Nombretabla=''; // nombre de la tabla
	var $idregistro=0; // id del registro
	var $Ntut=0;	// numero de tabla de usuario_tab
	var $Capas=array(); // capas de edición
	var $Capa_inicial=false; // capa inicial
	var $Campo_inicial=false; // campo inicial
	var $Adicionando=false; // esta variable se activa solo en la adición para capturar llaves
	var $Tiene_llave=false; // identifica si tiene campos llave
	var $Campos_check='';  // Campos que son checkbox
	var $Campos_Upd='';  // Listado de Campos que seran actualizados
	var $Config;	// objeto que contiene el resto de configuracion de la tabla
	var $Obligatorios=''; // campos obligatorios en la captura
	var $Aviso_Carga=''; // Aviso en javascript tan pronto carga la ventana  se activa en body onload=aviso_carga()

	function modregistro($nt,$Idregistro=0) // funcion constructora
	{
		$this->Idtabla=$nt;
		$query = "select * from usuario_tab where id=$this->Idtabla and usuario=".$_SESSION['User'];
		//echo $query;
		
		$this->Config=qo($query);
		$this->Nombretabla=$this->Config->tabla;
		$query = "select id from usuario_tab where tabla='usuario_tab' and usuario=".$_SESSION['User'];
		//echo $query;
		$this->Ntut=qo1($query);
		$this->idregistro=$Idregistro;
		if(!$Idregistro) $this->Adicionando=true;
		$this->capas();
		$RT=false;
		for($i=0;$i<count($this->Capas);$i++)
		{
			for($j=0;$j<count($this->Capas[$i]->Campo);$j++)
			{
				if($this->Capas[$i]->Campo[$j]->campo)
				{
					if(!$this->Campo_inicial) $this->Campo_inicial=$this->Capas[$i]->Campo[$j]->campo;
					if($this->Capas[$i]->Campo[$j]->Richtext) $RT=true;
					if($this->Capas[$i]->Campo[$j]->Checkbox) $this->Campos_check.=($this->Campos_check?',':'').$this->Capas[$i]->Campo[$j]->campo;
					if(!$this->Capas[$i]->Campo[$j]->Blob)
						$this->Campos_Upd.=($this->Campos_Upd?',':'').$this->Capas[$i]->Campo[$j]->campo;
					if($this->Capas[$i]->Campo[$j]->Config->obliga)
					{
						$this->Obligatorios.=($this->Obligatorios?",":"").$this->Capas[$i]->Campo[$j]->campo;
						if($this->Capas[$i]->Campo[$j]->Config->obligan)
						{
							$this->Obligatorios.=":n";
						}
						if(strpos(strtoupper(' '.$this->Capas[$i]->Campo[$j]->tipo),'DATE'))
						{
							$this->Obligatorios.=":f";
						}
					}
				}
			}
		}
		
		if($RT) include_once('html/spaw.inc.php');
		if(!is_file('inc/ciudades.html'))
		{
      chmod('inc',0777);
			$Archivo_ciudades=fopen('inc/ciudades.html','w+');
			fwrite($Archivo_ciudades,"<html><head>
                          <title>Busqueda de Ciudades</title>
                          <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
													<meta http-equiv='Cache-Control' content='no-cache; must-revalidate; proxy-revalidate; max-age=10'>
													<style type='text/css'>@import url(css/estilo.css);</style>
													<style tyle='text/css'>

													li.ciudad {cursor:pointer;color:000000;}
													li.ciudad:hover {color:ff5500;}

													li.depart {cursor:pointer;color:004400;}
													li.depart:hover {color:ff5500;font-weight:bold;}

													ul {list-style-image: url(../gifs/mas_opciones.png);}

													</style>
													<script language='javascript'>

													function abre_cierra(ciudad)
													{
														with(document.getElementById('mbc'+ciudad).style)
														{
															if(visibility=='hidden')
															{
																visibility='visible';
																position='relative';
															}
															else
															{
																visibility='hidden';
																position='absolute';
															}
														}
													}
													function selecciona_ciudad(codigo,nombre)
													{
														eval('var C1=parent.document.'+parent.Ciudad_forma+'.'+parent.Ciudad_campo+';');
														eval('var C2=parent.document.'+parent.Ciudad_forma+'._'+parent.Ciudad_campo+';');
														C1.value=codigo;
														C2.value=nombre;
														parent.oculta_busca_ciudad();
													}
													function verifica_salida(Evento)
													{
														var keynum;
														var Caracter;
														if(window.event) // IE
															keynum = Evento.keyCode;
														else if(Evento.which) // Netscape/Firefox/Opera
															keynum = Evento.which;
														if( keynum==27)
														{
															parent.oculta_busca_ciudad();
														}
													}

													</script>
													</head>
													<BODY onkeyup='verifica_salida(event);' onload='document.body.focus()'>
													<a onclick='parent.oculta_busca_ciudad()' style='cursor:pointer;background-color:000000;color:ffffff;'><img src='../gifs/standar/Cancel.png' border='0'> Cerrar </a><br>");
			fwrite($Archivo_ciudades,pide_ciudad2());
			fwrite($Archivo_ciudades,"</html>");
			fclose($Archivo_ciudades);
			chmod('inc',0755);
		}
	}

	function aparece()
	{
		global $VINCULOT,$VINCULOC;
		html();
		if($this->Adicionando)
		{
			if($this->Config->script_adicion) eval($this->Config->script_adicion);
		}
		else
		{
			$R=qo("select * from $this->Nombretabla where id=$this->idregistro");
			if($this->Config->script_premod) eval($this->Config->script_premod);
		}
		$CC="\$Botones=array();";
		if(count($this->Capas)>0) // Pinta las Capas
		{
			for($i=0;$i<count($this->Capas);$i++)
			{
				$CC.="\$Botones[$i]['id']='".$this->Capas[$i]->Id."';";
				$CC.="\$Botones[$i]['value']='".$this->Capas[$i]->Nombre."';";
			}
		}
		$CC.="\$Capa_inicial='$this->Capa_inicial';";
		$CC.="\$Idtabla=$this->Idtabla;";
		$CC.="\$Nombre_tabla=$this->Nombretabla;";
		$CC.="\$idregistro=$this->idregistro;";
		$CC.="\$Botones_Modificar=".$this->Config->modifica.";";
		$CC=urlencode(base64_encode($CC));

		$this->pintajs($CC);
		echo "<body leftmargin=0 topmargin=30 rightmargin=0 bottommargin=0
			onkeyup='verifica_salida(event);' onload='muestra_primera_capa();recargar();Aviso_Carga();'>
			<span id='mensajes'style='font-size:16;font-weight:bold;color:ffffcc;background-color:3333aa;position:fixed;width:400px;text-align:center;z-index:200;'>Cargando...</span>
			<form action='marcoindex.php' name='mod' id='mod' target='ModOculto_$this->Idtabla' method='post'>";
		if($R) echo "<input type='hidden' name='id' id='id' value='$R->id'>"; else echo "<br><br><input type='hidden' name='id' value='0'>";
		echo "<input type='hidden' name='Acc' id='Acc' value=''>";
		require('inc/link.php');
		for($i=0;$i<count($this->Capas);$i++)
		{
			$this->Capas[$i]->aparece($R /* envia el contenido del registro */,$LINK);
		}
		mysql_close($LINK);
		echo "</center>
			<input type='hidden' name='CAMPOSCHECK' id='CAMPOSCHECK' value='$this->Campos_check' >
			<input type='hidden' name='Campos_Upd' id='Campos_Upd' value='$this->Campos_Upd' >
			<input type='hidden' name='Num_Tabla' id='Num_Tabla' value='$this->Idtabla' >
			</form>
			</script>
			<iframe style='position:fixed;top:0;left:0;z-index:101;border-width:0px;opacity:0.75;' id='Cabezamod' name='Cabezamod'
				width='100%' height='24' border=0 frameborder='no' scrolling='no' src=''></iframe>
			<iframe id='context_campo' name='context_campo' width='50' height='50' frameborder='yes' src='gifs/standar/loading.gif'
   				style='visibility:hidden;position:absolute;border-style:solid;border-width:2px;background-color:#fdfdfd;z-index:200;'></iframe>
   			<span id='Menu_context_campo' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#fdfdfd;z-index:100;'></span>
   			<iframe id='Busqueda_Ciudad' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#eeffee;z-index:200;' height='400px' width='250px' ></iframe>
   			<iframe id='ModOculto_$this->Idtabla' name='ModOculto_$this->Idtabla' style='visibility:hidden' height=10 width=10></iframe>
   			<script language='javascript'>
   			if(Browser=='IE')
			{
				var DD=document.getElementById('Cabezamod');
				DD.style.position='absolute';
				DD.style.top=0;
				document.body.topmargin=10;
				var DD=document.getElementById('mensajes');
				DD.style.position='absolute';
				DD.style.visibility='hidden';
			}
   			</script>
   			</body>";
	}

	function pintajs($CC)
	{
		echo "<script language='javascript'>
			var Menu_contextual=false;
			var Descripcion='".$this->Config->descripcion."';
			var Registro=$this->idregistro;
			var Idtabla=$this->Idtabla;
			var Ventana_edicion=parent.document.getElementById('Edicion_$this->Idtabla');
			var Ciudad_campo='';
			var Ciudad_forma='';

			function Aviso_Carga()
			{
				".($this->Aviso_Carga?$this->Aviso_Carga:'return;')."
			}

			function verifica_salida(Evento)
			{
				var keynum;
				var Caracter;
				if(window.event) // IE
					keynum = Evento.keyCode;
				else if(Evento.which) // Netscape/Firefox/Opera
					keynum = Evento.which;
				if( keynum==27)
				{
					cerrar_edicion();
				}
				if( keynum==113) // F2 PARA GRABAR
				{
					actualizar_registro();
				}
			}

			function cerrar_edicion(Refrescar)
			{
				if(parent.document.getElementById('Edicion_$this->Idtabla'))
				{
					var Ventana=parent.document.getElementById('Edicion_$this->Idtabla');
				}
				else
				{
					if(document.getElementById('Edicion_$this->Idtabla'))
					{
						var Ventana=document.getElementById('Edicion_$this->Idtabla');
					}
					else
					{
						window.close();
						void(null);
						if(opener) opener.location.reload();
						else 
							if(parent.repinta_detalle) parent.repinta_detalle();
					}
				}
				if(Ventana)
				{
				  	Ventana.src='gifs/standar/loading.gif';
					Ventana.style.visibility='hidden';
				}
				if(Refrescar)  // si se solicita refrescar el detalle de la tabla
				{
					parent.parent.repinta_detalle();
				}
			}

			function recargar()
			{
				document.getElementById('Cabezamod').src='marcoindex.php?Acc=mod_reg2cabecera&i=$CC';
				document.getElementById('mensajes').innerHTML='';
				if(Browser=='IE') document.getElementById('mensajes').style.visibility='hidden';
			}

			function m_c(idCampo,Campo,f,c,clsp1,clsp2,rwsp1,rwsp2,fdod,fdoc,Tam,Capa,ar,ac,usu)
			{
				Menu=document.getElementById('Menu_context_campo');
				Menu.style.visibility='visible';
				Menu.style.left=mouseX;
				Menu.style.top=mouseY-10;
				var Siguiente=f+1;
				var Contenido=\"<form action='marcoindex.php' name='Fcc' target='ModOculto_$this->Idtabla' method='post'><input type='hidden' name='Acc' value='accion_menu_contextual_campo'>\";
				Contenido+=\"<input type='hidden' name='Tabla' value='$this->Nombretabla'><input type='hidden' name='Campo' value='\"+Campo+\"'>\";
				Contenido+=\"<table border=0 cellspacing='2' cellpadding='0' bgcolor='#eeddff' name='Context_Opciones' id='Context_Opciones'>\";
				Contenido+=\"<tr><td bgcolor='eeddff' style='cursor:pointer' onclick='oculta_mc();' align='center' colspan='4'><b>Cerrar menu</b></td></tr>\";
				Contenido+=\"<tr><td align='center' colspan='4'><b>$this->Nombretabla.\"+Campo+\"</td></tr>\";
				Contenido+=\"<tr bgcolor='ffffff'><td>Posición:</td><td><input type='text' class='numero' name='fil' id='fil' value='\"+f+\"' size=3><input type='text' class='numero' name='col' id='col' value='\"+c+\"' size=3></td>\";
				Contenido+=\"<td>Colspan:</td><td><input type='text' class='numero' name='csp1' id='csp1' value='\"+clsp1+\"' size=3><input type='text' class='numero' name='csp2' id='csp2' value='\"+clsp2+\"' size=3></td></tr>\";
				Contenido+=\"<tr bgcolor='ffffff'><td>Rowspan:</td><td><input type='text' class='numero' name='rsp1' id='rsp1' value='\"+rwsp1+\"' size=3><input type='text' class='numero' name='rsp2' id='rsp2' value='\"+rwsp2+\"' size=3></td>\";
				Contenido+=\"<td>Color:</td><td><input type='text' name='fondo1' id='fondo1' value='\"+fdod+\"' size=7><input type='text' name='fondo2' id='fondo2' value='\"+fdoc+\"' size=7></td></tr>\";
				Contenido+=\"<tr bgcolor='ffffff'><td>Tamaño:</td><td><input type='text' class='numero' name='sizecap' id='sizecap' value='\"+Tam+\"' size=3></td>\";
				Contenido+=\"<td>Area:</td><td><input type='text' class='numero' name='Arows' id='Arows' value='\"+ar+\"' size=3><input type='text' class='numero' name='Acols' id='Acols' value='\"+ac+\"' size=3></td></tr>\";
				Contenido+=\"<tr bgcolor='ffffff'><td colspan='1'>Usuarios:</td><td colspan='3'><input type='text' name='Usu' id='Usu' size='30' value='\"+usu+\"'></td></tr>\";
				Contenido+=\"<tr brcolor='ddddff'><td colspan='4' align='center'><input type='submit' value='Grabar' style='width:200px'></td></tr>\";
				Contenido+=\"<tr><td colspan='2' style='cursor:pointer;' onclick=\\\"definicion_campo(\"+idCampo+\");\\\"><img src='gifs/standar/Pencil.png' border='0'> Avanzado</td>\";
				Contenido+=\"<td colspan='2' nowrap='yes' style='cursor:pointer;' onclick=\\\"modal2('marcoindex.php?Acc=control_vertabla&nt=$this->Nombretabla&SOLO_ADICION=1&AdCapa=\"+Capa+\"&Mayor=\"+Siguiente+\"',10,10,500,900,'Adicionar_campo');\\\"><IMG SRC='gifs/standar/nuevo_campo.png' border=0>Adicionar un campo</td></tr>\";
				Contenido+=\"<tr><td colspan=4><a href='javascript:definicion_campo_s(\"+idCampo+\");'>Seguridad</a>\";
				Contenido+=\"</td></tr>\";
				Contenido+=\"</table></form>\";
				Menu.innerHTML=Contenido;
				Menu_contextual=true;
			}

			function oculta_mc()
			{
				Menu=document.getElementById('Menu_context_campo');
				Menu.style.visibility='hidden';
			}

			function oculta_capas()
			{";
		for($i=0;$i<count($this->Capas);$i++) echo "oculta('".$this->Capas[$i]->Id."');";
		echo "}

			function muestra_primera_capa()
			{
				muestra('$this->Capa_inicial');
				//document.mod.$this->Campo_inicial.focus();
			}

			function definicion_campo(idCampo)
			{
				modal('marcoindex.php?Acc=definicion_campo&Nombre_tabla=".$this->Nombretabla."&idcampo='+idCampo,10,10,600,850,'Definicion_campo');
			}

			function definicion_campo_s(idCampo)
			{
				modal('marcoindex.php?Acc=definicion_campo_seguridad&Nombre_tabla=".$this->Nombretabla."&idcampo='+idCampo,10,10,600,850,'Definicion_campo');
			}

			function bit_cam(accion)
			{ window.open('bit_cam.php?Nombre_tabla=".$this->Nombretabla."&id=$id&accion='+accion,'_blank','width=100,height=100'); }

			function busqueda_popup(Idcampo,Contenido,Campo)
			{
				modal('marcoindex.php?Acc=buscapopup&NT=".$this->Nombretabla."&IDC='+Idcampo+'&V='+Contenido+'&id=".$this->idregistro."&Forma=mod&Campo='+Campo,0,0,250,600,'buscapopup');
			}

			function busqueda_ciudad(Campo,Contenido)
			{
				modal('marcoindex.php?Acc=pide_ciudad&Campo='+Campo+'&Dato='+Contenido+'&Forma=mod',0,0,200,600,'PC');
			}

			function busqueda_ciudad2(Campo,Contenido)
			{
				var Ventana_ciudad=document.getElementById('Busqueda_Ciudad');
				Ventana_ciudad.style.visibility='visible';
				Ventana_ciudad.style.left=mouseX;
				Ventana_ciudad.style.top=mouseY-10;
				Ventana_ciudad.src='inc/ciudades.html';
				Ciudad_campo=Campo;
				Ciudad_forma='mod';
			}

			function oculta_busca_ciudad()
			{
				document.getElementById('Busqueda_Ciudad').style.visibility='hidden';
			}

			function ventana_texto(Campo,Descripcion)
			{
				modal('marcoindex.php?Acc=ventana_text&Campo=mod.'+Campo+'&Comentario='+escape(Descripcion),0,0,10,10);
			}

			".($this->Config->java_head?$this->Config->java_head:'')."

			function validacion_en_linea()
			{".($this->Config->validacion_enlinea?$this->Config->validacion_enlinea:" return valida_campos('mod','$this->Obligatorios');")."}

			function aplicar_registro()
			{
				document.getElementById('mensajes').innerHTML='Grabando Información ...';
				if(Browser=='IE') document.getElementById('mensajes').style.visibility='visible';
				document.mod.Acc.value='aplicar_registro';
				if(validacion_en_linea()) document.mod.submit();
				else document.getElementById('mensajes').innerHTML='';
			}

			function actualizar_registro()
			{
				document.getElementById('mensajes').innerHTML='Grabando Información y cerrando edición ...';
				if(Browser=='IE') document.getElementById('mensajes').style.visibility='visible';
				document.mod.Acc.value='actualizar_registro';
				if(validacion_en_linea()) document.mod.submit();
				else document.getElementById('mensajes').innerHTML='';
			}

			function cambio_imagen_blob(Campo)
			{
				window.open('marcoindex.php?Acc=reg_sube_blob&T=$this->Nombretabla&Id=$this->idregistro&C='+Campo,'simg_'+Campo);
			}

			function borra_imagen_blob(Campo)
			{
				if(confirm('Desea eliminar la imagen?'))
				window.open('marcoindex.php?Acc=reg_borra_blob&T=$this->Nombretabla&Id=$this->idregistro&C='+Campo,'simg_'+Campo);
				if(document.getElementById('img_ver_'+Campo))
				{
					document.getElementById('img_ver_'+Campo).style.visibility='hidden';
					document.getElementById('img_del_'+Campo).style.visibility='hidden';
				}
			}

			function cambio_imagen_std(Campo,ruta,tamrecimg)
			{
				window.open('marcoindex.php?Acc=reg_sube_img&T=$this->Nombretabla&Id=$this->idregistro&C='+Campo+'&tri='+tamrecimg+'&ruta='+ruta,'simg_'+Campo);
			}

			function cambio_imagen_webcam(Campo,ruta,tamrecimg,alto,ancho)
			{
				window.open('marcoindex.php?Acc=reg_img_webcam&T=$this->Nombretabla&Id=$this->idregistro&C='+Campo+'&tri='+tamrecimg+'&ruta='+ruta+'&alto='+alto+'&ancho='+ancho,'simg_'+Campo);
			}

			function borra_imagen_std(Campo)
			{
				if(confirm('Desea eliminar la imagen?'))
				window.open('marcoindex.php?Acc=reg_borra_imagen&T=$this->Nombretabla&Id=$this->idregistro&C='+Campo,'simg_'+Campo);
				if(document.getElementById('img_ver_'+Campo))
				{
					document.getElementById('img_ver_'+Campo).style.visibility='hidden';
					document.getElementById('img_del_'+Campo).style.visibility='hidden';
				}
			}

			</script>";
	}

	function capas()
	{
		require('inc/link.php');
		$Tabla_Control=$this->Config->tabla."_t";
		$sql = "select distinct capa from $Tabla_Control where modificar=1 ".($this->Adicionando?" and llave=1":"")." order by capa";
			//echo "<br>";
			//echo $sql;
			
		$Capas=mysql_query($sql,$LINK);
		if(!mysql_num_rows($Capas))
		{		 
			$sql = "select distinct capa from $Tabla_Control where modificar=1 order by capa";			
			$Capas=mysql_query($sql,$LINK);
		}
		while($Cp=mysql_fetch_object($Capas))
		{
			$this->Capas[]=new capamod($Cp->capa,$Tabla_Control,$this->Config->tabla,$this->Adicionando,$LINK);
		}
		$this->Capa_inicial=$this->Capas[0]->Id;
		mysql_close($LINK);

	}

}

function pide_ciudad2()
{
	$Contenido='';
	$Departamentos=q("select distinct departamento from ciudad order by departamento ");
	include('inc/link.php');
	$Contador=1;
	while($D=mysql_fetch_object($Departamentos))
	{
		$Contenido.="<li class='depart' onclick='abre_cierra($Contador);'><b>$D->departamento</b></li>
							<ul id='mbc$Contador' style='visibility:hidden;position:absolute;cursor:pointer;' >";
		$Ciudades=mysql_query("select codigo,nombre from ciudad where departamento='$D->departamento' and right(codigo,3)='000' order by nombre",$LINK);
		while($C=mysql_fetch_object($Ciudades))
		{
			$Contenido.="<li class='ciudad' onclick=\"selecciona_ciudad('$C->codigo','$D->departamento - $C->nombre');\">$C->nombre</li>";
		}
		$Contenido.="</ul>";
		$Contador++;
	}
	mysql_close($LINK);
	return $Contenido;
}

?>