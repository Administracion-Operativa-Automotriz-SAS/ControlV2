<?php/** * Clase para interfase de medicamentos * * @version $Id$ * @copyright 2009 ARTURO QUINTERO RODRIGUEZ  administracion@intercolombia.com */class Interfase_medicamento{	var $Codigo_producto='0000000000000';	var $Descripcion='                                                  ';	var $Referencia='                              ';	var $Tipo='P';	var $Maximos_y_minimos='N';	var $Lista_precio1='00000000000';	var $Lista_precio2='00000000000';	var $Lista_precio3='00000000000';	var $Lista_precio4='00000000000';	var $Lista_precio5='00000000000';	var $Lista_precio6='00000000000';	var $Lista_precio7='00000000000';	var $Lista_precio8='00000000000';	var $Lista_precio9='00000000000';	var $Lista_precio10='00000000000';	var $Lista_precio11='00000000000';	var $Lista_precio12='00000000000';	var $Codigo_barras='                    ';	var $Porcentaje_iva='0000';	var $Stock_maximo='00000000000000';	var $Stock_minimo='00000000000000';	var $Tiempo_reposicion='000';	var $Ultimo_valor_compra='0000000000000';	var $Tipo_unidad='   ';	var $Codigo_equivalencia='0000000000000';	var $Descripcion_equivalencia='                    ';	var $Marca='                    ';	var $Ubicacion='      ';	var $Ajustable='N';	var $Historia_proveedor1='0000000000000000000000000000000000';	var $Historia_proveedor2='0000000000000000000000000000000000';	var $Historia_proveedor3='0000000000000000000000000000000000';	var $Historia_proveedor4='0000000000000000000000000000000000';	###### SEGUNDA PARTE	var $Nombre_factor1='          ';	var $Operando_factor1=' ';	var $Valor_factor1='0000000000';	var $Nombre_factor2='          ';	var $Operando_factor2=' ';	var $Valor_factor2='0000000000';	var $Nombre_factor3='          ';	var $Operando_factor3=' ';	var $Valor_factor3='0000000000';	var $Nombre_factor4='          ';	var $Operando_factor4=' ';	var $Valor_factor4='0000000000';	var $Nombre_factor5='          ';	var $Operando_factor5=' ';	var $Valor_factor5='0000000000';	var $Costo1='0000000000000';	var $Costo2='0000000000000';	var $Costo3='0000000000000';	var $Costo4='0000000000000';	var $Costo5='0000000000000';	var $Costo6='0000000000000';	var $Costo7='0000000000000';	var $Costo8='0000000000000';	var $Costo9='0000000000000';	var $Costo10='0000000000000';	var $Costo11='0000000000000';	var $Costo12='0000000000000';	var $Costo13='0000000000000';	var $Iva='N';	var $Tarifa='1';	var $Valor_impoconsumo='0000000000000';	var $Clasificacion='N';	var $Factor='N';	var $Porcentaje_impuesto='00000';	var $Porcentaje_impuesto_deporte='00000';	var $Peso='0000000000000';	var $Iva_en_impresion='S';	var $Fin_de_linea="\r\n";	function genera1()	{		$Resultado=$this->Codigo_producto.$this->Descripcion.$this->Referencia.$this->Tipo.$this->Maximos_y_minimos.$this->Lista_precio1.		$this->Lista_precio2.$this->Lista_precio3.$this->Lista_precio4.$this->Lista_precio5.$this->Lista_precio6.$this->Lista_precio7.$this->Lista_precio8.		$this->Lista_precio9.$this->Lista_precio10.$this->Lista_precio11.$this->Lista_precio12;		$Resultado.=$this->Codigo_barras.$this->Porcentaje_iva.$this->Stock_maximo.$this->Stock_minimo.$this->Tiempo_reposicion.$this->Ultimo_valor_compra;		$Resultado.=$this->Tipo_unidad.$this->Codigo_equivalencia.$this->Descripcion_equivalencia.$this->Marca.$this->Ubicacion.$this->Ajustable;		$Resultado.=$this->Historia_proveedor1.$this->Historia_proveedor2.$this->Historia_proveedor3.$this->Historia_proveedor4;		$Resultado.=$this->Fin_de_linea;		return $Resultado;	}	function genera2()	{		$Resultado=$this->Nombre_factor1.$this->Operando_factor1.$this->Valor_factor1;		$Resultado.=$this->Nombre_factor2.$this->Operando_factor2.$this->Valor_factor2;		$Resultado.=$this->Nombre_factor3.$this->Operando_factor3.$this->Valor_factor3;		$Resultado.=$this->Nombre_factor4.$this->Operando_factor4.$this->Valor_factor4;		$Resultado.=$this->Nombre_factor5.$this->Operando_factor5.$this->Valor_factor5;		$Resultado.=$this->Costo1.$this->Costo2.$this->Costo3.$this->Costo4.$this->Costo5.$this->Costo6.$this->Costo7;		$Resultado.=$this->Costo8.$this->Costo9.$this->Costo10.$this->Costo11.$this->Costo12.$this->Costo13;		$Resultado.=$this->Iva.$this->Tarifa.$this->Valor_impoconsumo.$this->Clasificacion.$this->Factor;		$Resultado.=$this->Porcentaje_impuesto.$this->Porcentaje_impuesto_deporte.$this->Peso.$this->Iva_en_impresion;		$Resultado.=$this->Fin_de_linea;		return $Resultado;	}}class Interfase_documento{	var $Tipo_comprobante=' ';	var $Codigo_comprobante='000';	var $Numero_documento='00000000000';	var $Secuencia='00000';	var $Nit='0000000000000';	var $Sucursal='000';	var $Cuenta_contable='0000000000';  // 10 posiciones	var $Codigo_producto='0000000000000'; // 13	var $Fecha_documento='00000000';  // aaaammdd	var $Centro_costos='0000';	var $Subcentro_costos='000';	var $Descripcion='                                                  '; // 50 espacios	var $Afectacion='D'; // D | C	var $Valor='000000000000000'; // 13 enteros 2 decimales	var $Base_retencion='000000000000000';  // 13 enteros 2 decimales	var $Codigo_vendedor='0001';	var $Codigo_ciudad='0001';	var $Codigo_zona='001';	var $Codigo_bodega='0000';	var $Codigo_ubicacion='000';	var $Cantidad='000000000000000'; // 10 enteros 5 decimales	var $Tipo_documento_cruce=' ';	var $Codigo_comprobante_cruce='000';	var $Numero_documento_cruce='00000000000';	var $Secuencia_documento_cruce='000';	var $Fecha_vencimiento_documento_cruce='00000000'; // aaaammdd	var $Codigo_forma_pago='0000';	var $Codigo_banco='00';	var $Tipo_documento_pedido=' ';	var $Codigo_comprobante_pedido='000';	var $Numero_documento_pedido='00000000000';	var $Secuencia_pedido='000';	var $Codigo_moneda='00';	var $Tasa_cambio='000000000000000'; // 8 enteros 7 decimales	var $Valor_en_extranjera='000000000000000'; // 13 enteros 2 decimales	var $Concepto_nomina='000';	var $Cantidad_pago='00000000000'; // 9 enteros 2 decimales	var $Porcentaje_descuento_movimiento='0000'; // 2 enteros 2 decimales	var $Valor_descuento_movimiento='0000000000000'; // 11 enteros 2 decimales	var $Porcentaje_cargo_movimiento='0000'; // 2 enteros 2 decimales	var $Valor_cargo_movimiento='0000000000000'; // 11 enteros 2 decimales	var $Porcentaje_iva_movimiento='0000'; // 11 enteros 2 decimales	var $Valor_iva_movimiento='0000000000000'; // 11 enteros 2 decimales	var $Indicador_nomina='N'; //  S | N	var $Numero_pago='1';	var $Numero_cheque='00000000000';	var $Indicador_tipo_movimiento='N'; //  S | N	var $Nombre_computador='    '; // 4 espacios	var $Estado_comprobante=' ';  // un espacio o A para anulados	var $Derecho_devolucion='  '; // para Ecuador una letra S|N y un espacio otros paises 2 espacios en blanco	var $Credito_tributario='00'; // para Ecuador	var $Numero_comprobante_proveedor='    '; // para Peru 4 espacios	var $Numero_documento_proveedor='00000000000';	var $Prefijo_documento_proveedor='          '; // 10 posiciones alfabeticas	var $Fecha_documento_proveedor='00000000';	var $Precio_unitario_moneda_local='000000000000000000'; // 13 enteros 5 decimales	var $Precio_unitario_moneda_extranjera='000000000000000000'; // 13 enteros 5 decimales	var $Indicador_tipo_movimiento_activo=' '; // A para activos fijos	var $Veces_a_depreciar='000';	var $Secuencia_transaccion='00'; // para Ecuador	var $Autorizacion_imprenta='0000000000'; // para Ecuador y Peru	var $COA='A'; // para Ecuador valores: S|N|A=no aplica	var $Numero_caja='000';	var $Numero_puntos_obtenidos='000000000000000'; // 12 enteros 2 decimales y 1 signo	var $Cantidad_dos='000000000000000'; // 10 enteros 2 decimales	var $Cantidad_alterna_dos='000000000000000'; // 10 enteros 2 decimales	var $Metodo_depreciacion=' '; // L linea recta | S suma de digitos	var $Cantidad_factor_conversion='000000000000000000'; // 13 enteros 5 decimales	var $Operador_factor_conversion='0';	var $Factor_conversion='0000000000'; // 5 enteros 5 decimales	var $Fecha_caducidad='00000000'; // para Ecuador	var $Codigo_ice='00'; // para Ecuador	var $Codigo_retencion='00000'; // para Ecuador	var $Clase_retencion=' '; // para Ecuador	var $Fin_de_linea="\r\n";	function genera()	{		$this->Codigo_comprobante=$this->prepara_valor($this->Codigo_comprobante,3,0);		$this->Numero_documento=$this->prepara_valor($this->Numero_documento,11,0);		$this->Secuencia=$this->prepara_valor($this->Secuencia,5,0);		$this->Nit=$this->prepara_valor($this->Nit,13,0);		$this->Descripcion=$this->prepara_cadena($this->Descripcion,50);		$this->Valor=$this->prepara_valor($this->Valor,13,2);		$this->Base_retencion=$this->prepara_valor($this->Base_retencion,13,2);		$this->Cantidad=$this->prepara_valor($this->Cantidad,10,5);		$this->Numero_documento_cruce=$this->prepara_valor($this->Numero_documento_cruce,11,0);		$this->Secuencia_documento_cruce=$this->prepara_valor($this->Secuencia_documento_cruce,3,0);		$this->Numero_documento_pedido=$this->prepara_valor($this->Numero_documento_pedido,11,0);		$this->Tasa_cambio=$this->prepara_valor($this->Tasa_cambio,8,7);		$this->Valor_en_extranjera=$this->prepara_valor($this->Valor_en_extranjera,13,2);		$this->Cantidad_pago=$this->prepara_valor($this->Cantidad_pago,9,2);		$this->Porcentaje_descuento_movimiento=$this->prepara_valor($this->Porcentaje_descuento_movimiento,2,2);		$this->Valor_descuento_movimiento=$this->prepara_valor($this->Valor_descuento_movimiento,11,2);		$this->Porcentaje_cargo_movimiento=$this->prepara_valor($this->Porcentaje_cargo_movimiento,2,2);		$this->Valor_cargo_movimiento=$this->prepara_valor($this->Valor_cargo_movimiento,11,2);		$this->Porcentaje_iva_movimiento=$this->prepara_valor($this->Porcentaje_iva_movimiento,2,2);		$this->Valor_iva_movimiento=$this->prepara_valor($this->Valor_iva_movimiento,11,2);		$this->Numero_documento_proveedor=$this->prepara_valor($this->Numero_documento_proveedor,11,0);		$this->Precio_unitario_moneda_local=$this->prepara_valor($this->Precio_unitario_moneda_local,13,5);		//-----------------------------------------------------------------------------------------------------------------------		$Resultado=$this->Tipo_comprobante.$this->Codigo_comprobante.$this->Numero_documento.$this->Secuencia;		$Resultado.=$this->Nit.$this->Sucursal.$this->Cuenta_contable.$this->Codigo_producto;		$Resultado.=$this->Fecha_documento.$this->Centro_costos.$this->Subcentro_costos.$this->Descripcion;		$Resultado.=$this->Afectacion.$this->Valor.$this->Base_retencion;		$Resultado.=$this->Codigo_vendedor.$this->Codigo_ciudad.$this->Codigo_zona.$this->Codigo_bodega;		$Resultado.=$this->Codigo_ubicacion.$this->Cantidad.$this->Tipo_documento_cruce.$this->Codigo_comprobante_cruce;		$Resultado.=$this->Numero_documento_cruce.$this->Secuencia_documento_cruce.$this->Fecha_vencimiento_documento_cruce;		$Resultado.=$this->Codigo_forma_pago.$this->Codigo_banco.$this->Tipo_documento_pedido;		$Resultado.=$this->Codigo_comprobante_pedido.$this->Numero_documento_pedido.$this->Secuencia_pedido;		$Resultado.=$this->Codigo_moneda.$this->Tasa_cambio.$this->Valor_en_extranjera.$this->Concepto_nomina;		$Resultado.=$this->Cantidad_pago.$this->Porcentaje_descuento_movimiento.$this->Valor_descuento_movimiento;		$Resultado.=$this->Porcentaje_cargo_movimiento.$this->Valor_cargo_movimiento;		$Resultado.=$this->Porcentaje_iva_movimiento.$this->Valor_iva_movimiento;		$Resultado.=$this->Indicador_nomina.$this->Numero_pago.$this->Numero_cheque.$this->Indicador_tipo_movimiento;		$Resultado.=$this->Nombre_computador.$this->Estado_comprobante.$this->Derecho_devolucion;		$Resultado.=$this->Credito_tributario.$this->Numero_comprobante_proveedor.$this->Numero_documento_proveedor;		$Resultado.=$this->Prefijo_documento_proveedor.$this->Fecha_documento_proveedor.$this->Precio_unitario_moneda_local;		$Resultado.=$this->Precio_unitario_moneda_extranjera.$this->Indicador_tipo_movimiento_activo;		$Resultado.=$this->Veces_a_depreciar.$this->Secuencia_transaccion.$this->Autorizacion_imprenta;		$Resultado.=$this->COA.$this->Numero_caja.$this->Numero_puntos_obtenidos.$this->Cantidad_dos;		$Resultado.=$this->Cantidad_alterna_dos.$this->Metodo_depreciacion.$this->Cantidad_factor_conversion.$this->Operador_factor_conversion;		$Resultado.=$this->Factor_conversion.$this->Fecha_caducidad.$this->Codigo_ice.$this->Codigo_retencion.$this->Clase_retencion;		$Resultado.=$this->Fin_de_linea;		return $Resultado;	}	function prepara_valor($Valor=0,$Enteros=13,$Decimales=0)	{		$Entero=intval($Valor);		$Decimal=round($Valor-$Entero,$Decimales);		$Cadena1=strval($Entero);		$Resultado=str_pad($Cadena1,$Enteros,'0',STR_PAD_LEFT);		if($Decimales)		{			$Cadena2=strval($Decimal);			$Resultado.=str_pad(substr($Cadena2,strpos($Cadena2,'.')+1,$Decimales),$Decimales,'0',STR_PAD_RIGHT);		}		return $Resultado;	}	function prepara_cadena($Cadena,$Longitud=50)	{		return str_pad(substr($Cadena,0,$Longitud),$Longitud," ",STR_PAD_RIGHT);	}}class interfase_tercero{	var $Nit='0000000000000';   // 13 posiciones numericas	var $Sucursal='000';  // 3 numeros	var $Tipo_nit='O';   // C = cliente P = proveedor O= otros	var $Nombre='                                                            '; // Nombre de 60 posiciones	var $Contacto='                                                  '; // 50 posiciones	var $Direccion='                                                                                                    '; // 100 posiciones	var $Telefono1='00000000000';  // 11 numeros	var $Telefono2='00000000000';  // 11 numeros	var $Telefono3='00000000000';  // 11 numeros	var $Telefono4='00000000000';  // 11 numeros	var $Fax='00000000000';  // 11 numeros	var $AAereo='000000'; // 6 numeros	var $Email='                              '; // 30 caracteres	var $Sexo='M';  // F M E=empresa	var $Codigo_clasificacion_tributaria='0'; // Regimen comun, Simplificado etc.	var $Tipo_identificacion='C'; // C cedula N nit	var $Cupo_credito='00000000000'; // 11 numeros	var $Lista_precios='00'; // 2 numeros	var $Codigo_vendedor='0000'; // 4 numeros	var $Codigo_ciudad='0001'; // 4 numeros   0001 POR DEFECTO PARA BOGOTA	var $Porcentaje_descuento='00000000000'; // 11 posiciones 9 enteros 2 decimales	var $Periodo_pago='000'; // 3 numeros	var $Observacion='                              '; // 30 caracteres	var $Codigo_pais='001'; // 3 numeros  001 por defecto COLOMBIA	var $Digito_verificacion=' '; // 1 caracter	var $Calificacion='   '; // 3 caracteres	var $Actividad_economica='00000'; // 5 numeros	var $Forma_pago='0000'; // 4 numeros	var $Cobrador='0000'; // 4 numeros	var $Tipo_persona='00'; // 01 Natural 02 Juridica	var $Declarante='N'; // S / N 1 caracter	var $Agente_retenedor='N'; // S/N 1 caracter	var $Autoretenedor='N'; // S/ N 1 caracter	var $Beneficiario_reteiva='N'; // S / N 1 caracter	function genera()	{		$this->Nit=$this->prepara_valor($this->Nit,13,0);		$this->Telefono1=$this->prepara_valor($this->Telefono1,11,0);		$this->Telefono2=$this->prepara_valor($this->Telefono2,11,0);		$this->Telefono3=$this->prepara_valor($this->Telefono3,11,0);		$this->Telefono4=$this->prepara_valor($this->Telefono4,11,0);		$this->Nombre=$this->prepara_cadena($this->Nombre,60);		$this->Contacto=$this->prepara_cadena($this->Contacto,50);		$this->Direccion=$this->prepara_cadena($this->Direccion,100);		$this->Email=$this->prepara_cadena($this->Email,30);		$this->Observacion=$this->prepara_cadena($this->Observacion,30);		$this->Digito_verificacion=$this->prepara_cadena($this->Digito_verificacion,1);		$Resultado=$this->Nit.$this->Sucursal.$this->Tipo_nit.$this->Nombre.$this->Contacto.$this->Direccion.$this->Telefono1.$this->Telefono2.$this->Telefono3.$this->Telefono4;		$Resultado.=$this->Fax.$this->AAereo.$this->Email.$this->Sexo.$this->Codigo_clasificacion_tributaria.$this->Tipo_identificacion.$this->Cupo_credito.$this->Lista_precios;		$Resultado.=$this->Codigo_vendedor.$this->Codigo_ciudad.$this->Porcentaje_descuento.$this->Periodo_pago.$this->Observacion.$this->Codigo_pais.$this->Digito_verificacion;		$Resultado.=$this->Calificacion.$this->Actividad_economica.$this->Forma_pago.$this->Cobrador.$this->Tipo_persona.$this->Declarante.$this->Agente_retenedor.$this->Autoretenedor.$this->Beneficiario_reteiva;		return $Resultado;	}	function prepara_valor($Valor=0,$Enteros=13,$Decimales=0)	{		$Entero=intval($Valor);		$Decimal=round($Valor-$Entero,$Decimales);		$Cadena1=strval($Entero);		$Resultado=str_pad($Cadena1,$Enteros,'0',STR_PAD_LEFT);		if($Decimales)		{			$Cadena2=strval($Decimal);			$Resultado.=str_pad(substr($Cadena2,strpos($Cadena2,'.')+1,$Decimales),$Decimales,'0',STR_PAD_RIGHT);		}		return $Resultado;	}	function prepara_cadena($Cadena,$Longitud=50)	{		return str_pad(substr($Cadena,0,$Longitud),$Longitud," ",STR_PAD_RIGHT);	}}?>