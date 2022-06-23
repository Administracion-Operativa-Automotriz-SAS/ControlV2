<?

CLASS acumulador                	# CLASE UTILIZADA PARA LOS ACUMULADOS POR CADA ROMPIMIENTO INDIVIDUALMENTE
	{
		var $Nombre_variable;
		var $Operacion;
		var $Valor_acumulado=0;
		var $Contador=0;
		var $Promedio=0;

		function acumulador($R)		# FUNCION CONSTRUCTORA RECIBE LOS DATOS DE UN REGISTRO TIPO CAMPO
		{
			$this->Nombre_variable=$R->apodo;
			$this->Operacion=$R->operaciont;
		}

		function inicializa($I)			# INICIALIZA CADA VARIABLE AL INICIO DEL INFORME O EN EL CAMBIO DE ROMPIMIENTO
		{
			switch($this->Operacion)
			{
				case 'SUM':eval('$this->Valor_acumulado=$I->'.$this->Nombre_variable.';');break;
				case 'COUNT':$this->Valor_acumulado=1;break;
				case 'AVG':eval('$this->Valor_acumulado=$I->'.$this->Nombre_variable.';');
				 			  $this->Contador=1;
							  $this->Promedio=($this->Contador?$this->Valor_acumulado/$this->Contador:0);
							  break;
			}
		}

		function acumula($I)		#  ACUMULA CADA VARIABLE ATRAVES DEL ROMPIMIENTO ACTUAL
		{
			switch($this->Operacion)
			{
				case 'SUM':eval('$this->Valor_acumulado+=$I->'.$this->Nombre_variable.';');break;
				case 'COUNT':$this->Valor_acumulado+=1;break;
				case 'AVG':eval('$this->Valor_acumulado+=$I->'.$this->Nombre_variable.';');
							  $this->Contador+=1;
							  $this->Promedio=($this->Contador?$this->Valor_acumulado/$this->Contador:0);
							  break;
			}
		}
	}
##
##
##
	CLASS rompimiento  			#  CLASE PARA CONTROLAR LOS ROMPIMIENTOS DE ACUERDO AL CONTROL DE CAMBIO DE ROMPIMIENTO
	{
		var $Nombre_rompimiento='';
		var $Valor_control;
		var $Acumulador=array();
		var $script_Cabecera='';
		var $script_Pie='';
		var $Acumuladores=0;     ### controla el numero de acumuladores empezando con el numero 1
		var $Pinta_acumuladores='';
		var $Color;
		var $Label_grafica=0;  ## si Label_grafica=1 en Serie_grafica se guarda la instruccion del cookie para los datos de la gráfica
		var $Serie_grafica='';

		function rompimiento($R,&$Gcookiesd,&$Gcookiesl)		# FUNCION CONSTRUCTORA, RECIBE LOS DATOS DE UN REGISTRO TIPO ORDEN
		{
			$this->Nombre_rompimiento=$R->nombre;
			$this->script_Cabecera=$R->cabecera;
			$this->script_Pie=$R->pie;
			$this->Color=$R->color;
			$this->Label_grafica=$R->lgrafica;
			$this->configura_acumuladores(&$Gcookiesd,&$Gcookiesl);
		}

		function configura_acumuladores(&$Gcookiesd,&$Gcookiesl)		# FUNCION QUE CREA CADA UNO DE LOS ACUMULADORES PARA ESTE ROMPIMIENTO, USA LA CLASE ACUMULADOR
		{
			global $ID;
			$Incluir_id=qo1("select incluir_id from aqr_reporte where id=$ID");
			$Colspan_acumulador=0;
			if($Incluir_id)
			{
				$this->Pinta_acumuladores="<td>&nbsp;</td>";
				#$Colspan_acumulador=1;
			}
			if($Campos=q("Select * from aqr_reporte_field where idreporte=$ID and ver=1 order by orden"))
			{
				$SwColspan=false;
				while($C=mysql_fetch_object($Campos))
				{
					if($C->apodo==$this->Nombre_rompimiento)   ### AVERIGUA SI EL CAMPO ES EL OBJETO DEL ROMPIMIENTO PARA MOSTRARLO COMO NOMBRE Y USARLO COMO CONTROL
					{
						$this->Pinta_acumuladores.="<td align='left' bgcolor='".$this->Color."' Colspan='0'><b>TOTAL \".\$R[\$i]->Valor_control.\"</b> ";
						$SwColspan=true;
					}
					else   ## UTILIZA EL CAMPO PARA CONTROLAR LOS COLSPAN DEL INICIO DEL ROMPIMIENTO PARA PODERLO PINTAR
					{
						if(!$SwColspan)
							$this->Pinta_acumuladores.="<td align='".ejec_alinea($C->alinea)."' bgcolor='".$this->Color."'>";
						else
							$Colspan_acumulador++;
					}
					if($C->operaciont)
					{
						if($SwColspan && $C->apodo!=$this->Nombre_rompimiento)
							$this->Pinta_acumuladores.="<td align='".ejec_alinea($C->alinea)."' bgcolor='".$this->Color."'>";
						$SwColspan=false;
						###-------------------------------------------------------------------------------------------------------------------------
						$this->Acumuladores++;
						$this->Acumulador[$this->Acumuladores]= new acumulador($C);		# USO DE LA CLASE ACUMULADOR DENTRO DE ROMPIMIENTO
						###-------------------------------------------------------------------------------------------------------------------------
						$this->Pinta_acumuladores.="<b>\".coma_formatd(\$R[\$i]->Acumulador[".$this->Acumuladores."]->";
						$this->Pinta_acumuladores.=($C->operaciont=='AVG'?'Promedio':'Valor_acumulado');
						$this->Pinta_acumuladores.=",$C->comad).\"</b>";
						if($C->grafica && $this->Label_grafica) ## si este campo corresponde a una serie de gráfica y el rompimiento es label de gráfica
						{
							## se graba la instrucción para el cookie de la gráfica a partir de el total de este rompimiento
							$this->Serie_grafica="\$_SESSION['SERIE_d_".$ID."_".$C->apodo."'][]=\$R[\$i]->Acumulador[".$this->Acumuladores."]->".
														($C->operaciont=='AVG'?'Promedio':'Valor_acumulado').
														";\$_SESSION['SERIE_l_".$ID."_".$C->apodo."'][]=\$R[\$i]->Valor_control;";
							$Gcookiesd.=$ID."_$C->apodo,";
							$Gcookiesl.=$ID."_$C->apodo,";
							eval("\$_SESSION['SERIE_d_".$ID."_".$C->apodo."']=array();\$_SESSION['SERIE_l_".$ID."_".$C->apodo."']=array();");
						}
					}
					$this->Pinta_acumuladores.="</td>";
				}
				if($Colspan_acumulador)
				{
					$this->Pinta_acumuladores=str_replace(" Colspan='0'"," Colspan='$Colspan_acumulador'",$this->Pinta_acumuladores);
				}
			}
		}

		function inicializa_acumuladores($I,$IA=NULL)		# INICIALIZACION DE LOS ACUMULADORES DEL ROMPIMIENTO AL INICIO DEL INFORME O EN UN CAMBIO DE ROMPIMIENTO
		{
			global $Inicia_Rompimientos;
			if($this->script_Pie && !$Inicia_Rompimientos ) eval($this->script_Pie);
			if($this->script_Cabecera && $I) eval($this->script_Cabecera);

			for($i=1;$i<=$this->Acumuladores;$i++)
			{
				$this->Acumulador[$i]->inicializa($I);
			}
		}

		function incrementa_acumuladores($I)	# INCREMENTO DE LOS ACUMULADORES DEL ROMPIMIENTO
		{
			for($i=1;$i<=$this->Acumuladores;$i++)
			{
				$this->Acumulador[$i]->acumula($I);
			}
		}
	}

?>
