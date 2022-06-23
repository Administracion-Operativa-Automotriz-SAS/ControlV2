<?php
/** FUNCIONES DE AUTORIZACIONES **/


function desencripta_data($id,$LINK=0)
{
	if($LINK)  $D=qom("select * from sin_autor where id=$id",$LINK);
	else $D=qo("select * from sin_autor where id=$id");
	require_once('inc/Crypt.php');
	$C = new Crypt();
	$C->Mode = Crypt::MODE_HEX;
	$C->Key  = '!'.$D->id.'+';
	$Datos=$C->decrypt($D->data);
	$DR=explode('|',$Datos);
	$R['identificacion']=$DR[0];
	$R['numero']=$DR[1];
	$R['banco']=$DR[2];
	$R['vencimiento_mes']=$DR[3];
	$R['vencimiento_ano']=$DR[4];
	$R['num_autorizacion']=$DR[5];
	$R['funcionario']=$DR[6];
	$R['codigo_seguridad']=$DR[7];
	return $R;
}

function desencripta_data2($D)
{
	$C = new Crypt();
	$C->Mode = Crypt::MODE_HEX;
	$C->Key  = '!'.$D->id.'+';
	$Datos=$C->decrypt($D->data);
	$DR=explode('|',$Datos);
	$R['identificacion']=$DR[0];
	$R['numero']=$DR[1];
	$R['banco']=$DR[2];
	$R['vencimiento_mes']=$DR[3];
	$R['vencimiento_ano']=$DR[4];
	$R['num_autorizacion']=$DR[5];
	$R['funcionario']=$DR[6];
	$R['codigo_seguridad']=$DR[7];
	return $R;
}

function encripta_data()
{
	global $id;
	$D=qo("select *,t_codigo_ach(banco) as nbanco from sin_autor where id=$id");
	if($D->identificacion && $D->numero && $D->vencimiento_mes && $D->vencimiento_ano && $D->codigo_seguridad)
	{
		$Datos=$D->identificacion.'|'.$D->numero.'|'.$D->nbanco.'|'.$D->vencimiento_mes.'|'.$D->vencimiento_ano.'|'.$D->num_autorizacion.'|'.$D->funcionario.'|'.$D->codigo_seguridad;
		require_once 'inc/Crypt.php';
		$C = new Crypt();
		$C->Mode = Crypt::MODE_HEX;
		$C->Key  = '!'.$D->id.'+';
		$Datose = $C->encrypt($Datos);
		if($Datose)
		{
			q("update sin_autor set data=\"$Datose\",numero='',banco='',vencimiento_mes='',vencimiento_ano='',
				num_autorizacion='',codigo_seguridad='' where id=$id");
			echo "<body><script language='javascript'>alert('Encripcion Satisfactoria');window.close();void(null);opener.parent.location.reload();</script></body>";
		}
		else
		{
			echo "<body><script language='javascript'>alert('Encripcion Fallida, debe verificar la informacion.');</script></body>";
		}
	}
	else
	{
		echo "<body><script language='javascript'>alert('Ya fue encriptado o no hay informacion suficiente para encriptar.');</script></body>";
	}
}


 ?>