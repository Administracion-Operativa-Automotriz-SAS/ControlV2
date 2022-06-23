<?php
 
// PODER CAPTURAR LA CIUDAD EN UNA NUEVA REQUISICION
// COPIAR LA IMAGEN DE REFRESCADO EN LA CABECERA DE CONTROL

/********************
 * Autor Original: Sergio Urbina
 * 
 * Proyecto: Administrativo Adición ITEM DE REQUISICION
 * Documentos relacionados: 
 * Descripción del script:
 * Este el 
 * Cambios:
 * Autor: Sergio Urbina
 * 1. Se agrego la funcionalida de multiplicacion en Administrativo zrequisicion
 *
 * 
 * Fecha:27/02/2019
 *********************/

include('inc/funciones_.php');
sesion();
$USER=$_SESSION['User'];
$NUSUARIO=$_SESSION['Nombre'];
$BDA='aoacol_administra';
$NT_req=tu('requisicion','id');

if(!empty($Acc) && function_exists($Acc)){eval($Acc.'();');     die();}

function select2(){
	echo "<script
  <link href='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css' rel='stylesheet' />
  src='https://code.jquery.com/jquery-3.4.1.slim.js'
  crossorigin='anonymous'>
  src='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js'
  </script>
  ";
  
  echo "<script>
  $('#prov').select2();
  </script>
  
  
  ";
}
function ver_balance()
{
        global $BDA,$NUSUARIO,$FI,$FF,$USER;
        
		if($USER == 15){
			if(!$FI) {$FI=date('Y-m-d', strtotime('-1 month'));
			$FF=date('Y-m-d');}
		}else{
			if(!$FI) {$FI=date('Y').'-01-01';$FF=date('Y-m-d');}
		}
		html('CONTROL DE REQUISICIONES');
        java();
        echo "<body><script language='javascript'>centrar(s_ancho(),s_alto()/1.2);window.moveTo(0,0);</script>
        <h3>CONTROL DE REQUISICIONES <a onclick='recargar();' class='info'><img src='gifs/standar/Refresh.png' border='0'><span>Refrescar</span></a> .:. $NUSUARIO</h3>";
        
		if($USER == 15){
			echo "<h4>En este perfil podra ver todas las requiciones de los demas usuarios.<br><p>Estas son las requiciones de un mes hacia  atras, Use el filtro de fechas para ver mas requicisiones.</p></h4>";
		}
		echo "<form action='zrequisicion.php' target='_self' method='POST' name='forma' id='forma'>
                Requisiciónes de fecha: ".pinta_FC('forma','FI',$FI)." hasta fecha  ".pinta_FC('forma','FF',$FF)." <input type='submit' name='btncontinuar' id='btncontinuar' value=' CONTINUAR ' >
                <input type='hidden' name='Acc' value='ver_balance'>
        </form>";
        //ver_requisiciones();
		echo '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
		echo "<div class='container'>";
			echo "<div class='row'>";
				echo "<div class='col-lg-6 col-md-6'>"; 
					ver_requisiciones2();
				echo "</div>";
				echo "<div class='col-lg-6 col-md-6'>";
					filtro_requisiciones();
				echo "</div>";
			echo "</div>";
		echo "</div>";		
        echo "</body>";
}

function ver_balance_test()
{
       echo "disabled";
}

function filtro_requisiciones()
{
	header('Content-Type: text/html; charset=utf-8');
	include("views/subviews/requisiciones/filtro_requisiciones.html");
}

function java()
{
	global $NT_req;
     echo "<script language='javascript'>
				function adicionar_requisicion() { modal('zrequisicion.php?Acc=adicionar_requisicion',0,0,500,800,'adreq'); }
				function ver_imagen_requisicion(id) { modal('zrequisicion.php?Acc=ver_imagen_requisicion&id='+id,0,0,600,600,'imgrq'); }
				function adicionar_detaller(id) { modal('zrequisicion.php?Acc=adicionar_detaller&idr='+id,0,0,300,300,'add_dreq'); }
				function recargar() { window.open('zrequisicion.php?Acc=ver_balance&FI='+document.forma.FI.value+'&FF='+document.forma.FF.value,'_self'); }
				function cerrar_requisicion(id) { if(confirm('Desea cerrar la requisicion?')) { window.open('zrequisicion.php?Acc=cerrar_requisicion&id='+id,'Oculto_req'); } }
				function borrar_requisicion(id) { if(confirm('Desea borrar la requisicion?')) { window.open('zrequisicion.php?Acc=borrar_requisicion&id='+id,'Oculto_req'); } }
				function re_enviar_solicitud_aprobacion(id) { window.open('zrequisicion.php?Acc=renviar_requicision_verificar&id='+id+'&Aviso=1','Oculto_req'); }
				function borrar_item_requisicion(id) { if(confirm('Desea eliminar el item de esta requisicion?')) { window.open('zrequisicion.php?Acc=borrar_item_requisicion&id='+id,'Oculto_req'); } }
				function evaluar_requisicion(id) {modal('zrequisicion.php?Acc=evaluar_requisicion&id='+id,0,0,400,800,'evr');}
				function ver_requisicion(dato)
				{
						document.getElementById('Edicion_requisicion').style.visibility='visible';
						document.getElementById('Edicion_requisicion').src='zrequisicion.php?Acc=ver_requisicion&id='+dato;
				}

                function aparece(dato,dato2)
                {
                        var Ob=document.getElementById(dato);
                        if(Ob.style.visibility=='hidden') {Ob.style.visibility='visible';Ob.style.position='relative';if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/menos_opciones.png';}
                        else    {Ob.style.visibility='hidden';Ob.style.position='absolute';if(Ob=document.getElementById('img_'+dato)) Ob.src='gifs/mas_opciones.png';recoger(dato);}
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
                function cerrar_vista_requisicion()
                {
                        document.getElementById('Edicion_requisicion').style.visibility='hidden';
                        document.getElementById('Edicion_requisicion').src='gifs/Loading.gif';
                }
				function modificar_requisicion(id)
				{ modal('marcoindex.php?Acc=mod_reg&id='+id+'&Num_Tabla=$NT_req',0,0,500,600,'modrequisicion');}
        </script>
        <style tyle='text/css'>
                td {font-size:9px;}
                a {cursor:pointer;}
        </style>";
}

function ver_requisiciones()
{
        global $BDA,$NUSUARIO,$FI,$FF;
        $Estados=q("select * from estado_requisicion order by id");
        $A_estados=array();
		$A_estados[0]=array("color"=>'dddddd',"nombre"=>'Nueva');
        while($E=mysql_fetch_object($Estados))
        {
                $A_estados[$E->id]=array("color"=>$E->color_co,"nombre"=>$E->nombre);
        }
        if($Requisiciónes=q("select * from $BDA.requisicion where ubicacion=0 and solicitado_por='$NUSUARIO' and 
												fecha between '$FI 00:00:00' and '$FF 23:59:59' order by fecha desc "))
        {
                echo "<table border cellspacing='0' cellpadding='10' width='100%' bgcolor='ddddee'>";
                include('inc/link.php');
                while($Rq=mysql_fetch_object($Requisiciónes))
                {
                        $Total_rq=qo1m("select sum(valor) from $BDA.requisiciond where requisicion=$Rq->id",$LINK);
                        echo "<tr>
                                <td><table border cellspacing='0' width='100%'>
                                        <tr><td width=20 bgcolor='eeeeee'><b>Id</b></td><td width=20><b>$Rq->id</b></td><td width=50 bgcolor='eeeeee'><b>Fecha:</b></td>
                                        <td width=90><b>$Rq->fecha</b></td><td width=80 bgcolor='eeeeee'><b>Solicitado por:</b></td><td width=300><b>$Rq->solicitado_por</b></td>
                                        <td width=20 align='center'><a onclick='ver_imagen_requisicion($Rq->id)' style='cursor:pointer'>";
                        if($Rq->cotizacion_f && $Rq->cotizacion2_f && $Rq->cotizacion3_f) echo "<img src='gifs/standar/Search.png' border='0'>";
                        else echo "<img src='gifs/standar/Warning.png' border='0'> ".(($Rq->cotizacion_f?1:0)+($Rq->cotizacion2_f?1:0)+($Rq->cotizacion3_f?1:0));
                        echo "</a></td><td width=40 bgcolor='eeeeee'>Valor:</td><td align='right' width=100><b>$ ".coma_format($Total_rq)."</b></td>
                                <td width=40 bgcolor='eeeeee'>Estado:</td></td>
                                <td width='100' bgcolor='".$A_estados[$Rq->estado]['color']."'><b>".$A_estados[$Rq->estado]['nombre']."</b> ";
                        if($Rq->estado==1) echo "<a style='cursor:pointer' onclick='re_enviar_solicitud_aprobacion($Rq->id);'><img src='gifs/standar/derecha.png' border='0'></a>";
                        if($Rq->estado==2) echo "<a style='cursor:pointer' onclick='evaluar_requisicion($Rq->id);' class='rinfo'><img src='img/diploma.png' height='22px'><span style='width:100px'>Evaluar Requisición</span></a>";
                        echo "</td></tr></table>";
                        $DetalleR=mysql_query("select *,$BDA.t_requisiciont(tipo) as ntipo,$BDA.t_requisicionc(clase) as nclase
                                                                                                        FROM $BDA.requisiciond where requisicion=$Rq->id",$LINK);
                        echo "<table border cellspacing='0' width='100%'><tr>
                                <td align='center' bgcolor='999999'><b>Descripcion</b></td>
                                <td align='center' bgcolor='999999'><b>Clase</b></td>
                                <td align='center' bgcolor='999999'><b>Valor</b></td>
                                <td align='center' bgcolor='999999'><b>Utilidades</b></td>
                                </tr>";
                        while($DR =mysql_fetch_object($DetalleR ))
                        {
                                echo "<tr>
                                <td>$DR->ntipo .:. $DR->observaciones</td>
                                <td width='100'>$DR->nclase</td>
                                <td align='right' width='100'>".coma_format($DR->valor)."</td>
                                <td align='center'>";
                                if(!$Rq->cerrada) echo "<a class='rinfo' onclick='borrar_item_requisicion($DR->id);'><img src='gifs/standar/Cancel.png' border='0'><span>Borrar</span></a>";
                                echo "</td></tr>";
                        }
                        echo "</table>";
                        if(!$Rq->cerrada){
							
							echo "<a class='info' href='javascript:adicionar_detaller($Rq->id);'><img src='gifs/standar/nuevo_registro.png' border='0'>Adicionar item<span style='width:200px'>Adicionar un item al detalle de la requisicion</span></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a class='info' href='javascript:cerrar_requisicion($Rq->id);'><img src='gifs/standar/stop_16.png' border='0'> Cerrar Requisición - Enviar correo de solicitud de aprobación<span style='width:200px'>Cerrar Requisición - envía solicitud de aprobación automática.</span></a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a class='info' href='javascript:borrar_requisicion($Rq->id);'><img src='gifs/standar/borra_registro.png' border='0'> Borrar Requisición<span style='width:200px'>Borrar Requisición</span></a>";
							echo "</td></tr>";
						}
							
                }
                mysql_close($LINK);
                echo "</table><iframe name='Oculto_req' id='Oculto_req' style='visibility:hidden' width='1' height='1'></iframe>";
        }
        echo "<a class='info' href='javascript:adicionar_requisicion();'><img src='gifs/standar/nuevo_registro.png' border='0'>  Adicionar Requisición<span>Adicionar Requisición</span></a>";
}

function ver_requisiciones2()
{
        global $BDA,$NUSUARIO,$FI,$FF,$NT_req,$USER;
		if($USER!=8) $FILTRO=" and solicitado_por='$NUSUARIO' "; else $FILTRO='';
        $Estados=q("select * from estado_requisicion order by id");
        $A_estados=array();
		$A_estados[0]=array("color"=>'dddddd',"nombre"=>'Nueva');
        while($E=mysql_fetch_object($Estados)){$A_estados[$E->id]=array("color"=>$E->color_co,"nombre"=>$E->nombre);}

        echo "<a class='info' href='javascript:adicionar_requisicion();'><img src='gifs/standar/nuevo_registro.png' border='0'>  Adicionar Requisición<span>Adicionar Requisición</span></a>
                <iframe name='Edicion_requisicion' id='Edicion_requisicion' frameborder='no' style='visibility:hidden;position:fixed;left:20;top:10;z-index:100;' width='90%' height='90%' src='gifs/Loading.gif'></iframe>";

        echo "<table cellspacing='2' bgcolor='eeeeee'><tr><th colspan='2'>Control de Requisiciónes</th></tr>";
        /// REQUISICIONES ADMINISTRATIVAS Y DE MOVILIDAD
        $Perfil_requisicion='2,3,4';
        include('inc/link.php');
        
		
		if($USER == 15){
			$Periodos=mysql_query("Select distinct date_format(fecha,'%Y-%m') as periodo from $BDA.requisicion where fecha between '$FI 00:00:00' and '$FF 23:59:59' 
			                         order by fecha desc ",$LINK);
		}else{
			$Periodos=mysql_query("Select distinct date_format(fecha,'%Y-%m') as periodo from $BDA.requisicion where fecha between '$FI 00:00:00' and '$FF 23:59:59'
													$FILTRO  and perfil in ($Perfil_requisicion) order by fecha desc ",$LINK);
		}
		
		if(mysql_num_rows($Periodos))
        {

                echo "<tr><td valign='top' bgcolor='ffffff' width='80px'>ADMINISTRATIVAS</td><td valign='top'>
                                                        <table cellspacing='2' bgcolor='eeeeee'>";
                while($Pe=mysql_fetch_object($Periodos))
                {
                        $llave_periodo='AD-'.$Pe->periodo;
                        echo "<tr><td valign='top' bgcolor='ffffff' width='50px' align='center'>
                                                                <img id='img_$llave_periodo' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('$llave_periodo');\">
                                                                <a onclick=\"aparece('$llave_periodo','img_$llave_periodo');\">$Pe->periodo</a>
                                                                </td><td bgcolor='ffffff' valign='top'>
                                                        <table cellspacing='1' bgcolor='eeeeee' id='$llave_periodo' style='visibility:visible;position:relative;'>";
                        foreach($A_estados as $idestado => $aestado)
                        {
                                $llave_estado=$llave_periodo.'-e-'.$idestado;
                                $estado_bgcolor=$aestado['color'];$estado_nombre=$aestado['nombre'];
                                
								if($USER == 15){
									$Requisiciónes_por_estado=mysql_query("select *,$BDA.t_proveedor(proveedor) as nprov from $BDA.requisicion where estado=$idestado 
												  and date_format(fecha,'%Y-%m')='$Pe->periodo'  order by fecha desc",$LINK);
								}else{
									$Requisiciónes_por_estado=mysql_query("select *,$BDA.t_proveedor(proveedor) as nprov from $BDA.requisicion where estado=$idestado 
												$FILTRO  and date_format(fecha,'%Y-%m')='$Pe->periodo' and perfil in ($Perfil_requisicion)
                                                order by fecha desc",$LINK);
								}
								
								$Cantidad=mysql_num_rows($Requisiciónes_por_estado);
                                if($Cantidad>0)
                                {
                                        echo "<tr><td bgcolor='$estado_bgcolor' valign='top' width='110px' nowrap='yes'>
                                                                        <img id='img_$llave_estado' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('$llave_estado');\">
                                                                        <a onclick=\"aparece('$llave_estado','img_$llave_estado');\">$estado_nombre [$Cantidad]</a>
                                                                        <script language='javascript'>
                                                                                if(!Hijos['$llave_periodo']) Hijos['$llave_periodo']=new Array();
                                                                                Hijos['$llave_periodo'][Hijos['$llave_periodo'].length]='$llave_estado';
                                                                        </script>
                                                                        </td><td bgcolor='ffffff'>
                                                                        <table cellspacing='1' bgcolor='eeeeee' id='$llave_estado' style='visibility:hidden;position:absolute;'>
                                                                        <tr><th>#</th><th>Numero</th><th>Fecha</th><th>Proveedor</th></tr>";
                                        $Contador_req=0;
                                        while($Req=mysql_fetch_object($Requisiciónes_por_estado))
                                        {
                                                $Contador_req++;
                                                echo "<tr><td>$Contador_req</td><td bgcolor='ffffff' align='center'>";
												if($NT_req) echo "<a onclick='modificar_requisicion($Req->id)'>$Req->id</a>";
												else echo $Req->id;
												echo "</td>
                                                                <td bgcolor='ffffff' nowrap='yes'>$Req->fecha</td>
                                                                <td bgcolor='ffffff' nowrap='yes' width='300px'>$Req->nprov</td>
                                                                <td bgcolor='ffffff' nowrap='yes'><a class='info' onclick='ver_requisicion($Req->id);'><img src='gifs/standar/Preview.png'><span style='width:200px'>Ver Requisición</span></a></td>
                                                                </tr>";
                                        }
                                        echo "</table></td></tr>";
                                }
                        }
                        echo "</table></td></tr>";
                }
                echo "</table></td></tr>";
        }

        /// REQUISICIONES OPERATIVAS
        $Perfil_requisicion = "1";
		
		if($USER == 15){
			$sqlPeriodos = "Select distinct date_format(fecha,'%Y-%m') as periodo from $BDA.requisicion where fecha between '$FI 00:00:00' and '$FF 23:59:59'
									  order by fecha desc ";
		}else{
			$sqlPeriodos = "Select distinct date_format(fecha,'%Y-%m') as periodo from $BDA.requisicion where fecha between '$FI 00:00:00' and '$FF 23:59:59'
									$FILTRO and  perfil = $Perfil_requisicion order by fecha desc ";
		}
		if($Periodos=q($sqlPeriodos)){
                include('inc/link.php');
                echo "<tr><td valign='top' bgcolor='ffffff' width='80px'>OPERATIVAS</td><td valign='top'>
                                                        <table cellspacing='2' bgcolor='eeeeee'>";
                while($Pe=mysql_fetch_object($Periodos))
                {
                        $llave_periodo='OP-'.$Pe->periodo;
                        echo "<tr><td valign='top' bgcolor='ffffff' width='60px' align='center'>
                                                        <img id='img_$llave_periodo' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('$llave_periodo');\">
                                                                <a onclick=\"aparece('$llave_periodo','img_$llave_periodo');\">$Pe->periodo</a>
                                                                </td><td bgcolor='ffffff' valign='top'>
                                                        <table cellspacing='1' bgcolor='eeeeee' id='$llave_periodo' style='visibility:visible;position:relative;'>";
                        foreach($A_estados as $idestado => $aestado)
                        {
                                $llave_estado=$llave_periodo.'-e-'.$idestado;
                                $estado_bgcolor=$aestado['color'];$estado_nombre=$aestado['nombre'];
                                
								if($USER == 15){
									$sqlRequistate = "select *,$BDA.t_proveedor(proveedor) as nprov from $BDA.requisicion where estado=$idestado 
												and date_format(fecha,'%Y-%m')='$Pe->periodo'
                                                order by fecha desc";
								}else{
									$sqlRequistate = "select *,$BDA.t_proveedor(proveedor) as nprov from $BDA.requisicion where estado=$idestado 
												$FILTRO and date_format(fecha,'%Y-%m')='$Pe->periodo' and perfil=$Perfil_requisicion
                                                order by fecha desc";
								}
								
								$Requisiciónes_por_estado=mysql_query($sqlRequistate,$LINK);
                                
								
								$Cantidad=mysql_num_rows($Requisiciónes_por_estado);
                                if($Cantidad>0)
                                {
                                        echo "<tr><td bgcolor='$estado_bgcolor' valign='top' width='110px' nowrap='yes'>
                                                                        <img id='img_$llave_estado' src='gifs/mas_opciones.png' border='0' onclick=\"expandir('$llave_estado');\">
                                                                        <a onclick=\"aparece('$llave_estado','img_$llave_estado');\">$estado_nombre [$Cantidad]</a>
                                                                        <script language='javascript'>
                                                                                if(!Hijos['$llave_periodo']) Hijos['$llave_periodo']=new Array();
                                                                                Hijos['$llave_periodo'][Hijos['$llave_periodo'].length]='$llave_estado';
                                                                        </script>
                                                                        </td><td bgcolor='ffffff'>
                                                                        <table cellspacing='1' bgcolor='eeeeee' id='$llave_estado' style='visibility:hidden;position:absolute;'>
                                                                        <tr><th>#</th><th>Numero</th><th>Fecha</th><th>Proveedor</th></tr>";
                                        $Contador_req=0;
                                        while($Req=mysql_fetch_object($Requisiciónes_por_estado))
                                        {
                                                $Contador_req++;
                                                echo "<tr><td>$Contador_req</td><td bgcolor='ffffff' align='center'>$Req->id</td>
                                                                <td bgcolor='ffffff' nowrap='yes'>$Req->fecha</td>
                                                                <td bgcolor='ffffff' nowrap='yes' width='300px'>$Req->nprov</td>
                                                                <td bgcolor='ffffff' nowrap='yes'><a class='info' onclick='ver_requisicion($Req->id);'><img src='gifs/standar/Preview.png'><span style='width:200px'>Ver Requisición</span></a></td>
                                                                </tr>";
                                        }
                                        echo "</table></td></tr>";
                                }
                        }
                        echo "</table></td></tr>";
						
                }
				echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.1/sweetalert2.css'>
					<script src='https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.1/sweetalert2.all.js'></script>";
				echo "<script> Swal.fire(
						  'Que bien!',
						  'Información cargada!',
						  'success'
						) </script>";
                echo "</table></td></tr>";
        }
        mysql_close($LINK);
        echo "</table>";
		
}

function adicionar_requisicion()
{
        global $BDA,$NUSUARIO,$USER;
        html('ADICION DE REQUISICION');
        $Hoy=date('Y-m-d H:i:s');
        $Nusuario=$_SESSION['Nombre'];
        echo "<script language='javascript'>
                function grabar_n_req()
                {
                        if(!document.forma.ciudad.value) {alert('Debe seleccionar la ciudad');ciudad.style.backgroundColor='ffffaa';ciudad.focus();return false;}
                        if(!document.forma.proveedor) {alert('Debe seleccionar un proveedor confiable o medianamente confiable. Cree nuevo proveedor o solicite calificación de uno existente.');return false;}
                        else if(!document.forma.proveedor.value) {alert('Debe seleccionar un proveedor confiable o medianamente confiable');document.forma.proveedor.style.backgroundColor='ffffaa';return false;}
						if(!document.forma.perfil.value) {alert('Debe seleccionar a quien va dirigida la requisición');document.forma.perfil.style.backgroundColor='ffffaa';return false;}
						
                        document.forma.submit();
                }
                function crear_nuevo_proveedor() {modal('zproveedor.php?Acc=adicion_de_proveedor','crear_nuevo_proveedor');}
                function realizar_calificacion() {window.open('zproveedor.php?Acc=realizar_calificacion&refrescar_opener=1','_self');}
                function valida_cambio_proveedor() {if(document.forma.proveedor.value) document.forma.grabar.style.visibility='visible'; else document.forma.grabar.style.visibility='hidden';}
        </script><body><h3>Adición de Requisición</h3>
        <form action='zrequisicion.php' target='_self' method='POST' name='forma' id='forma'>
                <table cellspacing='5' bgcolor='dddddd'>"; 
                    if($USER == 15 ){
					 $sqlProveedores = "select id,nombre,calificacion_actual,$BDA.t_ciudad(ciudad) as nciudad from $BDA.proveedor where calificacion_actual in ('M','C') or tipo='E' order by nombre";
						echo "<tr><td style='font-size:18px' nowrap='yes'>Selecione fecha:</td><td bgcolor='ffffff'><input name='fecha' type='date'></td></tr>";
				   }else{
					 $sqlProveedores = "select id,nombre,calificacion_actual,$BDA.t_ciudad(ciudad) as nciudad from $BDA.proveedor where calificacion_actual in ('M','C') and activo = 1 or tipo='E' order by nombre";
					    echo "<tr><td style='font-size:18px' nowrap='yes'>Fecha:</td><td bgcolor='ffffff'><input type='text' name='fecha' value='$Hoy' readonly></td></tr>";}
                        echo "<tr><td style='font-size:18px' nowrap='yes'>Solicitado Por:</td><td bgcolor='ffffff'><input type='text' name='solicitado_por' value='$Nusuario' size='80' readonly></td></tr>
						<tr><td style='font-size:18px' nowrap='yes'>Dirigido a</td><td>".menu1("perfil","select id,nombre from perfil_requisicion",0,1)."</td></tr>
                        <tr><td style='font-size:18px' nowrap='yes'>Ciudad:</td><td bgcolor='ffffff'>".menu1("ciudad","select ciudad,nombre from oficina",0,1)."</td></tr>
                        <tr><td style='font-size:18px' nowrap='yes'>Proveedor:</td><td bgcolor='ffffff'><br>
                        La lista que aparece a continuación corresponde a los proveedores totalmente confiables y medianamente confiables, de acuerdo a la selección realizada siguiendo los lineamientos del proceso de Calidad. <br>
                        También aparecen los empleados de AOA a los que se les hace anticipos.<br>
                        ";
		if($Proveedores=q($sqlProveedores))
        {
			echo "<select name='proveedor' style='width:300px' onchange='valida_cambio_proveedor();'><option value=''></option>";
			while($P=mysql_fetch_object($Proveedores))
			{
					$bgc='cccccc';if($P->calificacion_actual=='M') $bgc='ffffaa';if($P->calificacion_actual=='C') $bgc='aaffaa';
					echo "<option value='$P->id' style='background-color:#$bgc'>$P->nombre [$P->nciudad]</option>";
			}
			echo "</select><br><br>
			Si el proveedor que busca no aparece en la lista, existen dos posibilidades: <br>
			<br><li> Que no esté creado en la base de datos. Por lo tanto puede tramitar con adquisiciones: <a style='display:none' onclick='crear_nuevo_proveedor();' class='info'><img src='img/adicionar_proveedor.png' height='30' border='0'><span style='width:100px'>Crear Proveedor</span></a>
			<li> Que si esté creado pero no haya sido seleccionado. Puede realizar la <b><i>Selección</i></b> para que aparezca en la lista de proveedores dando click aquí: <a onclick='realizar_calificacion()' class='info'><img src='img/diploma.png' height='30' border='0'><span style='width:100px'>Realizar Calificación</span></a>
			";
        }
        else echo "<b style='color:red'>No hay proveedores confiables</b><br>
                Si desea puede <b><i>Seleccionar</i></b> un proveedor específico dando click aquí: <a onclick='realizar_calificacion();' class='info'><img src='img/diploma.png' height='30' border='0'><span style='width:100px'>Realizar Calificación</span></a><br>
                Si desea crear un nuevo proveedor puede dar click aquí: <a  onclick='crear_nuevo_proveedor();' class='info'><img src='img/adicionar_proveedor.png' height='30' border='0'><span style='width:100px'>Crear Proveedor</span></a><br> ";
        echo "</td></tr>
                        <tr><td align='center' colspan='2'><input type='button' name='grabar' id='grabar' value=' GRABAR NUEVA REQUISICION ' onclick='grabar_n_req();' 
						style='visibility:hidden;font-size:18px;font-weight:bold;'></td></tr>
                </table><input type='hidden' name='Acc' value='adicionar_requisicion_ok'>
        </form>
        </body>";
}

function adicionar_requisicion_ok()
{
        global $fecha,$solicitado_por,$placa,$ciudad,$BDA,$proveedor,$perfil;
		
		$Nid=q("insert into $BDA.requisicion (fecha,solicitado_por,placa,ciudad,estado,perfil,proveedor) values ('$fecha','$solicitado_por','$placa','$ciudad','0',$perfil,'$proveedor');");
        
		echo "<body>
		<form action='zrequisicion.php' target='_self' method='POST' name='forma' id='forma'>
			<input type='hidden' name='Acc' value='adicionar_detaller'>
			<input type='hidden' name='idr' value='$Nid'>
		</form><script language='javascript'>document.forma.submit();</script></body>";
}

function ver_imagen_requisicion()
{
        global $id,$BDA;
        html('IMAGEN DE REQUISICION - COTIZACION');
        $D=qo("select * from $BDA.requisicion where id=$id");
        echo "<script language='javascript'>function salir(){opener.location.reload();}</script>
                        <body onunload='salir()'><h3>IMAGEN DE COTIZACION EN REQUISICION</h3>";
        if($D->cotizacion_f) echo "<iframe name='cot1' id='cot1' style='visibility:visible' width='100%' height='400px' src='$D->cotizacion_f'></iframe>";
        else
                echo "Por favor cargue la imágen de la cotización de la requisición y tan pronto la cargue cierre esta ventana.<br>
                <iframe id='simg_cotizacion_f' name='simg_cotizacion_f' width='100%' src='marcoindex.php?Acc=reg_sube_img&T=requisicion&C=cotizacion_f&Id=$id&tri=1000&ruta=requisicion&rfr=parent.parent.location.reload()' height='400px'></iframe>";
        if($D->cotizacion2_f) echo "<iframe name='cot2' id='cot2' style='visibility:visible' width='100%' height='400px' src='$D->cotizacion2_f'></iframe>";
        else
                echo "Por favor cargue la imágen de la cotización de la requisición y tan pronto la cargue cierre esta ventana.<br>
                <iframe id='simg_cotizacion2_f' name='simg_cotizacion2_f' width='100%' src='marcoindex.php?Acc=reg_sube_img&T=requisicion&C=cotizacion2_f&Id=$id&tri=1000&ruta=requisicion&rfr=parent.parent.location.reload()' height='400px'></iframe>";
        if($D->cotizacion3_f) echo "<iframe name='cot3' id='cot3' style='visibility:visible' width='100%' height='400px' src='$D->cotizacion3_f'></iframe>";
        else
                echo "Por favor cargue la imágen de la cotización de la requisición y tan pronto la cargue cierre esta ventana.<br>
                <iframe id='simg_cotizacion3_f' name='simg_cotizacion3_f' width='100%' src='marcoindex.php?Acc=reg_sube_img&T=requisicion&C=cotizacion3_f&Id=$id&tri=1000&ruta=requisicion&rfr=parent.parent.location.reload()' height='400px'></iframe>";
        echo "</body>";
}

function get_data_proveedor_administra(){
        header('Content-Type: text/html; charset=utf-8');
		global $type;
		
		$sql = "SELECT provee_produc_serv.id,concat(provee_produc_serv.nombre , ' = ' ,sistema.nombre, ' = ', unidad_de_medida.nombre)  as nombre
																	FROM aoacol_administra.provee_produc_serv 
																	INNER JOIN aoacol_administra.sistema ON provee_produc_serv.sistema = sistema.id  
																	INNER JOIN aoacol_administra.unidad_de_medida ON provee_produc_serv.unidad_de_medida = unidad_de_medida.id
																	where provee_produc_serv.activacion = 1  and provee_produc_serv.uso in (2,3) and tipo =  $type  
																	order by provee_produc_serv.nombre";
		$result = q($sql);
	
	$rows = array();
	
	while($row = mysql_fetch_object($result))
	{
		$row->nombre = utf8_encode($row->nombre);
		array_push($rows, $row);
	}
	
	//print_r($rows);
	
	echo json_encode($rows);
		

}
function modificar_item_funcion(){
	global $idr,$idRequi,$BDA;
	
	echo '<!-- Jquery  -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		
	<!-- select jquery --> 

	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script  src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>';
	
	html('MODIFICACION DE ITEM');
	$verificar=qo("select 
        requisiciond.requisicion,oficina.centro_operacion as centrodeoperacion,oficina.nombre,ubicacion.oficina,requisicion.ubicacion
        from aoacol_administra.requisiciond
        LEFT OUTER JOIN requisicion on requisiciond.requisicion = requisicion.id
		LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
		LEFT OUTER JOIN aoacol_aoacars.oficina on  ubicacion.oficina = oficina.id where requisicion = $idRequi");
		
		if($verificar->ubicacion == "" || $verificar->ubicacion == 0){
			$varEquals = 1;
		}else{
			$varEquals = 2;
		}
		
		if($verificar->centrodeoperacion == ''){
			$sqlAdministrativo = "select aoacol_administra.requisiciond.*,provee_produc_serv.nombre as ntipo,requisiciond.id as idItem,requisicionc.nombre as nclase,tipo.nombre as tipoBS,
                    unidad_de_medida.nombre as unidad_medida,requisiciond.observaciones,requisiciond.cantidad,
                    requisiciond.requisicion,requisiciond.valor_total,aoacol_aoacars.vehiculo.placa,
                    concat(oficina.centro_operacion,'  ',oficina.nombre) as centrodeoperacion,requisiciond.centro_costo as centrocosto,requisicion.fecha
					from aoacol_administra.requisiciond
					LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
                    LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id
                    LEFT OUTER JOIN aoacol_aoacars.oficina on requisiciond.centro_operacion = aoacol_aoacars.oficina.id
                    LEFT OUTER JOIN requisicionc on requisiciond.clase = requisicionc.id
                    LEFT OUTER JOIN ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo
					LEFT OUTER JOIN requisicion on requisiciond.requisicion = requisicion.id
					where requisiciond.id = $idr order by requisiciond.id";
			$sql = $sqlAdministrativo;
			
		}else{
			/*Consulta realizada por temas de centro de operaciones hay que sustraer este de la tabla ubicaciones*/			
		     $sqlControOperativo = "select aoacol_administra.requisiciond.*,provee_produc_serv.nombre as ntipo,requisiciond.id as idItem,requisicionc.nombre as nclase,tipo.nombre as tipoBS,
                    unidad_de_medida.nombre as unidad_medida,requisiciond.observaciones,requisiciond.cantidad,
                    requisiciond.requisicion,requisiciond.valor_total,aoacol_aoacars.vehiculo.placa,
                    concat( oficina.centro_operacion,'  ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto,
                    requisicion.fecha, ubicacion.oficina
					from aoacol_administra.requisiciond
					LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
                    LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id
                    LEFT OUTER JOIN requisicionc on requisiciond.clase = requisicionc.id
                    LEFT OUTER JOIN ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo
					LEFT OUTER JOIN requisicion on requisiciond.requisicion = requisicion.id
                    LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
                    LEFT OUTER JOIN aoacol_aoacars.oficina on  ubicacion.oficina = oficina.id
                    LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id
					WHERE requisiciond.id = $idr order by requisiciond.id 		
                    ";
			$sql = $sqlControOperativo;
			
		}
		$consultaItem = "SELECT provee_produc_serv.id,provee_produc_serv.nombre as nameItem,concat(provee_produc_serv.nombre , ' = ' ,sistema.nombre, ' = ', unidad_de_medida.nombre)  as nombre
																	FROM provee_produc_serv 
																	INNER JOIN sistema ON provee_produc_serv.sistema = sistema.id  
																	INNER JOIN unidad_de_medida ON provee_produc_serv.unidad_de_medida = unidad_de_medida.id
																	where provee_produc_serv.activacion = 1   
																	order by provee_produc_serv.nombre";
																	
		$queryClase = "select * from $BDA.requisicionc where id = 2 or id = 1 or id = 5 order by nombre";															
		$consultaTipo = "SELECT id,nombre FROM tipo";
		$proyectoPlaca = "select id,placa from  aoacol_aoacars.vehiculo;";
		$queryCentroOpe = "select id,nombre,centro_operacion from aoacol_aoacars.oficina where centro_operacion != '' order by nombre";
		$queryCcostos = "select nombre,codigo  from ccostos_uno";
		
		
		
		$varQueryCostos =q($queryCcostos);
		$varQueryOpeCentro = q($queryCentroOpe);
		$varQueryProPla = q($proyectoPlaca);
		$varQueryClase = q($queryClase);
		$varTipo=q($consultaTipo);
		$var2=q($consultaItem);
        $var=qo($sql);
		
		
	   include('views/subviews/modificar_items.php');
}

function modificar_item_ok(){
	global $tipoRequi,$tipoBS,$nclase,$tipo_cobro,$factor,$valor_unitario,$cantidad,
	$valor_total,$proyecto_placa,$centrodeoperacion,$centrocosto,$descripcion,$idItem,$varEquals;
	
	
	/*echo "update requisiciond set tipo1 = '$tipoRequi', tipo = '$tipoBS',clase= '$nclase',tipo_cobro='$tipo_cobro',factor='$factor', 
	valor='$valor_unitario',cantidad='$cantidad',valor_total = '$valor_total',id_vehiculo = 
	'$proyecto_placa',centro_operacion = '$centrodeoperacion',centro_costo = '$centrocosto',observaciones = '$descripcion' where id = $idItem";*/
	
	if($varEquals == 1){
		
		q("update requisiciond set tipo1 = '$tipoRequi', tipo = '$tipoBS',clase= '$nclase',tipo_cobro='$tipo_cobro',factor='$factor', 
	     valor='$valor_unitario',cantidad='$cantidad',valor_total = '$valor_total',id_vehiculo = 
		'$proyecto_placa',observaciones = '$descripcion' where id = $idItem");
	}else{
	
	q("update requisiciond set tipo1 = '$tipoRequi', tipo = '$tipoBS',clase= '$nclase',tipo_cobro='$tipo_cobro',factor='$factor', 
	valor='$valor_unitario',cantidad='$cantidad',valor_total = '$valor_total',id_vehiculo = 
	'$proyecto_placa',centro_operacion = '$centrodeoperacion',centro_costo = '$centrocosto',observaciones = '$descripcion' where id = $idItem");
	}
	
	
	
	
	//echo "<script>alert('Registro modificado'); </script>";
	echo "
	<script src='//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
	<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.1/sweetalert2.css'>
		<script src='https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.1/sweetalert2.all.js'></script>
      
	  <script>
	 
  $(document).ready(function () {
      swal({ title: 'Que bien', text: 'El item ya se encuentra modificado !', type: 'success' });
  setTimeout(function () { window.close();}, 2000);
  });

	  </script>";
	
	
}

function adicionar_detaller()
{
	
	echo '<!-- Jquery  -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		
	<!-- select jquery --> 

	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script  src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

	<script>	
		$(document).ready(function() {
			/*readOnly de javascript funciona solo para leer el input y disable solo*/
			document.forma.valorTotal.readOnly = true;
			//alert("jquery is working");
			$("#tipo1").select2();
			
		  $("#tipo2").change(function(){
			  let valor = $(this).val();
			  
			  $.post( "zrequisicion.php",{ Acc:"get_data_proveedor_administra",type:valor },function( data ){
				 let  html = "";
				 
				 data = JSON.parse(data);
				 
				 data.forEach(function( data ){
					 html += "<option value="+data.id+" >"+data.nombre+"</option>";
				 });
				 
				 $("#tipo1").html(html);
				 $("#tipo1").select2();
				 
				});
			});
			$("#placaproyecto").select2();
			
		});
	</script>
	';
        global $idr,$BDA,$USER;
        html('ADICION DE ITEM DE REQUISICION');
        $Req=qo("select * from $BDA.requisicion where id=$idr");
		
        if($USER == 15){
			$varSqlItem = "SELECT provee_produc_serv.id,concat(provee_produc_serv.nombre , ' = ' ,sistema.nombre, ' = ', unidad_de_medida.nombre)  as nombre
																	FROM $BDA.provee_produc_serv
																	INNER JOIN $BDA.sistema ON provee_produc_serv.sistema = sistema.id
																	INNER JOIN $BDA.unidad_de_medida ON provee_produc_serv.unidad_de_medida = unidad_de_medida.id
																	where provee_produc_serv.activacion = 1  and provee_produc_serv.uso in (1,2,3)
																	order by provee_produc_serv.nombre";
		}else{
			$varSqlItem = "SELECT provee_produc_serv.id,concat(provee_produc_serv.nombre , ' = ' ,sistema.nombre, ' = ', unidad_de_medida.nombre)  as nombre
																	FROM $BDA.provee_produc_serv
																	INNER JOIN $BDA.sistema ON provee_produc_serv.sistema = sistema.id
																	INNER JOIN $BDA.unidad_de_medida ON provee_produc_serv.unidad_de_medida = unidad_de_medida.id
																	where provee_produc_serv.activacion = 1  and provee_produc_serv.uso in (2,3)
																	order by provee_produc_serv.nombre";
		
		}
		
		$result = q("select id,nombre,centro_operacion from aoacol_aoacars.oficina where centro_operacion != '' order by nombre");
		$centros_operacion = array();
		while ($fila = mysql_fetch_object($result)) {
			array_push($centros_operacion, $fila);
		}
		
		$centros_operacion_select = "<select name='centros_operacion'>";
		$centros_operacion_select .= "<option>Selecciona</option>";
		$centros_operacion_select .= "<option value='NAL'>NACIONAL</option>";
		foreach($centros_operacion as $centros)
		{
			$centros_operacion_select .= "<option value=".$centros->id." >".$centros->nombre." ".$centros->centro_operacion."</option>";	
		}
		$centros_operacion_select .= "</select>";
		
		$result = q("select nombre,codigo  from ccostos_uno");
		$centros_costo = array();
		while ($fila = mysql_fetch_object($result)) {
			array_push($centros_costo, $fila);
		}
		
		$centros_costo_select = "<select name='centros_costo'>";
		$centros_costo_select .= "<option>Selecciona</option>";
		foreach($centros_costo as $centros)
		{
			$centros_costo_select .= "<option value=".$centros->codigo." >".$centros->nombre."</option>";	
		}
		$centros_costo_select .= "</select>";
		
		$factor = "<select name='factor'>
		<option>Selecciona</option>
		<option value='Flota'>Flota</option>
		<!-- Option  Quitado por solicitud de Sergio Castillo Solicitudes de servicio-->
		<option value='Solicitudes de servicio' style='display: none;'>Solicitudes de servicio</option>
		</select>
		";
		
		echo "<script language='javascript'>
        function creartipos() { modal('zrequisicion.php?Acc=tipos_requisicion',0,0,600,600,'tr'); }
        function validar_nuevo_item(continua)
        {
                with(document.forma)
                {
                        if(continua) Continuar.value='1';else Continuar.value='0';
                        if(!Number(valor.value)) {alert('Debe digitar un valor presupuestado estimado, sin comas ni puntos');valor.style.backgroundColor='ffffaa';valor.focus();return false;}
                        if(!Number(cantidad.value)) {alert('Debe digitar la cantidad exacta, sin comas ni puntos');cantidad.style.backgroundColor='ffffaa';cantidad.focus();return false;}
                        if(!alltrim(observaciones.value)) {alert('Debe digitar los comentarios respetivos de este item');observaciones.style.backgroundColor='ffffaa';observaciones.focus();return false;}
						if(!tipo_cobro.value) {alert('Debe seleccionar el tipo de cobro');tipo_cobro.style.backgroundColor='ffffaa';tipo_cobro.focus();return false;}
						if(!alltrim(tipo1.value)) {alert('Debera seleccionar un ítem');tipo1.style.backgroundColor='ffffaa';tipo1.focus();return false;}
                        if(confirm('Desea grabar este item?'))
                        {
                                submit();
                        }
                }
        }
        function cerrar() { opener.location.reload(); window.close(); void(null); }
        function continuar() {window.open('zrequisicion.php?Acc=adicionar_detaller&idr=$idr&id=$id','_self');}
        function solicitar_nuevo_tipo() { window.open('zrequisicion.php?Acc=solicitar_nuevo_bien_servicio','_self');}
		
		function Multiplicar(){
		let var1 = $('#valor').val();
		let var2 = $('#cantidad').val();
		
		let rest = parseInt(var1)* parseInt(var2);
		console.log(rest);
		document.forma.valorTotal.value = rest;
	    }
        
		</script><body><script language='javascript'>centrar(800,500);</script>
        <h3>REQUISICION: $Req->id Fecha: $Req->fecha <br>Por: $Req->solicitado_por</h3>
        <form action='zrequisicion.php' target='Oculto_item' method='POST' name='forma' id='forma'>
                <table>
				<tr><td>Seleccioné el tipo</td><td>".menu1("tipo2","SELECT id,nombre FROM $BDA.tipo;",0,1,"width:300px")."<br>
                        </td></tr>
						
                        <tr><td>Tipo de Item</td><td>".menu1("tipo1","$varSqlItem",0,1,"width:300px")."<br>
                        Señor Usuario, si el tipo de requisición no aparece en la lista, puede solicitar su creación<br>
                        mediante este link:
                        <a class='info' onclick='solicitar_nuevo_tipo();'><img src='img/nuevotipo.png' height='20px'> Solicitar creación de nuevo tipo.<span style='width:100px'>Solicitar creación de nuevo tipo de requisición.</span></a>
                        </td></tr>
						
                        <tr><td>Clase de Requisición</td><td>".menu1("clase","select * from $BDA.requisicionc where id = 2 or id = 1 or id = 5 order by nombre",1)."</td></tr>
						<tr><td>Cobro:</td><td><select name='tipo_cobro'><option value=''></option><option value='S'>Sin Recobro</option><option value='C'>Con Recobro</option><!--<option value='N'>No aplica</option>--></select></td></tr>
                        <tr><td>Cantidad</td><td><input type='text' name='cantidad' id='cantidad' value='1' size='10' maxlength='100' OnKeyUp='Multiplicar()'></td></tr>
						<tr><td>Valor Unitario</td><td><input type='text' name='valor' id='valor' value='' size='10' maxlength='100' class='numero' alt='digite el valor sin comas ni puntos'  title='digite el valor sin comas ni puntos' OnKeyUp='Multiplicar()'></td></tr>
						<tr><td>Valor total:</td><td> <input type='number' name='valorTotal' value='' id='valorTotal'  size='100' maxlength='100'></td></tr>
						<tr>
							<td colspan='2'>
								
								<strong><p>Nota:<br>
									IVA: Impuesto al Valor Agregado IVA (Impuesto a las Ventas IVA).<br>
									Proveedores de Régimen Simplificado: Persona Natural no factura IVA.<br>
									Proveedores de Régimen Común: Persona Natural ó Jurídica que factura IVA.<br>
									El valor que presenta la cotización puede estar incluido el IVA. Ejemplo: Valor+ IVA o Valor IVA incluido. En caso tal usted deberá realizar la operación para hallar la base antes de IVA.</p>
								</strong>
							
							</td>
						</tr>
                        <tr>
							<td>Centro de operación</td>
							<td>$centros_operacion_select</td>
						</tr>
						<tr>
							<td>Centro de costo</td>
							<td>$centros_costo_select</td>
						</tr>
						<tr>
							<td>Factor</td>
							<td>$factor</td>
						</tr>
						<tr><td>Proyecto o placa</td><td>".menu1("placaproyecto","select id,placa from  aoacol_aoacars.vehiculo;",0,1,"width:300px")."<br>
                        </td></tr>
						<tr><td>Descripcion del item</td><td><textarea name='observaciones' cols=80 rows=5></textarea></td></tr>
                        <tr><td align='center'><input type='button' name='grabar_item' id='grabar_item' value=' GRABAR ITEM  Y VOLVER AQUI ' onclick='validar_nuevo_item(1);'></td>
                        <tr><!--<td align='center'><input type='button' name='grabar_item' id='grabar_item' value=' GRABAR ITEM  Y CERRAR ' onclick='validar_nuevo_item(0);'></td>--></tr>
                </table>
                <input type='hidden' name='Acc' value='adicionar_detaller_ok'>
                <input type='hidden' name='idr' value='$idr'>
                <input type='hidden' name='Continuar' value=''>
        </form>
        <iframe name='Oculto_item' id='Oculto_item' style='visibility:hidden' width='1' height='1'></iframe>
        <!--<button onclick='open_cot_window()'>Agregar cotizaciones a la requisición</button>-->		
		</body>
		<script>
			function open_cot_window()
			{
				window.open('http://app.aoacolombia.com/Control/operativo/zbalance_estado.php?Acc=ver_imagen_requisicion&id=$idr','cotizaciones', 'width=500,height=500');
			} 
		</script>
		";
		
}

function subir_excel(){
include("views/cargues/subir_requisiciones.html");
}

function adicionar_detaller_ok()
{
        global $BDA,$idr,$tipo1,$clase,$valor,$observaciones,$Continuar,
		$cantidad,$tipo_cobro,$centros_operacion,$centros_costo,$valorTotal,$placaproyecto,$factor;
        if($placaproyecto == ''){
			$placaproyecto = 'null';
		}else{
			$placaproyecto;
		}
		
		/*echo "insert into $BDA.requisiciond (requisicion,tipo1,clase,valor,observaciones,tipo_cobro,cantidad,centro_operacion,centro_costo,valor_total,id_vehiculo,factor) values 
		('$idr','$tipo1','$clase','$valor',\"$observaciones\",'$tipo_cobro','$cantidad','$centros_operacion','$centros_costo',$valorTotal,$placaproyecto,'$factor')";*/
		
		q("insert into $BDA.requisiciond (requisicion,tipo1,clase,valor,observaciones,tipo_cobro,cantidad,centro_operacion,centro_costo,valor_total,id_vehiculo,factor) values 
		('$idr','$tipo1','$clase','$valor',\"$observaciones\",'$tipo_cobro','$cantidad','$centros_operacion','$centros_costo','$valorTotal',$placaproyecto,'$factor')");
        
		if($Continuar) echo "<body><script language='javascript'>parent.continuar();</script></body>";
        else echo "<body><script language='javascript'>parent.cerrar();</script></body>";
}

function borrar_item_requisicion()
{
        global $BDA,$id;
        q("delete from $BDA.requisiciond where id=$id");
        echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function tipos_requisicion()
{
        global $BDA;
        html('TIPOS DE REQUISICION');
        echo "<script language='javascript'>
        function validar_nuevo_tipo()
        {
                with(document.forma)
                {
                        if(!alltrim(nombre.value)) {alert('Debe digitar el nombre del nuevo tipo.');nombre.style.backgroundColor='ffffdd';nombre.focus();return false;}
                        submit();
                }
        }
        </script><body><h3>TIPOS DE REQUISICION</h3></body>";
        $Tipos=q("select * from $BDA.requisiciont order by nombre");
        echo "<table border cellspacing='0'><tr>
                <th>Id</th>
                <th>Nombre</th>
                </tr>";
        while($T =mysql_fetch_object($Tipos ))
        {
                echo "<tr>
                <td align='center'>$T->id</td>
                <td>$T->nombre</td>
                </tr>";
        }
        echo "</table>
        <form action='zrequisicion.php' target='_self' method='POST' name='forma' id='forma'>
                Nuevo Tipo: <input type='text' name='nombre' id='nombre' value='' size='60' maxlength='250'>
                <input type='button' name='continuar' id='continuar' value=' GRABAR ' onclick='validar_nuevo_tipo();'>
                <input type='hidden' name='Acc' value='crear_nuevo_tipo_requisicion'>
        </form>
        <input type='button' name='regresar' id='regresar' value=' REGRESAR AL DETALLE DE REQUISICION ' onclick='opener.location.reload();window.close();void(null);'>";
}

function crear_nuevo_tipo_requisicion()
{
        global $nombre,$BDA;
        q("insert into $BDA.requisiciont (nombre) values ('$nombre')");
        header("location:zrequisicion.php?Acc=tipos_requisicion");
}

function cerrar_requisicion()
{
        global $id,$BDA;
        q("update $BDA.requisicion set cerrada=1,estado=1 where id=$id");
        
		$verificar=qo("select ubicacion from requisicion where id = $id");
		
		if($verificar->ubicacion == 0){
			enviar_mail_solicitud_aprobacion_administrativa();
		}else{
			enviar_mail_solicitud_aprobacion_operativa();
		}
		echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function borrar_requisicion()
{
        global $id,$BDA;
        q("delete from $BDA.requisiciond where requisicion=$id");
        q("delete from $BDA.requisicion where id=$id");
        echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function renviar_requicision_verificar(){
	global $id,$BDA;
	$verificar=qo("select ubicacion from requisicion where id = $id");
		
		if($verificar->ubicacion == 0){
			enviar_mail_solicitud_aprobacion_administrativa();
			
		}else{
			enviar_mail_solicitud_aprobacion_operativa();
			
		}
}



function enviar_mail_solicitud_aprobacion_administrativa()
{
        global $id,$BDA,$Aviso;
		$ER=qo("select  
                 centro_operacion,aseguradora.ccostos_uno as centrocosto,ubicacion.flota, requisiciond.centro_costo as centrocosto_dos 
				 from aoacol_administra.requisiciond 
                 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion.id = $id");

        $D=qo("select * from $BDA.requisicion where id=$id");
        $Prov=qo("select * from $BDA.proveedor where id=$D->proveedor");
        $Ciu=qo1("select t_ciudad('$D->ciudad')");
        echo "select * from $BDA.perfil_requisicion where id=$D->perfil";
		$Pr=qo("select * from $BDA.perfil_requisicion where id=$D->perfil");
        $Email_usuario=usuario('email');
        $Hoy=date('Y-m-d H:i:s');
		
		$Detalle=q("select provee_produc_serv.nombre as item,tipo.nombre as tipo,unidad_de_medida.nombre as unidad_medida,requisiciond.observaciones,requisiciond.cantidad,
                    requisiciond.requisicion,requisiciond.valor_total,requisiciond.valor as valor_unitario,requisiciond.factor,aoacol_aoacars.vehiculo.placa,
                    aoacol_aoacars.oficina.nombre as centrodeoperacion,requisiciond.centro_costo as centrocosto 
					from aoacol_administra.requisiciond
					inner join aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					inner join aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					inner join aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
                    LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id
                    LEFT OUTER JOIN aoacol_aoacars.oficina on requisiciond.centro_operacion = aoacol_aoacars.oficina.id
                    LEFT OUTER JOIN requisicionc on requisiciond.clase = requisicionc.id
					where requisicion =$id order by requisiciond.id");
		if($Pr->contingencia){
			$Email_aprobador=$Pr->email_aprobacion_2;
			$Nombre_aprobador=$Pr->aprobado_por_2;
			}elseif($ER->centrocosto == 411 || $ER->centro_operacion == 20 || $ER->flota == 23 ){
			$Email_aprobador = 'gabriel.sandoval@transorientesas.com';
	        $Nombre_aprobador = 'Gabriel Sandoval';
			}else{
				$Email_aprobador=$Pr->email_aprobacion;
				$Nombre_aprobador=$Pr->aprobado_por;
			}

        $Ruta_correo="utilidades/Operativo/operativo.php?id=$id&Fecha=$Hoy&Usuario=$Nombre_aprobador&eUsuario=$Email_aprobador&Solicitado_por=".$_SESSION['Nombre']."&eSolicitado_por=$Email_usuario";
        
		$Cotizaciones='';
        if($D->cotizacion_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 1 </u></a><br>";
        if($D->cotizacion2_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion2_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 2 </u></a><br>";
        if($D->cotizacion3_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion3_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 3 </u></a><br>";
        if(!$Cotizaciones) $Cotizaciones="No hay imagenes cargadas";
        $Ruta_aprobacion=base64_encode("\$Programa='$Ruta_correo&Acc=aprobar_requisicion&observaciones='.\$observaciones.'&cotapr='.\$cotapr;\$Fecha_control=date('Y-m-d');");
        $Ruta_daprobacion=base64_encode("\$Programa='$Ruta_correo&Acc=daprobar_requisicion&observaciones='.\$observaciones;\$Fecha_control=date('Y-m-d');");
        $Fecha_control=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),30)));
    
    
		$Det="<table border cellspacing='0'><tr><th>Tipo de Requisici&oacute;n</th><th>Item</th><th>Unidad de medida</th><th>Descripcion</th><th>Cantidad</th><th>Valor unitario</th><th>Valor</th><th>Proyecto placa</th><th>Centro de operacion</th><th>Centro de costo</th><th>Factor</th>";
        
		//echo "select *,t_requisiciont(tipo) as ntipo, t_requisicionc(clase) as nclase from requisiciond where requisicion=$id";
		
		
        
		while($Dt =mysql_fetch_object($Detalle))
        {
			    if($Dt->placa == '' or $Dt->placa == null){
							$placaDinamica = 'No definida';
						}else{
							$placaDinamica = $Dt->placa;
						}
						if($Dt->centro_operacion == 'NAL'){
							$centroOperativo = 'NACIONAL';
						}else{
							$centroOperativo = $Dt->centrodeoperacion;
						}
						if($Dt->factor == '' or $Dt->factor == null){
							$factorDinamico = 'Factor no definido';
						}else{
							$factorDinamico = $Dt->factor;
						}
						if($Dt->centrocosto == '' or $Dt->centrocosto == null){
							$centrocosto = 'Centro de costo no definido';
						}else{
							$centrocosto = $Dt->centrocosto;
						}
						
				$Det.="<tr><td>$Dt->tipo</td><td>$Dt->item</td><td>$Dt->unidad_medida</td><td>$Dt->observaciones</td><td>$Dt->cantidad</td><td align='right'>$".coma_format($Dt->valor_unitario)."</td><td align='right'>$".coma_format($Dt->valor_total)."</td><td>$placaDinamica</td><td>$centroOperativo</td><td>$centrocosto</td><td>$factorDinamico</td></tr>";
        }
        $Det.="</table>";
			$DV=qo("select * from aoacol_aoacars.vehiculo where placa='$D->placa'");
		$HU=qo("select ubicacion from requisicion where id = $id");
		$HT=qo("select * from aoacol_aoacars.ubicacion where id='$HU->ubicacion'");
		$li=qo("select * from aoacol_aoacars.linea_vehiculo where id='$DV->linea'");
		$Mr=qo("select * from aoacol_aoacars.marca_vehiculo where id = '$li->marca'");
		$Prov=qo("select * from $BDA.proveedor where id=$D->proveedor");
		$Res="<table border cellspacing='4'><tr><th>Resultado</th>";
        //echo "select *,t_requisiciont(tipo) as ntipo, t_requisicionc(clase) as nclase from requisiciond where requisicion=$id";
		$retorno=q("select requisiciond.requisicion,requisiciond.valor_total,
		            sum(requisiciond.valor_total) as resultado 
					from aoacol_administra.requisiciond
					where requisicion  =$id");
        while($Dt =mysql_fetch_object($retorno))
        {
           $Res.="<tr><td>$".coma_format($Dt->resultado)."</td>";
        }
        $Res.="</table>";
		 
		$Envio1=enviar_gmail($Email_usuario /*de */,
                               $_SESSION['Nombre'] /*Nombre de */ ,
								$Email_aprobador /*para */,
								"" /*con copia*/,
                                "REQUISICION NUMERO $id" /*Objeto */,
                                "<html>
								  <head>
									<meta name='viewport' content='width=device-width, initial-scale=1.0' />
									<meta name='x-apple-disable-message-reformatting' />
									<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
									<title></title>
									<style type='text/css' rel='stylesheet' media='all'>
									/* reset */

								*
								{
									border: 0;
									box-sizing: content-box;
									color: inherit;
									font-family: inherit;
									font-size: inherit;
									font-style: inherit;
									font-weight: inherit;
									line-height: inherit;
									list-style: none;
									margin: 0;
									padding: 0;
									text-decoration: none;
									vertical-align: top;
								}

								/* content editable */

								*[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

								*[contenteditable] { cursor: pointer; }

								*[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 1em 0.5em #DEF; }

								span[contenteditable] { display: inline-block; }

								/* heading */

								h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }

								/* table */

								table { font-size: 75%; table-layout: fixed; width: 100%; }
								table { border-collapse: separate; border-spacing: 2px; }
								th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
								th, td { border-radius: 0.25em; border-style: solid; }
								th { background: #EEE; border-color: #BBB; }
								td { border-color: #DDD; }

								/* page */

								html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; padding: 0.5in; }
								html { background: #fff; cursor: default; }

								body { box-sizing: border-box; height:nome; margin: 0 auto; overflow: hidden; padding: 0.5in; width:nome; }
								body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }

								/* header */

								header { margin: 0 0 3em; }
								header:after { clear: both; content: ''; display: table; }

								header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
								header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
								header address p { margin: 0 0 0.25em; }
								header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
								header img { max-height: 100%; max-width: 100%; }
								header input { cursor: pointer; -ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity=0)'; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

								/* article */

								article, article address, table.meta, table.inventory { margin: 0 0 3em; }
								article:after { clear: both; content: ''; display: table; }
								article h1 { clip: rect(0 0 0 0); position: absolute; }

								article address { float: left; font-size: 125%; font-weight: bold; }

								/* table meta & balance */

								table.meta, table.balance { float: right; width: 36%; }
								table.meta:after, table.balance:after { clear: both; content: ''; display: table; }

								/* table meta */

								table.meta th { border: 1px solid black; border-color: rgba(118,136,29,1);width: 40%; }
								table.meta td {border: 1px solid black; border-color: rgba(118,136,29,1); width: 60%; }

								/* table items */
								.titulo{
								font-size:15px;
								font-weight:900;
								color:rgba(118,136,29,1);
								}
								table.inventory { clear: both; width: 100%;border: 1px solid black; border-color: rgba(118,136,29,1); }
								table.inventory th {  background-color:rgba(118,136,29,1); font-weight: bold; text-align: center; color:#fff; }

								table.inventory td:nth-child(1) { width: 26%; }
								table.inventory td:nth-child(2) { width: 38%; }
								table.inventory td:nth-child(3) { text-align: right; width: 12%; }
								table.inventory td:nth-child(4) { text-align: right; width: 12%; }
								table.inventory td:nth-child(5) { text-align: right; width: 12%; }

								/* table balance */

								table.balance th, table.balance td { width: 50%; }
								table.balance td { text-align: right; }

								/* aside */

								aside h1 { border: none; border-width: 0 0 1px; margin: 0 0 1em; }
								aside h1 { border-color: #999; border-bottom-style: solid; }

								/* javascript */

								.add, .cut
								{
									border-width: 1px;
									display: block;
									font-size: .8rem;
									padding: 0.25em 0.5em;	
									float: left;
									text-align: center;
									width: 0.6em;
								}
									p.sub {
									  font-size: 13px;
									}
									/* Utilities ------------------------------ */
									
									.align-right {
									  text-align: right;
									}
									
									.align-left {
									  text-align: left;
									}
									
									.align-center {
									  text-align: center;
									}
									/* Buttons ------------------------------ */
									
									.button {
									  background-color: #3869D4;
									  border-top: 10px solid #3869D4;
									  border-right: 18px solid #3869D4;
									  border-bottom: 10px solid #3869D4;
									  border-left: 18px solid #3869D4;
									  display: inline-block;
									  color: #FFF;
									  text-decoration: none;
									  border-radius: 3px;
									  box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
									  -webkit-text-size-adjust: none;
									  box-sizing: border-box;
									}
									
									.button--green {
									  background-color: #22BC66;
									  border-top: 10px solid #22BC66;
									  border-right: 18px solid #22BC66;
									  border-bottom: 10px solid #22BC66;
									  border-left: 18px solid #22BC66;
									}
									
									.button--red {
									  background-color: #FF6136;
									  border-top: 10px solid #FF6136;
									  border-right: 18px solid #FF6136;
									  border-bottom: 10px solid #FF6136;
									  border-left: 18px solid #FF6136;
									}
									
									@media only screen and (max-width: 500px) {
									  .button {
										width: 100% !important;
										text-align: center !important;
									  }
									}
									/* Attribute list ------------------------------ */
									
									.attributes {
									  margin: 0 0 21px;
									}
									
									.attributes_content {
									  background-color: #F4F4F7;
									  padding: 16px;
									}
									
									.attributes_item {
									  padding: 0;
									}
									/* Related Items ------------------------------ */
									
									.related {
									  width: 100%;
									  margin: 0;
									  padding: 25px 0 0 0;
									  -premailer-width: 100%;
									  -premailer-cellpadding: 0;
									  -premailer-cellspacing: 0;
									}
									
									.related_item {
									  padding: 10px 0;
									  color: #CBCCCF;
									  font-size: 15px;
									  line-height: 18px;
									}
									
									.related_item-title {
									  display: block;
									  margin: .5em 0 0;
									}
									
									.related_item-thumb {
									  display: block;
									  padding-bottom: 10px;
									}
									
									.related_heading {
									  border-top: 1px solid #CBCCCF;
									  text-align: center;
									  padding: 25px 0 10px;
									}
									/* Discount Code ------------------------------ */
									
									.discount {
									  width: 100%;
									  margin: 0;
									  padding: 24px;
									  -premailer-width: 100%;
									  -premailer-cellpadding: 0;
									  -premailer-cellspacing: 0;
									  background-color: #F4F4F7;
									  border: 2px dashed #CBCCCF;
									}
									
									.discount_heading {
									  text-align: center;
									}
									
									.discount_body {
									  text-align: center;
									  font-size: 15px;
									}
									/* Social Icons ------------------------------ */
									
									.social {
									  width: auto;
									}
									
									.social td {
									  padding: 0;
									  width: auto;
									}
									
									.social_icon {
									  height: 20px;
									  margin: 0 8px 10px 8px;
									  padding: 0;
									}
									/* Data table ------------------------------ */
									
									.purchase {
									  width: 100%;
									  margin: 0;
									  padding: 35px 0;
									  -premailer-width: 100%;
									  -premailer-cellpadding: 0;
									  -premailer-cellspacing: 0;
									}
									
									.purchase_content {
									  width: 100%;
									  margin: 0;
									  padding: 25px 0 0 0;
									  -premailer-width: 100%;
									  -premailer-cellpadding: 0;
									  -premailer-cellspacing: 0;
									}
									
									.purchase_item {
									  padding: 10px 0;
									  color: #51545E;
									  font-size: 15px;
									  line-height: 18px;
									}
									
									.purchase_heading {
									  padding-bottom: 8px;
									  border-bottom: 1px solid #EAEAEC;
									}

									.purchase_heading p {
									  margin: 0;
									  color: #85878E;
									  font-size: 12px;
									}
									
									.purchase_footer {
									  padding-top: 15px;
									  border-top: 1px solid #EAEAEC;
									}
									
									.purchase_total {
									  margin: 0;
									  text-align: right;
									  font-weight: bold;
									  color: #333333;
									}
									
									.purchase_total--label {
									  padding: 0 15px 0 0;
									}
									
									body {
									  background-color: #F4F4F7;
									  color: #51545E;
									}
									
									p {
									  color: #51545E;
									}
									
									p.sub {
									  color: #6B6E76;
									}
									
									.email-wrapper {
									  width: 100%;
									  margin: 0;
									  padding: 0;
									  -premailer-width: 100%;
									  -premailer-cellpadding: 0;
									  -premailer-cellspacing: 0;
									  background-color: #F4F4F7;
									}
									
									.email-content {
									  width: 100%;
									  margin: 0;
									  padding: 0;
									  -premailer-width: 100%;
									  -premailer-cellpadding: 0;
									  -premailer-cellspacing: 0;
									}
									/* Masthead ----------------------- */
									
									.email-masthead {
									  padding: 25px 0;
									  text-align: center;
									}
									
									.email-masthead_logo {
									  width: 94px;
									}
									
									.email-masthead_name {
									  font-size: 16px;
									  font-weight: bold;
									  color: #A8AAAF;
									  text-decoration: none;
									  text-shadow: 0 1px 0 white;
									}
									/* Body ------------------------------ */
									
									.email-body {
									  width: 100%;
									  margin: 0;
									  padding: 0;
									  -premailer-width: 100%;
									  -premailer-cellpadding: 0;
									  -premailer-cellspacing: 0;
									  background-color: #FFFFFF;
									}
									
									.email-body_inner {
									  width: 570px;
									  margin: 0 auto;
									  padding: 0;
									  -premailer-width: 570px;
									  -premailer-cellpadding: 0;
									  -premailer-cellspacing: 0;
									  background-color: #FFFFFF;
									}
									
									.email-footer {
									  width: nome;
									  margin: 0 auto;
									  padding: 0;
									  -premailer-width: 570px;
									  -premailer-cellpadding: 0;
									  -premailer-cellspacing: 0;
									  text-align: center;
									}
									
									.email-footer p {
									  color: #6B6E76;
									}
									
									.body-action {
									  width: 100%;
									  margin: 30px auto;
									  padding: 0;
									  -premailer-width: 100%;
									  -premailer-cellpadding: 0;
									  -premailer-cellspacing: 0;
									  text-align: center;
									}
									
									.body-sub {
									  margin-top: 25px;
									  padding-top: 25px;
									  border-top: 1px solid #EAEAEC;
									}
									
									.content-cell {
									  padding: 35px;
									}
								.add, .cut
								{
									background: #9AF;
									box-shadow: 0 1px 2px rgba(0,0,0,0.2);
									background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
									background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
									border-radius: 0.5em;
									border-color: #0076A3;
									color: #FFF;
									cursor: pointer;
									font-weight: bold;
									text-shadow: 0 -1px 2px rgba(0,0,0,0.333);
								}
								input[type=text]:focus {
								  border: 3px solid #555;
								}
								.add { margin: -2.5em 0 0; }

								.add:hover { background: #00ADEE; }

								.cut { opacity: 0; position: absolute; top: 0; left: -1.5em; }
								.cut { -webkit-transition: opacity 100ms ease-in; }

								tr:hover .cut { opacity: 1; }

								@media print {
									* { -webkit-print-color-adjust: exact; }
									html { background: none; padding: 0; }
									body { box-shadow: none; margin: 0; }
									span:empty { display: none; }
									.add, .cut { display: none; }
								}

								@page { margin: 0; }
									</style>
									<!--[if mso]>
									<style type='text/css'>
									  .f-fallback  {
										font-family: Arial, sans-serif;
									  }
									</style>
								  <![endif]-->
								  </head>

										<header>
										
										   <table   style=' text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
												  <tr>
													<td    style=' text-align: center;'  class='content-cell' align='center' >
														   <img   style=' text-align: center;'  src='https://app.aoacolombia.com/Administrativo/img/banner-vehiculo-sustituto-AOA.jpg'>
													</td>
												  </tr>

												</table>
												<br>
												  <table   style=' text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
												  <tr>
													<td   style=' text-align: center;'  class='content-cell' align='center' >
													   <b  style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);' class='titulo'>Solicitud de aprobaci&oacute;n Requisici&oacute;n N&uacute;mero $id</b><br> </td>
												  </tr>

												</table>
											<address contenteditable>
												<h3 class='titulo'  style='margin-bottom: 1%;  color: 1px solid rgba(118,136,29,1);'  >Datos del solicitante</h3>
												<p>$D->fecha</p>
												<p>$D->solicitado_por</p>
												<p>$Ciu</p>
												
											</address>
											
										</header>
										<br>
										<hr>
										<hr>
										<article>
											
											<address contenteditable>
													  <p  style='color: 1px solid rgba(118,136,29,1);' class='titulo' >Requisici&oacute;n Administrativa<br></p>
											</address>
											<table class='meta'>
												<caption> <h3  style='margin-bottom: 2 %;  color: 1px solid rgba(118,136,29,1);'  class='titulo'>Datos Requisici&oacute;n</h3> </caption>
												<tr style=' border: 1px solid rgba(118,136,29,1);'>
													<th><span contenteditable> Requisici&oacute;n #</span></th>
													<td><span contenteditable>$id</span></td>
												</tr>
												<tr style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'>
													<th><span contenteditable>Provedor</span></th>
													<td><span contenteditable>$Prov->nombre</span></td>
												</tr >
												<tr style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'>
													<th><span contenteditable>fecha</span></th>
													<td><span contenteditable>$D->fecha</span></td>
												</tr>
												<tr style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'>
													<th><span contenteditable>Ciudad</span></th>
													<td><span contenteditable>$Ciu</span></td>
												</tr>
												<tr style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'>
													<th><span contenteditable>Solicitado por</span></th>
													<td><span contenteditable>$D->solicitado_por</span></td>
												</tr>
												
											</table>
											
											  
											  $Det
										</article>
											
											  $Res
										
										 <article>	
												<table   style=' margin-bottom: 3%; text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
												  <tr>
													<td   style=' text-align: center;'  class='content-cell' align='center' >
													   <b   style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);' class='titulo'>Aprobación Requisición Número $id</b><br> </td>
												  </tr>
											   </table>
													<form  style='  margin-bottom: 5%; text-align : center;  '  action='http://app.aoacolombia.com/i.php' target='_blank' method='POST' name='forma' id='forma'>
													
															Selecione un estado: 
															<select style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'  style=' text-align:  center; '  name='i'>
															<option  value=''>Selecciona una opción</option> <option  value=\"$Ruta_aprobacion\">Aprobar</option><option value=\"$Ruta_daprobacion\">Rechazar</option>
															</select>
															<br>
															Observaciones: 
															<input type='text'   style='margin-bottom: 2%; border: 1px solid rgba(118,136,29,1);'  name='observaciones' id='observaciones' value='' size='50' maxlength='200'>
															<br>
															<br>
															<input type='submit'  style=' margin-bottom: 5%;  color:#fff; background-color:rgba(118,136,29,1);  text-align: center;'it' value=' PROCEDER ' >
															<input type='hidden' name='Fecha_control' value='$Fecha_control'>
													</form>
											   
										 
											
										</article>
												

										<aside>
											   <table class='email-footer'    align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
												  <tr>
													<td   style=' text-align: center;'  class='content-cell' align='center' >
													  <p  style=' text-align: center;'  class='f-fallback sub align-center'>
														018000186262
														<br>+(571) 8837069
														<br>adquisiciones@aoasoluciones.com; 
														<br>http://www.aoacolombia.com
													  </p>
													<img   style=' text-align: center;'  class='f-fallback email-masthead_name' src='https://app.aoacolombia.com/Administrativo/img/logo-footer-mail-AOA.jpg'>
													</td>
												  </tr>

												</table>
										</aside>
									</body>
								</html>");
								
								
        echo "<body><script language='javascript'>alert('Email satisfactoriamente a $Email_aprobador');</script></body>";
}


function enviar_mail_solicitud_aprobacion_operativa() // envia un correo para aprobacion de la requisicion control operativo
{
	
	global $id,$BDA,$Aviso;
	$ER=qo("select requisicion.placa, 
				 concat( oficina.centro_operacion,' ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto,aseguradora.nombre as ASEGURADORA,ubicacion.flota,requisiciond.centro_operacion
				 from aoacol_administra.requisiciond 
				 LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
				 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 inner JOIN aoacol_aoacars.oficina on ubicacion.oficina = oficina.id
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion.id = $id");
	$D=qo("select * from $BDA.requisicion where id=$id"); // trae la información de la requisicion
	$Ciu=qo1("select t_ciudad('$D->ciudad')"); // trae la información de la ciudad
	
	$Pr=qo("select * from $BDA.perfil_requisicion where id=$D->perfil"); // trae la información del perfil que aprueba la requisición
	$Email_usuario=usuario('email'); // obtiene el email del usuario
	if(!$Email_usuario) {
	echo "<body><script language='javascript'>alert('SU SESION EN ESTE SISTEMA ESTA CAIDA, NO SE PUEDE ENVIAR EL CORREO DE SOLICITUD DE AUTORIZACION');</script></body>"; die();}
	$Hoy=date('Y-m-d H:i:s');
	$Detalle=qo("select requisiciond.requisicion,provee_produc_serv.nombre as item,tipo.nombre as tipo, unidad_de_medida.nombre as unidad_medida,
				 requisiciond.observaciones,requisiciond.cantidad,requisiciond.valor as valor_unitario, requisiciond.valor_total,requisicion.placa, 
				 concat( oficina.centro_operacion,' ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto, requisicion.fecha 
				 from aoacol_administra.requisiciond 
				 LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
				 LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id 
				 LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id 
				 LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id 
				 LEFT OUTER JOIN aoacol_administra.requisicionc on requisiciond.clase = requisicionc.id 
				 LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
				 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 inner JOIN aoacol_aoacars.oficina on ubicacion.oficina = oficina.id
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion =$id");
	if($Pr->contingencia){
		    

		$Email_aprobador=$Pr->email_aprobacion2;$Nombre_aprobador=$Pr->aprobado_por2;
		
		}elseif($ER->centrocosto == 411  || $ER->flota == 23  ||  $ER->centro_operacion == 20){
		$Email_aprobador = 'gabriel.sandoval@transorientesas.com';
	    $Nombre_aprobador = 'Gabriel Sandoval';
		}else{
			$varValidation =  $Detalle;
		 
		 if($varValidation->item == 'TRANSPORTE OPERATIVO' || 
			$varValidation->item == 'TRANSPORTES' || 
			$varValidation->item == 'PEAJE' || 
			$varValidation->item == 'LAVADO DE VEHÍCULOS' ||
			$varValidation->item == 'RESTAURANTE' ||  
			$varValidation->item == 'TANQUEO DE COMBUSTIBLE' || 
			$varValidation->item == 'RECARGA EXTINTORES' || 
			$varValidation->item == 'BOTIQUÍN VEHÍCULOS'){
							$Email_aprobador = "adquisiciones@aoasoluciones.com"; 
							$Nombre_aprobador="Aquisiciones";
						}else{
							$Email_aprobador=$Pr->email_aprobacion;
		                    $Nombre_aprobador=$Pr->aprobado_por;
						}
	    
		} // perfil estandar de aprobación
	// construye una ruta de correo para la aprobacion por el funcionario adecuado
	$Ruta_correo="utilidades/Operativo/operativo.php?id=$id&Fecha=$Hoy&Usuario=$Nombre_aprobador&eUsuario=$Email_aprobador&Solicitado_por=".$_SESSION['Nombre']."&eSolicitado_por=$Email_usuario";
	$Cotizaciones='';
	// incluye las rutas para ver las imagenes de las cotizaciones
	if($D->cotizacion_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 1 </u></a><br>";
	if($D->cotizacion2_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion2_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 2 </u></a><br>";
	if($D->cotizacion3_f) $Cotizaciones.="<a href='http://app.aoacolombia.com/i.php?i=".base64_encode("\$Programa='utilidades/Operativo/operativo.php?Acc=descargar_imagen_requisicion&img=$D->cotizacion3_f';\$Fecha_control=date('Y-m-d');")."' target='_blank'><u> Descargar Cotizacion 3 </u></a><br>";
	if(!$Cotizaciones) $Cotizaciones="No hay imagenes cargadas";
	// hay dos rutas una para aprobación y una para el rechazo
	$Ruta_aprobacion=base64_encode("\$Programa='$Ruta_correo&Acc=aprobar_requisicion&observaciones='.\$observaciones.'&cotapr='.\$cotapr;\$Fecha_control=date('Y-m-d');"); 
	$Ruta_daprobacion=base64_encode("\$Programa='$Ruta_correo&Acc=daprobar_requisicion&observaciones='.\$observaciones;\$Fecha_control=date('Y-m-d');"); 
	$Fecha_control=date('Y-m-d',strtotime(aumentadias(date('Y-m-d'),30)));
	// incluye el detalle de la requisicion
	$Det="<table border cellspacing='0'><tr><th>Tipo de Requisici&oacute;n</th><th>Item</th><th>Unidad de medida</th><th>Descripcion</th><th>Centro de operacion</th><th>Cantidad</th><th>Valor unitario</th><th>Valor</th>";
	$Detalle=q("select requisiciond.requisicion,provee_produc_serv.nombre as item,tipo.nombre as tipo, unidad_de_medida.nombre as unidad_medida,
				 requisiciond.observaciones,requisiciond.cantidad,requisiciond.valor as valor_unitario, requisiciond.valor_total,requisicion.placa, 
				 concat( oficina.centro_operacion,' ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto, requisicion.fecha 
				 from aoacol_administra.requisiciond 
				 LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
				 LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id 
				 LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id 
				 LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id 
				 LEFT OUTER JOIN aoacol_administra.requisicionc on requisiciond.clase = requisicionc.id 
				 LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
				 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 inner JOIN aoacol_aoacars.oficina on ubicacion.oficina = oficina.id
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion =$id");
	while($Dt =mysql_fetch_object($Detalle ))
	{
		$Det.="<tr><td>$Dt->tipo</td><td>$Dt->item</td><td>$Dt->unidad_medida</td><td>$Dt->observaciones</td><td>$Dt->centrodeoperacion</td><td>$Dt->cantidad</td><td align='right'>$".coma_format($Dt->valor_unitario)."</td><td align='right'>$".coma_format($Dt->valor_total)."</td></tr>";
	}
	$Det.="</table>";
	
	$Res="<table border cellspacing='4'><tr><th>Resultado</th>";
        //echo "select *,t_requisiciont(tipo) as ntipo, t_requisicionc(clase) as nclase from requisiciond where requisicion=$id";
		$retorno=q("select requisiciond.requisicion,requisiciond.valor_total,
		            sum(requisiciond.valor_total) as resultado 
					from aoacol_administra.requisiciond
					where requisicion  =$id");
        while($Dt =mysql_fetch_object($retorno))
        {
           $Res.="<tr><td>$".coma_format($Dt->resultado)."</td>";
        }
        $Res.="</table>";
		$DV=qo("select * from aoacol_aoacars.vehiculo where placa='$D->placa'");
		$HU=qo("select ubicacion from requisicion where id = $id");
		$HT=qo("select * from aoacol_aoacars.ubicacion where id='$HU->ubicacion'");
		$li=qo("select * from aoacol_aoacars.linea_vehiculo where id='$DV->linea'");
		$Mr=qo("select * from aoacol_aoacars.marca_vehiculo where id = '$li->marca'");
		$Prov=qo("select * from $BDA.proveedor where id=$D->proveedor");	
	
	//"$Email_aprobador,$Nombre_aprobador" /*para */,    "arturoquintero@aoacolombia.com,ARTURO QUINTERO",  
	$Ruta_alterna=base64_encode("header('location:../Control/operativo/zbalance_estado.php?Acc=aprobacion_requisicion&id=$id');");
	// envia el correo al funcionario que debe aprobar esa requisicion
	$Envio1=enviar_gmail($Email_usuario /*de */,
				$_SESSION['Nombre'] /*Nombre de */ ,
				$Email_aprobador /*para */,
				"" /*con copia*/,
				"REQUISICION NUMERO $id" /*Objeto*/,
				"<html> 
					  <head>
						<meta name='viewport' content='width=device-width, initial-scale=1.0' />
						<meta name='x-apple-disable-message-reformatting' />
						<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
						<title></title>
						<style type='text/css' rel='stylesheet' media='all'>
						
						/* reset */

					*
					{
						border: 0;
						box-sizing: content-box;
						color: inherit;
						font-family: inherit;
						font-size: inherit;
						font-style: inherit;
						font-weight: inherit;
						line-height: inherit;
						list-style: none;
						margin: 0;
						padding: 0;
						text-decoration: none;
						vertical-align: top;
					}

					/* content editable */

					*[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

					*[contenteditable] { cursor: pointer; }

					*[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 1em 0.5em #DEF; }

					span[contenteditable] { display: inline-block; }

					/* heading */

					h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }

					/* table */

					table { font-size: 75%; table-layout: fixed; width: 100%; }
					table { border-collapse: separate; border-spacing: 2px; }
					th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
					th, td { border-radius: 0.25em; border-style: solid; }
					th { background: #EEE; border-color: #BBB; }
					td { border-color: #DDD; }

					/* page */

					html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; padding: 0.5in; }
					html { background: #fff; cursor: default; }

					body { box-sizing: border-box; height:nome; margin: 0 auto; overflow: hidden; padding: 0.5in; width:nome; }
					body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }

					/* header */

					header { margin: 0 0 3em; }
					header:after { clear: both; content: ''; display: table; }

					header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
					header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
					header address p { margin: 0 0 0.25em; }
					header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
					header img { max-height: 100%; max-width: 100%; }
					header input { cursor: pointer; -ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity=0)'; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

					/* article */

					article, article address, table.meta, table.inventory { margin: 0 0 3em; }
					article:after { clear: both; content: ''; display: table; }
					article h1 { clip: rect(0 0 0 0); position: absolute; }

					article address { float: left; font-size: 125%; font-weight: bold; }

					/* table meta & balance */

					table.meta, table.balance { float: right; width: 36%; }
					table.meta:after, table.balance:after { clear: both; content: ''; display: table; }

					/* table meta */

					table.meta th { border: 1px solid black; border-color: rgba(118,136,29,1);width: 40%; }
					table.meta td {border: 1px solid black; border-color: rgba(118,136,29,1); width: 60%; }

					/* table items */
					.titulo{
					font-size:15px;
					font-weight:900;
					color:rgba(118,136,29,1);
					}
					table.inventory { clear: both; width: 100%;border: 1px solid black; border-color: rgba(118,136,29,1); }
					table.inventory th {  background-color:rgba(118,136,29,1); font-weight: bold; text-align: center; color:#fff; }

					table.inventory td:nth-child(1) { width: 26%; }
					table.inventory td:nth-child(2) { width: 38%; }
					table.inventory td:nth-child(3) { text-align: right; width: 12%; }
					table.inventory td:nth-child(4) { text-align: right; width: 12%; }
					table.inventory td:nth-child(5) { text-align: right; width: 12%; }

					/* table balance */

					table.balance th, table.balance td { width: 50%; }
					table.balance td { text-align: right; }

					/* aside */

					aside h1 { border: none; border-width: 0 0 1px; margin: 0 0 1em; }
					aside h1 { border-color: #999; border-bottom-style: solid; }

					/* javascript */

					.add, .cut
					{
						border-width: 1px;
						display: block;
						font-size: .8rem;
						padding: 0.25em 0.5em;	
						float: left;
						text-align: center;
						width: 0.6em;
					}
						p.sub {
						  font-size: 13px;
						}
						/* Utilities ------------------------------ */
						
						.align-right {
						  text-align: right;
						}
						
						.align-left {
						  text-align: left;
						}
						
						.align-center {
						  text-align: center;
						}
						/* Buttons ------------------------------ */
						
						.button {
						  background-color: #3869D4;
						  border-top: 10px solid #3869D4;
						  border-right: 18px solid #3869D4;
						  border-bottom: 10px solid #3869D4;
						  border-left: 18px solid #3869D4;
						  display: inline-block;
						  color: #FFF;
						  text-decoration: none;
						  border-radius: 3px;
						  box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
						  -webkit-text-size-adjust: none;
						  box-sizing: border-box;
						}
						
						.button--green {
						  background-color: #22BC66;
						  border-top: 10px solid #22BC66;
						  border-right: 18px solid #22BC66;
						  border-bottom: 10px solid #22BC66;
						  border-left: 18px solid #22BC66;
						}
						
						.button--red {
						  background-color: #FF6136;
						  border-top: 10px solid #FF6136;
						  border-right: 18px solid #FF6136;
						  border-bottom: 10px solid #FF6136;
						  border-left: 18px solid #FF6136;
						}
						
						@media only screen and (max-width: 500px) {
						  .button {
							width: 100% !important;
							text-align: center !important;
						  }
						}
						/* Attribute list ------------------------------ */
						
						.attributes {
						  margin: 0 0 21px;
						}
						
						.attributes_content {
						  background-color: #F4F4F7;
						  padding: 16px;
						}
						
						.attributes_item {
						  padding: 0;
						}
						/* Related Items ------------------------------ */
						
						.related {
						  width: 100%;
						  margin: 0;
						  padding: 25px 0 0 0;
						  -premailer-width: 100%;
						  -premailer-cellpadding: 0;
						  -premailer-cellspacing: 0;
						}
						
						.related_item {
						  padding: 10px 0;
						  color: #CBCCCF;
						  font-size: 15px;
						  line-height: 18px;
						}
						
						.related_item-title {
						  display: block;
						  margin: .5em 0 0;
						}
						
						.related_item-thumb {
						  display: block;
						  padding-bottom: 10px;
						}
						
						.related_heading {
						  border-top: 1px solid #CBCCCF;
						  text-align: center;
						  padding: 25px 0 10px;
						}
						/* Discount Code ------------------------------ */
						
						.discount {
						  width: 100%;
						  margin: 0;
						  padding: 24px;
						  -premailer-width: 100%;
						  -premailer-cellpadding: 0;
						  -premailer-cellspacing: 0;
						  background-color: #F4F4F7;
						  border: 2px dashed #CBCCCF;
						}
						
						.discount_heading {
						  text-align: center;
						}
						
						.discount_body {
						  text-align: center;
						  font-size: 15px;
						}
						/* Social Icons ------------------------------ */
						
						.social {
						  width: auto;
						}
						
						.social td {
						  padding: 0;
						  width: auto;
						}
						
						.social_icon {
						  height: 20px;
						  margin: 0 8px 10px 8px;
						  padding: 0;
						}
						/* Data table ------------------------------ */
						
						.purchase {
						  width: 100%;
						  margin: 0;
						  padding: 35px 0;
						  -premailer-width: 100%;
						  -premailer-cellpadding: 0;
						  -premailer-cellspacing: 0;
						}
						
						.purchase_content {
						  width: 100%;
						  margin: 0;
						  padding: 25px 0 0 0;
						  -premailer-width: 100%;
						  -premailer-cellpadding: 0;
						  -premailer-cellspacing: 0;
						}
						
						.purchase_item {
						  padding: 10px 0;
						  color: #51545E;
						  font-size: 15px;
						  line-height: 18px;
						}
						
						.purchase_heading {
						  padding-bottom: 8px;
						  border-bottom: 1px solid #EAEAEC;
						}

						.purchase_heading p {
						  margin: 0;
						  color: #85878E;
						  font-size: 12px;
						}
						
						.purchase_footer {
						  padding-top: 15px;
						  border-top: 1px solid #EAEAEC;
						}
						
						.purchase_total {
						  margin: 0;
						  text-align: right;
						  font-weight: bold;
						  color: #333333;
						}
						
						.purchase_total--label {
						  padding: 0 15px 0 0;
						}
						
						body {
						  background-color: #F4F4F7;
						  color: #51545E;
						}
						
						p {
						  color: #51545E;
						}
						
						p.sub {
						  color: #6B6E76;
						}
						
						.email-wrapper {
						  width: 100%;
						  margin: 0;
						  padding: 0;
						  -premailer-width: 100%;
						  -premailer-cellpadding: 0;
						  -premailer-cellspacing: 0;
						  background-color: #F4F4F7;
						}
						
						.email-content {
						  width: 100%;
						  margin: 0;
						  padding: 0;
						  -premailer-width: 100%;
						  -premailer-cellpadding: 0;
						  -premailer-cellspacing: 0;
						}
						/* Masthead ----------------------- */
						
						.email-masthead {
						  padding: 25px 0;
						  text-align: center;
						}
						
						.email-masthead_logo {
						  width: 94px;
						}
						
						.email-masthead_name {
						  font-size: 16px;
						  font-weight: bold;
						  color: #A8AAAF;
						  text-decoration: none;
						  text-shadow: 0 1px 0 white;
						}
						/* Body ------------------------------ */
						
						.email-body {
						  width: 100%;
						  margin: 0;
						  padding: 0;
						  -premailer-width: 100%;
						  -premailer-cellpadding: 0;
						  -premailer-cellspacing: 0;
						  background-color: #FFFFFF;
						}
						
						.email-body_inner {
						  width: 570px;
						  margin: 0 auto;
						  padding: 0;
						  -premailer-width: 570px;
						  -premailer-cellpadding: 0;
						  -premailer-cellspacing: 0;
						  background-color: #FFFFFF;
						}
						
						.email-footer {
						  width: nome;
						  margin: 0 auto;
						  padding: 0;
						  -premailer-width: 570px;
						  -premailer-cellpadding: 0;
						  -premailer-cellspacing: 0;
						  text-align: center;
						}
						
						.email-footer p {
						  color: #6B6E76;
						}
						
						.body-action {
						  width: 100%;
						  margin: 30px auto;
						  padding: 0;
						  -premailer-width: 100%;
						  -premailer-cellpadding: 0;
						  -premailer-cellspacing: 0;
						  text-align: center;
						}
						
						.body-sub {
						  margin-top: 25px;
						  padding-top: 25px;
						  border-top: 1px solid #EAEAEC;
						}
						
						.content-cell {
						  padding: 35px;
						}
					.add, .cut
					{
						background: #9AF;
						box-shadow: 0 1px 2px rgba(0,0,0,0.2);
						background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
						background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
						border-radius: 0.5em;
						border-color: #0076A3;
						color: #FFF;
						cursor: pointer;
						font-weight: bold;
						text-shadow: 0 -1px 2px rgba(0,0,0,0.333);
					}
					input[type=text]:focus {
					  border: 3px solid #555;
					}
					.add { margin: -2.5em 0 0; }

					.add:hover { background: #00ADEE; }

					.cut { opacity: 0; position: absolute; top: 0; left: -1.5em; }
					.cut { -webkit-transition: opacity 100ms ease-in; }

					tr:hover .cut { opacity: 1; }

					@media print {
						* { -webkit-print-color-adjust: exact; }
						html { background: none; padding: 0; }
						body { box-shadow: none; margin: 0; }
						span:empty { display: none; }
						.add, .cut { display: none; }
					}

					@page { margin: 0; }
						</style>
						<!--[if mso]>
						<style type='text/css'>
						  .f-fallback  {
							font-family: Arial, sans-serif;
						  }
						</style>
					  <![endif]-->
					  </head>

							<header>
							
							   <table   style=' text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
									  <tr>
										<td    style=' text-align: center;'  class='content-cell' align='center' >
											   <img   style=' text-align: center;'  src='https://app.aoacolombia.com/Administrativo/img/banner-vehiculo-sustituto-AOA.jpg'>
										</td>
									  </tr>

									</table>
									<br>
									  <table   style=' text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
									  <tr>
										<td   style=' text-align: center;'  class='content-cell' align='center' >
										   <b  style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);' class='titulo'>Solicitud de aprobación Requisición Número $id</b><br> </td>
									  </tr>

									</table>
								<address  style='margin-bottom: 1%; ' contenteditable>
									<h3 class='titulo'  style='margin-bottom: 2%;  color: 1px solid rgba(118,136,29,1);'  >Datos del solicitante</h3>
									<p>Fecha : &nbsp;$D->fecha</p>
									<p> Solicitado por  :  &nbsp; $D->solicitado_por</p>
									<p style='margin-bottom: 5%; ' > Ciudad :  &nbsp; $Ciu</p>
									
									<h3 class='titulo'  style='margin-bottom: 1%;  color: 1px solid rgba(118,136,29,1);'  >Datos del vehiculo</h3>
									<p>Placa : &nbsp; $D->placa</p>
									<p> Modelo  :  &nbsp; $DV->modelo</p>
									<p>Marca : &nbsp;  $Mr->nombre</p>
									<p>Linea : &nbsp;  $li->nombre</p>
									<p>Fecha inicial : &nbsp;  $HT->fecha_inicial</p>
									<p>Kilomentraje inicial :  &nbsp; $HT->odometro_inicial</p>
								</address>
								
							</header>
							<br>
							<hr>
							<hr>
							<article>
								
								<address contenteditable>
										  <p  style='color: 1px solid rgba(118,136,29,1);' class='titulo' >Requisición Operativa<br></p>
								</address>
								<table class='meta'>
									<caption> <h3  style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);'  class='titulo'>Datos Requisición</h3> </caption>
									<tr style=' border: 1px solid rgba(118,136,29,1);'>
										<th><span contenteditable> Requisición #</span></th>
										<td><span contenteditable>$id</span></td>
									</tr>
									<tr style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'>
										<th><span contenteditable>Provedor</span></th>
										<td><span contenteditable>$Prov->nombre</span></td>
									</tr >
									<tr style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'>
										<th><span contenteditable>fecha</span></th>
										<td><span contenteditable>$D->fecha</span></td>
									</tr>
									<tr style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'>
										<th><span contenteditable>Ciudad</span></th>
										<td><span contenteditable>$Ciu</span></td>
									</tr>
									<tr style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'>
										<th><span contenteditable>Solicitado por</span></th>
										<td><span contenteditable>$D->solicitado_por</span></td>
									</tr>
									
								</table>
								
								  
								  $Det
							</article>
								
								  $Res
							
							 <article>	
									<table   style=' margin-bottom: 2%; text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
									  <tr>
										<td   style=' text-align: center;'  class='content-cell' align='center' >
										   <b   style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);' class='titulo'>Aprobación Requisición Número $id</b><br> </td>
									  </tr>
								   </table>
										<form  style='  margin-bottom: 5%; text-align : center;  '  action='http://app.aoacolombia.com/i.php' target='_blank' method='POST' name='forma' id='forma'>
										
												Selecione un estado: 
												<select style='margin-bottom: 2%;  border: 1px solid rgba(118,136,29,1);'  style=' text-align:  center; '  name='i'>
												<option  value=\"$Ruta_aprobacion\">Aprobar</option><option value=\"$Ruta_daprobacion\">Rechazar</option>
												</select>
												<br>
												Observaciones: 
												<input type='text'   style='margin-bottom: 2%; border: 1px solid rgba(118,136,29,1);'  name='observaciones' id='observaciones' value='' size='50' maxlength='200'>
												<br>
												<br>
												<input type='submit'  style=' margin-bottom: 5%;  color:#fff; background-color:rgba(118,136,29,1);  text-align: center;'it' value=' PROCEDER ' >
												<input type='hidden' name='Fecha_control' value='$Fecha_control'>
										</form>
								   
							 
								
							</article>
									

							<aside>
								   <table class='email-footer'    align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
									  <tr>
										<td   style=' text-align: center;'  class='content-cell' align='center' >
										  <p  style=' text-align: center;'  class='f-fallback sub align-center'>
											018000186262
											<br>+(571) 8837069
											<br>adquisiciones@aoasoluciones.com
											<br>http://www.aoacolombia.com
										  </p>
										<img   style=' text-align: center;'  class='f-fallback email-masthead_name' src='https://app.aoacolombia.com/Administrativo/img/logo-footer-mail-AOA.jpg'>
										</td>
									  </tr>

									</table>
							</aside>
						</body>
					</html>",
				"c:/temp/test.php", "test.ph");
				
    echo "<body> <script language='javascript'>alert('Email enviado satisfactoriamente a $Email_aprobador');</script></body>";
   
}

function solicitar_nuevo_bien_servicio()
{
$retorno=q("select * from sistema");while($Dt =mysql_fetch_object($retorno)){$Res.= "<option value='$Dt->nombre'>$Dt->nombre</option>";}
		html('SOLICITUD DE CREACION BIEN-SERVICIO');
        echo "<script language='javascript'>
                function enviar_solicitud()
                {
                        with(document.forma)
                        {
                                if(!tipo.value) {alert('Debe seleccionar si es un bien o un servicio');tipo.style.backgroundColor='ffffaa';return false;}
                                if(!alltrim(nombre.value)) {alert('Debe escribir un nombre'); nombre.style.backgroundColor='ffffaa'; return false;}
                                submit();
                        }
                }
                </script><body><h3>SOLICITUD DE CREACION DE BIEN O SERVICIO</h3>
				
				<style>
				.alin{
					display: flex;
                    /*justify-content: space-evenly;*/
                    align-items: center;
					
				}
				.aliniar{
					
                    /*justify-content: space-evenly;*/
                    width: 100%;
					display: flex;
					align-items: center;
					
				}
				.aliniar-select{
					
				}
				.boton_continuar{
					display: flex;
                    justify-content: center;
					
				}
				</style>
                <form action='' target='_self' method='POST' name='forma' id='forma'>
                        <div class='alin'>
						Tipo : <select name='tipo'><option value=''></option>
                                        <option value='B'>BIEN</option>
                                        <option value='S'>SERVICIO</option></select><br><br>
                        
						
						<p>Nombre:</p> <input type='text' name='nombre' id='nombre' value='' size='80' maxlength='200' onkeyup='this.value=this.value.toUpperCase();'><br><br>
						</div>
						<div class='aliniar'>
						<label>Descripcion de su solicitud:</label> <textarea type='textarea' rows='8' cols='90' name='descricion_nuevo' id='descricion_nuevo' value='' size='80' maxlength='200'></textarea><br><br>
						</div>
						<div class='centrar'>
						
						<div class='aliniar-select'>
						<p>Frecuancia de compra:</p>  
						<select name='frecuencia_compra' id='frecuencia_compra'>
						<option value='DIARIO'>DIARIO</option>
						<option value='MENSUAL'>MENSUAL</option>
						<option value='ANUAL'>ANUAL</option>
						</select>
						</div>
						<div class='aliniar-select'>
						<p>Uso:</p>  
						<select name='uso' id='uso'>
						<option value='OPERATIVO'>OPERATIVO</option>
						<option value='ADMINISTRATIVO'>ADMINISTRATIVO</option>
						<option value='COMUN'>COMUN</option>
						<option value='OPERACIONES'>OPERACIONES</option>
						</select>
						</div>
						<div class='aliniar-select'>
						<p>Sistema:</p>  
					    <select name='sistema' id='sistema'>
						$Res
						</select>
						</div>
						<div class='aliniar-select'>
						<p>Unidad de medida:</p>  
						<select name='unidad_medida' id='unidad_medida'>
						<option value='UD-UNIDAD'>UD-UNIDAD</option>
						<option value='CUARTO-GALON'>CUARTO-GALON</option>
						</select>
						</div>
						</div>
                        <div class='boton_continuar'>
						<input type='button' name='continuar' id='continuar' value=' CONTINUAR ' onclick='enviar_solicitud();'>
						</div>
						
                        
						<input type='hidden' name='Acc' value='solicitar_nuevo_bien_servicio_ok'>
                </form></body>";
}

function solicitar_nuevo_bien_servicio_ok()
{
        global $NUSUARIO,$tipo,$nombre,$descricion_nuevo,$frecuencia_compra,$uso,$sistema,$unidad_medida;
        $Email_usuario=usuario('email');
        if($tipo=='B') $Ntipo='BIEN';
        if($tipo=='S') $Ntipo='SERVICIO';
        if(enviar_gmail($Email_usuario,$NUSUARIO,
		'claudiacastro@aoacolombia.com,CLAUDIA CASTRO',
		
		'dirop@aoacolombia.com','SOLICITUD DE CREACION DE BIEN O SERVICIO',
        nl2br("Estimada Señora Claudia Castro,
        Reciba cordial saludo.

        Por medio del presente correo solicito el favor de crear el siguiente item dentro de la tabla de bienes y servicios:

        Tipo: $Ntipo
        Nombre: $nombre
		Descripcion: $descricion_nuevo
		Frecuencia de compra: $frecuencia_compra
		Uso: $uso
		Sistema: $sistema
		Unidad de medida: $unidad_medida

        Cordialmente,

        $NUSUARIO
        $Email_usuario
        ")))
        echo "<body><script language='javascript'>alert('Solicitud enviada satisfactoriamente'); window.close();void(null);</script></body>";
}

function evaluar_requisicion()
{
        global $id;
        $Req=qo("select * from requisicion where id=$id");
        $Proveedor=qo("select * from proveedor where id=$Req->proveedor");
        $Criterios_evaluacion=q("select * from prov_criterio_eval order by id");
        html('EVALUAR REQUISICION $id');
        echo "<script language='javascript'>
                A_criterio=new Array();
                Calificaciones=new Array();
                function cambio_opcion(criterio,opcion)
                {
                        document.getElementById('cal_'+criterio).value=A_criterio[criterio]['opciones'][opcion];
                        Calificaciones[criterio]['opcion']=opcion;
                        Calificaciones[criterio]['calificacion']=A_criterio[criterio]['opciones'][opcion];
                }
                function valida_envio()
                {
                        with(document.forma)
                        {
                                calificaciones.value='';
                                for(indice in Calificaciones)
                                {
                                        calificaciones.value+=indice+'|'+Calificaciones[indice]['opcion']+'|'+Calificaciones[indice]['calificacion']+',';
                                }
                                submit();
                        }

                }
        </script><body>
        <h3>EVALUACION DE REQUISICION</H3>
        <h4>Proveedor: $Proveedor->nombre</h4>
        <form action='zrequisicion.php' target='_self' method='POST' name='forma' id='forma'>
                <table><tr><th>Criterio</th><th>Opción</th><th>Comentario</th><th>Resultado</th></tr>";
                include('inc/link.php');
                $Js='';
                while($Cev=mysql_fetch_object($Criterios_evaluacion))
                {
                        $Js.="
                        A_criterio[$Cev->id]=new Array();A_criterio[$Cev->id]['nombre']='$Cev->nombre';
                        Calificaciones[$Cev->id]=new Array();";
                        echo "<tr><td>$Cev->nombre</td><td>";
						
						$Opciones_criterio=mysql_query("select id,nombre,calificacion from prov_rangos_eval where criterio=$Cev->id");
                        if(mysql_num_rows($Opciones_criterio))
                        {
                                echo "<select name='opcion_calificacion' style='width:200px' onchange='cambio_opcion($Cev->id,this.value);'><option value=''></option>";
                                $Js.="
                                A_criterio[$Cev->id]['opciones']=new Array();   ";
                                while($Op=mysql_fetch_object($Opciones_criterio))
                                {
                                        echo "<option value='$Op->id'>$Op->nombre</option>";
                                        $Js.="
                                        A_criterio[$Cev->id]['opciones'][$Op->id]=$Op->calificacion; ";
                                }
                                echo "</select>";
                        }
                        else
                        {
                                echo "<b style='color:red'>No tiene opciones</b>";
                        }
                        echo "</td><td><input type='text' name='comentario' id='comentario'></td><td align='center'><input type='text' name='cal_$Cev->id' id='cal_$Cev->id' value='' class='numero' size='2' maxlength='3' readonly></td></tr>";
                }
                echo "<script language='javascript'>$Js</script>
                </table>
				<br><center><input type='button' name='continuar' id='continuar' value=' GRABAR LA EVALUACION ' style='font-size:18px;font-weight:bold;height:40px;width:400px'
                        onclick='valida_envio();'></center>
                        <input type='hidden' name='Acc' value='evaluar_requisicion_ok'>
                        <input type='hidden' name='id' value='$id'>
                        <input type='hidden' name='calificaciones' value=''>
                        ";
                mysql_close($LINK);
        echo "</form></body>";
}

function evaluar_requisicion_ok()
{
	    global $id; // id de la requisicion
		//$comentario_evaluacion variable comentariada por solicitud
        global $calificaciones,$NUSUARIO,$comentario;
		$Ahora=date('Y-m-d H:i:s');
        //html();
        //echo "Calificaciones: $calificaciones ";
		
        $Criterios=explode(',',$calificaciones);
		$Total_calificacion=0;		
		
		foreach($Criterios as $Criterio)
        {
                if(strlen($Criterio))
                {
                        $Partes=explode('|',$Criterio);
                        $criterio=$Partes[0];$opcion=$Partes[1];$calificacion=$Partes[2];
                        //echo "<br>Criterio: $criterio opcion: $opcion calificacion: $calificacion";
                        q("insert ignore into prov_detalle_evaluacion (requisicion,criterio) values ('$id','$criterio')");
                        
						q("update prov_detalle_evaluacion set opcion='$opcion',calificacion='$calificacion', observaciones = '$comentario' where requisicion='$id' and criterio='$criterio' ");
						$Total_calificacion+=$calificacion;
                }
        }
		
        q("update requisicion set estado=4,evaluada_por='$NUSUARIO',fecha_evaluacion='$Ahora',calificacion='$Total_calificacion' where id=$id ");
        
		$Req=qo("select * from requisicion where id=$id");
		$Calificacion=qo("select opcion from prov_detalle_evaluacion where requisicion = $id and  opcion = 9 order by id desc");
		$Observaciones=qo("select observaciones from prov_detalle_evaluacion where requisicion = $id and  opcion = 9 order by id desc");
		
		if($Calificacion->opcion == 9){
			$varCalificacion = "Si";
		}else{
			$varCalificacion = "No";
		}
		
        $Proveedor=qo("select * from proveedor where id=$Req->proveedor");
	    
		header('Content-Type: text/html; charset=utf-8');
		ob_start();	
		include("correosHtml/correoEncuesta.php");
		$buffer = ob_get_clean();
		
		$Envio3 = enviar_gmail("sistemas@aoacolombia.com" /*de */,
					"Evaluacion de proveedor" /*Nombre de */ ,
					"$Proveedor->email" /*para */,
					"adquisiciones@aoasoluciones.com" /*con copia*/,
					"AOA COLOMBIA S.A. - EVALUACION DE PROVEEDOR" /*Objeto */,
					utf8_decode($buffer));
		
		
		graba_bitacora('requisicion','M',$id,'Evalua requisición');
        echo "<body><script language='javascript'>alert('Evaluacion guardada satisfactoriamente. enviada a $Proveedor->email');window.close();void(null);opener.recargar();</script></body>";
}

function crear_proveedor_abrev()
{
	//print_r($_POST);
	$query = qo("Select * from aoacol_administra.proveedor where identificacion = '".$_POST["identificacion"]."' ");
	if($query == null)
	{
		$sql = "Insert into aoacol_administra.proveedor (nombre,td,identificacion,sexo,representante_legal,cedula_rep_legal,ciudad,telefono1,celular,email,tipo_gasto_proveedor,nivel_criticidad,creado_abreviado)
		values  ('".$_POST['nombre']."','".$_POST['td']."','".$_POST['identificacion']."','".$_POST['sexo']."','".$_POST['representante_legal']."','".$_POST['cedula_rep_legal']."',
		'".$_POST['ciudad']."','".$_POST['telefono1']."','".$_POST['celular']."','".$_POST['email']."','".$_POST['tipo_gasto_proveedor']."','".$_POST['nivel_criticidad']."',1) ";
		
		$result = q($sql);
		
		$data = qo("Select * from aoacol_administra.proveedor where identificacion = ".$_POST['identificacion']);
		
		$response = array("message"=>"Proveedor creado","id"=>$data->id,"status"=>"OK");
	}
	else{
		$response = array("message"=>"Ya existe un proveedor similar en el sistema","status"=>"none");
	}
	
	echo json_encode($response);
}

function return_proveedores()
{
	$proveedores = array();
	$query = q("Select * from aoacol_administra.proveedor where calificacion_actual != 'N'  order by nombre");
	while($row = mysql_fetch_object($query))
	{
		array_push($proveedores,$row);
	}
	
	echo json_encode($proveedores);
	
}

function get_data_proveedor()
{
	$sql = "Select prov.* , selecc.nota from aoacol_administra.proveedor as prov inner join provee_seleccion as selecc on prov.id = selecc.proveedor
	where prov.id = ".$_POST["id"];
	$proveedor = qo($sql);
	$response = array("proveedor"=>$proveedor);
    echo json_encode($response);	
}

function check_eval_proveedor()
{
	$sql = "select * from aoacol_administra.provee_seleccion where proveedor = ".$_POST["id"]." order by id desc";
	$evaluation = qo($sql);
	if($evaluation != null)
	{
		$response = array("status"=>"OK","evaluacion"=>$evaluation);
	}
	else{$response = array("status"=>"NOT FOUND");}
	
	echo json_encode($response);
}

function activate_proveedor()
{
	$sql = "Update proveedor SET activo = 1 , causal_inactivacion = null where id = ".$_POST["id"];
	q($sql);
}

function ver_boton_excel(){
	global $id,$BDA;
	$verificar=qo("select ubicacion from requisicion where id = $id");
		if($verificar->ubicacion == 0){
			echo '
			<style type="text/css">
			.ocultar{
				display:block;
			}
			</style>
			';
		}else{
			echo '
			<style type="text/css">
			.ocultar{
				display:none;
			}
			</style>
			';
		}
		
}

function ver_requisicion()
{
	header('Content-Type: text/html; charset=utf-8');
        global $id,$BDA,$USER;
		
		
		
		$NT_req=tu('requisicion','id');
        $Proveedor=false;
		
			echo '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="inc/js/funciones.js"></script>		
		';
	
		echo "<div class='container'>";
				
        $sql = "select * from $DBA.requisicion where id=$id";
		//echo $sql;
		
		$Req=qo($sql);
		
		if($Req == null)
		{
			echo "No existe la requisición";
			exit;
		}
		
		
		
        if($Req->proveedor) $Proveedor=qo("select * from $DBA.proveedor where id='$Req->proveedor' ");
        $Ciudad=qo1("select t_ciudad('$Req->ciudad')");
		
		$Estado=qo("select * from $DBA.estado_requisicion where id=$Req->estado");
		if($Req->factura_referencia ) $Facref=qo1("select id from aoacol_aoacars.factura where consecutivo='$Req->factura_referencia' ");
        html();
        echo "<script language='javascript'>
                function cerrar()       {parent.cerrar_vista_requisicion();}
                function cambia_tipo1(id_detalle)
                {var T1=document.getElementById('tipo1_'+id_detalle);
                        if(T1.value)    {
                                if(confirm('Desea ajustar el bien/servicio de este item?'))
                                window.open('zrequisicion.php?Acc=ajusta_bs&id='+id_detalle+'&dato='+T1.value,'Oculto_detallerq');
                                else T1.value='';}
                        return true;}
                function recargar(){ window.open('zrequisicion.php?Acc=ver_requisicion&id=$id','_self');}
                function crear_nuevo_proveedor()        {modal('zproveedor.php?Acc=adicion_de_proveedor','crear_nuevo_proveedor');}
                function realizar_calificacion() {window.open('zproveedor.php?Acc=realizar_calificacion&refrescar_opener=$id','_self');}
                function valida_cambio_proveedor()
                {if(document.getElementById('proveedor').value)  {if(confirm('Desea asignar este proveedor a la requisición?'))
                                {window.open('zrequisicion.php?Acc=ajusta_prov&id_requisicion=$id&id_proveedor='+document.getElementById('proveedor').value,'Oculto_detallerq');}}}
				function cambia_tipo_cobro(id)
				{if(document.getElementById('tipo_cobro_'+id).value){if(confirm('Desea asignar este tipo de cobro al item?'))
						{window.open('zrequisicion.php?Acc=ajusta_tipo_cobro&id_detalle='+id+'&opcion='+document.getElementById('tipo_cobro_'+id).value,'Oculto_detallerq');}}}
				function evaluar_requisicion(){modal('zrequisicion.php?Acc=evaluar_requisicion&id=$id',0,0,500,500,'evrq');}
				function adicionar_detalle(){modal('zrequisicion.php?Acc=adicionar_detaller&idr=$id',0,0,500,500,'adrqd');}
				function modificar_item(idItem,idRequi){
					modal('zrequisicion.php?Acc=modificar_item_funcion&idRequi='+idRequi+'&idr='+idItem,0,0,500,500,'adrqd');
					}
				function subir_excel(){modal('zrequisicion.php?Acc=subir_excel&idr=$id',0,0,500,500,'adrqd');}
				function borrar_item_requisicion(id) { if(confirm('Desea eliminar el item de esta requisicion?')) { window.open('zrequisicion.php?Acc=borrar_item_requisicion&id='+id,'Oculto_detallerq'); } }
				function cerrar_requisicion(id) { if(confirm('Desea cerrar la requisicion?')) { window.open('zrequisicion.php?Acc=cerrar_requisicion&id='+id,'Oculto_detallerq'); } }
				function borrar_requisicion(id) { if(confirm('Desea borrar la requisicion?')) { window.open('zrequisicion.php?Acc=borrar_requisicion&id='+id,'Oculto_detallerq'); } }
				function re_enviar_solicitud_aprobacion(id) { window.open('zrequisicion.php?Acc=renviar_requicision_verificar&id='+id+'&Aviso=1','Oculto_detallerq'); }
				function asociar(id) {modal('zrequisicion.php?Acc=asociar_factura&iddr='+id,0,0,600,900,'asociacion');}
				function cambio_consuno(id,valor) { if(confirm('Desea cambiar el consecutivo de Sistema Uno para este item?'))	{window.open('zrequisicion.php?Acc=asigna_consecutivo_suno&id='+id+'&valor='+valor,'Oculto_detallerq');}}
				function cambio_consprv(id,valor) { if(confirm('Desea cambiar el consecutivo del proveedor para este item?'))	{window.open('zrequisicion.php?Acc=asigna_consecutivo_prov&id='+id+'&valor='+valor,'Oculto_detallerq');}}
				function asigna_sede(id) {window.open('zrequisicion.php?Acc=asigna_sede&sede='+id+'&id=$id','Oculto_detallerq');}
				function re_enviar_aprobacion(id) {window.open('zrequisicion.php?Acc=re_envio_requisicion_aprobada&id='+id,'Oculto_detallerq');}
				function adicionar_observaciones(){modal('zrequisicion.php?Acc=adicionar_observaciones&id=$id',0,0,100,100,'adobs');}
        </script>
        <BODY bgcolor='ccccdd'>
                <table width='100%'>
					<tr>
						<td>
							<h3>REQUISICION NUMERO $id</h3></td>
						<td width='5'>
							<a class='rinfo' onclick='cerrar()'><img src='gifs/standar/Cancel.png'><span>Cerrar</span></a>
						</td>
					</tr>
				</table>
				<table cellspacing='0' cellpadding='0' width='100%'>
					<tr>
						<td style='width:50%;'>
							<table border bgcolor='ffffff' cellspacing='0' width='100%'>
								<tr><td align='right'>Fecha de ".utf8_encode("Requisición:")."</td><td><b>$Req->fecha</b></td></tr>
								<tr><td align='right'>Solicitado por:</td><td><b>$Req->solicitado_por</b></td></tr>
								<tr><td align='right'>Placa:</td><td><b>$Req->placa</b></td></tr>";
		
		if(!$Facref){				
				$sql = "Select * from aoacol_administra.facturas_venta_requisicion where requisicion = $Req->id";
				
				
				$result = q($sql);
				$facturas_req = array();
				while($fila = mysql_fetch_object($result))
				{
					
					
					array_push($facturas_req,$fila);
				}
				
		}
		
		
		if($Req->factura_referencia || $facturas_req)
		{
			echo "<tr><td align='right'>Factura Referencia:</td><td><b>$Req->factura_referencia</b> ";
			foreach($facturas_req as $fac_req)
				{
					
					echo "<br><b>$fac_req->factura_referencia</b>";
					
					$Factu=qo1("select id from aoacol_aoacars.factura where consecutivo='$fac_req->factura_referencia' ");
					echo " <a style='cursor:pointer;' onclick=\"modal('../Control/operativo/zfacturacion.php?Acc=imprimir_factura&id=$Factu',0,0,700,700,'vf');\"><img src='gifs/standar/Preview.png' title='Ver Factura'></a>";	
				}
			
			//else { echo "<b style='color:red'>No se encuentra la factura en Operativo.</b>";}
			echo "</td></tr>";
		}
        echo "<tr><td align='right'>Ciudad:</td><td><b>$Ciudad</b></td></tr>
				<tr><td align='right'>Estado:</td><td bgcolor='$Estado->color_co'><b>$Estado->nombre</b></td></tr>
				<tr><td align='right'>Lider:</td><td><b>$Req->aprobado_por</b></td></tr>
				<tr>
					<td align='right'>
						Observaciones:
					</td>
					<td>
						<b>".nl2br($Req->observaciones)."</b><br>
						<a class='info' onclick='adicionar_observaciones();'><img src='gifs/standar/dsn_config.png'><span>Adicionar observaciones</span></a>
					</td>
				</tr>
				";
                if($Proveedor) ///  EVALUACION Y ASIGNACION DE PROVEEDOR A LA REQUISICION
				{
					if($Proveedor->tipo=='P') // si es proveedor no empleado
					{
						
						
						echo "<tr><td align='right'>Proveedor:</td><td nowrap='yes'><b>$Proveedor->nombre</b></td></tr>";
						if(inlist($Req->estado,'2,4'))
						{
							
							if($Req->evaluada_por && $Req->fecha_evaluacion!='0000-00-00 00:00:00')
							{
								
								echo "<tr><td align='right'>".utf8_encode("Evaluación")."</td><td>Fecha: $Req->fecha_evaluacion<br>
											Por: $Req->evaluada_por<br>Calificacion: $Req->calificacion</td></tr>";
							}
							else
							{
								
								echo "<tr><td align='right'>".utf8_encode("Evaluación")."</td>
								<td><a id='evaluarbs' onclick='evaluar_requisicion();' style='cursor:pointer;'><b style='color:red' ><img src='img/diploma.png' height='20px'> Evaluar Bien o Servicio</b></a></td></tr>";
							}
							
						}
					}
					else
					{
						echo "<tr><td align='right'>Empleado:</td><td nowrap='yes'><b>$Proveedor->nombre</b></td></tr>";
					}
				}
                else
                {
                        if($Proveedores=q("select id,nombre,calificacion_actual from $BDA.proveedor where calificacion_actual in ('M','C') or tipo='E' order by nombre"))
                        {
                                echo "<tr><td align='right'>Proveedor:</td><td nowrap='yes'><select name='proveedor' id='proveedor' style='width:300px' onchange='valida_cambio_proveedor();'><option value=''></option>";
                                while($P=mysql_fetch_object($Proveedores))
                                {
                                        $bgc='cccccc';if($P->calificacion_actual=='M') $bgc='ffffaa';if($P->calificacion_actual=='C') $bgc='aaffaa';
                                        echo "<option value='$P->id' style='background-color:#$bgc'>".utf8_encode($P->nombre)."</a>";
                                }
                                echo "</select><br><br>
                                Si el proveedor que busca no aparece en la lista, existen dos posibilidades: <br>
                                <li> Que no esté creado en la base de datos. Por lo tanto puede crearlo dando click aquí: <a onclick='crear_nuevo_proveedor();' class='info'><img src='img/adicionar_proveedor.png' height='30' border='0'><span style='width:100px'>Crear Proveedor</span></a>
                                <li> Que si esté creado pero no haya sido calificado. Puede calificar un proveedor dando click aquí: <a onclick='realizar_calificacion()' class='info'><img src='img/diploma.png' height='30' border='0'><span style='width:100px'>Realizar Calificación</span></a>
                                </td></tr>";
                        }
                }
        echo "
                </table></td><td style='width:50%;padding-left:10px;' valign='top'>";
		/// BUSQUEDA DE SEDES	
		if($Proveedor)
		{
			if($SEDES=q("select *,t_ciudad(ciudad) as nciudad from prov_sede where proveedor=$Proveedor->id"))
			{
				echo "
					<form name='sedes'>
						<b style='color:#ffffff;'>Debe seleccionar una sede para enviar el correo de solicitud de aprobación en el momento del cierre de la Requisición</b><br>
						<table border cellspacing='0' width='100%' bgcolor='ffffff'><tr><th colspan=3>SEDES DEL PROVEEDOR</th></tr>";
				while($S=mysql_fetch_object($SEDES))
				{
					echo "<tr><td><input type='radio' name='sede' onclick='asigna_sede($S->id);' ".($Req->sede==$S->id?"checked":"")." >$S->nombre</td><td>$S->nciudad ($S->direccion)</td><td>$S->email</td></tr>";
				}
				echo "</table>
					</form>";
			}
		}
		
		// fin busqueda de sedes
		echo "
		<div class='embed-responsive embed-responsive-16by9'>
	    <iframe class='embed-responsive-item' 
		  src='https://app.aoacolombia.com/Administrativo/zrequisicionDev.php?Acc=ver_requisicion&id=$id'></iframe>
		</div> </td></tr>   </table>  ";
		
		$verificar=qo("select 
        requisiciond.requisicion,oficina.centro_operacion as centrodeoperacion,oficina.nombre,ubicacion.oficina
        from aoacol_administra.requisiciond
        LEFT OUTER JOIN requisicion on requisiciond.requisicion = requisicion.id
		LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
		LEFT OUTER JOIN aoacol_aoacars.oficina on  ubicacion.oficina = oficina.id where requisicion = $id");
		if($verificar->centrodeoperacion == ''){
			$sqlAdministrativo = "select aoacol_administra.requisiciond.*,requisiciond.id as idrequisiciond,provee_produc_serv.nombre as ntipo,requisicionc.nombre as nclase,tipo.nombre as tipoBS,
                    unidad_de_medida.nombre as unidad_medida,requisiciond.observaciones,requisiciond.cantidad,
                    requisiciond.requisicion,requisiciond.valor_total,aoacol_aoacars.vehiculo.placa,
                    concat(oficina.centro_operacion,'  ',oficina.nombre) as centrodeoperacion,requisiciond.centro_costo as centrocosto,requisicion.fecha
					from aoacol_administra.requisiciond
					LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
                    LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id
                    LEFT OUTER JOIN aoacol_aoacars.oficina on requisiciond.centro_operacion = aoacol_aoacars.oficina.id
                    LEFT OUTER JOIN requisicionc on requisiciond.clase = requisicionc.id
                    LEFT OUTER JOIN ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo
					LEFT OUTER JOIN requisicion on requisiciond.requisicion = requisicion.id
					where requisicion =$id order by requisiciond.id";
			$sql = $sqlAdministrativo;
		}else{
			/*Consulta realizada por temas de centro de operaciones hay que sustraer este de la tabla ubicaciones*/			
		     $sqlControOperativo = "select aoacol_administra.requisiciond.*,requisiciond.id as idrequisiciond,provee_produc_serv.nombre as ntipo,requisicionc.nombre as nclase,tipo.nombre as tipoBS,
                    unidad_de_medida.nombre as unidad_medida,requisiciond.observaciones,requisiciond.cantidad,
                    requisiciond.requisicion,requisiciond.valor_total,aoacol_aoacars.vehiculo.placa,
                    concat( oficina.centro_operacion,'  ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto,
                    requisicion.fecha, ubicacion.oficina
					from aoacol_administra.requisiciond
					LEFT OUTER JOIN aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					LEFT OUTER JOIN aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					LEFT OUTER JOIN aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
                    LEFT OUTER JOIN aoacol_aoacars.vehiculo on requisiciond.id_vehiculo = aoacol_aoacars.vehiculo.id
                    LEFT OUTER JOIN requisicionc on requisiciond.clase = requisicionc.id
                    LEFT OUTER JOIN ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo
					LEFT OUTER JOIN requisicion on requisiciond.requisicion = requisicion.id
                    LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
                    LEFT OUTER JOIN aoacol_aoacars.oficina on  ubicacion.oficina = oficina.id
                    LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id
					WHERE requisicion =$id order by requisiciond.id 		
                    ";
			$sql = $sqlControOperativo;
		}
		
		//echo $sql;
		if($Detalle=q($sql)) 
        {
                echo "<table border cellspacing='0' bgcolor='ffffff'><tr>
					<th>Item</th>
					<th>Tipo de Requisición</th>
					<th>Unidad de medida</th>
					<th>Clase</th>
					<th>Tipo Cobro</th>
					<th>".utf8_encode("Descripción")."</th>
					<th>Valor Unitario</th>
					<th>Cantidad</th>
					<th>Valor Total item</th>
					<th>Proyecto o placa</th>
					<th>Centro de operaciones</th>
					<th>Centro de costos</th>
					<th>Factor</th>
					<th>".utf8_encode("Asociación")."</th>
					<th>Boton ver facturas</th>
					<th></th>";
					if($USER == 15){echo "<th>Modificar item</th> ";}
               echo  "</tr>
                ";
				$no_bs=true;
				$no_tc=true;
                while($D=mysql_fetch_object($Detalle))
                {
                        $Tipo_cobro='';
                        if($D->tipo_cobro=='S') $Tipo_cobro='SIN RECOBRO';
                        if($D->tipo_cobro=='C') $Tipo_cobro='CON RECOBRO';
                        if($D->tipo_cobro=='N') $Tipo_cobro='NO APLICA';
					echo "<tr><td>";
						if(!$D->tipo1)
						{
							if($Proveedor)
							{
								if($Proveedor->tipo=='P')  // si es proveedor no empleado
								{
									if(inlist($USER,'1,2,8')) // ASIGNACION DE BIENES Y SERVICIOS
									{
											$Opciones='';

											if($Bienes=q("select bs.id,bs.nombre from $BDA.provee_produc_serv bs,provee_ofrece po
													where bs.tipo='B' and po.proveedor='$Req->proveedor' and po.producto_servicio=bs.id
													order by bs.nombre"))
											{
													$Opciones.="<optgroup label='BIENES'>";
													while($B=mysql_fetch_object($Bienes))   $Opciones.="<option value='$B->id'>".utf8_encode($B->nombre)."</option>";
													$Opciones.="</optgroup>";
											}
											if($Servicios=q("select bs.id,bs.nombre from $BDA.provee_produc_serv bs,provee_ofrece po
													where bs.tipo='S' and po.proveedor='$Req->proveedor' and po.producto_servicio=bs.id
													order by bs.nombre"))
											{
													$Opciones.="<optgroup label='SERVICIOS'>";
													while($S=mysql_fetch_object($Servicios))        $Opciones.="<option value='$S->id'>".utf8_encode($S->nombre)."</option>";
													$Opciones.="</optgroup>";
											}
											if($Opciones)
													echo "<select name='tipo1_$D->id' id='tipo1_$D->id' style='width:300px' onchange='cambia_tipo1($D->id);'>
																<option value=''></option>$Opciones</select>";
											else
													echo "<b style='color:red'>No hay bienes o servicios configurados en este proveedor</b>";
									}
									else
										echo "Sin permiso para adicionar bienes o servicios.";
								}
								else
									echo "Empleado sin Bienes ni Servicios.";
							}
						}
                        else 
						{
							echo utf8_encode($D->ntipo);
							$no_bs=false;
						}
                        echo "</td>
						<td>$D->tipoBS</td>
						<td>$D->unidad_medida</td>
						<td>$D->nclase</td><td nowrap='yes'>";
						
						// PRESENTACION O ASIGNACION DE TIPO DE COBRO
						if($Tipo_cobro) {echo $Tipo_cobro; $no_tc=false;}
						else
						{
							echo "<select name='tipo_cobro_$D->id' id='tipo_cobro_$D->id' onchange='cambia_tipo_cobro($D->id);'><option value=''></option>
									<option value='S'>SIN RECOBRO</option>
									<option value='C'>CON RECOBRO</option>
									<option value='N'>NO APLICA</option>
									</select><br>
									";
						}
						// PRESENTACION DEL VALOR, CANTIDAD Y OBSERVACIONES.
						
						if($D->placa == '' or $D->placa == null){
							$placaDinamica = 'No definida';
						}else{
							$placaDinamica = $D->placa;
						}
						if($D->centro_operacion == 'NAL'){
							$centroOperativo = 'NACIONAL';
						}else{
							$centroOperativo = $D->centrodeoperacion;
						}
						if($D->factor == '' or $D->factor == null or $D->factor == 'Selecciona'){
							$factorDinamico = 'Factor no definido';
						}else{
							$factorDinamico = $D->factor;
						}
						if($D->centrocosto == '' or $D->centrocosto == null or $D->centrocosto == 'Selecciona'){
							$centrocosto = 'Centro de costo no definido';
						}else{
							$centrocosto = $D->centrocosto;
						}
						
						
						
						echo "</td>
									<td align='left'>".utf8_encode($D->observaciones)."</td>
									<td align='right'>$".coma_format($D->valor)."</td>
									<td align='right'>$D->cantidad</td>
									<td align='right'>$".coma_format($D->valor_total)."</td>
									<td align='right'>$placaDinamica</td>
									<td align='right'>$centroOperativo</td>
									<td align='right'>$centrocosto</td>
									<td align='right'>$factorDinamico</td>
									
									";
						///  PRESENTACION DE LA ASOCIACION DE FACTURA
						echo "<td align='center'>";
						if(!$D->tipo_cobro) echo "<b style='color:red'>Debe seleccionar el tipo de cobro</b>";
						if($Req->estado<2 && !$Req->cerrada)
							{echo "<a class='rinfo' style='cursor:pointer;' onclick='borrar_item_requisicion($D->id);'><img src='gifs/standar/Cancel.png' border='0'><span>Borrar</span></a>";}
						if($Req->estado==2 && $D->tipo_cobro)
						{
							if($Req->proveedor) 
							{
								if($Proveedor->tipo=='P')
									echo "<b style='color:blue'>Siguiente paso: evaluar al proveedor</b>";
								else
									echo "<b>El proveedor es empleado de AOA.<br>No requiere selección ni ".utf8_encode("evaluación").".</b><br>
											<a class='rinfo' onclick='asociar($D->id);'><img src='gifs/standar/Next.png'><span>Asociar a una factura de proveedores</span></a>";
							}
							else echo "<b style='color:red'>Debe seleccionar un proveedor para poder evaluarlo y luego asociar una factura a esta requisición</b>";
						}
						
						if($Req->estado==4 && $D->tipo_cobro)
						{
							if($Req->proveedor) 
							{
								if($Proveedor->tipo=='P')
								{
									if($Req->evaluada_por && $Req->fecha_evaluacion!='0000-00-00 00:00:00')
									{
										if($D->factura_proveedor)
										{
											$dFac=qo("select f.id,td.nombre as tdoc,numero,fecha_emision from factura f,tipo_documento td WHERE f.tipo_doc=td.id and f.id=$D->factura_proveedor");
											echo "[$dFac->tdoc] $dFac->numero ($dFac->fecha_emision)";
										}
										else
											echo "<a class='rinfo' onclick='asociar($D->id);'><img src='gifs/standar/Next.png'><span>Asociar a una factura de proveedores</span></a>";
									}
									else echo "<b style='color:red'>Debe evaluar al proveedor antes de asociar la factura</b>";
								}
								else
									echo "<a class='rinfo' onclick='asociar($D->id);'><img src='gifs/standar/Next.png'><span>Asociar a una factura de proveedores</span></a>";
							}
							else echo "<b style='color:red'>Debe seleccionar un proveedor para poder evaluarlo y luego asociar una factura a esta requisición</b>";
						}
						///  CAPTURA DE LOS CONSECUTIVOS DEL PROVEEDOR Y SISTEMA UNO.
						echo "<td><button onclick='facturas_agregadas($D->id)'>Ver Facturas</button></td>";
						//print_r($D);
						echo "<td> <!--<button onclick='incluir_factura($D->id)'>Incluir Factura</button> --></td>";
						
						if($USER == 15){
							echo "<td><button onclick='modificar_item($D->idrequisiciond,$id)'>Modificar item </button></td>";
						}
						
						// *********************************************************************************		
						echo "</tr>";
                }
					$retorno=qo("select requisiciond.requisicion,requisiciond.valor_total,
		            sum(requisiciond.valor_total) as resultado 
					from aoacol_administra.requisiciond
					where requisicion  =$id");
					
					echo "
					<tr style='border: none;'
					><td></td><td></td><td></td><td></td><td></td><td></td><td></td><th>Total</th><th><b>$".coma_format($retorno->resultado)."</b> COP</th></tr>";
					/*cierre de  tabla*/
				
				echo "</table>";
				if($no_bs || $no_tc) 
					echo "<script language='javascript'>document.getElementById('evaluarbs').style.visibility='hidden';</script>";
        }
		//print_r($Req);
		echo "
		<script>
			function facturas_agregadas(requisiciond)
			{
				$.post('zrequisicion.php',{'Acc':'requisiciond_facturas_agregadas','requisiciond':requisiciond},function( data ) {
				//console.log(data);
				  $('#ajax-content').html(data);
				});
				$('input[name=requisiciond_id]').val(requisiciond);
				$('#modalfacturas_agregadas').modal('show');
				
			}
			function incluir_factura(requisiciond)
			{
				$('input[name=requisiciond_id]').val(requisiciond);
				$('#modalincluir_factura').modal('show');
			}
			function agregar_factura()
			{
				$('#modalincluir_factura').modal('show');
			}
		</script>
		";	
		
		$cotizacion1 = $Req->cotizacion_f; 
		$cotizacion2 = $Req->cotizacion2_f;
		$cotizacion3 = $Req->cotizacion3_f;
		
		
		$cotizaciones_table ="<div>
						<table class='table ' border width='75%'>
							<thead>
								<tr>
									<th colspan='3'>Cotizaciones</th>
								</tr>
							</thead>
							<tbody>";
		$cotizaciones_table .=	"<tr>";
			$cotizaciones_table .=	"<td width='33%' height='165px'>";
							
				if($cotizacion1!= null)
				{
					$cotizaciones_table .= "<a href='/Administrativo/$cotizacion1' target='_blank'><b>".utf8_encode("Ver cotización")."</b></a>";
					$cotizaciones_table .= "<br>";
				
					if(strpos($cotizacion1,"pdf"))
					{					
						$cotizacion1_img = "<embed src='/Administrativo/$cotizacion1'  type='application/pdf'>";
					}
					else {$cotizacion1_img = "<img src='/Administrativo/$cotizacion1' style='max-width:180;' border='0'>";}				
				}
				
				$cotizaciones_table .= " ".$cotizacion1_img." ";
				
				if(($cotizacion1 == null or $Req->cotizacion_f_proveedor == null or $Req->cotizacion_f_valor == null) || $_SESSION['User']==1)
				{
					$cotizaciones_table .= "<br><form  id='form1' action='zrequisicion.php'>";
					$cotizaciones_table .= "<input type='file' name='image' required>";
					$cotizaciones_table .= "<input type='hidden' name='Acc' value='cotizacion_1_save'>";
					$cotizaciones_table .= "<input type='hidden' name='Req_id' value='$Req->id'>";
					$cotizaciones_table .= "<label>".utf8_encode("Valor cotizacion")."</label>&nbsp<input min='1000' autocomplete='off' type='number' style='width:50%;' name='valor' value='$Req->cotizacion_f_valor' required>";
					$cotizaciones_table .= "<br>";
					
					if($USER == 15){
					$proovedor_select = proov_select_all_history($Req->cotizacion3_f_proveedor);
					}else{
					$proovedor_select = proov_select($Req->cotizacion3_f_proveedor);
					}
					$cotizaciones_table .= "<label>proovedor:</label>&nbsp".$proovedor_select;
					$cotizaciones_table .= "<br>
					<button style='width:100%;'>Guardar</button></form>";
				}
				else
				{
					$cotizaciones_table .= "<br><b>".utf8_encode("Valor cotización")."</b> : ".$Req->cotizacion_f_valor."<br>";			
					
					
					if($USER == 15){
						$sql = "select * from aoacol_administra.proveedor";
					}else{
						$sql = "select * from aoacol_administra.proveedor where id = ".$Req->cotizacion_f_proveedor;
					}
					$proveedor = qo($sql);				
					
					$cotizaciones_table .= "<b>proovedor:</b>&nbsp".$proveedor->nombre;
				}
				
				
				if(($_SESSION['User']==1 ||  $Req->cotizacion_f != null) and $Req->cotizacion_seleccionada == null ){
					$cotizaciones_table .= " 					
					<form onsubmit='asignar_cotizacion(this)'>
						<input type='hidden' name='Req_id' value='".$Req->id."'>
						<input type='hidden' name='cotizacion' value='1'>
						<button style='width:100%;'>".utf8_encode("Asignar como cotización seleccionada para compra")."</button>
					</form>";	
				}
				//echo"<span id='coti_1'></span>";
				if($Req->cotizacion_seleccionada == 1)
				{
					$cotizaciones_table .= "<br> 
					<span><strong>".utf8_encode("COTIZACIÓN SELECCIONADA")."</strong></span>";
				}					
			
            $cotizaciones_table .=	"</td>";
            $cotizaciones_table .=	"<td width='33%' height='165px'>";			
		    
			if($cotizacion2 != null)
			{
				$cotizaciones_table .= "<a href='/Administrativo/$cotizacion2' target='_blank'>".utf8_encode("Ver cotización")."</a>";
				$cotizaciones_table .= "<br>";
				if(strpos($cotizacion2,"pdf"))
				{					
					$cotizacion2_img = "<embed src='/Administrativo/$cotizacion2'  type='application/pdf'>";
				}
				else {$cotizacion2_img = "<img src='/Administrativo/$cotizacion2' style='max-width:180;' border='0'>";}
			}
			
			$cotizaciones_table .= " ".$cotizacion2_img." ";
			
			
			if($cotizacion2 == null or $Req->cotizacion2_f_proveedor == null or $Req->cotizacion2_f_valor == null || $_SESSION['User']==1)
			{				
				$cotizaciones_table .= "<br><form  id='form2' action='zrequisicion.php'>";
				$cotizaciones_table .= "<input type='file' name='image' required>";
				$cotizaciones_table .= "<input type='hidden'  name='Acc' value='cotizacion_2_save'>";
				$cotizaciones_table .= "<input type='hidden' name='Req_id' value='$Req->id'>";
				$cotizaciones_table .= "<label>".utf8_encode("Valor cotizacion")."</label>&nbsp<input min='1000' autocomplete='off' type='number' style='width:50%;' value='$Req->cotizacion2_f_valor' name='valor' required>";
				$cotizaciones_table .= "<br>";
				
				if($USER == 15){
					$proovedor_select = proov_select_all_history($Req->cotizacion3_f_proveedor);
				}else{
					$proovedor_select = proov_select($Req->cotizacion3_f_proveedor);
				}
				
				
				$cotizaciones_table .= "<label>proovedor:</label>&nbsp".$proovedor_select;
				$cotizaciones_table .= "<br>
				<button style='width:100%;'>Guardar</button></form>";				
			}
			else
			{
				$cotizaciones_table .= "<br><b>".utf8_encode("Valor cotización")."</b> : ".$Req->cotizacion2_f_valor."<br>";			
				
				if($USER == 15){
					$sql = "select * from aoacol_administra.proveedor";
				}else{
					$sql = "select * from aoacol_administra.proveedor where id = ".$Req->cotizacion2_f_proveedor;
				}
				
				
				$proveedor = qo($sql);				
				
				$cotizaciones_table .= "<b>proovedor:</b>&nbsp".$proveedor->nombre;
			}
			
			if(($_SESSION['User']==1 ||  $Req->cotizacion2_f != null) and $Req->cotizacion_seleccionada == null){
				$cotizaciones_table .= "
				<form onsubmit='asignar_cotizacion(this)'>
					<input type='hidden' name='Req_id' value='".$Req->id."'>
					<input type='hidden' name='cotizacion' value='2'>
					<button style='width:100%;'>".utf8_encode("Asignar como cotización seleccionada para compra")."</button>
				</form>";	
			}
			//echo"<span id='coti_2'></span>";
			if($Req->cotizacion_seleccionada == 2)
			{
				$cotizaciones_table .= "<br> 
				<span><strong>".utf8_encode("COTIZACIÓN SELECCIONADA")."</strong></span>";
			}
			
			$cotizaciones_table .=	"</td>";
            $cotizaciones_table .=	"<td width='34%' height='165px'>";			
			
			if($cotizacion3 != null)
			{
				$cotizaciones_table .= "<a href='/Administrativo/$cotizacion3' target='_blank'>".utf8_encode("Ver cotización")."</a>";
				$cotizaciones_table .= "<br>";
				if(strpos($cotizacion3,"pdf"))
				{						
					$cotizacion3_img = "<embed src='/Administrativo/$cotizacion3'  type='application/pdf'>";
				}					
				else {$cotizacion3_img = "<img src='/Administrativo/$cotizacion3' style='max-width:180;' border='0'>";}
			}
			
			$cotizaciones_table .= " ".$cotizacion3_img." ";
			
			
			if($cotizacion3 == null or $Req->cotizacion3_f_proveedor == null or $Req->cotizacion3_f_valor == null || $_SESSION['User']==1)
			{		
				$cotizaciones_table .= "<br><form  id='form3' action='zrequisicion.php'>";
				$cotizaciones_table .= "<input type='file' name='image' required>";
				$cotizaciones_table .= "<input type='hidden' name='Acc' value='cotizacion_3_save'>";
				$cotizaciones_table .= "<input type='hidden' name='Req_id' value='$Req->id'>";
				$cotizaciones_table .= "<label>".utf8_encode("Valor cotizacion")."</label>&nbsp<input min='1000' autocomplete='off' type='number' style='width:50%;' value='$Req->cotizacion3_f_valor' name='valor' required>";
				$cotizaciones_table .= "<br>";
				if($USER == 15){
					$proovedor_select = proov_select_all_history($Req->cotizacion3_f_proveedor);
				}else{
					$proovedor_select = proov_select($Req->cotizacion3_f_proveedor);
				}
				
				$cotizaciones_table .= "<label>proovedor:</label>&nbsp".$proovedor_select;
				$cotizaciones_table .= "<br>
				<button style='width:100%;'>Guardar</button></form>";
			}
			else
			{
				$cotizaciones_table .= "<br><b>".utf8_encode("Valor cotización")."</b> : ".$Req->cotizacion3_f_valor."<br>";			
				
				if($USER == 15){
					$sql = "select * from aoacol_administra.proveedor";
				}else{
					$sql = "select * from aoacol_administra.proveedor where id = ".$Req->cotizacion3_f_proveedor;
				}
				
				
				$proveedor = qo($sql);				
				
				$cotizaciones_table .= "<b>proovedor:</b>&nbsp".$proveedor->nombre;
			}				

			if(($_SESSION['User']==1 ||  $Req->cotizacion3_f != null ) and $Req->cotizacion_seleccionada == null){
				$cotizaciones_table .= "
				<form onsubmit='asignar_cotizacion(this)'>
					<input type='hidden' name='Req_id' value='".$Req->id."'>
					<input type='hidden' name='cotizacion' value='3'>
					<button style='width:100%;'>".utf8_encode("Asignar como cotización seleccionada para compra")."</button>
				</form>";	
			}
			
			
			if($Req->cotizacion_seleccionada == 3)
			{
				$cotizaciones_table .= "<br> 
				<span><strong>".utf8_encode("COTIZACIÓN SELECCIONADA")."</strong></span>";	
			}
				
				
				
			
			$cotizaciones_table .=	"</td>";						
		$cotizaciones_table .=	"</tr>
							</tbody>	
						</table>	
					</div></div>";
					
		
		echo $cotizaciones_table;
		
		$opciones_proov = "
			<button style='margin-left:10%; margin-top: -30px;' onclick='crear_proovedor()'><img src='img/adicionar_proveedor.png' >Crear Proveedor</button><br>
		";
		
		echo $opciones_proov;
		
		if($Req->estado<2 && !$Req->cerrada) 
			
		echo "<br><a style='cursor:pointer;' onclick='adicionar_detalle();'><img src='gifs/standar/nuevo_registro.png' border='0'> ".utf8_encode("Adicionar detalle a esta requisición")."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
		                        
		if($USER == 15 ){
			
			
			if($Req->estado==2 or $Req->estado==4 or $Req->estado==1 or !$Req->cerrada){
			echo "<br><a style='cursor:pointer;' onclick='adicionar_detalle();'><img src='gifs/standar/nuevo_registro.png' border='0'> ".utf8_encode("Adicionar detalle a esta requisición")."</a>&nbsp;&nbsp;&nbsp;&nbsp;";		
			}
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<a class='info' href='javascript:borrar_requisicion($id);'><img src='gifs/standar/borra_registro.png' border='0'>".utf8_encode("Borrar Requisición")."<span style='width:200px'>Borrar Requisición</span></a>";
		}else{
			echo " <a class='info' href='javascript:cerrar_requisicion($id);'><img src='gifs/standar/stop_16.png' border='0'>".utf8_encode("Cerrar Requisición - Enviar correo de solicitud de aprobación")."<span style='width:200px'>Cerrar Requisición - envía solicitud de aprobación automática.</span></a>";
		}						
        
		
								
		echo "&nbsp;&nbsp;&nbsp;&nbsp;".ver_boton_excel()."<a class='info ocultar' href='javascript:subir_excel();'><img src='gifs/standar/nuevo_registro.png' border='0'>".utf8_encode("SUBIR EXCEL ADMINISTRATIVA")."<span style='width:200px'>Subir Excel</span></a>";
		if($Req->estado==2 or $Req->estado==4) echo "<a class='info' href='javascript:re_enviar_aprobacion($id);'><img src='gifs/standar/derecha.png' border='0'>".utf8_encode("Re-enviar la aprobación una vez mas al proveedor")."</a>";
		if($Req->estado==1) echo "<a style='cursor:pointer' onclick='re_enviar_solicitud_aprobacion($id);'><img src='gifs/standar/derecha.png' border='0'> Re-enviar la solicitud una vez mas.</a>";
        echo "<iframe name='Oculto_detallerq' id='Oculto_detallerq' style='visibility:hidden' width='1' height='1'></iframe></body>";

		
		include("views/subviews/requisiciones/extra_html.html");
}



function proov_select($selectedvalue)
{
	select2();
	if($selectedvalue == null)
	{
		$selectedvalue = 0;
	}
	
	$proovs = q("select * from aoacol_administra.proveedor where calificacion_actual != 'N' order by nombre");
	
	$proovs_array = array();
	
	while($fila = mysql_fetch_object($proovs))
	{
		array_push($proovs_array, $fila);
	}
	
	$proovedor_select = "<select name='prov' id='prov' style='width:60%;' required>";		
	$proovedor_select .= "<option value=''>Selecciona</option>";
	foreach($proovs_array as $proov)
	{
		if($proov->id == $selectedvalue)
		{
			$proovedor_select .= "<option value='".$proov->id."' selected>".utf8_encode($proov->nombre)."</option>";
		}
		else
		{
			$proovedor_select .= "<option value='".$proov->id."'>".utf8_encode($proov->nombre)."</option>";	
		}			
	}		
	$proovedor_select .= "</select>";
	
	return $proovedor_select;
}

function proov_select_all_history($selectedvalue)
{
	
	if($selectedvalue == null)
	{
		$selectedvalue = 0;
	}
	
	$proovs = q("select * from aoacol_administra.proveedor order by nombre");
	
	$proovs_array = array();
	
	while($fila = mysql_fetch_object($proovs))
	{
		array_push($proovs_array, $fila);
	}
	
	$proovedor_select = "<select name='prov' id='prov' style='width:60%;' required>";		
	$proovedor_select .= "<option value=''>Selecciona</option>";
	foreach($proovs_array as $proov)
	{
		if($proov->id == $selectedvalue)
		{
			$proovedor_select .= "<option value='".$proov->id."' selected>".utf8_encode($proov->nombre)."</option>";
		}
		else
		{
			$proovedor_select .= "<option value='".$proov->id."'>".utf8_encode($proov->nombre)."</option>";	
		}			
	}		
	$proovedor_select .= "</select>";
	select2();
	return $proovedor_select;
}
function cotizacion_1_save()
{
	global $valor,$prov,$Req_id;
	//echo "cotizacion_1_save ".$valor." prov ".$prov;
	$url = upload_cotizacion($Req_id);	
	$sql = "update aoacol_administra.requisicion set cotizacion_f_valor = '$valor' , cotizacion_f_proveedor = '$prov' , cotizacion_f = '$url' where id = $Req_id ";
	q($sql);
	echo "datos actualizados";	 
}

function cotizacion_2_save()
{
	global $valor,$prov,$Req_id;
	//echo "cotizacion_2_save ".$valor." prov ".$prov;
	$url = upload_cotizacion($Req_id);
	$sql = "update aoacol_administra.requisicion set cotizacion2_f_valor = '$valor' , cotizacion2_f_proveedor = '$prov' , cotizacion2_f = '$url' where id = $Req_id ";
	q($sql);
	echo "datos actualizados";
}

function cotizacion_3_save()
{
	global $valor,$prov,$Req_id;
	//echo "cotizacion_3_save ".$valor." prov ".$prov;
	$url = upload_cotizacion($Req_id);
	$sql = "update aoacol_administra.requisicion set cotizacion3_f_valor = '$valor' , cotizacion3_f_proveedor = '$prov' , cotizacion3_f = '$url' where id = $Req_id ";
	q($sql);
	echo "datos actualizados";
}

function upload_cotizacion($cotizacion_id)
{
	
	$imagen = $_FILES['image'];
	$file_url = '/var/www/html/public_html/Administrativo/';
	$folder='requisicion';
	$subfolder = "/0".substr($cotizacion_id, 0, 2)."/".$cotizacion_id;

	$Camino = $file_url.$folder.$subfolder;
	if(!is_dir($Camino))
	{
		mkdir($Camino, 0777, true);
	}
	else
	{
		chmod($Camino, 0777);
	}	
	
	$name = basename($_FILES['image']['name']);
	
	$uploadfile = $Camino .'/'. $name;
	
	$sqlpath = $folder.$subfolder."/".$name; 
	
	
	
	//echo "<p>";
	
	if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
		//echo "File is valid, and was successfully uploaded.\n";
	   return $sqlpath;
	
	} else {
	   //echo "Upload failed";
	   return null;
	}
}

function asignar_cotizacion()
{
	global $cotizacion,$Req_id;
	$sql = "update aoacol_administra.requisicion set cotizacion_seleccionada = ".$cotizacion." where id = ".$Req_id;
	q($sql);
	echo "cotización seleccionada";
}

function ver_requisicion_test()
{
       echo "disabled";
}

function requisiciond_facturas_agregadas()
{

	global $requisiciond;
	$sql = "select id, id as requisiciond, consecutivo_suno, consecutivo_provee, facprov_f, valor_factura, 'inrow' as tipo from aoacol_administra.requisiciond
		where id = '$requisiciond' union select id, requisiciond, consecutivo_suno, consecutivo_provee, facprov_f , valor_factura,'extra' as tipo from aoacol_aoacars.requisiciond_facturas where requisiciond = '$requisiciond'  ";
	
	$result = q($sql);
	$facturas = array();
	while($fila = mysql_fetch_object($result))
	{
		
		array_push($facturas,$fila);
	}
	
	header('Content-Type: text/html; charset=utf-8');
	include("views/subviews/requisiciond_facturas.html");
}

function incluir_factura_requisiciond()
{
		global $requisiciond_id,$consecutivo_provee,$consecutivo_uno,$valor_factura;
		
		$fecha_creacion = date('Y-m-d');
		if(!isset($requisiciond_id))
		{			
			$requisiciond_id = $_POST['requisiciond'];
			print_r($_POST);
		}		
		
		$imagen = $_FILES['image'];
		$file_url = '/var/www/html/public_html/Administrativo/';
		$folder='dreq_facprov';
		$subfolder = "/0".substr($requisiciond_id, 0, 2)."/".$requisiciond_id;
		//055/55195/facprov_f_000046.pdf	
		$Camino = $file_url.$folder.$subfolder;
		if(!is_dir($Camino))
		{
			mkdir($Camino, 0777, true);
		}
		else
		{
			chmod($Camino, 0777);
		}	
		
		$name = basename($_FILES['image']['name']);
		
		$uploadfile = $Camino .'/'. $name;
		
		$sqlpath = $folder.$subfolder."/".$name; 
		
		echo "camino: ".$uploadfile;
		
		echo "<p>";
		
		if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
			echo "File is valid, and was successfully uploaded.\n";
		} else {
		   echo "Upload failed";
		}
		
		if(isset($_POST['tipo']))
		{
			unlink("/var/www/html/public_html/Administrativo/".$_POST['prev_file']);
			
			if($_POST['tipo'] == "inrow")
			{
				$sql = "UPDATE  aoacol_administra.requisiciond set facprov_f = '$sqlpath'  where id = ".$_POST['id'];
			}
			
			if($_POST['tipo'] == "extra")
			{
				$sql = "UPDATE  aoacol_aoacars.requisiciond_facturas set facprov_f = '$sqlpath'  where id = ".$_POST['id'];
			}
			
			q($sql);
			echo "Datos actualizados";
		}
		else
		{
			$sql = "SELECT * from aoacol_administra.requisiciond where id = ".$requisiciond_id;		
			$requisiciond = qo($sql);
			//print_r($requisiciond);
			if($requisiciond->facprov_f == null)
			{
				$sql = "UPDATE  aoacol_administra.requisiciond SET consecutivo_suno = '$consecutivo_uno', consecutivo_provee = '$consecutivo_provee', facprov_f = '$sqlpath', valor_factura = '$valor_factura'  where id = '$requisiciond_id' ";
				q($sql);
				$_SESSION['factura_requisicion'.$requisiciond_id] = true;
			}
			else
			{
				$sql = "Insert into aoacol_aoacars.requisiciond_facturas  (requisiciond,consecutivo_suno,consecutivo_provee,facprov_f,valor_factura,fecha_creacion) values ('$requisiciond_id','$consecutivo_uno','$consecutivo_provee','$sqlpath','$valor_factura','$fecha_creacion') ";
				q($sql);
				
				$sql = "SELECT max(id) as id from aoacol_aoacars.requisiciond_facturas";		
				$requisiciond = qo($sql);
				$_SESSION['factura_requisicion'.$requisiciond->id] = true;
			}
		}
		
		
}



function borrar_requisiciond_factura()
{
	global $id,$tipo;
	
	unlink("/var/www/html/public_html/Administrativo/".$_POST['prev_file']);
	
	if($tipo == "inrow")
	{
		$sql = "UPDATE  aoacol_administra.requisiciond set consecutivo_suno = '',  consecutivo_provee = '' , facprov_f = '' where id =".$id;
	}
	
	if($tipo == "extra")
	{
		$sql = "DELETE from aoacol_aoacars.requisiciond_facturas where id = ".$id;
	}
	
	q($sql);
	
	echo "Datos eliminados";
}

function editar_requisiciond_factura()
{
	global $requisiciond,$consecutivo_suno,$consecutivo_provee,$tipo,$id;
	if($tipo == "inrow")
	{
		$sql = "UPDATE  aoacol_administra.requisiciond set consecutivo_suno = '$consecutivo_suno',  consecutivo_provee = '$consecutivo_provee'  where id = ".$id;
	}
	
	if($tipo == "extra")
	{
		$sql = "UPDATE  aoacol_aoacars.requisiciond_facturas set consecutivo_suno = '$consecutivo_suno',  consecutivo_provee = '$consecutivo_provee' where id = ".$id;
	}
	
	q($sql);
	
	echo "Datos editados";
}

function asigna_sede()
{
	global $id,$sede;
	q("update $BDA.requisicion set sede=$sede where id=$id");
	echo "<body><script language='javascript'>alert('Sede asignada');</script></body>";
}

function ajusta_bs() // ajusta bien, o servicio en el detalle de una requisicion
{
        global $id,$dato,$BDA; // id es el id del detalle de requisicion y dato es el id del bien o servicio que se está asignando.
        q("update $BDA.requisiciond set tipo1='$dato' where id='$id' ");
        echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function ajusta_prov() // ajusta el proveedor, lo graba en la requisicion
{
        global $id_requisicion,$id_proveedor,$BDA;
        q("update $BDA.requisicion set proveedor='$id_proveedor' where id='$id_requisicion' ");
        echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function ajusta_tipo_cobro()
{
	global $id_detalle,$opcion; // id_detalle es el id del registro en requisiciond y opcion es el valor del campo tipo_cobro
	q("update requisiciond set tipo_cobro='$opcion' where id='$id_detalle' ");
	echo "<body><script language='javascript'>parent.recargar();</script></body>";
}

function asociar_factura()
{
	global $iddr,$NUSUARIO;
	$Det=qo("select * from requisiciond where id=$iddr");
	$Req=qo("select * from requisicion where id=$Det->requisicion");
	$Proveedor=qo("select * from proveedor where id=$Req->proveedor");
	$Ciudad=qo("select nombre from ciudad where codigo='$Req->ciudad'");
	html();
	echo "
	<script language='javascript'>
		function asociar(id)
		{if(confirm('Desea asociar esta factura a la requisición?')) window.open('zrequisicion.php?Acc=asociar_factura_requisicion&idf='+id+'&iddr=$iddr','Oculto_asociar');}
		function recargar() {window.open('zrequisicion.php?Acc=asociar_factura&iddr=$iddr','_self');}
		function cerrar() {window.close();void(null);opener.recargar();}
		function crear_nuevo_proveedor() {modal('zproveedor.php?Acc=adicion_de_proveedor','crear_nuevo_proveedor');}
        function realizar_calificacion() {window.open('zproveedor.php?Acc=realizar_calificacion&refrescar_opener=1','_self');}
        function valida_cambio_proveedor() 
		{
			var P=document.getElementById('proveedor_nuevo');
			var Np=P.options[P.selectedIndex].text;
			if(confirm('Desea asignar al proveedor: '+Np+' a esta requisición?')) 
				window.open('zrequisicion.php?Acc=cambio_de_proveedor&idr=$Req->id&idp='+P.value,'Oculto_asociar');
		}
	</script>
	<body><h3>ASOCIACION DE FACTURA A LA REQUISICION NUMERO $Req->id Registro detalle: $iddr</h3>
	<iframe name='Oculto_asociar' id='Oculto_asociar' style='visibility:hidden' width='1' height='1'></iframe>
	
	<table border cellspacing='0'>
		<tr><td align='right'>Proveedor:</td><td><b>$Proveedor->nombre</b></td></tr>
		<tr><td align='right'>Fecha de Requisición</td><td>$Req->fecha</td></tr>
		<tr><td align='right'>Solicitado por</td><td>$Req->solicitado_por</td></tr>
		<tr><td align='right'>Ciudad</td><td>$Ciudad->nombre</td></tr>
		<tr><td align='right'>Lider de proceso:</td><td>$Req->aprobado_por</td></tr>
		<tr><td align='right'>Observaciones</td><td>$Req->observaciones</td></tr>
		<tr><td align='right'>Evaluada por</td><td>$Req->evaluada_por<br>
															Fecha: $Req->fecha_evaluacion<br>
															Calificacion: $Req->calificacion</td></tr>
	</table>
	";
	if($Proveedor->tipo=='P')
	{
		if($Req->estado==4 && $Req->evaluada_por && $Req->fecha_evaluacion!='0000-00-00 00:00:00' )
		{
			echo "<h3>Facturas de este proveedor:</h3> ";
			if($Facturas=q("select f.id,td.nombre as tdoc,numero,fecha_emision, descripcion,valor_a_pagar from factura f,tipo_documento td 
						WHERE f.tipo_doc=td.id and proveedor=$Proveedor->id"))
			{
				echo "<table border cellspacing='0'><tr>
					<th>Tipo Documento</th>
					<th>Numero</th>
					<th>Fecha Emision</th>
					<th>Descripci&oacute;n</th>
					<th>Valor</th>
					<th>Asociar</th>
					</tr>";
				while($F =mysql_fetch_object($Facturas ))
				{
					echo "<tr>
					<td>$F->tdoc</td>
					<td>$F->numero</td>
					<td>$F->fecha_emision</td>
					<td>$F->descripcion</td>
					<td align='right'>".coma_format($F->valor_a_pagar)."</td>
					<td align='center'><a class='info' onclick='asociar($F->id);'><img src='gifs/standar/derecha.png'><span>Asociar</span></a></td>
					</tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<b>No hay facturas de este proveedor</b>";
			}
		}
		else
		{
			echo "<b>Debe primero evaluar el bien o servicio antes de asociar facturas.</b> 
			<a style='cursor:pointer;' onclick='cerrar();'><u>Click aqu&iacute; para regresar a la pantalla anterior y evaluar el proveedor.</u></a> ";
		}
	}
	else
	{
		echo "<br><b>El proveedor es un empleado de AOA</b><br>
		Si se requiere puede cambiar el proveedor de esta requisici&oacute;n por un proveedor confiable o medianamente confiable. 
		O si se requiere puede crear un nuevo proveedor cumpliendo todos los lineamientos de calidad.<br><br>
		Para asignar un proveedor calificado, seleccionelo del siguiente men&uacute;:<br>
		";
	if($Proveedores=q("select id,nombre,calificacion_actual from proveedor where calificacion_actual in ('M','C') and tipo='P' order by nombre"))
    {
		echo "<select name='proveedor_nuevo' id='proveedor_nuevo' style='width:300px' onchange='valida_cambio_proveedor();'><option value=''></option>";
		while($P=mysql_fetch_object($Proveedores))
		{
			$bgc='cccccc';if($P->calificacion_actual=='M') $bgc='ffffaa';if($P->calificacion_actual=='C') $bgc='aaffaa';
			echo "<option value='$P->id' style='background-color:#$bgc'>$P->nombre</a>";
		}
		echo "</select><br><br>
			Si el proveedor que busca no aparece en la lista, existen dos posibilidades: <br>
			<li> Que no esté creado en la base de datos. Por lo tanto puede crearlo dando click aquí: <a onclick='crear_nuevo_proveedor();' class='info'><img src='img/adicionar_proveedor.png' height='30' border='0'><span style='width:100px'>Crear Proveedor</span></a>
			<li> Que si esté creado pero no haya sido seleccionado. Puede realizar la <i><b>Selección</b></i> para que aparezca en la lista de proveedores dando click aquí: <a onclick='realizar_calificacion()' class='info'><img src='img/diploma.png' height='30' border='0'><span style='width:100px'>Realizar Selección</span></a>
			";
    }
    else echo "<b style='color:red'>No hay proveedores confiables</b><br>
				Si desea puede <b><i>Seleccionar</i></b> un proveedor específico dando click aquí: <a onclick='realizar_calificacion();' class='info'><img src='img/diploma.png' height='30' border='0'><span style='width:100px'>Realizar Calificación</span></a><br>
                Si desea crear un nuevo proveedor puede dar click aquí: <a onclick='crear_nuevo_proveedor();' class='info'><img src='img/adicionar_proveedor.png' height='30' border='0'><span style='width:100px'>Crear Proveedor</span></a><br>";
       
	}
	echo "</body>";
}

function asociar_factura_requisicion()
{
	global $idf,$iddr; // id de factura e id de detalle de requisicion
	q("update requisiciond set factura_proveedor='$idf' where id='$iddr' ");
	echo "<body><script language='javascript'>parent.cerrar();</script></body>";
}

function cambio_de_proveedor()
{
	global $idr,$idp;
	q("update requisicion set proveedor='$idp' where id='$idr' ");
	echo "<body><script language='javascript'>parent.cerrar();</script></body>";
}

function asigna_consecutivo_suno()
{
	include('inc/gpos.php');
	q("update requisiciond set consecutivo_suno='$valor' where id=$id ");
	echo "<body><script language='javascript'>alert('Consecutivo Sistema Uno $valor asignado al movimiento $id satisfactoriamente.');</script></body>";
}

function asigna_consecutivo_prov()
{
	include('inc/gpos.php');
	q("update requisiciond set consecutivo_provee='$valor' where id=$id ");
	echo "<body><script language='javascript'>alert('Consecutivo Proveedor $valor asignado al movimiento $id satisfactoriamente.');</script></body>";
}


/*
3:30 
a) desarrollar la captura de una imagen en el programa de requisiciones enseguida del consecutivo del proveedor al momento de ver el detalle de la requisicion
b) Que al momento de aprobar una factura se vaya un correo electrónico automático al proveedor anunciando la aprobación de la requisición a modo de pedido. 

Estas dos solicitudes son de tipo Desarrollo.
*/


function re_envio_requisicion_aprobada()
{
	global $id;
	$D=qo("select * from aoacol_administra.requisicion where id=$id");
	//return print_r($D);
	$Pr=qo("select * from $BDA.perfil_requisicion where id=$D->perfil");
	if($Pr->contingencia) {
		$Email_aprobador=$Pr->email_aprobacion2;$Nombre_aprobador=$Pr->aprobado_por2;
		
		}else {
			
			$Email_aprobador=$Pr->email_aprobacion;$Nombre_aprobador=$Pr->aprobado_por;
		
		}
	$Usuario=$Nombre_aprobador;$eUsuario=$Email_aprobador;
	/*if($D->estado==4) 
	{
		echo "<body>alert('El estado de esta requisición ya fue procesado y es: Calificado');</body>";
		die();
	}*/
	if($D->estado==3) 
	{
		echo "<body>alert('El estado de esta requisición ya fue procesado y es: Rechazado');</body>";
		die();
	}
	if($D->estado==2 or $D->estado==4)
	{
		// ENVIO DE CORREO AL PROVEEDOR CON EL DETALLE DE LA REQUISICION
		$Proveedor=qo("select * from aoacol_administra.proveedor where id=$D->proveedor");
		$EmailDestino='';
		if($Proveedor->contacto) 
			$Nprov=$Proveedor->contacto;
		elseif($Proveedor->nombre) 
		
		$Nprov=$Proveedor->nombre;
		
		else $Nprov='PROVEEDOR';
		
		// busqueda del correo electronico de acuerdo a la sede registrada en la requisición
		if($D->sede)
		{
			if($Sede=qo("select * from prov_sede where id=$D->sede"))
			{
				if($Sede->email) $EmailDestino=$Sede->email;
			}
		}
		// si no hay sede registrada en la requisición, se toma el email del registro principal del proveedor
		if(!$EmailDestino)
		{
			if($Proveedor->email) $EmailDestino="$Proveedor->email";
		}
		// si hay correo electronico, se envia el mensaje
		if($EmailDestino)
		{
			$Det="<table border cellspacing='0'><tr><th>Tipo de Requisicion</th><th>Item</th><th>Unidad de medida</th><th>Descripcion</th><th>Cantidad</th><th>Valor unitario</th><th>Valor</th>";
	        
			$Detalle=q("select provee_produc_serv.nombre as item,tipo.nombre as tipo,unidad_de_medida.nombre as unidad_medida,requisiciond.observaciones,requisiciond.cantidad,
                    requisiciond.requisicion,requisiciond.valor_total,requisiciond.cantidad,requisiciond.valor as valor_unitario
					from aoacol_administra.requisiciond
					inner join aoacol_administra.provee_produc_serv on requisiciond.tipo1 = provee_produc_serv.id 
					inner join aoacol_administra.tipo on provee_produc_serv.tipo = tipo.id
					inner join aoacol_administra.unidad_de_medida on provee_produc_serv.unidad_de_medida = unidad_de_medida.id
					where requisicion =$id");
	        
			while($Dt =mysql_fetch_object($Detalle ))
	        {
		    $Det.="<tr><td>$Dt->tipo</td><td>$Dt->item</td><td>$Dt->unidad_medida</td><td>$Dt->observaciones</td><td>$Dt->cantidad</td><td align='right'>$".coma_format($Dt->valor_unitario)."</td><td align='right'>$".coma_format($Dt->valor_total)."</td></tr>";
	        }
	        $Det.="</table>";
			
			
			$Res="<table border cellspacing='4'><tr><th>Resultado</th>";
        
		     $retorno=q("select requisiciond.requisicion,requisiciond.valor_total,
		            sum(requisiciond.valor_total) as resultado 
					from aoacol_administra.requisiciond
					where requisicion  =$id");
			while($Dt =mysql_fetch_object($retorno))
			{
			   $Res.="<tr><td>$".coma_format($Dt->resultado)."</td>";
			}
			$Res.="</table>";
			
			$Ciudades=qo("select requisicion.ciudad as campoCity ,ciudad.nombre as ciudad, 
                    ciudad.departamento
					from aoacol_administra.requisiciond
					inner join aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id
                    inner join aoacol_administra.ciudad on requisicion.ciudad = ciudad.codigo
                    where requisiciond.requisicion = $id limit 1");
			$ciudad = $Ciudades->ciudad;
			$departamento = $Ciudades->departamento;
			
			$ER=qo("select requisicion.placa,
     	concat( oficina.centro_operacion,' ',oficina.nombre) as centrodeoperacion,aseguradora.ccostos_uno as centrocosto,aseguradora.nombre as ASEGURADORA, ubicacion.flota 
				 from aoacol_administra.requisiciond 
				 LEFT OUTER JOIN aoacol_administra.ccostos_uno on requisiciond.centro_costo = ccostos_uno.codigo 
				 LEFT OUTER JOIN aoacol_administra.requisicion on requisiciond.requisicion = requisicion.id 
				 LEFT OUTER JOIN aoacol_aoacars.ubicacion on requisicion.ubicacion = ubicacion.id 
				 inner JOIN aoacol_aoacars.oficina on ubicacion.oficina = oficina.id
				 LEFT OUTER JOIN aoacol_aoacars.aseguradora on  ubicacion.flota = aseguradora.id where requisicion.id = $id");
				 
		
             $verificar=qo("select ubicacion from aoacol_administra.requisicion where id = $id");
				
				if($verificar->ubicacion == 0){
					include('pdf/requisicionAdm.php');
				}else{
					include('pdf/requisicionOpe.php');
				}
				
				 $correo = $eUsuario;

                $attachment = $pdf->Output('Requisiciondecompra.pdf', 'S');
				
				
				//require("inc/PHPMailer-master/PHPMailerAutoload.php");
				
				$mail = new PHPMailer;
				$mail->IsSMTP();                                    // tell the class to use SMTP
				$mail->SMTPAuth   = true;                           // enable SMTP authentication
				$mail->Port       = 25;                             // set the SMTP server port
				$mail->Host       = "mail.aoasoluciones.com";           // SMTP server
				$mail->Username   = "contacto@aoasoluciones.com";  // SMTP server username
				$mail->Password   = "CorreoAoa2019*.*";            // SMTP server password
				$mail->SMTPSecure = 'tls';
				
				
				

				//$mail->IsSendmail();  // tell the class to use Sendmail
				//$mail->AddReplyTo("aherrera@akiris.net","Anibal Herrera");

				$mail->setFrom('sistemas@aoacolombia.com','Sistema de Control Operativo');
				//$mail->From       = "no-responder@acinco.com.co";
				//$mail->FromName   = utf8_decode("Protección Móvil.");
				$mail->AddAddress($EmailDestino);
				
				$mail->addCC("");
                $mail->addCC("sergiocastillo@aoacolombia.com");
					$mail->addCC($eUsuario); 
				


				$mail->AddStringAttachment($attachment, 'Requisiciondecompra.pdf');
				$mail->WordWrap   = 80; // set word wrap
				
				$mail->Subject = "CONFIRMACION DE APROBACION DE PEDIDO $id";

				
				
				
				$body  ="			
				<html>
			  <head>
				<meta name='viewport' content='width=device-width, initial-scale=1.0' />
				<meta name='x-apple-disable-message-reformatting' />
				<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
				<title></title>
				<style type='text/css' rel='stylesheet' media='all'>
				/* reset */

			*
			{
				border: 0;
				box-sizing: content-box;
				color: inherit;
				font-family: inherit;
				font-size: inherit;
				font-style: inherit;
				font-weight: inherit;
				line-height: inherit;
				list-style: none;
				margin: 0;
				padding: 0;
				text-decoration: none;
				vertical-align: top;
			}

			/* content editable */

			*[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

			*[contenteditable] { cursor: pointer; }

			*[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 1em 0.5em #DEF; }

			span[contenteditable] { display: inline-block; }

			/* heading */

			h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }

			/* table */

			table { font-size: 75%; table-layout: fixed; width: 100%; }
			table { border-collapse: separate; border-spacing: 2px; }
			th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
			th, td { border-radius: 0.25em; border-style: solid; }
			th { background: #EEE; border-color: #BBB; }
			td { border-color: #DDD; }

			/* page */

			html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; padding: 0.5in; }
			html { background: #fff; cursor: default; }

			body { box-sizing: border-box; height:nome; margin: 0 auto; overflow: hidden; padding: 0.5in; width:nome; }
			body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }

			/* header */

			header { margin: 0 0 3em; }
			header:after { clear: both; content: ''; display: table; }

			header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
			header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
			header address p { margin: 0 0 0.25em; }
			header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
			header img { max-height: 100%; max-width: 100%; }
			header input { cursor: pointer; -ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity=0)'; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

			/* article */

			article, article address, table.meta, table.inventory { margin: 0 0 3em; }
			article:after { clear: both; content: ''; display: table; }
			article h1 { clip: rect(0 0 0 0); position: absolute; }

			article address { float: left; font-size: 125%; font-weight: bold; }

			/* table meta & balance */

			table.meta, table.balance { float: right; width: 36%; }
			table.meta:after, table.balance:after { clear: both; content: ''; display: table; }

			/* table meta */

			table.meta th { border: 1px solid black; border-color: rgba(118,136,29,1);width: 40%; }
			table.meta td {border: 1px solid black; border-color: rgba(118,136,29,1); width: 60%; }

			/* table items */
			.titulo{
			font-size:15px;
			font-weight:900;
			color:rgba(118,136,29,1);
			}
			table.inventory { clear: both; width: 100%;border: 1px solid black; border-color: rgba(118,136,29,1); }
			table.inventory th {  background-color:rgba(118,136,29,1); font-weight: bold; text-align: center; color:#fff; }

			table.inventory td:nth-child(1) { width: 26%; }
			table.inventory td:nth-child(2) { width: 38%; }
			table.inventory td:nth-child(3) { text-align: right; width: 12%; }
			table.inventory td:nth-child(4) { text-align: right; width: 12%; }
			table.inventory td:nth-child(5) { text-align: right; width: 12%; }

			/* table balance */

			table.balance th, table.balance td { width: 50%; }
			table.balance td { text-align: right; }

			/* aside */

			aside h1 { border: none; border-width: 0 0 1px; margin: 0 0 1em; }
			aside h1 { border-color: #999; border-bottom-style: solid; }

			/* javascript */

			.add, .cut
			{
				border-width: 1px;
				display: block;
				font-size: .8rem;
				padding: 0.25em 0.5em;	
				float: left;
				text-align: center;
				width: 0.6em;
			}
				p.sub {
				  font-size: 13px;
				}
				/* Utilities ------------------------------ */
				
				.align-right {
				  text-align: right;
				}
				
				.align-left {
				  text-align: left;
				}
				
				.align-center {
				  text-align: center;
				}
				/* Buttons ------------------------------ */
				
				.button {
				  background-color: #3869D4;
				  border-top: 10px solid #3869D4;
				  border-right: 18px solid #3869D4;
				  border-bottom: 10px solid #3869D4;
				  border-left: 18px solid #3869D4;
				  display: inline-block;
				  color: #FFF;
				  text-decoration: none;
				  border-radius: 3px;
				  box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
				  -webkit-text-size-adjust: none;
				  box-sizing: border-box;
				}
				
				.button--green {
				  background-color: #22BC66;
				  border-top: 10px solid #22BC66;
				  border-right: 18px solid #22BC66;
				  border-bottom: 10px solid #22BC66;
				  border-left: 18px solid #22BC66;
				}
				
				.button--red {
				  background-color: #FF6136;
				  border-top: 10px solid #FF6136;
				  border-right: 18px solid #FF6136;
				  border-bottom: 10px solid #FF6136;
				  border-left: 18px solid #FF6136;
				}
				
				@media only screen and (max-width: 500px) {
				  .button {
					width: 100% !important;
					text-align: center !important;
				  }
				}
				/* Attribute list ------------------------------ */
				
				.attributes {
				  margin: 0 0 21px;
				}
				
				.attributes_content {
				  background-color: #F4F4F7;
				  padding: 16px;
				}
				
				.attributes_item {
				  padding: 0;
				}
				/* Related Items ------------------------------ */
				
				.related {
				  width: 100%;
				  margin: 0;
				  padding: 25px 0 0 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				}
				
				.related_item {
				  padding: 10px 0;
				  color: #CBCCCF;
				  font-size: 15px;
				  line-height: 18px;
				}
				
				.related_item-title {
				  display: block;
				  margin: .5em 0 0;
				}
				
				.related_item-thumb {
				  display: block;
				  padding-bottom: 10px;
				}
				
				.related_heading {
				  border-top: 1px solid #CBCCCF;
				  text-align: center;
				  padding: 25px 0 10px;
				}
				/* Discount Code ------------------------------ */
				
				.discount {
				  width: 100%;
				  margin: 0;
				  padding: 24px;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  background-color: #F4F4F7;
				  border: 2px dashed #CBCCCF;
				}
				
				.discount_heading {
				  text-align: center;
				}
				
				.discount_body {
				  text-align: center;
				  font-size: 15px;
				}
				/* Social Icons ------------------------------ */
				
				.social {
				  width: auto;
				}
				
				.social td {
				  padding: 0;
				  width: auto;
				}
				
				.social_icon {
				  height: 20px;
				  margin: 0 8px 10px 8px;
				  padding: 0;
				}
				/* Data table ------------------------------ */
				
				.purchase {
				  width: 100%;
				  margin: 0;
				  padding: 35px 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				}
				
				.purchase_content {
				  width: 100%;
				  margin: 0;
				  padding: 25px 0 0 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				}
				
				.purchase_item {
				  padding: 10px 0;
				  color: #51545E;
				  font-size: 15px;
				  line-height: 18px;
				}
				
				.purchase_heading {
				  padding-bottom: 8px;
				  border-bottom: 1px solid #EAEAEC;
				}

				.purchase_heading p {
				  margin: 0;
				  color: #85878E;
				  font-size: 12px;
				}
				
				.purchase_footer {
				  padding-top: 15px;
				  border-top: 1px solid #EAEAEC;
				}
				
				.purchase_total {
				  margin: 0;
				  text-align: right;
				  font-weight: bold;
				  color: #333333;
				}
				
				.purchase_total--label {
				  padding: 0 15px 0 0;
				}
				
				body {
				  background-color: #F4F4F7;
				  color: #51545E;
				}
				
				p {
				  color: #51545E;
				}
				
				p.sub {
				  color: #6B6E76;
				}
				
				.email-wrapper {
				  width: 100%;
				  margin: 0;
				  padding: 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  background-color: #F4F4F7;
				}
				
				.email-content {
				  width: 100%;
				  margin: 0;
				  padding: 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				}
				/* Masthead ----------------------- */
				
				.email-masthead {
				  padding: 25px 0;
				  text-align: center;
				}
				
				.email-masthead_logo {
				  width: 94px;
				}
				
				.email-masthead_name {
				  font-size: 16px;
				  font-weight: bold;
				  color: #A8AAAF;
				  text-decoration: none;
				  text-shadow: 0 1px 0 white;
				}
				/* Body ------------------------------ */
				
				.email-body {
				  width: 100%;
				  margin: 0;
				  padding: 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  background-color: #FFFFFF;
				}
				
				.email-body_inner {
				  width: 570px;
				  margin: 0 auto;
				  padding: 0;
				  -premailer-width: 570px;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  background-color: #FFFFFF;
				}
				
				.email-footer {
				  width: nome;
				  margin: 0 auto;
				  padding: 0;
				  -premailer-width: 570px;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  text-align: center;
				}
				
				.email-footer p {
				  color: #6B6E76;
				}
				
				.body-action {
				  width: 100%;
				  margin: 30px auto;
				  padding: 0;
				  -premailer-width: 100%;
				  -premailer-cellpadding: 0;
				  -premailer-cellspacing: 0;
				  text-align: center;
				}
				
				.body-sub {
				  margin-top: 25px;
				  padding-top: 25px;
				  border-top: 1px solid #EAEAEC;
				}
				
				.content-cell {
				  padding: 35px;
				}
			.add, .cut
			{
				background: #9AF;
				box-shadow: 0 1px 2px rgba(0,0,0,0.2);
				background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
				background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
				border-radius: 0.5em;
				border-color: #0076A3;
				color: #FFF;
				cursor: pointer;
				font-weight: bold;
				text-shadow: 0 -1px 2px rgba(0,0,0,0.333);
			}
			input[type=text]:focus {
			  border: 3px solid #555;
			}
			.add { margin: -2.5em 0 0; }

			.add:hover { background: #00ADEE; }

			.cut { opacity: 0; position: absolute; top: 0; left: -1.5em; }
			.cut { -webkit-transition: opacity 100ms ease-in; }

			tr:hover .cut { opacity: 1; }

			@media print {
				* { -webkit-print-color-adjust: exact; }
				html { background: none; padding: 0; }
				body { box-shadow: none; margin: 0; }
				span:empty { display: none; }
				.add, .cut { display: none; }
			}

			@page { margin: 0; }
				</style>
				<!--[if mso]>
				<style type='text/css'>
				  .f-fallback  {
					font-family: Arial, sans-serif;
				  }
				</style>
			  <![endif]-->
			  </head>

					<header>
					   <table  style='margin-bottom: 2%;   text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
							  <tr>
								<td    style=' text-align: center;'  class='content-cell' align='center' >
									   <img   style=' text-align: center;'  src='https://app.aoacolombia.com/Administrativo/img/banner-vehiculo-sustituto-AOA.jpg'>
								</td>
							  </tr>

							</table>
							<br>
							  <table   style='margin-bottom: 6%;  text-align: center;' align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
							  <tr>
								<td  style='margin-bottom: 3%; '  class='content-cell' align='center' >
								   <b  style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);' class='titulo'><b>Estimado Se&ntilde;or $Nprov,</b></b><br> </td>
							  </tr>

							</table>
						<address  style='margin-bottom: 1%; ' contenteditable>
							<h3 class='titulo'  style='margin-bottom: 5%;  color: 1px solid rgba(118,136,29,1);'  >Reciba cordial saludo.</h3>
						
			
								Por medio de este correo se le notifica formalmente sobre la 
								aprobaci&oacute;n de <b><u style='font-weight:600;'>Requisici&oacute;n Interna N&uacute;mero $id</u>
								</b>de la ciudad de&nbsp;<b>$ciudad</b>&nbsp;con el departamento&nbsp;<b>$departamento</b>&nbsp; 
								en nuestra empresa para adquirir los siguientes bienes/servicios:
						</address>
						
					</header>
					<br>
					<hr>
					<hr>
					<article style='margin-bottom: 3%; '>
					
					  $Det
					</article>
					
					<p style='margin-top: 3%; color: 1px solid rgba(118,136,29,1);' class='titulo' >Valor total<br></p>
					<address  style='margin-bottom: 3%; ' contenteditable>
					  $Res
					</address>	
					
					 <article style='margin-bottom: 3%; margin-top: 3%;' >	

               
					 <p style='margin-bottom: 3%; margin-top: 3%;' >Adjunto a esta notificaci&oacute;n, remitimos la Requisici&oacute;n en formato PDF para que sea adjuntada a su factura o cuenta de cobro. </p>
						
					</article>
				
					<address  style='margin-bottom: 2%; ' contenteditable>
						Agradecemos de antemano su gentil atenci&oacute;n.
					</address>	
					
					<address  style='margin-bottom: 1%; ' contenteditable>
						Cordialmente,
					</address>	
					 
					 <address  style='margin-bottom: 5%; ' contenteditable>
						$Usuario
					</address>
													

					<aside>
						   <table class='email-footer'    align='center' width='570' cellpadding='0' cellspacing='0' role='presentation'>
							  <tr>
								<td   style=' text-align: center;'  class='content-cell' align='center' >
								  <p  style=' text-align: center;'  class='f-fallback sub align-center'>
									
									
									<br> AOA COLOMBIA S.A.S
									<br>+571 7560510
									<br>$eUsuario
									<br>http://www.aoacolombia.com
									<br><i style='font-size:8px'>Mensaje autom&aacute;tico del sistema de Requisiciones de AOA Colombia S.A. desarrollado por Tecnologia AOA. (it@aoacolombia.co)</i>
								  </p>
								<img   style=' text-align: center;'  class='f-fallback email-masthead_name' src='https://app.aoacolombia.com/Administrativo/img/logo-footer-mail-AOA.jpg'>
								</td>
							  </tr>

							</table>
					</aside>
				</body>
			</html>";
				
									
				
			   $mail->MsgHTML($body);
				$mail->IsHTML(true);
				if($mail->send()){
		      	echo "<body><script language='javascript'>alert('Mensaje enviado satisfactoriamente $EmailDestino');</script></body>";
				}else{
		     	echo "<body><script language='javascript'>alert('Mensaje enviado satisfactoriamente $EmailDestino');</script></body>";
				}
			
			
			echo"$EmailDestino";
				exit();
		}
		else echo "<body><script language='javascript'>alert('El proveedor no tiene correo electr&oacute;nico definido.');</script></body>";
	}
	else echo "<body><script language='javascript'>alert('La requisici&oacute;n no est&aacute; en estado APROBADA.');</script></body>";
}


function adicionar_observaciones()
{
	include('inc/gpos.php');
	sesion();
	html();
	echo "<title>ADICION DE OBSERVACIONES</title>
			<script language='javascript'>
				function cerrar(){window.close();void(null);opener.recargar();}
			</script>
		</head>
		<body><script language='javascript'>centrar(600,400);</script>
			<form action='zrequisicion.php' target='Oculto_obsreq' method='POST' name='forma' id='forma'>
				Observaciones:<br>
				<textarea name='obs' id='obs' style='width:100%;height:70%;' placehoder='Observaciones...'></textarea><br>
				<input type='button' class='button' name='btn_guardar' id='btn_guardar' value='CONTINUAR' onclick=\"valida_campos('forma','obs');\">
				<input type='button' class='button' name='btn_cancelar' id='btn_cancelar' value='CANCELAR' onclick=\"window.close();void(null);\">
				<input type='hidden' name='id' value='$id'>
				<input type='hidden' name='Acc' value='adicionar_observaciones_ok'>
			</form>
			<iframe name='Oculto_obsreq' id='Oculto_obsreq' style='display:none' width='1' height='1'></iframe>
		</body>";
}

function adicionar_observaciones_ok()
{
	global $app;
	include('inc/gpos.php');
	sesion();
	$Usuario=$_SESSION['Nick'].'-'.$_SESSION['Nombre'];
	$Ahora=date('Y-m-d H:i:s');
	q("UPDATE requisicion SET observaciones=concat(observaciones,\"\n[$Usuario : $Ahora] $obs\") WHERE id=$id ");
	graba_bitacora('requisicion','M',$id,"Adiciona observaciones");
	echo "<body><script language='javascript'>parent.cerrar();</script></body>";
}

?>