<?php
/*  ACTUALIZACION DE SINIESTROS
 * el objetivo es que cada vez que se actualice un siniestro, se cambie el estado o cualquier novedad automáticamente en la base de datos de AOA-cars
 */
/// BUSQUEDA DEL REGISTRO EN AOACARS

if($R->proveedor && $R->concepto_trib && $R->valor_factura)
{
  $Prov=qo("select * from proveedor where id=$R->proveedor");
  if($Prov->regimen && $Prov->tipo_persona && $Prov->actividad_tributaria)
  {
    $Regimen=qo("select * from regimen_tributario where id=$Prov->regimen");
    // tipo de persona es 1: juridica 2: natural $R->tipo_persona
    $Activ=qo("select * from actividad_tributaria where id=$Prov->actividad_tributaria");
    //// OBTENCION DEL PORCENTAJE DE RETENCION EN LA FUENTE
    if($Regimen->rete_fuente) /* si el regimen indica que se debe obtener retencion en la fuente */
    {
    	if($R->concepto_trib!=15) // TRABAJADORES INDEPENDIENTES
    	{
    		$Concepto_tributario=qo("select * from concepto_tributario where id=$R->concepto_trib");
    		if($R->base1_ret>=$Concepto_tributario->base || $Prov->prov_permanente)
    		{
    			if($Prov->tipo_persona==1 /* juridica */)
    			{
    				$Porc_rete_fuente1=$Concepto_tributario->porc_juridica;  /* porcentaje de retefuente persona juridica */
    			}
    			else /* natural */
    			{
    				$Porc_rete_fuente1=$Concepto_tributario->porc_natural; /* porcentaje de retefuente persona natural */
    			}
    		}
    		else
    		{
    			$Porc_rete_fuente1=0;
    		}
    	}

    	//*****************************************************************************************************
    	if($R->concepto_trib2!=15)
    	{
    		$Concepto_tributario=qo("select * from concepto_tributario where id=$R->concepto_trib2");
    		if($R->base2_ret>=$Concepto_tributario->base || $Prov->prov_permanente)
    		{
    			if($Prov->tipo_persona==1 /* juridica */)
    			{
    				$Porc_rete_fuente2=$Concepto_tributario->porc_juridica;  /* porcentaje de retefuente persona juridica */
    			}
    			else /* natural */
    			{
    				$Porc_rete_fuente2=$Concepto_tributario->porc_natural; /* porcentaje de retefuente persona natural */
    			}
    		}
    		else
    		{
    			$Porc_rete_fuente2=0;
    		}
    	}
    }
    else  /* si el regimen indica que no se debe sacar retencion en la fuente */
    {
    	$Porc_rete_fuente1=0;
    	$Porc_rete_fuente2=0;
    }
    //// OBTENCION DEL PORCENTAJE DE RETENCION DE ICA
    if($Regimen->rete_ica) /* si el regimen indica que hay que obtener retencion de ica */
    {
    	$Porc_rete_ica=$Activ->porcentaje;  /* este es el porcentaje por mil de rete ica */
    }
    else /* si el regimen indica que no hay que obtener retencion de ica */
    {
    	$Porc_rete_ica=0;
    }
  	if($R->concepto_trib!=15) $Valor_rtefuente1=round($R->base1_ret*$Porc_rete_fuente1/100,0); else $Valor_rtefuente1=$R->rete_fuente;
    if($R->concepto_trib2!=15) $Valor_rtefuente2=round($R->base2_ret*$Porc_rete_fuente2/100,0); else $Valor_retefuente2=$R->rete_fuente2;
    $Valor_rteica=round($R->base1_reteica*$Porc_rete_ica/1000,0);
	
	if($Prov->rete_cree)
	{
		$Porcentaje_retecree=qo1("select porcentaje from rete_cree where id='$Prov->rete_cree' ");
		$Valor_retecree=round($R->base_retecree*$Porcentaje_retecree/100,0);
	}
	else
	{
		$Porcentaje_retecree=0;
		$Valor_retecree=0;
	}
	
    $Valor_a_pagar=$R->valor_factura-$Valor_rtefuente1-$Valor_rtefuente2-$Valor_rteica+$R->iva-$Valor_retecree;
    q("update factura set porc_retefuente='$Porc_rete_fuente1',porc_retefuente2='$Porc_rete_fuente2', rete_fuente='$Valor_rtefuente1',rete_fuente2='$Valor_rtefuente2',
     porc_reteica='$Porc_rete_ica',	rete_ica='$Valor_rteica', rete_cree='$Valor_retecree',porc_retecree='$Porcentaje_retecree', valor_a_pagar='$Valor_a_pagar' where id=$R->id");
  }
  else
  {
    echo "<script language='javascript'>
      alert('El proveedor $R->nombre no tiene definida información necesaria. Por favor revise: Régimen Tributario, Tipo de Persona o Actividad Tributaria.');
      </script>";
   }
}
/*
else
{
  echo "<script language='javascript'>
    alert('Falta información. Por favor revise: Proveedor, Concepto Tributario o Valor de la Factura.');
    </script>";
}
*/
?>