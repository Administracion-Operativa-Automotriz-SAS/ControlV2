<?php

/**
 * Traer datos del asegurado de un siniestro para Administracion
 *
 * @version $Id$
 * @copyright 2010
 */
include('inc/funciones_.php');

if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}
busca_cliente();

function busca_cliente()
{
	global $id;
	//  DEBE RECIBIR id COMO EL id interno de la factura
	if(!$Factura=qo("select * from cxc_factura where id=$id")) die('no existe el registro '.$id);
	if($Factura->base && $Factura->numsiniestro)
	{
		$Hoy=date('Y-m-d');
		$Numero_aseguradora=qo1("select idcars from base_aseguradora where id=$Factura->base");
		if($Sins=q("select id,numero,asegurado_nombre,asegurado_id,placa,asegurado_direccion,declarante_direccion,
								declarante_telefono from aoacol_aoacars.siniestro where numero like '%".$Factura->numsiniestro."%'
								and aseguradora=$Numero_aseguradora"))
		{
			if(mysql_num_rows($Sins)==1)
			{
				$S=mysql_fetch_object($Sins);
				echo "<script language='javascript'>
					function carga()
					{
						window.open('zgenera_factura.php?Acc=importar_cliente&id='+$S->id,'_self');
					}
				</script>
				<body onload='carga()'></body>";
			}
			else
			{
				html();
				echo "<script language='javascript'>
						function carga()
						{
							centrar(500,500);
						}
						function importar(id)
						{
							if(confirm('Desea importar la información de este siniestro?'))
							{
								window.open('zgenera_factura.php?Acc=importar_cliente&id='+id,'_self');
							}
						}
				</script>
				<body onload='carga()'>
					<b>Seleccione por favor uno de los siguientes registros encontrados</b><br />
					<table border cellspacing=0><tr>
						<th>Numero Siniestro</th>
						<th>Nombre Asegurado</th>
						<th>Identificación Asegurado</th>
						<th>Dirección Asegurado</th>
						<th>Dirección Declarante</th>
						<th>Teléfono Declarante</th>
						<th>Placa Asegurado</th>
						<th>Acción</th>
						</tr>";
				while($S=mysql_fetch_object($Sins))
				{
					echo "<tr>
								<td align='center'>$S->numero</td>
								<td align='left'>$S->asegurado_nombre</td>
								<td align='right'>$S->asegurado_id</td>
								<td align='left'>$S->asegurado_direccion</td>
								<td align='left'>$S->declarante_direccion</td>
								<td align='left'>$S->declarante_telefono</td>
								<td align='center'>$S->placa</td>
								<td align='center'><input type='button' value='Importar' onclick='importar($S->id);'></td>
								</tr>";
				}
				echo "</table>
				</body>";
			}
		}
		else
		{
			html();
			echo "<script language='javascript'>
					function carga()
					{
					centrar(10,10);
					alert('No hay registros que coincidan con el criterio. Verifique la base de la aseguradora y el número del siniestro');
					window.close();
					void(null);
					}
				</script>
				<body onload='carga()'></body>";
		}
	}
	else
	{
		html();
		echo "<script language='javascript'>
			function carga()
			{
				centrar(10,10);
				alert('No hay información suficiente en la factura. Verifique la base de la aseguradora y el número del siniestro');
				window.close();
				void(null);
			}
		</script>
		<body onload='carga()'></body>";
	}
}

function importar_cliente()
{
	global $id;
	$Datos=qo("select numero,asegurado_nombre,asegurado_id,placa,asegurado_direccion,declarante_direccion,
		declarante_telefono,declarante_ciudad,declarante_tel_resid,declarante_tel_ofic,declarante_celular,declarante_tel_otro,ciudad,ciudad_original
		from aoacol_aoacars.siniestro where id=$id");
	$Direccion=$Datos->asegurado_direccion?$Datos->asegurado_direccion:$Datos->declarante_direccion;

	if($Datos->declarante_telefono)
	{
		$Telefono1=$Datos->declarante_telefono;
		if($Datos->declarante_tel_resid)
		{
			$Telefono2=$Datos->declarante_tel_resid;
			if($Datos->declarante_tel_ofic) $Telefono3=$Datos->declarante_tel_ofic;
			elseif($Datos->declarante_tel_otro) $Telefono3=$Datos->declarante_tel_otro;
		}
		elseif($Datos->declarante_tel_ofic)
		{
			$Telefono2=$Datos->declarante_tel_ofic;
			if($Datos->declarante_tel_otro) $Telefono3=$Datos->declarante_tel_otro;
		}
		elseif($Datos->declarante_tel_otro) $Telefono2=$Datos->declarante_tel_otro;
	}
	elseif($Datos->declarante_tel_resid)
	{
		$Telefono1=$Datos->declarante_tel_resid;
		if($Datos->declarante_tel_ofic) $Telefono2=$Datos->declarante_tel_ofic;
		elseif($Datos->declarante_tel_otro) $Telefono2=$Datos->declarante_tel_otro;
	}
	elseif($Datos->declarante_tel_ofic)
	{
		$Telefono1=$Datos->declarante_tel_ofic;
		if($Datos->declarante_tel_otro) $Telefono2=$Datos->declarante_tel_otro;
	}
	elseif($Datos->declarante_tel_otro) $Telefono1=$Datos->declarante_tel_otro;

	if($Datos->declarante_celualr) $Celular=$Datos->declarante_celular; else $Celular='';


	html('Importacion de datos del cliente');
	echo "<script language='javascript'>
		function carga()
		{
			centrar(600,500);
		}
		function busqueda_ciudad(Campo,Contenido)
		{
			modal('marcoindex.php?Acc=pide_ciudad&Campo='+Campo+'&Dato='+Contenido+'&Forma=forma',0,0,200,600,'PC');
		}
	</script>
	<body onload='carga()'>
	<form action='' method='post' target='_self' name='forma' id='forma'>
		<h3><b>Datos del cliente</b></h3>
		<table border cellspacing=0 bgcolor='dedede'>
		<tr><td>Nombre del cliente:</td><td><input type='text' name='nombre' id='nombre' value='$Datos->asegurado_nombre' size=100></td></tr>
		<tr><td>Tipo de identificación:</td><td>".menu3('TD','NIT,NIT;CC,CC;RUT,RUT','',1)."</td></tr>
		<tr><td>Identificación:</td><td><input type='text' name='identificacion' id='identificacion' value='$Datos->asegurado_id' class='numero' size=10></td></tr>
		<tr><td>Dirección:</td><td><input type='text' name='direccion' id='direccion' value='$Direccion'  size=100></td></tr>
		<tr><td>Teléfono:</td><td><input type='text' name='telefono1' id='telefono1' value='$Datos->declarante_telefono' size=10 maxlength=10></td></tr>
		<tr><td>Ciudad:</td><td><input type='text' name='_ciudad' id='_ciudad' value='' size='100' onclick=\"busqueda_ciudad('ciudad','');\" readonly>
					<input type='hidden' name=ciudad id=ciudad value=''></td></tr>


	</form></body>";
}

?>