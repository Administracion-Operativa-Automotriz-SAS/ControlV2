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
  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Formulario</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false" onclick="prefacturas()">Pre-Facturas</a>
    </li>

  </ul>
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
      <div class="invoice p-3 mb-3">
        <!-- title row -->
        <div class="row">
          <div class="col-12 p-3">
            <h4>
              <i class="fas fa-globe"></i> Generar Prefactura.
              <small class="float-right">Fecha: <?php echo date("Y-m-d") ?></small>
            </h4>
          </div>
          <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">

          <!-- /.col -->
          <div class="col-sm-8 invoice-col">

            <b>Aseguaradora: <select name="ase" id="ase" class="form-control" onchange="validar()">
                <option value=""> Seleccione</option>
              </select></b>
            <b>Documento del Cliente</b> <input type="text" class="form-control" id="nit" disabled>
            <b>Cliente</b> <input type="text" class="form-control" id="cli" disabled><input type="hidden" class="form-control" id="idcli" disabled>
          </div>
          <!-- /.col -->
          <div class="col-sm-4 invoice-col">
            <b>Prefactura: <input type="text" class="form-control" id="pre" disabled></b>
            <div class="row invoice-info">
            <div class="col-sm-6 invoice-col">
            <b>Fecha Registro</b> <input type="date" class="form-control" id="fecreg" value="<?php echo date("Y-m-d") ?>">
            </div>
            <div class="col-sm-6 invoice-col">
            <b>Fecha Vencimiento</b> <input type="date" class="form-control" id="fecven" disabled>
            </div>
            </div>
            <b>Orden: <input type="text" class="form-control" id="orden" ></b>
           

          </div>
          <!-- /.col -->
        </div>
        <br>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
          <div class="col-12 table-responsive">
            <table class="table table-striped" id="tabla">
              <thead class="thead-dark">
                <tr>
                  <th>Id</th>
                  <th>Siniestro</th>
                  <th>Descripcion</th>
                  <th>Concepto</th>
                  <th>Aseguradora</th>
                  <th>Cant</th>
                  <th>Valor Neto</th>
                  <th>Iva</th>
                  <th>Valor Total</th>
                  <th>Opciones <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal" onclick="solicitudes(1)"> + </button></th>
                </tr>
              </thead>
              <tbody id="mostrar_solicitudes">


              </tbody>
            </table>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
          <!-- accepted payments column -->
          <div class="col-6">
            <p class="lead">Verificar Datos ingresados antes de guardar:</p>



          </div>
          <!-- /.col -->
          <div class="col-6">
            <p class="lead">Totales</p>

            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th style="width:50%">Subtotal:</th>
                  <td id="">$ <input type="text" id="st" disabled style="width: 100px;" value="0"></td>
                </tr>
                <tr>
                  <th>Iva</th>
                  <td id="">$ <input type="text" id="it" disabled style="width: 100px;" value="0"></td>
                </tr>

                <tr>
                  <th>Total:</th>
                  <td id="">$ <input type="text" id="gt" disabled style="width: 100px;" value="0"></td>
                </tr>
              </table>
            </div>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- this row will not appear when printing -->
        <div class="row no-print">
          <div class="col-12">
            <a href="invoice-print.html" rel="noopener" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> Imprimir</a>
            <button type="button" class="btn btn-success float-right" id="btnind" disabled  onclick="generarpremas()"><i class="far fa-credit-card"></i>
              Generar Prefactura Masivo
            </button>
            <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;" id="btnmas" onclick="generarpre()">
              <i class="fas fa-download"></i> Generar Prefactura x Item
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
      <div class="invoice p-3 mb-3">
        <div class="row">
          <div class="col-12 table-responsive">
            <br>
            <h4>
              <i class="fas fa-globe"></i> Lista Prefactura.
              <small class="float-right">Fecha: <?php echo date("Y-m-d") ?></small>
            </h4>
            <table class="table table-striped" id="tabla2">
              <thead class="thead-dark">
                <tr>
                  <th><input type="checkbox" onclick="marcar(this);" /> Prefactura</th>
                  <th>Cliente</th>
          
                  <th>Fecha Registro</th>
                  <th>Fecha Vencimiento</th>
                  <th>Valor Total</th>
                  <th>Opciones</th>
                </tr>
              </thead>
              <tbody id="mostrar_prefacturas">


              </tbody>
            </table>
            <button onclick="generarfacturar()">Generar Factura</button>
          </div>
          <!-- /.col -->
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

<div class="modal fade" id="myModalFact">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Generar Factura</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="row invoice-info">

          <!-- /.col -->
          <div class="col-sm-9 invoice-col">
            <div class="row">
               <div class="col-sm-12">
               <b>Aseguaradora: <select name="ase" id="aseguradora" class="form-control">
                <option value=""> Seleccione</option>
              </select></b>
               </div>
               <div class="col-sm-2">
               <b>Tipo</b> <input type="text" class="form-control" id="tipo" disabled>
               </div>
               <div class="col-sm-4">
               <b>Documento</b> <input type="hidden" class="form-control" id="idcli" disabled>
               <input type="text" class="form-control" id="nitcli" disabled>
               </div>
               <div class="col-sm-6">
               <b>Cliente</b> <input type="text" class="form-control" id="cliente" disabled>
               </div>
               <div class="col-sm-6">
               <b>Telefono</b> <input type="text" class="form-control" id="telcli" disabled>
               </div>
               <div class="col-sm-6">
               <b>Email</b> <input type="text" class="form-control" id="emailcli" disabled>
               </div>
               <div class="col-sm-6">
               <b>Direccion</b> <input type="text" class="form-control" id="direccion" disabled>
               </div>
               <div class="col-sm-6">
               <b>Ciudad</b> <input type="text" class="form-control" id="ciudad" disabled>
               </div>

            </div>
            
            
            

          </div>
          <!-- /.col -->
          <div class="col-sm-3 invoice-col">
            <b>Prefactura: <input type="text" class="form-control" id="prefactua" disabled></b>

            <b>Fecha Registro</b> <input type="date" class="form-control" id="fechareg">
            <b>Fecha Vencimiento</b> <input type="date" class="form-control" id="fechaven">

          </div>
          <!-- /.col -->
        </div>
        <br>
        <table id="dtBasicExample2" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
          <!-- <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_lista_paises"> -->
          <thead class="thead-dark">
            <tr>
              <th>Item</th>
              <th>Descripcion</th>
              <th>Cant</th>
              <th>Valor Und</th>
              <th>Iva</th>
              <th>Total</th>
            </tr>
          </thead>

          <tbody id="listadofac">
            <tr>
              <td colspan="7">Cargando datos...</td>
            </tr>

          </tbody>
        </table>




      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="generar()">Generar Factura</button>
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