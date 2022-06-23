<?php header('Content-Type: charset=utf-8'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>

</head>
<body>

<div class="container">
  <!-- Trigger the modal with a button -->
  <!-- Modal -->
  
  
  <div class="modal fade" id="myModal" role="dialog">
    
	<div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
	  
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Protocolo lineas de atencion</h4>
        </div>
        <div class="modal-body">
          
		  <p>Paso a paso a realizar cuando un cliente se comunica a las lineas de atencion de AOA..</p>
		  <h4 class="modal-title">Selecciona el paso que quiera visitar</h4>
		  <div class="pos-f-t">
  <div class="collapse" id="navbarToggleExternalContent">
    <div class="bg-dark p-4">
      <span class="text-muted">
	  <br>
	  Buenos días/tardes., 
	  Bien venido a su línea de asistencia de AOA Colombia, 
	  habla con ___________ ¿Con quién tengo el gusto de hablar? _________ 
	  para ofrecerle una atención oportuna, agradezco me brinde la siguiente 
	  información: Me informa un número de contacto al cual nos podamos comunicar 
	  con usted en caso de que la llamada se interrumpa?___________ Me puede proporcionar otro número alterno?__________<br>
.</span>
    </div>
  </div>
  <nav class="navbar navbar-dark bg-dark">
    <button class="btn btn-primary navbar-toggler" type="button" data-toggle="collapse" 
	data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
      Introduccion al recibir la llamada
    </button>
  </nav>
</div>
<div class="pos-f-t">
  <div class="collapse" id="navbarToggleExternalContent2">
    <div class="bg-dark p-4">
      <span class="text-muted">¿Cuál es la placa del vehículo que tiene en servicio o la placa de 
	  su vehículo en reparación? Usted tiene asignado el vehículo ______ el cual fue entregado 
	  en la ciudad _______ ¿Dónde se encuentra ubicado el vehículo en este momento?.</span><br><br>
    </div>
  </div>
  <nav class="navbar navbar-dark bg-dark">
    <button class="btn btn-primary navbar-toggler" type="button" data-toggle="collapse" 
	data-target="#navbarToggleExternalContent2" aria-controls="navbarToggleExternalContent2" aria-expanded="false" aria-label="Toggle navigation">
      Preguntas claves para continuar la llamada
    </button>
  </nav>
</div>
<div class="pos-f-t">
  <div class="collapse" id="navbarToggleExternalContent4">
    <div class="bg-dark p-4">
      <span class="text-muted">
	  <table class="table-bordered" id="firstTable">
	  <thead>
	  <tr>
	  <th>Solicitud del cliente</th>
	  <th>Respuesta</th>
	  </tr>
	  </thead>
	  <tbody>
	  <tr>
	  <td>
	  "El cliente solicita información sobre el cobro por uso de tarjeta de tercero que se aplica para la previsora"
	  </td>
	  <td>
	  Se le informa así: sr(a)__________ este cobro se genera cuando la tarjeta de crédito con la 
	  que se constituye la garantía es de un tercero y el cobro es de $6.000 diarios incluido IVA
	  </td>
	  </tr>
	  </tbody>
	  <tbody>
	  <tr>
	  <td>
	  El cliente solicita información sobre el valor de la penalidad por demora en la entrega del vehículo
	  </td>
	  <td>
	  Se ingresa a consulta de siniestros y se verifica la gama del vehículo que tiene en servicio, 
	  según esta se verifica en la lista de tarifas y se le informa así: Sr(a)___________ 
	  el valor de la penalidad por demora en la devolución del vehículo es la siguiente: $________. 
	  </td>
	  </tr>
	  </tbody>
	  <tbody>
	  <tr>
	  <td>
	  El cliente solicita información sobre el cobro de lavado
	  </td>
	  <td>
	  Se ingresa a consulta de siniestros y se verifica información  al cual pertenece el servicio, si es de (Ciudad) el valor es $_______,
	  </td>
	  </tr>
	  </tbody>
	  <tbody>
	  <tr>
	  <td>
	  El cliente solicita información sobre el cobro de combustible
	  </td>
	  <td>
	  Se le informa que se tomara el valor del surtidor + 30% por servicio de abastecimiento
	  </td>
	  </tr>
	  </tbody>
	  <tbody>
	  <tr>
	  <td>
	  El cliente solicita información sobre el servicio de conductor elegido
	  </td>
	  <td>
	  Se  ingresa a consulta de siniestros y se verifica la aseguradora del  vehículo que que tiene en 
	  servicio; se le informa al cliente con que aseguradora tiene la póliza el vehículo y el numeral a donde se 
	  tiene que comunicar para solicitar el servicio.
	  </td>
	  </tr>
	  </tbody>
	  <tbody>
	  <tr>
	  <td>
	  El cliente solicita información sobre si el vehículo cuenta con seguro
	  </td>
	  <td>
	  Se le informa que el vehículo que tiene en servicio cuenta con una póliza de seguro.
	  </td>
	  </tr>
	  </tbody>
	  </table>
.</span>
    </div>
  </div>
  
  <div class="collapse" id="navbarToggleExternalContent3">
    <div class="bg-dark p-4">
      <nav class="navbar navbar-dark bg-dark">
    <button class="btn btn-primary navbar-toggler" type="button" data-toggle="collapse" 
	data-target="#navbarToggleExternalContent4" aria-controls="navbarToggleExternalContent4" aria-expanded="false" aria-label="Toggle navigation">
      Solicitud Administrativa
    </button>
    </nav>
    </div>
	<div class="collapse" id="navbarToggleExternalContent5">
    <div class="bg-dark p-4">
      <span class="text-muted">
	  <table class="table-bordered" id="secondTable">
	  <thead>
	  <tr>
	  <th>Solicitud del cliente</th>
	  <th>Respuesta</th>
	  </tr>
	  </thead>
	  <tbody>
	  <tr>
	  <td>
	  El cliente solicita confirmar el día y hora de la devolución del servicio
	  </td>
	  <td>
	  Se ingresa a consulta de siniestros y se le informa así: Sr___________ le confirmo que la fecha y hora de devolución es el día_________ máximo a las______ debe presentarse en la oficina donde le entregaron el vehículo con este lavado y tanqueado.
      </td>
	  </tr>
	  </tbody>
	  <tbody>
	  <tr>
	  <td>
	  El cliente solicita información sobre el valor de día adicional asegurado
	  </td>
	  <td>
	  Se ingresa a consulta de siniestros y se verifica la gama del vehículo que tiene en servicio, según esta se verifica en la lista de tarifas y se le informa así: Sr___________ el valor del día adicional es de $__________ si desea tomar este servicio  procederemos a tomar su solicitud e iniciar el tramite respectivo; se le informa de los requisitos necesarios para acceder al beneficio.
      </td>
	  </tr>
	  </tbody>
	  <tbody>
	  <tr>
	  <td>
	  El cliente indica que la aseguradora le ha informado que tiene extensión del servicio
	  </td>
	  <td>
	  Se ingresa a consulta de siniestros y se valida esta información si ya se encuentra en nuestro sistema se le informa que se realiza la extensión del servicio por ___ días, se le indica la nueva fecha y hora de devolución. En caso de que no este reportado se le solicita que espere nuestro llamado mientras nos comunicamos con la aseguradora para validar.
      </td>
	  </tr>
	  </tbody>
	   <tbody>
	   <tr>
	  <td>
	  El cliente indica que la aseguradora le ha informado que tiene extensión del servicio
	  </td>
	  <td>
	  Sr___________, su servicio actualmente es de ______ días de acuerdo a la cobertura de su póliza, sin embargo le podemos ofrecer servicio de renta con una tarifa especial por ser usuario de vehículo de remplazo.
      </td>
	  </tr>
	  </tbody>
	  </table>
.</span>
    </div>
  </div>
	<div class="bg-dark p-4">
      <nav class="navbar navbar-dark bg-dark">
    <button class="btn btn-primary navbar-toggler" type="button" data-toggle="collapse" 
	data-target="#navbarToggleExternalContent5" aria-controls="navbarToggleExternalContent5" aria-expanded="false" aria-label="Toggle navigation">
      Solicitudes Servicio al cliente
    </button>
    </nav>
    </div>
	<div class="collapse" id="navbarToggleExternalContent6">
    <div class="bg-dark p-4">
      <span class="text-muted">
	  <table class="table-bordered" id="table_id">
	  <thead>
	  <tr>
	  <th>Información del cliente</th>
	  <th>Sistema del vehículo</th>
	  <th>Respuesta por parte de la línea de atención</th>
	  </tr>
	  </thead>
	  <tbody>
	  <tr>
	  <td>
	  Sonidos en puertas, tablero y otros elementos de carrocería
	  </td>
	  <td>
	  Carrocería
	  </td>
	  <td>
	Se solicita información de su ubicación si está en la ciudad 
	del servicio o en una ciudad donde se tenga oficina se le 
	informa al Coordinador o Asesor de servicio encargado 
	para que le brinden asistencia de manera inmediata y se 
	lleve el vehículo al proveedor de taller para la revisión y 
	si se tiene disponibilidad de flota realizar de manera
	inmediata el reemplazo del vehículo, en caso de que se 
	encuentre en carretera se enlaza comunicación entre el 
	asegurado y la aseguradora para que le brinden la 
	asistencia requerida. Si el cliente refiere que los sonidos son 
	leves y que desea continuar con el vehículo hasta finalizar el 
	servicio se informa a la oficina encargada del vehículo para que 
	una vez retorne se hagan las respectivas correcciones."
    </td>
	  </tr>
	  </tbody>
	  <tbody>
	  <tr>
	  <td>
	  El vehículo hala hacia alguno de los lados o presenta sonidos al girar la dirección
	  </td>
	  <td>
	  Dirección
	  </td>
	  <td>
	  "Si la falla es leve se valida con el asegurado si 
		puede continuar con el servicio o desea que le 
		cambiemos el vehículo"
		"Si la falla es considerable se solicita información de
		su ubicación si esta en la ciudad del servicio o en 
		una ciudad donde se tenga oficina se le informa al
		Coordinador o Asesor de servicio encargado para 
		que le brinden asistencia de manera inmediata para
		llevar el vehículo al proveedor de taller para 
		la revisión y si se tiene disponibilidad de flota 
		realizar de manera inmediata el reemplazo del 
		vehículo."
      </td>
	  </tr>
	  </tbody>
	  <tbody>
	  <tr>
	  <td>
	  Vehículo no enciende
	  </td>
	  <td>
	  Eléctrico
	  </td>
	  <td>
	  Si tiene en servicio un Nissan Sentra que tiene llave remota, se le pregunta al asegurado si en el tablero este encendido el testigo Key(llave), de ser así se le solicita que acerque la llave al vehículo con el fin de solucionar. En los otros vehículos se le pregunta si al encender el vehículo prenden luces, testigos y si suena el arranque, de ser así se puede determinar que la falla es de la batería, en este caso si esta en la ciudad del servicio o en una ciudad donde se tenga oficina se le informa al Coordinador o Asesor de servicio encargado para que le brinden asistencia y cambio de batería de manera inmediata, en caso de que se encuentre en carretera se enlaza comunicación entre el asegurado y la aseguradora para que le brinden la asistencia requerida.
      </td>
	  </tr>
	  </tbody>
	  <tbody>
	  <tr>
	  <td>
	  Radio no prende
	  </td>
	  <td>
	  Eléctrico
	  </td>
	  <td>
	  "Se solicita información de su ubicación si está en la ciudad  del servicio o en una ciudad donde se tenga oficina se le informa al Coordinador o Asesor de servicio encargado  para que le brinden asistencia de manera inmediata, en  este caso el primer paso a seguir es desconectar la batería y volverla a conectar."
      </td>
	  </tr>
	  </tbody>
	  <tr>
	  <td>
	  Alguno de los bombillos no prende
	  </td>
	  <td>Eléctrico</td>
	  <td>"Se le solicita que se acerque a la oficina mas 
	  cercana a su ubicación para que de manera inmediata 
	  se realice el reemplazo de los bombillos que no funcionan."</td>
	  </tr>
	  <tbody>
	  <tr>
	  <td>
	  Sonidos en el escape, se debe solicitar que lleve el vehículo a la oficina y reemplazarle el vehículo
	  </td>
	  <td>
	  Escape
	  </td>
	  <td>
	  "Si la falla es considerable se solicita información de
su ubicación si esta en la ciudad del servicio o en 
una ciudad donde se tenga oficina se le informa al
Coordinador o Asesor de servicio encargado para 
que le brinden asistencia de manera inmediata, en caso de que se encuentre en carretera se enlaza comunicación entre el asegurado y la aseguradora para que le brinden la asistencia requerida."
<br>
"Si la falla es leve se valida con el asegurado si 
puede continuar con el servicio o desea que le 
cambiemos el vehículo."
     </td>
	  </tr>
	  </tbody>
	  <tbody>
	  <tr>
	  <td>
	  Ruidos al frenar o pedal de freno se va muy abajo, el testigo del freno de emergencia se queda encendido
	  </td>
	  <td>
	  Frenos
	  </td>
	  <td>
	  "Si el cliente refiere que no frena adecuadamente, se le 
solicita que detenga el vehículo donde se encuentre por su seguridad,  si esta en la ciudad del servicio o en 
una ciudad donde se tenga oficina se le informa al
Coordinador o Asesor de servicio encargado para 
que le brinden asistencia de manera inmediata, en caso de que se 
encuentre en carretera se enlaza comunicación entre el asegurado y 
la aseguradora para que le brinden la asistencia requerida."<br>
Se le solicita que se acerque a la oficina mas cercana a su ubicación 
para que de manera inmediata se realice el reemplazo del vehículo y en 
caso de no tener disponibilidad la revisión del sistema de frenos.
</td>
 </tr>
 </tbody>
 <tbody>
 <tr>
 <td>
 Fuga de combustible y el vehículo se apaga, vehículo NO se apaga pero hay fuga de combustible.
</td>
 <td>
 Inyección
 </td>
 <td>
 "Se solicita información de
su ubicación si esta en la ciudad del servicio o en 
una ciudad donde se tenga oficina se le informa al
Coordinador o Asesor de servicio encargado para 
que le brinden asistencia de manera inmediata para
llevar el vehículo al proveedor de taller para 
la revisión y si se tiene disponibilidad de flota 
realizar de manera inmediata el reemplazo del 
vehículo, en caso de que se encuentre en carretera se enlaza comunicación entre el asegurado y la aseguradora para que le brinden la asistencia requerida."
</td>
 </tr>
 </tbody>
 <tbody>
 <tr>
 <td>
 Fuga de aceite, sonidos, pérdida de fuerza e inestabilidad
 </td>
 <td>
 Motor
 </td>
 <td>
 "Se solicita información de
su ubicación si esta en la ciudad del servicio o en 
una ciudad donde se tenga oficina se le informa al
Coordinador o Asesor de servicio encargado para 
que le brinden asistencia de manera inmediata para
llevar el vehículo al proveedor de taller para 
la revisión y si se tiene disponibilidad de flota 
realizar de manera inmediata el reemplazo del 
vehículo, en caso de que se encuentre en carretera se enlaza comunicación entre el asegurado y la aseguradora para que le brinden la asistencia requerida."
 </td>
 </tr>
 </tbody>
 <tbody>
 <tr>
 <td>
 Se eleva la temperatura del vehículo, fuga de refrigerante
 </td>
 <td>
 Refrigeración
 </td>
 <td>"Se solicita información de
su ubicación si esta en la ciudad del servicio o en 
una ciudad donde se tenga oficina se le informa al
Coordinador o Asesor de servicio encargado para 
que le brinden asistencia de manera inmediata para
llevar el vehículo al proveedor de taller para 
la revisión y si se tiene disponibilidad de flota 
realizar de manera inmediata el reemplazo del 
vehículo, en caso de que se encuentre en carretera se enlaza comunicación entre el asegurado y la aseguradora para que le brinden la asistencia requerida."
</td>
 </tr>
 </tbody>
 <tbody>
 <tr>
 <td>Sonidos al girar, golpeteo al suspender, zumbidos de las ruedas, que bote los cambios, se neutraliza la caja</td>
 <td>Suspensión y Transmisión</td>
 <td>"Se solicita información de
su ubicación si esta en la ciudad del servicio o en 
una ciudad donde se tenga oficina se le informa al
Coordinador o Asesor de servicio encargado para 
que le brinden asistencia de manera inmediata para
llevar el vehículo al proveedor de taller para 
la revisión y si se tiene disponibilidad de flota 
realizar de manera inmediata el reemplazo del 
vehículo, en caso de que se encuentre en carretera se enlaza comunicación entre el asegurado y la aseguradora para que le brinden la asistencia requerida."
</td>
 </tr>
 </tbody>
<tbody>
 <tr>
<td>
alguna de las llantas esta pinchada o suelta
</td>
<td>
Suspensión y Transmisión
</td>
<td>
"Se indaga sobre la viabilidad de que cambie la llanta y la ajuste, se le informa que el vehículo se encuentra equipado con los componentes necesarios para realizar el cambio, en caso de que manifieste que no lo puede realizar, se 
verifica con que aseguradora tiene la póliza y se le informa 
que se realizará enlace vía telefónica con la aseguradora 
para que le brinden la asistencia requerida."

</td>
</tr>
</tbody> 
</table>
.</span>
    </div>
  </div>
	<div class="bg-dark p-4">
      <nav class="navbar navbar-dark bg-dark">
    <button class="btn btn-primary navbar-toggler" type="button" data-toggle="collapse" 
	data-target="#navbarToggleExternalContent6" aria-controls="navbarToggleExternalContent6" aria-expanded="false" aria-label="Toggle navigation">
      Solicitud Operativa
    </button>
    </nav>
    </div>
	<div class="collapse" id="navbarToggleExternalContent7">
    <div class="bg-dark p-4">
      <span class="text-muted">
	  <table class="table-bordered">
	  <thead>
	  <tr>
	  <th>PREGUNTAS CLAVES DE PRIMEROS AUXILIOS</th>
	  <th>PREGUNTAS DIAGNOSTICO TIPO DE DAÑO</th>
	  <th>TIPO DE DAÑO</th>
	  <th>Accion</th>
	  </tr>
	  </thead>
	  <tr>
	  <td>"SR ________ usted o alguno de los implicados en el accidente se encuentra herido? Si la respuesta es afirmativa se debe:  Sr______ en este momento voy a solicitar la asistencia de ambulancia para que sea atendido en su ubicación. Por favor permanezca en línea.
SR _______ hay un tercero implicado en el accidente?
SR ______ usted requiere la presencia de acompañamiento legal?  Si la respuesta es positiva se debe: SR ___________en el momento se realizara el enlace con la aseguradora del vehículo para que envión la asistencia jurídica.  Por favor permanezca en línea. enlace 2 minutos.
SR _____________ en este momento se encuentran las autoridades de tránsito asistiendo el accidente? en caso de que la respuesta sea negativa se le debe preguntar:
Desea que  se realice el reporte a las autoridades de tránsito?"</td>
<td>"Que parte de vehículo esta afectada por el golpe?
El vehículo enciende y se puede movilizar sin problema? 
Usted se encuentra en condiciones para conducir el vehículo y continuar con el servicio?
"<br>
"Que parte de vehículo esta afectada por el golpe?"</td>
<td>
Daños causados al vehículo golpes leves<br>
Daños causados al vehículo golpes fuertes
</td>
<td>
"Se le informa que una vez termine el servicio se realizara
la cotización para el cobro de los daños, se deja la anotación en el servicio"<br>
"Se verifica con que aseguradora tiene la póliza y se 
enlaza la llamada con la aseguradora para que le 
brinden la asistencia requerida. 
Adicional a esto se debe informar mediante correo
electrónico al Coordinador de oficina o Asesor de 
oficina encargado, para que brinde apoyo y reporte
 el siniestro a la aseguradora o realice la cotización
 de los daños con el proveedor de taller en caso 
de que sea un incidente en el cual es vehículo 
se pueda movilizar, para evaluar si se debe o no 
afectar la póliza."

</td>

	  </tr>
	  
	  
	  </table>
.</span>
    </div>
  </div>
	<div class="bg-dark p-4">
      <nav class="navbar navbar-dark bg-dark">
    <button class="btn btn-primary navbar-toggler" type="button" data-toggle="collapse" 
	data-target="#navbarToggleExternalContent7" aria-controls="navbarToggleExternalContent7" aria-expanded="false" aria-label="Toggle navigation">
      Accidente
    </button>
    </nav>
    </div>
  </div>
  
  <nav class="navbar navbar-dark bg-dark">
    <button class="btn btn-primary navbar-toggler" type="button" data-toggle="collapse" 
	data-target="#navbarToggleExternalContent3" aria-controls="navbarToggleExternalContent3" aria-expanded="false" aria-label="Toggle navigation">
      Requerimiento del cliente
    </button>
  </nav>
</div>
        
		</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
<script>
  //$('#myModal').modal();
  $('#myModal').modal('show');
  $('#table_id').DataTable();
  $('#firstTable').DataTable();
  $('#secondTable').DataTable();
  

  </script>

</body>
</html>