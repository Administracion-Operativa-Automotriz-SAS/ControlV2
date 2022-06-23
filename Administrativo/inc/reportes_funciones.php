<?php
#### programa de ejecucion de reportes  ####
function menu_rep()
{
	global $idreporte,$SECCION;	
	$Alto_frames=180;
	global $Disenador;
	html();
	$Rep=qo("select * from aqr_reporte where id=$idreporte");
	echo "<script language='javascript'>
		function fija() // invoca la fijacion de columnas y titulos de columnas, adicionalmente el ancho total de la tabla
		{
			var Ob=document.getElementById('Edicionrep_$idreporte');
			Ob.style.height=document.body.clientHeight-30;
			Ob.style.width=document.body.clientWidth-40;
		}
		function param_basicos()
		{
			activa_edrep();
			window.open('reportes.php?Acc=rep_basicos&idreporte=$idreporte','Edicionrep_$idreporte');
		}
		function portada()
		{
			activa_edrep();
			window.open('reportes.php?Acc=rep_portada&idreporte=$idreporte&_RT=1','Edicionrep_$idreporte');
		}
		function resumen()
		{
			activa_edrep();
			window.open('reportes.php?Acc=rep_resumen&idreporte=$idreporte&_RT=1','Edicionrep_$idreporte');
		}
		function explicacion()
		{
			activa_edrep();
			window.open('reportes.php?Acc=rep_explicacion&idreporte=$idreporte&_RT=1','Edicionrep_$idreporte');
		}
		function grafica()
		{
			activa_edrep();
			window.open('reportes.php?Acc=rep_grafica&idreporte=$idreporte','Edicionrep_$idreporte');
		}
		function activa_edrep()
		{
			var Ob=document.getElementById('Edicionrep_$idreporte');
			if(Ob.style.visibility=='hidden')
			{
				Ob.style.visibility='visible';
			}
			else
			{
				Ob.contentWindow.location='gifs/standar/loading.gif';
				Ob.style.visibility='hidden';
			}
		}
		function inicial()
		{
			activa_edrep();
			window.open('reportes.php?Acc=rep_script_inicial&idreporte=$idreporte','Edicionrep_$idreporte');
		}
		function titulo()
		{
			activa_edrep();
			window.open('reportes.php?Acc=rep_script_titulo&idreporte=$idreporte','Edicionrep_$idreporte');
		}
		function detalle()
		{
			activa_edrep();
			window.open('reportes.php?Acc=rep_script_detalle&idreporte=$idreporte','Edicionrep_$idreporte');
		}
		function resumen2()
		{
			activa_edrep();
			window.open('reportes.php?Acc=rep_script_resumen&idreporte=$idreporte','Edicionrep_$idreporte');
		}
		function condiciones()
		{
			activa_edrep();
			window.open('reportes.php?Acc=rep_script_condiciones&idreporte=$idreporte','Edicionrep_$idreporte');
		}
		function repsql()
		{
			activa_edrep();
			window.open('reportes.php?Acc=rep_script_sql&idreporte=$idreporte','Edicionrep_$idreporte');
		}
		function editar_columna(idCampo)
		{
			activa_edrep();
			window.open('reportes.php?Acc=ad_campo_detalle&idcampo='+idCampo+'&idreporte=$idreporte','Edicionrep_$idreporte');
		}
		function duplicar_columna(idCampo)
		{
			activa_edrep();
			window.open('reportes.php?Acc=duplica_columna&idcampo='+idCampo+'&idreporte=$idreporte','Edicionrep_$idreporte');
		}
		function adicionar_columna()
		{
			activa_edrep();
			window.open('reportes.php?Acc=ad_campo&idreporte=$idreporte','Edicionrep_$idreporte');
		}
		function pinta_columnas()
		{
			var Ob=document.getElementById('r_campos').contentWindow.location.reload();
		}
		function verifica_escape(Evento)
		{
			var keynum;
			var Caracter;
			if(window.event) // IE
				keynum = Evento.keyCode;
			else if(Evento.which) // Netscape/Firefox/Opera
				keynum = Evento.which;
			if( keynum==27)
			{
			   activa_edrep();
			}
		}
	</script>
	<body onload='fija();' onresize='fija();' bgcolor='#ffffff' topmargin=0 leftmargin=0 rightmargin=0 bottonmargin=0
	onkeydown='verifica_escape(event);'>".
	titulo_modulo("<b><font style='font-family:arial'>[$Rep->clase] $Rep->nombre</font></b>").
	"<table border cellspacing=0>
		<tr>
			<form name='ejecutar' id='ejecutar'>
			<td align='center' valign='top' rowspan=2>
				<br /><b>ID=$idreporte</B><br>
				<input type='button' value='Ejecutar\nTodos\nlos\nRegistros' style='height:70;width:80;font-size:12;'
					onclick=\"javascript:modal2('reportes.php?Acc=ejecutar&Impr=si&ID=$idreporte&SECCION=$Rep->nombre',0,200,s_alto()-20,s_ancho()-200,'_blank');\"><br><br />
				<input type='button' value='Ejecutar\nMuestra' style='height:40;width:80;font-size:10;'
					onclick=\"javascript:modal2('reportes.php?Acc=ejecutar&Impr=si&SECCION=$Rep->nombre&ID=$idreporte&Muestra='+document.ejecutar.Muestra.value,0,0,s_alto()-20,s_ancho()-20,'Resultado');\"><br>
				Muestra: <br /><input type='text' name='Muestra' value='50' size='2' maxlength='10'>
			</form><br /><hr>
			<br /><a onclick='param_basicos()' style='cursor:pointer'><b><font color='blue'>PARAMETROS<br>BASICOS</font></b></a><br><br>
			<a onclick='portada()' style='cursor:pointer'><b><font color='blue'>Portada</font></b></a><br><br>
			<a onclick='resumen()' style='cursor:pointer'><b><font color='blue'>Resumen</font></b></a><br><br>
			<a onclick='explicacion()' style='cursor:pointer'><b><font color='blue'>Explicación</font></b></a><br><br>
			<a onclick='grafica()' style='cursor:pointer'><b><font color='blue'>Grafica</font></b></a><br><br>
			</td>
			<td align='center'><b>Tablas</b><br>
			<iframe name='r_tablas' id='r_tablas' frameborder='no' src='reportes.php?Acc=rep_tablas&idreporte=$idreporte'
			height=$Alto_frames width=180 scrolling='auto'></iframe></td>
			<td align='center' rowspan='3' valign='top'>".
			apopup('<b>Campos</b>',"reportes.php?Acc=rep_campos&idreporte=$idreporte&Cerrar=1",800,1000,'Vista ampliada','ampliada')."<br>
			<iframe name='r_campos' id='r_campos' frameborder='no' src='reportes.php?Acc=rep_campos&idreporte=$idreporte' height='".($Alto_frames*3)."' width='700' scrolling='auto'></iframe></td>
			<td align='center' nowrap='yes' valign='top' rowspan='3'>
			<b><u>AVANZADO</u></b><br><br>
			<a onclick='inicial()' style='cursor:pointer'><b><font color='blue'>Inicial</font></b></a><br><br>
			<a onclick='titulo()' style='cursor:pointer'><b><font color='blue'>Titulo</font></b></a><br><br>
			<a onclick='detalle()' style='cursor:pointer'><b><font color='blue'>Detalle</font></b></a><br><br>
			<a onclick='resumen2()' style='cursor:pointer'><b><font color='blue'>Resumen</font></b></a><br><br>
			<a onclick='condiciones()' style='cursor:pointer'><b><font color='blue'>Condiciones</font></b></a><br><br>
			<a onclick='repsql()' style='cursor:pointer'><b><font color='blue'>SQL final</font></b></a><br><br>";
			/*
			apopup("<b>Borrar Filtros</b>","reportes.php?Acc=rep_filtros_borrar&idreporte=$idreporte",500,700,'Borrar Filtros','bfilt')."<br><br />
			".apopup("<b>Duplicar</b>","reportes.php?Acc=rep_duplicar&idreporte=$idreporte&clase=$Rep->clase&nombre=$Rep->nombre",500,700,'Duplicar este informe')."<br><br />
			".apopup("<b>Eliminar</b>","reportes.php?Acc=rep_eliminar&idreporte=$idreporte",500,700,'Eliminar este informe')."<br /><br
			*/
		echo "<a href=\"inc/pdf/mysql.pdf\" target='Mysql'>MySQL</a><br /><br />
			<a href=\"inc/pdf/mapa_base_datos.pdf\" target='Mysql'>Modelo E-R</a><br /><br />
			<a style='cursor:pointer;' onclick=\"modal('reportes.php?Acc=define_funciones_mysql_reportes',0,0,200,500,'df');\">Definir Funciones</a><br />
			<br />";
			if($Disenador)
			{
				echo "<IMG SRC='gifs/ut.gif' HSPACE='5' BORDER=0 alt='Correr sql' title='Correr sql' onclick=\"javascript:V_ancho=screen.availWidth-20;V_alto=screen.availHeight-20;
				modal2('marcoindex.php?Acc=sql',10,10,V_alto,V_ancho,'_blank');\">";
			}
			echo "</td>
		</tr>
		<tr>
			<td align='center'>
				<b>Relaciones</b><br><iframe name='r_relacion' id='r_relacion' frameborder='no' src='reportes.php?Acc=rep_relacion&idreporte=$idreporte' height=$Alto_frames width=200 scrolling='auto'></iframe>
				<hr><b>Orden</b><br><iframe name='r_orden' id='r_orden' frameborder='no' src='reportes.php?Acc=rep_orden&idreporte=$idreporte' height=$Alto_frames width=200 scrolling='auto'></iframe>
			</td>
		</tr>
		</table>
		<iframe id='context_reporte' name='context_reporte' width='50' height='50' frameborder='yes' src='gifs/standar/loading.gif'
   				style='visibility:hidden;position:absolute;border-style:solid;border-width:2px;background-color:#fdfdfd;'></iframe>
   		<iframe id='Edicionrep_$idreporte' name='Edicionrep_$idreporte' height=100' width='100'
				style='visibility:hidden;position:fixed;top:10;left:10;border-style:solid;border-width:2px;background-color:#fdfdfd;
				z-index:119;' border='2' frameborder='yes' src='gifs/standar/loading.gif'></iframe>
		<script language='javascript'>
		if(Browser=='IE')
		{
			DD=document.getElementById('Edicionrep_$idreporte');
			DD.style.position='absolute';
		}
		</script>
		</body>";
}

function rep_filtros_borrar()
{
	global $idreporte;
	q("delete from aqr_reporte_filtro where idreporte='$idreporte'");
	echo "<body onload='window.close();void(null);'></body>";
}

function rep_duplicar()
{
	require('inc/gpos.php');
	html();
	echo "<body onload='centrar(400,500);'>".titulo_modulo("Adicionar informe");
	echo "<form action='reportes.php' method='post' target='_self' name='forma' id='forma'>
		Clasificación del informe: <input type='text' name='clase' size='50' maxlength='50' value='$clase'><br>
		Nombre del informe: <input type='text' name='nombre' size='50' maxlength='50' value='$nombre'>
		<input type='button' value='Grabar' onclick=\"valida_campos('forma','clase,nombre');\">
		<input type='hidden' name='Acc' value='rep_duplica_ok'>
		<input type='hidden' name='idreporte' value='$idreporte'>
	</form>
	";
}

function rep_duplica_ok()
{
	require('inc/gpos.php');
	q("drop table if exists copy_reporte");
	q("create table copy_reporte select * from aqr_reporte where id=$idreporte");


	q("insert into aqr_reporte (clase,nombre,instruccion,pre,versql,construye,titulo,titulo_rt,
		resumen,resumen_rt,fdetalle,borde,ancho,donde,gen_info,usuarios,vancho,valto,gen_infot,
		tit_Add,gen_csv,distintos,explicacion,extras,grafica,grafica_titulo,grafica_subtitulo,grafica_alto,
		grafica_ancho,grafica_tipo) select ('$clase') as clase,('$nombre') as nombre,instruccion,pre,versql,construye,titulo,titulo_rt,
		resumen,resumen_rt,fdetalle,borde,ancho,donde,gen_info,usuarios,vancho,valto,gen_infot,
		tit_Add,gen_csv,distintos,explicacion,extras,grafica,grafica_titulo,grafica_subtitulo,grafica_alto,
		grafica_ancho,grafica_tipo from copy_reporte ",1);
	$id=qo1("Select id from aqr_reporte where strcmp(clase,'$clase')=0 and strcmp(nombre,'$nombre')=0");
	q("insert into aqr_reporte_field (idreporte,orden,nombre,apodo,cabecera,operacion,ver,caja,alinea,coma,condicion,
		valorcondicion,imagen,grafica,lgrafica,operaciont,hipervinculo) select ('$id') as idreporte,orden,nombre,apodo,
		cabecera,operacion,ver,caja,alinea,coma,condicion,valorcondicion,imagen,grafica,lgrafica,operaciont,hipervinculo
		from aqr_reporte_field where idreporte=$idreporte",1);
	q("insert into aqr_reporte_filtro (idreporte,apodo,condicion,valorcondicion,orden,norden) select ('$id') as idreporte,
		apodo,condicion,valorcondicion,orden,norden from aqr_reporte_filtro where idreporte='$idreporte'",1);
	q("insert into aqr_reporte_order (idreporte,orden,nombre,agrupado,tipo,cabecera,pie,color,total) select ('$id') as idreporte,
		orden,nombre,agrupado,tipo,cabecera,pie,color,total from aqr_reporte_order where idreporte='$idreporte'",1);
	q("insert into aqr_reporte_relacion (idreporte,alias1,alias2) select ('$id') as idreporte,alias1,alias2 from
		aqr_reporte_relacion where idreporte='$idreporte'",1);
	q("insert into aqr_reporte_table (idreporte,nombre,apodo) select ('$id') as idreporte,nombre,apodo from
		aqr_reporte_table where idreporte='$idreporte'",1);

	echo "<body onload='javascript:window.close();void(null);opener.close();'>";
}

function rep_eliminar()
{
	global $idreporte;
	$Ninforme=qo1("select nombre from aqr_reporte where id=$idreporte");
	html();
	echo "<body onload='centrar(400,200);'>".titulo_modulo("Borrar el informe <b>$Ninforme</b> ?");
	echo "<a href='reportes.php?Acc=rep_eliminar_ok&idr=$idreporte' target='_self' >Borrar el informe</a></body>";
}

function rep_eliminar_ok()
{
	global $idr;
	q("delete from aqr_reporte where id=$idr");
	q("delete from aqr_reporte_field where idreporte=$idr");
	q("delete from aqr_reporte_filtro where idreporte=$idr");
	q("delete from aqr_reporte_order where idreporte=$idr");
	q("delete from aqr_reporte_relacion where idreporte=$idr");
	q("delete from aqr_reporte_table where idreporte=$idr");
	echo "<body onload='javascript:window.close();void(null);opener.opener.location.reload();opener.close();'>";
}

function rep_campos_tablas()
{
	global $idreporte;
	html();

	$Switch=true;
	if($Tablas=q("select * from aqr_reporte_table where idreporte=$idreporte order by nombre"))
	{
		while($T=mysql_fetch_object($Tablas))
		{
			if($Switch)
			{
				echo "<body bgcolor='#eeeeee' topmargin=0 leftmargin=0 rightmargin=0 bottonmargin=0
				onload=\"window.open('reportes.php?Acc=rep_campos_campos&tabla=$T->id&idreporte=$idreporte','c_campos');\">
				<table width='100%' cellspacing=0><tr><th colspan=2>Tablas de este informe</th></tr>";
				$Switch=false;
			}
			echo "<tr onclick=\"window.open('reportes.php?Acc=rep_campos_campos&tabla=$T->id&idreporte=$idreporte','c_campos');\"
				style='cursor:pointer;'><td >$T->nombre</td><td>$T->apodo</td></tr>";
		}
	}
	echo "</table></body>";
}

function rep_campos_campos()
{
	global $idreporte,$tabla;
	html();
	echo "<body bgcolor='#eeeeee' topmargin=0 leftmargin=0 rightmargin=0 bottonmargin=0>";
	$Ntabla=qo1("select nombre from aqr_reporte_table where id='$tabla'");
	$Tabla=$Ntabla.'_t';
	if(haytabla($Tabla))
	{
		$Campos=q("select campo,descripcion,traex from $Tabla order by campo");
		echo "<table><tr><th colspan=2>Campos de $Ntabla</th></tr>";
		while ($C=mysql_fetch_object($Campos))
		{
			echo "<tr><td>$C->campo</td><td>$C->descripcion</td></tr>";
		}
		echo "</table></body>";
	}
	else
	{
		echo "<body>No se encuentra la información</body>";
	}
}

function define_funciones_mysql_reportes()
{
	# # PER(fecha) Retorna los cuatro digitos del año y el numero del semestre a partir de la fecha dada
	q("drop function if exists quitatildes");
	q("create function quitatildes (Dato varchar(250)) returns varchar(250) no sql
		begin
		SET Dato=replace(Dato,'á','a');
		SET Dato=replace(Dato,'é','e');
		SET Dato=replace(Dato,'í','i');
		SET Dato=replace(Dato,'ó','o');
		SET Dato=replace(Dato,'ú','u');
		SET Dato=replace(Dato,'ü','u');
		SET Dato=replace(Dato,'ñ','n');
		SET Dato=replace(Dato,'Á','A');
		SET Dato=replace(Dato,'É','E');
		SET Dato=replace(Dato,'Í','I');
		SET Dato=replace(Dato,'Ó','O');
		SET Dato=replace(Dato,'Ú','U');
		SET Dato=replace(Dato,'Ü','U');
		SET Dato=replace(Dato,'Ñ','N');
		return Dato;
		end", 1);
		if(file_exists('zfunciones.mysql.php')) include('zfunciones.mysql.php');
		html();

	echo "<body onunload=\"alert('Funciones Definidas: quitatildes().');\">" . titulo_modulo("Definición de funciones MySQL") . "<br />
		Si no hay errores puede cerrar esta ventana, las funciones fueron creadas correctamente
		<input type='button' value='Cerrar esta Ventana' onclick='window.close();void(null);'></body>";
}

include('inc/reportes_scripts.php');
include('inc/reportes_tablas.php');
include('inc/reportes_relaciones.php');
include('inc/reportes_basicos.php');
include('inc/reportes_orden.php');
include('inc/reportes_campos.php');
include('inc/reportes_graficos.php');
include('inc/reportes_ejecutar.php');

?>
