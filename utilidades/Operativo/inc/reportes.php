<?php
# ###   GENERADOR DE INFORMES Y REPORTES NUEVO ####
if($Seguridad = qo("select * from usuario_tab where tabla='generador_de_reportes' and usuario='".$_SESSION['User']."'"))
{
	rep_tablas();
	$Tit_mod = ($Seguridad->dicono_f?"<img src='$Seguridad->dicono_f' border=0 height=30 valign='top'>&nbsp;&nbsp;&nbsp;":"")."<b>$Seguridad->descripcion</b>";

	html();
	echo "<head><script type='text/javascript' language='JavaScript' src='inc/js/stmenu.js'></script></head>";
	echo "<body topmargin=10 leftmargin=50 onclick='cerrar_menu_inicio();'><br>" . titulo_modulo("<center>$Tit_mod</center>", 0);

	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Buscar por número de reporte: <input type='text' name='ID' size=3 maxlength=5 onblur=\"";
	if ($Seguridad->modifica)
		echo "modal('reportes.php?Acc=menu_rep&idreporte='+this.value,0,0,750,1150,'_blank');";
	else
		echo "modal2('reporte.php?ID='+this.value,0,0,s_alto(),s_ancho(),'_blank');";
	echo "\"> <img src='gifs/standar/seguir.png' border=0>";
	if($Seguridad->adiciona == 1) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".apopup("<img src='gifs/standar/nuevo.png' border=0> <b>Adicionar un nuevo reporte</b>", "reportes.php?Acc=adreporte", 200, 400, 'Adicionar', 'dialogo');
	rep_menu();
	include('inc/firma.php');
}
else
	echo "No se encuentra el perfil";

function rep_menu()
{
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
	if($Privilegios = qo("select * from usuario_tab where tabla='generador_de_reportes' and usuario=".$_SESSION['User']))
	{
		echo "
			<script type='text/javascript' language='JavaScript1.2'>
			stm_bm(['menu2716',800,'','inc/js/blank.gif',0,'20','80',0,0,0,50,500,1,0,0,'','',0,0,1,1,'default','hand',''],this);
			stm_bp('p0',[1,4,0,0,8,7,7,7,100,'',-2,'',-2,85,1,3,'#CCCCCC','transparent','',0,0,0,'#999999']);
			";
		if($Reportes = q("select distinct clase from aqr_reporte $Cond order by clase"))
		{
			$Primera_fase = true;
			while($Rep = mysql_fetch_object($Reportes))
			{
				$NCapa = str_replace(' ', '_' , $Rep->clase);
				if($Primera_fase)
				{
					$Primera_fase=false;
					echo "
					stm_ai('p0i0',[0,'$Rep->clase','','',-1,-1,0,'#','_self','','','','inc/js/tt2.gif',5,4,1,'inc/js/arrow_white.gif','inc/js/arrow_gray.gif',7,7,0,0,0,'#AAAACF',0,'#BBBBFF',0,'','',3,3,1,1,'#000000','#666666','#FFFFFF','#000000','bold 8pt Verdana','bold 9pt Verdana',0,0]);
					stm_bp('p1',[1,2,0,0,4,5,7,0,100,'',-2,'',-2,85,0,0,'#7F7F7F','transparent','',3,0,0,'#333333']);
					";
					if($Reporte = q("select * from aqr_reporte where clase='$Rep->clase' $Cond1 order by nombre"))
					{
						while($R = mysql_fetch_object($Reporte))
						{
							echo "stm_aix('p1i0','p0i0',[0,'$R->nombre ($R->id)','','',-1,-1,0,\"";
							if ($Privilegios->modifica)
								echo "javascript:modal('reportes.php?Acc=menu_rep&idreporte=$R->id&SECCION=$R->nombre',0,0,750,1150,'_blank');";
							else
								echo "javascript:modal2('reporte.php?ID=$R->id&SECCION=$Rep->nombre',0,0,s_alto(),s_ancho(),'_blank');";
							echo "\",'_self','','','','inc/js/tt2.gif',5,4,1,'','',7,7,0,0,1,'#bbffbb',0,'#A7CEA7',0,'','',3,3,1,1,'#666666','#666666','#000000']);";
						}
					}

				}
				else
				{
					echo "
					stm_aix('p0i1','p0i0',[0,'$Rep->clase','','',-1,-1,0,'#','_self','','','','inc/js/tt2.gif',5,4,1,'inc/js/arrow_white.gif','inc/js/arrow_gray.gif',7,7,0,0,0,'#AAAACF',0,'#BBBBFF']);
					stm_bpx('p2','p1',[]);
					";

					if($Reporte = q("select * from aqr_reporte where clase='$Rep->clase' $Cond1 order by nombre"))
					{
						while($R = mysql_fetch_object($Reporte))
						{

							echo "stm_aix('p2i0','p1i0',[0,'$R->nombre ($R->id)','','',-1,-1,0,\"";
							if ($Privilegios->modifica)
								echo "javascript:modal('reportes.php?Acc=menu_rep&idreporte=$R->id&SECCION=$R->nombre',0,0,750,1150,'_blank');";
							else
								echo "javascript:modal2('reporte.php?ID=$R->id&SECCION=$R->nombre',0,0,s_alto(),s_ancho(),'_blank');";
							echo "\",'_self','','','','inc/js/tt2.gif',5,4,1,'','',7,7,0,0,1]);";
						}
					}
				}
				echo "
				stm_ep();
				";
			}
		}
		echo "
		stm_ep();
		stm_em();
		</script>";
	}
	else
		echo "<h3 align='center'><font color='red'><b>No tiene acceso a este modulo</b></font></h3>";
}


function rep_tablas()
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
#	if(haytabla('control_reporte'))
#	{
#		if(!q("select id from aqr_reporte"))
#		{
#			q("insert into aqr_reporte select id,clase,nombre,instruccion,pre,versql,construye,titulo,('') as titulo_rt,resumen,('') as resumen_rt,
#				fdetalle,borde,ancho,donde,gen_info,usuarios,vancho,valto,gen_infot,tit_add,gen_csv,(0) as distinto,('') as explicacion,('') as extras,
#				(0) as grafica, ('') as grafica_titulo, ('') as grafica_subtitulo, (500) as grafica_alto, (600) as grafica_ancho,('') as grafica_tipo,
#				(0) as incluir_id,(0) as etiqueta_id,('') as grafica_script from control_reporte ", 1);
#			q("insert into aqr_reporte_table select id,idreporte,nombre,nombre as apodo from control_reporte_table", 1);
#			q("insert ignore into aqr_reporte_field select id,idreporte,orden,nombre,('') as apodo, cabecera,operacion,('') as operaciont,ver,caja,alinea,coma,
#				('') as condicion, ('') as valorcondicion, (0) as imagen, (0) as grafica, (0) as lgrafica,('') as hipervinculo,('') as script from control_reporte_field", 1);
#			q("update aqr_reporte_field set apodo=concat('C',id)");
#			q("insert into aqr_reporte_order select id,idreporte,orden,nombre,agrupado,tipo,cabecera,pie,('#aaaaaa') as color,(0) as total from control_reporte_order", 1);
#		}
#	}
#	if(!haycampo('grafica_script', 'aqr_reporte')) q("alter table aqr_reporte add column grafica_script text not null");
#	if(!haycampo('propiedades_tr', 'aqr_reporte')) q("alter table aqr_reporte add column propiedades_tr text not null");
#	if(!haycampo('filtro_rapido', 'aqr_reporte_field')) q("alter table aqr_reporte_field add column filtro_rapido tinyint(1) unsigned not null default 0");
	if(!haycampo('comad', 'aqr_reporte_field')) q("alter table aqr_reporte_field add column comad tinyint(2) default 0");
#	if(!haycampo('lgrafica','aqr_reporte_order')) q("alter table aqr_reporte_order add column lgrafica tinyint(1) unsigned not null default 0");
#	if(!haycampo('csv_contitulos','aqr_reporte')) q("alter table aqr_reporte add column csv_contitulos tinyint(1) unsigned not null default 0");
#	if(!haycampo('csv_nombre','aqr_reporte')) q("alter table aqr_reporte add column csv_nombre varchar(50) not null default 'plano.csv'");
#	if(!haycampo('separador_csv','aqr_reporte')) q("alter table aqr_reporte add column separador_csv varchar(5) not null default ';'");
#	if(!haycampo('salida_exel','aqr_reporte')) q("alter table aqr_reporte add column salida_exel tinyint(1) not null default 0");
#	if(!haycampo('salida_exel_nombre','aqr_reporte')) q("alter table aqr_reporte add column salida_exel_nombre varchar(50) not null default 'informe.xls'");
}

?>
