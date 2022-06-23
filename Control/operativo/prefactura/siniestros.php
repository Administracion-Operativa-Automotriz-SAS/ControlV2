<?php 
include('conexion.php');
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!--     <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script  src="https://code.jquery.com/jquery-3.3.1.min.js"
integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
crossorigin="anonymous"></script> -->
  <script src="funciones.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script> -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
  <link type="text/css" href="demo_table.css" rel="stylesheet" />
  <script type="text/javascript" language="javascript" src="jquery.dataTables.js"></script>
  <title>Registro Prefactura</title>
</head>

<body>
 
  <div class="tab-content" id="myTabContent">
    
    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
      <div class="invoice p-3 mb-3">
        <div class="row">
          <div class="col-12 table-responsive">
            <br>
            <h4>
              <i class="fas fa-globe"></i> Lista de Siniestros facturados.
              <small class="float-right">Fecha: <?php echo date("Y-m-d") ?></small>
            </h4>
            <table class="table table-striped" id="tabla2">
              <thead class="thead-dark">
                <tr>
          
                  <th>Siniestro</th>
                  <th>Factura</th>
                  <th>Ver</th>
                </tr>
              </thead>
              <tbody id="mostrar_prefacturas">
                  <?php 
                  $resultado = mysql_query("SELECT * FROM factura_masiva where id_factura = ".$_GET['factura']);
                  while ($row = mysql_fetch_array($resultado)){
                    echo "<tr><td>".$row['siniestro']."</td><td>".$row['factura']."</td><td><a href='../zsiniestro.php?Acc=buscar_siniestro&id=".$row['siniestro']."'>Ver Siniestro</a></td>";
                  }

                   ?>

              </tbody>
            </table>
            
          </div>
          <!-- /.col -->
        </div>
      </div>
    </div>

  </div>

</body>




<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
<script src="funciones.js"></script>

<script>

</script>

</html>