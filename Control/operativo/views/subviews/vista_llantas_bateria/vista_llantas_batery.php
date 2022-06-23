  <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign Up Form</title>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script  src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

        <link rel="stylesheet" href="css/normalize.css">
        <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
    </head>
    <body>
<style>
*, *:before, *:after {
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

body {
  font-family: 'Nunito', sans-serif;
  color: #384047;
}

form {
  max-width: 300px;
  margin: 10px auto;
  padding: 10px 20px;
  background: #f4f7f8;
  border-radius: 8px;
}

h1 {
  margin: 0 0 30px 0;
  text-align: center;
}

input[type="text"],
input[type="password"],
input[type="date"],
input[type="datetime"],
input[type="email"],
input[type="number"],
input[type="search"],
input[type="tel"],
input[type="time"],
input[type="url"],
textarea,
select {
  background: rgba(255,255,255,0.1);
  border: none;
  font-size: 16px;
  height: auto;
  margin: 0;
  outline: 0;
  padding: 15px;
  width: 100%;
  background-color: #e8eeef;
  color: #8a97a0;
  box-shadow: 0 1px 0 rgba(0,0,0,0.03) inset;
  margin-bottom: 30px;
}

input[type="radio"],
input[type="checkbox"] {
  margin: 0 4px 8px 0;
}

select {
  padding: 6px;
  height: 32px;
  border-radius: 2px;
}

button {
  padding: 19px 39px 18px 39px;
  color: #FFF;
  background-color: #4bc970;
  font-size: 18px;
  text-align: center;
  font-style: normal;
  border-radius: 5px;
  width: 100%;
  border: 1px solid #3ac162;
  border-width: 1px 1px 3px;
  box-shadow: 0 -1px 0 rgba(255,255,255,0.1) inset;
  margin-bottom: 10px;
}

fieldset {
  margin-bottom: 30px;
  border: none;
}

legend {
  font-size: 1.4em;
  margin-bottom: 10px;
}

label {
  display: block;
  margin-bottom: 8px;
}

label.light {
  font-weight: 300;
  display: inline;
}

.number {
  background-color: #5fcf80;
  color: #fff;
  height: 30px;
  width: 30px;
  display: inline-block;
  font-size: 0.8em;
  margin-right: 4px;
  line-height: 30px;
  text-align: center;
  text-shadow: 0 1px 0 rgba(255,255,255,0.2);
  border-radius: 100%;
}

@media screen and (min-width: 480px) {

  form {
    max-width: 880px;
  }

}
.labelNuevo{
	display: flex;justify-content: space-around;
}
.inpustJuntos{
	display: flex;justify-content: space-between;
}

#ocultarLlantas{
	display: none;
}
#ocultarLlantas1{
	display: none;
}
#ocultarBaterias{
  display: none;
}
#descripcionBoton{
	display: none;
}
#descripcionBoton1{
	display: none;
}

table {
  
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
<form>
<div id="ocultarLlantas">
       <?php
		$validador = $_GET['disp'];
		//echo $validador;

		$iLlantas =  mysql_fetch_assoc($contLlantas);
		$to =  $iLlantas['total_llantas'];
		
		//echo  $to;
		
		 if($to >= 6){
			 echo $vaTitle = "<fieldset><legend><span class='number'>=)</span>Ya tienes los accesorios<br>(Quieres cambiar alguna rueda)</legend></fieldset>";
			 echo "<style>
			 #ocultarLlantas{
				 display: none;
			 }
			 </style>";
			 $varCambThLla = "<th>Llanta para cambio</th>";
		}else{
			 echo $vaTitle = "<fieldset><legend><span class='number'>2</span>Formulario para llantas <br>(Solo puedes ingresar 6 ruedas)</legend></fieldset>";
			 
			 $varCambThLla = "";
			 
		 }
		?>
		
		<label>Visualizar llantas:</label>
		<table>
		<tr>
		<th>Nombre</th><th>Lote serial</th><th>Serie</th><th>Ancho</th><th>Perfil</th><th>Diametro</th><th>Codigo de velocidad</th><th>Fecha postura</th><th>Descripcion</th><?php echo $varCambThLla?>
		</tr>
		<?php
		while($i=mysql_fetch_object($qGetLlantas)){
			if($to == 6){
		     $varCambTdLla = "<td>
		   <a href='https://app.aoacolombia.com/Control/operativo/bateriaLlantasVehiculos.php?Acc=actualizar_llantas&TABLA=vehiculo&id=$id&idLlantas=$i->id'><button type='button' class='btn btn-info'>Inabilitar llanta</button></a>
		   </td></tr>";
		   }else{
			 $varCambTdLla = "</tr>";  
		   }
			?>
		<tr>
		
		<td><?php echo $i->nombre_llantas?></td>
		<td><?php echo $i->lote_serial?></td>
		<td><?php echo $i->serie?></td>
		<td><?php echo $i->ancho?></td>
		<td><?php echo $i->perfil?></td>
		<td><?php echo $i->diametro?></td>
		<td><?php echo $i->codigo_velocidad?></td>
		<td><?php echo $i->fecha_postura?></td>
		<td><?php echo $i->descripcion?></td> 
		<?php echo $varCambTdLla; }?>
		</table>
      </div>
	 </form> 
		<form action='bateriaLlantasVehiculos.php' method='POST' name='forma' id='forma'>
		 <fieldset>
          <legend><span class="number">1</span>Seleccione su tipo de producto</legend>
        </fieldset>
		 <div class="labelNuevo">
		  <label for="item">Llantas o bateria :</label>
          </div>
		  <div class="inpustJuntos">
		  <label>&nbsp;&nbsp;&nbsp;</label>
          <select id="tipo" name="tipo" onchange="OcultarYVer()" required>
		  <option>Selecciona</option>
		  <?php while($i=mysql_fetch_object($tipoAccesorios)){ ?>
		  <option value='<?php echo $i->id?>'><?php echo $i->nombre ?></option>  
		  <?php } ?>
		  </select>
		  
		  </div>
		  <div id="ocultarLlantas1">
		  
		  <label for="marca_id_llantas">Marca de llantas:</label>
          
		  <select id="marca_id_llantas" name="marca_id_llantas" >
		  <option></option>
		  <?php while($i=mysql_fetch_object($qItemMarcaLlanta)){ ?>
		  
		  <option value='<?php echo $i->id?>'><?php echo $i->nombre ?></option>  
		  <?php } ?>
		  </select>
		  
		  <div class="labelNuevo">
		  <label for="serie">Serie:</label>
          <label for="ancho">Ancho:</label>
		  
		  </div>
		  
		  <div class="inpustJuntos">
		  <input type="text" id="serie" name="serie" >
		  
		  <label>&nbsp;&nbsp;&nbsp;</label>
		  <input type="text" id="ancho" name="ancho">
		  </div>
		  
		  <div class="labelNuevo">
		  <label for="perfil">Perfil:</label>
          <label for="diametro">Diametro:</label>
		  </div>
		  <div class="inpustJuntos">
		  <input type="text" id="perfil"  name="perfil">
		  
		  <label>&nbsp;&nbsp;&nbsp;</label>
          <input type="text" id="diametro" name="diametro" >
		  </div>
		  	
		  <div class="labelNuevo">
		  <label for="codigo_velocidad">Codigo velocidad:</label>
          </div>
		  
		  <div class="inpustJuntos">
		  <input type="text" id="codigo_velocidad"  name="codigo_velocidad" >
		  <label>&nbsp;&nbsp;&nbsp;</label>
		  </div>
		  
		  <div class="labelNuevo">
		  <label for="lote_serial">Lote serial:</label>
          </div>
		  
		  <div class="inpustJuntos">
		  <input type="text" id="lote_serial"  name="lote_serial">
		  <label>&nbsp;&nbsp;&nbsp;</label>
		  </div>
		  <label for="descripcion_llantas">Descripcion:</label>
          
		  <textarea id="descripcion_llantas" name="descripcion_llantas"></textarea >
		  
		  </div>
		  
		  
		  
		  <div id="ocultarBaterias">
		  <fieldset>
          <legend><span class="number">2</span>Formulario para baterias</legend>
        </fieldset>
		  <label for="marca_id_bateria">Marca de Bateria:</label>
          
		  <select id="marca_id_bateria" name="marca_id_bateria">
		  
		  <?php while($i=mysql_fetch_object($qItemMarcaBatery)){ ?>
		  <option>Selecciona</option>
		  <option value='<?php echo $i->id?>'><?php echo $i->nombre ?></option>  
		  <?php } ?>
		  </select>
		  
		  <div class="labelNuevo">
		  <label for="tipo_bateria">Tipo de bateria:</label>
          <label for="amperaje">Amperaje:</label>
		  
		  </div>
		  
		  <div class="inpustJuntos">
		  <input type="text" id="tipo_bateria" name="tipo_bateria">
		  
		  <label>&nbsp;&nbsp;&nbsp;</label>
		  <input type="text" id="amperaje" name="amperaje">
		  </div>
		  
		 <div class="labelNuevo">
		  <label for="voltaje">Voltaje:</label>
          </div>
		  <div class="inpustJuntos">
		  <input type="text" id="voltaje"  name="voltaje" >
		  <label>&nbsp;&nbsp;&nbsp;</label>
		  
		  </div>
		  <label for="descripcion_bateria">Descripcion:</label>
          
		  <textarea id="descripcion_bateria" name="descripcion_bateria"></textarea >
		  </div>
		  
		  
		  
		<div id="descripcionBoton1">  
			<fieldset>
          <legend><span class="number">3</span>Pulsa el boton para registrar tus accesorios</legend>
        </fieldset>
        
        <button type="submit" >Incluir</button>
		</div>
        <div id="descripcionBoton">
        
        <fieldset>
          <legend><span class="number">3</span>Pulsa el boton para registrar tus accesorios</legend>
        </fieldset>
        
        <button type="submit" >Incluir</button>
		
		<input type='hidden' name='Acc' value='Insertar_accesorios_ok'>
		<input type='hidden' name='idVehiculo' value='<?php echo $id ?>'>
		<input type='hidden' name='dateLlantas' value='<?php echo date("Y-m-d") ?>'>
		<input type='hidden' name='dateBatery' value='<?php echo date("Y-m-d") ?>'>
		<input type='hidden' name='contadorLlantas' value='<?php echo $iLlantas['total_llantas'] ?>'>
	</div>
	
	
	
      </form>
	  <script>
	$(document).ready(function(){
		$("#marca_id_llantas").select2({
		placeholder: "Seleccione",
		 width: '100%'
		});
		
		$("#marca_id_bateria").select2({
		placeholder: "Seleccione",
		 width: '100%'
		});
	
	});
	
	
	
	$("#proyecto_placa").select2();
	$("#centrodeoperacion").select2();
	$("#centrocosto").select2();
	
	function OcultarYVer(){
		var v =  document.getElementById("tipo").value;
		var divLlanta = document.getElementById("ocultarLlantas");
		var divLlanta1 = document.getElementById("ocultarLlantas1");
		
		var divBateria = document.getElementById("ocultarBaterias");
		var descripcionBoton = document.getElementById("descripcionBoton");
		
		var descripcionBoton1 = document.getElementById("descripcionBoton1");
		
		
		if(v == 1){
			divBateria.style.display = "block";
			descripcionBoton1.style.display = "block";
		}else{
			divBateria.style.display = "none";
			descripcionBoton1.style.display = "none";
		}
		
		if(v == 2){
			ocultarLlantas1.style.display = "block";
			divLlanta.style.display = "block";
			descripcionBoton.style.display = "block";
	    }else{
			ocultarLlantas1.style.display = "none";
			divLlanta.style.display = "none";
			descripcionBoton.style.display = "none";
		}
	}
	  </script>
   
    </body>
</html>