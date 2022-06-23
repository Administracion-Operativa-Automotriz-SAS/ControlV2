<?php

$_SESSION['MENUS'] = "select es.nombre , fw.opcional
			from estado_siniestro es, fw_estadosiniestro fw
			where es.id=fw.opcional and fw.usuario=".$_SESSION['User']." and fw.inicial=$R->estado ";

/*
if($_D=qo("select * from aoacol_aoacars.placa_especial where placa='$R->placa'"))
{
	$Texto='A T E N C I O N \n\nEsta placa tiene una condición especial exigida por la aseguradora, por favor verifique\nque la siguiente información coincida:\n\n'.
	$_D->descripcion.'\n\nCONDICION ESPECIAL DEL SERVICIO:\n'.$_D->condicion;
	$Texto=str_replace("\n",'',$Texto);
	$Texto=str_replace("\r",'\n',$Texto);
	$_SESSION['Aviso_Carga']="alert('$Texto');";
}
else
	$_SESSION['Aviso_Carga']='';
*/
if($_D=qo("select * from aoacol_aoacars.placa_especial where placa='$R->placa'")) // busca placas especiales para presentar un aviso en la carga de la informacion
{
	$Texto='A T E N C I O N \n\nEsta placa tiene una condición especial exigida por la aseguradora, por favor verifique\nque la siguiente información coincida:\n\n'.
	$_D->descripcion.'\n\nCONDICION ESPECIAL DEL SERVICIO:\n'.$_D->condicion;
	$Texto=str_replace("\n",'',$Texto);
	$Texto=str_replace("\r",'\n',$Texto);
	$this->Aviso_Carga="alert('$Texto');";
}

if($R->estado==5 /* Pendiente*/ || $R->estado==7 /* Servicio */ ||  $R->estado==3 /* Adjudicado*/) 
{
	$H1=date('Y-m-d');$H2=date('H:i:s');$U=$_SESSION['Nombre'];
	$fingreso=date('Y-m-d',strtotime($R->ingreso));
	$hingreso=date('H:i:s',strtotime($R->ingreso));
	$Hoy1=date('Y-m-d');$Hoy2=date('H:i:s');
	if($R->id)
	{
		// si no encuentra un registro de seguimiento asociado con el ingreso del caso a la base de datos, lo crea.
		if(!qo1("select id from seguimiento where siniestro=$R->id and tipo=1")) q("insert into seguimiento (siniestro,fecha,hora,descripcion,tipo) values ('$R->id','$fingreso','$hingreso','Ingreso a AOA',1)");
		q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($R->id,'$H1','$H2','$U','Consulta desde Siniestros',2)");
	}
}
if(!$R->estado) q("update siniestro set estado=5 where id=$R->id");

function activacion_modificacion($campo,$R) // funcion que permite modificar campos del siniestro de acuerdo a una ventana de tiempo otorgado por un dia
{
	$Resultado=false;
	$Hoy=date('Y-m-d');
	if(!$R->id) return true;
	if($campo=='img_contrato_f' )
	{
		if(inlist($_SESSION['User'],'1,2,7,10,13'))
		{
			if(empty($R->img_contrato_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='img_cedula_f' )
	{
		if(inlist($_SESSION['User'],'1,2,5,7,10,13'))
		{
			if(empty($R->img_cedula_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='img_pase_f' )
	{
		if(inlist($_SESSION['User'],'1,2,5,7,10,13'))
		{
			if(empty($R->img_pase_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='adicional1_f' )
	{
		if(inlist($_SESSION['User'],'1,2,5,7,10,13'))
		{
			if(empty($R->adicional1_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='adicional2_f' )
	{
		if(inlist($_SESSION['User'],'1,2,5,7,10,13'))
		{
			if(empty($R->adicional2_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='adicional3_f' )
	{
		if(inlist($_SESSION['User'],'1,2,5,7,10,13'))
		{
			if(empty($R->adicional3_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='adicional4_f' )
	{
		if(inlist($_SESSION['User'],'1,2,5,7,10,13'))
		{
			if(empty($R->adicional4_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='congelamiento_f' )
	{
		if(inlist($_SESSION['User'],'1,2,5,7,10,13'))
		{
			if(empty($R->congelamiento_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='gastosf_f' )
	{
		if(inlist($_SESSION['User'],'1,2,5,7,10,13'))
		{
			if(empty($R->gastosf_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='img_odo_salida_f' )
	{
		if(inlist($_SESSION['User'],'1,2,7,10,13'))
		{
			if(empty($R->img_odo_salida_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='img_inv_salida_f' )
	{
		if(inlist($_SESSION['User'],'1,2,7,10,13'))
		{
			if(empty($R->img_inv_salida_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='fotovh1_f' )
	{
		if(inlist($_SESSION['User'],'1,2,7,10,13'))
		{
			if(empty($R->fotovh1_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='fotovh2_f' )
	{
		if(inlist($_SESSION['User'],'1,2,7,10,13'))
		{
			if(empty($R->fotovh2_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='fotovh3_f' )
	{
		if(inlist($_SESSION['User'],'1,2,7,10,13'))
		{
			if(empty($R->fotovh3_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='fotovh4_f' )
	{
		if(inlist($_SESSION['User'],'1,2,7,10,13'))
		{
			if(empty($R->fotovh4_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='eadicional1_f' )
	{
		if(inlist($_SESSION['User'],'1,2,7,10,13'))
		{
			if(empty($R->eadicional1_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	if($campo=='eadicional2_f' )
	{
		if(inlist($_SESSION['User'],'1,2,7,10,13'))
		{
			if(empty($R->eadicional2_f) && $R->estado!=8) $Resultado=true;
			else if(qo("select id from activa_modsin where siniestro=$R->id and fecha='$Hoy' "))
				$Resultado=true;
		}
	}
	return $Resultado;
}

?>