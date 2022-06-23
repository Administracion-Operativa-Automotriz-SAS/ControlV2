const servidor = "http://127.0.0.1:8000/api/";

function get_table_solicitud_facturacion() {
  alert("En construccion");
}
//3124501148
function solicitudes(page) {
  var ase = $("#ase").val();
  var sini = $("#sini").val();
  var desc = $("#desc").val();
  var fec = $("#fec").val();
  var sol = $("#sol").val();
  axios
    .get(
      "modelo.php?sw=0&ase=" +
        ase +
        "&page=" +
        page +
        "&sini=" +
        sini +
        "&desc=" +
        desc +
        "&fec=" +
        fec +
        "&sol=" +
        sol
    )
    .then(function (res) {
      if (res.status == 200) {
        let listam = res.data.datos;
        let pag = res.data.page;
        let last = res.data.lastpage;
        $("#listado").html("");
        let arreglo = listam.map(function (obj) {
 
          var ids = "'" + obj.ids + "'";
          var sin = "'" + obj.siniestro + "'";
          var des = "'" + (obj.descripcion) + "'";
          var can = "'" + obj.cantidad + "'";
          var pre = "'" + obj.valor + "'";
          var fec = "'" + obj.fecha_solicitud + "'";
          var sol = "'" + obj.solicitado_por + "'";
          var con = "'" + obj.conceptos + "'";
          var ase = "'" + obj.ase + "'";
          var td =
            "<tr><td>" +
            obj.siniestro +
            "<td>" +
            obj.descripcion +
            "</td> <td> " +
            obj.cantidad +
            " </td><td> " +
            obj.valor +
            " </td><td> " +
            obj.fecha_solicitud +
            " </td><td> " +
            obj.solicitado_por +
            ' </td><td><input type="checkbox" id="id' +
            obj.id +
            '" onclick="pasar(' +
            ids +
            "," +
            sin +
            "," +
            des +
            "," +
            can +
            "," +
            pre +
            "," +
            fec +
            "," +
            sol +
            "," +
            con +
            "," +
            ase +
            ')"></td></tr>';
          $("#listado").append(td);
        });
        if (parseInt(pag) > 1) {
          $("#pag").html(
            '<li class="page-item"><a class="page-link" href="#" onclick="solicitudes(1)"> < </a></li><li class="page-item"><a class="page-link" href="#" onclick="solicitudes(' +
              (pag - 1) +
              ')"> << </a></li>'
          );
        } else {
          $("#pag").html(
            '<li class="page-item"><a class="page-link" href="#" onclick="solicitudes(1)"> < </a></li><li class="page-item"><a class="page-link" href="#" onclick="solicitudes(1)"> << </a></li>'
          );
        }
        if (parseInt(pag) < parseInt(last)) {
          $("#pag").append(
            '<li class="page-item"><a class="page-link" href="#" onclick="solicitudes(' +
              (parseInt(page) + 1) +
              ')"> >> </a></li><li class="page-item"><a class="page-link" href="#" onclick="solicitudes(' +
              last +
              ')"> > </a></li>'
          );
        } else {
          $("#pag").append(
            '<li class="page-item"><a class="page-link" href="#"> >> </a></li><li class="page-item"><a class="page-link" href="#"> > </a></li>'
          );
        }
        $("#info").html("Pagina " + pag + " de " + last);
      }
    })
    .catch(function (err) {
      console.log(err);
    })
    .then(function () {
      //loading.style.display = 'none';
    });
}
function reemplazar(texto){
     let t = texto.replace(","," ");
     return t;
}
function aseguradoras() {
  axios
    .get("modelo.php?sw=1")
    .then(function (res) {
      if (res.status == 200) {
        let listam = res.data;
        $("#ase").html("<option value=''>Facturar todos los items</option>");
        $("#aseguradora").html("<option value=''>Seleccione</option>");
        let arreglo = listam.map(function (obj) {
          var td =
            '<option value="' + obj.id + '">' + obj.nombre + "</option> ";
          $("#ase").append(td);
          $("#aseguradora").append(td);
        });
      }
    })
    .catch(function (err) {
      console.log(err);
    })
    .then(function () {
      //loading.style.display = 'none';
    });
}

function pasar(ids, sin, des, can, pre, fec, sol, con, ase) {
  var idi = $("#id"+sin).is( ":checked");
  console.log(idi);
  if(idi==false){
    $("#tr"+ids).remove();
      return false;
  }
  var datos = [ids, sin, des, can, pre, fec, sol];
  var tt = ((can*pre) * 1.19).toFixed(0);
  var iva = (tt - pre).toFixed(0);
  $("#mostrar_solicitudes").append(
    "<tr id='tr" +
      ids +
      "'><td>" +
      ids +
      "</td><td>" +
      sin +
      "</td><td>" +
      des +
      "</td><td>" +
      con +
      "</td><td>" +
      ase +
      "</td><td>" +
      can +
      "</td><td>" +
      pre +
      "</td><td>" +
      iva +
      "</td><td>" +
      tt +
      "</td><td><button onclick='quitar(" +
      ids +
      ")'>-</button></td>"
  );
  sumar();
}
function quitar(id) {
  $("#tr" + id)
    .closest("tr")
    .remove();
  sumar();
}
function sumar() {
  var total_col1 = 0;
  var total_col2 = 0;
  //Recorro todos los tr ubicados en el tbody
  $("#tabla tbody")
    .find("tr")
    .each(function (i, el) {
      //Voy incrementando las variables segun la fila ( .eq(0) representa la fila 1 )
      total_col1 += parseFloat($(this).find("td").eq(6).text());
      total_col2 += parseFloat($(this).find("td").eq(8).text());
    });
  $("#st").val(total_col1);
  $("#gt").val(total_col2);
  $("#it").val(parseInt(total_col2) - parseInt(total_col1));
}
function validar() {
  var ase = $("#ase").val();
  $("#mostrar_solicitudes").html("");
  if (ase == "") {
    $("#btnind").attr("disabled", true);
    $("#btnmas").attr("disabled", false);
  } else {
    $("#btnmas").attr("disabled", true);
    $("#btnind").attr("disabled", false);
  }
  var fecreg = $("#fecreg").val();
  axios
    .get("modelo.php?sw=6&ase=" + ase + "&fecreg=" + fecreg)
    .then(function (res) {
      var datos = res.data.datos[0];
      console.log(res.data);
      $("#nit").val(datos.identificacion);
      $("#cli").val(datos.nombre + " " + datos.apellido);
      $("#fecven").val(res.data.ven);
      $("#idcli").val(datos.cli);
    })
    .catch(function (err) {
      console.log(err);
    })
    .then(function () {
      //loading.style.display = 'none';
    });
}
function generarpre() {
  $("#tabla tbody")
    .find("tr")
    .each(function (i, el) {
      ids = parseFloat($(this).find("td").eq(0).text());
      siniestro = parseFloat($(this).find("td").eq(1).text());
      var fec = $("#fecreg").val();
      var ase = $("#ase").val();
      if (fec == "") {
        alert("Debes de seleccionar la fecha de registro");
        $("#fecreg").focus();
        return false;
      }
      axios({
        method: "post",
        url: servidor + "facturacion/prefactura",
        data: {
          ids: ids,
          siniestro: siniestro,
          registro: fec,
          ase: ase,
        },
      }).then(function (response) {
        console.log(response.data);
        Swal.fire(
          "Buen Trabajo!",
          "Se generaron las prefacturas con exito",
          "success"
        );
        $("#mostrar_solicitudes").html("");
      });
    });
}

function generarpremas() {
  var fec = $("#fecreg").val();
  var ase = $("#ase").val();
  var ven = $("#fecven").val();
  var cli = $("#idcli").val();
  var sub = $("#st").val();
  var iva = $("#it").val();
  var gt = $("#gt").val();
  var orden = $("#orden").val();
  if (fec == "") {
    alert("Debes de seleccionar la fecha de registro");
    $("#fecreg").focus();
    return false;
  }
  if (gt == 0) {
    alert("Debes de agregar por lo menos 1 registro");
    return false;
  }
  if (orden == 0 || orden == '') {
    alert("Debes de digitar el numero de orden");
    return false;
  }
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
    },
    buttonsStyling: false,
  });

  swalWithBootstrapButtons
    .fire({
      title: "Estas seguro de generar la pre-factura?",
      text: "No podrar revertir este proceso!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Si, generar!",
      cancelButtonText: "No, cancelar!",
      reverseButtons: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        axios
          .get(
            "modelo.php?sw=7&cli=" +
              cli +
              "&fec=" +
              fec +
              "&ven=" +
              ven +
              "&sub=" +
              sub +
              "&iva=" +
              iva +
              "&gt=" +
              gt+"&ase="+ase+"&orden="+orden
          )
          .then(function (res) {
            if (res.status == 200) {
              console.log(res.data);
              $("#pre").val(res.data.pre);
              var pre = res.data.pre;
              $("#tabla tbody")
                .find("tr")
                .each(function (i, el) {
                  ids = parseFloat($(this).find("td").eq(0).text());
                  siniestro = parseFloat($(this).find("td").eq(1).text());
                  iva = parseFloat($(this).find("td").eq(7).text());
                  axios
                    .get("modelo.php?sw=8&pre=" + pre + "&id=" + ids + "&iva=" +iva)
                    .then(function (response) {
                      console.log(response.data);
                      Swal.fire(
                        "Buen Trabajo!",
                        "Se generaron las prefacturas con exito ",
                        "success"
                      );
                      limpiar();
                    });
                });
            }
          })
          .catch(function (err) {
            console.log(err);
          })
          .then(function () {
            console.log("paso al terminar");
          });
      } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
      ) {
        swalWithBootstrapButtons.fire(
          "Cancelado",
          "El proceso se ha cancelado",
          "error"
        );
      }
    });
}

function limpiar(){

       var ase = $("#ase").val('');
       var ven = $("#fecven").val('');
       var cli = $("#idcli").val('');
       var sub = $("#st").val(0);
       var iva = $("#it").val(0);
       var gt = $("#gt").val(0);

       $("#nit").val('');
       $("#cli").val('');
       $("#pre").val('');

       $("#mostrar_solicitudes").html("");
}
function prefacturas() {
  axios
    .get("modelo.php?sw=2")
    .then(function (res) {
      if (res.status == 200) {
        console.log(res.data.datos);

        let listams = res.data.datos;
        let pag = res.data.page;
        let last = res.data.lastpage;
        $("#mostrar_prefacturas").html("");
        let arreglo = listams.map(function (obj) {
          var td =
            '<tr><td> <input type="checkbox" class="checkAll" name="item" id="' +
            obj.pre +
            '" onclick="marcar()" > ' +
            obj.pre +
            "</td><td>" +
            obj.cli +
            "</td><td>" +
            obj.fecha_emision +
            "</td><td>" +
            obj.fecha_vencimiento +
            "</td><td>" +
            obj.total +
            '</td><td><button type="button" data-toggle="modal" data-target="#myModalFact" onclick="addfact(' +
            obj.pre +
            ')"><i class="fas fa-edit"></i></button> </td> ';
          $("#mostrar_prefacturas").append(td);
          sumar();
        });
      }
    })
    .catch(function (err) {
      console.log(err);
    })
    .then(function () {
      //loading.style.display = 'none'; 3003201577
    });
}

function addfact(id) {
  axios
    .get("modelo.php?sw=3&id=" + id)
    .then(function (res) {
      if (res.status == 200) {
        $("#listadofac").html("");
        console.log(res.data);
        let listamss = res.data.items;
        let enc = res.data.datos[0];
        let sin = res.data.siniestro[0];
        $("#fechareg").val(enc.fecha_emision);
        $("#fechaven").val(enc.fecha_vencimiento);
        $("#cliente").val(enc.cli);
        $("#prefactua").val(enc.pre);
        $("#nitcli").val(enc.identificacion);
        $("#tipo").val(enc.tipo_id);
        $("#aseguradora").val(enc.seguro);

        $("#telcli").val(enc.telefono_casa);
        $("#emailcli").val(enc.email_e);
        $("#direccion").val(enc.direccion);
        $("#ciudad").val(res.data.datos.nameciudad);
        $("#idcli").val(enc.cliente);
        var total = 0;
        var ivat = 0;
        var subto = 0;
        let arreglo = listamss.map(function (obj) {
          var p = obj.valor * obj.cantidad;
          if (obj.porc_iva == "19.00") {
            to = p * 1.19;
            iv = to - p;
          } else {
            to = p;
            iv = 0;
          }
          subto += parseInt(p);
          total += to;
          ivat += iv;
          var td =
            "<tr><td>" +
            obj.sol +
            "</td><td>" +
            obj.descripcion +
            '</td><td>'+obj.cantidad+'</td><td><input type="text" id="val' +
            obj.sol +
            '"  style="width:100px" onchange="upitem(' +
            obj.sol +
            ')"  value="' +
            obj.valor +
            '"></td><td><input type="text" id="valiva' +
            obj.sol +
            '"  style="width:100px" value="' +
            iv +
            '"></td><td><input type="text"  style="width:100px" id="valto' +
            obj.sol +
            '" disabled value="' +
            to +
            '"></td> ';
          $("#listadofac").append(td);
        });
        td = '<tr><td colspan="5">Subtotal</td><td>' + subto + "</td> ";
        $("#listadofac").append(td);
        td = '<tr><td colspan="5">Iva</td><td>' + ivat + "</td> ";
        $("#listadofac").append(td);
        td = '<tr><td colspan="5">Total</td><td>' + total + "</td> ";
        $("#listadofac").append(td);
      }
    })
    .catch(function (err) {
      console.log(err);
    })
    .then(function () {
      //loading.style.display = 'none'; 3003201577
    });
}

function generar() {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
    },
    buttonsStyling: false,
  });

  swalWithBootstrapButtons
    .fire({
      title: "Estas seguro de generar la factura?",
      text: "No podrar revertir este proceso!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Si, generar!",
      cancelButtonText: "No, cancelar!",
      reverseButtons: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        var prefactura = $("#prefactua").val();
        axios
          .get("modelo.php?sw=4&id=" + prefactura)
          .then(function (res) {
            if (res.status == 200) {
              console.log(res.data);
              swalWithBootstrapButtons.fire(
                "Confirmado!",
                res.data.msj,
                res.data.status != false ? "success" : "error"
              );
              $("#myModalFact").modal("hide");
              prefacturas();
            }
          })
          .catch(function (err) {
            console.log(err);
          })
          .then(function () {
            //loading.style.display = 'none'; 3003201577
          });
      } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
      ) {
        swalWithBootstrapButtons.fire(
          "Cancelado",
          "El proceso se ha cancelado",
          "error"
        );
      }
    });
}
function marcar(source) {
  checkboxes = document.getElementsByName("item"); //obtenemos todos los controles del tipo Input
  for (i = 0; i < checkboxes.length; i++) {
    //recoremos todos los controles
    if (checkboxes[i].type == "checkbox") {
      //solo si es un checkbox entramos
      checkboxes[i].checked = source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
    }
  }
}

function generarfacturar() {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
    },
    buttonsStyling: false,
  });

  swalWithBootstrapButtons
    .fire({
      title: "Estas seguro de generar la factura?",
      text: "No podrar revertir este proceso!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Si, generar!",
      cancelButtonText: "No, cancelar!",
      reverseButtons: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        $("input[name=item]:checked")
          .each(function () {
            var id = $(this).attr("id");

            axios
              .get("modelo.php?sw=4&id=" + id)
              .then(function (res) {
                if (res.status == 200) {
                  console.log(res.data);
                }
              })
              .catch(function (err) {
                console.log(err);
              })
              .then(function () {
                //loading.style.display = 'none'; 3003201577
              });
          })
          .promise()
          .done(function () {
            prefacturas();
            Swal.fire({
              icon: "success",
              title: "Se han generado las factura con exito.",
              showConfirmButton: false,
              timer: 1500,
            });
          });
      } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
      ) {
        swalWithBootstrapButtons.fire(
          "Cancelado",
          "El proceso se ha cancelado",
          "error"
        );
      }
    });
}

function upitem(id) {
  var pre = $("#val" + id).val();
  var prefactura = $("#prefactua").val();
  if (pre == "" || pre == 0) {
    alert("El valor debe ser mayor a 0");
    return false;
  }

  axios.get("modelo.php?sw=5&id=" + id + "&pre=" + pre + "&prefactura=" + prefactura)
    .then(function (res) {
      if (res.status == 200) {
        addfact(prefactura);
        console.log(res.data);
        Swal.fire({
          icon: "success",
          title: res.data.msj,
          showConfirmButton: false,
          timer: 1500,
        });
        prefacturas();
      }
    });
}
$('#subida').submit(function(){
  $("#botonload").attr("disabled",true);
  
  var comprobar = $('#foto').val().length;
  console.log(comprobar);
  if(comprobar>0){		
    Swal.showLoading()
    var formulario = $('#subida');	
    var datos = formulario.serialize();		
    var archivos = new FormData();			
    var url = 'importar.php';		
    for (var i = 0; i < (formulario.find('input[type=file]').length); i++) { 			

                       archivos.append((formulario.find('input[type="file"]:eq('+i+')').attr("name")),((formulario.find('input[type="file"]:eq('+i+')')[0]).files[0]));		 

           }	
    $.ajax({			
      url: url+'?'+datos,			
      type: 'POST',			
      contentType: false, 			
                            data: archivos,			
                               processData:false,
                              beforeSend : function (){
                                  $('#msg2').html('Cargando...');

                              },
      success: function(data){
                                      $('#foto').focus();
                                      $('#foto').val('');
                                      $('#msg2').html(data);
                                      console.log(data);
                                      $("#botonload").attr("disabled",false);
                                      Swal.hideLoading();
        return false;

      }
    });
    return false;
  }else{
    alert('Error ! debes de llenar todos los campos necesario, para poder cargar los codigos');
    return false;
  }
});

function importados() {
  Swal.showLoading();
  axios
    .get("modelo.php?sw=9")
    .then(function (res) {
      if (res.status == 200) {
        //console.log(res.data.datos);

        let listams = res.data.datos;
        let pag = res.data.page;
        let last = res.data.lastpage;
        $("#mostrar_prefacturas").html("");
        var count = 0;
        let arreglo = listams.map(function (obj) {
          count++;
          var td =
            '<tr><td> <input type="checkbox" class="checkAll" name="item" id="' +
            obj.id +
            '" onclick="marcar()" > ' +
            obj.id +
            "</td><td>"+obj.placa+"</td><td>" +
            obj.descripcion +
            "</td><td>" +
            obj.kilometro +
            "</td><td>" +
            obj.galon +
            "</td><td>" +
            obj.valorgl +
            '</td><td>'+(obj.galon*obj.valorgl)+'</td> ';
          $("#mostrar_prefacturas").append(td);
        
        });
        $("#progreso").html('<div class="progress-bar" role="progressbar" aria-valuenow="0"aria-valuemin="0" aria-valuemax="100" style="width:0%"> 0% </div>');
        $("#count").val(count);
        Swal.hideLoading()
      }
    })
    .catch(function (err) {
      console.log(err);
    })
    .then(function () {
      //loading.style.display = 'none'; 3003201577
      Swal.hideLoading();
    });
}

function addubicacion() {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
    },
    buttonsStyling: false,
  });

  swalWithBootstrapButtons
    .fire({
      title: "Estas seguro de agregar los registro a ubicacion?",
      text: "No podrar revertir este proceso!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Si, generar!",
      cancelButtonText: "No, cancelar!",
      reverseButtons: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
       $("#ModalLoad").modal("show");
        let count = $("#count").val();
        $("#btns").attr("disabled",true);
        var ct = 0;
        
        $("input[name=item]:checked")
          .each(function () {
            var id = $(this).attr("id");
            ct++;
            var porcentaje = (ct * 100)/count;

            axios
              .get("modelo.php?sw=10&id=" + id)
              .then(function (res) {
                if (res.status == 200) {
                  console.log(res);
                  
                  $("#progreso").html('<div class="progress-bar" role="progressbar" aria-valuenow="0"aria-valuemin="0" aria-valuemax="100" style="width:'+porcentaje+'%"> '+porcentaje+'% </div>');
                }
              })
              .catch(function (err) {
                console.log(err);
              })
              .then(function () {
                //loading.style.display = 'none'; 3003201577
              });
          })
          .promise()
          .done(function () {
            importados();
            $("#btns").attr("disabled",false);
            if(porcentaje>98){
              Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Se ha subido con exito los registros.',
                showConfirmButton: false,
                timer: 2000
              });
              
            }
            
          });
      } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
      ) {
        swalWithBootstrapButtons.fire(
          "Cancelado",
          "El proceso se ha cancelado",
          "error"
        );
      }
    });
}
