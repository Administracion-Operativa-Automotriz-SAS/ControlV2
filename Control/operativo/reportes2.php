<?php
include_once('inc/funciones_.php');
sesion();
rep_verificacion_tablas();
include('inc/reportes_funciones.php');
include('inc/menu_inicio.php');
$Inicia_Rompimientos=true;
$Plano_csv='';
if(!empty($Acc) && function_exists($Acc))
{
	eval($Acc.'();');
	die();
}
####################   PRESENTACION DEL MENU DE REPORTES #####################
if($Seguridad = qo("select * from usuario_tab where tabla='reportes2.php' and usuario='".$_SESSION['User']."'"))
{
	$Tit_mod = ($Seguridad->dicono_f?"<img src='$Seguridad->dicono_f' border=0 height=30 valign='top'>&nbsp;&nbsp;&nbsp;":"")."<b>$Seguridad->descripcion</b>";
	html();
	echo "<script language='javascript'>
		function redimensiona()
		{
			var PP=document.getElementById('temas');
			PP.height=document.body.clientHeight-100;
			var PP=document.getElementById('Opciones');
			PP.height=document.body.clientHeight-100;
		}
	</script>";
	if($Seguridad->destino=='destino')
	{  $Body1=" topmargin='10' leftmargin='50' ";}
	else {$Body1=" topmargin='0' leftmargin='20'  ";}
	echo "<body bgcolor='#AACCAA'  $Body1 onload='redimensiona();' onresize='redimensiona()'><br>" . titulo_modulo("$Tit_mod", 0);
	echo "<table border cellspacing=0 cellpadding=0 width='97%'><tr>
	<td style='height:100%;width:40%'>
	<iframe name='temas' id='temas' frameborder='no' src='reportes2.php?Acc=menu_temas' height='100%' width='100%' scrolling='auto'></iframe></td>
	<td>
	<iframe name='Opciones' id='Opciones' frameborder='no' src='reportes2.php?Acc=opciones_informes' height='100%' width='100%' scrolling='auto'></iframe></td>
	</td>
	</tr></table>";
}

function menu_temas()
{
	$Seguridad = qo("select * from usuario_tab where tabla='reportes2.php' and usuario='".$_SESSION['User']."'");
	if($_SESSION['User'] == 1)
	{
		$Cond = '';
		$Cond1 = '';
	}
	else
	{
		$Cond = "where find_in_set('".$_SESSION['User']."',usuarios)";
		$Cond1 = " and find_in_set('".$_SESSION['User']."',usuarios)";
	}
	html();
	echo "
	<style type='text/css'>
	<!--
		td.tdrep {
		font-family: Arial, Verdana, Helvetica;
		font-size:8pt; color: #000000;
		font-weight: normal;
		font-style: normal;
		text-decoration: none;
		cursor:pointer;
		}
		td.tdrep:hover {
		background-color:#045936;font-size:10pt; color: #FFFF76;font-weight: bold;
		}
	-->
	</style>
	<script language='javascript'>
		function mod_rep(Id)
		{
			if(Id) modal('reportes.php?Acc=menu_rep&idreporte='+Id,0,0,750,1150);
		}
		function run_rep(Id)
		{
			if(Id) modal2('reporte.php?ID='+Id);
		}
		function opciones_informes(Tema)
		{
			window.open('reportes2.php?Acc=opciones_informes&Tema='+Tema,'Opciones');
		}
	</script>
	<body bgcolor='#ffffff' leftmargin=1 topmargin=1 rightmargin=1 bottommargin=1>
	<h3><b>TEMAS</B></H3>";
	if($Temas = q("select distinct clase from aqr_reporte $Cond order by clase"))
	{
		echo "<table width='100%' border=0 cellspacing=1 cellpadding=1 bgcolor='#eeeeee'>" ;
		while($T=mysql_fetch_object($Temas))
		{
			echo "<tr ><td class='tdrep' onclick=\"opciones_informes('$T->clase');\"  bgcolor='#ffffff'>$T->clase</td></tr>";
		}
		echo "</table>";
		if($_SESSION['Disenador'])
		{
			echo "<br>Reportes por ID:<br>".menu1('ID',"select id,concat('[',id,'] ',nombre) from aqr_reporte $Cond order by id",0,1,'font-size:9;',
				" onchange=' ".($Seguridad->modifica?'mod':'run')."_rep(this.value);' ")."</body>";
		}
	}
}

function opciones_informes()
{
	global $Tema;
	html();
	echo "<body bgcolor='#ffffff' leftmargin=1 topmargin=1 rightmargin=1 bottommargin=1>
	<h3><b>INFORMES ".($Tema?" - <span style='background-color:#005500;'><font color='#FFFF76'>&nbsp;$Tema&nbsp;</font></span>":"")."</B></H3>";
	if($Tema)
		echo "<iframe name='PintaOpciones' id='PintaOpciones' frameborder='no' border=0
				src='reportes2.php?Acc=pinta_opciones_informes&Tema=$Tema' height='88%' width='100%' scrolling='auto'></iframe>";
	else
		echo "Seleccione un tema de la lista izquierda.";
}


function pinta_opciones_informes()
{
	global $Tema;
	html();
	echo "<style type='text/css'>
	<!--
		td.tdrep {
		font-family: Arial, Verdana, Helvetica;
		font-size:8pt; color: #006600;
		font-weight: normal;
		font-style: normal;
		text-decoration: none;
		cursor:pointer;
		}
		td.tdrep:hover {
			background-color:#455F00;font-size:10pt; color: #00FFD2;font-weight: bold;
			}
		img.imgrep { width:14; height: 14;}
		img.imgrep:hover { width:20; height: 20;}
	-->
	</style>
	<script language='javascript'>

		function run_rep(Id,Seccion)
		{
			modal2('reporte.php?ID='+Id+'&SECCION='+Seccion,0,0,700,900,'_blank');
		}

		function mod_rep(Id,Seccion)
		{
			modal('reportes.php?Acc=menu_rep&idreporte='+Id+'&SECCION='+Seccion,0,0,750,1150,'_blank');
		}

		function dupl_rep(Id,Tema,Nombre)
		{
			modal('reportes.php?Acc=rep_duplicar&idreporte='+Id+'&clase='+Tema+'&nombre='+Nombre,0,0,500,700,'duplicar');
		}

		function del_rep(Id)
		{
			if(confirm('Desea borrar el informe ?'))
				modal('reportes.php?Acc=rep_eliminar&idreporte='+Id,0,0,500,700,'elimina');
		}

		function exp_rep(Id)
		{
			if(confirm('Desea exportar el informe?'))
				modal('reportes2.php?Acc=exportar_reporte&idreporte='+Id,0,0,500,700,'exporta');
		}
	</script>
	<body bgcolor='#ffffff' leftmargin=1 topmargin=1 rightmargin=1 bottommargin=1>";
	$Seguridad = qo("select * from usuario_tab where tabla='reportes2.php' and usuario='".$_SESSION['User']."'");
	if($_SESSION['User']==1) $Cond=''; else $Cond=" and find_in_set('".$_SESSION['User']."',usuarios)";
	if($Reportes = q("select * from aqr_reporte where clase='$Tema' $Cond order by nombre"))
	{
		echo "<table width='100%' border=0 cellspacing=1 cellpadding=1 bgcolor='#dfdfdf'>";
		if($Seguridad->adiciona)
		{
		echo "<tr>
		<td colspan=3 bgcolor='#ffffff'>
		&nbsp;<img class='imgrep' src='gifs/standar/nuevo.png' border=0
					onclick=\"modal('reportes.php?Acc=adreporte',0,0,300,400,'adicionar');\"
					>&nbsp;
			</tr>";
		}
		while($R=mysql_fetch_object($Reportes))
		{
			echo "<tr ><td class='tdrep'  bgcolor='#ffffff'
							onclick=\"run_rep($R->id,'$Rep->nombre');\">$R->nombre</td>
							<td width='10' align='center' bgcolor='#ffffff'>".
							($R->explicacion?"<a class='rinfo'><img src='gifs/standar/Warning.png'><span>$R->explicacion</span></a>":"&nbsp;").
							"</td><td width='20' align='right' bgcolor='#ffffff'><font style='font-size:8.5'>$R->id</font></td>";
			if($Seguridad->modifica+$Seguridad->adiciona+$Seguridad->borra>0)
			{
				echo "<td width='100'  bgcolor='#ffffff'>";
				if($Seguridad->modifica)
				{
					echo "&nbsp;<a class='rinfo' style='cursor:pointer;' onclick=\"mod_rep($R->id,'$R->nombre');\">
					<img class='imgrep' src='gifs/standar/dsn_estructura.png' border=0><span>Configurar el informe</span></a>&nbsp;";
				}
				if($Seguridad->adiciona)
				{
					echo "&nbsp;<a class='rinfo' style='cursor:pointer;' onclick=\"dupl_rep($R->id,'$Tema','$R->nombre');\">
					<img class='imgrep' src='gifs/duplicar.png' border=0><span>Crear un informe a partir de este</span></a>&nbsp;
					<a class='rinfo' style='cursor:pointer;' onclick='exp_rep($R->id);'>
					<img class='imgrep' src='gifs/standar/exportar.png' border=0><span>Exportar este informe a un script para ser importado en otra base de datos</span></a>&nbsp;";
				}
				if($Seguridad->borra)
				{
					echo "&nbsp;<a class='rinfo' style='cursor:pointer;' onclick='del_rep($R->id);'>
					<img class='imgrep' src='gifs/canasta.gif' border=0><span>Borrar este informe</span></a>&nbsp;";
				}
				echo "</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
	else
		echo "No hay informes configurados para el tema $Tema";
}



function rep_verificacion_tablas()
{
	q("create table if not exists aqr_reporte (id int auto_increment not null primary key,
			clase varchar(50) not null default 'Reportes',
			nombre varchar(50) not null default 'Nombre del reporte',
			instruccion text not null,
			pre text not null,
			versql tinyint(1) not null default 1,
			construye tinyint(1) not null default 1,
			titulo text not null,
			titulo_rt longtext not null,
			resumen text not null,
			resumen_rt longtext not null,
			fdetalle text not null,
			borde tinyint(1) default 1,
			ancho text not null,
			donde text not null,
			gen_info tinyint(1) not null default 0,
			usuarios tinytext not null,
			vancho smallint(4) unsigned not null default 800,
			valto smallint(4) unsigned not null default 600,
			gen_infot tinyint(1) not null default 0,
			tit_add text not null,
			gen_csv tinyint(1) not null default 0,
			distintos tinyint(1) not null default 0,
			explicacion longtext not null,
			extras text not null,
			grafica tinyint(1) default 0 not null,
			grafica_titulo varchar(100) not null,
			grafica_subtitulo varchar(100) not null,
			grafica_alto smallint(4) default 500 not null,
			grafica_ancho smallint(4) default 600 not null,
			grafica_tipo varchar(10) default 'PIE' not null,
			incluir_id tinyint(1) default 0 not null,
			etiqueta_id tinyint(1) default 0 not null,
			grafica_script text not null)");
	q("create table if not exists aqr_reporte_table (id int auto_increment not null primary key,
			idreporte int(10) not null default 0,
			nombre varchar(50) not null,
			apodo char(5) not null)");
	q("create table if not exists aqr_reporte_field (id int auto_increment not null primary key,
			idreporte int(10) not null default 0,
			orden smallint(4) not null default 0,
			nombre tinytext not null,
			apodo varchar(50) not null,
			cabecera varchar(50) not null,
			operacion varchar(8) not null,
			operaciont varchar(8) not null,
			ver tinyint(1) unsigned not null default 0,
			caja tinyint(1) unsigned not null default 0,
			alinea char(1) not null default 'C',
			coma tinyint(1) not null default 0,
			condicion varchar(30) not null,
			valorcondicion tinytext not null,
			imagen tinyint(1) not null default 0,
			grafica tinyint(1) not null default 0,
			lgrafica tinyint(1) not null default 0,
			hipervinculo text not null,
			script text not null)");
	q("create table if not exists aqr_reporte_order (id int auto_increment not null primary key,
			idreporte int(10) not null default 0,
			orden smallint(4) not null default 0,
			nombre tinytext not null,
			agrupado tinyint(1) unsigned not null default 0,
			tipo varchar(4) not null default 'ASC',
			cabecera text not null,
			pie text not null,
			color varchar(7) default '#aaaaaa' not null,
			total tinyint(1) default 0 not null)");
	q("create table if not exists aqr_reporte_filtro (id int auto_increment not null primary key,
			idreporte int(10) not null default 0,
			apodo varchar(50) not null,
			condicion varchar(30) not null,
			valorcondicion tinytext not null,
			orden varchar(4) not null,
			norden smallint(4) default 0)");
	q("create table if not exists aqr_reporte_relacion (id int auto_increment not null primary key,
			idreporte int(10) not null default 0,
			alias1 varchar(100) not null,
			alias2 varchar(100) not null)");
	if(haytabla('control_reporte'))
	{
		if(!q("select id from aqr_reporte"))
		{
			q("insert into aqr_reporte select id,clase,nombre,instruccion,pre,versql,construye,titulo,('') as titulo_rt,resumen,('') as resumen_rt,
				fdetalle,borde,ancho,donde,gen_info,usuarios,vancho,valto,gen_infot,tit_add,gen_csv,(0) as distinto,('') as explicacion,('') as extras,
				(0) as grafica, ('') as grafica_titulo, ('') as grafica_subtitulo, (500) as grafica_alto, (600) as grafica_ancho,('') as grafica_tipo,
				(0) as incluir_id,(0) as etiqueta_id,('') as grafica_script from control_reporte ", 1);
			q("insert into aqr_reporte_table select id,idreporte,nombre,nombre as apodo from control_reporte_table", 1);
			q("insert ignore into aqr_reporte_field select id,idreporte,orden,nombre,('') as apodo, cabecera,operacion,('') as operaciont,ver,caja,alinea,coma,
				('') as condicion, ('') as valorcondicion, (0) as imagen, (0) as grafica, (0) as lgrafica,('') as hipervinculo,('') as script from control_reporte_field", 1);
			q("update aqr_reporte_field set apodo=concat('C',id)");
			q("insert into aqr_reporte_order select id,idreporte,orden,nombre,agrupado,tipo,cabecera,pie,('#aaaaaa') as color,(0) as total from control_reporte_order", 1);
		}
	}
	if(!haycampo('grafica_script', 'aqr_reporte')) q("alter table aqr_reporte add column grafica_script text not null");
	if(!haycampo('propiedades_tr', 'aqr_reporte')) q("alter table aqr_reporte add column propiedades_tr text not null");
	if(!haycampo('filtro_rapido', 'aqr_reporte_field')) q("alter table aqr_reporte_field add column filtro_rapido tinyint(1) unsigned not null default 0");
	if(!haycampo('propiedades_td', 'aqr_reporte_field')) q("alter table aqr_reporte_field add column propiedades_td text not null");
	if(!haycampo('lgrafica','aqr_reporte_order')) q("alter table aqr_reporte_order add column lgrafica tinyint(1) unsigned not null default 0");
	if(!haycampo('csv_contitulos','aqr_reporte')) q("alter table aqr_reporte add column csv_contitulos tinyint(1) unsigned not null default 0");
	if(!haycampo('csv_nombre','aqr_reporte')) q("alter table aqr_reporte add column csv_nombre varchar(50) not null default 'plano.csv'");
	if(!haycampo('separador_csv','aqr_reporte')) q("alter table aqr_reporte add column separador_csv varchar(5) not null default ';'");
	if(!haycampo('salida_exel','aqr_reporte')) q("alter table aqr_reporte add column salida_exel tinyint(1) not null default 0");
	if(!haycampo('salida_exel_nombre','aqr_reporte')) q("alter table aqr_reporte add column salida_exel_nombre varchar(50) not null default 'informe.xls'");
	if(!haycampo('comad', 'aqr_reporte_field')) q("alter table aqr_reporte_field add column comad tinyint(2) default 0");
}


function exportar_reporte()
{
	global $idreporte;
	$reporte=qo("select * from aqr_reporte where id=$idreporte");
	$campos=q("select * from aqr_reporte_field where idreporte=$idreporte");
	$orden=q("select * from aqr_reporte_order where idreporte=$idreporte");
	$relaciones=q("select * from aqr_reporte_relacion where idreporte=$idreporte");
	$tablas=q("select * from aqr_reporte_table where idreporte=$idreporte");
	$A=fopen('planos/exportareporte.txt','w');
	$Enter="\r\n";
	fwrite($A,"<?php$Enter");
	fwrite($A,"include('inc/funciones_.php');$Enter");
	fwrite($A,"\$idr=q(\"insert into aqr_reporte (clase,nombre,instruccion,pre,versql,construye,titulo,titulo_rt,resumen,resumen_rt,fdetalle,borde,ancho,donde,gen_info,usuarios,vancho,valto,gen_infot,tit_add,gen_csv,distintos,explicacion,extras,grafica,grafica_titulo,grafica_subtitulo,grafica_alto,grafica_ancho,grafica_tipo,incluir_id,etiqueta_id,grafica_script,propiedades_tr,csv_contitulos,csv_nombre,separador_csv,salida_exel,salida_exel_nombre) $Enter");
	fwrite($A," values ('$reporte->clase','$reporte->nombre',\\\"$reporte->instruccion\\\",\\\"".exc($reporte->pre)."\\\",\\\"$reporte->versql\\\",\\\"$reporte->construye\\\",\\\"".exc($reporte->titulo)."\\\",\\\"".exc($reporte->titulo_rt)."\\\",\\\"".exc($reporte->resumen)."\\\",\\\"".exc($reporte->resumen_rt)."\\\",\\\"".exc($reporte->fdetalle)."\\\",\\\"".exc($reporte->borde)."\\\",\\\"".exc($reporte->ancho)."\\\",\\\"".exc($reporte->donde)."\\\",\\\"$reporte->gen_info\\\",\\\"$reporte->usuarios\\\",\\\"$reporte->vancho\\\",\\\"$reporte->valto\\\",\\\"$reporte->gen_infot\\\",\\\"".exc($reporte->tit_add)."\\\",\\\"$reporte->gen_csv\\\",\\\"$reporte->distintos\\\",\\\"".exc($reporte->explicacion)."\\\",\\\"$reporte->extras\\\",\\\"$reporte->grafica\\\",\\\"$reporte->grafica_titulo\\\",\\\"$reporte->grafica_subtitulo\\\",\\\"$reporte->grafica_alto\\\",\\\"$reporte->grafica_ancho\\\",\\\"$reporte->grafica_tipo\\\",\\\"$reporte->incluir_id\\\",\\\"$reporte->etiqueta_id\\\",\\\"".exc($reporte->grafica_script)."\\\",\\\"".exc($reporte->propiedades_tr)."\\\",\\\"$reporte->csv_contitulos\\\",\\\"$reporte->csv_nombre\\\",\\\"$reporte->separador_csv\\\",\\\"$reporte->salida_exel\\\",\\\"$reporte->salida_exel_nombre\\\")\");$Enter");
	while($T=mysql_fetch_object($tablas))
	{
		fwrite($A,"q(\"insert into aqr_reporte_table (idreporte,nombre,apodo) values ('\$idr','$T->nombre','$T->apodo')\");$Enter");
	}
	while($C=mysql_fetch_object($campos))
	{
		fwrite($A,"q(\"insert into aqr_reporte_field (idreporte,orden,nombre,apodo,cabecera,operacion,operaciont,ver,caja,alinea,coma,condicion,valorcondicion,imagen,grafica,lgrafica,hipervinculo,script,filtro_rapido,bgcolor,propiedades_td,comad) $Enter ");
		fwrite($A," values ('\$idr','$C->orden',\\\"".exc($C->nombre)."\\\",'$C->apodo','$C->cabecera','$C->operacion','$C->operaciont','$C->ver','$C->caja','$C->alinea','$C->coma','$C->condicion',\\\"".exc($C->valorcondicion)."\\\",'$C->imagen','$C->grafica','$C->lgrafica',\\\"".exc($C->hipervinculo)."\\\",\\\"".exc($C->script)."\\\",'$C->filtro_rapido','$C->bgcolor',\\\"".exc($C->propiedades_td)."\\\",'$C->comad')\");$Enter");
	}
	if($Relaciones)
	{
		while($R=mysql_fetch_object($relaciones))
		{
			fwrite($A,"q(\"insert into aqr_reporte_relacion (idreporte,alias1,alias2) values ('\$idr','$R->alias1','$R->alias2')\");$Enter");
		}
	}
	if($Orden)
	{
		while($O=mysql_fetch_object($orden))
		{
			fwrite($A,"q(\"insert into aqr_reporte_order (idreporte,orden,nombre,agrupado,tipo,cabecera,pie,color,total,lgrafica) ");
			fwrite($A," values ('\$idr','$O->orden','$O->nombre','$O->agrupado','$O->tipo',\\\"".exc($O->cabecera)."\\\",\\\"".exc($O->pie)."\\\",'$O->color','$O->total','$O->lgrafica') \");$Enter");
		}
	}

	fwrite($A,"$Enter?>");
	fclose($A);
	echo "<script language='javascript'>
		function carga()
		{
			window.open('marcoindex.php?Acc=bajar_archivo&Archivo=planos/exportareporte.txt&Salida=nrep_$idreporte.php','o1');
		}
		</script>
		<body onload='carga()'>
		<iframe name='o1' style='visibility:hidden' width=1 height=1></iframe></body>";
}


function exc($dato)
{
	$dato=str_replace('"',chr(92).chr(92).chr(92).'"',$dato);
	$dato=str_replace(chr(92).chr(92).chr(92).chr(92),chr(92).chr(92).chr(92).chr(92).chr(92).chr(92).chr(92),$dato);
	$dato=str_replace('$',chr(92).'$',$dato);
	return $dato;
}















?>