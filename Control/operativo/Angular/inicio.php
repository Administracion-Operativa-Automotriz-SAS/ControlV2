<?php
include "incluir/validar-sesion.php";
include "incluir/cabecera.php";
?>
<link href="estilos/mxkollection3.css" rel="stylesheet" type="text/css" />
<script language='javascript' src='popcalendar.js'></script>
<?php
//Listar productividad agentes y coordinador
 if($_SESSION['usuario']['idcargo']==1 OR $_SESSION['usuario']['USU_ID']==715 OR $_SESSION['usuario']['USU_ID']==714)
 {
?>
<div align="left">
  <p><img src="img/arrow.gif" width="20" height="20" /> <span class="style2 caption2"><strong>Productividad de hoy</strong></span></p>
  <p><img src="img/divisor.jpg" width="467" height="2" /></p>
  <form action="inicio.php" method="get" name="form1">
  <tr>
    <td width="490">
	<table border='0' class='tabdata'>
      <tr>
        <td><strong>Fecha Inicial:</strong></td>
        <td><strong>Fecha Final:</strong></td>
        <td></td>
      </tr>
      <tr>
        <td ><input type="text" value="<?php //echo date('Y-m-d h:i:s')?>" name="txtfecha1" id="txtfecha1" class="fechas"/><td/>
        <td ><input type="text" value="<?php //echo date('Y-m-d h:i:s')?>" name="txtfecha2" id="txtfecha2" class="fechas"/></td>
        <td ><input name='Ok2' type='submit' id='Ok2' value='consultar' /></td>
      </tr>
    </table></td>
  </tr>
  </form>
  <p>
</div>
      <table class="KT_tngtable" border="0">
	     <tr>
			<td><strong></strong></td>
			<td><strong>Desistidos</strong></td>
			<td><strong>Pre-Aprobados</strong></td>
			<td><strong>Entregados</strong></td>
			<td><strong>Rechazados</strong></td>
			<td><strong>Pendientes</strong></td>
			<td><strong>Aprobado Equivalencias</strong></td>
			<td><strong>Aprobado Suppla</strong></td>
			<td><strong>No-Eventos</strong></td>
            <td><strong>Total Casos</strong></td>			
			<td><strong>Llamadas</strong></td>
			<td><strong>Solicitudes Asignadas</strong></td>
		  </tr>
		<?php
		
		    $total_desistidos=0;
		    $total_preaprobados=0; 
			$total_entregados=0;
			$total_rechazados=0;
			$total_pendientes=0;
			$total_noeventos=0;
			$total_llamadas=0;
			$total_solicitudes=0;
			$total_aprob_equiv=0;
			$total_aprob_suppla=0;
			
            $fecha1 = $_GET['txtfecha1'];
            $fecha2 = $_GET['txtfecha2'];
            
            if($fecha1 and $fecha2)  
			{ $whererango="$fecha1, $fecha2";
			   
			   $sqleventos2 = "select  b.usu_nombres as usuario, c.ese_nombre estado_evento , a.eve_id as id , ese_eve_fecha as fecha , 'evento' as tipo FROM seguros.aon_ese_eve a  inner join seguros.aon_usuarios b on a.usu_id=b.usu_id inner join seguros.aon_estado_evento c  on a.ese_id=c.ese_id where date(ese_eve_fecha)>='$fecha1' and date(ese_eve_fecha)<='$fecha2' union select e.usu_nombres as usuario, f.estado as estado_evento, d.id as id, f.fecha as fecha, 'no evento' as tipo from otros_tickets d inner join aon_usuarios e on d.usuario = e.usu_id inner join otros_tickets_estado f on d.id_estado = f.id where date(f.fecha)>='$fecha1' and date(f.fecha)<='$fecha2' order by fecha";		       
			   
			   $sqlllamadas2 = "SELECT * FROM llamadas.registro ";
			   $sqlllamadas2 .= "WHERE date(fechainicio)>='$fecha1' and date(fechainicio)<='$fecha2' order by id DESC ";
		
		    }else{
		    	$sqleventos2 = "select b.usu_nombres as usuario, c.ese_nombre estado_evento , a.eve_id as id , a.ese_eve_fecha as fecha , 'evento' as tipo FROM seguros.aon_ese_eve a inner join seguros.aon_usuarios b on a.usu_id=b.usu_id inner join seguros.aon_estado_evento c on a.ese_id=c.ese_id where date(a.ese_eve_fecha)=date(curdate())  union select e.usu_nombres as usuario, f.estado as estado_evento, d.id as id, f.fecha as fecha, 'no evento' as tipo from otros_tickets d inner join aon_usuarios e on d.usuario = e.usu_id inner join otros_tickets_estado f on d.id_estado = f.id where date(f.fecha)=date(curdate()) order by fecha";

		    	//echo $sqleventos2; 

			   $sqlllamadas2 = "SELECT * FROM llamadas.registro ";
			   $sqlllamadas2 .= "WHERE date(fechainicio)>=date(curdate()) order by id DESC ";			   
				
			}
			
			$link=mysqli_connect("localhost", "protegeme", "@c!nco2016*", "seguros");
			$link->query("SET NAMES 'utf8'");
			$sql= "SELECT * from seguros.aon_usuarios where idcargo in (2,3) and USU_BLOQUEADO='NO' order by USU_LOGIN";
			$result=mysqli_query($link, $sql);
			$i=0;
			while ($row=mysqli_fetch_row($result)) 
			{
				 $desis= consultar_casos($row[0],5,$fecha1, $fecha2);
				 $prea= consultar_casos($row[0],13,$fecha1, $fecha2);
				 $entr= consultar_casos($row[0],3,$fecha1, $fecha2);
				 $rech= consultar_casos($row[0],4,$fecha1, $fecha2);
				 $pend= consultar_casos($row[0],1,$fecha1, $fecha2);
				 $noev= consultar_no_eventos($row[0],$fecha1, $fecha2);
				 $aprobequiv= consultar_casos($row[0],15,$fecha1, $fecha2);
				 $aprobsuppla= consultar_casos($row[0],16,$fecha1, $fecha2);
				 
				 $totalcasos= $prea + $entr + $rech + $pend + $noev + $desis + $aprobequiv + $aprobsuppla;
				 $llam= consultar_llamadas($row[3],$fecha1, $fecha2);
				 $solw= consultar_solicitudesweb($row[0],$fecha1, $fecha2);
					 
				echo "<tr align='center'>";
				echo "<td>".$row[3]."</td>";
				echo "<td>$desis</td>";  $total_desistidos = $total_desistidos + $desis; 
				echo "<td>$prea</td>";  $total_preaprobados = $total_preaprobados + $prea;
				echo "<td>$entr</td>";  $total_entregados = $total_entregados  + $entr;
				echo "<td>$rech</td>";  $total_rechazados = $total_rechazados + $rech;
				echo "<td>$pend</td>";  $total_pendientes = $total_pendientes + $pend;
				echo "<td>$aprobequiv</td>";  $total_aprob_equiv = $total_aprob_equiv + $aprobequiv;
				echo "<td>$aprobsuppla</td>";  $total_aprob_suppla = $total_aprob_suppla + $aprobsuppla;
				
				echo "<td>$noev</td>";  $total_noeventos = $total_noeventos + $noev; 
				echo "<td bgcolor='#d5d1d1'>$totalcasos</td>";  
				echo "<td>$llam</td>";  $total_llamadas = $total_llamadas + $llam;
				echo "<td>$solw</td>";  $total_solicitudes = $total_solicitudes + $solw;
				echo "<tr>";
				$totalcasos2= $total_preaprobados + $total_entregados + $total_rechazados + $total_pendientes + $total_noeventos + $total_desistidos + $total_aprob_equiv + $total_aprob_suppla;
			}
			mysqli_free_result($result);
			mysqli_close($link);
			echo "<strong><tr align='center'><td><strong>TOTAL</strong></td><td>$total_desistidos</td><td>$total_preaprobados</td><td>$total_entregados</td><td>$total_rechazados</td><td>$total_pendientes</td><td>$total_aprob_equiv</td><td>$total_aprob_suppla</td><td>$total_noeventos</td><td bgcolor='#d5d1d1'>$totalcasos2</td><td>$total_llamadas</td><td>$total_solicitudes</td></tr></strong>";
		?> 
 	  </table>
<?php
            if($fecha1 and $fecha2)  
			{ echo "Consulta de $fecha1 a $fecha2";}
		    ?>
					<table>
						<tr><td>
						 <form name="form1" method="post" action="expor_arc2.php" > 
							  <input type="text" name="consulta" value="<?php echo $sqleventos2; ?>" hidden/>                           					 
							  <input type="submit" name="export_excel" class="boton" value="Exportar eventos" />  
						 </form></td>  
						 <td><form name="form2" method="post" action="expor_arc2.php" > 
							  <input type="text" name="consulta" value="<?php echo $sqlllamadas2; ?>" hidden/>                           					 
							  <input type="submit" name="export_excel" class="boton" value="Exportar Llamadas" />  
						 </form></td>
						 </tr>
			      </table>
<?php             
 }
?>
<?php 
if($_SESSION['usuario']['idcargo']==3 or $_SESSION['usuario']['idcargo']==2)
 {
?>
	<table>
		<tr>
		<td>Casos gestionados hoy:</td>
		<td>Desistidos: <font color="red" size="3"><strong><?php echo consultar_casos($_SESSION['usuario']['USU_ID'], 5); ?></strong></font></td>
		<td>Pre-Aprobados: <font color="red" size="3"><strong><?php echo consultar_casos($_SESSION['usuario']['USU_ID'], 13); ?></strong></font></td>
		<td>Entregados: <font color="red" size="3"><strong><?php echo consultar_casos($_SESSION['usuario']['USU_ID'], 3); ?></strong></font></td>
		<td>Rechazados: <font color="red" size="3"><strong><?php echo consultar_casos($_SESSION['usuario']['USU_ID'], 4); ?></strong></font></td>
		<td>Pendientes: <font color="red" size="3"><strong><?php echo consultar_casos($_SESSION['usuario']['USU_ID'], 1); ?></strong></font></td>
		<td>Aprob.Equivalencias: <font color="red" size="3"><strong><?php echo consultar_casos($_SESSION['usuario']['USU_ID'], 15); ?></strong></font></td>
		<td>Aprob.Suppla: <font color="red" size="3"><strong><?php echo consultar_casos($_SESSION['usuario']['USU_ID'], 16); ?></strong></font></td>
		<td>No Eventos: <font color="red" size="3"><strong><?php echo consultar_no_eventos($_SESSION['usuario']['USU_ID']); ?></strong></font></td>
		</tr>
		<tr>
		<td colspan="5">Llamadas Registradas hoy: <font color="red" size="3"><strong><?php echo consultar_llamadas($_SESSION['usuario']['USU_LOGIN']); ?></strong></font></td>
		</tr>
		<tr>
		<td colspan="5">Solicitudes Web Asignadas:  <font color="red" size="3"><strong><?php echo consultar_solicitudesweb($_SESSION['usuario']['USU_ID'],'',''); ?></strong></font></td>
		</tr>
	</table>
<?php } ?>
<?php 
include "incluir/pie.php";
?>