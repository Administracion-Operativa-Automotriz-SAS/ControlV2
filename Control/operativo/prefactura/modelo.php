<?php
include('conexion.php');
session_start();

switch ($_GET['sw']) {

   case '0':
      $ase = $_GET['ase'];
      $sini = $_GET['sini'];
      $desc = $_GET['desc'];
      $fec = $_GET['fec'];
      $sol = $_GET['sol'];
      if ($ase != '') {
         $aseguradora = ' and b.aseguradora = ' . $ase;
      }
      if ($sini != '') {
         $siniestro = ' and b.id = ' . $sini;
      }
      if ($desc != '') {
         $descripcion = ' and a.descripcion like "%' . $desc . '%" ';
      }
      if ($fec != '') {
         $fecha = ' and a.fecha_solicitud like "' . $fec . '%" ';
      } else {
         $fecha = ' and a.fecha_solicitud like "' . date('Y') . '%" ';
      }
      if ($sol != '') {
         $solicitado = ' and a.solicitado_por like "%' . $sol . '%" ';
      }

      if (isset($_GET['page'])) {
         $page = $_GET['page'];
      } else {
         $page = 1;
      }
      $request = mysql_query("select count(*) from solicitud_factura a, siniestro b where a.siniestro=b.id and a.procesado_por='' and a.prefactura='0'  AND a.fecha_solicitud LIKE '" . date('Y') . "%' $aseguradora $siniestro $fecha $solicitado $descripcion");

      if ($request) {
         $request = mysql_fetch_row($request);
         $num_items = $request[0];
      } else {
         $num_items = 0;
      }
      $rows_by_page = 10;

      $last_page = ceil($num_items / $rows_by_page);
      $limit = 'LIMIT ' . ($page - 1) * $rows_by_page . ',' . $rows_by_page;

      $query = mysql_query("select *,a.id as ids, T_concepto_fac(a.concepto) as conceptos, T_aseguradora(b.aseguradora) as ase, REPLACE(descripcion,',',' ') as descripcion from solicitud_factura a, siniestro b where a.siniestro=b.id and a.procesado_por='' and a.prefactura='0'  AND a.fecha_solicitud LIKE '" . date('Y') . "%' $aseguradora $siniestro $fecha $solicitado $descripcion  $limit");
      $p = '';
      while ($row = mysql_fetch_array($query)) {
         $p[] = $row;
      }
      $r = array();
      $r['datos'] = $p;
      $r['lastpage'] = $last_page;
      $r['page'] = $page;
      $r['cant'] = $num_items;
      //$data = ["datos"=>$p, "last_page"=>$last_page, "pagina"=>$page];


      echo json_encode($r);
      break;

   case '1':
      $query = mysql_query("select * from aseguradora where activo=1  order by nombre asc ");
      $p = '';
      while ($row = mysql_fetch_array($query)) {
         $p[] = $row;
      }
      echo json_encode($p);

      break;
   case '2':
      if (isset($_GET['page'])) {
         $page = $_GET['page'];
      } else {
         $page = 1;
      }
      $request = mysql_query("select * from prefactura where factura='0' ");

      if ($request) {
         $request = mysql_fetch_row($request);
         $num_items = $request[0];
      } else {
         $num_items = 0;
      }
      $rows_by_page = 10;

      $last_page = ceil($num_items / $rows_by_page);
      $limit = 'LIMIT ' . ($page - 1) * $rows_by_page . ',' . $rows_by_page;
      $query = mysql_query("select *,t_cliente(cliente) AS cli,id as pre from prefactura where factura='0'  $limit");
      $p = '';
      while ($row = mysql_fetch_array($query)) {
         $p[] = $row;
      }
      $r = array();
      $r['datos'] = $p;
      $r['lastpage'] = $last_page;
      $r['page'] = $page;
      $r['cant'] = $num_items;

      echo json_encode($r);

      break;
   case 3:
      $id = $_GET['id'];
      $query = mysql_query("select *,t_cliente(cliente) AS cli, a.id as pre from prefactura a, cliente b where a.cliente=b.id and a.id= $id ");
      $row = mysql_fetch_array($query);
      $p[] = $row;

      $queryLIST = mysql_query("select *, a.id as sol from solicitud_factura a, concepto_fac b where a.concepto=b.id and a.prefactura = $id ");
      $l = '';
      while ($row2 = mysql_fetch_array($queryLIST)) {
         $l[] = $row2;
      }
      $sini = $row['siniestro'];
      $ciudad = $row['ciudad'];
      $queryciudad = mysql_query("SELECT * FROM ciudad WHERE codigo= $ciudad ");
      $rowc = mysql_fetch_array($queryciudad);

      $querysiniestro = mysql_query("select * from siniestro where id = $sini ");
      $rows = mysql_fetch_array($querysiniestro);
      $s[] = $rows;
      $p['nameciudad'] = $rowc['nombre'];

      $r = array();
      $r['datos'] = $p;
      $r['items'] = $l;
      $r['siniestro'] = $s;

      echo json_encode($r);


      break;
   case 4:
      $id = $_GET['id'];

      //consulta de la prefactura
      $query = mysql_query("select *,t_cliente(cliente) AS cli, a.id as pre from prefactura a, cliente b where a.cliente=b.id and a.id= $id ");
      $row = mysql_fetch_array($query);
      $p[] = $row;
      //consulta de item de la factura
      $queryLIST = mysql_query("select * from solicitud_factura a, concepto_fac b where a.concepto=b.id and a.prefactura = $id ");
      $total = 0;
      $iva = 0;
      $stotal = 0;
      $orden = $row['orden'];
      while ($row2 = mysql_fetch_array($queryLIST)) {
         $precio = $row2['valor'] * $row2['cantidad'];
         
         if ($row2['porc_iva'] == '19.00') {
            $stotal += $precio;
            $st =  $precio * 1.19;
            $iva += $st - $precio;
            $total += $st;
         } else {
            $stotal += $precio;
            $st =  $precio;
            $iva += $st - $precio;
            $total += $st;
         }
      }
      $sini = $row['siniestro'];
      $ciudad = $row['ciudad'];
      
      $querysiniestro = mysql_query("select * from siniestro where id = $sini ");
      $rows = mysql_fetch_array($querysiniestro);
      $aseguradora = $row['seguro'];

      // se consulta el ultimo consecutivo
      $queryfactura = mysql_query("SELECT consecutivo FROM factura ORDER BY id DESC LIMIT 1 ");
      $fila = mysql_fetch_array($queryfactura);

      $variable = $fila[0];
      $resultado = str_replace("IS", "", $variable);
      $factura = 'IS'.((int)$resultado + 1);
      $user = $_SESSION['Nombre'];
      $fecha = date("Y-m-d H:i:s");
      if ($_SESSION['Nombre']) {


         // se inserta la factura 
         mysql_query("insert into factura (cliente,fecha_emision,fecha_vencimiento,consecutivo,autorizadopor,subtotal,iva,total,aseguradora,siniestro,orden) select cliente,fecha_emision,fecha_vencimiento,'$factura','$user',$stotal,$iva,$total,'$aseguradora','$sini',orden from prefactura where id=$id ");
         $fac = mysql_insert_id();
         $error = mysql_error();

         $ultimo = mysql_query("SELECT MAX(id) FROM factura");
         $ult = mysql_fetch_array($ultimo);
         $fac = $ult[0];
         
         // se inserta el detalle de la factura
         mysql_query("insert into facturad (factura,concepto,cantidad,unitario,total,descripcion,iva) select '$fac', concepto,cantidad,valor,((cantidad*valor)+iva),descripcion,iva from solicitud_factura where prefactura=$id  ");

         
         mysql_query("update prefactura set factura='$factura' where id= $id ");
         mysql_query("update solicitud_factura set factura='$fac', procesado_por='$user', fecha_proceso='$fecha' where prefactura= $id ");
         if($orden != 0) {
            mysql_query("insert into factura_masiva (factura,id_factura,siniestro) select '$factura','$fac', siniestro from solicitud_factura where prefactura= $id ");
         }
         $r = array();
         $r['datos'] = $p;
         $r['items'] = $error;
         $r['msj'] = 'Se ha generado la factura No. '.$factura;
         $r['usuario'] = $_SESSION['Nombre'];
         $r['status'] = true;
      }else{
         $r = array();
         $r['msj'] = 'Debes de iniciar sesion';
         $r['status'] = false;
      }

      echo json_encode($r);
      break;

      case 5:
         $id = $_GET['id'];
         $prefactura = $_GET['prefactura'];
         
         $pre = $_GET['pre'];
         $queryLIST = mysql_query("select * from solicitud_factura a, concepto_fac b where a.concepto=b.id and a.id = $id ");
         $row2 = mysql_fetch_array($queryLIST);
         if ($row2['porc_iva'] == '19.00') {
            $stotal = $pre;
            $st =  $pre * 1.19;
            $iva = $st - $pre;
            $total = $st;
         }else{
            $stotal = $pre;
            $st =  $pre;
            $iva = $st - $pre;
            $total = $st;
         }
         mysql_query("update solicitud_factura set valor='$pre', iva='$iva' where id= $id ");

         $queryLIST2 = mysql_query("select * from solicitud_factura a, concepto_fac b where a.concepto=b.id and a.prefactura = $prefactura ");
      $total = 0;
      $iva = 0;
      $stotal = 0;
      while ($row2 = mysql_fetch_array($queryLIST2)) {
         if ($row2['porc_iva'] == '19.00') {
            $stotal += $row2['valor'];
            $st =  $row2['valor'] * 1.19;
            $iva += $st - $row2['valor'];
            $total += $st;
         } else {
            $stotal += $row2['valor'];
            $st =  $row2['valor'];
            $iva += $st - $row2['valor'];
            $total += $st;
         }
      }
      mysql_query("update prefactura set subtotal='$stotal', iva='$iva', total='$total' where id= $prefactura ");
         $r = array();
         $r['msj'] = 'Se ha actualizado con exito';
         $r['status'] = true;
         echo json_encode($r);
         break;
         case 6:
            $ase = $_GET['ase'];

            $query = mysql_query("select *, b.id as cli from aseguradora a, cliente b where a.nit=b.identificacion and a.id = $ase ");
            $f = mysql_fetch_array($query);
            $p[] = $f;
            $dia = $f['termino_pago_dias'];
            $date = $request['fecreg'];
                $mod_date = strtotime($date."+ $dia days");
                $vencimiento =  date("Y-m-d",$mod_date);
         
            $r = array();
            $r['datos'] = $p;
            $r['msj'] = 'datos cargados';
            $r['ven'] = $vencimiento;
            echo json_encode($r);

          


            break;
            case 7:
               $cli = $_GET['cli'];
               $fec = $_GET['fec'];
               $ven = $_GET['ven'];
               $ase = $_GET['ase'];

               $sub = $_GET['sub'];
               $iva = $_GET['iva'];
               $gt = $_GET['gt'];
               $orden = $_GET['orden'];

               mysql_query("insert into prefactura (cliente, fecha_emision, fecha_vencimiento,subtotal,iva,total,siniestro,seguro,orden) values ($cli,'$fec','$ven',$sub,$iva,$gt,'0','$ase','$orden')");
               $fac = mysql_insert_id();
               $error = mysql_error();
               $r = array();
              $r['pre'] = $fac;
              $r['msj'] = 'datos guardado con exito'.$fac;
              $r['status'] = true;
              echo json_encode($r);


               break;
               case 8:
                  $id = $_GET['id'];
                  $pre = $_GET['pre'];
                  $iva = $_GET['iva'];
                  mysql_query("update solicitud_factura set prefactura= $pre, iva= $iva where id = $id ");

                  break;
                  case 9:

                     $query = mysql_query("select * from tanqueos where estado='0' limit 1000  ");
                     $p = '';
                     while ($row = mysql_fetch_array($query)) {
                        
                        $p[] = $row;
                     }
                     $r = array();
                     $r['datos'] = $p;
               
                     echo json_encode($r);
                     

                     break;
                     case 10:
                        $id = $_GET['id'];
                        $query = mysql_query("select * from tanqueos where estado='0' and id='$id'  ");
                        $row = mysql_fetch_array($query);
                        $km_uno = $row['kilometro'];
                        $galon = $row['galon'];
                        $precio = $row['valorgl'];
                        $total = $row['total'];
                        $rodamiento = $row['rodamiento'];
                        $fecha = trim(substr($row['fechaproceso'],0,10));
                        $cadena = explode("/",$fecha);
                        $numero = $cadena[0];
                        $numeroConCeros = str_pad($numero, 2, "0", STR_PAD_LEFT);
                        $newDate = $cadena[2].'-'.$cadena[1].'-'.$numeroConCeros;
                        $obs = str_replace(","," ", $row['descripcion']);

                        $result = mysql_query("SELECT odometro_final, b.id, a.flota, a.ultima_ubicacion, b.vehiculo FROM vehiculo a, ubicacion b WHERE a.id=b.vehiculo AND a.placa='".$row['placa']."' ORDER BY fecha_final DESC LIMIT 1");
                        $o = mysql_fetch_array($result);
                        $km_mayor = $o['odometro_final'];
                        $flota = $o['flota'];
                        $oficina = $o['ultima_ubicacion'];
                        $vehiculo = $o['vehiculo'];
                        $idu = $o['id'];
                       
                        mysql_query("update tanqueos SET estado='1' WHERE id = '$id' ");
                        mysql_query("insert into ubicacion (oficina,vehiculo,fecha_inicial, fecha_final,estado,observaciones, odometro_inicial, odometro_final,obs_mantenimiento, flota,galon,valor_galon,total_galon,rodamiento) value ($oficina,$vehiculo,'$newDate','$newDate','109','','$km_uno','$km_uno','$obs',$flota,'$galon','$precio','$total','$rodamiento')");
                        $error = mysql_error();
                        if($error) {
                           echo "Error: " . $error;
                        }else{
                           if($km_uno > $km_mayor){
                              mysql_query("update ubicacion SET odometro_final = '$km_uno' WHERE id = '$idu' ");
                              
                              echo "id a actualizar $km_uno km mayor= $km_mayor id=".$row['id'].' fecha:'.$fecha.' after='.$newDate;
                           }else{
                              echo "no se $km_mayor id=".$row['id'];
                           }
                        }
                        

                        break;
}
