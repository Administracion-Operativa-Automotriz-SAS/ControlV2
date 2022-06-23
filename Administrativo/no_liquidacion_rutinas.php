<?php

/**
 * RUTINAS DE LIQUIDACION DE NOMINA
 *
 * @version $Id$
 * @copyright 2008
 */
function acumular_bases($HP/*historico de pago*/)
{
	global $Observaciones_pago;
	if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.="<br />Acumulando Bases.. ";
	include('inc/link.php');
	if($Acum = mysql_query("select sum(if(co.ac_ibc=1,hp.valor,0)) as base_ibc ,
		sum(if(co.ac_ibc_adicional=1,hp.valor,0)) as base_ibc_adicional,
		sum(if(co.ac_ret=1,hp.valor,0)) as base_retencion,
		sum(if(co.ac_ret_adicional=1,hp.valor,0)) as base_ret_adicional,
		sum(if(co.ac_prima=1,hp.valor,0)) as base_prima,
		sum(if(co.ac_ces=1,hp.valor,0)) as base_cesantias,
		sum(if(co.ac_ces_adicional=1,hp.valor,0)) as base_ces_adicional,
		sum(if(co.ac_bon=1,hp.valor,0)) as base_bonificacion,
		sum(if(co.ac_paraf=1,hp.valor,0)) as base_parafiscales,
		sum(if(co.tipo=1,hp.valor,0)) as devengados,
		sum(if(co.tipo=2,hp.valor,0)) as deducciones,
		sum(if(co.tipo=1,hp.valor,0)+if(co.tipo=2,-hp.valor,0)) as neto
		from no_hp_concepto hp,no_concepto co where
		hp.concepto=co.id and hp.pago=$HP", $LINK))
	{
		if($Ac = mysql_fetch_object($Acum))
		{
			if(!mysql_query("update no_hpago set base_ibc='$Ac->base_ibc',base_ibc_adicional='$Ac->base_ibc_adicional',
				base_retencion='$Ac->base_retencion',base_ret_adicional='$Ac->base_ret_adicional',base_prima='$Ac->base_prima',
				base_cesantias='$Ac->base_cesantias',base_ces_adicional='$Ac->base_ces_adicional',base_bonificacion='$Ac->base_bonificacion',
				base_parafiscales='$Ac->base_parafiscales',devengados='$Ac->devengados',deducciones='$Ac->deducciones',neto='$Ac->neto'
				where id='$HP'", $LINK)) echo mysql_error();
		}
	}
	else $Observaciones_pago.="Problemas acumulando bases. ";
	mysql_close($LINK);
	return qo("select * from no_hpago where id='$HP'");
}
# ##########################################################   ACUMULACION DE BASES TOTAL POR MES ##################################################
function acumular_mes($idContrato/* Contrato*/ , $idPlanilla/*Planilla*/)
{
	global $Observaciones_pago;
	if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.=" <br />Acumulando bases del mes.. ";
	return qo("select salario_basico, sum(sueldo) as sueldo,	sum(base_ibc) as ibc,sum(base_ibc_adicional) as ibc_ad,
					sum(base_retencion) as bret, sum(base_ret_adicional) as bret_ad,sum(base_parafiscales) as bparaf,
					sum(dias_trabajados) as diast
					FROM no_hpago,no_pl_acum
					where no_hpago.planilla=no_pl_acum.acumula and no_hpago.contrato='$idContrato'
					and no_pl_acum.planilla='$idPlanilla' GROUP by no_hpago.contrato ORDER by no_hpago.contrato");
}
# -------------------------------------------------------------------------------------------------------------------------------------------------
function salario_basico($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago,$Nt_salarios;
	if($Cont->salario)
	{
		if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.="<b>\$" . coma_format($Cont->salario) . "</b> ($Cont->fecha_vinculacion-$Cont->fecha_finalizacion)";
		if(!$Dias_a_pagar = qo1("select dias_trabajados from no_hpago where id=$HP"))
		{
			# ## verificacion de la fecha de ingreso contra la fecha de inicio de planilla y la fecha de finalizacion contra la fecha final de planilla
			$Dias_a_pagar = ($Cont->fecha_vinculacion > $Plan->fecha_inicial?dias($Cont->fecha_vinculacion, $Plan->fecha_final) + 1:$Plan->dias);
			if($Cont->fecha_finalizacion != '0000-00-00')
				$Dias_a_pagar = ($Cont->fecha_finalizacion < $Plan->fecha_final?$Dias_a_pagar - dias($Cont->fecha_finalizacion, $Plan->fecha_final):$Dias_a_pagar);
			IF($Menos_dias=qo1("select dias from no_anexo where contrato=$Cont->id and fecha_inicial<='$Plan->fecha_final' and fecha_final>='$Plan->fecha_inicial' "))
			{
				$Dias_a_pagar-=$Menos_dias;
				if($Dias_a_pagar<0) $Dias_a_pagar=0;
			}
		}
		if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.=" Dias: $Dias_a_pagar";


		if($Dias_a_pagar < 1)
		{
			$Observaciones_pago.=" No recibira sueldo este periodo.";
		}
		else
		{
			$Valor_a_pagar = $Dias_a_pagar * ($Cont->salario / 30);
			if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.=" Sueldo: " . coma_format($Valor_a_pagar);
			q("update no_hpago set dias_trabajados='$Dias_a_pagar',salario_basico='$Cont->salario',sueldo='$Valor_a_pagar' where id=$HP");
			guarda_concepto($HP, $Conc->id, $Dias_a_pagar, $Valor_a_pagar);
		}
	}
	else
	{
	  	$Observaciones_pago.="<font color='red'>No hay salario configurado para este empleado. </font><br />";
#		$Observaciones_pago.="No hay salario configurado para este empleado. " . ($Nt_salarios?" <a onclick=\"modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Nt_salarios&VINCULOC=contrato&VINCULOT=$Cont->id',0,0,300,300,'salarios');\">
#		Click aqui para corregir el problema</a>":"");
	}
}

function retencion_salarios($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago, $PARAMETROS, $ACUMULADO_MENSUAL, $Ano_liquidacion;
	if($Plan->calculo_ibc_ret == 'N')
		$Base_retencion = $ACUMULADO_MENSUAL->bret + $ACUMULADO_MENSUAL->bret_ad;
	else
		$Base_retencion = $ACUMULADO_MENSUAL->salario_basico + $ACUMULADO_MENSUAL->bret_ad;

	if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= " Busqueda de retención $Plan->calculo_ibc_ret";
	if($Base_retencion)
	{
		if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br />Retención en la fuente. Base de retención: " . coma_format($Base_retencion);

		if($Deducible = qo("select sum(if(co.deducible_ret='O',hp.valor,0)) as pension_obl,
			sum(if(co.deducible_ret='V',hp.valor,0)) as pension_vol,
			sum(if(co.deducible_ret='A',hp.valor,0)) as afc,
			sum(if(co.deducible_ret='I',hp.valor,0)) as vivienda,
			sum(if(co.deducible_ret='S',hp.valor,0)) as salud,
			sum(if(co.deducible_ret='S',hp.valor,0)) as salud_contingente
			from no_hp_concepto hp,no_concepto co where
			hp.concepto=co.id and hp.pago='$HP' and co.deducible_ret!='N' "))
		{
			if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Deducibles encontrados: </i><br />
			<i>Pensión obligatoria + fsp: </i>" . coma_format($Deducible->pension_obl) . "<br />
			<i>Pensión voluntaria: </i>" . coma_format($Deducible->pension_vol) . "<br />
			<i>AFC: </i>" . coma_format($Deducible->afc) . "";
			# # EVALUAMOS QUE la suma de las pensiones obligatorias + las voluntarias no sobrepase el 30% de la base inicial
			IF($Deducible->pension_obl + $Deducible->pension_vol > ($Base_retencion * ($PARAMETROS->lim_pensiones / 100)))
			{
				$Deducible->pension_vol = ($Base_retencion * ($PARAMETROS->lim_pensiones / 100)) - $Deducible->pension_obl;
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Reducción de la pensión voluntaria por límite de $PARAMETROS->lim_pensiones % a:</i>" . coma_format($Deducible->pension_vol);
			}
			# ---------------------------------------------------------------------------------------------------------------
			# # EVALUAMOS que la AFC no pase del 30% de la base inicial
			if($Deducible->afc > ($Base_retencion * ($PARAMETROS->lim_afc / 100)))
			{
				$Deducible->afc = $Base_retencion * ($PARAMETROS->lim_afc / 100);
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Reducción del afc por (límite del $PARAMETROS->lim_afc %) a:</i>" . coma_format($Deducible->afc);
			}
			# ---------------------------------------------------------------------------------------------------------------
			# HALLAMOS LA BASE BRUTA Y LA BASE BRUTA CONTINGENTE
			$BASE_BRUTA = $Base_retencion - ($Deducible->pension_obl + $Deducible->pension_vol + $Deducible->afc);
			if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Base Gravable = Base inicial - pensiones (obl+fsp+vol) - afc = </i>" . coma_format($BASE_BRUTA);
			$BASE_BRUTA_CONTINGENTE = $Base_retencion - $Deducible->pension_obl;
			if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Base Gravable Contingente = Base inicial - pensiones (obl+fsp) = </i>" . coma_format($BASE_BRUTA_CONTINGENTE);
			# # DE ACUERDO AL TIPO DE SALARIO SI ES ESTANDAR O INTEGRAL HALLAMOS LA BASE GRAVABLE
			IF($Cont->tipo_salario == 'I'/*salario integral*/)
			{
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Tipo de salario Integral</i>";
				$BASE_GRAVABLE = $BASE_BRUTA * ($PARAMETROS->base_gravable_in / 100);
				$BASE_GRAVABLE_CONTINGENTE = $BASE_BRUTA_CONTINGENTE * ($PARAMETROS->base_gravable_in / 100);
			}
			else
			{
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Tipo de salario estandar</i>";
				$BASE_GRAVABLE = $BASE_BRUTA * ($PARAMETROS->base_gravable_st / 100);
				$BASE_GRAVABLE_CONTINGENTE = $BASE_BRUTA_CONTINGENTE * ($PARAMETROS->base_gravable_st / 100);
			}
			if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Base Gravable </i>" . coma_format($BASE_GRAVABLE);
			if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Base Gravable contingente </i>" . coma_format($BASE_GRAVABLE_CONTINGENTE);
			# # SE BUSCAN LOS DOS ULTIMOS DEDUCIBLES: vivienda y salud/educacion  se toma uno de los dos y tienen unos limites
			if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Deducibles encontrados: <br />
			Vivienda - Corrección monetaria: </i>" . coma_format($Deducible->vivienda) . "<br />
			<i>Salud o Educación: </i>" . coma_format($Deducible->salud);
			# ##  SI EL DEDUCIBLE DE VIVIENDA SOBREPASA EL LIMITE SE AJUSTA
			if($Deducible->vivienda > $PARAMETROS->lim_ded_viv)
			{
				$Deducible->vivienda = $PARAMETROS->lim_ded_viv;
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Deducible por Vivienda - corrección monetaria (limite en valor:" . coma_format($PARAMETROS->lim_ded_viv) . ") =</i>" . coma_format($Deducible->vivienda);
			}
			# # SE EVALUA EL LIMITE DEL DEDUCIBLE POR SALUD Y/O EDUCACION ES UN PORCENTAJE DE LA BASE GRAVABLE
			if($Deducible->salud > round($BASE_GRAVABLE * ($PARAMETROS->lim_ded_salud / 100), 100))
			{
				$Deducible->salud = round($BASE_GRAVABLE * ($PARAMETROS->lim_ded_salud / 100), 100);
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Deducible por Salud y/o educación (limite: $PARAMETROS->lim_ded_salud %) =</i>" . coma_format($Deducible->salud);
			}
			if($Deducible->salud_contingente > round($BASE_GRAVABLE_CONTINGENTE * ($PARAMETROS->lim_ded_salud / 100), 100))
			{
				$Deducible->salud_contingente = round($BASE_GRAVABLE_CONTINGENTE * ($PARAMETROS->lim_ded_salud / 100), 100);
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Deducible por Salud y/o educación contingente (limite: $PARAMETROS->lim_ded_salud % =</i>" . coma_format($Deducible->salud_contingente);
			}

			$BASE_FINAL = $BASE_GRAVABLE - ($Deducible->vivienda > $Deducible->salud?$Deducible->vivienda:$Deducible->salud);
			$BASE_FINAL_CONTINGENTE = $BASE_GRAVABLE - ($Deducible->vivienda > $Deducible->salud_contingente?$Deducible->vivienda:$Deducible->salud_contingente);
			if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Base final </i>" . coma_format($BASE_FINAL) . "<i> Contingente: </i>" . coma_format($BASE_FINAL_CONTINGENTE);
			if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Valor UVT para el año $Ano_liquidacion es </i>" . coma_formatd($PARAMETROS->uvt, 2);
			$BASE_EN_UVT = round($BASE_FINAL / $PARAMETROS->uvt, 0);
			$BASE_EN_UVT_CONTINGENTE = round($BASE_FINAL_CONTINGENTE / $PARAMETROS->uvt, 0);
			if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Base en UVT: </i>" . coma_format($BASE_EN_UVT);
			# ### INICIALIZO LOS VALORES FINALES
			$VALOR_RETENCION_FINAL = 0;
			$VALOR_RETENCION_FINAL_CONTINGENTE = 0;
			# # BUSQUEDA EN LA TABLA DE RETENCIONES
			if($RTABLA = qo("select * from no_tabla_retencion where $BASE_EN_UVT between desde and hasta and ano=$Ano_liquidacion order by ano,desde desc limit 1"))
			{
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Tabla de Retenciones: desde :" . coma_format($RTABLA->desde) . " hasta :" . coma_format($RTABLA->hasta) . " $RTABLA->porcentaje % Valor: " . coma_format($RTABLA->valor);
				$BASE_UVT_FINAL = $BASE_EN_UVT - $RTABLA->desde + 1;
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br />Nueva base en uvt: </i>" . coma_format($BASE_UVT_FINAL);
				$BASE_NETA_UVT = $BASE_UVT_FINAL * ($RTABLA->porcentaje / 100) + $RTABLA->valor;
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Base uvt calculada: </i>" . coma_format($BASE_NETA_UVT);
				$VALOR_RETENCION = round($BASE_NETA_UVT * $PARAMETROS->uvt, 0);
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Valor Retención en pesos: </i>$ " . coma_format($VALOR_RETENCION);
				$VALOR_RETENCION_FINAL = ($VALOR_RETENCION < 10000?round($VALOR_RETENCION, -2):round($VALOR_RETENCION, -3));
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Valor Retención en pesos ajustada a miles si pasa de $10.000 :</i><u>" . coma_format($VALOR_RETENCION_FINAL) . "</u><br />";

				$BASE_UVT_FINAL_CONTINGENTE = $BASE_EN_UVT_CONTINGENTE - $RTABLA->desde + 1;
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Nueva base en uvt contingente: </i>" . coma_format($BASE_UVT_FINAL_CONTINGENTE);
				$BASE_NETA_UVT_CONTINGENTE = $BASE_UVT_FINAL_CONTINGENTE * ($RTABLA->porcentaje / 100) + $RTABLA->valor;
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Base uvt calculada contingente: </i>" . coma_format($BASE_NETA_UVT_CONTINGENTE);
				$VALOR_RETENCION_CONTINGENTE = round($BASE_NETA_UVT_CONTINGENTE * $PARAMETROS->uvt, 0);
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Valor Retención en pesos contingente: </i>$ " . coma_format($VALOR_RETENCION_CONTINGENTE);
				$VALOR_RETENCION_FINAL_CONTINGENTE = ($VALOR_RETENCION_CONTINGENTE < 10000?round($VALOR_RETENCION_CONTINGENTE, -2):round($VALOR_RETENCION_CONTINGENTE, -3));
				if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br /><i>Valor Retención en pesos ajustada a miles si pasa de $10.000 contingente:</i><u>" . coma_format($VALOR_RETENCION_FINAL_CONTINGENTE) . "</u><br />";
				if($VALOR_RETENCION_FINAL) guarda_concepto($HP, $Conc->id, 0, $VALOR_RETENCION_FINAL);
			}
			else
			{
				$Observaciones_pago.= "<br />No se encuentra registro en la tabla de retenciones";
			}
			q("update no_hpago set dret_pension_obl='$Deducible->pension_obl',
				dret_pension_vol='$Deducible->pension_vol',
				dret_afc='$Deducible->afc',
				base_ret_gravable='$BASE_GRAVABLE',base_ret_gravable_c='$BASE_GRAVABLE_CONTINGENTE',
				dret_vivienda='$Deducible->vivienda',
				dret_saludeduc='$Deducible->salud',dret_saludeduc_c='$Deducible->salud_contingente',
				base_ret_final='$BASE_FINAL',base_ret_final_c='$BASE_FINAL_CONTINGENTE',
				retencion_valor='$VALOR_RETENCION_FINAL',retencion_contingent='$VALOR_RETENCION_FINAL_CONTINGENTE'
				where id='$HP'");
		}
	}
	else $Observaciones_pago.= "  No encuentro acumulado mensual para Retenciones";
}

function auxilio_alimentacion_habiles($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago,$PARAMETROS;
	if($Conc->cantidad != 0) $Habiles = $Conc->cantidad;
	else
	{
		$i = ($Plan->fecha_inicial > $Cont->fecha_vinculacion?primer_dia_de_mes($Plan->fecha_inicial):$Cont->fecha_vinculacion);
		$m = date('m', strtotime($Plan->fecha_inicial));
		$Habiles = 0;
		while(true)
		{
			$dia_de_semana = date('w', strtotime($i));
			if($dia_de_semana > 0)
			{
				if(!qo1("select id from festivo where fecha='$i'"))
				{
					if($dia_de_semana == 6 && $PARAMETROS->sabado_habil == 1)
					{
						$Habiles++;
					}
					elseif($dia_de_semana < 6)
					{
						$Habiles++;
					}
				}
			}
			$i = aumentadias($i, 1);
			if(date('m', strtotime($i)) == $m) continue;
			break;
		}
	}
	$Valor = $Habiles * $PARAMETROS->aux_aliment_habil;
	if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= " $Habiles dias hábiles ";
	guarda_concepto($HP, $Conc->id, $Habiles, $Valor);
}

function horas_extras($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago;
	if($Cont->salario && $PAD1)
	{
		if($Valor = ($Cont->horas_mes?$Cont->salario / $Cont->horas_mes:0) * $Conc->cantidad * (1 + ($PAD1 / 100)))
			guarda_concepto($HP, $Conc->id, $Conc->cantidad, $Valor);
		if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= " Valor extras: " . coma_format($Valor);
	}
}

function pago_a_terceros($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago;
	if($Valor = qo1("select valor_descuento from no_beneficio where id=$Conc->cantidad")) guarda_concepto($HP, $Conc->id, $Conc->cantidad, $Valor);
	if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= " Valor: " . coma_format($Valor);
}

function prestamo_libranza($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago;
	if($Prst = qo("select * from no_emp_prestamo where id=$Conc->cantidad"))
	{
		guarda_concepto($HP, $Conc->id, $Conc->cantidad, $Prst->cuota_mensual, false /* no verifica la preexistencia del concepto en el periodo */);
		if(!q("update no_cuota_prestamo set fecha_cuota='$Plan->fecha_final',valor_capital='$Prst->cuota_mensual' where prestamo=$Prst->id and planilla=$Plan->id"))
		{
			$Numero_cuota = qo1("select max(num_cuota) from no_cuota_prestamo where prestamo=$Prst->id and fecha_cuota<'$Plan->fecha_final'") + 1;
			q("insert ignore into no_cuota_prestamo (prestamo,num_cuota,fecha_cuota,valor_capital,planilla)
			values ($Prst->id,$Numero_cuota,'$Plan->fecha_final',$Prst->cuota_mensual,$Plan->id) ");
		}
		if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= " Valor cuota prestamo: " . coma_format($Prst->cuota_mensual);
	}
}

function subsidio_transporte($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago,$PARAMETROS;
	$Ac = acumular_bases($HP);
	$Acm = acumular_mes($Cont->id, $Plan->id);
	if($Plan->calculo_ibc_ret == 'N') $Base_subtrans = $Acm->ibc + $Acm->ibc_ad;
	else $Base_subtrans = $Acm->salario_basico + $Acm->ibc_ad;

	if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= "<br />Ibc mes " . coma_format($Acm->ibc);
	if($Base_subtrans <= $PARAMETROS->limsup_subtrans)
	{
		IF($Valor = round($PARAMETROS->sub_transv / 30 * $Acm->diast, 0))
		{
			if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= " Valor: " . coma_format($Valor);
			guarda_concepto($HP, $Conc->id, $Acm->diast, $Valor);
		}
	}
}

function concepto_estandar($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago,$ACUMULADO_MENSUAL, $PARAMETROS;
	guarda_concepto($HP, $Conc->id, $Conc->cantiad, $Conc->valor);
#	$Observaciones_pago.=" " . coma_format($Conc->valor);
}

function ley100_pension($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago,$ACUMULADO_MENSUAL, $PARAMETROS;
	if($Plan->calculo_ibc_ret == 'N') $Base_IBC = $ACUMULADO_MENSUAL->ibc + $ACUMULADO_MENSUAL->ibc_ad;
	else $Base_IBC = $ACUMULADO_MENSUAL->salario_basico + $ACUMULADO_MENSUAL->ibc_ad;
	if($Base_IBC)
	{
		# ## calcula la pension con base en el ibc acumulado del mes
		if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= " IBC = " . coma_format($Base_IBC);
		if($Id_referencia=qo1("select l.id from no_emp_ley100 l,no_entidad e where e.tipo=4 and l.entidad=e.id and l.empleado=$Cont->empleado and
										((l.fecha_inicial<='$Plan->fecha_final' and l.fecha_final>='$Plan->fecha_inicial') or
										(l.fecha_inicial<='$Plan->fecha_final' and l.fecha_final='0000-00-00'))"))
		 		if($Valor = round($Base_IBC * $PARAMETROS->pension_empl / 100, 0)) guarda_concepto($HP, $Conc->id, $Id_referencia, $Valor);
		else
		$Observaciones_pago.= " No se encuentra el fondo de pensiones";
	}
	if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.=" Base IBC= ".coma_format($Base_IBC);
}

function ley100_pension_empr($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago,$ACUMULADO_MENSUAL, $PARAMETROS;
	if($Plan->calculo_ibc_ret == 'N')
		$Base_IBC = round(($ACUMULADO_MENSUAL->ibc * ($Cont->tipo_salario == 'I'?($PARAMETROS->ibc_integral / 100):1)) + $ACUMULADO_MENSUAL->ibc_ad, 0);
	else
		$Base_IBC = round(($ACUMULADO_MENSUAL->salario_basico * ($Cont->tipo_salario == 'I'?($PARAMETROS->ibc_integral / 100):1)) + $ACUMULADO_MENSUAL->ibc_ad, 0);
	if($Base_IBC)
	{
		# ## calcula la pension con base en el ibc acumulado del mes
		if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= " IBC = " . coma_format($Base_IBC);
		if($Id_referencia=qo1("select l.id from no_emp_ley100 l,no_entidad e where e.tipo=4 and l.entidad=e.id and l.empleado=$Cont->empleado and
										((l.fecha_inicial<='$Plan->fecha_final' and l.fecha_final>='$Plan->fecha_inicial') or
										(l.fecha_inicial<='$Plan->fecha_final' and l.fecha_final='0000-00-00'))"))
		 		if($Valor = round($Base_IBC * $PARAMETROS->pension_empr / 100, 0)) guarda_concepto($HP, $Conc->id, $Id_referencia, $Valor);
		else
		$Observaciones_pago.= " No se encuentra el fondo de pensiones";
	}
}

function ley100_salud($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago,$ACUMULADO_MENSUAL, $PARAMETROS;
	if($Plan->calculo_ibc_ret == 'N')
		$Base_IBC = round(($ACUMULADO_MENSUAL->ibc * ($Cont->tipo_salario == 'I'?($PARAMETROS->ibc_integral / 100):1)) + $ACUMULADO_MENSUAL->ibc_ad, 0);
	else
		$Base_IBC = round(($ACUMULADO_MENSUAL->salario_basico * ($Cont->tipo_salario == 'I'?($PARAMETROS->ibc_integral / 100):1)) + $ACUMULADO_MENSUAL->ibc_ad, 0);
	if($Base_IBC)
	{
		# ## calcula la pension con base en el ibc acumulado del mes
		if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= " IBC = " . coma_format($Base_IBC);
		if($Id_referencia=qo1("select l.id from no_emp_ley100 l,no_entidad e where e.tipo=1 and l.entidad=e.id and l.empleado=$Cont->empleado and
										((l.fecha_inicial<='$Plan->fecha_final' and l.fecha_final>='$Plan->fecha_inicial') or
										(l.fecha_inicial<='$Plan->fecha_final' and l.fecha_final='0000-00-00'))"))
			if($Valor = round($Base_IBC * $PARAMETROS->salud_empl / 100, 0)) guarda_concepto($HP, $Conc->id, $Id_referencia, $Valor);
		else
		$Observaciones_pago.= " No se encuentra el fondo de Salud";
	}
}

function ley100_salud_empr($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago,$ACUMULADO_MENSUAL, $PARAMETROS;
	if($Plan->calculo_ibc_ret == 'N')
		$Base_IBC = round(($ACUMULADO_MENSUAL->ibc * ($Cont->tipo_salario == 'I'?($PARAMETROS->ibc_integral / 100):1)) + $ACUMULADO_MENSUAL->ibc_ad, 0);
	else
		$Base_IBC = round(($ACUMULADO_MENSUAL->salario_basico * ($Cont->tipo_salario == 'I'?($PARAMETROS->ibc_integral / 100):1)) + $ACUMULADO_MENSUAL->ibc_ad, 0);
	if($Base_IBC)
	{
		# ## calcula la pension con base en el ibc acumulado del mes
		if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= " IBC = " . coma_format($Base_IBC);
		if($Id_referencia=qo1("select l.id from no_emp_ley100 l,no_entidad e where e.tipo=1 and l.entidad=e.id and l.empleado=$Cont->empleado and
										((l.fecha_inicial<='$Plan->fecha_final' and l.fecha_final>='$Plan->fecha_inicial') or
										(l.fecha_inicial<='$Plan->fecha_final' and l.fecha_final='0000-00-00'))"))
					if($Valor = round($Base_IBC * $PARAMETROS->salud_empr / 100, 0)) guarda_concepto($HP, $Conc->id, $Id_referencia, $Valor);
		else
		$Observaciones_pago.= " No se encuentra el fondo de Salud";
	}
}

function ley100_fsp($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago,$ACUMULADO_MENSUAL, $PARAMETROS;
	if($Plan->calculo_ibc_ret == 'N')
		$Base_IBC = round(($ACUMULADO_MENSUAL->ibc * ($Cont->tipo_salario == 'I'?($PARAMETROS->ibc_integral / 100):1)) + $ACUMULADO_MENSUAL->ibc_ad, 0);
	else
		$Base_IBC = round(($ACUMULADO_MENSUAL->salario_basico * ($Cont->tipo_salario == 'I'?($PARAMETROS->ibc_integral / 100):1)) + $ACUMULADO_MENSUAL->ibc_ad, 0);
	if($Base_IBC)
	{
		if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= " IBC = " . coma_format($Base_IBC);
		if($Base_IBC >= ($PARAMETROS->salario_minimo * 4) and $Base_IBC < ($PARAMETROS->salario_minimo * 16))
			$Valor = round($Base_IBC * $PARAMETROS->fsp_4_16 / 100, 0);
		elseif($Base_IBC >= ($PARAMETROS->salario_minimo * 16) and $Base_IBC < ($PARAMETROS->salario_minimo * 17))
			$Valor = round($Base_IBC * $PARAMETROS->fsp_16_17 / 100, 0);
		elseif($Base_IBC >= ($PARAMETROS->salario_minimo * 17) and $Base_IBC < ($PARAMETROS->salario_minimo * 18))
			$Valor = round($Base_IBC * $PARAMETROS->fsp_17_18 / 100, 0);
		elseif($Base_IBC >= ($PARAMETROS->salario_minimo * 18) and $Base_IBC < ($PARAMETROS->salario_minimo * 19))
			$Valor = round($Base_IBC * $PARAMETROS->fsp_18_19 / 100, 0);
		elseif($Base_IBC >= ($PARAMETROS->salario_minimo * 19) and $Base_IBC < ($PARAMETROS->salario_minimo * 20))
			$Valor = round($Base_IBC * $PARAMETROS->fsp_19_20 / 100, 0);
		elseif($Base_IBC >= ($PARAMETROS->salario_minimo * 20))
			$Valor = round($Base_IBC * $PARAMETROS->fsp_20 / 100, 0);
		if($Id_referencia=qo1("select l.id from no_emp_ley100 l,no_entidad e where e.tipo=4 and l.entidad=e.id and l.empleado=$Cont->empleado and
										((l.fecha_inicial<='$Plan->fecha_final' and l.fecha_final>='$Plan->fecha_inicial') or
										(l.fecha_inicial<='$Plan->fecha_final' and l.fecha_final='0000-00-00'))"))
					if($Valor) guarda_concepto($HP, $Conc->id, $Id_referencia, $Valor);
		else $Observaciones_pago.= " No se encuentra el fondo de Pensiones";
	}
}

function ajuste_dias_trabajados($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $Observaciones_pago;
	if($Conc->cantidad)
	{
		q("update no_hpago set dias_trabajados=$Conc->cantidad where id=$HP");
		if($_COOKIE['NOL_mostrar_liquidaciones']) $Observaciones_pago.= " Ajuste dias trabajados $Conc->cantidad ";
	}
}

function ajuste_ibc_basico_mas_novedades($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan1/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	global $PL;
	$PL->calculo_ibc_ret = 'E';
}

function parafiscal($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan1/*planilla*/ , $HP/*historico de pagos*/ , $Tipo = false/* parámetro adicional */)
{
	global $PARAMETROS, $ACUMULADO_MENSUAL;
	if($Tipo == 'caja')
	{
		IF($Valor = round($ACUMULADO_MENSUAL->bparaf * ($PARAMETROS->apr_caja / 100), -2)) guarda_concepto($HP, $Conc->id, $PARAMETROS->entidad_caja, $Valor);
	}
	elseif($Tipo == 'icbf')
	{
		IF($Valor = round($ACUMULADO_MENSUAL->bparaf * ($PARAMETROS->apr_icbf / 100), -2)) guarda_concepto($HP, $Conc->id, $PARAMETROS->entidad_ibc, $Valor);
	}
	elseif($Tipo == 'sena')
	{
		IF($Valor = round($ACUMULADO_MENSUAL->bparaf * ($PARAMETROS->apr_sena / 100), -2)) guarda_concepto($HP, $Conc->id, $PARAMETROS->entidad_sena, $Valor);
	}
}

function guarda_concepto($PAGO, $CONCEPTO, $CANTIDAD, $VALOR, $VERIFICAR=true)
{
	require('inc/link.php');
	{
		if($VERIFICAR)
		{
		 	if(mysql_query("update no_hp_concepto set cantidad='$CANTIDAD', valor='$VALOR' where pago='$PAGO' and concepto='$CONCEPTO' ", $LINK))
		 	{
				if(!mysql_affected_rows($LINK))
				{
					mysql_query("Insert ignore into no_hp_concepto (pago,concepto,cantidad,valor) values ('$PAGO','$CONCEPTO','$CANTIDAD','$VALOR')", $LINK);
				}
			}
		}
		ELSE
		{
			mysql_query("Insert ignore into no_hp_concepto (pago,concepto,cantidad,valor) values ('$PAGO','$CONCEPTO','$CANTIDAD','$VALOR')", $LINK);
		}
	}
	mysql_close($LINK);
}

function aporte_fondo_mutuo($Cont/*Contrato*/ , $Conc/*Concepto*/ , $Plan/*planilla*/ , $HP/*historico de pagos*/ , $PAD1 = false/* parámetro adicional */)
{
	# # la cantidad es es porcentaje y el PAD1 si es true significa que es obligatorio.
	global $ACUMULADO_MENSUAL,$Observaciones_pago;
	if($Conc->valor > 100)
	{
		if($_COOKIE['NOL_mostrar_liquidaciones']=='1') $Observaciones_pago.= " Concepto ajustado por valor " . coma_format($Conc->valor);
		guarda_concepto($HP, $Conc->id,$Conc->cantidad,$Conc->valor);
	}
	else
	{
		if($Plan->calculo_ibc_ret == 'N')
			$Base_IBC = $ACUMULADO_MENSUAL->ibc + $ACUMULADO_MENSUAL->ibc_ad;
		else
			$Base_IBC = $ACUMULADO_MENSUAL->salario_basico + $ACUMULADO_MENSUAL->ibc_ad;
		if($Base_IBC)
		{
			IF($Valor = round($Base_IBC * $Conc->valor / 100, 0)) guarda_concepto($HP, $Conc->id,$Conc->cantidad,$Valor);
		}
		else
			if($_COOKIE['NOL_mostrar_liquidaciones']=='1') $Observaciones_pago.= " No encuentra base.";
	}
}


?>