<?php

/**
 *   RESUMEN ESTADISTICO REPORTE NUMERO 52
 *
 *
 *
 * @version $Id$
 * @copyright 2010
 */
$Tabla="tmpi_".$_SESSION['Id_alterno']."_52";

if($Periodos=q("select distinct date_format(fec_autorizacion,'%Y%m') as periodo from $Tabla order by fec_autorizacion"))
{
	echo "<br><h3>INFORMACION ESTADISTICA DE ESTA CONSULTA</H3><table border cellspacing=0 cellpadding=4 align='center' width='100%'><tr>";
	include('inc/link.php');
	while($Per=mysql_fetch_object($Periodos))
	{
		echo "<th>$Per->periodo</th>";
	}
	echo "<th>Total</th>";
	echo "</tr><tr>";
	//                     CALCULO DE ERRONEOS POR PERIODO
	mysql_data_seek($Periodos, 0);
	while($Per=mysql_fetch_object($Periodos))
	{
		echo "<td align='center'>";
		$Total_siniestros=qo1m("select count(*) from $Tabla where date_format(fec_autorizacion,'%Y%m')='$Per->periodo' ",$LINK);
		$Total_erroneos=qo1m("select count(*) from $Tabla where date_format(fec_autorizacion,'%Y%m')='$Per->periodo' and info_erronea='1' ",$LINK);
		if($Total_siniestros) $Porc=round($Total_erroneos/$Total_siniestros*100,2); else $Porc=0;
		echo "<table border cellspacing='0'><tr><td>Total solicitudes:</td><td align='right'>".coma_format($Total_siniestros)."</td><td align='right'>100 %</td></tr>
					<tr><td>Solicitudes con información errónea:</td><td align='right'>".coma_format($Total_erroneos)."</td><td align='right'>$Porc %</td></tr></table>";
		echo "</td>";
	}
	echo "<td align='center'>";
	$Total_siniestros=qo1m("select count(*) from $Tabla ",$LINK);
	$Total_erroneos=qo1m("select count(*) from $Tabla where info_erronea='1' ",$LINK);
	if($Total_siniestros) $Porc=round($Total_erroneos/$Total_siniestros*100,2); else $Porc=0;
	echo "<table border cellspacing='0'><tr><td>Total solicitudes:</td><td align='right'>".coma_format($Total_siniestros)."</td><td align='right'>100 %</td></tr>
				<tr><td>Solicitudes con información errónea:</td><td align='right'>".coma_format($Total_erroneos)."</td><td align='right'>$Porc %</td></tr></table>";
	echo "</td></TR>";
	//              CALCULO DE NO ADJUDICADOS vs LOS DEMAS POR PERIODO
	mysql_data_seek($Periodos, 0);
	echo "<TR>";
	while($Per=mysql_fetch_object($Periodos))
	{
		echo "<td align='center'>";
		$No_adjudicados=qo1m("select count(*) from $Tabla where  date_format(fec_autorizacion,'%Y%m')='$Per->periodo' and estado='NO ADJUDICADO' and info_erronea=1",$LINK);
		$Los_demas=qo1m("select count(*) from $Tabla where  date_format(fec_autorizacion,'%Y%m')='$Per->periodo' and estado!='NO ADJUDICADO' and info_erronea=1 ",$LINK);
		$Total=$No_adjudicados+$Los_demas;
		if($Total) $Pna=round($No_adjudicados/$Total*100); else $Pna=0;
		if($Total) $Pld=round($Los_demas/$Total*100,2); else $Pld=0;
		echo "<table border cellspacing=0 align='center'><tr><td>Siniestros con información errónea:</td><td align='center'>".coma_format($Total)."</td><td align='right'>100 %</td></tr>
					<tr><td>No Adjudicados:</td><td align='right'>".coma_format($No_adjudicados)."</td><td align='right'>".coma_formatd($Pna,2)." %</td></tr>
					<tr><td>Concluidos, En servicio, Adjudicados y Pendientes</td><td align='right'>".coma_format($Los_demas)."</td><td align='right'>".coma_formatd($Pld,2)." %</td></tr></table>";
		echo "</td>";
	}
	echo "<td align='center'>";
	$No_adjudicados=qo1m("select count(*) from $Tabla where estado='NO ADJUDICADO' and info_erronea=1",$LINK);
	$Los_demas=qo1m("select count(*) from $Tabla where estado!='NO ADJUDICADO' and info_erronea=1 ",$LINK);
	$Total=$No_adjudicados+$Los_demas;
	if($Total) $Pna=round($No_adjudicados/$Total*100); else $Pna=0;
	if($Total) $Pld=round($Los_demas/$Total*100,2); else $Pld=0;
	echo "<table border cellspacing=0 align='center'><tr><td>Siniestros con información errónea:</td><td align='center'>".coma_format($Total)."</td><td align='right'>100 %</td></tr>
				<tr><td>No Adjudicados:</td><td align='right'>".coma_format($No_adjudicados)."</td><td align='right'>".coma_formatd($Pna,2)." %</td></tr>
				<tr><td>Concluidos, En servicio, Adjudicados y Pendientes</td><td align='right'>".coma_format($Los_demas)."</td><td align='right'>".coma_formatd($Pld,2)." %</td></tr></table>";
	echo "</td>";
	echo "</TR>";
	////             CALCULO DE INFORMACION ERRADA POR CAUSALES
	mysql_data_seek($Periodos, 0);
	echo "<TR>";
	while($Per=mysql_fetch_object($Periodos))
	{
		echo "<td align='center' valign='top'>";
		$Causales=mysql_query("select causal,count(*) as cantidad from $Tabla where  date_format(fec_autorizacion,'%Y%m')='$Per->periodo' and estado='NO ADJUDICADO' and info_erronea=1 group by causal order by causal",$LINK);
		if(mysql_num_rows($Causales))
		{
			$Total=0;while($Ca=mysql_fetch_object($Causales)) $Total+=$Ca->cantidad;
			mysql_data_seek($Causales,0);
			if($Total)
			{
				echo "<table border cellspacing=0 align='center'><tr><th>Causal</th><th>Cantidad</th><th> % </th></tr>";
				while($Ca=mysql_fetch_object($Causales))
				{
					$Porc=round($Ca->cantidad/$Total*100,2);
					echo "<tr><td>$Ca->causal</td><td align='right'>".coma_format($Ca->cantidad)."</td><td align='right'>".coma_formatd($Porc,2)." %</td></tr>";
				}
				echo "<tr><td><b>Total</b></td><td align='right'>".coma_format($Total)."</td></tr></table>";
			}
		}
	}
	echo "<td align='center'>";
	$Causales=mysql_query("select causal,count(*) as cantidad from $Tabla where estado='NO ADJUDICADO' and info_erronea=1 group by causal order by causal",$LINK);
	if(mysql_num_rows($Causales))
	{
		$Total=0;while($Ca=mysql_fetch_object($Causales)) $Total+=$Ca->cantidad;
		mysql_data_seek($Causales,0);
		if($Total)
		{
			echo "<table border cellspacing=0 align='center'><tr><th>Causal</th><th>Cantidad</th><th> % </th></tr>";
			while($Ca=mysql_fetch_object($Causales))
			{
				$Porc=round($Ca->cantidad/$Total*100,2);
				echo "<tr><td>$Ca->causal</td><td align='right'>".coma_format($Ca->cantidad)."</td><td align='right'>".coma_formatd($Porc,2)." %</td></tr>";
			}
			echo "<tr><td><b>Total</b></td><td align='right'>".coma_format($Total)."</td></tr></table>";
		}
	}

	echo "</td>";
	echo "</TR>";
	///     CALCULO DE TIEMPOS Y EVENTOS     Autorización   vs Ingreso a AOA   (1)
	mysql_data_seek($Periodos, 0);
	echo "<TR>";

	while($Per=mysql_fetch_object($Periodos))
	{
		echo "<td align='center' valign='top'>";
		$Tablap=$Tabla.'_'.$Per->periodo;
		echo "<center><b>Autorizacion vs Ingreso a AOA</b></center> ";
		mysql_query("drop table if exists $Tablap",$LINK);
		if(!mysql_query("create table $Tablap select si_id as id,  datediff(si_ingreso,fec_autorizacion) as dias1 from $Tabla
		where date_format(fec_autorizacion,'%Y%m')='$Per->periodo' and estado='NO ADJUDICADO' and info_erronea=1	",$LINK)) echo "Error en: ".mysql_error($LINK);
		$Dias=mysql_query("select dias1,count(id) as cantidad from $Tablap group by dias1 order by dias1",$LINK);
		if(mysql_num_rows($Dias))
		{
			$Tdias=0;while($D=mysql_fetch_object($Dias)) $Tdias+=$D->cantidad;
			mysql_data_seek($Dias,0);
			if($Tdias)
			{
				echo "<table border cellspacing=0><tr><th>Tiempo</th><th>Cantidad</th><th>%</th></tr>";
				while($D=mysql_fetch_object($Dias))
				{
					$Porc=round($D->cantidad/$Tdias*100,2);
					echo "<tr><td align='center'>$D->dias1</td><td align='right'>".coma_format($D->cantidad)."</td><td align='right'>".coma_formatd($Porc,2)."</td></tr>";
				}
				echo "</table>";
			}
		}
		echo "</td>";
	}
	echo "<td align='center' valign='top'>";
	$Tablap=$Tabla.'_todos';
	echo "<center><b>Autorizacion vs Ingreso a AOA</b></center>";
	mysql_query("drop table if exists $Tablap",$LINK);
	if(!mysql_query("create table $Tablap select si_id as id,  datediff(si_ingreso,fec_autorizacion) as dias1 from $Tabla
	where estado='NO ADJUDICADO' and info_erronea=1	",$LINK)) echo "Error en: ".mysql_error($LINK);
	$Dias=mysql_query("select dias1,count(id) as cantidad from $Tablap group by dias1 order by dias1",$LINK);
	if(mysql_num_rows($Dias))
	{
		$Tdias=0;while($D=mysql_fetch_object($Dias)) $Tdias+=$D->cantidad;
		mysql_data_seek($Dias,0);
		if($Tdias)
		{
			echo "<table border cellspacing=0><tr><th>Tiempo</th><th>Cantidad</th><th>%</th></tr>";
			while($D=mysql_fetch_object($Dias))
			{
				$Porc=round($D->cantidad/$Tdias*100,2);
				echo "<tr><td align='center'>$D->dias1</td><td align='right'>".coma_format($D->cantidad)."</td><td align='right'>".coma_formatd($Porc,2)."</td></tr>";
			}
			echo "</table>";
		}
	}

	echo "</td>";

	echo "</TR>";

	///     CALCULO DE TIEMPOS Y EVENTOS     Ingreso vs Actualizacion (8)
	mysql_data_seek($Periodos, 0);
	echo "<TR>";

	while($Per=mysql_fetch_object($Periodos))
	{
		echo "<td align='center' valign='top'>";
		$Tablap=$Tabla.'_A'.$Per->periodo;
		echo "<center><b>Ingreso vs Ultima Actualización</b></center> ";
		mysql_query("drop table if exists $Tablap",$LINK);
		if(!mysql_query("create table $Tablap select t.si_id as id,t.si_ingreso as ing, max(se.fecha) as actualiza from $Tabla t,seguimiento se
		where se.siniestro=t.si_id and
		date_format(t.fec_autorizacion,'%Y%m')='$Per->periodo' and t.estado='NO ADJUDICADO' and t.info_erronea=1 and se.tipo=8
		group by t.si_id order by t.si_id",$LINK)) echo "Error en: ".mysql_error($LINK);
		mysql_query("alter table $Tablap add column dif integer(10) not null",$LINK);
		mysql_query("update $Tablap set dif=datediff(actualiza,ing)");
		$Dias=mysql_query("select dif,count(id) as cantidad from $Tablap group by dif order by dif",$LINK);
		if(mysql_num_rows($Dias))
		{
			$Tdias=0;while($D=mysql_fetch_object($Dias)) $Tdias+=$D->cantidad;
			mysql_data_seek($Dias,0);
			if($Tdias)
			{
				echo "<table border cellspacing=0><tr><th>Tiempo</th><th>Cantidad</th><th>%</th></tr>";
				while($D=mysql_fetch_object($Dias))
				{
					$Porc=round($D->cantidad/$Tdias*100,2);
					echo "<tr><td align='center'>$D->dif</td><td align='right'>".coma_format($D->cantidad)."</td><td align='right'>".coma_formatd($Porc,2)."</td></tr>";
				}
				echo "</table>";
			}
		}

		echo "</td>";
	}
	echo "<td align='center' valign='top'>";
	$Tablap=$Tabla.'_Atodos';
	echo "<center><b>Ingreso vs Ultima Actualización</b></center> ";
	mysql_query("drop table if exists $Tablap",$LINK);
	if(!mysql_query("create table $Tablap select t.si_id as id,t.si_ingreso as ing, max(se.fecha) as actualiza from $Tabla t,seguimiento se
		where se.siniestro=t.si_id and estado='NO ADJUDICADO' and t.info_erronea=1 and se.tipo=8
		group by t.si_id order by t.si_id",$LINK)) echo "Error en: ".mysql_error($LINK);
	mysql_query("alter table $Tablap add column dif integer(10) not null",$LINK);
	mysql_query("update $Tablap set dif=datediff(actualiza,ing)");
	$Dias=mysql_query("select dif,count(id) as cantidad from $Tablap group by dif order by dif",$LINK);
	if(mysql_num_rows($Dias))
	{
		$Tdias=0;while($D=mysql_fetch_object($Dias)) $Tdias+=$D->cantidad;
		mysql_data_seek($Dias,0);
		if($Tdias)
		{
			echo "<table border cellspacing=0><tr><th>Tiempo</th><th>Cantidad</th><th>%</th></tr>";
			while($D=mysql_fetch_object($Dias))
			{
				$Porc=round($D->cantidad/$Tdias*100,2);
				echo "<tr><td align='center'>$D->dif</td><td align='right'>".coma_format($D->cantidad)."</td><td align='right'>".coma_formatd($Porc,2)."</td></tr>";
			}
			echo "</table>";
		}
	}

	echo "</td>";

	echo "</TR>";
	///     CALCULO DE TIEMPOS Y EVENTOS     Repeticiones de solicitud de actualizacion (4)
	mysql_data_seek($Periodos, 0);
	echo "<TR>";

	while($Per=mysql_fetch_object($Periodos))
	{
		echo "<td align='center' valign='top'>";
		$Tablap=$Tabla.'_S'.$Per->periodo;
		echo "<center><b>Solicitudes de Actualización</b></center> ";
		mysql_query("drop table if exists $Tablap",$LINK);
		if(!mysql_query("create table $Tablap select t.si_id as id, count(se.id) as solicita from $Tabla t,seguimiento se
		where se.siniestro=t.si_id and
		date_format(t.fec_autorizacion,'%Y%m')='$Per->periodo' and t.estado='NO ADJUDICADO' and t.info_erronea=1 and se.tipo=4
		group by t.si_id order by t.si_id",$LINK)) echo "Error en: ".mysql_error($LINK);
		$Dias=mysql_query("select solicita,count(id) as cantidad from $Tablap group by solicita order by solicita",$LINK);
		if(mysql_num_rows($Dias))
		{
			$Tdias=0;while($D=mysql_fetch_object($Dias)) $Tdias+=$D->cantidad;
			mysql_data_seek($Dias,0);
			if($Tdias)
			{
				echo "<table border cellspacing=0><tr><th>Solicitudes</th><th>Cantidad</th><th>%</th></tr>";
				while($D=mysql_fetch_object($Dias))
				{
					$Porc=round($D->cantidad/$Tdias*100,2);
					echo "<tr><td align='center'>$D->solicita</td><td align='right'>".coma_format($D->cantidad)."</td><td align='right'>".coma_formatd($Porc,2)."</td></tr>";
				}
				echo "</table>";
			}
		}

		echo "</td>";
	}
	echo "<td align='center' valign='top'>";
	$Tablap=$Tabla.'_Stodos';
	echo "<center><b>Solicitudes de Actualización</b></center> ";
	mysql_query("drop table if exists $Tablap",$LINK);
	if(!mysql_query("create table $Tablap select t.si_id as id, count(se.id) as solicita from $Tabla t,seguimiento se
		where se.siniestro=t.si_id and estado='NO ADJUDICADO' and t.info_erronea=1 and se.tipo=4
		group by t.si_id order by t.si_id",$LINK)) echo "Error en: ".mysql_error($LINK);
	$Dias=mysql_query("select solicita,count(id) as cantidad from $Tablap group by solicita order by solicita",$LINK);
	if(mysql_num_rows($Dias))
	{
		$Tdias=0;while($D=mysql_fetch_object($Dias)) $Tdias+=$D->cantidad;
		mysql_data_seek($Dias,0);
		if($Tdias)
		{
			echo "<table border cellspacing=0><tr><th>Solicitudes</th><th>Cantidad</th><th>%</th></tr>";
			while($D=mysql_fetch_object($Dias))
			{
				$Porc=round($D->cantidad/$Tdias*100,2);
				echo "<tr><td align='center'>$D->solicita</td><td align='right'>".coma_format($D->cantidad)."</td><td align='right'>".coma_formatd($Porc,2)."</td></tr>";
			}
			echo "</table>";
		}
	}

	echo "</td>";

	echo "</TR>";

	///     CALCULO DE TIEMPOS Y EVENTOS     Contacto exitoso despues de la actualizacion (3)
	mysql_data_seek($Periodos, 0);
	echo "<TR>";

	while($Per=mysql_fetch_object($Periodos))
	{
		echo "<td align='center' valign='top'>";
		$Tablap1=$Tabla.'_C'.$Per->periodo;
		$Tablap=$Tabla.'_A'.$Per->periodo;
		echo "<center><b>Contacto después de Actualización</b></center> ";
		mysql_query("drop table if exists $Tablap1",$LINK);
		if(!mysql_query("create table $Tablap1 select t.si_id as id,t1.actualiza,se.fecha as cex from $Tabla t,$Tablap t1,seguimiento se
		where se.siniestro=t.si_id and t1.id=t.si_id and t1.id=se.siniestro and se.fecha>=t1.actualiza and
		date_format(t.fec_autorizacion,'%Y%m')='$Per->periodo' and t.estado='NO ADJUDICADO' and t.info_erronea=1 and se.tipo=3
		group by t.si_id order by t.si_id",$LINK)) echo "Error en: ".mysql_error($LINK);
		mysql_query("alter table $Tablap1 add column dif integer(10) not null",$LINK);
		mysql_query("update $Tablap1 set dif=datediff(cex,actualiza)");
		$Dias=mysql_query("select dif,count(id) as cantidad from $Tablap1 group by dif order by dif",$LINK);
		if(mysql_num_rows($Dias))
		{
			$Tdias=0;while($D=mysql_fetch_object($Dias)) $Tdias+=$D->cantidad;
			mysql_data_seek($Dias,0);
			if($Tdias)
			{
				echo "<table border cellspacing=0><tr><th>Tiempo</th><th>Cantidad</th><th>%</th></tr>";
				while($D=mysql_fetch_object($Dias))
				{
					$Porc=round($D->cantidad/$Tdias*100,2);
					echo "<tr><td align='center'>$D->dif</td><td align='right'>".coma_format($D->cantidad)."</td><td align='right'>".coma_formatd($Porc,2)."</td></tr>";
				}
				echo "</table>";
			}
		}

		echo "</td>";
	}
	echo "<td align='center' valign='top'>";
	$Tablap=$Tabla.'_Atodos';
	$Tablap1=$Tabla.'_Ctodos';
	echo "<center><b>Contacto después de Actualización</b></center> ";
	mysql_query("drop table if exists $Tablap1",$LINK);
	if(!mysql_query("create table $Tablap1 select t.si_id as id,t1.actualiza,se.fecha as cex from $Tabla t,$Tablap t1,seguimiento se
	where se.siniestro=t.si_id and t1.id=t.si_id and t1.id=se.siniestro and se.fecha>=t1.actualiza and
	t.estado='NO ADJUDICADO' and t.info_erronea=1 and se.tipo=3
	group by t.si_id order by t.si_id",$LINK)) echo "Error en: ".mysql_error($LINK);
	mysql_query("alter table $Tablap1 add column dif integer(10) not null",$LINK);
	mysql_query("update $Tablap1 set dif=datediff(cex,actualiza)");
	$Dias=mysql_query("select dif,count(id) as cantidad from $Tablap1 group by dif order by dif",$LINK);
	if(mysql_num_rows($Dias))
	{
		$Tdias=0;while($D=mysql_fetch_object($Dias)) $Tdias+=$D->cantidad;
		mysql_data_seek($Dias,0);
		if($Tdias)
		{
			echo "<table border cellspacing=0><tr><th>Tiempo</th><th>Cantidad</th><th>%</th></tr>";
			while($D=mysql_fetch_object($Dias))
			{
				$Porc=round($D->cantidad/$Tdias*100,2);
				echo "<tr><td align='center'>$D->dif</td><td align='right'>".coma_format($D->cantidad)."</td><td align='right'>".coma_formatd($Porc,2)."</td></tr>";
			}
			echo "</table>";
		}
	}

	echo "</td>";

	echo "</TR>";

	mysql_close($LINK);
	echo "</tr></table>";
}
else
{
	echo "No puedo obtener los periodos de $Tabla ";
}

















?>