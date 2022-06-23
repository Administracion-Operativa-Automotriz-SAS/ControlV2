<?php 
//echo "json que sirve de base para el xml";




class json_fact_electronica_taxes
{

	function __construct(){
		
		$this->xmlstr = '{
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
															"Valor": ""
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
															"Valor": ""
														},
														{
															"Identificador": "AR-02",
															"Valor": ""
														},
														{
															"Identificador": "AR-03",
															"Valor": ""
														},
														{
															"Identificador": "AR-04",
															"Valor": ""
														},
														{
															"Identificador": "AR-05",
															"Valor": ""
														},
														{
															"Identificador": "AR-06",
															"Valor": ""
														},
														{
															"Identificador": "AR-07",
															"Valor": ""
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
															"Valor": ""
														},
														{
															"Identificador": "AR-11",
															"Valor": ""
														},
														{
															"Identificador": "AR-12",
															"Valor": ""
														},
														{
															"Identificador": "AR-13",
															"Valor": ""
														},
														{
															"Identificador": "AR-14",
															"Valor": ""
														},
														{
															"Identificador": "AR-15",
															"Valor": ""
														},
														{
															"Identificador": "AR-16",
															"Valor": ""
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
															"Valor": ""
														},
														{
															"Identificador": "AC-02",
															"Valor": ""
														},
														{
															"Identificador": "AC-03",
															"Valor": ""
														},
														{
															"Identificador": "AC-04",
															"Valor": ""
														},
														{
															"Identificador": "AC-05",
															"Valor": ""
														},
														{
															"Identificador": "AC-06",
															"Valor": ""
														},
														{
															"Identificador": "AC-07",
															"Valor": ""
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
															"Valor": ""
														},
														{
															"Identificador": "TextoResolDian",
															"Valor": ""
														},
														{
															"Identificador": "TextoEstatusOBL",
															"Valor": "ACTIVIDADES ECONOMICAS No. 7710 y 7720 GRANDES CONTRIBUYENTES-RESOLUCION 000076 1 DIC 2016 SOMOS GRANDES CONTRIBUYENTES IMPUESTOS DISTRITALES BOGOTA segun Resolucion No. DDI-010761 SHD del 30 de Marzo de 2016"
														},
														{
															"Identificador": "VlrEnLetras",
															"Valor": ""
														},
														{
															"Identificador": "OrdCompra",
															"Valor": ""
														},
														{
														  "Identificador": "ObservNotes",
														  "Valor": ""
														}
													]
												}
											}
										}
									}
								},
								"cbc:UBLVersionID": "UBL 2.0",
								"cbc:ProfileID": "DIAN 1.0",
								"cbc:ID": "FE1",
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
											"cbc:TaxLevelCode": "0",
											"cac:TaxScheme": {}
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
												"#text": ""
											}
										},
										"cac:PartyName": {
											"cbc:Name": ""
										},
										"fe:PhysicalLocation": {
											"fe:Address": {
												"cbc:Department": "",
												"cbc:CitySubdivisionName": "",
												"cbc:CityName": "",
												"cac:AddressLine": {
													"cbc:Line": ""
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
											"cbc:TaxLevelCode": "0",
											"cac:TaxScheme": {}
										},
										"fe:PartyLegalEntity": {
											"cbc:RegistrationName": ""
										},
										"cac:Contact": {
											"cbc:Telephone": ""
										},
										"fe:Person": {
											"cbc:FirstName": "",
											"cbc:FamilyName": ""
										}
									}
								},
								"fe:TaxTotal": {
									"cbc:TaxAmount": {
										"-currencyID": "COP",
										"#text": "0"
									},
									"cbc:TaxEvidenceIndicator": "false",
									"fe:TaxSubtotal": []
								},
								"fe:LegalMonetaryTotal": {
									"cbc:LineExtensionAmount": {
										"-currencyID": "COP",
										"#text": "0"
									},
									"cbc:TaxExclusiveAmount": {
										"-currencyID": "COP",
										"#text": "0"
									},
									"cbc:AllowanceTotalAmount": {
										"-currencyID": "COP",
										"#text": "0"
									},
									"cbc:PayableAmount": {
										"-currencyID": "COP",
										"#text": "36700.00"
									}
								},
								"fe:InvoiceLine": [
								]
							}
						}';
		
						
		$this->json_object = json_decode($this->xmlstr);
		
		$this->fact_item_string = '{
										"cbc:ID": "1",
										"cbc:InvoicedQuantity": "2.0",
										"cbc:LineExtensionAmount": {
											"-currencyID": "COP",
											"#text": "3432.64"
										},
										"cac:TaxTotal":{
											"cbc:TaxAmount": {
												"-currencyID": "COP",
												"#text": "0"
											},
											"cbc:TaxEvidenceIndicator": "false",
											"cac:TaxSubtotal": []
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
									
		$this->fact_item = json_decode($this->fact_item_string);
		
		$this->fact_tax_string = '{
										"cbc:TaxableAmount": {
											"-currencyID": "COP",
											"#text": "0"
										},
										"cbc:TaxAmount": {
											"-currencyID": "COP",
											"#text": "0"
										},
										"cbc:Percent": "0.00",
										"cac:TaxCategory": {
											"cac:TaxScheme": {
												"cbc:ID": {
													"-schemeURI": "http://www.dian.gov.co",
													"#text": "01"
												}
											}
										}
									}';							
									
		
		$this->fact_tax = json_decode($this->fact_tax_string);
		
		$this->fe_Invoice_key = "fe:Invoice";
	
		$this->UBLExtensions_key = "ext:UBLExtensions";
		
		$this->UBLExtension_key = "ext:UBLExtension";
		
		$this->ExtensionContent_key = "ext:ExtensionContent";		
		
		$this->PtesaExtensions_key = "PtesaExtensions:PtesaExtensions";
		
		//--variables para tipo de documento
		
		$this->schemeName = "-schemeName";
		
		$this->schemeID =  "-schemeID";
		
		$this->schemeAgencyID = "-schemeAgencyID";
		
		$this->schemeAgencyName = "-schemeAgencyName";
		
		
		
		//
		
		$this->AccountingCustomerParty_key = "fe:AccountingCustomerParty";
		
		$this->fe_Party_id = "fe:Party";
		
		$this->cac_PartyName_id = "cac:PartyName";
		
		$this->cac_PartyIdentification = "cac:PartyIdentification";		
		
		$this->cbc_Name_id = "cbc:Name";
		
		$this->fe_PhysicalLocation = "fe:PhysicalLocation";
		
		$this->fe_Address = "fe:Address";
		
		$this->cbc_Department = "cbc:Department";
		
		$this->cbc_CitySubdivisionName = "cbc:CitySubdivisionName";
		
		$this->cbc_CityName = "cbc:CityName";
		
		$this->cac_AddressLine = "cac:AddressLine";
		
		$this->cbc_Line = "cbc:Line";
		
		$this->fe_PartyLegalEntity = "fe:PartyLegalEntity";
		
		$this->cbc_RegistrationName = "cbc:RegistrationName";
		
		$this->cac_Contact = "cac:Contact";
		
		$this->cbc_Telephone = "cbc:Telephone";
		
		$this->fe_Person = "fe:Person";
		
		$this->cbc_FirstName = "cbc:FirstName";
		
		$this->cbc_FamilyName = "cbc:FamilyName";
		
		$this->fe_TaxTotal = "fe:TaxTotal";
		
		$this->cbc_TaxAmount = "cbc:TaxAmount";
		
		$this->innerText = "#text";
		
		$this->fe_TaxSubtotal = "fe:TaxSubtotal";
		
		$this->cbc_TaxableAmount = "cbc:TaxableAmount";
		
		$this->fe_LegalMonetaryTotal = "fe:LegalMonetaryTotal";
		
		$this->cbc_LineExtensionAmount = "cbc:LineExtensionAmount";
		
		$this->cbc_TaxExclusiveAmount = "cbc:TaxExclusiveAmount";
		
		$this->cbc_AllowanceTotalAmount = "cbc:AllowanceTotalAmount";
		
		$this->cbc_PayableAmount = "cbc:PayableAmount";
		
		$this->fe_InvoiceLine = "fe:InvoiceLine";
		
		$this->cbc_ID = "cbc:ID";
		
		$this->cbc_InvoicedQuantity = "cbc:InvoicedQuantity";
		
		$this->fe_Item = "fe:Item";
		
		$this->cbc_Description = "cbc:Description";
		
		$this->fe_Price = "fe:Price";
		
		$this->cbc_PriceAmount = "cbc:PriceAmount";
		
		
		//
		
		
		$this->cbc_IssueDate = "cbc:IssueDate";
		
		$this->cbc_IssueTime = "cbc:IssueTime";
		
		$this->cbc_Percent = "cbc:Percent";
		
		$this->cac_TaxTotal = "cac:TaxTotal";
		
		$this->cac_TaxSubtotal = "cac:TaxSubtotal";
		
	}
	
	public function consecutive($consecutive="FE13")
	{
		$this->json_object->{$this->fe_Invoice_key}->{$this->cbc_ID} = $consecutive;
	}
	
	public function set_InformacionDeDocumento($FechaVenc="2018/07/06",$VlrEnLetras="SON: TREINTA Y CINCO MIL PESOS MCTE CON CERO CENTAVOS",$OrdCompra="PA-SRP0000000006",$Resolucion_factura="Autorización Numeración de Facturación No.12354417 Factura por Computador de junio 21 de 2017 Prefijo FV. Rango 22351 al 30122 Vigencia por 12 Meses",$Observaciones="")
	{		
		
		$this->json_object->{$this->fe_Invoice_key}->{$this->cbc_IssueDate} = date('Y-m-d');
		$this->json_object->{$this->fe_Invoice_key}->{$this->cbc_IssueTime} = date('H:i:s');
		
		//0 fechavenc
		//3 valor en letras
		//4 orden compra , se pone el siniestro
		
		
		$OBL = "ACTIVIDADES ECONOMICAS No. 7710 y 7720 GRANDES CONTRIBUYENTES DIAN RESOLUCIÓN 012635 14 DIC 2018, GRANDES CONTRIBUYENTES IMPUESTOS DISTRITALES BOGOTA RESOLUCION DDI-01076 SHD 30 MAR 2016"; 
		
		$this->json_object->{$this->fe_Invoice_key}->{$this->UBLExtensions_key}->{$this->UBLExtension_key}->{$this->ExtensionContent_key}->{$this->PtesaExtensions_key}->InformacionDeDocumento->Informacion[0]->Valor = $FechaVenc;
		$this->json_object->{$this->fe_Invoice_key}->{$this->UBLExtensions_key}->{$this->UBLExtension_key}->{$this->ExtensionContent_key}->{$this->PtesaExtensions_key}->InformacionDeDocumento->Informacion[1]->Valor = $Resolucion_factura;
		$this->json_object->{$this->fe_Invoice_key}->{$this->UBLExtensions_key}->{$this->UBLExtension_key}->{$this->ExtensionContent_key}->{$this->PtesaExtensions_key}->InformacionDeDocumento->Informacion[2]->Valor = $OBL;
		$this->json_object->{$this->fe_Invoice_key}->{$this->UBLExtensions_key}->{$this->UBLExtension_key}->{$this->ExtensionContent_key}->{$this->PtesaExtensions_key}->InformacionDeDocumento->Informacion[3]->Valor = $VlrEnLetras;
		$this->json_object->{$this->fe_Invoice_key}->{$this->UBLExtensions_key}->{$this->UBLExtension_key}->{$this->ExtensionContent_key}->{$this->PtesaExtensions_key}->InformacionDeDocumento->Informacion[4]->Valor = $OrdCompra;
		$this->json_object->{$this->fe_Invoice_key}->{$this->UBLExtensions_key}->{$this->UBLExtension_key}->{$this->ExtensionContent_key}->{$this->PtesaExtensions_key}->InformacionDeDocumento->Informacion[5]->Valor = strtoupper($Observaciones);	
	
	}
	
	public function set_InformacionDeAdquiriente($EmailAdq="sergiocastillo@aoacolombia.com",$AplicaRUT = 0,$AplicaCC = 0)
	{	
		//0 --> Email
		//2 --> Rut?
		//3 --> CC? 
		
		$this->json_object->{$this->fe_Invoice_key}->{$this->UBLExtensions_key}->{$this->UBLExtension_key}->{$this->ExtensionContent_key}->{$this->PtesaExtensions_key}->InformacionDeAdquiriente->Informacion[0]->Valor = $EmailAdq;
		$this->json_object->{$this->fe_Invoice_key}->{$this->UBLExtensions_key}->{$this->UBLExtension_key}->{$this->ExtensionContent_key}->{$this->PtesaExtensions_key}->InformacionDeAdquiriente->Informacion[2]->Valor = $AplicaRUT;
		$this->json_object->{$this->fe_Invoice_key}->{$this->UBLExtensions_key}->{$this->UBLExtension_key}->{$this->ExtensionContent_key}->{$this->PtesaExtensions_key}->InformacionDeAdquiriente->Informacion[2]->Valor = $AplicaCC;
		
		
		
	}
	
	public function set_InformacionRUT()
	{
		
	}
	
	public function set_InformacionCamaraComercio()
	{
		
	}
	
	public function set_InformacionCliente($FirstName="Pepito",$FamilyName="Perez",$Department="Bogota D.C.",$CitySubdivisionName="CHICO",$cbc_CityName="Bogotá D.C.",$AddressLine="Cra X # XX - XX",$Telephone="(+571) 88 88 888",$PartyIdentification="12345678",$RegistrationName="Soluciones Pepito S.A.S.")
	{
		$Name = $FirstName." ".$FamilyName;
		// name nombre cliente
		// cbc_Deparment departamento del cliente
		// CitySubdivisionName nombre del barrio del cliente
		// CityName nombre de la ciudad del cliente
		// AddressLine dirección del cliente
		// RegistrationName nombre de razón social si tiene
		// Firstname primer nombre del cliente.
		//Familyname primer apellido del cliente.
		//
		
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->cac_PartyIdentification}->{$this->cbc_ID}->{$this->innerText} = $PartyIdentification;
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->cac_PartyName_id}->{$this->cbc_Name_id} = $Name;
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->fe_PhysicalLocation}->{$this->fe_Address}->{$this->cbc_Department} = $Department;
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->fe_PhysicalLocation}->{$this->fe_Address}->{$this->cbc_CitySubdivisionName} = $CitySubdivisionName;
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->fe_PhysicalLocation}->{$this->fe_Address}->{$this->cbc_CityName} = $cbc_CityName;
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->fe_PhysicalLocation}->{$this->fe_Address}->{$this->cac_AddressLine}->{$this->cbc_Line} = $AddressLine;
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->fe_PartyLegalEntity}->{$this->cbc_RegistrationName} = $RegistrationName;
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->cac_Contact}->{$this->cbc_Telephone} = $Telephone;
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->fe_Person}->{$this->cbc_FirstName} = $FirstName;
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->fe_Person}->{$this->cbc_FamilyName} = $FamilyName;
		
	}
	
	public function set_TaxTotal($TaxAmount="40000")
	{
		//TaxAmount total del iva cuando hallan otros impuestos sumar aca el total global del iva con otros impuestos
		
		$this->json_object->{$this->fe_Invoice_key}->{$this->fe_TaxTotal}->{$this->cbc_TaxAmount}->{$this->innerText} = number_format((float)$TaxAmount, 2, '.', '');
	}
	
	public function set_tax_subtotals($array_of_taxes=array(array("TaxBase"=>"10000","TaxValue"=>"1900","TaxPercent"=>"19.00"),array("TaxBase"=>"10000","TaxValue"=>"1600","TaxPercent"=>"16.00")))
	{
		
		//$TaxableAmount base para calcular el iva
		//$TaxAmount; en este caso solo el iva
		
		$array_objects = array();
		$count = 0;
		foreach($array_of_taxes as $tax)
		{
			$count++;			
			$new_tax = json_decode($this->fact_tax_string);
			
			$new_tax->{$this->cbc_TaxableAmount}->{$this->innerText} = number_format((float)$tax["TaxBase"], 2, '.', ''); 
			$new_tax->{$this->cbc_TaxAmount}->{$this->innerText} =	number_format((float)$tax["TaxValue"], 2, '.', '');		
			$new_tax->{$this->cbc_Percent} = $tax["TaxPercent"];
			
			array_push($array_objects,$new_tax);
		}

		$this->json_object->{$this->fe_Invoice_key}->{$this->fe_TaxTotal}->{$this->fe_TaxSubtotal} = $array_objects;
	}
	
	
	
	//Totales de la factura
	public function set_LegalMonetaryTotal($LineExtensionAmount="10000",$TaxExclusiveAmount="20000",$AllowanceTotalAmount="10000")
	{
		//LineExtensionAmount total de todos los items de la factura
		//$TaxExclusiveAmount total de los impuestos de la factura
		//AllowanceTotalAmount total de los descuentos
		//PayableAmount total valores - descuentos
		
		$this->json_object->{$this->fe_Invoice_key}->{$this->fe_LegalMonetaryTotal}->{$this->cbc_LineExtensionAmount}->{$this->innerText} = number_format((float)$LineExtensionAmount, 2, '.', '');
		$this->json_object->{$this->fe_Invoice_key}->{$this->fe_LegalMonetaryTotal}->{$this->cbc_TaxExclusiveAmount}->{$this->innerText} = number_format((float)$TaxExclusiveAmount, 2, '.', ''); 
		$this->json_object->{$this->fe_Invoice_key}->{$this->fe_LegalMonetaryTotal}->{$this->cbc_AllowanceTotalAmount}->{$this->innerText} = number_format((float)$AllowanceTotalAmount, 2, '.', '');
		$this->json_object->{$this->fe_Invoice_key}->{$this->fe_LegalMonetaryTotal}->{$this->cbc_PayableAmount}->{$this->innerText} = number_format((float)round(($LineExtensionAmount + $TaxExclusiveAmount)-$AllowanceTotalAmount), 2, '.', '');
		
	}
	
	public function set_factItems($array_of_items=array(array("cantidad"=>2,"valor"=>"35000.00","descripcion"=>"item no.1","valor_iva"=>"2520.00","iva_perc"=>"19.00"),
	                                                    array("cantidad"=>3,"valor"=>"20000","descripcion"=>"item no.2","valor_iva"=>"0.00","iva_perc"=>"0.00")))
	{
	
		//El valor debe ser el valor unitario 
		
		$array_objects = array();
		$count = 0;
		foreach($array_of_items as $item)
		{
			//print_r($item);
			
			$count++;
			
			$new_item = json_decode($this->fact_item_string);
			
			//print_r($this->fact_item);
			
			$new_item->{$this->cbc_ID} = $count;

			$new_item->{$this->cbc_InvoicedQuantity} = $item["cantidad"];
			
			
			
			//START valor del impuesto 
			$new_item->{$this->cac_TaxTotal}->{$this->cbc_TaxAmount}->{$this->innerText} = $item["valor_iva"];
			
			$array_taxes = array();
			
			$new_tax = json_decode($this->fact_tax_string);
			
			$new_tax->{$this->cbc_TaxableAmount}->{$this->innerText} = number_format((float)($item["cantidad"]*$item["valor"]), 2, '.', '');
			$new_tax->{$this->cbc_TaxAmount}->{$this->innerText} =	$item["valor_iva"];		
			$new_tax->{$this->cbc_Percent} = $item["iva_perc"];
			
			array_push($array_taxes,$new_tax);
			
			$new_item->{$this->cac_TaxTotal}->{$this->cac_TaxSubtotal} = $array_taxes;
			//END valor del impuesto	
			
			$new_item->{$this->cbc_LineExtensionAmount}->{$this->innerText} = number_format((float)($item["cantidad"]*$item["valor"]), 2, '.', ''); 			
		
			$new_item->{$this->fe_Item}->{$this->cbc_Description} =  utf8_decode($item["descripcion"]);
			
			$new_item->{$this->fe_Price}->{$this->cbc_PriceAmount}->{$this->innerText} = number_format((float)$item["valor"], 2, '.', ''); 
			
			//print_r($new_item);
			
			array_push($array_objects,$new_item);	
		}
		
		//print_r($array_objects);
		
		$this->json_object->{$this->fe_Invoice_key}->{$this->fe_InvoiceLine} = $array_objects;
	}
	
	//Nuevas implementaciones para tipo documento
	
	public function set_tipo_identificacion($Idscheme = "13",$tipoDeDocumento = "CC", $schemeAgencyID = "195", $schemeAgencyName = "CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)"){
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->cac_PartyIdentification}->{$this->cbc_ID}->{$this->schemeName} = $tipoDeDocumento;
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->cac_PartyIdentification}->{$this->cbc_ID}->{$this->schemeID} = $Idscheme;
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->cac_PartyIdentification}->{$this->cbc_ID}->{$this->schemeAgencyID} = $schemeAgencyID;
		$this->json_object->{$this->fe_Invoice_key}->{$this->AccountingCustomerParty_key}->{$this->fe_Party_id}->{$this->cac_PartyIdentification}->{$this->cbc_ID}->{$this->schemeAgencyName} = $schemeAgencyName;
	}
	
	
	
}







