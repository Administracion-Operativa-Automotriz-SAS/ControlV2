<?php
/// PROGRAMA PARA CALIFICACION DE PROVEEDORES SEGUN SISTEMA DE CONTROL DE CALIDAD
/*
	LA PRIMERA CALIFICACION SE LLAMA "SELECCION" Y SE HACE SOLO UNA VEZ CADA VEZ QUE INGRESA UN NUEVO PROVEEDOR O A MEDIDA QUE SE VAYAN HACIENDO
	REQUISICIONES QUE COINCIDAN CON LOS BIENES O SERVICIOS RELACIONADOS COMO SUSCEPTIBLES DE seleccion SEGUN EL DEPARTAMENTO DE CALIDAD
*/

include('inc/funciones_.php');
include('inc/chart/Includes/FusionCharts.php');
sesion();
$NUSUARIO=$_SESSION['Nombre'];
$USUARIO=$_SESSION['User'];
$USUARIO=$_SESSION['User'];
$A_escala=Array(); // arreglo de escalas usando objetos.

if(!empty($Acc) && function_exists($Acc)) {eval($Acc.'();');die();}
inicio_proveedor();

function inicio_proveedor()
{
	html('SISTEMA DE CALIFICACION DE PROVEEDORES SEGUN CONTROL DE CALIDAD');
	echo "<script language='javascript'>function procesar()	{document.forma.submit();}</script>
	<body>
	<h3>SISTEMA DE CALIFICACION DE PROVEEDORES .:. CONTROL DE CALIDAD</H3>
	<form action='zproveedor.php' target='Tablero_proveedor' method='POST' name='forma' id='forma'>
		Fecha de Selección: ".pinta_FC('forma','FI',date('Y-m-d'))." - ".pinta_FC('forma','FF',date('Y-m-d'))."  
		<input type='button' name='continuar' id='continuar' value=' CONTINUAR ' onclick='procesar()'>
		<input type='hidden' name='Acc' value='pinta_arbol1'>
	</form>
	<iframe name='Tablero_proveedor' id='Tablero_proveedor' style='visibility:visible' width='100%' height='80%'></iframe>
	</body>";
}

function pinta_js_arbol1($idProv=0)
{
	echo "<script language='javascript'>
		function aparece(dato,dato2)
		{
			var Ob=document.getElementById(dato);
			if(Ob.style.visibility=='hidden') {Ob.style.visibility='visible';Ob.style.position='relative';if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/menos_opciones.png';}
			else	{Ob.style.visibility='hidden';Ob.style.position='absolute';if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/mas_opciones.png';recoger(dato);}
		}
		// CONTROL DE NODOS HIJOS PARA OCULTAR O APARECER MASIVAMENTE
		var Hijos=new Array();
		
		function recoger(dato)
		{
			// FUNCION PARA OCULTAR UN NODO Y TODOS SUS HIJOS
			if(Hijos[dato])
			{
				for(var i=0; i<Hijos[dato].length; i++)
				{
					var nuevodato=Hijos[dato][i];
					if(document.getElementById(nuevodato))
					{
						if(document.getElementById(nuevodato).style.visibility=='visible')
						{
							document.getElementById(nuevodato).style.visibility='hidden';
							document.getElementById(nuevodato).style.position='absolute';
							if(Ob=document.getElementById('img_'+nuevodato)) Ob.src='gifs/mas_opciones.png';
							recoger(nuevodato);
						}
					}
				}
			}		
		}
		function expandir(dato)
		{
			// FUNCION PARA EXPANDIR UN NODO Y TODOS SUS HIJOS
			document.getElementById(dato).style.visibility='visible';
			document.getElementById(dato).style.position='relative';
			if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/menos_opciones.png';
			if(Hijos[dato])
			{
				for(var i=0;i<Hijos[dato].length;i++)
				{
					var nuevodato=Hijos[dato][i];
					if(document.getElementById(nuevodato))
					{
						document.getElementById(nuevodato).style.visibility='visible';
						document.getElementById(nuevodato).style.position='relative';
						if(Ob=document.getElementById('img_'+nuevodato)) Ob.src='gifs/menos_opciones.png';
						expandir(nuevodato);
					}
				}
			}
		}
		function adicionar_seleccion(dato) {modal('zproveedor.php?Acc=adicionar_seleccion&id='+dato,0,0,500,500,'adev');}
		function calificar_seleccion(dato) {modal('zproveedor.php?Acc=calificar_seleccion&id='+dato,0,0,500,500,'adev');}
		";
		if($idProv)
			echo "function recargar() {window.open('zproveedor.php?Acc=pinta_arbol1&idProv=$idProv','_self');} ";
		else
			echo "function recargar() {parent.procesar();} ";
		echo "function visualizar_estadistica(dato) {modal('zproveedor.php?Acc=ver_estadistica&id='+dato,0,0,600,950,'adev');}	
	</script>
	<style tyle='text/css'>
		td {font-size:9px;}
		a {cursor:pointer;}
	</style>";
}

class escala_proveedor
{
	var $Tipo=''; // tipo de compra, ya sea (B) bien o (S) servicio
	var $Rango_inicial=0; // rango inicial de la escala
	var $Rango_final=0; // rango final de la escala
	var $Color=''; // color que representa la escala
	var $Concepto=''; // nombre exacto de la escala
	var $Letra=''; // letra para calificar al proveedor, se utiliza la variable proveedor.ultima_calificacion 
	
	function escala_proveedor($E)
	{
		$this->Tipo=$E->tipo_proveedor;
		$this->Rango_inicial=$E->rango_inicial;
		$this->Rango_final=$E->rango_final;
		$this->Color=$E->color_co;
		$this->Concepto=$E->concepto;
		$this->Letra=$E->letra;
	}
}

function pinta_arbol1()
{
	global $FI,$FF,$A_escala,$idProv,$refrescar_opener;
	html();
	$NT_seleccion=tu('provee_seleccion','id');
	$nombre_Filtro='';
	if($idProv) 
	{
		$Filtro=" pr.id=$idProv ";
		$nombre_Filtro='Filtro por proveedor';
	}
	else 
	{
		$Filtro=" ev.fecha_seleccion between '$FI' and '$FF' ";
		$nombre_Filtro='Filtro por fecha';
	}
	if($Proveedores_seleccionados=q("select distinct pr.nombre,pr.id from provee_seleccion ev,proveedor pr 
									where pr.id=ev.proveedor and  $Filtro "))
	{
		$A_criterio=tabla2arreglo('prov_criterio');
		$A_rango=tabla2arreglo('prov_criterio_rango',array('id','rango1'));
		/// creacion de arreglo de escalas usando objetos
		$Escalas=q("select * from prov_escala");$Contador=0;
		while($Esc=mysql_fetch_object($Escalas))	{$Contador++;$A_escala[$Contador]=new escala_proveedor($Esc);} // crea un arreglo de objetos para las escalas
		/// ------------------
		pinta_js_arbol1($idProv);
		echo "<script language='javascript'>
			function modificar_seleccion(dato){modal('marcoindex.php?Acc=mod_reg&id='+dato+'&Num_Tabla=$NT_seleccion',0,0,500,600,'modeval');}
			function visualizar_que_ofrece(dato) {modal('marcoindex.php?Acc=abre_tabla&NTabla=provee_ofrece&VINCULOC=proveedor&VINCULOT='+dato,0,0,500,800,'ofrece');}
			function visualizar_facturas(dato) {modal('marcoindex.php?Acc=abre_tabla&NTabla=factura&VINCULOC=proveedor&VINCULOT='+dato,0,0,500,800,'facturas');}
		</script>";
		echo "<body";
		if($idProv && $refrescar_opener) echo  "onunload='opener.location.reload();' ";
		echo " bgcolor='ddddff'><h4>$nombre_Filtro</h4>";
		//print_r($A_escala);
		echo "<table cellspacing='0' cellpadding='1'><tr>
			<th>Proveedor </th><th></th>
			<th>Selección</th></tr>";
		include('inc/link.php');
		while($Pro=mysql_fetch_object($Proveedores_seleccionados))
		{
			$ultima_seleccion=true;
			$llave_proveedor='proveedor_'.$Pro->id;
			echo "<tr><td valign='top' nowrap='yes' id='td_$llave_proveedor'>
							<img id='img_$llave_proveedor' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('$llave_proveedor');\"> 
							<a onclick=\"aparece('$llave_proveedor','img_$llave_proveedor');\">$Pro->nombre</a>
						</td>
						<td valign='top' nowrap='yes'>
							<a class='info' onclick='adicionar_seleccion($Pro->id);'><img src='gifs/mas.gif'><span>Adicionar Selección</span></a>
							<!-- <a class='info' onclick='visualizar_estadistica($Pro->id);'><img src='gifs/standar/noticias.png'><span>Ver Estadistica</span></a> -->
							<a class='info' onclick='visualizar_que_ofrece($Pro->id);'><img src='gifs/standar/folder1.png'><span>Productos y/o Servicios</span></a>
							<a class='info' onclick='visualizar_facturas($Pro->id);'><img src='gifs/standar/folder2.png'><span>Facturas</span></a>
						</td>
						<td>";
			$Filtro=($idProv?" ":" and fecha_seleccion between '$FI' and '$FF'  ");
			$Selecciones_proveedor=mysql_query("select * from provee_seleccion where proveedor=$Pro->id $Filtro order by fecha_seleccion desc",$LINK);
			echo "<table border cellspacing='0' cellpadding='1' style='".($idProv?"visibility:visible;position:relative;":"visibility:hidden;position:absolute;")."' id='$llave_proveedor'>
							<tr><th>Fecha</th><th>Tipo</th><th>Seleccionado por</th><th>Nota</th><th></th><th>Resultado</th></tr>";
			while($Seleccion=mysql_fetch_object($Selecciones_proveedor))
			{
				$llave_seleccion='seleccion_'.$Pro->id.'_'.$Seleccion->id;
				$tipo_seleccion=$Seleccion->tipo=='B'?'BIEN':'SERVICIO';
				$ctd=obtener_propiedades($Seleccion);
				echo "<tr><td valign='top' nowrap='yes'>
								<img id='img_$llave_seleccion' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('$llave_seleccion');\"> 
								<a onclick=\"aparece('$llave_seleccion','img_$llave_seleccion');\">$Seleccion->fecha_seleccion</a>".
								($NT_seleccion?"<a onclick='modificar_seleccion($Seleccion->id);'>[+]</a>":"").
							"</td>
							<td valign='top' nowrap='yes'>
								<a onclick=\"aparece('$llave_seleccion','img_$llave_seleccion');\">$tipo_seleccion</a>
							</td>
							<td valign='top' nowrap='yes'>
								<a onclick=\"aparece('$llave_seleccion','img_$llave_seleccion');\">$Seleccion->realizada_por</a>
								<script language='javascript'>
									if(!Hijos['$llave_proveedor']) Hijos['$llave_proveedor']=new Array();
									Hijos['$llave_proveedor'][Hijos['$llave_proveedor'].length]='$llave_seleccion';
								</script>
							</td>
							<td valign='top' nowrap='yes' align='right'  bgcolor='".$ctd["color"]."' title='".$ctd["concepto"]."' >
								<a onclick=\"aparece('$llave_seleccion','img_$llave_seleccion');\">$Seleccion->nota</a>";
				if($ultima_seleccion)
				{
					$ultima_seleccion=false;
					echo "<script language='javascript'>
								document.getElementById('td_$llave_proveedor').style.backgroundColor='".$ctd["color"]."';
							</script>";
					mysql_query("update proveedor set calificacion_actual='".$ctd['letra']."' where id=$Pro->id",$LINK);
				}
				echo "</td>							
							<td valign='top'><a class='info' onclick='calificar_seleccion($Seleccion->id);'><img src='gifs/standar/Pencil.png'><span>Calificar Selección</span></a></td>
							<td valign='top'>";
				$Resultado=mysql_query("select * from provee_detalle_seleccion where seleccion=$Seleccion->id",$LINK);
				if(mysql_num_rows($Resultado))
				{
					echo "<table cellspacing='0' cellpadding='1' style='".($idProv?"visibility:visible;position:relative;":"visibility:hidden;position:absolute;")."' id='$llave_seleccion'><tr><th>Criterio</th><th>Opción</th><th>Calificación</th></tr>";
					while($Nota=mysql_fetch_object($Resultado))
					{
						$llave_nota='nota_'.$Pro->id.'_'.$Seleccion->id.'_'.$Nota->id;
						echo "<tr><td nowrap='yes'>".$A_criterio[$Nota->criterio]."</td><td>".$A_rango[$Nota->opcion]."</td>
								<td align='center'>$Nota->calificacion
									<script language='javascript'>
										if(!Hijos['$llave_seleccion']) Hijos['$llave_seleccion']=new Array();
										Hijos['$llave_seleccion'][Hijos['$llave_seleccion'].length]='$llave_nota';
									</script>
								</td></tr>";
					}
					echo "</table>";
				}
				else echo "<b style='background-color:ffdddd;'>No hay resultados</b>";
				echo "</td></tr>";
			}
			echo "</table>";
			echo "</td></tr>";
		}
		//echo "<tr><td></td></tr>";
		echo "</table></body>";
		mysql_close($LINK);
	}
	else 
	{
		if($idProv)
		{
			echo "<script language='javascript'>
				function procesar()
				{window.open('zproveedor.php?Acc=pinta_arbol1&idProv=$idProv&refrescar_opener=$refrescar_opener','_self');}
				
				function abrir_requisicion()
				{window.open('zrequisicion.php?Acc=ver_requisicion&id=$refrescar_opener','_self');}
				
			</script><body bgcolor='ffffff'>Insertando selección nueva..";
			echo "
			<iframe name='Seleccion_nueva' id='Seleccion_nueva' style='visibility:visible' width='100%' height='500' 
			src='zproveedor.php?Acc=adicionar_seleccion&id=$idProv&nuevo=1&refrescar_opener=$refrescar_opener'></iframe>
			</body>";
		}
		else echo "<body bgcolor='fffffff'><b style='color:red'>NO HAY SELECCIONES ENTRE LAS FECHAS $FI Y $FF</b></body>";
	}
}

function adicionar_seleccion()
{
	global $NUSUARIO,$id,$nuevo,$refrescar_opener;
	html('Adicionar Seleccion');
	$tipos=q("select distinct p.tipo from provee_produc_serv p,provee_ofrece o 
					where p.id=o.proveedor");
	echo "<script language='javascript'>
		function validar_adicion()
		{
			with(document.forma)
			{
				if(!alltrim(realizada_por.value)) {alert('Debe ditigar quien realiza la selección');realizada_por.style.backgroundColor='ffffaa';realizada_por.focus();return false;}
				if(!tipo.value) {alert('Debe seleccionar el tipo de selección.');tipo.style.backgroundColor='ffffaa';tipo.focus(); return false;}
				submit();
			}
		}
		</script>
		<body bgcolor='ddddff'><h3>ADICION DE SELECCION</H3>
		<form action='zproveedor.php' target='_self' method='POST' name='forma' id='forma'>
			Seleccione el proveedor: ".menu1("proveedor","select id,nombre from proveedor order by nombre",$id,0,"width:200px")."<br>
			Fecha de Selección: ".pinta_FC('forma','fecha_seleccion',date('Y-m-d'))."<br>
			Tipo de Selección: <select name='tipo'><option value=''></option><option value='B'>BIEN</option><option value='S'>SERVICIO</optcion></select><br>
			Realizada por: <input type='text' name='realizada_por' id='realizada_por' value='$NUSUARIO' size='50' maxlength='50' onkeyup='this.value=this.value.toUpperCase();' onblur='this.value=this.value.toUpperCase();' ><br>
			<input type='button' name='continuar' id='continuar' value=' CONTINUAR ' style='height:40px;width:300px;' onclick='validar_adicion();'>
			<input type='hidden' name='Acc' value='adicionar_seleccion_ok'><input type='hidden' name='nuevo' value='$nuevo'>
			<input type='hidden' name='refrescar_opener' value='$refrescar_opener'>
		</form></body>";
}

function adicionar_seleccion_ok()
{
	global $proveedor,$fecha_seleccion,$realizada_por,$tipo,$nuevo,$refrescar_opener;
	$ne=q("insert into provee_seleccion (proveedor,fecha_seleccion,realizada_por,tipo) values ('$proveedor','$fecha_seleccion','$realizada_por','$tipo')");
	graba_bitacora('provee_seleccion','A',$ne,'Adiciona registro');
	if($nuevo) echo "<body><script language='javascript'>window.open('zproveedor.php?Acc=calificar_seleccion&id=$ne&nuevo=1&refrescar_opener=$refrescar_opener','_self');</script>";
	else	echo "<body><script language='javascript'>window.close();void(null);opener.parent.procesar();</script>";
}

function calificar_seleccion()
{	
	global $id, // id de la seleccion
				$nuevo,$refrescar_opener;
	$Ev=qo("select * from provee_seleccion where id=$id");
	$Pr=qo("select id,nombre from proveedor where id=$Ev->proveedor");
	$Criterios=q("select * from prov_criterio where activo = 1");
	include('inc/link.php');
	$JS_acriterio='';
	while($Cr=mysql_fetch_object($Criterios))
	{
		$idcr=$Cr->id;
		$Opciones=mysql_query("select id,rango1,calificacion1 from prov_criterio_rango where criterio=$idcr",$LINK);
		if(mysql_num_rows($Opciones))
		{
			while($Op=mysql_fetch_object($Opciones))
			{
				$idop=$Op->id;
				$calificacion=$Op->calificacion1;
				$JS_acriterio.="Criterio[$idop]=$calificacion; 
				";
			}
		}
	}
	mysql_close($LINK);
	html('CALIFICACION DE seleccion');
	echo "<script language='javascript'>
		Criterio=new Array();
		$JS_acriterio
		Calificaciones=new Array();
		
		function cambio_opcion(criterio,opcion)
		{
			if(opcion) 
			{
				document.getElementById('rcr_'+criterio).innerHTML=Criterio[opcion];
				Calificaciones[criterio]=opcion;
				calcula_totales();
			}
		}
		function aplicar_calificacion() 
		{
			// cada calificacion se envia concatenada en una cadena comun en el campo cadena.value dentro del formato document.forma
			var indice;
			var C=document.forma.cadena;
			C.value='';
			for(indice in Calificaciones)
			{
				if(Calificaciones[indice])
				{
					C.value+=indice+'|'+Calificaciones[indice]+'|'+Criterio[Calificaciones[indice]]+',';
				}
			}
			if(confirm('Desea guardar estas calificaciones?')) document.forma.submit();
		}
		function calcula_totales()
		{
			var cont_opciones=0;
			var tot_calificacion=0;
			for(indice in Calificaciones)
			{
				if(Calificaciones[indice]) // si hay calificación seleccionada
				{
					cont_opciones++;
					tot_calificacion+=Criterio[Calificaciones[indice]];
				}
			}
			document.getElementById('total_opciones').innerHTML=cont_opciones;
			document.getElementById('total_calificacion').innerHTML=tot_calificacion;
			document.forma.nota.value=tot_calificacion;
		}
		function cerrar()
		{
			window.close();void(null);opener.recargar();
		}
	</script>
	<body bgcolor='ddffff'><script language='javascript'>centrar();</script>
	<h3>CALIFICACION DE SELECCION $Ev->fecha_seleccion POR $Ev->realizada_por</h3>
	<h4>Proveedor: $Pr->nombre</h4>
	<table border cellspacing='0'><tr><th>Criterio</th><th>Opcion</th><th>Calificacion</th></tr>";
	
	include('inc/link.php');
	mysql_data_seek($Criterios,0);
	while($Cr=mysql_fetch_object($Criterios))
	{
		echo "<tr><td>$Cr->nombre</td><td><select name='rango_$Cr->id' onchange='cambio_opcion($Cr->id,this.value);'><option value=''></option>";
		$Rangos=mysql_query("select id,rango1,calificacion1 from prov_criterio_rango where criterio=$Cr->id order by calificacion1",$LINK);
		$Calificado=qom("select opcion,calificacion from provee_detalle_seleccion where seleccion=$id and criterio=$Cr->id",$LINK);
		while($Ra=mysql_fetch_object($Rangos))
		{
			echo "<option value='$Ra->id' ".($Calificado?($Calificado->opcion==$Ra->id?'selected':''):'').">$Ra->rango1</option>";
		}
		echo "</select></td><td align='center' id='rcr_$Cr->id'>";
		if($Calificado)
		{
			echo "$Calificado->calificacion
						<script language='javascript'>Calificaciones[$Cr->id]=$Calificado->opcion;</script>";
		}
		echo "</td></tr>";
	}
	mysql_close($LINK);
	echo "<tr><td align='center' bgcolor='dddddd' style='font-weight:bold;'>Totales</td>
		<td id='total_opciones' align='right' bgcolor='dddddd' style='font-weight:bold;'></td>
		<td id='total_calificacion' align='right' bgcolor='dddddd' style='font-weight:bold;'></td>
		</tr></table>
	<input type='button' name='aplicar' id='aplicar' value=' APLICAR ' onclick='aplicar_calificacion();'>
	<form action='zproveedor.php' target='Oculto_calificacion' method='POST' name='forma' id='forma'>
		<input type='hidden' name='Acc' value='aplicar_calificacion'>
		<input type='hidden' name='id' value='$id'>
		<input type='hidden' name='cadena' value=''>
		<input type='hidden' name='nota' value=''>
		<input type='hidden' name='nuevo' value='$nuevo'>
		<input type='hidden' name='refrescar_opener' value='$refrescar_opener'>
	</form>
	<script language='javascript'>calcula_totales();</script>
	<iframe name='Oculto_calificacion' id='Oculto_calificacion' style='visibility:hidden' width='1' height='1'></iframe></body>";
}

function aplicar_calificacion()
{
	global $id,$cadena,$nota,$nuevo,$refrescar_opener;   // id es la seleccion, cadena de calificaciones
	global $A_escala;
	/* Cada calificación viene separada por una coma (,) está compuesta por dos partes, separadas por un pipe (|) 
		la primera parte es el id del criterio que va a guardar, la segunda parte es el id de la opción seleccionada para la calificación, 
		la tercera parte es el valor de la calificación.
		nota es la nota final de toda la seleccion
	*/
	// primero se parte la cadena por los criterios en un arreglo
	echo "<body>$cadena<br>";
	$Criterios=explode(',',$cadena);
	include('inc/link.php');
	mysql_query("update provee_seleccion set nota=$nota where id=$id",$LINK);
	foreach($Criterios as $criterio)
	{
		if($criterio)
		{
			$partes=explode('|',$criterio);
			$idcriterio=$partes[0];$idcalificacion=$partes[1];$calificacion=$partes[2];
			echo "criterio $idcriterio = $idcalificacion = $calificacion<br>";
			// debe insertar en la tabla de selecciones el resultado
			mysql_query("insert ignore into provee_detalle_seleccion (seleccion,criterio) values ($id,$idcriterio) ",$LINK);
			mysql_query("update provee_detalle_seleccion set opcion=$idcalificacion,calificacion=$calificacion where seleccion=$id and criterio=$idcriterio",$LINK);
			
		}
	}
	mysql_close($LINK);
	$Escalas=q("select * from prov_escala");$Contador=0;
	while($Esc=mysql_fetch_object($Escalas))	{$Contador++;$A_escala[$Contador]=new escala_proveedor($Esc);} // crea un arreglo de objetos para las escalas
	$Seleccion=qo("select * from provee_seleccion where id=$id");
	$ctd=obtener_propiedades($Seleccion);
	q("update proveedor set calificacion_actual='".$ctd['letra']."' where id=$Seleccion->proveedor");
	
	if($refrescar_opener) echo "<script language='javascript'>parent.parent.abrir_requisicion();</script></body>";
	elseif($nuevo) echo "<script language='javascript'>parent.parent.procesar();</script></body>";
	else echo "<script language='javascript'>if(confirm('INFORMACION GUARDADA SATISFACTORIAMENTE, desea cerrar la ventana de seleccion?')) parent.cerrar();</script></body>";
}

function obtener_propiedades($D)
{
	global $A_escala;
	//print_r($D);
	for($i=1;$i<=count($A_escala);$i++)
	{
		//echo "<br>$D->tipo ".$A_escala[$i]->Tipo;
		if($D->tipo==$A_escala[$i]->Tipo && $D->nota>=$A_escala[$i]->Rango_inicial && $D->nota<=$A_escala[$i]->Rango_final)
		{
			return array("color"=>$A_escala[$i]->Color,"descripcion"=>$A_escala[$i]->Concepto,"letra"=>$A_escala[$i]->Letra);
		}
	}
	return array("color"=>"dddddd","descripcion"=>"!! escala no encontrada !!");
}

function ver_estadistica()
{
	global $id; // id del proveedor
	$A_tipo=array("O"=>"OPORTUNIDAD","C"=>"CALIDAD","P"=>"PRECIO");
	html('ESTADISTICA DEL PROVEEDOR');
	echo "<script language='javascript' src='inc/chart/JSClass/FusionCharts.js'></script>";
	$Pro=qo("select id,nombre from proveedor where id=$id");
	echo "<body><h3>ESTADISTICAS $Pro->nombre</h3>";
	$AcumuladosB=q("select c.tipo,e.fecha_seleccion,sum(d.calificacion) as total 
										FROM provee_detalle_seleccion d , provee_seleccion e,prov_criterio c
										WHERE e.proveedor=$id and e.id=d.seleccion and d.criterio=c.id and e.tipo=1
										GROUP BY tipo,fecha_seleccion ORDER BY tipo,fecha_seleccion");
	
	$AcumuladosS=q("select c.tipo,e.fecha_seleccion,sum(d.calificacion) as total 
										FROM provee_detalle_seleccion d , provee_seleccion e,prov_criterio c
										WHERE e.proveedor=$id and e.id=d.seleccion and d.criterio=c.id and e.tipo=2
										GROUP BY tipo,fecha_seleccion ORDER BY tipo,fecha_seleccion");
	
	if($AcumuladosB || $AcumuladosS)
	{
		if($AcumuladosB)
		{
			$xml_oportunidad="<chart caption='OPORTUNIDAD' xAxisName='Fechas' yAxisName='Calificaciones' logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' bgColor='f1f1f1' showToolTip='0' canvasbgcolor='88aa88,DDFFDD' canvasbgalpha='60' canvasbgangle='270' outcnvBaseFontColor='1D8BD1' showValues='1'  showLabels='1' formatNumberScale='0' alternateHGridAlpha='30' alternateHGridColor='FFFFFF' canvasBorderThickness='1' canvasBorderColor='114B78' baseFontColor='1D8BD1' tooltextBorderColor='114B78' tooltextBgColor='E7EFF6' plotGradientColor='DCE6F9' plotFillAngle='90' plotFillColor='1D8BD1' plotfillalpha='80' drawAnchors='0' canvaspadding='20' plotFillRatio='10,90' showPlotBorder='1' plotBorderColor='FFFFFF' plotBorderAlpha='20' divlinecolor='FFFFFF' divlinealpha='60' numberSuffix='p'>";
			$xml_calidad="<chart caption='CALIDAD' xAxisName='Fechas' yAxisName='Calificaciones' logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' bgColor='f1f1f1' showToolTip='0' canvasbgcolor='AA88AA,FFAAFF' canvasbgalpha='60' canvasbgangle='270' outcnvBaseFontColor='1D8BD1' showValues='1'  showLabels='1' formatNumberScale='0' alternateHGridAlpha='30' alternateHGridColor='FFFFFF' canvasBorderThickness='1' canvasBorderColor='114B78' baseFontColor='1D8BD1' tooltextBorderColor='114B78' tooltextBgColor='E7EFF6' plotGradientColor='DCE6F9' plotFillAngle='90' plotFillColor='1D8BD1' plotfillalpha='80' drawAnchors='0' canvaspadding='20' plotFillRatio='10,90' showPlotBorder='1' plotBorderColor='FFFFFF' plotBorderAlpha='20' divlinecolor='FFFFFF' divlinealpha='60' numberSuffix='p'>";
			$xml_precio="<chart caption='PRECIO' xAxisName='Fechas' yAxisName='Calificaciones' logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' bgColor='f1f1f1' showToolTip='0' canvasbgcolor='1D8BD1,FFFFFF' canvasbgalpha='60' canvasbgangle='270' outcnvBaseFontColor='1D8BD1' showValues='1'  showLabels='1' formatNumberScale='0' alternateHGridAlpha='30' alternateHGridColor='FFFFFF' canvasBorderThickness='1' canvasBorderColor='114B78' baseFontColor='1D8BD1' tooltextBorderColor='114B78' tooltextBgColor='E7EFF6' plotGradientColor='DCE679' plotFillAngle='90' plotFillColor='DDDB81' plotfillalpha='80' drawAnchors='0' canvaspadding='20' plotFillRatio='10,90' showPlotBorder='1' plotBorderColor='FFFFFF' plotBorderAlpha='20' divlinecolor='FFFFFF' divlinealpha='60' numberSuffix='p'>";
			$Categorias=array("O"=>"<categories>","C"=>"<categories>","P"=>"<categories>");
			$Series=array("O"=>"<dataset seriesName='calificaciones'>","C"=>"<dataset seriesName='calificaciones'>","P"=>"<dataset seriesName='calificaciones'>");
			while($Ac=mysql_fetch_object($AcumuladosB))
			{
				$Categorias[$Ac->tipo].="<category label='$Ac->fecha_seleccion'/>";
				$Series[$Ac->tipo].="<set value='$Ac->total'/>";
			}
			$Categorias['O'].="</categories>";$Categorias['C'].="</categories>";$Categorias['P'].="</categories>";
			$Series['O'].="</dataset>";$Series['C'].="</dataset>";$Series['P'].="</dataset>";
			$adicionales="<styles><definition><style name='Bevel' type='bevel' distance='4' blurX='2' blurY='2'/><style name='DataValuesFont' type='font' borderColor='1D8BD1' bgColor='1D8BD1' color='153E7E' /><style name='myAnim' type='animation' param='_alpha' start='0' duration='1' /><style name='dummyShadow' type='Shadow' alpha='0' /></definition><application></application></styles>";
			$xml_oportunidad.=$Categorias['O'].$Series['O'].$adicionales."</chart>";
			$xml_calidad.=$Categorias['C'].$Series['C'].$adicionales."</chart>";
			$xml_precio.=$Categorias['P'].$Series['P'].$adicionales."</chart>";
			echo "<H3>BIENES</H3>";	
			echo "<br>OPORTUNIDAD: ".renderChart("inc/chart/Charts/MSArea.swf","",$xml_oportunidad,"bienes_oportunidad",900,300,false,false);
			echo "<br>CALIDAD: ".renderChart("inc/chart/Charts/MSArea.swf","",$xml_calidad,"bienes_calidad",900,300,false,false);
			echo "<br>PRECIO: ".renderChart("inc/chart/Charts/MSArea.swf","",$xml_precio,"bienes_precio",900,300,false,false);
		}
		if($AcumuladosS)
		{
			$xml_oportunidad="<chart caption='OPORTUNIDAD' xAxisName='Fechas' yAxisName='Calificaciones' logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' bgColor='f1f1f1' showToolTip='0' canvasbgcolor='88aa88,DDFFDD' canvasbgalpha='60' canvasbgangle='270' outcnvBaseFontColor='1D8BD1' showValues='1'  showLabels='1' formatNumberScale='0' alternateHGridAlpha='30' alternateHGridColor='FFFFFF' canvasBorderThickness='1' canvasBorderColor='114B78' baseFontColor='1D8BD1' tooltextBorderColor='114B78' tooltextBgColor='E7EFF6' plotGradientColor='DCE6F9' plotFillAngle='90' plotFillColor='1D8BD1' plotfillalpha='80' drawAnchors='0' canvaspadding='20' plotFillRatio='10,90' showPlotBorder='1' plotBorderColor='FFFFFF' plotBorderAlpha='20' divlinecolor='FFFFFF' divlinealpha='60' numberSuffix='p'>";
			$xml_calidad="<chart caption='CALIDAD' xAxisName='Fechas' yAxisName='Calificaciones' logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' bgColor='f1f1f1' showToolTip='0' canvasbgcolor='AA88AA,FFAAFF' canvasbgalpha='60' canvasbgangle='270' outcnvBaseFontColor='1D8BD1' showValues='1'  showLabels='1' formatNumberScale='0' alternateHGridAlpha='30' alternateHGridColor='FFFFFF' canvasBorderThickness='1' canvasBorderColor='114B78' baseFontColor='1D8BD1' tooltextBorderColor='114B78' tooltextBgColor='E7EFF6' plotGradientColor='DCE6F9' plotFillAngle='90' plotFillColor='1D8BD1' plotfillalpha='80' drawAnchors='0' canvaspadding='20' plotFillRatio='10,90' showPlotBorder='1' plotBorderColor='FFFFFF' plotBorderAlpha='20' divlinecolor='FFFFFF' divlinealpha='60' numberSuffix='p'>";
			$xml_precio="<chart caption='PRECIO' xAxisName='Fechas' yAxisName='Calificaciones' logoURL='img/LOGO_AOA_200.png' logoScale='20' logoAlpha='30' bgColor='f1f1f1' showToolTip='0' canvasbgcolor='1D8BD1,FFFFFF' canvasbgalpha='60' canvasbgangle='270' outcnvBaseFontColor='1D8BD1' showValues='1'  showLabels='1' formatNumberScale='0' alternateHGridAlpha='30' alternateHGridColor='FFFFFF' canvasBorderThickness='1' canvasBorderColor='114B78' baseFontColor='1D8BD1' tooltextBorderColor='114B78' tooltextBgColor='E7EFF6' plotGradientColor='DCE679' plotFillAngle='90' plotFillColor='DDDB81' plotfillalpha='80' drawAnchors='0' canvaspadding='20' plotFillRatio='10,90' showPlotBorder='1' plotBorderColor='FFFFFF' plotBorderAlpha='20' divlinecolor='FFFFFF' divlinealpha='60' numberSuffix='p'>";
			$Categorias=array("O"=>"<categories>","C"=>"<categories>","P"=>"<categories>");
			$Series=array("O"=>"<dataset seriesName='calificaciones'>","C"=>"<dataset seriesName='calificaciones'>","P"=>"<dataset seriesName='calificaciones'>");
			while($Ac=mysql_fetch_object($AcumuladosS))
			{
				$Categorias[$Ac->tipo].="<category label='$Ac->fecha_seleccion'/>";
				$Series[$Ac->tipo].="<set value='$Ac->total'/>";
			}
			$Categorias['O'].="</categories>";$Categorias['C'].="</categories>";$Categorias['P'].="</categories>";
			$Series['O'].="</dataset>";$Series['C'].="</dataset>";$Series['P'].="</dataset>";
			$adicionales="<styles><definition><style name='Bevel' type='bevel' distance='4' blurX='2' blurY='2'/><style name='DataValuesFont' type='font' borderColor='1D8BD1' bgColor='1D8BD1' color='153E7E' /><style name='myAnim' type='animation' param='_alpha' start='0' duration='1' /><style name='dummyShadow' type='Shadow' alpha='0' /></definition><application></application></styles>";
			$xml_oportunidad.=$Categorias['O'].$Series['O'].$adicionales."</chart>";
			$xml_calidad.=$Categorias['C'].$Series['C'].$adicionales."</chart>";
			$xml_precio.=$Categorias['P'].$Series['P'].$adicionales."</chart>";
			echo "<H3>SERVICIOS</H3>";	
			echo "<br>OPORTUNIDAD: ".renderChart("inc/chart/Charts/MSArea.swf","",$xml_oportunidad,"servicios_oportunidad",900,300,false,false);
			echo "<br>CALIDAD: ".renderChart("inc/chart/Charts/MSArea.swf","",$xml_calidad,"servicios_calidad",900,300,false,false);
			echo "<br>PRECIO: ".renderChart("inc/chart/Charts/MSArea.swf","",$xml_precio,"servicios_precio",900,300,false,false);
		}
		echo "</body>";
	}
	else
		echo "<b style='color:red'>NO HAY SELECCIONES HISTORICAS DE ESTE PROVEEDOR</b>";
}

function adicion_de_proveedor()
{
	global $NUSUARIO;
	$Ahora=date('Y-m-d');
	html('ADICION DE PROVEEDOR');
	
	if($_GET["id"]!= null)
	{
		echo "edition_mode";
		$proveedor = qo("select * from proveedor where id = ".$_GET["id"]);
	}
	
	$result_criticidad_proveedores=q("select * from nivel_criticidad_proveedor");
	$result_tipos_proveedores=q("select * from tipo_gasto_proveedor");
	
	$criticidad_proveedores = array();
	while ($fila = mysql_fetch_object($result_criticidad_proveedores)) {
			array_push($criticidad_proveedores,$fila);
	}
	
	$tipos_proveedores = array();
	while ($fila = mysql_fetch_object($result_tipos_proveedores)) {
			array_push($tipos_proveedores,$fila);
	}

echo "<script language='javascript'>
			function busqueda_ciudad2(Campo,Contenido)
			{
				var Ventana_ciudad=document.getElementById('Busqueda_Ciudad');
				Ventana_ciudad.style.visibility='visible';Ventana_ciudad.style.left=mouseX;Ventana_ciudad.style.top=mouseY-10;Ventana_ciudad.src='inc/ciudades.html';
				Ciudad_campo=Campo;Ciudad_forma='forma';
			}
			function oculta_busca_ciudad()
			{document.getElementById('Busqueda_Ciudad').style.visibility='hidden';}
			function validar_aplicar()
			{
				document.forma.submit();
			}
			function llenado_de_prueba()
			{
				with(document.forma)
				{
					nombre.value='ARTURO QUINTERO RODRIGUEZ';
					identificacion.value='791869911';
					sexo.value='M';
					representante_legal.value='ARTURO QUINTERO RODRIGUEZ';
					cedula_rep_legal.value='791869911';
					ciudad.value='11001000';_ciudad.value='BOGOTA - BOGOTA D.C.';
					telefono1.value='8647816';
					telefono2.value='7560510';
					telefono3.value='7560512';
					celular.value='3176562730';
					direccion.value='CALLE 2 NUMERO 0-01';
					contacto.value='OTONIEL QUINTERO CASTAÑEDA';
					email.value='administracion@intercolombia.net';
					email_tesoreria_e.value='arturo__quintero@hotmail.com';
					pagina_web.value='http://www.intercolombia.net';
					recomendado_por.value='GABRIEL SANDOVAL PAFAJEAU';
					objeto_social.value='DESARROLLO DE SOFTWARE';
					gestiona_calidad.value='N';
					prov_servicio.checked=true;
					cedula_representante.checked=true;
					rut.checked=true;
					td.value='CC';
				}
			}
		</script>
		<body><script language='javascript'>centrar();</script>
			<H3>ADICION DE PROVEEDORES".($_SESSION['User']==1?"<a onclick='llenado_de_prueba();'>llenado de prueba</a> ":"")."</H3>
			<iframe id='Busqueda_Ciudad' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#eeffee;z-index:200;' height='400px' width='200px' ></iframe>
			<form action='zproveedor.php' target='_self' method='POST' name='forma' enctype='multipart/form-data' id='forma'>
				<table cellspacing=0>
					<tr><td align='right'>Nombre o Razón social:</td><td><input type='text' name='nombre' id='nombre' value='' size='100' maxlength='200' onkeyup='this.value=this.value.toUpperCase();'></td></tr>
					<tr><td align='right'>Tipo de Identificación:</td><td> ".menu1("td","select codigo,nombre from tipo_identificacion",'NIT',1,"width:100px")."
										Nit / CC: <input type='text' name='identificacion' id='identificacion' class='numero' value='' size='15' maxlength='15'  onkeyup=\"verificanumero(event,'identificacion');\">
										Dígito de Verificación: <input type='text' name='dv' id='dv' value='' size='1' maxlength='1'  onkeyup=\"verificanumero(event,'dv');\">
										
										Sexo / tipo: <select name='sexo' style='width:50px'><option value=''></option><option value='E'>EMPRESA</option><option value='M'>MASCULINO</option><option value='F'>FEMENINO</option></select>
									</td></tr>
					<tr><td align='right'>Representante legal:</td><td><input type='text' name='representante_legal' id='representante_legal' value='' size='100' maxlength='100' onkeyup='this.value=this.value.toUpperCase();'
									onfocus=\"if(this.value=='') this.value=document.forma.nombre.value;\"></td></tr>
					<tr><td align='right'>Cédula del Representante Legal</td><td><input type='text' name='cedula_rep_legal' id='cedula_rep_legal' value='' size='15' maxlength='15' class='numero'
									onfocus=\"if(this.value=='') this.value=document.forma.identificacion.value;\"  onkeyup=\"verificanumero(event,'cedula_rep_legal');\"></td></tr>
					<tr><td align='right'>Ciudad:</td><td><input type='text' style='color:#000099;background-color:#FFFFFF;' name='_ciudad' id='_ciudad' size='30' onclick=\"busqueda_ciudad2('ciudad','05001000');\" readonly>
									<input type='hidden' name='ciudad' id='ciudad' value=''><span id='bc_ciudad'></span></td></tr>
					<tr><td align='right'>Telefonos fijos</td><td>
																		1: <input type='text' name='telefono1' id='telefono1' value='' size='10' maxlength='10' class='numero' onkeyup=\"verificanumero(event,'telefono1');\">
																		2: <input type='text' name='telefono2' id='telefono2' value='' size='10' maxlength='10' class='numero' onkeyup=\"verificanumero(event,'telefono2');\">
																		3: <input type='text' name='telefono3' id='telefono3' value='' size='10' maxlength='10' class='numero' onkeyup=\"verificanumero(event,'telefono3');\">
					</td></tr>
					<tr><td align='right'>Celular :</td><td><input type='text' name='celular' id='celular' value='' size='10' maxlength='10' class='numero' onkeyup=\"verificanumero(event,'celular');\"></td></tr>
					<tr><td align='right'>Dirección</td><td><input type='text' name='direccion' id='direccion' value='' size='100' maxlength='200' onkeyup='this.value=this.value.toUpperCase();'></td></tr>
					<tr><td align='right'>Persona de contacto</td><td><input type='text' name='contacto' id='contacto' value='' size='50' maxlength='50' onkeyup='this.value=this.value.toUpperCase();'></td></tr>
					<tr><td align='right'>Correo electronico</td><td><input type='text' name='email' id='email' value='' size='100' maxlength='100' onblur='validaemail(this,true);' onkeyup='this.value=this.value.toLowerCase();'></td></tr>
					<tr><td align='right'>Correo electronico de Tesorería</td><td><input type='text' name='email_tesoreria_e' id='email_tesoreria_e' value='' size='100' maxlength='100' onblur='validaemail(this);' onkeyup='this.value=this.value.toLowerCase();'></td></tr>
					<tr><td align='right'>Página web:</td><td><input type='text' name='pagina_web' id='pagina_web' value='http://www.' size='100' maxlength='100' onkeyup='this.value=this.value.toLowerCase();'></td></tr>
					<tr><td align='right'>Recomendado por:</td><td><input type='text' name='recomendado_por' id='recomendado_por' value='' size='50' maxlength='50' onkeyup='this.value=this.value.toUpperCase();'></td></tr>
					<tr><td align='right'>Objeto Social</td><td><input type='text' name='objeto_social' id='objeto_social' value='' size='100' maxlength='100' onkeyup='this.value=this.value.toUpperCase();'></td></tr>
					<input type='hidden' name='usuario_creador' id='usuario_creador' value='$NUSUARIO'>
				</table>
				<table cellspacing='0'>
					<tr><td align='right'>Posee sistema de Calidad</td><td><select name='gestiona_calidad'><option value=''>Selecciona</option><option value='S'>SI</option><option value='N'>NO</option></select></td></tr>
					
					<tr><td align='right'>Nivel de criticidad del proveedor</td><td><select name='nivel_criticidad'><option value=''>Selecciona</option>".generate_option_string_with_data($criticidad_proveedores,"id","nivel")."</select></td></tr>
					<tr><td align='right'>Tipo de proveedor</td><td><select name='tipo_gasto_proveedor'><option value=''>Selecciona</option>".generate_option_string_with_data($tipos_proveedores,"id","nombre")."</select></td></tr>
					tr><td align='right'>Proveedor empresa </td><td><select name='empresa_proveedor'><option value=''>Selecciona</option><option value='aoa'>AOA Colombia</option><option value='transoriente '>Trans Oriente </option><option value='mutuo'>Mutuo</option></select></td></tr>

					<tr><td align='right'>Este es proveedor de bienes</td><td><input type='checkbox' name='prov_bien' id='prov_bien'></td></tr>
					<tr><td align='right'>Este es proveedor de servicios</td><td><input type='checkbox' name='prov_servicio' id='prov_servicio'></td></tr>
					<tr><td align='right'>Este es proveedor de caja menor</td><td><input type='checkbox' name='prov_caja_menor' id='prov_caja_menor'></td></tr>
				</table>
				<h3>VERIFICACION DE DOCUMENTOS</H3>
				<table cellspacing='0'>
					<tr><td align='right'>Cédula del Representante Legal</td><td><input type='checkbox' name='cedula_representante' id='cedula_representante'></td><td>Observaciones</td><td><textarea name='obs_cedula_repr' cols=100 rows=2></textarea><input type='file' name='cedula_representante_f'></td></tr>
					<tr><td align='right'>Certificados Bancarios</td><td><input type='checkbox' name='certif_bancarias' id='certif_bancarias'></td><td>Observaciones</td><td><textarea name='obs_certif_banc' cols=100 rows=2></textarea><input type='file' name='certif_banco_f'></td></tr>
					<tr><td align='right'>Rut</td><td><input type='checkbox' name='rut' id='rut'></td><td>Observaciones</td><td><textarea name='obs_rut' cols=100 rows=2></textarea><input type='file' name='rut_f'></td></tr>
					<tr><td align='right'>Certificado de Cámara y Comercio</td><td><input type='checkbox' name='certif_camara_comerc' id='certif_camara_comerc'></td><td>Observaciones</td><td><textarea name='obs_cert_cam_comerc' cols=100 rows=2></textarea><input type='file' name='camaraycomercio_f'></td></tr>
					<tr><td align='right'>Formato</td><td><input type='checkbox' name='certif_camara_comerc' id='certif_camara_comerc'></td><td>Observaciones</td><td><textarea name='obs_cert_cam_comerc' cols=100 rows=2></textarea><input type='file' name='formulario_f'></td></tr>
					<tr><td align='right'>Estados Financieros</td><td><input type='checkbox' name='ee_ff' id='ee_ff'></td><td>Observaciones</td><td><textarea name='obs_ee_ff' cols=100 rows=2></textarea><input type='file' name='estados_financieros_f'></td></tr>
					
					<tr><td align='right'>Certificado de afiliación a la ARL</td><td><input type='checkbox' name='ee_arl' id='ee_arl'></td><td>Observaciones</td><td><textarea name='obs_ee_arl' cols=100 rows=2></textarea><input type='file' name='arl_f'></td></tr>
					
					<tr><td align='right'>Certificado de Accidentalidad de los dos últimos años</td><td><input type='checkbox' name='acidente_dos' id='acidente_dos'></td><td>Observaciones</td><td><textarea name='acidente_obs' cols=100 rows=2></textarea><input type='file' name='accidente_f'></td></tr>
				</table><br><br>
				<center><input type='button' name='aplicar' id='aplicar' value=' APLICAR  -> siguiente paso: BIENES Y SERVICIOS QUE OFRECE ' style='font-size:18px;font-weight:bold;height:60pz;width:800px' onclick='validar_aplicar();'></center>
				<input type='hidden' name='Acc' value='adicion_de_proveedor_paso2_2'>
			</form>
			
			<br><br><br><br>
		</body>";
}

function generate_option_string_with_data($dataset,$key,$name)
{
	$text = "";
	//print_r($dataset);
	foreach($dataset as $data)
	{
		//print_r($data);
		$text .= "<option value='".$data->$key."'  >".$data->$name."</option>";	
	}
	
	return $text;
	
}

function adicion_de_proveedor2()
{
	global $NUSUARIO;
	$Ahora=date('Y-m-d');
	html('ADICION DE PROVEEDOR');
	
	$result_criticidad_proveedores=q("select * from nivel_criticidad_proveedor");
	$result_tipos_proveedores=q("select * from tipo_gasto_proveedor");
	
	$criticidad_proveedores = array();
	while ($fila = mysql_fetch_object($result_criticidad_proveedores)) {
			array_push($criticidad_proveedores,$fila);
	}
	
	$tipos_proveedores = array();
	while ($fila = mysql_fetch_object($result_tipos_proveedores)) {
			array_push($tipos_proveedores,$fila);
	}	
	
	echo "<script language='javascript'>
			function busqueda_ciudad2(Campo,Contenido)
			{
				var Ventana_ciudad=document.getElementById('Busqueda_Ciudad');
				Ventana_ciudad.style.visibility='visible';Ventana_ciudad.style.left=mouseX;Ventana_ciudad.style.top=mouseY-10;Ventana_ciudad.src='inc/ciudades.html';
				Ciudad_campo=Campo;Ciudad_forma='forma';
			}
			function oculta_busca_ciudad()
			{document.getElementById('Busqueda_Ciudad').style.visibility='hidden';}
			function validar_aplicar()
			{
				document.forma.submit();
			}
			function llenado_de_prueba()
			{
				with(document.forma)
				{
					nombre.value='ARTURO QUINTERO RODRIGUEZ';
					identificacion.value='791869911';
					sexo.value='M';
					representante_legal.value='ARTURO QUINTERO RODRIGUEZ';
					cedula_rep_legal.value='791869911';
					ciudad.value='11001000';_ciudad.value='BOGOTA - BOGOTA D.C.';
					telefono1.value='8647816';
					telefono2.value='7560510';
					telefono3.value='7560512';
					celular.value='3176562730';
					direccion.value='CALLE 2 NUMERO 0-01';
					contacto.value='OTONIEL QUINTERO CASTAÑEDA';
					email.value='administracion@intercolombia.net';
					email_tesoreria_e.value='arturo__quintero@hotmail.com';
					pagina_web.value='http://www.intercolombia.net';
					recomendado_por.value='GABRIEL SANDOVAL PAFAJEAU';
					objeto_social.value='DESARROLLO DE SOFTWARE';
					gestiona_calidad.value='N';
					prov_servicio.checked=true;
					cedula_representante.checked=true;
					rut.checked=true;
					td.value='CC';
				}
			}
		</script>
		<body><script language='javascript'>centrar();</script>
			<H3>ADICION DE PROVEEDORES ".($_SESSION['User']==1?"<a onclick='llenado_de_prueba();'>llenado de prueba</a> ":"")."</H3>
			<iframe id='Busqueda_Ciudad' style='visibility:hidden;position:absolute;border-style=solid;border-width:2px;background-color:#eeffee;z-index:200;' height='400px' width='200px' ></iframe>
			<form action='zproveedor.php' target='_self' method='POST' name='forma' enctype='multipart/form-data' id='forma'>
				<table cellspacing=0>
					<tr><td align='right'>Nombre o Razón social:</td><td><input type='text' name='nombre' id='nombre' value='' size='100' maxlength='200' onkeyup='this.value=this.value.toUpperCase();'></td></tr>
					<tr><td align='right'>Tipo de Identificación:</td><td> ".menu1("td","select codigo,nombre from tipo_identificacion",'NIT',1,"width:100px")."
										Nit / CC: <input type='text' name='identificacion' id='identificacion' class='numero' value='' size='15' maxlength='15'  onkeyup=\"verificanumero(event,'identificacion');\">
										Dígito de Verificación: <input type='text' name='dv' id='dv' value='' size='1' maxlength='1'  onkeyup=\"verificanumero(event,'dv');\">
										
										Sexo / tipo: <select name='sexo' style='width:50px'><option value=''></option><option value='E'>EMPRESA</option><option value='M'>MASCULINO</option><option value='F'>FEMENINO</option></select>
									</td></tr>
					<tr><td align='right'>Representante legal:</td><td><input type='text' name='representante_legal' id='representante_legal' value='' size='100' maxlength='100' onkeyup='this.value=this.value.toUpperCase();'
									onfocus=\"if(this.value=='') this.value=document.forma.nombre.value;\"></td></tr>
					<tr><td align='right'>Cédula del Representante Legal</td><td><input type='text' name='cedula_rep_legal' id='cedula_rep_legal' value='' size='15' maxlength='15' class='numero'
									onfocus=\"if(this.value=='') this.value=document.forma.identificacion.value;\"  onkeyup=\"verificanumero(event,'cedula_rep_legal');\"></td></tr>
					<tr><td align='right'>Ciudad:</td><td><input type='text' style='color:#000099;background-color:#FFFFFF;' name='_ciudad' id='_ciudad' size='30' onclick=\"busqueda_ciudad2('ciudad','05001000');\" readonly>
									<input type='hidden' name='ciudad' id='ciudad' value=''><span id='bc_ciudad'></span></td></tr>
					<tr><td align='right'>Telefonos fijos</td><td>
																		1: <input type='text' name='telefono1' id='telefono1' value='' size='10' maxlength='10' class='numero' onkeyup=\"verificanumero(event,'telefono1');\">
																		2: <input type='text' name='telefono2' id='telefono2' value='' size='10' maxlength='10' class='numero' onkeyup=\"verificanumero(event,'telefono2');\">
																		3: <input type='text' name='telefono3' id='telefono3' value='' size='10' maxlength='10' class='numero' onkeyup=\"verificanumero(event,'telefono3');\">
					</td></tr>
					<tr><td align='right'>Celular :</td><td><input type='text' name='celular' id='celular' value='' size='10' maxlength='10' class='numero' onkeyup=\"verificanumero(event,'celular');\"></td></tr>
					<tr><td align='right'>Dirección</td><td><input type='text' name='direccion' id='direccion' value='' size='100' maxlength='200' onkeyup='this.value=this.value.toUpperCase();'></td></tr>
					<tr><td align='right'>Persona de contacto</td><td><input type='text' name='contacto' id='contacto' value='' size='50' maxlength='50' onkeyup='this.value=this.value.toUpperCase();'></td></tr>
					<tr><td align='right'>Correo electronico</td><td><input type='text' name='email' id='email' value='' size='100' maxlength='100' onblur='validaemail(this,true);' onkeyup='this.value=this.value.toLowerCase();'></td></tr>
					<tr><td align='right'>Correo electronico de Tesorería</td><td><input type='text' name='email_tesoreria_e' id='email_tesoreria_e' value='' size='100' maxlength='100' onblur='validaemail(this);' onkeyup='this.value=this.value.toLowerCase();'></td></tr>
					<tr><td align='right'>Página web:</td><td><input type='text' name='pagina_web' id='pagina_web' value='http://www.' size='100' maxlength='100' onkeyup='this.value=this.value.toLowerCase();'></td></tr>
					<tr><td align='right'>Recomendado por:</td><td><input type='text' name='recomendado_por' id='recomendado_por' value='' size='50' maxlength='50' onkeyup='this.value=this.value.toUpperCase();'></td></tr>
					<tr><td align='right'>Objeto Social</td><td><input type='text' name='objeto_social' id='objeto_social' value='' size='100' maxlength='100' onkeyup='this.value=this.value.toUpperCase();'></td></tr>
					<input type='hidden' name='usuario_creador' id='usuario_creador' value='$NUSUARIO'>
					
				</table>
				<table cellspacing='0'>
					<tr><td align='right'>Posee sistema de Calidad</td><td><select name='gestiona_calidad'><option value=''>Selecciona</option><option value='S'>SI</option><option value='N'>NO</option></select></td></tr>
					
					<tr><td align='right'>Nivel de criticidad del proveedor</td><td><select name='nivel_criticidad'><option value=''>Selecciona</option>".generate_option_string_with_data($criticidad_proveedores,"id","nivel")."</select></td></tr>
					<tr><td align='right'>Tipo de proveedor</td><td><select name='tipo_gasto_proveedor'><option value=''>Selecciona</option>".generate_option_string_with_data($tipos_proveedores,"id","nombre")."</select></td></tr>
					
					<tr><td align='right'>Este es proveedor de bienes</td><td><input type='checkbox' name='prov_bien' id='prov_bien'></td></tr>
					<tr><td align='right'>Este es proveedor de servicios</td><td><input type='checkbox' name='prov_servicio' id='prov_servicio'></td></tr>
				</table>
				<h3>VERIFICACION DE DOCUMENTOS</H3>
				<table cellspacing='0'>
					<tr><td align='right'>Cédula del Representante Legal</td><td><input type='checkbox' name='cedula_representante' id='cedula_representante'></td><td>Observaciones</td><td><textarea name='obs_cedula_repr' cols=100 rows=2></textarea><input type='file' name='cedula_representante_f'></td></tr>
					<tr><td align='right'>Certificados Bancarios</td><td><input type='checkbox' name='certif_bancarias' id='certif_bancarias'></td><td>Observaciones</td><td><textarea name='obs_certif_banc' cols=100 rows=2></textarea><input type='file' name='certif_banco_f'></td></tr>
					<tr><td align='right'>Rut</td><td><input type='checkbox' name='rut' id='rut'></td><td>Observaciones</td><td><textarea name='obs_rut' cols=100 rows=2></textarea><input type='file' name='rut_f'></td></tr>
					<tr><td align='right'>Certificado de Cámara y Comercio</td><td><input type='checkbox' name='certif_camara_comerc' id='certif_camara_comerc'></td><td>Observaciones</td><td><textarea name='obs_cert_cam_comerc' cols=100 rows=2></textarea><input type='file' name='camaraycomercio_f'></td></tr>
					<tr><td align='right'>Formulario</td><td><input type='checkbox' name='certif_camara_comerc' id='certif_camara_comerc'></td><td>Observaciones</td><td><textarea name='obs_cert_cam_comerc' cols=100 rows=2></textarea><input type='file' name='formulario_f'></td></tr>
					
					<tr><td align='right'>Estados Financieros</td><td><input type='checkbox' name='ee_ff' id='ee_ff'></td><td>Observaciones</td><td><textarea name='obs_ee_ff' cols=100 rows=2></textarea><input type='file' name='estados_financieros_f'></td></tr>
					
					<tr><td align='right'>Certificado de afiliación a la ARL</td><td><input type='checkbox' name='ee_arl' id='ee_arl'></td><td>Observaciones</td><td><textarea name='obs_ee_arl' cols=100 rows=2></textarea><input type='file' name='arl_f'></td></tr>
					
					<tr><td align='right'>Certificado de Accidentalidad de los dos últimos años</td><td><input type='checkbox' name='acidente_dos' id='acidente_dos'></td><td>Observaciones</td><td><textarea name='acidente_obs' cols=100 rows=2></textarea><input type='file' name='accidente_f'></td></tr>
				
				</table><br><br>
				<center><input type='button' name='aplicar' id='aplicar' value=' APLICAR  -> siguiente paso: BIENES Y SERVICIOS QUE OFRECE ' style='font-size:18px;font-weight:bold;height:60pz;width:800px' onclick='validar_aplicar();'></center>
				<input type='hidden' name='Acc' value='adicion_de_proveedor_paso2_2'>
			</form>
			
			<br><br><br><br>
		</body>";
}

function adicion_de_proveedor_paso2_2()
{	
	//print_r($_FILES);

	$sql = "Select max(id) as id from aoacol_administra.proveedor ";
	$result = q($sql);
	$last = mysql_fetch_object($result);
	

	$id = ($last->id)+1;	
	
	$rut_f = "";
	$certif_banco_f = "";
	$formulario_f = "";
	
	foreach($_FILES as $key => $temp)	
	{
		
		$path ='/var/www/html/public_html/Administrativo';
			
		$custompath = 'proveedor/000/'.$id;
		$path  = $path."/".$custompath;
		
		if(!is_dir($path))
		{
			mkdir($path, 0777, true);
		}

		$uploadfile = $path .'/'. basename($_FILES[$key]['name']);

				
		if (move_uploaded_file($_FILES[$key]['tmp_name'], $uploadfile)) {
			
			//echo "File was valid, uploaded";
			
			$camino = $custompath .'/'. basename($_FILES[$key]['name']);
			
			if($key == 'rut_f')
			{
				$rut_f = $camino;
			}
			if($key == 'certif_banco_f')
			{
				$certif_banco_f = $camino;
			}
			if($key == 'formulario_f')
			{
				$formulario_f = $camino;
			}	
			if($key == 'camaraycomercio_f')
			{
				$camaraycomercio_f = $camino;
			}			
			if($key == 'cedula_representante_f')
			{
				$cedula_representante_f = $camino;
			}
			if($key == 'estados_financieros_f')
			{
				$estados_financieros_f = $camino;
			}
			if($key == 'arl_f')
			{
				$arl_f = $camino;
			}
			if($key == 'accidente_f')
			{
				$accidente_f = $camino;
			}
			
			
		}
		else{
			//echo "upload failed";// 
			}		
	}
	
	global $nombre,$identificacion,$dv,$sexo,$representante_legal,$cedula_rep_legal,$ciudad,$telefono1,$telefono2,$telefono3,$celular,$direccion,$contacto,
	$email,$email_tesoreria_e,$pagina_web,$recomendado_por,$objeto_social,$gestiona_calidad,$prov_bien,$prov_servicio,$prov_caja_menor,$usuario_creador,$cedula_representante,
	$obs_cedula_repr,$rut,$obs_rut,$certif_camara_comerc,$obs_cert_cam_comerc,$certif_bancarias,$obs_certif_banc,$ee_ff,$obs_ee_ff,$td;$tipo_gasto_proveedor;$nivel_criticidad;
	$ee_arl;$acidente_dos;$obs_ee_arl;$acidente_obs;
	
	$nivel_criticidad = $_POST['nivel_criticidad'];
	
	$tipo_gasto_proveedor = $_POST['tipo_gasto_proveedor'];
	
	$prov_bien=sino($prov_bien);
	$prov_servicio=sino($prov_servicio);
	$prov_caja_menor=sino($prov_caja_menor);
	$cedula_representante=sino($cedula_representante);
	$rut=sino($rut);
	$certif_camara_comerc=sino($certif_camara_comerc);
	$certif_bancarias=sino($certif_bancarias);
	$ee_ff=sino($ee_ff);
	html('ADICION DE PROVEEDOR');
	$sql = "insert into proveedor (nombre,identificacion,dv,sexo,representante_legal,cedula_rep_legal,ciudad,telefono1,telefono2,telefono3,celular,direccion,contacto,
	email,email_tesoreria_e,pagina_web,recomendado_por,objeto_social,gestiona_calidad,prov_bien,prov_servicio,cedula_representante,obs_cedula_repr,
	rut,obs_rut,certif_camara_comerc,obs_cert_cam_comerc,certif_bancarias,obs_certif_banc,ee_ff,obs_ee_ff,td,tipo,rut_f,certif_banco_f,formulario_f,
	camaraycomercio_f,cedula_representante_f,estados_financieros_f,tipo_gasto_proveedor,nivel_criticidad,prov_caja_menor,usuario_creador,ee_arl,
	acidente_dos,obs_ee_arl,acidente_obs,arl_f,accidente_f) 
	
	values ('$nombre','$identificacion','$dv','$sexo','$representante_legal','$cedula_rep_legal','$ciudad','$telefono1','$telefono2','$telefono3','$celular','$direccion','$contacto',
	'$email','$email_tesoreria_e','$pagina_web','$recomendado_por','$objeto_social','$gestiona_calidad','$prov_bien','$prov_servicio','$cedula_representante',
	'$obs_cedula_repr','$rut','$obs_rut','$certif_camara_comerc','$obs_cert_cam_comerc','$certif_bancarias','$obs_certif_banc','$ee_ff','$obs_ee_ff','$td','P',
	'$rut_f','$certif_banco_f','$formulario_f','$camaraycomercio_f','$cedula_representante_f','$estados_financieros_f','$tipo_gasto_proveedor','$nivel_criticidad',
	'$prov_caja_menor','$usuario_creador','$ee_arl','$acidente_dos','$obs_ee_arl','$acidente_obs','$arl_f','$accidente_f')";
	
	echo $sql;
	if($NP=q($sql))
	{
		graba_bitacora('proveedor','A',$NP,'Adiciona registro');
		
		echo "<body>";
		echo "<b>Información grabada satisfactoriamente.. Pasando a la captura de bienes y servicios.</b>";
		sleep(2);
		echo "<script language='javascript'>window.open('zproveedor.php?Acc=add_bienes_y_servicios&id=$NP','_self');</script></body>";
	}
	else{echo "<body>No se pudo grabar la información del proveedor, intente nuevamente.</body>";}
	
}

function adicion_de_proveedor_paso2()
{
	$sql = "Select max(id) as id from aoacol_administra.proveedor ";
	$result = q($sql);
	$last = mysql_fetch_object($result);
	

	$id = ($last->id)+1;	
	
	$rut_f = "";
	$certif_banco_f = "";
	$formulario_f = "";
	
	foreach($_FILES as $key => $temp)	
	{
		
		$path ='/var/www/html/public_html/Administrativo';	
			
		$custompath = 'proveedor/000/'.$id;
		$path  = $path."/".$custompath;
		
		if(!is_dir($path))
		{
			mkdir($path, 0777, true);
		}

		$uploadfile = $path .'/'. basename($_FILES[$key]['name']);

				
		if (move_uploaded_file($_FILES[$key]['tmp_name'], $uploadfile)) {
			
			//echo "File was valid, uploaded";
			
			$camino = $custompath .'/'. basename($_FILES[$key]['name']);
			
			if($key == 'rut_f')
			{
				$rut_f = $camino;
			}
			if($key == 'certif_banco_f')
			{
				$certif_banco_f = $camino;
			}
			if($key == 'formulario_f')
			{
				$formulario_f = $camino;
			}	
			if($key == 'camaraycomercio_f')
			{
				$camaraycomercio_f = $camino;
			}			
			if($key == 'cedula_representante_f')
			{
				$cedula_representante_f = $camino;
			}
			if($key == 'estados_financieros_f')
			{
				$estados_financieros_f = $camino;
			}
		}
		else{
			//echo "upload failed";// 
			}		
	}
	
	
	
	global $nombre,$identificacion,$dv,$sexo,$representante_legal,$cedula_rep_legal,$ciudad,$telefono1,$telefono2,$telefono3,$celular,$direccion,$contacto,
	$email,$email_tesoreria_e,$pagina_web,$recomendado_por,$objeto_social,$gestiona_calidad,$prov_bien,$prov_servicio,$prov_caja_menor,$usuario_creador,$cedula_representante,
	$obs_cedula_repr,$rut,$obs_rut,$certif_camara_comerc,$obs_cert_cam_comerc,$certif_bancarias,$obs_certif_banc,$ee_ff,$obs_ee_ff,$td;$tipo_gasto_proveedor;$nivel_criticidad;
	
	$nivel_criticidad = $_POST['nivel_criticidad'];
	
	$tipo_gasto_proveedor = $_POST['tipo_gasto_proveedor'];
	
	
	
	$prov_bien=sino($prov_bien);
	$prov_servicio=sino($prov_servicio);
	$prov_caja_menor=sino($prov_caja_menor);
	$cedula_representante=sino($cedula_representante);
	$rut=sino($rut);
	$certif_camara_comerc=sino($certif_camara_comerc);
	$certif_bancarias=sino($certif_bancarias);
	$ee_ff=sino($ee_ff);
	html('ADICION DE PROVEEDOR');
	$sql = "insert into proveedor (nombre,identificacion,dv,sexo,representante_legal,cedula_rep_legal,ciudad,telefono1,telefono2,telefono3,celular,direccion,contacto,
	email,email_tesoreria_e,pagina_web,recomendado_por,objeto_social,gestiona_calidad,prov_bien,prov_servicio,cedula_representante,obs_cedula_repr,
	rut,obs_rut,certif_camara_comerc,obs_cert_cam_comerc,certif_bancarias,obs_certif_banc,ee_ff,obs_ee_ff,td,tipo,rut_f,certif_banco_f,formulario_f,camaraycomercio_f,cedula_representante_f,estados_financieros_f,tipo_gasto_proveedor,nivel_criticidad,prov_caja_menor,usuario_creador) 
	
	values ('$nombre','$identificacion','$dv','$sexo','$representante_legal','$cedula_rep_legal','$ciudad','$telefono1','$telefono2','$telefono3','$celular','$direccion','$contacto',
	'$email','$email_tesoreria_e','$pagina_web','$recomendado_por','$objeto_social','$gestiona_calidad','$prov_bien','$prov_servicio','$cedula_representante',
	'$obs_cedula_repr','$rut','$obs_rut','$certif_camara_comerc','$obs_cert_cam_comerc','$certif_bncarias','$obs_certif_banc','$ee_ff','$obs_ee_ff','$td','P','$rut_f','$certif_banco_f','$formulario_f','$camaraycomercio_f','$cedula_representante_f','$estados_financieros_f','$tipo_gasto_proveedor','$nivel_criticidad','$prov_caja_menor','$usuario_creador')";
	
	if($NP=q($sql))
	{
		echo "<body>";
		echo "<b>Información grabada satisfactoriamente.. Pasando a la captura de bienes y servicios.</b>";
		sleep(2);
		echo "<script language='javascript'>window.open('zproveedor.php?Acc=add_bienes_y_servicios&id=$NP','_self');</script></body>";
	}
	else{echo "<body>No se pudo grabar la información del proveedor, intente nuevamente.</body>";}
}

function add_bienes_y_servicios()
{
	global $id,$refrescar_opener; // id del proveedor , sabe si debe refrescar la ventana por debajo cuando termine
	html('BIENES Y SERVICIOS DEL PROVEEDOR');
	$Prov=qo("select * from proveedor where id=$id");
	echo "<script language='javascript'>
			function adicionar_bs()	{modal('zproveedor.php?Acc=adicionar_un_bs&id=$id','add_bs');}
			function recargar()			{window.open('zproveedor.php?Acc=add_bienes_y_servicios&id=$id','_self');}
			function proceso_seleccion() {window.open('zproveedor.php?Acc=pinta_arbol1&idProv=$id&refrescar_opener=$refrescar_opener','_self');}
		</script>
		<body bgcolor='ddffdd'><h3>BIENES Y SERVICIOS .:. $Prov->nombre</h3>
		<table width='100%' cellspacing='0' border><tr><th>Tipo</th><th>Nombre</th><th>Descripcion</th></tr>";
	$A_bien=array('B'=>'BIEN','S'=>'SERVICIO');
	if($BS=q("select ps.tipo,ps.nombre,p.especificaciones from provee_ofrece p,provee_produc_serv ps where p.proveedor=$id and ps.id=p.producto_servicio"))
	{
		while($bs=mysql_fetch_object($BS))
		{
			echo "<tr><td>".$A_bien[$bs->tipo]."</td><td>$bs->nombre</td><td>$bs->especificaciones</td></tr>";
		}
	}
	echo "<tr><td colspan=3><input type='button' name='btn_adicionar_bs' id='btn_adiconar_bs' value=' ADICIONAR OTRO BIEN O SERVICIO ' onclick='adicionar_bs();' style='height:40px;width:300px;'></td></tr>";
	echo "</table>";
	if(mysql_num_rows($BS))
	{
		echo "<br><input type='button' name='btn_continuar_seleccion' id='btn_continuar_seleccion' value=' Siguiente paso -> PROCESO DE SELECCION DEL PROVEEDOR ' onclick='proceso_seleccion();' style='height:40px;width:600px;'>";
	}
	else
	{
		echo "<script language='javascript'>adicionar_bs();</script>";
	}
	echo "</body>";
}

function adicionar_un_bs()
{
	global $id; // id del proveedor. El objetivo es adicionar un bien o servicio a este proveedor.
	html('ADICION DE UN BIEN O SERVICIO');
	echo "<script language='javascript'>
			function grabar_bs()
			{with(document.forma)
				{if(Number(producto_servicio.value)==0) {alert('Debe seleccionar un bien o servicio.');producto_servicio.style.backgroundColor='ffffaa';return false;}	submit();}
			}
			function solicitar_nuevo_tipo()	{window.open('zrequisicion.php?Acc=solicitar_nuevo_bien_servicio','_self');}
		</script>
		<body><h3>Adicion de un Bien o Servicio</h3><script language='javascript'>centrar(600,400);</script>
		<form action='zproveedor.php' target='_self' method='POST' name='forma' id='forma'>
			Bien o servicio: <select name='producto_servicio' style='width:300px'><option value=''></option>
			<optgroup label='BIENES'>";
	$Bienes=q("select id,nombre from provee_produc_serv where tipo= 1 order by nombre");
	while($B=mysql_fetch_object($Bienes))	echo "<option value='$B->id'>$B->nombre</option>";
	echo "</optgroup><optgroup label='SERVICIOS'>";
	$Servicios=q("select id,nombre from provee_produc_serv where tipo= 2 order by nombre");
	while($S=mysql_fetch_object($Servicios))	echo "<option value='$S->id'>$S->nombre</option>";
	echo "</optgroup></select><br>
			Señor Usuario, si el tipo de requisición no aparece en la lista, puede solicitar su creación<br>
			mediante este link:
			<a class='info' onclick='solicitar_nuevo_tipo();'><img src='img/nuevotipo.png' height='20px'><span style='width:100px'>Solicitar nuevo tipo</span></a>
			<br><br>
			Especificaciones: <br>
			<textarea name='especificaciones' cols=100 rows=4></textarea><br><br>
			<input type='button' name='btn_grabar' id='btn_grabar' value=' GRABAR ' onclick='grabar_bs();'>
			<input type='hidden' name='Acc' value='adicionar_un_bs_ok'><input type='hidden' name='id' value='$id'>
		</form></body>";
}

function adicionar_un_bs_ok()
{
	global $id,$producto_servicio,$especificaciones;
	q("insert into provee_ofrece (proveedor,producto_servicio,especificaciones) values ('$id','$producto_servicio','$especificaciones')");
	echo "<body><script language='javascript'>window.close();void(null);opener.recargar();</script></body>";
}

function realizar_calificacion()
{
	global $refrescar_opener;
	html('REALIZAR CALIFICACION DE PROVEEDOR');
	echo "
	<script language='javascript'>
		function enviar_solicitud()
		{
			with(document.forma)
			{
				if(!id.value) {alert('Debe seleccionar un proveedor');id.style.backgroundColor='ffffaa';return false;}
				submit();
			}
		}
	</script>
	<body bgcolor='ffffdd'><h3>REALIZAR CALIFICACON DE PROVEEDOR</h3><script language='javascript'>centrar(800,500);</script> 
	<form action='zproveedor.php' target='_self' method='POST' name='forma' id='forma'>
		<input type='hidden' name='Acc' value='add_bienes_y_servicios'>
		Seleccione el proveedor que requiere ser calificado para que pueda ser utilizado en las requisiciones:<br>".
		menu1("id","select id,nombre from proveedor where calificacion_actual not in ('M','C') order by nombre",0,1,"width:300px").
	"	<br><br><input type='button' name='continuar' id='continuar' value=' CONTINUAR ' onclick='enviar_solicitud()' style='width:300px;height:40px;'>	
	<input type='hidden' name='refrescar_opener' value='$refrescar_opener'>
	</form>";
	if($refrescar_opener) 
		echo "<br><input type='button' name='cancelar' id='cancelar' value=' CANCELAR '  onclick='history.back();'>";
}

function inactivar_proveedor()
{
	global $id;
	//echo "I am a test ".$id;
	echo " &nbspid :".$id;
	$query = "Select * from aoacol_administra.causales_inactivacion_proveedor";	 
	$resultado = q($query);
	$filas = array();
   	while ($fila = mysql_fetch_object($resultado)) {
		array_push($filas, $fila);
	}	
	header('Content-Type: text/html; charset=utf-8');
	include 'views/proveedores.html';
}

function inactivar_proveedor_ok()
{
		if(isset($_POST['activar']))
		{
			$query = "update aoacol_administra.proveedor SET activo = 1 , causal_inactivacion = null where id = ".$_POST['id']." limit 1";
			//echo $query; 	
			$resultado = q($query);
			//$fila = mysql_fetch_object($resultado);
			//print_r($fila);
			echo "Proveedor con el id ".$_POST['id']." Activado";
		}
		else
		{
			$query = "update aoacol_administra.proveedor SET activo = 0 , causal_inactivacion = ".$_POST['inactivar_id']." where id = ".$_POST['id']." limit 1";
			//echo $query; 	
			$resultado = q($query);
			//$fila = mysql_fetch_object($resultado);
			//print_r($fila);
			echo "Proveedor con el id ".$_POST['id']." Inactivado";			
		}	
	exit;
}

function modificar_documentos()
{	

	global $id;
	header('Content-Type: text/html; charset=utf-8');
	include 'views/proveedores_documentos.html';
}

function modificar_documentos_ok()
{
	$update_query = " ";
	
	foreach($_FILES as $key => $temp)	
	{
		
		$path ='/var/www/html/public_html/Administrativo';	
			
		$custompath = 'proveedor/000/'.$id;
		$path  = $path."/".$custompath;
		
		if(!is_dir($path))
		{
			mkdir($path, 0777, true);
		}

		$uploadfile = $path .'/'. basename($_FILES[$key]['name']);

				
		if (move_uploaded_file($_FILES[$key]['tmp_name'], $uploadfile)) {
			
			//echo "File was valid, uploaded";
			
			$camino = $custompath .'/'. basename($_FILES[$key]['name']);
			
			if($key == 'rut_f')
			{
				$update_query .=  "rut_f = '".$camino."',";
			}
			if($key == 'certif_banco_f')
			{
				$update_query .=  " certif_banco_f = '".$camino."',";
			}
			if($key == 'formulario_f')
			{
				$update_query .=  " formulario_f = '".$camino."',";
			}
			if($key == 'camaraycomercio_f')
			{
				$update_query .=  " camaraycomercio_f = '".$camino."',";
			}	
		}
				
	}
	
	$update_query = substr_replace($update_query, "", -1);
	
	$sql = "UPDATE aoacol_administra.proveedor SET ".$update_query." where id = ".$_POST['id'];
	//echo $sql;
	q($sql);
	echo "Documentos actualizados";
}

function borrar_seleccion()
{
	global $id;
	q("delete from provee_detalle_seleccion where seleccion=$id");
	q("delete from provee_seleccion where id=$id");
	echo "<body><script language='javascript'>alert('Seleccion borrada.');window.close();void(null);opener.parent.location.reload();</script></body>";
}

function interfaz_proveedores()
{	
	$query = "Select * from aoacol_administra.proveedor";	 
	$resultado = q($query);
	$filas = array();
   	while ($fila = mysql_fetch_object($resultado)) {
		array_push($filas, $fila);
	}	
	//print_r($filas);
	//header('Content-Type: text/html; charset=utf-8');
	//include 'views/proveedores.html';
}

function reporte_calificacion_ok()
{
	$proveedor = $_POST['proveedor'];
	$fecha1 = $_POST['fecha1'];
	$fecha2 = $_POST['fecha2'];
	if($fecha1 == null)
	{
		$where = "where req.proveedor = ".$proveedor;
	}
	elseif($proveedor == null)
	{
		$where = "where fecha between '".$fecha1."' and '".$fecha2."' ";
	}
	else{
		$where = "where req.proveedor = ".$proveedor." and fecha between '".$fecha1."' and '".$fecha2."' ";
	}
	
	try {
		$sql = "Select req.*, es.nombre as estado_requisicion, prov.nombre as nom_proveedor 
		from aoacol_administra.requisicion as req 
		inner join estado_requisicion as es on req.estado = es.id 
		inner join aoacol_administra.proveedor as prov on prov.id = req.proveedor ".$where." order by nom_proveedor";
		echo $sql;
		$resultado = q($sql);
		$requisiciones = array();
		while ($fila = mysql_fetch_object($resultado)) {
			array_push($requisiciones, $fila);
		}
		
		if($_POST['excel_generate'] == "generate")
		{
			reporte_calificacion($requisiciones,"excel");
		}
		else{
			reporte_calificacion($requisiciones,"html");
		}		
		
	} catch (Exception $e) {
		reporte_calificacion(null,"html");
	}
}



function reporte_calificacion($requisiciones=null,$view="html")
{
	$query = "Select * from aoacol_administra.proveedor order by nombre asc";	 
	$resultado = q($query);
	$filas = array();
   	while ($fila = mysql_fetch_object($resultado)) {
		array_push($filas, $fila);
	}
	if($view == "html")
	{
		header('Content-Type: text/html; charset=utf-8');
	}
	elseif($view == "excel")
	{
		
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=Reporte Clificacion evaluaciones.xls");  
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);	
	}	
	include 'views/Reporte_Calif_proveedores.html';
}

function detalles_informe_requisicion()
{
	$foid = $_POST['foid'];
	$query = "Select reqd.*,reqt.nombre as tipo_req,reqc.nombre as nombre_clase from aoacol_administra.requisiciond as reqd
	left join aoacol_administra.requisiciont as reqt on	reqd.tipo1 = reqt.id  left join requisicionc as reqc on 
	reqc.id = reqd.clase where reqd.requisicion = $foid ";	
	$resultado = q($query);
	$filas = array();
   	while ($fila = mysql_fetch_object($resultado)) {
		array_push($filas, $fila);
	}
	
	$query = "select * from requisicion where id = $foid ";
	$requisicion = qo($query);
	
	$query = "SELECT criterio.nombre, detalle.calificacion, rango.nombre as opcion,detalle.observaciones FROM aoacol_administra.prov_detalle_evaluacion as detalle
	inner join prov_criterio_eval as criterio on detalle.criterio = criterio.id inner join prov_rangos_eval as rango on detalle.opcion = rango.id
	where requisicion = $foid ";
	
	//echo $query;
	
    $result = q($query);

	$detalle_evaluacion = array();
	while ($fila = mysql_fetch_object($result)) {
		array_push($detalle_evaluacion, $fila);
	}	
	include 'views/subviews/detalles_requisicion.html';
}




?>