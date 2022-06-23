<?php
$app='x.cambia_ingreso.php';
include('inc/funciones_.php');

html();
echo "<body><h3>Cambio de seguridad</h3>";

echo "<br>Creando nueva tabla de perfiles de seguridad SISTEMA_PERFIL";
q("drop table if exists sistema_perfil");
q("create table sistema_perfil 
		id int not null auto_increment primary key,
		nombre varchar(100) not null,
		design tinyint(1) not null default 0,
		cambia_clave tinyint(1) not null default 0,
		add_tabla varchar(100) not null,
		script_entrada text,
		control_parse tinyint(1) not null default 0,
		control_acceso_app tinyint(1) not null default 0
	");

	echo "<br>Creando nueva tabla de Usuarios SISTEMA_INGRESO";
	q("drop table if exists sistema_ingreso");
	q("ceate table sistema_ingreso
		id int not null auto_increment primary key,
		usuario char(100) not null,
		nombre varchar(100) not null,
		email varchar(150) not null,
		clave varchar(250) not null,
		identificacion bigint(15) not null default 0,
		email2 varchar(150) not null,
		activo tinyint(1) not null default 0
		")

?>