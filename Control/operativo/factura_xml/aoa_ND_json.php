<?php

//json para nota credito


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE | E_NOTICE);


class json_nota_debito
{  
	function __construct()
	{	  
		
		
		$this->nota_debito_json = '{
	"fe:DebitNote": {
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
									"Valor": "leonardo.castro@ptesa.com"
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
									"Valor": "2018-07-06"
								},
								{
									"Identificador": "TextoResolDian",
									"Valor": ""
								},
								{
									"Identificador": "TextoEstatusOBL",
									"Valor": "ACTIVIDADES ECONOMICAS No. 7710 y 7720 GRANDES CONTRIBUYENTES-RESOLUCION 000076 1 DIC 2016 SOMOS GRANDES CONTRIBUYENTES IMPUESTOS DISTRITALES BOGOTA segun Resolucion No. DDI-010761 SHD del 30 de Marzo de 2016 "
								},
								{
									"Identificador": "VlrEnLetras",
									"Valor": "SON: TREINTA Y CINCO MIL PESOS MCTE CON CERO CENTAVOS"
								},
								{
									"Identificador": "OrdCompra",
									"Valor": "PA-SRP0000000005"
								},
								{
									"Identificador": "ObservNotes",
									"Valor": "."
								}
							]
						}
					}
				}
			}
		},
		"cbc:UBLVersionID": "UBL 2.0",
		"cbc:ProfileID": "DIAN 1.0",
		"cbc:ID": "NC3",
		"cbc:IssueDate": "2018-08-01",
		"cbc:IssueTime": "17:05:05",
		"cbc:DocumentCurrencyCode": "COP",
		"cac:DiscrepancyResponse": {
			"cbc:ReferenceID":{},
			"cbc:ResponseCode": {
				"-listName": "conceptos de notas crédito",
				"-listSchemeURI": "http://www.dian.gov.co/micrositios/fac_electronica/documentos/Anexo_Tecnico_001_Formatos_de_los_Documentos_XML_de_Facturacion_Electron.pdf",
				"#text": "1"
			}
		},
		"cac:BillingReference": {
			"cac:InvoiceDocumentReference": {
				"cbc:ID": {
					"-schemeName": "número de la factura a anular",
					"#text": "FE40"
				}
			}
		},
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
						"#text": "12312415184123"
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
				"#text": "0.00"
			},
			"cbc:TaxEvidenceIndicator": "false",
			"fe:TaxSubtotal": [{
					"cbc:TaxableAmount": {
						"-currencyID": "COP",
						"#text": "0.00"
					},
					"cbc:TaxAmount": {
						"-currencyID": "COP",
						"#text": "0.00"
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
						"#text": "0.00"
					},
					"cbc:TaxAmount": {
						"-currencyID": "COP",
						"#text": "0.00"
					},
					"cbc:Percent": "16.00",
					"cac:TaxCategory": {
						"cac:TaxScheme": {
							"cbc:ID": "01"
						}
					}
				}
			]
		},
		"fe:LegalMonetaryTotal": {
			"cbc:LineExtensionAmount": {
				"-currencyID": "COP",
				"#text": "0.00"
			},
			"cbc:TaxExclusiveAmount": {
				"-currencyID": "COP",
				"#text": "0.00"
			},
			"cbc:AllowanceTotalAmount": {
				"-currencyID": "COP",
				"#text": "0.00"
			},
			"cbc:PayableAmount": {
				"-currencyID": "COP",
				"#text": "0.00"
			}
		},
		"cac:DebitNoteLine": [
		]
	}
}';
							
		$this->nd_object = json_decode($this->nota_debito_json);
		$this->fe_DebitNote = "fe:DebitNote";
		$this->ext_UBLExtensions = "ext:UBLExtensions";		
		$this->ext_UBLExtension = "ext:UBLExtension";		
		$this->ext_ExtensionContent = "ext:ExtensionContent";		
		$this->PtesaExtensions_PtesaExtensions = "PtesaExtensions:PtesaExtensions";
		
		//Anulación de Factura
		$this->cac_BillingReference = "cac:BillingReference";
		$this->cac_InvoiceDocumentReference = "cac:InvoiceDocumentReference";
		$this->cbc_ID = "cbc:ID";
		$this->text = "#text";
		
		
		//Información del cliente
		
		$this->fe_AccountingCustomerParty = "fe:AccountingCustomerParty";
		$this->fe_Party = "fe:Party";
		$this->cac_PartyIdentification = "cac:PartyIdentification";		
		$this->cac_PartyName = "cac:PartyName";	
		$this->cbc_Name = "cbc:Name";
		
		
		$this->fe_PhysicalLocation = "fe:PhysicalLocation";
		$this->fe_Address = "fe:Address";
		$this->cbc_Department = "cbc:Department";
		$this->cbc_CitySubdivisionName = "cbc:CitySubdivisionName";
		$this->cbc_CityName = "cbc:CityName";
		$this->cac_AddressLine = "cac:AddressLine";
		$this->cbc_Line = "cbc:Line";
		
		
		$this->cac_Contact = "cac:Contact";
		$this->cbc_Telephone = "cbc:Telephone";
		
		
		$this->fe_Person = "fe:Person";
		$this->cbc_FirstName = "cbc:FirstName";
		$this->cbc_FamilyName = "cbc:FamilyName";
		
		
		$this->fe_PartyLegalEntity = "fe:PartyLegalEntity";
		$this->cbc_RegistrationName = "cbc:RegistrationName";
		
		
		//LUGAR DE IMPUESTOS
		$this->fe_TaxTotal = "fe:TaxTotal";
		
		$this->cbc_TaxAmount = "cbc:TaxAmount";
		
		$this->innerText = "#text";
		
		$this->fe_TaxSubtotal = "fe:TaxSubtotal";
		
		$this->cbc_TaxableAmount = "cbc:TaxableAmount";
		
		
		//Total de impuestos
		$this->fe_LegalMonetaryTotal = "fe:LegalMonetaryTotal";
		
		$this->cbc_LineExtensionAmount = "cbc:LineExtensionAmount";
		
		$this->cbc_TaxExclusiveAmount = "cbc:TaxExclusiveAmount";
		
		$this->cbc_AllowanceTotalAmount = "cbc:AllowanceTotalAmount";
		
		$this->cbc_PayableAmount = "cbc:PayableAmount";
		
		
		//Items para NC
		
		$this->ND_item_string = '{
			"cbc:ID": "1",
			"cbc:DebitedQuantity": "2.0",
			"cbc:LineExtensionAmount": {
				"-currencyID": "COP",
				"#text": "3432.64"
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
		}';
		
		$this->cbc_DebitedQuantity = "cbc:DebitedQuantity";
		
		$this->fe_Item = "fe:Item";
		
		$this->cbc_Description = "cbc:Description";
		
		$this->fe_Price = "fe:Price";
		
		$this->cbc_PriceAmount = "cbc:PriceAmount";
		
		$this->cac_DebitNoteLine = "cac:DebitNoteLine";
		
		
		$this->cbc_IssueDate = "cbc:IssueDate";
		
		$this->cbc_IssueTime = "cbc:IssueTime";
		
		
	}
	
	
	public function set_InformacionDeAdquiriente($EmailAdq="sergiocastillo@aoacolombia.com",$AplicaRUT = 0,$AplicaCC = 0)
	{	
		//0 --> Email
		//2 --> Rut?
		//3 --> CC? 
		
		$this->nd_object->{$this->fe_DebitNote}->{$this->ext_UBLExtensions}->{$this->ext_UBLExtension}->{$this->ext_ExtensionContent}->{$this->PtesaExtensions_PtesaExtensions}->InformacionDeAdquiriente->Informacion[0]->Valor = $EmailAdq;
		$this->nd_object->{$this->fe_DebitNote}->{$this->ext_UBLExtensions}->{$this->ext_UBLExtension}->{$this->ext_ExtensionContent}->{$this->PtesaExtensions_PtesaExtensions}->InformacionDeAdquiriente->Informacion[2]->Valor = $AplicaRUT;
		$this->nd_object->{$this->fe_DebitNote}->{$this->ext_UBLExtensions}->{$this->ext_UBLExtension}->{$this->ext_ExtensionContent}->{$this->PtesaExtensions_PtesaExtensions}->InformacionDeAdquiriente->Informacion[2]->Valor = $AplicaCC;
		
	}
	
	public function set_InformacionDeDocumento($FechaVenc="2018/07/06",$VlrEnLetras="SON: TREINTA Y CINCO MIL PESOS MCTE CON CERO CENTAVOS",$OrdCompra="N.A",$Resolucion_factura="Res. de fact")
	{
		//0 fechavenc
		//3 valor en letras
		//4 orden compra , se pone el siniestro
		
		$this->nd_object->{$this->fe_DebitNote}->{$this->ext_UBLExtensions}->{$this->ext_UBLExtension}->{$this->ext_ExtensionContent}->{$this->PtesaExtensions_PtesaExtensions}->InformacionDeDocumento->Informacion[0]->Valor = $FechaVenc;
		$this->nd_object->{$this->fe_DebitNote}->{$this->ext_UBLExtensions}->{$this->ext_UBLExtension}->{$this->ext_ExtensionContent}->{$this->PtesaExtensions_PtesaExtensions}->InformacionDeDocumento->Informacion[1]->Valor = $Resolucion_factura;
		$this->nd_object->{$this->fe_DebitNote}->{$this->ext_UBLExtensions}->{$this->ext_UBLExtension}->{$this->ext_ExtensionContent}->{$this->PtesaExtensions_PtesaExtensions}->InformacionDeDocumento->Informacion[3]->Valor = $VlrEnLetras;
		$this->nd_object->{$this->fe_DebitNote}->{$this->ext_UBLExtensions}->{$this->ext_UBLExtension}->{$this->ext_ExtensionContent}->{$this->PtesaExtensions_PtesaExtensions}->InformacionDeDocumento->Informacion[4]->Valor = $OrdCompra;			
	
	}
	
	public function set_Doc_anulacion($fact_to_null = "FE01")
	{
		$this->nd_object->{$this->fe_DebitNote}->{$this->cac_BillingReference}->{$this->cac_InvoiceDocumentReference}->{$this->cbc_ID}->{$this->text} = $fact_to_null;
	}
	
	public function set_Consecutive($consecutive = "NC1")
	{
		$this->nd_object->{$this->fe_DebitNote}->{$this->cbc_ID} = $consecutive;
		$this->nd_object->{$this->fe_DebitNote}->{$this->cbc_IssueDate} = date('Y-m-d');
		$this->nd_object->{$this->fe_DebitNote}->{$this->cbc_IssueTime} = date('H:i:s');
	}
	
	public function set_Info_Cliente($FirstName ="SERGIO", $FamilyName = "CASTILLO" ,$Deparment = "Bogota D.C.", $neighborhood = "CHICO", $City = "Bogotá D.C.",$Address = "Cra X # XX - XX",$Telephone = "(+571) 88 88 888",$PartyIdentification="12345678",$RegistrationName="Pepito SAS")
	{
		
		$customer_name = $FirstName." ".$FamilyName;
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_AccountingCustomerParty}->{$this->fe_Party}->{$this->cac_PartyName}->{$this->cbc_Name} =  $customer_name;
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_AccountingCustomerParty}->{$this->fe_Party}->{$this->fe_PhysicalLocation}->{$this->fe_Address}->{$this->cbc_Department} = $Deparment;
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_AccountingCustomerParty}->{$this->fe_Party}->{$this->fe_PhysicalLocation}->{$this->fe_Address}->{$this->cbc_CitySubdivisionName} = $neighborhood;
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_AccountingCustomerParty}->{$this->fe_Party}->{$this->fe_PhysicalLocation}->{$this->fe_Address}->{$this->cbc_CityName} = $City; 
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_AccountingCustomerParty}->{$this->fe_Party}->{$this->fe_PhysicalLocation}->{$this->fe_Address}->{$this->cac_AddressLine}->{$this->cbc_Line} = $Address;
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_AccountingCustomerParty}->{$this->fe_Party}->{$this->cac_Contact}->{$this->cbc_Telephone} = $Telephone;
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_AccountingCustomerParty}->{$this->fe_Party}->{$this->fe_Person}->{$this->cbc_FirstName} = $FirstName;
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_AccountingCustomerParty}->{$this->fe_Party}->{$this->fe_Person}->{$this->cbc_FamilyName} = $FamilyName;		
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_AccountingCustomerParty}->{$this->fe_Party}->{$this->cac_PartyIdentification}->{$this->cbc_ID}->{$this->innerText} = $PartyIdentification;		
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_AccountingCustomerParty}->{$this->fe_Party}->{$this->fe_PartyLegalEntity}->{$this->cbc_RegistrationName} = $RegistrationName;
	
	}
	
	public function set_TaxTotal($TaxAmount="40000",$TaxableAmount="50000")
	{
		//TaxAmount total del iva cuando hallan otros impuestos sumar aca el total global del iva con otros impuestos
		//$TaxableAmount base para calcular el iva
		//$TaxAmount; en este caso solo el iva
		
		//Esto es un arreglo para mandar los diferentes impuestos
		
		//echo "Taxable amount ".$TaxableAmount;
		
		//echo "prev valor ".$this->nd_object->{$this->fe_DebitNote}->{$this->fe_TaxTotal}->{$this->fe_TaxSubtotal}[0]->{$this->cbc_TaxableAmount}->{$this->innerText};
		
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_TaxTotal}->{$this->cbc_TaxAmount}->{$this->innerText} = number_format((float)$TaxAmount, 2, '.', '');
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_TaxTotal}->{$this->fe_TaxSubtotal}[0]->{$this->cbc_TaxableAmount}->{$this->innerText} = number_format((float)$TaxableAmount, 2, '.', '');
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_TaxTotal}->{$this->fe_TaxSubtotal}[0]->{$this->cbc_TaxAmount}->{$this->innerText} = number_format((float)$TaxAmount, 2, '.', ''); 
	}
	
	public function set_LegalMonetaryTotal($LineExtensionAmount="10000",$TaxExclusiveAmount="20000",$AllowanceTotalAmount="10000")
	{
		//LineExtensionAmount total de todos los items de la factura
		//$TaxExclusiveAmount total de los impuestos de la factura
		//AllowanceTotalAmount total de los descuentos
		//PayableAmount total valores - descuentos
		
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_LegalMonetaryTotal}->{$this->cbc_LineExtensionAmount}->{$this->innerText} = number_format((float)$LineExtensionAmount, 2, '.', '');
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_LegalMonetaryTotal}->{$this->cbc_TaxExclusiveAmount}->{$this->innerText} = number_format((float)$TaxExclusiveAmount, 2, '.', ''); 
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_LegalMonetaryTotal}->{$this->cbc_AllowanceTotalAmount}->{$this->innerText} = number_format((float)$AllowanceTotalAmount, 2, '.', '');
		$this->nd_object->{$this->fe_DebitNote}->{$this->fe_LegalMonetaryTotal}->{$this->cbc_PayableAmount}->{$this->innerText} = number_format((float)(($LineExtensionAmount + $TaxExclusiveAmount)-$AllowanceTotalAmount), 2, '.', '');
		
	}
	
	
	public function set_ndItems($array_of_items=array(array("cantidad"=>2,"valor"=>"3500","descripcion"=>"item no.1"),array("cantidad"=>3,"valor"=>"2000","descripcion"=>"item no.2")))
	{	
	
		//El valor debe ser el valor unitario 
		
		$array_objects = array();
		$count = 0;
		foreach($array_of_items as $item)
		{
			//print_r($item);
			
			$count++;
			
			$new_item = json_decode($this->ND_item_string);
			
			$new_item->{$this->cbc_ID} = $count;

			$new_item->{$this->cbc_DebitedQuantity} = $item["cantidad"];	
			
			$new_item->{$this->cbc_LineExtensionAmount}->{$this->innerText} = number_format((float)($item["cantidad"]*$item["valor"]), 2, '.', ''); 			
		
			$new_item->{$this->fe_Item}->{$this->cbc_Description} =  utf8_decode($item["descripcion"]);
			
			$new_item->{$this->fe_Price}->{$this->cbc_PriceAmount}->{$this->innerText} = number_format((float)$item["valor"], 2, '.', ''); 
			
			array_push($array_objects,$new_item);	
		}
		
		//print_r($array_objects);
		
		$this->nd_object->{$this->fe_DebitNote}->{$this->cac_DebitNoteLine} = $array_objects;
	}
	
	
}













?>