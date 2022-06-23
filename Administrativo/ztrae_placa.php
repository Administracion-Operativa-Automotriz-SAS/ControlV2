
<?php

/**
 *   PROGRAMA PARA TRAER PLACAS A LA BASE DE DATOS ADMINISTRATIVA
 *
 * @version $Id$
 * @copyright 2011
 */
include('inc/funciones_.php');
html('PLACAS');
echo "<script language='javascript'>
			function pasar(dato)
			{
				opener.document.mod.placa.value=dato;
				window.close();void(null);
			}
	</script>
	<body >
	</body><script language='javascript'>centrar(200,200);</script>
	Seleccione la placa: ".menu1("PLACA","Select placa,placa from aoacol_aoacars.vehiculo order by placa",0,1,""," onchange='pasar(this.value);' ");

?>
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>

<script>

/*(function(){
  let x = document.getElementById("PLACA");
  let option = document.createElement("option");
  option.text = "OPTION NUEVO";
  x.add(option,x[1]);
})();
*/
$( document ).ready(function() {
   $('#PLACA').append($('<option>', {value: "10KSEX",text : "10KSEX"}));
   $('#PLACA').append($('<option>', {value: "ANDI01",text : "ANDI01"}));
   $('#PLACA').append($('<option>', {value: "CMNDVR",text : "CMNDVR"}));
   $('#PLACA').append($('<option>', {value: "FERAUT",text : "FERAUT"}));
   $('#PLACA').append($('<option>', {value: "GMANCH",text : "GMANCH"}));
   $('#PLACA').append($('<option>', {value: "GMBACA",text : "GMBACA"}));
   $('#PLACA').append($('<option>', {value: "GPSKOD",text : "GPSKOD"}));
   $('#PLACA').append($('<option>', {value: "GUATAP",text : "GUATAP"}));
   $('#PLACA').append($('<option>', {value: "LAGART",text : "LAGART"}));
   $('#PLACA').append($('<option>', {value: "PARTYC",text : "PARTYC"}));
   






   
});

$('#PLACA').select2({
  placeholder: 'Select an option'
});



</script>