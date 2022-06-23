<?php
include_once('inc/funciones_.php');
html('DEFINICION DE FUNCIONES MYSQL');
echo "<body>";
echo "<h3>T_ubica: redefinición de la función que trae placa, fecha inicial, fecha final y el nombre del estado </h3>";
	q("drop function if exists T_ubica");
	q("create function T_ubica (Id_ int(10)) returns varchar(100) reads sql data
		begin
		declare Resultado_ varchar(100) ;
		select concat(veh.placa,' ',ub.fecha_inicial,' ',ub.fecha_final,' [',es.nombre,']') into Resultado_ from ubicacion ub,estado_vehiculo es ,vehiculo veh where ub.estado=es.id and veh.id=ub.vehiculo and ub.id=Id_;
		return Resultado_;
 		end", 1);
echo "<h3>T_ubica2: trae el periodo de la fecha inicial de la ubicacion </h3>";
q("drop function if exists T_ubica2");
	q("create function T_ubica2 (Id_ int(10)) returns varchar(100) reads sql data
		begin
		declare Resultado_ varchar(100) ;
		select date_format(ub.fecha_inicial,'%Y-%m') into Resultado_ from ubicacion ub where ub.id=Id_;
		return Resultado_;
 		end", 1);
echo "<h3>ndiasemana: trae el nombre del dia de la semana en español </h3>";
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
echo "<h3>kilometraje: trae el ultimo kilometraje de un vehiculo</h3>";
	q("drop function if exists kilometraje");
	q("create function kilometraje (Vh_ int(10)) returns int(10) reads sql data
			begin
			declare Resultado_ int(10) default 0;
			select if(odometro_final,odometro_final,odometro_inicial) into Resultado_ from ubicacion where vehiculo=Vh_ and odometro_inicial>0 order by odometro_final desc,fecha_final desc limit 1;
			return Resultado_;
			end",1 );
echo "<h3>tfranq: trae el nombre de la franquicia de acuerdo a un id de un recibo de caja </h3>";
	q("drop function if exists tfranqrc");
	q("create function tfranqrc(Rc_ int(10)) returns varchar(50) reads sql data
			begin
			declare Resultado_ varchar(50) default '';
			select ft.nombre into Resultado_ from recibo_caja rc, sin_autor sa, franquisia_tarjeta ft where ft.id=sa.franquicia and sa.id=rc.autorizacion and rc.id=Rc_;
			return Resultado_;
			end",1);
echo "<h3>dserv: Dias de servicio efectivo entre dos fechas </h3>";
	q("drop function if exists dserv");
	q("create function dserv(_Fecha1 date,_Fecha2 date,_Fecha date) returns int(10) no sql
		begin
			Declare _Primerdia date;
			Declare _Ultimodia date;
			Declare _Dias int(10) default 0;
			set _Primerdia=date_format(_Fecha,'%Y-%m-01');
			set _Ultimodia=last_day(_Fecha);
			if _Fecha1<_Primerdia and _Fecha2<_Primerdia then set _Dias=0;
			elseif _Fecha1>_Ultimodia and _Fecha2>_Ultimodia then set _Dias=0;
			elseif _Fecha1<_Primerdia then set _Dias=day(_Fecha2);
			elseif _Fecha2>_Ultimodia then set _Dias=datediff(_Ultimodia,_Fecha1)+1;
			else set _Dias=datediff(_Fecha2,_Fecha1);
			end if;
			return _Dias;
		end",1);
echo "<h3>hdevol: trae la hora de devolucion para los estados en servicio </h3>";
	q("drop function if exists hdevol");
	q("create function hdevol(Idvehiculo_ int(10), Fecha_ date) returns varchar(20) reads sql data
		begin
			declare Placa_ char(10) default '';
			declare Resultado_ varchar(20) default '';
			select placa into Placa_ from vehiculo where id=Idvehiculo_;
			select  hora_devol into Resultado_ from cita_servicio where placa=Placa_ and fec_devolucion=Fecha_ and estado='C' limit 1;
			return Resultado_;
		end",1);
echo "<h3>ultima_ubicacion: trae la ultima oficina de ubicación de un vehículo </h3>";
	q("drop function if exists ultima_ubicacion");
	q("create function ultima_ubicacion(Vehiculo_ int(10)) returns int(10) reads sql data
		begin
			declare Oficina_ int(10) default 0;
			select oficina into Oficina_ from ubicacion where vehiculo=Vehiculo_ order by id desc limit 1;
			return Oficina_;
		end",1);
echo "<h3>ultima_ubicacionid: trae la ultima id de ubicación de un vehículo </h3>";
	q("drop function if exists ultima_ubicacionid");
	q("create function ultima_ubicacionid(Vehiculo_ int(10)) returns int(10) reads sql data
		begin
			declare IdUbicacion_ int(10) default 0;
			select id into IdUbicacion_ from ubicacion where vehiculo=Vehiculo_ order by id desc limit 1;
			return IdUbicacion_;
		end",1);
echo "<h3>T_proveedor: trae el nombre de un proveedor de la base de datos administrativa </h3>";
q("drop function if exists T_proveedor");
q("create function T_proveedor(ID_ int(10)) returns varchar(200) reads sql data
		begin
			declare Resultado_ varchar(200);
			select nombre into Resultado_ from aoacol_administra.proveedor where id=ID_;
			return Resultado_;
		end",1);
echo "<h3>T_factura_proveedor: trae el consecutivo de una factura de proveedor de la base de datos administrativa </h3>";
q("drop function if exists T_factura_proveedor");
q("create function T_factura_proveedor(ID_ int(10)) returns varchar(200) reads sql data
		begin
			declare Resultado_ varchar(200);
			select concat(numero,' ',fecha_emision) into Resultado_ from aoacol_administra.factura where id=ID_;
			return Resultado_;
		end",1);
echo "<h3>T_ultimo_mantenimiento: trae el ultimo mantenimiento del vehiculo de acuerdo al tipo de mantenimiento</h3>";
q("drop function if exists T_ultimo_mantenimiento");
q("create function T_ultimo_mantenimiento(Placa_ char(10),Novedad_ char(3)) returns varchar(10) reads sql data
		begin
			declare Resultado_ varchar(10);
			set Resultado_='';
			if Novedad_='SOA' or Novedad_='RTM' then
				select fecha into Resultado_ from hv_vehiculo where placa=Placa_ and novedad=Novedad_ order by id desc limit 1;
			end if;
			if Novedad_='MNT' then
				select kilometraje into Resultado_ from hv_vehiculo where placa=Placa_ and novedad=Novedad_ order by id desc limit 1;
			end if;
			return Resultado_;
		end",1);
echo "<h3>T_veh_ub: trae el vehiculo correspondiente a un siniestro</h3>";
q("drop function if exists T_veh_ub");
q("create function T_veh_ub(Ubicacion_ int(10)) returns varchar(10) reads sql data
		begin
			declare Resultado_ varchar(10);
			set Resultado_='';
			select placa into Resultado_ from vehiculo,ubicacion where ubicacion.vehiculo=vehiculo.id and ubicacion.id=Ubicacion_;
			return Resultado_;
		end",1);
		
echo "<h3>ultimo_estado: trae el codigo del ultimo estado del vehiculo</h3>";
q("drop function if exists ultimo_estado");
q("create function ultimo_estado(Vehiculo_ int(10)) returns int(10) reads sql data
		begin
			declare Estado_ int(10) default 0;
			select estado into Estado_ from ubicacion where vehiculo=Vehiculo_ order by fecha_final desc, id desc limit 1;
			return Estado_;
		end",1);

echo "<h3>vcaja_menor: trae el valor de una caja menor asociada a un balance de estado</h3>";
q("drop function if exists vcaja_menor");
q("create function vcaja_menor(Ubicacion_ int(10)) returns int(10) reads sql data
		begin
			declare Acumulado_ int(10) default 0;
			select sum(valor) into Acumulado_ from aoacol_administra.caja_menord where ubicacion=Ubicacion_ ;
			return Acumulado_;
		end",1);

echo "<h3>foto_recepcion: trae la foto capturada en la recepcion</h3>";
q("drop function if exists foto_recepcion");
q("create function foto_recepcion(Siniestro_ int(10)) returns varchar(200) reads sql data
		begin
			declare Resultado_ varchar(200);
			select foto_f into Resultado_ from aoacol_administra.ingreso_recepcion where siniestro=Siniestro_  limit 1;
			return Resultado_;
		end",1);
		
echo "<h3>t_rcp: trae numero de recibo de caja provisional</h3>";
q("drop function if exists t_rcp");
q("create function t_rcp(Siniestro_ int(10)) returns varchar(200) reads sql data
		begin
			declare Resultado_ varchar(200);
			select concat(o.sigla,' ',r.consecutivo) into Resultado_ from oficina o, recibo_caja_prov r where r.oficina=o.id and r.siniestro=Siniestro_  limit 1;
			return Resultado_;
		end",1);
	
echo "<h3>t_rc_id: trae id del numero de recibo de caja</h3>";
q("drop function if exists t_rc_id");
q("create function t_rc_id(Autorizacion_ int(10)) returns int(10) reads sql data
		begin
			declare Resultado_ int(10);
			select r.id into Resultado_ from recibo_caja r where r.autorizacion=Autorizacion_ and r.garantia=1  limit 1;
			return Resultado_;
		end",1);

echo "<h3>t_rc: trae el numero de recibo de caja</h3>";
q("drop function if exists t_rc");
q("create function t_rc(Autorizacion_ int(10)) returns varchar(200) reads sql data
		begin
			declare Resultado_ varchar(200);
			select concat(o.sigla,' ',r.consecutivo) into Resultado_ from recibo_caja r,oficina o where r.autorizacion=Autorizacion_ and r.garantia=1 and r.oficina=o.id  limit 1;
			return Resultado_;
		end",1);
		
echo "<h3>t_respuestas_pqr: trae un valor positivo si un pqr tiene respuestas</h3>";
q("drop function if exists t_respuestas_pqr");
q("create function t_respuestas_pqr(Id_ int(10)) RETURNS tinyint(1) reads sql data
		begin
		declare Resultado_ int(10) default 0;
			select id into Resultado_ from pqr_respuesta where solicitud=Id_ limit 1;
			if Resultado_ != 0 then	return 1;
			else return 0;
			end if;
		end",1);

echo "<h3>t_resarcimiento_pqr: trae un valor positivo si un pqr tiene resarcimientos</h3>";
q("drop function if exists t_resarcimiento_pqr");
q("create function t_resarcimiento_pqr(Id_ int(10)) RETURNS tinyint(1) reads sql data
		begin
			declare Resultado_ int(10) default 0;
			select id into Resultado_ from pqr_resarcimiento where respuesta=Id_ limit 1;
			if Resultado_ != 0 then	return 1;
			else return 0;
			end if;
		end",1);

echo "<h3>tcall2cola1: trae la fecha de la cola 2 o del ingreso de un siniesro si no la hay</h3>";
q("drop function if exists tcall2cola1");
q("create function tcall2cola1(Id_ int(10)) RETURNS datetime reads sql data
		begin
			declare Cola1_ datetime;
			select fecha into Cola1_ from call2cola1 where siniestro=Id_ order by fecha desc limit 1;
			if Cola1_ != '0000-00-00 00:00:00' then	return Cola1_;
			else 
				select ingreso into Cola1_ from siniestro where id=Id_;
				return Cola1_;
			end if;
		end",1);

echo "<h3>t_call2cola1: trae la fecha de la cola 2 o del ingreso de un siniesro si no la hay</h3>";
q("drop function if exists t_call2cola1");
q("create function t_call2cola1(Id_ int(10)) RETURNS varchar(20) reads sql data
		begin
			declare Cola1_ varchar(20) default '';
			select fecha into Cola1_ from call2cola1 where siniestro=Id_ order by fecha desc limit 1;
			return Cola1_;
		end",1);
		
echo "<h3>t_dato_siniestro: trae 1: numero, 2: ciudad 3: aseguradora de siniestros sin importar si es actual o historico</h3>";
q("drop function if exists t_dato_siniestro");
q("create function t_dato_siniestro(Id_ int(10),campo_ tinyint(1)) RETURNS varchar(20) reads sql data
		begin
			declare Resultado_ varchar(150) default '';
			if(campo_=1) then
				select concat(numero,' ',asegurado_nombre) into Resultado_ from siniestro where id=Id_ ;
				if(Resultado_='') then
					select concat(numero,' ',asegurado_nombre) into Resultado_ from siniestro_hst where id=Id_ ;
				end if;
				return Resultado_;
			end if;
			if(campo_=2) then
				select ciudad.nombre into Resultado_ from siniestro,ciudad where siniestro.id=Id_ and siniestro.ciudad=ciudad.codigo ;
				if(Resultado_='') then
					select ciudad.nombre into Resultado_ from siniestro_hst,ciudad where siniestro_hst.id=Id_ and siniestro_hst.ciudad=ciudad.codigo ;
				end if;
				return Resultado_;
			end if;
			if(campo_=3) then
				select aseguradora.nombre into Resultado_ from siniestro,aseguradora where siniestro.id=Id_ and siniestro.aseguradora=aseguradora.id ;
				if(Resultado_='') then
					select aseguradora.nombre into Resultado_ from siniestro_hst,aseguradora where siniestro_hst.id=Id_ and siniestro_hst.aseguradora=aseguradora.id ;
				end if;
				return Resultado_;
			end if;
			
		end",1);
	
	echo "<h3>t_ofi_sigla : trae la sigla de la oficina buscando por el id</h3>";
	q("drop function if exists t_ofi_sigla");
	q("create function t_ofi_sigla(Id_ int(10)) RETURNS varchar(20) reads sql data
		begin
			declare Resultado_ varchar(20) default '';
			select sigla into Resultado_ from oficina where id=Id_ limit 1;
			return Resultado_;
		end",1);

	
	echo "<br /><br />Fin de la definición de funciones.";
	echo "<br /><br /><input type='button' value='Cerrar esta ventana' onclick='window.close();void(null);'></body>";
?>