<?php

header("Content-Type: text/plain");
//header("Content-type: text/xml");

 
error_reporting(E_ALL);
ini_set('display_errors', 1);


/*include_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/DbConnect.php");

include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/Json2xml.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/controllers/PTESA_ws.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/aoa_json_with_taxes.php");*/

require_once($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/factura_electronica.php");

//include($_SERVER["DOCUMENT_ROOT"]."/Control/operativo/factura_xml/NumeroALetras.php");

$json_taxes = '{
	"fe:Invoice": {
		"-xsi:schemaLocation": "http://www.dian.gov.co/contratos/facturaelectronica/v1 http://www.dian.gov.co/micrositios/fac_electronica/documentos/XSD/r0/DIAN_UBL.xsd urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2 http://www.dian.gov.co/micrositios/fac_electronica/documentos/common/UnqualifiedDataTypeSchemaModule-2.0.xsd urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2 http://www.dian.gov.co/micrositios/fac_electronica/documentos/common/UBL-QualifiedDatatypes-2.0.xsd",		
		"-xmlns:cac": "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2",
		"-xmlns:udt": "urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2",
		"-xmlns:clmIANAMIMEMediaType": "urn:un:unece:uncefact:codelist:specification:IANAMIMEMediaType:2003",
		"-xmlns:cbc": "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2",
		"-xmlns:sts": "http://www.dian.gov.co/contratos/facturaelectronica/v1/Structures",
		"-xmlns:clm54217": "urn:un:unece:uncefact:codelist:specification:54217:2001",
		"-xmlns:clm66411": "urn:un:unece:uncefact:codelist:specification:66411:2001",
		"-xmlns:fe": "http://www.dian.gov.co/contratos/facturaelectronica/v1",
		"-xmlns:qdt": "urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2",
		"-xmlns:ext": "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2",
		"-xmlns:xsi": "http://www.w3.org/2001/XMLSchema-instance",
		"ext:UBLExtensions": {
			"ext:UBLExtension": {
				"ext:ExtensionContent": {
					"PtesaExtensions:PtesaExtensions": {
						"-xmlns": "http://www.ptesa.com/facturaelectronica/v1/Structures",
						"-xmlns:PtesaExtensions": "http://www.ptesa.com/facturaelectronica/v1/Structures",
						"InformacionDeAdquiriente": {
							"Informacion": [{
									"Identificador": "EmailAdq",
									"Valor": "sergiocastillo@aoacolombia.com"
								},
								{
									"Identificador": "DigitoVerif",
									"Valor": "1"
								},
								{
									"Identificador": "AplicaRUT",
									"Valor": "0"
								},
								{
									"Identificador": "AplicaCC",
									"Valor": "0"
								}
							],
							"InformacionRUT": [{
									"Identificador": "AR-01",
									"Valor": "Nombre SAS."
								},
								{
									"Identificador": "AR-02",
									"Valor": "E-07"
								},
								{
									"Identificador": "AR-03",
									"Valor": "69631"
								},
								{
									"Identificador": "AR-04",
									"Valor": "Usaquén"
								},
								{
									"Identificador": "AR-05",
									"Valor": "BOGOTA, D.C."
								},
								{
									"Identificador": "AR-06",
									"Valor": "BOGOTA, D.C."
								},
								{
									"Identificador": "AR-07",
									"Valor": "CR 11 10 92"
								},
								{
									"Identificador": "AR-08",
									"Valor": "CO"
								},
								{
									"Identificador": "AR-09",
									"Valor": "COLOMBIA"
								},
								{
									"Identificador": "AR-10",
									"Valor": "Comercial SAS."
								},
								{
									"Identificador": "AR-11",
									"Valor": "830016046-1"
								},
								{
									"Identificador": "AR-12",
									"Valor": "O-03;O-05;O-07;O-08;O-09;O-10;O-11;O-13;O-14;O-15;O-33;O-19;O-18;O-40;O-41;O-42"
								},
								{
									"Identificador": "AR-13",
									"Valor": "Chico"
								},
								{
									"Identificador": "AR-14",
									"Valor": "BOGOTA, D.C."
								},
								{
									"Identificador": "AR-15",
									"Valor": "BOGOTA, D.C."
								},
								{
									"Identificador": "AR-16",
									"Valor": "CR 11 93 92"
								},
								{
									"Identificador": "AR-17",
									"Valor": "CO"
								},
								{
									"Identificador": "AR-18",
									"Valor": "COLOMBIA"
								}
							],
							"InformacionCamaraComercio": [{
									"Identificador": "AC-01",
									"Valor": "AVANTEL SAS."
								},
								{
									"Identificador": "AC-02",
									"Valor": "696311"
								},
								{
									"Identificador": "AC-03",
									"Valor": "Chico"
								},
								{
									"Identificador": "AC-04",
									"Valor": "BOGOTA, D.C."
								},
								{
									"Identificador": "AC-05",
									"Valor": "11001"
								},
								{
									"Identificador": "AC-06",
									"Valor": "BOGOTA, D.C."
								},
								{
									"Identificador": "AC-07",
									"Valor": "CR 11 93 92"
								},
								{
									"Identificador": "AC-08",
									"Valor": "CO"
								},
								{
									"Identificador": "AC-09",
									"Valor": "COLOMBIA"
								}
							]
						},
						"InformacionDeDocumento": {
							"Informacion": [{
									"Identificador": "FechaVenc",
									"Valor": "2018/07/06"
								},
								{
									"Identificador": "TextoResolDian",
									"Valor": "Autorización Numeración de Facturación No.12354417 Factura por Computador de junio 21 de 2017 Prefijo FV. Rango 22351 al 30122 Vigencia por 12 Meses"
								},
								{
									"Identificador": "TextoEstatusOBL",
									"Valor": "Agente retenedor de iva autorretenedores segun resolucion 078951 de 230324 tasa de cambio sujeta a recaudacion despes de tres dias calendario de fecha factura "
								},
								{
									"Identificador": "VlrEnLetras",
									"Valor": "SON: TREINTA Y CINCO MIL PESOS MCTE CON CERO CENTAVOS"
								},
								{
									"Identificador": "OrdCompra",
									"Valor": "PA-SRP0000000005"
								}
							]
						}
					}
				}
			}
		},
		"cbc:UBLVersionID": "UBL 2.0",
		"cbc:ProfileID": "DIAN 1.0",
		"cbc:ID": "FE18",
		"cbc:IssueDate": "2018-08-01",
		"cbc:IssueTime": "17:05:05",
		"cbc:InvoiceTypeCode": {
			"-listAgencyID": "195",
			"-listAgencyName": "CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)",
			"-listURI": "http://www.dian.gov.co",
			"-listSchemeURI": "http://www.dian.gov.co/contratos/facturaelectronica/v1/InvoiceType",
			"#text": "1"
		},
		"cbc:DocumentCurrencyCode": "COP",
		"fe:AccountingSupplierParty": {
			"cbc:AdditionalAccountID": {
				"-schemeDataURI": "http://www.dian.gov.co",
				"#text": "1"
			},
			"fe:Party": {
				"cac:PartyIdentification": {
					"cbc:ID": {
						"-schemeID": "31",
						"-schemeName": "NIT",
						"-schemeAgencyID": "195",
						"-schemeAgencyName": "CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)",
						"#text": "900174552"
					}
				},
				"cac:PartyName": {
					"cbc:Name": "ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S"
				},
				"fe:PhysicalLocation": {
					"fe:Address": {
						"cbc:Department": "Bogotá D.C.",
						"cbc:CitySubdivisionName": "La Alborada",
						"cbc:CityName": "Bogotá D.C.",
						"cac:AddressLine": {
							"cbc:Line": "Cl. 98a #69-96"
						},
						"cac:Country": {
							"cbc:IdentificationCode": {
								"-listID": "ISO 3166-1",
								"#text": "CO"
							},
							"cbc:Name": "Colombia"
						}
					}
				},
				"fe:PartyTaxScheme": {
					"cbc:TaxLevelCode": "0"
				},
				"fe:PartyLegalEntity": {
					"cbc:RegistrationName": "ADMINISTRACION OPERATIVA AUTOMOTRIZ S.A.S"
				},
				"cac:Contact": {
					"cbc:Telephone": "(1) 5897548"
				}
			}
		},
		"fe:AccountingCustomerParty": {
			"cbc:AdditionalAccountID": {
				"-schemeDataURI": "http://www.dian.gov.co",
				"#text": "2"
			},
			"fe:Party": {
				"cac:PartyIdentification": {
					"cbc:ID": {
						"-schemeID": "13",
						"-schemeName": "CC",
						"-schemeAgencyID": "195",
						"-schemeAgencyName": "CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)",
						"#text": "00000000"
					}
				},
				"cac:PartyName": {
					"cbc:Name": "Pepito Perez"
				},
				"fe:PhysicalLocation": {
					"fe:Address": {
						"cbc:Department": "Bogota D.C.",
						"cbc:CitySubdivisionName": "CHICO",
						"cbc:CityName": "Bogotá D.C.",
						"cac:AddressLine": {
							"cbc:Line": "Cra X # XX - XX"
						},
						"cac:Country": {
							"cbc:IdentificationCode": {
								"-listID": "ISO 3166-1",
								"#text": "CO"
							},
							"cbc:Name": "Colombia"
						}
					}
				},
				"fe:PartyTaxScheme": {
					"cbc:TaxLevelCode": "0"
				},
				"fe:PartyLegalEntity": {
					"cbc:RegistrationName": "Soluciones Pepito S.A.S."
				},
				"cac:Contact": {
					"cbc:Telephone": "(+571) 88 88 888"
				},
				"fe:Person": {
					"cbc:FirstName": "Pepito",
					"cbc:FamilyName": "Perez"
				}
			}
		},
		"fe:TaxTotal": {
			"cbc:TaxAmount": {
				"-currencyID": "COP",
				"#text": "4300.00"
			},
			"cbc:TaxEvidenceIndicator": "false",
			"fe:TaxSubtotal": [{
					"cbc:TaxableAmount": {
						"-currencyID": "COP",
						"#text": "20000.00"
					},
					"cbc:TaxAmount": {
						"-currencyID": "COP",
						"#text": "3800.00"
					},
					"cbc:Percent": "19.00",
					"cac:TaxCategory": {
						"cac:TaxScheme": {
							"cbc:ID": {
								"-schemeURI": "http://www.dian.gov.co",
								"#text": "01"
							}
						}
					}
				},
				{
					"cbc:TaxableAmount": {
						"-currencyID": "COP",
						"#text": "4946316.00"
					},
					"cbc:TaxAmount": {
						"-currencyID": "COP",
						"#text": "939800.00"
					},
					"cbc:Percent": "16.00",
					"cac:TaxCategory": {
						"cac:TaxScheme": {
							"cbc:ID": {
								"-schemeName": "IVA",
								"-schemeURI": "http://www.dian.gov.co",
								"#text": "01"
							}
						}
					}
				}
			]
		},
		"fe:LegalMonetaryTotal": {
			"cbc:LineExtensionAmount": {
				"-currencyID": "COP",
				"#text": "30000.00"
			},
			"cbc:TaxExclusiveAmount": {
				"-currencyID": "COP",
				"#text": "6700.00"
			},
			"cbc:AllowanceTotalAmount": {
				"-currencyID": "COP",
				"#text": "0.00"
			},
			"cbc:PayableAmount": {
				"-currencyID": "COP",
				"#text": "36700.00"
			}
		},
		"fe:InvoiceLine": [{
				"cbc:ID": "1",
				"cbc:InvoicedQuantity": "2.0",
				"cbc:LineExtensionAmount": {
					"-currencyID": "COP",
					"#text": "3432.64"
				},
				"cac:TaxTotal": {
					"cbc:TaxAmount": {
						"-currencyID": "COP",
						"#text": "3505253.00"
					},
					"cbc:TaxEvidenceIndicator": "false",
					"cac:TaxSubtotal": {
						"cbc:TaxableAmount": {
							"-currencyID": "COP",
							"#text": "18448700.00"
						},
						"cbc:TaxAmount": {
							"-currencyID": "COP",
							"#text": "3505253.00"
						},
						"cbc:Percent": "19.00",
						"cac:TaxCategory": {
							"cac:TaxScheme": {
								"cbc:ID": {
									"-schemeName": "IVA",
									"-schemeURI": "http://www.dian.gov.co",
									"#text": "01"
								}
							}
						}
					}
				},
				"fe:Item": {
					"cbc:Description": "DAÑOS CAUSADOS AL VEHICULO DE PLACAS HJV904 VEHICULO RETORNA CON GOLPE EN EL BOCA RUEDA LADO DERECHO"
				},
				"fe:Price": {
					"cbc:PriceAmount": {
						"-currencyID": "COP",
						"#text": "3432.64"
					}
				}
			},
			{
				"cbc:ID": "2",
				"cbc:InvoicedQuantity": "1.0",
				"cbc:LineExtensionAmount": {
					"-currencyID": "COP",
					"#text": "20000.00"
				},
				"cac:TaxTotal": {
					"cbc:TaxAmount": {
						"-currencyID": "COP",
						"#text": "3505253.00"
					},
					"cbc:TaxEvidenceIndicator": "false",
					"cac:TaxSubtotal": {
						"cbc:TaxableAmount": {
							"-currencyID": "COP",
							"#text": "18448700.00"
						},
						"cbc:TaxAmount": {
							"-currencyID": "COP",
							"#text": "3505253.00"
						},
						"cbc:Percent": "16.00",
						"cac:TaxCategory": {
							"cac:TaxScheme": {
								"cbc:ID": {
									"-schemeName": "IVA",
									"-schemeURI": "http://www.dian.gov.co",
									"#text": "01"
								}
							}
						}
					}
				},
				"fe:Item": {
					"cbc:Description": "SERVICIO RENTA CORTO PLAZO VEHICULO SERVICIO DE RENTA CORTO PLAZO POR 3 DÍAS."
				},
				"fe:Price": {
					"cbc:PriceAmount": {
						"-currencyID": "COP",
						"#text": "20000.00"
					}
				}
			}
		]
	}
}';


//$json_obj = json_decode($json_taxes);

//print_r($json_obj);

//$xml_g = utf8_decode(Json2xml::generate_xml($json_obj));


/*header('Content-disposition: attachment; filename="newfile.xml"');

header('Content-type: "text/xml"; charset="utf8"');*/

//echo $xml_g;

//exit;

//$ptesa = new PTESA_ws();
//$ptesa->test_new_xml(base64_encode($xml_g),"FE18"); 
//print_r($ptesa);

//exit;


//XML PARA FÁCTURAS.

if(isset($_GET["id"]))
{
	$test_fact = new test_fact(); 
	$xml_g = $test_fact->test_xml($_GET["id"]);
	echo $xml_g;	
	//exit;
	
	$ptesa = new PTESA_ws();
	$response = $ptesa->test_new_xml(base64_encode($xml_g),"FE816");
	//print_r($ptesa);
	print_r($response);
	
}
if(isset($_GET["test_json"])){
	$test_fact_objet = new test_fact;
	
	$object = $test_fact_objet->test_json();
	
	echo Json2xml::generate_xml($object);
	
  }


if(isset($_GET["ncid"]))
{
	echo "in ncid";
	
	$fact = new factura_electronica(null);
	
	$sql = "select * from nota_credito where id = '".$_GET["ncid"]."'";
	
	//echo $sql;
		
	$result = $fact->connect->query($sql);
		
	$nota_credito = $fact->connect->convert_object($result);	
	
	print_r($nota_credito);
	
	$special_invoices = array(43716,43717,43718,43719,43720);
	
	if(in_array($nota_credito->factura,$special_invoices))
	{
		echo " esta factura debe mandarse mal ";
	}	
	
	//exit;
	
	$fact->set_nota_credito($nota_credito);
	
	$xml_data = $fact->generate_xml_NC_with_taxes($nota_credito->id,"NC".$nota_credito->id);
	
	//print_r($xml_data);
	
	echo $xml_data["xml_g"];
}

//$test_fact = new test_fact();
//$json_obj = $test_fact->test_json();
//$xml_g = utf8_decode(Json2xml::generate_xml($json_obj));

//header('Content-disposition: attachment; filename="newfile.xml"');
//header('Content-type: "text/xml"; charset="utf8"');


//echo $xml_g;

//exit;

//$ptesa = new PTESA_ws();
//$response = $ptesa->test_new_xml(base64_encode($xml_g),"FE13");
//print_r($ptesa);
//print_r($response);

 
Class test_fact

{ 
	function __construct(){
		
		$this->connect = new DbConnect();
		$this->fact_obj = new json_fact_electronica_taxes();
	}
	
	
	public function test_xml($idfac)
	{
		$sql = "Select * from factura where id = ".$idfac;

		$query = $this->connect->query($sql);

		$factura = $this->connect->convert_object($query);		

		//Cliente

		$sql = "select * from cliente where id = ".$factura->cliente;		
		$query = $this->connect->query($sql);
		$cliente = $this->connect->convert_object($query);


		//Ciudad

		$sql = "select * from ciudad where codigo = ".$cliente->ciudad;		
		$query = $this->connect->query($sql); 
		$ciudad = $this->connect->convert_object($query);			


		//Detalles de factura

		$sql = "select f.*, c.nombre as concepto , c.porc_iva as ivaperc from facturad as f inner join  concepto_fac as c on c.id = f.concepto where factura = ".$factura->id ;		

		$query = $this->connect->query($sql);
		$detalles = $this->connect->convert_objects($query);


		if(isset($detalles[0]->id_siniestro))
		{
			$id_siniestro = $detalles[0]->id_siniestro;
		}
		else
		{
			$id_siniestro = $factura->siniestro;
		}

		//Siniestro  Esto toca modificarlo para que funcione con facturación por lote

		$sql = "select * from siniestro where id = ".$id_siniestro;		
		$query = $this->connect->query($sql);
		$siniestro = $this->connect->convert_object($query);		
		
		if(isset($siniestro->ciudad))
		{
			$sql = "select * from ciudad where id = ".$siniestro->ciudad;		
			$query = $this->connect->query($sql);		
			$ciudad_siniestro =  $this->connect->convert_object($query);		
		}
		

		
		
		$consecutive = $factura->consecutivo;

		$consecutive = "FE816";	
		
		$this->fact_obj->consecutive($consecutive); 


		$cliente->email_e = trim($cliente->email_e);


		//$this->fact_obj->set_InformacionDeAdquiriente($cliente->email_e);
		//$this->fact_obj->set_InformacionDeAdquiriente("ventas.javc@gmail.com");
		$this->fact_obj->set_InformacionDeAdquiriente("sergio.castillo@helpnow.com.co");


		//echo $cliente->email_e;

		if (filter_var($cliente->email_e, FILTER_VALIDATE_EMAIL)) {
			//echo "correo valido";

		} else {
			
			echo json_encode(array("status"=>"ERROR","desc"=>"El correo del usuario es invalido"));
			//exit;
			exit;
			
		}

		//Familyname no puede ir vacio o da error
		if(strlen($cliente->apellido) <= 1 )
		{				
			$cliente->apellido = $cliente->nombre;	
		}

		$this->fact_obj->set_InformacionCliente($cliente->nombre,$cliente->apellido,$ciudad->departamento,$cliente->barrio,$ciudad->nombre,$cliente->direccion,$cliente->celular,$cliente->identificacion,"");			

		$orden_compra = "N.A";
		if(isset($siniestro->numero))
		{
			//No todas las facturas tienen siniestro
			$orden_compra = $siniestro->numero;
		}

		$sql = "select * from aoacol_aoacars.resolucion_factura order by fecha desc limit 1;";

		$query = $this->connect->query($sql);
		$resolucion_fact = $this->connect->convert_object($query);

		//$resolucion = "Numeracion autorizada por la DIAN segun resolucion No. ".$resolucion_fact->numero." de ".$resolucion_fact->fecha." ".$resolucion_fact->consecutivo_inicial."-".$resolucion_fact->consecutivo_final;	

		$resolucion = "Documento oficial de autorizacion de numeracion de facturacion No. ".$resolucion_fact->numero." de                   ".$resolucion_fact->fecha." vigencia 24 meses ".$resolucion_fact->prefijo."".$resolucion_fact->consecutivo_inicial." ".$resolucion_fact->prefijo."".$resolucion_fact->consecutivo_final;			
		
		$observaciones = "";
		
		if(strlen($factura->comentario_factura) > 0)
		{
			$observaciones .= $factura->comentario_factura;		
		}	
		if(isset($ciudad_siniestro))
		{	
			$observaciones .= " Ciudad ".$ciudad_siniestro->nombre;
		}
		
		$this->fact_obj->set_InformacionDeDocumento($factura->fecha_vencimiento,NumeroALetras::convertir($factura->total, 'pesos', 'centavos'),$orden_compra,$resolucion,$observaciones);		
		
		$this->fact_obj->set_LegalMonetaryTotal($factura->subtotal,$factura->iva,0);
			
		$items = array();	
			
		$total_taxes_amount = 0;

		$array_of_taxes = array();	

		$tax_19 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"19.00");
		
		$tax_16 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"16.00");
		
		$tax_0 = array("TaxBase"=>"0","TaxValue"=>"0","TaxPercent"=>"0.00");
		
		
		
		foreach($detalles as $detalle)
		{
			$iva_perc = (int)$detalle->ivaperc;
			
			$item = array();
			$item["cantidad"] = $detalle->cantidad;
			$item["valor"] = $detalle->unitario;
			$item["descripcion"] = $detalle->concepto." ".$detalle->descripcion." IVA ".$detalle->ivaperc."%";
			
			if((int)$detalle->ivaperc != 0)
			{
				$total_taxes_amount += $item["cantidad"]*$item["valor"];
			}
			if((int)$detalle->ivaperc == 19)
			{
				$total_taxes_amount = $item["cantidad"]*$item["valor"];
				$tax_19["TaxBase"] +=  $item["cantidad"]*$item["valor"];
				$tax_19["TaxValue"] += ($item["cantidad"]*$item["valor"])*0.19;
			
				$item["valor_iva"] = ($item["cantidad"]*$item["valor"])*0.19;			
				$item["iva_perc"] = "19.00";
			}
			if((int)$detalle->ivaperc == 16)
			{
				$total_taxes_amount = $item["cantidad"]*$item["valor"];
				$tax_16["TaxBase"] +=  $item["cantidad"]*$item["valor"];
				$tax_16["TaxValue"] += ($item["cantidad"]*$item["valor"])*0.16;
			
				$item["valor_iva"] = ($item["cantidad"]*$item["valor"])*0.16;			
				$item["iva_perc"] = "16.00";
			}
			if((int)$detalle->ivaperc == 0)
			{
				$item["valor_iva"] = 0; 			
				$item["iva_perc"] =	"0.00";				
			}			 
			
			array_push($items,$item);
		}
		
		//print_r($tax_19);
		
		//print_r($tax_16);
		
		if($tax_19["TaxValue"]>0)
		{
			array_push($array_of_taxes,$tax_19);
		}
	
		if($tax_16["TaxValue"]>0)
		{
			array_push($array_of_taxes,$tax_16);
		}
		
		if($tax_19["TaxValue"] == 0 and $tax_16["TaxValue"] == 0)
		{
			array_push($array_of_taxes,$tax_0);			
		}

		$this->fact_obj->set_factItems($items);

		$this->fact_obj->set_TaxTotal($total_taxes_amount);		
		
		//print_r($array_of_taxes);
		
		$this->fact_obj->set_tax_subtotals($array_of_taxes);

		
		$xml_g = utf8_decode(Json2xml::generate_xml($this->fact_obj->json_object));
		
		return $xml_g;

	}

	public function test_json()
	{
		
		$this->fact_obj->consecutive();
		$this->fact_obj->set_InformacionDeDocumento();
		$this->fact_obj->set_tipo_identificacion();
		$this->fact_obj->set_InformacionDeAdquiriente();
		$this->fact_obj->set_InformacionCliente();
		$this->fact_obj->set_TaxTotal();
		$this->fact_obj->set_tax_subtotals();
		$this->fact_obj->set_LegalMonetaryTotal();
		$this->fact_obj->set_factItems();
		
		//print_r($this->fact_obj->json_object);
		
		return $this->fact_obj->json_object;
		
	}
	
	
}











?>