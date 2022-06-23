<?php
include_once('inc/funciones_.php');
$BDC='aoacol_aoacars';
html('DEFINICION DE FUNCIONES MYSQL');
echo "<body>";
echo "<h3>T_ubica: redefinición de la función que trae placa, fecha inicial, fecha final y el nombre del estado </h3>";
	q("drop function if exists T_ubica");
	q("create function T_ubica (Id_ int(10)) returns varchar(100) reads sql data
		begin
		declare Resultado_ varchar(100) ;
		select concat(veh.placa,' ',ub.fecha_inicial,' ',ub.fecha_final,' [',es.nombre,']') into Resultado_ from $BDC.ubicacion ub,$BDC.estado_vehiculo es ,$BDC.vehiculo veh 
			where ub.estado=es.id and veh.id=ub.vehiculo and ub.id=Id_;
		return Resultado_;
 		end", 1);
 	q("drop function if exists T_ubica2");
	q("create function T_ubica2 (Id_ int(10)) returns varchar(100) reads sql data
		begin
		declare Resultado_ varchar(100) ;
		select date_format(ub.fecha_inicial,'%Y-%m') into Resultado_ from $BDC.ubicacion ub where ub.id=Id_;
		return Resultado_;
 		end", 1);
 	q("drop function if exists ndiasemana");
	q("create function ndiasemana (Fec_ date) returns varchar(20)  no sql
		begin
		declare Ds_ tinyint(1);
		declare Resultado_ varchar(20);
		select date_format(Fec_,'%w') into Ds_;
		if Ds_=0 then set Resultado_= 'Domingo'; end if;
		if Ds_=1 then set Resultado_= 'Lunes'; end if;
		if Ds_=2 then set Resultado_= 'Martes'; end if;
		if Ds_=3 then set Resultado_= 'Miercoles'; end if;
		if Ds_=4 then set Resultado_= 'Jueves'; end if;
		if Ds_=5 then set Resultado_= 'Viernes'; end if;
		if Ds_=6 then set Resultado_= 'Sabado'; end if;
		return Resultado_;
 		end", 1);
	
	echo "<br /><br />Fin de la definición de funciones.";
	echo "<br /><br /><input type='button' value='Cerrar esta ventana' onclick='window.close();void(null);'></body>";
?>
