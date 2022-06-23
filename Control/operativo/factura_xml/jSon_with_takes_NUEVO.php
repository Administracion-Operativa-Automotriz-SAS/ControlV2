<?php 

class json_fact_electronica_taxes{
	
	function __construct(){
		
		$this->xml_nuevo_ptesa = '{
		  "XMLPtesa": {
			"ControlDocumento": { "Version": "1.0" },
			"Documentos": {
			  "Documento": {
				"FacturaElectronica": {
				  "Resolucion": {
					"NumeroAutorizacion": "18760000001",
					"FechaInicio": "2019-12-20",
					"FechaFin": "2030-01-19",
					"Prefijo": "IS",
					"Desde": "1",
					"Hasta": "20000"
				  },
				  "TipoOperacion": "10",
				  "NumeroDocumento": "IS990005718",
				  "AmbienteDestino": "1",
				  "FechaEmision": "2019-11-17T15:03:01-05:00",
				  "FechaVencimientoFactura": "2019-06-20",
				  "TipoFactura": "1",
				  "DivisaFactura": "COP",
				  "CantidadLineasFactura": "2.00",
				  "PeriodoFacturacion": {
					"FechaInicio": "2019-10-28",
					"FechaFin": "2019-11-29"
				  },
				  "ProveedorOF": {
					"Identificador": "102579",
					"TipoOrganizacion": "1",
					"CIIU": "7710",
					"NombreComercialEmisor": "ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S.",
					"DireccionFisica": {
					  "CodigoMunicipio": "11001",
					  "NombreCiudad": "BOGOTÁ, D.C.",
					  "CodigoPostal": "111121",
					  "NombreDepartamento": "Bogotá",
					  "CodigoDepartamento": "11",
					  "Lineas": { "Linea": "CR 69b 98A 10, Bogotá" },
					  "CodigoPais": "CO",
					  "NombrePais": "Colombia",
					  "IDLenguajePais": "es"
					},
					"InfoFiscal": {
					  "NombreRazonSocialFiscal": "ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S",
					  "NitEmisorFiscal": "900174552",
					  "DVEmisorFiscal": "5",
					  "TipoIDEmisorFiscal": "31",
					  "RegimenEmisor": "O-23",
					  "ListaRegimenEmisor": "48",
					  "DireccionFisica": {
						"CodigoMunicipio": "11001",
						"NombreCiudad": "BOGOTÁ, D.C.",
						"NombreDepartamento": "Bogotá",
						"CodigoDepartamento": "11",
						"Lineas": { "Linea": "CR 69b 98A 10, Bogotá" },
						"CodigoPais": "CO",
						"NombrePais": "Colombia",
						"IDLenguajePais": "es"
					  },
					  "IdentificadorTributo": "01",
					  "NombreTributo": "IVA"
					},
					"InfoLegal": {
					  "NombreRazonSocialLegal": "ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S",
					  "NitEmisorLegal": "900174552",
					  "DVNitEmisorLegal": "5",
					  "TipoIDEmisorLegal": "31",
					  "PrefijoFacturacion": "IS"
					},
					"Contacto":{
						"EmailContacto": "feclientes@aoacolombia.com"
					}
				  }
				}
			  }
			}
		  }
		}';
		
		$this->json_object = json_decode($this->xml_nuevo_ptesa);

            //$this->notas_faturas = json_decode($this->nota_factura);
			
			
			
			$this->valores_factura = '
			     {
					"TotalValorBruto": "12066404.00",
					"TotalBaseImponible": "12066404.00",
					"TotalValorBrutoMasTributos": "14359020.76",
					"ValorAPagarFactura": "14359020.76"
				  }';
				
			$this->varlor_factura = json_decode($this->valores_factura);
			
		
	  
	  $this->textos_libres = '{
		  "TextosLibres": { 
		  "TextoLibre": "7179510.38 COINCIDE CON EL VALOR TOTAL" 
		  },
	  }';
	  
	  $this->libres_textos = json_decode($this->textos_libres);
	  
	  
	
	$this->medio_pago = '{
		"IDMetodoPago": "1",
		"CodigoMedioPago": "46",
		"FechaVencimiento": "2019-10-05"
	}';	
	
	$this->pago_medio = json_decode($this->medio_pago);
	  
	  
	  
	  $this->extenciones = '{
		            "Extension": {
					    "Tipo": "DOC",
						"Clave": "VolorLetras",
						"Valor": "SIETE MILLONES CIENTO SETENTA Y NUEVE MIL QUINIENTOS DIEZ PESOS CON TREINTA Y OCHO CENTAVOS MCTE"
					}
				  }
			';
				  
		$this->extenciones_convertidas = json_decode($this->extenciones);
		
		$this->extenciones2 = '{
		            "Extension": {
					    "Tipo": "DOC",
						"Clave": "VolorLetras",
						"Valor": "SIETE MILLONES CIENTO SETENTA Y NUEVE MIL QUINIENTOS DIEZ PESOS CON TREINTA Y OCHO CENTAVOS MCTE"
					}
				  }
			';
				  
		$this->extenciones_convertidas2 = json_decode($this->extenciones2);
		
		$this->extenciones3 = '{
		            "Extension": {
					    "Tipo": "DOC",
						"Clave": "VolorLetras",
						"Valor": "SIETE MILLONES CIENTO SETENTA Y NUEVE MIL QUINIENTOS DIEZ PESOS CON TREINTA Y OCHO CENTAVOS MCTE"
					}
				  }
			';
				  
		$this->extenciones_convertidas3 = json_decode($this->extenciones3);
		
		$this->extenciones4 = '{
		            "Extension": {
					    "Tipo": "DOC",
						"Clave": "VolorLetras",
						"Valor": "SIETE MILLONES CIENTO SETENTA Y NUEVE MIL QUINIENTOS DIEZ PESOS CON TREINTA Y OCHO CENTAVOS MCTE"
					}
				  }
			';
				  
		$this->extenciones_convertidas4 = json_decode($this->extenciones4);
		
		
		
		$this->extenciones5 = '{
		            "Extension": {
					    "Tipo": "ITEM",
						"Clave": "VlrTotalItem",
						"Valor": "123"
					}
				  }';
				  
		$this->extenciones_convertidas5 = json_decode($this->extenciones5);
		
		$this->extenciones6 = '{
		            "Extension": {
					    "Tipo": "DOC",
						"Clave": "OrdServicio",
						"Valor": "SR4454654"
					}
				  }
			';
				  
		$this->extenciones_convertidas6 = json_decode($this->extenciones6);
		
		$this->extenciones7 = '{
		            "Extension": {
					    "Tipo": "DOC",
						"Clave": "TxtMora",
						"Valor": "SR4454654"
					}
				  }
			';
				  
		$this->extenciones_convertidas7 = json_decode($this->extenciones7);
		
		
		$this->extenciones8 = '{
		            "Extension": {
					    "Tipo": "DOC",
						"Clave": "TxtMora",
						"Valor": "SR4454654"
					}
				  }
			';
				  
		$this->extenciones_convertidas8 = json_decode($this->extenciones8);
		
		
		
		$this->subTotalTributoTarifa = '{
				"SubTotalTributoTarifa":{
                "BaseImponible": "12066404.00",
                "ValorTributo": "11111",
                "Porcentaje": "19.00"
				}
			  }';
						
			$this->tributoTarifaSubTotal = json_decode($this->subTotalTributoTarifa);
		
		
		$this->impuesto = '{
		  
			"TotalImpuesto": {
			  "ValorTributos": "11111",
			  "MontoRedondeo": "0.00",
			  "SubTotalTributoTarifa": [],
			  "IdentificadorTributo": "01",
			  "NombreTributo": "IVA"
			}
		}';
				
		
					
		$this->impuesto_conver = json_decode($this->impuesto);

		$this->tributo = '{
				"BaseImponible": "12066404.00",
				"ValorTributo": "11111",
				"Porcentaje": "19.00"
				}';
			
		$this->tributo_conver = json_decode($this->tributo);	
			
			$this->XMLPtesa = "XMLPtesa";
			
			$this->Documentos = "Documentos";
			
			$this->Documento = "Documento";
			
			$this->FacturaElectronica = "FacturaElectronica";
			
			$this->NumeroDocumento = "NumeroDocumento";
			
			$this->NotasFactura = "NotasFactura";
			
			$this->NotaFactura = "NotaFactura";
			
			$this->InfoFiscalClienteADQ = "InfoFiscalClienteADQ";
			
			$this->TotalImpuesto = "TotalImpuesto";
				
	}
	
	public function medioDePago($IDMetodoPago= 1,$CodigoMedioPago=46,$FechaVencimiento='2019-10-05'){
		
		
		$pago = $this->pago_medio;
		
		$array_pago = array();
		    
			$pago->IDMetodoPago = $IDMetodoPago;
			$pago->CodigoMedioPago = $CodigoMedioPago;
			$pago->FechaVencimiento = $FechaVencimiento;
			
			array_push($array_pago,$pago);
		   
		   $this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Pago = $array_pago;
	}
	
	
	public function resolucionFactura($resolucionNumero = '76786',$resolucionFecha = '2019-01-19',$dateSumYear = '2050-01-19',$prefijo='FE',$consecutivoInic= '1',$consecutivoFinal='2000'){
		
		//$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Resolucion->NumeroAutorizacion = $resolucionNumero;  volver a estado anterior cuando todo este bien
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Resolucion->NumeroAutorizacion = 	18764021590142;
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Resolucion->FechaInicio = '2021-11-22';
		
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Resolucion->FechaFin = '2022-11-22';
		 
		
		//$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Resolucion->FechaInicio = $resolucionFecha;
		
		
		//$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Resolucion->FechaFin = $dateSumYear;
		
		//$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Resolucion->Prefijo = $prefijo;
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Resolucion->Prefijo = "IS";
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Resolucion->Desde = 20001;
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Resolucion->Hasta = 50000;
		
		//$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Resolucion->Desde = $consecutivoInic;
		
		//$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Resolucion->Hasta = $consecutivoFinal;
	}
	public function periodoFacturacion($fechaInicio = '2020-10-28',$fechaFin = '2020-11-29'){
		
	
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->PeriodoFacturacion->FechaInicio = $fechaInicio;
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->PeriodoFacturacion->FechaFin = $fechaFin;
	}
	
	public function tipoOperacion($aseguradora){
		 
			$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->TipoOperacion = 10; 
		 
		 
	}

	
	public function proveedorOf($prefijoActual){
		
	    $this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->Identificador = 102579;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->TipoOrganizacion = 1;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->CIIU = 7710;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->NombreComercialEmisor = utf8_encode("ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S");
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->DireccionFisica->CodigoMunicipio = 11001;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->DireccionFisica->NombreCiudad = utf8_encode("BOGOTÁ, D.C.");
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->DireccionFisica->CodigoPostal = 111121;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->DireccionFisica->NombreDepartamento = utf8_encode("Bogotá");
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->DireccionFisica->CodigoDepartamento = utf8_encode("11");
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->DireccionFisica->Lineas->Linea = utf8_encode("CR 69b 98A 10, Bogotá");
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->DireccionFisica->CodigoPais = 'CO';
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->DireccionFisica->NombrePais = 'Colombia';
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->DireccionFisica->IDLenguajePais = 'es';
		
		
		
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->NombreRazonSocialFiscal = utf8_encode("ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S");
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->NitEmisorFiscal = 900174552;
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->DVEmisorFiscal = 5;
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->TipoIDEmisorFiscal = 31;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->RegimenEmisor = 'O-23';
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->ListaRegimenEmisor = 'No aplica';

		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->DireccionFisica->CodigoMunicipio = 11001;
		
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->DireccionFisica->NombreCiudad = utf8_encode("BOGOTÁ, D.C.");
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->DireccionFisica->NombreDepartamento = utf8_encode("Bogotá");
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->DireccionFisica->CodigoDepartamento = 11;
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->DireccionFisica->Lineas->Linea = utf8_encode("CR 69b 98A 10, Bogotá");
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->DireccionFisica->CodigoPais = 'CO';
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->DireccionFisica->NombrePais = 'Colombia';
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->DireccionFisica->IDLenguajePais = 'es';
     	$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->IdentificadorTributo = '01';
	    $this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoFiscal->NombreTributo = 'IVA';
						
	
	    $this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoLegal->NombreRazonSocialLegal = utf8_encode("ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S");
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoLegal->NitEmisorLegal = 900174552;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoLegal->DVNitEmisorLegal = 5;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoLegal->TipoIDEmisorLegal = 31;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->InfoLegal->PrefijoFacturacion = 'IS';
		
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ProveedorOF->Contacto->EmailContacto = 'feclientes@aoacolombia.com';
		
		
		
	
	}
	
	
	
	public function consecutive($consecutive="FE13",$fecha_emision="2019-11-17T15:03:01-05:00",$fecha_vencimiento = "2019-06-20"){
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->NumeroDocumento =  $consecutive;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->AmbienteDestino   = 1;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->FechaEmision   = $fecha_emision;
        
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->FechaVencimientoFactura = $fecha_vencimiento;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->TipoFactura = 1;
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->DivisaFactura = 'COP';
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->CantidadLineasFactura = 1;
	}
	
	public function agregar_notas_facturas($notas = array("prueba1" , "prueba2" )){
		
		foreach($notas as $nota){
			 $this->json_object->XMLPtesa->Documentos->Documento->NotasFactura->NotaFactura = $nota;
		}
	}
	
	public function set_InformacionClienteAdq($array_setcliente =  array(array("nombre_cliente" => "Pepito", "apellido_cliente" => "Perez",
	"ciudad_departamento" => "Bogota D.C.", "cliente_barrio" => "CHICO", "ciudad_nombre" => "Bogotá D.C.",
	"cliente_direccion" => "Cra X # XX - XX", "cliente_celular" => "(+571) 88 88 888","telefono_casa"=>"5455589",
	"cliente_identificacion" => "12345678","registro_name" => "ADMINISTRACIÓN OPERATIVA AUTOMOTRIZ S.A.S.", "linea" => "7560510", "correo" => "sergiourewbina@aoacolombia.com", 
	"tipo_persona" => "31231","codigo_municipio" => "11001", "dv" => 3,"tipo_id" => 4,
	"auto_retenedor_renta" => 1, 
	"auto_retenedor_iva" => 1, "auto_retenedor_rete_ica" => 1, "regimen_simple_tributacion" => 1, 
	"regimen_simple_tri_no_iva" => 1, "agente_retencion_iva_no_iva" => 1))){
		//2 --> Rut?
		//3 --> CC? 
		function eliminar_acentos($cadena){
		
		//Reemplazamos la A y a
		$cadena = str_replace(
		array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª', '°'),
		array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a',''),
		$cadena
		);

		//Reemplazamos la E y e
		$cadena = str_replace(
		array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
		array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
		$cadena );

		//Reemplazamos la I y i
		$cadena = str_replace(
		array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
		array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
		$cadena );

		//Reemplazamos la O y o
		$cadena = str_replace(
		array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
		array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
		$cadena );

		//Reemplazamos la U y u
		$cadena = str_replace(
		array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
		array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
		$cadena );

		//Reemplazamos la N, n, C y c
		$cadena = str_replace(
		array('Ñ', 'ñ', 'Ç', 'ç'),
		array('N', 'n', 'C', 'c'),
		$cadena
		);
		
		return $cadena;
	}
		$array_cliente = array();
		$array_direcciones = array();
		
		foreach($array_setcliente as $rowCliente2){
			//echo ltrim($rowCliente2["tipo_persona"],"0");
			//exit();
			
			if(ltrim($rowCliente2["tipo_persona"],"0") == 1){
				$varTipo = 2;
				} 
			if(ltrim($rowCliente2["tipo_persona"],"0") == 2 and $rowCliente2["dv"] != ''){
				
				$varTipo = 1;
				
				}else{
				 $varTipo = 2;
				}
		}
		
		if($varTipo == 1){
			$dvQ = '"DVADQ": "5",';
			$dvQ2 = '';
		}else{
			$dvQ = '';
			$dvQ2 = ',"PersonaNatural": { "NombresADQ" : "Sergio Test" }';
			
		}
			
			$this->cliente_adq = '{
		  "TipoPersona": "1",
            "IdentificacionADQ": "830096620",
            "NombreComercial": "PROFESIONALES EN TRANSACCIONES ELETRONICAS S.A. PTESA",
            "DireccionFisica": {
              "CodigoMunicipio": "11001",
              "NombreCiudad": "BOGOTÁ, D.C.",
              "NombreDepartamento": "Bogotá",
              "CodigoDepartamento": "11",
              "Lineas": { "Linea": "CARRERA 12 #89-28 PISO 5" },
              "CodigoPais": "CO",
              "NombrePais": "Colombia",
              "IDLenguajePais": "es"
            },
            "InfoFiscalClienteADQ": {
              "NombreRazonSocialFiscalADQ": "PROFESIONALES EN TRANSACCIONES ELETRONICAS S.A. PTESA",
              "NitFiscalADQ": "830096620",
              '.$dvQ.'
			  "TipoIDADQ": "31",
              "CodigoImpuesto": "O-99",
              "RegimenADQ": "48",
              "DireccionFisica": {
                "CodigoMunicipio": "11001",
                "NombreCiudad": "BOGOTÁ, D.C.",
                "NombreDepartamento": "Bogotá",
                "CodigoDepartamento": "11",
                "Lineas": { "Linea": "CARRERA 12 #89-28 PISO 5" },
                "CodigoPais": "CO",
                "NombrePais": "Colombia",
                "IDLenguajePais": "es"
              },
              "IdentificadorTributo": "01",
              "NombreTributo": "IVA"
            },
            "Contacto": { "TelefonoContacto": "12345678", 
			"EmailContacto": "layne.silva@ptesa.com"}
			'.$dvQ2.'
			
	}
	';
	
	    $this->cliente_adq_conver = json_decode($this->cliente_adq);
		
		
		
		
		
		
		foreach($array_setcliente as $rowCliente){
		
			if($rowCliente['tipo_id'] == 'NIT'){$varTipoCliente = 31;}else if($rowCliente['tipo_id'] == 'CC'){$varTipoCliente = 13;}else{if($rowCliente['tipo_id'] == 'CE'){ $varTipoCliente =  21;} if($rowCliente['tipo_id'] == 'PAS'){$varTipoCliente = 41; 
							} if($rowCliente['tipo_id'] == 'TI'){
								$varTipoCliente = 12;
								} if($rowCliente['tipo_id'] == ''){
									$varTipoCliente = 13;}
									}
									
			if($rowCliente['tipo_id'] == 'NIT' and $varTipo == 1){
				$varTipoCliente = 31;
			}else{
				$varTipoCliente = 13;
			}						
			
			$adquiriente = $this->cliente_adq_conver;
			$clienteDepartamento  = utf8_encode($rowCliente["ciudad_departamento"]);
			
			$adquiriente->TipoPersona = $varTipo;

			$adquiriente->IdentificacionADQ = $rowCliente["cliente_identificacion"];
			
			$nombreComercial =  eliminar_acentos(utf8_encode($rowCliente["nombre_cliente"]."  ".$rowCliente["apellido_cliente"]));
			
			$adquiriente->NombreComercial = $nombreComercial;
			
			$adquiriente->DireccionFisica->CodigoMunicipio = substr($rowCliente["codigo_municipio"], 0, -3);
			
			$adquiriente->DireccionFisica->NombreCiudad = utf8_encode($rowCliente["ciudad_nombre"]);
			
			$adquiriente->DireccionFisica->NombreDepartamento = $clienteDepartamento;
			
			$adquiriente->DireccionFisica->CodigoDepartamento = substr($rowCliente["codigo_municipio"], 0, -6);
			
			$Linea =  eliminar_acentos(utf8_encode($rowCliente["cliente_direccion"]));
			
			
			
			$adquiriente->DireccionFisica->Lineas->Linea = $Linea;
			
			
			
			
			$adquiriente->DireccionFisica->Lineas->Linea = $Linea;
			
			$adquiriente->DireccionFisica->CodigoPais= 'CO';
			
			$adquiriente->DireccionFisica->NombrePais= 'Colombia';
			$adquiriente->DireccionFisica->IDLenguajePais= 'es';
			$nombreFiscalAdq =  eliminar_acentos(utf8_encode($rowCliente["nombre_cliente"]."  ".$rowCliente["apellido_cliente"]));
			
			$adquiriente->InfoFiscalClienteADQ->NombreRazonSocialFiscalADQ = $nombreFiscalAdq;
			
			$adquiriente->InfoFiscalClienteADQ->NitFiscalADQ =  $rowCliente["cliente_identificacion"];
			
			
			
			if($varTipo == 1){
				$adquiriente->InfoFiscalClienteADQ->DVADQ = $rowCliente["dv"];
			}
			
			$adquiriente->InfoFiscalClienteADQ->TipoIDADQ = $varTipoCliente;
			
			$adquiriente->InfoFiscalClienteADQ->CodigoImpuesto = "R-99-PN";
			
			/*if($rowCliente["auto_retenedor_renta"] != 0){
			   $adquiriente->InfoFiscalClienteADQ->CodigoImpuesto = "O-15";	
			}elseif($rowCliente["auto_retenedor_iva"] !=0){
				$adquiriente->InfoFiscalClienteADQ->CodigoImpuesto = "O-15";
			}elseif($rowCliente["auto_retenedor_rete_ica"] !=0){
				$adquiriente->InfoFiscalClienteADQ->CodigoImpuesto = "O-15";
			}elseif($rowCliente["regimen_simple_tributacion"]){
				
			}*/
			
			$adquiriente->InfoFiscalClienteADQ->RegimenADQ = "No aplica";
			
			$adquiriente->InfoFiscalClienteADQ->DireccionFisica->CodigoMunicipio = substr($rowCliente["codigo_municipio"], 0, -3);
			
			$nombreCiudad = utf8_encode($rowCliente["ciudad_nombre"]);
			
			$adquiriente->InfoFiscalClienteADQ->DireccionFisica->NombreCiudad = $nombreCiudad;
			
		    $adquiriente->InfoFiscalClienteADQ->DireccionFisica->NombreDepartamento = $clienteDepartamento;
			
			$adquiriente->InfoFiscalClienteADQ->DireccionFisica->CodigoDepartamento = substr($rowCliente["codigo_municipio"], 0, -6);
			
			$Linea =  utf8_encode($rowCliente["cliente_direccion"]);
			
			if($Linea == ""){
				$Linea = "NO TIENE DIRECCION";
			}
			
			$adquiriente->InfoFiscalClienteADQ->DireccionFisica->Lineas->Linea = $Linea;
			$adquiriente->InfoFiscalClienteADQ->DireccionFisica->CodigoPais= 'CO';
			
			$adquiriente->InfoFiscalClienteADQ->DireccionFisica->NombrePais= 'Colombia';
			$adquiriente->InfoFiscalClienteADQ->DireccionFisica->IDLenguajePais= 'es';
			
			$adquiriente->InfoFiscalClienteADQ->IdentificadorTributo = '01';
			$adquiriente->InfoFiscalClienteADQ->NombreTributo = 'IVA';
			
			if($rowCliente["cliente_celular"] != ''){
				$telefono = $rowCliente["cliente_celular"];
			}else if($rowCliente["telefono_casa"] != ''){
				$telefono = $rowCliente["telefono_casa"];
			}else{
				$telefono = "N.A";
			}
			
			$adquiriente->Contacto->TelefonoContacto = $telefono;
			
			$adquiriente->Contacto->EmailContacto = $rowCliente["correo"];
			
			
			
			
			
			if($varTipo == 2){
				
				$varNombresAdq =  eliminar_acentos(utf8_encode($rowCliente["nombre_cliente"]."  ".$rowCliente["apellido_cliente"]));
                
				
				$adquiriente->PersonaNatural->NombresADQ = $varNombresAdq;
			}
			
			array_push($array_cliente,$adquiriente);
			
		}
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ClienteADQ = $array_cliente;
		
	}
	
	public function subTotalTributo($array_of_items=array(array("cantidad"=>2,"valor"=>"35000.00","descripcion"=>"item no.1","valor_iva"=>"2520.00","iva_perc"=>"19.00","identificador_tributo"=>"01","nombre_tributo"=>"IVA"),
	                                                    array("cantidad"=>3,"valor"=>"20000","descripcion"=>"item no.2","valor_iva"=>"0.00","iva_perc"=>"0.00","identificador_tributo"=>"06","nombre_tributo"=>"OTRO"))){
		
		$impuestos = array();

		$sublineas = array();	
		
		//print_r($array_of_items);		
		
		$i = 0;		
		
		$linea = json_decode($this->impuesto);
		
		$valorTributos = 0;
		
		foreach($array_of_items as $item){							
			
			$sublinea = json_decode($this->tributo);
			if($item["canon"]!=0){
				$baseImponible = number_format((float)($item["valor_iva"] / 0.19), 2, '.', '');
				$Porcentaje = '19.00';
			}else{
				$baseImponible = number_format((float)($item["cantidad"]*$item["valor"]), 2, '.', '');
				$Porcentaje = $item["iva_perc"];
			}
			
			
			$sublinea->BaseImponible = round($baseImponible,0,PHP_ROUND_HALF_EVEN);
			
			$sublinea->Porcentaje = ($baseImponible==0)?'0.00':$Porcentaje;			
			
			if($item["iva_perc"] == '19.00'){
				
				if($item["canon"]!=0){
					$valorTributoDiecinueve = ($item["valor_iva"]);
					$sublinea->ValorTributo = round($valorTributoDiecinueve,0,PHP_ROUND_HALF_EVEN);
				}else{
					$valorTributoDiecinueve = ($item["cantidad"]*$item["valor"])*0.19;
					$sublinea->ValorTributo = round($valorTributoDiecinueve,0,PHP_ROUND_HALF_EVEN);
				}
				$valorTributos += round($valorTributoDiecinueve,0,PHP_ROUND_HALF_EVEN);
			}else if($item["iva_perc"] == '16.00'){
				if($item["canon"]!=0){
					$valorTributoDieciseis = ($item["valor_iva"]);
				}else{
					$valorTributoDieciseis = ($item["cantidad"]*$item["valor"])*0.16;
				}
				
				$sublinea->ValorTributo = round($valorTributoDieciseis,2,PHP_ROUND_HALF_EVEN);
				//$linea->TotalImpuesto->ValorTributos = ($item["cantidad"]*$item["valor"])*0.16;
				$valorTributos += round($valorTributoDieciseis,2,PHP_ROUND_HALF_EVEN);
			}else{
				if($item["canon"]!=0){
					$sublinea->ValorTributo = ($item["valor_iva"]);
				}else{
					$sublinea->ValorTributo = 0.00;
				}
				
				//$linea->TotalImpuesto->ValorTributos = 0.00;
			}
			
			array_push($sublineas,$sublinea);						

			$i++;	
		}
		
		$linea->TotalImpuesto->ValorTributos = $valorTributos;
		$linea->TotalImpuesto->MontoRedondeo = '0.00';
		$linea->TotalImpuesto->SubTotalTributoTarifa = $sublineas;			
			
		$linea->TotalImpuesto->IdentificadorTributo = "01";
		
		$linea->TotalImpuesto->NombreTributo = "IVA";		
		
		$impuestos[0] =  $linea;
	
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->TotalImpuestos  = $impuestos;
	
	}
	
	public function valor_facturas($array_of_items=array(array("cantidad"=>2,"valor"=>"35000.00","descripcion"=>"item no.1","valor_iva"=>"2520.00","iva_perc"=>"19.00","identificador_tributo"=>"01","nombre_tributo"=>"IVA"),
	                                                    array("cantidad"=>3,"valor"=>"20000","descripcion"=>"item no.2","valor_iva"=>"0.00","iva_perc"=>"0.00","identificador_tributo"=>"06","nombre_tributo"=>"OTRO","seguro"=>"0.00","canon"=>"0.00"))){
		$valorTributos = 0;
		
		$valorTotalBruto = 0;
		$TotalBaseImponible = 0;
		
		foreach($array_of_items as $item){
			
            $baseImponible = number_format((float)($item["cantidad"]*$item["valor"]), 2, '.', '');			
		    $valorTotalBruto += round($baseImponible,0,PHP_ROUND_HALF_EVEN);
			if($item["iva_perc"] == '19.00'){
				if($item["canon"]!=0){
					$varlorTributosDiecinueve = ($item["valor_iva"]);
				}else{
					$varlorTributosDiecinueve = ($item["cantidad"]*$item["valor"])*0.19;
				}
		       
			    $valorTributos += round($varlorTributosDiecinueve,0,PHP_ROUND_HALF_EVEN);
			
			}else if($item["iva_perc"] == '16.00'){
		    	$valorTributoDieciseis = ($item["cantidad"]*$item["valor"])*0.16; 
				if($item["canon"]!=0){
					$valorTributos += ($item["valor_iva"]);
				}else{
					$valorTributos += round($valorTributoDieciseis,0,PHP_ROUND_HALF_EVEN);
				}
		        
			}else{
				if($item["canon"]!=0){
					$valorTributos += ($item["valor_iva"]);
				}else{
			        $valorTributos += 0.00;
				}
			}
			if($item["canon"]!=0){
				$TotalBaseImponible += number_format(($item["valor_iva"] / 0.19), 2, '.', '');	
			}else{
				$TotalBaseImponible += round($baseImponible,0,PHP_ROUND_HALF_EVEN);
			}
			
		}


		
		$array_valor_factura = array();
		
		$valueFac = $this->varlor_factura;
		
		$valueFac->TotalValorBruto = $valorTotalBruto;
		
		$valueFac->TotalBaseImponible = $TotalBaseImponible;
		
		$valueFac->TotalValorBrutoMasTributos = ($valorTributos + $valorTotalBruto);
		
		$valueFac->ValorAPagarFactura = ($valorTributos + $valorTotalBruto);
		
		array_push($array_valor_factura,$valueFac);
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->ValoresTotalesFactura = $array_valor_factura;
		
	}
	
	public function set_Extenciones($array_of_items=array(array("cantidad"=>2,"valor"=>"35000.00","descripcion"=>"item no.1","valor_iva"=>"2520.00","iva_perc"=>"19.00","identificador_tributo"=>"01","nombre_tributo"=>"IVA"),
	                                                    array("cantidad"=>3,"valor"=>"20000","descripcion"=>"item no.2","valor_iva"=>"0.00","iva_perc"=>"0.00","identificador_tributo"=>"06","nombre_tributo"=>"OTRO")),
														$array_extenciones = array(array("VlrEnLetras" => 'SON: TREINTA Y CINCO MIL PESOS MCTE CON CERO CENTAVOS',"resolucion_factura" => 'Autorización Numeración de Facturación No.12354417 Factura por Computador de junio 21 de 2017 Prefijo FV. Rango 22351 al 30122 Vigencia por 12 Meses',
		                                                "OBL" => 'Actividades Economicas No. 7710 Y 7020\nGrandes Contribuyentes 
																 Impuestos Distritales Bogota Resolucion DDI-032117 Shda 25 Oct 2019', "numero_orden" => "5464566","observaciones"=>"info", "observacionesMandato"=> "infoMandato")),$aseguradora){
		
		
		$array_ext = array();
		$extenciones = $this->extenciones_convertidas;
		
		$extenciones2 = $this->extenciones_convertidas2;
		
		$extenciones3 = $this->extenciones_convertidas3;
		
		$extenciones4 = $this->extenciones_convertidas4;
		
		$extenciones6 = $this->extenciones_convertidas6;
		
		$extenciones7 = $this->extenciones_convertidas7;
		
		$extenciones8 = $this->extenciones_convertidas8;
		
		
		foreach($array_extenciones as $extencion){
		    
			$extenciones->Extension->Tipo = "DOC";
			
			$extenciones->Extension->Clave = "ValorLetras";
			
			$extenciones->Extension->Valor = $extencion["VlrEnLetras"];
		    
			array_push($array_ext,$extenciones);			
			
		}
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Extensiones = $array_ext;
		
		foreach($array_extenciones as $extencion){
		    
			$extenciones2->Extension->Tipo = "DOC";
			
			$extenciones2->Extension->Clave = "MensRespon";
			
			$extenciones2->Extension->Valor = utf8_encode($extencion["OBL"]);
		    
			array_push($array_ext,$extenciones2);			
			
		}
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Extensiones = $array_ext;
		
		foreach($array_extenciones as $extencion){
		    
			$extenciones3->Extension->Tipo = "DOC";
			
			$extenciones3->Extension->Clave = "Concepto";
			
			$extenciones3->Extension->Valor = utf8_encode($extencion["resolucion_factura"]);
		    
			array_push($array_ext,$extenciones3);			
			
		}
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Extensiones = $array_ext;
		
		foreach($array_extenciones as $extencion){
		    
			$extenciones4->Extension->Tipo = "DOC";
			
			$extenciones4->Extension->Clave = "PTESA.COMPANY_BRANCH";
			
			$extenciones4->Extension->Valor = "102579";
		    
			array_push($array_ext,$extenciones4);			
			
		}
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Extensiones = $array_ext;
		
		foreach($array_extenciones as $extencion){
		    
			$extenciones6->Extension->Tipo = "DOC";
			
			$extenciones6->Extension->Clave = "OrdServicio";
			
			$extenciones6->Extension->Valor = utf8_encode($extencion['numero_orden']);
		    
			array_push($array_ext,$extenciones6);			
			
		}
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Extensiones = $array_ext;
		
		foreach($array_extenciones as $extencion){
		    
			$extenciones7->Extension->Tipo = "DOC";
			
			$extenciones7->Extension->Clave = "TxtMora";
			
			$extenciones7->Extension->Valor = utf8_encode("El incumplimiento de pago de esta factura causa interés de mora,a la tasa máxima legal permitida por la ley.");
		    
			array_push($array_ext,$extenciones7);			
		}
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Extensiones = $array_ext;
		
		if($aseguradora->mandato == 1  || $aseguradora->clase_servicio == 13){
			
		foreach($array_extenciones as $extencion){
		    
			$extenciones8->Extension->Tipo = "DOC";
			
			$extenciones8->Extension->Clave = "ObservNotes";
			
			$observacionesFacturaMandato = utf8_encode($extencion['observaciones']);
			
			$observacionesAseguradoraMandato = eliminar_acentos(trim(preg_replace('/\s+/', ' ', utf8_encode($extencion['observacionesMandato']))));
			
			$extenciones8->Extension->Valor =  "FACTURA MANDATO: ".preg_replace("[\n|\r|\n\r]", "", $observacionesAseguradoraMandato)."\n".$observacionesFacturaMandato."\n";
		    
			array_push($array_ext,$extenciones8);
		}
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->Extensiones = $array_ext;
		
		}
		
		
	}
	
	
	public function set_factItems($array_of_items=array(array("cantidad"=>2,"valor"=>"35000.00","descripcion"=>"item no.1","valor_iva"=>"2520.00","iva_perc"=>"19.00"),
	                                                    array("cantidad"=>3,"valor"=>"20000","descripcion"=>"item no.2","valor_iva"=>"0.00","iva_perc"=>"0.00","seguro"=>"0.00","canon"=>"0.00")),$aseguradora){
		if($aseguradora->mandato == 1){
			$Mandate = ',"Mandante": {"NitMandante": "900123456","DVMandante":"4","TipoIDMandante":"11"}';
		}else{
			$Mandate = '';
		}
		
		$this->linea_detalle = '{
	      "LineaDetalle":{
			"IDLinea": "1",
			
			"CantidadProductoServicio": "1",
			"IDUnidadMedida": "UN",
			"ValorTotalLinea": "6033202.00",
			"Impuestos": {
			  "Impuesto": {
				"ValorTributoLinea": "1146308.38",
				"MontoRedondeo": "0.00",
				"SubTotalTributoTarifa": {
				  "BaseImponibleLinea": "6033202.00",
				  "ValorTributoLineaBase": "1146308.38",
				  "Porcentaje": "19.00"
				},
				"IdentificadorTributo": "01",
				"NombreTributo": "IVA"
			  }
			},
			"Item": {"DescripcionItem": { "Texto": "Arrendamiento de Torre" },
			"Extensiones":{"Extension": { "Tipo": "ITEM","Clave": "VlrTotalItem","Valor": "123" }}
			'.$Mandate.'
			},
			
			"Precio": {
			  "PrecioArticulo": "6033202.00",
			  "CantidadBase": "1.00",
			  "UnidadDeCantidad": "UN"
			}
		  }
	}';
	  
	   
		$contar = count($array_of_items);
		
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->CantidadLineasFactura = $contar;
		
		$lineas = array();
		$i = 0;
		
		foreach($array_of_items as $item){
			$i++;
			//var_dump($array_of["descripcion"]);
			
			//exit();
			
			$linea =  json_decode($this->linea_detalle);
			
            //print("<pre>".print_r($linea,true)."</pre>"); para ver el arraglo mucho mejor
			
			$linea->LineaDetalle->IDLinea = $i;
			
			
			$linea->LineaDetalle->CantidadProductoServicio = $item["cantidad"];
			
			$linea->LineaDetalle->IDUnidadMedida = "94";
			
			$linea->LineaDetalle->ValorTotalLinea = ($item["valor"] * $item["cantidad"]);
			 $linea->LineaDetalle->Impuestos->Impuesto->MontoRedondeo = '0.00';
			
			if($item["iva_perc"] == '19.00'){
				$valorTributoDiecinueve = ($item["cantidad"]*$item["valor"])*0.19;
				$valorTributoBase = round($valorTributoDiecinueve,0,PHP_ROUND_HALF_EVEN);
				
			
			
			}else if($item["iva_perc"] == '16.00'){
				$valorTributoDieciseis = ($item["cantidad"]*$item["valor"])*0.16;
				$valorTributoBase = round($valorTributoDieciseis,2,PHP_ROUND_HALF_EVEN);
				
			
				
				
			}else{
				$valorTributoBase = 0.00;
				
			}

			$BaseImponibleLinea = number_format((float)($item["cantidad"]*$item["valor"]), 2, '.', '');

			if($item["canon"]!=0){
				$valorTributoBase = $item["valor_iva"];
				$BaseImponibleLinea = number_format(($item["valor_iva"] / 0.19), 2, '.', '');
			}

			
			$linea->LineaDetalle->Impuestos->Impuesto->ValorTributoLinea = $valorTributoBase;
			$linea->LineaDetalle->Impuestos->Impuesto->MontoRedondeo = '0.00';
		   
			$linea->LineaDetalle->Impuestos->Impuesto->SubTotalTributoTarifa->BaseImponibleLinea = $BaseImponibleLinea;
			
			$linea->LineaDetalle->Impuestos->Impuesto->SubTotalTributoTarifa->ValorTributoLineaBase = $valorTributoBase;
			
			
			$linea->LineaDetalle->Impuestos->Impuesto->SubTotalTributoTarifa->Porcentaje = ($valorTributoBase==0)?'0.00':$item["iva_perc"];
			
			$linea->LineaDetalle->Impuestos->Impuesto->IdentificadorTributo = "01";
			
			$linea->LineaDetalle->Impuestos->Impuesto->NombreTributo = "IVA";
			
			$textoItem = eliminar_acentos(trim(preg_replace('/\s+/', ' ', utf8_encode($item["descripcion"]))));
			
			$linea->LineaDetalle->Item->DescripcionItem->Texto = $textoItem;
			
			$linea->LineaDetalle->Item->Extensiones->Extension->Tipo = "ITEM";
			
			
			//$linea->LineaDetalle->Item->Extensiones->Extension->idItem = $i;
			
			if($item["iva_perc"] == '19.00'){
				
				//$linea->TotalImpuesto->ValorTributos = ($item["cantidad"]*$item["valor"])*0.19;			
				$valorTributoDiecinueve = ($item["cantidad"]*$item["valor"])*0.19;
				$valorTributos = round($valorTributoDiecinueve,0,PHP_ROUND_HALF_EVEN);
			}else if($item["iva_perc"] == '16.00'){
				
				//$linea->TotalImpuesto->ValorTributos = ($item["cantidad"]*$item["valor"])*0.16;
				$valorTributoDieciseis =  ($item["cantidad"]*$item["valor"])*0.16;
				$valorTributos = round($valorTributoDieciseis,2,PHP_ROUND_HALF_EVEN);
			}else{
				$valorTributoCero = ($item["cantidad"]*$item["valor"])*0.00;
				$valorTributos = round($valorTributoCero,0,PHP_ROUND_HALF_EVEN);
				//$linea->TotalImpuesto->ValorTributos = 0.00;
			}
			if($item["canon"]!=0){
				$valorItem = $valorTributoBase + ($item["valor"] * $item["cantidad"]);
			}else{
				$valorItem = $valorTributos + ($item["valor"] * $item["cantidad"]);
			}
			
			$linea->LineaDetalle->Item->Extensiones->Extension->Clave = "VlrTotalItem";
			
			
			$linea->LineaDetalle->Item->Extensiones->Extension->Valor = $valorItem ;
			
			
			
			if($aseguradora->mandato == 1){
			
			$linea->LineaDetalle->Item->Mandante->NitMandante = $aseguradora->nit;
			
			$linea->LineaDetalle->Item->Mandante->DVMandante = $aseguradora->dv_cliente_juridico;
			
			$linea->LineaDetalle->Item->Mandante->TipoIDMandante = $aseguradora->tipo_mandato;
			
			}
			
			
			
			$linea->LineaDetalle->Precio->PrecioArticulo = $item["valor"];
			
			$linea->LineaDetalle->Precio->CantidadBase = $item["cantidad"];
			
			$linea->LineaDetalle->Precio->UnidadDeCantidad = "ZZ";
			
		array_push($lineas,$linea);
		
		
		}
		//print_r($lineas);
		//exit();
		$this->json_object->XMLPtesa->Documentos->Documento->FacturaElectronica->LineasDetalle = $lineas;
		
	}
	
	
}
