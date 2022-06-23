<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="funciones.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

  <title>Subida Tanqueos</title>
</head>

<body>
  <div class="card p-6">

 
  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Importar Registros</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false" onclick="importados()">Registros Importados</a>
    </li>

  </ul>
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <form id="subida">
      <div class="invoice p-3 mb-3">
        <!-- title row -->
        <div class="row">
          <div class="col-12 p-3">
            <h4>
              <i class="fas fa-upload"></i> Importar Datos.
              <small class="float-right">Fecha: <?php echo date("Y-m-d") ?></small>
            </h4>
          </div>
          <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">

          <!-- /.col -->
          <div class="col-sm-8 invoice-col">

         
            <b>Archivo .csv</b> <input type="file" name="foto" class="form-control" id="foto" >
           
          </div>
          <!-- /.col -->
        </div>
        <br>
        <!-- /.row -->

        <div class="row">
          <!-- accepted payments column -->
          <div class="col-6">
            <p class="lead">Verificar Datos ingresados antes de guardar:
             <!--  <textarea name="" id="msg2" cols="30" rows="10" class="form-control" placeholder="Log de datos"></textarea> -->
              <div id="msg2"></div></p>
          </div>
        </div>
        <!-- /.row -->

        <!-- this row will not appear when printing -->
        <div class="row no-print">
          <div class="col-12">
        
            <button  class="btn btn-success float-right" id="botonload" ><i class="fas fa-upload"></i>
              Importar Archivos
            </button>

          </div>
        </div>
      </div>
</form>
    </div>
    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
      <div class="invoice p-3 mb-3">
        <div class="row">
          <div class="col-12 table-responsive">
            <br>
            <h4>
              <i class="fas fa-globe"></i> Archivos importados.
              <small class="float-right">Fecha: <?php echo date("Y-m-d") ?></small>
            </h4>
            <table class="table table-striped" id="tabla2">
              <thead class="thead-dark">
                <tr>
                  <th><input type="checkbox" onclick="marcar(this);" /> Items</th>
                  <th>Placa</th>
          
                  <th>Descripcion</th>
                  <th>Kilometraje</th>
                 
                  <th>Tanqueo Gl</th>
                  <th>Valor Gl</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody id="mostrar_prefacturas">


              </tbody>
            </table>
            <input type="hidden" id="count" disabled>
            
            <button onclick="addubicacion()" class="btn btn-primary" id="btns">Subir a ubicacion</button>
          </div>
          <!-- /.col -->
        </div>
      </div>
    </div>

  </div>
  </div>
</body>
<div class="modal fade" id="myModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Lista de Solicitudes</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
          <!-- <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_lista_paises"> -->
          <thead class="thead-dark">
            <tr>
              <th>Siniestro</th>
              <th>Descripcion</th>
              <th>Cant</th>
              <th>Valor</th>
              <th>Fecha Solicitud</th>
              <th>Solicitado por</th>
              <th>Agregar</th>
            </tr>
          </thead>
          <tr>
            <th><input type="text" id="sini" style="width: 100%;" onchange="solicitudes(1)"></th>
            <th><input type="text" id="desc" style="width: 100%;" onchange="solicitudes(1)"></th>
            <th></th>
            <th></th>
            <th><input type="date" id="fec" style="width: 100%;" onchange="solicitudes(1)"></th>
            <th><input type="text" id="sol" style="width: 100%;" onchange="solicitudes(1)"></th>
            <th></th>
          </tr>
          <tbody id="listado">
            <tr>
              <td colspan="7">Cargando datos...</td>
            </tr>

          </tbody>
        </table>

        <ul class="pagination pagination-sm" id="pag">
          <li class="page-item"><a class="page-link" href="#">
              << </a>
          </li>
          <li class="page-item"><a class="page-link" href="#">
              < </a>
          </li>
          <li class="page-item"><a class="page-link" href="#"> > </a></li>
          <li class="page-item"><a class="page-link" href="#"> >> </a></li>
        </ul>
        <span id="info"></span>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="ModalLoad">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Subiendo registros..</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
      <div class="progress" id="progreso">
              <div class="progress-bar" role="progressbar" aria-valuenow="0"aria-valuemin="0" aria-valuemax="100" style="width:0%"> 0% </div>
            </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
<script src="funciones.js"></script>

<script>
  aseguradoras(1);
</script>

</html>