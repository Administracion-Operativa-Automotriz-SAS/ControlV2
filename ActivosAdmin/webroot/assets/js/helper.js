
function Ajax(urlSnd, datos, opt, cb) {
    if (!datos) {
        datos = {};
    }
    //datos.TOKEN = TOKEN;
    
	if(ENABLE_MOCKS){
	   var baseUrl = RUTA_MOCKS+'?m='+urlSnd;   
	}else{
	   var baseUrl = BASE_URL + urlSnd;
	}
    if (opt) {
      //  baseUrl = urlSnd;
    }
    $.ajax({
        url: baseUrl,
        type: "POST",
        dataType: 'json',
        data: datos,
        beforeSend: function () {
           // $.LoadingOverlay("show");
        },
        success: function (msg) {
            cb(msg);
           // $.LoadingOverlay("hide");
        }
    });
}

function limpiarCampos($selector) {

    $selector.find('input').val('');
    $selector.find('textarea').val('');
}

function msg(cont, texto, tipo) {
    $('.textomsg', cont).html(texto);
    $(cont).addClass('alert');
    if (tipo == 'error') {
        $(cont).addClass('alert-warning');
    } else if (tipo == 'complete') {
        $(cont).addClass('alert-success');
    }

    $(cont).fadeIn(1000).delay(1500).fadeOut(1000);
}

function cargo_pagina() {
    $("#contenidoGen").css({"display": "block", "font-size": "100%"});
    $("#cargando_pagina").css({"display": "none", "font-size": "100%"});
}

setTimeout(function () {
    cargo_pagina();
}, 10000);

function respData(resp) {
    var data = {};
    if (resp.res) {
        data.estado = 'complete';
        data.mensaje = resp.dataObj;
    } else {
        data.estado = 'error';
        data.mensaje = resp.dataObj;
    }
    return data;
}


const Formulario = (FormularioData) => {
    limpiarMensaje();
    $(FormularioData[0].elements).each(function (index, el) {
        if ($(el).attr('tipo') == 'numerico') {
            validacionFormulario(el, 'numerico');
        }

        if ($(el).attr('tipo') == 'texto') {
            validacionFormulario(el, 'texto');
        }
    })
}


const validacionFormulario = (elemento, type) => {
    let error = false;

    error = EsVacio($(elemento).val(), elemento);

    if (error) {
        return error;
    }

    if (type == 'numerico') {
        if (!$.isNumeric($(elemento).val())) {
            $(elemento).after('<span class="alert alert-danger mensaje">Debe ser númerico</span>');
            error = true;
        }
    }

    if (error) {
        return error;
    }
}

const limpiarMensaje = () => $('.mensaje').remove();

const EsVacio = (valor, elemento) => {
    if (valor == '' || valor == undefined || !valor) {
        $(elemento).after('<span class="alert alert-danger mensaje">vacio</span>');
        return true;
    }
}

$(function () {
    $('.imagenLoad').on('change', function (event) {
        var input = $(this);
        var reader = new FileReader();
        reader.onload = function () {
            var dataURL = reader.result;
            $(input).attr('data-src', dataURL);
        };

        try {
            reader.readAsDataURL($(this).context.files[0]);
        } catch (e) {
            reader.readAsDataURL(input[0].files[0]);
        }
        return false;
    })

})


function serializar(input) {

    var values = $("input[name='" + input + "[]']").map(function () {
        return $(this).val();
    }).get();
    return values;

}

function base_url() {

    var pathparts = location.pathname.split('/');

    if (location.host == 'localhost') {
        var url = location.origin + '/' + pathparts[1].trim('/') + '/';
    } else {
        var url = location.origin;
    }
    return url;
}


$(function () {

    $('#areaA_id').on('change', function () {
        var id = $(this).val();
        if (id != '' || id.length != 0)
            asesoresListar(id);
        return false;
    });


    $('#dpto').on('change', function () {
        var id = $(this).val();
        if (id != '' || id.length != 0)
            municipioListar(id);
        return false;
    });

    $('#documento, #telefono, #celular').on('keypress', function (e) {
        return isNumberKey(e);
    });

});

function municipioListar(id) {


    $.ajax({
        url: BASE_URL + '/services/Pqr_serv/municipios',
        type: "POST",
        dataType: "json",
        data: {id: id},
        success: function (data) {
            if (data.res) {
                var html = '<option value="" class="d-none">Seleccione uno</option>';
                $.each(data.dataObj, function (id, value) {

                    html += '<option value="' + value['mpio_id'] + '">' + value['nombre'] + '</option>';
                });
                $('#mpio').html(html);
            }
        },
        error: function (e) {
            alert('error');
        }
    });
}


function asesoresListar(id) {


    $.ajax({
        url: BASE_URL + '/services/Pqr_serv/listarAsesor_Serv',
        type: "POST",
        dataType: "json",
        data: {id: id},
        success: function (data) {
            if (data.res) {
                var html = '<option value="" class="d-none">Seleccione uno</option>';
                $.each(data.dataObj, function (id, value) {

                    html += '<option value="' + value['funcionario_id'] + '">' + value['nombres'] + ' ' + value['apellidos'] + '</option>';
                });
                $('#responsable1').html(html);
            }
        },
        error: function (e) {
            alert('error');
        }
    });
}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

function _character_special($this, special = false) {

    var regex = new RegExp("^[a-zA-Z0-9\s]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (special) {
        var key = e.charCode || e.keyCode || 0;
        // (#, -, _ ) permitidos
        if (key == 35) {
            return true;
        }else{ return false;}
        if (key == 45) {
            return true;
        }else{ return false;}
        if (key == 95) {
            return true;
        }else{ return false;}
    }

    if (regex.test(str)) {
        return true;
    }
    e.preventDefault();
    return false;
}

//cargar datatale
function loadDataTable(){
	$('#datatable').DataTable({
			responsive: true,
			 language: {
			"decimal": "",
			"emptyTable": "No hay información",
			"info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
			"infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
			"infoFiltered": "(Filtrado de _MAX_ total entradas)",
			"infoPostFix": "",
			"thousands": ",",
			"lengthMenu": "Mostrar _MENU_ Entradas",
			"loadingRecords": "Cargando...",
			"processing": "Procesando...",
			"search": "Buscar:",
			"zeroRecords": "Sin resultados encontrados",
			"paginate": {
				"first": "Primero",
				"last": "Ultimo",
				"next": "Siguiente",
				"previous": "Anterior"
			}
		},
	});
}