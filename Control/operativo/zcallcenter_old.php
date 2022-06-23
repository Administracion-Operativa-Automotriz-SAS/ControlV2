<?php

/**
 * Call Center 2
 *
 * @version $Id$
 * @copyright 2011
 */

include('inc/funciones_.php');
sesion();
$Disp_flota=array();$Disp_aoa=array();$Estado=array();$Citado=array();$Pyp=array();

$USUARIO=$_SESSION['User'];
$Nick=$_SESSION['Nick'];
$NUSUARIO=$_SESSION['Nombre'];
$Hoy=date('Y-m-d');
$Ahora=date('Y-m-d H:i:s');

if(!empty($Acc) && function_exists($Acc)){	eval($Acc.'();');	die();}

function Verifica_variables($Dato,$id,$LINK=0)		# REEMPLAZO DE VALORES DE VARIABLES  DENTRO DE LAS FORMULAS
{
	global $USUARIO,$Nick,$NUSUARIO,$Hoy,$Ahora;
	$Cerrar=false;
	if(!$LINK) {include('inc/link.php');$Cerrar=true;	}
	$D=qom("select * from siniestro where id=$id",$LINK);
	$Aseguradora=qom("select * from aseguradora where id=$D->aseguradora",$LINK);
	$Oficina=qom("select *,t_ciudad(ciudad) as nciudad from oficina where ciudad='$D->ciudad' ",$LINK);
	if(strpos(' '.$Dato,'[NOMBRE_USUARIO]')) $Dato=str_replace("[NOMBRE_USUARIO]",' <b>'.$NUSUARIO.'</b> ',$Dato);
	if(strpos(' '.$Dato,'[NOMBRE_ASEGURADO]')) $Dato=str_replace("[NOMBRE_ASEGURADO]",' <b>['.$D->asegurado_nombre.'] - '.$D->declarante_nombre.'</b> ',$Dato);
	if(strpos(' '.$Dato,'[GARANTIA_ASEGURADORA]')) $Dato=str_replace("[GARANTIA_ASEGURADORA]",' <b>'.coma_format($Aseguradora->garantia).'</b> ',$Dato);
	if(strpos(' '.$Dato,'[DIRECCION_OFICINA]')) $Dato=str_replace("[DIRECCION_OFICINA]"," <b>$Oficina->direccion [$Oficina->nciudad]</b> ",$Dato);
	if(strpos(' '.$Dato,'[NOMBRE_SERVICIO]')) $Dato=str_replace("[NOMBRE_SERVICIO]"," <b>".strtoupper($Aseguradora->nombre_servicio)."</b> ",$Dato);
	if($Cerrar) mysql_close($LINK);
	return $Dato;
}

inicio_callcenter();

function inicio_callcenter()
{
	global $Hoy,$NUSUARIO;
	html('Control de Call Center');
	echo "<script language='javascript'>

			var Caso=new Array();
			var Recargar='';

			function segclass(siniestro,fecha,hora,usuario,tipo)
			{
				this.Siniestro=siniestro;
				this.Fecha=fecha;
				this.Hora=hora;
				this.Usuario=usuario;
				this.Tipo=tipo;
			}

			function fijar()
			{
				document.getElementById('Detalle_callcenter').style.height=document.body.clientHeight-250;
				recargar_control_compromisos();
				pinta_hora_exacta();
			}

			function validar()
			{
				with(document.forma)
				{
					var Aseguradoras=getSelected(Ase);
					var Aseg=''; for(var i=0;i<Aseguradoras.length;i++) { if(i>0) Aseg+=',';Aseg+=Aseguradoras[i].value;}
					Asegs.value=Aseg;
					var Oficinas=getSelected(Ofi);
					var Ofic=''; for(var i=0;i<Oficinas.length;i++) { if(i>0) Ofic+=',';Ofic+=Oficinas[i].value;}
					Ofics.value=Ofic;
					submit();
				}
			}

			function recargar_control_compromisos()
			{
				var Aseguradoras=getSelected(document.forma.Ase);
				var Aseg='';
				for(var i=0;i<Aseguradoras.length;i++)
				{
					if(i>0) Aseg+=',';
					Aseg+=Aseguradoras[i].value;
				}
				var Oficinas=getSelected(document.forma.Ofi);
				var Ofic='';
				for(var i=0;i<Oficinas.length;i++)
				{
					if(i>0) Ofic+=',';
					Ofic+=Oficinas[i].value;
				}
				window.open('zcallcenter.php?Acc=control_compromisos&Aseg='+Aseg+'&Ofic='+Ofic,'control_compromisos');
				Recargar=setTimeout(recargar_control_compromisos,100000);
			}

			function pinta_hora_exacta()
			{
				var Fecha=new Date();
				var Hora=Fecha.getHours()+':'+Fecha.getMinutes()+':'+Fecha.getSeconds();
				document.getElementById('hora_exacta').innerHTML=Hora;
				Repinta_hora=setTimeout(pinta_hora_exacta,1000);
			}

			function Compr(id,numero)
			{
				modal('zcompromiso.php?Acc=ver_compromisos&id='+id+'&DesdeCall=1',30,30,500,800,'ex_compromisos');
				document.forma.av_numero.value=numero;
				document.forma.av_placa.value='';
				document.forma.av_asegurado.value='';
				validar();
			}

		</script>
		<body bgcolor='eeeeee' onresize='fijar();'>";
	$Mes=date('Y-m');
	$Numero_citas=qo1("select count(id) from cita_servicio where date_format(fecha,'%Y-%m')='$Mes' and estado='C' and agendada_por like '%$NUSUARIO%' ");
	echo "<h3>AOA. CENTRO DE CONTROL CALL CENTER  <span style='background-color:ccccff'>[ Fecha: ".fecha_completa($Hoy)."</span> <span id='hora_exacta'></span> ]
				Citas Efectivas del mes: $NUSUARIO : $Numero_citas </H3>
		<form action='zcallcenter.php' method='post' target='Detalle_callcenter' name='forma' id='forma'>
			<input type='hidden' name='Acc' value='informe_callcenter'>
			<input type='hidden' name='Asegs' value=''>
			<input type='hidden' name='Ofics' value=''>
			<table width='100%'><tr><td valign='top' width='140px'>
			<b style='background-color:000000;color:ffffff'>Aseguradora:</b><br /><select name='Ase' multiple size=8>";

//	$NUSUARIO='LINA MARIA DOMINGUEZ';

	$Aseguradoras=q("select * from aseguradora where id!=6");
	while($A=mysql_fetch_object($Aseguradoras)) echo "<option value='$A->id' selected>$A->nombre</option>";
			echo "</select></td><td valign='top' width='120px'><b style='background-color:000000;color:ffffff'>Oficinas:</b><br /><select name='Ofi' multiple size=8>";
	$Oficinas=q("select ciudad,nombre from oficina");
	while($O=mysql_fetch_object($Oficinas)) echo "<option value='$O->ciudad' selected>$O->nombre</option>";
	echo "</select></td>
			<td align='center' valign='top' width='160px'>
				<table cellspacing='0' cellpadding='0'><tr><th colspan=2>Busqueda Avanzada:</th></tr>
				<tr><td align='right'>Placa:</td><td><input type='text' name='av_placa' size='7' alt='DIGITE LA PLACA DEL VEHICULO DEL ASEGURADO' title='DIGITE LA PLACA DEL VEHICULO DEL ASEGURADO'></td></tr>
				<tr><td align='right'>No. Siniestro:</td><td><input type='text' name='av_numero' size='10'></td></tr>
				<tr><td align='right'>Nombre::</td><td><input type='text' name='av_asegurado' size='10'></td></tr>
				<tr><td colspan=2>Ver mis compromisos <input type='checkbox' name='miscompromisos'></td></tr>
				<tr><td align='center' colspan=2><input type='button' value='CONSULTAR' onclick='validar()' style='width:150px'></td></tr>
				</table>
		</td>
			<td valign='top'>
			<iframe name='control_compromisos' id='control_compromisos' height='200px' width='100%' border='0' frameborder='no' src='zcallcenter.php?Acc=control_compromisos'></iframe>
			</td>
			</tr></table>
		</form>

		<iframe name='Detalle_callcenter' id='Detalle_callcenter' width='100%' height='200px' frameborder='no'></iframe>
		<script language='javascript'>fijar();</script>
		</body>";
}

function informe_callcenter()
{
	global $Asegs,$Ofics,$USUARIO,$Nick,$av_placa,$av_numero,$av_asegurado,$miscompromisos,$NUSUARIO;
	$miscompromisos=sino($miscompromisos);
	$Tseguimiento=tu('seguimiento','id');
	$Rango_tiempo1=60*60*4;
	$Rango_tiempo2=60*60*12;
	html('Informe Call Center');
	echo "
		<style tyle='text/css'>
		li.ciudad {cursor:pointer;color:000000;}
		li.ciudad:hover {color:ff5500;}
		li.depart {cursor:pointer;color:004400;}
		li.depart:hover {color:ff5500;font-weight:bold;}
		ul {list-style-image: url(gifs/mas_opciones.png);}
		</style>
		<script language='javascript'>
			var Caso=new Array();
			var Compromiso=new Array();
			var Casos_oficina=new Array();

			function segclass(siniestro,fecha,hora,usuario,tipo,ntipo)
			{  this.Siniestro=siniestro; this.Fecha=fecha; this.Hora=hora; this.Usuario=usuario; this.Tipo=tipo; this.Ntipo=ntipo; }

			function sinclass(id,ingreso)
			{ this.Id=id; this.Ingreso=ingreso; }

			function compclass(id,fecha,hora,usuario,descripcion,estado)
			{ this.Id=id; this.Fecha=fecha+' '+hora; this.Usuario=usuario; this.Descripcion=descripcion; this.Estado=estado; }

			function abre_cierra(id,nombre)
			{ with(document.getElementById(nombre+id).style)
				{
					if(visibility=='hidden')
					{ visibility='visible'; position='relative'; if(nombre=='Ofi') pinta_caso(id);  }
					else
					{ visibility='hidden'; position='absolute'; }
				}
			}

			function Callc(id_siniestro) { modal('zcallcenter.php?Acc=inicio_proceso_call&id_siniestro='+id_siniestro,0,0,600,900,'ca'); }

			function Compr(id)
			{ document.getElementById('if_compromisos').style.visibility='visible'; window.open('zcompromiso.php?Acc=ver_compromisos&id='+id+'&DesdeCall=1','if_compromisos'); }

			function ver_seguimiento(id)
			{ modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Tseguimiento&VINCULOT='+id+'&VINCULOC=siniestro',0,0,500,800,'seguimiento'); }

			function cierra_compromisos()
			{ document.getElementById('if_compromisos').style.visibility='hidden'; }

			function pinta_caso(oficina)
			{
				var Hoy=new Date();
				var bk='';
				Hoy=dmyhms(Hoy);
				var idcaso=0; var Ingreso=''; var Tiempo=0; var Procesado=false; var Contacto_exitoso=false; var Contacto_3_persona=false; var Mensaje_buzon=false;var Remision=false;
				var procesados=0;var verdes=0; var amarillos=0; var rojos=0;var nocontacto=0;var Ultimo_seguimiento='';var Tiempo_ultimo_seguimiento=0;var Ultimoseg='';var Culitmoseg='';
				for(var i=0;i<Casos_oficina[oficina].length;i++)
				{
					idcaso=Casos_oficina[oficina][i].Id;
					Ingreso=Casos_oficina[oficina][i].Ingreso;
					Tiempo=segundos_habiles(Ingreso,Hoy);

					Procesado=false;Contacto_exitoso=false;Contacto_3_persona=false;Mensaje_buzon=false;Remision=false;
					for(var j=0;j<Caso[idcaso].length;j++)
					{
						var Tipo=Caso[idcaso][j].Tipo;
						if(Tipo>2 && Tipo!=9 && Tipo!=10)  // busca los casos despues de consulta y que no sean modificaciones.
							Procesado=true;
						if(Tipo==3) Contacto_exitoso=true;
						if(Tipo==11 && !Contacto_exitoso) Contacto_3_persona=true;
						if(Tipo==4) Remision=true;
						if(Tipo==12) Mensaje_buzon=true;
						Ultimo_seguimiento=Caso[idcaso][j].Fecha+' '+Caso[idcaso][j].Hora+' '+Caso[idcaso][j].Ntipo;
					}

					Tiempo_ultimo_seguimiento=segundos_habiles(Ultimo_seguimiento,Hoy);
					Ultimoseg=segundos2horas(Tiempo_ultimo_seguimiento);
					if(Tiempo_ultimo_seguimiento<$Rango_tiempo1) Cultimoseg='55ff55';
					if(Tiempo_ultimo_seguimiento>=$Rango_tiempo1 && Tiempo_ultimo_seguimiento<$Rango_tiempo2 ) Cultimoseg='ffff55';
					if(Tiempo_ultimo_seguimiento>=$Rango_tiempo2 ) Cultimoseg='ff5555';

					if(!Procesado)
					{
						if(Tiempo<$Rango_tiempo1) {document.getElementById('sp1_'+idcaso).style.backgroundColor='55ff55';verdes++;}
						if(Tiempo>=$Rango_tiempo1 && Tiempo<$Rango_tiempo2) {document.getElementById('sp1_'+idcaso).style.backgroundColor='ffff55';amarillos++}
						if(Tiempo>=$Rango_tiempo2) {document.getElementById('sp1_'+idcaso).style.backgroundColor='ff5555';rojos++}
						document.getElementById('sp1_'+idcaso).innerHTML=segundos2horas(Tiempo);
					}
					else
					{ procesados++; document.getElementById('sp1_'+idcaso).innerHTML=\"<img src='gifs/standar/si.png' border='0'>\"; }

					if(Remision)
					{document.getElementById('sp5_'+idcaso).innerHTML=\"<a class='info'><img src='gifs/send_mail.png' border='0' height='18'><span>Remitido a la Aseguradora</span></a>\";}


					if(Contacto_exitoso)
					{
						document.getElementById('sp2a_'+idcaso).innerHTML=\"<a class='info'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='gifs/persona.gif' border='0'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style='width:100px'>Contacto Exitoso</span></a> \";
						document.getElementById('sp2b_'+idcaso).innerHTML=Ultimo_seguimiento;
						document.getElementById('sp2c_'+idcaso).innerHTML=Ultimoseg;
						document.getElementById('sp2c_'+idcaso).style.backgroundColor=Cultimoseg;
					}
					else if(Contacto_3_persona)
						{
							document.getElementById('sp2a_'+idcaso).innerHTML=\"<a class='info'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='gifs/persona01.gif' border='0'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style='width:100px'>Contacto tercera persona</span></a> \";
							document.getElementById('sp2b_'+idcaso).innerHTML=Ultimo_seguimiento;
							document.getElementById('sp2a_'+idcaso).style.backgroundColor='ffffcc';
							document.getElementById('sp2c_'+idcaso).innerHTML=Ultimoseg;
							document.getElementById('sp2c_'+idcaso).style.backgroundColor=Cultimoseg;
						}
					else
					{
						nocontacto++;
						if(Procesado)
						{ document.getElementById('sp2b_'+idcaso).innerHTML=Ultimo_seguimiento; document.getElementById('sp2c_'+idcaso).innerHTML=Ultimoseg; document.getElementById('sp2c_'+idcaso).style.backgroundColor=Cultimoseg; }
					}
					if(Mensaje_buzon)
						document.getElementById('sp3_'+idcaso).innerHTML=\"<a class='info'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='gifs/buzon_sugerencias.png' border='0' height='18'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style='width:100px'>Mensaje en buzón de voz</span></a> \";

					if(Compromiso[idcaso])
					{
						compromiso_caso='';
						for(var j=0;j<Compromiso[idcaso].length;j++)
						{
							if(Compromiso[idcaso][j].Usuario=='$Nick') bk=\" style='background-color:ffff44' \"; else bk='';
							var spcomp=\"<span><table border='0' bgcolor='000000' width='200px'><tr><td bgcolor='ffffff'><b>COMPROMISO</b><br>Usuario: \"+Compromiso[idcaso][j].Usuario+\"<br>Fecha: \";
							spcomp+=Compromiso[idcaso][j].Fecha+'<br>'+Compromiso[idcaso][j].Descripcion+\"<br>Estado: \";
							if(Compromiso[idcaso][j].Estado=='P') spcomp+=\"Pendiente\"; else spcomp+=\"Cumplido\";
							spcomp+=\"</td></tr></table></span>\";
							if(Compromiso[idcaso][j].Estado=='P')
							{
								if(Compromiso[idcaso][j].Fecha>Hoy)
									compromiso_caso+=\"<a class='rinfo' \"+bk+\" id='fl_\"+Compromiso[idcaso][j].Id+\"'>&nbsp;<img src='gifs/standar/seguir_amarillo.png' border='0' height='14'>&nbsp;\"+spcomp+\"</a>\";
								else
									compromiso_caso+=\"<a class='rinfo' \"+bk+\"  id='fl_\"+Compromiso[idcaso][j].Id+\"'>&nbsp;<img src='gifs/standar/seguir_ovr.png' border='0' height='14'>&nbsp;\"+spcomp+\"</a>\";
							}
							else
								compromiso_caso+=\"<a class='rinfo' \"+bk+\" id='fl_\"+Compromiso[idcaso][j].Id+\"'>&nbsp;<img src='gifs/standar/seguir.png' border='0' height='14'>&nbsp;\"+spcomp+\"</a>\";
						}
						document.getElementById('sp4_'+idcaso).innerHTML=compromiso_caso;
					}
				}
				document.getElementById('sp3_'+oficina).innerHTML=\" Procesados: \"+procesados+\" No procesados: [<b style='background-color:aaffaa;'> \"+verdes+\" </b>] [<b style='background-color:ffffaa;'> \"+amarillos+\" </b>] [<b style='background-color:ffaaaa;'> \"+rojos+\" </b>] Sin contacto exitoso: <b>\"+nocontacto+\"</b>\";
			}

			function retira_compromiso(idSiniestro,idCompromiso)
			{
				document.getElementById('fl_'+idCompromiso).innerHTML='';
				for(var i=0;i<Compromiso[idSiniestro].length;i++)
				{ if(Compromiso[idSiniestro][i].Id==idCompromiso) { Compromiso[idSiniestro].splice(i,1); return; } }
			}

			function adiciona_compromiso(idCompromiso,fecha,hora,usuario,descripcion,idSiniestro,estado)
			{
				var Hoy=new Date();
				Hoy=dmyhms(Hoy);
				if(!Compromiso[idSiniestro]) Compromiso[idSiniestro]=new compclass(idCompromiso,fecha,hora,usuario,descripcion,estado);
				else Compromiso[idSiniestro].push(new compclass(idCompromiso,fecha,hora,usuario,descripcion,estado));
				if(fecha>Hoy)
					document.getElementById('sp4_'+idSiniestro).innerHTML+=\"<a class='info'  id='fl_\"+idCompromiso+\"'><img src='gifs/standar/seguir.png' border='0'> \"+fecha+\"<span style='width:200px'><b>COMPROMISO</b><br>Fecha: \"+fecha+\"<br>\"+descripcion+\"</span></a>\";
				else
					document.getElementById('sp4_'+idSiniestro).innerHTML+=\"<a class='info'  id='fl_\"+idCompromiso+\"'><img src='gifs/standar/seguir_ovr.png' border='0'> \"+fecha+\"<span style='width:200px'><b>COMPROMISO VENCIDO</b><br>Fecha: \"+fecha+\"<br>\"+descripcion+\"</span></a>\";
			}

			function tachar(id)
			{ document.getElementById('tr_'+id).style.backgroundColor='550000'; }
			function tachar_adjudicado(id)
			{ document.getElementById('tr_'+id).style.backgroundColor='005500'; }
			function marcar_procesado(id)
			{ document.getElementById('tr_'+id).style.backgroundColor='AAAA00'; }

		</script>
		<body bgcolor='ffffff'>
		<iframe name='if_compromisos' id='if_compromisos' style='position:fixed;visibility:hidden;z-index:100' height='300' width='700' ></iframe>
		<script language='javascript'></script>
		";
	$Ofics="'".str_replace(',',"','",$Ofics)."'";

	$Filtro1='';$Filtro2='';
	if($av_placa)
	{
		$Filtro1=" and s.placa like '%$av_placa%' ";
		$Filtro2=" and si.placa like '%$av_placa%' ";
	}
	if($av_asegurado)
	{
		$Filtro1.=" and (concat(s.asegurado_nombre,s.declarante_nombre,s.conductor_nombre) like '%$av_asegurado%' )";
		$Filtro2.=" and (concat(si.asegurado_nombre,si.declarante_nombre,si.conductor_nombre) like '%$av_asegurado%' )";
	}
	if($av_numero)
	{
		$Filtro1.=" and s.numero like '%$av_numero%' ";
		$Filtro2.=" and si.numero like '%$av_numero%' ";
	}
	if($miscompromisos)
	{
		$Filtro1.=" and s.id in (select siniestro from compromiso where usuario like '%$NUSUARIO%' ) ";
	}
	
	$A_pendientes=array();
	if($QPendientes=q("select s.aseguradora,o.id as idoficina,s.id,s.numero,s.placa,s.asegurado_nombre,s.ingreso,s.observaciones,s.chevyseguro,s.retencion,s.no_garantia  
							FROM siniestro s,oficina o
							WHERE s.ciudad=o.ciudad ".($Filtro1?"":"and s.estado=5")." $Filtro1 order by s.aseguradora,o.id,s.ingreso"))
	while($QP=mysql_fetch_object($QPendientes)) $A_pendientes[$QP->aseguradora][$QP->idoficina][count($A_pendientes[$QP->aseguradora][$QP->idoficina])]=$QP;
	//echo count($A_pendientes);
	
	if($Aseguradoras=q("select distinct a.nombre,a.id from siniestro s,aseguradora a where s.aseguradora=a.id  ".($Filtro1?"":"and s.estado=5")." and a.id in ($Asegs) and s.ciudad in ($Ofics)  $Filtro1 order by a.nombre"))
	{
		if($Seguimientos=q("select si.id,si.ingreso,se.fecha,se.hora,se.usuario,se.tipo,ts.nombre as ntipo FROM seguimiento se,siniestro si,tipo_seguimiento ts
			WHERE si.id=se.siniestro  ".($Filtro2?"":"and si.estado=5")." and si.aseguradora in ($Asegs) and si.ciudad in ($Ofics) $Filtro2 and se.tipo=ts.id order by si.id,se.fecha,se.hora"))
		{
			echo "<script language='javascript'>";
			$Ultimo_id=0;
			$Contador=0;
			while($S=mysql_fetch_object($Seguimientos))
			{
				if($S->id!=$Ultimo_id)
				{ $Ultimo_id=$S->id; echo " Caso[$Ultimo_id]=new Array();"; $Contador=0; }
				//$Tiempo=segundos_habiles($S->ingreso,date('Y-m-d H:i:s'));
				echo " Caso[$Ultimo_id][$Contador]=new segclass($S->id,'$S->fecha','$S->hora','$S->usuario',$S->tipo,'$S->ntipo');";
				$Contador++;
			}
		}
		echo " </script>";
		$Script='';
		include('inc/link.php');
		while($As=mysql_fetch_object($Aseguradoras))   ///  ASEGURADORAS //
		{
			$Cantidad_aseguradora=0;
			echo "<li class='depart' onclick=\"abre_cierra('$As->id','Ase')\"><b>$As->nombre </b> <span id='sp1$As->id' style='color:000099'></span></li><ul id='Ase$As->id' style='visibility:hidden;position:absolute;cursor:pointer;' >";
			$Oficinas=mysql_query("select distinct o.id,o.nombre,o.ciudad from siniestro s, oficina o where s.ciudad=o.ciudad and s.aseguradora=$As->id  ".($Filtro1?"":"and s.estado=5")."
								and s.ciudad in ($Ofics) $Filtro1 order by o.nombre",$LINK);
			$Cantidad_oficina=0;
			while($Of=mysql_fetch_object($Oficinas))  //  OFICINAS  //
			{
				$Idofi=$As->id.'_'.$Of->id;
				echo "<li class='depart' onclick=\"abre_cierra('$Idofi','Ofi')\"><b>$Of->nombre</b> <span id='sp2_$Idofi' style='color:0000bb'></span> <span id='sp3_$Idofi' style='color:000000'></span></li><ul id='Ofi$Idofi' style='visibility:hidden;position:absolute;cursor:pointer;' >";
				//  -*-*-*-*-*-**-**-*-*-*-*-*-*-*-*-*-*-**-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-
			//	$Pendientes=mysql_query("select s.id,s.numero,s.placa,s.asegurado_nombre,s.ingreso,s.observaciones,s.chevyseguro,s.retencion,s.no_garantia  from siniestro s,oficina o
			//												WHERE s.ciudad=o.ciudad and s.aseguradora=$As->id and o.id=$Of->id  ".($Filtro1?"":"and s.estado=5")." $Filtro1
			//												order by s.ingreso",$LINK);
				//  -*-*-*-*-*-**-**-*-*-*-*-*-*-*-*-*-*-**-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-
				//  febrero 2 2012. se adicionó en la consulta de pendientes el campo siniestro.no_garantia para identificar visualmente desde este punto los siniestros cuyo servicio no va a requerir constituir garantía.
			//	$Cantidad_oficina=mysql_num_rows($Pendientes);
				$Cantidad_oficina=count($A_pendientes[$As->id][$Of->id]);
				$Script.="document.getElementById('sp2_$Idofi').innerHTML='[".coma_format($Cantidad_oficina)."]';
								Casos_oficina['$Idofi']=new Array();";
				$Cantidad_aseguradora+=$Cantidad_oficina;
				echo "<table border=0 cellspacing='1'><tr><th>Numero</th><th>Numero</th><th>Asegurado</th><th>Placa</th><th>Ingreso</th><th>Llamar</th><th>Iniciado</th>
							<th>Remitido</th><th>Mensaje/Buzón</th><th>Contacto</th><th colspan=2>Ultimo evento</th><th colspan=2>Compromisos</th></tr>";
				$Contador=1;
			//	while($P=mysql_fetch_object($Pendientes))  //  PENDIENTES //
					
					
				foreach($A_pendientes[$As->id][$Of->id] as $P)	
				{
					echo "<tr id='tr_$P->id'><td align='right' nowrap='yes'>".
						($P->no_garantia?" <a class='info'><img src='img/nogarantia.png' border='0' height='20px' alt='Servicio Sin Garantia' title='Servicio Sin Garantia' align='middle'><span><img src='img/nogarantia.png'><h3>Servicio Sin Garantía</h3></span></a> ":"")
							.($P->chevyseguro?" <a class='info'><img src='img/chevyseguro2.png' border='0' height='20px' alt='Chevyseguro' title='Chevyseguro' align='middle'><span><img src='img/chevyseguro2.png'><h3>ChevySeguro</h3></a>":"").
							" $Contador</td><td nowrap='yes'><a class='info' style='cursor:pointer' onclick='ver_seguimiento($P->id);'>$P->numero ".

							($P->retencion?" <img src='gifs/Help.png' border='0' alt='Retención' title='Retención'>":"")."
							<span style='width:700px'><table border='0' width='700px'><tr><td>Observaciones: ".nl2br($P->observaciones)."</td></tr></table></span></a></td>
								<td>$P->asegurado_nombre</td>
								<td>$P->placa</td>
								<td nowrap='yes'>$P->ingreso</td>
								<td align='center' nowrap='yes'>&nbsp;&nbsp;<a class='info' onclick='Callc($P->id);' style='cursor:pointer;'><img src='img/callphone.png' border=0><span style='width:100px'>Iniciar proceso</span></a>&nbsp;&nbsp;&nbsp;
								<a class='info' onclick=\"modal('zcallcenter.php?Acc=solicita_reactivacion&id=$P->id',0,0,100,100,'reac');\"><img src='gifs/standar/dsn_config.png' border='0'><span>Solicitar Modificacion</span></a>&nbsp;&nbsp;
								</td>
								<td id='sp1_$P->id' align='center'></td><td id='sp5_$P->id' align='center'></td>
								<td id='sp3_$P->id' align='center'></td><td id='sp2a_$P->id' align='center'></td>
								<td id='sp2b_$P->id' align='left'></td><td id='sp2c_$P->id' align='center'></td>
								<td align='center'><a class='info' onclick='Compr($P->id);' style='cursor:pointer;'>
								<img src='gifs/standar/nuevo_registro_blanco.png' border=0><span style='width:100px'>Revisar / Adicionar Compromisos</span></a></td>
								<td id='sp4_$P->id' align='center' nowrap='yes'></td>
								<td width='100'>&nbsp</td>
								</tr>";
					$Cont2=$Contador-1;
					$Script.="
						Casos_oficina['$Idofi'][$Cont2]=new sinclass($P->id,'$P->ingreso');";
					$Contador++;
				}
				echo "</table><br />";
				echo "</ul>";
			}
			$Script.="
				document.getElementById('sp1$As->id').innerHTML='[".coma_format($Cantidad_aseguradora)."]';";
			echo "</ul>";
		}
		echo "<script language='javascript'>$Script";
		if($Compromisos=q("select co.* from compromiso co,siniestro si,aseguradora ase where co.siniestro=si.id and si.aseguradora=ase.id and si.estado=5
		 and ase.id in ($Asegs) and si.ciudad in ($Ofics) order by co.siniestro,co.id "))
		{
			$Contador=0;$Ultimo_id=0;
			while($Co=mysql_fetch_object($Compromisos))
			{
				if($Co->siniestro!=$Ultimo_id)
				{
					$Ultimo_id=$Co->siniestro;
					echo "
							Compromiso[$Ultimo_id]=new Array();";
					$Contador=0;
				}
				$Co->descripcion=str_replace("\n","",$Co->descripcion);
				$Co->descripcion=str_replace("\r","",$Co->descripcion);
				echo "
						Compromiso[$Ultimo_id][$Contador]=new compclass($Co->id,'$Co->fecha','$Co->hora','$Co->usuario',\"$Co->descripcion\",'$Co->estado');";
				$Contador++;
			}
		}
		echo "</script>";
		mysql_close($LINK);
	}
	else echo "<b style='color:ff5555'>No hay información con los criterios dados.</b>";
}

function control_compromisos()
{
	global $USUARIO,$NUSUARIO,$Aseg,$Ofic;
	$Ahora=date('Y-m-d H:i:s');
	$Ofics="'".str_replace(',',"','",$Ofic)."'";
	html();
	echo "<script language='javascript'>
		function procesar_compromiso(id,numero)
		{
				parent.Compr(id,numero);
		}
	</script>
	<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0' bgcolor='ffffff'>";
	if($Aseg)
	{
		$Limite1=date('Y-m-d ').aumentaminutos($Ahora,60);
		//echo "$Ahora - $Limite1 ";
		if($Compromisos=q("select co.id,co.fecha,co.hora,si.numero,si.asegurado_nombre, ase.nombre as nase,ofi.nombre as noficina,co.siniestro as ids
									FROM siniestro si, oficina ofi,aseguradora ase,compromiso co
									WHERE co.siniestro=si.id and co.estado='P' and ase.id=si.aseguradora and si.ciudad=ofi.ciudad and
									si.aseguradora in ($Aseg)  and si.estado=5 and si.ciudad in ($Ofics) and concat(co.fecha,' ',co.hora) between '$Ahora' and '$Limite1'
									ORDER BY co.fecha,co.hora"))
		{
			echo "<table width='100%' bgcolor='ddddee'><tr><th>#</th><th>Aseguradora</th><th>Oficina</th><th>Fecha</th><th>Siniestro</th><th>Falta</th></tr>";
			$Contador=1;
			while($Co=mysql_fetch_object($Compromisos))
			{
				$Falta=diferencia_tiempo($Ahora,"$Co->fecha $Co->hora");
				$Minutos=round(segundos($Ahora,"$Co->fecha $Co->hora")/60,0);
				$Rojo=255;
				$Verde=70+($Minutos*3);
				if($Minutos<10) $Adorno="style='text-decoration:blink' "; else $Adorno='';
				$Color=rgb2hex(array($Rojo,$Verde,100));
				echo "<tr style='cursor:pointer' onclick='procesar_compromiso($Co->ids,\"$Co->numero\");'><td align='right'>$Contador</td><td $Adorno>$Co->nase</td><td $Adorno>$Co->noficina</td><td $Adorno>$Co->fecha $Co->hora</td>
							<td $Adorno>$Co->numero $Co->asegurado_nombre</td><td bgcolor='$Color' $Adorno>$Falta</td></tr>";
				$Contador++;
			}
			echo "</table>";
		}
		else
		{
			echo "No hay compromisos por vencerse en los próximos 60 minutos.";
		}
	}

	echo "</body>";
}


// --------------------------------------------*-------------------------------*-------------------------------------*---------------------------------------*------------------------------*-----------------------------*-----------------------------------*---------------------------

function inicio_proceso_call()
{
	global $id_siniestro,$USUARIO,$NUSUARIO;
	/* determinacion del estado del caso:
	- si es totalmente nuevo el caso y empieza su proceso,
	   */
	$Siniestro=qo("Select *,t_estado_siniestro(estado) as nestado from siniestro where id=$id_siniestro");
	$Oficina=qo1("select id from oficina where ciudad='$Siniestro->ciudad'");
	$Ciudad=qo1("select t_ciudad('$Siniestro->ciudad')");
	$Aseguradora=qo("Select * from aseguradora where id=$Siniestro->aseguradora");
	if($Siniestro->ciudad_original) $Ciudado=qo1("select t_ciudad('$Siniestro->ciudad_original')"); else $Ciudado=false;
	$Observaciones=nl2br($Siniestro->observaciones);
	$Tseguimiento=tu('seguimiento','id');
	$TSiniestro=tu('siniestro','id');
	html('CALL CENTER - Siniestro '.$Siniestro->numero);
	echo "<script language='javascript'>
			function fija()
			{document.getElementById('if_call').style.height=document.body.clientHeight-300;}

			function agrandar() {centrar();	}

			function ver_seguimiento(id) {modal('marcoindex.php?Acc=abre_tabla&Num_Tabla=$Tseguimiento&VINCULOT='+id+'&VINCULOC=siniestro',0,0,500,1000,'seguimiento');}

			function ver_siniestro(id) {modal('marcoindex.php?Acc=mod_reg&Num_Tabla=$TSiniestro&id='+id,0,0,500,1000,'siniestro');}

			function cerrar() { window.close();void(null); opener.tachar($id_siniestro); }

			function cerrar1() { window.close();void(null); }

			function cerrar_adjudicado() {window.close();void(null);tachar_adjudicado(); }

			function tachar_adjudicado() { opener.tachar_adjudicado($id_siniestro); window.close();void(null); }

			function guardar_observaciones()
			{
				with(document.forma)
				{
					if(!alltrim(observaciones.value))
					{
						alert('Si quiere registrar observaciones debe escribir algo en el campo Observaciones');
						observaciones.style.backgroundColor='ffff55';
						observaciones.focus();
						return false;
					}
					Acc.value='guardar_observaciones';
					submit();
				}
			}

			function recargar() { window.open('zcallcenter.php?Acc=inicio_proceso_call&id_siniestro=$id_siniestro','_self'); recargar_observaciones(); }

			function Compr(id) { modal('zcompromiso.php?Acc=ver_compromisos&id='+id+'&DesdeCall=0',0,0,400,700,'compromisos'); }

			function marcar_procesado() { opener.marcar_procesado($id_siniestro); }

			function recargar_observaciones() { document.forma.observaciones.value=''; document.getElementById('observaciones_grabadas').src='zcallcenter.php?Acc=traer_observaciones&id=$id_siniestro'; }

			function solicitar_autorizacion() {modal('zautorizaciones.php?sini=$Siniestro->numero&DesdeCall=1',0,0,600,600,'call');}

			function activar_agenda(dato) { modal('zcallcenter.php?Acc=proceso_adjudicacion&id=$id_siniestro&Filtro_placa='+dato,0,0,600,900,'wadj'); }

			function cerrar_plantilla() { document.getElementById('Plantilla_Observaciones').style.visibility='hidden';}

			var Contenido_efecto='';
			var Objeto_efecto='';
			var Estado_efecto=0;

			function efectovisual()
			{
				if(document.getElementById(Objeto_efecto))
				{
					if(Estado_efecto)
						{document.getElementById(Objeto_efecto).innerHTML=Contenido_efecto;Estado_efecto=0;}
					else
						{document.getElementById(Objeto_efecto).innerHTML='';Estado_efecto=1;}
				}
				var Repinta_efecto=setTimeout(efectovisual,1000);
			}
			</script>
			<body onload='fija()' onresize='fija()'><script language='javascript'>centrar();</script>
			<h3>Control de Seguimiento Call Center <i style='color:00000'>$Aseguradora->nombre  Póliza Número: $Siniestro->poliza Siniestro No. $Siniestro->numero </i>&nbsp;&nbsp;
			<a class='info' onclick='ver_seguimiento($id_siniestro);' style='cursor:pointer'><img src='img/seguimiento.png' border='0' height='30'><span>Seguimientos</span></a>
			<a class='info' onclick='ver_siniestro($id_siniestro);' style='cursor:pointer'><img src='img/versiniestro.png' border='0' height='30'><span>Ver el Siniestro</span></a>
			<a class='info' onclick='Compr($id_siniestro);' style='cursor:pointer;'><img src='img/compromiso.png' border=0 height='30'><span style='width:100px'>Revisar / Adicionar Compromisos</span></a>";
	if($Siniestro->chevyseguro) echo "<img src='img/chevyseguro1.png' border='0' height='40' alt='Chevyseguro' title='Chevyseguro'>";
	if($Siniestro->no_garantia) echo "<img src='img/nogarantia.png' border='0' height='40' alt='Servicio Sin Garantía' title='Servicio Sin Garantía'><span id='sg'><b>Servicio Sin Garantía</b></span>
															<script language='javascript'>Objeto_efecto='sg'; Contenido_efecto='<b>Servicio Sin Garantía</b>';efectovisual();</script>";
	echo "<br />Ciudad: <b>$Ciudad</b> ".($Ciudado?"Ciudad original: <b>$Ciudado</b>":"")."</h3>";
	echo "<div id='Plantilla_Observaciones' name='Plantilla_Observaciones' style='visibility:hidden;position:fixed;top:10;left:10;border-style:solid;border-width:2px;z-index:119;opacity: 0.90' border='1' frameborder='yes'>
				<table bgcolor='ffffee' border cellspacing='0'><tr ><th width='600px'>Observaciones Rapidas</th></tr>
				<tr ><td style='cursor:pointer' onclick=\"document.forma.observaciones.value+=this.innerHTML;\">El Asegurado usará tarjeta débito. Se le informa que debe traer número de cuenta. </td></tr>
				<tr ><td style='cursor:pointer' onclick=\"document.forma.observaciones.value+=this.innerHTML;\">El Asegurado usará tarjeta crédito de un tercero. Se le informa que el tarjetahabiente debe estar presente en el momento de la entrega. </td></tr>
				<tr ><td style='cursor:pointer' onclick=\"document.forma.observaciones.value+=this.innerHTML;\">El Asegurado solicita placa terminada en:  . </td></tr>
				<tr ><td style='cursor:pointer' onclick=\"document.forma.observaciones.value+=this.innerHTML;\">Asegurado envía autorizado. Se solicita carta de autorización, nombre del autorizado . </td></tr>
				<tr ><td style='cursor:pointer' onclick=\"document.forma.observaciones.value+=this.innerHTML;\">Asegurado usara TC TD Pagar . </td></tr>
				<tr ><td style='cursor:pointer' onclick=\"document.forma.observaciones.value+=this.innerHTML;\">Se comunica asegurado solicitando información acerca de la devolución de la garantía, se remite por mail al área de servicio al cliente con copia al Director y al Coordinador. </td></tr>
				<tr ><td style='cursor:pointer' onclick=\"document.forma.observaciones.value+=this.innerHTML;\">Se realiza cambio de placa. </td></tr>
				<tr ><td style='cursor:pointer' onclick=\"document.forma.observaciones.value+=this.innerHTML;\">Asegurado solicita reagendar la cita. </td></tr>
				<tr ><td style='cursor:pointer' onclick=\"document.forma.observaciones.value+=this.innerHTML;\">Asegurado no retorna el vehículo, requiere días adicionales. </td></tr>
				<tr ><td align='center'><input type='button' value='Cerrar esta ventana' onclick='cerrar_plantilla();'></td></tr></table></div>
				<table align='center'>
				<tr>
				<td align='center' valign='top' rowspan=2>
				Observaciones registradas:<br>
				<iframe height=190 width='300' name='observaciones_grabadas' id='observaciones_grabadas' src='zcallcenter.php?Acc=traer_observaciones&id=$id_siniestro'></iframe>
				</td>
				<td align='center' bgcolor='aaaaaa'>
				<table bgcolor='eeeeee'>
				<tr><td align='right' bgcolor='ffffff'>Fecha Siniestro</td><td bgcolor='ffffff'>$Siniestro->fec_siniestro</td><td align='right'' bgcolor='ffffff'>Fec.Autorización</td><td style='background-color:ffff44'>$Siniestro->fec_autorizacion</td></tr>
				<tr><td align='right' bgcolor='ffffff'>Fecha Declaración</td><td bgcolor='ffffff'>$Siniestro->fec_declaracion</td>
						<td align='right'' bgcolor='ffffff'>Placa</td><td bgcolor='ffffff' style='font-size:16px;font-weight:bold;color:995500;background-color:ddddff'>$Siniestro->placa</td></tr>
				</table>
				</td>
				<td rowspan=2 align='center' valign='top'>
					<form action='zcallcenter.php' method='post' target='Oculto_call' name='forma' id='forma'>
						<b>Observaciones:</b>  <a class='info' onclick=\"document.getElementById('Plantilla_Observaciones').style.visibility='visible';\"><img src='img/rayo.png' height='30'><span>Observaciones rápidas</span></a><br />
						<textarea name='observaciones' id='observaciones' cols=50 rows=5 style='font-family:arial;font-size:14px;'></textarea><br />
						<input type='button' value='Guardar Observaciones' onclick='guardar_observaciones();'>
						<input type='hidden' name='Acc' value=''><input type='hidden' name='id' value='$id_siniestro'>
					</form>
					<iframe name='Oculto_call' id='Oculto_call' height='1' width='1' style='visibility:hidden'></iframe>
				</td>
				</tr>
				<tr><td align='center' bgcolor='aaaaaa'>
				<table bgcolor='eeeeff' width='100%'>
					<tr><th colspan=4>ASEGURADO</th></tr>
					<tr><td>$Siniestro->asegurado_nombre</td><td>Id:</td><td>$Siniestro->asegurado_id</td></tr>
					<tr><th colspan=4>DECLARANTE</th></tr>
					<tr><td>$Siniestro->declarante_nombre</td><td>Id:</td><td>$Siniestro->declarante_id</td></tr>
					<tr><td colspan='4'><b>$Siniestro->declarante_telefono / $Siniestro->declarante_tel_resid / $Siniestro->declarante_tel_ofic / $Siniestro->declarante_celular / $Siniestro->declarate_tel_otro / $Siniestro->declarante_email</b></td></tr>
					<tr><th colspan=4>CONDUCTOR</th></tr>
					<tr><td>$Siniestro->conductor_nombre</td></tr>
					<tr><td colspan='4'><b>$Siniestro->declarante_telefono / $Siniestro->conductor_tel_resid / $Siniestro->conductor_tel_ofic / $Siniestro->conductor_celular / $Siniestro->conductor_tel_otro</b></td></tr>";
	if($Siniestro->actualizacion_aseg)
	{
		echo "<tr><th colspan=4>ACTUALIZACION ASEGURADORA/TERCERA PERSONA</th></tr>
					<tr><td colspan=4>$Siniestro->actualizacion_aseg<td></tr>";
	}
	echo "
				</table>
				</td></tr></table>";
	if($Siniestro->estado==5)
		echo "<iframe name='if_call' id='if_call' width='100%' height='300px' src='zcallcenter.php?Acc=procesar&id=$id_siniestro'></iframe>";
	else
		echo
			"<center><b style='font-size:18'>Estado actual del siniestro: $Siniestro->nestado</b></center><br /><br />
			<a href='zcallcenter.php?Acc=solicita_reactivacion&id=$id_siniestro' target='_self'>Solicitud de Modificación</a>
			";
	$Fecha=date('Y-m-d');$Hora=date('H:i:s');$Codigo=2; /*2: Consulta */
	$Idn=q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id_siniestro,'$NUSUARIO','$Fecha','$Hora','Consulta desde Call Center',$Codigo)");
	graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
}

function traer_observaciones()
{
	global $id;
	html();
	echo "<body>";
	echo nl2br(qo1("select observaciones from siniestro where id=$id"));
	echo "</body>";
}

function procesar()
{
	global $id,$NUSUARIO;
	html();
	echo "<script language='javascript'>
			function iniciar_proceso()
			{
				window.open('zcallcenter.php?Acc=proceso_contacto&id=$id','_self');
			}
		</script>
	<body>
		<h3>Estimado (a) $NUSUARIO: </h3>
			El proceso de call center se basa en un diagrama de flujo en el que se identifica cada etapa y busca facilitar la medición de tiempos y eventos de cada caso.<br /><br />
			Para iniciar el proceso por favor de click en el siguiente ícono:<br /><br />
			<center><a class='info' onclick='iniciar_proceso()'><img src='img/call_inicio.png' border='0'><span style='width:200px'>Iniciar proceso Call Center</span><br />INICIAR PROCESO</a>
		";
}

function proceso_contacto()
{
	global $id,$NUSUARIO;
	html();
	echo "<script language='javascript'>
			function volver()
			{window.open('zcallcenter.php?Acc=procesar&id=$id','_self');}

			function proceso_buzon()
			{window.open('zcallcenter.php?Acc=proceso_buzon&id=$id','_self');}

			function proceso_contacto_persona()
			{window.open('zcallcenter.php?Acc=proceso_contacto_persona&id=$id','_self');}

			function proceso_informacion_erronea()
			{window.open('zcallcenter.php?Acc=proceso_informacion_erronea&id=$id','_self');}

			function proceso_retorno_informacion()
			{window.open('zcallcenter.php?Acc=proceso_retorno_informacion&id=$id','_self');}

			function proceso_vencimiento_tiempos()
			{window.open('zcallcenter.php?Acc=proceso_vencimiento_tiempos&id=$id','_self');}
		</script>
		<body ><h3>Agente: $NUSUARIO - Inicio de Proceso</h3>
		<a class='info' onclick='volver()'><img src='gifs/atras.png' border='0'><span style='width:200px'>Volver a la pantalla anterior</span></a>
		<table cellspacing=10 cellpadding=40 align='center'><tr>
			<td align='center'><a class='info' onclick='proceso_contacto_persona()'><img src='img/contacto_call.png' border='0'>
					<span style='width:200px'>Contacto con una persona</span><br>Contacto con una persona.</a></td>
			<td align='center'><a class='info' onclick='proceso_buzon();'><img src='img/buzon.png' border='0'>
				<span style='width:200px'>Mensaje de Buzón de voz</span><br>Mensaje en buzón de voz.</a></td>
			<td align='center'>
			<a class='info' onclick='proceso_informacion_erronea()'><img src='img/informacion_erronea.png' border='0'>
			<span style='width:200px'>Información Errónea.</span><br>Información Errónea <br />Click para enviar email a la Aseguradora para solicitar<br />corrección de información de Contacto.</a>
		</td>
		<td align='center'>
		<a class='info' onclick='proceso_retorno_informacion()'><img src='img/retorno_informacion.png' border='0'>
			<span style='width:200px'>Retorno de información por parte de la Aseguradora.</span><br>Retorno de Información de Contacto<br />corregida por parte de la Aseguradora.</a>
		</td>
		<td align='center'>
		<a class='info' onclick='proceso_vencimiento_tiempos()'><img src='img/vencimiento_tiempos.png' border='0' height=120>
			<span style='width:200px'>Vencimiento por tiempos de contactabilidad.</span><br>Vencimiento por tiempos<br>de contactabilidad.</a>
		</td>
		</tr></table>";
}

function proceso_retorno_informacion()
{
	global $id,$NUSUARIO;
	$Siniestro=qo("select * from siniestro where id=$id");
	$Aseguradora=qo("select email_soporte_e from aseguradora where id=$Siniestro->aseguradora");
	html();
	echo "<script language='javascript'>
		function volver()
		{window.open('zcallcenter.php?Acc=proceso_contacto&id=$id','_self');}

		function guardar_informacion()
		{
			if(!alltrim(document.forma.actualizacion.value))
			{
				alert('Debe ingresar los datos de la actualización');
				document.forma.actualizacion.style.backgroundColor='ffff55';
				document.forma.actualizacion.focus();
				return false;
			}
			document.forma.submit();
		}
		</script>
		<body ><h3>Agente: $NUSUARIO - Retorno de Información por parte de la Aseguradora.</h3>
		<a class='info' onclick='volver()'><img src='gifs/atras.png' border='0'><span style='width:200px'>Volver a la pantalla anterior</span></a><br><br />
		<h4>RETORNO DE INFORMACION</h4>
		<form action='zcallcenter.php' method='post' target='Oculto_call' name='forma' id='forma'>
			Por favor ingrese los datos de actualización:<br />
			<textarea name='actualizacion' cols=100 rows=4 style='font-family:arial:font-size:14px'></textarea><br />
			<input type='button' value='GUARDAR INFORMACION' onclick='guardar_informacion()'>
			<input type='hidden' name='Acc' value='proceso_retorno_informacion_ok'>
			<input type='hidden' name='id' value='$id'>
		</form>
		<iframe name='Oculto_call' id='Oculto_call' height=1 width=1 style='visitiliby:hidden'></iframe>
		";
}

function proceso_retorno_informacion_ok()
{
	global $id,$actualizacion,$NUSUARIO,$USUARIO,$Hoy,$Ahora;

	//  cambio derogado por la gerencia diciembre 23 2011
	//q("update siniestro set observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]: Se recibe actualización de datos por parte de la Aseguradora: $actualizacion'),
	 //actualizacion_aseg=concat(actualizacion_aseg,'\n$NUSUARIO [$Ahora]: $actualizacion'),ingreso='$Ahora' where id=$id");

	q("update siniestro set observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]: Se recibe actualización de datos por parte de la Aseguradora: $actualizacion'),
	 actualizacion_aseg=concat(actualizacion_aseg,'\n$NUSUARIO [$Ahora]: $actualizacion') where id=$id");

	$H1=date('Y-m-d'); $H2=date('H:i:s');
	$Idn=q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'$H1','$H2','$NUSUARIO','Se recibe actualizació por parte de la aseguradora: $actualizacion',8)"); // 8: actualizacion de la aseguradora
	graba_bitacora('siniestro','M',$id,'Observaciones,Actualizacion aseguradora');
	graba_bitacora('seguimiento','A',$Idn,'Adiciona registro');
	echo "<script language='javascript'>
			function carga()
			{
				alert('Actualizacion grabada satisfactoriamente');
				parent.parent.recargar();
			}
		</script>
		<body onload='carga()'></body>";
}

function proceso_actualizacion()
{
	global $id,$NUSUARIO;
	$Siniestro=qo("select * from siniestro where id=$id");
	html();
	echo "<script language='javascript'>
		function volver()
		{window.open('zcallcenter.php?Acc=proceso_contacto3persona&id=$id','_self');}

		function guardar_informacion()
		{
			if(!alltrim(document.forma.actualizacion.value))
			{
				alert('Debe ingresar los datos de la actualización');
				document.forma.actualizacion.style.backgroundColor='ffff55';
				document.forma.actualizacion.focus();
				return false;
			}
			document.forma.submit();
		}
		</script>
		<body ><h3>Agente: $NUSUARIO - Actualización de información de Contacto.</h3>
		<a class='info' onclick='volver()'><img src='gifs/atras.png' border='0'><span style='width:200px'>Volver a la pantalla anterior</span></a><br><br />
		<h4>ACTUALIZACIÓN DE INFORMACIÓN DE CONTACTO</h4>
		<form action='zcallcenter.php' method='post' target='Oculto_call' name='forma' id='forma'>
			Por favor ingrese los datos de actualización:<br />
			<textarea name='actualizacion' cols=100 rows=4 style='font-family:arial:font-size:14px'></textarea><br />
			<input type='button' value='GUARDAR INFORMACION' onclick='guardar_informacion()'>
			<input type='hidden' name='Acc' value='proceso_actualizacion_ok'>
			<input type='hidden' name='id' value='$id'>
		</form>
		<iframe name='Oculto_call' id='Oculto_call' height=1 width=1 style='visitiliby:hidden'></iframe>
		";
}

function proceso_actualizacion_ok()
{
	global $id,$actualizacion,$NUSUARIO,$USUARIO,$Hoy,$Ahora;

	// cambio derogado por la gerencia diciembre 23 2011
//	q("update siniestro set observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]: Se actualiza información de Contacto: $actualizacion'),
//	 actualizacion_aseg=concat(actualizacion_aseg,'\n$NUSUARIO [$Ahora]: $actualizacion'),ingreso='$Ahora' where id=$id");

	q("update siniestro set observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]: Se actualiza información de Contacto: $actualizacion'),
	 actualizacion_aseg=concat(actualizacion_aseg,'\n$NUSUARIO [$Ahora]: $actualizacion') where id=$id");

	$H1=date('Y-m-d'); $H2=date('H:i:s');
	$Idn=q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'$H1','$H2','$NUSUARIO','Se recibe actualizació por parte de la aseguradora: $actualizacion',7)"); // 7: Observación general
	graba_bitacora('siniestro','M',$id,'Observaciones,Actualizacion aseguradora');
	graba_bitacora('seguimiento','A',$Idn,'Adiciona registro');
	echo "<script language='javascript'>
			function carga()
			{
				alert('Actualizacion grabada satisfactoriamente');
				parent.parent.marcar_procesado();
				parent.parent.recargar();
			}
		</script>
		<body onload='carga()'></body>";
}

function proceso_informacion_erronea()
{
	global $id,$NUSUARIO;
	$Siniestro=qo("select * from siniestro where id=$id");
	$Aseguradora=qo("select email_soporte_e,email_copia from aseguradora where id=$Siniestro->aseguradora");
	$Numero_Aviso=qo1("select count(id) from seguimiento where siniestro=$id and tipo=4")+1;
	$Email_usuario=usuario('email');
	$Oficina=qo1("select id from oficina where ciudad='$Siniestro->ciudad'");
	$Ciudad=qo1("select t_ciudad('$Siniestro->ciudad')");
	if($Siniestro->ciudad_original) $Ciudado=qo1("select t_ciudad('$Siniestro->ciudad_original')"); else $Ciudado=false;
	if($Siniestro->email_analista) $destino=$Siniestro->email_analista;
	elseif($Aseguradora->email_soporte_e) $destino=$Aseguradora->email_soporte_e;
	else $destino=false;
	$destino_copia=$Aseguradora->email_copia;
	html();
	echo "<script language='javascript'>
		function volver()
		{window.open('zcallcenter.php?Acc=proceso_contacto&id=$id','_self');}

		function enviar_mensaje()
		{
			if(!document.forma.tipificacion.value)
			{
				alert('Debe seleccionar la tipificación correcta para este caso'); document.forma.tipificacion.style.backgroundColor='ffff44';document.forma.tipificacion.focus();return false;
			}
			document.forma.submit();
		}
		</script>
		<body ><h3>Agente: $NUSUARIO - Información Errónea.</h3>
		<a class='info' onclick='volver()'><img src='gifs/atras.png' border='0'><span style='width:200px'>Volver a la pantalla anterior</span></a><br><br />
		<h4>INFORMACION ERRONEA</h4>";
	if($destino)
	{
		echo "
		<form action='zcallcenter.php' method='post' target='_self' name='forma' id='forma'>
			<table><tr><td>
			<input type='hidden' name='destinatario' id='destinatario' value='$destino'>
			<input type='hidden' name='destino_copia' id='destino_copia' value='$destino_copia'>
			Asunto del mensaje: <input type='text' name='asunto' value='Información Erronea Sinisetro $Siniestro->numero' size='50'><br><br>
			Mensaje: <br />
			<textarea name='mensaje' id='mensaje' cols='100' rows='10'>".
			"Reciban cordial saludo.\nPor medio del presente informamos que los datos suministrados correspondientes al Siniestro Número: $Siniestro->numero son erróneos o inconsistentes\n\n".
			"AVISO NUMERO: $Numero_Aviso\nPóliza Número: $Siniestro->poliza\nCiudad: $Ciudad ".($Ciudado?"\nCiudad original: $Ciudado":"")."\nFecha del siniestro: $Siniestro->fec_siniestro".
			"Fecha de declaración del siniestro: $Siniestro->fec_declaracion\nFECHA DE AUTORIZACION: $Siniestro->fec_autorizacion\nVigencia de la póliza:  Desde $Siniestro->vigencia_desde ".
			"hasta $Siniestro->vigencia_hasta\nPlaca: $Siniestro->placa Marca: $Siniestro->marca Tipo: $Siniestro->tipo Línea: $Siniestro->linea Modelo: $Siniestro->modelo Clase: $Siniestro->clase\n\n".
			"ASEGURADO: Nombre: $Siniestro->asegurado_nombre Identificación: $Siniestro->asegurado_id\n\n".
			"Declarante: Nombre: $Siniestro->declarante_nombre Identificación: $Siniestro->declarante_id\n".
			"Telefonos: $Siniestro->declarante_telefono / $Siniestro->declarante_tel_resid / $Siniestro->declarante_tel_ofic / $Siniestro->declarante_celular\n\n".
			"Conductor: Nombre: $Siniestro->conductor_nombre\n".
			"Telefonos: $Siniestro->declarante_telefono / $Siniestro->conductor_tel_resid / $Siniestro->conductor_tel_ofic / $Siniestro->conductor_celular / $Siniestro->conductor_tel_otro\n\n".
			"NOTA: Solicitamos el favor de retornar lo más pronto posible la información correcta através de este mismo medio, a las siguientes direcciones de correo electrónico:\n\n".
			"A: controloperativo@aoacolombia.com\nCC: $Email_usuario\n\nCordialmente,\n\nDepartamento de Call Center\nAdministración Operativa Automotriz S.A.\n</textarea><br /><br />
				La información erronea es: <input type='text' name='info_erronea1' value='El número telefónico o celular' size=80><br>
			</td><td >
				Seleccione la tipificación correcta para este caso:
				<br />".menu1("tipificacion","select id,nombre from tipifica_seguimiento where tipo='INFORMACION ERRONEA' ",0,1)."
			</td>
			</tr></table>
				<input type='hidden' name='Acc' id='Acc' value='proceso_informacion_erronea_ok'>
				<center><input type='button' value='ENVIAR MENSAJE' onclick='enviar_mensaje()'>
				<input type='hidden' name='id' id='id' value='$id'>
				</form>
		<hr>";
	}
	else
	{
		echo "<b>No existe correo electrónico ni de aseguradora ni del analista que lo envió.</b>";
	}
}

function proceso_informacion_erronea_ok()
{
	global $id,$mensaje,$asunto,$info_erronea1,$destinatario,$NUSUARIO,$tipificacion,$destino_copia;
	$mensaje=str_replace('NOTA:','NOTA: Los datos con posibilidad de error son '.$info_erronea1,$mensaje);
	$Email_usuario=usuario('email');

	$Envio=enviar_gmail($Email_usuario /*de */ ,$NUSUARIO /*nombre de */ ,
	"$destinatario,$destinatario;controloperativo@aoacolombia.com,Dervin Junior;oscargomez@aoacolombia.com,Oscar Gomez".($destino_copia?";$destino_copia":"") /*para */ ,
	"$Email_usuario,$NUSUARIO"   /*Con copia*/ ,
	"$asunto"  /*OBJETO*/,
	"<body>".nl2br($mensaje)."</body>" /*mensaje */);

//	$Envio=enviar_gmail($Email_usuario /*de */ ,$NUSUARIO /*nombre de */ ,
//	"administracion@intercolombia.net" /*para */ ,
//	""   /*Con copia*/ ,
//	"$asunto"  /*OBJETO*/,
//	"<body>".nl2br($mensaje)."</body>" /*mensaje */);

	if($Envio)
	{
		q("update siniestro set info_erronea=1,observaciones=concat(observaciones,'\n".$NUSUARIO.' '.date('Y-m-d H:i')." Se remite a $destinatario por información de contacto erronea') where id='$id'");
		$H1=date('Y-m-d'); $H2=date('H:i:s');
		$Idn=q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo,tipificacion) values ($id,'$H1','$H2','$NUSUARIO','Se remite a $destinatario por información de contacto erronea',4,'$tipificacion')");
		graba_bitacora('seguimiento','A',$Idn,'Adiciona registro');
		if($s1)
			echo "<script language='javascript'>
					function carga()
					{
						alert('Generación de Email hecha satisfactoriamente');
						parent.marcar_procesado();
						parent.cerrar1();
					}
				</script>
				<body onload='carga()'></body>";
	}
	else
	{
		echo "Problemas con el envío a $destinatario,controloperativo@aoacolombia.com, $destino_copia. ";
	}
}

function proceso_vencimiento_tiempos()
{
  global $id,$NUSUARIO,$Ahora;
  $Siniestro=qo("select aseguradora from siniestro where id=$id");
	$Aseguradora=qo("select buzon1 from aseguradora where id=$Siniestro->aseguradora");
	html();
	echo "<script language='javascript'>
		function volver()
		{window.open('zcallcenter.php?Acc=proceso_contacto&id=$id','_self');}
		</script>
		<body ><h3>Agente: $NUSUARIO - Vencimiento por tiempos de contactabilidad.</h3>
		<a class='info' onclick='volver()'><img src='gifs/atras.png' border='0'><span style='width:200px'>Volver a la pantalla anterior</span></a><br><br />
		<form action='zcallcenter.php' method='post' target='_self' name='forma' id='forma'>
			Seleccione la sub-Causal según el caso: ".
			menu1("subcausal","select id,nombre from subcausal where causal='18' ",0,1,"font-size:14;font-weight:bold;"," onchange='activa_submit();' ").
			"<input type='hidden' name='Acc' value='proceso_vencimiento_tiempos_ok'>
			<input type='hidden' name='id' value='$id'>
			<input type='submit' value='Continuar'>
		</form>	";
}

function proceso_vencimiento_tiempos_ok()
{
  global $id,$NUSUARIO,$Ahora,$subcausal;
  $Ncausal=qo1("select nombre from subcausal where id=$subcausal");
  $Fecha=date('Y-m-d');$Hora=date('H:i:s');$Codigo=6; // no adjudicacion
  $Idn=q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','No adjudicacion. Vencimiento por tiempos de contactabilidad. - $Ncausal',$Codigo)");
  graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
  q("update siniestro set estado=1,causal=18,subcausal='$subcausal', observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: No adjudica. (Vencimiento por tiempos de contactabilidad. - $Ncausal)\") where id=$id");
	echo "<script language='javascript'>parent.marcar_procesado()</script>
	<br /><br /><a style='cursor:pointer' onclick='parent.cerrar1();'><img src='gifs/standar/Cancel.png' border='0'> Finalizar Proceso Call Center</a>";

}

function proceso_buzon()
{
	global $id,$NUSUARIO,$Ahora;
	$Siniestro=qo("select aseguradora from siniestro where id=$id");
	$Aseguradora=qo("select buzon1 from aseguradora where id=$Siniestro->aseguradora");
	html();
	echo "<script language='javascript'>
		function volver()
		{window.open('zcallcenter.php?Acc=proceso_contacto&id=$id','_self');}
		</script>
		<body ><h3>Agente: $NUSUARIO - Buzon de voz.</h3>
		<a class='info' onclick='volver()'><img src='gifs/atras.png' border='0'><span style='width:200px'>Volver a la pantalla anterior</span></a><br><br />
		<h4>GUION DE BUZON DE VOZ</h4>
		$Aseguradora->buzon1
		<hr>
		<form action='zcallcenter.php' method='post' target='_self' name='forma' id='forma'>
			Seleccione la tipificación según el caso: ".menu1("tipificacion","select id,concat(tipo,' - ',nombre) from tipifica_seguimiento where tipo in ('TELEFONO FIJO','TELEFONO CELULAR')")."
			<input type='hidden' name='Acc' value='proceso_buzon_ok'>
			<input type='hidden' name='id' value='$id'>
			<input type='submit' value='Continuar'>
		</form>	";
}

function proceso_buzon_ok()
{
	global $id,$NUSUARIO,$Ahora,$tipificacion;
	$Ntipificacion=qo1("select concat(tipo,' - ',nombre) from tipifica_seguimiento where id='$tipificacion' ");
	$Fecha=date('Y-m-d');$Hora=date('H:i:s');$Codigo=12; /*12: Mensaje en Buzón de voz */
	$Idn=q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo,tipificacion) values ($id,'$NUSUARIO','$Fecha','$Hora','Se deja mensaje en buzón de voz.',$Codigo,'$tipificacion')");
	graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	q("update siniestro set observaciones=concat(observaciones,\"\n$NUSUARIO [$Ahora]: Se deja mensaje en buzón de voz. ($Ntipificacion)\") where id=$id");
	echo "<script language='javascript'>parent.marcar_procesado()</script>
	<br /><br /><a style='cursor:pointer' onclick='parent.cerrar1();'><img src='gifs/standar/Cancel.png' border='0'> Finalizar Proceso Call Center</a>";
}

function proceso_contacto_persona()
{
	global $id,$NUSUARIO;
	$Siniestro=qo("select aseguradora from siniestro where id=$id");
	$Aseguradora=qo("select guion_primer_contact from aseguradora where id=$Siniestro->aseguradora");
	$Guion=nl2br(verifica_variables($Aseguradora->guion_primer_contact,$id));
	html();
	echo "<script language='javascript'>
		function volver()
		{window.open('zcallcenter.php?Acc=proceso_contacto&id=$id','_self');}

		function proceso_contacto3persona()
		{window.open('zcallcenter.php?Acc=proceso_contacto3persona&id=$id','_self');}

		function proceso_contacto_exitoso()
		{window.open('zcallcenter.php?Acc=proceso_contacto_exitoso&id=$id','_self');}
		</script>
		<body ><h3>Agente: $NUSUARIO - Contacto con una Persona</h3>
		<a class='info' onclick='volver()'><img src='gifs/atras.png' border='0'><span style='width:200px'>Volver a la pantalla anterior</span></a>
		<H3>GUION DE SALUDO CONTACTO CON UNA PERSONA</H4>$Guion<hr>
		<table cellspacing=10 cellpadding=40 align='center'><tr>
		<td align='center'>
			<a class='info' onclick='proceso_contacto3persona()'><img src='img/contacto_call_3_persona.png' border='0'>
			<span style='width:200px'>Contacto con una tercera persona</span><br>Contacto con una tercera persona.</a></td>
		<td align='center'>
			<a class='info' onclick='proceso_contacto_exitoso()'><img src='img/contacto_call_asegurado.png' border='0'>
			<span style='width:200px'>Contacto EXITOSO con el asegurado.</span><br>Contacto EXITOSO con el asegurado.</a>
		</td></tr></table>
		";
}

function proceso_contacto3persona()
{
	global $id,$NUSUARIO;
	html();
	$Siniestro=qo("select aseguradora from siniestro where id=$id");
	$Aseguradora=qo("select contacto3persona from aseguradora where id=$Siniestro->aseguradora");
	$Guion=nl2br(verifica_variables($Aseguradora->contacto3persona,$id));
	$Fecha=date('Y-m-d');$Hora=date('H:i:s');$Codigo=11; /*11: Contacto con tercera persona */
	echo "<script language='javascript'>
		function volver()
		{window.open('zcallcenter.php?Acc=proceso_contacto_persona&id=$id','_self');}

		function proceso_mensaje3persona()
		{window.open('zcallcenter.php?Acc=proceso_mensaje3persona&id=$id','_self');}

		function proceso_compromiso()
		{window.open('zcompromiso.php?Acc=ver_compromisos&id=$id&Desde_proceso_call=proceso_contacto3persona','_self');}

		function proceso_actualizacion()
		{window.open('zcallcenter.php?Acc=proceso_actualizacion&id=$id','_self');}

		</script>
		<body ><h3>Agente: $NUSUARIO - Contacto con una Tercera Persona</h3>
		<a class='info' onclick='volver()'><img src='gifs/atras.png' border='0'><span style='width:200px'>Volver a la pantalla anterior</span></a>
		<h4>GUION CONTACTO TERCERA PERSONA</h4>$Guion<hr>
		<table cellspacing=10 cellpadding=40 align='center'><tr>
		<td align='center'>
			<a class='info' onclick='proceso_mensaje3persona()'><img src='img/buzon3persona.png' border='0'>
			<span style='width:200px'>Dejar mensaje con tercera persona</span><br>Dejar mensaje con tercera persona.</a></td>
		<td align='center'>
			<a class='info' onclick='proceso_compromiso()'><img src='img/compromiso.png' border='0'>
			<span style='width:200px'>Programar Compromiso.</span><br>Programar Compromiso.</a>
		</td>
		<td align='center'>
			<a class='info' onclick='proceso_actualizacion()'><img src='img/actualizacion.png' border='0' height=130>
			<span style='width:200px'>Actualizar información de Contacto</span><br>Actualizar información de Contacto.</a>
		</td>
		</tr></table>
		";
	$Idn=q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','Contacto con tercera persona',$Codigo)");
	graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');

}

function proceso_mensaje3persona()
{
	global $id,$NUSUARIO;
	html();
	$Siniestro=qo("select aseguradora from siniestro where id=$id");
	$Aseguradora=qo("select mensaje3persona from aseguradora where id=$Siniestro->aseguradora");
	$Guion=nl2br(verifica_variables($Aseguradora->mensaje3persona,$id));
	echo "<script language='javascript'>
		function volver()
		{window.open('zcallcenter.php?Acc=proceso_contacto3persona&id=$id','_self');}
		function grabar_obs()
		{
			if(alltrim(document.forma.observaciones.value))
			{
				document.forma.submit();
			}
			else
			{
				alert('Debe escribir algún texto en el campo Observaciones');
				document.forma.observaciones.style.backgroundColor='ffff33';
				document.forma.observaciones.focus();
			}
		}
		</script>
		<body ><h3>Agente: $NUSUARIO - Dejar mensaje con una Tercera Persona</h3>
		<a class='info' onclick='volver()'><img src='gifs/atras.png' border='0'><span style='width:200px'>Volver a la pantalla anterior</span></a>
		<h4>GUION DEJAR MENSAJE CON TERCERA PERSONA</h4>$Guion<hr>
		<form action='zcallcenter.php' method='post' target='Oculto_mensaje' name='forma' id='forma'>
			Observaciones:<br />
			<textarea name='observaciones' cols='100' rows='5' style='font-family:arial;font-size:12px'></textarea><br />
			<input type='button' value='Grabar Observaciones' onclick='grabar_obs();'>
			<input type='hidden' name='Acc' value='mensaje3persona_ok'>
			<input type='hidden' name='TS' value='11'>
			<input type='hidden' name='id' value='$id'>
		</form>
		<iframe name='Oculto_mensaje' id='Oculto_mensaje' style='visibility:hidden' height=1 width=1></iframe>
		";
}

function mensaje3persona_ok()
{
	global $id,$observaciones,$USUARIO,$NUSUARIO,$Hoy,$Ahora,$TS /*tipo seguimiento*/;
	$H1=date('Y-m-d'); $H2=date('H:i:s');
	q("update siniestro set observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]:$observaciones') where id=$id");
	if($TS==3) q("update siniestro set contacto_exitoso='$Hoy' where id=$id ");
	graba_bitacora('siniestro','M',$id,'Observaciones');
	$Fecha=date('Y-m-d');$Hora=date('H:i:s');$Codigo=13; /*13: Mensaje con tercera persona */
	$Idn=q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora',\"Mensaje con 3ra persona $observaciones\",$Codigo)");
	graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	echo "<script language='javascript'>
	function carga()
	{
		alert('Observaciones grabadas satisfactoriamente');
		parent.parent.marcar_procesado();
		parent.parent.cerrar1();
	}
	</script>
	<body onload='carga()'></body>";
}

function guardar_observaciones()
{
	global $id,$observaciones,$USUARIO,$NUSUARIO,$Hoy,$Ahora;
	$H1=date('Y-m-d'); $H2=date('H:i:s');
	q("update siniestro set observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]:$observaciones') where id=$id");
	$Idn=q("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($id,'$H1','$H2','$NUSUARIO','$observaciones',7)"); // 7: observacion general
	graba_bitacora('siniestro','M',$id,'Observaciones');
	graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	$Obs=qo1("select observaciones from siniestro where id=$id");
	echo "<script language='javascript'>
	function carga()
	{
		alert('Observaciones grabadas satisfactoriamente');
		parent.recargar_observaciones();
	}
	</script>
	<body onload='carga()'></body>";
}

function proceso_contacto_exitoso()
{
	global $id,$NUSUARIO,$Ahora;
	html();
	$Siniestro=qo("select aseguradora,numero,declarante_email from siniestro where id=$id");
	$Aseguradora=qo("select * from aseguradora where id=$Siniestro->aseguradora");
	$Guion1=nl2br(verifica_variables($Aseguradora->guion_contacto_exito,$id));
	$Guion2=nl2br(verifica_variables($Aseguradora->guion_requisitos,$id));
	echo "<script language='javascript'>
		function volver()
		{window.open('zcallcenter.php?Acc=proceso_contacto_persona&id=$id','_self');}
		function proceso_compromiso()
		{window.open('zcompromiso.php?Acc=ver_compromisos&id=$id&Desde_proceso_call=proceso_contacto_exitoso','_self');}
		function proceso_adjudicacion()
		{
			var Placa=document.getElementById('placa').value;
			if(Placa)
			{if(Placa!='*') {parent.agrandar();parent.activar_agenda(Placa);} else {alert('Debe digitar la placa o parte de la misma. Ya no se acepta el asterisco.');document.getElementById('placa').fosuc();}}
			else document.getElementById('placa').focus();
		}
		function proceso_no_adjudicacion()
		{parent.agrandar();window.open('zcallcenter.php?Acc=proceso_no_adjudicacion&id=$id','_self');}
		function solicitar_autorizacion()
		{modal('zautorizaciones.php?sini=$Siniestro->numero',0,0,600,600,'call');}
		</script>
		<body ><h3>Agente: $NUSUARIO - Proceso Contacto Exitoso</h3>
		<a class='info' onclick='volver()'><img src='gifs/atras.png' border='0'><span style='width:200px'>Volver a la pantalla anterior</span></a>
		<h4>GUION CONTACTO EXITOSO</h4>$Guion1<hr>
		<H3>PROCEDIMIENTO PARA ACCEDER AL SERVICIO</H3><B>$Guion2</B><hr>
		<table cellspacing=10 cellpadding=40 align='center'><tr><td align='center'>
			<a class='info' onclick='proceso_adjudicacion()'><img src='img/adjudicacion1.png' border='0'>
			<span style='width:200px'>Adjudicación</span><br>Adjudicación.</a>
			Placa: <input type='text' name='placa' id='placa' size=5 maxlength='6' style='font-family:arial;font-size:14px;font-weight:bold;'><br>
			Digitar la placa agiliza el proceso de adjudicación.
			</td>
		<td align='center'>
		<a class='info' onclick='proceso_compromiso()'><img src='img/compromiso.png' border='0'>
			<span style='width:200px'>Programar Compromiso.</span><br>Programar Compromiso.</a>
		</td>
		<td align='center'>
		<a class='info' onclick='proceso_no_adjudicacion()'><img src='img/no_adjudicacion.png' border='0'>
			<span style='width:200px'>No Adjudicar.</span><br>No Adjudicar.</a>
		</td></tr></table>
		<iframe name='Oculto_exitoso' id='Oculto_exitoso' height=1 width=1 style='visibility:hidden'></iframe>
		";
	$Fecha=date('Y-m-d');$Hora=date('H:i:s');$Codigo=3; /*3: Contacto exitoso */
	$Idn=q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','Contacto exitoso',$Codigo)");
	q("update siniestro set observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]:Contacto exitoso con el usuario.') where id=$id");
	graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
}

function guardar_email_declarante()
{
	global $id,$declarante_email;
	q("update siniestro set declarante_email='$declarante_email' where id=$id");
	echo "<script language='javascript'>
			function carga()
			{
				alert('Grabación del email satisfactoria');
			}
		</script>
		<body onload='carga()'></body>";
}

class Pdispon
{
	var $Placa='';
	var $Id_vehiculo=0;
	var $Kilometraje=0;
	var $Oficina=0; // id de la oficina
	var $Servicio=0;
	var $Servicio_desde='';
	var $Servicio_hasta='';
	var $Servicio_hora_retorno='06:00:00';
	var $Adjudicado=0;
	var $Adjudicado_numero_siniestro='';
	var $Mantenimiento=0;
	var $Fuera_servicio=0;
	var $Id_estado=0;
	var $Flota=0;
	var $Contador=0;
	var $Ultimo='';

	function Pdispon($Objeto,$Flota)
	{
		$this->Placa=$Objeto->placa;
		$this->Ultimo=r($this->Placa,1);
		$this->Id_vehiculo=$Objeto->id;
		$this->Kilometraje=$Objeto->kmf;
		$this->Oficina=$Objeto->oficina;
		$this->Flota=$Flota;
	//	$this->verifica_estado();
	}

	function pinta($Limite_dias,$Oficina,$Linea,$Flota,$LINK,$Solover=false)
	{
		$Hoy=date('Y-m-d');
		echo "<tr><td align='right' nowrap='yes'><b style='font-size:14;color:0000ff'>$this->Placa</b><br />".coma_format($this->Kilometraje)." </td>";
		for($i=0;$i<=$Limite_dias-1;$i++)
		{
			$Fecha=date('Y-m-d',strtotime(aumentadias($Hoy,$i)));
			if($this->Mantenimiento) {echo "<td bgcolor='709F9C' colspan=$Limite_dias>&nbsp;</td>";break;}
			elseif($this->Fuera_servicio) {echo "<td bgcolor='BF5652' colspan=$Limite_dias>&nbsp;</td>";break;}
			elseif($this->Adjudicado) {$this->pinta_adjudicado($Fecha,$Limite_dias);break;}
			elseif($this->Servicio) $this->pinta_servicio($Fecha,$Oficina,$Linea,$Flota,$LINK,$Solover);
			else
			{
				$id='celda_'.$Linea.'_'.$Flota.'_'.$this->Contador;
				if($Solover) echo "<td bgcolor='ffffff' nowrap='yes'>";
				else echo "<td bgcolor='ffffff' nowrap='yes' onmouseover=\"muestra('$id');\" onmouseout=\"oculta('$id');\">";
				$this->pinta_celda_cita($Fecha,$Oficina,$Linea,$Flota,$LINK,$Solover);
				echo "</td>";
			}
		}
	}

	function pinta_adjudicado($Fecha,$Limite_dias)
	{ echo "<td bgcolor='dddddd' nowrap='yes' alt='$this->Adjudicado_numero_siniestro' title='$this->Adjudicado_numero_siniestro' colspan='$Limite_dias'>&nbsp;</td>"; }

	function pinta_servicio($Fecha,$Oficina,$Linea,$Flota,$LINK,$Solover)
	{
		$id='celda_'.$Linea.'_'.$Flota.'_'.$this->Contador;
		if($Fecha>=$this->Servicio_desde && $Fecha<$this->Servicio_hasta)
		{ echo "<td bgcolor='C2FFC2' nowrap='yes'>&nbsp;</td>"; }
		elseif($Fecha==$this->Servicio_hasta)
		{
			if($Solover) echo "<td bgcolor='7196FF' nowrap='yes' >H.Dev: $this->Servicio_hora_retorno";
			else echo "<td bgcolor='7196FF' nowrap='yes' onmouseover=\"muestra('$id');\" onmouseout=\"oculta('$id');\">$this->Servicio_hora_retorno";
			if($this->Adjudicado) $this->pinta_adjudicado($Fecha);
			else $this->pinta_celda_cita($Fecha,$Oficina,$Linea,$Flota,$LINK,$Solover);
			echo "</td>";
		}
		else
		{
			if($Solover) echo "<td bgcolor='ffffff' nowrap='yes' >";
			else echo "<td bgcolor='ffffff' nowrap='yes' onmouseover=\"muestra('$id');\" onmouseout=\"oculta('$id');\">";
			$this->pinta_celda_cita($Fecha,$Oficina,$Linea,$Flota,$LINK,$Solover);echo "</td>";
		}
	}

	function pinta_celda_cita($Fecha,$Oficina,$Linea,$Flota,$LINK,$Solover)
	{
		global $Pyp,$USUARIO;
		
		$Pico_y_placa=false;
		$id='celda_'.$Linea.'_'.$Flota.'_'.$this->Contador;
		$this->Contador++;
		$Dia=date('w',strtotime($Fecha));
		for($i=0;$i<count($Pyp);$i++)
		{
			if($Fecha>=$Pyp[$i]->Fecha_inicial && $Fecha<=$Pyp[$i]->Fecha_final )
			{ if($Dia==$Pyp[$i]->Dia && strpos(' '.$Pyp[$i]->Placas,$this->Ultimo)) { echo "<a class='info'><img src='gifs/picoplaca.png' border='0' height='26'><span>Pico y Placa</span></a>"; $Pico_y_placa=true;} }
		}
		
		if($Solover) echo "<div id='$id' style='visibility:visible'>"; else echo "<div id='$id' style='visibility:hidden'>";
		if($Fecha>$this->Servicio_hasta) $h1=l($Oficina->hora_inicial,5);
		else { if(inlist($USUARIO,'1,26')) $h1=aumentaminutos($this->Servicio_hora_retorno,50);
		else $h1=aumentaminutos($this->Servicio_hora_retorno,20); }
		if($Pico_y_placa) { if($h1<'19:30') $h1='16:30'; }
		$Permite=false;
		if($this->Flota==6)
		{
			if(qo1m("select id from solicitud_faoa where placa='$this->Placa' and fecha='$Fecha'",$LINK)) $Permite=true;
			else if(!$Solover) echo "<input type='button' value='Solic.Activacion' onclick=\"solicita_activacion('$Fecha','$this->Placa')\"; style='font-size=9px'>";
		}
		else $Permite=true;
		if($Permite)
		{
			if(!$Solover) echo "<select name='cita_".$Fecha.$this->Placa."' id='cita_".$Fecha.$this->Placa."' style='width:50px'>";
			$Pinta=false;
			/* $Hora_inicial=l($Oficina->hora_inicial,2);
			$Hora_final=l($Oficina->hora_final,2); */
			if(inlist($USUARIO,'1,26')) { $Hora_final='20:00'; }
			$H=$Oficina->hora_inicial;
			while($H<=$Oficina->hora_final)
			{
				if($H>$h1)
				{ if($Solover) { echo date('h:i A',strtotime($Fecha.' '.$H));$Pinta=true;break; }
					else {echo "<option value='$H'>".date('h:i A',strtotime($Fecha.' '.$H))."</option>";$Pinta=true;} }
				$H=aumentaminutos($H,15);
			}
			echo "</select>";
			if($Pinta) { if(!$Solover) echo "<a href=\"javascript:agendar_cita('$Fecha','$this->Placa',$this->Oficina,$this->Flota);void(null);\"><img src='gifs/standar/Next.png' border='0'></a>"; }
			else echo "<a class='info' style='cursor:pointer'><img src='gifs/standar/Cancel.png' border='0'><span>No hay horario disponible para adjudicar. Retorno: $this->Servicio_hora_retorno $h1 $Oficina->hora_final </span></a>";
		}
		echo "</div>";
	}

	function verifica_estado()
	{
		global $Estado,$Citado;
		$AAE=$Estado[$this->Id_vehiculo];
		$Ultimo=false;
		for($i=0; $i<count($AAE);$i++) { if($this->Id_estado<$AAE[$i]->id) { $this->Id_estado=$AAE[$i]->id; $Ultimo=$AAE[$i]; } }
		if($Ultimo)
		{
			if($Ultimo->Estado==1  /*Servicio*/)
			{
				$this->Servicio=1;
				$this->Servicio_desde=$Ultimo->Desde;
				$this->Servicio_hasta=$Ultimo->Hasta;
				$this->Servicio_hora_retorno=$Ultimo->Hora_devolucion;
			}
			if($Ultimo->Estado==4 /*Mantenimiento*/ || $Ultimo->Estado==92 /*Mantenimiento programado*/  || $Ultimo->Estado==8) $this->Mantenimiento=1;
			if($Ultimo->Estado==5 /*Fuera de Servicio*/) $this->Fuera_servicio=1;
		}

		for($i=0; $i<count($AAE);$i++)
		{
			if($AAE[$i]->Id_vehiculo==$this->Id_vehiculo && $AAE[$i]->Desde>date('Y-m-d'))
			{
				if($AAE[$i]->Estado==4 /*Mantenimiento*/ || $AAE[$i]->Estado==92 /*Mantenimiento programado*/ || $AAE[$i]->Estado==8) {$this->Mantenimiento=1; break;}
				if($AAE[$i]->Estado==5 /*Fuera de Servicio*/) {$this->Fuera_servicio=1;break;}
			}
		}

		for($i=0;$i<count($Citado[$this->Id_vehiculo]);$i++)
		{
			if($Citado[$this->Id_vehiculo][$i]->Estado=='P' || $Citado[$this->Id_vehiculo][$i]->Estado=='C')
			{
				$this->Adjudicado=1;
				$this->Adjudicado_numero_siniestro=$Citado[$this->Id_vehiculo][$i]->NSiniestro;
				break;
			}
		}
	}
}

class Pestado
{
	var $Estado=0;
	var $Nestado='';
	var $Id_vehiculo=0;
	var $Orden=0;
	var $Desde='';
	var $Hasta='';
	var $Hora_devolucion='';
	var $id=0;

	function Pestado($Objeto)
	{
		$this->id=$Objeto->id;
		$this->Estado=$Objeto->estado;
		$this->Nestado=$Objeto->nestado;
		$this->Id_vehiculo=$Objeto->vehiculo;
		$this->Orden=$Objeto->id;
		$this->Desde=$Objeto->fecha_inicial;
		$this->Hasta=$Objeto->fecha_final;
		if($this->Estado==1) $this->Hora_devolucion=$Objeto->hdev;
	}
}

class Pcita
{
	var $Id_vehiculo=0;
	var $Siniestro=0;
	var $NSiniestro='';
	var $Fecha='';
	var $Hora='';
	var $Estado='';

	function Pcita($Objeto)
	{
		$this->Id_vehiculo=$Objeto->idv;
		$this->Siniestro=$Objeto->siniestro;
		$this->NSiniestro=$Objeto->nsiniestro;
		$this->Fecha=$Objeto->fecha;
		$this->Hora=$Objeto->hora;
		$this->Estado=$Objeto->estado;
	}
}

class pico_y_placa{
	var $Fecha_inicial;
	var $Fecha_final;
	var $Dia;
	var $Placas;

	function pico_y_placa($Objeto)
	{
		$this->Fecha_inicial=$Objeto->fecha_inicial;
		$this->Fecha_final=$Objeto->fecha_final;
		$this->Dia=$Objeto->dia;
		$this->Placas=$Objeto->placas;
	}
}

function proceso_adjudicacion()
{
	global $id,$Disp_flota,$Disp_aoa,$Estado,$Citado,$Pyp,$NUSUARIO,$Filtro_placa;
	html('ADJUDICACION DE CITA');
	include('inc/link.php');
	$S=qom("select * from siniestro where id=$id",$LINK);
	$Oficina=qom("select id,hora_inicial,hora_final from oficina where ciudad='$S->ciudad'" ,$LINK);
	$Ciudad=qo1m("select t_ciudad('$S->ciudad')",$LINK);
	$Aseguradora=qom("Select * from aseguradora where id=$S->aseguradora",$LINK);
	$Guion=nl2br(verifica_variables($Aseguradora->guion_requisitos,$id,$LINK));
	if($S->ciudad_original) $Ciudado=qo1m("select t_ciudad('$S->ciudad_original')",$LINK); else $Ciudado=false;
	if($S->estado==5)
	{
		$Hoy1=date('Y-m-d');
		$Ahora=date('Y-m-d');
		$CFI=aumentadias(date('Y-m-d'),-2);
		$CFF=aumentadias(date('Y-m-d'),10);
		echo "<script language='javascript'>
				function agendar_cita(Fecha,Placa,Oficina,Flota)
				{
					var Hora=document.getElementById('cita_'+Fecha+Placa).value;
					document.formaa.Acc.value='agendar_cita';
					document.formaa.id.value='$id';
					document.formaa.placa.value=Placa;
					document.formaa.fecha.value=Fecha;
					document.formaa.hora.value=Hora;
					document.formaa.oficina.value=Oficina;
					document.formaa.flota.value=Flota;
					document.formaa.submit();
				}
				function solicita_activacion(Fecha,Placa)
				{
				 	modal('zcallcenter.php?Acc=solicita_activacion&Fecha='+Fecha+'&Placa='+Placa+'&Siniestro=$id',0,0,500,500,'solicitud_activacion');
				}
				function volver()
				{window.open('zcallcenter.php?Acc=proceso_contacto_exitoso&id=$id','_self');}
			</script>

			<body>
			<hr color='brown'><h3>Agente: $NUSUARIO - Proceso ADJUDICACION Y ASIGNACION DE CITA <i>Ciudad: $Ciudad</i></h3>
			<script language='javascript'>centrar();</script>
			<a class='info' onclick='volver()'><img src='gifs/atras.png' border='0'><span style='width:200px'>Volver a la pantalla anterior</span></a>
				<b>Se puede asignar cita buscando las placas que aparecen en <font style='color:00000;background-color:ffffff'> color negro y fondo blanco</font>,
				ajustadas hacia la izquierda. Las placas que aparecen en fondo <font style='background-color:ddffdd;color:000000;'> verde claro</font> significa que el vehículo está prestando servicio.
				Las placas que aparecen en fondo <font style='background-color:7387A3;color:000000;'>azul petróleo</font> significa que el vehículo está en mantenimiento.
				Las placas que aparecen en fondo <font style='background-color:FFC1BF;color:000000;'>rojo</font> significa que el vehículo está fuera de servicio. Si aparece una fila entera
				en fondo <font style='background-color:aaaaaa;color:000000;'>gris</font> significa que ese vehículo ya está agendado y la hora aparece en alguna de las celdas de la fila. Las placas
				que aparecen en color <font color='red'>rojo</font> significa que tienen pico y placa.<br /><br />
				<table border cellspacing=0 width='100%' bgcolor='dddddd'>";$Sec=1;

		echo "<tr><td>Kilometraje</td>";
		$Limite_dias=15;
		while($Sec<=$Limite_dias)
		{
			$Dia=date('w',strtotime($Ahora));
			$Ndia=dia_semana($Dia);
			if($Dia==0 || $Dia==6) $Fondo='ffdddd'; else $Fondo='ffffff';
			echo "<th nowrap='yes' style='font-size:12;font-weight:bold;background-color:$Fondo;color:000000'>$Ahora $Ndia</th>";
			$Sec++;
			$Ahora=date('Y-m-d',strtotime(aumentadias($Ahora,1)));
		}
		////////////  CITAS PRE PROGRAMADAS //////

		///////-----------------------------------------------------------------------
		////////////////////////////////////////////////////          VEHICULOS DE LA FLOTA               ////////////////////////////////////////////////////////////////////////////////
		echo "</tr>";
		$Ahora=date('Y-m-d');
		//--------------------------------------------------------------------------------------------------------------------------------------------------------------------
		if($Picos=mysql_query("select * from picoyplaca	where ciudad='$S->ciudad' and  fecha_final>'$Ahora' ",$LINK))
		{$Contador=0;while($Pi=mysql_fetch_object($Picos))  { $Pyp[$Contador]=new pico_y_placa($Pi);$Contador++; }	}
		//--------------------------------------------------------------------------------------------------------------------------------------------------------------------
//		if($Disponibles=mysql_query("select distinct v.id,v.placa,kilometraje(v.id) as kmf,ultima_ubicacion as oficina
//										FROM vehiculo v,ubicacion u WHERE u.flota='$S->aseguradora' and u.vehiculo=v.id and u.fecha_final>='$Ahora' and v.ultima_ubicacion=$Oficina->id
//										and (v.inactivo_desde='00000-00-00' or v.inactivo_desde>'$Ahora') and v.flota_distinta=1 ORDER BY kmf",$LINK))
		
		if(!$Disponibles=mysql_query("select distinct v.id,v.placa,kilometraje(v.id) as kmf,v.ultima_ubicacion as oficina
										FROM vehiculo v,ubicacion u 
										WHERE u.flota='$S->aseguradora' and u.vehiculo=v.id and
										u.fecha_final>='$Ahora' and estado in (1,2,7,8,93) and 
										v.ultima_ubicacion=$Oficina->id and (v.inactivo_desde='00000-00-00' or v.inactivo_desde>'$Ahora') and v.flota_distinta=1 ".
										($Filtro_placa?" and v.placa like '%$Filtro_placa%' ":"")." ORDER BY kmf",$LINK))
			die(mysql_error());	
		$id_disponibles='0';
		if(mysql_num_rows($Disponibles))
		{ $Contador=0;  while($D=mysql_fetch_object($Disponibles)) { $Disp_flota[$Contador]=new Pdispon($D,$S->aseguradora); $Contador++;$id_disponibles.=','.$D->id; } }
		
		if($Disponibles=mysql_query("select distinct v.id,v.placa,kilometraje(v.id) as kmf,v.ultima_ubicacion as oficina
										FROM vehiculo v WHERE v.ultima_ubicacion=$Oficina->id and (v.inactivo_desde='00000-00-00' or v.inactivo_desde>'$Ahora') 
										and v.flota_distinta=0 ORDER BY kmf",$LINK))
		{ $Contador=0; while($D=mysql_fetch_object($Disponibles)) { $Disp_aoa[$Contador]=new Pdispon($D,6); $Contador++;$id_disponibles.=','.$D->id; } }
		
		if($id_disponibles)
		{
			$Estados=mysql_query(" select u.*,t_estado_vehiculo(u.estado) as nestado, hdevol(u.vehiculo,u.fecha_final) as hdev
									FROM ubicacion u WHERE u.vehiculo in ($id_disponibles) and u.fecha_final>='$Ahora' order by u.vehiculo,u.id ",$LINK);
			$IniCitas=date('Y-m-d',strtotime(aumentadias($Ahora,-8)));
			$Citas=mysql_query("select c.*,t_siniestro(c.siniestro) as nsiniestro,v.id as idv FROM cita_servicio c,vehiculo v
									WHERE ((c.fecha >='$IniCitas' and c.estado='P') or (c.fecha>='$Ahora' and c.estado in ('P','C') and c.estadod='P'))  and c.oficina=$Oficina->id
									and c.placa=v.placa and v.id in ($id_disponibles) ",$LINK);
			while($C=mysql_fetch_object($Citas)) {$Citado[$C->idv][count($Citado[$C->idv])]=new Pcita($C);}
			while($D=mysql_fetch_object($Estados)) { $Estado[$D->vehiculo][count($Estado[$D->vehiculo])]=new Pestado($D); }
			
			echo "<tr><th colspan=".($Limite_dias+1)." style='font-size:16;font-weight:bold;'>FLOTA: $Aseguradora->nombre [".count($Disp_flota)." vehiculos]</TH></tr>";
			for($i=0;$i<count($Disp_flota);$i++)
			{
				$Disp_flota[$i]->verifica_estado();
				$Disp_flota[$i]->pinta($Limite_dias,$Oficina,$i,1,$LINK);
			}
			
			echo "<tr><th colspan=".($Limite_dias+1)." style='font-size:16;font-weight:bold;'>FLOTA: AOA [".count($Disp_aoa)." vehiculos] </TH></tr>";
			for($i=0;$i<count($Disp_aoa);$i++)
			{
				$Disp_aoa[$i]->verifica_estado();
				$Disp_aoa[$i]->pinta($Limite_dias,$Oficina,$i,2,$LINK);
			}
		}
		else echo "No hay disponibles. ";
		mysql_close($LINK);
		echo "</table>";
		echo "<form action='zcallcenter.php' method='post' target='_self' name='formaa' id='formaa'>
					<input type='hidden' name='id' id='id' value=''>
					<input type='hidden' name='Acc' id='Acc' value=''>
					<input type='hidden' name='placa' id='placa' value=''>
					<input type='hidden' name='fecha' id='fecha' value=''>
					<input type='hidden' name='hora' id='hora' value=''>
					<input type='hidden' name='oficina' id='oficina' value=''>
					<input type='hidden' name='flota' id='flota' value=''>
					</form><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></body>";
	}
}

function verificacion_disponibilidad()
{
	global $Flota,$cCiudad,$Disp_flota,$Disp_aoa,$Estado,$Citado,$Pyp,$NUSUARIO;
	html('VERIFICACION DE DISPONIBILIDAD');
	include('inc/link.php');
	$Oficina=qom("select id,hora_inicial,hora_final from oficina where ciudad='$cCiudad'",$LINK);
	$Ciudad=qo1m("select t_ciudad('$cCiudad')",$LINK);
	$Aseguradora=qom("Select * from aseguradora where id=$Flota",$LINK);
	$Hoy1=date('Y-m-d');
	$Ahora=date('Y-m-d');
	$CFI=aumentadias(date('Y-m-d'),-2);
	$CFF=aumentadias(date('Y-m-d'),10);
	echo "<body><script language='javascript'>centrar();</script><hr color='brown'><h3>VERIFICACION DE DISPONIBILIDAD <i>Ciudad: $Ciudad</i></h3>
				<table border cellspacing=0 width='100%' bgcolor='dddddd'>";
	$Sec=1;
	echo "<tr><td>Kilometraje</td>";
	$Limite_dias=15;
	while($Sec<=$Limite_dias)
	{
		$Dia=date('w',strtotime($Ahora));
		$Ndia=dia_semana($Dia);
		if($Dia==0 || $Dia==6) $Fondo='ffdddd'; else $Fondo='ffffff';
		echo "<th nowrap='yes' style='font-size:12;font-weight:bold;background-color:$Fondo;color:000000'>$Ahora $Ndia</th>";
		$Sec++;
		$Ahora=date('Y-m-d',strtotime(aumentadias($Ahora,1)));
	}
		////////////  CITAS PRE PROGRAMADAS //////

		///////-----------------------------------------------------------------------
		////////////////////////////////////////////////////          VEHICULOS DE LA FLOTA               ////////////////////////////////////////////////////////////////////////////////
	echo "</tr>";
	$Ahora=date('Y-m-d');
	//------------------------------------------------------------------------------------------------------------------------------------
	if($Picos=mysql_query("select * from picoyplaca	where ciudad='$cCiudad' and  fecha_final>'$Ahora' ",$LINK))
	{$Contador=0;while($Pi=mysql_fetch_object($Picos))  { $Pyp[$Contador]=new pico_y_placa($Pi);$Contador++; }	}
	if($Disponibles=mysql_query("select distinct v.id,v.placa,kilometraje(v.id) as kmf,ultima_ubicacion as oficina
									FROM vehiculo v,ubicacion u WHERE u.flota='$Flota' and u.vehiculo=v.id and u.fecha_final>='$Ahora' and v.ultima_ubicacion=$Oficina->id
									and (v.inactivo_desde='00000-00-00' or v.inactivo_desde>'$Ahora') and v.flota_distinta=1 ORDER BY kmf",$LINK))
	{
		$Contador=0; $id_disponibles='0';
		while($D=mysql_fetch_object($Disponibles)) { $Disp_flota[$Contador]=new Pdispon($D,$S->aseguradora); $Contador++;$id_disponibles.=','.$D->id; }
		$Estados=mysql_query(" select u.*,t_estado_vehiculo(u.estado) as nestado, hdevol(u.vehiculo,u.fecha_final) as hdev
								FROM ubicacion u WHERE u.vehiculo in ($id_disponibles) and u.fecha_final>='$Ahora' order by u.vehiculo,u.estado ",$LINK);
		$IniCitas=date('Y-m-d',strtotime(aumentadias($Ahora,-8)));
		$Citas=mysql_query("select c.*,t_siniestro(c.siniestro) as nsiniestro,v.id as idv FROM cita_servicio c,vehiculo v
								WHERE ((c.fecha >='$IniCitas' and c.estado='P') or (c.fecha>='$Ahora' and c.estado in ('P','C') and c.estadod='P'))  and c.oficina=$Oficina->id
								and c.placa=v.placa and v.id in ($id_disponibles) ",$LINK);
		while($C=mysql_fetch_object($Citas)) {$Citado[$C->idv][count($Citado[$C->idv])]=new Pcita($C);}
		while($D=mysql_fetch_object($Estados)) { $Estado[$D->vehiculo][count($Estado[$D->vehiculo])]=new Pestado($D); }
		echo "<tr><th colspan=".($Limite_dias+1)." style='font-size:16;font-weight:bold;'>FLOTA: $Aseguradora->nombre [".count($Disp_flota)." vehiculos]</TH></tr>";
		for($i=0;$i<count($Disp_flota);$i++)
		{
			$Disp_flota[$i]->verifica_estado();
			$Disp_flota[$i]->pinta($Limite_dias,$Oficina,$i,1,$LINK,true);
		}
	}
	else echo "No hay disponibles. ";
	////////////////////////////////////////////////////          VEHICULOS DE AOA               ////////////////////////////////////////////////////////////////////////////////
	if($Disponibles=mysql_query("select distinct v.id,v.placa,kilometraje(v.id) as kmf,ultima_ubicacion as oficina
									FROM vehiculo v,ubicacion u WHERE u.vehiculo=v.id and u.fecha_final>='$Ahora' and v.ultima_ubicacion=$Oficina->id
									and (v.inactivo_desde='00000-00-00' or v.inactivo_desde>'$Ahora') and v.flota_distinta=0 ORDER BY kmf",$LINK))
	{
		$Contador=0; $id_disponibles='0';
		while($D=mysql_fetch_object($Disponibles)) { $Disp_aoa[$Contador]=new Pdispon($D,6); $Contador++;$id_disponibles.=','.$D->id; }
		$Estados=mysql_query(" select u.*,t_estado_vehiculo(u.estado) as nestado, hdevol(u.vehiculo,u.fecha_final) as hdev
								FROM ubicacion u WHERE u.vehiculo in ($id_disponibles) and u.fecha_final>='$Ahora' order by u.vehiculo,u.estado ",$LINK);
		$IniCitas=date('Y-m-d',strtotime(aumentadias($Ahora,-8)));
		$Citas=mysql_query("select c.*,t_siniestro(c.siniestro) as nsiniestro,v.id as idv FROM cita_servicio c,vehiculo v
								WHERE ((c.fecha >='$IniCitas' and c.estado='P') or (c.fecha>='$Ahora' and c.estado in ('P','C') and c.estadod='P'))  and c.oficina=$Oficina->id
								and c.placa=v.placa and v.id in ($id_disponibles) ",$LINK);
		while($C=mysql_fetch_object($Citas)) {$Citado[$C->idv][count($Citado[$C->idv])]=new Pcita($C);}
		while($D=mysql_fetch_object($Estados)) { $Estado[$D->vehiculo][count($Estado[$D->vehiculo])]=new Pestado($D); }
		echo "<tr><th colspan=".($Limite_dias+1)." style='font-size:16;font-weight:bold;'>FLOTA: AOA [".count($Disp_aoa)." vehiculos] </TH></tr>";
		for($i=0;$i<count($Disp_aoa);$i++)
		{
			$Disp_aoa[$i]->verifica_estado();
			$Disp_aoa[$i]->pinta($Limite_dias,$Oficina,$i,2,$LINK,true);
		}
	}
	else echo "No hay disponibles. ";
	mysql_close($LINK);
	echo "</table></body>";
}

function dia_semana($d)
{
	switch($d)
	{
		case 0: return 'Domingo';
		case 1: return 'Lunes';
		case 2: return 'Martes';
		case 3: return 'Miércoles';
		case 4: return 'Jueves';
		case 5: return 'Viernes';
		case 6: return 'Sábado';
	}
}

function solicita_activacion()
{
	global $Fecha,$Placa,$Siniestro,$NUSUARIO;
	$Sin=qo("select * from siniestro where id=$Siniestro");
	$Ciudad=qo("select * from ciudad where codigo='$Sin->ciudad' ");
	$Ruta1="utilidades/Operativo/operativo.php?Acc=activar_sinlogo&Placa=$Placa&Fecha=$Fecha&Usuario=GABRIEL SANDOVAL";
	$Ruta2="utilidades/Operativo/operativo.php?Acc=activar_sinlogo&Placa=$Placa&Fecha=$Fecha&Usuario=HENRY GONZALEZ";
	$Ruta3="utilidades/Operativo/operativo.php?Acc=activar_sinlogo&Placa=$Placa&Fecha=$Fecha&Usuario=ARTURO QUINTERO RODRIGUEZ";

	$Mensaje1="<body>Solicitud de Activación Vehículo de AOA<br><br>Placa: <b>$Placa</b><br>Fecha: <b>$Fecha</b><br>".
		"Funcionario que solicita: $NUSUARIO <br>Siniestro: <b>$Sin->numero $Sin->asegurado_nombre</b><br>Ciudad: <b>$Ciudad->nombre ($Ciudad->departamento)</b><br>".
		"Para activar haga click aquí: <a href='https://www.aoacolombia.com/i.php?i=".base64_encode("\$Programa='$Ruta1';\$Fecha_control='".date('Y-m-d')."';")."' target='_blank'>Autorizar</a></body>";

	$Mensaje2="<body>Solicitud de Activación Vehículo de AOA<br><br>Placa: <b>$Placa</b><br>Fecha: <b>$Fecha</b><br>".
		"Funcionario que solicita: $NUSUARIO <br>Siniestro: <b>$Sin->numero $Sin->asegurado_nombre</b><br>Ciudad: <b>$Ciudad->nombre ($Ciudad->departamento)</b><br>".
		"Para activar haga click aquí: <a href='https://www.aoacolombia.com/i.php?i=".base64_encode("\$Programa='$Ruta2';\$Fecha_control='".date('Y-m-d')."';")."' target='_blank'>Autorizar</a></body>";

	$Mensaje3="<body>Solicitud de Activación Vehículo de AOA<br><br>Placa: <b>$Placa</b><br>Fecha: <b>$Fecha</b><br>".
		"Funcionario que solicita: $NUSUARIO <br>Siniestro: <b>$Sin->numero $Sin->asegurado_nombre</b><br>Ciudad: <b>$Ciudad->nombre ($Ciudad->departamento)</b><br>".
		"Para activar haga click aquí: <a href='https://www.aoacolombia.com/i.php?i=".base64_encode("\$Programa='$Ruta3';\$Fecha_control='".date('Y-m-d')."';")."' target='_blank'>Autorizar</a></body>";

	$Email_usuario=usuario('email');

	$Envio1=enviar_gmail($Email_usuario /*de */,
										$NUSUARIO /*Nombre de */ ,
										"gabrielsandoval@aoacolombia.com,Gabriel Sandoval" /*para */,
										"$Email_usuario,$NUSUARIO" /*con copia*/,
										"Solicitud Activacion $Placa Flota AOA" /*Objeto */,
										$Mensaje1);

	$Envio2=enviar_gmail($Email_usuario /*de */,
										$NUSUARIO /*Nombre de */ ,
										"henrygonzalez@aoacolombia.com,Henry Gonzalez" /*para */,
										"$Email_usuario,$NUSUARIO" /*con copia*/,
										"Solicitud Activacion $Placa Flota AOA" /*Objeto */,
										$Mensaje2);

	$Envio3=enviar_gmail($Email_usuario /*de */,
										$NUSUARIO /*Nombre de */ ,
										"arturoquintero@aoacolombia.com,Arturo Quintero" /*para */,
										"$Email_usuario,$NUSUARIO" /*con copia*/,
										"Solicitud Activacion $Placa Flota AOA" /*Objeto */,
										$Mensaje3);
	if($Envio1 && $Envio2 && $Envio3)
		echo "Envio exitoso a: gabrielsandoval@aoacolombia.com, arturoquintero@aoacolombia.com, henrygonzalez@aoacolombia.com, $Email_usuario ";
	else
		echo "Falla en el envío del mail.";

}

function agendar_cita()
{
	global $id,$placa,$fecha,$hora,$oficina,$flota;
	$OF=qo("select * from oficina where id=$oficina");
	$S=qo("select * from siniestro where id=$id");
	$Ciudad=qo1("select t_ciudad($S->ciudad)");
	$Aseguradora=qo("Select * from aseguradora where id=$S->aseguradora");
	if($S->ciudad_original) $Ciudado=qo1("select t_ciudad($S->ciudad_original)"); else $Ciudado=false;
	$Observaciones=nl2br($S->observaciones);
	$Nhora=date('h:i A',strtotime($fecha.' '.$hora));
	html('AGENDAMIENTO DE CITA');
	echo "<script language='javascript'>
		function carga()
		{
			centrar(900,700);
		}
		function guardar_cita()
		{
			if(confirm('Esta seguro de Agendar esta cita?'))
			{
				document.forma.Acc.value='agendar_cita_ok';
				document.forma.submit();
				document.forma.Enviar.style.visibility='hidden';
			}
		}

		function cerrar_adjudicado()
		{
			opener.cerrar_adjudicado();
			window.close();void(null);
		}

		function valida_domicilio()
		{
			if(alltrim(document.forma.dir_domicilio.value) || alltrim(document.forma.tel_domicilio.value))
			{
				document.forma.btn_envio_aviso.style.visibility='visible';
			}
			else
			{
				document.forma.btn_envio_aviso.style.visibility='hidden';
			}
		}
	</script>
	<body onload='carga()' style='font-size:14' bgcolor='ffffdd'>
	<form action='zcallcenter.php' method='post' target='Oculto_agendar' name='forma' id='forma'>
	<hr color='eeeeee'><a href='javascript:void(null);' class='sinfo'><b><u>Observaciones registradas (pase el mouse para ver las observaciones)</u></b><span style='width:800'>
				<font style='font-size:10;color:00000'>$Observaciones</font></span></a><br>
				<hr color='eeeeee'><H3>AGENDAMIENTO DE CITA</H3>
				<br />Oficina: <b style='color:000000'>$OF->nombre ($OF->direccion)</b> <input type='hidden' name='oficina' value='$oficina'>
				<input type='hidden' name='siniestro' id='siniestro' value='$id'>
				<input type='hidden' name='flota' id='flota' value='$flota'>
				<br /><br />Vehiculo asignado: <input type='text' name='placa' value='$placa' readonly style='font-size:12;font-weight:bold' size=7>
				Fecha y hora de la cita: <input type='hidden' name='fecha' value='$fecha'><b style='font-size:12;font-weight:bold;color:000000'>".fecha_completa($fecha)."</b> HORA:
				<input type='hidden' name='hora' value='$hora'> <b style='font-size:12;font-weight:bold;color:000000'>$Nhora</b>
				<br /><br /><b style='font-size:16;color:0000ff;text-decoration:blink;'>Persona quien va a recoger el vehículo o conductor: </b>
				<input type='text' name='conductor' value='$S->declarante_nombre' style='font-size:12;font-weight:bold' size='50'><br />
				Este será el nombre que aparezca en el ACTA DE ENTREGA. Si quien recoge el vehículo es un TERCERO, indique los requisitos adicionales.
				<br />
				<table border cellspacing='0'>
				<tr><td style='background-color:ffff00;font-weight:bold;'>CORREO ELECTRONICO</td></tr>
				<tr><td>
					Correo electrónico del asegurado: <input type='text' name='declarante_email' value='$S->declarante_email' size=50 style='font-size:14px'>
				</td></tr></table>
				<br />Observaciones:
				<br /><textarea name='observaciones' id='observaciones' style='font-size:14' rows='5' cols='100'></textarea><br /><br />
					<table bgcolor='ddddff'><tr><td align='center' colspan=2 style='font-size:16'><B>DOMICILIO</B></td></tr>
					<tr><td align='right' style='font-size:14'>Dirección Domicilio:</td><td><input type='text' style='font-size:14' name='dir_domicilio' size='100' maxlength='200' onblur='valida_domicilio();'></td></tr>
					<tr><td align='right' style='font-size:14'>Teléfono Domicilio:</td><td><input type='text' style='font-size:14' name='tel_domicilio' size='30' maxlength='50' onblur='valida_domicilio();'></td></tr>
					<tr ><td align='center' colspan='2'><input type='button' id='btn_envio_aviso'  name='btn_envio_aviso' value=' Enviar Aviso de Llamada a Autorizaciones '
								style='visibility:hidden;font-weight:bold;font-size:14px;'	onclick='aviso_autorizacion();'></td></tr>
					</table>
					<br /><br />
				Señor usuario ".$_SESSION['Nombre'].", el agendamiento de la cita quedará registrada a nombre suyo con fecha y hora de registro.<br /><br />
				<input type='button' id='Enviar' name='Enviar' value='AGENDAR CITA' style='font-size:14;font-weight:bold;height:30px;width:300px' onclick='guardar_cita()'>
				<input type='hidden' name='Acc' id='' value=''><input type='hidden' name='siniestro' id='siniestro' value='$id'>
				</form>
				<iframe name='Oculto_agendar' id='Oculto_agendar' style='visibility:hidden' width=1 height=1></iframe></body>";
}

function agendar_cita_ok()
{
	global $oficina,$siniestro,$flota,$placa,$fecha,$hora,$conductor,$observaciones,$NUSUARIO,$dir_domicilio,$tel_domicilio,$declarante_email;
	echo "<body><script language='javascript'>";
	include('inc/link.php');
	$OF=qom("select * from oficina where id=$oficina",$LINK);
	$Nhora=date('h:i A',strtotime($fecha.' '.$hora));
	$Hoy=date('Y-m-d H:i');
	$Usuario=$_SESSION['Nombre'];
	$Dia=dia_semana(date('w',strtotime($fecha)));
	$Nciudad=qo1m("select t_ciudad(ciudad) from siniestro where id=$siniestro",$LINK);
	$Ndia=dia_semana(date('w',strtotime($fecha))).' '.date('d',strtotime($fecha)).' de '.mes(date('m',strtotime($fecha))).' de '.date('Y',strtotime($fecha));
	mysql_query("update siniestro set observaciones=concat(observaciones,\"\n$Usuario [$Hoy] Agenda cita para $Dia  $fecha a la(s) $hora en la ciudad de $Nciudad \"),estado=3,
			 declarante_email='$declarante_email' where id=$siniestro",$LINK);
	$H1=date('Y-m-d'); $H2=date('H:i:s');
	mysql_query("insert into seguimiento (siniestro,fecha,hora,usuario,descripcion,tipo) values ($siniestro,'$H1','$H2','$Usuario','Agenda cita para $Dia $fecha a la(s) $hora con el vehículo $placa en la ciudad de $Nciudad ".
		($dir_domicilio?" Domicilio: $dir_domicilio Tel: $tel_domicilio.":"")."',5)",$LINK);
	$Idn=mysql_insert_id($LINK);
	graba_bitacora('seguimiento','A',$Idn,'Adiciona registro',$LINK);
	$S=qom("select * from siniestro where id=$siniestro",$LINK);

	$Aseguradora=qom("select * from aseguradora where id=$S->aseguradora",$LINK);
	mysql_query("insert into cita_servicio (oficina,siniestro,flota,placa,fecha,hora,conductor,observaciones,agendada_por,fecha_agenda,estado,dir_domicilio,tel_domicilio)
			values ('$oficina','$siniestro','$flota','$placa','$fecha','$hora','$conductor','$observaciones','".$_SESSION['Nombre']."','".date('Y-m-d H:i:s')."','P','$dir_domicilio','$tel_domicilio')",$LINK);
	$Idn=mysql_insert_id($LINK);
	graba_bitacora('cita_servicio','A',$Idn,'Adiciona registro.',$LINK);
	///  BUSQUEDA SI YA TIENE UN ARRIBO DE ASEGURADO EL MISMO DIA
	if($Arribo=qo1m("select arribo from cita_servicio where siniestro=$siniestro and date_format(arribo,'%Y-%m-%d')='$fecha' and estado='W' ",$LINK))
	{
		mysql_query("update cita_servicio set arribo='$Arribo' where id=$Idn",$LINK);
	}
	mysql_close($LINK);
	$Email_usuario=usuario('email');
	if($S->declarante_email)
	{
		if($dir_domicilio)
		{
			$Correo="Respetado(a) Señor(a) $conductor <br><br>Reciba cordial saludo. <br><br>".
					"AOA confirma la cita que ha sido programada para el próximo ".fecha_completa($fecha)." a las $Nhora a fín de hacer entrega de un $Aseguradora->nombre_servicio ".
					"en la dirección $dir_domicilio Teléfono $tel_domicilio.<br><br>".
					"Los documentos que debe presentar son:<br><br>".
					"- Cédula de Ciudadanía original.<br>- Licencia de Conducción original debidamente registrada (ante el Runt o MinTransporte).".
					"<br>- Tarjeta de crédito utilizada para la constitución de la Garantía.".
					"<br>- El Tarjetahabiente debe estar presente y suministrar la cédula de ciudadanía para firmar el boucher de la Garantía. ".
					"<br>- Si usted no es el (la) asegurado(a), debe presentar una carta diligenciada por el (la) asegurado(a) indicando que usted está autorizado(a) para proceder con la gestión del servicio. ".
					"Adicionalmente debe traer una fotocopia de la Cédula de Ciudadanía del (la) asegurado(a).".
					"<br><br>Agradecemos tener en cuenta las siguientes recomendaciones:<br><br>".
					"* Estar puntual a la hora pactada en esta cita. Después de 15 minutos se asumirá que no pudo recibir el vehículo y nuestro Call Center se comunicará con Usted para pactar una nueva cita.".
					"<br>* Recuerde que después de cumplido el tiempo del servicio o el kilometraje máximo permitido, ".
					"usted debe devolver el vehículo en las mismas condiciones en que le fué entregado. ".
					"<br>* La hora de devolución del vehículo debe ser la misma de la entrega, o sea, $Nhora.".
					"<br>* La Garantía será devuelta 7 días después de devuelto el vehículo a AOA con el fin de poder verificar que no existan comparendos electrónicos.".
					"<br><br>Cualquier inquietud puede comunicarse através de nuestro Call Center en la línea: $Aseguradora->numero_call de la ciudad de Bogotá D.C.<br><br>Cordialmente<br><br>".
					"<br>Departamento de Call Center<br>Administración Operativa Automotriz<br>Teléfono: $Aseguradora->numero_call<br>atencionalcliente@aoacolombia.com";
		}
		else
		{
			$Correo="Respetado(a) Señor(a) $conductor <br><br>Reciba cordial saludo. <br><br>".
					"AOA confirma la cita que ha sido programada para el próximo ".fecha_completa($fecha)." a las $Nhora a fín de hacer entrega de un $Aseguradora->nombre_servicio ".
					"en las instalaciones de Administración Operativa Automotríz S.A. sucursal $OF->nombre, ubicada en $OF->direccion $OF->barrio Tel: $Aseguradora->numero_call<br><br>".
					"Los documentos que debe presentar son:<br><br>".
				"- Cédula de Ciudadanía original.<br>- Licencia de Conducción original debidamente registrada (ante el Runt o MinTransporte).".
				"<br>- Tarjeta de crédito con el cupo de ".coma_format($Aseguradora->garantia).
				" de acuerdo a lo indicado por el agente de Call Center para la constitución de la Garantía. Franquicias aceptadas: Visa, Master Card, Diners, American Express. ".
				"<br>- El Tarjetahabiente debe estar presente y suministrar la cédula de ciudadanía para firmar el voucher de la Garantía. ".
				"<br>- Si usted no es el (la) asegurado(a), debe presentar una carta diligenciada por el (la) asegurado(a) indicando que usted está autorizado(a) para proceder con la gestión del servicio. ".
				"Adicionalmente debe traer una fotocopia de la Cédula de Ciudadanía del (la) asegurado(a).".
				"<br><br>Agradecemos tener en cuenta las siguientes recomendaciones:<br><br>".
				"* Estar puntual a la hora pactada en esta cita. Después de 15 minutos se asumirá que no pudo recibir el vehículo y nuestro Call Center se comunicará con Usted para pactar una nueva cita.".
				"<br>* Recuerde que después de cumplido el tiempo del servicio o el kilometraje máximo permitido, ".
				"usted debe devolver el vehículo en las mismas condiciones en que le fué entregado. ".
				"<br>* La hora de devolución del vehículo debe ser la misma de la entrega, o sea, $Nhora.".
				"<br>* La Garantía será devuelta 7 días después de devuelto el vehículo a AOA con el fin de poder verificar que no existan comparendos electrónicos.".
				"<br><br>Cualquier inquietud puede comunicarse através de nuestro Call Center en la línea: $Aseguradora->numero_call de la ciudad de Bogotá D.C.<br><br>Cordialmente<br><br>".
				"<br>Departamento de Call Center<br>Administración Operativa Automotriz<br>Teléfono: $Aseguradora->numero_call<br>atencionalcliente@aoacolombia.com";
		}


		$Envio3=enviar_gmail("atencionalcliente@aoacolombia.com" /*de */,
										"Call Center AOA" /*Nombre de */ ,
										"$S->declarante_email,$conductor" /*para */,
										"$Email_usuario,$NUSUARIO" /*con copia*/,
										"Cita programada - Servicio AOA" /*Objeto */,
										$Correo);

	}

	echo ($Envio3?"alert('Email enviado a $S->declarante_email');":"")."
	window.open('zcallcenter.php?Acc=enviar_mail_servicio&idcita=$Idn','_self');</script>
	</body>";
}

function enviar_mail_servicio()
{
	global $idcita,$NUSUARIO;
	$Cita=qo("select * from cita_servicio where id=$idcita");
	$Ndia=dia_semana(date('w',strtotime($Cita->fecha))).' '.date('d',strtotime($Cita->fecha)).' de '.mes(date('m',strtotime($Cita->fecha))).' de '.date('Y',strtotime($Cita->fecha));
	$Ofi=qo("select * from oficina where id=$Cita->oficina");
	if($Ofi->email_e && $Ofi->envio_adjudicacion)
	{
		$S=qo("select * from siniestro where id=$Cita->siniestro");
		$BA=qo("select nombre from aseguradora where id=$S->aseguradora");
		$Correo="<body>Señor(a) $Ofi->contacto Reciba cordial saludo.<br><br>Por medio del presente e-mail se le informa oficialmente sobre la programación de cita para el dia $Ndia, ".
						"asegurado de $BA->nombre. La información se detalla a continuación:<br><br>".
						"<table border cellspacing='0'>".
						"<tr><td>Numero de Siniestro:</td><td>$S->numero</td></tr>".
						"<tr><td>Fecha y Hora de la cita:</td><td>$Ndia a las ".date('h:i A',strtotime($Cita->hora))."</td></tr>".
						"<tr><td>Vehículo Asignado:</td><td>$Cita->placa</td></tr>".
						"<tr><td>Nombre del asegurado:</td><td>$S->asegurado_nombre</td></tr>".
						"<tr><td>Nombre del declarante:</td><td>$S->declarante_nombre</td></tr>".
						"<tr><td>Telefonos del declarante:</td><td>$S->declarante_telefono $S->declarante_tel_resid $S->declarante_tel_ofic $S->declarante_celular $S->declarate_tel_otro</td></tr>".
						"<tr><td>Nombre del conductor:</td><td>$S->conductor_nombre</td></tr>".
						"<tr><td>Telefonos del conductor:</td><td>$S->conductor_tel_resid $S->conductor_tel_ofic $S->conductor_celular $S->conductor_tel_otro</td></tr>".
						"</table><br><br>Observaciones de la cita: $Cita->observaciones<br><br>Cordialmente,<br><br>".$_SESSION['Nombre']."<BR>Asesor de Servicio al Cliente<br>".
						"Call Center AOA Colombia S.A.<br>Pbx:7560510<br>Fax: 7560512<br>contacto@aoacolombia.com<br></body><BR><BR>";
		$Correo.="\n\n Señor(a) $Ofi->contacto Reciba cordial saludo.\n\nPor medio del presente e-mail se le informa oficialmente sobre la programación de cita para el dia $Ndia, ".
						"asegurado de $BA->nombre. La información se detalla a continuación:\n\n".
						"Numero de Siniestro:   $S->numero\n".
						"Fecha y Hora de la cita: $Ndia a las ".date('h:i A',strtotime($Cita->hora))."\n".
						"Vehículo Asignado: $Cita->placa \n".
						"Nombre del asegurado: $S->asegurado_nombre\n".
						"Nombre del declarante: $S->declarante_nombre \n".
						"Telefonos del declarante: $S->declarante_telefono $S->declarante_tel_resid $S->declarante_tel_ofic $S->declarante_celular $S->declarate_tel_otro \n".
						"Nombre del conductor: $S->conductor_nombre\n".
						"Telefonos del conductor: $S->conductor_tel_resid $S->conductor_tel_ofic $S->conductor_celular $S->conductor_tel_otro\n".
						"\n\nObservaciones de la cita: $Cita->observaciones\n\nCordialmente,\n\n".$_SESSION['Nombre']."\nAsesor de Servicio al Cliente\n".
						"Call Center AOA Colombia S.A.\nPbx: 7560510\nFax:7560512\ncontacto@aoacolombia.com\n<br><br>".
						"\n\nNOTA: Este correo se envia en formato html y en formato plano para compatibilidad con los distintos sistemas de correo.";

		$Email_usuario=usuario('email');
		$Envio3=enviar_gmail($Email_usuario /*de */,
		$NUSUARIO /*Nombre de */ ,
		"$Ofi->email_e,$Ofi->contacto" /*para */,
		"$Email_usuario,$NUSUARIO" /*con copia*/,
		"CITA PROGRAMADA" /*Objeto */,
		$Correo);

		echo "
			<body><script language='javascript'>
			alert('Envio de Mail exitoso a $Ofi->email_e $Ofi->contacto');
			parent.cerrar_adjudicado();
			</script></body>";
	}
	else
	{
		echo "<body><script language='javascript'>parent.cerrar_adjudicado();</script></body>";
	}
}

function proceso_no_adjudicacion()
{
	global $id,$NUSUARIO,$Causal;
	html();
	$Siniestro=qo("select aseguradora from siniestro where id=$id");
	$Aseguradora=qo("select guion_contacto_exito,retencion from aseguradora where id=$Siniestro->aseguradora");
	echo "<script language='javascript'>
	function volver()
	{window.open('zcallcenter.php?Acc=proceso_contacto_exitoso&id=$id','_self');}
	function valida_no_adjudicacion()
	{
		if(document.forma.Causal.value)
		{
			if(confirm('Desea continuar con la NO ADJUDICACION de este siniestro?'))
			{
				document.forma.submit();
			}
		}
		else
		{
			alert('Debe seleccionar la causal de no adjudicación');
			document.forma.Causal.style.backgroundColor='ffff00';
		}
	}
	function selecciona_causal(valor)
	{
		window.open('zcallcenter.php?Acc=proceso_no_adjudicacion&id=$id&Causal='+valor,'_self');
	}
	function activa_submit()
	{
		document.getElementById('continuar').style.visibility='visible';
	}
	</script>
	<body ><h3>Agente: $NUSUARIO - Proceso No Adjudicación</h3>
	<a class='info' onclick='volver()'><img src='gifs/atras.png' border='0'><span style='width:200px'>Volver a la pantalla anterior</span></a><br />
	<form action='zcallcenter.php' method='post' target='_self' name='forma' id='forma'>
		<br />SELECCIONE LA CAUSAL:</B> ".
		menu1('Causal',"select id,nombre from causal where id in (1,2,5,10) order by id",$Causal,1,"font-size:14;font-weight:bold;"," onchange='selecciona_causal(this.value);' ")."<br />";
	if($Causal)
		$Subcausales=qo1("select count(id) from subcausal where causal='$Causal'");
		echo "<br />Seleccione la Sub-Causal : ".menu1("subcausal","select id,nombre from subcausal where causal='$Causal'",0,1,"font-size:14;font-weight:bold;"," onchange='activa_submit();' ");
	echo "
			<br /><br />
		<input type='hidden' name='Acc' value='proceso_no_adjudicacion_ok'>
		<input type='hidden' name='id' value='$id'>
		<input type='hidden' name='retencion_aseguradora' value='$Aseguradora->retencion'>
		<input type='button' id='continuar' value='CONTINUAR CON LA NO ADJUDICACION' onclick='valida_no_adjudicacion()' style='font-size:14;visibility:hidden'>
	</form>";
	if(!$Subcausales && $Causal) echo "<script language='javascript'>activa_submit();</script>";

}

function proceso_no_adjudicacion_ok()
{
	global $id,$Causal,$NUSUARIO,$Ahora,$subcausal,$retencion_aseguradora;
	$Subc=qo("select * from subcausal where id='$subcausal' ");
	$ncausal=qo1("select nombre from causal where id=$Causal").' - '.$Subc->nombre;
	$Sin=qo("select id,estado,retencion,causal,subcausal from siniestro where id=$id");
	$Fecha=date('Y-m-d');$Hora=date('H:i:s');$Codigo=6; /*6: No Adjudicación */
	if($retencion_aseguradora)
	{
		if($Sin->retencion && $Sin->causal && $Sin->subcausal)  // Ya se habia grabado el estado en pendiente y debe pasar a no adjudicado
		{
			q("update siniestro set estado=1,causal=$Causal,subcausal='$subcausal',observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]:No Adjudica causal:$ncausal') where id=$id"); // cambia el estado a NO ADJUDICADO = 1
			graba_bitacora('siniestro','M',$id,'Cambia estado a No Adjudicado.');
			$Idn=q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','No Adjudica causal: $ncausal. SubCausal: $Subc->nombre',$Codigo)");
			graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
		}
		elseif($Subc->retencion) // Si no hay causal y subcausal significa que es susceptible de retencion, se verifica si la subcausal tiene marca de retención y se graba en estado pendiente
		{
			q("update siniestro set retencion=1,causal=$Causal,subcausal='$subcausal',observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]:No Adjudica causal:$ncausal - $Subc->nombre \n\n----- RETENCION -----\n') where id=$id"); // PERMANECE PENDIENTE PERO CON RETENCION
			graba_bitacora('siniestro','M',$id,'Cambia Siniestro Retenido, causal y subcausal.');
			$Idn=q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','Retención de Siniestro: $ncausal. SubCausal: $Subc->nombre',$Codigo)");
			graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
		}
		else
		{
			q("update siniestro set estado=1,causal=$Causal,subcausal='$subcausal',observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]: No Adjudica causal:$ncausal - $Subc->nombre') where id=$id"); // cambia el estado a NO ADJUDICADO = 1
			graba_bitacora('siniestro','M',$id,'Cambia estado a No Adjudicado.');
			$Idn=q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','No Adjudica causal: $ncausal. SubCausal: $Subc->nombre',$Codigo)");
			graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
		}
	}
	else
	{
		q("update siniestro set estado=1,causal=$Causal,subcausal='$subcausal',observaciones=concat(observaciones,'\n$NUSUARIO [$Ahora]: No Adjudica causal:$ncausal - $Subc->nombre') where id=$id"); // cambia el estado a NO ADJUDICADO = 1
		graba_bitacora('siniestro','M',$id,'Cambia estado a No Adjudicado.');
		$Idn=q("insert into seguimiento (siniestro,usuario,fecha,hora,descripcion,tipo) values ($id,'$NUSUARIO','$Fecha','$Hora','No Adjudica causal: $ncausal. SubCausal: $Subc->nombre',$Codigo)");
		graba_bitacora('seguimiento','A',$Idn,'Adiciona Registro');
	}
	echo "<script language='javascript'>
			function carga()
			{
				alert('Proceso de NO ADJUDICACION realizado satisfactoriamente.');
				parent.cerrar();
			}
		</script>
		<body onload='carga()'></body>";
}

function solicita_reactivacion()
{
	global $id;
	$Sin=qo("select * from siniestro where id=$id");
	$Ciudad=qo("select * from ciudad where codigo='$Sin->ciudad' ");
	if($Sin->ciudad_original) $Ciudado=qo("select * from ciudad where codigo='$Sin->ciudad_original' ");
	else $Ciudado=$Ciudad;
	$Nusuario=$_SESSION['Nombre'];
	$Fecha=date('Y-m-d H:i');
	html('Solicitud de Modificación');
	$Ciudades=menu1("ciudad","select ciu.codigo,ciu.nombre as nciudad from ciudad ciu,oficina ofi where ofi.ciudad=ciu.codigo order by nciudad ",$Ciudad,1);
	echo "<script language='javascript'>
			function carga() {centrar(500,500);}
			function activaruno() { if(document.forma.uno.checked) {document.getElementById('tduno').style.visibility='visible';document.forma.justificacion1.focus();} else document.getElementById('tduno').style.visibility='hidden'; }
			function activardos() { if(document.forma.dos.checked) document.getElementById('tddos').style.visibility='visible'; else document.getElementById('tddos').style.visibility='hidden'; }
			function enviar_solicitud()
			{
				with(document.forma)
				{
					if(uno.checked)
					{
						if(!alltrim(justificacion1.value))
						{
							alert('Debe justificar por qué se desea pasar a Pendiente el estado de este siniestro');
							justificacion1.style.backgroundColor='ffffdd';
							return false;
						}
					}
					if(dos.checked)
					{
						if(!alltrim(justificacion2.value))
						{
							alert('Debe justificar por qué se desea cambiar de ciudad este siniestro');
							justificacion2.style.backgroundColor='ffffdd';
							return false;
						}
						if(!ciudad.value)
						{
							alert('Debe especificar una ciudad válida');
							ciudad.style.backgroundColor='ffffdd';
							return false;
						}
					}
					if(!(uno.checked || dos.checked))
					{
						alert('La solicitud debe contener uno o los dos conceptos que son Reactivación o Cambio de Ciudad');
						return false;
					}
				}
				document.forma.btn_enviar.style.visibility='hidden';
				document.forma.submit();
			}
		</script>
		<body onload='carga()'>
		<form action='zcallcenter.php' method='post' target='_self' name='forma' id='forma'>
		<h3>Solicitud de Modificación de Siniestro</h3>
		Usuario: $Nusuario   Fecha: $Fecha <br />
		<table border cellspacing='0' width='100%'>
			<tr><td>Reactivación <input type='checkbox' name='uno' onchange='activaruno();' ".($Sin->estado==1?'':"disabled")."> El estado actual es: <b>".qo1("select t_estado_siniestro($Sin->estado)")."</b></td></tr>
			<tr><td  id='tduno' style='visibility:hidden;'>Esta opción solicita pasar el estado a PENDIENTE por favor escriba la justificación: <br /><textarea name='justificacion1' style='font-family:arial;font-size:12' rows='4' cols='80' valign='top'></textarea></td></tr>
			<tr><td>Cambio de Ciudad <input type='checkbox' name='dos' onchange='activardos();'></td></tr>
			<tr><td id='tddos' style='visibility:hidden;'>Ciudad: $Ciudades Justificación: <br><textarea name='justificacion2' style='font-family:arial;font-size:12' rows='4' cols='80' valign='top'></textarea></td></tr>
		</table>
		<center><input type='button' id='btn_enviar' name='btn_enviar' value='Enviar Solicitud' onclick='enviar_solicitud()'></center>
		<input type='hidden' name='id' value='$id'><input type='hidden' name='Acc' value='solicita_reactivacion_ok'>
	</form>";

	die();
}

function solicita_reactivacion_ok()
{
	global $id,$uno,$dos,$justificacion1,$justificacion2,$ciudad,$Nusuario,$Hoy,$NUSUARIO,$Ahora;

	$uno=sino($uno);
	$dos=sino($dos);
	$IDM=q("insert into solicitud_modsin (siniestro,cambio_estado,justificacion1,cambio_ciudad,ciudad,justificacion2,solicitado_por,fec_solicitud) values
		('$id','$uno',\"$justificacion1\",'$dos','$ciudad',\"$justificacion2\",'$Nusuario','$Hoy' )");
	$Nueva_ciudad=qo1("select t_ciudad('$ciudad') ");
	$Sin=qo("select id,numero,asegurado_nombre,ciudad,ciudad_original,aseguradora,fec_autorizacion from siniestro where id=$id");
	$Ciudad=qo("select * from ciudad where codigo='$Sin->ciudad' ");
	if($Sin->ciudad_original) $Ciudado=qo("select * from ciudad where codigo='$Sin->ciudad_original' ");
	else $Ciudado=$Ciudad;
	$Aseguradora=qo1("select nombre from aseguradora where id=$Sin->aseguradora");
	if(dias($Sin->fec_autorizacion,date('Y-m-d')) > 180)
	{
		$Ruta1="utilidades/Operativo/operativo.php?Acc=modificar_siniestro&idm=$IDM&Fecha=$Hoy&Usuario=GABRIEL SANDOVAL";
		$Email_usuario=usuario('email');
		$Mensaje='';
		$Mensaje.="<body><b>SOLICITUD DE MODIFICACION DE SINIESTROS</B><BR><BR>Numero Siniestro: $Sin->numero - $Aseguradora<br>".
	"Asegurado:$Sin->asegurado_nombre<br>Ciudad: $Ciudad->nombre ($Ciudad->departamento) <br>Ciudad Original: $Ciudado->nombre ($Ciudado->departamento)<br>".
	"Fecha de autorización: $Sin->fec_autorizacion<br>";
		if($uno)
		{
			$Mensaje.="<br><b>Cambio de estado a PENDIENTE: </b>$justificacion1<br>";
		}
		if($dos)
		{
			$Mensaje.="<br><b>Cambio de ciudad a $Nueva_ciudad: </b>$justificacion2<br>";
		}
		$Mensaje.="<br>Funcionario que solicita: $Nusuario Fecha de solicitud: $Hoy <br><br>";
		$Mensaje.="Para activar haga click aquí: <a href='https://www.aoacolombia.com/i.php?i=".base64_encode("\$Programa='$Ruta1';\$Fecha_control='".date('Y-m-d')."';")."' target='_blank'>Aprobar la modificación</a></body>";
		$Envio=enviar_gmail($Email_usuario /*de */,
		$_SESSION['Nombre'] /*Nombre de */ ,
		"gabrielsandoval@aoacolombia.com,Gabriel Sandoval;henrygonzalez@aoacolombia.com,Henry Gonzalez;arturoquintero@aoacolombia.com,Arturo Quintero Rodriguez" /*para */,
		"" /*con copia*/,
		"Solicitud Modificacion Siniestro $Sin->numero" /*Objeto */,
		$Mensaje);
		if($Envio)
			echo "Envio exitoso a: gabrielsandoval@aoacolombia.com, henrygonzalez@aoacolombia.com, arturoquintero@aoacolombia.com, $Email_usuario";
		else
			echo "Falla en el envío del mail.";
	}
	else
	{
		if($uno)
		{
			q("update solicitud_modsin set aprobado_por='$NUSUARIO',fec_aprobacion='$Ahora' where id=$IDM");
			q("update siniestro set estado=5,causal=0,subcausal=0 where id=$id ");
			graba_bitacora('siniestro','M',$id,"Reactiva el siniestro");
			echo "Reactivación del Siniestro exitosa.";
		}
		if($dos)
		{
			q("update solicitud_modsin set aprobado_por='$NUSUARIO',fec_aprobacion='$Ahora' where id=$IDM");
			q("update siniestro set ciudad='$ciudad' where id=$id");
			graba_bitacora('siniestro','M',$id,"Cambia de ciudad");
			echo "Cambio de ciudad del Siniestro exitosa.";
		}
	}


	echo "<br /><br /><CENTER><INPUT TYPE='BUTTON' VALUE='CERRAR ESTA VENTANA' onclick='window.close();void(null);' style='font-size:16;font-weight:bold; height=30px;'></center>";
}

function procesar_compromiso()
{
	global $id;
	$Co=qo("select * from compromiso where id=$id");
	$Sin=qo("select * from siniestro where id=$Co->siniestro");

	html();
	echo "<script language='javascript'>
			function carga()
			{

			}
		</script>
		<body onload='carga()'><h3>Proceso del Compromiso</h3>
		<table bgcolor='dddddd'>
			<tr><th colspan=2>Datos del compromiso</th></tr>
			<tr><td align='right'>Siniestro:</td><td>$Sin->numero $Sin->asegurado_nombre</td></tr>
			<tr><td align='right'>Fecha de programación:</td><td>$Co->fecha_programacion</td></tr>
			<tr><td align='right'>Fecha del compromiso:</td><td>$Co->fecha $Co->hora</td></tr>
			<tr><td align='right'>Agente que programó el compromiso:</td><td>$Co->usuario</td></tr>
			<tr><td align='right'>Estado:</td><td>".($Co->estado=='P'?"Pendiente":"Cumplido $Co->fecha_cumplimiento")."</td></tr>
		</table>";
	if($Co->estado=='C')	echo "<b style='color:990000'>Este compromiso ya fue cumplido por: $Co->cumplido_por</b>";
	echo "
		</body>";
}




















?>